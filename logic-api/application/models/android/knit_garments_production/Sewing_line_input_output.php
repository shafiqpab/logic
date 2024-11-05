<?php
//include 'grade_class.php';
//include 'observation_class.php';
//include 'company_class.php';
//include 'source_class.php';
//include 'common_class.php';
//include 'defect_class.php';
//include 'inch_class.php';
//include 'qc_dtls_class.php';

class Sewing_line_input_output extends CI_Model {

	function __construct() {
		error_reporting(0);
		parent::__construct();
	}


     function writeFile($fileName,$txt){
		$file="note_url_script/objectData/".$fileName.".text";
		$current = file_get_contents($file);
		$current .= $txt."\n..........".date('d-m-Y h:i:s a',time()).".........\n\n";
		file_put_contents($file, $current);
	 }


	function get_max_value($tableName, $fieldName) {
		return $this->db->select_max($fieldName)->get($tableName)->row()->{$fieldName};
	}


	function insertData($post, $tableName) {
		$this->db->trans_start();
		$this->db->insert($tableName, $post);
		$this->db->trans_complete();
		if ($this->db->trans_status() == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


	function updateData($tableName, $data, $condition) {
		$this->db->trans_start();
		$this->db->update($tableName, $data, $condition);
		$this->db->trans_complete();
		if ($this->db->trans_status() == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	
	function deleteRowByAttribute($tableName, $attribute) {
		$this->db->trans_start();
		$this->db->delete($tableName, $attribute);
		$this->db->trans_complete();
		if ($this->db->trans_status() == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	
	
	public function sewing_input_output_data($company = 0, $barcode = "", $type) {
		$data_arr = array();

		$size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
		$color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
		$country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
		$garments_item = return_library_array("select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name", "id", "item_name");

		if ($this->db->dbdriver == 'mysqli') {
			$year_field = "YEAR(f.insert_date)";
		} else {
			$year_field = "to_char(f.insert_date,'YYYY')";
		}

		$barcode = trim($barcode);
		$barcode_no_arr = sql_select("SELECT  COLOR_TYPE_ID,BUNDLE_NO from PPL_CUT_LAY_BUNDLE  where barcode_no='" . trim($barcode) . "'");

		
		if ($type == 12) {

			$input_sql = "SELECT BARCODE_NO,BUNDLE_NO from PRO_GARMENTS_PRODUCTION_DTLS where status_active=1 and production_type=12 and barcode_no='" . trim($barcode) . "'";
			$input_exist_data = sql_select($input_sql);
			if (count($input_exist_data) > 0) {
				return array(
					'message_bng' => 'বান্ডিল নং: ' . $input_exist_data[0]->BUNDLE_NO . ' ইতিমধ্যে স্ক্যান হয়েছে, দয়া করে অন্য একটি চেষ্টা করুন।',
					'message_eng' => 'Bundle No: ' . $input_exist_data[0]->BUNDLE_NO . ' is already scanned, please try another one',
					'bundle_no' => '',
					'barcode_no' => 0,
					'year' => 0,
					'color_size_id' => 0,
					'order_id' => 0,
					'item_id' => 0,
					'country_id' => 0,
					'size_id' => 0,
					'color_id' => 0,
					'cut_no' => '',
					'job_no' => 0,
					'buyer' => '',
					'order_no' => '',
					'item' => '',
					'country' => '',
					'color' => '',
					'size' => '',
					'qty' => 0,
					'is_rescan' => 0,
					'color_type_id' => 0,

				);
			}

		} 
		else if ($type == 13) {
			
			 $output_sql_rescan = "SELECT barcode_no,sum(case when is_rescan=0 then (reject_qty+spot_qty+alter_qty)-replace_qty else 0 end )-sum(case when is_rescan > 0 then production_qnty else 0 end) as PRODUCTION_QNTY from PRO_GARMENTS_PRODUCTION_DTLS where status_active=1 and production_type=13 and barcode_no='$barcode' group by barcode_no";
			$output_rescan_data = sql_select($output_sql_rescan);
			$balance_qty=0;
			foreach($output_rescan_data as $rows){
				$balance_qty+=$rows->PRODUCTION_QNTY;
			}
			
			
			
			if($balance_qty<=0 && count($output_rescan_data) > 0){
				return array(
					'message_bng' => 'বান্ডিল নং: ' . $barcode_no_arr[0]->BUNDLE_NO . ' ইতিমধ্যে স্ক্যান হয়েছে, দয়া করে অন্য একটি চেষ্টা করুন।',
					'message_eng' => 'Bundle No: ' . $barcode_no_arr[0]->BUNDLE_NO . ' is already scanned, please try another one',
					'bundle_no' => '',
					'barcode_no' => 0,
					'year' => 0,
					'color_size_id' => 0,
					'order_id' => 0,
					'item_id' => 0,
					'country_id' => 0,
					'size_id' => 0,
					'color_id' => 0,
					'cut_no' => '',
					'job_no' => 0,
					'buyer' => '',
					'order_no' => '',
					'item' => '',
					'country' => '',
					'color' => '',
					'size' => '',
					'qty' => 0,
					'is_rescan' => 0,
					'color_type_id' => 0,
				);
				
			}
			//reject_qty,alter_qty,spot_qty,replace_qty
			
			
			if (count($output_rescan_data) > 0) {

			$sqls = "SELECT c.COLOR_TYPE_ID, max(c.id) as prdid, d.id as COLORSIZEID, e.id as PO_ID,f.company_name as LC_COMPANY, f.JOB_NO_PREFIX_NUM, MAX($year_field) as YEAR, f.BUYER_NAME, d.ITEM_NUMBER_ID, d.COUNTRY_ID, d.SIZE_NUMBER_ID, d.COLOR_NUMBER_ID, c.cut_no,c.BUNDLE_NO, sum(case when is_rescan=0 then (c.reject_qty+c.spot_qty+c.alter_qty)-c.replace_qty else 0 end )-sum(case when is_rescan >0 then production_qnty else 0 end) as PRODUCTION_QNTY, e.PO_NUMBER,c.BARCODE_NO,1 as IS_RESCAN from PRO_GARMENTS_PRODUCTION_MST a,PRO_GARMENTS_PRODUCTION_DTLS c, WO_PO_COLOR_SIZE_BREAKDOWN d, WO_PO_BREAK_DOWN e, WO_PO_DETAILS_MASTER f where a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and c.production_type =13 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and c.barcode_no='$barcode' group by c.COLOR_TYPE_ID,d.id, e.id,f.company_name, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
				//return $sqls;
				$result = sql_select($sqls);
				if(count($result) < 1){
					return array(
						'message_bng' => 'বান্ডিল নং: ' . $barcode_no_arr[0]->BUNDLE_NO . ' এখনও আউটপুটের জন্য প্রস্তুত নয়।',
						'message_eng' => 'Bundle No: ' . $barcode_no_arr[0]->BUNDLE_NO . ' is not yet ready for output.',
						'bundle_no' => '',
						'barcode_no' => 0,
						'year' => 0,
						'color_size_id' => 0,
						'order_id' => 0,
						'item_id' => 0,
						'country_id' => 0,
						'size_id' => 0,
						'color_id' => 0,
						'cut_no' => '',
						'job_no' => 0,
						'buyer' => '',
						'order_no' => '',
						'item' => '',
						'country' => '',
						'color' => '',
						'size' => '',
						'replace_qty' => 0,
						'qty' => 0,
						'is_rescan' => 1,
						'color_type_id' => 0,
					);
				}

				$data_arr = array();
				foreach ($result as $v) {
					$data_arr["message_bng"] = '';
					$data_arr["message_eng"] = '';

					$data_arr["bundle_no"] = $v->BUNDLE_NO;
					$data_arr["barcode_no"] = $v->BARCODE_NO;

					$data_arr["year"] = $v->YEAR;

					$data_arr["color_size_id"] = $v->COLORSIZEID;
					$data_arr["order_id"] = $v->PO_ID;
					$data_arr["item_id"] = $v->ITEM_NUMBER_ID;
					$data_arr["country_id"] = $v->COUNTRY_ID;
					$data_arr["size_id"] = $v->SIZE_NUMBER_ID;
					$data_arr["color_id"] = $v->COLOR_NUMBER_ID;
					$data_arr["cut_no"] = $v->CUT_NO;

					$data_arr["job_no"] = $v->JOB_NO_PREFIX_NUM;

					if (isset($buyer_arr[$v->BUYER_NAME])) {
						$data_arr["buyer"] = $buyer_arr[$v->BUYER_NAME];
					} else {
						$data_arr["buyer"] = "";
					}

					$data_arr["order_no"] = "$v->PO_NUMBER "; //need always string

					if (isset($garments_item[$v->ITEM_NUMBER_ID])) {
						$data_arr["item"] = $garments_item[$v->ITEM_NUMBER_ID];
					} else {
						$data_arr["item"] = "";
					}

					if (isset($country_arr[$v->COUNTRY_ID])) {
						$data_arr["country"] = $country_arr[$v->COUNTRY_ID];
					} else {
						$data_arr["country"] = "";
					}

					if (isset($color_arr[$v->COLOR_NUMBER_ID])) {
						$data_arr["color"] = $color_arr[$v->COLOR_NUMBER_ID];
					} else {
						$data_arr["color"] = "";
					}

					if (isset($size_arr[$v->SIZE_NUMBER_ID])) {
						$data_arr["size"] = $size_arr[$v->SIZE_NUMBER_ID];
					} else {
						$data_arr["size"] = "";
					}
					$data_arr["qty"] = $v->PRODUCTION_QNTY;
					$data_arr["is_rescan"] = $v->IS_RESCAN;
					$data_arr["color_type_id"] = $v->COLOR_TYPE_ID;
				}

				return $data_arr;

			}

		}
		
		
		$sqls = "SELECT  c.COLOR_TYPE_ID,  0 as IS_RESCAN,max(c.id) as prdid, d.id as COLORSIZEID, e.id as PO_ID,f.company_name as LC_COMPANY, f.JOB_NO_PREFIX_NUM, MAX($year_field) as YEAR, f.BUYER_NAME, d.ITEM_NUMBER_ID, d.COUNTRY_ID, d.SIZE_NUMBER_ID, d.COLOR_NUMBER_ID, c.cut_no,c.BUNDLE_NO, sum(c.production_qnty) as PRODUCTION_QNTY, e.PO_NUMBER,c.BARCODE_NO from PRO_GARMENTS_PRODUCTION_MST a,PRO_GARMENTS_PRODUCTION_DTLS c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and c.production_type = 4 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and c.barcode_no='$barcode' group by c.COLOR_TYPE_ID, d.id, e.id,f.company_name, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, e.po_number";
		 //return $sqls;
		//return $buyer_arr;

		$message_bng='';
		$message_eng='';
		if ($type == 5) {
			$message_bng = 'বান্ডিল নং: ' . $barcode_no_arr[0]->BUNDLE_NO . ' এখনও আউটপুটের জন্য প্রস্তুত নয়।';
			$message_eng = 'Bundle No: ' . $barcode_no_arr[0]->BUNDLE_NO . ' is not yet ready for output.';
		} else if ($type == 4) {
			$message_bng = 'বান্ডিল নং: ' . $barcode_no_arr[0]->BUNDLE_NO . ' এখনও ইনপুটের জন্য প্রস্তুত নয়।';
			$message_eng = 'Bundle No: ' . $barcode_no_arr[0]->BUNDLE_NO . ' is not yet ready for input.';
		}

		$result = sql_select($sqls);
		if (count($result) == 0) {
			return array(
				'message_bng' => $message_bng,
				'message_eng' => $message_eng,
				'bundle_no' => '',
				'barcode_no' => 0,
				'year' => 0,
				'color_size_id' => 0,
				'order_id' => 0,
				'item_id' => 0,
				'country_id' => 0,
				'size_id' => 0,
				'color_id' => 0,
				'cut_no' => '',
				'job_no' => 0,
				'buyer' => '',
				'order_no' => '',
				'item' => '',
				'country' => '',
				'color' => '',
				'size' => 0,
				'qty' => 0,
				'is_rescan' => 0,
				'color_type_id' => 0,
			);
		}

		foreach ($result as $v) {

			$data_arr["message_bng"] = '';
			$data_arr["message_eng"] = '';
			$data_arr["bundle_no"] = $v->BUNDLE_NO;
			$data_arr["barcode_no"] = $v->BARCODE_NO;
			$data_arr["year"] = $v->YEAR;
			$data_arr["color_size_id"] = $v->COLORSIZEID;
			$data_arr["order_id"] = $v->PO_ID;
			$data_arr["item_id"] = $v->ITEM_NUMBER_ID;
			$data_arr["country_id"] = $v->COUNTRY_ID;
			$data_arr["size_id"] = $v->SIZE_NUMBER_ID;
			$data_arr["color_id"] = $v->COLOR_NUMBER_ID;
			$data_arr["cut_no"] = $v->CUT_NO;
			$data_arr["job_no"] = $v->JOB_NO_PREFIX_NUM;

			if ($buyer_arr[$v->BUYER_NAME]) {
				$data_arr["buyer"] = $buyer_arr[$v->BUYER_NAME];
			} else {
				$data_arr["buyer"] = "";
			}

			$data_arr["order_no"] = "$v->PO_NUMBER "; //need always string

			if (isset($garments_item[$v->ITEM_NUMBER_ID])) {
				$data_arr["item"] = $garments_item[$v->ITEM_NUMBER_ID];
			} else {
				$data_arr["item"] = "";
			}

			if (isset($country_arr[$v->COUNTRY_ID])) {
				$data_arr["country"] = $country_arr[$v->COUNTRY_ID];
			} else {
				$data_arr["country"] = "";
			}

			if (isset($color_arr[$v->COLOR_NUMBER_ID])) {
				$data_arr["color"] = $color_arr[$v->COLOR_NUMBER_ID];
			} else {
				$data_arr["color"] = "";
			}

			if (isset($size_arr[$v->SIZE_NUMBER_ID])) {
				$data_arr["size"] = $size_arr[$v->SIZE_NUMBER_ID];
			} else {
				$data_arr["size"] = 0;
			}
			
			$data_arr["qty"] = $v->PRODUCTION_QNTY;
			$data_arr["is_rescan"] = $v->IS_RESCAN;

			if (isset($cut_lay_info[0]->COLOR_TYPE_ID)) {
				$data_arr["color_type_id"] = $cut_lay_info[0]->COLOR_TYPE_ID;
			} else {
				$data_arr["color_type_id"] = 0;
			}

		}
		return $data_arr;
	}
	
	public function save_update_sewing_line_input_output($save_obj) {
		$response_obj = json_decode($save_obj);
		$qc_mst_arr = array();
		$qc_dtls_arr = array();
		foreach ($response_obj->data->list_data as $val) {
			$barcodeNo = $val->barcode_no;
		}
		
		//lc company get using barcode......................................start;
		$lc_company_data = sql_select("SELECT COMPANY_ID  from PRO_GARMENTS_PRODUCTION_MST a,PRO_GARMENTS_PRODUCTION_DTLS b where a.id=b.mst_id and a.status_active=1  and b.status_active=1 and a.production_type=1 and b.production_type=1 and barcode_no ='$barcodeNo'");
		//lc company get using barcode......................................end;

		if ($response_obj->status == true) {
			$production_types = $response_obj->production_type;
			if ($production_types == 12) {//line input
				$entry_forms = 348;
			} 
			elseif($production_types == 13) {//line output
				$entry_forms = 349;
			}

			$mst_tbl_id = 0;
			$dtls_tbl_id = 0;
			$this->db->trans_start();
			$production_date = $response_obj->data->index->production_date;
			$remarks = $response_obj->data->index->remarks;
			$txt_reporting_hour = $response_obj->data->index->hour;
			$txt_reporting_hour=str_replace("-",":",$txt_reporting_hour); 
			
			if ($this->db->dbdriver == 'mysqli') {
				$year_cond = "YEAR(insert_date)";
				$pc_date_time = date("Y-m-d H:i:s", time());
				$production_date = date("Y-m-d", strtotime($production_date));
				$txt_reporting_hour = str_replace("'", "", $production_date) . " " . str_replace("'", "", $txt_reporting_hour);
				$txt_reporting_hour = date("Y-m-d H:i:s", strtotime($txt_reporting_hour));
			} else {
				$year_cond = "to_char(insert_date,'YYYY')";
				$pc_date_time = date("d-M-Y h:i:s A", time());
				$production_date = date("d-M-Y", strtotime($production_date));
				$txt_reporting_hour = str_replace("'", "", $production_date) . " " . str_replace("'", "", $txt_reporting_hour);
				$txt_reporting_hour = "to_date('" . $txt_reporting_hour . "','DD MONTH YYYY HH24:MI:SS')";

			}
			
			

			$cbo_company_name = $lc_company_data[0]->COMPANY_ID;
			$location_id = $response_obj->data->index->location_id;
			$production_source = $response_obj->data->index->production_source;
			$serving_company = $response_obj->data->index->serving_company;
			$floor_id = $response_obj->data->index->floor_id;
			$sewing_line = $response_obj->data->index->sewing_line;
			$organic = $response_obj->data->index->organic;
			$user_id = $response_obj->data->index->user_id;
			$txt_system_id = $response_obj->data->index->txt_system_id;


			$is_prod_reso_allo = return_field_value("auto_update", "variable_settings_production", "company_name=$cbo_company_name and  variable_list=23 and is_deleted=0 and status_active=1", "auto_update");

			if (str_replace("'", "", $txt_system_id) == "") {
				if ($production_types == 12) {
					$mrr_sty = "SWLI";
				} 
				else if($production_types == 13){
					$mrr_sty = "SWLO";
				}

				$new_sys_number = explode("*", return_next_id_by_sequence("", "PRO_GMTS_DELIVERY_MST", "", 1, $cbo_company_name, $mrr_sty, 0, date("Y", time()), 0, 0, $production_types, 0, 0));

				$mst_id = return_next_id_by_sequence("PRO_GMTS_DELIVERY_MST_SEQ", "PRO_GMTS_DELIVERY_MST_SEQ", "", "", 0, "", 0, 0, 0, 0, 0, 0, 0);
				$challan_no = (int) $new_sys_number[2];
				$txt_challan_no = $new_sys_number[0];

				$bundle_mst_arr = array(
					'ID' => $mst_id,
					'SYS_NUMBER_PREFIX' => $new_sys_number[1],
					'SYS_NUMBER_PREFIX_NUM' => (int) $new_sys_number[2],
					'SYS_NUMBER' => $new_sys_number[0],
					'DELIVERY_DATE' => $production_date,
					'COMPANY_ID' => $cbo_company_name,
					'PRODUCTION_TYPE' => $production_types,
					'LOCATION_ID' => $location_id,
					'DELIVERY_BASIS' => 3,
					'PRODUCTION_SOURCE' => $production_source,
					'SERVING_COMPANY' => $serving_company,
					'FLOOR_ID' => $floor_id,
					'SEWING_LINE' => $sewing_line,
					'ORGANIC' => $organic,
					'ENTRY_FORM' => $entry_forms,
					'INSERTED_BY' => $user_id,
					'INSERT_DATE' => $pc_date_time,
				);

				 //return $bundle_mst_arr;

				$mrr_tbl_id = $this->insertData($bundle_mst_arr, "PRO_GMTS_DELIVERY_MST");

			} else {

				$bundle_mst_arr_up = array(
					'DELIVERY_DATE' => $production_date,
					'COMPANY_ID' => $cbo_company_name,
					'LOCATION_ID' => $location_id,
					'PRODUCTION_SOURCE' => $production_source,
					'SERVING_COMPANY' => $serving_company,
					'FLOOR_ID' => $floor_id,
					'SEWING_LINE' => $sewing_line,
					'ORGANIC' => $organic,
					'UPDATED_BY' => $user_id,
					'UPDATED_BY' => $pc_date_time,
				);

				$mst_id = str_replace("'", "", $txt_system_id);
				$this->updateData('PRO_GMTS_DELIVERY_MST', $bundle_mst_arr_up, array('ID' => $mst_id));

			}

			$mstArr = array();
			$dtlsArr = array();
			$colorSizeArr = array();
			$mstIdArr = array();
			$colorSizeIdArr = array();

			$bundleCutArr = array();
			$color_type_arr = array();
			$is_rescan_arr = array();
			$cutArr = array();
			$dtlsArrColorSize = array();
			$bundleRescanArr = array();
			$bundleBarcodeArr = array();
			$duplicate_bundle = array();
			$bundleCheckArr = array();
			$all_cut_no_arr = array();
			$prev_prod_qty_arr = array();
			$dtls_data = $response_obj->data->list_data;

			foreach ($dtls_data as $v) {
				$bundleCheck = $v->bundle_no;
				$cutNo = $v->cut_no;
				$is_rescan = $v->is_rescan;
				if ($is_rescan != 1) {
					$bundleCheckArr[trim($bundleCheck)] = trim($bundleCheck);
				}
				$all_cut_no_arr[$cutNo] = $cutNo;
			}
			
			//return $all_cut_no_arr;
			
			
			$bundle = "'" . implode("','", $bundleCheckArr) . "'";
			$receive_sql = "SELECT c.barcode_no,c.BUNDLE_NO from PRO_GARMENTS_PRODUCTION_DTLS c where  c.bundle_no  in ($bundle)  and c.production_type='$production_types' and c.status_active=1 and c.is_deleted=0 and (c.is_rescan=0 or c.is_rescan is null)";
			$receive_result = sql_select($receive_sql);
			foreach ($receive_result as $row) {

				$duplicate_bundle[trim($row->BUNDLE_NO)] = trim($row->BUNDLE_NO);
			}

			// ========================== prev qty ========================
			$prev_production_types = ($production_types==5) ? 4 : 1;
			$prev_receive_sql = "SELECT c.PRODUCTION_QNTY,c.BUNDLE_NO from PRO_GARMENTS_PRODUCTION_DTLS c where  c.bundle_no  in ($bundle)  and c.production_type='$prev_production_types' and c.status_active=1 and c.is_deleted=0 and (c.is_rescan=0 or c.is_rescan is null)";
			$prev_receive_result = sql_select($prev_receive_sql);
			foreach ($prev_receive_result as $row) {

				$prev_prod_qty_arr[trim($row->BUNDLE_NO)] += $row->PRODUCTION_QNTY;
			}

			//return $receive_sql;
			foreach ($dtls_data as $val) {
				$cutNo = $val->cut_no;
				$color_type_id = $val->color_type_id;
				$bundleNo = $val->bundle_no;
				$barcodeNo = $val->barcode_no;
				$orderId = $val->order_id;
				$gmtsitemId = $val->item_id;
				$countryId = $val->country_id;
				$colorId = $val->color_id;
				$sizeId = $val->size_id;
				$colorSizeId = $val->color_size_id;
				$qty = $val->qnty;
				$checkRescan = $val->is_rescan;

				if ($prev_prod_qty_arr[trim($bundleNo)]>=$qty) 
				{
					if (!isset($duplicate_bundle[trim($bundleNo)])) 
					{
						$bundleCutArr[$bundleNo] = $cutNo;
						$color_type_arr[$bundleNo] = $color_type_id;
						$is_rescan_arr[$bundleNo] = $checkRescan;
						$cutArr[$orderId][$gmtsitemId][$countryId] = $cutNo;
						if (isset($mstArr[$orderId][$gmtsitemId][$countryId])) {
							$mstArr[$orderId][$gmtsitemId][$countryId] += $qty;
						} else {
							$mstArr[$orderId][$gmtsitemId][$countryId] = $qty;
						}

						$colorSizeArr[$bundleNo] = $orderId . "**" . $gmtsitemId . "**" . $countryId;
						if (isset($dtlsArr[$bundleNo])) {
							$dtlsArr[$bundleNo] += $qty;
						} else {
							$dtlsArr[$bundleNo] = $qty;
						}


						$dtlsRejQtyArr[$bundleNo] += $val->reject;
						$dtlsAltQtyArr[$bundleNo] += $val->alter;
						$dtlsSpoQtyArr[$bundleNo] += $val->spot;
						$dtlsRepQtyArr[$bundleNo] += $val->replace;
						$dtlsQcQtyArr[$bundleNo]  += $val->qc_qnty;

						$dtlsArrColorSize[$bundleNo] = $colorSizeId;
						$bundleRescanArr[$bundleNo] = $checkRescan;
						$bundleBarcodeArr[$bundleNo] = $barcodeNo;
					}
				}
				else
				{
					return $resultset["status"] = "Failed";
				}

			}

		//return $mstArr;

			if ($response_obj->mode == "save") {

				foreach ($mstArr as $orderId => $orderData) {
					foreach ($orderData as $gmtsItemId => $gmtsItemIdData) {
						foreach ($gmtsItemIdData as $countryId => $qty) {
							$id = return_next_id_by_sequence("PRO_GAR_PRODUCTION_MST_SEQ", "PRO_GARMENTS_PRODUCTION_MST", "", "", 0, "", 0, 0, 0, 0, 0, 0, 0);

							$mst_part_data = array(
								'ID' => $id,
								'DELIVERY_MST_ID' => $mst_id,
								'CUT_NO' => $cutArr[$orderId][$gmtsItemId][$countryId],
								'COMPANY_ID' => $cbo_company_name,
								'GARMENTS_NATURE' => 2,
								'CHALLAN_NO' => $challan_no,
								'PO_BREAK_DOWN_ID' => $orderId,
								'ITEM_NUMBER_ID' => $gmtsItemId,
								'COUNTRY_ID' => $countryId,
								'PRODUCTION_SOURCE' => $production_source,
								'SERVING_COMPANY' => $serving_company,
								'LOCATION' => $location_id,
								'PRODUCTION_DATE' => $production_date,
								'PRODUCTION_QUANTITY' => $qty,
								'PRODUCTION_TYPE' => $production_types,
								'ENTRY_BREAK_DOWN_TYPE' => 3,
								'REMARKS' => $remarks,
								'FLOOR_ID' => $floor_id,
								'SEWING_LINE' => $sewing_line,
								'PROD_RESO_ALLO' => $is_prod_reso_allo,
								'ENTRY_FORM' => $entry_forms,
								'IS_TAB' => 1,
								'INSERTED_BY' => $user_id,
								'INSERT_DATE' => $pc_date_time,
							);

							
							$mst_tbl_id = $this->insertData($mst_part_data, "PRO_GARMENTS_PRODUCTION_MST");
							$mstIdArr[$orderId][$gmtsItemId][$countryId] = $id;
							if ($mst_tbl_id && $production_types == 13) {
								$this->db->query("update PRO_GARMENTS_PRODUCTION_MST set production_hour=$txt_reporting_hour where id ='$id'");
							}

						}
					}
				}

				foreach ($dtlsArr as $bundle_no => $qty) {

					$colorSizedData = explode("**", $colorSizeArr[$bundle_no]);
					$gmtsMstId = $mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
					$cut_no = $bundleCutArr[$bundle_no];
					$color_type_ids = $color_type_arr[$bundle_no];
					$is_rescan_id = $is_rescan_arr[$bundle_no];
					$dtls_id = return_next_id_by_sequence("PRO_GAR_PRODUCTION_DTLS_SEQ", "PRO_GARMENTS_PRODUCTION_DTLS", "", "", 0, "", 0, 0, 0, 0, 0, 0, 0);

					$dtls_part_data = array(
						'ID' => $dtls_id,
						'DELIVERY_MST_ID' => $mst_id,
						'MST_ID' => $gmtsMstId,
						'PRODUCTION_TYPE' => $production_types,
						'COLOR_SIZE_BREAK_DOWN_ID' => $dtlsArrColorSize[$bundle_no],
						'CUT_NO' => $cut_no,
						'BUNDLE_NO' => $bundle_no,
						'ENTRY_FORM' => $entry_forms,
						'BARCODE_NO' => $bundleBarcodeArr[$bundle_no],
						'IS_RESCAN' => $is_rescan_id,
						'COLOR_TYPE_ID' => $color_type_ids,
					);

					if ($production_types == 12) {
						$dtls_part_data['PRODUCTION_QNTY']=$qty;
					}
					else if ($production_types == 13) {
						$dtls_part_data['PRODUCTION_QNTY']=$dtlsQcQtyArr[$bundle_no];
						$dtls_part_data['REJECT_QTY']=$dtlsRejQtyArr[$bundle_no];
						$dtls_part_data['ALTER_QTY']=$dtlsAltQtyArr[$bundle_no];
						$dtls_part_data['REPLACE_QTY']=$dtlsRepQtyArr[$bundle_no];
						$dtls_part_data['SPOT_QTY']=$dtlsSpoQtyArr[$bundle_no];
					}




					$dtls_tbl_id = $this->insertData($dtls_part_data, "PRO_GARMENTS_PRODUCTION_DTLS");
				}

			}
			if ($response_obj->mode == "update") {
				$this->db->query("delete from PRO_GARMENTS_PRODUCTION_DTLS where mst_id ='$update_id'");
			}

			if ($this->db->trans_status() == TRUE) {
				if ($mst_tbl_id && $dtls_tbl_id) {
					$this->db->trans_commit();
					$this->db->trans_complete();
					return $resultset["status"] = "Successful";
				} else {
					$this->db->trans_rollback();
					$this->db->trans_complete();
					return $resultset["status"] = "Failed";
				}

			} else {
				$resultset["status"] = "Failed";
				$this->db->trans_complete();
			}
		} else {
			return $resultset["status"] = "Failed";
		}
	}


}
