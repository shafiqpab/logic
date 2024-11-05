<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');
$user_name = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 145, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}

if ($action=="load_drop_down_team_member")
{
	echo create_drop_down( "cbo_dealing_merchant", 145, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select --", $selected, "" );
}

//--------------------------------------------------------------------------------------------------------------------
function page_style()
{
	?>
	<style type="text/css">	
		
		table.rpt_table tr th, table.rpt_table tr td
		{
			word-break: break-all;
			word-wrap: break-word;
		}
	</style>
	<?
}

if ($action == "report_generate") 
{
	page_style();
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	//==================================== GETTING FORM VALUE =================================
	$company_name = str_replace("'", "", $cbo_company_name);
	$buyer_name = str_replace("'", "", trim($cbo_buyer_name));
	$team_leader = str_replace("'", "", $cbo_team_leader);
	$dealing_marchant = str_replace("'", "", $cbo_dealing_merchant);
	$booking_no = str_replace("'", "", $txt_booking_no);
	$booking_id = str_replace("'", "", $hide_booking_id);
	$start_date = str_replace("'", "", trim($txt_date_from));
	$end_date = str_replace("'", "", trim($txt_date_to));
	$cbo_year = str_replace("'", "", trim($cbo_year));
	$hilight_bg = "";

	//==================================== LOAD LIBRARY =================================
	$company_library=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$sample_type_library=return_library_array( "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name",'id','sample_name');
	$teamleader_library=return_library_array( "select id,team_leader_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_leader_name","id","team_leader_name");
	$merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id","team_member_name");
	$construction_library=return_library_array( "select id,construction from lib_yarn_count_determina_mst where status_active =1 and is_deleted=0 order by construction","id","construction");

	$composition_library = return_library_array("SELECT a.id, c.composition_name from lib_yarn_count_determina_mst a,lib_yarn_count_determina_dtls b, lib_composition_array c where a.id=b.mst_id and c.id=b.copmposition_id and a.status_active=1 and b.status_active=1 and c.status_active=1","id","composition_name");
	// print_r($composition_library);

	//==================================== MAKING QUERY CONDITION =================================

	$buyer_name_cond 	= ($buyer_name !=0) ? " and a.buyer_id in($buyer_name)" : "";
	$team_leader_cond 	= ($team_leader !=0) ? " and a.team_leader in($team_leader)" : "";
	$marchant_cond 		= ($dealing_marchant !=0) ? " and a.dealing_marchant in($dealing_marchant)" : "";
	$booking_no_cond 	= ($booking_no !=0) ? " and a.booking_no like '%$booking_no%'" : "";
	$booking_id_cond 	= ($booking_id !=0) ? " and a.id in($booking_id)" : "";

	if($cbo_year)
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else if($db_type==2)
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
		else
		{
			
		}
	}
	else
	{
		$year_cond="";
	}

	if ($end_date == "") 
	{
		$end_date = $start_date;
	} 
	else 
	{
		$end_date = $end_date;
	}

	if ($start_date != "" && $end_date != "") 
	{
		if ($db_type == 0) {
			$date_cond = " and a.booking_date between '" . $start_date . "' and '" . $end_date . "'";
		} else {
			$date_cond = " and a.booking_date between '" . $start_date . "' and '" . $end_date . "'";
		}
	} 
	else 
	{
		$date_cond = "";
	}
	// ================================== MAIN QUERY ======================================
	$sql="SELECT a.item_category,a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.sample_type, b.finish_fabric as finish_fabric_qty, b.fabric_description as construction, b.composition, a.booking_date,a.team_leader,a.dealing_marchant,b.remarks,b.gsm_weight as gsm,b.lib_yarn_count_deter_id as construction_id,a.is_approved
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.booking_type in(4) and a.company_id=$company_name $buyer_cond $marchant_cond $team_leader_cond $booking_no_cond $booking_id_cond $year_cond $date_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.booking_no";
	 //echo $sql;		
	$sql_res = sql_select($sql);
	$data_array = array();
	$booking_id_array = array();
	foreach ($sql_res as $val) 
	{
		$data_array[$val[csf('booking_id')]][$val[csf('construction_id')]][$val[csf('gsm')]]['booking_date'] 	= $val[csf('booking_date')];
		$data_array[$val[csf('booking_id')]][$val[csf('construction_id')]][$val[csf('gsm')]]['booking_no'] 		= $val[csf('booking_no')];
		$data_array[$val[csf('booking_id')]][$val[csf('construction_id')]][$val[csf('gsm')]]['booking_num'] 	= $val[csf('booking_no_prefix_num')];
		$data_array[$val[csf('booking_id')]][$val[csf('construction_id')]][$val[csf('gsm')]]['buyer_id'] 		= $val[csf('buyer_id')];
		$data_array[$val[csf('booking_id')]][$val[csf('construction_id')]][$val[csf('gsm')]]['team_leader'] 	= $val[csf('team_leader')];
		$data_array[$val[csf('booking_id')]][$val[csf('construction_id')]][$val[csf('gsm')]]['marchant']		= $val[csf('dealing_marchant')];
		$data_array[$val[csf('booking_id')]][$val[csf('construction_id')]][$val[csf('gsm')]]['remarks'] 		= $val[csf('remarks')];
		$data_array[$val[csf('booking_id')]][$val[csf('construction_id')]][$val[csf('gsm')]]['sample_type'] 	= $val[csf('sample_type')];
		$data_array[$val[csf('booking_id')]][$val[csf('construction_id')]][$val[csf('gsm')]]['composition'] 	= $val[csf('composition')];
		$data_array[$val[csf('booking_id')]][$val[csf('construction_id')]][$val[csf('gsm')]]['is_approved'] 	= $val[csf('is_approved')];
		$data_array[$val[csf('booking_id')]][$val[csf('construction_id')]][$val[csf('gsm')]]['finish_fabric_qty'] += $val[csf('finish_fabric_qty')];
		$booking_id_array[$val[csf('booking_id')]] = $val[csf('booking_id')];
		
		$item_category_id_array[$val[csf('booking_no')]] = $val[csf('item_category')];
	
		
		
	}
	// echo "<pre>";
	// print_r($data_array);
	// echo "</pre>";
	$bookingIds = implode(",", $booking_id_array);
	// ============================== FIN. FAB. RCV. =======================================
	$sql_fin_fab_rcv="SELECT d.booking_no_id as booking_id,b.gsm,b.fabric_description_id as construction_id, (sum(case when a.entry_form=37 then b.receive_qnty else 0 end) - sum(case when a.entry_form=52 then b.receive_qnty else 0 end)) as qnty from inv_receive_master a,pro_finish_fabric_rcv_dtls b,product_details_master c, pro_batch_create_mst d where a.id=b.mst_id and c.id=b.prod_id and d.id=b.batch_id  and d.booking_no_id in($bookingIds) and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 group by d.booking_no_id,b.gsm,b.fabric_description_id";//and a.receive_basis=9
	$sql_fin_fab_rcv_res = sql_select($sql_fin_fab_rcv);
	$fin_fab_rcv_array = array();
	foreach ($sql_fin_fab_rcv_res as $val) 
	{
		// $composition = $composition_library[$val[csf('construction_id')]];
		$fin_fab_rcv_array[$val[csf('booking_id')]][$val[csf('construction_id')]][$val[csf('gsm')]] = $val[csf('qnty')];//[$composition]
	}
	// echo "<pre>";
	// print_r($fin_fab_rcv_array);
	// echo "</pre>";

	// ============================== FIN. FAB. ISSUE. =======================================
	$sql_fin_fab_issue="SELECT d.booking_no_id as booking_id,c.gsm,c.detarmination_id as construction_id,c.gsm, (sum(case when a.entry_form=18 then b.issue_qnty else 0 end) - sum(case when a.entry_form=46 then b.issue_qnty else 0 end)) as qnty from inv_issue_master a,inv_finish_fabric_issue_dtls b,product_details_master c, pro_batch_create_mst d where a.id=b.mst_id and c.id=b.prod_id and d.id=b.batch_id and d.booking_no_id in($bookingIds) and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 group by d.booking_no_id,c.gsm,c.detarmination_id";//and a.issue_purpose=8 
	//echo $sql_fin_fab_issue;die;
	$sql_fin_fab_issue_res = sql_select($sql_fin_fab_issue);
	$fin_fab_issue_array = array();
	foreach ($sql_fin_fab_issue_res as $val) 
	{
		// $composition = $composition_library[$val[csf('construction_id')]];
		$fin_fab_issue_array[$val[csf('booking_id')]][$val[csf('construction_id')]][$val[csf('gsm')]] = $val[csf('qnty')];//[$composition]
	}
	// echo "<pre>";
	// print_r($fin_fab_issue_array);
	// echo "</pre>";
	// ============================== FIN. FAB. ISSUE RETURN REMARKS =======================================
	$sql_remarks="SELECT d.booking_no_id as booking_id,b.gsm,b.fabric_description_id as construction_id,b.remarks from inv_receive_master a,pro_finish_fabric_rcv_dtls b,product_details_master c, pro_batch_create_mst d where a.id=b.mst_id and c.id=b.prod_id and d.id=b.batch_id and d.booking_no_id in($bookingIds) and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and a.entry_form=52";
	$sql_remarks_res = sql_select($sql_remarks);
	$remarks_array = array();
	foreach ($sql_remarks_res as $val) 
	{
		// $composition = $composition_library[$val[csf('construction_id')]];
		$remarks_array[$val[csf('booking_id')]][$val[csf('construction_id')]][$val[csf('gsm')]] = $val[csf('remarks')];//[$composition]
	}
	
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='$company_name' and module_id=2 and report_id=4 and is_deleted=0 and status_active=1");
	$buttonIdArr=explode(',',$print_report_format);
	
	if($buttonIdArr[0]==34){$print_type=1;}
	else if($buttonIdArr[0]==35){ $print_type=2;}
	else if($buttonIdArr[0]==36){ $print_type=3;}
	else if($buttonIdArr[0]==37){ $print_type=4;}
	else if($buttonIdArr[0]==64){ $print_type=5;}
	else if($buttonIdArr[0]==72){ $print_type=6;}
	else if($buttonIdArr[0]==174){ $print_type=7;}
	
	
	
	
	ob_start();
	?>

	<fieldset width="1640">
		<table cellpadding="5" cellspacing="0" width="1640" style="padding: 10px 0">
			<tr>
				<td width="100%" colspan="15" style="font-size:22px; text-align: center !important; font-weight: bold;">
					Buyer Wise Sample Fabric Booking Report
				</td>
			</tr>
			<tr>
				<td width="100%" colspan="15" style="font-size:16px; text-align: center !important;font-weight: bold;">
					<?php echo $company_library[$company_name]; ?>
				</td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="15" style="font-size:16px; text-align: center !important;font-weight: bold;">
					<? if ($start_date != "" && $end_date != "") echo "From " . change_date_format($start_date) . " To " . change_date_format($end_date); ?>
				</td>
			</tr>
		</table>
		<!-- ================================== HEADING PART START =============================== -->
		<div>	
	    	<table border=1 rules='all' class="rpt_table" cellspacing="0" cellpadding="0" width="1620" align="left">
	    		<thead>
	    			<tr>
	    				<th width="30">S.L</th>
	    				<th width="80">Booking Date</th>
	    				<th width="130">Booking No.</th>
	    				<th width="130">Buyer</th>
	    				<th width="130">Team Leader</th>
	    				<th width="130">Merchandiser</th>
	    				<th width="120">Sample Type</th>
	    				<th width="155">Construction</th>
	    				<th width="155">Composition</th>
	    				<th width="60">GSM</th>
	    				<th width="100">Booking Finish Qty (Kg)</th>
	    				<th width="100">Actual Finish Fabric Rcv Qty(Kg)</th>
	    				<th width="100">Used Qty (Kg. Finish)</th>
	    				<th width="100">Balanced Qty (Kg.)</th>
	    				<th width="130">Remarks</th>
	    			</tr>
	    		</thead>
	    	</table>
    	</div>
    	<!-- ===================================== BODY PART START ============================== -->
    	<div style="width: 1640px; max-height:345px; overflow-y:auto; float:left;" >
    		<table border=1 rules='all' class="rpt_table" cellspacing="0" cellpadding="0" width="1620" align="left" id="scroll_body">
	    		<thead>
	    			<?
	    			$sl=1;
	    			$fab_booking_qty 	= 0;
	    			$fab_rcv_qty 		= 0;
	    			$fab_issue_qty		= 0;
	    			$fab_balance 		= 0;
	    			foreach ($data_array as $booking_id => $construction_data) 
	    			{
    					foreach ($construction_data as $construction => $gsm_data) 
    					{	
							foreach ($gsm_data as $gsm => $val) 
							{
								$rcv_qty 	= $fin_fab_rcv_array[$booking_id][$construction][$gsm];
								$issue_qty 	= $fin_fab_issue_array[$booking_id][$construction][$gsm];
								$remarks 	= $remarks_array[$booking_id][$construction][$gsm];
								$balance 	= $rcv_qty - $issue_qty;
				    			?>
				    			<tr>
				    				<td align="center" width="30"><? echo $sl; ?></td>
				    				<td align="center" width="80"><? echo change_date_format($val['booking_date']); ?></td>
				    				<td align="left" width="130">
				    					<a href="##" onClick="generate_order_report(<?= $print_type; ?>,'<?= $val['booking_no'];?>',<?= $company_name; ?>,<?= $val['is_approved'];?>,'<?= $item_category_id_array[$val['booking_no']];?>');">
				    						<?= $val['booking_num']; ?>
				    					</a>				    						
				    				</td>
				    				<td align="left" width="130"><? echo $buyer_library[$val['buyer_id']]; ?></td>
				    				<td align="left" width="130"><? echo $teamleader_library[$val['team_leader']]; ?></td>
				    				<td align="left" width="130"><? echo $merchant_library[$val['marchant']]; ?></td>
				    				<td align="left" width="120"><? echo $sample_type_library[$val['sample_type']]; ?></td>
				    				<td align="left" width="155"><? echo $construction_library[$construction]; ?></td>
				    				<td align="left" width="155"><? echo $val['composition']; ?></td>
				    				<td align="center" width="60"><? echo $gsm; ?></td>
				    				<td align="right" width="100"><? echo $val['finish_fabric_qty']; ?></td>
				    				<td align="right" width="100"><? echo $rcv_qty; ?></td>
				    				<td align="right" width="100"><? echo $issue_qty; ?></td>
				    				<td align="right" width="100"><? echo $balance; ?></td>
				    				<td align="left" width="130"><? echo $remarks; ?></td>
				    			</tr>
				    			<?
				    			$sl++;
				    			$fab_booking_qty 	+= $val['finish_fabric_qty'];
				    			$fab_rcv_qty 		+= $rcv_qty;
				    			$fab_issue_qty		+= $issue_qty;
				    			$fab_balance 		+= $balance;
				    		}
					    	
					    }
						
		    		}
		    		?>
	    		</thead>
	    	</table>
    	</div>
    	<!-- ================================= FOOTER PART START ================================= -->
    	<div>
	    	<table border=1 rules='all' class="rpt_table" cellspacing="0" cellpadding="0" width="1620" align="left">
	    		<tfoot>
	    			<tr>
	    				<th width="30">.</th>
	    				<th width="80"></th>
	    				<th width="130"></th>
	    				<th width="130"></th>
	    				<th width="130"></th>
	    				<th width="130"></th>
	    				<th width="120"></th>
	    				<th width="155"></th>
	    				<th width="155" align="right">Total </th>
	    				<th width="60"></th>
	    				<th width="100" align="right"><? echo $fab_booking_qty;?></th>
	    				<th width="100" align="right"><? echo $fab_rcv_qty;?></th>
	    				<th width="100" align="right"><? echo $fab_issue_qty;?></th>
	    				<th width="100" align="right"><? echo $fab_balance;?></th>
	    				<th width="130"></th>
	    			</tr>
	    		</tfoot>
	    	</table>
	    </div>
    </fieldset>

    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
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

//========================================

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
							<th>Po Buyer</th>
							<th>Booking Date</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="150">Please Enter Booking No</th>
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
							<td align="center">
								<?
								$search_by_arr = array(1 => "Booking No", 2 => "Job No");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value, 'create_booking_search_list_view', 'search_div', 'buyer_wise_sample_fabric_booking_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
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

if ($action == "create_booking_search_list_view") 
{
	$data = explode("_", $data);

	$search_string = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$buyer_id = $data[3];
	$date_from = trim($data[4]);
	$date_to = trim($data[5]);
	$cbo_within_group = trim($data[6]);


	if ($date_from != "" && $date_to != "") 
	{
		if ($db_type == 0) {
			$date_cond = "and booking_date between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and booking_date between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");

	$search_field_cond = "";
	if ($search_by == 1) {
		$search_field_cond .= " and sales_booking_no like '%$search_string%'";
	}else{
		$search_field_cond .= " and po_job_no like '%$search_string%'";
	}
	if ($buyer_id != 0) {
		$search_field_cond .= " and po_buyer=$buyer_id";
	}
	if ($cbo_within_group > 0) {
		$search_field_cond .= " and within_group=$cbo_within_group";
	}
	$sql = "select id, sales_booking_no booking_no, booking_date,buyer_id, company_id,job_no, style_ref_no,po_job_no from fabric_sales_order_mst where company_id= $company_id and status_active =1 and is_deleted=0 $search_field_cond $date_cond group by id, sales_booking_no, booking_date,buyer_id, company_id,job_no, style_ref_no,po_job_no";

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="80">PO Buyer</th>
			<th width="120">Booking No</th>
			<th width="90">Sales Order No</th>
			<th width="120">Style Ref.</th>
			<th width="80">Booking Date</th>
			<th>Job No.</th>
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
				onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('id')]; ?>')">
				<td width="40"><? echo $i; ?></td>
				<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td><? echo $row[csf('po_job_no')]; ?></td>
			</tr>
			<?
			$i++;
		}

		$sql_partial = "select a.id, a.booking_no,a.booking_no_prefix_num, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, c.job_no from wo_booking_mst a, wo_booking_dtls c,fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond and a.entry_form=108 group by a.id, a.booking_no,a.booking_no_prefix_num,a.booking_date,a.buyer_id,a.company_id,a.delivery_date,a.currency_id,c.job_no";
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
				<td><p><? echo $po_no; ?>&nbsp;</p></td>
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

?>