<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data		= $_REQUEST['data'];
$action		= $_REQUEST['action'];
$user_id 	= $_SESSION['logic_erp']["user_id"];
$permission = $_SESSION['page_permission'];

//load drop down supplier
if ($action == "load_drop_down_supplier") {
	if($data){$companyCon=" and a.tag_company='$data'";}
	else{$companyCon="";}
	echo create_drop_down("cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $companyCon and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}


if ($action == "generate_report") 
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$companyArr 		= return_library_array("select id,company_name from lib_company", "id", "company_name");
	//$companyArr[0] 	= "All Company";
	$supplierArr 		= return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$yarn_count_arr 	= return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr 	= return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	if ($db_type == 0) {
		$exchange_rate = return_field_value("conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and is_deleted=0 order by id DESC limit 0,1");
		$current_server_date = change_date_format( date("Y-m-d"), 'yyyy-mm-dd');
	} else if ($db_type == 2) {
		$exchange_rate = return_field_value("conversion_rate", "(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)", "ROWNUM = 1");
		$current_server_date = change_date_format( date("Y-m-d"), '', '', 1);
	} else {
		$current_server_date = "";
		$exchange_rate = 1;
	}

	if ($cbo_company_name == 0) {
		$company_cond = "";
		$nameArray = sql_select("select allocation from variable_settings_inventory where item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
	} else {
		$company_cond = " and a.company_id=$cbo_company_name";
		$nameArray = sql_select("select allocation from variable_settings_inventory where company_name=$cbo_company_name and item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
	}

	$allocated_qty_variable_settings = $nameArray[0][csf('allocation')];


	$receive_array = array();
	$sql_receive = "Select a.prod_id, max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,
	sum(case when a.transaction_type in (1,4) and a.transaction_date <= '" . $current_server_date . "' then a.cons_quantity else 0 end) as rcv_total_opening,
	sum(case when a.transaction_type in (1,4) and a.transaction_date <= '" . $current_server_date . "' then a.cons_amount else 0 end) as rcv_total_opening_amt,
	sum(case when a.transaction_type in (1,4) and a.transaction_date <= '" . $current_server_date . "' then a.cons_rate else 0 end) as rcv_total_opening_rate,
	sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date <= '" . $current_server_date . "' then a.cons_quantity else 0 end) as purchase,
	sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date <= '" . $current_server_date . "' then a.cons_amount else 0 end) as purchase_amt,
	sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date <= '" . $current_server_date . "' then a.cons_quantity else 0 end) as rcv_loan,
	sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date <= '" . $current_server_date . "' then a.cons_amount else 0 end) as rcv_loan_amt,
	sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date <= '" . $current_server_date . "' then a.cons_quantity else 0 end) as rcv_inside_return,
	sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date <= '" . $current_server_date . "' then a.cons_amount else 0 end) as rcv_inside_return_amt,
	sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date <=  '" . $current_server_date . "' then a.cons_quantity else 0 end) as rcv_outside_return,
	sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date <= '" . $current_server_date . "' then a.cons_amount else 0 end) as rcv_outside_return_amt 
	from inv_transaction a, inv_receive_master c where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond group by a.prod_id";
	
	//echo $sql_receive; die;
	$result_sql_receive = sql_select($sql_receive);
	foreach ($result_sql_receive as $row) {
		//$receive_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
		$receive_array[$row[csf("prod_id")]]['rcv_total_opening'] 		= $row[csf("rcv_total_opening")];
		$receive_array[$row[csf("prod_id")]]['rcv_total_opening_amt'] 	= $row[csf("rcv_total_opening_amt")];
		$receive_array[$row[csf("prod_id")]]['purchase'] 				= $row[csf("purchase")];
		$receive_array[$row[csf("prod_id")]]['purchase_amt'] 			= $row[csf("purchase_amt")];
		$receive_array[$row[csf("prod_id")]]['rcv_loan'] 				= $row[csf("rcv_loan")];
		$receive_array[$row[csf("prod_id")]]['rcv_loan_amt'] 			= $row[csf("rcv_loan_amt")];
		$receive_array[$row[csf("prod_id")]]['rcv_inside_return'] 		= $row[csf("rcv_inside_return")];
		$receive_array[$row[csf("prod_id")]]['rcv_inside_return_amt'] 	= $row[csf("rcv_inside_return_amt")];
		$receive_array[$row[csf("prod_id")]]['rcv_outside_return'] 		= $row[csf("rcv_outside_return")];
		$receive_array[$row[csf("prod_id")]]['rcv_outside_return_amt'] 	= $row[csf("rcv_outside_return_amt")];
		$receive_array[$row[csf("prod_id")]]['weight_per_bag'] 			= $row[csf("weight_per_bag")];
		$receive_array[$row[csf("prod_id")]]['weight_per_cone'] 		= $row[csf("weight_per_cone")];
		$receive_array[$row[csf("prod_id")]]['rcv_total_opening_rate'] 	= $row[csf("rcv_total_opening_rate")];
	}

	unset($result_sql_receive);

	$issue_array = array();
	$sql_issue = "select a.prod_id,
	sum(case when a.transaction_type in (2,3) and a.transaction_date <= '" . $current_server_date . "' then a.cons_quantity else 0 end) as issue_total_opening,
	sum(case when a.transaction_type in (2,3) and a.transaction_date <= '" . $current_server_date . "' then a.cons_rate else 0 end) as issue_total_opening_rate,
	sum(case when a.transaction_type in (2,3) and a.transaction_date <= '" . $current_server_date . "' then a.cons_amount else 0 end) as issue_total_opening_amt,
	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  <= '" . $current_server_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  <= '" . $current_server_date . "' then a.cons_quantity else 0 end) as issue_inside,
	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  <= '" . $current_server_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  <= '" . $current_server_date . "' then a.cons_quantity else 0 end) as issue_outside,
	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  <= '" . $current_server_date . "' then a.cons_amount else 0 end) as issue_outside_amt,
	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date <= '" . $current_server_date . "' then a.cons_quantity else 0 end) as rcv_return,
	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date <= '" . $current_server_date . "' then a.cons_amount else 0 end) as rcv_return_amt,
	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date <= '" . $current_server_date . "' then a.cons_quantity else 0 end) as issue_loan,
	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date <= '" . $current_server_date . "' then a.cons_amount else 0 end) as issue_loan_amt			
	from inv_transaction a, inv_issue_master c
	where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.prod_id";
	
	$result_sql_issue = sql_select($sql_issue);
	foreach ($result_sql_issue as $row) {
		//$issue_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
		$issue_array[$row[csf("prod_id")]]['issue_total_opening'] = $row[csf("issue_total_opening")];
		$issue_array[$row[csf("prod_id")]]['issue_total_opening_amt'] = $row[csf("issue_total_opening_amt")];
		$issue_array[$row[csf("prod_id")]]['issue_total_opening_rate'] = $row[csf("issue_total_opening_rate")];
		$issue_array[$row[csf("prod_id")]]['issue_inside'] = $row[csf("issue_inside")];
		$issue_array[$row[csf("prod_id")]]['issue_inside_amt'] = $row[csf("issue_inside_amt")];
		$issue_array[$row[csf("prod_id")]]['issue_outside'] = $row[csf("issue_outside")];
		$issue_array[$row[csf("prod_id")]]['issue_outside_amt'] = $row[csf("issue_outside_amt")];
		$issue_array[$row[csf("prod_id")]]['rcv_return'] = $row[csf("rcv_return")];
		$issue_array[$row[csf("prod_id")]]['rcv_return_amt'] = $row[csf("rcv_return_amt")];
		$issue_array[$row[csf("prod_id")]]['issue_loan'] = $row[csf("issue_loan")];
		$issue_array[$row[csf("prod_id")]]['issue_loan_amt'] = $row[csf("issue_loan_amt")];
	}
	
	unset($result_sql_issue);
	if ($store_wise == 1) {
		$trans_criteria_cond = "";
	} else {
		$trans_criteria_cond = " and c.transfer_criteria=1";
	}
	$transfer_qty_array = array();
	
	$sql_transfer = "select a.prod_id,
	sum(case when a.transaction_type=6 and a.transaction_date <= '" . $current_server_date . "' then a.cons_quantity else 0 end) as trans_out_total_opening,
	sum(case when a.transaction_type=6 and a.transaction_date <= '" . $current_server_date . "' then a.cons_amount else 0 end) as trans_out_total_opening_amt,
	sum(case when a.transaction_type=6 and a.transaction_date <= '" . $current_server_date . "' then a.cons_rate else 0 end) as trans_out_total_opening_rate,
	sum(case when a.transaction_type=5 and a.transaction_date <= '" . $current_server_date . "' then a.cons_quantity else 0 end) as trans_in_total_opening,
	sum(case when a.transaction_type=5 and a.transaction_date <= '" . $current_server_date . "' then a.cons_rate else 0 end) as trans_in_total_opening_rate,
	sum(case when a.transaction_type=5 and a.transaction_date <= '" . $current_server_date . "' then a.cons_amount else 0 end) as trans_in_total_opening_amt,
	sum(case when a.transaction_type=6 and a.transaction_date <= '" . $current_server_date . "' then a.cons_quantity else 0 end) as transfer_out_qty,
	sum(case when a.transaction_type=6 and a.transaction_date <= '" . $current_server_date . "' then a.cons_amount else 0 end) as transfer_out_amt,
	sum(case when a.transaction_type=5 and a.transaction_date <= '" . $current_server_date . "' then a.cons_quantity else 0 end) as transfer_in_qty,
	sum(case when a.transaction_type=5 and a.transaction_date <= '" . $current_server_date . "' then a.cons_amount else 0 end) as transfer_in_amt 
	from inv_transaction a, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1  and c.is_deleted=0 $trans_criteria_cond group by a.prod_id";
	
	$result_sql_transfer = sql_select($sql_transfer);
	foreach ($result_sql_transfer as $transRow) {
		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_qty'] 			= $transRow[csf("transfer_out_qty")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_amt'] 			= $transRow[csf("transfer_out_amt")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_qty'] 			= $transRow[csf("transfer_in_qty")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_amt'] 			= $transRow[csf("transfer_in_amt")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening'] 	= $transRow[csf("trans_out_total_opening")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_amt'] = $transRow[csf("trans_out_total_opening_amt")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening'] 		= $transRow[csf("trans_in_total_opening")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_amt'] 	= $transRow[csf("trans_in_total_opening_amt")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_rate'] 	= $transRow[csf("trans_in_total_opening_rate")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_rate'] 	= $transRow[csf("trans_out_total_opening_rate")];
	}
	
	unset($result_sql_transfer);

	if ($db_type == 0) {
		$yarn_allo_sql = sql_select("select product_id, group_concat(buyer_id) as buyer_id, group_concat(allocate_qnty) as allocate_qnty from com_balk_yarn_allocate where status_active=1 group by product_id");
	//LISTAGG(CAST( a.lc_sc_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_id) as lc_id
	} else if ($db_type == 2) {
		$yarn_allo_sql = sql_select("select product_id, LISTAGG(CAST(buyer_id as VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY buyer_id) as buyer_id, LISTAGG(CAST(allocate_qnty AS VARCHAR(4000)),',') WITHIN GROUP(ORDER BY allocate_qnty) as allocate_qnty from com_balk_yarn_allocate where status_active=1 group by product_id");
	}
	
	$yarn_allo_arr = array();
	foreach ($yarn_allo_sql as $row) {
		$yarn_allo_arr[$row[csf("product_id")]]['product_id'] 	= $row[csf("product_id")];
		$yarn_allo_arr[$row[csf("product_id")]]['buyer_id'] 	= implode(",", array_unique(explode(",", $row[csf("buyer_id")])));
		$yarn_allo_arr[$row[csf("product_id")]]['allocate_qnty'] = implode(",", array_unique(explode(",", $row[csf("allocate_qnty")])));
	}

	unset($yarn_allo_sql);

	$mrr_rate_sql = sql_select("select prod_id, min(transaction_date) as min_date, max(transaction_date), sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction 
	where status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5) group by prod_id");
	$mrr_rate_arr = array();
	foreach ($mrr_rate_sql as $row) {
	$date_array[$row[csf("prod_id")]]['min_date'] 	= $row[csf("min_date")];
	$date_array[$row[csf("prod_id")]]['max_date'] 	= $row[csf("max_date")];
	$mrr_rate_arr[$row[csf("prod_id")]] 			= $row[csf("cons_amount")] / $row[csf("cons_quantiy")];
	}

	unset($mrr_rate_sql);


	$cbo_supplier=str_replace("'","",$cbo_supplier);
	$txt_lot=trim($txt_lot);
	$cbo_yarn_count=str_replace("'","",$cbo_yarn_count);
	$cbo_yarn_type=str_replace("'","",$cbo_yarn_type);

	$sql_cond="";
	if ($cbo_company_name>0) $sql_cond =" and a.company_id=$cbo_company_name";
		
	if ($cbo_supplier>0) $sql_cond.=" and a.supplier_id='$cbo_supplier'"; 
	if ($txt_lot!="") $sql_cond.=" and a.lot = '$txt_lot'";
	if ($cbo_yarn_count>0) $sql_cond.=" and a.yarn_count_id='$cbo_yarn_count'";
	if ($cbo_yarn_type>0)  $sql_cond.=" and a.yarn_type=$cbo_yarn_type "; 

	$sql = "select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit 
	from product_details_master a
	where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $sql_cond 
	group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
	
	$result = sql_select($sql);
	
	$i = 1;
	ob_start();
	?>
	<div style="width:800px;" > 
		<table width="800" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all"> 
			<thead>
				<tr>
					<th width="40">SL</th>
					<th width="60">Prod.ID</th>
					<th width="100">Lot</th>
					<th width="60">Count</th>
					<th width="">Composition</th>
					<th width="80">Color</th>
					<th width="100">Yarn Type</th>
					<th width="80">Supplier</th>
					<th width="119">Stock</th>
				</tr> 
			</thead>
		</table>  
		<div style="width:800px;overflow-y:scroll; max-height:350px" id="scroll_body" > 
			<table width="780" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">   
			<?
				$tot_stock_value = 0;
				foreach ($result as $row) 
				{
					
					$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
					if ($row[csf("yarn_comp_type2nd")] != 0)
						$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
					
					$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
					$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
					
					$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
					$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];
					
					$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);
					
					$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
					$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;
					
					$stockInHand = $openingBalance + $totalRcv - $totalIssue;
					
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					
					if (number_format($stockInHand, 2) > 0.00) 
					{
						?>                                 
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="60"><? echo $row[csf("id")]; ?></td> 
                            <td width="100"><p><a href='##' onClick="openmypage_stock('<? echo $row[csf('id')] . "_" . $store_name. "_" . $row[csf("lot")] ?>', 'lot_popup')">
                            <? echo $row[csf("lot")]; ?>
                            </a></p></td> 	
                            <td width="60"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></p></td>                                
                            <td width=""><p><? echo $compositionDetails; ?></p></td>
                            <td width="80"><p><? echo $color_name_arr[$row[csf("color")]]; ?></p></td> 
                            <td width="100"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?></p></td> 
                            <td width="80"><p><? echo $supplierArr[$row[csf("supplier_id")]]; ?></p></td> 
                            <td width="100" align="right"><? echo number_format($stockInHand, 2); ?></td>
						</tr>
						<?
						$i++;
					}
				}
			?> 
			</table>  
		</div>
	</div> 
		<?
    /*$html = ob_get_contents();
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
    echo "$html**$filename";*/
	
    exit();
}


if ($action == "lot_popup") 
{
	echo load_html_head_contents("Stock Details", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	
	$prod_id_ref = explode("_", $prod_id);
	$prod_id = $prod_id_ref[0];
	$store_id = $prod_id_ref[1];
	$lot_number = $prod_id_ref[2];
	?>
<script>
	var permission = '<?php echo $permission; ?>';
	function fnc_yarn_test_entry( operation ) 
	{
		if(form_validation('txt_test_date*cbo_test_for*cbo_company_id*prod_id*lot_number','Test Date*Test For*Company Name*Product ID*Lot Number')==false)
		{
			return; 
		}
		
		if( operation == 2 ){
			var r=confirm(" Press \"OK\" to Delete.  \n Press \"Cancel\" for not.");
			if (r==true)
			{
				
			}
			else
			{
				return;
			}
		}
		
		
		var physical_test = "";
		
		var total_row = $("#physical_test_data_container tr").length;
		$("#physical_test_data_container tr").each(function()
		{
			var txtFabricFault 		= $(this).find('input[name="txtFabricFault[]"]').val();
			var txtPoint 			= $(this).find('input[name="txtPoint[]"]').val();
			var txtTotalPoint 		= $(this).find('input[name="txtTotalPoint[]"]').val();
			var txtPointY 			= $(this).find('input[name="txtPointY[]"]').val();
			var txtFabricClass 		= $(this).find('input[name="txtFabricClass[]"]').val();
			var txtRemarks 			= $(this).find('input[name="txtRemarks[]"]').val();
			var txtUpdateIdDtls 	= $(this).find('input[name="txtUpdateIdDtls[]"]').val();
			if (physical_test != ''){
				physical_test += "*"+ txtUpdateIdDtls + "_" +  txtFabricFault + "_" + txtPoint + "_" + txtTotalPoint + "_" + txtPointY + "_" + txtFabricClass + "_" + txtRemarks;
			} else {
				physical_test += txtUpdateIdDtls + "_" +  txtFabricFault + "_" + txtPoint + "_" + txtTotalPoint + "_" + txtPointY + "_" + txtFabricClass + "_" + txtRemarks;
			}
		});
		var data = "action=SaveUpdateDeleteTestData&operation="+operation+"&physicalTest="+ physical_test + get_submitted_data_string('txt_speciment_wgt*txt_speciment_lenght*txt_fabric_construction*cbo_color*txt_actual_yarn_count*txt_yarn_appearance*txt_twist_per_inch*txt_moisture_content*txt_csp_minimum*txt_csp_actual*yarnQualityComments*txt_test_date*cbo_test_for*cbo_company_id*prod_id*lot_number*deleted_ids*update_id', "../../../");
		http.open("POST","yarn_test_controller.php",true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_test_entry_response;
	}
	function fnc_yarn_test_entry_response()
	{
		if (http.readyState == 4)
		{
			var response = trim(http.responseText).split('**');
			show_msg(trim(response[0]));
			if(response[0] == 0){ 
				//parent.emailwindow.hide();
				$('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
				 {
					$('#msg_box_popp').html("Data Save Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
				 });
				populate_all_data(response[1]);
			}
			else if (response[0] == 1){
				$('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
				 {
					$('#msg_box_popp').html("Data Update Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
				 });
				populate_all_data(response[1]);
			}
			else if (response[0] == 2){
				/*$('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
				 {
					$('#msg_box_popp').html("Data Delete Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
				 });*/
				 //alert("Data Delete Sussessfully");
				parent.emailwindow.hide();
			}
			else
			{
				alert("You have to must input at least one Fabric Fault!");
				parent.emailwindow.hide();
				//reset_form('SerialNoPopUpFrm_1','','','','','cbo_company_id*prod_id*lot_number');
			}
			release_freezing();
		}
	}
	
	function fnc_yarn_test_report_printer(operation)
	{
		if(operation == 4) //for print preview
		{
			 if ($("#update_id").val() == "") {
                alert("Please Save First.");
                return;
            }
			
			fnc_yarn_test_report();
		}
		
	}
	
	function fnc_yarn_test_report()
	{
		generate_report_file($('#cbo_company_id').val() + '*' + $('#update_id').val(), 'yarn_test_report', 'yarn_test_controller');
		return;
	}
	
	
	
	function fnResetForm()
	{
		parent.emailwindow.hide();
		//alert("Data Refresh Decision Pending ...... ");
		//reset_form('SerialNoPopUpFrm_1','','','','','');
	}
	
	
	function generate_report_file(data, action, page) 
	{
        window.open("yarn_test_controller.php?data=" + data + '&action=' + action, true);
    }
</script>
</head>
<body onLoad="set_hotkey()">
	<div id="msg_box_popp" style="height:15px; width:200px; position:relative; left:250px"></div>
	<div style="width:720px; overflow-y:hidden; position:absolute; top:20px;">
        <div style="display:none;" >
            <?php  echo load_freeze_divs("../../../", $permission); ?> 
        </div>
	<form name="SerialNoPopUpFrm_1"  id="SerialNoPopUpFrm_1" autocomplete="off">
	<fieldset style="float:left">
		<legend>Basic Yarn Information</legend>
		<table width="100%" align="center" class="rpt_table" rules="all" cellspacing="0" cellpadding="0" border="0"  style="float:left">
        	<tr>
				<td width="100" class="must_entry_caption">Test Date</td>
                <td><input type="text" name="txt_test_date" id="txt_test_date" value="" class="datepicker" style="width:100px"  /></td>
			</tr>
            <tr>
				<td width="150" class="must_entry_caption">Test For</td>
				<td>
                 <? 
					$yarn_test_for_arr=array(1=>'Bulk Yarn',2=>'Sample Yarn');
					echo create_drop_down( "cbo_test_for", 112, $yarn_test_for_arr,"",1, "-- Select Test --",$selected,"",'','','','','','','','' );
				?>
                </td>
			</tr>
			<tr>
				<td width="150">Specimen Wgt</td>
				<td><input type="text" name="txt_speciment_wgt" id="txt_speciment_wgt" value="" class="text_boxes_numeric" style="width:100px" /></td>
			</tr>
			<tr>
				<td width="150">Speciment Length</td>
				<td><input type="text" name="txt_speciment_lenght" id="txt_speciment_lenght" value="" class="text_boxes" style="width:100px" /></td>
			</tr>
			<tr>
				<td width="150">Fabric Construction</td>
				<td><input type="text" name="txt_fabric_construction" id="txt_fabric_construction" value="" class="text_boxes" style="width:100px" /></td>
			</tr>
			<tr>
				<td width="150">Color Range</td>
				<td>
				<?
					/*if($db_type==0) $color_cond=" and color_name!=''"; else $color_cond=" and color_name IS NOT NULL";
					echo create_drop_down( "cbo_color", 111, "select id,color_name from lib_color where status_active=1 and grey_color=1 $color_cond order by color_name","id,color_name", 1, "--Select--", 0, "",0 );*/
					echo create_drop_down( "cbo_color", 111,$color_range,"", 1,"--Select--", 0, "",0 );
				?>
				</td>
			</tr>
            
		</table>
	</fieldset>
        <table width=""style="float:left">
            <td width="75">
                &nbsp;<br>
                <input type="hidden" name="cbo_company_id" id="cbo_company_id" value="<? echo $cbo_company_id;?>" class="text_boxes_numeric" style="width:50px"/> <br>
                <input type="hidden" name="prod_id" id="prod_id" value="<? echo $prod_id;?>" class="text_boxes" style="width:50px"/><br>
                <input type="hidden" name="lot_number" id="lot_number" value="<? echo $lot_number;?>"  class="text_boxes" style="width:50px"/><br>
                <input type="hidden" name="update_id" id="update_id" value="<? echo $update_id;?>"  class="text_boxes_numeric" style="width:50px"/><br>
                <input type="hidden" name="deleted_ids" id="deleted_ids" value="" class="text_boxes" style="width:50px" />
            </td>
        </table>
    <fieldset style="width:345px;float:left;" >
        <legend>Numerical Test</legend>
        <table width="100%" align="center" class="rpt_table" rules="all" cellspacing="0" cellpadding="0" border="0"  style="float:left">
            <tr>
                <td width="150">Actual Yarn Count</td>
                <td><input type="text" name="txt_actual_yarn_count" id="txt_actual_yarn_count" value="" class="text_boxes_numeric" style="width:100px" /></td>
            </tr>
            <tr>
                <td width="150">Yarn Appearance (Grade)</td>
                <td><input type="text" name="txt_yarn_appearance" id="txt_yarn_appearance" value="" class="text_boxes" style="width:100px" /></td>
            </tr>
            <tr>
                <td width="150">Twist Per Inch (TPI)</td>
                <td><input type="text" name="txt_twist_per_inch" id="txt_twist_per_inch" value="" class="text_boxes_numeric" style="width:100px" /></td>
            </tr>
            <tr>
                <td width="150">Moisture Content</td>
                <td><input type="text" name="txt_moisture_content" id="txt_moisture_content" value="" class="text_boxes_numeric" style="width:100px" /></td>
            <tr>
                <td width="150">CSP Minimum</td>
                <td><input type="text" name="txt_csp_minimum" id="txt_csp_minimum" value="" class="text_boxes_numeric" style="width:100px" /></td>
            </tr>
            <tr>
                <td width="150">CSP Actual</td>
                <td><input type="text" name="txt_csp_actual" id="txt_csp_actual" value="" class="text_boxes_numeric" style="width:100px" /></td>
            </tr>
                
            </tr>
        </table>
    </fieldset>
       
    <fieldset style="width:700px;float:left;">
        <legend>Physical Test</legend>
        <table id="tbl_physical_test" width="100%" align="center" class="rpt_table" rules="all" cellspacing="0" cellpadding="0" border="0" >
            <thead>
                <th width="42">SL</th>
                <th width="133" class="must_entry_caption">Fabric Fault</th>
                <th width="73">Point</th>
                <th width="73">Total Point</th>
                <th width="73">Point/100Y2</th>
                <th width="73">Fabric Class</th>
                <th width="143">Remarks</th>
                <th width="">Action</th>
            </thead>
            </table>
            
            <div style="width:700px; max-height:120px; overflow-y:scroll">
            <table id="physical_test_data_container" width="100%" align="center" class="rpt_table" rules="all" cellspacing="0" cellpadding="0" border="0">
                
                <?
                $i=1;
                for( $j=1; $j<=5; $j++ )
                {
                ?>
                <tr id="tr_<? echo $i; ?>" >
                    <td>
                    <input type="text" name="txtSl[]" id="txtSl_<? echo $i; ?>" value="<? echo $i; ?>" class="text_boxes" style="width:30px;text-align:center" disabled />
                    <input type="hidden" name="txtUpdateIdDtls[]" id="txtUpdateIdDtls_<? echo $i; ?>" value="" style="width:30px;" class="text_boxes_numeric" />
                    </td>
                    <td><input type="text" name="txtFabricFault[]" id="txtFabricFault_<? echo $i; ?>" value="" class="text_boxes" style="width:120px" /></td>
                    <td><input type="text" name="txtPoint[]" id="txtPoint_<? echo $i; ?>" value="" class="text_boxes_numeric" style="width:60px" /></td>
                    <td><input type="text" name="txtTotalPoint[]" id="txtTotalPoint_<? echo $i; ?>" value="" class="text_boxes_numeric" style="width:60px" /></td>
                    <td><input type="text" name="txtPointY[]" id="txtPointY_<? echo $i; ?>" value="" class="text_boxes_numeric" style="width:60px" /></td>
                    <td><input type="text" name="txtFabricClass[]" id="txtFabricClass_<? echo $i; ?>" value="" class="text_boxes" style="width:60px" /></td>
                    <td><input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $i; ?>" value="" class="text_boxes" style="width:130px" /></td>
                    <td>
                    <input type="button" name="btnAdd[]" id="btnAdd_<? echo $i; ?>" value="+" onClick="add_break_down_tr(<? echo $i; ?>)" class="formbutton" style="width:30px" />
                    <input type="button" name="btnDelete[]" id="btnDelete_<? echo $i; ?>" value="-" onClick="fn_deleteRow(<? echo $i; ?>)" class="formbutton" style="width:30px" />
                    </td>
                </tr>
                <?
                $i++;
                }
                ?>
            </table>
            </div>
    </fieldset>
    
    <fieldset style="width:700px">
        <legend>Yarn Quality Comments:</legend>
        <table cellpadding="0" cellspacing="1" width="100%">
            <tr>
                <td colspan="8">
                <textarea id="yarnQualityComments"  name="yarnQualityComments[]" class="text_area" style="height:30px; width:99%; border-style:solid; border-width:1px; border-color:#6699FF; border-radius:5px;resize: none;" maxlength="4000" cols="10" rows="1" ></textarea>
                </td>
            </tr> 
        </table> 
    </fieldset>
		<table cellpadding="0" cellspacing="1" width="700">
			<tr> 
			   <td colspan="8" align="center"></td>				
			</tr>
			<tr>
				<td align="center" colspan="6" valign="middle" class="button_container">
					 <!-- details table id for update -->
					 <input type="hidden" id="txt_prod_id" name="txt_prod_id" value="" />
					 <input type="hidden" id="update_id" name="update_id" value="" />
					 <? echo load_submit_buttons( $permission, "fnc_yarn_test_entry", 0,0,"fnResetForm()",1);?>
                     <input type="button" name="print1" id="print1" value="Print" onClick="fnc_yarn_test_report_printer(4)" style="width:80px; background-image: -moz-linear-gradient(center bottom , rgb(136, 170, 214) 7%, rgb(194, 220, 255) 10%, rgb(136, 170, 214) 96%); border: 1px outset #66cc00; border-radius: 0.7em;color: #171717;color: #171717; cursor: pointer; font-size: 13px; font-weight: bold; padding: 1px 2px;">
				</td>
		   </tr> 
		</table>  
	</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	function add_break_down_tr(i) 
	{
		//var row_num = $('#tbl_physical_test tbody tr').length;
		var row_num = $('#physical_test_data_container tr').length;
		if (row_num != i) {
			return false;
		} else {
			i++;
			//$('#samplepic_' + i).removeAttr("src,value");
			if (row_num < row_num + 1) {
				//$("#tbl_physical_test tbody tr:last").clone().find("input,select").each(function () {
				$("#physical_test_data_container tr:last").clone().find("input,select").each(function () {	
					$(this).attr({
						'id': function (_, id) {
							var id = id.split("_");
							//alert(id);
							return id[0] + "_" + i;
						},
						'value': function (_, value) {
							return value
						},
						'src': function (_, src) {
							return src
						}
					});
					
				}).end().appendTo("#physical_test_data_container");
				
				/*
				name': function (_, name) {
							var name = name.split("_");
							return name[0] + "_" + i;
						},
						'
				*/
				
				
				
				//$('#txtQty_' + i).removeAttr("onKeyup").attr("onKeyup", "calculate_total_qty()");
				//$("#tbl_physical_test tbody tr:last ").removeAttr('id').attr('id', 'tr_' + i);
				$("#physical_test_data_container tr:last ").removeAttr('id').attr('id', 'tr_' + i);
				//$("#txtqtyset_"+i).removeAttr('class','text_boxes_numeric').attr('class', 'text_boxes_numeric');
				//$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#btnAdd_' + i).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + i + ");");
				$('#btnDelete_' + i).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + i + ");");

				$('#txtSl_' + i).val(i);
				$('#txtUpdateIdDtls_' + i).val('');
				$('#txtFabricFault_' + i).val('');
				$('#txtPoint_' + i).val('');
				$('#txtTotalPoint_' + i).val('');
				$('#txtPointY_' + i).val('');
				$('#txtFabricClass_' + i).val('');
				$("#txtRemarks_" + i).val('');
				


				$('#txtPoint_' + i).attr('class', 'text_boxes_numeric');
				$('#txtTotalPoint_' + i).attr('class', 'text_boxes_numeric');
				$('#txtPointY_' + i).attr('class', 'text_boxes_numeric');
				
				//var result = parseInt(num1) + parseInt(num2);
				set_all_onclick();
			}
		}
	}

	function fn_deleteRow(rowNo) 
	{
		
		//var deleted_row="";
		var deleted_row = $("#deleted_ids").val();
		if (deleted_row != "") deleted_row = deleted_row + ",";
		//var numRow = $('#tbl_physical_test tbody tr').length;
		var numRow = $('#physical_test_data_container tr').length;
		//alert(numRow+"_"+rowNo+"_"+numRow+"_"+1);
		if (numRow == rowNo && numRow == 1) {
			return false;
		} else {
			deleted_row = deleted_row + $("#txtUpdateIdDtls_" + rowNo).val();
			$("#tr_" + rowNo).remove();
		}
		$("#deleted_ids").val(deleted_row);
	}
	
	function populate_all_data(id) 
	{
		get_php_form_data(id, "populate_all_data", "yarn_test_controller");
		var htmlResponse = return_global_ajax_value( id, 'physical_test_data', '', 'yarn_test_controller');
		$("#physical_test_data_container").html(htmlResponse);
	}
	
	window.onload = function() 
	{
		var company_id = $('#cbo_company_id').val();
		var lot_number = $('#lot_number').val();
		var prod_id = $('#prod_id').val();
		var response=return_global_ajax_value( company_id+"**"+prod_id+"**"+lot_number, 'check_product_test', '', 'yarn_test_controller');
		var response=response.split("_");
		if(response[0] == 1)
		{
			populate_all_data(response[1]);
		}
		else
		{
			
		}
		
	};
</script>
</html>
<?
exit();
}


if ($action == "populate_all_data") 
{
    //$data = explode("_", $data);
    //print_r($dara_arr); die;
    $data_array = sql_select("select id, company_id, test_date, test_for, prod_id, lot_number, specimen_wgt, specimen_length, fabric_construct, color, actual_yarn_count, yarn_apperance_grad, twist_per_inc, moisture_content, csp_minimum, csp_actual, yarn_quality_coments from inv_yarn_test_mst where status_active=1 and is_deleted=0 and id='$data'");

    foreach ($data_array as $row) {
        echo "document.getElementById('update_id').value 			= '" . $row[csf("id")] . "';\n";
        echo "document.getElementById('cbo_company_id').value 			= '" . $row[csf("company_id")] . "';\n";
        echo "document.getElementById('prod_id').value 		= '" . $row[csf("prod_id")] . "';\n";
        // echo "load_drop_down('requires/asset_acquisition_controller','" . $row[csf("company_id")] . "','load_drop_down_location','location_td' );\n";
        echo "document.getElementById('lot_number').value 		= '" . $row[csf("lot_number")] . "';\n";
        echo "document.getElementById('txt_speciment_wgt').value 				= '" . $row[csf("specimen_wgt")] . "';\n";
        echo "document.getElementById('txt_speciment_lenght').value 			= '" . $row[csf("specimen_length")] . "';\n";
        echo "document.getElementById('txt_fabric_construction').value 			= '" . $row[csf("fabric_construct")] . "';\n";
        echo "document.getElementById('cbo_color').value 			= '" . $row[csf("color")] . "';\n";
        echo "document.getElementById('txt_test_date').value 			= '" . change_date_format($row[csf("test_date")], "dd-mm-yyyy", "-") . "';\n";
        echo "document.getElementById('cbo_test_for').value 			= '" . $row[csf("test_for")] . "';\n";
        echo "document.getElementById('txt_actual_yarn_count').value 			= '" . $row[csf("actual_yarn_count")] . "';\n";
        echo "document.getElementById('txt_yarn_appearance').value	 		= '" . $row[csf("yarn_apperance_grad")] . "';\n";
        echo "document.getElementById('txt_twist_per_inch').value 			= '" . $row[csf("twist_per_inc")] . "';\n";
        echo "document.getElementById('txt_moisture_content').value 			= '" . $row[csf("moisture_content")] . "';\n";
        echo "document.getElementById('txt_csp_minimum').value 		= '" . $row[csf("csp_minimum")] . "';\n";
        echo "document.getElementById('txt_csp_actual').value 	= '" . $row[csf("csp_actual")] . "';\n";
        echo "document.getElementById('yarnQualityComments').value 		= '" . $row[csf("yarn_quality_coments")] . "';\n";
        echo "set_button_status(1, permission, 'fnc_yarn_test_entry',1);\n";
		//load_submit_buttons( $permission, "fnc_yarn_test_entry", 0,0,"fnResetForm()",1)
    }
}


if ($action == "physical_test_data") 
{
    $data = explode("**",$data);
	$sql = "select a.id, a.company_id, a.test_date, a.test_for, a.prod_id, a.lot_number, a.specimen_wgt, a.specimen_length, a.fabric_construct,  a.color, a.actual_yarn_count, a.yarn_apperance_grad, a.twist_per_inc, a.moisture_content, a.csp_minimum, a.csp_actual, a.yarn_quality_coments,
b.id as dtls_id, b.mst_id, b.fabric_fault, b.fabric_point, b.fabric_tot_point, b.fabric_point_y2, b.fabric_class, b.remarks 
from inv_yarn_test_mst a, inv_yarn_test_dtls b  
where a.id=b.mst_id and a.id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    $data_array = sql_select($sql);
	$physicalTestArr = array();
	foreach($data_array as $rows){
		$physicalTestArr[$rows[csf('dtls_id')]]['dtls_id']=$rows[csf('dtls_id')];
		$physicalTestArr[$rows[csf('dtls_id')]]['fabric_fault']=$rows[csf('fabric_fault')];
		$physicalTestArr[$rows[csf('dtls_id')]]['fabric_point']=$rows[csf('fabric_point')];
		$physicalTestArr[$rows[csf('dtls_id')]]['fabric_tot_point']=$rows[csf('fabric_tot_point')];
		$physicalTestArr[$rows[csf('dtls_id')]]['fabric_point_y2']=$rows[csf('fabric_point_y2')];
		$physicalTestArr[$rows[csf('dtls_id')]]['fabric_class']=$rows[csf('fabric_class')];
		$physicalTestArr[$rows[csf('dtls_id')]]['remarks']=$rows[csf('remarks')];
	}
	
	$i=1;
	
	foreach($physicalTestArr as $ids => $rows)
	{
	?>
	<tr id="tr_<? echo $i; ?>" >
		<td>
		<input type="text" name="txtSl[]" id="txtSl_<? echo $i; ?>" value="<? echo $i; ?>" class="text_boxes" style="width:30px;text-align:center" disabled />
		<input type="hidden" name="txtUpdateIdDtls[]" id="txtUpdateIdDtls_<? echo $i; ?>" value="<? echo $ids; ?>" style="width:30px;" class="text_boxes_numeric" />
		</td>
		<td><input type="text" name="txtFabricFault[]" id="txtFabricFault_<? echo $i; ?>" value="<? echo $rows['fabric_fault']; ?>" class="text_boxes" style="width:120px" /></td>
		<td><input type="text" name="txtPoint[]" id="txtPoint_<? echo $i; ?>" value="<? echo $rows['fabric_point']; ?>" class="text_boxes_numeric" style="width:60px" /></td>
		<td><input type="text" name="txtTotalPoint[]" id="txtTotalPoint_<? echo $i; ?>" value="<? echo $rows['fabric_tot_point']; ?>" class="text_boxes_numeric" style="width:60px" /></td>
		<td><input type="text" name="txtPointY[]" id="txtPointY_<? echo $i; ?>" value="<? echo $rows['fabric_point_y2']; ?>" class="text_boxes_numeric" style="width:60px" /></td>
		<td><input type="text" name="txtFabricClass[]" id="txtFabricClass_<? echo $i; ?>" value="<? echo $rows['fabric_class']; ?>" class="text_boxes" style="width:60px" /></td>
		<td><input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $i; ?>" value="<? echo $rows['remarks']; ?>" class="text_boxes" style="width:130px" /></td>
		<td>
		<input type="button" name="btnAdd[]" id="btnAdd_<? echo $i; ?>" value="+" onClick="add_break_down_tr(<? echo $i; ?>)" class="formbutton" style="width:30px" />
		<input type="button" name="btnDelete[]" id="btnDelete_<? echo $i; ?>" value="-" onClick="fn_deleteRow(<? echo $i; ?>)" class="formbutton" style="width:30px" />
		</td>
	</tr>
	<?
	$i++;
	}
}


if ($action == "check_product_test") 
{
	//company_id+"**"+prod_id+"**"+lot_number
    $data = explode("**",$data);
    $sql = "select id,company_id, prod_id, lot_number from inv_yarn_test_mst where  status_active=1 and is_deleted=0 and company_id='$data[0]' and prod_id='$data[1]' and lot_number='$data[2]'";
   //echo $sql; die;
    $data_array = sql_select($sql, 1);
    if (count($data_array) > 0) {
        echo "1_".$data_array[0][csf('id')];
    } else {
        echo "0";
    }
    exit();
}


if ($action == "yarn_test_report")
{
	extract($_REQUEST);
	echo load_html_head_contents("Yarn Issue Challan Print", "../../../", 1, 1, '', '', '');
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
	foreach( $result as $prod_val){
		
		$compositionDetails = $composition[$prod_val[csf("yarn_comp_type1st")]] . " " . $prod_val[csf("yarn_comp_percent1st")] . "%\n";
		if ($prod_val[csf("yarn_comp_type2nd")] != 0)
			$compositionDetails .= $composition[$prod_val[csf("yarn_comp_type2nd")]] . " " . $prod_val[csf("yarn_comp_percent2nd")] . "%";
			
		$product_array[$prod_val[csf('lot')]]['count']			= $prod_val[csf('yarn_count_id')];
		$product_array[$prod_val[csf('lot')]]['composition']	= $compositionDetails;
		$product_array[$prod_val[csf('lot')]]['color']			= $prod_val[csf('color')];
		$product_array[$prod_val[csf('lot')]]['yarn_type']		= $prod_val[csf('yarn_type')];
		$product_array[$prod_val[csf('lot')]]['supplier_id']	= $prod_val[csf('supplier_id')];
	}
	//echo "<pre>";
	//print_r($product_array);//die;
	
	$sql = "select a.id, a.company_id, a.test_date, a.test_for, a.prod_id, a.lot_number, a.specimen_wgt, a.specimen_length, a.fabric_construct, a.color, a.actual_yarn_count, a.yarn_apperance_grad, a.twist_per_inc, a.moisture_content, a.csp_minimum, a.csp_actual, a.yarn_quality_coments, 
	b.id as dtls_id, b.fabric_fault, b.fabric_point, b.fabric_tot_point, b.fabric_point_y2, b.fabric_class, b.remarks 
	from inv_yarn_test_mst a, inv_yarn_test_dtls b 
	where a.id=b.mst_id and a.company_id='$data[0]' and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
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
                            <img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50'
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
                    <td width="35" align="center">4</td><td >Color Range</td><td  style="text-align:center;"><? echo $color_range[$data_arr[0][csf('color')]]; ?></td>
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
                    <tr valign="middle"  height="30">
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
            <table cellspacing="0" width="100%" border="0">
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


if ($action=="SaveUpdateDeleteTestData")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	
	if($operation==0)	// Save Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
			
		//$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
		$id = return_next_id("id", "inv_yarn_test_mst", 1);
		
		
		
		$field_array="id,company_id,test_date,test_for,prod_id,lot_number,specimen_wgt,specimen_length,fabric_construct,color,actual_yarn_count,yarn_apperance_grad,twist_per_inc,moisture_content,csp_minimum,csp_actual,yarn_quality_coments,inserted_by,insert_date";
		$data_array="(".$id.",".$cbo_company_id.",".$txt_test_date.",".$cbo_test_for.",".$prod_id.",".$lot_number.",".$txt_speciment_wgt.",".$txt_speciment_lenght.",".$txt_fabric_construction.",".$cbo_color.",".$txt_actual_yarn_count.",".$txt_yarn_appearance.",".$txt_twist_per_inch.",".$txt_moisture_content.",".$txt_csp_minimum.",".$txt_csp_actual.",".$yarnQualityComments.",".$user_id.",'".$pc_date_time."')";
		
		
		$field_array_dtls="id, mst_id, fabric_fault, fabric_point, fabric_tot_point, fabric_point_y2, fabric_class, remarks, inserted_by, insert_date";
		
		//echo "10**$physicalTest";die;
		
		//$dataDtlsArr = array(); 
		//$colorArr = array(); 
		//$update_data = array();
		//$tot_row = explode("*",$physicalTest);
		$data_array_dtls="";
		$row_data_arr = explode("*",$physicalTest);
		$dtls_id = return_next_id("id", "inv_yarn_test_dtls", 1);
		foreach($row_data_arr as $row_data)
		{
			$val = explode("_",$row_data);
			if($val[1] != '')
			{
				if ($data_array_dtls != "")  $data_array_dtls.=",";
				
				$data_array_dtls .="(".$dtls_id.",".$id.",'".$val[1]."','".$val[2]."','".$val[3]."','".$val[4]."','".$val[5]."','".$val[6]."',".$user_id.",'".$pc_date_time."')";
					
				$dtls_id++;
			}
		}
		
		
		$rID = sql_insert("inv_yarn_test_mst",$field_array,$data_array,1);
		//echo "10**insert into inv_yarn_test_mst ($field_array) values $data_array";die;
        $rID1 = sql_insert("inv_yarn_test_dtls", $field_array_dtls, $data_array_dtls, 1);
		//echo "10**insert into inv_yarn_test_dtls ($field_array_dtls) values $data_array_dtls";die;
		
		
		
		//echo "10**$rID**$rID1";die;
		
		
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "0**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1)
			{
				oci_commit($con);   
				echo "0**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		
		
		
		$field_arr_mst_update="test_date*test_for*prod_id*lot_number*specimen_wgt*specimen_length*fabric_construct*color*actual_yarn_count*yarn_apperance_grad*twist_per_inc*moisture_content*csp_minimum*csp_actual*yarn_quality_coments*updated_by*update_date";
		$data_array_mst_update="".$txt_test_date."*".$cbo_test_for."*".$prod_id."*".$lot_number."*".$txt_speciment_wgt."*".$txt_speciment_lenght."*".$txt_fabric_construction."*".$cbo_color."*".$txt_actual_yarn_count."*".$txt_yarn_appearance."*".$txt_twist_per_inch."*".$txt_moisture_content."*".$txt_csp_minimum."*".$txt_csp_actual."*".$yarnQualityComments."*".$user_id."*'".$pc_date_time."'";
		//echo "10**".$physicalTest;die;
		
		$field_array_dtls = "id, mst_id, fabric_fault, fabric_point, fabric_tot_point, fabric_point_y2, fabric_class, remarks, inserted_by, insert_date";
		
		$field_array_dtls_update = "fabric_fault*fabric_point*fabric_tot_point*fabric_point_y2*fabric_class*remarks*updated_by*update_date";
		
		$update_dtls_ids_arr	=	array();
		$data_array_dtls_update	=	array();
		$data_array_dtls="";
		
		$dtls_id = return_next_id("id", "inv_yarn_test_dtls", 1);
		
		$row_data_arr = explode("*",$physicalTest);
		foreach($row_data_arr as $row_data)
		{
			$val = explode("_",$row_data);
			$update_id_dtls = $val[0];
			
			if($update_id_dtls != "")
			{
				 $update_dtls_ids_arr[] = $update_id_dtls;

                //$data_array_dtls_update[$update_id_dtls] = "'" . $val[1] . "'*'" . $val[2] . "'*'" . $val[3] . "'*'" . $val[4] . "'*'" . $val[5] . "'*'" . $val[6]."'*'". $_SESSION['logic_acc']['user_id'] . "'*'" . $pc_date_time . "'";
				 $data_array_dtls_update[$update_id_dtls]=explode("*",("'" . $val[1] . "'*'" . $val[2] . "'*'" . $val[3] . "'*'" . $val[4] . "'*'" . $val[5] . "'*'" . $val[6]."'*'". $user_id . "'*'" . $pc_date_time . "'"));
			}
			else
			{
				if ($data_array_dtls != "") $data_array_dtls.=",";
				$data_array_dtls .="(".$dtls_id.",".$update_id.",'".$val[1]."','".$val[2]."','".$val[3]."','".$val[4]."','".$val[5]."','".$val[6]."',".$user_id.",'".$pc_date_time."')";
				$dtls_id++;
			}
		}
		
		
		//echo "10**<pre>";
		//print_r($data_array_dtls_update);die;
		//echo "10**".$deleted_ids;die;
		
		$rID=$rID1=$rID2=$rID3=1;
		
		
		//echo "5** insert into inv_yarn_bag_receive_barcode ($field_array_barcode) values $data_array_barcode";die;
		$rID=sql_update("inv_yarn_test_mst",$field_arr_mst_update,$data_array_mst_update,"id",$update_id,1);		
		
		if( $data_array_dtls != "")
		{
			$rID1 = sql_insert("inv_yarn_test_dtls", $field_array_dtls, $data_array_dtls, 1);
		}
		
		
		if( count($data_array_dtls_update) > 0 )
		{
			$rID2 = execute_query(bulk_update_sql_statement("inv_yarn_test_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $update_dtls_ids_arr, 0), 1);
		}
		//echo "10**".bulk_update_sql_statement("inv_yarn_test_dtls","id",$field_array_dtls_update,$data_array_dtls_update,$update_dtls_ids_arr); die;
		
		//echo "10**".$deleted_ids; die;
		if(str_replace("'","",$deleted_ids) != "")
		{
			$field_array_delete = "status_active*is_deleted*updated_by*update_date";
        	$data_array_delete  = "'2'*'1'*'" . $user_id . "'*'" . $pc_date_time . "'";
			$dtlsIds = str_replace("'","",$deleted_ids);
			$rID3 = sql_multirow_update("inv_yarn_test_dtls", $field_array_delete, $data_array_delete, "id", $dtlsIds, 1);
		}
		
		//echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3; die;
		
		if($db_type==0)
		{
			if( $rID && $rID1 && $rID2 && $rID3 )
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if( $rID && $rID1 && $rID2 && $rID3 )
			{
				oci_commit($con); 
				echo "1**".str_replace("'", '', $update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		
		$field_array_delete = "status_active*is_deleted*updated_by*update_date";
        $data_array_delete  = "'2'*'1'*'" . $user_id . "'*'" . $pc_date_time . "'";
		
		$update_id = str_replace("'", "", $update_id);	
		$rID = sql_delete("inv_yarn_test_mst", $field_array_delete, $data_array_delete, "id",$update_id, 1);
        $rID1 = sql_delete("inv_yarn_test_dtls", $field_array_delete, $data_array_delete, "mst_id",$update_id, 1);
		
		
		//echo "10**".$rID."**".$rID1."**".$update_id; die;
		
		if($db_type==0)
		{
			if( $rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'", '', $update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if( $rID && $rID1 )
			{
				oci_commit($con); 
				echo "2**".str_replace("'", '', $update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**0";
			}
		}
		disconnect($con);
		die;
	}
}

?>