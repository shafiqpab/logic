<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class3/class.conditions.php');
require_once('../../../../includes/class3/class.reports.php');
require_once('../../../../includes/class3/class.yarns.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer"){
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="report_generate"){ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if($db_type==0){ $nvl="IFNULL"; } else{ $nvl="NVL"; }
	
	$cbo_company_name 	=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name 	=str_replace("'","",$cbo_buyer_name);
	$txt_fab_bk_no 		=str_replace("'","",$txt_fab_bk_no);
	$txt_job_no 		=str_replace("'","",$txt_job_no);
	$txt_style_ref 		=str_replace("'","",$txt_style_ref);
	$txt_ord_no 		=str_replace("'","",$txt_ord_no);
	$txt_req_no 		=str_replace("'","",$txt_req_no);
	$cbo_date_category 	=str_replace("'","",$cbo_date_category);
	$txt_date_from 		=str_replace("'","",$txt_date_from);
	$txt_date_to 		=str_replace("'","",$txt_date_to);	
	
	if($cbo_company_name==0) $comp_sql_cond=""; else $comp_sql_cond=" and a.company_id='$cbo_company_name'";
	if($cbo_buyer_name==0) $buyer_sql_cond=""; else $buyer_sql_cond="  and b.buyer_id='$cbo_buyer_name'";
	if(trim($txt_fab_bk_no)!="") $booking_sql_cond=" and b.booking_no like '%$txt_fab_bk_no%'";
	if(trim($txt_job_no)!="") $job_sql_cond=" and b.job_no like '%$txt_job_no%'";
	if(trim($txt_style_ref)!="") $style_sql_cond=" and b.style_ref_no like '%$txt_style_ref%'";
	if(trim($txt_ord_no)!="") $po_sql_con=" and c.po_number like '%$txt_ord_no%'";
	if(trim($txt_req_no)!="") $requ_sql_cond=" and a.requ_no like '%$txt_req_no%'";
	
	if($db_type==0){
		$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2){
		$date_from=change_date_format($txt_date_from,'','',1);
		$date_to=change_date_format($txt_date_to,'','',1);
	}
	else {
		$date_from="";
		$date_to="";
	}
	$date_cond_ship=""; $date_cond_req="";	
	if($cbo_date_category==1){
		if($date_from!="" && $date_to!="") $date_cond_req=" and a.requisition_date between '".$date_from."' and '".$date_to."'"; else $date_cond_req="";
	}
	else if($cbo_date_category==2){
		if($date_from!="" && $date_to!="") $date_cond_ship=" and c.shipment_date between '".$date_from."' and '".$date_to."'"; else $date_cond_ship="";
	}
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer ",'id','buyer_name');
	$count_arr=return_library_array( "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count",'id','yarn_count');
	
	$color_arr=return_library_array( "select id,color_name from lib_color where is_deleted=0 and status_active=1 order by color_name",'id','color_name');
	
	
	$sql_querry="select  a.company_id,a.requ_no,a.requisition_date,b.buyer_id,b.booking_no,b.job_no,b.style_ref_no,b.count_id,b.yarn_type_id,b.composition_id,b.com_percent,b.quantity,b.remarks,b.color_id from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b    
	where  a.id=b.mst_id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $comp_sql_cond $buyer_sql_cond $booking_sql_cond $job_sql_cond $style_sql_cond $requ_sql_cond $date_cond_ship $date_cond_req and $nvl(b.booking_no,'0')>'0'";
	
	$sql_result_multi=sql_select($sql_querry);
	$arr_multivalue=array();
	foreach($sql_result_multi as $row){
		$itemIndex=$row[csf('composition_id')]."_".$row[csf('com_percent')];
		
		$arr_multivalue[$row[csf('requ_no')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]][$itemIndex]['companyId']=$row[csf('company_id')];
		$arr_multivalue[$row[csf('requ_no')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]][$itemIndex]['requNo']=$row[csf('requ_no')];
		$arr_multivalue[$row[csf('requ_no')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]][$itemIndex]['requisitionDate']=$row[csf('requisition_date')];
		$arr_multivalue[$row[csf('requ_no')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]][$itemIndex]['bookingNo']=$row[csf('booking_no')];
		$arr_multivalue[$row[csf('requ_no')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]][$itemIndex]['job_no']=$row[csf('job_no')];
		$arr_multivalue[$row[csf('requ_no')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]][$itemIndex]['buyer_id']=$row[csf('buyer_id')];
		$arr_multivalue[$row[csf('requ_no')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]][$itemIndex]['style_ref_no']=$row[csf('style_ref_no')];
		$arr_multivalue[$row[csf('requ_no')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]][$itemIndex]['requisi_qty']+=$row[csf('quantity')];
		$arr_multivalue[$row[csf('requ_no')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]][$itemIndex]['rowFrom']="req";
		
		$arr_multivalue[$row[csf('requ_no')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]][$itemIndex]['color_id'][$row[csf('color_id')]]=$color_arr[$row[csf('color_id')]].' ['.$row[csf('quantity')].']';
		
		$arr_multivalue[$row[csf('requ_no')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]][$itemIndex]['item']=$composition[$row[csf('composition_id')]]." ".$row[csf('com_percent')];
		
		$arr_multivalue[$row[csf('requ_no')]][$row[csf('booking_no')]][$row[csf('job_no')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]][$itemIndex]['remarks']=$row[csf('remarks')];
		
	}
	
	
	
	$sql ="select m.requ_no, book.booking_no,book.job_no,book.pre_cost_fabric_cost_dtls_id,yarn.id,yarn.count_id,yarn.copm_one_id, yarn.percent_one, yarn.type_id,yarn.cons_ratio,sum(book.grey_fab_qnty) AS grey_fab_qnty,sum(((book.grey_fab_qnty*yarn.cons_ratio)/100)) AS yarn_req from wo_booking_dtls book join (select count(b.id) as tot,a.requ_no, b.booking_no from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $comp_sql_cond $buyer_sql_cond $booking_sql_cond $job_sql_cond $style_sql_cond $requ_sql_cond $date_cond_ship $date_cond_req and $nvl(b.booking_no,'0')>'0' group by a.requ_no,b.booking_no) m on book.booking_no=m.booking_no join wo_pre_cost_fab_yarn_cost_dtls yarn on yarn.job_no=book.job_no and yarn.fabric_cost_dtls_id=book.pre_cost_fabric_cost_dtls_id  where book.status_active=1 and book.is_deleted=0 group by m.requ_no,book.booking_no,book.job_no,book.pre_cost_fabric_cost_dtls_id,yarn.id,yarn.count_id,yarn.copm_one_id, yarn.percent_one, yarn.type_id,yarn.cons_ratio order by book.job_no";
	
	
	$sql_booking=sql_select($sql);
	foreach($sql_booking as $row_booking){
		$itemIndex=$row_booking[csf('copm_one_id')]."_".$row_booking[csf('percent_one')];
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('count_id')]][$row_booking[csf('type_id')]][$itemIndex]['booking_req']+=$row_booking[csf('yarn_req')];
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('count_id')]][$row_booking[csf('type_id')]][$itemIndex]['rowFrom']="book";
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('count_id')]][$row_booking[csf('type_id')]][$itemIndex]['item']=$composition[$row_booking[csf('copm_one_id')]]." ".$row_booking[csf('percent_one')];

	}
	//echo "select m.requ_no, book.booking_no,book.job_no,book.pre_cost_fabric_cost_dtls_id,yarn.id,yarn.count_id,yarn.copm_one_id, yarn.percent_one, yarn.type_id,yarn.cons_ratio,sum(book.grey_fab_qnty) AS grey_fab_qnty,sum(((book.grey_fab_qnty*yarn.cons_ratio)/100)) AS yarn_req from wo_booking_dtls book join (select count(b.id) as tot,a.requ_no, b.booking_no from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.item_category_id=1 $comp_sql_cond $buyer_sql_cond $booking_sql_cond $job_sql_cond $style_sql_cond $requ_sql_cond $date_cond_ship $date_cond_req and nvl(b.booking_no,'0')>'0' group by a.requ_no,b.booking_no) m on book.booking_no=m.booking_no join wo_pre_cost_fab_yarn_cost_dtls yarn on yarn.job_no=book.job_no and yarn.fabric_cost_dtls_id=book.pre_cost_fabric_cost_dtls_id  where book.status_active=1 and book.is_deleted=0 group by m.requ_no,book.booking_no,book.job_no,book.pre_cost_fabric_cost_dtls_id,yarn.id,yarn.count_id,yarn.copm_one_id, yarn.percent_one, yarn.type_id,yarn.cons_ratio order by book.job_no";
	//echo "select m.requ_no, book.booking_no,book.job_no,book.pre_cost_fabric_cost_dtls_id,yarn.id,yarn.count_id,yarn.copm_one_id, yarn.percent_one, yarn.type_id,yarn.cons_ratio,sum(book.grey_fab_qnty) AS grey_fab_qnty,sum(((book.grey_fab_qnty*yarn.cons_ratio)/100)) AS yarn_req,po.id,po.po_number from wo_booking_dtls book join (select count(b.id) as tot,a.requ_no, b.booking_no from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.item_category_id=1 $comp_sql_cond $buyer_sql_cond $booking_sql_cond $job_sql_cond $style_sql_cond $requ_sql_cond $date_cond_ship $date_cond_req and nvl(b.booking_no,'0')>'0' group by a.requ_no,b.booking_no) m on book.booking_no=m.booking_no join wo_pre_cost_fab_yarn_cost_dtls yarn on yarn.job_no=book.job_no and yarn.fabric_cost_dtls_id=book.pre_cost_fabric_cost_dtls_id join wo_pre_cost_fabric_cost_dtls fabric on yarn.fabric_cost_dtls_id=fabric.id join wo_pre_cos_fab_co_avg_con_dtls fabricDtls on fabric.id=fabricDtls.pre_cost_fabric_cost_dtls_id join wo_po_color_size_breakdown colorSize on fabricDtls.po_break_down_id=colorSize.po_break_down_id and fabric.item_number_id=colorSize.item_number_id and fabricDtls.color_number_id=colorSize.color_number_id and fabricDtls.gmts_sizes=colorSize.size_number_id and fabricDtls.cons>0 join wo_po_break_down po on po.id=fabricDtls.po_break_down_id    where book.status_active=1 and book.is_deleted=0 group by m.requ_no,book.booking_no,book.job_no,book.pre_cost_fabric_cost_dtls_id,yarn.id,yarn.count_id,yarn.copm_one_id, yarn.percent_one, yarn.type_id,yarn.cons_ratio,po.id,po.po_number order by book.job_no";
	
	/*$sql=sql_select("select m.requ_no, book.booking_no,book.job_no,yarn.count_id,yarn.copm_one_id,yarn.percent_one, yarn.type_id,yarn.cons_ratio,sum(book.grey_fab_qnty) AS grey_fab_qnty,sum(((book.grey_fab_qnty*yarn.cons_ratio)/100)) AS yarn_req,po.id as po_id,po.po_number,po.shipment_date,po.pub_shipment_date,colorSize.id,colorSize.order_quantity,colorSize.plan_cut_qnty from wo_booking_dtls book join (select count(b.id) as tot,a.requ_no, b.booking_no from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.item_category_id=1 $comp_sql_cond $buyer_sql_cond $booking_sql_cond $job_sql_cond $style_sql_cond $requ_sql_cond $date_cond_ship $date_cond_req and $nvl(b.booking_no,'0')>'0' group by a.requ_no,b.booking_no)  m on book.booking_no=m.booking_no join wo_pre_cost_fab_yarn_cost_dtls yarn on yarn.job_no=book.job_no and yarn.fabric_cost_dtls_id=book.pre_cost_fabric_cost_dtls_id join wo_pre_cost_fabric_cost_dtls fabric on yarn.fabric_cost_dtls_id=fabric.id join wo_pre_cos_fab_co_avg_con_dtls fabricDtls on fabric.id=fabricDtls.pre_cost_fabric_cost_dtls_id join wo_po_color_size_breakdown colorSize on fabricDtls.po_break_down_id=colorSize.po_break_down_id and fabric.item_number_id=colorSize.item_number_id and fabricDtls.color_number_id=colorSize.color_number_id and fabricDtls.gmts_sizes=colorSize.size_number_id and fabricDtls.cons>0 join wo_po_break_down po on po.id=fabricDtls.po_break_down_id where book.status_active=1 and book.is_deleted=0 group by m.requ_no,book.booking_no,book.job_no,yarn.count_id, yarn.type_id,yarn.cons_ratio,po.id,po.po_number,colorSize.id,colorSize.order_quantity,colorSize.plan_cut_qnty,po.shipment_date,po.pub_shipment_date,yarn.copm_one_id,percent_one order by book.job_no ");*/
	
$sql=sql_select("select m.requ_no, book.booking_no,book.job_no,yarn.count_id,yarn.copm_one_id,yarn.percent_one, yarn.type_id,yarn.cons_ratio,sum(book.grey_fab_qnty) AS grey_fab_qnty,sum(((book.grey_fab_qnty*yarn.cons_ratio)/100)) AS yarn_req,po.id as po_id,po.po_number,po.shipment_date,po.pub_shipment_date,colorSize.id,colorSize.order_quantity,colorSize.plan_cut_qnty from wo_booking_dtls book join (select count(b.id) as tot,a.requ_no, b.booking_no from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $comp_sql_cond $buyer_sql_cond $booking_sql_cond $job_sql_cond $style_sql_cond $requ_sql_cond $date_cond_ship $date_cond_req and $nvl(b.booking_no,'0')>'0' group by a.requ_no,b.booking_no)  m on book.booking_no=m.booking_no join wo_pre_cost_fab_yarn_cost_dtls yarn on yarn.job_no=book.job_no and yarn.fabric_cost_dtls_id=book.pre_cost_fabric_cost_dtls_id join wo_pre_cost_fabric_cost_dtls fabric on yarn.fabric_cost_dtls_id=fabric.id join wo_pre_cos_fab_co_avg_con_dtls fabricDtls on fabric.id=fabricDtls.pre_cost_fabric_cost_dtls_id join wo_po_color_size_breakdown colorSize on fabricDtls.po_break_down_id=colorSize.po_break_down_id and fabric.item_number_id=colorSize.item_number_id and fabricDtls.color_number_id=colorSize.color_number_id and fabricDtls.gmts_sizes=colorSize.size_number_id and fabricDtls.cons>0 join wo_po_break_down po on po.id=fabricDtls.po_break_down_id where book.status_active=1 and book.is_deleted=0 group by m.requ_no,book.booking_no,book.job_no,yarn.count_id, yarn.type_id,yarn.cons_ratio,po.id,po.po_number,colorSize.id,colorSize.order_quantity,colorSize.plan_cut_qnty,po.shipment_date,po.pub_shipment_date,yarn.copm_one_id,percent_one order by book.job_no ");
	
	
	foreach($sql as $row_booking){
		$itemIndex=$row_booking[csf('copm_one_id')]."_".$row_booking[csf('percent_one')];
		
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('count_id')]][$row_booking[csf('type_id')]][$itemIndex]['ponumber'][$row_booking[csf('po_number')]]=$row_booking[csf('po_number')];
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('count_id')]][$row_booking[csf('type_id')]][$itemIndex]['shipdate'][$row_booking[csf('po_id')]]=strtotime($row_booking[csf('shipment_date')]);
		
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('count_id')]][$row_booking[csf('type_id')]][$itemIndex]['poqty']+=$row_booking[csf('order_quantity')];
	}
	
	
	$sql_alloca=sql_select("select m.requ_no, book.booking_no,book.job_no,sum(book.qnty) as qnty,book.item_id,yarn.id,yarn.yarn_count_id,yarn.yarn_comp_type1st, yarn.yarn_comp_percent1st, yarn.yarn_type from inv_material_allocation_dtls book join (select count(b.id) as tot,a.requ_no, b.booking_no from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $comp_sql_cond $buyer_sql_cond $booking_sql_cond $job_sql_cond $style_sql_cond $requ_sql_cond $date_cond_ship $date_cond_req and $nvl(b.booking_no,'0')>'0' group by a.requ_no,b.booking_no) m on book.booking_no=m.booking_no join product_details_master yarn on yarn.id=book.item_id   where  book.item_category=1 and book.status_active=1 and book.is_deleted=0 group by m.requ_no,book.booking_no,book.job_no,yarn.id,book.item_id,yarn.yarn_count_id,yarn.yarn_comp_type1st, yarn.yarn_comp_percent1st, yarn.yarn_type order by book.job_no");
	foreach($sql_alloca as $row_booking){
		$itemIndex=$row_booking[csf('yarn_comp_type1st')]."_".$row_booking[csf('yarn_comp_percent1st')];
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('yarn_count_id')]][$row_booking[csf('yarn_type')]][$itemIndex]['allo_req']+=$row_booking[csf('qnty')];
		
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('yarn_count_id')]][$row_booking[csf('yarn_type')]][$itemIndex]['rowFrom']=$row_booking[csf('all')];
		
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('yarn_count_id')]][$row_booking[csf('yarn_type')]][$itemIndex]['item']=$composition[$row_booking[csf('yarn_comp_type1st')]]." ".$row_booking[csf('yarn_comp_percent1st')];
	}
	
	
	$sql_recv=sql_select("select m.requ_no,m.booking_no,m.job_no,recvmst.recv_number,tarns.pi_wo_batch_no,tarns.prod_id,tarns.cons_quantity,prod.id,prod.yarn_count_id,prod.yarn_comp_type1st, prod.yarn_comp_percent1st, prod.yarn_type from inv_receive_master recvmst join (select count(b.id) as tot,a.requ_no, b.booking_no,b.job_no,c.id,c.wo_number from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b,wo_non_order_info_mst c,wo_non_order_info_dtls d where a.id=b.mst_id and c.id=d.mst_id and b.id=d.requisition_dtls_id and d.requisition_no= a.requ_no and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $comp_sql_cond $buyer_sql_cond $booking_sql_cond $job_sql_cond $style_sql_cond $requ_sql_cond $date_cond_ship $date_cond_req and $nvl(b.booking_no,'0')>'0' group by a.requ_no,b.booking_no,b.job_no,c.id,c.wo_number) m on recvmst.booking_id=m.id join inv_transaction tarns on recvmst.id=tarns.mst_id join product_details_master prod on tarns.prod_id=prod.id where recvmst.receive_basis=2  and recvmst.receive_purpose =16 and recvmst.item_category=1 and recvmst.entry_form=1 and tarns.transaction_type=1");
		foreach($sql_recv as $row_booking){
		$itemIndex=$row_booking[csf('yarn_comp_type1st')]."_".$row_booking[csf('yarn_comp_percent1st')];
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('yarn_count_id')]][$row_booking[csf('yarn_type')]][$itemIndex]['recv_req']+=$row_booking[csf('cons_quantity')];
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('yarn_count_id')]][$row_booking[csf('yarn_type')]][$itemIndex]['rowFrom']="recv";
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('yarn_count_id')]][$row_booking[csf('yarn_type')]][$itemIndex]['item']=$composition[$row_booking[csf('yarn_comp_type1st')]]." ".$row_booking[csf('yarn_comp_percent1st')];
	}
	
	$sql_recvRe=sql_select("select m.requ_no,m.booking_no,m.job_no,recvmst.recv_number,tarns.pi_wo_batch_no,tarns.prod_id,tarns.cons_quantity,prod.id,prod.yarn_count_id,prod.yarn_comp_type1st, prod.yarn_comp_percent1st, prod.yarn_type from inv_receive_master recvmst join (select count(b.id) as tot,a.requ_no, b.booking_no,b.job_no,c.id,c.wo_number from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b,wo_non_order_info_mst c,wo_non_order_info_dtls d where a.id=b.mst_id and c.id=d.mst_id and b.id=d.requisition_dtls_id and d.requisition_no= a.requ_no and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $comp_sql_cond $buyer_sql_cond $booking_sql_cond $job_sql_cond $style_sql_cond $requ_sql_cond $date_cond_ship $date_cond_req and $nvl(b.booking_no,'0')>'0' group by a.requ_no,b.booking_no,b.job_no,c.id,c.wo_number) m on recvmst.booking_id=m.id join inv_issue_master ism on recvmst.recv_number=ism.received_mrr_no join inv_transaction tarns on ism.id=tarns.mst_id join product_details_master prod on tarns.prod_id=prod.id where recvmst.receive_basis=2  and recvmst.receive_purpose =16 and recvmst.item_category=1 and recvmst.entry_form=1 and tarns.transaction_type=3");
	foreach($sql_recvRe as $row_booking){
		$itemIndex=$row_booking[csf('yarn_comp_type1st')]."_".$row_booking[csf('yarn_comp_percent1st')];
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('yarn_count_id')]][$row_booking[csf('yarn_type')]][$itemIndex]['recvR_req']+=$row_booking[csf('cons_quantity')];
	}
	
	$sql_recv=sql_select( "select m.requ_no,m.booking_no,m.job_no,recvmst.recv_number,tarns.pi_wo_batch_no,tarns.prod_id,tarns.cons_quantity,prod.id,prod.yarn_count_id,prod.yarn_comp_type1st, prod.yarn_comp_percent1st, prod.yarn_type from inv_receive_master recvmst join (select count(b.id) as tot,a.requ_no, b.booking_no,b.job_no,c.id,c.wo_number,f.pi_id from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b,wo_non_order_info_mst c,wo_non_order_info_dtls d, com_pi_master_details e, com_pi_item_details f where a.id=b.mst_id and c.id=d.mst_id and b.id=d.requisition_dtls_id and d.requisition_no= a.requ_no and e.id=f.pi_id and a.item_category_id=1 and f.work_order_no=c.wo_number and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $comp_sql_cond $buyer_sql_cond $booking_sql_cond $job_sql_cond $style_sql_cond $requ_sql_cond $date_cond_ship $date_cond_req and $nvl(b.booking_no,'0')>'0' group by a.requ_no,b.booking_no,b.job_no,c.id,c.wo_number,f.pi_id) m on recvmst.booking_id=m.pi_id join inv_transaction tarns on recvmst.id=tarns.mst_id join product_details_master prod on tarns.prod_id=prod.id where recvmst.receive_basis=1  and recvmst.receive_purpose =16 and recvmst.item_category=1 and recvmst.entry_form=1 and tarns.transaction_type=1");
	foreach($sql_recv as $row_booking){
		$itemIndex=$row_booking[csf('yarn_comp_type1st')]."_".$row_booking[csf('yarn_comp_percent1st')];
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('yarn_count_id')]][$row_booking[csf('yarn_type')]][$itemIndex]['recv_req']+=$row_booking[csf('cons_quantity')];
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('yarn_count_id')]][$row_booking[csf('yarn_type')]][$itemIndex]['item']=$composition[$row_booking[csf('yarn_comp_type1st')]]." ".$row_booking[csf('yarn_comp_percent1st')];
	}
	
	$sql_recvRe=sql_select( "select m.requ_no,m.booking_no,m.job_no,recvmst.recv_number,tarns.pi_wo_batch_no,tarns.prod_id,tarns.cons_quantity,prod.id,prod.yarn_count_id,prod.yarn_comp_type1st, prod.yarn_comp_percent1st, prod.yarn_type from inv_receive_master recvmst join (select count(b.id) as tot,a.requ_no, b.booking_no,b.job_no,c.id,c.wo_number,f.pi_id from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b,wo_non_order_info_mst c,wo_non_order_info_dtls d, com_pi_master_details e, com_pi_item_details f where a.id=b.mst_id and c.id=d.mst_id and b.id=d.requisition_dtls_id and d.requisition_no= a.requ_no and e.id=f.pi_id and a.item_category_id=1 and f.work_order_no=c.wo_number and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $comp_sql_cond $buyer_sql_cond $booking_sql_cond $job_sql_cond $style_sql_cond $requ_sql_cond $date_cond_ship $date_cond_req and $nvl(b.booking_no,'0')>'0' group by a.requ_no,b.booking_no,b.job_no,c.id,c.wo_number,f.pi_id) m on recvmst.booking_id=m.pi_id join inv_issue_master ism on recvmst.recv_number=ism.received_mrr_no join inv_transaction tarns on ism.id=tarns.mst_id join product_details_master prod on tarns.prod_id=prod.id where recvmst.receive_basis=1  and recvmst.receive_purpose =16 and recvmst.item_category=1 and recvmst.entry_form=1 and tarns.transaction_type=3");
	foreach($sql_recvRe as $row_booking){
		$itemIndex=$row_booking[csf('yarn_comp_type1st')]."_".$row_booking[csf('yarn_comp_percent1st')];
		$arr_multivalue[$row_booking[csf('requ_no')]][$row_booking[csf('booking_no')]][$row_booking[csf('job_no')]][$row_booking[csf('yarn_count_id')]][$row_booking[csf('yarn_type')]][$itemIndex]['recvR_req']+=$row_booking[csf('cons_quantity')];
	}
	ob_start();
	?>
	<fieldset>
	<table width="2200">
	<tr class="form_caption">
	<td colspan="8" align="center">
	Ship Date wise / Req. Date wise Yarn Purchase Requisition
	<br/>
	<? if(($txt_date_from && $txt_date_to)!=""){ echo $txt_date_from . ' To ' . $txt_date_to; } ?>
	</td>
	</tr>
	</table>
	<table style="margin-top:10px" id="table_header_1" class="rpt_table" width="2200" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
        <tr>
            <th width="35">SL</th>
            <th width="140">Company</th>
            <th width="130">Req. No</th>
            <th width="100">Req  Date </th>
            <th width="150">Fab.Booking </th>
            <th width="100">Job No</th>
            <th width="130">Buyer </th> 
            <th width="100">Style Ref </th> 
            <th width="100">Yarn Color</th> 
            <th width="100">Yarn Details</th>
            <th width="100">Item Name</th>
            <th width="100">Required Qty(As booking)</th>	
            <th width="100">Requisition Qty</th>
            <th width="100">Allocate Qty</th>
            <th width="100">Yet to Purchase </th>
            <th width="130">Receive Qty</th>  
            <th width="100">Receive Balance</th> 
            <th width="100">Order No </th> 
            <th width="100">Order Qty (Pcs) </th>
            <th width="80"> Ship Date </th> 
            <th>Remarks</th> 
        </tr>
	</thead>
	</table>
	<div style="width:2220px; max-height:400px; overflow-y:scroll" id="scroll_body">
	<table class="rpt_table" width="2200" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	<?
	$total_requisition_qty="";
	$total_required_qty="";
	$total_allocate_qty="";
	$total_yet_to_pur_qty="";
	$total_receive_qty="";
	$total_rec_balance_qty="";
	$i=1; 
	$b=0; 
	$al=0;	
	$check_order=array();
	$check_allocate_qty=array();
	
	foreach($arr_multivalue as $requ_no => $fabBookingArr){
		foreach($fabBookingArr as $fabBookingno => $jobnoArr){
			foreach($jobnoArr as $jobno => $YcountArr){
				foreach($YcountArr as $count_id => $YtypeArr){
					foreach($YtypeArr as $type_id => $type_value_arr){
						foreach($type_value_arr as $type_value){
							if($type_value['requisi_qty']>0)
							{
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
								?>
								<tr bgcolor="<? echo  $bgcolor; ?>" title="<? echo $type_value['rowFrom']?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="35"><? echo $i; ?></td>
								<td width="140"><p><? echo $company_arr[$type_value['companyId']]; ?></p></td>
								<td width="130"><p><? echo $type_value['requNo']; ?></p></td>
								<td width="100"><p><? echo change_date_format($type_value['requisitionDate']); ?></p></td>
								<td width="150"><p><? echo $type_value['bookingNo']; ?></p></td>
								<td width="100"><p><? echo $type_value['job_no'];  ?></p></td>
								<td width="130"><p><? echo $buyer_arr[$type_value['buyer_id']] ?></p></td>
								<td width="100"><p><? echo $type_value['style_ref_no']; ?></p></td>
								<td width="100"><p><? echo implode(',',$type_value['color_id']); ?></p></td>
								
								<td width="100"><p><? echo $count_arr[$count_id]." ".$yarn_type[$type_id];?></p></td>
								<td width="100"><p><? echo $type_value['item']; ?></p></td>
								
								<td width="100" align="right" title="<? echo "Requisition Qty:" .$type_value['requisi_qty'];?>"><p><? echo number_format($type_value['booking_req'],4); ?></p></td>
								<td width="100" align="right"><p><? echo number_format($type_value['requisi_qty'],4); ?></p></td>
								<td width="100" align="right"><p><?  echo number_format($type_value['allo_req'],4); ?></p></td>
								<td width="100" align="right"><p>
								<? 
									$yetToPurchase=$type_value['requisi_qty']-$type_value['allo_req'];
									echo number_format($yetToPurchase,4);
								?></p>
								</td>
								<td width="130" align="right"><p>
								<? 
								$aclReceive=$type_value['recv_req']-$type_value['recvR_req'];
								echo number_format($aclReceive,4);
								?></p>
								</td>
								<td width="100" align="right"><p>
								<? 
									$aclReceiveBal=$yetToPurchase-$aclReceive;
									echo number_format($aclReceiveBal,4);
								?></p>
								</td>
								<td width="100" align=""><p><? echo implode(",",$type_value['ponumber']); ?></p></td>
								<td width="100" align="right"><p><? echo $type_value['poqty']; ?></p></td>
								<td width="80" align="right"><p><? if(count($type_value['shipdate'])>0)echo date("d-m-Y",max($type_value['shipdate'])); ?></p></td>
								<td><p><? echo $type_value['remarks']; ?></p></td>
								</tr>
								<?
								
								//if($i===5){var_dump($YtypeArr );die;}
								
								$total_requisition_qty+=number_format($type_value['requisi_qty'],4,".","");
								$total_required_qty+=number_format($type_value['booking_req'],4,".","");
								$total_allocate_qty+=number_format($type_value['allo_req'],4,".","");
								$total_yet_to_pur_qty+=number_format($yetToPurchase,4,".","");
								$total_receive_qty+=number_format($aclReceive,4,".","");
								$total_rec_balance_qty+=number_format($aclReceiveBal,4,".","");
								$i++;
							}
						}
					}
				}
			}
		}
	}
	?>
	</table>
	<table class="rpt_table" width="2200" cellpadding="0" cellspacing="0" border="1" rules="all">
        <tfoot>
            <th width="35"></th>
            <th width="140"></th>
            <th width="130"></th>
            <th width="100"></th>
            <th width="150"></th>
            <th width="100"></th>
            <th width="130"></th> 
            <th width="100"> </th> 
            <th width="100"></th> 
            <th width="100"></th>
            <th width="100" align="right"><b> Grand Total=</b></th>
            <th width="100" align="right" id="value_total_required_qty"><b><? echo number_format($total_required_qty,2); ?></b></th>
            <th width="100" align="right" id="value_total_requisition_qty"><b><? echo number_format($total_requisition_qty,2); ?></b></th>
            <th width="100" align="right" id="value_total_allocate_qty"><b><? echo number_format($total_allocate_qty,2); ?></b></th>
            <th width="100" align="right" id="value_total_yet_to_pur_qty"><b><? echo number_format($total_yet_to_pur_qty,2); ?></b></th>
            <th width="130" align="right" id="value_total_receive_qty"><b><? echo number_format($total_receive_qty,2); ?></b></th>
            <th width="100" align="right" id="value_total_rec_balance_qty"><b><? echo number_format($total_rec_balance_qty,2); ?></b></th>
            <th width="100"></th> 
            <th width="100" id="order_total_qty"></th>
            <th width="80"></th> 
            <th></th> 
        </tfoot>
	</table>
	</div>
	
	</fieldset> 
	<?
	foreach (glob(".../../../../ext_resource/tmp_report/$user_name*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	echo "$total_data****$filename";
	exit();
}
?>