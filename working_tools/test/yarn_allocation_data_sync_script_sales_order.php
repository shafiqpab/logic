<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
if ($db_type == 0) {
	mysql_query("BEGIN");
}
$prod_id = explode(",",$_GET["prod_id"]);
//$prod_cond = $prod_id != ""?" and item_id=$prod_id":"";
//$prod_cond2 = $prod_id != ""?" and c.prod_id=$prod_id":"";

$prod_cond = $_GET["prod_id"] != ""?" and item_id in (".implode(",",$prod_id).")":"";
$prod_cond2 = $_GET["prod_id"] != ""?" and c.prod_id in (".implode(",",$prod_id).")":"";

$buy_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
//$p_ids = "1800,325,337,413,475,766,887,923,954,959,976,1144,1286,1457,1462,1484,1552,1561,1785,1790,1799,1812,1815,1831,1840,1883,1893,1904,1905,1906,1907,1953,1968,1974,2005,2021,2036,2046,2050,2243,2378,2443,2507,2546,2650,2651,2696,2716,2813,2833,2917,2935,2968,2973,2997,3009,3040,3042,3120,3161,3178,3179,3286,3324,3388,3390,3404,3411,3427,3447,3452,3498,3512,3541,3549,3564,3579,3582,3589,3736,3743,3757,3799,3825,3866,3868,3907,3909,3934,3936,3943,3975,3977,4073,4074,4158,4216,4241,4257,4258,4270,4355,4356,4458,4558,4686,4811,4820,5009,5014,5047,5048,5154,5272,5349,5411,5412,5490,5503,5677,5678,5715,5851,5972,6263";
if ($db_type == 0) {
	$sql_allocation = "select id,mst_id,item_id,job_no, po_break_down_id,group_concat(booking_no) booking_no, sum(qnty) as allocate_qty,is_dyied_yarn,allocation_date from inv_material_allocation_dtls where status_active=1 and is_deleted=0 $prod_cond group by id,mst_id,item_id,job_no,po_break_down_id,is_dyied_yarn,allocation_date,id,mst_id";
}else{
	$sql_allocation = "select id,mst_id,item_id,job_no, po_break_down_id,listagg(cast(booking_no as varchar2(4000)), ',') within group (order by booking_no) as booking_no, sum(qnty) as allocate_qty,is_dyied_yarn,allocation_date from inv_material_allocation_dtls where status_active=1 and is_deleted=0 $prod_cond group by item_id,allocation_date,job_no,po_break_down_id,is_dyied_yarn,id,mst_id"; // and item_id in($p_ids)
}
//echo $sql_allocation;die;
$result_allocation = sql_select($sql_allocation);

$po_break_down_arr = $job_arr =  array();
foreach ($result_allocation as $row) {
	$po_break_down_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
	$item_arr[] = $row[csf("item_id")];
}

$all_item_ids=array_unique($item_arr);
if($db_type==2 && count($all_item_ids)>999)
{
	$item_cond=" and (";
	$item_cond2=" and (";
	$itemIdsArr=array_chunk($all_item_ids,999);
	foreach($itemIdsArr as $ids)
	{
		$ids=rtrim(implode(",",$ids),", ");
		$item_cond.=" id in($ids) or";
		$item_cond2.=" b.prod_id in($ids) or";
	}
	$item_cond=chop($item_cond,'or ');
	$item_cond2=chop($item_cond2,'or ');
	$item_cond.=")";
	$item_cond2.=")";
}
else
{
	$poIds=rtrim(implode(",",$all_item_ids),", ");
	$item_cond=" and id in($poIds)";
	$item_cond2=" and b.prod_id in($poIds)";
}

$sql_products = "select a.id, a.company_id, a.lot,a.product_name_details,a.current_stock, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $item_cond";

$product_array=array();
$productData = sql_select($sql_products);
foreach( $productData as $prod_val){
	$product_array[$prod_val[csf('id')]]['lot']						= $prod_val[csf('lot')];
	$product_array[$prod_val[csf('id')]]['product_name_details']	= $prod_val[csf('product_name_details')];
	$product_array[$prod_val[csf('id')]]['current_stock']			= $prod_val[csf('current_stock')];
	$product_array[$prod_val[csf('id')]]['allocated_qnty']			= $prod_val[csf('allocated_qnty')];
	$product_array[$prod_val[csf('id')]]['available_qnty']			= $prod_val[csf('available_qnty')];
}

/*$sql_cond = !empty($job_arr)?" and a.job_no_mst in(".implode(",",$job_arr).")":" and a.id in(".implode(",",$po_break_down_arr).")";
$po_number_arr = array();
$po_sql = sql_select("select a.id,a.job_no_mst,b.buyer_name, a.file_no,a.grouping,a.po_number from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0");
foreach ($po_sql as $row) {
	$po_number_arr[$row[csf("id")]]['po'] = $row[csf("po_number")];
	$po_number_arr[$row[csf("id")]]['file'] = $row[csf("file_no")];
	$po_number_arr[$row[csf("id")]]['ref'] = $row[csf("grouping")];
	$po_number_arr[$row[csf("id")]]['job_no'] = $row[csf("job_no_mst")];
	$po_number_arr[$row[csf("job_no_mst")]]['buyer_name'] = $row[csf("buyer_name")];
}*/

$all_sales_ids=array_unique($po_break_down_arr);
if($db_type==2 && count($all_sales_ids)>999)
{
	$sales_cond=" and (";
	$salesIdsArr=array_chunk($all_sales_ids,999);
	foreach($salesIdsArr as $ids)
	{
		$ids=rtrim(implode(",",$ids),", ");
		$sales_cond.=" a.id in($ids) or";
	}
	$sales_cond=chop($sales_cond,'or ');
	$sales_cond.=")";
}
else
{
	$poIds=rtrim(implode(",",$all_sales_ids),", ");
	$sales_cond=" and a.id in($poIds)";
}

$job_no_array = array();
$jobsql = "select a.id,a.job_no,a.buyer_id,a.sales_booking_no,a.style_ref_no,a.within_group from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $sales_cond";
$jobData = sql_select($jobsql);
foreach ($jobData as $row) {
	$sales_order_arr[$row[csf('job_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
	$sales_order_arr[$row[csf('job_no')]]['buyer_id']=$row[csf('buyer_id')];
	$sales_order_arr[$row[csf('job_no')]]['within_group']=$row[csf('within_group')];
}

$planning_array = array();
$plan_sql="select a.po_id,a.booking_no,a.dtls_id,b.requisition_no,b.prod_id,sum(b.yarn_qnty) req_qnty from ppl_planning_entry_plan_dtls a,ppl_yarn_requisition_entry b where a.dtls_id=b.knit_id and a.status_active=1 and b.status_active=1 $item_cond2 group by a.po_id,a.booking_no,a.dtls_id,b.requisition_no,b.prod_id";
$planData = sql_select($plan_sql);
foreach ($planData as $row) {
	$planning_array[$row[csf('po_id')]][$row[csf('prod_id')]]['req_qnty']+=$row[csf('req_qnty')];
	$planning_array[$row[csf('po_id')]][$row[csf('prod_id')]]['requisition_no'].=$row[csf('requisition_no')].",";
}

$issue_array = $job_wose_issue_array = array();
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
<style type="text/css">
table { width: 100%; margin: auto; font-family: arial; font-size: 12px; }
table tr td{ background-color: #E9F3FF; }
.global { background-color: rgba(52,168,83,.5) }
.over_less_allocation { background-color: red;color:#fff; }
.inc_dec_allocation { background-color: yellow !important; }
.new { background-color: rgba(52,168,83,.2); }
.border{border-top: 1px solid; background-color: #fff !important; font-weight: bold; font-size: 11px; }
thead th{  background-color: #ccc !important; }
</style>
<table border="1" cellpadding="1" cellspacing="0" class="rpt_table" rules="all">
	<?
	$i = 1;
	$balance = '';
	$prod_arr = array();
	if(!empty($result_allocation)){
		$balance = $balance_new = $issue_qty = 0;
		$html_hr="";
		$item_arr = $mst_data_update_arr = array();
		foreach ($result_allocation as $row) {
			$prod_id = $row[csf("item_id")];
			if(empty($item_arr)){
				$item_arr[] = $prod_id;
				$html_hr = "<tr><td colspan='16' class='border'>Product ID: ".$prod_id."; Lot: ".$product_array[$prod_id]['lot']."; Product Name:".$product_array[$prod_id]['product_name_details']."; Crrent stock: ".number_format($product_array[$prod_id]['current_stock'],2)."; Allocated: ".number_format($product_array[$prod_id]['allocated_qnty'],2)."; Available: ".number_format($product_array[$prod_id]['available_qnty'],2)."</td></tr>";
				$html_hr .= '<thead>
				<th width="25">SL</th>
				<th width="60">Product ID</th>
				<th width="150">FSO NO.</th>
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
					<th width="150">FSO NO.</th>
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

					$html_hr .= "<tr><td colspan='16' class='border'>Product ID: ".$prod_id."; Lot: ".$product_array[$prod_id]['lot']."; Product Name: ".$product_array[$prod_id]['product_name_details']."; Crrent stock: ".number_format($product_array[$prod_id]['current_stock'],2)."; Allocated: ".number_format($product_array[$prod_id]['allocated_qnty'],2)."; Available: ".number_format($product_array[$prod_id]['available_qnty'],2)."</td></tr>";
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
			$less_allocation = ($row[csf("allocate_qty")]<($issue_qty - $return_qty))?"over_less_allocation":"";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td width="25" align="center"><? echo $i; ?></td>
				<td width="60" align="center"><? echo $prod_id; ?></td>
				<td width="150" align="center" title="<? echo $row[csf('po_break_down_id')]; ?>" ><? echo $row[csf("job_no")]; ?></td>
				<td width="100" align="center"><? echo $buyername; ?></td>
				<td width="100" align="center"><? echo $po_number; ?></td>
				<td width="100" align="center"><? echo $booking_no; ?></td>
				<td width="75" align="right" class="<? echo $less_allocation;?>"><? echo number_format($row[csf("allocate_qty")], 2); ?></td>
				<td width="70" align="right"><? echo number_format($issue_qty, 2); ?></td>
				<td width="60" align="right"><? echo number_format($return_qty, 2); ?></td>
				<td align="right">
					<?
					$balance = $balance + $row[csf("allocate_qty")] - $issue_qty + $return_qty;
					echo number_format($balance, 2);
					?>
				</td>
				<?
				$requisition_qnty = number_format($planning_array[$row[csf("po_break_down_id")]][$prod_id]['req_qnty'], 2);
				$requisition_nos = rtrim($planning_array[$row[csf("po_break_down_id")]][$prod_id]['requisition_no'],", ");
				$issue_qty_new = $issue_qty;
				$return_qty_new = $return_qty;
				if($issue_qty_new > 0){
					if($row[csf("allocate_qty")] > ($issue_qty_new - $return_qty_new)){
						$new_allocation = ($row[csf("allocate_qty")]<$requisition_qnty)?$requisition_qnty:$row[csf("allocate_qty")];
					}else{
						$new_allocation = $row[csf("allocate_qty")] + ($issue_qty_new - ($row[csf("allocate_qty")]+$return_qty_new));
						$new_allocation = ($new_allocation<$requisition_qnty)?$requisition_qnty:$new_allocation;
					}
				}else{
					$new_allocation = $row[csf("allocate_qty")];
				}

				$over_allocation = (number_format($row[csf("allocate_qty")],2)!=$requisition_qnty)?"over_less_allocation":"global";
				$inc_dec_allocation = ($new_allocation!=$row[csf("allocate_qty")])?"inc_dec_allocation":"";
				?>
				<td class="<? echo $over_allocation;?>" width="75" align="right" title="Requisition no = <? echo $requisition_nos;?>"><? echo $requisition_qnty; ?></td>
				<td class="new <? echo $inc_dec_allocation;?>" width="75" align="right"><? echo number_format($new_allocation, 2); ?></td>
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

		//$prod_arr = $mst_data_update_arr = $dtls_data_update_arr = array();
		$balance1=0;
		foreach ($dtls_data_update_arr as $dtl_id => $dtl_row) {
			$current_stock = number_format($product_array[$dtl_row["prod_id"]]['current_stock'],2,".","");
			$mst_id = $dtl_row["mst_id"];
			$balance = number_format($dtls_balance_arr[$dtl_row["prod_id"]]["balance"],2,".","");
			$dtl_qnty = number_format($dtl_row["qnty"],2,".","");
			if($current_stock >= $balance){
				$balance1 = $balance;
				$allocation = $dtl_qnty;
				//echo "UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id g<br />";
				$update_allocation_dtls=execute_query("UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id");
			}else{
				if($dtl_row["issue_qty_new"] > 0){
					//$allocation = $dtl_qnty + ($dtl_row["issue_qty_new"] - ($dtl_qnty + $dtl_row["return_qty_new"]));
					$allocation = $dtl_row["issue_qty_new"] - $dtl_row["return_qty_new"];
				}else{
					$allocation = 0;
				}
				//$balance1 ."+". $allocation ."-(". $dtl_row["issue_qty_new"] ."-". $dtl_row["return_qty_new"].")";
				$balance1 += $allocation - ($dtl_row["issue_qty_new"]-$dtl_row["return_qty_new"]);
				//echo $allocation ."-". $dtl_row["issue_qty_new"] ."+". $dtl_row["return_qty_new"];
				//echo "UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id and mst_id=$mst_id <br />";
				$update_allocation_dtls=execute_query("UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id");
			}

			$mst_data_update_arr[$dtl_row["mst_id"]]["qnty"] += $allocation;
			$mst_data_update_arr[$dtl_row["mst_id"]]["prod_id"] = $dtl_row["prod_id"];
			$mst_data_update_arr[$dtl_row["mst_id"]]["balance"] = $balance1;
			$mst_data_update_arr[$dtl_row["mst_id"]]["current_stock"] = $product_array[$dtl_row["prod_id"]]['current_stock'];
			$mst_data_update_arr[$dtl_row["mst_id"]]["qnty_break_down"] .= $allocation . "_" . $dtl_row["po_break_down_id"] . "_" . $dtl_row["job_no"] . ",";
		}
		//echo "<pre>";
		//print_r($mst_data_update_arr);die;

		foreach ($mst_data_update_arr as $mst_id => $mst_row) {
			$prod_id = $mst_row["prod_id"];
			$current_stock = number_format($mst_row["current_stock"],2,".","");
			$qnty = number_format($mst_row["qnty"],2,".","");
			$balance = number_format($mst_row["balance"],2,".","");
			$qnty_break_down = trim($mst_row["qnty_break_down"],", ");

			if($current_stock >= $balance){
				$allocation = $qnty;
				//echo "UPDATE inv_material_allocation_mst set qnty='$allocation',qnty_break_down='$qnty_break_down' where id=$mst_id g <br />";
				$update_allocation_mst=execute_query("UPDATE inv_material_allocation_mst set qnty='$allocation',qnty_break_down='$qnty_break_down' where id=$mst_id");
				$prod_arr[$prod_id]["current_stock"] = $current_stock;
				$prod_arr[$prod_id]["allocated"] = $balance;
				$prod_arr[$prod_id]["available"] = $current_stock-$balance;
			}else{
				if($current_stock < $balance){
					//$cur_stock_less_than_allocation_arr[$prod_id] = $current_stock . " = " . $balance;
					//echo "UPDATE inv_material_allocation_mst set qnty='$qnty',qnty_break_down='$qnty_break_down' where id=$mst_id <br />";
					$update_allocation_dtls=execute_query("UPDATE inv_material_allocation_mst set qnty='$qnty',qnty_break_down='$qnty_break_down' where id=$mst_id");

					$prod_arr[$prod_id]["current_stock"] = $current_stock;
					$prod_arr[$prod_id]["allocated"] = 0;
					$prod_arr[$prod_id]["available"] = $current_stock;
				}
			}
		}

		foreach ($prod_arr as $key => $prod_row) {
			//$prod_info = explode("**",$prod_row);
			//echo "UPDATE product_details_master set allocated_qnty='".$prod_row['allocated']."',available_qnty='".$prod_row['available']."' where id=$key ;<br />";
			$update_allocation_mst=execute_query("UPDATE product_details_master set allocated_qnty='".$prod_row['allocated']."',available_qnty='".$prod_row['available']."' where id=$key");
		}
	}else{
		echo "<tr><th style='text-align:center;' colspan='16'>No Data Found</th></tr>";
	}
	?>
</table>
<?
//echo implode(",",array_unique($cur_stock_less_than_allocation_arr));
//echo "<pre>";
//print_r($prod_arr);
//die;
//oci_commit($con);
echo "Success";
die;
?>