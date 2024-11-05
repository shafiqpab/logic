<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:../../../login.php");
extract($_REQUEST);
include('../../../includes/common.php');

//---------------------------------------------------- Start

if ($action == "show_po_active_listview") {
	$arr = array(0 => $order_status, 11 => $row_status);
	$sql = "select is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,DATEDIFF(shipment_date,po_received_date) date_diff,status_active,id from  wo_po_break_down  where   status_active=1 and is_deleted=0 and job_no_mst='$data'";

	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Status", "90,130,80,80,80,80,80,80,80,80,50,70", "1050", "220", 0, $sql, "get_php_form_data", "id", 1, 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,status_active", $arr, "is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,status_active", "../woven_order/requires/woven_order_entry_controller/", '', '0,0,3,3,3,1,2,2,2,2,1');
}
if ($action == "load_drop_down_brand") {
	$width = 172;
	echo create_drop_down("cbo_brand_id", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC", "id,brand_name", 1, "--Brand--", $selected, "fnc_load_sales_target_data();");
	exit();
}

if ($action == "show_deleted_po_active_listview") {

	$arr = array(0 => $order_status, 11 => $row_status);
	$sql = "select is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,DATEDIFF(shipment_date,po_received_date) date_diff,status_active,id from  wo_po_break_down  where   status_active<>1 and is_deleted<>0 and job_no_mst='$data'";

	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Status", "90,130,80,80,80,80,80,80,80,80,50,70", "1050", "220", 0, $sql, "get_php_form_data", "id", 1, 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,status_active", $arr, "is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,status_active", "../woven_order/requires/woven_order_entry_controller/", '', '0,0,3,3,3,1,2,2,2,2,1');
}


if ($action == "cbo_dealing_merchant") {
	echo create_drop_down("cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name", "id,team_member_name", 1, "-- Select Team Member --", $selected, "");
}

if ($action == "load_drop_down_location") {
	echo create_drop_down("cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select --", $selected, "");
}

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.party_type='' order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
}

if ($action == "load_drop_down_agent") {
	echo create_drop_down("cbo_agent", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3))  order by buyer_name", "id,buyer_name", 1, "-- Select Agent --", $selected, "");
}



if ($action == "get_excess_cut_percent") {
	$data = explode("_", $data);

	$qry_result = sql_select("select slab_rang_start,slab_rang_end,excess_percent from  var_prod_excess_cutting_slab where company_name='$data[1]' and variable_list=2 and status_active=1 and is_deleted=0");
	foreach ($qry_result as $row) {
		if ($data[0] >= $row[csf("slab_rang_start")] && $data[0] <= $row[csf("slab_rang_end")]) {
			echo $row[csf("excess_percent")];
			die;
		}
	}
	echo "0";
	die;
}


if ($action == "create_po_search_list_view") {

	$data = explode('_', $data);
	if ($data[0] != 0) $company = " and a.company_name='$data[0]'";
	else {
		echo "Please Select Company First.";
		die;
	}

	if ($data[1] != 0) $buyer = " and a.buyer_name='$data[1]'";
	else {
		echo "Please Select Buyer First.";
		die;
	}

	//if ($data[2]!=0) $buyer=" and a.buyer_name='$data[2]'"; else { echo "Please Select CHEK First."; die; } die;

	if ($data[3] != "" &&  $data[4] != "") $shipment_date = "and b.shipment_date between '" . change_date_format($data[3], "yyyy-mm-dd", "-") . "' and '" . change_date_format($data[4], "yyyy-mm-dd", "-") . "'";
	else $shipment_date = "";

	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$comp = return_library_array("select id, company_name from lib_company", 'id', 'company_name');

	$arr = array(1 => $comp, 2 => $buyer_arr);

	if ($data[2] == 0) {
		$sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1  and b.status_active=1 $shipment_date $company $buyer order by a.job_no";

		echo  create_list_view("list_view", "Job No,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date", "90,120,100,100,90,90,90,80", "1000", "320", 0, $sql, "get_php_form_data", "id", 1, 1, "0,company_name,buyer_name,0,0,0,0", $arr, "job_no,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", "../contact_details/requires/", '', '0,0,0,0,1,0,1,3');
	} else {
		$sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no from wo_po_details_master a where a.status_active=1  and a.is_deleted=0 $company $buyer order by a.job_no";
		// $sql= "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity from wo_po_details_master a,wo_po_break_down b where a.job_no!=b.job_no_mst and  a.status_active=1  and a.is_deleted=0 $company $buyer $extra_cond"; 
		echo  create_list_view("list_view", "Job No,Company,Buyer Name,Style Ref. No,", "90,120,100,100,90", "1000", "320", 0, $sql, "get_php_form_data", "id", 1, 1, "0,company_name,buyer_name,0,0,0,0", $arr, "job_no,company_name,buyer_name,style_ref_no", "../contact_details/requires/", '', '0,0,0,0,1,0,2,3');
	}
}



//------------------------------------------------------------- reza
if ($action == "save_update_delete_mst") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		// Master part insert-----------------------------------------------
		$cbo_company_id = str_replace("'", "", $cbo_company_id);
		$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
		$cbo_agent = str_replace("'", "", $cbo_agent);
		$cbo_team_leader = str_replace("'", "", $cbo_team_leader);
		$cbo_starting_month = str_replace("'", "", $cbo_starting_month);
		$cbo_starting_year = str_replace("'", "", $cbo_starting_year);
		$txt_total_qty = str_replace("'", "", $txt_total_qty);
		$txt_total_val = str_replace("'", "", $txt_total_val);
		$txt_total_target_mint = str_replace("'", "", $txt_total_alo_prcnt);
		$cbo_brand_id = str_replace("'", "", $cbo_brand_id);

		$style_from_library = return_field_value("style_from_library", "variable_order_tracking", "company_name='$cbo_company_id' and variable_list=83", "style_from_library");
		$mst_id = return_next_id("id", "wo_sales_target_mst", 1);
		$field_array = "id, company_id, buyer_id, agent, team_leader, designation, starting_month, starting_year, total_target_qty, total_target_value, total_target_mint,brand_id, inserted_by, insert_date, status_active, is_deleted";
		$data_array = "(" . $mst_id . "," . $cbo_company_id . "," . $cbo_buyer_name . "," . $cbo_agent . "," . $cbo_team_leader . ",'" . $text_designation_value . "'," . $cbo_starting_month . "," . $cbo_starting_year . "," . $txt_total_qty . "," . $txt_total_val . "," . $txt_total_target_mint . ",'" . $cbo_brand_id . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','1','0')";


		// Details part insert ------------------------------------------------------
		$dtls_id = return_next_id("id", "wo_sales_target_dtls", 1);
		$field_array_dtls = "id, sales_target_mst_id, year_month_name, sales_target_qty, sales_target_value, sales_target_mint, sales_target_date, cm, cm_val_per, rm_val_per, actual_margin_per";

		$k = 1;
		$data_array_dtls = '';
		$data_array_nature_dtls = '';
		for ($i = 0; $i < 12; $i++) {
			if ($k < 13) {
				$month = $cbo_starting_month + $i;
				$yy = $cbo_starting_year;

				$month_year = "month_" . $month . $yy;
				$target_qty = "qty_" . $month . $yy;
				$target_value = "val_" . $month . $yy;
				$target_mint = "mint_" . $month . $yy;

				$cm = "cm_" . $month . $yy;
				$cmval = "cmval_" . $month . $yy;
				$rmval = "rmval_" . $month . $yy;
				$actualmargin = "actualmargin_" . $month . $yy;

				$savedata1 = "savedata_1_" . $month . "_" . $yy;
				$savedata2 = "savedata_2_" . $month . "_" . $yy;
				$savedata3 = "savedata_3_" . $month . "_" . $yy;

				$savedata1 = $$savedata1;
				$savedata2 = $$savedata2;
				$savedata3 = $$savedata3;

				$savedata1 = explode(",", str_replace("'", "", $savedata1));
				foreach ($savedata1 as $sv1) {
					$ex1 = explode("**", $sv1);
					if (!empty($ex1[1]) && !empty($ex1[2])) {
						$nature_dtls_id = return_next_id_by_sequence("wo_sales_target_nature_dtls_seq", "wo_sales_target_nature_dtls", $con);
						if ($data_array_nature_dtls == '') {
							$data_array_nature_dtls = "(" . $nature_dtls_id . "," . $mst_id . ",1," . $month . "," . $yy . "," . $ex1[1] . "," . $ex1[2] . ")";
						} else {
							$data_array_nature_dtls .= ",(" . $nature_dtls_id . "," . $mst_id . ",1," . $month . "," . $yy . "," . $ex1[1] . "," . $ex1[2] . ")";
						}
					}
				}

				$savedata2 = explode(",", str_replace("'", "", $savedata2));
				foreach ($savedata2 as $sv1) {
					$ex1 = explode("**", $sv1);
					if (!empty($ex1[1]) && !empty($ex1[2])) {
						$nature_dtls_id = return_next_id_by_sequence("wo_sales_target_nature_dtls_seq", "wo_sales_target_nature_dtls", $con);
						if ($data_array_nature_dtls == '') {
							$data_array_nature_dtls = "(" . $nature_dtls_id . "," . $mst_id . ",2," . $month . "," . $yy . "," . $ex1[1] . "," . $ex1[2] . ")";
						} else {
							$data_array_nature_dtls .= ",(" . $nature_dtls_id . "," . $mst_id . ",2," . $month . "," . $yy . "," . $ex1[1] . "," . $ex1[2] . ")";
						}
					}
				}

				$savedata3 = explode(",", str_replace("'", "", $savedata3));
				foreach ($savedata3 as $sv1) {
					$ex1 = explode("**", $sv1);
					if (!empty($ex1[1]) && !empty($ex1[2])) {
						$nature_dtls_id = return_next_id_by_sequence("wo_sales_target_nature_dtls_seq", "wo_sales_target_nature_dtls", $con);
						if ($data_array_nature_dtls == '') {
							$data_array_nature_dtls = "(" . $nature_dtls_id . "," . $mst_id . ",3," . $month . "," . $yy . "," . $ex1[1] . "," . $ex1[2] . ")";
						} else {
							$data_array_nature_dtls .= ",(" . $nature_dtls_id . "," . $mst_id . ",3," . $month . "," . $yy . "," . $ex1[1] . "," . $ex1[2] . ")";
						}
					}
				}



				if ($db_type == 0) {
					$target_date = $yy . '-' . $month . "-01";
				} else {
					$target_date = "01-" . $months[$month] . '-' . $yy;
				}
				if ($data_array_dtls == '') {
					$data_array_dtls = "(" . $dtls_id . "," . $mst_id . "," . $$month_year . "," . $$target_qty . "," . $$target_value . "," . $$target_mint . ",'" . $target_date . "'," . $$cm . "," . $$cmval . "," . $$rmval . "," . $$actualmargin . ")";
				} else {
					$data_array_dtls .= ",(" . $dtls_id . "," . $mst_id . "," . $$month_year . "," . $$target_qty . "," . $$target_value . "," . $$target_mint . ",'" . $target_date . "'," . $$cm . "," . $$cmval . "," . $$rmval . "," . $$actualmargin . ")";
				}
				$dtls_id++;
				if ($month == 12) {
					$cbo_starting_month = 0;
					$i = 0;
					$cbo_starting_year = $cbo_starting_year + 1;
				}
			}
			$k++;
		}

		//echo $field_array_dtls; die;

		$rID1 = sql_insert("wo_sales_target_mst", $field_array, $data_array, 0);
		$rID2 = sql_insert("wo_sales_target_dtls", $field_array_dtls, $data_array_dtls, 0);
		$rID3 = 1;
		if (!empty($data_array_nature_dtls)) {
			$field_array_dtls = "id,mst_id,target_type,target_month,target_year,nature_id,target_qty";
			$rID3 = sql_insert("wo_sales_target_nature_dtls", $field_array_dtls, $data_array_nature_dtls, 0);
		}
		//echo "INSERT INTO wo_sales_target_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls.""; die;
		////echo "INSERT INTO pro_roll_details (".$field_array_roll.") VALUES ".$data_array_roll.""; die;
		//echo "update inv_receive_master set(".$field_array_update.")=".$data_array_update; die;

		if ($db_type == 0) {
			if ($rID1 == 1 && $rID2 == 1 && $rID3 == 1) {
				mysql_query("COMMIT");
				echo "0**" . $mst_id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $mst_id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID1 == 1 && $rID2 == 1 && $rID3 == 1) {
				oci_commit($con);
				echo "0**" . $mst_id;
			} else {
				oci_rollback($con);
				echo "10**" . $mst_id . "** INSERT INTO wo_sales_target_nature_dtls (" . $field_array_dtls . ") VALUES " . $data_array_nature_dtls . "";
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 1)   // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		// delete here--------------------------------------------------------------- 
		$update_id = str_replace("'", "", $update_id);
		//echo $update_id; die;
		if ($update_id) {
			$rID1 = execute_query("delete from wo_sales_target_mst where id =" . $update_id . "", 0);
			$rID2 = execute_query("delete from wo_sales_target_dtls where sales_target_mst_id =" . $update_id . "", 0);
			$rID3 = execute_query("delete from wo_sales_target_nature_dtls where mst_id=" . $update_id . "", 0);
		}



		if ($db_type == 0) {
			if ($rID1 == 1 && $rID2 == 1 && $rID3 == 1) {
				mysql_query("COMMIT");
				echo "0**" . $mst_id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $mst_id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID1 == 1 && $rID2 == 1 && $rID3 == 1) {
				oci_commit($con);
				//echo "1**".$mst_id;
			} else {
				oci_rollback($con);
				//echo "10**".$mst_id;
			}
		}
		//Delete end----------------------------------------------

		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		// Master part insert-----------------------------------------------
		$cbo_company_id = str_replace("'", "", $cbo_company_id);
		$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
		$cbo_agent = str_replace("'", "", $cbo_agent);
		$cbo_team_leader = str_replace("'", "", $cbo_team_leader);
		$cbo_starting_month = str_replace("'", "", $cbo_starting_month);
		$cbo_starting_year = str_replace("'", "", $cbo_starting_year);
		$txt_total_qty = str_replace("'", "", $txt_total_qty);
		$txt_total_val = str_replace("'", "", $txt_total_val);
		$txt_total_target_mint = str_replace("'", "", $txt_total_alo_prcnt);
		$cbo_brand_id = str_replace("'", "", $cbo_brand_id);

		$mst_id = return_next_id("id", "wo_sales_target_mst", 1);
		$field_array = "id, company_id, buyer_id, agent, team_leader, designation, starting_month, starting_year, total_target_qty, total_target_value, total_target_mint,brand_id, inserted_by, insert_date, status_active, is_deleted";
		$data_array = "(" . $mst_id . "," . $cbo_company_id . "," . $cbo_buyer_name . "," . $cbo_agent . "," . $cbo_team_leader . ",'" . $text_designation_value . "'," . $cbo_starting_month . "," . $cbo_starting_year . "," . $txt_total_qty . "," . $txt_total_val . "," . $txt_total_target_mint . ",'" . $cbo_brand_id . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','1','0')";


		// Details part insert ------------------------------------------------------
		$dtls_id = return_next_id("id", "wo_sales_target_dtls", 1);
		$field_array_dtls = "id, sales_target_mst_id, year_month_name, sales_target_qty, sales_target_value, sales_target_mint, sales_target_date, cm, cm_val_per, rm_val_per, actual_margin_per";
		$k = 1;
		$data_array_dtls = '';
		$data_array_nature_dtls = '';
		for ($i = 0; $i < 12; $i++) {
			if ($k < 13) {
				$month = $cbo_starting_month + $i;
				$yy = $cbo_starting_year;

				$month_year = "month_" . $month . $yy;
				$target_qty = "qty_" . $month . $yy;
				$target_value = "val_" . $month . $yy;
				$target_mint = "mint_" . $month . $yy;

				$cm = "cm_" . $month . $yy;
				$cmval = "cmval_" . $month . $yy;
				$rmval = "rmval_" . $month . $yy;
				$actualmargin = "actualmargin_" . $month . $yy;

				$savedata1 = "savedata_1_" . $month . "_" . $yy;
				$savedata2 = "savedata_2_" . $month . "_" . $yy;
				$savedata3 = "savedata_3_" . $month . "_" . $yy;

				$savedata1 = $$savedata1;
				$savedata2 = $$savedata2;
				$savedata3 = $$savedata3;

				$savedata1 = explode(",", str_replace("'", "", $savedata1));
				foreach ($savedata1 as $sv1) {
					$ex1 = explode("**", $sv1);
					if (!empty($ex1[1]) && !empty($ex1[2])) {
						$nature_dtls_id = return_next_id_by_sequence("wo_sales_target_nature_dtls_seq", "wo_sales_target_nature_dtls", $con);
						if ($data_array_nature_dtls == '') {
							$data_array_nature_dtls = "(" . $nature_dtls_id . "," . $mst_id . ",1," . $month . "," . $yy . "," . $ex1[1] . "," . $ex1[2] . ")";
						} else {
							$data_array_nature_dtls .= ",(" . $nature_dtls_id . "," . $mst_id . ",1," . $month . "," . $yy . "," . $ex1[1] . "," . $ex1[2] . ")";
						}
					}
				}

				$savedata2 = explode(",", str_replace("'", "", $savedata2));
				foreach ($savedata2 as $sv1) {
					$ex1 = explode("**", $sv1);
					if (!empty($ex1[1]) && !empty($ex1[2])) {
						$nature_dtls_id = return_next_id_by_sequence("wo_sales_target_nature_dtls_seq", "wo_sales_target_nature_dtls", $con);
						if ($data_array_nature_dtls == '') {
							$data_array_nature_dtls = "(" . $nature_dtls_id . "," . $mst_id . ",2," . $month . "," . $yy . "," . $ex1[1] . "," . $ex1[2] . ")";
						} else {
							$data_array_nature_dtls .= ",(" . $nature_dtls_id . "," . $mst_id . ",2," . $month . "," . $yy . "," . $ex1[1] . "," . $ex1[2] . ")";
						}
					}
				}

				$savedata3 = explode(",", str_replace("'", "", $savedata3));
				foreach ($savedata3 as $sv1) {
					$ex1 = explode("**", $sv1);
					if (!empty($ex1[1]) && !empty($ex1[2])) {
						$nature_dtls_id = return_next_id_by_sequence("wo_sales_target_nature_dtls_seq", "wo_sales_target_nature_dtls", $con);
						if ($data_array_nature_dtls == '') {
							$data_array_nature_dtls = "(" . $nature_dtls_id . "," . $mst_id . ",3," . $month . "," . $yy . "," . $ex1[1] . "," . $ex1[2] . ")";
						} else {
							$data_array_nature_dtls .= ",(" . $nature_dtls_id . "," . $mst_id . ",3," . $month . "," . $yy . "," . $ex1[1] . "," . $ex1[2] . ")";
						}
					}
				}

				if ($db_type == 0) {
					$target_date = $yy . '-' . $month . "-01";
				} else {
					$target_date = "01-" . $months[$month] . '-' . $yy;
				}
				if ($data_array_dtls == '') {
					$data_array_dtls = "(" . $dtls_id . "," . $mst_id . "," . $$month_year . "," . $$target_qty . "," . $$target_value . "," . $$target_mint . ",'" . $target_date . "'," . $$cm . "," . $$cmval . "," . $$rmval . "," . $$actualmargin . ")";
				} else {
					$data_array_dtls .= ",(" . $dtls_id . "," . $mst_id . "," . $$month_year . "," . $$target_qty . "," . $$target_value . "," . $$target_mint . ",'" . $target_date . "'," . $$cm . "," . $$cmval . "," . $$rmval . "," . $$actualmargin . ")";
				}
				$dtls_id++;
				if ($month == 12) {
					$cbo_starting_month = 0;
					$i = 0;
					$cbo_starting_year = $cbo_starting_year + 1;
				}
			}
			$k++;
		}


		//echo "10**".$data_array_nature_dtls; die;
		//echo "INSERT INTO wo_sales_target_mst (".$field_array.") VALUES ".$data_array.""; die;
		$rID11 = sql_insert("wo_sales_target_mst", $field_array, $data_array, 1);
		$rID22 = sql_insert("wo_sales_target_dtls", $field_array_dtls, $data_array_dtls, 1);
		$rID3 = 1;
		if (!empty($data_array_nature_dtls)) {

			$field_array_dtls = "id,mst_id,target_type,target_month,target_year,nature_id,target_qty";
			$rID33 = sql_insert("wo_sales_target_nature_dtls", $field_array_dtls, $data_array_nature_dtls, 0);
		}
		// echo "10**".$rID11."==1 &&".$rID22."==1 && ".$rID33."==1 &&". $rID1."==1 &&". $rID2."==1 &&". $rID3; die;

		if ($db_type == 0) {
			if ($rID11 == 1 && $rID22 == 1 || $rID33 == 1 && $rID1 == 1 && $rID2 == 1 && $rID3 == 1) {
				mysql_query("COMMIT");
				echo "0**" . $mst_id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $mst_id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID11 == 1 && $rID22 == 1 || $rID33 == 1 && $rID1 == 1 && $rID2 == 1 && $rID3 == 1) {
				oci_commit($con);
				echo "0**" . $mst_id;
			} else {
				oci_rollback($con);
				echo "10**" . $mst_id . "** INSERT INTO wo_sales_target_nature_dtls (" . $field_array_dtls . ") VALUES " . $data_array_nature_dtls . "";
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 2)   // Delete Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		/*		
				// delete here--------------------------------------------------------------- 
				 $update_id=str_replace("'","",$update_id);
				if($update_id){
					$rID1=execute_query("delete from wo_sales_target_mst where id =".$update_id."",0);
					$rID2=execute_query("delete from wo_sales_target_dtls where sales_target_mst_id =".$update_id."",0);
				}
				 
				
				
				if($db_type==0)
				{
					if($rID1==1 && $rID2==1){
						mysql_query("COMMIT");  
						echo "0**".$mst_id;
					}
					else{
						mysql_query("ROLLBACK"); 
						echo "10**".$mst_id;
					}
				}
				
				else if($db_type==2 || $db_type==1 )
				{
					if($rID1==1 && $rID2==1){
						oci_commit($con);  
						//echo "1**".$mst_id;
					}
					else{
						oci_rollback($con);
						//echo "10**".$mst_id;
					}
				}
				//Delete end----------------------------------------------
				
		*/

		disconnect($con);
		echo "2****" . $rID;
	}
}

if ($action == "select_month_from_variable") {
	$month = return_field_value("sales_year_started", "variable_order_tracking", "company_name='$data' and variable_list=12", "sales_year_started");
	$excut_source = return_field_value("excut_source", "variable_order_tracking", "company_name='$data' and variable_list=83", "excut_source");
	$style_from_library = return_field_value("style_from_library", "variable_order_tracking", "company_name='$data' and variable_list=83", "style_from_library");
	echo "document.getElementById('cbo_starting_month').value='" . $month . "';\n";
	echo "document.getElementById('cbo_excut_source').value='" . $excut_source . "';\n";
	echo "document.getElementById('cbo_is_popup_yes').value='" . $style_from_library . "';\n";
}
if ($action == "target_data") {
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	$caption_name = "";
	if ($type == 1) $caption_name = "Qty Target";
	else if ($type == 2) $caption_name = "Value Target";
	else if ($type == 3) $caption_name = "Target Mint";
?>
	<script>
		function fnc_close() {
			var save_string = '';
			var tot_defect_qnty = '';
			var defect_id_array = new Array();
			$("#tbl_list_search").find('tr').each(function() {
				var txtDefectId = $(this).find('input[name="txtDefectId[]"]').val();
				var txtDefectQnty = $(this).find('input[name="txtDefectQnty[]"]').val();
				var txtDefectUpdateId = $(this).find('input[name="txtDefectUpdateId[]"]').val();
				tot_defect_qnty = tot_defect_qnty * 1 + txtDefectQnty * 1;
				//				
				if (txtDefectQnty * 1 > 0) {
					if (save_string == "") {
						save_string = txtDefectUpdateId + "**" + txtDefectId + "**" + txtDefectQnty;
					} else {
						save_string += "," + txtDefectUpdateId + "**" + txtDefectId + "**" + txtDefectQnty;
					}

					if (jQuery.inArray(txtDefectId, defect_id_array) == -1) {
						defect_id_array.push(txtDefectId);
					}
				}
			});
			//alert (save_string);
			//var defect_type_id=
			$('#defect_type_id').val();
			$('#save_string').val(save_string);
			$('#tot_defectQnty').val(tot_defect_qnty);
			$('#all_defect_id').val(defect_id_array);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="defect_1" id="defect_1" autocomplete="off">
				<? //echo load_freeze_divs ("../../",$permission,1); 
				?>
				<fieldset style="width:350px;">
					<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="<? echo $save_data; ?>">
					<input type="hidden" name="tot_defectQnty" id="tot_defectQnty" class="text_boxes" value="<? echo $defect_qty; ?>">
					<input type="hidden" name="all_defect_id" id="all_defect_id" class="text_boxes" value="<? echo $all_defect_id; ?>">
					<input type="hidden" name="defect_type_id" id="defect_type_id" class="text_boxes" value="<? echo $type; ?>">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="340">
						<thead>
							<tr>
								<th colspan="3"><? echo $caption_name; ?></th>
							</tr>
							<tr>
								<th width="40">SL</th>
								<th width="150">Order Nature</th>
								<th><?= $caption_name ?></th>
							</tr>
						</thead>
					</table>
					<div style="width:340px; max-height:300px; overflow-y:scroll" id="list_container" align="left">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="320" id="tbl_list_search">
							<?
							if ($type == 1) {
								if ($save_string == "") //$save_data=$prevQnty;
									$explSaveData = explode(",", $save_data);

								$defect_dataArray = array();
								foreach ($explSaveData as $val) {
									$difectVal = explode("**", $val);
									$defect_dataArray[$difectVal[1]]['up_id'] = $difectVal[0];
									$defect_dataArray[$difectVal[1]]['defectid'] = $difectVal[1];
									$defect_dataArray[$difectVal[1]]['defectQnty'] = $difectVal[2];
								}
								$i = 1;
								foreach ($fbooking_order_nature as $id => $val) {
									if ($i % 2 == 0) $bgcolor = "#E9F3FF";
									else $bgcolor = "#FFFFFF";
							?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="150"><? echo $val; ?></td>
										<td align="center">
											<input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>">
											<input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
											<input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
										</td>
									</tr>
								<?
									$i++;
								}
							} else if ($type == 2) {
								if ($save_string == "") //$save_data=$prevQnty;
									$explSaveData = explode(",", $save_data);

								$defect_dataArray = array();
								foreach ($explSaveData as $val) {
									$difectVal = explode("**", $val);
									$defect_dataArray[$difectVal[1]]['up_id'] = $difectVal[0];
									$defect_dataArray[$difectVal[1]]['defectid'] = $difectVal[1];
									$defect_dataArray[$difectVal[1]]['defectQnty'] = $difectVal[2];
								}
								$i = 1;
								foreach ($fbooking_order_nature as $id => $val) {
									if ($i % 2 == 0) $bgcolor = "#E9F3FF";
									else $bgcolor = "#FFFFFF";
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="150"><? echo $val; ?></td>
										<td align="center">
											<input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>">
											<input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
											<input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
										</td>
									</tr>
								<?
									$i++;
								}
							} else if ($type == 3) {
								if ($save_string == "") //$save_data=$prevQnty;
									$explSaveData = explode(",", $save_data);

								$defect_dataArray = array();
								foreach ($explSaveData as $val) {
									$difectVal = explode("**", $val);
									$defect_dataArray[$difectVal[1]]['up_id'] = $difectVal[0];
									$defect_dataArray[$difectVal[1]]['defectid'] = $difectVal[1];
									$defect_dataArray[$difectVal[1]]['defectQnty'] = $difectVal[2];
								}
								$i = 1;
								foreach ($fbooking_order_nature as $id => $val) {
									if ($i % 2 == 0) $bgcolor = "#E9F3FF";
									else $bgcolor = "#FFFFFF";
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="150"><? echo $val; ?></td>
										<td align="center">
											<input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>">
											<input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
											<input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
										</td>
									</tr>
							<?
									$i++;
								}
							}
							?>
						</table>
					</div>
					<table width="320" id="table_id">
						<tr>
							<td align="center" colspan="3">
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
}


if ($action == "generate_list_view") {
	list($company_id, $buyer_id, $agent, $tem_leader, $t_month, $t_year, $brand_id) = explode("_", $data);
	if ($t_year != 0 and $t_month != 0) {
		$brand_cond_sales_forecast = '';
		if (!empty($brand_id)) $brand_cond_sales_forecast = " and mst.brand_id=$brand_id";
		$style_from_library = return_field_value("style_from_library", "variable_order_tracking", "company_name='$company_id' and variable_list=83", "style_from_library");

		//Previous Data History------------------------------------------------------------------
		$sql = "select dtls.year_month_name, dtls.sales_target_qty, dtls.sales_target_value, dtls.sales_target_mint,mst.id FROM wo_sales_target_mst mst, wo_sales_target_dtls dtls WHERE mst.id=dtls.sales_target_mst_id AND mst.company_id='$company_id' AND mst.buyer_id='$buyer_id' AND mst.agent='$agent' AND mst.team_leader='$tem_leader' AND mst.starting_year=($t_year-1) $brand_cond_sales_forecast";
		//echo $sql;
		$sql_result = sql_select($sql);

		$mst_id_arr = array();

		foreach ($sql_result as $row) {
			$mmyy = trim(str_replace(",", "", $row[csf("year_month_name")]));
			$prv_data_arr['qty' . $mmyy] = $row[csf("sales_target_qty")];
			$prv_data_arr['val' . $mmyy] = $row[csf("sales_target_value")];
			$prv_data_arr['mint' . $mmyy] = $row[csf("sales_target_mint")];
		}


		//Current Data History------------------------------------------------------------------
		$sql = "select mst.id, mst.total_target_mint, dtls.year_month_name, dtls.sales_target_qty, dtls.sales_target_value, dtls.sales_target_mint, dtls.cm, dtls.cm_val_per, dtls.rm_val_per, dtls.actual_margin_per FROM wo_sales_target_mst mst, wo_sales_target_dtls dtls WHERE mst.id=dtls.sales_target_mst_id AND mst.company_id='$company_id' AND mst.buyer_id='$buyer_id' AND mst.agent='$agent' AND mst.team_leader='$tem_leader' AND mst.starting_year=$t_year $brand_cond_sales_forecast";
		$sql_result = sql_select($sql);
		$mst_id = "";
		$target_mint = "";
		foreach ($sql_result as $row) {
			$mmyy = trim(str_replace(",", "", $row[csf("year_month_name")]));
			$cur_data_arr['qty' . $mmyy] = $row[csf("sales_target_qty")];
			$cur_data_arr['val' . $mmyy] = $row[csf("sales_target_value")];
			$cur_data_arr['mint' . $mmyy] = $row[csf("sales_target_mint")];

			$cur_data_arr['cm' . $mmyy] = $row[csf("cm")];
			$cur_data_arr['cmval' . $mmyy] = $row[csf("cm_val_per")];
			$cur_data_arr['rmval' . $mmyy] = $row[csf("rm_val_per")];
			$cur_data_arr['actval' . $mmyy] = $row[csf("actual_margin_per")];

			$mst_id = $row[csf("id")];
			$target_mint = $row[csf("total_target_mint")];
			$mst_id_arr[$row[csf("id")]] = $row[csf("id")];
		}
		$nature_cond = where_con_using_array($mst_id_arr, 0, "mst_id");
		$sql_nature = "SELECT MST_ID,ID,TARGET_TYPE,NATURE_ID,TARGET_MONTH,TARGET_YEAR,TARGET_QTY FROM WO_SALES_TARGET_NATURE_DTLS WHERE status_active=1 and is_deleted=0 $nature_cond";
		//echo $sql_nature;
		$res_nature = sql_select($sql_nature);
		$nature_data = array();
		foreach ($res_nature as $row) {
			$target_str = $row[csf('ID')] . "**" . $row[csf('NATURE_ID')] . "**" . $row[csf('TARGET_QTY')];
			$nature_data[$row[csf('MST_ID')]][$row[csf('TARGET_TYPE')]][$row[csf('TARGET_YEAR')]][$row[csf('TARGET_MONTH')]] .= $target_str . ",";
		}
		//echo $mst_id;
		//current data------------------------------------------------------------------------------
		$sql_data = "select a.company_id, a.location_id, a.year_id, a.month_id, b.buyer_id, b.allocation_percentage, c.comapny_id, c.location_id, c.year, d.month_id, d.capacity_min, e.capacity_month_min from lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b, lib_capacity_calc_mst c, lib_capacity_calc_dtls d, lib_capacity_year_dtls e where a.id=b.mst_id and c.id=d.mst_id and c.capacity_source=1 and a.company_id=c.comapny_id and a.location_id=c.location_id and a.year_id=c.year and a.month_id=d.month_id and a.company_id=$company_id and b.buyer_id=$buyer_id and c.id=e.mst_id and d.month_id=e.month_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.capacity_min is not null and b.allocation_percentage is not null  group by a.company_id, a.location_id, a.year_id, a.month_id, b.buyer_id, b.allocation_percentage, c.comapny_id, c.location_id, c.year, d.month_id, d.capacity_min, e.capacity_month_min";
		//$sql_data="select a.company_id,a.location_id,a.year_id,a.month_id,b.buyer_id,b.allocation_percentage,c.comapny_id,c.location_id,c.year,d.month_id,d.capacity_min,e.capacity_month_min from lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b,lib_capacity_calc_mst c, lib_capacity_calc_dtls d where a.id=b.mst_id and c.id=d.mst_id and c.capacity_source=1 and a.company_id=c.comapny_id and a.location_id=c.location_id and a.year_id=c.year and a.month_id=d.month_id and a.company_id= $company_id and b.buyer_id= $buyer_id and c.year= $t_year and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.capacity_min is not null and b.allocation_percentage is not null  group by a.company_id,a.location_id,a.year_id,a.month_id,b.buyer_id,b.allocation_percentage,c.comapny_id,c.location_id,c.year,d.month_id,d.capacity_min"; 

		$al_prcnt_arr = array();
		$capacity_min_arr = array();
		$month_id_arr = array();
		$sql_data = sql_select($sql_data);
		foreach ($sql_data as $row) {
			$m = $row[csf("month_id")];
			$y = $row[csf("year_id")];
			$mmyy = $m . ',' . $y;
			$mmyy = trim(str_replace(",", "", $mmyy));
			$cur_data_arr_1['mint' . $mmyy] = $row[csf("allocation_percentage")];
			$cur_data_arr_2['mint' . $mmyy] = $row[csf("capacity_month_min")];
			//print_r($cur_data_arr_1);
		}
		//previous data----------------------------------------------------------------------------
		$pre_sql_data = sql_select("select a.company_id, a.location_id, a.year_id, a.month_id, b.buyer_id, b.allocation_percentage, c.comapny_id, c.location_id, c.year, d.month_id, d.capacity_min, e.capacity_month_min from lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b, lib_capacity_calc_mst c, lib_capacity_calc_dtls d, lib_capacity_year_dtls e where a.id=b.mst_id and c.id=d.mst_id and c.capacity_source=1 and a.company_id=c.comapny_id and a.location_id=c.location_id and a.year_id=c.year and a.month_id=d.month_id and a.company_id= $company_id and b.buyer_id= $buyer_id and c.year= ($t_year-1) and c.id=e.mst_id and d.month_id=e.month_id and  a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.capacity_min is not null and b.allocation_percentage is not null  group by a.company_id, a.location_id, a.year_id, a.month_id, b.buyer_id, b.allocation_percentage, c.comapny_id, c.location_id, c.year, d.month_id, d.capacity_min, e.capacity_month_min");
		$pre_al_prcnt_arr = array();
		$pre_capacity_min_arr = array();
		$pre_month_id_arr = array();
		foreach ($pre_sql_data as $row) {
			//$mmyy=trim(str_replace(",","",$row[csf("year_month_name")]));
			$pre_al_prcnt_arr[] = $row[csf("allocation_percentage")];
			//$pre_capacity_min_arr[]=$row[csf("capacity_min")];
			$pre_capacity_min_arr[] = $row[csf("capacity_month_min")];
			$pre_month_id_arr[] = $row[csf("month_id")];
		}
		//$pre_get_target_min=$pre_al_prcnt*$pre_capacity_min/100;
		$pre_arr_al_prcnt_arr = array_combine($pre_month_id_arr, $pre_al_prcnt_arr);
		$pre_arr_capacity_arr = array_combine($pre_month_id_arr, $pre_capacity_min_arr);
	?>
		<fieldset>
			<div><!--main div start-->
				<div style="width:1170px;"><!--first div start-->
					<table width="100%" class="rpt_table" id="tbl_header" cellpadding="0" cellspacing="1" rules="all">
						<thead class="form_table_header">
							<tr>
								<th width="150" rowspan="2">Month</th>
								<th colspan="7">Current Year </th>
								<th colspan="3">
									<p>Previous Year's Target</p>
								</th>
							</tr>
							<tr>
								<th width="100">Qty Target </th>
								<th width="100">Value Target</th>
								<th width="100">Target Mint </th>

								<th width="100">CM</th>
								<th width="100">CM Value %</th>
								<th width="100">RM Value %</th>
								<th width="100">Margin Value %</th>

								<th width="100">Qty. Target </th>
								<th width="100">Value Target</th>
								<th>Target Mint </th>
							</tr>
						</thead>
					</table>
				</div>
				<div style="width:1170px;"><!--Second div start-->
					<input type="hidden" id="update_id" name="update_id" value="<? echo $mst_id; ?>" />
					<table width="100%" class="rpt_table" id="table_body" cellpadding="0" cellspacing="1" rules="all">
						<?
						$k = 1;
						$readonly = '';
						if ($style_from_library == 1) $readonly = "readonly";
						$tot_prv_qty = $tot_prv_val = 0;
						for ($i = 0; $i < 12; $i++) {
							if ($k < 13) {
								$month = $t_month + $i;
								$yy = $t_year;
								$save_1 = $save_2 = $save_3 = '';
								if ($mst_id == "") {
									$cur_trg_mint_1 = $cur_data_arr_1['mint' . $month . $yy];  //$tot_trg_mint_1+=$cur_trg_mint_1; 
									$cur_trg_mint_2 = $cur_data_arr_2['mint' . $month . $yy];  //$tot_trg_mint_2+=$cur_trg_mint_2; 
									$trg_mint = $cur_trg_mint_1 * $cur_trg_mint_2 / 100;
									$tot_trg_mint += $trg_mint;
								} else {
									$trg_mint = $cur_data_arr['mint' . $month . $yy];
									$tot_trg_mint += $trg_mint;
									$save_1 = chop($nature_data[$mst_id][1][$yy][$month], ",");
									$save_2 = chop($nature_data[$mst_id][2][$yy][$month], ",");
									$save_3 = chop($nature_data[$mst_id][3][$yy][$month], ",");
								}

								$cur_qty = $cur_data_arr['qty' . $month . $yy];
								$tot_cur_qty += $cur_qty;
								$cur_val = $cur_data_arr['val' . $month . $yy];
								$tot_cur_val += $cur_val;

								$cm = $cur_data_arr['cm' . $month . $yy];
								$cm_val_per = $cur_data_arr['cmval' . $month . $yy];
								$rm_val_per = $cur_data_arr['rmval' . $month . $yy];
								$actual_margin_per = $cur_data_arr['actval' . $month . $yy];



						?>
								<tbody>
									<tr>
										<td width="150">
											<input type="hidden" id="month_<? echo $month . $yy; ?>" name="month_<? echo $month . $yy; ?>" value="<? echo $month . ',' . $yy; ?>" />
											<input type="text" name="monthdisplay_<? echo $month . $yy; ?>" id="monthdisplay_<? echo $month . $yy; ?>" readonly value="<? echo $months[$month] . ',' . $yy; ?>" d class="text_boxes" style="width:140px;" />
										</td>
										<td width="100">
											<input type="text" name="qty_<? echo $month . $yy; ?>" id="qty_<? echo $month . $yy; ?>" onKeyUp="fn_calculate()" class="text_boxes_numeric" value="<? echo $cur_qty; ?>" <?php if ($style_from_library == 1) : ?> onDblClick="openTarget(1,'<?= $month ?>','<?= $yy ?>','qty_<?= $month . $yy ?>')" onFocus="openTarget(1,'<?= $month ?>','<?= $yy ?>','qty_<?= $month . $yy ?>')" <?= $readonly ?> <?php endif ?> style="width:85px;" />

											<?
											$save_id = 'savedata_1_' . $month . '_' . $yy;
											$nature_id = 'allnatureid_1_' . $month . '_' . $yy;
											?>
											<input type="hidden" id="<?= $save_id; ?>" value="<?= $save_1; ?>">
											<input type="hidden" id="<?= $nature_id; ?>">
										</td>
										<td width="100">
											<input type="text" name="val_<? echo $month . $yy; ?>" id="val_<? echo $month . $yy; ?>" onKeyUp="fn_calculate()" class="text_boxes_numeric" <?php if ($style_from_library == 1) : ?> onDblClick="openTarget(2,'<?= $month ?>','<?= $yy ?>','val_<?= $month . $yy ?>')" onFocus="openTarget(2,'<?= $month ?>','<?= $yy ?>','val_<?= $month . $yy ?>')" <?= $readonly ?> <?php endif ?> value="<? echo $cur_val; ?>" style="width:85px;" />
											<?
											$save_id2 = 'savedata_2_' . $month . '_' . $yy;
											$nature_id2 = 'allnatureid_2_' . $month . '_' . $yy;
											?>
											<input type="hidden" id="<?= $save_id2; ?>" value="<?= $save_2; ?>">
											<input type="hidden" id="<?= $nature_id2; ?>">
										</td>
										<td width="100">
											<input type="text" name="mint_<? echo $month . $yy; ?>" id="mint_<? echo $month . $yy; ?>" onKeyUp="fn_calculate()" class="text_boxes_numeric" <?php if ($style_from_library == 1) : ?> onDblClick="openTarget(3,'<?= $month ?>','<?= $yy ?>','mint_<?= $month . $yy ?>')" onFocus="openTarget(3,'<?= $month ?>','<?= $yy ?>','mint_<?= $month . $yy ?>')" <?= $readonly ?> <?php endif ?> value="<?= $trg_mint; ?>" style="width:85px;" />
											<?
											$save_id3 = 'savedata_3_' . $month . '_' . $yy;
											$nature_id3 = 'allnatureid_3_' . $month . '_' . $yy;
											?>
											<input type="hidden" id="<?= $save_id3; ?>" value="<?= $save_3; ?>">
											<input type="hidden" id="<?= $nature_id3; ?>">
										</td>

										<td width="100">
											<input type="text" name="cm_<?= $month . $yy; ?>" id="cm_<?= $month . $yy; ?>" class="text_boxes_numeric" value="<?= $cm; ?>" style="width:85px;" />
										</td>
										<td width="100">
											<input type="text" name="cmval_<?= $month . $yy; ?>" id="cmval_<?= $month . $yy; ?>" class="text_boxes_numeric" value="<?= $cm_val_per; ?>" style="width:85px;" />
										</td>
										<td width="100">
											<input type="text" name="rmval_<?= $month . $yy; ?>" id="rmval_<?= $month . $yy; ?>" class="text_boxes_numeric" value="<?= $rm_val_per; ?>" style="width:85px;" />
										</td>
										<td width="100">
											<input type="text" name="actualmargin_<?= $month . $yy; ?>" id="actualmargin_<?= $month . $yy; ?>" class="text_boxes_numeric" value="<?= $actual_margin_per; ?>" style="width:85px;" />
										</td>

										<td width="100" align="right"><?= $prv_data_arr['qty' . $month . ($yy - 1)];
																		$tot_prv_qty += $prv_data_arr['qty' . $month . ($yy - 1)]; ?></td>
										<td width="100" align="right"><?= $prv_data_arr['val' . $month . ($yy - 1)];
																		$tot_prv_val += $prv_data_arr['val' . $month . ($yy - 1)]; ?></td>
										<td align="right">
											<?
											echo $prv_data_arr['mint' . $month . ($yy - 1)];
											$pre_tot_alo_prcnt_val += $prv_data_arr['mint' . $month . ($yy - 1)];
											?>
										</td>
									</tr>
							<?
								if ($month == 12) {
									$t_month = 0;
									$i = 0;
									$t_year = $t_year + 1;
								}
							}
							$k++;
						}
							?>
							<tr>
								<td>Total</td>
								<td align="center"><input type="text" id="txt_total_qty" value="<? echo $tot_cur_qty; ?>" readonly class="text_boxes_numeric" style="width:85px;" /></td>
								<td align="center"><input type="text" id="txt_total_val" value="<? echo $tot_cur_val; ?>" readonly class="text_boxes_numeric" style="width:85px;" /> </td>
								<td align="center"><input type="text" id="txt_total_alo_prcnt" value="<? if ($target_mint > 0) {
																											echo $target_mint;
																										} else {
																											echo $tot_trg_mint;
																										} ?>" readonly class="text_boxes_numeric" style="width:85px;" /> </td>

								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>

								<td align="right"><? echo $tot_prv_qty; ?></td>
								<td align="right"><? echo $tot_prv_val; ?></td>
								<td align="right"><? echo $pre_tot_alo_prcnt_val; ?></td>
							</tr>
								</tbody>
					</table>
				</div>
			</div> <!--main div close-->
		</fieldset>

<?
	} //end if con
	echo "*_*" . $mst_id;
	exit();
}
?>