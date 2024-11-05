<?php
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

/*$buyer_short_name_arr=return_library_array( "SELECT id, short_name from lib_buyer where status_active =1 and is_deleted=0",'id','short_name');
$company_short_name_arr=return_library_array( "SELECT id,company_short_name from lib_company where status_active =1 and is_deleted=0",'id','company_short_name');
$imge_arr=return_library_array( "SELECT id,master_tble_id,image_location from common_photo_library where status_active =1 and is_deleted=0",'id','image_location');
$party_arr=return_library_array( "SELECT id, buyer_name from  lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');*/

/*if($action == 'load_drop_down_buyer')  {
    echo create_drop_down('cbo_buyer_id', 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", 'id,buyer_name', 1, '-- Select Buyer --', $selected, '');
    exit();
}*/

if($action=="load_drop_down_buyer") {
	echo create_drop_down('cbo_buyer_id', 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name", 'id,buyer_name', 1, '-- Select Buyer --', $selected, '');
	exit();
}

/*if($action == 'load_drop_down_party') {
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1) {
		echo create_drop_down( 'cbo_party_id', 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Party --', $data[2], '');
		exit();
	}
	else
	{
		echo create_drop_down( 'cbo_party_id', 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name", 'id,buyer_name', 1, '-- Select Party --', $data[2], '' );
		exit();
	}
}*/

if($action=='load_drop_down_party') {
	$data=explode('_',$data);
	if($data[1]==1) {
		echo create_drop_down( "cbo_party_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "load_drop_down( 'requires/embellishment_production_status_report_controller', this.value, 'load_drop_down_party_location', 'party_location_td' );");
		exit();
	}
	else {
		echo create_drop_down( "cbo_party_id", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
}

if($action == 'report_generate_1') {
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array('select id, company_name from lib_company', 'id', 'company_name');

	$cbo_company_id = str_replace("'", '', $cbo_company_id);
	$cbo_party_id = str_replace("'", '', $cbo_party_id);
	$cbo_buyer_id = str_replace("'", '', $cbo_buyer_id);
	$cbo_based_on = str_replace("'", '', $cbo_based_on);
	$txt_wo_no = str_replace("'", '', $txt_wo_no);
	$txt_job_no = str_replace("'", '', $txt_job_no);
	$txt_buyer_po = str_replace("'", '', $txt_buyer_po);

	$txt_date_from = str_replace("'", "", $txt_date_from);
	$txt_date_to = str_replace("'", "", $txt_date_to);

	$result = array();

	$conditions = '';

	$conditions .= "a.company_id=$cbo_company_id ";
	if ($cbo_within_group != '') {
		$conditions .= " and a.within_group=$cbo_within_group ";
	}

	if ($cbo_party_id != 0) {
		$conditions .= " and a.party_id=$cbo_party_id ";
	}

	if ($txt_wo_no != '') {
		$txt_wo_no = str_replace("'", '', $txt_wo_no);
		$txt_wo_no = "'%".$txt_wo_no."%'";
		$conditions .= " and c.order_no like $txt_wo_no ";
	}

	if ($txt_job_no != '') {
		$txt_job_no = str_replace("'", '', $txt_job_no);
		$txt_job_no = "'%".$txt_job_no."%'";
		$conditions .= " and a.job_no like $txt_job_no ";
	}

	if ($cbo_buyer_id != 0) {
		$buyer_cond_deliv =  " and d.buyer_buyer='$cbo_buyer_id' ";
		$buyer_cond_bill =  " and b.buyer_buyer='$cbo_buyer_id' ";
	}

	if ($txt_buyer_po != '') {
		$txt_buyer_po = str_replace("'", '', $txt_buyer_po);
		$txt_buyer_po = "'%".$txt_buyer_po."%'";
		$conditions .= " and d.buyer_po_no like $txt_buyer_po ";
	}

	if($db_type==0) {
		// $conditions.=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		if ($txt_date_from!="" &&  $txt_date_to!="") $conditions .= "and a.delivery_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'"; else $conditions .="";
	}
	if($db_type==2) {
		// $conditions.=" and to_char(a.insert_date,'YYYY')=$data[4]";
		if ($txt_date_from!="" &&  $txt_date_to!="") $conditions .= "and a.delivery_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-",1)."'"; else $conditions .="";
	}

	/*$sql= "select a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.pay_mode,a.job_no,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved,d.grouping
	from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c,wo_po_break_down d
	where $conditions and d.job_no_mst=b.job_no and d.id=b.po_break_down_id and b.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_type=6 and a.entry_form=201 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.pay_mode,a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.job_no,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved,d.grouping order by a.id desc";*/

	/*$sql = "select a.id, a.booking_no, d.grouping, b.wo_qnty, b.rate, a.booking_date, a.job_no, b.po_break_down_id, b.gmt_item, c.emb_name, a.supplier_id, a.is_approved, a.ready_to_approved
    from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d
	where $conditions and d.job_no_mst = b.job_no
		and d.id = b.po_break_down_id and b.job_no = c.job_no and a.booking_no = b.booking_no
		and b.pre_cost_fabric_cost_dtls_id = c.id and a.booking_type = 6 and a.entry_form = 201 and a.status_active = 1
		and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
 	order by a.id desc";*/

 	$sql = "select a.id, a.delivery_no, to_char(a.delivery_date, 'Mon') as month, a.entry_form, a.job_no, a.party_id, a.within_group, b.buyer_po_id, b.delivery_qty, b.order_id as subcon_ord_id, c.order_no, d.buyer_po_no, d.buyer_style_ref, d.order_id
		from subcon_delivery_mst a, subcon_delivery_dtls b, subcon_ord_mst c, subcon_ord_dtls d
		where $conditions $buyer_cond_deliv and a.id=b.mst_id and a.entry_form=254 and a.job_no=d.job_no_mst and c.id=d.mst_id";

	// echo $sql;
	$delivery_result = sql_select($sql);

	$delivery_arr = array();
	foreach ($delivery_result as $row) {
		if (isset($delivery_arr[$row[csf('order_id')]])) {
			if(isset($delivery_arr[$row[csf('order_id')]][$row[csf('month')]])) {
				$delivery_arr[$row[csf('order_id')]][$row[csf('month')]]['delivery_qty'] += $row[csf('delivery_qty')];
			} else {
				$delivery_arr[$row[csf('order_id')]][$row[csf('month')]]['delivery_qty'] = $row[csf('delivery_qty')];
			}

			$delivery_arr[$row[csf('order_id')]]['total_delivery_qty'] += $row[csf('delivery_qty')];
		} else {
			$delivery_arr[$row[csf('order_id')]]['order_id'] = $row[csf('order_id')];
			$delivery_arr[$row[csf('order_id')]]['delivery_no'] = $row[csf('delivery_no')];
			$delivery_arr[$row[csf('order_id')]]['delivery_date'] = $row[csf('delivery_date')];
			$delivery_arr[$row[csf('order_id')]]['job_no'] = $row[csf('job_no')];
			$delivery_arr[$row[csf('order_id')]]['buyer_po_id'] = $row[csf('buyer_po_id')];
			$delivery_arr[$row[csf('order_id')]][$row[csf('month')]]['delivery_qty'] = $row[csf('delivery_qty')];
			$delivery_arr[$row[csf('order_id')]]['subcon_ord_id'] = $row[csf('subcon_ord_id')];
			$delivery_arr[$row[csf('order_id')]]['order_no'] = $row[csf('order_no')];
			$delivery_arr[$row[csf('order_id')]]['buyer_po_no'] = $row[csf('buyer_po_no')];
			$delivery_arr[$row[csf('order_id')]]['buyer_style_ref'] = $row[csf('buyer_style_ref')];
			$delivery_arr[$row[csf('order_id')]]['total_delivery_qty'] = $row[csf('delivery_qty')];
		}		
	}
	unset($delivery_result);

	/*$sql = "select a.id, a.booking_no, d.grouping, b.wo_qnty, b.rate, a.booking_date, a.job_no, b.po_break_down_id, b.gmt_item, c.emb_name, a.supplier_id, a.is_approved, a.ready_to_approved
    from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d
   	where a.company_id = '9' and a.delivery_date between '01-jan-2020' and '31-dec-2020' and d.job_no_mst = b.job_no and d.id = b.po_break_down_id and b.job_no = c.job_no and a.booking_no = b.booking_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.booking_type = 6 and a.entry_form = 201 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";*/

	$sql = "select a.id, a.booking_no, d.grouping, b.wo_qnty, b.rate, b.amount, a.booking_date, a.job_no, b.po_break_down_id, b.gmt_item, c.emb_name, a.supplier_id, a.is_approved, a.ready_to_approved
    from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d
   	where a.company_id = $cbo_company_id and d.job_no_mst = b.job_no and d.id = b.po_break_down_id and b.job_no = c.job_no and a.booking_no = b.booking_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.booking_type = 6 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";

   	// echo $sql;

   	$general_result = sql_select($sql);

   	$general_arr = array();
   	foreach ($general_result as $row) {
   		$general_arr[$row[csf('id')]]['id'] = $row[csf('id')];
   		$general_arr[$row[csf('id')]]['booking_no'] = $row[csf('booking_no')];
   		$general_arr[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
   		$general_arr[$row[csf('id')]]['wo_qnty'] = $row[csf('wo_qnty')];
   		$general_arr[$row[csf('id')]]['rate'] = $row[csf('rate')];
   		$general_arr[$row[csf('id')]]['amount'] = $row[csf('amount')];
   		$general_arr[$row[csf('id')]]['booking_date'] = $row[csf('booking_date')];
   		$general_arr[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
   		$general_arr[$row[csf('id')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
   	}
   	unset($general_result);

   	/*$sql = "(select a.id, a.embellishment_job, a.order_id, a.order_no, TO_CHAR(g.bill_date, 'Mon') AS month, a.currency_id, b.id as po_id, b.buyer_po_id, c.id as breakdown_id, c.qnty, c.rate as colorSizeRate, d.id as delvID, d.delivery_date, e.id as delivery_id,
        e.delivery_qty, f.id as upid, f.rate, f.amount, f.domestic_amount
   		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f, subcon_inbound_bill_mst g
  		where a.entry_form = 204 and a.embellishment_job = b.job_no_mst and b.job_no_mst = c.job_no_mst and a.id = b.mst_id and d.id = e.mst_id and c.id = e.color_size_id and e.id = f.delivery_id and f.process_id = 13 and f.entry_form = 395 and c.qnty > 0 and d.entry_form = 254 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 and f.status_active = 1 and f.is_deleted = 0 and b.id = c.mst_id and f.mst_id = g.id)
		union all (select a.id, a.embellishment_job, a.order_id, a.order_no, null AS month, a.currency_id, b.id as po_id, b.buyer_po_id, c.id as breakdown_id, c.qnty, c.rate as colorSizeRate, d.id as delvID, d.delivery_date, e.id as delivery_id, e.delivery_qty, 0 as upid, 0 as rate, 0 as amount, 0 as domestic_amount
   		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
  		where a.entry_form = 204 and a.embellishment_job = b.job_no_mst and b.job_no_mst = c.job_no_mst and a.id = b.mst_id and d.id = e.mst_id and c.id = e.color_size_id and e.bill_status != 1 and d.entry_form = 254 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and c.qnty > 0 and b.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 and b.id = c.mst_id)";*/

  	$sql = "(SELECT a.id, a.embellishment_job, a.order_id, a.order_no, TO_CHAR (g.bill_date, 'Mon') AS month, a.currency_id, b.id  AS po_id, b.buyer_po_id, c.id AS breakdown_id, c.qnty, c.rate AS colorSizeRate, d.id AS delvID, d.delivery_date, d.delivery_no, e.id AS delivery_id, e.delivery_qty, f.id AS upid, f.rate, f.amount, f.domestic_amount
   		FROM subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f, subcon_inbound_bill_mst g
  		WHERE $conditions $buyer_cond_bill and a.entry_form = 204 AND a.embellishment_job = b.job_no_mst AND b.job_no_mst = c.job_no_mst AND a.id = b.mst_id AND d.id = e.mst_id AND c.id = e.color_size_id AND e.id = f.delivery_id AND f.process_id = 13 AND f.entry_form = 395
        AND c.qnty > 0 AND d.entry_form = 254 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0
        AND f.status_active = 1 AND f.is_deleted = 0 AND b.id = c.mst_id AND f.mst_id = g.id)
		UNION ALL (SELECT a.id, a.embellishment_job, a.order_id, a.order_no, NULL AS month, a.currency_id, b.id AS po_id, b.buyer_po_id, c.id AS breakdown_id, c.qnty, c.rate AS colorSizeRate, d.id AS delvID, d.delivery_date, d.delivery_no,
        e.id AS delivery_id, e.delivery_qty, 0 AS upid, 0 AS rate, 0 AS amount, 0 AS domestic_amount
   		FROM subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
  		WHERE a.entry_form = 204 AND a.embellishment_job = b.job_no_mst and b.job_no_mst = c.job_no_mst AND a.id = b.mst_id AND d.id = e.mst_id AND c.id = e.color_size_id AND e.bill_status != 1 AND d.entry_form = 254 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND c.qnty > 0 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 AND b.id = c.mst_id)";

   	// echo $sql;
   	$bill_result = sql_select($sql);

   	$bill_arr = array();
   	foreach ($bill_result as $row) {
   		if (isset($bill_arr[$row[csf('order_id')]])) {
			if(isset($bill_arr[$row[csf('order_id')]][$row[csf('month')]])) {
				$bill_arr[$row[csf('order_id')]][$row[csf('month')]]['bill_qty'] += $row[csf('delivery_qty')];
			} else {
				$bill_arr[$row[csf('order_id')]][$row[csf('month')]]['bill_qty'] = $row[csf('delivery_qty')];
			}

			$bill_arr[$row[csf('order_id')]]['total_bill_qty'] += $row[csf('delivery_qty')];
		} else {
	   		$bill_arr[$row[csf('order_id')]]['order_id'] = $row[csf('order_id')];
	   		$bill_arr[$row[csf('order_id')]][$row[csf('month')]]['bill_qty'] = $row[csf('delivery_qty')];
	   		$bill_arr[$row[csf('order_id')]]['embellishment_job'] = $row[csf('embellishment_job')];
	   		$bill_arr[$row[csf('order_id')]]['delivery_no'] = $row[csf('delivery_no')];
	   		$bill_arr[$row[csf('order_id')]]['order_no'] = $row[csf('order_no')];
	   		$bill_arr[$row[csf('order_id')]]['delivery_date'] = $row[csf('delivery_date')];
	   		$bill_arr[$row[csf('order_id')]]['currency_id'] = $row[csf('currency_id')];
	   		$bill_arr[$row[csf('order_id')]]['po_id'] = $row[csf('po_id')];
	   		$bill_arr[$row[csf('order_id')]]['buyer_po_id'] = $row[csf('buyer_po_id')];
	   		$bill_arr[$row[csf('order_id')]]['breakdown_id'] = $row[csf('breakdown_id')];
	   		$bill_arr[$row[csf('order_id')]]['qnty'] = $row[csf('qnty')];
	   		$bill_arr[$row[csf('order_id')]]['colorSizeRate'] = $row[csf('colorSizeRate')];
	   		$bill_arr[$row[csf('order_id')]]['delvID'] = $row[csf('delvID')];
	   		$bill_arr[$row[csf('order_id')]]['delivery_id'] = $row[csf('delivery_id')];
	   		$bill_arr[$row[csf('order_id')]]['total_bill_qty'] = $row[csf('delivery_qty')];
	   		$bill_arr[$row[csf('order_id')]]['upid'] = $row[csf('upid')];
	   		$bill_arr[$row[csf('order_id')]]['rate'] = $row[csf('rate')];
	   		$bill_arr[$row[csf('order_id')]]['amount'] = $row[csf('amount')];
	   		$bill_arr[$row[csf('order_id')]]['domestic_amount'] = $row[csf('domestic_amount')];
	   	}
   	}

   	$startDate = @strtotime($txt_date_from);
	$endDate = @strtotime($txt_date_to);

	function generate_month( $start, $end ){
		$current = $start;
		$ret = array();

		while( $current<$end ){
			$next = @date('Y-M-d', $current) . "+1 month";			
			$ret[] = date('M',$current);
			$current = @strtotime($next);
		}

		return $ret;
	}

	$months_arr = generate_month($startDate, $endDate);
	$delivMonthTotal = array();
	$billMonthTotal = array();

   	if($cbo_based_on==1) {
   		foreach ($delivery_arr as $row) {
			$result[$row['order_id']]['order_id'] = $row['order_id'];
			$result[$row['order_id']]['delivery_no'] = $row['delivery_no'];
			$result[$row['order_id']]['delivery_date'] = $row['delivery_date'];
			$result[$row['order_id']]['job_no'] = $row['job_no'];
			$result[$row['order_id']]['buyer_po_id'] = $row['buyer_po_id'];
			$result[$row['order_id']]['subcon_ord_id'] = $row['subcon_ord_id'];
			$result[$row['order_id']]['order_no'] = $row['order_no'];
			$result[$row['order_id']]['buyer_po_no'] = $row['buyer_po_no'];
			$result[$row['order_id']]['buyer_style_ref'] = $row['buyer_style_ref'];
			$result[$row['order_id']]['total_delivery_qty'] = $row[csf('delivery_qty')];

			foreach ($months_arr as $month) {
				$result[$row['order_id']][$month]['delivery_qty'] = $row[$month]['delivery_qty'];
			}
   		}
   	} else {
   		foreach ($bill_arr as $row) {
			$result[$row['order_id']]['order_id'] = $row['order_id'];
			$result[$row['order_id']]['delivery_no'] = $row['delivery_no'];
			$result[$row['order_id']]['delivery_date'] = $row['delivery_date'];
			$result[$row['order_id']]['job_no'] = $row['embellishment_job'];
			$result[$row['order_id']]['buyer_po_id'] = $row['buyer_po_id'];
			$result[$row['order_id']]['subcon_ord_id'] = $row['id'];
			$result[$row['order_id']]['order_no'] = $row['order_no'];
			// $result[$row['order_id']]['buyer_po_no'] = $row['buyer_po_no'];
			$result[$row['order_id']]['buyer_style_ref'] = $row['buyer_style_ref'];
			$result[$row['order_id']]['total_delivery_qty'] = $row[csf('delivery_qty')];

			foreach ($months_arr as $month) {
				$result[$row['order_id']][$month]['delivery_qty'] = $row[$month]['delivery_qty'];
			}
   		}
   	}

   	ob_start();
   	?>
   	<style>
   		#rpt_table tr td {
		    padding: 10px;
		    word-wrap: break-word;
		}
   	</style>
   	<div class="report-inner-area">
   		<h4 style="text-align: center;">Company Name: <?php echo $company_arr[$cbo_company_id]; ?></h4>
    	<h3 style="text-align: center;">Wo Wise Bill Status Report</h3>
        <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" >
            <thead class="form_table_header">
            	<tr>
	                <th rowspan="2" style="width: 8%;">Work.O.No</th>
	                <th rowspan="2" style="width: 6%;">Buyer Style No</th>
	                <th rowspan="2" style="width: 5%;">WO Qty</th>
	                <th rowspan="2" style="width: 4%;">Avg Rate</th>
	                <th colspan="<?php echo count($months_arr); ?>" style="width:40%">Delivery Month</th>
	                <th rowspan="2" style="width: 5%;">Total Delivery</th>
	                <th rowspan="2" style="width: 5%;">Delivery Balance</th>
	                <th colspan="<?php echo count($months_arr); ?>" style="width:40%">Bill Month</th>
	                <th rowspan="2" style="width: 5%;">Total Bill</th>
	                <th rowspan="2" style="width: 5%;">Bill Balance</th>
	                <th rowspan="2" style="width: 5%;">Bill Rate $</th>
	                <th rowspan="2" style="width: 5%;">Value $</th>
                </tr>
                <tr>
                	<?php 
                		foreach ($months_arr as $month) {
                	?>
                		<th><?php echo $month; ?></th>
                	<?php
                		}
                	?>
                	<?php 
                		foreach ($months_arr as $month) {
                	?>
                		<th><?php echo $month; ?></th>
                	<?php
                		}
                	?>
                </tr>
            </thead>
            <tbody id="report_container">
				<?php
					$totalAvgRate = 0;
					foreach ($delivery_arr as $row) {
						$deliveryBalance = number_format($general_arr[$row['order_id']]['wo_qnty'] - $row['total_delivery_qty'], 0);
						$billBalance = number_format($general_arr[$row['order_id']]['wo_qnty'] - $row['total_delivery_qty'], 0);
						$woQty = number_format($general_arr[$row['order_id']]['wo_qnty'], 0);
						$rate = $general_arr[$row['order_id']]['rate'] ? $general_arr[$row['order_id']]['rate'] : 0;
						$value = ($rate * $general_arr[$row['order_id']]['amount']);
						$totalDeliveryQty = $row['total_delivery_qty'];
						$totalBillQty = $bill_arr[$row['order_id']]['total_bill_qty'] ? $bill_arr[$row['order_id']]['total_bill_qty'] : 0;
						$totalWoQty += $woQty;
						$totalAvgRate += $rate;
						$grossDeliveryTotal += $totalDeliveryQty;
						$totalDeliveryBalance += $deliveryBalance;
						$grossBillTotal += $totalBillQty;
						$totalBillBalance += $billBalance;
						$totalRate += $rate;
						$totalValue += $value;
						?>
							<tr>
			            		<td><?php echo $row['order_no']; ?></td>
			            		<td><?php echo $row['buyer_style_ref']; ?></td>
			            		<td><?php echo $woQty; ?></td>
			            		<td><?php echo $rate; ?></td>
			            		<?php 
				                		foreach ($months_arr as $month) {
				                			$delivQty = $row[$month]['delivery_qty'] ? $row[$month]['delivery_qty'] : 0;
				                			if(isset($delivMonthTotal[$month])) {
				                				$delivMonthTotal[$month] += $delivQty;
				                			} else {
				                				$delivMonthTotal[$month] = $delivQty;
				                			}
				                	?>
				                		<td><?php echo  number_format($delivQty,2); ?></td>
				                	<?php
				                		}
			                	?>
			                	<td><?php echo number_format($totalDeliveryQty,2); ?></td>
			                	<td><?php echo $deliveryBalance; ?></td>
			                	<?php
				                		foreach ($months_arr as $month) {
				                			$billQty = $bill_arr[$row['order_id']][$month]['bill_qty'] ? $bill_arr[$row['order_id']][$month]['bill_qty'] : 0;
				                			if(isset($billMonthTotal[$month])) {
				                				$billMonthTotal[$month] += $billQty;
				                			} else {
				                				$billMonthTotal[$month] = $billQty;
				                			}
				                	?>
				                		<td><?php echo $billQty; ?></td>
				                	<?php
				                		}
			                	?>
			                	<td><?php echo $totalBillQty; ?></td>
			                	<td><?php echo $billBalance; ?></td>
			                	<td><?php echo $rate ? $rate : 0; ?></td>
			                	<td><?php echo number_format($value, 0 ); ?></td>
			            	</tr>
						<?
					}
				?>
            </tbody>
            <tfoot>
            	<tr>
            		<th colspan="3">Total:</th>
            		<th><?php echo number_format($totalAvgRate,2); ?></th>
            		<?php
            			foreach ($delivMonthTotal as $delivTotal) {
            				?>
            				<th><?php echo number_format($delivTotal,2); ?></th>
            		<?php
            			}
            		?>
            		<th><?php echo number_format($grossDeliveryTotal,2); ?></th>
            		<th><?php echo $totalWoQty - $totalDeliveryBalance; ?></th>
            		<?php
            			foreach ($billMonthTotal as $billTotal) {
            				?>
            				<th><?php echo number_format($billTotal,2); ?></th>
            		<?php
            			}
            		?>
            		<th><?php echo number_format($grossBillTotal,2); ?></th>
            		<th><?php echo number_format($totalBillBalance,2); ?></th>
            		<th><?php echo $totalRate; ?></th>
            		<th><?php echo number_format($totalValue, 0); ?></th>
            	</tr>
            </tfoot>
        </table>
   	</div>   	
<?php
	ob_end_flush();
	exit();


}

if($action == 'report_generate_2')
 {
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array('select id, company_name from lib_company', 'id', 'company_name');
	$buyer_library=return_library_array( "SELECT id,buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name"  ); 

	$cbo_company_id = str_replace("'", '', $cbo_company_id);
	$cbo_party_id = str_replace("'", '', $cbo_party_id);
	$cbo_buyer_id = str_replace("'", '', $cbo_buyer_id);
	$cbo_based_on = str_replace("'", '', $cbo_based_on);
	$txt_wo_no = str_replace("'", '', $txt_wo_no);
	$txt_job_no = str_replace("'", '', $txt_job_no);
	$txt_buyer_po = str_replace("'", '', $txt_buyer_po);
	$txt_buyer_style = str_replace("'", '', $txt_buyer_style);

	$txt_date_from = str_replace("'", "", $txt_date_from);
	$txt_date_to = str_replace("'", "", $txt_date_to);

	$result = array();

	$conditions = '';

	$conditions .= "a.company_id=$cbo_company_id ";
	if ($cbo_within_group != '') {
		$conditions .= " and a.within_group=$cbo_within_group ";
	}

	if ($cbo_party_id != 0) {
		$conditions .= " and a.party_id=$cbo_party_id ";
	}

	if ($txt_wo_no != '') {
		$txt_wo_no = str_replace("'", '', $txt_wo_no);
		$txt_wo_no = "'%".$txt_wo_no."%'";
		$conditions .= " and c.order_no like $txt_wo_no ";
	}

	if ($txt_job_no != '') {
		$txt_job_no = str_replace("'", '', $txt_job_no);
		$txt_job_no = "'%".$txt_job_no."%'";
		$conditions .= " and a.job_no like $txt_job_no ";
	}

	if ($cbo_buyer_id != 0) {
		$buyer_cond_deliv =  " and d.buyer_buyer='$cbo_buyer_id' ";
		$buyer_cond_bill =  " and b.buyer_buyer='$cbo_buyer_id' ";
	}

	if ($txt_buyer_po != '') {
		$txt_buyer_po = str_replace("'", '', $txt_buyer_po);
		$txt_buyer_po = "'%".$txt_buyer_po."%'";
		$conditions .= " and d.buyer_po_no like $txt_buyer_po ";
	}
	
	if ($txt_buyer_style != '') {
		$txt_buyer_style = str_replace("'", '', $txt_buyer_style);
		$txt_buyer_style = "'%".$txt_buyer_style."%'";
		$conditions .= " and d.buyer_style_ref like $txt_buyer_style ";
	}

	if($db_type==0) {
		// $conditions.=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		if ($txt_date_from!="" &&  $txt_date_to!="") $conditions .= "and a.delivery_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'"; else $conditions .="";
	}
	if($db_type==2) {
		// $conditions.=" and to_char(a.insert_date,'YYYY')=$data[4]";
		if ($txt_date_from!="" &&  $txt_date_to!="") $conditions .= "and a.delivery_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-",1)."'"; else $conditions .="";
	}

 	$sql = "select distinct a.id, a.delivery_no, to_char(a.delivery_date, 'Mon') as month, a.entry_form, a.job_no, a.party_id, a.within_group, b.buyer_po_id, b.delivery_qty, b.order_id as subcon_ord_id, c.order_no, d.buyer_po_no, d.buyer_style_ref, d.order_id, d.order_quantity,d.rate as job_details_rate, d.buyer_buyer
		from subcon_delivery_mst a, subcon_delivery_dtls b, subcon_ord_mst c, subcon_ord_dtls d
		where $conditions $buyer_cond_deliv and a.id=b.mst_id and a.entry_form=254 and a.job_no=d.job_no_mst and c.id=d.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1";

	// echo $sql;
	$delivery_result = sql_select($sql);

	$delivery_arr = array();
	foreach ($delivery_result as $row) 
	{
		if ( isset($delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]) )
		{
			if(isset($delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]][$row[csf('month')]]))
			 {
				$delivery_arr[$row[csf('order_no')]][$row[csf('month')]]['delivery_qty'] += $row[csf('delivery_qty')];
				$delivery_arr[$row[csf('order_no')]][$row[csf('month')]]['order_quantity'] += $row[csf('order_quantity')];
			} 
			else 
			{
				$delivery_arr[$row[csf('order_no')]][$row[csf('month')]]['delivery_qty'] = $row[csf('delivery_qty')];
				$delivery_arr[$row[csf('order_no')]][$row[csf('month')]]['order_quantity'] = $row[csf('order_quantity')];
			}

			$delivery_arr[$row[csf('order_no')]]['total_delivery_qty'] += $row[csf('delivery_qty')];
			$delivery_arr[$row[csf('order_no')]]['total_order_qty'] += $row[csf('order_quantity')];
			$delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['job_details_rate'] = $row[csf('job_details_rate')];
		} 
		else
		{
			$delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['order_id'] = $row[csf('order_id')];
			$delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['delivery_no'] = $row[csf('delivery_no')];
			$delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['delivery_date'] = $row[csf('delivery_date')];
			$delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['job_no'] = $row[csf('job_no')];
			$delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['buyer_po_id'] = $row[csf('buyer_po_id')];
			$delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]][$row[csf('month')]]['delivery_qty'] = $row[csf('delivery_qty')];
			$delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]][$row[csf('month')]]['order_quantity'] = $row[csf('order_quantity')];
			$delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['subcon_ord_id'] = $row[csf('subcon_ord_id')];
			$delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['order_no'] = $row[csf('order_no')];
			$delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['buyer_po_no'] = $row[csf('buyer_po_no')];
			$delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['buyer_style_ref'] = $row[csf('buyer_style_ref')];
			$delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['buyer_buyer'] = $row[csf('buyer_buyer')];
			$delivery_arr[$row[csf('order_no')]]['total_delivery_qty'] = $row[csf('delivery_qty')];
			$delivery_arr[$row[csf('order_no')]]['total_order_qty'] = $row[csf('order_quantity')];
			$delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['order_quantity'] = $row[csf('order_quantity')];
			$delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['job_details_rate'] = $row[csf('job_details_rate')];
		}		
	}
	unset($delivery_result);

	$sql = "select a.id, a.booking_no, d.grouping, b.wo_qnty, b.rate, b.amount, a.booking_date, a.job_no, b.po_break_down_id, b.gmt_item, c.emb_name, a.supplier_id, a.is_approved, a.ready_to_approved, d.po_number
    from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d
   	where a.company_id = $cbo_company_id and d.job_no_mst = b.job_no and d.id = b.po_break_down_id and b.job_no = c.job_no and a.booking_no = b.booking_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.booking_type = 6 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";

   	// echo $sql;

   	$general_result = sql_select($sql);

   	$general_arr = array();
   	foreach ($general_result as $row) {
   		$general_arr[$row[csf('booking_no')]][$row[csf('po_number')]]['id'] = $row[csf('id')];
   		$general_arr[$row[csf('booking_no')]][$row[csf('po_number')]]['booking_no'] = $row[csf('booking_no')];
   		$general_arr[$row[csf('booking_no')]][$row[csf('po_number')]]['grouping'] = $row[csf('grouping')];
   		$general_arr[$row[csf('booking_no')]][$row[csf('po_number')]]['wo_qnty'] = $row[csf('wo_qnty')];
   		$general_arr[$row[csf('booking_no')]][$row[csf('po_number')]]['rate'] = $row[csf('rate')];
   		$general_arr[$row[csf('booking_no')]][$row[csf('po_number')]]['amount'] = $row[csf('amount')];
   		$general_arr[$row[csf('booking_no')]][$row[csf('po_number')]]['booking_date'] = $row[csf('booking_date')];
   		$general_arr[$row[csf('booking_no')]][$row[csf('po_number')]]['job_no'] = $row[csf('job_no')];
   		$general_arr[$row[csf('booking_no')]][$row[csf('po_number')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
   		$general_arr[$row[csf('booking_no')]][$row[csf('po_number')]]['po_number'] = $row[csf('po_number')];
   	}
   	unset($general_result);


  	$sql = "(SELECT a.id, a.embellishment_job, a.order_id, a.order_no, TO_CHAR (g.bill_date, 'Mon') AS month, a.currency_id, b.id  AS po_id, b.buyer_po_id, c.id AS breakdown_id, c.qnty, c.rate AS colorSizeRate, d.id AS delvID, d.delivery_date, d.delivery_no, e.id AS delivery_id, e.delivery_qty, f.id AS upid, f.rate, f.amount, f.domestic_amount, b.order_quantity, b.buyer_po_no
   		FROM subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e, subcon_inbound_bill_dtls f, subcon_inbound_bill_mst g
  		WHERE $conditions $buyer_cond_bill and a.entry_form = 204 AND a.embellishment_job = b.job_no_mst AND b.job_no_mst = c.job_no_mst AND a.id = b.mst_id AND d.id = e.mst_id AND c.id = e.color_size_id AND e.id = f.delivery_id AND f.process_id = 13 AND f.entry_form = 395
        AND c.qnty > 0 AND d.entry_form = 254 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0
        AND f.status_active = 1 AND f.is_deleted = 0 AND b.id = c.mst_id AND f.mst_id = g.id)
		UNION ALL (SELECT a.id, a.embellishment_job, a.order_id, a.order_no, NULL AS month, a.currency_id, b.id AS po_id, b.buyer_po_id, c.id AS breakdown_id, c.qnty, c.rate AS colorSizeRate, d.id AS delvID, d.delivery_date, d.delivery_no,
        e.id AS delivery_id, e.delivery_qty, 0 AS upid, 0 AS rate, 0 AS amount, 0 AS domestic_amount, b.order_quantity, b.buyer_po_no
   		FROM subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_delivery_mst d, subcon_delivery_dtls e
  		WHERE a.entry_form = 204 AND a.embellishment_job = b.job_no_mst and b.job_no_mst = c.job_no_mst AND a.id = b.mst_id AND d.id = e.mst_id AND c.id = e.color_size_id AND e.bill_status != 1 AND d.entry_form = 254 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND c.qnty > 0 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 AND b.id = c.mst_id)";

 //echo $sql;
   	$bill_result = sql_select($sql);

   	$bill_arr = array();
   	foreach ($bill_result as $row) 
	{
   		if ( isset($bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]) )
		 {
			/*if(isset($bill_arr[$row[csf('order_no')]][$row[csf('month')]])) {
				$bill_arr[$row[csf('order_no')]][$row[csf('month')]]['bill_qty'] += $row[csf('delivery_qty')];
				$bill_arr[$row[csf('order_no')]][$row[csf('month')]]['order_quantity'] += $row[csf('order_quantity')];
			} else {
				$bill_arr[$row[csf('order_no')]][$row[csf('month')]]['bill_qty'] = $row[csf('delivery_qty')];
				$bill_arr[$row[csf('order_no')]][$row[csf('month')]]['order_quantity'] = $row[csf('order_quantity')];
			}*/

			/*$bill_arr[$row[csf('order_no')]]['total_bill_qty'] += $row[csf('delivery_qty')];
			$bill_arr[$row[csf('order_no')]][$row[csf('order_no')]]['total_order_qty'] += $row[csf('order_quantity')];*/
			// $bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['total_bill_qty'] += $row[csf('delivery_qty')];
			$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['total_order_qty'] += $row[csf('order_quantity')];
		} 
		else 
		{
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['order_id'] = $row[csf('order_id')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['buyer_po_no'] = $row[csf('buyer_po_no')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]][$row[csf('month')]]['bill_qty'] = $row[csf('delivery_qty')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['embellishment_job'] = $row[csf('embellishment_job')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['delivery_no'] = $row[csf('delivery_no')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['order_no'] = $row[csf('order_no')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['delivery_date'] = $row[csf('delivery_date')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['currency_id'] = $row[csf('currency_id')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['po_id'] = $row[csf('po_id')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['buyer_po_id'] = $row[csf('buyer_po_id')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['breakdown_id'] = $row[csf('breakdown_id')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['qnty'] = $row[csf('qnty')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['colorSizeRate'] = $row[csf('colorSizeRate')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['delvID'] = $row[csf('delvID')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['delivery_id'] = $row[csf('delivery_id')];
	   		/*$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['total_bill_qty'] = $row[csf('delivery_qty')];*/
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['upid'] = $row[csf('upid')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['rate'] = $row[csf('rate')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['amount'] = $row[csf('amount')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['domestic_amount'] = $row[csf('domestic_amount')];
	   		$bill_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['order_quantity'] = $row[csf('order_quantity')];
	   		// $bill_arr[$row[csf('order_no')]][$row[csf('month')]]['order_quantity'] = $row[csf('order_quantity')];
	   	}
   	}

   	/*echo '<pre>';
   	print_r($bill_arr);
   	echo '</pre>';*/

   	$startDate = @strtotime($txt_date_from);
	$endDate = @strtotime($txt_date_to);

	function generate_month( $start, $end ){
		$current = $start;
		$ret = array();

		while( $current<$end ){
			$next = @date('Y-M-d', $current) . "+1 month";			
			$ret[] = date('M',$current);
			$current = @strtotime($next);
		}

		return $ret;
	}

	$months_arr = generate_month($startDate, $endDate);
	$delivMonthsStr = "deliv".implode(",deliv", $months_arr)."";
	$billMonthsStr = "bill".implode(",bill", $months_arr)."";

	// echo $delivMonthsStr;
   	if($cbo_based_on==1)
	 {
   		foreach ($delivery_arr as $order_no => $buyer_po_no) 
		{
   			foreach ($buyer_po_no as $row) 
			{
   				// $delivery_arr[$row[csf('order_no')]][$row[csf('buyer_po_no')]]['order_id'] = $row[csf('order_id')];
   				if($row['order_no'] != '')
				 {
   					$result[$row['order_no']][$row['buyer_po_no']]['order_id'] = $row['order_id'];
					$result[$row['order_no']][$row['buyer_po_no']]['delivery_no'] = $row['delivery_no'];
					$result[$row['order_no']][$row['buyer_po_no']]['delivery_date'] = $row['delivery_date'];
					$result[$row['order_no']][$row['buyer_po_no']]['job_no'] = $row['job_no'];
					$result[$row['order_no']][$row['buyer_po_no']]['buyer_po_id'] = $row['buyer_po_id'];
					$result[$row['order_no']][$row['buyer_po_no']]['subcon_ord_id'] = $row['subcon_ord_id'];
					$result[$row['order_no']][$row['buyer_po_no']]['order_no'] = $row['order_no'];
					$result[$row['order_no']][$row['buyer_po_no']]['buyer_po_no'] = $row['buyer_po_no'];
					$result[$row['order_no']][$row['buyer_po_no']]['buyer_style_ref'] = $row['buyer_style_ref'];
					$result[$row['order_no']][$row['buyer_po_no']]['buyer_buyer'] = $row['buyer_buyer'];
					$result[$row['order_no']][$row['buyer_po_no']]['order_quantity'] = $row['order_quantity'];
					$result[$row['order_no']][$row['buyer_po_no']]['job_details_rate'] = $row['job_details_rate'];
					// $result[$row['order_no']][$row['buyer_po_no']]['total_order_qty'] = $row['total_order_qty'];

					foreach ($months_arr as $month) 
					{
						$result[$row['order_no']][$row['buyer_po_no']]['total_delivery_qty'] += $row[$month]['delivery_qty'];
						$result[$row['order_no']][$row['buyer_po_no']]['total_order_qty'] += $row[$month]['order_quantity'];
						$result[$row['order_no']][$row['buyer_po_no']][$month]['delivery_qty'] = $row[$month]['delivery_qty'];
					}
   				}
   			}
   		}
   	} 
	else 
	{
   		 foreach ($bill_arr as $order_no => $order_id)
		 {
   			foreach ($order_id as $row) 
			{
   				if($order_no != '') 
				{
	   				$result[$order_no][$row['buyer_po_no']]['order_id'] = $row['order_id'];
	   				$result[$order_no][$row['buyer_po_no']]['job_no'] = $row['embellishment_job'];
	   				$result[$order_no][$row['buyer_po_no']]['delivery_no'] = $row['delivery_no'];
	   				$result[$order_no][$row['buyer_po_no']]['order_no'] = $row['order_no'];
	   				$result[$order_no][$row['buyer_po_no']]['delivery_date'] = $row['delivery_date'];
	   				$result[$order_no][$row['buyer_po_no']]['currency_id'] = $row['currency_id'];
	   				$result[$order_no][$row['buyer_po_no']]['subcon_ord_id'] = $row['po_id'];
	   				$result[$order_no][$row['buyer_po_no']]['buyer_po_no'] = $row['buyer_po_no'];
	   				$result[$order_no][$row['buyer_po_no']]['buyer_po_id'] = $row['buyer_po_id'];
	   				$result[$order_no][$row['buyer_po_no']]['breakdown_id'] = $row['breakdown_id'];
	   				$result[$order_no][$row['buyer_po_no']]['qnty'] = $row['qnty'];
	   				$result[$order_no][$row['buyer_po_no']]['colorSizeRate'] = $row['colorSizeRate'];
	   				$result[$order_no][$row['buyer_po_no']]['delvID'] = $row['delvID'];
	   				$result[$order_no][$row['buyer_po_no']]['delivery_id'] = $row['delivery_id'];
	   				$result[$order_no][$row['buyer_po_no']]['total_bill_qty'] = $row['total_bill_qty'];
	   				$result[$order_no][$row['buyer_po_no']]['upid'] = $row['upid'];
	   				$result[$order_no][$row['buyer_po_no']]['rate'] = $row['rate'];
	   				$result[$order_no][$row['buyer_po_no']]['amount'] = $row['amount'];
	   				$result[$order_no][$row['buyer_po_no']]['domestic_amount'] = $row['domestic_amount'];
	   				/*$result[$row['order_no']][$row['buyer_po_no']]['total_order_qty'] = $row['total_order_qty'];
	   				$result[$row['order_no']][$row['buyer_po_no']]['order_quantity'] = $row['order_quantity'];*/
	   				$result[$order_no][$row['buyer_po_no']]['total_order_qty'] += $row['order_quantity'];
	   				$result[$order_no][$row['buyer_po_no']]['order_quantity'] = $row['order_quantity'];
					$result[$order_no][$row['buyer_po_no']]['job_details_rate'] = $row['job_details_rate'];
					 

	   				foreach ($months_arr as $month) 
					{
						// $result[$row['order_no']][$row['buyer_po_no']][$month]['delivery_qty'] = $row[$month]['qnty'];
						// $result[$order_no][$row['buyer_po_no']][$month]['delivery_qty'] = $row[$month]['qnty'];
						$result[$order_no][$row['buyer_po_no']][$month]['bill_qnty'] = $row[$month]['bill_qty'];

						$result[$order_no][$row['buyer_po_no']]['total_delivery_qty'] += $delivery_arr[$row['order_no']][$row['buyer_po_no']]['total_delivery_qty'];
						$result[$order_no][$row['buyer_po_no']][$month]['delivery_qty'] = $delivery_arr[$row['order_no']][$row['buyer_po_no']][$month]['delivery_qty'];
					}
	   			}
	   		}
   		}
   	}


//echo "<pre>";
//print_r($result); die;



   	$row_woNoArray=$row_jobNoArray=$row_buyerStyleArray=array();

   	foreach ($result as $woNo => $jobNo) 
	{
   		$woNo_rowspan=0;
   		$jobNo_rowspan=0;
   		$byerStyle_rowspan=0;
   		foreach ($jobNo as $buyerStyleNo => $woQty) 
		{
   			$woNo_rowspan++;
   			$jobNo_rowspan++;
   			$byerStyle_rowspan++;
  			$total_wo_qty_Array[$woNo] +=$woQty['total_order_qty'];
    	}
   		$row_woNoArray[$woNo] = $woNo_rowspan;
   		$row_jobNoArray[$woNo] = $jobNo_rowspan;
   		$row_buyerStyleArray[$woNo] = $byerStyle_rowspan;
   	}
	
	
//echo "<pre>";
//print_r($total_wo_qty_Array); die;

   	$tbl_width = 1800+(150*count($months_arr));

   	ob_start();
   	?>
   	<style>
   		#table_body tr td {
		    padding: 10px;
		    word-wrap: break-word;
		    text-align: center;
		}
		#table_body tr:first-child td {
		  padding: 0;
		}
		tr.wo-total td {
			padding: 0 !important;
			text-align: center;
		}
   	</style>
	<h4 style="text-align: center;">Company Name: <?php echo $company_arr[$cbo_company_id]; ?></h4>
	<h3 style="text-align: center;">Wo Wise Bill Status Report</h3>
	<div style="width: <?php echo $tbl_width;?>px;">
		<table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" style="table-layout: fixed; width: 99%;">
		    <thead class="form_table_header">
		        <tr>
		        	<th rowspan="2">Work.O.No</th>
		        	<th rowspan="2">Buyer</th>
		            <th rowspan="2">Job No</th>
		            <th rowspan="2">Buyer Style No</th>
		            <th rowspan="2">Wo Qty</th>
		            <th rowspan="2">Buyer PO No</th>
		            <th rowspan="2">PO Wise WO Qty</th>
		            <th rowspan="2">Avg Rate</th>
		            <th colspan="<?php echo count($months_arr); ?>">Delivery Month</th>
		            <th rowspan="2">Total Delivery</th>
	                <th rowspan="2">Delivery Value</th>
		            <th rowspan="2">Delivery Balance</th>
		            <th colspan="<?php echo count($months_arr); ?>">Bill Month</th>
		            <th rowspan="2">Total Bill</th>
		            <th rowspan="2">Bill Balance</th>
		            <th rowspan="2">Bill Rate $</th>
		            <th rowspan="2">Value $</th>
		        </tr>
		        <tr>
		        	<?php 
		        		foreach ($months_arr as $month) {
		        	?>
		        		<th><?php echo $month; ?></th>
		        	<?php
		        		}
		        	?>
		        	<?php 
		        		foreach ($months_arr as $month) {
		        	?>
		        		<th><?php echo $month; ?></th>
		        	<?php
		        		}
		        	?>
		    	</tr>
		    </thead>
		</table>
	</div>
	<div style="max-height:350px; width: <?php echo $tbl_width;?>px; overflow-y:auto;" id="scroll_body">
		<table id="table_body" cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" >
			
		<tbody>
		<?php
			$totalAvgRate = 0;
			$grandTotalWoQty = 0;
			$grandTotalAvgRate = 0;
			$grandTotalDelivery = 0;
			$grandTotalDeliveryBalance = 0;
			$grandTotalBill = 0;
			$grandTotalBillBalance = 0;
			$grandTotalBillRate = 0;
			$grandTotalValue = 0;


			$delivMonthGrandTotal = array();
			$billMonthGrandTotal = array();

			foreach ($result as $woNo => $poNoArr) 
			{
				$woNo_rowspan = 0;
				$totalBillQty = 0;
				$woTotalWoQty = 0;
				$woTotalAvgRate = 0;
				$woTotalDelivery = 0;
				$woTotalDeliveryBalance = 0;
				$woTotalBill = 0;
				$woTotalBillBalance = 0;
				$woTotalBillRate = 0;
				$woTotalValue = 0;

				$delivMonthTotal = array();
				$billMonthTotal = array();
				//$woQty=0;
				
				
				
				foreach ($poNoArr as $row) 
				{
					
					
					// $deliveryBalance = $general_arr[$row['order_id']]['wo_qnty'] - $row['total_delivery_qty'];
					$total_woQty =$total_wo_qty_Array[$woNo];//$row['total_order_qty']
					$woQty =$row['total_order_qty'];
					$cbo_within_group = str_replace("'", '', $cbo_within_group);
					
					//echo $cbo_within_group; die;
					
					if($cbo_within_group==2)
					{
						//$job_details_rate=$row['job_details_rate']; 
						$rate = $row['job_details_rate'] ? $row['job_details_rate'] : 0;
					}
					else
					{
 						$rate = $general_arr[$row['order_no']][$row['buyer_po_no']]['rate'] ? $general_arr[$row['order_no']][$row['buyer_po_no']]['rate'] : 0;
					}
					
					 
					//$rate = $general_arr[$row['order_no']][$row['buyer_po_no']]['rate'] ? $general_arr[$row['order_no']][$row['buyer_po_no']]['rate'] : 0;
					// $value = ($rate * $general_arr[$row['order_id']]['amount']);
					$totalDeliveryQty = $row['total_delivery_qty'];
					$deliveryBalance = ($woQty - $totalDeliveryQty);
					$totalBillQty = $bill_arr[$woNo][$row['buyer_po_no']]['total_bill_qty'] ? $bill_arr[$woNo][$row['buyer_po_no']]['total_bill_qty'] : 0;
					// $totalBillQty = $bill_arr[$row['order_no']][$row['buyer_po_no']]['total_bill_qty'] ? $bill_arr[$row['order_no']][$row['buyer_po_no']]['total_bill_qty'] : 0;
					
					?>
						<tr>
							<?php if($woNo_rowspan==0)
							{ ?>
			            		<td style="vertical-align: middle;" align="center" rowspan="<?php echo $row_woNoArray[$woNo]; ?>"><?php echo $row['order_no']; ?></td>
			            		<td style="vertical-align: middle;" align="center" rowspan="<?php echo $row_woNoArray[$woNo]; ?>"><?php echo $buyer_library[$row['buyer_buyer']]; ?></td>
			            		<td style="vertical-align: middle;" align="center" rowspan="<?php echo $row_woNoArray[$woNo]; ?>"><?php echo $row['job_no']; ?></td>
			            		<td style="vertical-align: middle;" align="center" rowspan="<?php echo $row_woNoArray[$woNo]; ?>"><?php echo $row['buyer_style_ref']; ?></td>
		            			<td style="vertical-align: middle;" align="center" rowspan="<?php echo $row_woNoArray[$woNo]; ?>"><?php echo number_format($total_woQty); ?></td>
			            	<?php } ?>
		            		<td><?php echo $row['buyer_po_no']; ?></td>
		            		<td title=""><?php echo number_format($row['order_quantity']); ?></td>
		            		<td><?php echo number_format($rate,2); ?></td>
		            		<?php
		                		foreach ($months_arr as $month) {
		                			$delivQty = $row[$month]['delivery_qty'] ? $row[$month]['delivery_qty'] : 0;
		                			if(isset($delivMonthTotal[$month])) {
		                				$delivMonthTotal[$month] += $delivQty;
		                			} else {
		                				$delivMonthTotal[$month] = $delivQty;
		                			}
		                			if(isset($delivMonthGrandTotal[$month])) {
		                				$delivMonthGrandTotal[$month] += $delivQty;
		                			} else {
		                				$delivMonthGrandTotal[$month] = $delivQty;
		                			}
			                ?>
			                		<td><?php echo number_format($delivQty);  //echo $delivQty; ?></td>
			                <?php
			                		}
		                	?>
		                	<td><?php echo number_format($totalDeliveryQty); ?></td>
                            <td><?php echo number_format( $totalDeliveryQty*$rate); ?></td>
		                	<td><?php echo number_format($deliveryBalance); ?></td>
		                	<?php
			                		foreach ($months_arr as $month) {
			                			$billQty = $bill_arr[$woNo][$row['buyer_po_no']][$month]['bill_qty'] ? $bill_arr[$woNo][$row['buyer_po_no']][$month]['bill_qty'] : 0;
			                			$totalBillQty += $billQty;			                			
			                			if(isset($billMonthTotal[$month])) {
			                				$billMonthTotal[$month] += $billQty;
			                			} else {
			                				$billMonthTotal[$month] = $billQty;
			                			}

			                			if(isset($billMonthGrandTotal[$month])) {
			                				$billMonthGrandTotal[$month] += $billQty;
			                			} else {
			                				$billMonthGrandTotal[$month] = $billQty;
			                			}
			                	?>
			                		<td><?php echo number_format($billQty); //$billQty; ?></td>
			                	<?php
			                		}
		                	?>
		                	<td><?php echo $totalBillQty; ?></td>
		                	<td><?php $billBalance = number_format($woQty - $totalBillQty); echo $billBalance; ?></td>
		                	<td><?php echo number_format($rate,2);  ?></td>
		                	<td><?php $value = ($totalBillQty*$rate); echo number_format($value); ?></td>
		            	</tr>
					<?php
						$woNo_rowspan++;

						$woTotalWoQty += $woQty;
						$woTotalAvgRate += $rate;
						$woTotalDelivery += $totalDeliveryQty;
						$woTotalDeliveryBalance += $deliveryBalance;
						$woTotalBill += $totalBillQty;
						$woTotalBillBalance += $billBalance;
						$woTotalValue += $value;

						$grandTotalWoQty += $woQty;
						$grandTotalAvgRate += $rate;
						$grandTotalDelivery += $totalDeliveryQty;
						$grandTotalDeliveryBalance += $deliveryBalance;
						$grandTotalBill += $totalBillQty;
						$grandTotalBillBalance += $billBalance;
						$grandTotalBillRate += $rate;
						$grandTotalValue += $value;
					}
				?>
				<tr class="wo-total" style="background: #92CDDC;">
	        		<td colspan="6" style="text-align: right;"><b>Total:</b></td>
	        		<td id="tot_wo_qty"><b><?php echo number_format($woTotalWoQty); ?></b></td>
	        		<td id="tot_avg_rate"><b><?php echo number_format($woTotalAvgRate);  //$woTotalAvgRate; ?></b></td>
	        		<?php
	        			foreach ($delivMonthTotal as $month => $delivTotal) {
	        				?>
	        				<td id="<?php echo "deliv$month"; ?>"><b><?php echo number_format($delivTotal);    ?></b></td>
	        		<?php
	        			}
	        		?>
	        		<td><b><?php echo number_format($woTotalDelivery);   ?></b></td>
                    <td></td>
	        		<td><b><?php echo number_format($woTotalDeliveryBalance); ?></b></td>
	        		<?php
	        			foreach ($billMonthTotal as $month => $billTotal) {
	        				?>
	        				<td id="<?php echo "bill$month"; ?>"><b><?php echo number_format($billTotal);  ?></b></td>
	        		<?php
	        			}
	        		?>
	        		<td><b><?php echo number_format($woTotalBill);  //$woTotalBill; ?></b></td>
	        		<td><b><?php echo number_format($woTotalBillBalance);  //$woTotalBillBalance; ?></b></td>
	        		<td><b><?php echo number_format($woTotalAvgRate);  ///$woTotalAvgRate; ?></b></td>
	        		<td><b><?php echo number_format($woTotalValue); ?></b></td>
	        	</tr>
				<?php
			}
		?>
		</tbody>
	
        <tfoot>
        	<tr>
        		<th colspan="6">Grand Total:</th>
        		<th id="tot_wo_qty"><?php echo number_format($grandTotalWoQty); ?></th>
        		<th id="tot_avg_rate"><?php echo $grandTotalAvgRate; ?></th>
        		<?php
        			foreach ($delivMonthGrandTotal as $month => $delivTotal) {
        				?>
        				<th id="<?php echo "deliv$month"; ?>"><?php echo $delivTotal; ?></th>
        		<?php
        			}
        		?>
        		<th><?php echo $grandTotalDelivery; ?></th>
                <th></th>
        		<th><?php echo $grandTotalDeliveryBalance; ?></th>
        		<?php
        			foreach ($billMonthGrandTotal as $month => $billTotal) {
        				?>
        				<th id="<?php echo "bill$month"; ?>"><?php echo $billTotal; ?></th>
        		<?php
        			}
        		?>
        		<th><?php echo $grandTotalBill; ?></th>
        		<th><?php echo $grandTotalBillBalance; ?></th>
        		<th><?php echo $grandTotalAvgRate; ?></th>
        		<th><?php echo $grandTotalValue; ?></th>
        	</tr>
        </tfoot>
    </table>
	</div>
    <script>
    	var delivMonthsStr = "<?php echo $delivMonthsStr; ?>";
    	// console.log('delivMonthsStr: '+delivMonthsStr);
		var billMonthsStr = "<?php echo $billMonthsStr; ?>";
		var columns = 'tot_wo_qty,'+'tot_avg_rate,'+delivMonthsStr+',tot_deliv,'+'tot_deliv_balance,'+billMonthsStr+ 'tot_bill,'+ 'tot_bill_balance,'+ 'tot_bill_rate,'+ 'tot_bill_value';
		var columnIds = columns.split(',');
		// console.log(columnIds);

    	tableFilters = {
			col_operation: {
			   id: columnIds,
			   // col: [6,7,8,9,10,11,12,13,14,15,16],
			   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		};
    </script>
<?php
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename"; 
	exit();
}