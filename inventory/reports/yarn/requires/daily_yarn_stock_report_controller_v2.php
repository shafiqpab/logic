<?

header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "")
{
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//--------------------------------------------------------------------------------------------

if ($action == "load_drop_down_store")
{
	$data = explode("**", $data);

	if ($data[1] == 2)
	{
		$disable = 1;
	}
	else
	{
		$disable = 0;
	}
	echo create_drop_down("cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type in(1)  order by a.store_name", "id,store_name", 1, "-- All Store--", 0, "", $disable);
	exit();
}

//load drop down supplier
if ($action == "load_drop_down_supplier")
{
	if($data){$companyCon=" and a.tag_company='$data'";}
	else{$companyCon="";}
	echo create_drop_down("cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $companyCon and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 0, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "eval_multi_select")
{
	echo "set_multiselect('cbo_supplier','0','0','','0');\n";
	exit();
}

if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$companyArr[0] = "All Company";
	$companyShortName = return_library_array("select id,company_short_name from lib_company", "id", "company_short_name");
	$supplierArr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$yarnTestQalityCommentsArr = return_library_array("select prod_id, yarn_quality_coments from inv_yarn_test_mst where  status_active=1 and is_deleted=0", 'prod_id', 'yarn_quality_coments');
	$yarnTestArr = return_library_array("select prod_id, lot_number from inv_yarn_test_mst where status_active=1 and is_deleted=0", 'prod_id', 'lot_number');
	//echo '<pre>';print_r($yarnTestArr);die;

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
	ob_start();
	?>
	<style>
		a {
			color: #0254EB
		}
		a:visited {
			color: #0254EB
		}
		a.morelink {
			text-decoration:none;
			outline: none;
		}
		.morecontent span {
			display: none;
		}
		.comment {
			width: 400px;
			background-color: #f0f0f0;
			margin: 10px;
		}
	</style>
	<?

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
		}
		else
		{
			$company_cond = " and b.company_id=$cbo_company_name";
		}

		if ($to_date != "")
			$mrr_date_cond = " and a.transaction_date<='$to_date'";
		if ($to_date != "")
			$rcv_date_cond = " and b.transaction_date<='$to_date'";

		if ($cbo_company_name == 0)
		{
			$company_cond_mrr = "";
		}
		else
		{
			$company_cond_mrr = " and a.company_id=$cbo_company_name";
		}
		
		$issue_qnty_arr = sql_select("select a.prod_id, b.recv_trans_id, b.issue_qnty from  inv_transaction a,  inv_mrr_wise_issue_details b where a.id=b.issue_trans_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type in(2,3,6) and a.item_category=1 $company_cond_mrr $mrr_date_cond");
		$mrr_issue_qnty_arr = array();
		foreach ($issue_qnty_arr as $row)
		{
			$mrr_issue_qnty_arr[$row[csf("recv_trans_id")]][$row[csf("prod_id")]] += $row[csf("issue_qnty")];
		}
		unset($issue_qnty_arr);

        $receive_sql = sql_select("select c.id, c.recv_number, c.receive_date from inv_receive_master c where item_category=1");
        $receive_data = array();
        foreach ($receive_sql as $row)
        {
      	    $receive_data[$row[csf("id")]]["recv_number"] = $row[csf("recv_number")];
      	    $receive_data[$row[csf("id")]]["receive_date"] = $row[csf("receive_date")];
        }
        unset($receive_sql);

        $transfer_sql = sql_select("select c.id, c.transfer_system_id, c.transfer_date from inv_item_transfer_mst c where item_category=1");
        $transfer_data = array();
        foreach ($receive_sql as $row)
        {
      		$transfer_data[$row[csf("id")]]["transfer_system_id"] = $row[csf("transfer_system_id")];
      		$transfer_data[$row[csf("id")]]["transfer_date"] = $row[csf("transfer_date")];
        }
        unset($transfer_sql);

        $mrr_rate_sql = sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction
      	where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) group by prod_id");
        $mrr_rate_arr = array();
        foreach ($mrr_rate_sql as $row) {
      	    $mrr_rate_arr[$row[csf("prod_id")]] = $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
        }
        unset($mrr_rate_sql);

        if ($db_type == 0)
        {
	      	$sql = "select a.id, a.company_id,b.receive_basis, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, b.mst_id, b.transaction_type,d.pay_mode, group_concat(b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount
	      	from product_details_master a, inv_transaction b left join wo_yarn_dyeing_mst d on b.pi_wo_batch_no=d.id
	      	where a.id=b.prod_id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(1,4) $company_cond $search_cond  $rcv_date_cond
	      	group by a.id, a.company_id,b.receive_basis, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, b.mst_id, b.transaction_type,d.pay_mode
	      	union all
	      	select a.id, a.company_id,b.receive_basis,a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, b.mst_id, b.transaction_type,d.pay_mode, group_concat(b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount
	      	from product_details_master a, inv_transaction b left join wo_yarn_dyeing_mst d on b.pi_wo_batch_no=d.id, inv_item_transfer_mst c
	      	where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(5) and c.transfer_criteria=1 $company_cond $search_cond  $rcv_date_cond
	      	group by a.id, a.company_id,b.receive_basis, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, b.mst_id, b.transaction_type,d.pay_mode";
        }
        else
        {
	      $sql = "select a.id, a.company_id,b.receive_basis, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, b.mst_id, b.transaction_type,d.pay_mode, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount
	      	from product_details_master a, inv_transaction b left join wo_yarn_dyeing_mst d on b.pi_wo_batch_no=d.id
	      	where a.id=b.prod_id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(1,4) $company_cond $search_cond  $rcv_date_cond
	      	group by a.id, a.company_id,b.receive_basis, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, b.mst_id, b.transaction_type,d.pay_mode
	      	union all
	      	select a.id, a.company_id,b.receive_basis, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, b.mst_id, b.transaction_type,d.pay_mode,listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount
	      	from product_details_master a, inv_transaction b left join wo_yarn_dyeing_mst d on b.pi_wo_batch_no=d.id, inv_item_transfer_mst c
	      	where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type in(5) and c.transfer_criteria=1 $company_cond $search_cond  $rcv_date_cond
	      	group by a.id, a.company_id,b.receive_basis, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit, b.mst_id, b.transaction_type,d.pay_mode";
        }
        // echo $sql;
        $result = sql_select($sql);
        $i = 1;
        //ob_start();
        ?>
        <div>
            <table width="1680" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px"  rules="all" id="table_header_1" >
                <thead>
                    <tr class="form_caption" style="border:none;">
                        <td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" ><?php echo $_SESSION['page_title'];?></td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="16" align="center" style="border:none; font-size:14px;">
                            Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
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
            </table>
            <div style="width:1680px; overflow-y:scroll; max-height:350px" id="scroll_body" >
                <table width="1660" border="1" cellpadding="2" style="font:'Arial Narrow';"  cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
                $tot_stock_value = 0;
                foreach ($result as $row)
                {
                    if($row[csf("yarn_comp_percent1st")]==100)
					{
                        $compositionDetails = $composition[$row[csf("yarn_comp_type1st")]]  . " \n";
                    }
					else
					{
                        $compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
                    }
                    if ($row[csf("yarn_comp_type2nd")] != 0)
					{
                        if($row[csf("yarn_comp_percent2nd")]==100)
						{
                            $compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] ;
                        }
						else
						{
                            $compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
                        }
                    }

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
    
                    if ($value_with == 1)
                    {
                        if (number_format($stockInHand, 2) > 0.00) {
    
                            if (!in_array($check_string, $checkArr)) {
                                $checkArr[$i] = $check_string;
                                if ($i > 1) {
                                    ?>
                                    <tr bgcolor="#CCCCCC" style="font-weight:bold">
                                        <td colspan="9" align="right">Sub Total</td>
                                        <td width="100" align="right"><? echo number_format($sub_stock_in_hand, 2); ?></td>
                                        <td width="90" align="right">&nbsp;</td>
                                        <!--<td width="110" align="right"><? echo number_format($sub_stock_value, 2); ?></td>-->
                                        <td width="100" align="right"><? echo number_format($sub_stock_value_usd, 2); ?></td>
                                        <td width="120" align="right">&nbsp;</td>
                                        <td width="80" align="right">&nbsp;</td>
                                        <td width="50" align="right">&nbsp;</td>
                                        <td >&nbsp;</td>
                                        <td width="" align="right">&nbsp;</td>
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
                                    <? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
                                        <a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
                                    <? }else{
                                        echo $row[csf("lot")];
                                    }
                                    ?>
                                &nbsp;</p></td>
                                <td width="80" style="word-wrap:break-word; word-break: break-all;" title="<? echo "transaction Id=" . $row[csf("trans_id")] . "Receive Qnty=" . $totalRcv . "Issue Qnty=" . $totalIssue; ?>">
                                    <?
                                    if($receive_basis==2) // work order basis
                                    {													
                                        if($pay_mode==3 || $pay_mode==5)
                                        {
                                            echo $companyArr[$row[csf("supplier_id")]];
                                        }else{
                                            echo $supplierArr[$row[csf("supplier_id")]];
                                        }
                                    }else{
                                        echo $supplierArr[$row[csf("supplier_id")]];
                                    }
                                    ?>
                                </td>
                                <td width="100" style="word-wrap:break-word; word-break: break-all;" align="right"><a href='##' onclick="openmypage_stock('<? echo $row[csf('id')] . "," . $row[csf('mst_id')] . "," . $row[csf('transaction_type')]; ?>', 'stock_popup_mrr')"><? echo number_format($stockInHand, 4); ?></a></td>
                                <td width="90"  style="word-wrap:break-word; word-break: break-all;" align="right" title="<? echo $row[csf("cons_amount")] . "/" . $row[csf("cons_quantity")] . "=" . $avg_rate . "=" . $exchange_rate; ?>"><? echo number_format($avg_rate_usd, 4); ?></td>
                                <td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stock_value_usd, 2); ?></td>
                                <td width="120" align="center" title="<? echo "transaction type =" . $row[csf("transaction_type")]; ?>"><p><? echo $mrr_number; ?>&nbsp;</p></td>
                                <td width="80" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo change_date_format($mrr_date); ?></td>
                                <td width="50" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo $ageOfDays; ?></td>
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
                                        <!--<td width="110" align="right"><? echo number_format($sub_stock_value, 2); ?></td>-->
                                        <td width="100" align="right"><? echo number_format($sub_stock_value_usd, 2); ?></td>
                                        <td width="120" align="right">&nbsp;</td>
                                        <td width="80" align="right">&nbsp;</td>
                                        <td width="50" align="right">&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td width="" align="right">&nbsp;</td>
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
                                <td width="150"  style="word-wrap:break-word; word-break: break-all;"><? $compositionDetails;
            ?></td>
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
                                <td width="80" style="word-wrap:break-word; word-break: break-all;" title="<? echo "transaction Id=" . $row[csf("trans_id")] . "Receive Qnty=" . $totalRcv . "Issue Qnty=" . $totalIssue; ?>">
                                    <?
                                    if($receive_basis==2) // work order basis
                                    {													
                                        if($pay_mode==3 || $pay_mode==5)
                                        {
                                            echo $companyArr[$row[csf("supplier_id")]];
                                        }else{
                                            echo $supplierArr[$row[csf("supplier_id")]];
                                        }
                                    }else{
                                        echo $supplierArr[$row[csf("supplier_id")]];
                                    }
                                    ?>
                                </td>
                                <td width="100" align="right"><a href='##' onclick="openmypage_stock('<? echo $row[csf('id')] . "," . $row[csf('mst_id')] . "," . $row[csf('transaction_type')]; ?>', 'stock_popup_mrr')"><? echo number_format($stockInHand, 2); ?></a></td>
                                <td width="90" align="right"><p><? echo number_format($avg_rate_usd, 4); ?></p></td>
                                <td width="100" align="right"><? echo number_format($stock_value_usd, 2); ?></td>
                                <td width="120" align="center" title="<? echo "transaction type =" . $row[csf("transaction_type")]; ?>"><p><? echo $mrr_number; ?>&nbsp;</p></td>
                                <td width="80" align="center"><p><? echo change_date_format($mrr_date); ?>&nbsp;</p></td>
                                <td width="50" align="center"><? echo $ageOfDays; ?></td>
                                <? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != ""){ ?>
                                    <td width="140" align="left"><span class="comment more"><? echo $yarnTestQalityCommentsArr[$row[csf("id")]]; ?></span></td>
                                <?  }else{ ?>
                                    <td width="140" align="left"><span class="comment more"><? echo "&nbsp;"; ?></span></td>
                                <? 	} ?>
                                <td width="" align="right">
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
                <!--<td width="110" align="right"><echo number_format($sub_stock_value,2); ?></td>-->
                    <td width="100" align="right"><? echo number_format($sub_stock_value_usd, 2); ?></td>
                    <td width="120" align="right">&nbsp;</td>
                    <td width="80" align="right">&nbsp;</td>
                    <td width="50" align="right">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">&nbsp;</td>
                </tr>
            </table>
        </div>
        <table width="1520" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
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
                <!--<td width="110" align="right"><? echo number_format($tot_stock_value, 2); ?></td>-->
                <td width="100" align="right"><? echo number_format($grand_total_stock_value_usd, 2); ?></td>
                <td width="120" align="right">&nbsp;</td>
                <td width="80" align="right">&nbsp;</td>
                <td width="50" align="right">&nbsp;</td>
                <td >&nbsp;</td>
                <td width="" align="right">&nbsp;</td>
            </tr>
        </table>
        </div>
		<?
    }
    else
    {
		$search_cond = "";
		$search_cond_transfer = "";
		if ($cbo_dyed_type >0)
		{
			if ($cbo_dyed_type==2)
			{
				$search_cond .= " and a.dyed_type in (0,2)";
			}
			else
			{
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
			//$search_cond .= " and a.product_name_details like '%" . trim($txt_composition) . "%'";
			//$search_cond .= " and (a.yarn_comp_type1st = " .$txt_composition_id . " or a.yarn_comp_type2nd = " .$txt_composition_id .") " ;
			$search_cond .= " and a.yarn_comp_type1st in (" .$txt_composition_id .")";
		}
		
		//for yarn source
		$yarn_source_cond = '';
		if($cbo_yarn_source != 0)
		{
			$yarn_source_cond = " and c.source = ".$cbo_yarn_source;
		}
		//end for yarn source

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
		//echo $store_wise;die;
		
		if ($store_wise == 1)
		{
			if ($store_name == 0)
				$store_cond .= "";
			else
				$store_cond .= " and a.store_id = $store_name";
			$table_width = '2900' + $value_width;
			$colspan = '28' + $span;

			if ($db_type == 0)
			$select_field = "group_concat(distinct(a.store_id))";
			else if ($db_type == 2)
			$select_field = "RTRIM(XMLAGG(XMLELEMENT(e,a.store_id,',').EXTRACT('//text()') ORDER BY a.store_id).GETCLOBVAL(),',')";//"listagg(a.store_id,',') within group (order by a.store_id)";

			$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		}
		else
		{
			$select_field = "0";
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
		//$allocated_qty_variable_settings=0;

		$receive_array = array();
		$sql_receive = "Select a.prod_id,a.receive_basis,d.pay_mode, $select_field as store_id, max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,
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
		from inv_transaction a left join wo_yarn_dyeing_mst d on a.pi_wo_batch_no=d.id, inv_receive_master c where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $store_cond $yarn_source_cond group by a.prod_id,a.receive_basis,d.pay_mode

		union all  

		Select a.prod_id,a.receive_basis,0 as pay_mode, $select_field as store_id,max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone, 0 as rcv_total_opening,0 as rcv_total_opening_amt, 0 as rcv_total_opening_rate, 0 as purchase, 0 as purchase_amt,0 as rcv_loan, 0 as rcv_loan_amt,0 as rcv_inside_return,0 as rcv_inside_return_amt,0 as rcv_outside_return,0 as rcv_outside_return_amt,sum(case when a.transaction_type in (1,4) and a.receive_basis=30 and a.transaction_date<= '". $to_date . "' then a.cons_quantity else 0 end) as rcv_adjustment_qty,sum(case when a.transaction_type in (1,4) and a.receive_basis=30 and a.transaction_date<= '" . $to_date . "' then a.cons_amount else 0 end) as rcv_adjustment_amt from inv_transaction a where a.receive_basis=30 and a.item_category=1 and a.transaction_type in (1,4) and a.status_active=1 and a.is_deleted=0 $company_cond $store_cond group by a.prod_id,a.receive_basis";

		// echo $sql_receive;die;
		//$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
		//$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;

		$result_sql_receive = sql_select($sql_receive);
		foreach ($result_sql_receive as $row)
		{
			$receive_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
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

			if($row[csf("weight_per_bag")]!="" && $row[csf("weight_per_bag")]>0)
			{
				$receive_array[$row[csf("prod_id")]]['weight_per_bag'] = $row[csf("weight_per_bag")];
			}

			if( $row[csf("weight_per_cone")]!="" && $row[csf("weight_per_cone")]>0)
			{
				$receive_array[$row[csf("prod_id")]]['weight_per_cone'] = $row[csf("weight_per_cone")];
			}

			$receive_array[$row[csf("prod_id")]]['rcv_total_opening_rate'] = $row[csf("rcv_total_opening_rate")];
			$receive_array[$row[csf("prod_id")]]['rcv_adjustment_qty'] += $row[csf("rcv_adjustment_qty")];
			$receive_array[$row[csf("prod_id")]]['rcv_adjustment_amt'] += $row[csf("rcv_adjustment_amt")];
		}

		unset($result_sql_receive);

		$issue_array = array();
		$sql_issue = "select a.prod_id, $select_field as store_id,
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
		where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond group by a.prod_id

		union all 

		select a.prod_id, $select_field as store_id,
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
		where a.item_category=1 and a.status_active=1 and a.is_deleted=0 $store_cond group by a.prod_id
		";
		$result_sql_issue = sql_select($sql_issue);
		foreach ($result_sql_issue as $row)
		{
			$issue_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
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
	
		if ($store_wise == 1)
		{
			$trans_criteria_cond = "";
		}
		else
		{
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
		//LISTAGG(CAST( a.lc_sc_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_id) as lc_id
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
			<style type="text/css">
				.wrap_break {
					word-wrap: break-word;
					word-break: break-all;
				}
			</style>
			<?
			$date_array = array();
			$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 group by prod_id";
			$result_returnRes_date = sql_select($returnRes_date);
			foreach ($result_returnRes_date as $row)
			{
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
			}

			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit 
 				from product_details_master a, inv_material_allocation_dtls b where  b.item_id=a.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
			//echo $sql;die;
			$result = sql_select($sql);
			$i = 1;
			//ob_start();
			?>
			<div>
				<table width="<? echo $table_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<tr class="form_caption" style="border:none;">
						<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold" ><?php echo $_SESSION['page_title'];?></td>
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
				<div style="width:<? echo $table_width + 38; ?>px; overflow-y:scroll; max-height:350px" id="scroll_body" >
					<table style="width:<? echo $table_width +20; ?>px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$tot_stock_value = 0;
						foreach ($result as $row)
						{
							$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
							$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));
							if($row[csf("yarn_comp_percent1st")]==100)
							{
								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . "\n";
							}
							else
							{
								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
							}
							
							if ($row[csf("yarn_comp_type2nd")] != 0)
							{
								if($row[csf("yarn_comp_percent2nd")]==100)
								{
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . "";
								}
								else
								{
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
								}
							}
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
													<td width="100" align="right"><? echo number_format($total_stock_in_hand, 2); ?></td>
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
											<td width="100" class="wrap_break" style="mso-number-format:'\@';"><p>
												<? if( $yarnTestQalityCommentsArr[$row[csf("id")]] != "") { ?>
													<a href='##' onclick="show_test_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?> </a>
												<? }else if( $yarnTestArr[$row[csf("id")]] != "") { ?>
													<a href='##' onclick="show_test_report2('<? echo $row[csf('company_id')]?>','<? echo $row[csf('id')] ?>')"> <? echo $row[csf("lot")]; ?></a>
												<? } else { echo $row[csf("lot")]; } ?>
											&nbsp;</p></td>
											<td width="100" class="wrap_break" >
												<?
												if($receive_basis==2) // work order basis
												{													
													if($pay_mode==3 || $pay_mode==5)
													{
														echo $companyArr[$row[csf("supplier_id")]];
													}else{
														echo $supplierArr[$row[csf("supplier_id")]];
													}
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}
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
											<td width="100" align="right" class="wrap_break"><? echo number_format($stockInHand, 2); ?></td>
											<?
											$stock_value = 0;
											if ($show_val_column == 1)
											{
											    //echo $avz_rates_usd;die;
												$stock_value = $stockInHand * $row[csf("avg_rate_per_unit")];
												$stock_value_usd = ($stockInHand * $row[csf("avg_rate_per_unit")]) / $exchange_rate;

												$avz_rates_usd=0;
												if(number_format($stock_value_usd,2)>0 && number_format($stockInHand,2)>0) $avz_rates_usd=$stock_value_usd/$stockInHand;

												echo '<td width="90" align="right" class="wrap_break">' . number_format($row[csf("avg_rate_per_unit")], 2) . '</td>';
												echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
												echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
												echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
											}

											if ($store_wise == 1)
											{
												$store_name = '';
												$receive_store_id = explode(",", $receive_array[$row[csf("id")]]['store_id']);
												$issue_store_id = explode(",", $issue_array[$row[csf("id")]]['store_id']);
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
												if ($allocated_qty_variable_settings == 1)
												{
													echo "<td width='100' align='right' class='wrap_break'><a href='##' onclick=\"openmypage('" . $row[csf('id')] . "','allocation_popup')\">" . number_format($row[csf("allocated_qnty")], 2) . "</a></td>";
													echo '<td width="100" align="right" class="wrap_break">' . number_format($row[csf("available_qnty")], 2) . '</td>';
												}
												else
												{
													echo '<td width="100" align="right">&nbsp;  </td>';
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
												<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 2); ?></td>
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
											if($receive_basis==2) // work order basis
											{													
												if($pay_mode==3 || $pay_mode==5)
												{
													echo $companyArr[$row[csf("supplier_id")]];
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}
											}else{
												echo $supplierArr[$row[csf("supplier_id")]];
											}
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
										<td width="100" align="right" class="wrap_break"><? echo number_format($stockInHand, 2); ?></td>
										<?
										$stock_value = 0;
										if ($show_val_column == 1) {
											$stock_value = $stockInHand * $row[csf("avg_rate_per_unit")];
											$stock_value_usd = ($stockInHand * $row[csf("avg_rate_per_unit")]) / $exchange_rate;
											$avz_rates_usd=0;
											if(number_format($stock_value_usd,2)>0 && number_format($stockInHand,2)>0) $avz_rates_usd=$stock_value_usd/$stockInHand;
											echo '<td width="90" align="right" class="wrap_break">' . number_format($row[csf("avg_rate_per_unit")], 2) . '</td>';
											echo '<td width="110" align="right" class="wrap_break">' . number_format($stock_value, 2) . '</td>';
											echo '<td width="100" align="right" class="wrap_break">' . number_format($avz_rates_usd, 4) . '</td>';
											echo '<td width="100" align="right" class="wrap_break">' . number_format($stock_value_usd, 2) . '</td>';
										}

										if ($store_wise == 1) {
											$store_name = '';
											$receive_store_id = explode(",", $receive_array[$row[csf("id")]]['store_id']);
											$issue_store_id = explode(",", $issue_array[$row[csf("id")]]['store_id']);
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
												echo '<td width="100" align="right" class="wrap_break">&nbsp; </td>';
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
                        	<td width="100" class="wrap_break" align="right"><? echo number_format($total_stock_in_hand, 2); ?></td>
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
                    </table>
                </div>
                <table style="width:<? echo $table_width +20; ?>px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
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
                		<td width="100" align="right" class="wrap_break" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 2); ?></td>
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
				<div style="width:720px; overflow-y:scroll; max-height:350px" id="scroll_body" >
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
				<div style="width:720px; overflow-y:scroll; max-height:350px" id="scroll_body" >
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
				<div style="width:820px; overflow-y:scroll; max-height:350px" id="scroll_body" >
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
											$fullCompositionValue = $composition[$comp_1type] ;
											if($comp_1per!=100){
												$fullCompositionValue.=',' . $comp_1per . '%  ' . $composition[$comp_2type];
											}
											if($comp_2per!=100){
												$fullCompositionValue.=',' . $comp_2per . '%';
											}
											   
										} else {
											if($comp_1per==100){
												$fullCompositionValue = $composition[$comp_1type] ;
											}else{
												$fullCompositionValue = $composition[$comp_1type] . ',' . $comp_1per . '% ';
											}
											
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
	    //type_5_and_type10_start
		else if ($type == 5)
		{
			$mrr_rate_sql = sql_select("select prod_id, min(transaction_date) as min_date, max(transaction_date), sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction
				where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) group by prod_id");
			$mrr_rate_arr = array();
			foreach ($mrr_rate_sql as $row) {
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
				$mrr_rate_arr[$row[csf("prod_id")]] = $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
			}

			unset($mrr_rate_sql);

			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0  $company_cond $search_cond
			group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";

				//echo $sql;die;//echo count($result);
			$result = sql_select($sql);
				//echo count($result); die;
				// For Yarn Test //
			//$yarnTestQalityCommentsArr = return_library_array("select prod_id, yarn_quality_coments from inv_yarn_test_mst where  status_active=1 and is_deleted=0", 'prod_id', 'yarn_quality_coments');
			$yarnTestQalityCommentsArr = return_library_array("select a.prod_id as prod_id, b.comments_author as comments_author from inv_yarn_test_mst a, inv_yarn_test_comments b where a.id=b.mst_table_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.comments_author IS NOT NULL", 'prod_id', 'comments_author');
			//print_r($yarnTestQalityCommentsArr);

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
			<style type="text/css">
				table tr th, table tr td{word-wrap: break-word;word-break: break-all;}
			</style>

			<div>
				<table width="<? echo $tblWidth;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold" ><?php echo $_SESSION['page_title'];?></td>
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
				</table>
				<div style="width:1670px; overflow-y:scroll; max-height:220px" id="scroll_body" >
					<table width="<?php echo $tblWidth; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$grand_total_alocatted=0;$grand_total_free_stock=0;
						$tot_stock_value = 0;
						foreach ($result as $row)
						{
							$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
							if($row[csf("yarn_comp_percent1st")]==100){
								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . "\n";
							}else{
								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
							}
							
							if ($row[csf("yarn_comp_type2nd")] != 0)
							{
								
								if( $row[csf("yarn_comp_percent2nd")]==100){
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] ;
								}else{
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
								}	
							}
							$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
							$transfer_in_amt = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
							$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
							$transfer_out_amt = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];

							$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
							$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];
							$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];
							$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];

							$pay_mode = $receive_array[$row[csf("id")]]['pay_mode']."test";

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
		                            //$avg_rate=$tot_rcv_amt/$tot_rcv_qnty;
							$avg_rate = $mrr_rate_arr[$row[csf("id")]];

		                            //subtotal and group-----------------------
							$check_string = $row[csf("yarn_count_id")] . $compositionDetails . $row[csf("yarn_type")];

							if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0)) {




								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
		                                //number_format($stockInHand,2)
								if ($value_with == 1)
								{
									if (number_format($stockInHand, 2) > 0.00)
									{
										if (!in_array($check_string, $checkArr)) {
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
		                                          	<td width="120"><p><? echo $companyArr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
		                                          	<td width="60"><? echo $row[csf("id")]; ?></td>
		                                          	<td width="60"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?>&nbsp;</p></td>
		                                          	<td width="150"><p><? echo $compositionDetails; ?>&nbsp;</p></td>
		                                          	<td width="100"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
		                                          	<td width="80"><p><? echo $color_name_arr[$row[csf("color")]]; ?>&nbsp;</p></td>
		                                          	<td width="100"> <p>
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
		                                          		?>
		                                          	&nbsp;</p> </td>
		                                          	<td width="80">
		                                          		<p>
		                                          		<?
														if($receive_basis==2) // work order basis
														{													
															if($pay_mode==3 || $pay_mode==5)
															{
																echo $companyArr[$row[csf("supplier_id")]];
															}else{
																echo $supplierArr[$row[csf("supplier_id")]];
															}
														}else{
															echo $supplierArr[$row[csf("supplier_id")]];
														}
														?>
		                                          		&nbsp;</p>
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
											if($receive_basis==2) // work order basis
											{													
												if($pay_mode==3 || $pay_mode==5)
												{
													echo $companyArr[$row[csf("supplier_id")]];
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}
											}else{
												echo $supplierArr[$row[csf("supplier_id")]];
											}
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
						</table>
					</div>
					<table width="<?php echo $tblWidth; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
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
									<td> <? echo $yarn_type_tot[$yarn_type_id]; ?></td>
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


			$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";

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
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold" ><?php echo $_SESSION['page_title'];?> </td>
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
				<div style="width:<? echo $table_width + 400 + 20; ?>px; overflow-y:scroll; max-height:350px" id="scroll_body" >
					<table width="<? echo $table_width + 400; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$tot_stock_value = 0;
						foreach ($result as $row) {
							$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
							$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

							
							if($row[csf("yarn_comp_percent1st")]==100){
								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . "%\n";
							}else{
								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
							}
							if ($row[csf("yarn_comp_type2nd")] != 0){

								if($row[csf("yarn_comp_percent2nd")]==100){
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] ;
								}else{
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
								}
								
							}
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
									if($receive_basis==2) // work order basis
									{													
										if($pay_mode==3 || $pay_mode==5)
										{
											echo $companyArr[$row[csf("supplier_id")]];
										}else{
											echo $supplierArr[$row[csf("supplier_id")]];
										}
									}else{
										echo $supplierArr[$row[csf("supplier_id")]];
									}
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

								if ($store_wise == 1) {
									$store_name = '';
									$receive_store_id = explode(",", $receive_array[$row[csf("id")]]['store_id']);
									$issue_store_id = explode(",", $issue_array[$row[csf("id")]]['store_id']);
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
			</table>
			</div>
			<table width="<? echo $table_width +400; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
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
		else if ($type == 9) // show 2
		{
			if ($show_val_column == 1) {
				$value_width = 300;
				$span = 3;
				$column = '<th rowspan="2" width="90">Avg. Rate (USD)</th>
				<th rowspan="2" width="100">Stock Value (USD)</th>';
			} else {
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

			$sql = "SELECT a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit
			from product_details_master a
			where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";

			// echo $sql;die;
			//die;//echo count($result);
			$result = sql_select($sql);
			$i = 1;
				//ob_start();
			?>
			<div>
				<table width="<? echo $table_width + 20; ?>" border="1" style="font:'Arial Narrow';" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold" ><?php echo $_SESSION['page_title'];?></td>
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
				<div style="width:<? echo $table_width + 20; ?>px; overflow-y:scroll; max-height:350px" id="scroll_body" >
					<table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
						<?
						$tot_stock_value = 0;
						foreach ($result as $row)
						{
							$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
							$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));
							if($row[csf("yarn_comp_percent1st")]==100){
								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . "\n";
							}else{
								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
							}
							
							if ($row[csf("yarn_comp_type2nd")] != 0){
								if($row[csf("yarn_comp_percent2nd")]==100){
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] ;
								}else{
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
								}
								
							}
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
											if($receive_basis==2) // work order basis
											{													
												if($pay_mode==3 || $pay_mode==5)
												{
													echo $companyArr[$row[csf("supplier_id")]];
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}
											}else{
												echo $supplierArr[$row[csf("supplier_id")]];
											}
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
											<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stockInHand, 2); ?></td>
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
												if ($db_type == 2)
												{
													if(!empty($receive_array[$row[csf("id")]]['store_id']))
													{
														$receive_store_id = $receive_array[$row[csf("id")]]['store_id']->load();
													}

													if(!empty($issue_array[$row[csf("id")]]['store_id']))
													{
														$issue_store_id = $issue_array[$row[csf("id")]]['store_id']->load();
													}
												}
												$receive_store_id_arr = explode(",", $receive_store_id);
												$issue_store_id_arr = explode(",", $issue_store_id);
												// echo "<pre>";print_r($receive_store_id_arr);
												// $receive_store_id = explode(",", $receive_array[$row[csf("id")]]['store_id']);
												// $issue_store_id = explode(",", $issue_array[$row[csf("id")]]['store_id']);
												$store_name = '';
												$store_id = array_unique(array_merge($receive_store_id_arr, $issue_store_id_arr));
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
											if($receive_basis==2) // work order basis
											{													
												if($pay_mode==3 || $pay_mode==5)
												{
													echo $companyArr[$row[csf("supplier_id")]];
												}else{
													echo $supplierArr[$row[csf("supplier_id")]];
												}
											}else{
												echo $supplierArr[$row[csf("supplier_id")]];
											}
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
										<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($stockInHand, 2); ?></td>
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
											$receive_store_id = explode(",", $receive_array[$row[csf("id")]]['store_id']);
											$issue_store_id = explode(",", $issue_array[$row[csf("id")]]['store_id']);
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
							<td width="100" align="right"><? echo number_format($total_stock_in_hand, 2); ?></td>
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
					</table>
				</div>
				<table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
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
						<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 2); ?></td>
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
		else if ( $type == 10)
		{
			$mrr_rate_sql = sql_select("select prod_id, min(transaction_date) as min_date, max(transaction_date), sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction
				where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) group by prod_id");
			$mrr_rate_arr = array();
			foreach ($mrr_rate_sql as $row) {
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
				$mrr_rate_arr[$row[csf("prod_id")]] = $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
			}

			unset($mrr_rate_sql);

			$sql = "select a.id,b.yarn_count, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit
			 from product_details_master a  
			 left join lib_yarn_count b  
    		 on a.yarn_count_id=b.id
			where a.yarn_count_id=b.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0  $company_cond $search_cond
			
			 order by b.yarn_count, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.id";

				//echo $sql;die;//echo count($result);
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
			
				$tblWidth = "1040";
				$colspan = "8";
			
			?>
			<style type="text/css">
				table tr th, table tr td{word-wrap: break-word;word-break: break-all;height: 40px;}
			</style>

			<div style="font-family: Arial Narrow;text-align: center;">
				<table width="<? echo $tblWidth;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
					<thead>
						<?php	if($cbo_company_name!=0){
									$companyDetails=fnc_company_location_address($cbo_company_name);
								}else{
								$companyDetails[0]="";
								$companyDetails[1]="";
								$companyDetails[2]="";
							}

						?>
						
						<tr style="border: none;">
							<th  style="border:none; font-size:14px;" width="10%;" align="left;">
								<?php if(!empty($companyDetails[2])){?>
											 <img src="../../../<?echo $companyDetails[2];?>" height="70" width="110" style="padding: 2px;" align="left">
										<?php }?>
							</th>
							<th  style="border:none; font-size:14px;" align="left" width="10%;">
								
										
											<p  class="" style="font-size:16px; font-weight:bold;margin: 0px;" align="center">
												Daily Yarn Stock Report V2
											</p>
											
											
											<p  style="font-size:14px; font-weight:bold;margin:0px;" align="center">
												 <? echo $companyArr[str_replace("'", "", $cbo_company_name)];
												 	
												  ?>
											</p>

											
											<?php if(!empty($companyDetails[1])){

												
											
												?>
											<p  style="font-size:12px; font-weight:bold;margin: 0px;" align="center">
														Address : <? 
														 echo $companyDetails[1];
														 ?>
													
												</p>
												


											<?php	} ?>
											<p  style="font-size:12px; font-weight:bold;margin: 0px;" align="center">
												<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
											</p>
										
							</th>
							<th width="10%" style="border:none;"></th>
						</tr>
					</thead>
				</table>
				<table width="<? echo $tblWidth;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
							
						<tr  style="word-break:normal;">
							<th rowspan="2" width="40">SL</th>
							<th rowspan="2" width="100">Company<br>Name</th>
							<th colspan="7" width="520">Description</th>
							<th rowspan="2" width="100">Stock In Hand</th>

							<th rowspan="2" width="100">Allocated <br>to Order</th>
							<th rowspan="2" width="100">Un Allocated<br> Qty.</th>
							
							<th rowspan="2" width="50">Age <br>(Days)</th>
							
						</tr>
						<tr>
							
							<th width="50">Count</th>
							<th width="150">Composition</th>
							<th width="60">Yarn Type</th>
							<th width="50">Color</th>
							<th width="50">Lot</th>
							<th width="80">Supplier</th>
							<th width="80">Buyer</th>
						</tr>
					</thead>
				<tbody>
						<?
						$grand_total_alocatted=0;$grand_total_free_stock=0;
						$tot_stock_value = 0;
						foreach ($result as $row)
						{
							$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
							if($row[csf("yarn_comp_percent1st")]==100){
								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . "\n";
							}else{
								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
							}
							
							if ($row[csf("yarn_comp_type2nd")] != 0)
							{
								
								if( $row[csf("yarn_comp_percent2nd")]==100){
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] ;
								}else{
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
								}	
							}
							$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
							$transfer_in_amt = $transfer_qty_array[$row[csf("id")]]['transfer_in_amt'];
							$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
							$transfer_out_amt = $transfer_qty_array[$row[csf("id")]]['transfer_out_amt'];

							$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
							$trans_out_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening_amt'];
							$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];
							$trans_in_total_opening_amt = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening_amt'];

							$pay_mode = $receive_array[$row[csf("id")]]['pay_mode']."test";

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
		                            //$avg_rate=$tot_rcv_amt/$tot_rcv_qnty;
							$avg_rate = $mrr_rate_arr[$row[csf("id")]];

		                            //subtotal and group-----------------------
							$check_string = $row[csf("yarn_count_id")] . $compositionDetails ;

							if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0)) {




								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
		                                //number_format($stockInHand,2)
								if ($value_with == 1)
								{
									if (number_format($stockInHand, 2) > 0.00)
									{
										if (!in_array($check_string, $checkArr)) {
											$checkArr[$i] = $check_string;
											if ($i > 1) {
												?>
												<tr bgcolor="#CCCCCC" style="font-weight:bold">
													<td colspan="9" align="right">Sub Total</td>
													<td align="right"><? echo number_format($total_stock_in_hand, 2); ?></td>
													<td align="right"><? echo number_format($total_alocatted, 2);$total_alocatted = 0; ?></td>
													<td align="right"><? echo number_format($total_free_stock, 2);$total_free_stock = 0; ?></td>
													

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
		                                          	<td width="100"><p><? echo  $companyShortName[$row[csf("company_id")]]; ?>&nbsp;</p></td>
		                                          	
		                                          	<td width="50"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?>&nbsp;</p></td>
		                                          	<td width="150"><p><? echo $compositionDetails; ?>&nbsp;</p></td>
		                                          	<td width="60"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
		                                          	<td width="50"><p><? echo $color_name_arr[$row[csf("color")]]; ?>&nbsp;</p></td>
		                                          	<td width="50"> <p>
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
		                                          		?>
		                                          	&nbsp;</p> </td>
		                                          	<td width="80">
		                                          		<p>
		                                          		<?
														if($receive_basis==2) // work order basis
														{													
															if($pay_mode==3 || $pay_mode==5)
															{
																echo $companyArr[$row[csf("supplier_id")]];
															}else{
																echo $supplierArr[$row[csf("supplier_id")]];
															}
														}else{
															echo $supplierArr[$row[csf("supplier_id")]];
														}
														?>
		                                          		&nbsp;</p>
		                                          	<td width="80">
													  <?
														echo $buy_short_name_arr[$rcv_arr[$row[csf("id")]][$row[csf("lot")]][$row[csf("yarn_type")]][$row[csf("color")]]['buyer_id']];
														?>
													</td>
		                                          	<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" ><a href='##' onclick="openmypage_stock('<? echo $row[csf('id')] . "_" . $store_name ?>', 'stock_popup')"><? echo number_format($stockInHand, 2); ?></a></td>

		                                          	<td width="100" align="right"><p style="word-wrap:break-word; word-break: break-all;" ><a href='##' onclick="openmypage('<? echo $row[csf('id')]; ?>','allocation_popup')"><? echo number_format($row[csf("allocated_qnty")], 2);?></a></p></td>
		                                          	<td width="100" align="right"><p style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($row[csf("available_qnty")], 2) ;?></p></td>
		                                          	

		                                          	<td width="50" align="right"><? echo $ageOfDays; ?></td>
		                                         
		                                          	
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
									  	<td width="100"><p><? echo $companyShortName[$row[csf("company_id")]]; ?>&nbsp;</p></td>
									  	
									  	<td width="50"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?>&nbsp;</p></td>
									  	<td width="150"><p><? echo $compositionDetails; ?>&nbsp;</p></td>
									  	<td width="60"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
									  	<td width="50"><p><? echo $color_name_arr[$row[csf("color")]]; ?>&nbsp;</p></td>
									  	<td width="50"><p style="word-break:break-all;">
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
										if($receive_basis==2) // work order basis
										{													
											if($pay_mode==3 || $pay_mode==5)
											{
												echo $companyArr[$row[csf("supplier_id")]];
											}else{
												echo $supplierArr[$row[csf("supplier_id")]];
											}
										}else{
											echo $supplierArr[$row[csf("supplier_id")]];
										}
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
									  

									  	<td width="50" align="right"><? echo $ageOfDays; ?></td>
									  
									  	
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
								
								
								
								<td align="right">&nbsp;</td>
								
							</tr>
						</tbody>
					</table>
					
					<table width="<?php echo $tblWidth; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">
						<tr class="tbl_bottom">
							
							<td width="40"></td>
							<td width="100"></td>
							<td width="50"></td>
							<td width="150"></td>
							<td width="60"></td>
							<td width="50"></td>
							<td width="50"></td>
							<td width="80"></td>
							
							<td  width="80" align="right">Grand Total</td>
							<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;"  id="value_total_stock_in_hand"><? echo number_format($grand_total_stock_in_hand, 2); ?></td>
							
							<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($grand_total_alocatted, 2); ?></td>
							<td width="100" align="right" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($grand_total_free_stock, 2); ?></td>							
							

							<td width="50" align="right">&nbsp;</td>
							
						</tr>
					</table>
				</div>
				<?
		}
		//type5_and_type10_end
		}
	    ?>
		<script>
		    $(document).ready(function() {
			var showChar = 30;
			//var ellipsestext = "...";
			var ellipsestext = "";
			var moretext = "more";
			var lesstext = "less";
			$('.more').each(function() {

				var content = $(this).html();

				if(content.length > showChar) {

					var c = content.substr(0,showChar);
					var h = content.substr(showChar-1, content.length - showChar);

					var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

					$(this).html(html);
				}

			});

			$(".morelink").click(function(){

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
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("*.xls") as $filename) {
		            //if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		        //---------end------------//
		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$html**$filename";
		exit();
    }

if ($action == "yarn_test_report2")
{
	extract($_REQUEST);
	echo load_html_head_contents("Yarn Test Report", "../../../../", 1, 1, '', '', '');
	list($company_ID, $prod_ID) = explode("*", $data);

	$yarn_test_for_arr = array(1=>'Bulk Yarn',2=>'Sample Yarn');
	$yarn_test_result_arr = array(1=>'Nil',2=>'Major',3=>'Minor');
	$yarn_test_acceptance_arr = array(1=>'Yes',2=>'No');
	$comments_acceptance_arr = array(1=>'Acceptable',2=>'Special',3=>'Consideration',4=>'Not Acceptable');
	$phys_test_knitting_arr = array(1=>'Stripe(Patta)', 2=>'Thick & Thin Yarn', 3=>'Neps', 4=>'Poly-Propaline(Plastic Conta)', 5=>'Color Conta/Yarn', 6=>'Dead Fiber', 7=>'No Of Slub', 8=>'No Of Hole', 9=>'No Of Slub Hole', 10=>'Moisture Efect', 11=>'No Of Yarn Breakage', 12=>'No Of Setup', 13=>'Knotting End', 14=>'Haireness', 15=>'Hand Feel', 16=>'Twisting', 17=>'Contamination', 18=>'Foregin Fiber', 19=>'Oil Stain Yarn', 20=>'Foreign Matters', 21=>'Unlevel', 22=>'Double Yarn', 23=>'Fiber Migration', 24=>'Excessive Hard Yarn');
	//asort($phys_test_knitting_arr);
	$phys_test_dyeing_and_finishing_arr = array(1=>'Stripe(Patta)', 2=>'Thick & Thin Yarn', 3=>'Neps', 4=>'Color Conta', 5=>'Dead Fiber/Cotton', 6=>'No Of Slub', 7=>'No Of Hole', 8=>'No Of Slub Hole', 9=>'Moisture Efect', 10=>'Shrinkage', 11=>'Dye Pick Up%', 12=>'Enzyme Dosting %', 13=>'Knotting End', 14=>'Haireness', 15=>'Hand Feel', 16=>'Contamination', 17=>'Soft Yarn/Loose Yarn', 18=>'Oil Stain Yarn', 19=>'Bad Piecing', 20=>'Oily Slub', 21=>'Foreign Matters');
	//asort($phys_test_dyeing_and_finishing_arr);
	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
    $product_name_details = return_field_value("product_name_details","product_details_master","id=$prod_ID");
    $lot_number = return_field_value("lot","product_details_master","id=$prod_ID");
	?>

	<style type="text/css">
        /* .a4size {
           width: 21cm;
           height: 26.7cm;
           font-family: Cambria, Georgia, serif;
        } */
     /*   @media print {
               table {border-spacing: 0px; padding: 0px;}
				td th{padding: 0px;}
        size: A4 portrait;
        }*/
        .rpt_table2 td{border: 1px solid #8bAF00;}
    </style>
	<table style="width: 1300px;" align="center">
	    <tr><td align="center" style="font-size:xx-large"><strong><? echo $company_arr[$company_ID]; ?></strong></td></tr>
	    <tr>
	    	<td align="center" style="font-size: 16px;">
	    		<?
				$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$company_ID");
				foreach ($nameArray as $result)
				{
					?>
					<? echo $result[csf('plot_no')]; ?>
					<? echo $result[csf('level_no')]; ?>
					<? echo $result[csf('road_no')]; ?>
					<? echo $result[csf('block_no')];?>
					<? echo $result[csf('city')]; ?>
					<? echo $result[csf('zip_code')]; ?>
					<? echo $result[csf('province')]; ?>
					<? echo $country_arr[$result[csf('country_id')]]; ?><br>
					<? echo $result[csf('email')]; ?>
					<? echo $result[csf('website')];
				}
				?>
	    	</td>
	    </tr>
	    <tr><td align="center" style="font-size: 25px;">Assessment of Numerical Test &amp; Physical Inspection Report</td></tr>
	    <tr><td align="center" style="font-size: 25px;">Yarn Test Report</td></tr>
	</table>
	<br/>

	<div style="font-size: 25px; margin-left: 5px;" title='Product ID=<? echo $prod_ID ;?> and Lot Number=<? echo $lot_number ;?>'><strong>Product Details: <? echo $prod_ID.', '.$lot_number.', '.$product_name_details; ?></strong></div><br>
	<div style="margin-left: 5px;">
		<?
		$sql_mst_comments = "select a.id, a.company_id, a.prod_id, a.lot_number, a.test_date, a.test_for, a.specimen_wgt, a.specimen_length, a.color, a.receive_qty, a.lc_number, a.lc_qty, a.actual_yarn_count, a.actual_yarn_count_phy, a.yarn_apperance_grad, a.yarn_apperance_phy, a.twist_per_inc, a.twist_per_inc_phy, a.moisture_content, a.moisture_content_phy, a.ipi_value, a.ipi_value_phy, a.csp_minimum, a.csp_minimum_phy, a.csp_actual, a.csp_actual_phy, a.thin_yarn, a.thin_yarn_phy, a.thick, a.thick_phy, a.u, a.u_phy, a.cv, a.cv_phy, a.neps_per_km, a.neps_per_km_phy, a.heariness, a.heariness_phy, a.counts_cv, a.system_result, a.counts_cv_phy, a.grey_gsm, a.grey_wash_gsm, a.required_gsm, a.required_dia, a.machine_dia, a.stich_length, a.grey_gsm_dye, a.batch, a.finish_gsm, a.finish_dia, a.length, a.width, b.comments_knit_acceptance, b.comments_knit, b.comments_dye_acceptance, b.comments_dye, b.comments_author_acceptance, b.comments_author from inv_yarn_test_mst a, inv_yarn_test_comments b where a.id=b.mst_table_id and a.prod_id=$prod_ID and a.company_id=$company_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id desc";
		$sql_mst_comments_rslt = sql_select($sql_mst_comments);
		$yarn_info_arr = array();

		foreach ($sql_mst_comments_rslt as $value)
		{
			$attribute = array('id', 'company_id', 'prod_id', 'lot_number', 'test_date', 'test_for', 'specimen_wgt', 'specimen_length', 'color', 'receive_qty', 'lc_number', 'lc_qty', 'actual_yarn_count', 'actual_yarn_count_phy', 'yarn_apperance_grad', 'yarn_apperance_phy', 'twist_per_inc', 'twist_per_inc_phy', 'moisture_content', 'moisture_content_phy', 'ipi_value', 'ipi_value_phy', 'csp_minimum', 'csp_minimum_phy', 'csp_actual', 'csp_actual_phy', 'thin_yarn', 'thin_yarn_phy', 'thick', 'thick_phy', 'u', 'u_phy', 'cv', 'cv_phy', 'neps_per_km', 'neps_per_km_phy', 'heariness', 'heariness_phy', 'counts_cv', 'system_result', 'counts_cv_phy');

			foreach ($attribute as $attr)
			{
				$yarn_info_arr[$value[csf('id')]][$attr] = $value[csf($attr)];
			}
		}

		$sql_dtls = "select a.id, a.color, a.grey_gsm, a.grey_wash_gsm, a.required_gsm, a.required_dia, a.machine_dia, a.stich_length, a.grey_gsm_dye, a.batch, a.finish_gsm, a.finish_dia, a.length, a.width, b.id as dtls_id, b.testing_parameters_id, b.fab_type, b.testing_parameters, b.fabric_point, b.result, b.acceptance, b.fabric_class, b.remarks from inv_yarn_test_mst a, inv_yarn_test_dtls b where a.id=b.mst_id and a.prod_id=$prod_ID and a.company_id=$company_ID and b.fab_type in(1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.color, b.testing_parameters_id";
		$sql_dtls_result = sql_select($sql_dtls);
		$color_range_arr = array();
		$knit_mstdata_arr=array();
		$color_knit_dtls_arr=array();
		$color_dye_dtls_arr=array();
		$dtls_data_arr = array();
		foreach ($sql_dtls_result as $value)
		{
			$color_range_arr[$value[csf('id')]]['color'] = $value[csf('color')];
			$attribute_mst = array('grey_gsm', 'grey_wash_gsm', 'required_gsm', 'required_dia', 'machine_dia', 'stich_length', 'grey_gsm_dye', 'batch', 'finish_gsm', 'finish_dia', 'length', 'width');
			foreach ($attribute_mst as $attr)
			{
				$knit_mstdata_arr[$value[csf('id')]][$attr] = $value[csf($attr)];
			}

			$color_knit_dtls_arr[$value[csf('fab_type')]][$value[csf('id')]]['dtls_id'] .= $value[csf('dtls_id')].',';
			$color_dye_dtls_arr[$value[csf('fab_type')]][$value[csf('id')]]['dtls_id'] .= $value[csf('dtls_id')].',';

			//$dtls_data_arr[$value[csf('dtls_id')]]['testing_parameters']=$value[csf('testing_parameters')];
			$dtls_data_arr[$value[csf('dtls_id')]]['testing_parameters_id']=$value[csf('testing_parameters_id')];
			$dtls_data_arr[$value[csf('dtls_id')]]['fabric_point']=$value[csf('fabric_point')];
			$dtls_data_arr[$value[csf('dtls_id')]]['result']=$value[csf('result')];
			$dtls_data_arr[$value[csf('dtls_id')]]['acceptance']=$value[csf('acceptance')];
			$dtls_data_arr[$value[csf('dtls_id')]]['fabric_class']=$value[csf('fabric_class')];
			$dtls_data_arr[$value[csf('dtls_id')]]['remarks']=$value[csf('remarks')];
		}
		?>
	    <table cellspacing="1" cellpadding="1" class="rpt_table2" rules="all" style="border: 2px solid black;">
	        <caption style="background-color: #dbb768; font-weight: bold; text-align: left; border-top: 2px solid black; border-right: 2px solid black; border-left: 2px solid black;">Basic Yarn Information</caption>
	        <tr>
	            <td width="50"><b>1</b></td>
	            <td width="150"><b>Color Range</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><b><? echo $color_range[$value['color']]; ?></b></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
	            <td width="50"><b>2</b></td>
	            <td width="150"><b>Test For</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><? echo $yarn_test_for_arr[$value['test_for']]; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
	            <td width="50"><b>3</b></td>
	            <td width="150"><b>Specimen Weight</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><? if($value['specimen_wgt']==0) echo ''; else echo $value['specimen_wgt']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
	            <td width="50"><b>4</b></td>
	            <td width="150"><b>Specimen Length</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><? if($value['specimen_length']==0) echo ''; else echo $value['specimen_length']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
	            <td width="50"><b>5</b></td>
	            <td width="150"><b>Receive Quantity</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><? if($value['receive_qty']==0) echo ''; else echo $value['receive_qty']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
	            <td width="50"><b>6</b></td>
	            <td width="150"><b>LC Quantity</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><? if($value['lc_qty']==0) echo ''; else echo $value['lc_qty']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
	            <td width="50"><b>7</b></td>
	            <td width="150"><b>LC Number</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><? echo $value['lc_number']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
	            <td width="50"><b>8</b></td>
	            <td width="150"><b>Test Date</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="121" style="border-left: 2px solid black;"><? echo $value['test_date']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	    </table>
	    <br>
	    <table cellspacing="1" cellpadding="1" class="rpt_table2" rules="all" style="border: 2px solid black;">
	        <caption style="background-color: #dbb768; font-weight: bold; text-align: left; border-top: 2px solid black; border-right: 2px solid black; border-left: 2px solid black;">Numerical Test</caption>
	        <tr style="background-color: #ddb;">
	        	<td colspan="2"></td>
	        	<?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	        		<td style="border-left: 2px solid black;"><b>Require</b></td>
	        		<td><b>Physical</b></td>
	        		<?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>1</b></td>
	            <td width="150"><b>Actual Yarn Count</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['actual_yarn_count']==0) echo ''; else echo $value['actual_yarn_count']; ?></td>
	                    <td width="60"><? if($value['actual_yarn_count_phy']==0) echo ''; else echo $value['actual_yarn_count_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>2</b></td>
	            <td width="150"><b>Yarn Apperance (Grade)</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['yarn_apperance_grad']==0) echo ''; else echo $value['yarn_apperance_grad']; ?></td>
	                    <td width="60"><? if($value['yarn_apperance_phy']==0) echo ''; else echo $value['yarn_apperance_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>3</b></td>
	            <td width="150"><b>Twist Per Inch (TPI)</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['twist_per_inc']==0) echo ''; else echo $value['twist_per_inc']; ?></td>
	                    <td width="60"><? if($value['twist_per_inc_phy']==0) echo ''; else echo $value['twist_per_inc_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>4</b></td>
	            <td width="150"><b>Moisture Content</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['moisture_content']==0) echo ''; else echo $value['moisture_content']; ?></td>
	                    <td width="60"><? if($value['moisture_content_phy']==0) echo ''; else echo $value['moisture_content_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>5</b></td>
	            <td width="150"><b>IPI Value (Uster)</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['ipi_value']==0) echo ''; else echo $value['ipi_value']; ?></td>
	                    <td width="60"><? if($value['ipi_value_phy']==0) echo ''; else echo $value['ipi_value_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>6</b></td>
	            <td width="150"><b>CSP Minimum</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['csp_minimum']==0) echo ''; else echo $value['csp_minimum']; ?></td>
	                    <td width="60"><? if($value['csp_minimum_phy']==0) echo ''; else echo $value['csp_minimum_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>7</b></td>
	            <td width="150"><b>CSP Actua</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['csp_actual']==0) echo ''; else echo $value['csp_actual']; ?></td>
	                    <td width="60"><? if($value['csp_actual_phy']==0) echo ''; else echo $value['csp_actual_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>8</b></td>
	            <td width="150"><b>Thin Yarn</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['thin_yarn']==0) echo ''; else echo $value['thin_yarn']; ?></td>
	                    <td width="60"><? if($value['thin_yarn_phy']==0) echo ''; else echo $value['thin_yarn_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>9</b></td>
	            <td width="150"><b>Thick</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['thick']==0) echo ''; else echo $value['thick']; ?></td>
	                    <td width="60"><? if($value['thick_phy']==0) echo ''; else echo $value['thick_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>10</b></td>
	            <td width="150"><b>U %</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['u']==0) echo ''; else echo $value['u']; ?></td>
	                    <td width="60"><? if($value['u_phy']==0) echo ''; else echo $value['u_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>11</b></td>
	            <td width="150"><b>CV %</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['cv']==0) echo ''; else echo $value['cv']; ?></td>
	                    <td width="60"><? if($value['cv_phy']==0) echo ''; else echo $value['cv_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>12</b></td>
	            <td width="150"><b>Neps Per KM</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['neps_per_km']==0) echo ''; else echo $value['neps_per_km']; ?></td>
	                    <td width="60"><? if($value['neps_per_km_phy']==0) echo ''; else echo $value['neps_per_km_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>13</b></td>
	            <td width="150"><b>Heariness %</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['heariness']==0) echo ''; else echo $value['heariness']; ?></td>
	                    <td width="60"><? if($value['heariness_phy']==0) echo ''; else echo $value['heariness_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>14</b></td>
	            <td width="150"><b>Counts CV %</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td width="60" style="border-left: 2px solid black;"><? if($value['counts_cv']==0) echo ''; else echo $value['counts_cv']; ?></td>
	                    <td width="60"><? if($value['counts_cv_phy']==0) echo ''; else echo $value['counts_cv_phy']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	        <tr>
        		<td width="50"><b>15</b></td>
	            <td width="150"><b>System Result</b></td>
	            <?
	            foreach ($yarn_info_arr as $value)
	            {
	                ?>
	                    <td colspan="2" width="120" style="border-left: 2px solid black;"><? echo $value['system_result']; ?></td>
	                <?
	            }
	            ?>
	        </tr>
	    </table>
	</div>
	<br>
	<div style="margin-left: 5px;">
    	<table>
    		<?
			foreach ($color_range_arr as $mst_id => $color_val)
			{
				$knit_dtls=array_unique(explode(",",rtrim($color_knit_dtls_arr[1][$mst_id]['dtls_id'],',')));
				$dye_dtls=array_unique(explode(",",rtrim($color_dye_dtls_arr[2][$mst_id]['dtls_id'],',')));

				$grey_gsm=$knit_mstdata_arr[$mst_id]['grey_gsm'];
				if($grey_gsm==0) $grey_gsm='';
				$grey_wash_gsm=$knit_mstdata_arr[$mst_id]['grey_wash_gsm'];
				if($grey_wash_gsm==0) $grey_wash_gsm='';
				$required_gsm=$knit_mstdata_arr[$mst_id]['required_gsm'];
				if($required_gsm==0) $required_gsm='';
				$required_dia=$knit_mstdata_arr[$mst_id]['required_dia'];
				if($required_dia==0) $required_dia='';
				$machine_dia=$knit_mstdata_arr[$mst_id]['machine_dia'];
				if($machine_dia==0) $machine_dia='';
				$stich_length=$knit_mstdata_arr[$mst_id]['stich_length'];
				if($stich_length==0) $stich_length='';

				$grey_gsm_dye=$knit_mstdata_arr[$mst_id]['grey_gsm_dye'];
				if($grey_gsm_dye==0) $grey_gsm_dye='';
				$finish_gsm=$knit_mstdata_arr[$mst_id]['finish_gsm'];
				if($finish_gsm==0) $finish_gsm='';
				$finish_dia=$knit_mstdata_arr[$mst_id]['finish_dia'];
				if($finish_dia==0) $finish_dia='';
				$length=$knit_mstdata_arr[$mst_id]['length'];
				if($length==0) $length='';
				$width=$knit_mstdata_arr[$mst_id]['width'];
				if($width==0) $width='';
				?>
	        	<tr>
	        		<td style="vertical-align: top;">
		                <table cellspacing="1" cellpadding="1" class="rpt_table2" rules="all" border="1" style="width: 660px; margin-right: 20px; border: 2px solid black;">
		                    <caption style="background-color: #dbb768; font-weight: bold; border-top: 2px solid black; border-left: 2px solid black; border-right: 2px solid black;">Knitting For <? echo $color_range[$color_val['color']]; ?></caption>
		                    <tr>
		                        <td width="80"><b>Gray GSM</b></td>
		                        <td width="80"><? echo $grey_gsm; ?></td>
		                        <td width="80" rowspan="2"></td>
		                        <td width="95"><b>Gray Wash GSM</b></td>
		                        <td width="80"><? echo $grey_wash_gsm; ?></td>
		                        <td width="80" rowspan="2"></td>
		                        <td width="80"><b>Required GSM</b></td>
		                        <td><? echo $required_gsm; ?></td>
		                    </tr>
		                    <tr>
		                        <td width="80"><b>Required Dia</b></td>
		                        <td width="80"><? echo $required_dia; ?></td>
		                        <td width="95"><b>Machine Dia</b></td>
		                        <td width="80"><? echo $machine_dia; ?></td>
		                        <td width="80"><b>Stich Length</b></td>
		                        <td><? echo $stich_length; ?></td>
		                    </tr>
		                    <tr style="background-color: #ddb;">
		                    	<td width="160" colspan="2"><b>Testing Parameters</b></td>
		                    	<td width="80"><b>Point</b></td>
		                    	<td width="95"><b>Result</b></td>
		                    	<td width="80"><b>Acceptance</b></td>
		                    	<td width="80"><b>Fabric Class</b></td>
		                    	<td colspan="2"><b>Remarks</b></td>
		                    </tr>
		                    <?
							foreach ($knit_dtls as $row)
							{
		                        ?>
			                    <tr>
			                    	<td width="160" colspan="2"><strong><? echo $phys_test_knitting_arr[$dtls_data_arr[$row]['testing_parameters_id']]; ?></strong></td>
			                    	<td width="80"><? if($dtls_data_arr[$row]['fabric_point']==0) echo ''; else echo $dtls_data_arr[$row]['fabric_point']; ?></td>
			                    	<td width="95"><? echo $yarn_test_result_arr[$dtls_data_arr[$row]['result']]; ?></td>
			                    	<td width="80"><? echo $yarn_test_acceptance_arr[$dtls_data_arr[$row]['acceptance']]; ?></td>
			                    	<td width="80"><? echo $dtls_data_arr[$row]['fabric_class']; ?></td>
			                    	<td colspan="2"><? echo $dtls_data_arr[$row]['remarks']; ?></td>
			                    </tr>
			                    <?
			                }
			                ?>
		                </table>
		            </td>
		            <td style="vertical-align: top;">
		                <table cellspacing="1" cellpadding="1" class="rpt_table2" rules="all" border="1" style="width: 650px; margin-right: 20px; border: 2px solid black;">
		                    <caption style="background-color: #dbb768; font-weight: bold; border-top: 2px solid black; border-left: 2px solid black; border-right: 2px solid black;">Dyeing For <? echo $color_range[$color_val['color']]; ?></caption>
		                    <tr>
		                        <td width="80"><b>Gray GSM</b></td>
		                        <td width="80"><? echo $grey_gsm_dye; ?></td>
		                        <td width="80" rowspan="2"></td>
		                        <td width="80"><b>Batch</b></td>
		                        <td width="80"><? echo $knit_mstdata_arr[$mst_id]['batch']; ?></td>
		                        <td width="80" rowspan="2"></td>
		                        <td width="80"><b>Finish GSM</b></td>
		                        <td><? echo $finish_gsm; ?></td>
		                    </tr>
		                    <tr>
		                        <td width="80"><b>Finish Dia</b></td>
		                        <td width="80"><? echo $finish_dia; ?></td>
		                        <td width="80"><b>Length %</b></td>
		                        <td width="80"><? echo $length; ?></td>
		                        <td width="80"><b>Width</b></td>
		                        <td><? echo $width; ?></td>
		                    </tr>
		                    <tr style="background-color: #ddb;">
		                    	<td width="160" colspan="2"><b>Testing Parameters</b></td>
		                    	<td width="80"><b>Point</b></td>
		                    	<td width="80"><b>Result</b></td>
		                    	<td width="80"><b>Acceptance</b></td>
		                    	<td width="80"><b>Fabric Class</b></td>
		                    	<td colspan="2"><b>Remarks</b></td>
		                    </tr>
		                    <?
							foreach ($dye_dtls as $row)
							{
		                        ?>
		                        <tr>
			                    	<td width="160" colspan="2"><b><? echo $phys_test_dyeing_and_finishing_arr[$dtls_data_arr[$row]['testing_parameters_id']]; ?></b></td>
			                    	<td width="80"><? if($dtls_data_arr[$row]['fabric_point']==0) echo ''; else echo $dtls_data_arr[$row]['fabric_point']; ?></td>
			                    	<td width="80"><? echo $yarn_test_result_arr[$dtls_data_arr[$row]['result']]; ?></td>
			                    	<td width="80"><? echo $yarn_test_acceptance_arr[$dtls_data_arr[$row]['acceptance']]; ?></td>
			                    	<td width="80"><? echo $dtls_data_arr[$row]['fabric_class']; ?></td>
			                    	<td colspan="2"><? echo $dtls_data_arr[$row]['remarks']; ?></td>
			                    </tr>
			                    <?
			                }
			                ?>
		                </table>
		            </td>
	            </tr>
	            <tr><td></td></tr>
	            <tr><td></td></tr>
	            <tr><td></td></tr>
	            <tr><td></td></tr>
	        	<?
	        }
	        ?>
	    </table>
	</div>
	<br>
    <div style="width: 1320px; margin-left: 5px;">
    	<?
		foreach ($sql_mst_comments_rslt as $row)
		{
    		?>
	    	<table cellspacing="1" cellpadding="1" class="rpt_table2" rules="all" border="1" style="width: 1335px; border: 2px solid black;">
	    		<caption style="background-color: #dbb768; font-weight: bold; border-top: 2px solid black; border-left: 2px solid black; border-right:2px solid black;">Comments For <? echo $color_range[$row[csf('color')]]; ?></caption>
	    		<tr style="font-weight: bold;">
	    			<td width="200" align="center"><b>Department</b></td>
	    			<td width="100" align="center"><b>Acceptance</b></td>
	    			<td align="center"><b>Comments</b></td>
	    		</tr>
	    		<tr>
	    			<td width="200"><b>Comentes For Knitting Dept.</b></td>
	    			<td width="100"><? echo $comments_acceptance_arr[$row[csf('comments_knit_acceptance')]];?></td>
	    			<td><? echo $row[csf('comments_knit')];?></td>
	    		</tr>
	    		<tr>
	    			<td width="200"><b>Comentes For Dyeing/Finishing Dept.</b></td>
	    			<td width="100"><? echo $comments_acceptance_arr[$row[csf('comments_dye_acceptance')]];?></td>
	    			<td><? echo $row[csf('comments_dye')];?></td>
	    		</tr>
	    		<tr>
	    			<td width="200"><b>Comentes For Authorize Dept.</b></td>
	    			<td width="100"><? echo $comments_acceptance_arr[$row[csf('comments_author_acceptance')]];?></td>
	    			<td><? echo $row[csf('comments_author')];?></td>
	    		</tr>
	    	</table>
	    	<br>
	    	<?
	    }
	    ?>
    </div>
	<?
}

if ($action == "yarn_test_report")
{
	extract($_REQUEST);
	echo load_html_head_contents("Yarn Test Report Print", "../../../../", 1, 1, '', '', '');
	$data = explode('*', $data);

	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

	$supplierArr 		= return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$yarn_count_arr 	= return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr 	= return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');



	if($db_type==0) $color_cond=" and color_name!=''"; else $color_cond=" and color_name IS NOT NULL";
	$color_range_arr = return_library_array("select id,color_name from lib_color where status_active=1 and grey_color=1 $color_cond order by color_name", "id", "color_name");


	$sql_for_array = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit
	from product_details_master a
	where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.company_id='$data[0]'
	group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
	$product_array=array();
	$result = sql_select($sql_for_array);
	foreach( $result as $prod_val)
	{
		$compositionDetails = $composition[$prod_val[csf("yarn_comp_type1st")]] . " " . $prod_val[csf("yarn_comp_percent1st")] . "%\n";
		if ($prod_val[csf("yarn_comp_type2nd")] != 0)
			$compositionDetails .= $composition[$prod_val[csf("yarn_comp_type2nd")]] . " " . $prod_val[csf("yarn_comp_percent2nd")] . "%";
		$product_array[$prod_val[csf('lot')]]['count']			= $prod_val[csf('yarn_count_id')];
		$product_array[$prod_val[csf('lot')]]['composition']	= $compositionDetails;
		$product_array[$prod_val[csf('lot')]]['color']			= $prod_val[csf('color')];
		$product_array[$prod_val[csf('lot')]]['yarn_type']		= $prod_val[csf('yarn_type')];
		$product_array[$prod_val[csf('lot')]]['supplier_id']	= $prod_val[csf('supplier_id')];
	}

	$sql = "select a.id, a.company_id, a.test_date, a.test_for, a.prod_id, a.lot_number, a.specimen_wgt, a.specimen_length, a.fabric_construct, a.color, a.actual_yarn_count, a.yarn_apperance_grad, a.twist_per_inc, a.moisture_content, a.csp_minimum, a.csp_actual, a.yarn_quality_coments,
	b.id as dtls_id, b.fabric_fault, b.fabric_point, b.fabric_tot_point, b.fabric_point_y2, b.fabric_class, b.remarks
	from inv_yarn_test_mst a, inv_yarn_test_dtls b
	where a.id=b.mst_id and a.company_id='$data[0]' and a.prod_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$data_arr = sql_select($sql);
	?>
	<div style="width:800px;" align="center">
		<div style="width:100%;">
			<table width="100%" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
				<tr class="form_caption">
					<?
					$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
					?>
					<td align="left" width="50">
						<?
						foreach ($data_array as $img_row)
						{
							?>
							<img src='../../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
							align="middle"/>
							<?
						}
						?>
					</td>
					<td colspan="2" align="center">
						<strong style="font-size:18px"><? echo $company_library[$data[0]]; ?></strong><br>
						<?
						echo show_company($data[0], '', array('city'));
						?>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center" style="font-size:16px"><strong><u>Assessment of Numerical Test & Physical Inspection Report
						<br/>Yarn Test Report</u></strong></center></td>
					</tr>
					<tr> <td colspan="3">&nbsp;</td> </tr>
					<tr>
						<?
						if($data_arr[0][csf('test_for')] == 1){
							$checked_bulk = "checked";
						}else if($data_arr[0][csf('test_for')] == 2){
							$checked_sample = "checked";
						}else{
							$checked_bulk = "";
							$checked_sample = "";
						}
						$checked = "checked";
						?>
						<td colspan="3" style="text-align:center;"><strong>
							<input type="checkbox" name="bulk_yarn" value="Bulk Yarn" <? echo $checked_bulk; ?> onClick="return false;" /> Bulk Yarn
							<input type="checkbox" name="sample_yarn" value="Sample Yarn" <? echo $checked_sample; ?> onClick="return false;" /> Sample Yarn
						</strong></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:left;">
							<strong>Test Date: </strong><? echo change_date_format($data_arr[0][csf('test_date')]); ?>
						</td>
						<td style="text-align:right; padding-right:10px;">
							<strong>K. Date: </strong>&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
						</td>
					</tr>
					<tr>
						<td colspan="3"><strong>Product Details : </strong> <?

						$product_dtls = $data_arr[0][csf('prod_id')].", ".$data_arr[0][csf('lot_number')].", ".$yarn_count_arr[$product_array[$data_arr[0][csf('lot_number')]]['count']].", ".$product_array[$data_arr[0][csf('lot_number')]]['composition'].", ".$color_name_arr[$product_array[$data_arr[0][csf('lot_number')]]['color']].", ".$yarn_type[$product_array[$data_arr[0][csf('lot_number')]]['yarn_type']].", ".$product_array[$data_arr[0][csf('lot_number')]]['composition'].", ".$supplierArr[$product_array[$data_arr[0][csf('lot_number')]]['supplier_id']];
						echo $product_dtls;

						?></td>
					</tr>

				</table>
			</div>
			<div style="width:100%;">
				<table style="" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
					<tr>
						<td colspan="3"  bgcolor="#dddddd"><b>Basic Yarn Information :</b></td>
					</tr>
					<tr>
						<td width="35" align="center">1</td><td width="61%">Tested Sample Weight</td><td  style="text-align:center;"><? echo $data_arr[0][csf('specimen_wgt')]; ?> kg</td>
					</tr>
					<tr>
						<td width="35" align="center">2</td><td >Tested Sample Length</td><td  style="text-align:center;"><? echo $data_arr[0][csf('specimen_length')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">3</td><td >Fabric Construction</td><td  style="text-align:center;"><? echo $data_arr[0][csf('fabric_construct')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">4</td><td >Color Range</td><td  style="text-align:center;"><? echo $color_range_arr[$data_arr[0][csf('color')]]; ?></td>
					</tr>
					<tr>
						<td colspan="3"  bgcolor="#dddddd"><b>Numerical Test :</b></td>
					</tr>
					<tr>
						<td width="35" align="center">1</td><td >Actual Yarn Count</td><td  style="text-align:center;"><? echo $data_arr[0][csf('actual_yarn_count')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">2</td><td >Yarn Appearance (Grade)</td><td  style="text-align:center;"><? echo $data_arr[0][csf('yarn_apperance_grad')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">3</td><td >Twist Per Inch (TPI)</td><td  style="text-align:center;"><? echo $data_arr[0][csf('twist_per_inc')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">4</td><td >Moisture Content</td><td  style="text-align:center;"><? echo $data_arr[0][csf('moisture_content')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">5</td><td >CSP Minimum</td><td  style="text-align:center;"><? echo $data_arr[0][csf('csp_minimum')]; ?></td>
					</tr>
					<tr>
						<td width="35" align="center">6</td><td >CSP Actual</td><td  style="text-align:center;"><? echo $data_arr[0][csf('csp_actual')]; ?></td>
					</tr>
				</table>
			</div>
			<br/>
			<div style="width:100%;">
				<table cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
					<thead bgcolor="#dddddd" style="font-size:13px">
						<tr>
							<td colspan="7"><strong> Physical Test : </strong></td>
						</tr>
						<tr>
							<th width="35" align="center">SL</th>
							<th width="150">Fabric Fault</th>
							<th width="70">Point</th>
							<th width="70">Total Point</th>
							<th width="70">Point / 100Y2</th>
							<th width="120">Fabric Class</th>
							<th >Remarks</th>
						</tr>
					</thead>
					<tbody>
						<?
						$i=1;
						foreach($data_arr as $row)
						{
							?>
							<tr valign="middle" height="30">
								<td width="35" align="center"><? echo $i; ?></td>
								<td ><? echo $row[csf('fabric_fault')]; ?></td>
								<td align="center"><? echo $row[csf('fabric_point')]; ?></td>
								<td align="center"><? echo $row[csf('fabric_tot_point')]; ?></td>
								<td align="center"><? echo $row[csf('fabric_point_y2')]; ?></td>
								<td align="center"><? echo $row[csf('fabric_class')]; ?></td>
								<td style="text-align:justify;"><? echo $row[csf('remarks')]; ?></td>
							</tr>
							<?
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<br/>
			<div style="width:100%;">
				<table cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
					<tr>
						<td width="100%" colspan="7" style=" text-align:justify; word-break:normal; height:80px;text-align:left;" valign="top"><strong><u>Yarn Quality Comments</u></strong>:  <? echo $data_arr[0][csf('yarn_quality_coments')]; ?></td>
					</tr>
				</table>
			</div>
			<br/> <br/> <br/><br/> <br/> <br/>
			<div style="width:100%;">
				<table cellspacing="0" width="100%" border="0" >
					<tr align="center">
						<td>Manager QA(Knit)</td>
						<td>Sr. Manager(Knit)</td>
						<td>Manager QA(Dyeing)</td>
						<td>GM/DCM(Textile)</td>
						<td>H. of Textile QA & Test Lab.</td>
					</tr>
				</table>
			</div>
		</div>
		<?
	}

	if ($action == "mrr_remarks")
	{
		echo load_html_head_contents("MRR Statement", "../../../../", 1, 1, $unicode, '', '');
		extract($_REQUEST);
		$sql= "Select a.id, a.recv_number, a.receive_date, b.cons_quantity, a.remarks
		from inv_receive_master a, inv_transaction b
		where a.id=b.mst_id and a.entry_form=1 and b.item_category=1 and b.transaction_type=1 and b.prod_id=$prod_id and b.status_active=1 and a.status_active=1";
	     //echo $sql;
		$result= sql_select($sql);
		?>
		<div align="center">
			<table width="550" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="130">MRR NO.</th>
						<th width="100">MRR Date</th>
						<th width="100">MRR Qty</th>
						<th>Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($result as $row)
					{
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
							<td align="center"><? echo $i;?></td>
							<td><p><? echo $row[csf("recv_number")]; ?>&nbsp;</p></td>
							<td align="center"><p><? if($row[csf("receive_date")]!="" && $row[csf("receive_date")]!="0000-00-00") echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</p></td>
							<td align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
							<td><p><? echo $row[csf("remarks")]; ?>&nbsp;</p></td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
			</table>
			<br>
			<table width="550" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th>Balk Yarn Allocation Remarks</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td bgcolor="#FFFFFF" align="center">
							<?
							$all_remarks_arr=array();
							$all_remarks="";
							$remarks_result= sql_select("Select remarks as allocate_remark from com_balk_yarn_allocate where product_id=$prod_id and status_active=1 and is_deleted=0");
							foreach ($remarks_result as $value)
							{
								if(in_array($value[csf('allocate_remark')], $all_remarks_arr)==false)
								{
									$all_remarks_arr[]=$value[csf('allocate_remark')];
									$all_remarks.=$value[csf('allocate_remark')].",";
								}
							}
							echo chop($all_remarks, ",");
							?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?
		exit();
	}

	if ($action == "allocation_popup")
	{
		echo load_html_head_contents("Allocation Statement", "../../../../", 1, 1, $unicode, '', '');
		extract($_REQUEST);
		$buy_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

		if ($db_type == 0) 
		{
			$sql_allocation = "select a.item_id,a.job_no, a.po_break_down_id,a.booking_no,c.entry_form, sum(qnty) as allocate_qty, sum(a.qnty) as allocate_qty,a.allocation_date,a.is_sales,b.dyed_type is_dyied_yarn from inv_material_allocation_dtls a,inv_material_allocation_mst c,product_details_master b where a.mst_id=c.id and a.item_id=b.id and a.status_active=1 and a.is_deleted=0 and a.item_id='$prod_id' and a.qnty>0  and b.status_active=1 and b.is_deleted=0 group by a.item_id,a.job_no,a.po_break_down_id,a.allocation_date,a.is_sales,b.dyed_type,a.booking_no,c.entry_form";
		} //and length(a.booking_no)>0
		else
		{
			$sql_allocation = "select a.item_id,a.job_no, a.po_break_down_id,a.booking_no,c.entry_form, sum(a.qnty) as allocate_qty,a.allocation_date,a.is_sales,b.dyed_type is_dyied_yarn from inv_material_allocation_dtls a,inv_material_allocation_mst c,product_details_master b where a.mst_id=c.id and a.item_id=b.id and a.status_active=1 and a.is_deleted=0 and a.item_id='$prod_id' and a.qnty>0 and b.status_active=1 and b.is_deleted=0 group by a.item_id,a.job_no,a.po_break_down_id,a.allocation_date,a.is_sales,b.dyed_type,a.booking_no,c.entry_form"; //and length(a.booking_no)>0
		}
		//echo $sql_allocation; 
		$result_allocation = sql_select($sql_allocation);

		foreach ($result_allocation as $row) 
		{

			$job_no = str_replace("'", "", $row[csf("job_no")]);

			if($job_no!="")
			{
				$job_arr[$job_no] = "'".$job_no."'";
			}

			if($row[csf("is_sales")]==1)
			{
				$sales_job_arr[$row[csf("po_break_down_id")]] = $row[csf("job_no")];
				$is_sales_arr[$row[csf("po_break_down_id")]] = $row[csf("is_sales")];
			}

			$is_dyied_yarn=$row[csf("is_dyied_yarn")];
			$booking_arr=explode(",",$row[csf("booking_no")]);
			foreach ($booking_arr as $booking) {
				$booking_allocation_arr[$row[csf("po_break_down_id")]][$row[csf("item_id")]][$booking] += $row[csf("allocate_qty")]*1;
			}
		}

		if(!empty($job_arr)){
			$sql_cond = " and a.job_no_mst in(".implode(",",$job_arr).")";
		}else{
			$sql_cond = (!empty($po_break_down_arr))?" and a.id in(".implode(",",$po_break_down_arr).")":"";
		}

		$po_number_arr = array();
		if($sql_cond !="")
		{
			$po_sql = sql_select("select a.id,a.job_no_mst,a.shipment_date,b.buyer_name, a.file_no,a.grouping,a.po_number from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 $sql_cond");
			foreach ($po_sql as $row) {
				$po_number_arr[$row[csf("id")]]['po'] = $row[csf("po_number")];
				$po_number_arr[$row[csf("id")]]['file'] = $row[csf("file_no")];
				$po_number_arr[$row[csf("id")]]['ref'] = $row[csf("grouping")];
				$po_number_arr[$row[csf("id")]]['job_no'] = $row[csf("job_no_mst")];
				$po_number_arr[$row[csf("id")]]['shipment_date'] = $row[csf("shipment_date")];
				$po_number_arr[$row[csf("job_no_mst")]]['buyer_name'] = $row[csf("buyer_name")];
			}
		}

		if(!empty($sales_job_arr))
		{
			$sales_job_cond = " and a.job_no in('".implode("','",$sales_job_arr)."')";
		}

		$jobsql = "select a.id, a.job_no,a.buyer_id,a.sales_booking_no, a.style_ref_no,a.within_group from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $sales_job_cond";
		$jobData = sql_select($jobsql);
		$job_no_array = array();
		foreach ($jobData as $row) 
		{
			$sales_order_arr[$row[csf('job_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
			$sales_order_arr[$row[csf('job_no')]]['buyer_id']=$row[csf('buyer_id')];
			$sales_order_arr[$row[csf('job_no')]]['within_group']=$row[csf('within_group')];
		}

		$planning_array = array();
		$plan_sql="select a.po_id,a.booking_no,a.dtls_id,b.requisition_no,b.prod_id from ppl_planning_entry_plan_dtls a,ppl_yarn_requisition_entry b where a.dtls_id=b.knit_id and a.status_active=1 and b.status_active=1 and b.prod_id=$prod_id group by a.po_id,a.booking_no,a.dtls_id,b.requisition_no,b.prod_id";
		$planData = sql_select($plan_sql);
		foreach ($planData as $row) 
		{
			$planning_array[$row[csf('po_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]][]=$row[csf('booking_no')];
		}

		$issue_array = $job_wose_issue_array = $issue_basis_arr = array();
		$prod_cond2 = " and c.prod_id in (".$prod_id.")";
		$prodCond = " and b.prod_id in (".$prod_id.")";
		
		$sql_issue = "select a.id,a.issue_basis,a.issue_purpose,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type, sum(c.quantity) as issue_qty,b.job_no,c.is_sales from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and (a.issue_purpose in(1,2) or a.issue_purpose in(1,2,7,15,38) and b.job_no is not null) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 $prod_cond2  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by a.id,a.issue_basis,a.issue_purpose,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type,b.job_no,c.is_sales";

		//echo $sql_issue;

		$result_issue = sql_select($sql_issue);
		$issue_array_req=$booking_arr=array();
		foreach ($result_issue as $row) 
		{
			$issue_job[$row[csf("id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("job_no")];
			$issue_basis_arr[$row[csf("id")]]["basis"] = $row[csf("issue_basis")];
			$issue_basis_arr[$row[csf("id")]]["issue_id"] = $row[csf("id")];

			if($row[csf('dyed_type')] == 1)
			{
				$is_sales = $is_sales_arr[$row[csf("po_breakdown_id")]];

				if($is_sales==1)
				{
					$job_no = $sales_job_arr[$row[csf("po_breakdown_id")]];
					$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
				}else{
					$job_no = $po_number_arr[$row[csf("po_breakdown_id")]]['job_no'];
					$issue_arr[$job_no][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
				}
				
				$issue_array[$job_no][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];

				/*
				if($row[csf("issue_basis")] == 3 || $row[csf("issue_basis")] == 8)
				{

					$booking_arr = $planning_array[$row[csf('po_breakdown_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]];
					$booking_arr = array_unique($booking_arr);
					foreach ($booking_arr as $booking) 
					{
						if($booking != "")
						{
							$booking_allocation = $booking_allocation_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking];

							if($booking_allocation>0)
							{
								$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
								$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_id"][] = $row[csf("id")];
								$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
							}
							else
							{								
								$issue_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
								$issue_array_req[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']][$row[csf("prod_id")]]["issue_id"][] = $row[csf("id")];
								$issue_arr[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
							}
						}						
					}
				}
				else
				{
					$is_sales = $is_sales_arr[$row[csf("job_no")]];

					if($is_sales==1)
					{
						$job_no = $sales_job_arr[$row[csf("job_no")]];
						$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
					}else{
						$job_no = $po_number_arr[$row[csf("po_breakdown_id")]]['job_no'];
						$issue_arr[$job_no][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
					}
					
					$issue_array[$job_no][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
				}*/
			}
			else
			{
				$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];

				if($row[csf("issue_basis")] == 3 || $row[csf("issue_basis")]==1 || $row[csf("issue_basis")] == 8)
				{
					if($row[csf("issue_basis")]==1)
					{
						$booking=0;
						$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
						$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_id"][] = $row[csf("id")];
					}
					else
					{
						$booking_arr = $planning_array[$row[csf('po_breakdown_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]];
						$booking_arr = array_unique($booking_arr);
						foreach ($booking_arr as $booking) {
							if($booking != ""){
								$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
								$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_id"][] = $row[csf("id")];
							}
						}
					}

				}
				else
				{
					$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
					$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_id"][] = $row[csf("id")];
				}
			}

			$job_wose_issue_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']] += $row[csf("issue_qty")];
		}

		//echo "<pre>";
		//print_r($issue_job);

		$issue_return_array = $issue_return_req_array = $job_wose_issue_return_array = array();
		$sql_return = "Select b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose,d.dyed_type, sum(c.quantity) as issue_return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and c.trans_type=4 and c.prod_id=d.id and a.entry_form=9 and b.id=c.trans_id and b.item_category=1 and c.issue_purpose in(1,2,7,15,38,46) and a.receive_basis not in (2) $prod_cond2 and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose,d.dyed_type";
		//echo $sql_return;

		$result_return = sql_select($sql_return);
		foreach ($result_return as $row) 
		{
			$issue_basis = $issue_basis_arr[$row[csf("issue_id")]]["basis"];
			$issueIdArr[$row[csf("issue_id")]] = $row[csf("issue_id")];
			if($row[csf('dyed_type')] == 1)
			{
				$po_number_arr[$row[csf("po_breakdown_id")]]['job_no'];
				$job_wose_issue_return_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']][$row[csf('prod_id')]] += $row[csf("issue_return_qty")];
			}
			else
			{
				if($issue_basis == 3 || $issue_basis == 8)
				{
					$issue_id = $issue_basis_arr[$row[csf("issue_id")]]["issue_id"];
					$issue_return_po_array[$issue_id][$row[csf('prod_id')]] .= $row[csf('po_breakdown_id')].",";
					$issue_return_req_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$issue_id] += $row[csf("issue_return_qty")];
				}
				else
				{				
					//$issue_job = $issue_job[$row[csf("issue_id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]];
					//echo $issue_job."==". $row[csf("issue_id")]."==".$row[csf("po_breakdown_id")]."==".$row[csf("prod_id")]."<br>";

					if($issue_basis==1)
					{
						foreach ($variable as $key => $value) {
							# code...
						}
						if( ( $row[csf("issue_purpose")]==7 || $row[csf("issue_purpose")]==15 || $row[csf("issue_purpose")]==38 || $row[csf("issue_purpose")]==46))
						{

							$booking_no = 0;
							$issue_return_array[$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
						}
					}
					else
					{

						$issue_return_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];					
					}
				}
			}
			
		}

		$issueIdcond = "";
		if(!empty($issueIdArr))
		{
			$issueIdcond = "and b.issue_id not in(".implode(',', $issueIdArr).")";
		}
		
		$sqlRtnQty = "Select a.receive_basis as RECEIVE_BASIS,a.booking_id as BOOKING_ID, b.issue_id as ISSUE_ID, b.prod_id as PROD_ID, sum(b.order_qnty) as RETURN_QTY from inv_receive_master a, inv_transaction b, product_details_master d where a.id=b.mst_id and b.prod_id=d.id and a.entry_form=9 and b.item_category=1 and b.transaction_type=4 and a.receive_basis not in (2) and a.status_active=1 and b.status_active=1 ".$prodCond." ".$issueIdcond." group by a.receive_basis,a.booking_id,b.issue_id, b.prod_id"; 
		//echo $sqlRtnQty;
		$sqlRtnQtyRslt = sql_select($sqlRtnQty);
		
		$sampleReturnQty = array();
		foreach($sqlRtnQtyRslt as $row)
		{
			if($row['RECEIVE_BASIS']==3)
			{
				$smn_booking_no = return_field_value("b.booking_no as BOOKING_NO","ppl_yarn_requisition_entry a,ppl_planning_entry_plan_dtls b","a.knit_id=b.dtls_id and a.requisition_no ='".$row[csf("booking_id")]."' and a.prod_id ='".$row[csf("prod_id")]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","BOOKING_NO");
			}

			$sampleReturnQty[$smn_booking_no][$row['PROD_ID']] += $row['RETURN_QTY'];
		}

		//echo "<pre>";
		//print_r($sampleReturnQty);
		//die();

		if($prod_id!="")
		{
			$dyed_yarn_receive_info = sql_select("select a.id,b.job_no,sum(b.cons_quantity) receive_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=1 and b.prod_id=$prod_id and b.item_category=1 and b.transaction_type=1 and a.status_active=1 and b.status_active=1 group by a.id,b.job_no");
			foreach ($dyed_yarn_receive_info as $dy_row) 
			{
				$dy_rec_arr[$dy_row[csf("id")]] = $dy_row[csf("job_no")];
			}

			$sql_rcv_rtn = "select c.received_id,a.prod_id,sum(case when a.transaction_type=3 then a.cons_quantity else 0 end) as recieved_return_qty from inv_transaction a, product_details_master b, inv_issue_master c where a.mst_id=c.id and a.prod_id=b.id and b.dyed_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id=$prod_id group by c.received_id,a.prod_id";
			$result_sql_rcv_rtn = sql_select($sql_rcv_rtn);

			$rcv_rtn_qty_arr = array();
			foreach ($result_sql_rcv_rtn as $row) 
			{
				$rcv_rtn_qty_arr[$row[csf("prod_id")]][$dy_rec_arr[$row[csf("received_id")]]] = $row[csf("recieved_return_qty")];
			}
		}
		?>
		<script>
			function openmypage_job(job,prod_id,action,title,popup_width)
			{
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'daily_yarn_stock_report_controller.php?job='+job+'&prod_id='+prod_id+'&action='+action, title, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../../');
			}
		</script>
		<div align="center">
			<table width="1090" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<th width="25">SL</th>
					<th width="75">Date</th>
					<th width="110">Job /FSO NO.</th>
					<th width="100">Buyer</th>
					<th width="100">Order No.</th>
					<th width="100">Shipment Date</th>
					<th width="100">File No.</th>
					<th width="100">Ref. No.</th>
					<th width="110">Booking No.</th>
					<th width="75">Allocated Qty</th>
					<th width="70">Issue Qty</th>
					<th width="60">Issue Rtn Qty</th>
					<th width="">Cumul. Balance</th>
				</thead>
			</table>
			<div style="width:1090px; max-height:300px" id="scroll_body">
				<table width="1090" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
					$i = 1;
					$balance = '';
					$issue_qty = 0;
					if(!empty($result_allocation))
					{
						foreach ($result_allocation as $row) 
						{
							$sample_buyer_id=return_field_value("b.buyer_id as buyer_id","inv_material_allocation_mst a,wo_non_ord_samp_booking_mst b","a.booking_no=b.booking_no and a.item_category=1 and a.booking_no ='".$row[csf("booking_no")]."' and a.status_active=1 and a.is_deleted=0  and (a.is_dyied_yarn!=1 or a.is_dyied_yarn is null)","buyer_id");

							$sample_issue_booking_qnty=0;
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							if($row[csf("po_break_down_id")]=="" && $row[csf("booking_no")]=="")
							{
								$issue_basis = array_unique($issue_arr[$row[csf('job_no')]][$prod_id]);
							}
							else if($row[csf("po_break_down_id")]=="")
							{
								$issue_basis = array_unique($issue_arr[$row[csf('job_no')]][$prod_id]);
								
								$sql="SELECT a.company_id,a.buyer_id, a.issue_basis, a.issue_purpose,d.booking_no, sum(b.cons_quantity) issue_qty 
								from inv_issue_master a, inv_transaction b,ppl_yarn_requisition_entry c, ppl_planning_entry_plan_dtls d where a.id=b.mst_id and b.requisition_no=c.requisition_no and c.knit_id=d.dtls_id and b.prod_id=$prod_id and c.prod_id=$prod_id and b.transaction_type=2 and b.item_category=1 and  a.issue_purpose=1 and  a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.booking_no='".$row[csf("booking_no")]."' group by a.company_id, a.buyer_id,a.issue_basis, a.issue_purpose,d.booking_no
	        							union all
	        					SELECT a.company_id,a.buyer_id, a.issue_basis, a.issue_purpose,d.booking_no,sum(b.cons_quantity) issue_qty
	        							from inv_issue_master a, inv_transaction b,wo_yarn_dyeing_mst c, wo_yarn_dyeing_dtls d where a.id=b.mst_id and a.booking_no=c.ydw_no and b.prod_id=$prod_id and d.product_id=$prod_id and c.id=d.mst_id and b.transaction_type=2 and b.item_category=1 and  a.issue_purpose=2 and  a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.booking_no='".$row[csf("booking_no")]."' group by a.company_id, a.buyer_id,a.issue_basis, a.issue_purpose,d.booking_no";

	    						$res=sql_select($sql);
	    						$sample_issue_booking_qnty=$res[0][csf('issue_qty')];
	    						$buyername=$buy_name_arr[$res[0][csf('buyer_id')]];
							}
							else
							{
								$issue_basis = array_unique($issue_arr[$row[csf("po_break_down_id")]][$prod_id]);
							}

							//print_r($issue_basis);
							if($row[csf("is_dyied_yarn")] == 1)
							{

								$issue_qty += $issue_array[$row[csf("job_no")]][$prod_id]["issue_qty"];
								/*
								$issue_qty = 0;
								foreach ($issue_basis as $basis) 
								{
									if($basis==3 || $basis==8)
									{
										if($row[csf("booking_no")] != "")
										{
											$booking_nos = explode(",",$row[csf("booking_no")]);
											$issue_qty=0;
											foreach ($booking_nos as $booking_row) {
												$issue_qty += $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_qty"];
											}
										}	
									}
									else
									{
										//echo "test==$basis";	
										$issue_qty += $issue_array[$row[csf("job_no")]][$prod_id]["issue_qty"];
										//echo $issue_qty."==<br>";
									}

								}*/
							}
							else
							{
								$issue_qty=$issue_qty_wo=0;

								foreach ($issue_basis as $basis) 
								{
									if($basis==3 || $basis==1 || $basis==8)
									{
										if($basis==1)
										{
											$booking_row = 0;
											if( $row[csf("booking_no")] == "" || ( $row[csf("booking_no")] != "" && $row[csf("entry_form")]!=94  ) )
											{
												$issue_qty_wo += $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_qty"];
											}
										}
										else
										{
											if($row[csf("booking_no")] != "")
											{
												$booking_nos = explode(",",$row[csf("booking_no")]);
												$issue_qty=0;
												foreach ($booking_nos as $booking_row) 
												{
													$issue_qty += $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_qty"];
												}
											}
										}
									}
									else
									{
										$issue_qty += $issue_array[$row[csf('job_no')]][$prod_id]["issue_qty"];
									}
								}
							}

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
									
									if(!empty($buy_name_arr[$buyer_id])){
										$buyername=$buy_name_arr[$buyer_id];

									}
									$po_number="";
								}
								$shipment_date = "";
								
								// ===== 23/06/2020
								$return_qty=0;
								foreach ($issue_basis as $basis) 
								{
									//echo $basis . "*";
									if($basis==3 || $basis==8)
									{
										//$return_qty=0;
										$booking_nos = explode(",",$row[csf("booking_no")]);
										foreach ($booking_nos as $booking_row)
										{
											$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_id"]);
											foreach ($issue_ids as $issue_id)
											{
												$return_qty += $issue_return_req_array[$row[csf('po_break_down_id')]][$prod_id][$issue_id];
											}
										}
									}
									else
									{
										if($basis==1)
										{
											$booking_row = 0;
											$return_qty += $issue_return_array[$booking_row][$row[csf("po_break_down_id")]][$prod_id];
										}else{
											$return_qty += $issue_return_array[$row[csf("po_break_down_id")]][$prod_id];
										}									
										//echo $return_qty."ooooo";
									}
								}
								// ===== 23/06/2020
							}
							else
							{
								$po_number=$po_number_arr[$row[csf("po_break_down_id")]]['po'];
								if(!empty($buy_name_arr[$po_number_arr[$row[csf("job_no")]]['buyer_name']])){
									$buyername=$buy_name_arr[$po_number_arr[$row[csf("job_no")]]['buyer_name']];
								}
								$shipment_date = $po_number_arr[$row[csf("po_break_down_id")]]['shipment_date'];

								if($row[csf("is_dyied_yarn")] == 1)
								{
									$return_qty = $job_wose_issue_return_array[$row[csf("job_no")]][$prod_id];
									/* omit -- 27-07-2020
									foreach ($issue_basis as $basis) 
									{
										if($basis==3 || $basis==8)
										{
											if($row[csf("booking_no")] != "")
											{
												$booking_nos = explode(",",$row[csf("booking_no")]);
												foreach ($booking_nos as $booking_row) 
												{
													$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_id"]);
													foreach ($issue_ids as $issue_id) 
													{
														$return_qty += $issue_return_req_array[$row[csf('po_break_down_id')]][$prod_id][$issue_id];
													}
												}
											}
											else
											{

												$issue_ids = array_unique($issue_array_req[$row[csf("job_no")]][$prod_id]["issue_id"]);
												$return_qty = 0;
												foreach ($issue_ids as $issue_id) 
												{
													$po_ids = explode(",",chop($issue_return_po_array[$issue_id][$prod_id],","));
													foreach ($po_ids as $po_id)
													 {
														$return_qty += $issue_return_req_array[$po_id][$prod_id][$issue_id];
													}

												}
											}
										}else{
											$return_qty = $job_wose_issue_return_array[$row[csf("job_no")]];
										}
									}
									*/
								}
								else
								{
									$return_qty=0;
									foreach ($issue_basis as $basis) 
									{
										//echo $basis . "*";
										$return_qty=0;

										if($basis==1 || $basis==3 || $basis==8 )
										{
											if($basis==1) // booking basis-- work order
											{
												$booking_row = 0;
												$return_qty += $issue_return_array[$booking_row][$row[csf("po_break_down_id")]][$prod_id];
											}
											else
											{
												$booking_nos = explode(",",$row[csf("booking_no")]);
												foreach ($booking_nos as $booking_row) 
												{
													$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_id"]);
													foreach ($issue_ids as $issue_id)
													 {
														$return_qty += $issue_return_req_array[$row[csf('po_break_down_id')]][$prod_id][$issue_id];
													}
												}
											}
										}
										else
										{
											$return_qty += $issue_return_array[$row[csf("po_break_down_id")]][$prod_id];
											//echo $return_qty."<br>";
										}
									}
								}
							}

							if($row[csf("is_dyied_yarn")] == 1)
							{
								$return_qty = $job_wose_issue_return_array[$row[csf("job_no")]][$prod_id];
								//for smaple without order issue return qty
								if($row[csf("job_no")] == '')
								{
									$return_qty = $job_wose_issue_return_array[$row[csf("job_no")]][$prod_id]+$sampleReturnQty[$row[csf("booking_no")]][$prod_id];
								}
							}
							else
							{
								$return_qty=0;
								foreach ($issue_basis as $basis) 
								{
									//echo $basis . "*";
									$return_qty=0;
									if( $basis==3 || $basis==8 )
									{
										$booking_nos = explode(",",$row[csf("booking_no")]);
										foreach ($booking_nos as $booking_row) 
										{
											$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_id"]);
											foreach ($issue_ids as $issue_id)
											{
												$return_qty += $issue_return_req_array[$row[csf('po_break_down_id')]][$prod_id][$issue_id];
											}
										}
										
									}
									else
									{
										if($basis==1) // booking basis-- work order
										{
											$booking_row = 0;
											$return_qty += $issue_return_array[$booking_row][$row[csf("po_break_down_id")]][$prod_id];
										}
										else
										{
											$return_qty += $issue_return_array[$row[csf("po_break_down_id")]][$prod_id];
										}
										//echo $return_qty."<br>";
									}
								}
								
								//for smaple without order issue return qty
								if($row[csf("job_no")] == '')
								{
									$return_qty = $return_qty+$sampleReturnQty[$row[csf("booking_no")]][$prod_id];
								}
							}
							
							$booking_no = implode(", ",array_unique(explode(",", $row[csf("booking_no")])));

							if(empty($buyername))
							{
								$buyername=$buy_name_arr[$sample_buyer_id];
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="25" align="center"><? echo $i; ?></td>
								<td width="75" align="center"><? echo change_date_format($row[csf("allocation_date")]); ?></td>
								<td width="110">
									<div style="word-wrap:break-word; width:110px;text-align: center;">
										<? 
										if($issue_basis==3) 
										{
											?>
											<a href="#report_details" onClick="openmypage_job('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('item_id')]; ?>','requisition_details_popup','Requisition Details','480px')"><? echo $row[csf("job_no")]; ?></a>
											<?
										}
										else
										{
											echo $row[csf("job_no")];
										}
										?>
									</div>
								</td>
								<td width="100" align="center"><p><? echo $buyername; ?></div></td>
								<td width="100" title='<? echo $row[csf("po_break_down_id")]; ?>' align="center"><p><? echo $po_number; ?></p></td>
								<td width="100" align="center"><? echo change_date_format($shipment_date); ?></td>
								<td width="100"><p><? echo $po_number_arr[$row[csf("po_break_down_id")]]['file']; ?></p></td>
								<td width="100"><p><? echo $po_number_arr[$row[csf("po_break_down_id")]]['ref']; ?></p></td>
								<td width="110"><p><? echo $booking_no; ?></p></td>
								<td width="75" align="right">

									<?
									$rcv_rtn_qty=0;
									$allocate_qty = $row[csf("allocate_qty")];
									if($row[csf("is_dyied_yarn")]==1)
									{
										$rcv_rtn_qty = $rcv_rtn_qty_arr[$row[csf("item_id")]][$row[csf("job_no")]];
										$allocate_qty = ($allocate_qty-$rcv_rtn_qty);
									}
									echo number_format($allocate_qty, 2);
									?>
								</td>
								<td width="70" align="right" title="<? echo $issue_id."==".$issue_qty .'=='. $issue_qty_wo .'=='.$sample_issue_booking_qnty; ?>"><? echo number_format(($issue_qty+$issue_qty_wo+$sample_issue_booking_qnty), 2); //$issue_qty."+".$issue_qty_wo."+".$sample_issue_booking_qnty;  ?></td>
								<td width="60" align="right"><? echo number_format($return_qty, 2); ?></td>
								<td align="right">
									<?
									$balance = $balance + ($row[csf("allocate_qty")] + number_format($return_qty,2,".","")) - (number_format(($issue_qty+$issue_qty_wo+$sample_issue_booking_qnty),2,".","") + number_format($rcv_rtn_qty,2,".",""));
									echo number_format($balance, 2);
									?>
								</td>
							</tr>
							<?
							$i++;
							$issue_qty=$return_qty=$issue_qty_wo=0;
						}
					}
					else
					{
						echo "<tr colspan='13'><th style='text-align:center;'>No Data Found</th></tr>";
					}

						?>
				</table>
			</div>
		</div>
			<?
		exit();
	}

	if ($action == "requisition_details_popup") 
	{
		echo load_html_head_contents("Requisition Statement", "../../../../", 1, 1, $unicode, '', '');
		extract($_REQUEST);
		if ($db_type == 0) {
			$grp_con="group_concat(dtls_id) as prog_no";
		} else {
			$grp_con="LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY id) as prog_no";
		}
		$job_no_array = array();
		$jobsql = ("select a.id, a.job_no,a.sales_booking_no, a.style_ref_no,a.within_group from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.job_no='$job'");
		$jobData = sql_select($jobsql);
		foreach ($jobData as $row) {
			$job_no_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
			$job_no_array[$row[csf('id')]]['job_id'] = $row[csf('id')];
			$job_no_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			$po_id=$row[csf('id')];
			$within_group=$row[csf('within_group')];
		}
		$prog_no=return_field_value("$grp_con","ppl_planning_entry_plan_dtls","po_id ='".$po_id."' and is_deleted=0 and status_active=1 and is_sales=1","prog_no");
		?>
		<div>
			<fieldset style="width:430px; text-align:center">
			<legend>Requisition Details</legend>
			<table width="400" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<th width="50">SL</th>
					<th width="100">Requisition Date</th>
					<th width="130">Requisition No</th>
					<th width="">Requisition Qty.</th>
				</thead>
			</table>
			<div style="width:420px; overflow-y:scroll; max-height:300px" id="scroll_body">
				<table width="400" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
					$i = 1; $tot_recv_qnty=0;
					$sql = "select a.id, a.knit_id, a.requisition_no, a.requisition_date,sum(a.yarn_qnty) as qnty from ppl_yarn_requisition_entry a	where a.status_active=1 and a.is_deleted=0 and a.prod_id in($prod_id) and a.knit_id in($prog_no) group by a.id, a.knit_id, a.requisition_no,a.requisition_date order by a.id";
					$result = sql_select($sql);
					foreach ($result as $row) 
					{
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$receive_qty = $row[csf("qnty")];
						$tot_recv_qnty += $receive_qty;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="100"><p><? echo change_date_format($row[csf("requisition_date")]); ?></p></td>
							<td width="130" align="center"><? echo $row[csf("requisition_no")]; ?>&nbsp;</td>
							<td width="" align="right"><? echo number_format($receive_qty, 2); ?>&nbsp;</td>
						</tr>
						<?
						$i++;
					}
					?>
					<tfoot>
						<tr>
							<th colspan="3" align="right">Total </th>
							<th  align="right"><? echo number_format($tot_recv_qnty, 2); ?> </th>
						</tr>
					</tfoot>
				</table>
			</div>
			</fieldset>
		</div>
		<?
	}

	if ($action == "stock_popup") 
	{
		echo load_html_head_contents("Stock Details", "../../../../", 1, 1, $unicode, '', '');
		extract($_REQUEST);
		$prod_id_ref = explode("_", $prod_id);
		$prod_id = $prod_id_ref[0];
		$store_id = $prod_id_ref[1];
		?>
		<fieldset style="width:720px">
			<legend>Yarn Receive Details</legend>
			<table width="720" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<th width="50">SL</th>
					<th width="120">MRR No.</th>
					<th width="100">Receive Date</th>
					<th width="110">Receive Qty.</th>
					<th width="110">Receive Basis</th>
					<th>BTB LC No.</th>
				</thead>
			</table>
			<div style="width:720px; overflow-y:scroll; max-height:300px" id="scroll_body">
				<table width="700" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
					$i = 1;
					$tot_recv_qnty = '';
					$btblc_arr = return_library_array("select b.pi_id, a.lc_number from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.pi_entry_form=165", 'pi_id', 'lc_number');
					$store_cond = "";
					if ($store_id > 0)
						$store_cond = " and b.store_id=$store_id";
					$sql = "select c.id, c.recv_number, c.receive_date, c.receive_basis, c.booking_id, sum(b.cons_quantity) as qnty from inv_transaction b, inv_receive_master c
					where b.mst_id=c.id and b.prod_id=$prod_id and c.entry_form=1 and b.transaction_type=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 $store_cond group by c.id, c.recv_number, c.receive_date, c.receive_basis, c.booking_id order by c.id";

					$result = sql_select($sql);
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$receive_qty = $row[csf("qnty")];
						$tot_recv_qnty += $receive_qty;

						$btblc_no = '';
						if ($row[csf("receive_basis")] == 1) {
							$btblc_no = $btblc_arr[$row[csf("booking_id")]];
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="120"><p><? echo $row[csf("recv_number")]; ?></p></td>
							<td width="100" align="center"><? echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($receive_qty, 2); ?>&nbsp;</td>
							<td width="110"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?></p></td>
							<td><p><? echo $btblc_no; ?>&nbsp;</p></td>
						</tr>
						<?
						$i++;
					}

					$sql = "select c.id,c.transfer_system_id,c.transfer_date,sum(b.cons_quantity) as qnty from inv_transaction b,inv_item_transfer_mst c where b.mst_id=c.id and b.prod_id=$prod_id and b.transaction_type=5 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by c.id, c.transfer_system_id, c.transfer_date order by c.id";
					$result = sql_select($sql);
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$receive_qty = $row[csf("qnty")];
						$tot_recv_qnty += $receive_qty;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="120"><p><? echo $row[csf("transfer_system_id")]; ?></p></td>
							<td width="100" align="center"><? echo change_date_format($row[csf("transfer_date")]); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($receive_qty, 2); ?>&nbsp;</td>
							<td width="110">Transfer</td>
							<td>&nbsp;</td>
						</tr>
						<?
						$i++;
					}
					?>
					<tfoot>
						<th colspan="3">Total</th>
						<th><? echo number_format($tot_recv_qnty, 2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
		exit();
	}

	if ($action == "stock_popup_mrr") 
	{
		echo load_html_head_contents("Stock Details", "../../../../", 1, 1, $unicode, '', '');
		extract($_REQUEST);
		$pord_ref = explode(",", $prod_id);
		$prod_id = $pord_ref[0];
		$mrr_id = $pord_ref[1];
		$type = $pord_ref[2];
		?>
		<fieldset style="width:720px">
			<legend>Yarn Receive Details</legend>
			<table width="720" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<th width="50">SL</th>
					<th width="120">MRR No.</th>
					<th width="100">Receive Date</th>
					<th width="110">Receive Qty.</th>
					<th width="110">Receive Basis</th>
					<th>BTB LC No.</th>
				</thead>
			</table>
			<div style="width:720px; overflow-y:scroll; max-height:300px" id="scroll_body">
				<table width="700" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
					$i = 1;
					$tot_recv_qnty = '';
					$btblc_arr = return_library_array("select b.pi_id, a.lc_number from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.item_category_id=1", 'pi_id', 'lc_number');
					if (($type == 1) || $type == 4) {
						$sql = "select c.id, c.recv_number, c.receive_date, c.receive_basis, c.booking_id, sum(b.cons_quantity) as qnty from inv_transaction b, inv_receive_master c
						where b.mst_id=c.id and b.prod_id=$prod_id and c.id=$mrr_id and c.entry_form in(1,9) and b.transaction_type in(1,4) and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by c.id, c.recv_number, c.receive_date, c.receive_basis, c.booking_id order by c.id";
					} else if($type== 5){
						$sql = "select c.id, c.transfer_system_id as recv_number, c.transfer_date as receive_date,  null as receive_basis, 0 as booking_id, sum(b.cons_quantity) as qnty from inv_transaction b,  inv_item_transfer_mst c
						where b.mst_id=c.id and b.prod_id=$prod_id and c.id=$mrr_id and b.transaction_type=5 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by c.id, c.transfer_system_id, c.transfer_date order by c.id";
					}

		//echo $sql;

					$result = sql_select($sql);
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$receive_qty = $row[csf("qnty")];
						$tot_recv_qnty += $receive_qty;

						$btblc_no = '';
						if ($row[csf("receive_basis")] == 1) {
							$btblc_no = $btblc_arr[$row[csf("booking_id")]];
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="120"><p><? echo $row[csf("recv_number")]; ?></p></td>
							<td width="100" align="center"><? echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($receive_qty, 2); ?>&nbsp;</td>
							<td width="110"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?></p></td>
							<td><p><? echo $btblc_no; ?>&nbsp;</p></td>
						</tr>
						<?
						$i++;
					}

					$sql = "select c.id, c.transfer_system_id, c.transfer_date, sum(b.cons_quantity) as qnty from inv_transaction b, inv_item_transfer_mst c
					where b.mst_id=c.id and b.prod_id=$prod_id and b.transaction_type=5 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by c.id, c.transfer_system_id, c.transfer_date order by c.id";
					$result = sql_select($sql);
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$receive_qty = $row[csf("qnty")];
						$tot_recv_qnty += $receive_qty;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="120"><p><? echo $row[csf("transfer_system_id")]; ?></p></td>
							<td width="100" align="center"><? echo change_date_format($row[csf("transfer_date")]); ?>&nbsp;</td>
							<td width="110" align="right"><? echo number_format($receive_qty, 2); ?>&nbsp;</td>
							<td width="110">Transfer</td>
							<td>&nbsp;</td>
						</tr>
						<?
						$i++;
					}
					?>
					<tfoot>
						<th colspan="3">Total</th>
						<th><? echo number_format($tot_recv_qnty, 2); ?>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
		exit();
	}

	if ($action == "transferPopup") 
	{
		echo load_html_head_contents("Transfer Details", "../../../../", 1, 1, $unicode, '', '');
		extract($_REQUEST);

		if ($store_name == 0)
			$store_cond = "";
		else
			$store_cond = " and a.store_id=$store_name";

		$sql_transfer = "select
		sum(case when c.transfer_criteria=1 then a.cons_quantity else 0 end) as com_trans_qty,
		sum(case when c.transfer_criteria=2 then a.cons_quantity else 0 end) as store_trans_qty
		from inv_transaction a, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=$trans_type and c.transfer_criteria in(1,2) and a.prod_id=$prod_id $store_cond";
		$transferData = sql_select($sql_transfer);
		$com_trans_qty = $transferData[0][csf('com_trans_qty')];
		$store_trans_qty = $transferData[0][csf('store_trans_qty')];

		$cap_arr = array(5 => "In", 6 => "Out");
		?>
		<div align="center">
			<fieldset style="width:420px; margin-left:7px;">
				<legend>Transfer <? echo $cap_arr[$trans_type]; ?> Details</legend>
				<table width="400" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<th width="50">SL</th>
						<th width="150">Transfer Type</th>
						<th>Transfer Qty.</th>
					</thead>
					<tr bgcolor="#E9F3FF" onclick="change_color('tr_1', '#E9F3FF')" id="tr_1">
						<td width="50">1</td>
						<td width="150">Company To Company</td>
						<td align="right"><? echo number_format($com_trans_qty, 2); ?>&nbsp;</td>
					</tr>
					<tr bgcolor="#FFFFFF" onclick="change_color('tr_2', '#FFFFFF')" id="tr_2">
						<td width="50">2</td>
						<td width="150">Store To Store</td>
						<td align="right"><? echo number_format($store_trans_qty, 2); ?>&nbsp;</td>
					</tr>
					<tfoot>
						<th colspan="2">Total</th>
						<th><? echo number_format($com_trans_qty + $store_trans_qty, 2); ?>&nbsp;</th>
					</tfoot>
				</table>
			</fieldset>
		</div>
		<?
		exit();
	}

	if($action=="company_wise_report_button_setting")
	{
		extract($_REQUEST);

		$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=36 and is_deleted=0 and status_active=1");

		if($print_report_format != "")
		{
			$print_report_format_arr=explode(",",$print_report_format);
			echo "$('#search').hide();\n";
			echo "$('#search1').hide();\n";
			echo "$('#search2').hide();\n";
			echo "$('#search3').hide();\n";
			echo "$('#search4').hide();\n";
			echo "$('#search5').hide();\n";
			echo "$('#search6').hide();\n";
			echo "$('#search7').hide();\n";

			foreach($print_report_format_arr as $id)
			{
				if($id==152){echo "$('#search').show();\n";}
				if($id==108){echo "$('#search1').show();\n";}
				if($id==102){echo "$('#search2').show();\n";}
				if($id==103){echo "$('#search3').show();\n";}
				if($id==104){echo "$('#search4').show();\n";}
				if($id==105){echo "$('#search5').show();\n";}
				if($id==106){echo "$('#search6').show();\n";}
				if($id==107){echo "$('#search7').show();\n";}
			}
		}
		else
		{
			echo "$('#search').show();\n";
			echo "$('#search1').show();\n";
			echo "$('#search2').show();\n";
			echo "$('#search3').show();\n";
			echo "$('#search4').show();\n";
			echo "$('#search5').show();\n";
			echo "$('#search6').show();\n";
			echo "$('#search7').show();\n";
		}
		exit();
	}

	if($action == "composition_popup")
	{
		echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','1','');
		extract($_REQUEST);
		?>
		<script>
		/*function js_set_value( composition,id )
		{
	 	 	$('#hidden_composition').val(composition);
	 	 	$('#hidden_composition_id').val(id);
	  		parent.emailwindow.hide();
		}*/


		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_pre_composition_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( str )
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hidden_composition_id').val(id);
			$('#hidden_composition').val(name);
		}
		</script>
		</head>
		<fieldset style="width:390px">
			<legend>Yarn Receive Details</legend>
			<input type="hidden" name="hidden_composition" id="hidden_composition" value="">
			<input type="hidden" name="hidden_composition_id" id="hidden_composition_id" value="">
			<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th colspan="2">
							<? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?>
						</th>
					</tr>
					<tr>
						<th width="50">SL</th>
						<th width="">Composition Name</th>
					</tr>
				</thead>
			</table>
			<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
				<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;

			$result=sql_select("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name");
			$pre_composition_id_arr=explode(",",$pre_composition_id);
			foreach ($result as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";


				if(in_array($row[csf("id")],$pre_composition_id_arr))
				{
					if($pre_composition_ids=="") $pre_composition_ids=$i; else $pre_composition_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="50">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("composition_name")]; ?>"/>
					</td>
					<td width=""><p><? echo $row[csf("composition_name")]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="txt_pre_composition_row_id" id="txt_pre_composition_row_id" value="<?php echo $pre_composition_ids; ?>"/>
		</table>
		</div>
		<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
		</fieldset>
		<script type="text/javascript">
			setFilterGrid('table_body',-1);
			set_all();
		</script>
		<?
	}
?>
