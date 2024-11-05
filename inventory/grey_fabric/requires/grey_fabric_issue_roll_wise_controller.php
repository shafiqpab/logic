<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php"); 

include('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_knitting_com") {
	$data = explode("**", $data);
	$company_id = $data[1];

	if ($data[0] == 1) {
		echo create_drop_down("cbo_dyeing_comp", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select --", $company_id, "", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_dyeing_comp", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "");
	} else {
		echo create_drop_down("cbo_dyeing_comp", 152, $blank_array, "", 1, "-- Select --", 0, "");
	}
	exit();
}

if ($action == "mix_po_variable_settings") {
	$variable_inventory = return_field_value("allocation", "variable_settings_inventory", "company_name='$data' and variable_list=40 and status_active=1 and is_deleted=0");
	echo $variable_inventory;
	exit();
}

if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	//$avg_product_rate_arr=return_library_array( "select id, avg_rate_per_unit from product_details_master",'id','avg_rate_per_unit');

	for ($k = 1; $k <= $tot_row; $k++) {
		$productId = "productId_" . $k;
		$prod_ids .= $$productId . ",";
	}

	$prod_ids = implode(",", array_unique(explode(",", chop($prod_ids, ","))));
	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in( $prod_ids) and transaction_type in (1,4,5) and status_active=1 and is_deleted=0", "max_date");
	if ($max_recv_date != "") {
		$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
		if ($issue_date < $max_recv_date) {
			echo "20**Issue Date Can not Be Less Than Last Receive Date Of These Lot";
			die;
		}
	}

	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		// ================Start
		for ($k = 1; $k <= $tot_row; $k++) {
			$barcodeNo = "barcodeNo_" . $k;
			$all_barcodeNo .= $$barcodeNo . ",";
		}
		// $barcodeNOS=implode(",",array_unique(explode(",",chop($all_barcodeNo,',')))); cbo_company_id
		$all_barcodeNo = chop($all_barcodeNo, ',');
		$all_barcodeNo_arr = explode(",", $all_barcodeNo);

		if ($all_barcodeNo != "") {
			$all_barcodeNo_arr = array_filter($all_barcodeNo_arr);
			if (count($all_barcodeNo_arr) > 0) {
				$barcod_NOs = implode(",", $all_barcodeNo_arr);
				$all_barcode_no_cond = "";
				$barCond = "";
				if ($db_type == 2 && count($all_barcodeNo_arr) > 999) {
					$all_barcodeNo_arr_chunk = array_chunk($all_barcodeNo_arr, 999);
					foreach ($all_barcodeNo_arr_chunk as $chunk_arr) {
						$chunk_arr_value = implode(",", $chunk_arr);
						$barCond .= " a.barcode_no in($chunk_arr_value) or ";
					}

					$all_barcode_no_cond .= " and (" . chop($barCond, 'or ') . ")";
				} else {
					$all_barcode_no_cond = " and a.barcode_no in($barcod_NOs)";
				}
			}
		}

		$check_if_already_scanned = sql_select("select a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 61 and  a.entry_form=61 and a.is_returned!=1 $all_barcode_no_cond and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0");

		if ($check_if_already_scanned[0][csf("barcode_no")] != "") {
			echo "20**Sorry! Barcode already Scanned. Challan No: " . $check_if_already_scanned[0][csf("issue_number")] . " Barcode No " . $$barcodeNo;
			die;
		}

		$trans_check_sql = sql_select("select a.barcode_no, a.entry_form, a.po_breakdown_id, a.qnty from pro_roll_details a where a.entry_form in ( 22,58,83,133,82,180,110,183,84) $all_barcode_no_cond and a.re_transfer =0 and a.status_active = 1 and a.is_deleted = 0 union all select a.barcode_no, a.entry_form, a.po_breakdown_id, a.qnty from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.entry_form in (2) and b.trans_id<>0 and a.re_transfer =0 $all_barcode_no_cond and a.status_active = 1 and a.is_deleted = 0");

		if ($trans_check_sql[0][csf("barcode_no")] != "") {
			foreach ($trans_check_sql as $val) {
				$trans_po_barcode_check_arr[$val[csf("barcode_no")]][$val[csf("po_breakdown_id")]] = $val[csf("barcode_no")] . "__" . $val[csf("po_breakdown_id")];
				$actual_wgt_arr[$val[csf("barcode_no")]] = $val[csf("qnty")];
			}
		}

		/*$proceed_knitting_chk = sql_select("SELECT a.barcode_no, e.proceed_knitting, e.proceed_dyeing
		from pro_roll_details a, inv_receive_master p, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst d ,wo_booking_mst e
		where a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.is_sales!=1 and p.entry_form=2 and p.receive_basis =2 and p.status_active=1 and p.is_deleted=0 and p.id=a.mst_id and p.booking_id=b.id and b.mst_id=d.id and e.booking_no = d.booking_no and e.proceed_knitting=1 and e.proceed_dyeing=2 $all_barcode_no_cond");

		foreach ($proceed_knitting_chk as $row)
		{
			$proceed_knitting_only[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		}
		unset($proceed_knitting_chk);

		if(!empty($proceed_knitting_only)){
			echo "20**Only Proceed for Knitting Check Found at Fabric booking Page. So Roll Can Not Issue of This Program barcode.\nBarcode no/s :".implode(',', $proceed_knitting_only);
			disconnect($con);
			die;
		}*/

		$proceed_knitting_sql = sql_select("SELECT a.barcode_no, c.proceed_knitting, c.proceed_dyeing, a.is_sales, b.booking_no
		from pro_roll_details a, wo_booking_dtls b, wo_booking_mst c
		where  a.po_breakdown_id=b.po_break_down_id and b.booking_no = c.booking_no and a.booking_without_order=0 and a.is_sales !=1 $all_barcode_no_cond and a.re_transfer=0 and c.booking_type =1 and c.is_short=2 and a.entry_form in (22,58,84,82,83,183) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and c.proceed_knitting=1 and c.proceed_dyeing=2 and a.is_sales!=1
		group by a.barcode_no, c.proceed_knitting, c.proceed_dyeing, a.is_sales, b.booking_no");
		foreach ($proceed_knitting_sql as $key => $row) {
			$proceed_knitting_only[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		}

		if (!empty($proceed_knitting_only)) {
			echo "20**Only Proceed for Knitting Check Found at Fabric booking Page. So Roll Can Not Issue of This Program barcode.\nBarcode no/s :" . implode(',', $proceed_knitting_only);
			disconnect($con);
			die;
		}
		// echo "20**proceed_knitting check";die;

		// ======================end

		if ($db_type == 0)
			$year_cond = "YEAR(insert_date)";
		else if ($db_type == 2)
			$year_cond = "to_char(insert_date,'YYYY')";
		else
			$year_cond = ""; //defined Later

		//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'KGIR', date("Y",time()), 5, "select issue_number_prefix,issue_number_prefix_num from inv_issue_master where company_id=$cbo_company_id and entry_form=61 and $year_cond=".date('Y',time())." order by id desc ", "issue_number_prefix","issue_number_prefix_num"));
		//$id=return_next_id( "id", "inv_issue_master", 1 ) ;

		/*
		|--------------------------------------------------------------------------
		| inv_issue_master
		| data preparing for
		| $data_array
		|--------------------------------------------------------------------------
		|
		*/
		$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
		$new_mrr_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con, 1, $cbo_company_id, "KGIR", 61, date("Y", time()), 13));
		$data_array = "(" . $id . ",'" . $new_mrr_number[1] . "'," . $new_mrr_number[2] . ",'" . $new_mrr_number[0] . "',0," . $cbo_issue_purpose . ",61,13," . $cbo_company_id . "," . $txt_batch_id . "," . $txt_issue_date . "," . $cbo_dyeing_source . "," . $cbo_dyeing_comp . "," . $txt_remarks . "," . $txt_attention . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

		/*echo "10**Failed";
		print_r($new_mrr_number);
		die;*/

		$barcodeNos = '';
		$all_prod_id = '';
		for ($j = 1; $j <= $tot_row; $j++) {
			$recvBasis = "recvBasis_" . $j;
			$barcodeNo = "barcodeNo_" . $j;
			$progBookPiId = "progBookPiId_" . $j;
			$productId = "productId_" . $j;
			$orderId = "orderId_" . $j;
			$rollId = "rollId_" . $j;
			$rollWgt = "rollWgt_" . $j;
			$hiddenQtyInPcs = "hiddenQtyInPcs_" . $j;

			$yarnLot = "yarnLot_" . $j;
			$yarnCount = "yarnCount_" . $j;
			$colorId = "colorId_" . $j;
			$stichLn = "stichLn_" . $j;
			$brandId = "brandId_" . $j;

			$floor = "floorId_" . $j;
			$room = "roomId_" . $j;

			$rack = "rack_" . $j;
			$shelf = "shelf_" . $j;
			$bin = "bin_" . $j;

			$rollNo = "rollNo_" . $j;
			$locationId = "locationId_" . $j;
			$machineId = "machineId_" . $j;
			$roll_rate = "rollRate_" . $j;
			$issueRtnRollId = "issueRtnRollId_" . $j;
			$bookWithoutOrder = "bookWithoutOrder_" . $j;
			$smnBooking = "smnBooking_" . $j;
			$isSalesOrder = "isSalesOrder_" . $j;
			$orderNo = "orderNo_" . $j;
			$storeId = "storeId_" . $j;
			$bodyPartId = "bodyPartId_" . $j;

			$yarnRate = "yarnRate_" . $j;
			$knittingCharge = "knittingCharge_" . $j;

			$cons_rate = str_replace("'", "", $$roll_rate);
			$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;

			//$cons_rate=$$roll_rate;
			//$cons_amount=$cons_rate*$$rollWgt;

			// ==============================start
			if ($trans_po_barcode_check_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$orderId)] != str_replace("'", "", $$barcodeNo) . "__" . str_replace("'", "", $$orderId)) {
				if ($$bookWithoutOrder == 1) {
					echo "20**Sorry! This barcode " . $$barcodeNo . " doesn't belong to this booking " . $$smnBooking . "";
				} else {
					echo "20**Sorry! This barcode " . $$barcodeNo . " doesn't belong to this order/FSO " . $$orderNo . "";
				}
				disconnect($con);
				die;
			}
			if (number_format($actual_wgt_arr[str_replace("'", "", $$barcodeNo)], 2, ".", "")  != number_format($$rollWgt, 2, ".", "")) {
				echo "20**Sorry! This barcode (" . $$barcodeNo . ") is split. actual weight " . number_format($actual_wgt_arr[str_replace("'", "", $$barcodeNo)], 2, ".", "") . " doesn't match with current " . $$rollWgt . "";
				disconnect($con);
				die;
			}
			// =============================end
			// echo "20**Failed";die;
			/*
			|--------------------------------------------------------------------------
			| inv_transaction
			| data preparing for inv_grey_fabric_issue_dtls
			| $data_array_trans
			|--------------------------------------------------------------------------
			|
			*/
			$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			if ($data_array_trans != "") $data_array_trans .= ",";
			$data_array_trans .= "(" . $transactionID . "," . $id . ",'" . $$recvBasis . "','" . $$progBookPiId . "'," . $cbo_company_id . ",'" . $$productId . "',13,2," . $txt_issue_date . ",'" . $$rollWgt . "','" . $cons_rate . "','" . $cons_amount . "','" . $$brandId . "','" . $$locationId . "','" . $$machineId . "','" . $$stichLn . "','" . $$floor . "','" . $$room . "','" . $$rack . "','" . $$shelf . "','" . $$bin . "','" . $$storeId . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . $$bodyPartId . "')";

			/*
			|--------------------------------------------------------------------------
			| inv_grey_fabric_issue_dtls
			| data preparing for
			| $data_array_dtls
			|--------------------------------------------------------------------------
			|
			*/
			$dtls_id = return_next_id_by_sequence("INV_GREY_FAB_ISS_DTLS_PK_SEQ", "inv_grey_fabric_issue_dtls", $con);
			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $dtls_id . "," . $id . "," . $transactionID . ",'" . $$recvBasis . "','" . $$progBookPiId . "','" . $$productId . "','" . $$rollWgt . "','" . $cons_rate . "','" . $cons_amount . "','" . $$colorId . "','" . $$locationId . "','" . $$machineId . "','" . $$stichLn . "','" . $$yarnLot . "','" . $$yarnCount . "','" . $$brandId . "','" . $$floor . "','" . $$room . "','" . $$rack . "','" . $$shelf . "','" . $$bin . "','" . $$storeId . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$hiddenQtyInPcs . ",'" . $$bodyPartId . "','" . $$yarnRate . "','" . $$knittingCharge . "')";

			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| data preparing for
			| $data_array_roll
			|--------------------------------------------------------------------------
			|
			*/
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			if ($data_array_roll != "") $data_array_roll .= ",";
			$data_array_roll .= "(" . $id_roll . ",'" . $$barcodeNo . "'," . $id . "," . $dtls_id . ",'" . $$orderId . "',61,'" . $$rollWgt . "','" . $cons_rate . "','" . $cons_amount . "','" . $$rollNo . "','" . $$rollId . "','" . $$bookWithoutOrder . "','" . $$smnBooking . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$isSalesOrder . "," . $$hiddenQtyInPcs . ")";

			/*
			|--------------------------------------------------------------------------
			| order_wise_pro_details
			| data preparing for
			| $data_array_prop
			|--------------------------------------------------------------------------
			|
			*/
			if ($$bookWithoutOrder != 1) {
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				if ($data_array_prop != "") $data_array_prop .= ",";
				$data_array_prop .= "(" . $id_prop . "," . $transactionID . ",2,61,'" . $dtls_id . "','" . $$orderId . "','" . $$productId . "','" . $$rollWgt . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$isSalesOrder . "," . $$hiddenQtyInPcs . ")";
				//$id_prop = $id_prop+1;
			}

			$barcodeNos .= $$barcodeNo . "__" . $dtls_id . "__" . $transactionID . "__" . $id_roll . ",";
			$prodData_array[$$productId] += $$rollWgt;
			$prodData_amount[$$productId] += $cons_amount;
			$all_prod_id .= $$productId . ",";

			$inserted_roll_id_arr[$id_roll] =  $id_roll;
			$new_inserted[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);
		}
		//echo $data_array_dtls."***".$data_array_trans."***".$data_array_roll;die;
		//echo "10**insert into inv_grey_fabric_issue_dtls  ($field_array_dtls) values ($data_array_dtls)";die;

		/*
		|--------------------------------------------------------------------------
		| product_details_master
		| data preparing for
		| $data_array_prod_update
		|--------------------------------------------------------------------------
		|
		*/
		$prod_id_array = array();
		$all_prod_id = implode(",", array_unique(explode(",", substr($all_prod_id, 0, -1))));
		$prodResult = sql_select("select id, current_stock,stock_value,avg_rate_per_unit from product_details_master where id in($all_prod_id)");
		foreach ($prodResult as $row) {
			$issue_qty = $prodData_array[$row[csf('id')]];
			$issue_amount = $prodData_amount[$row[csf('id')]];
			$current_stock = $row[csf('current_stock')] - $issue_qty;
			$stock_value = $row[csf('stock_value')] - $issue_amount;
			if ($current_stock > 0) {
				$avg_rate = $stock_value / $current_stock;
			} else {
				$avg_rate = 0;
			}

			if (is_nan($avg_rate))
				$avg_rate = 0;
			// if Qty is zero then rate & value will be zero
			if ($current_stock <= 0) {
				$stock_value = 0;
				$avg_rate = 0;
			}

			$prod_id_array[] = $row[csf('id')];
			$data_array_prod_update[$row[csf('id')]] = explode("*", ($prodData_array[$row[csf('id')]] . "*'" . $current_stock . "'*'" . $stock_value . "'*'" . $avg_rate . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
		}

		/*
		|--------------------------------------------------------------------------
		| inv_issue_master
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array = "id,issue_number_prefix,issue_number_prefix_num,issue_number,issue_basis,issue_purpose,entry_form,item_category,company_id,batch_no,issue_date,knit_dye_source, knit_dye_company,remarks,attention,inserted_by,insert_date";
		$rID = sql_insert("inv_issue_master", $field_array, $data_array, 0);

		/*
		|--------------------------------------------------------------------------
		| inv_transaction
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_trans = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,prod_id,item_category,transaction_type,transaction_date, cons_quantity,cons_rate,cons_amount,brand_id,location_id,machine_id,stitch_length,floor_id,room,rack,self,bin_box,store_id,inserted_by,insert_date,body_part_id";
		$rID2 = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);

		/*
		|--------------------------------------------------------------------------
		| inv_grey_fabric_issue_dtls
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_dtls = "id,mst_id,trans_id,basis,program_no,prod_id,issue_qnty,rate, amount,color_id,location_id,machine_id,stitch_length,yarn_lot,yarn_count,brand_id,floor_id,room,rack,self,bin_box,store_name,inserted_by,insert_date,qty_in_pcs,body_part_id,yarn_rate,kniting_charge";
		$rID3 = sql_insert("inv_grey_fabric_issue_dtls", $field_array_dtls, $data_array_dtls, 0);

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_roll = "id,barcode_no,mst_id,dtls_id, po_breakdown_id,entry_form,qnty,rate,amount,roll_no,roll_id,booking_without_order,booking_no,inserted_by,insert_date,is_sales,qc_pass_qnty_pcs";
		$rID4 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);

		/*
		|--------------------------------------------------------------------------
		| order_wise_pro_details
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$rID5 = true;
		if ($data_array_prop != "") {
			$field_array_prop = "id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,quantity,inserted_by,insert_date,is_sales,quantity_pcs";
			$rID5 = sql_insert("order_wise_pro_details", $field_array_prop, $data_array_prop, 0);
		}

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| is_returned data updating here
		|--------------------------------------------------------------------------
		|
		*/
		$rID6 = execute_query("update pro_roll_details set is_returned=1 where barcode_no in (" . implode(',', $new_inserted) . ") and id not in (" . implode(',', $inserted_roll_id_arr) . ")");

		/*
		|--------------------------------------------------------------------------
		| product_details_master
		| data updating here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_prod_update = "last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
		$prodUpdate = execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array));

		//echo "10**insert into inv_grey_fabric_issue_dtls  ($field_array_dtls) values ($data_array_dtls)";die;
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$rID6."&&".$prodUpdate;die;

		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate) {
				mysql_query("COMMIT");
				echo "0**" . $id . "**" . $new_mrr_number[0] . "**" . substr($barcodeNos, 0, -1);
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate) {
				oci_commit($con);
				echo "0**" . $id . "**" . $new_mrr_number[0] . "**" . substr($barcodeNos, 0, -1);
			} else {
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 1)   // Update Here
	{
		$con = connect();
		$check_in_gate_pass = return_field_value("sys_number", "inv_gate_pass_mst", "challan_no in($txt_issue_no) and status_active=1 and is_deleted=0", "sys_number");
		if ($check_in_gate_pass != "") {
			echo "20**Gate Pass found.\nGate Pass ID = $check_in_gate_pass";
			disconnect($con);
			die;
		}

		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		/*
         * List of fields that will not change/update on update button event
         * fields=> issue_purpose*knit_dye_source*knit_dye_company*
         * data=> $cbo_issue_purpose."*".$cbo_dyeing_source."*".$cbo_dyeing_comp."*".
         */

		/*$sql_nxt_process = sql_select("select count(b.barcode_no)  rcv_by_batch from pro_roll_details b,
        	(select barcode_no from pro_roll_details where mst_id = $update_id and entry_form = 61 and status_active =1) a
        	where b.barcode_no = a.barcode_no and b.entry_form =62 and status_active =1");*/
		$sql_get_barcode = sql_select("select barcode_no from pro_roll_details where mst_id = $update_id and entry_form = 61 and status_active =1");

		$allBarcode = "";
		foreach ($sql_get_barcode as $row) {
			$allBarcode .= $row[csf("barcode_no")] . ",";
		}
		$allBarcode = rtrim($allBarcode, ",");
		$allBarcodes = explode(",", $allBarcode);
		$allBarcodes = array_chunk($allBarcodes, 999);
		$barcode_cond = " and";
		foreach ($allBarcodes as $all_barcodes) {
			if ($barcode_cond == " and")  $barcode_cond .= "(barcode_no in(" . implode(',', $all_barcodes) . ")";
			else $barcode_cond .= " or barcode_no in(" . implode(',', $all_barcodes) . ")";
		}
		$barcode_cond .= ")";
		//echo $pi_qnty_cond;die;
		$sql_nxt_process = sql_select("select count(b.barcode_no) rcv_by_batch from pro_roll_details b where b.entry_form =62 and b.status_active =1 $barcode_cond");

		if ($sql_nxt_process[0][csf("rcv_by_batch")] > 0) {
			$source_company_field = "";
			$source_company_value = "";
		} else {
			$source_company_field = "knit_dye_source*knit_dye_company*";
			$source_company_value = $cbo_dyeing_source . "*" . $cbo_dyeing_comp . "*";
		}

		/*
         * List of fields that will not change/update on update event
         * fields=>company_id,
         * data=> $cbo_company_id.",'".
         */

		$barcodeNos = '';
		$all_prod_id = '';
		$all_roll_id = '';
		$all_scanned_barcode_no = chop($new_barcode_nos, ",");
		$all_scanned_barcode_arr = array_filter(explode(",", $all_scanned_barcode_no));
		$scannedNewBarcodeCond = "";
		$barCond = "";
		if ($db_type == 2 && count($all_scanned_barcode_arr) > 999) {
			$all_scanned_barcode_chunk = array_chunk($all_scanned_barcode_arr, 999);
			foreach ($all_scanned_barcode_chunk as $chunk_arr) {
				$chunk_arr_value = implode(",", $chunk_arr);
				$barCond .= "  a.barcode_no in($chunk_arr_value) or ";
			}

			$scannedNewBarcodeCond .= " and (" . chop($barCond, 'or ') . ")";
		} else {
			$scannedNewBarcodeCond = " and a.barcode_no in($all_scanned_barcode_no)";
		}

		if ($all_scanned_barcode_no) {
			$check_if_already_scanned = sql_select("select a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 61 and  a.entry_form=61 and a.is_returned!=1 $scannedNewBarcodeCond and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0");

			if ($check_if_already_scanned[0][csf("barcode_no")] != "") {
				echo "20**Sorry! Barcode already Scanned. Challan No: " . $check_if_already_scanned[0][csf("issue_number")] . " Barcode No " . $check_if_already_scanned[0][csf("barcode_no")];
				disconnect($con);
				die;
			}

			/*$proceed_knitting_chk = sql_select("SELECT a.barcode_no, e.proceed_knitting, e.proceed_dyeing
			from pro_roll_details a, inv_receive_master p, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst d ,wo_booking_mst e
			where a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.is_sales!=1 and p.entry_form=2 and p.receive_basis =2 and p.status_active=1 and p.is_deleted=0 and p.id=a.mst_id and p.booking_id=b.id and b.mst_id=d.id and e.booking_no = d.booking_no and e.proceed_knitting=1 and e.proceed_dyeing=2 $scannedNewBarcodeCond");

			foreach ($proceed_knitting_chk as $row)
			{
				$proceed_knitting_only[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
			}
			unset($proceed_knitting_chk);

			if(!empty($proceed_knitting_only)){
				echo "20**Only Proceed for Knitting Check Found at Fabric booking Page. So Roll Can Not Issue of This Program barcode.\nBarcode no/s :".implode(',', $proceed_knitting_only);
				disconnect($con);
				die;
			}*/

			$proceed_knitting_sql = sql_select("SELECT a.barcode_no, c.proceed_knitting, c.proceed_dyeing, a.is_sales, b.booking_no
			from pro_roll_details a, wo_booking_dtls b, wo_booking_mst c
			where  a.po_breakdown_id=b.po_break_down_id and b.booking_no = c.booking_no and a.booking_without_order=0 and a.is_sales !=1 $scannedNewBarcodeCond and a.re_transfer=0 and c.booking_type =1 and c.is_short=2 and a.entry_form in (22,58,84,82,83,183) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and c.proceed_knitting=1 and c.proceed_dyeing=2 and a.is_sales!=1
			group by a.barcode_no, c.proceed_knitting, c.proceed_dyeing, a.is_sales, b.booking_no");
			foreach ($proceed_knitting_sql as $key => $row) {
				$proceed_knitting_only[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
			}

			if (!empty($proceed_knitting_only)) {
				echo "20**Only Proceed for Knitting Check Found at Fabric booking Page. So Roll Can Not Issue of This Program barcode.\nBarcode no/s :" . implode(',', $proceed_knitting_only);
				disconnect($con);
				die;
			}
		}
		// echo "20**proceed_knitting check";die;


		if ($all_scanned_barcode_no) {
			$trans_check_sql = sql_select("select a.barcode_no,a.entry_form,a.po_breakdown_id, a.qnty from pro_roll_details a where a.entry_form in (22,58,83,133,82,180,110,183,84) and a.re_transfer =0 $scannedNewBarcodeCond and a.status_active = 1 and a.is_deleted = 0 union all select a.barcode_no,a.entry_form,a.po_breakdown_id, a.qnty from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.entry_form in (2) and b.trans_id<>0 and a.re_transfer =0 $scannedNewBarcodeCond and a.status_active = 1 and a.is_deleted = 0");

			if ($trans_check_sql[0][csf("barcode_no")] != "") {
				foreach ($trans_check_sql as $val) {
					$trans_po_barcode_check_arr[$val[csf("barcode_no")]][$val[csf("po_breakdown_id")]] = $val[csf("barcode_no")] . "__" . $val[csf("po_breakdown_id")];
					$actual_wgt_arr[$val[csf("barcode_no")]] = $val[csf("qnty")];
				}
			}
		}

		//echo "10**";print_r($all_scanned_barcode_no);die;

		for ($j = 1; $j <= $tot_row; $j++) {
			$recvBasis = "recvBasis_" . $j;
			$barcodeNo = "barcodeNo_" . $j;
			$progBookPiId = "progBookPiId_" . $j;
			$productId = "productId_" . $j;
			$orderId = "orderId_" . $j;
			$rollId = "rollId_" . $j;
			$rollWgt = "rollWgt_" . $j;
			$hiddenQtyInPcs = "hiddenQtyInPcs_" . $j;
			$yarnLot = "yarnLot_" . $j;
			$yarnCount = "yarnCount_" . $j;
			$colorId = "colorId_" . $j;
			$stichLn = "stichLn_" . $j;
			$brandId = "brandId_" . $j;

			$floor = "floorId_" . $j;
			$room = "roomId_" . $j;
			$rack = "rack_" . $j;
			$shelf = "shelf_" . $j;
			$bin = "bin_" . $j;

			$dtlsId = "dtlsId_" . $j;
			$transId = "transId_" . $j;
			$rolltableId = "rolltableId_" . $j;
			$rollNo = "rollNo_" . $j;
			$locationId = "locationId_" . $j;
			$machineId = "machineId_" . $j;
			$roll_rate = "rollRate_" . $j;
			$bookWithoutOrder = "bookWithoutOrder_" . $j;
			$orderNo = "orderNo_" . $j;
			$storeId = "storeId_" . $j;
			$bodyPartId = "bodyPartId_" . $j;
			$smnBooking = "smnBooking_" . $j;
			$isSalesOrder = "isSalesOrder_" . $j;
			$yarnRate = "yarnRate_" . $j;
			$knittingCharge = "knittingCharge_" . $j;

			$cons_rate = str_replace("'", "", $$roll_rate);
			$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;

			//$cons_rate=$$roll_rate;
			//$cons_amount=$cons_rate*$$rollWgt;

			if ($$rolltableId > 0) {
				$transId_arr[$$transId] = $$transId;
				$data_array_update_trans[$$transId] = explode("*", ($txt_issue_date . "*'" . $$brandId . "'*'" . $$locationId . "'*'" . $$machineId . "'*'" . $$stichLn . "'*'" . $$floor . "'*'" . $$room . "'*'" . $$rack . "'*'" . $$shelf . "'*'" . $$bin . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

				$dtlsId_arr[] = $$dtlsId;
				$data_array_update_dtls[$$dtlsId] = explode("*", ("'" . $$colorId . "'*'" . $$locationId . "'*'" . $$machineId . "'*'" . $$stichLn . "'*'" . $$yarnLot . "'*'" . $$yarnCount . "'*'" . $$brandId . "'*'" . $$floor . "'*'" . $$room . "'*'" . $$rack . "'*'" . $$shelf . "'*'" . $$bin . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $$hiddenQtyInPcs));

				$rollId_arr[] = $$rolltableId;
				$data_array_update_roll[$$rolltableId] = explode("*", ("'" . $cons_rate . "'*'" . $cons_amount . "'*'" . $$rollNo . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $$isSalesOrder . "*" . $$hiddenQtyInPcs));

				$barcodeNos .= $$barcodeNo . "__" . $$dtlsId . "__" . $$transId . "__" . $$rolltableId . ",";
				$dtlsId_prop = $$dtlsId;
				$transId_prop = $$transId;
				$all_roll_id .= $$rolltableId . ",";
			} else {
				if ($all_scanned_barcode_no) {

					if ($trans_po_barcode_check_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$orderId)] != str_replace("'", "", $$barcodeNo) . "__" . str_replace("'", "", $$orderId)) {
						if ($$bookWithoutOrder == 1) {
							echo "20**Sorry! This barcode =" . $$barcodeNo . " doesn't belong to this booking no =  " . $$smnBooking . "";
						} else {
							echo "20**Sorry! This barcode =" . $$barcodeNo . " doesn't belong to this order/fso no =  " . $$orderNo . "";
						}
						disconnect($con);
						die;
					}

					if (number_format($actual_wgt_arr[str_replace("'", "", $$barcodeNo)], 2, ".", "")  != number_format($$rollWgt, 2, ".", "")) {
						echo "20**Sorry! This barcode (" . str_replace("'", "", $$barcodeNo) . ") is split. actual weight " . number_format($actual_wgt_arr[str_replace("'", "", $$barcodeNo)], 2, ".", "") . " doesn't match with current " . number_format($$rollWgt, 2, ".", "") . "";
						disconnect($con);
						die;
					}
				}

				/*
				|--------------------------------------------------------------------------
				| inv_transaction
				| data preparing for
				| $data_array_trans
				|--------------------------------------------------------------------------
				|
				*/
				$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans[$transactionID] = "(" . $transactionID . "," . $update_id . ",'" . $$recvBasis . "','" . $$progBookPiId . "'," . $cbo_company_id . "," . $$productId . ",13,2," . $txt_issue_date . ",'" . $$rollWgt . "','" . $cons_rate . "','" . $cons_amount . "','" . $$brandId . "','" . $$locationId . "','" . $$machineId . "','" . $$stichLn . "','" . $$floor . "','" . $$room . "','" . $$rack . "','" . $$shelf . "','" . $$bin . "','" . $$storeId . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . $$bodyPartId . "')";

				/*
				|--------------------------------------------------------------------------
				| inv_grey_fabric_issue_dtls
				| data preparing for
				| $data_array_dtls
				|--------------------------------------------------------------------------
				|
				*/
				$dtls_id = return_next_id_by_sequence("INV_GREY_FAB_ISS_DTLS_PK_SEQ", "inv_grey_fabric_issue_dtls", $con);
				$data_array_dtls[$dtls_id] = "(" . $dtls_id . "," . $update_id . "," . $transactionID . ",'" . $$recvBasis . "','" . $$progBookPiId . "','" . $$productId . "','" . $$rollWgt . "','" . $cons_rate . "','" . $cons_amount . "','" . $$colorId . "','" . $$locationId . "','" . $$machineId . "','" . $$stichLn . "','" . $$yarnLot . "','" . $$yarnCount . "','" . $$brandId . "','" . $$floor . "','" . $$room . "','" . $$rack . "','" . $$shelf . "','" . $$bin . "','" . $$storeId . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$hiddenQtyInPcs . ",'" . $$bodyPartId . "','" . $$yarnRate . "','" . $$knittingCharge . "')";

				/*
				|--------------------------------------------------------------------------
				| pro_roll_details
				| data preparing for
				| $data_array_roll
				|--------------------------------------------------------------------------
				|
				*/
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$data_array_roll[$id_roll] = "(" . $id_roll . ",'" . $$barcodeNo . "'," . $update_id . "," . $dtls_id . ",'" . $$orderId . "',61,'" . $$rollWgt . "','" . $cons_rate . "','" . $cons_amount . "','" . $$rollNo . "','" . $$rollId . "','" . $$bookWithoutOrder . "','" . $$smnBooking . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$isSalesOrder . "," . $$hiddenQtyInPcs . ")";

				$dtlsId_prop = $dtls_id;
				$transId_prop = $transactionID;
				$barcodeNos .= $$barcodeNo . "__" . $dtls_id . "__" . $transactionID . "__" . $id_roll . ",";

				$new_array_inserted[str_replace("'", "", $$barcodeNo)] = $id_roll;
			}

			$prodData_array[$$productId] += $$rollWgt;
			$prodData_amount[$$productId] += $cons_amount;
			$all_prod_id .= $$productId . ",";

			/*
			|--------------------------------------------------------------------------
			| order_wise_pro_details
			| data preparing for
			| $data_array_prop
			|--------------------------------------------------------------------------
			|
			*/
			/*if($$bookWithoutOrder!=1)
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop[$id_prop] ="(".$id_prop.",".$transId_prop.",2,61,'".$dtlsId_prop."','".$$orderId."','".$$productId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$isSalesOrder.",".$$hiddenQtyInPcs.")";
				//$id_prop = $id_prop+1;
			}*/

			$data_array_prop_arr[$transId_prop]['bookWithoutOrder'] = $$bookWithoutOrder;
			$data_array_prop_arr[$transId_prop]['transId_prop'] = $transId_prop;
			$data_array_prop_arr[$transId_prop]['dtlsId_prop'] = $dtlsId_prop;
			$data_array_prop_arr[$transId_prop]['orderId'] = $$orderId;
			$data_array_prop_arr[$transId_prop]['productId'] = $$productId;
			$data_array_prop_arr[$transId_prop]['rollWgt'] += $$rollWgt;
			$data_array_prop_arr[$transId_prop]['isSalesOrder'] = $$isSalesOrder;
			$data_array_prop_arr[$transId_prop]['hiddenQtyInPcs'] += $$hiddenQtyInPcs;
		}
		// echo "<pre>";print_r($data_array_prop_arr);die;
		foreach ($data_array_prop_arr as $key => $row) {
			if ($row['bookWithoutOrder'] != 1) {
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop[$id_prop] = "(" . $id_prop . "," . $row['transId_prop'] . ",2,61,'" . $row['dtlsId_prop'] . "','" . $row['orderId'] . "','" . $row['productId'] . "','" . $row['rollWgt'] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $row['isSalesOrder'] . "," . $row['hiddenQtyInPcs'] . ")";
			}
		}

		$txt_deleted_barcodes = str_replace("'", "", $txt_deleted_barcodes);
		if ($txt_deleted_barcodes != "") {
			//Checking if issue return or receive for batch found
			$txt_deleted_barcodes_arr = explode(",", $txt_deleted_barcodes);
			$txt_deleted_barcodes_arr = array_filter($txt_deleted_barcodes_arr);
			$delBarcode_condition = "";
			$delBarcode_cond = "";
			if ($db_type == 2 && count($txt_deleted_barcodes_arr) > 999) {
				$txt_deleted_barcodes_arr_chunk = array_chunk($txt_deleted_barcodes_arr, 999);
				foreach ($txt_deleted_barcodes_arr_chunk as $chunk_arr) {
					$chunk_arr_value = implode(",", $chunk_arr);
					$delBarcode_cond .= "  barcode_no in($chunk_arr_value) or ";
				}

				$delBarcode_condition .= " and (" . chop($delBarcode_cond, 'or ') . ")";
			} else {
				$delBarcode_condition = " and barcode_no in($txt_deleted_barcodes)";
			}

			$del_barcode_chk = sql_select("SELECT entry_form, barcode_no from pro_roll_details where entry_form in (84,62) and re_transfer=0 and status_active=1 $delBarcode_condition");

			foreach ($del_barcode_chk as  $val) {
				if ($val[csf("entry_form")] == 62) {
					if ($rcv_for_batch_barcodes == "") {
						$rcv_for_batch_barcodes = $val[csf("barcode_no")];
					} else {
						$rcv_for_batch_barcodes .= "," . $val[csf("barcode_no")];
					}
				}
				if ($val[csf("entry_form")] == 84) {
					if ($issue_returned_barcodes == "") {
						$issue_returned_barcodes = $val[csf("barcode_no")];
					} else {
						$issue_returned_barcodes .= "," . $val[csf("barcode_no")];
					}
				}
			}

			if ($rcv_for_batch_barcodes != "") {
				echo "20**Received for batch barcode found.\nbarcodes :" . $rcv_for_batch_barcodes;
				disconnect($con);
				die;
			}

			if ($issue_returned_barcodes != "") {
				echo "20**Issue Returned barcode found.\nbarcodes :" . $issue_returned_barcodes;
				disconnect($con);
				die;
			}
		}



		$txt_deleted_id = str_replace("'", "", $txt_deleted_id);
		$adj_prod_array = array();
		$update_dtls_id = '';
		$update_trans_id = '';
		$update_delete_dtls_id = '';

		if ($txt_deleted_id != "")
			$all_roll_id = $all_roll_id . $txt_deleted_id;
		else
			$all_roll_id = substr($all_roll_id, 0, -1);

		$deleted_id_arr = explode(",", $txt_deleted_id);
		$all_roll_id_arr = array_filter(explode(",", $all_roll_id));
		$roll_id_cond = "";
		$roll_cond = "";
		if ($db_type == 2 && count($all_roll_id_arr) > 999) {
			$all_roll_id_chunk = array_chunk($all_roll_id_arr, 999);
			foreach ($all_roll_id_chunk as $chunk_arr) {
				$chunk_arr_value = implode(",", $chunk_arr);
				$roll_cond .= "  a.id in($chunk_arr_value) or ";
			}

			$roll_id_cond .= " and (" . chop($roll_cond, 'or ') . ")";
		} else {
			$roll_id_cond = " and a.id in($all_roll_id)";
		}

		if ($all_roll_id != "") {
			$rollData = sql_select("select a.id, a.qnty, b.id as dtls_id, b.trans_id, b.prod_id,a.amount from pro_roll_details a, inv_grey_fabric_issue_dtls b where a.dtls_id=b.id and a.entry_form=61 $roll_id_cond"); //and a.id in($all_roll_id)
			foreach ($rollData as $row) {
				$adj_prod_array[$row[csf('prod_id')]] += $row[csf('qnty')];
				$adj_prod_amount[$row[csf('prod_id')]] += $row[csf('amount')];
				$all_prod_id .= $row[csf('prod_id')] . ",";
				$update_dtls_id .= $row[csf('dtls_id')] . ",";

				if (in_array($row[csf('id')], $deleted_id_arr)) {
					$update_trans_id .= $row[csf('trans_id')] . ",";
					$update_delete_dtls_id .= $row[csf('dtls_id')] . ",";
				}
			}
		}

		$update_trans_id = substr($update_trans_id, 0, -1);
		$update_delete_dtls_id = substr($update_delete_dtls_id, 0, -1);

		$prod_id_array = array();
		$all_prod_id = implode(",", array_unique(explode(",", substr($all_prod_id, 0, -1))));
		$prodResult = sql_select("select id, current_stock,stock_value,avg_rate_per_unit from product_details_master where id in($all_prod_id)");
		foreach ($prodResult as $row) {
			$issue_qty = $prodData_array[$row[csf('id')]];
			$current_stock = $row[csf('current_stock')] + $adj_prod_array[$row[csf('id')]] - $issue_qty;
			//$stock_value=$current_stock*$row[csf('avg_rate_per_unit')];
			$stock_value = $row[csf('stock_value')] + $adj_prod_amount[$row[csf('id')]] - $prodData_amount[$row[csf('id')]];
			$avg_rate = $stock_value / $current_stock;
			$prod_id_array[$row[csf('id')]] = $row[csf('id')];
			if (is_nan($avg_rate) || is_infinite($avg_rate)) $avg_rate = 0;
			// if Qty is zero then rate & value will be zero
			if ($current_stock <= 0) {
				$stock_value = 0;
				$avg_rate = 0;
			}

			$data_array_prod_update[$row[csf('id')]] = explode("*", ("'" . $prodData_array[$row[csf('id')]] . "'*'" . $current_stock . "'*'" . $stock_value . "'*'" . $avg_rate . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
		}

		/*
		|--------------------------------------------------------------------------
		| order_wise_pro_details
		| data deleting here
		|--------------------------------------------------------------------------
		|
		*/
		$update_dtls_id = chop($update_dtls_id, ",");
		$update_dtls_id_arr = array_filter(explode(",", $update_dtls_id));
		$update_dtls_id_cond = "";
		$upDtlsIdCond = "";
		if ($db_type == 2 && count($update_dtls_id_arr) > 999) {
			$update_dtls_id_chunk = array_chunk($update_dtls_id_arr, 999);
			foreach ($update_dtls_id_chunk as $chunk_arr) {
				$chunk_arr_value = implode(",", $chunk_arr);
				$upDtlsIdCond .= "  dtls_id in($chunk_arr_value) or ";
			}

			$update_dtls_id_cond .= " and (" . chop($upDtlsIdCond, 'or ') . ")";
		} else {
			$update_dtls_id_cond = " and dtls_id in($update_dtls_id)";
		}

		if ($update_dtls_id != "") {
			//$delete_prop=execute_query("delete from order_wise_pro_details where dtls_id in(".substr($update_dtls_id,0,-1).") and entry_form=61",0);
			$delete_prop = execute_query("delete from order_wise_pro_details where entry_form=61 $update_dtls_id_cond", 0);
		}

		/*
		|--------------------------------------------------------------------------
		| inv_transaction
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array = $source_company_field . "batch_no*issue_date*remarks*attention*updated_by*update_date";
		$data_array = $source_company_value . $txt_batch_id . "*" . $txt_issue_date . "*" . $txt_remarks . "*" . $txt_attention . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
		$rID = sql_update("inv_issue_master", $field_array, $data_array, "id", $update_id, 0);

		$rID2 = 1;
		$rID3 = 1;
		$rID4 = 1;
		$rID5 = 1;
		$rID6 = 1;
		$rID7 = 1;
		$rID8 = 1;
		$statusChangeTrans = 1;
		$statusChangeDtls = 1;
		$statusChangeRoll = 1;
		$isReturnedFlag = 1;

		if (count($data_array_dtls) > 0) {
			/*
			|--------------------------------------------------------------------------
			| inv_transaction
			| data inserting here
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_trans = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,prod_id,item_category,transaction_type,transaction_date, cons_quantity,cons_rate, cons_amount,brand_id,location_id,machine_id,stitch_length,floor_id,room,rack,self,bin_box,store_id,inserted_by,insert_date,body_part_id";
			$data_array_trans_set = array_chunk($data_array_trans, 200);
			foreach ($data_array_trans_set as $setRows) {
				//echo "10** insert into inv_transaction ($field_array_trans) values ".implode(",",$setRows);oci_rollback($con);die;
				$rID2 = sql_insert("inv_transaction", $field_array_trans, implode(",", $setRows), 0);
				if ($rID2 == 1)
					$flag = 1;
				else if ($rID2 == 0) {
					$flag = 0;
					oci_rollback($con);
					echo "10**";
					disconnect($con);
					die;
				}
			}

			/*
			|--------------------------------------------------------------------------
			| inv_grey_fabric_issue_dtls
			| data inserting here
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_dtls = "id,mst_id,trans_id,basis,program_no,prod_id,issue_qnty,rate,amount,color_id,location_id,machine_id,stitch_length,yarn_lot,yarn_count,brand_id,floor_id,room,rack,self,bin_box,store_name,inserted_by,insert_date,qty_in_pcs,body_part_id,yarn_rate,kniting_charge";
			$data_array_dtls_set = array_chunk($data_array_dtls, 200);
			foreach ($data_array_dtls_set as $setRows) {
				$rID3 = sql_insert("inv_grey_fabric_issue_dtls", $field_array_dtls, implode(",", $setRows), 0);
				if ($rID3 == 1) $flag = 1;
				else if ($rID3 == 0) {
					$flag = 0;
					oci_rollback($con);
					echo "10**";
					disconnect($con);
					die;
				}
			}


			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| data inserting here
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_roll = "id,barcode_no,mst_id,dtls_id,po_breakdown_id,entry_form,qnty,rate,amount,roll_no,roll_id,booking_without_order,booking_no,inserted_by,insert_date,is_sales,qc_pass_qnty_pcs";
			$data_array_roll_set = array_chunk($data_array_roll, 200);
			foreach ($data_array_roll_set as $setRows) {
				$rID4 = sql_insert("pro_roll_details", $field_array_roll, implode(",", $setRows), 0);
				if ($rID4 == 1)
					$flag = 1;
				else if ($rID4 == 0) {
					$flag = 0;
					oci_rollback($con);
					echo "10**";
					disconnect($con);
					die;
				}
			}

			if (!empty($new_array_inserted)) {
				foreach ($new_array_inserted as $nBarcode => $nRollId) {
					$isReturnedFlag = execute_query("update pro_roll_details set is_returned=1 where barcode_no =$nBarcode and id <> $nRollId");
					if ($flag == 1) {
						if ($isReturnedFlag) {
							$flag = 1;
						} else {
							$flag = 0;
							oci_rollback($con);
							echo "10**";
							disconnect($con);
							die;
						}
					}
				}
			}
		}

		/*if($data_array_dtls!="")
		{
			$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			$rID3=sql_insert("inv_grey_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,0);
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		}*/
		//echo "10**".bulk_update_sql_statement( "pro_roll_details", "id", $field_array_updateroll, $data_array_update_roll, $rollId_arr );die;
		//echo "10**".print_r($data_array_update_trans);die;

		if (count($data_array_update_dtls) > 0) {
			/*
			|--------------------------------------------------------------------------
			| inv_transaction
			| data updating here
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_updatetrans = "transaction_date*brand_id*location_id*machine_id*stitch_length*floor_id*room*rack*self*bin_box*updated_by*update_date";
			$data_array_update_trans_chunk = array_chunk($data_array_update_trans, 50, true);
			$transId_up_arr = array_chunk($transId_arr, 50, true);
			$count_up_trans = count($transId_up_arr);
			for ($i = 0; $i < $count_up_trans; $i++) {
				$rID5 = execute_query(bulk_update_sql_statement("inv_transaction", "id", $field_array_updatetrans, $data_array_update_trans_chunk[$i], array_values($transId_up_arr[$i])), 1);

				if ($rID5 != "1") {
					oci_rollback($con);
					echo "6**0**1";
					disconnect($con);
					die;
				}
			}

			if ($rID5)
				$flag = 1;
			else
				$flag = 0;

			/*
			|--------------------------------------------------------------------------
			| inv_grey_fabric_issue_dtls
			| data updating here
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_updatedtls = "color_id*location_id*machine_id*stitch_length*yarn_lot*yarn_count*brand_id*floor_id*room*rack*self*bin_box*updated_by*update_date*qty_in_pcs";
			$data_array_update_dtls_chunk = array_chunk($data_array_update_dtls, 50, true);
			$dtlsId_up_arr = array_chunk($dtlsId_arr, 50, true);
			$count_up = count($dtlsId_up_arr);
			for ($i = 0; $i < $count_up; $i++) {
				$rID6 = execute_query(bulk_update_sql_statement("inv_grey_fabric_issue_dtls", "id", $field_array_updatedtls, $data_array_update_dtls_chunk[$i], array_values($dtlsId_up_arr[$i])), 1);
				if ($rID6 != "1") {
					oci_rollback($con);
					echo "6**0**1";
					disconnect($con);
					die;
				}
			}
			if ($rID6)
				$flag = 1;
			else
				$flag = 0;

			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| data updating here
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_updateroll = "rate*amount*roll_no*updated_by*update_date*is_sales*qc_pass_qnty_pcs";
			$data_array_update_roll_chunk = array_chunk($data_array_update_roll, 50, true);
			$rollId_up_arr = array_chunk($rollId_arr, 50, true);
			$count_up_rolls = count($rollId_up_arr);
			for ($i = 0; $i < $count_up_rolls; $i++) {
				$rID7 = execute_query(bulk_update_sql_statement("pro_roll_details", "id", $field_array_updateroll, $data_array_update_roll_chunk[$i], array_values($rollId_up_arr[$i])), 1);
				if ($rID7 != "1") {
					oci_rollback($con);
					echo "6**0**1";
					disconnect($con);
					die;
				}
			}

			if ($rID7)
				$flag = 1;
			else
				$flag = 0;
		}

		/*if(count($data_array_update_dtls)>0)
		{
			$rID5=execute_query(bulk_update_sql_statement( "inv_transaction", "id", $field_array_updatetrans, $data_array_update_trans, $transId_arr ));
			$rID6=execute_query(bulk_update_sql_statement( "inv_grey_fabric_issue_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr ));
			$rID7=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_updateroll, $data_array_update_roll, $rollId_arr ));
		}*/

		/*
		|--------------------------------------------------------------------------
		| inv_transaction
		| inv_grey_fabric_issue_dtls
		| pro_roll_details
		| data delete @ updating here
		|--------------------------------------------------------------------------
		|
		*/
		if ($txt_deleted_id != "") {
			$field_array_status = "updated_by*update_date*status_active*is_deleted";
			$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
			$statusChangeTrans = sql_multirow_update("inv_transaction", $field_array_status, $data_array_status, "id", $update_trans_id, 0);
			$statusChangeDtls = sql_multirow_update("inv_grey_fabric_issue_dtls", $field_array_status, $data_array_status, "id", $update_delete_dtls_id, 0);
			$statusChangeRoll = sql_multirow_update("pro_roll_details", $field_array_status, $data_array_status, "id", $txt_deleted_id, 0);
		}

		/*if($data_array_prop!="")
		{
			$rID8=sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,0);
		}*/

		/*
		|--------------------------------------------------------------------------
		| order_wise_pro_details
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		if (count($data_array_prop) > 0) {
			$field_array_prop = "id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,quantity,inserted_by,insert_date,is_sales,quantity_pcs";
			$data_array_prop_set = array_chunk($data_array_prop, 200);
			foreach ($data_array_prop_set as $setRows) {
				$rID8 = sql_insert("order_wise_pro_details", $field_array_prop, implode(",", $setRows), 0);
				if ($rID8 == 1)
					$flag = 1;
				else if ($rID8 == 0) {
					$flag = 0;
					if ($db_type == 0) {
						mysql_query("ROLLBACK");
						echo "10**";
						disconnect($con);
						die;
					} else {
						oci_rollback($con);
						echo "10**";
						disconnect($con);
						die;
					}
				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| product_details_master
		| data updating here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_prod_update = "last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
		$prodUpdate = execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array_prod_update, $data_array_prod_update, array_values($prod_id_array)));

		//oci_rollback($con);
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$rID6."&&".$rID7."&&".$rID8."&&".$delete_prop."&&".$prodUpdate."&&".$statusChangeTrans."&&".$statusChangeDtls."&&".$statusChangeRoll."&&".$isReturnedFlag; oci_rollback($con);die;
		//echo "10**".$statusChangeTrans."--".$statusChangeDtls."--".$statusChangeRoll."--".$prodUpdate;die;
		//echo bulk_update_sql_statement("inv_grey_fabric_issue_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr );die;

		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8 && $delete_prop && $prodUpdate && $statusChangeTrans && $statusChangeDtls && $statusChangeRoll && $isReturnedFlag) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_issue_no) . "**" . substr($barcodeNos, 0, -1);
			} else {
				mysql_query("ROLLBACK");
				echo "6**" . str_replace("'", '', $update_id) . "**";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8 && $delete_prop && $prodUpdate && $statusChangeTrans && $statusChangeDtls && $statusChangeRoll && $isReturnedFlag) {
				oci_commit($con);
				echo "1**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_issue_no) . "**" . substr($barcodeNos, 0, -1);
			} else {
				oci_rollback($con);
				echo "6**" . str_replace("'", '', $update_id) . "**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "roll_used_check") {
	$data = explode("_", $data);
	$roll_id = return_field_value("id", "pro_roll_details", "entry_form=62 and status_active=1 and is_deleted=0 and barcode_no=$data[0]");
	if ($roll_id == "") {
		$roll_id = return_field_value("id", "pro_roll_details", "entry_form=61 and is_returned=1 and roll_used=1 and dtls_id=$data[1] and status_active=1 and is_deleted=0 and barcode_no=$data[0]");
		if ($roll_id == "") echo "0";
		else echo "2";
	} else {
		echo "1";
	}

	exit();
}

if ($action == "issue_popup") {
	echo load_html_head_contents("Issue Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>

	<script>
		function js_set_value(id, posted_account) {
			$('#hidden_system_id').val(id);
			$('#hidden_posted_account').val(posted_account);
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:760px;">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:760px; margin-left:2px">
					<table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Issue Date Range</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="180">Please Enter Issue No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="hidden_system_id" id="hidden_system_id">
								<input type="hidden" name="hidden_posted_account" id="hidden_posted_account">
							</th>
						</thead>
						<tr class="general">
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>
							<td align="center">
								<?
								$search_by_arr = array(1 => "Issue No", 2 => "Barcode No");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", 1, $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_challan_search_list_view', 'search_div', 'grey_fabric_issue_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_challan_search_list_view") {
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0]);
	$search_by = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$year = $data[5];
	if ($db_type == 0) {
		$year_cond = "and YEAR(a.insert_date)=$year";
	} else {
		$year_cond = "and to_char(a.insert_date,'YYYY')=$year";
	}
	// echo $year_cond;die;

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and a.issue_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and a.issue_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}
	if ($db_type == 2) {
		$group_con = "LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id desc) as mst_id";
	} else {
		$group_con = "group_concat(mst_id) as mst_id";
	}
	if ($search_by == 2) {
		$barcode_no = trim($data[0]);
		if ($barcode_no != '') {
			$mst_id = return_field_value("$group_con", "pro_roll_details", "barcode_no=$barcode_no and entry_form=61 and status_active=1 and is_deleted=0 ", "mst_id");
		}
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		if ($search_by == 1) $search_field_cond = "and a.issue_number like '$search_string'";
		else if ($search_by == 2) {
			if ($mst_id != "") {
				$search_field_cond = "and a.id in($mst_id)";
			} else {
				$search_field_cond = "and a.id in(0)";
			}
		}
	}

	if ($db_type == 0) {
		$year_field = "YEAR(a.insert_date) as year,";
	} else if ($db_type == 2) {
		$year_field = "to_char(a.insert_date,'YYYY') as year,";
	} else $year_field = ""; //defined Later

	$sql = "SELECT a.id, $year_field a.issue_number_prefix_num, a.issue_number, a.knit_dye_source, a.knit_dye_company, a.issue_date, a.batch_no, a.issue_purpose, a.is_posted_account,sum(c.qnty) as issue_qnty
	from inv_issue_master a,inv_grey_fabric_issue_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id  =c.dtls_id and c.entry_form =61 and a.entry_form=61 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $year_cond and b.status_active=1 and b.is_deleted=0 and c.is_service=0
	group by a.id, a.insert_date, a.issue_number_prefix_num, a.issue_number, a.knit_dye_source, a.knit_dye_company, a.issue_date, a.batch_no, a.issue_purpose, a.is_posted_account order by a.id"; // and c.is_returned=0
	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="70">Issue No</th>
			<th width="60">Year</th>
			<th width="120">Dyeing Source</th>
			<th width="140">Dyeing Company</th>
			<th width="110">Issue Purpose</th>
			<th width="100">Issue Quantity</th>
			<th width="100">Batch</th>
			<th>Issue date</th>
		</thead>
	</table>
	<div style="width:840px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				$dye_comp = "&nbsp;";
				if ($row[csf('knit_dye_source')] == 1)
					$dye_comp = $company_arr[$row[csf('knit_dye_company')]];
				else
					$dye_comp = $supllier_arr[$row[csf('knit_dye_company')]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>','<? echo $row[csf('is_posted_account')]; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="70">
						<p>&nbsp;<? echo $row[csf('issue_number_prefix_num')]; ?></p>
					</td>
					<td width="60" align="center">
						<p><? echo $row[csf('year')]; ?></p>
					</td>
					<td width="120">
						<p><? echo $knitting_source[$row[csf('knit_dye_source')]]; ?>&nbsp;</p>
					</td>
					<td width="140">
						<p><? echo $dye_comp; ?>&nbsp;</p>
					</td>
					<td width="110">
						<p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?>&nbsp;</p>
					</td>
					<td width="100">
						<p><? echo $row[csf('issue_qnty')]; ?>&nbsp;</p>
					</td>
					<td width="100">
						<p><? echo $batch_arr[$row[csf('batch_no')]]; ?>&nbsp;</p>
					</td>
					<td align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
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

if ($action == "populate_data_from_data") {
	$sql = "select id, company_id, issue_number, knit_dye_source, knit_dye_company, issue_date, batch_no, issue_purpose,remarks, attention from inv_issue_master where id=$data and entry_form=61";
	//echo $sql;
	$res = sql_select($sql);
	foreach ($res as $row) {
		echo "$('#txt_issue_no').val('" . $row[csf("issue_number")] . "');\n";
		echo "$('#cbo_company_id').val(" . $row[csf("company_id")] . ");\n";
		echo "$('#cbo_company_id').attr('disabled','true')" . ";\n";
		echo "$('#cbo_issue_purpose').val(" . $row[csf("issue_purpose")] . ");\n";
		echo "$('#cbo_issue_purpose').attr('disabled','true')" . ";\n";
		echo "$('#txt_issue_date').val('" . change_date_format($row[csf("issue_date")]) . "');\n";
		echo "$('#cbo_dyeing_source').val(" . $row[csf("knit_dye_source")] . ");\n";
		//echo "$('#cbo_dyeing_source').attr('disabled','true')".";\n";
		echo "load_drop_down( 'requires/grey_fabric_issue_roll_wise_controller', " . $row[csf("knit_dye_source")] . "+'**'+" . $row[csf("company_id")] . ", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
		echo "$('#cbo_dyeing_comp').val(" . $row[csf("knit_dye_company")] . ");\n";
		//echo "$('#cbo_dyeing_comp').attr('disabled','true')".";\n";

		$batchno = return_field_value("batch_no", "pro_batch_create_mst", "id='" . $row[csf("batch_no")] . "'");
		echo "$('#txt_batch_no').val('" . $batchno . "');\n";
		echo "$('#txt_batch_id').val(" . $row[csf("batch_no")] . ");\n";
		echo "$('#txt_remarks').val('" . $row[csf("remarks")] . "');\n";
		echo "$('#txt_attention').val('" . $row[csf("attention")] . "');\n";
		echo "$('#update_id').val(" . $row[csf("id")] . ");\n";
	}

	$sql_get_barcode = sql_select("select barcode_no from pro_roll_details where mst_id =  $data and entry_form = 61 and status_active =1");

	$allBarcode = "";
	foreach ($sql_get_barcode as $row) {
		$allBarcode .= $row[csf("barcode_no")] . ",";
	}
	$allBarcode = rtrim($allBarcode, ",");
	$allBarcodes = explode(",", $allBarcode);
	$allBarcodes = array_chunk($allBarcodes, 999);
	$barcode_cond = " and";
	foreach ($allBarcodes as $all_barcodes) {
		if ($barcode_cond == " and")  $barcode_cond .= "(barcode_no in(" . implode(',', $all_barcodes) . ")";
		else $barcode_cond .= " or barcode_no in(" . implode(',', $all_barcodes) . ")";
	}
	$barcode_cond .= ")";
	//echo $pi_qnty_cond;die;
	$sql_nxt_process = sql_select("select count(b.barcode_no) rcv_by_batch from pro_roll_details b where b.entry_form =62 and b.status_active =1 $barcode_cond");

	/*$sql_nxt_process = sql_select("select count(b.barcode_no)  rcv_by_batch from pro_roll_details b,
	(select barcode_no from pro_roll_details where mst_id = $data and entry_form = 61 and status_active =1) a
	where b.barcode_no = a.barcode_no and b.entry_form =62 and status_active =1");*/
	if ($sql_nxt_process[0][csf("rcv_by_batch")] > 0) {
		echo "$('#cbo_dyeing_source').attr('disabled','true')" . ";\n";
		echo "$('#cbo_dyeing_comp').attr('disabled','true')" . ";\n";
	}

	exit();
}

if ($action == "barcode_nos") {
	if ($db_type == 0) {
		$barcode_nos = return_field_value("group_concat(barcode_no order by id desc) as barcode_nos", "pro_roll_details", "entry_form=61 and status_active=1 and is_deleted=0 and mst_id=$data", "barcode_nos");
	} else if ($db_type == 2) {
		$barcode_nos = return_field_value("LISTAGG(barcode_no, ',') WITHIN GROUP (ORDER BY id desc) as barcode_nos", "pro_roll_details", "entry_form=61 and status_active=1 and is_deleted=0 and mst_id=$data", "barcode_nos");
	}
	echo $barcode_nos;
	exit();
}

if ($action == "barcode_popup") {
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	if ($company_id > 0) $disable = 1;
	else $disable = 0;
?>

	<script>
		var selected_id = new Array();
		

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(str) {
			toggle(document.getElementById('search' + str), '#FFFFCC');

			var total_selected_val = $('#hidden_selected_row_total').val() * 1; // txt_individual_qty
			var total_selected_pcs_val = $('#hidden_selected_pcs_total').val() * 1; // txt_individual_qty


			if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_individual_id' + str).val());
				total_selected_val = total_selected_val + $('#txt_individual_qty' + str).val() * 1;
				total_selected_pcs_val = total_selected_pcs_val + $('#txt_individual_pcs_qty' + str).val() * 1;
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				total_selected_val = total_selected_val - $('#txt_individual_qty' + str).val() * 1;
				total_selected_pcs_val = total_selected_pcs_val - $('#txt_individual_pcs_qty' + str).val() * 1;
			}
			var id = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
			}
			id = id.substr(0, id.length - 1);

			$('#hidden_barcode_nos').val(id);
			$('#hidden_selected_row_total').val(total_selected_val.toFixed(2));
			$('#hidden_selected_pcs_total').val(total_selected_pcs_val.toFixed(2));


			if (id != "") {
				var no_of_roll = id.split(',').length;
			} else {
				var no_of_roll = "0";
			}
			$('#hidden_selected_row_count').val(no_of_roll);
		}

		function fnc_close() {
			parent.emailwindow.hide();
		}

		function reset_hide_field() {
			$('#hidden_barcode_nos').val('');
			selected_id = new Array();
		}


		function change_booking_placeholder() {
			if (document.getElementById('chkIsSales').checked) {
				$("#txt_booking_no").attr("placeholder", "Full Booking No");
			} else {
				$("#txt_booking_no").attr("placeholder", "Booking No Prefix");
			}
		}

		var tableFilters = {
			col_operation: {
				id: ["total_selected_value_td"],
				//col: [7,14,16,17,18,19,20,21,22,24,25,26],
				col: [24],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				if ($("#search" + i).css("display") != "none") {
					js_set_value(i);
				}
			}
		}

		function disable_enable_check_box()
		{
			var company_id = '<?=$company_id?>';
			var url = "grey_fabric_issue_roll_wise_controller.php?action=check_box&company_id="+company_id;
			fetch(url)
			.then((response) => {
				return response.text();
			})
			.then((data) => {
				console.log(data);

			if (data!=0) {
                document.getElementById("chkIsSales").checked = true;
				change_booking_placeholder();
            } else {
                document.getElementById("chkIsSales").checked = false;
				change_booking_placeholder();
            }

			})
		}

	window.onload = disable_enable_check_box;

	</script>

	</head>

	<body>
		<div align="center" style="width:960px;">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:960px; margin-left:2px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="1090" border="1" rules="all" class="rpt_table">
						<thead>
							<th colspan="13">
								<?
								echo create_drop_down("cbo_search_category", 140, $string_search_type, '', 1, "-- Search Catagory --", 4);
								?>
							</th>
						</thead>
						<thead>
							<th>Year</th>
							<th>Location</th>
							<th>Job No</th>
							<th>Order No</th>
							<th>File No</th>
							<th>Internal Ref. No</th>
							<th>Barcode No</th>
							<th>Sales Order No</th>
							<th>Receive ID</th>
							<th>Transfer Id</th>
							<th>Booking No</th>
							<th>Store Name</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:50px" class="formbutton" />
								<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?php
								echo create_drop_down("cbo_year_selection", 65, create_year_array(), "", 0, "-- --", date("Y", time()), "", 0, "");
								?>
							</td>
							<td>
								<?
								echo create_drop_down("cbo_location_name", 120, "select id,location_name from lib_location where company_id=$company_id and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");
								?>
							</td>
							<td align="center">
								<input type="text" style="width:60px" class="text_boxes" name="txt_job_no" id="txt_job_no" placeholder="Job No Prefix" />
							</td>
							<td align="center">
								<input type="text" style="width:100px" class="text_boxes" name="txt_order_no" id="txt_order_no" />
							</td>
							<td align="center">
								<input type="text" style="width:100px" class="text_boxes" name="txt_file_no" id="txt_file_no" />
							</td>
							<td align="center">
								<input type="text" style="width:100px" class="text_boxes" name="txt_ref_no" id="txt_ref_no" />
							</td>
							<td align="center"><input type="text" name="barcode_no" id="barcode_no" style="width:100px" class="text_boxes" /></td>
							<td align="center">
								<input type="text" style="width:100px" class="text_boxes" name="txt_sales_order_no" id="txt_sales_order_no" />
							</td>
							<td align="center">
								<input type="text" style="width:100px" class="text_boxes" name="txt_receive_id" id="txt_receive_id" />
							</td>
							<td align="center">
								<input type="text" style="width:100px" class="text_boxes" name="txt_trans_id" id="txt_trans_id" />
							</td>
							<td align="center">
								
								<input type="text" style="width:80px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" placeholder=" Booking Prefix" />

								
								<input type="checkbox" name="chkIsSales" id="chkIsSales" onChange="change_booking_placeholder()" />
								<label for="chkIsSales" >Is sales order</label>
								
							</td>
							<td id="store_td">
								<?
								$user_id = $_SESSION['logic_erp']['user_id'];
								$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
								$store_location_id = $userCredential[0][csf('store_location_id')];

								$store_location_credential_cond = ($store_location_id != "") ? " and a.id in($store_location_id)" : "";

								echo create_drop_down("cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $store_location_credential_cond and b.category_type in(13)  order by a.store_name", "id,store_name", 1, "-- All Store--", 0, "");;
								?>
							</td>

							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('barcode_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_sales_order_no').value+'_'+document.getElementById('chkIsSales').checked+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_trans_id').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_store_name').value+'_'+document.getElementById('txt_receive_id').value, 'create_barcode_search_list_view', 'search_div', 'grey_fabric_issue_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1,tableFilters);reset_hide_field();')" style="width:50px;" />
							</td>
						</tr>
					</table>
					<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>

	<script>
		var tableFilters = {
			col_operation: {
				id: ["value_total_selected_value_td", "value_total_selected_pcs_td"],
				//col: [7,14,16,17,18,19,20,21,22,24,25,26],
				col: [27, 28],
				operation: ["sum", "sum"],
				write_method: ["innerHTML", "innerHTML"]
			}
		}
		setFilterGrid("tbl_list_search", -1, tableFilters);
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_barcode_search_list_view") {
	$data = explode("_", $data);

	$location_id = trim($data[0]);
	$order_no = trim($data[1]);
	$company_id = $data[2];
	$file_no = trim($data[3]);
	$ref_no = trim($data[4]);
	$barcode_no = trim($data[5]);
	$booking_no = trim($data[6]);
	$sales_order_no = trim($data[7]);
	$is_sales = trim($data[8]);
	$job_no = trim($data[9]);
	$year = trim($data[10]);
	$trans_id = trim($data[11]);
	$search_category = trim($data[12]);
	$cbo_store_name = trim($data[13]);
	$receive_id = trim($data[14]);

	if ($cbo_store_name) {
		$store_cond_rcv = " and a.store_id =" . $cbo_store_name;
		$store_cond_trans = " and b.to_store=" . $cbo_store_name;
	}

	//print_r($data);die;
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$store_arr = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in(13)", "id", "store_name");
	$po_cancel_status_arr = return_library_array("select id, status_active from wo_po_break_down where status_active =3 and is_deleted=0", 'id', 'status_active');

	$lib_company_arr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id", "company_name");

	$machine_array = return_library_array("select id, machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0", "id", "machine_no");

	$lib_supplier_arr = return_library_array("SELECT a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");

	$floorRoomRackShelf_array = return_library_array("SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");

	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");

	$search_field_cond = $search_field_cond2 = $booking_cond = "";
	$reference_po_book_cond = "";
	if ($order_no != "") {
		if ($search_category == 1) {
			$search_field_cond = " and d.po_number = '$order_no'";
			$reference_po_book_cond = " and d.po_number = '$order_no'";
		} else if ($search_category == 0 || $search_category == 4) {
			$search_field_cond = " and d.po_number like '%$order_no%'";
			$reference_po_book_cond = " and d.po_number like '%$order_no%'";
		} else if ($search_category == 2) {
			$search_field_cond = " and d.po_number like '$order_no%'";
			$reference_po_book_cond = " and d.po_number like '$order_no%'";
		} else if ($search_category == 3) {
			$search_field_cond = " and d.po_number like '%$order_no'";
			$reference_po_book_cond = " and d.po_number like '%$order_no'";
		} else {
			$search_field_cond = "";
		}
	} else if ($barcode_no != "") {
		$barcode_cond = " and c.barcode_no='$barcode_no'";
	} else if ($job_no != "") {
		$search_field_cond .= " and d.job_no_mst like '%-" . substr($year, -2) . "-%'";
		if ($search_category == 1) {
			$search_field_cond .= " and d.job_no_mst = '$job_no'";
			$reference_po_book_cond .= " and d.job_no_mst = '$job_no'";
		} else if ($search_category == 0 || $search_category == 4) {
			$search_field_cond .= " and d.job_no_mst like '%$job_no%'";
			$reference_po_book_cond .= " and d.job_no_mst like '%$job_no%'";
		} else if ($search_category == 2) {
			$search_field_cond .= " and d.job_no_mst like '$job_no%'";
			$reference_po_book_cond .= " and d.job_no_mst like '$job_no%'";
		} else if ($search_category == 3) {
			$search_field_cond .= " and d.job_no_mst like '%$job_no'";
			$reference_po_book_cond .= " and d.job_no_mst like '%$job_no'";
		} else {
			$search_field_cond .= "";
		}
	} else if ($sales_order_no != "") {
		if ($search_category == 1) $sales_order_cond = " and d.job_no = '$sales_order_no'";
		else if ($search_category == 0 || $search_category == 4) $sales_order_cond = " and d.job_no like '%$sales_order_no%'";
		else if ($search_category == 2) $sales_order_cond = " and d.job_no like '$sales_order_no%'";
		else if ($search_category == 3) $sales_order_cond = " and d.job_no like '%$sales_order_no'";
		else $sales_order_cond = "";
		if ($db_type == 0) {
			$order_year =	" and year(d.insert_date) = $year";
		} else {
			$order_year = "and to_char(d.insert_date,'yyyy') = $year";
		}
	} else if ($booking_no != "") {
		if ($search_category == 1) {
			$search_field_cond .= " and e.booking_no  ='$booking_no'";
			$reference_po_book_cond .= " and e.booking_no  ='$booking_no'";
		} else if ($search_category == 0 || $search_category == 4) {
			$search_field_cond .= " and e.booking_no  like '%$booking_no%'";
			$reference_po_book_cond .= " and e.booking_no  like '%$booking_no%'";
		} else if ($search_category == 2) {
			$search_field_cond .= " and e.booking_no  like '$booking_no%'";
			$reference_po_book_cond .= " and e.booking_no  like '$booking_no%'";
		} else if ($search_category == 3) {
			$search_field_cond .= " and e.booking_no  like '%$booking_no'";
			$reference_po_book_cond .= " and e.booking_no  like '%$booking_no'";
		} else {
			$search_field_cond .= "";
		}

		if ($search_category == 1) $non_order_booking = " and d.booking_no  ='$booking_no'";
		else if ($search_category == 0 || $search_category == 4) $non_order_booking = " and d.booking_no  like '%$booking_no%'";
		else if ($search_category == 2) $non_order_booking = " and d.booking_no  like '$booking_no%'";
		else if ($search_category == 3) $non_order_booking = " and d.booking_no  like '%$booking_no'";
		else $non_order_booking = "";

		if ($db_type == 0) {
			$non_order_booking .=	" and year(d.booking_date) = $year";
			$order_year =	" and year(d.insert_date) = $year";
		} else {
			$non_order_booking .=	" and to_char(d.booking_date,'YYYY') = $year";
			$order_year = "and to_char(d.insert_date,'yyyy') = $year";
		}
	} else if ($receive_id != "" || $trans_id != "" || $ref_no != "") {
	} else {
		echo "<div style='color:red; font-weight:bold; text-align:center;'>Please enter Order No</div>";
		die;
	}

	if ($receive_id != "") {
		if ($receive_id != "") {
			$receive_id_cond = " and a.recv_number like '%$receive_id%' and a.recv_number like '%-" . substr($year, -2) . "-%'";
			$receive_id_cond2 = " and a.id =0";
		} else {
			$receive_id_cond = "";
			$receive_id_cond2 = "";
		}
	}
	// echo $receive_id_cond;die;

	if ($trans_id != "") {
		if ($trans_id != "") {
			$trans_id_cond = " and a.transfer_system_id like '%$trans_id%' and a.transfer_system_id like '%-" . substr($year, -2) . "-%'";
			$trans_id_cond2 = " and a.id =0";
		} else {
			$trans_id_cond = "";
			$trans_id_cond2 = "";
		}
	}
	// echo $trans_id_cond;die;
	if ($db_type == 0) {
		$booking_without_order_null_cond =	" c.booking_without_order = '' ";
	} else {
		$booking_without_order_null_cond =	" c.booking_without_order is null ";
	}

	if ($file_no != "") {
		if ($search_category == 1) {
			$search_field_cond .= " and d.file_no = '$file_no'";
			$reference_po_book_cond .= " and d.file_no = '$file_no'";
		} else if ($search_category == 0 || $search_category == 4) {
			$search_field_cond .= " and d.file_no like '%$file_no%'";
			$reference_po_book_cond .= " and d.file_no like '%$file_no%'";
		} else if ($search_category == 2) {
			$search_field_cond .= " and d.file_no like '$file_no%'";
			$reference_po_book_cond .= " and d.file_no like '$file_no%'";
		} else if ($search_category == 3) {
			$search_field_cond .= " and d.file_no like '%$file_no'";
			$reference_po_book_cond .= " and d.file_no like '%$file_no'";
		} else {
			$search_field_cond .= "";
		}
	}
	if ($ref_no != "") {
		if ($search_category == 1) {
			$search_field_cond .= " and d.grouping = '$ref_no'";
			$reference_po_book_cond .= " and d.grouping = '$ref_no'";
		} else if ($search_category == 0 || $search_category == 4) {
			$search_field_cond .= " and d.grouping like '%$ref_no%'";
			$reference_po_book_cond .= " and d.grouping like '%$ref_no%'";
		} else if ($search_category == 2) {
			$search_field_cond .= " and d.grouping like '$ref_no%'";
			$reference_po_book_cond .= " and d.grouping like '$ref_no%'";
		} else if ($search_category == 3) {
			$search_field_cond .= " and d.grouping like '%$ref_no'";
			$reference_po_book_cond .= " and d.grouping like '%$ref_no'";
		} else {
			$search_field_cond .= "";
		}
		$ref_cond = " and d.grouping = '$ref_no'";
	}
	// echo $search_field_cond;die;


	if ($reference_po_book_cond != "") {
		if ($booking_no != "") {
			$sql_book_to_ord = sql_select("select d.id as po_id, e.booking_no from wo_po_break_down d, wo_booking_dtls e, wo_booking_mst f where d.id = e.po_break_down_id and e.booking_no = f.booking_no and f.company_id = $company_id and e.status_active = 1 and e.is_deleted = 0 and d.status_active=1 and d.is_deleted=0 and e.booking_type=1 $reference_po_book_cond and e.booking_no like '%-" . substr($year, -2) . "-%' ");
		} else {
			$sql_book_to_ord = sql_select("select d.id as po_id from wo_po_break_down d, wo_po_details_master g where d.job_no_mst = g.job_no and g.status_active = 1 and g.is_deleted = 0 and d.status_active=1 and d.is_deleted=0 and g.company_name = $company_id $reference_po_book_cond $order_year");
		}

		foreach ($sql_book_to_ord as $val) {
			$po_ref_arr[$val[csf("po_id")]] = $val[csf("po_id")];
		}

		$all_po_ref_arr = array_filter(array_unique($po_ref_arr));

		if (count($all_po_ref_arr) > 0) {
			$all_po_ref_no = implode(",", $all_po_ref_arr);
			$poCond = $all_po_ref_cond = "";

			if ($db_type == 2 && count($all_barcode_no_arr) > 999) {
				$all_barcode_no_chunk = array_chunk($all_barcode_no_arr, 999);
				foreach ($all_barcode_no_chunk as $chunk_arr) {
					$poCond .= " c.po_breakdown_id in(" . implode(",", $chunk_arr) . ") or ";
				}

				$all_po_ref_cond .= " and (" . chop($poCond, 'or ') . ")";
			} else {
				$all_po_ref_cond = " and c.po_breakdown_id in($all_po_ref_no)";
			}
		}
	}

	$location_cond = "";
	if ($location_id > 0) $location_cond = " and a.location_id=$location_id";


	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');

	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	if ($sales_order_no != '' || $is_sales == 'true') {
		if ($ref_no != "") {
			$sql_book_ord = "SELECT f.id as fso_id, f.job_no
			from wo_po_break_down d, wo_booking_dtls e, FABRIC_SALES_ORDER_MST f
			where d.id = e.po_break_down_id and e.BOOKING_MST_ID = f.BOOKING_ID and f.company_id = $company_id and e.status_active = 1 and e.is_deleted = 0
			and d.status_active=1 and d.is_deleted=0 and e.booking_type in(1,4) and d.grouping = '$ref_no'
			group by f.id, f.job_no";
			//echo $sql_book_ord;die;
			$sql_book_ord_rst = sql_select($sql_book_ord);
			$fso_ref_arr = array();
			foreach ($sql_book_ord_rst as $val) {
				$fso_ref_arr[$val[csf("fso_id")]] = $val[csf("fso_id")];
			}

			$all_fso_ref_arr = array_filter(array_unique($fso_ref_arr));

			if (count($all_fso_ref_arr) > 0) {
				$all_fso_ref_no = implode(",", $all_fso_ref_arr);
				$poCond = $all_fso_ref_cond = "";

				if ($db_type == 2 && count($all_fso_no_arr) > 999) {
					$all_fso_no_chunk = array_chunk($all_fso_no_arr, 999);
					foreach ($all_fso_no_chunk as $chunk_arr) {
						$poCond .= " d.id in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_fso_ref_cond .= " and (" . chop($poCond, 'or ') . ")";
				} else {
					$all_fso_ref_cond = " and d.id in($all_fso_ref_no)";
				}
			}
		}
		//echo "string";
		if ($search_category == 1) {
			if ($booking_no != "") $booking_no_cond = " and d.sales_booking_no='$booking_no'";
			else $booking_no_cond = "";
			if ($job_no != "") $job_no_cond = " and d.po_job_no like '%" . $job_no . "%'";
		} else if ($search_category == 0 || $search_category == 4) {
			if ($booking_no != "") $booking_no_cond = " and d.sales_booking_no like '%$booking_no%'";
			else $booking_no_cond = "";
			if ($job_no != "") $job_no_cond = " and d.po_job_no like '%" . $job_no . "%'";
		} else if ($search_category == 2) {
			if ($booking_no != "") $booking_no_cond = " and d.sales_booking_no like '$booking_no%'";
			else $booking_no_cond = "";
			if ($job_no != "") $job_no_cond = " and d.po_job_no like '" . $job_no . "%'";
		} else if ($search_category == 3) {
			if ($booking_no != "") $booking_no_cond = " and d.sales_booking_no like '%$booking_no'";
			else $booking_no_cond = "";
			if ($job_no != "") $job_no_cond = " and d.po_job_no like '%" . $job_no . "'";
		}

		$sales_order = 1;
		$sql = "SELECT a.recv_number,a.knitting_source,a.knitting_company, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.booking_no, d.sales_booking_no,d.job_no  sales_order_no,d.within_group,c.is_sales , d.id as po_id, d.po_job_no,b.color_id, b.body_part_id, b.floor_id, b.room, cast(b.rack as varchar2(100)) as rack, b.self
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, fabric_sales_order_mst d
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58,84) and c.entry_form in(2,22,58,84) and c.status_active=1 and c.is_deleted=0 and c.re_transfer=0 and c.roll_no>0  $booking_no_cond $job_no_cond $sales_order_cond $location_cond $barcode_cond $receive_id_cond $trans_id_cond2 $store_cond_rcv $order_year $all_fso_ref_cond
		group by a.recv_number, a.knitting_source,a.knitting_company, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.booking_no,d.sales_booking_no,d.job_no,d.within_group,c.is_sales ,d.id,d.po_job_no,b.color_id, b.body_part_id, b.floor_id, b.room, b.rack, b.self
		union all
		select a.transfer_system_id as recv_number,0 as knitting_source,0 as knitting_company, 0 as location_id,b.from_prod_id as  prod_id, c.barcode_no, c.roll_no, c.qnty,c.booking_no,d.sales_booking_no,d.job_no sales_order_no,d.within_group,c.is_sales ,d.id as po_id,d.po_job_no,null as color_id, b.to_body_part as body_part_id,b.to_floor_id as floor_id, b.to_room as room, cast(b.to_rack as varchar2(100)) as rack, b.to_shelf as self
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, fabric_sales_order_mst d
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form in(133) and c.entry_form in(133) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.re_transfer=0  $search_field_cond2 $job_no_cond $sales_order_cond $booking_no_cond $barcode_cond $receive_id_cond2 $trans_id_cond $store_cond_trans $order_year $all_fso_ref_cond
		group by a.transfer_system_id,b.from_prod_id, c.barcode_no, c.roll_no, c.qnty,c.booking_no,d.sales_booking_no,d.job_no,d.within_group,c.is_sales, d.id, d.po_job_no, b.to_body_part,b.to_floor_id, b.to_room, b.to_rack, b.to_shelf";
	} else {
		//echo "else";
		if (count($all_po_ref_arr) > 0 || $barcode_cond != "" || $trans_id != "" || $receive_id != "") {
			//echo "if";
			$sql = "SELECT a.recv_number,a.knitting_source,a.knitting_company,a.location_id, b.prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order, b.color_id, null as booking_no, c.po_breakdown_id, a.store_id, b.body_part_id, b.floor_id, b.room, cast(b.rack as varchar2(100)) as rack, b.self
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58,84) and c.entry_form in(2,22,58,84) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.re_transfer=0 and c.roll_no>0 and (c.booking_without_order =0 or $booking_without_order_null_cond) $location_cond $trans_id_cond2 $receive_id_cond $barcode_cond $all_po_ref_cond $store_cond_rcv and c. is_sales!=1
			group by a.recv_number, a.knitting_source,a.knitting_company,a.location_id, b.prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty,c.booking_no, c.booking_without_order,b.color_id, c.po_breakdown_id, a.store_id, b.body_part_id, b.floor_id, b.room, b.rack, b.self
			union all
			select a.transfer_system_id as recv_number,0 as knitting_source,0 as knitting_company,0 as location_id,b.to_prod_id as  prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order,null as color_id, null as booking_no, c.po_breakdown_id, b.to_store as store_id, b.body_part_id,b.to_floor_id as floor_id, b.to_room as room, cast(b.to_rack as varchar2(100)) as rack, b.to_shelf as self
			from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.to_company=$company_id and a.entry_form in(83,183) and c.entry_form in(83,183)  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.re_transfer=0 and (c.booking_without_order=0 or $booking_without_order_null_cond) $barcode_cond $trans_id_cond $receive_id_cond2 $all_po_ref_cond $store_cond_trans and c. is_sales!=1
			group by a.transfer_system_id,b.to_prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order, c.po_breakdown_id, b.to_store, b.body_part_id,b.to_floor_id, b.to_room, b.to_rack, b.to_shelf
			union all
			select a.transfer_system_id as recv_number,0 as knitting_source,0 as knitting_company, 0 as location_id,b.to_prod_id as  prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order,null as color_id, null as booking_no, c.po_breakdown_id, b.to_store as store_id, b.body_part_id,b.to_floor_id as floor_id, b.to_room as room, cast(b.to_rack as varchar2(100)) as rack, b.to_shelf as self
			from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.transfer_criteria = 1 and a.to_company=$company_id and a.entry_form in(82) and c.entry_form in(82) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.re_transfer=0 and (c.booking_without_order =0 or $booking_without_order_null_cond) $barcode_cond $trans_id_cond $receive_id_cond2 $all_po_ref_cond $store_cond_trans and c. is_sales!=1
			group by a.transfer_system_id,b.to_prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order, c.po_breakdown_id, b.to_store, b.body_part_id,b.to_floor_id, b.to_room, b.to_rack, b.to_shelf
			union all
			select a.transfer_system_id as recv_number,0 as knitting_source,0 as knitting_company,0 as location_id,b.from_prod_id as  prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order,null as color_id, null as booking_no, c.po_breakdown_id, b.to_store as store_id, b.body_part_id,b.to_floor_id as floor_id, b.to_room as room, cast(b.to_rack as varchar2(100)) as rack, b.to_shelf as self
			from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.transfer_criteria in (2,4) and a.company_id=$company_id  and a.entry_form in(82) and c.entry_form in(82) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.re_transfer=0 and (c.booking_without_order =0 or $booking_without_order_null_cond) $barcode_cond $trans_id_cond $receive_id_cond2 $all_po_ref_cond $store_cond_trans and c. is_sales!=1
			group by a.transfer_system_id,b.from_prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order, c.po_breakdown_id, b.to_store, b.body_part_id,b.to_floor_id, b.to_room, b.to_rack, b.to_shelf";
		}

		if ($booking_no != "" || $barcode_cond != "" || $trans_id != "" || $receive_id != "") {
			//echo "if2";
			if ($sql != "") {
				$sql .= " union all ";
			}
			$sql .= "SELECT a.recv_number,a.knitting_source,a.knitting_company, a.location_id, b.prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order, b.color_id , d.booking_no, c.po_breakdown_id, a.store_id, b.body_part_id, b.floor_id, b.room, cast(b.rack as varchar2(100)) as rack, b.self
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c , wo_non_ord_samp_booking_mst d
			where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58,84) and c.entry_form in(2,22,58,84) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.re_transfer=0
			and c.booking_without_order=1 $barcode_cond $non_order_booking $location_cond $trans_id_cond2 $receive_id_cond $store_cond_rcv
			group by a.recv_number,a.knitting_source,a.knitting_company, a.location_id, b.prod_id, c.barcode_no,c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order,b.color_id, d.booking_no, c.po_breakdown_id , a.store_id, b.body_part_id , b.floor_id, b.room, b.rack, b.self
			union all
			select a.transfer_system_id as recv_number,0 as knitting_source,0 as knitting_company,0 as location_id,b.to_prod_id as  prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order,null as color_id, null as booking_no, c.po_breakdown_id, b.to_store as store_id, b.body_part_id,b.to_floor_id as floor_id, b.to_room as room, cast(b.to_rack as varchar2(100)) as rack, b.to_shelf as self
			from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_non_ord_samp_booking_mst d where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.transfer_criteria in (6,8) and a.to_company=$company_id and a.entry_form in(110,180) and c.entry_form in(110,180) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.re_transfer=0 and c.booking_without_order=1 $barcode_cond $trans_id_cond $receive_id_cond2 $non_order_booking $store_cond_trans group by a.transfer_system_id,b.to_prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order, c.po_breakdown_id, b.to_store, b.body_part_id,b.to_floor_id, b.to_room, b.to_rack, b.to_shelf
			union all
			select a.transfer_system_id as recv_number, 0 as knitting_source,0 as knitting_company,0 as location_id, b.to_prod_id as  prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order,null as color_id, null as booking_no, c.po_breakdown_id, b.to_store as store_id, b.body_part_id,b.to_floor_id as floor_id, b.to_room as room, cast(b.to_rack as varchar2(100)) as rack, b.to_shelf as self
			from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_non_ord_samp_booking_mst d where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.entry_form in(82) and c.entry_form in(82) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.booking_without_order=1 and c.re_transfer=0 and a.transfer_criteria in (1,2,4) and a.to_company=$company_id $barcode_cond $trans_id_cond $receive_id_cond2 $non_order_booking $store_cond_trans group by a.transfer_system_id,b.to_prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order, c.po_breakdown_id, b.to_store, b.body_part_id,b.to_floor_id, b.to_room, b.to_rack, b.to_shelf
			";
		}
	}
	// echo $sql; die;
	$result = sql_select($sql);
	$barcode_arr = array();
	$po_nos_arr = array();
	foreach ($result as $row) {

		if ($po_cancel_status_arr[$row[csf('po_breakdown_id')]] == 3 && $row[csf('booking_without_order')] != 1) {
			echo "<div style='color:red; font-weight:bold; text-align:center;'>Not Allow Cancelled Order</div>";
			die;
		}


		if ($sales_order == 1 && $row[csf('within_group')] == 1) {
			$sales_within_group = true;
		} else {
			$sales_within_group = false;
		}

		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];

		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		if ($sales_order == 1) {
			$fso_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		} else {
			if ($row[csf('booking_without_order')] != 1) {
				$po_nos_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			}
		}
	}

	if (!empty($barcode_arr)) {

		$barcode_nos = implode(",", $barcode_arr);
		$barCond = $all_barcode_for_program_cond = "";
		if ($db_type == 2 && count($barcode_arr) > 999) {
			$barcode_arr_chunk = array_chunk($barcode_arr, 999);
			foreach ($barcode_arr_chunk as $chunk_arr) {
				$barCond .= " barcode_no in(" . implode(",", $chunk_arr) . ") or ";
			}
			$all_barcode_for_program_cond .= " and (" . chop($barCond, 'or ') . ")";
		} else {
			$all_barcode_for_program_cond = " and barcode_no in($barcode_nos)";
		}

		$sqlprog = sql_select("select barcode_no,booking_no from pro_roll_details where entry_form=2 and receive_basis=2 and status_active=1 $all_barcode_for_program_cond ");
		$programNoArr = array();

		foreach ($sqlprog as $row) {
			$programNoArr[$row[csf('barcode_no')]] = $row[csf('booking_no')];
		}
	}

	$po_nos_arr = array_filter(array_unique($po_nos_arr));

	if (count($po_nos_arr) > 0) {
		$po_nos = implode(",", $po_nos_arr);
		$poCond = $all_po_cond = "";
		if ($db_type == 2 && count($po_nos_arr) > 999) {
			$po_nos_arr_chunk = array_chunk($po_nos_arr, 999);
			foreach ($po_nos_arr_chunk as $chunk_arr) {
				$poCond .= " d.id in(" . implode(",", $chunk_arr) . ") or ";
			}
			$all_po_cond .= " and (" . chop($poCond, 'or ') . ")";
		} else {
			$all_po_cond = " and d.id in($po_nos)";
		}

		$po_info = sql_select("SELECT d.id as po_id,d.po_number, d.job_no_mst, d.file_no, d.grouping,d.shipment_date, e.booking_no from wo_po_break_down d left join  wo_booking_dtls e on d.id = e.po_break_down_id and e.booking_type in (1,4) and e.status_active = 1 and e.is_deleted = 0 where d.status_active=1 and d.is_deleted=0 $all_po_cond");
		foreach ($po_info as $po_row) {
			$po_no_ref_arr[$po_row[csf('po_id')]]["po_number"] = $po_row[csf('po_number')];
			$po_no_ref_arr[$po_row[csf('po_id')]]["job_no"] = $po_row[csf('job_no_mst')];
			$po_no_ref_arr[$po_row[csf('po_id')]]["book"] .= $po_row[csf('booking_no')] . ",";
			$po_no_ref_arr[$po_row[csf('po_id')]]["file_no"] = $po_row[csf('file_no')];
			$po_no_ref_arr[$po_row[csf('po_id')]]["grouping"] = $po_row[csf('grouping')];
			$po_no_ref_arr[$po_row[csf('po_id')]]["shipment_date"] = $po_row[csf('shipment_date')];
		}
	}

	$fso_id_arr = array_filter(array_unique($fso_id_arr));
	if (count($fso_id_arr) > 0) {
		$fso_id = implode(",", $fso_id_arr);
		$fsoCond = $all_fso_cond = "";
		if ($db_type == 2 && count($fso_id_arr) > 999) {
			$fso_id_arr_chunk = array_chunk($fso_id_arr, 999);
			foreach ($fso_id_arr_chunk as $chunk_arr) {
				$fsoCond .= " f.id in(" . implode(",", $chunk_arr) . ") or ";
			}
			$all_fso_cond .= " and (" . chop($fsoCond, 'or ') . ")";
		} else {
			$all_fso_cond = " and f.id in($fso_id)";
		}

		$sql_book_ord_sql = "SELECT d.grouping, f.id as fso_id, f.job_no
		from wo_po_break_down d, wo_booking_dtls e, FABRIC_SALES_ORDER_MST f
		where d.id = e.po_break_down_id and e.BOOKING_MST_ID = f.BOOKING_ID and f.company_id = $company_id and e.status_active = 1 and e.is_deleted = 0
		and d.status_active=1 and d.is_deleted=0 and e.booking_type in(1,4) $all_fso_cond group by d.grouping, f.id, f.job_no";
		// echo $sql_book_ord_sql;die;
		$sql_book_ord_sql_rst = sql_select($sql_book_ord_sql);
		$int_ref_arr = array();
		foreach ($sql_book_ord_sql_rst as $row) {
			$int_ref_arr[$row[csf('job_no')]] = $row[csf('grouping')];
		}
		// echo "<pre>";print_r($int_ref_arr);
	}

	$barcode_arr = array_filter(array_unique($barcode_arr));

	if (count($barcode_arr) > 0) {
		$all_barcode_nos = implode(",", $barcode_arr);
		$BarCond = $all_barcode_cond = "";

		if ($db_type == 2 && count($barcode_arr) > 999) {
			$barcode_arr_chunk = array_chunk($barcode_arr, 999);
			foreach ($barcode_arr_chunk as $chunk_arr) {
				$BarCond .= " a.barcode_no in(" . implode(",", $chunk_arr) . ") or ";
			}

			$all_barcode_cond .= " and (" . chop($BarCond, 'or ') . ")";
		} else {
			$all_barcode_cond = " and a.barcode_no in($all_barcode_nos)";
		}
	}

	if (!empty($barcode_arr)) {
		$scanned_barcode_arr = array();
		$barcodeData = sql_select("select a.barcode_no from pro_roll_details a where a.entry_form=61 and a.status_active=1 and a.is_deleted=0
			$all_barcode_cond and a.is_returned <>1 ");
		foreach ($barcodeData as $row) {
			$scanned_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		}

		$stitch_lot_sql = sql_select("SELECT a.barcode_no, a.qc_pass_qnty_pcs, a.coller_cuff_size, b.stitch_length, b.yarn_lot, b.machine_no_id, b.color_id, b.yarn_count from pro_roll_details a,pro_grey_prod_entry_dtls b where a.dtls_id = b.id and a.entry_form in (2,22) and a.status_active=1 and b.status_active =1 and b.is_deleted =0 and a.is_deleted =0 $all_barcode_cond");
		

		/*$stitch_lot_sql = sql_select("SELECT a.barcode_no, b.stitch_length, b.yarn_lot, b.machine_no_id, b.color_id, f.booking_no, f.proceed_knitting, f.proceed_dyeing
			from pro_roll_details a,pro_grey_prod_entry_dtls b, inv_receive_master c
			left join ppl_planning_info_entry_dtls d on c.booking_id=d.id and c.receive_basis=2 and c.entry_form=2
			left join ppl_planning_info_entry_mst e on d.mst_id=e.id
			left join wo_booking_mst f on e.booking_no = f.booking_no
			where a.dtls_id = b.id and b.mst_id=c.id and a.entry_form in (2,22) and a.status_active=1 and b.status_active =1 and b.is_deleted =0 and a.is_deleted =0 $all_barcode_cond");*/


		foreach ($stitch_lot_sql as $row) {

			$count = '';
			$yarn_count = explode(",", $row[csf('yarn_count')]);
			foreach ($yarn_count as $count_id) {
				if ($count == '')
					$count = $yarn_count_details[$count_id];
				else
					$count .= "," . $yarn_count_details[$count_id];
			}

			$stitch_lot_arr[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
			$stitch_lot_arr[$row[csf("barcode_no")]]['yarn_lot'] = $row[csf("yarn_lot")];
			$stitch_lot_arr[$row[csf("barcode_no")]]['machine_id'] = $row[csf("machine_no_id")];
			$stitch_lot_arr[$row[csf("barcode_no")]]['color_id'] = $row[csf("color_id")];
			$stitch_lot_arr[$row[csf("barcode_no")]]['qty_in_pcs'] = $row[csf("qc_pass_qnty_pcs")];
			$stitch_lot_arr[$row[csf("barcode_no")]]['size'] = $row[csf("coller_cuff_size")];
			$stitch_lot_arr[$row[csf("barcode_no")]]['yarn_count'] = $count ;
		}
	}

?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2280" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="120">System Number</th>
			<th width="50">Source</th>
			<th width="100">Party Name</th>
			<th width="100">Body Part</th>
			<th width="160">Fabric Description</th>
			<th width="30">Gsm</th>
			<th width="30">Dia</th>
			<th width="60">Stitch L.</th>
			<th width="90">Yarn Lot</th>
			<th width="100">Yarn Count</th>
			<th width="40">Machine No</th>
			<th width="80">Job No</th>
			<th width="90">Booking No</th>
			<th width="110">Order/FSO No</th>
			<th width="60">Program No</th>
			<th width="50">Within Group</th>
			<th width="70">Color Name</th>
			<th width="105">Location</th>
			<th width="70">File No</th>
			<th width="70">Ref No</th>
			<th width="50">Floor</th>
			<th width="50">Room</th>
			<th width="50">Rack</th>
			<th width="50">Shelf</th>
			<th width="65">Shipment Date</th>
			<th width="75">Barcode No</th>
			<th width="40">Roll No</th>
			<th width="50">Roll Qty.</th>
			<th width="50">Qty.In Pcs</th>
			<th width="50">Size</th>
			<th>Store Name</th>
		</thead>
	</table>
	<div style="width:2290px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2272" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			$total_roll_weight = 0;
			$total_qty_in_pcs = 0;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
				if ($scanned_barcode_arr[$row[csf('barcode_no')]] == "") {
					$within_group_con = ($row[csf('within_group')] == 1) ? "Yes" : "No";

					$within_group = $row[csf('within_group')];
					$is_sales = $row[csf('is_sales')];
					if ($sales_order == 1) {
						$sales_order_order = $row[csf('sales_order_no')];
						$sales_booking_no = $row[csf('sales_booking_no')];
						if ($within_group == 1) {
							$job_no = $row[csf("po_job_no")];
							$group_no = $int_ref_arr[$sales_order_order];
						} else {
							$job_no = '';
							$po_shipdate_no = '';
							$group_no = '';
						}
					} else {
						if ($row[csf('booking_without_order')] == 1) {
							$sales_order_order = "";
							$job_no = "";
							$sales_booking_no = $row[csf('booking_no')];
							$po_shipdate_no = "";
							$file_no = "";
							$group_no = "";
						} else {
							$sales_order_order = $po_no_ref_arr[$row[csf('po_breakdown_id')]]["po_number"];
							$job_no = $po_no_ref_arr[$row[csf('po_breakdown_id')]]["job_no"];
							$sales_booking_no = implode(",", array_unique(explode(",", chop($po_no_ref_arr[$row[csf('po_breakdown_id')]]["book"], ","))));
							$file_no = $po_no_ref_arr[$row[csf('po_breakdown_id')]]["file_no"];
							$group_no = $po_no_ref_arr[$row[csf('po_breakdown_id')]]["grouping"];
							$po_shipdate_no = $po_no_ref_arr[$row[csf('po_breakdown_id')]]["shipment_date"];
						}
					}
					$color = '';
					//$color_id=explode(",",$row[csf('color_id')]);
					$color_id = explode(",", $stitch_lot_arr[$row[csf("barcode_no")]]['color_id']);
					foreach ($color_id as $val) {
						if ($val > 0) $color .= $color_arr[$val] . ",";
					}
					$color = chop($color, ',');

					$product_data = explode(",", $product_arr[$row[csf('prod_id')]]);

					if ($row[csf('knitting_source')] == 1) {
						$knitting_comp = $lib_company_arr[$row[csf('knitting_company')]];
					} elseif ($row[csf('knitting_source')] == 3) {
						$knitting_comp = $lib_supplier_arr[$row[csf('knitting_company')]];
					} else {
						$knitting_comp = "";
					}

			?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="30" align="center">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>" />
							<input type="hidden" name="txt_individual_qty[]" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $row[csf('qnty')]; ?>" />
							<input type="hidden" name="txt_individual_pcs_qty[]" id="txt_individual_pcs_qty<?php echo $i; ?>" value="<?php echo  $stitch_lot_arr[$row[csf("barcode_no")]]['qty_in_pcs']; ?>" />
						</td>
						<td width="120">
							<p><? echo $row[csf('recv_number')]; ?></p>
						</td>
						<td width="50">
							<p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p>
						</td>
						<td width="100">
							<p><? echo $knitting_comp; ?></p>
						</td>
						<td width="100">
							<p><? echo $body_part[$row[csf('body_part_id')]]; ?></p>
						</td>
						<td width="160">
							<p><? echo $product_arr[$row[csf('prod_id')]]; ?></p>
						</td>
						<td width="30">
							<p><? echo $product_data[2]; ?></p>
						</td>
						<td width="30">
							<p><? echo $product_data[3]; ?></p>
						</td>

						<td width="60">
							<p><? echo $stitch_lot_arr[$row[csf("barcode_no")]]['stitch_length']; //$product_data[3]; 
								?></p>
						</td>
						<td width="90">
							<p><? echo $stitch_lot_arr[$row[csf("barcode_no")]]['yarn_lot']; //$product_data[3]; 
								?></p>
						</td>
						<td width="100">
							<p><? echo $stitch_lot_arr[$row[csf("barcode_no")]]['yarn_count']; //$product_data[3]; 
								?></p>
						</td>
						<td width="40">
							<p><? echo $machine_array[$stitch_lot_arr[$row[csf("barcode_no")]]['machine_id']]; ?></p>
						</td>

						<td width="80">
							<p><? echo $job_no; ?></p>
						</td>
						<td width="90">
							<p><? echo $sales_booking_no; ?></p>
						</td>
						<td width="110">
							<p><? echo $sales_order_order; ?></p>
						</td>
						<td width="60" align="center">
							<p><? echo $programNoArr[$row[csf('barcode_no')]]; ?></p>
						</td>
						<td width="50" align="center">
							<p><? echo $within_group_con; ?></p>
						</td>
						<td width="70">
							<p><? echo $color; ?></p>
						</td>
						<td width="105"><? echo $location_arr[$row[csf('location_id')]]; ?>&nbsp;</td>
						<td width="70"><? echo $file_no; ?>&nbsp;</td>
						<td width="70"><? echo $group_no; ?>&nbsp;</td>
						<td width="50"><? echo $floorRoomRackShelf_array[$row[csf('floor_id')]]; ?>&nbsp;</td>
						<td width="50"><? echo $floorRoomRackShelf_array[$row[csf('room')]]; ?>&nbsp;</td>
						<td width="50"><? echo $floorRoomRackShelf_array[$row[csf('rack')]]; ?>&nbsp;</td>
						<td width="50"><? echo $floorRoomRackShelf_array[$row[csf('self')]]; ?>&nbsp;</td>
						<td width="65" align="center"><? if ($row[csf('booking_without_order')] == 1) echo '&nbsp;';
														else echo change_date_format($po_shipdate_no); ?></td>
						<td width="75">
							<p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p>
						</td>
						<td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
						<td width="50" align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
						<td width="50" align="right"><? echo $stitch_lot_arr[$row[csf("barcode_no")]]['qty_in_pcs']; ?></td>
						<td width="50" align="right"><? echo $stitch_lot_arr[$row[csf("barcode_no")]]['size']; ?></td>
						<td align="center"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
					</tr>
			<?
					$i++;
					$total_roll_weight += $row[csf('qnty')];
					$total_qty_in_pcs += $stitch_lot_arr[$row[csf("barcode_no")]]['qty_in_pcs'];
				}
			}
			?>
		</table>
	</div>
	<table width="2270" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
		<tr class="tbl_bottom">
			<td width="30"></td>
			<td width="120"></td>
			<td width="50"></td>
			<td width="100"></td>
			<td width="100"></td>
			<td width="160"></td>
			<td width="30"></td>
			<td width="30"></td>
			<td width="60"></td>
			<td width="90"></td>
			<td width="100"></td>
			<td width="40"></td>
			<td width="80"></td>
			<td width="90"></td>
			<td width="110"></td>
			<td width="60"></td>
			<td width="50"></td>
			<td width="70"></td>
			<td width="105"></td>
			<td width="70"></td>
			<td width="70" title="ref no"></td>
			<td width="50"></td>
			<td width="50"></td>
			<td width="50"></td>
			<td width="50"></td>
			<td width="65" title="Ship Date"></td>
			<td width="75"></td>
			<td width="40">Total</td>
			<td width="50" id="value_total_selected_value_td" align="right"><?php echo number_format($total_roll_weight, 2); ?></td>
			<td width="50" id="value_total_selected_pcs_td" align="right"><?php echo number_format($total_qty_in_pcs, 2); ?></td>
			<td width="50"></td>
			<td width=""></td>
		</tr>
		<tr class="tbl_bottom">
			<td width="30"></td>
			<td width="120"></td>
			<td width="50"></td>
			<td width="100"></td>
			<td width="100"></td>
			<td width="160"></td>
			<td width="30"></td>
			<td width="30"></td>
			<td width="60"></td>
			<td width="90"></td>
			<td width="100"></td>
			<td width="40"></td>
			<td width="80"></td>
			<td width="90"></td>
			<td width="110"></td>
			<td width="60"></td>
			<td width="50"></td>

			<td width="70"></td>
			<td width="105"></td>
			<td width="70"></td>
			<td width="70" title="ref no"></td>
			<td width="150" colspan="3">Count of Selected Row=</td>
			<td width="50">
				<input type="text" style="width:35px" class="text_boxes_numeric" name="hidden_selected_row_count" id="hidden_selected_row_count" readonly value="0">
			</td>
			<td width="135" colspan="2" title="Ship Date">Selected Row Total=</td>
			<td width="90" colspan="2" align="right">
				<input type="text" class="text_boxes_numeric" name="hidden_selected_row_total" id="hidden_selected_row_total" readonly="" value="0" style="width:75px">
			</td>
			<td width="100" colspan="2" valign="bottom">
				<sub>
					<input type="text" class="text_boxes_numeric" name="hidden_selected_pcs_total" id="hidden_selected_pcs_total" readonly="" value="0" style="width: 70px; float: left; display: flex;">
				</sub>
			</td>

			<td width=""></td>

		</tr>
		<tr>
			<td align="left" colspan="2">
				<input type="checkbox" name="close" class="formbutton" onClick="check_all_data()" /> Check all
			</td>
			<td align="center" colspan="26">
				<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
			</td>
		</tr>
	</table>
<?
	exit();
}

if ($action == "batch_number_popup") {
	echo load_html_head_contents("Batch Number Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id, batch_no) {
			$('#hidden_batch_id').val(id);
			$('#hidden_batch_no').val(batch_no);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:800px;">
			<form name="searchbatchnofrm" id="searchbatchnofrm">
				<fieldset style="width:790px; margin-left:10px">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" border="1" rules="all" width="770" class="rpt_table">
						<thead>
							<th width="240">Batch Date Range</th>
							<th width="170">Search By</th>
							<th id="search_by_td_up" width="200">Please Enter Batch No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
								<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;">To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;">
							</td>
							<td>
								<?
								$search_by_arr = array(0 => "Batch No", 1 => "Fabric Booking no.", 2 => "Color");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td" width="140px">
								<input type="text" style="width:130px;" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value, 'create_batch_search_list_view', 'search_div', 'grey_fabric_issue_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="width:100%; margin-top:5px" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_batch_search_list_view") {
	$data = explode("_", $data);
	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];

	$date_cond = "";
	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and batch_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and batch_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		if ($search_by == 0)
			$search_field_cond = "and batch_no like '$search_string'";
		else if ($search_by == 1)
			$search_field_cond = "and booking_no like '$search_string'";
		else
			$search_field_cond = "and color_id in(select id from lib_color where status_active =1 and is_deleted=0 and color_name like '$search_string')";
	}

	$po_arr = array();
	$po_data = sql_select("select id, po_number, job_no_mst from wo_po_break_down");
	foreach ($po_data as $row) {
		$po_arr[$row[csf('id')]]['po_no'] = $row[csf('po_number')];
		$po_arr[$row[csf('id')]]['job_no'] = $row[csf('job_no_mst')];
	}

	if ($db_type == 0) {
		$order_id_arr = return_library_array("select mst_id, group_concat(po_id) as po_id from pro_batch_create_dtls where status_active=1 and is_deleted=0 group by mst_id", 'mst_id', 'order_id');
	} else {
		$order_id_arr = return_library_array("select mst_id, LISTAGG(cast(po_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY po_id) as po_id from pro_batch_create_dtls where status_active=1 and is_deleted=0 group by mst_id", 'mst_id', 'po_id');
	}
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');

	$sql = "select id, batch_no, extention_no, batch_date, batch_weight, booking_no, color_id, batch_against, booking_without_order, re_dyeing_from from pro_batch_create_mst where entry_form=0 and batch_for=1 and batch_against<>4 and company_id=$company_id and status_active=1 and is_deleted=0 $search_field_cond $date_cond";
	//echo $sql;die;
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Batch No</th>
			<th width="80">Extention No</th>
			<th width="80">Batch Date</th>
			<th width="80">Batch Qnty</th>
			<th width="115">Booking No</th>
			<th width="110">Color</th>
			<th>Po No</th>
		</thead>
	</table>
	<div style="width:780px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="760" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			$nameArray = sql_select($sql);
			foreach ($nameArray as $selectResult) {
				$po_no = '';
				$job_array = array();
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
				$order_id = array_unique(explode(",", $order_id_arr[$selectResult[csf('id')]]));
				foreach ($order_id as $value) {
					if ($po_no == '') $po_no = $po_arr[$value]['po_no'];
					else $po_no .= "," . $po_arr[$value]['po_no'];
					$job_no = $po_arr[$value]['job_no'];
					if (!in_array($job_no, $job_array)) {
						$job_array[] = $job_no;
					}
				}
				$job_no = implode(",", $job_array);
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('batch_no')]; ?>')">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="90">
						<p><? echo $selectResult[csf('batch_no')]; ?></p>
					</td>
					<td width="80">
						<p><? if ($selectResult[csf('extention_no')] != 0) echo $selectResult[csf('extention_no')]; ?>&nbsp;</p>
					</td>
					<td width="80" align="center"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
					<td width="80" align="right"><? echo $selectResult[csf('batch_weight')]; ?>&nbsp;</td>
					<td width="115">
						<p><? echo $selectResult[csf('booking_no')]; ?>&nbsp;</p>
					</td>
					<td width="110">
						<p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p>
					</td>
					<td>
						<p><? echo $po_no; ?>&nbsp;</p>
					</td>
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

if ($action == "populate_barcode_datas") {
	if ($db_type == 0) {
		$poIds = return_field_value("group_concat(po_breakdown_id order by po_breakdown_id desc) as po_breakdown_id", "pro_roll_details", "entry_form=83 and status_active=1 and is_deleted=0 and from_roll_id in($data) and re_transfer=0", "po_breakdown_id");
	} else if ($db_type == 2) {
		$poIds = return_field_value("LISTAGG(po_breakdown_id, ',') WITHIN GROUP (ORDER BY po_breakdown_id desc) as po_breakdown_id", "pro_roll_details", "entry_form=83 and status_active=1 and is_deleted=0 and from_roll_id in($data) and re_transfer=0", "po_breakdown_id");
	}
	echo $poIds;
	exit();
}

if ($action == "check_barcode_for_delete") {
	//echo $data;die;
	$data = explode("_", $data);
	$update_id = $data[0];
	$barcode_nos = rtrim($data[1], ",");
	$is_posted_accounts = sql_select("select is_posted_account,issue_number  from inv_issue_master  where id=$update_id");
	if ($is_posted_accounts[csf('is_posted_account')] == 1) {
		echo "1_" . $is_posted_accounts[csf('issue_number')];
		die;
	} else {


		if ($db_type == 0) {
			$barcode_in_RBbatch = return_field_value("group_concat(barcode_no order by barcode_no desc) as barcode_no", "pro_roll_details", "entry_form=62 and status_active=1 and is_deleted=0 and barcode_no in ($barcode_nos)", "barcode_no");
		} else if ($db_type == 2) {
			$barcode_in_RBbatch = return_field_value("LISTAGG(barcode_no, ',') WITHIN GROUP (ORDER BY barcode_no desc) as barcode_no", "pro_roll_details", "entry_form=62 and status_active=1 and is_deleted=0 and barcode_no in ($barcode_nos)", "barcode_no");
		}
		//echo $barcode_in_RBbatch;die;
		//$barcode_in_RBbatch=sql_select(" select barcode_no   from pro_roll_details   where entry_form=62 and barcode_no in ($barcode_nos) ");
		if ($barcode_in_RBbatch != "") {
			echo "2_" . $barcode_in_RBbatch;
			die;
		} else {
			echo 0;
			die;
		}
	}
	exit();
}


if ($action == "populate_poIds") {
	$poIdsData = '';
	$dataArray = sql_select("select po_breakdown_id, barcode_no from pro_roll_details where entry_form=83 and status_active=1 and is_deleted=0 and barcode_no in($data) and re_transfer=0");
	foreach ($dataArray as $row) {
		$poIdsData .= $row[csf('barcode_no')] . "_" . $row[csf('po_breakdown_id')] . ",";
	}
	echo substr($poIdsData, 0, -1);
	exit();
}

if ($action == "check_batch_no") {
	$data = explode("**", $data);
	$sql = "select id, batch_no from pro_batch_create_mst where batch_no='" . trim($data[0]) . "' and company_id='" . $data[1] . "' and is_deleted=0 and status_active=1 and entry_form=0 order by id desc";
	$data_array = sql_select($sql, 1);
	if (count($data_array) > 0) {
		echo $data_array[0][csf('id')];
	} else {
		echo "0";
	}
	exit();
}

if ($action == "check_report_button") {
	$sql = "select format_id from lib_report_template where template_name='" . trim($data) . "' and report_id=27 and is_deleted=0 and status_active=1";
	$data_array = sql_select($sql);
	if (count($data_array) > 0) {
		echo $data_array[0][csf('format_id')];
	} else {
		echo "";
	}
	exit();
}

if ($action == "load_scanned_barcode_nos") {
	$scanned_arr = array();
	//$dataArr=sql_select("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0");
	$issued_barcode_data = sql_select("select a.barcode_no from pro_roll_details a, inv_grey_fabric_issue_dtls b where a.dtls_id=b.id and a.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$data "); // and is_returned !=1
	foreach ($issued_barcode_data as $row) {
		$scanned_arr[] = $row[csf('barcode_no')];
	}
	$jsbarcode_array = json_encode($scanned_arr);
	echo $jsbarcode_array;
	exit();
}

if ($action == "populate_barcode_data") {
	$barcodeData = '';
	$po_ids_arr = array();
	$po_details_array = array();
	$barcodeDataArr = array();
	$barcodeBuyerArr = array();
	$transRollIds = '';
	$transPoIdsArr = array();
	$company_name_array = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$po_cancel_status_arr = return_library_array("select id, status_active from wo_po_break_down where status_active =3 and is_deleted=0", 'id', 'status_active');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

	//$tmp_data=explode(",",$data);
	//$tmp_data=array_flip($tmp_data);
	//$scanned_barcode_data=sql_select("select barcode_no from pro_roll_details where entry_form=61 and is_returned!=1 and barcode_no in( $data ) and status_active=1 and is_deleted=0");

	/*$requisition_arr=sql_select( "select a.requisition_status,b.barcode_no from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b where a.id = b.mst_id and a.entry_form in (110,180,183)  and b.barcode_no in ($data) and a.requisition_status=1 and b.status_active=1");

	if(!empty($requisition_arr))
	{
		echo "999!!barcode is in requision";
		die;
	}*/
	//$scanned_barcode_data=sql_select("select a.barcode_no, a.entry_form, a.mst_id, a.dtls_id, a.is_returned, a.po_breakdown_id, a.re_transfer, a.booking_without_order from pro_roll_details a where a.barcode_no in($data) and entry_form !=56 and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0");

	$scanned_barcode_data = sql_select("SELECT a.barcode_no, a.entry_form, a.mst_id, a.dtls_id, a.is_returned, a.po_breakdown_id, a.re_transfer, a.booking_without_order, b.trans_id
	from pro_roll_details a left join pro_grey_prod_entry_dtls b on a.dtls_id =b.id and entry_form in (2,22,58) and b.trans_id <> 0
	where a.barcode_no in($data) and entry_form !=56 and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0");


	$all_barcode_for_program_cond = " and barcode_no in($data)";
	$sqlprog = sql_select("select barcode_no,booking_no from pro_roll_details where entry_form=2 and receive_basis=2 and status_active=1 $all_barcode_for_program_cond ");
	$programNoArr = array();

	foreach ($sqlprog as $row) {
		$programNoArr[$row[csf('barcode_no')]] = $row[csf('booking_no')];
	}



	$pre_sys_ref = array();
	$barcodeArrCheck = array();
	$order_to_sample_check = 0;
	$sample_to_order_check = 0;
	foreach ($scanned_barcode_data as $row) {
		if ($row[csf("entry_form")] == 61 && $row[csf("is_returned")] != 1) {
			$issue_number = return_field_value("issue_number as issue_number", "inv_issue_master", "status_active=1 and id='" . $row[csf("mst_id")] . "'", "issue_number");
			echo "99!!" . $issue_number;
			die;
		}

		if ($row[csf("re_transfer")] == 0 && ($row[csf("entry_form")] == 110 || $row[csf("entry_form")] == 180 || $row[csf("entry_form")] == 82) && $row[csf("booking_without_order")] == 1) {
			$order_to_sample_check = 1;
		}

		if ($row[csf("re_transfer")] == 0 && ($row[csf("entry_form")] == 183 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 82) && $row[csf("booking_without_order")] == 0) {
			$sample_to_order_check = 1;
		}

		if ($row[csf("re_transfer")] == 0 && $row[csf("entry_form")] == 133 && $row[csf("booking_without_order")] == 0) {
			$sales_order_to_order_check = 1;
		}

		if ($row[csf("re_transfer")] == 0 && $row[csf("entry_form")] == 84) {
			$issue_return_check = 1;
			$issue_return_booking_without_order = $row[csf("booking_without_order")];
		}

		if ($row[csf("re_transfer")] == 0 && ($row[csf("entry_form")] == 183 || $row[csf("entry_form")] == 83 || $row[csf("entry_form")] == 82 || $row[csf("entry_form")] == 133 || $row[csf("entry_form")] == 110)) {
			$transPoIdsArr[$row[csf("barcode_no")]] = $row[csf("po_breakdown_id")];

			if ($row[csf("booking_without_order")] == 0) {
				$po_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			}
			$transEntryFormArr[$row[csf("barcode_no")]] = $row[csf("entry_form")];
			if ($row[csf("entry_form")] == 133) {
				$transFSO_Arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			}
		}

		if ($row[csf("trans_id")] == "" || $row[csf("trans_id")] == 0) {
			//only transfer barcode, without auto receive, issue, issue return data comes here
			if ($row[csf("entry_form")] != 2 && $row[csf("entry_form")] != 61) {
				//here condition for normal process receive (omit production entry form 2) and auto transfer receive data
				//Only transfer and iss return data
				$barcodeArrCheck = $row[csf("barcode_no")];
			}
		} elseif ($row[csf("entry_form")] == 2 || $row[csf("entry_form")] == 22 || $row[csf("entry_form")] == 58) {
			$barcodeArrCheck = $row[csf("barcode_no")];
		}
	}
	if (empty($scanned_barcode_data)) {
		echo "0";
		die;
	}
	if (empty($barcodeArrCheck)) {
		echo "0";
		die;
	}
	// echo "<pre>";print_r($all_po_ids_arr);die;
	//2,22,58     110,183,82,83    84


	//echo "99!!Here";die;
	//$jsscanned_barcode_array= json_encode($scanned_barcode_array);
	if ($issue_return_check) {
		if ($issue_return_booking_without_order == 1) {
			$issue_return_sql = sql_select("SELECT a.barcode_no, b.recv_number, a.po_breakdown_id, a.roll_id, a.roll_no, b.company_id, b.store_id, d.booking_no, d.grouping, c.prod_id, c.floor_id, c.room, c.rack, c.self, c.bin_box, d.buyer_id, a.booking_without_order, c.body_part_id, a.entry_form, d.id as booking_id, c.yarn_rate, c.kniting_charge, a.rate from pro_roll_details a, inv_receive_master b, pro_grey_prod_entry_dtls c, wo_non_ord_samp_booking_mst d where a.barcode_no in($data) and a.mst_id=b.id and a.dtls_id=c.id and a.entry_form=84 and a.po_breakdown_id=d.id  and a.status_active=1 and b.status_active =1 and c.status_active =1 and a.re_transfer=0 and a.booking_without_order=1");
		} else {
			$issue_return_sql = sql_select("SELECT a.barcode_no, b.recv_number, a.po_breakdown_id, a.roll_id, a.roll_no, b.company_id, b.store_id, c.floor_id, c.room, c.rack, c.self, c.bin_box, d.po_number, d.grouping, e.job_no, c.prod_id, e.buyer_name as buyer_id, a.booking_without_order, c.body_part_id, a.entry_form, c.yarn_rate, c.kniting_charge, a.rate from pro_roll_details a, inv_receive_master b, pro_grey_prod_entry_dtls c, wo_po_break_down d, wo_po_details_master e where a.barcode_no in($data) and a.mst_id=b.id and a.entry_form=84 and b.id=c.mst_id and a.dtls_id=c.id and a.po_breakdown_id= d.id and d.job_id=e.id and a.status_active=1 and b.status_active=1 and a.re_transfer=0 and a.booking_without_order=0");
		}

		$issue_return_data = array();
		foreach ($issue_return_sql as $row) {
			$issue_return_data[$row[csf("barcode_no")]]["company_id"] = $row[csf("company_id")];
			$issue_return_data[$row[csf("barcode_no")]]["recv_number"] = $row[csf("recv_number")];
			$issue_return_data[$row[csf("barcode_no")]]["po_breakdown_id"] = $row[csf("po_breakdown_id")];
			$issue_return_data[$row[csf("barcode_no")]]["booking_without_order"] = $row[csf("booking_without_order")];
			$issue_return_data[$row[csf("barcode_no")]]["buyer_id"] = $row[csf("buyer_id")];
			$issue_return_data[$row[csf("barcode_no")]]["id"] = $row[csf("id")];
			$issue_return_data[$row[csf("barcode_no")]]["booking_no"] = $row[csf("booking_no")];
			$issue_return_data[$row[csf("barcode_no")]]["entry_form"] = $row[csf("entry_form")];
			$issue_return_data[$row[csf("barcode_no")]]["booking_id"] = $row[csf("booking_id")];
			$issue_return_data[$row[csf("barcode_no")]]["grouping"] = $row[csf("grouping")];
			$issue_return_data[$row[csf("barcode_no")]]["prod_id"] = $row[csf("prod_id")];
			$issue_return_data[$row[csf("barcode_no")]]["roll_id"] = $row[csf("roll_id")];
			$issue_return_data[$row[csf("barcode_no")]]["roll_no"] = $row[csf("roll_no")];
			$issue_return_data[$row[csf("barcode_no")]]["floor_id"] = $row[csf("floor_id")];
			$issue_return_data[$row[csf("barcode_no")]]["room"] = $row[csf("room")];
			$issue_return_data[$row[csf("barcode_no")]]["rack"] = $row[csf("rack")];
			$issue_return_data[$row[csf("barcode_no")]]["self"] = $row[csf("self")];
			$issue_return_data[$row[csf("barcode_no")]]["bin_box"] = $row[csf("bin_box")];


			$barcode_store_arr[$row[csf("barcode_no")]]["trans"] = $row[csf("store_id")];
			$body_part_id_data[$row[csf("barcode_no")]]["trans"] = $row[csf("body_part_id")];

			$yarn_rate_data[$row[csf("barcode_no")]]["yarn_rate"]['trans'] = $row[csf("yarn_rate")];
			$knitting_charge_data[$row[csf("barcode_no")]]["kniting_charge"]['trans'] = $row[csf("kniting_charge")];
			$roll_rate_data[$row[csf("barcode_no")]]["rate"]['trans'] = $row[csf("rate")];

			$po_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
		}
		unset($issue_return_sql);
	}
	//echo "99!!";die;
	if ($order_to_sample_check) {
		$order_to_sample_sql = sql_select("SELECT c.barcode_no, c.po_breakdown_id, b.buyer_id, b.id, b.booking_no , d.to_store as store_id, a.company_id, a.to_company, d.to_prod_id, d.from_prod_id, d.to_floor_id, d.to_room, d.to_rack, d.to_shelf, d.to_bin_box, d.to_body_part, d.yarn_rate, d.kniting_charge, c.rate
			from pro_roll_details c, wo_non_ord_samp_booking_mst b, inv_item_transfer_dtls d, inv_item_transfer_mst a
			where c.barcode_no in($data) and c.entry_form in(110,180,82) and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 and b.id=c.po_breakdown_id and c.dtls_id = d.id and d.mst_id = a.id and c.re_transfer=0");
		$order_to_sample_data = array();
		foreach ($order_to_sample_sql as $row) {
			$order_to_sample_data[$row[csf("barcode_no")]]["po_breakdown_id"] = $row[csf("po_breakdown_id")];
			$order_to_sample_data[$row[csf("barcode_no")]]["buyer_id"] = $row[csf("buyer_id")];
			$order_to_sample_data[$row[csf("barcode_no")]]["id"] = $row[csf("id")];
			$order_to_sample_data[$row[csf("barcode_no")]]["booking_no"] = $row[csf("booking_no")];

			$to_company = ($row[csf("to_company")] == 0) ? $row[csf("company_id")] : $row[csf("to_company")];
			$to_prod_id = ($row[csf("to_prod_id")] == 0) ? $row[csf("from_prod_id")] : $row[csf("to_prod_id")];

			$order_to_sample_data[$row[csf("barcode_no")]]["company_id"] = $to_company;
			$order_to_sample_data[$row[csf("barcode_no")]]["prod_id"] = $to_prod_id;
			$order_to_sample_data[$row[csf("barcode_no")]]["to_floor_id"] = $row[csf("to_floor_id")];
			$order_to_sample_data[$row[csf("barcode_no")]]["to_room"] = $row[csf("to_room")];
			$order_to_sample_data[$row[csf("barcode_no")]]["to_rack"] = $row[csf("to_rack")];
			$order_to_sample_data[$row[csf("barcode_no")]]["to_shelf"] = $row[csf("to_shelf")];
			$order_to_sample_data[$row[csf("barcode_no")]]["to_bin_box"] = $row[csf("to_bin_box")];

			$yarn_rate_data[$row[csf("barcode_no")]]["yarn_rate"]['trans'] = $row[csf("yarn_rate")];
			$knitting_charge_data[$row[csf("barcode_no")]]["kniting_charge"]['trans'] = $row[csf("kniting_charge")];
			$roll_rate_data[$row[csf("barcode_no")]]["rate"]['trans'] = $row[csf("rate")];

			$barcode_store_arr[$row[csf("barcode_no")]]["trans"] = $row[csf("store_id")];
			$body_part_id_data[$row[csf("barcode_no")]]["trans"] = $row[csf("to_body_part")];
		}
		unset($order_to_sample_sql);
	}

	if ($sample_to_order_check) {
		$sample_to_order_sql = sql_select("SELECT a.transfer_system_id,a.entry_form, a.company_id,a.to_company,a.transfer_criteria, b.to_prod_id, e.job_no, e.buyer_name, e.style_ref_no, e.gmts_item_id, d.po_number, d.po_quantity, d.pub_shipment_date AS shipment_date, c.barcode_no, c.id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.roll_id as roll_id_prev, b.yarn_rate, b.kniting_charge, c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.is_sales, c.qc_pass_qnty_pcs, b.to_store as store_id, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.to_body_part,  c.re_transfer
		FROM pro_roll_details c, inv_item_transfer_dtls b, wo_po_break_down d, inv_item_transfer_mst a, wo_po_details_master e
		WHERE c.barcode_no IN($data) and c.status_active=1 and c.is_deleted=0 AND c.entry_form in (183,83,82) and c.re_transfer=0 AND b.id=c.dtls_id AND c.po_breakdown_id = d.id AND a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and e.id=d.job_id");

		$sample_to_order_data = array();
		foreach ($sample_to_order_sql as $row) {
			if ($row[csf("entry_form")] == 183 || $row[csf("entry_form")] == 82) {
				$gmts_item_id = explode(",", $row[csf('gmts_item_id')]);
				foreach ($gmts_item_id as $item_id) {
					if ($gmts_item == "")
						$gmts_item = $garments_item[$item_id];
					else
						$gmts_item .= "," . $garments_item[$item_id];
				}

				$sample_to_order_data[$row[csf("barcode_no")]]["company_id"] = $row[csf("company_id")];
				$sample_to_order_data[$row[csf("barcode_no")]]["entry_form"] = $row[csf("entry_form")];
				$sample_to_order_data[$row[csf("barcode_no")]]["is_transfer"] = $row[csf("is_transfer")];
				$sample_to_order_data[$row[csf("barcode_no")]]["transfer_id"] = $row[csf("transfer_system_id")];
				$sample_to_order_data[$row[csf("barcode_no")]]["po_breakdown_id"] = $row[csf("po_breakdown_id")];
				$sample_to_order_data[$row[csf("barcode_no")]]["is_sales"] = $row[csf("is_sales")];
				$sample_to_order_data[$row[csf("barcode_no")]]["buyer_id"] = $row[csf("buyer_name")];
				$sample_to_order_data[$row[csf("barcode_no")]]["is_transfer"] = $row[csf("is_transfer")];
				$sample_to_order_data[$row[csf("barcode_no")]]["roll_id"] = $row[csf("roll_id")];
				$sample_to_order_data[$row[csf("barcode_no")]]["roll_no"] = $row[csf("roll_no")];
				$sample_to_order_data[$row[csf("barcode_no")]]["body_part"] = $row[csf("gmts_item_id")];
				$sample_to_order_data[$row[csf("barcode_no")]]["to_company"] = $row[csf("to_company")];
				$sample_to_order_data[$row[csf("barcode_no")]]["transfer_criteria"] = $row[csf("transfer_criteria")];
				$sample_to_order_data[$row[csf("barcode_no")]]["to_prod_id"] = $row[csf("to_prod_id")];
				$sample_to_order_data[$row[csf("barcode_no")]]["to_floor_id"] = $row[csf("to_floor_id")];
				$sample_to_order_data[$row[csf("barcode_no")]]["to_room"] = $row[csf("to_room")];
				$sample_to_order_data[$row[csf("barcode_no")]]["to_rack"] = $row[csf("to_rack")];
				$sample_to_order_data[$row[csf("barcode_no")]]["to_shelf"] = $row[csf("to_shelf")];
				$sample_to_order_data[$row[csf("barcode_no")]]["to_bin_box"] = $row[csf("to_bin_box")];
				$sample_to_order_data[$row[csf("barcode_no")]]["barcode_no"] = $row[csf("barcode_no")];
				$sample_to_order_data[$row[csf("barcode_no")]]["booking_without_order"] = $row[csf("booking_without_order")];
			} else {
				$order_to_order_data[$row[csf("barcode_no")]]["po_breakdown_id"] = $row[csf("po_breakdown_id")];
				$sample_to_order_data[$row[csf("barcode_no")]]["entry_form"] = $row[csf("entry_form")];
				$sample_to_order_data[$row[csf("barcode_no")]]["is_transfer"] = $row[csf("is_transfer")];
				$sample_to_order_data[$row[csf("barcode_no")]]["barcode_no"] = $row[csf("barcode_no")];
			}
			$barcode_store_arr[$row[csf("barcode_no")]]["trans"] = $row[csf("store_id")];
			$body_part_id_data[$row[csf("barcode_no")]]["trans"] = $row[csf("to_body_part")];

			$yarn_rate_data[$row[csf("barcode_no")]]["yarn_rate"]['trans'] = $row[csf("yarn_rate")];
			$knitting_charge_data[$row[csf("barcode_no")]]["kniting_charge"]['trans'] = $row[csf("kniting_charge")];
			$roll_rate_data[$row[csf("barcode_no")]]["rate"]['trans'] = $row[csf("rate")];
		}
		unset($sample_to_order_sql);
	}

	if ($sales_order_to_order_check) {

		// echo "SELECT a.transfer_system_id, c.barcode_no, c.id, c.po_breakdown_id, b.to_store as store_id, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.to_body_part,c.entry_form
		// FROM  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, fabric_sales_order_mst d
		// WHERE c.barcode_no IN($data)  and c.entry_form in (133) and c.re_transfer=0 and b.id=c.dtls_id and c.po_breakdown_id = d.id AND a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";

		$sales_ord_to_ord_sql = sql_select("SELECT a.transfer_criteria, a.company_id, a.to_company, a.transfer_system_id, c.barcode_no, c.id, c.po_breakdown_id, b.from_prod_id, b.to_store as store_id, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.to_body_part, b.to_prod_id,c.entry_form
		FROM  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, fabric_sales_order_mst d
		WHERE c.barcode_no IN($data)  and c.entry_form in (133) and c.re_transfer=0 and b.id=c.dtls_id and c.po_breakdown_id = d.id AND a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0");

		$sales_order_to_order_data = array();

		foreach ($sales_ord_to_ord_sql as $row) {
			if ($row[csf("transfer_criteria")] == 1) {
				$company_id = $row[csf("to_company")];
				$prod_id = $row[csf("to_prod_id")];
			} else {
				$company_id = $row[csf("company_id")];
				$prod_id = $row[csf("from_prod_id")];
			}
			$sales_order_to_order_data[$row[csf("barcode_no")]]["entry_form"] = $row[csf("entry_form")];
			$sales_order_to_order_data[$row[csf("barcode_no")]]["transfer_id"] = $row[csf("transfer_system_id")];
			$sales_order_to_order_data[$row[csf("barcode_no")]]["to_floor_id"] = $row[csf("to_floor_id")];
			$sales_order_to_order_data[$row[csf("barcode_no")]]["to_room"] = $row[csf("to_room")];
			$sales_order_to_order_data[$row[csf("barcode_no")]]["to_rack"] = $row[csf("to_rack")];
			$sales_order_to_order_data[$row[csf("barcode_no")]]["to_shelf"] = $row[csf("to_shelf")];
			$sales_order_to_order_data[$row[csf("barcode_no")]]["to_bin_box"] = $row[csf("to_bin_box")];
			$sales_order_to_order_data[$row[csf("barcode_no")]]["to_prod_id"] = $prod_id;
			$sales_order_to_order_data[$row[csf("barcode_no")]]["company_id"] = $company_id;

			$barcode_store_arr[$row[csf("barcode_no")]]["trans"] = $row[csf("store_id")];
			$body_part_id_data[$row[csf("barcode_no")]]["trans"] = $row[csf("to_body_part")];
		}

		unset($sales_ord_to_ord_sql);
	}




	/*$data_array_receive_basis = sql_select("select a.id, a.company_id, a.recv_number, a.booking_no, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, c.roll_no, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty, c.coller_cuff_size
		from inv_receive_master a, pro_roll_details c
		where c.barcode_no in($data) and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and a.entry_form=2 and a.receive_basis in(1,2) and a.status_active=1 and a.is_deleted=0 and a.id=c.mst_id ");*/

	$data_array_receive_basis = sql_select("SELECT a.id, a.company_id, a.recv_number, a.booking_no, a.receive_basis, a.receive_date, a.knitting_source, a.knitting_company, c.roll_no, c.barcode_no, c.id as roll_id, c.po_breakdown_id, c.qnty, c.qc_pass_qnty, c.coller_cuff_size, e.proceed_knitting, e.proceed_dyeing, c.is_sales
	from pro_roll_details c, inv_receive_master a
	left join ppl_planning_info_entry_dtls b on a.booking_id=b.id and a.receive_basis=2
	left join ppl_planning_info_entry_mst d on b.mst_id=d.id
	left join wo_booking_mst e on e.booking_no = d.booking_no
	where c.barcode_no in($data) and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and a.entry_form=2 and a.receive_basis in(1,2) and a.status_active=1 and a.is_deleted=0 and a.id=c.mst_id");

	foreach ($data_array_receive_basis as $row) {
		$receive_basis_arr[$row[csf('barcode_no')]]['plan_id'] = $row[csf('booking_no')];
		$receive_basis_arr[$row[csf('barcode_no')]]['coller_cuff_size'] = $row[csf('coller_cuff_size')];
		$receive_basis_arr[$row[csf('barcode_no')]]['receive_basis'] = $row[csf('receive_basis')];
		if ($row[csf('receive_basis')] == 2) {
			$program_no_plan_basis_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
			/*if($row[csf("proceed_knitting")] == 1 && $row[csf("proceed_dyeing")] == 2 && $row[csf("is_sales")] !=1)
			{
				$proceed_knitting_only[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
			}*/
		}
	}
	unset($data_array_receive_basis);

	$proceed_knitting_sql = sql_select("SELECT a.barcode_no, c.proceed_knitting, c.proceed_dyeing, a.is_sales, b.booking_no
	from pro_roll_details a, wo_booking_dtls b, wo_booking_mst c
	where  a.po_breakdown_id=b.po_break_down_id and b.booking_no = c.booking_no and a.booking_without_order=0 and a.is_sales !=1 and a.barcode_no in($data) and a.re_transfer=0 and c.booking_type =1 and c.is_short=2 and a.entry_form in (22,58,84,82,83,183) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and c.proceed_knitting=1 and c.proceed_dyeing=2 and a.is_sales!=1
	group by a.barcode_no, c.proceed_knitting, c.proceed_dyeing, a.is_sales, b.booking_no");
	foreach ($proceed_knitting_sql as $key => $row) {
		$proceed_knitting_only[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
	}

	if (!empty($proceed_knitting_only)) {
		echo "999!!Only Proceed for Knitting Check Found at Fabric booking Page. So Roll Can Not Issue of This Program barcode.\nBarcode no :" . implode(',', $proceed_knitting_only);
		die;
	}
	// echo "999!!proceed_knitting check";die;

	$program_no_plan_basis_arr = array_filter($program_no_plan_basis_arr);
	if (count($program_no_plan_basis_arr) > 0) {
		$all_program_id = implode(",", $program_no_plan_basis_arr);
		$program_id_cond = "";
		$progIds_cond = "";
		if ($db_type == 2 && count($program_no_plan_basis_arr) > 999) {
			$program_no_plan_basis_chunk = array_chunk($program_no_plan_basis_arr, 999);
			foreach ($program_no_plan_basis_chunk as $chunk_arr) {
				$chunk_arr_value = implode(",", $chunk_arr);
				$progIds_cond .= "  a.id in($chunk_arr_value) or ";
			}

			$program_id_cond .= " and (" . chop($bokIds_cond, 'or ') . ")";
			//echo $booking_id_cond;die;
		} else {
			$program_id_cond = " and a.id in($all_program_id)";
		}

		$booking_no_planbasis_sql = sql_select("select a.id,b.booking_no from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id = b.id and a.status_active=1 and a.is_deleted=0 $program_id_cond");
		foreach ($booking_no_planbasis_sql as  $val) {
			$booking_no_plan_basis_arr[$val[csf("id")]] =  $val[csf("booking_no")];
		}
	}

	//zs c.qc_pass_qnty_pcs
	// echo "SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.location_id, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.machine_no_id, b.brand_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.roll_id as roll_id_prev, b.yarn_rate, b.kniting_charge, c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.transfer_criteria, c.is_sales, c.qc_pass_qnty_pcs, a.store_id
	// FROM pro_roll_details c, pro_grey_prod_entry_dtls b, inv_receive_master a
	// WHERE c.barcode_no in($data) and c.entry_form in(2,22,58)  and c.status_active=1 and c.is_deleted=0 and b.trans_id<>0 and a.entry_form in(2,22,58) and b.id=c.dtls_id and a.id=b.mst_id and c.is_service=0";
	$data_array = sql_select("SELECT a.id, a.entry_form, a.company_id, b.program_no, a.receive_basis, a.location_id, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.machine_no_id, b.brand_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.roll_id as roll_id_prev, b.yarn_rate, b.kniting_charge, c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.transfer_criteria, c.is_sales, c.qc_pass_qnty_pcs, a.store_id
		FROM pro_roll_details c, pro_grey_prod_entry_dtls b, inv_receive_master a
		WHERE c.barcode_no in($data) and c.entry_form in(2,22,58)  and c.status_active=1 and c.is_deleted=0 and b.trans_id<>0 and a.entry_form in(2,22,58) and b.id=c.dtls_id and a.id=b.mst_id and c.is_service=0");




	foreach ($data_array as $row) {
		$color_id_ref_arr[$row[csf("color_id")]] = chop($row[csf("color_id")], ",");
	}

	$color_id_ref_arr = array_filter(array_unique($color_id_ref_arr));
	if (count($color_id_ref_arr) > 0) {
		$all_color_ids = implode(",", $color_id_ref_arr);
		$all_color_id_cond = "";
		$colorCond = "";
		if ($db_type == 2 && count($color_id_ref_arr) > 999) {
			$color_id_ref_chunk = array_chunk($color_id_ref_arr, 999);
			foreach ($color_id_ref_chunk as $chunk_arr) {
				$chunk_arr_value = implode(",", $chunk_arr);
				$colorCond .= " id in($chunk_arr_value) or ";
			}

			$all_color_id_cond .= " and (" . chop($colorCond, 'or ') . ")";
		} else {
			$all_color_id_cond = " and id in($all_color_ids)";
		}

		$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0 $all_color_id_cond", "id", "color_name");
	}


	$is_sales_arr = array();
	foreach ($data_array as $row) {
		$is_sales_arr[$row[csf('barcode_no')]] = $row[csf("is_sales")];
		$booking_no_id = $row[csf('po_breakdown_id')];
		if (($row[csf("entry_form")] == 2 && $row[csf("receive_basis")] == 0) || ($row[csf("entry_form")] == 22 && ($row[csf("receive_basis")] == 4 || $row[csf("receive_basis")] == 6))) {
			$receive_basis = "Independent";
			$receive_basis_id = 0;
		} else if (($row[csf("entry_form")] == 2 && $row[csf("receive_basis")] == 1) || ($row[csf("entry_form")] == 22 && $row[csf("receive_basis")] == 2)) {
			$receive_basis = "Booking";
			$receive_basis_id = 2;
		} else if ($row[csf("entry_form")] == 2 && $row[csf("receive_basis")] == 2) {
			$receive_basis = "Knitting Plan";
			$receive_basis_id = 3;
		} else if ($row[csf("entry_form")] == 22 && $row[csf("receive_basis")] == 1) {
			$receive_basis = "PI";
			$receive_basis_id = 1;
		} else if ($row[csf("entry_form")] == 58) {
			$receive_basis = "Delivery";
			$receive_basis_id = 9;
		}

		if ($row[csf("knitting_source")] == 1) {
			$knit_company = $company_name_array[$row[csf("knitting_company")]];
		} else if ($row[csf("knitting_source")] == 3) {
			$knit_company = $supplier_arr[$row[csf("knitting_company")]];
		}

		$rate = $row[csf("rate")];
		//$yarn_rate=$row[csf("yarn_rate")];
		//$kniting_charge=$row[csf("kniting_charge")];

		$yarn_rate_data[$row[csf("barcode_no")]]["yarn_rate"]['rcv'] = $row[csf("yarn_rate")];
		$knitting_charge_data[$row[csf("barcode_no")]]["kniting_charge"]['rcv'] = $row[csf("kniting_charge")];
		$roll_rate_data[$row[csf("barcode_no")]]["rate"]['rcv'] = $row[csf("rate")];


		if ($row[csf("entry_form")] == 58) {
			//$rate=$row[csf("rate")];
			$roll_id = $row[csf("roll_id_prev")];
		} else {
			$roll_id = $row[csf("roll_id")];
			//$rate='';
		}

		$buyer_id = '';
		if ($row[csf("booking_without_order")] == 1) {
			//$buyer_id=$without_order_buyer[$row[csf("barcode_no")]];
			$buyer_id = $order_to_sample_data[$row[csf("barcode_no")]]["buyer_id"];
			$sample_booking_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
		} else {
			if ($row[csf("is_sales")] == 1) {
				$is_salesOrder = 1;
				$sales_order_no = $booking_no_id;
				$all_sales_ids .= $booking_no_id . ",";
			} else {
				if ($order_to_sample_data[$row[csf("barcode_no")]]["po_breakdown_id"] == "") {
					// echo $row[csf("po_breakdown_id")].'==<br>';
					$po_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
				} else {
					$buyer_id = $order_to_sample_data[$row[csf("barcode_no")]]["buyer_id"];
				}
			}
		}

		$is_transfer = 0;
		if ($row[csf("is_transfer")] == 6 && $row[csf("transfer_criteria")] == 4 && $row[csf("entry_form")] == 58) {
			$transRollIds .= $row[csf("roll_id_prev")] . ",";
			$is_transfer = 1;
		} elseif ($row[csf("is_transfer")] == 6 && $row[csf("transfer_criteria")] == 4 && $row[csf("entry_form")] != 58) {
			$transRollIds .= $row[csf("roll_id")] . ",";
			$is_transfer = 1;
		}
		//echo $is_salesOrder; die;

		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$count = '';
		$yarn_count = explode(",", $row[csf('yarn_count')]);
		foreach ($yarn_count as $count_id) {
			if ($count == '')
				$count = $yarn_count_details[$count_id];
			else
				$count .= "," . $yarn_count_details[$count_id];
		}

		$brand = '';
		$brand_id = explode(",", $row[csf('brand_id')]);
		$program_no = $programNoArr[$row[csf('barcode_no')]];
		foreach ($brand_id as $brand_id) {
			if ($brand == '')
				$brand = $brand_arr[$brand_id];
			else
				$brand .= "," . $brand_arr[$brand_id];
		}

		$barcode_store_arr[$row[csf("barcode_no")]]["rcv"] = $row[csf("store_id")];
		$body_part_id_data[$row[csf("barcode_no")]]["rcv"] = $row[csf("body_part_id")];
		if ($sample_to_order_data[$row[csf('barcode_no')]]["entry_form"] == 183) //Roll Order To Order Transfer
		{
			$receive_basis = "Delivery";
			$receive_basis_id = 9;

			$is_transfer = 0;
			if ($row[csf("is_transfer")] == 6 && $row[csf("transfer_criteria")] == 7) {
				$transRollIds .= $sample_to_order_data[$row[csf("barcode_no")]]["roll_id"] . ",";
				$is_transfer = 1;
			}

			$barcodeDataArr[$row[csf('barcode_no')]] = $row[csf("barcode_no")] . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["to_company"] . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["roll_no"] . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["roll_id"] . "**" . $row[csf("location_id")] . "**" . $row[csf("machine_no_id")] . "**" . $body_part[$row[csf("body_part_id")]] . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["job_no"] . "**" . $receive_basis . "**" . $receive_basis_id . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["transfer_id"] . "**" . $row[csf("po_breakdown_id")] . "**" . $color . "**" . $row[csf("color_id")] . "**" . $row[csf("knitting_source")] . "**" . $knitting_source[$row[csf("knitting_source")]] . "**" . $row[csf("knitting_company")] . "**" . $knit_company . "**" . $row[csf("yarn_lot")] . "**" . $row[csf('yarn_count')] . "**" . $row[csf("stitch_length")] . "**" . $row[csf("brand_id")] . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["to_rack"] . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["to_shelf"] . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["to_prod_id"] . "**" . $row[csf("febric_description_id")] . "**" . $row[csf("gsm")] . "**" . $row[csf("width")] . "**" . number_format($row[csf("qnty")], 2, '.', '') . "**" . $rate . "**0**" . $count . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["to_floor_id"] . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["to_room"] . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["to_bin_box"] . "**" . $brand . "**" . $program_no;

			$barcodeBuyerArr[$row[csf('barcode_no')]] = "0__" . $sample_to_order_data[$row[csf("barcode_no")]]["po_breakdown_id"] . "__" . $is_transfer . "__" . $sample_to_order_data[$row[csf("barcode_no")]]["buyer_id"] . "__" . $row[csf("qc_pass_qnty_pcs")];
		} else if ($sample_to_order_data[$row[csf("barcode_no")]]["entry_form"] == 82) // Roll Transfer
		{
			$receive_basis = "Delivery";
			$receive_basis_id = 9;

			$is_transfer = 0;
			if ($sample_to_order_data[$row[csf("barcode_no")]]["is_transfer"] == 6) {
				$transRollIds .= $sample_to_order_data[$row[csf("barcode_no")]]["roll_id"] . ",";
				$is_transfer = 1;
				//$company_id=$sample_to_order_data[$row[csf("barcode_no")]]["to_company"];
			} else {
				//$company_id=$sample_to_order_data[$row[csf("barcode_no")]]["company_id"];
			}

			if ($sample_to_order_data[$row[csf("barcode_no")]]["transfer_criteria"] == 2) {
				$company_id = $sample_to_order_data[$row[csf("barcode_no")]]["company_id"];
				$to_prod_id = $row[csf("prod_id")];
			} else {
				$company_id = $sample_to_order_data[$row[csf("barcode_no")]]["to_company"];
				$to_prod_id = $sample_to_order_data[$row[csf("barcode_no")]]["to_prod_id"];
			}


			//$sample_to_order_data[$row[csf("barcode_no")]]["job_no"]
			$barcodeDataArr[$row[csf('barcode_no')]] = $row[csf("barcode_no")] . "**" . $company_id . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["roll_no"] . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["roll_id"] . "**" . $row[csf("location_id")] . "**" . $row[csf("machine_no_id")] . "**" . $body_part[$row[csf("body_part_id")]] . "**" . $row[csf("bwo")] . "**" . $receive_basis . "**" . $receive_basis_id . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["transfer_id"] . "**" . $row[csf("po_breakdown_id")] . "**" . $color . "**" . $row[csf("color_id")] . "**" . $row[csf("knitting_source")] . "**" . $knitting_source[$row[csf("knitting_source")]] . "**" . $row[csf("knitting_company")] . "**" . $knit_company . "**" . $row[csf("yarn_lot")] . "**" . $row[csf('yarn_count')] . "**" . $row[csf("stitch_length")] . "**" . $row[csf("brand_id")] . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["to_rack"] . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["to_shelf"] . "**" . $to_prod_id . "**" . $row[csf("febric_description_id")] . "**" . $row[csf("gsm")] . "**" . $row[csf("width")] . "**" . number_format($row[csf("qnty")], 2, '.', '') . "**" . $rate . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["booking_without_order"] . "**" . $count . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["to_floor_id"] . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["to_room"] . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["to_bin_box"] . "**" . $brand . "**" . $program_no;

			$barcodeBuyerArr[$row[csf('barcode_no')]] = $sample_to_order_data[$row[csf("barcode_no")]]["booking_without_order"] . "__" . $sample_to_order_data[$row[csf("barcode_no")]]["po_breakdown_id"] . "__" . $is_transfer . "__" . $sample_to_order_data[$row[csf("barcode_no")]]["buyer_id"] . "__" . $row[csf("qc_pass_qnty_pcs")];
		} else if ($issue_return_data[$row[csf("barcode_no")]]["entry_form"] == 84) // Roll Issue Return
		{
			$receive_basis = "Delivery";
			$receive_basis_id = 9;
			$is_transfer = 0;
			$company_id = $issue_return_data[$row[csf("barcode_no")]]["company_id"];
			$to_prod_id = $issue_return_data[$row[csf("barcode_no")]]["prod_id"];
			$barcodeDataArr[$row[csf('barcode_no')]] = $row[csf("barcode_no")] . "**" . $company_id . "**" . $issue_return_data[$row[csf("barcode_no")]]["roll_no"] . "**" . $issue_return_data[$row[csf("barcode_no")]]["roll_id"] . "**" . $row[csf("location_id")] . "**" . $row[csf("machine_no_id")] . "**" . $body_part[$row[csf("body_part_id")]] . "**" . $row[csf("bwo")] . "**" . $receive_basis . "**" . $receive_basis_id . "**" . $issue_return_data[$row[csf("barcode_no")]]["recv_number"] . "**" . $issue_return_data[$row[csf("barcode_no")]]["po_breakdown_id"] . "**" . $color . "**" . $row[csf("color_id")] . "**" . $row[csf("knitting_source")] . "**" . $knitting_source[$row[csf("knitting_source")]] . "**" . $row[csf("knitting_company")] . "**" . $knit_company . "**" . $row[csf("yarn_lot")] . "**" . $row[csf('yarn_count')] . "**" . $row[csf("stitch_length")] . "**" . $row[csf("brand_id")] . "**" . $issue_return_data[$row[csf("barcode_no")]]["rack"] . "**" . $issue_return_data[$row[csf("barcode_no")]]["self"] . "**" . $to_prod_id . "**" . $row[csf("febric_description_id")] . "**" . $row[csf("gsm")] . "**" . $row[csf("width")] . "**" . number_format($row[csf("qnty")], 2, '.', '') . "**" . $rate . "**" . $issue_return_data[$row[csf("barcode_no")]]["booking_without_order"] . "**" . $count . "**" . $issue_return_data[$row[csf("barcode_no")]]["floor_id"] . "**" . $issue_return_data[$row[csf("barcode_no")]]["room"] . "**" . $issue_return_data[$row[csf("barcode_no")]]["bin_box"] . "**" . $brand . "**" . $program_no;

			$barcodeBuyerArr[$row[csf('barcode_no')]] = $issue_return_data[$row[csf("barcode_no")]]["booking_without_order"] . "__" . $issue_return_data[$row[csf("barcode_no")]]["po_breakdown_id"] . "__" . $is_transfer . "__" . $issue_return_data[$row[csf("barcode_no")]]["buyer_id"] . "__" . $row[csf("qc_pass_qnty_pcs")];
		} else // Roll Receive
		{
			//echo "string<br>";
			if ($order_to_sample_data[$row[csf("barcode_no")]]["po_breakdown_id"] == "") {
				$is_transfer = 0;
				if (!empty($sample_to_order_data[$row[csf("barcode_no")]]["barcode_no"])) {
					$transRollIds .= $sample_to_order_data[$row[csf("barcode_no")]]["roll_id"] . ",";
					$is_transfer = 1;
					//echo "stringgg<br>";
				}
				//echo "string<br>";

				if ($sales_order_to_order_data[$row[csf('barcode_no')]]["entry_form"] == 133) // sales Order To sales Order Transfer
				{
					$floor_id 	= $sales_order_to_order_data[$row[csf("barcode_no")]]["to_floor_id"];
					$room 	  	= $sales_order_to_order_data[$row[csf("barcode_no")]]["to_room"];
					$rack 	  	= $sales_order_to_order_data[$row[csf("barcode_no")]]["to_rack"];
					$self	  	= $sales_order_to_order_data[$row[csf("barcode_no")]]["to_shelf"];
					$bin_box  	= $sales_order_to_order_data[$row[csf("barcode_no")]]["to_bin_box"];
					$booking_no = $sales_order_to_order_data[$row[csf("barcode_no")]]["transfer_id"];
					$prod_id 	= $sales_order_to_order_data[$row[csf("barcode_no")]]["to_prod_id"];
					$company_id = $sales_order_to_order_data[$row[csf("barcode_no")]]["company_id"];
					//$booking_id = $row[csf("booking_id")]; //  delivery Id

				} else {
					$floor_id 	= $row[csf("floor_id")];
					$room 	  	= $row[csf("room")];
					$rack 	  	= $row[csf("rack")];
					$self 	  	= $row[csf("self")];
					$bin_box  	= $row[csf("bin_box")];
					$booking_no = $row[csf("booking_no")];
					$prod_id 	= $row[csf("prod_id")];
					$company_id = $row[csf("company_id")];
				}


				$barcodeDataArr[$row[csf('barcode_no')]] = $row[csf("barcode_no")] . "**" . $company_id . "**" . $row[csf("roll_no")] . "**" . $roll_id . "**" . $row[csf("location_id")] . "**" . $row[csf("machine_no_id")] . "**" . $body_part[$row[csf("body_part_id")]] . "**" . $row[csf("bwo")] . "**" . $receive_basis . "**" . $receive_basis_id . "**" . $booking_no . "**" . $row[csf("booking_id")] . "**" . $color . "**" . $row[csf("color_id")] . "**" . $row[csf("knitting_source")] . "**" . $knitting_source[$row[csf("knitting_source")]] . "**" . $row[csf("knitting_company")] . "**" . $knit_company . "**" . $row[csf("yarn_lot")] . "**" . $row[csf('yarn_count')] . "**" . $row[csf("stitch_length")] . "**" . $row[csf("brand_id")] . "**" . $rack . "**" . $self . "**" . $prod_id . "**" . $row[csf("febric_description_id")] . "**" . $row[csf("gsm")] . "**" . $row[csf("width")] . "**" . number_format($row[csf("qnty")], 2, '.', '') . "**" . $rate . "**" . $row[csf("booking_without_order")] . "**" . $count . "**" . $floor_id . "**" . $room . "**" . $bin_box . "**" . $brand . "**" . $program_no;

				$barcodeBuyerArr[$row[csf('barcode_no')]] = $row[csf("booking_without_order")] . "__" . $row[csf("po_breakdown_id")] . "__" . $is_transfer . "__" . $buyer_id . "__" . $row[csf("qc_pass_qnty_pcs")];
			} else {
				//echo "string 2";die; $order_to_sample_data[$row[csf("barcode_no")]]["prod_id"]
				$barcodeDataArr[$row[csf('barcode_no')]] = $row[csf("barcode_no")] . "**" . $order_to_sample_data[$row[csf("barcode_no")]]["company_id"] . "**" . $row[csf("roll_no")] . "**" . $roll_id . "**" . $row[csf("location_id")] . "**" . $row[csf("machine_no_id")] . "**" . $body_part[$row[csf("body_part_id")]] . "**" . $order_to_sample_data[$row[csf('barcode_no')]]["booking_no"] . "**" . $receive_basis . "**" . $receive_basis_id . "**" . $order_to_sample_data[$row[csf('barcode_no')]]["booking_no"] . "**" . $order_to_sample_data[$row[csf('barcode_no')]]["id"] . "**" . $color . "**" . $row[csf("color_id")] . "**" . $row[csf("knitting_source")] . "**" . $knitting_source[$row[csf("knitting_source")]] . "**" . $row[csf("knitting_company")] . "**" . $knit_company . "**" . $row[csf("yarn_lot")] . "**" . $row[csf('yarn_count')] . "**" . $row[csf("stitch_length")] . "**" . $row[csf("brand_id")] . "**" . $order_to_sample_data[$row[csf("barcode_no")]]["to_rack"] . "**" . $order_to_sample_data[$row[csf("barcode_no")]]["to_shelf"] . "**" . $order_to_sample_data[$row[csf("barcode_no")]]["prod_id"] . "**" . $row[csf("febric_description_id")] . "**" . $row[csf("gsm")] . "**" . $row[csf("width")] . "**" . number_format($row[csf("qnty")], 2, '.', '') . "**" . $rate . "**1**" . $count . "**" . $order_to_sample_data[$row[csf("barcode_no")]]["to_floor_id"] . "**" . $order_to_sample_data[$row[csf("barcode_no")]]["to_room"] . "**" . $order_to_sample_data[$row[csf("barcode_no")]]["to_bin_box"] . "**" . $brand . "**" . $program_no;

				$barcodeBuyerArr[$row[csf('barcode_no')]] = "1__" . $order_to_sample_data[$row[csf("barcode_no")]]["po_breakdown_id"] . "__" . $is_transfer . "__" . $buyer_id . "__" . $row[csf("qc_pass_qnty_pcs")];
			}
		}

		$all_barcode_no_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}
	unset($data_array);

	$all_barcode_no_arr = array_filter(array_unique($all_barcode_no_arr));

	if (count($all_barcode_no_arr) > 0) {
	}

	if (count($po_ids_arr) > 0) {
		$data_array = sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.po_number, b.id as po_id, c.booking_no, b.grouping as internal_ref_no
			FROM wo_po_details_master a, wo_po_break_down b left join wo_booking_dtls c on b.id = c.po_break_down_id
			WHERE a.job_no=b.job_no_mst and b.id in(" . implode(",", $po_ids_arr) . ")");
		$po_details_array = array();
		foreach ($data_array as $row) {
			$po_details_array[$row[csf("po_id")]]['booking_no'] = $row[csf("booking_no")];
			$po_details_array[$row[csf("po_id")]]['job_no'] = $row[csf("job_no")];
			$po_details_array[$row[csf("po_id")]]['style_no'] = $row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];
			$po_details_array[$row[csf("po_id")]]['internal_ref_no'] = $row[csf("internal_ref_no")];
		}
		unset($data_array);
	}

	if (count($sample_booking_ids_arr) > 0) {
		$sample_data_array = sql_select("SELECT a.id, a.grouping as internal_ref_no FROM wo_non_ord_samp_booking_mst a
		WHERE a.id in(" . implode(",", $sample_booking_ids_arr) . ")");
		$sample_details_array = array();
		foreach ($sample_data_array as $row) {
			$sample_details_array[$row[csf("id")]]['internal_ref_no'] = $row[csf("internal_ref_no")];
		}
		unset($data_array);
	}

	$all_sales_ids = rtrim($all_sales_ids, ", ");
	$transFSO_ids = implode(",", array_filter($transFSO_Arr));
	if ($transFSO_ids != "") {
		$all_sales_ids = $all_sales_ids . "," . $transFSO_ids;
	}


	if ($all_sales_ids != "") {
		$data_array = sql_select("SELECT id as po_id,job_no,job_no_prefix_num, sales_booking_no, buyer_id,within_group,style_ref_no FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and id in(" . $all_sales_ids . ")");
		$sales_arr = array();
		foreach ($data_array as $row) {
			$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
			$sales_arr[$row[csf("po_id")]]['job_no'] = $row[csf("job_no_prefix_num")];
			$sales_arr[$row[csf("po_id")]]['style_no'] = $row[csf("style_ref_no")];
			$sales_arr[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_id")];
			$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
			$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
			$sales_booking_no .= "'" . $row[csf("sales_booking_no")] . "',";
		}
		$all_sales_booking_nos = rtrim($sales_booking_no, ", ");

		unset($data_array);

		$data_array = sql_select("SELECT b.job_no, a.buyer_id, b.booking_no, c.grouping, d.style_ref_no
		from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c, wo_po_details_master d
		where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.booking_no in($all_sales_booking_nos)");
		$po_details_arr = array();
		foreach ($data_array as $row) {
			$po_details_arr[$row[csf("booking_no")]]['job_no'] = $row[csf("job_no")];
			$po_details_arr[$row[csf("booking_no")]]['style_no'] = $row[csf("style_ref_no")];
			$po_details_arr[$row[csf("booking_no")]]['buyer_name'] = $row[csf("buyer_id")];
			$po_details_arr[$row[csf("booking_no")]]['int_ref'] = $row[csf("grouping")];
		}
		unset($data_array);
	}


	if (count($barcodeDataArr) > 0) {
		foreach ($barcodeDataArr as $barcode_no => $value) {
			$barcodeDatas = explode("__", $barcodeBuyerArr[$barcode_no]);
			$booking_without_order = $barcodeDatas[0];
			$is_transfer = $barcodeDatas[2];
			$is_sales = $is_sales_arr[$barcode_no];

			if ($transEntryFormArr[$barcode_no] == 133) {
				$is_transfer = 1;
				//echo $is_transfer."hello<br>";
			}
			if ($is_transfer == 1) {

				if ($sample_to_order_data[$barcode_no]["entry_form"] == 83) {
					$po_id = $order_to_order_data[$barcode_no]["po_breakdown_id"];
				} else {
					$po_id = $transPoIdsArr[$barcode_no];
				}
			} else {
				$po_id = $barcodeDatas[1];
			}
			// echo "10**".$is_transfer.$po_id; die;
			if ($po_cancel_status_arr[$po_id] == 3 && $booking_without_order != 1 && $is_sales != 1) {
				$foundCancelledOrder = "Cancelled Order is not Allowed";
				echo "999!!" . $foundCancelledOrder;
				die;
			}
			if ($booking_without_order == 1) {
				$buyer_id = $barcodeDatas[3];
				$po_no = '';
				$job_no = '';
				$style_no = '';
				//$internal_ref_no='';
				$internal_ref_no = $sample_details_array[$po_id]['internal_ref_no'];
			} else {
				if ($is_sales == 1) {
					$sales_booking = $sales_arr[$po_id]['sales_booking_no'];
					$bookingNumber = $sales_booking;
					$within_group = $sales_arr[$po_id]['within_group'];
					if ($within_group == 1) {
						$po_no = $sales_arr[$po_id]['sales_order'];
						$job_no = $po_details_arr[$sales_booking]['job_no'];
						$style_no = $po_details_arr[$sales_booking]['style_no'];
						$buyer_id = $po_details_arr[$sales_booking]['buyer_name'];
						$internal_ref_no = $po_details_arr[$sales_booking]['int_ref'];
						// $internal_ref_no="";
					} else {
						$po_no = $sales_arr[$po_id]['sales_order'];
						$job_no = $sales_arr[$po_id]['job_no'];
						$style_no = $sales_arr[$po_id]['style_no'];
						$buyer_id = $sales_arr[$po_id]['buyer_name'];
						$internal_ref_no = "";
					}
				} else {
					$bookingNumber = $po_details_array[$po_id]['booking_no'];
					$po_no = $po_details_array[$po_id]['po_number'];
					$job_no = $po_details_array[$po_id]['job_no'];
					$style_no = $po_details_array[$po_id]['style_no'];
					$buyer_id = $po_details_array[$po_id]['buyer_name'];
					$internal_ref_no = $po_details_array[$po_id]['internal_ref_no'];

					if ($receive_basis_arr[$barcode_no]['receive_basis'] == 2) {
						$bookingNumber = $booking_no_plan_basis_arr[$receive_basis_arr[$barcode_no]['plan_id']];
					} else {
						$bookingNumber = $receive_basis_arr[$barcode_no]['plan_id'];
					}
				}
			}

			if ($barcode_store_arr[$barcode_no]["trans"]) {
				$store_id = $barcode_store_arr[$barcode_no]["trans"];
			} else {
				$store_id = $barcode_store_arr[$barcode_no]["rcv"];
			}


			if ($body_part_id_data[$barcode_no]["trans"]) {
				$body_part_id_latest = $body_part_id_data[$barcode_no]["trans"];
			} else {
				$body_part_id_latest = $body_part_id_data[$barcode_no]["rcv"];
			}

			if ($yarn_rate_data[$barcode_no]["yarn_rate"]["trans"]) {
				$yarn_rate = $yarn_rate_data[$barcode_no]["yarn_rate"]["trans"];
			} else {
				$yarn_rate = $yarn_rate_data[$barcode_no]["yarn_rate"]["rcv"];
			}
			if ($knitting_charge_data[$barcode_no]["kniting_charge"]["trans"]) {
				$kniting_charge = $knitting_charge_data[$barcode_no]["kniting_charge"]["trans"];
			} else {
				$kniting_charge = $knitting_charge_data[$barcode_no]["kniting_charge"]["rcv"];
			}
			if ($roll_rate_data[$barcode_no]["rate"]["trans"]) {
				$roll_rate = $roll_rate_data[$barcode_no]["rate"]["trans"];
			} else {
				$roll_rate = $roll_rate_data[$barcode_no]["rate"]["rcv"];
			}


			if ($po_id == '') {
				$po_id = 0;
			}

			if ($order_to_order_data[$barcode_no]["po_breakdown_id"] != "" || $sample_to_order_data[$barcode_no]["po_breakdown_id"] != "" || $order_to_sample_data[$barcode_no]["po_breakdown_id"] != "" || $transEntryFormArr[$barcode_no] != "") {
				$transfer_flag = " (T) ";
			} else {
				$transfer_flag = "";
			}

			$coller_cuff_size = $receive_basis_arr[$barcode_no]['coller_cuff_size'];

			$barcodeData .= $value . "**" . $po_id . "**" . $buyer_id . "**" . $po_no . "**" . $job_no . "**" . $is_sales . "**" . $bookingNumber . $transfer_flag . "**" . $store_id . "**" . $internal_ref_no . "**" . $barcodeDatas[4] . "**" . $coller_cuff_size . "**" . $body_part_id_latest . "**" . $body_part[$body_part_id_latest] . "**" . $yarn_rate . "**" . $kniting_charge . "**" . $roll_rate . "**" . $style_no . "#";
		}
		echo substr($barcodeData, 0, -1);
	} else {
		echo "0";
	}

	exit();
}

if ($action == "populate_barcode_data_update") {
	$po_ids_arr = array();
	$po_details_array = array();
	$barcodeDataArr = array();
	$company_name_array = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name");
	$buyer_name_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$machine_array = return_library_array("select id, machine_no from lib_machine_name where category_id=1", "id", "machine_no");
	$location_array = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

	$floor_room_rack_array = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");

	$composition_arr = array();
	$constructtion_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	$issued_data_arr = $split_from = array();
	$barcode_nos = '';
	$issued_barcode_data = sql_select("SELECT a.id, a.barcode_no, a.dtls_id, a.roll_id, a.rate, a.qnty, a.po_breakdown_id, a.booking_without_order, a.is_sales, b.trans_id, a.roll_split_from, b.store_name, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.qc_pass_qnty_pcs, a.is_returned, b.body_part_id, b.yarn_rate, b.kniting_charge, b.prod_id from pro_roll_details a, inv_grey_fabric_issue_dtls b where a.dtls_id=b.id and a.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$data "); //and is_returned!=1

	$all_po_breake_id == "";
	foreach ($issued_barcode_data as $row) {
		$issued_data_arr[$row[csf('barcode_no')]]['dtls_id'] = $row[csf('dtls_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['trans_id'] = $row[csf('trans_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['po_id'] = $row[csf('po_breakdown_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['booking_without_order'] = $row[csf('booking_without_order')];
		$issued_data_arr[$row[csf('barcode_no')]]['id'] = $row[csf('id')];
		$issued_data_arr[$row[csf('barcode_no')]]['roll_id'] = $row[csf('roll_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['rate'] = $row[csf('rate')];
		$issued_data_arr[$row[csf('barcode_no')]]['yarn_rate'] = $row[csf('yarn_rate')];
		$issued_data_arr[$row[csf('barcode_no')]]['kniting_charge'] = $row[csf('kniting_charge')];
		$issued_data_arr[$row[csf('barcode_no')]]['qnty'] = number_format($row[csf("qnty")], 2, '.', '');
		$issued_data_arr[$row[csf('barcode_no')]]['qty_in_pcs'] = $row[csf("qc_pass_qnty_pcs")] * 1;
		$issued_data_arr[$row[csf('barcode_no')]]['store_name'] = $row[csf("store_name")];
		$issued_data_arr[$row[csf('barcode_no')]]['body_part_id'] = $row[csf("body_part_id")];
		$issued_data_arr[$row[csf('barcode_no')]]['prod_id'] = $row[csf("prod_id")];

		$issued_data_arr[$row[csf('barcode_no')]]['floor_id'] = $row[csf("floor_id")];
		$issued_data_arr[$row[csf('barcode_no')]]['room'] = $row[csf("room")];
		$issued_data_arr[$row[csf('barcode_no')]]['rack'] = $row[csf("rack")];
		$issued_data_arr[$row[csf('barcode_no')]]['self'] = $row[csf("self")];
		$issued_data_arr[$row[csf('barcode_no')]]['bin_box'] = $row[csf("bin_box")];
		$issued_data_arr[$row[csf('barcode_no')]]['is_returned'] = $row[csf("is_returned")];

		$barcode_nos .= $row[csf('barcode_no')] . ',';
		if ($row[csf("booking_without_order")] == 1) {
			$sample_booking_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
		} else {
			if ($row[csf('is_sales')] == 1) {
				$sales_ids_arr[] = $row[csf("po_breakdown_id")];
			} else {
				$po_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			}
		}


		if ($row[csf("roll_split_from")] > 0) {
			$split_from[$row[csf("barcode_no")]] = $row[csf("roll_id")];
		}

		if ($all_po_breake_id == "") {
			$all_po_breake_id .=  $row[csf("po_breakdown_id")];
		} else {
			$all_po_breake_id .=  "," . $row[csf("po_breakdown_id")];
		}
	}

	$barcode_nos = chop($barcode_nos, ',');
	$barcode_nos_arr =  array_filter(explode(",", $barcode_nos));
	if (count($barcode_nos_arr) > 0) {
		$all_barcode_nos = implode(",", $barcode_nos_arr);
		$all_barcode_nos_cond = "";
		$barCond = "";
		if ($db_type == 2 && count($barcode_nos_arr) > 999) {
			$barcode_nos_chunk = array_chunk($barcode_nos_arr, 999);
			foreach ($barcode_nos_chunk as $chunk_arr) {
				$chunk_arr_value = implode(",", $chunk_arr);
				$barCond .= "  c.barcode_no in($chunk_arr_value) or ";
			}
			$all_barcode_nos_cond .= " and (" . chop($barCond, 'or ') . ")";
		} else {
			$all_barcode_nos_cond = " and c.barcode_no in($all_barcode_nos)";
		}
	} else {
		echo "<p style='font-weight:bold;align:center;width:350px'>Data Not Found</p>";
		die;
	}


	$all_sales_ids = implode(",", array_unique($sales_ids_arr));

	$all_po_breake_id = implode(",", array_unique(explode(",", $all_po_breake_id)));

	if ($all_po_breake_id != "") {
		$sqlplan = sql_select("SELECT po_id,dtls_id,booking_no FROM ppl_planning_entry_plan_dtls WHERE po_id in ($all_po_breake_id) AND status_active=1 AND is_deleted=0");

		$planDetails = array();
		foreach ($sqlplan as $row) {
			$planDetails[$row[csf("po_id")]][$row[csf("dtls_id")]]['booking_no'] = $row[csf("booking_no")];
			$planDetails[$row[csf("po_id")]][$row[csf("dtls_id")]]['program_no'] = $row[csf("dtls_id")];
		}
	}

	$order_to_sample_sql = sql_select("SELECT c.barcode_no, c.po_breakdown_id, b.buyer_id, b.id, b.booking_no, d.to_store as store_id from wo_non_ord_samp_booking_mst b, pro_roll_details c, inv_item_transfer_dtls d where b.id=c.po_breakdown_id and c.dtls_id = d.id and c.entry_form in(110,180) and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 $all_barcode_nos_cond "); //and c.barcode_no in($barcode_nos)
	$order_to_sample_data = array();
	foreach ($order_to_sample_sql as $row) {
		$order_to_sample_data[$row[csf("barcode_no")]]["po_breakdown_id"] = $row[csf("po_breakdown_id")];
		$order_to_sample_data[$row[csf("barcode_no")]]["buyer_id"] = $row[csf("buyer_id")];
		$order_to_sample_data[$row[csf("barcode_no")]]["id"] = $row[csf("id")];
		$order_to_sample_data[$row[csf("barcode_no")]]["booking_no"] = $row[csf("booking_no")];
		$barcode_store_arr[$row[csf("barcode_no")]]["trans"] = $row[csf("store_id")];
	}


	$sample_to_order_sql = sql_select("SELECT a.transfer_system_id,a.entry_form, a.company_id,e.job_no, e.buyer_name, e.style_ref_no, e.gmts_item_id, d.po_number, d.po_quantity, d.pub_shipment_date AS shipment_date, c.barcode_no, c.id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.roll_id as roll_id_prev,c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.transfer_criteria,c.is_sales, b.to_store as store_id
	FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e
	WHERE a.id=b.mst_id AND b.id=c.dtls_id AND c.po_breakdown_id = d.id AND c.entry_form in (183,83) AND e.job_no=d.job_no_mst $all_barcode_nos_cond "); //AND c.barcode_no IN($barcode_nos)

	$sample_to_order_data = array();
	foreach ($sample_to_order_sql as $row) {
		if ($row[csf("entry_form")] == 183) {
			$gmts_item_id = explode(",", $row[csf('gmts_item_id')]);
			foreach ($gmts_item_id as $item_id) {
				if ($gmts_item == "") $gmts_item = $garments_item[$item_id];
				else $gmts_item .= "," . $garments_item[$item_id];
			}

			$sample_to_order_data[$row[csf("barcode_no")]]["company_id"] = $row[csf("company_id")];
			$sample_to_order_data[$row[csf("barcode_no")]]["entry_form"] = $row[csf("entry_form")];
			$sample_to_order_data[$row[csf("barcode_no")]]["transfer_id"] = $row[csf("transfer_system_id")];
			$sample_to_order_data[$row[csf("barcode_no")]]["po_breakdown_id"] = $row[csf("po_breakdown_id")];
			$sample_to_order_data[$row[csf("barcode_no")]]["is_sales"] = $row[csf("is_sales")];
			$sample_to_order_data[$row[csf("barcode_no")]]["buyer_id"] = $row[csf("buyer_name")];
			$sample_to_order_data[$row[csf("barcode_no")]]["is_transfer"] = $row[csf("is_transfer")];
			$sample_to_order_data[$row[csf("barcode_no")]]["transfer_criteria"] = $row[csf("transfer_criteria")];
			$sample_to_order_data[$row[csf("barcode_no")]]["roll_id"] = $row[csf("roll_id")];
			$sample_to_order_data[$row[csf("barcode_no")]]["roll_no"] = $row[csf("roll_no")];
			$sample_to_order_data[$row[csf("barcode_no")]]["body_part"] = $row[csf("gmts_item_id")];
		} else {
			$order_to_order_data[$row[csf("barcode_no")]]["po_breakdown_id"] = $row[csf("po_breakdown_id")];
		}
		$barcode_store_arr[$row[csf("barcode_no")]]["trans"] = $row[csf("store_id")];
	}

	$sales_ord_to_ord_sql = sql_select("SELECT a.transfer_system_id, c.barcode_no, c.id, c.po_breakdown_id, b.to_store as store_id, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.to_body_part,c.entry_form
	FROM  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, fabric_sales_order_mst d
	WHERE  b.id=c.dtls_id and c.po_breakdown_id = d.id AND a.id=b.mst_id  and c.entry_form in (133) and c.re_transfer=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $all_barcode_nos_cond ");

	$sales_order_to_order_data = array();

	foreach ($sales_ord_to_ord_sql as $row) {
		$sales_order_to_order_data[$row[csf("barcode_no")]]["entry_form"] = $row[csf("entry_form")];
		$sales_order_to_order_data[$row[csf("barcode_no")]]["transfer_id"] = $row[csf("transfer_system_id")];

		$barcode_store_arr[$row[csf("barcode_no")]]["trans"] = $row[csf("store_id")];
	}



	$without_order_buyer = return_library_array("SELECT c.barcode_no, a.buyer_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and a.booking_without_order=1 and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 $all_barcode_nos_cond ", "barcode_no", "buyer_id"); //and c.barcode_no in($barcode_nos)

	$data_array_receive_basis = sql_select("SELECT a.id, a.company_id, a.recv_number, a.booking_no, a.receive_basis, a.receive_date, a.booking_no,a.knitting_source, a.knitting_company, c.roll_no, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty, c.coller_cuff_size
		from inv_receive_master a, pro_roll_details c
		where a.id=c.mst_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and a.receive_basis in(1,2,4) and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $all_barcode_nos_cond "); //and c.barcode_no in($barcode_nos)
	foreach ($data_array_receive_basis as $row) {
		$receive_basis_arr[$row[csf('barcode_no')]]['plan_id'] = $row[csf('booking_no')];
		$receive_basis_arr[$row[csf('barcode_no')]]['coller_cuff_size'] = $row[csf('coller_cuff_size')];
		$receive_basis_arr[$row[csf('barcode_no')]]['receive_basis'] = $row[csf('receive_basis')];
		if ($row[csf('receive_basis')] == 2) {
			$program_no_plan_basis_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}
	}

	if (count($program_no_plan_basis_arr) > 0) {
		$all_program_id = implode(",", $program_no_plan_basis_arr);
		$program_id_cond = "";
		$progIds_cond = "";
		if ($db_type == 2 && count($program_no_plan_basis_arr) > 999) {
			$program_no_plan_basis_chunk = array_chunk($program_no_plan_basis_arr, 999);
			foreach ($program_no_plan_basis_chunk as $chunk_arr) {
				$chunk_arr_value = implode(",", $chunk_arr);
				$progIds_cond .= "  a.id in($chunk_arr_value) or ";
			}

			$program_id_cond .= " and (" . chop($bokIds_cond, 'or ') . ")";
			//echo $booking_id_cond;die;
		} else {
			$program_id_cond = " and a.id in($all_program_id)";
		}

		$booking_no_planbasis_sql = sql_select("select a.id,b.booking_no from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id = b.id and a.status_active=1 and a.is_deleted=0 $program_id_cond");
		foreach ($booking_no_planbasis_sql as  $val) {
			$booking_no_plan_basis_arr[$val[csf("id")]] =  $val[csf("booking_no")];
		}
	}

	$data_array = sql_select("SELECT a.id, a.entry_form, c.booking_no as program_no, a.company_id, a.receive_basis, a.location_id, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.machine_no_id, b.brand_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.po_breakdown_id, c.barcode_no, c.roll_no, c.booking_no as bwo, c.booking_without_order, c.is_sales,a.store_id
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0  $all_barcode_nos_cond"); //and c.barcode_no in($barcode_nos)

	foreach ($data_array as $row) {
		$is_salesOrder = $row[csf("is_sales")];
		if (($row[csf("entry_form")] == 2 && $row[csf("receive_basis")] == 0) || ($row[csf("entry_form")] == 22 && ($row[csf("receive_basis")] == 4 || $row[csf("receive_basis")] == 6))) {
			$receive_basis = "Independent";
			$receive_basis_id = 0;
		} else if (($row[csf("entry_form")] == 2 && $row[csf("receive_basis")] == 1) || ($row[csf("entry_form")] == 22 && $row[csf("receive_basis")] == 2)) {
			$receive_basis = "Booking";
			$receive_basis_id = 2;
		} else if ($row[csf("entry_form")] == 2 && $row[csf("receive_basis")] == 2) {
			$receive_basis = "Knitting Plan";
			$receive_basis_id = 3;
		} else if ($row[csf("entry_form")] == 22 && $row[csf("receive_basis")] == 1) {
			$receive_basis = "PI";
			$receive_basis_id = 1;
		} else if ($row[csf("entry_form")] == 58) {
			$receive_basis = "Delivery";
			$receive_basis_id = 9;
		}

		if ($row[csf("knitting_source")] == 1) {
			$knit_company = $company_name_array[$row[csf("knitting_company")]];
		} else if ($row[csf("knitting_source")] == 3) {
			$knit_company = $supplier_arr[$row[csf("knitting_company")]];
		}

		$color = '';
		$color_id = array_unique(explode(",", $row[csf('color_id')]));
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');
		$plan_id = $receive_basis_arr[$row[csf('barcode_no')]]['plan_id'];
		$receive_basis = $receive_basis_arr[$row[csf('barcode_no')]]['receive_basis'];
		$coller_cuff_size = $receive_basis_arr[$row[csf('barcode_no')]]['coller_cuff_size'];

		$barcode_store_arr[$row[csf("barcode_no")]]["rcv"] = $row[csf("store_id")];

		if ($sample_to_order_data[$row[csf('barcode_no')]]["entry_form"] == 183) {
			$receive_basis = 10;
			$receive_basis_id = 9;

			$is_transfer = 0;

			if ($sample_to_order_data[$row[csf('barcode_no')]]["is_transfer"] == 6 && $sample_to_order_data[$row[csf('barcode_no')]]["transfer_criteria"] == 7) {
				$transRollIds .= $sample_to_order_data[$row[csf('barcode_no')]]["roll_id"] . ",";
				$is_transfer = 1;
			}

			$barcodeDataArr[$row[csf('barcode_no')]] = $row[csf("roll_no")] . "**" . $row[csf("location_id")] . "**" . $row[csf("machine_no_id")] . "**" . $gmts_item . "**" . $order_to_sample_data[$row[csf("barcode_no")]]["booking_no"] . "**" . $receive_basis . "**" . $receive_basis_id . "**" . $sample_to_order_data[$row[csf("barcode_no")]]["transfer_id"] . "**" . $order_to_sample_data[$row[csf("barcode_no")]]["id"] . "**" . $color . "**" . $row[csf("color_id")] . "**" . $row[csf("knitting_source")] . "**" . $knitting_source[$row[csf("knitting_source")]] . "**" . $row[csf("knitting_company")] . "**" . $knit_company . "**" . $row[csf("yarn_lot")] . "**" . $row[csf('yarn_count')] . "**" . $row[csf("stitch_length")] . "**" . $row[csf("brand_id")] . "**" . $row[csf("rack")] . "**" . $row[csf("self")] . "**" . $row[csf("prod_id")] . "**" . $row[csf("febric_description_id")] . "**" . $row[csf("gsm")] . "**" . $row[csf("width")] . "**0**0**" . $row[csf("is_sales")] . "**" . $row[csf("floor_id")] . "**" . $row[csf("room")] . "**" . $row[csf("bin_box")] . "**" . $row[csf("program_no")];

			$barcodeBuyerArr[$row[csf('barcode_no')]] = "0__" . $sample_to_order_data[$row[csf("barcode_no")]]["po_breakdown_id"] . "__" . $is_transfer . "__" . $sample_to_order_data[$row[csf("barcode_no")]]["buyer_id"];
		} else {

			if ($order_to_sample_data[$row[csf('barcode_no')]]["po_breakdown_id"] == "") {
				if ($sales_order_to_order_data[$row[csf('barcode_no')]]["entry_form"] == 133) // sales Order To sales Order Transfer
				{
					$booking_no = $sales_order_to_order_data[$row[csf("barcode_no")]]["transfer_id"];
				} else {
					$booking_no = $row[csf("booking_no")];
				}

				$barcodeDataArr[$row[csf('barcode_no')]] = $row[csf("roll_no")] . "**" . $row[csf("location_id")] . "**" . $row[csf("machine_no_id")] . "**" . $body_part[$row[csf("body_part_id")]] . "**" . $row[csf("bwo")] . "**" . $receive_basis . "**" . $receive_basis_id . "**" . $booking_no . "**" . $row[csf("booking_id")] . "**" . $color . "**" . $row[csf("color_id")] . "**" . $row[csf("knitting_source")] . "**" . $knitting_source[$row[csf("knitting_source")]] . "**" . $row[csf("knitting_company")] . "**" . $knit_company . "**" . $row[csf("yarn_lot")] . "**" . $row[csf('yarn_count')] . "**" . $row[csf("stitch_length")] . "**" . $row[csf("brand_id")] . "**" . $row[csf("rack")] . "**" . $row[csf("self")] . "**" . $row[csf("prod_id")] . "**" . $row[csf("febric_description_id")] . "**" . $row[csf("gsm")] . "**" . $row[csf("width")] . "**" . $row[csf("booking_without_order")] . "**0**" . $row[csf("is_sales")] . "**" . $row[csf("floor_id")] . "**" . $row[csf("room")] . "**" . $row[csf("bin_box")] . "**" . $row[csf("program_no")];
			} else {
				$barcodeDataArr[$row[csf('barcode_no')]] = $row[csf("roll_no")] . "**" . $row[csf("location_id")] . "**" . $row[csf("machine_no_id")] . "**" . $body_part[$row[csf("body_part_id")]] . "**" . $order_to_sample_data[$row[csf("barcode_no")]]["booking_no"] . "**" . $receive_basis . "**" . $receive_basis_id . "**" . $order_to_sample_data[$row[csf("barcode_no")]]["booking_no"] . "**" . $order_to_sample_data[$row[csf("barcode_no")]]["id"] . "**" . $color . "**" . $row[csf("color_id")] . "**" . $row[csf("knitting_source")] . "**" . $knitting_source[$row[csf("knitting_source")]] . "**" . $row[csf("knitting_company")] . "**" . $knit_company . "**" . $row[csf("yarn_lot")] . "**" . $row[csf('yarn_count')] . "**" . $row[csf("stitch_length")] . "**" . $row[csf("brand_id")] . "**" . $row[csf("rack")] . "**" . $row[csf("self")] . "**" . $row[csf("prod_id")] . "**" . $row[csf("febric_description_id")] . "**" . $row[csf("gsm")] . "**" . $row[csf("width")] . "**1**1**" . $row[csf("is_sales")] . "**" . $row[csf("floor_id")] . "**" . $row[csf("room")] . "**" . $row[csf("bin_box")] . "**" . $row[csf("program_no")];
			}
		}
	}

	// ADJUST SPLITTED ROLL
	$split_from = array_filter($split_from);
	if (count($split_from) > 0) {
		$splited_barcode_parent = return_library_array("select id,barcode_no from pro_roll_details where id in(" . implode(",", $split_from) . ")", "id", "barcode_no");
		foreach ($split_from as $key => $split_barcode) {
			if ($barcodeDataArr[$splited_barcode_parent[$split_barcode]] != "") {
				$barcodeDataArr[$key] = $barcodeDataArr[$splited_barcode_parent[$split_barcode]];
			}
		}
	}

	if ($sample_to_order_data[$barcode_nos]["entry_form"] == 183) {
		$transRollIds = chop($transRollIds, ',');
		if ($transRollIds != "") {
			$transPoIds = sql_select("select barcode_no, po_breakdown_id, entry_form from pro_roll_details where entry_form in(83,133,183) and status_active=1 and is_deleted=0 and roll_id in($transRollIds) and re_transfer=0");
			foreach ($transPoIds as $rowP) {
				$transPoIdsArr[$rowP[csf("barcode_no")]] = $rowP[csf("po_breakdown_id")];
				$po_ids_arr[$rowP[csf("po_breakdown_id")]] = $rowP[csf("po_breakdown_id")];
			}
		}
	}

	if (count($po_ids_arr) > 0) {
		$data_array = sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.po_number, b.id as po_id,c.booking_no, b.grouping as internal_ref_no FROM wo_po_details_master a, wo_po_break_down b left join wo_booking_dtls c  on b.id=c.po_break_down_id  and c.booking_type=1 WHERE a.job_no=b.job_no_mst and b.id in(" . implode(",", $po_ids_arr) . ")");

		$po_details_array = array();
		foreach ($data_array as $row) {
			$po_details_array[$row[csf("po_id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['booking_no'] = $row[csf("booking_no")];
			$po_details_array[$row[csf("po_id")]]['job_no'] = $row[csf("job_no")];
			$po_details_array[$row[csf("po_id")]]['style_no'] = $row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];
			$po_details_array[$row[csf("po_id")]]['internal_ref_no'] = $row[csf("internal_ref_no")];
		}
	}

	if (count($sample_booking_ids_arr) > 0) {
		$sample_data_array = sql_select("SELECT a.id, a.grouping as internal_ref_no FROM wo_non_ord_samp_booking_mst a
		WHERE a.id in(" . implode(",", $sample_booking_ids_arr) . ")");
		$sample_details_array = array();
		foreach ($sample_data_array as $row) {
			$sample_details_array[$row[csf("id")]]['internal_ref_no'] = $row[csf("internal_ref_no")];
		}
		unset($sample_data_array);
	}

	if ($all_sales_ids != "") {
		$data_array = sql_select("SELECT id as po_id,job_no,job_no_prefix_num, sales_booking_no, buyer_id,within_group, style_ref_no FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and id in($all_sales_ids)");
		$sales_arr = array();
		foreach ($data_array as $row) {
			$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
			$sales_arr[$row[csf("po_id")]]['job_no_prefix_num'] = $row[csf("job_no_prefix_num")];
			$sales_arr[$row[csf("po_id")]]['style_no'] = $row[csf("style_ref_no")];
			$sales_arr[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_id")];
			$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
			$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
			$sales_booking_no .= "'" . $row[csf("sales_booking_no")] . "',";
		}
		$all_sales_booking_nos = rtrim($sales_booking_no, ", ");
		$data_array = sql_select("SELECT b.job_no,a.buyer_id,b.booking_no, c.grouping, d.style_ref_no
		from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c, wo_po_details_master d
		where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.booking_no in($all_sales_booking_nos) group by b.job_no,a.buyer_id,b.booking_no, c.grouping, d.style_ref_no");
		$po_details_arr = array();
		foreach ($data_array as $row) {
			$po_details_arr[$row[csf("booking_no")]]['job_no'] = $row[csf("job_no")];
			$po_details_arr[$row[csf("booking_no")]]['style_no'] = $row[csf("style_ref_no")];
			$po_details_arr[$row[csf("booking_no")]]['buyer_name'] = $row[csf("buyer_id")];
			$po_details_arr[$row[csf("booking_no")]]['int_ref'] = $row[csf("grouping")];
		}
	}

	$i = count($issued_barcode_data);
	foreach ($barcodeDataArr as $barcode_no => $value) {
		$barcodeDatas = explode("**", $value);
		$roll_no = $barcodeDatas[0];
		$location_id = $barcodeDatas[1];
		$machine_no_id = $barcodeDatas[2];
		$body_part_name = $barcodeDatas[3];
		$bwo = $barcodeDatas[4];
		$receive_basis = $barcodeDatas[5];
		$receive_basis_id = $barcodeDatas[6];
		$booking_no = $barcodeDatas[7];
		$booking_id = $barcodeDatas[8];
		$color = $barcodeDatas[9];
		$color_id = $barcodeDatas[10];
		$knitting_source_id = $barcodeDatas[11];
		$knitting_source_name = $barcodeDatas[12];
		$knitting_company_id = $barcodeDatas[13];
		$knit_company = $barcodeDatas[14];
		$yarn_lot = $barcodeDatas[15];
		$yarn_count = $barcodeDatas[16];
		$stitch_length = $barcodeDatas[17];
		$brand_id = $barcodeDatas[18];
		//$rack_id=$barcodeDatas[19];
		//$self_id=$barcodeDatas[20];
		//$prod_id=$barcodeDatas[21];
		$febric_description_id = $barcodeDatas[22];
		$gsm = $barcodeDatas[23];
		$width = $barcodeDatas[24];
		//$booking_without_order=$barcodeDatas[25];
		$sample_without_order = $barcodeDatas[26];
		$is_salesOrder = $barcodeDatas[27];

		//$floor_id=$barcodeDatas[28];
		//$room_id=$barcodeDatas[29];
		//$bin_id=$barcodeDatas[30];
		$program_no = $barcodeDatas[31];

		$cons_comp = $constructtion_arr[$febric_description_id] . ", " . $composition_arr[$febric_description_id];

		$dtls_id = $issued_data_arr[$barcode_no]['dtls_id'];
		$trans_id = $issued_data_arr[$barcode_no]['trans_id'];
		$po_id = $issued_data_arr[$barcode_no]['po_id'];
		$roll_table_id = $issued_data_arr[$barcode_no]['id'];
		$roll_id = $issued_data_arr[$barcode_no]['roll_id'];
		$rate = $issued_data_arr[$barcode_no]['rate'];
		$qnty = $issued_data_arr[$barcode_no]['qnty'];
		$qty_in_pcs = $issued_data_arr[$barcode_no]['qty_in_pcs'];
		$coller_cuff_size = $receive_basis_arr[$barcode_no]['coller_cuff_size'];

		$floor_id = $issued_data_arr[$barcode_no]['floor_id'];
		$room_id = $issued_data_arr[$barcode_no]['room'];
		$rack_id = $issued_data_arr[$barcode_no]['rack'];
		$self_id = $issued_data_arr[$barcode_no]['self'];
		$bin_id = $issued_data_arr[$barcode_no]['bin_box'];
		$is_returned = $issued_data_arr[$barcode_no]['is_returned'];
		$prod_id = $issued_data_arr[$barcode_no]['prod_id'];
		$booking_without_order = $issued_data_arr[$barcode_no]['booking_without_order'];

		if ($is_returned == 1) {
			$bgcolor = "background-color: #ffa490";
			$add_css = "display: none";
			$title = "Returned Barcode";
		} else {
			$bgcolor = "";
			$add_css = "";
		}

		if ($booking_without_order == 1) {
			if ($sample_without_order == 1) {
				$buyer_id = $order_to_sample_data[$barcode_no]["buyer_id"];
				$buyer_name = $buyer_name_array[$order_to_sample_data[$barcode_no]["buyer_id"]];
			} else {
				$buyer_id = $without_order_buyer[$barcode_no];
				$buyer_name = $buyer_name_array[$without_order_buyer[$barcode_no]];
			}

			$job_no = '';
			$style_no = '';
			//$internal_ref_no="";
			$internal_ref_no = $sample_details_array[$po_id]['internal_ref_no'];
			if ($is_salesOrder == 1) {
				$po_no = $sales_arr[$po_id]['sales_order']; //$po_no=$sales_order_no;
			} else {
				$po_no = '';
			}
		} else {
			if ($is_salesOrder == 1) {
				$sales_booking = $sales_arr[$po_id]['sales_booking_no'];
				$within_group = $sales_arr[$po_id]['within_group'];
				if ($within_group == 1) {
					$po_no = $sales_arr[$po_id]['sales_order'];
					$job_no = $po_details_arr[$sales_booking]['job_no'];
					$style_no = $po_details_arr[$sales_booking]['style_no'];
					$buyer_name = $buyer_name_array[$po_details_arr[$sales_booking]['buyer_name']];
					$internal_ref_no = $po_details_arr[$sales_booking]['int_ref'];
				} else {
					$po_no = $sales_arr[$po_id]['sales_order'];
					$job_no = "";
					$style_no = $sales_arr[$po_id]['style_no'];
					$buyer_name = $buyer_name_array[$sales_arr[$po_id]['buyer_name']];
					$internal_ref_no = "";
				}
			} else {
				$buyer_id = $po_details_array[$po_id]['buyer_name'];
				$buyer_name = $buyer_name_array[$po_details_array[$po_id]['buyer_name']];
				$po_no = $po_details_array[$po_id]['po_number'];
				$job_no = $po_details_array[$po_id]['job_no'];
				$style_no = $po_details_array[$po_id]['style_no'];
				$internal_ref_no = $po_details_array[$po_id]['internal_ref_no'];
			}
		}

		$bookingNumber = $planDetails[$po_id][$bwo]['booking_no'];
		$programNo = $planDetails[$po_id][$bwo]['program_no'];

		if ($planDetails[$po_id][$bwo]['booking_no'] == "") {
			$bookingNumber = $bwo;
		}

		if ($receive_basis_arr[$barcode_no]['receive_basis'] == 2) {
			$bookingNumber = $booking_no_plan_basis_arr[$receive_basis_arr[$barcode_no]['plan_id']];
			$programNo = $receive_basis_arr[$barcode_no]['plan_id'];
		} else if ($receive_basis_arr[$barcode_no]['receive_basis'] == 1) {
			$bookingNumber = $receive_basis_arr[$barcode_no]['plan_id'];
		}


		if ($order_to_order_data[$barcode_no]["po_breakdown_id"] != "" || $sample_to_order_data[$barcode_no]["po_breakdown_id"] != "" || $order_to_sample_data[$barcode_no]["po_breakdown_id"] != "") {
			$transfer_flag = " (T) ";
		} else {
			$transfer_flag = "";
		}

		if ($is_salesOrder == 1) {
			$bookingNumber = $sales_booking;
		}

		/*if($barcode_store_arr[$barcode_no]["trans"])
		{
			$store_id = $barcode_store_arr[$barcode_no]["trans"];
		}
		else
		{
			$store_id = $barcode_store_arr[$barcode_no]["rcv"];
		}*/
		$store_id = $issued_data_arr[$barcode_no]['store_name'];
		$body_part_id = $issued_data_arr[$barcode_no]['body_part_id'];
		$yarn_rate = $issued_data_arr[$barcode_no]['yarn_rate'];
		$kniting_charge = $issued_data_arr[$barcode_no]['kniting_charge'];
	?>
		<tr id="tr_<? echo $i; ?>" align="center" valign="middle" title="<? echo $title; ?>">
			<td width="30" id="sl_<? echo $i; ?>" style="<? echo $bgcolor; ?>"><? echo $i; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="60" id="buyer_<? echo $i; ?>"><? echo $buyer_name; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="70" id="style_<? echo $i; ?>"><? echo $style_no; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="70" id="job_<? echo $i; ?>"><? echo $job_no; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="70" id="order_<? echo $i; ?>" align="left"><? echo $po_no; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="60" id="bookingNo_<? echo $i; ?>">
				<? echo $bookingNumber . $transfer_flag; ?>
			</td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="70" id="basis_<? echo $i; ?>"><? echo $receive_basis_arr[10]; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="90" id="knitCompany_<? echo $i; ?>"><? echo $knit_company; ?></td>
			<td width="70" id="location_<? echo $i; ?>" style="<? echo $bgcolor; ?>"><? echo $location_array[$location_id]; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="100" id="internalRefNo_<? echo $i; ?>"><? echo $internal_ref_no; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="100" id="progBookPiNo_<? echo $i; ?>"><? echo $booking_no; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="60" id="machine_<? echo $i; ?>"><? echo $machine_array[$machine_no_id]; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="70" id="bodyPart_<? echo $i; ?>"><? echo $body_part[$body_part_id]; ?></td>
			<td width="50" id="prodId_<? echo $i; ?>" style="<? echo $bgcolor; ?>"><? echo $prod_id; ?></td>
			<td width="60" align="right" id="qtyInPcs_<? echo $i; ?>" style="<? echo $bgcolor; ?>"><? echo $qty_in_pcs; ?></td>
			<td width="60" align="right" id="collarCuffSize_<? echo $i; ?>" style="<? echo $bgcolor; ?>"><? echo $coller_cuff_size; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="70" id="count_<? echo $i; ?>">
				<?
				$Ycount = '';
				$yarn_count_arr = explode(",", $yarn_count);
				foreach ($yarn_count_arr as $count_id) {
					if ($Ycount == '') $Ycount = $yarn_count_details[$count_id];
					else $Ycount .= "," . $yarn_count_details[$count_id];
				}
				echo $Ycount;
				?>
			</td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="70" id="brand_<? echo $i; ?>">
				<?
				$Ybrand = '';
				$brand_id_arr = explode(",", $brand_id);
				foreach ($brand_id_arr as $brand_id) {
					if ($Ybrand == '') $Ybrand = $brand_arr[$brand_id];
					else $Ybrand .= "," . $brand_arr[$brand_id];
				}
				echo $Ybrand;
				?>
			</td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="70" id="lot_<? echo $i; ?>"><? echo $yarn_lot; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="100" id="cons_<? echo $i; ?>" align="left"><? echo $cons_comp; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="50" id="stL_<? echo $i; ?>"><? echo $stitch_length; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="50" id="dia_<? echo $i; ?>"><? echo $width; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="50" id="gsm_<? echo $i; ?>"><? echo $gsm; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="70" id="program_<? echo $i; ?>"><? echo $programNo; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor; ?>" width="70" id="color_<? echo $i; ?>"><? echo $color; ?></td>
			<td width="50" id="floor_<? echo $i; ?>" style="<? echo $bgcolor; ?>"><? echo $floor_room_rack_array[$floor_id]; ?></td>
			<td width="50" id="room_<? echo $i; ?>" style="<? echo $bgcolor; ?>"><? echo $floor_room_rack_array[$room_id]; ?></td>
			<td width="50" id="rack_<? echo $i; ?>" style="<? echo $bgcolor; ?>"><? echo $floor_room_rack_array[$rack_id]; ?></td>
			<td width="50" id="shelf_<? echo $i; ?>" style="<? echo $bgcolor; ?>"><? echo $floor_room_rack_array[$self_id]; ?></td>
			<td width="50" id="bin_<? echo $i; ?>" style="<? echo $bgcolor; ?>"><? echo $floor_room_rack_array[$bin_id]; ?></td>
			<td width="50" id="roll_<? echo $i; ?>" style="<? echo $bgcolor; ?>"><? echo $roll_no; ?></td>
			<td width="70" id="barcode_<? echo $i; ?>" style="<? echo $bgcolor; ?>"><? echo $barcode_no; ?></td>
			<td width="60" align="right" id="rollWeight_<? echo $i; ?>" style="<? echo $bgcolor; ?>"><? echo $qnty; ?></td>
			<td id="button_<? echo $i; ?>" align="center" style="<? echo $bgcolor; ?>">
				<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px;<? echo $add_css; ?>" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
				<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $barcode_no; ?>" />
				<input type="hidden" name="recvBasis[]" id="recvBasis_<? echo $i; ?>" value="<? echo $receive_basis_id; ?>" />
				<input type="hidden" name="progBookPiId[]" id="progBookPiId_<? echo $i; ?>" value="<? echo $booking_id; ?>" />
				<input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $prod_id; ?>" />
				<input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $po_id; ?>" />
				<input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $roll_id; ?>" />
				<input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<? echo $qnty; ?>" />
				<input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_<? echo $i; ?>" value="<? echo $qty_in_pcs; ?>" />
				<input type="hidden" name="yarnLot[]" id="yarnLot_<? echo $i; ?>" value="<? echo $yarn_lot; ?>" />
				<input type="hidden" name="yarnCount[]" id="yarnCount_<? echo $i; ?>" value="<? echo $yarn_count; ?>" />
				<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $color_id; ?>" />
				<input type="hidden" name="stichLn[]" id="stichLn_<? echo $i; ?>" value="<? echo $stitch_length; ?>" />
				<input type="hidden" name="locationId[]" id="locationId_<? echo $i; ?>" value="<? echo $location_id; ?>" />
				<input type="hidden" name="machineId[]" id="machineId_<? echo $i; ?>" value="<? echo $machine_no_id; ?>" />
				<input type="hidden" name="brandId[]" id="brandId_<? echo $i; ?>" value="<? echo $brand_id; ?>" />
				<input type="hidden" name="floorId[]" id="floorId_<? echo $i; ?>" value="<? echo $floor_id; ?>" />
				<input type="hidden" name="roomId[]" id="roomId_<? echo $i; ?>" value="<? echo $room_id; ?>" />
				<input type="hidden" name="rackId[]" id="rackId_<? echo $i; ?>" value="<? echo $rack_id; ?>" />
				<input type="hidden" name="shelfId[]" id="shelfId_<? echo $i; ?>" value="<? echo $self_id; ?>" />
				<input type="hidden" name="binId[]" id="binId_<? echo $i; ?>" value="<? echo $bin_id; ?>" />
				<input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $dtls_id; ?>" />
				<input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $trans_id; ?>" />
				<input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value="<? echo $roll_table_id; ?>" />
				<input type="hidden" name="rollRate[]" id="rollRate_<? echo $i; ?>" value="<? echo $rate; ?>" />
				<input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_<? echo $i; ?>" value="<? echo $booking_without_order; ?>" />
				<input type="hidden" name="smnBooking[]" id="smnBooking_<? echo $i; ?>" value="<? echo $bwo; ?>" />
				<input type="hidden" name="isSalesOrder[]" id="isSalesOrder_<? echo $i; ?>" value="<? echo $is_salesOrder; ?>" />
				<input type="hidden" name="storeId[]" id="storeId_<? echo $i; ?>" value="<? echo $store_id; ?>" />
				<input type="hidden" name="isReturned[]" id="isReturned_<? echo $i; ?>" value="<? echo $is_returned; ?>" />
				<input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $i; ?>" value="<? echo $body_part_id; ?>" />
				<input type="hidden" name="yarnRate[]" id="yarnRate_<? echo $i; ?>" value="<? echo $yarn_rate; ?>" />
				<input type="hidden" name="knittingCharge[]" id="knittingCharge_<? echo $i; ?>" value="<? echo $kniting_charge; ?>" />
			</td>
		</tr>
	<?
		$i--;
	}
	exit();
}

if ($action == "grey_issue_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$lib_store_name = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$floorRoomRackShelf_array = return_library_array("SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");

	//$booking_arr=return_library_array( "select id, booking_no from wo_booking_mst where item_category in(2,13)", "id", "booking_no");
	$smn_booking_buyer_arr = return_library_array("select booking_no, buyer_id from wo_non_ord_samp_booking_mst", "booking_no", "buyer_id");
	$smn_booking_buyer_arr2 = return_library_array("select booking_no, style_des from wo_non_ord_samp_booking_dtls where status_active=1 and is_deleted=0", "booking_no", "style_des");

	$smn_booking_style_arr = return_library_array("select booking_no, style_ref_no from wo_non_ord_samp_booking_dtls a, sample_development_mst b where a.style_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by booking_no, style_ref_no", "booking_no", "style_ref_no");
	// print_r($smn_booking_style_arr);
	//$pi_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");

	$production_arr = array();
	$production_del_sql = sql_select("select id, sys_number_prefix_num, sys_number, knitting_source, knitting_company, location_id from pro_grey_prod_delivery_mst where entry_form=56 and company_id=$company");
	foreach ($production_del_sql as $row) {
		$production_arr[$row[csf('id')]]['sys'] = $row[csf('sys_number')];
		$production_arr[$row[csf('id')]]['sys_num'] = $row[csf('sys_number_prefix_num')];
		$production_arr[$row[csf('id')]]['unit'] = $row[csf('location_id')];
		$production_arr[$row[csf('id')]]['knit_sou'] = $row[csf('knitting_source')];
		$production_arr[$row[csf('id')]]['knit_com'] = $row[csf('knitting_company')];
	}

	//$mc_id_arr=return_library_array( "select b.id, a.machine_no_id from	pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=2", "id", "machine_no_id");

	$lib_mc_arr = array();
	$mc_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($mc_sql as $row) {
		$lib_mc_arr[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
	}


	$dataArray = sql_select("select issue_purpose,issue_date,knit_dye_source,knit_dye_company,batch_no,remarks from inv_issue_master where id=$update_id");

	$product_array = array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach ($product_sql as $row) {
		$product_array[$row[csf("id")]]['gsm'] = $row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width'] = $row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id'] = $row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom'] = $row[csf("unit_of_measure")];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$grey_issue_basis = array(0 => "Independent", 1 => "PI", 2 => "Booking", 3 => "Knitting Plan", 9 => "Delivery");
	?>
	<div>
		<table width="1030" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[3]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:18px; font-weight:bold;"><strong>Challan No :</strong></td>
				<td width="175" style="font-size:18px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
				<td width="130"><strong>Dyeing Company:</strong> </td>
				<td style="font-size:18px; font-weight:bold;">
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
				<td width="125"><strong>Issue Date:</strong></td>
				<td width="175"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td>
				<td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				<td><strong>Issue Purpose:</strong></td>
				<td><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
				<td><strong>Batch Number:</strong></td>
				<td><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>

			</tr>
			<tr>
				<td><strong>Remarks:</strong></td>
				<td><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			<tr>
				<td colspan="3" id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1230" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center" style="font-size:12px">
				<th width="20">SL</th>
				<th width="70">File /Ref./ Order/Booking</th>
				<th width="80">Job/ Buyer. /Style</th>
				<th width="50">Basis</th>
				<th width="70">Prog/Book/ D.Challan</th>
				<th width="135">Item Description</th>
				<th width="65">Barcode</th>
				<th width="50">Stich Length</th>
				<th width="50">GSM/ Fin. Dia</th>
				<th width="60">MC No / Dia X Gauge</th>
				<th width="60">Color</th>
				<th width="35">Brand /UOM</th>
				<th width="50">Count /Y. Lot</th>
				<th width="55">K. Party</th>
				<th width="100">Store Name</th>
				<th width="30">Rack/ Shelf</th>
				<th width="100">Program Batch No</th>
				<th width="35">Roll No</th>
				<th>Issue Qty</th>
			</thead>
			<?

			//$roll_arr=return_library_array( "select id, roll_no from pro_roll_details where entry_form in(2,22)", "id", "roll_no");
			$i = 1;
			$tot_qty = 0;
			$x = 0;
			//$sql = "select a.basis, a.program_no, a.prod_id, a.issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, b.roll_no, b.barcode_no, b.po_breakdown_id, c.quantity from inv_grey_fabric_issue_dtls a, pro_roll_details b, order_wise_pro_details c where a.id=b.dtls_id and a.id=c.dtls_id and a.mst_id=$update_id and b.entry_form=61 and c.entry_form=61 and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			$sql = "SELECT a.basis, a.program_no, a.prod_id, a.issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id,a.store_name, b.roll_id, b.roll_no, b.barcode_no, b.po_breakdown_id, b.qnty as quantity, b.booking_no, b.booking_without_order,b.is_sales
			from inv_grey_fabric_issue_dtls a, pro_roll_details b
			where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61  and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			// echo $sql;
			$result = sql_select($sql);

			/*###########  according to saeed vai decission booking no show from booking table againest order   ###############*/
			$all_order_id = $all_program_no = $all_barcode_no = "";
			foreach ($result as $row) {
				if ($row[csf("is_sales")] == 1) {
					$all_fso_id .= $row[csf("po_breakdown_id")] . ",";
				} else {
					$all_order_id .= $row[csf("po_breakdown_id")] . ",";
				}

				$all_barcode_no .= $row[csf("barcode_no")] . ",";
			}
			$all_order_id = implode(",", array_unique(explode(",", chop($all_order_id, ","))));
			$all_fso_id = implode(",", array_unique(explode(",", chop($all_fso_id, ","))));
			$all_barcode_no = implode(",", array_unique(explode(",", chop($all_barcode_no, ","))));

			$job_array = array();
			if ($all_order_id != "") {
				$job_sql = "select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_order_id)";
				$job_sql_result = sql_select($job_sql);
				foreach ($job_sql_result as $row) {
					$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
					$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
					$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
					$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
					$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
					$job_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				}
			}

			if ($all_fso_id != "") {
				$data_array = sql_select("SELECT id as po_id,job_no, sales_booking_no, po_buyer,within_group,style_ref_no,po_job_no FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and id in($all_fso_id)");
				$sales_arr = array();
				foreach ($data_array as $row) {
					$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
					$sales_arr[$row[csf("po_id")]]['buyer'] = $row[csf("po_buyer")];
					$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
					$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
					$sales_arr[$row[csf("po_id")]]['style_ref'] = $row[csf("style_ref_no")];
					$sales_arr[$row[csf("po_id")]]['job'] = $row[csf("po_job_no")];
				}
			}

			if ($all_barcode_no != "") {
				$production_sql = sql_select("select b.barcode_no, a.booking_id, a.booking_no, a.receive_basis,a.knitting_source, a.knitting_company  from  inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and b.status_active=1 and b.barcode_no in($all_barcode_no)");

				$production_data_arr = array();
				foreach ($production_sql as $row) {
					$booking_ref = explode("-", $row[csf("booking_no")]);
					$production_data_arr[$row[csf("barcode_no")]]["booking_no"] = abs($booking_ref[3]);
					$production_data_arr[$row[csf("barcode_no")]]["receive_basis"] = $row[csf("receive_basis")];
					$production_data_arr[$row[csf("barcode_no")]]["booking_id"] = $row[csf("booking_id")];
					$production_data_arr[$row[csf("barcode_no")]]["knitting_source"] = $row[csf("knitting_source")];
					$production_data_arr[$row[csf("barcode_no")]]["knitting_company"] = $row[csf("knitting_company")];
					if ($row[csf("receive_basis")] == 2) {
						$all_program_no .= $row[csf("booking_id")] . ",";
					}
				}

				$production_store_sql = sql_select("select b.barcode_no,a.store_id from  inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=58 and b.entry_form=58 and b.status_active=1 and b.barcode_no in($all_barcode_no)");
				$production_store_data_arr = array();
				foreach ($production_store_sql as $row) {
					$production_store_data_arr[$row[csf("barcode_no")]]["store_id"] = $row[csf("store_id")];
				}

				$prodArr = sql_select("select b.id, a.machine_no_id, a.machine_dia, a.machine_gg from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=2 and b.barcode_no in($all_barcode_no)");
				foreach ($prodArr as $row) {
					$mc_id_arr[$row[csf('id')]] = $row[csf('machine_no_id')];
					$gg_dia_arr[$row[csf('id')]]['dia'] = $row[csf('machine_dia')];
					$gg_dia_arr[$row[csf('id')]]['gauge'] = $row[csf('machine_gg')];
				}

				$is_transfer = sql_select("select id,barcode_no, is_transfer from pro_roll_details where entry_form=180 and status_active=1 and barcode_no in($all_barcode_no)");
				$is_transfer_arr = array();
				foreach ($is_transfer as $row) {
					$is_transfer_arr[$row[csf("barcode_no")]]["is_tans"] = $row[csf("is_transfer")];
				}
			}

			$all_program_no = implode(",", array_unique(explode(",", chop($all_program_no, ","))));

			if ($all_program_no != "") {
				$program_sql = sql_select("SELECT a.booking_id, a.booking_without_order, c.booking_no, b.id as program_no, b.batch_no
					from inv_receive_master a, ppl_planning_info_entry_dtls b,ppl_planning_info_entry_mst c
					where a.booking_id=b.id and b.mst_id=c.id and a.entry_form=2 and a.receive_basis=2 and a.booking_id in($all_program_no)");
				$prog_book_arr = array();
				$prog_full_book_arr = array();
				$program_batch_arr = array();
				foreach ($program_sql as $row) {
					$booking_ref = explode("-", $row[csf("booking_no")]);
					$prog_book_arr[$row[csf("booking_id")]] = abs($booking_ref[3]);
					$prog_full_book_arr[$row[csf("booking_id")]] = $row[csf("booking_no")];
					$program_batch_arr[$row[csf("program_no")]] =  $row[csf("batch_no")];
					if ($row[csf('booking_without_order')] == 1) {
						$nonOrdBooking .= "'" . $row[csf('booking_no')] . "',";
					}
				}
			}



			/*###########  according to saeed vai decission booking no show from booking table againest order end   ###############*/

			$nonOrdBooking = implode(",", array_filter(array_unique(explode(',', $nonOrdBooking))));
			if ($nonOrdBooking != "") {
				$nonOrdBooking = explode(",", $nonOrdBooking);
				$nonOrdBooking_chnk = array_chunk($nonOrdBooking, 999);
				$nonOrdBooking_chnk_cond = " and";
				foreach ($nonOrdBooking_chnk as $dtls_id) {
					if ($nonOrdBooking_chnk_cond == " and")  $nonOrdBooking_chnk_cond .= "(a.booking_no in(" . implode(',', $dtls_id) . ")";
					else $nonOrdBooking_chnk_cond .= " or a.booking_no in(" . implode(',', $dtls_id) . ")";
				}
				$nonOrdBooking_chnk_cond .= ")";
				$nonOrdBooking_sql = "SELECT b.booking_no, c.style_ref_no
				from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst c
				where a.booking_no=b.booking_no and b.style_id=c.id and b.entry_form_id=140 and a.booking_type=4 and a.item_category=2 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and c.entry_form_id=203 $nonOrdBooking_chnk_cond
				group by b.booking_no, c.style_ref_no, a.grouping, b.style_id";
				$nonOrdBooking_data = sql_select($nonOrdBooking_sql);
				$nonOrdBooking_arr = array();
				$nonOrdStyle_arr = array();
				foreach ($nonOrdBooking_data as $key => $row) {
					$nonOrdStyle_arr[$row[csf('booking_no')]] = $row[csf('style_ref_no')];
				}
			}

			foreach ($result as $row) {
				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
				}
				$mc_id = $mc_id_arr[$row[csf("roll_id")]];

				$file_ref_ord = "";
				$job_buyer_style = "";
				if ($row[csf('booking_without_order')] == 1 && $row[csf("is_sales")] != 2) // without order
				{
					//$file_ref_ord='F : <br>R : <br>B : '.$row[csf('booking_no')];
					//$prog_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']];

					//$job_buyer_style='J: <br>B : '.$buyer_array[$smn_booking_buyer_arr[$row[csf('booking_no')]]].'<br>S : ';
					// $buyer_array[$smn_booking_buyer_arr[$prog_full_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']]]]
					$file_ref_ord = "";
					$job_buyer_style = "";
					if ($production_data_arr[$row[csf("barcode_no")]]["receive_basis"] == 1) {
						$file_ref_ord = 'F : <br>R : <br>B : ' . $row[csf('booking_no')] . "<br>";
						$job_buyer_style = 'J: <br>B : ' . $buyer_array[$smn_booking_buyer_arr[$row[csf('booking_no')]]] . '<br>S : ' . $smn_booking_style_arr[$row[csf('booking_no')]];
					} else {
						//this booking and booking wise styleRef changed prog booking to issue booking. issue id: 31789
						//$file_ref_ord='F : <br>R : <br>B : '.$prog_full_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']]."<br>";
						//$job_buyer_style='J: <br>B : '.$buyer_array[$smn_booking_buyer_arr[$prog_full_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']]]].'<br>S : '.$smn_booking_style_arr[$prog_full_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']]];
						$file_ref_ord = 'F : <br>R : <br>B : ' . $row[csf("booking_no")] . "<br>";

						$job_buyer_style = 'J: <br>B : ' . $buyer_array[$smn_booking_buyer_arr[$row[csf("booking_no")]]] . '<br>S : ' . $smn_booking_style_arr[$row[csf('booking_no')]];
					}
				} else if ($row[csf('booking_without_order')] == 1 && $row[csf("is_sales")] == 2) // without order
				{
					$nonOrdStyle = $nonOrdStyle_arr[$prog_full_book_arr[$row[csf('booking_no')]]];
					if ($nonOrdStyle == "") {
						$nonOrdStyle = $smn_booking_buyer_arr2[$prog_full_book_arr[$row[csf('booking_no')]]];
					}
					/*if($production_data_arr[$row[csf("barcode_no")]]["receive_basis"]==2) // program
					{
						$file_ref_ord='F : <br>R : <br>B : '.$prog_full_book_arr[$row[csf('booking_no')]];

						$job_buyer_style='J: <br>B : '.$buyer_array[$smn_booking_buyer_arr[$prog_full_book_arr[$row[csf('booking_no')]]]].'<br>S : '.$nonOrdStyle;
					}
					else
					{*/
					$file_ref_ord = 'F : <br>R : <br>B : ' . $prog_full_book_arr[$row[csf('booking_no')]];

					$job_buyer_style = 'J: <br>B : ' . $buyer_array[$smn_booking_buyer_arr[$prog_full_book_arr[$row[csf('booking_no')]]]] . '<br>S : ' . $nonOrdStyle;
					//$smn_booking_buyer_arr2[$prog_full_book_arr[$row[csf('booking_no')]]];
					//}
				} else  // with order
				{
					if ($row[csf("is_sales")] == 1) {
						$job = $sales_arr[$row[csf('po_breakdown_id')]]['job'];
						$buyer = $sales_arr[$row[csf('po_breakdown_id')]]['buyer'];
						$style_ref = $sales_arr[$row[csf('po_breakdown_id')]]['style_ref'];
						$po_id = $sales_arr[$row[csf('po_breakdown_id')]]['sales_order'];
					} else {
						$job = $job_array[$row[csf('po_breakdown_id')]]['job'];
						$buyer = $job_array[$row[csf('po_breakdown_id')]]['buyer'];
						$style_ref = $job_array[$row[csf('po_breakdown_id')]]['style_ref'];
						$po_id = $job_array[$row[csf('po_breakdown_id')]]['po'];

						$file_ref_ord = 'F : ' . $job_array[$row[csf('po_breakdown_id')]]['file_no'] . '<br>R : ' . $job_array[$row[csf('po_breakdown_id')]]['grouping'] . '<br>O : ' . $po_id;
					}

					$job_buyer_style = 'J: ' . $job . '<br>B : ' . $buyer_array[$buyer] . '<br>S : ' . $style_ref;
				}

				$knit_party = "";

				$knit_source = $production_data_arr[$row[csf("barcode_no")]]["knitting_source"];
				if ($knit_source == 1) $knit_party = $company_array[$production_data_arr[$row[csf("barcode_no")]]["knitting_company"]]['shortname'];
				else if ($knit_source == 3) $knit_party = $supplier_arr[$production_data_arr[$row[csf("barcode_no")]]["knitting_company"]];

				$color = '';
				$color_id = explode(",", $row[csf("color_id")]);
				foreach ($color_id as $val) {
					if ($val > 0) $color .= $color_arr[$val] . ",";
				}
				$color = chop($color, ',');

				$is_transfer = $is_transfer_arr[$row[csf("barcode_no")]]["is_tans"];
				if ($is_transfer == 5) $tras_cond = "(T)";
				else $tras_cond = "";

				$book_prog_challan = "";
				if ($production_data_arr[$row[csf("barcode_no")]]["receive_basis"] == 1) {
					$book_prog_challan = "B.N.-" . $production_data_arr[$row[csf("barcode_no")]]['booking_no'] . $tras_cond . "<br>";
				} else {
					$book_prog_challan = "B.N.-" . $prog_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']] . "<br>";
					$book_prog_challan .= "P.N.-" . $production_data_arr[$row[csf("barcode_no")]]['booking_id'] . "<br>";
					$program_batch = $program_batch_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']];
				}
				$book_prog_challan .= "D.C.-" . $production_arr[$row[csf("program_no")]]['sys_num'];

			?>
				<tr style="font-size:11px">
					<td><? echo $i; ?></td>
					<td style="word-break:break-all;"><? echo $file_ref_ord; ?></td>
					<td style="word-break:break-all;"><? echo $job_buyer_style; ?></td>
					<td><? echo $grey_issue_basis[$row[csf('basis')]]; ?></td>
					<td style="word-break:break-all;"><? echo $book_prog_challan; ?></td>
					<td><? echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']]; ?></td>
					<td><? echo $row[csf('barcode_no')]; ?></td>
					<td><? echo $row[csf('stitch_length')]; ?></td>
					<td style="word-break:break-all;"><? echo 'G : ' . $product_array[$row[csf("prod_id")]]['gsm'] . '<br>D : ' . $product_array[$row[csf("prod_id")]]['dia_width']; ?></td>
					<td style="word-break:break-all;"><? echo 'N : ' . $lib_mc_arr[$mc_id]['no'] . '<br> D : ' . $gg_dia_arr[$row[csf("roll_id")]]['dia'] . ' X ' . $gg_dia_arr[$row[csf("roll_id")]]['gauge']; ?></td>
					<td style="word-break:break-all;"><? echo $color; ?></td>
					<td style="word-break:break-all;"><? echo 'B :' . $brand_arr[$row[csf("brand_id")]] . '<br>U :' . $unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']]; ?></td>
					<td style="word-break:break-all;"><? echo 'C : ' . $count . '<br>L : ' . $row[csf('yarn_lot')]; ?></td>
					<td style="word-break:break-all;"><? echo $knit_party; ?></td>
					<td style="word-break:break-all; text-align: center;"><? echo $lib_store_name[$production_store_data_arr[$row[csf("barcode_no")]]["store_id"]]; ?></td>
					<td style="word-break:break-all;"><? echo 'R : ' . $floorRoomRackShelf_array[$row[csf('rack')]] . '<br>S : ' . $floorRoomRackShelf_array[$row[csf('self')]]; ?></td>
					<td style="word-break:break-all;"><? echo $program_batch; ?></td>
					<td style="word-break:break-all;" align="right"><? echo $row[csf('roll_no')]; ?></td>
					<td align="right"><? echo number_format($row[csf('quantity')], 2); ?></td>
				</tr>
			<?
				//$tot_roll+=$row[csf('roll_no')];
				$tot_qty += $row[csf('quantity')];
				$i++;
				$x++;
			}
			?>
			<tr style="font-size:12px">
				<td align="right" colspan="17"><strong>Total</strong></td>
				<td align="right"><? echo $x; ?></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
			</tr>
		</table>
	</div>
	<? //echo signature_table(17, $company, "930px"); 
	?>
	<? echo signature_table(124, $company, "930px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "roll_issue_challan_print1") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$country_name_arr = return_library_array("select id, country_name from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name");
	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name, plot_no, road_no, city, contact_no, country_id from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$company_address_arr[$row[csf('id')]] = 'Plot No:' . $row[csf('plot_no')] . ', Road No:' . $row[csf('road_no')] . ', City / Town:' . $row[csf('city')] . ', Country:' . $country_name_arr[$row[csf('country_id')]] . ', Contact No:' . $row[csf('contact_no')];
	}

	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	// $supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$lib_store_name = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$floorRoomRackShelf_array = return_library_array("SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");

	//for supplier
	$sqlSupplier = sql_select("select id as id, supplier_name as supplier_name, short_name as short_name, address_1 from lib_supplier where status_active=1 and is_deleted=0");
	foreach ($sqlSupplier as $row) {
		$supplier_arr[$row[csf('id')]] = $row[csf('short_name')];
		$supplier_dtls_arr[$row[csf('id')]] = $row[csf('supplier_name')];
		$supplier_address_arr[$row[csf('id')]] = $row[csf('address_1')];
	}
	unset($sqlSupplier);

	$sql_booking_sql = sql_select("SELECT a.id, a.buyer_id, a.booking_no, c.style_ref_no, c.buyer_name, a.entry_form_id
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b left join  sample_development_mst c on b.style_id=c.id and c.status_active=1 and c.is_deleted=0 where a.booking_no = b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.buyer_id, a.booking_no, c.style_ref_no, c.buyer_name, a.entry_form_id ");

	foreach ($sql_booking_sql as $val) {
		if ($val[csf("entry_form_id")] == 140) {
			$smn_booking_buyer_arr[$val[csf("booking_no")]] = $val[csf("buyer_name")];
			$smn_booking_id_style_buyer_arr[$val[csf("id")]]["buyer"] = $val[csf("buyer_name")];
		} else {
			$smn_booking_buyer_arr[$val[csf("booking_no")]] = $val[csf("buyer_id")];
			$smn_booking_id_style_buyer_arr[$val[csf("id")]]["buyer"] = $val[csf("buyer_id")];
		}
		$smn_booking_style_arr[$val[csf("booking_no")]] = $val[csf("style_ref_no")];
		$smn_booking_id_style_buyer_arr[$val[csf("id")]]["style"] = $val[csf("style_ref_no")];
		$smn_booking_id_style_buyer_arr[$val[csf("id")]]["book_no"] = $val[csf("booking_no")];
	}


	$production_arr = array();
	$production_del_sql = sql_select("select id, sys_number_prefix_num, sys_number, knitting_source, knitting_company, location_id from pro_grey_prod_delivery_mst where entry_form=56 and company_id=$company");
	foreach ($production_del_sql as $row) {
		$production_arr[$row[csf('id')]]['sys'] = $row[csf('sys_number')];
		$production_arr[$row[csf('id')]]['sys_num'] = $row[csf('sys_number_prefix_num')];
		$production_arr[$row[csf('id')]]['unit'] = $row[csf('location_id')];
		$production_arr[$row[csf('id')]]['knit_sou'] = $row[csf('knitting_source')];
		$production_arr[$row[csf('id')]]['knit_com'] = $row[csf('knitting_company')];
	}

	//$mc_id_arr=return_library_array( "select b.id, a.machine_no_id from	pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=2", "id", "machine_no_id");

	$lib_mc_arr = array();
	$mc_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($mc_sql as $row) {
		$lib_mc_arr[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
	}


	$dataArray = sql_select("SELECT issue_purpose, issue_date, issue_basis, knit_dye_source, knit_dye_company, batch_no, remarks from inv_issue_master where id=$update_id");

	$product_array = array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach ($product_sql as $row) {
		$product_array[$row[csf("id")]]['gsm'] = $row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width'] = $row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id'] = $row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom'] = $row[csf("unit_of_measure")];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$grey_issue_basis = array(0 => "Independent", 1 => "PI", 2 => "Booking", 3 => "Knitting Plan", 9 => "Delivery");
?>
	<div>
		<table width="1200" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u>Grey Fabric Roll Issue Challan</u></strong></td>
			</tr>
			<tr>
				<td width="100" style="font-size:18px; font-weight:bold;"><strong>Challan No :</strong></td>
				<td width="230" style="font-size:18px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
				<td width="100"><strong>Dyeing Company:</strong></td>
				<td width="120" style="font-size:18px; font-weight:bold;">
					<?
					$knit_dye_company_address = '';
					if ($dataArray[0][csf('knit_dye_source')] == 1)
						$knit_dye_company_address = $company_address_arr[$dataArray[0][csf('knit_dye_company')]];
					else
						$knit_dye_company_address = $supplier_address_arr[$dataArray[0][csf('knit_dye_company')]];

					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
				<td width="80"><strong>Issue Date:</strong></td>
				<td width="100"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Address:</strong></td>
				<td><? echo $knit_dye_company_address; ?></td>
				<td><strong>Dyeing Source:</strong></td>
				<td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				<td><strong>Issue Purpose:</strong></td>
				<td><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Batch Number:</strong></td>
				<td><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
				<td><strong>Remarks:</strong></td>
				<td><? echo $dataArray[0][csf('remarks')]; ?></td>
				<td><strong>Basis :</strong></td>
				<td><? echo $grey_issue_basis[$dataArray[0][csf('issue_basis')]]; ?></td>
			</tr>
			<tr>
				<td colspan="3" id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1200" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center" style="font-size:12px">
				<th width="20">SL</th>
				<th width="70">File /Ref./ Order/Booking</th>
				<th width="80">Job/ Buyer. /Style</th>
				<!-- <th width="50">Basis</th>  -->
				<th width="70" style="font-size:14px;">Prog/Book/ D.Challan</th>
				<th width="135">Item Description</th>
				<th width="65" style="font-size:14px;">Barcode</th>
				<th width="50">Stich Length</th>
				<th width="50">GSM/ Fin. Dia</th>
				<th width="60">MC No / Dia X Gauge</th>
				<th width="60">Color</th>
				<th width="35">Brand /UOM</th>
				<th width="50">Count /Y. Lot</th>
				<th width="55">K. Party</th>
				<th width="70">Store Name</th>
				<th width="30">Rack/ Shelf</th>
				<!-- <th width="100">Program Batch No</th> -->
				<th width="35" style="font-size:14px;">Roll No</th>
				<th style="font-size:14px;">Issue Qty</th>
				<th>Item Size</th>
			</thead>
			<?

			//$roll_arr=return_library_array( "select id, roll_no from pro_roll_details where entry_form in(2,22)", "id", "roll_no");
			$i = 1;
			$tot_qty = 0;
			$x = 0;
			//$sql = "select a.basis, a.program_no, a.prod_id, a.issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, b.roll_no, b.barcode_no, b.po_breakdown_id, c.quantity from inv_grey_fabric_issue_dtls a, pro_roll_details b, order_wise_pro_details c where a.id=b.dtls_id and a.id=c.dtls_id and a.mst_id=$update_id and b.entry_form=61 and c.entry_form=61 and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			$sql = "SELECT a.basis, a.program_no, a.prod_id, a.issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id,a.store_name, b.roll_id, b.roll_no, b.barcode_no, b.po_breakdown_id, b.qnty as quantity, b.booking_no, b.booking_without_order,b.is_sales
			from inv_grey_fabric_issue_dtls a, pro_roll_details b
			where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61  and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			// echo $sql;
			$result = sql_select($sql);

			/*###########  according to saeed vai decission booking no show from booking table againest order   ###############*/
			$all_order_id = $all_program_no = $all_barcode_no = "";
			foreach ($result as $row) {
				if ($row[csf("is_sales")] == 1) {
					$all_fso_id .= $row[csf("po_breakdown_id")] . ",";
				} else {
					$all_order_id .= $row[csf("po_breakdown_id")] . ",";
				}

				$all_barcode_no .= $row[csf("barcode_no")] . ",";
			}
			$all_order_id = implode(",", array_unique(explode(",", chop($all_order_id, ","))));
			$all_fso_id = implode(",", array_unique(explode(",", chop($all_fso_id, ","))));
			$all_barcode_no = implode(",", array_unique(explode(",", chop($all_barcode_no, ","))));

			$job_array = array();
			if ($all_order_id != "") {
				$job_sql = "select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_order_id)";
				$job_sql_result = sql_select($job_sql);
				foreach ($job_sql_result as $row) {
					$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
					$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
					$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
					$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
					$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
					$job_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				}
			}

			if ($all_fso_id != "") {
				$data_array = sql_select("SELECT id as po_id,job_no, sales_booking_no, po_buyer,within_group,style_ref_no,po_job_no FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and id in($all_fso_id)");
				$sales_arr = array();
				$sales_booking_arr = array();
				foreach ($data_array as $row) {
					$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
					$sales_arr[$row[csf("po_id")]]['buyer'] = $row[csf("po_buyer")];
					$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
					$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
					$sales_arr[$row[csf("po_id")]]['style_ref'] = $row[csf("style_ref_no")];
					$sales_arr[$row[csf("po_id")]]['job'] = $row[csf("po_job_no")];
					$sales_booking_arr[] = "'" . $row[csf('sales_booking_no')] . "'";
				}
			}
			// echo "<pre>"; print_r($sales_booking_arr);die;

			if (!empty($sales_booking_arr)) {
				$booking_cond = " and a.booking_no in (" . implode(",", $sales_booking_arr) . ")";
				$booking_details = sql_select("SELECT a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping ref_no from wo_booking_mst a,wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and b.booking_type in(1,4) $booking_cond group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping");

				foreach ($booking_details as $booking_row) {
					$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
					$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
					$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
					$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
					$booking_arr[$booking_row[csf("booking_no")]]["int_ref"] = $booking_row[csf("ref_no")];
					$booking_arr[$booking_row[csf("po_break_down_id")]]["grouping"] = $booking_row[csf("ref_no")];
				}
			}


			if ($all_barcode_no != "") {
				$production_sql = sql_select("select b.barcode_no, a.booking_id, a.booking_no, a.receive_basis,a.knitting_source, a.knitting_company, b.coller_cuff_size  from  inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and b.status_active=1 and b.barcode_no in($all_barcode_no)");

				$production_data_arr = array();
				foreach ($production_sql as $row) {
					$booking_ref = explode("-", $row[csf("booking_no")]);
					$production_data_arr[$row[csf("barcode_no")]]["booking_no"] = abs($booking_ref[3]);
					$production_data_arr[$row[csf("barcode_no")]]["receive_basis"] = $row[csf("receive_basis")];
					$production_data_arr[$row[csf("barcode_no")]]["booking_id"] = $row[csf("booking_id")];
					$production_data_arr[$row[csf("barcode_no")]]["knitting_source"] = $row[csf("knitting_source")];
					$production_data_arr[$row[csf("barcode_no")]]["knitting_company"] = $row[csf("knitting_company")];
					$production_data_arr[$row[csf("barcode_no")]]["coller_cuff_size"] = $row[csf("coller_cuff_size")];
					if ($row[csf("receive_basis")] == 2) {
						$all_program_no .= $row[csf("booking_id")] . ",";
					}
				}

				$production_store_sql = sql_select("select b.barcode_no,a.store_id from  inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=58 and b.entry_form=58 and b.status_active=1 and b.barcode_no in($all_barcode_no)");
				$production_store_data_arr = array();
				foreach ($production_store_sql as $row) {
					$production_store_data_arr[$row[csf("barcode_no")]]["store_id"] = $row[csf("store_id")];
				}

				$prodArr = sql_select("select b.id, a.machine_no_id, a.machine_dia, a.machine_gg from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=2 and b.barcode_no in($all_barcode_no)");
				foreach ($prodArr as $row) {
					$mc_id_arr[$row[csf('id')]] = $row[csf('machine_no_id')];
					$gg_dia_arr[$row[csf('id')]]['dia'] = $row[csf('machine_dia')];
					$gg_dia_arr[$row[csf('id')]]['gauge'] = $row[csf('machine_gg')];
				}

				$is_transfer = sql_select("select id,barcode_no, is_transfer from pro_roll_details where entry_form=180 and status_active=1 and barcode_no in($all_barcode_no)");
				$is_transfer_arr = array();
				foreach ($is_transfer as $row) {
					$is_transfer_arr[$row[csf("barcode_no")]]["is_tans"] = $row[csf("is_transfer")];
				}
			}

			$all_program_no = implode(",", array_unique(explode(",", chop($all_program_no, ","))));

			if ($all_program_no != "") {
				$program_sql = sql_select("SELECT a.booking_id, a.booking_without_order, c.booking_no, b.id as program_no, b.batch_no
					from inv_receive_master a, ppl_planning_info_entry_dtls b,ppl_planning_info_entry_mst c
					where a.booking_id=b.id and b.mst_id=c.id and a.entry_form=2 and a.receive_basis=2 and a.booking_id in($all_program_no)");
				$prog_book_arr = array();
				$prog_full_book_arr = array();
				$program_batch_arr = array();
				foreach ($program_sql as $row) {
					$booking_ref = explode("-", $row[csf("booking_no")]);
					$prog_book_arr[$row[csf("booking_id")]] = abs($booking_ref[3]);
					$prog_full_book_arr[$row[csf("booking_id")]] = $row[csf("booking_no")];
					$program_batch_arr[$row[csf("program_no")]] =  $row[csf("batch_no")];
					if ($row[csf('booking_without_order')] == 1) {
						$nonOrdBooking .= "'" . $row[csf('booking_no')] . "',";
					}
				}
			}



			/*###########  according to saeed vai decission booking no show from booking table againest order end   ###############*/

			$nonOrdBooking = implode(",", array_filter(array_unique(explode(',', $nonOrdBooking))));
			if ($nonOrdBooking != "") {
				$nonOrdBooking = explode(",", $nonOrdBooking);
				$nonOrdBooking_chnk = array_chunk($nonOrdBooking, 999);
				$nonOrdBooking_chnk_cond = " and";
				foreach ($nonOrdBooking_chnk as $dtls_id) {
					if ($nonOrdBooking_chnk_cond == " and")  $nonOrdBooking_chnk_cond .= "(a.booking_no in(" . implode(',', $dtls_id) . ")";
					else $nonOrdBooking_chnk_cond .= " or a.booking_no in(" . implode(',', $dtls_id) . ")";
				}
				$nonOrdBooking_chnk_cond .= ")";
				$nonOrdBooking_sql = "SELECT a.id, b.booking_no, c.style_ref_no
				from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst c
				where a.booking_no=b.booking_no and b.style_id=c.id and b.entry_form_id=140 and a.booking_type=4 and a.item_category=2 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and c.entry_form_id=203 $nonOrdBooking_chnk_cond
				group by b.booking_no, c.style_ref_no, a.grouping, b.style_id";
				$nonOrdBooking_data = sql_select($nonOrdBooking_sql);
				$nonOrdBooking_arr = array();
				$nonOrdStyle_arr = array();
				foreach ($nonOrdBooking_data as $key => $row) {
					$nonOrdStyle_arr[$row[csf('booking_no')]] = $row[csf('style_ref_no')];
					$nonOrdBookIdStyle_arr[$row[csf('id')]] = $row[csf('style_ref_no')];
				}
			}

			foreach ($result as $row) {
				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
				}
				$mc_id = $mc_id_arr[$row[csf("roll_id")]];

				$file_ref_ord = "";
				$job_buyer_style = "";
				if ($row[csf('booking_without_order')] == 1 && $row[csf("is_sales")] != 2) // without order
				{
					//$file_ref_ord='F : <br>R : <br>B : '.$row[csf('booking_no')];
					//$prog_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']];

					//$job_buyer_style='J: <br>B : '.$buyer_array[$smn_booking_buyer_arr[$row[csf('booking_no')]]].'<br>S : ';
					// $buyer_array[$smn_booking_buyer_arr[$prog_full_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']]]]
					$file_ref_ord = "";
					$job_buyer_style = "";
					if ($production_data_arr[$row[csf("barcode_no")]]["receive_basis"] == 1) {
						$file_ref_ord = 'F : <br>R : <br>B : ' . $row[csf('booking_no')] . "<br>";
						$job_buyer_style = 'J: <br>B : ' . $buyer_array[$smn_booking_buyer_arr[$row[csf('booking_no')]]] . '<br>S : ' . $smn_booking_style_arr[$row[csf('booking_no')]];
					} else {
						//this booking and booking wise styleRef changed prog booking to issue booking. issue id: 31789
						//$file_ref_ord='F : <br>R : <br>B : '.$prog_full_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']]."<br>";
						//$job_buyer_style='J: <br>B : '.$buyer_array[$smn_booking_buyer_arr[$prog_full_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']]]].'<br>S : '.$smn_booking_style_arr[$prog_full_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']]];

						//$file_ref_ord='F : <br>R : <br>B : '.$row[csf("booking_no")]."<br>";
						//$job_buyer_style='J: <br>B : '.$buyer_array[$smn_booking_buyer_arr[$row[csf("booking_no")]]].'<br>S : '.$smn_booking_style_arr[$row[csf('booking_no')]];

						$file_ref_ord = 'F : <br>R : <br>B : ' . $smn_booking_id_style_buyer_arr[$row[csf('po_breakdown_id')]]["book_no"];
						$job_buyer_style = 'J: <br>B : ' . $buyer_array[$smn_booking_id_style_buyer_arr[$row[csf('po_breakdown_id')]]["buyer"]] . '<br>S : ' . $smn_booking_id_style_buyer_arr[$row[csf('po_breakdown_id')]]["style"];
					}
				} else if ($row[csf('booking_without_order')] == 1 && $row[csf("is_sales")] == 2) // without order
				{
					$nonOrdStyle = $nonOrdStyle_arr[$prog_full_book_arr[$row[csf('booking_no')]]];
					if ($nonOrdStyle == "") {
						$nonOrdStyle = $smn_booking_buyer_arr2[$prog_full_book_arr[$row[csf('booking_no')]]];
					}
					/*if($production_data_arr[$row[csf("barcode_no")]]["receive_basis"]==2) // program
					{
						$file_ref_ord='F : <br>R : <br>B : '.$prog_full_book_arr[$row[csf('booking_no')]];

						$job_buyer_style='J: <br>B : '.$buyer_array[$smn_booking_buyer_arr[$prog_full_book_arr[$row[csf('booking_no')]]]].'<br>S : '.$nonOrdStyle;
					}
					else
					{*/
					//$file_ref_ord='F : <br>R : <br>B : '.$prog_full_book_arr[$row[csf('booking_no')]];


					//$job_buyer_style='J: <br>B : '.$buyer_array[$smn_booking_buyer_arr[$prog_full_book_arr[$row[csf('booking_no')]]]].'<br>S : '.$nonOrdStyle;
					//$smn_booking_buyer_arr2[$prog_full_book_arr[$row[csf('booking_no')]]];
					//}

					$file_ref_ord = 'F : <br>R : <br>B : ' . $smn_booking_id_style_buyer_arr[$row[csf('po_breakdown_id')]]["book_no"];
					$job_buyer_style = 'J: <br>B : ' . $buyer_array[$smn_booking_id_style_buyer_arr[$row[csf('po_breakdown_id')]]["buyer"]] . '<br>S : ' . $smn_booking_id_style_buyer_arr[$row[csf('po_breakdown_id')]]["style"];
				} else  // with order
				{
					if ($row[csf("is_sales")] == 1) {
						$job = $sales_arr[$row[csf('po_breakdown_id')]]['job'];
						$buyer = $sales_arr[$row[csf('po_breakdown_id')]]['buyer'];
						$style_ref = $sales_arr[$row[csf('po_breakdown_id')]]['style_ref'];
						$po_id = $sales_arr[$row[csf('po_breakdown_id')]]['sales_order'];
						$sales_booking = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];

						$int_ref = $booking_arr[$sales_booking]['int_ref'];
						$file_ref_ord = 'F : ' . $job_array[$row[csf('po_breakdown_id')]]['file_no'] . '<br>R : ' . $int_ref . '<br>O : ' . $po_id;
					} else {
						$job = $job_array[$row[csf('po_breakdown_id')]]['job'];
						$buyer = $job_array[$row[csf('po_breakdown_id')]]['buyer'];
						$style_ref = $job_array[$row[csf('po_breakdown_id')]]['style_ref'];
						$po_id = $job_array[$row[csf('po_breakdown_id')]]['po'];

						$file_ref_ord = 'F : ' . $job_array[$row[csf('po_breakdown_id')]]['file_no'] . '<br>R : ' . $job_array[$row[csf('po_breakdown_id')]]['grouping'] . '<br>O : ' . $po_id;
					}

					$job_buyer_style = 'J: ' . $job . '<br>B : ' . $buyer_array[$buyer] . '<br>S : ' . $style_ref;
				}

				$knit_party = "";

				$knit_source = $production_data_arr[$row[csf("barcode_no")]]["knitting_source"];
				if ($knit_source == 1) $knit_party = $company_array[$production_data_arr[$row[csf("barcode_no")]]["knitting_company"]]['shortname'];
				else if ($knit_source == 3) $knit_party = $supplier_arr[$production_data_arr[$row[csf("barcode_no")]]["knitting_company"]];

				$color = '';
				$color_id = explode(",", $row[csf("color_id")]);
				foreach ($color_id as $val) {
					if ($val > 0) $color .= $color_arr[$val] . ",";
				}
				$color = chop($color, ',');

				$is_transfer = $is_transfer_arr[$row[csf("barcode_no")]]["is_tans"];
				if ($is_transfer == 5) $tras_cond = "(T)";
				else $tras_cond = "";

				$book_prog_challan = "";
				if ($production_data_arr[$row[csf("barcode_no")]]["receive_basis"] == 1) {
					$book_prog_challan = "B.N.-" . $production_data_arr[$row[csf("barcode_no")]]['booking_no'] . $tras_cond . "<br>";
				} else {
					$book_prog_challan = "B.N.-" . $prog_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']] . "<br>";
					$book_prog_challan .= "P.N.-" . $production_data_arr[$row[csf("barcode_no")]]['booking_id'] . "<br>";
					$program_batch = $program_batch_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']];
				}
				$book_prog_challan .= "D.C.-" . $production_arr[$row[csf("program_no")]]['sys_num'];

				$coller_cuff_size = $production_data_arr[$row[csf("barcode_no")]]["coller_cuff_size"];

			?>
				<tr style="font-size:11px" colspan="16">
					<td><? echo $i; ?></td>
					<td style="word-break:break-all;"><? echo $file_ref_ord; ?></td>
					<td style="word-break:break-all;"><? echo $job_buyer_style; ?></td>
					<!-- <td>//<? // echo// $grey_issue_basis[$row[csf('basis')]]; 
								?></td> -->
					<td style="word-break:break-all;font-size: 13px;"><? echo $book_prog_challan; ?></td>
					<td><? echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']]; ?></td>
					<td style="font-size: 13px;"><? echo $row[csf('barcode_no')]; ?></td>
					<td><? echo $row[csf('stitch_length')]; ?></td>
					<td style="word-break:break-all;"><? echo 'G : ' . $product_array[$row[csf("prod_id")]]['gsm'] . '<br>D : ' . $product_array[$row[csf("prod_id")]]['dia_width']; ?></td>
					<td style="word-break:break-all;"><? echo 'N : ' . $lib_mc_arr[$mc_id]['no'] . '<br> D : ' . $gg_dia_arr[$row[csf("roll_id")]]['dia'] . ' X ' . $gg_dia_arr[$row[csf("roll_id")]]['gauge']; ?></td>
					<td style="word-break:break-all;"><? echo $color; ?></td>
					<td style="word-break:break-all;"><? echo 'B :' . $brand_arr[$row[csf("brand_id")]] . '<br>U :' . $unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']]; ?></td>
					<td style="word-break:break-all;"><? echo 'C : ' . $count . '<br>L : ' . $row[csf('yarn_lot')]; ?></td>
					<td style="word-break:break-all;"><? echo $knit_party; ?></td>
					<td style="word-break:break-all; text-align: center;"><? echo $lib_store_name[$production_store_data_arr[$row[csf("barcode_no")]]["store_id"]]; ?></td>
					<td style="word-break:break-all;"><? echo 'R : ' . $floorRoomRackShelf_array[$row[csf('rack')]] . '<br>S : ' . $floorRoomRackShelf_array[$row[csf('self')]]; ?></td>
					<td style="word-break:break-all;font-size: 13px;" align="right"><? echo $row[csf('roll_no')]; ?></td>
					<td align="right" style="font-size: 13px;"><? echo number_format($row[csf('quantity')], 2); ?></td>
					<td align="right" width="60"><? echo $coller_cuff_size; ?></td>

				</tr>
			<?
				//$tot_roll+=$row[csf('roll_no')];
				$tot_qty += $row[csf('quantity')];
				$i++;
				$x++;
			}
			?>
			<tr style="font-size:12px">
				<td align="right" colspan="15"><strong>Total</strong></td>
				<td align="right"><? echo $x; ?></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
				<!-- <td align="right">&nbsp;</td>  -->
			</tr>
		</table>
	</div>
	<? //echo signature_table(17, $company, "930px"); 
	?>
	<? echo signature_table(124, $company, "930px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "mc_wise_print") {
	extract($_REQUEST);
	$data = explode('*', $data);

	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$booking_arr = return_library_array("select id, booking_no from wo_booking_mst where item_category in(2,13)", "id", "booking_no");
	$pi_arr = return_library_array("select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");

	$smn_booking_buyer_arr = return_library_array("select booking_no, buyer_id from wo_non_ord_samp_booking_mst", "booking_no", "buyer_id");





	$lib_mc_arr = array();
	$mc_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($mc_sql as $row) {
		$lib_mc_arr[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
	}

	$dataArray = sql_select("select issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no,remarks from inv_issue_master where id=$update_id");
	/*foreach($dataArray as $row)
	{
		$batch_no_arr[$row[csf('batch_no')]]=$row[csf('batch_no')];
	}
	$batch_no_cond = where_con_using_array($batch_no_arr,0,'id');
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where status_active=1 and is_deleted=0 $batch_no_cond", "id", "batch_no");*/


	/*$product_array=array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id']=$row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];
	}*/

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$grey_issue_basis = array(0 => "Independent", 1 => "PI", 2 => "Booking", 3 => "Knitting Plan", 9 => "Delivery");
?>
	<div>
		<table width="1000" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[3]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="130" mc_wise_print style="font-size:16px; font-weight:bold;"><strong>Dyeing Company:</strong> </td>
				<td style="font-size:18px; font-weight:bold;">
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
				<td width="120"><strong>Issue ID :</strong></td>
				<td width="175px" style="font-size:16px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
				<td width="125"><strong>Issue Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td>
				<td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				<td><strong>Issue Purpose:</strong></td>
				<td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
				<td><strong>Batch Number:</strong></td>
				<td width="175px"><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Remarks:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			<tr>
				<td colspan="3" id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center" style="font-size:12px">
				<th width="20">SL</th>
				<th width="80">File /Ref./ Order</th>
				<th width="90">Job/ Buyer. /Style</th>
				<th width="50">Basis</th>
				<th width="100">Prog/Book/ PI No</th>
				<th width="110">Item Description</th>
				<th width="50">Stich Length</th>
				<th width="50">GSM/ Fin. Dia</th>
				<th width="70">MC No / Dia X Gauge</th>
				<th width="70">Color</th>
				<th width="35">Brand /UOM</th>
				<th width="60">Count /Y. Lot</th>
				<th width="60">K. Party</th>
				<th width="30">Rack/ Shelf</th>
				<th width="35">Total Roll</th>
				<th>Issue Qty</th>
			</thead>
			<?
			$i = 1;
			$tot_qty = 0;
			$sql = "SELECT a.basis, a.program_no, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, count(b.roll_no) as tot_roll, b.po_breakdown_id, sum(b.qnty) as quantity, b.booking_no, b.booking_without_order,b.is_sales, c.detarmination_id as deter_id, c.gsm, c.dia_width, c.unit_of_measure as uom
			from inv_grey_fabric_issue_dtls a, pro_roll_details b, product_details_master c
			where a.id=b.dtls_id and a.prod_id=c.id and a.mst_id=$update_id and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			group by a.basis, a.program_no, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, b.po_breakdown_id, b.booking_no, b.booking_without_order,b.is_sales, c.detarmination_id, c.gsm, c.dia_width, c.unit_of_measure";
			$result = sql_select($sql);
			foreach ($result as $row) {
				$po_id_array[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
				$program_no_array[$row[csf('program_no')]] = $row[csf('program_no')];
				$roll_id_array[$row[csf('roll_id')]] = $row[csf('roll_id')];
			}
			$po_id_array = array_filter(array_unique($po_id_array));
			$program_no_array = array_filter(array_unique($program_no_array));
			$roll_id_array = array_filter(array_unique($roll_id_array));

			if (count($po_id_array) > 0) {
				$po_ids = implode(",", $po_id_array);
				$transBar = $all_po_id_cond = "";

				if ($db_type == 2 && count($po_id_array) > 999) {
					$po_id_arr_chunk = array_chunk($po_id_array, 999);
					foreach ($po_id_arr_chunk as $chunk_arr) {
						$transBar .= " b.id in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_po_id_cond .= " and (" . chop($transBar, 'or ') . ")";
				} else {
					$all_po_id_cond = " and b.id in($po_ids)";
				}

				$data_array = sql_select("SELECT b.id as po_id, b.job_no, b.sales_booking_no, b.buyer_id, b.within_group, b.style_ref_no FROM fabric_sales_order_mst b WHERE b.status_active=1 and b.is_deleted=0 $all_po_id_cond");
				$sales_arr = array();
				foreach ($data_array as $row) {
					$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
					$sales_arr[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_id")];
					$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
					$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
					$sales_arr[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
				}

				$job_array = array();
				$job_sql = "SELECT a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $all_po_id_cond";
				// echo $job_sql;
				$job_sql_result = sql_select($job_sql);
				foreach ($job_sql_result as $row) {
					$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
					$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
					$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
					$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
					$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
					$job_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				}

				$data_array = sql_select("SELECT b.id, a.style_ref_no, a.job_no, a.buyer_name,b.file_no, b.grouping,c.booking_no from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id $all_po_id_cond");
				$po_details_arr = array();
				foreach ($data_array as $row) {
					$po_details_arr[$row[csf("booking_no")]]['job_no'] = $row[csf("job_no")];
					$po_details_arr[$row[csf("booking_no")]]['buyer_name'] = $row[csf("buyer_name")];
					$po_details_arr[$row[csf("booking_no")]]['style_ref_no'] = $row[csf("style_ref_no")];
					$po_details_arr[$row[csf("booking_no")]]['file_no'] = $row[csf("file_no")];
					$po_details_arr[$row[csf("booking_no")]]['grouping'] = $row[csf("grouping")];
				}
			}
			if (count($program_no_array) > 0) {
				$program_nos = implode(",", $program_no_array);
				$progNo = $all_program_no_cond = "";

				if ($db_type == 2 && count($program_no_array) > 999) {
					$program_no_arr_chunk = array_chunk($program_no_array, 999);
					foreach ($program_no_arr_chunk as $chunk_arr) {
						$progNo .= " id in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_program_no_cond .= " and (" . chop($progNo, 'or ') . ")";
				} else {
					$all_program_no_cond = " and id in($program_nos)";
				}

				$production_arr = array();
				$production_del_sql = sql_select("SELECT id, sys_number, knitting_source, knitting_company, location_id from pro_grey_prod_delivery_mst where entry_form=56 and company_id=$company $all_program_no_cond");
				foreach ($production_del_sql as $row) {
					$production_arr[$row[csf('id')]]['sys'] = $row[csf('sys_number')];
					$production_arr[$row[csf('id')]]['unit'] = $row[csf('location_id')];
					$production_arr[$row[csf('id')]]['knit_sou'] = $row[csf('knitting_source')];
					$production_arr[$row[csf('id')]]['knit_com'] = $row[csf('knitting_company')];
				}
			}
			if (count($roll_id_array) > 0) {
				$roll_ids = implode(",", $roll_id_array);
				$rollId = $all_roll_id_cond = "";

				if ($db_type == 2 && count($roll_id_array) > 999) {
					$roll_id_arr_chunk = array_chunk($roll_id_array, 999);
					foreach ($roll_id_arr_chunk as $chunk_arr) {
						$rollId .= " b.id in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_roll_id_cond .= " and (" . chop($rollId, 'or ') . ")";
				} else {
					$all_roll_id_cond = " and b.id in($roll_ids)";
				}

				$mc_id_arr = array();
				$gg_dia_arr = array();
				$prodArr = sql_select("SELECT b.id, a.machine_no_id, a.machine_dia, a.machine_gg from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=2 $all_roll_id_cond");
				foreach ($prodArr as $row) {
					$mc_id_arr[$row[csf('id')]] = $row[csf('machine_no_id')];
					$gg_dia_arr[$row[csf('id')]]['dia'] = $row[csf('machine_dia')];
					$gg_dia_arr[$row[csf('id')]]['gauge'] = $row[csf('machine_gg')];
				}
			}

			foreach ($result as $row) {
				$is_sales = $row[csf('is_sales')];
				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
				}
				$mc_id = $mc_id_arr[$row[csf("roll_id")]];
				if ($row[csf('basis')] == 1) $pi_book_plan = $pi_arr[$row[csf("program_no")]];
				else if ($row[csf('basis')] == 2) $pi_book_plan = $booking_arr[$row[csf("program_no")]];
				else if ($row[csf('basis')] == 3) $pi_book_plan = $row[csf("program_no")];
				else if ($row[csf('basis')] == 9) $pi_book_plan = $production_arr[$row[csf("program_no")]]['sys']; //$production_del_arr[$row[csf("program_no")]];
				else $pi_book_plan = "&nbsp;";

				$sales_booking = $sales_arr[$row[csf("po_breakdown_id")]]['sales_booking_no'];
				$within_group = $sales_arr[$row[csf("po_breakdown_id")]]['within_group'];
				if ($is_sales == 1) {
					if ($within_group == 1) {
						$job_no = $po_details_arr[$sales_booking]['job_no'];
						$buyer_name = $buyer_array[$po_details_arr[$sales_booking]['buyer_name']];
						$style_ref = $po_details_arr[$sales_booking]['style_ref_no'];
					} else {
						$job_no = "";
						$buyer_name = $buyer_array[$sales_arr[$row[csf("po_breakdown_id")]]['buyer_name']];
						$style_ref = $sales_arr[$row[csf("po_breakdown_id")]]['style_ref_no'];
					}
					$po_number = $sales_arr[$row[csf("po_breakdown_id")]]['sales_order'];
					$file_no = $po_details_arr[$sales_booking]['file_no'];
					$ref_no = $po_details_arr[$sales_booking]['grouping'];
				} else {
					$po_number = $job_array[$row[csf('po_breakdown_id')]]['po'];
					$job_no = $job_array[$row[csf('po_breakdown_id')]]['job'];
					$buyer_name = $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']];
					$style_ref = $job_array[$row[csf('po_breakdown_id')]]['style_ref'];
					$file_no = $job_array[$row[csf('po_breakdown_id')]]['file_no'];
					$ref_no = $job_array[$row[csf('po_breakdown_id')]]['grouping'];
				}
				$file_ref_ord = "";
				$job_buyer_style = "";
				if ($row[csf('booking_without_order')] == 1) {
					$file_ref_ord = 'F : <br>R : <br>B : ' . $row[csf('booking_no')];
					$job_buyer_style = 'J: <br>B : ' . $buyer_array[$smn_booking_buyer_arr[$row[csf('booking_no')]]] . '<br>S : ';
				} else {
					$file_ref_ord = 'F : ' . $file_no . '<br>R : ' . $ref_no . '<br>O : ' . $po_number;
					$job_buyer_style = 'J: ' . $job_no . '<br>B : ' . $buyer_name . '<br>S : ' . $style_ref;
				}

				$knit_party = "";
				$knit_source = $production_arr[$row[csf("program_no")]]['knit_sou'];
				if ($knit_source == 1) $knit_party = $company_array[$production_arr[$row[csf("program_no")]]['knit_com']]['shortname'];
				else if ($knit_source == 3) $knit_party = $supplier_arr[$production_arr[$row[csf("program_no")]]['knit_com']];

				$print_dt[$file_ref_ord][$job_buyer_style][$grey_issue_basis[$row[csf('basis')]]][$pi_book_plan][$composition_arr[$row[csf("deter_id")]]]['G : ' . $row[csf("gsm")] . '<br>D : ' . $row[csf("dia_width")]]['N : ' . $lib_mc_arr[$mc_id]['no'] . '<br> D : ' . $gg_dia_arr[$row[csf("roll_id")]]['dia'] . ' X ' . $gg_dia_arr[$row[csf("roll_id")]]['gauge']][$row[csf("color_id")]]['B :' . $brand_arr[$row[csf("brand_id")]] . '<br>U :' . $unit_of_measurement[$row[csf("uom")]]]['C : ' . $count . '<br>L : ' . $row[csf('yarn_lot')]][$knit_party][$row[csf('stitch_length')]]['roll_no'] += $row[csf('tot_roll')];
				$print_dt[$file_ref_ord][$job_buyer_style][$grey_issue_basis[$row[csf('basis')]]][$pi_book_plan][$composition_arr[$row[csf("deter_id")]]]['G : ' . $row[csf("gsm")] . '<br>D : ' . $row[csf("dia_width")]]['N : ' . $lib_mc_arr[$mc_id]['no'] . '<br> D : ' . $gg_dia_arr[$row[csf("roll_id")]]['dia'] . ' X ' . $gg_dia_arr[$row[csf("roll_id")]]['gauge']][$row[csf("color_id")]]['B :' . $brand_arr[$row[csf("brand_id")]] . '<br>U :' . $unit_of_measurement[$row[csf("uom")]]]['C : ' . $count . '<br>L : ' . $row[csf('yarn_lot')]][$knit_party][$row[csf('stitch_length')]]['qnty'] += $row[csf('quantity')];
			}
			//	print_r($print_dt);
			foreach ($print_dt as $file => $fdet) {
				foreach ($fdet as $job_buyers => $jbdet) {
					foreach ($jbdet as $basis => $basbdet) {
						foreach ($basbdet as $pi_book_p => $pibookdet) {
							foreach ($pibookdet as $compos => $composdet) {
								foreach ($composdet as $diawid => $diawiddet) {
									foreach ($diawiddet as $gaugewid => $gaugewiddet) {
										foreach ($gaugewiddet as $colo => $colodet) {
											foreach ($colodet as $brandde => $branddedet) {
												foreach ($branddedet as $countde => $dedet) {
													foreach ($dedet as $partys => $partysdet) {
														foreach ($partysdet as $slength => $slength_val) {
															$color = '';
															$color_id = explode(",", $colo);
															foreach ($color_id as $val) {
																if ($val > 0) $color .= $color_arr[$val] . ",";
															}
															$color = chop($color, ',');
			?>
															<tr style="font-size:11px">
																<td><? echo $i; ?></td>
																<td style="word-break:break-all;"><? echo $file; ?></td>
																<td style="word-break:break-all;"><? echo $job_buyers; ?></td>
																<td><? echo $basis; ?></td>
																<td style="word-break:break-all;"><? echo $pi_book_p; ?></td>
																<td><? echo $compos; ?></td>
																<td><? echo $slength; ?></td>
																<td style="word-break:break-all;"><? echo $diawid; ?></td>
																<td style="word-break:break-all;"><? echo $gaugewid; ?></td>
																<td style="word-break:break-all;"><? echo $color; ?></td>
																<td style="word-break:break-all;"><? echo $brandde; ?></td>
																<td style="word-break:break-all;"><? echo $countde; ?></td>
																<td style="word-break:break-all;"><? echo $partys; ?></td>
																<td style="word-break:break-all;"><? echo 'R : ' . $row[csf('rack')] . '<br>S : ' . $row[csf('self')]; ?></td>
																<td style="word-break:break-all;" align="right"><? echo $slength_val['roll_no']; ?></td>
																<td align="right"><? echo number_format($slength_val['qnty'], 2); ?></td>
															</tr>
			<?
															$tot_roll += $slength_val['roll_no'];
															$tot_qty += $slength_val['qnty'];
															$i++;
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
			?>
			<tr style="font-size:12px">
				<td align="right" colspan="14"><strong>Total</strong></td>
				<td align="right"><? echo number_format($tot_roll); ?></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
			</tr>
		</table>
	</div>
	<? echo signature_table(124, $company, "1000px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "print_mg_two") {
	extract($_REQUEST);
	$data = explode('*', $data);

	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$booking_arr = return_library_array("select id, booking_no from wo_booking_mst where item_category in(2,13)", "id", "booking_no");

	$smn_booking_buyer_arr = return_library_array("select booking_no, buyer_id from wo_non_ord_samp_booking_mst", "booking_no", "buyer_id");

	$lib_mc_arr = array();
	$mc_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($mc_sql as $row) {
		$lib_mc_arr[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
	}

	$dataArray = sql_select("select issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no,remarks from inv_issue_master where id=$update_id");

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$grey_issue_basis = array(0 => "Independent", 1 => "PI", 2 => "Booking", 3 => "Knitting Plan", 9 => "Delivery");
?>
	<div>
		<table width="1000" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[3]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="130" mc_wise_print style="font-size:16px; font-weight:bold;"><strong>Dyeing Company:</strong> </td>
				<td style="font-size:18px; font-weight:bold;">
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
				<td width="120"><strong>Issue ID :</strong></td>
				<td width="175px" style="font-size:16px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
				<td width="125"><strong>Issue Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td>
				<td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				<td><strong>Issue Purpose:</strong></td>
				<td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
				<td><strong>Batch Number:</strong></td>
				<td width="175px"><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Remarks:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			<tr>
				<td colspan="3" id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center" style="font-size:12px">
				<th width="20">SL</th>
				<th width="120">File /Ref./ Order</th>
				<th width="100">Job/ Buyer. /Style</th>
				<th width="120">Del/Prog/Book</th>
				<th width="140">Item Description</th>
				<th width="50">Stich Length</th>
				<th width="50">GSM/ Fin. Dia</th>
				<th width="70">MC No / Dia X Gauge</th>
				<th width="70">Color</th>
				<th width="35">Brand</th>
				<th width="60">Count /Y. Lot</th>
				<th width="60">K. Party</th>
				<th width="35">Total Roll</th>
				<th>Issue Qty</th>
			</thead>
			<?
			$i = 1;
			$tot_qty = 0;
			$sql = "SELECT a.basis, a.program_no, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.brand_id, b.roll_id, count(b.roll_no) as tot_roll, b.po_breakdown_id, sum(b.qnty) as quantity, b.booking_no, b.booking_without_order,b.is_sales, b.barcode_no, c.detarmination_id as deter_id, c.gsm, c.dia_width
			from inv_grey_fabric_issue_dtls a, pro_roll_details b, product_details_master c
			where a.id=b.dtls_id and a.prod_id=c.id and a.mst_id=$update_id and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			group by a.basis, a.program_no, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.brand_id, b.roll_id, b.po_breakdown_id, b.booking_no, b.booking_without_order,b.is_sales, b.barcode_no, c.detarmination_id, c.gsm, c.dia_width";
			$result = sql_select($sql);
			$barcodeNos = "";
			foreach ($result as $row) {
				$program_no_array[$row[csf('program_no')]] = $row[csf('program_no')];
				$roll_id_array[$row[csf('roll_id')]] = $row[csf('roll_id')];

				if ($row[csf('is_sales')] == 1) {
					$sales_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
				} else {
					$po_id_array[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
				}
				$barcodeNos .= $row[csf("barcode_no")] . ",";
			}
			$sales_ids_arr = array_filter(array_unique($sales_ids_arr));
			$po_id_array = array_filter(array_unique($po_id_array));
			$program_no_array = array_filter(array_unique($program_no_array));
			$roll_id_array = array_filter(array_unique($roll_id_array));
			$barcodeNos = chop($barcodeNos, ",");

			if (count($po_id_array) > 0) {
				$po_ids = implode(",", $po_id_array);
				$transBar = $all_po_id_cond = "";

				if ($db_type == 2 && count($po_id_array) > 999) {
					$po_id_arr_chunk = array_chunk($po_id_array, 999);
					foreach ($po_id_arr_chunk as $chunk_arr) {
						$transBar .= " b.id in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_po_id_cond .= " and (" . chop($transBar, 'or ') . ")";
				} else {
					$all_po_id_cond = " and b.id in($po_ids)";
				}

				$job_array = array();
				$job_sql = "SELECT a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $all_po_id_cond";
				// echo $job_sql;
				$job_sql_result = sql_select($job_sql);
				foreach ($job_sql_result as $row) {
					$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
					$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
					$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
					$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
					$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
					$job_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				}
			}

			if (count($sales_ids_arr) > 0) {
				$fso_ids = implode(",", $sales_ids_arr);
				$transBar = $all_fso_id_cond = "";

				if ($db_type == 2 && count($sales_ids_arr) > 999) {
					$po_id_arr_chunk = array_chunk($sales_ids_arr, 999);
					foreach ($po_id_arr_chunk as $chunk_arr) {
						$transBar .= " b.id in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_fso_id_cond .= " and (" . chop($transBar, 'or ') . ")";
				} else {
					$all_fso_id_cond = " and b.id in($fso_ids)";
				}

				$data_array = sql_select("SELECT b.booking_id, b.id as po_id, b.job_no, b.sales_booking_no, b.buyer_id, b.within_group, b.style_ref_no FROM fabric_sales_order_mst b WHERE b.status_active=1 and b.is_deleted=0 $all_fso_id_cond");
				$sales_arr = array();
				$sales_booking_id_arr = array();
				foreach ($data_array as $row) {
					$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
					$sales_arr[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_id")];
					$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
					$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
					$sales_arr[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
					$sales_booking_id_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
				}
				$all_sales_booking_ids = implode(",", array_unique($sales_booking_id_arr));
				if (!empty($sales_booking_id_arr)) {
					$po_data_array = sql_select("SELECT b.id, a.style_ref_no, a.job_no, a.buyer_name,b.file_no, b.grouping,c.booking_no from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and c.booking_mst_id in($all_sales_booking_ids)");
					$po_details_arr = array();
					foreach ($po_data_array as $row) {
						$po_details_arr[$row[csf("booking_no")]]['job_no'] = $row[csf("job_no")];
						$po_details_arr[$row[csf("booking_no")]]['buyer_name'] = $row[csf("buyer_name")];
						$po_details_arr[$row[csf("booking_no")]]['style_ref_no'] = $row[csf("style_ref_no")];
						$po_details_arr[$row[csf("booking_no")]]['file_no'] = $row[csf("file_no")];
						$po_details_arr[$row[csf("booking_no")]]['grouping'] = $row[csf("grouping")];
					}
				}
			}

			if (count($program_no_array) > 0) {
				$program_nos = implode(",", $program_no_array);
				$progNo = $all_program_no_cond = "";

				if ($db_type == 2 && count($program_no_array) > 999) {
					$program_no_arr_chunk = array_chunk($program_no_array, 999);
					foreach ($program_no_arr_chunk as $chunk_arr) {
						$progNo .= " id in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_program_no_cond .= " and (" . chop($progNo, 'or ') . ")";
				} else {
					$all_program_no_cond = " and id in($program_nos)";
				}

				$production_arr = array();
				$production_del_sql = sql_select("SELECT id, sys_number, knitting_source, knitting_company, location_id from pro_grey_prod_delivery_mst where entry_form=56 and company_id=$company $all_program_no_cond");
				foreach ($production_del_sql as $row) {
					$production_arr[$row[csf('id')]]['sys'] = $row[csf('sys_number')];
					$production_arr[$row[csf('id')]]['unit'] = $row[csf('location_id')];
					$production_arr[$row[csf('id')]]['knit_sou'] = $row[csf('knitting_source')];
					$production_arr[$row[csf('id')]]['knit_com'] = $row[csf('knitting_company')];
				}
			}
			if (count($roll_id_array) > 0) {
				$roll_ids = implode(",", $roll_id_array);
				$rollId = $all_roll_id_cond = "";

				if ($db_type == 2 && count($roll_id_array) > 999) {
					$roll_id_arr_chunk = array_chunk($roll_id_array, 999);
					foreach ($roll_id_arr_chunk as $chunk_arr) {
						$rollId .= " b.id in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_roll_id_cond .= " and (" . chop($rollId, 'or ') . ")";
				} else {
					$all_roll_id_cond = " and b.id in($roll_ids)";
				}

				$mc_id_arr = array();
				$gg_dia_arr = array();
				$prodArr = sql_select("SELECT b.id, a.machine_no_id, a.machine_dia, a.machine_gg from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=2 $all_roll_id_cond");
				foreach ($prodArr as $row) {
					$mc_id_arr[$row[csf('id')]] = $row[csf('machine_no_id')];
					$gg_dia_arr[$row[csf('id')]]['dia'] = $row[csf('machine_dia')];
					$gg_dia_arr[$row[csf('id')]]['gauge'] = $row[csf('machine_gg')];
				}
			}

			$sql_production = sql_select("SELECT a.barcode_no,b.booking_no as program_no,c.machine_dia,c.machine_gg ,c.body_part_id ,e.booking_no
			from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c,ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e
			where a.mst_id=b.id  and a.dtls_id=c.id and b.id=c.mst_id and b.booking_id=d.id and d.mst_id=e.id and a.barcode_no in($barcodeNos) and b.receive_basis=2 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

			foreach ($sql_production as $row) {
				$production_data_arr[$row[csf("barcode_no")]] = $row[csf("program_no")];

				$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
				$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
				$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] = $body_part[$row[csf("body_part_id")]];
				$production_rcv_data[$row[csf("barcode_no")]]["progBooking"] = $row[csf("booking_no")];
			}

			foreach ($result as $row) {
				if ($row[csf("booking_without_order")] == 0) {
					$booking_number = $production_rcv_data[$row[csf("barcode_no")]]["progBooking"];
				} else {
					$booking_number = $booking_data_arr[$row[csf("po_breakdown_id")]];
				}
				$program_no = $production_data_arr[$row[csf("barcode_no")]];

				$is_sales = $row[csf('is_sales')];
				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
				}
				$mc_id = $mc_id_arr[$row[csf("roll_id")]];
				if ($row[csf('basis')] == 2) $delivery = $booking_arr[$row[csf("program_no")]];
				else if ($row[csf('basis')] == 3) $delivery = $row[csf("program_no")];
				else if ($row[csf('basis')] == 9) $delivery = $production_arr[$row[csf("program_no")]]['sys']; //$production_del_arr[$row[csf("program_no")]];
				else $delivery = "&nbsp;";

				$del_book_prog = 'D: ' . $delivery . '<br>P : ' . $program_no . '<br>B : ' . $booking_number;

				$sales_booking = $sales_arr[$row[csf("po_breakdown_id")]]['sales_booking_no'];
				$within_group = $sales_arr[$row[csf("po_breakdown_id")]]['within_group'];
				if ($is_sales == 1) {
					if ($within_group == 1) {
						$job_no = $po_details_arr[$sales_booking]['job_no'];
						$buyer_name = $buyer_array[$po_details_arr[$sales_booking]['buyer_name']];
						$style_ref = $po_details_arr[$sales_booking]['style_ref_no'];
					} else {
						$job_no = "";
						$buyer_name = $buyer_array[$sales_arr[$row[csf("po_breakdown_id")]]['buyer_name']];
						$style_ref = $sales_arr[$row[csf("po_breakdown_id")]]['style_ref_no'];
					}
					$po_number = $sales_arr[$row[csf("po_breakdown_id")]]['sales_order'];
					$file_no = $po_details_arr[$sales_booking]['file_no'];
					$ref_no = $po_details_arr[$sales_booking]['grouping'];
				} else {
					$po_number = $job_array[$row[csf('po_breakdown_id')]]['po'];
					$job_no = $job_array[$row[csf('po_breakdown_id')]]['job'];
					$buyer_name = $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']];
					$style_ref = $job_array[$row[csf('po_breakdown_id')]]['style_ref'];
					$file_no = $job_array[$row[csf('po_breakdown_id')]]['file_no'];
					$ref_no = $job_array[$row[csf('po_breakdown_id')]]['grouping'];
				}
				$file_ref_ord = "";
				$job_buyer_style = "";
				if ($row[csf('booking_without_order')] == 1) {
					$file_ref_ord = 'F : <br>R : <br>B : ' . $row[csf('booking_no')];
					$job_buyer_style = 'J: <br>B : ' . $buyer_array[$smn_booking_buyer_arr[$row[csf('booking_no')]]] . '<br>S : ';
				} else {
					$file_ref_ord = 'F : ' . $file_no . '<br>R : ' . $ref_no . '<br>O : ' . $po_number;
					$job_buyer_style = 'J: ' . $job_no . '<br>B : ' . $buyer_name . '<br>S : ' . $style_ref;
				}

				$knit_party = "";
				$knit_source = $production_arr[$row[csf("program_no")]]['knit_sou'];
				if ($knit_source == 1) $knit_party = $company_array[$production_arr[$row[csf("program_no")]]['knit_com']]['shortname'];
				else if ($knit_source == 3) $knit_party = $supplier_arr[$production_arr[$row[csf("program_no")]]['knit_com']];

				$print_dt[$file_ref_ord][$job_buyer_style][$del_book_prog][$composition_arr[$row[csf("deter_id")]]]['G : ' . $row[csf("gsm")] . '<br>D : ' . $row[csf("dia_width")]]['N : ' . $lib_mc_arr[$mc_id]['no'] . '<br> D : ' . $gg_dia_arr[$row[csf("roll_id")]]['dia'] . ' X ' . $gg_dia_arr[$row[csf("roll_id")]]['gauge']][$row[csf("color_id")]]['B :' . $brand_arr[$row[csf("brand_id")]]]['C : ' . $count . '<br>L : ' . $row[csf('yarn_lot')]][$knit_party][$row[csf('stitch_length')]]['roll_no'] += $row[csf('tot_roll')];
				$print_dt[$file_ref_ord][$job_buyer_style][$del_book_prog][$composition_arr[$row[csf("deter_id")]]]['G : ' . $row[csf("gsm")] . '<br>D : ' . $row[csf("dia_width")]]['N : ' . $lib_mc_arr[$mc_id]['no'] . '<br> D : ' . $gg_dia_arr[$row[csf("roll_id")]]['dia'] . ' X ' . $gg_dia_arr[$row[csf("roll_id")]]['gauge']][$row[csf("color_id")]]['B :' . $brand_arr[$row[csf("brand_id")]]]['C : ' . $count . '<br>L : ' . $row[csf('yarn_lot')]][$knit_party][$row[csf('stitch_length')]]['qnty'] += $row[csf('quantity')];

				$all_barcode_no .= $row[csf("barcode_no")] . ",";
				$booking_program_arr[$program_no] = $program_no;
				$tot_rows++;
			}

			//	print_r($print_dt);
			foreach ($print_dt as $file => $fdet) {
				foreach ($fdet as $job_buyers => $jbdet) {
					foreach ($jbdet as $del_book_p => $pibookdet) {
						foreach ($pibookdet as $compos => $composdet) {
							foreach ($composdet as $diawid => $diawiddet) {
								foreach ($diawiddet as $gaugewid => $gaugewiddet) {
									foreach ($gaugewiddet as $colo => $colodet) {
										foreach ($colodet as $brandde => $branddedet) {
											foreach ($branddedet as $countde => $dedet) {
												foreach ($dedet as $partys => $partysdet) {
													foreach ($partysdet as $slength => $slength_val) {
														$color = '';
														$color_id = explode(",", $colo);
														foreach ($color_id as $val) {
															if ($val > 0) $color .= $color_arr[$val] . ",";
														}
														$color = chop($color, ',');
			?>
														<tr style="font-size:11px">
															<td><? echo $i; ?></td>
															<td style="word-break:break-all;"><? echo $file; ?></td>
															<td style="word-break:break-all;"><? echo $job_buyers; ?></td>
															<td style="word-break:break-all;"><? echo $del_book_p; ?></td>
															<td><? echo $compos; ?></td>
															<td><? echo $slength; ?></td>
															<td style="word-break:break-all;"><? echo $diawid; ?></td>
															<td style="word-break:break-all;"><? echo $gaugewid; ?></td>
															<td style="word-break:break-all;"><? echo $color; ?></td>
															<td style="word-break:break-all;"><? echo $brandde; ?></td>
															<td style="word-break:break-all;"><? echo $countde; ?></td>
															<td style="word-break:break-all;"><? echo $partys; ?></td>
															<td style="word-break:break-all;" align="right"><? echo $slength_val['roll_no']; ?></td>
															<td align="right"><? echo number_format($slength_val['qnty'], 2); ?></td>
														</tr>
			<?
														$tot_roll += $slength_val['roll_no'];
														$tot_qty += $slength_val['qnty'];
														$i++;
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
			?>
			<tr style="font-size:12px">
				<td align="right" colspan="12"><strong>Total</strong></td>
				<td align="right"><? echo number_format($tot_roll); ?></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
			</tr>
		</table>
	</div>
	<br>
	<div style="width:1685px;">
		<?
		$barcode_nums = chop($all_barcode_no, ",");
		$barcode_cond = "";
		if ($db_type == 2 && $tot_rows > 1000) {
			$barcode_cond = " and (";
			$barcodeArr = array_chunk(explode(",", $barcode_nums), 999);
			foreach ($barcodeArr as $barcode) {
				$barcode = implode(",", $barcode);
				$barcode_cond .= " c.barcode_no in($barcode) or ";
			}

			$barcode_cond = chop($barcode_cond, 'or ');
			$barcode_cond .= ")";
		} else {
			$barcode_cond = " and c.barcode_no in ($barcode_nums)";
		}

		// For Coller and Cuff data
		$planOrder = sql_select("SELECT a.id, b.booking_no, b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(" . implode(",", $booking_program_arr) . ")");
		$plan_arr = array();
		foreach ($planOrder as $plan_row) {
			$plan_arr[$plan_row[csf("id")]]["machine_dia"] = $plan_row[csf("machine_dia")];
			$plan_arr[$plan_row[csf("id")]]["feeder"] = $plan_row[csf("feeder")];
			$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
		}

		$colarCupArr = sql_select("SELECT id, body_part_type, body_part_full_name from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(40,50)");
		foreach ($colarCupArr as $row) {
			$body_part_data_arr[$row[csf('id')]]['body_part_full_name'] = $row[csf('body_part_full_name')];
			$body_part_data_arr[$row[csf('id')]]['body_part_type'] = $row[csf('body_part_type')];
		}

		$sql_coller_cuff = "SELECT a.receive_basis, a.booking_no, b.body_part_id, c.booking_no as bwo, c.coller_cuff_size, c.qnty, c.qc_pass_qnty_pcs, c.qc_pass_qnty
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 $barcode_cond order by b.body_part_id, c.coller_cuff_size";
		// echo $sql_coller_cuff;
		$sql_coller_cuff_result = sql_select($sql_coller_cuff);
		foreach ($sql_coller_cuff_result as $row2) {
			if ($body_part_data_arr[$row2[csf('body_part_id')]]['body_part_type'] > 0 && $row2[csf('qc_pass_qnty_pcs')] > 0) {
				$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qnty'] += $row2[csf('qnty')];
				$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty_pcs'] += $row2[csf('qc_pass_qnty_pcs')];
				$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty'] += $row2[csf('qc_pass_qnty')];
				$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['no_of_roll']++;
				$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['receive_basis'] = $row2[csf('receive_basis')];
			}
		}
		// echo "<pre>"; print_r($coller_cuff_data_arr);die;
		//print_r($cuff_data_arr);die;

		// echo '<pre>';print_r($coller_cuff_data_arr);
		$CoCu = 1;
		foreach ($coller_cuff_data_arr as $coll_cuff_id => $booking_data_arr) {
			if (count($booking_data_arr) > 0) {
				//$body_part_full_name = return_field_value("body_part_full_name", "lib_body_part", "status_active=1 and is_deleted =0 and id=$coll_cuff_id", "body_part_full_name");
		?>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left;">
					<tr>
						<th colspan="4"><? echo $body_part_data_arr[$coll_cuff_id]['body_part_full_name']; ?> Details</th>
					</tr>
					<tr>
						<th>Internal Ref. No</th>
						<th>Fabric Booking No</th>
						<th>Size</th>
						<th>Qty Pcs</th>
						<th>Qty KG</th>
						<th>Roll Qty</th>
					</tr>
					<?
					$coller_cuff_qty_total = 0;
					$qc_pass_qnty_total = 0;
					$no_of_roll_total = 0;
					foreach ($booking_data_arr as $bookingId => $bookingData) {
						foreach ($bookingData as $jobId => $jobData) {
							foreach ($jobData as $size => $row) {
					?>
								<tr>
									<?
									if ($row['receive_basis'] == 2) {
									?>
										<td><?
											if ($row['receive_basis'] == 2) {
												//echo  $booking_arr[$plan_arr[$bookingId]["booking_no"]]["booking_ref_no"];
												echo  $po_details_arr[$plan_arr[$bookingId]["booking_no"]]['grouping'];
											} else {
												echo  $booking_arr[$bookingId]["booking_ref_no"];
											}
											?></td>
										<td><? echo $plan_arr[$bookingId]["booking_no"]; ?></td>
									<?
									} else {
									?>
										<td><? echo $booking_arr[$bookingId]["job_no"]; ?></td>
										<td><? echo  $bookingId;  ?></td>
									<?
									}
									?>
									<td align="center"><? echo $size; ?></td>
									<td align="center"><? echo $row['qc_pass_qnty_pcs']; ?></td>
									<td align="center"><? echo number_format($row['qc_pass_qnty'], 2); ?></td>
									<td align="center"><? echo $row['no_of_roll']; ?></td>
								</tr>
					<?
								$coller_cuff_qty_total += $row['qc_pass_qnty_pcs'];
								$qc_pass_qnty_total += $row['qc_pass_qnty'];
								$no_of_roll_total += $row['no_of_roll'];
							}
						}
					}
					?>
					<tr>
						<td colspan="3" align="right"><b>Total</b></td>
						<td align="center"><b><? echo $coller_cuff_qty_total; ?></b></td>
						<td align="center"><b><? echo number_format($qc_pass_qnty_total, 2); ?></b></td>
						<td align="center"><b><? echo $no_of_roll_total; ?></b></td>
					</tr>
				</table>
		<?
				if ($CoCu == 1) {
					echo "<table width=\"30%\" style=\"float:left\"><tr><td colspan=\"4\">&nbsp;</td></tr></table>";
				}
				$CoCu++;
			}
		}
		?>
	</div>
	<div style="float: left; display: block; width:100%;
        margin-top: 10px;
        height: 175px;">
		<? echo signature_table(124, $company, "1000px"); ?>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "fabric_details_print") {
	extract($_REQUEST);
	$data = explode('*', $data);


	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_array = array();

	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");

	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$booking_arr = return_library_array("select id, booking_no from wo_booking_mst where item_category in(2,13)", "id", "booking_no");
	$pi_arr = return_library_array("select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");
	$production_arr = return_library_array("select id, sys_number from pro_grey_prod_delivery_mst where entry_form=56", "id", "sys_number");
	$dataArray = sql_select("select issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no,remarks from inv_issue_master where id=$update_id");
	$floor_room_rack_array = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");

	/*$product_array=array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id']=$row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];
	}*/

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$grey_issue_basis = array(0 => "Independent", 1 => "PI", 2 => "Booking", 3 => "Knitting Plan", 9 => "Delivery");
?>
	<div>
		<table width="900" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[3]; ?> Grey Fabric Roll Issue Challan</u></strong></td>
			</tr>
			<tr>
				<td width="130" style="font-size:16px; font-weight:bold;"><strong>Dyeing Company:</strong> </td>
				<td style="font-size:18px; font-weight:bold;">
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
				<td width="120"><strong>Issue ID :</strong></td>
				<td width="175px" style="font-size:16px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
				<td width="125"><strong>Issue Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td>
				<td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				<td><strong>Issue Purpose:</strong></td>
				<td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
				<td><strong>Batch Number:</strong></td>
				<td width="175px"><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
			</tr>

			<tr>
				<td>
					<?
					if ($db_type == 0) {
						$po_id = return_field_value("group_concat(po_breakdown_id) as po_id", "inv_grey_fabric_issue_dtls a, order_wise_pro_details b", "a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "po_id");
					} else {
						$po_id = return_field_value("LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id", "inv_grey_fabric_issue_dtls a, order_wise_pro_details b", "a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "po_id");
					}
					// echo $po_id;
					$sales_data = sql_select("select po_breakdown_id,mst_id,is_sales,barcode_no from pro_roll_details where mst_id=$update_id and entry_form=61 and status_active=1 and is_deleted=0");
					$sales_data_arr = array();
					foreach ($sales_data as $row) {
						$sales_data_arr[$row[csf('po_breakdown_id')]]['is_sales'] = $row[csf('is_sales')];
					}
					$po_ids = implode(',', array_unique(explode(',', $po_id)));
					$data_array = sql_select("SELECT id as po_id,job_no, sales_booking_no, buyer_id,within_group,style_ref_no FROM fabric_sales_order_mst WHERE id in($po_ids) and status_active=1 and is_deleted=0");
					$sales_arr = array();
					foreach ($data_array as $row) {
						$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
						$sales_arr[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_id")];
						$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
						$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
						$sales_arr[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
					}

					$po_exp = array_unique(explode(',', $po_id));
					foreach ($po_exp as $id) {
						$sales_booking = $sales_arr[$id]['sales_booking_no'];
						$po_id_arr[$id] = $id;
					}
					// echo implode(',', $po_id_arr);
					// echo "<pre>";print_r($po_id_arr);die;
					$job_array = array();
					$job_sql = "SELECT a.style_ref_no, a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and b.id in(" . implode(',', $po_id_arr) . ") ";
					$job_sql_result = sql_select($job_sql);
					foreach ($job_sql_result as $row) {
						$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
						$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
						$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
					}

					$data_array = sql_select("SELECT b.id, a.style_ref_no, a.job_no, a.buyer_name,b.file_no, b.grouping,c.booking_no from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c where a.id=b.job_id and c.po_break_down_id=b.id  and b.id in(" . implode(',', $po_id_arr) . ")");
					$po_details_arr = array();
					foreach ($data_array as $row) {
						$po_details_arr[$row[csf("booking_no")]]['job_no'] = $row[csf("job_no")];
						$po_details_arr[$row[csf("booking_no")]]['buyer_name'] = $row[csf("buyer_name")];
						$po_details_arr[$row[csf("booking_no")]]['style_ref_no'] = $row[csf("style_ref_no")];
					}

					$po_no = '';
					$job = '';
					$style_ref = '';
					foreach ($po_exp as $id) {
						$is_sales = $sales_data_arr[$id]['is_sales'];
						$sales_booking = $sales_arr[$id]['sales_booking_no'];
						$within_group = $sales_arr[$id]['within_group'];
						if ($is_sales == 1) {
							if ($within_group == 1) {
								if ($po_no == '') $po_no = $sales_arr[$id]['sales_order'];
								else $po_no .= ', ' . $sales_arr[$id]['sales_order'];
								if ($job == '') $job = $po_details_arr[$sales_booking]['job_no'];
								else $job .= ',' . $po_details_arr[$sales_booking]['job_no'];
								if ($style_ref == '') $style_ref = $po_details_arr[$sales_booking]['style_ref_no'];
								else $style_ref .= ',' . $po_details_arr[$sales_booking]['style_ref_no'];
							} else {
								if ($po_no == '') $po_no = $sales_arr[$id]['sales_order'];
								else $po_no .= ', ' . $sales_arr[$id]['sales_order'];
								if ($job == '') $job = $po_details_arr[$sales_booking]['job_no'];
								else $job .= ',' . $po_details_arr[$sales_booking]['job_no'];
								if ($style_ref == '') $style_ref = $sales_arr[$id]['style_ref_no'];
								else $style_ref .= ',' . $sales_arr[$id]['style_ref_no'];
							}
						} else {
							if ($po_no == '') $po_no = $job_array[$id]['po'];
							else $po_no .= ', ' . $job_array[$id]['po'];
							if ($job == '') $job = $job_array[$id]['job'];
							else $job .= ',' . $job_array[$id]['job'];
							if ($style_ref == '') $style_ref = $job_array[$id]['style_ref'];
							else $style_ref .= ',' . $job_array[$id]['style_ref'];
						}
					}
					$job = implode(",", array_unique(explode(',', $job)));
					$po_no = implode(",", array_unique(explode(',', $po_no)));
					$style_ref = implode(",", array_unique(explode(',', $style_ref)));
					?>
					<strong>Job No:</strong>
				</td>
				<td width="175px" colspan="3"><? echo $job; ?></td>
				<td><strong>Style Ref.:</strong></td>
				<td width="175px"><? echo $style_ref; ?></td>
			</tr>
			<tr>
				<td><strong>Order No:</strong></td>
				<td colspan="5"><? echo $po_no; ?></td>
			</tr>
			<tr>
				<td><strong>Remarks:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			<tr>
				<td colspan="3" id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center">
				<th width="20">SL</th>
				<th width="50">Basis</th>
				<th width="80">Prog/Book/ PI No</th>
				<th width="130">Item Description</th>
				<th width="50">Stich Length</th>
				<th width="40">GSM</th>
				<th width="40">Fin. Dia</th>
				<th width="70">Color</th>
				<th width="40">No of Roll</th>
				<th width="40">UOM</th>
				<th width="50">Count</th>
				<th width="50">Brand</th>
				<th width="50">Yarn Lot</th>
				<th width="30">Rack</th>
				<th width="30">Shelf</th>
				<th>Issue Qty</th>
			</thead>
			<?
			//$roll_arr=return_library_array( "select id, roll_no from pro_roll_details where entry_form in(2,22)", "id", "roll_no");
			$i = 1;
			$tot_qty = 0;
			/*$sql = "SELECT a.basis, a.program_no, a.prod_id, sum(a.issue_qnty) as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot,
			a.yarn_count, a.rack, a.self, a.brand_id, count(b.roll_id) as no_of_roll
			from inv_grey_fabric_issue_dtls a, pro_roll_details b
			where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
			and b.is_deleted=0
			group by a.basis, a.program_no, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id";*/

			$sql = "SELECT a.basis, a.program_no, a.prod_id, sum(b.qnty) as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, count(b.roll_id) as no_of_roll
			, c.id, detarmination_id as deter_id, c.gsm, c.dia_width, c.unit_of_measure as uom
			from inv_grey_fabric_issue_dtls a, pro_roll_details b, product_details_master c
			where a.id=b.dtls_id and a.prod_id=c.id and a.mst_id=$update_id and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.item_category_id=13
			group by a.basis, a.program_no, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id , c.id, detarmination_id, c.gsm, c.dia_width, c.unit_of_measure";
			//echo $sql;
			$result = sql_select($sql);
			foreach ($result as $row) {
				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
				}

				if ($row[csf('basis')] == 1) $pi_book_plan = $pi_arr[$row[csf("program_no")]];
				else if ($row[csf('basis')] == 2) $pi_book_plan = $booking_arr[$row[csf("program_no")]];
				else if ($row[csf('basis')] == 3) $pi_book_plan = $row[csf("program_no")];
				else if ($row[csf('basis')] == 9) $pi_book_plan = $production_arr[$row[csf("program_no")]];
				else $pi_book_plan = "&nbsp;";
			?>
				<tr>
					<td><? echo $i; ?></td>
					<td><? echo $grey_issue_basis[$row[csf('basis')]]; ?></td>
					<td style="word-break:break-all;"><? echo $pi_book_plan; ?></td>
					<td><? echo $composition_arr[$row[csf("deter_id")]]; ?></td>
					<td><? echo $row[csf('stitch_length')]; ?></td>
					<td style="word-break:break-all;"><? echo $row[csf("gsm")]; ?></td>
					<td style="word-break:break-all;"><? echo $row[csf("dia_width")]; ?></td>
					<td style="word-break:break-all;"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $row[csf("no_of_roll")]; ?></td>
					<td style="word-break:break-all;"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
					<td style="word-break:break-all;"><? echo $count; ?></td>
					<td style="word-break:break-all;"><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
					<td style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
					<td style="word-break:break-all;"><? echo $floor_room_rack_array[$row[csf('rack')]]; ?></td>
					<td style="word-break:break-all;"><? echo $floor_room_rack_array[$row[csf('self')]]; ?></td>
					<td align="right"><? echo number_format($row[csf('issue_qnty')], 2); ?></td>
				</tr>
			<?
				$tot_qty += $row[csf('issue_qnty')];
				$i++;
			}

			?>
			<tr>
				<td align="right" colspan="15"><strong>Total</strong></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
			</tr>
		</table>
	</div>
	<? echo signature_table(124, $company, "930px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "fabric_details_print_bpkw") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_array = get_company_array();
	$color_arr = get_color_array();
	$supplier_arr = get_supplier_array();
	$buyer_array = get_buyer_array();
	$yarn_count_details = get_yarn_count_array();
	$brand_arr = get_brand_array();
	$country_arr = get_country_array();
	$batch_arr = get_batch_array();

	$booking_arr = return_library_array("select id, booking_no_prefix_num from wo_booking_mst where item_category in(2,13)", "id", "booking_no_prefix_num");
	$pi_arr = return_library_array("select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");
	$production_arr = return_library_array("select id, sys_number_prefix_num from pro_grey_prod_delivery_mst where entry_form=56", "id", "sys_number_prefix_num");

	/*
	|--------------------------------------------------------------------------
	| for floor, room, rack and shelf disable
	|--------------------------------------------------------------------------
	|
	*/
	$floorRoomRackShelf_array = return_library_array("SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst WHERE company_id=" . $company . "", "floor_room_rack_id", "floor_room_rack_name");

	$dataArray = sql_select("select issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no,remarks from inv_issue_master where id=$update_id");
	$get_roll_po_id = sql_select("select po_breakdown_id from pro_roll_details where mst_id=$update_id");
	$poIDS = "";
	foreach ($get_roll_po_id as $row) {
		$poIDS .= $row[csf('po_breakdown_id')] . ",";
	}
	$poIDS = chop($poIDS, ",");

	/*$job_array=array();
	$job_sql="select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
	}*/

	$data_array = sql_select("SELECT id as po_id,job_no, sales_booking_no, buyer_id,within_group,style_ref_no FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0");
	$sales_arr = array();
	foreach ($data_array as $row) {
		$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
		$sales_arr[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_id")];
		$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
		$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
		$sales_arr[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
	}

	$data_array = sql_select("select b.id, a.style_ref_no, a.job_no, a.buyer_name,b.file_no,b.po_number, b.grouping,c.booking_no from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and b.id in($poIDS)");
	$po_details_arr = array();
	$job_array = array();
	foreach ($data_array as $row) {
		$po_details_arr[$row[csf("booking_no")]]['job_no'] = $row[csf("job_no")];
		$po_details_arr[$row[csf("booking_no")]]['buyer_name'] = $row[csf("buyer_name")];
		$po_details_arr[$row[csf("booking_no")]]['style_ref_no'] = $row[csf("style_ref_no")];
		$po_details_arr[$row[csf("booking_no")]]['file_no'] = $row[csf("file_no")];
		$po_details_arr[$row[csf("booking_no")]]['grouping'] = $row[csf("grouping")];

		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
	}

	$product_array = array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach ($product_sql as $row) {
		$product_array[$row[csf("id")]]['gsm'] = $row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width'] = $row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id'] = $row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom'] = $row[csf("unit_of_measure")];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$grey_issue_basis = array(0 => "Independent", 1 => "PI", 2 => "Booking", 3 => "Knitting Plan", 9 => "Delivery");
	$sql_barcode = "SELECT  b.barcode_no from inv_grey_fabric_issue_dtls a, pro_roll_details b					where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1	and b.is_deleted=0";
	foreach (sql_select($sql_barcode) as $key => $value) {
		$baroce_arrs[$value[csf("barcode_no")]] = $value[csf("barcode_no")];
	}

	if ($db_type == 0) {
		$po_id = return_field_value("group_concat(po_breakdown_id) as po_id", "inv_grey_fabric_issue_dtls a, order_wise_pro_details b", "a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and po_breakdown_id in($poIDS)", "po_id");
	} else {
		$po_id = return_field_value("LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id", "inv_grey_fabric_issue_dtls a, order_wise_pro_details b", "a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($poIDS)", "po_id");
	}
	// echo $po_id;
	$sales_data = sql_select("select po_breakdown_id,mst_id,is_sales,barcode_no from pro_roll_details where mst_id=$update_id and entry_form=61 and status_active=1 and is_deleted=0");
	$sales_data_arr = array();
	foreach ($sales_data as $row) {
		$sales_data_arr[$row[csf('po_breakdown_id')]]['is_sales'] = $row[csf('is_sales')];
	}
	$po_exp = array_unique(explode(',', $po_id));
	$all_po_string = implode(",", $po_exp);
	$po_no = '';
	$job = '';
	$style_ref = '';
	$all_buyer = "";
	foreach ($po_exp as $id) {
		$is_sales = $sales_data_arr[$id]['is_sales'];
		$sales_booking = $sales_arr[$id]['sales_booking_no'];
		$within_group = $sales_arr[$id]['within_group'];
		if ($is_sales == 1) {
			if ($within_group == 1) {
				if ($po_no == '') $po_no = $sales_arr[$id]['sales_order'];
				else $po_no .= ', ' . $sales_arr[$id]['sales_order'];
				if ($job == '') $job = $po_details_arr[$sales_booking]['job_no'];
				else $job .= ',' . $po_details_arr[$sales_booking]['job_no'];
				if ($style_ref == '') $style_ref = $po_details_arr[$sales_booking]['style_ref_no'];
				else $style_ref .= ',' . $po_details_arr[$sales_booking]['style_ref_no'];
				$all_buyer .= $buyer_array[$po_details_arr[$sales_booking]['buyer_name']] . ",";
			} else {
				if ($po_no == '') $po_no = $sales_arr[$id]['sales_order'];
				else $po_no .= ', ' . $sales_arr[$id]['sales_order'];
				if ($job == '') $job = "";
				if ($style_ref == '') $style_ref = $sales_arr[$id]['style_ref_no'];
				else $style_ref .= ',' . $sales_arr[$id]['style_ref_no'];
				$all_buyer .= $buyer_array[$sales_arr[$id]['buyer_name']] . ",";
			}
		} else {
			if ($po_no == '') $po_no = $job_array[$id]['po'];
			else $po_no .= ', ' . $job_array[$id]['po'];
			if ($job == '') $job = $job_array[$id]['job'];
			else $job .= ',' . $job_array[$id]['job'];
			if ($style_ref == '') $style_ref = $job_array[$id]['style_ref'];
			else $style_ref .= ',' . $job_array[$id]['style_ref'];
			$all_buyer .= $buyer_array[$job_array[$id]['buyer_name']] . ",";
		}
	}
	$job = implode(",", array_unique(explode(',', $job)));
	$style_ref = implode(",", array_unique(explode(',', $style_ref)));
	$po_no = implode(",", array_unique(explode(',', $po_no)));
	$all_buyer = implode(",", array_unique(explode(',', chop($all_buyer, ","))));
	if ($all_po_string != "") {
		$all_barcode_id = implode(",", $baroce_arrs);
		$knit_prod_rcv_sql = sql_select("select b.barcode_no, a.machine_dia, a.machine_gg, a.body_part_id from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.roll_id=0 and b.barcode_no in($all_barcode_id)");
		$production_rcv_data = array();
		foreach ($knit_prod_rcv_sql as $row) {
			$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
			$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
			$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] = $body_part[$row[csf("body_part_id")]];
		}
	} else {
		$knit_prod_rcv_sql = sql_select("select b.barcode_no, a.machine_dia, a.machine_gg, a.body_part_id from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.roll_id=0 and booking_without_order=1");
		$production_rcv_data = array();
		foreach ($knit_prod_rcv_sql as $row) {
			$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
			$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
			$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] = $body_part[$row[csf("body_part_id")]];
		}
		$sam_buyer_sql = sql_select("select a.buyer_id from  wo_non_ord_samp_booking_mst a, pro_roll_details b where a.id=b.po_breakdown_id and b.booking_without_order=1 and b.entry_form=61 and b.mst_id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($sam_buyer_sql as $row) {
			$all_buyer .= $buyer_array[$row[csf('buyer_id')]] . ",";
		}
		$all_buyer = implode(",", array_unique(explode(',', chop($all_buyer, ","))));
	}

?>
	<div>
		<table width="1230" cellspacing="0">
			<tr>
				<td colspan="9" align="center" style="font-size:22px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="9" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="9" align="center" style="font-size:18px"><strong><u><? echo $data[3]; ?> Grey Fabric Roll Issue Challan</u></strong></td>
			</tr>
			<tr>
				<td width="150" style="font-size:16px"><strong>Dyeing Company</strong> </td>
				<td width="10"><strong>:</strong></td>
				<td width="250" style="font-size:18px; font-weight:bold;"><? if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
																			else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]]; ?></td>

				<td width="150" style="font-size:16px; font-weight:bold;"><strong>Issue ID</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="250px" style="font-size:18px; font-weight:bold;"><? echo $txt_issue_no; ?></td>

				<td width="150"><strong>Issue Date</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="250px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>

				<td><strong>Issue Purpose</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>

				<td><strong>Batch Number</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Job No</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $job; ?></td>

				<td><strong>Style Ref.</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $style_ref; ?></td>

				<td colspan="3"></td>
			</tr>
			<tr>
				<td><strong>Buyer Name</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $all_buyer; ?></td>

				<td><strong>Order No</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $po_no; ?></td>

				<td colspan="3"></td>
			</tr>
			<tr>
				<td><strong>Remarks</strong></td>
				<td><strong>:</strong></td>
				<td colspan="7" width="175px"><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			<tr>
				<td colspan="9" id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1540" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center">
				<th width="20">SL</th>
				<th width="50">Basis</th>
				<th width="80">Deli. / Book/ PI No</th>
				<th width="100">Prog/Booking No</th>
				<th width="150">Item Description</th>
				<th width="110">Body Part</th>
				<th width="50">Stich Length</th>
				<th width="40">GSM</th>
				<th width="40">Fin. Dia</th>
				<th width="60">M/C Dia</th>
				<th width="60">M/C Gauge</th>
				<th width="100">Color</th>
				<th width="40">No of Roll</th>
				<th width="40">UOM</th>
				<th width="50">Count</th>
				<th width="50">Brand</th>
				<th width="50">Yarn Lot</th>
				<th width="50">Qty. In Pcs</th>
				<th width="50">Issue Qty</th>
				<th width="70">Floor</th>
				<th width="70">Room</th>
				<th width="70">Rack</th>
				<th width="70">Shelf</th>
				<th>Bin/Box</th>
			</thead>
			<?
			//$roll_arr=return_library_array( "select id, roll_no from pro_roll_details where entry_form in(2,22)", "id", "roll_no");
			$i = 1;
			$tot_qty = 0;
			$tot_qtyInPcs = 0;
			$program_arr = array();
			$program_sql = "select a.id,b.booking_no from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id = b.id and a.status_active=1 and a.is_deleted=0";
			$program_result = sql_select($program_sql);
			foreach ($program_result as $program_row) {
				$program_arr[$program_row[csf("id")]]["program_no"] = $program_row[csf("id")];
				$program_arr[$program_row[csf("id")]]["booking_no"] = $program_row[csf("booking_no")];
			}

			$bookingBarNo_sql = sql_select("select barcode_no, receive_basis from pro_roll_details where entry_form=2 and is_deleted=0 and status_active=1 and po_breakdown_id in($poIDS) ");
			foreach ($bookingBarNo_sql as $row) {
				$bookingBar_data[$row[csf("barcode_no")]]["receive_basis"] = $row[csf("receive_basis")];
			}
			// echo "<pre>";
			//print_r($bookingBar_data);

			if ($db_type == 0) {
				$sql_data = "SELECT a.basis, group_concat(a.program_no) as program_no,group_concat(b.booking_no) as booking_no, a.prod_id, sum(b.qnty) as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self, a.bin_box, a.brand_id, count(b.roll_id) as no_of_roll, group_concat(b.barcode_no) as barcode_no, sum(b.qc_pass_qnty_pcs) as qty_in_pcs,d.sys_number_prefix_num as delivery_prefix
                from inv_grey_fabric_issue_dtls a, pro_roll_details b left join pro_roll_details c on b.barcode_no=c.barcode_no  and c.entry_form=56 left join pro_grey_prod_delivery_mst d on c.mst_id=d.id
                where a.id=b.dtls_id and a.mst_id=$update_id and b.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                group by b.booking_no,a.basis, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self, a.bin_box, a.brand_id,d.sys_number_prefix_num";
			} else {
				/*$sql = "select a.basis, listagg(cast(a.program_no as varchar(4000)),',') within group (order by a.program_no) as program_no, a.prod_id, sum(b.qnty) as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self, count(b.roll_id) as no_of_roll, sum(b.qc_pass_qnty_pcs) as qty_in_pcs,
                listagg(cast(a.brand_id as varchar(4000)),',') within group (order by a.brand_id) as brand_id,
                listagg(cast(b.barcode_no as varchar(4000)),',') within group (order by b.barcode_no) as barcode_no,
                listagg(cast(b.booking_no as varchar(4000)),',') within group (order by b.booking_no) as booking_no,d.sys_number_prefix_num as delivery_prefix,e.booking_no as prog
                from inv_grey_fabric_issue_dtls a, pro_roll_details b left join pro_roll_details c on b.barcode_no=c.barcode_no  and c.entry_form=56 left join pro_grey_prod_delivery_mst d on c.mst_id=d.id left join pro_roll_details e on b.barcode_no=e.barcode_no  and e.entry_form=2
                where a.id=b.dtls_id and a.mst_id=$update_id and b.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                group by b.booking_no, a.basis, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self,d.sys_number_prefix_num,e.booking_no";*/

				$sql_data = "SELECT a.basis, a.program_no, a.prod_id, sum(b.qnty) as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self, a.bin_box, count(b.roll_id) as no_of_roll, sum(b.qc_pass_qnty_pcs) as qty_in_pcs, a.brand_id,b.barcode_no,b.booking_no, b.po_breakdown_id,b.booking_without_order, b.is_sales
                from inv_grey_fabric_issue_dtls a, pro_roll_details b
                where a.id=b.dtls_id and a.mst_id=$update_id and b.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                group by b.booking_no, a.basis, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self, a.bin_box,b.po_breakdown_id,b.booking_without_order,a.program_no,a.brand_id,b.barcode_no,b.booking_no, b.is_sales ";
			}
			// echo $sql_data;
			$booking_with_order_ids = "";
			$booking_without_order_ids = "";
			$barcodeNos = "";
			$fso_booking_with_order_ids = "";
			$result_data = sql_select($sql_data);
			foreach ($result_data as $row) {
				$barcodeNos .= $row[csf("barcode_no")] . ",";

				if ($row[csf("booking_without_order")] == 1) {
					$booking_without_order_ids .= $row[csf("po_breakdown_id")] . ",";
				} else if ($row[csf("is_sales")] == 1) {
					$fso_booking_with_order_ids .= $row[csf("po_breakdown_id")] . ",";
				} else {
					$booking_with_order_ids .= $row[csf("po_breakdown_id")] . ",";
				}
			}
			$barcodeNos = chop($barcodeNos, ",");
			$booking_with_order_ids = implode(",", array_unique(array_filter(explode(",", chop($booking_with_order_ids, ",")))));
			$fso_booking_with_order_ids = implode(",", array_unique(array_filter(explode(",", chop($fso_booking_with_order_ids, ",")))));
			$booking_without_order_ids = implode(",", array_unique(array_filter(explode(",", chop($booking_without_order_ids, ",")))));

			$sql_chk_trnsf = sql_select("select a.barcode_no   from PRO_ROLL_DETAILS a, INV_ITEM_TRANSFER_MST b
			where a.mst_id=b.id and a.barcode_no in($barcodeNos)
			and a.entry_form in(82,83) and b.transfer_criteria in(1,4)
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach ($sql_chk_trnsf as $rows) {
				$trnsf_chk_arr[$rows[csf("barcode_no")]] = $rows[csf("barcode_no")];
			}

			$sql_booking_withorder = sql_select("select po_break_down_id,booking_no from wo_booking_dtls  where po_break_down_id in($booking_with_order_ids) and status_active=1 and is_deleted=0 and booking_type=1");
			foreach ($sql_booking_withorder as $rows) {
				$booking_data_arr[$rows[csf("po_break_down_id")]] = $rows[csf("booking_no")];
			}

			$sql_fso_booking_withorder = sql_select("SELECT id,sales_booking_no from fabric_sales_order_mst  where id in($fso_booking_with_order_ids) and status_active=1 and is_deleted=0");
			foreach ($sql_fso_booking_withorder as $rows) {
				$booking_data_arr[$rows[csf("id")]] = $rows[csf("sales_booking_no")];
			}

			$sql_booking_without_order = sql_select("select id,booking_no from wo_non_ord_samp_booking_mst  where id in($booking_without_order_ids) and status_active=1 and is_deleted=0");
			foreach ($sql_booking_without_order as $rows) {
				$booking_data_arr[$rows[csf("id")]] = $rows[csf("booking_no")];
			}

			$sql_delivery = sql_select("select a.barcode_no,b.sys_number_prefix_num as delivery_prefix from pro_roll_details a,pro_grey_prod_delivery_mst b where a.mst_id=b.id and a.barcode_no in($barcodeNos) and a.entry_form=56 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach ($sql_delivery as $row) {
				$delivery_data_arr[$row[csf("barcode_no")]] = $row[csf("delivery_prefix")];
			}
			$sql_production = sql_select("select a.barcode_no,b.booking_no as program_no,c.machine_dia,c.machine_gg ,c.body_part_id ,e.booking_no from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c,ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e where a.mst_id=b.id  and a.dtls_id=c.id and b.id=c.mst_id and b.booking_id=d.id and d.mst_id=e.id and a.barcode_no in($barcodeNos) and b.receive_basis=2 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			union all  select a.barcode_no,null as program_no,c.machine_dia,c.machine_gg ,c.body_part_id ,b.booking_no from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c where a.mst_id=b.id  and a.dtls_id=c.id and b.id=c.mst_id and a.barcode_no in($barcodeNos) and b.receive_basis=1 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

			foreach ($sql_production as $row) {
				$production_data_arr[$row[csf("barcode_no")]] = $row[csf("program_no")];

				$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
				$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
				$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] = $body_part[$row[csf("body_part_id")]];
				$production_rcv_data[$row[csf("barcode_no")]]["progBooking"] = $row[csf("booking_no")];
			}

			$booking_number = "";
			foreach ($result_data as $row) {
				if ($row[csf("booking_without_order")] == 0) {
					if ($trnsf_chk_arr[$row[csf("barcode_no")]]) {
						$booking_number = $booking_data_arr[$row[csf("po_breakdown_id")]];
					} else if ($row[csf("is_sales")] == 1) {
						$booking_number = $booking_data_arr[$row[csf("po_breakdown_id")]];
					} else {
						$booking_number = $production_rcv_data[$row[csf("barcode_no")]]["progBooking"];
					}
				} else {
					$booking_number = $booking_data_arr[$row[csf("po_breakdown_id")]];
				}
				$data_arr_string = $row[csf("basis")] . "*" . $production_data_arr[$row[csf("barcode_no")]] . "*" . $booking_number . "*" . $delivery_data_arr[$row[csf("barcode_no")]] . "*" . $row[csf("prod_id")] . "*" . $row[csf("color_id")] . "*" . $row[csf("stitch_length")] . "*" . $row[csf("yarn_lot")] . "*" . $row[csf("yarn_count")] . "*" . $row[csf("floor_id")] . "*" . $row[csf("room")] . "*" . $row[csf("rack")] . "*" . $row[csf("self")] . "*" . $row[csf("bin_box")];
				$main_arr[$data_arr_string]['brand'] .= $row[csf("brand_id")] . ",";
				$main_arr[$data_arr_string]['issue_qnty'] += $row[csf("issue_qnty")];
				$main_arr[$data_arr_string]['no_of_roll'] += $row[csf("no_of_roll")];
				$main_arr[$data_arr_string]['qty_in_pcs'] += $row[csf("qty_in_pcs")];

				$main_arr[$data_arr_string]['machine_dia'] .= $production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] . ",";
				$main_arr[$data_arr_string]['machine_gg'] .= $production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] . ",";
				$main_arr[$data_arr_string]['body_part_id'] .= $production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] . ",";
			}

			foreach ($main_arr as $stringData => $row) {
				$stringDataArr = explode("*", $stringData);

				$basis = $stringDataArr[0];
				$pi_book_plan = $stringDataArr[3];
				$booking_no = $stringDataArr[2];
				$programNO = $stringDataArr[1];
				$bookingAndProg = $booking_no . "<br />" . $programNO;
				$fabric_des = $composition_arr[$product_array[$stringDataArr[4]]['deter_id']];
				$body_part_name = $row["body_part_id"];
				$body_part_name = implode(",", array_unique(array_filter(explode(",", chop($body_part_name, ",")))));


				$stitch_length = $stringDataArr[6];
				$gsm = $product_array[$stringDataArr[4]]['gsm'];
				$fin_dia = $product_array[$stringDataArr[4]]['dia_width'];


				$machine_dia = implode(",", array_unique(array_filter(explode(",", chop($row["machine_dia"], ",")))));
				$machine_gg = implode(",", array_unique(array_filter(explode(",", chop($row["machine_gg"], ",")))));



				$color_id = array_unique(array_filter(explode(",", chop($stringDataArr[5], ","))));

				$color = '';
				foreach ($color_id as $val) {
					if ($color == "") {
						$color .= $color_arr[$val];
					} else {
						$color .= "," . $color_arr[$val];
					}
				}

				$no_of_roll = $row["no_of_roll"];
				$uom = $product_array[$stringDataArr[4]]['uom'];
				$brand_id = $row["brand"];

				$brand_id_arr = array_unique(explode(",", $brand_id));
				$brand = '';
				foreach ($brand_id_arr as $val) {
					if ($brand == "") {
						$brand .= $brand_arr[$val];
					} else {
						$brand .= "," . $brand_arr[$val];
					}
				}

				$count = '';
				$yarn_count = explode(",", $stringDataArr[8]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
				}


				$yarn_lot = $stringDataArr[7];

				$qty_in_pcs = $row["qty_in_pcs"];
				$issue_qnty = $row["issue_qnty"];
				$floor = $floorRoomRackShelf_array[$stringDataArr[9]];
				$room = $floorRoomRackShelf_array[$stringDataArr[10]];
				$rack = $floorRoomRackShelf_array[$stringDataArr[11]];
				$shelf = $floorRoomRackShelf_array[$stringDataArr[12]];
				$bin = $floorRoomRackShelf_array[$stringDataArr[13]];

			?>

				<tr>
					<td><? echo $i; ?></td>
					<td><? echo $grey_issue_basis[$basis]; ?></td>
					<td style="word-break:break-all;"><? echo $pi_book_plan; ?></td>
					<td style="font-size:12px;"><? echo $bookingAndProg; ?></td>
					<td><? echo $fabric_des; ?></td>
					<td style="word-break:break-all;"><? echo $body_part_name; ?></td>
					<td><? echo $stitch_length; ?></td>
					<td style="word-break:break-all;"><? echo $gsm; ?></td>
					<td style="word-break:break-all;"><? echo $fin_dia; ?></td>
					<td style="word-break:break-all;"><? echo $machine_dia; ?></td>
					<td style="word-break:break-all;"><? echo $machine_gg; ?></td>
					<td style="word-break:break-all;"><? echo $color; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $no_of_roll; ?></td>
					<td style="word-break:break-all;"><? echo $unit_of_measurement[$uom]; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $count; //$row[csf('yarn_count')]; 
																		?></td>
					<td style="word-break:break-all;"><? echo $brand; //$brand_arr[$row[csf("brand_id")]]; 
														?></td>
					<td style="word-break:break-all;"><? echo $yarn_lot; ?></td>
					<td align="right"><? echo $qty_in_pcs; ?></td>
					<td align="right"><? echo number_format($issue_qnty, 2); ?></td>
					<td style="word-break:break-all;"><? echo $floor; ?></td>
					<td style="word-break:break-all;"><? echo $room; ?></td>
					<td style="word-break:break-all;"><? echo $rack; ?></td>
					<td style="word-break:break-all;"><? echo $shelf; ?></td>
					<td style="word-break:break-all;"><? echo $bin; ?></td>
				</tr>
			<?
				$tot_qty += $issue_qnty;
				$total_roll += $no_of_roll;
				$tot_qtyInPcs += $qty_in_pcs;
				$i++;
			}
			?>
			<tr>
				<td align="right" colspan="12"><strong>Total</strong></td>
				<td align="center"><? echo $total_roll; ?></td>
				<td colspan="4"></td>
				<td align="right"><? echo $tot_qtyInPcs; ?></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>

		</table>
	</div>
	<? echo signature_table(124, $company, "1470px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "fabric_details_print_bpkw_gin6") // GIN-6 for hams // Tipu
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_array = get_company_array();
	$color_arr = get_color_array();
	$supplier_arr = get_supplier_array();
	$buyer_array = get_buyer_array();
	$yarn_count_details = get_yarn_count_array();
	$brand_arr = get_brand_array();
	$country_arr = get_country_array();
	$batch_arr = get_batch_array();

	$booking_arr = return_library_array("select id, booking_no_prefix_num from wo_booking_mst where item_category in(2,13)", "id", "booking_no_prefix_num");
	$pi_arr = return_library_array("select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");
	$production_arr = return_library_array("select id, sys_number_prefix_num from pro_grey_prod_delivery_mst where entry_form=56", "id", "sys_number_prefix_num");

	/*
	|--------------------------------------------------------------------------
	| for floor, room, rack and shelf disable
	|--------------------------------------------------------------------------
	|
	*/
	$floorRoomRackShelf_array = return_library_array("SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst WHERE company_id=" . $company . "", "floor_room_rack_id", "floor_room_rack_name");

	$dataArray = sql_select("select issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no,remarks from inv_issue_master where id=$update_id");
	$get_roll_po_id = sql_select("select po_breakdown_id from pro_roll_details where mst_id=$update_id");
	$poIDS = "";
	foreach ($get_roll_po_id as $row) {
		$poIDS .= $row[csf('po_breakdown_id')] . ",";
	}
	$poIDS = chop($poIDS, ",");

	/*$job_array=array();
	$job_sql="select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
	}*/

	$data_array = sql_select("SELECT id as po_id,job_no, sales_booking_no, buyer_id,within_group,style_ref_no FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0");
	$sales_arr = array();
	foreach ($data_array as $row) {
		$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
		$sales_arr[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_id")];
		$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
		$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
		$sales_arr[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
	}

	$data_array = sql_select("select b.id, a.style_ref_no, a.job_no, a.buyer_name,b.file_no,b.po_number, b.grouping,c.booking_no from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and b.id in($poIDS)");
	$po_details_arr = array();
	$job_array = array();
	foreach ($data_array as $row) {
		$po_details_arr[$row[csf("booking_no")]]['job_no'] = $row[csf("job_no")];
		$po_details_arr[$row[csf("booking_no")]]['buyer_name'] = $row[csf("buyer_name")];
		$po_details_arr[$row[csf("booking_no")]]['style_ref_no'] = $row[csf("style_ref_no")];
		$po_details_arr[$row[csf("booking_no")]]['file_no'] = $row[csf("file_no")];
		$po_details_arr[$row[csf("booking_no")]]['grouping'] = $row[csf("grouping")];

		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
	}

	$product_array = array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach ($product_sql as $row) {
		$product_array[$row[csf("id")]]['gsm'] = $row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width'] = $row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id'] = $row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom'] = $row[csf("unit_of_measure")];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$grey_issue_basis = array(0 => "Independent", 1 => "PI", 2 => "Booking", 3 => "Knitting Plan", 9 => "Delivery");
	$sql_barcode = "SELECT  b.barcode_no from inv_grey_fabric_issue_dtls a, pro_roll_details b					where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1	and b.is_deleted=0";
	foreach (sql_select($sql_barcode) as $key => $value) {
		$baroce_arrs[$value[csf("barcode_no")]] = $value[csf("barcode_no")];
	}

	if ($db_type == 0) {
		$po_id = return_field_value("group_concat(po_breakdown_id) as po_id", "inv_grey_fabric_issue_dtls a, order_wise_pro_details b", "a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and po_breakdown_id in($poIDS)", "po_id");
	} else {
		$po_id = return_field_value("LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id", "inv_grey_fabric_issue_dtls a, order_wise_pro_details b", "a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($poIDS)", "po_id");
	}
	// echo $po_id;
	$sales_data = sql_select("select po_breakdown_id,mst_id,is_sales,barcode_no from pro_roll_details where mst_id=$update_id and entry_form=61 and status_active=1 and is_deleted=0");
	$sales_data_arr = array();
	foreach ($sales_data as $row) {
		$sales_data_arr[$row[csf('po_breakdown_id')]]['is_sales'] = $row[csf('is_sales')];
	}
	$po_exp = array_unique(explode(',', $po_id));
	$all_po_string = implode(",", $po_exp);
	$po_no = '';
	$job = '';
	$style_ref = '';
	$all_buyer = "";
	foreach ($po_exp as $id) {
		$is_sales = $sales_data_arr[$id]['is_sales'];
		$sales_booking = $sales_arr[$id]['sales_booking_no'];
		$within_group = $sales_arr[$id]['within_group'];
		if ($is_sales == 1) {
			if ($within_group == 1) {
				if ($po_no == '') $po_no = $sales_arr[$id]['sales_order'];
				else $po_no .= ', ' . $sales_arr[$id]['sales_order'];
				if ($job == '') $job = $po_details_arr[$sales_booking]['job_no'];
				else $job .= ',' . $po_details_arr[$sales_booking]['job_no'];
				if ($style_ref == '') $style_ref = $po_details_arr[$sales_booking]['style_ref_no'];
				else $style_ref .= ',' . $po_details_arr[$sales_booking]['style_ref_no'];
				$all_buyer .= $buyer_array[$po_details_arr[$sales_booking]['buyer_name']] . ",";
			} else {
				if ($po_no == '') $po_no = $sales_arr[$id]['sales_order'];
				else $po_no .= ', ' . $sales_arr[$id]['sales_order'];
				if ($job == '') $job = "";
				if ($style_ref == '') $style_ref = $sales_arr[$id]['style_ref_no'];
				else $style_ref .= ',' . $sales_arr[$id]['style_ref_no'];
				$all_buyer .= $buyer_array[$sales_arr[$id]['buyer_name']] . ",";
			}
		} else {
			if ($po_no == '') $po_no = $job_array[$id]['po'];
			else $po_no .= ', ' . $job_array[$id]['po'];
			if ($job == '') $job = $job_array[$id]['job'];
			else $job .= ',' . $job_array[$id]['job'];
			if ($style_ref == '') $style_ref = $job_array[$id]['style_ref'];
			else $style_ref .= ',' . $job_array[$id]['style_ref'];
			$all_buyer .= $buyer_array[$job_array[$id]['buyer_name']] . ",";
		}
	}
	$job = implode(",", array_unique(explode(',', $job)));
	$style_ref = implode(",", array_unique(explode(',', $style_ref)));
	$po_no = implode(",", array_unique(explode(',', $po_no)));
	$all_buyer = implode(",", array_unique(explode(',', chop($all_buyer, ","))));
	if ($all_po_string != "") {
		$all_barcode_id = implode(",", $baroce_arrs);
		$knit_prod_rcv_sql = sql_select("select b.barcode_no, a.machine_dia, a.machine_gg, a.body_part_id from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.roll_id=0 and b.barcode_no in($all_barcode_id)");
		$production_rcv_data = array();
		foreach ($knit_prod_rcv_sql as $row) {
			$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
			$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
			$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] = $row[csf("body_part_id")];
		}
	} else {
		$knit_prod_rcv_sql = sql_select("select b.barcode_no, a.machine_dia, a.machine_gg, a.body_part_id from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.roll_id=0 and booking_without_order=1");
		$production_rcv_data = array();
		foreach ($knit_prod_rcv_sql as $row) {
			$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
			$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
			$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] = $row[csf("body_part_id")];
		}
		$sam_buyer_sql = sql_select("select a.buyer_id from  wo_non_ord_samp_booking_mst a, pro_roll_details b where a.id=b.po_breakdown_id and b.booking_without_order=1 and b.entry_form=61 and b.mst_id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($sam_buyer_sql as $row) {
			$all_buyer .= $buyer_array[$row[csf('buyer_id')]] . ",";
		}
		$all_buyer = implode(",", array_unique(explode(',', chop($all_buyer, ","))));
	}

?>
	<div>
		<?
		$i = 1;
		$tot_qty = 0;
		$tot_qtyInPcs = 0;
		$program_arr = array();
		$program_sql = "select a.id,b.booking_no from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id = b.id and a.status_active=1 and a.is_deleted=0";
		$program_result = sql_select($program_sql);
		foreach ($program_result as $program_row) {
			$program_arr[$program_row[csf("id")]]["program_no"] = $program_row[csf("id")];
			$program_arr[$program_row[csf("id")]]["booking_no"] = $program_row[csf("booking_no")];
		}

		$bookingBarNo_sql = sql_select("select barcode_no, receive_basis from pro_roll_details where entry_form=2 and is_deleted=0 and status_active=1 and po_breakdown_id in($poIDS) ");
		foreach ($bookingBarNo_sql as $row) {
			$bookingBar_data[$row[csf("barcode_no")]]["receive_basis"] = $row[csf("receive_basis")];
		}
		// echo "<pre>";
		//print_r($bookingBar_data);

		if ($db_type == 0) {
			$sql_data = "SELECT a.basis, group_concat(a.program_no) as program_no,group_concat(b.booking_no) as booking_no, a.prod_id, sum(b.qnty) as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self, a.bin_box, a.brand_id, count(b.roll_id) as no_of_roll, group_concat(b.barcode_no) as barcode_no, sum(b.qc_pass_qnty_pcs) as qty_in_pcs,d.sys_number_prefix_num as delivery_prefix
	            from inv_grey_fabric_issue_dtls a, pro_roll_details b left join pro_roll_details c on b.barcode_no=c.barcode_no  and c.entry_form=56 left join pro_grey_prod_delivery_mst d on c.mst_id=d.id
	            where a.id=b.dtls_id and a.mst_id=$update_id and b.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	            group by b.booking_no,a.basis, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self, a.bin_box, a.brand_id,d.sys_number_prefix_num";
		} else {
			/*$sql = "select a.basis, listagg(cast(a.program_no as varchar(4000)),',') within group (order by a.program_no) as program_no, a.prod_id, sum(b.qnty) as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self, count(b.roll_id) as no_of_roll, sum(b.qc_pass_qnty_pcs) as qty_in_pcs,
	            listagg(cast(a.brand_id as varchar(4000)),',') within group (order by a.brand_id) as brand_id,
	            listagg(cast(b.barcode_no as varchar(4000)),',') within group (order by b.barcode_no) as barcode_no,
	            listagg(cast(b.booking_no as varchar(4000)),',') within group (order by b.booking_no) as booking_no,d.sys_number_prefix_num as delivery_prefix,e.booking_no as prog
	            from inv_grey_fabric_issue_dtls a, pro_roll_details b left join pro_roll_details c on b.barcode_no=c.barcode_no  and c.entry_form=56 left join pro_grey_prod_delivery_mst d on c.mst_id=d.id left join pro_roll_details e on b.barcode_no=e.barcode_no  and e.entry_form=2
	            where a.id=b.dtls_id and a.mst_id=$update_id and b.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	            group by b.booking_no, a.basis, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self,d.sys_number_prefix_num,e.booking_no";*/

			$sql_data = "SELECT a.basis, a.program_no, a.prod_id, sum(b.qnty) as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self, a.bin_box, count(b.roll_id) as no_of_roll, sum(b.qc_pass_qnty_pcs) as qty_in_pcs,
	            a.brand_id,b.barcode_no,b.booking_no, b.po_breakdown_id,b.booking_without_order
	            from inv_grey_fabric_issue_dtls a, pro_roll_details b
	            where a.id=b.dtls_id and a.mst_id=$update_id and b.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	            group by b.booking_no, a.basis, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self, a.bin_box,b.po_breakdown_id,b.booking_without_order,a.program_no,a.brand_id,b.barcode_no,b.booking_no ";
		}
		$booking_with_order_ids = "";
		$booking_without_order_ids = "";
		$barcodeNos = "";
		$result_data = sql_select($sql_data);
		foreach ($result_data as $row) {
			$barcodeNos .= $row[csf("barcode_no")] . ",";

			if ($row[csf("booking_without_order")] == 1) {
				$booking_without_order_ids .= $row[csf("po_breakdown_id")] . ",";
			} else {
				$booking_with_order_ids .= $row[csf("po_breakdown_id")] . ",";
			}
		}
		$barcodeNos = chop($barcodeNos, ",");
		$booking_with_order_ids = implode(",", array_unique(array_filter(explode(",", chop($booking_with_order_ids, ",")))));
		$booking_without_order_ids = implode(",", array_unique(array_filter(explode(",", chop($booking_without_order_ids, ",")))));

		$sql_chk_trnsf = sql_select("select a.barcode_no   from PRO_ROLL_DETAILS a, INV_ITEM_TRANSFER_MST b
			where a.mst_id=b.id and a.barcode_no in($barcodeNos)
			and a.entry_form in(82,83) and b.transfer_criteria in(1,4)
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($sql_chk_trnsf as $rows) {
			$trnsf_chk_arr[$rows[csf("barcode_no")]] = $rows[csf("barcode_no")];
		}

		$sql_booking_withorder = sql_select("select po_break_down_id,booking_no from wo_booking_dtls  where po_break_down_id in($booking_with_order_ids) and status_active=1 and is_deleted=0 and booking_type=1");
		foreach ($sql_booking_withorder as $rows) {
			$booking_data_arr[$rows[csf("po_break_down_id")]] = $rows[csf("booking_no")];
		}



		$sql_booking_without_order = sql_select("select id,booking_no from wo_non_ord_samp_booking_mst  where id in($booking_without_order_ids) and status_active=1 and is_deleted=0");
		foreach ($sql_booking_without_order as $rows) {
			$booking_data_arr[$rows[csf("id")]] = $rows[csf("booking_no")];
		}

		$sql_delivery = sql_select("select a.barcode_no,b.sys_number_prefix_num as delivery_prefix from pro_roll_details a,pro_grey_prod_delivery_mst b where a.mst_id=b.id and a.barcode_no in($barcodeNos) and a.entry_form=56 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($sql_delivery as $row) {
			$delivery_data_arr[$row[csf("barcode_no")]] = $row[csf("delivery_prefix")];
		}
		$sql_production = sql_select("SELECT a.barcode_no,b.booking_no as program_no,c.machine_dia,c.machine_gg ,c.body_part_id ,e.booking_no
			from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c,ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e
			where a.mst_id=b.id  and a.dtls_id=c.id and b.id=c.mst_id and b.booking_id=d.id and d.mst_id=e.id and a.barcode_no in($barcodeNos) and b.receive_basis=2 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			union all
			select a.barcode_no,null as program_no,c.machine_dia,c.machine_gg ,c.body_part_id ,b.booking_no
			from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c
			where a.mst_id=b.id  and a.dtls_id=c.id and b.id=c.mst_id and a.barcode_no in($barcodeNos) and b.receive_basis=1 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

		foreach ($sql_production as $row) {
			$production_data_arr[$row[csf("barcode_no")]] = $row[csf("program_no")];

			$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
			$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
			$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] = $body_part[$row[csf("body_part_id")]];
			$production_rcv_data[$row[csf("barcode_no")]]["progBooking"] = $row[csf("booking_no")];
		}

		foreach ($result_data as $row) {
			if ($row[csf("booking_without_order")] == 0) {
				if ($trnsf_chk_arr[$row[csf("barcode_no")]]) {
					$booking_number = $booking_data_arr[$row[csf("po_breakdown_id")]];
				} else {
					$booking_number = $production_rcv_data[$row[csf("barcode_no")]]["progBooking"];
				}
			} else {
				// $booking_number='BKL-SMN-21-00206';
				$booking_number = $booking_data_arr[$row[csf("po_breakdown_id")]];
			}
		}
		?>
		<table width="1230" cellspacing="0">
			<tr>
				<td colspan="9" align="center" style="font-size:22px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="9" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="9" align="center" style="font-size:18px"><strong><u><? echo $data[3]; ?> Grey Fabric Roll Issue Challan</u></strong></td>
			</tr>
			<tr>
				<td width="150" style="font-size:16px"><strong>Dyeing Company</strong> </td>
				<td width="10"><strong>:</strong></td>
				<td width="250" style="font-size:18px; font-weight:bold;"><? if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
																			else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]]; ?></td>

				<td width="150" style="font-size:16px; font-weight:bold;"><strong>Issue ID</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="250px" style="font-size:18px; font-weight:bold;"><? echo $txt_issue_no; ?></td>

				<td width="150"><strong>Issue Date</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="250px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>

				<td><strong>Issue Purpose</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>

				<td><strong>Batch Number</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Job No</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $job; ?></td>

				<td><strong>Style Ref.</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $style_ref; ?></td>

				<td><strong>Booking No</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $booking_number; ?></td </tr>
			<tr>
				<td><strong>Buyer Name</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $all_buyer; ?></td>

				<td><strong>Order No</strong></td>
				<td><strong>:</strong></td>
				<td><? echo $po_no; ?></td>

				<td colspan="3"></td>
			</tr>
			<tr>
				<td><strong>Remarks</strong></td>
				<td><strong>:</strong></td>
				<td colspan="7" width="175px"><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			<tr>
				<td colspan="9" id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1280" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center">
				<th width="20">SL</th>
				<th width="50">Basis</th>
				<th width="80">Deli. / Book/ PI No</th>
				<th width="100">Prog/Booking No</th>
				<th width="150">Item Description</th>
				<th width="110">Body Part</th>
				<th width="50">Stich Length</th>
				<th width="40">GSM</th>
				<th width="40">Fin. Dia</th>
				<th width="60">M/C Dia</th>
				<th width="60">M/C Gauge</th>
				<th width="100">Color</th>
				<th width="40">No of Roll</th>
				<th width="40">UOM</th>
				<th width="50">Count</th>
				<th width="50">Brand</th>
				<th width="50">Yarn Lot</th>
				<th width="50">Qty. In Pcs</th>
				<th>Issue Qty</th>
				<!--  <th width="70">Floor</th>
                <th width="70">Room</th>
                <th width="70">Rack</th>
                <th width="70">Shelf</th>
                <th>Bin/Box</th> -->
			</thead>
			<?
			//$roll_arr=return_library_array( "select id, roll_no from pro_roll_details where entry_form in(2,22)", "id", "roll_no");


			foreach ($result_data as $row) {
				if ($row[csf("booking_without_order")] == 0) {
					/*if($trnsf_chk_arr[$row[csf("barcode_no")]])
	            	{
	            		$booking_number=$booking_data_arr[$row[csf("po_breakdown_id")]];
	            	}
	            	else
	            	{
	            		$booking_number=$production_rcv_data[$row[csf("barcode_no")]]["progBooking"];
	            	}*/
					$booking_number = $production_rcv_data[$row[csf("barcode_no")]]["progBooking"];
				} else {
					// $booking_number='BKL-SMN-21-00206';
					$booking_number = $booking_data_arr[$row[csf("po_breakdown_id")]];
				}

				$data_arr_string = $row[csf("basis")] . "*" . $production_data_arr[$row[csf("barcode_no")]] . "*" . $booking_number . "*" . $delivery_data_arr[$row[csf("barcode_no")]] . "*" . $row[csf("prod_id")] . "*" . $row[csf("color_id")] . "*" . $row[csf("stitch_length")] . "*" . $row[csf("yarn_lot")] . "*" . $row[csf("yarn_count")] . "*" . $row[csf("floor_id")] . "*" . $row[csf("room")] . "*" . $row[csf("rack")] . "*" . $row[csf("self")] . "*" . $row[csf("bin_box")];
				$main_arr[$data_arr_string]['brand'] .= $row[csf("brand_id")] . ",";
				$main_arr[$data_arr_string]['issue_qnty'] += $row[csf("issue_qnty")];
				$main_arr[$data_arr_string]['no_of_roll'] += $row[csf("no_of_roll")];
				$main_arr[$data_arr_string]['qty_in_pcs'] += $row[csf("qty_in_pcs")];

				$main_arr[$data_arr_string]['machine_dia'] .= $production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] . ",";
				$main_arr[$data_arr_string]['machine_gg'] .= $production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] . ",";
				$main_arr[$data_arr_string]['body_part_id'] .= $production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] . ",";
			}


			foreach ($main_arr as $stringData => $row) {
				$stringDataArr = explode("*", $stringData);

				$basis = $stringDataArr[0];
				$pi_book_plan = $stringDataArr[3];
				$booking_no = $stringDataArr[2];
				$programNO = $stringDataArr[1];
				$bookingAndProg = $booking_no . "<br />" . $programNO;
				$fabric_des = $composition_arr[$product_array[$stringDataArr[4]]['deter_id']];
				$body_part_name = $row["body_part_id"];
				$body_part_name = implode(",", array_unique(array_filter(explode(",", chop($body_part_name, ",")))));


				$stitch_length = $stringDataArr[6];
				$gsm = $product_array[$stringDataArr[4]]['gsm'];
				$fin_dia = $product_array[$stringDataArr[4]]['dia_width'];


				$machine_dia = implode(",", array_unique(array_filter(explode(",", chop($row["machine_dia"], ",")))));
				$machine_gg = implode(",", array_unique(array_filter(explode(",", chop($row["machine_gg"], ",")))));



				$color_id = array_unique(array_filter(explode(",", chop($stringDataArr[5], ","))));

				$color = '';
				foreach ($color_id as $val) {
					if ($color == "") {
						$color .= $color_arr[$val];
					} else {
						$color .= "," . $color_arr[$val];
					}
				}

				$no_of_roll = $row["no_of_roll"];
				$uom = $product_array[$stringDataArr[4]]['uom'];
				$brand_id = $row["brand"];

				$brand_id_arr = array_unique(explode(",", $brand_id));
				$brand = '';
				foreach ($brand_id_arr as $val) {
					if ($brand == "") {
						$brand .= $brand_arr[$val];
					} else {
						$brand .= "," . $brand_arr[$val];
					}
				}

				$count = '';
				$yarn_count = explode(",", $stringDataArr[8]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
				}


				$yarn_lot = $stringDataArr[7];

				$qty_in_pcs = $row["qty_in_pcs"];
				$issue_qnty = $row["issue_qnty"];
				$floor = $floorRoomRackShelf_array[$stringDataArr[9]];
				$room = $floorRoomRackShelf_array[$stringDataArr[10]];
				$rack = $floorRoomRackShelf_array[$stringDataArr[11]];
				$shelf = $floorRoomRackShelf_array[$stringDataArr[12]];
				$bin = $floorRoomRackShelf_array[$stringDataArr[13]];

			?>

				<tr>
					<td><? echo $i; ?></td>
					<td><? echo $grey_issue_basis[$basis]; ?></td>
					<td style="word-break:break-all;"><? echo $pi_book_plan; ?></td>
					<td style="font-size:12px;"><? echo $bookingAndProg; ?></td>
					<td><? echo $fabric_des; ?></td>
					<td style="word-break:break-all;"><? echo $body_part_name; ?></td>
					<td><? echo $stitch_length; ?></td>
					<td style="word-break:break-all;"><? echo $gsm; ?></td>
					<td style="word-break:break-all;"><? echo $fin_dia; ?></td>
					<td style="word-break:break-all;"><? echo $machine_dia; ?></td>
					<td style="word-break:break-all;"><? echo $machine_gg; ?></td>
					<td style="word-break:break-all;"><? echo $color; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $no_of_roll; ?></td>
					<td style="word-break:break-all;"><? echo $unit_of_measurement[$uom]; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $count; //$row[csf('yarn_count')]; 
																		?></td>
					<td style="word-break:break-all;"><? echo $brand; //$brand_arr[$row[csf("brand_id")]]; 
														?></td>
					<td style="word-break:break-all;"><? echo $yarn_lot; ?></td>
					<td align="right"><? echo $qty_in_pcs; ?></td>
					<td align="right"><? echo number_format($issue_qnty, 2); ?></td>
					<!-- <td style="word-break:break-all;"><? //echo $floor; 
															?></td>
                    <td style="word-break:break-all;"><? //echo $room; 
														?></td>
                    <td style="word-break:break-all;"><? //echo $rack; 
														?></td>
                    <td style="word-break:break-all;"><? //echo $shelf; 
														?></td>
                    <td style="word-break:break-all;"><? //echo $bin; 
														?></td> -->
				</tr>
			<?
				$tot_qty += $issue_qnty;
				$total_roll += $no_of_roll;
				$tot_qtyInPcs += $qty_in_pcs;
				$i++;
			}
			?>
			<tr>
				<td align="right" colspan="12"><strong>Total</strong></td>
				<td align="center"><? echo $total_roll; ?></td>
				<td colspan="4"></td>
				<td align="right"><? echo $tot_qtyInPcs; ?></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
				<!--  <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td> -->
			</tr>
		</table>
	</div>
	<? echo signature_table(124, $company, "1470px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "fabric_details_print_bpkw_gin7") // GIN-7 for Charka TexTile
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$country_name_arr = return_library_array("select id, country_name from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name");
	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name, plot_no, road_no, city, contact_no, country_id from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$company_address_arr[$row[csf('id')]] = 'Plot No:' . $row[csf('plot_no')] . ', Road No:' . $row[csf('road_no')] . ', City / Town:' . $row[csf('city')] . ', Country:' . $country_name_arr[$row[csf('country_id')]] . ', Contact No:' . $row[csf('contact_no')];
	}

	//for supplier
	$sqlSupplier = sql_select("select id as id, supplier_name as supplier_name, short_name as short_name, address_1 from lib_supplier where status_active=1 and is_deleted=0");
	foreach ($sqlSupplier as $row) {
		$supplier_arr[$row[csf('id')]] = $row[csf('short_name')];
		$supplier_dtls_arr[$row[csf('id')]] = $row[csf('supplier_name')];
		$supplier_address_arr[$row[csf('id')]] = $row[csf('address_1')];
	}
	unset($sqlSupplier);

	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	//$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$booking_arr = return_library_array("select id, booking_no from wo_booking_mst where item_category in(2,13)", "id", "booking_no");
	$pi_arr = return_library_array("select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");

	$smn_booking_buyer_arr = return_library_array("select booking_no, buyer_id from wo_non_ord_samp_booking_mst", "booking_no", "buyer_id");
	$floorRoomRackSelfArr = return_library_array( "SELECT a.floor_room_rack_id, a.floor_room_rack_name FROM lib_floor_room_rack_mst a  WHERE a.status_active = 1 AND a.is_deleted = 0 AND a.company_id IN(".$company.")", 'floor_room_rack_id', 'floor_room_rack_name');


	// pro_roll_details 

	$lib_mc_arr = array();
	$mc_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($mc_sql as $row) {
		$lib_mc_arr[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
	}

	$dataArray = sql_select("select issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no, remarks, inserted_by,insert_date from inv_issue_master where id=$update_id");
	//print_r($dataArray);die;

	$inserted_by=$dataArray[0][csf('inserted_by')];
	$insert_date=$dataArray[0][csf('insert_date')];


	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$grey_issue_basis = array(0 => "Independent", 1 => "PI", 2 => "Booking", 3 => "Knitting Plan", 9 => "Delivery");
    ?>
	<div>
		<table width="1170" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[3]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:16px; font-weight:bold;"><strong>Dyeing Company:</strong> </td>
				<td width="320" style="font-size:18px; font-weight:bold;">
					<?
					$knit_dye_company_address = '';
					if ($dataArray[0][csf('knit_dye_source')] == 1)
						$knit_dye_company_address = $company_address_arr[$dataArray[0][csf('knit_dye_company')]];
					else
						$knit_dye_company_address = $supplier_address_arr[$dataArray[0][csf('knit_dye_company')]];

					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
				<td width="100"><strong>Issue ID :</strong></td>
				<td width="60" style="font-size:16px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
				<td width="80"><strong>Issue Date:</strong></td>
				<td width="80"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Address:</strong></td>
				<td><? echo $knit_dye_company_address; ?></td>
				<td><strong>Dyeing Source:</strong></td>
				<td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				<td><strong>Issue Purpose:</strong></td>
				<td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Batch Number:</strong></td>
				<td width="175px"><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
				<td><strong>Remarks:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			<tr>
				<td colspan="3" id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1170" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center" style="font-size:12px">
				<th width="20">SL</th>
				<th width="80">Textile Ref No</th>
				<th width="90">Job/Style</th>
				<th width="100">Buyer Name</th>
				<th width="50">Basis</th>
				<th width="100">Fabric Booking No</th>
				<th width="110">Item Description</th>
				<th width="50">Stich Length</th>
				<th width="50">GSM/ Fin. Dia</th>
				<th width="70">Dia X Gauge</th>
				<th width="70">Color</th>
				<th width="55">Brand /UOM</th>
				<th width="60">Count /Y. Lot/ Y. Type</th>
				<th width="60">K. Party</th>
				<th width="70">Rack/ Shelf</th>
				<th width="35">Total Roll</th>
				<th>Issue Qty</th>
			</thead>
			<?
			// inserted_by insert_date
			$i = 1;
			$tot_qty = 0;
			$sql = "SELECT a.basis, a.program_no, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, count(b.roll_no) as tot_roll, b.po_breakdown_id, sum(b.qnty) as quantity, b.booking_no, b.booking_without_order, b.is_sales, c.detarmination_id as deter_id, c.gsm, c.dia_width, c.unit_of_measure as uom, b.barcode_no , c.yarn_type, c.company_id , c.lot
			from inv_grey_fabric_issue_dtls a, pro_roll_details b, product_details_master c
			where a.id=b.dtls_id and a.prod_id=c.id and a.mst_id=$update_id and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			group by a.basis, a.program_no, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, b.po_breakdown_id, b.booking_no, b.booking_without_order,b.is_sales, c.detarmination_id, c.gsm, c.dia_width, c.unit_of_measure, b.barcode_no, c.yarn_type , c.company_id , c.lot";
			// echo $sql;
			$result = sql_select($sql);
			$yarnLot='';
			$booking_without_order_ids = $booking_with_order_ids = "";
			foreach ($result as $row) {
				$po_id_array[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
				$program_no_array[$row[csf('program_no')]] = $row[csf('program_no')];
				$roll_id_array[$row[csf('roll_id')]] = $row[csf('roll_id')];
				$barcodeNo_array[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
				$yarnLot .= "'".$row[csf('lot')]."',";

				if ($row[csf("booking_without_order")] == 1) {
					$booking_without_order_ids .= $row[csf("po_breakdown_id")] . ",";
				} else {
					$booking_with_order_ids .= $row[csf("po_breakdown_id")] . ",";
				}

				
			}

			$yarnLot=chop($yarnLot,",");

			$po_id_array = array_filter(array_unique($po_id_array));
			$program_no_array = array_filter(array_unique($program_no_array));
			$roll_id_array = array_filter(array_unique($roll_id_array));
			$barcodeNo_array = array_filter(array_unique($barcodeNo_array));

			//  echo $yarnLot;
			// die;

			 $yarnsql="SELECT lot,  yarn_type from product_details_master where company_id=$company and allocated_qnty >0 and item_category_id=1 and lot in ($yarnLot) and  status_active=1 and is_deleted=0 ";
			 $yarnsqlData = sql_select($yarnsql);

			 $yarntype_arr=array();

			 foreach($yarnsqlData as $row){

				$yarntype_arr[$row[csf("lot")]] =  $yarn_type[ (int) $row[csf("yarn_type")]] ;
			 }



			$booking_with_order_ids = implode(",", array_unique(array_filter(explode(",", chop($booking_with_order_ids, ",")))));
			$booking_without_order_ids = implode(",", array_unique(array_filter(explode(",", chop($booking_without_order_ids, ",")))));

			$sql_booking_withorder = sql_select("select po_break_down_id,booking_no from wo_booking_dtls  where po_break_down_id in($booking_with_order_ids) and status_active=1 and is_deleted=0 and booking_type=1");
			foreach ($sql_booking_withorder as $rows) {
				$booking_data_arr[$rows[csf("po_break_down_id")]] = $rows[csf("booking_no")];
			}



			$sql_booking_without_order = sql_select("select id,booking_no from wo_non_ord_samp_booking_mst  where id in($booking_without_order_ids) and status_active=1 and is_deleted=0");
			foreach ($sql_booking_without_order as $rows) {
				$booking_data_arr[$rows[csf("id")]] = $rows[csf("booking_no")];
			}

			if (count($barcodeNo_array) > 0) {
				$barcodeNos = implode(",", $barcodeNo_array);
				$barcode = $all_barcodeNo_cond = "";

				if ($db_type == 2 && count($barcodeNo_array) > 999) {
					$barcodeNo_arr_chunk = array_chunk($barcodeNo_array, 999);
					foreach ($barcodeNo_arr_chunk as $chunk_arr) {
						$barcode .= " a.barcode_no in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_barcodeNo_cond .= " and (" . chop($barcode, 'or ') . ")";
				} else {
					$all_barcodeNo_cond = " and a.barcode_no in($barcodeNos)";
				}


				$sql_production = sql_select("SELECT a.barcode_no,b.booking_no as program_no,c.machine_dia,c.machine_gg ,c.body_part_id ,e.booking_no
				from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c,ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e
				where a.mst_id=b.id  and a.dtls_id=c.id and b.id=c.mst_id and b.booking_id=d.id and d.mst_id=e.id $all_barcodeNo_cond and b.receive_basis=2 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				union all
				select a.barcode_no,null as program_no,c.machine_dia,c.machine_gg ,c.body_part_id ,b.booking_no
				from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c
				where a.mst_id=b.id  and a.dtls_id=c.id and b.id=c.mst_id $all_barcodeNo_cond and b.receive_basis=1 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

				foreach ($sql_production as $row) {
					$production_rcv_data[$row[csf("barcode_no")]]["progBooking"] = $row[csf("booking_no")];
				}
			}

			if (count($po_id_array) > 0) {
				$po_ids = implode(",", $po_id_array);
				$transBar = $all_po_id_cond = "";

				if ($db_type == 2 && count($po_id_array) > 999) {
					$po_id_arr_chunk = array_chunk($po_id_array, 999);
					foreach ($po_id_arr_chunk as $chunk_arr) {
						$transBar .= " b.id in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_po_id_cond .= " and (" . chop($transBar, 'or ') . ")";
				} else {
					$all_po_id_cond = " and b.id in($po_ids)";
				}

				$data_array = sql_select("SELECT b.id as po_id, b.job_no, b.sales_booking_no, b.buyer_id, b.within_group, b.style_ref_no FROM fabric_sales_order_mst b WHERE b.status_active=1 and b.is_deleted=0 $all_po_id_cond");
				$sales_arr = array();
				foreach ($data_array as $row) {
					$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
					$sales_arr[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_id")];
					$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
					$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
					$sales_arr[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
					$sales_booking_no .= "'" . $row[csf("sales_booking_no")] . "',";
				}

				$all_sales_booking_nos = rtrim($sales_booking_no, ", ");

				$job_array = array();
				$job_sql = "SELECT a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $all_po_id_cond";
				// echo $job_sql;
				$job_sql_result = sql_select($job_sql);
				foreach ($job_sql_result as $row) {
					$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
					$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
					$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
					$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
					$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
					$job_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				}

				// $data_array=sql_select("SELECT b.id, a.style_ref_no, a.job_no, a.buyer_name,b.file_no, b.grouping,c.booking_no from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id $all_po_id_cond");

				// $po_details_arr=array();
				// foreach($data_array as $row)
				// {
				// 	$po_details_arr[$row[csf("booking_no")]]['job_no']=$row[csf("job_no")];
				// 	$po_details_arr[$row[csf("booking_no")]]['buyer_name']=$row[csf("buyer_name")];
				// 	$po_details_arr[$row[csf("booking_no")]]['style_ref_no']=$row[csf("style_ref_no")];
				// 	$po_details_arr[$row[csf("booking_no")]]['file_no']=$row[csf("file_no")];
				// 	$po_details_arr[$row[csf("booking_no")]]['grouping']=$row[csf("grouping")];
				// }

				$data_array = sql_select("select b.job_no,a.buyer_id,b.booking_no from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_no in($all_sales_booking_nos) group by b.job_no,a.buyer_id,b.booking_no");

				$po_details_arr = array();
				foreach ($data_array as $row) {
					$po_details_arr[$row[csf("booking_no")]]['job_no'] = $row[csf("job_no")];
					$po_details_arr[$row[csf("booking_no")]]['buyer_id'] = $row[csf("buyer_id")];
				}

				$data_array_info = sql_select("select b.job_no,a.buyer_id,b.booking_no, d.style_ref_no from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c ,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id  and d.job_no=c.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_no in($all_sales_booking_nos) group by b.job_no,a.buyer_id,b.booking_no, d.style_ref_no");

				foreach ($data_array_info as $row) {
					$po_details_arr[$row[csf("booking_no")]]['style_ref_no'] = $row[csf("style_ref_no")];
					$po_details_arr[$row[csf("booking_no")]]['file_no'] = $row[csf("file_no")];
					$po_details_arr[$row[csf("booking_no")]]['grouping'] = $row[csf("grouping")];
				}
			}
			if (count($program_no_array) > 0) {
				$program_nos = implode(",", $program_no_array);
				$progNo = $all_program_no_cond = "";

				if ($db_type == 2 && count($program_no_array) > 999) {
					$program_no_arr_chunk = array_chunk($program_no_array, 999);
					foreach ($program_no_arr_chunk as $chunk_arr) {
						$progNo .= " id in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_program_no_cond .= " and (" . chop($progNo, 'or ') . ")";
				} else {
					$all_program_no_cond = " and id in($program_nos)";
				}

				$production_arr = array();
				$production_del_sql = sql_select("SELECT id, sys_number, knitting_source, knitting_company, location_id from pro_grey_prod_delivery_mst where entry_form=56 and company_id=$company $all_program_no_cond");
				foreach ($production_del_sql as $row) {
					$production_arr[$row[csf('id')]]['sys'] = $row[csf('sys_number')];
					$production_arr[$row[csf('id')]]['unit'] = $row[csf('location_id')];
					$production_arr[$row[csf('id')]]['knit_sou'] = $row[csf('knitting_source')];
					$production_arr[$row[csf('id')]]['knit_com'] = $row[csf('knitting_company')];
				}
			}
			if (count($roll_id_array) > 0) {
				$roll_ids = implode(",", $roll_id_array);
				$rollId = $all_roll_id_cond = "";

				if ($db_type == 2 && count($roll_id_array) > 999) {
					$roll_id_arr_chunk = array_chunk($roll_id_array, 999);
					foreach ($roll_id_arr_chunk as $chunk_arr) {
						$rollId .= " b.id in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_roll_id_cond .= " and (" . chop($rollId, 'or ') . ")";
				} else {
					$all_roll_id_cond = " and b.id in($roll_ids)";
				}

				$mc_id_arr = array();
				$gg_dia_arr = array();
				$prodArr = sql_select("SELECT b.id, a.machine_no_id, a.machine_dia, a.machine_gg from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=2 $all_roll_id_cond");
				foreach ($prodArr as $row) {
					$mc_id_arr[$row[csf('id')]] = $row[csf('machine_no_id')];
					$gg_dia_arr[$row[csf('id')]]['dia'] = $row[csf('machine_dia')];
					$gg_dia_arr[$row[csf('id')]]['gauge'] = $row[csf('machine_gg')];
				}
			}

		

			foreach ($result as $row) {
				$is_sales = $row[csf('is_sales')];
				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);


				// $yarn_type = explode(",", $row[csf('yarn_type')]);
				//var_dump($row[csf('yarn_type')]);

				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
					
				}
				$mc_id = $mc_id_arr[$row[csf("roll_id")]];
				// if($row[csf('basis')]==1) $pi_book_plan=$pi_arr[$row[csf("program_no")]];
				// else if($row[csf('basis')]==2) $pi_book_plan=$booking_arr[$row[csf("program_no")]];
				// else if($row[csf('basis')]==3) $pi_book_plan=$row[csf("program_no")];
				// else if($row[csf('basis')]==9) $pi_book_plan=$production_arr[$row[csf("program_no")]]['sys'];//$production_del_arr[$row[csf("program_no")]];
				// else $pi_book_plan="&nbsp;";

				$sales_booking = $sales_arr[$row[csf("po_breakdown_id")]]['sales_booking_no'];
				$within_group = $sales_arr[$row[csf("po_breakdown_id")]]['within_group'];
				if ($is_sales == 1) {
					if ($within_group == 1) {
						$booking_number = $sales_booking;
						$job_no = $po_details_arr[$sales_booking]['job_no'];
						$buyer_name = $buyer_array[$po_details_arr[$sales_booking]['buyer_id']];
						$style_ref = $po_details_arr[$sales_booking]['style_ref_no'];
					} else {
						$booking_number = "";
						$job_no = "";
						$buyer_name = $buyer_array[$sales_arr[$row[csf("po_breakdown_id")]]['buyer_name']];
						$style_ref = $sales_arr[$row[csf("po_breakdown_id")]]['style_ref_no'];
					}
					$po_number = $sales_arr[$row[csf("po_breakdown_id")]]['sales_order'];
					$file_no = $po_details_arr[$sales_booking]['file_no'];
					$ref_no = $po_details_arr[$sales_booking]['grouping'];
				} else {
					$po_number = $job_array[$row[csf('po_breakdown_id')]]['po'];
					$job_no = $job_array[$row[csf('po_breakdown_id')]]['job'];
					$buyer_name = $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']];
					$style_ref = $job_array[$row[csf('po_breakdown_id')]]['style_ref'];
					$file_no = $job_array[$row[csf('po_breakdown_id')]]['file_no'];
					$ref_no = $job_array[$row[csf('po_breakdown_id')]]['grouping'];

					if ($row[csf("booking_without_order")] == 0) {
						$booking_number = $production_rcv_data[$row[csf("barcode_no")]]["progBooking"];
					} else {
						$booking_number = $booking_data_arr[$row[csf("po_breakdown_id")]];
					}
				}
				$file_ref_ord = "";
				$job_buyer_style = "";
				if ($row[csf('booking_without_order')] == 1) {
					$file_ref_ord = $row[csf('booking_no')];
					$job_buyer_style = 'J: <br>s : ' . '___' . $buyer_array[$smn_booking_buyer_arr[$row[csf('booking_no')]]];
				} else {
					$file_ref_ord = $po_number;
					$job_buyer_style = 'J: ' . $job_no . '<br>S : ' . $style_ref . '___' . $buyer_name;
				}

				$knit_party = "";
				$knit_source = $production_arr[$row[csf("program_no")]]['knit_sou'];
				if ($knit_source == 1) $knit_party = $company_array[$production_arr[$row[csf("program_no")]]['knit_com']]['shortname'];
				else if ($knit_source == 3) $knit_party = $supplier_arr[$production_arr[$row[csf("program_no")]]['knit_com']];


				$print_dt[$file_ref_ord][$job_buyer_style][$grey_issue_basis[$row[csf('basis')]]][$booking_number][$composition_arr[$row[csf("deter_id")]]]['G : ' . $row[csf("gsm")] . '<br>D : ' . $row[csf("dia_width")]][$gg_dia_arr[$row[csf("roll_id")]]['dia'] . ' X ' . $gg_dia_arr[$row[csf("roll_id")]]['gauge']][$row[csf("color_id")]]['B :' . $brand_arr[$row[csf("brand_id")]] . '<br>U :' . $unit_of_measurement[$row[csf("uom")]]]['C : ' . $count . '<br>L : ' . $row[csf('yarn_lot')]][$knit_party][$row[csf('stitch_length')]]['roll_no'] += $row[csf('tot_roll')];
				$print_dt[$file_ref_ord][$job_buyer_style][$grey_issue_basis[$row[csf('basis')]]][$booking_number][$composition_arr[$row[csf("deter_id")]]]['G : ' . $row[csf("gsm")] . '<br>D : ' . $row[csf("dia_width")]][$gg_dia_arr[$row[csf("roll_id")]]['dia'] . ' X ' . $gg_dia_arr[$row[csf("roll_id")]]['gauge']][$row[csf("color_id")]]['B :' . $brand_arr[$row[csf("brand_id")]] . '<br>U :' . $unit_of_measurement[$row[csf("uom")]]]['C : ' . $count . '<br>L : ' . $row[csf('yarn_lot')]][$knit_party][$row[csf('stitch_length')]]['qnty'] += $row[csf('quantity')];
			}
			//print_r($print_dt);
			foreach ($print_dt as $file => $fdet) {
				foreach ($fdet as $job_buyers => $jbdet) {
					foreach ($jbdet as $basis => $basbdet) {
						foreach ($basbdet as $book_no => $bookdet) {
							foreach ($bookdet as $compos => $composdet) {
								foreach ($composdet as $diawid => $diawiddet) {
									foreach ($diawiddet as $gaugewid => $gaugewiddet) {
										foreach ($gaugewiddet as $colo => $colodet) {
											foreach ($colodet as $brandde => $branddedet) {
												foreach ($branddedet as $countde => $dedet) {
													foreach ($dedet as $partys => $partysdet) {
														foreach ($partysdet as $slength => $slength_val) {
															$color = '';
															$color_id = explode(",", $colo);
															foreach ($color_id as $val) {
																if ($val > 0) $color .= $color_arr[$val] . ",";
															}
															$color = chop($color, ',');
															$job_buyer = explode("___", $job_buyers);

														
													?>
															<tr style="font-size:11px">
																<td><? echo $i; ?></td>
																<td style="word-break:break-all;" align="center"><? echo $file; ?></td>
																<td style="word-break:break-all;" align="center"><? echo $job_buyer[0]; ?> </td>
																<td style="word-break:break-all;" align="center"><? echo $job_buyer[1]; ?> </td>
																<td><? echo $basis; ?></td>
																<td style="word-break:break-all;"><? echo $book_no; ?></td>
																<td><? echo $compos; ?></td>
																<td><? echo $slength; ?></td>
																<td style="word-break:break-all;"><? echo $diawid; ?></td>
																<td style="word-break:break-all;"><? echo $gaugewid; ?></td>
																<td style="word-break:break-all;"><? echo $color; ?></td>
																<td style="word-break:break-all;"><? echo $brandde; ?></td>
																<td style="word-break:break-all;">
																<? 
																	$tmp= explode(": ",$countde);

																	echo $countde .'<br>T : '.  $yarntype_arr[$tmp[2]]; 
																?>
																 </td>
																<td style="word-break:break-all;"><? echo $partys  ; ?></td>
																<td style="word-break:break-all;"><? echo 'R : ' . $floorRoomRackSelfArr[$row[csf('rack')]] . '<br>S : ' . $floorRoomRackSelfArr[$row[csf('self')]]; ?></td>
																<td style="word-break:break-all;" align="right"><? echo $slength_val['roll_no']; ?></td>
																<td align="right"><? echo number_format($slength_val['qnty'], 2); ?></td>
															</tr>
													<?
															$tot_roll += $slength_val['roll_no'];
															$tot_qty += $slength_val['qnty'];
															$i++;
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
			?>
			<tr style="font-size:12px">
				<td align="right" colspan="15"><strong>Total</strong></td>
				<td align="right"><? echo number_format($tot_roll); ?></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
			</tr>
		</table>
	</div>

	<?php
	$sql = "SELECT a.id, a.user_full_name, a.designation, b.id as desig_id, b.custom_designation FROM user_passwd a, lib_designation b WHERE a.designation= b.id";
	$user_res = sql_select($sql);
	$user_arr = array();
	foreach($user_res as $row)
	{
		$user_arr[$row['ID']]['name'] = $row['USER_FULL_NAME']; 
		$user_arr[$row['ID']]['custom_designation'] = $row['CUSTOM_DESIGNATION']; 
	} 
	$userDtlsArr=array(); 
	$userDtlsArr[$dataArray[0]['INSERTED_BY']] = "<div><b>".$user_arr[$dataArray[0]['INSERTED_BY']]['name']."</b></div><div><b>".$user_arr[$dataArray[0]['INSERTED_BY']]['custom_designation']."</b></div><div><small>".$dataArray[0]['INSERT_DATE']."</small></div>";
	echo get_app_signature(124, $company, "1000px",'', '', $inserted_by, $userDtlsArr); 
	?>

	<? //echo signature_table(124, $company, "1000px"); ?>

	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
    <?
	exit();
}

if ($action == "fabric_details_print_bpkw_gin7_28_04_2022") // GIN-7 for Charka TexTile
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$booking_arr = return_library_array("select id, booking_no from wo_booking_mst where item_category in(2,13)", "id", "booking_no");
	$pi_arr = return_library_array("select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");

	$smn_booking_buyer_arr = return_library_array("select booking_no, buyer_id from wo_non_ord_samp_booking_mst", "booking_no", "buyer_id");





	$lib_mc_arr = array();
	$mc_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($mc_sql as $row) {
		$lib_mc_arr[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
	}

	$dataArray = sql_select("select issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no,remarks from inv_issue_master where id=$update_id");


	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$grey_issue_basis = array(0 => "Independent", 1 => "PI", 2 => "Booking", 3 => "Knitting Plan", 9 => "Delivery");
?>
	<div>
		<table width="1100" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[3]; ?> Challan</u></strong></td>
			</tr>
			<tr>
				<td width="130" style="font-size:16px; font-weight:bold;"><strong>Dyeing Company:</strong> </td>
				<td style="font-size:18px; font-weight:bold;">
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
				<td width="120"><strong>Issue ID :</strong></td>
				<td width="175px" style="font-size:16px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
				<td width="125"><strong>Issue Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td>
				<td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				<td><strong>Issue Purpose:</strong></td>
				<td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
				<td><strong>Batch Number:</strong></td>
				<td width="175px"><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Remarks:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			<tr>
				<td colspan="3" id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1100" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center" style="font-size:12px">
				<th width="20">SL</th>
				<th width="80">Textile Ref No</th>
				<th width="90">Job/Style</th>
				<th width="100">Buyer Name</th>
				<th width="50">Basis</th>
				<th width="100">Fabric Booking No</th>
				<th width="110">Item Description</th>
				<th width="50">Stich Length</th>
				<th width="50">GSM/ Fin. Dia</th>
				<th width="70">MC No / Dia X Gauge</th>
				<th width="70">Color</th>
				<th width="35">Brand /UOM</th>
				<th width="60">Count /Y. Lot</th>
				<th width="60">K. Party</th>
				<th width="30">Rack/ Shelf</th>
				<th width="35">Total Roll</th>
				<th>Issue Qty</th>
			</thead>
			<?
			$i = 1;
			$tot_qty = 0;
			$sql = "SELECT a.basis, a.program_no, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, count(b.roll_no) as tot_roll, b.po_breakdown_id, sum(b.qnty) as quantity, b.booking_no, b.booking_without_order,b.is_sales, c.detarmination_id as deter_id, c.gsm, c.dia_width, c.unit_of_measure as uom, b.barcode_no
			from inv_grey_fabric_issue_dtls a, pro_roll_details b, product_details_master c
			where a.id=b.dtls_id and a.prod_id=c.id and a.mst_id=$update_id and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			group by a.basis, a.program_no, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, b.po_breakdown_id, b.booking_no, b.booking_without_order,b.is_sales, c.detarmination_id, c.gsm, c.dia_width, c.unit_of_measure, b.barcode_no";
			//echo $sql;
			$result = sql_select($sql);
			$booking_without_order_ids = $booking_with_order_ids = "";
			foreach ($result as $row) {
				$po_id_array[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
				$program_no_array[$row[csf('program_no')]] = $row[csf('program_no')];
				$roll_id_array[$row[csf('roll_id')]] = $row[csf('roll_id')];
				$barcodeNo_array[$row[csf('barcode_no')]] = $row[csf('barcode_no')];

				if ($row[csf("booking_without_order")] == 1) {
					$booking_without_order_ids .= $row[csf("po_breakdown_id")] . ",";
				} else {
					$booking_with_order_ids .= $row[csf("po_breakdown_id")] . ",";
				}
			}
			$po_id_array = array_filter(array_unique($po_id_array));
			$program_no_array = array_filter(array_unique($program_no_array));
			$roll_id_array = array_filter(array_unique($roll_id_array));
			$barcodeNo_array = array_filter(array_unique($barcodeNo_array));

			$booking_with_order_ids = implode(",", array_unique(array_filter(explode(",", chop($booking_with_order_ids, ",")))));
			$booking_without_order_ids = implode(",", array_unique(array_filter(explode(",", chop($booking_without_order_ids, ",")))));

			$sql_booking_withorder = sql_select("select po_break_down_id,booking_no from wo_booking_dtls  where po_break_down_id in($booking_with_order_ids) and status_active=1 and is_deleted=0 and booking_type=1");
			foreach ($sql_booking_withorder as $rows) {
				$booking_data_arr[$rows[csf("po_break_down_id")]] = $rows[csf("booking_no")];
			}



			$sql_booking_without_order = sql_select("select id,booking_no from wo_non_ord_samp_booking_mst  where id in($booking_without_order_ids) and status_active=1 and is_deleted=0");
			foreach ($sql_booking_without_order as $rows) {
				$booking_data_arr[$rows[csf("id")]] = $rows[csf("booking_no")];
			}

			if (count($barcodeNo_array) > 0) {
				$barcodeNos = implode(",", $barcodeNo_array);
				$barcode = $all_barcodeNo_cond = "";

				if ($db_type == 2 && count($barcodeNo_array) > 999) {
					$barcodeNo_arr_chunk = array_chunk($barcodeNo_array, 999);
					foreach ($barcodeNo_arr_chunk as $chunk_arr) {
						$barcode .= " a.barcode_no in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_barcodeNo_cond .= " and (" . chop($barcode, 'or ') . ")";
				} else {
					$all_barcodeNo_cond = " and a.barcode_no in($barcodeNos)";
				}


				$sql_production = sql_select("SELECT a.barcode_no,b.booking_no as program_no,c.machine_dia,c.machine_gg ,c.body_part_id ,e.booking_no
				from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c,ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e
				where a.mst_id=b.id  and a.dtls_id=c.id and b.id=c.mst_id and b.booking_id=d.id and d.mst_id=e.id $all_barcodeNo_cond and b.receive_basis=2 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				union all
				select a.barcode_no,null as program_no,c.machine_dia,c.machine_gg ,c.body_part_id ,b.booking_no
				from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c
				where a.mst_id=b.id  and a.dtls_id=c.id and b.id=c.mst_id $all_barcodeNo_cond and b.receive_basis=1 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

				foreach ($sql_production as $row) {
					$production_rcv_data[$row[csf("barcode_no")]]["progBooking"] = $row[csf("booking_no")];
				}
			}

			if (count($po_id_array) > 0) {
				$po_ids = implode(",", $po_id_array);
				$transBar = $all_po_id_cond = "";

				if ($db_type == 2 && count($po_id_array) > 999) {
					$po_id_arr_chunk = array_chunk($po_id_array, 999);
					foreach ($po_id_arr_chunk as $chunk_arr) {
						$transBar .= " b.id in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_po_id_cond .= " and (" . chop($transBar, 'or ') . ")";
				} else {
					$all_po_id_cond = " and b.id in($po_ids)";
				}

				$data_array = sql_select("SELECT b.id as po_id, b.job_no, b.sales_booking_no, b.buyer_id, b.within_group, b.style_ref_no FROM fabric_sales_order_mst b WHERE b.status_active=1 and b.is_deleted=0 $all_po_id_cond");
				$sales_arr = array();
				foreach ($data_array as $row) {
					$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
					$sales_arr[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_id")];
					$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
					$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
					$sales_arr[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
					$sales_booking_no .= "'" . $row[csf("sales_booking_no")] . "',";
				}

				$all_sales_booking_nos = rtrim($sales_booking_no, ", ");

				$job_array = array();
				$job_sql = "SELECT a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $all_po_id_cond";
				// echo $job_sql;
				$job_sql_result = sql_select($job_sql);
				foreach ($job_sql_result as $row) {
					$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
					$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
					$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
					$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
					$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
					$job_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				}

				// $data_array=sql_select("SELECT b.id, a.style_ref_no, a.job_no, a.buyer_name,b.file_no, b.grouping,c.booking_no from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id $all_po_id_cond");

				// $po_details_arr=array();
				// foreach($data_array as $row)
				// {
				// 	$po_details_arr[$row[csf("booking_no")]]['job_no']=$row[csf("job_no")];
				// 	$po_details_arr[$row[csf("booking_no")]]['buyer_name']=$row[csf("buyer_name")];
				// 	$po_details_arr[$row[csf("booking_no")]]['style_ref_no']=$row[csf("style_ref_no")];
				// 	$po_details_arr[$row[csf("booking_no")]]['file_no']=$row[csf("file_no")];
				// 	$po_details_arr[$row[csf("booking_no")]]['grouping']=$row[csf("grouping")];
				// }

				$data_array = sql_select("select b.job_no,a.buyer_id,b.booking_no from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_no in($all_sales_booking_nos) group by b.job_no,a.buyer_id,b.booking_no");

				$po_details_arr = array();
				foreach ($data_array as $row) {
					$po_details_arr[$row[csf("booking_no")]]['job_no'] = $row[csf("job_no")];
					$po_details_arr[$row[csf("booking_no")]]['buyer_id'] = $row[csf("buyer_id")];
				}

				$data_array_info = sql_select("select b.job_no,a.buyer_id,b.booking_no, d.style_ref_no from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c ,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id  and d.job_no=c.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_no in($all_sales_booking_nos) group by b.job_no,a.buyer_id,b.booking_no, d.style_ref_no");

				foreach ($data_array_info as $row) {
					$po_details_arr[$row[csf("booking_no")]]['style_ref_no'] = $row[csf("style_ref_no")];
					$po_details_arr[$row[csf("booking_no")]]['file_no'] = $row[csf("file_no")];
					$po_details_arr[$row[csf("booking_no")]]['grouping'] = $row[csf("grouping")];
				}
			}
			if (count($program_no_array) > 0) {
				$program_nos = implode(",", $program_no_array);
				$progNo = $all_program_no_cond = "";

				if ($db_type == 2 && count($program_no_array) > 999) {
					$program_no_arr_chunk = array_chunk($program_no_array, 999);
					foreach ($program_no_arr_chunk as $chunk_arr) {
						$progNo .= " id in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_program_no_cond .= " and (" . chop($progNo, 'or ') . ")";
				} else {
					$all_program_no_cond = " and id in($program_nos)";
				}

				$production_arr = array();
				$production_del_sql = sql_select("SELECT id, sys_number, knitting_source, knitting_company, location_id from pro_grey_prod_delivery_mst where entry_form=56 and company_id=$company $all_program_no_cond");
				foreach ($production_del_sql as $row) {
					$production_arr[$row[csf('id')]]['sys'] = $row[csf('sys_number')];
					$production_arr[$row[csf('id')]]['unit'] = $row[csf('location_id')];
					$production_arr[$row[csf('id')]]['knit_sou'] = $row[csf('knitting_source')];
					$production_arr[$row[csf('id')]]['knit_com'] = $row[csf('knitting_company')];
				}
			}
			if (count($roll_id_array) > 0) {
				$roll_ids = implode(",", $roll_id_array);
				$rollId = $all_roll_id_cond = "";

				if ($db_type == 2 && count($roll_id_array) > 999) {
					$roll_id_arr_chunk = array_chunk($roll_id_array, 999);
					foreach ($roll_id_arr_chunk as $chunk_arr) {
						$rollId .= " b.id in(" . implode(",", $chunk_arr) . ") or ";
					}

					$all_roll_id_cond .= " and (" . chop($rollId, 'or ') . ")";
				} else {
					$all_roll_id_cond = " and b.id in($roll_ids)";
				}

				$mc_id_arr = array();
				$gg_dia_arr = array();
				$prodArr = sql_select("SELECT b.id, a.machine_no_id, a.machine_dia, a.machine_gg from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=2 $all_roll_id_cond");
				foreach ($prodArr as $row) {
					$mc_id_arr[$row[csf('id')]] = $row[csf('machine_no_id')];
					$gg_dia_arr[$row[csf('id')]]['dia'] = $row[csf('machine_dia')];
					$gg_dia_arr[$row[csf('id')]]['gauge'] = $row[csf('machine_gg')];
				}
			}

			foreach ($result as $row) {
				if ($row[csf("booking_without_order")] == 0) {
					$booking_number = $production_rcv_data[$row[csf("barcode_no")]]["progBooking"];
				} else {
					$booking_number = $booking_data_arr[$row[csf("po_breakdown_id")]];
				}

				$is_sales = $row[csf('is_sales')];
				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
				}
				$mc_id = $mc_id_arr[$row[csf("roll_id")]];
				// if($row[csf('basis')]==1) $pi_book_plan=$pi_arr[$row[csf("program_no")]];
				// else if($row[csf('basis')]==2) $pi_book_plan=$booking_arr[$row[csf("program_no")]];
				// else if($row[csf('basis')]==3) $pi_book_plan=$row[csf("program_no")];
				// else if($row[csf('basis')]==9) $pi_book_plan=$production_arr[$row[csf("program_no")]]['sys'];//$production_del_arr[$row[csf("program_no")]];
				// else $pi_book_plan="&nbsp;";

				$sales_booking = $sales_arr[$row[csf("po_breakdown_id")]]['sales_booking_no'];
				$within_group = $sales_arr[$row[csf("po_breakdown_id")]]['within_group'];
				if ($is_sales == 1) {
					if ($within_group == 1) {
						$job_no = $po_details_arr[$sales_booking]['job_no'];
						$buyer_name = $buyer_array[$po_details_arr[$sales_booking]['buyer_id']];
						$style_ref = $po_details_arr[$sales_booking]['style_ref_no'];
					} else {
						$job_no = "";
						$buyer_name = $buyer_array[$sales_arr[$row[csf("po_breakdown_id")]]['buyer_name']];
						$style_ref = $sales_arr[$row[csf("po_breakdown_id")]]['style_ref_no'];
					}
					$po_number = $sales_arr[$row[csf("po_breakdown_id")]]['sales_order'];
					$file_no = $po_details_arr[$sales_booking]['file_no'];
					$ref_no = $po_details_arr[$sales_booking]['grouping'];
				} else {
					$po_number = $job_array[$row[csf('po_breakdown_id')]]['po'];
					$job_no = $job_array[$row[csf('po_breakdown_id')]]['job'];
					$buyer_name = $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']];
					$style_ref = $job_array[$row[csf('po_breakdown_id')]]['style_ref'];
					$file_no = $job_array[$row[csf('po_breakdown_id')]]['file_no'];
					$ref_no = $job_array[$row[csf('po_breakdown_id')]]['grouping'];
				}
				$file_ref_ord = "";
				$job_buyer_style = "";
				if ($row[csf('booking_without_order')] == 1) {
					$file_ref_ord = $row[csf('booking_no')];
					$job_buyer_style = 'J: <br>s : ' . '___' . $buyer_array[$smn_booking_buyer_arr[$row[csf('booking_no')]]];
				} else {
					$file_ref_ord = $po_number;
					$job_buyer_style = 'J: ' . $job_no . '<br>S : ' . $style_ref . '___' . $buyer_name;
				}

				$knit_party = "";
				$knit_source = $production_arr[$row[csf("program_no")]]['knit_sou'];
				if ($knit_source == 1) $knit_party = $company_array[$production_arr[$row[csf("program_no")]]['knit_com']]['shortname'];
				else if ($knit_source == 3) $knit_party = $supplier_arr[$production_arr[$row[csf("program_no")]]['knit_com']];

				$print_dt[$file_ref_ord][$job_buyer_style][$grey_issue_basis[$row[csf('basis')]]][$booking_number][$composition_arr[$row[csf("deter_id")]]]['G : ' . $row[csf("gsm")] . '<br>D : ' . $row[csf("dia_width")]]['N : ' . $lib_mc_arr[$mc_id]['no'] . '<br> D : ' . $gg_dia_arr[$row[csf("roll_id")]]['dia'] . ' X ' . $gg_dia_arr[$row[csf("roll_id")]]['gauge']][$row[csf("color_id")]]['B :' . $brand_arr[$row[csf("brand_id")]] . '<br>U :' . $unit_of_measurement[$row[csf("uom")]]]['C : ' . $count . '<br>L : ' . $row[csf('yarn_lot')]][$knit_party][$row[csf('stitch_length')]]['roll_no'] += $row[csf('tot_roll')];
				$print_dt[$file_ref_ord][$job_buyer_style][$grey_issue_basis[$row[csf('basis')]]][$booking_number][$composition_arr[$row[csf("deter_id")]]]['G : ' . $row[csf("gsm")] . '<br>D : ' . $row[csf("dia_width")]]['N : ' . $lib_mc_arr[$mc_id]['no'] . '<br> D : ' . $gg_dia_arr[$row[csf("roll_id")]]['dia'] . ' X ' . $gg_dia_arr[$row[csf("roll_id")]]['gauge']][$row[csf("color_id")]]['B :' . $brand_arr[$row[csf("brand_id")]] . '<br>U :' . $unit_of_measurement[$row[csf("uom")]]]['C : ' . $count . '<br>L : ' . $row[csf('yarn_lot')]][$knit_party][$row[csf('stitch_length')]]['qnty'] += $row[csf('quantity')];
			}
			//print_r($print_dt);
			foreach ($print_dt as $file => $fdet) {
				foreach ($fdet as $job_buyers => $jbdet) {
					foreach ($jbdet as $basis => $basbdet) {
						foreach ($basbdet as $book_no => $bookdet) {
							foreach ($bookdet as $compos => $composdet) {
								foreach ($composdet as $diawid => $diawiddet) {
									foreach ($diawiddet as $gaugewid => $gaugewiddet) {
										foreach ($gaugewiddet as $colo => $colodet) {
											foreach ($colodet as $brandde => $branddedet) {
												foreach ($branddedet as $countde => $dedet) {
													foreach ($dedet as $partys => $partysdet) {
														foreach ($partysdet as $slength => $slength_val) {
															$color = '';
															$color_id = explode(",", $colo);
															foreach ($color_id as $val) {
																if ($val > 0) $color .= $color_arr[$val] . ",";
															}
															$color = chop($color, ',');
															$job_buyer = explode("___", $job_buyers);
			?>
															<tr style="font-size:11px">
																<td><? echo $i; ?></td>
																<td style="word-break:break-all;" align="center"><? echo $file; ?></td>
																<td style="word-break:break-all;" align="center"><? echo $job_buyer[0]; ?> </td>
																<td style="word-break:break-all;" align="center"><? echo $job_buyer[1]; ?> </td>
																<td><? echo $basis; ?></td>
																<td style="word-break:break-all;"><? echo $book_no; ?></td>
																<td><? echo $compos; ?></td>
																<td><? echo $slength; ?></td>
																<td style="word-break:break-all;"><? echo $diawid; ?></td>
																<td style="word-break:break-all;"><? echo $gaugewid; ?></td>
																<td style="word-break:break-all;"><? echo $color; ?></td>
																<td style="word-break:break-all;"><? echo $brandde; ?></td>
																<td style="word-break:break-all;"><? echo $countde; ?></td>
																<td style="word-break:break-all;"><? echo $partys; ?></td>
																<td style="word-break:break-all;"><? echo 'R : ' . $row[csf('rack')] . '<br>S : ' . $row[csf('self')]; ?></td>
																<td style="word-break:break-all;" align="right"><? echo $slength_val['roll_no']; ?></td>
																<td align="right"><? echo number_format($slength_val['qnty'], 2); ?></td>
															</tr>
			<?
															$tot_roll += $slength_val['roll_no'];
															$tot_qty += $slength_val['qnty'];
															$i++;
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
			?>
			<tr style="font-size:12px">
				<td align="right" colspan="15"><strong>Total</strong></td>
				<td align="right"><? echo number_format($tot_roll); ?></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
			</tr>
		</table>
	</div>
	<? echo signature_table(124, $company, "1000px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}



if ($action == "fabric_details_print_bpkw_gin5____old") {
	extract($_REQUEST);
	$data = explode('*', $data);


	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$booking_arr = return_library_array("select id, booking_no_prefix_num from wo_booking_mst where item_category in(2,13)", "id", "booking_no_prefix_num");
	$pi_arr = return_library_array("select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");
	$production_arr = return_library_array("select id, sys_number_prefix_num from pro_grey_prod_delivery_mst where entry_form=56", "id", "sys_number_prefix_num");
	$dataArray = sql_select("select issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no,remarks from inv_issue_master where id=$update_id");

	$job_array = array();
	$job_sql = "select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
		$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
	}

	$data_array = sql_select("SELECT id as po_id,job_no, sales_booking_no, buyer_id,within_group,style_ref_no FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0");
	$sales_arr = array();
	foreach ($data_array as $row) {
		$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
		$sales_arr[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_id")];
		$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
		$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
		$sales_arr[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
	}

	$data_array = sql_select("select b.id, a.style_ref_no, a.job_no, a.buyer_name,b.file_no, b.grouping,c.booking_no from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id");
	$po_details_arr = array();
	foreach ($data_array as $row) {
		$po_details_arr[$row[csf("booking_no")]]['job_no'] = $row[csf("job_no")];
		$po_details_arr[$row[csf("booking_no")]]['buyer_name'] = $row[csf("buyer_name")];
		$po_details_arr[$row[csf("booking_no")]]['style_ref_no'] = $row[csf("style_ref_no")];
		$po_details_arr[$row[csf("booking_no")]]['file_no'] = $row[csf("file_no")];
		$po_details_arr[$row[csf("booking_no")]]['grouping'] = $row[csf("grouping")];
	}

	$product_array = array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach ($product_sql as $row) {
		$product_array[$row[csf("id")]]['gsm'] = $row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width'] = $row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id'] = $row[csf("detarmination_id")];

		$product_array[$row[csf("id")]]['uom'] = $row[csf("unit_of_measure")];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$grey_issue_basis = array(0 => "Independent", 1 => "PI", 2 => "Booking", 3 => "Knitting Plan", 9 => "Delivery");

	if ($db_type == 0) {
		$po_id = return_field_value("group_concat(po_breakdown_id) as po_id", "inv_grey_fabric_issue_dtls a, order_wise_pro_details b", "a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "po_id");
	} else {
		$po_id = return_field_value("LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id", "inv_grey_fabric_issue_dtls a, order_wise_pro_details b", "a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "po_id");
	}
	// echo $po_id;
	$sales_data = sql_select("select po_breakdown_id,mst_id,is_sales,barcode_no from pro_roll_details where mst_id=$update_id and entry_form=61 and status_active=1 and is_deleted=0");
	$sales_data_arr = array();
	foreach ($sales_data as $row) {
		$sales_data_arr[$row[csf('po_breakdown_id')]]['is_sales'] = $row[csf('is_sales')];
	}
	$po_exp = array_unique(explode(',', $po_id));
	$all_po_string = implode(",", $po_exp);

	if ($all_po_string != "") {
		$knit_prod_rcv_sql = sql_select("select b.barcode_no, a.machine_dia, a.machine_gg, a.body_part_id from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.roll_id=0 and b.po_breakdown_id in($all_po_string)");
		$production_rcv_data = array();
		foreach ($knit_prod_rcv_sql as $row) {
			$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
			$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
			$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] = $row[csf("body_part_id")];
		}
	} else {
		$knit_prod_rcv_sql = sql_select("select b.barcode_no, a.machine_dia, a.machine_gg, a.body_part_id from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.roll_id=0 and booking_without_order=1");
		$production_rcv_data = array();
		foreach ($knit_prod_rcv_sql as $row) {
			$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
			$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
			$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] = $row[csf("body_part_id")];
		}
		$sam_buyer_sql = sql_select("select a.buyer_id from  wo_non_ord_samp_booking_mst a, pro_roll_details b where a.id=b.po_breakdown_id and b.booking_without_order=1 and b.entry_form=61 and b.mst_id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($sam_buyer_sql as $row) {
			//$all_buyer.=$buyer_array[$row[csf('buyer_id')]].",";
		}
		//$all_buyer=implode(",",array_unique(explode(',',chop($all_buyer,","))));
	}


	/*	$sales_data=sql_select("select po_breakdown_id,mst_id,is_sales,barcode_no from pro_roll_details where mst_id=$update_id and entry_form=61 and status_active=1 and is_deleted=0");
	$sales_data_arr=array();
	foreach ($sales_data as $row) {
		$sales_data_arr[$row[csf('po_breakdown_id')]]['is_sales'] = $row[csf('is_sales')];
	}*/



	/*if($all_po_string!="")
	{
		$knit_prod_rcv_sql=sql_select("select b.barcode_no, a.machine_dia, a.machine_gg, a.body_part_id from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.roll_id=0 and b.po_breakdown_id in($all_po_string)");
		$production_rcv_data=array();
		foreach($knit_prod_rcv_sql as $row)
		{
			$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"]=$row[csf("machine_dia")];
			$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"]=$row[csf("machine_gg")];
			$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"]=$row[csf("body_part_id")];
		}
	}
	else
	{
		$knit_prod_rcv_sql=sql_select("select b.barcode_no, a.machine_dia, a.machine_gg, a.body_part_id from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.roll_id=0 and booking_without_order=1");
		$production_rcv_data=array();
		foreach($knit_prod_rcv_sql as $row)
		{
			$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"]=$row[csf("machine_dia")];
			$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"]=$row[csf("machine_gg")];
			$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"]=$row[csf("body_part_id")];
		}
		$sam_buyer_sql=sql_select("select a.buyer_id from  wo_non_ord_samp_booking_mst a, pro_roll_details b where a.id=b.po_breakdown_id and b.booking_without_order=1 and b.entry_form=61 and b.mst_id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($sam_buyer_sql as $row)
		{
			$all_buyer.=$buyer_array[$row[csf('buyer_id')]].",";
		}
		$all_buyer=implode(",",array_unique(explode(',',chop($all_buyer,","))));
	}*/
?>
	<div>
		<table width="900" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">


					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[3]; ?> Grey Fabric Roll Issue Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:16px; font-weight:bold;"><strong>Issue ID :</strong></td>
				<td width="175px" style="font-size:18px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
				<td width="125"><strong>Issue Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Issue Purpose:</strong></td>
				<td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
				<td width="130" style="font-size:16px"><strong>Dyeing Company:</strong> </td>
				<td style="font-size:18px; font-weight:bold;">
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
			</tr>
			<tr>
				<td><strong>Remarks:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			<tr>
				<td colspan="3" id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1160" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center">
				<th width="20">SL</th>
				<th width="50">Buyer</th>
				<th width="80">Style</th>
				<th width="100">Job No</th>
				<th width="150">Item Description</th>
				<th width="110">Body Part</th>
				<th width="50">Stich Length</th>
				<th width="40">GSM</th>
				<th width="40">Fin. Dia</th>
				<th width="60">M/C Dia</th>
				<th width="60">M/C Gauge</th>
				<th width="70">Color</th>
				<th width="50">Count</th>
				<th width="50">Brand</th>
				<th width="50">Yarn Lot</th>
				<th width="40">Issue Qty</th>
				<th>No of Roll</th>
			</thead>
			<?
			//$roll_arr=return_library_array( "select id, roll_no from pro_roll_details where entry_form in(2,22)", "id", "roll_no");
			$i = 1;
			$tot_qty = 0;
			$program_arr = array();
			$program_sql = "select a.id,b.booking_no from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id = b.id and a.status_active=1 and a.is_deleted=0";
			$program_result = sql_select($program_sql);
			foreach ($program_result as $program_row) {
				$program_arr[$program_row[csf("id")]]["program_no"] = $program_row[csf("id")];
				$program_arr[$program_row[csf("id")]]["booking_no"] = $program_row[csf("booking_no")];
			}

			$bookingBarNo_sql = sql_select("select barcode_no, receive_basis from pro_roll_details where entry_form=2 and is_deleted=0 and status_active=1");
			foreach ($bookingBarNo_sql as $row) {
				$bookingBar_data[$row[csf("barcode_no")]]["receive_basis"] = $row[csf("receive_basis")];
			}
			// echo "<pre>";
			//print_r($bookingBar_data);
			$job_array = array();
			$job_sql = "SELECT a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1";
			$job_sql_result = sql_select($job_sql);
			foreach ($job_sql_result as $row) {
				$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
				$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
				$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
				$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
				$job_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
			}

			if ($db_type == 0) {
				/*$sql = "select b.po_breakdown_id,a.basis, group_concat(a.program_no) as program_no,group_concat(b.booking_no) as booking_no, a.prod_id, sum(a.issue_qnty) as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, count(b.roll_id) as no_of_roll, group_concat(b.barcode_no) as barcode_no
					from inv_grey_fabric_issue_dtls a, pro_roll_details b
					where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1	and b.is_deleted=0
					group by a.basis, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id,b.po_breakdown_id";*/

				$sql = "SELECT a.basis, group_concat(a.program_no) as program_no,group_concat(b.po_breakdown_id) as po_breakdown_id,b.booking_no as booking_no, a.prod_id, sum(a.issue_qnty) as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, count(b.roll_id) as no_of_roll, group_concat(b.barcode_no) as barcode_no
					from inv_grey_fabric_issue_dtls a, pro_roll_details b
					where a.id=b.dtls_id and a.mst_id=$update_id  and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1	and b.is_deleted=0
					group by a.basis, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id,b.booking_no";
			} else {
				/*$sql = "select a.basis, listagg(cast(a.program_no as varchar(4000)),',') within group (order by a.program_no) as program_no, a.prod_id, sum(a.issue_qnty) as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, count(b.roll_id) as no_of_roll, listagg(cast(b.barcode_no as varchar(4000)),',') within group (order by b.barcode_no) as barcode_no,listagg(cast(b.booking_no as varchar(4000)),',') within group (order by b.booking_no) as booking_no
					from inv_grey_fabric_issue_dtls a, pro_roll_details b
					where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1	and b.is_deleted=0
					group by a.basis, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id";*/
				$sql = "SELECT a.basis, listagg(cast(a.program_no as varchar(4000)),',') within group (order by a.program_no) as program_no,listagg(cast(b.po_breakdown_id as varchar(4000)),',') within group (order by a.po_breakdown_id) as po_breakdown_id, a.prod_id, sum(a.issue_qnty) as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, count(b.roll_id) as no_of_roll, listagg(cast(b.barcode_no as varchar(4000)),',') within group (order by b.barcode_no) as barcode_no,b.booking_no as booking_no
					from inv_grey_fabric_issue_dtls a, pro_roll_details b
					where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1	and b.is_deleted=0
					group by a.basis, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id,b.booking_no";
			}
			$result = sql_select($sql);
			$bookingID = "";
			foreach ($result as $row_data) {
				$bookingID .= "'" . $row_data[csf('booking_no')] . "'" . ',';
			}

			$bookingIDs = chop($bookingID, ',');

			$booking_details_sql = sql_select("select a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping as ref_no,d.style_ref_no
					from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id
					and a.status_active=1 and b.status_active=1 and b.job_no=d.job_no and a.booking_no in(" . $bookingIDs . ")
					group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping,d.style_ref_no");

			//$bookingData=sql_select($booking_details_sql);
			$booking_data_arr = array();
			foreach ($booking_details_sql as $row_datas) {
				$booking_data_arr[$row_datas[csf('booking_no')]]['job_no'] = $row_datas[csf('job_no')];
				$booking_data_arr[$row_datas[csf('booking_no')]]['buyer_id'] = $row_datas[csf('buyer_id')];
				$booking_data_arr[$row_datas[csf('booking_no')]]['style_ref_no'] = $row_datas[csf('style_ref_no')];
			}


			/*echo $sql;
				$result_po=sql_select($sql);
				$po_IDs="";
				foreach($result_po as $row)
				{
					 $po_IDs.=$row[csf('po_breakdown_id')].',';
				}
				$po_ID=chop($po_IDs,',');

				$sql_po_breakdown=sql_select("select a.id,b.job_no,b.buyer_name,b.style_ref_no from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no and a.id in ($po_ID) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				$poData_arr=array();
				foreach($sql_po_breakdown as $row)
				{
					$poData_arr[$row[csf('id')]]["buyer_name"]=$row[csf('buyer_name')];
					$poData_arr[$row[csf('id')]]["job_no"]=$row[csf('job_no')];
					$poData_arr[$row[csf('id')]]["style_ref_no"]=$row[csf('style_ref_no')];
				}
				//print_r($poData_arr);*/



			$po_no = '';
			$job = '';
			$style_ref = '';
			$all_buyer = "";

			foreach ($result as $row) {
				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
				}
				$pi_book_plan = "";
				//$program_no_arr=array_unique(explode(",",$row[csf("program_no")]));
				$program_no_arr = array_unique(explode(",", $row[csf("program_no")]));
				foreach ($program_no_arr as $program_no) {
					if ($row[csf('basis')] == 1) $pi_book_plan .= $pi_arr[$program_no] . ",";
					else if ($row[csf('basis')] == 2) $pi_book_plan .= $booking_arr[$program_no] . ",";
					else if ($row[csf('basis')] == 3)  $pi_book_plan .= $program_no . ",";
					else if ($row[csf('basis')] == 9) $pi_book_plan .= $production_arr[$program_no] . ",";
					//$booking_no = $program_arr[$program_no]["program_no"]."<br />".$program_arr[$program_no]["booking_no"];
				}


				$pi_book_plan = implode(",", array_unique(explode(",", chop($pi_book_plan, ","))));

				$barcode_no_arr = array_unique(explode(",", $row[csf("barcode_no")]));
				$mc_dia = $mc_gg = $body_part_name = "";
				foreach ($barcode_no_arr as $barcode_no) {
					$mc_dia .= $production_rcv_data[$barcode_no]["machine_dia"] . ",";
					$mc_gg .= $production_rcv_data[$barcode_no]["machine_gg"] . ",";
					$body_part_name .= $body_part[$production_rcv_data[$barcode_no]["body_part_id"]] . ",";
					$receive_basis = $bookingBar_data[$barcode_no]["receive_basis"];
				}
				$booking_no_arr = array_unique(explode(",", $row[csf("booking_no")]));
				$booking_no = "";
				foreach ($booking_no_arr as $booking_no) {
					if ($receive_basis == 2) {
						$booking_no = $program_arr[$booking_no]["program_no"] . "<br />" . $program_arr[$booking_no]["booking_no"];
					}
				}
				// echo $booking_no_array[$barcode_no]['booking_no'] ;
				// print_r($booking_no_array);
				$mc_dia = implode(",", array_unique(explode(",", chop($mc_dia, ","))));
				$mc_gg = implode(",", array_unique(explode(",", chop($mc_gg, ","))));
				$body_part_name = implode(",", array_unique(explode(",", chop($body_part_name, ","))));


				$is_sales = $sales_data_arr[$row[csf("po_breakdown_id")]]['is_sales'];
				$sales_booking = $sales_arr[$row[csf("po_breakdown_id")]]['sales_booking_no'];
				$within_group = $sales_arr[$row[csf("po_breakdown_id")]]['within_group'];
				if ($is_sales == 1) {
					if ($within_group == 1) {
						if ($po_no == '') $po_no = $sales_arr[$row[csf("po_breakdown_id")]]['sales_order'];
						else $po_no .= ', ' . $sales_arr[$row[csf("po_breakdown_id")]]['sales_order'];
						if ($job == '') $job = $po_details_arr[$sales_booking]['job_no'];
						else $job = $po_details_arr[$sales_booking]['job_no'];
						if ($style_ref == '') $style_ref = $po_details_arr[$sales_booking]['style_ref_no'];
						else $style_ref = $po_details_arr[$sales_booking]['style_ref_no'];
						$all_buyer = $buyer_array[$po_details_arr[$sales_booking]['buyer_name']];
					} else {
						if ($po_no == '') $po_no = $sales_arr[$row[csf("po_breakdown_id")]]['sales_order'];
						else $po_no .= ', ' . $sales_arr[$row[csf("po_breakdown_id")]]['sales_order'];
						if ($job == '') $job = "";
						if ($style_ref == '') $style_ref = $sales_arr[$row[csf("po_breakdown_id")]]['style_ref_no'];
						else $style_ref = $sales_arr[$row[csf("po_breakdown_id")]]['style_ref_no'];
						$all_buyer = $buyer_array[$sales_arr[$row[csf("po_breakdown_id")]]['buyer_name']];
					}
				} else {
					if ($po_no == '') $po_no = $job_array[$row[csf("po_breakdown_id")]]['po'];
					else $po_no .= ', ' . $job_array[$row[csf("po_breakdown_id")]]['po'];
					if ($job == '') $job = $job_array[$row[csf("po_breakdown_id")]]['job'];
					else $job = $job_array[$row[csf("po_breakdown_id")]]['job'];
					if ($style_ref == '') $style_ref = $job_array[$row[csf("po_breakdown_id")]]['style_ref'];
					else $style_ref = $job_array[$row[csf("po_breakdown_id")]]['style_ref'];
					$all_buyer = $buyer_array[$job_array[$row[csf("po_breakdown_id")]]['buyer_name']];
				}
				if (!$row[csf('booking_no')]) {
					$all_po = array_unique(explode(",", $row[csf("po_breakdown_id")]));
					$buyer = "";
					$job = "";
					$style = "";
					foreach ($all_po as $key => $vals) {
						if ($job == "") {
							$job .= $job_array[$vals]['job'];
						} else {
							$job .= ',' . $job_array[$vals]['job'];
						}
						if ($buyer == "") {
							$buyer .= $buyer_array[$job_array[$vals]['buyer']];
						} else {
							$buyer .= ',' . $buyer_array[$job_array[$vals]['buyer']];
						}
						if ($style == "") {
							$style .= $job_array[$vals]['style_ref'];
						} else {
							$style .= ',' . $job_array[$vals]['style_ref'];
						}
					}
				} else {
					$job = $booking_data_arr[$row[csf('booking_no')]]['job_no'];
					$style = $booking_data_arr[$row[csf('booking_no')]]['style_ref_no'];
					$buyer = $buyer_array[$booking_data_arr[$row[csf('booking_no')]]['buyer_id']];
				}


			?>
				<tr>
					<td><? echo $i; ?></td>
					<td><? echo $buyer; ?></td>
					<td style="word-break:break-all;"><? echo $style; ?></td>
					<td style="font-size:12px;"><? echo $job; ?></td>
					<td><? echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']]; ?></td>
					<td style="word-break:break-all;"><? echo $body_part_name; ?></td>
					<td><? echo $row[csf('stitch_length')]; ?></td>
					<td style="word-break:break-all;"><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></td>
					<td style="word-break:break-all;"><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></td>
					<td style="word-break:break-all;"><? echo $mc_dia; ?></td>
					<td style="word-break:break-all;"><? echo $mc_gg; ?></td>
					<td style="word-break:break-all;"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $count; ?></td>
					<td style="word-break:break-all;"><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
					<td style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
					<td align="right"><? echo number_format($row[csf('issue_qnty')], 2); ?></td>
					<td style="word-break:break-all;" align="center"><? echo $row[csf("no_of_roll")]; ?></td>
				</tr>
			<?
				$tot_qty += $row[csf('issue_qnty')];
				$total_roll += $row[csf("no_of_roll")];
				$i++;
			}

			?>
			<tr>
				<td align="right" colspan="15"><strong>Total</strong></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
				<td align="center"><? echo $total_roll; ?></td>
			</tr>
		</table>
	</div>
	<? echo signature_table(124, $company, "1160px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "fabric_details_print_bpkw_gin5") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}
	$buyer_array = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	unset($data_array);


	$sql = "SELECT a.basis, a.program_no , b.po_breakdown_id, a.prod_id, b.qnty as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, b.barcode_no,b.booking_no,b.is_sales,b.booking_without_order,c.issue_date, c.issue_purpose,c.knit_dye_company, c.knit_dye_source, c.remarks
	from inv_grey_fabric_issue_dtls a, pro_roll_details b, inv_issue_master c
	where a.id=b.dtls_id and a.mst_id = c.id and b.mst_id = c.id and c.entry_form=61 and a.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0";

	$dataArray = sql_select($sql);

	foreach ($dataArray as  $val) {
		if ($val[csf("is_sales")] == "1") {
			$all_sales_flag[$val[csf("is_sales")]] = $val[csf("is_sales")];
		} else {
			if ($val[csf("booking_without_order")] == 1) {
				$booking_id_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			} else {
				$po_ids_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			}
			$barcode_no_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
		}
	}

	$all_sales_flags = implode(",", array_filter(array_unique($all_sales_flag)));

	if ($all_sales_flags == "1") {
		echo "This Print Doesn't show sales order data";
		die;
	}



	$booking_id_arr = array_filter($booking_id_arr);

	if (count($booking_id_arr) > 0) {
		$book_sql = sql_select("select id, booking_no, buyer_id from wo_non_ord_samp_booking_mst where id in(" . implode(",", $booking_id_arr) . ")");
		foreach ($book_sql as $val) {
			$booking_ref[$val[csf("id")]] = $val[csf("booking_no")];
			$booking_ref_buyer[$val[csf("id")]]["buyer_id"] = $val[csf("buyer_id")];
		}
	}

	$po_ids_arr = array_filter($po_ids_arr);

	$job_array = array();
	if (count($po_ids_arr) > 0) {
		$job_sql = "SELECT a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and b.id in (" . implode(",", $po_ids_arr) . ")";

		$job_sql_result = sql_select($job_sql);
		foreach ($job_sql_result as $row) {
			$job_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
			$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
			$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			$job_array[$row[csf('id')]]['int_ref'] = $row[csf('grouping')];
			$job_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
		}
	}


	$barcode_no_arr = array_filter(array_unique($barcode_no_arr));
	if (count($barcode_no_arr) > 0) {
		$barcode_nos = implode(",", $barcode_no_arr);
		$transBar = $all_barcode_no_cond = "";

		if ($db_type == 2 && count($barcode_no_arr) > 999) {
			$barcode_no_arr_chunk = array_chunk($barcode_no_arr, 999);
			foreach ($barcode_no_arr_chunk as $chunk_arr) {
				$transBar .= " b.barcode_no in(" . implode(",", $chunk_arr) . ") or ";
			}

			$all_barcode_no_cond .= " and (" . chop($transBar, 'or ') . ")";
		} else {
			$all_barcode_no_cond = " and b.barcode_no in($barcode_nos)";
		}
	}

	if ($barcode_no_arr != "") {
		$production_sql = sql_select("select b.barcode_no, a.booking_id, a.booking_no, a.receive_basis, a.knitting_source, a.knitting_company, b.qc_pass_qnty_pcs, b.coller_cuff_size, b.dtls_id, c.body_part_id from inv_receive_master a, pro_roll_details b ,pro_grey_prod_entry_dtls c where c.id=b.dtls_id and a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and b.status_active=1 $all_barcode_no_cond");

		$coller_cuff_size_arr = array();
		foreach ($production_sql as $row) {
			if ($row[csf("coller_cuff_size")] != '') {
				$coller_cuff_size_arr[$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]['qnty_pcs'] += $row[csf("qc_pass_qnty_pcs")];
			}
		}
		//var_dump($coller_cuff_size_arr);
	}


	$knit_prod_rcv_sql = sql_select("SELECT b.barcode_no, a.machine_dia, a.machine_gg, a.body_part_id, a.stitch_length, a.yarn_lot,a.gsm,a.febric_description_id,a.color_id,a.yarn_count,a.brand_id, a.width from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form in (2,22) and b.status_active=1 and a.status_active=1 $all_barcode_no_cond");
	$production_rcv_data = array();
	foreach ($knit_prod_rcv_sql as $row) {
		$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
		$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
		$production_rcv_data[$row[csf("barcode_no")]]["yarn_lot"] = $row[csf("yarn_lot")];
		$production_rcv_data[$row[csf("barcode_no")]]["gsm"] = $row[csf("gsm")];
		$production_rcv_data[$row[csf("barcode_no")]]["febric_description_id"] = $row[csf("febric_description_id")];
		$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] = $row[csf("body_part_id")];
		$production_rcv_data[$row[csf("barcode_no")]]["stitch_length"] = $row[csf("stitch_length")];
		$production_rcv_data[$row[csf("barcode_no")]]["color_id"] = $row[csf("color_id")];
		$production_rcv_data[$row[csf("barcode_no")]]["yarn_count"] = $row[csf("yarn_count")];
		$production_rcv_data[$row[csf("barcode_no")]]["brand_id"] = $row[csf("brand_id")];
		$production_rcv_data[$row[csf("barcode_no")]]["width"] = $row[csf("width")];
	}

	$booking_with_order_ids = "";
	$booking_without_order_ids = "";
	foreach ($dataArray as $val) {
		if ($val[csf("is_sales")] != 1) {
			if ($val[csf("booking_without_order")] == 1) {
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["qnty"] +=  $val[csf("issue_qnty")];
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["buyer"] =  $booking_ref_buyer[$val[csf("po_breakdown_id")]]["buyer_id"];
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["stitch_length"] .=  $production_rcv_data[$val[csf("barcode_no")]]["stitch_length"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["machine_dia"] .=  $production_rcv_data[$val[csf("barcode_no")]]["machine_dia"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["machine_gg"] .=  $production_rcv_data[$val[csf("barcode_no")]]["machine_gg"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["yarn_lot"] .=  $production_rcv_data[$val[csf("barcode_no")]]["yarn_lot"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["gsm"] .=  $production_rcv_data[$val[csf("barcode_no")]]["gsm"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["febric_description_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["febric_description_id"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["body_part_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["body_part_id"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["color_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["color_id"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["yarn_count"] .=  $production_rcv_data[$val[csf("barcode_no")]]["yarn_count"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["brand_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["brand_id"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["width"] .=  $production_rcv_data[$val[csf("barcode_no")]]["width"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]['no_of_roll']++;

				//$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["po_breakdown_id"] .=  $val[csf("po_breakdown_id")].",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["barcode_no"] .=  $val[csf("barcode_no")] . ",";
				//$booking_without_order_ids.=$val[csf("po_breakdown_id")].",";
			} else {
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["qnty"] +=  $val[csf("issue_qnty")];
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["buyer"] =  $job_array[$val[csf("po_breakdown_id")]]["buyer"];
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["style_ref"] =  $job_array[$val[csf("po_breakdown_id")]]["style_ref"];

				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["int_ref"] =  $job_array[$val[csf("po_breakdown_id")]]["int_ref"];
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["order_no"] =  $job_array[$val[csf("po_breakdown_id")]]["po_number"];

				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["stitch_length"] .=  $production_rcv_data[$val[csf("barcode_no")]]["stitch_length"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["machine_dia"] .=  $production_rcv_data[$val[csf("barcode_no")]]["machine_dia"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["machine_gg"] .=  $production_rcv_data[$val[csf("barcode_no")]]["machine_gg"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["yarn_lot"] .=  $production_rcv_data[$val[csf("barcode_no")]]["yarn_lot"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["febric_description_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["febric_description_id"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["gsm"] .=  $production_rcv_data[$val[csf("barcode_no")]]["gsm"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["body_part_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["body_part_id"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["color_id"] =  $production_rcv_data[$val[csf("barcode_no")]]["color_id"];
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["yarn_count"] .=  $production_rcv_data[$val[csf("barcode_no")]]["yarn_count"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["brand_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["brand_id"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["width"] .=  $production_rcv_data[$val[csf("barcode_no")]]["width"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]['no_of_roll']++;

				//$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["po_breakdown_id"] .=  $val[csf("po_breakdown_id")].",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["barcode_no"] .=  $val[csf("barcode_no")] . ",";
				//$booking_with_order_ids.=$val[csf("po_breakdown_id")].",";
			}
		}
	}
	// echo "<pre>";print_r($data_array);die;
	/*$sql_chk_trnsf=sql_select("select b.barcode_no   from PRO_ROLL_DETAILS b, INV_ITEM_TRANSFER_MST c
	where b.mst_id=c.id $all_barcode_no_cond
	and b.entry_form in(82,83) and c.transfer_criteria in(1,4)
	and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	foreach($sql_chk_trnsf as $rows)
	{
		$trnsf_chk_arr[$rows[csf("barcode_no")]]=$rows[csf("barcode_no")];
	}
	// echo "<pre>";print_r($trnsf_chk_arr);die;
	$booking_with_order_ids=implode(",",array_unique(array_filter(explode(",",chop($booking_with_order_ids,",")))));*/
	//$booking_without_order_ids=implode(",",array_unique(array_filter(explode(",",chop($booking_without_order_ids,",")))));
	// echo $booking_with_order_ids.'<br>'.$booking_without_order_ids;die;

	/*$sql_booking_withorder=sql_select("select po_break_down_id,booking_no from wo_booking_dtls  where po_break_down_id in($booking_with_order_ids) and status_active=1 and is_deleted=0 and booking_type=1");
	foreach($sql_booking_withorder as $rows)
	{
		$booking_data_arr[$rows[csf("po_break_down_id")]]=$rows[csf("booking_no")];
	}*/
	$sql_production = sql_select("SELECT b.barcode_no,f.booking_no as program_no,c.machine_dia,c.machine_gg ,c.body_part_id ,e.booking_no
	from pro_roll_details b,inv_receive_master f, pro_grey_prod_entry_dtls c,ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e
	where b.mst_id=f.id  and b.dtls_id=c.id and f.id=c.mst_id and f.booking_id=d.id and d.mst_id=e.id $all_barcode_no_cond and f.receive_basis=2 and b.entry_form=2 and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0
	union all
	select b.barcode_no,null as program_no,c.machine_dia,c.machine_gg ,c.body_part_id ,f.booking_no
	from pro_roll_details b,inv_receive_master f, pro_grey_prod_entry_dtls c
	where b.mst_id=f.id  and b.dtls_id=c.id and f.id=c.mst_id $all_barcode_no_cond and f.receive_basis=1 and b.entry_form=2 and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and b.is_deleted=0 ");
	$prod_rcv_data = array();
	foreach ($sql_production as $row) {
		$prod_rcv_data[$row[csf("barcode_no")]] = $row[csf("booking_no")];
	}
	// echo "<pre>";print_r($prod_rcv_data);die;

	/*$sql_booking_withorder=sql_select("select b.po_break_down_id,b.booking_no, b.booking_type from wo_booking_mst a, wo_booking_dtls b
	where  a.booking_no=b.booking_no and a.job_no=b.job_no  and b.po_break_down_id in($booking_with_order_ids) and b.status_active=1 and b.is_deleted=0 and b.booking_type=1");
	foreach($sql_booking_withorder as $rows)
	{
		$booking_data_arr[$rows[csf("po_break_down_id")]]=$rows[csf("booking_no")];
	}*/

	/*$sql_booking_without_order=sql_select("select id,booking_no from wo_non_ord_samp_booking_mst  where id in($booking_without_order_ids) and status_active=1 and is_deleted=0");
	foreach($sql_booking_without_order as $rows)
	{
		$booking_data_arr[$rows[csf("id")]]=$rows[csf("booking_no")];
	}*/
	// echo "<pre>";print_r($booking_data_arr);die;
?>
	<div>
		<table width="900" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">


					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[3]; ?> Grey Fabric Roll Issue Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:16px; font-weight:bold;"><strong>Issue ID :</strong></td>
				<td width="175px" style="font-size:18px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
				<td width="125"><strong>Issue Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Issue Purpose:</strong></td>
				<td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
				<td width="130" style="font-size:16px"><strong>Dyeing Company:</strong> </td>
				<td style="font-size:18px; font-weight:bold;">
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
			</tr>
			<tr>
				<td><strong>Remarks:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			<tr>
				<td colspan="3" id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1540" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center">
				<th width="20">SL</th>
				<th width="50">Buyer</th>
				<th width="80">Style</th>
				<th width="80">Internal Ref.</th>
				<th width="100">Job No</th>
				<th width="100">Order/FSO No</th>
				<th width="100">Booking No</th>
				<th width="150">Item Description</th>
				<th width="110">Body Part</th>
				<th width="50">Stich Length</th>
				<th width="40">GSM</th>
				<th width="40">Fin. Dia</th>
				<th width="60">M/C Dia</th>
				<th width="60">M/C Gauge</th>
				<th width="70">Color</th>
				<th width="50">Count</th>
				<th width="150">Brand</th>
				<th width="50">Yarn Lot</th>
				<th width="40">Issue Qty</th>
				<th>No of Roll</th>
			</thead>
			<?
			$i = 1;
			foreach ($data_array as $order_type => $order_data) {
				foreach ($order_data as $job_no => $job_data) {
					foreach ($job_data as $prod_id => $prod_data) {
						foreach ($prod_data as $colors_id => $row) {
							$iss_barcode_nos = array_filter(array_unique(explode(",", $row['barcode_no'])));
							$booking_no = '';
							foreach ($iss_barcode_nos as $barcode) {
								if ($booking_no == '') $booking_no = $prod_rcv_data[$barcode];
								else $booking_no .= "," . $prod_rcv_data[$barcode];
								$booking_no = implode(',', array_filter(array_unique(explode(",", $booking_no))));
							}

							$count = '';
							$yarn_count = array_filter(array_unique(explode(",", $row['yarn_count'])));
							foreach ($yarn_count as $count_id) {
								if ($count == '') $count = $yarn_count_details[$count_id];
								else $count .= "," . $yarn_count_details[$count_id];
							}

							$febric_description_id = array_filter(array_unique(explode(",", chop($row['febric_description_id'], ","))));

							$description = "";
							foreach ($febric_description_id as $f_id) {
								if ($description == '') $description = $composition_arr[$f_id];
								else $description .= "," . $composition_arr[$f_id];
							}


							$bodyPartName = array_filter(array_unique(explode(",", chop($row['body_part_id'], ","))));

							$body_part_name = "";
							foreach ($bodyPartName as $f_id) {
								if ($body_part_name == '') $body_part_name = $body_part[$f_id];
								else $body_part_name .= "," . $body_part[$f_id];
							}

							$color_ids = array_filter(array_unique(explode(",", chop($row['color_id'], ","))));

							$color_names = "";
							foreach ($color_ids as $f_id) {
								if ($color_names == '') $color_names = $color_arr[$f_id];
								else $color_names .= "," . $color_arr[$f_id];
							}

							$brand_ids = array_filter(array_unique(explode(",", chop($row['brand_id'], ","))));

							$brand_names = "";
							foreach ($brand_ids as $f_id) {
								if ($brand_names == '') $brand_names = $brand_arr[$f_id];
								else $brand_names .= "," . $brand_arr[$f_id];
							}

							$mc_dia = implode(",", array_filter(array_unique(explode(",", chop($row['machine_dia'], ",")))));
							$mc_gg = implode(",", array_filter(array_unique(explode(",", chop($row['machine_gg'], ",")))));
							$gsm = implode(",", array_filter(array_unique(explode(",", chop($row['gsm'], ",")))));
							$yarn_lot = implode(",", array_filter(array_unique(explode(",", chop($row['yarn_lot'], ",")))));
							$stitch_length = implode(",", array_filter(array_unique(explode(",", chop($row['stitch_length'], ",")))));
							$width = implode(",", array_filter(array_unique(explode(",", chop($row['width'], ",")))));

			?>
							<tr>
								<td><? echo $i; ?></td>
								<td><? echo $buyer_array[$row["buyer"]]; ?></td>
								<td style="word-break:break-all;"><? echo $row["style_ref"]; ?></td>
								<td style="word-break:break-all;"><? echo $row["int_ref"]; ?></td>
								<td style="font-size:12px;"><? echo $job_no; ?></td>
								<td style="font-size:12px;"><? echo $row["order_no"]; ?></td>
								<td style="font-size:12px;"><? echo $booking_no; ?></td>
								<td><? echo $description; ?></td>
								<td style="word-break:break-all;"><? echo $body_part_name; ?></td>
								<td><? echo $stitch_length; ?></td>
								<td style="word-break:break-all;"><? echo $gsm; ?></td>
								<td style="word-break:break-all;"><? echo $width; ?></td>
								<td style="word-break:break-all;"><? echo $mc_dia; ?></td>
								<td style="word-break:break-all;"><? echo $mc_gg; ?></td>
								<td style="word-break:break-all;" title="<? echo $prod_id; ?>"><? echo $color_names; ?></td>
								<td style="word-break:break-all;" align="center"><? echo $count; ?></td>
								<td style="word-break:break-all;"><? echo $brand_names; ?></td>
								<td style="word-break:break-all;"><? echo $yarn_lot; ?></td>
								<td align="right"><? echo number_format($row['qnty'], 2); ?></td>
								<td style="word-break:break-all;" align="center"><? echo $data_array[$order_type][$job_no][$prod_id][$colors_id]['no_of_roll']; ?></td>
							</tr>
			<?
							$tot_qty += $row['qnty'];
							$total_roll += $data_array[$order_type][$job_no][$prod_id][$colors_id]['no_of_roll'];
							$i++;
						}
					}
				}
			}

			?>
			<tr>
				<td align="right" colspan="18"><strong>Total</strong></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
				<td align="center"><? echo $total_roll; ?></td>
			</tr>
		</table>
	</div>
	<div style="width: 1300px; margin-top: 10px">
		<?
		foreach ($coller_cuff_size_arr as $body_part_id => $body_part_arr) { ?>
			<div style="width: 500px; float: left;">
				<table cellspacing="0" width="400" border="1" rules="all" class="rpt_table">
					<thead bgcolor="#dddddd" align="center" style="font-size:12px">
						<tr>
							<th colspan="2"><? echo $body_part[$body_part_id]; ?> Details</th>
						</tr>
						<tr>
							<th width="150" style="text-align: center;">Size</th>
							<th width="100" style="text-align: center;">Pcs</th>
						</tr>
					</thead>
					<? $total_qty = "";
					foreach ($body_part_arr as $key => $value) { ?>
						<tr>
							<td style="text-align: center;"><? echo $key ?></td>
							<td style="text-align: center;"><? echo $value['qnty_pcs'];
															$total_qty += $value['qnty_pcs']; ?></td>
						</tr>

					<? } ?>
					<tr>
						<th>Total</th>
						<th><? echo $total_qty; ?></th>
					</tr>
				</table>
			</div>
		<? } ?>
	</div>
	<? echo signature_table(124, $company, "1340px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "issue_challan_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];
	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");

	$dataArray = sql_select("select count(b.id) as number_of_roll,sum(b.qnty) as total_qnty,a.issue_date, a.knit_dye_source, a.knit_dye_company from inv_issue_master a, pro_roll_details b where a.id=$update_id and b.entry_form=61 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.issue_date, a.knit_dye_source, a.knit_dye_company");

?>

	<div align="center">
		<table width="350" cellspacing="0">
			<tr>
				<td colspan="2" align="left" id="barcode_img_id"></td>
			</tr>
			<tr>
				<td width="130"><strong>Issue No :</strong></td>
				<td width="200"><? echo $txt_issue_no; ?></td>
			</tr>

			<tr>
				<td><strong>Issue Date:</strong></td>
				<td width="200"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>No of roll:</strong></td>
				<td width="200"><? echo $dataArray[0][csf('number_of_roll')]; ?></td>
			</tr>
			<tr>
				<td><strong>Total Quantity:</strong></td>
				<td width="200"><? echo $dataArray[0][csf('total_qnty')]; ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td>
				<td width="200"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
			</tr>
			<tr>
				<td width="130"><strong>Dyeing Company:</strong> </td>
				<td width="200" style="font-size:18px; font-weight:bold;">
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
			</tr>

		</table>
	</div>

	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "fabric_details_print_bpkw_gin8") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}
	$buyer_array = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	unset($data_array);


	$sql = "SELECT a.basis, a.program_no , b.po_breakdown_id, a.prod_id, b.qnty as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, b.barcode_no,b.booking_no,b.is_sales,b.booking_without_order,c.issue_date, c.issue_purpose,c.knit_dye_company, c.knit_dye_source, c.remarks, c.company_id
	from inv_grey_fabric_issue_dtls a, pro_roll_details b, inv_issue_master c
	where a.id=b.dtls_id and a.mst_id = c.id and b.mst_id = c.id and c.entry_form=61 and a.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0";

	// echo $sql;

	$dataArray = sql_select($sql);

	foreach ($dataArray as  $val) {
		if ($val[csf("is_sales")] == "1") {
			$all_sales_flag[$val[csf("is_sales")]] = $val[csf("is_sales")];
		} else {
			if ($val[csf("booking_without_order")] == 1) {
				$booking_id_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			} else {
				$po_ids_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			}
			$barcode_no_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
		}
	}

	$all_sales_flags = implode(",", array_filter(array_unique($all_sales_flag)));

	if ($all_sales_flags == "1") {
		echo "This Print Doesn't show sales order data";
		die;
	}



	$booking_id_arr = array_filter($booking_id_arr);

	if (count($booking_id_arr) > 0) {
		$book_sql = sql_select("select id, booking_no, buyer_id from wo_non_ord_samp_booking_mst where id in(" . implode(",", $booking_id_arr) . ")");
		foreach ($book_sql as $val) {
			$booking_ref[$val[csf("id")]] = $val[csf("booking_no")];
			$booking_ref_buyer[$val[csf("id")]]["buyer_id"] = $val[csf("buyer_id")];
		}
	}

	$po_ids_arr = array_filter($po_ids_arr);

	$job_array = array();
	if (count($po_ids_arr) > 0) {
		$job_sql = "SELECT a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and b.id in (" . implode(",", $po_ids_arr) . ")";

		$job_sql_result = sql_select($job_sql);
		foreach ($job_sql_result as $row) {
			$job_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
			$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
			$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			$job_array[$row[csf('id')]]['int_ref'] = $row[csf('grouping')];
			$job_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
		}
	}


	$barcode_no_arr = array_filter(array_unique($barcode_no_arr));
	if (count($barcode_no_arr) > 0) {
		$barcode_nos = implode(",", $barcode_no_arr);
		$transBar = $all_barcode_no_cond = "";

		if ($db_type == 2 && count($barcode_no_arr) > 999) {
			$barcode_no_arr_chunk = array_chunk($barcode_no_arr, 999);
			foreach ($barcode_no_arr_chunk as $chunk_arr) {
				$transBar .= " b.barcode_no in(" . implode(",", $chunk_arr) . ") or ";
			}

			$all_barcode_no_cond .= " and (" . chop($transBar, 'or ') . ")";
		} else {
			$all_barcode_no_cond = " and b.barcode_no in($barcode_nos)";
		}
	}

	if ($barcode_no_arr != "") {
		$production_sql = sql_select("select b.barcode_no, a.booking_id, a.booking_no, a.receive_basis, a.knitting_source, a.knitting_company, b.qc_pass_qnty_pcs, b.coller_cuff_size, b.dtls_id, c.body_part_id from inv_receive_master a, pro_roll_details b ,pro_grey_prod_entry_dtls c where c.id=b.dtls_id and a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and b.status_active=1 $all_barcode_no_cond");

		$coller_cuff_size_arr = array();
		foreach ($production_sql as $row) {
			if ($row[csf("coller_cuff_size")] != '') {
				$coller_cuff_size_arr[$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]['qnty_pcs'] += $row[csf("qc_pass_qnty_pcs")];
			}
		}
		//var_dump($coller_cuff_size_arr);
	}


	$knit_prod_rcv_sql = sql_select("SELECT b.barcode_no, a.machine_dia, a.machine_gg, a.body_part_id, a.stitch_length, a.yarn_lot,a.gsm,a.febric_description_id,a.color_id,a.yarn_count,a.brand_id, a.width from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form in (2,22) and b.status_active=1 and a.status_active=1 $all_barcode_no_cond");
	$production_rcv_data = array();
	foreach ($knit_prod_rcv_sql as $row) {
		$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
		$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
		$production_rcv_data[$row[csf("barcode_no")]]["yarn_lot"] = $row[csf("yarn_lot")];
		$production_rcv_data[$row[csf("barcode_no")]]["gsm"] = $row[csf("gsm")];
		$production_rcv_data[$row[csf("barcode_no")]]["febric_description_id"] = $row[csf("febric_description_id")];
		$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] = $row[csf("body_part_id")];
		$production_rcv_data[$row[csf("barcode_no")]]["stitch_length"] = $row[csf("stitch_length")];
		$production_rcv_data[$row[csf("barcode_no")]]["color_id"] = $row[csf("color_id")];
		$production_rcv_data[$row[csf("barcode_no")]]["yarn_count"] = $row[csf("yarn_count")];
		$production_rcv_data[$row[csf("barcode_no")]]["brand_id"] = $row[csf("brand_id")];
		$production_rcv_data[$row[csf("barcode_no")]]["width"] = $row[csf("width")];
	}

	$booking_with_order_ids = "";
	$booking_without_order_ids = "";
	foreach ($dataArray as $val) {
		if ($val[csf("is_sales")] != 1) {
			if ($val[csf("booking_without_order")] == 1) {
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["qnty"] +=  $val[csf("issue_qnty")];
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["buyer"] =  $booking_ref_buyer[$val[csf("po_breakdown_id")]]["buyer_id"];
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["stitch_length"] .=  $production_rcv_data[$val[csf("barcode_no")]]["stitch_length"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["machine_dia"] .=  $production_rcv_data[$val[csf("barcode_no")]]["machine_dia"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["machine_gg"] .=  $production_rcv_data[$val[csf("barcode_no")]]["machine_gg"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["yarn_lot"] .=  $production_rcv_data[$val[csf("barcode_no")]]["yarn_lot"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["gsm"] .=  $production_rcv_data[$val[csf("barcode_no")]]["gsm"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["febric_description_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["febric_description_id"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["body_part_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["body_part_id"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["color_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["color_id"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["yarn_count"] .=  $production_rcv_data[$val[csf("barcode_no")]]["yarn_count"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["brand_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["brand_id"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["width"] .=  $production_rcv_data[$val[csf("barcode_no")]]["width"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]['no_of_roll']++;

				//$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["po_breakdown_id"] .=  $val[csf("po_breakdown_id")].",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["barcode_no"] .=  $val[csf("barcode_no")] . ",";
				//$booking_without_order_ids.=$val[csf("po_breakdown_id")].",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["company"] +=  $val[csf("company_id")];
			} else {
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["qnty"] +=  $val[csf("issue_qnty")];
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["buyer"] =  $job_array[$val[csf("po_breakdown_id")]]["buyer"];
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["style_ref"] =  $job_array[$val[csf("po_breakdown_id")]]["style_ref"];

				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["int_ref"] =  $job_array[$val[csf("po_breakdown_id")]]["int_ref"];
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["order_no"] =  $job_array[$val[csf("po_breakdown_id")]]["po_number"];

				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["stitch_length"] .=  $production_rcv_data[$val[csf("barcode_no")]]["stitch_length"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["machine_dia"] .=  $production_rcv_data[$val[csf("barcode_no")]]["machine_dia"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["machine_gg"] .=  $production_rcv_data[$val[csf("barcode_no")]]["machine_gg"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["yarn_lot"] .=  $production_rcv_data[$val[csf("barcode_no")]]["yarn_lot"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["febric_description_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["febric_description_id"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["gsm"] .=  $production_rcv_data[$val[csf("barcode_no")]]["gsm"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["body_part_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["body_part_id"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["color_id"] =  $production_rcv_data[$val[csf("barcode_no")]]["color_id"];
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["yarn_count"] .=  $production_rcv_data[$val[csf("barcode_no")]]["yarn_count"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["brand_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["brand_id"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["width"] .=  $production_rcv_data[$val[csf("barcode_no")]]["width"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]['no_of_roll']++;

				//$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["po_breakdown_id"] .=  $val[csf("po_breakdown_id")].",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["barcode_no"] .=  $val[csf("barcode_no")] . ",";
				//$booking_with_order_ids.=$val[csf("po_breakdown_id")].",";

				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["company"] +=  $val[csf("company_id")];
			}
		}
	}
	// echo "<pre>";print_r($data_array);die;
	/*$sql_chk_trnsf=sql_select("select b.barcode_no   from PRO_ROLL_DETAILS b, INV_ITEM_TRANSFER_MST c
	where b.mst_id=c.id $all_barcode_no_cond
	and b.entry_form in(82,83) and c.transfer_criteria in(1,4)
	and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	foreach($sql_chk_trnsf as $rows)
	{
		$trnsf_chk_arr[$rows[csf("barcode_no")]]=$rows[csf("barcode_no")];
	}
	// echo "<pre>";print_r($trnsf_chk_arr);die;
	$booking_with_order_ids=implode(",",array_unique(array_filter(explode(",",chop($booking_with_order_ids,",")))));*/
	//$booking_without_order_ids=implode(",",array_unique(array_filter(explode(",",chop($booking_without_order_ids,",")))));
	// echo $booking_with_order_ids.'<br>'.$booking_without_order_ids;die;

	/*$sql_booking_withorder=sql_select("select po_break_down_id,booking_no from wo_booking_dtls  where po_break_down_id in($booking_with_order_ids) and status_active=1 and is_deleted=0 and booking_type=1");
	foreach($sql_booking_withorder as $rows)
	{
		$booking_data_arr[$rows[csf("po_break_down_id")]]=$rows[csf("booking_no")];
	}*/
	$sql_production = sql_select("SELECT b.barcode_no,f.booking_no as program_no,c.machine_dia,c.machine_gg ,c.body_part_id ,e.booking_no
	from pro_roll_details b,inv_receive_master f, pro_grey_prod_entry_dtls c,ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e
	where b.mst_id=f.id  and b.dtls_id=c.id and f.id=c.mst_id and f.booking_id=d.id and d.mst_id=e.id $all_barcode_no_cond and f.receive_basis=2 and b.entry_form=2 and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0
	union all
	select b.barcode_no,null as program_no,c.machine_dia,c.machine_gg ,c.body_part_id ,f.booking_no
	from pro_roll_details b,inv_receive_master f, pro_grey_prod_entry_dtls c
	where b.mst_id=f.id  and b.dtls_id=c.id and f.id=c.mst_id $all_barcode_no_cond and f.receive_basis=1 and b.entry_form=2 and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and b.is_deleted=0 ");
	$prod_rcv_data = array();
	foreach ($sql_production as $row) {
		$prod_rcv_data[$row[csf("barcode_no")]] = $row[csf("booking_no")];
	}
	// echo "<pre>";print_r($prod_rcv_data);die;

	/*$sql_booking_withorder=sql_select("select b.po_break_down_id,b.booking_no, b.booking_type from wo_booking_mst a, wo_booking_dtls b
	where  a.booking_no=b.booking_no and a.job_no=b.job_no  and b.po_break_down_id in($booking_with_order_ids) and b.status_active=1 and b.is_deleted=0 and b.booking_type=1");
	foreach($sql_booking_withorder as $rows)
	{
		$booking_data_arr[$rows[csf("po_break_down_id")]]=$rows[csf("booking_no")];
	}*/

	/*$sql_booking_without_order=sql_select("select id,booking_no from wo_non_ord_samp_booking_mst  where id in($booking_without_order_ids) and status_active=1 and is_deleted=0");
	foreach($sql_booking_without_order as $rows)
	{
		$booking_data_arr[$rows[csf("id")]]=$rows[csf("booking_no")];
	}*/
	// echo "<pre>";print_r($booking_data_arr);die;
?>
	<div>
		<table width="900" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">


					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[3]; ?> Grey Fabric Roll Issue Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:16px; font-weight:bold;"><strong>Issue ID :</strong></td>
				<td width="175px" style="font-size:18px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
				<td width="125"><strong>Issue Date:</strong></td>
				<td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Issue Purpose:</strong></td>
				<td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
				<td width="130" style="font-size:16px"><strong>Dyeing Company:</strong> </td>
				<td style="font-size:18px; font-weight:bold;">
					<?
					echo $company_arr[$dataArray[0][csf('company_id')]];
					
					?>
				</td>
			</tr>
			<tr>
				<td><strong>Remarks:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
			<tr>
				<td colspan="3" id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1540" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center">
				<th width="20">SL</th>
				<th width="50">Buyer</th>
				<th width="80">Style</th>
				<th width="80">Internal Ref.</th>
				<th width="100">Job No</th>
				<th width="100">Order/FSO No</th>
				<th width="100">Booking No</th>
				<th width="270">Item Description</th>
				<th width="110">Body Part</th>
				<th width="50">Stich Length</th>
				<th width="40">GSM</th>
				<th width="40">Fin. Dia</th>
				<th width="60">M/C Dia</th>
				<th width="60">M/C Gauge</th>
				<th width="100">Color</th>
				<th width="50">Count</th>
				<!-- <th width="150">Brand</th> -->
				<th width="50">Yarn Lot</th>
				<th width="40">Issue Qty</th>
				<th>No of Roll</th>
			</thead>
			<?
			$i = 1;
			foreach ($data_array as $order_type => $order_data) {
				foreach ($order_data as $job_no => $job_data) {
					foreach ($job_data as $prod_id => $prod_data) {
						foreach ($prod_data as $colors_id => $row) {
							$iss_barcode_nos = array_filter(array_unique(explode(",", $row['barcode_no'])));
							$booking_no = '';
							foreach ($iss_barcode_nos as $barcode) {
								if ($booking_no == '') $booking_no = $prod_rcv_data[$barcode];
								else $booking_no .= "," . $prod_rcv_data[$barcode];
								$booking_no = implode(',', array_filter(array_unique(explode(",", $booking_no))));
							}

							$count = '';
							$yarn_count = array_filter(array_unique(explode(",", $row['yarn_count'])));
							foreach ($yarn_count as $count_id) {
								if ($count == '') $count = $yarn_count_details[$count_id];
								else $count .= "," . $yarn_count_details[$count_id];
							}

							$febric_description_id = array_filter(array_unique(explode(",", chop($row['febric_description_id'], ","))));

							$description = "";
							foreach ($febric_description_id as $f_id) {
								if ($description == '') $description = $composition_arr[$f_id];
								else $description .= "," . $composition_arr[$f_id];
							}


							$bodyPartName = array_filter(array_unique(explode(",", chop($row['body_part_id'], ","))));

							$body_part_name = "";
							foreach ($bodyPartName as $f_id) {
								if ($body_part_name == '') $body_part_name = $body_part[$f_id];
								else $body_part_name .= "," . $body_part[$f_id];
							}

							$color_ids = array_filter(array_unique(explode(",", chop($row['color_id'], ","))));

							$color_names = "";
							foreach ($color_ids as $f_id) {
								if ($color_names == '') $color_names = $color_arr[$f_id];
								else $color_names .= "," . $color_arr[$f_id];
							}

							$brand_ids = array_filter(array_unique(explode(",", chop($row['brand_id'], ","))));

							$brand_names = "";
							foreach ($brand_ids as $f_id) {
								if ($brand_names == '') $brand_names = $brand_arr[$f_id];
								else $brand_names .= "," . $brand_arr[$f_id];
							}

							$mc_dia = implode(",", array_filter(array_unique(explode(",", chop($row['machine_dia'], ",")))));
							$mc_gg = implode(",", array_filter(array_unique(explode(",", chop($row['machine_gg'], ",")))));
							$gsm = implode(",", array_filter(array_unique(explode(",", chop($row['gsm'], ",")))));
							$yarn_lot = implode(",", array_filter(array_unique(explode(",", chop($row['yarn_lot'], ",")))));
							$stitch_length = implode(",", array_filter(array_unique(explode(",", chop($row['stitch_length'], ",")))));
							$width = implode(",", array_filter(array_unique(explode(",", chop($row['width'], ",")))));

							if($description!="" || $job_no!=""){

			?>
							<tr>
								<td><? echo $i; ?></td>
								<td><? echo $buyer_array[$row["buyer"]]; ?></td>
								<td style="word-break:break-all;"><? echo $row["style_ref"]; ?></td>
								<td style="word-break:break-all;"><? echo $row["int_ref"]; ?></td>
								<td style="font-size:12px;"><? echo $job_no; ?></td>
								<td style="font-size:12px;"><? echo $row["order_no"]; ?></td>
								<td style="font-size:12px;"><? echo $booking_no; ?></td>
								<td><? echo $description; ?></td>
								<td style="word-break:break-all;"><? echo $body_part_name; ?></td>
								<td><? echo $stitch_length; ?></td>
								<td style="word-break:break-all;"><? echo $gsm; ?></td>
								<td style="word-break:break-all;"><? echo $width; ?></td>
								<td style="word-break:break-all;"><? echo $mc_dia; ?></td>
								<td style="word-break:break-all;"><? echo $mc_gg; ?></td>
								<td style="word-break:break-all;" title="<? echo $prod_id; ?>"><? echo $color_names; ?></td>
								<td style="word-break:break-all;" align="center"><? echo $count; ?></td>
								<!-- <td style="word-break:break-all;"><?// echo $brand_names; ?></td> -->
								<td style="word-break:break-all;"><? echo $yarn_lot; ?></td>
								<td align="right"><? echo number_format($row['qnty'], 2); ?></td>
								<td style="word-break:break-all;" align="center"><? echo $data_array[$order_type][$job_no][$prod_id][$colors_id]['no_of_roll']; ?></td>
							</tr>
			<?
							$tot_qty += $row['qnty'];
							$total_roll += $data_array[$order_type][$job_no][$prod_id][$colors_id]['no_of_roll'];
							$i++;
						}
					}
					}
				}
			}

			?>
			<tr>
				<td align="right" colspan="17"><strong>Total</strong></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
				<td align="center"><? echo $total_roll; ?></td>
			</tr>
		</table>
	</div>
	<div style="width: 1300px; margin-top: 10px">
		<?
		foreach ($coller_cuff_size_arr as $body_part_id => $body_part_arr) { ?>
			<div style="width: 500px; float: left;">
				<table cellspacing="0" width="400" border="1" rules="all" class="rpt_table">
					<thead bgcolor="#dddddd" align="center" style="font-size:12px">
						<tr>
							<th colspan="2"><? echo $body_part[$body_part_id]; ?> Details</th>
						</tr>
						<tr>
							<th width="150" style="text-align: center;">Size</th>
							<th width="100" style="text-align: center;">Pcs</th>
						</tr>
					</thead>
					<? $total_qty = "";
					foreach ($body_part_arr as $key => $value) { ?>
						<tr>
							<td style="text-align: center;"><? echo $key ?></td>
							<td style="text-align: center;"><? echo $value['qnty_pcs'];
															$total_qty += $value['qnty_pcs']; ?></td>
						</tr>

					<? } ?>
					<tr>
						<th>Total</th>
						<th><? echo $total_qty; ?></th>
					</tr>
				</table>
			</div>
		<? } ?>
	</div>
	<? echo signature_table(124, $company, "1340px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}



if ($action == "roll_issue_challan_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

	$lib_mc_arr = array();
	$mc_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($mc_sql as $row) {
		$lib_mc_arr[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
	}

	$dataArray = sql_select("select issue_purpose, issue_date, location_id, knit_dye_source, knit_dye_company, batch_no,remarks from inv_issue_master where id=$update_id");
	$loc_dataArray = sql_select("select location_id from inv_receive_master where company_id=$company");



	$product_array = array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach ($product_sql as $row) {
		$product_array[$row[csf("id")]]['gsm'] = $row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width'] = $row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id'] = $row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom'] = $row[csf("unit_of_measure")];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	unset($data_array);


	$sql = " select a.basis, a.program_no , b.po_breakdown_id, a.prod_id, a.issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, b.barcode_no,b.booking_no,b.is_sales,b.booking_without_order,c.issue_date, c.issue_purpose,c.knit_dye_company, c.knit_dye_source, c.remarks
	from inv_grey_fabric_issue_dtls a, pro_roll_details b, inv_issue_master c
	where a.id=b.dtls_id and a.mst_id = c.id and b.mst_id = c.id and c.entry_form=61 and a.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0";

	$dataArray = sql_select($sql);

	foreach ($dataArray as  $val) {
		if ($val[csf("is_sales")] == "1") {
			$all_sales_flag[$val[csf("is_sales")]] = $val[csf("is_sales")];
		} else {
			if ($val[csf("booking_without_order")] == 1) {
				$booking_id_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			} else {
				$po_ids_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			}
			$barcode_no_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
		}
	}

	$all_sales_flags = implode(",", array_filter(array_unique($all_sales_flag)));

	if ($all_sales_flags == "1") {
		echo "This Print Doesn't show sales order data";
		die;
	}



	$booking_id_arr = array_filter($booking_id_arr);

	if (count($booking_id_arr) > 0) {
		$book_sql = sql_select("select id,buyer_id from wo_non_ord_samp_booking_mst where id in(" . implode(",", $booking_id_arr) . ")");
		foreach ($book_sql as $val) {
			//$booking_ref[$val[csf("id")]]["buyer_id"] =$val[csf("buyer_id")];
			$booking_ref[$val[csf("id")]] = $val[csf("buyer_id")];
		}
	}

	$po_ids_arr = array_filter($po_ids_arr);

	$job_array = array();
	if (count($po_ids_arr) > 0) {
		$job_sql = "SELECT a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and b.id in (" . implode(",", $po_ids_arr) . ")";

		$job_sql_result = sql_select($job_sql);
		foreach ($job_sql_result as $row) {
			$job_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
			$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
			$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
		}
	}


	$barcode_no_arr = array_filter(array_unique($barcode_no_arr));
	if (count($barcode_no_arr) > 0) {
		$barcode_nos = implode(",", $barcode_no_arr);
		$transBar = $all_barcode_no_cond = "";

		if ($db_type == 2 && count($barcode_no_arr) > 999) {
			$barcode_no_arr_chunk = array_chunk($barcode_no_arr, 999);
			foreach ($barcode_no_arr_chunk as $chunk_arr) {
				$transBar .= " b.barcode_no in(" . implode(",", $chunk_arr) . ") or ";
			}

			$all_barcode_no_cond .= " and (" . chop($transBar, 'or ') . ")";
		} else {
			$all_barcode_no_cond = " and b.barcode_no in($barcode_nos)";
		}
	}


	$knit_prod_rcv_sql = sql_select("select b.barcode_no, a.machine_dia, a.machine_gg, a.body_part_id, a.stitch_length, a.yarn_lot,a.gsm,a.febric_description_id,a.color_id,a.yarn_count,a.brand_id, a.width from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form in (2,22) and b.status_active=1 and a.status_active=1 $all_barcode_no_cond");
	$production_rcv_data = array();
	foreach ($knit_prod_rcv_sql as $row) {
		$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
		$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
		$production_rcv_data[$row[csf("barcode_no")]]["yarn_lot"] = $row[csf("yarn_lot")];
		$production_rcv_data[$row[csf("barcode_no")]]["gsm"] = $row[csf("gsm")];
		$production_rcv_data[$row[csf("barcode_no")]]["febric_description_id"] = $row[csf("febric_description_id")];
		$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] = $row[csf("body_part_id")];
		$production_rcv_data[$row[csf("barcode_no")]]["stitch_length"] = $row[csf("stitch_length")];
		$production_rcv_data[$row[csf("barcode_no")]]["color_id"] = $row[csf("color_id")];
		$production_rcv_data[$row[csf("barcode_no")]]["yarn_count"] = $row[csf("yarn_count")];
		$production_rcv_data[$row[csf("barcode_no")]]["brand_id"] = $row[csf("brand_id")];
		$production_rcv_data[$row[csf("barcode_no")]]["width"] = $row[csf("width")];
	}

	foreach ($dataArray as $val) {
		if ($val[csf("is_sales")] != 1) {
			if ($val[csf("booking_without_order")] == 1) {
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["qnty"] +=  $val[csf("issue_qnty")];
				//$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["buyer"] =  $booking_ref[$val[csf("po_breakdown_id")]]["buyer_id"];
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["buyer"] =  $booking_ref[$val[csf("po_breakdown_id")]];
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["stitch_length"] .=  $production_rcv_data[$val[csf("barcode_no")]]["stitch_length"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["machine_dia"] .=  $production_rcv_data[$val[csf("barcode_no")]]["machine_dia"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["machine_gg"] .=  $production_rcv_data[$val[csf("barcode_no")]]["machine_gg"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["yarn_lot"] .=  $production_rcv_data[$val[csf("barcode_no")]]["yarn_lot"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["gsm"] .=  $production_rcv_data[$val[csf("barcode_no")]]["gsm"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["febric_description_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["febric_description_id"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["body_part_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["body_part_id"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["color_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["color_id"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["yarn_count"] .=  $production_rcv_data[$val[csf("barcode_no")]]["yarn_count"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["brand_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["brand_id"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["width"] .=  $production_rcv_data[$val[csf("barcode_no")]]["width"] . ",";
				$data_array['non'][$booking_ref[$val[csf("po_breakdown_id")]]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]['no_of_roll']++;
			} else {
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["qnty"] +=  $val[csf("issue_qnty")];
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["buyer"] =  $job_array[$val[csf("po_breakdown_id")]]["buyer"];
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["style_ref"] =  $job_array[$val[csf("po_breakdown_id")]]["style_ref"];
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["stitch_length"] .=  $production_rcv_data[$val[csf("barcode_no")]]["stitch_length"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["machine_dia"] .=  $production_rcv_data[$val[csf("barcode_no")]]["machine_dia"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["machine_gg"] .=  $production_rcv_data[$val[csf("barcode_no")]]["machine_gg"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["yarn_lot"] .=  $production_rcv_data[$val[csf("barcode_no")]]["yarn_lot"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["febric_description_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["febric_description_id"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["gsm"] .=  $production_rcv_data[$val[csf("barcode_no")]]["gsm"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["body_part_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["body_part_id"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["color_id"] =  $production_rcv_data[$val[csf("barcode_no")]]["color_id"];
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["yarn_count"] .=  $production_rcv_data[$val[csf("barcode_no")]]["yarn_count"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["brand_id"] .=  $production_rcv_data[$val[csf("barcode_no")]]["brand_id"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]["width"] .=  $production_rcv_data[$val[csf("barcode_no")]]["width"] . ",";
				$data_array['order'][$job_array[$val[csf("po_breakdown_id")]]["job_no"]][$val[csf("prod_id")]][$production_rcv_data[$val[csf("barcode_no")]]["color_id"]]['no_of_roll']++;
			}
		}
	}


?>
	<div>
		<table width="1030" cellspacing="0" style="font-size: 12px;">
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[3]; ?>Grey Fabric Roll Issue Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:18px; font-weight:bold;"><strong>Challan No :</strong></td>
				<td width="175" style="font-size:18px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
				<td width="130" style="font-size:16px; font-weight:bold;"><strong>Dyeing Company:</strong> </td>
				<td style="font-size:18px; font-weight:bold;">
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
				<td><strong>Issue Purpose:</strong></td>
				<td><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			</tr>
			<tr>
				<td width="125"><strong>Delivery Date:</strong></td>
				<td width="175"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
				<td><strong>Location:</strong></td>
				<td><? echo $location_arr[$loc_dataArray[2][csf('location_id')]]; ?></td>
				<td><strong>Batch Number:</strong></td>
				<td><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td>
				<td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				<td><strong>Barcode:</strong></td>
				<td colspan="3" id="barcode_img_id"></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Remarks:</strong></td>
				<td><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1700" class="rpt_table" style="font-family: tahoma; font-size: 12px;">
			<thead bgcolor="#dddddd" align="center" style="font-size:12px">
				<th width="30">SL</th>
				<th width="60">System ID</th>
				<th width="100">PO Buyer /<br>Style</th>
				<th width="110">Sales Order No</th>
				<th width="110">Prog./ Book. No</th>
				<th width="80">Production Basis</th>
				<th width="80">Knitting Company</th>
				<th width="60">Yarn Count</th>
				<th width="80">Yarn Brand</th>
				<th width="60">Lot No</th>
				<th width="80">Fab Color</th>
				<th width="80">Color Range</th>
				<th width="100">Fabrication</th>
				<th width="60">Stich Length</th>
				<th width="60">Spandex S.L</th>
				<th width="60">Fin GSM</th>
				<th width="40">Fab. Dia</th>
				<th width="40">MC. No</th>
				<th width="40">MC. Dia</th>
				<th width="40">MC. Gauge</th>
				<th width="80">Barcode No</th>
				<th width="100">Program Batch No</th>
				<th width="40">Roll No</th>
				<th>QC Pass Qty</th>
			</thead>
			<?
			$i = 1;
			$tot_qty = 0;
			$all_order_id = $all_program_no = $all_barcode_no = "";
			$roll_details = array();
			$sql = "select a.mst_id, a.basis, a.program_no, a.prod_id, a.issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, b.roll_no, b.barcode_no, b.po_breakdown_id, b.qnty as quantity, b.booking_no, b.booking_without_order from inv_issue_master c,inv_grey_fabric_issue_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=c.id and a.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$result = sql_select($sql);
			foreach ($result as $row) {
				$all_order_id	.= $row[csf("po_breakdown_id")] . ",";
				$all_barcode_no	.= $row[csf("barcode_no")] . ",";
				$roll_details[$row[csf("barcode_no")]]["system_id"] = $row[csf("mst_id")];

				$roll_details[$row[csf("barcode_no")]]["basis"] 	= $row[csf("basis")];
				$roll_details[$row[csf("barcode_no")]]["brand_id"]  = $row[csf("brand_id")];
				$roll_details[$row[csf("barcode_no")]]["yarn_lot"]  = $row[csf("yarn_lot")];
				$roll_details[$row[csf("barcode_no")]]["yarn_count"] = $row[csf("yarn_count")];
				$roll_details[$row[csf("barcode_no")]]["prod_id"]   = $row[csf("prod_id")];
				$roll_details[$row[csf("barcode_no")]]["stitch_length"]  = $row[csf("stitch_length")];
			}
			$all_order_id	= implode(",", array_unique(explode(",", chop($all_order_id, ","))));
			$all_barcode_no	= implode(",", array_unique(explode(",", chop($all_barcode_no, ","))));
			$sales_barcode_no = "";
			if ($all_barcode_no != "") {
				$production_sql = sql_select("select a.booking_id, a.booking_no, a.receive_basis,a.knitting_company,a.knitting_source,a.recv_number, c.barcode_no,c.qnty,c.is_sales,c.po_breakdown_id,b.machine_dia,b.machine_gg,b.machine_no_id,b.prod_id, b.color_id,c.roll_no,b.width,b.gsm,b.color_range_id,b.febric_description_id,b.brand_id from  inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and b.status_active=1 and c.status_active=1 and c.barcode_no in($all_barcode_no)");

				$production_data_arr = array();
				$sales_ids = $po_ids = "";
				foreach ($production_sql as $pr_row) {
					$system_id 		= $roll_details[$pr_row[csf("barcode_no")]]["system_id"];
					$yarn 			= $roll_details[$pr_row[csf("barcode_no")]]["prod_id"];
					$yarn_count 	= $roll_details[$pr_row[csf("barcode_no")]]["yarn_count"];
					$yarn_lot 		= $roll_details[$pr_row[csf("barcode_no")]]["yarn_lot"];
					$stitch_length 	= $roll_details[$pr_row[csf("barcode_no")]]["stitch_length"];
					$booking_ref 	= explode("-", $pr_row[csf("booking_no")]);

					$production_data_arr[$pr_row[csf("machine_no_id")]][$pr_row[csf("knitting_company")]][$yarn_lot][$pr_row[csf("width")]][$pr_row[csf("barcode_no")]] = $pr_row[csf("po_breakdown_id")] . "**" . $pr_row[csf("booking_no")] . "**" . $pr_row[csf("receive_basis")] . "**" . $yarn_count . "**" . $yarn_lot . "**" . $pr_row[csf("color_id")] . "**" . $stitch_length . "**" . $pr_row[csf("gsm")] . "**" . $pr_row[csf("width")] . "**" . $pr_row[csf("machine_no_id")] . "**" . $pr_row[csf("machine_dia")] . "**" . $pr_row[csf("machine_gg")] . "**" . $pr_row[csf("barcode_no")] . "**" . $pr_row[csf("qnty")] . "**" . $pr_row[csf("roll_no")] . "**" . $pr_row[csf("is_sales")] . "**" . $system_id . "**" . $pr_row[csf("color_range_id")] . "**" . $pr_row[csf("febric_description_id")] . "**" . $pr_row[csf("knitting_source")] . "**" . $pr_row[csf("brand_id")];

					if ($pr_row[csf("receive_basis")] == 2) {
						$all_program_no .= $pr_row[csf("booking_id")] . ",";
					}
					if ($pr_row[csf("receive_basis")] == 1) {
						$all_booking_no .= $pr_row[csf("booking_no")] . ",";
					}

					if ($pr_row[csf("is_sales")] == 1) {
						$sales_ids .= $pr_row[csf("po_breakdown_id")] . ",";
						$sales_barcode_no .= $pr_row[csf("barcode_no")] . ",";
					} else {
						$po_ids .= $pr_row[csf("po_breakdown_id")] . ",";
					}
				}
			}
			$po_ids = rtrim($po_ids, ",");
			$sales_ids = rtrim($sales_ids, ",");
			$sales_barcode_no = chop($sales_barcode_no, ",");

			$job_array = array();
			$job_sql = "select a.style_ref_no,a.job_no,a.buyer_name,b.id,b.po_number,b.file_no,b.grouping from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_ids)";
			$job_sql_result = sql_select($job_sql);
			foreach ($job_sql_result as $row) {
				$job_array[$row[csf('id')]]['job']		= $row[csf('job_no')];
				$job_array[$row[csf('id')]]['buyer']	= $row[csf('buyer_name')];
				$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$job_array[$row[csf('id')]]['po']		= $row[csf('po_number')];
			}

			$booking_array = array();
			$booking_sql = "select a.booking_no,a.buyer_id,b.style_ref_no from wo_booking_mst a, wo_booking_dtls c,wo_po_details_master b where c.job_no=b.job_no and a.booking_no=c.booking_no and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no,a.buyer_id,b.style_ref_no";
			$booking_sql_result = sql_select($booking_sql);
			foreach ($booking_sql_result as $row) {
				$booking_array[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
				$booking_array[$row[csf('booking_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
			}
			$sales_array = array();
			//$sales_barcode_no
			if ($sales_barcode_no == "") $sales_barcode_no = 0;
			$sales_sql = "select a.id, a.job_no, a.sales_booking_no, a.within_group, a.buyer_id, a.style_ref_no, b.barcode_no
			from fabric_sales_order_mst a, pro_roll_details b
			where a.id=b.po_breakdown_id and b.barcode_no in($sales_barcode_no) and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$sales_sql_result = sql_select($sales_sql);
			foreach ($sales_sql_result as $row) {
				$sales_array[$row[csf('barcode_no')]]['job']			= $row[csf('job_no')];
				$sales_array[$row[csf('barcode_no')]]['booking_no']		= $row[csf('sales_booking_no')];
				$sales_array[$row[csf('barcode_no')]]['within_group']	= $row[csf('within_group')];
				$sales_array[$row[csf('barcode_no')]]['buyer_id']		= $row[csf('buyer_id')];
				$sales_array[$row[csf('barcode_no')]]['style_ref_no']	= $row[csf('style_ref_no')];
			}

			$all_program_no = implode(",", array_unique(explode(",", chop($all_program_no, ","))));
			if ($all_program_no != "") {

				$program_sql = sql_select("SELECT a.booking_id,b.spandex_stitch_length,c.booking_no,c.within_group, b.id as program_no, b.batch_no from inv_receive_master a,ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c where a.booking_id=b.id and b.mst_id=c.id and a.entry_form=2 and a.receive_basis=2 and a.booking_id in($all_program_no)");

				$prog_book_arr = array();
				$program_batch_arr = array();
				foreach ($program_sql as $row) {
					$booking_ref = $row[csf("booking_no")];
					$prog_book_arr[$row[csf("booking_id")]] = $row[csf("booking_no")];
					$spandexsl_arr[$row[csf("booking_id")]]['spandexsl'] = $row[csf('spandex_stitch_length')];
					$program_batch_arr[$row[csf("program_no")]] =  $row[csf("batch_no")];
				}
			}
			$grand_tot_roll = $grand_tot_qty = 0;
			rsort($production_data_arr);
			foreach ($production_data_arr as $machine_id => $machine_row) {
				foreach ($machine_row as $knitting_company => $knit_row) {
					foreach ($knit_row as $lot => $lot_row) {
						foreach ($lot_row as $fab_dia => $fab_dia_row) {
							$tot_roll = $tot_qty = 0;
							foreach ($fab_dia_row as $barcode => $barcode_info) {
								$barcode_row = explode("**", $barcode_info);
								$job_no = ($barcode_row[15] == 1) ? $sales_array[$barcode]['job'] : $job_array[$barcode_row[0]]['job'];

								if ($barcode_row[15] == 1) { // is sales
									$within_group = $sales_array[$barcode]['within_group'];
									$buyer = ($within_group == 1) ? $buyer_array[$booking_array[$sales_array[$barcode]['booking_no']]['buyer_id']] : $buyer_array[$sales_array[$barcode]['buyer_id']];
									$style = ($within_group == 1) ? $booking_array[$sales_array[$barcode]['booking_no']]['style_ref_no'] : $sales_array[$barcode]['style_ref_no'];
								} else {
									$buyer = $buyer_array[$job_array[$barcode_row[0]]['buyer']];
									$style = $job_array[$barcode_row[0]]['style_ref'];
								}

								$book_prog_challan = "";
								if ($barcode_row[2] == 1) {
									$book_prog_challan = "<b>B.N</b>. " . $barcode_row[1] . "<br>";
								} else {
									//$book_prog_challan="<b>B.N</b>. ".$prog_book_arr[$barcode_row[1]]."<br>";
									$book_prog_challan = "<b>B.N</b>. " . $sales_array[$barcode]['booking_no'] . "<br>";
									$book_prog_challan .= "<b>P.N</b>. " . $barcode_row[1] . "<br>";
									$program_batch = $program_batch_arr[$barcode_row[1]];
								}
								$color_names = "";
								$color_array = explode(",", $barcode_row[5]);
								foreach ($color_array as $color_row) {
									$color_names .= $color_arr[$color_row]  . ",";
								}
								$count = '';
								$yarn_count = explode(",", $barcode_row[3]);
								foreach ($yarn_count as $count_id) {
									if ($count == '') $count = $yarn_count_details[$count_id];
									else $count .= "," . $yarn_count_details[$count_id];
								}
								$knitting_com = ($barcode_row[19] == 1) ? $company_arr[$knitting_company] : $supplier_arr[$knitting_company];
								$spandexsl = $spandexsl_arr[$barcode_row[1]]['spandexsl'];
								$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan", 4 => "Sales Order");

								//echo $barcode_row[1];
			?>
								<tr style="font-size:11px">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="60" align="center"><? echo $barcode_row[16]; ?></td>
									<td width="100" style="word-break:break-all;"><b>B:</b> <? echo $buyer . "<br /><b>S:</b> " . $style; ?></td>
									<td width="110" style="word-break:break-all; text-align: center;"><? echo $job_no; ?></td>
									<td width="110"><? echo $book_prog_challan; ?></td>
									<td width="80" align="center"><? echo $receive_basis[$barcode_row[2]]; ?></td>
									<td width="80" align="center"><? echo $knitting_com; ?></td>
									<td width="80" style="word-break:break-all;" align="center"><? echo $count; //$barcode_row[3]; 
																								?></td>
									<td width="80" style="word-break:break-all;" align="center"><? echo $brand_arr[$barcode_row[20]]; ?></td>
									<td width="60" style="word-break:break-all;" align="center"><? echo $barcode_row[4]; ?></td>
									<td width="80" style="word-break:break-all;"><? echo rtrim($color_names, ","); ?></td>
									<td width="80" style="word-break:break-all;" align="center"><? echo $color_range[$barcode_row[17]]; ?></td>
									<td width="100" align="center"><? echo $composition_arr[$barcode_row[18]]; ?></td>
									<td width="60" align="center"><? echo $barcode_row[6]; ?></td>
									<td width="60" style="word-break:break-all;" align="center"><? echo $spandexsl; ?></td>
									<td width="60" style="word-break:break-all;" align="center"><? echo $barcode_row[7]; ?></td>
									<td width="40" style="word-break:break-all;" align="center"><? echo $barcode_row[8]; ?></td>
									<td width="40" align="center"><? echo $lib_mc_arr[$barcode_row[9]]['no']; ?></td>
									<td width="40" style="word-break:break-all;" align="center"><? echo $barcode_row[10]; ?></td>
									<td width="40" align="center" style="word-break:break-all;"><? echo $barcode_row[11]; ?></td>
									<td width="70" align="center"><? echo $barcode_row[12]; ?></td>
									<td width="100" align="center"><? echo $program_batch; ?></td>
									<td width="40" style="word-break:break-all;" align="center"><? echo $barcode_row[14]; ?></td>
									<td align="right"><? echo number_format($barcode_row[13], 2); ?></td>
								</tr>
							<?php
								$tot_roll = $tot_roll + 1; // roll total
								$tot_qty += $barcode_row[13]; // qc pass qnty total
								$i++;
							}
							?>
							<tr style="font-size:12px; background-image:linear-gradient(#ccc, #fefefe);">
								<td align="right" colspan="22"><strong>Total:</strong></td>
								<td align="center"><strong><? echo number_format($tot_roll); ?></strong></td>
								<td align="right"><strong><? echo number_format($tot_qty, 2, '.', ''); ?></strong></td>
							</tr>
			<?php
							$grand_tot_roll += $tot_roll; // roll grand total
							$grand_tot_qty += $tot_qty; // qc pass qnty grand total
						}
					}
				}
			}
			?>
			<tr style="font-size:12px;">
				<td align="right" colspan="22"><strong>Grand Total:</strong></td>
				<td align="center"><strong><? echo number_format($grand_tot_roll); ?></strong></td>
				<td align="right"><strong><? echo number_format($grand_tot_qty, 2, '.', ''); ?></strong></td>
			</tr>
		</table>
	</div>
	<? echo signature_table(124, $company, "1700px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "roll_issue_challan_print2") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$lib_store_name = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$smn_booking_buyer_arr = return_library_array("select booking_no, buyer_id from wo_non_ord_samp_booking_mst", "booking_no", "buyer_id");

	$production_arr = array();
	$production_del_sql = sql_select("select id, sys_number_prefix_num, sys_number, knitting_source, knitting_company, location_id from pro_grey_prod_delivery_mst where entry_form=56 and company_id=$company");
	foreach ($production_del_sql as $row) {
		$production_arr[$row[csf('id')]]['sys'] = $row[csf('sys_number')];
		$production_arr[$row[csf('id')]]['sys_num'] = $row[csf('sys_number_prefix_num')];
		$production_arr[$row[csf('id')]]['unit'] = $row[csf('location_id')];
		$production_arr[$row[csf('id')]]['knit_sou'] = $row[csf('knitting_source')];
		$production_arr[$row[csf('id')]]['knit_com'] = $row[csf('knitting_company')];
	}


	$lib_mc_arr = array();
	$mc_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($mc_sql as $row) {
		$lib_mc_arr[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
	}


	$dataArray = sql_select("select issue_purpose,issue_date,knit_dye_source,knit_dye_company,batch_no,remarks from inv_issue_master where id=$update_id");

	$product_array = array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach ($product_sql as $row) {
		$product_array[$row[csf("id")]]['gsm'] = $row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width'] = $row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id'] = $row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom'] = $row[csf("unit_of_measure")];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$grey_issue_basis = array(0 => "Independent", 1 => "PI", 2 => "Booking", 3 => "Knitting Plan", 9 => "Delivery");
?>
	<div>
		<table width="1030" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u>Grey Fabric Roll Issue Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:18px; font-weight:bold;"><strong>Issue ID :</strong></td>
				<td width="175" style="font-size:18px;"><? echo $txt_issue_no; ?></td>

				<td width="125"><strong>Issue Date:</strong></td>
				<td width="175"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td width="130"><strong>Dyeing Company:</strong> </td>
				<td style="font-size:18px;">
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
				<td><strong>Issue Purpose:</strong></td>
				<td><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1130" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center" style="font-size:12px">
				<th width="20">SL</th>
				<th width="80">Buyer</th>
				<th width="80">Style</th>
				<th width="80">Internal Ref.No</th>
				<th width="80">Job No</th>
				<th width="80">Body Part</th>
				<th width="135">Item Description</th>
				<th width="50">GSM</th>
				<th width="50">Fin. Dia</th>
				<th width="65">M/C Dia</th>
				<th width="70">M/C Gauge</th>
				<th width="60">Color</th>
				<th width="60">Count</th>
				<th width="35">Brand</th>
				<th width="50">Yarn Lot</th>
				<th width="50">Issue Qty</th>
				<th width="55">No of Roll</th>
			</thead>
			<?
			$i = 1;
			$tot_qty = 0;
			$sql = "select a.basis, a.program_no, a.prod_id, a.issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id,a.store_name, b.roll_id, b.roll_no, b.barcode_no, b.po_breakdown_id, b.qnty as quantity, b.booking_no, b.booking_without_order from inv_grey_fabric_issue_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61  and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			//echo $sql; die;
			$result = sql_select($sql);

			/*###########  according to saeed vai decission booking no show from booking table againest order   ###############*/
			$all_order_id = $all_program_no = $all_barcode_no = "";
			foreach ($result as $row) {
				$all_order_id .= $row[csf("po_breakdown_id")] . ",";
				$all_barcode_no .= $row[csf("barcode_no")] . ",";
			}
			$all_order_id = implode(",", array_unique(explode(",", chop($all_order_id, ","))));
			$all_barcode_no = implode(",", array_unique(explode(",", chop($all_barcode_no, ","))));

			$job_array = array();
			if ($all_order_id != "") {
				$job_sql = "select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_order_id)";
				$job_sql_result = sql_select($job_sql);
				foreach ($job_sql_result as $row) {
					$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
					$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
					$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
					$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
					$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
					$job_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				}
			}

			if ($all_barcode_no != "") {
				$production_sql = sql_select("select b.barcode_no, a.booking_id, a.booking_no, a.receive_basis, a.knitting_source, a.knitting_company, b.qc_pass_qnty_pcs, b.coller_cuff_size, b.dtls_id, c.body_part_id from inv_receive_master a, pro_roll_details b ,pro_grey_prod_entry_dtls c where c.id=b.dtls_id and a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and b.status_active=1 and b.barcode_no in($all_barcode_no)");

				$production_data_arr = array();
				$coller_cuff_size_arr = array();
				foreach ($production_sql as $row) {
					$booking_ref = explode("-", $row[csf("booking_no")]);
					$production_data_arr[$row[csf("barcode_no")]]["booking_no"] = abs($booking_ref[3]);
					$production_data_arr[$row[csf("barcode_no")]]["receive_basis"] = $row[csf("receive_basis")];
					$production_data_arr[$row[csf("barcode_no")]]["booking_id"] = $row[csf("booking_id")];
					$production_data_arr[$row[csf("barcode_no")]]["knitting_source"] = $row[csf("knitting_source")];
					$production_data_arr[$row[csf("barcode_no")]]["knitting_company"] = $row[csf("knitting_company")];
					if ($row[csf("receive_basis")] == 2) {
						$all_program_no .= $row[csf("booking_id")] . ",";
					}
					if ($row[csf("coller_cuff_size")] != '') {
						$coller_cuff_size_arr[$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]['qnty_pcs'] += $row[csf("qc_pass_qnty_pcs")];
					}
				}

				$production_store_sql = sql_select("select b.barcode_no,a.store_id from  inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=58 and b.entry_form=58 and b.status_active=1 and b.barcode_no in($all_barcode_no)");
				$production_store_data_arr = array();
				foreach ($production_store_sql as $row) {
					$production_store_data_arr[$row[csf("barcode_no")]]["store_id"] = $row[csf("store_id")];
				}
				$prodArr = sql_select("select b.id, a.machine_no_id, a.machine_dia, a.machine_gg , a.body_part_id from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=2 and b.barcode_no in($all_barcode_no)");
				foreach ($prodArr as $row) {
					$mc_id_arr[$row[csf('id')]] = $row[csf('machine_no_id')];
					$gg_dia_arr[$row[csf('id')]]['dia'] = $row[csf('machine_dia')];
					$gg_dia_arr[$row[csf('id')]]['gauge'] = $row[csf('machine_gg')];
					$body_part_arr[$row[csf('id')]]['body_part'] = $row[csf('body_part_id')];
				}

				$is_transfer = sql_select("select id,barcode_no, is_transfer from pro_roll_details where entry_form=180 and status_active=1 and barcode_no in($all_barcode_no)");
				$is_transfer_arr = array();
				foreach ($is_transfer as $row) {
					$is_transfer_arr[$row[csf("barcode_no")]]["is_tans"] = $row[csf("is_transfer")];
				}
			}

			$all_program_no = implode(",", array_unique(explode(",", chop($all_program_no, ","))));

			if ($all_program_no != "") {

				$program_sql = sql_select("select a.booking_id, c.booking_no from inv_receive_master a,  ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c
					where a.booking_id=b.id and b.mst_id=c.id and a.entry_form=2 and a.receive_basis=2 and a.booking_id in($all_program_no)");
				$prog_book_arr = array();
				foreach ($program_sql as $row) {
					$booking_ref = explode("-", $row[csf("booking_no")]);
					$prog_book_arr[$row[csf("booking_id")]] = abs($booking_ref[3]);
				}
			}



			/*###########  according to saeed vai decission booking no show from booking table againest order end   ###############*/

			foreach ($result as $row) {
				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
				}
				$mc_id = $mc_id_arr[$row[csf("roll_id")]];

				$file_ref_ord = "";
				$job_buyer_style = "";
				$buyer = "";
				$job = "";
				$style = "";
				$ref_no = "";
				$boby_part_id = "";
				if ($row[csf('booking_without_order')] == 1) {
					$file_ref_ord = 'F : <br>R : <br>B : ' . $row[csf('booking_no')];
					$buyer = $buyer_array[$smn_booking_buyer_arr[$row[csf('booking_no')]]];
					$job = "";
					$style = "";
					$ref_no = "";
					$boby_part_id = "";
				} else {

					$file_ref_ord = 'F : ' . $job_array[$row[csf('po_breakdown_id')]]['file_no'] . '<br>R : ' . $job_array[$row[csf('po_breakdown_id')]]['grouping'] . '<br>O : ' . $job_array[$row[csf('po_breakdown_id')]]['po'];
					$buyer = $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']];
					$job = $job_array[$row[csf('po_breakdown_id')]]['job'];
					$style = $job_array[$row[csf('po_breakdown_id')]]['style_ref'];
					$ref_no = $job_array[$row[csf('po_breakdown_id')]]['grouping'];
					$boby_part_id = $job_array[$row[csf('po_breakdown_id')]]['boby_part_id'];
					//echo $boby_part_id; die;
				}

				$knit_party = "";
				$knit_source = $production_data_arr[$row[csf("barcode_no")]]["knitting_source"];
				if ($knit_source == 1) $knit_party = $company_array[$production_data_arr[$row[csf("barcode_no")]]["knitting_company"]]['shortname'];
				else if ($knit_source == 3) $knit_party = $supplier_arr[$production_data_arr[$row[csf("barcode_no")]]["knitting_company"]];

				$color = '';
				$color_id = explode(",", $row[csf("color_id")]);
				foreach ($color_id as $val) {
					if ($val > 0) $color .= $color_arr[$val] . ",";
				}
				$color = chop($color, ',');

				$is_transfer = $is_transfer_arr[$row[csf("barcode_no")]]["is_tans"];
				if ($is_transfer == 5) $tras_cond = "(T)";
				else $tras_cond = "";

				$book_prog_challan = "";
				if ($production_data_arr[$row[csf("barcode_no")]]["receive_basis"] == 1) {
					$book_prog_challan = "B.N.-" . $production_data_arr[$row[csf("barcode_no")]]['booking_no'] . $tras_cond . "<br>";
				} else {
					$book_prog_challan = "B.N.-" . $prog_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']] . "<br>";
					$book_prog_challan .= "P.N.-" . $production_data_arr[$row[csf("barcode_no")]]['booking_id'] . "<br>";
				}
				$book_prog_challan .= "D.C.-" . $production_arr[$row[csf("program_no")]]['sys_num'];

			?>
				<tr style="font-size:11px">
					<td><? echo $i; ?></td>
					<td><? echo $buyer; ?> </td>
					<td><? echo $style; ?> </td>
					<td><? echo $ref_no; ?> </td>
					<td><? echo $job; ?> </td>
					<td><? echo $body_part[$body_part_arr[$row[csf("roll_id")]]['body_part']]; ?> </td>
					<td><? echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']]; ?></td>
					<td><? echo $product_array[$row[csf("prod_id")]]['gsm'] ?></td>
					<td><? echo $product_array[$row[csf("prod_id")]]['dia_width'] ?></td>
					<td><? echo $gg_dia_arr[$row[csf("roll_id")]]['dia'] ?></td>
					<td><? echo $gg_dia_arr[$row[csf("roll_id")]]['gauge'] ?></td>
					<td><? echo $color; ?></td>
					<td><? echo $count; ?></td>
					<td><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
					<td><? echo $row[csf('yarn_lot')]; ?></td>
					<td><? echo number_format($row[csf('quantity')], 2); ?></td>
					<td><? echo $row[csf('roll_no')] ?></td>
				</tr>
			<?
				$tot_roll += $row[csf('roll_no')];
				$tot_qty += $row[csf('quantity')];
				$i++;
			}
			?>
			<tr style="font-size:12px">
				<td align="right" colspan="15"><strong>Total</strong></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
			</tr>
		</table>
	</div>
	<div style="width: 1300px; margin-top: 10px">
		<?
		foreach ($coller_cuff_size_arr as $body_part_id => $body_part_arr) { ?>
			<div style="width: 500px; float: left;">
				<table cellspacing="0" width="400" border="1" rules="all" class="rpt_table">
					<thead bgcolor="#dddddd" align="center" style="font-size:12px">
						<tr>
							<th colspan="2"><? echo $body_part[$body_part_id]; ?> Details</th>
						</tr>
						<tr>
							<th width="150" style="text-align: center;">Size</th>
							<th width="100" style="text-align: center;">Pcs</th>
						</tr>
					</thead>
					<? $total_qty = "";
					foreach ($body_part_arr as $key => $value) { ?>
						<tr>
							<td style="text-align: center;"><? echo $key ?></td>
							<td style="text-align: center;"><? echo $value['qnty_pcs'];
															$total_qty += $value['qnty_pcs']; ?></td>
						</tr>

					<? } ?>
					<tr>
						<th>Total</th>
						<th><? echo $total_qty; ?></th>
					</tr>
				</table>
			</div>
		<? } ?>
	</div>

	<? echo signature_table(124, $company, "930px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "roll_issue_challan_print3") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	//var_dump($data);

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$lib_store_name = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");


	$production_arr = array();
	$production_del_sql = sql_select("select id, sys_number_prefix_num, sys_number, knitting_source, knitting_company, location_id from pro_grey_prod_delivery_mst where entry_form=56 and company_id=$company");
	foreach ($production_del_sql as $row) {
		$production_arr[$row[csf('id')]]['sys'] = $row[csf('sys_number')];
		$production_arr[$row[csf('id')]]['sys_num'] = $row[csf('sys_number_prefix_num')];
		$production_arr[$row[csf('id')]]['unit'] = $row[csf('location_id')];
		$production_arr[$row[csf('id')]]['knit_sou'] = $row[csf('knitting_source')];
		$production_arr[$row[csf('id')]]['knit_com'] = $row[csf('knitting_company')];
	}


	$lib_mc_arr = array();
	$mc_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($mc_sql as $row) {
		$lib_mc_arr[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
	}


	$dataArray = sql_select("select issue_purpose,issue_date,knit_dye_source,knit_dye_company,batch_no,remarks from inv_issue_master where id=$update_id");

	$product_array = array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach ($product_sql as $row) {
		$product_array[$row[csf("id")]]['gsm'] = $row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width'] = $row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id'] = $row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom'] = $row[csf("unit_of_measure")];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$grey_issue_basis = array(0 => "Independent", 1 => "PI", 2 => "Booking", 3 => "Knitting Plan", 9 => "Delivery");
?>
	<div>
		<table width="1030" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u>Grey Fabric Roll Issue Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:18px; font-weight:bold;"><strong>Challan No :</strong></td>
				<td width="175" style="font-size:18px;"><? echo $txt_issue_no; ?></td>

				<td width="125"><strong>Issue Date:</strong></td>
				<td width="175"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td width="130"><strong>Dyeing Company:</strong> </td>
				<td style="font-size:18px;">
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
				<td><strong>Issue Purpose:</strong></td>
				<td><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			</tr>
			<tr>
				<td width="130"><strong>Dyeing Source :</strong> </td>
				<td style="font-size:18px;">
					<? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?>
				</td>
				<td><strong>Remarks:</strong></td>
				<td><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1130" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center" style="font-size:12px">
				<th width="20">SL</th>
				<th width="80">Buyer</th>
				<th width="80">Style</th>
				<th width="80">Job No</th>
				<th width="80">Body Part</th>
				<th width="135">Item Description</th>
				<th width="135">Barcode No</th>
				<th width="50">GSM</th>
				<th width="50">Fin. Dia</th>
				<th width="65">M/C Dia</th>
				<th width="70">M/C Gauge</th>
				<th width="60">Color</th>
				<th width="60">Count</th>
				<th width="35">Brand</th>
				<th width="50">Yarn Lot</th>
				<th width="50">Issue Qty</th>
			</thead>
			<?
			$i = 1;
			$tot_qty = 0;
			$sql = "SELECT a.basis, a.program_no, a.prod_id, a.issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id,a.store_name, b.roll_id, b.roll_no, b.barcode_no, b.po_breakdown_id, b.qnty as quantity, b.booking_no, b.booking_without_order,b.is_sales
			from inv_grey_fabric_issue_dtls a, pro_roll_details b
			where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61  and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			//echo $sql; die;
			$result = sql_select($sql);

			/*###########  according to saeed vai decission booking no show from booking table againest order   ###############*/
			$all_fso_id = $all_order_id = $all_program_no = $all_barcode_no = "";
			$bookingChkArr = [];
			$smnBookingNoArr = [];
			foreach ($result as $row) {
				if ($row[csf("is_sales")] == 1) {
					$all_fso_id .= $row[csf("po_breakdown_id")] . ",";
				} else {
					$all_order_id .= $row[csf("po_breakdown_id")] . ",";
				}
				$all_barcode_no .= $row[csf("barcode_no")] . ",";

				if ($bookingChkArr[$row[csf("booking_no")]] == "") {
					$booking_chk_arr[$row[csf("booking_no")]] = $row[csf("booking_no")];
					array_push($smnBookingNoArr, $row[csf("booking_no")]);
				}
			}
			$all_order_id = implode(",", array_unique(explode(",", chop($all_order_id, ","))));
			$all_fso_id = implode(",", array_unique(explode(",", chop($all_fso_id, ","))));
			$all_barcode_no = implode(",", array_unique(explode(",", chop($all_barcode_no, ","))));

			if (!empty($smnBookingNoArr)) {
				$smn_booking_buyer_arr = return_library_array("select booking_no, buyer_id from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 " . where_con_using_array($smnBookingNoArr, 1, 'booking_no') . "", "booking_no", "buyer_id");
			}

			$job_array = array();
			if ($all_order_id != "") {
				$job_sql = "select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_order_id)";
				$job_sql_result = sql_select($job_sql);
				foreach ($job_sql_result as $row) {
					$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
					$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
					$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
					$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
					$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
					$job_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				}
			}

			if ($all_fso_id != "") {
				$data_array = sql_select("SELECT id as po_id,job_no, sales_booking_no, po_buyer,within_group,style_ref_no,po_job_no FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and id in($all_fso_id)");
				$sales_arr = array();
				$sales_booking_arr = array();
				foreach ($data_array as $row) {
					$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
					$sales_arr[$row[csf("po_id")]]['buyer'] = $row[csf("po_buyer")];
					$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
					$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
					$sales_arr[$row[csf("po_id")]]['style_ref'] = $row[csf("style_ref_no")];
					$sales_arr[$row[csf("po_id")]]['job'] = $row[csf("po_job_no")];
					$sales_booking_arr[] = "'" . $row[csf('sales_booking_no')] . "'";
				}
			}
			// echo "<pre>"; print_r($sales_booking_arr);die;

			if ($all_barcode_no != "") {
				$production_sql = sql_select("select b.barcode_no, a.booking_id, a.booking_no, a.receive_basis, a.knitting_source, a.knitting_company, b.qc_pass_qnty_pcs, b.coller_cuff_size, b.dtls_id, c.body_part_id from inv_receive_master a, pro_roll_details b ,pro_grey_prod_entry_dtls c where c.id=b.dtls_id and a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and b.status_active=1 and b.barcode_no in($all_barcode_no)");

				$production_data_arr = array();
				$coller_cuff_size_arr = array();
				foreach ($production_sql as $row) {
					$booking_ref = explode("-", $row[csf("booking_no")]);
					$production_data_arr[$row[csf("barcode_no")]]["booking_no"] = abs($booking_ref[3]);
					$production_data_arr[$row[csf("barcode_no")]]["receive_basis"] = $row[csf("receive_basis")];
					$production_data_arr[$row[csf("barcode_no")]]["booking_id"] = $row[csf("booking_id")];
					$production_data_arr[$row[csf("barcode_no")]]["knitting_source"] = $row[csf("knitting_source")];
					$production_data_arr[$row[csf("barcode_no")]]["knitting_company"] = $row[csf("knitting_company")];
					if ($row[csf("receive_basis")] == 2) {
						$all_program_no .= $row[csf("booking_id")] . ",";
					}
					if ($row[csf("coller_cuff_size")] != '') {
						$coller_cuff_size_arr[$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]['qnty_pcs'] += $row[csf("qc_pass_qnty_pcs")];
					}
				}

				$production_store_sql = sql_select("select b.barcode_no,a.store_id from  inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=58 and b.entry_form=58 and b.status_active=1 and b.barcode_no in($all_barcode_no)");
				$production_store_data_arr = array();
				foreach ($production_store_sql as $row) {
					$production_store_data_arr[$row[csf("barcode_no")]]["store_id"] = $row[csf("store_id")];
				}
				$prodArr = sql_select("select b.id, a.machine_no_id, a.machine_dia, a.machine_gg , a.body_part_id from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=2 and b.barcode_no in($all_barcode_no)");
				foreach ($prodArr as $row) {
					$mc_id_arr[$row[csf('id')]] = $row[csf('machine_no_id')];
					$gg_dia_arr[$row[csf('id')]]['dia'] = $row[csf('machine_dia')];
					$gg_dia_arr[$row[csf('id')]]['gauge'] = $row[csf('machine_gg')];
					$body_part_arr[$row[csf('id')]]['body_part'] = $row[csf('body_part_id')];
				}

				$is_transfer = sql_select("select id,barcode_no, is_transfer from pro_roll_details where entry_form=180 and status_active=1 and barcode_no in($all_barcode_no)");
				$is_transfer_arr = array();
				foreach ($is_transfer as $row) {
					$is_transfer_arr[$row[csf("barcode_no")]]["is_tans"] = $row[csf("is_transfer")];
				}
			}

			$all_program_no = implode(",", array_unique(explode(",", chop($all_program_no, ","))));

			if ($all_program_no != "") {

				$program_sql = sql_select("select a.booking_id, c.booking_no from inv_receive_master a,  ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c
					where a.booking_id=b.id and b.mst_id=c.id and a.entry_form=2 and a.receive_basis=2 and a.booking_id in($all_program_no)");
				$prog_book_arr = array();
				foreach ($program_sql as $row) {
					$booking_ref = explode("-", $row[csf("booking_no")]);
					$prog_book_arr[$row[csf("booking_id")]] = abs($booking_ref[3]);
				}
			}



			/*###########  according to saeed vai decission booking no show from booking table againest order end   ###############*/

			foreach ($result as $row) {
				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
				}
				$mc_id = $mc_id_arr[$row[csf("roll_id")]];

				$file_ref_ord = "";
				$job_buyer_style = "";
				$buyer = "";
				$job = "";
				$style = "";
				$ref_no = "";
				$boby_part_id = "";
				if ($row[csf('booking_without_order')] == 1) {
					$file_ref_ord = 'F : <br>R : <br>B : ' . $row[csf('booking_no')];
					$buyer = $smn_booking_buyer_arr[$row[csf('booking_no')]];
					$job = "";
					$style = "";
					$ref_no = "";
					$boby_part_id = "";
				} else {
					if ($row[csf("is_sales")] == 1) {
						$job = $sales_arr[$row[csf('po_breakdown_id')]]['job'];
						$buyer = $sales_arr[$row[csf('po_breakdown_id')]]['buyer'];
						$style = $sales_arr[$row[csf('po_breakdown_id')]]['style_ref'];
						$po_id = $sales_arr[$row[csf('po_breakdown_id')]]['sales_order'];
						$sales_booking = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];

						$int_ref = $booking_arr[$sales_booking]['int_ref'];
						$file_ref_ord = 'F : ' . $job_array[$row[csf('po_breakdown_id')]]['file_no'] . '<br>R : ' . $int_ref . '<br>O : ' . $po_id;
					} else {
						$file_ref_ord = 'F : ' . $job_array[$row[csf('po_breakdown_id')]]['file_no'] . '<br>R : ' . $job_array[$row[csf('po_breakdown_id')]]['grouping'] . '<br>O : ' . $job_array[$row[csf('po_breakdown_id')]]['po'];
						$buyer = $job_array[$row[csf('po_breakdown_id')]]['buyer'];
						$job = $job_array[$row[csf('po_breakdown_id')]]['job'];
						$style = $job_array[$row[csf('po_breakdown_id')]]['style_ref'];
						$ref_no = $job_array[$row[csf('po_breakdown_id')]]['grouping'];
						$boby_part_id = $job_array[$row[csf('po_breakdown_id')]]['boby_part_id'];
						//echo $boby_part_id; die;
					}
				}

				$knit_party = "";
				$knit_source = $production_data_arr[$row[csf("barcode_no")]]["knitting_source"];
				if ($knit_source == 1) $knit_party = $company_array[$production_data_arr[$row[csf("barcode_no")]]["knitting_company"]]['shortname'];
				else if ($knit_source == 3) $knit_party = $supplier_arr[$production_data_arr[$row[csf("barcode_no")]]["knitting_company"]];

				$color = '';
				$color_id = explode(",", $row[csf("color_id")]);
				foreach ($color_id as $val) {
					if ($val > 0) $color .= $color_arr[$val] . ",";
				}
				$color = chop($color, ',');

				$is_transfer = $is_transfer_arr[$row[csf("barcode_no")]]["is_tans"];
				if ($is_transfer == 5) $tras_cond = "(T)";
				else $tras_cond = "";

				$book_prog_challan = "";
				if ($production_data_arr[$row[csf("barcode_no")]]["receive_basis"] == 1) {
					$book_prog_challan = "B.N.-" . $production_data_arr[$row[csf("barcode_no")]]['booking_no'] . $tras_cond . "<br>";
				} else {
					$book_prog_challan = "B.N.-" . $prog_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']] . "<br>";
					$book_prog_challan .= "P.N.-" . $production_data_arr[$row[csf("barcode_no")]]['booking_id'] . "<br>";
				}
				$book_prog_challan .= "D.C.-" . $production_arr[$row[csf("program_no")]]['sys_num'];

			?>
				<tr style="font-size:11px">
					<td><? echo $i; ?></td>
					<td align="center"><? echo $buyer_array[$buyer]; ?> </td>
					<td><? echo $style; ?> </td>
					<td><? echo $job; ?> </td>
					<td><? echo $body_part[$body_part_arr[$row[csf("roll_id")]]['body_part']]; ?> </td>
					<td><? echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']]; ?></td>
					<td align="center"><? echo $row[csf("barcode_no")]; ?> </td>
					<td><? echo $product_array[$row[csf("prod_id")]]['gsm'] ?></td>
					<td><? echo $product_array[$row[csf("prod_id")]]['dia_width'] ?></td>
					<td><? echo $gg_dia_arr[$row[csf("roll_id")]]['dia'] ?></td>
					<td><? echo $gg_dia_arr[$row[csf("roll_id")]]['gauge'] ?></td>
					<td><? echo $color; ?></td>
					<td><? echo $count; ?></td>
					<td><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
					<td><? echo $row[csf('yarn_lot')]; ?></td>
					<td align="right"><? echo number_format($row[csf('quantity')], 2); ?></td>
				</tr>
			<?
				$tot_roll += $row[csf('roll_no')];
				$tot_qty += $row[csf('quantity')];
				$i++;
			}
			?>
			<tr style="font-size:12px">
				<td align="right" colspan="15"><strong>Total</strong></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
			</tr>
		</table>
	</div>
	<div style="width: 1300px; margin-top: 10px">
		<?
		foreach ($coller_cuff_size_arr as $body_part_id => $body_part_arr) { ?>
			<div style="width: 500px; float: left;">
				<table cellspacing="0" width="400" border="1" rules="all" class="rpt_table">
					<thead bgcolor="#dddddd" align="center" style="font-size:12px">
						<tr>
							<th colspan="2"><? echo $body_part[$body_part_id]; ?> Details</th>
						</tr>
						<tr>
							<th width="150" style="text-align: center;">Size</th>
							<th width="100" style="text-align: center;">Pcs</th>
						</tr>
					</thead>
					<? $total_qty = "";
					foreach ($body_part_arr as $key => $value) { ?>
						<tr>
							<td style="text-align: center;"><? echo $key ?></td>
							<td style="text-align: center;"><? echo $value['qnty_pcs'];
															$total_qty += $value['qnty_pcs']; ?></td>
						</tr>

					<? } ?>
					<tr>
						<th>Total</th>
						<th><? echo $total_qty; ?></th>
					</tr>
				</table>
			</div>
		<? } ?>
	</div>

	<? echo signature_table(124, $company, "930px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "roll_issue_challan_print5") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	//var_dump($data);

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$country_arr = return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$lib_store_name = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$booking_arr = return_library_array("select id, booking_no_prefix_num from wo_booking_mst where item_category in(2,13)", "id", "booking_no_prefix_num");


	$production_arr = array();
	$production_del_sql = sql_select("select id, sys_number_prefix_num, sys_number, knitting_source, knitting_company, location_id from pro_grey_prod_delivery_mst where entry_form=56 and company_id=$company");
	foreach ($production_del_sql as $row) {
		$production_arr[$row[csf('id')]]['sys'] = $row[csf('sys_number')];
		$production_arr[$row[csf('id')]]['sys_num'] = $row[csf('sys_number_prefix_num')];
		$production_arr[$row[csf('id')]]['unit'] = $row[csf('location_id')];
		$production_arr[$row[csf('id')]]['knit_sou'] = $row[csf('knitting_source')];
		$production_arr[$row[csf('id')]]['knit_com'] = $row[csf('knitting_company')];
	}


	$lib_mc_arr = array();
	$mc_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($mc_sql as $row) {
		$lib_mc_arr[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
	}


	$dataArray = sql_select("select issue_purpose,issue_date,knit_dye_source,knit_dye_company,batch_no,remarks from inv_issue_master where id=$update_id");

	$product_array = array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach ($product_sql as $row) {
		$product_array[$row[csf("id")]]['gsm'] = $row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width'] = $row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id'] = $row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom'] = $row[csf("unit_of_measure")];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$grey_issue_basis = array(0 => "Independent", 1 => "PI", 2 => "Booking", 3 => "Knitting Plan", 9 => "Delivery");
?>
	<div>
		<div width="1330" align="center">
			<table width="1030" cellspacing="0">
				<tr>
					<td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="6" align="center" style="font-size:14px">
						<? echo show_company($company, '', ''); ?>
					</td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:16px"><strong><u>Grey Fabric Roll Issue Challan</u></strong></td>
				</tr>
				<tr>
					<td width="120" style="font-size:18px; font-weight:bold;"><strong>Challan No :</strong></td>
					<td width="175" style="font-size:18px;"><? echo $txt_issue_no; ?></td>

					<td width="125"><strong>Issue Date:</strong></td>
					<td width="175"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
				</tr>
				<tr>
					<td width="130"><strong>Dyeing Company:</strong> </td>
					<td style="font-size:18px;">
						<?
						if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
						else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
						?>
					</td>
					<td><strong>Issue Purpose:</strong></td>
					<td><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
				</tr>
				<tr>
					<td width="130"><strong>Dyeing Source :</strong> </td>
					<td style="font-size:18px;">
						<? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?>
					</td>
					<td><strong>Remarks:</strong></td>
					<td><? echo $dataArray[0][csf('remarks')]; ?></td>
				</tr>
			</table>
		</div>
		<br>
		<table cellspacing="0" width="1330" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center" style="font-size:12px">
				<th width="20">SL</th>
				<th width="80">Buyer</th>
				<th width="80">Style</th>
				<th width="100">Booking No<br>Sample/Bulk</th>
				<th width="80">Job No</th>
				<th width="100">IR</th>
				<th width="80">Body Part</th>
				<th width="135">Item Description</th>
				<th width="135">Barcode No</th>
				<th width="50">GSM</th>
				<th width="50">Fin. Dia</th>
				<th width="65">M/C Dia</th>
				<th width="70">M/C Gauge</th>
				<th width="60">Color</th>
				<th width="60">Count</th>
				<th width="35">Brand</th>
				<th width="50">Yarn Lot</th>
				<th width="50">Issue Qty</th>
			</thead>
			<?
			$i = 1;
			$tot_qty = 0;
			$sql = "SELECT a.basis, a.program_no, a.prod_id, a.issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id,a.store_name, b.roll_id, b.roll_no, b.barcode_no, b.po_breakdown_id, b.qnty as quantity, b.booking_no, b.booking_without_order,b.is_sales
			from inv_grey_fabric_issue_dtls a, pro_roll_details b
			where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61  and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			//  echo $sql; die; 
			$result = sql_select($sql);

			/*###########  according to saeed vai decission booking no show from booking table againest order   ###############*/
			$all_fso_id = $all_order_id = $all_program_no = $all_barcode_no = "";
			$bookingChkArr = [];
			$smnBookingNoArr = [];
			foreach ($result as $row) {
				if ($row[csf("is_sales")] == 1) {
					$all_fso_id .= $row[csf("po_breakdown_id")] . ",";
				} else {
					$all_order_id .= $row[csf("po_breakdown_id")] . ",";
				}
				$all_barcode_no .= $row[csf("barcode_no")] . ",";

				if ($bookingChkArr[$row[csf("booking_no")]] == "") {
					$booking_chk_arr[$row[csf("booking_no")]] = $row[csf("booking_no")];
					array_push($smnBookingNoArr, $row[csf("booking_no")]);
				}
			}
			$all_order_id = implode(",", array_unique(explode(",", chop($all_order_id, ","))));
			$all_fso_id = implode(",", array_unique(explode(",", chop($all_fso_id, ","))));
			$all_barcode_no = implode(",", array_unique(explode(",", chop($all_barcode_no, ","))));

			if (!empty($smnBookingNoArr)) {
				$smn_booking_buyer_arr = return_library_array("select booking_no, buyer_id from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 " . where_con_using_array($smnBookingNoArr, 1, 'booking_no') . "", "booking_no", "buyer_id");
			}
			//// Booking 

			// echo $sql_data;
			$booking_with_order_ids = "";
			$booking_without_order_ids = "";
			$barcodeNos = "";
			$fso_booking_with_order_ids = "";
			foreach ($result as $row) {
				$barcodeNos .= $row[csf("barcode_no")] . ",";

				if ($row[csf("booking_without_order")] == 1) {
					$booking_without_order_ids .= $row[csf("po_breakdown_id")] . ",";
				} else if ($row[csf("is_sales")] == 1) {
					$fso_booking_with_order_ids .= $row[csf("po_breakdown_id")] . ",";
				} else {
					$booking_with_order_ids .= $row[csf("po_breakdown_id")] . ",";
				}
			}
			$barcodeNos = chop($barcodeNos, ",");
			$booking_with_order_ids = implode(",", array_unique(array_filter(explode(",", chop($booking_with_order_ids, ",")))));
			$fso_booking_with_order_ids = implode(",", array_unique(array_filter(explode(",", chop($fso_booking_with_order_ids, ",")))));
			$booking_without_order_ids = implode(",", array_unique(array_filter(explode(",", chop($booking_without_order_ids, ",")))));

			// $sql_chk_trnsf = sql_select("select a.barcode_no   from PRO_ROLL_DETAILS a, INV_ITEM_TRANSFER_MST b
			// where a.mst_id=b.id and a.barcode_no in($barcodeNos)
			// and a.entry_form in(82,83) and b.transfer_criteria in(1,4)
			// and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			// foreach ($sql_chk_trnsf as $rows) {
			// 	$trnsf_chk_arr[$rows[csf("barcode_no")]] = $rows[csf("barcode_no")];
			// }

			$sql_booking_withorder = sql_select("select po_break_down_id,booking_no from wo_booking_dtls  where po_break_down_id in($booking_with_order_ids) and status_active=1 and is_deleted=0 and booking_type=1");
			foreach ($sql_booking_withorder as $rows) {
				$booking_data_arr[$rows[csf("po_break_down_id")]] = $rows[csf("booking_no")];
			}

			$sql_fso_booking_withorder = sql_select("SELECT id,sales_booking_no from fabric_sales_order_mst  where id in($fso_booking_with_order_ids) and status_active=1 and is_deleted=0");
			foreach ($sql_fso_booking_withorder as $rows) {
				$booking_data_arr[$rows[csf("id")]] = $rows[csf("sales_booking_no")];
			}

			$sql_booking_without_order = sql_select("select id,booking_no from wo_non_ord_samp_booking_mst  where id in($booking_without_order_ids) and status_active=1 and is_deleted=0");
			foreach ($sql_booking_without_order as $rows) {
				$booking_data_arr[$rows[csf("id")]] = $rows[csf("booking_no")];
			}
			//// booking end

			$job_array = array();
			if ($all_order_id != "") {
				$job_sql = "select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_order_id)";
				$job_sql_result = sql_select($job_sql);
				foreach ($job_sql_result as $row) {
					$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
					$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
					$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
					$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
					$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
					$job_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				}
			}

			if ($all_fso_id != "") {
				$data_array = sql_select("SELECT id as po_id,job_no, sales_booking_no, po_buyer,within_group,style_ref_no,po_job_no FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and id in($all_fso_id)");
				$sales_arr = array();
				$sales_booking_arr = array();
				foreach ($data_array as $row) {
					$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
					$sales_arr[$row[csf("po_id")]]['buyer'] = $row[csf("po_buyer")];
					$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
					$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
					$sales_arr[$row[csf("po_id")]]['style_ref'] = $row[csf("style_ref_no")];
					$sales_arr[$row[csf("po_id")]]['job'] = $row[csf("po_job_no")];
					$sales_booking_arr[] = "'" . $row[csf('sales_booking_no')] . "'";
				}
			}
			// echo "<pre>"; print_r($sales_booking_arr);die;

			if ($all_barcode_no != "") {
				$production_sql = sql_select("select b.barcode_no, a.booking_id, a.booking_no, a.receive_basis, a.knitting_source, a.knitting_company, b.qc_pass_qnty_pcs, b.coller_cuff_size, b.dtls_id, c.body_part_id from inv_receive_master a, pro_roll_details b ,pro_grey_prod_entry_dtls c where c.id=b.dtls_id and a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and b.status_active=1 and b.barcode_no in($all_barcode_no)");

				$production_data_arr = array();
				$coller_cuff_size_arr = array();
				foreach ($production_sql as $row) {
					$booking_ref = explode("-", $row[csf("booking_no")]);
					$production_data_arr[$row[csf("barcode_no")]]["booking_no"] = abs($booking_ref[3]);
					$production_data_arr[$row[csf("barcode_no")]]["receive_basis"] = $row[csf("receive_basis")];
					$production_data_arr[$row[csf("barcode_no")]]["booking_id"] = $row[csf("booking_id")];
					$production_data_arr[$row[csf("barcode_no")]]["knitting_source"] = $row[csf("knitting_source")];
					$production_data_arr[$row[csf("barcode_no")]]["knitting_company"] = $row[csf("knitting_company")];
					if ($row[csf("receive_basis")] == 2) {
						$all_program_no .= $row[csf("booking_id")] . ",";
					}
					if ($row[csf("coller_cuff_size")] != '') {
						$coller_cuff_size_arr[$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]['qnty_pcs'] += $row[csf("qc_pass_qnty_pcs")];
					}
				}

				$production_store_sql = sql_select("select b.barcode_no,a.store_id from  inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=58 and b.entry_form=58 and b.status_active=1 and b.barcode_no in($all_barcode_no)");
				$production_store_data_arr = array();
				foreach ($production_store_sql as $row) {
					$production_store_data_arr[$row[csf("barcode_no")]]["store_id"] = $row[csf("store_id")];
				}
				$prodArr = sql_select("select b.id, a.machine_no_id, a.machine_dia, a.machine_gg , a.body_part_id from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=2 and b.barcode_no in($all_barcode_no)");
				foreach ($prodArr as $row) {
					$mc_id_arr[$row[csf('id')]] = $row[csf('machine_no_id')];
					$gg_dia_arr[$row[csf('id')]]['dia'] = $row[csf('machine_dia')];
					$gg_dia_arr[$row[csf('id')]]['gauge'] = $row[csf('machine_gg')];
					$body_part_arr[$row[csf('id')]]['body_part'] = $row[csf('body_part_id')];
				}

				$is_transfer = sql_select("select id,barcode_no, is_transfer from pro_roll_details where entry_form=180 and status_active=1 and barcode_no in($all_barcode_no)");
				$is_transfer_arr = array();
				foreach ($is_transfer as $row) {
					$is_transfer_arr[$row[csf("barcode_no")]]["is_tans"] = $row[csf("is_transfer")];
				}
			}

			$all_program_no = implode(",", array_unique(explode(",", chop($all_program_no, ","))));

			if ($all_program_no != "") {

				$program_sql = sql_select("select a.booking_id, c.booking_no from inv_receive_master a,  ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c
					where a.booking_id=b.id and b.mst_id=c.id and a.entry_form=2 and a.receive_basis=2 and a.booking_id in($all_program_no)");
				$prog_book_arr = array();
				foreach ($program_sql as $row) {
					$booking_ref = explode("-", $row[csf("booking_no")]);
					$prog_book_arr[$row[csf("booking_id")]] = abs($booking_ref[3]);
				}
			}



			/*###########  according to saeed vai decission booking no show from booking table againest order end   ###############*/

			foreach ($result as $row) {
				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
				}
				$mc_id = $mc_id_arr[$row[csf("roll_id")]];

				$file_ref_ord = "";
				$job_buyer_style = "";
				$buyer = "";
				$job = "";
				$style = "";
				$ref_no = "";
				$boby_part_id = "";
				if ($row[csf('booking_without_order')] == 1) {
					$file_ref_ord = 'F : <br>R : <br>B : ' . $row[csf('booking_no')];
					$buyer = $smn_booking_buyer_arr[$row[csf('booking_no')]];
					$job = "";
					$style = "";
					$ref_no = "";
					$boby_part_id = "";

				} else {
					if ($row[csf("is_sales")] == 1) {
						$job = $sales_arr[$row[csf('po_breakdown_id')]]['job'];
						$buyer = $sales_arr[$row[csf('po_breakdown_id')]]['buyer'];
						$style = $sales_arr[$row[csf('po_breakdown_id')]]['style_ref'];
						$po_id = $sales_arr[$row[csf('po_breakdown_id')]]['sales_order'];
						$sales_booking = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];

						$int_ref = $booking_arr[$sales_booking]['int_ref'];
						$file_ref_ord = 'F : ' . $job_array[$row[csf('po_breakdown_id')]]['file_no'] . '<br>R : ' . $int_ref . '<br>O : ' . $po_id;
					} else {
						$file_ref_ord = 'F : ' . $job_array[$row[csf('po_breakdown_id')]]['file_no'] . '<br>R : ' . $job_array[$row[csf('po_breakdown_id')]]['grouping'] . '<br>O : ' . $job_array[$row[csf('po_breakdown_id')]]['po'];
						$buyer = $job_array[$row[csf('po_breakdown_id')]]['buyer'];
						$job = $job_array[$row[csf('po_breakdown_id')]]['job'];
						$style = $job_array[$row[csf('po_breakdown_id')]]['style_ref'];
						$ref_no = $job_array[$row[csf('po_breakdown_id')]]['grouping'];
						$boby_part_id = $job_array[$row[csf('po_breakdown_id')]]['boby_part_id'];
						//echo $boby_part_id; die;
					}
				}


				$knit_party = "";
				$knit_source = $production_data_arr[$row[csf("barcode_no")]]["knitting_source"];
				if ($knit_source == 1) $knit_party = $company_array[$production_data_arr[$row[csf("barcode_no")]]["knitting_company"]]['shortname'];
				else if ($knit_source == 3) $knit_party = $supplier_arr[$production_data_arr[$row[csf("barcode_no")]]["knitting_company"]];

				$color = '';
				$color_id = explode(",", $row[csf("color_id")]);
				foreach ($color_id as $val) {
					if ($val > 0) $color .= $color_arr[$val] . ",";
				}
				$color = chop($color, ',');

				$is_transfer = $is_transfer_arr[$row[csf("barcode_no")]]["is_tans"];
				if ($is_transfer == 5) $tras_cond = "(T)";
				else $tras_cond = "";

				$book_prog_challan = "";
				if ($production_data_arr[$row[csf("barcode_no")]]["receive_basis"] == 1) {
					$book_prog_challan = "B.N.-" . $production_data_arr[$row[csf("barcode_no")]]['booking_no'] . $tras_cond . "<br>";
				} else {
					$book_prog_challan = "B.N.-" . $prog_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']] . "<br>";
					$book_prog_challan .= "P.N.-" . $production_data_arr[$row[csf("barcode_no")]]['booking_id'] . "<br>";
				}
				$book_prog_challan .= "D.C.-" . $production_arr[$row[csf("program_no")]]['sys_num'];


					$booking_number = "";
					if($ref_no==""){
						$ref_no = "";
					}
					
					if ($row[csf("booking_without_order")] == 0) {
						if ($trnsf_chk_arr[$row[csf("barcode_no")]]) {
							$booking_number = $booking_data_arr[$row[csf("po_breakdown_id")]];
						} else if ($row[csf("is_sales")] == 1) {
							$booking_number = $booking_data_arr[$row[csf("po_breakdown_id")]];
							if($ref_no==""){
							$ref_no = $booking_arr[$booking_number]['int_ref'];
							}
						} else {
							$booking_number = $production_rcv_data[$row[csf("barcode_no")]]["progBooking"];
						}
					} else {
						$booking_number = $booking_data_arr[$row[csf("po_breakdown_id")]];
					}
					
					if($ref_no==""){
						$ref_no=return_field_value("grouping","wo_po_break_down","job_no_mst='$job' and is_deleted=0 and status_active=1");
					}
		

			?>
				<tr style="font-size:11px">
					<td><? echo $i; ?></td>
					<td align="center"><? echo $buyer_array[$buyer]; ?> </td>
					<td><? echo $style; ?> </td>
					<td><? echo $booking_number; ?> </td>
					<td><? echo $job; ?> </td>
					<td><? echo $ref_no; ?> </td>
					<td><? echo $body_part[$body_part_arr[$row[csf("roll_id")]]['body_part']]; ?> </td>
					<td><? echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']]; ?></td>
					<td align="center"><? echo $row[csf("barcode_no")]; ?> </td>
					<td><? echo $product_array[$row[csf("prod_id")]]['gsm'] ?></td>
					<td><? echo $product_array[$row[csf("prod_id")]]['dia_width'] ?></td>
					<td><? echo $gg_dia_arr[$row[csf("roll_id")]]['dia'] ?></td>
					<td><? echo $gg_dia_arr[$row[csf("roll_id")]]['gauge'] ?></td>
					<td><? echo $color; ?></td>
					<td><? echo $count; ?></td>
					<td><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
					<td><? echo $row[csf('yarn_lot')]; ?></td>
					<td align="right"><? echo number_format($row[csf('quantity')], 2); ?></td>
				</tr>
			<?
				$tot_roll += $row[csf('roll_no')];
				$tot_qty += $row[csf('quantity')];
				$i++;
			}
			?>
			<tr style="font-size:12px">
				<td align="right" colspan="17"><strong>Total</strong></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
			</tr>
		</table>
	</div>
	<div style="width: 1300px; margin-top: 10px">
		<?
		foreach ($coller_cuff_size_arr as $body_part_id => $body_part_arr) { ?>
			<div style="width: 500px; float: left;">
				<table cellspacing="0" width="400" border="1" rules="all" class="rpt_table">
					<thead bgcolor="#dddddd" align="center" style="font-size:12px">
						<tr>
							<th colspan="2"><? echo $body_part[$body_part_id]; ?> Details</th>
						</tr>
						<tr>
							<th width="150" style="text-align: center;">Size</th>
							<th width="100" style="text-align: center;">Pcs</th>
						</tr>
					</thead>
					<? $total_qty = "";
					foreach ($body_part_arr as $key => $value) { ?>
						<tr>
							<td style="text-align: center;"><? echo $key ?></td>
							<td style="text-align: center;"><? echo $value['qnty_pcs'];
															$total_qty += $value['qnty_pcs']; ?></td>
						</tr>

					<? } ?>
					<tr>
						<th>Total</th>
						<th><? echo $total_qty; ?></th>
					</tr>
				</table>
			</div>
		<? } ?>
	</div>

	<? echo signature_table(124, $company, "1230px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "roll_issue_challan_print_atg") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	//var_dump($data);

	$location_arr = return_library_array("SELECT id, location_name from  lib_location", 'id', 'location_name');
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$supplier_arr = return_library_array("SELECT id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("SELECT id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("SELECT id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("SELECT id, brand_name from lib_brand", 'id', 'brand_name');
	$country_arr = return_library_array("SELECT id, country_name from  lib_country", "id", "country_name");
	$lib_store_name = return_library_array("SELECT id, store_name from  lib_store_location", "id", "store_name");

	$company_array = array();
	$company_data = sql_select("SELECT id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$lib_mc_arr = array();
	$mc_sql = sql_select("SELECT id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($mc_sql as $row) {
		$lib_mc_arr[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
	}

	$dataArray = sql_select("SELECT issue_purpose,issue_date,knit_dye_source,knit_dye_company,batch_no,remarks from inv_issue_master where id=$update_id");

	$sql = "SELECT a.basis, a.program_no, a.prod_id, a.issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id,a.store_name, b.roll_id, b.roll_no, b.barcode_no, b.po_breakdown_id, b.qnty as quantity, b.booking_no, b.booking_without_order,b.is_sales
	from inv_grey_fabric_issue_dtls a, pro_roll_details b
	where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61  and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $sql; die;
	$result = sql_select($sql);

	/*###########  according to saeed vai decission booking no show from booking table againest order   ###############*/
	$all_order_id = $all_program_no = $all_barcode_no = "";
	$bookingChkArr = [];
	$smnBookingNoArr = [];
	$prodIdChkArr = [];
	$prodIdArr = [];
	$programNoChkArr = [];
	$programNoArr = [];
	$allFsoIdChkArr = [];
	$allFsoIdArr = [];
	$allOrderIdArr = [];
	$allOrderIdChkArr = [];
	foreach ($result as $row) {
		if ($row[csf("is_sales")] == 1) {
			if ($allFsoIdChkArr[$row[csf("po_breakdown_id")]] == "") {
				$allFsoIdChkArr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
				array_push($allFsoIdArr, $row[csf("po_breakdown_id")]);
			}
		} else {
			if ($allOrderIdChkArr[$row[csf("po_breakdown_id")]] == "") {
				$allOrderIdChkArr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
				array_push($allOrderIdArr, $row[csf("po_breakdown_id")]);
			}
			//$all_order_id.=$row[csf("po_breakdown_id")].",";
		}
		$all_barcode_no .= $row[csf("barcode_no")] . ",";

		if ($row[csf('booking_without_order')] == 1) {
			if ($bookingChkArr[$row[csf("booking_no")]] == "") {
				$bookingChkArr[$row[csf("booking_no")]] = $row[csf("booking_no")];
				array_push($smnBookingNoArr, $row[csf("booking_no")]);
			}
		}

		if ($prodIdChkArr[$row[csf("prod_id")]] == "") {
			$prodIdChkArr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			array_push($prodIdArr, $row[csf("prod_id")]);
		}

		if ($programNoChkArr[$row[csf("program_no")]] == "") {
			$programNoChkArr[$row[csf("program_no")]] = $row[csf("program_no")];
			array_push($programNoArr, $row[csf("program_no")]);
		}
	}
	//echo "<pre>";print_r($allOrderIdArr);

	//$all_order_id=implode(",",array_unique(explode(",",chop($all_order_id,","))));
	$all_barcode_no = implode(",", array_unique(explode(",", chop($all_barcode_no, ","))));

	if (!empty($smnBookingNoArr)) {
		$smn_booking_buyer_arr = return_library_array("SELECT booking_no, buyer_id from wo_non_ord_samp_booking_mst where  company_id=$company and status_active=1 and is_deleted=0 " . where_con_using_array($smnBookingNoArr, 1, 'booking_no') . "", "booking_no", "buyer_id");
	}

	if (!empty($prodIdArr)) {
		$product_array = array();
		$product_sql = sql_select("SELECT id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13 and company_id=$company " . where_con_using_array($prodIdArr, 0, 'id') . " ");
		foreach ($product_sql as $row) {
			$product_array[$row[csf("id")]]['gsm'] = $row[csf("gsm")];
			$product_array[$row[csf("id")]]['dia_width'] = $row[csf("dia_width")];
			$product_array[$row[csf("id")]]['deter_id'] = $row[csf("detarmination_id")];
			$product_array[$row[csf("id")]]['uom'] = $row[csf("unit_of_measure")];
		}
	}

	if (!empty($programNoArr)) {
		$production_arr = array();
		$production_del_sql = sql_select("SELECT id, sys_number_prefix_num, sys_number, knitting_source, knitting_company, location_id from pro_grey_prod_delivery_mst where entry_form=56 and company_id=$company " . where_con_using_array($programNoArr, 0, 'id') . "");
		foreach ($production_del_sql as $row) {
			$production_arr[$row[csf('id')]]['sys'] = $row[csf('sys_number')];
			$production_arr[$row[csf('id')]]['sys_num'] = $row[csf('sys_number_prefix_num')];
			$production_arr[$row[csf('id')]]['unit'] = $row[csf('location_id')];
			$production_arr[$row[csf('id')]]['knit_sou'] = $row[csf('knitting_source')];
			$production_arr[$row[csf('id')]]['knit_com'] = $row[csf('knitting_company')];
		}
	}

	if (!empty($allOrderIdArr)) {
		$job_sql = "SELECT a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id " . where_con_using_array($allOrderIdArr, 0, 'b.id') . " ";
		//echo $job_sql;
		$job_sql_result = sql_select($job_sql);
		$job_array = array();
		foreach ($job_sql_result as $row) {
			$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
			$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
			$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
			$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
			$job_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
		}
	}

	if (!empty($allFsoIdArr)) {
		$data_array = sql_select("SELECT id as po_id,job_no, sales_booking_no, po_buyer,within_group,style_ref_no,po_job_no FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 " . where_con_using_array($allFsoIdArr, 0, 'id') . " ");
		$sales_arr = array();
		$sales_booking_arr = array();
		foreach ($data_array as $row) {
			$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
			$sales_arr[$row[csf("po_id")]]['buyer'] = $row[csf("po_buyer")];
			$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
			$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
			$sales_arr[$row[csf("po_id")]]['style_ref'] = $row[csf("style_ref_no")];
			$sales_arr[$row[csf("po_id")]]['job'] = $row[csf("po_job_no")];
			$sales_booking_arr[] = "'" . $row[csf('sales_booking_no')] . "'";
		}
	}
	// echo "<pre>"; print_r($sales_booking_arr);die;

	if ($all_barcode_no != "") {
		$production_sql = sql_select("SELECT b.barcode_no, a.booking_id, a.booking_no, a.receive_basis, a.knitting_source, a.knitting_company, b.qc_pass_qnty_pcs, b.coller_cuff_size, b.dtls_id, c.body_part_id from inv_receive_master a, pro_roll_details b ,pro_grey_prod_entry_dtls c where c.id=b.dtls_id and a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and b.status_active=1 and b.barcode_no in($all_barcode_no)");

		$production_data_arr = array();
		$coller_cuff_size_arr = array();
		$all_program_noChkArr = array();
		$all_program_noArr = array();
		foreach ($production_sql as $row) {
			$booking_ref = explode("-", $row[csf("booking_no")]);
			$production_data_arr[$row[csf("barcode_no")]]["booking_no"] = abs($booking_ref[3]);
			$production_data_arr[$row[csf("barcode_no")]]["receive_basis"] = $row[csf("receive_basis")];
			$production_data_arr[$row[csf("barcode_no")]]["booking_id"] = $row[csf("booking_id")];
			$production_data_arr[$row[csf("barcode_no")]]["knitting_source"] = $row[csf("knitting_source")];
			$production_data_arr[$row[csf("barcode_no")]]["knitting_company"] = $row[csf("knitting_company")];
			if ($row[csf("receive_basis")] == 2) {
				if ($all_program_noChkArr[$row[csf("booking_id")]] == "") {
					$all_program_noChkArr[$row[csf("booking_id")]] = $row[csf("booking_id")];
					array_push($all_program_noArr, $row[csf("booking_id")]);
				}

				//$all_program_no.=$row[csf("booking_id")].",";
			}
			if ($row[csf("coller_cuff_size")] != '') {
				$coller_cuff_size_arr[$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]['qnty_pcs'] += $row[csf("qc_pass_qnty_pcs")];
			}
		}

		$production_store_sql = sql_select("SELECT b.barcode_no,a.store_id from  inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=58 and b.entry_form=58 and b.status_active=1 and b.barcode_no in($all_barcode_no)");

		$production_store_data_arr = array();
		foreach ($production_store_sql as $row) {
			$production_store_data_arr[$row[csf("barcode_no")]]["store_id"] = $row[csf("store_id")];
		}
		$prodArr = sql_select("SELECT b.id, a.machine_no_id, a.machine_dia, a.machine_gg , a.body_part_id from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=2 and b.barcode_no in($all_barcode_no)");

		foreach ($prodArr as $row) {
			$mc_id_arr[$row[csf('id')]] = $row[csf('machine_no_id')];
			$gg_dia_arr[$row[csf('id')]]['dia'] = $row[csf('machine_dia')];
			$gg_dia_arr[$row[csf('id')]]['gauge'] = $row[csf('machine_gg')];
			$body_part_arr[$row[csf('id')]]['body_part'] = $row[csf('body_part_id')];
		}

		$is_transfer = sql_select("SELECT id,barcode_no, is_transfer from pro_roll_details where entry_form=180 and status_active=1 and barcode_no in($all_barcode_no)");

		$is_transfer_arr = array();
		foreach ($is_transfer as $row) {
			$is_transfer_arr[$row[csf("barcode_no")]]["is_tans"] = $row[csf("is_transfer")];
		}
	}

	if (!empty($all_program_noArr)) {
		$program_sql = sql_select("SELECT a.booking_id, c.booking_no from inv_receive_master a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c where a.booking_id=b.id and b.mst_id=c.id and a.entry_form=2 and a.receive_basis=2 " . where_con_using_array($all_program_noArr, 0, 'a.booking_id') . " ");
		$prog_book_arr = array();
		foreach ($program_sql as $row) {
			$booking_ref = explode("-", $row[csf("booking_no")]);
			$prog_book_arr[$row[csf("booking_id")]] = abs($booking_ref[3]);
		}
	}
	//echo "<pre>";print_r($prog_book_arr);

	$composition_arr = array();
	$sql_deter = "SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$grey_issue_basis = array(0 => "Independent", 1 => "PI", 2 => "Booking", 3 => "Knitting Plan", 9 => "Delivery");
?>
	<div>
		<table width="1030" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u>Grey Fabric Roll Issue Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:18px; font-weight:bold;"><strong>Challan No :</strong></td>
				<td width="175" style="font-size:18px;"><? echo $txt_issue_no; ?></td>

				<td width="125"><strong>Issue Date:</strong></td>
				<td width="175"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td width="130"><strong>Dyeing Company:</strong> </td>
				<td style="font-size:18px;">
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
				<td><strong>Issue Purpose:</strong></td>
				<td><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			</tr>
			<tr>
				<td width="130"><strong>Dyeing Source :</strong> </td>
				<td style="font-size:18px;">
					<? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?>
				</td>
				<td><strong>Remarks:</strong></td>
				<td><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1130" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center" style="font-size:12px">
				<th width="20">SL</th>
				<th width="80">Buyer</th>
				<th width="80">Style</th>
				<th width="80">Job No</th>
				<th width="80">Body Part</th>
				<th width="135">Item Description</th>
				<th width="135">Barcode No</th>
				<th width="50">GSM</th>
				<th width="50">Fin. Dia</th>
				<th width="65">M/C Dia</th>
				<th width="70">M/C Gauge</th>
				<th width="60">Color</th>
				<th width="60">Count</th>
				<th width="35">Brand</th>
				<th width="50">Yarn Lot</th>
				<th width="50">Issue Qty</th>
			</thead>
			<?
			$i = 1;
			$tot_qty = 0;

			/*###########  according to saeed vai decission booking no show from booking table againest order end   ###############*/

			$grand_tot_qty = 0;
			foreach ($result as $row) {
				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
				}
				$mc_id = $mc_id_arr[$row[csf("roll_id")]];

				$file_ref_ord = "";
				$job_buyer_style = "";
				$buyer = "";
				$job = "";
				$style = "";
				$ref_no = "";
				$boby_part_id = "";
				if ($row[csf('booking_without_order')] == 1) {
					$file_ref_ord = 'F : <br>R : <br>B : ' . $row[csf('booking_no')];
					$buyer = $smn_booking_buyer_arr[$row[csf('booking_no')]];
					$job = "";
					$style = "";
					$ref_no = "";
					$boby_part_id = "";
				} else {
					if ($row[csf("is_sales")] == 1) {
						$job = $sales_arr[$row[csf('po_breakdown_id')]]['job'];
						$buyer = $sales_arr[$row[csf('po_breakdown_id')]]['buyer'];
						$style = $sales_arr[$row[csf('po_breakdown_id')]]['style_ref'];
						$po_id = $sales_arr[$row[csf('po_breakdown_id')]]['sales_order'];
						$sales_booking = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];

						$int_ref = $booking_arr[$sales_booking]['int_ref'];
						$file_ref_ord = 'F : ' . $job_array[$row[csf('po_breakdown_id')]]['file_no'] . '<br>R : ' . $int_ref . '<br>O : ' . $po_id;
					} else {
						$file_ref_ord = 'F : ' . $job_array[$row[csf('po_breakdown_id')]]['file_no'] . '<br>R : ' . $job_array[$row[csf('po_breakdown_id')]]['grouping'] . '<br>O : ' . $job_array[$row[csf('po_breakdown_id')]]['po'];
						$buyer = $job_array[$row[csf('po_breakdown_id')]]['buyer'];
						$job = $job_array[$row[csf('po_breakdown_id')]]['job'];
						$style = $job_array[$row[csf('po_breakdown_id')]]['style_ref'];
						$ref_no = $job_array[$row[csf('po_breakdown_id')]]['grouping'];
						$boby_part_id = $job_array[$row[csf('po_breakdown_id')]]['boby_part_id'];
						//echo $boby_part_id; die;
					}
				}

				$knit_party = "";
				$knit_source = $production_data_arr[$row[csf("barcode_no")]]["knitting_source"];
				if ($knit_source == 1) $knit_party = $company_array[$production_data_arr[$row[csf("barcode_no")]]["knitting_company"]]['shortname'];
				else if ($knit_source == 3) $knit_party = $supplier_arr[$production_data_arr[$row[csf("barcode_no")]]["knitting_company"]];

				$color = '';
				$color_id = explode(",", $row[csf("color_id")]);
				foreach ($color_id as $val) {
					if ($val > 0) $color .= $color_arr[$val] . ",";
				}
				$color = chop($color, ',');

				$is_transfer = $is_transfer_arr[$row[csf("barcode_no")]]["is_tans"];
				if ($is_transfer == 5) $tras_cond = "(T)";
				else $tras_cond = "";

				$book_prog_challan = "";
				if ($production_data_arr[$row[csf("barcode_no")]]["receive_basis"] == 1) {
					$book_prog_challan = "B.N.-" . $production_data_arr[$row[csf("barcode_no")]]['booking_no'] . $tras_cond . "<br>";
				} else {
					$book_prog_challan = "B.N.-" . $prog_book_arr[$production_data_arr[$row[csf("barcode_no")]]['booking_id']] . "<br>";
					$book_prog_challan .= "P.N.-" . $production_data_arr[$row[csf("barcode_no")]]['booking_id'] . "<br>";
				}
				$book_prog_challan .= "D.C.-" . $production_arr[$row[csf("program_no")]]['sys_num'];

				$check_string = $product_array[$row[csf("prod_id")]]['deter_id'];

				if (!in_array($check_string, $checkArr)) {
					$checkArr[$i] = $check_string;
					if ($i > 1) {
			?>
						<tr bgcolor="#CCCCCC" style="font-size:13px">
							<td align="right" colspan="15"><strong>Sub Total : </strong></td>
							<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
						</tr>
				<?
						$tot_qty = 0;
					}
				}
				?>

				<tr style="font-size:11px">
					<td><? echo $i; ?></td>
					<td align="center"><? echo $buyer_array[$buyer]; ?> </td>
					<td><? echo $style; ?> </td>
					<td><? echo $job; ?> </td>
					<td><? echo $body_part[$body_part_arr[$row[csf("roll_id")]]['body_part']]; ?> </td>
					<td><? echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']]; ?></td>
					<td align="center"><? echo $row[csf("barcode_no")]; ?> </td>
					<td><? echo $product_array[$row[csf("prod_id")]]['gsm'] ?></td>
					<td><? echo $product_array[$row[csf("prod_id")]]['dia_width'] ?></td>
					<td><? echo $gg_dia_arr[$row[csf("roll_id")]]['dia'] ?></td>
					<td><? echo $gg_dia_arr[$row[csf("roll_id")]]['gauge'] ?></td>
					<td><? echo $color; ?></td>
					<td><? echo $count; ?></td>
					<td><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
					<td><? echo $row[csf('yarn_lot')]; ?></td>
					<td align="right"><? echo number_format($row[csf('quantity')], 2); ?></td>
				</tr>
			<?
				$tot_qty += $row[csf('quantity')];
				$grand_tot_qty += $row[csf('quantity')];
				$i++;
			}
			?>
			<tr bgcolor="#CCCCCC" style="font-size:13px">
				<td align="right" colspan="15"><strong>Sub Total:</strong></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
			</tr>
			<tr style="font-size:15px">
				<td align="right" colspan="15"><strong>Grand Total:</strong></td>
				<td align="right"><? echo number_format($grand_tot_qty, 2, '.', ''); ?></td>
			</tr>
		</table>
	</div>
	<div style="width: 1300px; margin-top: 10px">
		<?
		foreach ($coller_cuff_size_arr as $body_part_id => $body_part_arr) { ?>
			<div style="width: 500px; float: left;">
				<table cellspacing="0" width="400" border="1" rules="all" class="rpt_table">
					<thead bgcolor="#dddddd" align="center" style="font-size:12px">
						<tr>
							<th colspan="2"><? echo $body_part[$body_part_id]; ?> Details</th>
						</tr>
						<tr>
							<th width="150" style="text-align: center;">Size</th>
							<th width="100" style="text-align: center;">Pcs</th>
						</tr>
					</thead>
					<? $total_qty = "";
					foreach ($body_part_arr as $key => $value) { ?>
						<tr>
							<td style="text-align: center;"><? echo $key ?></td>
							<td style="text-align: center;"><? echo $value['qnty_pcs'];
															$total_qty += $value['qnty_pcs']; ?></td>
						</tr>

					<? } ?>
					<tr>
						<th>Total</th>
						<th><? echo $total_qty; ?></th>
					</tr>
				</table>
			</div>
		<? } ?>
	</div>

	<? echo signature_table(124, $company, "930px", "", "50px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "sales_roll_issue_challan_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

	$lib_mc_arr = array();
	$mc_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($mc_sql as $row) {
		$lib_mc_arr[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
	}

	$dataArray = sql_select("select issue_purpose, issue_date, location_id, knit_dye_source, knit_dye_company, batch_no,remarks from inv_issue_master where id=$update_id");
	$loc_dataArray = sql_select("select location_id from inv_receive_master where company_id=$company");



	$product_array = array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach ($product_sql as $row) {
		$product_array[$row[csf("id")]]['gsm'] = $row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width'] = $row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id'] = $row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom'] = $row[csf("unit_of_measure")];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

?>
	<div>
		<table width="1030" cellspacing="0" style="font-size: 12px;">
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[3]; ?>Grey Fabric Roll Issue Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:18px; font-weight:bold;"><strong>Challan No :</strong></td>
				<td width="175" style="font-size:18px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
				<td width="130" style="font-size:16px; font-weight:bold;"><strong>Dyeing Company:</strong> </td>
				<td style="font-size:18px; font-weight:bold;">
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
				<td><strong>Issue Purpose:</strong></td>
				<td><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			</tr>
			<tr>
				<td width="125"><strong>Delivery Date:</strong></td>
				<td width="175"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
				<td><strong>Location:</strong></td>
				<td><? echo $location_arr[$loc_dataArray[2][csf('location_id')]]; ?></td>
				<td><strong>Batch Number:</strong></td>
				<td><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td>
				<td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				<td><strong>Barcode:</strong></td>
				<td colspan="3" id="barcode_img_id"></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Remarks:</strong></td>
				<td><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1700" class="rpt_table" style="font-family: tahoma; font-size: 12px;">
			<thead bgcolor="#dddddd" align="center" style="font-size:12px">
				<th width="30">SL</th>
				<th width="60">System ID</th>
				<th width="100">PO Buyer /<br>Style</th>
				<th width="110">Sales Order No</th>
				<th width="110">Prog./ Book. No</th>
				<th width="80">Production Basis</th>
				<th width="80">Knitting Company</th>
				<th width="60">Yarn Count</th>
				<th width="80">Yarn Brand</th>
				<th width="60">Lot No</th>
				<th width="80">Fab Color</th>
				<th width="80">Color Range</th>
				<th width="100">Fabrication</th>
				<th width="60">Stich Length</th>
				<th width="60">Spandex S.L</th>
				<th width="60">Fin GSM</th>
				<th width="40">Fab. Dia</th>
				<th width="40">MC. No</th>
				<th width="40">MC. Dia</th>
				<th width="40">MC. Gauge</th>
				<th width="80">Barcode No</th>
				<th width="100">Program Batch No</th>
				<th width="40">Roll No</th>
				<th>QC Pass Qty</th>
			</thead>
			<?
			$i = 1;
			$tot_qty = 0;
			$all_order_id = $all_program_no = $all_barcode_no = "";
			$roll_details = array();
			$sql = "select a.mst_id, a.basis, a.program_no, a.prod_id, a.issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, b.roll_no, b.barcode_no, b.po_breakdown_id, b.qnty as quantity, b.booking_no, b.booking_without_order from inv_issue_master c,inv_grey_fabric_issue_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=c.id and a.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$result = sql_select($sql);
			foreach ($result as $row) {
				$all_order_id	.= $row[csf("po_breakdown_id")] . ",";
				$all_barcode_no	.= $row[csf("barcode_no")] . ",";
				$roll_details[$row[csf("barcode_no")]]["system_id"] = $row[csf("mst_id")];
				$roll_details[$row[csf("barcode_no")]]["basis"] 	= $row[csf("basis")];
				$roll_details[$row[csf("barcode_no")]]["brand_id"]  = $row[csf("brand_id")];
				$roll_details[$row[csf("barcode_no")]]["yarn_lot"]  = $row[csf("yarn_lot")];
				$roll_details[$row[csf("barcode_no")]]["yarn_count"] = $row[csf("yarn_count")];
				$roll_details[$row[csf("barcode_no")]]["prod_id"]   = $row[csf("prod_id")];
				$roll_details[$row[csf("barcode_no")]]["stitch_length"]  = $row[csf("stitch_length")];
			}
			$all_order_id	= implode(",", array_unique(explode(",", chop($all_order_id, ","))));
			$all_barcode_no	= implode(",", array_unique(explode(",", chop($all_barcode_no, ","))));
			$sales_barcode_no = "";
			if ($all_barcode_no != "") {
				$production_sql = sql_select("select a.booking_id, a.booking_no, a.receive_basis,a.knitting_company,a.knitting_source,a.recv_number, c.barcode_no,c.qnty,c.is_sales,c.po_breakdown_id,b.machine_dia,b.machine_gg,b.machine_no_id,b.prod_id, b.color_id,c.roll_no,b.width,b.gsm,b.color_range_id,b.febric_description_id,b.brand_id from  inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and b.status_active=1 and c.status_active=1 and c.barcode_no in($all_barcode_no)");

				$production_data_arr = array();
				$sales_ids = $po_ids = "";
				foreach ($production_sql as $pr_row) {
					$system_id 		= $roll_details[$pr_row[csf("barcode_no")]]["system_id"];
					$yarn 			= $roll_details[$pr_row[csf("barcode_no")]]["prod_id"];
					$yarn_count 	= $roll_details[$pr_row[csf("barcode_no")]]["yarn_count"];
					$yarn_lot 		= $roll_details[$pr_row[csf("barcode_no")]]["yarn_lot"];
					$stitch_length 	= $roll_details[$pr_row[csf("barcode_no")]]["stitch_length"];
					$booking_ref 	= explode("-", $pr_row[csf("booking_no")]);

					$production_data_arr[$pr_row[csf("machine_no_id")]][$pr_row[csf("knitting_company")]][$yarn_lot][$pr_row[csf("width")]][$pr_row[csf("barcode_no")]] = $pr_row[csf("po_breakdown_id")] . "**" . $pr_row[csf("booking_no")] . "**" . $pr_row[csf("receive_basis")] . "**" . $yarn_count . "**" . $yarn_lot . "**" . $pr_row[csf("color_id")] . "**" . $stitch_length . "**" . $pr_row[csf("gsm")] . "**" . $pr_row[csf("width")] . "**" . $pr_row[csf("machine_no_id")] . "**" . $pr_row[csf("machine_dia")] . "**" . $pr_row[csf("machine_gg")] . "**" . $pr_row[csf("barcode_no")] . "**" . $pr_row[csf("qnty")] . "**" . $pr_row[csf("roll_no")] . "**" . $pr_row[csf("is_sales")] . "**" . $system_id . "**" . $pr_row[csf("color_range_id")] . "**" . $pr_row[csf("febric_description_id")] . "**" . $pr_row[csf("knitting_source")] . "**" . $pr_row[csf("brand_id")];

					if ($pr_row[csf("receive_basis")] == 2) {
						$all_program_no .= $pr_row[csf("booking_id")] . ",";
					}
					if ($pr_row[csf("receive_basis")] == 1) {
						$all_booking_no .= $pr_row[csf("booking_no")] . ",";
					}

					if ($pr_row[csf("is_sales")] == 1) {
						$sales_ids .= $pr_row[csf("po_breakdown_id")] . ",";
						$sales_barcode_no .= $pr_row[csf("barcode_no")] . ",";
					} else {
						$po_ids .= $pr_row[csf("po_breakdown_id")] . ",";
					}
				}
			}
			$po_ids = rtrim($po_ids, ",");
			$sales_ids = rtrim($sales_ids, ",");
			$sales_barcode_no = chop($sales_barcode_no, ",");

			$job_array = array();
			$job_sql = "select a.style_ref_no,a.job_no,a.buyer_name,b.id,b.po_number,b.file_no,b.grouping from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_ids)";
			$job_sql_result = sql_select($job_sql);
			foreach ($job_sql_result as $row) {
				$job_array[$row[csf('id')]]['job']		= $row[csf('job_no')];
				$job_array[$row[csf('id')]]['buyer']	= $row[csf('buyer_name')];
				$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$job_array[$row[csf('id')]]['po']		= $row[csf('po_number')];
			}

			$booking_array = array();
			$booking_sql = "select a.booking_no,a.buyer_id,b.style_ref_no from wo_booking_mst a, wo_booking_dtls c,wo_po_details_master b where c.job_no=b.job_no and a.booking_no=c.booking_no and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no,a.buyer_id,b.style_ref_no";
			$booking_sql_result = sql_select($booking_sql);
			foreach ($booking_sql_result as $row) {
				$booking_array[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
				$booking_array[$row[csf('booking_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
			}
			$sales_array = array();
			//$sales_barcode_no
			if ($sales_barcode_no == "") $sales_barcode_no = 0;
			$sales_sql = "select a.id, a.job_no, a.sales_booking_no, a.within_group, a.buyer_id, a.style_ref_no, b.barcode_no
			from fabric_sales_order_mst a, pro_roll_details b
			where a.id=b.po_breakdown_id and b.barcode_no in($sales_barcode_no) and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$sales_sql_result = sql_select($sales_sql);
			foreach ($sales_sql_result as $row) {
				$sales_array[$row[csf('barcode_no')]]['job']			= $row[csf('job_no')];
				$sales_array[$row[csf('barcode_no')]]['booking_no']		= $row[csf('sales_booking_no')];
				$sales_array[$row[csf('barcode_no')]]['within_group']	= $row[csf('within_group')];
				$sales_array[$row[csf('barcode_no')]]['buyer_id']		= $row[csf('buyer_id')];
				$sales_array[$row[csf('barcode_no')]]['style_ref_no']	= $row[csf('style_ref_no')];
			}

			$all_program_no = implode(",", array_unique(explode(",", chop($all_program_no, ","))));
			if ($all_program_no != "") {
				$program_sql = sql_select("SELECT a.booking_id,b.spandex_stitch_length,c.booking_no,c.within_group, b.id as program_no, b.batch_no from inv_receive_master a,ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c where a.booking_id=b.id and b.mst_id=c.id and a.entry_form=2 and a.receive_basis=2 and a.booking_id in($all_program_no)");

				$prog_book_arr = array();
				$program_batch_arr = array();
				foreach ($program_sql as $row) {
					$booking_ref = $row[csf("booking_no")];
					$prog_book_arr[$row[csf("booking_id")]] = $row[csf("booking_no")];
					$spandexsl_arr[$row[csf("booking_id")]]['spandexsl'] = $row[csf('spandex_stitch_length')];
					$program_batch_arr[$row[csf("program_no")]] =  $row[csf("batch_no")];
				}
			}
			$grand_tot_roll = $grand_tot_qty = 0;
			rsort($production_data_arr);
			foreach ($production_data_arr as $machine_id => $machine_row) {
				foreach ($machine_row as $knitting_company => $knit_row) {
					foreach ($knit_row as $lot => $lot_row) {
						foreach ($lot_row as $fab_dia => $fab_dia_row) {
							$tot_roll = $tot_qty = 0;
							foreach ($fab_dia_row as $barcode => $barcode_info) {
								$barcode_row = explode("**", $barcode_info);
								$job_no = ($barcode_row[15] == 1) ? $sales_array[$barcode]['job'] : $job_array[$barcode_row[0]]['job'];

								if ($barcode_row[15] == 1) { // is sales
									$within_group = $sales_array[$barcode]['within_group'];
									$buyer = ($within_group == 1) ? $buyer_array[$booking_array[$sales_array[$barcode]['booking_no']]['buyer_id']] : $buyer_array[$sales_array[$barcode]['buyer_id']];
									$style = ($within_group == 1) ? $booking_array[$sales_array[$barcode]['booking_no']]['style_ref_no'] : $sales_array[$barcode]['style_ref_no'];
								} else {
									$buyer = $buyer_array[$job_array[$barcode_row[0]]['buyer']];
									$style = $job_array[$barcode_row[0]]['style_ref'];
								}

								$book_prog_challan = "";
								if ($barcode_row[2] == 1) {
									$book_prog_challan = "<b>B.N</b>. " . $barcode_row[1] . "<br>";
								} else {
									//$book_prog_challan="<b>B.N</b>. ".$prog_book_arr[$barcode_row[1]]."<br>";
									$book_prog_challan = "<b>B.N</b>. " . $sales_array[$barcode]['booking_no'] . "<br>";
									$book_prog_challan .= "<b>P.N</b>. " . $barcode_row[1] . "<br>";
									$program_batch = $program_batch_arr[$barcode_row[1]];
								}
								$color_names = "";
								$color_array = explode(",", $barcode_row[5]);
								foreach ($color_array as $color_row) {
									$color_names .= $color_arr[$color_row]  . ",";
								}
								$count = '';
								$yarn_count = explode(",", $barcode_row[3]);
								foreach ($yarn_count as $count_id) {
									if ($count == '') $count = $yarn_count_details[$count_id];
									else $count .= "," . $yarn_count_details[$count_id];
								}
								$knitting_com = ($barcode_row[19] == 1) ? $company_arr[$knitting_company] : $supplier_arr[$knitting_company];
								$spandexsl = $spandexsl_arr[$barcode_row[1]]['spandexsl'];
								$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan", 4 => "Sales Order");

								//echo $barcode_row[1];
			?>
								<tr style="font-size:11px">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="60" align="center"><? echo $barcode_row[16]; ?></td>
									<td width="100" style="word-break:break-all;"><b>B:</b> <? echo $buyer . "<br /><b>S:</b> " . $style; ?></td>
									<td width="110" style="word-break:break-all; text-align: center;"><? echo $job_no; ?></td>
									<td width="110"><? echo $book_prog_challan; ?></td>
									<td width="80" align="center"><? echo $receive_basis[$barcode_row[2]]; ?></td>
									<td width="80" align="center"><? echo $knitting_com; ?></td>
									<td width="80" style="word-break:break-all;" align="center"><? echo $count; //$barcode_row[3]; 
																								?></td>
									<td width="80" style="word-break:break-all;" align="center"><? echo $brand_arr[$barcode_row[20]]; ?></td>
									<td width="60" style="word-break:break-all;" align="center"><? echo $barcode_row[4]; ?></td>
									<td width="80" style="word-break:break-all;"><? echo rtrim($color_names, ","); ?></td>
									<td width="80" style="word-break:break-all;" align="center"><? echo $color_range[$barcode_row[17]]; ?></td>
									<td width="100" align="center"><? echo $composition_arr[$barcode_row[18]]; ?></td>
									<td width="60" align="center"><? echo $barcode_row[6]; ?></td>
									<td width="60" style="word-break:break-all;" align="center"><? echo $spandexsl; ?></td>
									<td width="60" style="word-break:break-all;" align="center"><? echo $barcode_row[7]; ?></td>
									<td width="40" style="word-break:break-all;" align="center"><? echo $barcode_row[8]; ?></td>
									<td width="40" align="center"><? echo $lib_mc_arr[$barcode_row[9]]['no']; ?></td>
									<td width="40" style="word-break:break-all;" align="center"><? echo $barcode_row[10]; ?></td>
									<td width="40" align="center" style="word-break:break-all;"><? echo $barcode_row[11]; ?></td>
									<td width="70" align="center"><? echo $barcode_row[12]; ?></td>
									<td width="100" align="center"><? echo $program_batch; ?></td>
									<td width="40" style="word-break:break-all;" align="center"><? echo $barcode_row[14]; ?></td>
									<td align="right"><? echo number_format($barcode_row[13], 2); ?></td>
								</tr>
							<?php
								$tot_roll = $tot_roll + 1; // roll total
								$tot_qty += $barcode_row[13]; // qc pass qnty total
								$i++;
							}
							?>
							<tr style="font-size:12px; background-image:linear-gradient(#ccc, #fefefe);">
								<td align="right" colspan="22"><strong>Total:</strong></td>
								<td align="center"><strong><? echo number_format($tot_roll); ?></strong></td>
								<td align="right"><strong><? echo number_format($tot_qty, 2, '.', ''); ?></strong></td>
							</tr>
			<?php
							$grand_tot_roll += $tot_roll; // roll grand total
							$grand_tot_qty += $tot_qty; // qc pass qnty grand total
						}
					}
				}
			}
			?>
			<tr style="font-size:12px;">
				<td align="right" colspan="22"><strong>Grand Total:</strong></td>
				<td align="center"><strong><? echo number_format($grand_tot_roll); ?></strong></td>
				<td align="right"><strong><? echo number_format($grand_tot_qty, 2, '.', ''); ?></strong></td>
			</tr>
		</table>
	</div>
	<? echo signature_table(124, $company, "1700px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

if ($action == "sales_roll_issue_challan_print2") // Print 2 for Palmal
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	$floorRoomRackShelf_array = return_library_array("SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst WHERE company_id=" . $company . "", "floor_room_rack_id", "floor_room_rack_name");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

	$lib_mc_arr = array();
	$mc_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($mc_sql as $row) {
		$lib_mc_arr[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
	}

	$dataArray = sql_select("select issue_purpose, issue_date, location_id, knit_dye_source, knit_dye_company, batch_no,remarks from inv_issue_master where id=$update_id");
	$loc_dataArray = sql_select("select location_id from inv_receive_master where company_id=$company");



	$product_array = array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach ($product_sql as $row) {
		$product_array[$row[csf("id")]]['gsm'] = $row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width'] = $row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id'] = $row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom'] = $row[csf("unit_of_measure")];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

?>
	<div>
		<table width="1030" cellspacing="0" style="font-size: 12px;">
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[3]; ?>Grey Fabric Roll Issue Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:18px; font-weight:bold;"><strong>Challan No :</strong></td>
				<td width="175" style="font-size:18px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
				<td width="130" style="font-size:16px; font-weight:bold;"><strong>Dyeing Company:</strong> </td>
				<td style="font-size:18px; font-weight:bold;">
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
				<td><strong>Issue Purpose:</strong></td>
				<td><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			</tr>
			<tr>
				<td width="125"><strong>Delivery Date:</strong></td>
				<td width="175"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
				<td><strong>Location:</strong></td>
				<td><? echo $location_arr[$loc_dataArray[2][csf('location_id')]]; ?></td>
				<td><strong>Batch Number:</strong></td>
				<td><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td>
				<td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				<td><strong>Barcode:</strong></td>
				<td colspan="3" id="barcode_img_id"></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Remarks:</strong></td>
				<td><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1780" class="rpt_table" style="font-family: tahoma; font-size: 12px;">
			<thead bgcolor="#dddddd" align="center" style="font-size:12px">
				<th width="30">SL</th>
				<th width="60">Production ID</th>
				<th width="80">Customer/<br>Customer Buyer</th>
				<th width="110">Sales Order No</th>
				<th width="110">Prog./ Book. No</th>
				<th width="80">Production Basis</th>
				<th width="80">Knitting Company</th>
				<th width="60">Yarn Count</th>
				<th width="80">Yarn Brand</th>
				<th width="60">Lot No</th>
				<th width="80">Fab Color</th>
				<th width="80">Color Range</th>
				<th width="100">Fabrication</th>
				<th width="60">Stich Length</th>
				<th width="60">Fin GSM</th>
				<th width="40">Fab. Dia</th>
				<th width="40">MC. No</th>
				<th width="40">MC. Dia</th>
				<th width="40">MC. Gauge</th>
				<th width="80">Barcode No</th>
				<th width="40">Roll No</th>
				<th width="50">QC Pass Qty</th>
				<th width="80">Rack<br>Shelf</th>
			</thead>
			<?
			$i = 1;
			$tot_qty = 0;
			$all_order_id = $all_program_no = $all_barcode_no = "";
			$roll_details = array();
			$sql = "SELECT a.mst_id, a.basis, a.program_no, a.prod_id, a.issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, b.roll_no, b.barcode_no, b.po_breakdown_id, b.qnty as quantity, b.booking_no, b.booking_without_order, b.is_sales
			from inv_issue_master c,inv_grey_fabric_issue_dtls a, pro_roll_details b
			where a.id=b.dtls_id and a.mst_id=c.id and a.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$result = sql_select($sql);
			$sales_barcode_no = "";
			$sales_ids = $po_ids = "";
			foreach ($result as $row) {
				$all_order_id	.= $row[csf("po_breakdown_id")] . ",";
				$all_barcode_no	.= $row[csf("barcode_no")] . ",";
				$roll_details[$row[csf("barcode_no")]]["system_id"] = $row[csf("mst_id")];
				$roll_details[$row[csf("barcode_no")]]["basis"] 	= $row[csf("basis")];
				$roll_details[$row[csf("barcode_no")]]["brand_id"]  = $row[csf("brand_id")];
				$roll_details[$row[csf("barcode_no")]]["yarn_lot"]  = $row[csf("yarn_lot")];
				$roll_details[$row[csf("barcode_no")]]["yarn_count"] = $row[csf("yarn_count")];
				$roll_details[$row[csf("barcode_no")]]["prod_id"]   = $row[csf("prod_id")];
				$roll_details[$row[csf("barcode_no")]]["stitch_length"]  = $row[csf("stitch_length")];
				$roll_details[$row[csf("barcode_no")]]["rack"]  = $row[csf("rack")];
				$roll_details[$row[csf("barcode_no")]]["self"]  = $row[csf("self")];
				$roll_details[$row[csf("barcode_no")]]["quantity"]  = $row[csf("quantity")];

				if ($row[csf("is_sales")] == 1) {
					$sales_ids .= $row[csf("po_breakdown_id")] . ",";
					$sales_barcode_no .= $row[csf("barcode_no")] . ",";
				} else {
					$po_ids .= $row[csf("po_breakdown_id")] . ",";
				}
			}
			$all_order_id	= implode(",", array_unique(explode(",", chop($all_order_id, ","))));
			$all_barcode_no	= implode(",", array_unique(explode(",", chop($all_barcode_no, ","))));

			// ========================== Split Check ===========================
			if ($all_barcode_no != "") {
				$split_chk_sql = sql_select("SELECT c.barcode_no , c.qnty from pro_roll_split b, pro_roll_details c where b.entry_form =75 and b.split_from_id = c.roll_split_from and b.status_active=1 and c.status_active=1 and c.barcode_no in($all_barcode_no)");

				if (!empty($split_chk_sql)) {
					foreach ($split_chk_sql as $val) {
						$split_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
					}

					$split_barcodes = implode(",", $split_barcode_arr);
					if ($db_type == 2 && count($split_barcode_arr) > 999) {
						$split_barcode_arr_chunk = array_chunk($split_barcode_arr, 999);
						$split_barcode_cond = " and (";

						foreach ($split_barcode_arr_chunk as $chunk_arr) {
							$split_barcode_cond .= " a.barcode_no in(" . implode(",", $chunk_arr) . ") or ";
						}

						$split_barcode_cond = chop($split_barcode_cond, "or ");
						$split_barcode_cond .= ")";
					} else {
						$split_barcode_cond = " and a.barcode_no in($split_barcodes)";
					}

					// echo "select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1 $split_barcode_cond";
					$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1 $split_barcode_cond");

					if (!empty($split_ref_sql)) {
						foreach ($split_ref_sql as $value) {
							//$mother_barcode_arr[$value[csf("mother_barcode")]] = $value[csf("barcode_no")];
							$mother_barcode_arr[$value[csf("mother_barcode")]] .= $value[csf("barcode_no")] . ',';
						}
					}
				}
			}
			// ========================== Split Check ===========================

			// $sales_barcode_no="";
			if ($all_barcode_no != "") {
				$production_sql = sql_select("SELECT a.recv_number_prefix_num, a.booking_id, a.booking_no, a.receive_basis,a.knitting_company,a.knitting_source,a.recv_number, c.barcode_no,c.qnty,c.is_sales,c.po_breakdown_id,b.machine_dia,b.machine_gg,b.machine_no_id,b.prod_id, b.color_id,c.roll_no,b.width,b.gsm,b.color_range_id,b.febric_description_id,b.brand_id
				from  inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c
				where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and b.status_active=1 and c.status_active=1 and c.barcode_no in($all_barcode_no)");

				$production_data_arr = array();
				// $sales_ids = $po_ids = "";
				foreach ($production_sql as $pr_row) {
					$system_id 		= $roll_details[$pr_row[csf("barcode_no")]]["system_id"];
					$yarn 			= $roll_details[$pr_row[csf("barcode_no")]]["prod_id"];
					$yarn_count 	= $roll_details[$pr_row[csf("barcode_no")]]["yarn_count"];
					$yarn_lot 		= $roll_details[$pr_row[csf("barcode_no")]]["yarn_lot"];
					$stitch_length 	= $roll_details[$pr_row[csf("barcode_no")]]["stitch_length"];
					$rack 	= $roll_details[$pr_row[csf("barcode_no")]]["rack"];
					$self 	= $roll_details[$pr_row[csf("barcode_no")]]["self"];
					$qnty 	= $roll_details[$pr_row[csf("barcode_no")]]["quantity"];
					$booking_ref 	= explode("-", $pr_row[csf("booking_no")]);

					$production_data_arr[$pr_row[csf("machine_no_id")]][$pr_row[csf("knitting_company")]][$yarn_lot][$pr_row[csf("width")]][$pr_row[csf("barcode_no")]] = $pr_row[csf("po_breakdown_id")] . "**" . $pr_row[csf("booking_no")] . "**" . $pr_row[csf("receive_basis")] . "**" . $yarn_count . "**" . $yarn_lot . "**" . $pr_row[csf("color_id")] . "**" . $stitch_length . "**" . $pr_row[csf("gsm")] . "**" . $pr_row[csf("width")] . "**" . $pr_row[csf("machine_no_id")] . "**" . $pr_row[csf("machine_dia")] . "**" . $pr_row[csf("machine_gg")] . "**" . $pr_row[csf("barcode_no")] . "**" . $qnty . "**" . $pr_row[csf("roll_no")] . "**" . $pr_row[csf("is_sales")] . "**" . $pr_row[csf("recv_number_prefix_num")] . "**" . $pr_row[csf("color_range_id")] . "**" . $pr_row[csf("febric_description_id")] . "**" . $pr_row[csf("knitting_source")] . "**" . $pr_row[csf("brand_id")] . "**" . $rack . "**" . $self;

					$mother_barcode_no = $mother_barcode_arr[$pr_row[csf("barcode_no")]];
					if ($mother_barcode_no != "") {
						$mother_chaild_barcode_arr = explode(",", chop($mother_barcode_no, ","));
						foreach ($mother_chaild_barcode_arr as $key => $cbarcode) {
							$qnty 	= $roll_details[$cbarcode]["quantity"];

							$production_data_arr[$pr_row[csf("machine_no_id")]][$pr_row[csf("knitting_company")]][$yarn_lot][$pr_row[csf("width")]][$cbarcode] = $pr_row[csf("po_breakdown_id")] . "**" . $pr_row[csf("booking_no")] . "**" . $pr_row[csf("receive_basis")] . "**" . $yarn_count . "**" . $yarn_lot . "**" . $pr_row[csf("color_id")] . "**" . $stitch_length . "**" . $pr_row[csf("gsm")] . "**" . $pr_row[csf("width")] . "**" . $pr_row[csf("machine_no_id")] . "**" . $pr_row[csf("machine_dia")] . "**" . $pr_row[csf("machine_gg")] . "**" . $cbarcode . "**" . $qnty . "**" . $pr_row[csf("roll_no")] . "**" . $pr_row[csf("is_sales")] . "**" . $pr_row[csf("recv_number_prefix_num")] . "**" . $pr_row[csf("color_range_id")] . "**" . $pr_row[csf("febric_description_id")] . "**" . $pr_row[csf("knitting_source")] . "**" . $pr_row[csf("brand_id")] . "**" . $rack . "**" . $self;
						}
					}

					if ($pr_row[csf("receive_basis")] == 2) {
						$all_program_no .= $pr_row[csf("booking_id")] . ",";
					}
					if ($pr_row[csf("receive_basis")] == 1) {
						$all_booking_no .= $pr_row[csf("booking_no")] . ",";
					}

					/*if($pr_row[csf("is_sales")] == 1)
					{
						$sales_ids .= $pr_row[csf("po_breakdown_id")] . ",";
						$sales_barcode_no .=$pr_row[csf("barcode_no")].",";
					}
					else
					{
						$po_ids .= $pr_row[csf("po_breakdown_id")] . ",";
					}*/
				}
			}
			$po_ids = rtrim($po_ids, ",");
			$sales_ids = rtrim($sales_ids, ",");
			$sales_barcode_no = chop($sales_barcode_no, ",");

			$job_array = array();
			$job_sql = "select a.style_ref_no,a.job_no,a.buyer_name,b.id,b.po_number,b.file_no,b.grouping from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_ids)";
			$job_sql_result = sql_select($job_sql);
			foreach ($job_sql_result as $row) {
				$job_array[$row[csf('id')]]['job']		= $row[csf('job_no')];
				$job_array[$row[csf('id')]]['buyer']	= $row[csf('buyer_name')];
				$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$job_array[$row[csf('id')]]['po']		= $row[csf('po_number')];
			}

			$booking_array = array();
			$booking_sql = "select a.booking_no,a.buyer_id,b.style_ref_no from wo_booking_mst a, wo_booking_dtls c,wo_po_details_master b where c.job_no=b.job_no and a.booking_no=c.booking_no and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no,a.buyer_id,b.style_ref_no";
			$booking_sql_result = sql_select($booking_sql);
			foreach ($booking_sql_result as $row) {
				$booking_array[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
				$booking_array[$row[csf('booking_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
			}
			$sales_array = array();
			//$sales_barcode_no
			if ($sales_barcode_no == "") $sales_barcode_no = 0;
			$sales_sql = "select a.id, a.job_no, a.sales_booking_no, a.within_group, a.buyer_id, a.customer_buyer, a.style_ref_no, b.barcode_no
			from fabric_sales_order_mst a, pro_roll_details b
			where a.id=b.po_breakdown_id and b.barcode_no in($sales_barcode_no) and b.mst_id=$update_id and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$sales_sql_result = sql_select($sales_sql);
			foreach ($sales_sql_result as $row) {
				$sales_array[$row[csf('barcode_no')]]['job']			= $row[csf('job_no')];
				$sales_array[$row[csf('barcode_no')]]['booking_no']		= $row[csf('sales_booking_no')];
				$sales_array[$row[csf('barcode_no')]]['within_group']	= $row[csf('within_group')];
				$sales_array[$row[csf('barcode_no')]]['buyer_id']		= $row[csf('buyer_id')];
				$sales_array[$row[csf('barcode_no')]]['style_ref_no']	= $row[csf('style_ref_no')];
				$sales_array[$row[csf('barcode_no')]]['customer_buyer']	= $row[csf('customer_buyer')];
			}

			$all_program_no = implode(",", array_unique(explode(",", chop($all_program_no, ","))));
			if ($all_program_no != "") {
				$program_sql = sql_select("SELECT a.booking_id,b.spandex_stitch_length,c.booking_no,c.within_group, b.id as program_no, b.batch_no from inv_receive_master a,ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c where a.booking_id=b.id and b.mst_id=c.id and a.entry_form=2 and a.receive_basis=2 and a.booking_id in($all_program_no)");

				$prog_book_arr = array();
				$program_batch_arr = array();
				foreach ($program_sql as $row) {
					$booking_ref = $row[csf("booking_no")];
					$prog_book_arr[$row[csf("booking_id")]] = $row[csf("booking_no")];
					$spandexsl_arr[$row[csf("booking_id")]]['spandexsl'] = $row[csf('spandex_stitch_length')];
					$program_batch_arr[$row[csf("program_no")]] =  $row[csf("batch_no")];
				}
			}
			$grand_tot_roll = $grand_tot_qty = 0;
			rsort($production_data_arr);
			foreach ($production_data_arr as $machine_id => $machine_row) {
				foreach ($machine_row as $knitting_company => $knit_row) {
					foreach ($knit_row as $lot => $lot_row) {
						foreach ($lot_row as $fab_dia => $fab_dia_row) {
							$tot_roll = $tot_qty = 0;
							foreach ($fab_dia_row as $barcode => $barcode_info) {
								$barcode_row = explode("**", $barcode_info);
								$job_no = ($barcode_row[15] == 1) ? $sales_array[$barcode]['job'] : $job_array[$barcode_row[0]]['job'];

								if ($barcode_row[15] == 1)  // is sales
								{
									$within_group = $sales_array[$barcode]['within_group'];
									$buyer = ($within_group == 1) ? $buyer_array[$booking_array[$sales_array[$barcode]['booking_no']]['buyer_id']] : $buyer_array[$sales_array[$barcode]['buyer_id']];

									$customer_buyer = ($within_group == 1) ? $buyer_array[$booking_array[$sales_array[$barcode]['booking_no']]['buyer_id']] : $buyer_array[$sales_array[$barcode]['customer_buyer']];

									$style = ($within_group == 1) ? $booking_array[$sales_array[$barcode]['booking_no']]['style_ref_no'] : $sales_array[$barcode]['style_ref_no'];
								} else {
									$buyer = $buyer_array[$job_array[$barcode_row[0]]['buyer']];
									$customer_buyer = "";
									$style = $job_array[$barcode_row[0]]['style_ref'];
								}

								$book_prog_challan = "";
								if ($barcode_row[2] == 1) {
									$book_prog_challan = "<b>B.N</b>. " . $barcode_row[1] . "<br>";
								} else {
									//$book_prog_challan="<b>B.N</b>. ".$prog_book_arr[$barcode_row[1]]."<br>";
									$book_prog_challan = "<b>B.N</b>. " . $sales_array[$barcode]['booking_no'] . "<br>";
									$book_prog_challan .= "<b>P.N</b>. " . $barcode_row[1] . "<br>";
									$program_batch = $program_batch_arr[$barcode_row[1]];
								}
								$color_names = "";
								$color_array = explode(",", $barcode_row[5]);
								foreach ($color_array as $color_row) {
									$color_names .= $color_arr[$color_row]  . ",";
								}
								$count = '';
								$yarn_count = explode(",", $barcode_row[3]);
								foreach ($yarn_count as $count_id) {
									if ($count == '') $count = $yarn_count_details[$count_id];
									else $count .= "," . $yarn_count_details[$count_id];
								}
								$knitting_com = ($barcode_row[19] == 1) ? $company_arr[$knitting_company] : $supplier_arr[$knitting_company];
								$spandexsl = $spandexsl_arr[$barcode_row[1]]['spandexsl'];
								$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan", 4 => "Sales Order");

								//echo $barcode_row[1];
			?>
								<tr style="font-size:11px">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="60" align="center"><? echo $barcode_row[16]; ?></td>
									<td width="80" style="word-break:break-all;"><b>B:</b> <? echo $buyer . "<br /><b>C.B:</b> " . $customer_buyer; ?></td>
									<td width="110" style="word-break:break-all; text-align: center;"><? echo $job_no; ?></td>
									<td width="110"><? echo $book_prog_challan; ?></td>
									<td width="80" align="center"><? echo $receive_basis[$barcode_row[2]]; ?></td>
									<td width="80" align="center"><? echo $knitting_com; ?></td>
									<td width="80" style="word-break:break-all;" align="center"><? echo $count; //$barcode_row[3]; 
																								?></td>
									<td width="80" style="word-break:break-all;" align="center"><? echo $brand_arr[$barcode_row[20]]; ?></td>
									<td width="60" style="word-break:break-all;" align="center"><? echo $barcode_row[4]; ?></td>
									<td width="80" style="word-break:break-all;"><? echo rtrim($color_names, ","); ?></td>
									<td width="80" style="word-break:break-all;" align="center"><? echo $color_range[$barcode_row[17]]; ?></td>
									<td width="100" align="center"><? echo $composition_arr[$barcode_row[18]]; ?></td>
									<td width="60" align="center"><? echo $barcode_row[6]; ?></td>
									<td width="60" style="word-break:break-all;" align="center"><? echo $barcode_row[7]; ?></td>
									<td width="40" style="word-break:break-all;" align="center"><? echo $barcode_row[8]; ?></td>
									<td width="40" align="center"><? echo $lib_mc_arr[$barcode_row[9]]['no']; ?></td>
									<td width="40" style="word-break:break-all;" align="center"><? echo $barcode_row[10]; ?></td>
									<td width="40" align="center" style="word-break:break-all;"><? echo $barcode_row[11]; ?></td>
									<td width="70" align="center" title="barcode_no"><? echo $barcode_row[12]; ?></td>
									<td width="40" style="word-break:break-all;" align="center"><? echo $barcode_row[14]; ?></td>
									<td width="50" align="right"><? echo number_format($barcode_row[13], 2); ?></td>
									<td width="80"><?
													echo '<b>R: </b>' . $floorRoomRackShelf_array[$barcode_row[21]] . '<br>';
													echo '<b>S: </b>' . $floorRoomRackShelf_array[$barcode_row[22]]; ?></td>
								</tr>
							<?php
								$tot_roll = $tot_roll + 1; // roll total
								$tot_qty += $barcode_row[13]; // qc pass qnty total
								$i++;
							}
							?>
							<tr style="font-size:12px; background-image:linear-gradient(#ccc, #fefefe);">
								<td align="right" colspan="20"><strong>Total:</strong></td>
								<td align="center"><strong><? echo number_format($tot_roll); ?></strong></td>
								<td align="right"><strong><? echo number_format($tot_qty, 2, '.', ''); ?></strong></td>
								<td align="right"><strong></strong></td>
							</tr>
			<?php
							$grand_tot_roll += $tot_roll; // roll grand total
							$grand_tot_qty += $tot_qty; // qc pass qnty grand total
						}
					}
				}
			}
			?>
			<tr style="font-size:12px;">
				<td align="right" colspan="20"><strong>Grand Total:</strong></td>
				<td align="center"><strong><? echo number_format($grand_tot_roll); ?></strong></td>
				<td align="right"><strong><? echo number_format($grand_tot_qty, 2, '.', ''); ?></strong></td>
				<td align="right"><strong></strong></td>
			</tr>
		</table>
	</div>
	<? echo signature_table(124, $company, "1700px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
	<?
	exit();
}

if ($action == "sales_roll_issue_challan_print3") // Print 4
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];
	$no_copy = $data[3];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where status_active=1 and is_deleted=0");
	$floorRoomRackShelf_array = return_library_array("SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst WHERE company_id=" . $company . "", "floor_room_rack_id", "floor_room_rack_name");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

	$lib_mc_arr = array();
	$mc_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($mc_sql as $row) {
		$lib_mc_arr[$row[csf('id')]]['no'] = $row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge'] = $row[csf('gauge')];
	}

	$dataArray = sql_select("select issue_purpose, issue_date, location_id, knit_dye_source, knit_dye_company, batch_no,remarks from inv_issue_master where id=$update_id");
	$loc_dataArray = sql_select("select location_id from inv_receive_master where company_id=$company");



	$product_array = array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach ($product_sql as $row) {
		$product_array[$row[csf("id")]]['gsm'] = $row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width'] = $row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id'] = $row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom'] = $row[csf("unit_of_measure")];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	for ($x = 1; $x <= $no_copy; $x++) {
		if ($x == 1) {
			$sup = 'st';
		} else if ($x == 2) {
			$sup = 'nd';
		} else if ($x == 3) {
			$sup = 'rd';
		} else {
			$sup = 'th';
		}

		$noOfCopy = "<span style='font-size:x-large;font-weight:bold'>" . $x . "<sup>" . $sup . "</sup> Copy</span>";

	?>
		<div>
			<table width="1030" cellspacing="0" style="font-size: 12px;">
				<tr>
					<td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="5" align="center" style="font-size:14px">
						<? echo show_company($company, '', ''); ?>
					</td>
					<td colspan="1" align="center" style="font-size:14px">
						<? echo $noOfCopy; ?>
					</td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:16px"><strong><u>Grey Fabric Delivery Challan</u></strong></td>
				</tr>
				<tr>
					<td width="140" style="font-size:18px; font-weight:bold;"><strong>Challan No :</strong></td>
					<td width="175" style="font-size:16px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
					<td width="160" style="font-size:16px; font-weight:bold;"><strong>Dyeing Company:</strong> </td>
					<td style="font-size:18px; font-weight:bold;">
						<?
						if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
						else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
						?>
					</td>
					<td><strong>Issue Purpose:</strong></td>
					<td><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
				</tr>
				<tr>
					<td width="140"><strong>Delivery Date:</strong></td>
					<td width="175"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
					<td><strong>Location:</strong></td>
					<td><? echo $location_arr[$loc_dataArray[2][csf('location_id')]]; ?></td>
					<td><strong>Batch Number:</strong></td>
					<td><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Dyeing Source:</strong></td>
					<td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
					<td><strong>Barcode:</strong></td>
					<td colspan="3" id="barcode_img_id"></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><strong>Remarks:</strong></td>
					<td><? echo $dataArray[0][csf('remarks')]; ?></td>
				</tr>
			</table>
			<br>
			<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1400" class="rpt_table" style="font-family: tahoma; font-size: 12px;">
				<thead bgcolor="#dddddd" align="center" style="font-size:12px">
					<th width="30">SL</th>
					<th width="100">IR <br>Buyer Name <br>Program</th>
					<th width="140">Knitting Company</th>
					<th width="80">Yarn Count</th>
					<th width="80">Yarn Brand</th>
					<th width="80">Lot No</th>
					<th width="80">Fab. Color</th>
					<th width="60">Color Range</th>
					<th width="120">Fabrication</th>
					<th width="60">Stich Length</th>
					<th width="80">Fin GSM</th>
					<th width="80">Fab. Dia</th>
					<th width="80">MC DIA</th>

					<th width="60">MC. Gauge</th>
					<th width="40">No of Roll</th>
					<th width="50">QC Pass Qty</th>
					<th width="80">Rack<br>Shelf</th>
				</thead>
				<?
				$i = 1;
				$tot_qty = 0;
				$all_order_id = $all_program_no = $all_barcode_no = "";
				$roll_details = array();
				$sql = "SELECT a.mst_id, a.basis, a.program_no, a.prod_id, a.issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.room, a.rack, a.self, a.brand_id, b.roll_id, b.roll_no, b.barcode_no, b.po_breakdown_id, b.qnty as quantity, b.booking_no, b.booking_without_order, b.is_sales
				from inv_issue_master c,inv_grey_fabric_issue_dtls a, pro_roll_details b
				where a.id=b.dtls_id and a.mst_id=c.id and a.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				//echo $sql;
				$result = sql_select($sql);
				$sales_barcode_no = "";
				$sales_ids = $po_ids = "";
				foreach ($result as $row) {
					$all_order_id	.= $row[csf("po_breakdown_id")] . ",";
					$all_barcode_no	.= $row[csf("barcode_no")] . ",";
					$roll_details[$row[csf("barcode_no")]]["system_id"] = $row[csf("mst_id")];
					$roll_details[$row[csf("barcode_no")]]["basis"] 	= $row[csf("basis")];
					$roll_details[$row[csf("barcode_no")]]["brand_id"]  = $row[csf("brand_id")];
					$roll_details[$row[csf("barcode_no")]]["yarn_lot"]  = $row[csf("yarn_lot")];
					$roll_details[$row[csf("barcode_no")]]["yarn_count"] = $row[csf("yarn_count")];
					$roll_details[$row[csf("barcode_no")]]["prod_id"]   = $row[csf("prod_id")];
					$roll_details[$row[csf("barcode_no")]]["stitch_length"]  = $row[csf("stitch_length")];
					$roll_details[$row[csf("barcode_no")]]["room"]  = $row[csf("room")];
					$roll_details[$row[csf("barcode_no")]]["rack"]  = $row[csf("rack")];
					$roll_details[$row[csf("barcode_no")]]["self"]  = $row[csf("self")];
					$roll_details[$row[csf("barcode_no")]]["quantity"]  = $row[csf("quantity")];

					if ($row[csf("is_sales")] == 1) {
						$sales_ids .= $row[csf("po_breakdown_id")] . ",";
						$sales_barcode_no .= $row[csf("barcode_no")] . ",";
					} else {
						$po_ids .= $row[csf("po_breakdown_id")] . ",";
					}
				}
				$all_order_id	= implode(",", array_unique(explode(",", chop($all_order_id, ","))));
				$all_barcode_no	= implode(",", array_unique(explode(",", chop($all_barcode_no, ","))));

				// ========================== Split Check ===========================
				if ($all_barcode_no != "") {
					$split_chk_sql = sql_select("SELECT c.barcode_no , c.qnty from pro_roll_split b, pro_roll_details c where b.entry_form =75 and b.split_from_id = c.roll_split_from and b.status_active=1 and c.status_active=1 and c.barcode_no in($all_barcode_no)");

					if (!empty($split_chk_sql)) {
						foreach ($split_chk_sql as $val) {
							$split_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
						}

						$split_barcodes = implode(",", $split_barcode_arr);
						if ($db_type == 2 && count($split_barcode_arr) > 999) {
							$split_barcode_arr_chunk = array_chunk($split_barcode_arr, 999);
							$split_barcode_cond = " and (";

							foreach ($split_barcode_arr_chunk as $chunk_arr) {
								$split_barcode_cond .= " a.barcode_no in(" . implode(",", $chunk_arr) . ") or ";
							}

							$split_barcode_cond = chop($split_barcode_cond, "or ");
							$split_barcode_cond .= ")";
						} else {
							$split_barcode_cond = " and a.barcode_no in($split_barcodes)";
						}

						// echo "select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1 $split_barcode_cond";
						$split_ref_sql = sql_select("select a.barcode_no, a.qnty, a.roll_id, b.barcode_no as mother_barcode from pro_roll_details a, pro_roll_details b where a.entry_form = 61 and a.roll_id = b.id and a.status_active =1 and b.status_active=1 $split_barcode_cond");

						if (!empty($split_ref_sql)) {
							foreach ($split_ref_sql as $value) {
								$mother_barcode_arr[$value[csf("mother_barcode")]] = $value[csf("barcode_no")];
							}
						}
					}
				}
				// echo "<pre>";print_r($mother_barcode_arr);
				// ========================== Split Check ===========================

				// $sales_barcode_no="";
				if ($all_barcode_no != "") {
					$production_sql = sql_select("SELECT a.recv_number_prefix_num, a.booking_id, a.booking_no, a.receive_basis,a.knitting_company,a.knitting_source,a.recv_number, c.barcode_no,c.qnty,c.is_sales,c.po_breakdown_id,b.machine_dia,b.machine_gg,b.machine_no_id,b.prod_id, b.color_id,c.roll_no,b.width,b.gsm,b.color_range_id,b.febric_description_id,b.brand_id
					from  inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and b.status_active=1 and c.status_active=1 and c.barcode_no in($all_barcode_no)");
				}
				$po_ids = rtrim($po_ids, ",");
				$sales_ids = rtrim($sales_ids, ",");
				$sales_barcode_no = chop($sales_barcode_no, ",");

				$job_array = array();
				$job_sql = "select a.style_ref_no,a.job_no,a.buyer_name,b.id,b.po_number,b.file_no,b.grouping from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_ids)";
				$job_sql_result = sql_select($job_sql);
				foreach ($job_sql_result as $row) {
					$job_array[$row[csf('id')]]['job']		= $row[csf('job_no')];
					$job_array[$row[csf('id')]]['buyer']	= $row[csf('buyer_name')];
					$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
					$job_array[$row[csf('id')]]['po']		= $row[csf('po_number')];
				}

				$booking_array = array();
				$booking_sql = "select a.booking_no,a.buyer_id,b.style_ref_no from wo_booking_mst a, wo_booking_dtls c,wo_po_details_master b where c.job_no=b.job_no and a.booking_no=c.booking_no and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no,a.buyer_id,b.style_ref_no";
				$booking_sql_result = sql_select($booking_sql);
				foreach ($booking_sql_result as $row) {
					$booking_array[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
					$booking_array[$row[csf('booking_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
				}
				$sales_array = array();
				//$sales_barcode_no
				if ($sales_barcode_no == "") $sales_barcode_no = 0;
				$sales_sql = "select a.id, a.job_no, a.sales_booking_no, a.within_group, a.buyer_id, a.customer_buyer, a.style_ref_no, b.barcode_no
				from fabric_sales_order_mst a, pro_roll_details b
				where a.id=b.po_breakdown_id and b.barcode_no in($sales_barcode_no) and b.mst_id=$update_id and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				//echo $sales_sql;
				$sales_sql_result = sql_select($sales_sql);
				foreach ($sales_sql_result as $row) {
					$sales_array[$row[csf('barcode_no')]]['job']			= $row[csf('job_no')];
					$sales_array[$row[csf('barcode_no')]]['booking_no']		= $row[csf('sales_booking_no')];
					$sales_array[$row[csf('barcode_no')]]['within_group']	= $row[csf('within_group')];
					$sales_array[$row[csf('barcode_no')]]['buyer_id']		= $row[csf('buyer_id')];
					$sales_array[$row[csf('barcode_no')]]['style_ref_no']	= $row[csf('style_ref_no')];
					$sales_array[$row[csf('barcode_no')]]['customer_buyer']	= $row[csf('customer_buyer')];

					$sales_booking_arr[] = "'" . $row[csf('sales_booking_no')] . "'";
				}

				if (!empty($sales_booking_arr)) {
					$booking_cond = " and a.booking_no in (" . implode(",", $sales_booking_arr) . ")";

					$booking_details = sql_select("SELECT a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping ref_no from wo_booking_mst a,wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and b.booking_type in(1,4) $booking_cond group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping");

					foreach ($booking_details as $booking_row) {
						$booking_arr[$booking_row[csf("booking_no")]]["int_ref"] = $booking_row[csf("ref_no")];
					}
				}

				// main data arr
				$production_data_arr = array();
				foreach ($production_sql as $pr_row) {
					$system_id 		= $roll_details[$pr_row[csf("barcode_no")]]["system_id"];
					$yarn 			= $roll_details[$pr_row[csf("barcode_no")]]["prod_id"];
					$yarn_count 	= $roll_details[$pr_row[csf("barcode_no")]]["yarn_count"];
					$yarn_lot 		= $roll_details[$pr_row[csf("barcode_no")]]["yarn_lot"];
					$stitch_length 	= $roll_details[$pr_row[csf("barcode_no")]]["stitch_length"];
					$room 			= $roll_details[$pr_row[csf("barcode_no")]]["room"];
					$rack 			= $roll_details[$pr_row[csf("barcode_no")]]["rack"];
					$self 			= $roll_details[$pr_row[csf("barcode_no")]]["self"];
					$qnty 			= $roll_details[$pr_row[csf("barcode_no")]]["quantity"];
					//$booking_ref 	= explode("-",$pr_row[csf("booking_no")]);

					if ($pr_row[csf("is_sales")] == 1)  // is sales
					{
						$within_group = $sales_array[$pr_row[csf("barcode_no")]]['within_group'];
						$buyer = ($within_group == 1) ? $buyer_array[$booking_array[$sales_array[$pr_row[csf("barcode_no")]]['booking_no']]['buyer_id']] : $buyer_array[$sales_array[$pr_row[csf("barcode_no")]]['buyer_id']];

						//$customer_buyer = ($within_group == 1) ? $buyer_array[$booking_array[$sales_array[$pr_row[csf("barcode_no")]]['booking_no']]['buyer_id']] : $buyer_array[$sales_array[$pr_row[csf("barcode_no")]]['customer_buyer']];

						//$style = ($within_group == 1) ? $booking_array[$sales_array[$pr_row[csf("barcode_no")]]['booking_no']]['style_ref_no'] : $sales_array[$pr_row[csf("barcode_no")]]['style_ref_no'];

						$int_ref_no = ($within_group == 1) ? $booking_arr[$sales_array[$pr_row[csf("barcode_no")]]['booking_no']]["int_ref"] : "";
					} else {
						$buyer = $buyer_array[$job_array[$pr_row[csf("po_breakdown_id")]]['buyer']];
						$int_ref_no = "";
					}

					$ref_str = $buyer . "**" . $int_ref_no . "**" . $pr_row[csf("booking_no")] . "**" . $yarn_count . "**" . $yarn_lot . "**" . $pr_row[csf("color_id")] . "**" . $stitch_length . "**" . $pr_row[csf("machine_dia")] . "**" . $pr_row[csf("machine_gg")] . "**" . $pr_row[csf("color_range_id")] . "**" . $pr_row[csf("febric_description_id")] . "**" . $pr_row[csf("brand_id")] . "**" . $pr_row[csf("knitting_source")];

					$production_data_arr[$pr_row[csf("knitting_company")]][$pr_row[csf("width")]][$pr_row[csf("gsm")]][$ref_str]['qnty'] += $qnty;
					$production_data_arr[$pr_row[csf("knitting_company")]][$pr_row[csf("width")]][$pr_row[csf("gsm")]][$ref_str]['rack'] .= $rack . ',';
					$production_data_arr[$pr_row[csf("knitting_company")]][$pr_row[csf("width")]][$pr_row[csf("gsm")]][$ref_str]['self'] .= $self . ',';
					$production_data_arr[$pr_row[csf("knitting_company")]][$pr_row[csf("width")]][$pr_row[csf("gsm")]][$ref_str]['roll_count']++;

					$mother_barcode_no = $mother_barcode_arr[$pr_row[csf("barcode_no")]];
					if ($mother_barcode_no != "") {
						$qnty 	= $roll_details[$mother_barcode_no]["quantity"];
						$production_data_arr[$pr_row[csf("knitting_company")]][$pr_row[csf("width")]][$pr_row[csf("gsm")]][$ref_str]['qnty'] += $qnty;
						$production_data_arr[$pr_row[csf("knitting_company")]][$pr_row[csf("width")]][$pr_row[csf("gsm")]][$ref_str]['rack'] .= $rack . ',';
						$production_data_arr[$pr_row[csf("knitting_company")]][$pr_row[csf("width")]][$pr_row[csf("gsm")]][$ref_str]['self'] .= $self . ',';
						$production_data_arr[$pr_row[csf("knitting_company")]][$pr_row[csf("width")]][$pr_row[csf("gsm")]][$ref_str]['roll_count']++;
					}
				}
				// echo "<pre>";print_r($production_data_arr);die;

				$grand_tot_roll = $grand_tot_qty = 0;
				foreach ($production_data_arr as $knitting_company => $knit_row) {
					foreach ($knit_row as $fab_dia => $fab_dia_row) {
						foreach ($fab_dia_row as $gsm => $gsm_row) {
							$tot_roll = $tot_qty = 0;
							foreach ($gsm_row as $str_ref => $row) {
								$dataStr = explode("**", $str_ref);
								$buyer = $dataStr[0];
								$int_ref_no = $dataStr[1];
								$book_prog_challan = $dataStr[2];
								$yarn_count = $dataStr[3];
								$yarn_lot = $dataStr[4];
								$color_id = $dataStr[5];
								$stitch_length = $dataStr[6];
								$machine_dia = $dataStr[7];
								$machine_gg = $dataStr[8];
								$color_range_id = $dataStr[9];
								$febric_description_id = $dataStr[10];
								$brand_id = $dataStr[11];
								$knittingSource = $dataStr[12];

								$color_names = "";
								$color_array = explode(",", $color_id);
								foreach ($color_array as $color_row) {
									$color_names .= $color_arr[$color_row]  . ",";
								}
								$count = '';
								$yarn_counts = explode(",", $yarn_count);
								foreach ($yarn_counts as $count_id) {
									if ($count == '') $count = $yarn_count_details[$count_id];
									else $count .= "," . $yarn_count_details[$count_id];
								}
								$knitting_com = ($knittingSource == 1) ? $company_arr[$knitting_company] : $supplier_arr[$knitting_company];

								$rack_names = "";
								$rack_array = array_unique(explode(",", chop($row['rack'], ",")));
								foreach ($rack_array as $rack_id) {
									$rack_names .= $floorRoomRackShelf_array[$rack_id]  . ",";
								}

								$self_names = "";
								$self_array = array_unique(explode(",", chop($row['self'], ",")));
								foreach ($self_array as $self_id) {
									$self_names .= $floorRoomRackShelf_array[$self_id]  . ",";
								}
				?>
								<tr style="font-size:11px">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="100" align="center">
										<b>IR:</b> <? echo $int_ref_no; ?><br />
										<b>B:</b> <? echo $buyer; ?><br />
										<b>P:</b> <? echo $book_prog_challan; ?>
									</td>
									<td width="140" style="word-break:break-all;"><? echo $knitting_com; ?></td>
									<td width="80" style="word-break:break-all; text-align: center;"><? echo $count; ?></td>
									<td width="80"><? echo $brand_arr[$brand_id]; ?></td>
									<td width="80" align="center"><? echo $yarn_lot; ?></td>
									<td width="80" align="center"><? echo rtrim($color_names, ","); ?></td>
									<td width="60" style="word-break:break-all;" align="center"><? echo $color_range[$color_range_id]; ?></td>
									<td width="120" style="word-break:break-all;" align="center"><? echo $composition_arr[$febric_description_id]; ?></td>
									<td width="60" style="word-break:break-all;" align="center"><? echo $stitch_length; ?></td>
									<td width="80" style="word-break:break-all;"><? echo $gsm; ?></td>
									<td width="80" style="word-break:break-all;" align="center"><? echo $fab_dia; ?></td>
									<td width="80" align="center"><? echo $machine_dia; ?></td>
									<td width="60" align="center"><? echo $machine_gg; ?></td>
									<td width="40" style="word-break:break-all;" align="center"><? echo $row['roll_count']; ?></td>
									<td width="50" align="center"><? echo number_format($row['qnty'], 2); ?></td>
									<td width="80"><? echo chop($rack_names, ",") . '<br>';
													echo "<hr>";
													echo chop($self_names, ","); ?></td>
								</tr>
							<?php
								$tot_roll += $row['roll_count']; // roll total
								$tot_qty += $row['qnty']; // qc pass qnty total
								$i++;
							}
							?>
							<tr style="font-size:12px; background-image:linear-gradient(#ccc, #fefefe);">
								<td align="right" colspan="14"><strong>Total:</strong></td>
								<td align="center"><strong><? echo number_format($tot_roll); ?></strong></td>
								<td align="right"><strong><? echo number_format($tot_qty, 2, '.', ''); ?></strong></td>
								<td align="right"><strong></strong></td>
							</tr>
				<?php
							$grand_tot_roll += $tot_roll; // roll grand total
							$grand_tot_qty += $tot_qty; // qc pass qnty grand total
						}
					}
				}
				?>
				<tr style="font-size:12px;">
					<td align="right" colspan="14"><strong>Grand Total:</strong></td>
					<td align="center"><strong><? echo number_format($grand_tot_roll); ?></strong></td>
					<td align="right"><strong><? echo number_format($grand_tot_qty, 2, '.', ''); ?></strong></td>
					<td align="right"><strong></strong></td>
				</tr>
			</table>
		</div>
		<div>
			<? echo signature_table(124, $company, "1700px", "", "20px"); ?>
			<script type="text/javascript" src="../../../js/jquery.js"></script>
			<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
			<script>
				function generateBarcode(valuess) {
					var value = valuess; //$("#barcodeValue").val();
					//alert(value)
					var btype = 'code39'; //$("input[name=btype]:checked").val();
					var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

					var settings = {
						output: renderer,
						bgColor: '#FFFFFF',
						color: '#000000',
						barWidth: 1,
						barHeight: 30,
						moduleSize: 5,
						posX: 10,
						posY: 20,
						addQuietZone: 1
					};
					//$("#barcode_img_id").html('11');
					value = {
						code: value,
						rect: false
					};

					$("#barcode_img_id").show().barcode(value, btype, settings);
				}
				generateBarcode('<? echo $txt_issue_no; ?>');
			</script>
		</div>
		<p style="page-break-after:always;"></p>
	<?
	}
	exit();
}

if ($action == "sales_roll_issue_challan_print_mg") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

	$company_array = array();

	$company_data = sql_select("select id, company_name, company_short_name from lib_company where id=$company and status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$dataArray = sql_select("select issue_purpose, issue_date, location_id, knit_dye_source, knit_dye_company, batch_no,remarks from inv_issue_master where id=$update_id");


	$all_order_id = $all_program_no = $all_barcode_no = "";
	$roll_details = array();
	$sql = "SELECT a.mst_id, a.basis, a.program_no, a.prod_id, a.issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, b.roll_no, b.barcode_no, b.po_breakdown_id, b.qnty as quantity, b.booking_no, b.booking_without_order from inv_issue_master c,inv_grey_fabric_issue_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=c.id and a.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$result = sql_select($sql);
	foreach ($result as $row) {
		$all_order_id	.= $row[csf("po_breakdown_id")] . ",";
		$all_barcode_no	.= $row[csf("barcode_no")] . ",";
		$roll_details[$row[csf("barcode_no")]]["system_id"] = $row[csf("mst_id")];
		$roll_details[$row[csf("barcode_no")]]["basis"] 	= $row[csf("basis")];
		$roll_details[$row[csf("barcode_no")]]["brand_id"]  = $row[csf("brand_id")];
		$roll_details[$row[csf("barcode_no")]]["yarn_lot"]  = $row[csf("yarn_lot")];
		$roll_details[$row[csf("barcode_no")]]["yarn_count"] = $row[csf("yarn_count")];
		$roll_details[$row[csf("barcode_no")]]["prod_id"]   = $row[csf("prod_id")];
		$roll_details[$row[csf("barcode_no")]]["stitch_length"]  = $row[csf("stitch_length")];
	}
	$all_order_id	= implode(",", array_unique(explode(",", chop($all_order_id, ","))));
	$all_barcode_no	= implode(",", array_unique(explode(",", chop($all_barcode_no, ","))));
	$sales_barcode_no = "";
	if ($all_barcode_no != "") {
		$production_sql = sql_select("SELECT a.booking_id, a.booking_no, a.receive_basis,a.knitting_company,a.knitting_source,a.recv_number, c.barcode_no,c.qnty,c.is_sales,c.po_breakdown_id,b.machine_dia,b.machine_gg,b.machine_no_id,b.prod_id, b.color_id,c.roll_no,b.width,b.gsm,b.color_range_id,b.febric_description_id,b.brand_id,a.location_id from  inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and b.status_active=1 and c.status_active=1 and c.barcode_no in($all_barcode_no)");

		$production_data_arr = array();
		$duplicateChk = array();
		$sales_ids = $po_ids = "";
		$all_feb_des_ids = "";
		foreach ($production_sql as $pr_row) {
			$system_id 		= $roll_details[$pr_row[csf("barcode_no")]]["system_id"];
			$yarn 			= $roll_details[$pr_row[csf("barcode_no")]]["prod_id"];
			$yarn_count 	= $roll_details[$pr_row[csf("barcode_no")]]["yarn_count"];
			$yarn_lot 		= $roll_details[$pr_row[csf("barcode_no")]]["yarn_lot"];
			$stitch_length 	= $roll_details[$pr_row[csf("barcode_no")]]["stitch_length"];
			$booking_ref 	= explode("-", $pr_row[csf("booking_no")]);

			$production_data_arr[$pr_row[csf("machine_no_id")]][$pr_row[csf("knitting_company")]][$yarn_lot][$pr_row[csf("width")]][$pr_row[csf("barcode_no")]] = $pr_row[csf("po_breakdown_id")] . "**" . $pr_row[csf("booking_no")] . "**" . $pr_row[csf("receive_basis")] . "**" . $yarn_count . "**" . $yarn_lot . "**" . $pr_row[csf("color_id")] . "**" . $stitch_length . "**" . $pr_row[csf("gsm")] . "**" . $pr_row[csf("width")] . "**" . $pr_row[csf("machine_no_id")] . "**" . $pr_row[csf("machine_dia")] . "**" . $pr_row[csf("machine_gg")] . "**" . $pr_row[csf("barcode_no")] . "**" . $pr_row[csf("qnty")] . "**" . $pr_row[csf("roll_no")] . "**" . $pr_row[csf("is_sales")] . "**" . $system_id . "**" . $pr_row[csf("color_range_id")] . "**" . $pr_row[csf("febric_description_id")] . "**" . $pr_row[csf("knitting_source")] . "**" . $pr_row[csf("brand_id")] . "**" . $pr_row[csf("location_id")];

			if ($pr_row[csf("receive_basis")] == 2) {
				$all_program_no .= $pr_row[csf("booking_id")] . ",";
			}
			if ($pr_row[csf("receive_basis")] == 1) {
				$all_booking_no .= $pr_row[csf("booking_no")] . ",";
			}

			if ($pr_row[csf("is_sales")] == 1) {
				$sales_ids .= $pr_row[csf("po_breakdown_id")] . ",";
				$sales_barcode_no .= $pr_row[csf("barcode_no")] . ",";
			} else {
				$po_ids .= $pr_row[csf("po_breakdown_id")] . ",";
			}


			if ($duplicateChk[$pr_row[csf('febric_description_id')]] == "") {
				$duplicateChk[$pr_row[csf('febric_description_id')]] = $pr_row[csf('febric_description_id')];
				$all_feb_des_ids .= $pr_row[csf("febric_description_id")] . ",";
			}
		}
	}
	$po_ids = rtrim($po_ids, ",");
	$sales_ids = rtrim($sales_ids, ",");
	$sales_barcode_no = chop($sales_barcode_no, ",");
	$all_feb_des_id = chop($all_feb_des_ids, ",");

	if ($po_ids != "") {
		$job_array = array();
		$job_sql = "SELECT a.style_ref_no,a.job_no,a.buyer_name,b.id,b.po_number,b.file_no,b.grouping from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_ids)";
		//echo $job_sql;die;
		$job_sql_result = sql_select($job_sql);
		foreach ($job_sql_result as $row) {
			$job_array[$row[csf('id')]]['job']		= $row[csf('job_no')];
			$job_array[$row[csf('id')]]['buyer']	= $row[csf('buyer_name')];
			$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			$job_array[$row[csf('id')]]['po']		= $row[csf('po_number')];
		}
	}

	if ($sales_barcode_no != "") {

		//$sales_barcode_no

		$sales_sql = "SELECT a.id, a.job_no, a.sales_booking_no, a.within_group, a.buyer_id, a.style_ref_no, b.barcode_no
		from fabric_sales_order_mst a, pro_roll_details b
		where a.id=b.po_breakdown_id and b.barcode_no in($sales_barcode_no) and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$sales_sql_result = sql_select($sales_sql);
		$sales_array = array();
		$bookingNoChk = array();
		$bookingNoArr = array();
		foreach ($sales_sql_result as $row) {
			$sales_array[$row[csf('barcode_no')]]['job']			= $row[csf('job_no')];
			$sales_array[$row[csf('barcode_no')]]['booking_no']		= $row[csf('sales_booking_no')];
			$sales_array[$row[csf('barcode_no')]]['within_group']	= $row[csf('within_group')];
			$sales_array[$row[csf('barcode_no')]]['buyer_id']		= $row[csf('buyer_id')];
			$sales_array[$row[csf('barcode_no')]]['style_ref_no']	= $row[csf('style_ref_no')];

			if ($bookingNoChk[$row[csf('sales_booking_no')]] == "") {
				$bookingNoChk[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
				array_push($bookingNoArr, $row[csf('sales_booking_no')]);
			}
		}
		if (!empty($bookingNoArr)) {
			$booking_array = array();
			$booking_sql = "SELECT a.booking_no,a.buyer_id,b.style_ref_no from wo_booking_mst a, wo_po_details_master b where a.job_no=b.job_no and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  " . where_con_using_array($bookingNoArr, 1, 'a.booking_no') . " ";
			//echo $booking_sql;
			$booking_sql_result = sql_select($booking_sql);
			foreach ($booking_sql_result as $row) {
				$booking_array[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
				$booking_array[$row[csf('booking_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
			}
		}
	}


	$all_program_no = implode(",", array_unique(explode(",", chop($all_program_no, ","))));
	if ($all_program_no != "") {
		$program_sql = sql_select("SELECT a.booking_id,b.spandex_stitch_length,c.booking_no,c.within_group, b.id as program_no, b.batch_no from inv_receive_master a,ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c where a.booking_id=b.id and b.mst_id=c.id and a.entry_form=2 and a.receive_basis=2 and a.booking_id in($all_program_no)");

		$prog_book_arr = array();
		$program_batch_arr = array();
		foreach ($program_sql as $row) {
			$booking_ref = $row[csf("booking_no")];
			$prog_book_arr[$row[csf("booking_id")]] = $row[csf("booking_no")];
			$spandexsl_arr[$row[csf("booking_id")]]['spandexsl'] = $row[csf('spandex_stitch_length')];
			$program_batch_arr[$row[csf("program_no")]] =  $row[csf("batch_no")];
		}
	}

	if ($all_feb_des_id != "") {
		$composition_arr = array();
		$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in($all_feb_des_id)";
		//echo $sql_deter;
		$data_array = sql_select($sql_deter);
		foreach ($data_array as $row) {
			if (array_key_exists($row[csf('id')], $composition_arr)) {
				$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			} else {
				$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			}
		}
	}


	rsort($production_data_arr);
	$buyer = "";
	$knitting_com = "";
	$receive_basis = "";
	$location_id = "";
	$receive_basis_arr = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan", 4 => "Sales Order");
	foreach ($production_data_arr as $machine_id => $machine_row) {
		foreach ($machine_row as $knitting_company => $knit_row) {
			foreach ($knit_row as $lot => $lot_row) {
				foreach ($lot_row as $fab_dia => $fab_dia_row) {

					foreach ($fab_dia_row as $barcode => $barcode_info) {
						$barcode_row = explode("**", $barcode_info);

						if ($barcode_row[15] == 1) { // is sales
							$within_group = $sales_array[$barcode]['within_group'];
							$buyer = ($within_group == 1) ? $buyer_array[$booking_array[$sales_array[$barcode]['booking_no']]['buyer_id']] : $buyer_array[$sales_array[$barcode]['buyer_id']];
						} else {
							$buyer = $buyer_array[$job_array[$barcode_row[0]]['buyer']];
						}

						$knitting_com = ($barcode_row[19] == 1) ? $company_arr[$knitting_company] : $supplier_arr[$knitting_company];

						$receive_basis = $receive_basis_arr[$barcode_row[2]];
						$location_id = $barcode_row[21];
					}
				}
			}
		}
	}


	?>
	<div>
		<table width="1030" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[3]; ?>Grey Fabric Roll Issue Challan/Gate Pass</u></strong></td>
			</tr>
			<tr>
				<td width="120" style="font-size:18px; font-weight:bold;"><strong>Challan No :</strong></td>
				<td width="175" style="font-size:18px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
				<td width="130" style="font-size:16px; font-weight:bold;"><strong>Dyeing Company:</strong> </td>
				<td style="font-size:18px; font-weight:bold;">
					<?
					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]];
					?>
				</td>
				<td style="font-size: 16px;"><strong>Issue Purpose:</strong></td>
				<td style="font-size: 16px;"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			</tr>
			<tr>
				<td width="125" style="font-size: 16px;"><strong>Delivery Date:</strong></td>
				<td width="175" style="font-size: 16px;"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
				<td style="font-size: 16px;"><strong>Location:</strong></td>
				<td style="font-size: 16px;"><? echo $location_arr[$location_id]; ?></td>
				<td style="font-size: 16px;"><strong>Batch Number:</strong></td>
				<td style="font-size: 16px;"><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
			</tr>
			<tr>
				<td width="125" style="font-size: 16px;"><strong>Buyer:</strong></td>
				<td width="175" style="font-size: 16px;"><? echo $buyer; ?></td>
				<td style="font-size: 16px;"><strong>Knit. Com:</strong></td>
				<td style="font-size: 16px;"><? echo $knitting_com; ?></td>
				<td style="font-size: 16px;"><strong>Prod Basis:</strong></td>
				<td style="font-size: 16px;"><? echo $receive_basis; ?></td>
			</tr>
			<tr>
				<td style="font-size: 16px;"><strong>Dyeing Source:</strong></td>
				<td style="font-size: 16px;"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				<td><strong>Barcode:</strong></td>
				<td colspan="3" id="barcode_img_id"></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size: 16px;"><strong>Remarks:</strong></td>
				<td style="font-size: 16px;"><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1300" class="rpt_table" style="font-family: tahoma; font-size: 16px;">
			<thead bgcolor="#dddddd" align="center" style="font-size:12px">
				<th width="30">SL</th>
				<th width="100">Style</th>
				<th width="110">Sales Order No</th>
				<th width="110">Prog./ Book. No</th>
				<th width="60">Yarn Count</th>
				<th width="80">Yarn Brand</th>
				<th width="60">Lot No</th>
				<th width="80">Fab Color</th>
				<th width="200">Fabrication</th>
				<th width="60">Stich Length</th>
				<th width="60">Fin GSM</th>
				<th width="40">Fab. Dia</th>
				<th width="40">MC. Dia</th>
				<th width="40">MC. Gauge</th>
				<th width="80">Barcode No</th>
				<th width="40">Roll No</th>
				<th>QC Pass Qty</th>
			</thead>
			<?
			$i = 1;
			$tot_qty = 0;

			$grand_tot_roll = $grand_tot_qty = 0;

			foreach ($production_data_arr as $machine_id => $machine_row) {
				foreach ($machine_row as $knitting_company => $knit_row) {
					foreach ($knit_row as $lot => $lot_row) {
						foreach ($lot_row as $fab_dia => $fab_dia_row) {
							$tot_roll = $tot_qty = 0;
							foreach ($fab_dia_row as $barcode => $barcode_info) {
								$barcode_row = explode("**", $barcode_info);
								$job_no = ($barcode_row[15] == 1) ? $sales_array[$barcode]['job'] : $job_array[$barcode_row[0]]['job'];

								if ($barcode_row[15] == 1) { // is sales
									$within_group = $sales_array[$barcode]['within_group'];
									$style = ($within_group == 1) ? $booking_array[$sales_array[$barcode]['booking_no']]['style_ref_no'] : $sales_array[$barcode]['style_ref_no'];
								} else {
									$style = $job_array[$barcode_row[0]]['style_ref'];
								}

								$book_prog_challan = "";
								if ($barcode_row[2] == 1) {
									$book_prog_challan = "<b>B.N</b>. " . $barcode_row[1] . "<br>";
								} else {
									//$book_prog_challan="<b>B.N</b>. ".$prog_book_arr[$barcode_row[1]]."<br>";
									$book_prog_challan = "<b>B.N</b>. " . $sales_array[$barcode]['booking_no'] . "<br>";
									$book_prog_challan .= "<b>P.N</b>. " . $barcode_row[1] . "<br>";
									$program_batch = $program_batch_arr[$barcode_row[1]];
								}
								$color_names = "";
								$color_array = explode(",", $barcode_row[5]);
								foreach ($color_array as $color_row) {
									$color_names .= $color_arr[$color_row]  . ",";
								}
								$count = '';
								$yarn_count = explode(",", $barcode_row[3]);
								foreach ($yarn_count as $count_id) {
									if ($count == '') $count = $yarn_count_details[$count_id];
									else $count .= "," . $yarn_count_details[$count_id];
								}

								$spandexsl = $spandexsl_arr[$barcode_row[1]]['spandexsl'];

								//echo $barcode_row[1];
			?>
								<tr style="font-size:11px">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="100" style="word-break:break-all;"><? echo $style; ?></td>
									<td width="110" style="word-break:break-all; text-align: center;"><? echo $job_no; ?></td>
									<td width="110"><? echo $book_prog_challan; ?></td>
									<td width="80" style="word-break:break-all;" align="center"><? echo $count; //$barcode_row[3]; 
																								?></td>
									<td width="80" style="word-break:break-all;" align="center"><? echo $brand_arr[$barcode_row[20]]; ?></td>
									<td width="60" style="word-break:break-all;" align="center"><? echo $barcode_row[4]; ?></td>
									<td width="80" style="word-break:break-all;"><? echo rtrim($color_names, ","); ?></td>
									<td width="200" align="center"><? echo $composition_arr[$barcode_row[18]]; ?></td>
									<td width="60" align="center"><? echo $barcode_row[6]; ?></td>
									<td width="60" style="word-break:break-all;" align="center"><? echo $barcode_row[7]; ?></td>
									<td width="40" style="word-break:break-all;" align="center"><? echo $barcode_row[8]; ?></td>
									<td width="40" style="word-break:break-all;" align="center"><? echo $barcode_row[10]; ?></td>
									<td width="40" align="center" style="word-break:break-all;"><? echo $barcode_row[11]; ?></td>
									<td width="70" align="center"><? echo $barcode_row[12]; ?></td>
									<td width="40" style="word-break:break-all;" align="center"><? echo $barcode_row[14]; ?></td>
									<td align="right"><? echo number_format($barcode_row[13], 2); ?></td>
								</tr>
							<?php
								$tot_roll = $tot_roll + 1; // roll total
								$tot_qty += $barcode_row[13]; // qc pass qnty total
								$i++;
							}
							?>
							<tr style="font-size:12px; background-image:linear-gradient(#ccc, #fefefe);">
								<td align="right" colspan="15"><strong>Total:</strong></td>
								<td align="center"><strong><? echo number_format($tot_roll); ?></strong></td>
								<td align="right"><strong><? echo number_format($tot_qty, 2, '.', ''); ?></strong></td>
							</tr>
			<?php
							$grand_tot_roll += $tot_roll; // roll grand total
							$grand_tot_qty += $tot_qty; // qc pass qnty grand total
						}
					}
				}
			}
			?>
			<tr style="font-size:12px;">
				<td align="right" colspan="15"><strong>Grand Total:</strong></td>
				<td align="center"><strong><? echo number_format($grand_tot_roll); ?></strong></td>
				<td align="right"><strong><? echo number_format($grand_tot_qty, 2, '.', ''); ?></strong></td>
			</tr>
		</table>
	</div>
	<? echo signature_table(124, $company, "1300px", "", "40px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}

//for norban
if ($action == "roll_issue_no_of_copy_print______old") // Print 1, created by Tipu
{
	extract($_REQUEST);
	echo load_html_head_contents("Roll Wise Grey Issue", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);

	$company   		= $data[0];
	$system_no 		= $data[1];
	$report_title 	= $data[2];
	$mst_id     	= $data[3];
	$knit_source    = $data[4];
	$no_copy 		= $data[5];
	$dyeing_company = $data[6];

	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	//$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$body_part_type = return_library_array("select id, body_part_type from lib_body_part where status_active=1", 'id', 'body_part_type');
	$composition_arr = array();
	$yarn_composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " :: " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . " :: " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}

		if (array_key_exists($row[csf('id')], $yarn_composition_arr)) {
			$yarn_composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]];
		} else {
			$yarn_composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]];
		}
	}



	$store_location_id = return_field_value("location_id", "lib_store_location", "id=$store_id and is_deleted=0", "location_id");
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$country_name_arr = return_library_array("select id, country_name from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name");


	//for buyer
	$sqlBuyer = sql_select("select id as ID, buyer_name as BUYER_NAME, short_name as SHORT_NAME from lib_buyer");
	foreach ($sqlBuyer as $row) {
		$buyer_arr[$row['ID']] = $row['SHORT_NAME'];
		$buyer_dtls_arr[$row['ID']] = $row['BUYER_NAME'];
	}
	unset($sqlBuyer);

	$company_info = sql_select("select ID, COMPANY_NAME, PLOT_NO, ROAD_NO, CITY, CONTACT_NO, COUNTRY_ID from lib_company where status_active=1 and is_deleted=0 order by company_name");
	foreach ($company_info as $row) {
		$company_library[$row['ID']] = $row['COMPANY_NAME'];
		$company_address_arr[$row['ID']] = 'Plot No:' . $row['PLOT_NO'] . ', Road No:' . $row['ROAD_NO'] . ', City / Town:' . $row['CITY'] . ', Country:' . $country_name_arr[$row['COUNTRY_ID']] . ', Contact No:' . $row['CONTACT_NO'];
	}
	unset($company_info);

	//for supplier
	$sqlSupplier = sql_select("select id as ID, supplier_name as SUPPLIER_NAME, short_name as SHORT_NAME, ADDRESS_1 from lib_supplier where id=$dyeing_company");
	foreach ($sqlSupplier as $row) {
		$supplier_arr[$row['ID']] = $row['SHORT_NAME'];
		$supplier_dtls_arr[$row['ID']] = $row['SUPPLIER_NAME'];
		$supplier_address_arr[$row['ID']] = $row['ADDRESS_1'];
	}
	unset($sqlSupplier);

	//for gate pass
	$sql_get_pass = "SELECT a.ID, a.SYS_NUMBER, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.DRIVER_NAME,a.DRIVER_LICENSE_NO,a.SECURITY_LOCK_NO, b.QUANTITY, b.NO_OF_BAGS FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b WHERE a.id = b.mst_id AND a.company_id = " . $company . " AND a.basis = 3 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.challan_no LIKE '" . $system_no . "%'";
	// echo $sql_get_pass;
	$sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach ($sql_get_pass_rslt as $row) {
		$exp = explode(',', $row['CHALLAN_NO']);
		// echo "<pre>"; print_r($exp);
		foreach ($exp as $key => $val) {
			if ($val == $system_no) {
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];

				$row['OUT_DATE'] = ($row['OUT_DATE'] != '' ? date('d-m-Y', strtotime($row['OUT_DATE'])) : '');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE'] != '' ? date('d-m-Y', strtotime($row['EST_RETURN_DATE'])) : '');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE'] != '' ? date('d-m-Y', strtotime($row['EST_RETURN_DATE'])) : '');

				if ($row['WITHIN_GROUP'] == 1) {
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					$row['LOCATION_NAME'] = $location_arr[$row['LOCATION_ID']];
				}

				//for gate pass info
				$gatePassDataArr[$val]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$val]['from_company'] = $company_library[$row['COMPANY_ID']];
				$gatePassDataArr[$val]['from_location'] = $location_arr[$row['COM_LOCATION_ID']];
				$gatePassDataArr[$val]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$val]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$val]['est_return_date'] = $row['EST_RETURN_DATE'];

				$gatePassDataArr[$val]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$val]['to_location'] = $row['LOCATION_NAME'];
				$gatePassDataArr[$val]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$val]['delivery_bag'] += $row['NO_OF_BAGS'];

				$gatePassDataArr[$val]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				$gatePassDataArr[$val]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$val]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$val]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$val]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$val]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$val]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$val]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$val]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
				$gatePassDataArr[$val]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
			}
		}
	}
	// echo "<pre>";print_r($gatePassDataArr);

	//for gate out
	if ($gate_pass_id != '') {
		$sql_gate_out = "SELECT OUT_DATE, OUT_TIME FROM INV_GATE_OUT_SCAN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND INV_GATE_PASS_MST_ID='" . $gate_pass_id . "'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if (!empty($sql_gate_out_rslt)) {
			foreach ($sql_gate_out_rslt as $row) {
				$is_gate_out = 1;
				$gatePassDataArr[$system_no]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$system_no]['out_time'] = $row['OUT_TIME'];
			}
		}
	}

	$dataArray = sql_select("SELECT issue_number, issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no, remarks, attention from inv_issue_master where id=$mst_id");
	$reqBookingNoArr = array();
	foreach ($dataArray as $row) {
		$issue_number = $row[csf('issue_number')];
		$issue_date = $row[csf('issue_date')];
		// $knit_dye_company = $row[csf('knit_dye_company')];
		$knit_dye_source = $row[csf('knit_dye_source')];
		$issue_purpose = $yarn_issue_purpose[$row[csf('issue_purpose')]];
		$attention = $row[csf('attention')];
		$remarks = $row[csf('remarks')];

		//for issue to
		$knit_dye_company = '';
		if ($row[csf('knit_dye_source')] == 1)
			$knit_dye_company = $company_library[$row[csf('knit_dye_company')]];
		else
			$knit_dye_company = $supplier_dtls_arr[$row[csf('knit_dye_company')]];

		$knit_dye_company_address = '';
		if ($row[csf('knit_dye_source')] == 1)
			$knit_dye_company_address = $company_address_arr[$row[csf('knit_dye_company')]];
		else
			$knit_dye_company_address = $supplier_address_arr[$row[csf('knit_dye_company')]];
	}

	// ====
	$issue_res = sql_select("SELECT a.barcode_no, sum(a.qnty) as issue_qty from pro_roll_details a where a.entry_form=61 and a.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 group by a.barcode_no");
	foreach ($issue_res as $val) {
		$barcode_nums .= $val[csf("barcode_no")] . ",";
		$qntyFromRoll[$val[csf("barcode_no")]] = $val[csf("issue_qty")];
	}
	$barcode_nums = chop($barcode_nums, ",");

	$sql = "SELECT a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as issue_qty,count(c.barcode_no) as num_of_roll, sum(c.reject_qnty) as reject_qnty, sum(c.qc_pass_qnty_pcs) as issue_qty_pcs, b.body_part_id, b.prod_id, d.detarmination_id
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d
	where a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums)
	group by  a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no, c.booking_without_order, c.is_sales, b.body_part_id, b.prod_id, d.detarmination_id
	order by a.booking_no";
	// echo $sql;die;
	$sql_result = sql_select($sql);
	$feedar_prog_id = "";
	$po_id_array = $sales_id_array = $booking_program_arr = array();
	foreach ($sql_result as $row) {
		if ($row[csf("is_sales")] == 1) {
			$sales_id_array[] = $row[csf("po_breakdown_id")];
		} else {
			$po_id_array[] = $row[csf("po_breakdown_id")];
		}

		if ($row[csf('receive_basis')] == 2) {
			$booking_program_arr[] = $row[csf("booking_no")];
			$productionYarnCount[$row[csf("booking_no")]] = $row[csf("yarn_count")];
		} else {
			$booking_no = explode("-", $row[csf('booking_no')]);
			$booking_program_arr[] = (int)$booking_no[3];
		}

		$feedar_prog_id .= $row[csf("bwo")] . ",";
	}
	$feedar_prog_ids = chop($feedar_prog_id, ",");
	//print_r($booking_program_arr);
	$planOrder = sql_select("SELECT a.id, b.booking_no, b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(" . implode(",", $booking_program_arr) . ")");
	$plan_arr = array();
	foreach ($planOrder as $plan_row) {
		$plan_arr[$plan_row[csf("id")]]["machine_dia"] = $plan_row[csf("machine_dia")];
		$plan_arr[$plan_row[csf("id")]]["feeder"] = $plan_row[csf("feeder")];
		$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
	}

	$job_array = $job_company_arr = $sales_arr = $sales_booking_arr = $booking_arr = array();
	if (!empty($po_id_array)) {
		$job_sql = "SELECT a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(" . implode(",", $po_id_array) . ")";
		$job_sql_result = sql_select($job_sql);
		foreach ($job_sql_result as $row) {
			$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
			$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
			$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
			$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
			$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			$job_company_arr[$row[csf('job_no_prefix_num')]]['company_name'] = $row[csf('company_name')];
		}
	}

	if (!empty($sales_id_array)) {
		$sales_details = sql_select("SELECT id, job_no, sales_booking_no, within_group, style_ref_no, buyer_id from fabric_sales_order_mst where id in(" . implode(",", $sales_id_array) . ")");
		foreach ($sales_details as $sales_row) {
			$sales_arr[$sales_row[csf('id')]]['sales_booking_no'] = $sales_row[csf('sales_booking_no')];
			$sales_arr[$sales_row[csf('id')]]['within_group'] = $sales_row[csf('within_group')];
			$sales_arr[$sales_row[csf('id')]]['style_ref_no'] = $sales_row[csf('style_ref_no')];
			$sales_arr[$sales_row[csf('id')]]['buyer_id'] = $sales_row[csf('buyer_id')];
			$sales_booking_arr[] = "'" . $sales_row[csf('sales_booking_no')] . "'";
		}
	}
	//$booking_cond = !empty($sales_booking_arr)? " and a.booking_no in (".implode(",",$sales_booking_arr).")" : " and b.po_break_down_id in (".implode(",",$po_id_array).")";
	$booking_details = sql_select("SELECT a.booking_no, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping as ref_no,d.style_ref_no,d.sustainability_standard,d.fab_material from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and b.status_active=1 group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping,d.style_ref_no,d.sustainability_standard,d.fab_material");

	foreach ($booking_details as $booking_row) {
		$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
		$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
		$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
		$booking_arr[$booking_row[csf("po_break_down_id")]]["ref_no"] = $booking_row[csf("ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["booking_ref_no"] = $booking_row[csf("ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["style_ref_no"] = $booking_row[csf("style_ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["internal_ref_no"] = $booking_row[csf("ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["sustainability_standard"] = $booking_row[csf("sustainability_standard")];
		$booking_arr[$booking_row[csf("booking_no")]]["fab_material"] = $booking_row[csf("fab_material")];
	}
	$reqs_array = array();
	$reqs_sql = sql_select("SELECT knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 group by knit_id, requisition_no");
	foreach ($reqs_sql as $row) {
		$reqs_array[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
	}
	$ppl_count_feeder_sql = sql_select("SELECT b.id as prog_no, c.count_id, c.feeding_id , c.seq_no
		from ppl_planning_info_entry_dtls b,ppl_planning_count_feed_dtls c
		where  b.mst_id=c.mst_id and b.id=c.dtls_id and b.id in($feedar_prog_ids)
		and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by c.count_id,c.feeding_id ,c.seq_no,b.id order by c.seq_no");
	$ppl_count_feeder_array = array();
	foreach ($ppl_count_feeder_sql as $row) {
		$feeder_count = strlen($feeding_arr[$row[csf('feeding_id')]]);
		if ($row[csf('feeding_id')] == 0) {
			$dividerSign = "";
		} else {
			$dividerSign = "_";
		}
		$ppl_count_feeder_array[$row[csf('prog_no')]]['count_id'] .= substr($feeding_arr[$row[csf('feeding_id')]], -$feeder_count, 1) . $dividerSign . $yarn_count_details[$row[csf('count_id')]] . ',';
	}
	$refno_data_array = array();
	$jobCountArr = array();
	/*echo "<pre>";
	print_r($sql_result);die;*/
	$ppl_feeding_id = "";
	$ppl_count_id = "";
	foreach ($sql_result as $row) {
		$is_sales = $row[csf('is_sales')];
		if ($is_sales == 1) {
			$within_group = $sales_arr[$row[csf('po_breakdown_id')]]['within_group'];
			if ($within_group == 1) {
				$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
				$job_no = $booking_arr[$booking_no]["job_no"];
				$po_id = $booking_arr[$booking_no]["po_break_down_id"];
				$style_ref_no = $job_array[$po_id]['style_ref_no'];
				$ref_no = $booking_arr[$po_id]["ref_no"];
				$buyer_id = $booking_arr[$booking_no]["buyer_id"];
			} else {
				$job_no = "";
				$style_ref_no = "";
				$ref_no = "";
				$po = "";
				$buyer_id = $sales_arr[$row[csf('po_breakdown_id')]]['buyer_id'];
				$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
			}
		} else {
			$ref_no = $job_array[$row[csf('po_breakdown_id')]]['ref_no'];
			$job_no = $job_array[$row[csf('po_breakdown_id')]]['job'];
			$style_ref_no = $job_array[$row[csf('po_breakdown_id')]]['style_ref_no'];
			$po = $job_array[$row[csf('po_breakdown_id')]]['po'];
			$buyer_id = $row[csf('buyer_id')];
			$booking_no = $booking_arr[$row[csf('po_breakdown_id')]]["booking_no"];
		}
		$jobCountArr[$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]['job_no']] = array(job_no => $job_no);


		$refno_data_array[$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]['job_no']][$row[csf('febric_description_id')]][] = array(
			recv_number_prefix_num => $row[csf('recv_number_prefix_num')],
			buyer_id => $buyer_id,
			ref_no => $ref_no,
			receive_basis => $row[csf('receive_basis')],
			booking_id => $row[csf('booking_id')],
			booking_no => $booking_no,
			knitting_source => $row[csf('knitting_source')],
			knitting_company => $row[csf('knitting_company')],
			location_id => $row[csf('location_id')],
			febric_description_id => $row[csf('febric_description_id')],
			gsm => $row[csf('gsm')],
			width => $row[csf('width')],
			yarn_count => $row[csf('yarn_count')],
			yarn_lot => $row[csf('yarn_lot')],
			color_id => $row[csf('color_id')],
			color_range_id => $row[csf('color_range_id')],
			machine_no_id => $row[csf('machine_no_id')],
			stitch_length => $row[csf('stitch_length')],
			body_part_id => $row[csf('body_part_id')],
			brand_id => $row[csf('brand_id')],
			shift_name => $row[csf('shift_name')],
			machine_gg => $row[csf('machine_gg')],
			machine_dia => $row[csf('machine_dia')],
			num_of_roll => $row[csf('num_of_roll')],
			no_of_roll => $row[csf('no_of_roll')],
			po_breakdown_id => $row[csf('po_breakdown_id')],
			issue_qty => $row[csf('issue_qty')],
			bwo => $row[csf('bwo')],
			booking_without_order => $row[csf('booking_without_order')],
			within_group => $row[csf('within_group')],
			is_sales => $row[csf('is_sales')],
			issue_qty_pcs => $row[csf('issue_qty_pcs')],
			reject_qnty => $row[csf('reject_qnty')],
			detarmination_id => $row[csf('detarmination_id')],

			receive_date => $row[csf('receive_date')],
			job_no => $job_no,
			style_ref_no => $style_ref_no,
			po => $po_id
		); //seq_no=>$row[csf('seq_no')],
	}
	// echo "<pre>"; print_r($refno_data_array);

	$colarCupArr = sql_select("SELECT id, body_part_type, body_part_full_name from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(40,50)");
	foreach ($colarCupArr as $row) {
		$body_part_data_arr[$row[csf('id')]]['body_part_full_name'] = $row[csf('body_part_full_name')];
		$body_part_data_arr[$row[csf('id')]]['body_part_type'] = $row[csf('body_part_type')];
	}

	// For Coller and Cuff data
	$sql_coller_cuff = "SELECT a.receive_basis, a.booking_no, b.body_part_id, c.booking_no as bwo, c.coller_cuff_size, c.qnty, c.qc_pass_qnty_pcs, c.barcode_no
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums) order by b.body_part_id, c.coller_cuff_size";
	// echo $sql_coller_cuff;
	$sql_coller_cuff_result = sql_select($sql_coller_cuff);
	foreach ($sql_coller_cuff_result as $row2) {
		if ($body_part_data_arr[$row2[csf('body_part_id')]]['body_part_type'] > 0 && $row2[csf('qc_pass_qnty_pcs')] > 0) {
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qnty'] += $row2[csf('qnty')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty_pcs'] += $row2[csf('qc_pass_qnty_pcs')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['receive_basis'] = $row2[csf('receive_basis')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['no_of_roll'] += count($row2[csf('barcode_no')]);
		}
	}
	//echo "<pre>";
	//print_r($coller_data_arr);//die;
	//print_r($cuff_data_arr);die;

	//Without order booking
	$bookings_without_order = "";
	foreach ($refno_data_array as $refArr) {
		foreach ($refArr as $refDataArr) {
			foreach ($refDataArr as $row) {
				if ($row['booking_without_order'] == 1 && $row['receive_basis'] == 2) {
					$bookings_without_order .= "'" . $plan_arr[$row['bwo']]["booking_no"] . "',";
				}
				if ($row['booking_without_order'] == 1 && $row['receive_basis'] != 2) {
					$bookings_without_order .= "'" . $row['bwo'] . "',";
				}
			}
		}
	}
	$bookings_without_order = chop($bookings_without_order, ',');
	$non_order_booking_sql = sql_select("SELECT a.booking_no,a.buyer_id,a.grouping, b.style_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_no in($bookings_without_order) group by  a.booking_no,a.buyer_id,a.grouping, b.style_id");
	foreach ($non_order_booking_sql as $row) {
		$style_id = $row[csf("style_id")];
		$nonOrderBookingData_arr[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
		$nonOrderBookingData_arr[$row[csf('booking_no')]]['grouping'] = $row[csf('grouping')];
		$nonOrderBookingData_arr[$row[csf('booking_no')]]['sustainability_std_id'] = return_field_value("sustainability_std_id", "sample_development_mst", "id=$style_id");
		$nonOrderBookingData_arr[$row[csf('booking_no')]]['fabric_material_id'] = return_field_value("fabric_material_id", "sample_development_mst", "id=$style_id");
		//$nonOrderBookingData_arr[$row[csf('booking_no')]]['style_id']=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
	}
	//var_dump($nonOrderBookingData_arr);die;
	$nonOrderBookingStyle = return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");

	$com_dtls = fnc_company_location_address($company, $store_location_id, 2);
?>
	<style type="text/css">
		table tr td {
			font-size: 16px;
		}

		.rpt_table thead th {
			font-size: 16px;
		}

		.rpt_table tfoot th {
			font-size: 16px;
		}
	</style>
	<?php
	$data_array = sql_select("select image_location  from common_photo_library where master_tble_id='" . $data[0] . "' and form_name='company_details' and is_deleted=0 and file_type=1");

	$noOfCopy = "";
	for ($x = 1; $x <= $no_copy; $x++) {
		if ($x == 1) {
			$sup = 'st';
		} else if ($x == 2) {
			$sup = 'nd';
		} else if ($x == 3) {
			$sup = 'rd';
		} else {
			$sup = 'th';
		}

		$noOfCopy = "<span style='font-size:x-large;font-weight:bold'>" . $x . "<sup>" . $sup . "</sup> Copy</span>";
	?>

		<div style="width:1240px;">
			<table width="1240" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
				<tr>
					<td align="left" width="50">
						<?
						foreach ($data_array as $img_row) {
						?>
							<img src='../../../<? echo $com_dtls[2]; ?>' height='50' width='50' align="middle" />
						<?
						}
						?>
					</td>
					<td align="center" style="font-size:30px" colspan="3"><strong><? echo $com_dtls[0] . "<br><span style=\"font-size:14px;\">" . $com_dtls[1] . "</span>"; ?></strong></td>
					<td width="110" align="right"><?php echo $noOfCopy . ($is_gate_pass == 1 ? "<br><span style=\"color:#F00;font-weight:bold;\">Gate Pass Done</span>" : '') . ($is_gate_out == 1 ? "<br><span style=\"color:#F00;font-weight:bold;\">Gate Out Done</span>" : ''); ?></td>
				</tr>
				<tr>
					<td colspan="3" align="center" height="50" valign="middle" style="font-size:25px">
						<strong>Greige Fabric Delivery Challan</strong>
						<?php
						if ($data[4] == 1) {
						?>
							<!-- <span style="color:#0F0; font-weight:bold;">[Approved]</span> -->
						<?php
						}
						?>
					</td>
				</tr>
			</table>
			<div style="width:100%;">
				<div style="clear:both;">
					<table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
						<tr>
							<td width="125"><strong>Dyeing Company:</strong></td>
							<td width="250px"><? echo $knit_dye_company; ?></td>
							<td width="125"><strong>Attention:</strong></td>
							<td width="150px"><? echo $attention; ?></td>
							<td width="130"><strong>Delivery Challan No:</strong></td>
							<td width="130"><? echo $issue_number; ?></td>
						</tr>
						<tr>
							<td><strong>Address:</strong></td>
							<td><? echo $knit_dye_company_address; ?></td>
							<td><strong>Issue Purpose:</strong></td>
							<td><? echo $issue_purpose; ?></td>
							<td><strong>Delivery Date:</strong></td>
							<td><? echo change_date_format($issue_date); ?></td>
						</tr>
						<tr>
							<td><strong>Source:</strong></td>
							<td><? echo $knitting_source[$knit_dye_source]; ?></td>
							<td><strong>Remarks:</strong></td>
							<td colspan="3"><? echo $location_arr[$inhouse_location]; ?></td>
						</tr>
						<tr>
							<td align="center" colspan="6" id="barcode_img_id_<?php echo $x; ?>" height="50" valign="middle" style="border-left:hidden;border-right:hidden;border-bottom:hidden;"></td>
						</tr>
					</table>

					<table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
						<thead bgcolor="#dddddd">

							<tr>
								<th rowspan="2" width="20">SL</th>
								<th rowspan="2" width="120">Buyer, Job, <br>Style and<br>Booking</th>
								<th rowspan="2" width="45">Body Part</th>
								<th rowspan="2" width="210">Fabric Details</th>
								<th rowspan="2" width="65">Color Range</th>
								<th rowspan="2" width="180">Yarn Details</th>
								<th rowspan="2" width="80">Service Work Order</th>
								<th rowspan="2" width="80">Batch No</th>
								<th rowspan="2" width="60">Fab. Dia<br>& GSM</th>
								<th rowspan="2" width="50">MC DIA <br /> X <br /> M.GAUGE</th>
								<th rowspan="2" width="60">S.L</th>
								<th colspan="2" width="120">Delivery Qty</th>
								<th rowspan="2" width="80">Roll Qty</th>
								<th rowspan="2">Reject Qty</th>
							</tr>
							<tr>
								<th width="60">KG</th>
								<th width="60">PCS</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = 1;
							$sub_group_arr = array();

							foreach ($refno_data_array as $job => $jobArr) {
								$sub_tot_qty = 0;
								$sub_total_no_of_roll = 0;
								foreach ($jobArr as $febricDescDataArr) {
									$sub_total_no_of_roll_fabric = $sub_tot_qty_fabric = 0;
									foreach ($febricDescDataArr as $row) {
										if ($i % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";

										$count = '';
										$yarn_count = explode(",", $row['yarn_count']);
										foreach ($yarn_count as $count_id) {
											if ($count == '') $count = $yarn_count_details[$count_id];
											else $count .= "," . $yarn_count_details[$count_id];
										}

										if ($row['receive_basis'] == 1) {
											$booking_no = explode("-", $row['booking_no']);
											$prog_book_no = (int)$booking_no[3];
										} else {
											$prog_book_no = $row['bwo'];
										}

										if ($row['receive_basis'] == 2) {
											$ppl_count_ids = "";
											$countID = explode(",", $productionYarnCount[$row['bwo']]);
											foreach ($countID as $count_ids) {
												$ppl_count_ids .= $yarn_count_details[$count_ids] . ",";
											}
											$ppl_count_id = chop($ppl_count_ids, ',');

											/*$ppl_count_ids=$ppl_count_feeder_array[$row['bwo']]['count_id'];
											$ppl_count_id =chop($ppl_count_ids,',');*/
										} else if ($row['receive_basis'] == 1) {
											if ($row['booking_without_order'] == 1) {
												//$ppl_count_id =$yarn_count_details[$row['yarn_count']];
												$yarn_count = explode(",", $row['yarn_count']);
												$ppl_count_id = "";
												foreach ($yarn_count as $count_id) {
													if ($ppl_count_id == '') $ppl_count_id = $yarn_count_details[$count_id];
													else $ppl_count_id .= "," . $yarn_count_details[$count_id];
												}
											}
										}
										$fab_material = array(1 => "Organic", 2 => "BCI");
										$buyer = $jobNo = $style = $booking = $sustainability = $material = "";
										if ($row['receive_basis'] == 1) {
											if ($row['booking_without_order'] == 1) {
												$buyer = $buyer_array[$nonOrderBookingData_arr[$row['bwo']]['buyer_id']];
											} else {
												$buyer = $buyer_array[$row['buyer_id']];
											}

											if ($row['booking_without_order'] == 1) {
												$jobNo = "";
												$style = "";
												$sustainability = "";
												$material = "";
											} else {
												$jobNo = $booking_arr[$row['bwo']]["job_no"];
												$style = $booking_arr[$row['bwo']]["style_ref_no"];
												$sustainability = $sustainability_standard[$booking_arr[$row['bwo']]["sustainability_standard"]];
												$material = $fab_material[$booking_arr[$row['bwo']]["fab_material"]];
											}
										} else {
											if ($row['is_sales'] == 1) {
												$buyer = $buyer_array[$row['buyer_id']];
											} else {
												if ($row['booking_without_order'] == 1) {
													if ($row['receive_basis'] == 2 && $row['is_sales'] == 2) {
														$buyer = $buyer_array[$nonOrderBookingData_arr[$plan_arr[$prog_book_no]["booking_no"]]["buyer_id"]];
														$sustainability = $sustainability_standard[$nonOrderBookingData_arr[$plan_arr[$prog_book_no]["booking_no"]]["sustainability_std_id"]];
														$material = $fab_material[$nonOrderBookingData_arr[$plan_arr[$prog_book_no]["booking_no"]]["fabric_material_id"]];
													} else {
														$buyer = $buyer_array[$nonOrderBookingData_arr[$row['bwo']]['buyer_id']];
													}
												} else {
													$buyer = $buyer_array[$booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["buyer_id"]];
												}
											}
											if ($row['receive_basis'] == 2) {
												if ($row['is_sales'] == 2) {
													$style = $nonOrderBookingStyle;
												} else {
													$jobNo = $booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["job_no"];
													$style = $booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["style_ref_no"];
													$sustainability = $sustainability_standard[$booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["sustainability_standard"]];
													$material = $fab_material[$booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["fab_material"]];
												}
											} else {
												$jobNo = $booking_arr[$row['bwo']]["job_no"];
												$style = $booking_arr[$row['bwo']]["style_ref_no"];
												$sustainability = $sustainability_standard[$booking_arr[$row['bwo']]["sustainability_standard"]];
												$material = $fab_material[$booking_arr[$row['bwo']]["fab_material"]];
											}
										}

										if ($row['receive_basis'] == 2) {
											$booking = $plan_arr[$prog_book_no]["booking_no"];
										} else {
											$booking = $row['bwo'];
										}

							?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td style="font-size: 15px" title="order type: <? echo $row['booking_without_order']; ?>"><? echo $i; ?></td>
											<td style="font-size: 15px">
												<div style="word-wrap:break-word; width:130px"><?
																								echo $buyer . ' ::<br>' . $jobNo . ' ::<br>' . $style . ' ::<br>' . $booking . ' ::<br>' . $sustainability . ' ::' . $material; ?>
												</div>
											</td>
											<td style="font-size: 15px">
												<div style="word-wrap:break-word; width:45px"><? echo $body_part[$row['body_part_id']]; ?></div>
											</td>
											<td style="font-size: 15px" title="<? echo $row['febric_description_id']; ?>">
												<div style="word-wrap:break-word; width:210px">
													<?
													$color_id_arr = array_unique(explode(",", $row["color_id"]));
													$all_color_name = "";
													foreach ($color_id_arr as $c_id) {
														$all_color_name .= $color_arr[$c_id] . ",";
													}
													$all_color_name = chop($all_color_name, ",");
													echo $all_color_name . ' :: ' . $composition_arr[$row['febric_description_id']]; ?>
												</div>
											</td>
											<td style="font-size: 15px">
												<div style="word-wrap:break-word; width:65px"><? echo $color_range[$row["color_range_id"]]; ?></div>
											</td>
											<td style="font-size: 15px" title="Yarn Dtls:<? echo $row['detarmination_id']; ?>">
												<div style="word-wrap:break-word; width:180">
													<? echo $ppl_count_id . ', ' . $yarn_composition_arr[$row['febric_description_id']] . ', ' . $row['yarn_lot'] . ', ' . $brand_details[$row['brand_id']]; ?>
												</div>
											</td>
											<td style="font-size: 15px">
												<div style="word-wrap:break-word; width:80px">

												</div>
											</td>
											<td style="font-size: 15px">
												<div style="word-wrap:break-word; width:80px">

												</div>
											</td>
											<td style="font-size: 15px; text-align: center;">
												<div style="word-wrap:break-word; width:60px">
													<? echo $row['width'] . ' & ' . $row['gsm']; ?>
												</div>
											</td>
											<td style="font-size: 15px">
												<div style="word-wrap:break-word; width:65px;text-align: center;"><? echo $row['machine_dia'] . 'X' . $row['machine_gg']; ?></div>
											</td>
											<td style="font-size: 15px; text-align: center;">
												<div style="word-wrap:break-word; width:60px"><? echo $row['stitch_length']; ?></div>
											</td>
											<td style="font-size: 15px" align="right" style="font-size: 15px;"><? echo number_format($row['issue_qty'], 2, '.', ''); ?></td>
											<td style="font-size: 15px" align="right" style="font-size: 15px;"><?
																												if ($row['issue_qty_pcs'] == "") {
																													echo 0;
																												} else {
																													echo $row['issue_qty_pcs'];
																												} ?>
											</td>
											<td style="font-size: 15px" align="right"><? echo $row['num_of_roll']; ?></td>
											<td style="font-size: 15px" align="right">
												<div style="word-wrap:break-word; width:60px"><? echo number_format($row['reject_qnty'], 2, '.', ''); ?></div>
											</td>
										</tr>
									<?
										$sub_tot_qty_fabric += $row['issue_qty'];
										$sub_total_issue_qty_pcs_fabric += $row['issue_qty_pcs'];
										$sub_total_no_of_roll_fabric += $row['num_of_roll'];
										$sub_total_reject_qnty_fabric += $row['reject_qnty'];

										$sub_tot_qty += $row['issue_qty'];
										$sub_total_issue_qty_pcs_qnty += $row['issue_qty_pcs'];
										$sub_total_no_of_roll += $row['num_of_roll'];
										$sub_total_reject_qnty += $row['reject_qnty'];

										$i++;
										$grnd_tot_qty += $row['issue_qty'];
										$grnd_total_issue_qty_pcs_qnty += $row['issue_qty_pcs'];
										$grnd_total_no_of_roll += $row['num_of_roll'];
										$grnd_total_reject_qnty += $row['reject_qnty'];
									}
									?>
									<tr class="tbl_bottom">
										<td colspan="11" style=" text-align:right;font-size: 14px;"><strong>Fabric Type Total</strong></td>
										<td align="right" style="font-size: 14px;">
											<b><? echo number_format($sub_tot_qty_fabric, 2, '.', ''); ?></b>
										</td>
										<td align="right" style="font-size: 14px;"><? echo $sub_total_issue_qty_pcs_fabric; ?></td>
										<td align="right" style="font-size: 14px;"><? echo number_format($sub_total_no_of_roll_fabric, 2, '.', ''); ?></td>
										<td align="right" style="font-size: 14px;"><? echo number_format($sub_total_reject_qnty_fabric, 2, '.', ''); ?></td>
									</tr>
								<?
								}
								// echo "<pre>";print_r($refno_data_array);
								if ($row['booking_without_order'] == 0) {
								?>
									<tr class="tbl_bottom">
										<td colspan="11" style=" text-align:right;font-size: 14px;"><strong>Job Total</strong></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $sub_tot_qty; ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $sub_total_issue_qty_pcs_qnty; ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($sub_total_no_of_roll, 2); ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($sub_total_reject_qnty, 2); ?></td>
									</tr>
							<?
								}
							}

							?>
							<tr class="tbl_bottom">

								<td style="font-size: 16px;" colspan="2" align="center"><b>Total Job:
										<?php
										if (!empty($jobCountArr)) {
											echo " " . count($jobCountArr);
										}
										?></b></td>
								<td align="right"></td>
								<td align="right" style="font-size: 16px;" colspan="8"><strong>Grand Total</strong></td>
								<td align="center" style="font-weight: bold; font-size: 16px;"><? echo number_format($grnd_tot_qty, 2, '.', ''); ?></td>
								<td align="right" style="font-size: 16px;"><strong><? echo $grnd_total_issue_qty_pcs_qnty; ?></strong></td>
								<td align="right" style="font-size: 16px;"><strong><? echo number_format($grnd_total_no_of_roll, 2, '.', ''); ?></strong></td>
								<td align="right" style="font-size: 16px;"><strong><? echo number_format($grnd_total_reject_qnty, 2, '.', ''); ?></strong></td>
							</tr>
						</tbody>
					</table>
					<br>
					<!-- =========== Collar and Cuff Details Start ============= -->
					<?
					//echo '<pre>';print_r($coller_cuff_data_arr);
					$CoCu = 1;
					foreach ($coller_cuff_data_arr as $coll_cuff_id => $booking_data_arr) {
						if (count($booking_data_arr) > 0) {
					?>
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left; margin-bottom:10px;">
								<thead bgcolor="#dddddd">
									<tr>
										<th colspan="3"><? echo $body_part_data_arr[$coll_cuff_id]['body_part_full_name']; ?> Details</th>
									</tr>
									<tr>
										<th>Size</th>
										<th>Qty Pcs</th>
										<th>No. of Roll</th>
									</tr>
								</thead>
								<?
								$coller_cuff_qty_total = $coller_cuff_roll_total = 0;
								foreach ($booking_data_arr as $bookingId => $bookingData) {
									foreach ($bookingData as $jobId => $jobData) {
										foreach ($jobData as $size => $row) {
								?>
											<tr>
												<td align="center"><? echo $size; ?></td>
												<td align="center"><? echo $row['qc_pass_qnty_pcs']; ?></td>
												<td align="center"><? echo $row['no_of_roll']; ?></td>
											</tr>
								<?
											$coller_cuff_qty_total += $row['qc_pass_qnty_pcs'];
											$coller_cuff_roll_total += $row['no_of_roll'];
										}
									}
								}
								?>
								<tr>
									<td align="right"><b>Total</b></td>
									<td align="center"><b><? echo $coller_cuff_qty_total; ?></b></td>
									<td align="center"><b><? echo $coller_cuff_roll_total; ?></b></td>
								</tr>
							</table>
					<?
							if ($CoCu == 1) {
								echo "<table width=\"30%\" style=\"float:left\"><tr><td colspan=\"3\">&nbsp;</td></tr></table>";
							}
							$CoCu++;
						}
					}
					?>
					<!-- =========== Collar and Cuff Details End ============= -->

					<!-- ============= Gate Pass Info Start ========= -->
					<table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
						<tr>
							<td colspan="15" height="30" style="border-left:hidden;border-right:hidden; text-align: center;">For mishandling or other reason no claim is acceptable in any stage, once the Goods is received in good condition and quality and out from factory premises.</td>
						</tr>
						<tr>
							<td colspan="4" align="center" valign="middle" style="font-size:25px;"><strong>&lt;&lt;Gate Pass&gt;&gt;</strong></td>
							<td colspan="9" align="center" valign="middle" id="gate_pass_barcode_img_id_<?php echo $x; ?>" height="50"></td>
						</tr>
						<tr>
							<td colspan="2" title="<? echo $system_no; ?>"><strong>From Company:</strong></td>
							<td colspan="2" width="120"><?php echo $gatePassDataArr[$system_no]['from_company']; ?></td>

							<td colspan="2"><strong>To Company:</strong></td>
							<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['to_company']; ?></td>

							<td colspan="3"><strong>Carried By:</strong></td>
							<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['carried_by']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>From Location:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['from_location']; ?></td>
							<td colspan="2"><strong>To Location:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['to_location']; ?></td>
							<td colspan="3"><strong>Driver Name:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_name']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Gate Pass ID:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_id']; ?></td>
							<td colspan="2" rowspan="2"><strong>Delivery Qnty</strong></td>
							<td align="center"><strong>Kg</strong></td>
							<td align="center"><strong>Roll</td>
							<td align="center"><strong>PCS</td>
							<td colspan="3"><strong>Vehicle Number:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['vhicle_number']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Gate Pass Date:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_date']; ?></td>
							<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_kg']; ?></td>
							<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_bag']; ?></td>
							<td align="center"><?php
												if ($gatePassDataArr[$system_no]['gate_pass_id'] != "") {
													if ($grnd_total_issue_qty_pcs_qnty > 0) {
														echo $grnd_total_issue_qty_pcs_qnty;
													}
												}
												?></td>
							<td colspan="3"><strong>Driver License No.:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_license_no']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Out Date:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_date']; ?></td>
							<td colspan="2"><strong>Dept. Name:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['department']; ?></td>
							<td colspan="3"><strong>Mobile No.:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['mobile_no']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Out Time:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_time']; ?></td>
							<td colspan="2"><strong>Attention:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['attention']; ?></td>
							<td colspan="3"><strong>Sequrity Lock No.:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['security_lock_no']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Returnable:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['returnable']; ?></td>
							<td colspan="2"><strong>Purpose:</strong></td>
							<td colspan="9"><?php echo $gatePassDataArr[$system_no]['issue_purpose']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Est. Return Date:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['est_return_date']; ?></td>
							<td colspan="2"><strong>Remarks:</strong></td>
							<td colspan="9"><?php echo $gatePassDataArr[$system_no]['remarks']; ?></td>
						</tr>
					</table>
					<!-- ============= Gate Pass Info End =========== -->
				</div>
				<br>
				<? echo signature_table(124, $company, "1200px"); ?>
			</div>
		</div>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {
				var zs = '<?php echo $x; ?>';
				var value = valuess; //$("#barcodeValue").val();
				var btype = 'code39'; //$("input[name=btype]:checked").val();
				var renderer = 'bmp'; // $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#barcode_img_id_" + zs).html('11');
				value = {
					code: value,
					rect: false
				};
				$("#barcode_img_id_" + zs).show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $data[1]; ?>');

			//for gate pass barcode
			function generateBarcodeGatePass(valuess) {
				var zs = '<?php echo $x; ?>';
				var value = valuess; //$("#barcodeValue").val();
				var btype = 'code39'; //$("input[name=btype]:checked").val();
				var renderer = 'bmp'; // $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#gate_pass_barcode_img_id_" + zs).html('11');
				value = {
					code: value,
					rect: false
				};
				$("#gate_pass_barcode_img_id_" + zs).show().barcode(value, btype, settings);
			}

			if ('<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>' != '') {
				generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
			}
		</script>
		<div style="page-break-after:always;"></div>
	<?php
	}
	exit();
}
// This print report only for norban, if any kind of change for others customer please contact with norban concern person
if ($action == "roll_issue_no_of_copy_print") // Print 1, created by Tipu
{
	extract($_REQUEST);
	echo load_html_head_contents("Roll Wise Grey Issue", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);
	//var_dump($data);
	$company   		= $data[0];
	$system_no 		= $data[1];
	$report_title 	= $data[2];
	$mst_id     	= $data[3];
	$knit_source    = $data[4];
	$no_copy 		= $data[5];
	$dyeing_company = $data[6];
	$dyeing_source  = $data[7];

	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	//$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$body_part_type = return_library_array("select id, body_part_type from lib_body_part where status_active=1", 'id', 'body_part_type');
	$composition_arr = array();
	$yarn_composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " :: " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . " :: " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}

		if (array_key_exists($row[csf('id')], $yarn_composition_arr)) {
			$yarn_composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]];
		} else {
			$yarn_composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]];
		}
	}



	$store_location_id = return_field_value("location_id", "lib_store_location", "id=$store_id and is_deleted=0", "location_id");
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$country_name_arr = return_library_array("select id, country_name from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name");


	//for buyer
	$sqlBuyer = sql_select("select id as ID, buyer_name as BUYER_NAME, short_name as SHORT_NAME from lib_buyer");
	foreach ($sqlBuyer as $row) {
		$buyer_arr[$row['ID']] = $row['SHORT_NAME'];
		$buyer_dtls_arr[$row['ID']] = $row['BUYER_NAME'];
	}
	unset($sqlBuyer);

	$company_info = sql_select("select ID, COMPANY_NAME, PLOT_NO, ROAD_NO, CITY, CONTACT_NO, COUNTRY_ID from lib_company where status_active=1 and is_deleted=0 order by company_name");
	foreach ($company_info as $row) {
		$company_library[$row['ID']] = $row['COMPANY_NAME'];
		$company_address_arr[$row['ID']] = 'Plot No:' . $row['PLOT_NO'] . ', Road No:' . $row['ROAD_NO'] . ', City / Town:' . $row['CITY'] . ', Country:' . $country_name_arr[$row['COUNTRY_ID']] . ', Contact No:' . $row['CONTACT_NO'];
	}
	unset($company_info);

	//for supplier
	$sqlSupplier = sql_select("select id as ID, supplier_name as SUPPLIER_NAME, short_name as SHORT_NAME, ADDRESS_1 from lib_supplier where id=$dyeing_company");
	foreach ($sqlSupplier as $row) {
		$supplier_arr[$row['ID']] = $row['SHORT_NAME'];
		$supplier_dtls_arr[$row['ID']] = $row['SUPPLIER_NAME'];
		$supplier_address_arr[$row['ID']] = $row['ADDRESS_1'];
	}
	unset($sqlSupplier);

	//for gate pass
	$sql_get_pass = "SELECT a.ID, a.SYS_NUMBER, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.DRIVER_NAME,a.DRIVER_LICENSE_NO,a.SECURITY_LOCK_NO, b.QUANTITY, b.NO_OF_BAGS FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b WHERE a.id = b.mst_id AND a.company_id = " . $company . " AND a.basis = 3 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.challan_no LIKE '" . $system_no . "%'";
	// echo $sql_get_pass;
	$sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach ($sql_get_pass_rslt as $row) {
		$exp = explode(',', $row['CHALLAN_NO']);
		// echo "<pre>"; print_r($exp);
		foreach ($exp as $key => $val) {
			if ($val == $system_no) {
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];

				$row['OUT_DATE'] = ($row['OUT_DATE'] != '' ? date('d-m-Y', strtotime($row['OUT_DATE'])) : '');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE'] != '' ? date('d-m-Y', strtotime($row['EST_RETURN_DATE'])) : '');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE'] != '' ? date('d-m-Y', strtotime($row['EST_RETURN_DATE'])) : '');

				if ($row['WITHIN_GROUP'] == 1) {
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					$row['LOCATION_NAME'] = $location_arr[$row['LOCATION_ID']];
				}

				//for gate pass info
				$gatePassDataArr[$val]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$val]['from_company'] = $company_library[$row['COMPANY_ID']];
				$gatePassDataArr[$val]['from_location'] = $location_arr[$row['COM_LOCATION_ID']];
				$gatePassDataArr[$val]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$val]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$val]['est_return_date'] = $row['EST_RETURN_DATE'];

				$gatePassDataArr[$val]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$val]['to_location'] = $row['LOCATION_NAME'];
				$gatePassDataArr[$val]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$val]['delivery_bag'] += $row['NO_OF_BAGS'];

				$gatePassDataArr[$val]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				$gatePassDataArr[$val]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$val]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$val]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$val]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$val]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$val]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$val]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$val]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
				$gatePassDataArr[$val]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
			}
		}
	}
	// echo "<pre>";print_r($gatePassDataArr);

	//for gate out
	if ($gate_pass_id != '') {
		$sql_gate_out = "SELECT OUT_DATE, OUT_TIME FROM INV_GATE_OUT_SCAN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND INV_GATE_PASS_MST_ID='" . $gate_pass_id . "'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if (!empty($sql_gate_out_rslt)) {
			foreach ($sql_gate_out_rslt as $row) {
				$is_gate_out = 1;
				$gatePassDataArr[$system_no]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$system_no]['out_time'] = $row['OUT_TIME'];
			}
		}
	}

	// Issue master data
	$dataArray = sql_select("SELECT issue_number, issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no, remarks, attention from inv_issue_master where id=$mst_id");
	$reqBookingNoArr = array();
	foreach ($dataArray as $row) {
		$issue_number = $row[csf('issue_number')];
		$issue_date = $row[csf('issue_date')];
		// $knit_dye_company = $row[csf('knit_dye_company')];
		$knit_dye_source = $row[csf('knit_dye_source')];
		$issue_purpose = $yarn_issue_purpose[$row[csf('issue_purpose')]];
		$attention = $row[csf('attention')];
		$remarks = $row[csf('remarks')];

		//for issue to
		$knit_dye_company = '';
		if ($row[csf('knit_dye_source')] == 1)
			$knit_dye_company = $company_library[$row[csf('knit_dye_company')]];
		else
			$knit_dye_company = $supplier_dtls_arr[$row[csf('knit_dye_company')]];

		$knit_dye_company_address = '';
		if ($row[csf('knit_dye_source')] == 1)
			$knit_dye_company_address = $company_address_arr[$row[csf('knit_dye_company')]];
		else
			$knit_dye_company_address = $supplier_address_arr[$row[csf('knit_dye_company')]];
	}

	// Roll issue
	$issue_res = sql_select("SELECT a.barcode_no, sum(a.qnty) as issue_qty, a.po_breakdown_id, a.booking_without_order from pro_roll_details a where a.entry_form=61 and a.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 group by a.barcode_no, a.po_breakdown_id, a.booking_without_order");
	$issued_data_arr = $po_ref_arr = $sales_id_arr = $non_order_booking_arr = array();
	foreach ($issue_res as $val) {
		$barcode_nums .= $val[csf("barcode_no")] . ",";
		$qntyFromRoll[$val[csf("barcode_no")]] = $val[csf("issue_qty")];
		$issued_data_arr[$val[csf('barcode_no')]]['po_id'] = $val[csf('po_breakdown_id')];
		$issued_data_arr[$val[csf('barcode_no')]]['is_sales'] = $val[csf('is_sales')];
		$issued_data_arr[$val[csf('barcode_no')]]['booking_without_order'] = $val[csf('booking_without_order')];
		if ($val[csf('is_sales')] == 0) //
		{
			if ($val[csf('booking_without_order')] == 1) {
				$non_order_booking_arr[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
			} else {
				$po_ref_arr[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
			}
		} else  // sales order
		{
			$sales_id_arr[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
		}
	}
	// echo "<pre>";print_r($issued_data_arr);die;
	$barcode_nums = chop($barcode_nums, ",");

	// order and with order booking
	$job_array = $job_company_arr = $sales_arr = $sales_booking_arr = $booking_arr = array();
	if (!empty($po_ref_arr)) {
		$job_sql = "SELECT  b.id, a.job_no_prefix_num, a.job_no,b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping as ref_no, a.buyer_name, a.sustainability_standard, a.fab_material, c.booking_no ,b.grouping
		from wo_po_details_master a, wo_po_break_down b left join wo_booking_dtls c on c.po_break_down_id=b.id
		where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in (1,3) and b.is_deleted=0 and c.booking_type in(1) and c.is_short=2 and b.id in(" . implode(",", $po_ref_arr) . ")
		group by b.id, a.job_no_prefix_num, a.job_no, b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping, a.buyer_name, a.sustainability_standard, a.fab_material, c.booking_no, b.grouping";
		$job_sql_result = sql_select($job_sql); // is_short=2 main fabric booking
		foreach ($job_sql_result as $row) {
			$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
			$job_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
			$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
			$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
			$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
			$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			$job_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
			$job_array[$row[csf("id")]]["sustainability_std_id"] = $row[csf("sustainability_standard")];
			$job_array[$row[csf("id")]]["fabric_material_id"] = $row[csf("fab_material")];
			$job_company_arr[$row[csf('job_no_prefix_num')]]['company_name'] = $row[csf('company_name')];
			$job_array[$row[csf("id")]]["booking_no"] = $row[csf("booking_no")];
			$booking_arr[$row[csf("booking_no")]]["booking_ref_no"] = $row[csf("ref_no")];

			//$int_ref_arr[$row[csf('job_no')]]["grouping"] = $row[csf("grouping")];
		}
	}


	$int_ref_sql = "select internal_ref, job_no from wo_order_entry_internal_ref";

	$int_ref = sql_select($int_ref_sql);

	foreach ($int_ref as $row) {
		$int_ref_arr[$row[csf('job_no')]]["grouping"] = $row[csf("internal_ref")];
	}
	// Sales order
	if (!empty($sales_id_arr)) {
		$sales_details = sql_select("SELECT id, job_no, sales_booking_no, within_group, style_ref_no, buyer_id, po_buyer, po_job_no from fabric_sales_order_mst where id in(" . implode(",", $sales_id_arr) . ")");
		foreach ($sales_details as $sales_row) {
			$sales_arr[$sales_row[csf('id')]]['sales_booking_no'] = $sales_row[csf('sales_booking_no')];
			$sales_arr[$sales_row[csf('id')]]['within_group'] = $sales_row[csf('within_group')];
			$sales_arr[$sales_row[csf('id')]]['style_ref_no'] = $sales_row[csf('style_ref_no')];
			$sales_arr[$sales_row[csf('id')]]['buyer_id'] = $sales_row[csf('buyer_id')];
			$sales_arr[$sales_row[csf('id')]]['po_buyer'] = $sales_row[csf('po_buyer')];
			$sales_arr[$sales_row[csf('id')]]['po_job_no'] = $sales_row[csf('po_job_no')];
			$sales_booking_arr[] = "'" . $sales_row[csf('sales_booking_no')] . "'";
		}
	}

	// Non-order booking
	$non_order_booking_sql = sql_select("SELECT a.id as booking_id, a.booking_no,a.buyer_id,a.grouping, b.style_id
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
	where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.id in(" . implode(",", $non_order_booking_arr) . ") group by a.id, a.booking_no,a.buyer_id,a.grouping, b.style_id");
	$nonOrderBookingData_arr = array();
	foreach ($non_order_booking_sql as $row) {
		$style_id = $row[csf("style_id")];
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['booking_no'] = $row[csf('booking_no')];
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['buyer_id'] = $row[csf('buyer_id')];
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['grouping'] = $row[csf('grouping')];
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['sustainability_std_id'] = return_field_value("sustainability_std_id", "sample_development_mst", "id=$style_id");
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['fabric_material_id'] = return_field_value("fabric_material_id", "sample_development_mst", "id=$style_id");
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['style_id'] = return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
	}
	// $nonOrderBookingStyle=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");

	// Knitting Production
	$sql = "SELECT a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as issue_qty,count(c.barcode_no) as num_of_roll, sum(c.reject_qnty) as reject_qnty, sum(c.qc_pass_qnty_pcs) as issue_qty_pcs, b.body_part_id, b.prod_id, d.detarmination_id, c.barcode_no,  c.po_breakdown_id
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d
	where a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums)
	group by  a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no, c.booking_without_order, c.is_sales, b.body_part_id, b.prod_id, d.detarmination_id, c.barcode_no, c.po_breakdown_id
	order by a.booking_no";
	// echo $sql;die;
	$sql_result = sql_select($sql);
	$booking_program_arr = array();
	foreach ($sql_result as $row) {
		if ($row[csf('receive_basis')] == 2) {
			$booking_program_arr[] = $row[csf("booking_no")];
			$productionYarnCount[$row[csf("booking_no")]] = $row[csf("yarn_count")];
		} else {
			$booking_no = explode("-", $row[csf('booking_no')]);
			$booking_program_arr[] = (int)$booking_no[3];
		}
	}
	//print_r($booking_program_arr);
	$planOrder = sql_select("SELECT a.id, b.booking_no, b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(" . implode(",", $booking_program_arr) . ")");
	$plan_arr = array();
	foreach ($planOrder as $plan_row) {
		$plan_arr[$plan_row[csf("id")]]["machine_dia"] = $plan_row[csf("machine_dia")];
		$plan_arr[$plan_row[csf("id")]]["feeder"] = $plan_row[csf("feeder")];
		$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
	}

	$refno_data_array = array();
	foreach ($sql_result as $row) {
		$po_id = $issued_data_arr[$row[csf('barcode_no')]]['po_id'];
		$booking_without_order = $issued_data_arr[$row[csf('barcode_no')]]['booking_without_order'];

		$is_sales = $row[csf('is_sales')];
		if ($is_sales == 1 || $is_sales == 2) {
			$within_group = $sales_arr[$po_id]['within_group'];
			if ($within_group == 1) {
				$style_ref_no = "";
				$sales_booking = $sales_arr[$po_id]['sales_booking_no'];
				$po = $sales_arr[$po_id]['sales_order'];
				$job_no = $job_array[$sales_booking]['job_no'];
				$buyer_id = $job_array[$sales_booking]['buyer_name'];
			} else {
				$job_no = "";
				$style_ref_no = "";
				$ref_no = "";
				$po = $sales_arr[$po_id]['sales_order'];
				$buyer_id = $sales_arr[$po_id]['buyer_name'];
				$booking_no = $sales_arr[$po_id]['sales_booking_no'];
			}
		} else {
			if ($booking_without_order == 1) {
				$ref_no = '';
				$job_no = '';
				$po = '';
				$booking_no = $nonOrderBookingData_arr[$po_id]["booking_no"];
				$buyer_id = $nonOrderBookingData_arr[$po_id]["buyer_id"];
				$style_ref_no = $nonOrderBookingData_arr[$po_id]["style_id"];
				$sustainability = $nonOrderBookingData_arr[$po_id]["sustainability_std_id"];
				$material = $nonOrderBookingData_arr[$po_id]["fabric_material_id"];
			} else {
				$ref_no = $job_array[$po_id]['ref_no'];
				$job_no = $job_array[$po_id]['job_no'];
				$style_ref_no = $job_array[$po_id]['style_ref_no'];
				$po = $job_array[$po_id]['po'];
				$buyer_id = $job_array[$po_id]['buyer_name'];
				$booking_no = $job_array[$po_id]["booking_no"];
				$sustainability = $job_array[$po_id]["sustainability_std_id"];
				$material = $job_array[$po_id]["fabric_material_id"];
			}
		}
		// echo $booking_no;

		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['job_no'] = $job_no;
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['buyer_id'] = $buyer_id;
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['style_ref_no'] = $style_ref_no;
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['booking_no'] .=$booking_no.',';
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['sustainability'] = $sustainability;
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['material'] = $material;

		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['body_part_id'] = $row[csf('body_part_id')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['detarmination_id'] = $row[csf('detarmination_id')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['color_id'] .= $row[csf('color_id')] . ',';
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['color_range_id'] = $row[csf('color_range_id')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['brand_id'] = $row[csf('brand_id')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['stitch_length'] = $row[csf('stitch_length')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['issue_qty'] += $row[csf('issue_qty')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['issue_qty_pcs'] += $row[csf('issue_qty_pcs')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['reject_qnty'] += $row[csf('reject_qnty')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['num_of_roll'] += $row[csf('num_of_roll')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['barcode_no'].=$row[csf('barcode_no')].',';
	}
	// echo "<pre>"; print_r($refno_data_array);die;

	$colarCupArr = sql_select("SELECT id, body_part_type, body_part_full_name from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(40,50)");
	foreach ($colarCupArr as $row) {
		$body_part_data_arr[$row[csf('id')]]['body_part_full_name'] = $row[csf('body_part_full_name')];
		$body_part_data_arr[$row[csf('id')]]['body_part_type'] = $row[csf('body_part_type')];
	}

	// For Coller and Cuff data
	$sql_coller_cuff = "SELECT a.receive_basis, a.booking_no, b.body_part_id, c.booking_no as bwo, c.coller_cuff_size, c.qnty, c.qc_pass_qnty_pcs, c.barcode_no
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums) order by b.body_part_id, c.coller_cuff_size";
	// echo $sql_coller_cuff;
	$sql_coller_cuff_result = sql_select($sql_coller_cuff);
	foreach ($sql_coller_cuff_result as $row2) {
		if ($body_part_data_arr[$row2[csf('body_part_id')]]['body_part_type'] > 0 && $row2[csf('qc_pass_qnty_pcs')] > 0) {
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qnty'] += $row2[csf('qnty')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty_pcs'] += $row2[csf('qc_pass_qnty_pcs')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['receive_basis'] = $row2[csf('receive_basis')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['no_of_roll'] += count($row2[csf('barcode_no')]);
		}
	}
	$com_dtls = fnc_company_location_address($company, $store_location_id, 2);
	?>
	<style type="text/css">
		table tr td {
			font-size: 16px;
		}

		.rpt_table thead th {
			font-size: 16px;
		}

		.rpt_table tfoot th {
			font-size: 16px;
		}
	</style>
	<?php
	$data_array = sql_select("select image_location  from common_photo_library where master_tble_id='" . $data[0] . "' and form_name='company_details' and is_deleted=0 and file_type=1");
	$lib_store_name = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");


	$data_store_transfer_sql = sql_select("select a.store_id from inv_transaction a,pro_roll_details b where a.mst_id=b.mst_id and b.entry_form=82 and a.item_category=13 and a.transaction_type=5 and b.barcode_no in($barcode_nums)
	group by a.store_id");
	$sotreNameTransfer = $sotreName = $transferBarcode = "";
	foreach ($data_store_transfer_sql as $row) {
		$transferBarcode .= $row[csf('store_id')] . ",";
		$sotreNameTransfer .= $lib_store_name[$row[csf('store_id')]] . ",";
	}
	$transferBarcode = chop($transferBarcode, ",");
	$sotreNameTransfer = chop($sotreNameTransfer, ",");


	if ($transferBarcode != "") {
		$transferBarcodeCond = "and b.barcode_no not in($transferBarcode)";
	}

	$data_store_sql = sql_select("select a.store_id from inv_transaction a,pro_roll_details b where a.mst_id=b.mst_id and b.entry_form=58 and a.item_category=13 and a.transaction_type=1 and b.barcode_no in($barcode_nums) $transferBarcodeCond
	group by a.store_id");

	foreach ($data_store_sql as $row) {
		$sotreName .= $lib_store_name[$row[csf('store_id')]] . ",";
	}

	$sotreName = chop($sotreName, ",");
	$allStore = $sotreNameTransfer . $sotreName;


	$noOfCopy = "";
	for ($x = 1; $x <= $no_copy; $x++) {
		if ($x == 1) {
			$sup = 'st';
		} else if ($x == 2) {
			$sup = 'nd';
		} else if ($x == 3) {
			$sup = 'rd';
		} else {
			$sup = 'th';
		}

		$noOfCopy = "<span style='font-size:x-large;font-weight:bold'>" . $x . "<sup>" . $sup . "</sup> Copy</span>";
	?>

		<div style="width:1240px;">
			<table width="1240" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
				<tr>
					<td align="left" width="50">
						<?
						foreach ($data_array as $img_row) {
						?>
							<img src='../../../<? echo $com_dtls[2]; ?>' height='50' width='50' align="middle" />
						<?
						}
						?>
					</td>
					<td align="center" style="font-size:30px" colspan="3"><strong><? echo $com_dtls[0] . "<br><span style=\"font-size:14px;\">" . $com_dtls[1] . "</span>"; ?></strong></td>
					<td width="110" align="right"><?php echo $noOfCopy . ($is_gate_pass == 1 ? "<br><span style=\"color:#F00;font-weight:bold;\">Gate Pass Done</span>" : '') . ($is_gate_out == 1 ? "<br><span style=\"color:#F00;font-weight:bold;\">Gate Out Done</span>" : ''); ?></td>
				</tr>
				<tr>
					<td colspan="3" align="center" height="50" valign="middle" style="font-size:25px">
						<strong>Greige Fabric Delivery Challan</strong>
						<?php
						if ($data[4] == 1) {
						?>
							<!-- <span style="color:#0F0; font-weight:bold;">[Approved]</span> -->
						<?php
						}
						?>
					</td>
				</tr>
			</table>
			<div style="width:100%;">
				<div style="clear:both;">
					<table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
						<tr>
							<td width="125"><strong>Dyeing Company:</strong></td>
							<td width="250px"><? echo $knit_dye_company; ?></td>
							<td width="125"><strong>Attention:</strong></td>
							<td width="150px"><? echo $attention; ?></td>
							<td width="130"><strong>Delivery Challan No:</strong></td>
							<td width="130"><? echo $issue_number; ?></td>
						</tr>
						<tr>
							<td><strong>Address:</strong></td>
							<td><? echo $knit_dye_company_address; ?></td>
							<td><strong>Issue Purpose:</strong></td>
							<td><? echo $issue_purpose; ?></td>
							<td><strong>Delivery Date:</strong></td>
							<td><? echo change_date_format($issue_date); ?></td>
						</tr>
						<tr>
							<td><strong>Source:</strong></td>
							<td><? echo $knitting_source[$knit_dye_source]; ?></td>
							<td><strong>Store Name:</strong></td>
							<td><? echo $allStore; ?></td>
							<td><strong>Remarks:</strong></td>
							<td><? echo $location_arr[$inhouse_location]; ?></td>
						</tr>
						<tr>
							<td align="center" colspan="6" id="barcode_img_id_<?php echo $x; ?>" height="50" valign="middle" style="border-left:hidden;border-right:hidden;border-bottom:hidden;"></td>
						</tr>
					</table>

					<table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
						<thead bgcolor="#dddddd">

							<tr>
								<th rowspan="2" width="20">SL</th>
								<th rowspan="2" width="120">Buyer, Job, <br>Style and<br>Booking</th>
								<th rowspan="2" width="45">Body Part</th>
								<th rowspan="2" width="210">Fabric Details</th>
								<th rowspan="2" width="65">Color Range</th>
								<th rowspan="2" width="180">Yarn Details</th>
								<th rowspan="2" width="180">Internal Ref.</th>
								<th rowspan="2" width="180">Fabric Color</th>
								<th rowspan="2" width="80">Service Work Order</th>
								<th rowspan="2" width="80">Batch No</th>
								<th rowspan="2" width="60">Fab. Dia<br>& GSM</th>
								<th rowspan="2" width="50">MC DIA <br /> X <br /> M.GAUGE</th>
								<th rowspan="2" width="60">S.L</th>
								<th colspan="2" width="120">Delivery Qty</th>
								<th rowspan="2" width="80">Roll Qty</th>
								<th rowspan="2">Reject Qty</th>
							</tr>
							<tr>
								<th width="60">KG</th>
								<th width="60">PCS</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = 1;
							$k = 1;
							$sub_group_arr = array();
							//$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]]['num_of_roll']+=$row[csf('num_of_roll')];
							$fab_material = array(1 => "Organic", 2 => "BCI");
							foreach ($refno_data_array as $job => $jobArr) {
								$sub_tot_qty = 0;
								$sub_total_no_of_roll = 0;
								foreach ($jobArr as $febricDesc => $febricDescDataArr) {
									$sub_total_no_of_roll_fabric = $sub_tot_qty_fabric = 0;
									foreach ($febricDescDataArr as $yarn_lot => $yarn_lotArr) {
										foreach ($yarn_lotArr as $yarn_count => $yarn_countArr) {
											foreach ($yarn_countArr as $machine_dia => $machine_diaArr) {
												foreach ($machine_diaArr as $machine_gg => $machine_ggArr) {
													foreach ($machine_ggArr as $gsm => $gsmArr) {
														foreach ($gsmArr as $dia => $diaArr) {
															foreach ($diaArr as $color_ids => $color_ids_Data) {
																foreach ($color_ids_Data as $stitch_length => $row) {
																	if ($i % 2 == 0)
																		$bgcolor = "#E9F3FF";
																	else
																		$bgcolor = "#FFFFFF";

																	$ycount = '';
																	$yarn_count = explode(",", $yarn_count);
																	foreach ($yarn_count as $count_id) {
																		if ($ycount == '') $ycount = $yarn_count_details[$count_id];
																		else $ycount .= "," . $yarn_count_details[$count_id];
																	}

																	$booking_nos = implode(",",array_filter(array_unique(explode(",",chop($row['booking_no'] ,",")))));

							?>
																	<tr bgcolor="<? echo $bgcolor; ?>">
																		<td style="font-size: 15px"><? echo $i; ?></td>
																		<td style="font-size: 15px">
																			<div style="word-wrap:break-word; width:130px"><?
																															if ($knit_dye_source == 3 && $dyeing_source == 0) {
																																$buyer_info = 'WHS';
																																$style_info = 'WHS';
																															} else {
																																$buyer_info = $buyer_array[$row['buyer_id']];
																																$style_info = $row['style_ref_no'];
																															}


																															echo $buyer_info . ' ::<br>' . $job . ' ::<br>' . $style_info . ' ::<br>' . $booking_nos . ' ::<br>' . $sustainability_standard[$row['sustainability']] . ' ::' . $fab_material[$row['material']];
																															?>
																			</div>
																		</td>
																		<td style="font-size: 15px">
																			<div style="word-wrap:break-word; width:45px"><? echo $body_part[$row['body_part_id']]; ?></div>
																		</td>
																		<td style="font-size: 15px" title="<? echo $febricDesc; ?>">
																			<div style="word-wrap:break-word; width:210px">
																				<?
																				$color_id_arr = array_unique(explode(",", $row["color_id"]));
																				$all_color_name = "";
																				foreach ($color_id_arr as $c_id) {
																					$all_color_name .= $color_arr[$c_id] . ",";
																				}
																				$all_color_name = chop($all_color_name, ",");
																				echo $all_color_name . ' :: ' . $composition_arr[$febricDesc]; ?>
																			</div>
																		</td>
																		<td style="font-size: 15px">
																			<div style="word-wrap:break-word; width:65px"><? echo $color_range[$row["color_range_id"]]; ?></div>
																		</td>
																		<td style="font-size: 15px" title="Yarn Dtls:<? echo $row['detarmination_id']; ?>">
																			<div style="word-wrap:break-word; width:180">
																				<?
																				echo $ycount . ', ' . $yarn_composition_arr[$febricDesc] . ', ' . $yarn_lot . ', ' . $brand_details[$row['brand_id']]; ?>
																			</div>
																		</td>
																		<td style="font-size: 15px" title="Yarn Dtls:<? echo $row['detarmination_id']; ?>">
																			<div style="word-wrap:break-word; width:180">
																				<?
																				echo $int_ref_arr[$job]["grouping"]; ?>
																			</div>
																		</td>
																		<td style="font-size: 15px" title="Yarn Dtls:<? echo $row['detarmination_id']; ?>">
																			<div style="word-wrap:break-word; width:180">
																				<?
																				echo $all_color_name; ?>
																			</div>
																		</td>
																		<td style="font-size: 15px">
																			<div style="word-wrap:break-word; width:80px">
																			</div>
																		</td>
																		<td style="font-size: 15px">
																			<div style="word-wrap:break-word; width:80px">
																			</div>
																		</td>
																		<td style="font-size: 15px; text-align: center;">
																			<div style="word-wrap:break-word; width:60px">
																				<? echo $dia . ' & ' . $gsm; ?>
																			</div>
																		</td>
																		<td style="font-size: 15px">
																			<div style="word-wrap:break-word; width:65px;text-align: center;"><? echo $machine_dia . 'X' . $machine_gg; ?></div>
																		</td>
																		<td style="font-size: 15px; text-align: center;">
																			<div style="word-wrap:break-word; width:60px"><? echo $row['stitch_length']; ?></div>
																		</td>
																		<td style="font-size: 15px" align="right" style="font-size: 15px;" title='<? echo $row['barcode_no'];?>'><? echo number_format($row['issue_qty'], 2, '.', ''); ?></td>
																		<td style="font-size: 15px" align="right" style="font-size: 15px;"><?
																																			if ($row['issue_qty_pcs'] == "") {
																																				echo 0;
																																			} else {
																																				echo $row['issue_qty_pcs'];
																																			} ?>
																		</td>
																		<td style="font-size: 15px" align="right"><? echo $row['num_of_roll']; ?></td>
																		<td style="font-size: 15px" align="right">
																			<div style="word-wrap:break-word; width:60px"><? echo number_format($row['reject_qnty'], 2, '.', ''); ?></div>
																		</td>
																	</tr>
									<?
																	$i++;
																	$sub_tot_qty_fabric += $row['issue_qty'];
																	$sub_total_issue_qty_pcs_fabric += $row['issue_qty_pcs'];
																	$sub_total_no_of_roll_fabric += $row['num_of_roll'];
																	$sub_total_reject_qnty_fabric += $row['reject_qnty'];

																	$sub_tot_qty += $row['issue_qty'];
																	$sub_total_issue_qty_pcs_qnty += $row['issue_qty_pcs'];
																	$sub_total_no_of_roll += $row['num_of_roll'];
																	$sub_total_reject_qnty += $row['reject_qnty'];

																	$grnd_tot_qty += $row['issue_qty'];
																	$grnd_total_issue_qty_pcs_qnty += $row['issue_qty_pcs'];
																	$grnd_total_no_of_roll += $row['num_of_roll'];
																	$grnd_total_reject_qnty += $row['reject_qnty'];
																}
															}
														}
													}
												}
											}
										}
									}
									?>
									<tr class="tbl_bottom">
										<td colspan="11" style=" text-align:right;font-size: 14px;"><strong>Fabric Type Total</strong></td>
										<td align="right" style="font-size: 14px;">
											<b><? echo number_format($sub_tot_qty_fabric, 2, '.', ''); ?></b>
										</td>
										<td align="right" style="font-size: 14px;"><? echo $sub_total_issue_qty_pcs_fabric; ?></td>
										<td align="right" style="font-size: 14px;"><? echo number_format($sub_total_no_of_roll_fabric, 2, '.', ''); ?></td>
										<td align="right" style="font-size: 14px;"><? echo number_format($sub_total_reject_qnty_fabric, 2, '.', ''); ?></td>
									</tr>
								<?
								}
								$job_total = $k++;
								// echo "<pre>";print_r($refno_data_array);
								if ($row['booking_without_order'] == 0) {
								?>
									<tr class="tbl_bottom">
										<td colspan="11" style=" text-align:right;font-size: 14px;"><strong>Job Total</strong></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($sub_tot_qty, 2); ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $sub_total_issue_qty_pcs_qnty; ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($sub_total_no_of_roll, 2); ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($sub_total_reject_qnty, 2); ?></td>
									</tr>
							<?
								}
							}

							?>
							<tr class="tbl_bottom">

								<td style="font-size: 16px;" colspan="2" align="center"><b>Total Job:
										<?php
										/*if(!empty($jobCountArr))
									{
		                           		echo " ".count($jobCountArr);
									}*/
										echo $job_total;
										?></b></td>
								<td align="right"></td>
								<td align="right" style="font-size: 16px;" colspan="8"><strong>Grand Total</strong></td>
								<td align="center" style="font-weight: bold; font-size: 16px;"><? echo number_format($grnd_tot_qty, 2, '.', ''); ?></td>
								<td align="right" style="font-size: 16px;"><strong><? echo $grnd_total_issue_qty_pcs_qnty; ?></strong></td>
								<td align="right" style="font-size: 16px;"><strong><? echo number_format($grnd_total_no_of_roll, 2, '.', ''); ?></strong></td>
								<td align="right" style="font-size: 16px;"><strong><? echo number_format($grnd_total_reject_qnty, 2, '.', ''); ?></strong></td>
							</tr>
						</tbody>
					</table>
					<br>
					<!-- =========== Collar and Cuff Details Start ============= -->
					<?
					//echo '<pre>';print_r($coller_cuff_data_arr);
					$CoCu = 1;
					foreach ($coller_cuff_data_arr as $coll_cuff_id => $booking_data_arr) {
						if (count($booking_data_arr) > 0) {
					?>
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left; margin-bottom:10px;">
								<thead bgcolor="#dddddd">
									<tr>
										<th colspan="3"><? echo $body_part_data_arr[$coll_cuff_id]['body_part_full_name']; ?> Details</th>
									</tr>
									<tr>
										<th>Size</th>
										<th>Qty Pcs</th>
										<th>No. of Roll</th>
									</tr>
								</thead>
								<?
								$coller_cuff_qty_total = $coller_cuff_roll_total = 0;
								foreach ($booking_data_arr as $bookingId => $bookingData) {
									foreach ($bookingData as $jobId => $jobData) {
										foreach ($jobData as $size => $row) {
								?>
											<tr>
												<td align="center"><? echo $size; ?></td>
												<td align="center"><? echo $row['qc_pass_qnty_pcs']; ?></td>
												<td align="center"><? echo $row['no_of_roll']; ?></td>
											</tr>
								<?
											$coller_cuff_qty_total += $row['qc_pass_qnty_pcs'];
											$coller_cuff_roll_total += $row['no_of_roll'];
										}
									}
								}
								?>
								<tr>
									<td align="right"><b>Total</b></td>
									<td align="center"><b><? echo $coller_cuff_qty_total; ?></b></td>
									<td align="center"><b><? echo $coller_cuff_roll_total; ?></b></td>
								</tr>
							</table>
					<?
							if ($CoCu == 1) {
								echo "<table width=\"30%\" style=\"float:left\"><tr><td colspan=\"3\">&nbsp;</td></tr></table>";
							}
							$CoCu++;
						}
					}
					?>
					<!-- =========== Collar and Cuff Details End ============= -->

					<!-- ============= Gate Pass Info Start ========= -->
					<table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
						<tr>
							<td colspan="15" height="30" style="border-left:hidden;border-right:hidden; text-align: center;">For mishandling or other reason no claim is acceptable in any stage, once the Goods is received in good condition and quality and out from factory premises.</td>
						</tr>
						<tr>
							<td colspan="4" align="center" valign="middle" style="font-size:25px;"><strong>&lt;&lt;Gate Pass&gt;&gt;</strong></td>
							<td colspan="9" align="center" valign="middle" id="gate_pass_barcode_img_id_<?php echo $x; ?>" height="50"></td>
						</tr>
						<tr>
							<td colspan="2" title="<? echo $system_no; ?>"><strong>From Company:</strong></td>
							<td colspan="2" width="120"><?php echo $gatePassDataArr[$system_no]['from_company']; ?></td>

							<td colspan="2"><strong>To Company:</strong></td>
							<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['to_company']; ?></td>

							<td colspan="3"><strong>Carried By:</strong></td>
							<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['carried_by']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>From Location:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['from_location']; ?></td>
							<td colspan="2"><strong>To Location:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['to_location']; ?></td>
							<td colspan="3"><strong>Driver Name:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_name']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Gate Pass ID:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_id']; ?></td>
							<td colspan="2" rowspan="2"><strong>Delivery Qnty</strong></td>
							<td align="center"><strong>Kg</strong></td>
							<td align="center"><strong>Roll</td>
							<td align="center"><strong>PCS</td>
							<td colspan="3"><strong>Vehicle Number:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['vhicle_number']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Gate Pass Date:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_date']; ?></td>
							<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_kg']; ?></td>
							<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_bag']; ?></td>
							<td align="center"><?php
												if ($gatePassDataArr[$system_no]['gate_pass_id'] != "") {
													if ($grnd_total_issue_qty_pcs_qnty > 0) {
														echo $grnd_total_issue_qty_pcs_qnty;
													}
												}
												?></td>
							<td colspan="3"><strong>Driver License No.:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_license_no']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Out Date:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_date']; ?></td>
							<td colspan="2"><strong>Dept. Name:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['department']; ?></td>
							<td colspan="3"><strong>Mobile No.:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['mobile_no']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Out Time:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_time']; ?></td>
							<td colspan="2"><strong>Attention:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['attention']; ?></td>
							<td colspan="3"><strong>Sequrity Lock No.:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['security_lock_no']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Returnable:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['returnable']; ?></td>
							<td colspan="2"><strong>Purpose:</strong></td>
							<td colspan="9"><?php echo $gatePassDataArr[$system_no]['issue_purpose']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Est. Return Date:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['est_return_date']; ?></td>
							<td colspan="2"><strong>Remarks:</strong></td>
							<td colspan="9"><?php echo $gatePassDataArr[$system_no]['remarks']; ?></td>
						</tr>
					</table>
					<!-- ============= Gate Pass Info End =========== -->
				</div>
				<br>
				<? echo signature_table(124, $company, "1200px"); ?>
			</div>
		</div>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {
				var zs = '<?php echo $x; ?>';
				var value = valuess; //$("#barcodeValue").val();
				var btype = 'code39'; //$("input[name=btype]:checked").val();
				var renderer = 'bmp'; // $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#barcode_img_id_" + zs).html('11');
				value = {
					code: value,
					rect: false
				};
				$("#barcode_img_id_" + zs).show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $data[1]; ?>');

			//for gate pass barcode
			function generateBarcodeGatePass(valuess) {
				var zs = '<?php echo $x; ?>';
				var value = valuess; //$("#barcodeValue").val();
				var btype = 'code39'; //$("input[name=btype]:checked").val();
				var renderer = 'bmp'; // $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#gate_pass_barcode_img_id_" + zs).html('11');
				value = {
					code: value,
					rect: false
				};
				$("#gate_pass_barcode_img_id_" + zs).show().barcode(value, btype, settings);
			}

			if ('<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>' != '') {
				generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
			}
		</script>
		<div style="page-break-after:always;"></div>
	<?php
	}
	exit();
}

// This print report only for Charka, if any kind of change for others customer please contact with Charka concern person
if ($action == "roll_issue_no_of_copy_print_charka") // Print 3, created by Tipu
{
	extract($_REQUEST);
	echo load_html_head_contents("Roll Wise Grey Issue", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);
	//var_dump($data);
	$company   		= $data[0];
	$system_no 		= $data[1];
	$report_title 	= $data[2];
	$mst_id     	= $data[3];
	$knit_source    = $data[4];
	$no_copy 		= $data[5];
	$dyeing_company = $data[6];
	$dyeing_source  = $data[7];

	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	//$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$body_part_type = return_library_array("select id, body_part_type from lib_body_part where status_active=1", 'id', 'body_part_type');
	$composition_arr = array();
	$yarn_composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " :: " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . " :: " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}

		if (array_key_exists($row[csf('id')], $yarn_composition_arr)) {
			$yarn_composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]];
		} else {
			$yarn_composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]];
		}
	}



	$store_location_id = return_field_value("location_id", "lib_store_location", "id=$store_id and is_deleted=0", "location_id");
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$country_name_arr = return_library_array("select id, country_name from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name");


	//for buyer
	$sqlBuyer = sql_select("select id as ID, buyer_name as BUYER_NAME, short_name as SHORT_NAME from lib_buyer");
	foreach ($sqlBuyer as $row) {
		$buyer_arr[$row['ID']] = $row['SHORT_NAME'];
		$buyer_dtls_arr[$row['ID']] = $row['BUYER_NAME'];
	}
	unset($sqlBuyer);

	$company_info = sql_select("select ID, COMPANY_NAME, PLOT_NO, ROAD_NO, CITY, CONTACT_NO, COUNTRY_ID from lib_company where status_active=1 and is_deleted=0 order by company_name");
	foreach ($company_info as $row) {
		$company_library[$row['ID']] = $row['COMPANY_NAME'];
		$company_address_arr[$row['ID']] = 'Plot No:' . $row['PLOT_NO'] . ', Road No:' . $row['ROAD_NO'] . ', City / Town:' . $row['CITY'] . ', Country:' . $country_name_arr[$row['COUNTRY_ID']] . ', Contact No:' . $row['CONTACT_NO'];
	}
	unset($company_info);

	//for supplier
	$sqlSupplier = sql_select("select id as ID, supplier_name as SUPPLIER_NAME, short_name as SHORT_NAME, ADDRESS_1 from lib_supplier where id=$dyeing_company");
	foreach ($sqlSupplier as $row) {
		$supplier_arr[$row['ID']] = $row['SHORT_NAME'];
		$supplier_dtls_arr[$row['ID']] = $row['SUPPLIER_NAME'];
		$supplier_address_arr[$row['ID']] = $row['ADDRESS_1'];
	}
	unset($sqlSupplier);

	//for gate pass
	$sql_get_pass = "SELECT a.ID, a.SYS_NUMBER, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.DRIVER_NAME,a.DRIVER_LICENSE_NO,a.SECURITY_LOCK_NO, b.QUANTITY, b.NO_OF_BAGS FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b WHERE a.id = b.mst_id AND a.company_id = " . $company . " AND a.basis = 3 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.challan_no LIKE '" . $system_no . "%'";
	// echo $sql_get_pass;
	$sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach ($sql_get_pass_rslt as $row) {
		$exp = explode(',', $row['CHALLAN_NO']);
		// echo "<pre>"; print_r($exp);
		foreach ($exp as $key => $val) {
			if ($val == $system_no) {
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];

				$row['OUT_DATE'] = ($row['OUT_DATE'] != '' ? date('d-m-Y', strtotime($row['OUT_DATE'])) : '');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE'] != '' ? date('d-m-Y', strtotime($row['EST_RETURN_DATE'])) : '');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE'] != '' ? date('d-m-Y', strtotime($row['EST_RETURN_DATE'])) : '');

				if ($row['WITHIN_GROUP'] == 1) {
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					$row['LOCATION_NAME'] = $location_arr[$row['LOCATION_ID']];
				}

				//for gate pass info
				$gatePassDataArr[$val]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$val]['from_company'] = $company_library[$row['COMPANY_ID']];
				$gatePassDataArr[$val]['from_location'] = $location_arr[$row['COM_LOCATION_ID']];
				$gatePassDataArr[$val]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$val]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$val]['est_return_date'] = $row['EST_RETURN_DATE'];

				$gatePassDataArr[$val]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$val]['to_location'] = $row['LOCATION_NAME'];
				$gatePassDataArr[$val]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$val]['delivery_bag'] += $row['NO_OF_BAGS'];

				$gatePassDataArr[$val]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				$gatePassDataArr[$val]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$val]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$val]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$val]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$val]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$val]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$val]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$val]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
				$gatePassDataArr[$val]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
			}
		}
	}
	// echo "<pre>";print_r($gatePassDataArr);

	//for gate out
	if ($gate_pass_id != '') {
		$sql_gate_out = "SELECT OUT_DATE, OUT_TIME FROM INV_GATE_OUT_SCAN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND INV_GATE_PASS_MST_ID='" . $gate_pass_id . "'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if (!empty($sql_gate_out_rslt)) {
			foreach ($sql_gate_out_rslt as $row) {
				$is_gate_out = 1;
				$gatePassDataArr[$system_no]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$system_no]['out_time'] = $row['OUT_TIME'];
			}
		}
	}

	// Issue master data
	$dataArray = sql_select("SELECT issue_number, issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no, remarks, attention from inv_issue_master where id=$mst_id");
	$reqBookingNoArr = array();
	foreach ($dataArray as $row) {
		$issue_number = $row[csf('issue_number')];
		$issue_date = $row[csf('issue_date')];
		// $knit_dye_company = $row[csf('knit_dye_company')];
		$knit_dye_source = $row[csf('knit_dye_source')];
		$issue_purpose = $yarn_issue_purpose[$row[csf('issue_purpose')]];
		$attention = $row[csf('attention')];
		$remarks = $row[csf('remarks')];

		//for issue to
		$knit_dye_company = '';
		if ($row[csf('knit_dye_source')] == 1)
			$knit_dye_company = $company_library[$row[csf('knit_dye_company')]];
		else
			$knit_dye_company = $supplier_dtls_arr[$row[csf('knit_dye_company')]];

		$knit_dye_company_address = '';
		if ($row[csf('knit_dye_source')] == 1)
			$knit_dye_company_address = $company_address_arr[$row[csf('knit_dye_company')]];
		else
			$knit_dye_company_address = $supplier_address_arr[$row[csf('knit_dye_company')]];
	}

	// Roll issue
	$issue_res = sql_select("SELECT a.barcode_no, sum(a.qnty) as issue_qty, a.po_breakdown_id, a.booking_without_order,a.is_sales  from pro_roll_details a where a.entry_form=61 and a.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 group by a.barcode_no, a.po_breakdown_id, a.booking_without_order,a.is_sales ");
	$issued_data_arr = $po_ref_arr = $sales_id_arr = $non_order_booking_arr = array();
	foreach ($issue_res as $val) {
		$barcode_nums .= $val[csf("barcode_no")] . ",";
		$qntyFromRoll[$val[csf("barcode_no")]] = $val[csf("issue_qty")];
		$issued_data_arr[$val[csf('barcode_no')]]['po_id'] = $val[csf('po_breakdown_id')];
		$issued_data_arr[$val[csf('barcode_no')]]['is_sales'] = $val[csf('is_sales')];
		$issued_data_arr[$val[csf('barcode_no')]]['booking_without_order'] = $val[csf('booking_without_order')];
		if ($val[csf('is_sales')] == 0) //
		{
			if ($val[csf('booking_without_order')] == 1) {
				$non_order_booking_arr[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
			} else {
				$po_ref_arr[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
			}
		} else  // sales order
		{
			$sales_id_arr[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
		}
	}
	// echo "<pre>";print_r($issued_data_arr);die;
	$barcode_nums = chop($barcode_nums, ",");

	// order and with order booking
	$job_array = $job_company_arr = $sales_arr = $sales_booking_arr = $booking_arr = array();
	if (!empty($po_ref_arr)) {
		$job_sql = "SELECT  b.id, a.job_no_prefix_num, a.job_no,b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping as ref_no, a.buyer_name, a.sustainability_standard, a.fab_material, c.booking_no ,b.grouping
		from wo_po_details_master a, wo_po_break_down b left join wo_booking_dtls c on c.po_break_down_id=b.id
		where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in (1,3) and b.is_deleted=0 and c.booking_type in(1) and c.is_short=2 and b.id in(" . implode(",", $po_ref_arr) . ")
		group by b.id, a.job_no_prefix_num, a.job_no, b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping, a.buyer_name, a.sustainability_standard, a.fab_material, c.booking_no, b.grouping";
		$job_sql_result = sql_select($job_sql); // is_short=2 main fabric booking
		foreach ($job_sql_result as $row) {
			$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
			$job_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
			$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
			$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
			$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
			$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			$job_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
			$job_array[$row[csf("id")]]["sustainability_std_id"] = $row[csf("sustainability_standard")];
			$job_array[$row[csf("id")]]["fabric_material_id"] = $row[csf("fab_material")];
			$job_company_arr[$row[csf('job_no_prefix_num')]]['company_name'] = $row[csf('company_name')];
			$job_array[$row[csf("id")]]["booking_no"] = $row[csf("booking_no")];
			$booking_arr[$row[csf("booking_no")]]["booking_ref_no"] = $row[csf("ref_no")];

			//$int_ref_arr[$row[csf('job_no')]]["grouping"] = $row[csf("grouping")];
		}
	}


	$int_ref_sql = "select internal_ref, job_no from wo_order_entry_internal_ref";

	$int_ref = sql_select($int_ref_sql);

	foreach ($int_ref as $row) {
		$int_ref_arr[$row[csf('job_no')]]["grouping"] = $row[csf("internal_ref")];
	}
	// Sales order
	if (!empty($sales_id_arr)) {
		$sales_details = sql_select("SELECT id, job_no, sales_booking_no, within_group, style_ref_no, buyer_id, po_buyer, po_job_no from fabric_sales_order_mst where id in(" . implode(",", $sales_id_arr) . ")");
		foreach ($sales_details as $sales_row) {
			$sales_arr[$sales_row[csf('id')]]['sales_booking_no'] = $sales_row[csf('sales_booking_no')];
			$sales_arr[$sales_row[csf('id')]]['within_group'] = $sales_row[csf('within_group')];
			$sales_arr[$sales_row[csf('id')]]['style_ref_no'] = $sales_row[csf('style_ref_no')];
			$sales_arr[$sales_row[csf('id')]]['buyer_id'] = $sales_row[csf('buyer_id')];
			$sales_arr[$sales_row[csf('id')]]['po_buyer'] = $sales_row[csf('po_buyer')];
			$sales_arr[$sales_row[csf('id')]]['po_job_no'] = $sales_row[csf('po_job_no')];
			$sales_booking_arr[] = "'" . $sales_row[csf('sales_booking_no')] . "'";
		}
	}

	// Non-order booking
	$non_order_booking_sql = sql_select("SELECT a.id as booking_id, a.booking_no,a.buyer_id,a.grouping, b.style_id
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
	where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.id in(" . implode(",", $non_order_booking_arr) . ") group by a.id, a.booking_no,a.buyer_id,a.grouping, b.style_id");
	$nonOrderBookingData_arr = array();
	foreach ($non_order_booking_sql as $row) {
		$style_id = $row[csf("style_id")];
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['booking_no'] = $row[csf('booking_no')];
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['buyer_id'] = $row[csf('buyer_id')];
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['grouping'] = $row[csf('grouping')];
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['sustainability_std_id'] = return_field_value("sustainability_std_id", "sample_development_mst", "id=$style_id");
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['fabric_material_id'] = return_field_value("fabric_material_id", "sample_development_mst", "id=$style_id");
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['style_id'] = return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
	}
	// $nonOrderBookingStyle=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");

	// Knitting Production
	$sql = "SELECT a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as issue_qty,count(c.barcode_no) as num_of_roll, sum(c.reject_qnty) as reject_qnty, sum(c.qc_pass_qnty_pcs) as issue_qty_pcs, b.body_part_id, b.prod_id, d.detarmination_id, c.barcode_no,  c.po_breakdown_id
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d
	where a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums)
	group by  a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no, c.booking_without_order, c.is_sales, b.body_part_id, b.prod_id, d.detarmination_id, c.barcode_no, c.po_breakdown_id
	order by a.booking_no";
	// echo $sql;die;
	$sql_result = sql_select($sql);
	$booking_program_arr = array();
	foreach ($sql_result as $row) {
		if ($row[csf('receive_basis')] == 2) {
			$booking_program_arr[] = $row[csf("booking_no")];
			$productionYarnCount[$row[csf("booking_no")]] = $row[csf("yarn_count")];
		} else {
			$booking_no = explode("-", $row[csf('booking_no')]);
			$booking_program_arr[] = (int)$booking_no[3];
		}
	}
	//print_r($booking_program_arr);
	$planOrder = sql_select("SELECT a.id, b.booking_no, b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(" . implode(",", $booking_program_arr) . ")");
	$plan_arr = array();
	foreach ($planOrder as $plan_row) {
		$plan_arr[$plan_row[csf("id")]]["machine_dia"] = $plan_row[csf("machine_dia")];
		$plan_arr[$plan_row[csf("id")]]["feeder"] = $plan_row[csf("feeder")];
		$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
	}

	$refno_data_array = array();

	foreach ($sql_result as $row) {

		$po_id = $issued_data_arr[$row[csf('barcode_no')]]['po_id'];
		$booking_without_order = $issued_data_arr[$row[csf('barcode_no')]]['booking_without_order'];

		$is_sales = $row[csf('is_sales')];
		if ($is_sales == 1 || $is_sales == 2) {
			$within_group = $sales_arr[$po_id]['within_group'];
			if ($within_group == 1) {
				$style_ref_no = "";
				$sales_booking = $sales_arr[$po_id]['sales_booking_no'];

				//$po=$sales_arr[$po_id]['sales_order'];
				$job_no = $job_array[$sales_booking]['job_no'];
				//$buyer_id=$job_array[$sales_booking]['buyer_name'];

				$po = $sales_arr[$po_id]['sales_order'];
				$buyer_id = $sales_arr[$po_id]['buyer_name'];
				$booking_no = $sales_arr[$po_id]['sales_booking_no'];
				$poJobNo = $sales_arr[$po_id]['po_job_no'];
				$poBuyer = $sales_arr[$po_id]['po_buyer'];
				$salesBooking = $sales_arr[$po_id]['sales_booking_no'];
				$salesStyle = $sales_arr[$po_id]['style_ref_no'];
			} else {
				$job_no = "";
				$style_ref_no = "";
				$ref_no = "";
				$po = $sales_arr[$po_id]['sales_order'];
				$buyer_id = $sales_arr[$po_id]['buyer_name'];
				$booking_no = $sales_arr[$po_id]['sales_booking_no'];
			}
		} else {
			if ($booking_without_order == 1) {
				$ref_no = '';
				$job_no = '';
				$po = '';
				$booking_no = $nonOrderBookingData_arr[$po_id]["booking_no"];
				$buyer_id = $nonOrderBookingData_arr[$po_id]["buyer_id"];
				$style_ref_no = $nonOrderBookingData_arr[$po_id]["style_id"];
				$sustainability = $nonOrderBookingData_arr[$po_id]["sustainability_std_id"];
				$material = $nonOrderBookingData_arr[$po_id]["fabric_material_id"];
			} else {
				$ref_no = $job_array[$po_id]['ref_no'];
				$job_no = $job_array[$po_id]['job_no'];
				$style_ref_no = $job_array[$po_id]['style_ref_no'];
				$po = $job_array[$po_id]['po'];
				$buyer_id = $job_array[$po_id]['buyer_name'];
				$booking_no = $job_array[$po_id]["booking_no"];
				$sustainability = $job_array[$po_id]["sustainability_std_id"];
				$material = $job_array[$po_id]["fabric_material_id"];
			}
		}
		// echo $booking_no;

		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['job_no'] = $job_no;
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['buyer_id'] = $buyer_id;
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['style_ref_no'] = $style_ref_no;
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['booking_no'] = $booking_no;
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['sustainability'] = $sustainability;
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['material'] = $material;

		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['body_part_id'] = $row[csf('body_part_id')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['detarmination_id'] = $row[csf('detarmination_id')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['color_id'] .= $row[csf('color_id')] . ',';
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['color_range_id'] = $row[csf('color_range_id')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['brand_id'] = $row[csf('brand_id')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['stitch_length'] = $row[csf('stitch_length')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['issue_qty'] += $row[csf('issue_qty')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['issue_qty_pcs'] += $row[csf('issue_qty_pcs')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['reject_qnty'] += $row[csf('reject_qnty')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['num_of_roll'] += $row[csf('num_of_roll')];
	}
	// echo "<pre>"; print_r($refno_data_array);die;

	$colarCupArr = sql_select("SELECT id, body_part_type, body_part_full_name from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(40,50)");
	foreach ($colarCupArr as $row) {
		$body_part_data_arr[$row[csf('id')]]['body_part_full_name'] = $row[csf('body_part_full_name')];
		$body_part_data_arr[$row[csf('id')]]['body_part_type'] = $row[csf('body_part_type')];
	}

	// For Coller and Cuff data
	$sql_coller_cuff = "SELECT a.receive_basis, a.booking_no, b.body_part_id, c.booking_no as bwo, c.coller_cuff_size, c.qnty, c.qc_pass_qnty_pcs, c.barcode_no
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums) order by b.body_part_id, c.coller_cuff_size";
	// echo $sql_coller_cuff;
	$sql_coller_cuff_result = sql_select($sql_coller_cuff);
	foreach ($sql_coller_cuff_result as $row2) {
		if ($body_part_data_arr[$row2[csf('body_part_id')]]['body_part_type'] > 0 && $row2[csf('qc_pass_qnty_pcs')] > 0) {
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qnty'] += $row2[csf('qnty')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty_pcs'] += $row2[csf('qc_pass_qnty_pcs')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['receive_basis'] = $row2[csf('receive_basis')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['no_of_roll'] += count($row2[csf('barcode_no')]);
		}
	}
	$com_dtls = fnc_company_location_address($company, $store_location_id, 2);
	?>
	<style type="text/css">
		table tr td {
			font-size: 16px;
		}

		.rpt_table thead th {
			font-size: 16px;
		}

		.rpt_table tfoot th {
			font-size: 16px;
		}
	</style>
	<?php
	$data_array = sql_select("select image_location  from common_photo_library where master_tble_id='" . $data[0] . "' and form_name='company_details' and is_deleted=0 and file_type=1");
	$lib_store_name = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");


	$data_store_transfer_sql = sql_select("select a.store_id from inv_transaction a,pro_roll_details b where a.mst_id=b.mst_id and b.entry_form=82 and a.item_category=13 and a.transaction_type=5 and b.barcode_no in($barcode_nums)
	group by a.store_id");
	$sotreNameTransfer = $sotreName = $transferBarcode = "";
	foreach ($data_store_transfer_sql as $row) {
		$transferBarcode .= $row[csf('store_id')] . ",";
		$sotreNameTransfer .= $lib_store_name[$row[csf('store_id')]] . ",";
	}
	$transferBarcode = chop($transferBarcode, ",");
	$sotreNameTransfer = chop($sotreNameTransfer, ",");


	if ($transferBarcode != "") {
		$transferBarcodeCond = "and b.barcode_no not in($transferBarcode)";
	}

	$data_store_sql = sql_select("select a.store_id from inv_transaction a,pro_roll_details b where a.mst_id=b.mst_id and b.entry_form=58 and a.item_category=13 and a.transaction_type=1 and b.barcode_no in($barcode_nums) $transferBarcodeCond
	group by a.store_id");

	foreach ($data_store_sql as $row) {
		$sotreName .= $lib_store_name[$row[csf('store_id')]] . ",";
	}

	$sotreName = chop($sotreName, ",");
	$allStore = $sotreNameTransfer . $sotreName;


	$noOfCopy = "";
	for ($x = 1; $x <= $no_copy; $x++) {
		if ($x == 1) {
			$sup = 'st';
		} else if ($x == 2) {
			$sup = 'nd';
		} else if ($x == 3) {
			$sup = 'rd';
		} else {
			$sup = 'th';
		}

		$noOfCopy = "<span style='font-size:x-large;font-weight:bold'>" . $x . "<sup>" . $sup . "</sup> Copy</span>";
	?>

		<div style="width:1240px;">
			<table width="1240" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
				<tr>
					<td align="left" width="50">
						<?
						foreach ($data_array as $img_row) {
						?>
							<img src='../../../<? echo $com_dtls[2]; ?>' height='50' width='50' align="middle" />
						<?
						}
						?>
					</td>
					<td align="center" style="font-size:30px" colspan="3"><strong><? echo $com_dtls[0] . "<br><span style=\"font-size:14px;\">" . $com_dtls[1] . "</span>"; ?></strong></td>
					<td width="110" align="right"><?php echo $noOfCopy . ($is_gate_pass == 1 ? "<br><span style=\"color:#F00;font-weight:bold;\">Gate Pass Done</span>" : '') . ($is_gate_out == 1 ? "<br><span style=\"color:#F00;font-weight:bold;\">Gate Out Done</span>" : ''); ?></td>
				</tr>
				<tr>
					<td colspan="3" align="center" height="50" valign="middle" style="font-size:25px">
						<strong>Greige Fabric Delivery Challan</strong>
						<?php
						if ($data[4] == 1) {
						?>
							<!-- <span style="color:#0F0; font-weight:bold;">[Approved]</span> -->
						<?php
						}
						?>
					</td>
				</tr>
			</table>
			<div style="width:100%;">
				<div style="clear:both;">
					<table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
						<tr>
							<td width="125"><strong>Dyeing Company:</strong></td>
							<td width="250px"><? echo $knit_dye_company; ?></td>
							<td width="125"><strong>Attention:</strong></td>
							<td width="150px"><? echo $attention; ?></td>
							<td width="130"><strong>Delivery Challan No:</strong></td>
							<td width="130"><? echo $issue_number; ?></td>
						</tr>
						<tr>
							<td><strong>Address:</strong></td>
							<td><? echo $knit_dye_company_address; ?></td>
							<td><strong>Issue Purpose:</strong></td>
							<td><? echo $issue_purpose; ?></td>
							<td><strong>Delivery Date:</strong></td>
							<td><? echo change_date_format($issue_date); ?></td>
						</tr>
						<tr>
							<td><strong>Source:</strong></td>
							<td><? echo $knitting_source[$knit_dye_source]; ?></td>
							<td><strong>Store Name:</strong></td>
							<td><? echo $allStore; ?></td>
							<td><strong>Remarks:</strong></td>
							<td><? echo $location_arr[$inhouse_location]; ?></td>
						</tr>
						<tr>
							<td align="center" colspan="6" id="barcode_img_id_<?php echo $x; ?>" height="50" valign="middle" style="border-left:hidden;border-right:hidden;border-bottom:hidden;"></td>
						</tr>
					</table>

					<table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
						<thead bgcolor="#dddddd">

							<tr>
								<th rowspan="2" width="20">SL</th>
								<th rowspan="2" width="120">Buyer, Job, <br>Style and<br>Booking</th>
								<th rowspan="2" width="45">Body Part</th>
								<th rowspan="2" width="210">Fabric Details</th>
								<th rowspan="2" width="65">Color Range</th>
								<th rowspan="2" width="180">Yarn Details</th>
								<th rowspan="2" width="180">Internal Ref.</th>
								<th rowspan="2" width="180">Fabric Color</th>
								<th rowspan="2" width="80">Service Work Order</th>
								<th rowspan="2" width="80">Batch No</th>
								<th rowspan="2" width="60">Fab. Dia<br>& GSM</th>
								<th rowspan="2" width="50">MC DIA <br /> X <br /> M.GAUGE</th>
								<th rowspan="2" width="60">S.L</th>
								<th colspan="2" width="120">Delivery Qty</th>
								<th rowspan="2" width="80">Roll Qty</th>
								<th rowspan="2">Reject Qty</th>
							</tr>
							<tr>
								<th width="60">KG</th>
								<th width="60">PCS</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = 1;
							$k = 1;
							$sub_group_arr = array();
							//$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]]['num_of_roll']+=$row[csf('num_of_roll')];
							$fab_material = array(1 => "Organic", 2 => "BCI");
							foreach ($refno_data_array as $job => $jobArr) {
								$sub_tot_qty = 0;
								$sub_total_no_of_roll = 0;
								foreach ($jobArr as $febricDesc => $febricDescDataArr) {
									$sub_total_no_of_roll_fabric = $sub_tot_qty_fabric = 0;
									foreach ($febricDescDataArr as $yarn_lot => $yarn_lotArr) {
										foreach ($yarn_lotArr as $yarn_count => $yarn_countArr) {
											foreach ($yarn_countArr as $machine_dia => $machine_diaArr) {
												foreach ($machine_diaArr as $machine_gg => $machine_ggArr) {
													foreach ($machine_ggArr as $gsm => $gsmArr) {
														foreach ($gsmArr as $dia => $diaArr) {
															foreach ($diaArr as $color_ids => $color_ids_Data) {
																foreach ($color_ids_Data as $stitch_length => $row) {
																	if ($i % 2 == 0)
																		$bgcolor = "#E9F3FF";
																	else
																		$bgcolor = "#FFFFFF";

																	$ycount = '';
																	$yarn_count = explode(",", $yarn_count);
																	foreach ($yarn_count as $count_id) {
																		if ($ycount == '') $ycount = $yarn_count_details[$count_id];
																		else $ycount .= "," . $yarn_count_details[$count_id];
																	}

							?>
																	<tr bgcolor="<? echo $bgcolor; ?>">
																		<td style="font-size: 15px"><? echo $i; ?></td>
																		<td style="font-size: 15px">
																			<div style="word-wrap:break-word; width:130px"><?
																															/*if($knit_dye_source==3 && $dyeing_source==0)
																			{
																				$buyer_info = 'WHS';
																				$style_info = 'WHS';
																			}
																			else
																			{
																				echo "string";
																				$buyer_info = $buyer_array[$row['buyer_id']];
																				$style_info = $row['style_ref_no'];
																			}*/
																															//echo $buyer_info.' ::<br>'.$job.' ::<br>'.$style_info.' ::<br>'.$row['booking_no'].' ::<br>'.$sustainability_standard[$row['sustainability']].' ::'.$fab_material[$row['material']];
																															echo $buyer_array[$poBuyer] . ' ::<br>' . $poJobNo . ' ::<br>' . $salesStyle . ' ::<br>' . $salesBooking . ' ::<br>' . $sustainability_standard[$row['sustainability']] . ' ::' . $fab_material[$row['material']];
																															?>
																			</div>
																		</td>
																		<td style="font-size: 15px">
																			<div style="word-wrap:break-word; width:45px"><? echo $body_part[$row['body_part_id']]; ?></div>
																		</td>
																		<td style="font-size: 15px" title="<? echo $febricDesc; ?>">
																			<div style="word-wrap:break-word; width:210px">
																				<?
																				$color_id_arr = array_unique(explode(",", $row["color_id"]));
																				$all_color_name = "";
																				foreach ($color_id_arr as $c_id) {
																					$all_color_name .= $color_arr[$c_id] . ",";
																				}
																				$all_color_name = chop($all_color_name, ",");
																				echo $all_color_name . ' :: ' . $composition_arr[$febricDesc]; ?>
																			</div>
																		</td>
																		<td style="font-size: 15px">
																			<div style="word-wrap:break-word; width:65px"><? echo $color_range[$row["color_range_id"]]; ?></div>
																		</td>
																		<td style="font-size: 15px" title="Yarn Dtls:<? echo $row['detarmination_id']; ?>">
																			<div style="word-wrap:break-word; width:180">
																				<?
																				echo $ycount . ', ' . $yarn_composition_arr[$febricDesc] . ', ' . $yarn_lot . ', ' . $brand_details[$row['brand_id']]; ?>
																			</div>
																		</td>
																		<td style="font-size: 15px" title="Yarn Dtls:<? echo $row['detarmination_id']; ?>">
																			<div style="word-wrap:break-word; width:180">
																				<?
																				echo $int_ref_arr[$job]["grouping"]; ?>
																			</div>
																		</td>
																		<td style="font-size: 15px" title="Yarn Dtls:<? echo $row['detarmination_id']; ?>">
																			<div style="word-wrap:break-word; width:180">
																				<?
																				echo $all_color_name; ?>
																			</div>
																		</td>
																		<td style="font-size: 15px">
																			<div style="word-wrap:break-word; width:80px">
																			</div>
																		</td>
																		<td style="font-size: 15px">
																			<div style="word-wrap:break-word; width:80px">
																			</div>
																		</td>
																		<td style="font-size: 15px; text-align: center;">
																			<div style="word-wrap:break-word; width:60px">
																				<? echo $dia . ' & ' . $gsm; ?>
																			</div>
																		</td>
																		<td style="font-size: 15px">
																			<div style="word-wrap:break-word; width:65px;text-align: center;"><? echo $machine_dia . 'X' . $machine_gg; ?></div>
																		</td>
																		<td style="font-size: 15px; text-align: center;">
																			<div style="word-wrap:break-word; width:60px"><? echo $row['stitch_length']; ?></div>
																		</td>
																		<td style="font-size: 15px" align="right" style="font-size: 15px;"><? echo number_format($row['issue_qty'], 2, '.', ''); ?></td>
																		<td style="font-size: 15px" align="right" style="font-size: 15px;"><?
																																			if ($row['issue_qty_pcs'] == "") {
																																				echo 0;
																																			} else {
																																				echo $row['issue_qty_pcs'];
																																			} ?>
																		</td>
																		<td style="font-size: 15px" align="right"><? echo $row['num_of_roll']; ?></td>
																		<td style="font-size: 15px" align="right">
																			<div style="word-wrap:break-word; width:60px"><? echo number_format($row['reject_qnty'], 2, '.', ''); ?></div>
																		</td>
																	</tr>
									<?
																	$i++;
																	$sub_tot_qty_fabric += $row['issue_qty'];
																	$sub_total_issue_qty_pcs_fabric += $row['issue_qty_pcs'];
																	$sub_total_no_of_roll_fabric += $row['num_of_roll'];
																	$sub_total_reject_qnty_fabric += $row['reject_qnty'];

																	$sub_tot_qty += $row['issue_qty'];
																	$sub_total_issue_qty_pcs_qnty += $row['issue_qty_pcs'];
																	$sub_total_no_of_roll += $row['num_of_roll'];
																	$sub_total_reject_qnty += $row['reject_qnty'];

																	$grnd_tot_qty += $row['issue_qty'];
																	$grnd_total_issue_qty_pcs_qnty += $row['issue_qty_pcs'];
																	$grnd_total_no_of_roll += $row['num_of_roll'];
																	$grnd_total_reject_qnty += $row['reject_qnty'];
																}
															}
														}
													}
												}
											}
										}
									}
									?>
									<tr class="tbl_bottom">
										<td colspan="11" style=" text-align:right;font-size: 14px;"><strong>Fabric Type Total</strong></td>
										<td align="right" style="font-size: 14px;">
											<b><? echo number_format($sub_tot_qty_fabric, 2, '.', ''); ?></b>
										</td>
										<td align="right" style="font-size: 14px;"><? echo $sub_total_issue_qty_pcs_fabric; ?></td>
										<td align="right" style="font-size: 14px;"><? echo number_format($sub_total_no_of_roll_fabric, 2, '.', ''); ?></td>
										<td align="right" style="font-size: 14px;"><? echo number_format($sub_total_reject_qnty_fabric, 2, '.', ''); ?></td>
									</tr>
								<?
								}
								$job_total = $k++;
								// echo "<pre>";print_r($refno_data_array);
								if ($row['booking_without_order'] == 0) {
								?>
									<tr class="tbl_bottom">
										<td colspan="11" style=" text-align:right;font-size: 14px;"><strong>Job Total</strong></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($sub_tot_qty, 2); ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $sub_total_issue_qty_pcs_qnty; ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($sub_total_no_of_roll, 2); ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($sub_total_reject_qnty, 2); ?></td>
									</tr>
							<?
								}
							}

							?>
							<tr class="tbl_bottom">

								<td style="font-size: 16px;" colspan="2" align="center"><b>Total Job:
										<?php
										/*if(!empty($jobCountArr))
									{
		                           		echo " ".count($jobCountArr);
									}*/
										echo $job_total;
										?></b></td>
								<td align="right"></td>
								<td align="right" style="font-size: 16px;" colspan="8"><strong>Grand Total</strong></td>
								<td align="center" style="font-weight: bold; font-size: 16px;"><? echo number_format($grnd_tot_qty, 2, '.', ''); ?></td>
								<td align="right" style="font-size: 16px;"><strong><? echo $grnd_total_issue_qty_pcs_qnty; ?></strong></td>
								<td align="right" style="font-size: 16px;"><strong><? echo number_format($grnd_total_no_of_roll, 2, '.', ''); ?></strong></td>
								<td align="right" style="font-size: 16px;"><strong><? echo number_format($grnd_total_reject_qnty, 2, '.', ''); ?></strong></td>
							</tr>
						</tbody>
					</table>
					<br>
					<!-- =========== Collar and Cuff Details Start ============= -->
					<?
					//echo '<pre>';print_r($coller_cuff_data_arr);
					$CoCu = 1;
					foreach ($coller_cuff_data_arr as $coll_cuff_id => $booking_data_arr) {
						if (count($booking_data_arr) > 0) {
					?>
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left; margin-bottom:10px;">
								<thead bgcolor="#dddddd">
									<tr>
										<th colspan="3"><? echo $body_part_data_arr[$coll_cuff_id]['body_part_full_name']; ?> Details</th>
									</tr>
									<tr>
										<th>Size</th>
										<th>Qty Pcs</th>
										<th>No. of Roll</th>
									</tr>
								</thead>
								<?
								$coller_cuff_qty_total = $coller_cuff_roll_total = 0;
								foreach ($booking_data_arr as $bookingId => $bookingData) {
									foreach ($bookingData as $jobId => $jobData) {
										foreach ($jobData as $size => $row) {
								?>
											<tr>
												<td align="center"><? echo $size; ?></td>
												<td align="center"><? echo $row['qc_pass_qnty_pcs']; ?></td>
												<td align="center"><? echo $row['no_of_roll']; ?></td>
											</tr>
								<?
											$coller_cuff_qty_total += $row['qc_pass_qnty_pcs'];
											$coller_cuff_roll_total += $row['no_of_roll'];
										}
									}
								}
								?>
								<tr>
									<td align="right"><b>Total</b></td>
									<td align="center"><b><? echo $coller_cuff_qty_total; ?></b></td>
									<td align="center"><b><? echo $coller_cuff_roll_total; ?></b></td>
								</tr>
							</table>
					<?
							if ($CoCu == 1) {
								echo "<table width=\"30%\" style=\"float:left\"><tr><td colspan=\"3\">&nbsp;</td></tr></table>";
							}
							$CoCu++;
						}
					}
					?>
					<!-- =========== Collar and Cuff Details End ============= -->

					<!-- ============= Gate Pass Info Start ========= -->
					<table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
						<tr>
							<td colspan="15" height="30" style="border-left:hidden;border-right:hidden; text-align: center;">For mishandling or other reason no claim is acceptable in any stage, once the Goods is received in good condition and quality and out from factory premises.</td>
						</tr>
						<tr>
							<td colspan="4" align="center" valign="middle" style="font-size:25px;"><strong>&lt;&lt;Gate Pass&gt;&gt;</strong></td>
							<td colspan="9" align="center" valign="middle" id="gate_pass_barcode_img_id_<?php echo $x; ?>" height="50"></td>
						</tr>
						<tr>
							<td colspan="2" title="<? echo $system_no; ?>"><strong>From Company:</strong></td>
							<td colspan="2" width="120"><?php echo $gatePassDataArr[$system_no]['from_company']; ?></td>

							<td colspan="2"><strong>To Company:</strong></td>
							<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['to_company']; ?></td>

							<td colspan="3"><strong>Carried By:</strong></td>
							<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['carried_by']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>From Location:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['from_location']; ?></td>
							<td colspan="2"><strong>To Location:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['to_location']; ?></td>
							<td colspan="3"><strong>Driver Name:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_name']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Gate Pass ID:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_id']; ?></td>
							<td colspan="2" rowspan="2"><strong>Delivery Qnty</strong></td>
							<td align="center"><strong>Kg</strong></td>
							<td align="center"><strong>Roll</td>
							<td align="center"><strong>PCS</td>
							<td colspan="3"><strong>Vehicle Number:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['vhicle_number']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Gate Pass Date:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_date']; ?></td>
							<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_kg']; ?></td>
							<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_bag']; ?></td>
							<td align="center"><?php
												if ($gatePassDataArr[$system_no]['gate_pass_id'] != "") {
													if ($grnd_total_issue_qty_pcs_qnty > 0) {
														echo $grnd_total_issue_qty_pcs_qnty;
													}
												}
												?></td>
							<td colspan="3"><strong>Driver License No.:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_license_no']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Out Date:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_date']; ?></td>
							<td colspan="2"><strong>Dept. Name:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['department']; ?></td>
							<td colspan="3"><strong>Mobile No.:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['mobile_no']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Out Time:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_time']; ?></td>
							<td colspan="2"><strong>Attention:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['attention']; ?></td>
							<td colspan="3"><strong>Sequrity Lock No.:</strong></td>
							<td colspan="3"><?php echo $gatePassDataArr[$system_no]['security_lock_no']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Returnable:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['returnable']; ?></td>
							<td colspan="2"><strong>Purpose:</strong></td>
							<td colspan="9"><?php echo $gatePassDataArr[$system_no]['issue_purpose']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><strong>Est. Return Date:</strong></td>
							<td colspan="2"><?php echo $gatePassDataArr[$system_no]['est_return_date']; ?></td>
							<td colspan="2"><strong>Remarks:</strong></td>
							<td colspan="9"><?php echo $gatePassDataArr[$system_no]['remarks']; ?></td>
						</tr>
					</table>
					<!-- ============= Gate Pass Info End =========== -->
				</div>
				<br>
				<? echo signature_table(124, $company, "1200px"); ?>
			</div>
		</div>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {
				var zs = '<?php echo $x; ?>';
				var value = valuess; //$("#barcodeValue").val();
				var btype = 'code39'; //$("input[name=btype]:checked").val();
				var renderer = 'bmp'; // $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#barcode_img_id_" + zs).html('11');
				value = {
					code: value,
					rect: false
				};
				$("#barcode_img_id_" + zs).show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $data[1]; ?>');

			//for gate pass barcode
			function generateBarcodeGatePass(valuess) {
				var zs = '<?php echo $x; ?>';
				var value = valuess; //$("#barcodeValue").val();
				var btype = 'code39'; //$("input[name=btype]:checked").val();
				var renderer = 'bmp'; // $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#gate_pass_barcode_img_id_" + zs).html('11');
				value = {
					code: value,
					rect: false
				};
				$("#gate_pass_barcode_img_id_" + zs).show().barcode(value, btype, settings);
			}

			if ('<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>' != '') {
				generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
			}
		</script>
		<div style="page-break-after:always;"></div>
	<?php
	}
	exit();
}

if ($action == "fabric_details_print_bpkw_tg1") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];

	$company_array = get_company_array();
	$color_arr = get_color_array();
	$supplier_arr = get_supplier_array();
	$buyer_array = get_buyer_array();
	$yarn_count_details = get_yarn_count_array();
	$brand_arr = get_brand_array();
	$country_arr = get_country_array();
	$batch_arr = get_batch_array();

	$booking_arr = return_library_array("select id, booking_no_prefix_num from wo_booking_mst where item_category in(2,13)", "id", "booking_no_prefix_num");
	$pi_arr = return_library_array("select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");
	$production_arr = return_library_array("select id, sys_number_prefix_num from pro_grey_prod_delivery_mst where entry_form=56", "id", "sys_number_prefix_num");

	$company_address_arr = array();
	$company_data = sql_select("select id, company_name, company_short_name, plot_no, road_no, city, contact_no, country_id from lib_company where status_active=1 and is_deleted=0");
	foreach ($company_data as $row) {
		$company_address_arr[$row[csf('id')]] = 'Plot No:' . $row[csf('plot_no')] . ', Road No:' . $row[csf('road_no')] . ', City / Town:' . $row[csf('city')] . ', Country:' . $country_name_arr[$row[csf('country_id')]] . ', Contact No:' . $row[csf('contact_no')];
	}

	/*
	|--------------------------------------------------------------------------
	| for floor, room, rack and shelf disable
	|--------------------------------------------------------------------------
	|
	*/
	$floorRoomRackShelf_array = return_library_array("SELECT floor_room_rack_id, floor_room_rack_name FROM lib_floor_room_rack_mst WHERE company_id=" . $company . "", "floor_room_rack_id", "floor_room_rack_name");

	$dataArray = sql_select("select issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no,remarks from inv_issue_master where id=$update_id");

	$get_roll_po_id = sql_select("select po_breakdown_id,is_sales from pro_roll_details where mst_id=$update_id");
	$poIDS = "";
	foreach ($get_roll_po_id as $row) {
		if ($row[csf("is_sales")] == 1) {
			$all_fso_id .= $row[csf("po_breakdown_id")] . ",";
		} else {
			$poIDS .= $row[csf('po_breakdown_id')] . ",";
		}
	}

	$poIDS = implode(",", array_unique(explode(",", chop($poIDS, ","))));
	$all_fso_id = implode(",", array_unique(explode(",", chop($all_fso_id, ","))));

	/*$job_array=array();
	$job_sql="select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
	}*/
	if ($all_fso_id != "") {
		$data_array = sql_select("SELECT id as po_id,job_no, sales_booking_no, buyer_id,within_group,style_ref_no FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and id in($all_fso_id)");
		$sales_arr = array();
		$sales_booking_arr = array();
		foreach ($data_array as $row) {
			$sales_arr[$row[csf("po_id")]]['sales_order'] = $row[csf("job_no")];
			$sales_arr[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_id")];
			$sales_arr[$row[csf("po_id")]]['sales_booking_no'] = $row[csf("sales_booking_no")];
			$sales_arr[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
			$sales_arr[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$sales_booking_arr[] = "'" . $row[csf('sales_booking_no')] . "'";
		}
	}

	if (!empty($sales_booking_arr)) {
		$booking_cond = " and a.booking_no in (" . implode(",", $sales_booking_arr) . ")";

		$booking_details = sql_select("SELECT a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping ref_no from wo_booking_mst a,wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and b.booking_type in(1,4) $booking_cond group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping");

		foreach ($booking_details as $booking_row) {
			$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
			$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
			$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
			$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
			$booking_arr[$booking_row[csf("booking_no")]]["int_ref"] = $booking_row[csf("ref_no")];
			$booking_arr[$booking_row[csf("po_break_down_id")]]["grouping"] = $booking_row[csf("ref_no")];
		}
	}

	if ($poIDS != "") {
		$data_array = sql_select("select b.id, a.style_ref_no, a.job_no, a.buyer_name,b.file_no,b.po_number, b.grouping,c.booking_no from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and b.id in($poIDS)");
		$po_details_arr = array();
		$job_array = array();
		foreach ($data_array as $row) {
			$po_details_arr[$row[csf("booking_no")]]['job_no'] = $row[csf("job_no")];
			$po_details_arr[$row[csf("booking_no")]]['buyer_name'] = $row[csf("buyer_name")];
			$po_details_arr[$row[csf("booking_no")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$po_details_arr[$row[csf("booking_no")]]['file_no'] = $row[csf("file_no")];
			$po_details_arr[$row[csf("booking_no")]]['grouping'] = $row[csf("grouping")];

			$job_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
			$job_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
			$job_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
		}
	}

	$product_array = array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach ($product_sql as $row) {
		$product_array[$row[csf("id")]]['gsm'] = $row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width'] = $row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id'] = $row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom'] = $row[csf("unit_of_measure")];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$grey_issue_basis = array(0 => "Independent", 1 => "PI", 2 => "Booking", 3 => "Knitting Plan", 9 => "Delivery");
	$sql_barcode = "SELECT  b.barcode_no from inv_grey_fabric_issue_dtls a, pro_roll_details b					where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1	and b.is_deleted=0";
	foreach (sql_select($sql_barcode) as $key => $value) {
		$baroce_arrs[$value[csf("barcode_no")]] = $value[csf("barcode_no")];
	}

	if ($db_type == 0) {
		$po_id = return_field_value("group_concat(po_breakdown_id) as po_id", "inv_grey_fabric_issue_dtls a, order_wise_pro_details b", "a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and po_breakdown_id in($poIDS)", "po_id");
	} else {
		$po_id = return_field_value("LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id", "inv_grey_fabric_issue_dtls a, order_wise_pro_details b", "a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($poIDS)", "po_id");
	}
	// echo $po_id;
	$sales_data = sql_select("select po_breakdown_id,mst_id,is_sales,barcode_no from pro_roll_details where mst_id=$update_id and entry_form=61 and status_active=1 and is_deleted=0");
	$sales_data_arr = array();
	foreach ($sales_data as $row) {
		$sales_data_arr[$row[csf('po_breakdown_id')]]['is_sales'] = $row[csf('is_sales')];
	}
	$po_exp = array_unique(explode(',', $po_id));
	$all_po_string = implode(",", $po_exp);
	$po_no = '';
	$job = '';
	$style_ref = '';
	$all_buyer = "";
	foreach ($po_exp as $id) {
		$is_sales = $sales_data_arr[$id]['is_sales'];
		$sales_booking = $sales_arr[$id]['sales_booking_no'];
		$within_group = $sales_arr[$id]['within_group'];
		if ($is_sales == 1) {
			if ($within_group == 1) {
				if ($po_no == '') $po_no = $sales_arr[$id]['sales_order'];
				else $po_no .= ', ' . $sales_arr[$id]['sales_order'];
				if ($job == '') $job = $po_details_arr[$sales_booking]['job_no'];
				else $job .= ',' . $po_details_arr[$sales_booking]['job_no'];
				if ($style_ref == '') $style_ref = $po_details_arr[$sales_booking]['style_ref_no'];
				else $style_ref .= ',' . $po_details_arr[$sales_booking]['style_ref_no'];
				$all_buyer .= $buyer_array[$po_details_arr[$sales_booking]['buyer_name']] . ",";
			} else {
				if ($po_no == '') $po_no = $sales_arr[$id]['sales_order'];
				else $po_no .= ', ' . $sales_arr[$id]['sales_order'];
				if ($job == '') $job = "";
				if ($style_ref == '') $style_ref = $sales_arr[$id]['style_ref_no'];
				else $style_ref .= ',' . $sales_arr[$id]['style_ref_no'];
				$all_buyer .= $buyer_array[$sales_arr[$id]['buyer_name']] . ",";
			}
		} else {
			if ($po_no == '') $po_no = $job_array[$id]['po'];
			else $po_no .= ', ' . $job_array[$id]['po'];
			if ($job == '') $job = $job_array[$id]['job'];
			else $job .= ',' . $job_array[$id]['job'];
			if ($style_ref == '') $style_ref = $job_array[$id]['style_ref'];
			else $style_ref .= ',' . $job_array[$id]['style_ref'];
			$all_buyer .= $buyer_array[$job_array[$id]['buyer_name']] . ",";
		}
	}
	$job = implode(",", array_unique(explode(',', $job)));
	$style_ref = implode(",", array_unique(explode(',', $style_ref)));
	$po_no = implode(",", array_unique(explode(',', $po_no)));
	$all_buyer = implode(",", array_unique(explode(',', chop($all_buyer, ","))));
	if ($all_po_string != "") {
		$all_barcode_id = implode(",", $baroce_arrs);
		$knit_prod_rcv_sql = sql_select("select b.barcode_no, a.machine_dia, a.machine_gg, a.body_part_id from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.roll_id=0 and b.barcode_no in($all_barcode_id)");
		$production_rcv_data = array();
		foreach ($knit_prod_rcv_sql as $row) {
			$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
			$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
			$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] = $body_part[$row[csf("body_part_id")]];
		}
	} else {
		$knit_prod_rcv_sql = sql_select("select b.barcode_no, a.machine_dia, a.machine_gg, a.body_part_id from pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.roll_id=0 and booking_without_order=1");
		$production_rcv_data = array();
		foreach ($knit_prod_rcv_sql as $row) {
			$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
			$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
			$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] = $body_part[$row[csf("body_part_id")]];
		}
		$sam_buyer_sql = sql_select("select a.buyer_id from  wo_non_ord_samp_booking_mst a, pro_roll_details b where a.id=b.po_breakdown_id and b.booking_without_order=1 and b.entry_form=61 and b.mst_id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($sam_buyer_sql as $row) {
			$all_buyer .= $buyer_array[$row[csf('buyer_id')]] . ",";
		}
		$all_buyer = implode(",", array_unique(explode(',', chop($all_buyer, ","))));
	}

	?>
	<div>
		<table width="1800" cellspacing="0">
			<tr>
				<td colspan="9" align="center" style="font-size:22px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="9" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="9" align="center" style="font-size:18px"><strong><u><? echo $data[3]; ?> Grey Fabric Roll Issue Challan</u></strong></td>
			</tr>
			<tr>
				<td width="150" style="font-size:16px"><strong>Challan No</strong> </td>
				<td width="10"><strong>:</strong></td>
				<td width="310" style="font-size:18px; font-weight:bold;"><? echo $txt_issue_no; ?></td>
				<td width="40"></td>
				<td width="180" style="font-size:16px; font-weight:bold;"><strong>Dyeing Company</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="290px" style="font-size:18px; font-weight:bold;">
					<?
					$knit_dye_company_address = '';
					if ($dataArray[0][csf('knit_dye_source')] == 1)
						$knit_dye_company_address = $company_address_arr[$dataArray[0][csf('knit_dye_company')]];
					else
						$knit_dye_company_address = $supplier_address_arr[$dataArray[0][csf('knit_dye_company')]];

					if ($dataArray[0][csf('knit_dye_source')] == 1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name'];
					else if ($dataArray[0][csf('knit_dye_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]]; ?>
				</td>

				<td width="150"><strong>Issue Date</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="290px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td valign="top"><strong>Address</strong></td>
				<td valign="top"><strong>:</strong></td>
				<td valign="top"><? echo $knit_dye_company_address; ?></td>
				<td width="40"></td>
				<td valign="top"><strong>Dyeing Source</strong></td>
				<td valign="top"><strong>:</strong></td>
				<td valign="top"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>

				<td valign="top"><strong>Issue Purpose</strong></td>
				<td valign="top"><strong>:</strong></td>
				<td valign="top"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
			</tr>
			<tr>
				<td valign="top"><strong>Batch Number</strong></td>
				<td valign="top"><strong>:</strong></td>
				<td valign="top"><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
				<td width="40"></td>
				<td valign="top"><strong>Remarks</strong></td>
				<td valign="top"><strong>:</strong></td>
				<td valign="top" colspan="1" width="175px"><? echo $dataArray[0][csf('remarks')]; ?></td>
				<td colspan="4" id="barcode_img_id"></td>
			</tr>
			<tr>

			</tr>
			<tr>
				<td colspan="4"></td>
				<td colspan="5" id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1800" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center">
				<th width="20">SL</th>
				<th width="50">Basis</th>
				<th width="80">Deli. /<br> Book/<br> PI No</th>
				<th width="100">Booking No/<br>Internal reff</th>
				<th width="110">Body Part</th>
				<th width="250">Item Description</th>
				<th width="250">Color</th>
				<th width="50">Stich Length</th>
				<th width="40">GSM</th>
				<th width="40">Fin. Dia</th>
				<th width="60">M/C Dia</th>
				<th width="60">M/C Gauge</th>
				<th width="40">No of Roll</th>
				<th width="40">UOM</th>
				<th width="80">Count</th>
				<th width="80">Brand</th>
				<th width="80">Yarn Lot</th>
				<th width="80">Size</th>
				<th width="50">Qty. In Pcs</th>
				<th>Issue Qty</th>
			</thead>
			<?
			//$roll_arr=return_library_array( "select id, roll_no from pro_roll_details where entry_form in(2,22)", "id", "roll_no");
			$i = 1;
			$tot_qty = 0;
			$tot_qtyInPcs = 0;
			$program_arr = array();
			$program_sql = "select a.id,b.booking_no from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id = b.id and a.status_active=1 and a.is_deleted=0";
			$program_result = sql_select($program_sql);
			foreach ($program_result as $program_row) {
				$program_arr[$program_row[csf("id")]]["program_no"] = $program_row[csf("id")];
				$program_arr[$program_row[csf("id")]]["booking_no"] = $program_row[csf("booking_no")];
			}

			$bookingBarNo_sql = sql_select("select barcode_no, receive_basis from pro_roll_details where entry_form=2 and is_deleted=0 and status_active=1 and po_breakdown_id in($poIDS) ");
			foreach ($bookingBarNo_sql as $row) {
				$bookingBar_data[$row[csf("barcode_no")]]["receive_basis"] = $row[csf("receive_basis")];
			}
			// echo "<pre>";
			//print_r($bookingBar_data);

			if ($db_type == 0) {
				$sql_data = "SELECT a.basis, group_concat(a.program_no) as program_no,group_concat(b.booking_no) as booking_no, a.prod_id, sum(b.qnty) as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self, a.bin_box, a.brand_id, count(b.roll_id) as no_of_roll, group_concat(b.barcode_no) as barcode_no, sum(b.qc_pass_qnty_pcs) as qty_in_pcs,d.sys_number_prefix_num as delivery_prefix 
                from inv_grey_fabric_issue_dtls a, pro_roll_details b left join pro_roll_details c on b.barcode_no=c.barcode_no  and c.entry_form=56 left join pro_grey_prod_delivery_mst d on c.mst_id=d.id
                where a.id=b.dtls_id and a.mst_id=$update_id and b.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                group by b.booking_no,a.basis, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self, a.bin_box, a.brand_id,d.sys_number_prefix_num";
			} else {
				$sql_data = "SELECT a.basis, a.program_no, a.prod_id, sum(b.qnty) as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self, a.bin_box, count(b.roll_id) as no_of_roll, sum(b.qc_pass_qnty_pcs) as qty_in_pcs, a.brand_id,b.barcode_no,b.booking_no, b.po_breakdown_id,b.booking_without_order, b.is_sales
                from inv_grey_fabric_issue_dtls a, pro_roll_details b
                where a.id=b.dtls_id and a.mst_id=$update_id and b.mst_id=$update_id and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                group by b.booking_no, a.basis, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.floor_id, a.room, a.rack, a.self, a.bin_box,b.po_breakdown_id,b.booking_without_order,a.program_no,a.brand_id,b.barcode_no,b.booking_no, b.is_sales ";
			}
			// echo $sql_data;
			$booking_with_order_ids = "";
			$booking_without_order_ids = "";
			$barcodeNos = "";
			$fso_booking_with_order_ids = "";
			$result_data = sql_select($sql_data);
			foreach ($result_data as $row) {
				$barcodeNos .= $row[csf("barcode_no")] . ",";

				if ($row[csf("booking_without_order")] == 1) {
					$booking_without_order_ids .= $row[csf("po_breakdown_id")] . ",";
				} else if ($row[csf("is_sales")] == 1) {
					$fso_booking_with_order_ids .= $row[csf("po_breakdown_id")] . ",";
				} else {
					$booking_with_order_ids .= $row[csf("po_breakdown_id")] . ",";
				}
			}
			$barcodeNos = chop($barcodeNos, ",");
			$booking_with_order_ids = implode(",", array_unique(array_filter(explode(",", chop($booking_with_order_ids, ",")))));
			$fso_booking_with_order_ids = implode(",", array_unique(array_filter(explode(",", chop($fso_booking_with_order_ids, ",")))));
			$booking_without_order_ids = implode(",", array_unique(array_filter(explode(",", chop($booking_without_order_ids, ",")))));

			$sql_chk_trnsf = sql_select("select a.barcode_no   from PRO_ROLL_DETAILS a, INV_ITEM_TRANSFER_MST b
			where a.mst_id=b.id and a.barcode_no in($barcodeNos)
			and a.entry_form in(82,83) and b.transfer_criteria in(1,4)
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach ($sql_chk_trnsf as $rows) {
				$trnsf_chk_arr[$rows[csf("barcode_no")]] = $rows[csf("barcode_no")];
			}

			$sql_booking_withorder = sql_select("select po_break_down_id,booking_no from wo_booking_dtls  where po_break_down_id in($booking_with_order_ids) and status_active=1 and is_deleted=0 and booking_type=1");
			foreach ($sql_booking_withorder as $rows) {
				$booking_data_arr[$rows[csf("po_break_down_id")]] = $rows[csf("booking_no")];
			}

			$sql_fso_booking_withorder = sql_select("SELECT id,sales_booking_no from fabric_sales_order_mst  where id in($fso_booking_with_order_ids) and status_active=1 and is_deleted=0");
			foreach ($sql_fso_booking_withorder as $rows) {
				$booking_data_arr[$rows[csf("id")]] = $rows[csf("sales_booking_no")];
			}

			$sql_booking_without_order = sql_select("select id,booking_no from wo_non_ord_samp_booking_mst  where id in($booking_without_order_ids) and status_active=1 and is_deleted=0");
			foreach ($sql_booking_without_order as $rows) {
				$booking_data_arr[$rows[csf("id")]] = $rows[csf("booking_no")];
			}

			$sql_delivery = sql_select("select a.barcode_no,b.sys_number_prefix_num as delivery_prefix from pro_roll_details a,pro_grey_prod_delivery_mst b where a.mst_id=b.id and a.barcode_no in($barcodeNos) and a.entry_form=56 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach ($sql_delivery as $row) {
				$delivery_data_arr[$row[csf("barcode_no")]] = $row[csf("delivery_prefix")];
			}
			$sql_production = sql_select("SELECT a.barcode_no,b.booking_no as program_no,c.machine_dia,c.machine_gg ,c.body_part_id ,e.booking_no, a.coller_cuff_size
			from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c,ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e
			where a.mst_id=b.id  and a.dtls_id=c.id and b.id=c.mst_id and b.booking_id=d.id and d.mst_id=e.id and a.barcode_no in($barcodeNos) and b.receive_basis=2 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			union all
			select a.barcode_no,null as program_no,c.machine_dia,c.machine_gg ,c.body_part_id ,b.booking_no, a.coller_cuff_size
			from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c
			where a.mst_id=b.id  and a.dtls_id=c.id and b.id=c.mst_id and a.barcode_no in($barcodeNos) and b.receive_basis=1 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

			foreach ($sql_production as $row) {
				$production_data_arr[$row[csf("barcode_no")]] = $row[csf("program_no")];

				$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
				$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
				$production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] = $body_part[$row[csf("body_part_id")]];
				$production_rcv_data[$row[csf("barcode_no")]]["progBooking"] = $row[csf("booking_no")];
				$production_rcv_data[$row[csf("barcode_no")]]["coller_cuff_size"] = $row[csf("coller_cuff_size")];

				// a.coller_cuff_size
			}

			$booking_number = "";
			$int_ref = "";
			foreach ($result_data as $row) {
				if ($row[csf("booking_without_order")] == 0) {
					if ($trnsf_chk_arr[$row[csf("barcode_no")]]) {
						$booking_number = $booking_data_arr[$row[csf("po_breakdown_id")]];
					} else if ($row[csf("is_sales")] == 1) {
						$booking_number = $booking_data_arr[$row[csf("po_breakdown_id")]];
						$int_ref = $booking_arr[$booking_number]['int_ref'];
					} else {
						$booking_number = $production_rcv_data[$row[csf("barcode_no")]]["progBooking"];
					}
				} else {
					$booking_number = $booking_data_arr[$row[csf("po_breakdown_id")]];
				}
				$data_arr_string = $row[csf("basis")] . "*" . $production_data_arr[$row[csf("barcode_no")]] . "*" . $booking_number . "*" . $delivery_data_arr[$row[csf("barcode_no")]] . "*" . $row[csf("prod_id")] . "*" . $row[csf("color_id")] . "*" . $row[csf("stitch_length")] . "*" . $row[csf("yarn_lot")] . "*" . $row[csf("yarn_count")] . "*" . $row[csf("floor_id")] . "*" . $row[csf("room")] . "*" . $row[csf("rack")] . "*" . $row[csf("self")] . "*" . $row[csf("bin_box")] . "*" . $int_ref ;

				$main_arr[$data_arr_string]['brand'] .= $row[csf("brand_id")] . ",";

				$main_arr[$data_arr_string]['issue_qnty'] += $row[csf("issue_qnty")];
				$main_arr[$data_arr_string]['no_of_roll'] += $row[csf("no_of_roll")];
				$main_arr[$data_arr_string]['qty_in_pcs'] += $row[csf("qty_in_pcs")];

				$main_arr[$data_arr_string]['machine_dia'] .= $production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] . ",";
				$main_arr[$data_arr_string]['machine_gg'] .= $production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] . ",";
				$main_arr[$data_arr_string]['body_part_id'] .= $production_rcv_data[$row[csf("barcode_no")]]["body_part_id"] . ",";
				$main_arr[$data_arr_string]['coller_cuff_size'] .= $production_rcv_data[$row[csf("barcode_no")]]["coller_cuff_size"] . ",";

			}

			foreach ($main_arr as $stringData => $row) {
				$stringDataArr = explode("*", $stringData);
				$basis = $stringDataArr[0];
				$pi_book_plan = $stringDataArr[3];
				$booking_no = $stringDataArr[2];
				$programNO = $stringDataArr[1];
				$intRef = $stringDataArr[14];
				// $size= $stringDataArr[15];
				$bookingAndRef = $booking_no . "<br />" . $intRef;
				$fabric_des = $composition_arr[$product_array[$stringDataArr[4]]['deter_id']];
				$body_part_name = $row["body_part_id"];
				$body_part_name = implode(",", array_unique(array_filter(explode(",", chop($body_part_name, ",")))));
				$size_name = $row["coller_cuff_size"];
				$size_name = implode(",", array_unique(array_filter(explode(",", chop($size_name, ",")))));

				$stitch_length = $stringDataArr[6];
				$gsm = $product_array[$stringDataArr[4]]['gsm'];
				$fin_dia = $product_array[$stringDataArr[4]]['dia_width'];
				$machine_dia = implode(",", array_unique(array_filter(explode(",", chop($row["machine_dia"], ",")))));
				$machine_gg = implode(",", array_unique(array_filter(explode(",", chop($row["machine_gg"], ",")))));
				$color_id = array_unique(array_filter(explode(",", chop($stringDataArr[5], ","))));

				$color = '';
				foreach ($color_id as $val) {
					if ($color == "") {
						$color .= $color_arr[$val];
					} else {
						$color .= "," . $color_arr[$val];
					}
				}

				$no_of_roll = $row["no_of_roll"];
				$uom = $product_array[$stringDataArr[4]]['uom'];
				$brand_id = $row["brand"];

				$brand_id_arr = array_unique(explode(",", $brand_id));
				$brand = '';
				foreach ($brand_id_arr as $val) {
					if ($brand == "") {
						$brand .= $brand_arr[$val];
					} else {
						$brand .= "," . $brand_arr[$val];
					}
				}

				$count = '';
				$yarn_count = explode(",", $stringDataArr[8]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
				}


				$yarn_lot = $stringDataArr[7];

				$qty_in_pcs = $row["qty_in_pcs"];
				$issue_qnty = $row["issue_qnty"];
				// $floor=$floorRoomRackShelf_array[$stringDataArr[9]];
				// $room=$floorRoomRackShelf_array[$stringDataArr[10]];
				// $rack=$floorRoomRackShelf_array[$stringDataArr[11]];
				// $shelf=$floorRoomRackShelf_array[$stringDataArr[12]];
				// $bin=$floorRoomRackShelf_array[$stringDataArr[13]];

				$str_ref = $fabric_des . '*' . $stitch_length . '*' . $gsm . '*' . $fin_dia . '*' . $machine_dia . '*' . $machine_gg . '*' . $uom . '*' . $brand . '*' . $count . '*' . $yarn_lot. '*' . $size_name;
				$main_data_arr[$color][$body_part_name][$str_ref]['basis'] = $basis;
				$main_data_arr[$color][$body_part_name][$str_ref]['pi_book_plan'] = $pi_book_plan;
				$main_data_arr[$color][$body_part_name][$str_ref]['bookingAndRef'] = $bookingAndRef;
				$main_data_arr[$color][$body_part_name][$str_ref]['no_of_roll'] += $no_of_roll;
				$main_data_arr[$color][$body_part_name][$str_ref]['qty_in_pcs'] += $qty_in_pcs;
				$main_data_arr[$color][$body_part_name][$str_ref]['issue_qnty'] += $issue_qnty;

				/*$main_data_arr[$color][$body_part_name][$fabric_des]['basis']=$basis;
				$main_data_arr[$color][$body_part_name][$fabric_des]['pi_book_plan']=$pi_book_plan;
				$main_data_arr[$color][$body_part_name][$fabric_des]['bookingAndRef']=$bookingAndRef;
				$main_data_arr[$color][$body_part_name][$fabric_des]['stitch_length']=$stitch_length;
				$main_data_arr[$color][$body_part_name][$fabric_des]['gsm']=$gsm;
				$main_data_arr[$color][$body_part_name][$fabric_des]['fin_dia']=$fin_dia;
				$main_data_arr[$color][$body_part_name][$fabric_des]['machine_dia']=$machine_dia;
				$main_data_arr[$color][$body_part_name][$fabric_des]['machine_gg']=$machine_gg;
				$main_data_arr[$color][$body_part_name][$fabric_des]['no_of_roll']+=$no_of_roll;
				$main_data_arr[$color][$body_part_name][$fabric_des]['uom']=$uom;
				$main_data_arr[$color][$body_part_name][$fabric_des]['brand']=$brand;
				$main_data_arr[$color][$body_part_name][$fabric_des]['count']=$count;
				$main_data_arr[$color][$body_part_name][$fabric_des]['yarn_lot']=$yarn_lot;
				$main_data_arr[$color][$body_part_name][$fabric_des]['qty_in_pcs']+=$qty_in_pcs;
				$main_data_arr[$color][$body_part_name][$fabric_des]['issue_qnty']+=$issue_qnty;*/
			}

			// echo "<pre>";print_r($main_data_arr);
			$tot_qty = 0;
			$total_roll = 0;
			$tot_qtyInPcs = 0;
			foreach ($main_data_arr as $k_color => $v_color) {
				$color_tot_qty = 0;
				$color_total_roll = 0;
				$color_tot_qtyInPcs = 0;
				foreach ($v_color as $k_body_part => $v_body_part) {
					$body_tot_qty = 0;
					$body_total_roll = 0;
					$body_tot_qtyInPcs = 0;
					foreach ($v_body_part as $k_str_ref => $row) {
						$stringDataArr = explode("*", $k_str_ref);
						$fabric_des = $stringDataArr[0];
						$stitch_length = $stringDataArr[1];
						$gsm = $stringDataArr[2];
						$fin_dia = $stringDataArr[3];
						$machine_dia = $stringDataArr[4];
						$machine_gg = $stringDataArr[5];
						$uom = $stringDataArr[6];
						$brand = $stringDataArr[7];
						$count = $stringDataArr[8];
						$yarn_lot = $stringDataArr[9];
						$size_coller_cuff= $stringDataArr[10];
			?>
						<tr>
							<td><? echo $i; ?></td>
							<td><? echo $grey_issue_basis[$row['basis']]; ?></td>
							<td style="word-break:break-all;"><? echo $row['pi_book_plan']; ?></td>
							<td style="font-size:12px;"><? echo $row['bookingAndRef']; ?></td>
							<td style="word-break:break-all;"><? echo $k_body_part; ?></td>
							<td style="word-break:break-all;"><? echo $fabric_des; ?></td>
							<td style="word-break:break-all;"><? echo $k_color; ?></td>
							<td style="word-break:break-all;"><? echo $stitch_length; ?></td>
							<td style="word-break:break-all;"><? echo $gsm; ?></td>
							<td style="word-break:break-all;"><? echo $fin_dia; ?></td>
							<td style="word-break:break-all;"><? echo $machine_dia; ?></td>
							<td style="word-break:break-all;"><? echo $machine_gg; ?></td>
							<td style="word-break:break-all;" align="center"><? echo $row['no_of_roll']; ?></td>
							<td style="word-break:break-all;"><? echo $unit_of_measurement[$uom]; ?></td>
							<td style="word-break:break-all;" align="center"><? echo $count; ?></td>
							<td style="word-break:break-all;"><? echo $brand; ?></td>
							<td style="word-break:break-all;"><? echo $yarn_lot; ?></td>
							<td style="word-break:break-all;"><? echo $size_coller_cuff; ?></td>
							<td align="right"><? echo $row['qty_in_pcs']; ?></td>
							<td align="right"><? echo number_format($row['issue_qnty'], 2); ?></td>
						</tr>
					<?
						$body_tot_qty += $row['issue_qnty'];
						$body_total_roll += $row['no_of_roll'];
						$body_tot_qtyInPcs += $row['qty_in_pcs'];

						$color_tot_qty += $row['issue_qnty'];
						$color_total_roll += $row['no_of_roll'];
						$color_tot_qtyInPcs += $row['qty_in_pcs'];

						$tot_qty += $row['issue_qnty'];
						$total_roll += $row['no_of_roll'];
						$tot_qtyInPcs += $row['qty_in_pcs'];
						$i++;
					}
					?>
					<tr>
						<td align="right" colspan="12"><strong><?= $k_body_part; ?> Total</strong></td>
						<td align="center"><strong><? echo $body_total_roll; ?></strong></td>
						<td colspan="5"></td>
						<td align="right"><strong><? echo $body_tot_qtyInPcs; ?></strong></td>
						<td align="right"><strong><? echo number_format($body_tot_qty, 2, '.', ''); ?></strong></td>
					</tr>
				<?
				}
				?>
				<tr>
					<td align="right" colspan="12"><strong><?= $k_color; ?> Total</strong></td>
					<td align="center"><strong><? echo $color_total_roll; ?></strong></td>
					<td colspan="5"></td>
					<td align="right"><strong><? echo $color_tot_qtyInPcs; ?></strong></td>
					<td align="right"><strong><? echo number_format($color_tot_qty, 2, '.', ''); ?></strong></td>
				</tr>
			<?
			}

			?>
			<tr>
				<td align="right" colspan="12"><strong>Grand Total</strong></td>
				<td align="center"><strong><? echo $total_roll; ?></strong></td>
				<td colspan="5"></td>
				<td align="right"><strong><? echo $tot_qtyInPcs; ?></strong></td>
				<td align="right"><strong><? echo number_format($tot_qty, 2, '.', ''); ?></strong></td>
			</tr>

		</table>
	</div>
	<? echo signature_table(124, $company, "1470px", "", "40px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}
// This print report only for Cotton Club bd, if any kind of change for others customer please contact with Cotton Club bd concern person
if ($action == "roll_issue_no_of_copy_print_ccl_____") // Print 4
{
	extract($_REQUEST);
	echo load_html_head_contents("Roll Wise Grey Issue", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);
	//var_dump($data);
	$company   		= $data[0];
	$system_no 		= $data[1];
	$report_title 	= $data[2];
	$mst_id     	= $data[3];
	$knit_source    = $data[4];
	$no_copy 		= $data[5];
	$dyeing_company = $data[6];
	$dyeing_source  = $data[7];

	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$body_part_type = return_library_array("select id, body_part_type from lib_body_part where status_active=1", 'id', 'body_part_type');
	$floor_room_rack_array = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
	$composition_arr = array();
	$yarn_composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " :: " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . " :: " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}

		if (array_key_exists($row[csf('id')], $yarn_composition_arr)) {
			$yarn_composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]];
		} else {
			$yarn_composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]];
		}
	}



	$store_location_id = return_field_value("location_id", "lib_store_location", "id=$store_id and is_deleted=0", "location_id");
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$country_name_arr = return_library_array("select id, country_name from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name");


	//for buyer
	$sqlBuyer = sql_select("select id as ID, buyer_name as BUYER_NAME, short_name as SHORT_NAME from lib_buyer");
	foreach ($sqlBuyer as $row) {
		$buyer_arr[$row['ID']] = $row['SHORT_NAME'];
		$buyer_dtls_arr[$row['ID']] = $row['BUYER_NAME'];
	}
	unset($sqlBuyer);

	$company_info = sql_select("select ID, COMPANY_NAME, PLOT_NO, ROAD_NO, CITY, CONTACT_NO, COUNTRY_ID from lib_company where status_active=1 and is_deleted=0 order by company_name");
	foreach ($company_info as $row) {
		$company_library[$row['ID']] = $row['COMPANY_NAME'];
		$company_address_arr[$row['ID']] = 'Plot No:' . $row['PLOT_NO'] . ', Road No:' . $row['ROAD_NO'] . ', City / Town:' . $row['CITY'] . ', Country:' . $country_name_arr[$row['COUNTRY_ID']] . ', Contact No:' . $row['CONTACT_NO'];
	}
	unset($company_info);

	//for supplier
	$sqlSupplier = sql_select("select id as ID, supplier_name as SUPPLIER_NAME, short_name as SHORT_NAME, ADDRESS_1 from lib_supplier where id=$dyeing_company");
	foreach ($sqlSupplier as $row) {
		$supplier_arr[$row['ID']] = $row['SHORT_NAME'];
		$supplier_dtls_arr[$row['ID']] = $row['SUPPLIER_NAME'];
		$supplier_address_arr[$row['ID']] = $row['ADDRESS_1'];
	}
	unset($sqlSupplier);

	//for gate pass
	$sql_get_pass = "SELECT a.ID, a.SYS_NUMBER, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.DRIVER_NAME,a.DRIVER_LICENSE_NO,a.SECURITY_LOCK_NO, b.QUANTITY, b.NO_OF_BAGS FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b WHERE a.id = b.mst_id AND a.company_id = " . $company . " AND a.basis = 3 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.challan_no LIKE '" . $system_no . "%'";
	// echo $sql_get_pass;
	$sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach ($sql_get_pass_rslt as $row) {
		$exp = explode(',', $row['CHALLAN_NO']);
		// echo "<pre>"; print_r($exp);
		foreach ($exp as $key => $val) {
			if ($val == $system_no) {
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];

				$row['OUT_DATE'] = ($row['OUT_DATE'] != '' ? date('d-m-Y', strtotime($row['OUT_DATE'])) : '');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE'] != '' ? date('d-m-Y', strtotime($row['EST_RETURN_DATE'])) : '');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE'] != '' ? date('d-m-Y', strtotime($row['EST_RETURN_DATE'])) : '');

				if ($row['WITHIN_GROUP'] == 1) {
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					$row['LOCATION_NAME'] = $location_arr[$row['LOCATION_ID']];
				}

				//for gate pass info
				$gatePassDataArr[$val]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$val]['from_company'] = $company_library[$row['COMPANY_ID']];
				$gatePassDataArr[$val]['from_location'] = $location_arr[$row['COM_LOCATION_ID']];
				$gatePassDataArr[$val]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$val]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$val]['est_return_date'] = $row['EST_RETURN_DATE'];

				$gatePassDataArr[$val]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$val]['to_location'] = $row['LOCATION_NAME'];
				$gatePassDataArr[$val]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$val]['delivery_bag'] += $row['NO_OF_BAGS'];

				$gatePassDataArr[$val]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				$gatePassDataArr[$val]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$val]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$val]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$val]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$val]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$val]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$val]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$val]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
				$gatePassDataArr[$val]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
			}
		}
	}
	// echo "<pre>";print_r($gatePassDataArr);

	//for gate out
	if ($gate_pass_id != '') {
		$sql_gate_out = "SELECT OUT_DATE, OUT_TIME FROM INV_GATE_OUT_SCAN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND INV_GATE_PASS_MST_ID='" . $gate_pass_id . "'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if (!empty($sql_gate_out_rslt)) {
			foreach ($sql_gate_out_rslt as $row) {
				$is_gate_out = 1;
				$gatePassDataArr[$system_no]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$system_no]['out_time'] = $row['OUT_TIME'];
			}
		}
	}

	// Issue master data
	$dataArray = sql_select("SELECT issue_number, issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no, remarks, attention from inv_issue_master where id=$mst_id");
	$reqBookingNoArr = array();
	foreach ($dataArray as $row) {
		$issue_number = $row[csf('issue_number')];
		$issue_date = $row[csf('issue_date')];
		// $knit_dye_company = $row[csf('knit_dye_company')];
		$knit_dye_source = $row[csf('knit_dye_source')];
		$issue_purpose = $yarn_issue_purpose[$row[csf('issue_purpose')]];
		$attention = $row[csf('attention')];
		$remarks = $row[csf('remarks')];

		//for issue to
		$knit_dye_company = '';
		if ($row[csf('knit_dye_source')] == 1)
			$knit_dye_company = $company_library[$row[csf('knit_dye_company')]];
		else
			$knit_dye_company = $supplier_dtls_arr[$row[csf('knit_dye_company')]];

		$knit_dye_company_address = '';
		if ($row[csf('knit_dye_source')] == 1)
			$knit_dye_company_address = $company_address_arr[$row[csf('knit_dye_company')]];
		else
			$knit_dye_company_address = $supplier_address_arr[$row[csf('knit_dye_company')]];
	}

	// Roll issue

	$issue_res = sql_select("SELECT a.barcode_no, sum(a.qnty) as issue_qty, a.po_breakdown_id, a.booking_without_order,a.is_sales  from pro_roll_details a where a.entry_form=61 and a.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 group by a.barcode_no, a.po_breakdown_id, a.booking_without_order,a.is_sales ");
	$issued_data_arr = $po_ref_arr = $sales_id_arr = $non_order_booking_arr = array();
	foreach ($issue_res as $val) {
		$barcode_nums .= $val[csf("barcode_no")] . ",";
		$qntyFromRoll[$val[csf("barcode_no")]] = $val[csf("issue_qty")];
		$issued_data_arr[$val[csf('barcode_no')]]['po_id'] = $val[csf('po_breakdown_id')];
		$issued_data_arr[$val[csf('barcode_no')]]['is_sales'] = $val[csf('is_sales')];
		$issued_data_arr[$val[csf('barcode_no')]]['booking_without_order'] = $val[csf('booking_without_order')];
		if ($val[csf('is_sales')] == 0) //
		{
			if ($val[csf('booking_without_order')] == 1) {
				$non_order_booking_arr[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
			} else {
				$po_ref_arr[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
			}
		} else  // sales order
		{
			$sales_id_arr[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
		}
	}
	// echo "<pre>";print_r($issued_data_arr);die;
	$barcode_nums = chop($barcode_nums, ",");

	// order and with order booking
	$job_array = $job_company_arr = $sales_arr = $sales_booking_arr = $booking_arr = array();
	if (!empty($po_ref_arr)) {
		$job_sql = "SELECT  b.id, a.job_no_prefix_num, a.job_no,b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping as ref_no, a.buyer_name, a.sustainability_standard, a.fab_material, c.booking_no ,b.grouping
		from wo_po_details_master a, wo_po_break_down b left join wo_booking_dtls c on c.po_break_down_id=b.id
		where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in (1,3) and b.is_deleted=0 and c.booking_type in(1) and c.is_short=2 and b.id in(" . implode(",", $po_ref_arr) . ")
		group by b.id, a.job_no_prefix_num, a.job_no, b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping, a.buyer_name, a.sustainability_standard, a.fab_material, c.booking_no, b.grouping";
		//echo $job_sql;
		$job_sql_result = sql_select($job_sql); // is_short=2 main fabric booking
		foreach ($job_sql_result as $row) {
			$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
			$job_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
			$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
			$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
			$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
			$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			$job_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
			$job_array[$row[csf("id")]]["sustainability_std_id"] = $row[csf("sustainability_standard")];
			$job_array[$row[csf("id")]]["fabric_material_id"] = $row[csf("fab_material")];
			$job_company_arr[$row[csf('job_no_prefix_num')]]['company_name'] = $row[csf('company_name')];
			$job_array[$row[csf("id")]]["booking_no"] = $row[csf("booking_no")];
			$booking_arr[$row[csf("booking_no")]]["booking_ref_no"] = $row[csf("ref_no")];

			//$int_ref_arr[$row[csf('job_no')]]["grouping"] = $row[csf("grouping")];
		}
	}


	$int_ref_sql = "select internal_ref, job_no from wo_order_entry_internal_ref";

	$int_ref = sql_select($int_ref_sql);

	foreach ($int_ref as $row) {
		$int_ref_arr[$row[csf('job_no')]]["grouping"] = $row[csf("internal_ref")];
	}
	// Sales order
	if (!empty($sales_id_arr)) {
		$sales_details = sql_select("SELECT id, job_no, sales_booking_no, within_group, style_ref_no, buyer_id, po_buyer, po_job_no from fabric_sales_order_mst where id in(" . implode(",", $sales_id_arr) . ")");
		foreach ($sales_details as $sales_row) {
			$sales_arr[$sales_row[csf('id')]]['sales_booking_no'] = $sales_row[csf('sales_booking_no')];
			$sales_arr[$sales_row[csf('id')]]['within_group'] = $sales_row[csf('within_group')];
			$sales_arr[$sales_row[csf('id')]]['style_ref_no'] = $sales_row[csf('style_ref_no')];
			$sales_arr[$sales_row[csf('id')]]['buyer_id'] = $sales_row[csf('buyer_id')];
			$sales_arr[$sales_row[csf('id')]]['po_buyer'] = $sales_row[csf('po_buyer')];
			$sales_arr[$sales_row[csf('id')]]['po_job_no'] = $sales_row[csf('po_job_no')];
			$sales_booking_arr[] = "'" . $sales_row[csf('sales_booking_no')] . "'";
		}
	}

	if (!empty($sales_booking_arr)) {
		$booking_cond = " and a.booking_no in (" . implode(",", $sales_booking_arr) . ")";
		$booking_details = sql_select("SELECT a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping ref_no from wo_booking_mst a,wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and b.booking_type in(1,4) $booking_cond group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping");

		foreach ($booking_details as $booking_row) {
			$booking_arr[$booking_row[csf("booking_no")]]["int_ref"] = $booking_row[csf("ref_no")];
		}
	}

	// Non-order booking
	$non_order_booking_sql = sql_select("SELECT a.id as booking_id, a.booking_no,a.buyer_id,a.grouping, b.style_id
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
	where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.id in(" . implode(",", $non_order_booking_arr) . ") group by a.id, a.booking_no,a.buyer_id,a.grouping, b.style_id");
	$nonOrderBookingData_arr = array();
	foreach ($non_order_booking_sql as $row) {
		$style_id = $row[csf("style_id")];
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['booking_no'] = $row[csf('booking_no')];
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['buyer_id'] = $row[csf('buyer_id')];
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['grouping'] = $row[csf('grouping')];
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['sustainability_std_id'] = return_field_value("sustainability_std_id", "sample_development_mst", "id=$style_id");
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['fabric_material_id'] = return_field_value("fabric_material_id", "sample_development_mst", "id=$style_id");
		$nonOrderBookingData_arr[$row[csf('booking_id')]]['style_id'] = return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
	}
	// $nonOrderBookingStyle=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");

	// Knitting Production
	$sql = "SELECT a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as issue_qty,count(c.barcode_no) as num_of_roll, sum(c.reject_qnty) as reject_qnty, sum(c.qc_pass_qnty_pcs) as issue_qty_pcs, SUM (c.qc_pass_qnty) AS qc_pass_qnty, b.body_part_id, b.prod_id, d.detarmination_id, c.barcode_no,  c.po_breakdown_id,c.roll_no,d.brand
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d
	where a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums)
	group by  a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no, c.booking_without_order, c.is_sales, b.body_part_id, b.prod_id, d.detarmination_id, c.barcode_no, c.po_breakdown_id,c.roll_no,d.brand
	order by a.booking_no";
	//echo $sql;die;
	$sql_result = sql_select($sql);
	$booking_program_arr = array();
	foreach ($sql_result as $row) {
		if ($row[csf('receive_basis')] == 2) {
			$booking_program_arr[] = $row[csf("booking_no")];
			$productionYarnCount[$row[csf("booking_no")]] = $row[csf("yarn_count")];
		} else {
			$booking_no = explode("-", $row[csf('booking_no')]);
			$booking_program_arr[] = (int)$booking_no[3];
		}
	}
	//print_r($booking_program_arr);
	$planOrder = sql_select("SELECT a.id, b.booking_no, b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(" . implode(",", $booking_program_arr) . ")");
	$plan_arr = array();
	foreach ($planOrder as $plan_row) {
		$plan_arr[$plan_row[csf("id")]]["machine_dia"] = $plan_row[csf("machine_dia")];
		$plan_arr[$plan_row[csf("id")]]["feeder"] = $plan_row[csf("feeder")];
		$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
	}

	$refno_data_array = array();

	$duplicateLocation_check = array();
	$location_ids_arr = array();
	foreach ($sql_result as $row) {
		if ($duplicateLocation_check[$row[csf('location_id')]] == '') {
			$duplicateLocation_check[$row[csf('location_id')]] = $row[csf('location_id')];
			array_push($location_ids_arr, $row[csf('location_id')]);
		}
		$po_id = $issued_data_arr[$row[csf('barcode_no')]]['po_id'];
		$booking_without_order = $issued_data_arr[$row[csf('barcode_no')]]['booking_without_order'];

		$is_sales = $row[csf('is_sales')];
		if ($is_sales == 1 || $is_sales == 2) {
			$within_group = $sales_arr[$po_id]['within_group'];

			if ($within_group == 1) {
				$style_ref_no = "";
				$sales_booking = $sales_arr[$po_id]['sales_booking_no'];
				//$po=$sales_arr[$po_id]['sales_order'];
				//$job_no=$job_array[$sales_booking]['job_no'];
				$job_no = $sales_arr[$po_id]['po_job_no'];
				//$buyer_id=$job_array[$sales_booking]['buyer_name'];
				$ref_no = $booking_arr[$sales_booking]["int_ref"];
				$po = $sales_arr[$po_id]['sales_order'];
				$buyer_id = $sales_arr[$po_id]['buyer_name'];
				$booking_no = $sales_arr[$po_id]['sales_booking_no'];
				$poJobNo = $sales_arr[$po_id]['po_job_no'];
				$poBuyer = $sales_arr[$po_id]['po_buyer'];
				$salesBooking = $sales_arr[$po_id]['sales_booking_no'];
				$salesStyle = $sales_arr[$po_id]['style_ref_no'];
			} else {
				$job_no = "";
				$style_ref_no = "";
				$ref_no = "";
				$po = $sales_arr[$po_id]['sales_order'];
				$buyer_id = $sales_arr[$po_id]['buyer_name'];
				$booking_no = $sales_arr[$po_id]['sales_booking_no'];
			}
		} else {
			if ($booking_without_order == 1) {
				$ref_no = '';
				$job_no = '';
				$po = '';
				$booking_no = $nonOrderBookingData_arr[$po_id]["booking_no"];
				$buyer_id = $nonOrderBookingData_arr[$po_id]["buyer_id"];
				$style_ref_no = $nonOrderBookingData_arr[$po_id]["style_id"];
				$sustainability = $nonOrderBookingData_arr[$po_id]["sustainability_std_id"];
				$material = $nonOrderBookingData_arr[$po_id]["fabric_material_id"];
			} else {
				$ref_no = $job_array[$po_id]['ref_no'];
				$job_no = $job_array[$po_id]['job_no'];
				$style_ref_no = $job_array[$po_id]['style_ref_no'];
				$po = $job_array[$po_id]['po'];
				$buyer_id = $job_array[$po_id]['buyer_name'];
				$booking_no = $job_array[$po_id]["booking_no"];
				$sustainability = $job_array[$po_id]["sustainability_std_id"];
				$material = $job_array[$po_id]["fabric_material_id"];
			}
		}
		// echo $booking_no;

		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['job_no'] = $job_no;
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['buyer_id'] = $buyer_id;
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['style_ref_no'] = $style_ref_no;
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['booking_no'] = $booking_no;
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['sustainability'] = $sustainability;
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['material'] = $material;

		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['body_part_id'] = $row[csf('body_part_id')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['detarmination_id'] = $row[csf('detarmination_id')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['color_id'] .= $row[csf('color_id')] . ',';
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['color_range_id'] = $row[csf('color_range_id')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['brand_id'] = $row[csf('brand')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['stitch_length'] = $row[csf('stitch_length')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['issue_qty'] += $row[csf('issue_qty')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['issue_qty_pcs'] += $row[csf('issue_qty_pcs')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['reject_qnty'] += $row[csf('reject_qnty')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['num_of_roll'] += $row[csf('num_of_roll')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['program_no'] = $row[csf('bwo')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['knitting_company'] = $row[csf('knitting_company')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['barcode_no'] = $row[csf('barcode_no')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['roll_no'] = $row[csf('roll_no')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['qc_pass_qnty'] += $row[csf('qc_pass_qnty')];
		$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]][$row[csf("stitch_length")]]['int_ref_no'] = $ref_no;
	}
	//echo "<pre>"; print_r($refno_data_array);die;

	$colarCupArr = sql_select("SELECT id, body_part_type, body_part_full_name from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(40,50)");
	foreach ($colarCupArr as $row) {
		$body_part_data_arr[$row[csf('id')]]['body_part_full_name'] = $row[csf('body_part_full_name')];
		$body_part_data_arr[$row[csf('id')]]['body_part_type'] = $row[csf('body_part_type')];
	}

	// For Coller and Cuff data
	$sql_coller_cuff = "SELECT a.receive_basis, a.booking_no, b.body_part_id, c.booking_no as bwo, c.coller_cuff_size, c.qnty, c.qc_pass_qnty_pcs, c.barcode_no
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums) order by b.body_part_id, c.coller_cuff_size";
	// echo $sql_coller_cuff;
	$sql_coller_cuff_result = sql_select($sql_coller_cuff);
	foreach ($sql_coller_cuff_result as $row2) {
		if ($body_part_data_arr[$row2[csf('body_part_id')]]['body_part_type'] > 0 && $row2[csf('qc_pass_qnty_pcs')] > 0) {
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qnty'] += $row2[csf('qnty')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty_pcs'] += $row2[csf('qc_pass_qnty_pcs')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['receive_basis'] = $row2[csf('receive_basis')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['no_of_roll'] += count($row2[csf('barcode_no')]);
		}
	}
	$com_dtls = fnc_company_location_address($company, $store_location_id, 2);
?>
	<style type="text/css">
		table tr td {
			font-size: 16px;
		}

		.rpt_table thead th {
			font-size: 16px;
		}

		.rpt_table tfoot th {
			font-size: 16px;
		}
	</style>
	<?php
	$data_array = sql_select("select image_location  from common_photo_library where master_tble_id='" . $data[0] . "' and form_name='company_details' and is_deleted=0 and file_type=1");
	$lib_store_name = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");


	$data_store_transfer_sql = sql_select("select a.store_id from inv_transaction a,pro_roll_details b where a.mst_id=b.mst_id and b.entry_form=82 and a.item_category=13 and a.transaction_type=5 and b.barcode_no in($barcode_nums)
	group by a.store_id");
	$sotreNameTransfer = $sotreName = $transferBarcode = "";
	foreach ($data_store_transfer_sql as $row) {
		$transferBarcode .= $row[csf('store_id')] . ",";
		$sotreNameTransfer .= $lib_store_name[$row[csf('store_id')]] . ",";
	}
	$transferBarcode = chop($transferBarcode, ",");
	$sotreNameTransfer = chop($sotreNameTransfer, ",");


	if ($transferBarcode != "") {
		$transferBarcodeCond = "and b.barcode_no not in($transferBarcode)";
	}

	$data_store_sql = sql_select("select a.store_id from inv_transaction a,pro_roll_details b where a.mst_id=b.mst_id and b.entry_form=58 and a.item_category=13 and a.transaction_type=1 and b.barcode_no in($barcode_nums) $transferBarcodeCond
	group by a.store_id");

	foreach ($data_store_sql as $row) {
		$sotreName .= $lib_store_name[$row[csf('store_id')]] . ",";
	}

	$sotreName = chop($sotreName, ",");
	$allStore = $sotreNameTransfer . $sotreName;


	$issued_barcode_data = sql_select("SELECT a.id, a.barcode_no, b.store_name, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.qc_pass_qnty_pcs,a.qc_pass_qnty from pro_roll_details a, inv_grey_fabric_issue_dtls b where a.dtls_id=b.id and a.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.barcode_no in($barcode_nums) ");
	foreach ($issued_barcode_data as $row) {
		$issued_data_arr[$row[csf('barcode_no')]]['room'] = $row[csf("room")];
		$issued_data_arr[$row[csf('barcode_no')]]['rack'] = $row[csf("rack")];
		$issued_data_arr[$row[csf('barcode_no')]]['self'] = $row[csf("self")];
		$issued_data_arr[$row[csf('barcode_no')]]['bin_box'] = $row[csf("bin_box")];
	}

	$roll_rcv_sql = "SELECT a.id,b.brand_id,c.barcode_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id = b.mst_id AND b.id = c.dtls_id AND b.trans_id <> 0 AND a.entry_form IN (2, 22, 58) AND c.entry_form IN (2, 22, 58) AND c.status_active = 1 AND c.is_deleted = 0 AND c.barcode_no IN ($barcode_nums)";
	$roll_rcv_sql_data = sql_select($roll_rcv_sql);
	foreach ($roll_rcv_sql_data as $row) {
		$roll_rcv_arr[$row[csf('barcode_no')]]['brand_id'] = $row[csf("brand_id")];
	}

	$noOfCopy = "";
	for ($x = 1; $x <= $no_copy; $x++) {
		if ($x == 1) {
			$sup = 'st';
		} else if ($x == 2) {
			$sup = 'nd';
		} else if ($x == 3) {
			$sup = 'rd';
		} else {
			$sup = 'th';
		}

		$noOfCopy = "<span style='font-size:x-large;font-weight:bold'>" . $x . "<sup>" . $sup . "</sup> Copy</span>";
	?>

		<div style="width:1240px;">
			<table width="1240" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
				<tr>
					<td align="left" width="50">
						<?
						foreach ($data_array as $img_row) {
						?>
							<img src='../../../<? echo $com_dtls[2]; ?>' height='50' width='50' align="middle" />
						<?
						}
						?>
					</td>
					<td align="center" style="font-size:30px" colspan="3"><strong><? echo $com_dtls[0] . "<br><span style=\"font-size:14px;\">" . $com_dtls[1] . "</span>"; ?></strong></td>
					<td width="110" align="right"><?php echo $noOfCopy . ($is_gate_pass == 1 ? "<br><span style=\"color:#F00;font-weight:bold;\">Gate Pass Done</span>" : '') . ($is_gate_out == 1 ? "<br><span style=\"color:#F00;font-weight:bold;\">Gate Out Done</span>" : ''); ?></td>
				</tr>
				<tr>
					<td colspan="3" align="center" height="50" valign="middle" style="font-size:25px">
						<strong>Greige Fabric Delivery Challan</strong>
						<?php
						if ($data[4] == 1) {
						?>
							<!-- <span style="color:#0F0; font-weight:bold;">[Approved]</span> -->
						<?php
						}
						?>
					</td>
				</tr>
			</table>
			<div style="width:100%;">
				<div style="clear:both;">
					<table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
						<tr>
							<td width="125"><strong>Challan No:</strong></td>
							<td width="250px"><? echo $issue_number; ?></td>
							<td width="125"><strong>Dyeing Company:</strong></td>
							<td width="150px"><? echo $knit_dye_company; ?></td>
							<td width="130"><strong>Issue Purpose:</strong></td>
							<td width="130"><? echo $issue_purpose; ?></td>
						</tr>
						<tr>
							<td><strong>Delivery Date:</strong></td>
							<td><? echo change_date_format($issue_date); ?></td>
							<td><strong>Location:</strong></td>
							<td>
								<?

								$location_ids_arr = array_filter($location_ids_arr);
								$location_name = '';
								foreach ($location_ids_arr as $row) {
									$location_name .= $location_arr[$row];
								}
								echo $location_name; ?>
							</td>
							<td><strong>Batch Number:</strong></td>
							<td><? //echo change_date_format($issue_date); 
								?></td>
						</tr>
						<tr>
							<td><strong>Dyeing Source:</strong></td>
							<td><? echo $knitting_source[$knit_dye_source]; ?></td>
							<td><strong>Remarks:</strong></td>
							<td colspan="2"><? echo $location_arr[$inhouse_location]; ?></td>
						</tr>
						<tr>
							<td align="center" colspan="6" id="barcode_img_id_<?php echo $x; ?>" height="50" valign="middle" style="border-left:hidden;border-right:hidden;border-bottom:hidden;"></td>
						</tr>
					</table>

					<table style="margin-right:-40px;" cellspacing="0" width="1660" border="1" rules="all" class="rpt_table">
						<thead bgcolor="#dddddd">

							<tr>
								<th width="20">SL</th>
								<th width="120" style="font-size: 15px;">IR <br>&nbsp;Buyer Name<br> &nbsp;Program</th>
								<th width="120">Knitting Company</th>
								<th width="80">Yarn Count</th>
								<th width="80">Yarn Brand</th>
								<th width="80">Lot No</th>
								<th width="100">Fab. Color</th>
								<th width="100">Color Range</th>
								<th width="210">Fabrication</th>
								<th width="80">Stich Length</th>
								<th width="80">Fin GSM</th>
								<th width="60">Fab. Dia</th>
								<th width="50">MC DIA </th>
								<th width="60">MC. Gauge</th>
								<th width="120">Barcode No</th>
								<th width="80">Roll No</th>
								<th width="80">QC Pass Qty</th>
								<th>Rack <br> Shelf</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = 1;
							$k = 1;
							$sub_group_arr = array();
							//$refno_data_array[$job_no][$row[csf('febric_description_id')]][$row[csf('yarn_lot')]][$row[csf('yarn_count')]][$row[csf('machine_dia')]][$row[csf('machine_gg')]][$row[csf('gsm')]][$row[csf('width')]]['num_of_roll']+=$row[csf('num_of_roll')];
							$fab_material = array(1 => "Organic", 2 => "BCI");
							foreach ($refno_data_array as $job => $jobArr) {
								$sub_tot_roll_no = 0;
								$grnd_tot_roll_no = 0;
								$sub_tot_qc_pass_qty = 0;
								$grnd_tot_qc_pass_qty = 0;
								foreach ($jobArr as $febricDesc => $febricDescDataArr) {
									$sub_total_no_of_roll_fabric = $sub_tot_qty_fabric = 0;
									foreach ($febricDescDataArr as $yarn_lot => $yarn_lotArr) {
										foreach ($yarn_lotArr as $yarn_count => $yarn_countArr) {
											foreach ($yarn_countArr as $machine_dia => $machine_diaArr) {
												foreach ($machine_diaArr as $machine_gg => $machine_ggArr) {
													foreach ($machine_ggArr as $gsm => $gsmArr) {
														foreach ($gsmArr as $dia => $diaArr) {
															foreach ($diaArr as $color_ids => $color_ids_Data) {
																foreach ($color_ids_Data as $stitch_length => $row) {
																	if ($i % 2 == 0)
																		$bgcolor = "#E9F3FF";
																	else
																		$bgcolor = "#FFFFFF";

																	$ycount = '';
																	$yarn_count = explode(",", $yarn_count);
																	foreach ($yarn_count as $count_id) {
																		if ($ycount == '') $ycount = $yarn_count_details[$count_id];
																		else $ycount .= "," . $yarn_count_details[$count_id];
																	}

																	$rack = $floor_room_rack_array[$issued_data_arr[$row['barcode_no']]['rack']];
																	$shelf = $floor_room_rack_array[$issued_data_arr[$row['barcode_no']]['self']];

																	$color_id_arr = array_unique(explode(",", $row["color_id"]));
																	$all_color_name = "";
																	foreach ($color_id_arr as $c_id) {
																		$all_color_name .= $color_arr[$c_id] . ",";
																	}
																	$all_color_name = chop($all_color_name, ",");
							?>
																	<tr bgcolor="<? echo $bgcolor; ?>">
																		<td style="font-size: 15px"><? echo $i; ?></td>
																		<td style="font-size: 15px" title=<? echo "Job:";
																											$job; ?>>
																			<?
																			echo "IR: " . $row["int_ref_no"] . ' <br>' . "B: " . $buyer_array[$poBuyer] . ' <br>' . "P: " . $row['program_no'];
																			?>

																		</td>
																		<td style="font-size: 15px">
																			<? echo $company_library[$row['knitting_company']]; ?>
																		</td>
																		<td style="word-wrap:break-all; font-size: 15px" title="">
																			<? echo $ycount; ?>
																		</td>
																		<td style="word-wrap:break-all; font-size: 15px" title="">
																			<? echo $brand_details[$roll_rcv_arr[$row['barcode_no']]['brand_id']]; ?>
																		</td>
																		<td style="font-size: 15px">
																			<? echo $yarn_lot; ?>
																		</td>
																		<td style="font-size: 15px" title="Yarn Dtls:<? echo $row['detarmination_id']; ?>">
																			<? echo $all_color_name; ?>
																		</td>
																		<td style="font-size: 15px" title="">
																			<? echo $color_range[$row["color_range_id"]]; ?>
																		</td>
																		<td style="font-size: 15px" title="">
																			<? echo $composition_arr[$febricDesc]; ?>
																		</td>
																		<td style="font-size: 15px">
																			<? echo $row['stitch_length']; ?>
																		</td>
																		<td style="font-size: 15px">
																			<? echo $gsm; ?>
																		</td>
																		<td style="font-size: 15px; text-align: center;">
																			<? echo $dia; ?>
																		</td>
																		<td style="font-size: 15px">
																			<? echo $machine_dia; ?>
																		</td>
																		<td style="font-size: 15px; text-align: center;">
																			<? echo $machine_gg; ?>
																		</td>
																		<td style="font-size: 15px" align="right" style="font-size: 15px;">
																			<? echo $row['barcode_no']; ?>
																		</td>
																		<td style="font-size: 15px" align="right" style="font-size: 15px;">
																			<? echo $row['roll_no']; ?>
																		</td>
																		<td style="font-size: 15px" align="right">
																			<? echo number_format($row['qc_pass_qnty'], 2); ?></td>
																		<td style="font-size: 15px" align="right">
																			<?
																			echo "R: " . $rack . "<br>" . "S: " . $shelf;
																			?>
																		</td>
																	</tr>
									<?
																	$i++;
																	$sub_tot_roll_no += $row['roll_no'];
																	$grnd_tot_roll_no += $row['roll_no'];

																	$sub_tot_qc_pass_qty += $row['qc_pass_qnty'];
																	$grnd_tot_qc_pass_qty += $row['qc_pass_qnty'];
																}
															}
														}
													}
												}
											}
										}
									}
									?>
									<tr class="tbl_bottom">
										<td colspan="15" style=" text-align:right;font-size: 14px;"><strong>Total:</strong></td>
										<td align="right" style="font-size: 14px;"><? echo $sub_tot_roll_no; ?></td>
										<td align="right"><? echo number_format($sub_tot_qc_pass_qty, 2); ?></td>
										<td align="right"></td>
									</tr>
							<?
								}
							}

							?>
							<tr class="tbl_bottom">
								<td align="right" style="font-size: 16px;" colspan="15"><strong>Grand Total:</strong></td>
								<td align="right" style="font-size: 16px;"><strong><? echo $grnd_tot_roll_no; ?></strong></td>
								<td align="right"><? echo number_format($grnd_tot_qc_pass_qty, 2); ?></td>
								<td align="right"></td>
							</tr>
						</tbody>
					</table>
					<br>

				</div>
				<br>
				<? echo signature_table(124, $company, "1200px"); ?>
			</div>
		</div>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {
				var zs = '<?php echo $x; ?>';
				var value = valuess; //$("#barcodeValue").val();
				var btype = 'code39'; //$("input[name=btype]:checked").val();
				var renderer = 'bmp'; // $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#barcode_img_id_" + zs).html('11');
				value = {
					code: value,
					rect: false
				};
				$("#barcode_img_id_" + zs).show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $data[1]; ?>');

			//for gate pass barcode
			function generateBarcodeGatePass(valuess) {
				var zs = '<?php echo $x; ?>';
				var value = valuess; //$("#barcodeValue").val();
				var btype = 'code39'; //$("input[name=btype]:checked").val();
				var renderer = 'bmp'; // $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#gate_pass_barcode_img_id_" + zs).html('11');
				value = {
					code: value,
					rect: false
				};
				$("#gate_pass_barcode_img_id_" + zs).show().barcode(value, btype, settings);
			}

			if ('<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>' != '') {
				generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
			}
		</script>
		<div style="page-break-after:always;"></div>
	<?php
	}
	exit();
}

if ($action == "multi_issue_popup") {
	echo load_html_head_contents("Issue Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(id, posted_account) {
			$('#hidden_system_id').val(id);
			$('#hidden_posted_account').val(posted_account);
			parent.emailwindow.hide();
		}

		function fnc_submit() {
			if (document.getElementById('cbo_dyeing_comp').value == 0) {
				alert("select Dyeing Company");
				return;
			}
			show_list_view(document.getElementById('txt_search_common').value + '_' + document.getElementById('cbo_search_by').value + '_' + document.getElementById('txt_date_from').value + '_' + document.getElementById('txt_date_to').value + '_' + '<? echo $cbo_company_id; ?>' + '_' + document.getElementById('cbo_dyeing_source').value + '_' + document.getElementById('cbo_dyeing_comp').value, 'create_multi_challan_search_list_view', 'search_div', 'grey_fabric_issue_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);')
		}

		var selected_id = new Array();

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(str) {
			toggle(document.getElementById('search' + str), '#FFFFCC');
			if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_individual_id' + str).val());
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
			}

			var id = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
			}

			id = id.substr(0, id.length - 1);

			$('#hidden_system_id').val(id);
		}

		function window_close() {

			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:858px;">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:850px; margin-left:2px">
					<table cellpadding="0" cellspacing="0" width="840" border="1" rules="all" class="rpt_table">
						<thead>
							<th class="must_entry_caption">Dyeing Source</th>
							<th class="must_entry_caption">Dyeing Company</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="150">Please Enter Issue No</th>
							<th>Issue Date Range</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="hidden_system_id" id="hidden_system_id" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down("cbo_dyeing_source", 80, $knitting_source, "", 1, "-- Select --", $cbo_dyeing_source, "load_drop_down( 'grey_fabric_issue_roll_wise_controller',this.value+'**'+'" . $cbo_company_id . "','load_drop_down_knitting_com','dye_company_td' );", "", "1,3");

								?>
							</td>
							<td id="dye_company_td">
								<?
								if ($cbo_dyeing_source == 1) {
									$comp_cond = " and comp.id=$cbo_dyeing_comp";
									echo create_drop_down("cbo_dyeing_comp", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $comp_cond order by comp.company_name", "id,company_name", 1, "-- Select --", $company_id, "", "");
								} else if ($cbo_dyeing_source == 3) {
									$comp_cond = " and a.id=$cbo_dyeing_comp";
									echo create_drop_down("cbo_dyeing_comp", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 $comp_cond group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "");
								} else {
									echo create_drop_down("cbo_dyeing_comp", 152, $blank_array, "", 1, "-- Select --", 0, "");
								}

								?>
							</td>

							<td align="center">
								<?
								$search_by_arr = array(1 => "Issue No", 2 => "FSO No", 3 => "Booking No");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", 1, $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_submit()" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_multi_challan_search_list_view") {
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$dyeing_source = $data[5];
	$dyeing_company = $data[6];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and a.issue_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and a.issue_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		if ($search_by == 1) {
			$search_field_cond = "and a.issue_number like '$search_string'";
		} else if ($search_by == 2) {
			$job_booking_cond = "and d.job_no like '$search_string'";
		} else if ($search_by == 3) {
			$job_booking_cond = "and d.sales_booking_no like '$search_string'";
		}

		if ($search_by == 2 || $search_by == 3) {
			$search_field_cond = " and a.id in (SELECT a.id from inv_issue_master a, pro_roll_details c, fabric_sales_order_mst d where a.id=c.mst_id and c.po_breakdown_id=d.id and c.is_sales=1 and c.entry_form =61 and c.is_returned=0 and a.entry_form=61 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$company_id $job_booking_cond group by a.id)";
		}
	}

	if ($db_type == 0) {
		$year_field = "YEAR(a.insert_date) as year,";
	} else if ($db_type == 2) {
		$year_field = "to_char(a.insert_date,'YYYY') as year,";
	} else $year_field = ""; //defined Later

	$sql = "SELECT a.id, a.issue_date, a.issue_number, a.knit_dye_source, a.knit_dye_company, sum(c.qnty) as issue_qnty
	 from inv_issue_master a,inv_grey_fabric_issue_dtls b, pro_roll_details c, fabric_sales_order_mst d
	 where a.id=b.mst_id and b.id =c.dtls_id and c.po_breakdown_id=d.id and c.is_sales=1 and c.entry_form =61 and c.is_returned=0 and a.entry_form=61 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.knit_dye_source=$dyeing_source and a.knit_dye_company=$dyeing_company $date_cond and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $search_field_cond
	 group by a.id, a.issue_date, a.issue_number, a.knit_dye_source, a.knit_dye_company
	 order by a.id";

	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');

?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="80">Issue Date</th>
			<th width="100">Issue No</th>
			<th width="120">Dyeing Source</th>
			<th width="140">Dyeing Company</th>
			<th width="100">Issue Quantity</th>
		</thead>
	</table>
	<div style="width:840px; max-height:200px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				$dye_comp = "&nbsp;";
				if ($row[csf('knit_dye_source')] == 1)
					$dye_comp = $company_arr[$row[csf('knit_dye_company')]];
				else
					$dye_comp = $supllier_arr[$row[csf('knit_dye_company')]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">

					<td width="40"><? echo $i; ?><input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>" /></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
					<td width="100">
						<p>&nbsp;<? echo $row[csf('issue_number')]; ?></p>
					</td>
					<td width="120">
						<p><? echo $knitting_source[$row[csf('knit_dye_source')]]; ?>&nbsp;</p>
					</td>
					<td width="140">
						<p><? echo $dye_comp; ?>&nbsp;</p>
					</td>
					<td width="100">
						<p><? echo $row[csf('issue_qnty')]; ?>&nbsp;</p>
					</td>

				</tr>
			<?
				$i++;
			}
			?>
		</table>
	</div>
	<table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" />
						Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="window_close()" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
<?
	exit();
}

if ($action == "sales_multi_issue_challan_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$issue_ids = $data[1];

	$company_array = get_company_array();
	$color_arr = get_color_array();
	$supplier_arr = get_supplier_array();
	$buyer_array = get_buyer_array();
	$yarn_count_details = get_yarn_count_array();
	$brand_arr = get_brand_array();
	$country_arr = get_country_array();
	//$batch_arr = get_batch_array();
	//print_r($brand_arr[1803]);die;


	$issue_idsArr = explode(",", $issue_ids);
	$issue_idsArr = array_filter($issue_idsArr);
	//print_r($issue_idsArr);
	if (!empty($issue_idsArr)) {
		$all_issue_id_cond = "";
		$issId = "";
		if ($db_type == 2 && count($issue_idsArr) > 999) {
			$issue_idsArr_chunk = array_chunk($issue_idsArr, 999);
			foreach ($issue_idsArr_chunk as $chunk_arr) {
				$chunk_arr_value = implode(",", $chunk_arr);
				$issId .= "  a.mst_id in($chunk_arr_value) or ";
			}

			$all_issue_id_cond .= " and (" . chop($issId, 'or ') . ")";
		} else {
			$all_issue_id_cond = " and a.mst_id in(" . implode(",", $issue_idsArr) . ")";
		}

		$sql_data = "SELECT c.id, c.knit_dye_source, c.knit_dye_company, c.issue_number, c.issue_date, c.issue_purpose, c.remarks, d.job_no, d.sales_booking_no, d.po_buyer, d.style_ref_no, b.barcode_no, b.po_breakdown_id, d.buyer_id, d.within_group, sum(b.qnty) as issue_qnty, count(b.roll_id) as no_of_roll, sum(b.qc_pass_qnty_pcs) as qty_in_pcs from inv_grey_fabric_issue_dtls a, pro_roll_details b, inv_issue_master c, fabric_sales_order_mst d where a.id=b.dtls_id and a.mst_id=b.mst_id and a.mst_id=c.id and b.PO_BREAKDOWN_ID=d.id and b.is_sales=1 $all_issue_id_cond and b.entry_form=61 and b.is_returned!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.id, c.knit_dye_source, c.knit_dye_company, c.issue_number, c.issue_date, c.issue_purpose, c.remarks, d.job_no, d.sales_booking_no, d.po_buyer, d.style_ref_no, b.po_breakdown_id, d.buyer_id, d.within_group, b.barcode_no order by c.id asc";
		//and a.mst_id in ($issue_ids) and b.mst_id in ($issue_ids)

		$result_data = sql_select($sql_data);
		foreach ($result_data as $row) {
			$barcodeNos .= $row[csf("barcode_no")] . ",";

			$issue_purpose[$yarn_issue_purpose[$row[csf("issue_purpose")]]] = $yarn_issue_purpose[$row[csf("issue_purpose")]];
			$remarks[$row[csf("remarks")]] = $row[csf("remarks")];
			$knit_dye_source = $row[csf("knit_dye_source")];
			$knit_dye_company = $row[csf("knit_dye_company")];
		}
		$barcodeNos = chop($barcodeNos, ",");

		$issue_purposes = implode(",", array_filter($issue_purpose));
		$remarks = implode(",", array_filter($remarks));
	} else {
		echo "Data not Found";
		die;
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}


?>
	<style type="text/css">
		.word_wrap_brek {
			word-break: break-all;
			word-wrap: break-word;
		}
	</style>
	<div>
		<table width="1290" cellspacing="0">
			<tr>
				<td colspan="9" align="center" style="font-size:22px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="9" align="center" style="font-size:14px">
					<? echo show_company($company, '', ''); ?>
				</td>
			</tr>
			<tr>
				<td colspan="9" align="center" style="font-size:18px"><strong><u><? echo $data[3]; ?> Grey Fabric Roll Issue Challan</u></strong></td>
			</tr>

			<tr>
				<td width="150" style="font-size:16px; font-weight:bold;"><strong>Company</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="250px" style="font-size:18px;"><? echo $company_array[$company]['name']; ?></td>

				<td width="150" style="font-size:16px"><strong>Issue Purpose</strong> </td>
				<td width="5"><strong>:</strong></td>
				<td width="250" style="font-size:18px;"><? echo $issue_purposes; ?></td>

			</tr>

			<tr>
				<td width="150" style="font-size:16px; font-weight:bold;"><strong>Dyeing Source</strong></td>
				<td width="10"><strong>:</strong></td>
				<td width="250px" style="font-size:18px;"><? echo $knitting_source[$knit_dye_source]; ?></td>

				<td width="150" style="font-size:16px"><strong>Dyeing Company</strong> </td>
				<td width="5"><strong>:</strong></td>
				<td width="250" style="font-size:18px;"><? if ($knit_dye_source == 1) echo $company_array[$knit_dye_company]['name'];
														else if ($knit_dye_source == 3) echo $supplier_arr[$knit_dye_company]; ?></td>
			</tr>

			<tr>
				<td style="font-size:16px; font-weight:bold;"><strong>Address</strong></td>
				<td width="10"><strong>:</strong></td>
				<td style="font-size:12px;">
					<?
					if ($knit_dye_source == 1) {
						echo show_company($knit_dye_company, '', '');
					} else {
						echo return_field_value("address_1", "lib_supplier", "id=$knit_dye_company", "address_1");
					}

					?>

				</td>
				<td><strong>Remarks</strong></td>
				<td><strong>:</strong></td>
				<td width="175px"><? echo $remarks; ?></td>
			</tr>
			<tr>
				<td colspan="9" id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1600" border="1" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center">
				<th width="20">SL</th>
				<th width="100">Challan No</th>
				<th width="80">Issue Date</th>
				<th width="80">PO Buyer/ Style</th>
				<th width="100">Booking No / Prog. No</th>
				<th width="100">Sales Order No</th>
				<th width="50">Yarn Count</th>
				<th width="50">Yarn Brand</th>
				<th width="50">Lot No</th>
				<th width="100">Fab Color</th>
				<!-- <th width="100">Color Range</th> -->
				<th width="100">Dyeing Color</th>
				<th width="150">Fabrication</th>
				<th width="50">Stich Length</th>
				<th width="40">Fin GSM</th>
				<th width="40">Fin. Dia</th>
				<th width="60">Req. Dia</th>
				<th width="60">M/C Dia</th>
				<th width="60">M/C Gauge</th>
				<th width="50">Issue Qty</th>
				<th width="40">No of Roll</th>
			</thead>
			<?

			$i = 1;

			$barcodeNosArr = explode(",", $barcodeNos);
			$barcodeNosArr = array_filter($barcodeNosArr);
			if (!empty($barcodeNosArr)) {
				$all_barcode_cond = "";
				$barProd = "";
				if ($db_type == 2 && count($barcodeNosArr) > 999) {
					$barcodeNosArr_chunk = array_chunk($barcodeNosArr, 999);
					foreach ($barcodeNosArr_chunk as $chunk_arr) {
						$chunk_arr_value = implode(",", $chunk_arr);
						$barProd .= "  a.barcode_no in($chunk_arr_value) or ";
					}

					$all_barcode_cond .= " and (" . chop($barProd, 'or ') . ")";
				} else {
					$all_barcode_cond = " and a.barcode_no in(" . implode(",", $barcodeNosArr) . ")";
				}

				$sql_production = sql_select("SELECT a.barcode_no,c.machine_dia,c.machine_gg , c.color_range_id, c.color_id, c.yarn_count, c.yarn_lot, c.yarn_prod_id, c.stitch_length, c.gsm, c.width, c.febric_description_id, b.receive_basis, b.booking_no from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c where a.mst_id=b.id  and a.dtls_id=c.id and b.id=c.mst_id $all_barcode_cond  and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

				//and a.barcode_no in($barcodeNos)

				foreach ($sql_production as $row) {
					$production_data_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];

					$production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] = $row[csf("machine_dia")];
					$production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] = $row[csf("machine_gg")];
					$production_rcv_data[$row[csf("barcode_no")]]["color_range_id"] = $color_range[$row[csf("color_range_id")]];
					$production_rcv_data[$row[csf("barcode_no")]]["color_id"] = $row[csf("color_id")];
					$production_rcv_data[$row[csf("barcode_no")]]["yarn_count"] = $row[csf("yarn_count")];
					$production_rcv_data[$row[csf("barcode_no")]]["yarn_lot"] = $row[csf("yarn_lot")];
					$production_rcv_data[$row[csf("barcode_no")]]["yarn_prod_id"] = $row[csf("yarn_prod_id")];
					$production_rcv_data[$row[csf("barcode_no")]]["stitch_length"] = $row[csf("stitch_length")];
					$production_rcv_data[$row[csf("barcode_no")]]["gsm"] = $row[csf("gsm")];
					$production_rcv_data[$row[csf("barcode_no")]]["width"] = $row[csf("width")];
					$production_rcv_data[$row[csf("barcode_no")]]["febric_description_id"] = $row[csf("febric_description_id")];
					if ($row[csf("receive_basis")] == 2) {
						$production_rcv_data[$row[csf("barcode_no")]]["program_no"] = $row[csf("booking_no")];
					}

					$all_yarn_prod[$row[csf("yarn_prod_id")]] = $row[csf("yarn_prod_id")];
				}

				if (!empty($all_yarn_prod)) {
					$all_yarn_prod_arr = array_filter($all_yarn_prod);

					$all_yarn_prod_arr = explode(",", implode(",", $all_yarn_prod_arr));
					$all_yarn_prod_arr = array_unique($all_yarn_prod_arr);

					//echo count($all_yarn_prod_arr);die;
					$all_yarn_cond = "";
					$yProd = "";
					if ($db_type == 2 && count($all_yarn_prod_arr) > 999) {
						$all_yarn_prod_arr_chunk = array_chunk($all_yarn_prod_arr, 999);
						foreach ($all_yarn_prod_arr_chunk as $chunk_arr) {
							$chunk_arr_value = implode(",", $chunk_arr);
							$yProd .= "  id in($chunk_arr_value) or ";
						}

						$all_yarn_cond .= " and (" . chop($yProd, 'or ') . ")";
					} else {
						$all_yarn_cond = " and id in(" . implode(",", $all_yarn_prod_arr) . ")";
					}

					$yarn_brand_arr = return_library_array("select brand, id from product_details_master where status_active=1 $all_yarn_cond", "id", "brand");
				}
			}


			foreach ($result_data as $row) {
				$data_arr_string = $production_rcv_data[$row[csf("barcode_no")]]["yarn_count"] . "*" . $production_rcv_data[$row[csf("barcode_no")]]["yarn_prod_id"] . "*" . $production_rcv_data[$row[csf("barcode_no")]]["yarn_lot"] . "*" . $production_rcv_data[$row[csf("barcode_no")]]["color_id"] . "*" . $production_rcv_data[$row[csf("barcode_no")]]["color_range_id"] . "*" . $production_rcv_data[$row[csf("barcode_no")]]["febric_description_id"] . "*" . $production_rcv_data[$row[csf("barcode_no")]]["stitch_length"] . "*" . $production_rcv_data[$row[csf("barcode_no")]]["gsm"] . "*" . $production_rcv_data[$row[csf("barcode_no")]]["width"] . "*" . $production_rcv_data[$row[csf("barcode_no")]]["machine_dia"] . "*" . $production_rcv_data[$row[csf("barcode_no")]]["machine_gg"] . "*" . $production_rcv_data[$row[csf("barcode_no")]]["program_no"] . "*" . $row[csf("job_no")] . "*" . $row[csf("issue_number")];


				$main_arr[$data_arr_string]['issue_qnty'] += $row[csf("issue_qnty")];
				$main_arr[$data_arr_string]['no_of_roll'] += $row[csf("no_of_roll")];
				$main_arr[$data_arr_string]['issue_number'] = $row[csf("issue_number")];
				$main_arr[$data_arr_string]['issue_date'] = $row[csf("issue_date")];
				$main_arr[$data_arr_string]['job_no'] = $row[csf("job_no")];
				$main_arr[$data_arr_string]['sales_booking_no'] = $row[csf("sales_booking_no")];
				if ($row[csf("within_group")] == 1) {
					$main_arr[$data_arr_string]['po_buyer'] = $row[csf("po_buyer")];
				} else {
					$main_arr[$data_arr_string]['po_buyer'] = $row[csf("buyer_id")];
				}
				$main_arr[$data_arr_string]['style_ref_no'] = $row[csf("style_ref_no")];
			}


			foreach ($main_arr as $stringData => $row) {
				$stringDataArr = explode("*", $stringData);

				$yarn_count = $stringDataArr[0];
				$yarn_prod_id = $stringDataArr[1];
				$yarn_lot = $stringDataArr[2];
				$color_id = $stringDataArr[3];
				$color_range = $stringDataArr[4];
				$febric_description_id = $stringDataArr[5];
				$stitch_length = $stringDataArr[6];
				$gsm = $stringDataArr[7];
				$width = $stringDataArr[8];
				$machine_dia = $stringDataArr[9];
				$machine_gg = $stringDataArr[10];
				$program_no = $stringDataArr[11];

				$count = '';
				$yarn_count_arr = explode(",", $yarn_count);
				foreach ($yarn_count_arr as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id];
					else $count .= "," . $yarn_count_details[$count_id];
				}

				//echo $yarn_prod_id;die;

				$yarn_prod_id_arr = array_unique(explode(",", $yarn_prod_id));
				$brand = '';
				foreach ($yarn_prod_id_arr as $val) {
					if ($brand == "") {
						$brand .= $brand_arr[$yarn_brand_arr[$val]];
					} else {
						$brand .= "," . $brand_arr[$yarn_brand_arr[$val]];
					}
				}

				$color = '';
				$colorArr = explode(",", $color_id);
				foreach ($colorArr as $val) {
					if ($color == "") {
						$color .= $color_arr[$val];
					} else {
						$color .= "," . $color_arr[$val];
					}
				}
				$fabric_des = $composition_arr[$febric_description_id];

				$no_of_roll = $row["no_of_roll"];
				$issue_qnty = $row["issue_qnty"];

				$check_string = $row["issue_number"];

				if (!in_array($check_string, $checkArr))
				{
					$checkArr[$i] = $check_string;
					if ($i > 1)
					{
						?>
						<tr bgcolor="#CCCCCC" style="font-weight:bold">
							<td colspan="18" align="right">Sub Total:</td>
							<td width="50" align="right"><? echo number_format($sub_tot_qty, 2); ?></td>
							<td width="40" align="right"><? echo $sub_total_roll;?></td>
						</tr>
						<?
						$sub_tot_qty = 0;
						$sub_total_roll = 0;
					}
				}

			?>
				<tr>
					<td width="20"><? echo $i; ?></td>
					<td width="100"><? echo $row["issue_number"]; ?></td>
					<td width="80"><? echo change_date_format($row["issue_date"]); ?></td>
					<td width="80"><? echo $buyer_array[$row["po_buyer"]] . " / " . $row["style_ref_no"] ?></td>
					<td class="word_wrap_brek" width="100"><? echo $row["sales_booking_no"] . " / " . $program_no; ?></td>
					<td width="100"><? echo $row["job_no"]; ?></td>
					<td width="50"><? echo $count; ?></td>
					<td class="word_wrap_brek" width="50"><? echo $brand; ?></td>
					<td class="word_wrap_brek" width="50"><? echo $yarn_lot; ?></td>
					<td class="word_wrap_brek" width="100"><? echo $color; ?></td>
					<!-- <td width="100"><? //echo $color_range;
											?></td> -->
					<td width="100"><? //echo $color_range;
									?></td>
					<td class="word_wrap_brek" width="150"><? echo $fabric_des; ?></td>
					<td class="word_wrap_brek" width="50"><? echo $stitch_length; ?></td>
					<td width="40"><? echo $gsm; ?></td>
					<td width="40"><? echo $width; ?></td>
					<td width="60"><? //echo $machine_dia; 
									?></td>
					<td width="60"><? echo $machine_dia; ?></td>
					<td width="60"><? echo $machine_gg; ?></td>
					<td width="50" align="right"><? echo number_format($issue_qnty, 2); ?></td>
					<td width="40" align="right"><? echo $no_of_roll; ?></td>
				</tr>
			<?
				$sub_tot_qty += $issue_qnty;
				$sub_total_roll += $no_of_roll;

				$tot_qty += $issue_qnty;
				$total_roll += $no_of_roll;
				$i++;
			}
			?>
			<tr bgcolor="#CCCCCC" style="font-weight:bold">
				<td align="right" colspan="18">Sub Total:</td>
				<td align="right"><? echo number_format($sub_tot_qty, 2, '.', ''); ?></td>
				<td align="right"><? echo $sub_total_roll; ?></td>
			</tr>
			<tr bgcolor="#CCCCCC" style="font-weight:bold">
				<td align="right" colspan="18">Grand Total:</td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
				<td align="right"><? echo $total_roll; ?></td>
			</tr>

		</table>
	</div>
	<? echo signature_table(124, $company, "1600px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
	exit();
}


if($action == "check_box")
{
	extract($_REQUEST);
	
	$variable_textile_sales_maintain = sql_select("select production_entry, process_loss_editable from variable_settings_production where company_name=$company_id and variable_list=66 and status_active=1");
	
	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2) 
	{
		$textile_sales_maintain = 1;
	 } 
	 else 
	{
	 $textile_sales_maintain = 0;
	}

	echo $textile_sales_maintain;
	exit();
}