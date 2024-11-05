<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if ($action == "load_drop_down_knitting_com") 
{
	$data = explode("_", $data);
	$company_id = $data[1];
	if ($data[0] == 1) {
		echo create_drop_down("cbo_knitting_company", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", "", "", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_knitting_company", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "");
	} else {
		echo create_drop_down("cbo_knitting_company", 120, $blank_array, "", 1, "--Select Knit Company--", 0, "");
	}
	exit();
}

if ($action=="load_drop_down_buyer")
{
	if($data!=0)
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0  $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0,"" );
	}
	exit();
}

if($action=="textile_report_generate") 
{	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_style_no=trim(str_replace("'","",$txt_style_no));
	$txt_sales_order_no=str_replace("'","",$txt_sales_order_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_sales_order_id= trim(str_replace("'","",$txt_sales_order_id));

	$search_cond="";
	if($cbo_buyer_name>0){$search_cond=" and ((a.buyer_id=$cbo_buyer_name and within_group=2) or (a.po_buyer=$cbo_buyer_name and within_group=1))";}
	if($txt_style_no !=''){$search_cond .=" and a.style_ref_no like '%".$txt_style_no."%'";}
	if($txt_sales_order_no!=''){$search_cond=" and a.job_no like '%$txt_sales_order_no%'";}
	if($txt_sales_order_id!=''){$search_cond=" and a.id = '$txt_sales_order_id'";}

	$sys_date='';
	if($db_type==0)
	{ 
		if ($txt_date_from!="" &&  $txt_date_to!="") $search_cond .= "and a.insert_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; 
	}
	else
	{
		if ($txt_date_from!="" &&  $txt_date_to!=""){
			$search_cond .= "and a.insert_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'";
			$sys_date = change_date_format($txt_date_from, "", "",1) .'To'. change_date_format($txt_date_to, "", "",1); 
		} 
	}
	$com_dtls = fnc_company_location_address($cbo_company_name, "", 1);
	
	ob_start();
	// php start

	?>
	
		<?
			$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
			$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
			$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");

			$con = connect();
			$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (86)");
			if($r_id)
			{
				oci_commit($con);
			}
			
			$fso_sql="SELECT a.id as fso_id, a.job_no as fso_no, a.style_ref_no, a.within_group, a.sales_booking_no, a.booking_without_order, a.booking_id, a.buyer_id, a.po_buyer, b.color_id, b.item_number_id, b.grey_qty, a.booking_entry_form, a.po_job_no, a.entry_form as mst_entry_form
			from fabric_sales_order_mst a, fabric_sales_order_dtls b
			where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name $search_cond";
			$fso_sql_result=sql_select($fso_sql);
			foreach($fso_sql_result as $row)
			{
				if($row[csf("within_group")]==1)
				{
					$buyer_id =$row[csf("po_buyer")];
				}
				else
				{
					$buyer_id =$row[csf("buyer_id")];
				}
				$data_array[$row[csf("fso_id")]][$row[csf("color_id")]]["fso_no"]=$row[csf("fso_no")];
				$data_array[$row[csf("fso_id")]][$row[csf("color_id")]]["buyer_id"]=$buyer_id;
				$data_array[$row[csf("fso_id")]][$row[csf("color_id")]]["color_id"]=$row[csf("color_id")];
				$data_array[$row[csf("fso_id")]][$row[csf("color_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
				$data_array[$row[csf("fso_id")]][$row[csf("color_id")]]["sales_booking_no"]=$row[csf("sales_booking_no")];
				$data_array[$row[csf("fso_id")]][$row[csf("color_id")]]["within_group"]=$row[csf("within_group")];
				$data_array[$row[csf("fso_id")]][$row[csf("color_id")]]["booking_id"]=$row[csf("booking_id")];
				$data_array[$row[csf("fso_id")]][$row[csf("color_id")]]["booking_without_order"]=$row[csf("booking_without_order")];
				$data_array[$row[csf("fso_id")]][$row[csf("color_id")]]["booking_entry_form"]=$row[csf("booking_entry_form")];
				$data_array[$row[csf("fso_id")]][$row[csf("color_id")]]["po_job_no"]=$row[csf("po_job_no")];
				$data_array[$row[csf("fso_id")]][$row[csf("color_id")]]["mst_entry_form"]=$row[csf("mst_entry_form")];
				$data_array[$row[csf("fso_id")]][$row[csf("color_id")]]["item_number_id"] .=$garments_item[$row[csf("item_number_id")]].",";
				$data_array[$row[csf("fso_id")]][$row[csf("color_id")]]["grey_qty"] +=$row[csf("grey_qty")];

				if($row[csf('within_group')] == 1 && $row[csf("booking_without_order")]*1==0)
				{
					$orderBooking[$row[csf("booking_id")]]=$row[csf("booking_id")];
				} 
				else if($row[csf('within_group')] == 1 && $row[csf("booking_without_order")]*1==1) 
				{
					$sampBooking[$row[csf("booking_id")]]=$row[csf("booking_id")];
				}

				$fsoArr[$row[csf("fso_id")]]=$row[csf("fso_id")];
			}

			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 86, 1, $fsoArr,  $empty_arr);//FSO ID
			
			if(!empty($orderBooking))
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 86, 2, $orderBooking,  $empty_arr);//order booking ID

				$po_data_sql = sql_select("SELECT a.booking_no, a.id as booking_id, b.po_break_down_id, d.style_description,
				a.booking_type, a.is_short, a.company_id, a.po_break_down_id, a.item_category, a.fabric_source, a.job_no, a.entry_form, a.is_approved 
				from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d, gbl_temp_engine g where a.booking_no = b.booking_no and b.po_break_down_id=c.id and c.job_id=d.id and b.status_active=1 and b.is_deleted=0 and a.booking_type in (1,4) and a.id=g.ref_val and g.ref_from=2 and g.user_id=$user_id and g.entry_form=86");

				foreach ($po_data_sql as $row)
				{
					$po_data_arr[$row[csf('booking_id')]]['po'] .= $row[csf('po_break_down_id')].",";
					$po_data_arr[$row[csf('booking_id')]]['style_description'].= $row[csf('style_description')].",";

					$booking_type_arr[$row[csf("booking_no")]]=$row[csf("booking_type")];
					$booking_is_short_arr[$row[csf("booking_no")]]=$row[csf("is_short")];

					$booking_Arr[$row[csf('booking_no')]]['booking_company_id'] = $row[csf('company_id')];
					$booking_Arr[$row[csf('booking_no')]]['booking_order_id'] = $row[csf('po_break_down_id')];
					$booking_Arr[$row[csf('booking_no')]]['booking_fabric_natu'] = $row[csf('item_category')];
					$booking_Arr[$row[csf('booking_no')]]['booking_fabric_source'] = $row[csf('fabric_source')];
					$booking_Arr[$row[csf('booking_no')]]['booking_job_no'] = $row[csf('job_no')];
					$booking_Arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
				}
				unset($po_data_sql);
			}

			if(!empty($sampBooking))
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 86, 3, $sampBooking,  $empty_arr);//order booking ID

				$booking_sql="SELECT a.booking_no, a.company_id, a.po_break_down_id, a.item_category, a.fabric_source, a.job_no, a.is_approved, a.is_short, a.booking_type from WO_NON_ORD_SAMP_BOOKING_MST a, gbl_temp_engine g where a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and a.id=g.ref_val and g.ref_from=3 and g.user_id=$user_id and g.entry_form=86";
		
				// echo $booking_sql;die;
				$booking_sql_dataArr = sql_select($booking_sql);
				$non_order_booking_Arr=array();
				foreach($booking_sql_dataArr as $row)
				{
					$non_order_booking_Arr[$row[csf('booking_no')]]['booking_company_id'] = $row[csf('company_id')];
					$non_order_booking_Arr[$row[csf('booking_no')]]['booking_order_id'] = $row[csf('po_break_down_id')];
					$non_order_booking_Arr[$row[csf('booking_no')]]['booking_fabric_natu'] = $row[csf('item_category')];
					$non_order_booking_Arr[$row[csf('booking_no')]]['booking_fabric_source'] = $row[csf('fabric_source')];
					$non_order_booking_Arr[$row[csf('booking_no')]]['booking_job_no'] = $row[csf('job_no')];
					$non_order_booking_Arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
				}
			}

			$yarnIssueData = sql_select("SELECT a.id, a.cons_quantity as qnty, c.color_id, d.po_id 
			from inv_transaction a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_dtls c, ppl_planning_entry_plan_dtls d, gbl_temp_engine g
			where a.requisition_no = b.requisition_no  and a.receive_basis in(3,8) and a.status_active=1 and a.is_deleted=0 and b.knit_id=c.id and c.id=d.dtls_id and a.item_category=1 and a.transaction_type=2 and d.po_id=g.ref_val and g.ref_from=1 and g.user_id=$user_id and g.entry_form=86");

			$yarnIssueTransChk=array();
			foreach ($yarnIssueData as $row)
			{
				if($yarnIssueTransChk[$row[csf('id')]]=="")
				{
					$yarnIssueTransChk[$row[csf('id')]]=$row[csf('id')];
					$yarn_iss_arr[$row[csf('po_id')]][$row[csf('color_id')]]+= $row[csf('qnty')];
				}
			}
			unset($yarnIssueData);

			$yarnIssueRtnData = sql_select("SELECT b.id, d.color_id, e.po_id, b.cons_quantity as qnty
			from inv_receive_master a, inv_transaction b, ppl_yarn_requisition_entry c, ppl_planning_info_entry_dtls d, ppl_planning_entry_plan_dtls e, gbl_temp_engine g
			where a.id = b.mst_id and a.receive_basis in (3) and a.entry_form = 9 and b.item_category = 1 and b.transaction_type = 4 and a.status_active = 1
			and a.is_deleted = 0 
			and a.booking_id=c.requisition_no and c.knit_id=d.id and d.id=e.dtls_id and e.po_id=g.ref_val and g.ref_from=1 and g.user_id=$user_id and g.entry_form=86
			union all
			select b.id, d.color_id, e.po_id, b.cons_quantity as qnty
			from inv_receive_master a, inv_transaction b, ppl_yarn_requisition_entry c, ppl_planning_info_entry_dtls d, ppl_planning_entry_plan_dtls e, gbl_temp_engine g
			where a.id = b.mst_id and a.receive_basis in (8) and a.entry_form = 9 and b.item_category = 1 and b.transaction_type = 4 and a.status_active = 1 and a.is_deleted = 0 
			and a.requisition_no=c.requisition_no and c.knit_id=d.id and d.id=e.dtls_id and e.po_id=g.ref_val and g.ref_from=1 and g.user_id=$user_id and g.entry_form=86");
			$yarnIssueReturnTransChk=array();
			foreach ($yarnIssueRtnData as $row)
			{
				if($yarnIssueReturnTransChk[$row[csf('id')]]=="")
				{
					$yarnIssueReturnTransChk[$row[csf('id')]]=$row[csf('id')];
					$yarn_IssRtn_arr[$row[csf('po_id')]][$row[csf('color_id')]]+= $row[csf('qnty')];
				}
			}
			unset($yarnIssueRtnData);

			$knitting_dataArray = sql_select("SELECT a.po_breakdown_id, b.color_id, a.quantity
			from order_wise_pro_details a, pro_grey_prod_entry_dtls b, gbl_temp_engine c
			where a.dtls_id=b.id and a.entry_form=2 and a.is_sales=1 and a.po_breakdown_id=c.ref_val and c.ref_from=1 and c.entry_form=86 and c.user_id=$user_id");

			foreach ($knitting_dataArray as $row)
			{
				$knitting_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]+= $row[csf('quantity')];
			}
			unset($knitting_dataArray);

			$dyeing_dataArray = sql_select("SELECT a.color_id, b.po_id, SUM(b.batch_qnty) AS batch_qnty
			from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess f, gbl_temp_engine c 
			where  a.id=b.mst_id and f.batch_id=a.id and f.batch_id=b.mst_id and a.is_sales=1 and b.is_sales=1 and a.entry_form=0  and f.entry_form=35 and f.load_unload_id=1 and a.batch_against in(1,2,3) 
			and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
			and f.status_active=1 and f.is_deleted=0 and b.po_id=c.ref_val and c.ref_from=1 and c.entry_form=86 and c.user_id=$user_id
			GROUP BY  a.color_id, b.po_id");

			foreach ($dyeing_dataArray as $row)
			{
				$dyeing_qnty_arr[$row[csf('po_id')]][$row[csf('color_id')]]+= $row[csf('batch_qnty')];
			}
			unset($dyeing_dataArray);
			
			$finish_production_dataArray = sql_select("SELECT a.po_breakdown_id, b.color_id, a.quantity
			from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, gbl_temp_engine c 
			where a.dtls_id=b.id and a.entry_form=66 and a.status_active=1 and b.status_active=1 and a.po_breakdown_id=c.ref_val and c.ref_from=1 and c.entry_form=86 and c.user_id=$user_id");

			foreach ($finish_production_dataArray as $row)
			{
				$fin_production_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]+= $row[csf('quantity')];
			}
			unset($finish_production_dataArray);

			$finish_deli_to_garments_dataArray = sql_select("SELECT a.po_breakdown_id, a.color_id, a.quantity
			from order_wise_pro_details a, inv_finish_fabric_issue_dtls b, gbl_temp_engine c 
			where a.dtls_id=b.id and a.entry_form=318 and a.status_active=1 and b.status_active=1 and a.po_breakdown_id=c.ref_val and c.ref_from=1 and c.entry_form=86 and c.user_id=$user_id");

			foreach ($finish_deli_to_garments_dataArray as $row)
			{
				$fin_deli_garments_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]+= $row[csf('quantity')];
			}
			unset($finish_deli_to_garments_dataArray);


			$issue_to_process_and_receive_sql = sql_select("SELECT a.entry_form, a.po_breakdown_id, a.qnty, b.color_id
			from pro_roll_details a, pro_grey_batch_dtls b, gbl_temp_engine c 
			where a.dtls_id=b.id and a.entry_form in (63,65) and a.status_active=1 and a.is_sales=1 and a.po_breakdown_id=c.ref_val and c.ref_from=1 and c.entry_form=86 and c.user_id=$user_id");

			foreach ($issue_to_process_and_receive_sql as $row)
			{
				if($row[csf('entry_form')]==63){
					$issue_to_process_and_receive_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['issue']+= $row[csf('qnty')];
				}
				else
				{
					$issue_to_process_and_receive_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['rcv']+= $row[csf('qnty')];
				}
			}
			unset($issue_to_process_and_receive_sql);


			$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (86)");
			if($r_id)
			{
				oci_commit($con);
			}
			disconnect($con);


			$format_sql = sql_select("select format_id, template_name, report_id, module_id from lib_report_template where module_id in (2,7) and report_id in (1,2,3,4,35,67) and is_deleted=0 and status_active=1 order by id ");

			foreach ($format_sql as $val) {
				if($val[csf('module_id')]==2){
					$print_report_format_arr[$val[csf('report_id')]][$val[csf('template_name')]] = $val[csf('format_id')];
				}

				if($val[csf('module_id')]==7 && $val[csf('template_name')] == $cbo_company_name && $val[csf('report_id')] == 67)
				{
					$print_sales_order_format = $val[csf('format_id')];
				}
			}

			$print_sales_order_format_arr=explode(",",$print_sales_order_format);
			$fsoFormatId=$print_sales_order_format_arr[0];

		?>
		<style>
			.breakAll{
				word-break:break-all;
				word-wrap: break-word;
			}
			.inline { 
				display: inline-block; 
			}
		</style>
		<fieldset style="width:2500px;">
		<table width="2500">
			<tr>
				<td align="center" width="100%" colspan="29" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="29" class="form_caption" style="font-size:12px;"><? echo   $com_dtls[1]; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="29" class="form_caption" style="font-size:18px;">Textile and Garments Production Report</td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="29" class="form_caption" style="font-size:14px;"><? echo $sys_date; ?></td>
			</tr>
		</table>
		<div> 
			<!-- Program  Info Start -->
			<div class='inline' style="width: 2790px; float:left;">
			<table width="2770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
				<thead>
					<tr>
						<th rowspan="2" width="30">SL</th>
						<th rowspan="2" width="120">Buyer</th>
						<th rowspan="2" width="120">Style Ref.</th>
						<th rowspan="2" width="120">Style Description</th>
						<th rowspan="2" width="120">Item</th>
						<th rowspan="2" width="120">Booking No</th>
						<th rowspan="2" width="120">Textile Ref. No</th>
						<th rowspan="2" width="100">Order</th>
						<th rowspan="2" width="120">Color</th>

						<th colspan="3">Yarn</th>
						<th colspan="3">Kintting</th>
						<th colspan="3">Dyeing</th>
						<th colspan="3">AOP</th>
						<th colspan="3">Finish Fabric</th>
						<th colspan="3">Cutting Fabric Receiving Status</th>
					</tr>
					<tr>
						<th width="100">Req. Yarn</th>
						<th width="100">Issued Yarn</th>
						<th width="100">Yarn Issue Balance</th>

						<th width="100">Req. Knitting</th>
						<th width="100">Knitting Complete</th>
						<th width="100">Knitting Balance</th>

						<th width="100">Req. Dyeing</th>
						<th width="100">Dyeing Complete</th>
						<th width="100">Dyeing Balance</th>

						<th width="100">Send For AOP</th>
						<th width="100">Recvd. From AOP</th>
						<th width="100">AOP Balance</th>

						<th width="100">Req. Finish Fabric</th>
						<th width="100">Finish Fabric Recvd.</th>
						<th width="100">Finish Fabric Balance</th>

						<th width="100">Req. Fabric</th>
						<th width="100">Recvd. Fabric</th>
						<th width="100">Fabric Balance</th>
					</tr>
				</thead>
			</table>
			
			<div style="width:2790px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body6">
			<table width="2770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show6" style="float:left;">
				<tbody>
				<?
				$i=1;$tot_issue_qty=$tot_issue_amount=$tot_recv_qty=$tot_recv_amount=$tot_bal_qty=$tot_bal_amount=0;
				foreach($data_array as $fso_id=>$fso_data)
				{
					$sub_tot_fso_required=$sub_tot_yarn_net_issue=$sub_tot_yarn_requ_balance=$sub_tot_knitting_qnty=$sub_tot_knit_requ_balance=$sub_tot_dyeing_qnty=$sub_tot_dyeing_qnty_balance=$sub_tot_fin_production_qnty=$sub_tot_fin_production_balance=$sub_tot_fin_deli_garmnts_qnty=$sub_tot_fin_deli_garmnts_balance=0;
					foreach($fso_data as $color_id=>$row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$booking_company=0;
						if ($row['within_group']==1 && $row['booking_without_order']==0) 
						{
							$booking_company=$booking_Arr[$row["sales_booking_no"]]['booking_company_id'];
							$booking_order_id=$booking_Arr[$row["sales_booking_no"]]['booking_order_id'];
							$booking_fabric_natu=$booking_Arr[$row["sales_booking_no"]]['booking_fabric_natu'];
							$booking_fabric_source=$booking_Arr[$row["sales_booking_no"]]['booking_fabric_source'];
							$booking_job_no=$booking_Arr[$row["sales_booking_no"]]['booking_job_no'];
							$is_approved_id=$booking_Arr[$row["sales_booking_no"]]['is_approved'];
						}
						elseif ($row['within_group']==1 && $row['booking_without_order']==1) 
						{
							$booking_company=$non_order_booking_Arr[$row["sales_booking_no"]]['booking_company_id'];
							$booking_order_id=$non_order_booking_Arr[$row["sales_booking_no"]]['booking_order_id'];
							$booking_fabric_natu=$non_order_booking_Arr[$row["sales_booking_no"]]['booking_fabric_natu'];
							$booking_fabric_source=$non_order_booking_Arr[$row["sales_booking_no"]]['booking_fabric_source'];
							$booking_job_no=$non_order_booking_Arr[$row["sales_booking_no"]]['booking_job_no'];
							$is_approved_id=$non_order_booking_Arr[$row["sales_booking_no"]]['is_approved'];
						}

						$booking_entry_form = $row['booking_entry_form'];
						$sale_booking_no=$row['sales_booking_no'];
						$sale_booking_no_sm_smn=explode('-', $sale_booking_no);

						$fbReportId=0;

						if($booking_company !=0)
						{
							if ($booking_entry_form==86 || $booking_entry_form==118) 
							{// Budget Wise Fabric Booking and Main Fabric Booking V2
								$print_report_format2 = $print_report_format_arr[1][$booking_company];
								$fReportId2=explode(",",$print_report_format2);
								$fbReportId=$fReportId2[0];
							}
							else if($booking_entry_form==88)
							{
								$print_report_format3 = $print_report_format_arr[2][$booking_company];
								$fReportId3=explode(",",$print_report_format3);
								$fbReportId=$fReportId3[0];
							}
							else if($booking_entry_form==108)
							{
								$print_report_format6 = $print_report_format_arr[35][$booking_company];
								$fReportId6=explode(",",$print_report_format6);
								$fbReportId=$fReportId6[0];
							}
							else if($sale_booking_no_sm_smn[1]=='SM')
							{
								// Sample with order
								$booking_entry_form='SM';

								$print_report_format4 = $print_report_format_arr[3][$booking_company];
								$fReportId4=explode(",",$print_report_format4);
								$fbReportId=$fReportId4[0];

							}
							else if($sale_booking_no_sm_smn[1]=='SMN')
							{
								// Sample without order
								$booking_entry_form='SMN';
								$print_report_format5 = $print_report_format_arr[4][$booking_company];
								$fReportId5=explode(",",$print_report_format5);
								$fbReportId=$fReportId5[0];
							}
						}
						
						$item_number_id = implode(",",array_unique(explode(",",chop($row["item_number_id"],','))));

						if($row['within_group']==1 && $row['booking_without_order']*1 ==0)
						{
							$style_description = implode(',',array_unique(explode(',',chop($po_data_arr[$row['booking_id']]['style_description'],','))));
						}

						$yarn_issue_qnty = $yarn_iss_arr[$fso_id][$color_id];
						$yarn_issue_return_qnty = $yarn_IssRtn_arr[$fso_id][$color_id];
						$yarn_net_issue = $yarn_issue_qnty-$yarn_issue_return_qnty;
						$yarn_requ_balance = $row["grey_qty"]-$yarn_net_issue;

						$knitting_qnty = $knitting_qnty_arr[$fso_id][$color_id];
						$knit_requ_balance = $row["grey_qty"]-$yarn_net_issue;

						$dyeing_qnty =  $dyeing_qnty_arr[$fso_id][$color_id];
						$dyeing_qnty_balance = $row["grey_qty"]-$dyeing_qnty;

						$fin_production_qnty = $fin_production_qnty_arr[$fso_id][$color_id];
						$fin_production_balance = $row["grey_qty"]-$fin_production_qnty;

						$fin_deli_garmnts_qnty = $fin_deli_garments_qnty_arr[$fso_id][$color_id];
						$fin_deli_garmnts_qnty_balance = $row["grey_qty"]-$fin_deli_garmnts_qnty;

						$issue_to_process_qnty = $issue_to_process_and_receive_qnty_arr[$fso_id][$color_id]['issue'];
						$aop_receive_qnty = $issue_to_process_and_receive_qnty_arr[$fso_id][$color_id]['rcv'];
						$aop_balance_qnty = $issue_to_process_qnty-$aop_receive_qnty;

						$sales_order = "<a href='##' style='color:#000' onclick=\"fnc_fabric_sales_order_print('" . $cbo_company_name . "','" . $row['booking_id'] . "','" . $sale_booking_no . "','" . $row['fso_no'] . "','" . $row['mst_entry_form'] . "','" . $fsoFormatId. "','" . $row['within_group'] . "')\"><font style='font-weight:bold' $wo_color>" . $row["fso_no"] . "</font></a>";
						?>						
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
							<td width="30" class='breakAll'><? echo $i; ?></td>
							<td width="120" class='breakAll'><? echo $buyer_library[$buyer_id]; ?></td>
							<td width="120" class='breakAll'><? echo $row["style_ref_no"]; ?></td>
							<td width="120" class='breakAll'><? echo $style_description; ?></td>
							<td width="120" class='breakAll'><? echo $item_number_id; ?></td>

							<td width="120" class='breakAll'><p><? echo "<a href='##' onclick=\"generate_booking_report('".$sale_booking_no."',".$booking_company.",'".$booking_order_id."',".$booking_fabric_natu.",".$booking_fabric_source.",".$is_approved_id.",'".$booking_job_no."','".$booking_entry_form."','".$fbReportId."' )\">$sale_booking_no</a>"; ?>&nbsp;</p></td>

							<td width="120" class='breakAll'><? echo $sales_order;//$row["fso_no"]; ?></td>
							<td width="100" class='breakAll' align="center"><a href='#' onclick="fnc_order_view('<? echo $booking_company;?>','<? echo $row['po_job_no'];?>')" class='view_order' title='Click here to view Order numbers'>view</a></td>
							<td width="120" class='breakAll'><? echo $color_library[$row["color_id"]]; ?></td>

							<td width="100" class='breakAll' align="right"><? echo number_format($row["grey_qty"],2); ?></td>
							<td width="100" class='breakAll' align="right"><? echo number_format($yarn_net_issue,2); ?></td>
							<td width="100" class='breakAll' align="right"><? echo number_format($yarn_requ_balance,2); ?></td>

							<td width="100" class='breakAll' align="right"><? echo number_format($row["grey_qty"],2); ?></td>
							<td width="100" class='breakAll' align="right"><? echo number_format($knitting_qnty,2); ?></td>
							<td width="100" class='breakAll' align="right"><? echo number_format($knit_requ_balance,2); ?></td>
							
							<td width="100" class='breakAll' align="right"><? echo number_format($row["grey_qty"],2); ?></td>
							<td width="100" class='breakAll' align="right"><? echo number_format($dyeing_qnty,2); ?></td>
							<td width="100" class='breakAll' align="right"><? echo number_format($dyeing_qnty_balance,2); ?></td>

							<td width="100" class='breakAll' align="right"><? echo number_format($issue_to_process_qnty,2); ?></td>
							<td width="100" class='breakAll' align="right"><? echo number_format($aop_receive_qnty,2); ?></td>
							<td width="100" class='breakAll' align="right"><? echo number_format($aop_balance_qnty,2); ?></td>

							<td width="100" class='breakAll' align="right"><? echo number_format($row["grey_qty"],2); ?></td>
							<td width="100" class='breakAll' align="right"><? echo number_format($fin_production_qnty,2); ?></td>
							<td width="100" class='breakAll' align="right"><? echo number_format($fin_production_balance,2); ?></td>

							<td width="100" class='breakAll' align="right"><? echo number_format($row["grey_qty"],2); ?></td>
							<td width="100" class='breakAll' align="right"><? echo number_format($fin_deli_garmnts_qnty,2); ?></td>
							<td width="100" class='breakAll' align="right"><? echo number_format($fin_deli_garmnts_balance,2); ?></td>
						</tr>
						<?
						$sub_tot_fso_required +=$row["grey_qty"];
						$sub_tot_yarn_net_issue+=$yarn_net_issue;
						$sub_tot_yarn_requ_balance+=$yarn_requ_balance;
						$sub_tot_knitting_qnty+=$knitting_qnty;
						$sub_tot_knit_requ_balance+=$knit_requ_balance;
						$sub_tot_dyeing_qnty+=$dyeing_qnty;
						$sub_tot_dyeing_qnty_balance+=$dyeing_qnty_balance;
						$sub_tot_issue_to_process_qnty+=$issue_to_process_qnty;
						$sub_tot_aop_receive_qnty+=$aop_receive_qnty;
						$sub_tot_aop_balance_qnty+=$aop_balance_qnty;
						$sub_tot_fin_production_qnty+=$fin_production_qnty;
						$sub_tot_fin_production_balance+=$fin_production_balance;
						$sub_tot_fin_deli_garmnts_qnty+=$fin_deli_garmnts_qnty;
						$sub_tot_fin_deli_garmnts_balance+=$fin_deli_garmnts_balance;
						
						$tot_fso_required +=$row["grey_qty"];
						$tot_yarn_net_issue+=$yarn_net_issue;
						$tot_yarn_requ_balance+=$yarn_requ_balance;
						$tot_knitting_qnty+=$knitting_qnty;
						$tot_knit_requ_balance+=$knit_requ_balance;
						$tot_dyeing_qnty+=$dyeing_qnty;
						$tot_dyeing_qnty_balance+=$dyeing_qnty_balance;
						$tot_issue_to_process_qnty+=$issue_to_process_qnty;
						$tot_aop_receive_qnty+=$aop_receive_qnty;
						$tot_aop_balance_qnty+=$aop_balance_qnty;
						$tot_fin_production_qnty+=$fin_production_qnty;
						$tot_fin_production_balance+=$fin_production_balance;
						$tot_fin_deli_garmnts_qnty+=$fin_deli_garmnts_qnty;
						$tot_fin_deli_garmnts_balance+=$fin_deli_garmnts_balance;
						$i++;
					}
					?>
						<tr style="background-color:#D3D3D3;">
							<td width="30">&nbsp;</td>
							<td width="120">&nbsp;</td>
							<td width="120">&nbsp;</td>
							<td width="120">&nbsp;</td>
							<td width="120">&nbsp;</td>
							<td width="120">&nbsp;</td>
							<td width="120">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="120"><b>Sub Total:</b></td>


							<td width="100" align="right"><strong><? echo number_format($sub_tot_fso_required,2); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($sub_tot_yarn_net_issue,2); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($sub_tot_yarn_requ_balance,2); ?></strong></td>

							<td width="100" align="right"><strong><? echo number_format($sub_tot_fso_required,2); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($sub_tot_knitting_qnty,2); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($sub_tot_knit_requ_balance,2); ?></strong></td>

							<td width="100" align="right"><strong><? echo number_format($sub_tot_fso_required,2); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($sub_tot_dyeing_qnty,2); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($sub_tot_dyeing_qnty_balance,2); ?></strong></td>

							<td width="100" align="right"><strong><? echo number_format($sub_tot_issue_to_process_qnty,2); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($sub_tot_aop_receive_qnty,2); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($sub_tot_aop_balance_qnty,2); ?></strong></td>

							<td width="100" align="right"><strong><? echo number_format($sub_tot_fso_required,2); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($sub_tot_fin_production_qnty,2); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($sub_tot_fin_production_balance,2); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($sub_tot_fso_required,2); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($sub_tot_fin_deli_garmnts_qnty,2); ?></strong></td>
							<td width="100" align="right"><strong><? echo number_format($sub_tot_fin_deli_garmnts_balance,2); ?>
						</tr>

					<?
				}
				?>
				</tbody>
			</table>
			<table width="2770" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
				<tfoot>
					<tr style="background-color:#bebebe;">
						<td width="30">&nbsp;</td>
						<td width="120">&nbsp;</td>
						<td width="120">&nbsp;</td>
						<td width="120">&nbsp;</td>
						<td width="120">&nbsp;</td>
						<td width="120">&nbsp;</td>
						<td width="120">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="120"><b>Grand Total:</b></td>


						<td width="100" align="right"><strong><? echo number_format($tot_fso_required,2); ?></strong></td>
						<td width="100" align="right"><strong><? echo number_format($tot_yarn_net_issue,2); ?></strong></td>
						<td width="100" align="right"><strong><? echo number_format($tot_yarn_requ_balance,2); ?></strong></td>

						<td width="100" align="right"><strong><? echo number_format($tot_fso_required,2); ?></strong></td>
						<td width="100" align="right"><strong><? echo number_format($tot_knitting_qnty,2); ?></strong></td>
						<td width="100" align="right"><strong><? echo number_format($tot_knit_requ_balance,2); ?></strong></td>

						<td width="100" align="right"><strong><? echo number_format($tot_fso_required,2); ?></strong></td>
						<td width="100" align="right"><strong><? echo number_format($tot_dyeing_qnty,2); ?></strong></td>
						<td width="100" align="right"><strong><? echo number_format($tot_dyeing_qnty_balance,2); ?></strong></td>

						<td width="100" align="right"><strong><? echo number_format($tot_issue_to_process_qnty,2); ?></strong></td>
						<td width="100" align="right"><strong><? echo number_format($tot_aop_receive_qnty,2); ?></strong></td>
						<td width="100" align="right"><strong><? echo number_format($tot_aop_balance_qnty,2); ?></strong></td>

						<td width="100" align="right"><strong><? echo number_format($tot_fso_required,2); ?></strong></td>
						<td width="100" align="right"><strong><? echo number_format($tot_fin_production_qnty,2); ?></strong></td>
						<td width="100" align="right"><strong><? echo number_format($tot_fin_production_balance,2); ?></strong></td>
						<td width="100" align="right"><strong><? echo number_format($tot_fso_required,2); ?></strong></td>
						<td width="100" align="right"><strong><? echo number_format($tot_fin_deli_garmnts_qnty,2); ?></strong></td>
						<td width="100" align="right"><strong><? echo number_format($tot_fin_deli_garmnts_balance,2); ?></strong>
					</tr>
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
	echo "$total_data####$filename####$reportType";
	exit();
}

if($action=="garments_report_generate") 
{	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//print_r($_REQUEST); exit();
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_po_company=str_replace("'","",$cbo_po_company);
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_style_no=trim(str_replace("'","",$txt_style_no));
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_date_from= str_replace("'","",$txt_date_from);
	$txt_date_to= str_replace("'","",$txt_date_to);
	$hidden_order_id= trim(str_replace("'","",$hidden_order_id));
	$hidden_job_id= trim(str_replace("'","",$hidden_job_id));

	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $companyArr[0] = "All Company";
    $buyerArr = return_library_array("select id, BUYER_NAME from LIB_BUYER", "id", "BUYER_NAME");
    $colorArr = return_library_array("select id, COLOR_NAME from LIB_COLOR", "id", "COLOR_NAME");
    $com_dtls = fnc_company_location_address($cbo_company_name, "", 1);

	//echo change_date_format($txt_date_from, "", "",1);
	$search_cond="";
	/* if ($txt_date_from!="" &&  $txt_date_to!=""){
		$search_cond .= "and c.PRODUCTION_DATE between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'";
	}  */
	if($cbo_company_name != 0){
		$search_cond .= " and a.company_name=$cbo_company_name";
	}else{
		$search_cond .= " ";
	}
	if($cbo_po_company != 0){
		$search_cond .= " and f.SERVING_COMPANY=$cbo_po_company";
	}
	if($cbo_buyer_name != 0){
		$search_cond .= " and a.buyer_name=$cbo_buyer_name";
	}
	
	
	if($hidden_job_id != ''){
		$search_cond .= " and a.id=$hidden_job_id";
	}else{
		if($txt_style_no !=''){
			$search_cond .= " and a.style_ref_no='$txt_style_no'";
		}
	}
	if($hiddehn_order_id != ''){
		$searc_cond .= " and b.id=$hidden_order_id";
	}else{
		if($txt_order_no != ''){
			$search_cond .= " and b.po_number='$txt_order_no'";
		}
	}
	

	if($txt_date_from != '' and $txt_date_to != '')
	{
		$po_id_arr = array();
		$production_type="1,2,3,4,5,7,8,11";
		$sql = "SELECT po_break_down_id from pro_garments_production_mst
				where PRODUCTION_DATE between '$txt_date_from' and '$txt_date_to' and production_type IN ($production_type) and status_active=1 and is_deleted=0";
		//echo $sql; exit();
		$po_result = sql_select($sql);
		foreach($po_result as $v){
			$po_id_arr[$v['PO_BREAK_DOWN_ID']] = $v['PO_BREAK_DOWN_ID'];
		}

		$sql = "SELECT po_break_down_id from pro_ex_factory_mst
				where ex_factory_date  between '$txt_date_from' and '$txt_date_to' and status_active=1 and is_deleted=0";
		
		$po_result = sql_select($sql);
		foreach($po_result as $v){
			$po_id_arr[$v['PO_BREAK_DOWN_ID']] = $v['PO_BREAK_DOWN_ID'];
		}

		if(count($po_id_arr) > 0){
			$con = connect();
			execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =1 and ENTRY_FORM=86");
			oci_commit($con);
			
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 86, 1, $po_id_arr, $empty_arr);//Po ID
			disconnect($con);
		}
	}

	//echo "<pre>"; print_r($po_id_arr); exit();
	if($txt_date_from != '' and $txt_date_to != '')
	{
		$query = "SELECT a.id as job_id, a.company_name, a.buyer_name, a.style_ref_no, a.style_description, a.GMTS_ITEM_ID, d.plan_cut_qnty,d.id as color_size_id,
			b.id as order_id,b.po_number, b.PUB_SHIPMENT_DATE, d.COLOR_NUMBER_ID
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown d, GBL_TEMP_ENGINE e, pro_garments_production_mst f
			where a.id = b.job_id and d.po_break_down_id = b.id and e.ref_val = b.id and f.po_break_down_id=d.po_break_down_id and e.entry_form=86  and e.user_id=$user_id and e.ref_from=1  $search_cond "; //
	}
	else
	{
		$query = "SELECT a.id as job_id, a.company_name, a.buyer_name, a.style_ref_no, a.style_description, a.GMTS_ITEM_ID, d.plan_cut_qnty,d.id as color_size_id,
			b.id as order_id,b.po_number, b.PUB_SHIPMENT_DATE, d.COLOR_NUMBER_ID
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown d, pro_garments_production_mst f
			where a.id = b.job_id and d.po_break_down_id = b.id and f.po_break_down_id=d.po_break_down_id  $search_cond ";
	}

	
	//echo $query; exit();
	$uniqueColorSizeArray = array();
	$results = sql_select($query);
	$data_array = array();
	$po_id_arr = array();
	foreach ($results as $v) 
	{
		$po_id_arr[$v['ORDER_ID']] = $v['ORDER_ID'];
		$data_array[$v['JOB_ID']][$v['ORDER_ID']][$v['GMTS_ITEM_ID']][$v['COLOR_NUMBER_ID']]['STYLE_REF_NO'] = $v['STYLE_REF_NO'];
		$data_array[$v['JOB_ID']][$v['ORDER_ID']][$v['GMTS_ITEM_ID']][$v['COLOR_NUMBER_ID']]['COMPANY_NAME'] = $v['COMPANY_NAME'];
		$data_array[$v['JOB_ID']][$v['ORDER_ID']][$v['GMTS_ITEM_ID']][$v['COLOR_NUMBER_ID']]['BUYER_NAME'] = $v['BUYER_NAME'];
		$data_array[$v['JOB_ID']][$v['ORDER_ID']][$v['GMTS_ITEM_ID']][$v['COLOR_NUMBER_ID']]['STYLE_DESCRIPTION'] = $v['STYLE_DESCRIPTION'];
		$data_array[$v['JOB_ID']][$v['ORDER_ID']][$v['GMTS_ITEM_ID']][$v['COLOR_NUMBER_ID']]['GMTS_ITEM_ID'] = $v['GMTS_ITEM_ID'];

		if(!in_array($v['COLOR_SIZE_ID'], $uniqueColorSizeArray)){
			$uniqueColorSizeArray[] = $v['COLOR_SIZE_ID'];
			$data_array[$v['JOB_ID']][$v['ORDER_ID']][$v['GMTS_ITEM_ID']][$v['COLOR_NUMBER_ID']]['PLAN_CUT_QNTY'] += $v['PLAN_CUT_QNTY'];
		}
		
		$data_array[$v['JOB_ID']][$v['ORDER_ID']][$v['GMTS_ITEM_ID']][$v['COLOR_NUMBER_ID']]['PO_NUMBER'] = $v['PO_NUMBER'];
		$data_array[$v['JOB_ID']][$v['ORDER_ID']][$v['GMTS_ITEM_ID']][$v['COLOR_NUMBER_ID']]['PUB_SHIPMENT_DATE'] = $v['PUB_SHIPMENT_DATE'];
		$data_array[$v['JOB_ID']][$v['ORDER_ID']][$v['GMTS_ITEM_ID']][$v['COLOR_NUMBER_ID']]['COLOR_NUMBER_ID'] = $v['COLOR_NUMBER_ID'];
	}
	//echo "<pre>"; print_r($color_size_id); exit();
	//between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'
	
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =1 and ENTRY_FORM=86");
	oci_commit($con);
	disconnect($con);

	if(count($po_id_arr) > 0)
	{		
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 86, 1, $po_id_arr, $empty_arr);//Po ID
	}
	

	$date_range = '';
	if($txt_date_from != '' and $txt_date_to != '')
	{
		$between = " and a.production_date  between '$txt_date_from' and '$txt_date_to'";
		$ex_factory_between = " and a.ex_factory_date between '$txt_date_from' and '$txt_date_to'";
		$date_range = change_date_format($txt_date_from, "", "",1) .'To'. change_date_format($txt_date_to, "", "",1);
	}
	else
	{
		$between = " ";
		$ex_factory_between = ' ';
	}

	$production_mst_arr=sql_select("select c.job_id, a.po_break_down_id,c.color_number_id,c.ITEM_NUMBER_ID,b.production_type,a.EMBEL_NAME,
			b.production_qnty,
		   CASE WHEN b.production_type =1 $between THEN b.production_qnty ELSE 0 END AS today_cutting,
		   CASE WHEN b.production_type =1 THEN b.production_qnty ELSE 0 END AS total_cutting,
		   CASE WHEN b.production_type =2  and a.embel_name=1 $between  THEN b.production_qnty ELSE 0 END
		   AS print_issue,
		   CASE WHEN b.production_type =3 and a.embel_name=1 $between  THEN b.production_qnty ELSE 0 END
		   AS print_recv,
		   CASE WHEN b.production_type =2 and a.embel_name=2 $between  THEN b.production_qnty ELSE 0 END
		   AS emb_issue ,
		   CASE WHEN b.production_type =3 and a.embel_name=2 $between  THEN b.production_qnty ELSE 0 END
		   AS emb_recv,
		   CASE WHEN b.production_type =4 $between  THEN b.production_qnty ELSE 0 END AS today_sewing_in,
		   CASE WHEN b.production_type =4  THEN b.production_qnty ELSE 0 END AS total_sewing_in,
		   CASE WHEN b.production_type =5 $between  THEN b.production_qnty ELSE 0 END AS today_sewing_out,
		   CASE WHEN b.production_type =5  THEN b.production_qnty ELSE 0 END AS total_sewing_out,
		   CASE WHEN b.production_type =7 $between  THEN b.production_qnty ELSE 0 END AS today_iron,
		   CASE WHEN b.production_type =7  THEN b.production_qnty ELSE 0 END AS total_iron,
		   CASE WHEN b.production_type =11 $between  THEN b.production_qnty ELSE 0 END AS today_poly,
		   CASE WHEN b.production_type =11  THEN b.production_qnty ELSE 0 END AS total_poly,
		   CASE WHEN b.production_type =8 $between  THEN b.production_qnty ELSE 0 END AS today_finish_recv,
		   CASE WHEN b.production_type =8  THEN b.production_qnty ELSE 0 END AS total_finish_recv

		   from  pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, GBL_TEMP_ENGINE d

		   where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id and d.ref_val = c.po_break_down_id and d.entry_form=86  and d.user_id=$user_id and d.ref_from=1 and b.production_qnty>0
		   and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0");

	//echo $production_mst_arr; exit();
	$gmts_data_arr = array();
	foreach($production_mst_arr as $production){
		//po - item-color - prod type - embel name //po_break_down_id
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['TODAY_CUTTING'] += $production['TODAY_CUTTING'];
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['TOTAL_CUTTING'] += $production['TOTAL_CUTTING'];
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['PRINT_ISSUE'] += $production['PRINT_ISSUE'];
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['PRINT_RECV'] += $production['PRINT_RECV'];
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['EMB_ISSUE'] += $production['EMB_ISSUE'];
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['EMB_RECV'] += $production['EMB_RECV'];
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['TODAY_SEWING_IN'] += $production['TODAY_SEWING_IN'];
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['TOTAL_SEWING_IN'] += $production['TOTAL_SEWING_IN'];
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['TODAY_SEWING_OUT'] += $production['TODAY_SEWING_OUT'];
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['TOTAL_SEWING_OUT'] += $production['TOTAL_SEWING_OUT'];
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['TODAY_IRON'] += $production['TODAY_IRON'];
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['TOTAL_IRON'] += $production['TOTAL_IRON'];
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['TODAY_POLY'] += $production['TODAY_POLY'];
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['TOTAL_POLY'] += $production['TOTAL_POLY'];
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['TODAY_FINISH_RECV'] += $production['TODAY_FINISH_RECV'];
		$gmts_data_arr[$production['JOB_ID']][$production['PO_BREAK_DOWN_ID']][$production['ITEM_NUMBER_ID']][$production['COLOR_NUMBER_ID']][$production['PRODUCTION_TYPE']][$production['EMBEL_NAME']]['TOTAL_FINISH_RECV'] += $production['TOTAL_FINISH_RECV'];
	}

	//Query for shipment data 
	

	$shipmentArr=sql_select("select c.job_id, a.po_break_down_id,c.color_number_id,c.ITEM_NUMBER_ID,

		   CASE WHEN a.entry_form = 85  $ex_factory_between THEN e.PRODUCTION_QNTY ELSE 0 END AS return_qnty,
		   CASE WHEN a.entry_form != 85 $ex_factory_between THEN e.PRODUCTION_QNTY ELSE 0 END AS shipment_qnty,
		   CASE WHEN a.entry_form = 85  THEN e.PRODUCTION_QNTY ELSE 0 END AS total_return_qnty,
		   CASE WHEN a.entry_form != 85 THEN e.PRODUCTION_QNTY ELSE 0 END AS total_shipment_qnty
		   
		   from  pro_ex_factory_mst a, wo_po_color_size_breakdown c, GBL_TEMP_ENGINE d, pro_ex_factory_dtls e

		   where a.po_break_down_id=c.po_break_down_id and d.ref_val = c.po_break_down_id and e.mst_id = a.id and c.id = e.color_size_break_down_id
		   and d.entry_form=86  and d.user_id=$user_id and d.ref_from=1 and a.status_active=1 and a.is_deleted=0 ");
	$shipment_data_arr = array();
	foreach($shipmentArr as $shipment){
		//po - item-color - prod type - embel name //po_break_down_id [$production['JOB_ID']]
		$shipment_data_arr[$shipment['JOB_ID']][$shipment['PO_BREAK_DOWN_ID']][$shipment['ITEM_NUMBER_ID']][$shipment['COLOR_NUMBER_ID']]['RETURN_QNTY'] += $shipment['RETURN_QNTY'];
		$shipment_data_arr[$shipment['JOB_ID']][$shipment['PO_BREAK_DOWN_ID']][$shipment['ITEM_NUMBER_ID']][$shipment['COLOR_NUMBER_ID']]['TOTAL_RETURN_QNTY'] += $shipment['TOTAL_RETURN_QNTY'];
		$shipment_data_arr[$shipment['JOB_ID']][$shipment['PO_BREAK_DOWN_ID']][$shipment['ITEM_NUMBER_ID']][$shipment['COLOR_NUMBER_ID']]['SHIPMENT_QNTY'] += $shipment['SHIPMENT_QNTY'];
		$shipment_data_arr[$shipment['JOB_ID']][$shipment['PO_BREAK_DOWN_ID']][$shipment['ITEM_NUMBER_ID']][$shipment['COLOR_NUMBER_ID']]['TOTAL_SHIPMENT_QNTY'] += $shipment['TOTAL_SHIPMENT_QNTY'];
	}

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =1 and ENTRY_FORM=86");
	oci_commit($con);
	disconnect($con);

	ob_start();
	?>
	<fieldset style="width: 3990px; margin-top: 20px;">
		<style>
			.breakAll{
				word-break:break-all;
				word-wrap: break-word;
			}
			.inline { 
				display: inline-block; 
			}
		</style>

		<table width="2850" >
			<tr>
				<td align="center" width="100%" colspan="29" class="form_caption"><? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="29" class="form_caption" style="font-size:12px;"><? echo   $com_dtls[1]; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="29" class="form_caption" style="font-size:18px;">Garments production report</td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="29" class="form_caption" style="font-size:14px;"><? echo $date_range; ?></td>
			</tr>
		</table>
		
		<div>
			<table align="left" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px; position: sticky; width: 3990px;">
				<thead >
					<tr >
						<th rowspan="2" width="30"><p>SL</p></th>
						<th rowspan="2" width="120"><p>Buyer</p></th>
						<th rowspan="2" width="120"><p>Style Ref.</p></th>
						<th rowspan="2" width="120"><p>Style Description</p></th>
						<th rowspan="2" width="120"><p>Item</p></th>
						<th rowspan="2" width="120"><p>Order</p></th>
						<th rowspan="2" width="120"><p>Ex-factory date</p></th>
						<th rowspan="2" width="120"><p>Color</p></th>
						<th rowspan="2" width="120"><p>Order Qty</p></th>

						<th colspan="3" width="300"><p>Cutting</p></th>
						<th colspan="3" width="300"><p>Print</p></th>
						<th colspan="3" width="300"><p>Emb.</p></th>
						<th colspan="3" width="300"><p>Sewing input</p></th>
						<th colspan="3" width="300"><p>Sewing Output</p></th>
						<th colspan="3" width="300"><p>Iron</p></th>
						<th colspan="3" width="300"><p>Poly</p></th>
						<th colspan="3" width="300"><p>Finishing Recvd</p></th>
						<th colspan="2" width="300"><p>Cartoon</p></th>
						<th colspan="3" width="300"><p>Shipment</p></th>
					</tr>
					<tr>
						<th width="100"><p>Today cutting<p></th>
						<th width="100"><p>Total cutting<p></th>
						<th width="100"><p>Cutting balance<p></th>

						<th width="100"><p>Send for print<p></th>
						<th width="100"><p>Recvd. from print<p></th>
						<th width="100"><p>Recvd. balance<p></th>

						<th width="100"><p>Send for Emb.<p></th>
						<th width="100"><p>Recvd. from emb<p></th>
						<th width="100"><p>Recvd. balance<p></th>

						<th width="100"><p>Today input<p></th>
						<th width="100"><p>Total input<p></th>
						<th width="100"><p>Input balance<p></th>

						<th width="100"><p>Today output<p></th>
						<th width="100"><p>Total output<p></th>
						<th width="100"><p>Output Balance<p></th>

						<th width="100"><p>Today iron<p></th>
						<th width="100"><p>Total iron<p></th>
						<th width="100"><p>Iron balance<p></th>

						<th width="100"><p>Today poly<p></th>
						<th width="100"><p>Total poly<p></th>
						<th width="100"><p>Poly balance<p></th>

						<th width="100"><p>Today recvd<p></th>
						<th width="100"><p>Total recvd<p></th>
						<th width="100"><p>Recvd balance<p></th>

						<th width="150"><p>Today Carton<p></th>
						<th width="150"><p>Total Carton<p></th>

						<th width="100"><p>Today shipment<p></th>
						<th width="100"><p>Total shipment<p></th>
						<th width="100"><p>Shipment balance<p></th>
					</tr>
				</thead>
			</table>
		</div>
		<div style="width: 3990px; overflow-y: scroll; max-height:300px ;"> 
			<table align="left" style="width: 3990px; " cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
				<tbody>
					<?php 
						$i = 1; 
						foreach($data_array as $job_id => $job_data)
						{
							$subtotal_order = 0;

							$subtotal_today_cutting = 0;
							$subtotal_total_cutting = 0;
							$subtotal_cutting_balance = 0 ;

							$subtotal_print_issue = 0;
							$subtotal_print_recv = 0;
							$subtotal_print_balance = 0;

							$subtotal_emb_issue = 0;
							$subtotal_emb_recv = 0;
							$subtotal_emb_balance = 0;

							$subtotal_today_sewing_in = 0;
							$subtotal_total_sewing_in = 0;
							$subtotal_sewing_in_balance = 0;

							$subtotal_today_sewing_out = 0;
							$subtotal_total_sewing_out = 0;
							$subtotal_sewing_out_balance = 0;

							$subtotal_today_iron = 0;
							$subtotal_total_iron = 0;
							$subtotal_iron_balance = 0;

							$subtotal_today_poly = 0;
							$subtotal_total_poly = 0;
							$subtotal_poly_balance = 0;

							$subtotal_today_finish_recv = 0;
							$subtotal_total_finish_recv = 0;
							$subtotal_finish_recv_balance = 0;

							$subtotal_today_shipment = 0;
							$subtotal_total_shipment = 0;
							$subtotal_shipment_balance = 0;
							$subTotalCheck = false;
							foreach($job_data as $order_id=>$order_data)
							{							
								foreach($order_data as $item_id => $item_data)
								{
									foreach($item_data as $color_id => $data)
									{
										$today_cutting 			= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][1][0]['TODAY_CUTTING'];
										$total_cutting 			= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][1][0]['TOTAL_CUTTING'];
										$print_issue 			= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][2][1]['PRINT_ISSUE'];
										$print_recv 			= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][3][1]['PRINT_RECV'];
										$emb_issue 				= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][2][2]['EMB_ISSUE'];
										$emb_recv 				= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][3][2]['EMB_RECV'];
										$today_sewing_in 		= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][4][0]['TODAY_SEWING_IN'];
										$total_sewing_in 		= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][4][0]['TOTAL_SEWING_IN'];
										$today_sewing_out 		= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][5][0]['TODAY_SEWING_OUT'];
										$total_sewing_out 		= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][5][0]['TOTAL_SEWING_OUT'];
										$today_iron 			= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][7][0]['TODAY_IRON'];
										$total_iron 			= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][7][0]['TOTAL_IRON'];
										$today_poly				= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][11][0]['TODAY_POLY'];
										$total_poly				= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][11][0]['TOTAL_POLY'];
										$today_finish_recv 		= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][8][0]['TODAY_FINISH_RECV'];
										$total_finish_recv 		= $gmts_data_arr[$job_id][$order_id][$item_id][$color_id][8][0]['TOTAL_FINISH_RECV'];

										$today_shipment 		= $shipment_data_arr[$job_id][$order_id][$item_id][$color_id]['SHIPMENT_QNTY'] - $shipment_data_arr[$job_id][$order_id][$item_id][$color_id]['RETURN_QNTY'];
										$total_shipment 		= $shipment_data_arr[$job_id][$order_id][$item_id][$color_id]['TOTAL_SHIPMENT_QNTY']- $shipment_data_arr[$job_id][$order_id][$item_id][$color_id]['TOTAL_RETURN_QNTY'];

										if($today_cutting>0 || $today_sewing_out > 0)
										{
											$subTotalCheck = true;
											//balances
											$cutting_balance = $data["PLAN_CUT_QNTY"] - $total_cutting;
											$print_balance = $print_issue - $print_recv;
											$emb_balance = $emb_issue - $emb_recv;
											$sewing_in_balance = $data["PLAN_CUT_QNTY"] - $total_sewing_in;
											$sewing_out_balance = $data["PLAN_CUT_QNTY"] - $total_sewing_out;
											$iron_balance = $data["PLAN_CUT_QNTY"] - $total_iron;
											$poly_balance = $data["PLAN_CUT_QNTY"] - $total_poly;
											$finish_recv_balance = $data["PLAN_CUT_QNTY"] - $total_finish_recv;
											$shipment_balance = $data["PLAN_CUT_QNTY"] - $total_shipment;
											//subtotal 
											$subtotal_order += $data["PLAN_CUT_QNTY"];

											$subtotal_today_cutting += $today_cutting;
											$subtotal_total_cutting += $total_cutting;
											$subtotal_cutting_balance += $cutting_balance;

											$subtotal_print_issue += $print_issue;
											$subtotal_print_recv += $print_recv;
											$subtotal_print_balance += $print_balance;

											$subtotal_emb_issue += $emb_issue;
											$subtotal_emb_recv += $emb_recv;
											$subtotal_emb_balance += $emb_balance;

											$subtotal_today_sewing_in += $today_sewing_in;
											$subtotal_total_sewing_in += $total_sewing_in;
											$subtotal_sewing_in_balance += $sewing_in_balance;

											$subtotal_today_sewing_out += $today_sewing_out;
											$subtotal_total_sewing_out += $total_sewing_out;
											$subtotal_sewing_out_balance += $sewing_out_balance;

											$subtotal_today_iron += $today_iron;
											$subtotal_total_iron += $total_iron;
											$subtotal_iron_balance += $iron_balance;

											$subtotal_today_poly += $today_poly;
											$subtotal_total_poly += $total_poly;
											$subtotal_poly_balance += $poly_balance;

											$subtotal_today_finish_recv += $today_finish_recv;
											$subtotal_total_finish_recv += $total_finish_recv;
											$subtotal_finish_recv_balance += $finish_recv_balance;

											$subtotal_today_shipment += $today_shipment;
											$subtotal_total_shipment += $total_shipment;
											$subtotal_shipment_balance += $shipment_balance;

											//grand totals
											$grandtotal_order += $data["PLAN_CUT_QNTY"];

											$grandtotal_today_cutting += $today_cutting;
											$grandtotal_total_cutting += $total_cutting;
											$grandtotal_cutting_balance += $cutting_balance;

											$grandtotal_print_issue += $print_issue;
											$grandtotal_print_recv += $print_recv;
											$grandtotal_print_balance += $print_balance;

											$grandtotal_emb_issue += $emb_issue;
											$grandtotal_emb_recv += $emb_recv;
											$grandtotal_emb_balance += $emb_balance;

											$grandtotal_today_sewing_in += $today_sewing_in;
											$grandtotal_total_sewing_in += $total_sewing_in;
											$grandtotal_sewing_in_balance += $sewing_in_balance;

											$grandtotal_today_sewing_out += $today_sewing_out;
											$grandtotal_total_sewing_out += $total_sewing_out;
											$grandtotal_sewing_out_balance += $sewing_out_balance;

											$grandtotal_today_iron += $today_iron;
											$grandtotal_total_iron += $total_iron;
											$grandtotal_iron_balance += $iron_balance;

											$grandtotal_today_poly += $today_poly;
											$grandtotal_total_poly += $total_poly;
											$grandtotal_poly_balance += $poly_balance;

											$grandtotal_today_finish_recv += $today_finish_recv;
											$grandtotal_total_finish_recv += $total_finish_recv;
											$grandtotal_finish_recv_balance += $finish_recv_balance;

											$grandtotal_today_shipment += $today_shipment;
											$grandtotal_total_shipment += $total_shipment;
											$grandtotal_shipment_balance += $shipment_balance;

											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>	
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
												<td width="30" class='breakAll'><p><? echo $i; ?></p></td>
												<td width="120" class='breakAll'><p><? echo $buyerArr[$data['BUYER_NAME']]; ?></p></td>
												<td width="120" class='breakAll'><p><? echo $data["STYLE_REF_NO"]; ?></p></td>
												<td width="120" class='breakAll'><p><? echo $data['STYLE_DESCRIPTION']; ?></p></td>
												<td width="120" class='breakAll'><p><? echo $garments_item[$data['GMTS_ITEM_ID']]; ?></p></td>

												<td width="120" class='breakAll'><p><? echo $data['PO_NUMBER']; ?>&nbsp;</p></td>

												<td width="120" class='breakAll'><p><? echo $data["PUB_SHIPMENT_DATE"]; ?></p></td>
												<td width="120" class='breakAll' align="center"><p><? echo $colorArr[$data["COLOR_NUMBER_ID"]]; ?></p></td>
												<td width="120" class='breakAll' align="right"><p><? echo $data["PLAN_CUT_QNTY"]; ?></p></td>

												<td width="100" class='breakAll' align="right"><p><? echo $today_cutting; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $total_cutting; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $cutting_balance; ?></p></td>

												<td width="100" class='breakAll' align="right"><p><? echo $print_issue; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $print_recv; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $print_balance;?></p></td>
												
												<td width="100" class='breakAll' align="right"><p><? echo $emb_issue; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $emb_recv; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $emb_balance; ?></p></td>

												<td width="100" class='breakAll' align="right"><p><? echo $today_sewing_in; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $total_sewing_in; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $sewing_in_balance; ?></p></td>

												<td width="100" class='breakAll' align="right"><p><? echo $today_sewing_out; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $total_sewing_out; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $sewing_out_balance; ?></p></td>
												
												<td width="100" class='breakAll' align="right"><p><? echo $today_iron ; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $total_iron ; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $iron_balance; ?></p></td>

												<td width="100" class='breakAll' align="right"><p><? echo $today_poly ; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $total_poly ; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $poly_balance; ?></p></td>

												<td width="100" class='breakAll' align="right"><p><? echo $today_finish_recv ; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $total_finish_recv ; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $finish_recv_balance; ?></p></td>

												<td width="150" class='breakAll' align="right"><p></p></td>
												<td width="150" class='breakAll' align="right"><p></p></td>

												<td width="100" class='breakAll' align="right"><p><? echo $today_shipment ; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $total_shipment ; ?></p></td>
												<td width="100" class='breakAll' align="right"><p><? echo $shipment_balance; ?></p></td>

											</tr>
											<?php $i += 1; 
										}
									}
								}
							}
							if($subTotalCheck){
								?>
									<tr style="background-color:#D3D3D3;">
										<!-- <td width="30">&nbsp;</td>
										<td width="120">&nbsp;</td>
										<td width="120">&nbsp;</td>
										<td width="120">&nbsp;</td>
										<td width="120">&nbsp;</td>
										<td width="120">&nbsp;</td>
										<td width="120">&nbsp;</td> -->
										<td width="120" colspan='8' align="right"><p><b>Sub Total:</b></p></td>
										<td width="120" align="right"><p><strong><?php echo $subtotal_order; ?></strong></p></td>


										<td width="100" align="right"><p><strong><?php echo $subtotal_today_cutting; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_total_cutting; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_cutting_balance; ?></strong></p></td>

										<td width="100" align="right"><p><strong><?php echo $subtotal_print_issue; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_print_recv; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_print_balance; ?></strong></p></td>

										<td width="100" align="right"><p><strong><?php echo $subtotal_emb_issue; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_emb_recv; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_emb_balance; ?></strong></p></td>

										<td width="100" align="right"><p><strong><?php echo $subtotal_today_sewing_in; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_total_sewing_in; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_sewing_in_balance; ?></strong></p></td>

										<td width="100" align="right"><p><strong><?php echo $subtotal_today_sewing_out; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_total_sewing_out; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_sewing_out_balance; ?></strong></p></td>

										<td width="100" align="right"><p><strong><?php echo $subtotal_today_iron; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_total_iron; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_iron_balance; ?></strong></p></td>

										<td width="100" align="right"><p><strong><?php echo $subtotal_today_poly; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_total_poly; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_poly_balance; ?></strong></p></td>

										<td width="100" align="right"><p><strong><?php echo $subtotal_today_finish_recv; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_total_finish_recv; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_finish_recv_balance; ?></strong></p></td>

										<td width="150" align="right"><p><strong></strong></p></td>
										<td width="150" align="right"><p><strong></strong></p></td>

										<td width="100" align="right"><p><strong><?php echo $subtotal_today_shipment; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_total_shipment; ?></strong></p></td>
										<td width="100" align="right"><p><strong><?php echo $subtotal_shipment_balance; ?></strong></p></td>


									</tr>
								<?php
							}
						}
						 
					?>
					
				</tbody>
			</table>
		</div>
		<!-- </div> -->
		<table align="left" style="width: 3990px;" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
			<tfoot>
				

				<tr style="background-color:#D3D3D3;">
					<td width="30">&nbsp;</td>
					<td width="120">&nbsp;</td>
					<td width="120">&nbsp;</td>
					<td width="120">&nbsp;</td>
					<td width="120">&nbsp;</td>
					<td width="120">&nbsp;</td>
					<td width="120">&nbsp;</td>
					<td width="120" align="right"><p><b>Grand Total:</b></p></td>
					<td width="120" align="right"><p><strong><?php echo $grandtotal_order; ?></strong></p></td>


					<td width="100" align="right"><p><strong><?php echo $grandtotal_today_cutting; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_total_cutting; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_cutting_balance; ?></strong></p></td>

					<td width="100" align="right"><p><strong><?php echo $grandtotal_print_issue; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_print_recv; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_print_balance; ?></strong></p></td>

					<td width="100" align="right"><p><strong><?php echo $grandtotal_emb_issue; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_emb_recv; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_emb_balance; ?></strong></p></td>

					<td width="100" align="right"><p><strong><?php echo $grandtotal_today_sewing_in; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_total_sewing_in; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_sewing_in_balance; ?></strong></p></td>

					<td width="100" align="right"><p><strong><?php echo $grandtotal_today_sewing_out; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_total_sewing_out; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_sewing_out_balance; ?></strong></p></td>

					<td width="100" align="right"><p><strong><?php echo $grandtotal_today_iron; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_total_iron; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_iron_balance; ?></strong></p></td>

					<td width="100" align="right"><p><strong><?php echo $grandtotal_today_poly; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_total_poly; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_poly_balance; ?></strong></p></td>

					<td width="100" align="right"><p><strong><?php echo $grandtotal_today_finish_recv; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_total_finish_recv; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_finish_recv_balance; ?></strong></p></td>

					<td width="150" align="right"><p><strong></strong></p></td>
					<td width="150" align="right"><p><strong></strong></p></td>

					<td width="100" align="right"><p><strong><?php echo $grandtotal_today_shipment; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_total_shipment; ?></strong></p></td>
					<td width="100" align="right"><p><strong><?php echo $grandtotal_shipment_balance; ?></strong></p></td>


				</tr>
			</tfoot>
		</table>
	<fieldset>
	<?
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	exit();   
}

if ($action == "fso_no_popup") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(booking_data) {
			document.getElementById('hidden_booking_data').value = booking_data;
			parent.emailwindow.hide();
		}

	</script>
</head>
<body>
	<div align="center">
		<fieldset style="width:830px;margin-left:4px;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table cellpadding="0" cellspacing="0" width="600" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Company</th>
						<th>Buyer</th>
						<th>Sales Order</th>
						<th>Booking</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
							class="formbutton"/>
							<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
						</th>
					</thead>
					<tr class="general">
						<td align="center"> 
							<?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $cbo_company_id, "",'1' );
                            ?>
                        </td>
                        <td align="center">
                        	 <? 
								if($buyer>0) $buy_cond=" and a.id=$cbo_buyer_name";
								echo create_drop_down( "cbo_buyer_name", 140, "select a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 $buy_cond order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $cbo_buyer_name, "",0,"" );
							?>
                        </td>  
						<td align="center" >
							<input type="text" style="width:120px" class="text_boxes" name="txt_sales_order" id="txt_sales_order"/>
						</td>
						<td align="center">
							<input type="text" style="width:120px" class="text_boxes" name="txt_booking" id="txt_booking"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sales_order').value + '_' + document.getElementById('txt_booking').value, 'create_fso_search_list_view', 'search_div', 'textile_and_garments_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;"/>
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"></div>
			</form>
		</fieldset>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_fso_search_list_view") {
	$data = explode('_', $data);

	$company_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

	$company_id = trim($data[0]);
	$cbo_buyer_name = $data[1];
	$txt_sales_order = trim($data[2]);
	$txt_booking = trim($data[3]);
	
	$search_cond="";
	$search_cond .=" and c.company_id='$company_id'";
	if ($txt_sales_order!=''){
		$search_cond .=" and c.job_no like '%$txt_sales_order%'";
	}

	if ($txt_booking!=''){
		$search_cond.=" and c.sales_booking_no like '%$txt_booking%'";
	}

	if($cbo_buyer_name){
		$search_cond .= " and ( (c.po_buyer=".$cbo_buyer_name." and c.within_group=1) or (c.po_buyer=".$cbo_buyer_name." and c.within_group=2)) ";
	}
	
	
	$sql= "select  c.id,c.company_id, c.buyer_id, c.po_buyer, c.job_no as fso_number, c.sales_booking_no, c.within_group  from 
	fabric_sales_order_mst c where c.is_deleted=0 and c.status_active=1 $search_cond order by c.id desc"; 

	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="500" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="100">Company</th>
			<th width="120">Buyer</th>
			<th width="120">Sales Order No</th>
			<th width="130">Sales/ Booking No</th>
		</thead>
	</table>
	<div style="width:520px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="500" class="rpt_table" id="tbl_list_search">
		<?
		$i = 1;
		if(!empty($result)){
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				if ($row[csf('within_group')] == 1)
					$buyer = $buyer_arr[$row[csf('po_buyer')]];
				else
					$buyer = $buyer_arr[$row[csf('buyer_id')]];
				
				$batch_nos=chop($row[csf('batch_no')],',');
				$batch_nos=implode(",",array_unique(explode(",",$batch_nos)));

				$booking_data = $row[csf('fso_number')] . "**" . $row[csf('id')] ;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value('<? echo $booking_data; ?>');">
					<td width="30"><? echo $i; ?></td>
					<td width="100"><p><? echo $company_arr[$row[csf('company_id')]]; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $buyer; ?>&nbsp;</p></td>
					<td width="120"><p>&nbsp;<? echo $row[csf('fso_number')]; ?></p></td>
					<td width="130"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
				</tr>
				<?
				$i++;
			}
		}else{

		}
		?>
	</table>
</div>
<?
exit();
}

if($action=="job_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_report_type;die;
	?>
	
	<script>
    function js_set_value(id)
    {
		//alert(id);
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
    }
	function fnc_show()
	{
		if($("#txt_search_common").val().trim()=="" && $("#cbo_buyer_name").val()==0)
		{
			if(form_validation('txt_search_common','Search')==false)
			{
				return;
			}
		}
		show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cboYearSelection').value+'**'+document.getElementById('cbo_report_type').value, 'job_popup_search_list_view', 'search_div', 'textile_and_garments_production_report_controller', 'setFilterGrid(\'table_body2\',-1)');
	}

    </script>
    </head>
    <body>
    <div align="center" style="width:820px;">
        <form name="styleRef_form" id="styleRef_form">
			<input id="cbo_report_type" name="cbo_report_type" type="hidden" value="<?=$cbo_report_type;?>"/>
		<fieldset style="width:800px;">
            <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Company</th>
                    <th>Buyer</th>
					<th>Year</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" id="selected_id" name="selected_id" />
                </thead>
                <tbody>
                	<tr class="general">
                    	<td align="center"> 
							<?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, "",'1' );
                            ?>
                        </td>
                        <td align="center">
                        	 <? 
								if($buyer>0) $buy_cond=" and a.id=$buyer";
								echo create_drop_down( "cbo_buyer_name", 140, "select a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 $buy_cond order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
							?>
                        </td>  
						<td align="center"><? echo create_drop_down( "cboYearSelection", 80, $year,"", 0, "-- All --", date('Y'), "",0 ); ?></td>               
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="fnc_show();" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if ($action=="job_popup_search_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_type,$search_value,$cbo_year, $cbo_report_type)=explode('**',$data);
	if($cbo_report_type != 2){
		if($company_id==0)
		{
			echo "Please Select Company Name";
			die;
		}
	}
	
	//echo $company_id."==".$buyer_id."==".$search_type."==".$search_value."==".$cbo_year;die;
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}
	
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	else $year_cond="";
	
	if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY')";
	} 
	else if($db_type==0) 
	{
		$year_field="YEAR(a.insert_date)";
	}
	if($company_id != 0){
		$company_cond = " and a.company_name=$company_id ";
	}else{
		$company_cond = " ";
	}
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$company_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');

	$sql= "SELECT a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year
	from wo_po_details_master a,  wo_po_break_down b 
	where a.job_no=b.job_no_mst and b.status_active in(1,2,3) $company_cond $buyer_cond $year_cond $search_con 
	group by a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.insert_date
	order by a.id";
	//echo $sql;//die;
	$rows=sql_select($sql);
	?>
    <table width="800" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="120">Company</th>
                <th width="120">Buyer</th>
                <th width="50">Year</th>
                <th width="120">Job no</th>
                <th width="120">Style</th>
            </tr>
       </thead>
    </table>
    <div style="max-height:820px; overflow:auto;">
    <table id="table_body2" width="800" border="1" rules="all" class="rpt_table">
     <? $rows=sql_select($sql);
         $i=1;
         foreach($rows as $data)
         {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$po_num=implode(",",array_unique(explode(",",$data[csf('PO_NUMBER')])));
			?>
			<tr bgcolor="<? echo  $bgcolor;?>" onClick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('style_ref_no')]; ?>')" style="cursor:pointer;">
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="120"><p><? echo $company_arr[$data[csf('company_name')]]; ?></p></td>
                <td width="120"><p><? echo $buyer_arr[$data[csf('buyer_name')]]; ?></p></td>
                <td align="center" width="50"><p><? echo $data[csf('year')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('job_no')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
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

//order wise browse------------------------------//
if($action=="order_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
		}
    </script>
	<?
	extract($_REQUEST);
	//print_r($_REQUEST);die;
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$job_cond='';
	if(str_replace("'","",$job_id)!="")  $job_cond="and b.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)!="") $job_cond="and a.job_no_mst='".$job_no."'";
	else if($cbo_year!=0)
	{
		if($db_type==0) $job_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
		if($db_type==2) $job_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
	}

	if($db_type==0)
	{
		$insert_year= "year(b.insert_date)as year";
	}
	else if($db_type==2)
	{
		$insert_year= "TO_CHAR(b.insert_date,'YYYY') as year";
	}
	
	$sql = "SELECT distinct a.id,a.PO_NUMBER,b.style_ref_no,b.job_no_prefix_num,$insert_year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active in(1,2,3)   $job_cond  $buyer_name $style_cond";
	//echo $sql; die;
	echo create_list_view("list_view", "Year,Job No,Style Ref,Order Number","50,100,120,150,","550","310",0, $sql , "js_set_value", "id,PO_NUMBER", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,PO_NUMBER", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

if ($action == "order_popup") {
	echo load_html_head_contents("Order Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	$po_info = sql_select("select b.PO_NUMBER, b.pub_shipment_date, b.po_quantity from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.job_no='$job_no' and a.company_name=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<thead>
			<th width="30">SL</th>
			<th width="115">PO Number</th>
			<th width="75">PO Quantity</th>
			<th width="60">Shipment Date</th>
		</thead>
		<tbody>
			<?php
			$i = 1;
			foreach ($po_info as $row) {
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="100" align="center"><? echo $row[csf('PO_NUMBER')]; ?></td>
					<td width="100" align="center"><? echo $row[csf('po_quantity')]; ?></td>
					<td width="100" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
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
?>
