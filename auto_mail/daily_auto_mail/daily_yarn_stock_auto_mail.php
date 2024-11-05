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
	
 


	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$companyArr[0] = "All Company";
	$supplierArr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_dtls = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$yarnTestQalityCommentsArr = return_library_array("select prod_id, yarn_quality_coments from inv_yarn_test_mst where  status_active=1 and is_deleted=0", 'prod_id', 'yarn_quality_coments');
	$yarnTestArr = return_library_array("select prod_id, lot_number from inv_yarn_test_mst where status_active=1 and is_deleted=0", 'prod_id', 'lot_number');
	$floor_room_rack_arr = return_library_array("select floor_room_rack_id,floor_room_rack_name from lib_floor_room_rack_mst", 'floor_room_rack_id', 'floor_room_rack_name');
	//echo '<pre>';print_r($yarnTestArr);die;








foreach($companyArr as $company_id=>$company_name){
$type=1;

if($type==10){
	$action="generate_report";
	$cbo_company_name=$company_id;
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
}
else if($type==1){
	$action=	"generate_report";
	$cbo_company_name=	"0";
	$cbo_dyed_type=	"0";
	$cbo_yarn_type=	"";
	$txt_count=	"";
	$txt_lot_no=	"";
	$from_date=	"28-09-2022";
	$to_date=	"28-09-2022";
	$store_wise=	"2";
	$store_name=	"0";
	$value_with=	"1";
	$cbo_supplier=	"";
	$show_val_column=	"0";
	$get_upto=	"0";
	$txt_days=	"";
	$get_upto_qnty=	"0";
	$txt_qnty=	"";
	$type=	"1";
	$txt_composition=	"";
	$txt_composition_id	="";
	$lot_search_type=	"0";
	$source_name=	"0";
}	






//--------------------------------------------------------------------------------------------

if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));



	if ($db_type == 0)
	{
		$exchange_rate = return_field_value("conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and is_deleted=0 order by id DESC limit 0,1");
		$from_date = change_date_format($from_date, 'yyyy-mm-dd');
		$to_date = change_date_format($to_date, 'yyyy-mm-dd');
	}
	else if ($db_type == 2)
	{
		$exchange_rate = return_field_value("conversion_rate", "(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)", "ROWNUM = 1");
		$from_date = change_date_format($from_date, '', '', 1);
		$to_date = change_date_format($to_date, '', '', 1);
	}
	else
	{
		$from_date = "";
		$to_date = "";
		$exchange_rate = 1;
	}

	$txt_lot_no = trim($txt_lot_no);
	$txt_composition = trim($txt_composition);
	
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
		.wrap_break {
			word-wrap: break-word;
			word-break: break-all;
		}

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

		if ($cbo_company_name == 0) 
		{
			$company_cond = "";
			$company_cond_mrr = "";
		} 
		else 
		{
			$company_cond = " and b.company_id=$cbo_company_name";
			$company_cond_mrr = " and a.company_id=$cbo_company_name";
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
        //ob_start();
        ?>
        <div>
      		<table width="1780" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px"  rules="all" id="table_header_1" >
	      		<thead>
	      			<tr class="form_caption" style="border:none;">
	      				<td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Yarn Stock </td>
	      			</tr>
	      			<tr style="border:none;">
	      				<td colspan="17" align="center" style="border:none; font-size:14px;">
	      					Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
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
                                /* $sub_stock_in_hand+=$stockInHand;
                                  $sub_stock_value+=$stock_value;
                                  $sub_stock_value_usd+=$stock_value_usd;

                                  $grand_total_stock_in_hand+=$stockInHand;
                                  $grand_total_stock_value+=$stock_value;
                                  $grand_total_stock_value_usd+=$stock_value_usd; */

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
                            //$stock_value_usd=($stockInHand*$row[csf("avg_rate_per_unit")])/$exchange_rate;
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
        </div>
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
			$table_width = '3400' + $value_width;
			$colspan = '28' + $span;
			$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		}
		else
		{
			$table_width = '2900' + $value_width;
			$colspan = '29' + $span;
		}

		if ($cbo_company_name == 0) 
		{
			$company_cond = "";
			$nameArray = sql_select("select allocation from variable_settings_inventory where item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
		} 
		else 
		{
			$company_cond = " and a.company_id=$cbo_company_name";
			$nameArray = sql_select("select allocation from variable_settings_inventory where company_name=$cbo_company_name and item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
		}

		$allocated_qty_variable_settings = $nameArray[0][csf('allocation')];

		$receive_array = array();
		
		$sql_receive = "SELECT a.prod_id,a.receive_basis,d.pay_mode as ydw_pay_mode,e.pay_mode as purchase_order_pay_mode,a.store_id, max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,
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
		sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_outside_return_amt,0 as rcv_adjustment_qty, 0 as rcv_adjustment_amt, a.floor_id, a.room, a.rack, a.self, a.bin_box
		from inv_transaction a left join wo_non_order_info_mst e on a.pi_wo_batch_no=e.id, inv_receive_master c left join wo_yarn_dyeing_mst d on c.booking_id=d.id and c.receive_purpose=2 where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $store_cond group by a.store_id,a.prod_id,a.receive_basis,d.pay_mode,e.pay_mode,a.floor_id,a.room,a.rack, a.self, a.bin_box

		union all  

		SELECT a.prod_id,a.receive_basis,0 as ydw_pay_mode,0 as purchase_order_pay_mode,a.store_id,max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone, 0 as rcv_total_opening,0 as rcv_total_opening_amt, 0 as rcv_total_opening_rate, 0 as purchase, 0 as purchase_amt,0 as rcv_loan, 0 as rcv_loan_amt,0 as rcv_inside_return,0 as rcv_inside_return_amt,0 as rcv_outside_return,0 as rcv_outside_return_amt,sum(case when a.transaction_type in (1,4) and a.receive_basis=30 and a.transaction_date<= '". $to_date . "' then a.cons_quantity else 0 end) as rcv_adjustment_qty,sum(case when a.transaction_type in (1,4) and a.receive_basis=30 and a.transaction_date<= '" . $to_date . "' then a.cons_amount else 0 end) as rcv_adjustment_amt, a.floor_id, a.room, a.rack, a.self, a.bin_box from inv_transaction a where a.receive_basis=30 and a.item_category=1 and a.transaction_type in (1,4) and a.status_active=1 and a.is_deleted=0 $company_cond $store_cond group by a.store_id,a.prod_id,a.receive_basis, a.floor_id, a.room,a.rack, a.self, a.bin_box";

		//echo $sql_receive;

		$result_sql_receive = sql_select($sql_receive);
		$storeArr = array();
		foreach ($result_sql_receive as $row)
		{

			$receive_array[$row[csf("prod_id")]]['store_id'][$row[csf("store_id")]] = $row[csf("store_id")];
			$receive_array[$row[csf("prod_id")]]['floor_id'][$row[csf("floor_id")]] = $row[csf("floor_id")];
			$receive_array[$row[csf("prod_id")]]['room'][$row[csf("room")]] = $row[csf("room")];
			$receive_array[$row[csf("prod_id")]]['rack'][$row[csf("rack")]] = $row[csf("rack")];
			$receive_array[$row[csf("prod_id")]]['self'][$row[csf("self")]] = $row[csf("self")];
			$receive_array[$row[csf("prod_id")]]['bin_box'][$row[csf("bin_box")]] = $row[csf("bin_box")];
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
		0 as issue_adjustment_qty,0 as issue_adjustment_amt, a.floor_id, a.room,a.rack, a.self, a.bin_box
		from inv_transaction a, inv_issue_master c
		where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond group by a.store_id,a.prod_id, a.floor_id, a.room,a.rack, a.self, a.bin_box

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
		sum(case when a.transaction_type in (2,3) and a.receive_basis=30 and a.transaction_date<='" . $to_date . "' then a.cons_quantity else 0 end) as issue_adjustment_qty,sum(case when a.transaction_type in (2,3) and a.receive_basis=30 and a.transaction_date<='" . $to_date . "' then a.cons_amount else 0 end) as issue_adjustment_amt, a.floor_id, a.room,a.rack, a.self, a.bin_box
		from inv_transaction a
		where a.item_category=1 and a.status_active=1 and a.is_deleted=0 $store_cond group by a.store_id,a.prod_id, a.floor_id, a.room,a.rack, a.self, a.bin_box";
		//echo $sql_issue;
		$result_sql_issue = sql_select($sql_issue);
		foreach ($result_sql_issue as $row)
		{
			$issue_array[$row[csf("prod_id")]]['store_id'][$row[csf("store_id")]] = $row[csf("store_id")];
			$issue_array[$row[csf("prod_id")]]['floor_id'][$row[csf("floor_id")]] = $row[csf("floor_id")];
			$issue_array[$row[csf("prod_id")]]['room'][$row[csf("room")]] = $row[csf("room")];
			$issue_array[$row[csf("prod_id")]]['rack'][$row[csf("rack")]] = $row[csf("rack")];
			$issue_array[$row[csf("prod_id")]]['self'][$row[csf("self")]] = $row[csf("self")];
			$issue_array[$row[csf("prod_id")]]['bin_box'][$row[csf("bin_box")]] = $row[csf("bin_box")];
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

		if ($type == 1)
		{
			?>
		 
			<?
			$date_array = array();
			$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 group by prod_id";
			$result_returnRes_date = sql_select($returnRes_date);
			foreach ($result_returnRes_date as $row)
			{
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
			}

			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
			$result = sql_select($sql);
			$i = 1;
			//ob_start();

			$mrr_rate_sql = sql_select("select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction
				where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) group by prod_id");
			$mrr_rate_arr = array();
			foreach ($mrr_rate_sql as $row) {
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
				$mrr_rate_arr[$row[csf("prod_id")]] = $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
			}

			//echo "<pre>";
			//print_r($mrr_rate_arr);
			?>
			<div>
				<table width="<? echo $table_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<tr class="form_caption" style="border:none;">
						<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Yarn Stock </td>
					</tr>
					<tr style="border:none;">
						<td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:14px;">
							Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
						</td>
					</tr>
				</table>
				<table width="<? echo $table_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr>
							<th rowspan="2" width="30">SL</th>
							<th rowspan="2" width="130">Company</th>
							<th colspan="7">Description</th>
							<th rowspan="2" width="100">Wgt. Bag/Cone</th>
							<th rowspan="2" width="110">OpeningStock</th>
							<th colspan="6">Receive</th>
							<th colspan="6">Delivery</th>
							<th rowspan="2" width="100">Stock InHand</th>
							<?
							echo $column;
							if ($store_wise == 1) {
								echo '<th rowspan="2" width="100">Store Name</th>';
							} else {
								echo '<th rowspan="2" width="100">Allocated to Order</th>';
								echo '<th rowspan="2" width="100">Un Allocated Qty.</th>';
							}
							?>
							<th rowspan="2" width="50">Age (Days)</th>
							<th rowspan="2" width="50">DOH</th>
							<th rowspan="2" width="140">Yarn Quality<br>Comments</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<th width="60">Prod.ID</th>
							<th width="60">Count</th>
							<th width="150">Composition</th>
							<th width="100">Yarn Type</th>
							<th width="120">Color</th>
							<th width="100">Lot</th>
							<th width="100">Supplier</th>
							<th width="90">Purchase</th>
							<th width="90">Inside Return</th>
							<th width="90">Outside Return</th>
							<th width="90">Transfer In</th>
							<th width="90">Loan</th>
							<th width="100">Total Recv</th>
							<th width="90">Inside</th>
							<th width="90">Outside</th>
							<th width="90">Recv. Return</th>
							<th width="90">Trans. Out</th>
							<th width="90">Loan</th>
							<th width="100">Total Delivery</th>
						</tr>
					</thead>
				</table>
				<div style="width:<? echo $table_width + 38; ?>px;" id="scroll_body" >
					<table style="width:<? echo $table_width +20; ?>px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$tot_stock_value = 0;
						foreach ($result as $row)
						{
							$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
							$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

							$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
							if ($row[csf("yarn_comp_type2nd")] != 0)
								$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
							$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
							$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

							$transfer_in_amt = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
							$transfer_out_amt = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];


							$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
							$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

							$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];
							$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];

							$pay_mode = $receive_array[$row[csf("id")]]['pay_mode'];
							$receive_basis = $receive_array[$row[csf("id")]]['receive_basis'];

							$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

							$openingBalanceAmt = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);

							$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $receive_array[$row[csf("id")]]['rcv_adjustment_qty'] + $transfer_in_qty;
							
							$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $issue_array[$row[csf("id")]]['issue_adjustment_qty'] + $transfer_out_qty;

							$totalRcvAmt = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $receive_array[$row[csf("id")]]['rcv_adjustment_amt'] + $transfer_in_amt;

							$totalIssueAmt = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] +$issue_array[$row[csf("id")]]['issue_adjustment_amt'] + $transfer_out_qty;


							$stockInHand = $openingBalance + $totalRcv - $totalIssue;

							$stockInHandAmt = $openingBalanceAmt + $totalRcvAmt - $totalIssueAmt;

							//subtotal and group-----------------------
							$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

							if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0))
							{
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
									
								if($value_with == 1)
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
													<td colspan="10" align="right">Sub Total</td>
													<td width="110" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
													<?
													if ($show_val_column == 1) {
														echo '<td width="90" align="right">&nbsp;</td>';
														echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
														echo '<td width="100" align="right">&nbsp;</td>';
														echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
													}

													if ($store_wise == 1) {
														echo '<td width="100">&nbsp;</td>';
													} else {
														if ($allocated_qty_variable_settings == 1) {
															echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
															echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
														} else {
															echo '<td width="100" align="right">&nbsp;</td>';
															echo '<td width="100" align="right">&nbsp;</td>';
														}
													}
													?>
													<td width="50" align="right">&nbsp;</td>
													<td width="50" align="right">&nbsp;</td>
													<td align="right">&nbsp;</td>
													<td width="" align="right">&nbsp;</td>
												</tr>
												<?
												$total_opening_balance = 0;
												$total_purchase = 0;
												$total_inside_return = 0;
												$total_outside_return = 0;
												$total_rcv_loan = 0;
												$total_total_rcv = 0;
												$total_issue_inside = 0;
												$total_issue_outside = 0;
												$total_receive_return = 0;
												$total_issue_loan = 0;
												$total_total_delivery = 0;
												$total_stock_in_hand = 0;
												$total_alocatted = 0;
												$total_free_stock = 0;
												$sub_stock_value = 0;
												$sub_stock_value_usd = 0;
												$total_transfer_out_qty = 0;
												$total_transfer_in_qty = 0;
											}
										}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30"><? echo $i; ?></td>
											<td width="130" class="wrap_break" ><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
											<td width="60"><? echo $row[csf("id")]; ?></td>
											<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
											<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
											<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
											<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
											<td width="100"><p>
												<?
													if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
													<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
												<? }else if( $yarnTestArr[$row[csf("id")]] != "") { ?>
													<a href='##' onclick="show_test_report2('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
												<? } else {
													echo $row[csf("lot")];
												} ?></p> </td>
											<td width="100" class="wrap_break" >
												<?
												//commented dated 16.09.2021
												/*if($receive_basis==2) // work order basis
												{													
													if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
													{
														echo $companyArr[$row[csf("supplier_id")]];
													}else{
														echo $supplierArr[$row[csf("supplier_id")]];
													}
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}*/
												echo $supplier_name;
												?>
											</td>
											<td width="100" class="wrap_break" ><? echo 'Bg:' . $receive_array[$row[csf("id")]]['weight_per_bag'] . '; ' . 'Cn:' . $receive_array[$row[csf("id")]]['weight_per_cone']; ?></td>
											<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
											<td width="90" class="wrap_break" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break">
												<?
												if ($store_wise == 1) {
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
												} else {
													echo number_format($transfer_in_qty, 2);
												}
												?>
											</td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break">
												<?
												if ($store_wise == 1) {
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
												} else {
													echo number_format($transfer_out_qty, 2);
												}
												?>
											</td>
											<td width="90" class="wrap_break" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($stockInHand, 4); ?></td>
											<?

											$stock_value = 0;
											if ($show_val_column == 1)
											{
												$avg_rate = ($stockInHandAmt/$stockInHand); //$mrr_rate_arr[$row[csf("id")]];
												if($avg_rate>0)
												{
													$avg_rate=$avg_rate;	
												}else{
													$avg_rate = "0.00"; //$mrr_rate_arr[$row[csf("id")]];
												}
												
												$stock_value = $stockInHand * $avg_rate;
												$stock_value_usd = ($stockInHand * $avg_rate) / $exchange_rate;
												$avz_rates_usd=0;

												if(number_format($stock_value_usd,2)>0 && number_format($stockInHand,2)>0){
													$avz_rates_usd=$stock_value_usd/$stockInHand;
												}else{
													$avz_rates_usd="0.00";
												}

												echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
												echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
												echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
												echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
											}

											if ($store_wise == 1)
											{
												$store_name = '';
												//$receive_store_id = explode(",", $receive_array[$row[csf("id")]]['store_id']);
												//$issue_store_id = explode(",", $issue_array[$row[csf("id")]]['store_id']);
												$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
												$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];
												
												$store_id = array_unique(array_merge($receive_store_id, $issue_store_id));
												foreach ($store_id as $val) {
													if ($store_name == "")
														$store_name = $store_arr[$val];
													else
														$store_name .= ", " . $store_arr[$val];
												}
												echo '<td width="100" class="wrap_break">' . $store_name . '</td>';
											}
											else
											{
												if ($allocated_qty_variable_settings == 1) {
													echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
													echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
												} else {
													echo '<td width="100" align="right">&nbsp;</td>';
													echo '<td width="100" align="right">&nbsp;</td>';
												}
											}
											?>
											<td width="50" align="right" class="wrap_break"><? echo $ageOfDays; //$ageOfDays;         ?></td>
											<td width="50" align="right" class="wrap_break">
											<?if($daysOnHand >= 180){?>
											<p style="background-color: red;" title="180 days or above">
											<?
											if ($stockInHand > 0)
												echo $daysOnHand;
											else
											echo "&nbsp;"; //$daysOnHand;
											?></p>
											<?}else{?>
											<p>
											<?
											if ($stockInHand > 0)
												echo $daysOnHand;
											else
											echo "&nbsp;"; //$daysOnHand;
											?></p>	
											<?}?>
											</td>

											<?
											if( $yarnTestQalityCommentsArr[$row[csf("id")]] != ""){
												?>
												<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
											<?  }else{ ?>
												<td width="140" align="left"><span class="wrap_break"><? echo "&nbsp;"; ?></span></td>
											<? 	} ?>


											<td width="" align="center" class="wrap_break">
												<?
											/*$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
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
											echo $buyer_all;*/
											?>
											<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
											</td>
										</tr>
										<?
										$i++;

										$total_opening_balance += $openingBalance;
										$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
										$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
										$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
										$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
										$total_total_rcv += $totalRcv;
										$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
										$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
										$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
										$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
										$total_total_delivery += $totalIssue;
										$total_stock_in_hand += $stockInHand;
										$total_alocatted += $row[csf("allocated_qnty")];
										$total_free_stock += $row[csf("available_qnty")];
										$sub_stock_value += $stock_value;
										$sub_stock_value_usd += $stock_value_usd;
										$total_transfer_out_qty += $transfer_out_qty;
										$total_transfer_in_qty += $transfer_in_qty;

										//grand total===========================
										$grand_total_opening_balance += $openingBalance;
										$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
										$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
										$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
										$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
										$grand_total_total_rcv += $totalRcv;
										$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
										$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
										$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
										$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
										$grand_total_total_delivery += $totalIssue;
										$grand_total_stock_in_hand += $stockInHand;
										$grand_total_alocatted += $row[csf("allocated_qnty")];
										$grand_total_free_stock += $row[csf("available_qnty")];
										$tot_stock_value += $stock_value;
										$tot_stock_value_usd += $stock_value_usd;

										$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
										$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
									}
								}
								else
								{
									if (!in_array($check_string, $checkArr))
									{
										$checkArr[$i] = $check_string;
										if ($i > 1)
										{
											?>
											<tr bgcolor="#CCCCCC" style="font-weight:bold">
												<td colspan="10" align="right">Sub Total</td>
												<td width="110" class="wrap_break" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
												<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
												<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
												<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
												<?
												if ($show_val_column == 1) {
													echo '<td width="90" align="right" >&nbsp;</td>';
													echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
													echo '<td width="100" align="right">&nbsp;</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
												}

												if ($store_wise == 1) {
													echo '<td width="100">&nbsp;</td>';
												} else {
													if ($allocated_qty_variable_settings == 1) {
														echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
														echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
													} else {
														echo '<td width="100" align="right">&nbsp;</td>';
														echo '<td width="100" align="right">&nbsp;</td>';
													}
												}
												?>
												<td width="50" align="right">&nbsp;</td>
												<td width="50" align="right">&nbsp;</td>
												<td width="" align="right">&nbsp;</td>
												<td width="" align="right">&nbsp;</td>
											</tr>
											<?
											$total_opening_balance = 0;
											$total_purchase = 0;
											$total_inside_return = 0;
											$total_outside_return = 0;
											$total_rcv_loan = 0;
											$total_total_rcv = 0;
											$total_issue_inside = 0;
											$total_issue_outside = 0;
											$total_receive_return = 0;
											$total_issue_loan = 0;
											$total_total_delivery = 0;
											$total_stock_in_hand = 0;
											$total_alocatted = 0;
											$total_free_stock = 0;
											$sub_stock_value = 0;
											$sub_stock_value_usd = 0;
											$total_transfer_out_qty = 0;
											$total_transfer_in_qty = 0;
										}
									}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30"><? echo $i; ?></td>
										<td width="130" class="wrap_break"><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
										<td width="60" class="wrap_break"><? echo $row[csf("id")]; ?></td>
										<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
										<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
										<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
										<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
										<td width="100" class="wrap_break" style="mso-number-format:'\@';"><p>
											<? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
												<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
											<? }else{
												echo $row[csf("lot")];
											}
											?>
										&nbsp;</p></td>

										<td width="100" class="wrap_break">
											<?
											//commented by Zaman dated 19.09.2021
											/*if($receive_basis==2) // work order basis
											{													
												if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
												{
													echo $companyArr[$row[csf("supplier_id")]];
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}
											}else{
												echo $supplierArr[$row[csf("supplier_id")]];
											}*/
											echo $supplier_name;
											?>
										</td>
										<td width="100" class="wrap_break"><? echo 'Bg:' . $receive_array[$row[csf("id")]]['weight_per_bag'] . '; ' . 'Cn:' . $receive_array[$row[csf("id")]]['weight_per_cone']; ?></td>
										<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
										<td width="90" class="wrap_break" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
										<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
										<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
										<td width="90" align="right" class="wrap_break">
											<?
											if ($store_wise == 1) {
												echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
											} else {
												echo number_format($transfer_in_qty, 2);
											}
											?>
										</td>
										<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
										<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
										<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
										<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
										<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
										<td width="90" align="right" class="wrap_break">
											<?
											if ($store_wise == 1) {
												echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
											} else {
												echo number_format($transfer_out_qty, 2);
											}
											?>
										</td>
										<td width="90" class="wrap_break" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
										<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>
										<td width="100" align="right" class="wrap_break"><? echo number_format($stockInHand, 4); ?></td>
										<?
										$stock_value = 0;
										if ($show_val_column == 1) {

											$avg_rate = ($stockInHandAmt/$stockInHand); //$mrr_rate_arr[$row[csf("id")]];
											if($avg_rate>0)
											{
												$avg_rate = $avg_rate;
											}else{
												$avg_rate = "0.00"; //$mrr_rate_arr[$row[csf("id")]];
											}
											
											$stock_value = $stockInHand * $avg_rate;
											$stock_value_usd = ($stockInHand * $avg_rate) / $exchange_rate;
											$avz_rates_usd=0;

											if(number_format($stock_value_usd,2)>0 && number_format($stockInHand,2)>0) {
												$avz_rates_usd=$stock_value_usd/$stockInHand;
											}else{
												$avz_rates_usd = "0.00";
											}

											echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
											echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
											echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
											echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
										}

										if ($store_wise == 1) {
											$store_name = '';
											$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
											$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];
											$store_id = array_unique(array_merge($receive_store_id, $issue_store_id));
											foreach ($store_id as $val) {
												if ($store_name == "")
													$store_name = $store_arr[$val];
												else
													$store_name .= ", " . $store_arr[$val];
											}
											echo '<td width="100" class="wrap_break">' . $store_name . '</td>';
										}
										else {
											if ($allocated_qty_variable_settings == 1) {
												echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
												echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
											} else {
												echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
												echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
											}
										}
										?>
										<td width="50" align="right" class="wrap_break"><? echo $ageOfDays; //$ageOfDays;         ?></td>
										<td width="50" align="right" class="wrap_break"><?
										if ($stockInHand > 0)
											echo $daysOnHand;
										else
												echo "&nbsp;"; //$daysOnHand;
										?></td>

										<? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != ""){ ?>
											<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
										<?  }else{ ?>
											<td width="140" align="left"><span ><? echo "&nbsp;"; ?></span></td>
										<? 	} ?>

										<td width="" align="center" class="wrap_break">
											<?
										/*$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
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
										echo $buyer_all;*/
										?>
										<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a></td>
									</tr>
									<?
									$i++;

									$total_opening_balance += $openingBalance;
									$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
									$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
									$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
									$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
									$total_total_rcv += $totalRcv;
									$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
									$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
									$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
									$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
									$total_total_delivery += $totalIssue;
									$total_stock_in_hand += $stockInHand;
									$total_alocatted += $row[csf("allocated_qnty")];
									$total_free_stock += $row[csf("available_qnty")];
									$sub_stock_value += $stock_value;
									$sub_stock_value_usd += $stock_value_usd;
									$total_transfer_out_qty += $transfer_out_qty;
									$total_transfer_in_qty += $transfer_in_qty;

									//grand total===========================
									$grand_total_opening_balance += $openingBalance;
									$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
									$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
									$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
									$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
									$grand_total_total_rcv += $totalRcv;
									$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
									$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
									$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
									$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
									$grand_total_total_delivery += $totalIssue;
									$grand_total_stock_in_hand += $stockInHand;
									$grand_total_alocatted += $row[csf("allocated_qnty")];
									$grand_total_free_stock += $row[csf("available_qnty")];
									$tot_stock_value += $stock_value;
									$tot_stock_value_usd += $stock_value_usd;

									$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
									$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
								}
							}
						}
						?>
						<tr bgcolor="#CCCCCC" style="font-weight:bold">
							<td colspan="10" align="right">Sub Total</td>
							<td width="110" class="wrap_break" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
							<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
							<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
							<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
							<?
							if ($show_val_column == 1) {
								echo '<td width="90" align="right">&nbsp;</td>';
								echo '<td width="110" class="wrap_break" align="right">' . number_format($sub_stock_value, 2) . '</td>';
								echo '<td width="100" align="right">&nbsp;</td>';
								echo '<td width="100" class="wrap_break" align="right">' . number_format($sub_stock_value_usd, 2) . '</td>';
							}

							if ($store_wise == 1) {
								echo '<td width="100">&nbsp;</td>';
							} else {
								if ($allocated_qty_variable_settings == 1) {
									echo '<td width="100" align="right">' . number_format($total_alocatted, 2) . '</td>';
									echo '<td width="100" align="right">' . number_format($total_free_stock, 2) . '</td>';
								} else {
									echo '<td width="100" align="right">&nbsp;</td>';
									echo '<td width="100" align="right">&nbsp;</td>';
								}
							}
							?>
							<td width="50" align="right">&nbsp;</td>
							<td width="50" align="right">&nbsp;</td>
							<td width="" align="right">&nbsp;</td>
							<td width="" align="right">&nbsp;</td>
						</tr>
		 
					<tr class="tbl_bottom">
						<td width="30"></td>
						<td width="130"></td>
						<td width="60"></td>
						<td width="60"></td>
						<td width="150"></td>
						<td width="100"></td>
						<td width="120"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100" align="right">Grand Total</td>
						<td width="110" class="wrap_break" align="right" id="value_total_opening_balance"><? echo number_format($grand_total_opening_balance, 2); ?></td>
						<td width="90" align="right" id="value_total_purchase"><? echo number_format($grand_total_purchase, 2); ?></td>
						<td width="90" align="right" id="value_total_inside_return"><? echo number_format($grand_total_inside_return, 2); ?></td>
						<td width="90" align="right" id="value_total_outside_return"><? echo number_format($grand_total_outside_return, 2); ?></td>
						<td width="90" align="right" id="value_total_transfer_in"><? echo number_format($grand_total_transfer_in_qty, 2); ?></td>
						<td width="90" class="wrap_break" align="right" id="value_total_rcv_loan"><? echo number_format($grand_total_rcv_loan, 2); ?></td>
						<td width="100" class="wrap_break" align="right" id="value_total_total_rcv"><? echo number_format($grand_total_total_rcv, 2); ?></td>
						<td width="90" class="wrap_break" align="right" id="value_total_issue_inside"><? echo number_format($grand_total_issue_inside, 2); ?></td>
						<td width="90" class="wrap_break" align="right" id="value_total_issue_outside"><? echo number_format($grand_total_issue_outside, 2); ?></td>
						<td width="90" class="wrap_break"  align="right" id="value_total_receive_return"><? echo number_format($grand_total_receive_return, 2); ?></td>
						<td width="90" align="right" class="wrap_break" id="value_total_transfer_out"><? echo number_format($grand_total_transfer_out_qty, 2); ?></td>
						<td width="90" align="right" class="wrap_break" id="value_total_issue_loan"><? echo number_format($grand_total_issue_loan, 2); ?></td>
						<td width="100" align="right" class="wrap_break" id="value_total_total_delivery"><? echo number_format($grand_total_total_delivery, 2); ?></td>
						<td width="100" align="right" class="wrap_break" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 4); ?></td>
						<?
						if ($show_val_column == 1)
						{
							echo '<td width="90" align="right">&nbsp;</td>';
							echo '<td width="110" class="wrap_break" align="right">' . number_format($tot_stock_value, 2) . '</td>';
							echo '<td width="100" align="right">&nbsp;</td>';
							echo '<td width="100" class="wrap_break" align="right">' . number_format($tot_stock_value_usd, 2) . '</td>';
						}

						if ($store_wise == 1) {
							echo '<td width="100">&nbsp;</td>';
						} else {
							if ($allocated_qty_variable_settings == 1) {
								echo '<td width="100" align="right" id="value_total_alocatted">' . number_format($grand_total_alocatted, 2) . '</td>';
								echo '<td width="100" align="right" id="value_total_free_stock">' . number_format($grand_total_free_stock, 2) . '</td>';
							} else {
								echo '<td width="100" align="right" id="value_total_alocatted">&nbsp;</td>';
								echo '<td width="100" align="right" id="value_total_free_stock">&nbsp;</td>';
							}
						}
						?>
						<td width="50" align="right">&nbsp;</td>
						<td width="50" align="right">&nbsp;</td>
						<td width="140" align="right">&nbsp;</td>
						<td width="" align="right">&nbsp;</td>
					</tr>
				</table>
			</div>
			<?
		}
		else if ($type == 2)
		{
			if ($value_with == 0)
				$search_cond .= "";
			else
				$search_cond .= "  and a.current_stock>0";
			$count_arr = array();
			$sql = "select a.id, a.yarn_count_id, a.yarn_type from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.yarn_count_id, a.yarn_type order by a.yarn_count_id, a.yarn_type";

			$result = sql_select($sql);
			foreach ($result as $row) {

				$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
				$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
				$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
				$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

				$openingBalance = ( $receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;
				$stockInHand = ($openingBalance + $totalRcv) - $totalIssue;
				$count_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]] += $stockInHand;
			}
			$i = 1;
			ob_start();
			?>
			<div style="margin-top:5px">
				<table width="702" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="4" align="center" style="border:none;font-size:16px; font-weight:bold">Count Wise Yarn Stock Summary Report</td>
						</tr>
						<tr style="border:none;">
							<td colspan="4" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="4" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
							</td>
						</tr>
						<tr>
							<th width="70">SL</th>
							<th width="150">Count</th>
							<th width="200">Type</th>
							<th>Stock In Hand</th>
						</tr>
					</thead>
				</table>
				<div style="width:720px; " id="scroll_body" >
					<table width="702" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$tot_stock_qty = 0;
						foreach ($count_arr as $count => $value) {
							foreach ($value as $type => $stock_qnty) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								if (!in_array($count, $checkArr)) {
									$checkArr[$i] = $count;
									if ($i > 1) {
										?>
										<tr bgcolor="#CCCCCC" style="font-weight:bold">
											<td colspan="3" align="right">Sub Total</td>
											<td align="right"><? echo number_format($count_tot_qnty, 2); ?></td>
										</tr>
										<?
										$count_tot_qnty = 0;
									}
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="70"><? echo $i; ?></td>
									<td width="150"><p><? echo $yarn_count_arr[$count]; ?>&nbsp;</p></td>
									<td width="200"><p><? echo $yarn_type[$type]; ?>&nbsp;</p></td>
									<td align="right"><? echo number_format($stock_qnty, 2); ?></td>
								</tr>
								<?
								$i++;

								$count_tot_qnty += $stock_qnty;
								$tot_stock_qty += $stock_qnty;
							}
						}
						?>
						<tr bgcolor="#CCCCCC" style="font-weight:bold">
							<td colspan="3" align="right">Sub Total</td>
							<td align="right"><? echo number_format($count_tot_qnty, 2); ?></td>
						</tr>
					</table>
				</div>
				<table width="720" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
					<tr class="tbl_bottom">
						<td width="70">&nbsp;</td>
						<td width="150">&nbsp;</td>
						<td width="200">Total</td>
						<td align="right" style="padding-right:18px"><? echo number_format($tot_stock_qty, 2); ?></td>
					</tr>
				</table>
			</div>
			<?
		}
		else if ($type == 3)
		{
			if ($value_with == 0)
				$search_cond .= "";
			else
				$search_cond .= "  and a.current_stock>0";
			$type_arr = array();
			$sql = "select a.id, a.yarn_type, a.yarn_count_id from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.yarn_type, a.yarn_count_id order by a.yarn_type, a.yarn_count_id";

			/*if ($cbo_dyed_type == 0) {
				$sql = "select a.id, a.yarn_type, a.yarn_count_id from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.yarn_type, a.yarn_count_id order by a.yarn_type, a.yarn_count_id";
			} else {
				$sql = "select a.id, a.yarn_type, a.yarn_count_id from product_details_master a, inv_transaction b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type=1 $company_cond $search_cond group by a.id, a.yarn_type, a.yarn_count_id order by a.yarn_type, a.yarn_count_id";
			}*/
			//echo $sql;die;
			$result = sql_select($sql);
			foreach ($result as $row) {

				$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
				$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
				$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
				$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

				$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;
				$stockInHand = ($openingBalance + $totalRcv) - $totalIssue;
				$type_arr[$row[csf("yarn_type")]][$row[csf("yarn_count_id")]] += $stockInHand;
			}
			$i = 1;
			//ob_start();
			?>
			<div style="margin-top:5px">
				<table width="702" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="4" align="center" style="border:none;font-size:16px; font-weight:bold">Count Wise Yarn Stock Summary Report</td>
						</tr>
						<tr style="border:none;">
							<td colspan="4" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="4" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
							</td>
						</tr>
						<tr>
							<th width="70">SL</th>
							<th width="200">Type</th>
							<th width="150">Count</th>
							<th>Stock In Hand</th>
						</tr>
					</thead>
				</table>
				<div style="width:720px;" id="scroll_body" >
					<table width="702" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$tot_stock_qty = 0;
						foreach ($type_arr as $type => $value) {
							foreach ($value as $count => $stock_qnty) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								if (!in_array($type, $checkArr)) {
									$checkArr[$i] = $type;
									if ($i > 1) {
										?>
										<tr bgcolor="#CCCCCC" style="font-weight:bold">
											<td colspan="3" align="right">Sub Total</td>
											<td align="right"><? echo number_format($count_tot_qnty, 2); ?></td>
										</tr>
										<?
										$count_tot_qnty = 0;
									}
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="70"><? echo $i; ?></td>
									<td width="200"><p><? echo $yarn_type[$type]; ?>&nbsp;</p></td>
									<td width="150"><p><? echo $yarn_count_arr[$count]; ?>&nbsp;</p></td>
									<td align="right"><? echo number_format($stock_qnty, 2); ?></td>
								</tr>
								<?
								$i++;

								$count_tot_qnty += $stock_qnty;
								$tot_stock_qty += $stock_qnty;
							}
						}
						?>
						<tr bgcolor="#CCCCCC" style="font-weight:bold">
							<td colspan="3" align="right">Sub Total</td>
							<td align="right"><? echo number_format($count_tot_qnty, 2); ?></td>
						</tr>
					</table>
				</div>
				<table width="720" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
					<tr class="tbl_bottom">
						<td width="70">&nbsp;</td>
						<td width="150">&nbsp;</td>
						<td width="200">Total</td>
						<td align="right" style="padding-right:18px"><? echo number_format($tot_stock_qty, 2); ?></td>
					</tr>
				</table>
			</div>
			<?
		}
		else if ($type == 4)
		{
			if ($value_with == 0)
				$search_cond .= "";
			else
				$search_cond .= "  and a.current_stock>0";
			$pipe_line_purchase_arr = array();
			$pipe_line_ydbooking_arr = array();
			$pipe_line_pi_arr = array();
			$sql_booking = sql_select("select b.yarn_count, b.yarn_type, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.color_name, sum(b.supplier_order_quantity) as qnty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.pay_mode!=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.yarn_count, b.yarn_type, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.color_name"); //and a.wo_number='OG-15-00043'
			foreach ($sql_booking as $row) {
				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$compositionDtls = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$compositionDtls = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}
				$pipe_line_purchase_arr[$row[csf("yarn_count")]][$row[csf("yarn_type")]][$compositionDtls][$row[csf("color_name")]] = $row[csf("qnty")];
			}
			//print_r($pipe_line_purchase_arr);
			$sql_ywdbooking = sql_select("select c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.color, sum(b.yarn_wo_qty) as qnty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and c.item_category_id=1 and a.pay_mode!=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.color");
			foreach ($sql_ywdbooking as $row) {
				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$compositionDtl = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$compositionDtl = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}
				$pipe_line_ydbooking_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$compositionDtl][$row[csf("color")]] = $row[csf("qnty")];
			}

			$sql_pi = sql_select("select b.count_name, b.yarn_type, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.color_id, sum(b.quantity) as qnty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.count_name, b.yarn_type, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.color_id");  //and a.id in (1161,1162)
			foreach ($sql_pi as $row) {
				if ($row[csf("yarn_composition_item2")] != 0) {
					$compositionDtlPi = $row[csf("yarn_composition_item1")] . '**' . $row[csf("yarn_composition_percentage1")] . '**' . $row[csf("yarn_composition_item2")] . '**' . $row[csf("yarn_composition_percentage2")];
				} else {
					$compositionDtlPi = $row[csf("yarn_composition_item1")] . '**' . $row[csf("yarn_composition_percentage1")];
				}
				$pipe_line_pi_arr[$row[csf("count_name")]][$row[csf("yarn_type")]][$compositionDtlPi][$row[csf("color_id")]] = $row[csf("qnty")];
			}
			//print_r ($pipe_line_pi_arr);
			$pipelineArr = array();

			$sql_ppl = "select a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by  a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color order by a.yarn_type, a.yarn_count_id";

			/* if ($cbo_dyed_type == 0) {
				$sql_ppl = "select a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by  a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color order by a.yarn_type, a.yarn_count_id";
			} else {
				$sql_ppl = "select  a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color, c.receive_basis,	c.receive_purpose from product_details_master a, inv_transaction b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type in(1,4) $company_cond $search_cond group by a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color, c.receive_basis, c.receive_purpose order by a.yarn_type, a.yarn_count_id";  //and c.recv_number in ('OG-YRV-15-00040','OG-YRV-15-00041','OG-YRV-15-00042','OG-YRV-15-00043','OG-YRV-15-00044')
			}*/

			$result_ppl = sql_select($sql_ppl);
			foreach ($result_ppl as $row) {
				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}
				if ($row[csf("receive_basis")] == 2) {
					if ($row[csf("receive_purpose")] == 16) {
						$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $pipe_line_purchase_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];
					} else if ($row[csf("receive_purpose")] == 2) {
						$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $pipe_line_ydbooking_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];
					}
				} else {
					$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $pipe_line_pi_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];
				}
				//$pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]]=$pipe_line_qty;
			}
			//print_r($pipelineArr);
			$type_arr = array();

			if ($cbo_dyed_type == 0) {
				$sql = "select a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color order by a.yarn_type, a.yarn_count_id";
			} else {
				$sql = "select a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color from product_details_master a, inv_transaction b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type=1 $company_cond $search_cond group by a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.color order by a.yarn_type, a.yarn_count_id";
			}
			//echo $sql;die;
			$result = sql_select($sql);
			foreach ($result as $row) {
				//$pipe_line_qty=0;
				$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
				$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

				$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
				$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

				$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;
				$stockInHand = ($openingBalance + $totalRcv) - $totalIssue;
				if ($row[csf("yarn_comp_type2nd")] != 0) {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")] . '**' . $row[csf("yarn_comp_type2nd")] . '**' . $row[csf("yarn_comp_percent2nd")];
				} else {
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_percent1st")];
				}

				$pipe_line_qty = $pipelineArr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]];

				$type_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] += $stockInHand;
				$pipe_line_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]][$composition_val][$row[csf("color")]] = $pipe_line_qty;
			}
			//print_r($pipe_line_arr);
			$colorArr = return_library_array("select id, color_name from lib_color", "id", "color_name");
			$i = 1;
			//ob_start();
			?>
			<div style="margin-top:5px">
				<table width="802" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="7" align="center" style="border:none;font-size:16px; font-weight:bold">Composition Wise Yarn Stock Summary Report</td>
						</tr>
						<tr style="border:none;">
							<td colspan="7" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="7" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
							</td>
						</tr>
						<tr>
							<th width="50">SL</th>
							<th width="100">Count</th>
							<th width="200">Composition</th>
							<th width="100">Type</th>
							<th width="100">Color</th>
							<th width="100">Stock In Hand</th>
							<th>Pipe Line</th>
						</tr>
					</thead>
				</table>
				<div style="width:820px;" id="scroll_body" >
					<table width="802" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$tot_stock_qty = 0;
						foreach ($type_arr as $count => $value) {
							foreach ($value as $type => $type_val) {
								foreach ($type_val as $compo => $comp_val) {
									foreach ($comp_val as $color => $stock_qty) {
										$pipeline_qty = $pipe_line_arr[$count][$type][$compo][$color];
										$bal_pipeline_qty = $pipeline_qty - $stock_qty;
										//echo $pipeline_qty.'=='.$stock_qty;
										$ex_comp = explode('**', $compo);
										$comp_1type = $ex_comp[0];
										$comp_1per = $ex_comp[1];
										$comp_2type = $ex_comp[2];
										$comp_2per = $ex_comp[3];
										$fullCompositionValue = "";
										if ($comp_2type != 0) {
											$fullCompositionValue = $composition[$comp_1type] . ',' . $comp_1per . '%  ' . $composition[$comp_2type] . ',' . $comp_2per . '%';
										} else {
											$fullCompositionValue = $composition[$comp_1type] . ',' . $comp_1per . '% ';
										}

										if ($i % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";
										/* if( !in_array($type,$checkArr))
										  {
										  $checkArr[$i]=$type;
										  if($i>1)
										  {
										  ?>
										  <tr bgcolor="#CCCCCC" style="font-weight:bold">
										  <td colspan="3" align="right">Sub Total</td>
										  <td align="right"><? echo number_format($count_tot_qnty,2); ?></td>
										  </tr>
										  <?
										  $count_tot_qnty=0;
										  }
										} */
										//print_r ($ex_comp);
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="50"><? echo $i; ?></td>
											<td width="100"><p><? echo $yarn_count_arr[$count]; ?>&nbsp;</p></td>
											<td width="200"><p><? echo $fullCompositionValue; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $yarn_type[$type]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $colorArr[$color]; ?>&nbsp;</p></td>
											<td width="100" align="right"><? echo number_format($stock_qty, 2); ?></td>
											<td align="right"><? echo number_format($bal_pipeline_qty, 2); ?></td>
										</tr>
										<?
										$i++;

										$count_tot_qnty += $stock_qty;
										$tot_stock_qty += $stock_qty;
										$tot_pipeline_qty += $bal_pipeline_qty;
									}
								}
							}
						}
						?>
			<!--<tr bgcolor="#CCCCCC" style="font-weight:bold">
			<td colspan="3" align="right">Sub Total</td>
			<td align="right"><? // echo number_format($count_tot_qnty,2);          ?></td>
			</tr>-->
			</table>
			</div>
			<table width="820" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
				<tr class="tbl_bottom">
					<td width="50">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="200">Total</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($tot_stock_qty, 2); ?></td>
					<td align="right" style="padding-right:18px"><? echo number_format($tot_pipeline_qty, 2); ?></td>
				</tr>
			</table>
			</div>
		   <?
		}
		else if ($type == 5 || $type == 10)
		{
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
				//echo count($result); die;
				// For Yarn Test //
			//$yarnTestQalityCommentsArr = return_library_array("select prod_id, yarn_quality_coments from inv_yarn_test_mst where  status_active=1 and is_deleted=0", 'prod_id', 'yarn_quality_coments');
			$yarnTestQalityCommentsArr = return_library_array("select a.prod_id as prod_id, b.comments_author as comments_author from inv_yarn_test_mst a, inv_yarn_test_comments b where a.id=b.mst_table_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.comments_author IS NOT NULL", 'prod_id', 'comments_author');
			//print_r($yarnTestQalityCommentsArr);

			$prod_ids_arr = array();
			foreach($result as $row)
			{
				array_push($prod_ids_arr, $row[csf("id")]);				
			}

			$sql_recv = "SELECT b.id, b.prod_id, b.brand_id, c.lot,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd,c.yarn_type,c.color, b.supplier_id, b.buyer_id
			from inv_receive_master a, inv_transaction b, product_details_master c
			where a.id=b.mst_id and b.prod_id=c.id ".where_con_using_array($prod_ids_arr,0,'b.prod_id')." ";
			//echo $sql_recv;

			$rcv_arr = array();
			$sql_recv_result = sql_select($sql_recv);
			foreach($sql_recv_result as $row)
			{
				$rcv_arr[$row[csf("prod_id")]][$row[csf("lot")]][$row[csf("yarn_type")]][$row[csf("color")]]['buyer_id'] = $row[csf("buyer_id")];		
			}


			$i = 1;
				//ob_start();
			if($type==5)
			{
				$tblWidth = "1650";
				$colspan = "9";
			}else{
				$tblWidth = "1540";
				$colspan = "7";
			}
			?>
	 

			<div>
				<table width="<? echo $tblWidth;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="18" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Yarn Stock </td>
						</tr>
						<tr style="border:none;">
							<td colspan="18" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="18" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
							</td>
						</tr>
						<tr  style="word-break:normal;">
							<th rowspan="2" width="40">SL</th>
							<th rowspan="2" width="120">Company Name</th>
							<th colspan="8" width="710">Description</th>
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
							<th width="80">Buyer</th>
						</tr>
					</thead>
		 
						<?
						$grand_total_alocatted=0;$grand_total_free_stock=0;
						$tot_stock_value = 0;
						//echo "<pre>";print_r($receive_array[620188]);
						//echo "<pre>";print_r($issue_array[620188]);
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

							//echo $row[csf("id")]."test". $openingBalance ."+". $totalRcv ."-". $totalIssue; //die();

							$stockInHand = $openingBalance + $totalRcv - $totalIssue;
							$tot_rcv_qnty = $openingBalance + $totalRcv;
							$stockInHandAmt = $openingBalanceAmt + $totalRcvAmt - $totalIssueAmt;
							$tot_rcv_amt = $openingBalanceAmt + $totalRcvAmt;
							$avg_rate=$tot_rcv_amt/$tot_rcv_qnty;
							//$avg_rate = $mrr_rate_arr[$row[csf("id")]];

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
													<td colspan="10" align="right">Sub Total</td>
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
														//commented dated 16.09.2021
														/*if($receive_basis==2) // work order basis
														{													
															if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
															{
																echo $companyArr[$row[csf("supplier_id")]];
															}else{
																echo $supplierArr[$row[csf("supplier_id")]];
															}
														}else{
															echo $supplierArr[$row[csf("supplier_id")]];
														}*/
														echo $supplier_name;
														?></p>
													</td>
													<td width="80">
														<?
														echo $buy_short_name_arr[$rcv_arr[$row[csf("id")]][$row[csf("lot")]][$row[csf("yarn_type")]][$row[csf("color")]]['buyer_id']];
														?>
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
														<td colspan="10" align="right">Sub Total</td>
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
                                        //commented dated 16.09.2021
                                        /*if($receive_basis==2) // work order basis
                                        {													
                                            if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
                                            {
                                                echo $companyArr[$row[csf("supplier_id")]];
                                            }else{
                                                echo $supplierArr[$row[csf("supplier_id")]];
                                            }
                                        }else{
                                            echo $supplierArr[$row[csf("supplier_id")]];
                                        }*/
                                        echo $supplier_name;
                                        ?>
                                        &nbsp;
										</p>
										</td>
										<td width="80">
											<?
												echo $buy_short_name_arr[$rcv_arr[$row[csf("id")]][$row[csf("lot")]][$row[csf("yarn_type")]][$row[csf("color")]]['buyer_id']];
											?>
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
								<td colspan="10" align="right">Sub Total</td>
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
							<td width="80"></td>
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
				</div>
				<?
		}
		else if ($type == 7)
		{
			if ($value_with == 0)
				$search_cond .= "";
			else
				$search_cond .= "  and a.current_stock>0";
			$count_arr = array();
			$sql = "select a.id, a.yarn_count_id, a.yarn_type from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.yarn_count_id, a.yarn_type order by a.yarn_count_id, a.yarn_type";

			/*if ($cbo_dyed_type == 0) {
				$sql = "select a.id, a.yarn_count_id, a.yarn_type from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.yarn_count_id, a.yarn_type order by a.yarn_count_id, a.yarn_type";
			} else {
				$sql = "select a.id, a.yarn_count_id, a.yarn_type from product_details_master a, inv_transaction b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type=1 $company_cond $search_cond group by a.id, a.yarn_count_id, a.yarn_type order by a.yarn_count_id, a.yarn_type";
			}*/

			$result = sql_select($sql);
			foreach ($result as $row) {

				$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
				$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
				$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
				$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

				$openingBalance = ( $receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;
				$stockInHand = ($openingBalance + $totalRcv) - $totalIssue;
			//$count_arr[$row[csf("yarn_count_id")]][$row[csf("yarn_type")]]+=$stockInHand;
				$count_arr[$row[csf("yarn_type")]][$row[csf("yarn_count_id")]] += $stockInHand;
				$header_arr[$row[csf("yarn_count_id")]] = $row[csf("yarn_count_id")];
			}
			//echo count($header_arr);
			$i = 1;
			//ob_start();
			?>
			<div style="margin-top:5px">
	        <div style="max-height:350px" id="scroll_body" >
	            <table border="1" cellpadding="0" cellspacing="1" class="rpt_table" rules="all" id="table_header_1" >
	                <thead>
	                    <tr class="form_caption">
	                        <td align="center" colspan="<? echo round(count($header_arr)); ?>" style="border:none;font-size:16px; font-weight:bold">Count & Categoty Wise Yarn Stock</td>
	                    </tr>
	                    <tr>
	                        <td align="center" colspan="<? echo round(count($header_arr)); ?>" style="border:none;font-size:14px;">
	                            Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
	                        </td>
	                    </tr>
	                    <tr>
	                        <td align="center" colspan="<? echo round(count($header_arr)); ?>" style="border:none;font-size:12px;">
	                            <? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
	                        </td>
	                    </tr>
	                    <tr>
	                        <th width="70">Yarn Type</th>

	                        <? foreach ($header_arr as $count_id => $count_id) { ?><th width="70"><? echo $yarn_count_arr[$count_id]; ?></th> <? } ?>
	                        <th width="70">Count Total</th>
	                    </tr>

	                </thead>
	                <tbody>
	                    <?
	                    $yarn_type_tot = array();
	                    foreach ($count_arr as $yarn_type_id => $value) {
	                        if ($i % 2 == 0)
	                            $bgcolor = "#E9F3FF";
	                        else
	                            $bgcolor = "#FFFFFF";
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                            <td> <? echo $yarn_type[$yarn_type_id] ?></td>
	                            <? foreach ($header_arr as $count_id => $type) { ?>
	                                <td> <?
	                                echo $count_arr[$yarn_type_id][$count_id];
	                                $yarn_type_tot[$yarn_type_id] += $count_arr[$yarn_type_id][$count_id];
	                                $count_tot[$count_id] += $count_arr[$yarn_type_id][$count_id];
	                                ?></td>
	                            <? } ?>
	                        </tr>
	                        <?
	                        $i++;
	                    }
	                    ?>
	                    <tr>
	                        <td>
	                            <b>Grand Total </b>
	                        </td>
	                        <? foreach ($header_arr as $count_id => $type) { ?>
	                            <td>
	                                <?
	                                echo $count_tot[$count_id];
	                                $GrandTotal += $count_tot[$count_id];
	                                ?>
	                            </td>
	                        <? } ?>
	                        <td>
	                            <? echo $GrandTotal; ?>
	                        </td>
	                    </tr>
	                    <tr>
	                        <td><b>Percentage</b></td>
	                        <? foreach ($header_arr as $count_id => $type) { ?>
	                            <td>
	                                <?
	                                echo number_format(($count_tot[$count_id] / $GrandTotal) * 100, 2)."%";
	                                ?>
	                            </td>
	                        <? } ?>

	                    </tr>
	                </tbody>
	            </table>
	        </div>
	        <?
		}
		else if($type == 8)
		{

			$date_array = array();
			$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 group by prod_id";
			$result_returnRes_date = sql_select($returnRes_date);
			foreach ($result_returnRes_date as $row) {
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
			}


			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";

			/*if ($cbo_dyed_type == 0) {
				$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit
				from product_details_master a
				where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
			} else {
				$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit
				from product_details_master a, inv_transaction b, inv_receive_master c
				where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type=1 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_type,a.id";
			}*/
			//echo $sql;
			//echo count($result);
			$result = sql_select($sql);
			$i = 1;
			//ob_start();
			?>
			<div>
				<table width="<? echo $table_width + 400 + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Yarn Stock </td>
						</tr>
						<tr style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
							</td>
						</tr>
						<tr>
							<th rowspan="2" width="30">SL</th>
							<th rowspan="2" width="130">Company</th>
							<th colspan="7">Description</th>
							<th rowspan="2" width="100">Wgt. Bag/Cone</th>
							<th rowspan="2" width="100">Opening Rate</th>
							<th rowspan="2" width="110">OpeningStock</th>
							<th rowspan="2" width="100">Opening Value</th>
							<th colspan="7">Receive</th>
							<th colspan="7">Delivery</th>
							<th rowspan="2" width="100">Stock InHand</th>
							<?
							echo $column;
							if ($store_wise == 1) {
								echo '<th rowspan="2" width="100">Store Name</th>';
							} else {
								echo '<th rowspan="2" width="100">Allocated to Order</th>';
								echo '<th rowspan="2" width="100">Un Allocated Qty.</th>';
							}
							?>
							<th rowspan="2" width="50">Age (Days)</th>
							<th rowspan="2" width="140">DOH</th>
							<th rowspan="2" width="140">Yarn Quality<br>Comments</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<th width="60">Prod.ID</th>
							<th width="60">Count</th>
							<th width="150">Composition</th>
							<th width="100">Yarn Type</th>
							<th width="120">Color</th>
							<th width="100">Lot</th>
							<th width="100">Supplier</th>
							<th width="90">Purchase</th>
							<th width="90">Inside Return</th>
							<th width="90">Outside Return</th>
							<th width="90">Transfer In</th>
							<th width="90">Loan</th>
							<th width="100">Total Recv</th>
							<th width="100">Total Recv Value</th>
							<th width="90">Inside</th>
							<th width="90">Outside</th>
							<th width="90">Recv. Return</th>
							<th width="90">Trans. Out</th>
							<th width="90">Loan</th>
							<th width="100">Total Delivery</th>
							<th width="100">Total Delivery value</th>
						</tr>
					</thead>
				</table>
				<div style="width:<? echo $table_width + 400 + 20; ?>px;" id="scroll_body" >
					<table width="<? echo $table_width + 400; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$tot_stock_value = 0;
						foreach ($result as $row) {
							$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
							$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

							$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
							if ($row[csf("yarn_comp_type2nd")] != 0)
								$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
							$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
							$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

							$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
							$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];
							$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];
							$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];

							$pay_mode = $receive_array[$row[csf("id")]]['pay_mode'];
							$receive_basis = $receive_array[$row[csf("id")]]['receive_basis'];


							$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
							$openingAmount = 0;$openingRate =0;
							if(($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) > 0)
							{
								$openingRate = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) /($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening);
							}
							//$openingAmount= $openingBalance*$openingRate;
							$openingAmount= $openingBalance* $row[csf("avg_rate_per_unit")];

				   /*
					$openingAmount = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);
					$openingRate =  $openingAmount/$openingBalance;
					if( number_format($openingBalance, 2) <= 0){
						$openingAmount = 0;
						$openingRate =0;
					}
					 */

					$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
					$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;

					$totalRcvValue = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] +$receive_array[$row[csf("id")]]['rcv_inside_return_amt']+ $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] +$transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
					$totalIssueValue = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] +$issue_array[$row[csf("id")]]['issue_loan_amt'] + $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];

					$stockInHand = $openingBalance + $totalRcv - $totalIssue;
					//echo $value_with."<br>";
					//subtotal and group-----------------------
					//if(((($value_with ==1) && (number_format($stockInHand,2) > 0.00)) || ($value_with ==0)) && ((number_format($openingBalance,2) > 0.00) || (number_format($totalRcv,2) > 0.00) || (number_format($totalIssue,2) > 0.00)) )
					$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];
					
														
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

					if(($value_with ==1 && (number_format($openingBalance,2) > 0.00 || number_format($stockInHand,2) > 0.00 ) )    ||    ($value_with ==0 && (number_format($openingBalance,2) > 0.00 || number_format($stockInHand,2) > 0.00 || number_format($totalRcv,2) > 0.00 || number_format($totalIssue,2) > 0.00 )))
					{

						if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0)) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							if (!in_array($check_string, $checkArr)) {
								$checkArr[$i] = $check_string;
								if ($i > 1) {
									?>
									<tr bgcolor="#CCCCCC" style="font-weight:bold">
										<td colspan="10" align="right">Sub Total</td>
										<td width="100"></td>
										<td width="110" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
										<td width="100"></td>
										<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
										<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
										<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
										<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
										<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
										<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
										<td width="100" align="right"><? echo number_format($total_totalRcvValue, 2); ?></td>
										<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
										<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
										<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
										<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
										<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
										<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
										<td width="100" align="right"><? echo number_format($total_totalIssueValue, 2); ?></td>
										<td width="100" align="right"><? echo number_format($total_stock_in_hand, 2); ?></td>
										<?
										if ($show_val_column == 1) {
											echo '<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
											echo '<td width="110" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($sub_stock_value, 2) . '</td>';
											echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($sub_stock_value_usd, 2) . '</td>';
										}

										if ($store_wise == 1) {
											echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
										} else {
											if ($allocated_qty_variable_settings == 1) {
												echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_alocatted, 2) . '</td>';
												echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_free_stock, 2) . '</td>';
											} else {
												echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;"align="right">&nbsp;</td>';
												echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">&nbsp;</td>';
											}
										}
										?>
										<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
										<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
										<td style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
										<td width="" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
									</tr>
									<?
									$total_opening_balance = 0;
									$total_purchase = 0;
									$total_inside_return = 0;
									$total_outside_return = 0;
									$total_rcv_loan = 0;
									$total_total_rcv = 0;
									$total_totalRcvValue = 0;
									$total_issue_inside = 0;
									$total_issue_outside = 0;
									$total_receive_return = 0;
									$total_issue_loan = 0;
									$total_total_delivery = 0;
									$total_totalIssueValue = 0;
									$total_stock_in_hand = 0;
									$total_alocatted = 0;
									$total_free_stock = 0;
									$sub_stock_value = 0;
									$sub_stock_value_usd = 0;
									$total_transfer_out_qty = 0;
									$total_transfer_in_qty = 0;
								}
							}


							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="130" style="word-wrap:break-word; word-break: break-all;"><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
								<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $row[csf("id")]; ?></td>
								<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
								<td width="150" style="word-wrap:break-word;word-wrap:break-word:150px;"><? echo $compositionDetails; ?></td>
								<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
								<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
								<td width="100" style="word-wrap:break-word; word-break: break-all;"><p>
									<? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
										<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
									<? }else{
										echo $row[csf("lot")];
									}
									?>
								&nbsp;</p></td>
								<td width="100" style="word-wrap:break-word; word-break: break-all;">
									<?
									//commented dated 16.09.2021
									/*if($receive_basis==2) // work order basis
									{													
										if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
										{
											echo $companyArr[$row[csf("supplier_id")]];
										}else{
											echo $supplierArr[$row[csf("supplier_id")]];
										}
									}else{
										echo $supplierArr[$row[csf("supplier_id")]];
									}*/
									echo $supplier_name;
									?>
								</td>
								<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo 'Bg:' . $receive_array[$row[csf("id")]]['weight_per_bag'] . '; ' . 'Cn:' . $receive_array[$row[csf("id")]]['weight_per_cone']; ?></td>
								<td width="100" style="word-wrap:break-word; word-break: break-all; text-align: right;"><p><? echo number_format($row[csf("avg_rate_per_unit")], 2);?></p></td>
								<td width="110" style="word-wrap:break-word; word-break: break-all;" align="right" title="<? echo "rcv=".$receive_array[$row[csf("id")]]['rcv_total_opening'] .",tr_in=". $trans_in_total_opening .",iss=".$issue_array[$row[csf("id")]]['issue_total_opening'] .",tr_out=". $trans_out_total_opening;?>"><? echo number_format($openingBalance, 2); ?></td>
								<td width="100" style="word-wrap:break-word; word-break: break-all; text-align: right;" title="<? echo "rece_tot=".$receive_array[$row[csf("id")]]['rcv_total_opening_amt'] .",tra_in=". $trans_in_total_opening_amt .",iss_tot=".$issue_array[$row[csf("id")]]['issue_total_opening_amt'].",tr_out=". $trans_out_total_opening_amt;?>"><p><? echo number_format($openingAmount,2);?></p></td>
								<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
									<?
									if ($store_wise == 1) {
										echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
									} else {
										echo number_format($transfer_in_qty, 2);
									}
									?>
								</td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
								<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalRcv, 2); ?></td>
								<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalRcvValue, 2);?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
								<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
									<?
									if ($store_wise == 1) {
										echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
									} else {
										echo number_format($transfer_out_qty, 2);
									}
									?>
								</td>
								<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
								<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalIssue, 2); ?></td>
								<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalIssueValue, 2)?></td>
								<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stockInHand, 2); ?></td>
								<?
								$stock_value = 0;
								if ($show_val_column == 1) {
									$stock_value = $stockInHand * $row[csf("avg_rate_per_unit")];
									$stock_value_usd = ($stockInHand * $row[csf("avg_rate_per_unit")]) / $exchange_rate;
									echo '<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($row[csf("avg_rate_per_unit")], 2) . '</td>';
									echo '<td width="110" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($stock_value, 2) . '</td>';
									echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($stock_value_usd, 2) . '</td>';
								}

								//fhdf
								?>
								<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo $ageOfDays; //$ageOfDays;         ?></td>
								<?

                              if ($store_wise == 1) {
									$store_name = '';
	 								$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
									$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];		
									$store_id = array_unique(array_merge($receive_store_id, $issue_store_id));
									foreach ($store_id as $val) {
										if ($store_name == "")
											$store_name = $store_arr[$val];
										else
											$store_name .= ", " . $store_arr[$val];
									}
									echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">' . $store_name . '</td>';
								}
								else {
									if ($allocated_qty_variable_settings == 1) {
										echo "<td width='100' align='right' style='word-wrap:break-word; word-break: break-all;' ><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
										echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($row[csf("available_qnty")], 2) . '</td>';
									} else {
										echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
										echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
									}
								}
								?>

								<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><?
								if ($stockInHand > 0)
									echo $daysOnHand;
								else
										echo "&nbsp;"; //$daysOnHand;
									?></td>

									<? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != ""){ ?>
										<td width="140" align="left"><span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
									<?  }else{ ?>
										<td width="140" align="left"><span class="comment more"><? echo "&nbsp;"; ?></span></td>
									<? 	} ?>

									<td width="" align="right" style="word-wrap:break-word; word-break: break-all;">
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
										?></td>
										<td width="185"></td>
									</tr>
									<?
									$i++;

									$total_opening_balance += $openingBalance;
									$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
									$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
									$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
									$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
									$total_total_rcv += $totalRcv;
									$total_totalRcvValue+=$totalRcvValue;
									$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
									$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
									$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
									$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
									$total_total_delivery += $totalIssue;
									$total_totalIssueValue+=$totalIssueValue;
									$total_stock_in_hand += $stockInHand;
									$total_alocatted += $row[csf("allocated_qnty")];
									$total_free_stock += $row[csf("available_qnty")];
									$sub_stock_value += $stock_value;
									$sub_stock_value_usd += $stock_value_usd;
									$total_transfer_out_qty += $transfer_out_qty;
									$total_transfer_in_qty += $transfer_in_qty;

							//grand total===========================
									$grand_total_opening_balance += $openingBalance;
									$grand_total_opening_amount_value+=$openingAmount;
									$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
									$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
									$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
									$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
									$grand_total_total_rcv += $totalRcv;
							$grand_total_rcv_amount_value+=$totalRcvValue; //$totalIssueValue
							$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
							$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
							$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
							$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
							$grand_total_total_delivery += $totalIssue;
							$grand_total_issue_amount_value+= $totalIssueValue;
							$grand_total_stock_in_hand += $stockInHand;
							$grand_total_alocatted += $row[csf("allocated_qnty")];
							$grand_total_free_stock += $row[csf("available_qnty")];
							$tot_stock_value += $stock_value;
							$tot_stock_value_usd += $stock_value_usd;

							$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
							$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
						}
					}
				}
				?>
				<tr bgcolor="#CCCCCC" style="font-weight:bold">
					<td colspan="10" align="right">Sub Total</td>
					<td width="100"></td>
					<td width="110" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
					<td width="100"></td>
					<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
					<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
					<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
					<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
					<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
					<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
					<td width="100" align="right"><? echo number_format($total_totalRcvValue, 2); ?></td>
					<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
					<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
					<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
					<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
					<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
					<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
					<td width="100" align="right"><? echo number_format($total_totalIssueValue, 2); ?></td>
					<td width="100" align="right"><? echo number_format($total_stock_in_hand, 2); ?></td>
					<?
					if ($show_val_column == 1) {
						echo '<td width="90" align="right">&nbsp;</td>';
						echo '<td width="110" align="right">' . number_format($sub_stock_value, 2) . '</td>';
						echo '<td width="100" align="right">' . number_format($sub_stock_value_usd, 2) . '</td>';
					}

					if ($store_wise == 1) {
						echo '<td width="100">&nbsp;</td>';
					} else {
						if ($allocated_qty_variable_settings == 1) {
							echo '<td width="100" align="right">' . number_format($total_alocatted, 2) . '</td>';
							echo '<td width="100" align="right">' . number_format($total_free_stock, 2) . '</td>';
						} else {
							echo '<td width="100" align="right">&nbsp;</td>';
							echo '<td width="100" align="right">&nbsp;</td>';
						}
					}
					?>
					<td width="50" align="right">&nbsp;</td>
					<td width="50" align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td width="" align="right">&nbsp;</td>
				</tr>
		 
				<tr class="tbl_bottom">
					<td width="30"></td>
					<td width="130"></td>
					<td width="60"></td>
					<td width="60"></td>
					<td width="150"></td>
					<td width="100"></td>
					<td width="120"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100" align="right">Grand Total</td>
					<td width="100"></td>
					<td width="110" align="right" id="value_total_opening_balance"><? echo number_format($grand_total_opening_balance, 2); ?></td>
					<td width="100" align="right"><? echo number_format($grand_total_opening_amount_value,2);?></td>
					<td width="90" align="right" id="value_total_purchase"><? echo number_format($grand_total_purchase, 2); ?></td>
					<td width="90" align="right" id="value_total_inside_return"><? echo number_format($grand_total_inside_return, 2); ?></td>
					<td width="90" align="right" id="value_total_outside_return"><? echo number_format($grand_total_outside_return, 2); ?></td>
					<td width="90" align="right" id="value_total_transfer_in"><? echo number_format($grand_total_transfer_in_qty, 2); ?></td>
					<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_rcv_loan"><? echo number_format($grand_total_rcv_loan, 2); ?></td>
					<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_total_rcv"><? echo number_format($grand_total_total_rcv, 2); ?></td>
					<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($grand_total_rcv_amount_value,2);?></td>
					<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_issue_inside"><? echo number_format($grand_total_issue_inside, 2); ?></td>
					<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_issue_outside"><? echo number_format($grand_total_issue_outside, 2); ?></td>
					<td width="90"style="word-wrap:break-word; word-break: break-all;"  align="right" id="value_total_receive_return"><? echo number_format($grand_total_receive_return, 2); ?></td>
					<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_transfer_out"><? echo number_format($grand_total_transfer_out_qty, 2); ?></td>
					<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_issue_loan"><? echo number_format($grand_total_issue_loan, 2); ?></td>
					<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_total_delivery"><? echo number_format($grand_total_total_delivery, 2); ?></td>
					<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($grand_total_issue_amount_value,2);?></td>
					<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 2); ?></td>
					<?
					if ($show_val_column == 1) {
						echo '<td width="90" align="right">&nbsp;</td>';
						echo '<td width="110" align="right">' . number_format($tot_stock_value, 2) . '</td>';
						echo '<td width="100" align="right">' . number_format($tot_stock_value_usd, 2) . '</td>';
					}

					if ($store_wise == 1) {
						echo '<td width="100">&nbsp;</td>';
					} else {
						if ($allocated_qty_variable_settings == 1) {
							echo '<td width="100" align="right" id="value_total_alocatted">' . number_format($grand_total_alocatted, 2) . '</td>';
							echo '<td width="100" align="right" id="value_total_free_stock">' . number_format($grand_total_free_stock, 2) . '</td>';
						} else {
							echo '<td width="100" align="right" id="value_total_alocatted">&nbsp;</td>';
							echo '<td width="100" align="right" id="value_total_free_stock">&nbsp;</td>';
						}
					}
					?>
					<td width="50" align="right">&nbsp;</td>
					<td width="50" align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td width="" align="right">&nbsp;</td>
				</tr>
			</table>
			</div>
			<?
		}
		else if ($type == 9)
		{

			if ($show_val_column == 1) 
			{
				$value_width = 300;
				$span = 3;
				$column = '<th rowspan="2" width="90">Avg. Rate (USD)</th>
				<th rowspan="2" width="100">Stock Value (USD)</th>';
			} 
			else 
			{
				$value_width = 0;
				$span = 0;
				$column = '';
			}

			$date_array = array();
			$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 group by prod_id";
			$result_returnRes_date = sql_select($returnRes_date);
			foreach ($result_returnRes_date as $row)
			{
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
			}

			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";

				//echo $sql;
				//die;//echo count($result);
			$result = sql_select($sql);
			$i = 1;
				//ob_start();
			?>
			<div>
				<table width="<? echo $table_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Yarn Stock </td>
						</tr>
						<tr style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
							</td>
						</tr>
						<tr>
							<th rowspan="2" width="30">SL</th>
							<th rowspan="2" width="130">Company</th>
							<th colspan="7">Description</th>
							<th rowspan="2" width="100">Wgt. Bag/Cone</th>
							<th rowspan="2" width="110">OpeningStock</th>
							<th colspan="6">Receive</th>
							<th colspan="6">Delivery</th>
							<th rowspan="2" width="100">Stock InHand</th>
							<?
							echo $column;
							if ($store_wise == 1) {
								echo '<th rowspan="2" width="100">Store Name</th>';
							} else {
								echo '<th rowspan="2" width="100">Allocated to Order</th>';
								echo '<th rowspan="2" width="100">Un Allocated Qty.</th>';
							}
							?>
							<th rowspan="2" width="50">Age (Days)</th>
							<th rowspan="2" width="50">DOH</th>
							<th rowspan="2" width="140">Yarn Quality<br>Comments</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<th width="60">Prod.ID</th>
							<th width="60">Count</th>
							<th width="150">Composition</th>
							<th width="100">Yarn Type</th>
							<th width="120">Color</th>
							<th width="100">Lot</th>
							<th width="100">Supplier</th>
							<th width="90">Purchase</th>
							<th width="90">Inside Return</th>
							<th width="90">Outside Return</th>
							<th width="90">Transfer In</th>
							<th width="90">Loan</th>
							<th width="100">Total Recv</th>
							<th width="90">Inside</th>
							<th width="90">Outside</th>
							<th width="90">Recv. Return</th>
							<th width="90">Trans. Out</th>
							<th width="90">Loan</th>
							<th width="100">Total Delivery</th>
						</tr>
					</thead>
				</table>
				<div style="width:<? echo $table_width + 20; ?>px;" id="scroll_body" >
					<table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$tot_stock_value = 0;
						foreach ($result as $row)
						{
							$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
							$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

							$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
							if ($row[csf("yarn_comp_type2nd")] != 0)
								$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
							$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
							$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

							$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
							$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

							$pay_mode = $receive_array[$row[csf("id")]]['pay_mode'];
							$receive_basis = $receive_array[$row[csf("id")]]['receive_basis'];

							$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

							$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
							$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;

							$stockInHand = $openingBalance + $totalRcv - $totalIssue;

						//subtotal and group-----------------------
							$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

							if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0))
							{
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

								if($value_with == 1)
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
													<td colspan="10" align="right">Sub Total</td>
													<td width="110" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
													<?
													if ($show_val_column == 1)
													{
														echo '<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
														echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($sub_stock_value_usd, 2) . '</td>';
													}

													if ($store_wise == 1)
													{
														echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
													}
													else
													{
														if ($allocated_qty_variable_settings == 1)
														{
															echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_alocatted, 2) . '</td>';
															echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_free_stock, 2) . '</td>';
														}
														else
														{
															echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;"align="right">&nbsp;</td>';
															echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">&nbsp;</td>';
														}
													}
													?>
													<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
													<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
													<td align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
													<td width="" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
												</tr>
												<?
												$total_opening_balance = 0;
												$total_purchase = 0;
												$total_inside_return = 0;
												$total_outside_return = 0;
												$total_rcv_loan = 0;
												$total_total_rcv = 0;
												$total_issue_inside = 0;
												$total_issue_outside = 0;
												$total_receive_return = 0;
												$total_issue_loan = 0;
												$total_total_delivery = 0;
												$total_stock_in_hand = 0;
												$total_alocatted = 0;
												$total_free_stock = 0;
												$sub_stock_value = 0;
												$sub_stock_value_usd = 0;
												$total_transfer_out_qty = 0;
												$total_transfer_in_qty = 0;
											}
										}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30"><? echo $i; ?></td>
											<td width="130" style="word-wrap:break-word; word-break: break-all;"><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
											<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $row[csf("id")]; ?></td>
											<td width="60" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
											<td width="150" style="word-wrap:break-word;word-wrap:break-word:150px;"><? echo $compositionDetails; ?></td>
											<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
											<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
											<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@';"><p>
												<? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
													<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
												<? }else{
													echo $row[csf("lot")];
												}
												?>
											&nbsp;</p></td>
											<td width="100" style="word-wrap:break-word; word-break: break-all;">
											<?
											//commented dated 16.09.2021
											/*if($receive_basis==2) // work order basis
											{													
												if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
												{
													echo $companyArr[$row[csf("supplier_id")]];
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}
											}else{
												echo $supplierArr[$row[csf("supplier_id")]];
											}*/
											echo $supplier_name;
											?>
											</td>
											<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo 'Bg:' . $receive_array[$row[csf("id")]]['weight_per_bag'] . '; ' . 'Cn:' . $receive_array[$row[csf("id")]]['weight_per_cone']; ?></td>
											<td width="110" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($openingBalance, 2); ?></td>
											<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
												<?
												if ($store_wise == 1)
												{
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
												}
												else
												{
													echo number_format($transfer_in_qty, 2);
												}
												?>
											</td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
											<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalRcv, 2); ?></td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
											<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
												<?
												if ($store_wise == 1)
												{
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
												}
												else
												{
													echo number_format($transfer_out_qty, 2);
												}
												?>
											</td>
											<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
											<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalIssue, 2); ?></td>
											<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stockInHand, 4); ?></td>
											<?
											$stock_value = 0;
											if ($show_val_column == 1)
											{
												$avg_rate_per_unit_usd=$row[csf("avg_rate_per_unit")]/$exchange_rate;
												$stock_value = $stockInHand * $row[csf("avg_rate_per_unit")];
												$stock_value_usd = ($stockInHand * $row[csf("avg_rate_per_unit")]) / $exchange_rate;
												echo '<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($avg_rate_per_unit_usd, 2) . '</td>';

												echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($stock_value_usd, 2) . '</td>';
											}

											if ($store_wise == 1)
											{
												$store_name = '';
												$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
												$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];
												$store_id = array_unique(array_merge($receive_store_id, $issue_store_id));
												foreach ($store_id as $val) {
													if ($store_name == "")
														$store_name = $store_arr[$val];
													else
														$store_name .= ", " . $store_arr[$val];
												}
												echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">' . $store_name . '</td>';
											}
											else
											{
												if ($allocated_qty_variable_settings == 1)
												{
													echo "<td width='100' align='right' style='word-wrap:break-word; word-break: break-all;' ><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($row[csf("available_qnty")], 2) . '</td>';
												}
												else
												{
													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
												}
											}
											?>
											<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo $ageOfDays; //$ageOfDays;         ?></td>
											<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><?
											if ($stockInHand > 0)
												echo $daysOnHand;
											else
								  			echo "&nbsp;"; //$daysOnHand;
								?>
							</td>
							<? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != ""){ ?>
								<td width="140" align="left"><span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
							<?  }else{ ?>
								<td width="140" align="left"><span class="comment more"><? echo "&nbsp;"; ?></span></td>
							<? 	} ?>
							<td width="" align="center" style="word-wrap:break-word; word-break: break-all;">
								<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
							</td>
						</tr>
						<?
						$i++;

						$total_opening_balance += $openingBalance;
						$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
						$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
						$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
						$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
						$total_total_rcv += $totalRcv;
						$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
						$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
						$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
						$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
						$total_total_delivery += $totalIssue;
						$total_stock_in_hand += $stockInHand;
						$total_alocatted += $row[csf("allocated_qnty")];
						$total_free_stock += $row[csf("available_qnty")];
						$sub_stock_value += $stock_value;
						$sub_stock_value_usd += $stock_value_usd;
						$total_transfer_out_qty += $transfer_out_qty;
						$total_transfer_in_qty += $transfer_in_qty;

								  //grand total===========================
						$grand_total_opening_balance += $openingBalance;
						$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
						$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
						$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
						$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
						$grand_total_total_rcv += $totalRcv;
						$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
						$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
						$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
						$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
						$grand_total_total_delivery += $totalIssue;
						$grand_total_stock_in_hand += $stockInHand;
						$grand_total_alocatted += $row[csf("allocated_qnty")];
						$grand_total_free_stock += $row[csf("available_qnty")];
						$tot_stock_value += $stock_value;
						$tot_stock_value_usd += $stock_value_usd;

						$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
						$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
					}

				}
								else
								{
				
									if (!in_array($check_string, $checkArr))
									{
										$checkArr[$i] = $check_string;
										if ($i > 1)
										{
											?>
											<tr bgcolor="#CCCCCC" style="font-weight:bold">
												<td colspan="10" align="right">Sub Total</td>
												<td width="110" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
												<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
												<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
												<td width="100" align="right"><? echo number_format($total_stock_in_hand, 2); ?></td>
												<?
												if ($show_val_column == 1)
												{
													echo '<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
													echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($sub_stock_value_usd, 2) . '</td>';
												}
				
												if ($store_wise == 1)
												{
													echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
												}
												else
												{
													if ($allocated_qty_variable_settings == 1)
													{
														echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_alocatted, 2) . '</td>';
														echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">' . number_format($total_free_stock, 2) . '</td>';
													}
													else
													{
														echo '<td width="100"  style="word-wrap:break-word; word-break: break-all;"align="right">&nbsp;</td>';
														echo '<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right">&nbsp;</td>';
													}
												}
												?>
												<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
												<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
												<td align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
												<td width="" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>
											</tr>
											<?
											$total_opening_balance = 0;
											$total_purchase = 0;
											$total_inside_return = 0;
											$total_outside_return = 0;
											$total_rcv_loan = 0;
											$total_total_rcv = 0;
											$total_issue_inside = 0;
											$total_issue_outside = 0;
											$total_receive_return = 0;
											$total_issue_loan = 0;
											$total_total_delivery = 0;
											$total_stock_in_hand = 0;
											$total_alocatted = 0;
											$total_free_stock = 0;
											$sub_stock_value = 0;
											$sub_stock_value_usd = 0;
											$total_transfer_out_qty = 0;
											$total_transfer_in_qty = 0;
										}
									}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30"><? echo $i; ?></td>
										<td width="130" style="word-wrap:break-word; word-break: break-all;"><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
										<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $row[csf("id")]; ?></td>
										<td width="60" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
										<td width="150" style="word-wrap:break-word;word-wrap:break-word:150px;"><? echo $compositionDetails; ?></td>
										<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
										<td width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
										<td width="100" style="word-wrap:break-word; word-break: break-all; mso-number-format:'\@';"><p>
											<? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
												<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
											<? }else{
												echo $row[csf("lot")];
											}
											?>
										&nbsp;</p></td>
										<td width="100" style="word-wrap:break-word; word-break: break-all;">
											<?
											//commented dated 16.09.2021
											/*if($receive_basis==2) // work order basis
											{													
												if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
												{
													echo $companyArr[$row[csf("supplier_id")]];
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}
											}else{
												echo $supplierArr[$row[csf("supplier_id")]];
											}*/
											echo $supplier_name;
											?>
										</td>
										<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo 'Bg:' . $receive_array[$row[csf("id")]]['weight_per_bag'] . '; ' . 'Cn:' . $receive_array[$row[csf("id")]]['weight_per_cone']; ?></td>
										<td width="110" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($openingBalance, 2); ?></td>
										<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
										<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
										<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
										<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
											<?
											if ($store_wise == 1)
											{
												echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
											}
											else
											{
												echo number_format($transfer_in_qty, 2);
											}
											?>
										</td>
										<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
										<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalRcv, 2); ?></td>
										<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
										<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
										<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
										<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">
											<?
											if ($store_wise == 1)
											{
												echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
											}
											else
											{
												echo number_format($transfer_out_qty, 2);
											}
											?>
										</td>
										<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
										<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($totalIssue, 2); ?></td>
										<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stockInHand, 4); ?></td>
										<?
										$stock_value = 0;
										if ($show_val_column == 1)
										{
											$avg_rate_per_unit_usd=$row[csf("avg_rate_per_unit")]/$exchange_rate;
											$stock_value = $stockInHand * $row[csf("avg_rate_per_unit")];
											$stock_value_usd = ($stockInHand * $row[csf("avg_rate_per_unit")]) / $exchange_rate;
				
											echo '<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($avg_rate_per_unit_usd, 2) . '</td>';
				
											echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($stock_value_usd, 2) . '</td>';
										}
				
										if ($store_wise == 1)
										{
											$store_name = '';
											$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
											$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];				
											$store_id = array_unique(array_merge($receive_store_id, $issue_store_id));
											foreach ($store_id as $val)
											{
												if ($store_name == "")
													$store_name = $store_arr[$val];
												else
													$store_name .= ", " . $store_arr[$val];
											}
											echo '<td width="100" style="word-wrap:break-word; word-break: break-all;">' . $store_name . '</td>';
										}
										else
										{
											if ($allocated_qty_variable_settings == 1)
											{
												echo "<td width='100' align='right' style='word-wrap:break-word; word-break: break-all;' ><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
												echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">' . number_format($row[csf("available_qnty")], 2) . '</td>';
											}
											else
											{
												echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
												echo '<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;">&nbsp;</td>';
											}
										}
										?>
										<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo $ageOfDays; //$ageOfDays;         ?></td>
										<td width="50" align="right" style="word-wrap:break-word; word-break: break-all;"><?
										if ($stockInHand > 0)
											echo $daysOnHand;
										else
																echo "&nbsp;"; //$daysOnHand;
															?></td>
				
															<? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != ""){ ?>
																<td width="140" align="left"><span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
															<?  }else{ ?>
																<td width="140" align="left"><span class="comment more"></span></td>
															<? 	} ?>
				
															<td width="" align="center" style="word-wrap:break-word; word-break: break-all;">
																<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
															</td>
														</tr>
														<?
														$i++;
				
														$total_opening_balance += $openingBalance;
														$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
														$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
														$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
														$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
														$total_total_rcv += $totalRcv;
														$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
														$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
														$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
														$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
														$total_total_delivery += $totalIssue;
														$total_stock_in_hand += $stockInHand;
														$total_alocatted += $row[csf("allocated_qnty")];
														$total_free_stock += $row[csf("available_qnty")];
														$sub_stock_value += $stock_value;
														$sub_stock_value_usd += $stock_value_usd;
														$total_transfer_out_qty += $transfer_out_qty;
														$total_transfer_in_qty += $transfer_in_qty;
				
																//grand total===========================
														$grand_total_opening_balance += $openingBalance;
														$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
														$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
														$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
														$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
														$grand_total_total_rcv += $totalRcv;
														$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
														$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
														$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
														$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
														$grand_total_total_delivery += $totalIssue;
														$grand_total_stock_in_hand += $stockInHand;
														$grand_total_alocatted += $row[csf("allocated_qnty")];
														$grand_total_free_stock += $row[csf("available_qnty")];
														$tot_stock_value += $stock_value;
														$tot_stock_value_usd += $stock_value_usd;
				
														$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
														$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
													}
								}
							}
							?>
							<tr bgcolor="#CCCCCC" style="font-weight:bold">
								<td colspan="10" align="right">Sub Total</td>
								<td width="110" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
								<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
								<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
								<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
								<td width="100" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
								<?
								if ($show_val_column == 1)
								{
									echo '<td width="90" align="right">&nbsp;</td>';
									echo '<td width="100" align="right">' . number_format($sub_stock_value_usd, 2) . '</td>';
								}

								if ($store_wise == 1)
								{
									echo '<td width="100">&nbsp;</td>';
								}
								else
								{
									if ($allocated_qty_variable_settings == 1)
									{
										echo '<td width="100" align="right">' . number_format($total_alocatted, 2) . '</td>';
										echo '<td width="100" align="right">' . number_format($total_free_stock, 2) . '</td>';
									}
									else
									{
										echo '<td width="100" align="right">&nbsp;</td>';
										echo '<td width="100" align="right">&nbsp;</td>';
									}
								}
								?>
								<td width="50" align="right">&nbsp;</td>
								<td width="50" align="right">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td width="" align="right">&nbsp;</td>
							</tr>
			 
						<tr class="tbl_bottom">
							<td width="30"></td>
							<td width="130"></td>
							<td width="60"></td>
							<td width="60"></td>
							<td width="150"></td>
							<td width="100"></td>
							<td width="120"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100" align="right">Grand Total</td>
							<td width="110" align="right" id="value_total_opening_balance"><? echo number_format($grand_total_opening_balance, 2); ?></td>
							<td width="90" align="right" id="value_total_purchase"><? echo number_format($grand_total_purchase, 2); ?></td>
							<td width="90" align="right" id="value_total_inside_return"><? echo number_format($grand_total_inside_return, 2); ?></td>
							<td width="90" align="right" id="value_total_outside_return"><? echo number_format($grand_total_outside_return, 2); ?></td>
							<td width="90" align="right" id="value_total_transfer_in"><? echo number_format($grand_total_transfer_in_qty, 2); ?></td>
							<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_rcv_loan"><? echo number_format($grand_total_rcv_loan, 2); ?></td>
							<td width="100" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_total_rcv"><? echo number_format($grand_total_total_rcv, 2); ?></td>
							<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_issue_inside"><? echo number_format($grand_total_issue_inside, 2); ?></td>
							<td width="90" style="word-wrap:break-word; word-break: break-all;" align="right" id="value_total_issue_outside"><? echo number_format($grand_total_issue_outside, 2); ?></td>
							<td width="90"style="word-wrap:break-word; word-break: break-all;"  align="right" id="value_total_receive_return"><? echo number_format($grand_total_receive_return, 2); ?></td>
							<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_transfer_out"><? echo number_format($grand_total_transfer_out_qty, 2); ?></td>
							<td width="90" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_issue_loan"><? echo number_format($grand_total_issue_loan, 2); ?></td>
							<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_total_delivery"><? echo number_format($grand_total_total_delivery, 2); ?></td>
							<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 4); ?></td>
							<?
							if ($show_val_column == 1)
							{
								echo '<td width="90" align="right">&nbsp;</td>';
								echo '<td width="100" align="right">' . number_format($tot_stock_value_usd, 2) . '</td>';
							}

							if ($store_wise == 1)
							{
								echo '<td width="100">&nbsp;</td>';
							}
							else
							{
								if ($allocated_qty_variable_settings == 1)
								{
									echo '<td width="100" align="right" id="value_total_alocatted">' . number_format($grand_total_alocatted, 2) . '</td>';
									echo '<td width="100" align="right" id="value_total_free_stock">' . number_format($grand_total_free_stock, 2) . '</td>';
								}
								else
								{
									echo '<td width="100" align="right" id="value_total_alocatted">&nbsp;</td>';
									echo '<td width="100" align="right" id="value_total_free_stock">&nbsp;</td>';
								}
							}
							?>
							<td width="50" align="right">&nbsp;</td>
							<td width="50" align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td width="" align="right">&nbsp;</td>
						</tr>
					</table>
				</div>
				<?
		}
		else if ($type == 11) // count & composition Buttom
		{
			/*---------- count & composition grouping summary ----
			Made by : Md Didarul Alam
			Date: 06/02/2021
			Requsition By: Narban Group
			/*--------------------------------------------------*/
			
			$count_lib_array = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 $yarn_count_ids_cond and is_deleted=0", 'id', 'yarn_count');

			if ($value_with == 0)
				$search_cond .= "";
			else
				$search_cond .= "  and a.current_stock>0";
		
			//$sql = "select a.id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_type2nd from product_details_master a, inv_transaction b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type=1 $company_cond $search_cond group by a.id, a.yarn_type,a.yarn_count_id,a.yarn_comp_type1st,a.yarn_comp_type2nd order by a.yarn_type, a.yarn_count_id";
			
			$sql = "select a.id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_type2nd, a.allocated_qnty, a.available_qnty from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.yarn_type, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_type2nd, a.allocated_qnty, a.available_qnty order by a.yarn_type, a.yarn_count_id";
			$type_arr = array();
			$result = sql_select($sql);
			foreach ($result as $row) 
			{
				//$pipe_line_qty=0;
				$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
				$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

				$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
				$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

				/*$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;*/
				
				$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $receive_array[$row[csf("id")]]['rcv_adjustment_qty'] + $transfer_in_qty;
				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $issue_array[$row[csf("id")]]['issue_adjustment_qty'] + $transfer_out_qty;
				$stockInHand = ($openingBalance + $totalRcv) - $totalIssue;

				if ($row[csf("yarn_comp_type2nd")] != 0) 
				{
					$composition_val = $row[csf("yarn_comp_type1st")] . '**' . $row[csf("yarn_comp_type2nd")];
				} 
				else 
				{
					$composition_val = $row[csf("yarn_comp_type1st")];
				}

				//$counts_arr[$row[csf("yarn_count_id")]] += $stockInHand;	
				//$count_composition_arr[$row[csf("yarn_count_id")]][$composition_val] += $stockInHand;			
				
				$counts_arr[$count_lib_array[$row[csf("yarn_count_id")]]]['stock_in_hand'] += $stockInHand;
				$counts_arr[$count_lib_array[$row[csf("yarn_count_id")]]]['allocated_qnty'] += $row[csf("allocated_qnty")];
				$counts_arr[$count_lib_array[$row[csf("yarn_count_id")]]]['available_qnty'] += $row[csf("available_qnty")];
				
				$count_composition_arr[$count_lib_array[$row[csf("yarn_count_id")]]][$composition_val]['stock_in_hand'] += $stockInHand;			
				$count_composition_arr[$count_lib_array[$row[csf("yarn_count_id")]]][$composition_val]['allocated_qnty'] += $row[csf("allocated_qnty")];
				$count_composition_arr[$count_lib_array[$row[csf("yarn_count_id")]]][$composition_val]['available_qnty'] += $row[csf("available_qnty")];
			}
			ksort($count_composition_arr);
			?>
			<div style="width:1000px; margin: 0 auto;">
				<table width="802" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="3" align="center" style="border:none;font-size:16px; font-weight:bold">Count & Composition Wise Yarn Stock Summary Report</td>
						</tr>
						<tr style="border:none;">
							<td colspan="3" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="3" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
							</td>
						</tr>
						<tr>
							<th width="40%">Count/Composition</th>						
							<th width="20%">Stock In Hand</th>
							<th width="20%">Allocated to Order</th>
							<th width="20%">Un Allocated Qty.</th>
						</tr>
					</thead>
				</table>
				<div style="width:820px; " id="scroll_body" >
					<table width="802" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$tot_stock_qty = 0;
						$i = 1;
						foreach ($count_composition_arr as $count => $composition_arr) 
						{
							if( !in_array($count,$counts_arr) )
							{
							    ?>
							    <tr bgcolor="#CCCCCC">
							        <td width="40%" style="padding-left:5px;">
							            <b>
							            	<?php 
							            	//$captionData = $count_lib_array[$count];
							            	$captionData = $count;
							                echo $captionData;
							                ?> 
							            </b> 
							        </td>
							        <td width="20%" align="right" style="padding-right:5px;">
							            <b>
							            	<?php 
							                echo number_format($counts_arr[$count]['stock_in_hand'],2);
							                ?> 
							            </b> 
							        </td>
                                    <td width="20%" align="right" style="padding-right:5px;"><b><? echo number_format($counts_arr[$count]['allocated_qnty'],2); ?></b></td>
                                    <td width="20%" align="right" style="padding-right:5px;"><b><? echo number_format($counts_arr[$count]['available_qnty'],2); ?></b></td>
							    </tr>
							    <?                              
							}
							
							foreach ($composition_arr as $compo => $stock_qty) 
							{										
								$ex_comp = explode('**', $compo);
								$comp_1type = $ex_comp[0];
								$comp_2type = $ex_comp[1];
								$fullCompositionValue = "";
								if ($comp_2type != 0) {
									$fullCompositionValue = $composition[$comp_1type] . ',' . $composition[$comp_2type];
								} else {
									$fullCompositionValue = $composition[$comp_1type];
								}

								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40%" style="padding-left:5px;"><p><? echo $fullCompositionValue; ?>&nbsp;</p></td>
									<td width="20%" align="right" style="padding-right:5px;"><? echo number_format($stock_qty['stock_in_hand'], 2); ?></td>
                                    <td width="20%" align="right" style="padding-right:5px;"><? echo number_format($stock_qty['allocated_qnty'], 2); ?></td>
                                    <td width="20%" align="right" style="padding-right:5px;"><? echo number_format($stock_qty['available_qnty'], 2); ?></td>
								</tr>
								<?
								$i++;
								$tot_stock_qty += $stock_qty['stock_in_hand'];
								$tot_allocated_qty += $stock_qty['allocated_qnty'];
								$tot_available_qty += $stock_qty['available_qnty'];
							}
						}
						?>
					</table>
				</div>
				<table width="802" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
					<tr class="tbl_bottom">
						<td width="40%" style="padding-right:5px;">Total</td>
						<td width="20%" align="right" style="padding-right:5px;"><? echo number_format($tot_stock_qty, 2); ?></td>
                        <td width="20%" align="right" style="padding-right:5px;"><? echo number_format($tot_allocated_qty, 2); ?></td>
                        <td width="20%" align="right" style="padding-right:5px;"><? echo number_format($tot_available_qty, 2); ?></td>
					</tr>
				</table>
			</div>
		   <?
		   //$countWiseStock
		}
		else if ($type == 12)
		{
			?>
 
			<?
			$date_array = array();
			$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 group by prod_id";
			$result_returnRes_date = sql_select($returnRes_date);
			foreach ($result_returnRes_date as $row)
			{
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
			}

			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
			$result = sql_select($sql);
			$i = 1;
			//ob_start();

			$mrr_rate_sql = sql_select("select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction
				where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) group by prod_id");
			$mrr_rate_arr = array();
			foreach ($mrr_rate_sql as $row) {
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
				$mrr_rate_arr[$row[csf("prod_id")]] = $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
			}

			//echo "<pre>";
			//print_r($mrr_rate_arr);
			?>
			<div>
				<table width="<? echo $table_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<tr class="form_caption" style="border:none;">
						<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Yarn Stock </td>
					</tr>
					<tr style="border:none;">
						<td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:14px;">
							Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
						</td>
					</tr>
				</table>
				<table width="<? echo $table_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr>
							<th rowspan="2" width="30">SL</th>
							<th rowspan="2" width="130">Company</th>
							<th colspan="7">Description</th>
							<th rowspan="2" width="100">Wgt. Bag/Cone</th>
							<th rowspan="2" width="110">OpeningStock</th>
							<th colspan="6">Receive</th>
							<th colspan="6">Delivery</th>
							<th rowspan="2" width="100">Stock InHand</th>
							<?
							echo $column;
							if ($store_wise == 1) {
								echo '<th rowspan="2" width="100">Store Name</th>';
							} else {
								echo '<th rowspan="2" width="100">Allocated to Order</th>';
								echo '<th rowspan="2" width="100">Un Allocated Qty.</th>';
							}
							?>
							<th rowspan="2" width="50">Age (Days)</th>
							<th rowspan="2" width="50">DOH</th>
							<th rowspan="2" width="140">Yarn Quality<br>Comments</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<th width="60">Prod.ID</th>
							<th width="60">Count</th>
							<th width="150">Composition</th>
							<th width="100">Yarn Type</th>
							<th width="120">Color</th>
							<th width="100">Lot</th>
							<th width="100">Supplier</th>
							<th width="90">Purchase</th>
							<th width="90">Inside Return</th>
							<th width="90">Outside Return</th>
							<th width="90">Transfer In</th>
							<th width="90">Loan</th>
							<th width="100">Total Recv</th>
							<th width="90">Inside</th>
							<th width="90">Outside</th>
							<th width="90">Recv. Return</th>
							<th width="90">Trans. Out</th>
							<th width="90">Loan</th>
							<th width="100">Total Delivery</th>
						</tr>
					</thead>
				</table>
				<div style="width:<? echo $table_width + 38; ?>px;" id="scroll_body" >
					<table style="width:<? echo $table_width +20; ?>px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$tot_stock_value = 0;
						foreach ($result as $row)
						{
							if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "" || $yarnTestArr[$row[csf("id")]] != "" ) 
							{
								$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
								$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
								if ($row[csf("yarn_comp_type2nd")] != 0)
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
								$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
								$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

								$transfer_in_amt = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
								$transfer_out_amt = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];


								$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
								$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

								$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];
								$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];

								$pay_mode = $receive_array[$row[csf("id")]]['pay_mode'];
								$receive_basis = $receive_array[$row[csf("id")]]['receive_basis'];

								$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

								$openingBalanceAmt = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);

								$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $receive_array[$row[csf("id")]]['rcv_adjustment_qty'] + $transfer_in_qty;
								
								$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $issue_array[$row[csf("id")]]['issue_adjustment_qty'] + $transfer_out_qty;

								$totalRcvAmt = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $receive_array[$row[csf("id")]]['rcv_adjustment_amt'] + $transfer_in_amt;

								$totalIssueAmt = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] +$issue_array[$row[csf("id")]]['issue_adjustment_amt'] + $transfer_out_qty;


								$stockInHand = $openingBalance + $totalRcv - $totalIssue;

								$stockInHandAmt = $openingBalanceAmt + $totalRcvAmt - $totalIssueAmt;

								//subtotal and group-----------------------
								$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

								if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0))
								{
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

									if($value_with == 1)
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
														<td colspan="10" align="right">Sub Total</td>
														<td width="110" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
														<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
														<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
														<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
														<td width="100" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
														<?
														if ($show_val_column == 1) {
															echo '<td width="90" align="right">&nbsp;</td>';
															echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
															echo '<td width="100" align="right">&nbsp;</td>';
															echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
														}

														if ($store_wise == 1) {
															echo '<td width="100">&nbsp;</td>';
														} else {
															if ($allocated_qty_variable_settings == 1) {
																echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
																echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
															} else {
																echo '<td width="100" align="right">&nbsp;</td>';
																echo '<td width="100" align="right">&nbsp;</td>';
															}
														}
														?>
														<td width="50" align="right">&nbsp;</td>
														<td width="50" align="right">&nbsp;</td>
														<td align="right">&nbsp;</td>
														<td width="" align="right">&nbsp;</td>
													</tr>
													<?
													$total_opening_balance = 0;
													$total_purchase = 0;
													$total_inside_return = 0;
													$total_outside_return = 0;
													$total_rcv_loan = 0;
													$total_total_rcv = 0;
													$total_issue_inside = 0;
													$total_issue_outside = 0;
													$total_receive_return = 0;
													$total_issue_loan = 0;
													$total_total_delivery = 0;
													$total_stock_in_hand = 0;
													$total_alocatted = 0;
													$total_free_stock = 0;
													$sub_stock_value = 0;
													$sub_stock_value_usd = 0;
													$total_transfer_out_qty = 0;
													$total_transfer_in_qty = 0;
												}
											}
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="30"><? echo $i; ?></td>
												<td width="130" class="wrap_break" ><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
												<td width="60"><? echo $row[csf("id")]; ?></td>
												<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
												<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
												<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
												<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
												<td width="100"><p>
													<?
														if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
														<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
													<? }else if( $yarnTestArr[$row[csf("id")]] != "") { ?>
														<a href='##' onclick="show_test_report2('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
													<? } ?></p> </td>
												<td width="100" class="wrap_break" >
													<?
													//commented dated 16.09.2021
													/*if($receive_basis==2) // work order basis
													{													
														if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
														{
															echo $companyArr[$row[csf("supplier_id")]];
														}else{
															echo $supplierArr[$row[csf("supplier_id")]];
														}
													}else{
														echo $supplierArr[$row[csf("supplier_id")]];
													}*/
													echo $supplier_name;
													?>
												</td>
												<td width="100" class="wrap_break" ><? echo 'Bg:' . $receive_array[$row[csf("id")]]['weight_per_bag'] . '; ' . 'Cn:' . $receive_array[$row[csf("id")]]['weight_per_cone']; ?></td>
												<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
												<td width="90" class="wrap_break" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_in_qty, 2);
													}
													?>
												</td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
												<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
												<td width="90" align="right" class="wrap_break">
													<?
													if ($store_wise == 1) {
														echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
													} else {
														echo number_format($transfer_out_qty, 2);
													}
													?>
												</td>
												<td width="90" class="wrap_break" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>
												<td width="100" align="right" class="wrap_break"><? echo number_format($stockInHand, 4); ?></td>
												<?

												$stock_value = 0;
												if ($show_val_column == 1)
												{
													$avg_rate = ($stockInHandAmt/$stockInHand); //$mrr_rate_arr[$row[csf("id")]];
													if($avg_rate>0)
													{
														$avg_rate=$avg_rate;	
													}else{
														$avg_rate = "0.00"; //$mrr_rate_arr[$row[csf("id")]];
													}
													
													$stock_value = $stockInHand * $avg_rate;
													$stock_value_usd = ($stockInHand * $avg_rate) / $exchange_rate;
													$avz_rates_usd=0;

													if(number_format($stock_value_usd,2)>0 && number_format($stockInHand,2)>0){
														$avz_rates_usd=$stock_value_usd/$stockInHand;
													}else{
														$avz_rates_usd="0.00";
													}

													echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
													echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
												}

												if ($store_wise == 1)
												{
													$store_name = '';
													//$receive_store_id = explode(",", $receive_array[$row[csf("id")]]['store_id']);
													//$issue_store_id = explode(",", $issue_array[$row[csf("id")]]['store_id']);
													$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
													$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];
													
													$store_id = array_unique(array_merge($receive_store_id, $issue_store_id));
													foreach ($store_id as $val) {
														if ($store_name == "")
															$store_name = $store_arr[$val];
														else
															$store_name .= ", " . $store_arr[$val];
													}
													echo '<td width="100" class="wrap_break">' . $store_name . '</td>';
												}
												else
												{
													if ($allocated_qty_variable_settings == 1) {
														echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
														echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
													} else {
														echo '<td width="100" align="right">&nbsp;</td>';
														echo '<td width="100" align="right">&nbsp;</td>';
													}
												}
												?>
												<td width="50" align="right" class="wrap_break"><? echo $ageOfDays; //$ageOfDays;         ?></td>
												<td width="50" align="right" class="wrap_break"><?
												if ($stockInHand > 0)
													echo $daysOnHand;
												else
												echo "&nbsp;"; //$daysOnHand;
												?></td>

												<?
												if( $yarnTestQalityCommentsArr[$row[csf("id")]] != ""){
													?>
													<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
												<?  }else{ ?>
													<td width="140" align="left"><span class="wrap_break"><? echo "&nbsp;"; ?></span></td>
												<? 	} ?>


												<td width="" align="center" class="wrap_break">
													<?
												/*$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
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
												echo $buyer_all;*/
												?>
												<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
												</td>
											</tr>
											<?
											$i++;

											$total_opening_balance += $openingBalance;
											$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
											$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
											$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
											$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
											$total_total_rcv += $totalRcv;
											$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
											$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
											$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
											$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
											$total_total_delivery += $totalIssue;
											$total_stock_in_hand += $stockInHand;
											$total_alocatted += $row[csf("allocated_qnty")];
											$total_free_stock += $row[csf("available_qnty")];
											$sub_stock_value += $stock_value;
											$sub_stock_value_usd += $stock_value_usd;
											$total_transfer_out_qty += $transfer_out_qty;
											$total_transfer_in_qty += $transfer_in_qty;

											//grand total===========================
											$grand_total_opening_balance += $openingBalance;
											$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
											$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
											$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
											$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
											$grand_total_total_rcv += $totalRcv;
											$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
											$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
											$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
											$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
											$grand_total_total_delivery += $totalIssue;
											$grand_total_stock_in_hand += $stockInHand;
											$grand_total_alocatted += $row[csf("allocated_qnty")];
											$grand_total_free_stock += $row[csf("available_qnty")];
											$tot_stock_value += $stock_value;
											$tot_stock_value_usd += $stock_value_usd;

											$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
											$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
										}
									}
									else
									{
										if (!in_array($check_string, $checkArr))
										{
											$checkArr[$i] = $check_string;
											if ($i > 1)
											{
												?>
												<tr bgcolor="#CCCCCC" style="font-weight:bold">
													<td colspan="10" align="right">Sub Total</td>
													<td width="110" class="wrap_break" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
													<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
													<?
													if ($show_val_column == 1) {
														echo '<td width="90" align="right" >&nbsp;</td>';
														echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
														echo '<td width="100" align="right">&nbsp;</td>';
														echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
													}

													if ($store_wise == 1) {
														echo '<td width="100">&nbsp;</td>';
													} else {
														if ($allocated_qty_variable_settings == 1) {
															echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
															echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
														} else {
															echo '<td width="100" align="right">&nbsp;</td>';
															echo '<td width="100" align="right">&nbsp;</td>';
														}
													}
													?>
													<td width="50" align="right">&nbsp;</td>
													<td width="50" align="right">&nbsp;</td>
													<td width="" align="right">&nbsp;</td>
													<td width="" align="right">&nbsp;</td>
												</tr>
												<?
												$total_opening_balance = 0;
												$total_purchase = 0;
												$total_inside_return = 0;
												$total_outside_return = 0;
												$total_rcv_loan = 0;
												$total_total_rcv = 0;
												$total_issue_inside = 0;
												$total_issue_outside = 0;
												$total_receive_return = 0;
												$total_issue_loan = 0;
												$total_total_delivery = 0;
												$total_stock_in_hand = 0;
												$total_alocatted = 0;
												$total_free_stock = 0;
												$sub_stock_value = 0;
												$sub_stock_value_usd = 0;
												$total_transfer_out_qty = 0;
												$total_transfer_in_qty = 0;
											}
										}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30"><? echo $i; ?></td>
											<td width="130" class="wrap_break"><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
											<td width="60" class="wrap_break"><? echo $row[csf("id")]; ?></td>
											<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
											<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
											<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
											<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
											<td width="100" class="wrap_break" style="mso-number-format:'\@';"><p>
												<? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
													<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
												<? }else{
													echo $row[csf("lot")];
												}
												?>
											&nbsp;</p></td>

											<td width="100" class="wrap_break">
												<?
												//commented dated 16.09.2021
												/*if($receive_basis==2) // work order basis
												{													
													if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
													{
														echo $companyArr[$row[csf("supplier_id")]];
													}else{
														echo $supplierArr[$row[csf("supplier_id")]];
													}
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}*/
												echo $supplier_name;
												?>
											</td>
											<td width="100" class="wrap_break"><? echo 'Bg:' . $receive_array[$row[csf("id")]]['weight_per_bag'] . '; ' . 'Cn:' . $receive_array[$row[csf("id")]]['weight_per_cone']; ?></td>
											<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
											<td width="90" class="wrap_break" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break">
												<?
												if ($store_wise == 1) {
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
												} else {
													echo number_format($transfer_in_qty, 2);
												}
												?>
											</td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break">
												<?
												if ($store_wise == 1) {
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
												} else {
													echo number_format($transfer_out_qty, 2);
												}
												?>
											</td>
											<td width="90" class="wrap_break" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($stockInHand, 4); ?></td>
											<?
											$stock_value = 0;
											if ($show_val_column == 1) {

												$avg_rate = ($stockInHandAmt/$stockInHand); //$mrr_rate_arr[$row[csf("id")]];
												if($avg_rate>0)
												{
													$avg_rate = $avg_rate;
												}else{
													$avg_rate = "0.00"; //$mrr_rate_arr[$row[csf("id")]];
												}
												
												$stock_value = $stockInHand * $avg_rate;
												$stock_value_usd = ($stockInHand * $avg_rate) / $exchange_rate;
												$avz_rates_usd=0;

												if(number_format($stock_value_usd,2)>0 && number_format($stockInHand,2)>0) {
													$avz_rates_usd=$stock_value_usd/$stockInHand;
												}else{
													$avz_rates_usd = "0.00";
												}

												echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
												echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
												echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
												echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
											}

											if ($store_wise == 1) {
												$store_name = '';
												$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
												$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];
												$store_id = array_unique(array_merge($receive_store_id, $issue_store_id));
												foreach ($store_id as $val) {
													if ($store_name == "")
														$store_name = $store_arr[$val];
													else
														$store_name .= ", " . $store_arr[$val];
												}
												echo '<td width="100" class="wrap_break">' . $store_name . '</td>';
											}
											else {
												if ($allocated_qty_variable_settings == 1) {
													echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
													echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
												} else {
													echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
													echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
												}
											}
											?>
											<td width="50" align="right" class="wrap_break"><? echo $ageOfDays; //$ageOfDays;         ?></td>
											<td width="50" align="right" class="wrap_break"><?
											if ($stockInHand > 0)
												echo $daysOnHand;
											else
													echo "&nbsp;"; //$daysOnHand;
											?></td>

											<? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != ""){ ?>
												<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
											<?  }else{ ?>
												<td width="140" align="left"><span ><? echo "&nbsp;"; ?></span></td>
											<? 	} ?>

											<td width="" align="center" class="wrap_break">
												<?
											/*$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
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
											echo $buyer_all;*/
											?>
											<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a></td>
										</tr>
										<?
										$i++;

										$total_opening_balance += $openingBalance;
										$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
										$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
										$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
										$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
										$total_total_rcv += $totalRcv;
										$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
										$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
										$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
										$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
										$total_total_delivery += $totalIssue;
										$total_stock_in_hand += $stockInHand;
										$total_alocatted += $row[csf("allocated_qnty")];
										$total_free_stock += $row[csf("available_qnty")];
										$sub_stock_value += $stock_value;
										$sub_stock_value_usd += $stock_value_usd;
										$total_transfer_out_qty += $transfer_out_qty;
										$total_transfer_in_qty += $transfer_in_qty;

										//grand total===========================
										$grand_total_opening_balance += $openingBalance;
										$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
										$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
										$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
										$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
										$grand_total_total_rcv += $totalRcv;
										$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
										$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
										$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
										$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
										$grand_total_total_delivery += $totalIssue;
										$grand_total_stock_in_hand += $stockInHand;
										$grand_total_alocatted += $row[csf("allocated_qnty")];
										$grand_total_free_stock += $row[csf("available_qnty")];
										$tot_stock_value += $stock_value;
										$tot_stock_value_usd += $stock_value_usd;

										$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
										$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
									}
								}
							}
						}
						?>
						<tr bgcolor="#CCCCCC" style="font-weight:bold">
							<td colspan="10" align="right">Sub Total</td>
							<td width="110" class="wrap_break" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
							<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
							<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
							<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
							<?
							if ($show_val_column == 1) {
								echo '<td width="90" align="right">&nbsp;</td>';
								echo '<td width="110" class="wrap_break" align="right">' . number_format($sub_stock_value, 2) . '</td>';
								echo '<td width="100" align="right">&nbsp;</td>';
								echo '<td width="100" class="wrap_break" align="right">' . number_format($sub_stock_value_usd, 2) . '</td>';
							}

							if ($store_wise == 1) {
								echo '<td width="100">&nbsp;</td>';
							} else {
								if ($allocated_qty_variable_settings == 1) {
									echo '<td width="100" align="right">' . number_format($total_alocatted, 2) . '</td>';
									echo '<td width="100" align="right">' . number_format($total_free_stock, 2) . '</td>';
								} else {
									echo '<td width="100" align="right">&nbsp;</td>';
									echo '<td width="100" align="right">&nbsp;</td>';
								}
							}
							?>
							<td width="50" align="right">&nbsp;</td>
							<td width="50" align="right">&nbsp;</td>
							<td width="" align="right">&nbsp;</td>
							<td width="" align="right">&nbsp;</td>
						</tr>
		 
					<tr class="tbl_bottom">
						<td width="30"></td>
						<td width="130"></td>
						<td width="60"></td>
						<td width="60"></td>
						<td width="150"></td>
						<td width="100"></td>
						<td width="120"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100" align="right">Grand Total</td>
						<td width="110" class="wrap_break" align="right" id="value_total_opening_balance"><? echo number_format($grand_total_opening_balance, 2); ?></td>
						<td width="90" align="right" id="value_total_purchase"><? echo number_format($grand_total_purchase, 2); ?></td>
						<td width="90" align="right" id="value_total_inside_return"><? echo number_format($grand_total_inside_return, 2); ?></td>
						<td width="90" align="right" id="value_total_outside_return"><? echo number_format($grand_total_outside_return, 2); ?></td>
						<td width="90" align="right" id="value_total_transfer_in"><? echo number_format($grand_total_transfer_in_qty, 2); ?></td>
						<td width="90" class="wrap_break" align="right" id="value_total_rcv_loan"><? echo number_format($grand_total_rcv_loan, 2); ?></td>
						<td width="100" class="wrap_break" align="right" id="value_total_total_rcv"><? echo number_format($grand_total_total_rcv, 2); ?></td>
						<td width="90" class="wrap_break" align="right" id="value_total_issue_inside"><? echo number_format($grand_total_issue_inside, 2); ?></td>
						<td width="90" class="wrap_break" align="right" id="value_total_issue_outside"><? echo number_format($grand_total_issue_outside, 2); ?></td>
						<td width="90" class="wrap_break"  align="right" id="value_total_receive_return"><? echo number_format($grand_total_receive_return, 2); ?></td>
						<td width="90" align="right" class="wrap_break" id="value_total_transfer_out"><? echo number_format($grand_total_transfer_out_qty, 2); ?></td>
						<td width="90" align="right" class="wrap_break" id="value_total_issue_loan"><? echo number_format($grand_total_issue_loan, 2); ?></td>
						<td width="100" align="right" class="wrap_break" id="value_total_total_delivery"><? echo number_format($grand_total_total_delivery, 2); ?></td>
						<td width="100" align="right" class="wrap_break" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 4); ?></td>
						<?
						if ($show_val_column == 1)
						{
							echo '<td width="90" align="right">&nbsp;</td>';
							echo '<td width="110" class="wrap_break" align="right">' . number_format($tot_stock_value, 2) . '</td>';
							echo '<td width="100" align="right">&nbsp;</td>';
							echo '<td width="100" class="wrap_break" align="right">' . number_format($tot_stock_value_usd, 2) . '</td>';
						}

						if ($store_wise == 1) {
							echo '<td width="100">&nbsp;</td>';
						} else {
							if ($allocated_qty_variable_settings == 1) {
								echo '<td width="100" align="right" id="value_total_alocatted">' . number_format($grand_total_alocatted, 2) . '</td>';
								echo '<td width="100" align="right" id="value_total_free_stock">' . number_format($grand_total_free_stock, 2) . '</td>';
							} else {
								echo '<td width="100" align="right" id="value_total_alocatted">&nbsp;</td>';
								echo '<td width="100" align="right" id="value_total_free_stock">&nbsp;</td>';
							}
						}
						?>
						<td width="50" align="right">&nbsp;</td>
						<td width="50" align="right">&nbsp;</td>
						<td width="140" align="right">&nbsp;</td>
						<td width="" align="right">&nbsp;</td>
					</tr>
				</table>
			</div>
			<?
		}
		else if ($type == 13)
		{
	 
			$date_array = array();
			$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 group by prod_id";
			$result_returnRes_date = sql_select($returnRes_date);
			foreach ($result_returnRes_date as $row)
			{
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
			}

			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, a.is_within_group order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
			$result = sql_select($sql);
			$i = 1;
			//ob_start();
			/*
			$mrr_rate_sql = sql_select("select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction
				where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) group by prod_id");
			$mrr_rate_arr = array();
			foreach ($mrr_rate_sql as $row) {
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
				$mrr_rate_arr[$row[csf("prod_id")]] = $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
			}*/

			//echo "<pre>";
			//print_r($mrr_rate_arr);
			?>
			<div>
				<table width="<? echo $table_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<tr class="form_caption" style="border:none;">
						<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Yarn Stock </td>
					</tr>
					<tr style="border:none;">
						<td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:14px;">
							Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
						</td>
					</tr>
				</table>
				<table width="<? echo $table_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr>
							<th rowspan="2" width="30">SL</th>
							<th rowspan="2" width="130">Company <br>rack wise</th>
							<th colspan="7">Description</th>
							<th rowspan="2" width="100">Wgt. Bag/Cone</th>
							<th rowspan="2" width="110">OpeningStock</th>
							<th colspan="6">Receive</th>
							<th colspan="6">Delivery</th>
							<th rowspan="2" width="100">Stock InHand</th>
							<?
							echo $column;
							if ($store_wise == 1) {
								echo '<th rowspan="2" width="100">Store Name</th>';
								echo '<th rowspan="2" width="100">Floor</th>';
								echo '<th rowspan="2" width="100">Room</th>';
								echo '<th rowspan="2" width="100">Rack</th>';
								echo '<th rowspan="2" width="100">Self</th>';
								echo '<th rowspan="2" width="100">Bin-Box</th>';
							} else {
								echo '<th rowspan="2" width="100">Allocated to Order</th>';
								echo '<th rowspan="2" width="100">Un Allocated Qty.</th>';
							}
							?>
							<th rowspan="2" width="50">Age (Days)</th>
							<th rowspan="2" width="50">DOH</th>
							<th rowspan="2" width="140">Yarn Quality<br>Comments</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<th width="60">Prod.ID</th>
							<th width="60">Count</th>
							<th width="150">Composition</th>
							<th width="100">Yarn Type</th>
							<th width="120">Color</th>
							<th width="100">Lot</th>
							<th width="100">Supplier</th>
							<th width="90">Purchase</th>
							<th width="90">Inside Return</th>
							<th width="90">Outside Return</th>
							<th width="90">Transfer In</th>
							<th width="90">Loan</th>
							<th width="100">Total Recv</th>
							<th width="90">Inside</th>
							<th width="90">Outside</th>
							<th width="90">Recv. Return</th>
							<th width="90">Trans. Out</th>
							<th width="90">Loan</th>
							<th width="100">Total Delivery</th>
						</tr>
					</thead>
				</table>
				<div style="width:<? echo $table_width + 38; ?>px;" id="scroll_body" >
					<table style="width:<? echo $table_width +20; ?>px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$tot_stock_value = 0;
						foreach ($result as $row)
						{
							$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
							$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

							$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
							if ($row[csf("yarn_comp_type2nd")] != 0)
								$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
							$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
							$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

							$transfer_in_amt = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
							$transfer_out_amt = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];


							$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
							$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

							$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];
							$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];

							$pay_mode = $receive_array[$row[csf("id")]]['pay_mode'];
							$receive_basis = $receive_array[$row[csf("id")]]['receive_basis'];

							$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

							$openingBalanceAmt = ($receive_array[$row[csf("id")]]['rcv_total_opening_amt'] + $trans_in_total_opening_amt) - ($issue_array[$row[csf("id")]]['issue_total_opening_amt'] + $trans_out_total_opening_amt);

							$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $receive_array[$row[csf("id")]]['rcv_adjustment_qty'] + $transfer_in_qty;
							
							$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $issue_array[$row[csf("id")]]['issue_adjustment_qty'] + $transfer_out_qty;

							$totalRcvAmt = $receive_array[$row[csf("id")]]['purchase_amt'] + $receive_array[$row[csf("id")]]['rcv_inside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_outside_return_amt'] + $receive_array[$row[csf("id")]]['rcv_loan_amt'] + $receive_array[$row[csf("id")]]['rcv_adjustment_amt'] + $transfer_in_amt;

							$totalIssueAmt = $issue_array[$row[csf("id")]]['issue_inside_amt'] + $issue_array[$row[csf("id")]]['issue_outside_amt'] + $issue_array[$row[csf("id")]]['rcv_return_amt'] + $issue_array[$row[csf("id")]]['issue_loan_amt'] +$issue_array[$row[csf("id")]]['issue_adjustment_amt'] + $transfer_out_qty;


							$stockInHand = $openingBalance + $totalRcv - $totalIssue;

							$stockInHandAmt = $openingBalanceAmt + $totalRcvAmt - $totalIssueAmt;

							//subtotal and group-----------------------
							$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

							if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0))
							{
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
									
								if($value_with == 1)
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
													<td colspan="10" align="right">Sub Total</td>
													<td width="110" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
													<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
													<td width="100" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
													<?
													if ($show_val_column == 1) {
														echo '<td width="90" align="right">&nbsp;</td>';
														echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
														echo '<td width="100" align="right">&nbsp;</td>';
														echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
													}

													if ($store_wise == 1) {
														echo '<td width="100">&nbsp;</td>';
														echo '<td width="100">&nbsp;</td>';
														echo '<td width="100">&nbsp;</td>';
														echo '<td width="100">&nbsp;</td>';
														echo '<td width="100">&nbsp;</td>';
														echo '<td width="100">&nbsp;</td>';
													} else {
														if ($allocated_qty_variable_settings == 1) {
															echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
															echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
														} else {
															echo '<td width="100" align="right">&nbsp;</td>';
															echo '<td width="100" align="right">&nbsp;</td>';
														}
													}
													?>
													<td width="50" align="right">&nbsp;</td>
													<td width="50" align="right">&nbsp;</td>
													<td align="right">&nbsp;</td>
													<td width="" align="right">&nbsp;</td>
												</tr>
												<?
												$total_opening_balance = 0;
												$total_purchase = 0;
												$total_inside_return = 0;
												$total_outside_return = 0;
												$total_rcv_loan = 0;
												$total_total_rcv = 0;
												$total_issue_inside = 0;
												$total_issue_outside = 0;
												$total_receive_return = 0;
												$total_issue_loan = 0;
												$total_total_delivery = 0;
												$total_stock_in_hand = 0;
												$total_alocatted = 0;
												$total_free_stock = 0;
												$sub_stock_value = 0;
												$sub_stock_value_usd = 0;
												$total_transfer_out_qty = 0;
												$total_transfer_in_qty = 0;
											}
										}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30"><? echo $i; ?></td>
											<td width="130" class="wrap_break" ><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
											<td width="60"><? echo $row[csf("id")]; ?></td>
											<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
											<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
											<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
											<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
											<td width="100"><p>
												<?
													if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
													<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
												<? }else if( $yarnTestArr[$row[csf("id")]] != "") { ?>
													<a href='##' onclick="show_test_report2('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
												<? } else {
													echo $row[csf("lot")];
												} ?></p> </td>
											<td width="100" class="wrap_break" >
												<?
												//commented dated 16.09.2021
												/*if($receive_basis==2) // work order basis
												{													
													if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
													{
														echo $companyArr[$row[csf("supplier_id")]];
													}else{
														echo $supplierArr[$row[csf("supplier_id")]];
													}
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}*/
												echo $supplier_name;
												?>
											</td>
											<td width="100" class="wrap_break" ><? echo 'Bg:' . $receive_array[$row[csf("id")]]['weight_per_bag'] . '; ' . 'Cn:' . $receive_array[$row[csf("id")]]['weight_per_cone']; ?></td>
											<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
											<td width="90" class="wrap_break" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break">
												<?
												if ($store_wise == 1) {
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
												} else {
													echo number_format($transfer_in_qty, 2);
												}
												?>
											</td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
											<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
											<td width="90" align="right" class="wrap_break">
												<?
												if ($store_wise == 1) {
													echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
												} else {
													echo number_format($transfer_out_qty, 2);
												}
												?>
											</td>
											<td width="90" class="wrap_break" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>
											<td width="100" align="right" class="wrap_break"><? echo number_format($stockInHand, 4); ?></td>
											<?

											$stock_value = 0;
											if ($show_val_column == 1)
											{
												$avg_rate = ($stockInHandAmt/$stockInHand); //$mrr_rate_arr[$row[csf("id")]];
												if($avg_rate>0)
												{
													$avg_rate=$avg_rate;	
												}else{
													$avg_rate = "0.00"; //$mrr_rate_arr[$row[csf("id")]];
												}
												
												$stock_value = $stockInHand * $avg_rate;
												$stock_value_usd = ($stockInHand * $avg_rate) / $exchange_rate;
												$avz_rates_usd=0;

												if(number_format($stock_value_usd,2)>0 && number_format($stockInHand,2)>0){
													$avz_rates_usd=$stock_value_usd/$stockInHand;
												}else{
													$avz_rates_usd="0.00";
												}

												echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
												echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
												echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
												echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
											}

											if ($store_wise == 1)
											{
												$store_name = '';
												//$receive_store_id = explode(",", $receive_array[$row[csf("id")]]['store_id']);
												//$issue_store_id = explode(",", $issue_array[$row[csf("id")]]['store_id']);
												$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
												$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];
												
												$store_id = array_unique(array_merge($receive_store_id, $issue_store_id));
												foreach ($store_id as $val) {
													if ($store_name == "")
														$store_name = $store_arr[$val];
													else
														$store_name .= ", " . $store_arr[$val];
												}
												echo '<td width="100" class="wrap_break">' . $store_name . '</td>';

												$floor_name = '';
												$receive_floor_id = $receive_array[$row[csf("id")]]['floor_id'];
												$issue_floor_id = $issue_array[$row[csf("id")]]['floor_id'];
												
												$floor_id = array_unique(array_merge($receive_floor_id, $issue_floor_id));
												foreach ($floor_id as $val) {
													if ($floor_name == "")
														$floor_name = $floor_room_rack_arr[$val];
													else
														$floor_name .= ", " . $floor_room_rack_arr[$val];
												}

												echo '<td width="100" class="wrap_break">' . $floor_name . '</td>';

												$room_name = '';
												$receive_room_id = $receive_array[$row[csf("id")]]['room'];
												$issue_room_id = $issue_array[$row[csf("id")]]['room'];
												
												$room_id = array_unique(array_merge($receive_room_id, $issue_room_id));
												foreach ($room_id as $val) {
													if ($room_name == "")
														$room_name = $floor_room_rack_arr[$val];
													else
														$room_name .= ", " . $floor_room_rack_arr[$val];
												}

												echo '<td width="100" class="wrap_break">' . $room_name . '</td>';

												$rack_name = '';
												$receive_rack_id = $receive_array[$row[csf("id")]]['rack'];
												$issue_rack_id = $issue_array[$row[csf("id")]]['rack'];
												
												$rack_id = array_unique(array_merge($receive_rack_id, $issue_rack_id));
												foreach ($rack_id as $val) {
													if ($rack_name == "")
														$rack_name = $floor_room_rack_arr[$val];
													else
														$rack_name .= ", " . $floor_room_rack_arr[$val];
												}
												echo '<td width="100" class="wrap_break">' . $rack_name . '</td>';

												$self_name = '';
												$receive_self_id = $receive_array[$row[csf("id")]]['self'];
												$issue_self_id = $issue_array[$row[csf("id")]]['self'];
												
												$self_id = array_unique(array_merge($receive_self_id, $issue_self_id));
											
												foreach ($self_id as $val) {
													if ($self_name == "")
														$self_name = $floor_room_rack_arr[$val];
													else
														$self_name .= ", " . $floor_room_rack_arr[$val];
												}

												echo '<td width="100" class="wrap_break">' . $self_name . '</td>';

												$bin_box_name = '';
												$receive_bin_box_id = $receive_array[$row[csf("id")]]['bin_box'];
												$issue_bin_box_id = $issue_array[$row[csf("id")]]['bin_box'];
												
												$bin_box_id = array_unique(array_merge($receive_bin_box_id, $issue_bin_box_id));
												foreach ($bin_box_id as $val) {
													if ($bin_box_name == "")
														$bin_box_name = $floor_room_rack_arr[$val];
													else
														$bin_box_name .= ", " . $floor_room_rack_arr[$val];
												}

												echo '<td width="100" class="wrap_break">' . $bin_box_name . '</td>';
											}
											else
											{
												if ($allocated_qty_variable_settings == 1) {
													echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
													echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
												} else {
													echo '<td width="100" align="right">&nbsp;</td>';
													echo '<td width="100" align="right">&nbsp;</td>';
												}
											}
											?>
											<td width="50" align="right" class="wrap_break"><? echo $ageOfDays; //$ageOfDays;         ?></td>
											<td width="50" align="right" class="wrap_break">
											<?if($daysOnHand >= 180){?>
											<p style="background-color: red;" title="180 days or above">
											<?
											if ($stockInHand > 0)
												echo $daysOnHand;
											else
											echo "&nbsp;"; //$daysOnHand;
											?></p>
											<?}else{?>
											<p>
											<?
											if ($stockInHand > 0)
												echo $daysOnHand;
											else
											echo "&nbsp;"; //$daysOnHand;
											?></p>	
											<?}?>
											</td>

											<?
											if( $yarnTestQalityCommentsArr[$row[csf("id")]] != ""){
												?>
												<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
											<?  }else{ ?>
												<td width="140" align="left"><span class="wrap_break"><? echo "&nbsp;"; ?></span></td>
											<? 	} ?>


											<td width="" align="center" class="wrap_break">
												<?
											/*$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
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
											echo $buyer_all;*/
											?>
											<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a>
											</td>
										</tr>
										<?
										$i++;

										$total_opening_balance += $openingBalance;
										$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
										$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
										$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
										$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
										$total_total_rcv += $totalRcv;
										$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
										$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
										$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
										$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
										$total_total_delivery += $totalIssue;
										$total_stock_in_hand += $stockInHand;
										$total_alocatted += $row[csf("allocated_qnty")];
										$total_free_stock += $row[csf("available_qnty")];
										$sub_stock_value += $stock_value;
										$sub_stock_value_usd += $stock_value_usd;
										$total_transfer_out_qty += $transfer_out_qty;
										$total_transfer_in_qty += $transfer_in_qty;

										//grand total===========================
										$grand_total_opening_balance += $openingBalance;
										$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
										$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
										$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
										$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
										$grand_total_total_rcv += $totalRcv;
										$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
										$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
										$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
										$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
										$grand_total_total_delivery += $totalIssue;
										$grand_total_stock_in_hand += $stockInHand;
										$grand_total_alocatted += $row[csf("allocated_qnty")];
										$grand_total_free_stock += $row[csf("available_qnty")];
										$tot_stock_value += $stock_value;
										$tot_stock_value_usd += $stock_value_usd;

										$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
										$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
									}
								}
								else
								{
									if (!in_array($check_string, $checkArr))
									{
										$checkArr[$i] = $check_string;
										if ($i > 1)
										{
											?>
											<tr bgcolor="#CCCCCC" style="font-weight:bold">
												<td colspan="10" align="right">Sub Total</td>
												<td width="110" class="wrap_break" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
												<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
												<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
												<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
												<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
												<?
												if ($show_val_column == 1) {
													echo '<td width="90" align="right" >&nbsp;</td>';
													echo '<td width="110" align="right" class="wrap_break">' . number_format($sub_stock_value, 2) . '</td>';
													echo '<td width="100" align="right">&nbsp;</td>';
													echo '<td width="100" align="right" class="wrap_break">' . number_format($sub_stock_value_usd, 2) . '</td>';
												}

												if ($store_wise == 1) {
													echo '<td width="100">&nbsp;</td>';
													echo '<td width="100">&nbsp;</td>';
													echo '<td width="100">&nbsp;</td>';
													echo '<td width="100">&nbsp;</td>';
													echo '<td width="100">&nbsp;</td>';
													echo '<td width="100">&nbsp;</td>';
												} else {
													if ($allocated_qty_variable_settings == 1) {
														echo '<td width="100"  class="wrap_break" align="right">' . number_format($total_alocatted, 2) . '</td>';
														echo '<td width="100" class="wrap_break" align="right">' . number_format($total_free_stock, 2) . '</td>';
													} else {
														echo '<td width="100" align="right">&nbsp;</td>';
														echo '<td width="100" align="right">&nbsp;</td>';
													}
												}
												?>
												<td width="50" align="right">&nbsp;</td>
												<td width="50" align="right">&nbsp;</td>
												<td width="" align="right">&nbsp;</td>
												<td width="" align="right">&nbsp;</td>
											</tr>
											<?
											$total_opening_balance = 0;
											$total_purchase = 0;
											$total_inside_return = 0;
											$total_outside_return = 0;
											$total_rcv_loan = 0;
											$total_total_rcv = 0;
											$total_issue_inside = 0;
											$total_issue_outside = 0;
											$total_receive_return = 0;
											$total_issue_loan = 0;
											$total_total_delivery = 0;
											$total_stock_in_hand = 0;
											$total_alocatted = 0;
											$total_free_stock = 0;
											$sub_stock_value = 0;
											$sub_stock_value_usd = 0;
											$total_transfer_out_qty = 0;
											$total_transfer_in_qty = 0;
										}
									}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30"><? echo $i; ?></td>
										<td width="130" class="wrap_break"><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
										<td width="60" class="wrap_break"><? echo $row[csf("id")]; ?></td>
										<td width="60" class="wrap_break" style="mso-number-format:'\@';"><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></td>
										<td width="150" class="wrap_break"><? echo $compositionDetails; ?></td>
										<td width="100" class="wrap_break"><? echo $yarn_type[$row[csf("yarn_type")]]; ?></td>
										<td width="120" class="wrap_break"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
										<td width="100" class="wrap_break" style="mso-number-format:'\@';"><p>
											<? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
												<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
											<? }else{
												echo $row[csf("lot")];
											}
											?>
										&nbsp;</p></td>

										<td width="100" class="wrap_break">
											<?
											//commented by Zaman dated 19.09.2021
											/*if($receive_basis==2) // work order basis
											{													
												if($pay_mode==3 || $pay_mode==5) // in-houwse/withing group
												{
													echo $companyArr[$row[csf("supplier_id")]];
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}
											}else{
												echo $supplierArr[$row[csf("supplier_id")]];
											}*/
											echo $supplier_name;
											?>
										</td>
										<td width="100" class="wrap_break"><? echo 'Bg:' . $receive_array[$row[csf("id")]]['weight_per_bag'] . '; ' . 'Cn:' . $receive_array[$row[csf("id")]]['weight_per_cone']; ?></td>
										<td width="110" class="wrap_break" align="right"><? echo number_format($openingBalance, 2); ?></td>
										<td width="90" class="wrap_break" align="right"><? echo number_format($receive_array[$row[csf("id")]]['purchase'], 2); ?></td>
										<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_inside_return'], 2); ?></td>
										<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_outside_return'], 2); ?></td>
										<td width="90" align="right" class="wrap_break">
											<?
											if ($store_wise == 1) {
												echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",5,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_in_qty, 2) . "</a>";
											} else {
												echo number_format($transfer_in_qty, 2);
											}
											?>
										</td>
										<td width="90" align="right" class="wrap_break"><? echo number_format($receive_array[$row[csf("id")]]['rcv_loan'], 2); ?></td>
										<td width="100" align="right" class="wrap_break"><? echo number_format($totalRcv, 2); ?></td>
										<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_inside'], 2); ?></td>
										<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['issue_outside'], 2); ?></td>
										<td width="90" align="right" class="wrap_break"><? echo number_format($issue_array[$row[csf("id")]]['rcv_return'], 2); ?></td>
										<td width="90" align="right" class="wrap_break">
											<?
											if ($store_wise == 1) {
												echo "<a href='##' onclick=\"openmypage_trans(" . $row[csf('id')] . ",6,'" . $store_name . "','" . $from_date . "','" . $to_date . "','transferPopup')\">" . number_format($transfer_out_qty, 2) . "</a>";
											} else {
												echo number_format($transfer_out_qty, 2);
											}
											?>
										</td>
										<td width="90" class="wrap_break" align="right"><? echo number_format($issue_array[$row[csf("id")]]['issue_loan'], 2); ?></td>
										<td width="100" align="right" class="wrap_break"><? echo number_format($totalIssue, 2); ?></td>
										<td width="100" align="right" class="wrap_break"><? echo number_format($stockInHand, 4); ?></td>
										<?
										$stock_value = 0;
										if ($show_val_column == 1) {

											$avg_rate = ($stockInHandAmt/$stockInHand); //$mrr_rate_arr[$row[csf("id")]];
											if($avg_rate>0)
											{
												$avg_rate = $avg_rate;
											}else{
												$avg_rate = "0.00"; //$mrr_rate_arr[$row[csf("id")]];
											}
											
											$stock_value = $stockInHand * $avg_rate;
											$stock_value_usd = ($stockInHand * $avg_rate) / $exchange_rate;
											$avz_rates_usd=0;

											if(number_format($stock_value_usd,2)>0 && number_format($stockInHand,2)>0) {
												$avz_rates_usd=$stock_value_usd/$stockInHand;
											}else{
												$avz_rates_usd = "0.00";
											}

											echo '<td width="90" align="right" class="wrap_break">' . number_format($avg_rate, 2) . '</td>';
											echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
											echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
											echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
										}

										if ($store_wise == 1)
										{
											$store_name = '';
											//$receive_store_id = explode(",", $receive_array[$row[csf("id")]]['store_id']);
											//$issue_store_id = explode(",", $issue_array[$row[csf("id")]]['store_id']);
											$receive_store_id = $receive_array[$row[csf("id")]]['store_id'];
											$issue_store_id = $issue_array[$row[csf("id")]]['store_id'];
											
											$store_id = array_unique(array_merge($receive_store_id, $issue_store_id));
											foreach ($store_id as $val) {
												if ($store_name == "")
													$store_name = $store_arr[$val];
												else
													$store_name .= ", " . $store_arr[$val];
											}
											echo '<td width="100" class="wrap_break">' . $store_name . '</td>';

											$floor_name = '';
											$receive_floor_id = $receive_array[$row[csf("id")]]['floor_id'];
											$issue_floor_id = $issue_array[$row[csf("id")]]['floor_id'];
											
											$floor_id = array_unique(array_merge($receive_floor_id, $issue_floor_id));
											foreach ($floor_id as $val) {
												if ($floor_name == "")
													$floor_name = $floor_room_rack_arr[$val];
												else
													$floor_name .= ", " . $floor_room_rack_arr[$val];
											}

											echo '<td width="100" class="wrap_break">' . $floor_name . '</td>';

											$room_name = '';
											$receive_room_id = $receive_array[$row[csf("id")]]['room'];
											$issue_room_id = $issue_array[$row[csf("id")]]['room'];
											
											$room_id = array_unique(array_merge($receive_room_id, $issue_room_id));
											foreach ($room_id as $val) {
												if ($room_name == "")
													$room_name = $floor_room_rack_arr[$val];
												else
													$room_name .= ", " . $floor_room_rack_arr[$val];
											}

											echo '<td width="100" class="wrap_break">' . $room_name . '</td>';

											$rack_name = '';
											$receive_rack_id = $receive_array[$row[csf("id")]]['rack'];
											$issue_rack_id = $issue_array[$row[csf("id")]]['rack'];
											
											$rack_id = array_unique(array_merge($receive_rack_id, $issue_rack_id));
											foreach ($rack_id as $val) {
												if ($rack_name == "")
													$rack_name = $floor_room_rack_arr[$val];
												else
													$rack_name .= ", " . $floor_room_rack_arr[$val];
											}
											echo '<td width="100" class="wrap_break">' . $rack_name . '</td>';

											$self_name = '';
											$receive_self_id = $receive_array[$row[csf("id")]]['self'];
											$issue_self_id = $issue_array[$row[csf("id")]]['self'];
											
											$self_id = array_unique(array_merge($receive_self_id, $issue_self_id));
										
											foreach ($self_id as $val) {
												if ($self_name == "")
													$self_name = $floor_room_rack_arr[$val];
												else
													$self_name .= ", " . $floor_room_rack_arr[$val];
											}

											echo '<td width="100" class="wrap_break">' . $self_name . '</td>';

											$bin_box_name = '';
											$receive_bin_box_id = $receive_array[$row[csf("id")]]['bin_box'];
											$issue_bin_box_id = $issue_array[$row[csf("id")]]['bin_box'];
											
											$bin_box_id = array_unique(array_merge($receive_bin_box_id, $issue_bin_box_id));
											foreach ($bin_box_id as $val) {
												if ($bin_box_name == "")
													$bin_box_name = $floor_room_rack_arr[$val];
												else
													$bin_box_name .= ", " . $floor_room_rack_arr[$val];
											}

											echo '<td width="100" class="wrap_break">' . $bin_box_name . '</td>';
										}
										else {
											if ($allocated_qty_variable_settings == 1) {
												echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
												echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
											} else {
												echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
												echo '<td width="100" align="right" class="wrap_break">&nbsp;</td>';
											}
										}
										?>
										<td width="50" align="right" class="wrap_break"><? echo $ageOfDays; //$ageOfDays;         ?></td>
										<td width="50" align="right" class="wrap_break"><?
										if ($stockInHand > 0)
											echo $daysOnHand;
										else
												echo "&nbsp;"; //$daysOnHand;
										?></td>

										<? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != ""){ ?>
											<td width="140" align="left"><span class="comment more" style="word-wrap: break-word;word-break: break-all;"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
										<?  }else{ ?>
											<td width="140" align="left"><span ><? echo "&nbsp;"; ?></span></td>
										<? 	} ?>

										<td width="" align="center" class="wrap_break">
											<?
										/*$buyer_id_arr = explode(",", $yarn_allo_arr[$row[csf("id")]]['buyer_id']);
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
										echo $buyer_all;*/
										?>
										<a href='##' onclick="openmypage_remarks(<? echo $row[csf("id")]; ?>)">View</a></td>
									</tr>
									<?
									$i++;

									$total_opening_balance += $openingBalance;
									$total_purchase += $receive_array[$row[csf("id")]]['purchase'];
									$total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
									$total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
									$total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
									$total_total_rcv += $totalRcv;
									$total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
									$total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
									$total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
									$total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
									$total_total_delivery += $totalIssue;
									$total_stock_in_hand += $stockInHand;
									$total_alocatted += $row[csf("allocated_qnty")];
									$total_free_stock += $row[csf("available_qnty")];
									$sub_stock_value += $stock_value;
									$sub_stock_value_usd += $stock_value_usd;
									$total_transfer_out_qty += $transfer_out_qty;
									$total_transfer_in_qty += $transfer_in_qty;

									//grand total===========================
									$grand_total_opening_balance += $openingBalance;
									$grand_total_purchase += $receive_array[$row[csf("id")]]['purchase'];
									$grand_total_inside_return += $receive_array[$row[csf("id")]]['rcv_inside_return'];
									$grand_total_outside_return += $receive_array[$row[csf("id")]]['rcv_outside_return'];
									$grand_total_rcv_loan += $receive_array[$row[csf("id")]]['rcv_loan'];
									$grand_total_total_rcv += $totalRcv;
									$grand_total_issue_inside += $issue_array[$row[csf("id")]]['issue_inside'];
									$grand_total_issue_outside += $issue_array[$row[csf("id")]]['issue_outside'];
									$grand_total_receive_return += $issue_array[$row[csf("id")]]['rcv_return'];
									$grand_total_issue_loan += $issue_array[$row[csf("id")]]['issue_loan'];
									$grand_total_total_delivery += $totalIssue;
									$grand_total_stock_in_hand += $stockInHand;
									$grand_total_alocatted += $row[csf("allocated_qnty")];
									$grand_total_free_stock += $row[csf("available_qnty")];
									$tot_stock_value += $stock_value;
									$tot_stock_value_usd += $stock_value_usd;

									$grand_total_transfer_out_qty += $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
									$grand_total_transfer_in_qty += $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
								}
							}
						}
						?>
						<tr bgcolor="#CCCCCC" style="font-weight:bold">
							<td colspan="10" align="right">Sub Total</td>
							<td width="110" class="wrap_break" align="right"><? echo number_format($total_opening_balance, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_purchase, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_inside_return, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_outside_return, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_transfer_in_qty, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_rcv_loan, 2); ?></td>
							<td width="100" align="right"><? echo number_format($total_total_rcv, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_issue_inside, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_issue_outside, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_receive_return, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_transfer_out_qty, 2); ?></td>
							<td width="90" align="right"><? echo number_format($total_issue_loan, 2); ?></td>
							<td width="100" align="right"><? echo number_format($total_total_delivery, 2); ?></td>
							<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 4); ?></td>
							<?
							if ($show_val_column == 1) {
								echo '<td width="90" align="right">&nbsp;</td>';
								echo '<td width="110" class="wrap_break" align="right">' . number_format($sub_stock_value, 2) . '</td>';
								echo '<td width="100" align="right">&nbsp;</td>';
								echo '<td width="100" class="wrap_break" align="right">' . number_format($sub_stock_value_usd, 2) . '</td>';
							}

							if ($store_wise == 1) {
								echo '<td width="100">&nbsp;</td>';
								echo '<td width="100">&nbsp;</td>';
								echo '<td width="100">&nbsp;</td>';
								echo '<td width="100">&nbsp;</td>';
								echo '<td width="100">&nbsp;</td>';
								echo '<td width="100">&nbsp;</td>';
							} else {
								if ($allocated_qty_variable_settings == 1) {
									echo '<td width="100" align="right">' . number_format($total_alocatted, 2) . '</td>';
									echo '<td width="100" align="right">' . number_format($total_free_stock, 2) . '</td>';
								} else {
									echo '<td width="100" align="right">&nbsp;</td>';
									echo '<td width="100" align="right">&nbsp;</td>';
								}
							}
							?>
							<td width="50" align="right">&nbsp;</td>
							<td width="50" align="right">&nbsp;</td>
							<td width="" align="right">&nbsp;</td>
							<td width="" align="right">&nbsp;</td>
						</tr>
				
					<tr class="tbl_bottom">
						<td width="30"></td>
						<td width="130"></td>
						<td width="60"></td>
						<td width="60"></td>
						<td width="150"></td>
						<td width="100"></td>
						<td width="120"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="100" align="right">Grand Total</td>
						<td width="110" class="wrap_break" align="right" id="value_total_opening_balance"><? echo number_format($grand_total_opening_balance, 2); ?></td>
						<td width="90" align="right" id="value_total_purchase"><? echo number_format($grand_total_purchase, 2); ?></td>
						<td width="90" align="right" id="value_total_inside_return"><? echo number_format($grand_total_inside_return, 2); ?></td>
						<td width="90" align="right" id="value_total_outside_return"><? echo number_format($grand_total_outside_return, 2); ?></td>
						<td width="90" align="right" id="value_total_transfer_in"><? echo number_format($grand_total_transfer_in_qty, 2); ?></td>
						<td width="90" class="wrap_break" align="right" id="value_total_rcv_loan"><? echo number_format($grand_total_rcv_loan, 2); ?></td>
						<td width="100" class="wrap_break" align="right" id="value_total_total_rcv"><? echo number_format($grand_total_total_rcv, 2); ?></td>
						<td width="90" class="wrap_break" align="right" id="value_total_issue_inside"><? echo number_format($grand_total_issue_inside, 2); ?></td>
						<td width="90" class="wrap_break" align="right" id="value_total_issue_outside"><? echo number_format($grand_total_issue_outside, 2); ?></td>
						<td width="90" class="wrap_break"  align="right" id="value_total_receive_return"><? echo number_format($grand_total_receive_return, 2); ?></td>
						<td width="90" align="right" class="wrap_break" id="value_total_transfer_out"><? echo number_format($grand_total_transfer_out_qty, 2); ?></td>
						<td width="90" align="right" class="wrap_break" id="value_total_issue_loan"><? echo number_format($grand_total_issue_loan, 2); ?></td>
						<td width="100" align="right" class="wrap_break" id="value_total_total_delivery"><? echo number_format($grand_total_total_delivery, 2); ?></td>
						<td width="100" align="right" class="wrap_break" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 4); ?></td>
						<?
						if ($show_val_column == 1)
						{
							echo '<td width="90" align="right">&nbsp;</td>';
							echo '<td width="110" class="wrap_break" align="right">' . number_format($tot_stock_value, 2) . '</td>';
							echo '<td width="100" align="right">&nbsp;</td>';
							echo '<td width="100" class="wrap_break" align="right">' . number_format($tot_stock_value_usd, 2) . '</td>';
						}

						if ($store_wise == 1) {
							echo '<td width="100">&nbsp;</td>';
							echo '<td width="100">&nbsp;</td>';
							echo '<td width="100">&nbsp;</td>';
							echo '<td width="100">&nbsp;</td>';
							echo '<td width="100">&nbsp;</td>';
							echo '<td width="100">&nbsp;</td>';
						} else {
							if ($allocated_qty_variable_settings == 1) {
								echo '<td width="100" align="right" id="value_total_alocatted">' . number_format($grand_total_alocatted, 2) . '</td>';
								echo '<td width="100" align="right" id="value_total_free_stock">' . number_format($grand_total_free_stock, 2) . '</td>';
							} else {
								echo '<td width="100" align="right" id="value_total_alocatted">&nbsp;</td>';
								echo '<td width="100" align="right" id="value_total_free_stock">&nbsp;</td>';
							}
						}
						?>
						<td width="50" align="right">&nbsp;</td>
						<td width="50" align="right">&nbsp;</td>
						<td width="140" align="right">&nbsp;</td>
						<td width="" align="right">&nbsp;</td>
					</tr>
				</table>
			</div>
			<?
		}
	}
	?>
	<script>
		$(document).ready(function() 
		{
			var showChar = 30;
			//var ellipsestext = "...";
			var ellipsestext = "";
			var moretext = "more";
			var lesstext = "less";
			$('.more').each(function() 
			{
				var content = $(this).html();

				if(content.length > showChar) 
				{

					var c = content.substr(0,showChar);
					var h = content.substr(showChar-1, content.length - showChar);

					var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

					$(this).html(html);
				}

			});

			$(".morelink").click(function()
			{
				if($(this).hasClass("less")) {
					$(this).removeClass("less");
					$(this).html(moretext);
				} else {
					$(this).addClass("less");
					$(this).html(lesstext);
				}

				$(this).parent().prev().toggle();
				$(this).prev().toggle();
				return false;
			});

		});
	</script>
	<?



	
	$html = ob_get_contents();
	ob_end_clean();
	echo $html; 
 
	
	// foreach (glob("../tmp/"."*.pdf") as $filename) {			
	// 	@unlink($filename);
	// }
	// $att_file_arr=array();
	// $mpdf = new mPDF();
	// $mpdf->WriteHTML($html,2);
	// $REAL_FILE_NAME = 'daily_yarn_stock_auto_mail_' . date('j-M-Y_h-iA') . '.pdf';
	// $mpdf->Output('../tmp/' . $REAL_FILE_NAME, 'F');
	// $att_file_arr[]='../tmp/'.$REAL_FILE_NAME.'**'.$REAL_FILE_NAME;
	

	$mail_item=88;
	$to="";	
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=88 and a.company_id=".$company_id." and b.mail_user_setup_id=c.id   and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";//and 
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if($row[csf('email_address')]){$toMailArr[]=$row[csf('email_address')]; }
	}
 	
	$to=implode(',',$toMailArr);
	$subject = "Daily yarn stock auto mail";
	$message="<b>Sir,</b><br>Please check daily yarn stock att. file";
	
	
	require_once('../../mailer/class.phpmailer.php');
	require('../setting/mail_setting.php');
	$header=mailHeader();
	

	if($_REQUEST['isview']==1){
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo  $message."<br>".$html;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);}
	}

	unset($html);
	
	
}

}//company
 
 
 
 

 
	?>
