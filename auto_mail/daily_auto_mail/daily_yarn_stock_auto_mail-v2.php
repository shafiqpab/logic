<?
date_default_timezone_set("Asia/Dhaka");

header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
require('../../ext_resource/mpdf60/mpdf.php');
$user_id = $_SESSION['logic_erp']["user_id"];

$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

	
	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
    $previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
	
 

	$companyArr = return_library_array("select id,company_name from lib_company where 1=1", "id", "company_name");
	$supplierArr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_dtls = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$yarnTestQalityCommentsArr = return_library_array("select prod_id, yarn_quality_coments from inv_yarn_test_mst where  status_active=1 and is_deleted=0", 'prod_id', 'yarn_quality_coments');
	$yarnTestArr = return_library_array("select prod_id, lot_number from inv_yarn_test_mst where status_active=1 and is_deleted=0", 'prod_id', 'lot_number');


foreach($companyArr as $company_id=>$company_name){
$action="generate_report";
//$company_id="3";
$cbo_dyed_type=2;
$cbo_yarn_type="";
$txt_count="";
$txt_lot_no="";
$from_date=$previous_date;
$to_date=$previous_date;
$store_wise="2";
$store_name="0";
$value_with="1";
$cbo_supplier="";
$show_val_column="";
$get_upto="0";
$txt_days="";
$get_upto_qnty="1";
$txt_qnty=5;
$type="10";
$txt_composition="";
$txt_composition_id="";
$lot_search_type="0";





//--------------------------------------------------------------------------------------------


if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

 
	$companyArr[0] = "All Company";
	
	$exchange_rate = return_field_value("conversion_rate", "(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)", "ROWNUM = 1");
	$from_date = change_date_format($from_date, '', '', 1);
	$to_date = change_date_format($to_date, '', '', 1);
	
 	
	
	$txt_lot_no = trim($txt_lot_no);
	$txt_composition = trim($txt_composition);
	ob_start();
	?>
	<style>
		a 
		{
			color: #0254EB
		}
		a:visited {
			color: #0254EB
		}
		a.morelink 
		{
			text-decoration:none;
			outline: none;
		}
		.morecontent span 
		{
			display: none;
		}
		.comment 
		{
			width: 400px;
			background-color: #f0f0f0;
			margin: 10px;
		}
		
			 
		table tr th, table tr td{word-wrap: break-word;word-break: break-all;}
		 	
		
	</style>
	<?
ob_start();
	if ($type == 6)
	{
		$search_cond = "";
		if ($cbo_yarn_type == 0)
			$search_cond .= "";
		else
			$search_cond .= " and a.yarn_type in ($cbo_yarn_type)";
		if ($txt_count == "")
			$search_cond .= "";
		else
			$search_cond .= " and a.yarn_count_id in($txt_count)";
		if ($txt_lot_no == "")
			$search_cond .= "";
		else
			$search_cond .= " and trim(a.lot)='" . trim($txt_lot_no) . "'";

		if ($cbo_supplier == 0)
			$search_cond .= "";
		else
			$search_cond .= "  and a.supplier_id in($cbo_supplier)";
		if ($txt_composition == "")
			$search_cond .= "";
		else
			$search_cond .= " and a.yarn_comp_type1st in (" .$txt_composition_id .")";

		if ($company_id == 0) 
		{
			$company_cond = "";
			$company_cond_mrr = "";
		} 
		else 
		{
			$company_cond = " and b.company_id=$company_id";
			$company_cond_mrr = " and a.company_id=$company_id";
		}

		if ($to_date != "")
			$mrr_date_cond = " and a.transaction_date<='$to_date'";
		if ($to_date != "")
			$rcv_date_cond = " and b.transaction_date<='$to_date'";

		$issue_qnty_arr = sql_select("select a.prod_id, b.recv_trans_id, b.issue_qnty 
		from  inv_transaction a,  inv_mrr_wise_issue_details b 
		where a.id=b.issue_trans_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type in(2,3,6) and a.item_category=1 $company_cond_mrr $mrr_date_cond");
		$mrr_issue_qnty_arr = array();
		foreach ($issue_qnty_arr as $row) 
		{
			$mrr_issue_qnty_arr[$row[csf("recv_trans_id")]][$row[csf("prod_id")]] += $row[csf("issue_qnty")];
		}
		unset($issue_qnty_arr);
		//echo "<pre>";
		//print_r($mrr_issue_qnty_arr[8706390]);
		//die;

        //for issue information
		$sql_issue_rslt = sql_select("SELECT b.ID, b.BUYER_ID FROM INV_ISSUE_MASTER b WHERE b.ITEM_CATEGORY=1".$company_cond);
        $issue_data_arr = array();
        foreach ($sql_issue_rslt as $row)
        {
      	    $issue_data_arr[$row['ID']]['buyer_id'] = $row['BUYER_ID'];
        }
        unset($sql_issue_rslt);

        //for receive information
		$receive_sql = sql_select("select b.ID, b.RECV_NUMBER, b.RECEIVE_DATE, b.ISSUE_ID FROM INV_RECEIVE_MASTER b WHERE b.ITEM_CATEGORY=1".$company_cond);
        $receive_data = array();
        foreach ($receive_sql as $row)
        {
      	    $receive_data[$row['ID']]['recv_number'] = $row['RECV_NUMBER'];
      	    $receive_data[$row['ID']]['receive_date'] = $row['RECEIVE_DATE'];
      	    $receive_data[$row['ID']]['issue_id'] = $row['ISSUE_ID'];
        }
        unset($receive_sql);

        //for transfer information
		$transfer_sql = sql_select("select b.id, b.transfer_system_id, b.transfer_date from inv_item_transfer_mst b where item_category=1".$company_cond);
        $transfer_data = array();
        foreach ($transfer_sql as $row)
        {
      		$transfer_data[$row[csf("id")]]["transfer_system_id"] = $row[csf("transfer_system_id")];
      		$transfer_data[$row[csf("id")]]["transfer_date"] = $row[csf("transfer_date")];
        }
        unset($transfer_sql);

        //for transaction information
		$mrr_rate_sql = sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction
      	where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) and cons_quantity>0 group by prod_id");
        $mrr_rate_arr = array();
        foreach ($mrr_rate_sql as $row) 
        {
      	    $mrr_rate_arr[$row[csf("prod_id")]] = $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
        }
        unset($mrr_rate_sql);

        if ($db_type == 0)
        {
	      	$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type, group_concat(b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount
	      	from product_details_master a, inv_transaction b
	      	where a.id=b.prod_id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(1,4) $company_cond $search_cond  $rcv_date_cond
	      	group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type
	      	union all
	      	select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type, group_concat(b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount
	      	from product_details_master a, inv_transaction b, inv_item_transfer_mst c
	      	where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(5) $company_cond $search_cond  $rcv_date_cond
	      	group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type";
        }
        else
        {
	      	$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type, b.buyer_id, d.pay_mode, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount
	      	from product_details_master a, inv_transaction b left join wo_yarn_dyeing_mst d on b.pi_wo_batch_no=d.id
	      	where a.id=b.prod_id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(1,4) $company_cond $search_cond  $rcv_date_cond
	      	group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type, b.buyer_id, d.pay_mode
	      	union all
	      	select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type, b.buyer_id, d.pay_mode,listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount
	      	from product_details_master a, inv_transaction b left join wo_yarn_dyeing_mst d on b.pi_wo_batch_no=d.id, inv_item_transfer_mst c
	      	where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(5) $company_cond $search_cond  $rcv_date_cond
	      	group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group, b.mst_id, b.transaction_type, b.buyer_id, d.pay_mode
			order by  yarn_count_id,  yarn_comp_type1st, yarn_type,id";
        }
         
        $result = sql_select($sql);
        $i = 1;
      
        ?>
      
      		<table width="1780" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px"  rules="all" id="table_header_1" >
	      		<thead>
	      			<tr class="form_caption" style="border:none;">
	      				<td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Yarn Stock </td>
	      			</tr>
	      			<tr style="border:none;">
	      				<td colspan="17" align="center" style="border:none; font-size:14px;">
	      					Company Name : <? echo $companyArr[str_replace("'", "", $company_id)]; ?>
	      				</td>
	      			</tr>
	      			<tr style="border:none;">
	      				<td colspan="17" align="center" style="border:none;font-size:12px; font-weight:bold">
	      					<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
	      				</td>
	      			</tr>
	      			<tr>
	      				<th rowspan="2" width="40">SL</th>
	      				<th rowspan="2" width="120">Company Name</th>
	      				<th colspan="7">Description</th>
	      				<th rowspan="2" width="100">Stock In Hand</th>
	      				<th rowspan="2" width="90">Avg. Rate (USD)</th>
	      				<th rowspan="2" width="100">Stock Value (USD)</th>
	      				<th rowspan="2" width="120">MRR No.</th>
	      				<th rowspan="2" width="80">Receive Date</th>
	      				<th rowspan="2" width="100">Buyer</th>
	      				<th rowspan="2" width="50">Age (Days)</th>
	      				<th rowspan="2">Remarks</th>
	      			</tr>
	      			<tr>
	      				<th width="60">Prod.ID</th>
	      				<th width="60">Count</th>
	      				<th width="150">Composition</th>
	      				<th width="100">Yarn Type</th>
	      				<th width="80">Color</th>
	      				<th width="100">Lot</th>
	      				<th width="80">Supplier</th>
	      			</tr>
	      		</thead>
      				<?
      				$tot_stock_value = 0;
      				foreach ($result as $row)
      				{
      					$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
      					if ($row[csf("yarn_comp_type2nd")] != 0)
      					$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";

      					$totalRcv = $row[csf("cons_quantity")];
      					$totalIssue = 0;
      					$stockInHand = $avg_rate = 0;

      					$trans_id_arr = array_unique(explode(",", $row[csf("trans_id")]));
      					foreach ($trans_id_arr as $tr_id) {
      						$totalIssue += $mrr_issue_qnty_arr[$tr_id][$row[csf("id")]];
      					}

      					$stockInHand = $totalRcv - $totalIssue;

                        //subtotal and group-----------------------
      					$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];
                        //$receive_data[$row[csf("id")]]["recv_number"]
      					$mrr_number = $mrr_date = "";
      					if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4)
						{
      						$mrr_number = $receive_data[$row[csf("mst_id")]]["recv_number"];
      						$mrr_date = $receive_data[$row[csf("mst_id")]]["receive_date"];
      					}
						else
						{
      						$mrr_number = $transfer_data[$row[csf("mst_id")]]["transfer_system_id"];
      						$mrr_date = $transfer_data[$row[csf("mst_id")]]["transfer_date"];
      					}

      					$ageOfDays = datediff("d", $mrr_date, date("Y-m-d"));

      					if ($i % 2 == 0)
      						$bgcolor = "#E9F3FF";
      					else
      						$bgcolor = "#FFFFFF";

                        //$avg_rate=$row[csf("cons_amount")]/$row[csf("cons_quantity")];
      					$avg_rate_usd = 0;
      					$avg_rate = $mrr_rate_arr[$row[csf("id")]];
      					$stock_value = $stockInHand * $avg_rate;
      					$stock_value_usd = $stock_value / $exchange_rate;
      					$avg_rate_usd = $stock_value_usd / $stockInHand;
      					$avg_rate_usd = abs($avg_rate_usd);
						
						//for supplier
						if($row[csf('is_within_group')] == 1)
						{
							$supplier_name = $companyArr[$row[csf('supplier_id')]];
						}
						else
						{
							$supplier_name = $supplierArr[$row[csf('supplier_id')]];
						}
						//end for supplier
						
						//for buyer
						if($row[csf("transaction_type")] == 4)
						{
							$row[csf('buyer_id')] = $issue_data_arr[$receive_data[$row[csf('mst_id')]]['issue_id']]['buyer_id'];
						}
						//end for buyer

                        if ($value_with == 1)
                        {
                          	if (number_format($stockInHand, 2) > 0.00)
							{
                          		if (!in_array($check_string, $checkArr))
								{
                          			$checkArr[$i] = $check_string;
                          			if ($i > 1)
									{
                          				?>
                          				<tr bgcolor="#CCCCCC" style="font-weight:bold">
                          					<td colspan="9" align="right">Sub Total</td>
                          					<td width="100" align="right"><? echo number_format($sub_stock_in_hand, 2); ?></td>
                          					<td width="90" align="right">&nbsp;</td>
                          					<td width="100" align="right"><? echo number_format($sub_stock_value_usd, 2); ?></td>
                          					<td width="120" align="right">&nbsp;</td>
                          					<td width="80" align="right">&nbsp;</td>
                          					<td width="100" align="right">&nbsp;</td>
                          					<td width="50">&nbsp;</td>
                          					<td width="140">&nbsp;</td>
                          					<td>&nbsp;</td>
                          				</tr>
                          				<?
                          				$sub_stock_in_hand = 0;
                          				$sub_stock_value = 0;
                          				$sub_stock_value_usd = 0;
                          			}
                          		}

                                //$stock_value_usd=($stockInHand*$row[csf("avg_rate_per_unit")])/$exchange_rate;
                          		?>
                          		<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                          			<td width="40"><? echo $i; ?></td>
                          			<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $companyArr[$row[csf("company_id")]]; ?></td>
                          			<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $row[csf("id")]; ?></td>
                          			<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
                          			<td width="150" style="word-wrap:break-word; word-break: break-all;"><? echo $compositionDetails; ?></td>
                          			<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
                          			<td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
                          			<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@';"><p>
                          				<? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "")
										{
											?>
                          					<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"><? echo $row[csf("lot")]; ?></a>
                                            <?
										}
										else
										{
                          					echo $row[csf("lot")];
                          				}
                          				?></p>
                          			</td>
                          			<td width="80" style="word-wrap:break-word; word-break: break-all;" title="<? echo "transaction Id=" . $row[csf("trans_id")] . "Receive Qnty=" . $totalRcv . "Issue Qnty=" . $totalIssue; ?>"><? echo $supplier_name; ?></td>
                          			<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right"><a href='##' onclick="openmypage_stock('<? echo $row[csf('id')] . "," . $row[csf('mst_id')] . "," . $row[csf('transaction_type')]; ?>', 'stock_popup_mrr')"><? echo number_format($stockInHand, 2); ?></a></td>
                          			<td width="90"  style="word-wrap:break-word; word-break: break-all;" align="right" title="<? echo $row[csf("cons_amount")] . "/" . $row[csf("cons_quantity")] . "=" . $avg_rate . "=" . $exchange_rate; ?>"><? echo number_format($avg_rate_usd, 4); ?></td>
                          			<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stock_value_usd, 2); ?></td>
                          			<td width="120" align="center" title="<? echo "transaction type =" . $row[csf("transaction_type")]; ?>"><p><? echo $mrr_number; ?>&nbsp;</p></td>
                          			<td width="80" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo change_date_format($mrr_date); ?></td>
                          			<td width="100" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo $buyer_dtls[$row[csf('buyer_id')]]; ?></td>
                          			<td width="50" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo $ageOfDays; ?></td>
                          			<?
									if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "")
									{
									?>
									<td width="140" align="left"><span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
									<?
									}
									else
									{
										?>
										<td width="140" align="left"></td>
										<?
									}
									?>
                          			<td width="" align="right" style="word-wrap:break-word; word-break: break-all;">
									<?
                                    $buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
                                    $allocate_qnty_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['allocate_qnty']);
                                    $buyer_all = "";
                                    $m = 0;
                                    foreach ($buyer_id_arr as $buy_id)
									{
                                        if ($buyer_all != "")
                                            $buyer_all .= "<br>";
                                        $buyer_all .= $buy_short_name_arr[$buy_id];
                                        if ($buyer_all != "")
                                            $buyer_all .= "&nbsp;Qnty: " . $allocate_qnty_arr[$m];
                                        $m++;
                                    }
                                    echo $buyer_all;
                                    ?>
                          			</td>
                          		</tr>
                          		<?
                          		$i++;
                                  $sub_stock_in_hand += $stockInHand;
                                  $sub_stock_value += $stock_value;
                                  $sub_stock_value_usd += $stock_value_usd;

                                  $grand_total_stock_in_hand += $stockInHand;
                                  $grand_total_stock_value += $stock_value;
                                  $grand_total_stock_value_usd += $stock_value_usd;
                                }
                        }
                        else
                        {
                          	if ($stockInHand >= 0)
							{
                          		if (!in_array($check_string, $checkArr))
								{
                          			$checkArr[$i] = $check_string;
                          			if ($i > 1)
									{
                          				?>
                          				<tr bgcolor="#CCCCCC" style="font-weight:bold">
                          					<td colspan="9" align="right">Sub Total</td>
                          					<td width="100" align="right"><? echo number_format($sub_stock_in_hand, 2); ?></td>
                          					<td width="90" align="right">&nbsp;</td>
                          					<td width="100" align="right"><? echo number_format($sub_stock_value_usd, 2); ?></td>
                          					<td width="120" align="right">&nbsp;</td>
                          					<td width="80" align="right">&nbsp;</td>
                          					<td width="100" align="right">&nbsp;</td>
                          					<td width="50">&nbsp;</td>
                          					<td width="140">&nbsp;</td>
                          					<td>&nbsp;</td>
                          				</tr>
                          				<?
                          				$sub_stock_in_hand = 0;
                          				$sub_stock_value = 0;
                          				$sub_stock_value_usd = 0;
                          			}
                          		}
                          		?>
                          		<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                          			<td width="40"><? echo $i; ?></td>
                          			<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $companyArr[$row[csf("company_id")]]; ?></td>
                          			<td width="60"><? echo $row[csf("id")]; ?></td>
                          			<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
                          			<td width="150"  style="word-wrap:break-word; word-break: break-all;"><? echo $compositionDetails; ?></td>
                          			<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
                          			<td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
                          			<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@';"><p>
									<? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
                                    <a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
                                    <? }else{
                                    echo $row[csf("lot")];
                                    }
                                    ?>
                          			&nbsp;</p></td>
                          			<td width="80" style="word-wrap:break-word; word-break: break-all;" title="<? echo "transaction Id=" . $row[csf("trans_id")] . "Receive Qnty=" . $totalRcv . "Issue Qnty=" . $totalIssue; ?>"><? echo $supplier_name; ?></td>
                          			<td width="100" align="right"><a href='##' onclick="openmypage_stock('<? echo $row[csf('id')] . "," . $row[csf('mst_id')] . "," . $row[csf('transaction_type')]; ?>', 'stock_popup_mrr')"><? echo number_format($stockInHand, 2); ?></a></td>
                          			<td width="90" align="right" title="<? echo $row[csf("cons_amount")] . "/" . $row[csf("cons_quantity")] . "=" . $avg_rate . "=" . $exchange_rate; ?>"><p><? echo number_format($avg_rate_usd, 4); ?></p></td>
                          			<td width="100" align="right"><? echo number_format($stock_value_usd, 2); ?></td>
                          			<td width="120" align="center" title="<? echo "transaction type =" . $row[csf("transaction_type")]; ?>"><p><? echo $mrr_number; ?>&nbsp;</p></td>
                          			<td width="80" align="center"><p><? echo change_date_format($mrr_date); ?>&nbsp;</p></td>
                          			<td width="100"><? echo $buyer_dtls[$row[csf('buyer_id')]]; ?></td>
                          			<td width="50" align="center"><? echo $ageOfDays; ?></td>
									<?
                                    if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "")
                                    {
                                        ?>
                                        <td width="140" align="left"><span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
                                        <?
                                    }
                                    else
                                    {
                                        ?>
                                        <td width="140" align="left"></td>
                                        <?
                                    }
                                    ?>
                          			<td width="" align="right"><p>
										<?
                                        $buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
                                        $allocate_qnty_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['allocate_qnty']);
                                        $buyer_all = "";
                                        $m = 0;
                                        foreach ($buyer_id_arr as $buy_id) {
                                            if ($buyer_all != "")
                                                $buyer_all .= "<br>";
                                            $buyer_all .= $buy_short_name_arr[$buy_id];
                                            if ($buyer_all != "")
                                                $buyer_all .= "&nbsp;Qnty: " . $allocate_qnty_arr[$m];
                                            $m++;
                                        }
                                        echo $buyer_all;
                                        ?>&nbsp;
                          			</p></td>
                          		</tr>
                          		<?
                          		$i++;
                          		$sub_stock_in_hand += $stockInHand;
                          		$sub_stock_value += $stock_value;
                          		$sub_stock_value_usd += $stock_value_usd;

                          		$grand_total_stock_in_hand += $stockInHand;
                          		$grand_total_stock_value += $stock_value;
                          		$grand_total_stock_value_usd += $stock_value_usd;
                          	}
                        }
                    }
                    ?>
                    <tr bgcolor="#CCCCCC" style="font-weight:bold">
                        <td colspan="9" align="right">Sub Total</td>
                        <td width="100" align="right"><? echo number_format($sub_stock_in_hand, 2); ?></td>
                        <td width="90" align="right">&nbsp;</td>
                  		<td width="100" align="right"><? echo number_format($sub_stock_value_usd, 2); ?></td>
                  		<td width="120" align="right">&nbsp;</td>
                  		<td width="80" align="right">&nbsp;</td>
                  		<td width="100" align="right">&nbsp;</td>
                  		<td width="50">&nbsp;</td>
                  		<td width="140">&nbsp;</td>
                  		<td>&nbsp;</td>
                  	</tr>
                    
                    
                    
                    
                    
                    
                    
                <tr class="tbl_bottom">
                    <td width="40"></td>
                    <td width="120"></td>
                    <td width="60"></td>
                    <td width="60"></td>
                    <td width="150"></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="100"></td>
                    <td width="80" align="right">Grand Total</td>
                    <td width="100" align="right" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 2); ?></td>
                    <td width="90" align="right">&nbsp;</td>
                    <td width="100" align="right"><? echo number_format($grand_total_stock_value_usd, 2); ?></td>
                    <td width="120" align="right">&nbsp;</td>
                    <td width="80" align="right">&nbsp;</td>
                    <td width="100" align="right">&nbsp;</td>
                    <td width="50" align="right">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
      
        <?
	}
	else
	{
		$search_cond = "";$search_cond_transfer = "";

		if ($cbo_dyed_type >0)
		{
			if ($cbo_dyed_type==2)
			{
				$search_cond .= " and a.dyed_type in (0,2)";
			}else {
				$search_cond .= " and a.dyed_type in (1)";
			}

		}

		if ($cbo_yarn_type > 0)
		{
			$search_cond .= " and a.yarn_type in ($cbo_yarn_type)";
		}

		if ($txt_count != "")
		{
			$search_cond .= " and a.yarn_count_id in($txt_count)";
		}

		if ($txt_lot_no != "")
		{
			if($lot_search_type == 1)
			{
				if($db_type == 2)
				{
					$search_cond .= " and regexp_like (a.lot, '^".trim($txt_lot_no)."')";
				}
				else
				{
					$search_cond .= " and a.lot like '".trim($txt_lot_no)."%'";
				}

			}
			else
			{
				$search_cond .= " and a.lot='" . trim($txt_lot_no) . "'";
			}

		}

		if ($cbo_supplier > 0)
		{
			$search_cond .= "  and a.supplier_id in($cbo_supplier)";
		}
		if ($txt_composition != "")
		{
			$search_cond .= " and a.yarn_comp_type1st in (" .$txt_composition_id .")";
		}

		if ($show_val_column == 1) 
		{
			$value_width = 400;
			$span = 3;
			$column = '<th rowspan="2" width="90">Avg. Rate (Tk)</th><th rowspan="2" width="110">Stock Value</th><th rowspan="2" width="100">Avg. Rate (USD)</th><th rowspan="2" width="100">Stock Value (USD)</th>';
		} 
		else 
		{
			$value_width = 0;
			$span = 0;
			$column = '';
		}

		if ($store_wise == 1)
		{
			if ($store_name == 0)
				$store_cond .= "";
			else
				$store_cond .= " and a.store_id = $store_name";
			$table_width = '2900' + $value_width;
			$colspan = '28' + $span;
			$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		}
		else
		{
			$table_width = '2900' + $value_width;
			$colspan = '29' + $span;
		}

		if ($company_id == 0) 
		{
			$company_cond = "";
			$nameArray = sql_select("select allocation from variable_settings_inventory where item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
		} 
		else 
		{
			$company_cond = " and a.company_id=$company_id";
			$nameArray = sql_select("select allocation from variable_settings_inventory where company_name=$company_id and item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
		}

		$allocated_qty_variable_settings = $nameArray[0][csf('allocation')];

		$receive_array = array();
		
		$sql_receive = "Select a.prod_id,a.receive_basis,d.pay_mode as ydw_pay_mode,e.pay_mode as purchase_order_pay_mode,a.store_id, max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,
		sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as rcv_total_opening,
		sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as rcv_total_opening_amt,
		sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as rcv_total_opening_rate,         	
		sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as purchase,
		sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as purchase_amt,
		sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_loan,
		sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_loan_amt,
		sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_inside_return,
		sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_inside_return_amt,
		sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_outside_return,
		sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_outside_return_amt,0 as rcv_adjustment_qty, 0 as rcv_adjustment_amt
		from inv_transaction a left join wo_non_order_info_mst e on a.pi_wo_batch_no=e.id, inv_receive_master c left join wo_yarn_dyeing_mst d on c.booking_id=d.id and c.receive_purpose=2 where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $store_cond group by a.store_id,a.prod_id,a.receive_basis,d.pay_mode,e.pay_mode

		union all  

		Select a.prod_id,a.receive_basis,0 as ydw_pay_mode,0 as purchase_order_pay_mode,a.store_id,max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone, 0 as rcv_total_opening,0 as rcv_total_opening_amt, 0 as rcv_total_opening_rate, 0 as purchase, 0 as purchase_amt,0 as rcv_loan, 0 as rcv_loan_amt,0 as rcv_inside_return,0 as rcv_inside_return_amt,0 as rcv_outside_return,0 as rcv_outside_return_amt,sum(case when a.transaction_type in (1,4) and a.receive_basis=30 and a.transaction_date<= '". $to_date . "' then a.cons_quantity else 0 end) as rcv_adjustment_qty,sum(case when a.transaction_type in (1,4) and a.receive_basis=30 and a.transaction_date<= '" . $to_date . "' then a.cons_amount else 0 end) as rcv_adjustment_amt from inv_transaction a where a.receive_basis=30 and a.item_category=1 and a.transaction_type in (1,4) and a.status_active=1 and a.is_deleted=0 $company_cond $store_cond group by a.store_id,a.prod_id,a.receive_basis";

		//echo $sql_receive;

		$result_sql_receive = sql_select($sql_receive);
		foreach ($result_sql_receive as $row)
		{

			$receive_array[$row[csf("prod_id")]]['store_id'][$row[csf("store_id")]] = $row[csf("store_id")];
			$receive_array[$row[csf("prod_id")]]['pay_mode'] = $row[csf("pay_mode")];
			$receive_array[$row[csf("prod_id")]]['receive_basis'] = $row[csf("receive_basis")]; 
			$receive_array[$row[csf("prod_id")]]['rcv_total_opening'] += $row[csf("rcv_total_opening")];
			$receive_array[$row[csf("prod_id")]]['rcv_total_opening_amt'] += $row[csf("rcv_total_opening_amt")];
			$receive_array[$row[csf("prod_id")]]['purchase'] += $row[csf("purchase")];
			$receive_array[$row[csf("prod_id")]]['purchase_amt'] += $row[csf("purchase_amt")];
			$receive_array[$row[csf("prod_id")]]['rcv_loan'] += $row[csf("rcv_loan")];
			$receive_array[$row[csf("prod_id")]]['rcv_loan_amt'] += $row[csf("rcv_loan_amt")];
			$receive_array[$row[csf("prod_id")]]['rcv_inside_return'] += $row[csf("rcv_inside_return")];
			$receive_array[$row[csf("prod_id")]]['rcv_inside_return_amt'] += $row[csf("rcv_inside_return_amt")];
			$receive_array[$row[csf("prod_id")]]['rcv_outside_return'] += $row[csf("rcv_outside_return")];
			$receive_array[$row[csf("prod_id")]]['rcv_outside_return_amt'] += $row[csf("rcv_outside_return_amt")];

			if($row[csf("weight_per_bag")]!="" && $row[csf("weight_per_bag")]>0 && $receive_array[$row[csf("prod_id")]]['weight_per_bag']<$row[csf("weight_per_bag")]) 
			{
				$receive_array[$row[csf("prod_id")]]['weight_per_bag'] = $row[csf("weight_per_bag")];
			}

			if( $row[csf("weight_per_cone")]!="" && $row[csf("weight_per_cone")]>0 && $receive_array[$row[csf("prod_id")]]['weight_per_cone']<$row[csf("weight_per_cone")])
			{
				$receive_array[$row[csf("prod_id")]]['weight_per_cone'] = $row[csf("weight_per_cone")];
			}

			$receive_array[$row[csf("prod_id")]]['rcv_total_opening_rate'] = $row[csf("rcv_total_opening_rate")];
			$receive_array[$row[csf("prod_id")]]['rcv_adjustment_qty'] += $row[csf("rcv_adjustment_qty")];
			$receive_array[$row[csf("prod_id")]]['rcv_adjustment_amt'] += $row[csf("rcv_adjustment_amt")];
		}

		unset($result_sql_receive);

		$issue_array = array();
		$sql_issue = "select a.prod_id, a.store_id as store_id,
		sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as issue_total_opening,
		sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as issue_total_opening_rate,
		sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as issue_total_opening_amt,
		sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
		sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_inside,
		sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
		sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_outside,
		sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_outside_amt,
		sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_return,
		sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_return_amt,
		sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_loan,
		sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_loan_amt,
		0 as issue_adjustment_qty,0 as issue_adjustment_amt
		from inv_transaction a, inv_issue_master c
		where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond group by a.store_id,a.prod_id

		union all 

		select a.prod_id, a.store_id,
		0 as issue_total_opening,
		0 as issue_total_opening_rate,
		0 as issue_total_opening_amt,
		0 as issue_inside_amt,
		0 as issue_inside,
		0 as issue_inside_amt,
		0 as issue_outside,
		0 as issue_outside_amt,
		0 as rcv_return,
		0 as rcv_return_amt,
		0 as issue_loan,
		0 as issue_loan_amt,
		sum(case when a.transaction_type in (2,3) and a.receive_basis=30 and a.transaction_date<='" . $to_date . "' then a.cons_quantity else 0 end) as issue_adjustment_qty,sum(case when a.transaction_type in (2,3) and a.receive_basis=30 and a.transaction_date<='" . $to_date . "' then a.cons_amount else 0 end) as issue_adjustment_amt
		from inv_transaction a
		where a.item_category=1 and a.status_active=1 and a.is_deleted=0 $store_cond group by a.store_id,a.prod_id
		";
		$result_sql_issue = sql_select($sql_issue);
		foreach ($result_sql_issue as $row)
		{
			$issue_array[$row[csf("prod_id")]]['store_id'][$row[csf("store_id")]] = $row[csf("store_id")];
			$issue_array[$row[csf("prod_id")]]['issue_total_opening'] += $row[csf("issue_total_opening")];
			$issue_array[$row[csf("prod_id")]]['issue_total_opening_amt'] += $row[csf("issue_total_opening_amt")];
			$issue_array[$row[csf("prod_id")]]['issue_total_opening_rate'] = $row[csf("issue_total_opening_rate")];
			$issue_array[$row[csf("prod_id")]]['issue_inside'] += $row[csf("issue_inside")];
			$issue_array[$row[csf("prod_id")]]['issue_inside_amt'] += $row[csf("issue_inside_amt")];
			$issue_array[$row[csf("prod_id")]]['issue_outside'] += $row[csf("issue_outside")];
			$issue_array[$row[csf("prod_id")]]['issue_outside_amt'] += $row[csf("issue_outside_amt")];
			$issue_array[$row[csf("prod_id")]]['rcv_return'] += $row[csf("rcv_return")];
			$issue_array[$row[csf("prod_id")]]['rcv_return_amt'] += $row[csf("rcv_return_amt")];
			$issue_array[$row[csf("prod_id")]]['issue_loan'] += $row[csf("issue_loan")];
			$issue_array[$row[csf("prod_id")]]['issue_loan_amt'] += $row[csf("issue_loan_amt")];
			$issue_array[$row[csf("prod_id")]]['issue_adjustment_qty'] += $row[csf("issue_adjustment_qty")];
			$issue_array[$row[csf("prod_id")]]['issue_adjustment_amt'] += $row[csf("issue_adjustment_amt")];
		}

		unset($result_sql_issue);
		if ($store_wise == 1) {
			$trans_criteria_cond = "";
		} else {
			$trans_criteria_cond = " and c.transfer_criteria=1";
		}
		$transfer_qty_array = array();
		$sql_transfer = "select a.prod_id,
		sum(case when a.transaction_type=6 and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as trans_out_total_opening,
		sum(case when a.transaction_type=6 and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as trans_out_total_opening_amt,
		sum(case when a.transaction_type=6 and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as trans_out_total_opening_rate,
		sum(case when a.transaction_type=5 and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as trans_in_total_opening,
		sum(case when a.transaction_type=5 and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as trans_in_total_opening_rate,
		sum(case when a.transaction_type=5 and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as trans_in_total_opening_amt,
		sum(case when a.transaction_type=6 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as transfer_out_qty,
		sum(case when a.transaction_type=6 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as transfer_out_amt,
		sum(case when a.transaction_type=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as transfer_in_qty,
		sum(case when a.transaction_type=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as transfer_in_amt
		from inv_transaction a, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1  and c.is_deleted=0 $trans_criteria_cond $store_cond group by a.prod_id";
		//echo $sql_transfer;
		$result_sql_transfer = sql_select($sql_transfer);
		foreach ($result_sql_transfer as $transRow)
		{
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_qty'] = $transRow[csf("transfer_out_qty")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_amt'] = $transRow[csf("transfer_out_amt")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_qty'] = $transRow[csf("transfer_in_qty")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_amt'] = $transRow[csf("transfer_in_amt")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening'] = $transRow[csf("trans_out_total_opening")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_amt'] = $transRow[csf("trans_out_total_opening_amt")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening'] = $transRow[csf("trans_in_total_opening")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_amt'] = $transRow[csf("trans_in_total_opening_amt")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_rate'] = $transRow[csf("trans_in_total_opening_rate")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_rate'] = $transRow[csf("trans_out_total_opening_rate")];
		}

		unset($result_sql_transfer);

		if ($db_type == 0) 
		{
			$yarn_allo_sql = sql_select("select product_id, group_concat(buyer_id) as buyer_id, group_concat(allocate_qnty) as allocate_qnty from com_balk_yarn_allocate where status_active=1 group by product_id");
		} 
		else if ($db_type == 2) 
		{
			$yarn_allo_sql = sql_select("select product_id, LISTAGG(CAST(buyer_id as VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY buyer_id) as buyer_id, LISTAGG(CAST(allocate_qnty AS VARCHAR(4000)),',') WITHIN GROUP(ORDER BY allocate_qnty) as allocate_qnty from com_balk_yarn_allocate where status_active=1 group by product_id");
		}
		$yarn_allo_arr = array();
		foreach ($yarn_allo_sql as $row)
		{
			$yarn_allo_arr[$row[csf("product_id")]]['product_id'] = $row[csf("product_id")];
			$yarn_allo_arr[$row[csf("product_id")]]['buyer_id'] = implode(",", array_unique(explode(",", $row[csf("buyer_id")])));
			$yarn_allo_arr[$row[csf("product_id")]]['allocate_qnty'] = implode(",", array_unique(explode(",", $row[csf("allocate_qnty")])));
		}

		unset($yarn_allo_sql);

		$mrr_rate_sql = sql_select("select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction
				where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) group by prod_id");
			$mrr_rate_arr = array();
			foreach ($mrr_rate_sql as $row) 
			{
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
				$mrr_rate_arr[$row[csf("prod_id")]] = $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
			}

			unset($mrr_rate_sql);

			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group 
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond 
			group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group 
			order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
			//echo $sql;die;
			$result = sql_select($sql);
				
			$yarnTestQalityCommentsArr = return_library_array("select a.prod_id as prod_id, b.comments_author as comments_author from inv_yarn_test_mst a, inv_yarn_test_comments b where a.id=b.mst_table_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.comments_author IS NOT NULL", 'prod_id', 'comments_author');//

			$i = 1;
				//ob_start();
			if($type==5)
			{
				$tblWidth = "1650";
				$colspan = "9";
			}else{
				$tblWidth = "1460";
				$colspan = "7";
			}
			?>


							<table width="<? echo $tblWidth;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Yarn Stock </td>
						</tr>
						<tr style="border:none;">
							<td colspan="17" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $company_id)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="17" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
							</td>
						</tr>
						<tr  style="word-break:normal;">
							<th rowspan="2" width="40">SL</th>
							<th rowspan="2" width="120">Company Name</th>
							<th colspan="7" width="630">Description</th>
							<th rowspan="2" width="100">Stock In Hand</th>

							<th rowspan="2" width="100">Allocated to Order</th>
							<th rowspan="2" width="100">Un Allocated Qty.</th>
							<?
							if($type == 5)
							{
								?>
								<th rowspan="2" width="90">Avg. Rate (USD)</th>
								<th rowspan="2" width="100">Stock Value (USD)</th>
								<?
							}
							?>
							<th rowspan="2" width="50">Age (Days)</th>
							<th rowspan="2" width="140">Yarn Quality<br>Comments</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<th width="60">Prod.ID</th>
							<th width="60">Count</th>
							<th width="150">Composition</th>
							<th width="100">Yarn Type</th>
							<th width="80">Color</th>
							<th width="100">Lot</th>
							<th width="80">Supplier</th>
						</tr>
					</thead>

						<?
						$grand_total_alocatted=0;$grand_total_free_stock=0;
						$tot_stock_value = 0;
						foreach ($result as $row)
						{
							$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));

							$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
							if ($row[csf("yarn_comp_type2nd")] != 0)
								$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
							
							$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
							$transfer_in_amt = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
							$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
							$transfer_out_amt = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];

							$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
							$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];
							$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];
							$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];

							$pay_mode = $receive_array[$row[csf("id")]]['pay_mode'];
							$receive_basis = $receive_array[$row[csf("id")]]['receive_basis'];

							$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
							$openingBalanceAmt = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);

							$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
							$totalRcvAmt = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $transfer_in_amt;
							$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;
							$totalIssueAmt = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] + $transfer_out_amt;


							$stockInHand = $openingBalance + $totalRcv - $totalIssue;
							$tot_rcv_qnty = $openingBalance + $totalRcv;
							$stockInHandAmt = $openingBalanceAmt + $totalRcvAmt - $totalIssueAmt;
							$tot_rcv_amt = $openingBalanceAmt + $totalRcvAmt;
							$avg_rate=$tot_rcv_amt/$tot_rcv_qnty;

									//subtotal and group-----------------------
							$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

							if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0)) {

								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
									
								//for supplier
								if($row[csf('is_within_group')] == 1)
								{
									$supplier_name = $companyArr[$row[csf('supplier_id')]];
								}
								else
								{
									$supplier_name = $supplierArr[$row[csf('supplier_id')]];
								}
								//end for supplier
									
								//number_format($stockInHand,2)
								if ($value_with == 1)
								{
									if (number_format($stockInHand, 2) > 0.00)
									{
										if (!in_array($check_string, $checkArr)) 
										{
											$checkArr[$i] = $check_string;
											if ($i > 1) {
												?>
												<tr bgcolor="#CCCCCC" style="font-weight:bold">
													<td colspan="9" align="right">Sub Total</td>
													<td align="right"><? echo number_format($total_stock_in_hand, 2); ?></td>
													<td align="right"><? echo number_format($total_alocatted, 2);$total_alocatted = 0; ?></td>
													<td align="right"><? echo number_format($total_free_stock, 2);$total_free_stock = 0; ?></td>
													<?
													if($type==5)
													{
														?>
														<td align="right">&nbsp;</td>
														<td align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($sub_stock_value_usd, 2); ?></td>
														<?
													}
													?>

													<td align="right">&nbsp;</td>
													<td align="right">&nbsp;</td>
													<td align="right">&nbsp;</td>
												</tr>
												<?
												$total_stock_in_hand = 0;
												$sub_stock_value = 0;
												$sub_stock_value_usd = 0;
											}
										}


												  $stock_value = $stockInHand * $avg_rate;
												  $avg_rate_usd = $avg_rate / $exchange_rate;
												  $stock_value_usd = $stock_value / $exchange_rate;
												  ?>
												  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
													<td width="40"><? echo $i; ?></td>
													<td width="120"><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
													<td width="60"><? echo $row[csf("id")]; ?></td>
													<td width="60"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></p></td>
													<td width="150"><p><? echo $compositionDetails; ?></p></td>
													<td width="100"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?></p></td>
													<td width="80"><p><? echo $color_name_arr[$row[csf("color")]]; ?></p></td>
													<td width="100"><p>
														<?
														if( $yarnTestArr[$row[csf("id")]] != "") { ?>
															<a href='##' onclick="show_test_report2('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
															<? 
														}
														else if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "")
														{
															?>
															<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"><? echo $row[csf("lot")]; ?></a>
															<?
														} 
														else 
														{
															echo $row[csf("lot")];
														}
														?></p> </td>
													<td width="80">
														<p>
														<?
														
														echo $supplier_name;
														?></p>
													</td>
													<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" ><a href='##' onclick="openmypage_stock('<? echo $row[csf('id')] . "_" . $store_name ?>', 'stock_popup')"><? echo number_format($stockInHand, 2); ?></a></td>

													<td width="100" align="right"><p style="word-wrap:break-word; word-break: break-all;" ><a href='##' onclick="openmypage('<? echo $row[csf('id')]; ?>','allocation_popup')"><? echo number_format($row[csf("allocated_qnty")], 2);?></a></p></td>
													<td width="100" align="right"><p style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($row[csf("available_qnty")], 2) ;?></p></td>
													<?
													if($type==5)
													{
														?>
														<td width="90" align="right" title="<? echo "op bal qnty=" . $openingBalance . "tot rcv qnty=" . $totalRcv . "op bal Amt" . $openingBalanceAmt . "tot_rcv Amt" . $totalRcvAmt . "Rate=" . $avg_rate . "=" . $exchange_rate; ?>"><? echo number_format($avg_rate_usd, 4); ?></td>
														
														
														<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stock_value_usd, 2); ?></td>
														<?
													}
													?>

													<td width="50" align="right"><? echo $ageOfDays; ?></td>
													<td width="140" align="left"><p>
														<?
														if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "")
														{
															?>
															<span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?> </span>
															<?
														}
														?>
													</p></td>
													<td align="right">
														<p>
															<?
															$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
															$allocate_qnty_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['allocate_qnty']);
															$buyer_all = "";
															$m = 0;
															foreach ($buyer_id_arr as $buy_id) {
																if ($buyer_all != "")
																	$buyer_all .= "<br>";
																$buyer_all .= $buy_short_name_arr[$buy_id];
																if ($buyer_all != "")
																	$buyer_all .= "&nbsp;Qnty: " . $allocate_qnty_arr[$m];
																$m++;
															}
															echo $buyer_all;
															?>&nbsp;
														</p>
													</td>
												  </tr>
												  <?
												  $i++;

												  $total_stock_in_hand += $stockInHand;
												  $sub_stock_value += $stock_value;
												  $sub_stock_value_usd += $stock_value_usd;

												  $grand_total_stock_in_hand += $stockInHand;
												  $tot_stock_value += $stock_value;
												  $tot_stock_value_usd += $stock_value_usd;

												  $total_alocatted += $row[csf("allocated_qnty")];
												  $total_free_stock += $row[csf("available_qnty")];
												  $grand_total_alocatted += $row[csf("allocated_qnty")];
												  $grand_total_free_stock += $row[csf("available_qnty")];

											  }
										  }
										  else
										  {
											if (!in_array($check_string, $checkArr)) {
												$checkArr[$i] = $check_string;
												if ($i > 1) {
													?>
													<tr bgcolor="#CCCCCC" style="font-weight:bold">
														<td colspan="9" align="right">Sub Total</td>
														<td align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($total_stock_in_hand, 2); ?></td>
														<td align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($total_alocatted, 2);$total_alocatted = 0; ?></td>
														<td align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($total_free_stock, 2);$total_free_stock = 0; ?></td>

														<?
														if($type==5)
														{
															?>
															<td align="right">&nbsp;</td>
															<td align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($sub_stock_value_usd, 2); ?></td>
															<?
														}
														?>

														<td align="right">&nbsp;</td>
														<td align="right">&nbsp;</td>
														<td width="" align="right">&nbsp;</td>
													</tr>
													<?
													$total_stock_in_hand = 0;
													$sub_stock_value = 0;
													$sub_stock_value_usd = 0;

												}
											}

											$stock_value = $stockInHand * $avg_rate;
											$avg_rate_usd = $avg_rate / $exchange_rate;
											$stock_value_usd = $stock_value / $exchange_rate;
									  ?>
									  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="120"><p><? echo $companyArr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
										<td width="60"><? echo $row[csf("id")]; ?></td>
										<td width="60"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?>&nbsp;</p></td>
										<td width="150"><p><? echo $compositionDetails; ?>&nbsp;</p></td>
										<td width="100"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
										<td width="80"><p><? echo $color_name_arr[$row[csf("color")]]; ?>&nbsp;</p></td>
										<td width="100"><p style="word-break:break-all;">
											<?
											if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "")
											{
												?>
												<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
												<?
											}else{
												echo $row[csf("lot")];
											}
											?>
										&nbsp;</p></td>
										<td width="80">
										<p>
										<?
                                        
                                        echo $supplier_name;
                                        ?>
                                        &nbsp;
										</p>
										</td>
										<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" >
											<a href='##' onclick="openmypage_stock('<? echo $row[csf('id')] . "_" . $store_name ?>', 'stock_popup')"><? echo number_format($stockInHand, 2); ?></a>
										</td>

										<td width="100" align="right"><p style="word-wrap:break-word; word-break: break-all;" ><a href='##' onclick="openmypage('<? echo $row[csf('id')]; ?>','allocation_popup')"><? echo number_format($row[csf("allocated_qnty")], 2);?></a></p></td>
										<td width="100" align="right"><p style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($row[csf("available_qnty")], 2) ;?></p></td>
										<?
										if($type == 5)
										{
										?>
											<td width="90" align="right"><? echo number_format($avg_rate_usd, 4); ?></td>
											<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stock_value_usd, 2); ?></td>
											<?
										}
										?>

										<td width="50" align="right"><? echo $ageOfDays; ?></td>
										<td width="140" align="left">
											<span class="comment more">
												<?
												if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "")
												{
													echo $yarnTestQalityCommentsArr[$row[csf("id")]];
												}
												?>
											</span>
										</td>
										<td align="right">
											<p>
												<?
												$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
												$allocate_qnty_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['allocate_qnty']);
												$buyer_all = "";
												$m = 0;
												foreach ($buyer_id_arr as $buy_id) {
													if ($buyer_all != "")
														$buyer_all .= "<br>";
													$buyer_all .= $buy_short_name_arr[$buy_id];
													if ($buyer_all != "")
														$buyer_all .= "&nbsp;Qnty: " . $allocate_qnty_arr[$m];
													$m++;
												}
												echo $buyer_all;
												?>&nbsp;
											</p>
										</td>
									  </tr>
									  <?
									  $i++;
									  $total_stock_in_hand += $stockInHand;
									  $sub_stock_value += $stock_value;
									  $sub_stock_value_usd += $stock_value_usd;

									  $grand_total_stock_in_hand += $stockInHand;
									  $tot_stock_value += $stock_value;
									  $tot_stock_value_usd += $stock_value_usd;

									  $total_alocatted += $row[csf("allocated_qnty")];
									  $total_free_stock += $row[csf("available_qnty")];

									  $grand_total_alocatted += $row[csf("allocated_qnty")];
									  $grand_total_free_stock += $row[csf("available_qnty")];


									}
								}
							}
							?>
							<tr bgcolor="#CCCCCC" style="font-weight:bold">
								<td colspan="9" align="right">Sub Total</td>
								<td align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($total_stock_in_hand, 2); ?></td>
								<td align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($total_alocatted, 2);$total_alocatted = 0; ?></td>
								<td align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($total_free_stock, 2);$total_free_stock = 0; ?></td>
								
								<?
								if($type==5)
								{
									?>
									<td align="right">&nbsp;</td>
									<td align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($sub_stock_value_usd, 2); ?></td>
									<?
								}
								?>
								<td align="right">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td align="right">&nbsp;</td>
							</tr>

						<tr class="tbl_bottom">
							
							<td width="40"></td>
							<td width="120"></td>
							<td width="60"></td>
							<td width="60"></td>
							<td width="150"></td>
							<td width="100"></td>
							<td width="80"></td>
							<td width="100"></td>
							
							<td  width="80" align="right">Grand Total</td>
							<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"  id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 2); ?></td>
							
							<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($grand_total_alocatted, 2); ?></td>
							<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($grand_total_free_stock, 2); ?></td>							
							<?
							if($type==5)
							{
								?>
								<td width="90" align="right">&nbsp;</td>
								<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($tot_stock_value_usd, 2); ?></td>
								<?
							}
							?>

							<td width="50" align="right">&nbsp;</td>
							<td width="140" align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
						</tr>
					</table>
				<?
		
		//include("daily_yarn_stock_auto_mail_content_pdf.php");
	
	}
	
	$html = ob_get_contents();
	ob_end_clean();

	 



 
	foreach (glob("../tmp/"."*.pdf") as $filename) {			
		@unlink($filename);
	}
	$att_file_arr=array();
	$mpdf = new mPDF();
	$mpdf->WriteHTML($html,2);
	$REAL_FILE_NAME = 'daily_yarn_stock_auto_mail_' . date('j-M-Y_h-iA') . '.pdf';
	$mpdf->Output('../tmp/' . $REAL_FILE_NAME, 'F');
	$att_file_arr[]='../tmp/'.$REAL_FILE_NAME.'**'.$REAL_FILE_NAME;
	
	$to="";	
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=88 and a.company_id=".$company_id." and b.mail_user_setup_id=c.id AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";//and 
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if($row[csf('email_address')]){$toMailArr[]=$row[csf('email_address')]; }
	}
 	
	$to=implode(',',$toMailArr);
	$subject = "Daily yarn stock auto mail";
	$message="<b>Sir,</b><br>Please check daily yarn stock att. file";
	
	
 

	if($_REQUEST['isview']==1){
		$mail_item=88;
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message."<br>".$html;
	}
	else{
		$header=mailHeader();
		require_once('../../mailer/class.phpmailer.php');
		require('../setting/mail_setting.php');
		
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);}
	}

	unset($html);
	
	
}

}//company
 
 
 

 
	?>
