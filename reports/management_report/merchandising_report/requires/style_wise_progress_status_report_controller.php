<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');

if($action=="load_drop_down_buyer")
{
	$party="1,3,21,90";
	echo create_drop_down( "cbo_buyer_id", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
if($action=='report_generate')
{
	// ===================================
	// platform-v3.5\reports\management_report\merchandising_report\requires\style_closing_status_report_v2_controller.php //Textile part Start
	// ===================================
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$txt_conv_rate=str_replace("'","",$txt_conv_rate);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$cbo_ship_status=str_replace("'","",$cbo_ship_status);
	
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_style=str_replace("'","",$txt_style);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$type=str_replace("'","",$type);


	$ship_date_cond="";
	if($txt_date_from!="" && $txt_date_to!="")
	{
		$ship_date_cond=" and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to' ";
	}

	$job_no_cond="";
	
	if(trim($txt_job_no)!="") $job_no_cond.=" and a.job_no_prefix_num  in($txt_job_no)";
	if(trim($txt_ref_no)!="") $job_no_cond.=" and b.grouping='$txt_ref_no'";	
	if(trim($cbo_buyer_id)>0) $job_no_cond.=" and a.buyer_name='$cbo_buyer_id'";	
	if(trim($cbo_ship_status)>0) $job_no_cond.=" and b.shiping_status='$cbo_ship_status'";	
	if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";
	if(trim($txt_style)!="") $job_no_cond.=" and a.style_ref_no like('%$txt_style%')";

	$con = connect();
	$r_id2=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (45)");
	if($r_id2)
	{
		oci_commit($con);
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	// Order Entry
	$sql_po="SELECT a.buyer_name, a.job_no, a.season_buyer_wise,a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.plan_cut,b.grouping,b.file_no, b.pub_shipment_date, a.avg_unit_price, b.shiping_status, a.gmts_item_id, b.shipment_date as last_ship_date
	from wo_po_details_master a, wo_po_break_down b
	where a.id=b.job_id and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_no_cond $ship_date_cond
	group by a.buyer_name, a.job_no, a.season_buyer_wise,a.job_no_prefix_num, to_char(a.insert_date,'YYYY') , a.style_ref_no, a.total_set_qnty, b.id, b.po_number, b.po_quantity, b.plan_cut,b.grouping,b.file_no, b.pub_shipment_date, a.avg_unit_price, b.shiping_status, a.gmts_item_id, b.shipment_date order by a.job_no, b.shipment_date";
   	// echo $sql_po;
	$sql_po_result=sql_select($sql_po);
	$style_wise_po_count = array();
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $JobArr=array();
	foreach($sql_po_result as $row)
	{
		//if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		$all_po_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
		$result_data_arr[$row[csf("job_no")]]["po_id"].=$row[csf("po_id")].',';
		$result_data_arr[$row[csf("job_no")]]["season"]=$row[csf("season_buyer_wise")];
		$result_data_arr[$row[csf("job_no")]]["file_no"].=$row[csf("file_no")].',';
		$result_data_arr[$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
		$result_data_arr[$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
		$result_data_arr[$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
		$result_data_arr[$row[csf("job_no")]]["job_year"]=$row[csf("job_year")];
		$result_data_arr[$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$result_data_arr[$row[csf("job_no")]]["ratio"]=$row[csf("ratio")];
		$result_data_arr[$row[csf("job_no")]]["ref_no"]=$row[csf("grouping")];
		$result_data_arr[$row[csf("job_no")]]["file_no"]=$row[csf("file_no")];
		$result_data_arr[$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
		$result_data_arr[$row[csf("job_no")]]["po_qnty"]+=$row[csf("po_qnty")]*$row[csf("ratio")];
		$result_data_arr[$row[csf("job_no")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$result_data_arr[$row[csf("job_no")]]["avg_unit_price"]+=$row[csf("avg_unit_price")];
		$result_data_arr[$row[csf("job_no")]]["total_po_count"]++;
		$result_data_arr[$row[csf("job_no")]]["shiping_status"]=$row[csf("shiping_status")];
		$result_data_arr[$row[csf("job_no")]]["gmts_item_id"]=$row[csf("gmts_item_id")];
		$result_data_arr[$row[csf("job_no")]]["last_ship_date"]=$row[csf("last_ship_date")];
		$result_job_wise[$row[csf("job_no")]].=$row[csf("po_id")].",";
		$JobArr[]="'".$row[csf('job_no')]."'";
		$job_no=$row[csf('job_no')];
		$style_wise_po_count[$row[csf('job_no')]]++;
	}
	// echo "<pre>";print_r($result_data_arr);

	if(empty($all_po_id_arr)){echo "<h2 style='color:#FE4B4B;'>Data not found</h2>";exit();}

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 45, 1,$all_po_id_arr, $empty_arr);

	//$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	
	/* 
		$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; $po_cond_for_in4="";
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$po_cond_for_in3=" and (";
			$po_cond_for_in4=" and (";
			
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
				$po_cond_for_in2.=" a.po_breakdown_id in($ids) or"; 
				$po_cond_for_in3.=" b.order_id in($ids) or"; 
				$po_cond_for_in4.=" b.po_id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			$po_cond_for_in2=chop($po_cond_for_in2,'or ');
			$po_cond_for_in2.=")";
			$po_cond_for_in3=chop($po_cond_for_in3,'or ');
			$po_cond_for_in3.=")";
			$po_cond_for_in4=chop($po_cond_for_in4,'or ');
			$po_cond_for_in4.=")";
		}
		else
		{
			$po_cond_for_in=" and b.po_break_down_id in($poIds)";
			$po_cond_for_in2=" and a.po_breakdown_id  in($poIds)";
			$po_cond_for_in3=" and b.order_id in($poIds)";
			$po_cond_for_in4=" and b.po_id in($poIds)";
		} 
	*/

	// Main Fabric Booking V2 and Short Fabric Booking
	$sql_wo=sql_select("SELECT a.booking_no,a.booking_type,b.po_break_down_id,
	(CASE WHEN A.IS_SHORT=2 and a.fabric_source=1 and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS main_grey_req_qnty, 
	(CASE WHEN A.IS_SHORT=2 and a.fabric_source=1 and a.item_category=2 THEN b.fin_fab_qnty ELSE 0 END) AS main_fin_fab_req_qnty,
	(CASE WHEN A.IS_SHORT=1 and a.fabric_source=1 and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS short_grey_req_qnty, 
	(CASE WHEN A.IS_SHORT=1 and a.fabric_source=1 and a.item_category=2 THEN b.fin_fab_qnty ELSE 0 END) AS short_fin_fab_req_qnty
	from wo_booking_mst a, wo_booking_dtls b, GBL_TEMP_ENGINE g  where a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id=g.ref_val and g.entry_form=45 and g.ref_from=1 and g.user_id=$user_id");
	//$po_cond_for_in
	$booking_req_arr=array();
	foreach ($sql_wo as $brow)
	{
		if($brow[csf("main_grey_req_qnty")]>0)
		{
			$booking_req_arr[$brow[csf("po_break_down_id")]]['main_grey']+=$brow[csf("main_grey_req_qnty")];
		}
		if($brow[csf("main_fin_fab_req_qnty")]>0)
		{
			$booking_req_arr[$brow[csf("po_break_down_id")]]['main_fin']+=$brow[csf("main_fin_fab_req_qnty")];
		}
		if($brow[csf("short_grey_req_qnty")]>0)
		{
			$booking_req_arr[$brow[csf("po_break_down_id")]]['short_grey']+=$brow[csf("short_grey_req_qnty")];
		}		
		if($brow[csf("short_fin_fab_req_qnty")]>0)
		{
			$booking_req_arr[$brow[csf("po_break_down_id")]]['short_fin']+=$brow[csf("short_fin_fab_req_qnty")];
		}
		if($brow[csf("booking_type")]==1)
		{
			$booking_req_arr[$brow[csf("po_break_down_id")]]['booking_no'].=$brow[csf("booking_no")].',';
		}
	}

	// Garments Delivery Entry
	$sql_res=sql_select("SELECT b.po_break_down_id as po_id, c.job_no_mst ,
	sum(CASE WHEN b.entry_form=0 THEN b.ex_factory_qnty ELSE 0 END) as exfac_qnty, b.ex_factory_date, d.style_ref_no
	from  pro_ex_factory_mst b, wo_po_break_down c, wo_po_details_master d, GBL_TEMP_ENGINE g  
	where b.po_break_down_id=c.id and c.job_id=d.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and b.po_break_down_id=g.ref_val and g.entry_form=45 and g.ref_from=1 and g.user_id=$user_id group by b.po_break_down_id, c.job_no_mst, b.ex_factory_date, d.style_ref_no order by b.ex_factory_date");
	//$po_cond_for_in
	$ex_factory_qty_arr=array(); $exfact_wise_po_count=array();$ex_factory_max_date_arr=array();
	foreach($sql_res as $row)
	{
		$ex_factory_qty_arr[$row[csf('job_no_mst')]]['exfac_qnty']+=$row[csf('exfac_qnty')];
		$ex_factory_max_date_arr[$row[csf('job_no_mst')]]['ex_factory_date']=$row[csf('ex_factory_date')];
		$exfact_wise_po_count[$row[csf('job_no_mst')]]++;
	}
	/*echo "<pre>";print_r($ex_factory_max_date_arr);;*/


	$all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
	
	// Yarn Issue
	$yarnDataArr=sql_select("SELECT a.po_breakdown_id, 
	sum(case when a.entry_form=3 and c.entry_form=3  then a.quantity else 0 end) as yarn_issue_qnty,
	sum(case when a.entry_form=3 and c.entry_form=3  then a.quantity*b.cons_rate else 0 end) as yarn_issue_value
	from order_wise_pro_details a, inv_transaction b, inv_issue_master c, GBL_TEMP_ENGINE g 
	where a.trans_id=b.id and b.mst_id=c.id and a.trans_type=2 and b.transaction_type=2 and b.item_category=1 and c.issue_purpose in(1,4) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.po_breakdown_id=g.ref_val and g.entry_form=45 and g.ref_from=1 and g.user_id=$user_id group by a.po_breakdown_id");
	//$po_cond_for_in2

	$yarn_issue_arr=array();
	foreach($yarnDataArr as $row)
	{
		$yarn_issue_arr[$row[csf("po_breakdown_id")]]["yarn_issue_qnty"]=$row[csf("yarn_issue_qnty")];
		$yarn_issue_arr[$row[csf("po_breakdown_id")]]["yarn_issue_value"]=$row[csf("yarn_issue_value")];
	}
	// echo "<pre>";print_r($yarn_issue_arr);

	// Yarn Issue Return
	$yarnReturnDataArr=sql_select("SELECT a.po_breakdown_id, 
	sum(CASE WHEN a.entry_form=9 and c.entry_form=9  THEN a.quantity ELSE 0 END) AS yarn_issue_rtn_qnty,
	sum(CASE WHEN a.entry_form=9 and c.entry_form=9  THEN a.quantity*b.cons_rate ELSE 0 END) AS yarn_issue_rtn_value
	from order_wise_pro_details a, inv_transaction b, inv_receive_master c, GBL_TEMP_ENGINE g 
	where a.trans_id=b.id and b.mst_id=c.id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.po_breakdown_id=g.ref_val and g.entry_form=45 and g.ref_from=1 and g.user_id=$user_id
	group by a.po_breakdown_id");
	//$po_cond_for_in2
	$yarn_issue_rtn_arr=array();
	foreach($yarnReturnDataArr as $row)
	{
		$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["yarn_issue_rtn_qnty"]=$row[csf("yarn_issue_rtn_qnty")];
		$yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["yarn_issue_rtn_value"]=$row[csf("yarn_issue_rtn_value")];
	}

	// Knit Grey Fabric Roll Receive
	$grey_roll_recv_data_arr=sql_select("SELECT a.po_breakdown_id,
	sum(CASE WHEN a.entry_form in(58) THEN a.quantity ELSE 0 END) AS knit_grey_roll_recv
	from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c, GBL_TEMP_ENGINE g where a.dtls_id=b.id and b.mst_id=c.id  and c.item_category=13 and a.entry_form in(58) and c.entry_form in(58) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.po_breakdown_id=g.ref_val and g.entry_form=45 and g.ref_from=1 and g.user_id=$user_id group by a.po_breakdown_id");
	// $po_cond_for_in2
	
	$grey_roll_recv_data_arr=sql_select("SELECT a.po_breakdown_id, sum(CASE WHEN a.entry_form in(58) THEN a.quantity ELSE 0 END) AS knit_grey_roll_recv,
	sum(CASE WHEN c.knitting_source in(1) THEN a.quantity ELSE 0 END) AS grey_roll_recv_Inhouse,
	sum(CASE WHEN c.knitting_source in(3) THEN a.quantity ELSE 0 END) AS grey_roll_recv_outbound
	from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c, GBL_TEMP_ENGINE g 
	where a.dtls_id=b.id and b.mst_id=c.id and c.item_category=13 and a.entry_form in(58) and c.entry_form in(58) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.po_breakdown_id=g.ref_val and g.entry_form=45 and g.ref_from=1 and g.user_id=$user_id
	group by a.po_breakdown_id");
	// $po_cond_for_in2
	// and c.receive_basis<>9
	$knit_grey_roll_recv_arr=array();
	foreach($grey_roll_recv_data_arr as $row)
	{
		$knit_grey_roll_recv_arr[$row[csf("po_breakdown_id")]]["knit_grey_roll_recv"]=$row[csf("knit_grey_roll_recv")];
		$knit_grey_roll_recv_arr[$row[csf("po_breakdown_id")]]["grey_roll_recv_Inhouse"]=$row[csf("grey_roll_recv_Inhouse")];
		$knit_grey_roll_recv_arr[$row[csf("po_breakdown_id")]]["grey_roll_recv_outbound"]=$row[csf("grey_roll_recv_outbound")];
	}

	// Finish Fabric Delivery to Store
	$knitFinDataArr=sql_select("SELECT b.order_id, sum(b.current_delivery) as finish_delevery_qnty, sum(b.grey_used_qnty) as finish_delevery_grey_qnty
	from pro_grey_prod_delivery_dtls b, pro_grey_prod_delivery_mst c, GBL_TEMP_ENGINE g 
	where b.mst_id=c.id and c.entry_form in(54) and c.status_active=1 and c.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.order_id=g.ref_val and g.entry_form=45 and g.ref_from=1 and g.user_id=$user_id group by b.order_id ");
	//$po_cond_for_in3
	$finish_delToStore_arr=array();
	foreach($knitFinDataArr as $row)
	{
		$finish_delToStore_arr[$row[csf("order_id")]]["finish_delevery_qnty"]=$row[csf("finish_delevery_qnty")];
		$finish_delToStore_arr[$row[csf("order_id")]]["finish_delevery_grey_qnty"]=$row[csf("finish_delevery_grey_qnty")];
	}

	// Export Invoice Entry
	$export_invoice_data=sql_select("SELECT a.po_breakdown_id, 
	sum(a.current_invoice_qnty) as invoice_qty,
	sum(a.current_invoice_rate) as invoice_rate, count(a.po_breakdown_id) as total_po
	from com_export_invoice_ship_dtls a, GBL_TEMP_ENGINE g 
	where a.status_active=1 and a.is_deleted=0 and a.po_breakdown_id=g.ref_val and g.entry_form=45 and g.ref_from=1 and g.user_id=$user_id  group by a.po_breakdown_id");
	// $po_cond_for_in2
	$export_invoice_arr=array();
	foreach($export_invoice_data as $row)
	{
		$export_invoice_arr[$row[csf("po_breakdown_id")]]["invoice_qty"]=$row[csf("invoice_qty")];
		$export_invoice_arr[$row[csf("po_breakdown_id")]]["invoice_rate"]=$row[csf("invoice_rate")];
		$export_invoice_arr[$row[csf("po_breakdown_id")]]["total_po"]=$row[csf("total_po")];
	}
	// echo "<pre>";print_r($export_invoice_arr);

	// Knitting Bill Issue Inbound
	$knitting_bill_in_data=sql_select("SELECT b.order_id, sum(b.delivery_qty) as knitting_bill_in_qty, sum(b.amount) as knitting_bill_in_value, b.currency_id
	from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, GBL_TEMP_ENGINE g
	where a.id=b.mst_id and b.order_id=g.ref_val and g.entry_form=45 and g.ref_from=1 and g.user_id=$user_id and a.process_id=2 and b.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by b.order_id, b.currency_id");
	// $po_cond_for_in3
	$knitting_bill_in_arr=array();
	foreach($knitting_bill_in_data as $row)
	{
		$currency_id=$row[csf('currency_id')];
		if($currency_id==1) // Tk
		{
			$knitting_bill_in_arr[$row[csf("order_id")]]["knitting_bill_in_value"]+=$row[csf("knitting_bill_in_value")]/$txt_conv_rate;
		}
		else // USD
		{
			$knitting_bill_in_arr[$row[csf("order_id")]]["knitting_bill_in_value"]+=$row[csf("knitting_bill_in_value")];
		}
		$knitting_bill_in_arr[$row[csf("order_id")]]["knitting_bill_in_qty"]+=$row[csf("knitting_bill_in_qty")];
	}
	// echo "<pre>";print_r($knitting_bill_in_arr);

	// Knitting Bill Issue Outbound
	$knitting_bill_out_data=sql_select("SELECT b.order_id, sum(b.receive_qty) as knitting_bill_out_qty, sum(b.amount) as knitting_bill_out_value, b.currency_id
	from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, GBL_TEMP_ENGINE g
	where a.id=b.mst_id and b.order_id=g.ref_val and g.entry_form=45 and g.ref_from=1 and g.user_id=$user_id and a.process_id=2 and b.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by b.order_id, b.currency_id");
	// $po_cond_for_in3
	$knitting_bill_out_arr=array();
	foreach($knitting_bill_out_data as $row)
	{
		$currency_id=$row[csf('currency_id')];
		if($currency_id==1) // Tk
		{
			$knitting_bill_out_arr[$row[csf("order_id")]]["knitting_bill_out_value"]+=$row[csf("knitting_bill_out_value")]/$txt_conv_rate;
		}
		else // USD
		{
			$knitting_bill_out_arr[$row[csf("order_id")]]["knitting_bill_out_value"]+=$row[csf("knitting_bill_out_value")];
		}
		$knitting_bill_out_arr[$row[csf("order_id")]]["knitting_bill_out_qty"]+=$row[csf("knitting_bill_out_qty")];
	}

	// Dyeing And Finishing Bill Issue Inbound
	/*$dyeing_bill_in_data=sql_select("SELECT b.order_id, b.currency_id, 
	sum(CASE WHEN b.add_process in ('31') THEN b.delivery_qty ELSE 0 END) AS dyeing_bill_in_qty, 
	sum(CASE WHEN b.add_process not in('31') THEN b.delivery_qty ELSE 0 END) AS finishing_bill_in_qty, 
	sum(b.amount) as dyeing_bill_in_value
	from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b
	where a.id=b.mst_id $po_cond_for_in3 and a.process_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by b.order_id, b.currency_id");*/
	
	$dyeing_bill_in_data=sql_select("SELECT b.order_id, b.currency_id,
	sum(b.delivery_qty) as dyeing_bill_in_qty, sum(b.amount) as dyeing_bill_in_value 
	from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, pro_grey_prod_delivery_mst c, GBL_TEMP_ENGINE g
	where a.id=b.mst_id  and b.delivery_id=c.id  and b.challan_no=c.sys_number_prefix_num and c.entry_form=54 and b.order_id=g.ref_val and g.entry_form=45 and g.ref_from=1 and g.user_id=$user_id and a.process_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 
	group by b.order_id, b.currency_id");
	// $po_cond_for_in3
	 // and b.add_process=31 is Fabric Dyeing
	$dyeing_bill_in_arr=array();
	foreach($dyeing_bill_in_data as $row)
	{
		$currency_id=$row[csf('currency_id')];
		if($currency_id==1) // Tk
		{
			$dyeing_bill_in_arr[$row[csf("order_id")]]["dyeing_bill_in_value"]=$row[csf("dyeing_bill_in_value")]/$txt_conv_rate;
		}
		else
		{
			$dyeing_bill_in_arr[$row[csf("order_id")]]["dyeing_bill_in_value"]=$row[csf("dyeing_bill_in_value")];
		}
		$dyeing_bill_in_arr[$row[csf("order_id")]]["dyeing_bill_in_qty"]=$row[csf("dyeing_bill_in_qty")];
	}

	// Dyeing And Finishing Bill Issue Outbound
	$dyeing_bill_out_data=sql_select("SELECT b.order_id, 
	sum(case when b.sub_process_id is null or b.sub_process_id=31 then b.receive_qty else 0 end) as dyeing_bill_out_qty,  
	sum(case when  b.sub_process_id !=31 then b.receive_qty else 0 end) as finishing_bill_out_qty, 
	sum(b.amount) as dyeing_bill_out_value, b.currency_id
	from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, GBL_TEMP_ENGINE g
	where a.id=b.mst_id and b.order_id=g.ref_val and g.entry_form=45 and g.ref_from=1 and g.user_id=$user_id and a.process_id=4 and b.process_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by b.order_id, b.currency_id");
	// $po_cond_for_in3
	$dyeing_bill_out_arr=array();
	foreach($dyeing_bill_out_data as $row)
	{
		$currency_id=$row[csf('currency_id')];
		if($currency_id==1) // Tk
		{
			$dyeing_bill_out_arr[$row[csf("order_id")]]["dyeing_bill_out_value"]=$row[csf("dyeing_bill_out_value")]/$txt_conv_rate;
		}
		else
		{
			$dyeing_bill_out_arr[$row[csf("order_id")]]["dyeing_bill_out_value"]=$row[csf("dyeing_bill_out_value")];
		}
		$dyeing_bill_out_arr[$row[csf("order_id")]]["dyeing_bill_out_qty"]=$row[csf("dyeing_bill_out_qty")];
		$dyeing_bill_out_arr[$row[csf("order_id")]]["finishing_bill_out_qty"]=$row[csf("finishing_bill_out_qty")];
	}

	// Batch Creation
	$batch_sql=sql_select("SELECT a.id, a.batch_no, b.po_id, sum(b.batch_qnty) as batch_qty
	from pro_batch_create_mst a, pro_batch_create_dtls b, GBL_TEMP_ENGINE g
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_id=g.ref_val and g.entry_form=45 and g.ref_from=1 and g.user_id=$user_id and a.entry_form=0 group by a.id, a.batch_no, b.po_id");
	// $po_cond_for_in4
	$batch_qty_arr=array();
	foreach($batch_sql as $row)
	{
		$batch_qty_arr[$row[csf("po_id")]]["batch_qty"]+=$row[csf("batch_qty")];
	}
	// echo "<pre>"; print_r($batch_qty_arr);
	
	if(empty($all_po_id_arr))
	{
		echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;
	}
	
	$tbl_width=3070;

	$r_id2=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (45)");
	oci_commit($con);
	disconnect($con);
	
	ob_start();
	?>
    <div style="width:100%">
        <table width="<? echo $tbl_width;?>">
            <tr>
                <td align="center" width="100%" colspan="15" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)].'<br/>';
				if($txt_date_from!="") echo  $txt_date_from.' To '.$txt_date_to;
				 ?></td>
            </tr>
        </table>

        <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
            <thead>
                <tr style="font-size:13px">
                   	<th width="80">Internal Ref</th>
                   	<th width="100">Style No</th>
                   	<th width="100">Garments Item</th>
                   	<th width="100">Buyer Name</th>
                   	<th width="50">Job No</th>
                   	<th width="50">Job Year</th>
                   	<th width="80">Avg Unit Price [Order Entry]</th>
                   	<th width="80">Job Qty.<br/> (Pcs)</th>
                   	<th width="100">Ex-Factory Qty (Pcs)</th>
                   	<th width="80">Avg Unit Price [Export Invoice]</th>
                   	<th width="80">Export Invoice Qty (Pcs)</th>
                   	<th width="90">Fab.Req. Finish (Main Booking)</th>
                   	<th width="80">Fab.Req. Grey (Main Booking)</font></th>
                   	<th width="80">Fab.Req. Finish (Short)</th>
                   	<th width="80">Fab.Req. Grey (Short)</th>
                   	<th width="80">Fab.Req. Finish (Main+Short)</th>
                   	<th width="80">Fab.Req. Grey (Main+Short)</th>
                   	<th width="80">Net Yarn Issued Qty</th>
                   	<th width="80">Yarn Issued Value</th>
                   	<th width="80">Grey Fabric Rcv [Inhouse]</th>
                   	<th width="80">Grey Fabric Rcv [Outbound]</th>
                   	<th width="80">Total Grey Rcv Qty</th>
                   	<th width="80">In-House Knitting Bill Qty</th>
                   	<th width="80">Out-Bound Knitting Bill Qty</th>

                   	<th width="80">Total Knitting Bill Qty</th>
                   	<th width="80">Knitting Bill Value</th>
                   	<th width="80">Finish Fabric Deli. To Store</th>
                   	<th width="80">Finish Fabric Deli. To Store [Grey Qty]</th>
                   	<th width="80">Total Batch Qty</th>

                   	<th width="80">In-House Dyeing Bill Qty</th>
                   	<th width="80">Out-Bound Dyeing Bill Qty</th>
                   	<th width="80">Finishing Bill Qty</th>

                   	<th width="80">Total Dyeing & Finishing Bill Qty</th>
                   	<th width="80">Total Dyeing Bill Value</th>
                   	<th width="80">Last Ship Date</th>
                   	<th width="80">Last Ex-Factory Date</th>
                   	<th>Order Status</th>
                </tr>
            </thead>
       	</table>

        <div style="width:<? echo $tbl_width+20;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
        	<table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">
            	<?
				$i=1;
				$tot_order_qty=$tot_ex_factory_qty=$tot_invoice_qty=$tot_main_finish_fabric_req_qnty=$tot_main_grey_fabric_req_qnty=$tot_short_finish_fabric_req_qnty=$tot_short_grey_fabric_req_qnty=$tot_fin_main_sort=$tot_grey_main_sort=$tot_total_issued=$tot_total_issued_value=$tot_knit_gray_roll_rec_qty=$tot_knitting_bill_qty=$tot_knitting_bill_value=$tot_finish_delevToStore_qnty=$tot_finish_delevToStore_grey_qnty=$tot_batch_qnty=$tot_dyeing_bill_qty=$tot_dyeing_bill_value=$tot_grey_roll_recv_Inhouse=$tot_grey_roll_recv_outbound=$tot_knitting_bill_in_qty=$tot_knitting_bill_out_qty=$tot_dyeing_bill_in_qty=$tot_dyeing_bill_out_qty=$tot_finishing_bill_qty=0;
				
				foreach($result_data_arr as $job_no=>$val)
				{
					$ratio=$val["ratio"];
					$po_id=rtrim($val["po_id"],',');
					$po_ids=array_unique(explode(",",$po_id));
					
					$main_finish_fabric_req_qnty=$main_grey_fabric_req_qnty=$short_finish_fabric_req_qnty=$short_grey_fabric_req_qnty=$grey_roll_recv_Inhouse=$grey_roll_recv_outbound=$knit_gray_roll_rec_qty=$finish_delevToStore_qnty=$finish_delevToStore_grey_qnty=$yarn_issue_qnty=$yarn_issue_rtn_qnty=$yarn_issue_value=$yarn_issue_rtn_value=$invoice_qty=$invoice_rate=$invoice_total_po=$knitting_bill_in_qty=$knitting_bill_in_value=$knitting_bill_out_qty=$knitting_bill_out_value=$dyeing_bill_in_qty=$finishing_bill_out_qty=$dyeing_bill_in_value=$dyeing_bill_out_qty=$dyeing_bill_out_value=$batch_qty=0;

					foreach($po_ids as $pId)
					{
						/*$ex_factory_qty+=$ex_factory_qty_arr[$pId]['exfac_qnty'];
						$ex_factory_max_date=$ex_factory_max_date_arr[$pId]['ex_factory_date'];*/

						$main_finish_fabric_req_qnty+=$booking_req_arr[$pId]['main_fin'];
						$main_grey_fabric_req_qnty+=$booking_req_arr[$pId]['main_grey'];
						$short_finish_fabric_req_qnty+=$booking_req_arr[$pId]['short_fin'];
						$short_grey_fabric_req_qnty+=$booking_req_arr[$pId]['short_grey'];
						$grey_roll_recv_Inhouse+=$knit_grey_roll_recv_arr[$pId]["grey_roll_recv_Inhouse"];
						$grey_roll_recv_outbound+=$knit_grey_roll_recv_arr[$pId]["grey_roll_recv_outbound"];
						$knit_gray_roll_rec_qty+=$knit_grey_roll_recv_arr[$pId]["knit_grey_roll_recv"];
						$finish_delevToStore_qnty+=$finish_delToStore_arr[$pId]["finish_delevery_qnty"];
						$finish_delevToStore_grey_qnty+=$finish_delToStore_arr[$pId]["finish_delevery_grey_qnty"];
						$yarn_issue_qnty+=$yarn_issue_arr[$pId]["yarn_issue_qnty"];
						$yarn_issue_rtn_qnty+=$yarn_issue_rtn_arr[$pId]["yarn_issue_rtn_qnty"];
						$yarn_issue_value+=$yarn_issue_arr[$pId]["yarn_issue_value"];
						$yarn_issue_rtn_value+=$yarn_issue_rtn_arr[$pId]["yarn_issue_rtn_value"];
						$invoice_qty+=$export_invoice_arr[$pId]["invoice_qty"];
						$invoice_rate+=$export_invoice_arr[$pId]["invoice_rate"];
						$invoice_total_po+=$export_invoice_arr[$pId]["total_po"];

						$knitting_bill_in_qty+=$knitting_bill_in_arr[$pId]["knitting_bill_in_qty"];
						$knitting_bill_in_value+=$knitting_bill_in_arr[$pId]["knitting_bill_in_value"];
						$knitting_bill_out_qty+=$knitting_bill_out_arr[$pId]["knitting_bill_out_qty"];
						$knitting_bill_out_value+=$knitting_bill_out_arr[$pId]["knitting_bill_out_value"];

						$dyeing_bill_in_qty+=$dyeing_bill_in_arr[$pId]["dyeing_bill_in_qty"];
						$dyeing_bill_in_value+=$dyeing_bill_in_arr[$pId]["dyeing_bill_in_value"];
						$dyeing_bill_out_qty+=$dyeing_bill_out_arr[$pId]["dyeing_bill_out_qty"];
						$finishing_bill_out_qty+=$dyeing_bill_out_arr[$pId]["finishing_bill_out_qty"];
						$dyeing_bill_out_value+=$dyeing_bill_out_arr[$pId]["dyeing_bill_out_value"];
						
						$batch_qty+=$batch_qty_arr[$pId]["batch_qty"];
					}

					$ex_factory_qty=$ex_factory_qty_arr[$val["job_no"]]['exfac_qnty'];
					$ex_factory_max_date=$ex_factory_max_date_arr[$val["job_no"]]['ex_factory_date'];

					$total_issued=$yarn_issue_qnty-$yarn_issue_rtn_qnty;
					$total_issued_value=($yarn_issue_value-$yarn_issue_rtn_value)/$txt_conv_rate;
					$knitting_bill_qty=$knitting_bill_in_qty+$knitting_bill_out_qty;
					$knitting_bill_value=$knitting_bill_in_value+$knitting_bill_out_value;
					$dyeing_bill_qty=$dyeing_bill_in_qty+$dyeing_bill_out_qty+$finishing_bill_out_qty;
					$finishing_bill_qty=$finishing_bill_out_qty;
					$dyeing_bill_value=$dyeing_bill_in_value+$dyeing_bill_out_value;

					$gmts_item_id_arr=array_unique(explode(",", $val["gmts_item_id"]));
					$gmts_item="";
					foreach ($gmts_item_id_arr as $key => $value) 
					{
						if ($gmts_item=="") 
						{
							$gmts_item.=$garments_item[$value];
						}
						else
						{
							$gmts_item.=','.$garments_item[$value];
						}
					}
					// echo $gmts_item;

					if ($invoice_rate>0 && $invoice_total_po>0) 
					{
						$invoice_avg_rate=$invoice_rate/$invoice_total_po;
					}
					else{
						$invoice_avg_rate=0;
					}

					if ($val["avg_unit_price"]>0 && $val["total_po_count"]>0) 
					{
						$po_avg_rate=$val["avg_unit_price"]/$val["total_po_count"];
					}
					else{
						$po_avg_rate=0;
					}

					$ship_status='';
					// echo $style_wise_po_count[$val["job_no"]] .'=='. $exfact_wise_po_count[$val["job_no"]];
					/*if ( $style_wise_po_count[$val["job_no"]] == $exfact_wise_po_count[$val["job_no"]] ) 
					{ 
						$ship_status='Full Shipment';
					}
					elseif( ($exfact_wise_po_count[$val["job_no"]] > 0) && ( $style_wise_po_count[$val["job_no"]] > $exfact_wise_po_count[$val["job_no"]]) )
					{
						$ship_status='Partial';
					}
					else
					{
						$ship_status='Full Pending';
					}*/
					// $ex_factory_qty=500;
					if($val["po_qnty"]<=$ex_factory_qty)
					{
						$ship_status='Full Shipment';
					}
					elseif( ($val["po_qnty"] > 0) && ($val["po_qnty"] != $ex_factory_qty) && ($ex_factory_qty > 0) )
					{
						$ship_status='Partial';
					}
					else
					{
						$ship_status='Full Pending';
					}

					?>
					<tr bgcolor = "<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
		 				<td width="80" align="center"><p><? echo $val["ref_no"]; ?></p></td>
                        <td width="100" align="center"><p><? echo $val["style_ref_no"]; ?></p></td>
                        <td width="100" align="center"><p><? echo $gmts_item; ?></p></td>
                        <td width="100" align="center"><p><? echo $buyer_arr[$val["buyer_name"]]; ?></p></td>
                        <td width="50" align="center"><p><? echo $val["job_no_prefix_num"]; ?></p></td> 
                        <td width="50" align="center"><p><? echo $val['job_year']; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($po_avg_rate,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($val["po_qnty"],2);?></p></td>
                        <td width="100" align="right"><p><? echo number_format($ex_factory_qty,2); ?></p></td>
                        <td width="80" align="right" title="<? echo $invoice_rate.'/'.$invoice_total_po; ?>"><p><? echo number_format($invoice_avg_rate,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($invoice_qty*$ratio,2); ?></p></td>
                        <td width="90" align="right"><p><? echo number_format($main_finish_fabric_req_qnty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($main_grey_fabric_req_qnty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($short_finish_fabric_req_qnty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($short_grey_fabric_req_qnty,2); ?></p></td>
                        <td width="80" align="right"><p><? $fin_main_sort=$main_finish_fabric_req_qnty+$short_finish_fabric_req_qnty; echo number_format($fin_main_sort,2); ?></p></td>
                        <td width="80" align="right"><p><? $grey_main_sort=$main_grey_fabric_req_qnty+$short_grey_fabric_req_qnty; echo number_format($grey_main_sort,2) ?></p></td>
                        <td width="80" align="right"><p><a href="javascript:open_po_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','Yarn Info','yarn_popup_qty',2)"><? echo number_format($total_issued,2); ?></a></p></td>
                        <td width="80" align="right"><p><a href="javascript:open_po_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','Yarn Info','yarn_popup_value',2,<? echo $txt_conv_rate; ?>)"><? echo number_format($total_issued_value,2); ?></a></p></td>


                        <td width="80" align="right"><p><? echo number_format($grey_roll_recv_Inhouse,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($grey_roll_recv_outbound,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($knit_gray_roll_rec_qty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($knitting_bill_in_qty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($knitting_bill_out_qty,2); ?></p></td>


                        <td width="80" align="right"><p><a href="javascript:open_bill_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','Knitting Bill Info','knitting_bill_popup',2,<? echo $txt_conv_rate; ?>)"><? echo number_format($knitting_bill_qty,2); ?></a></p></td>
                        <td width="80" align="right"><p><? echo number_format($knitting_bill_value,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($finish_delevToStore_qnty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($finish_delevToStore_grey_qnty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($batch_qty,2); ?></p></td>


                        <td width="80" align="right"><p><? echo number_format($dyeing_bill_in_qty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($dyeing_bill_out_qty,2); ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($finishing_bill_qty,2); ?></p></td>


                        <td width="80" align="right"><p><a href="javascript:open_bill_popup('<? echo implode(',',array_unique(explode(",",$po_id)));?>','Dyeing Bill Info','dyeing_bill_popup',2,<? echo $txt_conv_rate; ?>)"><? echo number_format($dyeing_bill_qty,2); ?></a></p></td>
                        <td width="80" align="right"><p><? echo number_format($dyeing_bill_value,2); ?></p></td>
                        <td width="80" align="center"><p><? echo change_date_format($val["last_ship_date"]); ?></p></td>
                        <td width="80" align="center"><p><? echo change_date_format($ex_factory_max_date); ?></p></td>
                        <td width="" align="center"><p><? echo $shipment_status[$val["shiping_status"]]; ?></p></td>
					</tr>
					<?
					$tot_order_qty+=$val["po_qnty"];
					$tot_ex_factory_qty+=$ex_factory_qty;
					$tot_invoice_qty+=$invoice_qty;
					$tot_main_finish_fabric_req_qnty+=$main_finish_fabric_req_qnty;
					$tot_main_grey_fabric_req_qnty+=$main_grey_fabric_req_qnty;
					$tot_short_finish_fabric_req_qnty+=$short_finish_fabric_req_qnty;
					$tot_short_grey_fabric_req_qnty+=$short_grey_fabric_req_qnty;
					$tot_fin_main_sort+=$fin_main_sort;
					$tot_grey_main_sort+=$grey_main_sort;
					$tot_total_issued+=$total_issued;
					$tot_total_issued_value+=$total_issued_value;
					$tot_grey_roll_recv_Inhouse+=$grey_roll_recv_Inhouse;
					$tot_grey_roll_recv_outbound+=$grey_roll_recv_outbound;
					$tot_knit_gray_roll_rec_qty+=$knit_gray_roll_rec_qty;

					$tot_knitting_bill_in_qty+=$knitting_bill_in_qty;
					$tot_knitting_bill_out_qty+=$knitting_bill_out_qty;

					$tot_knitting_bill_qty+=$knitting_bill_qty;
					$tot_knitting_bill_value+=$knitting_bill_value;
					$tot_finish_delevToStore_qnty+=$finish_delevToStore_qnty;
					$tot_finish_delevToStore_grey_qnty+=$finish_delevToStore_grey_qnty;
					$tot_batch_qnty+=$batch_qty;

					$tot_dyeing_bill_in_qty+=$dyeing_bill_in_qty;
					$tot_dyeing_bill_out_qty+=$dyeing_bill_out_qty;
					$tot_finishing_bill_qty+=$finishing_bill_qty;

					$tot_dyeing_bill_qty+=$dyeing_bill_qty;
					$tot_dyeing_bill_value+=$dyeing_bill_value;
					$i++;
				}
				?>
            </table>
        </div>

        <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tr style="font-size:13px">
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>   
                <td width="100">&nbsp;</td>   
                <td width="100">&nbsp;</td>   
                <td width="50">&nbsp;</td>
                <td width="50">&nbsp;</td>             
                 <td width="80">Total:</td>
                <td width="80" align="right" id="td_order_qty"><? echo number_format($tot_order_qty,2); ?></td>
                <td width="100" align="right" id="td_ex_factory_qty"><? echo number_format($tot_ex_factory_qty,2); ?></td> 
                <td width="80"></td>
                <td width="80" align="right" id="td_invoice_qty"><? echo number_format($tot_invoice_qty,2); ?></td>
                <td width="90" align="right" id="td_main_finish_fabric_req_qnty"><? echo number_format($tot_main_finish_fabric_req_qnty,2); ?></td>
                <td width="80" align="right" id="td_main_grey_fabric_req_qnty"><? echo number_format($tot_main_grey_fabric_req_qnty,2); ?></td>
                <td width="80" align="right" id="td_short_finish_fabric_req_qnty"><? echo number_format($tot_short_finish_fabric_req_qnty,2); ?></td>
                <td width="80" align="right" id="td_short_grey_fabric_req_qnty"><? echo number_format($tot_short_grey_fabric_req_qnty,2); ?></td>
                <td width="80" align="right" id="td_fin_main_sort"><? echo number_format($tot_fin_main_sort,2); ?></td>
                <td width="80" align="right" id="td_grey_main_sort"><? echo number_format($tot_grey_main_sort,2); ?></td>
                <td width="80" align="right" id="td_total_issued"><? echo number_format($tot_total_issued,2); ?></td>
                <td width="80" align="right" id="td_total_issued_value"><? echo number_format($tot_total_issued_value,2); ?></td>


                <td width="80" align="right" id="td_total_grey_roll_recv_in"><? echo number_format($tot_grey_roll_recv_Inhouse,2); ?></td>
                <td width="80" align="right" id="td_total_grey_roll_recv_out"><? echo number_format($tot_grey_roll_recv_outbound,2); ?></td>
                <td width="80" align="right" id="td_knit_gray_roll_rec_qty"><? echo number_format($tot_knit_gray_roll_rec_qty,2); ?></td>
                <td width="80" align="right" id="td_total_knitting_bill_qty_in"><? echo number_format($tot_knitting_bill_in_qty,2); ?></td>
                <td width="80" align="right" id="td_total_knitting_bill_qty_out"><? echo number_format($tot_knitting_bill_out_qty,2); ?></td>


                <td width="80" align="right" id="td_knitting_bill_qty"><? echo number_format($tot_knitting_bill_qty,2); ?></td>
                <td width="80" align="right" id="td_knitting_bill_value"><? echo number_format($tot_knitting_bill_value,2); ?></td>
                <td width="80" align="right" id="td_finish_delevToStore_qnty"><? echo number_format($tot_finish_delevToStore_qnty,2); ?></td>
                <td width="80" align="right" id="td_finish_delevToStore_gry_qnty"><? echo number_format($tot_finish_delevToStore_grey_qnty,2); ?></td>
                <td width="80" align="right" id="td_batch_qnty"><? echo number_format($tot_batch_qnty,2); ?></td>


                <td width="80" align="right" id="td_total_dyeing_bill_qty_in"><? echo number_format($tot_dyeing_bill_in_qty,2); ?></td>
                <td width="80" align="right" id="td_total_dyeing_bill_qty_out"><? echo number_format($tot_dyeing_bill_out_qty,2); ?></td>
                <td width="80" align="right" id="td_total_finishing_bill_qty"><? echo number_format($tot_finishing_bill_qty,2); ?></td>


                <td width="80" align="right" id="td_dyeing_bill_qty"><? echo number_format($tot_dyeing_bill_qty,2); ?></td> 
                <td width="80" align="right" id="td_dyeing_bill_value"><? echo number_format($tot_dyeing_bill_value,2); ?></td>
                <td width="80"></td>
                <td width="80"></td>
                <td width=""></td>
            </tr>
       	</table>
    </div>
	<?
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****1****$type";
    exit();
}

if($action=="booking_popup")//not used
{
 	echo load_html_head_contents("Booking Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	/*echo $from.'_'.$to;//$job_no;
	die;*/
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:530px">
            <div style="width:100%">
            <?
     $sql_wo="select a.is_short,a.booking_no,a.booking_type,b.po_break_down_id,
	(CASE WHEN a.fabric_source=1 and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS grey_req_qnty,
	(CASE WHEN a.fabric_source=1 and a.item_category=2 THEN b.fin_fab_qnty ELSE 0 END) AS fin_fab_qnty,
	(CASE WHEN a.fabric_source in(1,2) and a.item_category=3 THEN b.grey_fab_qnty ELSE 0 END) AS woven_req_qnty,
	(CASE WHEN a.fabric_source in(1,2) and a.item_category=3 THEN b.fin_fab_qnty ELSE 0 END) AS fin_woven_req_qnty,
	
	(b.fin_fab_qnty) as fin_fab_qnty,
	(CASE WHEN a.item_category=12 and a.process=35 THEN b.wo_qnty ELSE 0 END) AS aop_wo_qnty
	from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and b.po_break_down_id in($po_id) order by a.booking_no,a.booking_type";
	
	 $sql_book=sql_select($sql_wo);
	// echo $type.'ddd';
	
	foreach ($sql_book as $brow)
	{
		if($type==1)
		{
			$qty_type=$brow[csf("grey_req_qnty")];
		} 
		else $qty_type=$brow[csf("fin_fab_qnty")];
		
		if($brow[csf("grey_req_qnty")]>0 && $brow[csf("booking_type")]==1 && $brow[csf("is_short")]==2)
		{
		$main_booking_req_arr[$brow[csf("booking_no")]]['main']+=$qty_type;
		}
		else if($brow[csf("grey_req_qnty")]>0 && $brow[csf("booking_type")]==1 && $brow[csf("is_short")]==1)
		{
		$short_booking_req_arr[$brow[csf("booking_no")]]['short']+=$qty_type;
		}
		else if($brow[csf("grey_req_qnty")]>0 && $brow[csf("booking_type")]==4)
		{
		$samp_booking_req_arr[$brow[csf("booking_no")]]['sample']+=$qty_type;
		}
		
	}
			?>
          <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
            <caption> Main Fabric Booking</caption>
                <thead>
                    <tr>
                        <th width="20">SL</th>
                        <th width="100">Booking No</th>
                        <th width="100">Booking Grey Qty</th>
                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
				$tot_grey_main=0;
                foreach($main_booking_req_arr as $booking_no=>$row)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trm_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trm_<? echo $i; ?>">
                        <td width="20"><? echo $i; ?></td>
                        <td width="100"><? echo $booking_no; ?></td>
                        <td width="100" align="right"><? echo number_format($row[("main")],2); ?></td>
                    </tr>
                    <?
                    $tot_grey_main+=$row[("main")];
					
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th align="right"><? echo number_format($tot_grey_main,2); ?></th>
                </tr>
                
                </tfoot>
            </table>
            
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
            <caption> Short Fabric Booking</caption>
                <thead>
                    <tr>
                        <th width="20">SL</th>
                        <th width="100">Booking No</th>
                        <th width="100">Booking Grey Qty</th>
                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
				$tot_grey_short=0;
                foreach($short_booking_req_arr as $booking_no=>$row)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trshort_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trshort_<? echo $i; ?>">
                        <td width="20"><? echo $i; ?></td>
                        <td width="100"><? echo $booking_no; ?></td>
                        <td width="100" align="right"><? echo number_format($row[("short")],2); ?></td>
                    </tr>
                    <?
                    $tot_grey_short+=$row[("short")];
					
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th align="right"><? echo number_format($tot_grey_short,2); ?></th>
                </tr>
                
                </tfoot>
            </table>
            
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
            <caption> Sample Fabric Booking</caption>
                <thead>
                    <tr>
                        <th width="20">SL</th>
                        <th width="100">Booking No</th>
                        <th width="100">Booking Grey Qty</th>
                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
				$tot_grey_sample=0;
                foreach($samp_booking_req_arr as $booking_no=>$row)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">
                        <td width="20"><? echo $i; ?></td>
                        <td width="100"><? echo $booking_no; ?></td>
                        <td width="100" align="right"><? echo number_format($row[("sample")],2); ?></td>
                    </tr>
                    <?
                    $tot_grey_sample+=$row[("sample")];
					
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th align="right"><? echo number_format($tot_grey_sample,2); ?></th>
                </tr>
                
                </tfoot>
            </table>
        </div>
		</fieldset>
	</div>
	<?
    exit();
}

if($action=="yarn_popup_qty")
{
 	echo load_html_head_contents("Yarn Qty Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
	
	$sqlWO="SELECT a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($po_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
	foreach($resultWo as $woRow)
	{
		$fab_source_ids.=$woRow[csf('fabric_source')].',';
	}
	$fab_source=rtrim($fab_source_ids,',');
	$fab_source_id=array_unique(explode(",",$fab_source));
	?>
	<script>

		function print_window()
		{
			//document.getElementById('scroll_body').style.overflow="auto";
			//document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			//document.getElementById('scroll_body').style.overflowY="scroll";
			//document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:865px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="10"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                    <th width="105">Issue Id</th>
                    <th width="90">Issue To</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="75">Issue Date</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="80">Yarn Description</th>
                    <th width="90">Issue Qnty (In)</th>
                    <th>Issue Qnty (Out)</th>
				</thead>
                <?
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;

				$sql="SELECT a.id as issue_id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id, a.issue_basis, d.requisition_no from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d 
				where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($po_id) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose  in (1,4) 
				group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id, a.issue_basis, d.requisition_no order by a.issue_date ASC";
				// echo $sql;
                $result=sql_select($sql);
                foreach($result as $row)
				{
					if($row[csf('issue_basis')] == 3){
						$requisition_no_arr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
					}
					$issue_id_arr[$row[csf("issue_id")]] = $row[csf("issue_id")];
				}
				$requisition_no_arr = array_filter($requisition_no_arr);

				if(!empty($requisition_no_arr))
				{
					$requ_booking_no_arr = return_library_array("select a.requisition_no, c.booking_no from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c where a.knit_id = b.id and b.mst_id = c.id and a.status_active=1 and a.requisition_no in (".implode(",", $requisition_no_arr).") group by a.requisition_no, c.booking_no","requisition_no","booking_no");					
				}
                
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						
					$issue_to="";
					if($row[csf('knit_dye_source')]==1) $issue_to=$company_library[$row[csf('knit_dye_company')]]; 
					else $issue_to=$supplier_details[$row[csf('knit_dye_company')]];
						
                   	foreach($fab_source_id as $fsid)
					{
						if($fsid==1)
						{
						 	$yarn_issued=$row[csf('issue_qnty')];	
						}
						
					}
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="90"><p><? echo $issue_to; ?></p></td>
                        <td width="105">
                        	<p>
                        	<? 
                        		if($row[csf('issue_basis')] == 3){
									echo $requ_booking_no_arr[$row[csf("requisition_no")]];
								}
								else if($row[csf('issue_basis')] == 1)
								{
									echo $row[csf('booking_no')];
								}
                        		
                        	?>
                        	&nbsp;
                        	</p>
                        </td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="80"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90">
							<? 
								if($row[csf('knit_dye_source')]!=3)
								{
									echo number_format($yarn_issued,2);
									$total_yarn_issue_qnty+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<? 
								if($row[csf('knit_dye_source')]==3)
								{ 
									echo number_format($yarn_issued,2); 
									$total_yarn_issue_qnty_out+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
	                <?
	                $total_issue = $total_yarn_issue_qnty+$total_yarn_issue_qnty_out;
	                $i++;
                }
				unset($result);
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
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_issue,2);?></td>
                </tr>
                <thead>
                    <th colspan="10"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th width="105">Return Id</th>
                    <th width="90">Return From</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="75">Return Date</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="80">Yarn Description</th>
                    <th width="90">Return Qnty (In)</th>
                    <th>Return Qnty (Out)</th>
               	</thead>
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;

				if(!empty($issue_id_arr))
				{
					$issue_id_cond = " and a.issue_id in (".implode(',', $issue_id_arr).") ";
				}
                $sql="SELECT a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, sum(b.reject_qty) as reject_qty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id, a.receive_basis 
                from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d 
                where a.id=d.mst_id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($po_id) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose  in (1,4) $issue_id_cond 
                group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id, a.receive_basis order by a.receive_date ASC";
                // echo $sql;
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$return_from="";
					if($row[csf('knitting_source')]==1) $return_from=$company_library[$row[csf('knitting_company')]]; 
					else $return_from=$supplier_details[$row[csf('knitting_company')]];
						
                    $yarn_returned=$row[csf('returned_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="90"><p><? echo $return_from; ?></p></td>
                        <td width="105"><p>
                        	<? 
                        		if($row[csf('receive_basis')] == 3)
                        		{
									echo $requ_booking_no_arr[$row[csf("booking_no")]];
								}
								else if($row[csf('receive_basis')] == 1)
								{
									echo $row[csf('booking_no')];
								}
                        	?>&nbsp;</p>
                        </td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="80"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90">
							<? 
								if($row[csf('knitting_source')]!=3)
								{
									echo number_format($yarn_returned,2);
									$total_yarn_return_qnty+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<? 
								if($row[csf('knitting_source')]==3)
								{ 
									echo number_format($yarn_returned,2); 
									$total_yarn_return_qnty_out+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
	                <?
	                $return_qnty = $total_yarn_return_qnty+$total_yarn_return_qnty_out;
	                $i++;
                }
				unset($result);
                $total_balence = $total_issue-$return_qnty;
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
                    <td align="right"><? echo number_format($total_yarn_return_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Return Total</td>
                    <td align="right"><? echo number_format($return_qnty,2);?></td>
                </tr>

                <thead>
                    <th colspan="10"><b>Yarn Reject Return</b></th>
                </thead>
                <thead>
                	<th width="105">Return Id</th>
                    <th width="90">Return From</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="75">Return Date</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="80">Yarn Description</th>
                    <th width="90">Return Qnty (In)</th>
                    <th>Return Qnty (Out)</th>
               	</thead>
                <?
                $total_yarn_reject_return_qnty=0; $total_yarn_reject_return_qnty_out=0;
                // echo $sql;
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$return_from="";
					if($row[csf('knitting_source')]==1) $return_from=$company_library[$row[csf('knitting_company')]]; 
					else $return_from=$supplier_details[$row[csf('knitting_company')]];
						
                    $yarn_reject_returned=$row[csf('reject_qty')];
                    if ($yarn_reject_returned>0) 
                    {
	                    ?>
	                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                        <td width="90"><p><? echo $return_from; ?></p></td>
	                        <td width="105">
	                        	<p>
	                        	<? 
	                        		if($row[csf('receive_basis')] == 3)
	                        		{
										echo $requ_booking_no_arr[$row[csf("booking_no")]];
									}
									else if($row[csf('receive_basis')] == 1)
									{
										echo $row[csf('booking_no')];
									}
	                        	?>
	                        	&nbsp;
	                        	</p>
	                        </td>
	                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
	                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
	                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
	                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
	                        <td width="80"><p><? echo $row[csf('product_name_details')]; ?></p></td>
	                        <td align="right" width="90">
								<? 
									if($row[csf('knitting_source')]!=3)
									{
										echo number_format($yarn_reject_returned,2);
										$total_yarn_reject_return_qnty+=$yarn_reject_returned;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                        <td align="right">
								<? 
									if($row[csf('knitting_source')]==3)
									{ 
										echo number_format($yarn_reject_returned,2); 
										$total_yarn_reject_return_qnty_out+=$yarn_reject_returned;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                    </tr>
		                <?
		                $reject_return_qnty = $total_yarn_reject_return_qnty+$total_yarn_reject_return_qnty_out;
		                $i++;
	            	}
                }
				unset($result);
				$net_yarn_issue_in=$total_yarn_issue_qnty-$total_yarn_return_qnty;
				$net_yarn_issue_out=$total_yarn_issue_qnty_out-$total_yarn_return_qnty_out;
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
                    <td align="right"><? echo number_format($total_yarn_reject_return_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_reject_return_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Reject Return Total :</td>
                    <td align="right"><? echo number_format($reject_return_qnty,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="8">Net Yarn Issue (Without Reject Qty.) :</td>
                    <th align="right"><? echo number_format($net_yarn_issue_in,2);?></td>
                    <td align="right"><? echo number_format($net_yarn_issue_out,2);?></td>
                </tr>

                <tfoot>    
                    <tr>
                        <th align="right" colspan="9">Net Yarn Issue (Without Reject Qty.) :</th>
                        <th align="right"><? echo number_format($total_balence,2);?></th>
                    </tr>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
	<?
	exit();
}

if($action=="yarn_popup_value")
{
 	echo load_html_head_contents("Yarn Value Details", "../../../../", 1, 1,$unicode,'','');
 	// echo "<pre>"; print_r($_REQUEST);
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
	
	$sqlWO="SELECT a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($po_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
	foreach($resultWo as $woRow)
	{
		$fab_source_ids.=$woRow[csf('fabric_source')].',';
	}
	$fab_source=rtrim($fab_source_ids,',');
	$fab_source_id=array_unique(explode(",",$fab_source));
	?>
	<script>

		function print_window()
		{
			//document.getElementById('scroll_body').style.overflow="auto";
			//document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			//document.getElementById('scroll_body').style.overflowY="scroll";
			//document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:940px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:935px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="930" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="11"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                    <th width="105">Issue Id</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="75">Issue Date</th>
                    <th width="60">Lot No</th>
                    <th width="80">Yarn Description</th>
                    <th width="90">Issue Qnty (In)</th>
                    <th width="90">Issue Qnty (Out)</th>
                    <th width="70">Total Qnty</th>
                    <th width="70">Avg Rate (USD)</th>
                    <th>Yarn Cost</th>
				</thead>
                <?
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;$total_issue_amount=0;

				$sql="SELECT a.id as issue_id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id, a.issue_basis, d.requisition_no, d.cons_rate, d.cons_amount 
				from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d 
				where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($po_id) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose  in (1,4) 
				group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id, a.issue_basis, d.requisition_no, d.cons_rate, d.cons_amount order by a.issue_date ASC";
				// echo $sql;
                $result=sql_select($sql);
                foreach($result as $row)
				{
					if($row[csf('issue_basis')] == 3){
						$requisition_no_arr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
					}
					$issue_id_arr[$row[csf("issue_id")]] = $row[csf("issue_id")];
				}
				$requisition_no_arr = array_filter($requisition_no_arr);

				if(!empty($requisition_no_arr))
				{
					$requ_booking_no_arr = return_library_array("select a.requisition_no, c.booking_no from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c where a.knit_id = b.id and b.mst_id = c.id and a.status_active=1 and a.requisition_no in (".implode(",", $requisition_no_arr).") group by a.requisition_no, c.booking_no","requisition_no","booking_no");					
				}
                
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						
					$issue_to="";
					if($row[csf('knit_dye_source')]==1) $issue_to=$company_library[$row[csf('knit_dye_company')]]; 
					else $issue_to=$supplier_details[$row[csf('knit_dye_company')]];
						
                   	foreach($fab_source_id as $fsid)
					{
						if($fsid==1)
						{
						 	$yarn_issued=$row[csf('issue_qnty')];	
						}
						
					}
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="105">
                        	<p>
                        	<? 
                        		if($row[csf('issue_basis')] == 3){
									echo $requ_booking_no_arr[$row[csf("requisition_no")]];
								}
								else if($row[csf('issue_basis')] == 1)
								{
									echo $row[csf('booking_no')];
								}
                        		
                        	?>
                        	&nbsp;
                        	</p>
                        </td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="80"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90">
							<? 
								if($row[csf('knit_dye_source')]!=3)
								{
									echo number_format($yarn_issued,2);
									$total_yarn_issue_qnty+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td width="90" align="right">
							<? 
								if($row[csf('knit_dye_source')]==3)
								{ 
									echo number_format($yarn_issued,2); 
									$total_yarn_issue_qnty_out+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td width="70" align="right"><p><? echo $yarn_issued; ?></p></td>
                        <td width="70" align="right"><p><? $issue_avg_rate_usd=$row[csf('cons_rate')]/$conv_rate; echo number_format($issue_avg_rate_usd,2); ?></p></td>
                        <td align="right"><p><? $issue_amount_usd=$row[csf('cons_amount')]/$conv_rate; echo number_format($issue_amount_usd,2); ?></p></td>
                    </tr>
	                <?
	                $total_issue = $total_yarn_issue_qnty+$total_yarn_issue_qnty_out;
	                $total_issue_amount += $issue_amount_usd;
	                $i++;
                }
				unset($result);
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2);?></td>
                    <td align="right"><? echo number_format($total_issue,2);?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_issue_amount,2);?></td>
                </tr>

                <thead>
                    <th colspan="11"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th width="105">Return Id</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="75">Return Date</th>
                    <th width="60">Lot No</th>
                    <th width="80">Yarn Description</th>
                    <th width="90">Return Qnty (In)</th>
                    <th width="90">Return Qnty (Out)</th>
                    <th width="70">Total Qnty</th>
                    <th width="70">Avg Rate (USD)</th>
                    <th>Yarn Cost</th>
               	</thead>
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;$total_issue_rtn_amount=0;

				if(!empty($issue_id_arr))
				{
					$issue_id_cond = " and a.issue_id in (".implode(',', $issue_id_arr).") ";
				}
                $sql="SELECT a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, sum(b.reject_qty) as reject_qty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id, a.receive_basis, d.cons_rate, d.cons_amount
                from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d 
                where a.id=d.mst_id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($po_id) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose  in (1,4) $issue_id_cond 
                group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id, a.receive_basis, d.cons_rate, d.cons_amount order by a.receive_date ASC";
                // echo $sql;
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$return_from="";
					if($row[csf('knitting_source')]==1) $return_from=$company_library[$row[csf('knitting_company')]]; 
					else $return_from=$supplier_details[$row[csf('knitting_company')]];
						
                    $yarn_returned=$row[csf('returned_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="105"><p>
                        	<? 
                        		if($row[csf('receive_basis')] == 3)
                        		{
									echo $requ_booking_no_arr[$row[csf("booking_no")]];
								}
								else if($row[csf('receive_basis')] == 1)
								{
									echo $row[csf('booking_no')];
								}
                        	?>&nbsp;</p>
                        </td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="80"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90">
							<? 
								if($row[csf('knitting_source')]!=3)
								{
									echo number_format($yarn_returned,2);
									$total_yarn_return_qnty+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td width="90" align="right">
							<? 
								if($row[csf('knitting_source')]==3)
								{ 
									echo number_format($yarn_returned,2); 
									$total_yarn_return_qnty_out+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td width="70" align="right"><p><? echo $yarn_returned; ?></p></td>
                        <td width="70" align="right"><p><? $issue_rtn_avg_rate_usd=$row[csf('cons_rate')]/$conv_rate; echo number_format($issue_rtn_avg_rate_usd,2); ?></p></td>
                        <td align="right"><p><? $issue_rtn_amount_usd=$row[csf('cons_amount')]/$conv_rate; echo number_format($issue_rtn_amount_usd,2); ?></p></td>
                    </tr>
	                <?
	                $return_qnty = $total_yarn_return_qnty+$total_yarn_return_qnty_out;
	                $total_issue_rtn_amount += $issue_rtn_amount_usd;
	                $i++;
                }
				unset($result);
                $total_balence = $total_issue-$return_qnty;
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty_out,2);?></td>
                    <td align="right"><? echo number_format($return_qnty,2);?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_issue_rtn_amount,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="6">Net Yarn Issue</td>
                    <th align="right"><? echo number_format($total_yarn_issue_qnty-$total_yarn_return_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out-$total_yarn_return_qnty_out,2);?></td>
                    <td align="right"><? echo number_format($total_issue-$return_qnty,2);?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_issue_amount-$total_issue_rtn_amount,2);?></td>
                </tr>
            </table>	
		</div>
	</fieldset>  
	<?
	exit();
}

if($action=="knitting_bill_popup")
{
 	echo load_html_head_contents("Yarn Value Details", "../../../../", 1, 1,$unicode,'','');
 	// echo "<pre>"; print_r($_REQUEST);
	extract($_REQUEST);
	$supplier_library_arr=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	?>
	<script>

		function print_window()
		{
			//document.getElementById('scroll_body').style.overflow="auto";
			//document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			//document.getElementById('scroll_body').style.overflowY="scroll";
			//document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:540px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:565px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="560" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="7"><b>In Bound Bill</b></th>
				</thead>
				<thead>
                    <th width="40">SL</th>
                    <th width="105">Bill No</th>
                    <th width="105">Party</th>
                    <th width="80">System Challan</th>
                    <th width="75">Party Challan</th>
                    <th width="60">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
				</thead>
                <?
                $i=1; $total_knitting_bill_in_qty=0;$total_knitting_bill_in_amount=0;

				$knitting_bill_in_data=sql_select("SELECT a.id as bill_id, a.bill_no, a.party_id, b.challan_no, b.order_id, sum(b.delivery_qty) as knitting_bill_in_qty, sum(b.amount) as knitting_bill_in_amount, b.currency_id 
				from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b
				where a.id=b.mst_id and b.order_id in($po_id) and a.process_id=2 and b.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by a.id, a.bill_no, a.party_id, b.challan_no, b.order_id, b.currency_id");
				$knitting_bill_in_arr=array();
				foreach($knitting_bill_in_data as $row)
				{
					$knitting_bill_in_arr[$row[csf("bill_id")]]["bill_no"]=$row[csf("bill_no")];
					$knitting_bill_in_arr[$row[csf("bill_id")]]["party_id"]=$row[csf("party_id")];
					$knitting_bill_in_arr[$row[csf("bill_id")]]["challan_no"].=$row[csf("challan_no")].',';
					$knitting_bill_in_arr[$row[csf("bill_id")]]["knitting_bill_in_qty"]+=$row[csf("knitting_bill_in_qty")];

					$currency_id=$row[csf('currency_id')];
					if($currency_id==1) // Tk to USD
					{
						$knitting_bill_in_arr[$row[csf("bill_id")]]["knitting_bill_in_amount"]+=$row[csf("knitting_bill_in_amount")]/$conv_rate;
					}
					else // USD
					{
						$knitting_bill_in_arr[$row[csf("bill_id")]]["knitting_bill_in_amount"]+=$row[csf("knitting_bill_in_amount")];
					}
				}
				// echo "<pre>";print_r($knitting_bill_in_arr);
				foreach($knitting_bill_in_arr as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$challan_no=implode(",",array_unique(explode(",",$row['challan_no'])));
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><p><? echo $i; ?></p></td>
                    	<td width="105" align="center"><p><? echo $row['bill_no']; ?></p></td>
                        <td width="105" align="center"><p><? echo $company_library[$row["party_id"]]; ?></p></td>
                        <td width="80"><p><? echo chop($challan_no,','); ?></p></td>
                        <td width="75" align="center"><? echo ''; ?></td>
                        <td width="60" align="right"><p><? echo number_format($row["knitting_bill_in_qty"],2); ?></p></td>
                        <td align="right"><p><? echo number_format($row["knitting_bill_in_amount"],2); ?></p></td>
                    </tr>
	                <?
	                $total_knitting_bill_in_qty += $row["knitting_bill_in_qty"];
	                $total_knitting_bill_in_amount += $row["knitting_bill_in_amount"];
	                $i++;
                }
				unset($knitting_bill_in_data);
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_knitting_bill_in_qty,2);?></td>
                    <td align="right"><? echo number_format($total_knitting_bill_in_amount,2);?></td>
                </tr>

                <thead>
					<th colspan="7"><b>Out Bound Bill</b></th>
				</thead>
				<thead>
                    <th width="40">SL</th>
                    <th width="105">Bill No</th>
                    <th width="105">Party</th>
                    <th width="80">System Challan</th>
                    <th width="75">Party Challan</th>
                    <th width="60">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
				</thead>
                <?
                $total_knitting_bill_out_qty=0; $total_knitting_bill_out_amount=0;

				$knitting_bill_out_data=sql_select("SELECT a.id as bill_id, a.bill_no, a.supplier_id, b.challan_no, b.order_id, sum(b.receive_qty) as knitting_bill_out_qty, sum(b.amount) as knitting_bill_out_amount, b.currency_id, b.receive_id
				from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b 
				where a.id=b.mst_id and b.order_id in($po_id) and a.process_id=2 and b.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by a.id, a.bill_no, a.supplier_id, b.challan_no, b.order_id, b.currency_id, b.receive_id");

				$knitting_bill_out_arr=array();
				$knitting_challan_arr=array();$knitting_receive_id_arr=array();
				foreach($knitting_bill_out_data as $row)
				{
					$knitting_bill_out_arr[$row[csf("bill_id")]]["bill_no"]=$row[csf("bill_no")];
					$knitting_bill_out_arr[$row[csf("bill_id")]]["supplier_id"]=$row[csf("supplier_id")];
					$knitting_bill_out_arr[$row[csf("bill_id")]]["challan_no"].=$row[csf("challan_no")].',';
					$knitting_bill_out_arr[$row[csf("bill_id")]]["receive_id"].=$row[csf("receive_id")].',';
					$knitting_bill_out_arr[$row[csf("bill_id")]]["knitting_bill_out_qty"]+=$row[csf("knitting_bill_out_qty")];

					$currency_id=$row[csf('currency_id')];
					if($currency_id==1) // Tk to USD
					{
						$knitting_bill_out_arr[$row[csf("bill_id")]]["knitting_bill_out_amount"]+=$row[csf("knitting_bill_out_amount")]/$conv_rate;
					}
					else // USD
					{
						$knitting_bill_out_arr[$row[csf("bill_id")]]["knitting_bill_out_amount"]+=$row[csf("knitting_bill_out_amount")];
					}

					$knitting_challan_arr[$row[csf("challan_no")]].=$row[csf("challan_no")].',';
					$knitting_receive_id_arr[$row[csf("receive_id")]].=$row[csf("receive_id")].',';
				}
				$all_sys_challan = chop(implode(",",$knitting_challan_arr),',');
				$all_receive_id = chop(implode(",",$knitting_receive_id_arr),',');
				//print_r($challan_no2);
				$challan_sql="SELECT a.id, a.recv_number_prefix_num, a.challan_no, c.po_breakdown_id, a.recv_number
				from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
				where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=3 and a.company_id=$companyID and a.entry_form in (2,22,58) and c.entry_form in (2,22,58)
				and a.item_category=13 and a.receive_basis in (0,1,2,4,9,10,11) and b.trans_id>0 and nvl(c.booking_without_order,0)=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in($all_receive_id) and a.recv_number_prefix_num in($all_sys_challan) and c.po_breakdown_id in($po_id)
				group by a.id, a.recv_number_prefix_num, a.challan_no, c.po_breakdown_id, a.recv_number order by a.id";
				// echo $challan_sql;
				$challan_data=sql_select($challan_sql);
				$challan_data_arr=array();
				foreach($challan_data as $row)
				{
					$challan_data_arr[$row[csf("id")]]=$row[csf("challan_no")];
				}
				// print_r($challan_data_arr);
				foreach($knitting_bill_out_arr as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$sys_challan_no=implode(",",array_unique(explode(",",$row['challan_no'])));
					
					$sys_challan_arr=array_unique(explode(",",chop($row['receive_id'],',')));
					// print_r($sys_challan_arr);
					$party_challan="";
					foreach ($sys_challan_arr as $key => $receive_id) 
					{
						if ($party_challan=="") 
						{
							$party_challan=$challan_data_arr[$receive_id];
						}
						else
						{
							$party_challan=','.$challan_data_arr[$receive_id];
						}
					}
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><p><? echo $i; ?></p></td>
                    	<td width="105" align="center"><p><? echo $row['bill_no']; ?></p></td>
                        <td width="105" align="center"><p><? echo $supplier_library_arr[$row["supplier_id"]]; ?></p></td>
                        <td width="80"><p><? echo chop($sys_challan_no,','); ?></p></td>
                        <td width="75" align="center"><? echo $party_challan; ?></td>
                        <td width="60" align="right"><p><? echo number_format($row["knitting_bill_out_qty"],2); ?></p></td>
                        <td align="right"><p><? echo number_format($row["knitting_bill_out_amount"],2); ?></p></td>
                    </tr>
	                <?
	                $total_knitting_bill_out_qty += $row["knitting_bill_out_qty"];
	                $total_knitting_bill_out_amount += $row["knitting_bill_out_amount"];
	                $i++;
                }
				unset($knitting_bill_out_data);
				$g_total_knitting_bill_qty=$total_knitting_bill_out_qty+$total_knitting_bill_in_qty;
				$g_total_knitting_bill_amount=$total_knitting_bill_in_amount+$total_knitting_bill_out_amount;
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_knitting_bill_out_qty,2);?></td>
                    <td align="right"><? echo number_format($total_knitting_bill_out_amount,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="5">Grand Total</td>
                    <td align="right"><? echo number_format($g_total_knitting_bill_qty,2);?></td>
                    <td align="right"><? echo number_format($g_total_knitting_bill_amount,2);?></td>
                </tr>
            </table>	
		</div>
	</fieldset>  
	<?
	exit();
}

if($action=="dyeing_bill_popup")
{
 	echo load_html_head_contents("Yarn Value Details", "../../../../", 1, 1,$unicode,'','');
 	// echo "<pre>"; print_r($_REQUEST);
	extract($_REQUEST);
	$supplier_library_arr=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	?>
	<script>

		function print_window()
		{
			//document.getElementById('scroll_body').style.overflow="auto";
			//document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			//document.getElementById('scroll_body').style.overflowY="scroll";
			//document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:540px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:565px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="560" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="7"><b>In Bound Bill</b></th>
				</thead>
				<thead>
                    <th width="40">SL</th>
                    <th width="105">Bill No</th>
                    <th width="105">Party</th>
                    <th width="80">System Challan</th>
                    <th width="75">Party Challan</th>
                    <th width="60">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
				</thead>
                <?
                $i=1; $total_dyeing_bill_in_qty=0;$total_dyeing_bill_in_amount=0;

				$dyeing_bill_in_data=sql_select("SELECT a.id as bill_id, a.bill_no, a.party_id, b.challan_no, b.order_id, sum(b.delivery_qty) as dyeing_bill_in_qty, sum(b.amount) as dyeing_bill_in_amount, b.currency_id 
				from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b
				where a.id=b.mst_id and b.order_id in($po_id) and a.process_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by a.id, a.bill_no, a.party_id, b.challan_no, b.order_id, b.currency_id");
				$dyeing_bill_in_arr=array();
				foreach($dyeing_bill_in_data as $row)
				{
					$dyeing_bill_in_arr[$row[csf("bill_id")]]["bill_no"]=$row[csf("bill_no")];
					$dyeing_bill_in_arr[$row[csf("bill_id")]]["party_id"]=$row[csf("party_id")];
					$dyeing_bill_in_arr[$row[csf("bill_id")]]["challan_no"].=$row[csf("challan_no")].',';
					$dyeing_bill_in_arr[$row[csf("bill_id")]]["dyeing_bill_in_qty"]+=$row[csf("dyeing_bill_in_qty")];

					$currency_id=$row[csf('currency_id')];
					if($currency_id==1) // Tk to USD
					{
						$dyeing_bill_in_arr[$row[csf("bill_id")]]["dyeing_bill_in_amount"]+=$row[csf("dyeing_bill_in_amount")]/$conv_rate;
					}
					else // USD
					{
						$dyeing_bill_in_arr[$row[csf("bill_id")]]["dyeing_bill_in_amount"]+=$row[csf("dyeing_bill_in_amount")];
					}
				}
				// echo "<pre>";print_r($dyeing_bill_in_arr);
				foreach($dyeing_bill_in_arr as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$challan_no=implode(",",array_unique(explode(",",$row['challan_no'])));
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><p><? echo $i; ?></p></td>
                    	<td width="105" align="center"><p><? echo $row['bill_no']; ?></p></td>
                        <td width="105" align="center"><p><? echo $company_library[$row["party_id"]]; ?></p></td>
                        <td width="80"><p><? echo chop($challan_no,','); ?></p></td>
                        <td width="75" align="center"><? echo ''; ?></td>
                        <td width="60" align="right"><p><? echo number_format($row["dyeing_bill_in_qty"],2); ?></p></td>
                        <td align="right"><p><? echo number_format($row["dyeing_bill_in_amount"],2); ?></p></td>
                    </tr>
	                <?
	                $total_dyeing_bill_in_qty += $row["dyeing_bill_in_qty"];
	                $total_dyeing_bill_in_amount += $row["dyeing_bill_in_amount"];
	                $i++;
                }
				unset($dyeing_bill_in_data);
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_dyeing_bill_in_qty,2);?></td>
                    <td align="right"><? echo number_format($total_dyeing_bill_in_amount,2);?></td>
                </tr>

                <thead>
					<th colspan="7"><b>Out Bound Bill</b></th>
				</thead>
				<thead>
                    <th width="40">SL</th>
                    <th width="105">Bill No</th>
                    <th width="105">Party</th>
                    <th width="80">System Challan</th>
                    <th width="75">Party Challan</th>
                    <th width="60">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
				</thead>
                <?
                $total_dyeing_bill_out_qty=0; $total_dyeing_bill_out_amount=0;

				$dyeing_bill_out_data=sql_select("SELECT a.id as bill_id, a.bill_no, a.supplier_id, b.order_id, sum(b.receive_qty) as dyeing_bill_out_qty, sum(b.amount) as dyeing_bill_out_amount, b.currency_id, b.challan_no as party_challan, b.receive_id  
				from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b 
				where a.id=b.mst_id and b.order_id in($po_id) and a.process_id=4 and b.process_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by a.id, a.bill_no, a.supplier_id, b.order_id, b.currency_id, b.challan_no, b.receive_id");

				$dyeing_bill_out_arr=array();$finis_receive_id_arr=array();
				foreach($dyeing_bill_out_data as $row)
				{
					$dyeing_bill_out_arr[$row[csf("bill_id")]]["bill_no"]=$row[csf("bill_no")];
					$dyeing_bill_out_arr[$row[csf("bill_id")]]["supplier_id"]=$row[csf("supplier_id")];
					$dyeing_bill_out_arr[$row[csf("bill_id")]]["party_challan"].=$row[csf("party_challan")].',';
					$dyeing_bill_out_arr[$row[csf("bill_id")]]["receive_id"].=$row[csf("receive_id")].',';
					$dyeing_bill_out_arr[$row[csf("bill_id")]]["dyeing_bill_out_qty"]+=$row[csf("dyeing_bill_out_qty")];

					$currency_id=$row[csf('currency_id')];
					if($currency_id==1) // Tk to USD
					{
						$dyeing_bill_out_arr[$row[csf("bill_id")]]["dyeing_bill_out_amount"]+=$row[csf("dyeing_bill_out_amount")]/$conv_rate;
					}
					else // USD
					{
						$dyeing_bill_out_arr[$row[csf("bill_id")]]["dyeing_bill_out_amount"]+=$row[csf("dyeing_bill_out_amount")];
					}
					$finis_receive_id_arr[$row[csf("receive_id")]]=$row[csf("receive_id")];
				}
				// echo implode(",", $finis_receive_id_arr);die;
				$finis_receive_id_arr=array_unique(explode(",",implode(",", $finis_receive_id_arr)));
				// echo "<pre>"; print_r($finis_receive_id_arr);die;
				$all_receive_id = chop(implode(",",$finis_receive_id_arr),',');

				$challan_sql="SELECT a.id as mst_id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id as bookingno, 
				b.batch_id, b.prod_id, b.id as dtls_id, c.id, c.po_breakdown_id, d.booking_no_id, d.booking_no 
				FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d 
				WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and c.trans_type=1 and c.entry_form in (7,37,66,68) and c.trans_id!=0 and a.entry_form in (7,37,66,68) AND a.knitting_source=3 AND a.company_id=$companyID and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.id in($all_receive_id) and c.po_breakdown_id in($po_id)
				group by a.id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id, 
				b.batch_id, b.prod_id, b.id, c.id, c.po_breakdown_id, d.booking_no_id, d.booking_no 
				order by a.recv_number_prefix_num DESC";
				// echo $challan_sql;
				$challan_data=sql_select($challan_sql);
				$challan_data_arr=array();
				foreach($challan_data as $row)
				{
					// $challan_data_arr[$row[csf("id")]]=$row[csf("recv_number_prefix_num")];
					$challan_data_arr[$row[csf("challan_no")]]=$row[csf("recv_number_prefix_num")];
				}

				foreach($dyeing_bill_out_arr as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$party_challan=implode(",",array_unique(explode(",",$row['party_challan'])));

					// $sys_challan_arr=array_unique(explode(",",chop($row['receive_id'],',')));
					$sys_challan_arr=array_unique(explode(",",chop($row['party_challan'],',')));
					// print_r($sys_challan_arr);
					$sys_challan="";
					foreach ($sys_challan_arr as $key => $receive_id) 
					{
						if ($sys_challan=="") 
						{
							$sys_challan=$challan_data_arr[$receive_id];
						}
						else
						{
							$sys_challan=','.$challan_data_arr[$receive_id];
						}
					}
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><p><? echo $i; ?></p></td>
                    	<td width="105" align="center"><p><? echo $row['bill_no']; ?></p></td>
                        <td width="105" align="center"><p><? echo $supplier_library_arr[$row["supplier_id"]]; ?></p></td>
                        <td width="80"><p><? echo $sys_challan; ?></p></td>
                        <td width="75" align="center"><? echo chop($party_challan,','); ?></td>
                        <td width="60" align="right"><p><? echo number_format($row["dyeing_bill_out_qty"],2); ?></p></td>
                        <td align="right"><p><? echo number_format($row["dyeing_bill_out_amount"],2); ?></p></td>
                    </tr>
	                <?
	                $total_dyeing_bill_out_qty += $row["dyeing_bill_out_qty"];
	                $total_dyeing_bill_out_amount += $row["dyeing_bill_out_amount"];
	                $i++;
                }
				unset($dyeing_bill_out_data);
				$g_total_dyeing_bill_qty=$total_dyeing_bill_out_qty+$total_dyeing_bill_in_qty;
				$g_total_dyeing_bill_amount=$total_dyeing_bill_in_amount+$total_dyeing_bill_out_amount;
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_dyeing_bill_out_qty,2);?></td>
                    <td align="right"><? echo number_format($total_dyeing_bill_out_amount,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="5">Grand Total</td>
                    <td align="right"><? echo number_format($g_total_dyeing_bill_qty,2);?></td>
                    <td align="right"><? echo number_format($g_total_dyeing_bill_amount,2);?></td>
                </tr>
            </table>	
		</div>
	</fieldset>  
	<?
	exit();
}
?>
