<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

/* if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(2) and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 order by a.supplier_name ","id,supplier_name", 1, "-- Select --", 0, "",0 );
	exit();
} */


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$yarnCount_arr=return_library_array( "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_search_no=str_replace("'","",$txt_search_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_receive_status=str_replace("'","",$cbo_receive_status);
	$cbo_supplier=str_replace("'","",$cbo_supplier);
	$cbo_po_type=str_replace("'","",$cbo_po_type);
	$cbo_approval_status=str_replace("'","",$cbo_approval_status);
	
	
	$str_cond=$str_cond_independ="";
	
	//echo $cbo_supplier ; die;
	// req condition check here
	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$txt_date_from=change_date_format($txt_date_from,'','',-1);
		$txt_date_to=change_date_format($txt_date_to,'','',-1);
	}
	//($cbo_supplier!=0) $str_cond.=" and a.supplier_id=$cbo_supplier";
	$str_cond=$str_cond_independ=$pi_cond=$btb_cond="";
	if($txt_search_no!="") $str_cond.=" and d.wo_number_prefix_num ='$txt_search_no'";
	if($txt_date_from!="" && $txt_date_to!="")  $str_cond.=" and d.wo_date between '$txt_date_from' and '$txt_date_to'";
	if($txt_search_no!="") $str_cond_independ.=" and d.wo_number_prefix_num = '$txt_search_no'";
	if($txt_date_from!="" && $txt_date_to!="") $str_cond_independ.=" and d.wo_date between '$txt_date_from' and '$txt_date_to'";
	if($cbo_supplier!=0) $str_cond.=" and d.supplier_id=$cbo_supplier";
	if($cbo_company_name!=0) $str_cond.=" and d.company_name in($cbo_company_name)";
	//echo $str_cond; die;
	$sql_pi=sql_select("select a.id as pi_id, a.supplier_id as pi_suplier, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, b.id as pi_dtls_id, b.work_order_id, b.work_order_dtls_id, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, b.uom, b.quantity, b.rate, b.amount from com_pi_master_details a, com_pi_item_details b 
	where a.item_category_id=1 and a.id=b.pi_id and a.status_active=1 and b.status_active=1 and a.goods_rcv_status<>1 $pi_cond");
	
	$pi_data_arr=array();
	foreach($sql_pi as $row)
	{
		$pi_wo_dtls_id_all[]=$row[csf("work_order_dtls_id")];
		$pi_id_arr[]=$row[csf("pi_id")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["work_order_dtls_id"]=$row[csf("work_order_dtls_id")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_id"]=$row[csf("pi_id")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_dtls_id"]=$row[csf("pi_dtls_id")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_id_all"].=$row[csf("pi_id")].",";
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_suplier"].=$row[csf("pi_suplier")].",";
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_number"].=$row[csf("pi_number")].",";
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_date"].=$row[csf("pi_date")].",";
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["last_shipment_date"].=$row[csf("last_shipment_date")].",";
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["currency_id"].=$row[csf("currency_id")].",";
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["work_order_id"]=$row[csf("work_order_id")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["color_id"]=$row[csf("color_id")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["count_name"]=$row[csf("count_name")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_composition_item1"]=$row[csf("yarn_composition_item1")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_composition_percentage1"]=$row[csf("yarn_composition_percentage1")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_composition_item2"]=$row[csf("yarn_composition_item2")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_comp_percent2nd"]=$row[csf("yarn_comp_percent2nd")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_type"]=$row[csf("yarn_type")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["uom"]=$row[csf("uom")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["quantity"]+=$row[csf("quantity")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["rate"]=$row[csf("rate")];
		$pi_data_arr[$row[csf("work_order_dtls_id")]]["amount"]+=$row[csf("amount")];
	}
	$sql_btb="select a.id as btb_id, a.lc_number,a.lc_date,a.lc_value, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.pi_entry_form=165 and a.status_active=1 and b.status_active=1";
	//echo $sql_btb;die;
		
	$sql_btb_result=sql_select($sql_btb);
	$btb_data_arr=array();
	foreach($sql_btb_result as $row)
	{
		$btb_data_arr[$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
		$btb_data_arr[$row[csf("pi_id")]]["btb_id"]=$row[csf("btb_id")];
		$btb_data_arr[$row[csf("pi_id")]]["btb_id_all"].=$row[csf("btb_id")].",";
		$btb_data_arr[$row[csf("pi_id")]]["lc_number"].=$row[csf("lc_number")].",";
		$btb_data_arr[$row[csf("pi_id")]]["lc_date"].=change_date_format($row[csf("lc_date")]).",";
		$btb_data_arr[$row[csf("pi_id")]]["lc_value"]+=$row[csf("lc_value")];
	}
	//echo "<pre>";
    //print_r($btb_data_arr);
	$rcv_return_sql=sql_select("select b.prod_id, a.received_id, b.cons_quantity, b.cons_quantity, b.cons_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=8 and b.transaction_type=3 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$rcv_rtn_data=array();
	foreach($rcv_return_sql as $row)
	{
		$rcv_rtn_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
		$rcv_rtn_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
	}
	//wo_dtls_id
	$req_wo_recv_sql=sql_select("select a.receive_basis, a.booking_id, c.color, c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, b.order_qnty as recv_qnty, b.order_amount as recv_amt, b.mst_id, b.prod_id, b.transaction_date ,b.pi_wo_req_dtls_id
	from  inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and a.item_category=1 and a.booking_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	order by a.booking_id, a.receive_basis, c.color, c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, b.transaction_date");
	$min_date=$max_date="";
	$b=0;
	foreach($req_wo_recv_sql as $row)
	{
		if($item_check[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=="")
		{
			$item_check[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("yarn_comp_type1st")];
			$min_date=$row[csf("transaction_date")];
			$max_date=$row[csf("transaction_date")];
			$b++;
		}
		else
		{
			if(strtotime($row[csf("transaction_date")])>strtotime($max_date)) $max_date=$row[csf("transaction_date")];
		}
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("pi_wo_req_dtls_id")]]['booking_id']=$row[csf("booking_id")];
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("pi_wo_req_dtls_id")]]['min_date']=$min_date;
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("pi_wo_req_dtls_id")]]['max_date']=$max_date;
		
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("pi_wo_req_dtls_id")]]['receive_basis']=$row[csf("receive_basis")];
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("pi_wo_req_dtls_id")]]['recv_qnty']+=$row[csf("recv_qnty")]-$rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_quantity"];
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("pi_wo_req_dtls_id")]]['recv_amt']+=$row[csf("recv_amt")]-$rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_amount"];

	}
	//echo $b."<pre>";print_r($req_wo_recv_arr[15678][1]);die;
	$wo_qty_arr=sql_select("select a.id as wo_id, b.color_name as color, b.yarn_type as yarn_type, b.yarn_count as yarn_count_id, b.yarn_comp_type1st as yarn_comp_type1st, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id  and a.item_category in (1) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id,b.color_name,b.yarn_type,b.yarn_count,b.yarn_comp_type1st");
	//and a.company_name=$cbo_company_name
	$wo_pipe_array=array();
	foreach($wo_qty_arr as $row)
	{
		$wo_pipe_array[$row[csf("wo_id")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("qty")];
	}
	/*echo "select a.id as pi_id, b.color_id as color, b.yarn_type as yarn_type, b.count_name as yarn_count_id, b.yarn_composition_item1 as yarn_comp_type1st, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.importer_id=$cbo_company_name and a.item_category_id in (1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.color_id, b.yarn_type, b.count_name, b.yarn_composition_item1";die;*/
	$pi_qty_arr=sql_select("select a.id as pi_id, b.color_id as color, b.yarn_type as yarn_type, b.count_name as yarn_count_id, b.yarn_composition_item1 as yarn_comp_type1st, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and a.item_category_id in (1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.color_id, b.yarn_type, b.count_name, b.yarn_composition_item1");  //and a.importer_id=$cbo_company_name
	$pi_pipe_array=array();
	foreach($pi_qty_arr as $row)
	{
		$pi_pipe_array[$row[csf("pi_id")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("qty")];
	}
	
	//echo "<pre>";print_r($pi_pipe_array[15076]); die;
	
	$ref_close_cond="";
	if($cbo_receive_status==4) $ref_close_cond .=" and d.ref_closing_status<>1";
	if($cbo_po_type>0)
	{
		if($cbo_po_type==2) $ref_close_cond .=" and d.payterm_id = 5";
		else $ref_close_cond .=" and d.payterm_id <> 5";
	}
	//echo $cbo_year;die;
	if($cbo_year>0)
	{
		if($db_type==0) $year_cond=" and year(d.insert_date)='$cbo_year'"; else $year_cond=" and to_char(d.insert_date,'YYYY')='$cbo_year'";
	} 
	$approve_cond="";
	if($cbo_approval_status>0) 
	{
		if($cbo_approval_status==1) $approve_cond=" and d.is_approved=1"; else $approve_cond=" and d.is_approved<>1";
	}
	$sql_req_wo="select a.id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.company_id, b.buyer_id, b.id as req_dtls_id, b.color_id, b.count_id, b.composition_id, b.yarn_type_id, b.cons_uom as req_uom, b.quantity as req_qnty, b.rate as req_rate, b.amount as req_amt, d.id as wo_id,d.wo_number, d.wo_number_prefix_num, d.wo_date, d.supplier_id, d.is_approved, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount, e.number_of_lot, e.yarn_inhouse_date ,e.delivery_end_date, a.basis
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
	left join wo_non_order_info_dtls e on b.id=e.requisition_dtls_id and e.status_active=1 and e.is_deleted=0 
	left join wo_non_order_info_mst d on d.id=e.mst_id and d.wo_basis_id<>2 and d.status_active=1 and d.is_deleted=0 $ref_close_cond $year_cond $approve_cond 
	where a.id=b.mst_id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=70 $str_cond
	union all
	select 0 as id, null as requ_no, null as requ_prefix_num, null as requisition_date, d.company_name as company_id, e.buyer_id, 0 as req_dtls_id, e.color_name as color_id, e.yarn_count as count_id, e.yarn_comp_type1st as composition_id, e.yarn_type as yarn_type_id, e.uom as req_uom, 0 as req_qnty, 0 as req_rate, 0 as req_amt, d.id as wo_id, d.wo_number, d.wo_number_prefix_num, d.wo_date, d.supplier_id, d.is_approved, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount, e.number_of_lot, e.yarn_inhouse_date, e.delivery_end_date, null as basis
	from wo_non_order_info_dtls e, wo_non_order_info_mst d
	where d.id=e.mst_id and e.item_category_id=1 and e.status_active=1 and e.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=144 and d.wo_basis_id=2 $ref_close_cond $year_cond $approve_cond $str_cond 
	order by wo_date desc, wo_id desc"; //and a.company_id='$cbo_company_name'
	//echo $sql_req_wo;//die;
	$req_result=sql_select($sql_req_wo);
	//echo "jahid";die;
	ob_start();
	?>
    <div style="width:2930px">
        <table width="1600" cellpadding="0" cellspacing="0" id="caption"  align="left">
            <tr>
                <td align="center" width="100%"  class="form_caption" colspan="47"><strong style="font-size:22px"> <? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
            </tr> 
            <tr>  
                <td align="center" width="100%" class="form_caption"  colspan="47"><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
            </tr>
            <tr>  
                <td align="center" width="100%"  class="form_caption"  colspan="47"><strong style="font-size:12px">From : <? echo $txt_date_from; ?> To : <? echo $txt_date_to; ?></strong></td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
            </tr>
        </table>
            <table width="2930" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1"  align="left">
            <thead>
            	<tr>
                	<th width="30" rowspan="2">Sl</th>
                	<th colspan="17">Work Order Details</th>
                	<th colspan="6">Matarials Received Information</th>
                	<th colspan="3">PI & LC Details</th>
                	<th colspan="7">Requisiton Details</th>
                </tr>
                <tr>
					<th width="130">Company Name</th>
                	<th width="50">Y/PO ID</th>
                    <th width="80">Y/PO No.</th>
                	<th width="60">PO Date</th>
                    <th width="80">Approval Status</th>
                	<th width="140">Supplier</th>
                    <th width="80">Yarn Color</th>
                    <th width="50">Count</th>
                    <th width="250">Composition</th>
                    <th width="80">Yarn Type</th>
                    <th width="50">UOM</th>
                    <th width="80">PO Qty</th>
                    <th width="70">PO Rate</th>
                    <th width="100">PO Value</th>
                    <th width="70">No of Lot</th>
                    <th width="70">IH Start</th>
                    <th width="70">IH Close</th>
                    <th width="80">MRR Qnty</th>
                    <th width="90">MRR Value($)</th>
                    <th width="80">Balance Qty</th>
                    <th width="90">Balance Value ($)</th>
                    <th width="70">1st Rcv Date</th>
                    <th width="70">Last Rcv Date</th>
                    <th width="100">PI No.</th>
                    <th width="100">LC No.</th>
                    <th width="70">LC Date</th>
                    <th width="120">Req. No</th>
                    <th width="100">Req. Basis</th>
                    <th width="70">Req. Date</th>
                    <th width="100">Buyer</th>
                    <th width="80">Req. Qty.</th>
                    <th width="70">Req. Rate</th>
                    <th>Req. Value ($)</th>
                </tr>
            </thead>
        </table>
        <div style="width:2950px; overflow-y:scroll; max-height:450px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
        <table width="2930px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
            <tbody>
            <?
            $k=1;
			/*foreach($req_result as $val)
			{
				if($val[csf("wo_id")]>0)
				{
					$tem_arr[$val[csf("wo_id")]][$val[csf("prod_id")]]++;
				}
			}*/
			//var_dump($tem_arr);die;
			$array_check=array();$m=1;$q=1;
            foreach($req_result as $row)
            {
            	if($row[csf("wo_dtls_id")]=='') 
            	{
            		$row[csf("wo_dtls_id")]=0;
            	}
            	
				if ($k%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				
				$mrr_qnty=$pipe_wo_qnty=$pipe_pi_qnty=$pipe_line=0;$min_date=$max_date="";
				$mrr_value=$short_value=0;
				$booking_id=$receive_basis="";			

				$id=$row_result[csf('id')];
				$reqsn_rate=$row[csf("amount")]/$row[csf("req_quantity")];
				if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt']!="")
				{
					$wo_pi_ids=$row[csf("wo_id")];
				}
				else
				{
					$wo_pi_ids=$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"];
				}
				
				/*if($mrr_check_prev[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=="")
                {*/
					//$mrr_check_prev[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=$row[csf("wo_id")];
					/*if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty']!="")
					$mrr_qnty=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty']; 
					else $mrr_qnty=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty'];*/
					
				if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("wo_dtls_id")]]['recv_qnty']!="")
				$mrr_qnty=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("wo_dtls_id")]]['recv_qnty']; 
				else $mrr_qnty=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]][$pi_data_arr[$row[csf("wo_dtls_id")]]['pi_dtls_id']]['recv_qnty'];
				
				

				if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("wo_dtls_id")]]['recv_amt']!="")
				{
					$mrr_value=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("wo_dtls_id")]]['recv_amt'];
					$booking_id=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("wo_dtls_id")]]['booking_id'];
					$receive_basis=2;

					$min_date=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("wo_dtls_id")]]['min_date'];
					$max_date=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("wo_dtls_id")]]['max_date'];
					//$short_value=$row[csf("wo_amount")]-$mrr_value;
					
				}
				else 
				{
					$mrr_value=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]][$pi_data_arr[$row[csf("wo_dtls_id")]]['pi_dtls_id']]['recv_amt'];
					$booking_id=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]][$pi_data_arr[$row[csf("wo_dtls_id")]]['pi_dtls_id']]['booking_id'];
					$receive_basis=1;

					$min_date=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]][$pi_data_arr[$row[csf("wo_dtls_id")]]['pi_dtls_id']]['min_date'];
					$max_date=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]][$pi_data_arr[$row[csf("wo_dtls_id")]]['pi_dtls_id']]['max_date'];
					//$short_value=$row[csf("wo_amount")]-$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"];
					//$short_qty=$row[csf("wo_qnty")]-$pi_data_arr[$row[csf("wo_dtls_id")]]["quantity"];
				}
				//$test_datas=$wo_pi_ids."*".$receive_basis."*".$row[csf("color_id")]."*".$row[csf("yarn_type_id")]."*".$row[csf("count_id")]."*".$row[csf("composition_id")]."*".$pi_data_arr[$row[csf("wo_dtls_id")]]['pi_dtls_id'];
					
				/*}*/
				if($pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]=="") $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]=0;
				if($mrr_value=="") $mrr_value=0;
				if($mrr_qnty=="") $mrr_qnty=0;
				if($mrr_value>0 && $mrr_qnty>0)  $recv_rate=$mrr_value/$mrr_qnty;
				
				$receiving_cond=0;
				if($cbo_receive_status==1 && ($mrr_value=="" || $mrr_value==0)) $receiving_cond=1;
				if($cbo_receive_status==2 && $receive_basis==2 && number_format($row[csf("wo_amount")],2,'.','')>number_format($mrr_value,2,'.','') && $mrr_value>0) $receiving_cond=1;
				if($cbo_receive_status==2 && $receive_basis==1 && number_format($pi_data_arr[$row[csf("wo_dtls_id")]]["amount"],2,'.','')>number_format($mrr_value,2,'.','') && $mrr_value>0) $receiving_cond=1;
				if($cbo_receive_status==3 && $receive_basis==2 && number_format($mrr_value,2,'.','')>=number_format($row[csf("wo_amount")],2,'.','') && $mrr_value>0) $receiving_cond=1;
				if($cbo_receive_status==3 && $receive_basis==1 && number_format($mrr_value,2,'.','')>=number_format($pi_data_arr[$row[csf("wo_dtls_id")]]["amount"],2,'.','') && $mrr_value>0) $receiving_cond=1;
				if($cbo_receive_status==4 && $receive_basis==2 && (number_format($mrr_value,2,'.','') < number_format($row[csf("wo_amount")],2,'.','') || number_format($mrr_value,2,'.','')==0)) $receiving_cond=1;
				if($cbo_receive_status==4 && $receive_basis==1 && (number_format($mrr_value,2,'.','') < number_format($pi_data_arr[$row[csf("wo_dtls_id")]]["amount"],2,'.','') || number_format($mrr_value,2,'.','')==0)) $receiving_cond=1;
				if($cbo_receive_status==5) $receiving_cond=1;
				//echo $mrr_value."=".$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]."=".$row[csf("wo_number_prefix_num")]."=".$row[csf("wo_dtls_id")]."<br>";
				if($receiving_cond==1)
				{
					//echo $mrr_qnty.'==';
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                        <td width="30" align="center" title="<? echo $cbo_receive_status."=".$receive_basis."=".$mrr_value."=".$row[csf("wo_amount")]."=".$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; ?>"><p><? echo $k;//$row_result[csf('id')];?></p></td>
                        <?php /*?><td width="50" align="center" title="<? echo $row[csf("wo_id")]?>"><p><? echo $row[csf("wo_number_prefix_num")]; ?></p></td><?php */?>
						<td width="130" align="center" title="<? echo $row[csf("company_id")]?>"><p><? echo $company_arr[$row[csf("company_id")]]; ?></p></td>
                        <td width="50" align="center" title="<? echo $row[csf("wo_id")]?>"><p><? echo $row[csf("wo_id")]; ?></p></td>
                        <td width="80" align="center" title="<? echo $row[csf("wo_id")]?>"><p><? echo $row[csf("wo_number")]; ?></p></td>
                        <td width="60" align="center" title="<? echo $test_datas;?>"><p><? echo change_date_format($row[csf("wo_date")]); ?></p></td>
                        <td width="80" align="center" title="<? echo $row[csf("is_approved")];?>"><p><? if($row[csf("is_approved")]==1) echo "Approved"; else echo "Un-Approved"; ?></p></td>
                        <td width="140" align="left" title="<? echo $row[csf("supplier_id")]?>"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
                        <td width="80"><p><? echo $color_arr[$row[csf("wo_color")]]; ?></p></td>
                        <td width="50" align="center"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("wo_count")]]; ?></p></td>
                        <td width="250"><p>
                        <? 
                        if($row[csf("yarn_comp_type2nd")]>0) $wo_com_percent2=$row[csf("yarn_comp_percent2nd")]."%"; else $wo_com_percent2=" ";
                        echo $composition[$row[csf("wo_yarn_comp_type1st")]]." ".$row[csf("wo_yarn_comp_percent1st")]."% ".$composition[$row[csf("yarn_comp_type2nd")]]." ".$wo_com_percent2; ?></p></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf("wo_yarn_type")]]; ?></p></td>
                        <td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf("wo_uom")]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($row[csf("wo_rate")],2); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[csf("wo_amount")],2); ?></p></td>
                        <td width="70" align="center" title="<? echo $row[csf("wo_id")]?>"><p><? echo $row[csf("number_of_lot")]; ?></p></td>
                        <td width="70" align="center" title="<? echo $row[csf("wo_id")]?>"><p><? echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                        <td width="70" align="center" title="<? echo $row[csf("wo_id")]?>"><p><? echo change_date_format($row[csf("delivery_end_date")]); ?></p></td>
						<?
						if($mrr_qnty>0)
						{ 
							//$pipe_mrr_qnty=$mrr_qnty;
							if($receive_basis)$pi_wo_req_dtls_id=$pi_data_arr[$row[csf("wo_dtls_id")]]['pi_dtls_id'];
							else $pi_wo_req_dtls_id=$row[csf("wo_dtls_id")];
							
							?>
							<td width="80" align="right" title="<? echo $wo_pi_ids."=".$receive_basis."=".$row[csf("color_id")]."=".$row[csf("yarn_type_id")]."=".$row[csf("count_id")]."=".$row[csf("composition_id")];?>"><p><a href="##" onclick="fn_mrr_details('<? echo $booking_id;?>','<? echo $receive_basis;?>','<? echo $row[csf("color_id")];?>','<? echo $row[csf("yarn_type_id")];?>','<? echo $row[csf("count_id")];?>','<? echo $row[csf("composition_id")];?>','<? echo $pi_wo_req_dtls_id;?>','receive_details_popup')"><? echo number_format($mrr_qnty,2); ?></a></p></td>
							<?
							 $total_mrr_qnty  += $mrr_qnty;
						}
						else
						{
							?>
							<td width="80" align="right" title="<? echo $wo_pi_ids."=".$receive_basis."=".$row[csf("color_id")]."=".$row[csf("yarn_type_id")]."=".$row[csf("count_id")]."=".$row[csf("composition_id")];?>"><p><? echo '0.00'; ?></p></td>
							<?
						}
                        ?>
                        <td width="90" align="right"><p><? if($mrr_value>0) { echo number_format($mrr_value,2); $total_mrr_value += $mrr_value; } else echo '0.00' ;?></p></td>
                        <td align="right" width="80"><p><? 
                        	$balance_qty=$row[csf("wo_qnty")]-$mrr_qnty; echo number_format($balance_qty,2); //number_format($short_qty,2); ?></p></td>
                        <td align="right" width="90"><p><? 
                        	$balance_val=$row[csf("wo_amount")]-$mrr_value; echo number_format($balance_val,2); //number_format($short_value,2); ?></p></td>
                        <td width="70" align="center"><? if($min_date!="" && $min_date!="0000-00-00" && $mrr_qnty>0) echo change_date_format($min_date);?></td>
                        <td width="70" align="center"><? if($max_date!="" && $max_date!="0000-00-00" && $mrr_qnty>0) echo change_date_format($max_date);?></td>
                        <td width="100"><p><? $pi_num=implode(" , ",array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_number"]," , ")))); echo $pi_num; ?></p></td>
                        <? 
                        $btb_lc_no="";
                        $btb_lc_no_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_number"]," , ")));
                        $btb_lc_date_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_date"]," , ")));
                        $btb_lc_no=implode(" , ",$btb_lc_no_arr);
                        $btb_lc_date=implode(" , ",$btb_lc_date_arr);
                        ?>
                        <td width="100"><p><?  echo chop( $btb_lc_no,","); ?></p></td>
                        <td width="70" align="center"><p>&nbsp;<? echo $btb_lc_date; ?></p></td>
                        <td width="120" align="center" title="<? echo $row[csf("id")]?>"><p><? echo $row[csf("requ_no")]; ?></p></td>
						<td width="100" align="center" title="<? echo $row[csf("id")]?>"><p><? echo $issue_basis[$row[csf("basis")]]; ?></p></td>
                        <td width="70" align="center"><p>&nbsp;<? if($row[csf("requisition_date")]!="" && $row[csf("requisition_date")]!='0000-00-00') echo change_date_format($row[csf("requisition_date")]); ?></p></td>
                        <td width="100"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf("req_qnty")],2); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($row[csf("req_rate")],2); ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf("req_qnty")]*$row[csf("req_rate")],2); ?></p></td>
                    </tr>
                    <?
                    $k++;
                    $total_req_qty   += $row[csf("req_qnty")];
                    $total_req_amount+=$row[csf("req_qnty")]*$row[csf("req_rate")];
                   	//$total_req_amount+=$row[csf("req_amt")];
                    $total_wo_qty +=$row[csf("wo_qnty")];
                    $total_wo_amount +=$row[csf("wo_amount")];
                    $total_pi_qnty   += $pi_data_arr[$row[csf("wo_dtls_id")]]["quantity"];
                    $total_pi_amt    += $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"];
                    $total_short_amt += $balance_val;
                    $total_short_qty += $balance_qty;
				}
            }
            ?>
            </tbody>
        </table>
        </div>
        <table cellspacing="0" width="2930"  border="1" rules="all" class="rpt_table" id="report_table_footer" align="left">
            <tfoot>
            	<th width="30">&nbsp;</th>
                <th width="130">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="80">&nbsp;</th>
            	<th width="60">&nbsp;</th>
                <th width="80">&nbsp;</th>
            	<th width="140">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="250">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="50">Total:</th>
                <th width="80" align="right" id="value_total_wo_qty"><? echo number_format($total_wo_qty,2);?></th>
                <th width="70">&nbsp;</th>
                <th width="100" align="right" id="value_total_wo_amount"><? echo number_format($total_wo_amount,2);?></th>
                <th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80" align="right" id="value_total_mrr_qnty"><? echo number_format($total_mrr_qnty,2); ?></th> 
                <th width="90" align="right" id="value_total_mrr_value"><? echo number_format($total_mrr_value,2); ?></th>
                <th width="80" align="right" id="value_total_short_qty"><? echo number_format($total_short_qty,2); ?></th>
                <th width="90" align="right" id="value_total_short_amt"><? echo number_format($total_short_amt,2); ?></th>
                <th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="120">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80" id="value_total_req_qty"><p><? echo number_format($total_req_qty,2);?></p></th>
                <th width="70">&nbsp;</th>
                <th id="value_total_req_amount"><? echo number_format($total_req_amount,2);?></th>
            </tfoot>        
		</table>
        </div>
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


if($action=="receive_details_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	//echo $color_id."&&".$yarn_type."&&".$count_id."&&".$composition;die;
	?>
    	<div style="width:520px;">
    	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_pop">
        	<thead>
            	<tr>
                	<th width="30">SL</th>
                    <th width="120">WO/PI No</th>
                    <th width="130">MRR No.</th>
                    <th width="70">Receive Date</th>
                    <th width="50">UOM</th>
                    <th>Receive Qty</th>
                </tr>
            </thead>
        </table>
        <div style="width:520px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body_pop" >
        <table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_pop">
        	<tbody>
            <?
			$wo_num_arr=return_library_array( "select id, wo_number from wo_non_order_info_mst",'id','wo_number');
			$pi_num_arr=return_library_array( "select id, pi_number from  com_pi_master_details",'id','pi_number');
			
			$rcv_sql=sql_select("select a.id, a.recv_number, a.receive_basis, a.booking_id, a.receive_date, max(b.cons_uom) as cons_uom, sum(b.cons_quantity) as mrr_qnty 
			from inv_receive_master a, inv_transaction b, product_details_master c
			where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis=$book_basis and a.booking_id=$booking_id and c.color=$color_id and c.yarn_type=$yarn_type and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition and b.pi_wo_req_dtls_id=$pi_wo_req_dtls_id 
			group by a.id, a.recv_number, a.receive_basis, a.booking_id, a.receive_date");
			
			$k=1;$all_rcv_id=array();
			foreach($rcv_sql as $row)
			{
				if ($k%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				$all_rcv_id[$row[csf("id")]]=$row[csf("id")];
				?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                	<td width="30" align="center"><? echo $k; ?></td>
                    <?
					$wo_pi_no="";
					if($row[csf("receive_basis")]==1)
					{
						$wo_pi_no=$pi_num_arr[$row[csf("booking_id")]];
					}
					else
					{
						$wo_pi_no=$wo_num_arr[$row[csf("booking_id")]];
					}
					?>
                    <td width="120"><p><? echo $wo_pi_no; ?></p></td>
                    <td width="130"><p><? echo $row[csf("recv_number")]; ?></p></td>
                    <td align="center" width="70"><p>&nbsp;<? if($row[csf("receive_date")]!="" && $row[csf("receive_date")]!="0000-00-00") echo change_date_format($row[csf("receive_date")]); ?></p></td>
                    <td  align="center" width="50"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("mrr_qnty")],2); $total_mrr_qnty+=$row[csf("mrr_qnty")]; ?></p></td>
                </tr>
                <?
				$k++;
			}
			unset($rcv_sql);
			
			//==============>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> for PI from WO
			?>
            </tbody>
            <tfoot>
            	<tr>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total</th>
                    <th><? echo number_format($total_mrr_qnty,2); ?></th>
                </tr>
            </tfoot>
        </table>
        </div>
        <br/>
        <?
			$all_rcv_ids=implode(",",$all_rcv_id);
			if($all_rcv_ids=="") $all_rcv_ids=0;
			$rcv_return_sql=sql_select("select a.issue_number, a.issue_date, b.cons_uom, sum(b.cons_quantity) as qnty, sum(b.cons_amount) as amt from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=8 and b.transaction_type=3 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.received_id in($all_rcv_ids) group by a.issue_number, a.issue_date, b.cons_uom");
			?>
            <table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_pop">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="130">MRR No.</th>
                        <th width="70">Return Date</th>
                        <th width="50">UOM</th>
                        <th>Return Qty</th>
                    </tr>
                </thead>
                <tbody>
					<?
					$i=1;
					foreach($rcv_return_sql as $row)
					{
						if ($k%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                            <td align="center"><? echo $i;?></td>
                            <td><p><? echo $row[csf("issue_number")]; ?></p></td>
                            <td align="center"><p><? echo change_date_format($row[csf("issue_date")]); ?></p></td>
                            <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                            <td align="right"><? echo number_format($row[csf("qnty")],2); $total_rtn+=$row[csf("qnty")]; ?></td>
                        </tr>
                        <?
						$i++;$k++;
					}
                    ?>
                	
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total Rtn</th>
                        <th><? echo number_format($total_rtn,2); ?></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Balance</th>
                        <th><? $balance_qnty=($total_mrr_qnty-$total_rtn); echo number_format($balance_qnty,2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <script>setFilterGrid("table_body_pop",-1);</script>
    <?
	exit();
}
	disconnect($con);
?>


 