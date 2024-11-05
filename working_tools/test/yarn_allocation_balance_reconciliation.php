<?
error_reporting(1);
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
if ($db_type == 0) {
	mysql_query("BEGIN");
}
$prod_id = explode(",",$_GET["prod_id"]);

$prod_cond = $_GET["prod_id"] != ""?" and item_id in (".implode(",",$prod_id).")":"";
$prod_cond2 = $_GET["prod_id"] != ""?" and c.prod_id in (".implode(",",$prod_id).")":"";
$prod_cond3 = $_GET["prod_id"] != ""?" and b.prod_id in(".implode(",",$prod_id).")":"";

$buy_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
if ($db_type == 0) {
	$sql_allocation = "select id,mst_id,item_id,job_no, po_break_down_id,group_concat(booking_no) booking_no, sum(qnty) as allocate_qty,is_dyied_yarn,allocation_date from inv_material_allocation_dtls where status_active=1 and is_deleted=0 $prod_cond group by id,mst_id,item_id,job_no,po_break_down_id,is_dyied_yarn,allocation_date,id,mst_id";
}else{
	$sql_allocation = "select a.id dtls_id,a.mst_id,a.item_id,a.job_no, a.po_break_down_id,listagg(cast(a.booking_no as varchar2(4000)), ',') within group (order by a.booking_no) as booking_no,
	sum(a.qnty) as allocate_qty,a.is_dyied_yarn,b.lot,b.product_name_details,b.current_stock,b.allocated_qnty,b.available_qnty
	from inv_material_allocation_dtls a, product_details_master b
	where a.item_id=b.id and a.status_active=1 and a.is_deleted=0 $prod_cond group by a.id,a.item_id,a.job_no,a.po_break_down_id,a.is_dyied_yarn,a.mst_id,b.lot,b.product_name_details,b.current_stock,b.allocated_qnty,b.available_qnty"; // and item_id in($p_ids)  and a.item_id in(422113)
}
//echo $sql_allocation;
$result_allocation = sql_select($sql_allocation);
/*echo "<pre>";
print_r($result_allocation);die;*/


$po_break_down_arr = $job_arr =  array();
foreach ($result_allocation as $row) {
	if($row[csf("is_dyied_yarn")] == 1){
		$job_arr[$row[csf("job_no")]] = "'".$row[csf("job_no")]."'";
	} else {
		$po_break_down_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
	}
	$item_arr[$row[csf("item_id")]] = $row[csf("item_id")];
	$product_type[$row[csf("item_id")]] = $row[csf("is_dyied_yarn")];
	$product_array[$row[csf("item_id")]]['current_stock'] = $row[csf("current_stock")];
}

$sql_cond = !empty($job_arr)?" and a.job_no_mst in(".implode(",",$job_arr).")":" and a.id in(".implode(",",$po_break_down_arr).")";
$po_number_arr = array();
$po_sql = sql_select("select a.id,a.job_no_mst,c.booking_no,b.buyer_name, a.file_no,a.grouping,a.po_number
	from wo_booking_dtls c,wo_po_break_down a,wo_po_details_master b
	where c.po_break_down_id=a.id and a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0
	group by a.id,a.job_no_mst,c.booking_no,b.buyer_name, a.file_no,a.grouping,a.po_number");
foreach ($po_sql as $row) {
	$po_number_arr[$row[csf("id")]]['po'] = $row[csf("po_number")];
	$po_number_arr[$row[csf("id")]]['file'] = $row[csf("file_no")];
	$po_number_arr[$row[csf("id")]]['ref'] = $row[csf("grouping")];
	$po_number_arr[$row[csf("id")]]['job_no'] = $row[csf("job_no_mst")];
	$po_number_arr[$row[csf("job_no_mst")]]['buyer_name'] = $row[csf("buyer_name")];
	$booking_job[$row[csf("booking_no")]] = $row[csf("job_no_mst")];
}

if(!empty($item_arr))
{
	if($db_type==2 && count($item_arr)>1000)
	{
		$prod_id_cond=" and (";

		$proIdArr=array_chunk(array_unique($item_arr),999);
		foreach($proIdArr as $ids)
		{
			$ids=implode(",",$ids);
			$prod_id_cond.=" c.prod_id in($ids) or ";
		}

		$prod_id_cond=chop($prod_id_cond,'or ');
		$prod_id_cond.=")";
	}
	else
	{
		$prod_id_cond=" and c.prod_id in (".implode(",",$item_arr).")";
	}
}

$planning_array = array();
$plan_sql="select b.po_id,b.booking_no,b.dtls_id,b.program_qnty,b.total_prog_qnty,c.id req_id,c.requisition_no,c.prod_id,sum(c.yarn_qnty) req_qnty from (select a.po_id,a.booking_no,a.dtls_id,a.program_qnty,d.program_qnty total_prog_qnty from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls d where a.dtls_id=d.id and a.status_active=1 and d.status_active=1 group by a.po_id,a.booking_no,a.dtls_id,a.program_qnty,d.program_qnty) b,ppl_yarn_requisition_entry c where b.dtls_id=c.knit_id and c.status_active=1 $prod_id_cond group by b.po_id,b.booking_no,b.dtls_id,b.program_qnty,b.total_prog_qnty,c.id,c.requisition_no,c.prod_id order by b.dtls_id asc";

$planData = sql_select($plan_sql);
$requisition_qnty_array=array();
foreach ($planData as $row) {
	$total_prog_qnty = $row[csf('total_prog_qnty')];
	$po_program_qnty = $row[csf('program_qnty')];
	$perc 			 = ($po_program_qnty / $total_prog_qnty) * 100;
	$req_qnty 		 = ($row[csf('req_qnty')] / 100)*$perc;

	$requisition_qnty_array[$po_number_arr[$row[csf('po_id')]]['job_no']][$row[csf('requisition_no')]][$row[csf('prod_id')]]+=$req_qnty;
	$job_req_info[$po_number_arr[$row[csf('po_id')]]['job_no']][$row[csf('prod_id')]]['req_qnty'] +=$req_qnty;
	$job_req_info[$po_number_arr[$row[csf('po_id')]]['job_no']][$row[csf('prod_id')]]['req_no'] .= $row[csf('requisition_no')].",";

	$planning_array[$row[csf('po_id')]][$row[csf('booking_no')]][$row[csf('requisition_no')]][$row[csf('prod_id')]]['req_qnty'] += $req_qnty;
	$planning_array[$row[csf('po_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['requisition_no'] .= $row[csf('requisition_no')].",";
	$planning_array[$row[csf('po_id')]][$row[csf('prod_id')]]['req_id'] .= $row[csf('req_id')].",";
	$planning_array[$row[csf('po_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]][]=$row[csf('booking_no')];
	$requisition_booking[$row[csf('requisition_no')]][$row[csf('prod_id')]] = $row[csf('booking_no')];
}
/*echo "<pre>";
print_r($job_req_info);
echo "</pre>";
die;*/
$issue_array = $job_wose_issue_array = $issue_basis_arr = array();
$sql_issue = "select a.id,a.issue_basis,a.issue_purpose,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type, sum(c.quantity) as issue_qty,b.job_no from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and (a.issue_purpose in(1,2) or a.issue_purpose in(1,2,15) and b.job_no is not null) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 $prod_id_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by a.id,a.issue_basis,a.issue_purpose,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type,b.job_no";

$result_issue = sql_select($sql_issue);
$issue_array_req=$booking_arr=array();
foreach ($result_issue as $row) {

	$issue_job[$row[csf("id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] = $row[csf("job_no")];
	$issue_basis_arr[$row[csf("id")]]["basis"] = $row[csf("issue_basis")];
	$issue_basis_arr[$row[csf("id")]]["issue_id"] = $row[csf("id")];
	$issue_requisition_arr[$row[csf("id")]]["requisition_no"][] = $row[csf("requisition_no")];
	$issue_requisition_arr[$row[csf("id")]]["job_no"][] = $row[csf("job_no")];

	if($row[csf('dyed_type')] == 1){

		if($row[csf("issue_basis")] == 3 || $row[csf("issue_basis")] == 8){

			$job_no = $booking_job[$requisition_booking[$row[csf('requisition_no')]][$row[csf("prod_id")]]];
		}else{
			$job_no = $row[csf("job_no")];
		}

		$issue_array[$job_no][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
		$requisition_wise_issue_array[$job_no][$row[csf("requisition_no")]][$row[csf("prod_id")]] += $row[csf("issue_qty")];

	}else{
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
						$issue_array_issue_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking][$row[csf("id")]][] = $row[csf("requisition_no")];
					}
				}
				$requisition_wise_issue_array[$row[csf("po_breakdown_id")]][$row[csf("requisition_no")]][$row[csf("prod_id")]] += $row[csf("issue_qty")];
			}

		}else{

			$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
			$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_id"][] = $row[csf("id")];
		}
	}

	//$job_wose_issue_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']] += $row[csf("issue_qty")];
}



$issue_return_array = $issue_return_req_array = $job_wose_issue_return_array = array();
$sql_return = "Select a.booking_no requisition_no,b.issue_id,c.po_breakdown_id,c.prod_id, sum(c.quantity) as issue_return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and c.trans_type=4 and a.entry_form=9 and b.id=c.trans_id and b.item_category=1 and c.issue_purpose in(1,2) $prod_cond2 and a.status_active=1 and b.status_active=1 and c.status_active=1 group by a.booking_no,b.issue_id,c.po_breakdown_id,c.prod_id";
$result_return = sql_select($sql_return);
foreach ($result_return as $row) {
	$dyed_type = $product_type[$row[csf("prod_id")]];
	if($dyed_type == 1){
		$issue_basis = $issue_basis_arr[$row[csf("issue_id")]]["basis"];
		if($issue_basis == 3){

			$job_no = $booking_job[$requisition_booking[$row[csf('requisition_no')]][$row[csf("prod_id")]]];
			$job_wose_issue_return_array[$job_no][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];

		}else{
			$job_no = $issue_requisition_arr[$row[csf("issue_id")]]["job_no"];
			$job_wose_issue_return_array[$job_no][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
		}
	}
	else
	{
		$issue_basis = $issue_basis_arr[$row[csf("issue_id")]]["basis"];
		if($issue_basis == 3 || $issue_basis == 8){
			$issue_id = $issue_basis_arr[$row[csf("issue_id")]]["issue_id"];
			$issue_return_po_array[$issue_id][$row[csf('prod_id')]] .= $row[csf('po_breakdown_id')].",";
			$issue_return_req_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$issue_id] = $row[csf("issue_return_qty")];
		}else{
			$issue_job = $issue_job[$row[csf("issue_id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]];
			if($issue_job!="" && ($row[csf("issue_purpose")]==7 || $row[csf("issue_purpose")]==15 || $row[csf("issue_purpose")]==38 || $row[csf("issue_purpose")]==46)){
				$issue_return_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
			}else{
				$issue_return_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
			}
		}
	}


	//$job_wose_issue_return_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']] += $row[csf("issue_return_qty")];
}

/*echo "<pre>";
print_r($issue_return_req_array);
echo "</pre>";*/


$ydsw_sql = "select d.job_no,d.product_id,sum(d.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_dtls d where d.status_active=1 and d.is_deleted=0 $prod_cond4 group by d.job_no,d.product_id";

$wo_result = sql_select($ydsw_sql);

$work_order_data = array();
foreach ($wo_result as $row) {
	$work_order_data[$row[csf("job_no")]][$row[csf("product_id")]] += $row[csf("yarn_wo_qty")];
}


?>
<style type="text/css">
	table.rpt_table { width: 100%; margin: auto; font-family: arial; font-size: 12px; }
	table.rpt_table tr td{ background-color: #E9F3FF; }
	.global { background-color: rgba(52,168,83,.5) }
	table.rpt_table tr td.new { background-color: rgba(52,168,83,.2);}
	.border{border-top: 1px solid; background-color: #fff !important; font-weight: bold; font-size: 11px; }
	thead th{  background-color: #ccc !important; }
	.fonts{ font-family: Tahoma; font-size: 12px; margin-bottom: 10px; }
</style>
<table border="1" cellpadding="1" cellspacing="0" class="rpt_table" rules="all">
	<?
	$i = 1;
	$balance = '';
	$prod_arr = $requisition_arr = array();
	if(!empty($result_allocation)){
		$balance = $balance_new = $issue_qty = 0;
		$html_hr="";
		$item_arr = $mst_data_update_arr = array();

		foreach ($result_allocation as $row) {
			$prod_id = $row[csf("item_id")];
			if(empty($item_arr)){
				$item_arr[] = $prod_id;
			}else{
				if(!in_array($prod_id, $item_arr)){
					$i = 1;
					$balance = $balance_new = 0;
					unset($item_arr);
					$item_arr[] = $prod_id;
				}else{
					$html_hr = "";
				}
			}

			$issue_basis = $issue_arr[$row[csf("po_break_down_id")]][$prod_id];
			if($row[csf("is_dyied_yarn")] == 1){
				$issue_qty = $issue_array[$row[csf("job_no")]][$prod_id]["issue_qty"];
			}else{
				//print_r($issue_basis);
				if($issue_basis==3 || $issue_basis==8){
					if($row[csf("booking_no")] != ""){
						$issue_qty = $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$row[csf("booking_no")]]["issue_qty"];
					}
				}else{
					$issue_qty = $issue_array[$row[csf("po_break_down_id")]][$prod_id]["issue_qty"];
				}
			}

			$po_number=$po_number_arr[$row[csf("po_break_down_id")]]['po'];
			$buyername=$buy_name_arr[$po_number_arr[$row[csf("job_no")]]['buyer_name']];

			if($row[csf("is_dyied_yarn")] == 1){
				$return_qty = $job_wose_issue_return_array[$row[csf("job_no")]][$prod_id];
			}else{
				if($issue_basis==3){
					if($row[csf("booking_no")] != ""){
						$issue_ids = $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$row[csf("booking_no")]]["issue_id"];
						$return_qty=0;
						foreach ($issue_ids as $issue_id) {
							$return_qty += $issue_return_req_array[$row[csf('po_break_down_id')]][$prod_id][$issue_id];
						}
					}
				}else{
					$return_qty += $issue_return_array[$row[csf("po_break_down_id")]][$prod_id];
				}
			}

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

			$balance_new = $balance_new + $new_allocation - $issue_qty_new + $return_qty_new;
			$dtls_id = $row[csf("dtls_id")];
			$dtls_data_update_arr[$dtls_id]["qnty"] += $new_allocation;
			$dtls_data_update_arr[$dtls_id]["po_break_down_id"] = $row[csf("po_break_down_id")];
			$dtls_data_update_arr[$dtls_id]["job_no"] = $row[csf("job_no")];
			$dtls_data_update_arr[$dtls_id]["prod_id"] = $prod_id;
			$dtls_data_update_arr[$dtls_id]["mst_id"] = $row[csf("mst_id")];
			$dtls_data_update_arr[$dtls_id]["issue_qty_new"] = $issue_qty_new;
			$dtls_data_update_arr[$dtls_id]["return_qty_new"] = $return_qty_new;
			$dtls_data_update_arr[$dtls_id]["balance"] = $balance_new;
			$dtls_balance_arr[$prod_id]["balance"] = $balance_new;
			$i++;
		}


		echo "<div class='fonts'>";
		foreach ($dtls_data_update_arr as $dtl_id => $dtl_row) {
			$current_stock = number_format($product_array[$dtl_row["prod_id"]]['current_stock'],2,".","");
			$mst_id = $dtl_row["mst_id"];
			$balance = number_format($dtls_balance_arr[$dtl_row["prod_id"]]["balance"],2,".","");
			$dtl_qnty = number_format($dtl_row["qnty"],2,".","");
			$allocation = number_format($dtl_qnty,2,".","");
			$dtls_data_arr[$dtl_id]["allocation"] = $allocation;
		}
		echo "</div>";


		$balance = $balance_new = $issue_qty = $return_qty_wo=0;
		$item_arr = $mst_data_update_arr = $dtls_data_update_arr = array();
		foreach ($result_allocation as $row) {
			$prod_id = $row[csf("item_id")];
			if(empty($item_arr)){
				$item_arr[] = $prod_id;
				$html_hr = "<tr><td colspan='16' class='border'>Product ID: ".$prod_id."; Lot: ".$row[csf('lot')]."; Product Name:".$row[csf('product_name_details')]."; Crrent stock: ".number_format($row[csf('current_stock')],2,".","")."; Allocated: ".number_format($row[csf('allocated_qnty')],2,".","")."; Available: ".number_format($row[csf('available_qnty')],2,".","")."</td></tr>";
				$html_hr .= '<thead>
				<th width="25">SL</th>
				<th width="25">DTLS</th>
				<th width="60">Product</th>
				<th width="110">Job/FSO NO.</th>
				<th width="105">Buyer</th>
				<th width="150">Order ID</th>
				<th width="120">Booking No.</th>
				<th width="75">Allocated</th>
				<th width="70">Issue</th>
				<th width="60">Rtn</th>
				<th width="">Balance</th>

				<th class="new" width="75">Allocated</th>
				<th class="new" width="75">Requisition</th>
				<th class="new" width="70">Issue</th>
				<th class="new" width="60">Rtn</th>
				<th class="new" width="">Balance</th>
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
					<th width="110">Job/FSO NO.</th>
					<th width="105">Buyer</th>
					<th width="150">Order ID</th>
					<th width="120">Booking No.</th>
					<th width="75">Allocated</th>
					<th width="70">Issue</th>
					<th width="60">Rtn</th>
					<th width="">Balance</th>

					<th class="new" width="75">Allocated</th>
					<th class="new" width="75">Requisition</th>
					<th class="new" width="70">Issue</th>
					<th class="new" width="60">Rtn</th>
					<th class="new" width="">Balance</th>
					</thead>';

					$html_hr .= "<tr><td colspan='16' class='border'>Product ID: ".$prod_id."; Lot: ".$row[csf('lot')]."; Product Name:".$row[csf('product_name_details')]."; Crrent stock: ".number_format($row[csf('current_stock')],2,".","")."; Allocated: ".number_format($row[csf('allocated_qnty')],2,".","")."; Available: ".number_format($row[csf('available_qnty')],2,".","")."</td></tr>";
				}else{
					$html_hr = "";
				}
			}

			$issue_basis = $issue_arr[$row[csf("po_break_down_id")]][$prod_id];
			if($row[csf("is_dyied_yarn")] == 1){
				$issue_qty = $issue_array[$row[csf("job_no")]][$prod_id]["issue_qty"];
			}else{
				$issue_qty=$issue_qty_wo=0;
				foreach ($issue_basis as $basis) {
					if($basis==3 || $basis==1 || $basis==8){
						if($basis==1){
							$booking_row = 0;
							$issue_qty_wo = $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_qty"];
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
						//$issue_qty += $issue_array[$row[csf('job_no')]][$prod_id]["issue_qty"];
						//$issue_qty = $issue_array[$row[csf("po_break_down_id")]][$prod_id]["issue_qty"];
						$issue_qty += $issue_array_req[$row[csf("po_break_down_id")]][$prod_id]["issue_qty"];
					}
				}

				/*if($issue_basis==3 || $issue_basis==8){
					if($row[csf("booking_no")] != ""){
						$issue_qty = $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$row[csf("booking_no")]]["issue_qty"];
					}
				}else{
					$issue_qty = $issue_array[$row[csf("po_break_down_id")]][$prod_id]["issue_qty"];
				}*/
			}

			$po_number=$po_number_arr[$row[csf("po_break_down_id")]]['po'];
			$buyername=$buy_name_arr[$po_number_arr[$row[csf("job_no")]]['buyer_name']];
			if($row[csf("is_dyied_yarn")] == 1){
				$return_qty_wo = $job_wose_issue_return_array[$row[csf("job_no")]][$prod_id];
			}else{
				$return_qty_wo=0;
				foreach ($issue_basis as $basis) {
					if($basis==3 || $basis==8){
						if($row[csf("booking_no")] != ""){
							$issue_ids = $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$row[csf("booking_no")]]["issue_id"];
							$return_qty_req=0;
							$iss_arr=array();
							foreach ($issue_ids as $issue_id) {
								if(!in_array($issue_id, $iss_arr)){
									$return_qty_req += $issue_return_req_array[$row[csf('po_break_down_id')]][$prod_id][$issue_id];
								}
								array_push($iss_arr, $issue_id);
							}
						}
					}else{
						$return_qty_wo = $issue_return_array[$row[csf("po_break_down_id")]][$prod_id];

					}
				}

			}

			$return_qty = $return_qty_wo + $return_qty_req;
			//echo $row[csf("po_break_down_id")]."=". $return_qty."<br />";
			$booking_no = implode(",",array_unique(explode(",", $row[csf("booking_no")])));
			echo $html_hr;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td width="25" align="center"><? echo $i; ?></td>
				<td width="25" align="center"><? echo $row[csf("dtls_id")]; ?></td>
				<td width="60" align="center"><? echo $prod_id; ?></td>
				<td width="110" align="center"><? echo $row[csf("job_no")]; ?></td>
				<td width="105" align="center"><? echo $buyername; ?></td>
				<td width="150" align="center"><? echo $row[csf("po_break_down_id")]."=".$po_number; ?></td>
				<td width="120" align="center"><? echo $booking_no; ?></td>
				<td width="75" align="right"><? echo number_format($row[csf("allocate_qty")], 2); ?></td>
				<td width="70" align="right" title="<? echo 'Req. Issue:'.$issue_qty.', WO Issue:'.$issue_qty_wo?>"><? echo number_format($issue_qty+$issue_qty_wo, 2); ?></td>
				<td width="60" align="right"><? echo number_format($return_qty, 2); ?></td>
				<td align="right">
					<?
					$balance = $balance + $row[csf("allocate_qty")] - ($issue_qty+$issue_qty_wo) + $return_qty;
					echo number_format($balance, 2);
					?>
				</td>
				<?
				$issue_qty_new = $issue_qty+$issue_qty_wo;
				$return_qty_new = $return_qty;
				$dtls_id = $row[csf("dtls_id")];

				//$new_allocation = $dtls_data_arr[$row[csf("id")]]["allocation"];
				$new_allocation = $row[csf("allocate_qty")] - ($row[csf("allocate_qty")] - $issue_qty_new) - $return_qty_new;

				if($row[csf("is_dyied_yarn")] == 1){
					$total_req_qnty = $job_req_info[$row[csf("job_no")]][$prod_id]["req_qnty"];
					$requisition_nos = rtrim($job_req_info[$row[csf("job_no")]][$prod_id]['req_no'],", ");

					$requisition_no_title = "";
					foreach (array_unique(explode(",",$requisition_nos)) as $req_no) {
						$requisition_issue_qnty = $requisition_wise_issue_array[$row[csf("job_no")]][$req_no][$prod_id];
						$req_qnty = $requisition_qnty_array[$row[csf("job_no")]][$req_no][$prod_id];
						$requisition_no_title .= "Req. no=".$req_no.", Req. Qnty=".number_format($req_qnty,2).",Issue=".number_format($requisition_issue_qnty,2).",\n";
						$requisiton_arr[$dtls_id][$booking_no][$row[csf("po_break_down_id")]][$req_no] += $req_qnty;
						$dtls_data_update_arr[$dtls_id]["po_requisition_qty"] = $requisiton_arr;
						$inc_requisition_qnty = number_format($requisition_issue_qnty-$req_qnty,2,".","");
						//echo "10**UPDATE PPL_YARN_REQUISITION_ENTRY set yarn_qnty=".number_format($req_qnty+$inc_requisition_qnty,2,".","")." where requisition_no=$req_no and prod_id=$prod_id -- $req_qnty;<br />";
						execute_query("UPDATE PPL_YARN_REQUISITION_ENTRY set yarn_qnty=".number_format($req_qnty+$inc_requisition_qnty,2,".","")." where requisition_no=$req_no and prod_id=$prod_id");
					}
				}
				else
				{
					//$requisition_nos = $requisition_array[$row[csf("po_break_down_id")]][$prod_id][$booking_no]["requisition_no"];
					$requisition_nos = $planning_array[$row[csf('po_break_down_id')]][$row[csf("booking_no")]][$prod_id]['requisition_no'];
					$total_req_qnty=0;
					$requisition_no_title="";
					foreach (array_unique(explode(",",$requisition_nos)) as $req_no) {

						$requisition_issue_qnty = $requisition_wise_issue_array[$row[csf("po_break_down_id")]][$req_no][$prod_id];
						$req_qnty = $planning_array[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$req_no][$prod_id]['req_qnty'];
						$requisition_no_title .= "Req. no=".$req_no.", Req. Qnty=".number_format($req_qnty,2).",Issue=".number_format($requisition_issue_qnty,2).",\n";
						$total_req_qnty += $req_qnty;
						$inc_requisition_qnty = number_format($requisition_issue_qnty-$req_qnty,2,".","");
						if(number_format($req_qnty+$inc_requisition_qnty,2,".","") > 0){
							//echo "10**UPDATE PPL_YARN_REQUISITION_ENTRY set yarn_qnty=".number_format($req_qnty+$inc_requisition_qnty,2,".","")." where requisition_no=$req_no and prod_id=$prod_id -- $req_qnty;<br />";
							execute_query("UPDATE PPL_YARN_REQUISITION_ENTRY set yarn_qnty=".number_format($req_qnty+$inc_requisition_qnty,2,".","")." where requisition_no=$req_no and prod_id=$prod_id");
						}
					}
				}
				?>
				<td class="new" width="75" align="right"><? echo number_format($new_allocation, 2); ?></td>
				<td class="new" width="75" align="right" title="<? echo $requisition_no_title;?>"><? echo number_format($total_req_qnty+$inc_requisition_qnty, 2); ?></td>
				<td class="new" width="70" align="right"><? echo number_format($issue_qty_new, 2); ?></td>
				<td class="new" width="60" align="right"><? echo number_format($return_qty_new, 2); ?></td>
				<td class="new" align="right">
					<?
					$balance_new = $balance_new + $new_allocation - $issue_qty_new + $return_qty_new;
					echo number_format($balance_new, 2);
					?>
				</td>
			</tr>
			<?
			$dtls_data_update_arr[$dtls_id]["qnty"] += $new_allocation;
			$dtls_data_update_arr[$dtls_id]["po_break_down_id"] = $row[csf("po_break_down_id")];
			$dtls_data_update_arr[$dtls_id]["job_no"] = $row[csf("job_no")];
			$dtls_data_update_arr[$dtls_id]["prod_id"] = $prod_id;
			$dtls_data_update_arr[$dtls_id]["mst_id"] = $row[csf("mst_id")];
			$dtls_data_update_arr[$dtls_id]["issue_qty_new"] = $issue_qty_new;
			$dtls_data_update_arr[$dtls_id]["return_qty_new"] = $return_qty_new;
			$dtls_balance_arr[$prod_id]["balance"] = $balance_new;
			$issue_qty=$return_qty=$issue_qty_wo=$return_qty_wo=$return_qty_req=0;
			$i++;
		}

		/*echo "<pre>";
		print_r($dtls_data_update_arr);
		echo "</pre>";*/

		$balance1=0;
		echo "<div class='fonts'>";
		foreach ($dtls_data_update_arr as $dtl_id => $dtl_row) {
			$current_stock = number_format($product_array[$dtl_row["prod_id"]]['current_stock'],2,".","");
			$mst_id = $dtl_row["mst_id"];
			$balance = number_format($dtls_balance_arr[$dtl_row["prod_id"]]["balance"],2,".","");
			$allocation = number_format($dtl_row["qnty"],2,".","");

			//echo "UPDATE INV_MATERIAL_ALLOCATION_DTLS set qnty=$allocation where id=$dtl_id; <br />";
			$update_allocation_dtls=execute_query("UPDATE INV_MATERIAL_ALLOCATION_DTLS set qnty=$allocation where id=$dtl_id");

			$mst_data_update_arr[$dtl_row["mst_id"]]["qnty"] += $allocation;
			$mst_data_update_arr[$dtl_row["mst_id"]]["prod_id"] = $dtl_row["prod_id"];
			$mst_data_update_arr[$dtl_row["mst_id"]]["balance"] = $balance;
			$mst_data_update_arr[$dtl_row["mst_id"]]["current_stock"] = $product_array[$dtl_row["prod_id"]]['current_stock'];
			$mst_data_update_arr[$dtl_row["mst_id"]]["qnty_break_down"] .= $allocation . "_" . $dtl_row["po_break_down_id"] . "_" . $dtl_row["job_no"] . ",";
		}
		echo "</div><div class='fonts'>";
		foreach ($mst_data_update_arr as $mst_id => $mst_row) {
			$prod_id = $mst_row["prod_id"];
			$current_stock = number_format($mst_row["current_stock"],2,".","");
			$qnty = number_format($mst_row["qnty"],2,".","");
			$balance = number_format($mst_row["balance"],2,".","");
			$qnty_break_down = trim($mst_row["qnty_break_down"],", ");
			//echo "UPDATE INV_MATERIAL_ALLOCATION_MST set qnty='$qnty',qnty_break_down='$qnty_break_down' where id=$mst_id;<br />";
			$update_allocation_mst=execute_query("UPDATE INV_MATERIAL_ALLOCATION_MST set qnty='$qnty',qnty_break_down='$qnty_break_down' where id=$mst_id");

			$prod_arr[$prod_id]["current_stock"] = $current_stock;
			$prod_arr[$prod_id]["allocated"] = $balance;
			$prod_arr[$prod_id]["available"] = number_format($current_stock-$balance,2,'.','');
		}
		echo "</div><div class='fonts'>";
		foreach ($prod_arr as $pr_id => $prod_row) {
			//echo "UPDATE product_details_master set allocated_qnty='".$prod_row['allocated']."',available_qnty='".$prod_row['available']."' where id=$pr_id;<br />";
			$product_details_mst=execute_query("UPDATE product_details_master set allocated_qnty='".$prod_row['allocated']."',available_qnty='".$prod_row['available']."' where id=$pr_id");
		}
		echo "</div>";
		//oci_commit($con);
	}else{
		echo "<tr><th style='text-align:center;' colspan='16'>No Data Found</th></tr>";
	}
	?>
</table>
<?
/*echo "<pre>";
print_r($dtls_data_arr);*/

oci_commit($con);
echo "Success";
die;
//echo $missed_id;
//echo implode(",",array_unique($cur_stock_less_than_allocation_arr));
// echo "<pre>";
// print_r($prod_arr);die;
//die;
//oci_commit($con);

die;
?>