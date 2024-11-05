<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con = connect();

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "")
{
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//--------------------------------------------------------------------------------------------

$action = "generate_report";

if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$companyArr[0] = "All Company";
	$supplierArr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$yarnTestQalityCommentsArr = return_library_array("select prod_id, yarn_quality_coments from inv_yarn_test_mst where  status_active=1 and is_deleted=0", 'prod_id', 'yarn_quality_coments');
	$yarnTestArr = return_library_array("select prod_id, lot_number from inv_yarn_test_mst where status_active=1 and is_deleted=0", 'prod_id', 'lot_number');

	if ($db_type == 0)
	{
		$from_date = change_date_format($from_date, 'yyyy-mm-dd');
		$to_date = change_date_format($to_date, 'yyyy-mm-dd');
	}
	else if ($db_type == 2)
	{
		
		$from_date = change_date_format($from_date, '', '', 1);
		$to_date = change_date_format($to_date, '', '', 1);
	}
	else
	{
		$from_date = "";
		$to_date = "";
	}

	$txt_lot_no = trim($txt_lot_no);
	$txt_composition = trim($txt_composition);
	ob_start();
	//===========

	$buy_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

	$search_cond = "";
	if ($cbo_yarn_type == 0)
		$search_cond .= "";
	else
		$search_cond .= " and b.yarn_type in ($cbo_yarn_type)";
	if ($txt_count == "")
		$search_cond .= "";
	else
		$search_cond .= " and b.yarn_count_id in($txt_count)";
	if ($txt_lot_no == "")
		$search_cond .= "";
	else
		$search_cond .= " and trim(b.lot)='" . trim($txt_lot_no) . "'";

	if ($cbo_supplier == 0)
		$search_cond .= "";
	else
		$search_cond .= "  and b.supplier_id in($cbo_supplier)";
	if ($txt_composition == "")
		$search_cond .= "";
	else
		$search_cond .= " and b.yarn_comp_type1st in (" .$txt_composition_id .")";
        
    $cbo_company_name = 17;

	if ($cbo_company_name == 0) {
		$company_cond = "";
	} else {
		$company_cond = " and a.company_id=$cbo_company_name";
	}

	if ($to_date != "")
		$mrr_date_cond = " and b.transaction_date<='$to_date'";

	if ($cbo_company_name == 0) {
		$company_cond_mrr = "";
	} else {
		$company_cond_mrr = " and b.company_id=$cbo_company_name";
	}

   //$prodidCond = " and a.prod_id in (21236,26394,25771,20192)"; 

   $receive_array = array();

   $sql_receive = "Select a.prod_id,a.receive_basis,d.pay_mode,max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,
  	sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as rcv_total_opening,
  	sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as rcv_total_opening_amt,
  	sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as rcv_total_opening_rate,
  	sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as purchase,
  	sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as purchase_amt,
  	sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_loan,
  	sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_loan_amt,
  	sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_inside_return,
  	sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_inside_return_amt,
  	sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_outside_return,
  	sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_outside_return_amt
  	from inv_transaction a left join wo_yarn_dyeing_mst d on a.pi_wo_batch_no=d.id, inv_receive_master c where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $prodidCond group by a.prod_id,a.receive_basis,d.pay_mode";
  	//echo $sql_receive;  die();

  	$result_sql_receive = sql_select($sql_receive);
  	foreach ($result_sql_receive as $row)
  	{
  		$receive_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
  		$receive_array[$row[csf("prod_id")]]['pay_mode'] = $row[csf("pay_mode")];
  		$receive_array[$row[csf("prod_id")]]['receive_basis'] = $row[csf("receive_basis")]; 
  		$receive_array[$row[csf("prod_id")]]['rcv_total_opening'] += $row[csf("rcv_total_opening")];
  		$receive_array[$row[csf("prod_id")]]['rcv_total_opening_amt'] += $row[csf("rcv_total_opening_amt")];
  		$receive_array[$row[csf("prod_id")]]['purchase'] += $row[csf("purchase")];
  		$receive_array[$row[csf("prod_id")]]['purchase_amt'] += $row[csf("purchase_amt")];
  		$receive_array[$row[csf("prod_id")]]['rcv_loan'] += $row[csf("rcv_loan")];
  		$receive_array[$row[csf("prod_id")]]['rcv_loan_amt'] += $row[csf("rcv_loan_amt")];
  		$receive_array[$row[csf("prod_id")]]['rcv_inside_return'] += $row[csf("rcv_inside_return")];
  		$receive_array[$row[csf("prod_id")]]['rcv_inside_return_amt'] += $row[csf("rcv_inside_return_amt")];
  		$receive_array[$row[csf("prod_id")]]['rcv_outside_return'] += $row[csf("rcv_outside_return")];
  		$receive_array[$row[csf("prod_id")]]['rcv_outside_return_amt'] += $row[csf("rcv_outside_return_amt")];
  		$receive_array[$row[csf("prod_id")]]['weight_per_bag'] = $row[csf("weight_per_bag")];
  		$receive_array[$row[csf("prod_id")]]['weight_per_cone'] = $row[csf("weight_per_cone")];
  		$receive_array[$row[csf("prod_id")]]['rcv_total_opening_rate'] = $row[csf("rcv_total_opening_rate")];

  	}
  	unset($result_sql_receive);

  	$issue_array = array();
    $sql_issue = "select a.prod_id,
  	sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as issue_total_opening,
  	sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as issue_total_opening_rate,
  	sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as issue_total_opening_amt,
  	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_inside,
  	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
  	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_outside,
  	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_outside_amt,
  	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_return,
  	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_return_amt,
  	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_loan,
  	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_loan_amt
  	from inv_transaction a, inv_issue_master c
  	where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $prodidCond group by a.prod_id";

  	//echo $sql_issue; die();

  	$result_sql_issue = sql_select($sql_issue);
  	foreach ($result_sql_issue as $row)
  	{
  		$issue_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
  		$issue_array[$row[csf("prod_id")]]['issue_total_opening'] = $row[csf("issue_total_opening")];
  		$issue_array[$row[csf("prod_id")]]['issue_total_opening_amt'] = $row[csf("issue_total_opening_amt")];
  		$issue_array[$row[csf("prod_id")]]['issue_total_opening_rate'] = $row[csf("issue_total_opening_rate")];
  		$issue_array[$row[csf("prod_id")]]['issue_inside'] = $row[csf("issue_inside")];
  		$issue_array[$row[csf("prod_id")]]['issue_inside_amt'] = $row[csf("issue_inside_amt")];
  		$issue_array[$row[csf("prod_id")]]['issue_outside'] = $row[csf("issue_outside")];
  		$issue_array[$row[csf("prod_id")]]['issue_outside_amt'] = $row[csf("issue_outside_amt")];
  		$issue_array[$row[csf("prod_id")]]['rcv_return'] = $row[csf("rcv_return")];
  		$issue_array[$row[csf("prod_id")]]['rcv_return_amt'] = $row[csf("rcv_return_amt")];
  		$issue_array[$row[csf("prod_id")]]['issue_loan'] = $row[csf("issue_loan")];
  		$issue_array[$row[csf("prod_id")]]['issue_loan_amt'] = $row[csf("issue_loan_amt")];
  	}
  	unset($result_sql_issue);

  	$transfer_qty_array = array();
  	$sql_transfer = "select a.prod_id,
  	sum(case when a.transaction_type=6 and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as trans_out_total_opening,
  	sum(case when a.transaction_type=6 and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as trans_out_total_opening_amt,
  	sum(case when a.transaction_type=6 and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as trans_out_total_opening_rate,
  	sum(case when a.transaction_type=5 and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as trans_in_total_opening,
  	sum(case when a.transaction_type=5 and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as trans_in_total_opening_rate,
  	sum(case when a.transaction_type=5 and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as trans_in_total_opening_amt,
  	sum(case when a.transaction_type=6 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as transfer_out_qty,
  	sum(case when a.transaction_type=6 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as transfer_out_amt,
  	sum(case when a.transaction_type=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as transfer_in_qty,
  	sum(case when a.transaction_type=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as transfer_in_amt
  	from inv_transaction a, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1  and c.is_deleted=0 and c.transfer_criteria=1 group by a.prod_id";
    //echo $sql_transfer;
  	$result_sql_transfer = sql_select($sql_transfer);
  	foreach ($result_sql_transfer as $transRow)
  	{
  		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_qty'] = $transRow[csf("transfer_out_qty")];
  		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_amt'] = $transRow[csf("transfer_out_amt")];
  		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_qty'] = $transRow[csf("transfer_in_qty")];
  		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_amt'] = $transRow[csf("transfer_in_amt")];
  		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening'] = $transRow[csf("trans_out_total_opening")];
  		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_amt'] = $transRow[csf("trans_out_total_opening_amt")];
  		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening'] = $transRow[csf("trans_in_total_opening")];
  		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_amt'] = $transRow[csf("trans_in_total_opening_amt")];
  		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_rate'] = $transRow[csf("trans_in_total_opening_rate")];
  		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_rate'] = $transRow[csf("trans_out_total_opening_rate")];
  	}

  	unset($result_sql_transfer);

	$mrr_rate_sql = sql_select("select b.prod_id, min(b.transaction_date) as min_date, max(b.transaction_date), sum(b.cons_quantity) as cons_quantiy, sum(b.cons_amount) as cons_amount from inv_transaction b where b.status_active=1 and b.is_deleted=0 and b.item_category=1 and b.transaction_type in(1,4,5) $company_cond_mrr group by b.prod_id");

	$mrr_rate_arr = array();
	foreach ($mrr_rate_sql as $row) {
		$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
		$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
		$mrr_rate_arr[$row[csf("prod_id")]]['avg_rate'] = $row[csf("cons_amount")] / $row[csf("cons_quantiy")];

	}
	unset($mrr_rate_sql);

	if($db_type == 0) 
	{

		$sql_allocation = "select a.item_id,a.job_no, a.po_break_down_id,group_concat(booking_no) booking_no, sum(qnty) as allocate_qty, sum(a.qnty) as allocate_qty,a.insert_date,a.allocation_date,a.is_sales,b.company_id, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.lot, b.allocated_qnty, b.available_qnty, b.avg_rate_per_unit,b.dyed_type is_dyied_yarn from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.status_active=1 and a.is_deleted=0 and a.qnty>0 and b.status_active=1 and b.is_deleted=0 $search_cond $company_cond_mrr group by b.company_id,a.item_id,b.lot, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.allocated_qnty, b.available_qnty,b.avg_rate_per_unit,b.dyed_type,a.job_no,a.po_break_down_id,a.insert_date,a.allocation_date,a.is_sales";
	}
	else
	{	

		//$prodIds = "21236,26394,25771,20192";
		//$item_id_cond = "and a.item_id in ($prodIds)"; 
		$sql_allocation = "select b.company_id, b.id, a.item_id,a.job_no, a.po_break_down_id,listagg(cast(a.booking_no as varchar2(4000)), ',') within group (order by a.booking_no) as booking_no, sum(a.qnty) as allocate_qty,a.insert_date,a.allocation_date,a.is_sales, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.lot, b.allocated_qnty, b.available_qnty, b.avg_rate_per_unit,b.dyed_type is_dyied_yarn from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.status_active=1 and a.is_deleted=0 and a.qnty>0 and b.status_active=1 and b.is_deleted=0 $search_cond $company_cond_mrr $item_id_cond group by b.company_id,b.id,a.item_id,b.lot, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.allocated_qnty, b.available_qnty,b.avg_rate_per_unit,b.dyed_type,a.job_no,a.po_break_down_id,a.insert_date,a.allocation_date,a.is_sales";
	}

	//echo $sql_allocation; die();

	$result_allocation = sql_select($sql_allocation);

	foreach ($result_allocation as $row) 
	{
		if( ( $row[csf('job_no')]!="" ) && (strlen($row[csf('job_no')])>4) )// unexpected string ommit
		{
			$jobNumbers .= "'".$row[csf('job_no')]."',";
		}			

		if($row[csf("is_sales")]==1)
		{
			$sales_job_arr[$row[csf("job_no")]] = "'".$row[csf("job_no")]."'";
			$is_sales_arr[$row[csf("job_no")]] = $row[csf("is_sales")];
		}

		$is_dyied_yarn=$row[csf("is_dyied_yarn")];
		$booking_arr=explode(",",$row[csf("booking_no")]);
		foreach ($booking_arr as $booking) {
			$booking_allocation_arr[$row[csf("po_break_down_id")]][$row[csf("item_id")]][$booking] += $row[csf("allocate_qty")]*1;
			$bookingNumbersArr[$booking] = $booking;
		}
	} 	

	$all_booking_cond="";  $bookingCond=""; 
	$bookingNumbersArr = array_filter(array_unique($bookingNumbersArr));
	$all_booking_no="'".implode("','",$bookingNumbersArr)."'";

	if($db_type==2 && count($bookingNumbersArr)>999)
	{
	    $all_booking_chunk_arr=array_chunk($bookingNumbersArr,999) ;
	    foreach($all_booking_chunk_arr as $chunk_arr)
	    {
	        $chunk_arr_value="'".implode("','",$chunk_arr)."'";   
	        $bookingCond.=" booking_no in($chunk_arr_value) or ";  
	    }

	    $all_booking_cond.=" and (".chop($bookingCond,'or ').")";
	}
	else
	{
	    $all_booking_cond=" and booking_no in($all_booking_no)";    
	}

	if($all_booking_cond!="")
	{
		$bookingSql = "select booking_no,booking_date from wo_booking_mst where status_active=1 and is_deleted=0 $all_booking_cond";
		$bookingResult= sql_select($bookingSql);
		$bookingData = array();
		foreach ($bookingResult as $brow) {
			$bookingData[$brow[csf("booking_no")]]['date']=$brow[csf("booking_date")];
		}
	} 

	$all_JOB_cond=""; $JobCond=""; 
	$all_job_arr = array_unique(explode(",", rtrim($jobNumbers,", ")));
	$all_job_ids = rtrim(implode(",", array_unique(explode(",", $jobNumbers))),", ");

	if($db_type==2 && count($all_job_arr)>999)
	{
	    $all_job_chunk_arr=array_chunk($all_job_arr,999) ;
	    
	    foreach($all_job_chunk_arr as $chunk_arr)
	    {
	    	$chunk_arr_value=implode(",",$chunk_arr);   
	        $JobCond.=" b.job_no in($chunk_arr_value) or ";  
	    }
	    
	    $all_JOB_cond.=" and (".chop($JobCond,'or ').")";
	}
	else
	{
	    $all_JOB_cond=" and b.job_no in($all_job_ids)";    
	}

	$po_number_arr = array();

	if($all_JOB_cond !="")
	{
		$po_sql = sql_select("select a.id,a.job_no_mst,a.shipment_date,a.shiping_status,b.buyer_name, a.file_no,a.grouping,a.po_number from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $all_JOB_cond");

		//echo $po_sql;
		foreach ($po_sql as $row) {
			$po_number_arr[$row[csf("id")]]['po'] = $row[csf("po_number")];
			$po_number_arr[$row[csf("id")]]['file'] = $row[csf("file_no")];
			$po_number_arr[$row[csf("id")]]['ref'] = $row[csf("grouping")];
			$po_number_arr[$row[csf("id")]]['job_no'] = $row[csf("job_no_mst")];
			$po_number_arr[$row[csf("id")]]['shipment_date'] = $row[csf("shipment_date")];
            $po_number_arr[$row[csf("id")]]['shiping_status'] = $row[csf("shiping_status")];
			$po_number_arr[$row[csf("job_no_mst")]]['buyer_name'] = $row[csf("buyer_name")];
		}
	}

	
	if(!empty($sales_job_arr))
	{ 
		$sales_job_cond = " and a.job_no in(".implode(",",$sales_job_arr).")";
	}

	$jobsql = "select a.id, a.job_no,a.buyer_id,a.sales_booking_no, a.style_ref_no,a.within_group from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $sales_job_cond";
	$jobData = sql_select($jobsql);
	$job_no_array = array();
	foreach ($jobData as $row) 
	{
		$sales_order_arr[$row[csf('job_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
		$sales_order_arr[$row[csf('job_no')]]['buyer_id']=$row[csf('buyer_id')];
		$sales_order_arr[$row[csf('job_no')]]['within_group']=$row[csf('within_group')];
	}
	

	$planning_array = array();
	$plan_sql="select a.po_id,a.booking_no,a.dtls_id,b.requisition_no,b.prod_id from ppl_planning_entry_plan_dtls a,ppl_yarn_requisition_entry b where a.dtls_id=b.knit_id and a.status_active=1 and b.status_active=1 group by a.po_id,a.booking_no,a.dtls_id,b.requisition_no,b.prod_id";
	$planData = sql_select($plan_sql);
	foreach ($planData as $row) 
	{
		$planning_array[$row[csf('po_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]][]=$row[csf('booking_no')];
	}

	$issue_array = $job_wose_issue_array = $issue_basis_arr = array();
	//$prod_cond2 = " and c.prod_id in (".$prod_id.")";
	$sql_issue_al = "select a.id,a.issue_basis,a.issue_purpose,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type, sum(c.quantity) as issue_qty,b.job_no from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and (a.issue_purpose in(1,2) or a.issue_purpose in(1,2,15,38) and b.job_no is not null) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by a.id,a.issue_basis,a.issue_purpose,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type,b.job_no";

	$result_issue_al = sql_select($sql_issue_al);

	//print_r($result_issue);

	$issue_array_req=$booking_arr=array();
	foreach ($result_issue_al as $row) 
	{
		$issue_job[$row[csf("id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] = $row[csf("job_no")];
		$issue_basis_arr[$row[csf("id")]]["basis"] = $row[csf("issue_basis")];
		$issue_basis_arr[$row[csf("id")]]["issue_id"] = $row[csf("id")];

		if($row[csf('dyed_type')] == 1)
		{
			$is_sales = $is_sales_arr[$row[csf("job_no")]];

			if($is_sales==1)
			{
				$job_no = $sales_job_arr[$row[csf("job_no")]];
				$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			}else{
				$job_no = $po_number_arr[$row[csf("po_breakdown_id")]]['job_no'];
				$issue_arr[$job_no][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			}
			
			$issue_array[$job_no][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
		}
		else
		{
			$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			if($row[csf("issue_basis")] == 3 || $row[csf("issue_basis")]==1 || $row[csf("issue_basis")] == 8){
				if($row[csf("issue_basis")]==1)
				{
					$booking=0;
					$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
					$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_id"][] = $row[csf("id")];
				}else{
					$booking_arr = $planning_array[$row[csf('po_breakdown_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]];
					$booking_arr = array_unique($booking_arr);
					foreach ($booking_arr as $booking) {
						if($booking != ""){
							$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
							$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_id"][] = $row[csf("id")];
						}
					}
				}

			}else{
				$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
				$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_id"][] = $row[csf("id")];
			}
		}

		$job_wose_issue_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']] += $row[csf("issue_qty")];
	}

	//echo "<pre>";
	//print_r($issue_array_req);
	//echo "</pre>";
	// and b.prod_id=415461 
	$issue_return_array = $issue_return_req_array = $job_wose_issue_return_array = array();
	$sql_return = "Select b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose,d.dyed_type, sum(c.quantity) as issue_return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master d where a.id=b.mst_id and c.trans_type=4 and c.prod_id=d.id and a.entry_form=9 and b.id=c.trans_id and b.item_category=1 and c.issue_purpose in(1,2,7,15,38,46) and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose,d.dyed_type";
	$result_return = sql_select($sql_return);
	foreach ($result_return as $row) 
	{
		$issue_basis = $issue_basis_arr[$row[csf("issue_id")]]["basis"];

		if($row[csf('dyed_type')] == 1)
		{
			$job_wose_issue_return_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']][$row[csf('prod_id')]] += $row[csf("issue_return_qty")];
		}
		else
		{
			if($issue_basis == 3 || $issue_basis == 8)
			{  
				$issue_id = $issue_basis_arr[$row[csf("issue_id")]]["issue_id"];
				$issue_return_po_array[$issue_id][$row[csf('prod_id')]] .= $row[csf('po_breakdown_id')].",";
				$issue_return_req_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$issue_id] = $row[csf("issue_return_qty")];
			}
			else
			{
				$issue_job = $issue_job[$row[csf("issue_id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]];
				if($issue_job!="" && ($row[csf("issue_purpose")]==7 || $row[csf("issue_purpose")]==15 || $row[csf("issue_purpose")]==38 || $row[csf("issue_purpose")]==46))
				{
					$issue_return_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
				}
				else
				{
					$issue_return_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
				}
			}

		}		
		
	}

	/*echo "<pre>";
	print_r($job_wose_issue_return_array);*/
	
	$dyed_yarn_receive_info = sql_select("select a.id,b.job_no,sum(b.cons_quantity) receive_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=1 and b.item_category=1 and b.transaction_type=1 and a.status_active=1 and b.status_active=1 group by a.id,b.job_no");
	foreach ($dyed_yarn_receive_info as $dy_row) 
	{
		$dy_rec_arr[$dy_row[csf("id")]] = $dy_row[csf("job_no")];
	}

	$sql_rcv_rtn = "select c.received_id,a.prod_id,sum(case when a.transaction_type=3 then a.cons_quantity else 0 end) as recieved_return_qty from inv_transaction a, product_details_master b, inv_issue_master c where a.mst_id=c.id and a.prod_id=b.id and b.dyed_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.received_id,a.prod_id";
	$result_sql_rcv_rtn = sql_select($sql_rcv_rtn);

	$rcv_rtn_qty_arr = array();
	foreach ($result_sql_rcv_rtn as $row) 
	{
		$rcv_rtn_qty_arr[$row[csf("prod_id")]][$dy_rec_arr[$row[csf("received_id")]]] = $row[csf("recieved_return_qty")];
	}		

	$i = 1;
	$balance = 0;
	$grand_total_allocate_qty = 0;
	$grand_total_issue_qty = 0;
	$grand_total_issue_rtn_qty = 0;
	$grand_total_balance = 0;
	$prodStock = array();

	if(!empty($result_allocation))
	{
		foreach ($result_allocation as $row) 
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			$prod_id = $row[csf("item_id")];


            $ageOfDays = datediff("d", $row[csf("insert_date")], date("Y-m-d"));

			$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
			if ($row[csf("yarn_comp_type2nd")] != 0)
				$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
			
			$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")] . $prod_id;

			$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
			$transfer_in_amt = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
			$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
			$transfer_out_amt = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];

			$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
			$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];
			$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];
			$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];

			$pay_mode = $receive_array[$row[csf("id")]]['pay_mode'];
			$receive_basis = $receive_array[$row[csf("id")]]['receive_basis'];

			$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
			$openingBalanceAmt = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);

			$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
			$totalRcvAmt = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $transfer_in_amt;
			$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;
			$totalIssueAmt = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] + $transfer_out_amt;

			//echo "test". $openingBalance ."+". $totalRcv ."-". $totalIssue; die();

			$stockInHand = $openingBalance + $totalRcv - $totalIssue;
			$tot_rcv_qnty = $openingBalance + $totalRcv;
			$stockInHandAmt = $openingBalanceAmt + $totalRcvAmt - $totalIssueAmt;
			$tot_rcv_amt = $openingBalanceAmt + $totalRcvAmt;
                    //$avg_rate=$tot_rcv_amt/$tot_rcv_qnty;
			$avg_rate = $mrr_rate_arr[$row[csf("id")]];


			if($row[csf("po_break_down_id")]=="" || $row[csf("booking_no")]=="")
			{
				$issue_basis = array_unique($issue_arr[$row[csf('job_no')]][$prod_id]);
			}else
			{
				$issue_basis = array_unique($issue_arr[$row[csf("po_break_down_id")]][$prod_id]);
			}

			if($row[csf("is_dyied_yarn")] == 1)
			{

				$issue_qty += $issue_array[$row[csf("job_no")]][$prod_id]["issue_qty"];
			}
			else
			{
				$issue_qty=$issue_qty_wo=0;
				foreach ($issue_basis as $basis) {
					if($basis==3 || $basis==1 || $basis==8)
					{
						if($basis==1){
							$booking_row = 0;
							$issue_qty_wo += $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_qty"];
						}else{
							if($row[csf("booking_no")] != ""){
								$booking_nos = explode(",",$row[csf("booking_no")]);
								$issue_qty=0;
								foreach ($booking_nos as $booking_row) {
									$issue_qty += $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_qty"];
								}
							}
						}


					}else{
						$issue_qty += $issue_array[$row[csf('job_no')]][$prod_id]["issue_qty"];
					}
				}
			}

			$within_group=$sales_order_arr[$row[csf('job_no')]]['within_group'];
			$sales_booking_no=$sales_order_arr[$row[csf('job_no')]]['sales_booking_no'];

			if($sales_booking_no!="")
			{ 
				$return_qty = 0;
				if($within_group==1)
				{
					$buyer_id=return_field_value("buyer_id as buyer_id","wo_booking_mst","booking_no ='".$sales_booking_no."' and is_deleted=0 and status_active=1","buyer_id");
					$buyername=$buy_name_arr[$buyer_id];
					$po_number=$po_number_arr[$row[csf("po_break_down_id")]]['po'];
                    $shiping_status = $po_number_arr[$row[csf("po_break_down_id")]]['shiping_status'];
				}
				else
				{
					$buyer_id=$sales_order_arr[$row[csf('job_no')]]['buyer_id'];
					$buyername=$buy_name_arr[$buyer_id];
					$po_number="";
                    $shiping_status = "";
				}
				$shipment_date = "";
				
				// ===== 23/06/2020
				$return_qty=0;
				foreach ($issue_basis as $basis) {
					//echo $basis . "*";
					if($basis==3 || $basis==8){
						//$return_qty=0;
						$booking_nos = explode(",",$row[csf("booking_no")]);
						foreach ($booking_nos as $booking_row) {
							$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_id"]);
							foreach ($issue_ids as $issue_id) {
								$return_qty += $issue_return_req_array[$row[csf('po_break_down_id')]][$prod_id][$issue_id];
							}
						}
					}else{
						$return_qty += $issue_return_array[$row[csf("po_break_down_id")]][$prod_id];
						//echo $return_qty."ooooo";
					}
				}
				// ===== 23/06/2020
			}
			else
			{
				$po_number=$po_number_arr[$row[csf("po_break_down_id")]]['po'];
				$shiping_status = $po_number_arr[$row[csf("po_break_down_id")]]['shiping_status'];
                $shipment_date = $po_number_arr[$row[csf("po_break_down_id")]]['shipment_date'];
                $buyername=$buy_name_arr[$po_number_arr[$row[csf("job_no")]]['buyer_name']];

				if($row[csf("is_dyied_yarn")] == 1)
				{  
					$return_qty = $job_wose_issue_return_array[$row[csf("job_no")]][$prod_id];	
				}
				else
				{
					$return_qty=0;
					foreach ($issue_basis as $basis) 
					{
						//echo $basis . "*";
						if($basis==3 || $basis==8){
							//$return_qty=0;

							$booking_nos = explode(",",$row[csf("booking_no")]);
							foreach ($booking_nos as $booking_row) {
								$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_id"]);
								foreach ($issue_ids as $issue_id) {
									$return_qty += $issue_return_req_array[$row[csf('po_break_down_id')]][$prod_id][$issue_id];
								}
							}
						}else{
							$return_qty += $issue_return_array[$row[csf("po_break_down_id")]][$prod_id];
							//echo $return_qty."ooooo";
						}
					}
				}
			}

			$booking_no = implode(", ",array_unique(explode(",", $row[csf("booking_no")])));

			$rcv_rtn_qty=0;
			$allocate_qty = $row[csf("allocate_qty")];

			if($row[csf("is_dyied_yarn")]==1)
			{
				$rcv_rtn_qty = $rcv_rtn_qty_arr[$row[csf("item_id")]][$row[csf("job_no")]];
				$allocate_qty = ($allocate_qty-$rcv_rtn_qty);
			}
			
			$balance = ($row[csf("allocate_qty")] + number_format($return_qty,2,".","")) - (number_format(($issue_qty+$issue_qty_wo),2,".","") + number_format($rcv_rtn_qty,2,".",""));

			$qumaletive_balance_arr[$row[csf("item_id")]] += $balance;	

			$i++;																	
		}
	}
	
	$update_field="cumulative_balance";
	$upProdID=true;
	foreach($qumaletive_balance_arr as $prod_id=>$qumaletive_balance)
	{
		$upProdID =execute_query("update product_details_master set cumulative_balance=".number_format($qumaletive_balance,2,'.','')." where id=$prod_id"); 

		//echo "update product_details_master set CUMULATIVE_BALANCE='".number_format($qumaletive_balance,2,'.','')."' where id=$prod_id";
		//echo "<br>";
		//if(!$upProdID) { echo "update product_details_master set cumulative_balance=".number_format($qumaletive_balance,2,'.','')." where id=$prod_id";oci_rollback($con); die;}

		$update_id_arr[]=$prod_id;
		$update_data_arr[$prod_id]=explode("*",("'".$qumaletive_balance."'"));

	}

	$upProdID="";
	if(count($update_id_arr)>0)
	{
		$upProdID=bulk_update_sql_statement2("product_details_master","id",$update_field,$update_data_arr,$update_id_arr);
	}

	$rID=execute_query($upProdID);

	if($db_type==2)
	{
		if($rID )
		{
			oci_commit($con); 
			echo "Data Update Successfully. <br>";die;
		}
		else
		{
			oci_rollback($con);
			echo "Data Update Failed";
			die;
		}
	}

	exit();
}

function bulk_update_sql_statement2( $table, $id_column, $update_column, $data_values, $id_count )
{
	$field_array=explode("*",$update_column);
	$id_count_arr=array_chunk($id_count,'999');
	
	//echo "<pre>";print_r($id_count_arr);die;
	
	//$id_count=explode("*",$id_count);
	//$data_values=explode("*",$data_values);
	//print_r($data_values);die;
	
	$sql_up.= "UPDATE $table SET ";
	
	 for ($len=0; $len<count($field_array); $len++)
	 {
		 $sql_up.=" ".$field_array[$len]." = CASE $id_column ";
		 for ($id=0; $id<count($id_count); $id++)
		 {
			 if (trim($data_values[$id_count[$id]][$len])=="") $sql_up.=" when ".$id_count[$id]." then  '".$data_values[$id_count[$id]][$len]."'" ;
			 else $sql_up.=" when ".$id_count[$id]." then  ".$data_values[$id_count[$id]][$len]."" ;
		 }
		 if ($len!=(count($field_array)-1)) $sql_up.=" END, "; else $sql_up.=" END ";
	 }
	 if(count($id_count)>999)
	 {
		$sql_up.=" where";
		$p=1;
		foreach($id_count_arr as $id_arr)
		{
			if($p==1) $sql_up .=" $id_column in(".implode(',',$id_arr).")"; else $sql_up .=" or $id_column in(".implode(',',$id_arr).")";
			$p++;
		}
	 }
	 else
	 {
		$sql_up.=" where $id_column in (".implode(",",$id_count).")";
	 }
	 
	 return $sql_up;     
}

?>
