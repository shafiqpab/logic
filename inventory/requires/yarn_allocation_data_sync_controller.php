<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$buy_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$company_id=str_replace("'","",$cbo_company_id);
	$product_id = implode(",",explode(",",str_replace("'","",$txt_product_id)));
	if($product_id == ""){
		die;
	}

	$prod_cond = " and item_id in (".$product_id.")";
	if ($db_type == 0) {
		$sql_allocation = "select a.id,a.mst_id,a.item_id,a.job_no, a.po_break_down_id,group_concat(booking_no) booking_no, sum(a.qnty) as allocate_qty,a.is_dyied_yarn from inv_material_allocation_dtls_b a,product_details_master_b b where a.item_id=b.id and b.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_cond group by a.item_id,a.job_no,a.po_break_down_id,a.is_dyied_yarn,a.id,a.mst_id";
	}else{
		$sql_allocation = "select a.id,a.mst_id,a.item_id,a.job_no, a.po_break_down_id,listagg(cast(a.booking_no as varchar2(4000)), ',') within group (order by a.booking_no) as booking_no, sum(a.qnty) as allocate_qty,a.is_dyied_yarn from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and b.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_cond group by a.item_id,a.job_no,a.po_break_down_id,a.is_dyied_yarn,a.id,a.mst_id";
	}

	$result_allocation = sql_select($sql_allocation);

	$po_break_down_arr = $job_arr =  array();
	foreach ($result_allocation as $row) {
		if($row[csf("is_dyied_yarn")] == 1){
			$job_arr[$row[csf("job_no")]] = "'".$row[csf("job_no")]."'";
		} else {
			$po_break_down_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
		}
		$item_arr[] = $row[csf("item_id")];
	}

	$sql_products = "select a.id, a.company_id, a.lot,a.product_name_details,a.current_stock, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit,dyed_type from product_details_master a where a.item_category_id=1 and company_id=$company_id and id in($product_id) and a.status_active=1 and a.is_deleted=0";

	$product_array=array();
	$productData = sql_select($sql_products);
	foreach( $productData as $prod_val){
		$product_array[$prod_val[csf('id')]]['lot']						= $prod_val[csf('lot')];
		$product_array[$prod_val[csf('id')]]['product_name_details']	= $prod_val[csf('product_name_details')];
		$product_array[$prod_val[csf('id')]]['current_stock']			= $prod_val[csf('current_stock')];
		$product_array[$prod_val[csf('id')]]['allocated_qnty']			= $prod_val[csf('allocated_qnty')];
		$product_array[$prod_val[csf('id')]]['available_qnty']			= $prod_val[csf('available_qnty')];
		$product_array[$prod_val[csf('id')]]['dyed_type']				= $prod_val[csf('dyed_type')];
	}

	$sql_cond = !empty($job_arr)?" and a.job_no_mst in(".implode(",",$job_arr).")":" and a.id in(".implode(",",$po_break_down_arr).")";
	$po_number_arr = array();
	$po_sql = sql_select("select a.id,a.job_no_mst,b.buyer_name, a.file_no,a.grouping,a.po_number from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active!=0 and a.is_deleted!=1 $sql_cond");
	foreach ($po_sql as $row) {
		$po_number_arr[$row[csf("id")]]['po'] = $row[csf("po_number")];
		$po_number_arr[$row[csf("id")]]['file'] = $row[csf("file_no")];
		$po_number_arr[$row[csf("id")]]['ref'] = $row[csf("grouping")];
		$po_number_arr[$row[csf("id")]]['job_no'] = $row[csf("job_no_mst")];
		$po_number_arr[$row[csf("job_no_mst")]]['buyer_name'] = $row[csf("buyer_name")];
		$po_number_arr[$row[csf("id")]]['shiping_status'] = $row[csf("shiping_status")];
	}

	$job_no_array = array();
	/*$jobsql = "select a.id, a.job_no,a.buyer_id,a.sales_booking_no, a.style_ref_no,a.within_group from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0";
	$jobData = sql_select($jobsql);
	foreach ($jobData as $row) {
		$sales_order_arr[$row[csf('job_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
		$sales_order_arr[$row[csf('job_no')]]['buyer_id']=$row[csf('buyer_id')];
		$sales_order_arr[$row[csf('job_no')]]['within_group']=$row[csf('within_group')];
	}*/

	$planning_array = array();
	$plan_sql="select b.po_id,b.booking_no,b.dtls_id,b.program_qnty,b.total_prog_qnty,c.id req_id,c.requisition_no,c.prod_id,sum(c.yarn_qnty) req_qnty from (select a.po_id,a.booking_no,a.dtls_id,a.program_qnty,d.program_qnty total_prog_qnty from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls d where a.dtls_id=d.id and a.status_active=1 and d.status_active=1 group by a.po_id,a.booking_no,a.dtls_id,a.program_qnty,d.program_qnty) b,ppl_yarn_requisition_entry c where b.dtls_id=c.knit_id and c.status_active=1  and c.prod_id=$product_id group by b.po_id,b.booking_no,b.dtls_id,b.program_qnty,b.total_prog_qnty,c.id,c.requisition_no,c.prod_id";
	$planData = sql_select($plan_sql);
	$requisition_qnty_array=array();
	foreach ($planData as $row) {
		$total_prog_qnty = $row[csf('total_prog_qnty')];
		$po_program_qnty = $row[csf('program_qnty')];
		$perc 			 = ($po_program_qnty / $total_prog_qnty) * 100;
		$req_qnty 		 = ($row[csf('req_qnty')] / 100)*$perc;

		$requisition_qnty_array[$row[csf('po_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]]+=$req_qnty;

		$planning_array[$row[csf('po_id')]][$row[csf('booking_no')]][$row[csf('requisition_no')]][$row[csf('prod_id')]]['req_qnty'] += $req_qnty;
		$planning_array[$row[csf('po_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['requisition_no'] .= $row[csf('requisition_no')].",";
		$planning_array[$row[csf('po_id')]][$row[csf('prod_id')]]['req_id'] .= $row[csf('req_id')].",";
		$planning_array[$row[csf('po_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]][] = $row[csf('booking_no')];

	}

	$issue_array = $job_wose_issue_array = $issue_basis_arr = array();
	$prod_cond2 = " and c.prod_id in (".$prod_id.")";
	$sql_issue = "select a.id,a.issue_basis,a.issue_purpose,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type, sum(c.quantity) as issue_qty,b.job_no from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and (a.issue_purpose in(1,2) or a.issue_purpose in(1,2,15) and b.job_no is not null) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 and c.prod_id=$product_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by a.id,a.issue_basis,a.issue_purpose,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type,b.job_no";

	$result_issue = sql_select($sql_issue);
	$issue_array_req=$booking_arr=array();
	foreach ($result_issue as $row) {

		$issue_job[$row[csf("id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] = $row[csf("job_no")];
		$issue_basis_arr[$row[csf("id")]]["basis"] = $row[csf("issue_basis")];
		$issue_basis_arr[$row[csf("id")]]["issue_id"] = $row[csf("id")];

		if($row[csf('dyed_type')] == 1){
			$issue_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
			$issue_arr[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
		}else{
			$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			if($row[csf("issue_basis")] == 3){
				$booking_arr = $planning_array[$row[csf('po_breakdown_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]];
				$booking_arr = array_unique($booking_arr);
				foreach ($booking_arr as $booking) {
					if($booking != ""){
						$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
						$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_id"][] = $row[csf("id")];
						$issue_array_issue_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking][$row[csf("id")]][] = $row[csf("requisition_no")];
					}
				}
				$requisition_wise_issue_array[$row[csf("po_breakdown_id")]][$row[csf("requisition_no")]][$row[csf("prod_id")]] += $row[csf("issue_qty")];
			}else{
				$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
				$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_id"][] = $row[csf("id")];
			}
		}
		$job_wose_issue_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']][$row[csf("prod_id")]] += $row[csf("issue_qty")];
	}
	/*echo "<pre>";
	print_r($issue_array_req);
	echo "</pre>";*/
	$issue_return_array = $issue_return_req_array = $job_wose_issue_return_array = array();
	$sql_return = "Select b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose, sum(c.quantity) as issue_return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and c.trans_type=4 and a.entry_form=9 and b.id=c.trans_id and b.item_category=1 and c.issue_purpose in(1,2,7,15,38,46) and c.prod_id=$product_id and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose";
	$result_return = sql_select($sql_return);
	foreach ($result_return as $row) {
		$issue_basis = $issue_basis_arr[$row[csf("issue_id")]]["basis"];
		if($issue_basis == 3){
			$issue_id = $issue_basis_arr[$row[csf("issue_id")]]["issue_id"];
			$issue_return_req_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$issue_id] = $row[csf("issue_return_qty")];
		}else{
			$issue_job = $issue_job[$row[csf("issue_id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]];
			if($issue_job!="" && ($row[csf("issue_purpose")]==7 || $row[csf("issue_purpose")]==15 || $row[csf("issue_purpose")]==38 || $row[csf("issue_purpose")]==46)){
				$issue_return_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
			}else{
				$issue_return_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
			}
		}

		$job_wose_issue_return_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
	}
	?>
	<style type="text/css">
	table { width: 100%; margin: auto; font-family: arial; font-size: 12px; }
	table tr td{ background-color: #E9F3FF; }
	.new { background-color: rgba(52,168,83,.2); }
	.border{border-top: 1px solid; background-color: #fff !important; font-weight: bold; font-size: 11px; }
	thead th{  background-color: #ccc !important; }
	.global { background-color: rgba(52,168,83,.5) }
	.over_less_allocation { background-color: red !important;color:#fff; }
	.inc_dec_allocation { background-color: yellow !important; }
</style>
<div align="center">
	<input id="search" value="Click For Synchronize" onclick="synchronize_allocation()" style="width:140px" class="formbutton" type="button">
	<div id="result_set"></div>
</div><br />
<table border="1" cellpadding="1" cellspacing="0" class="rpt_table" rules="all">
	<?
	$i = 1;
	$balance = '';
	$prod_arr = $mst_data_update_arr = $dtls_data_update_arr = $item_arr = $dtls_balance_arr = array();
	if(!empty($result_allocation)){
		$balance = $balance_new = $issue_qty = 0;
		$html_hr=$allocation_mismatch="";
		foreach ($result_allocation as $row) {
			$prod_id = $row[csf("item_id")];
			$dtls_id = $row[csf("id")];
			if(empty($item_arr)){
				$item_arr[] = $prod_id;
				$html_hr = "<tr><td colspan='16' class='border ".$allocation_mismatch."'>Product ID: ".$prod_id."; Lot: ".$product_array[$prod_id]['lot']."; Product Name:".$product_array[$prod_id]['product_name_details']."; Crrent stock: ".number_format($product_array[$prod_id]['current_stock'],2)."; Allocated: ".number_format($product_array[$prod_id]['allocated_qnty'],2)."; Available: ".number_format($product_array[$prod_id]['available_qnty'],2)."</td></tr>";
				$html_hr .= '<thead>
				<th width="25">SL</th>
				<th width="60">Product ID</th>
				<th width="150">Job NO.</th>
				<th width="100">Buyer</th>
				<th width="100">Order ID</th>
				<th width="110">Booking No.</th>
				<th width="75">Allocated Qty</th>
				<th width="70">Issue Qty</th>
				<th width="60">Rtn Qty</th>
				<th width="">Cumul. Balance</th>

				<th class="global" width="75" title="Requisition quantity">Req. qnty</th>
				<th class="new" width="75">Allocated Qty</th>
				<th class="new" width="70">Issue Qty</th>
				<th class="new" width="60">Rtn Qty</th>
				<th class="new" width="">Cumul. Balance</th>
				</thead>';
			}else{
				if(!in_array($prod_id, $item_arr)){
					$i = 1;
					$balance = $balance_new = 0;
					unset($item_arr);
					$item_arr[] = $prod_id;

					$html_hr = '<thead>
					<th width="25">SL</th>
					<th width="60">Product ID</th>
					<th width="150">Job NO.</th>
					<th width="100">Buyer</th>
					<th width="100">Order ID</th>
					<th width="110">Booking No.</th>
					<th width="75">Allocated Qty</th>
					<th width="70">Issue Qty</th>
					<th width="60">Rtn Qty</th>
					<th width="">Cumul. Balance</th>

					<th class="global" width="75" title="Requisition quantity">Req. qnty</th>
					<th class="new" width="75">Allocated Qty</th>
					<th class="new" width="70">Issue Qty</th>
					<th class="new" width="60">Rtn Qty</th>
					<th class="new" width="">Cumul. Balance</th>
					</thead>';

					$html_hr .= "<tr><td colspan='16' class='border ".$allocation_mismatch."'>Product ID: ".$prod_id."; Lot: ".$product_array[$prod_id]['lot']."; Product Name: ".$product_array[$prod_id]['product_name_details']."; Crrent stock: ".number_format($product_array[$prod_id]['current_stock'],2)."; Allocated: ".number_format($product_array[$prod_id]['allocated_qnty'],2)."; Available: ".number_format($product_array[$prod_id]['available_qnty'],2)."</td></tr>";
				}else{
					$html_hr = "";
				}
			}

			if($row[csf("is_dyied_yarn")] == 1){
				$issue_qty = $job_wose_issue_array[$row[csf("job_no")]][$prod_id];
			}else{
				$issue_qty=0;

				if($row[csf("po_break_down_id")]==""){
					$issue_basis = array_unique($issue_arr[$row[csf('job_no')]][$prod_id]);
				}else{
					$issue_basis = array_unique($issue_arr[$row[csf("po_break_down_id")]][$prod_id]);
				}

				foreach ($issue_basis as $basis) {
					if($basis==3 || $basis==1){
						if($basis==1){
							$issue_qty += $issue_array_req[$row[csf("po_break_down_id")]][$prod_id]["issue_qty"];
						}else{
							if($row[csf("booking_no")] != ""){
								$booking_nos = explode(",",$row[csf("booking_no")]);
								$issue_qty=0;
								foreach ($booking_nos as $booking_row) {
									$issue_qty += $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_qty"];
									$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_id"]);
									$requisitions="";
									foreach ($issue_ids as $issue_id) {
										$requisitions_arr = $issue_array_issue_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row][$issue_id];
										foreach ($requisitions_arr as $r_row) {
											$requisitions .= $r_row.",";
										}
									}
								}
							}
						}
					}else{
						$issue_qty += $issue_array[$row[csf('job_no')]][$prod_id]["issue_qty"];
					}
				}
			}

			$issue_qty = ($row[csf('job_no')]!="")?$issue_qty:0;

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
				}
				else
				{
					$buyer_id=$sales_order_arr[$row[csf('job_no')]]['buyer_id'];
					$buyername=$buy_name_arr[$buyer_id];
					$po_number="";
				}
			}
			else
			{
				$po_number=$po_number_arr[$row[csf("po_break_down_id")]]['po'];
				$buyername=$buy_name_arr[$po_number_arr[$row[csf("job_no")]]['buyer_name']];
				if($row[csf("is_dyied_yarn")] == 1){
					$return_qty = $job_wose_issue_return_array[$row[csf("job_no")]][$prod_id];
				}else{
					$issue_ids=array();
					$return_qty=0;
					foreach ($issue_basis as $basis) {
						if($basis==3){

							$booking_nos = explode(",",$row[csf("booking_no")]);
							foreach ($booking_nos as $booking_row) {
								$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_id"]);
								/*echo "<pre>";
								print_r($issue_ids);
								echo "</pre>";*/
								$return_qty=0;
								foreach ($issue_ids as $issue_id) {
									$return_qty += $issue_return_req_array[$row[csf('po_break_down_id')]][$prod_id][$issue_id];
								}
							}
						}else{
							$return_qty = $issue_return_array[$row[csf("po_break_down_id")]][$prod_id];
						}
					}
				}
				$return_qty = ($row[csf('job_no')]!="")?$return_qty:0;
			}
			//echo "=";
			$booking_no = implode(",",array_unique(explode(",", $row[csf("booking_no")])));
			echo $html_hr;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td width="25" align="center"><? echo $i; ?></td>
				<td width="60" align="center"><? echo $prod_id; ?></td>
				<td width="150" align="center" ><? echo $row[csf("job_no")]; ?></td>
				<td width="100" align="center"><? echo $buyername; ?></td>
				<td width="100" align="center" title="<? echo $row[csf('po_break_down_id')]; ?>"><? echo $po_number; ?></td>
				<td width="100" align="center"><? echo $booking_no; ?></td>
				<td width="75" align="right"><? echo number_format($row[csf("allocate_qty")], 2,".",""); ?></td>
				<td width="70" align="right"><? echo number_format($issue_qty, 2,".",""); ?></td>
				<td width="60" align="right"><? echo number_format($return_qty, 2,".",""); ?></td>
				<td align="right">
					<?
					$balance = $balance + (number_format($row[csf("allocate_qty")], 2,".","") + number_format($return_qty, 2,".","")) - number_format($issue_qty, 2,".","");
					echo number_format($balance, 2,".","");
					?>
				</td>
				<?
				$issue_qty_new = number_format($issue_qty, 2,".","");
				$return_qty_new = number_format($return_qty, 2,".","");

				$requisitions = array_unique(explode(",",rtrim($requisitions,", ")));

				$requisition_nos = "";
				$total_requisition_qnty=0;
				$requisiton_arr=array();
				foreach ($requisitions as $req_no) {
					$total_requisition_qnty += $planning_array[$row[csf("po_break_down_id")]][$booking_no][$req_no][$prod_id]['req_qnty'];

					$requisition_issue_qnty = $requisition_wise_issue_array[$row[csf("po_break_down_id")]][$req_no][$prod_id];
					$req_qnty = $requisition_qnty_array[$row[csf('po_break_down_id')]][$req_no][$prod_id];
					$requisition_nos .= "Req. no=".$req_no.", Req. Qnty=".number_format($req_qnty,2).",Issue=".number_format($requisition_issue_qnty,2).",\n";
					$requisiton_arr[$dtls_id][$booking_no][$row[csf("po_break_down_id")]][$req_no] += $req_qnty;
					$dtls_data_update_arr[$dtls_id]["po_requisition_qty"] = $requisiton_arr;
					if($req_qnty<$requisition_issue_qnty){
						$inc_requisition_qnty = number_format($requisition_issue_qnty-$req_qnty,2,".","");
					}
				}
				$requisition_qnty = $total_requisition_qnty;
				$requisition_nos=rtrim($requisition_nos,", ");

				if($issue_qty_new > 0){
					if($requisition_qnty < $issue_qty_new){
						$new_allocation = number_format(($issue_qty_new-$return_qty_new),2,".","");
					}else{
						$new_allocation = number_format(($requisition_qnty-$return_qty_new),2,".","");
					}
				}else{
					$new_allocation = number_format($row[csf("allocate_qty")],2,".","");
				}

				$balance_new = $balance_new + ($new_allocation + $return_qty_new) - $issue_qty_new;
				$inc_dec_allocation = (number_format($new_allocation,2,".","")!=number_format($row[csf("allocate_qty")],2,".",""))?"inc_dec_allocation":"";
				$over_less_allocation = ($return_qty_new > $issue_qty_new)?"over_less_allocation":"";
				?>
				<td width="75" align="right" title="<? echo $requisition_nos;?>"><? echo number_format($requisition_qnty,2,".",""); ?></td>
				<td class="new <? echo $inc_dec_allocation;?>" width="75" align="right"><? echo number_format($new_allocation,2,".",""); ?></td>
				<td class="new <? echo $over_less_allocation;?>" width="70" align="right"><? echo number_format($issue_qty_new, 2,".",""); ?></td>
				<td class="new" width="60" align="right"><? echo number_format($return_qty_new, 2,".",""); ?></td>
				<td class="new" align="right"><? echo number_format($balance_new, 2,".",""); ?></td>
			</tr>
			<?
			$dtls_data_update_arr[$dtls_id]["qnty"] += $new_allocation;
			$dtls_data_update_arr[$dtls_id]["pre_qnty"] += $row[csf("allocate_qty")];
			$dtls_data_update_arr[$dtls_id]["po_break_down_id"] = $row[csf("po_break_down_id")];
			$dtls_data_update_arr[$dtls_id]["job_no"] = $row[csf("job_no")];
			$dtls_data_update_arr[$dtls_id]["prod_id"] = $prod_id;
			$dtls_data_update_arr[$dtls_id]["mst_id"] = $row[csf("mst_id")];
			$dtls_data_update_arr[$dtls_id]["booking_no"] = $booking_no;
			$dtls_data_update_arr[$dtls_id]["issue_qty_new"] = $issue_qty_new;
			$dtls_data_update_arr[$dtls_id]["return_qty_new"] = $return_qty_new;
			$dtls_data_update_arr[$dtls_id]["requisition_ids"] = rtrim($planning_array[$row[csf("po_break_down_id")]][$prod_id]['req_id'],", ");
			$dtls_data_update_arr[$dtls_id]["requisition_nos"] = rtrim($planning_array[$row[csf('po_break_down_id')]][$booking_no][$prod_id]['requisition_no'],", ");
			$dtls_data_update_arr[$dtls_id]["requisition_qty"] = $requisition_qnty;

			$dtls_balance_arr[$prod_id]["balance"] = $balance_new;
			$i++;
		}

		$balance1=0;
		$item_arr=$req_a=$req_issue_qnty=array();
		foreach ($dtls_data_update_arr as $dtl_id => $dtl_row) {
			$current_stock = $product_array[$dtl_row["prod_id"]]['current_stock'];
			$mst_id   = $dtl_row["mst_id"];
			$booking_no   = $dtl_row["booking_no"];
			$balance  = $dtls_balance_arr[$dtl_row["prod_id"]]["balance"];
			$dtl_qnty = $dtl_row["qnty"];
			$pre_qnty = $dtl_row["pre_qnty"];

			$requisitions 	 = array_unique(explode(",",$dtl_row["requisition_ids"]));
			$requisition_qty = $dtl_row["requisition_qty"];

			$requisition_nos = array_unique(explode(",",$dtl_row["requisition_nos"]));
			$po_requisition_qty=$dtl_row["po_requisition_qty"];

			if($current_stock >= $balance){
				if(empty($item_arr)){
					$item_arr[] = $dtl_row["prod_id"];
					$balance1 = $balance;
				}else{
					if(!in_array($dtl_row["prod_id"], $item_arr)){
						$balance1 = $balance;
						unset($item_arr);
						$item_arr[] = $dtl_row["prod_id"];
					}else{
						$balance1 = $balance;
					}
				}

				if($dtl_row["issue_qty_new"] > $requisition_qty){
					if($pre_qnty>$dtl_row["issue_qty_new"]){
						$allocation = number_format($pre_qnty,2,".","");
					}else{
						$allocation = number_format($dtl_qnty,2,".","");
					}

					//echo "UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id;<br />";
					//$update_allocation_dtls=execute_query("UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id");
					foreach ($requisition_nos as $req_no) {
						$req_issue_qnty = $requisition_wise_issue_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]];
						$po_req_qnty = $requisition_qnty_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]];
						$req_a[$req_no][$dtl_row["prod_id"]] += $po_req_qnty + ($req_issue_qnty-$po_req_qnty);
						$req_qnty = $requisition_qnty_array[$row[csf('po_break_down_id')]][$req_no][$prod_id];
					}
				}else{
					$allocation = number_format($dtl_qnty,2,".","");
					//echo "UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id;<br />";
					foreach ($requisition_nos as $req_no) {
						$req_a[$req_no][$dtl_row["prod_id"]] += $requisition_qnty_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]];
					}
				}

			}else{
				if($dtl_row["issue_qty_new"] > 0){
					$allocation = number_format(($dtl_row["issue_qty_new"]-$dtl_row["return_qty_new"]),2,".","");
					$balance1 += $allocation;
				}else{
					$allocation = 0;
				}

				foreach ($requisition_nos as $req_no) {
					$req_issue_qnty = $requisition_wise_issue_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]]*1;
					$po_req_qnty = $requisition_qnty_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]];
					$req_a[$req_no][$dtl_row["prod_id"]] += $po_req_qnty + ($req_issue_qnty-$po_req_qnty);
				}
			}

			$mst_data_update_arr[$dtl_row["mst_id"]]["qnty"] += $allocation;
			$mst_data_update_arr[$dtl_row["mst_id"]]["prod_id"] = $dtl_row["prod_id"];
			$mst_data_update_arr[$dtl_row["mst_id"]]["balance"] = $balance1;
			$mst_data_update_arr[$dtl_row["mst_id"]]["current_stock"] = $product_array[$dtl_row["prod_id"]]['current_stock'];
			$mst_data_update_arr[$dtl_row["mst_id"]]["qnty_break_down"] .= number_format($allocation,2,".","") . "_" . $dtl_row["po_break_down_id"] . "_" . $dtl_row["job_no"] . ",";
		}

		$prod_arr=array();
		foreach ($mst_data_update_arr as $mst_id => $mst_row) {
			$prod_id = $mst_row["prod_id"];
			$current_stock = $mst_row["current_stock"];
			$qnty = number_format($mst_row["qnty"],2,".","");
			$balance = $mst_row["balance"];
			$qnty_break_down = trim($mst_row["qnty_break_down"],", ");

			if($current_stock >= $balance){
				$allocation = $qnty;
				$prod_arr[$prod_id]["current_stock"] = $current_stock;
				$prod_arr[$prod_id]["allocated"] = number_format($balance,2,".","");
				$prod_arr[$prod_id]["available"] = number_format(($current_stock-$balance),2,".","");
			}else{
				if($current_stock < $balance){
					$prod_arr[$prod_id]["current_stock"] = $current_stock;
					$prod_arr[$prod_id]["allocated"] = 0;
					$prod_arr[$prod_id]["available"] = number_format($current_stock,2,".","");
				}
			}
		}

	}else{
		echo "<tr><th style='text-align:center;' colspan='16'>No Data Found</th></tr>";
	}
	?>
</table>
<table border="1" cellpadding="1" cellspacing="0" class="rpt_table" style="width:200px;margin-top:5px;" rules="all">
	<thead>
		<tr>
			<th>Requisition No</th>
			<th>Qnty</th>
		</tr>
	</thead>
	<?

	foreach ($req_a as $req_no => $product) {
		if($req_no!=""){
			foreach ($product as $product_id => $req_qnty) {
				echo "<tr>";
				echo "<td>$req_no</td>";
				echo "<td align='right'>".number_format($req_qnty,2,".","")."</td>";
				echo "</tr>";
			}
		}
	}
	?>
</table>
<?
exit();
}

if ($action=="synchronize_allocation12")
{
	extract($_REQUEST);
	$con = connect();

	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_id=str_replace("'","",$company_id);
	$product_id = implode(",",explode(",",str_replace("'","",$product_id)));
	if($product_id == ""){
		die;
	}

	$prod_cond = " and item_id in (".$product_id.")";
	if ($db_type == 0) {
		$sql_allocation = "select a.id,a.mst_id,a.item_id,a.job_no, a.po_break_down_id,group_concat(booking_no) booking_no, sum(a.qnty) as allocate_qty,a.is_dyied_yarn,a.allocation_date from inv_material_allocation_dtls_b a,product_details_master_b b where a.item_id=b.id and b.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_cond group by a.item_id,a.allocation_date,a.job_no,a.po_break_down_id,a.is_dyied_yarn,a.id,a.mst_id";
	}else{
		$sql_allocation = "select a.id,a.mst_id,a.item_id,a.job_no, a.po_break_down_id,listagg(cast(a.booking_no as varchar2(4000)), ',') within group (order by a.booking_no) as booking_no, sum(a.qnty) as allocate_qty,a.is_dyied_yarn,a.allocation_date from inv_material_allocation_dtls_b a,product_details_master_b b where a.item_id=b.id and b.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_cond group by a.item_id,a.allocation_date,a.job_no,a.po_break_down_id,a.is_dyied_yarn,a.id,a.mst_id";
	}

	$result_allocation = sql_select($sql_allocation);

	$po_break_down_arr = $job_arr =  array();
	foreach ($result_allocation as $row) {
		if($row[csf("is_dyied_yarn")] == 1){
			$job_arr[$row[csf("job_no")]] = "'".$row[csf("job_no")]."'";
		} else {
			$po_break_down_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
		}
		$item_arr[] = $row[csf("item_id")];
	}

	$sql_products = "select a.id, a.company_id, a.lot,a.product_name_details,a.current_stock, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit,dyed_type from product_details_master_b a where a.item_category_id=1 and company_id=$company_id and id in($product_id) and a.status_active=1 and a.is_deleted=0";

	$product_array=array();
	$productData = sql_select($sql_products);
	foreach( $productData as $prod_val){
		$product_array[$prod_val[csf('id')]]['lot']						= $prod_val[csf('lot')];
		$product_array[$prod_val[csf('id')]]['product_name_details']	= $prod_val[csf('product_name_details')];
		$product_array[$prod_val[csf('id')]]['current_stock']			= $prod_val[csf('current_stock')];
		$product_array[$prod_val[csf('id')]]['allocated_qnty']			= $prod_val[csf('allocated_qnty')];
		$product_array[$prod_val[csf('id')]]['available_qnty']			= $prod_val[csf('available_qnty')];
		$product_array[$prod_val[csf('id')]]['dyed_type']				= $prod_val[csf('dyed_type')];
	}

	$sql_cond = !empty($job_arr)?" and a.job_no_mst in(".implode(",",$job_arr).")":" and a.id in(".implode(",",$po_break_down_arr).")";
	$po_number_arr = array();
	$po_sql = sql_select("select a.id,a.job_no_mst,b.buyer_name, a.file_no,a.grouping,a.po_number,shiping_status from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $sql_cond and a.status_active=1 and a.is_deleted=0");
	foreach ($po_sql as $row) {
		$po_number_arr[$row[csf("id")]]['po'] = $row[csf("po_number")];
		$po_number_arr[$row[csf("id")]]['file'] = $row[csf("file_no")];
		$po_number_arr[$row[csf("id")]]['ref'] = $row[csf("grouping")];
		$po_number_arr[$row[csf("id")]]['job_no'] = $row[csf("job_no_mst")];
		$po_number_arr[$row[csf("job_no_mst")]]['buyer_name'] = $row[csf("buyer_name")];
		$po_number_arr[$row[csf("id")]]['shiping_status'] = $row[csf("shiping_status")];
	}

	$job_no_array = array();
	$jobsql = "select a.id, a.job_no,a.buyer_id,a.sales_booking_no, a.style_ref_no,a.within_group from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0";
	$jobData = sql_select($jobsql);
	foreach ($jobData as $row) {
		$sales_order_arr[$row[csf('job_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
		$sales_order_arr[$row[csf('job_no')]]['buyer_id']=$row[csf('buyer_id')];
		$sales_order_arr[$row[csf('job_no')]]['within_group']=$row[csf('within_group')];
	}

	$issue_array = $job_wose_issue_array = array();
	$prod_cond2 = " and c.prod_id in (".$product_id.")";
	$sql_issue = "select c.po_breakdown_id,c.prod_id, sum(c.quantity) as issue_qty from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.issue_purpose in(1,2) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 $prod_cond2  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by c.po_breakdown_id,c.prod_id";
	$result_issue = sql_select($sql_issue);
	foreach ($result_issue as $row) {
		$issue_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] = $row[csf("issue_qty")];
		$job_wose_issue_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']] += $row[csf("issue_qty")];
	}

	$issue_return_array = $job_wose_issue_return_array = array();
	$sql_return = "Select c.po_breakdown_id,c.prod_id, sum(c.quantity) as issue_return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and c.trans_type=4 and a.entry_form=9 and b.id=c.trans_id and b.item_category=1 and c.issue_purpose in(1,2) $prod_cond2 and a.status_active=1 and b.status_active=1 and c.status_active=1 group by c.po_breakdown_id,c.prod_id";
	$result_return = sql_select($sql_return);
	foreach ($result_return as $row) {
		$issue_return_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
		$job_wose_issue_return_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']] += $row[csf("issue_return_qty")];
	}
	?>
	<div align="center">
		<input id="search" value="Click For Synchronize" onclick="synchronize_allocation()" style="width:140px" class="formbutton" type="button">
		<div id="result_set"></div>
	</div><br />
	<table border="1" cellpadding="1" cellspacing="0" class="rpt_table" rules="all">
		<?
		$i = 1;
		$balance = '';
		$prod_arr = array();
		if(!empty($result_allocation)){
			$balance = $balance_new = $issue_qty = 0;
			$html_hr="";
			$item_arr = $mst_data_update_arr = array();
			$shipping_status = array(1=>"Pending",2=>"Partial",3=>"Full");
			foreach ($result_allocation as $row) {
				$prod_id = $row[csf("item_id")];
				if(empty($item_arr)){
					$item_arr[] = $prod_id;
					$html_hr = "<tr><td colspan='16' class='border'>Product ID: ".$prod_id."; Lot: ".$product_array[$prod_id]['lot']."; Product Name:".$product_array[$prod_id]['product_name_details']."; Crrent stock: ".$product_array[$prod_id]['current_stock']."; Allocated: ".$product_array[$prod_id]['allocated_qnty']."; Available: ".$product_array[$prod_id]['available_qnty']."</td></tr>";
					$html_hr .= '<thead>
					<th width="25">SL</th>
					<th width="25">DTLS</th>
					<th width="60">Product</th>
					<th width="130">Job/FSO No</th>
					<th width="150">Buyer</th>
					<th width="180">Order No</th>
					<th width="60">Shipping</th>
					<th width="110">Booking No.</th>
					<th width="75">Allocated</th>
					<th width="70">Issue</th>
					<th width="60">Return</th>
					<th width="">Balance</th>

					<th class="new" width="85">N Allocated</th>
					<th class="new" width="80">N Issue</th>
					<th class="new" width="70">N Return</th>
					<th class="new">N Balance</th>
					</thead>';
				}else{
					if(!in_array($prod_id, $item_arr)){
						$i = 1;
						$balance = $balance_new = 0;
						unset($item_arr);
						$item_arr[] = $prod_id;

						$html_hr = '<thead>
						<th width="25">SL</th>
						<th width="25">DTLS</th>
						<th width="60">Product</th>
						<th width="130">Job/FSO No</th>
						<th width="150">Buyer</th>
						<th width="180">Order No</th>
						<th width="60">Shipping</th>
						<th width="110">Booking No.</th>
						<th width="75">Allocated</th>
						<th width="70">Issue</th>
						<th width="60">Return</th>
						<th width="">Balance</th>

						<th class="new" width="85">N Allocated</th>
						<th class="new" width="80">N Issue</th>
						<th class="new" width="70">N Return</th>
						<th class="new">N Balance</th>
						</thead>';

						$html_hr .= "<tr><td colspan='16' class='border'>Product ID: ".$prod_id."; Lot: ".$product_array[$prod_id]['lot']."; Product Name: ".$product_array[$prod_id]['product_name_details']."; Crrent stock: ".$product_array[$prod_id]['current_stock']."; Allocated: ".$product_array[$prod_id]['allocated_qnty']."; Available: ".$product_array[$prod_id]['available_qnty']."</td></tr>";
					}else{
						$html_hr = "";
					}
				}

				$issue_qty = ($row[csf("is_dyied_yarn")]==1)?$job_wose_issue_array[$row[csf("job_no")]]:$issue_array[$row[csf("po_break_down_id")]][$prod_id];
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
					}
					else
					{
						$buyer_id=$sales_order_arr[$row[csf('job_no')]]['buyer_id'];
						$buyername=$buy_name_arr[$buyer_id];
						$po_number="";
					}
				}
				else
				{
					$po_number=$po_number_arr[$row[csf("po_break_down_id")]]['po'];
					$buyername=$buy_name_arr[$po_number_arr[$row[csf("job_no")]]['buyer_name']];
					$return_qty = ($row[csf("is_dyied_yarn")]==1)?$job_wose_issue_return_array[$row[csf("job_no")]]:$issue_return_array[$row[csf("po_break_down_id")]][$prod_id];
				}
				$booking_no = implode(",",array_unique(explode(",", $row[csf("booking_no")])));
				echo $html_hr;
				$dyed_type = $product_array[$prod_id]['dyed_type'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td align="center" <? echo ($dyed_type==1)? "class='dyed'":"";?> ><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("id")]; ?></td>
					<td align="center"><? echo $prod_id; ?></td>
					<td align="center"><? echo $row[csf("job_no")]; ?></td>
					<td align="center"><? echo $buyername; ?></td>
					<td align="center"><? echo $po_number; ?></td>
					<td align="center"><? echo $shipping_status[$po_number_arr[$row[csf("po_break_down_id")]]['shiping_status']]; ?></td>
					<td align="center"><? echo $booking_no; ?></td>
					<td align="right"><? echo number_format($row[csf("allocate_qty")], 2); ?></td>
					<td align="right"><? echo number_format($issue_qty, 2); ?></td>
					<td align="right"><? echo number_format($return_qty, 2); ?></td>
					<td align="right">
						<?
						$balance = $balance + $row[csf("allocate_qty")] - $issue_qty + $return_qty;
						echo number_format($balance, 2);
						?>
					</td>
					<?
					$issue_qty_new = $issue_qty;
					$return_qty_new = $return_qty;
					if($issue_qty_new > 0){
						if($row[csf("allocate_qty")] > ($issue_qty_new - $return_qty_new)){
							$new_allocation = $row[csf("allocate_qty")];
						}else{
							$new_allocation = $row[csf("allocate_qty")] + ($issue_qty_new - ($row[csf("allocate_qty")]+$return_qty_new));
						}
					}else{
						$new_allocation = $row[csf("allocate_qty")];
					}
					?>
					<td class="new" align="right"><? echo number_format($new_allocation, 2); ?></td>
					<td class="new" align="right"><? echo number_format($issue_qty_new, 2); ?></td>
					<td class="new" align="right"><? echo number_format($return_qty_new, 2); ?></td>
					<td class="new" align="right">
						<?
						$balance_new = $balance_new + $new_allocation - $issue_qty_new + $return_qty_new;
						echo number_format($balance_new, 2);
						?>
					</td>
				</tr>
				<?
				$dtls_id = $row[csf("id")];
				$dtls_data_update_arr[$dtls_id]["qnty"] += $new_allocation;
				$dtls_data_update_arr[$dtls_id]["po_break_down_id"] = $row[csf("po_break_down_id")];
				$dtls_data_update_arr[$dtls_id]["job_no"] = $row[csf("job_no")];
				$dtls_data_update_arr[$dtls_id]["prod_id"] = $prod_id;
				$dtls_data_update_arr[$dtls_id]["mst_id"] = $row[csf("mst_id")];
				$dtls_data_update_arr[$dtls_id]["issue_qty_new"] = $issue_qty_new;
				$dtls_data_update_arr[$dtls_id]["return_qty_new"] = $return_qty_new;
				$dtls_balance_arr[$prod_id]["balance"] = $balance_new;
				$i++;
			}

			$balance1=0;
			foreach ($dtls_data_update_arr as $dtl_id => $dtl_row) {
				$current_stock = number_format($product_array[$dtl_row["prod_id"]]['current_stock'],2,".","");
				$mst_id = $dtl_row["mst_id"];
				$balance = number_format($dtls_balance_arr[$dtl_row["prod_id"]]["balance"],2,".","");
				$dtl_qnty = number_format($dtl_row["qnty"],2,".","");
				if($current_stock >= $balance){
					$balance1 = $balance;
					$allocation = $dtl_qnty;
					//echo "UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id;<br />";
				}else{
					$allocation = $dtl_qnty;
					$balance1 += $allocation - ($dtl_row["issue_qty_new"]-$dtl_row["return_qty_new"]);

					//echo "UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id and mst_id=$mst_id; <br />";
				}

				$mst_data_update_arr[$dtl_row["mst_id"]]["qnty"] += $allocation;
				$mst_data_update_arr[$dtl_row["mst_id"]]["prod_id"] = $dtl_row["prod_id"];
				$mst_data_update_arr[$dtl_row["mst_id"]]["balance"] = $balance1;
				$mst_data_update_arr[$dtl_row["mst_id"]]["current_stock"] = $product_array[$dtl_row["prod_id"]]['current_stock'];
				$mst_data_update_arr[$dtl_row["mst_id"]]["qnty_break_down"] .= $allocation . "_" . $dtl_row["po_break_down_id"] . "_" . $dtl_row["job_no"] . ",";
			}

			foreach ($mst_data_update_arr as $mst_id => $mst_row) {
				$prod_id = $mst_row["prod_id"];
				$current_stock = number_format($mst_row["current_stock"],2,".","");
				$qnty = number_format($mst_row["qnty"],2,".","");
				$balance = number_format($mst_row["balance"],2,".","");
				$qnty_break_down = trim($mst_row["qnty_break_down"],", ");

				if($current_stock >= $balance){
					$allocation = $qnty;
					//echo "UPDATE inv_material_allocation_mst set qnty='$allocation',qnty_break_down='$qnty_break_down' where id=$mst_id; <br />";
					$prod_arr[$prod_id]["current_stock"] = $current_stock;
					$prod_arr[$prod_id]["allocated"] = $balance;
					$prod_arr[$prod_id]["available"] = $current_stock-$balance;
					$prod_arr[$prod_id]["material_allo"] = $balance;
				}else{
					//echo "UPDATE inv_material_allocation_mst set qnty='$qnty',qnty_break_down='$qnty_break_down' where id=$mst_id; <br />";
					$prod_arr[$prod_id]["current_stock"] = $balance;
					$prod_arr[$prod_id]["allocated"] = $balance;
					$prod_arr[$prod_id]["available"] = 0;
					$prod_arr[$prod_id]["material_allo"] = $balance;
				}
			}

			foreach ($prod_arr as $key => $prod_row) {
				//echo "UPDATE product_details_master set current_stock='".$prod_row['current_stock']."',allocated_qnty='".$prod_row['allocated']."',available_qnty='".$prod_row['available']."' where id=$key;<br />";
			}
		}else{
			echo "<tr><th style='text-align:center;' colspan='16'>No Data Found</th></tr>";
		}
		?>
	</table><br /><br />
	<?
	exit();
}

if ($action=="synchronize_allocation")
{
	extract($_REQUEST);
	$con = connect();

	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_id=str_replace("'","",$company_id);
	$product_id = implode(",",explode(",",str_replace("'","",$product_id)));
	if($product_id == ""){
		die;
	}

	$prod_cond = " and item_id in (".$product_id.")";
	if ($db_type == 0) {
		$sql_allocation = "select a.id,a.mst_id,a.item_id,a.job_no, a.po_break_down_id,group_concat(booking_no) booking_no, sum(a.qnty) as allocate_qty,a.is_dyied_yarn from inv_material_allocation_dtls_b a,product_details_master_b b where a.item_id=b.id and b.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_cond group by a.item_id,a.job_no,a.po_break_down_id,a.is_dyied_yarn,a.id,a.mst_id";
	}else{
		$sql_allocation = "select a.id,a.mst_id,a.item_id,a.job_no, a.po_break_down_id,listagg(cast(a.booking_no as varchar2(4000)), ',') within group (order by a.booking_no) as booking_no, sum(a.qnty) as allocate_qty,a.is_dyied_yarn from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and b.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_cond group by a.item_id,a.job_no,a.po_break_down_id,a.is_dyied_yarn,a.id,a.mst_id";
	}

	$result_allocation = sql_select($sql_allocation);

	$po_break_down_arr = $job_arr =  array();
	foreach ($result_allocation as $row) {
		if($row[csf("is_dyied_yarn")] == 1){
			$job_arr[$row[csf("job_no")]] = "'".$row[csf("job_no")]."'";
		} else {
			$po_break_down_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
		}
		$item_arr[] = $row[csf("item_id")];
	}

	$sql_products = "select a.id, a.company_id, a.lot,a.product_name_details,a.current_stock, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit,dyed_type from product_details_master a where a.item_category_id=1 and company_id=$company_id and id in($product_id) and a.status_active=1 and a.is_deleted=0";

	$product_array=array();
	$productData = sql_select($sql_products);
	foreach( $productData as $prod_val){
		$product_array[$prod_val[csf('id')]]['lot']						= $prod_val[csf('lot')];
		$product_array[$prod_val[csf('id')]]['product_name_details']	= $prod_val[csf('product_name_details')];
		$product_array[$prod_val[csf('id')]]['current_stock']			= $prod_val[csf('current_stock')];
		$product_array[$prod_val[csf('id')]]['allocated_qnty']			= $prod_val[csf('allocated_qnty')];
		$product_array[$prod_val[csf('id')]]['available_qnty']			= $prod_val[csf('available_qnty')];
		$product_array[$prod_val[csf('id')]]['dyed_type']				= $prod_val[csf('dyed_type')];
	}

	$sql_cond = !empty($job_arr)?" and a.job_no_mst in(".implode(",",$job_arr).")":" and a.id in(".implode(",",$po_break_down_arr).")";
	$po_number_arr = array();
	$po_sql = sql_select("select a.id,a.job_no_mst,b.buyer_name, a.file_no,a.grouping,a.po_number,shiping_status from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $sql_cond and a.status_active=1 and a.is_deleted=0");
	foreach ($po_sql as $row) {
		$po_number_arr[$row[csf("id")]]['po'] = $row[csf("po_number")];
		$po_number_arr[$row[csf("id")]]['file'] = $row[csf("file_no")];
		$po_number_arr[$row[csf("id")]]['ref'] = $row[csf("grouping")];
		$po_number_arr[$row[csf("id")]]['job_no'] = $row[csf("job_no_mst")];
		$po_number_arr[$row[csf("job_no_mst")]]['buyer_name'] = $row[csf("buyer_name")];
		$po_number_arr[$row[csf("id")]]['shiping_status'] = $row[csf("shiping_status")];
	}

	$job_no_array = array();
	/*$jobsql = "select a.id, a.job_no,a.buyer_id,a.sales_booking_no, a.style_ref_no,a.within_group from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0";
	$jobData = sql_select($jobsql);
	foreach ($jobData as $row) {
		$sales_order_arr[$row[csf('job_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
		$sales_order_arr[$row[csf('job_no')]]['buyer_id']=$row[csf('buyer_id')];
		$sales_order_arr[$row[csf('job_no')]]['within_group']=$row[csf('within_group')];
	}*/

	$planning_array = array();
	$plan_sql="select b.po_id,b.booking_no,b.dtls_id,b.program_qnty,b.total_prog_qnty,c.id req_id,c.requisition_no,c.prod_id,sum(c.yarn_qnty) req_qnty from (select a.po_id,a.booking_no,a.dtls_id,a.program_qnty,d.program_qnty total_prog_qnty from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls d where a.dtls_id=d.id and a.status_active=1 and d.status_active=1 group by a.po_id,a.booking_no,a.dtls_id,a.program_qnty,d.program_qnty) b,ppl_yarn_requisition_entry c where b.dtls_id=c.knit_id and c.status_active=1  and c.prod_id in($product_id) group by b.po_id,b.booking_no,b.dtls_id,b.program_qnty,b.total_prog_qnty,c.id,c.requisition_no,c.prod_id";
	$planData = sql_select($plan_sql);
	$requisition_qnty_array=array();
	foreach ($planData as $row) {
		$total_prog_qnty = $row[csf('total_prog_qnty')];
		$po_program_qnty = $row[csf('program_qnty')];
		$perc 			 = ($po_program_qnty / $total_prog_qnty) * 100;
		$req_qnty 		 = ($row[csf('req_qnty')] / 100)*$perc;

		$requisition_qnty_array[$row[csf('po_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]]+=$req_qnty;

		$planning_array[$row[csf('po_id')]][$row[csf('booking_no')]][$row[csf('requisition_no')]][$row[csf('prod_id')]]['req_qnty'] += $req_qnty;
		$planning_array[$row[csf('po_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['requisition_no'] .= $row[csf('requisition_no')].",";
		$planning_array[$row[csf('po_id')]][$row[csf('prod_id')]]['req_id'] .= $row[csf('req_id')].",";
		$planning_array[$row[csf('po_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]][] = $row[csf('booking_no')];

	}


	$issue_array = $job_wose_issue_array = $issue_basis_arr = array();
	$prod_cond2 = " and c.prod_id in (".$prod_id.")";
	$sql_issue = "select a.id,a.issue_basis,a.issue_purpose,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type, sum(c.quantity) as issue_qty,b.job_no from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and (a.issue_purpose in(1,2) or a.issue_purpose in(1,2,15) and b.job_no is not null) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 and c.prod_id=$product_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by a.id,a.issue_basis,a.issue_purpose,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type,b.job_no";

	$result_issue = sql_select($sql_issue);
	$issue_array_req=$booking_arr=array();
	foreach ($result_issue as $row) {

		$issue_job[$row[csf("id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] = $row[csf("job_no")];
		$issue_basis_arr[$row[csf("id")]]["basis"] = $row[csf("issue_basis")];
		$issue_basis_arr[$row[csf("id")]]["issue_id"] = $row[csf("id")];

		if($row[csf('dyed_type')] == 1){
			$issue_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
			$issue_arr[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
		}else{
			$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			if($row[csf("issue_basis")] == 3){
				$booking_arr = $planning_array[$row[csf('po_breakdown_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]];
				$booking_arr = array_unique($booking_arr);
				foreach ($booking_arr as $booking) {
					if($booking != ""){
						$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
						$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_id"][] = $row[csf("id")];
						$issue_array_issue_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking][$row[csf("id")]][] = $row[csf("requisition_no")];
					}
				}
				$requisition_wise_issue_array[$row[csf("po_breakdown_id")]][$row[csf("requisition_no")]][$row[csf("prod_id")]] += $row[csf("issue_qty")];
			}else{
				$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
				$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_id"][] = $row[csf("id")];
			}
		}
		$job_wose_issue_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']][$row[csf("prod_id")]] += $row[csf("issue_qty")];
	}

	$issue_return_array = $issue_return_req_array = $job_wose_issue_return_array = array();
	$sql_return = "Select b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose, sum(c.quantity) as issue_return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and c.trans_type=4 and a.entry_form=9 and b.id=c.trans_id and b.item_category=1 and c.issue_purpose in(1,2,7,15,38,46) and c.prod_id=$product_id and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose";
	$result_return = sql_select($sql_return);
	foreach ($result_return as $row) {
		$issue_basis = $issue_basis_arr[$row[csf("issue_id")]]["basis"];
		if($issue_basis == 3){
			$issue_id = $issue_basis_arr[$row[csf("issue_id")]]["issue_id"];
			$issue_return_req_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$issue_id] = $row[csf("issue_return_qty")];
		}else{
			$issue_job = $issue_job[$row[csf("issue_id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]];
			if($issue_job!="" && ($row[csf("issue_purpose")]==7 || $row[csf("issue_purpose")]==15 || $row[csf("issue_purpose")]==38 || $row[csf("issue_purpose")]==46)){
				$issue_return_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
			}else{
				$issue_return_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
			}
		}

		$job_wose_issue_return_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
	}
	//echo "10**";
	$i = 1;
	$balance = '';
	$prod_arr = array();

	if(!empty($result_allocation)){
		$balance = $balance_new = $issue_qty = $return_qty = 0;
		$item_arr = $mst_data_update_arr = array();
		foreach ($result_allocation as $row) {
			$prod_id = $row[csf("item_id")];
			$dtls_id = $row[csf("id")];
			if(empty($item_arr)){
				$item_arr[] = $prod_id;
			}else{
				if(!in_array($prod_id, $item_arr)){
					$i = 1;
					$balance = $balance_new = 0;
					unset($item_arr);
					$item_arr[] = $prod_id;
				}
			}

			if($row[csf("is_dyied_yarn")] == 1){
				$issue_qty = $job_wose_issue_array[$row[csf("job_no")]][$prod_id];
			}else{
				$issue_qty=0;

				if($row[csf("po_break_down_id")]==""){
					$issue_basis = array_unique($issue_arr[$row[csf('job_no')]][$prod_id]);
				}else{
					$issue_basis = array_unique($issue_arr[$row[csf("po_break_down_id")]][$prod_id]);
				}

				foreach ($issue_basis as $basis) {
					if($basis==3 || $basis==1){
						if($basis==1){
							$issue_qty += $issue_array_req[$row[csf("po_break_down_id")]][$prod_id]["issue_qty"];
						}else{
							if($row[csf("booking_no")] != ""){
								$booking_nos = explode(",",$row[csf("booking_no")]);
								$issue_qty=0;
								foreach ($booking_nos as $booking_row) {
									$issue_qty += $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_qty"];
									$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_id"]);
									$requisitions="";
									foreach ($issue_ids as $issue_id) {
										$requisitions_arr = $issue_array_issue_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row][$issue_id];
										foreach ($requisitions_arr as $r_row) {
											$requisitions .= $r_row.",";
										}
									}
								}
							}
						}
					}else{
						$issue_qty += $issue_array[$row[csf('job_no')]][$prod_id]["issue_qty"];
					}
				}
			}

			$issue_qty = ($row[csf('job_no')]!="")?$issue_qty:0;
			$sales_booking_no=$sales_order_arr[$row[csf('job_no')]]['sales_booking_no'];

			if($sales_booking_no!="")
			{
				$return_qty = 0;
			}
			else
			{
				if($row[csf("is_dyied_yarn")] == 1){
					$return_qty = $job_wose_issue_return_array[$row[csf("job_no")]][$prod_id];
				}else{

					foreach ($issue_basis as $basis) {
						$return_qty=0;
						if($basis==3){
							$booking_nos = explode(",",$row[csf("booking_no")]);
							foreach ($booking_nos as $booking_row) {
								$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_id"]);
								$return_qty=0;
								foreach ($issue_ids as $issue_id) {
									$return_qty += $issue_return_req_array[$row[csf('po_break_down_id')]][$prod_id][$issue_id];
								}
							}
						}else{
							$return_qty = $issue_return_array[$row[csf("po_break_down_id")]][$prod_id];
						}
					}
				}
				$return_qty = ($row[csf('job_no')]!="")?$return_qty:0;
			}
			$booking_no = implode(",",array_unique(explode(",", $row[csf("booking_no")])));
			$balance = $balance + (number_format($row[csf("allocate_qty")], 2,".","") + number_format($return_qty, 2,".","")) - number_format($issue_qty, 2,".","");

			$issue_qty_new = number_format($issue_qty, 2,".","");
			$return_qty_new = number_format($return_qty, 2,".","");

			$requisitions = array_unique(explode(",",rtrim($requisitions,", ")));
			$requisition_nos = "";
			$total_requisition_qnty=0;
			$requisiton_arr=array();
			foreach ($requisitions as $req_no) {
				$total_requisition_qnty += $planning_array[$row[csf("po_break_down_id")]][$booking_no][$req_no][$prod_id]['req_qnty'];

				$requisition_issue_qnty = $requisition_wise_issue_array[$row[csf("po_break_down_id")]][$req_no][$prod_id];
				$req_qnty = $requisition_qnty_array[$row[csf('po_break_down_id')]][$req_no][$prod_id];
				$requisition_nos .= "Req. no=".$req_no.", Req. Qnty=".number_format($req_qnty,2).",Issue=".number_format($requisition_issue_qnty,2).",\n";
				$requisiton_arr[$dtls_id][$booking_no][$row[csf("po_break_down_id")]][$req_no] += $req_qnty;
				$dtls_data_update_arr[$dtls_id]["po_requisition_qty"] = $requisiton_arr;
				if($req_qnty<$requisition_issue_qnty){
					$inc_requisition_qnty = number_format($requisition_issue_qnty-$req_qnty,2,".","");
				}
			}

			$requisition_qnty = $total_requisition_qnty;
			$requisition_nos=rtrim($requisition_nos,", ");

			if($issue_qty_new > 0){
				if($requisition_qnty < $issue_qty_new){
					$new_allocation = number_format(($issue_qty_new-$return_qty_new),2,".","");
				}else{
					$new_allocation = number_format(($requisition_qnty-$return_qty_new),2,".","");
				}
			}else{
				$new_allocation = number_format($row[csf("allocate_qty")],2,".","");
			}

			$balance_new = $balance_new + ($new_allocation + $return_qty_new) - $issue_qty_new;

			$dtls_data_update_arr[$dtls_id]["qnty"] += $new_allocation;
			$dtls_data_update_arr[$dtls_id]["pre_qnty"] += $row[csf("allocate_qty")];
			$dtls_data_update_arr[$dtls_id]["po_break_down_id"] = $row[csf("po_break_down_id")];
			$dtls_data_update_arr[$dtls_id]["job_no"] = $row[csf("job_no")];
			$dtls_data_update_arr[$dtls_id]["prod_id"] = $prod_id;
			$dtls_data_update_arr[$dtls_id]["mst_id"] = $row[csf("mst_id")];
			$dtls_data_update_arr[$dtls_id]["booking_no"] = $booking_no;
			$dtls_data_update_arr[$dtls_id]["issue_qty_new"] = $issue_qty_new;
			$dtls_data_update_arr[$dtls_id]["return_qty_new"] = $return_qty_new;
			$dtls_data_update_arr[$dtls_id]["requisition_ids"] = rtrim($planning_array[$row[csf("po_break_down_id")]][$prod_id]['req_id'],", ");
			$dtls_data_update_arr[$dtls_id]["requisition_nos"] = rtrim($planning_array[$row[csf('po_break_down_id')]][$booking_no][$prod_id]['requisition_no'],", ");
			$dtls_data_update_arr[$dtls_id]["requisition_qty"] = $requisition_qnty;

			$dtls_balance_arr[$prod_id]["balance"] = $balance_new;
			$i++;
		}

		//echo "10**";
		$balance1=0;
		$item_arr=$req_a=$req_issue_qnty=array();
		foreach ($dtls_data_update_arr as $dtl_id => $dtl_row) {
			$current_stock = $product_array[$dtl_row["prod_id"]]['current_stock'];
			$mst_id   = $dtl_row["mst_id"];
			$booking_no   = $dtl_row["booking_no"];
			$balance  = $dtls_balance_arr[$dtl_row["prod_id"]]["balance"];
			$dtl_qnty = $dtl_row["qnty"];
			$pre_qnty = $dtl_row["pre_qnty"];

			$requisitions 	 = array_unique(explode(",",$dtl_row["requisition_ids"]));
			$requisition_qty = $dtl_row["requisition_qty"];

			$requisition_nos = array_unique(explode(",",$dtl_row["requisition_nos"]));

			$po_requisition_qty = $dtl_row["po_requisition_qty"];

			if($current_stock >= $balance){
				if(empty($item_arr)){
					$item_arr[] = $dtl_row["prod_id"];
					$balance1 = $balance;
				}else{
					if(!in_array($dtl_row["prod_id"], $item_arr)){
						$balance1 = $balance;
						unset($item_arr);
						$item_arr[] = $dtl_row["prod_id"];
					}else{
						$balance1 = $balance;
					}
				}

				if($dtl_row["issue_qty_new"] > $requisition_qty){
					$allocation = number_format($dtl_qnty,2,".","");
					//echo "10**UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id;<br />";
					$dtls_id_array[]=$dtl_id;
					$data_array_dtls_update[$dtl_id]=explode("*",("".$allocation."*'999'*'".$pc_date_time."'"));
					//$update_allocation_dtls=execute_query("UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id");
					foreach ($requisition_nos as $req_no) {
						$req_issue_qnty = $requisition_wise_issue_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]];
						$po_req_qnty = $requisition_qnty_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]];
						$req_a[$req_no][$dtl_row["prod_id"]] += $po_req_qnty + ($req_issue_qnty-$po_req_qnty);
						$req_qnty = $requisition_qnty_array[$row[csf('po_break_down_id')]][$req_no][$prod_id];
					}
				}else{
					$allocation = number_format($dtl_qnty,2,".","");
					//echo "10**UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id;<br />";

					$dtls_id_array[]=$dtl_id;
					$data_array_dtls_update[$dtl_id]=explode("*",("".$allocation."*'999'*'".$pc_date_time."'"));
					//$update_allocation_dtls=execute_query("UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id");
					foreach ($requisition_nos as $req_no) {
						$req_a[$req_no][$dtl_row["prod_id"]] += $requisition_qnty_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]];
					}
				}

			}else{
				if($dtl_row["issue_qty_new"] > 0){
					$allocation = number_format(($dtl_row["issue_qty_new"]-$dtl_row["return_qty_new"]),2,".","");
					$balance1 += $allocation;
				}else{
					$allocation = 0;
				}

				$dtls_id_array[]=$dtl_id;
				$data_array_dtls_update[$dtl_id]=explode("*",("".$allocation."*'999'*'".$pc_date_time."'"));

				foreach ($requisition_nos as $req_no) {
					$req_issue_qnty = $requisition_wise_issue_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]]*1;
					$po_req_qnty = $requisition_qnty_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]];
					$req_a[$req_no][$dtl_row["prod_id"]] += $po_req_qnty + ($req_issue_qnty-$po_req_qnty);
				}

				//echo "10**UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id and mst_id=$mst_id; <br />";
				//$update_allocation_dtls=execute_query("UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id");

			}

			$mst_data_update_arr[$dtl_row["mst_id"]]["qnty"] += $allocation;
			$mst_data_update_arr[$dtl_row["mst_id"]]["prod_id"] = $dtl_row["prod_id"];
			$mst_data_update_arr[$dtl_row["mst_id"]]["balance"] = $balance1;
			$mst_data_update_arr[$dtl_row["mst_id"]]["current_stock"] = $product_array[$dtl_row["prod_id"]]['current_stock'];
			$mst_data_update_arr[$dtl_row["mst_id"]]["qnty_break_down"] .= number_format($allocation,2,".","") . "_" . $dtl_row["po_break_down_id"] . "_" . $dtl_row["job_no"] . ",";
		}

		$prod_arr=array();
		foreach ($mst_data_update_arr as $mst_id => $mst_row) {
			$prod_id = $mst_row["prod_id"];
			$current_stock = $mst_row["current_stock"];
			$qnty = number_format($mst_row["qnty"],2,".","");
			$balance = $mst_row["balance"];
			$qnty_break_down = trim($mst_row["qnty_break_down"],", ");

			if($current_stock >= $balance){
				$allocation = $qnty;
				//echo "10**UPDATE inv_material_allocation_mst set qnty='$allocation',qnty_break_down='$qnty_break_down' where id=$mst_id; <br />";
				//$update_allocation_mst=execute_query("UPDATE inv_material_allocation_mst set qnty='$allocation',qnty_break_down='$qnty_break_down' where id=$mst_id");

				$mst_id_array[]=$mst_id;
				$data_array_mst_update[$mst_id]=explode("*",("".$qnty."*'".$qnty_break_down."'*'999'*'".$pc_date_time."'"));

				$prod_arr[$prod_id]["current_stock"] = $current_stock;
				$prod_arr[$prod_id]["allocated"] = number_format($balance,2,".","");
				$prod_arr[$prod_id]["available"] = number_format(($current_stock-$balance),2,".","");
			}else{
				if($current_stock < $balance){
					//echo "10**UPDATE inv_material_allocation_mst set qnty='$qnty',qnty_break_down='$qnty_break_down' where id=$mst_id;<br />";
					//$update_allocation_dtls=execute_query("UPDATE inv_material_allocation_mst set qnty='$qnty',qnty_break_down='$qnty_break_down' where id=$mst_id");

					$mst_id_array[]=$mst_id;
					$data_array_mst_update[$mst_id]=explode("*",("".$qnty."*'".$qnty_break_down."'*'999'*'".$pc_date_time."'"));

					$prod_arr[$prod_id]["current_stock"] = $current_stock;
					$prod_arr[$prod_id]["allocated"] = 0;
					$prod_arr[$prod_id]["available"] = number_format($current_stock,2,".","");
				}
			}
		}

		foreach ($req_a as $req_no => $product) {
			if($req_no!=""){
				foreach ($product as $product_id => $req_qnty) {
					//echo "10**UPDATE ppl_yarn_requisition_entry set yarn_qnty=".number_format($req_qnty,2,".","")." where requisition_no=$req_no and prod_id=$product_id;<br />";
					execute_query("UPDATE ppl_yarn_requisition_entry set yarn_qnty=".number_format($req_qnty,2,".","")." where requisition_no=$req_no and prod_id=$product_id");
					//$req_no_array[]=$req_no;
					//$data_array_requisition_update[$req_no]=explode("*",("".$req_qnty."*'999'*'".$pc_date_time."'"));
				}
			}
		}

		foreach ($prod_arr as $pr_id => $prod_row) {
			//echo "10**UPDATE product_details_master set allocated_qnty='".$prod_row['allocated']."',available_qnty='".$prod_row['available']."' where id=$pr_id;<br />";
			//$update_allocation_mst=execute_query("UPDATE product_details_master set allocated_qnty='".$prod_row['allocated']."',available_qnty='".$prod_row['available']."' where id=$pr_id");
			$prID_array[]=$pr_id;
			$data_array_product_update[$pr_id]=explode("*",("".$prod_row['allocated']."*".$prod_row['available']."*'999'*'".$pc_date_time."'"));
		}

	}else{
		echo "Data Synchronize is not completed successfully";
	}
	//echo "<pre>";
	//print_r($data_array_dtls_update);
	//echo bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_trans_update,$trans_array);
	$field_array_mst_update="qnty*qnty_break_down*updated_by*update_date";
	$field_array_dtls_update="qnty*updated_by*update_date";
	$field_array_product_update="allocated_qnty*available_qnty*updated_by*update_date";
	//$field_array_requisiton_update="yarn_qnty*updated_by*update_date";

	$rID=execute_query(bulk_update_sql_statement("inv_material_allocation_mst","id",$field_array_mst_update,$data_array_mst_update,$mst_id_array),1);
	$rID2=execute_query(bulk_update_sql_statement("inv_material_allocation_dtls","id",$field_array_dtls_update,$data_array_dtls_update,$dtls_id_array),1);
	$rID3=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_product_update,$data_array_product_update,$prID_array),1);
	//$rID4=execute_query(bulk_update_sql_statement("ppl_yarn_requisition_entry","requisition_no",$field_array_requisiton_update,$data_array_requisition_update,$req_no_array),1);

	//echo "10**".$rID ."&&". $rID2 ."&&". $rID3;die;
	if($db_type==0)
	{
		if($rID && $rID2 && $rID3)
		{
			mysql_query("COMMIT");
			echo "0**Data Synchronize is completed successfully";
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**Data Synchronize is not completed successfully";
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID && $rID2 && $rID3)
		{
			oci_commit($con);
			echo "0**Data Synchronize is completed successfully";
		}
		else
		{
			oci_rollback($con);
			echo "10**Data Synchronize is not completed successfully**$data_array_prod_update";
		}
	}

	disconnect($con);
	die;

}

?>
