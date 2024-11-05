<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
$supplier_short_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name");
$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
$other_party_arr=return_library_array( "select id,other_party_name from lib_other_party", "id", "other_party_name");

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));


	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$search_cond='';
	if ($txt_search_comm=="")
	{
		$search_cond.="";
	}
	else
	{
		if($cbo_search_by==1) $search_cond.=" and c.job_no_prefix_num in ($txt_search_comm) ";
		else if($cbo_search_by==2) $search_cond.=" and c.style_ref_no = '$txt_search_comm'";
		else if($cbo_search_by==3) $search_cond.=" and e.booking_no = '$txt_search_comm'";
		else if($cbo_search_by==4) $search_cond.=" and e.booking_no = '$txt_search_comm'";

		/* if($cbo_search_by==1) $search_cond.=" and c.job_no_prefix_num in ($txt_search_comm) ";
		else if($cbo_search_by==2) $search_cond.=" and c.style_ref_no = '$txt_search_comm'";
		else if($cbo_search_by==3) $search_cond.=" and e.booking_no LIKE '%$txt_search_comm%'";
		else if($cbo_search_by==4) $search_cond.=" and e.booking_no LIKE '%$txt_search_comm%'"; */
	}


    if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond_knit=" and c.buyer_name=$cbo_buyer_id";
    $year_id=str_replace("'","",$cbo_year);;
    $month_id=str_replace("'","",$cbo_month);;


    if(str_replace("'","",$cbo_issue_purpose)!="" && str_replace("'","",$cbo_issue_purpose)!=0) $issue_purpose_cond_knit=" and e.issue_purpose=$cbo_issue_purpose";

    if($year_id!=0) $year_cond_knit=" and to_char(e.insert_date,'yyyy')=$year_id"; else $year_cond_knit="";
    if($month_id!=0) $month_cond_knit=" and to_char(e.insert_date,'mm')=$month_id"; else $month_cond_knit="";

	if($cbo_search_by==4)
	{
		$sql_issue_sample_data="SELECT null as job_no, a.id,a.style_ref_no,a.buyer_name, d.brand_id as brand_id, e.company_id as company_id, e.booking_id as booking_id, e.booking_no as booking_no,
		d.supplier_id, e.issue_purpose,e.id as Issue_id,e.issue_number, d.cons_quantity as issue_qnty, d.cons_rate, f.brand,f.id as product_id,
		f.lot,f.yarn_type, f.yarn_count_id, f.yarn_comp_type1st,f.yarn_comp_percent1st,e.issue_date,e.challan_no
		FROM sample_development_mst a, wo_non_ord_samp_booking_mst b, wo_non_ord_samp_booking_dtls c,inv_transaction d,inv_issue_master e,product_details_master f
		WHERE b.id=c.booking_mst_ido and c.style_id=a.id and b.id=e.booking_id and  d.mst_id=e.id and d.prod_id=f.id and d.item_category=1 and d.transaction_type=2 and e.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.issue_basis=1 and e.issue_purpose in(8) and e.company_id=$cbo_company_id $buyer_id_cond_knit $search_cond $issue_purpose_cond_knit $year_cond_knit  $month_cond_knit group by a.id, a.style_ref_no,a.buyer_name, d.brand_id, e.company_id, e.booking_id, e.booking_no,d.supplier_id, e.issue_purpose,e.id,e.issue_number, d.cons_quantity, d.cons_rate, f.brand,f.id,f.lot,f.yarn_type, f.yarn_count_id, f.yarn_comp_type1st,f.yarn_comp_percent1st,e.issue_date,e.challan_no";

		// echo $sql_issue_sample_data;

		$result_issue_sample_data = sql_select($sql_issue_sample_data);
		$sdmIdArr = array();
		foreach ($result_issue_sample_data as $row)
		{
			if($smnbookingNoChk[$row[csf("id")]]=="")
			{
				$smnbookingNoChk[$row[csf("id")]] = $row[csf("id")];
				array_push($sdmIdArr,$row[csf("id")]);
			}
		}

		$sql_data = "SELECT a.booking_date, b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id, sum (b.cons_qnty) as cons_qnty from wo_non_ord_samp_booking_mst a,sample_development_yarn_dtls b where a.booking_no=b.booking_no and  b.status_active=1 ".where_con_using_array($sdmIdArr,0,'b.mst_id')." and b.determin_id in (select determination_id from sample_development_fabric_acc  where status_active=1 ".where_con_using_array($sdmIdArr,0,'sample_mst_id')." and form_type=1) group by a.booking_date,b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id";
		// echo $sql_data;
		$data_array=sql_select($sql_data);

		$smnbooking_info_arr = array();
		$smnbudgetSummaryArr = array();
		foreach($data_array as $row)
		{
			$smnbooking_info_arr[$row[csf("booking_no")]]["book_qnty"] += $row[csf("cons_qnty")];

			if($bookNoChk[$row[csf("booking_no")]]=="")
			{
				$bookNoChk[$row[csf("booking_no")]] = $row[csf("booking_no")];

				$smnbooking_info_arr[$row[csf("booking_no")]]["booking_date"] = $row[csf("booking_date")];
			}

			$smnbudgetSummaryArr[$row[csf("count_id")]][$row[csf("type_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]]["req_quantity"]+=$row[csf("cons_qnty")];


		}
		unset($data_array);
		//echo "<pre>";print_r($smnbooking_info_arr);


		$groupdataArr = array();
		$requiredInfoArr = array();
		$knitting_smn_book_qnty = 0;
		foreach ($result_issue_sample_data as $row)
		{
			$compPercent = $row[csf('yarn_comp_percent1st')];
			$yanrType = $row[csf('yarn_type')];
			$yarnComposition = $row[csf('yarn_comp_type1st')];
			$yarnCount = $row[csf('yarn_count_id')];
			$product_id=$row[csf('product_id')];
			$issue_id = $row[csf('issue_id')];

			$groupKey = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType;

			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_id"] = $row[csf('issue_id')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_number"] = $row[csf('issue_number')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["job_no"] = $row[csf('job_no')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["style_ref_no"] = $row[csf('style_ref_no')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["buyer_name"] = $row[csf('buyer_name')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_date"] = $row[csf('issue_date')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["challan_no"] = $row[csf('challan_no')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["lot"] = $row[csf('lot')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["brand_id"] = $row[csf('brand_id')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["supplier_id"] = $row[csf('supplier_id')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_qnty"] += $row[csf('issue_qnty')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["cons_rate"] = $row[csf('cons_rate')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["booking_no"] = $row[csf('booking_no')];

			$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["book_qty"] = $smnbooking_info_arr[$row[csf("booking_no")]]["book_qnty"];
			$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["booking_date"] = $smnbooking_info_arr[$row[csf("booking_no")]]["booking_date"];
			$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["is_short"] = 'SMN';

			$knitting_smn_book_qnty += $row[csf("issue_qnty")];
			$issue_ids .=$row[csf("issue_id")].",";

		}

		// echo "<pre>";
		// print_r($requiredInfoArr);
		// echo "</pre>";

	}



	$sql_issue_data="SELECT a.trans_id, c.id AS job_id, c.job_no, c.buyer_name AS buyer_name, c.style_ref_no  AS style_ref_no, d.brand_id AS brand_id, e.company_id  AS company_id, e.booking_id  AS booking_id, e.booking_no  AS booking_no, d.supplier_id, e.issue_purpose, e.id AS Issue_id, e.issue_number, d.cons_quantity AS issue_qnty, d.cons_rate, f.brand, f.id AS product_id, f.lot, f.yarn_type, f.yarn_count_id, f.yarn_comp_type1st, f.yarn_comp_percent1st, e.issue_date, e.challan_no, e.issue_basis,d.requisition_no FROM  inv_issue_master e, inv_transaction  d, product_details_master  f, order_wise_pro_details  a, wo_po_break_down b, wo_po_details_master c WHERE   e.id=d.mst_id and d.id= a.trans_id and d.prod_id=f.id and a.PO_BREAKDOWN_ID=b.id and b.job_id=c.id  and d.item_category = 1 and a.trans_type = 2 and e.issue_basis in (1,3) and e.issue_purpose IN (1, 2)and e.company_id = $cbo_company_id $buyer_id_cond_knit $search_cond $issue_purpose_cond_knit $year_cond_knit  $month_cond_knit";

	//echo $sql_issue_data;//die;


	$transissueIdChk = array();
	$jobArr = array();
	$bookArr = array();
	$bookingNoChk = array();
	$po_data=array();
	$groupKeyArr=array();
	$jobArr=array();
	$ydwArr = array();
	$requisitionNoArr = array();

	$s_book_qnty = 0;
	$m_book_qnty = 0;
	$knitting_book_qnty = 0;
	$yDyeing_book_qnty = 0;
	$fin_fab_qnty = 0;
	$grey_fab_qnty = 0;

    $result_issue=sql_select($sql_issue_data);

	// echo "<pre>";
	// print_r($result_issue);die;

	foreach ($result_issue as $row)
    {
		if($bookingNoChk[$row[csf("booking_no")]]=="")
		{
			$bookingNoChk[$row[csf("booking_no")]] = $row[csf("booking_no")];

			$booking_no = explode('-',$row[csf("booking_no")]);
			if($booking_no[1]=='Fb' || $booking_no[1]=='FB')
			{
				array_push($bookArr,$row[csf("booking_no")]);
			}
			else if($booking_no[1]=='YDW')
			{
				array_push($ydwArr,$row[csf("booking_no")]);
			}
			if($row[csf("issue_basis")]==3)
			{
				array_push($requisitionNoArr,$row[csf("requisition_no")]);
			}

			array_push($jobArr,$row[csf("job_id")]);
		}
	}

	//var_dump($jobArr);

	if(!empty($jobArr))
	{
		$sql_shiping_status="SELECT a.shiping_status, a.job_id, a.job_no_mst FROM wo_po_break_down a
		WHERE a.status_active=1 and a.is_deleted=0 and a.shiping_status !=3 ".where_con_using_array($jobArr,1,'a.job_id')." group by a.shiping_status, a.job_id, a.job_no_mst ";
		//echo $sql_shiping_status;
		$sql_shiping_status_rslt = sql_select($sql_shiping_status);

		$shipingStatusArr = array();
		foreach ($sql_shiping_status_rslt as $row)
		{
			$shipingStatusArr[$row[csf("job_no_mst")]]["shiping_status"] = $row[csf("shiping_status")];
		}
		unset($sql_shiping_status_rslt);

	}


	if(!empty($ydwArr))
	{
		$sql_ydw = "SELECT a.id, a.job_no, a.fab_booking_no, c.ydw_no from wo_yarn_dyeing_dtls a, wo_po_break_down b,wo_yarn_dyeing_mst c where a.job_no_id=b.job_id and a.mst_id=c.id and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($ydwArr,1,'c.ydw_no')."  group by a.id, a.job_no, a.fab_booking_no, c.ydw_no";
		//a.job_no=b.job_no_mst
		//echo $sql_ydw;die;
		$sql_ydw_rslt = sql_select($sql_ydw);
		$ydw_arr = array();
		foreach($sql_ydw_rslt as $row)
		{
			$ydw_arr[$row[csf("job_no")]][$row[csf("ydw_no")]]["fab_booking_no"] = $row[csf("fab_booking_no")];
			if($bookingNoChk[$row[csf("fab_booking_no")]]=="")
			{
				$bookingNoChk[$row[csf("fab_booking_no")]] = $row[csf("fab_booking_no")];
				array_push($bookArr,$row[csf("fab_booking_no")]);
			}

		}
		unset($sql_ydw_rslt);
		//echo "<pre>";print_r($ydw_arr);

	}

	if(!empty($requisitionNoArr))
	{
		$sql_req = "SELECT a.requisition_no, a.allocation_qnty_breakdown, b.booking_no from ppl_yarn_requisition_entry a, ppl_planning_info_entry_mst b, ppl_planning_info_entry_dtls c where a.knit_id=c.id and  b.id=c.mst_id " . where_con_using_array($requisitionNoArr,0,'a.requisition_no') . " and a.status_active=1 and a.is_deleted=0";
		//echo $sql;
		$sql_req_rslt = sql_select($sql_req);
		$req_book_info_arr = array();
		foreach($sql_req_rslt as $row)
		{
			$all_qnty_breakdown = explode('_',$row[csf("allocation_qnty_breakdown")]);

			$req_book_info_arr[$all_qnty_breakdown[2]][$row[csf("requisition_no")]]["booking_no"] = $row[csf("booking_no")];

			if($bookingNoChk[$row[csf("booking_no")]]=="")
			{
				$bookingNoChk[$row[csf("booking_no")]] = $row[csf("booking_no")];
				array_push($bookArr,$row[csf("booking_no")]);
			}

		}
		unset($sql_req_rslt);
		//echo "<pre>";print_r($req_info_arr);
	}



	//for booking qty
    //echo "<pre>";print_r($bookArr);
	if($bookArr)
	{
		$short_fab_description= sql_select("SELECT b.booking_no,b.fin_fab_qnty,b.grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id ".where_con_using_array($bookArr,1,'b.booking_no')." and b.status_active=1 and b.is_deleted=0  and b.grey_fab_qnty>0 and b.is_short=1");
		$fabric_data_arr = array();
		foreach ($short_fab_description as $row)
		{
			$fabric_data_arr[$row[csf('booking_no')]]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
			$fabric_data_arr[$row[csf('booking_no')]]['grey_fab_qnty'] += $row[csf('grey_fab_qnty')];
		}
		unset($short_fab_description);
		//echo "<pre>";print_r($fabric_data_arr);


		$main_fab_description= sql_select("SELECT d.booking_no,d.fin_fab_qnty, d.grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d WHERE a.job_id=b.job_id and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_id=a.job_id and c.id=b.color_size_table_id and b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id ".where_con_using_array($bookArr,1,'d.booking_no')." and d.status_active=1 and d.is_deleted=0  AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND d.is_short=2");

		foreach ($main_fab_description as $row)
		{
			$fabric_data_arr[$row[csf('booking_no')]]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
			$fabric_data_arr[$row[csf('booking_no')]]['grey_fab_qnty'] += $row[csf('grey_fab_qnty')];
		}
		unset($main_fab_description);
		//echo "<pre>";print_r($fabric_data_arr);

		$sql_1="SELECT a.id as job_id, a.job_no, a.buyer_name, d.id as booking_id, d.booking_no, d.booking_date, (c.grey_fab_qnty*e.cons_ratio/100) as book_qnty, d.booking_type,d.is_short, e.count_id, e.type_id, e.copm_one_id, e.copm_two_id, e.percent_one, e.percent_two from wo_po_details_master a, wo_booking_dtls c, wo_booking_mst d, wo_pre_cost_fab_yarn_cost_dtls e where a.job_no=c.job_no and c.booking_mst_id=d.id and c.pre_cost_fabric_cost_dtls_id=e.fabric_cost_dtls_id and c.job_no=e.job_no and a.company_name in (".$cbo_company_id.") and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active = 1 and e.is_deleted=0  ".where_con_using_array($bookArr,1,'d.booking_no')." ";
		//echo $sql_1; die();
		$sql_1_rslt = sql_select($sql_1);
		$booking_info_arr = array();
		$bookNoChk = array();
		$budgetSummaryArr = array();
		foreach($sql_1_rslt as $row)
		{
			$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["book_qnty"] += $row[csf("book_qnty")];
			if($row[csf("is_short")]==1)
			{
				$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["s_book_qnty"] += $row[csf("book_qnty")];
			}
			else
			{
				$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["m_book_qnty"] += $row[csf("book_qnty")];
			}
			if($bookNoChk[$row[csf("booking_no")]]=="")
			{
				$bookNoChk[$row[csf("booking_no")]] = $row[csf("booking_no")];

				$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["booking_date"] = $row[csf("booking_date")];
				$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["is_short"] = $row[csf("is_short")];
			}

			$budgetSummaryArr[$row[csf("count_id")]][$row[csf("type_id")]][$row[csf("copm_one_id")]][$row[csf("copm_two_id")]][$row[csf("percent_one")]][$row[csf("percent_two")]]["req_quantity"]+=$row[csf("book_qnty")];


		}
		unset($sql_1_rslt);
		//echo "<pre>";print_r($booking_info_arr);

		$po_data = array();
		$job_id_Chk = array();
		$all_job_id_arr = array();
		foreach ($result_issue as $row)
		{
			$compPercent = $row[csf('yarn_comp_percent1st')];
			$yanrType = $row[csf('yarn_type')];
			$yarnComposition = $row[csf('yarn_comp_type1st')];
			$yarnCount = $row[csf('yarn_count_id')];
			$product_id=$row[csf('product_id')];
			$issue_id = $row[csf('issue_id')];

			$groupKey = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType;
			if($transissueIdChk[$row[csf("trans_id")]]=="")
			{
				$transissueIdChk[$row[csf("trans_id")]] = $row[csf("trans_id")];

				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_id"] = $row[csf('issue_id')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_basis"] = $row[csf('issue_basis')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_number"] = $row[csf('issue_number')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["job_no"] = $row[csf('job_no')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["style_ref_no"] = $row[csf('style_ref_no')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["buyer_name"] = $row[csf('buyer_name')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_date"] = $row[csf('issue_date')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["challan_no"] = $row[csf('challan_no')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["lot"] = $row[csf('lot')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["brand_id"] = $row[csf('brand_id')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["supplier_id"] = $row[csf('supplier_id')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_qnty"] += $row[csf('issue_qnty')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["cons_rate"] = $row[csf('cons_rate')];



				$booking_no = explode('-',$row[csf("booking_no")]);
				if($booking_no[1]=='Fb' || $booking_no[1]=='FB')
				{
					$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["booking_no"] = $row[csf('booking_no')];
					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["book_qty"] = $booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["book_qnty"];
					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["booking_date"] = $booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["booking_date"];
					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["is_short"] = $booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["is_short"];

					$groupKeyArr[$groupKey]['required_qty'] =$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["book_qnty"];
					if($booking_noChk[$row[csf("booking_no")]]=="")
					{
						$booking_noChk[$row[csf("booking_no")]] = $row[csf("booking_no")];
						$s_book_qnty += $booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["s_book_qnty"];
						$m_book_qnty += $booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["m_book_qnty"];
					}


					if($row[csf("issue_purpose")]==1)
					{
						$knitting_book_qnty += $row[csf("issue_qnty")];
					}
					else if($row[csf("issue_purpose")]==2)
					{
						$yDyeing_book_qnty += $row[csf("issue_qnty")];
					}

					if($booking_noChk[$row[csf("booking_no")]]=="")
					{
						$booking_noChk[$row[csf("booking_no")]] = $row[csf("booking_no")];
						$fin_fab_qnty +=$fabric_data_arr[$row[csf('booking_no')]]['fin_fab_qnty'];
						$grey_fab_qnty +=$fabric_data_arr[$row[csf('booking_no')]]['grey_fab_qnty'];
					}


				}
				else if($booking_no[1]=='YDW')
				{
					$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["ydw_no"] = $row[csf('booking_no')];

					$ydwbookin_no = $ydw_arr[$row[csf("job_no")]][$row[csf('booking_no')]]["fab_booking_no"];
					$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["booking_no"] =$ydwbookin_no;

					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["book_qty"] = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["book_qnty"];
					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["booking_date"] = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["booking_date"];
					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["is_short"] = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["is_short"];

					$groupKeyArr[$groupKey]['required_qty'] = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["book_qnty"];

					if($ydwbookin_noChk[$ydwbookin_no]=="")
					{
						$ydwbookin_noChk[$ydwbookin_no] = $ydwbookin_no;
						$s_book_qnty = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["s_book_qnty"];
						$m_book_qnty = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["m_book_qnty"];
					}



					if($row[csf("issue_purpose")]==1)
					{
						$knitting_book_qnty += $row[csf("issue_qnty")];
					}
					else if($row[csf("issue_purpose")]==2)
					{
						$yDyeing_book_qnty += $row[csf("issue_qnty")];
					}

					if($booking_noChk[$ydwbookin_no]=="")
					{
						$booking_noChk[$ydwbookin_no] = $ydwbookin_no;
						$fin_fab_qnty +=$fabric_data_arr[$ydwbookin_no]['fin_fab_qnty'];
						$grey_fab_qnty +=$fabric_data_arr[$ydwbookin_no]['grey_fab_qnty'];
					}

				}

				if($row[csf("issue_basis")]==3)
				{
					$req_booking_no=$req_book_info_arr[$row[csf('job_no')]][$row[csf("requisition_no")]]["booking_no"];
					$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["booking_no"] =$req_booking_no;
					$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["requisition_no"] =$row[csf('requisition_no')];

					$requiredInfoArr[$row[csf('job_no')]][$req_booking_no]["book_qty"] = $booking_info_arr[$row[csf("job_no")]][$req_booking_no]["book_qnty"];
					$requiredInfoArr[$row[csf('job_no')]][$req_booking_no]["booking_date"] = $booking_info_arr[$row[csf("job_no")]][$req_booking_no]["booking_date"];
					$requiredInfoArr[$row[csf('job_no')]][$req_booking_no]["is_short"] = $booking_info_arr[$row[csf("job_no")]][$req_booking_no]["is_short"];

					$groupKeyArr[$groupKey]['required_qty'] = $booking_info_arr[$row[csf("job_no")]][$req_booking_no]["book_qnty"];

					if($req_booking_noChk[$req_booking_no]=="")
					{
						$req_booking_noChk[$req_booking_no] = $req_booking_no;
						$s_book_qnty = $booking_info_arr[$row[csf("job_no")]][$req_booking_no]["s_book_qnty"];
						$m_book_qnty = $booking_info_arr[$row[csf("job_no")]][$req_booking_no]["m_book_qnty"];
					}



					if($row[csf("issue_purpose")]==1)
					{
						$knitting_book_qnty += $row[csf("issue_qnty")];
					}
					else if($row[csf("issue_purpose")]==2)
					{
						$yDyeing_book_qnty += $row[csf("issue_qnty")];
					}

					if($req_booking_noChk1[$req_booking_no]=="")
					{
						$req_booking_noChk1[$req_booking_no] = $req_booking_no;
						$fin_fab_qnty +=$fabric_data_arr[$req_booking_no]['fin_fab_qnty'];
						$grey_fab_qnty +=$fabric_data_arr[$req_booking_no]['grey_fab_qnty'];
					}
				}
			}


			$issue_ids .=$row[csf("issue_id")].",";

			if($job_id_Chk[$row[csf("job_id")]]=="")
			{
				$job_id_Chk[$row[csf("job_id")]] = $row[csf("job_id")];
				array_push($all_job_id_arr,$row[csf("job_id")]);
			}


		}
		unset($result_issue);
		//echo "<pre>";print_r($requiredInfoArr);die;

		$sql_job_data="SELECT a.job_id, a.job_no_mst, a.pub_shipment_date
		FROM wo_po_break_down a
		WHERE a.is_deleted=0 and a.status_active=1 ".where_con_using_array($all_job_id_arr,0,'a.job_id')." ";
		$rslt_job_data = sql_select($sql_job_data);
		//echo $sql_job_data;
		foreach($rslt_job_data as $row)
		{
			$po_data[$row[csf('job_no_mst')]]['poshipdate'].=$row[csf('pub_shipment_date')].',';
		}

	}



	$issue_ids =chop($issue_ids,",");
	$issue_ids=implode(",",array_filter(array_unique(explode(",",$issue_ids))));

	// ================= Issue Return ===============//
	if($issue_ids!="")
	{
		$issue_ids=explode(",",$issue_ids);
		$issue_ids_chnk=array_chunk($issue_ids,999);
		$issue_no_cond=" and";
		foreach($issue_ids_chnk as $issueId)
		{
			if($issue_no_cond==" and")  $issue_no_cond.="(c.id in(".implode(',',$issueId).")"; else $issue_no_cond.=" or c.id in(".implode(',',$issueId).")";
		}
		$issue_no_cond.=")";
		//echo $issue_no_cond;die;
		// echo "SELECT a.recv_number,a.booking_no, b.cons_quantity, c.id as issue_id,b.id as trans_id,d.brand,d.id as product_id,d.lot,d.yarn_type, d.yarn_count_id, d.yarn_comp_type1st,d.yarn_comp_percent1st from inv_receive_master a, inv_transaction b, inv_issue_master c,product_details_master d where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and b.prod_id=d.id and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 $issue_no_cond";
		$issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, b.cons_quantity, b.issue_id,b.id as trans_id,d.brand,d.id as product_id,d.lot,d.yarn_type, d.yarn_count_id, d.yarn_comp_type1st,d.yarn_comp_percent1st from inv_receive_master a, inv_transaction b, inv_issue_master c,product_details_master d where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and b.prod_id=d.id and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 $issue_no_cond");
	}

	$transIdChk = array();
	foreach ($issue_return_res as $val)
	{
		if($transIdChk[$val[csf("trans_id")]]=="")
		{
			$transIdChk[$val[csf("trans_id")]] = $val[csf("trans_id")];

			$compPercent = $val[csf('yarn_comp_percent1st')];
			$yanrType = $val[csf('yarn_type')];
			$yarnComposition = $val[csf('yarn_comp_type1st')];
			$yarnCount = $val[csf('yarn_count_id')];
			$prod_id=$val[csf('product_id')];
			$issue_id=$val[csf('issue_id')];

			$groupKey = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType;
			$issue_return_qnty_arr[$issue_id][$groupKey][$val[csf('issue_id')]][$prod_id] += $val[csf("cons_quantity")];

		}
	}


	$usd_arr = array();
    $sqlSelectData = sql_select("select con_date,conversion_rate from currency_conversion_rate where currency=2 and is_deleted=0 order by con_date desc");
    foreach ($sqlSelectData as $row)
    {
        $usd_arr[date('d-m-Y',strtotime($row[csf('con_date')]))] = $row[csf('conversion_rate')];
    }


 	ob_start();
	?>
     <fieldset style="width:1350px;" >

            <table cellpadding="0" cellspacing="0" width="1330" style="float: left;">
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>"><p  style="font-size:20px;"><? echo $report_title; ?></p></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" ><p style="font-size:18px"><? echo $company_arr[str_replace("'", "", $cbo_company_id)]; ?></p></td>
                </tr>

            </table>

            <table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                <thead>
                    <th width="30" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >SL</p></th>
                    <th width="200" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Name</p></th>
                    <th width="230" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >LOT::Brand::Supplier</th>
                    <th width="150" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Required Qty<br>
					<? if($cbo_search_by==4){?> [<span >Sample</span>]<?} else {?>
					[Yarn+<span style="color: #FF0000;">Short</span>] <? } ?></p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Allocated Qty</p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Allocated Bal</p></th>
                    <th width="120" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Issued Qty</p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Issue Return Qty</p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Issue Bal</p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Unit Price <br>(USD)</p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >TTL Value <br>(USD)</p></th>
                </thead>
            </table>
            <div style="width:1350px; overflow-y: scroll; max-height:380px; float: left; margin-bottom:20px" id="scroll_body">
                <table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                    <tbody>
                        <?
                        $i=1;
						$groupKey_count = 0;
                        $g_issue_subtotal_qnty = 0;
						$g_issue_rtn_subtotal_qnty = 0;
						$g_issue_bal_subtotal_qnty = 0;
						$g_amount_subtotal_qnty = 0;
						$g_required_total_qnty = 0;
						$allocated_qnty = 0;

                        foreach ($groupdataArr as $jobKey=>$jobData)
                        {
                            foreach ($jobData as $bookingkey=>$bookingData)
                            {
                                foreach ($bookingData as $groupKey=>$groupData)
                                {
                                    foreach ($groupData as $issueKey=>$issueData)
                                    {
										foreach ($issueData as $prod_id=>$row)
										{
											if($groupKeyChk[$groupKey]=="")
											{
												$groupKeyChk[$groupKey] = $groupKey;
												$groupKey_count += count($groupKey);
											}
											if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

											$compPercent = $row['yarn_comp_percent1st'];
											$yanrType = $row['yarn_type'];
											$yarnComposition = $row['yarn_comp_type1st'];
											$yarnCount = $row['yarn_count_id'];
											$issueDate = $row['issue_date'];

											$caption = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType;


											if($row['ydw_no'])
											{
												$ydw_booking = ' ( '.$row['ydw_no'].' )';
											}
											else if($row['issue_basis']==3)
											{
												$ydw_booking = ' ( '."req no-".$row['requisition_no'].' )';
											}
											else
											{
												$ydw_booking = '';
											}

											$exchangeRate=$usd_arr[date('d-m-Y',strtotime($row['issue_date']))];
											if($exchangeRate =="")
											{
												foreach ($usd_arr as $rate_date => $rat)
												{
													if(strtotime($rate_date) <= strtotime($row['issue_date']))
													{
														$rate_date = date('d-m-Y',strtotime($rate_date));
														$exchangeRate=$rat;
														break;
													}
												}
											}
											//echo $groupKey."==".$issueId."|<br>";
											$issue_return_qnty = $issue_return_qnty_arr[ $issueKey][$groupKey][$issueKey][$prod_id];


											//echo $exchangeRate;

											//$amount = $row[csf('issue_qnty')] * $exchangeRate;
											$amount =  ( ($row['issue_qnty']-$issue_return_qnty) * number_format($row['cons_rate']/$exchangeRate, 2) );
											$requiredQty = 0;
											if($row['issue_basis']==3)
											{
												$requiredQty = $requiredInfoArr[$jobKey][$row['booking_no']]["book_qty"];
												$booking_date = $requiredInfoArr[$jobKey][$row['booking_no']]["booking_date"];
												$is_short = $requiredInfoArr[$jobKey][$row['booking_no']]["is_short"];
											}
											else
											{
												$requiredQty = $requiredInfoArr[$jobKey][$bookingkey]["book_qty"];
												$booking_date = $requiredInfoArr[$jobKey][$bookingkey]["booking_date"];
												$is_short = $requiredInfoArr[$jobKey][$bookingkey]["is_short"];
											}

											$booking_type = '';
											if ($is_short == 1)
											{
												$booking_type = 'Short';
												$txtcolor = "#FF0000";
											}
											else if ($is_short == 'SMN')
											{
												$booking_type = 'Sample';
												//$txtcolor = "#0e2ab5";
											}
											else
											{
												$booking_type = 'Main';
												$txtcolor = "#000000";
											}

											$dates=array_unique(array_filter(explode(",",$po_data[$jobKey]['poshipdate'])));

											$fs_date='';
											$ls_date='';
											foreach ($dates as $key => $val) {
												if(empty($fs_date))
												{
													$fs_date=strtotime('21-01-2025');
												}
												$curDate = strtotime($val);
												if ($curDate > $ls_date) {
													$ls_date = $curDate;
												}
												if ($curDate < $fs_date) {
													$fs_date = $curDate;
												}

											}

											if($cbo_search_by !=4)
											{
												$shiping_status = $shipingStatusArr[$row['job_no']]["shiping_status"];

												if($shiping_status)
												{
													$shiping_sts = '::Ship Date:F=( '.change_date_format(date('d-M-y',$fs_date)).' ), L=( '.change_date_format(date('d-M-y',$ls_date)).' )'.'::  Knit Closed:'.'<span style="color:red">No</span>'.'::  Grey Closed:'.'<span style="color:red">No</span>::Job Closed:<span style="color:red">No</span>';
												}
												else
												{
													$shiping_sts = '::Ship Date:F=( '.change_date_format(date('d-M-y',$fs_date)).' ), L=( '.change_date_format(date('d-M-y',$ls_date)).' )'.'::  Knit Closed:'.'<span style="color:red">No</span>'.'::  Grey Closed:'.'<span style="color:red">No</span>::Job Closed:<span style="color:green">Yes</span>';
												}
											}



											if(!in_array($jobKey."**".$bookingkey."**".$groupKey,$yarnDescriptionArr))
											{

												if($i!=1)
												{
													?>
													<tr bgcolor="#CCCCCC">
														<td colspan="3" align="right"><b>Sub Total : </b></td>
														<td align="right"><b><?php echo number_format($required_subtotal_qnty,2,'.',''); ?></b></td>
														<td align="right">&nbsp;</td>
														<td align="right">&nbsp;</td>
														<td align="right"><b><?php echo number_format($issue_subtotal_qnty,2,'.',''); ?></b></td>

														<td align="right"><b><?php echo number_format($issue_rtn_subtotal_qnty,2,'.',''); ?></b></td>
														<td align="right">
															<b>
																<?php
																$issue_bal_subtotal_qnty = ($required_subtotal_qnty-$issue_subtotal_qnty)+$issue_rtn_subtotal_qnty;
																echo number_format($issue_bal_subtotal_qnty,2,'.','');
																?>
															</b>
														</td>
														<td>&nbsp;</td>
														<td align="right"><b><?php echo number_format($amount_subtotal_qnty,2,'.',''); ?></b></td>
													</tr>
													<?
													$required_subtotal_qnty=0;
													$issue_subtotal_qnty=0;
													$issue_rtn_subtotal_qnty = 0;
													$issue_bal_subtotal_qnty = 0;
													$amount_subtotal_qnty = 0;
												}

												$yarnDescriptionArr[$i]=$jobKey."**".$bookingkey."**".$groupKey;


												?>
												<tr>
													<td rowspan="2">&nbsp;</td>
													<td colspan="10" bgcolor="#EEEEEE" title="<? echo $ydw_booking;?>">
														<b>
														<?php echo 'JOB :'.$row['job_no'].'::Style :'.$row['style_ref_no'].'::Buyer :'.$buyer_arr[$row['buyer_name']].'::Booking :'.$row['booking_no'].$ydw_booking.'::Fab Req Date:'.change_date_format($booking_date).$shiping_sts; ?>
														</b>
													</td>
												</tr>

												<tr>

													<td colspan="2" bgcolor="#EEEEEE">
														<b>
														<?php
														//var_dump($yarnDescriptionArr[$i]);
														$captionData = explode("**",$yarnDescriptionArr[$i]);
															echo $count_arr[$captionData[2]] . " ". $composition[$captionData[3]] . " ". $captionData[4] . "% ". $yarn_type[$captionData[5]];
															?>
														</b>
													</td>
													<td  bgcolor="#EEEEEE" align="right" title="<? echo $booking_type;?>">
														<p style="color:<? echo $txtcolor?>">
														 <? echo number_format($requiredQty,2); $required_subtotal_qnty += $requiredQty;

														 if($bookingKeyChk[$row['booking_no']]=="")
														 {
															$bookingKeyChk[$row['booking_no']] = $row['booking_no'];
															$g_required_total_qnty += $requiredQty;
														 }
														 ?>
														</p>

													</td>
													<td colspan="7" bgcolor="#EEEEEE"></td>
												</tr>
												<?
											}


											?>

											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">
												<td width="30" align="center" ><? echo $i; ?></td>
												<td width="200"  style="word-break: break-all;" title="<? echo 'Issue Number : '.$row['issue_number'];?>"><? echo 'CH NO: '.$row['challan_no'].'::'.change_date_format($row['issue_date']); ?>&nbsp;</td>
												<td width="230" style="word-break: break-all;" ><? echo 'Lot: '.$row['lot'].'::Brand: '.$brand_arr[$row['brand_id']].'::Supplier: '.$supplier_short_arr[$row['supplier_id']]; ?></td>
												<td width="150" style="word-break: break-all;"align="center" ></td>

												<td width="100" style="word-break: break-all;" >&nbsp;</td>
												<td width="100" style="word-break: break-all;" >&nbsp;</td>
												<td width="120" style="word-break: break-all;" align="right"><? echo number_format($row['issue_qnty'],2); ?></td>
												<td width="100" style="word-break: break-all;" align="right"><? echo number_format($issue_return_qnty,2); ?></td>
												<td width="100" style="word-break: break-all; " title='(Required Qty-Issued Qty+Issue Return Qty)' align="right">
												<?

												$issue_bal = ($requiredQty-$row['issue_qnty'])+$issue_return_qnty;
												echo number_format($issue_bal,2);
												?>
												</td>
												<td width="100" style="word-break: break-all; " align="right"><? echo number_format($row['cons_rate']/$exchangeRate, 2); ?></td>
												<td width="100" style="word-break: break-all; " align="right" title="<? echo $row['cons_rate'].'='.$exchangeRate;?>"><? echo number_format($amount,2); ?></td>

											</tr>
											<?

   											$issue_subtotal_qnty += $row['issue_qnty'];
											$issue_rtn_subtotal_qnty += $issue_return_qnty;
											//$issue_bal_subtotal_qnty += $issue_bal;
											$amount_subtotal_qnty += $amount;

											$g_issue_subtotal_qnty += $row['issue_qnty'];
											$g_issue_rtn_subtotal_qnty += $issue_return_qnty;
											//$g_issue_bal_subtotal_qnty += $issue_bal;
											$g_amount_subtotal_qnty += $amount;

											$i++;
										}
                                    }
                                }
                            }
                        }
                        ?>
						<tr bgcolor="#CCCCCC">
							<td colspan="2" align="left"><b>TTL Yarn - ( <? echo $groupKey_count;?> ) </b></td>
							<td align="right"><b>Sub Total : </b></td>
							<td align="right"><b><?php echo number_format($required_subtotal_qnty,2,'.',''); ?></b></td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right"><b><?php echo number_format($issue_subtotal_qnty,2,'.',''); ?></b></td>

							<td align="right"><b><?php echo number_format($issue_rtn_subtotal_qnty,2,'.',''); ?></b></td>
							<td align="right">
								<b>
									<?php
									$issue_bal_subtotal_qnty = ($required_subtotal_qnty-$issue_subtotal_qnty)+$issue_rtn_subtotal_qnty;
									echo number_format($issue_bal_subtotal_qnty,2,'.','');

									?>
							</b></td>
							<td>&nbsp;</td>
							<td align="right"><b><?php echo number_format($amount_subtotal_qnty,2,'.',''); ?></b></td>
						</tr>

						<tr bgcolor="#CCCCCC">
							<td colspan="3" align="right"><b>Grand Total : </b></td>
							<td align="right"><b><?php echo number_format($g_required_total_qnty,2,'.',''); ?></b></td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right"><b><?php echo number_format($g_issue_subtotal_qnty,2,'.',''); ?></b></td>

							<td align="right"><b><?php echo number_format($g_issue_rtn_subtotal_qnty,2,'.',''); ?></b></td>
							<td align="right"><b><?php echo number_format(($g_required_total_qnty-$g_issue_subtotal_qnty)+$g_issue_rtn_subtotal_qnty,2,'.',''); ?></b></td>
							<td>&nbsp;</td>
							<td align="right"><b><?php echo number_format($g_amount_subtotal_qnty,2,'.',''); ?></b></td>
						</tr>

                </table>
            </div>
			<br><br>


			<div style="margin-top: 20px;padding-top: 20px;">

				<table cellpadding="0" width="400" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th colspan="2"><p style="font-size:18px; font-weight:normal" >Summary</p></th>
						</tr>
						<tr>
                   			<th width="300" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Particulars</p></th>
                   			<th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Total Qnty</p></th>
						</tr>
					</thead>
					<tbody>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Required Fab Booking</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($m_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Required Short Fab</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($s_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Allocation</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;" >Total Yarn Received</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 16px tahoma; font-weight:bold">Total Yarn Received Balance</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Issued To Knitting</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($knitting_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Issued To Sample Knitting</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($knitting_smn_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Issued To Yarn Dyeing</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($yDyeing_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma; font-weight:bold">Total Yarn Issue Balance</td>
							<td align="right" style="font: 14px tahoma; font-weight:bold"><? echo number_format($knitting_book_qnty+$yDyeing_book_qnty+$knitting_smn_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Grey Fabric Required</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($grey_fab_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Grey Fabric Received</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma; font-weight:bold">Total Grey Fabric Rcvd Balance</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Grey Fabric Issued To Dye</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma; font-weight:bold">Total Grey Fabric Issue Balance</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Finish Fabric Required</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($fin_fab_qnty,2);?> &nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Finish Fabric Received</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma; font-weight:bold">Total Finish Fabric Received Balance</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Finish Fabric Issued To Cut</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Finish Fabric Stock In Hand</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
					</tbody>
				</table>


				<table cellpadding="0" width="3" cellspacing="0" align="left" rules="all" border="1">
					<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
				</table>

				<? if($cbo_search_by==4){ ?>
				<table cellpadding="0" width="500" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th colspan="5" ><p  style="font-size:18px; font-weight:normal" > Requirement as per Budget</p></th>
						</tr>
						<tr>
							<th width="30" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >SL</p></th>
                   			<th width="70" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Count</p></th>
                   			<th width="200" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Composition</p></th>
                   			<th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Type</p></th>
                   			<th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Required</p></th>
						</tr>
					</thead>
					<tbody>
						<?
						$i=1;
						$y_count=0;
						$total_yarn_require_qty = 0;
						foreach ($smnbudgetSummaryArr as $k_count => $v_count)
						{
							foreach ($v_count as $k_type => $v_type)
							{
								foreach ($v_type as $k_copm_one => $v_copm_one)
								{
										foreach ($v_copm_one as $k_percent_one => $data)
										{
											$y_count +=count($k_count);
											$total_yarn_require_qty += $data['req_quantity'];
											$compos = $composition[$k_copm_one]." ".$k_percent_one." %";
											?>
											<tr bgcolor="<? echo "#FFFFFF"; ?>">
												<td><? echo $i;?></td>
												<td><? echo $count_arr[$k_count];?></td>
												<td><? echo $compos;?></td>
												<td><? echo $yarn_type[$k_type];?></td>
												<td align="right"><? echo number_format($data['req_quantity'],2,'.','');?></td>
											</tr>
											<?
											$i++;
										}
								}
							}


						}
						?>

					</tbody>
					<tfoot>
						<tr bgcolor="#CCCCCC">
							<td colspan="4" align="right"><b>TTL Yarn - ( <? echo $y_count;?> ) :</b></td>
							<td align="right"><b><? echo number_format($total_yarn_require_qty,2)?></b></td>
						</tr>
					</tfoot>
				</table>
				<? }else {?>
				<table cellpadding="0" width="500" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th colspan="5" ><p  style="font-size:18px; font-weight:normal" >Requirement as per Budget</p></th>
						</tr>
						<tr>
							<th width="30" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >SL</p></th>
                   			<th width="70" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Count</p></th>
                   			<th width="200" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Composition</p></th>
                   			<th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Type</p></th>
                   			<th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Required</p></th>
						</tr>
					</thead>
					<tbody>
						<?
						$i=1;
						$y_count=0;
						$total_yarn_require_qty = 0;
						foreach ($budgetSummaryArr as $k_count => $v_count)
						{
							foreach ($v_count as $k_type => $v_type)
							{
								foreach ($v_type as $k_copm_one => $v_copm_one)
								{
									foreach ($v_copm_one as $k_copm_two => $v_copm_two)
									{
										foreach ($v_copm_two as $k_percent_one => $v_percent_one)
										{
											foreach ($v_percent_one as $k_percent_two => $data)
											{
												$y_count +=count($k_count);
												$total_yarn_require_qty += $data['req_quantity'];
												$compos = $composition[$k_copm_one]." ".$k_percent_one." %"." ".$composition[$k_copm_two];
												?>
												<tr bgcolor="<? echo "#FFFFFF"; ?>">
													<td><? echo $i;?></td>
													<td><? echo $count_arr[$k_count];?></td>
													<td><? echo $compos;?></td>
													<td><? echo $yarn_type[$k_type];?></td>
													<td align="right"><? echo number_format($data['req_quantity'],2,'.','');?></td>
												</tr>
												<?
												$i++;
											}
										}
									}
								}
							}


						}
						?>

					</tbody>
					<tfoot>
						<tr bgcolor="#CCCCCC">
							<td colspan="4" align="right"><b>TTL Yarn - ( <? echo $y_count;?> ) :</b></td>
							<td align="right"><b><? echo number_format($total_yarn_require_qty,2)?></b></td>
						</tr>
					</tfoot>
				</table>
				<? } ?>
			</div>
        </fieldset>
	<?
    foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));


	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$search_cond='';
	if ($txt_search_comm=="")
	{
		$search_cond.="";
	}
	else
	{
		if($cbo_search_by==1) $search_cond.=" and c.job_no_prefix_num in ($txt_search_comm) ";
		else if($cbo_search_by==2) $search_cond.=" and c.style_ref_no = '$txt_search_comm'";
		else if($cbo_search_by==3) $search_cond.=" and e.booking_no = '$txt_search_comm'";
		else if($cbo_search_by==4) $search_cond.=" and e.booking_no = '$txt_search_comm'";

		/* if($cbo_search_by==1) $search_cond.=" and c.job_no_prefix_num in ($txt_search_comm) ";
		else if($cbo_search_by==2) $search_cond.=" and c.style_ref_no = '$txt_search_comm'";
		else if($cbo_search_by==3) $search_cond.=" and e.booking_no LIKE '%$txt_search_comm%'";
		else if($cbo_search_by==4) $search_cond.=" and e.booking_no LIKE '%$txt_search_comm%'"; */
	}


    if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond_knit=" and c.buyer_name=$cbo_buyer_id";
    $year_id=str_replace("'","",$cbo_year);;
    $month_id=str_replace("'","",$cbo_month);;


    if(str_replace("'","",$cbo_issue_purpose)!="" && str_replace("'","",$cbo_issue_purpose)!=0) $issue_purpose_cond_knit=" and e.issue_purpose=$cbo_issue_purpose";

    if($year_id!=0) $year_cond_knit=" and to_char(e.insert_date,'yyyy')=$year_id"; else $year_cond_knit="";
    if($month_id!=0) $month_cond_knit=" and to_char(e.insert_date,'mm')=$month_id"; else $month_cond_knit="";

	if($cbo_search_by==4)
	{
		$sql_issue_sample_data="SELECT null as job_no, a.id,a.style_ref_no,a.buyer_name, d.brand_id as brand_id, e.company_id as company_id, e.booking_id as booking_id, e.booking_no as booking_no,
		d.supplier_id, e.issue_purpose,e.id as Issue_id,e.issue_number, d.cons_quantity as issue_qnty, d.cons_rate, f.brand,f.id as product_id,
		f.lot,f.yarn_type, f.yarn_count_id, f.yarn_comp_type1st,f.yarn_comp_percent1st,e.issue_date,e.challan_no
		FROM sample_development_mst a, wo_non_ord_samp_booking_mst b, wo_non_ord_samp_booking_dtls c,inv_transaction d,inv_issue_master e,product_details_master f
		WHERE b.id=c.booking_mst_ido and c.style_id=a.id and b.id=e.booking_id and  d.mst_id=e.id and d.prod_id=f.id and d.item_category=1 and d.transaction_type=2 and e.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.issue_basis=1 and e.issue_purpose in(8) and e.company_id=$cbo_company_id $buyer_id_cond_knit $search_cond $issue_purpose_cond_knit $year_cond_knit  $month_cond_knit group by a.id, a.style_ref_no,a.buyer_name, d.brand_id, e.company_id, e.booking_id, e.booking_no,d.supplier_id, e.issue_purpose,e.id,e.issue_number, d.cons_quantity, d.cons_rate, f.brand,f.id,f.lot,f.yarn_type, f.yarn_count_id, f.yarn_comp_type1st,f.yarn_comp_percent1st,e.issue_date,e.challan_no";

		// echo $sql_issue_sample_data;

		$result_issue_sample_data = sql_select($sql_issue_sample_data);
		$sdmIdArr = array();
		foreach ($result_issue_sample_data as $row)
		{
			if($smnbookingNoChk[$row[csf("id")]]=="")
			{
				$smnbookingNoChk[$row[csf("id")]] = $row[csf("id")];
				array_push($sdmIdArr,$row[csf("id")]);
			}
		}

		$sql_data = "SELECT a.booking_date, b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id, sum (b.cons_qnty) as cons_qnty from wo_non_ord_samp_booking_mst a,sample_development_yarn_dtls b where a.booking_no=b.booking_no and  b.status_active=1 ".where_con_using_array($sdmIdArr,0,'b.mst_id')." and b.determin_id in (select determination_id from sample_development_fabric_acc  where status_active=1 ".where_con_using_array($sdmIdArr,0,'sample_mst_id')." and form_type=1) group by a.booking_date,b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id";
		// echo $sql_data;
		$data_array=sql_select($sql_data);

		$smnbooking_info_arr = array();
		$smnbudgetSummaryArr = array();
		foreach($data_array as $row)
		{
			$smnbooking_info_arr[$row[csf("booking_no")]]["book_qnty"] += $row[csf("cons_qnty")];

			if($bookNoChk[$row[csf("booking_no")]]=="")
			{
				$bookNoChk[$row[csf("booking_no")]] = $row[csf("booking_no")];

				$smnbooking_info_arr[$row[csf("booking_no")]]["booking_date"] = $row[csf("booking_date")];
			}

			$smnbudgetSummaryArr[$row[csf("count_id")]][$row[csf("type_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]]["req_quantity"]+=$row[csf("cons_qnty")];


		}
		unset($data_array);
		//echo "<pre>";print_r($smnbooking_info_arr);


		$groupdataArr = array();
		$requiredInfoArr = array();
		$knitting_smn_book_qnty = 0;
		foreach ($result_issue_sample_data as $row)
		{
			$compPercent = $row[csf('yarn_comp_percent1st')];
			$yanrType = $row[csf('yarn_type')];
			$yarnComposition = $row[csf('yarn_comp_type1st')];
			$yarnCount = $row[csf('yarn_count_id')];
			$product_id=$row[csf('product_id')];
			$issue_id = $row[csf('issue_id')];

			$groupKey = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType;

			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_id"] = $row[csf('issue_id')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_number"] = $row[csf('issue_number')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["job_no"] = $row[csf('job_no')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["style_ref_no"] = $row[csf('style_ref_no')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["buyer_name"] = $row[csf('buyer_name')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_date"] = $row[csf('issue_date')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["challan_no"] = $row[csf('challan_no')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["lot"] = $row[csf('lot')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["brand_id"] = $row[csf('brand_id')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["supplier_id"] = $row[csf('supplier_id')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_qnty"] += $row[csf('issue_qnty')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["cons_rate"] = $row[csf('cons_rate')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["booking_no"] = $row[csf('booking_no')];

			$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["book_qty"] = $smnbooking_info_arr[$row[csf("booking_no")]]["book_qnty"];
			$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["booking_date"] = $smnbooking_info_arr[$row[csf("booking_no")]]["booking_date"];
			$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["is_short"] = 'SMN';

			$knitting_smn_book_qnty += $row[csf("issue_qnty")];
			$issue_ids .=$row[csf("issue_id")].",";

		}

		// echo "<pre>";
		// print_r($requiredInfoArr);
		// echo "</pre>";

	}



	$sql_issue_data="SELECT a.trans_id, c.id AS job_id, c.job_no, c.buyer_name AS buyer_name, c.style_ref_no  AS style_ref_no, d.brand_id AS brand_id, e.company_id  AS company_id, e.booking_id  AS booking_id, e.booking_no  AS booking_no, d.supplier_id, e.issue_purpose, e.id AS Issue_id, e.issue_number, d.cons_quantity AS issue_qnty, d.cons_rate, f.brand, f.id AS product_id, f.lot, f.yarn_type, f.yarn_count_id, f.yarn_comp_type1st, f.yarn_comp_percent1st, e.issue_date, e.challan_no, e.issue_basis,d.requisition_no FROM  inv_issue_master e, inv_transaction  d, product_details_master  f, order_wise_pro_details  a, wo_po_break_down b, wo_po_details_master c WHERE   e.id=d.mst_id and d.id= a.trans_id and d.prod_id=f.id and a.PO_BREAKDOWN_ID=b.id and b.job_id=c.id  and d.item_category = 1 and a.trans_type = 2 and e.issue_basis in (1,3) and e.issue_purpose IN (1, 2) and e.company_id = $cbo_company_id $buyer_id_cond_knit $search_cond $issue_purpose_cond_knit $year_cond_knit  $month_cond_knit";

	//echo $sql_issue_data;//die;


	$transissueIdChk = array();
	$jobArr = array();
	$bookArr = array();
	$bookingNoChk = array();
	$po_data=array();
	$groupKeyArr=array();
	$jobArr=array();
	$ydwArr = array();
	$requisitionNoArr = array();

	$s_book_qnty = 0;
	$m_book_qnty = 0;
	$knitting_book_qnty = 0;
	$yDyeing_book_qnty = 0;
	$fin_fab_qnty = 0;
	$grey_fab_qnty = 0;

    $result_issue=sql_select($sql_issue_data);

	// echo "<pre>";
	// print_r($result_issue);die;

	foreach ($result_issue as $row)
    {
		if($bookingNoChk[$row[csf("booking_no")]]=="")
		{
			$bookingNoChk[$row[csf("booking_no")]] = $row[csf("booking_no")];

			$booking_no = explode('-',$row[csf("booking_no")]);
			if($booking_no[1]=='Fb' || $booking_no[1]=='FB')
			{
				array_push($bookArr,$row[csf("booking_no")]);
			}
			else if($booking_no[1]=='YDW')
			{
				array_push($ydwArr,$row[csf("booking_no")]);
			}
			if($row[csf("issue_basis")]==3)
			{
				array_push($requisitionNoArr,$row[csf("requisition_no")]);
			}

			array_push($jobArr,$row[csf("job_id")]);
		}
	}

	//var_dump($jobArr);

	if(!empty($jobArr))
	{
		$sql_shiping_status="SELECT a.shiping_status, a.job_id, a.job_no_mst FROM wo_po_break_down a
		WHERE a.status_active=1 and a.is_deleted=0 and a.shiping_status !=3 ".where_con_using_array($jobArr,1,'a.job_id')." group by a.shiping_status, a.job_id, a.job_no_mst ";
		//echo $sql_shiping_status;
		$sql_shiping_status_rslt = sql_select($sql_shiping_status);

		$shipingStatusArr = array();
		foreach ($sql_shiping_status_rslt as $row)
		{
			$shipingStatusArr[$row[csf("job_no_mst")]]["shiping_status"] = $row[csf("shiping_status")];
		}
		unset($sql_shiping_status_rslt);
		//var_dump($shipingStatusArr);

		$allocation_yarn_sql ="SELECT a.id,a.job_no,a.booking_no,a.po_break_down_id,a.item_id as prod_id,a.qnty,a.is_dyied_yarn from inv_material_allocation_mst a, wo_po_details_master b, wo_booking_mst d where a.job_no=b.job_no and a.booking_no=d.booking_no ".where_con_using_array($jobArr,0,'b.id')." and a.item_category=1 and a.qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";

		//echo $sql_dyied_yarn_sql;

		$allocation_yarn_sql_rslt = sql_select($allocation_yarn_sql);
		$alloInfoArr = array();
		foreach ($allocation_yarn_sql_rslt as $row)
		{
			$alloInfoArr[$row[csf("job_no")]][$row[csf("booking_no")]][$row[csf("prod_id")]]["allocated_qnty"] += $row[csf("qnty")];
		}
		unset($allocation_yarn_sql_rslt);
		//echo "<pre>";print_r($alloInfoArr);

	}


	if(!empty($ydwArr))
	{
		$sql_ydw = "SELECT a.id, a.job_no, a.fab_booking_no, c.ydw_no from wo_yarn_dyeing_dtls a, wo_po_break_down b,wo_yarn_dyeing_mst c where a.job_no_id=b.job_id and a.mst_id=c.id and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($ydwArr,1,'c.ydw_no')."  group by a.id, a.job_no, a.fab_booking_no, c.ydw_no";
		//a.job_no=b.job_no_mst
		//echo $sql_ydw;die;
		$sql_ydw_rslt = sql_select($sql_ydw);
		$ydw_arr = array();
		foreach($sql_ydw_rslt as $row)
		{
			$ydw_arr[$row[csf("job_no")]][$row[csf("ydw_no")]]["fab_booking_no"] = $row[csf("fab_booking_no")];
			if($bookingNoChk[$row[csf("fab_booking_no")]]=="")
			{
				$bookingNoChk[$row[csf("fab_booking_no")]] = $row[csf("fab_booking_no")];
				array_push($bookArr,$row[csf("fab_booking_no")]);
			}

		}
		unset($sql_ydw_rslt);
		//echo "<pre>";print_r($ydw_arr);

	}

	if(!empty($requisitionNoArr))
	{
		$sql_req = "SELECT a.requisition_no, b.booking_no, d.po_id, e.job_no_mst,c.id as program_no, d.program_qnty
		FROM ppl_yarn_requisition_entry  a,
			ppl_planning_info_entry_mst b,
			ppl_planning_info_entry_dtls c,
			ppl_planning_entry_plan_dtls d,
			wo_po_break_down e
		WHERE  a.knit_id = c.id
			AND b.id = c.mst_id
			and a.knit_id = d.dtls_id
			and c.id=d.dtls_id
			and d.po_id=e.id
			AND a.status_active = 1
			AND a.is_deleted = 0
			AND b.status_active = 1
			AND b.is_deleted = 0
			AND c.status_active = 1
			AND c.is_deleted = 0
			AND d.status_active = 1
			AND d.is_deleted = 0
			AND e.status_active = 1
			AND e.is_deleted = 0
			" . where_con_using_array($requisitionNoArr,0,'a.requisition_no') . "
			group by a.requisition_no, b.booking_no, d.po_id, e.job_no_mst, c.id, d.program_qnty";
		//echo $sql_req;
		$sql_req_rslt = sql_select($sql_req);
		$req_book_info_arr = array();
		$program_info_arr = array();
		$programNoArr = array();
		$programNoChk = array();
		foreach($sql_req_rslt as $row)
		{
			$req_book_info_arr[$row[csf("job_no_mst")]][$row[csf("requisition_no")]]["booking_no"] = $row[csf("booking_no")];
			$program_info_arr[$row[csf("job_no_mst")]][$row[csf("booking_no")]]["program_qnty"] += $row[csf("program_qnty")];
			$program_info_arr[$row[csf("job_no_mst")]][$row[csf("booking_no")]]["program_no"] .= $row[csf("program_no")].',';

			if($bookingNoChk[$row[csf("booking_no")]]=="")
			{
				$bookingNoChk[$row[csf("booking_no")]] = $row[csf("booking_no")];
				array_push($bookArr,$row[csf("booking_no")]);
			}

			if($programNoChk[$row[csf("program_no")]]=="")
			{
				$programNoChk[$row[csf("program_no")]] = $row[csf("program_no")];
				array_push($programNoArr,$row[csf("program_no")]);
			}

		}
		unset($sql_req_rslt);
		//echo "<pre>";print_r($program_info_arr);
	}

	if(!empty($programNoArr))
	{
		$sql_production="SELECT a.id, a.booking_id as prog_id, b.grey_receive_qnty as prod_qty  from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.entry_form=2 and a.receive_basis=2 ".where_con_using_array($programNoArr,0,'a.booking_id')." ";
		//echo $sql_production;
		$sql_prod_rslt = sql_select($sql_production);
		$production_data_arr = array();
		foreach ($sql_prod_rslt as $data)
		{
		   $production_data_arr[$data[csf("prog_id")]]["tot_prod_qty"] +=$data[csf("prod_qty")];
		}
		unset($sql_prod_rslt);
	}




	//for booking qty
    //echo "<pre>";print_r($bookArr);
	if($bookArr)
	{
		// $sql_wo = "SELECT b.po_break_down_id, a.id, a.booking_no, a.booking_no_prefix_num, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ".where_con_using_array($bookArr,1,'a.booking_no')." group by b.po_break_down_id, a.id, a.booking_no, a.booking_no_prefix_num, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id";

		$sql_fabDesc = "SELECT b.po_break_down_id, b.fin_fab_qnty, b.grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ".where_con_using_array($bookArr,1,'a.booking_no')."";
		//echo $sql_fabDesc;
		$fabric_data_arr = array();
        $rslt_fabDesc = sql_select($sql_fabDesc);
        foreach ($rslt_fabDesc as $row)
        {
			$fabric_data_arr[$row[csf('po_break_down_id')]]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
			$fabric_data_arr[$row[csf('po_break_down_id')]]['grey_fab_qnty'] += $row[csf('grey_fab_qnty')];
		}
		unset($rslt_fabDesc);
		//echo "<pre>";print_r($fabric_data_arr);


		/* $short_fab_description= sql_select("SELECT b.booking_no,b.fin_fab_qnty,b.grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id ".where_con_using_array($bookArr,1,'b.booking_no')." and b.status_active=1 and b.is_deleted=0  and b.grey_fab_qnty>0 and b.is_short=1");
		$fabric_data_arr = array();
		foreach ($short_fab_description as $row)
		{
			$fabric_data_arr[$row[csf('booking_no')]]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
			$fabric_data_arr[$row[csf('booking_no')]]['grey_fab_qnty'] += $row[csf('grey_fab_qnty')];
		}
		unset($short_fab_description); */
		//echo "<pre>";print_r($fabric_data_arr);

		/* $main_fab_description= sql_select("SELECT d.booking_no,d.fin_fab_qnty, d.grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d WHERE a.job_id=b.job_id and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_id=a.job_id and c.id=b.color_size_table_id and b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id ".where_con_using_array($bookArr,1,'d.booking_no')." and d.status_active=1 and d.is_deleted=0  AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND d.is_short=2");

		foreach ($main_fab_description as $row)
		{
			$fabric_data_arr[$row[csf('booking_no')]]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
			$fabric_data_arr[$row[csf('booking_no')]]['grey_fab_qnty'] += $row[csf('grey_fab_qnty')];
		}
		unset($main_fab_description); */
		//echo "<pre>";print_r($fabric_data_arr);

		$sql_1="SELECT a.id as job_id, a.job_no, a.buyer_name, d.id as booking_id, d.booking_no, d.booking_date, (c.grey_fab_qnty*e.cons_ratio/100) as book_qnty, d.booking_type,d.is_short, e.count_id, e.type_id, e.copm_one_id, e.copm_two_id, e.percent_one, e.percent_two from wo_po_details_master a, wo_booking_dtls c, wo_booking_mst d, wo_pre_cost_fab_yarn_cost_dtls e where a.job_no=c.job_no and c.booking_mst_id=d.id and c.pre_cost_fabric_cost_dtls_id=e.fabric_cost_dtls_id and c.job_no=e.job_no and a.company_name in (".$cbo_company_id.") and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active = 1 and e.is_deleted=0  ".where_con_using_array($bookArr,1,'d.booking_no')." ";
		//echo $sql_1; die();
		$sql_1_rslt = sql_select($sql_1);
		$booking_info_arr = array();
		$bookNoChk = array();
		$budgetSummaryArr = array();
		foreach($sql_1_rslt as $row)
		{
			$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["book_qnty"] += $row[csf("book_qnty")];
			if($row[csf("is_short")]==1)
			{
				$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["s_book_qnty"] += $row[csf("book_qnty")];
			}
			else
			{
				$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["m_book_qnty"] += $row[csf("book_qnty")];
			}
			if($bookNoChk[$row[csf("booking_no")]]=="")
			{
				$bookNoChk[$row[csf("booking_no")]] = $row[csf("booking_no")];

				$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["booking_date"] = $row[csf("booking_date")];
				$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["is_short"] = $row[csf("is_short")];
			}

			$budgetSummaryArr[$row[csf("count_id")]][$row[csf("type_id")]][$row[csf("copm_one_id")]][$row[csf("copm_two_id")]][$row[csf("percent_one")]][$row[csf("percent_two")]]["req_quantity"]+=$row[csf("book_qnty")];


		}
		unset($sql_1_rslt);
		//echo "<pre>";print_r($booking_info_arr);

		$po_data = array();
		$job_id_Chk = array();
		$all_job_id_arr = array();
		$allBookingNoChk = array();
		$all_booking_no_arr = array();
		foreach ($result_issue as $row)
		{
			$compPercent = $row[csf('yarn_comp_percent1st')];
			$yanrType = $row[csf('yarn_type')];
			$yarnComposition = $row[csf('yarn_comp_type1st')];
			$yarnCount = $row[csf('yarn_count_id')];
			$product_id=$row[csf('product_id')];
			$issue_id = $row[csf('issue_id')];

			$groupKey = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType;
			if($transissueIdChk[$row[csf("trans_id")]]=="")
			{
				$transissueIdChk[$row[csf("trans_id")]] = $row[csf("trans_id")];

				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_id"] = $row[csf('issue_id')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_basis"] = $row[csf('issue_basis')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_number"] = $row[csf('issue_number')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["job_no"] = $row[csf('job_no')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["style_ref_no"] = $row[csf('style_ref_no')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["buyer_name"] = $row[csf('buyer_name')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_date"] = $row[csf('issue_date')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["challan_no"] = $row[csf('challan_no')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["lot"] = $row[csf('lot')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["brand_id"] = $row[csf('brand_id')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["supplier_id"] = $row[csf('supplier_id')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_qnty"] += $row[csf('issue_qnty')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["cons_rate"] = $row[csf('cons_rate')];



				$booking_no = explode('-',$row[csf("booking_no")]);
				if($booking_no[1]=='Fb' || $booking_no[1]=='FB')
				{
					$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["booking_no"] = $row[csf('booking_no')];
					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["book_qty"] = $booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["book_qnty"];
					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["booking_date"] = $booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["booking_date"];
					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["is_short"] = $booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["is_short"];

					$groupKeyArr[$groupKey]['required_qty'] =$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["book_qnty"];
					if($booking_noChk[$row[csf("booking_no")]]=="")
					{
						$booking_noChk[$row[csf("booking_no")]] = $row[csf("booking_no")];
						$s_book_qnty += $booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["s_book_qnty"];
						$m_book_qnty += $booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["m_book_qnty"];
					}


					if($row[csf("issue_purpose")]==1)
					{
						$knitting_book_qnty += $row[csf("issue_qnty")];
					}
					else if($row[csf("issue_purpose")]==2)
					{
						$yDyeing_book_qnty += $row[csf("issue_qnty")];
					}

					// if($booking_noChk[$row[csf("booking_no")]]=="")
					// {
					// 	$booking_noChk[$row[csf("booking_no")]] = $row[csf("booking_no")];
					// 	$fin_fab_qnty +=$fabric_data_arr[$row[csf('booking_no')]]['fin_fab_qnty'];
					// 	$grey_fab_qnty +=$fabric_data_arr[$row[csf('booking_no')]]['grey_fab_qnty'];

					// }

					if($allBookingNoChk[$row[csf("booking_no")]]=="")
					{
						$allBookingNoChk[$row[csf("booking_no")]] =$row[csf("booking_no")];
						array_push($all_booking_no_arr,$row[csf("booking_no")]);
					}


				}
				else if($booking_no[1]=='YDW')
				{
					$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["ydw_no"] = $row[csf('booking_no')];

					$ydwbookin_no = $ydw_arr[$row[csf("job_no")]][$row[csf('booking_no')]]["fab_booking_no"];
					$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["booking_no"] =$ydwbookin_no;

					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["book_qty"] = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["book_qnty"];
					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["booking_date"] = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["booking_date"];
					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["is_short"] = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["is_short"];

					$groupKeyArr[$groupKey]['required_qty'] = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["book_qnty"];

					if($ydwbookin_noChk[$ydwbookin_no]=="")
					{
						$ydwbookin_noChk[$ydwbookin_no] = $ydwbookin_no;
						$s_book_qnty = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["s_book_qnty"];
						$m_book_qnty = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["m_book_qnty"];


					}

					if($allBookingNoChk[$ydwbookin_no]=="")
					{
						$allBookingNoChk[$ydwbookin_no] = $ydwbookin_no;
						array_push($all_booking_no_arr,$ydwbookin_no);
					}



					if($row[csf("issue_purpose")]==1)
					{
						$knitting_book_qnty += $row[csf("issue_qnty")];
					}
					else if($row[csf("issue_purpose")]==2)
					{
						$yDyeing_book_qnty += $row[csf("issue_qnty")];
					}

					// if($booking_noChk[$ydwbookin_no]=="")
					// {
					// 	$booking_noChk[$ydwbookin_no] = $ydwbookin_no;
					// 	$fin_fab_qnty +=$fabric_data_arr[$ydwbookin_no]['fin_fab_qnty'];
					// 	$grey_fab_qnty +=$fabric_data_arr[$ydwbookin_no]['grey_fab_qnty'];
					// }

				}

				if($row[csf("issue_basis")]==3)
				{
					$req_booking_no=$req_book_info_arr[$row[csf('job_no')]][$row[csf("requisition_no")]]["booking_no"];

					$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["booking_no"] =$req_booking_no;
					$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["requisition_no"] =$row[csf('requisition_no')];

					$requiredInfoArr[$row[csf('job_no')]][$req_booking_no]["book_qty"] = $booking_info_arr[$row[csf("job_no")]][$req_booking_no]["book_qnty"];
					$requiredInfoArr[$row[csf('job_no')]][$req_booking_no]["booking_date"] = $booking_info_arr[$row[csf("job_no")]][$req_booking_no]["booking_date"];
					$requiredInfoArr[$row[csf('job_no')]][$req_booking_no]["is_short"] = $booking_info_arr[$row[csf("job_no")]][$req_booking_no]["is_short"];

					$groupKeyArr[$groupKey]['required_qty'] = $booking_info_arr[$row[csf("job_no")]][$req_booking_no]["book_qnty"];

					if($req_booking_noChk[$req_booking_no]=="")
					{
						$req_booking_noChk[$req_booking_no] = $req_booking_no;
						$s_book_qnty = $booking_info_arr[$row[csf("job_no")]][$req_booking_no]["s_book_qnty"];
						$m_book_qnty = $booking_info_arr[$row[csf("job_no")]][$req_booking_no]["m_book_qnty"];
					}



					if($row[csf("issue_purpose")]==1)
					{
						$knitting_book_qnty += $row[csf("issue_qnty")];
					}
					else if($row[csf("issue_purpose")]==2)
					{
						$yDyeing_book_qnty += $row[csf("issue_qnty")];
					}

					// if($req_booking_noChk[$req_booking_no]=="")
					// {
					// 	$req_booking_noChk[$req_booking_no] = $req_booking_no;
					// 	$fin_fab_qnty +=$fabric_data_arr[$req_booking_no]['fin_fab_qnty'];
					// 	$grey_fab_qnty +=$fabric_data_arr[$req_booking_no]['grey_fab_qnty'];

					// }
					if($allBookingNoChk[$req_booking_no]=="")
					{
						$allBookingNoChk[$req_booking_no] = $req_booking_no;
						array_push($all_booking_no_arr,$req_booking_no);
					}
				}
			}


			$issue_ids .=$row[csf("issue_id")].",";

			if($job_id_Chk[$row[csf("job_id")]]=="")
			{
				$job_id_Chk[$row[csf("job_id")]] = $row[csf("job_id")];
				array_push($all_job_id_arr,$row[csf("job_id")]);
			}


		}
		unset($result_issue);
		//echo "<pre>";print_r($all_booking_no_arr);

		$sql_job_data="SELECT a.job_id, a.job_no_mst, a.pub_shipment_date
		FROM wo_po_break_down a
		WHERE a.is_deleted=0 and a.status_active=1 ".where_con_using_array($all_job_id_arr,0,'a.job_id')." ";
		$rslt_job_data = sql_select($sql_job_data);
		//echo $sql_job_data;
		foreach($rslt_job_data as $row)
		{
			$po_data[$row[csf('job_no_mst')]]['poshipdate'].=$row[csf('pub_shipment_date')].',';
		}

	}



	$issue_ids =chop($issue_ids,",");
	$issue_ids=implode(",",array_filter(array_unique(explode(",",$issue_ids))));

	// ================= Issue Return ===============//
	if($issue_ids!="")
	{
		$issue_ids=explode(",",$issue_ids);
		$issue_ids_chnk=array_chunk($issue_ids,999);
		$issue_no_cond=" and";
		foreach($issue_ids_chnk as $issueId)
		{
			if($issue_no_cond==" and")  $issue_no_cond.="(c.id in(".implode(',',$issueId).")"; else $issue_no_cond.=" or c.id in(".implode(',',$issueId).")";
		}
		$issue_no_cond.=")";
		//echo $issue_no_cond;die;
		// echo "SELECT a.recv_number,a.booking_no, b.cons_quantity, c.id as issue_id,b.id as trans_id,d.brand,d.id as product_id,d.lot,d.yarn_type, d.yarn_count_id, d.yarn_comp_type1st,d.yarn_comp_percent1st from inv_receive_master a, inv_transaction b, inv_issue_master c,product_details_master d where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and b.prod_id=d.id and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 $issue_no_cond";
		$issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, b.cons_quantity, b.issue_id,b.id as trans_id,d.brand,d.id as product_id,d.lot,d.yarn_type, d.yarn_count_id, d.yarn_comp_type1st,d.yarn_comp_percent1st from inv_receive_master a, inv_transaction b, inv_issue_master c,product_details_master d where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and b.prod_id=d.id and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 $issue_no_cond");
	}

	$transIdChk = array();
	foreach ($issue_return_res as $val)
	{
		if($transIdChk[$val[csf("trans_id")]]=="")
		{
			$transIdChk[$val[csf("trans_id")]] = $val[csf("trans_id")];

			$compPercent = $val[csf('yarn_comp_percent1st')];
			$yanrType = $val[csf('yarn_type')];
			$yarnComposition = $val[csf('yarn_comp_type1st')];
			$yarnCount = $val[csf('yarn_count_id')];
			$prod_id=$val[csf('product_id')];
			$issue_id=$val[csf('issue_id')];

			$groupKey = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType;
			$issue_return_qnty_arr[$issue_id][$groupKey][$val[csf('issue_id')]][$prod_id] += $val[csf("cons_quantity")];

		}
	}

	if(!empty($all_booking_no_arr))
	{
		$sql_booking="SELECT b.job_no, b.booking_no, b.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.id=b.booking_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($all_booking_no_arr,1,'b.booking_no')." group by b.job_no, b.booking_no, b.po_break_down_id";
		//echo $sql_booking;//die();
		$rslt_booking = sql_select($sql_booking);
		$booking_info_arr = array();
		$poIdChk = array();
		$all_po_id_arr = array();
		foreach ($rslt_booking as $row)
		{
			$booking_info_arr[$row[csf('job_no')]][$row[csf('booking_no')]]['po_id'] .=$row[csf('po_break_down_id')].',';

			if($poIdChk[$row[csf("po_break_down_id")]]=="")
			{
				$poIdChk[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
				array_push($all_po_id_arr,$row[csf("po_break_down_id")]);
			}
		}
		unset($rslt_booking);
		//echo "<pre>";print_r($booking_info_arr);
		if(!empty($all_po_id_arr))
		{
			/* $sql_grey_rcv="SELECT c.po_breakdown_id, c.quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (22,2,58) and c.entry_form in (22,2,58) and c.trans_id !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ".where_con_using_array($all_po_id_arr,0,'c.po_breakdown_id')." ";
			//echo $sql_grey_purchase;//die();

			$rsltGreyrcv = sql_select($sql_grey_rcv);
			$greyRcvQntyArr = array();
			foreach ($rsltGreyrcv as $row)
			{
				$greyRcvQntyArr[$row[csf('po_breakdown_id')]]['grey_rcv_qnty'] +=$row[csf('quantity')];
			}
			unset($rsltGreyrcv); */
			//echo "<pre>";print_r($greyRcvQntyArr);


			$finish_recv_qnty_arr = array();
			/* $sql_fin_purchase="SELECT c.po_breakdown_id, c.color_id, c.quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=37 and c.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ".where_con_using_array($all_po_id_arr,0,'c.po_breakdown_id')."";

			// echo $sql_fin_purchase;die();
			$dataArrayFinPurchase=sql_select($sql_fin_purchase);
			foreach($dataArrayFinPurchase as $row)
			{
				$finish_recv_qnty_arr[$row[csf('po_breakdown_id')]]['finish_rcv'] +=$row[csf('quantity')];
			}
			unset($dataArrayFinPurchase); */

			$sqlTrans = "SELECT trans_id,po_breakdown_id, entry_form, trans_type, quantity from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in (2,7,11,13,14,15,16,18,37,45,46,51,52,61,66,71,82,83,84,134) ".where_con_using_array($all_po_id_arr,0,'po_breakdown_id')."";
			//echo $sqlTrans;
			$rsltTrans = sql_select($sqlTrans);

			$greyRcvRtnQntyArr = array();
			$grey_issue_qnty_arr = array();
			$grey_issue_return_qnty_arr = array();
			$trans_qnty_arr = array();
			$finish_issue_qnty_arr = array();
			$finish_issue_rtn_qnty_arr = array();
			$greyRcvQntyArr = array();
			$finish_recv_rtn_qnty_arr = array();
			$finish_available_arr = array();

			foreach ($rsltTrans as $row)
			{
				//knit
				if($row[csf('entry_form')]==2 || $row[csf('entry_form')]==45 && $row[csf('trans_type')]==3 || $row[csf('entry_form')]==51 && $row[csf('trans_type')]==4 || $row[csf('entry_form')]==16 || $row[csf('entry_form')]==61 || $row[csf('entry_form')]==11 || $row[csf('entry_form')]==13 || $row[csf('entry_form')]==82 || $row[csf('entry_form')]==83)
				{
					if($row[csf('entry_form')]==2)
					{
						$greyRcvQntyArr[$row[csf('po_breakdown_id')]]['grey_rcv_qnty'] += $row[csf('quantity')];
					}

					if($row[csf('entry_form')]==45 && $row[csf('trans_type')]==3)
					{
						$greyRcvRtnQntyArr[$row[csf('po_breakdown_id')]]['rtn_qnty'] += $row[csf('quantity')];
					}

					if(($row[csf('entry_form')]==51 || $row[csf('entry_form')]==84) && $row[csf('trans_type')]==4)
					{
						$grey_issue_return_qnty_arr[$row[csf('po_breakdown_id')]]['grey_issue_rtn'] += $row[csf('quantity')];
					}

					if($row[csf('entry_form')]==16 || $row[csf('entry_form')]==61)
					{
						$grey_issue_qnty_arr[$row[csf('po_breakdown_id')]]['grey_issue'] += $row[csf('quantity')];
					}

					if($row[csf('entry_form')]==13 || $row[csf('entry_form')]==82 || $row[csf('entry_form')]==83)
					{
						if($row[csf('trans_type')]==5)
						{
							$trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans'] += $row[csf('quantity')];
						}
						if($row[csf('trans_type')]==6)
						{
							$trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans'] -= $row[csf('quantity')];
						}
					}
				}

				//finish
				if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==14 || $row[csf('entry_form')]==15 || $row[csf('entry_form')]==134 || $row[csf('entry_form')]==66 || $row[csf('entry_form')]==18 || $row[csf('entry_form')]==71 || ($row[csf('entry_form')]==46 && $row[csf('trans_type')]==3) || ($row[csf('entry_form')]==52 && $row[csf('trans_type')]==4) || $row[csf('entry_form')]==37)
				{
					$finish_trns_out=0; $finish_trns_in=0;
					if($row[csf('entry_form')]==14 || $row[csf('entry_form')]==15 || $row[csf('entry_form')]==134)
					{

						if($row[csf('trans_type')]==5)
						{
							if($row[csf('trans_id')]!=0)
							{
								$finish_trns_in=$row[csf('quantity')];
							}
						}
						if($row[csf('trans_type')]==6)
						{
							if($row[csf('trans_id')]!=0)
							{
								$finish_trns_out=$row[csf('quantity')];
							}
						}
					}
					if($row[csf('entry_form')]==18 || $row[csf('entry_form')]==71)
					{
						$finish_issue_qnty_arr[$row[csf('po_breakdown_id')]]['finish_issue'] +=$row[csf('quantity')];
					}
					if($row[csf('entry_form')]==52 && $row[csf('trans_type')]==4)
					{
						$finish_issue_rtn_qnty_arr[$row[csf('po_breakdown_id')]]['finish_issue_rtn'] +=$row[csf('quantity')];
					}
					if($row[csf('entry_form')]==46 && $row[csf('trans_type')]==3)
					{
						$finish_recv_rtn_qnty_arr[$row[csf('po_breakdown_id')]]['finish_recv_rtn'] +=$row[csf('quantity')];
					}
					$finish_avail=0; $finish_rec=0; $finish_rec_return=0; $net_finish_trns=0;
					if($row[csf('trans_id')]!=0)
					{
						if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==37)
						{
							$finish_rec= $row[csf('quantity')];
							if($row[csf('entry_form')]==7)
							{
								$finish_recv_qnty_arr[$row[csf('po_breakdown_id')]]['finish_rcv']+=$row[csf('quantity')];
							}
						}
						if($row[csf('entry_form')]==46 && $row[csf('trans_type')]==3)
						{
							$finish_rec_return= $row[csf('quantity')];
						}
					}
					$net_finish_trns=$finish_trns_in-$finish_trns_out;
					$finish_avail=$finish_rec+$net_finish_trns - $finish_rec_return;
					$finish_available_arr[$row[csf('po_breakdown_id')]]+=$finish_avail;
				}

			}
			unset($rsltTrans);
			//echo "<pre>";print_r($finish_recv_qnty_arr);
		}

	}


	$usd_arr = array();
    $sqlSelectData = sql_select("select con_date,conversion_rate from currency_conversion_rate where currency=2 and is_deleted=0 order by con_date desc");
    foreach ($sqlSelectData as $row)
    {
        $usd_arr[date('d-m-Y',strtotime($row[csf('con_date')]))] = $row[csf('conversion_rate')];
    }


 	ob_start();
	?>
     <fieldset style="width:1350px;" >

            <table cellpadding="0" cellspacing="0" width="1330" style="float: left;">
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>"><p  style="font-size:20px;"><? echo $report_title; ?></p></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" ><p style="font-size:18px"><? echo $company_arr[str_replace("'", "", $cbo_company_id)]; ?></p></td>
                </tr>

            </table>

            <table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                <thead>
                    <th width="30" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >SL</p></th>
                    <th width="200" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Name</p></th>
                    <th width="230" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >LOT::Brand::Supplier</th>
                    <th width="150" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Required Qty<br>
					<? if($cbo_search_by==4){?> [<span >Sample</span>]<?} else {?>
					[Bulk+<span style="color: #FF0000;">Short</span>] <? } ?></p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Allocated Qty</p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Allocated Bal</p></th>
                    <th width="120" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Issued Qty</p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Issue Return Qty</p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Issue Bal</p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Unit Price <br>(USD)</p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >TTL Value <br>(USD)</p></th>
                </thead>
            </table>
            <div style="width:1350px; overflow-y: scroll; max-height:380px; float: left; margin-bottom:20px" id="scroll_body">
                <table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                    <tbody>
                        <?
                        $i=1;
						$groupKey_count = 0;
                        $g_issue_subtotal_qnty = 0;
						$g_issue_rtn_subtotal_qnty = 0;
						$g_issue_bal_subtotal_qnty = 0;
						$g_amount_subtotal_qnty = 0;
						$g_required_total_qnty = 0;
						$allocated_qnty = 0;
						$g_allocated_subtotal_qnty = 0;
   						$g_allocat_balance_subtotal_qnty = 0;
						$s_booking_qnty = 0;
						$m_booking_qnty = 0;
						$knit_close_balance = 0;
						$grey_close_balance = 0;
						$total_issue_to_cut_qnty = 0;
						$total_grey_recv_qnty = 0;
						$total_finish_feb_recv_qnty = 0;
						$total_fin_fab_required = 0;
						$total_grey_fab_required = 0;
						$total_finish_stock = 0;
						$total_grey_feb_issue_to_dye = 0;

						$knit_close = '';
						$grey_close = '';
						$bookChk = array();


                        foreach ($groupdataArr as $jobKey=>$jobData)
                        {
                            foreach ($jobData as $bookingkey=>$bookingData)
                            {
                                foreach ($bookingData as $groupKey=>$groupData)
                                {
                                    foreach ($groupData as $issueKey=>$issueData)
                                    {
										foreach ($issueData as $prod_id=>$row)
										{
											if($groupKeyChk[$groupKey]=="")
											{
												$groupKeyChk[$groupKey] = $groupKey;
												$groupKey_count += count($groupKey);
											}
											if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

											$compPercent = $row['yarn_comp_percent1st'];
											$yanrType = $row['yarn_type'];
											$yarnComposition = $row['yarn_comp_type1st'];
											$yarnCount = $row['yarn_count_id'];
											$issueDate = $row['issue_date'];

											$caption = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType;


											if($row['ydw_no'])
											{
												$ydw_booking = ' ( '.$row['ydw_no'].' )';
											}
											else if($row['issue_basis']==3)
											{
												$ydw_booking = ' ( '."req no-".$row['requisition_no'].' )';
											}
											else
											{
												$ydw_booking = '';
											}

											$exchangeRate=$usd_arr[date('d-m-Y',strtotime($row['issue_date']))];
											if($exchangeRate =="")
											{
												foreach ($usd_arr as $rate_date => $rat)
												{
													if(strtotime($rate_date) <= strtotime($row['issue_date']))
													{
														$rate_date = date('d-m-Y',strtotime($rate_date));
														$exchangeRate=$rat;
														break;
													}
												}
											}
											//echo $groupKey."==".$issueId."|<br>";
											$issue_return_qnty = $issue_return_qnty_arr[ $issueKey][$groupKey][$issueKey][$prod_id];
											//echo $jobKey."==".$bookingkey."==".$prod_id."|<br>";
											$allocated_qnty = $alloInfoArr[$jobKey][$row['booking_no']][$prod_id]["allocated_qnty"];

											//echo $exchangeRate;

											//$amount = $row[csf('issue_qnty')] * $exchangeRate;
											$amount =  ( ($row['issue_qnty']-$issue_return_qnty) * number_format($row['cons_rate']/$exchangeRate, 2) );
											$requiredQty = 0;
											if($row['issue_basis']==3)
											{
												$requiredQty = $requiredInfoArr[$jobKey][$row['booking_no']]["book_qty"];
												$booking_date = $requiredInfoArr[$jobKey][$row['booking_no']]["booking_date"];
												$is_short = $requiredInfoArr[$jobKey][$row['booking_no']]["is_short"];
											}
											else
											{
												$requiredQty = $requiredInfoArr[$jobKey][$bookingkey]["book_qty"];
												$booking_date = $requiredInfoArr[$jobKey][$bookingkey]["booking_date"];
												$is_short = $requiredInfoArr[$jobKey][$bookingkey]["is_short"];
											}

											$booking_type = '';
											if ($is_short == 1)
											{
												if($bookWiseQntyChk[$row['booking_no']]=="")
												{
													$bookWiseQntyChk[$row['booking_no']] = $row['booking_no'];
													$s_booking_qnty += $requiredQty;
												}
												$booking_type = 'Short';
												$txtcolor = "#FF0000";
											}
											else if ($is_short == 'SMN')
											{
												$booking_type = 'Sample';
												//$txtcolor = "#0e2ab5";
											}
											else
											{
												if($bookWiseQntyChk[$row['booking_no']]=="")
												{
													$bookWiseQntyChk[$row['booking_no']] = $row['booking_no'];
													$m_booking_qnty += $requiredQty;
												}

												$booking_type = 'Main';
												$txtcolor = "#000000";
											}

											$dates=array_unique(array_filter(explode(",",$po_data[$jobKey]['poshipdate'])));

											$fs_date='';
											$ls_date='';
											foreach ($dates as $key => $val)
											{
												if(empty($fs_date))
												{
													$fs_date=strtotime('21-01-2025');
												}
												$curDate = strtotime($val);
												if ($curDate > $ls_date) {
													$ls_date = $curDate;
												}
												if ($curDate < $fs_date) {
													$fs_date = $curDate;
												}

											}

											$sqljob=sql_select("SELECT b.id as prog_no  from  ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b, wo_po_break_down c,wo_po_details_master d where  b.id=a.dtls_id and c.id=a.po_id and d.job_no=c.job_no_mst and b.status_active =1 and c.status_active =1 and a.status_active =1 and d.company_name=$cbo_company_id and d.job_no='$jobKey'");
											$allProgramNoArr = array();
											foreach($sqljob as $p_rows)
											{
												array_push($allProgramNoArr,$p_rows[csf("prog_no")]);
											}

											if(!empty($allProgramNoArr))
											{
												$result_prog=sql_select("SELECT a.ref_closing_status
												from inv_receive_master a , pro_grey_prod_entry_dtls b, ppl_planning_info_entry_dtls c
												where a.id=b.mst_id and c.id=a.booking_id and a.is_deleted=0 and a.status_active=1 and a.entry_form=2 and a.receive_basis=2 and a.company_id=$cbo_company_id and a.ref_closing_status=1 ".where_con_using_array($allProgramNoArr,0,'c.id')." group by a.ref_closing_status" );
											}



											$all_po_ids = $booking_info_arr[$jobKey][$row['booking_no']]['po_id'];
											$all_po_ids_arr = array_unique(array_filter(explode(",",chop($all_po_ids))));
											//echo "<pre>";print_r($all_po_ids_arr);
											$total_greyRcvQnty = 0;
											$total_greyRtnQnty = 0;
											$total_netTransKnit = 0;
											$total_greyIssueQnty = 0;
											$total_greyIssueTrnQnty = 0;
											$total_finish_issue_qnty = 0;
											$total_finish_issue_rtn_qnty = 0;
											$total_fin_fab_qnty = 0;
											$total_grey_fab_qnty = 0;
											$total_finish_recv_qnty = 0;
											$total_finish_recv_rtn_qnty = 0;
											$total_finish_available_qnty = 0;

											foreach ($all_po_ids_arr as $row_po_id)
											{
												$total_greyRcvQnty += $greyRcvQntyArr[$row_po_id]['grey_rcv_qnty'];
												$total_greyRtnQnty += $greyRcvRtnQntyArr[$row_po_id]['rtn_qnty'];
												$total_netTransKnit += $trans_qnty_arr[$row_po_id]['knit_trans'];
												$total_greyIssueQnty += $grey_issue_qnty_arr[$row_po_id]['grey_issue'];
												$total_greyIssueTrnQnty += $grey_issue_return_qnty_arr[$row_po_id]['grey_issue_rtn'];
												$total_finish_issue_qnty += $finish_issue_qnty_arr[$row_po_id]['finish_issue'];
												$total_finish_issue_rtn_qnty += $finish_issue_rtn_qnty_arr[$row_po_id]['finish_issue_rtn'];

												$total_fin_fab_qnty+=$fabric_data_arr[$row_po_id]['fin_fab_qnty'];
												$total_grey_fab_qnty +=$fabric_data_arr[$row_po_id]['grey_fab_qnty'];
												$total_finish_recv_qnty +=$finish_recv_qnty_arr[$row_po_id]['finish_rcv'];
												$total_finish_recv_rtn_qnty +=$finish_recv_rtn_qnty_arr[$row_po_id]['finish_recv_rtn'];
												$total_finish_available_qnty +=$finish_available_arr[$row_po_id];
											}

											if($bookChk[$row['booking_no']]=="")
											{
												$bookChk[$row['booking_no']] = $row['booking_no'];
												$total_issue_to_cut_qnty += ($total_finish_issue_qnty - $total_finish_issue_rtn_qnty);
												$total_grey_recv_qnty += $total_greyRcvQnty - $total_greyRtnQnty;
												$total_finish_feb_recv_qnty += $total_finish_recv_qnty - $total_finish_recv_rtn_qnty;
												$total_fin_fab_required += $total_fin_fab_qnty;
												$total_grey_fab_required += $total_grey_fab_qnty;
												$total_finish_stock += $total_finish_available_qnty-$total_issue_to_cut_qnty;
												$total_grey_feb_issue_to_dye += $total_greyIssueQnty-$total_greyIssueTrnQnty;
											}



											$grey_rcv_qnty = $total_greyRcvQnty - $total_greyRtnQnty;
											$grey_available = $grey_rcv_qnty + $total_netTransKnit;
											$grey_fabric_issue = $total_greyIssueQnty - $total_greyIssueTrnQnty;

											//echo "<pre>";print_r($grey_fabric_issue);
											$grey_close_balance = $grey_available - $grey_fabric_issue;
											if(number_format($grey_close_balance,2)<=0.00)
											{
												$grey_close ='<span style="color:green; font-weight:bold;" >Yes</span>';
											}
											else
											{
												$grey_close ='<span style="color:red; font-weight:bold;">No</span>';
											}

											$program_qnty = $program_info_arr[$jobKey][$row['booking_no']]["program_qnty"];
											$program_nos = $program_info_arr[$jobKey][$row['booking_no']]["program_no"];
											$program_nos_arr = array_unique(array_filter(explode(",",chop($program_nos))));

											$total_production = 0;
											foreach ($program_nos_arr as $row_p)
											{
												$total_production +=$production_data_arr[$row_p]["tot_prod_qty"];
											}

											$knit_close_balance = $program_qnty-$total_production;

											if(number_format($knit_close_balance,2)<=0.00 || !empty($result_prog))
											{
												$knit_close ='<span style="color:green; font-weight:bold;" title="'.'Program: '.$program_qnty.' - Production: '.$total_production.' = Balance: '.$knit_close_balance.'">Yes</span>';
											}
											else
											{
												$knit_close ='<span style="color:red; font-weight:bold;" title="'.'Program: '.$program_qnty.' - Production: '.$total_production.' = Balance: '.$knit_close_balance.'">No</span>';
											}

											if($cbo_search_by !=4)
											{
												$shiping_status = $shipingStatusArr[$row['job_no']]["shiping_status"];

												if($shiping_status)
												{
													$shiping_sts = '::Ship Date:F=( '.change_date_format(date('d-M-y',$fs_date)).' ), L=( '.change_date_format(date('d-M-y',$ls_date)).' )'.'::  Knit Closed:'.$knit_close.'::  Grey Closed:'.$grey_close.'::Job Closed:<span style="color:red">No</span>';
												}
												else
												{
													$shiping_sts = '::Ship Date:F=( '.change_date_format(date('d-M-y',$fs_date)).' ), L=( '.change_date_format(date('d-M-y',$ls_date)).' )'.'::  Knit Closed:'.$knit_close.'::  Grey Closed:'.$grey_close.'::Job Closed:<span style="color:green">Yes</span>';
												}
											}




											if(!in_array($jobKey."**".$bookingkey."**".$groupKey,$yarnDescriptionArr))
											{

												if($i!=1)
												{
													?>
													<tr bgcolor="#CCCCCC">
														<td colspan="3" align="right"><b>Sub Total : </b></td>
														<td align="right"><b><?php echo number_format($required_subtotal_qnty,2,'.',''); ?></b></td>
														<td align="right"><b><?php echo number_format($allocated_subtotal_qnty,2,'.',''); ?></b></td>
														<td align="right">
															<b>
																<?php
																$allocat_balance_subtotal_qnty = ($required_subtotal_qnty-$allocated_subtotal_qnty);
																echo number_format($allocat_balance_subtotal_qnty,2,'.','');
																?>
															</b>
														</td>
														<td align="right"><b><?php echo number_format($issue_subtotal_qnty,2,'.',''); ?></b></td>

														<td align="right"><b><?php echo number_format($issue_rtn_subtotal_qnty,2,'.',''); ?></b></td>
														<td align="right">
															<b>
																<?php
																$issue_bal_subtotal_qnty = ($required_subtotal_qnty-$issue_subtotal_qnty)+$issue_rtn_subtotal_qnty;
																echo number_format($issue_bal_subtotal_qnty,2,'.','');
																?>
															</b>
														</td>
														<td>&nbsp;</td>
														<td align="right"><b><?php echo number_format($amount_subtotal_qnty,2,'.',''); ?></b></td>
													</tr>
													<?
													$required_subtotal_qnty=0;
													$allocated_subtotal_qnty=0;
   													$allocat_balance_subtotal_qnty=0;
													$issue_subtotal_qnty=0;
													$issue_rtn_subtotal_qnty = 0;
													$issue_bal_subtotal_qnty = 0;
													$amount_subtotal_qnty = 0;
												}

												$yarnDescriptionArr[$i]=$jobKey."**".$bookingkey."**".$groupKey;


												?>
												<tr>
													<td rowspan="2">&nbsp;</td>
													<td colspan="10" bgcolor="#EEEEEE" title="<? echo $ydw_booking;?>">
														<b>
														<?php echo 'JOB :'.$row['job_no'].'::Style :'.$row['style_ref_no'].'::Buyer :'.$buyer_arr[$row['buyer_name']].'::Booking :'.$row['booking_no'].$ydw_booking.'::Fab Req Date:'.change_date_format($booking_date).$shiping_sts; ?>
														</b>
													</td>
												</tr>

												<tr>

													<td colspan="2" bgcolor="#EEEEEE">
														<b>
														<?php
														//var_dump($yarnDescriptionArr[$i]);
														$captionData = explode("**",$yarnDescriptionArr[$i]);
															echo $count_arr[$captionData[2]] . " ". $composition[$captionData[3]] . " ". $captionData[4] . "% ". $yarn_type[$captionData[5]];
															?>
														</b>
													</td>
													<td  bgcolor="#EEEEEE" align="right" title="<? echo $booking_type;?>">
														<p style="color:<? echo $txtcolor?>">
														 <? echo number_format($requiredQty,2); $required_subtotal_qnty += $requiredQty;

														 if($bookingKeyChk[$row['booking_no']]=="")
														 {
															$bookingKeyChk[$row['booking_no']] = $row['booking_no'];
															$g_required_total_qnty += $requiredQty;
															//$g_allocat_balance_subtotal_qnty +=$requiredQty-$allocated_qnty;
															$g_allocated_subtotal_qnty += $allocated_qnty;
														 }
														 ?>
														</p>

													</td>
													<td bgcolor="#EEEEEE" align="right"><? echo number_format($allocated_qnty,2); ?></td>
													<td bgcolor="#EEEEEE" align="right">
														<?
														$allocat_balance_qnty=$requiredQty-$allocated_qnty;
														echo number_format($allocat_balance_qnty,2);
														?>
													</td>
													<td colspan="5" bgcolor="#EEEEEE"></td>
												</tr>
												<?
											}


											?>

											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">
												<td width="30" align="center" ><? echo $i; ?></td>
												<td width="200"  style="word-break: break-all;" title="<? echo 'Issue Number : '.$row['issue_number'];?>"><? echo 'CH NO: '.$row['issue_number'].'::'.change_date_format($row['issue_date']); ?>&nbsp;</td>
												<td width="230" style="word-break: break-all;" ><? echo 'Lot: '.$row['lot'].'::Brand: '.$brand_arr[$row['brand_id']].'::Supplier: '.$supplier_short_arr[$row['supplier_id']]; ?></td>
												<td width="150" style="word-break: break-all;"align="center" ></td>

												<td width="100" style="word-break: break-all;" align="right"></td>
												<td width="100" style="word-break: break-all;" align="right"></td>
												<td width="120" style="word-break: break-all;" align="right"><? echo number_format($row['issue_qnty'],2); ?></td>
												<td width="100" style="word-break: break-all;" align="right"><? echo number_format($issue_return_qnty,2); ?></td>
												<td width="100" style="word-break: break-all; " title='(Required Qty-Issued Qty+Issue Return Qty)' align="right">
												<?

												$issue_bal = ($requiredQty-$row['issue_qnty'])+$issue_return_qnty;
												echo number_format($issue_bal,2);
												?>
												</td>
												<td width="100" style="word-break: break-all; " align="right"><? echo number_format($row['cons_rate']/$exchangeRate, 2); ?></td>
												<td width="100" style="word-break: break-all; " align="right" title="<? echo $row['cons_rate'].'='.$exchangeRate;?>"><? echo number_format($amount,2); ?></td>

											</tr>
											<?

   											$allocated_subtotal_qnty += $allocated_qnty;
   											$allocat_balance_subtotal_qnty += $allocat_balance_qnty;
   											$issue_subtotal_qnty += $row['issue_qnty'];
											$issue_rtn_subtotal_qnty += $issue_return_qnty;
											//$issue_bal_subtotal_qnty += $issue_bal;
											$amount_subtotal_qnty += $amount;

											//$g_allocated_subtotal_qnty += $allocated_qnty;
   											//$g_allocat_balance_subtotal_qnty += $allocat_balance_qnty;
											$g_issue_subtotal_qnty += $row['issue_qnty'];
											$g_issue_rtn_subtotal_qnty += $issue_return_qnty;
											//$g_issue_bal_subtotal_qnty += $issue_bal;
											$g_amount_subtotal_qnty += $amount;

											$i++;
										}
                                    }
                                }
                            }
                        }
                        ?>
						<tr bgcolor="#CCCCCC">
							<td colspan="2" align="left"><b>TTL Yarn - ( <? echo $groupKey_count;?> ) </b></td>
							<td align="right"><b>Sub Total : </b></td>
							<td align="right"><b><?php echo number_format($required_subtotal_qnty,2,'.',''); ?></b></td>
							<td align="right"><b><?php echo number_format($allocated_subtotal_qnty,2,'.',''); ?></b></td>
							<td align="right">
								<b>
									<?php
									$allocat_balance_subtotal_qnty = ($required_subtotal_qnty-$allocated_subtotal_qnty);
									echo number_format($allocat_balance_subtotal_qnty,2,'.','');

									?>
								</b>
							</td>
							<td align="right"><b><?php echo number_format($issue_subtotal_qnty,2,'.',''); ?></b></td>

							<td align="right"><b><?php echo number_format($issue_rtn_subtotal_qnty,2,'.',''); ?></b></td>
							<td align="right">
								<b>
									<?php
									$issue_bal_subtotal_qnty = ($required_subtotal_qnty-$issue_subtotal_qnty)+$issue_rtn_subtotal_qnty;
									echo number_format($issue_bal_subtotal_qnty,2,'.','');

									?>
							</b></td>
							<td>&nbsp;</td>
							<td align="right"><b><?php echo number_format($amount_subtotal_qnty,2,'.',''); ?></b></td>
						</tr>

						<tr bgcolor="#CCCCCC">
							<td colspan="3" align="right"><b>Grand Total : </b></td>
							<td align="right"><b><?php echo number_format($g_required_total_qnty,2,'.',''); ?></b></td>
							<td align="right"><b><?php echo number_format($g_allocated_subtotal_qnty,2,'.',''); ?></b></td>
							<td align="right"><b><?php echo number_format(($g_required_total_qnty-$g_allocated_subtotal_qnty),2,'.',''); ?></b></td>
							<td align="right"><b><?php echo number_format($g_issue_subtotal_qnty,2,'.',''); ?></b></td>

							<td align="right"><b><?php echo number_format($g_issue_rtn_subtotal_qnty,2,'.',''); ?></b></td>
							<td align="right"><b><?php echo number_format(($g_required_total_qnty-$g_issue_subtotal_qnty)+$g_issue_rtn_subtotal_qnty,2,'.',''); ?></b></td>
							<td>&nbsp;</td>
							<td align="right"><b><?php echo number_format($g_amount_subtotal_qnty,2,'.',''); ?></b></td>
						</tr>

                </table>
            </div>
			<br><br>


			<div style="margin-top: 20px;padding-top: 20px;">

				<table cellpadding="0" width="400" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th colspan="2"><p style="font-size:18px; font-weight:normal" >Summary</p></th>
						</tr>
						<tr>
                   			<th width="300" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Particulars</p></th>
                   			<th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Total Qnty</p></th>
						</tr>
					</thead>
					<tbody>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Required Fab Booking</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($m_booking_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Required Short Fab</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($s_booking_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Allocation</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($g_allocated_subtotal_qnty,2)?></td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;" >Total Yarn Received</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 16px tahoma; font-weight:bold">Total Yarn Received Balance</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Issued To Knitting</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($knitting_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Issued To Sample Knitting</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($knitting_smn_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Issued To Yarn Dyeing</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($yDyeing_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma; font-weight:bold">Total Yarn Issue Balance</td>
							<td align="right" style="font: 14px tahoma; font-weight:bold"><? echo number_format($knitting_book_qnty+$yDyeing_book_qnty+$knitting_smn_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Grey Fabric Required</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($total_grey_fab_required,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Grey Fabric Received</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($total_grey_recv_qnty,2)?></td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma; font-weight:bold">Total Grey Fabric Rcvd Balance</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($total_grey_fab_required-$total_grey_recv_qnty,2)?></td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Grey Fabric Issued To Dye</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($total_grey_feb_issue_to_dye,2)?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma; font-weight:bold">Total Grey Fabric Issue Balance</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Finish Fabric Required</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($total_fin_fab_required,2);?> &nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Finish Fabric Received</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($total_finish_feb_recv_qnty,2)?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma; font-weight:bold">Total Finish Fabric Received Balance</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($total_fin_fab_required-$total_finish_feb_recv_qnty,2)?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Finish Fabric Issued To Cut</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($total_issue_to_cut_qnty,2)?></td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Finish Fabric Stock In Hand</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($total_finish_stock,2)?>&nbsp;</td>
						</tr>
					</tbody>
				</table>


				<table cellpadding="0" width="3" cellspacing="0" align="left" rules="all" border="1">
					<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
				</table>

				<? if($cbo_search_by==4){ ?>
				<table cellpadding="0" width="500" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th colspan="5" ><p  style="font-size:18px; font-weight:normal" > Requirement as per Budget</p></th>
						</tr>
						<tr>
							<th width="30" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >SL</p></th>
                   			<th width="70" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Count</p></th>
                   			<th width="200" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Composition</p></th>
                   			<th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Type</p></th>
                   			<th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Required</p></th>
						</tr>
					</thead>
					<tbody>
						<?
						$i=1;
						$y_count=0;
						$total_yarn_require_qty = 0;
						foreach ($smnbudgetSummaryArr as $k_count => $v_count)
						{
							foreach ($v_count as $k_type => $v_type)
							{
								foreach ($v_type as $k_copm_one => $v_copm_one)
								{
										foreach ($v_copm_one as $k_percent_one => $data)
										{
											$y_count +=count($k_count);
											$total_yarn_require_qty += $data['req_quantity'];
											$compos = $composition[$k_copm_one]." ".$k_percent_one." %";
											?>
											<tr bgcolor="<? echo "#FFFFFF"; ?>">
												<td><? echo $i;?></td>
												<td><? echo $count_arr[$k_count];?></td>
												<td><? echo $compos;?></td>
												<td><? echo $yarn_type[$k_type];?></td>
												<td align="right"><? echo number_format($data['req_quantity'],2,'.','');?></td>
											</tr>
											<?
											$i++;
										}
								}
							}


						}
						?>

					</tbody>
					<tfoot>
						<tr bgcolor="#CCCCCC">
							<td colspan="4" align="right"><b>TTL Yarn - ( <? echo $y_count;?> ) :</b></td>
							<td align="right"><b><? echo number_format($total_yarn_require_qty,2)?></b></td>
						</tr>
					</tfoot>
				</table>
				<? }else {?>
				<table cellpadding="0" width="500" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th colspan="5" ><p  style="font-size:18px; font-weight:normal" >Requirement as per Budget</p></th>
						</tr>
						<tr>
							<th width="30" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >SL</p></th>
                   			<th width="70" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Count</p></th>
                   			<th width="200" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Composition</p></th>
                   			<th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Type</p></th>
                   			<th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Required</p></th>
						</tr>
					</thead>
					<tbody>
						<?
						$i=1;
						$y_count=0;
						$total_yarn_require_qty = 0;
						foreach ($budgetSummaryArr as $k_count => $v_count)
						{
							foreach ($v_count as $k_type => $v_type)
							{
								foreach ($v_type as $k_copm_one => $v_copm_one)
								{
									foreach ($v_copm_one as $k_copm_two => $v_copm_two)
									{
										foreach ($v_copm_two as $k_percent_one => $v_percent_one)
										{
											foreach ($v_percent_one as $k_percent_two => $data)
											{
												$y_count +=count($k_count);
												$total_yarn_require_qty += $data['req_quantity'];
												$compos = $composition[$k_copm_one]." ".$k_percent_one." %"." ".$composition[$k_copm_two];
												?>
												<tr bgcolor="<? echo "#FFFFFF"; ?>">
													<td><? echo $i;?></td>
													<td><? echo $count_arr[$k_count];?></td>
													<td><? echo $compos;?></td>
													<td><? echo $yarn_type[$k_type];?></td>
													<td align="right"><? echo number_format($data['req_quantity'],2,'.','');?></td>
												</tr>
												<?
												$i++;
											}
										}
									}
								}
							}


						}
						?>

					</tbody>
					<tfoot>
						<tr bgcolor="#CCCCCC">
							<td colspan="4" align="right"><b>TTL Yarn - ( <? echo $y_count;?> ) :</b></td>
							<td align="right"><b><? echo number_format($total_yarn_require_qty,2)?></b></td>
						</tr>
					</tfoot>
				</table>
				<? } ?>
			</div>
        </fieldset>
	<?
    foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=="report_generate_200623")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));


	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$search_cond='';
	if ($txt_search_comm=="")
	{
		$search_cond.="";
	}
	else
	{
		if($cbo_search_by==1) $search_cond.=" and c.job_no_prefix_num in ($txt_search_comm) ";
		else if($cbo_search_by==2) $search_cond.=" and c.style_ref_no = '$txt_search_comm'";
		else if($cbo_search_by==3) $search_cond.=" and e.booking_no = '$txt_search_comm'";
		else if($cbo_search_by==4) $search_cond.=" and e.booking_no = '$txt_search_comm'";

		/* if($cbo_search_by==1) $search_cond.=" and c.job_no_prefix_num in ($txt_search_comm) ";
		else if($cbo_search_by==2) $search_cond.=" and c.style_ref_no = '$txt_search_comm'";
		else if($cbo_search_by==3) $search_cond.=" and e.booking_no LIKE '%$txt_search_comm%'";
		else if($cbo_search_by==4) $search_cond.=" and e.booking_no LIKE '%$txt_search_comm%'"; */
	}


    if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond_knit=" and c.buyer_name=$cbo_buyer_id";
    $year_id=str_replace("'","",$cbo_year);;
    $month_id=str_replace("'","",$cbo_month);;


    if(str_replace("'","",$cbo_issue_purpose)!="" && str_replace("'","",$cbo_issue_purpose)!=0) $issue_purpose_cond_knit=" and e.issue_purpose=$cbo_issue_purpose";

    if($year_id!=0) $year_cond_knit=" and to_char(e.insert_date,'yyyy')=$year_id"; else $year_cond_knit="";
    if($month_id!=0) $month_cond_knit=" and to_char(e.insert_date,'mm')=$month_id"; else $month_cond_knit="";

	if($cbo_search_by==4)
	{
		$sql_issue_sample_data="SELECT null as job_no, a.id,a.style_ref_no,a.buyer_name, d.brand_id as brand_id, e.company_id as company_id, e.booking_id as booking_id, e.booking_no as booking_no,
		d.supplier_id, e.issue_purpose,e.id as Issue_id,e.issue_number, d.cons_quantity as issue_qnty, d.cons_rate, f.brand,f.id as product_id,
		f.lot,f.yarn_type, f.yarn_count_id, f.yarn_comp_type1st,f.yarn_comp_percent1st,e.issue_date,e.challan_no
		FROM sample_development_mst a, wo_non_ord_samp_booking_mst b, wo_non_ord_samp_booking_dtls c,inv_transaction d,inv_issue_master e,product_details_master f
		WHERE b.booking_no=c.booking_no and c.style_id=a.id and b.id=e.booking_id and  d.mst_id=e.id and d.prod_id=f.id and d.item_category=1 and d.transaction_type=2 and e.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.issue_basis=1 and e.issue_purpose in(8) and e.company_id=$cbo_company_id $buyer_id_cond_knit $search_cond $issue_purpose_cond_knit $year_cond_knit  $month_cond_knit group by a.id, a.style_ref_no,a.buyer_name, d.brand_id, e.company_id, e.booking_id, e.booking_no,d.supplier_id, e.issue_purpose,e.id,e.issue_number, d.cons_quantity, d.cons_rate, f.brand,f.id,f.lot,f.yarn_type, f.yarn_count_id, f.yarn_comp_type1st,f.yarn_comp_percent1st,e.issue_date,e.challan_no";

		//echo $sql_issue_sample_data;

		$result_issue_sample_data = sql_select($sql_issue_sample_data);
		$sdmIdArr = array();
		foreach ($result_issue_sample_data as $row)
		{
			if($smnbookingNoChk[$row[csf("id")]]=="")
			{
				$smnbookingNoChk[$row[csf("id")]] = $row[csf("id")];
				array_push($sdmIdArr,$row[csf("id")]);
			}
		}


		$data_array=sql_select("SELECT a.booking_date, b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id, sum (b.cons_qnty) as cons_qnty from wo_non_ord_samp_booking_mst a,sample_development_yarn_dtls b where a.booking_no=b.booking_no and  b.status_active=1 ".where_con_using_array($sdmIdArr,0,'b.mst_id')." and b.determin_id in (select determination_id from sample_development_fabric_acc  where status_active=1 ".where_con_using_array($sdmIdArr,0,'sample_mst_id')." and form_type=1) group by a.booking_date,b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id");

		$smnbooking_info_arr = array();
		$smnbudgetSummaryArr = array();
		foreach($data_array as $row)
		{
			$smnbooking_info_arr[$row[csf("booking_no")]]["book_qnty"] += $row[csf("cons_qnty")];

			if($bookNoChk[$row[csf("booking_no")]]=="")
			{
				$bookNoChk[$row[csf("booking_no")]] = $row[csf("booking_no")];

				$smnbooking_info_arr[$row[csf("booking_no")]]["booking_date"] = $row[csf("booking_date")];
			}

			$smnbudgetSummaryArr[$row[csf("count_id")]][$row[csf("type_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]]["req_quantity"]+=$row[csf("cons_qnty")];


		}
		unset($data_array);
		//echo "<pre>";print_r($smnbooking_info_arr);


		$groupdataArr = array();
		$requiredInfoArr = array();
		$knitting_smn_book_qnty = 0;
		foreach ($result_issue_sample_data as $row)
		{
			$compPercent = $row[csf('yarn_comp_percent1st')];
			$yanrType = $row[csf('yarn_type')];
			$yarnComposition = $row[csf('yarn_comp_type1st')];
			$yarnCount = $row[csf('yarn_count_id')];
			$product_id=$row[csf('product_id')];
			$issue_id = $row[csf('issue_id')];

			$groupKey = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType;

			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_id"] = $row[csf('issue_id')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_number"] = $row[csf('issue_number')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["job_no"] = $row[csf('job_no')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["style_ref_no"] = $row[csf('style_ref_no')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["buyer_name"] = $row[csf('buyer_name')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_date"] = $row[csf('issue_date')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["challan_no"] = $row[csf('challan_no')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["lot"] = $row[csf('lot')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["brand_id"] = $row[csf('brand_id')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["supplier_id"] = $row[csf('supplier_id')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_qnty"] += $row[csf('issue_qnty')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["cons_rate"] = $row[csf('cons_rate')];
			$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["booking_no"] = $row[csf('booking_no')];

			$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["book_qty"] = $smnbooking_info_arr[$row[csf("booking_no")]]["book_qnty"];
			$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["booking_date"] = $smnbooking_info_arr[$row[csf("booking_no")]]["booking_date"];
			$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["is_short"] = 'SMN';

			$knitting_smn_book_qnty += $row[csf("issue_qnty")];
			$issue_ids .=$row[csf("issue_id")].",";

		}

		// echo "<pre>";
		// print_r($requiredInfoArr);
		// echo "</pre>";

	}

    $sql_issue_data="SELECT a.trans_id, c.id as job_id, c.job_no, c.buyer_name as buyer_name,
    c.style_ref_no as style_ref_no, d.brand_id as brand_id, e.company_id as company_id, e.booking_id as booking_id, e.booking_no as booking_no,d.supplier_id, e.issue_purpose,e.id as Issue_id,e.issue_number, d.cons_quantity as issue_qnty, d.cons_rate, f.brand,f.id as product_id,f.lot,f.yarn_type, f.yarn_count_id, f.yarn_comp_type1st,f.yarn_comp_percent1st,e.issue_date,e.challan_no,b.id as po_id,b.pub_shipment_date
    FROM order_wise_pro_details a,wo_po_break_down b,wo_po_details_master c,inv_transaction d,inv_issue_master e,product_details_master f
    WHERE a.po_breakdown_id=b.id and b.job_id=c.id and a.trans_id=d.id and d.mst_id=e.id and d.prod_id=f.id and d.item_category=1 and a.trans_type=2 and e.entry_form=3 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.issue_basis=1 and e.issue_purpose in(1,2) and e.company_id=$cbo_company_id $buyer_id_cond_knit $search_cond $issue_purpose_cond_knit $year_cond_knit  $month_cond_knit";

    //echo $sql_issue_data;die;

	$transissueIdChk = array();
	$jobArr = array();
	$bookArr = array();
	$ydwArr = array();
	$bookingNoChk = array();

	$po_data=array();
	$groupKeyArr=array();
	$jobArr=array();
	$s_book_qnty = 0;
	$m_book_qnty = 0;
	$knitting_book_qnty = 0;
	$yDyeing_book_qnty = 0;
	$fin_fab_qnty = 0;
	$grey_fab_qnty = 0;

    $result_issue=sql_select($sql_issue_data);

	foreach ($result_issue as $row)
    {
		if($bookingNoChk[$row[csf("booking_no")]]=="")
		{
			$bookingNoChk[$row[csf("booking_no")]] = $row[csf("booking_no")];

			$booking_no = explode('-',$row[csf("booking_no")]);
			if($booking_no[1]=='Fb' || $booking_no[1]=='FB')
			{
				array_push($bookArr,$row[csf("booking_no")]);
			}
			else if($booking_no[1]=='YDW')
			{
				array_push($ydwArr,$row[csf("booking_no")]);
			}

			array_push($jobArr,$row[csf("job_id")]);
		}
	}

	//var_dump($jobArr);

	$sql_shiping_status="SELECT a.shiping_status, a.job_id, a.job_no_mst FROM wo_po_break_down a
	WHERE a.status_active=1 and a.is_deleted=0 and a.shiping_status !=3 ".where_con_using_array($jobArr,1,'a.job_id')." group by a.shiping_status, a.job_id, a.job_no_mst ";
	//echo $sql_shiping_status;
	$sql_shiping_status_rslt = sql_select($sql_shiping_status);

	$shipingStatusArr = array();
	foreach ($sql_shiping_status_rslt as $row)
	{
		$shipingStatusArr[$row[csf("job_no_mst")]]["shiping_status"] = $row[csf("shiping_status")];
	}
	//var_dump($shipingStatusArr);

	if($ydwArr)
	{
		$sql_ydw = "SELECT a.id, a.job_no, a.fab_booking_no, c.ydw_no from wo_yarn_dyeing_dtls a, wo_po_break_down b,wo_yarn_dyeing_mst c where a.job_no_id=b.job_id and a.mst_id=c.id and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($ydwArr,1,'c.ydw_no')."  group by a.id, a.job_no, a.fab_booking_no, c.ydw_no";
		//a.job_no=b.job_no_mst
		//echo $sql_ydw;die;
		$sql_ydw_rslt = sql_select($sql_ydw);
		$ydw_arr = array();
		foreach($sql_ydw_rslt as $row)
		{
			$ydw_arr[$row[csf("job_no")]][$row[csf("ydw_no")]]["fab_booking_no"] = $row[csf("fab_booking_no")];
			if($bookingNoChk[$row[csf("fab_booking_no")]]=="")
			{
				$bookingNoChk[$row[csf("fab_booking_no")]] = $row[csf("fab_booking_no")];
				array_push($bookArr,$row[csf("fab_booking_no")]);
			}

		}
		unset($sql_ydw_rslt);
		//echo "<pre>";print_r($ydw_arr);
	}



	//for booking qty
    //echo "<pre>";print_r($bookArr);
	if($bookArr)
	{
		$short_fab_description= sql_select("SELECT b.booking_no,b.fin_fab_qnty,b.grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id ".where_con_using_array($bookArr,1,'b.booking_no')." and b.status_active=1 and b.is_deleted=0  and b.grey_fab_qnty>0 and b.is_short=1");
		$fabric_data_arr = array();
		foreach ($short_fab_description as $row)
		{
			$fabric_data_arr[$row[csf('booking_no')]]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
			$fabric_data_arr[$row[csf('booking_no')]]['grey_fab_qnty'] += $row[csf('grey_fab_qnty')];
		}
		unset($short_fab_description);
		//echo "<pre>";print_r($fabric_data_arr);


		$main_fab_description= sql_select("SELECT d.booking_no,d.fin_fab_qnty, d.grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d WHERE a.job_id=b.job_id and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_id=a.job_id and c.id=b.color_size_table_id and b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id ".where_con_using_array($bookArr,1,'d.booking_no')." and d.status_active=1 and d.is_deleted=0  AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND d.is_short=2");

		foreach ($main_fab_description as $row)
		{
			$fabric_data_arr[$row[csf('booking_no')]]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
			$fabric_data_arr[$row[csf('booking_no')]]['grey_fab_qnty'] += $row[csf('grey_fab_qnty')];
		}
		unset($main_fab_description);
		//echo "<pre>";print_r($fabric_data_arr);

		$sql_1="SELECT a.id as job_id, a.job_no, a.buyer_name, d.id as booking_id, d.booking_no, d.booking_date, (c.grey_fab_qnty*e.cons_ratio/100) as book_qnty, d.booking_type,d.is_short, e.count_id, e.type_id, e.copm_one_id, e.copm_two_id, e.percent_one, e.percent_two from wo_po_details_master a, wo_booking_dtls c, wo_booking_mst d, wo_pre_cost_fab_yarn_cost_dtls e where a.job_no=c.job_no and c.booking_mst_id=d.id and c.pre_cost_fabric_cost_dtls_id=e.fabric_cost_dtls_id and c.job_no=e.job_no and a.company_name in (".$cbo_company_id.") and a.status_active=1 and c.status_active=1 and d.status_active=1  ".where_con_using_array($bookArr,1,'d.booking_no')." ";
		//echo $sql_1; die();
		$sql_1_rslt = sql_select($sql_1);
		$booking_info_arr = array();
		$bookNoChk = array();
		$budgetSummaryArr = array();
		foreach($sql_1_rslt as $row)
		{
			$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["book_qnty"] += $row[csf("book_qnty")];
			if($row[csf("is_short")]==1)
			{
				$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["s_book_qnty"] += $row[csf("book_qnty")];
			}
			else
			{
				$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["m_book_qnty"] += $row[csf("book_qnty")];
			}
			if($bookNoChk[$row[csf("booking_no")]]=="")
			{
				$bookNoChk[$row[csf("booking_no")]] = $row[csf("booking_no")];

				$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["booking_date"] = $row[csf("booking_date")];
				$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["is_short"] = $row[csf("is_short")];
			}

			$budgetSummaryArr[$row[csf("count_id")]][$row[csf("type_id")]][$row[csf("copm_one_id")]][$row[csf("copm_two_id")]][$row[csf("percent_one")]][$row[csf("percent_two")]]["req_quantity"]+=$row[csf("book_qnty")];


		}
		unset($sql_1_rslt);
		//echo "<pre>";print_r($booking_info_arr);

		$po_data = array();
		foreach ($result_issue as $row)
		{
			$compPercent = $row[csf('yarn_comp_percent1st')];
			$yanrType = $row[csf('yarn_type')];
			$yarnComposition = $row[csf('yarn_comp_type1st')];
			$yarnCount = $row[csf('yarn_count_id')];
			$product_id=$row[csf('product_id')];
			$issue_id = $row[csf('issue_id')];

			$groupKey = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType;
			if($transissueIdChk[$row[csf("trans_id")]]=="")
			{
				$transissueIdChk[$row[csf("trans_id")]] = $row[csf("trans_id")];

				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_id"] = $row[csf('issue_id')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_number"] = $row[csf('issue_number')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["job_no"] = $row[csf('job_no')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["style_ref_no"] = $row[csf('style_ref_no')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["buyer_name"] = $row[csf('buyer_name')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_date"] = $row[csf('issue_date')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["challan_no"] = $row[csf('challan_no')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["lot"] = $row[csf('lot')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["brand_id"] = $row[csf('brand_id')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["supplier_id"] = $row[csf('supplier_id')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["issue_qnty"] += $row[csf('issue_qnty')];
				$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["cons_rate"] = $row[csf('cons_rate')];



				$booking_no = explode('-',$row[csf("booking_no")]);
				if($booking_no[1]=='Fb' || $booking_no[1]=='FB')
				{
					$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["booking_no"] = $row[csf('booking_no')];
					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["book_qty"] = $booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["book_qnty"];
					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["booking_date"] = $booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["booking_date"];
					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["is_short"] = $booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["is_short"];

					$groupKeyArr[$groupKey]['required_qty'] =$booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["book_qnty"];
					if($booking_noChk[$row[csf("booking_no")]]=="")
					{
						$booking_noChk[$row[csf("booking_no")]] = $row[csf("booking_no")];
						$s_book_qnty += $booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["s_book_qnty"];
						$m_book_qnty += $booking_info_arr[$row[csf("job_no")]][$row[csf("booking_no")]]["m_book_qnty"];
					}


					if($row[csf("issue_purpose")]==1)
					{
						$knitting_book_qnty += $row[csf("issue_qnty")];
					}
					else if($row[csf("issue_purpose")]==2)
					{
						$yDyeing_book_qnty += $row[csf("issue_qnty")];
					}

					if($booking_noChk[$row[csf("booking_no")]]=="")
					{
						$booking_noChk[$row[csf("booking_no")]] = $row[csf("booking_no")];
						$fin_fab_qnty +=$fabric_data_arr[$row[csf('booking_no')]]['fin_fab_qnty'];
						$grey_fab_qnty +=$fabric_data_arr[$row[csf('booking_no')]]['grey_fab_qnty'];
					}


				}
				else if($booking_no[1]=='YDW')
				{
					$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["ydw_no"] = $row[csf('booking_no')];

					$ydwbookin_no = $ydw_arr[$row[csf("job_no")]][$row[csf('booking_no')]]["fab_booking_no"];
					$groupdataArr[$row[csf('job_no')]][$row[csf('booking_no')]][$groupKey][$issue_id][$product_id]["booking_no"] =$ydwbookin_no;

					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["book_qty"] = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["book_qnty"];
					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["booking_date"] = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["booking_date"];
					$requiredInfoArr[$row[csf('job_no')]][$row[csf('booking_no')]]["is_short"] = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["is_short"];

					$groupKeyArr[$groupKey]['required_qty'] = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["book_qnty"];

					if($ydwbookin_noChk[$ydwbookin_no]=="")
					{
						$ydwbookin_noChk[$ydwbookin_no] = $ydwbookin_no;
						$s_book_qnty = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["s_book_qnty"];
						$m_book_qnty = $booking_info_arr[$row[csf("job_no")]][$ydwbookin_no]["m_book_qnty"];
					}



					if($row[csf("issue_purpose")]==1)
					{
						$knitting_book_qnty += $row[csf("issue_qnty")];
					}
					else if($row[csf("issue_purpose")]==2)
					{
						$yDyeing_book_qnty += $row[csf("issue_qnty")];
					}

					if($booking_noChk[$ydwbookin_no]=="")
					{
						$booking_noChk[$ydwbookin_no] = $ydwbookin_no;
						$fin_fab_qnty +=$fabric_data_arr[$ydwbookin_no]['fin_fab_qnty'];
						$grey_fab_qnty +=$fabric_data_arr[$ydwbookin_no]['grey_fab_qnty'];
					}

				}



			}

			$po_data[$row[csf('booking_no')]]['poshipdate'].=$row[csf('pub_shipment_date')].',';
			$issue_ids .=$row[csf("issue_id")].",";


		}
		unset($result_issue);
		//echo "<pre>";print_r($groupdataArr);die;

	}



	$issue_ids =chop($issue_ids,",");
	$issue_ids=implode(",",array_filter(array_unique(explode(",",$issue_ids))));

	// ================= Issue Return ===============//
	if($issue_ids!="")
	{
		$issue_ids=explode(",",$issue_ids);
		$issue_ids_chnk=array_chunk($issue_ids,999);
		$issue_no_cond=" and";
		foreach($issue_ids_chnk as $issueId)
		{
			if($issue_no_cond==" and")  $issue_no_cond.="(c.id in(".implode(',',$issueId).")"; else $issue_no_cond.=" or c.id in(".implode(',',$issueId).")";
		}
		$issue_no_cond.=")";
		//echo $issue_no_cond;die;
		// echo "SELECT a.recv_number,a.booking_no, b.cons_quantity, c.id as issue_id,b.id as trans_id,d.brand,d.id as product_id,d.lot,d.yarn_type, d.yarn_count_id, d.yarn_comp_type1st,d.yarn_comp_percent1st from inv_receive_master a, inv_transaction b, inv_issue_master c,product_details_master d where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and b.prod_id=d.id and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 $issue_no_cond";
		$issue_return_res = sql_select("SELECT a.recv_number,a.booking_no, b.cons_quantity, b.issue_id,b.id as trans_id,d.brand,d.id as product_id,d.lot,d.yarn_type, d.yarn_count_id, d.yarn_comp_type1st,d.yarn_comp_percent1st from inv_receive_master a, inv_transaction b, inv_issue_master c,product_details_master d where a.id = b.mst_id and a.issue_id = c.id and b.transaction_type = 4 and b.item_category = 1 and b.prod_id=d.id and a.entry_form = 9 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 $issue_no_cond");
	}

	$transIdChk = array();
	foreach ($issue_return_res as $val)
	{
		if($transIdChk[$val[csf("trans_id")]]=="")
		{
			$transIdChk[$val[csf("trans_id")]] = $val[csf("trans_id")];

			$compPercent = $val[csf('yarn_comp_percent1st')];
			$yanrType = $val[csf('yarn_type')];
			$yarnComposition = $val[csf('yarn_comp_type1st')];
			$yarnCount = $val[csf('yarn_count_id')];
			$prod_id=$val[csf('product_id')];
			$issue_id=$val[csf('issue_id')];

			$groupKey = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType;
			$issue_return_qnty_arr[$issue_id][$groupKey][$val[csf('issue_id')]][$prod_id] += $val[csf("cons_quantity")];

		}
	}


	$usd_arr = array();
    $sqlSelectData = sql_select("select con_date,conversion_rate from currency_conversion_rate where currency=2 and is_deleted=0 order by con_date desc");
    foreach ($sqlSelectData as $row)
    {
        $usd_arr[date('d-m-Y',strtotime($row[csf('con_date')]))] = $row[csf('conversion_rate')];
    }


 	ob_start();
	?>
     <fieldset style="width:1350px;" >

            <table cellpadding="0" cellspacing="0" width="1330" style="float: left;">
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>"><p  style="font-size:20px;"><? echo $report_title; ?></p></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" ><p style="font-size:18px"><? echo $company_arr[str_replace("'", "", $cbo_company_id)]; ?></p></td>
                </tr>

            </table>

            <table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                <thead>
                    <th width="30" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >SL</p></th>
                    <th width="200" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Name</p></th>
                    <th width="230" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >LOT::Brand::Supplier</th>
                    <th width="150" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Required Qty<br>
					<? if($cbo_search_by==4){?> [<span >Sample</span>]<?} else {?>
					[Yarn+<span style="color: #FF0000;">Short</span>] <? } ?></p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Allocated Qty</p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Allocated Bal</p></th>
                    <th width="120" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Issued Qty</p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Issue Return Qty</p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Issue Bal</p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Unit Price <br>(USD)</p></th>
                    <th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >TTL Value <br>(USD)</p></th>
                </thead>
            </table>
            <div style="width:1350px; overflow-y: scroll; max-height:380px; float: left; margin-bottom:20px" id="scroll_body">
                <table width="1330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;">
                    <tbody>
                        <?
                        $i=1;
						$groupKey_count = 0;
                        $g_issue_subtotal_qnty = 0;
						$g_issue_rtn_subtotal_qnty = 0;
						$g_issue_bal_subtotal_qnty = 0;
						$g_amount_subtotal_qnty = 0;
						$g_required_total_qnty = 0;

                        foreach ($groupdataArr as $jobKey=>$jobData)
                        {
                            foreach ($jobData as $bookingkey=>$bookingData)
                            {
                                foreach ($bookingData as $groupKey=>$groupData)
                                {
                                    foreach ($groupData as $issueKey=>$issueData)
                                    {
										foreach ($issueData as $prod_id=>$row)
										{
											if($groupKeyChk[$groupKey]=="")
											{
												$groupKeyChk[$groupKey] = $groupKey;
												$groupKey_count += count($groupKey);
											}
											if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

											$compPercent = $row['yarn_comp_percent1st'];
											$yanrType = $row['yarn_type'];
											$yarnComposition = $row['yarn_comp_type1st'];
											$yarnCount = $row['yarn_count_id'];
											$issueDate = $row['issue_date'];

											$caption = $yarnCount."**".$yarnComposition."**".$compPercent."**".$yanrType;


											if($row['ydw_no'])
											{
												$ydw_booking = ' ( '.$row['ydw_no'].' )';
											}
											else
											{
												$ydw_booking = '';
											}

											$exchangeRate=$usd_arr[date('d-m-Y',strtotime($row['issue_date']))];
											if($exchangeRate =="")
											{
												foreach ($usd_arr as $rate_date => $rat)
												{
													if(strtotime($rate_date) <= strtotime($row['issue_date']))
													{
														$rate_date = date('d-m-Y',strtotime($rate_date));
														$exchangeRate=$rat;
														break;
													}
												}
											}
											//echo $groupKey."==".$issueId."|<br>";
											$issue_return_qnty = $issue_return_qnty_arr[ $issueKey][$groupKey][$issueKey][$prod_id];

											//echo $exchangeRate;

											//$amount = $row[csf('issue_qnty')] * $exchangeRate;
											$amount =  ( ($row['issue_qnty']-$issue_return_qnty) * number_format($row['cons_rate']/$exchangeRate, 2) );
											$requiredQty = $requiredInfoArr[$jobKey][$bookingkey]["book_qty"];
											$booking_date = $requiredInfoArr[$jobKey][$bookingkey]["booking_date"];
											$is_short = $requiredInfoArr[$jobKey][$bookingkey]["is_short"];
											$booking_type = '';
											if ($is_short == 1)
											{
												$booking_type = 'Short';
												$txtcolor = "#FF0000";
											}
											else if ($is_short == 'SMN')
											{
												$booking_type = 'Sample';
												//$txtcolor = "#0e2ab5";
											}
											else
											{
												$booking_type = 'Main';
												$txtcolor = "#000000";
											}

											$dates=array_unique(array_filter(explode(",",$po_data[$bookingkey]['poshipdate'])));

											$fs_date='';
											$ls_date='';
											foreach ($dates as $key => $val) {
												if(empty($fs_date))
												{
													$fs_date=strtotime('21-01-2025');
												}
												$curDate = strtotime($val);
												if ($curDate > $ls_date) {
													$ls_date = $curDate;
												}
												if ($curDate < $fs_date) {
													$fs_date = $curDate;
												}

											}

											if($cbo_search_by !=4)
											{
												$shiping_status = $shipingStatusArr[$row['job_no']]["shiping_status"];

												if($shiping_status)
												{
													$shiping_sts = '::Ship Date:F=( '.change_date_format(date('d-M-y',$fs_date)).' ), L=( '.change_date_format(date('d-M-y',$ls_date)).' )'.'::  Knit Closed:'.'<span style="color:red">No</span>'.'::  Grey Closed:'.'<span style="color:red">No</span>::Job Closed:<span style="color:red">No</span>';
												}
												else
												{
													$shiping_sts = '::Ship Date:F=( '.change_date_format(date('d-M-y',$fs_date)).' ), L=( '.change_date_format(date('d-M-y',$ls_date)).' )'.'::  Knit Closed:'.'<span style="color:red">No</span>'.'::  Grey Closed:'.'<span style="color:red">No</span>::Job Closed:<span style="color:green">Yes</span>';
												}
											}



											if(!in_array($jobKey."**".$bookingkey."**".$groupKey,$yarnDescriptionArr))
											{

												if($i!=1)
												{
													?>
													<tr bgcolor="#CCCCCC">
														<td colspan="3" align="right"><b>Sub Total : </b></td>
														<td align="right"><b><?php echo number_format($required_subtotal_qnty,2,'.',''); ?></b></td>
														<td align="right">&nbsp;</td>
														<td align="right">&nbsp;</td>
														<td align="right"><b><?php echo number_format($issue_subtotal_qnty,2,'.',''); ?></b></td>

														<td align="right"><b><?php echo number_format($issue_rtn_subtotal_qnty,2,'.',''); ?></b></td>
														<td align="right">
															<b>
																<?php
																$issue_bal_subtotal_qnty = ($required_subtotal_qnty-$issue_subtotal_qnty)+$issue_rtn_subtotal_qnty;
																echo number_format($issue_bal_subtotal_qnty,2,'.','');
																$g_issue_bal_subtotal_qnty += $issue_bal_subtotal_qnty;
																?>
															</b>
														</td>
														<td>&nbsp;</td>
														<td align="right"><b><?php echo number_format($amount_subtotal_qnty,2,'.',''); ?></b></td>
													</tr>
													<?
													$required_subtotal_qnty=0;
													$issue_subtotal_qnty=0;
													$issue_rtn_subtotal_qnty = 0;
													$issue_bal_subtotal_qnty = 0;
													$amount_subtotal_qnty = 0;
												}

												$yarnDescriptionArr[$i]=$jobKey."**".$bookingkey."**".$groupKey;


												?>
												<tr>
													<td rowspan="2">&nbsp;</td>
													<td colspan="10" bgcolor="#EEEEEE" title="<? echo $ydw_booking;?>">
														<b>
														<?php echo 'JOB :'.$row['job_no'].'::Style :'.$row['style_ref_no'].'::Buyer :'.$buyer_arr[$row['buyer_name']].'::Booking :'.$row['booking_no'].$ydw_booking.'::Fab Req Date:'.change_date_format($booking_date).$shiping_sts; ?>
														</b>
													</td>
												</tr>

												<tr>

													<td colspan="2" bgcolor="#EEEEEE">
														<b>
														<?php
														//var_dump($yarnDescriptionArr[$i]);
														$captionData = explode("**",$yarnDescriptionArr[$i]);
															echo $count_arr[$captionData[2]] . " ". $composition[$captionData[3]] . " ". $captionData[4] . "% ". $yarn_type[$captionData[5]];
															?>
														</b>
													</td>
													<td  bgcolor="#EEEEEE" align="right" title="<? echo $booking_type;?>">
														<p style="color:<? echo $txtcolor?>">
														 <? echo number_format($requiredQty,2); $required_subtotal_qnty += $requiredQty;

														 if($bookingKeyChk[$row['booking_no']]=="")
														 {
															$bookingKeyChk[$row['booking_no']] = $row['booking_no'];
															$g_required_total_qnty += $requiredQty;
														 }
														 ?>
														</p>

													</td>
													<td colspan="7" bgcolor="#EEEEEE"></td>
												</tr>
												<?
											}


											?>

											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">
												<td width="30" align="center" ><? echo $i; ?></td>
												<td width="200"  style="word-break: break-all;" title="<? echo 'Issue Number : '.$row['issue_number'];?>"><? echo 'CH NO: '.$row['challan_no'].'::'.change_date_format($row['issue_date']); ?>&nbsp;</td>
												<td width="230" style="word-break: break-all;" ><? echo 'Lot: '.$row['lot'].'::Brand: '.$brand_arr[$row['brand_id']].'::Supplier: '.$supplier_short_arr[$row['supplier_id']]; ?></td>
												<td width="150" style="word-break: break-all;"align="center" ></td>

												<td width="100" style="word-break: break-all;" >&nbsp;</td>
												<td width="100" style="word-break: break-all;" >&nbsp;</td>
												<td width="120" style="word-break: break-all;" align="right"><? echo number_format($row['issue_qnty'],2); ?></td>
												<td width="100" style="word-break: break-all;" align="right"><? echo number_format($issue_return_qnty,2); ?></td>
												<td width="100" style="word-break: break-all; " title='(Required Qty-Issued Qty+Issue Return Qty)' align="right">
												<?

												$issue_bal = ($requiredQty-$row['issue_qnty'])+$issue_return_qnty;
												echo number_format($issue_bal,2);
												?>
												</td>
												<td width="100" style="word-break: break-all; " align="right"><? echo number_format($row['cons_rate']/$exchangeRate, 2); ?></td>
												<td width="100" style="word-break: break-all; " align="right" title="<? echo $row['cons_rate'].'='.$exchangeRate;?>"><? echo number_format($amount,2); ?></td>

											</tr>
											<?

   											$issue_subtotal_qnty += $row['issue_qnty'];
											$issue_rtn_subtotal_qnty += $issue_return_qnty;
											//$issue_bal_subtotal_qnty += $issue_bal;
											$amount_subtotal_qnty += $amount;

											$g_issue_subtotal_qnty += $row['issue_qnty'];
											$g_issue_rtn_subtotal_qnty += $issue_return_qnty;
											//$g_issue_bal_subtotal_qnty += $issue_bal;
											$g_amount_subtotal_qnty += $amount;

											$i++;
										}
                                    }
                                }
                            }
                        }
                        ?>
						<tr bgcolor="#CCCCCC">
							<td colspan="2" align="left"><b>TTL Yarn - ( <? echo $groupKey_count;?> ) </b></td>
							<td align="right"><b>Sub Total : </b></td>
							<td align="right"><b><?php echo number_format($required_subtotal_qnty,2,'.',''); ?></b></td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right"><b><?php echo number_format($issue_subtotal_qnty,2,'.',''); ?></b></td>

							<td align="right"><b><?php echo number_format($issue_rtn_subtotal_qnty,2,'.',''); ?></b></td>
							<td align="right">
								<b>
									<?php
									$issue_bal_subtotal_qnty = ($required_subtotal_qnty-$issue_subtotal_qnty)+$issue_rtn_subtotal_qnty;
									echo number_format($issue_bal_subtotal_qnty,2,'.','');
									$g_issue_bal_subtotal_qnty += $issue_bal_subtotal_qnty;
									?>
							</b></td>
							<td>&nbsp;</td>
							<td align="right"><b><?php echo number_format($amount_subtotal_qnty,2,'.',''); ?></b></td>
						</tr>

						<tr bgcolor="#CCCCCC">
							<td colspan="3" align="right"><b>Grand Total : </b></td>
							<td align="right"><b><?php echo number_format($g_required_total_qnty,2,'.',''); ?></b></td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right"><b><?php echo number_format($g_issue_subtotal_qnty,2,'.',''); ?></b></td>

							<td align="right"><b><?php echo number_format($g_issue_rtn_subtotal_qnty,2,'.',''); ?></b></td>
							<td align="right"><b><?php echo number_format(($g_required_total_qnty-$g_issue_subtotal_qnty)+$g_issue_rtn_subtotal_qnty,2,'.',''); ?></b></td>
							<td>&nbsp;</td>
							<td align="right"><b><?php echo number_format($g_amount_subtotal_qnty,2,'.',''); ?></b></td>
						</tr>

                </table>
            </div>
			<br><br>


			<div style="margin-top: 20px;padding-top: 20px;">

				<table cellpadding="0" width="400" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th colspan="2"><p style="font-size:18px; font-weight:normal" >Summary</p></th>
						</tr>
						<tr>
                   			<th width="300" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Particulars</p></th>
                   			<th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Total Qnty</p></th>
						</tr>
					</thead>
					<tbody>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Required Fab Booking</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($m_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Required Short Fab</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($s_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Allocation</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;" >Total Yarn Received</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 16px tahoma; font-weight:bold">Total Yarn Received Balance</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Issued To Knitting</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($knitting_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Issued To Sample Knitting</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($knitting_smn_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Yarn Issued To Yarn Dyeing</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($yDyeing_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma; font-weight:bold">Total Yarn Issue Balance</td>
							<td align="right" style="font: 14px tahoma; font-weight:bold"><? echo number_format($knitting_book_qnty+$yDyeing_book_qnty+$knitting_smn_book_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Grey Fabric Required</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($grey_fab_qnty,2);?>&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Grey Fabric Received</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma; font-weight:bold">Total Grey Fabric Rcvd Balance</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Grey Fabric Issued To Dye</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma; font-weight:bold">Total Grey Fabric Issue Balance</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Finish Fabric Required</td>
							<td align="right" style="font: 14px tahoma;"><? echo number_format($fin_fab_qnty,2);?> &nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Finish Fabric Received</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma; font-weight:bold">Total Finish Fabric Received Balance</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Finish Fabric Issued To Cut</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
						<tr bgcolor="#E9F3FF" >
							<td style="font: 14px tahoma;">Total Finish Fabric Stock In Hand</td>
							<td align="right" style="font: 14px tahoma;">&nbsp;</td>
						</tr>
					</tbody>
				</table>


				<table cellpadding="0" width="3" cellspacing="0" align="left" rules="all" border="1">
					<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
				</table>

				<? if($cbo_search_by==4){ ?>
				<table cellpadding="0" width="500" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th colspan="5" ><p  style="font-size:18px; font-weight:normal" > Requirement as per Budget</p></th>
						</tr>
						<tr>
							<th width="30" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >SL</p></th>
                   			<th width="70" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Count</p></th>
                   			<th width="200" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Composition</p></th>
                   			<th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Type</p></th>
                   			<th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Required</p></th>
						</tr>
					</thead>
					<tbody>
						<?
						$i=1;
						$y_count=0;
						$total_yarn_require_qty = 0;
						foreach ($smnbudgetSummaryArr as $k_count => $v_count)
						{
							foreach ($v_count as $k_type => $v_type)
							{
								foreach ($v_type as $k_copm_one => $v_copm_one)
								{
										foreach ($v_copm_one as $k_percent_one => $data)
										{
											$y_count +=count($k_count);
											$total_yarn_require_qty += $data['req_quantity'];
											$compos = $composition[$k_copm_one]." ".$k_percent_one." %";
											?>
											<tr bgcolor="<? echo "#FFFFFF"; ?>">
												<td><? echo $i;?></td>
												<td><? echo $count_arr[$k_count];?></td>
												<td><? echo $compos;?></td>
												<td><? echo $yarn_type[$k_type];?></td>
												<td align="right"><? echo number_format($data['req_quantity'],2,'.','');?></td>
											</tr>
											<?
											$i++;
										}
								}
							}


						}
						?>

					</tbody>
					<tfoot>
						<tr bgcolor="#CCCCCC">
							<td colspan="4" align="right"><b>TTL Yarn - ( <? echo $y_count;?> ) :</b></td>
							<td align="right"><b><? echo number_format($total_yarn_require_qty,2)?></b></td>
						</tr>
					</tfoot>
				</table>
				<? }else {?>
				<table cellpadding="0" width="500" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th colspan="5" ><p  style="font-size:18px; font-weight:normal" >Requirement as per Budget</p></th>
						</tr>
						<tr>
							<th width="30" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >SL</p></th>
                   			<th width="70" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Count</p></th>
                   			<th width="200" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Composition</p></th>
                   			<th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Type</p></th>
                   			<th width="100" style="word-break: break-all;"><p style="font-size:16px; font-weight:normal" >Yarn Required</p></th>
						</tr>
					</thead>
					<tbody>
						<?
						$i=1;
						$y_count=0;
						$total_yarn_require_qty = 0;
						foreach ($budgetSummaryArr as $k_count => $v_count)
						{
							foreach ($v_count as $k_type => $v_type)
							{
								foreach ($v_type as $k_copm_one => $v_copm_one)
								{
									foreach ($v_copm_one as $k_copm_two => $v_copm_two)
									{
										foreach ($v_copm_two as $k_percent_one => $v_percent_one)
										{
											foreach ($v_percent_one as $k_percent_two => $data)
											{
												$y_count +=count($k_count);
												$total_yarn_require_qty += $data['req_quantity'];
												$compos = $composition[$k_copm_one]." ".$k_percent_one." %"." ".$composition[$k_copm_two];
												?>
												<tr bgcolor="<? echo "#FFFFFF"; ?>">
													<td><? echo $i;?></td>
													<td><? echo $count_arr[$k_count];?></td>
													<td><? echo $compos;?></td>
													<td><? echo $yarn_type[$k_type];?></td>
													<td align="right"><? echo number_format($data['req_quantity'],2,'.','');?></td>
												</tr>
												<?
												$i++;
											}
										}
									}
								}
							}


						}
						?>

					</tbody>
					<tfoot>
						<tr bgcolor="#CCCCCC">
							<td colspan="4" align="right"><b>TTL Yarn - ( <? echo $y_count;?> ) :</b></td>
							<td align="right"><b><? echo number_format($total_yarn_require_qty,2)?></b></td>
						</tr>
					</tfoot>
				</table>
				<? } ?>
			</div>
        </fieldset>
	<?
    foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action == "job_no_popup")
{
	echo load_html_head_contents("Job Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			$("#hidden_job_no_prefix_num").val(str);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<fieldset style="width:390px">
		<input type="hidden" name="hidden_job_no_prefix_num" id="hidden_job_no_prefix_num" value="">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">Job No</th>
					<th width="50">Job No Prefix</th>
					<th width="80">Job Year</th>
					<th width="">Style Ref.</th>
				</tr>
			</thead>
		</table>
		<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
			<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
		<?

		if($db_type==0) $year_field=" YEAR(insert_date) as year";
		else if($db_type==2) $year_field="  to_char(insert_date,'YYYY') as year";
		else $year_field="";
		$sql_job= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field  from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$companyId order by id desc";
		//echo $sql;die();
		$rslt_job = sql_select($sql_job);
		$i = 1;
		foreach ($rslt_job as $key=> $val)
		{
			//var_dump($val);
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $val[csf('job_no_prefix_num')]; ?>')" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
				<td width="30"><? echo $i; ?></td>
				<td width="100"><p><? echo $val[csf('job_no')]; ?></p></td>
				<td width="50"><p><? echo $val[csf('job_no_prefix_num')]; ?></p></td>
				<td width="80"><p><? echo $val[csf('year')]; ?></p></td>
				<td width=""><p><? echo $val[csf('style_ref_no')]; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>

		</table>
		</div>
	</fieldset>
	<script type="text/javascript">
		setFilterGrid('table_body',-1);
	</script>
	<?
}
if($action == "style_ref_popup")
{
	echo load_html_head_contents("Job Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			$("#hidden_style_ref_no").val(str);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<fieldset style="width:390px">
		<input type="hidden" name="hidden_style_ref_no" id="hidden_style_ref_no" value="">

		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">Job No</th>
					<th width="50">Job No Prefix</th>
					<th width="80">Job Year</th>
					<th width="">Style Ref.</th>
				</tr>
			</thead>
		</table>
		<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
			<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
		<?

		if($db_type==0) $year_field=" YEAR(insert_date) as year";
		else if($db_type==2) $year_field="  to_char(insert_date,'YYYY') as year";
		else $year_field="";
		$sql_job= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field  from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$companyId order by id desc";
		//echo $sql;die();
		$rslt_job = sql_select($sql_job);
		$i = 1;
		foreach ($rslt_job as $key=> $val)
		{
			//var_dump($val);
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo$val[csf('style_ref_no')]; ?>')" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
				<td width="30"><? echo $i; ?></td>
				<td width="100"><p><? echo $val[csf('job_no')]; ?></p></td>
				<td width="50"><p><? echo $val[csf('job_no_prefix_num')]; ?></p></td>
				<td width="80"><p><? echo $val[csf('year')]; ?></p></td>
				<td width=""><p><? echo $val[csf('style_ref_no')]; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>

		</table>
		</div>
	</fieldset>
	<script type="text/javascript">
		setFilterGrid('table_body',-1);
	</script>
	<?
}

if($action == "booking_popup")
{
	echo load_html_head_contents("Booking Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			$("#hidden_booking_no").val(str);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<fieldset style="width:390px">
		<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" value="">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="130">Booking No</th>
					<th width="100">Booking No Prefix </th>
					<th width="">Booking Year </th>
				</tr>
			</thead>
		</table>
		<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
			<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
		<?

		if($db_type==0) $year_field=" YEAR(insert_date) as year";
		else if($db_type==2) $year_field="  to_char(insert_date,'YYYY') as year";
		else $year_field="";
		$sql_booking= "SELECT id,booking_no,booking_no_prefix_num, $year_field from wo_booking_mst where company_id=$companyId  and status_active=1 and is_deleted=0 and booking_type=1 order by id desc";
		//echo $sql_booking;die;
		$rslt_booking = sql_select($sql_booking);
		$i = 1;
		foreach ($rslt_booking as $key=> $val)
		{
			//var_dump($val);
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $val[csf('booking_no')]; ?>')" style="text-decoration:none; cursor:pointer" >
				<td width="30"><? echo $i; ?></td>
				<td width="130"><p><? echo $val[csf('booking_no')]; ?></p></td>
				<td width="100"><p><? echo $val[csf('booking_no_prefix_num')]; ?></p></td>
				<td width=""><p><? echo $val[csf('year')]; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>

		</table>
		</div>
	</fieldset>
	<script type="text/javascript">
		setFilterGrid('table_body',-1);
	</script>
	<?
}

if($action == "smn_booking_popup")
{
	echo load_html_head_contents("Sample Booking Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{

			$("#hidden_smn_booking_no").val(str);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<fieldset style="width:390px">
		<input type="hidden" name="hidden_smn_booking_no" id="hidden_smn_booking_no" value="">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="130">Booking No</th>
					<th width="100">Booking No Prefix </th>
					<th width="">Booking Year </th>
				</tr>
			</thead>
		</table>
		<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
			<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
		<?

		if($db_type==0) $year_field=" YEAR(insert_date) as year";
		else if($db_type==2) $year_field="  to_char(insert_date,'YYYY') as year";
		else $year_field="";
		$sql_smn_booking= "SELECT id,booking_no,booking_no_prefix_num, $year_field from wo_non_ord_samp_booking_mst where company_id=$companyId and status_active=1 and is_deleted=0 and booking_type=4 order by id desc";
		//echo $sql_smn_booking;die;
		$rslt_smn_booking = sql_select($sql_smn_booking);
		$i = 1;
		foreach ($rslt_smn_booking as $key=> $val)
		{
			//var_dump($val);
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $val[csf('booking_no')];?>')" style="text-decoration:none; cursor:pointer" >
				<td width="30"><? echo $i; ?></td>
				<td width="130"><p><? echo $val[csf('booking_no')]; ?></p></td>
				<td width="100"><p><? echo $val[csf('booking_no_prefix_num')]; ?></p></td>
				<td width=""><p><? echo $val[csf('year')]; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>

		</table>
		</div>
	</fieldset>
	<script type="text/javascript">
		setFilterGrid('table_body',-1);
	</script>
	<?
}
