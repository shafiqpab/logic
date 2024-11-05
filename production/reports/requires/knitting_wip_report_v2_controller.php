<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
    extract($_REQUEST);
    $choosenCompany = $data;
	echo create_drop_down( "cbo_lc_location_name", 140, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $choosenCompany) group by id,location_name  order by location_name","id,location_name", 1, "-- Select location --", $selected, "" );
	exit();
}

if ($action == "load_drop_down_party")
{
	$data = explode("_", $data);
	$company_id = $data[1];

	//$company_id
	if ($data[0] == 1)
	{
		echo create_drop_down("cbo_party_name", 130, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", "", "", 0);
	}
	else if ($data[0] == 3)
	{
		echo create_drop_down("cbo_party_name", 130, "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Supplier--", 0, "");
	}
	else
	{
		echo create_drop_down("cbo_party_name", 130, $blank_array, "", 1, "--Select Company--", 0, "");
	}
	exit();
}

/* if ($action=="load_drop_down_wc_location")
{
    extract($_REQUEST);
    $choosenCompany = $data;
	echo create_drop_down( "cbo_wc_location_name", 140, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $choosenCompany) group by id,location_name  order by location_name","id,location_name", 1, "-- Select location --", $selected, "" ,0);
	exit();
} */

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name"  ); // and b.party_type=23

	// ============================== GETTING FORM DATA ================================

	$lc_company_id 	= str_replace("'", "", $cbo_company_name);
	$lc_location_id = str_replace("'", "", $cbo_lc_location_name);
	$source 		= str_replace("'", "", $cbo_source);
	$party_id 		= str_replace("'", "", $cbo_party_name);
	$wc_location_id = str_replace("'", "", $cbo_wc_location_name);
	$job_no 	    = str_replace("'", "", $txt_job_no);
	$smn_booking_no = str_replace("'", "", $txt_smn_booking_no);
	$type 			= str_replace("'","",$type);
	$search_type 	= str_replace("'","",$search_type);
	$cbo_year 	    = str_replace("'","",$cbo_year);

	if($db_type==0)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'yyyy-mm-dd');
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'yyyy-mm-dd');
	}
	elseif($db_type==2)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'','',1);
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'','',1);
	}

	//echo $to_date;

	// ====================================== MAKING QUERY CON =============================
	$company_cond 		 = ($lc_company_id) ? " and a.company_id = $lc_company_id" : "";
	$location_cond 		 = ($lc_location_id) ? " and a.location_id = $lc_location_id" : "";
	$source_cond 		 = ($source) ? " and a.knit_dye_source = $source" : "";
	$party_cond 		 = ($party_id) ? " and a.knit_dye_company = $party_id" : "";
	$smn_booking_no_cond = ($smn_booking_no) ? " and c.booking_no like '%$smn_booking_no%' " : "";
    $job_no_cond 		 = ($job_no) ? " and d.job_no_mst like '%$job_no%' " : "";


	if(trim($cbo_year)!=0)
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(b.TRANSACTION_DATE)=$cbo_year";
		}
		else if($db_type==2)
		{
			$year_cond=" and to_char(b.TRANSACTION_DATE,'YYYY')=$cbo_year";
		}
		else
		{
			$year_cond="";
		}
	}
	else $year_cond="";

	if($search_type==1)
	{
		$date_cond = " and b.transaction_date between '$from_date' and '$to_date'";
	}
	else
	{

		$as_on_date_cond = " and b.transaction_date < '$from_date'";
	}


	// ====================================== MAIN QUERY ====================================

	$con = connect();
	$r_id1=execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3,4,5,6) and ENTRY_FORM = 181");
	if($r_id1)
	{
		oci_commit($con);
	}

	if(!empty($smn_booking_no))
	{
		$sql_planbooking = "SELECT a.requisition_no,c.booking_no from  ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c where a.knit_id=b.id and b.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $smn_booking_no_cond group by a.requisition_no,c.booking_no";
		//echo $sql_planbooking;die;
		$sql_planbooking_res = sql_select($sql_planbooking);


		$reqNoChk = array();
		$all_reqNo_arr = array();
		foreach ($sql_planbooking_res as $val)
		{
			if($reqNoChk[$val[csf('requisition_no')]] == "")
			{
				$reqNoChk[$val[csf('requisition_no')]] = $val[csf('requisition_no')];
				$all_reqNo_arr[$val[csf('requisition_no')]] = $val[csf('requisition_no')];
			}
		}

		if(!empty($all_reqNo_arr))
		{
			$reqNoCond = "".where_con_using_array($all_reqNo_arr,0,'b.requisition_no')."";
		}
	}

	if(empty($job_no_cond))
	{
		$smn_issue_sql = "SELECT a.issue_number,a.knit_dye_source as source, a.knit_dye_company as party,a.issue_basis, a.issue_basis,  b.cons_uom, b.cons_quantity, b.return_qnty, b.requisition_no, b.prod_id, a.booking_no, c.lot,c.product_name_details,g.fabric_desc,g.determination_id from inv_issue_master a, inv_transaction b, product_details_master c,ppl_yarn_requisition_entry  d,ppl_planning_info_entry_mst e,ppl_planning_info_entry_dtls f,ppl_planning_entry_plan_dtls g where a.id=b.mst_id and b.prod_id = c.id and b.requisition_no = d.requisition_no
		and d.knit_id=f.id and e.id = f.mst_id and f.id = g.dtls_id and e.id=g.mst_id  and a.issue_basis in(3) and a.item_category=1 and b.item_category=1 and a.issue_purpose=8 and b.transaction_type=2 and knit_dye_source in(1,3)  $reqNoCond $smn_booking_no_cond1 $company_cond $location_cond $source_cond $party_cond $date_cond  $as_on_date_cond $year_cond group by a.issue_number,a.knit_dye_source, a.knit_dye_company, a.issue_basis, b.cons_uom, b.cons_quantity, b.return_qnty, b.requisition_no, b.prod_id, a.booking_no, c.lot,c.product_name_details,g.fabric_desc,g.determination_id  order by a.knit_dye_source";
		//echo $smn_issue_sql;die;

		$smn_issue_sql_res = sql_select($smn_issue_sql);

		$smnReqNoChk = array();
		$all_smnReqNo_arr = array();
		foreach ($smn_issue_sql_res as $val)
		{
			if(!empty($val[csf('requisition_no')]))
			{
				if($smnReqNoChk[$val[csf('requisition_no')]] == "")
				{
					$smnReqNoChk[$val[csf('requisition_no')]] = $val[csf('requisition_no')];
					$all_smnReqNo_arr[$val[csf('requisition_no')]] = $val[csf('requisition_no')];
				}
			}

		}

		$all_smnReqNo_arr = array_filter($all_smnReqNo_arr);
		if(!empty($all_smnReqNo_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 181, 4,$all_smnReqNo_arr, $empty_arr); //recv id
			//die;

			$sql_smn_planbooking = "SELECT a.requisition_no, b.id as program_no, c.booking_no,c.determination_id from  ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c, gbl_temp_engine x where a.knit_id=b.id and b.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.requisition_no=x.ref_val and x.user_id=$user_id and x.entry_form=181 and x.ref_from=4 group by a.requisition_no, b.id, c.booking_no,c.determination_id";
			//echo $sql_smn_planbooking;die;
			$sql_smn_planbooking_res = sql_select($sql_smn_planbooking);
			$smn_booking_info_arr = array();
			$programNoChk = array();
			$all_programNo_arr = array();
			$smn_program_info_arr = array();
			foreach($sql_smn_planbooking_res as $val)
			{
				$smn_booking_info_arr[$val[csf('requisition_no')]]['booking_no'] = $val[csf('booking_no')];
				$smn_program_info_arr[$val[csf('booking_no')]][$val[csf('determination_id')]]['program_no'] .= $val[csf('program_no')].',';

				if($programNoChk[$val[csf('program_no')]] == "")
				{
					$programNoChk[$val[csf('program_no')]] = $val[csf('program_no')];
					$all_programNo_arr[$val[csf('program_no')]] = $val[csf('program_no')];
				}
			}
			unset($sql_smn_planbooking_res);
			//echo "<pre>";print_r($smn_program_info_arr);


		}

		$smn_main_array = array();
		$smnProdIdChk = array();
		$all_smnprodId_arr = array();
		foreach ($smn_issue_sql_res as $val)
		{
			if($val[csf('issue_basis')]==1)
			{
				$booking_no = $val[csf('booking_no')];
			}
			else
			{
				$booking_no = $smn_booking_info_arr[$val[csf('requisition_no')]]['booking_no'];
			}
			$smn_main_array[$val[csf('source')]][$val[csf('party')]][$booking_no][$val[csf('determination_id')]][$val[csf('prod_id')]]['uom'] = $val[csf('cons_uom')];
			$smn_main_array[$val[csf('source')]][$val[csf('party')]][$booking_no][$val[csf('determination_id')]][$val[csf('prod_id')]]['issue_qty'] += $val[csf('cons_quantity')];
			$smn_main_array[$val[csf('source')]][$val[csf('party')]][$booking_no][$val[csf('determination_id')]][$val[csf('prod_id')]]['rtn_qty'] += $val[csf('return_qnty')];
			$smn_main_array[$val[csf('source')]][$val[csf('party')]][$booking_no][$val[csf('determination_id')]][$val[csf('prod_id')]]['product_name_details'] = $val[csf('product_name_details')];
			$smn_main_array[$val[csf('source')]][$val[csf('party')]][$booking_no][$val[csf('determination_id')]][$val[csf('prod_id')]]['lot'] = $val[csf('lot')];
			$smn_main_array[$val[csf('source')]][$val[csf('party')]][$booking_no][$val[csf('determination_id')]][$val[csf('prod_id')]]['fabric_desc'] = $val[csf('fabric_desc')];

			if($smnProdIdChk[$val[csf('prod_id')]] == "")
			{
				$smnProdIdChk[$val[csf('prod_id')]] = $val[csf('prod_id')];
				$all_smnprodId_arr[$val[csf('prod_id')]] = $val[csf('prod_id')];
			}

		}

		//echo "<pre>";print_r($smn_main_array);

		$all_smnprodId_arr = array_filter($all_smnprodId_arr);
		if(!empty($all_smnprodId_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 181, 5,$all_smnprodId_arr, $empty_arr); //recv id
			//die;

			if($search_type==1)
			{
				// ========================================== OPENING BALANCE (receive) ==========================================
				/* $sql_smn_opn_bal_rcv = "SELECT a.knitting_source as source, a.knitting_company as party, SUM(b.cons_quantity) as receive,b.prod_id, a.requisition_no,a.receive_basis,a.booking_no from inv_receive_master a, inv_transaction b, gbl_temp_engine x where a.item_category in(1) and a.entry_form in(9) and a.id=b.mst_id and b.transaction_date < '$from_date' and a.status_active=1 and b.transaction_type in (1,4,5) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.knitting_source in(1,3) and a.receive_basis in(1,3) and b.prod_id=x.ref_val and x.user_id=$user_id and x.entry_form=181 and x.ref_from=5 $company_cond $location_cond $source_cond_rcv $party_cond_rcv $year_cond group by a.knitting_source, a.knitting_company,b.prod_id,a.requisition_no,a.receive_basis,a.booking_no"; */

				$sql_smn_opn_bal_rcv = "SELECT a.recv_number, b.cons_quantity as receive,b.prod_id, a.requisition_no,a.receive_basis,a.booking_no from inv_receive_master a, inv_transaction b, gbl_temp_engine x where a.item_category in(1) and a.entry_form in(1,9,10) and a.id=b.mst_id and b.transaction_date < '$from_date' and a.status_active=1 and b.transaction_type in (1,4,5) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.knitting_source in(1,3) and b.prod_id=x.ref_val and x.user_id=$user_id and x.entry_form=181 and x.ref_from=5 $company_cond $location_cond $source_cond_rcv $party_cond_rcv $year_cond";

				//echo $sql_smn_opn_bal_rcv;die();
				$sql_smn_opn_bal_rcv_res = sql_select($sql_smn_opn_bal_rcv);
				$smn_opn_receive_array = array();
				foreach ($sql_smn_opn_bal_rcv_res as $val)
				{
					if($val[csf('receive_basis')]==1)
					{
						$booking_no = $val[csf('booking_no')];
					}
					else
					{
						$booking_no = $smn_booking_info_arr[$val[csf('requisition_no')]]['booking_no'];
					}

					//$smn_opn_receive_array[$val[csf('source')]][$val[csf('party')]][$booking_no][$val[csf('prod_id')]] = $val[csf('receive')];
					$smn_opn_receive_array[$val[csf('prod_id')]] += $val[csf('receive')];
				}
				unset($sql_smn_opn_bal_rcv_res);
				//echo "<pre>";print_r($smn_opn_receive_array);

				// ========================================== OPENING BALANCE (issue) ==========================================

				$sql_smn_opn_bal_issue = "SELECT a.issue_number, b.cons_quantity as issue, b.prod_id,b.requisition_no,a.issue_basis,a.booking_no from inv_issue_master a, inv_transaction b, gbl_temp_engine x where a.item_category=1 and a.entry_form=3 and a.id=b.mst_id and b.item_category=1 and b.transaction_type in (2,3) and b.transaction_date < '$from_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.issue_purpose=8 and b.prod_id=x.ref_val and x.user_id=$user_id and x.entry_form=181 and x.ref_from=5 $company_cond $location_cond $source_cond $party_cond $year_cond";

				//echo $sql_smn_opn_bal_issue;die();
				$sql_smn_opn_bal_issue_res = sql_select($sql_smn_opn_bal_issue);
				$smn_opn_issue_array = array();
				foreach($sql_smn_opn_bal_issue_res as $val)
				{
					if($val[csf('issue_basis')]==1)
					{
						$booking_no = $val[csf('booking_no')];
					}
					else
					{
						$booking_no = $smn_booking_info_arr[$val[csf('requisition_no')]]['booking_no'];
					}
					$smn_opn_issue_array[$val[csf('prod_id')]] += $val[csf('issue')];
				}
				unset($sql_opn_bal_issue_res);
				//echo "<pre>";print_r($opn_issue_array);
			}

			// =========================================== YARN RETURN ===================================
			$date_cond_rcv 	= str_replace("issue_date", "receive_date", $date_cond);
			$party_cond_rcv = str_replace("knit_dye_company", "knitting_company", $party_cond);
			$source_cond_rcv= str_replace("knit_dye_source", "knitting_source", $source_cond);

			$smn_sql_yarn_rec="SELECT a.knitting_source as source, a.knitting_company as party, sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty, sum(b.cons_reject_qnty) as cons_reject_qnty,a.entry_form,b.transaction_type,b.prod_id,a.requisition_no,a.receive_basis,a.booking_no
			from inv_receive_master a, inv_transaction b, gbl_temp_engine x
			where a.item_category in(1) and a.entry_form in(9) and a.id=b.mst_id and b.item_category in(1) and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id=x.ref_val and x.user_id=$user_id and x.entry_form=181 and x.ref_from=5 $company_cond $location_cond $source_cond_rcv $party_cond_rcv $date_cond_rcv $year_cond group by a.knitting_source, a.knitting_company,a.entry_form,b.transaction_type,b.prod_id,a.requisition_no,a.receive_basis,a.booking_no
			order by a.knitting_company";
			//echo $smn_sql_yarn_rec;die();
			$smn_sql_yarn_rec_res = sql_select($smn_sql_yarn_rec);
			$smn_yarn_rtn_array = array();
			foreach ($smn_sql_yarn_rec_res as $val)
			{
				if($val[csf('receive_basis')]==1)
				{
					$booking_no = $val[csf('booking_no')];
				}
				else
				{
					$booking_no = $smn_booking_info_arr[$val[csf('requisition_no')]]['booking_no'];
				}

				$smn_yarn_rtn_array[$val[csf('source')]][$val[csf('party')]][$booking_no][$val[csf('prod_id')]]['rtn_qty'] = $val[csf('cons_quantity')];
				$smn_yarn_rtn_array[$val[csf('source')]][$val[csf('party')]][$booking_no][$val[csf('prod_id')]]['rej_qty'] = $val[csf('cons_reject_qnty')];
			}
			unset($smn_sql_yarn_rec_res);

			//echo "<pre>";print_r($smn_yarn_rtn_array);

			$smn_yarn_avg_rate = "SELECT a.prod_id, a.order_rate
			from inv_transaction a, gbl_temp_engine x
			where a.item_category in(1) and a.item_category in(1) and a.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and a.prod_id=x.ref_val and x.user_id=$user_id and x.entry_form=181 and x.ref_from=5";
			//echo $smn_yarn_avg_rate;die;
			$smn_yarn_avg_rate_res = sql_select($smn_yarn_avg_rate);
			$smn_yarn_avg_rate_array = array();
			foreach ($smn_yarn_avg_rate_res as $val)
			{
				$smn_yarn_avg_rate_array[$val[csf('prod_id')]]['order_rate'] = $val[csf('order_rate')];
			}
			unset($smn_yarn_avg_rate_res);
			//echo "<pre>";print_r($smn_yarn_avg_rate_array);

		}

		// =========================================== Fabric Info for SMN ===================================

		$all_programNo_arr = array_filter($all_programNo_arr);
		if(!empty($all_programNo_arr))
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 181, 6,$all_programNo_arr, $empty_arr); //recv id
			//die;
			$sql_knit_prod = "SELECT a.id, a.booking_id as program_no, a.booking_no, a.knitting_source, a.knitting_company, c.barcode_no,c.qc_pass_qnty,c.reject_qnty
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, gbl_temp_engine x
			WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and a.item_category=13 and a.receive_basis=2 and c.status_active=1 and c.is_deleted=0 and a.booking_id=x.ref_val and x.user_id=$user_id and x.entry_form=181 and x.ref_from=6";
			//echo $sql_knit_prod;die;
			$sql_knit_prod_res = sql_select($sql_knit_prod);

			$smn_knitting_info_array = array();
			foreach($sql_knit_prod_res as $val)
			{
				$smn_knitting_info_array[$val[csf('knitting_source')]][$val[csf('program_no')]]['barcode_no'] .= $val[csf('barcode_no')].',';
				$smn_knitting_info_array[$val[csf('knitting_source')]][$val[csf('program_no')]]['qc_pass_qnty'] += $val[csf('qc_pass_qnty')];
				$smn_knitting_info_array[$val[csf('knitting_source')]][$val[csf('program_no')]]['reject_qnty'] += $val[csf('reject_qnty')];
			}
			unset($sql_knit_prod_res);
			//echo "<pre>";print_r($smn_knitting_info_array);

			/* $all_jobId_arr = array_filter($all_jobId_arr);
			if(!empty($all_jobId_arr))
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 181, 2,$all_jobId_arr, $empty_arr);  *///recv id
				//die;

				/* $date_cond_rcv 	= str_replace("issue_date", "receive_date", $date_cond);
				$party_cond_rcv = str_replace("knit_dye_company", "knitting_company", $party_cond);
				$source_cond_rcv= str_replace("knit_dye_source", "knitting_source", $source_cond);

				$smn_sql_fab_rec="SELECT a.knitting_source as source, a.knitting_company as party, sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty, sum(b.cons_reject_qnty) as cons_reject_qnty,a.entry_form,b.transaction_type,b.prod_id
				from inv_receive_master a, inv_transaction b, pro_roll_details c, gbl_temp_engine x
				where a.item_category in(13) and a.entry_form in(58) and a.id=b.mst_id and a.id=c.mst_id and c.status_active=1 and b.item_category in(13) and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_no=cast(x.ref_val as varchar2(4000)) and x.user_id=$user_id and x.entry_form=181 and x.ref_from=6 $company_cond $location_cond $source_cond_rcv $party_cond_rcv $date_cond_rcv $year_cond group by a.knitting_source, a.knitting_company,a.entry_form,b.transaction_type,b.prod_id
				";
				echo $smn_sql_fab_rec;die();
				$smn_sql_fab_rec_res = sql_select($smn_sql_fab_rec);
				$fab_rcv_array = array();
				foreach ($smn_sql_fab_rec_res as $val)
				{
					$fab_rcv_array[$val[csf('source')]][$val[csf('party')]][$val[csf('job_no_mst')]]['rtn_qty'] += $val[csf('cons_quantity')];
					$fab_rcv_array[$val[csf('source')]][$val[csf('party')]][$val[csf('job_no_mst')]]['rej_qty'] += $val[csf('cons_reject_qnty')];

				}
				unset($sql_fab_rec_res); */
			//}

			$sql_smn_material_used = "SELECT a.recv_number,b.febric_description_id,a.knitting_source,c.id,c.prod_id,c.used_qty,c.rate,c.amount,c.yarn_percentage,c.porcess_loss
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_material_used_dtls c, gbl_temp_engine x
			WHERE a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=13 and a.receive_basis=2 and a.booking_id=x.ref_val and x.user_id=$user_id and x.entry_form=181 and x.ref_from=6 ";
			//echo $sql_smn_material_used;die;

			$sql_smn_material_used_res = sql_select($sql_smn_material_used);
			$smn_used_yarn_qnty_arr = array();
			foreach ($sql_smn_material_used_res as $val)
			{
				$smn_used_yarn_qnty_arr[$val[csf('knitting_source')]][$val[csf('febric_description_id')]][$val[csf('prod_id')]]['used_qty'] += $val[csf('used_qty')];
			}
			unset($sql_smn_material_used_res);


		}

	}


	// $sql_iss = "SELECT a.issue_number,a.knit_dye_source as source,a.knit_dye_company as party,b.cons_uom,b.cons_quantity,b.return_qnty,b.prod_id,c.product_name_details,c.lot,h.job_no,e.fabric_desc,e.determination_id from inv_issue_master a,inv_transaction b,product_details_master c,
	// ppl_yarn_requisition_entry d,ppl_planning_info_entry_mst e,ppl_planning_info_entry_dtls f,wo_booking_mst h
	// where a.id = b.mst_id and b.requisition_no= d.requisition_no and d.knit_id=f.id and e.id = f.mst_id and e.booking_no = h.booking_no and b.prod_id = c.id and b.item_category = 1 and b.transaction_type = 2 and a.status_active = 1 and a.is_deleted = 0
	// and b.status_active = 1 and b.is_deleted = 0 and a.knit_dye_source in (1, 3) $company_cond $location_cond $source_cond $party_cond $job_no_cond $date_cond $as_on_date_cond $year_cond $reqNoCond
	// GROUP BY a.issue_number,a.knit_dye_source,a.knit_dye_company,b.cons_uom,b.prod_id,c.product_name_details,c.lot,h.job_no,e.fabric_desc,b.cons_quantity,b.return_qnty,e.determination_id ORDER BY a.knit_dye_source";



	/* $sql_iss="SELECT a.knit_dye_source as source, a.knit_dye_company as party,  b.cons_uom, sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty, d.job_no_mst,b.prod_id,e.product_name_details,e.lot, d.job_id,d.id as order_id
	from inv_issue_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, product_details_master e
	where a.id=b.mst_id and b.id=c.trans_id and c.po_breakdown_id=d.id and b.prod_id=e.id and b.transaction_type=c.trans_type and  a.item_category=1 and a.entry_form=3 and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1  and knit_dye_source in(1,3) $company_cond $location_cond $source_cond $party_cond $job_no_cond $date_cond $as_on_date_cond $year_cond $reqNoCond group by a.knit_dye_source, a.knit_dye_company, b.cons_uom, d.job_no_mst,b.prod_id,e.product_name_details,e.lot, d.job_id,d.id order by a.knit_dye_source"; */

	$sql_iss="SELECT a.issue_number,a.knit_dye_source as source, a.knit_dye_company as party,  b.cons_uom, b.cons_quantity, b.return_qnty, d.job_no_mst,b.prod_id,e.product_name_details,e.lot, d.job_id,d.id as order_id,i.fabric_desc,i.determination_id
	from inv_issue_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, product_details_master e,ppl_yarn_requisition_entry f,ppl_planning_info_entry_mst g,ppl_planning_info_entry_dtls h, ppl_planning_entry_plan_dtls i
	where a.id=b.mst_id and b.id=c.trans_id and c.po_breakdown_id=d.id and b.prod_id=e.id and b.transaction_type=c.trans_type and b.requisition_no= f.requisition_no and f.knit_id=h.id and g.id = h.mst_id and h.id = i.dtls_id and g.id=i.mst_id and  a.item_category=1 and a.entry_form=3 and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.issue_basis=3 and a.issue_purpose=1 and knit_dye_source in(1,3) $company_cond $location_cond $source_cond $party_cond $job_no_cond $date_cond $as_on_date_cond $year_cond $reqNoCond group by a.issue_number,a.knit_dye_source, a.knit_dye_company, b.cons_uom,b.cons_quantity, b.return_qnty, d.job_no_mst,b.prod_id,e.product_name_details,e.lot, d.job_id,d.id,i.fabric_desc,i.determination_id order by a.knit_dye_source";

	//echo $sql_iss;die();
	$sql_res = sql_select($sql_iss);
	$main_array = array();
	$prodIdChk = array();
	$all_prodId_arr = array();
	$jobIdChk = array();
	$all_jobId_arr = array();
	$orderIdChk = array();
	$all_orderId_arr = array();
	foreach ($sql_res as $val)
	{

		$main_array[$val[csf('source')]][$val[csf('party')]][$val[csf('job_no_mst')]][$val[csf('determination_id')]][$val[csf('prod_id')]]['uom'] = $val[csf('cons_uom')];
		$main_array[$val[csf('source')]][$val[csf('party')]][$val[csf('job_no_mst')]][$val[csf('determination_id')]][$val[csf('prod_id')]]['issue_qty'] += $val[csf('cons_quantity')];
		$main_array[$val[csf('source')]][$val[csf('party')]][$val[csf('job_no_mst')]][$val[csf('determination_id')]][$val[csf('prod_id')]]['rtn_qty'] += $val[csf('return_qnty')];
		$main_array[$val[csf('source')]][$val[csf('party')]][$val[csf('job_no_mst')]][$val[csf('determination_id')]][$val[csf('prod_id')]]['fabric_desc'] = $val[csf('fabric_desc')];
		$main_array[$val[csf('source')]][$val[csf('party')]][$val[csf('job_no_mst')]][$val[csf('determination_id')]][$val[csf('prod_id')]]['lot'] = $val[csf('lot')];

		if($prodIdChk[$val[csf('prod_id')]] == "")
		{
			$prodIdChk[$val[csf('prod_id')]] = $val[csf('prod_id')];
			$all_prodId_arr[$val[csf('prod_id')]] = $val[csf('prod_id')];
		}
		if($jobIdChk[$val[csf('job_id')]] == "")
		{
			$jobIdChk[$val[csf('job_id')]] = $val[csf('job_id')];
			$all_jobId_arr[$val[csf('job_id')]] = $val[csf('job_id')];
		}
		if($orderIdChk[$val[csf('order_id')]] == "")
		{
			$orderIdChk[$val[csf('order_id')]] = $val[csf('order_id')];
			$all_orderId_arr[$val[csf('order_id')]] = $val[csf('order_id')];
		}

	}
    unset($sql_res);
	// echo "<pre>";
	//echo "<pre>";print_r($main_array);//die;



	$all_prodId_arr = array_filter($all_prodId_arr);
	if(!empty($all_prodId_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 181, 1,$all_prodId_arr, $empty_arr); //recv id
		//die;

		if($search_type==1)
		{
			// ========================================== OPENING BALANCE (receive) ==========================================
			/* $sql_opn_bal_rcv = "SELECT a.knitting_source as source, a.knitting_company as party, SUM(b.cons_quantity) as receive,d.job_no_mst,b.prod_id from inv_receive_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d,gbl_temp_engine x where a.item_category in(1) and a.entry_form in(9) and a.id=b.mst_id and b.id=c.trans_id and c.po_breakdown_id=d.id and b.transaction_date < '$from_date' and a.status_active=1 and b.transaction_type in (1,4,5) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.knitting_source in(1,3) and b.item_category=1 and b.prod_id=x.ref_val and x.user_id=$user_id and x.entry_form=181 and x.ref_from=1 $company_cond $location_cond $source_cond_rcv $party_cond_rcv $year_cond group by a.knitting_source, a.knitting_company,d.job_no_mst,b.prod_id"; */
			$sql_opn_bal_rcv = "SELECT a.recv_number, b.cons_quantity as receive,b.prod_id from inv_receive_master a, inv_transaction b, gbl_temp_engine x where a.item_category in(1) and a.entry_form in(1,9,10,11) and a.id=b.mst_id and b.transaction_date < '$from_date' and a.status_active=1 and b.transaction_type in (1,4,5) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=1 and b.prod_id=x.ref_val and x.user_id=$user_id and x.entry_form=181 and x.ref_from=1 $company_cond $location_cond $source_cond_rcv $party_cond_rcv $year_cond";
			//echo $sql_opn_bal_rcv;die();
			$sql_opn_bal_rcv_res = sql_select($sql_opn_bal_rcv);
			$opn_receive_array = array();
			foreach ($sql_opn_bal_rcv_res as $val)
			{
				//$opn_receive_array[$val[csf('source')]][$val[csf('party')]][$val[csf('job_no_mst')]][$val[csf('prod_id')]] = $val[csf('receive')];
				$opn_receive_array[$val[csf('prod_id')]] += $val[csf('receive')];
			}
			unset($sql_opn_bal_rcv_res);
			//echo "<pre>";print_r($opn_receive_array);

			// ========================================== OPENING BALANCE (issue) ==========================================
			/* $sql_opn_bal_issue = "SELECT a.knit_dye_source as source, a.knit_dye_company as party, SUM(b.cons_quantity) as issue,d.job_no_mst,b.prod_id from inv_issue_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, gbl_temp_engine x where a.item_category=1 and a.entry_form=3 and a.id=b.mst_id and b.id=c.trans_id and c.po_breakdown_id=d.id and b.item_category=1 and b.transaction_type in (2,3) and b.transaction_date < '$from_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.issue_purpose=8 and b.prod_id=x.ref_val and x.user_id=$user_id and x.entry_form=181 and x.ref_from=1 $company_cond $location_cond $source_cond $party_cond $year_cond group by a.knit_dye_source, a.knit_dye_company,d.job_no_mst,b.prod_id"; */

			$sql_opn_bal_issue = "SELECT a.issue_number, b.cons_quantity as issue, b.prod_id from inv_issue_master a, inv_transaction b, gbl_temp_engine x where a.item_category=1 and a.entry_form in(3,8,10,11) and a.id=b.mst_id and b.item_category=1 and b.transaction_type in (2,3,6) and b.transaction_date < '$from_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id=x.ref_val and x.user_id=$user_id and x.entry_form=181 and x.ref_from=1 $company_cond $location_cond $source_cond $party_cond $year_cond";

			//echo $sql_opn_bal_issue;die();
			$sql_opn_bal_issue_res = sql_select($sql_opn_bal_issue);
			$opn_issue_array = array();
			foreach($sql_opn_bal_issue_res as $val)
			{
				//$opn_issue_array[$val[csf('source')]][$val[csf('party')]][$val[csf('job_no_mst')]][$val[csf('prod_id')]] = $val[csf('issue')];
				$opn_issue_array[$val[csf('prod_id')]] = $val[csf('issue')];
			}
			unset($sql_opn_bal_issue_res);
			//echo "<pre>";print_r($opn_issue_array);
		}


		// =========================================== YARN RETURN ===================================
		$date_cond_rcv 	= str_replace("issue_date", "receive_date", $date_cond);
		$party_cond_rcv = str_replace("knit_dye_company", "knitting_company", $party_cond);
		$source_cond_rcv= str_replace("knit_dye_source", "knitting_source", $source_cond);

		$sql_yarn_rec="SELECT a.knitting_source as source, a.knitting_company as party, sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty, sum(b.cons_reject_qnty) as cons_reject_qnty,a.entry_form,b.transaction_type,d.job_no_mst,b.prod_id
		from inv_receive_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, gbl_temp_engine x
		where a.item_category in(1) and a.entry_form in(9) and a.id=b.mst_id and b.id=c.trans_id  and c.po_breakdown_id=d.id and b.transaction_type=c.trans_type and c.status_active=1 and b.item_category in(1) and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id=x.ref_val and x.user_id=$user_id and x.entry_form=181 and x.ref_from=1 $company_cond $location_cond $source_cond_rcv $party_cond_rcv $date_cond_rcv $year_cond group by a.knitting_source, a.knitting_company,a.entry_form,b.transaction_type,d.job_no_mst,b.prod_id
		order by a.knitting_company";
		//echo $sql_rec;die();
		$sql_yarn_rec_res = sql_select($sql_yarn_rec);
		$yarn_rtn_array = array();
		foreach ($sql_yarn_rec_res as $val)
		{
			$yarn_rtn_array[$val[csf('source')]][$val[csf('party')]][$val[csf('job_no_mst')]][$val[csf('prod_id')]]['rtn_qty'] = $val[csf('cons_quantity')];
			$yarn_rtn_array[$val[csf('source')]][$val[csf('party')]][$val[csf('job_no_mst')]][$val[csf('prod_id')]]['rej_qty'] = $val[csf('cons_reject_qnty')];
		}
		unset($sql_yarn_rec_res);

		$yarn_avg_rate = "SELECT a.prod_id, a.order_rate
		from inv_transaction a, gbl_temp_engine x
		where a.item_category in(1) and a.item_category in(1) and a.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and a.prod_id=x.ref_val and x.user_id=$user_id and x.entry_form=181 and x.ref_from=1";
		//echo $yarn_avg_rate;die;
		$yarn_avg_rate_res = sql_select($yarn_avg_rate);
		$yarn_avg_rate_array = array();
		foreach ($yarn_avg_rate_res as $val)
		{
			$yarn_avg_rate_array[$val[csf('prod_id')]]['order_rate'] = $val[csf('order_rate')];
		}
		unset($yarn_avg_rate_res);

	}

	// =========================================== Fabric Info ===================================
	$all_jobId_arr = array_filter($all_jobId_arr);
	if(!empty($all_jobId_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 181, 2,$all_jobId_arr, $empty_arr); //recv id
		//die;

		$date_cond_rcv 	= str_replace("issue_date", "receive_date", $date_cond);
		$party_cond_rcv = str_replace("knit_dye_company", "knitting_company", $party_cond);
		$source_cond_rcv= str_replace("knit_dye_source", "knitting_source", $source_cond);

		$sql_fab_rec="SELECT a.knitting_source as source, a.knitting_company as party, sum(b.cons_quantity) as cons_quantity, sum(b.return_qnty) as return_qnty, sum(b.cons_reject_qnty) as cons_reject_qnty,a.entry_form,b.transaction_type,d.job_no_mst,b.prod_id,e.item_description,e.detarmination_id
		from inv_receive_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d,product_details_master e, gbl_temp_engine x
		where a.item_category in(13) and a.entry_form in(58) and a.id=b.mst_id and b.id=c.trans_id  and c.po_breakdown_id=d.id and b.prod_id=e.id and b.transaction_type=c.trans_type and c.status_active=1 and b.item_category in(13) and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.job_id=x.ref_val and x.user_id=$user_id and x.entry_form=181 and x.ref_from=2 $company_cond $location_cond $source_cond_rcv $party_cond_rcv $date_cond_rcv $year_cond group by a.knitting_source, a.knitting_company,a.entry_form,b.transaction_type,d.job_no_mst,b.prod_id,e.item_description,e.detarmination_id
		order by a.knitting_company";
		//echo $sql_fab_rec;die();
		$sql_fab_rec_res = sql_select($sql_fab_rec);
		$fab_rcv_array = array();
		foreach ($sql_fab_rec_res as $val)
		{
			$fab_rcv_array[$val[csf('source')]][$val[csf('party')]][$val[csf('job_no_mst')]][$val[csf('detarmination_id')]]['rcv_qty'] = $val[csf('cons_quantity')];
			$fab_rcv_array[$val[csf('source')]][$val[csf('party')]][$val[csf('job_no_mst')]][$val[csf('detarmination_id')]]['rej_qty'] = $val[csf('cons_reject_qnty')];

		}
		unset($sql_fab_rec_res);
		//echo "<pre>";print_r($fab_rcv_array);
	}

	// =========================================== Grey Used Info ===================================
	$all_orderId_arr = array_filter($all_orderId_arr);
	if(!empty($all_orderId_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 181, 3,$all_orderId_arr, $empty_arr); //recv id
		//die;
		$sql_material_used = "SELECT a.recv_number,b.febric_description_id,a.knitting_source,c.id,c.prod_id,c.used_qty,c.rate,c.amount,c.yarn_percentage,c.porcess_loss
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_material_used_dtls c, gbl_temp_engine x
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.order_id=cast(x.ref_val as varchar2(4000))  and x.user_id=$user_id and x.entry_form=181 and x.ref_from=3 ";
		//echo $sql_material_used;die;

		$sql_material_used_res = sql_select($sql_material_used);
		$used_yarn_qnty_arr = array();
		foreach ($sql_material_used_res as $val)
		{
			$used_yarn_qnty_arr[$val[csf('knitting_source')]][$val[csf('febric_description_id')]][$val[csf('prod_id')]]['used_qty'] += $val[csf('used_qty')];

		}
	}


	$inhouse_party_count = array();
	$inhouse_job_count = array();
	$inhouse_feb_des_count = array();
	$outbound_party_count = array();
	$outbound_job_count = array();
	$outbound_feb_des_count = array();
	foreach ($main_array as $source_key => $source_val)
	{
		foreach ($source_val as $party_key => $party_val)
		{
			foreach ($party_val as $job_key => $job_val)
			{
				foreach ($job_val as $feb_des_key => $feb_des_val)
				{
					foreach ($feb_des_val as $prod_id_key => $row)
					{
						if($source_key==1)
						{
							$inhouse_party_count[$source_key][$party_key]++;
							$inhouse_job_count[$source_key][$party_key][$job_key]++;
							$inhouse_feb_des_count[$source_key][$party_key][$job_key][$feb_des_key]++;
						}
						else
						{
							$outbound_party_count[$source_key][$party_key]++;
							$outbound_job_count[$source_key][$party_key][$job_key]++;
							$outbound_feb_des_count[$source_key][$party_key][$job_key][$feb_des_key]++;
						}
					}

				}
			}
		}
	}
	//echo "<pre>";print_r($party_count);

	$smn_inhouse_party_count = array();
	$smn_inhouse_booking_count = array();
	$smn_inhouse_prod_id_count = array();
	$smn_outbound_party_count = array();
	$smn_outbound_booking_count = array();
	$smn_outbound_prod_id_count = array();
	foreach ($smn_main_array as $source_key => $source_val)
	{
		foreach ($source_val as $party_key => $party_val)
		{
			foreach ($party_val as $booking_key => $booking_val)
			{
				foreach ($booking_val as $feb_des_key => $feb_des_val)
				{
					foreach ($feb_des_val as $prod_id_key => $row)
					{
						if($source_key==1)
						{
							$smn_inhouse_party_count[$source_key][$party_key]++;
							$smn_inhouse_booking_count[$source_key][$party_key][$booking_key]++;
							$smn_inhouse_feb_des_count[$source_key][$party_key][$booking_key][$feb_des_key]++;
						}
						else
						{
							$smn_outbound_party_count[$source_key][$party_key]++;
							$smn_outbound_booking_count[$source_key][$party_key][$booking_key]++;
							$smn_outbound_feb_des_count[$source_key][$party_key][$booking_key][$feb_des_key]++;
						}

					}
				}
			}
		}
	}
	//echo "<pre>";print_r($smn_inhouse_booking_count);

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3,4,5,6) and ENTRY_FORM=181");
	oci_commit($con);
	disconnect($con);
	ob_start();

	if($type==1)
	{
		?>
		<style type="text/css">
			table tr td, table th{word-wrap: break-word;word-break: break-all;}
		</style>
        <fieldset style="width:1820px;" >
            <table width="1800" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="12" style="font-size:24px !important;"><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="12" style="font-size:16px;font-weight: bold;"><? echo $report_title; ?> (Party Wise)</td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="12" style="font-size:16px;font-weight: bold;">
				   <?
					if($search_type==1)
					{
						echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to));
					}
					else
					{
						echo change_date_format(str_replace("'","",$txt_date_from));
					}

					?>
				</td>
                </tr>
            </table>
			<?

			$bulk_smn_gr_opn_bal 	     = 0;
			$bulk_smn_gr_yarn_issue      = 0;
			$bulk_smn_gr_yarn_rtn 	     = 0;
			$bulk_smn_gr_rej_yarn_rtn    = 0;
			$bulk_smn_gr_net_yarn_issue  = 0;
			$bulk_smn_gr_fab_rcv 	     = 0;
			$bulk_smn_gr_rej_fab_rcv     = 0;
			$bulk_smn_gr_used_yarn_qnty  = 0;
			$bulk_smn_gr_closing_balance = 0;
			$bulk_smn_gr_total_value     = 0;

			if(!empty($main_array))
			{
				?>
				<table width="1800" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
					<thead>
						<th width="40">SL</th>
						<th width="120">Party Name</th>
						<th width="100">Job No.</th>
						<th width="180">Item Description</th>
						<th width="100">Lot No</th>
						<th width="60">UOM</th>
						<th width="100">Opening Bal</th>
						<th width="100">Yarn Issue</th>
						<th width="100">Yarn Return</th>
						<th width="100">Rej Yarn Rtn</th>
						<th width="100">Net Yarn Issue</th>
						<th width="100">Fab Rcv</th>
						<th width="100">Rej Fab Rcv</th>
						<th width="100">Used Yarn Qty</th>
						<th width="100">Closing Balance</th>
						<th width="100">Avg Rate</th>
						<th width="100">Total Value</th>
						<th width="">Status</th>
					</thead>
				</table>
				<div style="width:1820px; overflow-y: auto; max-height:450px;" id="scroll_body">
					<table width="1800" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" align="left">
						<tr bgcolor="#82a2ba">
							<td colspan="18" style="font-size: 20px; font-weight:bold; padding:0px">Bulk</td>
						</tr>
						<?

						$in_out_gr_opn_bal=$in_out_gr_yarn_issue=$in_out_gr_yarn_rtn=$in_out_gr_rej_yarn_rtn=$in_out_gr_net_yarn_issue =$in_out_gr_fab_rcv=$in_out_gr_rej_fab_rcv=$in_out_gr_used_yarn_qnty=$in_out_gr_closing_balance=$in_out_gr_total_value=0;

						foreach ($main_array as $source_key => $source_val)
						{
							if($source_key==1)
							{
								$gr_opn_bal=$gr_yarn_issue=$gr_yarn_rtn=$gr_rej_yarn_rtn=$gr_net_yarn_issue=$gr_fab_rcv=$gr_rej_fab_rcv=$gr_used_yarn_qnty=$gr_closing_balance=$gr_total_value=$opn_receive_qty=$opn_issue_qty=$used_yarn_qnty=$closing_balance=$avg_rate= 0;

								$i=1;
								foreach ($source_val as $party_key => $party_val)
								{

									foreach ($party_val as $job_key => $job_val)
									{
										foreach ($job_val as $feb_des_key => $feb_des_val)
										{
											foreach ($feb_des_val as $prod_id_key => $row)
											{
												$inhouse_party_span = $inhouse_party_count[$source_key][$party_key];
												$inhouse_job_span = $inhouse_job_count[$source_key][$party_key][$job_key];
												$inhouse_feb_des_span = $inhouse_feb_des_count[$source_key][$party_key][$job_key][$feb_des_key];

												$yarn_rtn_qty 	= $yarn_rtn_array[$source_key][$party_key][$job_key][$prod_id_key]['rtn_qty'];
												$yarn_rej_qty 	= $yarn_rtn_array[$source_key][$party_key][$job_key][$prod_id_key]['rej_qty'];


												$fab_rcv_qty 	= $fab_rcv_array[$source_key][$party_key][$job_key][$feb_des_key]['rcv_qty'];
												$fab_rej_qty 	= $fab_rcv_array[$source_key][$party_key][$job_key][$feb_des_key]['rej_qty'];


												// $opn_receive_qty = $opn_receive_array[$source_key][$party_key][$job_key][$prod_id_key];
												// $opn_issue_qty = $opn_issue_array[$source_key][$party_key][$job_key][$prod_id_key];
												$opn_receive_qty = $opn_receive_array[$prod_id_key];
												$opn_issue_qty = $opn_issue_array[$prod_id_key];


												$opening_balance = $opn_receive_qty - $opn_issue_qty;

												$net_yarn_issue = ($row['issue_qty']-$yarn_rtn_qty-$yarn_rej_qty);

												$used_yarn_qnty = $used_yarn_qnty_arr[$source_key][$feb_des_key][$prod_id_key]['used_qty'];
												$closing_balance =$net_yarn_issue-$used_yarn_qnty;

												$avg_rate = $yarn_avg_rate_array[$prod_id_key]['order_rate'];
												$total_value 	= $closing_balance*$avg_rate;



												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

												if(!in_array($source_key,$sourceKeyArr))
												{

													?>
													<tr bgcolor="#CCCCCC">

														<td colspan="18" style="font-size: 17px; font-weight:bold; padding:0px"><? echo $knitting_source[$source_key];?></td>
													</tr>

													<?
													$sourceKeyArr[$i]=$source_key;
												}


												if(!in_array($source_key."**".$party_key."**".$job_key,$job_chk111))
												{

													if($i!=1)
													{

														?>
														<tr bgcolor="#CCCCCC">
															<td colspan="6" align="right" ><b>Sub Total : </b></td>
															<td  align="right"><b><? echo number_format($sub_opn_bal,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_yarn_issue,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_yarn_rtn,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_rej_yarn_rtn,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_net_yarn_issue,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_fab_rcv,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_rej_fab_rcv,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_used_yarn_qnty,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_closing_balance,2,'.','');?></b></td>
															<td  align="right">&nbsp;</td>
															<td  align="right"><b><? echo number_format($sub_total_value,2,'.','');?></b></td>
															<td  align="right">&nbsp;</td>
														</tr>
														<?
														$sub_opn_bal 	     =0;
														$sub_yarn_issue 	 =0;
														$sub_yarn_rtn 	     =0;
														$sub_rej_yarn_rtn    =0;
														$sub_net_yarn_issue  =0;
														$sub_fab_rcv 	     =0;
														$sub_rej_fab_rcv     =0;
														$sub_used_yarn_qnty  =0;
														$sub_closing_balance =0;
														$sub_total_value     =0;
													}
													$job_chk111[]=$source_key."**".$party_key."**".$job_key;

												}

												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">

													<?

													if(!in_array($source_key."**".$party_key."**".$job_key,$job_chk))
													{
														$job_chk[]=$source_key."**".$party_key."**".$job_key;
														?>
														<td width="40" align="center" rowspan="<? echo $inhouse_job_span; ?>" valign="middle"><? echo $i;?></td>

														<td width="120" align="center" rowspan="<? echo $inhouse_job_span; ?>" title="<? echo $party_key;?>" valign="middle">
															<?
																if($source_key==1)
																{
																	echo $company_arr[$party_key];
																}
																else
																{
																	echo $party_library[$party_key];
																}

															?>
														</td>
														<td width="100" valign="middle" align="center" rowspan="<? echo $inhouse_job_span; ?>" ><p><? echo $job_key;?></p></td>
														<?
													}

													if(!in_array($source_key."**".$party_key."**".$job_key."**".$feb_des_key,$feb_des_chk))
													{
														$feb_des_chk[]=$source_key."**".$party_key."**".$job_key."**".$feb_des_key;
														?>

														<td width="180" align="center" valign="middle" rowspan="<? echo $inhouse_feb_des_span; ?>"><p><? echo $row['fabric_desc'];?></p></td>
													<? }?>

													<td width="100" align="center" title="<? echo $prod_id_key;?>"><? echo $row['lot'];?></td>
													<td width="60" align="center"><? echo $unit_of_measurement[$row['uom']];?></td>
													<td width="100" align="right" title="<? echo $source_key.'='.$party_key.'='.$job_key.'='.$prod_id_key;?>"><? echo number_format($opening_balance,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($row['issue_qty'],2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($yarn_rtn_qty,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($yarn_rej_qty,2,'.','');?></td>
													<td width="100" align="right" title="(Yarn Issue-Yarn Return-Rej Yarn Rtn)"><? echo number_format($net_yarn_issue,2,'.','');?></td>
													<?

													if(!in_array($source_key."**".$party_key."**".$job_key."**".$feb_des_key,$feb_des_chk2))
													{
														$feb_des_chk2[]=$source_key."**".$party_key."**".$job_key."**".$feb_des_key;
														?>
														<td width="100" valign="middle" align="right" rowspan="<? echo $inhouse_feb_des_span; ?>"><? echo number_format($fab_rcv_qty,2,'.','');?></td>
														<td width="100" valign="middle" align="right" rowspan="<? echo $inhouse_feb_des_span; ?>"><? echo number_format($fab_rej_qty,2,'.','');?></td>
														<?
														$sub_fab_rcv 	     += $fab_rcv_qty;
														$sub_rej_fab_rcv     += $fab_rej_qty;
														$gr_fab_rcv 	    += $fab_rcv_qty;
														$gr_rej_fab_rcv     += $fab_rej_qty;
														$in_out_gr_fab_rcv 	   += $fab_rcv_qty;
														$in_out_gr_rej_fab_rcv += $fab_rej_qty;
														$bulk_smn_gr_fab_rcv   += $fab_rcv_qty;
														$bulk_smn_gr_rej_fab_rcv += $fab_rej_qty;
													}

													?>

													<td width="100" align="right"><? echo number_format($used_yarn_qnty,2,'.','');?></td>
													<td width="100" align="right" title="<? echo "(Net Yarn Issue-Used Yarn Qty)";?>"><? echo number_format($closing_balance,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($avg_rate,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($total_value,2,'.','');?></td>
													<?
													if(!in_array($source_key."**".$party_key."**".$job_key,$job_chk33))
													{
														$job_chk33[]=$source_key."**".$party_key."**".$job_key;
														?>
														<td width="" align="center" rowspan="<? echo $inhouse_job_span; ?>" valign="middle">
														<?
															$ord_status_sql="SELECT a.id, a.shiping_status from wo_po_break_down a where a.status_active=1 and a.is_deleted=0 and a.shiping_status<>3 and a.job_no_mst='$job_key'";
															//echo $ord_status_sql;
															$ord_status_sql_res = sql_select($ord_status_sql);
															if(count($ord_status_sql_res)>0)
															{
																echo "Partial";
															}
															else
															{
																echo "Closed";
															}

														?>
														</td>
													<? } ?>
												</tr>
												<?

												$sub_opn_bal 	     += $opening_balance;
												$sub_yarn_issue 	 += $row['issue_qty'];
												$sub_yarn_rtn 	     += $yarn_rtn_qty;
												$sub_rej_yarn_rtn    += $yarn_rej_qty;
												$sub_net_yarn_issue  += $net_yarn_issue;
												$sub_used_yarn_qnty  += $used_yarn_qnty;
												$sub_closing_balance += $closing_balance;
												$sub_total_value     += $total_value;

												$gr_opn_bal 	    += $opening_balance;
												$gr_yarn_issue 	    += $row['issue_qty'];
												$gr_yarn_rtn 	    += $yarn_rtn_qty;
												$gr_rej_yarn_rtn    += $yarn_rej_qty;
												$gr_net_yarn_issue  += $net_yarn_issue;

												$gr_used_yarn_qnty  += $used_yarn_qnty;
												$gr_closing_balance += $closing_balance;
												$gr_total_value     += $total_value;

												$in_out_gr_opn_bal 	       += $opening_balance;
												$in_out_gr_yarn_issue 	   += $row['issue_qty'];
												$in_out_gr_yarn_rtn 	   += $yarn_rtn_qty;
												$in_out_gr_rej_yarn_rtn    += $yarn_rej_qty;
												$in_out_gr_net_yarn_issue  += $net_yarn_issue;

												$in_out_gr_used_yarn_qnty  += $used_yarn_qnty;
												$in_out_gr_closing_balance += $closing_balance;
												$in_out_gr_total_value     += $total_value;

												$bulk_smn_gr_opn_bal 	     += $opening_balance;
												$bulk_smn_gr_yarn_issue      += $row['issue_qty'];
												$bulk_smn_gr_yarn_rtn 	     += $yarn_rtn_qty;
												$bulk_smn_gr_rej_yarn_rtn    += $yarn_rej_qty;
												$bulk_smn_gr_net_yarn_issue  += $net_yarn_issue;
												$bulk_smn_gr_used_yarn_qnty  += $used_yarn_qnty;
												$bulk_smn_gr_closing_balance += $closing_balance;
												$bulk_smn_gr_total_value     += $total_value;
											}
											$i++;

										}
									}

								}
								?>
									<tr bgcolor="#CCCCCC">
										<td colspan="6" align="right"><b>Sub Total : </b></td>
										<td  align="right"><b><? echo number_format($sub_opn_bal,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_yarn_issue,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_yarn_rtn,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_rej_yarn_rtn,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_net_yarn_issue,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_fab_rcv,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_rej_fab_rcv,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_used_yarn_qnty,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_closing_balance,2,'.','');?></b></td>
										<td  align="right">&nbsp;</td>
										<td  align="right"><b><? echo number_format($sub_total_value,2,'.','');?></b></td>
										<td  align="right">&nbsp;</td>
									</tr>
									<tr bgcolor="#CCCCCC">
										<td colspan="6" align="right" style="font-size: 17px; font-weight:bold; padding:0px">Inhouse Total : </td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_opn_bal,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_yarn_issue,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_yarn_rtn,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_rej_yarn_rtn,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_net_yarn_issue,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_fab_rcv,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_rej_fab_rcv,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_used_yarn_qnty,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_closing_balance,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px">&nbsp;</td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_total_value,2,'.','');?></td>
										<td  align="right" >&nbsp;</td>
									</tr>
								<?
							}
							else
							{

								unset($i,$gr_opn_bal,$gr_yarn_issue,$gr_yarn_rtn,$gr_rej_yarn_rtn,$gr_net_yarn_issue,$gr_rej_fab_rcv,$gr_used_yarn_qnty,$gr_closing_balance,$gr_total_value,$opn_receive_qty,$opn_issue_qty,$used_yarn_qnty,$closing_balance,$avg_rate,$sub_opn_bal,$sub_yarn_issue,$sub_yarn_rtn,$sub_rej_yarn_rtn,$sub_net_yarn_issue,$sub_rej_fab_rcv,$sub_used_yarn_qnty,$sub_closing_balance,$sub_total_value,$sub_fab_rcv,$sub_rej_fab_rcv);



								$gr_opn_bal=$gr_yarn_issue=$gr_yarn_rtn=$gr_rej_yarn_rtn=$gr_net_yarn_issue=$gr_fab_rcv=$gr_rej_fab_rcv=$gr_used_yarn_qnty=$gr_closing_balance=$gr_total_value=$opn_receive_qty=$opn_issue_qty=$used_yarn_qnty=$closing_balance=$avg_rate= 0;
								$i=1;
								foreach ($source_val as $party_key => $party_val)
								{
									foreach ($party_val as $job_key => $job_val)
									{
										foreach ($job_val as $feb_des_key => $feb_des_val)
										{
											foreach ($feb_des_val as $prod_id_key => $row)
											{
												$outbound_party_span = $outbound_party_count[$source_key][$party_key];
												$outbound_job_span= $outbound_job_count[$source_key][$party_key][$job_key];
												$outbound_feb_des_span = $outbound_feb_des_count[$source_key][$party_key][$job_key][$feb_des_key];

												$yarn_rtn_qty 	= $yarn_rtn_array[$source_key][$party_key][$job_key][$prod_id_key]['rtn_qty'];
												$yarn_rej_qty 	= $yarn_rtn_array[$source_key][$party_key][$job_key][$prod_id_key]['rej_qty'];

												$fab_rcv_qty 	= $fab_rcv_array[$source_key][$party_key][$job_key][$feb_des_key]['rcv_qty'];
												$fab_rej_qty 	= $fab_rcv_array[$source_key][$party_key][$job_key][$feb_des_key]['rej_qty'];


												// $opn_receive_qty = $opn_receive_array[$source_key][$party_key][$job_key][$prod_id_key];
												// $opn_issue_qty = $opn_issue_array[$source_key][$party_key][$job_key][$prod_id_key];
												$opn_receive_qty = $opn_receive_array[$prod_id_key];
												$opn_issue_qty = $opn_issue_array[$prod_id_key];

												$opening_balance = $opn_receive_qty - $opn_issue_qty;

												$net_yarn_issue = ($row['issue_qty']-$yarn_rtn_qty-$yarn_rej_qty);

												$used_yarn_qnty = $used_yarn_qnty_arr[$source_key][$feb_des_key][$prod_id_key]['used_qty'];
												$closing_balance =$net_yarn_issue-$used_yarn_qnty;

												$avg_rate = $yarn_avg_rate_array[$prod_id_key]['order_rate'];
												$total_value 	= $closing_balance*$avg_rate;


												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

												if(!in_array($source_key,$sourceKeyArr))
												{

													?>
													<tr bgcolor="#CCCCCC">

														<td colspan="18" style="font-size: 20px; font-weight:bold; padding:0px"><? echo $knitting_source[$source_key];?></td>
													</tr>

													<?
													$sourceKeyArr[$i]=$source_key;
												}


												if(!in_array($source_key."**".$party_key."**".$job_key,$job_chk222))
												{
													if($i!=1)
													{

														?>
														<tr bgcolor="#CCCCCC">
															<td colspan="6" align="right" ><b>Sub Total : </b></td>
															<td  align="right"><b><? echo number_format($sub_opn_bal,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_yarn_issue,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_yarn_rtn,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_rej_yarn_rtn,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_net_yarn_issue,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_fab_rcv,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_rej_fab_rcv,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_used_yarn_qnty,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_closing_balance,2,'.','');?></b></td>
															<td  align="right">&nbsp;</td>
															<td  align="right"><b><? echo number_format($sub_total_value,2,'.','');?></b></td>
															<td  align="right">&nbsp;</td>
														</tr>
														<?
														$sub_opn_bal 	     =0;
														$sub_yarn_issue 	 =0;
														$sub_yarn_rtn 	     =0;
														$sub_rej_yarn_rtn    =0;
														$sub_net_yarn_issue  =0;
														$sub_fab_rcv 	     =0;
														$sub_rej_fab_rcv     =0;
														$sub_used_yarn_qnty  =0;
														$sub_closing_balance =0;
														$sub_total_value     =0;
													}
													$job_chk222[]=$source_key."**".$party_key."**".$job_key;

												}
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr1_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr1_<? echo $i; ?>">

													<?


													if(!in_array($source_key."**".$party_key."**".$job_key,$job_chk1))
													{
														$job_chk1[]=$source_key."**".$party_key."**".$job_key;
														?>
														<td width="40" align="center" rowspan="<? echo $outbound_job_span; ?>" valign="middle"><? echo $i;?></td>

														<td width="120" valign="middle" align="center" rowspan="<? echo $outbound_job_span; ?>" title="<? echo $party_key;?>">

															<?
																if($source_key==1)
																{
																	echo $company_arr[$party_key];
																}
																else
																{
																	echo $party_library[$party_key];
																}

															?>
														</td>
														<td width="100" align="center" valign="middle" rowspan="<? echo $outbound_job_span; ?>" ><? echo $job_key;?></td>
														<?
													}



													if(!in_array($source_key."**".$party_key."**".$job_key."**".$feb_des_key,$feb_des_chk1))
													{
														$feb_des_chk1[]=$source_key."**".$party_key."**".$job_key."**".$feb_des_key;
														?>

														<td width="180" align="center" valign="middle" rowspan="<? echo $outbound_feb_des_span; ?>"><p><? echo $row['fabric_desc'];?></p></td>
													<? }?>

													<td width="100" align="center"><? echo $row['lot'];?></td>
													<td width="60" align="center"><? echo $unit_of_measurement[$row['uom']];?></td>
													<td width="100" align="right" title="<? echo $source_key.'='.$party_key.'='.$job_key.'='.$prod_id_key;?>"><? echo number_format($opening_balance,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($row['issue_qty'],2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($yarn_rtn_qty,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($yarn_rej_qty,2,'.','');?></td>
													<td width="100" align="right" title="(Yarn Issue-Yarn Return-Rej Yarn Rtn)"><? echo number_format($net_yarn_issue,2,'.','');?></td>
													<?
													if(!in_array($source_key."**".$party_key."**".$job_key."**".$feb_des_key,$feb_des_chk3))
													{
														$feb_des_chk3[]=$source_key."**".$party_key."**".$job_key."**".$feb_des_key;

														?>
														<td width="100" valign="middle" align="right" rowspan="<? echo $outbound_job_span; ?>"><? echo number_format($fab_rcv_qty,2,'.','');?></td>
														<td width="100" valign="middle" align="right" rowspan="<? echo $outbound_job_span; ?>"><? echo number_format($fab_rej_qty,2,'.','');?></td>
														<?
														$sub_fab_rcv 	     += $fab_rcv_qty;
														$sub_rej_fab_rcv     += $fab_rej_qty;
														$gr_fab_rcv 	    += $fab_rcv_qty;
														$gr_rej_fab_rcv     += $fab_rej_qty;
														$in_out_gr_fab_rcv 	   += $fab_rcv_qty;
														$in_out_gr_rej_fab_rcv += $fab_rej_qty;
														$bulk_smn_gr_fab_rcv   += $fab_rcv_qty;
														$bulk_smn_gr_rej_fab_rcv += $fab_rej_qty;
													}

													?>

													<td width="100" align="right"><? echo number_format($used_yarn_qnty,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($closing_balance,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($avg_rate,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($total_value,2,'.','');?></td>
													<?
													if(!in_array($source_key."**".$party_key."**".$job_key,$job_chk333))
													{
														$job_chk333[]=$source_key."**".$party_key."**".$job_key;
														?>
														<td width="" align="center" rowspan="<? echo $inhouse_job_span; ?>" valign="middle">
														<?
															$ord_status_sql="SELECT a.id, a.shiping_status from wo_po_break_down a where a.status_active=1 and a.is_deleted=0 and a.shiping_status<>3 and a.job_no_mst='$job_key'";

															$ord_status_sql_res = sql_select($ord_status_sql);
															if(count($ord_status_sql_res)>0)
															{
																echo "Partial";
															}
															else
															{
																echo "Closed";
															}

														?>
														</td>
													<? } ?>
												</tr>
												<?

												$sub_opn_bal 	     += $opening_balance;
												$sub_yarn_issue 	 += $row['issue_qty'];
												$sub_yarn_rtn 	     += $yarn_rtn_qty;
												$sub_rej_yarn_rtn    += $yarn_rej_qty;
												$sub_net_yarn_issue  += $net_yarn_issue;
												$sub_used_yarn_qnty  += $used_yarn_qnty;
												$sub_closing_balance += $closing_balance;
												$sub_total_value     += $total_value;

												$gr_opn_bal 	    += $opening_balance;
												$gr_yarn_issue 	    += $row['issue_qty'];
												$gr_yarn_rtn 	    += $yarn_rtn_qty;
												$gr_rej_yarn_rtn    += $yarn_rej_qty;
												$gr_net_yarn_issue  += $net_yarn_issue;
												$gr_used_yarn_qnty  += $used_yarn_qnty;
												$gr_closing_balance += $closing_balance;
												$gr_total_value     += $total_value;

												$in_out_gr_opn_bal 	       += $opening_balance;
												$in_out_gr_yarn_issue 	   += $row['issue_qty'];
												$in_out_gr_yarn_rtn 	   += $yarn_rtn_qty;
												$in_out_gr_rej_yarn_rtn    += $yarn_rej_qty;
												$in_out_gr_net_yarn_issue  += $net_yarn_issue;
												$in_out_gr_used_yarn_qnty  += $used_yarn_qnty;
												$in_out_gr_closing_balance += $closing_balance;
												$in_out_gr_total_value     += $total_value;

												$bulk_smn_gr_opn_bal 	     += $opening_balance;
												$bulk_smn_gr_yarn_issue      += $row['issue_qty'];
												$bulk_smn_gr_yarn_rtn 	     += $yarn_rtn_qty;
												$bulk_smn_gr_rej_yarn_rtn    += $yarn_rej_qty;
												$bulk_smn_gr_net_yarn_issue  += $net_yarn_issue;
												$bulk_smn_gr_used_yarn_qnty  += $used_yarn_qnty;
												$bulk_smn_gr_closing_balance += $closing_balance;
												$bulk_smn_gr_total_value     += $total_value;
											}
											$i++;
										}
									}

								}
								?>
									<tr bgcolor="#CCCCCC">
										<td colspan="6" align="right"><b>Sub Total : </b></td>
										<td  align="right"><b><? echo number_format($sub_opn_bal,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_yarn_issue,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_yarn_rtn,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_rej_yarn_rtn,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_net_yarn_issue,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_fab_rcv,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_rej_fab_rcv,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_used_yarn_qnty,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_closing_balance,2,'.','');?></b></td>
										<td  align="right">&nbsp;</td>
										<td  align="right"><b><? echo number_format($sub_total_value,2,'.','');?></b></td>
										<td  align="right">&nbsp;</td>
									</tr>
									<tr bgcolor="#CCCCCC">
										<td colspan="6" align="right" style="font-size: 17px; font-weight:bold; padding:0px">Out-bound Subcontract Total : </td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_opn_bal,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_yarn_issue,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_yarn_rtn,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_rej_yarn_rtn,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_net_yarn_issue,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_fab_rcv,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_rej_fab_rcv,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_used_yarn_qnty,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_closing_balance,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px">&nbsp;</td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_total_value,2,'.','');?></td>
										<td  align="right" >&nbsp;</td>
									</tr>
								<?
							}

						}
						?>
						<tr>
							<td colspan="6" align="right" style="font-size: 18px; font-weight:bold; padding:0px">
								In House + Outbound Subcontract Grand Total :
							</td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_opn_bal,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_yarn_issue,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_yarn_rtn,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_rej_yarn_rtn,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_net_yarn_issue,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_fab_rcv,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_rej_fab_rcv,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_used_yarn_qnty,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_closing_balance,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px">&nbsp;</td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_total_value,2,'.','');?></td>
							<td  align="right" >&nbsp;</td>
						</tr>
					</table>

				</div>

				<br><br>
			<? } ?>


			<?
			if(!empty($smn_main_array))
			{
				unset($i,$gr_opn_bal,$gr_yarn_issue,$gr_yarn_rtn,$gr_rej_yarn_rtn,$gr_net_yarn_issue,$gr_rej_fab_rcv,$gr_used_yarn_qnty,$gr_closing_balance,$gr_total_value,$opn_receive_qty,$opn_issue_qty,$used_yarn_qnty,$closing_balance,$avg_rate,$sub_opn_bal,$sub_yarn_issue,$sub_yarn_rtn,$sub_rej_yarn_rtn,$sub_net_yarn_issue,$sub_rej_fab_rcv,$sub_used_yarn_qnty,$sub_closing_balance,$sub_total_value);

				?>
				<table width="1800" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
					<thead>
						<th width="40">SL</th>
						<th width="120">Party Name</th>
						<th width="100">Booking No.</th>
						<th width="180">Item Description</th>
						<th width="100">Lot No</th>
						<th width="60">UOM</th>
						<th width="100">Opening Bal</th>
						<th width="100">Yarn Issue</th>
						<th width="100">Yarn Return</th>
						<th width="100">Rej Yarn Rtn</th>
						<th width="100">Net Yarn Issue</th>
						<th width="100">Fab Rcv</th>
						<th width="100">Rej Fab Rcv</th>
						<th width="100">Used Yarn Qty</th>
						<th width="100">Closing Balance</th>
						<th width="100">Avg Rate</th>
						<th width="100">Total Value</th>
						<th width="">Status</th>
					</thead>
				</table>
				<div style="width:1820px; overflow-y: auto; max-height:450px;" id="scroll_body">
					<table width="1800" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" align="left">
						<tr bgcolor="#82a2ba">
							<td colspan="18" style="font-size: 20px; font-weight:bold; padding:0px">Sample</td>
						</tr>
						<?

						$in_out_gr_opn_bal=$in_out_gr_yarn_issue=$in_out_gr_yarn_rtn=$in_out_gr_rej_yarn_rtn=$in_out_gr_net_yarn_issue =$in_out_gr_fab_rcv=$in_out_gr_rej_fab_rcv=$in_out_gr_used_yarn_qnty=$in_out_gr_closing_balance=$in_out_gr_total_value=0;

						foreach ($smn_main_array as $source_key => $source_val)
						{
							if($source_key==1)
							{
								$gr_opn_bal=$gr_yarn_issue=$gr_yarn_rtn=$gr_rej_yarn_rtn=$gr_net_yarn_issue=$gr_fab_rcv=$gr_rej_fab_rcv=$gr_used_yarn_qnty=$gr_closing_balance=$gr_total_value=$opn_receive_qty=$opn_issue_qty=$used_yarn_qnty=$closing_balance=$avg_rate= 0;
								$i=1;

								foreach ($source_val as $party_key => $party_val)
								{
									foreach ($party_val as $booking_key => $booking_val)
									{
										foreach ($booking_val as $feb_des_key => $feb_des_val)
										{
											foreach ($feb_des_val as $prod_id_key => $row)
											{
												$smn_inhouse_party_span = $smn_inhouse_party_count[$source_key][$party_key];
												$smn_inhouse_booking_span = $smn_inhouse_booking_count[$source_key][$party_key][$booking_key];
												$smn_inhouse_feb_des_span = $smn_inhouse_feb_des_count[$source_key][$party_key][$booking_key][$feb_des_key];


												$yarn_rtn_qty 	= $smn_yarn_rtn_array[$source_key][$party_key][$booking_key][$prod_id_key]['rtn_qty'];
												$yarn_rej_qty 	= $smn_yarn_rtn_array[$source_key][$party_key][$booking_key][$prod_id_key][9]['rej_qty'];


												$program_no_arr = array_unique(explode(",",chop($smn_program_info_arr[$booking_key][$feb_des_key]['program_no'] ,",")));

												$fab_rcv_qty = 0;
												$fab_rej_qty = 0;
												foreach ($program_no_arr as $p_rows)
												{
													$fab_rcv_qty += $smn_knitting_info_array[$source_key][$p_rows]['qc_pass_qnty'];
													$fab_rej_qty += $smn_knitting_info_array[$source_key][$p_rows]['reject_qnty'];

												}

												$opn_receive_qty = $smn_opn_receive_array[$prod_id_key];
												$opn_issue_qty = $smn_opn_issue_array[$prod_id_key];

												$opening_balance = $opn_receive_qty - $opn_issue_qty;

												$net_yarn_issue = ($row['issue_qty']-$yarn_rtn_qty-$yarn_rej_qty);

												$used_yarn_qnty = $smn_used_yarn_qnty_arr[$source_key][$feb_des_key][$prod_id_key]['used_qty'];
												$closing_balance =$net_yarn_issue-$used_yarn_qnty;

												$avg_rate = $smn_yarn_avg_rate_array[$prod_id_key]['order_rate'];
												$total_value 	= $closing_balance*$avg_rate;

												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

												if(!in_array($source_key,$sourceKeyArr))
												{

													?>
													<tr bgcolor="#CCCCCC">

														<td colspan="18" style="font-size: 17px; font-weight:bold; padding:0px"><? echo $knitting_source[$source_key];?></td>
													</tr>

													<?
													$sourceKeyArr[$i]=$source_key;
												}


												if(!in_array($source_key."**".$party_key."**".$booking_key,$smn_booking_chk))
												{

													if($i!=1)
													{

														?>
														<tr bgcolor="#CCCCCC">
															<td colspan="6" align="right" ><b>Sub Total : </b></td>
															<td  align="right"><b><? echo number_format($sub_opn_bal,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_yarn_issue,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_yarn_rtn,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_rej_yarn_rtn,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_net_yarn_issue,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_fab_rcv,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_rej_fab_rcv,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_used_yarn_qnty,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_closing_balance,2,'.','');?></b></td>
															<td  align="right">&nbsp;</td>
															<td  align="right"><b><? echo number_format($sub_total_value,2,'.','');?></b></td>
															<td  align="right">&nbsp;</td>
														</tr>
														<?
														$sub_opn_bal 	     =0;
														$sub_yarn_issue 	 =0;
														$sub_yarn_rtn 	     =0;
														$sub_rej_yarn_rtn    =0;
														$sub_net_yarn_issue  =0;
														$sub_fab_rcv 	     =0;
														$sub_rej_fab_rcv     =0;
														$sub_used_yarn_qnty  =0;
														$sub_closing_balance =0;
														$sub_total_value     =0;
													}
													$smn_booking_chk[]=$source_key."**".$party_key."**".$booking_key;

												}

												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">

													<?

													if(!in_array($source_key."**".$party_key."**".$booking_key,$smn_booking_chk1))
													{
														$smn_booking_chk1[]=$source_key."**".$party_key."**".$booking_key;
														?>
														<td width="40" align="center" rowspan="<? echo $smn_inhouse_booking_span; ?>" valign="middle"><? echo $i;?></td>

														<td width="120" align="center" rowspan="<? echo $smn_inhouse_booking_span; ?>" title="<? echo $party_key;?>" valign="middle">
															<?
																if($source_key==1)
																{
																	echo $company_arr[$party_key];
																}
																else
																{
																	echo $party_library[$party_key];
																}

															?>
														</td>
														<td width="100" valign="middle" align="center" rowspan="<? echo $smn_inhouse_booking_span; ?>" ><p><? echo $booking_key;?></p></td>
														<?
													}

													if(!in_array($source_key."**".$party_key."**".$booking_key."**".$feb_des_key,$smn_feb_des_chk1))
													{
														$smn_feb_des_chk1[]=$source_key."**".$party_key."**".$booking_key."**".$feb_des_key;
														?>

														<td width="180" align="center" valign="middle" rowspan="<? echo $smn_inhouse_feb_des_span; ?>"><p><? echo $row['fabric_desc'];?></p></td>
													<? } ?>
													<td width="100" align="center"><? echo $row['lot'];?></td>
													<td width="60" align="center"><? echo $unit_of_measurement[$row['uom']];?></td>
													<td width="100" align="right" title="<? echo $source_key.'='.$party_key.'='.$booking_key.'='.$prod_id_key;?>"><? echo number_format($opening_balance,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($row['issue_qty'],2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($yarn_rtn_qty,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($yarn_rej_qty,2,'.','');?></td>
													<td width="100" align="right" title="(Yarn Issue-Yarn Return-Rej Yarn Rtn)"><? echo number_format($net_yarn_issue,2,'.','');?></td>
													<?

													if(!in_array($source_key."**".$party_key."**".$booking_key."**".$feb_des_key,$smn_feb_des_chk2))
													{
														$smn_feb_des_chk2[]=$source_key."**".$party_key."**".$booking_key."**".$feb_des_key;
														?>
														<td width="100" valign="middle" align="right" rowspan="<? echo $smn_inhouse_feb_des_span; ?>"><? echo number_format($fab_rcv_qty,2,'.','');?></td>
														<td width="100" valign="middle" align="right" rowspan="<? echo $smn_inhouse_feb_des_span; ?>"><? echo number_format($fab_rej_qty,2,'.','');?></td>
														<?

														$sub_fab_rcv 	 		 += $fab_rcv_qty;
														$sub_rej_fab_rcv 		 += $fab_rej_qty;
														$gr_fab_rcv 	         += $fab_rcv_qty;
														$gr_rej_fab_rcv          += $fab_rej_qty;
														$in_out_gr_fab_rcv 	     += $fab_rcv_qty;
														$in_out_gr_rej_fab_rcv   += $fab_rej_qty;
														$bulk_smn_gr_fab_rcv     += $fab_rcv_qty;
														$bulk_smn_gr_rej_fab_rcv += $fab_rej_qty;
													}

													?>

													<td width="100" align="right"><? echo number_format($used_yarn_qnty,2,'.','');?></td>
													<td width="100" align="right" title="Net Yarn Issue-Used Yarn Qty"><? echo number_format($closing_balance,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($avg_rate,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($total_value,2,'.','');?></td>
													<td width="" align="right">&nbsp;</td>
												</tr>
												<?

												$sub_opn_bal 	     += $opening_balance;
												$sub_yarn_issue 	 += $row['issue_qty'];
												$sub_yarn_rtn 	     += $yarn_rtn_qty;
												$sub_rej_yarn_rtn    += $yarn_rej_qty;
												$sub_net_yarn_issue  += $net_yarn_issue;
												$sub_used_yarn_qnty  += $used_yarn_qnty;
												$sub_closing_balance += $closing_balance;
												$sub_total_value     += $total_value;

												$gr_opn_bal 	    += $opening_balance;
												$gr_yarn_issue 	    += $row['issue_qty'];
												$gr_yarn_rtn 	    += $yarn_rtn_qty;
												$gr_rej_yarn_rtn    += $yarn_rej_qty;
												$gr_net_yarn_issue  += $net_yarn_issue;
												$gr_used_yarn_qnty  += $used_yarn_qnty;
												$gr_closing_balance += $closing_balance;
												$gr_total_value     += $total_value;

												$in_out_gr_opn_bal 	       += $opening_balance;
												$in_out_gr_yarn_issue 	   += $row['issue_qty'];
												$in_out_gr_yarn_rtn 	   += $yarn_rtn_qty;
												$in_out_gr_rej_yarn_rtn    += $yarn_rej_qty;
												$in_out_gr_net_yarn_issue  += $net_yarn_issue;
												$in_out_gr_used_yarn_qnty  += $used_yarn_qnty;
												$in_out_gr_closing_balance += $closing_balance;
												$in_out_gr_total_value     += $total_value;

												$bulk_smn_gr_opn_bal 	     += $opening_balance;
												$bulk_smn_gr_yarn_issue      += $row['issue_qty'];
												$bulk_smn_gr_yarn_rtn 	     += $yarn_rtn_qty;
												$bulk_smn_gr_rej_yarn_rtn    += $yarn_rej_qty;
												$bulk_smn_gr_net_yarn_issue  += $net_yarn_issue;
												$bulk_smn_gr_used_yarn_qnty  += $used_yarn_qnty;
												$bulk_smn_gr_closing_balance += $closing_balance;
												$bulk_smn_gr_total_value     += $total_value;
											}
										}
											$i++;

									}

								}
								?>
									<tr bgcolor="#CCCCCC">
										<td colspan="6" align="right"><b>Sub Total : </b></td>
										<td  align="right"><b><? echo number_format($sub_opn_bal,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_yarn_issue,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_yarn_rtn,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_rej_yarn_rtn,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_net_yarn_issue,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_fab_rcv,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_rej_fab_rcv,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_used_yarn_qnty,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_closing_balance,2,'.','');?></b></td>
										<td  align="right">&nbsp;</td>
										<td  align="right"><b><? echo number_format($sub_total_value,2,'.','');?></b></td>
										<td  align="right">&nbsp;</td>
									</tr>
									<tr bgcolor="#CCCCCC">
										<td colspan="6" align="right" style="font-size: 17px; font-weight:bold; padding:0px">Inhouse Total : </td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_opn_bal,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_yarn_issue,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_yarn_rtn,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_rej_yarn_rtn,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_net_yarn_issue,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_fab_rcv,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_rej_fab_rcv,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_used_yarn_qnty,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_closing_balance,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px">&nbsp;</td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_total_value,2,'.','');?></td>
										<td  align="right" >&nbsp;</td>
									</tr>
								<?
							}
							else
							{

								unset($i,$gr_opn_bal,$gr_yarn_issue,$gr_yarn_rtn,$gr_rej_yarn_rtn,$gr_net_yarn_issue,$gr_rej_fab_rcv,$gr_used_yarn_qnty,$gr_closing_balance,$gr_total_value,$opn_receive_qty,$opn_issue_qty,$used_yarn_qnty,$closing_balance,$avg_rate,$sub_opn_bal,$sub_yarn_issue,$sub_yarn_rtn,$sub_rej_yarn_rtn,$sub_net_yarn_issue,$sub_rej_fab_rcv,$sub_used_yarn_qnty,$sub_closing_balance,$sub_total_value,$sub_fab_rcv,$sub_rej_fab_rcv);


								$gr_opn_bal=$gr_yarn_issue=$gr_yarn_rtn=$gr_rej_yarn_rtn=$gr_net_yarn_issue=$gr_fab_rcv=$gr_rej_fab_rcv=$gr_used_yarn_qnty=$gr_closing_balance=$gr_total_value=$opn_receive_qty=$opn_issue_qty=$used_yarn_qnty=$closing_balance=$avg_rate= 0;
								$i=1;
								foreach($source_val as $party_key => $party_val)
								{
									foreach($party_val as $booking_key => $booking_val)
									{
										foreach ($booking_val as $feb_des_key => $feb_des_val)
										{
											foreach($feb_des_val as $prod_id_key => $row)
											{
												$smn_outbound_party_span = $smn_outbound_party_count[$source_key][$party_key];
												$smn_outbound_booking_span= $smn_outbound_booking_count[$source_key][$party_key][$booking_key];
												$smn_outbound_feb_des_span = $smn_outbound_feb_des_count[$source_key][$party_key][$booking_key][$feb_des_key];

												$yarn_rtn_qty 	= $smn_yarn_rtn_array[$source_key][$party_key][$booking_key][$prod_id_key]['rtn_qty'];
												$yarn_rej_qty 	= $smn_yarn_rtn_array[$source_key][$party_key][$booking_key][$prod_id_key]['rej_qty'];

												$program_no_arr = array_unique(explode(",",chop($smn_program_info_arr[$booking_key][$feb_des_key]['program_no'] ,",")));

												$fab_rcv_qty = 0;
												$fab_rej_qty = 0;
												foreach ($program_no_arr as $p_rows)
												{
													$fab_rcv_qty += $smn_knitting_info_array[$source_key][$p_rows]['qc_pass_qnty'];
													$fab_rej_qty += $smn_knitting_info_array[$source_key][$p_rows]['reject_qnty'];
												}


												$opn_receive_qty = $opn_receive_array[$source_key][$party_key][$booking_key][$prod_id_key];
												$opn_issue_qty = $opn_issue_array[$source_key][$party_key][$booking_key][$prod_id_key];

												$opening_balance = $opn_receive_qty - $opn_issue_qty;

												$net_yarn_issue = ($row['issue_qty']-$yarn_rtn_qty-$yarn_rej_qty);

												$used_yarn_qnty = $smn_used_yarn_qnty_arr[$source_key][$feb_des_key][$prod_id_key]['used_qty'];

												$closing_balance =$net_yarn_issue-$used_yarn_qnty;

												$avg_rate = $smn_yarn_avg_rate_array[$prod_id_key]['order_rate'];
												$total_value 	= $closing_balance*$avg_rate;


												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

												if(!in_array($source_key,$sourceKeyArr))
												{

													?>
													<tr bgcolor="#CCCCCC">

														<td colspan="18" style="font-size: 20px; font-weight:bold; padding:0px"><? echo $knitting_source[$source_key];?></td>
													</tr>

													<?
													$sourceKeyArr[$i]=$source_key;
												}


												if(!in_array($source_key."**".$party_key."**".$booking_key,$smn_booking_chk222))
												{
													if($i!=1)
													{

														?>
														<tr bgcolor="#CCCCCC">
															<td colspan="6" align="right" ><b>Sub Total : </b></td>
															<td  align="right"><b><? echo number_format($sub_opn_bal,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_yarn_issue,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_yarn_rtn,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_rej_yarn_rtn,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_net_yarn_issue,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_fab_rcv,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_rej_fab_rcv,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_used_yarn_qnty,2,'.','');?></b></td>
															<td  align="right"><b><? echo number_format($sub_closing_balance,2,'.','');?></b></td>
															<td  align="right">&nbsp;</td>
															<td  align="right"><b><? echo number_format($sub_total_value,2,'.','');?></b></td>
															<td  align="right">&nbsp;</td>
														</tr>
														<?
														$sub_opn_bal 	     =0;
														$sub_yarn_issue 	 =0;
														$sub_yarn_rtn 	     =0;
														$sub_rej_yarn_rtn    =0;
														$sub_net_yarn_issue  =0;
														$sub_fab_rcv 	     =0;
														$sub_rej_fab_rcv     =0;
														$sub_used_yarn_qnty  =0;
														$sub_closing_balance =0;
														$sub_total_value     =0;
													}
													$smn_booking_chk222[]=$source_key."**".$party_key."**".$booking_key;

												}
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr1_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr1_<? echo $i; ?>">

													<?

													if(!in_array($source_key."**".$party_key."**".$booking_key,$smn_booking_chk11))
													{
														$smn_booking_chk11[]=$source_key."**".$party_key."**".$booking_key;
														?>
														<td width="40" align="center" rowspan="<? echo $smn_outbound_booking_span; ?>" valign="middle"><? echo $i;?></td>

														<td width="120" valign="middle" align="center" rowspan="<? echo $smn_outbound_booking_span; ?>" title="<? echo $party_key;?>">

															<?
																if($source_key==1)
																{
																	echo $company_arr[$party_key];
																}
																else
																{
																	echo $party_library[$party_key];
																}

															?>
														</td>
														<td width="100" align="center" valign="middle" rowspan="<? echo $smn_outbound_booking_span; ?>" ><? echo $booking_key;?></td>
														<?
													}

													if(!in_array($source_key."**".$party_key."**".$booking_key."**".$feb_des_key,$smn_feb_deschk11))
													{
														$smn_feb_deschk11[]=$source_key."**".$party_key."**".$booking_key."**".$feb_des_key;

														?>
														<td width="180" align="center" valign="middle" rowspan="<? echo $smn_outbound_feb_des_span; ?>"><p><? echo $row['fabric_desc'];?></p></td>
													<? } ?>
													<td width="100" align="center"><? echo $row['lot'];?></td>
													<td width="60" align="center"><? echo $unit_of_measurement[$row['uom']];?></td>
													<td width="100" align="right" title="<? echo $source_key.'='.$party_key.'='.$job_key.'='.$prod_id_key;?>"><? echo number_format($opening_balance,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($row['issue_qty'],2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($yarn_rtn_qty,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($yarn_rej_qty,2,'.','');?></td>
													<td width="100" align="right" title="(Yarn Issue-Yarn Return-Rej Yarn Rtn)"><? echo number_format($net_yarn_issue,2,'.','');?></td>
													<?

													if(!in_array($source_key."**".$party_key."**".$booking_key."**".$feb_des_key,$smn_feb_des_chk3))
													{
														$smn_feb_des_chk3[]=$source_key."**".$party_key."**".$booking_key."**".$feb_des_key;
														?>
														<td width="100" valign="middle" align="right" rowspan="<? echo $smn_outbound_feb_des_span; ?>"><? echo number_format($fab_rcv_qty,2,'.','');?></td>
														<td width="100" valign="middle" align="right" rowspan="<? echo $smn_outbound_feb_des_span; ?>"><? echo number_format($fab_rej_qty,2,'.','');?></td>
														<?
														$sub_fab_rcv += $fab_rcv_qty;
														$sub_rej_fab_rcv += $fab_rej_qty;
														$gr_fab_rcv += $fab_rcv_qty;
														$gr_rej_fab_rcv += $fab_rej_qty;
														$in_out_gr_fab_rcv += $fab_rcv_qty;
														$in_out_gr_rej_fab_rcv += $fab_rej_qty;
														$bulk_smn_gr_fab_rcv   += $fab_rcv_qty;
														$bulk_smn_gr_rej_fab_rcv += $fab_rej_qty;

													}

													?>

													<td width="100" align="right"><? echo number_format($used_yarn_qnty,2,'.','');?></td>
													<td width="100" align="right" title="Net Yarn Issue-Used Yarn Qty"><? echo number_format($closing_balance,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($avg_rate,2,'.','');?></td>
													<td width="100" align="right"><? echo number_format($total_value,2,'.','');?></td>
													<td width="" align="right">&nbsp;</td>
												</tr>
												<?

												$sub_opn_bal 	     += $opening_balance;
												$sub_yarn_issue 	 += $row['issue_qty'];
												$sub_yarn_rtn 	     += $yarn_rtn_qty;
												$sub_rej_yarn_rtn    += $yarn_rej_qty;
												$sub_net_yarn_issue  += $net_yarn_issue;
												$sub_used_yarn_qnty  += $used_yarn_qnty;
												$sub_closing_balance += $closing_balance;
												$sub_total_value     += $total_value;

												$gr_opn_bal 	    += $opening_balance;
												$gr_yarn_issue 	    += $row['issue_qty'];
												$gr_yarn_rtn 	    += $yarn_rtn_qty;
												$gr_rej_yarn_rtn    += $yarn_rej_qty;
												$gr_net_yarn_issue  += $net_yarn_issue;
												$gr_used_yarn_qnty  += $used_yarn_qnty;
												$gr_closing_balance += $closing_balance;
												$gr_total_value     += $total_value;

												$in_out_gr_opn_bal 	       += $opening_balance;
												$in_out_gr_yarn_issue 	   += $row['issue_qty'];
												$in_out_gr_yarn_rtn 	   += $yarn_rtn_qty;
												$in_out_gr_rej_yarn_rtn    += $yarn_rej_qty;
												$in_out_gr_net_yarn_issue  += $net_yarn_issue;
												$in_out_gr_used_yarn_qnty  += $used_yarn_qnty;
												$in_out_gr_closing_balance += $closing_balance;
												$in_out_gr_total_value     += $total_value;

												$bulk_smn_gr_opn_bal 	     += $opening_balance;
												$bulk_smn_gr_yarn_issue      += $row['issue_qty'];
												$bulk_smn_gr_yarn_rtn 	     += $yarn_rtn_qty;
												$bulk_smn_gr_rej_yarn_rtn    += $yarn_rej_qty;
												$bulk_smn_gr_net_yarn_issue  += $net_yarn_issue;
												$bulk_smn_gr_used_yarn_qnty  += $used_yarn_qnty;
												$bulk_smn_gr_closing_balance += $closing_balance;
												$bulk_smn_gr_total_value     += $total_value;
											}
											$i++;
										}
									}

								}
								?>
									<tr bgcolor="#CCCCCC">
										<td colspan="6" align="right"><b>Sub Total : </b></td>
										<td  align="right"><b><? echo number_format($sub_opn_bal,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_yarn_issue,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_yarn_rtn,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_rej_yarn_rtn,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_net_yarn_issue,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_fab_rcv,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_rej_fab_rcv,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_used_yarn_qnty,2,'.','');?></b></td>
										<td  align="right"><b><? echo number_format($sub_closing_balance,2,'.','');?></b></td>
										<td  align="right">&nbsp;</td>
										<td  align="right"><b><? echo number_format($sub_total_value,2,'.','');?></b></td>
										<td  align="right">&nbsp;</td>
									</tr>
									<tr bgcolor="#CCCCCC">
										<td colspan="6" align="right" style="font-size: 17px; font-weight:bold; padding:0px">Out-bound Subcontract Total : </td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_opn_bal,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_yarn_issue,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_yarn_rtn,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_rej_yarn_rtn,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_net_yarn_issue,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_fab_rcv,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_rej_fab_rcv,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_used_yarn_qnty,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_closing_balance,2,'.','');?></td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px">&nbsp;</td>
										<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($gr_total_value,2,'.','');?></td>
										<td  align="right" >&nbsp;</td>
									</tr>
								<?
							}

						}
						?>
						<tr>
							<td colspan="6" align="right" style="font-size: 18px; font-weight:bold; padding:0px">
								In House + Outbound Subcontract Grand Total :
							</td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_opn_bal,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_yarn_issue,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_yarn_rtn,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_rej_yarn_rtn,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_net_yarn_issue,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_fab_rcv,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_rej_fab_rcv,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_used_yarn_qnty,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_closing_balance,2,'.','');?></td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px">&nbsp;</td>
							<td  align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($in_out_gr_total_value,2,'.','');?></td>
							<td  align="right" >&nbsp;</td>
						</tr>
					</table>

				</div>
			<? }

			?>

			<table width="1800" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
					<td width="40">&nbsp;</td>
					<td width="120">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td align="right" width="340" style="font-size: 20px; font-weight:bold; padding:0px">Bulk + Sample Grand Total :</td>
					<td width="100" align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($bulk_smn_gr_opn_bal,2,'.','');?></td>
					<td width="100" align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($bulk_smn_gr_yarn_issue,2,'.','');?></td>
					<td width="100" align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($bulk_smn_gr_yarn_rtn,2,'.','');?></td>
					<td width="100" align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($bulk_smn_gr_rej_yarn_rtn,2,'.','');?></td>
					<td width="100" align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($bulk_smn_gr_net_yarn_issue,2,'.','');?></td>
					<td width="100" align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($bulk_smn_gr_fab_rcv,2,'.','');?></td>
					<td width="100" align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($bulk_smn_gr_rej_fab_rcv,2,'.','');?></td>
					<td width="100" align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($bulk_smn_gr_used_yarn_qnty,2,'.','');?></td>
					<td width="100" align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($bulk_smn_gr_closing_balance,2,'.','');?></td>
					<td width="100" >&nbsp;</td>
					<td width="100" align="right" style="font-size: 17px; font-weight:bold; padding:0px"><? echo number_format($bulk_smn_gr_total_value,2,'.','');?></td>
					<td width="">&nbsp;</td>
				</thead>
			</table>


        </fieldset>
		<?
	}

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
?>
