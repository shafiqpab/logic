<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
include('../../includes/common.php');
$permission = $_SESSION['page_permission'];
$user_id = $_SESSION['logic_erp']['user_id'];

function return_domestic_rate($rate, $ile_cost, $exchange_rate, $conversion_factor)
{
	$rate_ile = $rate + $ile_cost;
	$rate_ile_exchange = $rate_ile * $exchange_rate;

	if ($conversion_factor > 0) {
		$doemstic_rate = $rate_ile_exchange / $conversion_factor;
	} else {
		$doemstic_rate = $rate_ile_exchange;
	}
	return $doemstic_rate;
}

//$specialCharactersArr = array(0=>'*', 1=>'\'', 2=>'£', 3=>'$', 4=>'&', 5=>'(', 6=>')', 7=>'#', 8=>'~', 9=>'|', 10=>'=', 11=>'_', 12=>'"', 13=>'`', 14=>'^', 15=>'\\');

function check_special_character($string)
{
	$special_charater = '*\'£$&()#~|=_"`^\\';
	$specialCharactersArr = str_split($special_charater);
	$splitStringArr = str_split($string);
	$result = array_diff($specialCharactersArr, $splitStringArr);
	if (count($result) < count($specialCharactersArr)) return 1;
	else return 0;
}

$cdate = date("d-m-Y");
include('excel_reader.php');
$output = `uname -a`;
if (isset($_POST["submit"])) {
	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');	
	extract($_REQUEST);

	$company_library = return_library_array("select company_name, id from lib_company where status_active=1 and is_deleted=0", "company_name", "id");
	$yarn_count_library = return_library_array("select yarn_count, id from lib_yarn_count where status_active=1 and is_deleted=0", "yarn_count", "id");
	$color_library = return_library_array("select color_name, id from lib_color where status_active=1 and is_deleted=0", "color_name", "id");
	$brand_library = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0", 'id', 'brand_name');

	$sql_supplier = sql_select("select c.id as ID, c.supplier_name as SUPPLIER_NAME, a.tag_company as COMPANY_ID from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id=b.supplier_id and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name,a.tag_company order by supplier_name");
	$supplier_arr = array();
	foreach ($sql_supplier as $val) {
		$supplier_arr[$val['COMPANY_ID']][$val['SUPPLIER_NAME']] = $val['ID'];
	}

	$sql_store = sql_select("select a.id as ID, a.store_name as STORE_NAME, a.company_id as COMPANY_ID from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.category_type=1");
	$store_arr = array();
	foreach ($sql_store as $val) {
		$store_arr[$val['COMPANY_ID']][$val['STORE_NAME']] = $val['ID'];
	}


	foreach (glob("files/" . "*.xls") as $filename) {
		@unlink($filename);
	}
	foreach (glob("files/" . "*.xlsx") as $filename) {
		@unlink($filename);
	}

	$source_excel = $_FILES['uploadfile']['tmp_name'];
	$targetzip = 'files/' . $_FILES['uploadfile']['name'];
	$file_name = $_FILES['uploadfile']['name'];
	// echo $source_excel.'**'.$targetzip.'**'.$file_name;die;
	unset($_SESSION['excel']);
	if (move_uploaded_file($source_excel, $targetzip)) {
		$excel = new Spreadsheet_Excel_Reader($targetzip);

		//die("system testing");

		$card_colum = 0;
		$m = 1;
		$all_data_arr = array();
		for ($i = 1; $i <= $excel->sheets[0]['numRows']; $i++) {
			if ($m == 1) {
				for ($j = 1; $j <= $excel->sheets[0]['numCols']; $j++) {
				}
				$m++;
			} else {
				$company = $supplier = $dyed_type = $challan_no = $store_name = $currency_name = $source_name = $yarn_count = '';
				$composition_name = $yarn_type_name = $color = $lot_batch = $brand = $recv_qty = $uom = $rate = '';
				$no_of_bag = $no_of_cone_per_bag = $no_of_loose_cone = $weight_per_bag = $wght_cone = $floor = $room = '';
				$rack = $shelf = $bin_box = $remarks = $receive_date = '';
				//echo '<pre>';print_r($excel->sheets[0]['cells'][$i]);die;

				$str_rep = array("*",  "=", "\r", "\n", "#");
				//$str_rep= preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $excel->sheets[0]['cells'][$i]); 
				if (isset($excel->sheets[0]['cells'][$i][1])) $company = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][1]);
				if (isset($excel->sheets[0]['cells'][$i][2])) $supplier = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][2]);
				if (isset($excel->sheets[0]['cells'][$i][3])) $dyed_type = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][3]);
				if (isset($excel->sheets[0]['cells'][$i][4])) $challan_no = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][4]);
				if (isset($excel->sheets[0]['cells'][$i][5])) $store_name = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][5]);
				if (isset($excel->sheets[0]['cells'][$i][6])) $currency_name = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][6]);
				if (isset($excel->sheets[0]['cells'][$i][7])) $source_name  =  str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][7]);
				if (isset($excel->sheets[0]['cells'][$i][8])) $yarn_count = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][8]);
				if (isset($excel->sheets[0]['cells'][$i][9])) $composition_name = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][9]);
				if (isset($excel->sheets[0]['cells'][$i][10])) $yarn_type_name = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][10]);
				if (isset($excel->sheets[0]['cells'][$i][11])) $color = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][11]);
				if (isset($excel->sheets[0]['cells'][$i][12])) $lot_batch = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][12]);
				if (isset($excel->sheets[0]['cells'][$i][13])) $brand = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][13]);
				if (isset($excel->sheets[0]['cells'][$i][14])) $recv_qty = $excel->sheets[0]['cells'][$i][14];
				if (isset($excel->sheets[0]['cells'][$i][15])) $uom = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][15]);
				if (isset($excel->sheets[0]['cells'][$i][16])) $rate = $excel->sheets[0]['cells'][$i][16];
				if (isset($excel->sheets[0]['cells'][$i][17])) $no_of_bag = $excel->sheets[0]['cells'][$i][17];
				if (isset($excel->sheets[0]['cells'][$i][18])) $no_of_cone_per_bag = $excel->sheets[0]['cells'][$i][18];
				if (isset($excel->sheets[0]['cells'][$i][19])) $no_of_loose_cone = $excel->sheets[0]['cells'][$i][19];
				if (isset($excel->sheets[0]['cells'][$i][20])) $weight_per_bag = $excel->sheets[0]['cells'][$i][20];
				if (isset($excel->sheets[0]['cells'][$i][21])) $wght_cone = $excel->sheets[0]['cells'][$i][21];
				if (isset($excel->sheets[0]['cells'][$i][22])) $floor = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][22]);
				if (isset($excel->sheets[0]['cells'][$i][23])) $room = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][23]);
				if (isset($excel->sheets[0]['cells'][$i][24])) $rack = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][24]);
				if (isset($excel->sheets[0]['cells'][$i][25])) $shelf = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][25]);
				if (isset($excel->sheets[0]['cells'][$i][26])) $bin_box = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][26]);
				if (isset($excel->sheets[0]['cells'][$i][27])) $remarks = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][27]);
				if (isset($excel->sheets[0]['cells'][$i][28])) $receive_date = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][28]);
				if (isset($excel->sheets[0]['cells'][$i][29])) $excel_prod_id = str_replace($str_rep, ' ', $excel->sheets[0]['cells'][$i][29]);

				$all_data_arr[$i][1]['company'] = trim($company);
				$all_data_arr[$i][2]['supplier'] = trim($supplier);
				$all_data_arr[$i][3]['dyed_type'] = trim($dyed_type);
				$all_data_arr[$i][4]['challan_no'] = trim($challan_no);
				$all_data_arr[$i][5]['store_name'] = trim($store_name);
				$all_data_arr[$i][6]['currency_name'] = trim($currency_name);
				$all_data_arr[$i][7]['source_name'] = trim($source_name);
				$all_data_arr[$i][8]['yarn_count'] = trim($yarn_count);
				$all_data_arr[$i][9]['composition_name'] = trim($composition_name);
				$all_data_arr[$i][10]['yarn_type_name'] = trim($yarn_type_name);
				$all_data_arr[$i][11]['color'] = trim($color);
				$all_data_arr[$i][12]['lot_batch'] = trim($lot_batch);
				$all_data_arr[$i][13]['brand'] = trim($brand);
				$all_data_arr[$i][14]['recv_qty'] = trim($recv_qty);
				$all_data_arr[$i][15]['uom'] = trim($uom);
				$all_data_arr[$i][16]['rate'] = trim($rate);
				$all_data_arr[$i][17]['no_of_bag'] = trim($no_of_bag);
				$all_data_arr[$i][18]['no_of_cone_per_bag'] = trim($no_of_cone_per_bag);
				$all_data_arr[$i][19]['no_of_loose_cone'] = trim($no_of_loose_cone);
				$all_data_arr[$i][20]['weight_per_bag'] = trim($weight_per_bag);
				$all_data_arr[$i][21]['wght_cone'] = trim($wght_cone);
				$all_data_arr[$i][22]['floor'] = trim($floor);
				$all_data_arr[$i][23]['room'] = trim($room);
				$all_data_arr[$i][24]['rack'] = trim($rack);
				$all_data_arr[$i][25]['shelf'] = trim($shelf);
				$all_data_arr[$i][26]['bin_box'] = trim($bin_box);
				$all_data_arr[$i][27]['remarks'] = trim($remarks);
				$all_data_arr[$i][28]['receive_date'] = trim($receive_date);
				$all_data_arr[$i][29]['excel_prod_id'] = (int) trim($excel_prod_id);
			}
		}

		$current_date = date("Y-m-d");
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$issue_purpose_arr = array_flip($yarn_issue_purpose);
		$currency_arr = array_flip($currency);
		$source_arr = array_flip($source);
		$composition_arr = array_flip($composition);
		$yarn_type_arr = array_flip($yarn_type);
		$unit_of_measurement_arr = array_flip($unit_of_measurement);

		$sql_prod_res = sql_select("select id as ID, company_id as COMPANY_ID, item_category_id as ITEM_CATEGORY_ID, supplier_id as SUPPLIER_ID, yarn_count_id as YARN_COUNT_ID, yarn_comp_type1st as YARN_COMP_TYPE1ST, yarn_comp_percent1st as YARN_COMP_PERCENT1ST, yarn_comp_type2nd as YARN_COMP_TYPE2ND, yarn_comp_percent2nd as YARN_COMP_PERCENT2ND, yarn_type as YARN_TYPE, color as COLOR,unit_of_measure as UNIT_OF_MEASURE, current_stock as CURRENT_STOCK, allocated_qnty  as ALLOCATED_QNTY, lot as LOT, avg_rate_per_unit as AVG_RATE_PER_UNIT, stock_value as STOCK_VALUE, conversion_factor as CONVERSION_FACTOR from product_details_master where status_active=1 and is_deleted=0 and item_category_id=1");
		$product_data_arr = $duplicate_product_data_arr = $product_id_data_arr = array();

		foreach ($sql_prod_res as $val) {
			$key = trim($val['COMPANY_ID']) . '**' . trim($val['ITEM_CATEGORY_ID']) . '**' . trim($val['SUPPLIER_ID']) . '**' . trim($val['YARN_COUNT_ID']) . '**' . trim($val['YARN_COMP_TYPE1ST']) . '**' . trim($val['YARN_COMP_PERCENT1ST']) . '**' . trim($val['YARN_COMP_TYPE2ND']) . '**' . trim($val['YARN_COMP_PERCENT2ND']) . '**' . trim($val['YARN_TYPE']) . '**' . trim($val['COLOR']) . '**' . trim($val['LOT']);
			$product_data_arr[$key] = $val['ID'];
			$duplicate_product_data_arr[$key] = $val['ID'];

			$product_id_data_arr[$val['ID']]['COMPANY_ID'] = $val['COMPANY_ID'];
			$product_id_data_arr[$val['ID']]['SUPPLIER_ID'] = $val['SUPPLIER_ID'];
			$product_id_data_arr[$val['ID']]['UNIT_OF_MEASURE'] = $val['UNIT_OF_MEASURE'];
			$product_id_data_arr[$val['ID']]['CURRENT_STOCK'] = $val['CURRENT_STOCK'];
			$product_id_data_arr[$val['ID']]['ALLOCATED_QNTY'] = $val['ALLOCATED_QNTY'];
			$product_id_data_arr[$val['ID']]['AVG_RATE_PER_UNIT'] = $val['AVG_RATE_PER_UNIT'];
			$product_id_data_arr[$val['ID']]['STOCK_VALUE'] = $val['STOCK_VALUE'];
			$product_id_data_arr[$val['ID']]['CONVERSION_FACTOR'] = $val['CONVERSION_FACTOR'];
		}
		//echo '<pre>';print_r($product_data_arr);die;

		$field_array_receive = "id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, company_id, receive_basis, receive_purpose, receive_date, challan_no, store_id, exchange_rate, currency_id, supplier_id, source, inserted_by, insert_date";
		$field_array_trans = "id, mst_id, receive_basis, company_id, supplier_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, order_uom, order_qnty, order_rate, order_ile, order_ile_cost, order_amount, cons_uom, cons_quantity, cons_rate, cons_ile, cons_ile_cost, cons_amount, balance_qnty, balance_amount, no_of_bags, cone_per_bag, no_loose_cone, weight_per_bag, weight_per_cone, floor_id, room, rack, self, bin_box, dyeing_color_id, remarks, is_excel, inserted_by, insert_date";
		$field_array_prod = "id, company_id, supplier_id, item_category_id, product_name_details, lot, unit_of_measure, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, dyed_type, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, allocated_qnty, available_qnty, is_excel, inserted_by, insert_date";
		$field_array_prod_update = "current_stock*available_qnty*avg_rate_per_unit*stock_value*supplier_id*is_excel*store_id*updated_by*update_date";

		$data_array_receive = $data_array_trans = $data_array_prod = '';
		$duplicate_product_arr = array();
		$row_num_excel = 1;
		$is_excel = 1;
		$prod_id_update_from_excel = 2;

		foreach ($all_data_arr as $column_val) {
			$row_num_excel++;

			$excel_prod_id = (int)$column_val[29]['excel_prod_id'];

			if ($excel_prod_id > 0) {
				$company_id = $product_id_data_arr[$excel_prod_id]['COMPANY_ID'];
				$supplier_id = $product_id_data_arr[$excel_prod_id]['SUPPLIER_ID'];
				$uom_id = $product_id_data_arr[$excel_prod_id]['UNIT_OF_MEASURE'];
			} else {
				$company_id = $company_library[$column_val[1]['company']];
				$supplier_id = (int) $supplier_arr[$company_id][$column_val[2]['supplier']];
				$uom_id = $unit_of_measurement_arr[$column_val[15]['uom']];
			}



			if ($excel_prod_id < 1) {
				if ($column_val[1]['company'] == "" && $company_id == "") {
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Company Name [' . $column_val[1]["company"] . '] and Excel row number [' . $row_num_excel . ']</p>';
					oci_rollback($con);
					disconnect($con);
					die;
				}

				if ($supplier_id < 1) {
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Supplier Name [' . $column_val[2]["supplier"] . '] and Excel row number [' . $row_num_excel . ']</p>';
					oci_rollback($con);
					disconnect($con);
					die;
				}

				$yarn_count_id = $yarn_count_library[$column_val[8]['yarn_count']];
				if ($column_val[8]['yarn_count'] == "" || $yarn_count_id == "") {
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Yarn Count Name [' . $column_val[8]["yarn_count"] . '] and Excel row number [' . $row_num_excel . ']</p>';
					oci_rollback($con);
					disconnect($con);
					die;
				}

				$composition_id = $composition_arr[$column_val[9]['composition_name']];
				if ($column_val[9]['composition_name'] == "" || $composition_id == "") {
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Composition Name [' . $column_val[9]["composition_name"] . '] and Excel row number [' . $row_num_excel . ']</p>';
					oci_rollback($con);
					disconnect($con);
					die;
				}

				$yarn_type_id = $yarn_type_arr[$column_val[10]['yarn_type_name']];
				if ($column_val[10]['yarn_type_name'] == "" || $yarn_type_id == "") {
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Yarn Type Name [' . $column_val[10]["yarn_type_name"] . '] and Excel row number [' . $row_num_excel . ']</p>';
					oci_rollback($con);
					disconnect($con);
					die;
				}

				$color_id = $color_library[$column_val[11]['color']];
				if ($column_val[11]['color'] == "" || $column_val[11]['color'] != 'GREY') {
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct color Name [' . $column_val[11]["color"] . '] and Excel row number [' . $row_num_excel . '] and ' . $color_id . '</p>';
					oci_rollback($con);
					disconnect($con);
					die;
				}

				$lot_batch = $column_val[12]['lot_batch'];
				if ($lot_batch == "") {
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Lot/Batch [' . $lot_batch . '] and Excel row number [' . $row_num_excel . ']</p>';
					oci_rollback($con);
					disconnect($con);
					die;
				}

				$brand_name = $column_val[13]['brand'];
				if ($brand_name == "") {
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Brand Name [' . $brand_name . '] and Excel row number [' . $row_num_excel . ']</p>';
					oci_rollback($con);
					disconnect($con);
					die;
				}
				if ($brand_name != "") {
					if (!in_array($brand_name, $new_array_brand)) {
						$brand_id = return_id($brand_name, $brand_library, "lib_brand", "id,brand_name", "1");
						$new_array_brand[$brand_id] = $brand_name;
					} else $brand_id = array_search($brand_name, $new_array_brand);
				} else $brand_id = 0;

				$uom_id = $unit_of_measurement_arr[$column_val[15]['uom']];
				if ($column_val[15]['uom'] == "" || $uom_id != 12) {
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct UOM [' . $column_val[15]["uom"] . '] and Excel row number [' . $row_num_excel . ']</p>';
					oci_rollback($con);
					disconnect($con);
					die;
				}
			}

			$stock_qty = $column_val[14]['recv_qty'];
			if ($stock_qty <= 0 || $stock_qty == "") {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Stock Quantity Always Greater Than Zero [' . $column_val[14]['recv_qty'] . '] and Excel row number [' . $row_num_excel . ']</p>';
				oci_rollback($con);
				disconnect($con);
				die;
			}

			$purpose_id = $issue_purpose_arr[$column_val[3]['dyed_type']];
			if ($column_val[3]['dyed_type'] == "" || $purpose_id != 16) {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Dyed Type [' . $column_val[3]["dyed_type"] . '] and Excel row number [' . $row_num_excel . ']</p>';
				oci_rollback($con);
				disconnect($con);
				die;
			}

			$challan_no = $column_val[4]['challan_no'];
			if ($challan_no == "") {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up challan_no [' . $challan_no . '] and Excel row number [' . $row_num_excel . ']</p>';
				oci_rollback($con);
				disconnect($con);
				die;
			}

			$store_id = $store_arr[$company_id][$column_val[5]['store_name']];
			if ($column_val[5]['store_name'] == "" || $store_id == "") {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Store Name [' . $column_val[5]["store_name"] . '] and Excel row number [' . $row_num_excel . ']</p>';
				oci_rollback($con);
				disconnect($con);
				die;
			}

			$currency_id = $currency_arr[$column_val[6]['currency_name']];
			if ($column_val[6]['currency_name'] == "" || $currency_id == "") {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Currency Name [' . $column_val[6]["currency_name"] . '] and Excel row number [' . $row_num_excel . ']</p>';
				oci_rollback($con);
				disconnect($con);
				die;
			}

			$source_id = $source_arr[$column_val[7]['source_name']];
			if ($column_val[7]['source_name'] == "" || $source_id == "") {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Source Name [' . $column_val[7]["source_name"] . '] and Excel row number [' . $row_num_excel . ']</p>';
				oci_rollback($con);
				disconnect($con);
				die;
			}

			$rate = $column_val[16]['rate'];
			if ($rate <= 0 || $rate == "") {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Rate Always Greater Than Zero [' . $column_val[16]['rate'] . '] and Excel row number [' . $row_num_excel . ']</p>';
				oci_rollback($con);
				disconnect($con);
				die;
			}

			$no_of_bag = $column_val[17]['no_of_bag'];
			$no_of_cone_per_bag = $column_val[18]['no_of_cone_per_bag'];
			$no_of_loose_cone = $column_val[19]['no_of_loose_cone'];
			$weight_per_bag = $column_val[20]['weight_per_bag'];
			$wght_cone = $column_val[21]['wght_cone'];
			$floor = $column_val[22]['floor'];
			$room = $column_val[23]['room'];
			$rack = $column_val[24]['rack'];
			$shelf = $column_val[25]['shelf'];
			$bin_box = $column_val[26]['bin_box'];
			$remarks = $column_val[27]['remarks'];
			$receive_date = $column_val[28]['receive_date'];
			//$excel_prod_id= (int) $column_val[29]['excel_prod_id']; 

			if ($receive_date <= 0 || $receive_date == "") {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill Up Correct Receive Date [' . $column_val[28]['receive_date'] . '] and Excel row number [' . $row_num_excel . ']</p>';
				oci_rollback($con);
				disconnect($con);
				die;
			}

			//echo $receive_date.'system';die;	
			$product_data = $company_id . '**1**' . $supplier_id . '**' . $yarn_count_id . '**' . $composition_id . '**100**0**0**' . $yarn_type_id . '**' . $color_id . '**' . $lot_batch;

			$exit_product_id = "";

			if ($excel_prod_id > 0) // if provide prod id in excel
			{
				$exit_product_id = $excel_prod_id;
			} else {

				if (array_key_exists($product_data, $product_data_arr)) // if not provide product id
				{
					$exit_product_id = $product_data_arr[$product_data];
				}
			}

			//echo $exit_product_id."system tesing"; echo $flag = 0; die();

			//echo $product_id.'system';die; $prod_id

			if ($exit_product_id != "") //update item with stock
			{
				if ($db_type == 0) {
					$receive_date = change_date_format($receive_date, "Y-m-d", "-", 1);
					$conversion_date = $receive_date;
				} else {
					$receive_date = change_date_format($receive_date, "d-M-y", "-", 1);
					$conversion_date = $receive_date;
				}
				$exchange_rate = set_conversion_rate($currency_id, $conversion_date);

				$max_trans_query = sql_select("SELECT MAX ( CASE WHEN transaction_type IN (2, 3, 6) THEN id ELSE NULL END) AS max_id, max(transaction_date) as max_date from inv_transaction where prod_id=$product_id and store_id=$store_id and item_category=1 and status_active=1");

				if (!empty($max_trans_query)) {
					$max_transaction_date = $max_trans_query[0][csf('max_date')];

					if ($max_transaction_date != "") {
						$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
						$receive_date = date("Y-m-d", strtotime($receive_date));
						if ($receive_date < $max_transaction_date) {
							echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Receive Date Can not Be Less Than Last Transaction Date Of This product \nReceive Date $receive_date \nLast Transaction Date $max_transaction_date[' . $product_id . '] and Excel row number [' . $row_num_excel . ']</p>';
							disconnect($con);
							die;
						}
					}
				}

				$presentStock = $presentStockValue = $presentAvgRate = 0;
				$presentStock		  = $product_id_data_arr[$exit_product_id]['CURRENT_STOCK'];
				$presentStockValue	  = $product_id_data_arr[$exit_product_id]['STOCK_VALUE'];
				$presentAllocatedQnty = $product_id_data_arr[$exit_product_id]['ALLOCATED_QNTY'];
				$presentAvgRate		  = $product_id_data_arr[$exit_product_id]['AVG_RATE_PER_UNIT'];
				$conversion_factor	  = $product_id_data_arr[$exit_product_id]['CONVERSION_FACTOR'];

				$txt_receive_qty = $stock_qty;
				$txt_rate = $rate;
				$txt_amount = $txt_receive_qty * $txt_rate;
				$ile = $ile_cost = 0;
				//echo $txt_rate.'**'.$ile_cost.'**'.$exchange_rate.'**'.$conversion_factor;					
				$domestic_rate = return_domestic_rate($txt_rate, $ile_cost, $exchange_rate, $conversion_factor);
				//echo $domestic_rate.'system';
				$cons_rate = number_format($domestic_rate, $dec_place[3], ".", "");
				if ($conversion_factor > 0) {
					$con_quantity = $conversion_factor * $txt_receive_qty;
				} else {
					$con_quantity = $txt_receive_qty;
				}

				$con_amount 	= $cons_rate * $con_quantity;


				$stock_value 	= $cons_rate * $con_quantity;
				$currentStock   = $presentStock + $con_quantity;
				$available_qnty = ($currentStock - $presentAllocatedQnty);

				$StockValue = $avgRate = 0;
				if ($currentStock != 0) {
					$StockValue	 = number_format($presentStockValue + $stock_value, $dec_place[4], ".", "");
					$avgRate	 = number_format($StockValue / $currentStock, $dec_place[4], ".", "");
				}

				$updateID_array[] = $exit_product_id;
				$update_product_data[$exit_product_id] = explode("*", ("" . $currentStock . "*" . $available_qnty . "*" . $avgRate . "*" . $StockValue . "*" . $supplier_id . "*" . $prod_id_update_from_excel . "*" . $store_id . "*" . $user_id . "*'" . $pc_date_time . "'"));
			} else //new item insert with stock
			{
				// Duplicate Product Check
				if (array_key_exists($product_data, $duplicate_product_data_arr)) {
					$duplicate_prod_keys = $column_val[1]['company'] . "**1**" . $column_val[2]['supplier'] . "**" . $column_val[8]['yarn_count'] . "**" . $column_val[9]['composition_name'] . "**100**0**0**" . $column_val[10]['yarn_type_name'] . "**" . $column_val[11]['color'] . "**" . $column_val[12]['lot_batch'];
					$duplicate_product_arr[] = $duplicate_prod_keys;
					continue;
				}

				//$yarn_count.','.$composition.','.$ytype.','.$color;
				$product_name_details = $column_val[8]['yarn_count'] . " " . $column_val[9]['composition_name'] . " 100 " . $column_val[10]['yarn_type_name'] . " " . $column_val[11]['color'];

				$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);

				$duplicate_product_data_arr[$product_data] = $new_prod_id; // Push item from excel data in array
				$stock_value = $stock_qty * $rate;

				$receive_basis = 4;

				if ($db_type == 0) {
					$receive_date = change_date_format($receive_date, "Y-m-d", "-", 1);
					$conversion_date = $receive_date;
				} else {
					$receive_date = change_date_format($receive_date, "d-M-y", "-", 1);
					$conversion_date = $receive_date;
				}
				$exchange_rate = set_conversion_rate($currency_id, $conversion_date);


				if ($receive_date != "") {
					$current_date = change_date_format($current_date, "Y-m-d", "-", 1);
					//echo str $receive_date.'susyem'.$current_date;die; 
					if (strtotime($receive_date) > strtotime($current_date)) {
						echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Receive Date Can not be Greter than Current Date [' . $receive_date . '] and Excel row number [' . $row_num_excel . ']</p>';
						oci_rollback($con);
						disconnect($con);
						die;
					}
				}

				$txt_receive_qty = $stock_qty;
				$txt_rate = $rate;
				$txt_amount = $txt_receive_qty * $txt_rate;
				$ile = $ile_cost = 0;
				$conversion_factor = 1; // yarn always KG
				//echo $txt_rate.'**'.$ile_cost.'**'.$exchange_rate.'**'.$conversion_factor;

				$domestic_rate = return_domestic_rate($txt_rate, $ile_cost, $exchange_rate, $conversion_factor);
				$cons_rate = number_format($domestic_rate, $dec_place[3], ".", "");

				if ($conversion_factor > 0) {
					$con_quantity = $conversion_factor * $txt_receive_qty;
				} else {
					$con_quantity = $txt_receive_qty;
				}

				$con_amount = $cons_rate * $con_quantity;

				$con_ile = $con_ile_cost = 0;
				$cbo_floor = $cbo_room = $txt_rack = $txt_shelf = $cbo_bin = 0;

				$allocated_qnty = 0;
				$available_qnty = $con_quantity;

				if ($data_array_prod != '') $data_array_prod .= ",";
				$data_array_prod .= "(" . $new_prod_id . ",'" . $company_id . "','" . $supplier_id . "',1,'" . $product_name_details . "','" . $lot_batch . "','" . $uom_id . "','" . $yarn_count_id . "','" . $composition_id . "',100,0,0,'" . $yarn_type_id . "','" . $color_id . "','2','" . $cons_rate . "','" . $con_quantity . "','" . $con_quantity . "','" . number_format($con_amount, $dec_place[4], ".", "") . "','" . $allocated_qnty . "','" . $available_qnty . "','" . $is_excel . "','" . $user_id . "','" . $pc_date_time . "')";
			}

			$receive_basis = 4; //.$supplier_id."**"
			$master_key = $receive_basis . "**" . $purpose_id . "**" . $receive_date . "**" . $challan_no . "**" . $store_id . "**" . $currency_id . "**" . $exchange_rate . "**" . $source_id;

			$productId = ($exit_product_id != "") ? $exit_product_id : $new_prod_id;
			$data_prepare_arr[$company_id][$master_key][$productId]['receive_date']    = $receive_date;
			$data_prepare_arr[$company_id][$master_key][$productId]['challan_no'] 	    = $challan_no;
			$data_prepare_arr[$company_id][$master_key][$productId]['store_id'] 	    = $store_id;
			$data_prepare_arr[$company_id][$master_key][$productId]['exchange_rate']   = $exchange_rate;
			$data_prepare_arr[$company_id][$master_key][$productId]['currency_id']     = $currency_id;
			$data_prepare_arr[$company_id][$master_key][$productId]['supplier_id']     = $supplier_id;
			$data_prepare_arr[$company_id][$master_key][$productId]['source_id']       = $source_id;

			$data_prepare_arr[$company_id][$master_key][$productId]['prod_id']         = $productId;
			$data_prepare_arr[$company_id][$master_key][$productId]['brand_id']        = $brand_id;
			$data_prepare_arr[$company_id][$master_key][$productId]['order_uom']       = $uom_id;
			$data_prepare_arr[$company_id][$master_key][$productId]['order_qnty']      = $txt_receive_qty;
			$data_prepare_arr[$company_id][$master_key][$productId]['order_rate']      = $txt_rate;
			$data_prepare_arr[$company_id][$master_key][$productId]['order_ile']       = $ile;
			$data_prepare_arr[$company_id][$master_key][$productId]['order_ile_cost']  = $ile_cost;
			$data_prepare_arr[$company_id][$master_key][$productId]['order_amount']    = $txt_amount;
			$data_prepare_arr[$company_id][$master_key][$productId]['cons_uom']    		= $uom_id;
			$data_prepare_arr[$company_id][$master_key][$productId]['cons_quantity']    = $con_quantity;
			$data_prepare_arr[$company_id][$master_key][$productId]['cons_rate']    	 = $cons_rate;
			$data_prepare_arr[$company_id][$master_key][$productId]['cons_ile']    	 = $con_ile;
			$data_prepare_arr[$company_id][$master_key][$productId]['cons_ile_cost']    = $con_ile_cost;
			$data_prepare_arr[$company_id][$master_key][$productId]['cons_amount']    	 = number_format($con_amount, $dec_place[4], ".", "");
			$data_prepare_arr[$company_id][$master_key][$productId]['balance_qnty']    	 = $con_quantity;
			$data_prepare_arr[$company_id][$master_key][$productId]['balance_amount']    	 = number_format($con_amount, $dec_place[4], ".", "");
			$data_prepare_arr[$company_id][$master_key][$productId]['no_of_bags']    	 	 = $no_of_bag;
			$data_prepare_arr[$company_id][$master_key][$productId]['cone_per_bag']    	 = $no_of_cone_per_bag;
			$data_prepare_arr[$company_id][$master_key][$productId]['no_loose_cone']    	 = $no_of_loose_cone;
			$data_prepare_arr[$company_id][$master_key][$productId]['weight_per_bag']    	 = $weight_per_bag;
			$data_prepare_arr[$company_id][$master_key][$productId]['weight_per_cone']      = $wght_cone;
			$data_prepare_arr[$company_id][$master_key][$productId]['floor_id']    	 	 = $floor;
			$data_prepare_arr[$company_id][$master_key][$productId]['room']    		 	 = $room;
			$data_prepare_arr[$company_id][$master_key][$productId]['rack']    	 		 = $rack;
			$data_prepare_arr[$company_id][$master_key][$productId]['self']    	 		 = $shelf;
			$data_prepare_arr[$company_id][$master_key][$productId]['bin_box']    	 	 	= $bin_box;
			$data_prepare_arr[$company_id][$master_key][$productId]['dyeing_color_id']     = $color_id;
			$data_prepare_arr[$company_id][$master_key][$productId]['remarks']    	 	    = $remarks;
			$data_prepare_arr[$company_id][$master_key][$productId]['is_excel']    	 	 = $is_excel;
		}
		//echo "10** insert into inv_transaction ($field_array_trans) values $data_array_trans";
		//echo '<pre>';print_r($data_prepare_arr);die;

		foreach ($data_prepare_arr as $company_id => $msterDataArr) {
			foreach ($msterDataArr as $master_key => $dataArr) {
				//echo $master_key."<br>"; //4**16**02-Jul-2022**122**57**517**2**85.6**3
				$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
				$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con, 1, $company_id, 'YRV', 1, date("Y", time()), 1));
				//$supplier_id,
				list($receive_basis, $purpose_id, $receive_date, $challan_no, $store_id, $currency_id, $exchange_rate, $source_id) = explode("**", $master_key);

				if ($data_array_receive != '') $data_array_receive .= ",";
				$data_array_receive .= "(" . $id . ",'" . $new_recv_number[1] . "','" . $new_recv_number[2] . "','" . $new_recv_number[0] . "',1,1,'" . $company_id . "','" . $receive_basis . "','" . $purpose_id . "','" . $receive_date . "','" . $challan_no . "','" . $store_id . "','" . $exchange_rate . "','" . $currency_id . "','" . $supplier_id . "','" . $source_id . "','" . $user_id . "','" . $pc_date_time . "')";

				foreach ($dataArr as $productId => $details_row) {
					$trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);

					if ($data_array_trans != '') $data_array_trans .= ",";
					$data_array_trans .= "(" . $trans_id . "," . $id . ",'" . $receive_basis . "','" . $company_id . "','" . $supplier_id . "','" . $productId . "',1,1,'" . $receive_date . "','" . $store_id . "','" . $details_row['brand_id'] . "','" . $details_row['order_uom'] . "','" . $details_row['order_qnty'] . "','" . $details_row['order_rate'] . "','" . $details_row['order_ile'] . "','" . $details_row['order_ile_cost'] . "','" . $details_row['order_amount'] . "','" . $details_row['order_uom'] . "','" . $details_row['cons_quantity'] . "','" . $details_row['cons_rate'] . "','" . $details_row['cons_ile'] . "','" . $details_row['cons_ile_cost'] . "','" . $details_row['cons_amount'] . "','" . $details_row['balance_qnty'] . "','" . $details_row['balance_amount'] . "','" . $details_row['no_of_bags'] . "','" . $details_row['cone_per_bag'] . "','" . $details_row['no_loose_cone'] . "','" . $details_row['weight_per_bag'] . "','" . $details_row['weight_per_cone'] . "','" . $details_row['floor_id'] . "','" . $details_row['room'] . "','" . $details_row['rack'] . "','" . $details_row['self'] . "','" . $details_row['bin_box'] . "','" . $details_row['dyeing_color_id'] . "','" . $details_row['remarks'] . "','" . $details_row['is_excel'] . "','" . $user_id . "','" . $pc_date_time . "')";
				}
			}
		}

		$flag = $rID = $rID2 = $rID3 = $prodUpdate = 1;

		if ($data_array_prod != "") {
			//echo "10** insert into product_details_master ($field_array_prod) values $data_array_prod"; die();
			$rID = sql_insert("product_details_master", $field_array_prod, $data_array_prod, 0);
			if ($flag == 1) {
				if ($rID) $flag = 1;
				else $flag = 0;
			}
		}

		if ($data_array_receive != "") {
			//echo "10** insert into inv_receive_master ($field_array_receive) values $data_array_receive"; die();
			$rID2 = sql_insert("inv_receive_master", $field_array_receive, $data_array_receive, 0);
			if ($flag == 1) {
				if ($rID2) $flag = 1;
				else $flag = 0;
			}
		}

		if ($data_array_trans != "") {
			//echo "10** insert into inv_transaction ($field_array_trans) values $data_array_trans";die();
			$rID3 = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);
			if ($flag == 1) {
				if ($rID3) $flag = 1;
				else $flag = 0;
			}
		}

		if (!empty($update_product_data)) {
			$prodUpdate = execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array_prod_update, $update_product_data, $updateID_array));
			//echo bulk_update_sql_statement("product_details_master", "id", $field_array_prod_update, $update_product_data, $updateID_array);

			if ($flag == 1) {
				if ($prodUpdate) $flag = 1;
				else $flag = 0;
			}
		}


		$all_datas = '';
		if (!empty($duplicate_product_arr)) {
			$all_datas .= '<div style="width:100%">';
			$all_datas .= '<table border="0" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" style="font-size: 16px;">
					<tr>
						<td colspan="10"><strong>Duplicate Item List:</strong></td>
					</tr>
				</table>';
			$all_datas .= '<table border="1" class="rpt_table" rules="all" width="1110" cellpadding="0" cellspacing="0" style="background-color:#bbb; font-size: 14px;">
					<tr>
						<th width="30">SL</th>
						<th width="200">Company</th>
						<th width="150">Supplier</th>
						<th width="100">Yarn Count</th>
						<th width="150">Composition</th>
						<th width="100">Yarn Type</th>
						<th width="100">Color</th>
						<th width="100">Lot Batch</th>
					</tr>
				</table>
				<table border="1" class="rpt_table" rules="all" width="1110" cellpadding="0" cellspacing="0" style="font-size: 14px;">';
			$i = 1;
			foreach ($duplicate_product_arr as $value) {
				$exp_val = explode('**', $value);
				$all_datas .= '<tr>
							<td width="30">' . $i . '</td>
							<td width="200" style="word-break:break-all">' . $exp_val[0] . '</td>
							<td width="150" style="word-break:break-all">' . $exp_val[2] . '</td>
							<td width="100" style="word-break:break-all">' . $exp_val[3] . '</td>
							<td width="150" style="word-break:break-all">' . $exp_val[4] . '</td>
							<td width="100" style="word-break:break-all">' . $exp_val[8] . '</td>
							<td width="100" style="word-break:break-all">' . $exp_val[9] . '</td>
							<td width="100" style="word-break:break-all">' . $exp_val[10] . '</td>
						</tr>';
				$i++;
			}
			$all_datas .= '</table></div>';
		}

		//echo "10** insert into product_details_master ($field_array) values $data_array";die;
		//echo "10** insert into inv_receive_master ($field_array_receive) values $data_array_receive"; die();
		//echo "10** insert into inv_transaction ($field_array_trams) values $data_array_trans";die;
		//echo "10**".bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$update_product_data,$updateID_array); die();
		/* echo $rID . "#" . $rID2 . "#" . $rID3 . "#" . $prodUpdate . "#" . $flag;
		oci_rollback($con);
		disconnect($con);
		die(); */


		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File Upload Successfully...</p><br/>';
				echo $all_datas;
			} else {
				mysql_query("ROLLBACK");
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File is not Uploaded...</p><br/>';
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File Upload Successfully...</p><br/>';
				echo $all_datas;
			} else {
				oci_rollback($con);
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File is not Uploaded...</p><br/>';
			}
		}
		disconnect($con);
		die;
	} else {
		echo "Failed";
	}
	die;
}
