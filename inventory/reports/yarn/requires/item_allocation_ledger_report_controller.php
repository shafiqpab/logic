<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "item_description_search") {
	echo load_html_head_contents("Popup Info", "../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value(functionParam);

			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(strCon)
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];

			toggle(document.getElementById('tr_' + str), '#FFFFCC');

			if (jQuery.inArray(selectID, selected_id) == -1) {
				selected_id.push(selectID);
				selected_name.push(selectDESC);
				selected_no.push(str);
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == selectID)
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				selected_no.splice(i, 1);
			}
			var id = '';
			var name = '';
			var job = '';
			var num = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			num = num.substr(0, num.length - 1);

			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
			$('#txt_selected_no').val(num);
		}

		function fn_check_lot()
		{
			show_list_view(document.getElementById('cbo_search_by').value + '_' + document.getElementById('txt_search_common').value + '_' +<? echo $company; ?>, 'create_lot_search_list_view', 'search_div', 'item_ledger_report_controller', 'setFilterGrid("list_view",-1)');
		}
	</script>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="500" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>                	 
							<th>Search By</th>
							<th align="center" width="200" id="search_by_td_up">Enter Lot Number</th>
							<th>
								<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
								<input type='hidden' id='txt_selected_id' />
								<input type='hidden' id='txt_selected' />
								<input type='hidden' id='txt_selected_no' />
							</th>
						</tr>
					</thead>
					<tbody>
						<tr align="center">
							<td align="center">
								<?
								$search_by = array(1 => 'Lot No', 2 => 'Item Description');
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../')";
								echo create_drop_down("cbo_search_by", 150, $search_by, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="180" align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check_lot()" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</tr>         
			</table>    
			<div align="center" valign="top" style="margin-top:5px" id="search_div"> </div> 
		</form>
	</div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
<?
exit();
}

if ($action == "create_lot_search_list_view") {
	$ex_data = explode("_", $data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];

	$sql_cond = "";
	if (trim($txt_search_common) != "") {
		if (trim($txt_search_by) == 1) {
			$sql_cond = " and lot LIKE '%$txt_search_common%'";
        } else if (trim($txt_search_by) == 2) { // for Yarn Count
        	$sql_cond = " and product_name_details LIKE '%$txt_search_common%'";
        }
    }

    $sql = "select id,supplier_id,lot,product_name_details from product_details_master where company_id=$company and item_category_id=1 $sql_cond";
    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
    $arr = array(1 => $supplier_arr);
    echo create_list_view("list_view", "Product Id, Supplier, Lot, Item Description", "70,160,70", "600", "260", 0, $sql, "js_set_value", "id,product_name_details", "", 1, "0,supplier_id,0,0", $arr, "id,supplier_id,lot,product_name_details", "", "", "0", "", 1);

    exit();
}

if ($action == "generate_report") {

	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$search_cond = "";
	$company_id = str_replace("'", "", $cbo_company_name);
	if ($db_type == 0) {
		if ($from_date != "" && $to_date != ""){
			$allocation_date_cond = " and b.insert_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
			$tr_date_cond = " and a.insert_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
		}
		
	}
	else {
		if ($from_date != "" && $to_date != ""){
			$allocation_date_cond .= " and b.insert_date  between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
			$tr_date_cond .= " and a.insert_date  between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
		}
	}

	$lot = str_replace("'", "", trim($txt_lot_no));
	if (str_replace("'", "", trim($txt_lot_no)) != "")
		$search_string = " and b.lot='$lot'";
	else
		$search_string = "";

    //library array
	$company_name = return_field_value("company_name", "lib_company", "id=$company_id and status_active=1 and is_deleted=0", "company_name");

	if($txt_product_id=="") $txt_product_id=0;
	$result_arr = $all_issue_trans_id = $sales_order_ids= $booking_ids = $order_ids = array();
	$allocation_sql = "select a.job_no,a.booking_no,a.po_break_down_id,a.item_id,c.product_name_details,c.lot, b.allocation_date, b.insert_date, sum(b.qnty) allocation_qnty,c.unit_of_measure,c.store_id from inv_material_allocation_mst a,inv_material_allocation_dtls b,product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.item_id in($txt_product_id) $allocation_date_cond and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no,a.booking_no,a.po_break_down_id,a.item_id,c.product_name_details,c.lot,b.allocation_date, b.insert_date,c.unit_of_measure,c.store_id order by a.item_id,b.insert_date asc";

	//echo $allocation_sql;

	$allocation_result = sql_select($allocation_sql);
	foreach ($allocation_result as $row) {
		$result_arr[$row[csf("item_id")]][$row[csf("insert_date")]]["item_name"] = $row[csf("product_name_details")];
		$result_arr[$row[csf("item_id")]][$row[csf("insert_date")]]["store_id"] = $row[csf("store_id")];
		$result_arr[$row[csf("item_id")]][$row[csf("insert_date")]]["lot"] = $row[csf("lot")];
		$result_arr[$row[csf("item_id")]][$row[csf("insert_date")]]["job_no"] = $row[csf("job_no")];
		$result_arr[$row[csf("item_id")]][$row[csf("insert_date")]]["booking_no"] = $row[csf("booking_no")];
		$result_arr[$row[csf("item_id")]][$row[csf("insert_date")]]["po_break_down_id"] = $row[csf("po_break_down_id")];		
		$result_arr[$row[csf("item_id")]][$row[csf("insert_date")]]["transaction_type"] = "10";
		$result_arr[$row[csf("item_id")]][$row[csf("insert_date")]]["issue_purpose"] = "";
		$result_arr[$row[csf("item_id")]][$row[csf("insert_date")]]["trans_id"] = "";
		$result_arr[$row[csf("item_id")]][$row[csf("insert_date")]]["qnty"] = $row[csf("allocation_qnty")];
		$all_issue_trans_id[$issue_row[csf("trid")]] = "";
		if (strpos($row[csf("job_no")], 'FSOE') == false) {
			$booking_ids[$row[csf("booking_no")]] = "'".$row[csf("booking_no")]."'";
			$order_ids[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
			$result_arr[$row[csf("item_id")]][$row[csf("insert_date")]]["is_sales"] = 0;
		}else{
			$sales_order_ids[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			$result_arr[$row[csf("item_id")]][$row[csf("insert_date")]]["is_sales"] = 1;
		}
	}
	
	$issue_sql = "select a.id as trid,a.mst_id,a.transaction_type,a.insert_date,a.transaction_date,sum(case when a.transaction_type=2 then a.cons_quantity else 0 end) issue_qnty,sum(case when a.transaction_type=4 then a.cons_quantity else 0 end) issue_return_qnty,a.prod_id,a.store_id,c.product_name_details,c.lot,d.is_sales,d.po_breakdown_id from inv_transaction a,product_details_master c,order_wise_pro_details d where a.prod_id=c.id and a.id=d.trans_id and a.prod_id in ($txt_product_id) $tr_date_cond and a.transaction_type in(2,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 group by a.id,a.mst_id,a.transaction_type, a.insert_date,a.transaction_date,a.prod_id,a.store_id, c.product_name_details, c.lot, d.is_sales,d.po_breakdown_id order by a.prod_id,a.insert_date asc";
	$issue_result = sql_select($issue_sql);
	foreach ($issue_result as $issue_row) {
		$result_arr[$issue_row[csf("prod_id")]][$issue_row[csf("insert_date")]]["item_name"] = $issue_row[csf("product_name_details")];
		$result_arr[$issue_row[csf("prod_id")]][$issue_row[csf("insert_date")]]["store_id"] = $issue_row[csf("store_id")];
		$result_arr[$issue_row[csf("prod_id")]][$issue_row[csf("insert_date")]]["lot"] = $issue_row[csf("lot")];
		$result_arr[$issue_row[csf("prod_id")]][$issue_row[csf("insert_date")]]["mst_id"] = $issue_row[csf("mst_id")];
		$result_arr[$issue_row[csf("prod_id")]][$issue_row[csf("insert_date")]]["transaction_type"] = $issue_row[csf("transaction_type")];
		$result_arr[$issue_row[csf("prod_id")]][$issue_row[csf("insert_date")]]["issue_purpose"] = $issue_row[csf("issue_purpose")];
		$result_arr[$issue_row[csf("prod_id")]][$issue_row[csf("insert_date")]]["qnty"] = $issue_row[csf("issue_qnty")];
		$result_arr[$issue_row[csf("prod_id")]][$issue_row[csf("insert_date")]]["issue_return_qnty"] = $issue_row[csf("issue_return_qnty")];
		$result_arr[$issue_row[csf("prod_id")]][$issue_row[csf("insert_date")]]["trans_id"] = $issue_row[csf("trid")];
		$result_arr[$issue_row[csf("prod_id")]][$issue_row[csf("insert_date")]]["is_sales"] = $issue_row[csf("is_sales")];
		$result_arr[$issue_row[csf("prod_id")]][$issue_row[csf("insert_date")]]["po_break_down_id"] = $issue_row[csf("po_breakdown_id")];

		if($issue_row[csf("is_sales")] == 1){
			$sales_order_ids[$issue_row[csf("po_breakdown_id")]] = $issue_row[csf("po_breakdown_id")];
		}else{
			$order_ids[$issue_row[csf("po_breakdown_id")]] = $issue_row[csf("po_breakdown_id")];
		}
		if($issue_row[csf("transaction_type")] == 2){
			$issue_ids[$issue_row[csf("mst_id")]] = $issue_row[csf("mst_id")];
		}else{
			$receive_ids[$issue_row[csf("mst_id")]] = $issue_row[csf("mst_id")];
		}
	}

	$issue_arr = array();
	$issue_idss=implode(",",$issue_ids);
	if($issue_idss=="") $issue_idss=0;

	$issue_details = sql_select("select id,issue_number,issue_purpose from inv_issue_master where id in(".$issue_idss.")");
	foreach ($issue_details as $issue) {
		$issue_arr[$issue[csf("id")]]["issue_number"] = $issue[csf("issue_number")];
		$issue_arr[$issue[csf("id")]]["issue_purpose"] = $issue[csf("issue_purpose")];
	}

	$receive_idss=implode(",",$receive_ids);
	if($receive_idss=="") $receive_idss=0;
	$receive_arr = return_library_array("select id,recv_number from inv_receive_master where id in(".$receive_idss.")",'id','recv_number');

	$sales_ids = ltrim(implode(',',$sales_order_ids),", ");
	if($sales_ids=="") $sales_ids=0;
	$issue_sales_order=sql_select("select job_no,sales_booking_no,within_group from fabric_sales_order_mst where id in($sales_ids)");
	$salesIssueData=array();
	foreach($issue_sales_order as $sales_row)
	{
		$salesIssueData[$sales_row[csf("id")]]["job_no"]=$sales_row[csf("job_no")];
		$salesIssueData[$sales_row[csf("id")]]["booking_no"]=$sales_row[csf("sales_booking_no")];
		$salesIssueData[$sales_row[csf("id")]]["within_group"]=$sales_row[csf("within_group")];
		$booking_ids[$sales_row[csf("sales_booking_no")]] = "'".$sales_row[csf("sales_booking_no")]."'";
	}
	if(empty($booking_ids)){
		if(!empty($order_ids))
		{
			$jon_nond = "and b.id in (".implode(',',$order_ids).")";
		}
	}else{
		$jon_nond = "and d.booking_no in(".implode(',',$booking_ids).")";
	}

	$issue_job_order=sql_select("select a.job_no, a.style_ref_no, a.buyer_name, b.id as po_id, b.po_number,d.booking_no from wo_po_details_master a,wo_po_break_down b,wo_booking_dtls d where a.job_no=b.job_no_mst and b.id=d.po_break_down_id and d.status_active=1 $jon_nond group by a.job_no, a.style_ref_no, a.buyer_name, b.id, b.po_number,d.booking_no");
	$jobIssueData=array();
	foreach($issue_job_order as $po_row)
	{
		$jobIssueData[$po_row[csf("po_id")]]["job_no"] 	 	= $po_row[csf("job_no")];
		$jobIssueData[$po_row[csf("po_id")]]["booking_no"] 	= $po_row[csf("booking_no")];
		$jobIssueData[$po_row[csf("po_id")]]["po_number"] 	= $po_row[csf("po_number")];

		$jobIssueData[$po_row[csf("booking_no")]]["job_no"] 	 = $po_row[csf("job_no")];
		$jobIssueData[$po_row[csf("booking_no")]]["style_ref_no"]= $po_row[csf("style_ref_no")];
		$jobIssueData[$po_row[csf("booking_no")]]["buyer_name"]  = $po_row[csf("buyer_name")];
	}
	?>
	<style type="text/css">
	table tbody tr td{ text-align: center; }
	.right-align{ text-align: right; }
</style>
<div id="scroll_body"> 
	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
		<thead>
			<tr class="form_caption" style="border:none;">
				<td colspan="19" align="center" style="border:none;font-size:16px; font-weight:bold" >Yarn Item Allocation Ledger </td> 
			</tr>
			<tr style="border:none;">
				<td colspan="19" align="center" style="border:none; font-size:14px;">Company Name : <? echo $company_name; ?></td>
			</tr>
			<tr style="border:none;">
				<td colspan="11" align="center" style="border:none;font-size:12px; font-weight:bold">
					<? if ($from_date != "" || $to_date != "") echo "From " . change_date_format($from_date) . " To " . change_date_format($to_date) . ""; ?>
				</td>
			</tr>
			<tr>
				<th width="20">SL</th>
				<th width="120">Job</th>
				<th width="120">Booking</th>
				<th width="110">Order No</th>
				<th width="130">Trans Date</th>
				<th width="120">Trans Ref No</th>
				<th width="100">Trans Type</th>
				<th width="100">Purpose</th>
				<th width="80">Allocation (+)</th>
				<th width="80">Allocation (-)</th>
				<th width="80">Balance</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 1;			
			$item_arr = array();
			if(!empty($result_arr)){
				foreach ($result_arr as $item_id=>$item_row) {
					$balance=0;
					$total_allocation_qnty = 0;
					$total_issue_qnty = 0;
					$total_balance = 0;
					foreach ($item_row as $transaction_date=>$row) {
						ksort($transaction_date);
						if($row["transaction_type"] == "10"){ // 10=Allocation
							$allocation_qnty = number_format($row["qnty"],2,".","");
							$balance += number_format($row["qnty"],2,".","");
							$issue_qnty=0;
							$booking_no = $row["booking_no"];
							if (strpos($row[csf("job_no")], 'FSOE') == false) {
								$job_no = $jobIssueData[$booking_no]["job_no"];
								$po_break_down_id = $row["po_break_down_id"];
							}else{
								$job_no = $jobIssueData[$booking_no]["job_no"];
								$po_break_down_id = $row["job_no"];
							}						
							$po_break_down_id = in_array("FSOE", explode("-",$row["job_no"]))?$row["job_no"]:$jobIssueData[$row["po_break_down_id"]]["po_number"];
						}else{
							$issue_qnty = number_format($row["qnty"],2,".","");
							$allocation_qnty = number_format($row["issue_return_qnty"],2,".","");
							$balance = ($balance - $issue_qnty) + $allocation_qnty;

							if($row["is_sales"] == 1){
								$booking_no = $salesIssueData[$row["po_break_down_id"]]["booking_no"];
								$job_no = $jobIssueData[$booking_no]["job_no"];							
								$po_break_down_id = $salesIssueData[$row["po_break_down_id"]]["job_no"];
							}else{
								$po_ids = explode(",",$row["po_break_down_id"]);
								$po_break_down_id=$job_no=$booking_no="";
								foreach ($po_ids as $po) {
									$job_no .= $jobIssueData[$po]["job_no"].",";
									$booking_no .= $jobIssueData[$po]["booking_no"].",";
									$po_break_down_id .= $jobIssueData[$po]["po_number"].",";
								}
							}
							$trans_ref = ($row["transaction_type"] == 2)?$issue_arr[$row["mst_id"]]:$receive_arr[$row["mst_id"]];
							if($row["transaction_type"] == 2){
								$trans_ref = $issue_arr[$row["mst_id"]]["issue_number"];
								$issue_purpose = $issue_arr[$row["mst_id"]]["issue_purpose"];
							}else{
								$trans_ref = $receive_arr[$row["mst_id"]];
							}
						}

						if(!in_array($item_id, $item_arr)){
							?>
							<tr>
								<td colspan="11" style="text-align: left; background-color: #e3e3e3;"><b>&nbsp;Product ID : <? echo $item_id . ", " . $row["item_name"] . ", Lot#" . $row["lot"] . ", UOM#" . $unit_of_measurement[$row["unit_of_measure"]]; ?></b></td>
							</tr>
							<?
						}					
						$bgcolor = ($i % 2 == 0)?"#E9F3FF":"#FFFFFF";
						$stylecolor = ($row["transaction_type"] == 2)? "style='color:#A61000'" : "style='color:#000000'";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td><? echo $i;?></td>
							<td><? echo rtrim(implode(",",array_unique(explode(",",$job_no))),", ");?></td>
							<td><? echo rtrim(implode(",",array_unique(explode(",",$booking_no))),", ");?></td>
							<td style="word-break: break-all;"><? echo rtrim($po_break_down_id,", ");?></td>
							<td><? echo $transaction_date;?></td>
							<td><? echo $trans_ref;?></td>
							<td><? echo ($row["transaction_type"]=="10")?"Allocation" : $transaction_type[$row["transaction_type"]];?></td>
							<td><? echo $yarn_issue_purpose[$issue_purpose];?></td>
							<td class="right-align"><? echo $allocation_qnty;?></td>
							<td class="right-align"><? echo $issue_qnty;?></td>
							<td class="right-align"><? echo number_format($balance,2,".","");?></td>
						</tr>
						<?
						$total_allocation_qnty += $allocation_qnty;
						$total_issue_qnty += $issue_qnty;
						$total_balance = $balance;
						$item_arr[$item_id] = $item_id;
						$i++;
					}
					?>
					<tr>
						<th style="border-bottom: 1px solid #8DAFDA;padding: 1px;" colspan="11"></th>
					</tr>
					<tr>
						<th class="right-align" colspan="8">Total = </th>
						<th class="right-align"><? echo number_format($total_allocation_qnty,2,".",""); ?></th>
						<th class="right-align"><? echo number_format($total_issue_qnty,2,".",""); ?></th>
						<th class="right-align"><? echo number_format($total_balance,2,".",""); ?></th>
					</tr>
					<?
					$j++;
				}
			}else{
				echo "<tr><th colspan='11' style='color:red;'>No Data Found</th></tr>";
			}
			?>
		</tbody>
	</table>
</div><br />
<?
}
?>

