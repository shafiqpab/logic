<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');
$user_name = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}

if ($action==='print_button_variable_setting')
{
	$print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=7 and report_id=109 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit();
}
//--------------------------------------------------------------------------------------------------------------------
function page_style()
{
	?>
	<style type="text/css">
	table tr th small {
		font-weight: normal !important;
	}

	table tr td, table tr th {
		text-align: right;
		padding: 0px 2px;
	}

	#summary tr td {
		text-align: left !important;
	}

	table tr td.right, #summary tr td.right {
		text-align: right !important;
	}

	table tr td.left {
		text-align: left !important;
	}

	table tr td.center {
		text-align: center !important;
	}

</style>
<?
}

if ($action == "report_generate") // Show
{
	page_style();
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$company_name = str_replace("'", "", $cbo_company_name);
	$buyer_name = str_replace("'", "", trim($cbo_buyer_name));
	$sales_job_no = str_replace("'", "", $txt_sales_job_no);
	$hide_job_id = str_replace("'", "", $hide_job_id);
	$sales_booking_no = str_replace("'", "", $txt_booking_no);
	$hide_booking_id = str_replace("'", "", $hide_booking_id);
	$start_date = str_replace("'", "", trim($txt_date_from));
	$end_date = str_replace("'", "", trim($txt_date_to));
	$cbo_year_selection = str_replace("'", "", trim($cbo_year_selection));
	$cbo_within_group = str_replace("'", "", trim($cbo_within_group));
	$hilight_bg = "";

	if($db_type==0)
	{
		$year_cond=" and YEAR(a.insert_date)=$cbo_year_selection";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";
	}
	else
	{
		$year_cond="";;
	}

	if ($end_date == "") {
		$end_date = $start_date;
	} else {
		$end_date = $end_date;
	}

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$str_cond_insert = " and a.delivery_date between '" . $start_date . "' and '" . $end_date . "'";
		} else {
			$str_cond_insert = " and a.delivery_date between '" . $start_date . "' and '" . $end_date . "'";
		}
	} else {
		$str_cond_insert = "";
	}
	if($hide_job_id == ""){
		$sales_order_cond = ($sales_job_no != "") ? " and a.job_no_prefix_num=$sales_job_no" : "";
	}else{
		$sales_order_cond = " and a.id in($hide_job_id)";
	}
	if($hide_booking_id == ""){
		$sales_booking_cond = ($sales_booking_no != "") ? " and a.sales_booking_no like '%$sales_booking_no%'" : "";
	}else{
		$sales_booking_cond = " and a.sales_booking_no='$sales_booking_no'";
	}

	$buyer_cond = ($buyer_name != 0) ? " and a.customer_buyer=$buyer_name" : "";

	$dataArraySalesOrder = array();
	$started = microtime(true);
	// SALES ORDER DATA RESULT
	// ,c.id booking_id
	$salesOrderDataSql = "SELECT a.booking_id,a.id,a.buyer_id sales_buyer,a.within_group,a.entry_form as mst_entry_form,c.buyer_id,a.company_id,a.job_no sales_job_no, a.job_no_prefix_num,a.style_ref_no,a.delivery_date sales_order_dt,a.delivery_date,c.po_break_down_id,a.sales_booking_no booking_no, c.fabric_composition,c.is_short,c.fabric_source,c.job_no,c.is_approved,c.item_category,c.entry_form,c.booking_type,a.within_group, a.team_leader, a.dealing_marchant, a.garments_marchant, a.customer_buyer, a.delivery_start_date 
	from fabric_sales_order_mst a left join wo_booking_mst c on a.booking_id = c.id  
	where a.is_deleted=0 and a.status_active=1 $str_cond_insert and a.company_id = $company_name and a.entry_form = 472 $sales_order_cond $sales_booking_cond $buyer_cond $year_cond order by a.delivery_date desc";
	//and a.within_group=$cbo_within_group

	// echo $salesOrderDataSql;

	$salesOrderDataResult = sql_select($salesOrderDataSql);
	$sales_order_ids = $job_no_arr = array();
	if (!empty($salesOrderDataResult)) 
	{
		foreach ($salesOrderDataResult as $row) {
			$sales_order_ids[] = $row[csf("id")];
			$job_no_arr[] = "'".$row[csf("job_no")]."'";
			$booking_no_arr[] = "'".$row[csf("booking_no")]."'";
		}
		$sales_ids = implode(",",$sales_order_ids);
		$job_nos = implode(",",$job_no_arr);
		$booking_nos = implode(",",array_unique($booking_no_arr));


		if($sales_ids != "")
		{
			$sales_ids_arr = explode(",", $sales_ids);
			$fin_rcv_trans_iss_fso_Cond=""; $fsoCond_14="";

			if($db_type==2 && count($sales_ids_arr)>999)
			{
				$sales_ids_arr_chunk=array_chunk($sales_ids_arr,999) ;
				foreach($sales_ids_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$fsoCond_14.=" c.po_breakdown_id in($chunk_arr_value) or ";
				}

				$fin_rcv_trans_iss_fso_Cond.=" and (".chop($fsoCond_14,'or ').")";
			}
			else
			{
				$fin_rcv_trans_iss_fso_Cond=" and c.po_breakdown_id in ($sales_ids)";
			}
		}


		// PREPARE REQUIRED ARRAY LIBRARIES
		$buyer_name_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
		$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
		$color_array = return_library_array("select id, color_name from lib_color", "id", "color_name");
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyer_short_name_library = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
		$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
		$dealing_marchant_array = return_library_array("select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");

		$team_sql_data=sql_select("SELECT id, team_name, team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0");
		foreach ($team_sql_data as $key => $row) 
		{
			$team_name_arr[$row[csf("id")]] = $row[csf("team_name")];
			$team_leader_name_arr[$row[csf("id")]] = $row[csf("team_leader_name")];
		}

		$yarn_qty_requisition_arr = array();
		$composition_arr = array();
		$style_owner_arr = array();
		$yarn_details_arr = array();
		$yarn_issue_details_arr = array();
		$grey_fabric_status_arr = array();
		$knitting_arr = $finish_arr = array();
		$grey_issue_qnty_arr = array();
		$dataArrayYarnIssue = array();
		$yarn_issue_details_arr1 = array();
		$yarn_details_found_arr = array();
		$buyer_wise_total_fab_req = array();
		$sales_color_arr = array();
		$sales_fin_qnty_arr = array();

		$salesOrderDetailsDataSql = "SELECT mst_id,determination_id,color_id,sum(finish_qty) finish_qty, sum(pp_qnty) pp_qnty, sum(mtl_qnty) mtl_qnty, sum(fpt_qnty) fpt_qnty, sum(gpt_qnty) gpt_qnty, sum(process_loss) process_loss from fabric_sales_order_dtls where is_deleted=0 and status_active=1 and mst_id in($sales_ids) group by mst_id,determination_id,color_id";
		$salesOrderDetailsDataResult = sql_select($salesOrderDetailsDataSql);
		foreach ($salesOrderDetailsDataResult as $row) {

			$sales_color_arr[$row[csf('mst_id')]][$row[csf("determination_id")]][] = array(
				'color_id' => $row[csf("color_id")]
			);
			$sales_fin_qnty_arr[$row[csf('mst_id')]][$row[csf("determination_id")]][$row[csf("color_id")]] = $row[csf("finish_qty")];
			
			$confirm_finish_qty_arr[$row[csf('mst_id')]] += $row[csf('finish_qty')] + $row[csf('pp_qnty')] + $row[csf('mtl_qnty')] + $row[csf('fpt_qnty')] + $row[csf('gpt_qnty')];
			$process_loss_arr[$row[csf('mst_id')]] += $row[csf('process_loss')];
		}

		// PREPARE YARN REQUISITION DATA ARRAY
		$yarn_qty_requisition = sql_select("select a.dtls_id, a.determination_id,b.knit_id,b.requisition_no,b.yarn_qnty,a.po_id,b.id,b.prod_id,c.yarn_count_id,c.yarn_comp_percent1st,c.yarn_comp_type1st,c.yarn_type from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join product_details_master c on b.prod_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.po_id in($sales_ids)");
		foreach ($yarn_qty_requisition as $row) {
			$yarn_qty_requisition_arr[$row[csf('po_id')]][$row[csf('yarn_count_id')]][$row[csf('yarn_comp_type1st')]][$row[csf('yarn_comp_percent1st')]][$row[csf('yarn_type')]] = $row[csf('yarn_qnty')];
			$yarn_qty_requisition_arr[$row[csf('po_id')]]['requisition'] = $row[csf('id')];
			$yarn_qty_requisition_arr[$row[csf('po_id')]][$row[csf('determination_id')]]['requisition_qnty'] += $row[csf('yarn_qnty')];
			$yarn_qty_requisition_arr[$row[csf('po_id')]][$row[csf('determination_id')]]['requisition_no'] .= $row[csf('requisition_no')].",";
		}

		$compositionData = sql_select("select mst_id, copmposition_id, percent from lib_yarn_count_determina_dtls where status_active=1 and is_deleted=0");
		foreach ($compositionData as $row) {
			$composition_arr[$row[csf('mst_id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
		}
		// STYLE OWNER INFO START
		$style_owner_info = sql_select("select a.job_no, a.style_owner,b.booking_no from wo_po_details_master a,wo_booking_dtls b where a.job_no=b.job_no and a.status_active = 1 and a.is_deleted = 0 and a.style_owner != 0 and b.status_active=1 and b.booking_no in($booking_nos) group by a.job_no, a.style_owner,b.booking_no");
		foreach ($style_owner_info as $row) {
			$style_owner_arr[$row[csf('booking_no')]]["style_owner"] = $row[csf('style_owner')];
			$style_owner_arr[$row[csf('booking_no')]]["job_no"] = $row[csf('job_no')];
		}
		// STYLE OWNER INFO END
		//
		$sql_yarn_iss = "SELECT a.po_id,a.determination_id,d.mst_id,sum(d.cons_quantity) cons_quantity from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join inv_transaction d on (b.requisition_no=d.requisition_no and d.transaction_type=2 and b.prod_id=d.prod_id) where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.po_id in($sales_ids) and a.is_sales=1 group by a.po_id,d.mst_id,a.determination_id";
		$dataArrayIssue = sql_select($sql_yarn_iss);
		foreach ($dataArrayIssue as $row_yarn_iss) {
			$yarn_issue_details_arr[$row_yarn_iss[csf('po_id')]][$row_yarn_iss[csf('determination_id')]]['issue_qnty'] += $row_yarn_iss[csf('cons_quantity')];
			$yarn_issue_details_arr[$row_yarn_iss[csf('po_id')]][$row_yarn_iss[csf('determination_id')]]['issue_ids'] .= $row_yarn_iss[csf('mst_id')].",";
		}

		// YARN DETAILS ARRAY START
		$yarn_sql = "SELECT b.mst_id, b.deter_id ,sum(b.cons_qty)cons_qty from fabric_sales_order_yarn_dtls b where b.status_active=1 and b.is_deleted=0 and b.mst_id in($sales_ids) group by b.mst_id, b.deter_id ";
		$yarn_info = sql_select($yarn_sql);
		foreach ($yarn_info as $row) {
			$yarn_details_arr[$row[csf('mst_id')]][$row[csf('deter_id')]] += $row[csf('cons_qty')];
		}

		// GREY FABRIC DETAILS ARRAY START
		$grey_fabric_status_sql = "select a.mst_id, a.fabric_desc, a.determination_id,sum(a.grey_qty) grey_qty from fabric_sales_order_dtls a where a.status_active = 1 and a.is_deleted = 0 and a.mst_id in($sales_ids) group by a.mst_id,a.fabric_desc, a.determination_id";
		$grey_fabric_status_info = sql_select($grey_fabric_status_sql);
		foreach ($grey_fabric_status_info as $row) {
			$grey_fabric_status_arr[$row[csf('mst_id')]][] = array(
				'fabric_desc' => $row[csf("fabric_desc")],
				'determination_id' => $row[csf("determination_id")],
				'grey_qty' => $row[csf('grey_qty')]
			);
		}

		$dataArrayTrans = sql_select("select a.po_breakdown_id, c.detarmination_id,
			sum(CASE when a.entry_form in (2) then quantity else 0 end) AS grey_receive,
			sum(CASE when a.entry_form in (58) then quantity else 0 end) AS grey_roll_receive,
			sum(CASE WHEN a.entry_form ='45' and a.trans_type=3 THEN quantity ELSE 0 END) AS grey_receive_return,
			sum(case when a.entry_form ='16' then a.quantity else 0 end) as grey_issue,
			sum(case when a.entry_form ='61' then a.quantity else 0 end) as grey_issue_roll_wise,
			sum(CASE WHEN a.entry_form ='51' and a.trans_type=4 THEN quantity ELSE 0 END) AS grey_issue_return,
			sum(case when a.entry_form ='133' and a.trans_type=6 then quantity else 0 end) as transfer_out,
			sum(case when a.entry_form ='133' and a.trans_type=5 then quantity else 0 end) as transfer_in
			from order_wise_pro_details a
			left join product_details_master c on a.prod_id=c.id
			where a.status_active=1 and a.is_deleted=0 and a.entry_form in(11,13,16,45,51,58,61,80,81,83,133) and a.po_breakdown_id in($sales_ids)
			group by a.po_breakdown_id, c.detarmination_id");
		foreach ($dataArrayTrans as $row) {
			$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]]=$row[csf('grey_receive')] + $row[csf('grey_roll_receive')];
			$grey_issue_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]]=$row[csf('grey_issue')] + $row[csf('grey_issue_roll_wise')];
			$grey_receive_return_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]]=$row[csf('grey_receive_return')];
			$grey_issue_return_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]]=$row[csf('grey_issue_return')];
			$trans_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]]["transfer_out"] = $row[csf('transfer_out')];
			$trans_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]]["transfer_in"] = $row[csf('transfer_in')];
		}
		// grey fabric delivery to store
		//$sql_grey_delivery = "select order_id,entry_form,color_id,sum(current_delivery) as grey_delivery_qty,product_id from pro_grey_prod_delivery_dtls where entry_form in(53,54,56,67) and status_active=1 and is_deleted=0 and order_id in($sales_ids) group by order_id,product_id,entry_form,color_id";

		$sql_grey_delivery = "select a.order_id, a.entry_form, a.color_id, sum(a.current_delivery) as grey_delivery_qty, a.product_id, b.detarmination_id from pro_grey_prod_delivery_dtls a, product_details_master b where a.product_id=b.id and a.entry_form in(53,54,56,67) and a.status_active=1 and a.is_deleted=0 and a.order_id in($sales_ids) group by a.order_id, a.product_id, a.entry_form, a.color_id, b.detarmination_id";

		$data_grey_delivery = sql_select($sql_grey_delivery);
		foreach ($data_grey_delivery as $greyDel) 
		{
			/*if($greyDel[csf('entry_form')]==54 || $greyDel[csf('entry_form')]==67)
			{
				$finDeliveryArray[$greyDel[csf('order_id')]][$greyDel[csf('product_id')]][$greyDel[csf('color_id')]] += $greyDel[csf('grey_delivery_qty')];
			}else{
				$greyDeliveryArray[$greyDel[csf('order_id')]][$greyDel[csf('product_id')]] += $greyDel[csf('grey_delivery_qty')];
			}*/

			if($greyDel[csf('entry_form')]==54 || $greyDel[csf('entry_form')]==67)
			{
				$finish_arr[$greyDel[csf('order_id')]][$greyDel[csf('detarmination_id')]][$greyDel[csf('color_id')]]['delivery_to_store'] += $greyDel[csf('grey_delivery_qty')];
			}else{
				$knitting_arr[$greyDel[csf('order_id')]][$greyDel[csf('detarmination_id')]]['delivery_to_store'] += $greyDel[csf('grey_delivery_qty')];
			}
		}

		// GREY FABRIC DETAILS ARRAY END
		$knitting_sql = sql_select("select a.id,a.booking_no,a.booking_id, sum(b.grey_receive_qnty) grey_receive_qnty,b.febric_description_id,b.body_part_id, b.prod_id,c.quantity,c.po_breakdown_id from inv_receive_master a inner join pro_grey_prod_entry_dtls b on a.id = b.mst_id left join order_wise_pro_details c on b.id=c.dtls_id where  a.status_active = 1 and a.is_deleted = 0 and b.status_active= 1 and b.is_deleted = 0 and c.entry_form in(2,11,13,45,51) and c.po_breakdown_id in($sales_ids) group by a.id,a.booking_no,a.booking_id,b.febric_description_id,b.body_part_id,b.prod_id,c.quantity,c.po_breakdown_id");

		foreach ($knitting_sql as $row) {
			$knitting_arr[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['prod_id'] = $row[csf("prod_id")];
			$knitting_arr[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['grey_receive_qnty'] += $row[csf("grey_receive_qnty")];
			$knitting_arr[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['issue_qnty'] += $row[csf("quantity")];

			//$knitting_arr[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['delivery_to_store'] = $greyDeliveryArray[$row[csf('po_breakdown_id')]][$row[csf("prod_id")]];
		}

		// RECEIVE BY BATCH ARRAY
		$receive_by_batch_sql=sql_select("select c.po_breakdown_id,b.febric_description_id,sum(c.qnty) roll_wgt from pro_grey_batch_dtls b,inv_receive_mas_batchroll a,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and c.entry_form=62 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($sales_ids) group by c.po_breakdown_id,b.febric_description_id");
		$receive_by_batch_arr=array();
		foreach ($receive_by_batch_sql as $row) {
			$receive_by_batch_arr[$row[csf("po_breakdown_id")]][$row[csf('febric_description_id')]]['receive_qnty'] = $row[csf("roll_wgt")];
		}

		// BATCH ARRAY
		$batch_sql = "select a.sales_order_id,a.color_id,c.detarmination_id,a.extention_no,sum(b.batch_qnty) qnty from pro_batch_create_mst a,pro_batch_create_dtls b,product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.sales_order_id in($sales_ids) and (a.extention_no is null or a.extention_no=0) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by a.sales_order_id,a.color_id,c.detarmination_id,a.extention_no";
		$batch_result = sql_select($batch_sql);
		$batch_arr=array();
		foreach ($batch_result as $row) {
			$batch_arr[$row[csf("sales_order_id")]][$row[csf("detarmination_id")]][$row[csf("color_id")]] = $row[csf("qnty")];
			$batch_arr[$row[csf("sales_order_id")]][$row[csf("detarmination_id")]]["total_fab_batch"] += $row[csf("qnty")];
		}

		// DYEING PRODUCTION
		$sql_dye = "select b.po_id, a.color_id,b.prod_id,d.detarmination_id, sum(b.batch_qnty) as dye_qnty from pro_batch_create_mst a, pro_batch_create_dtls b, pro_fab_subprocess c,product_details_master d where a.id=b.mst_id and a.id=c.batch_id and b.prod_id=d.id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.po_id in ($sales_ids) group by b.po_id, a.color_id,b.prod_id,d.detarmination_id";
		$resultDye = sql_select($sql_dye);
		foreach ($resultDye as $dyeRow) {
			$dye_qnty_arr[$dyeRow[csf('po_id')]][$dyeRow[csf('detarmination_id')]][$dyeRow[csf('color_id')]] += $dyeRow[csf('dye_qnty')];
		}
		unset($resultDye);

		// FINISH PRODUCTION
		$finish_sql = sql_select("select c.po_breakdown_id,b.fabric_description_id,b.color_id,b.prod_id,sum(c.quantity ) fin_receive_qnty from inv_receive_master a,pro_finish_fabric_rcv_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form in(7,66) and c.po_breakdown_id in($sales_ids) group by c.po_breakdown_id,b.fabric_description_id,b.color_id,b.prod_id");

		foreach ($finish_sql as $row) {
			$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('fabric_description_id')]][$row[csf('color_id')]]['prod_id'] = $row[csf("prod_id")];
			$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('fabric_description_id')]][$row[csf('color_id')]]['fin_receive_qnty'] += $row[csf("fin_receive_qnty")];
			//$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('fabric_description_id')]][$row[csf('color_id')]]['delivery_to_store'] = $finDeliveryArray[$row[csf('po_breakdown_id')]][$row[csf("prod_id")]][$row[csf('color_id')]];
		}

		// TRANSFER IN ARRAY
		$transfer_in_sql = sql_select("select a.company_id,a.to_order_id,b.from_prod_id,c.product_name_details,c.detarmination_id, c.gsm, c.dia_width, sum(b.transfer_qnty) transfer_qnty from inv_item_transfer_mst a,inv_item_transfer_dtls b,product_details_master c where a.id=b.mst_id and b.from_prod_id=c.id and a.status_active=1 and b.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and a.to_order_id in($sales_ids) group by a.company_id,a.to_order_id,b.from_prod_id,c.product_name_details,c.detarmination_id, c.gsm, c.dia_width");
		//a.transfer_system_id ,
		$transfer_arr=array();
		foreach ($transfer_in_sql as $transfer_row) {
			$transfer_arr[$transfer_row[csf('to_order_id')]][] = array(
				'fabric_desc' => $transfer_row[csf("product_name_details")],
				'detarmination_id' => $transfer_row[csf("detarmination_id")],
				'gsm' => $transfer_row[csf("gsm")],
				'dia_width' => $transfer_row[csf("dia_width")],
				'transfer_qnty' => $transfer_row[csf('transfer_qnty')]
			);
		}

		$fin_rcv_trans_iss_sql = sql_select("SELECT c.po_breakdown_id,d.detarmination_id,d.color,c.prod_id,sum(c.quantity ) quantity ,c.entry_form from order_wise_pro_details c, product_details_master d where c.prod_id=d.id and c.entry_form in(224,318,225,317) and c.is_sales=1 $fin_rcv_trans_iss_fso_Cond group by c.po_breakdown_id,d.detarmination_id,d.color,c.prod_id,c.entry_form");

		//(225,287,224,230,233,317,318)
		foreach ($fin_rcv_trans_iss_sql as $row)
		{
			if($row[csf('entry_form')] == 225 || $row[csf('entry_form')] == 317)
			{
				$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]["fin_tex_rcv"] += $row[csf('quantity')];
			}
			else if($row[csf('entry_form')] == 224 || $row[csf('entry_form')] == 318)
			{
				$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]["fin_deli_to_garments"] += $row[csf('quantity')];
			}
		}

		ob_start();
		$i = 1;
		$html = "";
		foreach ($salesOrderDataResult as $row) 
		{
			$sales_id = $row[csf('id')];
			$grey_fabric_status_details = $grey_fabric_status_arr[$row[csf('id')]];
			$grey_fabric_transfer_details = $transfer_arr[$row[csf('id')]];
			$grey_fabric_count = count($grey_fabric_status_details);
			$grey_fabric_color_count = count($grey_fabric_color_details);
			$confirm_finish_qty=$confirm_finish_qty_arr[$sales_id];
			$process_loss=$process_loss_arr[$sales_id];
			$k=0;
			$po_ids = rtrim($row[csf('po_break_down_id')], ',');
			$within_group = ($row[csf('within_group')] == 1) ? "Yes" : "No";
			if ($row[csf('within_group')] == 1) {
				$main_booking = "<a href='##' style='color:#000' onclick=\"generate_worder_report('" . $row[csf('booking_type')] . "','" . $row[csf('booking_no')] . "','" . $company_name . "','" . $po_ids . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "'," . $row[csf('entry_form')] . "," . $row[csf('is_short')] . ",'show_fabric_booking_report_urmi')\"><font style='font-weight:bold' $wo_color >" . $row[csf('booking_no')] . "</font></a>";
			} else {
				$main_booking = "<strong>" . $row[csf('booking_no')] . "</strong>";
			}
			$sales_order = "<a href='##' style='color:#000' onclick=\"fnc_fabric_sales_order_print('" . $company_name . "','" . $row[csf('booking_id')] . "','" . $row[csf('booking_no')] . "','" . $row[csf('sales_job_no')] . "','" . $row[csf('mst_entry_form')] . "','1')\"><font style='font-weight:bold' $wo_color>" . $row[csf('sales_job_no')] . "</font></a>";

			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			$rowspan=$fabric_span=0;
			$fabric_rowspan=array();
			foreach ($grey_fabric_status_details as $grey_fabric_row) 
			{
				$determination_id = $grey_fabric_row["determination_id"];
				$grey_fabric_color_details = $sales_color_arr[$row[csf('id')]][$determination_id];

				foreach ($grey_fabric_color_details as $value) {
					$rowspan++;
					$fabric_rowspan[$determination_id][] = $value['color_id'];
					$yet_to_batch_qnty[$determination_id] += $batch_arr[$sales_id][$determination_id][$value['color_id']];
				}
			}
			foreach ($grey_fabric_transfer_details as $transfer_row) 
			{
				$rowspan++;
			}
			$job_no = $style_owner_arr[$row[csf('booking_no')]]["job_no"];
			$style_owner = $style_owner_arr[$row[csf('booking_no')]]["style_owner"];
			$buyer = ($row[csf('within_group')]==1)?$row[csf("buyer_id")]:$row[csf("sales_buyer")];
			$rowspan = "rowspan='" . $rowspan . "'";
			$html .= "<tr bgcolor='$bgcolor' onclick=\"change_color('tr_" . $i . "','#FFFFFF')\" id=\"tr_$i\">
			<td width='40' class='center' $rowspan>" . $i . "</td>
			<td width='100' class='center' $rowspan>" . $team_name_arr[$row[csf('team_leader')]] . "</td>
			<td width='100' class='center' $rowspan>" . $team_leader_name_arr[$row[csf('team_leader')]] . "</td>
			<td width='100' class='center' $rowspan>" . $dealing_marchant_array[$row[csf('dealing_marchant')]] . "</td>
			<td width='100' class='center' $rowspan>" . $row[csf('garments_marchant')] . "</td>
			<td width='50' class='center' $rowspan>" . date("Y", strtotime($row[csf('sales_order_dt')])) . "</td>
			<td width='100' class='center' $rowspan>" . $buyer_name_array[$row[csf('customer_buyer')]] . "</td>
			<td width='100' class='center' $rowspan>" . $buyer_name_array[$buyer] . "</td>
			<td width='165' class='center' $rowspan>" . $main_booking . "</td>
			<td width='165' class='center' $rowspan>" . $sales_order . "</td>	
			<td width='80' class='center' $rowspan>" . $row[csf('style_ref_no')] . "</td>		
			<td width='100' class='center' $rowspan>" . $within_group . "</td>
			<td width='100' class='center' $rowspan>" . date("d-m-Y", strtotime($row[csf('delivery_start_date')])) . "</td>
			<td width='100' class='center' $rowspan>" . date("d-m-Y", strtotime($row[csf('sales_order_dt')])) . "</td>
			<td width='100' class='center' $rowspan>" . $confirm_finish_qty . "</td>
			<td width='100' class='center' $rowspan>" . $process_loss . "</td>";

			foreach ($grey_fabric_status_details as $grey_fabric_row) 
			{
				$determination_id = $grey_fabric_row["determination_id"];
				$grey_fabric_color_details = $sales_color_arr[$row[csf('id')]][$determination_id];
				//echo count($grey_fabric_color_details);
				$knitting_production = ($knitting_arr[$row[csf('id')]][$determination_id]['grey_receive_qnty'] > 0) ? number_format($knitting_arr[$row[csf('id')]][$determination_id]['grey_receive_qnty'], 2, '.', '') : "";
				$grey_required = ($grey_fabric_row["grey_qty"] > 0) ? number_format($grey_fabric_row["grey_qty"], 2, '.', '') : "";
				$grey_receive_qnty = ($grey_receive_qnty_arr[$row[csf('id')]][$determination_id] > 0) ? number_format($grey_receive_qnty_arr[$row[csf('id')]][$determination_id], 2, '.', '') : "";
				$issue_qnty = ($grey_issue_qnty_arr[$row[csf('id')]][$determination_id] > 0) ? number_format($grey_issue_qnty_arr[$row[csf('id')]][$determination_id], 2, '.', '') : "";
				$delivery_to_store = ($knitting_arr[$row[csf('id')]][$determination_id]['delivery_to_store'] > 0) ? number_format($knitting_arr[$row[csf('id')]][$determination_id]['delivery_to_store'], 2, '.', '') : "";

				$grey_receive_return = $grey_receive_return_qnty_arr[$row[csf('id')]][$determination_id];
				$grey_issue_return = $grey_issue_return_qnty_arr[$row[csf('id')]][$determination_id];
				$net_return = (($grey_issue_return - $grey_receive_return) > 0) ? number_format(($grey_issue_return - $grey_receive_return), 2, '.', '') : "";
				$knitting_balance = (($grey_fabric_row["grey_qty"] - $knitting_production) > 0) ? number_format(($grey_fabric_row["grey_qty"] - $knitting_production), 2, '.', '') : "";
				$net_transfer = ($trans_qnty_arr[$row[csf('id')]][$determination_id]['transfer_out'] != 0) ? number_format($trans_qnty_arr[$row[csf('id')]][$determination_id]['transfer_out'], 2, '.', '') : "";
				$net_transfer_in = ($trans_qnty_arr[$row[csf('id')]][$determination_id]['transfer_in'] != 0) ? number_format($trans_qnty_arr[$row[csf('id')]][$determination_id]['transfer_in'], 2, '.', '') : "";
				//$grey_available = (($knitting_production + $net_transfer) > 0) ? ($knitting_production + $net_transfer) : "";
				$grey_available = (($grey_receive_qnty + $net_transfer_in - $net_transfer) > 0) ? ($knitting_production + $net_transfer_in - $net_transfer) : "";
				$grey_balance = (($grey_fabric_row["grey_qty"] - $grey_available) > 0) ? number_format(($grey_fabric_row["grey_qty"] - $grey_available), 2, '.', '') : "";

				$grey_in_hand = (($grey_available - $issue_qnty) > 0) ? number_format(($grey_available - $issue_qnty), 2, '.', '') : "";
				$grey_issue_bal = (($grey_fabric_row["grey_qty"] - $issue_qnty) > 0) ? number_format(($grey_fabric_row["grey_qty"] - $issue_qnty), 2, '.', '') : "";
				$receive_by_batch = ($receive_by_batch_arr[$row[csf('id')]][$determination_id]['receive_qnty'] > 0)?number_format($receive_by_batch_arr[$row[csf('id')]][$determination_id]['receive_qnty'], 2, '.', ''):"";
				$grey_in_knit_floor = (($knitting_production - $delivery_to_store) > 0) ? number_format(($knitting_production - $delivery_to_store), 2, '.', '') : "";

				$fab_rowspan = $rowspan = "rowspan='" . count($fabric_rowspan[$determination_id]) . "'";
				$requisition_nos = rtrim($yarn_qty_requisition_arr[$sales_id][$determination_id]['requisition_no'],", ")."****1";
				$yarn_required = ($yarn_details_arr[$sales_id][$determination_id] > 0) ? number_format($yarn_details_arr[$sales_id][$determination_id], 2, '.', '') : "";

				$yarn_requisition = ($yarn_qty_requisition_arr[$sales_id][$determination_id]['requisition_qnty'] > 0)?number_format($yarn_qty_requisition_arr[$sales_id][$determination_id]['requisition_qnty'], 2, '.', ''):"";
				$yarn_issue = ($yarn_issue_details_arr[$sales_id][$determination_id]['issue_qnty'] > 0)?number_format($yarn_issue_details_arr[$sales_id][$determination_id]['issue_qnty'], 2, '.', ''):"";
				$yarn_balance = ($yarn_requisition-$yarn_issue > 0)?number_format(($yarn_requisition-$yarn_issue), 2, '.', ''):"";
				$yarn_issue_ids = rtrim($yarn_issue_details_arr[$sales_id][$determination_id]['issue_ids'], ", ") ."_".$requisition_nos;
				$html .= ($k > 0) ? "<tr bgcolor='$bgcolor' onclick=\"change_color('tr_" . $i . "','#FFFFFF')\" id=\"tr_$i\">" : "";
				$html .= "<td width='80' $rowspan>".$yarn_required."</td>
				<td width='80' $rowspan><a href='##' onclick=\"openmypage('$requisition_nos','yarn_requisition_popup','" . $determination_id . "')\">" .$yarn_requisition."</a></td>
				<td width='100' $rowspan><a href='##' onclick=\"openmypage('$yarn_issue_ids','yarn_issue_popup','" . $determination_id . "')\">" . $yarn_issue . "</a></td>
				<td width='70' $rowspan>".$yarn_balance."</td>";
				$html .= "<td width='280' class='center' $fab_rowspan>" . $grey_fabric_row["fabric_desc"] . "</td>
				<td width='80' $fab_rowspan>" . $grey_required . "</td>
				<td width='100' $fab_rowspan><a  href='##' onclick=\"open_febric_receive_status_order_wise_popup('" . $sales_id . "','grey_receive_popup',''," . $determination_id . ")\">" . $knitting_production . "</a></td>
				<td width='100' $fab_rowspan>" . number_format($knitting_balance, 2, '.', '') . "</td>
				<td width='100' $fab_rowspan><a  href='##' onclick=\"open_febric_receive_status_order_wise_popup('" . $sales_id . "_9','grey_purchase_delivery',''," . $determination_id . ")\">" . $delivery_to_store . "</a></td>
				<td width='100' $fab_rowspan>" . $grey_in_knit_floor . "</td>
				<td width='100' $fab_rowspan><a  href='##' onclick=\"open_febric_receive_status_order_wise_popup('" . $sales_id . "_9','grey_purchase_popup',''," . $determination_id . ")\">" . $grey_receive_qnty . "</a></td>
				<!--<td width='100' $fab_rowspan>" . $net_return . "</td>-->
				<td width='100' $fab_rowspan><a  href='##' onclick=\"open_febric_receive_status_order_wise_popup('" . $sales_id . "','yarn_trans_in_popup','','" . $determination_id  ."_".$gsm."_".$dia_width. "')\">" . $net_transfer_in . "</a></td>
				<td width='100' $fab_rowspan><a  href='##' onclick=\"open_febric_receive_status_order_wise_popup('" . $sales_id . "','yarn_trans_out_popup','','" . $determination_id ."_".$gsm."_".$dia_width. "')\">" . $net_transfer . "</a></td>
				<td width='100' $fab_rowspan>" . number_format($grey_available, 2, '.', '') . "</td>
				<td width='100' $fab_rowspan>" . $grey_balance . "</td>
				<td width='100' $fab_rowspan><a  href='##' onclick=\"open_febric_receive_status_order_wise_popup('" . $sales_id . "','grey_issue_popup',''," . $determination_id . ")\">" . $issue_qnty . "</a></td>
				<td width='100' $fab_rowspan>" . $grey_in_hand . "</td>
				<td width='100' $fab_rowspan>" . $grey_issue_bal . "</td>
				<td width='100' $fab_rowspan><a  href='##' onclick=\"open_febric_receive_status_order_wise_popup('" . $sales_id . "','grey_receive_by_batch_popup',''," . $determination_id . ")\">" . $receive_by_batch . "</a></td>";
				$t=$batch_qnty_bal = 0;
				foreach ($grey_fabric_color_details as $grey_color_row) 
				{
					$batch_qnty = ($batch_arr[$sales_id][$determination_id][$grey_color_row['color_id']] > 0) ? number_format($batch_arr[$sales_id][$determination_id][$grey_color_row['color_id']], 2, '.', '') : "";
					$html .= ($t > 0) ? "<tr bgcolor='$bgcolor' onclick=\"change_color('tr_" . $i . "','#FFFFFF')\" id=\"tr_$i\">" : "";
					$html .= "<td width='100' class='center'>". $color_array[$grey_color_row['color_id']] ."</td>
					<td width='100'><a  href='##' onclick=\"open_febric_receive_status_order_wise_popup('" . $sales_id . "','batch_popup','".$grey_color_row['color_id']."'," . $determination_id . ")\">" . $batch_qnty."</a></td>";
					if(count($grey_fabric_color_details) > 0){
						$yet_to_batch = (($issue_qnty - $batch_arr[$sales_id][$determination_id]["total_fab_batch"])>0)?number_format(($issue_qnty - $batch_arr[$sales_id][$determination_id]["total_fab_batch"]), 2, '.', ''):"";
						if($t==0){
							$html .= "<td width='100' rowspan='".count($grey_fabric_color_details)."'>".$yet_to_batch."</td>";
						}
					}
					$dye_qnty = number_format($dye_qnty_arr[$sales_id][$determination_id][$grey_color_row['color_id']], 2, '.', '');
					$dye_bal = number_format(($batch_qnty-$dye_qnty), 2, '.', '');
					$req_fin_qnty = number_format(($sales_fin_qnty_arr[$sales_id][$determination_id][$grey_color_row['color_id']]), 2, '.', '');
					$fin_qnty = number_format($finish_arr[$sales_id][$determination_id][$grey_color_row['color_id']]['fin_receive_qnty'], 2, '.', '');
					$fin_bal = number_format(($req_fin_qnty - $fin_qnty), 2, '.', '');
					$fin_del_to_store = number_format($finish_arr[$sales_id][$determination_id][$grey_color_row['color_id']]['delivery_to_store'], 2, '.', '');
					$prod_floor_bal = number_format(($fin_qnty - $fin_del_to_store), 2, '.', '');

					$fin_rec_by_textile = number_format($finish_arr[$sales_id][$determination_id][$grey_color_row['color_id']]["fin_tex_rcv"], 2, '.', '');
					$fin_deli_to_garments = number_format($finish_arr[$sales_id][$determination_id][$grey_color_row['color_id']]["fin_deli_to_garments"], 2, '.', '');

					$delivery_balance=number_format($req_fin_qnty-$fin_deli_to_garments, 2, '.', '');
					$less_delivery=number_format($delivery_balance/$req_fin_qnty*100, 2, '.', '');
					$finish_fabric_available=number_format($fin_rec_by_textile+$fin_transfer_in-$fin_transfer, 2, '.', '');
					

					$html .="<td width='100'><a  href='##' onclick=\"open_febric_receive_status_order_wise_popup('" . $sales_id . "','dyeing_popup'," . $grey_color_row['color_id'].",".$determination_id . ")\">" . (($dye_qnty > 0)?$dye_qnty:"") ."</a></td>
					<td width='100'>" . (($dye_bal > 0)?$dye_bal:"") ."</td>
					<td width='100'>". $req_fin_qnty ."</td>

					<td width='100'><a  href='##' onclick=\"open_febric_receive_status_order_wise_popup('" . $sales_id . "','production_qty_popup'," . $grey_color_row['color_id'].",".$determination_id . ")\">" . (($fin_qnty > 0)?$fin_qnty:"") ."</a></td>
					
					<td width='100'>". $fin_bal ."</td>

					<td width='100'><a  href='##' onclick=\"open_febric_receive_status_order_wise_popup('" . $sales_id . "','fin_delv_to_store_popup'," . $grey_color_row['color_id'].",".$determination_id . ")\">" . (($fin_del_to_store > 0)?$fin_del_to_store:"") ."</a></td>
					<td width='100'>" . (($prod_floor_bal > 0)?$prod_floor_bal:"") ."</td>
					<td width='100'><a  href='##' onclick=\"open_febric_receive_status_order_wise_popup('" . $sales_id . "','fin_rec_by_textile_popup'," . $grey_color_row['color_id'].",".$determination_id . ")\">" . (($fin_rec_by_textile > 0)?$fin_rec_by_textile:"") ."</a></td>
					<td width='100'>". $fin_transfer_in ."</td>
					<td width='100'>". $fin_transfer ."</td>
					<td width='100'>". $finish_fabric_available ."</td>
					<td width='100'><a  href='##' onclick=\"open_febric_receive_status_order_wise_popup('" . $sales_id . "','deli_to_garments_popup'," . $grey_color_row['color_id'].",".$determination_id . ")\">" . (($fin_deli_to_garments > 0)?$fin_deli_to_garments:"") ."</a></td>	
					<td width='100'>". $delivery_balance ."</td>				
					<td width='100'>" . $less_delivery ."</td>
					</tr>";
					$t++;
					$batch_qnty_bal +=$batch_qnty;


					$total_dye_qnty += $dye_qnty;
					$total_dye_bal += $dye_bal;
					$total_req_fin_qnty += $req_fin_qnty;
					$total_fin_bal += $fin_bal;
					$total_prod_floor_bal += $prod_floor_bal;
					// $total_less_delivery += $less_delivery;
					
					$total_fin_qnty += $fin_qnty;
					$total_fin_del_to_store += $fin_del_to_store;
					$total_fin_rec_by_textile += $fin_rec_by_textile;
					$total_fin_deli_to_garments += $fin_deli_to_garments;
					$total_finish_fabric_available += $finish_fabric_available;
					$total_delivery_balance += $delivery_balance;
				}
				$total_yarn_req += $yarn_required;
				$total_yarn_requisition += $yarn_requisition;
				$total_yarn_issue += $yarn_issue;
				$total_yarn_balance += $yarn_requisition-$yarn_issue;

				$total_fab_req += $grey_fabric_row["grey_qty"];
				$total_knitting_production += $knitting_production;
				$total_knitting_balance += $knitting_balance;
				$total_delivery_to_store += $delivery_to_store;
				$total_grey_in_knit_floor += $grey_in_knit_floor;
				$total_grey_receive_qnty += $grey_receive_qnty;
				$total_grey_net_return += $net_return;
				$total_net_transfer += $net_transfer;//out
				$total_fin_transfer += $fin_transfer;//out
				$total_grey_available += $grey_available;
				$total_grey_balance += $grey_balance;
				$total_grey_in_hand += $grey_in_hand;
				$total_grey_issue_qnty += $issue_qnty;
				$total_grey_issue_bal += $grey_issue_bal;
				$receive_by_batch_bal += $receive_by_batch;
				$total_batch_qnty += $batch_qnty_bal;
				$total_yet_to_batch += $yet_to_batch;

				

				$buyer_arr[] = $row[csf('customer_buyer')];

				$buyer_wise_total_fab_req[$row[csf('customer_buyer')]]['issue_transfer'] += $yarn_issue + $net_transfer + $fin_transfer;
				$buyer_wise_total_fab_req[$row[csf('customer_buyer')]]['yarn_balance'] += $yarn_requisition-$yarn_issue;

				$buyer_wise_total_fab_req[$row[csf('customer_buyer')]]['grey_req'] += $grey_fabric_row["grey_qty"];
				$buyer_wise_total_fab_req[$row[csf('customer_buyer')]]['grey_available'] += $grey_available;
				$buyer_wise_total_fab_req[$row[csf('customer_buyer')]]['grey_recv_balance'] += $grey_receive_qnty;
				$buyer_wise_total_fab_req[$row[csf('customer_buyer')]]['grey_issue'] += $issue_qnty;
				$buyer_wise_total_fab_req[$row[csf('customer_buyer')]]['receive_by_batch'] += $receive_by_batch;
				$buyer_wise_total_fab_req[$row[csf('customer_buyer')]]['batch_qnty'] += $batch_qnty_bal;
				$buyer_wise_total_fab_req[$row[csf('customer_buyer')]]['yet_to_batch'] += $yet_to_batch;

				$buyer_wise_total_fab_req[$row[csf('customer_buyer')]]['dye_qnty'] += $dye_qnty;
				$buyer_wise_total_fab_req[$row[csf('customer_buyer')]]['dye_bal'] += $dye_bal;
				$buyer_wise_total_fab_req[$row[csf('customer_buyer')]]['req_fin_qnty'] += $req_fin_qnty;
				$buyer_wise_total_fab_req[$row[csf('customer_buyer')]]['fin_qnty'] += $fin_qnty;
				$buyer_wise_total_fab_req[$row[csf('customer_buyer')]]['fin_bal'] += $fin_bal;
				$k++;
			}

			foreach ($grey_fabric_transfer_details as $transfer_row) 
			{
				$fab_rowspan="";
				$determination_id = $transfer_row["detarmination_id"];
				$gsm = $transfer_row["gsm"];
				$dia_width = $transfer_row["dia_width"];
				$transfer_in = ($transfer_row["transfer_qnty"] != 0) ? number_format($transfer_row["transfer_qnty"], 2, '.', '') : "";
				$html .= ($k > 0) ? "<tr bgcolor='$bgcolor' onclick=\"change_color('tr_" . $i . "','#FFFFFF')\" id=\"tr_$i\">" : "";
				$html .= "<td width='80'></td>
				<td width='80'></td>
				<td width='100'></td>
				<td width='70'></td>";
				$html .= "<td width='280' class='center' $fab_rowspan>" . $transfer_row["fabric_desc"] . " <strong style='color:red;'>(T)</strong></td>
				<td width='80' $fab_rowspan></td>
				<td width='100' $fab_rowspan></td>
				<td width='100' $fab_rowspan></td>
				<td width='100' $fab_rowspan></td>
				<td width='100' $fab_rowspan></td>
				<td width='100' $fab_rowspan></td>
				<!--<td width='100' $fab_rowspan></td>-->
				<td width='100' $fab_rowspan><a  href='##' onclick=\"open_febric_receive_status_order_wise_popup('" . $sales_id . "','yarn_trans_in_popup','','" . $determination_id ."_".$gsm."_".$dia_width. "')\">" . $transfer_in . "</a></td>
				<td width='100' $fab_rowspan></td>
				<td width='100' $fab_rowspan></td>
				<td width='100' $fab_rowspan></td>
				<td width='100' $fab_rowspan></td>
				<td width='100' $fab_rowspan></td>
				<td width='100' $fab_rowspan></td>
				<td width='100' $fab_rowspan></td>";
				$html .= "<td width='100' class='center'></td>
				<td width='100'></td>
				<td width='100'></td>
				<td width='100'></td>
				<td width='100'></td>
				<td width='100'></td>
				<td width='100'></td>
				<td width='100'></td>
				<td width='100'></td>
				<td width='100'></td>
				<td width='100'></td>
				<td width='100'></td>
				<td width='100'></td>
				<td width='100'></td>
				<td width='100'></td>
				<td width='100'></td>
				<td width='100'></td>
				</tr>";

				$total_net_transfer_in += $transfer_in;
				$k++;
			}

			$i++;
		}

		$html .= "<tr><th width='40'></th>
		<th width='100'></th>
		<th width='100'></th>
		<th width='100'></th>
		<th width='100'></th>
		<th width='50'></th>
		<th width='100'></th>
		<th width='100' ></th>
		<th width='165'></th>
		<th width='165'></th>		
		<th width='80'></th>
		<th width='100'></th>
		<th width='100'></th>
		<th width='100'></th>
		<th width='100'></th>
		<th width='100'></th>		
		<th width='80'> " . number_format($total_yarn_req, 2) . "</th>
		<th width='80'> " . number_format($total_yarn_requisition, 2) . "</th>
		<th width='100'> " . number_format($total_yarn_issue, 2) . "</th>
		<th width='70'> " . number_format($total_yarn_balance, 2) . "</th>
		<th width='280'></th>
		<th width='80'> " . number_format($total_fab_req, 2) . " </th>
		<th width='100'> " . number_format($total_knitting_production, 2) . " </th>
		<th width='100'> " . number_format($total_knitting_balance, 2) . " </th>
		<th width='100'> " . number_format($total_delivery_to_store, 2) . " </th>
		<th width='100'> " . number_format($total_grey_in_knit_floor, 2) . " </th>
		<th width='100'> " . number_format($total_grey_receive_qnty, 2) . " </th>
		<!--<th width='100'> " . number_format($total_grey_net_return, 2) . " </th>-->
		<th width='100'> " . number_format($total_net_transfer_in, 2) . " </th>
		<th width='100'> " . number_format($total_net_transfer, 2) . " </th>
		<th width='100'> " . number_format($total_grey_available, 2) . " </th>
		<th width='100'> " . number_format($total_grey_balance, 2) . " </th>
		<th width='100'> " . number_format($total_grey_issue_qnty, 2) . " </th>
		<th width='100'> " . number_format($total_grey_in_hand, 2) . " </th>
		<th width='100'> " . number_format($total_grey_issue_bal, 2) . " </th>
		<th width='100'>" . number_format($receive_by_batch_bal, 2) . "</th>
		<th width='100'></th>
		<th width='100'>" . number_format($total_batch_qnty, 2) . "</th>
		<th width='100'>" . number_format($total_yet_to_batch, 2) . "</th>
		<th width='100'>".number_format($total_dye_qnty, 2)."</th>
		<th width='100'>".number_format($total_dye_bal, 2)."</th>
		<th width='100'>".number_format($total_req_fin_qnty, 2)."</th>
		<th width='100'>".number_format($total_fin_qnty, 2)."</th>
		<th width='100'>".number_format($total_fin_bal, 2)."</th>
		<th width='100'>".number_format($total_fin_del_to_store, 2)."</th>
		<th width='100'>".number_format($total_prod_floor_bal, 2)."</th>
		<th width='100'>".number_format($total_fin_rec_by_textile, 2)."</th>
		<th width='100'> " . number_format($total_fin_transfer_in, 2) . " </th>
		<th width='100'> " . number_format($total_fin_transfer, 2) . " </th>
		<th width='100'>".number_format($total_finish_fabric_available, 2)."</th>
		<th width='100'>".number_format($total_fin_deli_to_garments, 2)."</th>
		<th width='100'>".number_format($total_delivery_balance, 2)."</th>
		<th width='100'></th>
		</tr>";
	}
	// echo "Execution Time: " . (microtime(true) - $started) . "S";die; // in seconds
	?>

	<fieldset width="3900">
		<table cellpadding="5" cellspacing="0" width="3900">
			<tr>
			<td width="100%" colspan="<? echo $colspan + 29; ?>" style="font-size:16px; text-align: left !important;">
				<strong><?php echo $company_library[$company_name]; ?></strong></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="<? echo $colspan + 29; ?>" style="font-size:16px; text-align: left !important;">
					<strong><? if ($start_date != "" && $end_date != "") echo "From " . change_date_format($start_date) . " To " . change_date_format($end_date); ?></strong>
				</td>
			</tr>
		</table>

		<!-- Details part Start -->
		<table class="rpt_table" border="1" rules="all" width="3900" cellpadding="5" cellspacing="0"
		id="tbl_list_search">
		<thead>
			<tr>
				<th colspan="16">Order Details</th>
				<th colspan="5">Yarn Status</th>
				<th colspan="6">Knitting Production</th>
				<th colspan="8">Grey Fabric Store Status</th>
				<th colspan="5">Deying Production</th>
				<th colspan="5">Finish Fabric Production</th>
				<th colspan="7">Finish Fabric Store Status</th>
			</tr>
			<tr>
				<th width="40" rowspan="2">SL</th>
				<th width="100" rowspan="2">Team Name</th>
				<th width="100" rowspan="2">Team Leader</th>
				<th width="100" rowspan="2">Dealing Merchant</th>
				<th width="100" rowspan="2">Gmt. Merchant</th>
				<th width="50" rowspan="2">Year</th>
				<th width="100" rowspan="2">Cust. Buyer</th>
				<th width="100" rowspan="2">Customer</th>
				<th width="165" rowspan="2">Fabric Booking No</th>
				<th width="165" rowspan="2">Sales Order Number</th>	
				<th width="80" rowspan="2">Style Ref.</th>			
				<th width="100" rowspan="2">Within Group</th>
				<th width="100" rowspan="2">Delivery Start Date</th>
				<th width="100" rowspan="2">Delivery End Date</th>
				<th width="100" rowspan="2">Confirm Finish Qty</th>
				<th width="100" rowspan="2">Process Loss%</th>
				<th width="80" rowspan="2">Required<br/>
					<small>(As Per Sales Order)</small>
				</th>
				<th width="80" rowspan="2">Required<br/>
					<small>(As Per Requisition)</small>
				</th>
				<th width="100" rowspan="2">Issued</th>
				<th width="70" rowspan="2">Balance<br/>
					<small>(Required as per requisition - Issue)</small>
				</th>
				<th width="280" rowspan="2">Fabric Description</th>
				<th width="80" rowspan="2">Grey Required<br/>
					<small>(As Per Sales Order)</small>
				</th>
				<th width="100" rowspan="2">Grey Production</font></th>
				<th width="100" rowspan="2">Knitting Balance<br/>
					<small>(Grey Required - Grey Prod.)</small>
				</th>
				<th width="100" rowspan="2">Grey Fab Delv. To Store</th>
				<th width="100" rowspan="2">Grey in Knit Floor<br/>
					<small>(Grey Prod. - Grey Fab Delv. To Store)</small>
				</th>
				<th width="100" rowspan="2">Grey Receive</th>
				<!-- <th width="100" rowspan="2">Net Return<br/>
					<small>(Issue Return - Rcvd Return)</small>
				</th> -->
				<th width="80" colspan="2">Transfer</th>
				<th width="100" rowspan="2">Grey Available<br/>
					<small>(Grey Receive + Transfer In - Transfer Out)</small>
				</th>
				<th width="100" rowspan="2">Grey Balance<br/>
					<small>(Grey Required - Available)</small>
				</th>
				<th width="100" rowspan="2">Grey Issue</th>
				<th width="100" rowspan="2">Grey In Hand<br/>
					<small>(Grey Available - Grey Issue)</small>
				</th>
				<th width="100" rowspan="2">Grey Issue Bal.<br/>
					<small>(Required-Grey Issue)</small>
				</th>
				<th width="100" rowspan="2">Receive By Batch</th>
				<th width="100" rowspan="2">Fabric Color</th>
				<th width="100" rowspan="2">Batch Qnty</th>
				<th width="100" rowspan="2">Yet To Batch<br/>
					<small>(Grey Issue - Batch Qty)</small>
				</th>
				<th width="180" rowspan="2">Dye Qnty</th>
				<th width="180" rowspan="2">Balance Qty</th>
				<th width="100" rowspan="2">Req. Qty<br/>
					<small>(As Per Seals Order)</small>
				</th>
				<th width="100" rowspan="2">Production Qty</th>
				<th width="100" rowspan="2">Balance Qty</th>
				<th width="100" rowspan="2">Finish Fab. Delv. To Store</th>
				<th width="100" rowspan="2">Fabric in Prod. Floor</th>
				<th width="100" rowspan="2">Finish Fab. Recv. By textile</th>
				<th width="80" colspan="2">Transfer</th>
				<th width="100" rowspan="2">Finish Fabric Available</th>
				<th width="100" rowspan="2">Finish Fab. Deli. To Garments</th>
				<th width="100" rowspan="2">Delivery Balance</th>
				<th width="100" rowspan="2">Less Delivery %</th>
			</tr>
			<tr>
				<th>In</th>
				<th>Out</th>
				<th>In</th>
				<th>Out</th>
			</tr>
		</thead>
			<tbody>
				<?php echo ($html != "") ? $html : "<tr><td colspan='40' style='text-align: left !important; color: red; font-weight: bold; font-size: 16px; padding-left: 10px;'>No Data Found</td></tr>"; 
				?>
			</tbody>
    	</table>
    	<br/>
    	<!-- Details part end -->

    	<? //die;?>

    	<!-- Summary Start-->
    	<table id="summary" border=1 rules='all' class="rpt_table" style="float: left; width: 10%;">
    		<thead>
    			<tr>
    				<th colspan='3'>Summary</th>
    			</tr>
    			<tr bgcolor='#FFFFFF'>
    				<th>Particulars</th>
    				<th>Total Qnty</th>
    				<th>% On Required</th>
    			</tr>
    		</thead>
    		<tbody>
    			<tr bgcolor='#FFFFFF'>
    				<td class="left">Total Yarn Required</td>
    				<td class="right"><?php echo number_format($total_yarn_req, 2); ?></td>
    				<td class="right"></td>
    			</tr>
    			<tr bgcolor='#FFFFFF'>
    				<td>Total Yarn Issued To Knitting</td>
    				<td class="right"><?php echo number_format($total_yarn_issue, 2); ?></td>
    				<td class="right"><?php echo number_format($total_yarn_issue / $total_yarn_req * 100, 2); ?></td>
    			</tr>
    			<tr style='background-color:#CFF; font-weight:bold'>
    				<td>Total Yarn Balance</td>
    				<td class="right"><?php echo number_format($total_yarn_balance, 2); ?></td>
    				<td class="right"><?php echo number_format(((($total_yarn_balance) / $total_yarn_req) * 100), 2); ?></td>
    			</tr>
    			<tr bgcolor='#FFFFFF'>
    				<td>Total Grey Fabric Required</td>
    				<td class="right"><?php echo number_format($total_fab_req, 2); ?></td>
    				<td class="right"></td>
    			</tr>
    			<tr bgcolor='#FFFFFF'>
    				<td>Total Grey Fabric Available</td>
    				<td class="right"><?php echo number_format($total_grey_available, 2); ?></td>
    				<td class="right"><?php echo number_format(($total_grey_available) / $total_fab_req * 100, 2); ?></td>
    			</tr>
    			<tr bgcolor='#FFFFFF'>
    				<td>Total Grey Fabric Issued To Dye</td>
    				<td class="right"><?php echo number_format($total_grey_issue_qnty, 2); ?></td>
    				<td class="right"><?php echo number_format($total_grey_issue_qnty / $total_fab_req * 100, 2); ?></td>
    			</tr>
    			<tr style='background-color:#CFF; font-weight:bold'>
    				<td>Total Grey Fabric Issue Balance</td>
    				<td class="right"><?php echo number_format($total_grey_issue_bal, 2); ?></td>
    				<td class="right"><?php echo number_format(((($total_grey_issue_bal) / $total_fab_req) * 100), 2); ?></td>
    			</tr>
    			<tr bgcolor='#FFFFFF'>
    				<td>Total Batch Qnty</td>
    				<td class="right"><?php echo number_format($total_batch_qnty, 2); ?></td>
    				<td class="right"><?php echo number_format(((($total_batch_qnty) / $total_fab_req) * 100), 2); ?></td>
    			</tr>
    			<tr style='background-color:#CFF; font-weight:bold'>
    				<td>Total Batch Balance To Grey Required</td>
    				<td class="right"><?php echo number_format($total_fab_req - $total_batch_qnty, 2); ?></td>
    				<td class="right">
						<b>
						<? 
						$total_batch_balance_to_grey_required_perc = (($total_fab_req - $total_batch_qnty)/$total_fab_req)*100;
						echo number_format($total_batch_balance_to_grey_required_perc,2);?>
						</b>
					</td>
    			</tr>
    			<tr bgcolor='#FFFFFF'>
    				<td>Total Dye Qnty</td>
    				<td class="right"><?php echo number_format($total_dye_qnty, 2); ?></td>
    				<td class="right"><?php echo number_format(((($total_dye_qnty) / $total_fab_req) * 100), 2); ?></td>
    			</tr>
    			<tr style='background-color:#CFF; font-weight:bold'>
    				<td>Total Dye Balance To Grey Required</td>
    				<td class="right"><?php echo number_format($total_fab_req - $total_dye_qnty, 2); ?></td>
    				<td class="right"><?php echo number_format(($total_fab_req - $total_dye_qnty) / $total_fab_req * 100, 2); ?></td>
    			</tr>
    			<tr bgcolor='#FFFFFF'>
    				<td>Total Finish Fabric Required</td>
    				<td class="right"><?php echo number_format($total_req_fin_qnty, 2); ?></td>
    				<td class="right"></td>
    			</tr>
    			<tr bgcolor='#FFFFFF'>
    				<td>Total Finish Fabric Receive</td>
    				<td class="right"><?php echo number_format($total_fin_qnty, 2); ?></td>
    				<td class="right">
						<b>
						<? 
						$total_finish_fabric_recv_perc = ($total_fin_qnty /$total_req_fin_qnty)*100;
						echo number_format($total_finish_fabric_recv_perc,2);?>
						</b>
					</td>
    			</tr>
    			<tr style='background-color:#CFF; font-weight:bold'>
    				<td>Total Finish Fabric Balance</td>
    				<td class="right"><?php echo number_format($total_req_fin_qnty-$total_fin_qnty, 2); ?></td>
    				<td class="right"><?php echo number_format(($total_req_fin_qnty-$total_fin_qnty)/$total_req_fin_qnty*100, 2); ?></td>
    			</tr>
    			<tr bgcolor='#FFFFFF'>
    				<td>Total Finish Fabric Issued To Garments</td>
    				<td class="right"><?php echo number_format($total_fin_deli_to_garments, 2); ?></td>
    				<td class="right">
					<b>
						<? 
						$total_finish_fabric_iss_to_garments_perc = ($total_fin_deli_to_garments /$total_req_fin_qnty)*100;
						echo number_format($total_finish_fabric_iss_to_garments_perc,2);?>
						</b>
					</td>
    			</tr>
    		</tbody>
    	</table>
    	<!-- Summary End-->

    	<!-- Buyer Level Summary Start-->
    	<table width="1920" class="rpt_table" border="0" rules="all" style="float: left; width: 10%; margin-left:20px;">
    		<thead>
    			<tr align="center">
    				<th colspan="19">Cust. Buyer Level Summary</th>
    			</tr>
    			<tr>
    				<th width="40">SL</th>
    				<th width="130">Cust. Buyer Name</th>
    				<th width="100">Grey Req</th>
    				<th width="100">Yarn Issue</th>
    				<th width="100">Yarn Balance</th>
    				<th width="100">Grey Fabric Available</th>
    				<th width="100">Grey Receive Balance</th>
    				<th width="100">Gery To Dye</th>
    				<th width="100">Receive By Batch</th>
    				<th width="100">Batch Qnty</th>
    				<th width="100">Yet To Batch</th>
    				<th width="100">Batch Balance</th>
    				<th width="100">Total Dye Qnty</th>
    				<th width="100">Dyeing Balance</th>
    				<th width="100">Finish Fabric Req</th>
    				<th width="100">Fininish Fabric Available</th>
    				<th width="100">Finish Fabric Balance</th>
    				<th>Issue To Garments</th>
    			</tr>
    		</thead>
    		<tbody>
    			<?
    			$b_sl = 1;
    			// echo "<pre>";print_r($buyer_arr);
    			asort($buyer_arr);
    			foreach (array_unique($buyer_arr) as $buyer) {
    				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
    				?>
    				<tr bgcolor="<? echo $bgcolor; ?>">
    					<td width="40"><? echo $b_sl; ?></td>
    					<td width="130"	align="center"><? echo $buyer_name_array[$buyer]; ?></td>
    					<td width="100"	align="right"><? echo number_format($buyer_wise_total_fab_req[$buyer]['grey_req'], 2);
    					$grey_required_array_tot += $buyer_wise_total_fab_req[$buyer]['grey_req']; ?></td>
    					<td width="100"	align="right">
    						<?
    						echo number_format($buyer_wise_total_fab_req[$buyer]['issue_transfer'], 2);
    						$yarn_issue_array_tot += $buyer_wise_total_fab_req[$buyer]['issue_transfer'];
    						?>
    					</td>
    					<td width="100" align="right">
    						<?
    						echo number_format($buyer_wise_total_fab_req[$buyer]['yarn_balance'], 2);
    						$yarn_balance_array_tot += $buyer_wise_total_fab_req[$buyer]['yarn_balance'];
    						?>
    					</td>
    					<td width="100" align="right">
    						<?
    						echo number_format($buyer_wise_total_fab_req[$buyer]['grey_available'], 2);
    						$knitted_array_tot += $buyer_wise_total_fab_req[$buyer]['grey_available'];
    						?>
    					</td>
    					<td width="100" align="right">
    						<? echo number_format($buyer_wise_total_fab_req[$buyer]['grey_recv_balance'], 2);
    						$grey_balance_array_tot += $buyer_wise_total_fab_req[$buyer]['grey_recv_balance'];
    						?>
    					</td>
    					<td width="100" align="right">
    						<?
    						echo number_format($buyer_wise_total_fab_req[$buyer]['grey_issue'], 2);
    						$grey_issue_array_tot += $buyer_wise_total_fab_req[$buyer]['grey_issue'];
    						?>
    					</td>
    					<td width="100" align="right">
    						<? echo number_format($buyer_wise_total_fab_req[$buyer]['receive_by_batch'], 2);
    						$receive_by_batch_array_tot += $buyer_wise_total_fab_req[$buyer]['receive_by_batch'];
    						?>
    					</td>
    					<td width="100" align="right">
    						<?
    						echo number_format($buyer_wise_total_fab_req[$buyer]['batch_qnty'], 2);
    						$batch_qnty_array_tot += $buyer_wise_total_fab_req[$buyer]['batch_qnty'];
    						?>
    					</td>
    					<td width="100" align="right" title="Receive By Batch-Batch Quantity">
    						<?
    						echo number_format($buyer_wise_total_fab_req[$buyer]['yet_to_batch'], 2);
    						$yet_to_batch_array_tot += $buyer_wise_total_fab_req[$buyer]['yet_to_batch'];
    						?>
    					</td>
    					<td width="100" align="right">
    						<?
    						echo number_format($batch_bl, 2);
    						$batch_bl_tot += $batch_bl;
    						?>
    					</td>
    					<td width="100" align="right">
    						<?
    						echo number_format($buyer_wise_total_fab_req[$buyer]['dye_qnty'], 2);
    						$dye_qnty_array_tot += $buyer_wise_total_fab_req[$buyer]['dye_qnty'];
    						?>
    					</td>
    					<td width="100" align="right">
    						<?
    						echo number_format($buyer_wise_total_fab_req[$buyer]['dye_bal'], 2);
    						$dye_bl_tot += $buyer_wise_total_fab_req[$buyer]['dye_bal'];
    						?>
    					</td>
    					<td width="100" align="right">
    						<?
    						echo number_format($buyer_wise_total_fab_req[$buyer]['req_fin_qnty'], 2);
    						$fin_fab_Requi_array_tot += $buyer_wise_total_fab_req[$buyer]['req_fin_qnty'];
    						?>
    					</td>
    					<td width="100" align="right">
    						<?
    						echo number_format($buyer_wise_total_fab_req[$buyer]['fin_qnty'], 2);
    						$fin_fab_recei_array_tot += $buyer_wise_total_fab_req[$buyer]['fin_qnty'];
    						?>
    					</td>
    					<td width="100" align="right">
    						<?
    						echo number_format($buyer_wise_total_fab_req[$buyer]['fin_bal'], 2);
    						$fin_balance_array_tot += $buyer_wise_total_fab_req[$buyer]['fin_bal'];
    						?>
    					</td>
    					<td align="right">
    						<? echo number_format($issue_toCut_array[$buyer], 2);
    						$issue_toCut_array_tot += $issue_toCut_array[$buyer];
    						?>
    					</td>
    				</tr>
    				<?
    				$b_sl++;
    			}
    			?>
    		</tbody>
    		<tfoot>
    			<tr>
    				<th width="40" colspan="2" align="right">Total</th>
    				<th width="100" align="right"><? echo number_format($grey_required_array_tot, 2); ?></th>
    				<th width="100" align="right"><? echo number_format($yarn_issue_array_tot, 2); ?></th>
    				<th width="100" align="right"><? echo number_format($yarn_balance_array_tot, 2); ?></th>
    				<th width="100" align="right"><? echo number_format($knitted_array_tot, 2); ?></th>
    				<th width="100" align="right"><? echo number_format($grey_balance_array_tot, 2); ?></th>
    				<th width="100" align="right"><? echo number_format($grey_issue_array_tot, 2); ?></th>
    				<th width="100" align="right"><? echo number_format($receive_by_batch_array_tot, 2); ?></th>
    				<th width="100" align="right"><? echo number_format($batch_qnty_array_tot, 2); ?></th>
    				<th width="100" align="right"><? echo number_format($yet_to_batch_array_tot, 2); ?></th>
    				<th width="100" align="right"><? echo number_format($batch_bl_tot, 2); ?></th>
    				<th width="100" align="right"><? echo number_format($dye_qnty_array_tot, 2); ?></th>
    				<th width="100" align="right"><? echo number_format($dye_bl_tot, 2); ?></th>
    				<th width="100" align="right"><? echo number_format($fin_fab_Requi_array_tot, 2); ?></th>
    				<th width="100" align="right"><? echo number_format($fin_fab_recei_array_tot, 2); ?></th>
    				<th width="100" align="right"><? echo number_format($fin_balance_array_tot, 2); ?></th>
    				<th align="right"><? echo number_format($issue_toCut_array_tot, 2); ?></th>
    			</tr>
    		</tfoot>
    	</table>
    	<!-- Buyer Level Summary End-->
    </fieldset>

    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_name*.xls") as $filename) {
    	@unlink($filename);

    }
    //---------end------------//
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename";
    exit;
}

if($action == "report_generate_2") // Summary
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$company_name = str_replace("'", "", $cbo_company_name);
	$buyer_name = str_replace("'", "", trim($cbo_buyer_name));
	$sales_job_no = str_replace("'", "", $txt_sales_job_no);
	$hide_job_id = str_replace("'", "", $hide_job_id);
	$sales_booking_no = str_replace("'", "", $txt_booking_no);
	$hide_booking_id = str_replace("'", "", $hide_booking_id);
	$start_date = str_replace("'", "", trim($txt_date_from));
	$end_date = str_replace("'", "", trim($txt_date_to));
	$cbo_year_selection = str_replace("'", "", trim($cbo_year_selection));
	$cbo_within_group = str_replace("'", "", trim($cbo_within_group));
	$hilight_bg = "";

	if($db_type==0)
	{
		$year_cond=" and YEAR(a.insert_date)=$cbo_year_selection";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";
	}
	else
	{
		$year_cond="";;
	}

	if ($end_date == "") {
		$end_date = $start_date;
	} else {
		$end_date = $end_date;
	}

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$str_cond_insert = " and a.delivery_date between '" . $start_date . "' and '" . $end_date . "'";
		} else {
			$str_cond_insert = " and a.delivery_date between '" . $start_date . "' and '" . $end_date . "'";
		}
	} else {
		$str_cond_insert = "";
	}
	if($hide_job_id == ""){
		$sales_order_cond = ($sales_job_no != "") ? " and a.job_no_prefix_num=$sales_job_no" : "";
	}else{
		$sales_order_cond = " and a.id in($hide_job_id)";
	}
	if($hide_booking_id == ""){
		$sales_booking_cond = ($sales_booking_no != "") ? " and a.sales_booking_no like '%$sales_booking_no%'" : "";
	}else{
		$sales_booking_cond = " and a.sales_booking_no='$sales_booking_no'";
	}

	$buyer_cond = ($buyer_name != 0) ? " and a.customer_buyer=$buyer_name" : "";

	$dataArraySalesOrder = array();
	$started = microtime(true);
	// SALES ORDER DATA RESULT
	// ,c.id booking_id
	$salesOrderDataSql = "SELECT a.booking_id,a.id,a.buyer_id sales_buyer,a.within_group,a.entry_form as mst_entry_form,c.buyer_id,a.company_id,a.job_no sales_job_no, a.job_no_prefix_num,a.style_ref_no,a.delivery_date sales_order_dt,a.delivery_date,c.po_break_down_id,a.sales_booking_no booking_no, c.fabric_composition,c.is_short,c.fabric_source,c.job_no,c.is_approved,c.item_category,c.entry_form,c.booking_type,a.within_group, a.team_leader, a.dealing_marchant, a.garments_marchant, a.customer_buyer, a.delivery_start_date 
	from fabric_sales_order_mst a left join wo_booking_mst c on a.booking_id = c.id  
	where a.is_deleted=0 and a.status_active=1 $str_cond_insert and a.company_id = $company_name and a.entry_form = 472 $sales_order_cond $sales_booking_cond $buyer_cond $year_cond order by a.job_no, a.delivery_date desc";
	//and a.within_group=$cbo_within_group

	// echo $salesOrderDataSql; 

	$salesOrderDataResult = sql_select($salesOrderDataSql);
	$sales_order_ids = $job_no_arr = array();
	if (!empty($salesOrderDataResult)) 
	{
		foreach ($salesOrderDataResult as $row) 
		{
			$sales_order_ids[] = $row[csf("id")];
			$job_no_arr[] = "'".$row[csf("job_no")]."'";
			$booking_no_arr[] = "'".$row[csf("booking_no")]."'";
		}
		$sales_ids = implode(",",$sales_order_ids);
		$job_nos = implode(",",$job_no_arr);
		$booking_nos = implode(",",array_unique($booking_no_arr));


		if($sales_ids != "")
		{
			$sales_ids_arr = explode(",", $sales_ids);
			$fin_rcv_trans_iss_fso_Cond=""; $fsoCond_14="";

			if($db_type==2 && count($sales_ids_arr)>999)
			{
				$sales_ids_arr_chunk=array_chunk($sales_ids_arr,999) ;
				foreach($sales_ids_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$fsoCond_14.=" c.po_breakdown_id in($chunk_arr_value) or ";
				}

				$fin_rcv_trans_iss_fso_Cond.=" and (".chop($fsoCond_14,'or ').")";
			}
			else
			{
				$fin_rcv_trans_iss_fso_Cond=" and c.po_breakdown_id in ($sales_ids)";
			}
		}


		// PREPARE REQUIRED ARRAY LIBRARIES
		$buyer_name_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
		$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
		$color_array = return_library_array("select id, color_name from lib_color", "id", "color_name");
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyer_short_name_library = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
		$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
		$dealing_marchant_array = return_library_array("select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");

		$team_sql_data=sql_select("SELECT id, team_name, team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0");
		foreach ($team_sql_data as $key => $row) 
		{
			$team_name_arr[$row[csf("id")]] = $row[csf("team_name")];
			$team_leader_name_arr[$row[csf("id")]] = $row[csf("team_leader_name")];
		}

		$yarn_qty_requisition_arr = array();
		$composition_arr = array();
		$style_owner_arr = array();
		$yarn_details_arr = array();
		$yarn_issue_details_arr = array();
		$grey_fabric_status_arr = array();
		$knitting_arr = $finish_arr = array();
		$grey_issue_qnty_arr = array();
		$dataArrayYarnIssue = array();
		$yarn_issue_details_arr1 = array();
		$yarn_details_found_arr = array();
		$buyer_wise_total_fab_req = array();
		$sales_color_arr = array();
		$sales_fin_qnty_arr = array();

		$salesOrderDetailsDataSql = "SELECT mst_id,determination_id,color_id,sum(finish_qty) finish_qty, sum(pp_qnty) pp_qnty, sum(mtl_qnty) mtl_qnty, sum(fpt_qnty) fpt_qnty, sum(gpt_qnty) gpt_qnty, sum(process_loss) process_loss from fabric_sales_order_dtls where is_deleted=0 and status_active=1 and mst_id in($sales_ids) group by mst_id,determination_id,color_id";
		$salesOrderDetailsDataResult = sql_select($salesOrderDetailsDataSql);
		foreach ($salesOrderDetailsDataResult as $row) 
		{
			$sales_color_arr[$row[csf('mst_id')]][] = array(
				'color_id' => $row[csf("color_id")]
			);
			$sales_fin_qnty_arr[$row[csf('mst_id')]] += $row[csf("finish_qty")];
			
			$confirm_finish_qty_arr[$row[csf('mst_id')]] += $row[csf('finish_qty')] + $row[csf('pp_qnty')] + $row[csf('mtl_qnty')] + $row[csf('fpt_qnty')] + $row[csf('gpt_qnty')];
			$process_loss_arr[$row[csf('mst_id')]] += $row[csf('process_loss')];
		}

		// PREPARE YARN REQUISITION DATA ARRAY
		$yarn_qty_requisition = sql_select("SELECT a.dtls_id, a.determination_id,b.knit_id,b.requisition_no,b.yarn_qnty,a.po_id,b.id,b.prod_id,c.yarn_count_id,c.yarn_comp_percent1st,c.yarn_comp_type1st,c.yarn_type from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join product_details_master c on b.prod_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.po_id in($sales_ids)");
		foreach ($yarn_qty_requisition as $row) 
		{
			$yarn_qty_requisition_arr[$row[csf('po_id')]][$row[csf('yarn_type')]] = $row[csf('yarn_qnty')];
			$yarn_qty_requisition_arr[$row[csf('po_id')]]['requisition'] = $row[csf('id')];
			$yarn_qty_requisition_arr[$row[csf('po_id')]]['requisition_qnty'] += $row[csf('yarn_qnty')];
			$yarn_qty_requisition_arr[$row[csf('po_id')]]['requisition_no'] .= $row[csf('requisition_no')].",";
		}

		$compositionData = sql_select("select mst_id, copmposition_id, percent from lib_yarn_count_determina_dtls where status_active=1 and is_deleted=0");
		foreach ($compositionData as $row) {
			$composition_arr[$row[csf('mst_id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
		}
		// STYLE OWNER INFO START
		$style_owner_info = sql_select("SELECT a.job_no, a.style_owner,b.booking_no from wo_po_details_master a,wo_booking_dtls b where a.job_no=b.job_no and a.status_active = 1 and a.is_deleted = 0 and a.style_owner != 0 and b.status_active=1 and b.booking_no in($booking_nos) group by a.job_no, a.style_owner,b.booking_no");
		foreach ($style_owner_info as $row) 
		{
			$style_owner_arr[$row[csf('booking_no')]]["style_owner"] = $row[csf('style_owner')];
			$style_owner_arr[$row[csf('booking_no')]]["job_no"] = $row[csf('job_no')];
		}
		// STYLE OWNER INFO END

		//
		$sql_yarn_iss = "SELECT a.po_id,a.determination_id,d.mst_id,sum(d.cons_quantity) cons_quantity from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join inv_transaction d on (b.requisition_no=d.requisition_no and d.transaction_type=2 and b.prod_id=d.prod_id) where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.po_id in($sales_ids) and a.is_sales=1 group by a.po_id,d.mst_id,a.determination_id";
		$dataArrayIssue = sql_select($sql_yarn_iss);
		foreach ($dataArrayIssue as $row_yarn_iss) 
		{
			$yarn_issue_details_arr[$row_yarn_iss[csf('po_id')]]['issue_qnty'] += $row_yarn_iss[csf('cons_quantity')];
			$yarn_issue_details_arr[$row_yarn_iss[csf('po_id')]]['issue_ids'] .= $row_yarn_iss[csf('mst_id')].",";
		}

		$yarn_allocate_sql="SELECT a.po_break_down_id as sales_order_id, a.item_id as item_id, a.qnty as qty
		FROM inv_material_allocation_mst a WHERE a.po_break_down_id in($sales_ids) and a.entry_form=475 AND a.item_category=1 AND a.status_active=1";
		$yarn_allocate_data = sql_select($yarn_allocate_sql);
		foreach ($yarn_allocate_data as $row) 
		{
			$yarn_allocate_arr[$row[csf('sales_order_id')]]['yarn_allocate'] += $row[csf('qty')];
		}

		// YARN DETAILS ARRAY START
		$yarn_sql = "SELECT b.mst_id, b.deter_id ,sum(b.cons_qty)cons_qty from fabric_sales_order_yarn_dtls b where b.status_active=1 and b.is_deleted=0 and b.mst_id in($sales_ids) group by b.mst_id, b.deter_id ";
		$yarn_info = sql_select($yarn_sql);
		foreach ($yarn_info as $row) 
		{
			$yarn_details_arr[$row[csf('mst_id')]] += $row[csf('cons_qty')];
		}

		// GREY FABRIC DETAILS ARRAY START
		$grey_fabric_status_sql = "SELECT a.mst_id, a.fabric_desc, a.determination_id,sum(a.grey_qty) grey_qty from fabric_sales_order_dtls a where a.status_active = 1 and a.is_deleted = 0 and a.mst_id in($sales_ids) group by a.mst_id,a.fabric_desc, a.determination_id";
		$grey_fabric_status_info = sql_select($grey_fabric_status_sql);
		foreach ($grey_fabric_status_info as $row) 
		{
			$grey_fabric_status_arr[$row[csf('mst_id')]][] = array(
				'grey_qty' => $row[csf('grey_qty')]
			);
		} // need to check
		// echo "<pre>";print_r($grey_fabric_status_arr);die;

		$dataArrayTrans = sql_select("SELECT a.po_breakdown_id, c.detarmination_id,
		sum(CASE when a.entry_form in (2) then quantity else 0 end) AS grey_receive,
		sum(CASE when a.entry_form in (58) then quantity else 0 end) AS grey_roll_receive,
		sum(CASE WHEN a.entry_form ='45' and a.trans_type=3 THEN quantity ELSE 0 END) AS grey_receive_return,
		sum(case when a.entry_form ='16' then a.quantity else 0 end) as grey_issue,
		sum(case when a.entry_form ='61' then a.quantity else 0 end) as grey_issue_roll_wise,
		sum(CASE WHEN a.entry_form ='51' and a.trans_type=4 THEN quantity ELSE 0 END) AS grey_issue_return,
		sum(case when a.entry_form ='133' and a.trans_type=6 then quantity else 0 end) as transfer_out,
		sum(case when a.entry_form ='133' and a.trans_type=5 then quantity else 0 end) as transfer_in
		from order_wise_pro_details a left join product_details_master c on a.prod_id=c.id
		where a.status_active=1 and a.is_deleted=0 and a.entry_form in(11,13,16,45,51,58,61,80,81,83,133) and a.po_breakdown_id in($sales_ids) and a.is_sales = 1
		group by a.po_breakdown_id, c.detarmination_id");
		foreach ($dataArrayTrans as $row) 
		{
			$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')] + $row[csf('grey_roll_receive')];
			$grey_issue_qnty_arr[$row[csf('po_breakdown_id')]]+=$row[csf('grey_issue')] + $row[csf('grey_issue_roll_wise')];
			$grey_receive_return_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive_return')];
			$grey_issue_return_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_issue_return')];
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]["transfer_out"] = $row[csf('transfer_out')];
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]["transfer_in"] = $row[csf('transfer_in')];
		}
		// echo "<pre>";print_r($grey_issue_qnty_arr);

		// grey fabric delivery to store
		$sql_grey_delivery = "SELECT a.order_id, a.entry_form, a.color_id, sum(a.current_delivery) as grey_delivery_qty, a.product_id, b.detarmination_id from pro_grey_prod_delivery_dtls a, product_details_master b where a.product_id=b.id and a.entry_form in(53,54,56,67) and a.status_active=1 and a.is_deleted=0 and a.order_id in($sales_ids) group by a.order_id, a.product_id, a.entry_form, a.color_id, b.detarmination_id";

		$data_grey_delivery = sql_select($sql_grey_delivery);
		foreach ($data_grey_delivery as $greyDel) 
		{
			if($greyDel[csf('entry_form')]==54 || $greyDel[csf('entry_form')]==67)
			{
				$finish_arr[$greyDel[csf('order_id')]]['delivery_to_store'] += $greyDel[csf('grey_delivery_qty')];
			}
			else
			{
				$knitting_arr[$greyDel[csf('order_id')]]['delivery_to_store'] += $greyDel[csf('grey_delivery_qty')];
			}
		}

		// GREY FABRIC DETAILS ARRAY END
		$knitting_sql = sql_select("SELECT a.id,a.booking_no,a.booking_id, sum(b.grey_receive_qnty) grey_receive_qnty,b.febric_description_id,b.body_part_id, b.prod_id,c.quantity,c.po_breakdown_id from inv_receive_master a inner join pro_grey_prod_entry_dtls b on a.id = b.mst_id left join order_wise_pro_details c on b.id=c.dtls_id where  a.status_active = 1 and a.is_deleted = 0 and b.status_active= 1 and b.is_deleted = 0 and c.entry_form in(2,11,13,45,51) and c.po_breakdown_id in($sales_ids) and is_sales = 1 group by a.id,a.booking_no,a.booking_id,b.febric_description_id,b.body_part_id,b.prod_id,c.quantity,c.po_breakdown_id");

		foreach ($knitting_sql as $row) 
		{
			$knitting_arr[$row[csf('po_breakdown_id')]]['prod_id'] = $row[csf("prod_id")];
			$knitting_arr[$row[csf('po_breakdown_id')]]['grey_receive_qnty'] += $row[csf("grey_receive_qnty")];
			$knitting_arr[$row[csf('po_breakdown_id')]]['issue_qnty'] += $row[csf("quantity")];
		}

		// RECEIVE BY BATCH ARRAY
		$receive_by_batch_sql=sql_select("SELECT c.po_breakdown_id,b.febric_description_id,sum(c.qnty) roll_wgt from pro_grey_batch_dtls b,inv_receive_mas_batchroll a,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and c.entry_form=62 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($sales_ids) group by c.po_breakdown_id,b.febric_description_id");
		$receive_by_batch_arr=array();
		foreach ($receive_by_batch_sql as $row) 
		{
			$receive_by_batch_arr[$row[csf("po_breakdown_id")]]['receive_qnty'] = $row[csf("roll_wgt")];
		}

		// BATCH ARRAY
		$batch_sql = "SELECT a.sales_order_id,a.color_id,c.detarmination_id,a.extention_no,sum(b.batch_qnty) qnty from pro_batch_create_mst a,pro_batch_create_dtls b,product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.sales_order_id in($sales_ids) and (a.extention_no is null or a.extention_no=0) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by a.sales_order_id,a.color_id,c.detarmination_id,a.extention_no";
		$batch_result = sql_select($batch_sql);
		$batch_arr=array();
		foreach ($batch_result as $row) 
		{
			$batch_arr[$row[csf("sales_order_id")]] += $row[csf("qnty")];
			$batch_arr[$row[csf("sales_order_id")]]["total_fab_batch"] += $row[csf("qnty")];
		}

		// DYEING PRODUCTION
		$sql_dye = "SELECT b.po_id, a.color_id,b.prod_id,d.detarmination_id, sum(b.batch_qnty) as dye_qnty from pro_batch_create_mst a, pro_batch_create_dtls b, pro_fab_subprocess c,product_details_master d where a.id=b.mst_id and a.id=c.batch_id and b.prod_id=d.id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.po_id in ($sales_ids) group by b.po_id, a.color_id,b.prod_id,d.detarmination_id";
		$resultDye = sql_select($sql_dye);
		foreach ($resultDye as $dyeRow) 
		{
			$dye_qnty_arr[$dyeRow[csf('po_id')]] += $dyeRow[csf('dye_qnty')];
		}
		unset($resultDye);

		// FINISH PRODUCTION
		$finish_sql = sql_select("SELECT c.po_breakdown_id,b.fabric_description_id,b.color_id,b.prod_id,sum(c.quantity ) fin_receive_qnty from inv_receive_master a,pro_finish_fabric_rcv_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form in(7,66) and c.po_breakdown_id in($sales_ids) group by c.po_breakdown_id,b.fabric_description_id,b.color_id,b.prod_id");

		foreach ($finish_sql as $row) 
		{
			$finish_arr[$row[csf('po_breakdown_id')]]['prod_id'] = $row[csf("prod_id")];
			$finish_arr[$row[csf('po_breakdown_id')]]['fin_receive_qnty'] += $row[csf("fin_receive_qnty")];
		}

		// TRANSFER IN ARRAY
		$transfer_in_sql = sql_select("SELECT a.transfer_system_id ,a.company_id,a.to_order_id,b.from_prod_id,c.product_name_details,c.detarmination_id,sum(b.transfer_qnty) transfer_qnty from inv_item_transfer_mst a,inv_item_transfer_dtls b,product_details_master c where a.id=b.mst_id and b.from_prod_id=c.id and a.status_active=1 and b.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and a.to_order_id in($sales_ids) group by a.transfer_system_id ,a.company_id,a.to_order_id,b.from_prod_id,c.product_name_details,c.detarmination_id");
		$transfer_arr=array();
		foreach ($transfer_in_sql as $transfer_row) 
		{
			$transfer_arr[$transfer_row[csf('to_order_id')]][] = array(
				'fabric_desc' => $transfer_row[csf("product_name_details")],
				'detarmination_id' => $transfer_row[csf("detarmination_id")],
				'transfer_qnty' => $transfer_row[csf('transfer_qnty')]
			);
		} // need to check

		$fin_rcv_trans_iss_sql = sql_select("SELECT c.po_breakdown_id,d.detarmination_id,d.color,c.prod_id,sum(c.quantity ) quantity ,c.entry_form from order_wise_pro_details c, product_details_master d where c.prod_id=d.id and c.entry_form in(224,318,225,317) and c.is_sales=1 $fin_rcv_trans_iss_fso_Cond group by c.po_breakdown_id,d.detarmination_id,d.color,c.prod_id,c.entry_form");
		foreach ($fin_rcv_trans_iss_sql as $row)
		{
			if($row[csf('entry_form')] == 225 || $row[csf('entry_form')] == 317)
			{
				$finish_arr[$row[csf('po_breakdown_id')]]["fin_tex_rcv"] += $row[csf('quantity')];
			}
			else if($row[csf('entry_form')] == 224 || $row[csf('entry_form')] == 318)
			{
				$finish_arr[$row[csf('po_breakdown_id')]]["fin_deli_to_garments"] += $row[csf('quantity')];
			}
		}		
	}

	ob_start();

	?>
	<style type="text/css">
		.alignment_css
		{
			word-break: break-all;
			word-wrap: break-word;
		}
	</style>

	<fieldset width="3340">
	<table cellpadding="5" cellspacing="0" width="3320">
		<tr>
			<td width="100%"  colspan="<? echo $colspan + 21; ?>" style="font-size:16px; text-align: left !important;">
				<strong><?php echo $company_library[$company_name]; ?></strong></td>
		</tr>
		<tr>
			<td align="center" width="100%" colspan="<? echo $colspan + 21; ?>" style="font-size:16px; text-align: left !important;">
					<strong><? if ($start_date != "" && $end_date != "") echo "From " . change_date_format($start_date) . " To " . change_date_format($end_date); ?></strong>
			</td>
		</tr>
	</table>

	<table class="rpt_table" border="1" rules="all" width="3320" cellpadding="1" cellspacing="0" id="tbl_list_search" align="left">
		<thead>
			<tr>
				<th class="alignment_css" width="40">SL</th>
				<th class="alignment_css" width="100">Team Name</th>
				<th class="alignment_css" width="100">Team Leader</th>
				<th class="alignment_css" width="100">Dealing Merchant </th>
				<th class="alignment_css" width="100">Gmt. Merchant </th>
				<th class="alignment_css" width="50">Year</th>
				<th class="alignment_css" width="80">Cust. Buyer</th>
				<th class="alignment_css" width="80">Customer</th>
				<th class="alignment_css" width="100">Fabric Booking No</th>
				<th class="alignment_css" width="130">Sales Order Number</th>
				<th class="alignment_css" width="100">Style Ref.</th>
				<th class="alignment_css" width="80">Within Group</th>
				<th class="alignment_css" width="80">Delivery Start Date</th>
				<th class="alignment_css" width="80">Delivery End Date</th>

				<th class="alignment_css" width="100">Confirm Finish Qty</th>
				<th class="alignment_css" width="100">Total Yarn Required</th>
				<th class="alignment_css" width="100">Total Yarn Allocate</th>
				<th class="alignment_css" width="100">Total Yarn Yet To Allocate</th>
				<th class="alignment_css" width="100">Total Yarn Issued To Knitting</th>
				<th class="alignment_css" width="100">Total Yarn Balance</th>
				<th class="alignment_css" width="100">Total Grey Fabric Required</th>
				<th class="alignment_css" width="100">Total Grey Fabric Production</th>
				<th class="alignment_css" width="100">Total Grey Fabric Available</th>
				<th class="alignment_css" width="100">Total Grey Fabric Issued To Dye</th>
				<th class="alignment_css" width="100">Total Grey Fabric Issue Balance</th>
				<th class="alignment_css" width="100">Total Batch Qnty</th>
				<th class="alignment_css" width="100">Total Batch Balance To Grey Required</th>
				<th class="alignment_css" width="100">Total Dye Qnty</th>
				<th class="alignment_css" width="100">Total Dye Balance To Grey Required</th>
				<th class="alignment_css" width="100">Total Finish Fabric Required</th>
				<th class="alignment_css" width="100">Total Finish Fabric Receive</th>
				<th class="alignment_css" width="100">Total Finish Fabric Balance</th>
				<th class="alignment_css" width="100">Total Finish Fabric Delivery to  Garments</th>
				<th class="alignment_css" width="100">Total Finish Fabric Delivery Balance</th>
				<th class="alignment_css" width="">Less<br>Delivery %</th>
			</tr>
		</thead>
	</table>

	<div style="width:3340px; max-height:240px; overflow-y:scroll"  align="left" id="scroll_body">
		<table class="rpt_table" border="1" rules="all" width="3320" cellpadding="1" cellspacing="0" id="tbl_list_search" align="left">
			<tbody>
				<?
				$i=1;
				// echo "<pre>";print_r($salesOrderDataResult);
				foreach ($salesOrderDataResult as $row)
				{
					$sales_id=$row[csf('id')];
					$within_group = ($row[csf('within_group')] == 1) ? "Yes" : "No";
					$sales_order = "<a href='##' style='color:#000' onclick=\"fnc_fabric_sales_order_print('" . $company_name . "','" . $row[csf('booking_id')] . "','" . $row[csf('booking_no')] . "','" . $row[csf('sales_job_no')] . "','" . $row[csf('mst_entry_form')] . "','1')\"><font style='font-weight:bold' $wo_color>" . $row[csf('sales_job_no')] . "</font></a>";
					$po_ids = rtrim($row[csf('po_break_down_id')], ',');

					$confirm_finish_qty=$confirm_finish_qty_arr[$sales_id];
					$yarn_issued_to_knitting = ($yarn_issue_details_arr[$sales_id]['issue_qnty'] > 0)?$yarn_issue_details_arr[$sales_id]['issue_qnty']:"";
					$yarn_allocate=$yarn_allocate_arr[$sales_id]['yarn_allocate'];
					
					$yarn_balance=$yarn_allocate-$yarn_issued_to_knitting;
					
					$yarn_required = ($yarn_details_arr[$sales_id] > 0) ? $yarn_details_arr[$sales_id] : "";
					$yarn_yet_to_allocate=$yarn_required-$yarn_allocate;
					$grey_fabric_status_details = $grey_fabric_status_arr[$sales_id];
					$rowspan=$grey_required=0;
					$fabric_rowspan=array();
					foreach ($grey_fabric_status_details as $grey_fabric_row) 
					{
						$grey_required += $grey_fabric_row["grey_qty"];
						$rowspan++;
					}
					$knitting_production = ($knitting_arr[$sales_id]['grey_receive_qnty'] > 0) ? $knitting_arr[$sales_id]['grey_receive_qnty'] : "";
					$net_transfer = ($trans_qnty_arr[$sales_id]['transfer_out'] != 0) ? $trans_qnty_arr[$sales_id]['transfer_out'] : "";
					$grey_available = (($knitting_production + $net_transfer) > 0) ? ($knitting_production + $net_transfer) : "";
					$issue_qnty = ($grey_issue_qnty_arr[$sales_id] > 0) ? number_format($grey_issue_qnty_arr[$sales_id], 2, '.', '') : "";
					$grey_fabric_issue_balance=$grey_required-$issue_qnty;
					$batch_qnty = ($batch_arr[$sales_id] > 0) ? number_format($batch_arr[$sales_id], 2, '.', '') : "";
					$batch_balance_to_grey_required=$grey_required-$batch_qnty;
					$dye_qnty = $dye_qnty_arr[$sales_id];
					$dye_balance_to_grey_required=$grey_required-$dye_qnty;
					$req_fin_qnty = $sales_fin_qnty_arr[$sales_id];
					$finish_fabric_receive_qty = $finish_arr[$sales_id]['fin_receive_qnty'];
					$finish_fabric_balance=$req_fin_qnty-$finish_fabric_receive_qty;
					$fin_deli_to_garments=$finish_arr[$sales_id]["fin_deli_to_garments"];
					$finish_fabric_delivery_balance = $req_fin_qnty-$fin_deli_to_garments;
					$less_delivery = $finish_fabric_delivery_balance/$req_fin_qnty*100;
					

					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
					<tr bgcolor='<? echo $bgcolor;?>' onClick="change_color('tr_<? echo $i; ?>','#FFFFFF')" id="tr_<? echo $i;?>">
						<td class="alignment_css" width="40"><? echo $i?></td>
						<td class="alignment_css" width="100"><? echo $team_name_arr[$row[csf('team_leader')]];?></td>
						<td class="alignment_css" width="100"><? echo $team_leader_name_arr[$row[csf('team_leader')]];?></td>
						<td class="alignment_css" width="100"><? echo $dealing_marchant_array[$row[csf('dealing_marchant')]];?></td>
						<td class="alignment_css" width="100"><? echo $row[csf('garments_marchant')];?></td>
						<td class="alignment_css" width="50"><? echo date("Y", strtotime($row[csf('sales_order_dt')]));?></td>
						<td class="alignment_css" width="80"><? echo $buyer_name_array[$row[csf('customer_buyer')]];?></td>
						<td class="alignment_css" width="80"><? echo $buyer_name_array[$row[csf('sales_buyer')]];?></td>
						<td class="alignment_css" width="100"><? echo $row[csf('booking_no')];?></td>
						<td class="alignment_css" width="130" title="<? echo $sales_id; ?>"><? echo $sales_order;?></td>
						<td class="alignment_css" width="100"><? echo $row[csf('style_ref_no')] ?> </td>
						<td class="alignment_css" width="80"><? echo $within_group;?></td>
						<td class="alignment_css" width="80"><? echo date("d-m-Y", strtotime($row[csf('delivery_start_date')])); ?></td>
						<td class="alignment_css" width="80"><? echo date("d-m-Y", strtotime($row[csf('sales_order_dt')]));?></td>

						<td class="alignment_css" width="100" align="right"><? echo number_format($confirm_finish_qty,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($yarn_required,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($yarn_allocate,2,".","");?></td>
						<td class="alignment_css" width='100' align="right"><? echo number_format($yarn_yet_to_allocate,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($yarn_issued_to_knitting,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($yarn_balance,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($grey_required,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($knitting_production,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($grey_available,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($issue_qnty,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($grey_fabric_issue_balance,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($batch_qnty,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($batch_balance_to_grey_required,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($dye_qnty,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($dye_balance_to_grey_required,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($req_fin_qnty,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($finish_fabric_receive_qty,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($finish_fabric_balance,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($fin_deli_to_garments,2,".","");?></td>
						<td class="alignment_css" width="100" align="right"><? echo number_format($finish_fabric_delivery_balance,2,".","");?></td>
						<td class="alignment_css" width="" align="right"><? echo number_format($less_delivery,2,".","");?></td>
					</tr>
					<?
					$i++;
					$total_confirm_finish_qty += $confirm_finish_qty;
					$total_yarn_required += $yarn_required;
					$total_yarn_allocate += $yarn_allocate;
					$total_yarn_yet_to_allocate += $yarn_yet_to_allocate;
					$total_yarn_issued_to_knitting += $yarn_issued_to_knitting;
					$total_yarn_balance += $yarn_balance;
					$total_grey_fabric_required += $grey_required;
					$total_grey_production_qnty +=$knitting_production;
					$total_grey_fabric_available +=$grey_available;
					$total_grey_issue_qnty += $issue_qnty;
					$total_grey_fabric_issue_balance += $grey_fabric_issue_balance;
					$total_batch_qnty += $batch_qnty;
					$total_batch_balance_to_grey_required += $batch_balance_to_grey_required;
					$total_dye_qnty += $dye_qnty;
					$total_dye_balance_to_grey_required += $dye_balance_to_grey_required;
					$total_req_fin_qnty += $req_fin_qnty;
					$total_finish_fabric_receive_qty += $finish_fabric_receive_qty;
					$total_finish_fabric_balance += $finish_fabric_balance;
					$total_fin_deli_to_garments += $fin_deli_to_garments;
					$total_finish_fabric_delivery_balance += $finish_fabric_delivery_balance;
				}
				?>
			</tbody>
		</table>
	</div>
	<table class="rpt_table" border="1" rules="all" width="3320" cellpadding="1" cellspacing="0" align="left">
		<tfoot>
			<tr>
				<th class="alignment_css" width="40">&nbsp;</th>
				<th class="alignment_css" width="100">&nbsp;</th>
				<th class="alignment_css" width="100">&nbsp;</th>
				<th class="alignment_css" width="100">&nbsp;</th>
				<th class="alignment_css" width="100">&nbsp;</th>
				<th class="alignment_css" width="50">&nbsp;</th>
				<th class="alignment_css" width="80">&nbsp;</th>
				<th class="alignment_css" width="80">&nbsp;</th>
				<th class="alignment_css" width="100">&nbsp;</th>
				<th class="alignment_css" width="130">&nbsp;</th>
				<th class="alignment_css" width="100">&nbsp;</th>
				<th class="alignment_css" width="80">&nbsp;</th>
				<th class="alignment_css" width="80">&nbsp;</th>
				<th class="alignment_css" width="80">&nbsp;</th>

				<th class="alignment_css" width="100"><? echo number_format($total_confirm_finish_qty,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_yarn_required,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_yarn_allocate,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_yarn_yet_to_allocate,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_yarn_issued_to_knitting,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_yarn_balance,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_grey_fabric_required,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_grey_production_qnty,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_grey_fabric_available,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_grey_issue_qnty,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_grey_fabric_issue_balance,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_batch_qnty,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_batch_balance_to_grey_required,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_dye_qnty,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_dye_balance_to_grey_required,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_req_fin_qnty,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_finish_fabric_receive_qty,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_finish_fabric_balance,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_fin_deli_to_garments,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_finish_fabric_delivery_balance,2);?></th>
				<th class="alignment_css" width="">&nbsp;</th>
			</tr>
		</tfoot>
	</table>
	</fieldset>
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("$user_name*.xls") as $filename) {
		@unlink($filename);

	}
    //---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename";
} // Summary End

//========================================
if ($action == "yarn_requisition_popup") {
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	page_style();
	extract($_REQUEST);
	$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$order_id = explode("****", $order_id);
	?>
	<script>
		function print_window() {
			//document.getElementById('scroll_body').style.overflow = "auto";
			//document.getElementById('scroll_body').style.maxHeight = "none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
			d.close();
			//document.getElementById('scroll_body').style.overflowY = "scroll";
			//document.getElementById('scroll_body').style.maxHeight = "230px";
		}
	</script>
	<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
		style="width:100px" class="formbutton"/></div>
		<fieldset style="width:865px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="11"><b>Yarn Requisition</b></th>
					</thead>
					<thead>
						<th width="105">SL</th>
						<th width="105">Booking No</th>
						<th width="80">Requisition No</th>
						<th width="75">Requisition Date</th>
						<th width="70">Brand</th>
						<th width="200">Yarn Description</th>
						<th width="60">Lot No</th>
						<th width="80">Yarn Type</th>
						<th width="90">Requisition Qnty</th>
					</thead>
					<?
					$i = 1;
					$total_yarn_issue_qnty = 0;
					$total_yarn_issue_qnty_out = 0;
					if($order_id[1] == 1)
					{
						$sql = "select a.dtls_id,a.booking_no, a.determination_id,b.knit_id,b.requisition_no,b.requisition_date,b.yarn_qnty,a.po_id,b.id,b.prod_id,c.product_name_details,yarn_type,c.lot,c.brand from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join product_details_master c on b.prod_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.requisition_no in($order_id[0]) and a.determination_id=$yarn_count";
					}
					else
					{
						$sql = "select a.dtls_id,a.booking_no, a.determination_id,b.knit_id,b.requisition_no,b.requisition_date,b.yarn_qnty,a.po_id,b.id,b.prod_id,c.product_name_details,yarn_type,c.lot,c.brand from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join product_details_master c on b.prod_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.requisition_no in($order_id[0]) and c.yarn_type=$yarn_type_id and c.yarn_comp_percent1st = $yarn_comp_percent1st and c.yarn_comp_type1st = $yarn_comp_type1st and c.yarn_count_id=$yarn_count";
					}

					$result = sql_select($sql);
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$issue_to = "";
						if ($row[csf('knit_dye_source')] == 1) {
							$issue_to = $company_library[$row[csf('knit_dye_company')]];
						} else {
							$issue_to = $supplier_details[$row[csf('knit_dye_company')]];
						}

						$yarn_issued = $row[csf('issue_qnty')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
							<td width="105" class="center"><? echo $i; ?></td>
							<td width="105" class="center"><? echo $row[csf('booking_no')]; ?></td>
							<td width="80" class="center"><? echo $row[csf('requisition_no')]; ?></td>
							<td width="75" class="center"><? echo change_date_format($row[csf('requisition_date')]); ?></td>
							<td width="70" class="center"><? echo $brand_array[$row[csf('brand')]]; ?></td>
							<td width="60" class="center"><? echo $row[csf('product_name_details')]; ?></td>
							<td width="60" class="center"><? echo $row[csf('lot')]; ?></td>
							<td width="80" class="center"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
							<td align="right" width="90"><? echo $row[csf('yarn_qnty')]; ?></td>
						</tr>
						<?
						$total_req_qnty += $row[csf('yarn_qnty')];
						$i++;
					}
					?>
					<tr style="font-weight:bold">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right">Total</td>
						<td align="right"><? echo number_format($total_req_qnty, 2); ?></td>
					</tr>
				</table>
			</div>
		</fieldset>
		<?
		exit();
	}

	if ($action == "yarn_issue_popup") {
		echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
		page_style();
		extract($_REQUEST);
		$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		//$order_id = explode('_', $order_id);

		$order_id_requ = explode('****', $order_id);
		$order_id = explode('_', $order_id_requ[0]);
		?>
		<script>
			function print_window() {
				document.getElementById('scroll_body').style.overflow = "auto";
				document.getElementById('scroll_body').style.maxHeight = "none";
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
					'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
				d.close();
				document.getElementById('scroll_body').style.overflowY = "scroll";
				document.getElementById('scroll_body').style.maxHeight = "230px";
			}
		</script>
		<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
			style="width:100px" class="formbutton"/></div>
			<fieldset style="width:865px; margin-left:3px">
				<div id="report_container">

					<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
						<thead>
							<th colspan="11"><b>Yarn Issue</b></th>
						</thead>
						<thead>
							<th width="105">Issue Id</th>
							<th width="90">Issue To</th>
							<th width="105">Booking No</th>
							<th width="80">Challan No</th>
							<th width="70">Brand</th>
							<th width="200">Yarn Description</th>
							<th width="60">Lot No</th>
							<th width="75">Issue Date</th>
							<th width="80">Yarn Type</th>
							<th width="90">Issue Qnty (In)</th>
							<th>Issue Qnty (Out)</th>
						</thead>
						<?
						$i = 1;
						$total_yarn_issue_qnty = 0;
						$total_yarn_issue_qnty_out = 0;
						$sql = "select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, e.sales_booking_no booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d,fabric_sales_order_mst e where a.id=d.mst_id and b.po_breakdown_id=e.id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and a.id in ($order_id[0]) and d.requisition_no in($order_id[1]) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, e.sales_booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
						$result = sql_select($sql);
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$issue_to = "";
							if ($row[csf('knit_dye_source')] == 1) {
								$issue_to = $company_library[$row[csf('knit_dye_company')]];
							} else {
								$issue_to = $supplier_details[$row[csf('knit_dye_company')]];
							}

							$yarn_issued = $row[csf('issue_qnty')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"
								onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="105" class="center"><? echo $row[csf('issue_number')]; ?></td>
								<td width="90" class="center"><? echo $issue_to; ?></td>
								<td width="105" class="center"><? echo $row[csf('booking_no')]; ?></td>
								<td width="80" class="center"><? echo $row[csf('challan_no')]; ?></td>
								<td width="70" class="center"><? echo $brand_array[$row[csf('brand_id')]]; ?></td>
								<td width="60" class="center"><? echo $row[csf('product_name_details')]; ?></td>
								<td width="60" class="center"><? echo $row[csf('lot')]; ?></td>
								<td width="75" class="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
								<td width="80" class="center"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
								<td align="right" width="90">
									<?
									if ($row[csf('knit_dye_source')] != 3) {
										echo number_format($yarn_issued, 2);
										$total_yarn_issue_qnty += $yarn_issued;
									} else echo "&nbsp;";
									?>
								</td>
								<td align="right">
									<?
									if ($row[csf('knit_dye_source')] == 3) {
										echo number_format($yarn_issued, 2);
										$total_yarn_issue_qnty_out += $yarn_issued;
									} else echo "&nbsp;";
									?>
								</td>
							</tr>
							<?
							$i++;
						}
						?>
						<tr style="font-weight:bold">
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right">Total</td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty, 2); ?></td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty_out, 2); ?></td>
						</tr>
						<tr style="font-weight:bold">
							<td align="right" colspan="10">Issue Total</td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty + $total_yarn_issue_qnty_out, 2); ?></td>
						</tr>
						<thead>
							<th colspan="11"><b>Yarn Return</b></th>
						</thead>
						<thead>
							<th width="105">Return Id</th>
							<th width="90">Return From</th>
							<th width="105">Booking No</th>
							<th width="80">Challan No</th>
							<th width="70">Brand</th>
							<th width="200">Yarn Description</th>
							<th width="60">Lot No</th>
							<th width="75">Return Date</th>
							<th width="80">Yarn Type</th>
							<th width="90">Return Qnty (In)</th>
							<th>Return Qnty (Out)</th>
						</thead>
						<?
						$total_yarn_return_qnty = 0;
						$total_yarn_return_qnty_out = 0;
					//$issue_ids = return_field_value("listagg(mst_id ,',') within group (order by mst_id) as mst_id","inv_transaction", "requisition_no=$order_id[1]","mst_id");

						$sql = "select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.product_name_details,c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d,fabric_sales_order_mst e where a.id=d.mst_id and b.po_breakdown_id=e.id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.prod_id=c.id and d.issue_id in($order_id[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot,c.product_name_details, c.yarn_type, c.product_name_details, d.brand_id";
						$result = sql_select($sql);
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$return_from = "";
							if ($row[csf('knitting_source')] == 1) {
								$return_from = $company_library[$row[csf('knitting_company')]];
							} else {
								$return_from = $supplier_details[$row[csf('knitting_company')]];
							}

							$yarn_returned = $row[csf('returned_qnty')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"
								onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="90"><p><? echo $return_from; ?></p></td>
								<td width="105"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
								<td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?></p></td>
								<td width="60"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
								<td align="right" width="90">
									<?
									if ($row[csf('knitting_source')] != 3) {
										echo number_format($yarn_returned, 2);
										$total_yarn_return_qnty += $yarn_returned;
									} else echo "&nbsp;";
									?>
								</td>
								<td align="right">
									<?
									if ($row[csf('knitting_source')] == 3) {
										echo number_format($yarn_returned, 2);
										$total_yarn_return_qnty_out += $yarn_returned;
									} else echo "&nbsp;";
									?>
								</td>
							</tr>
							<?
							$i++;
						}
						?>
						<tr style="font-weight:bold">
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right">Balance</td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty - $total_yarn_return_qnty, 2); ?></td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty_out - $total_yarn_return_qnty_out, 2); ?></td>
						</tr>
						<tfoot>
							<tr>
								<th align="right" colspan="10">Total Balance</th>
								<th align="right"><? echo number_format(($total_yarn_issue_qnty + $total_yarn_issue_qnty_out) - ($total_yarn_return_qnty + $total_yarn_return_qnty_out), 2); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
			<?
			exit();
		}


		if ($action == "yarn_issue_popup_for_report_2") {
			echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
			page_style();
			extract($_REQUEST);
			$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
			$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
			$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

			?>
			<script>
				function print_window() {
				//document.getElementById('scroll_body').style.overflow = "auto";
				//document.getElementById('scroll_body').style.maxHeight = "none";
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
					'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
				d.close();
				//document.getElementById('scroll_body').style.overflowY = "scroll";
				//document.getElementById('scroll_body').style.maxHeight = "230px";
			}
		</script>
		<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
			style="width:100px" class="formbutton"/></div>
			<fieldset style="width:865px; margin-left:3px">
				<div id="report_container">

					<table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
						<thead>
							<th colspan="12"><b>Yarn Issue</b></th>
						</thead>
						<thead>
							<th width="105">Issue Id</th>
							<th width="90">Issue To</th>
							<th width="105">Booking No</th>
							<th width="80">Challan No</th>
							<th width="70">Brand</th>
							<th width="200">Yarn Description</th>
							<th width="60">Lot No</th>
							<th width="75">Issue Date</th>
							<th width="80">Yarn Type</th>
							<th width="90">Issue Qnty (In)</th>
							<th width="90">Issue Qnty (Out)</th>
							<th >Returnable Qnty</th>
						</thead>
						<?
						$i = 1;
						$total_yarn_issue_qnty = 0;
						$total_yarn_issue_qnty_out = 0;

						$sql = " select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, e.sales_booking_no booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id, sum(b.returnable_qnty) as returnable_qnty from inv_issue_master a,order_wise_pro_details b, product_details_master c, inv_transaction d,fabric_sales_order_mst e where a.id=d.mst_id and b.po_breakdown_id=e.id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 and b.po_breakdown_id = $order_id and c.yarn_count_id = $yarn_count and c.yarn_type = $yarn_type_id and c.yarn_comp_percent1st= $yarn_comp_percent1st and c.yarn_comp_type1st = $yarn_comp_type1st group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, e.sales_booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
						$result = sql_select($sql);
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$issue_to = "";
							if ($row[csf('knit_dye_source')] == 1) {
								$issue_to = $company_library[$row[csf('knit_dye_company')]];
							} else {
								$issue_to = $supplier_details[$row[csf('knit_dye_company')]];
							}

							$yarn_issued = $row[csf('issue_qnty')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"
								onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="105" class="center"><? echo $row[csf('issue_number')]; ?></td>
								<td width="90" class="center"><? echo $issue_to; ?></td>
								<td width="105" class="center"><? echo $row[csf('booking_no')]; ?></td>
								<td width="80" class="center"><? echo $row[csf('challan_no')]; ?></td>
								<td width="70" class="center"><? echo $brand_array[$row[csf('brand_id')]]; ?></td>
								<td width="60" class="center"><? echo $row[csf('product_name_details')]; ?></td>
								<td width="60" class="center"><? echo $row[csf('lot')]; ?></td>
								<td width="75" class="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
								<td width="80" class="center"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>

								<td align="right" width="90">
									<?
									if ($row[csf('knit_dye_source')] != 3) {
										echo number_format($yarn_issued, 2);
										$total_yarn_issue_qnty += $yarn_issued;
									} else echo "&nbsp;";
									?>
								</td>
								<td align="right" width="90">
									<?
									if ($row[csf('knit_dye_source')] == 3) {
										echo number_format($yarn_issued, 2);
										$total_yarn_issue_qnty_out += $yarn_issued;
									} else echo "&nbsp;";
									?>
								</td>
								<td  class="center"><? echo $row[csf('returnable_qnty')]; $total_returnable_qnty += $row[csf('returnable_qnty')]; ?></td>
							</tr>
							<?
							$i++;
						}
						?>
						<tr style="font-weight:bold">
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right">Total</td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty, 2); ?></td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty_out, 2); ?></td>
							<td align="right"><? echo number_format($total_returnable_qnty, 2); ?></td>
						</tr>
						<tr style="font-weight:bold">
							<td align="right" colspan="9">Issue Total</td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty + $total_yarn_issue_qnty_out, 2); ?></td>
						</tr>
						<thead>
							<th colspan="12"><b>Yarn Return</b></th>
						</thead>
						<thead>
							<th width="105">Return Id</th>
							<th width="90">Return From</th>
							<th width="105">Booking No</th>
							<th width="80">Challan No</th>
							<th width="70">Brand</th>
							<th width="200">Yarn Description</th>
							<th width="60">Lot No</th>
							<th width="75">Return Date</th>
							<th width="80">Yarn Type</th>
							<th width="90">Return Qnty (In)</th>
							<th>Return Qnty (Out)</th>
						</thead>
						<?
						$total_yarn_return_qnty = 0;
						$total_yarn_return_qnty_out = 0;

						$sql = "select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.product_name_details,c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id , e.id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d,fabric_sales_order_mst e where a.id=d.mst_id and b.po_breakdown_id=e.id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.prod_id=c.id and c.yarn_count_id=$yarn_count and c.yarn_comp_type1st =$yarn_comp_type1st and c.yarn_type = $yarn_type_id and c.yarn_comp_percent1st = $yarn_comp_percent1st and e.id = $order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot,c.product_name_details, c.yarn_type, c.product_name_details, d.brand_id, e.id";
						$result = sql_select($sql);
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$return_from = "";
							if ($row[csf('knitting_source')] == 1) {
								$return_from = $company_library[$row[csf('knitting_company')]];
							} else {
								$return_from = $supplier_details[$row[csf('knitting_company')]];
							}

							$yarn_returned = $row[csf('returned_qnty')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"
								onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="90"><p><? echo $return_from; ?></p></td>
								<td width="105"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
								<td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?></p></td>
								<td width="60"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
								<td align="right" width="90">
									<?
									if ($row[csf('knitting_source')] != 3) {
										echo number_format($yarn_returned, 2);
										$total_yarn_return_qnty += $yarn_returned;
									} else echo "&nbsp;";
									?>
								</td>
								<td align="right">
									<?
									if ($row[csf('knitting_source')] == 3) {
										echo number_format($yarn_returned, 2);
										$total_yarn_return_qnty_out += $yarn_returned;
									} else echo "&nbsp;";
									?>
								</td>
							</tr>
							<?
							$i++;
						}
						?>
						<tr style="font-weight:bold">
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right">Total</td>
							<td align="right"><? echo number_format($total_yarn_return_qnty, 2); ?></td>
							<td align="right"><? echo number_format($total_yarn_return_qnty_out, 2); ?></td>
						</tr>
						<tfoot>
							<tr>
								<th align="right" colspan="10">Total Balance</th>
								<th align="right"><? echo number_format(($total_yarn_issue_qnty + $total_yarn_issue_qnty_out) - ($total_yarn_return_qnty + $total_yarn_return_qnty_out), 2); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
			<?
			exit();
		}

		if ($action == "yarn_trans_popup") {
			echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
			page_style();
			extract($_REQUEST);
			$po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");

			$determination_id= $deter_id;
			/*$deter_pop_arr=array();
			$deter_pop_arr = explode("_",$deter_id);
			//print_r($deter_pop_arr);
			$determination_id = $deter_pop_arr[0];
			$popup_type = $deter_pop_arr[1];*/
			?>
			<script>

				function print_window() {
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
						'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

					d.close();
				}

			</script>
			<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
				style="width:100px" class="formbutton"/></div>
				<fieldset style="width:700px; margin:auto;">
					<div id="report_container">
						<table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0">
							<? 
							//if ($popup_type ==1)
							//{?>
								<thead>
									<tr>
										<th colspan="6">Transfer In</th>
									</tr>
									<tr>
										<th width="40">SL</th>
										<th width="115">Transfer Id</th>
										<th width="80">Transfer Date</th>
										<th width="100">From Order</th>
										<th width="170">Item Description</th>
										<th>Transfer Qnty</th>
									</tr>
								</thead>
								<?
								$i = 1;
								$total_trans_in_qnty = 0;
								if($determination_id != "") $deter_id_cond = " and d.detarmination_id=$determination_id"; else $deter_id_cond = "";
								$sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=133 and c.po_breakdown_id in ($order_id) $deter_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
								$result = sql_select($sql);
								foreach ($result as $row) {
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>"
										onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
										<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
										<td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
										<td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
										<td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
									</tr>
									<?
									$total_trans_in_qnty += $row[csf('transfer_qnty')];
									$i++;
								}
								?>
								<tr style="font-weight:bold">
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right">Total</td>
									<td align="right"><? echo number_format($total_trans_in_qnty, 2); ?></td>
								</tr>
							<?
							/*}
							else
							{*/
								?>
								<thead>
									<tr>
										<th colspan="6">Transfer Out</th>
									</tr>
									<tr>
										<th width="40">SL</th>
										<th width="115">Transfer Id</th>
										<th width="80">Transfer Date</th>
										<th width="100">To Order</th>
										<th width="170">Item Description</th>
										<th>Transfer Qnty</th>
									</tr>
								</thead>
								<?
								$total_trans_out_qnty = 0;
								if($determination_id != "") $deter_id_cond = " and d.detarmination_id=$determination_id"; else $deter_id_cond = "";
								$sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=133 and c.po_breakdown_id in ($order_id) $deter_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
								$result = sql_select($sql);
								foreach ($result as $row) {
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>"
										onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
										<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
										<td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
										<td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
										<td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
									</tr>
									<?
									$total_trans_out_qnty += $row[csf('transfer_qnty')];
									$i++;
								}
								?>
								<tr style="font-weight:bold">
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right">Total</td>
									<td align="right"><? echo number_format($total_trans_out_qnty, 2); ?></td>
								</tr>
								<?
							//}
							?>
							<!-- <tfoot>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th>Net Transfer</th>
								<th><? echo number_format($total_trans_in_qnty - $total_trans_out_qnty, 2); ?></th>
							</tfoot> -->
						</table>
					</div>
				</fieldset>
				<?
				exit();
			}

			if ($action == "yarn_trans_in_popup") {
				echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
				page_style();
				extract($_REQUEST);
				$po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");

				$deter_gsm_dia_arr = explode("_",$deter_id);
				$determination_id = $deter_gsm_dia_arr[0];
				$gsm = $deter_gsm_dia_arr[1];
				$dia_width = $deter_gsm_dia_arr[2];
				?>
				<script>

					function print_window() {
						var w = window.open("Surprise", "#");
						var d = w.document.open();
						d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
							'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

						d.close();
					}

				</script>
				<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
					style="width:100px" class="formbutton"/></div>
					<fieldset style="width:700px; margin:auto;">
						<div id="report_container">
							<table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0">
								<thead>
									<tr>
										<th colspan="6">Transfer In</th>
									</tr>
									<tr>
										<th width="40">SL</th>
										<th width="115">Transfer Id</th>
										<th width="80">Transfer Date</th>
										<th width="100">From Order</th>
										<th width="170">Item Description</th>
										<th>Transfer Qnty</th>
									</tr>
								</thead>
								<?
								$i = 1;
								$total_trans_in_qnty = 0;
								if($determination_id != "") $deter_id_cond = " and d.detarmination_id=$determination_id"; else $deter_id_cond = "";
								if($gsm != "") $gsm_cond = " and d.gsm=$gsm"; else $gsm_cond = "";
								if($dia_width != "") $dia_width_cond = " and d.dia_width=$dia_width"; else $dia_width_cond = "";
								$sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form in (133,362) and c.po_breakdown_id in ($order_id) $deter_id_cond $gsm_cond $dia_width_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
								$result = sql_select($sql);
								foreach ($result as $row) {
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>"
										onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
										<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
										<td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
										<td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
										<td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
									</tr>
									<?
									$total_trans_in_qnty += $row[csf('transfer_qnty')];
									$i++;
								}
								?>

								<tfoot>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th>Total</th>
									<th><? echo number_format($total_trans_in_qnty, 2); ?></th>
								</tfoot>
							</table>
						</div>
					</fieldset>
					<?
					exit();
				}

				if ($action == "yarn_trans_out_popup")
				{
					echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
					page_style();
					extract($_REQUEST);
					$po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");

					$deter_gsm_dia_arr = explode("_",$deter_id);
					$determination_id = $deter_gsm_dia_arr[0];
					$gsm = $deter_gsm_dia_arr[1];
					$dia_width = $deter_gsm_dia_arr[2];
					?>
					<script>

						function print_window() {
							var w = window.open("Surprise", "#");
							var d = w.document.open();
							d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
								'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

							d.close();
						}

					</script>
					<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
						style="width:100px" class="formbutton"/></div>
						<fieldset style="width:700px; margin:auto;">
							<div id="report_container">
								<table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0">
									<thead>
										<tr>
											<th colspan="6">Transfer Out</th>
										</tr>
										<tr>
											<th width="40">SL</th>
											<th width="115">Transfer Id</th>
											<th width="80">Transfer Date</th>
											<th width="100">To Order</th>
											<th width="170">Item Description</th>
											<th>Transfer Qnty</th>
										</tr>
									</thead>
									<?
									$total_trans_out_qnty = 0;
									if($determination_id != "") $deter_id_cond = " and d.detarmination_id=$determination_id"; else $deter_id_cond = "";
									if($gsm != "") $gsm_cond = " and d.gsm=$gsm"; else $gsm_cond = "";
									if($dia_width != "") $dia_width_cond = " and d.dia_width=$dia_width"; else $dia_width_cond = "";
									$sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form  in (133,362) and c.po_breakdown_id in ($order_id) $deter_id_cond $gsm_cond $dia_width_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
									$result = sql_select($sql);
									foreach ($result as $row) {
										if ($i % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>"
											onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="40"><? echo $i; ?></td>
											<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
											<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
											<td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
											<td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
											<td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
										</tr>
										<?
										$total_trans_out_qnty += $row[csf('transfer_qnty')];
										$i++;
									}
									?>
									<tfoot>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th>Total</th>
										<th><? echo number_format( $total_trans_out_qnty, 2); ?></th>
									</tfoot>
								</table>
							</div>
						</fieldset>
						<?
						exit();
					}
					if ($action == "grey_required_popup") {

						echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
						page_style();
						extract($_REQUEST);


						$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
						$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
						?>
						<script>

							var tableFilters = {
								col_operation: {
									id: ["td_booking_qty", "td_finish_qty", "td_grey_qty"],
									col: [9, 13, 15],
									operation: ["sum", "sum", "sum"],
									write_method: ["innerHTML", "innerHTML", "innerHTML"]
								}
							}
							$(document).ready(function (e) {
								setFilterGrid('tbl_list_search', -1, tableFilters);
							});

							function print_window() {
								document.getElementById('scroll_body').style.overflow = "auto";
								document.getElementById('scroll_body').style.maxHeight = "none";

								$('.flt').hide();

								var w = window.open("Surprise", "#");
								var d = w.document.open();
								d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
									'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

								d.close();
								document.getElementById('scroll_body').style.overflowY = "scroll";
								document.getElementById('scroll_body').style.maxHeight = "330px";

								$('.flt').show();
							}

						</script>
						<style type="text/css">
						.alignment_css
						{
							word-break: break-all;
							word-wrap: break-word;
						}
					</style>
					<div style="width:1308px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
						style="width:100px" class="formbutton"/></div>
						<fieldset style="width:1308px;">
							<div id="report_container">

								<table border="1" class="rpt_table" rules="all" width="1290" cellpadding="0" cellspacing="0"  align="left">
									<thead>
										<th colspan="17"><b>Grey Required Info</b></th>
									</thead>
									<thead>
										<tr>


											<th class='alignment_css' width="30">SL</th>
											<th class='alignment_css' width="115">Body Part</th>
											<th class='alignment_css' width="95">Color Type</th>
											<th class='alignment_css' width="110">Fabric Description</th>
											<th class='alignment_css' width="60">Fabric GSM</th>
											<th class='alignment_css' width="60">Fabric Dia</th>
											<th class='alignment_css' width="100">Color</th>
											<th class='alignment_css' width="100">Color Range</th>
											<th class='alignment_css' width="60">Con. UOM</th>
											<th class='alignment_css' width="60">Booking Qty</th>
											<th class='alignment_css' width="80">Avg. Price</th>
											<th class='alignment_css' width="80">Amount</th>
											<th class='alignment_css' width="60">UOM</th>
											<th class='alignment_css' width="70">Finish Qty</th>
											<th class='alignment_css'  width="70">Process Loss %.</th>
											<th class='alignment_css' width="70">Grey Qty</th>
											<th class='alignment_css' width="70">Remarks</th>
										</tr>
									</thead>
								</table>
								<div style="width:1308px; max-height:330px; overflow-y:scroll" id="scroll_body">
									<table border="1"  align="left" class="rpt_table" rules="all" width="1290" cellpadding="0" cellspacing="0"
									id="tbl_list_search">
									<tbody>
										<?
										$i = 1;
										$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
										$total_receive_qnty = 0;

										$product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
										if($deter_id != "") $deter_id_cond = " and b.febric_description_id=$deter_id";

										$sql = "SELECT id, mst_id, job_no_mst, body_part_id, color_type_id, determination_id, fabric_desc, gsm_weight, dia, width_dia_type, color_id, color_range_id, finish_qty, avg_rate, amount, process_loss, grey_qty, work_scope, yarn_data, order_uom, pre_cost_remarks, rmg_qty, pre_cost_fabric_cost_dtls_id, item_number_id, grey_qnty_by_uom, cons_uom FROM fabric_sales_order_dtls Where MST_ID = '$order_id' and status_active=1 order by id asc "; //, process_id, process_seq, barcode_year, barcode_suffix_no, barcode_no
										$result = sql_select($sql);

										foreach ($result as $row) {
											if ($i % 2 == 0)
												$bgcolor = "#E9F3FF";
											else
												$bgcolor = "#FFFFFF";

											$total_receive_qnty += $row[csf('quantity')];
											?>
											<tr bgcolor="<? echo $bgcolor; ?>"
												onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td class='alignment_css' width="30"><? echo $i; ?></td>
												<td class='alignment_css' width="115" class="center"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
												<td class='alignment_css' width="95" class="center"><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
												<td class='alignment_css' width="110" class="center"><? echo $row[csf('fabric_desc')]; ?></td>
												<td class='alignment_css' width="60" class="center"><? echo $row[csf('gsm_weight')]; ?></td>
												<td class='alignment_css' width="60" class="center"><? echo $row[csf('dia')]; ?></td>

												<td class='alignment_css' width="100" class="center"><? echo  $color_arr[$row[csf('color_id')]]; ?></td>
												<td class='alignment_css' width="100" class="center"><? echo  $color_range[$row[csf('color_range_id')]]; ?></td>
												<td class='alignment_css' width="60" class="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
												<td class='alignment_css' width="60" class="left"><? echo number_format( $row[csf('grey_qnty_by_uom')],2); ?></td>
												<td class='alignment_css' width="80" class="center"><? echo $row[csf('avg_rate')]; ?></td>
												<td class='alignment_css' width="80" class="left"><? echo number_format($row[csf('amount')],2); ?></td>
												<td class='alignment_css' width="60" class="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
												<td class='alignment_css' width="70" class="left"><? echo number_format($row[csf('finish_qty')],2); ?></td>
												<td class='alignment_css' width="70" class="center"><? echo $row[csf('process_loss')]; ?></td>
												<td class='alignment_css' width="70" class="left"><? echo number_format($row[csf('grey_qty')],2); ?></td>
												<td class='alignment_css' width="70" class="center"><? echo $row[csf('pre_cost_remarks')]; ?></td>


											</tr>
											<?
											$i++;
										}
										?>
									</tbody>
								</table>
								<table border="1" class="rpt_table" rules="all" width="1290" cellpadding="0" cellspacing="0" align="left">
									<tfoot>
										<th class='alignment_css' width="30"></th>
										<th class='alignment_css' width="115"> </th>
										<th class='alignment_css' width="95"> </th>
										<th class='alignment_css' width="110"> </th>
										<th class='alignment_css' width="60"> </th>
										<th class='alignment_css' width="60"> </th>
										<th class='alignment_css' width="100"></th>
										<th class='alignment_css' width="100"> </th>
										<th class='alignment_css' width="60"> </th>
										<th class='alignment_css' id="td_booking_qty" width="60"></th>
										<th class='alignment_css' width="80"></th>
										<th class='alignment_css' width="80"></th>
										<th class='alignment_css' width="60"></th>
										<th class='alignment_css' id="td_finish_qty" width="70"></th>
										<th class='alignment_css'  width="70"></th>
										<th class='alignment_css' id="td_grey_qty" width="70"> </th>
										<th class='alignment_css' width="70"></th>
									</tfoot>
								</table>
							</div>

						</div>
					</fieldset>
					<?
					exit();
				}


				if ($action == "grey_receive_popup") {
					echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
					page_style();
					extract($_REQUEST);

					$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
					$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
					?>
					<script>

						var tableFilters = {
							col_operation: {
								id: ["value_receive_qnty_in", "value_receive_qnty_out", "value_receive_qnty_tot"],
								col: [9,10,11],
								operation: ["sum", "sum", "sum"],
								write_method: ["innerHTML", "innerHTML", "innerHTML"]
							}
						}
						$(document).ready(function (e) {
							setFilterGrid('tbl_list_search', -1, tableFilters);
						});

						function print_window() {
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
					<div style="width:1237px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
						style="width:100px" class="formbutton"/></div>
						<fieldset style="width:1237px;">
							<div id="report_container">

								<table border="1" class="rpt_table" rules="all" width="1220" cellpadding="0" cellspacing="0">
									<thead>
										<th colspan="14"><b>Grey Receive Info</b></th>
									</thead>
									<thead>
										<th width="30">SL</th>
										<th width="115">Receive Id</th>
										<th width="95">Receive Basis</th>
										<th width="110">Product Details</th>
										<th width="100">Booking / Program No</th>
										<th width="100">Color</th>
										<th width="100">Color Range</th>
										<th width="60">Machine No</th>
										<th width="75">Production Date</th>
										<th width="80">Inhouse Production</th>
										<th width="80">Outside Production</th>
										<th width="80">Production Qnty</th>
										<th width="70">Challan No</th>
										<th>Kniting Com.</th>
									</thead>
								</table>
								<div style="width:1238px; max-height:330px; overflow-y:scroll" id="scroll_body">
									<table border="1" class="rpt_table" rules="all" width="1220" cellpadding="0" cellspacing="0"
									id="tbl_list_search">
									<?
									$i = 1;
									$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
									$total_receive_qnty = 0;
									$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

									$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
									$product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
									if($deter_id != "") $deter_id_cond = " and b.febric_description_id=$deter_id";

									$sql = "select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.po_breakdown_id in($order_id) and c.is_sales=1 $deter_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";
									$result = sql_select($sql);
									$all_program_arr=array();
									foreach($result as $v)
									{
										$all_program_arr[$v[csf("booking_no")]]=$v[csf("booking_no")];
									}
									$all_programs=implode(",", $all_program_arr);
									$program_wise_col_sql="SELECT  id , color_id, color_range FROM ppl_planning_info_entry_dtls Where ID in($all_programs) ";
									$program_wise_col_arr=array();
									foreach(sql_select($program_wise_col_sql) as $v)
									{
										$colors=$v[csf("color_id")];
										$program_id=$v[csf("id")];
										$range=$v[csf("color_range")];
										foreach(explode(",",$colors) as $color_id)
										{
											if($program_wise_col_arr[$program_id]['color']=="")$program_wise_col_arr[$program_id]['color']=$color_arr[$color_id];
											else $program_wise_col_arr[$program_id]['color'].=','.$color_arr[$color_id];
										}
										$program_wise_col_arr[$program_id]['range']=$color_range[$range];
									}
									foreach ($result as $row) {
										if ($i % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";

										$total_receive_qnty += $row[csf('quantity')];
										?>
										<tr bgcolor="<? echo $bgcolor; ?>"
											onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30"><? echo $i; ?></td>
											<td width="115" class="center"><? echo $row[csf('recv_number')]; ?></td>
											<td width="95" class="center"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
											<td width="110" class="center"><? echo $product_arr[$row[csf('prod_id')]]; ?></td>
											<td width="100" class="center"><? echo $row[csf('booking_no')]; ?></td>
											<td width="100" class="center"><? echo  $program_wise_col_arr[$row[csf('booking_no')]]['color']; ?></td>
											<td width="100" class="center"><? echo  $program_wise_col_arr[$row[csf('booking_no')]]['range']; ?></td>

											<td width="60" class="center"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
											<td width="75" class="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
											<td align="right" width="80">
												<?
												if ($row[csf('knitting_source')] != 3) {
													echo number_format($row[csf('quantity')], 2, '.', '');
													$total_receive_qnty_in += $row[csf('quantity')];
												} else echo "&nbsp;";
												?>
											</td>
											<td align="right" width="80">
												<?
												if ($row[csf('knitting_source')] == 3) {
													echo number_format($row[csf('quantity')], 2, '.', '');
													$total_receive_qnty_out += $row[csf('quantity')];
												} else echo "&nbsp;";
												?>
											</td>
											<td class="right"
											width="80"><? echo number_format($row[csf('quantity')], 2, '.', ''); ?></td>
											<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
											<td>
												<p><? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p>
											</td>
										</tr>
										<?
										$i++;
									}
									?>
								</table>
							</div>
							<table border="1" class="rpt_table" rules="all" width="1220" cellpadding="0" cellspacing="0">
								<tfoot>
									<th width="30">&nbsp;</th>
									<th width="115">&nbsp;</th>
									<th width="95">&nbsp;</th>
									<th width="110">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="60">&nbsp;</th>
									<th width="75" align="right">Total</th>
									<th width="80" align="right"
									id="value_receive_qnty_in"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
									<th width="80" align="right"
									id="value_receive_qnty_out"><? echo number_format($total_receive_qnty_out, 2, '.', ''); ?></th>
									<th width="80" align="right"
									id="value_receive_qnty_tot"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
									<th width="70">&nbsp;</th>
									<th>&nbsp;</th>
								</tfoot>
							</table>
						</div>
					</fieldset>
					<?
					exit();
				}

				if ($action == "grey_purchase_delivery") {
					echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
					page_style();
					extract($_REQUEST);
					$order_id = explode('_', $order_id);
					?>
					<script>
						function print_window() {
							document.getElementById('scroll_body').style.overflow = "auto";
							document.getElementById('scroll_body').style.maxHeight = "none";

							var w = window.open("Surprise", "#");
							var d = w.document.open();
							d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
								'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

							d.close();
							document.getElementById('scroll_body').style.overflowY = "scroll";
							document.getElementById('scroll_body').style.maxHeight = "230px";
						}
					</script>
					<div style="width:750px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
						style="width:100px" class="formbutton"/></div>
						<fieldset style="width:740px; margin-left:2px">
							<div id="report_container">
								<table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
									<thead>
										<th colspan="11"><b>Grey Receive / Purchase Info</b></th>
									</thead>
									<thead>
										<th width="30">SL</th>
										<th width="125">Receive Id</th>
										<th width="150">Product Details</th>
										<th width="75">Production Date</th>
										<th width="80">Delivery Quantity</th>
										<th>Kniting Com.</th>
									</thead>
								</table>
								<div style="width:740px; max-height:330px; overflow-y:scroll" id="scroll_body">
									<table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
										<?
										$i = 1;
										$total_receive_qnty = 0;
										$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
										$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
										$product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
										if($deter_id)
										{
											$deter_id_cond = " and b.determination_id=$deter_id";
										}
										$sql = "select a.sys_number,a.knitting_company,a.knitting_source,a.delevery_date, b.order_id, sum(b.current_delivery) as quantity,b.product_id,b.grey_sys_id from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, pro_roll_details c where a.id=b.mst_id and b.id = c.dtls_id and b.entry_form =56 and c.entry_form = 56 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.order_id in($order_id[0]) $deter_id_cond and c.is_sales =1 group by a.sys_number,a.knitting_company,a.knitting_source,a.delevery_date,b.order_id,b.product_id,b.grey_sys_id";
										$result = sql_select($sql);
										foreach ($result as $row) {
											if ($i % 2 == 0)
												$bgcolor = "#E9F3FF";
											else
												$bgcolor = "#FFFFFF";

											$total_receive_qnty += $row[csf('quantity')];
											?>
											<tr bgcolor="<? echo $bgcolor; ?>"
												onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="30"><? echo $i; ?></td>
												<td width="125"><p><? echo $row[csf('sys_number')]; ?></p></td>
												<td width="150"><p><? echo $product_arr[$row[csf('product_id')]]; ?></p></td>
												<td width="75" align="center"><? echo change_date_format($row[csf('delevery_date')]); ?></td>
												<td align="right" width="80">
													<?
													echo number_format($row[csf('quantity')], 2, '.', '');
													$total_receive_qnty_in += $row[csf('quantity')];
													?>
												</td>
												<td>
													<? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?>
												</td>
											</tr>
											<?
											$i++;
										}
										?>
										<tfoot>
											<th colspan="4" align="right">Total</th>
											<th align="right"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
											<th>&nbsp;</th>
										</tfoot>
									</table>
								</div>
							</div>
						</fieldset>
						<?
						exit();
					}
					if ($action == "grey_purchase_popup") {
						echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
						page_style();
						extract($_REQUEST);
						$order_id = explode('_', $order_id);
						?>
						<script>
							function print_window() {
								document.getElementById('scroll_body').style.overflow = "auto";
								document.getElementById('scroll_body').style.maxHeight = "none";

								var w = window.open("Surprise", "#");
								var d = w.document.open();
								d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
									'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

								d.close();
								document.getElementById('scroll_body').style.overflowY = "scroll";
								document.getElementById('scroll_body').style.maxHeight = "230px";
							}
						</script>
						<div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
							style="width:100px" class="formbutton"/></div>
							<fieldset style="width:1037px; margin-left:2px">
								<div id="report_container">
									<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
										<thead>
											<th colspan="11"><b>Grey Receive / Purchase Info</b></th>
										</thead>
										<thead>
											<th width="30">SL</th>
											<th width="125">Receive Id</th>
											<th width="95">Receive Basis</th>
											<th width="150">Product Details</th>
											<th width="110">Booking/PI/ Production No</th>
											<th width="75">Production Date</th>
											<th width="80">Inhouse Production</th>
											<th width="80">Outside Production</th>
											<th width="80">Production Qnty</th>
											<th width="65">Challan No</th>
											<th>Kniting Com.</th>
										</thead>
									</table>
									<div style="width:1037px; max-height:330px; overflow-y:scroll" id="scroll_body">
										<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
											<?
											if ($order_id[1] == 9) $receive_basis_cond = " and a.receive_basis in (9,10)"; else if ($order_id[1] == 0) $receive_basis_cond = " and a.receive_basis not in (9,10)";
											if($deter_id != "") $deter_id_cond = " and b.febric_description_id=$deter_id";else $deter_id_cond = "";
											$i = 1;
											$total_receive_qnty = 0;
											$product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
											$sql = "select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id $receive_basis_cond and a.entry_form in (22,58) and c.entry_form in (22,58) and c.po_breakdown_id in($order_id[0]) $deter_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id";
											$result = sql_select($sql);
											foreach ($result as $row) {
												if ($i % 2 == 0)
													$bgcolor = "#E9F3FF";
												else
													$bgcolor = "#FFFFFF";

												$total_receive_qnty += $row[csf('quantity')];
												?>
												<tr bgcolor="<? echo $bgcolor; ?>"
													onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
													<td width="30"><? echo $i; ?></td>
													<td width="125"><p><? echo $row[csf('recv_number')]; ?></p></td>
													<td width="95"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></p></td>
													<td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
													<td width="110"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
													<td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
													<td align="right" width="80">
														<?
														if ($row[csf('knitting_source')] != 3) {
															echo number_format($row[csf('quantity')], 2, '.', '');
															$total_receive_qnty_in += $row[csf('quantity')];
														} else echo "&nbsp;";
														?>
													</td>
													<td align="right" width="80">
														<?
														if ($row[csf('knitting_source')] == 3) {
															echo number_format($row[csf('quantity')], 2, '.', '');
															$total_receive_qnty_out += $row[csf('quantity')];
														} else echo "&nbsp;";
														?>
													</td>
													<td align="right"
													width="80"><? echo number_format($row[csf('quantity')], 2, '.', ''); ?></td>
													<td width="65"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
													<td>
														<p><? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?>
													&nbsp;</p></td>
												</tr>
												<?
												$i++;
											}
											?>
											<tfoot>
												<th colspan="6" align="right">Total</th>
												<th align="right"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
												<th align="right"><? echo number_format($total_receive_qnty_out, 2, '.', ''); ?></th>
												<th align="right"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
												<th>&nbsp;</th>
												<th>&nbsp;</th>
											</tfoot>
										</table>
									</div>
								</div>
							</fieldset>
							<?
							exit();
						}

						if ($action == "grey_issue_popup") {
							echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
							page_style();
							extract($_REQUEST);
							$order_id = explode('_', $order_id);
							?>
							<script>

								function print_window() {
									document.getElementById('scroll_body').style.overflow = "auto";
									document.getElementById('scroll_body').style.maxHeight = "none";

									var w = window.open("Surprise", "#");
									var d = w.document.open();
									d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
										'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

									d.close();
									document.getElementById('scroll_body').style.overflowY = "scroll";
									document.getElementById('scroll_body').style.maxHeight = "230px";
								}

							</script>
							<div style="width:955px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
								style="width:100px" class="formbutton"/></div>
								<fieldset style="width:970px; margin-left:3px">
									<div id="report_container">
										<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
											<thead>
												<tr>
													<th colspan="10"><b>Grey Issue Info</b></th>
												</tr>
												<tr>
													<th width="40">SL</th>
													<th width="120">Issue Id</th>
													<th width="100">Issue Purpose</th>
													<th width="100">Issue To</th>
													<th width="115">Booking No</th>
													<th width="90">Batch No</th>
													<th width="90">Batch Color</th>
													<th width="80">Issue Date</th>
													<th width="100">Issue Qnty (In)</th>
													<th>Issue Qnty (Out)</th>
												</tr>
											</thead>
										</table>
										<div style="width:967px; max-height:320px; overflow-y:scroll" id="scroll_body">
											<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
												<?
												$batch_color_details = return_library_array("select  id,color_id from pro_batch_create_mst", "id", "color_id");
												$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
												$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
												$batch_details = return_library_array("select id, batch_no from pro_batch_create_mst", "id", "batch_no");
												$i = 1;
												$issue_to = '';
												if($deter_id !="" ) {$deter_id_cond = " and d.detarmination_id=$deter_id";} else {$deter_id_cond = "";}
												$sql = "select a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, e.sales_booking_no booking_no, a.batch_no, sum(c.quantity) as quantity,d.detarmination_id  from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c,product_details_master d,fabric_sales_order_mst e  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and b.prod_id=d.id and a.entry_form in(16,61) and c.entry_form in(16,61) and c.po_breakdown_id in($order_id[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $deter_id_cond group by a.id,  a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, e.sales_booking_no, a.batch_no,d.detarmination_id";
												$result = sql_select($sql);
												foreach ($result as $row) {
													if ($i % 2 == 0)
														$bgcolor = "#E9F3FF";
													else
														$bgcolor = "#FFFFFF";

													if ($row[csf('knit_dye_source')] == 1) {
														$issue_to = $company_library[$row[csf('knit_dye_company')]];
													} else if ($row['knit_dye_source'] == 3) {
														$issue_to = $supplier_details[$row[csf('knit_dye_company')]];
													} else
													$issue_to = "&nbsp;";

													?>
													<tr bgcolor="<? echo $bgcolor; ?>"
														onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
														<td width="40"><? echo $i; ?></td>
														<td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
														<td width="100"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
														<td width="100"><p><? echo $issue_to; ?></p></td>
														<td width="115"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
														<td width="90"><p><? echo $batch_details[$row[csf('batch_no')]]; ?>&nbsp;</p></td>
														<td width="90"><p><? echo $color_array[$batch_color_details[$row[csf('batch_no')]]]; ?>
													&nbsp;</p></td>
													<td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
													<td width="100" align="right">
														<?
														if ($row[csf('knit_dye_source')] != 3) {
															echo number_format($row[csf('quantity')], 2);
															$total_issue_qnty += $row[csf('quantity')];
														} else echo "&nbsp;";
														?>
													</td>
													<td align="right">
														<?
														if ($row[csf('knit_dye_source')] == 3) {
															echo number_format($row[csf('quantity')], 2);
															$total_issue_qnty_out += $row[csf('quantity')];
														} else echo "&nbsp;";
														?>
													</td>
												</tr>
												<?
												$i++;
											}
											?>
											<tfoot>
												<tr>
													<th colspan="8" align="right">Total</th>
													<th align="right"><? echo number_format($total_issue_qnty, 2); ?></th>
													<th align="right"><? echo number_format($total_issue_qnty_out, 2); ?></th>
												</tr>
												<tr>
													<th colspan="8" align="right">Grand Total</th>
													<th align="right"
													colspan="2"><? echo number_format($total_issue_qnty + $total_issue_qnty_out, 2); ?></th>
												</tr>
											</tfoot>
										</table>
									</div>
									<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
										<thead>
											<th colspan="6"><b>Grey fabric issue Return</b></th>
										</thead>
										<thead>
											<th width="40">SL</th>
											<th width="105">Issue Return No</th>
											<th width="100">Issue No</th>
											<th width="100">Booking No</th>
											<th width="100">Return Date</th>
											<th width="100">Return Qnty</th>
										</thead>
										<?
										$total_yarn_return_qnty = 0;
										$total_yarn_return_qnty_out = 0;

										$sql ="select  a.po_breakdown_id, a.qnty, c.recv_number, c.receive_date, f.sales_booking_no, g.issue_number from pro_roll_details a, pro_roll_details b, inv_receive_master c, pro_grey_prod_entry_dtls d, fabric_sales_order_mst f ,inv_issue_master g where a.barcode_no = b.barcode_no and a.entry_form =61 and b.entry_form = 84 and b.mst_id = c.id and c.id = d.mst_id and b.po_breakdown_id= f.id and a.mst_id = g.id and g.entry_form=61 and a.is_sales =1 and a.po_breakdown_id in($order_id[0]) and a.is_deleted =0 and a.status_active = 1 and b.is_deleted =0 and b.status_active = 1 group by  a.po_breakdown_id, a.qnty, c.recv_number, c.receive_date,f.sales_booking_no,g.issue_number";
										$result = sql_select($sql);
										$y=1;
										foreach ($result as $row) {
											if ($y % 2 == 0)
												$bgcolor = "#E9F3FF";
											else
												$bgcolor = "#FFFFFF";
											$fab_desc = $composition_arr[$row[csf('detarmination_id')]] ;
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $y; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $y; ?>">
												<td width="40" class="center"><? echo $y; ?></td>
												<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
												<td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
												<td width="100"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
												<td width="100" class="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
												<td width="100" class="right"><? echo $row[csf('qnty')]; ?></td>
											</tr>
											<?
											$y++;
											$return_qnty += $row[csf('qnty')];
										}

										$balance_qnty = $total_issue_qnty + $total_issue_qnty_out - $return_qnty;
										?>
										<tr style="font-weight:bold">
											<td align="right" colspan="5">Total Return</td>
											<td align="right"><? echo number_format($return_qnty, 2); ?></td>
										</tr>
										<tfoot>
											<tr>
												<th align="right" colspan="5">Total Balance</th>
												<th align="right"><? echo number_format($balance_qnty, 2); ?></th>
											</tr>
										</tfoot>
									</table>
								</div>
							</fieldset>

							<?
							exit();
						}

						if($action=="grey_receive_by_batch_popup"){
							echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
							page_style();
							extract($_REQUEST);
							?>
							<script>

								function print_window() {
									document.getElementById('scroll_body').style.overflow = "auto";
									document.getElementById('scroll_body').style.maxHeight = "none";

									var w = window.open("Surprise", "#");
									var d = w.document.open();
									d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
										'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

									d.close();
									document.getElementById('scroll_body').style.overflowY = "scroll";
									document.getElementById('scroll_body').style.maxHeight = "230px";
								}

							</script>
							<div style="width:1020px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
								style="width:100px" class="formbutton"/></div>
								<fieldset style="width:1030px; margin-left:3px">
									<div id="report_container">
										<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
											<thead>
												<tr>
													<th colspan="10"><b>Grey Receive By Batch Info</b></th>
												</tr>
												<tr>
													<th width="40">SL</th>
													<th width="140">Company</th>
													<th width="140">Receive No</th>
													<th width="70">Year</th>
													<th width="120">Dyeing Source</th>
													<th width="140">Dyeing Company</th>
													<th width="130">Receive date</th>
													<th width="100">Recv Qty</th>
													<th width="130">Issue Challan</th>
												</tr>
											</thead>
										</table>
										<div style="width:1030px; max-height:320px; overflow-y:scroll" id="scroll_body">
											<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
												<?
												if($db_type==0)
												{
													$year_field=" YEAR(a.insert_date) as year,";
												}
												else if($db_type==2)
												{
													$year_field=" to_char(a.insert_date,'YYYY') as year,";

												}
												else $year_field="";

												if($deter_id != "") $deter_id_cond = " and b.febric_description_id=$deter_id"; else $deter_id_cond = "";
												$sql="select a.id,a.recv_number,a.company_id, a.dyeing_source,a.dyeing_company, a.receive_date,a.challan_no, $year_field c.po_breakdown_id,b.febric_description_id,sum(c.qnty) roll_wgt from pro_grey_batch_dtls b,inv_receive_mas_batchroll a,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and c.entry_form=62 and c.po_breakdown_id in($order_id) $deter_id_cond and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,a.recv_number,a.company_id, a.dyeing_source,a.dyeing_company, a.receive_date,a.challan_no,c.po_breakdown_id,b.febric_description_id,a.insert_date";
												$result = sql_select($sql);
												$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
												$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
												$result = sql_select($sql);
												$i=1;
												foreach ($result as $row) {
													if ($i % 2 == 0)
														$bgcolor = "#E9F3FF";
													else
														$bgcolor = "#FFFFFF";

													$knit_comp="&nbsp;";
													if($row[csf('dyeing_source')]==1)
														$knit_comp=$company_arr[$row[csf('dyeing_company')]];
													else
														$knit_comp=$supllier_arr[$row[csf('dyeing_company')]];

													?>
													<tr bgcolor="<? echo $bgcolor; ?>"
														onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
														<td width="40" style="text-align: center;"><? echo $i; ?></td>
														<td width="140" style="text-align: center;"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
														<td width="140" style="text-align: center;"><? echo $row[csf('recv_number')]; ?></td>
														<td width="70" style="text-align: center;"><? echo $row[csf('year')]; ?></td>
														<td width="120" style="text-align:center;"><? echo $knitting_source[$row[csf('dyeing_source')]]; ?></td>
														<td width="140" style="text-align: center;"><? echo $knit_comp; ?></td>
														<td width="130" style="text-align: center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
														<td width="100"><? echo number_format($row[csf('roll_wgt')],2); ?></td>
														<td width="130" style="text-align: center;"><? echo $row[csf('challan_no')]; ?></td>
													</tr>
													<?
													$total_issue_qnty += $row[csf('roll_wgt')];
													$i++;
												}
												?>
												<tfoot>
													<tr>
														<th colspan="7" align="right">Total</th>
														<th align="right"><? echo number_format($total_issue_qnty, 2); ?></th>
														<th></th>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
								</fieldset>
								<?
								exit();
							}

							if($action=="batch_popup"){
								echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
								page_style();
								extract($_REQUEST);
								?>
								<script>
									function print_window() {
										document.getElementById('scroll_body').style.overflow = "auto";
										document.getElementById('scroll_body').style.maxHeight = "none";
										var w = window.open("Surprise", "#");
										var d = w.document.open();
										d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
											'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
										d.close();
										document.getElementById('scroll_body').style.overflowY = "scroll";
										document.getElementById('scroll_body').style.maxHeight = "230px";
									}
								</script>
								<?
								$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
								$result = sql_select("select a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id,sum(b.batch_qnty)batch_qnty from pro_batch_create_mst a,pro_batch_create_dtls b, product_details_master c where a.id=b.mst_id and a.page_without_roll=0 and a.status_active=1 and a.entry_form=0 and a.is_deleted=0 and a.sales_order_id=$order_id and a.color_id=$color and b.prod_id=c.id and c.detarmination_id=$deter_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id,b.is_sales order by a.batch_date desc");

								//$result = sql_select("select a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id,sum(b.batch_qnty)batch_qnty from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.page_without_roll=0 and a.status_active=1 and a.entry_form=0 and a.is_deleted=0 and a.sales_order_id=$order_id and a.color_id=$deter_id  and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id,b.is_sales order by a.batch_date desc");//and (a.extention_no is null or a.extention_no=0)
								?>
								<div style="width:1020px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
									style="width:100px" class="formbutton"/></div>
									<fieldset style="width:1030px; margin-left:3px">
										<div id="report_container">
											<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
												<thead>
													<tr>
														<th width="50">SL No</th>
														<th width="100">Batch No</th>
														<th width="70">Ext. No</th>
														<th width="150">Sales Order No</th>
														<th width="105">Booking No</th>
														<th width="80">Batch Quantity</th>
														<th width="80">Batch Date</th>
														<th width="80">Batch Against</th>
														<th width="85">Batch For</th>
														<th>Color</th>
													</tr>
												</thead>
											</table>
											<div style="width:1030px; max-height:320px; overflow-y:scroll" id="scroll_body">
												<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
													<tbody>
														<?php
														$i = 1;
														foreach ($result as $row) {
															if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
															?>
															<tr onClick="js_set_value(<? echo $row[csf('id')]; ?>)" bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
																<td style="text-align: center;" width="50"><?php echo $i; ?></td>
																<td style="text-align: center;" width="100"><?php echo $row[csf("batch_no")]; ?></td>
																<td style="text-align: center;" width="70"><?php echo $row[csf("extention_no")]; ?></td>
																<td style="text-align: center;" width="150"><p><?php echo $row[csf("sales_order_no")]; ?></p></td>
																<td style="text-align: center;" width="105"><?php echo $row[csf("booking_no")]; ?></td>
																<td width="80"><?php echo number_format($row[csf("batch_qnty")], 2); ?></td>
																<td style="text-align: center;" width="80"><?php echo $row[csf("batch_date")]; ?></td>
																<td style="text-align: center;" width="80"><?php echo $batch_against[$row[csf("batch_against")]]; ?></td>
																<td style="text-align: center;" width="85"><?php echo $batch_for[$row[csf("batch_for")]]; ?></td>
																<td style="text-align: center;"><?php echo $color_arr[$row[csf("color_id")]]; ?></td>
															</tr>
															<?php
															$total_batch_qnty += $row[csf("batch_qnty")];
															$i++;
														}
														?>
														<tfoot>
															<tr>
																<th colspan="5" align="right">Total</th>
																<th align="right"><? echo number_format($total_batch_qnty, 2); ?></th>
																<th></th>
																<th></th>
																<th></th>
																<th></th>
															</tr>
														</tfoot>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</fieldset>
								<?
								exit();
							}

							if($action=="dyeing_popup"){
								echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
								page_style();
								extract($_REQUEST);
								?>
								<script>
									function print_window() {
										document.getElementById('scroll_body').style.overflow = "auto";
										document.getElementById('scroll_body').style.maxHeight = "none";
										var w = window.open("Surprise", "#");
										var d = w.document.open();
										d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
											'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
										d.close();
										document.getElementById('scroll_body').style.overflowY = "scroll";
										document.getElementById('scroll_body').style.maxHeight = "230px";
									}
								</script>
								<?
								$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

								$result = sql_select("select a.batch_no,a.batch_against,a.color_id,a.extention_no,a.sales_order_no,a.booking_no, b.item_description as febric_description, sum(b.batch_qnty) as batch_qnty, c.process_end_date, c.process_id from pro_batch_create_mst a, pro_batch_create_dtls b, pro_fab_subprocess c, product_details_master d where a.id=b.mst_id and a.id=c.batch_id and a.color_id=$color and c.load_unload_id=2 and c.entry_form=35 and b.prod_id=d.id and d.detarmination_id=$deter_id and b.po_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.batch_no,a.batch_against,a.color_id, a.extention_no, a.sales_order_no, a.booking_no, b.item_description, c.process_end_date, c.process_id");

								//$result = sql_select("select a.batch_no,a.batch_against,a.color_id,a.extention_no,a.sales_order_no,a.booking_no, b.item_description as febric_description, sum(b.batch_qnty) as batch_qnty,c.process_end_date,c.process_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and a.color_id=$color and c.load_unload_id=2 and c.entry_form=35 and b.po_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.batch_no,a.batch_against,a.color_id,a.extention_no,a.sales_order_no,a.booking_no, b.item_description,c.process_end_date,c.process_id");// and a.batch_against<>2
								?>
								<div style="width:1020px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
									style="width:100px" class="formbutton"/></div>
									<fieldset style="width:1030px; margin-left:3px">
										<div id="report_container">
											<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
												<thead>
													<tr>
														<th width="50">SL No</th>
														<th width="100">Batch No</th>
														<th width="70">Ext. No</th>
														<th width="150">Sales Order No</th>
														<th width="105">Booking No</th>
														<th width="80">Dyeing Quantity</th>
														<th width="80">Dyeing Date</th>
														<th width="80">Batch Against</th>
														<th width="85">Process For</th>
														<th>Color</th>
													</tr>
												</thead>
											</table>
											<div style="width:1030px; max-height:320px; overflow-y:scroll" id="scroll_body">
												<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
													<tbody>
														<?php
														$i = 1;
														foreach ($result as $row) {
															if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
															?>
															<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
																<td style="text-align: center;" width="50"><?php echo $i; ?></td>
																<td style="text-align: center;" width="100"><?php echo $row[csf("batch_no")]; ?></td>
																<td style="text-align: center;" width="70"><?php echo $row[csf("extention_no")]; ?></td>
																<td style="text-align: center;" width="150"><p><?php echo $row[csf("sales_order_no")]; ?></p></td>
																<td style="text-align: center;" width="105"><?php echo $row[csf("booking_no")]; ?></td>
																<td width="80"><?php echo number_format($row[csf("batch_qnty")], 2); ?></td>
																<td style="text-align: center;" width="80"><?php echo $row[csf("process_end_date")]; ?></td>
																<td style="text-align: center;" width="80"><?php echo $batch_against[$row[csf("batch_against")]]; ?></td>
																<td style="text-align: center;" width="85"><?php echo $conversion_cost_head_array[$row[csf("process_id")]]; ?></td>
																<td style="text-align: center;"><?php echo $color_arr[$row[csf("color_id")]]; ?></td>
															</tr>
															<?php
															$total_batch_qnty += $row[csf("batch_qnty")];
															$i++;
														}
														?>
														<tfoot>
															<tr>
																<th colspan="5" align="right">Total</th>
																<th align="right"><? echo number_format($total_batch_qnty, 2); ?></th>
																<th></th>
																<th></th>
																<th></th>
																<th></th>
															</tr>
														</tfoot>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</fieldset>
								<?
								exit();
							}

							if($action=="finish_receive_popup"){
								echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
								page_style();
								extract($_REQUEST);
								?>
								<script>
									function print_window() {
										document.getElementById('scroll_body').style.overflow = "auto";
										document.getElementById('scroll_body').style.maxHeight = "none";
										var w = window.open("Surprise", "#");
										var d = w.document.open();
										d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
											'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
										d.close();
										document.getElementById('scroll_body').style.overflowY = "scroll";
										document.getElementById('scroll_body').style.maxHeight = "230px";
									}
								</script>
								<?
								$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

								$result = sql_select("select a.recv_number, a.receive_date, a.booking_no, d.batch_no, d.booking_no as sales_booking_no, d.sales_order_no, b.color_id,b.body_part_id,e.product_name_details,b.uom, c.quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d,product_details_master e where a.id = b.mst_id and b.id = c.dtls_id and b.batch_id = d.id and b.prod_id = e.id  and a.receive_basis in (10,14) and a.entry_form in (225) and c.entry_form in (225) and c.po_breakdown_id in($order_id) and b.color_id=$color and c.is_sales = 1 and a.status_active = 1 and b.status_active=1 and c.status_active=1 order by b.uom ASC");

								?>
								<div style="width:970px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton"/></div>
								<fieldset style="width:970px; margin-left:3px">
									<div id="report_container">
										<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
											<thead>
												<th colspan="11"><b>Finish Fabric Receive</b></th>
											</thead>
											<thead>
												<tr>
													<th width="40">SL No</th>
													<th width="120">Challan No</th>
													<th width="80">Receive Date</th>
													<th width="80">Batch No</th>
													<th width="120">Body Part</th>
													<th width="250">Fabric Description</th>
													<th width="100">Color</th>
													<th width="50">UOM</th>
													<th >Rec. Qty</th>
												</tr>
											</thead>
										</table>
										<div style="width:970px; max-height:320px; overflow-y:scroll" id="scroll_body">
											<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
												<tbody>
													<?php
													$i = 1;
													foreach ($result as $row)
													{
														if (!in_array($row[csf("uom")], $checkUomArr))
														{
															$checkUomArr[$i] = $row[csf("uom")];
															if ($i > 1)
															{
																$sub_uom=implode(",",array_filter(array_unique(explode(",",chop($sub_uom,",")))));
																?>
																<tr style="font-weight: bold;">
																	<td colspan="8" align="right">UOM Total (<? echo $unit_of_measurement[$sub_uom]; ?>)</td>
																	<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
																</tr>
																<?
																$sub_uom="";
																$sub_uom_total=0;
															}
														}
														if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
														?>
														<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
															<td style="text-align: center;" width="40"><?php echo $i; ?></td>
															<td style="text-align: center;" width="120"><?php echo $row[csf("recv_number")]; ?></td>
															<td style="text-align: center;" width="80"><?php echo change_date_format($row[csf("receive_date")]); ?></td>
															<td style="text-align: center;" width="80"><p><?php echo $row[csf("batch_no")]; ?></p></td>
															<td style="text-align: center;" width="120"><p><?php echo $body_part[$row[csf("body_part_id")]]; ?></p></td>
															<td style="text-align: left;" width="250"><p><?php echo $row[csf("product_name_details")]; ?></p></td>
															<td style="text-align: center;" width="100"><?php echo $color_arr[$row[csf("color_id")]]; ?></td>
															<td style="text-align: center;" width="50"><?php echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
															<td style="text-align: right;"><?php echo $row[csf("quantity")]; ?></td>
														</tr>
														<?php
														$total_receive_qnty += $row[csf("quantity")];
														$sub_uom .= $row[csf("uom")].",";
														$sub_uom_total+=$row[csf('quantity')];
														$i++;
													}
													?>
													<tfoot>
														<tr style="font-weight: bold;">
															<td colspan="8" align="right">UOM Total (<? echo $unit_of_measurement[$row[csf('uom')]]; ?>) </td>
															<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
														</tr>
													<!-- <tr>
														<th colspan="8" align="right">Total</th>
														<th align="right"><? //echo number_format($total_receive_qnty, 2); ?></th>
													</tr> -->
												</tfoot>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</fieldset>
						<?
						exit();
					}

					if ($action == "finish_issue_popup") {
						echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
						page_style();
						extract($_REQUEST);
						$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
						$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
						$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
						$color_array = return_library_array("select id, color_name from lib_color", "id", "color_name");

						$composition_arr=array();
						$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
						$data_array=sql_select($sql_deter);
						if(count($data_array)>0)
						{
							foreach( $data_array as $row )
							{
								if(array_key_exists($row[csf('id')],$composition_arr))
								{
									$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
								}
								else
								{
									$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
								}
							}
						}
						$order_id = explode('_', $order_id);
						?>
						<script>
							function print_window() {
								document.getElementById('scroll_body').style.overflow = "auto";
								document.getElementById('scroll_body').style.maxHeight = "none";
								var w = window.open("Surprise", "#");
								var d = w.document.open();
								d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
									'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
								d.close();
								document.getElementById('scroll_body').style.overflowY = "scroll";
								document.getElementById('scroll_body').style.maxHeight = "230px";
							}
						</script>



						<div style="width:920px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton"/></div>
						<fieldset style="width:920px; margin-left:3px">
							<div id="report_container">

								<table border="1" class="rpt_table" rules="all" width="910" cellpadding="0" cellspacing="0">
									<thead>
										<th colspan="9"><b>Finish Fabric Delivery to Garments</b></th>
									</thead>
									<thead>
										<th width="40">SL</th>
										<th width="105">Challan No</th>
										<th width="75">Delivery Date</th>
										<th width="105">Batch No</th>
										<th width="150">Body Part</th>
										<th width="250">Fabric Description</th>
										<th width="60">Fabric Color</th>
										<th width="40">UOM</th>
										<th>Del. Qnty</th>

									</thead>
								</table>

								<div id="scroll_body">
									<table border="1" class="rpt_table" rules="all" width="910" cellpadding="0" cellspacing="0">
										<tbody>
											<?
											$i = 1;
											$sql = "select a.issue_number, a.issue_date, d.batch_no,b.body_part_id,b.uom, sum(c.quantity) as  quantity,   e.detarmination_id,e.color
											from inv_issue_master a,inv_finish_fabric_issue_dtls b, order_wise_pro_details c, pro_batch_create_mst d, product_details_master e
											where a.id = b.mst_id and b.id = c.dtls_id and b.batch_id = d.id and b.prod_id = e.id  and a.entry_form = 224 and c.entry_form = 224 and c.is_sales = 1 and c.po_breakdown_id  =  $order_id[0] and d.color_id= $color and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
											group by a.issue_number, a.issue_date, d.batch_no,b.body_part_id,b.uom,  e.detarmination_id,e.color order by b.uom ASC";
											$result = sql_select($sql);
											foreach ($result as $row) {
												if ($i % 2 == 0)
													$bgcolor = "#E9F3FF";
												else
													$bgcolor = "#FFFFFF";
												$fab_desc = $composition_arr[$row[csf('detarmination_id')]] ;
												if (!in_array($row[csf("uom")], $checkUomArr))
												{
													$checkUomArr[$i] = $row[csf("uom")];
													if ($i > 1)
													{
														$sub_uom=implode(",",array_filter(array_unique(explode(",",chop($sub_uom,",")))));
														?>
														<tr style="font-weight: bold;">
															<td colspan="8" align="right">UOM Total (<? echo $unit_of_measurement[$sub_uom]; ?>)</td>
															<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
														</tr>
														<?
														$sub_uom="";
														$sub_uom_total=0;
													}
												}
												?>
												<tr bgcolor="<? echo $bgcolor; ?>"
													onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
													<td width="40" class="center"><? echo $i; ?></td>
													<td width="105" class="center"><? echo $row[csf('issue_number')]; ?></td>
													<td width="75" class="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
													<td align="center" width="105" class="center"><? echo $row[csf('batch_no')]; ?></td>
													<td width="150" class="center"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
													<td style="text-align: center;" width="250"><p><? echo $fab_desc; ?></p></td>
													<td style="text-align: center;width:60px;"><? echo $color_array[$row[csf('color')]]; ?></td>
													<td style="text-align: left; width:40px;"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
													<td style="text-align: right;"><? echo $row[csf('quantity')]; ?></td>

												</tr>

												<?
												$i++;
												$total_finish_issue_qnty += $row[csf('quantity')];
												$sub_uom .= $row[csf("uom")].",";
												$sub_uom_total+=$row[csf('quantity')];
											}
											?>
											<tr style="font-weight: bold;">
												<td colspan="8" align="right">UOM Total (<? echo $unit_of_measurement[$row[csf('uom')]]; ?>) </td>
												<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
											</tr>
										</tbody>
									</table>

								</div>
							</div>
							<table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0">
								<thead>
									<th colspan="11"><b>Finish fabric issue Return</b></th>
								</thead>
								<thead>
									<th width="40">SL</th>
									<th width="105">Issue Return No</th>
									<th width="80">Issue No</th>
									<th width="75">Return Date</th>
									<th width="105">Batch No</th>
									<th width="90">Issue Qnty</th>
									<th width="160">Fabric Description</th>
								</thead>
								<?
								$total_yarn_return_qnty = 0;
								$total_yarn_return_qnty_out = 0;

								$sql = "select a.recv_number, a.receive_date, sum(c.quantity) quantity, a.booking_id, a.booking_no,  d.batch_no, e.detarmination_id
								from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d , product_details_master e
								where a.id = b.mst_id and b.id = c.dtls_id and b.batch_id = d.id and b.prod_id = e.id and c.po_breakdown_id  =  $order_id[0] and c.color_id=$color and a.entry_form = 233 and c.entry_form = 233 and c.is_sales=1 and a.status_active =1 and b.status_active =1 and c.status_active=1 group by a.recv_number, a.receive_date,  a.booking_id, a.booking_no,  d.batch_no, e.detarmination_id";
								$result = sql_select($sql);
								$y=1;
								foreach ($result as $row) {
									if ($y % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
									$fab_desc = $composition_arr[$row[csf('detarmination_id')]] ;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $y; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $y; ?>">
										<td width="40" class="center"><? echo $y; ?></td>
										<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
										<td width="80"><p><? echo $row[csf('booking_no')]; ?></p></td>
										<td width="75" class="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
										<td width="105"><p><? echo $row[csf('batch_no')]; ?></p></td>
										<td width="90" class="right"><? echo $row[csf('quantity')]; ?></td>
										<td align="right" width="160"><? echo $fab_desc; ?></td>
									</tr>
									<?
									$y++;
									$return_qnty += $row[csf('quantity')];
								}

								$balance_qnty = $total_finish_issue_qnty - $return_qnty;
								?>
								<tr style="font-weight:bold">
									<td align="right" colspan="5">Total Return</td>
									<td align="right"><? echo number_format($return_qnty, 2); ?></td>
									<td align="right"></td>
								</tr>
								<tfoot>
									<tr>
										<th align="right" colspan="5">Total Balance</th>
										<th align="right"><? echo number_format($balance_qnty, 2); ?></th>
										<th align="right"></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</fieldset>
					<?
					exit();
				}

				if ($action == "style_ref_search_popup")
				{
					echo load_html_head_contents("Style Reference / Job No. Info", "../../../", 1, 1, '', '', '');
					extract($_REQUEST);

					?>

					<script>

						var selected_id = new Array;
						var selected_name = new Array;

						function check_all_data() {
							var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
							tbl_row_count = tbl_row_count - 1;

							for (var i = 1; i <= tbl_row_count; i++) {
								js_set_value(i);
							}
						}

						function toggle(x, origColor) {
							var newColor = 'yellow';
							if (x.style) {
								x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
							}
						}

						function js_set_value(str) {

							toggle(document.getElementById('search' + str), '#FFFFCC');

							if (jQuery.inArray($('#txt_job_id' + str).val(), selected_id) == -1) {
								selected_id.push($('#txt_job_id' + str).val());
								selected_name.push($('#txt_job_no' + str).val());

							}
							else {
								for (var i = 0; i < selected_id.length; i++) {
									if (selected_id[i] == $('#txt_job_id' + str).val()) break;
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
							<fieldset style="width:620px;">
								<table width="550" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
								class="rpt_table" id="tbl_list">
								<thead>
									<th>Cust. Name</th>
									<th>Search By</th>
									<th id="search_by_td_up" width="170">Please Enter Sales Order No</th>
									<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
									<input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
									<input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
								</thead>
								<tbody>
									<tr>
										<td id="buyer_td">
											<?
											echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $buyerID, "");
											?>
										</td>
										<td align="center">
											<?
											$search_by_arr = array(1 => "Sales Order No", 2 => "Sales/Booking No");
											$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
											echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
											?>
										</td>
										<td align="center" id="search_by_td">
											<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
											id="txt_search_common"/>
										</td>
										<td align="center">
											<input type="button" name="button" class="formbutton" value="Show"
											onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'fabric_receive_status_report_sales_order_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
											style="width:90px;"/>
										</td>
									</tr>
								</tbody>
							</table>
							<div style="margin-top:05px" id="search_div"></div>
						</fieldset>
					</form>
				</div>
			</body>
			<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
			</html>
			<?
			exit();
		}

		if ($action == "create_job_search_list_view")
		{
			$data = explode('**', $data);
			$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
			$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

			$company_id = $data[0];
			$buyer_id = $data[1];
			$po_buyer_id = $data[2];
			$within_group = $data[3];
			$search_by = $data[4];
			$search_string = trim($data[5]);

			$search_field_cond = '';
			if ($search_string != "") {
				if ($search_by == 1) {
					$search_field_cond = " and a.job_no like '%" . $search_string . "'";
				}
				else
				{
					$search_field_cond = " and a.sales_booking_no like '%$search_string%'";
				}
			}

			if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and within_group=$within_group";
			//echo "==".$_SESSION['logic_erp']["buyer_id"];die;
			if ($po_buyer_id == 0) {
				if ($_SESSION['logic_erp']["buyer_id"] != "")
				{
					if($within_group == 1)
					{
						$po_buyer_id_cond = " and a.po_buyer in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
					}
					else if($within_group == 2)
					{
						$po_buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
					}
					else
					{
						$po_buyer_id_cond = " and (a.po_buyer in (" . $_SESSION['logic_erp']["buyer_id"] .") or a.buyer_id in ( " .$_SESSION['logic_erp']["buyer_id"]. ") )";
					}
				}
				else
				{
					$po_buyer_id_cond = "";
				}
			}
			else
			{
				if($within_group == 1)
				{
					$po_buyer_id_cond = " and a.po_buyer=$po_buyer_id";
				}
				else if($within_group == 2)
				{
					$po_buyer_id_cond = " and a.buyer_id=$po_buyer_id";
				}
				else
				{
					$po_buyer_id_cond = " and (a.po_buyer=$po_buyer_id or a.buyer_id=$po_buyer_id )";
				}
			}

			if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
			else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
			else $year_field = "";

			$sql = "SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, a.po_buyer, a.po_company_id, a.customer_buyer from fabric_sales_order_mst a
			where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $po_buyer_id_cond order by a.id desc";

			$result = sql_select($sql);
			?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" align="left">
				<thead>
					<th width="40">SL</th>
					<th width="70">Cust. Buyer</th>
					<th width="120">Sales/ Booking No</th>
					<th width="115">Sales Order No</th>
					<th width="80">Style Ref.</th>
					<th width="60">Year</th>
					<th>Within Group</th>
				</thead>
			</table>
			<div style="width:620px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" align="left" id="tbl_list_search">
					<?
					$i = 1;
					foreach ($result as $row)
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						if ($row[csf('within_group')] == 1)
							$sales_order_buyer = $company_arr[$row[csf('buyer_id')]];
						else
							$sales_order_buyer = $buyer_arr[$row[csf('customer_buyer')]];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
							<td width="40" align="center"><? echo $i; ?>
								<input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
								<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $row[csf('job_no')]; ?>"/>
							</td>
							<td width="70" align="center"><p><? echo $sales_order_buyer; ?>&nbsp;</p></td>
							<td width="120" align="center"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
							<td width="115" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
							<td width="80"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
							<td align="center"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
						</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
		<table width="600" cellspacing="0" cellpadding="0" style="border:none" align="left">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/> Check /
							Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton"
							value="Close" style="width:100px"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
		<?
		exit();
	}

	if ($action == "booking_no_search_popup")
	{
		echo load_html_head_contents("Booking Info", "../../../", 1, 1, '', '', '');
		extract($_REQUEST);
		?>
		<script>

			function js_set_value(booking_no,booking_num) {
				$('#hidden_booking_no').val(booking_no);
				$('#hidden_booking_num').val(booking_num);
				parent.emailwindow.hide();
			}

		</script>
	</head>

	<body>
		<div align="center" style="width:730px;">
			<form name="searchwofrm" id="searchwofrm" autocomplete=off>
				<fieldset style="width:100%;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="725" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Cust. Buyer</th>
							<th>Booking Date</th>
							<th width="150">Please Enter Sales/Booking No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:90px"
								class="formbutton"/>
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
								value="<? echo $companyID; ?>">
								<input type="hidden" name="cbo_within_group" id="cbo_within_group" class="text_boxes"
								value="<? echo $cbo_within_group; ?>">
								<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
								<input type="hidden" name="hidden_booking_num" id="hidden_booking_num" class="text_boxes" value="">
							</th>
						</thead>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
								?>
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
								style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
								readonly>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_sales_booking_no"
								id="txt_sales_booking_no"/>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_sales_booking_no').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value, 'create_booking_search_list_view', 'search_div', 'fabric_receive_status_report_sales_order_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:90px;"/>
							</td>
						</tr>
						<tr>
							<td colspan="5" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="width:100%; margin-top:5px; margin-left:3px" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_booking_search_list_view") {
	$data = explode("_", $data);

	$sales_booking_no = trim($data[0]);
	$company_id = $data[1];
	$buyer_id = $data[2];
	$date_from = trim($data[3]);
	$date_to = trim($data[4]);
	$cbo_within_group = trim($data[5]);


	if ($date_from != "" && $date_to != "") {
		if ($db_type == 0) {
			$date_cond = "and booking_date between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and booking_date between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");

	$search_field_cond = "";
	$search_field_cond .= " and sales_booking_no like '%$sales_booking_no%'";
	if ($buyer_id != 0) {
		$search_field_cond .= " and po_buyer=$buyer_id";
	}
	if ($cbo_within_group > 0) {
		$search_field_cond .= " and within_group=$cbo_within_group";
	}
	if ($db_type == 0) $year_field = "YEAR(insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YYYY') as year";
	else $year_field = "";

	$sql = "SELECT id, $year_field, sales_booking_no booking_no, booking_date,buyer_id, company_id,job_no, style_ref_no,po_job_no, within_group from fabric_sales_order_mst where company_id= $company_id and status_active =1 and is_deleted=0 $search_field_cond $date_cond group by id, sales_booking_no, booking_date,buyer_id, company_id,job_no, style_ref_no,po_job_no, within_group, insert_date";

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="80">Cust. Buyer</th>
			<th width="120">Sales/ Booking No</th>
			<th width="90">Sales Order No</th>
			<th width="120">Style Ref.</th>
			<th width="80">Year</th>
			<th>Within Group</th>
		</thead>
	</table>
	<div style="width:720px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		$j = 1;
		$result = sql_select($sql);
		foreach ($result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "") {
				$po_no = '';
				$po_ids = explode(",", $row[csf('po_break_down_id')]);
				foreach ($po_ids as $po_id) {
					if ($po_no == "") $po_no = $po_arr[$po_id]; else $po_no .= "," . $po_arr[$po_id];
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('booking_no_prefix_num')]; ?>')">
				<td width="40"><? echo $i; ?></td>
				<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo $row[csf('year')]; ?></td>
				<td><? echo $yes_no[$row[csf('within_group')]]; ?></td>
			</tr>
			<?
			$i++;
		}

		$sql_partial = "select a.id, a.booking_no,a.booking_no_prefix_num, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, b.job_no,b.po_job_no,b.style_ref_no from wo_booking_mst a, wo_booking_dtls c,fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond and a.entry_form=108 group by a.id, a.booking_no,a.booking_no_prefix_num,a.booking_date,a.buyer_id,a.company_id,a.delivery_date,a.currency_id,b.job_no,b.po_job_no,b.style_ref_no";
		$result_partial = sql_select($sql_partial);
		foreach ($result_partial as $row) {
			if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "") {
				$po_no = '';
				$po_ids = array_unique(explode(",", $row[csf('po_break_down_id')]));
				foreach ($po_ids as $po_id) {
					if ($po_no == "") $po_no = $po_arr[$po_id]; else $po_no .= "," . $po_arr[$po_id];
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('booking_no_prefix_num')]; ?>')">
				<td width="40"><? echo $j; ?>p</td>
				<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td><p><? echo $row[csf('po_job_no')]; ?>&nbsp;</p></td>
			</tr>
			<?
			$j++;
		}
		?>
	</table>
</div>
<?
exit();
}

if ($action == "order_popup") {
	echo load_html_head_contents("Order Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	$po_info = sql_select("select a.job_no,a.style_ref_no,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst   and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<thead>
			<th width="30">SL</th>
			<th width="115">Job No</th>
			<th width="75">Style Reference No</th>
			<th width="60">PO Number</th>
		</thead>
		<tbody>
			<?php
			$i = 1;
			foreach ($po_info as $row) {
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="115" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="75" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="60" align="center"><? echo $row[csf('po_number')]; ?></td>
				</tr>
				<?php
				$i++;
			}
			?>
		</tbody>
	</table>
	<?php
	exit();
}

if($action=="fabric_receive")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$color_array = return_library_array("select id, color_name from lib_color", "id", "color_name");
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}

	</script>
	<div style="width:1075px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1070px; margin-left:3px">
		<div id="report_container">

			<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="11"><b>Finish Fabric Production Deatails</b></th>
				</thead>
				<thead>
					<th width="30">SL</th>
					<th width="120">System Id</th>
					<th width="75">Production Date</th>
					<th width="100">Batch No</th>
					<!-- <th width="80">Rec. Basis</th> -->
					<th width="100">Dyeing Source</th>
					<th width="100">Dyeing Company</th>
					<th width="120">Body part</th>
					<th width="190">Fabric Description</th>
					<th width="100">Fabric Color</th>
					<th width="40">UOM</th>
					<th>Prod. Qnty</th>

				</thead>
			</table>
			<div style="width:1070px; max-height:320px; overflow-y:scroll" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0">
					<?
					$i=1;
					$total_fabric_recv_qnty=0; $dye_company='';
					$sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id,b.body_part_id,b.uom, d.batch_no, b.prod_id, sum(c.quantity) as quantity,e.product_name_details,e.color from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d,  product_details_master e where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id = d.id and b.prod_id = e.id and a.entry_form in (7) and c.entry_form in (7) and c.po_breakdown_id in($order_id)  and b.is_sales =1 and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id,b.body_part_id,b.uom,d.batch_no, b.prod_id,e.product_name_details,e.color order by b.uom";
					$result=sql_select($sql);

					foreach($result as $row)
					{

						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						if($row[csf('knitting_source')]==1)
						{
							$dye_company=$company_library[$row[csf('knitting_company')]];
						}
						else if($row['knitting_source']==3)
						{
							$dye_company=$supplier_details[$row[csf('knitting_company')]];
						}
						else
							$dye_company="&nbsp;";

						$total_fabric_recv_qnty+=$row[csf('quantity')];

						if (!in_array($row[csf("uom")], $checkUomArr))
						{
							$checkUomArr[$i] = $row[csf("uom")];
							if ($i > 1)
							{
								$sub_uom=implode(",",array_filter(array_unique(explode(",",chop($sub_uom,",")))));
								?>
								<tr style="font-weight: bold;">
									<td colspan="10" align="right">UOM Total (<? echo $unit_of_measurement[$sub_uom]; ?>)</td>
									<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
								</tr>
								<?
								$sub_uom="";
								$sub_uom_total=0;
							}
						}

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><? echo $i; ?></td>
							<td align="center" width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
							<td align="center" width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
							<!-- <td width="80"><? //echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td> -->
							<td align="center" width="100"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
							<td align="center" width="100"><p><? echo $dye_company; ?></p></td>
							<td align="center" width="120"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
							<td width="190"><p><? echo $row[csf('product_name_details')]; ?></p></td>
							<td align="center" width="100"><p><? echo $color_array[$row[csf('color')]] ; ?></p></td>
							<td width="40"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
							<td align="right"><? echo number_format($row[csf('quantity')],2); ?></td>

						</tr>
						<?
						$sub_uom .= $row[csf("uom")].",";
						$sub_uom_total+=$row[csf('quantity')];
						$i++;
					}
					?>

					<tr style="font-weight: bold;">
						<td colspan="10" align="right">UOM Total (<? echo $unit_of_measurement[$row[csf('uom')]]; ?>) </td>
						<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
					</tr>
                    <!-- <tfoot>
                        <th colspan="10" align="right">Total</th>
                        <th align="right"><? //echo number_format($total_fabric_recv_qnty,2); ?></th>
                    </tfoot> -->
                </table>
            </div>
        </div>
    </fieldset>
    <?
    exit();
}

if($action=="finish_delivery_to_store")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_data=explode('_',$order_id);
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	?>
	<script>

		var tableFilters = {
			col_operation: {
				id: ["value_delivery_qnty"],
				col: [8],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1,tableFilters);
		});

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";

			$('#tbl_list_search tr:first').hide();

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";

			$('#tbl_list_search tr:first').show();
		}

	</script>
	<div style="width:970px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:970px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="10"><b>Finish Delivery To Store Info</b></th>
				</thead>
				<thead>
					<th width="30">SL</th>
					<th width="115">Challan No</th>
					<th width="80">Delivery Date</th>
					<th width="100">Batch No</th>
					<th width="120">Body Part</th>
					<th width="250">Fabric Description</th>
					<th width="70">Color</th>
					<th width="40">UOM</th>
					<th>Delivery Qnty</th>
				</thead>
			</table>
			<div style="width:990px; max-height:380px; overflow-y:scroll" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0" id="tbl_list_searchs">
					<?
					$i=1; $total_delivery_fin_qnty=0;
					$sql="select a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm,b.bodypart_id,b.uom,b.batch_id, b.dia, sum(b.current_delivery) as delivery_qty, c.product_name_details, c.color from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.entry_form in (54) and b.order_id in ($order_id) and c.color='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_sales = 1 group by a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm,b.bodypart_id,b.uom,b.batch_id, b.dia, c.product_name_details, c.color order by b.uom ASC";
					$result=sql_select($sql);
					$batch_ids="";
					foreach($result as $row)
					{
						$batch_ids.=$row[csf('batch_id')].',';
					}
					$batch_ids=chop($batch_ids,",");
					$batchNo_arr = return_library_array("select id, batch_no from pro_batch_create_mst where id in($batch_ids)","id","batch_no");

					foreach($result as $row)
					{
						if (!in_array($row[csf("uom")], $checkUomArr))
						{
							$checkUomArr[$i] = $row[csf("uom")];
							if ($i > 1)
							{
								$sub_uom=implode(",",array_filter(array_unique(explode(",",chop($sub_uom,",")))));
								?>
								<tr style="font-weight: bold;">
									<td colspan="8" align="right">UOM Total (<? echo $unit_of_measurement[$sub_uom]; ?>)</td>
									<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
								</tr>
								<?
								$sub_uom="";
								$sub_uom_total=0;
							}
						}

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><? echo $i; ?></td>
							<td align="center" width="115"><p><? echo $row[csf('sys_number')]; ?></p></td>
							<td align="center" width="80"><p><? echo change_date_format($row[csf('delevery_date')]); ?></p></td>
							<td align="center" width="100"><p><? echo $batchNo_arr[$row[csf('batch_id')]]; ?></p></td>
							<td align="center" width="120"><p><? echo $body_part[$row[csf('bodypart_id')]]; ?></p></td>
							<td width="250"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
							<td align="center" width="70"><? echo $color_arr[$row[csf('color')]]; ?></td>
							<td width="40"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
							<td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
						</tr>
						<?
						$total_delivery_fin_qnty+=$row[csf('delivery_qty')];
						$sub_uom .= $row[csf("uom")].",";
						$sub_uom_total+=$row[csf('delivery_qty')];
						$i++;
					}
					?>
					<tfoot>
						<tr style="font-weight: bold;">
							<td colspan="8"  align="right" >UOM Total (<? echo $unit_of_measurement[$row[csf('uom')]]; ?>) </td>
							<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
						</tr>
					</tfoot>
				</table>
			</div>

		</div>
	</fieldset>
	<?
	exit();
}


if ($action == "finish_trans_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	page_style();
	extract($_REQUEST);
	$store_arr = return_library_array("select id, store_name from lib_store_location", "id", "store_name");
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);

	if(count($deter_array)>0)
	{
		foreach( $deter_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$row[csf('construction')];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
		}
	}
	unset($deter_array);
	?>
	<script>

		function print_window() {
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

			d.close();
		}

	</script>
	<div style="width:975px" align="center"><input type="button" value="Print Preview" onClick="print_window()" Style="width:100px" class="formbutton"/></div>
	<fieldset style="width:980px; margin:auto;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th colspan="11">Transfer In</th>
					</tr>
					<tr>
						<th width="40">SL</th>
						<th width="115">Transfer Id</th>
						<th width="80">Transfer Date</th>
						<th width="100">From Order</th>
						<th width="100">Batch No</th>
						<th width="70">Fab. Shade</th>
						<th width="100">Body Part</th>
						<th width="70">Dia/W.Type</th>
						<th width="110">Fabric Description</th>
						<th width="80">Store Name</th>
						<th>Transfer In Qnty</th>
					</tr>

				</thead>
				<?
				$i = 1;
				$total_trans_in_qnty = 0;
				if($db_type ==1)
				{
					$null_cond = " and c.quantity =''";
				}else{
					$null_cond = " and c.quantity is not null";
				}
				$sql = "select a.transfer_system_id,a.transfer_date,a.from_order_id,d.job_no,b.batch_id,e.batch_no, b.fabric_shade, b.body_part_id, b.dia_width_type, b.feb_description_id, b.to_store,  c.quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, pro_batch_create_mst e where a.id = b.mst_id and b.id = c.dtls_id and a.from_order_id = d.id and c.trans_type=5 and b.batch_id = e.id and c.color_id =$color and a.to_order_id = $order_id and c.entry_form = 230 and a.entry_form = 230 and a.status_active =1 and a.is_deleted =0 and c.quantity <> 0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and d.status_active=1 and a.is_deleted=0 and c.is_sales=1";
				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"
						onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
						<td width="100"><p><? echo $row[csf('job_no')]; ?></p></td>
						<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
						<td width="70"><p><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></p></td>
						<td width="100"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						<td width="70"><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
						<td width="110"><p><? echo $composition_arr[$row[csf('feb_description_id')]]; ?></p></td>
						<td width="80"><p><? echo $store_arr[$row[csf('to_store')]]; ?></p></td>
						<td align="right"><? echo number_format($row[csf('quantity')], 2); ?> </td>
					</tr>
					<?
					$total_trans_in_qnty += $row[csf('quantity')];
					$i++;
				}
				?>
				<tr style="font-weight:bold;background-color: #e0e0e0;">
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
					<td align="right"><? echo number_format($total_trans_in_qnty, 2); ?></td>
				</tr>
				<thead>
					<tr>
						<th colspan="11">Transfer Out</th>
					</tr>
					<tr>
						<th width="40">SL</th>
						<th width="115">Transfer Id</th>
						<th width="80">Transfer Date</th>
						<th width="100">To Order</th>
						<th width="100">Batch No</th>
						<th width="70">Fab. Shade</th>
						<th width="100">Body Part</th>
						<th width="70">Dia/W.Type</th>
						<th width="110">Fabric Description</th>
						<th width="80">Store Name</th>
						<th>Transfer Qnty</th>
					</tr>
				</thead>
				<?
				$total_trans_out_qnty = 0;
				$y =1;
				$sql = "select a.transfer_system_id,a.transfer_date,a.to_order_id,d.job_no,b.batch_id,e.batch_no, b.fabric_shade, b.body_part_id, b.dia_width_type, b.feb_description_id, b.from_store, c.quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, pro_batch_create_mst e where a.id = b.mst_id and b.id = c.dtls_id and a.to_order_id = d.id and c.trans_type=6 and b.batch_id = e.id  and  c.color_id =$color and a.from_order_id = $order_id and c.entry_form = 230 and a.entry_form = 230 and a.status_active =1 and a.is_deleted =0 and c.quantity is not null and  c.quantity <> 0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and d.status_active=1 and a.is_deleted=0 and c.is_sales=1";
				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($y % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"
						onclick="change_color('tr_<? echo $y; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $y; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
						<td width="100"><p><? echo $row[csf('job_no')]; ?></p></td>
						<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
						<td width="70"><p><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></p></td>
						<td width="100"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						<td width="70"><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
						<td width="110"><p><? echo $composition_arr[$row[csf('feb_description_id')]]; ?></p></td>
						<td width="80"><p><? echo $store_arr[$row[csf('from_store')]]; ?></p></td>
						<td align="right"><? echo number_format($row[csf('quantity')], 2); ?> </td>
					</tr>
					<?
					$total_trans_out_qnty += $row[csf('quantity')];
					$y++;
				}
				?>
				<tr style="font-weight:bold;background-color: #e0e0e0;">
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
					<td align="right"><? echo number_format($total_trans_out_qnty, 2); ?></td>
				</tr>
				<tfoot>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>Net Transfer</th>
					<th><? echo number_format($total_trans_in_qnty - $total_trans_out_qnty, 2); ?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if ($action == "finish_trans_in_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	page_style();
	extract($_REQUEST);
	$store_arr = return_library_array("select id, store_name from lib_store_location", "id", "store_name");
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);

	if(count($deter_array)>0)
	{
		foreach( $deter_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$row[csf('construction')];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
		}
	}
	unset($deter_array);
	?>
	<script>

		function print_window() {
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

			d.close();
		}

	</script>
	<div style="width:975px" align="center"><input type="button" value="Print Preview" onClick="print_window()" Style="width:100px" class="formbutton"/></div>
	<fieldset style="width:980px; margin:auto;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th colspan="11">Transfer In</th>
					</tr>
					<tr>
						<th width="40">SL</th>
						<th width="115">Transfer Id</th>
						<th width="80">Transfer Date</th>
						<th width="100">From Order</th>
						<th width="100">Batch No</th>
						<th width="70">Fab. Shade</th>
						<th width="100">Body Part</th>
						<th width="70">Dia/W.Type</th>
						<th width="110">Fabric Description</th>
						<th width="80">Store Name</th>
						<th>Transfer In Qnty</th>
					</tr>

				</thead>
				<?
				$i = 1;
				$total_trans_in_qnty = 0;
				if($db_type ==1)
				{
					$null_cond = " and c.quantity =''";
				}else{
					$null_cond = " and c.quantity is not null";
				}
				$sql = "select a.transfer_system_id,a.transfer_date,a.from_order_id,d.job_no,b.batch_id,e.batch_no, b.fabric_shade, b.body_part_id, b.dia_width_type, b.feb_description_id, b.to_store,  c.quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, pro_batch_create_mst e where a.id = b.mst_id and b.id = c.dtls_id and a.from_order_id = d.id and c.trans_type=5 and b.batch_id = e.id and c.color_id =$color and a.to_order_id = $order_id and c.entry_form = 230 and a.entry_form = 230 and a.status_active =1 and a.is_deleted =0 and c.quantity <> 0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and d.status_active=1 and a.is_deleted=0 and c.is_sales=1";
				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"
						onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
						<td width="100"><p><? echo $row[csf('job_no')]; ?></p></td>
						<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
						<td width="70"><p><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></p></td>
						<td width="100"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						<td width="70"><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
						<td width="110"><p><? echo $composition_arr[$row[csf('feb_description_id')]]; ?></p></td>
						<td width="80"><p><? echo $store_arr[$row[csf('to_store')]]; ?></p></td>
						<td align="right"><? echo number_format($row[csf('quantity')], 2); ?> </td>
					</tr>
					<?
					$total_trans_in_qnty += $row[csf('quantity')];
					$i++;
				}
				?>

				<tfoot>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>Total</th>
					<th><? echo number_format($total_trans_in_qnty, 2); ?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if ($action == "finish_trans_out_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	page_style();
	extract($_REQUEST);
	$store_arr = return_library_array("select id, store_name from lib_store_location", "id", "store_name");
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);

	if(count($deter_array)>0)
	{
		foreach( $deter_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$row[csf('construction')];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
		}
	}
	unset($deter_array);
	?>
	<script>

		function print_window() {
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

			d.close();
		}

	</script>
	<div style="width:975px" align="center"><input type="button" value="Print Preview" onClick="print_window()" Style="width:100px" class="formbutton"/></div>
	<fieldset style="width:980px; margin:auto;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th colspan="11">Transfer Out</th>
					</tr>
					<tr>
						<th width="40">SL</th>
						<th width="115">Transfer Id</th>
						<th width="80">Transfer Date</th>
						<th width="100">To Order</th>
						<th width="100">Batch No</th>
						<th width="70">Fab. Shade</th>
						<th width="100">Body Part</th>
						<th width="70">Dia/W.Type</th>
						<th width="110">Fabric Description</th>
						<th width="80">Store Name</th>
						<th>Transfer Qnty</th>
					</tr>
				</thead>
				<?
				$total_trans_out_qnty = 0;
				$y =1;
				$sql = "select a.transfer_system_id,a.transfer_date,a.to_order_id,d.job_no,b.batch_id,e.batch_no, b.fabric_shade, b.body_part_id, b.dia_width_type, b.feb_description_id, b.from_store, c.quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, pro_batch_create_mst e where a.id = b.mst_id and b.id = c.dtls_id and a.to_order_id = d.id and c.trans_type=6 and b.batch_id = e.id  and  c.color_id =$color and a.from_order_id = $order_id and c.entry_form = 230 and a.entry_form = 230 and a.status_active =1 and a.is_deleted =0 and c.quantity is not null and  c.quantity <> 0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and d.status_active=1 and a.is_deleted=0 and c.is_sales=1";
				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($y % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"
						onclick="change_color('tr_<? echo $y; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $y; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
						<td width="100"><p><? echo $row[csf('job_no')]; ?></p></td>
						<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
						<td width="70"><p><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></p></td>
						<td width="100"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						<td width="70"><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
						<td width="110"><p><? echo $composition_arr[$row[csf('feb_description_id')]]; ?></p></td>
						<td width="80"><p><? echo $store_arr[$row[csf('from_store')]]; ?></p></td>
						<td align="right"><? echo number_format($row[csf('quantity')], 2); ?> </td>
					</tr>
					<?
					$total_trans_out_qnty += $row[csf('quantity')];
					$y++;
				}
				?>
				<tfoot>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>Total</th>
					<th><? echo number_format($total_trans_out_qnty, 2); ?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="production_qty_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	page_style();
	extract($_REQUEST);
	?>
	<script>
		function print_window() {
			document.getElementById('scroll_body').style.overflow = "auto";
			document.getElementById('scroll_body').style.maxHeight = "none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY = "scroll";
			document.getElementById('scroll_body').style.maxHeight = "230px";
		}
	</script>
	<?
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	
	$sql = "SELECT a.id, a.batch_no,a.batch_against,a.color_id,a.extention_no,a.sales_order_no,a.booking_no, a.batch_for as process_for,  sum(c.receive_qnty) as production_qty, d.receive_date, d.recv_number
	from pro_batch_create_mst a, pro_finish_fabric_rcv_dtls c, inv_receive_master d 
	where a.id=c.batch_id and c.mst_id=d.id and a.color_id=$color and d.entry_form in(7,66) and c.order_id in('$order_id') and c.fabric_description_id=$deter_id and a.status_active=1 and a.is_deleted=0 
	and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
	group by a.id, a.batch_no,a.batch_against,a.color_id,a.extention_no,a.sales_order_no,a.booking_no, a.batch_for, d.receive_date, d.recv_number order by d.recv_number";
	// echo $sql;
	$result = sql_select($sql);
	?>
	<div style="width:1020px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
		style="width:100px" class="formbutton"/></div>
		<fieldset style="width:1030px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th width="50">SL No</th>
							<th width="100">FFPR. Challan</th>
							<th width="100">Batch No</th>
							<th width="50">Ext. No</th>
							<th width="100">Sales Order No</th>
							<th width="80">Booking No</th>
							<th width="80">Fin. Fab. Production</th>
							<th width="80">Production Date</th>
							<th width="80">Batch Against</th>
							<th width="80">Process For</th>
							<th>Color</th>
						</tr>
					</thead>
				</table>
				<div style="width:1030px; max-height:320px; overflow-y:scroll" id="scroll_body">
					<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
						<tbody>
							<?php
							$i = 1;
							foreach ($result as $row) 
							{
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
									<td style="text-align: center;" width="50"><?php echo $i; ?></td>
									<td style="text-align: center;" width="100"><?php echo $row[csf("recv_number")]; ?></td>
									<td style="text-align: center;" width="100"><?php echo $row[csf("batch_no")]; ?></td>
									<td style="text-align: center;" width="50"><?php echo $row[csf("extention_no")]; ?></td>
									<td style="text-align: center;" width="100"><p><?php echo $row[csf("sales_order_no")]; ?></p></td>
									<td style="text-align: center;" width="80"><?php echo $row[csf("booking_no")]; ?></td>
									<td width="80"><?php echo number_format($row[csf("production_qty")], 2); ?></td>
									<td style="text-align: center;" width="80"><?php echo $row[csf("receive_date")]; ?></td>
									<td style="text-align: center;" width="80"><?php echo $batch_against[$row[csf("batch_against")]]; ?></td>
									<td style="text-align: center;" width="80"><?php echo $conversion_cost_head_array[$row[csf("process_for")]]; ?></td>
									<td style="text-align: center;"><?php echo $color_arr[$row[csf("color_id")]]; ?></td>
								</tr>
								<?php
								$total_production_qty += $row[csf("production_qty")];
								$i++;
							}
							?>
							<tfoot>
								<tr>
									<th colspan="6" align="right">Total</th>
									<th align="right"><? echo number_format($total_production_qty, 2); ?></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</tbody>
					</table>
				</div>
			</div>
		</fieldset>
	</div>
	<?
	exit();
}

if($action=="fin_delv_to_store_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	page_style();
	extract($_REQUEST);
	?>
	<script>
		function print_window() {
			document.getElementById('scroll_body').style.overflow = "auto";
			document.getElementById('scroll_body').style.maxHeight = "none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY = "scroll";
			document.getElementById('scroll_body').style.maxHeight = "230px";
		}
	</script>
	<?
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	
	$sql = "SELECT a.id, a.batch_no,a.batch_against,a.color_id,a.extention_no,a.sales_order_no,a.booking_no, a.batch_for as process_for, sum(c.current_delivery) as delivery_qty, d.delevery_date as delivery_date, d.sys_number
    from pro_batch_create_mst a, pro_grey_prod_delivery_dtls c, pro_grey_prod_delivery_mst d 
    where a.id=c.batch_id and c.mst_id=d.id and a.color_id=$color and d.entry_form in(54,67) and c.order_id in($order_id) and c.determination_id=$deter_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
    group by a.id, a.batch_no,a.batch_against,a.color_id,a.extention_no,a.sales_order_no,a.booking_no, a.batch_for, d.delevery_date, d.sys_number order by d.sys_number";
	//echo $sql;
	$result = sql_select($sql);
	?>
	<div style="width:1020px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
		style="width:100px" class="formbutton"/></div>
		<fieldset style="width:1030px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th width="50">SL No</th>
							<th width="100">Batch No</th>
							<th width="100">FDSR. Challan</th>
							<th width="50">Ext. No</th>
							<th width="100">Sales Order No</th>
							<th width="80">Booking No</th>
							<th width="80">Fin. Fab. Deliv.</th>
							<th width="80">Deliv. Date</th>
							<th width="80">Batch Against</th>
							<th width="80">Process For</th>
							<th>Color</th>
						</tr>
					</thead>
				</table>
				<div style="width:1030px; max-height:320px; overflow-y:scroll" id="scroll_body">
					<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
						<tbody>
							<?php
							$i = 1;
							foreach ($result as $row) 
							{
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
									<td style="text-align: center;" width="50"><?php echo $i; ?></td>
									<td style="text-align: center;" width="100"><?php echo $row[csf("sys_number")]; ?></td>
									<td style="text-align: center;" width="100"><?php echo $row[csf("batch_no")]; ?></td>
									<td style="text-align: center;" width="50"><?php echo $row[csf("extention_no")]; ?></td>
									<td style="text-align: center;" width="100"><p><?php echo $row[csf("sales_order_no")]; ?></p></td>
									<td style="text-align: center;" width="80"><?php echo $row[csf("booking_no")]; ?></td>
									<td width="80"><?php echo number_format($row[csf("delivery_qty")], 2); ?></td>
									<td style="text-align: center;" width="80"><?php echo $row[csf("delivery_date")]; ?></td>
									<td style="text-align: center;" width="80"><?php echo $batch_against[$row[csf("batch_against")]]; ?></td>
									<td style="text-align: center;" width="80"><?php echo $conversion_cost_head_array[$row[csf("process_for")]]; ?></td>
									<td style="text-align: center;"><?php echo $color_arr[$row[csf("color_id")]]; ?></td>
								</tr>
								<?php
								$total_delv_to_store_qty += $row[csf("delivery_qty")];
								$i++;
							}
							?>
							<tfoot>
								<tr>
									<th colspan="6" align="right">Total</th>
									<th align="right"><? echo number_format($total_delv_to_store_qty, 2); ?></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</tbody>
					</table>
				</div>
			</div>
		</fieldset>
	</div>
	<?
	exit();
}

if($action=="fin_rec_by_textile_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	page_style();
	extract($_REQUEST);
	?>
	<script>
		function print_window() {
			document.getElementById('scroll_body').style.overflow = "auto";
			document.getElementById('scroll_body').style.maxHeight = "none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY = "scroll";
			document.getElementById('scroll_body').style.maxHeight = "230px";
		}
	</script>
	<?
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

    $sql = "SELECT a.id, a.batch_no,a.batch_against,a.color_id,a.extention_no,a.sales_order_no,a.booking_no, a.batch_for as process_for, sum(c.receive_qnty) as fin_recv_textile_qty, d.receive_date, d.recv_number 
    from pro_batch_create_mst a, pro_finish_fabric_rcv_dtls c, inv_receive_master d 
    where a.id=c.batch_id and c.mst_id=d.id and a.color_id=$color and d.entry_form in(225,317) and c.order_id in('$order_id') and c.fabric_description_id=$deter_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
    group by a.id, a.batch_no,a.batch_against,a.color_id,a.extention_no,a.sales_order_no,a.booking_no, a.batch_for, d.receive_date, d.recv_number order by d.recv_number";
	// echo $sql;
	$result = sql_select($sql);
	?>
	<div style="width:1020px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
		style="width:100px" class="formbutton"/></div>
		<fieldset style="width:1030px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th width="50">SL No</th>
							<th width="100">FFRRT. Challan</th>
							<th width="100">Batch No</th>
							<th width="50">Ext. No</th>
							<th width="100">Sales Order No</th>
							<th width="80">Booking No</th>
							<th width="80">Fin. Fab. Deliv.</th>
							<th width="80">Deliv. Date</th>
							<th width="80">Batch Against</th>
							<th width="80">Process For</th>
							<th>Color</th>
						</tr>
					</thead>
				</table>
				<div style="width:1030px; max-height:320px; overflow-y:scroll" id="scroll_body">
					<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
						<tbody>
							<?php
							$i = 1;
							foreach ($result as $row) 
							{
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
									<td style="text-align: center;" width="50"><?php echo $i; ?></td>
									<td style="text-align: center;" width="100"><?php echo $row[csf("recv_number")]; ?></td>
									<td style="text-align: center;" width="100"><?php echo $row[csf("batch_no")]; ?></td>
									<td style="text-align: center;" width="50"><?php echo $row[csf("extention_no")]; ?></td>
									<td style="text-align: center;" width="100"><p><?php echo $row[csf("sales_order_no")]; ?></p></td>
									<td style="text-align: center;" width="80"><?php echo $row[csf("booking_no")]; ?></td>
									<td width="80"><?php echo number_format($row[csf("fin_recv_textile_qty")], 2); ?></td>
									<td style="text-align: center;" width="80"><?php echo $row[csf("receive_date")]; ?></td>
									<td style="text-align: center;" width="80"><?php echo $batch_against[$row[csf("batch_against")]]; ?></td>
									<td style="text-align: center;" width="80"><?php echo $conversion_cost_head_array[$row[csf("process_for")]]; ?></td>
									<td style="text-align: center;"><?php echo $color_arr[$row[csf("color_id")]]; ?></td>
								</tr>
								<?php
								$total_qty += $row[csf("fin_recv_textile_qty")];
								$i++;
							}
							?>
							<tfoot>
								<tr>
									<th colspan="6" align="right">Total</th>
									<th align="right"><? echo number_format($total_qty, 2); ?></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</tbody>
					</table>
				</div>
			</div>
		</fieldset>
	</div>
	<?
	exit();
}

if($action=="deli_to_garments_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	page_style();
	extract($_REQUEST);
	?>
	<script>
		function print_window() {
			document.getElementById('scroll_body').style.overflow = "auto";
			document.getElementById('scroll_body').style.maxHeight = "none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY = "scroll";
			document.getElementById('scroll_body').style.maxHeight = "230px";
		}
	</script>
	<?
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

    $sql = "SELECT a.id, a.batch_no,a.batch_against,a.color_id,a.extention_no,a.sales_order_no,a.booking_no, a.batch_for as process_for, sum(c.issue_qnty) as issue_qnty, d.issue_date, d.issue_number
    from pro_batch_create_mst a, inv_finish_fabric_issue_dtls c, inv_issue_master d, product_details_master e  
    where a.id=c.batch_id and c.mst_id=d.id and a.color_id=$color and d.entry_form in(224,318) and c.order_id in('$order_id') and c.prod_id=e.id and e.detarmination_id=$deter_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
    and d.status_active=1 and d.is_deleted=0 
    group by a.id, a.batch_no,a.batch_against,a.color_id,a.extention_no,a.sales_order_no,a.booking_no, a.batch_for, d.issue_date, d.issue_number order by d.issue_number";
	//echo $sql;
	$result = sql_select($sql);
	?>
	<div style="width:1020px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
		style="width:100px" class="formbutton"/></div>
		<fieldset style="width:1030px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th width="50">SL No</th>
							<th width="100">FRDG. Challan</th>
							<th width="100">Batch No</th>
							<th width="50">Ext. No</th>
							<th width="100">Sales Order No</th>
							<th width="80">Booking No</th>
							<th width="80">Fin. Fab. Deliv. Gmnts</th>
							<th width="80">Recv. Date</th>
							<th width="80">Batch Against</th>
							<th width="80">Process For</th>
							<th>Color</th>
						</tr>
					</thead>
				</table>
				<div style="width:1030px; max-height:320px; overflow-y:scroll" id="scroll_body">
					<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
						<tbody>
							<?php
							$i = 1;
							foreach ($result as $row) 
							{
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
									<td style="text-align: center;" width="50"><?php echo $i; ?></td>
									<td style="text-align: center;" width="100"><?php echo $row[csf("issue_number")]; ?></td>
									<td style="text-align: center;" width="100"><?php echo $row[csf("batch_no")]; ?></td>
									<td style="text-align: center;" width="50"><?php echo $row[csf("extention_no")]; ?></td>
									<td style="text-align: center;" width="100"><p><?php echo $row[csf("sales_order_no")]; ?></p></td>
									<td style="text-align: center;" width="80"><?php echo $row[csf("booking_no")]; ?></td>
									<td width="80"><?php echo number_format($row[csf("issue_qnty")], 2); ?></td>
									<td style="text-align: center;" width="80"><?php echo $row[csf("issue_date")]; ?></td>
									<td style="text-align: center;" width="80"><?php echo $batch_against[$row[csf("batch_against")]]; ?></td>
									<td style="text-align: center;" width="80"><?php echo $conversion_cost_head_array[$row[csf("process_for")]]; ?></td>
									<td style="text-align: center;"><?php echo $color_arr[$row[csf("color_id")]]; ?></td>
								</tr>
								<?php
								$total_qty += $row[csf("issue_qnty")];
								$i++;
							}
							?>
							<tfoot>
								<tr>
									<th colspan="6" align="right">Total</th>
									<th align="right"><? echo number_format($total_qty, 2); ?></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</tbody>
					</table>
				</div>
			</div>
		</fieldset>
	</div>
	<?
	exit();
}
?>