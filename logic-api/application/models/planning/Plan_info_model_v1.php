<?php
class Plan_info_model extends CI_Model
{ //v1

	function __construct()
	{
		parent::__construct();
		error_reporting(0);
		ini_set('precision', 8);
	}
	/**
	 * [get_max_value description]
	 * @param  [string] $tableName [defining name of the table]
	 * @param  [string] $fieldName [defining name of the table column]
	 * @return [integer]            [return max value of the table column]
	 */
	function get_max_value($tableName, $fieldName)
	{
		return $this->db->select_max($fieldName)->get($tableName)->row()->{$fieldName};
	}

	/**
	 * [insertDataWithReturn description]
	 * @param  [string] $tableName [defining name of the table]
	 * @param  [array] $post [defining data to be inserted]
	 * @return [boolean]            [TRUE/FALSE]
	 */
	function insertData($post, $tableName)
	{
		$this->db->trans_start();
		$this->db->insert($tableName, $post);
		$this->db->trans_complete();
		if ($this->db->trans_status() == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * [updateData description]
	 * @param  [string] $tableName [defining name of the table]
	 * @param  [array] $data [defining data to be updated]
	 * @param  [type] $condition [defining the condition for update]
	 * @return [boolean]            [TRUE/FALSE]
	 */
	function updateData($tableName, $data, $condition)
	{
		$this->db->trans_start();
		$this->db->update($tableName, $data, $condition);
		$this->db->trans_complete();
		if ($this->db->trans_status() == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * [deleteRowByAttribute description]
	 * @param  [string] $tableName [defining name of the table]
	 * @param  [array] $data [value by which row will be deleted]
	 * @return [boolean]            [TRUE/FALSE]
	 */
	function deleteRowByAttribute($tableName, $attribute)
	{
		$this->db->trans_start();
		$this->db->delete($tableName, $attribute);
		$this->db->trans_complete();
		if ($this->db->trans_status() == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	/**
	 * [get_field_value_by_attribute description]
	 * @param  [type] $tableName [description]
	 * @param  [type] $fieldName [description]
	 * @param  [type] $attribute [description]
	 * @return [type]            [description]
	 */
	function get_field_value_by_attribute($tableName, $fieldName, $attribute)
	{
		$attribute = str_replace("'", "", $attribute);
		if (($attribute * 1) > 0) {
			$query = $this->db->query('select ' . $tableName . '.' . $fieldName . ' from ' . $tableName . ' where id in(' . $attribute . ')');
			$result = $query->row();
			if (!empty($result)) :
				return $result->{$fieldName};
			else :
				return false;
			endif;
		}
	}

    function plan_info($company_id, $location_id, $floor_id, $txt_date_from, $user_id)
	{
		//$starttime = microtime(true);  //return true;
		
		if ($location_id > 0) {
			$locCon = " and LOCATION_NAME in(0,$location_id)";
		}

		if ($floor_id > 0) {
			$florCon = " and FLOOR_NAME in(0,$floor_id)";
		}

		$table_PLANNING_BOARD_STATUS = $this->db->query("SELECT ID,USER_ID,LOCATION_NAME,FLOOR_NAME from PLANNING_BOARD_STATUS where BOARD_STATUS=1 $locCon $florCon ")->result();
		foreach ($table_PLANNING_BOARD_STATUS as $rows) {
			$board_location_arr[$rows->LOCATION_NAME] = $rows->LOCATION_NAME;
			$board_floor_arr[$rows->FLOOR_NAME] = $rows->FLOOR_NAME;
		}
		
		// $table_PPL_SEWING_PLAN_BOARD_POWISE = $this->db->query("SELECT PO_BREAK_DOWN_ID,PLAN_ID from PPL_SEWING_PLAN_BOARD_POWISE ")->result();
		// foreach ($table_PPL_SEWING_PLAN_BOARD_POWISE as $row) {
		// 	$pobreakdown_id[$row->PLAN_ID] = $row->PO_BREAK_DOWN_ID;
		// }
		// print_r($pobreakdown_id); die;
		$locations = '';
		$floors = '';
		if (count($board_location_arr) > 0 && $location_id > 0) {
			$locations = ',' . implode(',', $board_location_arr);
		}
		if (count($board_floor_arr) > 0 && $floor_id > 0) {
			$floors = ',' . implode(',', $board_floor_arr);
		}

		//$floor_cond_res = '';
		$floor_cond_line = '';
		$floor_cond_line_sts = '';
		if ($floor_id > 0) {
			//$floor_cond_res = " and floor_id='$floor_id' ";
			$floor_cond_line = " and floor_name='$floor_id' ";
			$floor_cond_line_sts = " and floor_name in ($floor_id $floors) ";
		}

		$user_arr = array();
		$user_arr_type = array();
		$table_USER_PASSWD = $this->db->query("SELECT ID,USER_NAME,IS_PLANNER FROM USER_PASSWD ")->result();
		
		foreach ($table_USER_PASSWD as $value) {
			$user_arr[$value->ID] = $value->USER_NAME;
			$user_arr_type[$value->ID] = $value->IS_PLANNER;
		}

		$user_arr[0] = '';
		$table_locked = '';
		$need_to_update = 0;
		$need_to_insert = 1;
		$max_id = 0;

		$ppl_sewing_plan_board_dtls_data = array();

		$loc_cond = "";
		$loc_cond_lock = "";
		if ($location_id > 0) {
			$loc_cond_lock = " and location_name in($location_id $locations)";
			$loc_cond .= " and location_name='$location_id' ";
		}

		if ($company_id > 0) {
			$comCon3 = " and company_name=$company_id";
		}

		$user_wise_floor_data_arr = array();
		$line_names_ids = array();
		$query_LIB_SEWING_LINE = "SELECT ID,USER_IDS,LOCATION_NAME,FLOOR_NAME,LINE_NAME from LIB_SEWING_LINE where STATUS_ACTIVE=1 and IS_DELETED=0 $comCon3 $loc_cond $floor_cond_line order by sewing_line_serial";
		
		//echo $sql_line;die;
		$table_LIB_SEWING_LINE = $this->db->query($query_LIB_SEWING_LINE)->result();
		foreach ($table_LIB_SEWING_LINE as $ids => $vals) {
			foreach (explode(',', $vals->USER_IDS) as $uid) {
				$user_wise_floor_data_arr[$uid][$vals->FLOOR_NAME] = $vals->FLOOR_NAME;
			}
			$line_names_ids[$vals->ID] = $vals->ID;
		}

		$null_arr = array();
		if (count($line_names_ids) < 1) {
			return $null_arr;
		}

		if (count($user_wise_floor_data_arr[$user_id])) {
			$floor_cond_line_sts = " and floor_name in (" . implode(',', $user_wise_floor_data_arr[$user_id]) . ") ";
		}

		if ($user_arr_type[$user_id] == 1) {

			if ($company_id > 0) {
				$comCon = " and a.company_name=$company_id";
			}

			$sql_line = "SELECT a.BOARD_STATUS,a.USER_ID ,b.IS_PLANNER from PLANNING_BOARD_STATUS a,USER_PASSWD b  where b.id=a.user_id and b.IS_PLANNER=1 $comCon $loc_cond_lock $floor_cond_line_sts order by a.BOARD_STATUS asc";
			
			//echo $sql_line;die;
			$new_line_resource = $this->db->query($sql_line)->result();

			foreach ($new_line_resource as $ids => $vals) {
				if ($vals->USER_ID != $user_id) {
					if ($vals->BOARD_STATUS == 1) {
						$table_locked = $user_arr[$vals->USER_ID];
					}
				} else {
					if ($vals->BOARD_STATUS != 1) {
						$need_to_update = 1;
					} else {
						$need_to_insert = 0;
					}
				}
			}



			if ($table_locked == '') // need to lock board for this user
			{
				if ($need_to_update == 0 && $need_to_insert == 1) // New Insert
				{
					foreach ($user_wise_floor_data_arr[$user_id] as $fid) {
						$max_id = $this->get_max_value("PLANNING_BOARD_STATUS", "ID") + 1;
						$ppl_sewing_plan_board_dtls_data = array(
							'ID' => $max_id,
							'COMPANY_NAME' => $company_id,
							'LOCATION_NAME' => $location_id,
							'FLOOR_NAME' => $fid,
							'USER_ID' => $user_id,
							'BOARD_STATUS' => 1,
						);
						$this->insertData($ppl_sewing_plan_board_dtls_data, "PLANNING_BOARD_STATUS");
					}
				} else {
					if ($company_id > 0) {
						$comCon2 = " and company_name=$company_id";
					}
					$this->db->query("UPDATE planning_board_status set board_status=1 where user_id=$user_id $comCon2 $loc_cond  $floor_cond_line");
				}
			}
		} else {
			$table_locked = 'VISITOR';
		}

		//---------------ws_gsd
		
		$is_integrated = return_field_value("WORK_STUDY_INTEGRATED", "variable_settings_production", "company_name='$company_id' and variable_list=9", "WORK_STUDY_INTEGRATED");

		if ($is_integrated == 1) {
			$machineSql = "SELECT B.GSD_MST_ID,sum(case when b.resource_gsd  in(40,41,43,44,48,55,68,69,70) then b.layout_mp  end) as TOT_HELPER,sum(case when b.resource_gsd  not in(40,41,43,44,48,68,69,53,54,55,56,70) then b.layout_mp  end) as TOT_OP_MEC from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b  where a.id=b.mst_id  and a.balancing_page=1 and a.is_deleted=0  and b.is_deleted=0  and a.status_active = 1  and b.status_active = 1 group by b.gsd_mst_id"; // and a.gsd_mst_id=367
			$machine_result = $this->db->query($machineSql)->result();
			$machine_data_arr = array();
			foreach ($machine_result as $row) {
				$machine_data_arr['HELPER'][$row->GSD_MST_ID] = $row->TOT_HELPER;
				$machine_data_arr['MACHINE'][$row->GSD_MST_ID] = $row->TOT_OP_MEC;
				$machine_data_arr['OPERATOR'][$row->GSD_MST_ID] = $row->TOT_OP_MEC;
			}

			$gsdSql = "SELECT A.ID, C.JOB_NO, B.EFFICIENCY, B.TARGET,b.ALLOCATED_MP from ppl_gsd_entry_mst a,  ppl_balancing_mst_entry b,  wo_po_details_mas_set_details c where a.id = b.gsd_mst_id and c.quot_id = a.id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and  b.balancing_page=2 order by a.id desc"; //and a.id = 367
			$gsd_result = $this->db->query($gsdSql)->result();
			$gsd_data_arr = array();
			foreach ($gsd_result as $row) {
				$gsd_data_arr['EFFICIENCY'][$row->JOB_NO] = $row->EFFICIENCY;
				$gsd_data_arr['TARGET'][$row->JOB_NO] = $row->TARGET;
				$gsd_data_arr['HELPER'][$row->JOB_NO] = $machine_data_arr['HELPER'][$row->ID];
				$gsd_data_arr['MACHINE'][$row->JOB_NO] = $machine_data_arr['MACHINE'][$row->ID];
				$gsd_data_arr['OPERATOR'][$row->JOB_NO] = $machine_data_arr['OPERATOR'][$row->ID];
				//$gsd_data_arr['ALLOCATED_MP'][$row->JOB_NO]=$row->ALLOCATED_MP;
			}
		}
		
		$today = date("Y-m-d");
		$from_date = date("Y-m-d", strtotime($txt_date_from));
		$tmp_from_date = $from_date;
		$days_forward = 365; //change by regan
		//$days_backward = 30;


		//$from_date = add_date($from_date, $days_backward, 0);

		$to_date = add_date($tmp_from_date, $days_forward, 1);

		if ($company_id > 0) {
			$comCon4 = " and  a.company_id=$company_id";
		}

		
		$plan_sql = "SELECT a.MERGED_PLAN_ID,a.set_dtls_id,a.color_size_id,a.PO_BREAK_DOWN_ID as multi_po,a.learing_iterator, a.half, a.id,a.LINE_ID,a.PLAN_ID,a.start_date,a.start_hour,a.end_date,a.end_hour,a.duration,a.plan_qnty,a.comp_level,a.first_day_output, a.next_first_day_output, a.next_increment, a.next_terget, a.increment_qty,a.terget,a.day_wise_plan,a.company_id,a.location_id,a.off_day_plan,a.order_complexity,a.ship_date, a.extra_param,a.plan_level,a.first_day_capacity,a.last_day_capacity,a.seq_no,a.po_company_id,1 as use_learning_curve,a.current_production_date,a.production_percent, a.top_border_color,a.bottom_border_color,a.left_color,a.right_color,1 as job_no,a.LOCATION_X,a.LOCATION_Y,a.SMV,a.MERGE_COMMENTS,a.MERGE_TYPE,a.INSERT_DATE  ,a.NOTES,a.CLOSING_STATUS,a.CLOSED_BY,a.CLOSING_DATE,a.CLOSING_NOTE,a.RE_OPEN_DATE,a.RE_OPENED_BY,a.RE_OPEN_NOTE,a.ALLOCATED_MP,a.BYPASS_MP,a.remaining_work_hour,a.AUTO_TARGET,a.ITEM_NAME,a.STYLE_REF_NO,a.COLOR_NUMBER,a.COLOR_NUMBER_H,a.PO_NUMBER,a.COLOR_NUMBER_ID_H,a.JOB_NO,a.ITEM_NUMBER_ID,a.PO_QUANTITY_H,a.PO_BREAK_DOWN_ID_H,a.PO_NUMBER_H,a.COLOR_NUMBER_ID,a.ITEM_NUMBER_ID_H,a.POWISE_QUANTITY_H,a.PO_INITIAL_QTY_H,a.ORDER_TYPE,a.PHD from ppl_sewing_plan_board a where a.CLOSING_STATUS<>1  " . where_con_using_array($line_names_ids, 0, 'a.line_id') . "  $comCon4  and (a.start_date between to_date('" . $from_date . "','yyyy-mm-dd')  and to_date('" . $to_date . "','yyyy-mm-dd')   or a.end_date between to_date('" . $from_date . "','yyyy-mm-dd')  and to_date('" . $to_date . "','yyyy-mm-dd')  or ( a.start_date < to_date('" . $from_date . "','yyyy-mm-dd')  and a.end_date> to_date('" . $to_date . "','yyyy-mm-dd')))  and a.status_active=1 and a.MERGE_TYPE is null   group by a.MERGED_PLAN_ID,a.PO_BREAK_DOWN_ID,a.half,a.id,a.line_id,a.plan_id,a.start_date,a.start_hour,a.end_date,a.end_hour,a.duration, a.plan_qnty,a.comp_level,a.first_day_output,  a.next_first_day_output, a.LEARING_ITERATOR, a.next_increment, a.next_terget,a.increment_qty,a.terget,a.day_wise_plan,a.company_id,a.location_id,a.item_number_id, a.off_day_plan,a.order_complexity,a.ship_date, extra_param,a.plan_level,a.first_day_capacity,a.last_day_capacity,a.seq_no,a.po_company_id,a.use_learning_curve,a.current_production_date,a.production_percent, a.top_border_color,a.bottom_border_color,a.left_color,a.right_color , a.MERGE_COMMENTS,a.MERGE_TYPE,a.INSERT_DATE ,a.NOTES,a.CLOSING_STATUS,a.CLOSED_BY,a.CLOSING_DATE,a.CLOSING_NOTE,a.RE_OPEN_DATE,a.RE_OPENED_BY,a.RE_OPEN_NOTE,a.set_dtls_id,a.color_size_id,a.ALLOCATED_MP,a.BYPASS_MP,a.remaining_work_hour,a.AUTO_TARGET,a.LOCATION_X,a.LOCATION_Y,a.SMV,a.ITEM_NAME,a.STYLE_REF_NO,a.COLOR_NUMBER_H,a.PO_NUMBER,a.COLOR_NUMBER,a.COLOR_NUMBER_ID_H,a.JOB_NO,a.ITEM_NUMBER_ID,a.PO_QUANTITY_H,a.PO_BREAK_DOWN_ID_H,a.PO_NUMBER_H,a.COLOR_NUMBER_ID,a.ITEM_NUMBER_ID_H,a.POWISE_QUANTITY_H,a.PO_INITIAL_QTY_H,a.ORDER_TYPE,a.PHD ORDER BY a.ID desc ";
		// and a.plan_id=2073
		//echo $plan_sql; die;

		$plan_data = $this->db->query($plan_sql)->result();
		
		$allPlanIdArr = array();
		$line_id_arr = array();
		$insert_plan_temp_data = array();
	
		//$max_tmp_id = $this->get_max_value("GBL_TEMP_ENGINE", "ID") + 1;
		foreach ($plan_data as $rows) {
			$allPlanIdArr[$rows->PLAN_ID] = $rows->PLAN_ID;
			
			$line_id_arr[] = $rows->LINE_ID * 1;

			// $insert_plan_temp_data[$rows->PLAN_ID] = array(
			// 	'ID' => $max_tmp_id,
			// 	'USER_ID' => $user_id,
			// 	'REF_FROM' => 57,
			// 	'ENTRY_FORM' => 5000,
			// 	'REF_VAL' => $rows->PLAN_ID
			// );
			// $max_tmp_id++;
			
		}






		//First delete  by 'ENTRY_FORM' => 5001 & self user data....................then insert ;
		if (count($allPlanIdArr)) {
			$empty_arr = array();
			$this->deleteRowByAttribute('GBL_TEMP_ENGINE', array('ENTRY_FORM' => 5000, "USER_ID" => $user_id));
			$this->deleteRowByAttribute('GBL_TEMP_ENGINE', array('ENTRY_FORM' => 5001, "USER_ID" => $user_id));
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 5000, 53, $allPlanIdArr, $empty_arr);
			//$this->db->insert_batch("GBL_TEMP_ENGINE", $insert_plan_temp_data);
		}
		
		$sql_plan_dtls = "SELECT b.PLAN_ID,b.PO_BREAK_DOWN_ID,b.ITEM_NUMBER_ID,b.SIZE_NUMBER_ID,b.COLOR_NUMBER_ID,b.COUNTRY_ID from ppl_sewing_plan_board_powise b, gbl_temp_engine gbl where b.plan_id=gbl.ref_val and gbl.entry_form=5000 and gbl.REF_FROM=53 and gbl.user_id=$user_id ";
		$plan_dtls_data = $this->db->query($sql_plan_dtls)->result();
		//echo $allPlanIdArr;die;
		//print_r($allPlanIdArr);die;
		
		$com_res = $this->db->query("SELECT ID,COMPANY_NAME from lib_company where status_active =1 and is_deleted=0 and CORE_BUSINESS=1  order by company_name")->result();
		foreach ($com_res as $value) {
			$comp[$value->ID] = $value->COMPANY_NAME;
		}

		$location_res = $this->db->query("SELECT ID,LOCATION_NAME from lib_location  where status_active =1 and is_deleted=0  order by location_name")->result();
		foreach ($location_res as $value) {
			$location_arr[$value->ID] = $value->LOCATION_NAME;
		}

		$garment_res = $this->db->query("SELECT ID,ITEM_NAME from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name")->result();
		foreach ($garment_res as $value) {
			$garments_item[$value->ID] = $value->ITEM_NAME;
		}

		$buyer_res = $this->db->query("SELECT ID,BUYER_NAME from  lib_buyer where status_active=1 and is_deleted=0 order by BUYER_NAME")->result();
		foreach ($buyer_res as $value) {
			$buyer_arr[$value->ID] = $value->BUYER_NAME;
		}

		$resource_res = $this->db->query("SELECT ID,LINE_NUMBER from  PROD_RESOURCE_MST ")->result();
		foreach ($resource_res as $value) {
			$resource_arr[$value->ID] = $value->LINE_NUMBER;
		}

		
		
		// test start

		$query_powise = "SELECT a.PO_BREAK_DOWN_ID,a.PUB_SHIPMENT_DATE FROM PPL_SEWING_PLAN_BOARD_POWISE a,gbl_temp_engine gbl WHERE a.plan_id=gbl.ref_val and gbl.entry_form=5000 and gbl.REF_FROM=53 and gbl.user_id=$user_id";
		$table_powise = $this->db->query($query_powise)->result();
		$po_arr = Array();
		$pub_ship_date_arr_by_po = array();
		foreach($table_powise as $row){
			$po_arr[$row->PO_BREAK_DOWN_ID]=$row->PO_BREAK_DOWN_ID;
			$pub_ship_date_arr_by_po[$row->PO_BREAK_DOWN_ID] = $row->PUB_SHIPMENT_DATE;
		}
		//print_r($query_powise);die;
		$query_booking = "SELECT b.PO_BREAK_DOWN_ID, a.ITEM_CATEGORY, b.GMTS_COLOR_ID, b.FIN_FAB_QNTY AS QUANTITY, a.FABRIC_SOURCE, b.COLOR_SIZE_TABLE_ID, b.FABRIC_COLOR_ID, a.DELIVERY_DATE, b.COLOR_TYPE,b.WO_QNTY,b.TRIM_GROUP FROM WO_BOOKING_MST a, WO_BOOKING_DTLS b WHERE a.ID = b.BOOKING_MST_ID AND a.COMPANY_ID = $company_id AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1" . where_con_using_array($po_arr, 0, 'b.PO_BREAK_DOWN_ID')  ; 
		//print_r($query_booking);die;
		$table_booking = $this->db->query($query_booking)->result();
		$lib_booking_quantity = array();
		$lib_booking_delivery_date = array();
		foreach ($table_booking as $row) {
			if ($row->ITEM_CATEGORY == 2) {
				$lib_booking_quantity[$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->GMTS_COLOR_ID] += $row->QUANTITY;
			} elseif ($row->ITEM_CATEGORY == 4) {
				$lib_booking_quantity[$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->TRIM_GROUP] += $row->WO_QNTY;
			}


			if ($lib_booking_delivery_date[$row->PO_BREAK_DOWN_ID] == null || strtotime($lib_booking_delivery_date[$row->PO_BREAK_DOWN_ID]) > strtotime($row->DELIVERY_DATE)) {
				$lib_booking_delivery_date[$row->PO_BREAK_DOWN_ID] = $row->DELIVERY_DATE;
			}
		}
		
		//print_r($lib_booking_delivery_date);die;
		// $query_production_quantity = "SELECT d.PO_BREAKDOWN_ID,c.ITEM_CATEGORY,a.COLOR_ID,a.RECEIVE_QNTY,c.RECEIVE_BASIS,c.TRANSACTION_TYPE FROM PRO_FINISH_FABRIC_RCV_DTLS a, PRO_BATCH_CREATE_MST b, INV_TRANSACTION c,ORDER_WISE_PRO_DETAILS d,PRODUCT_DETAILS_MASTER e WHERE a.BATCH_ID = b.ID AND a.TRANS_ID = c.ID AND d.TRANS_ID = c.ID AND e.ID = d.PROD_ID AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND c.IS_DELETED = 0 AND c.STATUS_ACTIVE = 1 AND d.IS_DELETED = 0 AND d.STATUS_ACTIVE = 1 AND e.IS_DELETED = 0 AND e.STATUS_ACTIVE = 1". where_con_using_array($po_arr, 0, 'd.PO_BREAKDOWN_ID');
		$query_production_quantity = "SELECT d.PO_BREAKDOWN_ID,c.ITEM_CATEGORY,a.COLOR_ID,a.RECEIVE_QNTY,c.RECEIVE_BASIS,c.TRANSACTION_TYPE FROM PRO_FINISH_FABRIC_RCV_DTLS a, PRO_BATCH_CREATE_MST b, INV_TRANSACTION c,ORDER_WISE_PRO_DETAILS d,PRODUCT_DETAILS_MASTER e, gbl_temp_engine gbl WHERE a.BATCH_ID = b.ID AND a.TRANS_ID = c.ID AND d.TRANS_ID = c.ID AND e.ID = d.PROD_ID AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND c.IS_DELETED = 0 AND c.STATUS_ACTIVE = 1 AND d.IS_DELETED = 0 AND d.STATUS_ACTIVE = 1 AND e.IS_DELETED = 0 AND e.STATUS_ACTIVE = 1 and d.PO_BREAKDOWN_ID = gbl.ref_val and gbl.REF_FROM=53 and gbl.entry_form=5000 and gbl.user_id=$user_id";
		$table_production_quantity = $this->db->query($query_production_quantity)->result();
		$lib_production_quantity = array();
		foreach ($table_production_quantity as $row) {
			$lib_production_quantity[$row->PO_BREAKDOWN_ID][$row->ITEM_CATEGORY][$row->COLOR_ID] += $row->RECEIVE_QNTY;
		}
		
		//print_r($query_production_quantity);die;
		
		$query_trims_entry_dtls = "SELECT c.PO_BREAKDOWN_ID, b.ITEM_CATEGORY, a.ITEM_GROUP_ID, a.RECEIVE_QNTY FROM inv_trims_entry_dtls a, INV_TRANSACTION b, ORDER_WISE_PRO_DETAILS c, gbl_temp_engine gbl WHERE a.TRANS_ID = b.ID AND b.ID = c.TRANS_ID AND b.ITEM_CATEGORY = 4 AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND c.IS_DELETED = 0 AND c.STATUS_ACTIVE = 1 and b.COMPANY_ID = $company_id and c.PO_BREAKDOWN_ID = gbl.ref_val and gbl.REF_FROM=53 and gbl.entry_form=5000 and gbl.user_id=$user_id";
		$table_trims_entry_dtls = $this->db->query($query_trims_entry_dtls)->result();
		$lib_trims_entry_dtls = array();
		foreach ($table_trims_entry_dtls as $row) {
			$lib_trims_entry_dtls[$row->PO_BREAKDOWN_ID][$row->ITEM_CATEGORY][$row->ITEM_GROUP_ID] += $row->RECEIVE_QNTY;
		}

		
		
		
		//print_r($query_trims_entry_dtls);die;
		$inv_status = array();
		foreach ($table_booking as $row) {
			//Fabric inventory
			if ($row->ITEM_CATEGORY == 2) {
				//print_r($lib_booking_quantity);die;
				if ($lib_production_quantity[$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->GMTS_COLOR_ID] > 0) {
					if ($lib_booking_quantity[$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->GMTS_COLOR_ID] > $lib_production_quantity[$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->GMTS_COLOR_ID]) {
						if ($inv_status[$row->PO_BREAK_DOWN_ID] == null || $inv_status[$row->PO_BREAK_DOWN_ID] == 0) {
							$inv_status[$row->PO_BREAK_DOWN_ID] = 1;
							// if($row->PO_BREAK_DOWN_ID == 79954){
							// 	echo $row->PO_BREAK_DOWN_ID.'**'.$row->ITEM_CATEGORY.'**'.$row->GMTS_COLOR_ID;die;
							// }

						}
					} elseif ($lib_booking_quantity[$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->GMTS_COLOR_ID] <= $lib_production_quantity[$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->GMTS_COLOR_ID]) {
						if ($inv_status[$row->PO_BREAK_DOWN_ID] == null || $inv_status[$row->PO_BREAK_DOWN_ID] != 0) {
							$inv_status[$row->PO_BREAK_DOWN_ID] = 2;
						}
					}
				} else {
					if ($inv_status[$row->PO_BREAK_DOWN_ID] == null) {
						$inv_status[$row->PO_BREAK_DOWN_ID] = 0;
					}
				}
			} elseif ($row->ITEM_CATEGORY == 4) {
				//print_r($lib_booking_quantity);die;
				if ($lib_trims_entry_dtls[$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->TRIM_GROUP] > 0) {

					if ($lib_booking_quantity[$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->TRIM_GROUP] > $lib_trims_entry_dtls[$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->TRIM_GROUP]) {
						if ($inv_status[$row->PO_BREAK_DOWN_ID] == null || $inv_status[$row->PO_BREAK_DOWN_ID] == 0) {
							$inv_status[$row->PO_BREAK_DOWN_ID] = 1;
						}
					} elseif ($lib_booking_quantity[$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->TRIM_GROUP] <= $lib_trims_entry_dtls[$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->TRIM_GROUP]) {
						if ($inv_status[$row->PO_BREAK_DOWN_ID] == null || $inv_status[$row->PO_BREAK_DOWN_ID] != 0) {
							$inv_status[$row->PO_BREAK_DOWN_ID] = 2;
						}
					}
				} else {
					if ($inv_status[$row->PO_BREAK_DOWN_ID] == null) {
						$inv_status[$row->PO_BREAK_DOWN_ID] = 0;
					}
				}
			}
		}
		//print_r($lib_production_quantity);die;

		$first_booking_delivery_date = array();
		$inv_status_by_plan = array();
		$npos = array();
		$po_arr_by_plan = array();
		$insert_plan_temp_data = array();
		$po_by_plan_id = array();
		//$max_tmp_id = $this->get_max_value("GBL_TEMP_ENGINE", "ID") + 1;
		
		foreach ($plan_dtls_data as $rows) {


			if ($inv_status[$rows->PO_BREAK_DOWN_ID] == 2) {
				if ($inv_status_by_plan[$rows->PLAN_ID] == null || $inv_status_by_plan[$rows->PLAN_ID] != 0 || $inv_status_by_plan[$rows->PLAN_ID] != 1) {
					$inv_status_by_plan[$rows->PLAN_ID] = 2;
				}
			} elseif ($inv_status[$rows->PO_BREAK_DOWN_ID] == 1) {
				if ($inv_status_by_plan[$rows->PLAN_ID] == null || $inv_status_by_plan[$rows->PLAN_ID] != 0) {
					$inv_status_by_plan[$rows->PLAN_ID] = 1;
				}
			} elseif ($inv_status[$rows->PO_BREAK_DOWN_ID] == 0) {
				$inv_status_by_plan[$rows->PLAN_ID] = 0;
			}

			if ($first_booking_delivery_date[$rows->PLAN_ID] == Null || strtotime($first_booking_delivery_date[$rows->PLAN_ID]) > strtotime($lib_booking_delivery_date[$rows->PO_BREAK_DOWN_ID])) {
				$first_booking_delivery_date[$rows->PLAN_ID] = $lib_booking_delivery_date[$rows->PO_BREAK_DOWN_ID];
			}

			//$po_arr_by_plan[$rows->PLAN_ID] = $rows->PO_BREAK_DOWN_ID;
			//print_r($inv_status);die;
			//test end

			$po_arr_by_plan[$rows->PLAN_ID][] = $rows->PO_BREAK_DOWN_ID;
			$item_arr_by_plan[$rows->PLAN_ID][$rows->ITEM_NUMBER_ID] = $rows->ITEM_NUMBER_ID;
			$size_arr_by_plan[$rows->PLAN_ID][] = $rows->SIZE_NUMBER_ID;

			if ($rows->COLOR_NUMBER_ID) {
				$color_arr_by_plan[$rows->PLAN_ID][] = $rows->COLOR_NUMBER_ID;
				$color_id[$rows->COLOR_NUMBER_ID] = $rows->COLOR_NUMBER_ID;
			}

			$country_arr_by_plan[$rows->PLAN_ID][] = $rows->COUNTRY_ID;
			$size_id_m[$rows->SIZE_NUMBER_ID] = $rows->SIZE_NUMBER_ID;

			if ($rows->PO_BREAK_DOWN_ID > 0) {
				$npos[$rows->PO_BREAK_DOWN_ID] = $rows->PO_BREAK_DOWN_ID;
				//$is_plan = 1;
			}


			//$tmp_seq_id = $this->db->query("SELECT PRC_TEMPENGINE_ID_SEQ.nextval VALUE FROM DUAL") ->result_array();
			// $insert_plan_temp_data[$rows->PO_BREAK_DOWN_ID] = array(
			// 	'ID' => $max_tmp_id,
			// 	'USER_ID' => $user_id,
			// 	'REF_FROM' => 56,
			// 	'ENTRY_FORM' => 5001,
			// 	'REF_VAL' => $rows->PO_BREAK_DOWN_ID
			// );
			// $max_tmp_id++;
			$insert_plan_temp_data[$rows->PO_BREAK_DOWN_ID]=$rows->PO_BREAK_DOWN_ID;
		}
		//print_r($plan_dtls_data);die;
		//print_r($inv_status_by_plan);die;

		
		


		
		

		$images_sql = "SELECT a.ID,b.IMAGE_LOCATION from WO_PO_BREAK_DOWN a, COMMON_PHOTO_LIBRARY b, gbl_temp_engine gbl where a.job_no_mst=b.MASTER_TBLE_ID  and a.is_deleted=0 and a.id= gbl.ref_val and gbl.REF_FROM=53 and gbl.entry_form=5000 and gbl.user_id=$user_id and b.file_type=1  order by b.id asc  ";
		$images_sql_result = $this->db->query($images_sql)->result();
		foreach ($images_sql_result as $img_rows) {
			$po_wise_img_location_arr[$img_rows->ID] = $img_rows->IMAGE_LOCATION;
		}
		
		//print_r($po_wise_img_location_arr);die;

		$color_ids = ($color_id) ? implode(',', $color_id) : 0;
		$size_ids = ($size_id_m[0]) ? implode(',', $size_id_m) : 0;

		//print_r($color_ids);die;
		

		$table_color =  $this->db->query("SELECT ID,COLOR_NAME from  LIB_COLOR where ID in ($color_ids)")->result();
		$lib_color_arr = array();
		foreach ($table_color as $row) {
			$lib_color_arr[$row->ID] = $row->COLOR_NAME;
		}

		$table_size =  $this->db->query("SELECT ID,SIZE_NAME from  LIB_SIZE where ID in ($size_ids)")->result();
		$lib_size_arr = array();
		foreach ($table_size as $row) {
			$lib_size_arr[$row->ID] = $row->SIZE_NAME;
		}
		//print_r($table_color);die;
		// end test
		// INSERT PLAN_ID INTO GBL_TEMP_ENGINE TABLE
		if (count($insert_plan_temp_data)) {
			$empty_arr = array();
			//$this->db->insert_batch("GBL_TEMP_ENGINE", $insert_plan_temp_data);
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 5001, 56, $insert_plan_temp_data, $empty_arr);
			//print_r($insert_plan_temp_data);die;
		}
		
		$marge_plan_sql = "SELECT a.MERGED_PLAN_ID,a.SET_DTLS_ID,a.COLOR_SIZE_ID,a.PO_BREAK_DOWN_ID as multi_po,a.learing_iterator, a.half, a.id,a.line_id,a.plan_id,a.start_date,a.start_hour,a.end_date,a.end_hour,a.duration,a.plan_qnty,a.comp_level,a.first_day_output, a.next_first_day_output,
		a.next_increment, a.next_terget, a.increment_qty,a.terget,a.day_wise_plan,a.company_id,a.location_id,a.off_day_plan,a.order_complexity,a.ship_date, a.extra_param,a.plan_level,a.first_day_capacity,a.last_day_capacity,a.seq_no,a.po_company_id,1 as use_learning_curve,a.current_production_date,a.production_percent, a.top_border_color,a.bottom_border_color,a.left_color,a.right_color,1 as job_no,a.LOCATION_X,a.LOCATION_Y,a.SMV, 0 as PO_BREAK_DOWN_ID,  0 as ITEM_NUMBER_ID,  0 as SIZE_NUMBER_ID, 0 as COLOR_NUMBER_ID, 0 as COUNTRY_ID, a.MERGE_COMMENTS,a.MERGE_TYPE,a.INSERT_DATE  ,a.NOTES,a.CLOSING_STATUS,a.CLOSED_BY,a.CLOSING_DATE,a.CLOSING_NOTE,a.RE_OPEN_DATE,a.RE_OPENED_BY,a.RE_OPEN_NOTE,a.ALLOCATED_MP,a.BYPASS_MP,a.remaining_work_hour,a.AUTO_TARGET,a.ITEM_NAME,a.STYLE_REF_NO,a.PO_NUMBER,a.COLOR_NUMBER_ID,a.JOB_NO,a.ITEM_NUMBER_ID_H,a.PO_QUANTITY_H,a.POWISE_QUANTITY_H,a.PO_INITIAL_QTY_H,a.ORDER_TYPE FROM ppl_sewing_plan_board a, gbl_temp_engine gbl where a.MERGED_PLAN_ID=gbl.ref_val and gbl.entry_form=5000 and gbl.REF_FROM=53 and gbl.user_id=$user_id and a.MERGE_TYPE is not null and a.status_active=1 ";


		//echo $marge_plan_sql;die;
		$marge_plan_sql_res = $this->db->query($marge_plan_sql)->result();
		foreach ($marge_plan_sql_res as $rows) {
			//Marge data assing here................
			$plan_data[] = $rows;
		}
		
		//print_r($plan_data); die;

		$company_id = ($company_id) ? $company_id : 1;
		$prod_reso_allo = get_resource_allocation_variable($company_id);
		
		$production_arr = array();
		$temp_production_arr = array();
		if (!empty($line_id_arr)) {
			// $production_sql = "SELECT a.PRODUCTION_DATE,a.PO_BREAK_DOWN_ID, a.SEWING_LINE,sum(b.PRODUCTION_QNTY) PRODUCTION_QNTY, a.ITEM_NUMBER_ID,c.COLOR_NUMBER_ID FROM pro_garments_production_mst a,pro_garments_production_dtls b,WO_PO_COLOR_SIZE_BREAKDOWN c, gbl_temp_engine gbl where a.id=b.mst_id and b.COLOR_SIZE_BREAK_DOWN_ID=c.id and a.PO_BREAK_DOWN_ID=gbl.ref_val and gbl.entry_form=5001 and gbl.REF_FROM=56 and gbl.user_id=$user_id
			// and a.production_type=5  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.PRODUCTION_DATE,a.po_break_down_id, a.SEWING_LINE, a.ITEM_NUMBER_ID,c.COLOR_NUMBER_ID"; // $sql3
			$production_sql = "SELECT a.PRODUCTION_DATE, a.PO_BREAK_DOWN_ID, a.SEWING_LINE, a.ITEM_NUMBER_ID, c.COLOR_NUMBER_ID, b.PRODUCTION_QNTY as PRODUCTION_QNTY, 1 as ORDER_TYPE FROM GBL_TEMP_ENGINE gbl, WO_PO_COLOR_SIZE_BREAKDOWN c , PRO_GARMENTS_PRODUCTION_DTLS b, PRO_GARMENTS_PRODUCTION_MST a WHERE gbl.entry_form = 5001 and gbl.ref_from = 56 and gbl.user_id = $user_id  and gbl.ref_val=c.po_break_down_id and c.id=b.color_size_break_down_id and b.mst_id=a.id and c.status_active = 1 and c.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and b.production_type = 5 and a.status_active = 1 and a.production_type = 5 and a.is_deleted = 0 
			UNION ALL 
			SELECT a.PRODUCTION_DATE,c.ID as PO_BREAK_DOWN_ID ,a.LINE_ID as SEWING_LINE,a.GMTS_ITEM_ID as ITEM_NUMBER_ID,b.COLOR_ID as COLOR_NUMBER_ID,d.PROD_QNTY as PRODUCTION_QNTY , 2 as ORDER_TYPE
			FROM GBL_TEMP_ENGINE gbl, SUBCON_ORD_DTLS c, subcon_gmts_prod_dtls a, subcon_ord_breakdown b,SUBCON_GMTS_PROD_COL_SZ d
			WHERE gbl.entry_form = 5001 AND gbl.REF_FROM = 56 AND gbl.user_id = $user_id 
			AND gbl.ref_val=c.ID 
			AND c.ID = b.ORDER_ID
			AND a.ORDER_ID = c.ID
			and d.ORD_COLOR_SIZE_ID = b.ID
			AND d.DTLS_ID = a.id
			AND a.status_active = 1 AND a.is_deleted = 0 and a.PRODUCTION_TYPE=2 AND d.PRODUCTION_TYPE=2";
			//echo $production_sql;die;
			
			
			$production_result = $this->db->query($production_sql)->result();
			
			
			if (!empty($production_result)) {
				$po_wise_productionArr = array();
				$item_wise_productionArr = array();
				$item_po_color_wise_productionArr = array();
				$po_line_production_arr = array();
				$date_production_arr = array();
				$po_line_color_production_arr = array();
				$po_line_color_date_production_arr = array();
				$po_wise_colour_production_arr = array();
				
				foreach ($production_result as $production_row) {
					$po_wise_productionArr[$production_row->ORDER_TYPE][$production_row->PO_BREAK_DOWN_ID] += $production_row->PRODUCTION_QNTY;
					$item_wise_productionArr[$production_row->ORDER_TYPE][$production_row->ITEM_NUMBER_ID][$production_row->PO_BREAK_DOWN_ID] += $production_row->PRODUCTION_QNTY;
					$item_po_color_wise_productionArr[$production_row->ORDER_TYPE][$production_row->ITEM_NUMBER_ID][$production_row->PO_BREAK_DOWN_ID][$production_row->COLOR_NUMBER_ID] += $production_row->PRODUCTION_QNTY;





					//print_r($production_row->PO_BREAK_DOWN_ID);die;
					//if (isset($resource_arr[$production_row->SEWING_LINE])) {
					if ($prod_reso_allo == 1) {


						$lines = $resource_arr[$production_row->SEWING_LINE];
						$line_arr = explode(",", $lines);
						//print_r($production_row->SEWING_LINE);die;


						//print_r($line_arr);die;
						foreach ($line_arr as $linId) {
							$production_arr[$production_row->PO_BREAK_DOWN_ID][$linId]["SEWING_LINE"] = $linId;
							$production_arr[$production_row->PO_BREAK_DOWN_ID][$linId]["PRODUCTION_QNTY"] += $production_row->PRODUCTION_QNTY;
							$date_production_arr[$production_row->PO_BREAK_DOWN_ID][$linId][$production_row->PRODUCTION_DATE] += $production_row->PRODUCTION_QNTY;
							$po_line_production_arr[$production_row->ORDER_TYPE][$production_row->PO_BREAK_DOWN_ID][$linId] += $production_row->PRODUCTION_QNTY;
							//color lavel data.......................
							$po_line_color_production_arr[$production_row->ORDER_TYPE][$production_row->PO_BREAK_DOWN_ID][$linId][$production_row->COLOR_NUMBER_ID] += $production_row->PRODUCTION_QNTY;
							$po_line_color_date_production_arr[$production_row->PO_BREAK_DOWN_ID][$linId][$production_row->COLOR_NUMBER_ID][$production_row->PRODUCTION_DATE] += $production_row->PRODUCTION_QNTY;
							//...................................color lavel data end;

							//start without plan production qty distibute on plan line ...............
							$temp_production_arr[$production_row->PO_BREAK_DOWN_ID][$production_row->PRODUCTION_DATE][$linId] += $production_row->PRODUCTION_QNTY;
							//.................end;


						}
					} else {

						$production_arr[$production_row->PO_BREAK_DOWN_ID][$production_row->SEWING_LINE]["SEWING_LINE"] = $production_row->SEWING_LINE;
						$production_arr[$production_row->PO_BREAK_DOWN_ID][$production_row->SEWING_LINE]["PRODUCTION_QNTY"] += $production_row->PRODUCTION_QNTY;
						$date_production_arr[$production_row->PO_BREAK_DOWN_ID][$production_row->SEWING_LINE][$production_row->PRODUCTION_DATE] += $production_row->PRODUCTION_QNTY;

						$po_line_production_arr[$production_row->ORDER_TYPE][$production_row->PO_BREAK_DOWN_ID][$production_row->SEWING_LINE] += $production_row->PRODUCTION_QNTY;

						//color lavel data.......................
						$po_line_color_production_arr[$production_row->ORDER_TYPE][$production_row->PO_BREAK_DOWN_ID][$production_row->SEWING_LINE][$production_row->COLOR_NUMBER_ID] += $production_row->PRODUCTION_QNTY;
						$po_line_color_date_production_arr[$production_row->PO_BREAK_DOWN_ID][$production_row->SEWING_LINE][$production_row->COLOR_NUMBER_ID][$production_row->PRODUCTION_DATE] += $production_row->PRODUCTION_QNTY;
						//...................................color lavel data end;

						//start without plan production qty distibute on plan line ...............
						$temp_production_arr[$production_row->PO_BREAK_DOWN_ID][$production_row->PRODUCTION_DATE][$production_row->SEWING_LINE] += $production_row->PRODUCTION_QNTY;
						//.................................end;
					}
				}
			}
		}
		
		//print_r($po_wise_productionArr);die;
		

		$daywise_sql = "SELECT a.SHIP_DATE,b.IS_OFFDAY, b.WORKING_HOUR, c.PO_BREAK_DOWN_ID,a.LINE_ID,a.START_DATE,a.END_DATE, a.BALANCE_QTY,a.FIRST_DAY_OUTPUT,a.INCREMENT_QTY,a.TERGET ,a.COMPANY_ID, a.LOCATION_ID,c.ITEM_NUMBER_ID ,a.OFF_DAY_PLAN,a.ORDER_COMPLEXITY,b.PLAN_DATE,b.PLAN_QNTY,b.PLAN_ID ,C.JOB_NO,c.COLOR_NUMBER_ID,c.PLAN_QNTY as PLAN_COLOR_QTY,a.ORDER_TYPE from ppl_sewing_plan_board a,ppl_sewing_plan_board_dtls b,PPL_SEWING_PLAN_BOARD_POWISE c, gbl_temp_engine gbl  where a.plan_id=c.plan_id and  b.plan_id=c.plan_id and a.plan_id=b.plan_id  and a.plan_id=gbl.ref_val and gbl.entry_form=5000 and gbl.REF_FROM=53 and gbl.user_id=$user_id and a.is_deleted=0 and a.status_active=1  ORDER BY a.plan_id desc";
		//echo $daywise_sql;die;
		$daywise_plan = $this->db->query($daywise_sql)->result();
		
		$plan_job_arr = array('0');
		foreach ($daywise_plan as $rows) {
			$plan_job_arr[$rows->JOB_NO] = $rows->JOB_NO;
			$planboard_po_shipdate[$rows->PO_BREAK_DOWN_ID][$rows->PLAN_ID] = $rows->SHIP_DATE;
		}

		//test
		foreach ($daywise_plan as $row) {
			$pobreakdown_id[$row->PLAN_ID] = $row->PO_BREAK_DOWN_ID;
		}
		
		//print_r($pobreakdown_id); die;
		//test
		//print_r($planboard_po_shipdate); die;
		$plan_smv_query = "SELECT JOB_NO, GMTS_ITEM_ID, SMV_PCS FROM wo_po_details_mas_set_details where 1=1 " . where_con_using_array($plan_job_arr, 1, 'JOB_NO') . " ";
		//echo $plan_smv_query; die;
		$job_smv_sql = sql_select($plan_smv_query);
		$job_smv_arr = array();
		foreach ($job_smv_sql as $v) {
			$job_smv_arr[$v->JOB_NO][$v->GMTS_ITEM_ID] = $v->SMV_PCS;
		}

		$plan_po_color_qty_arr = array();
		$plan_po_line_arr = array();
		$plan_production_qnty = array();
		//$plan_production_qnty_subcon = array();
		$blance_pro_qty_qnty = array();
		//$po_production_qnty = array();
		$plan_wise_balance_qty_arr = array();
		foreach ($daywise_plan as $rows) {
			//$production_line_qnty = 0;
			if ($date_production_arr[$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID][$rows->PLAN_DATE]) { //print_r(5);die;
				$plan_po_line_arr[$rows->ORDER_TYPE][$rows->PLAN_ID][$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID] = $rows->LINE_ID;
				$plan_po_line_color_arr[$rows->PLAN_ID][$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID][$rows->COLOR_NUMBER_ID] = $rows->COLOR_NUMBER_ID;
				$plan_po_line_color_date_arr[$rows->PLAN_ID][$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID][$rows->COLOR_NUMBER_ID][$rows->PLAN_DATE] = $rows->PLAN_DATE;
			}


			$date_plan_qty_total_arr[$rows->PLAN_DATE][$rows->PLAN_ID] = $rows->PLAN_QNTY;
			$plan_wise_balance_qty_arr[$rows->PLAN_ID] = $rows->BALANCE_QTY;
			$date_plan_smv_min_arr[$rows->PLAN_DATE][$rows->PLAN_ID] = ($rows->PLAN_QNTY * $job_smv_arr[$rows->JOB_NO][$rows->ITEM_NUMBER_ID]);

			//start without plan production qty distibute on plan line ...............
			unset($temp_production_arr[$rows->PO_BREAK_DOWN_ID][$rows->PLAN_DATE][$rows->LINE_ID]);
			$po_line_wise_plan_blance[$rows->PO_BREAK_DOWN_ID][$rows->PLAN_DATE][$rows->LINE_ID] = ($rows->PLAN_QNTY - $date_production_arr[$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID][$rows->PLAN_DATE]);
			//......................................................................end

			$plan_po_color_qty_arr[$rows->ORDER_TYPE. '*' .$rows->PLAN_ID . '*' . $rows->PO_BREAK_DOWN_ID . '*' . $rows->COLOR_NUMBER_ID] = $rows->PLAN_COLOR_QTY;
		}

		$changed_data_arr = $this->get_changed_po_info($plan_po_color_qty_arr);

		//print_r($plan_po_line_arr); die;
		$plan_lavel_data_arr = $this->db->query("SELECT BULLETIN_TYPE  from variable_settings_production where variable_list=12 and IS_DELETED=0 and STATUS_ACTIVE=1")->result();

		

		//blance data.................................

		$blance_pro_res_arr = $this->db->query("SELECT a.PLAN_ID,a.PO_ID,LINE_ID,a.COLOR_ID,a.PRODUCTION_DATE,a.PRODUCTION_QNTY  from PPL_SEWING_PLAN_VS_PRO_DTLS a, gbl_temp_engine gbl where a.PO_ID=gbl.ref_val and gbl.entry_form=5001 and gbl.REF_FROM=56 and gbl.user_id=$user_id ")->result();

		foreach ($blance_pro_res_arr as $rows) {
			$blance_color_line_prod_data_arr[$rows->PLAN_ID][$rows->PO_ID][$rows->LINE_ID][$rows->COLOR_ID][$rows->PRODUCTION_DATE] += $rows->PRODUCTION_QNTY;
			$blance_prod_data_arr[$rows->PLAN_ID][$rows->PO_ID][$rows->LINE_ID] += $rows->PRODUCTION_QNTY;
		}
		//.................................blance data


		if ($plan_lavel_data_arr[0]->BULLETIN_TYPE == 2) { //color lavel
			foreach ($plan_po_line_color_date_arr as $plan_id => $poArr) {
				foreach ($poArr as $po_id => $lineArr) {
					foreach ($lineArr as $line => $colorArr) {
						foreach ($colorArr as $color => $rolorRow) {
							foreach ($rolorRow as $date) {
								$plan_production_qnty[$plan_id] += $po_line_color_date_production_arr[$po_id][$line][$color][$date];
								$blance_pro_qty_qnty[$plan_id] += $blance_color_line_prod_data_arr[$plan_id][$po_id][$line][$color][$date];


								$po_wise_colour_production_arr[$plan_id][$po_id][$color] += ($po_line_color_date_production_arr[$po_id][$line][$color][$date] - $blance_color_line_prod_data_arr[$plan_id][$po_id][$line][$color][$date]);
							}
						}
					}
				}
			}
		} else { 
			foreach($plan_po_line_arr as $type =>$plan_arr){
				foreach ($plan_arr as $plan_id => $poArr) {
					foreach ($poArr as $po_id => $lineArr) {
						foreach ($lineArr as $line) {
							$plan_production_qnty[$plan_id] += $po_line_production_arr[$type][$po_id][$line];							
							$blance_pro_qty_qnty[$plan_id] += $blance_prod_data_arr[$plan_id][$line];
						}
					}
				}
			}
				
		}
		//------------------
		//print_r($po_line_production_arr);die;

		//return array(DTLS=>$plan_production_qnty);
		//-----------------
		$tna_table = $this->db->query("SELECT min(a.task_start_date) as TASK_START_DATE,max(a.task_finish_date) as TASK_FINISH_DATE,a.PO_NUMBER_ID from tna_process_mst a, gbl_temp_engine gbl where a.is_deleted=0 and a.status_active=1  and a.task_number in(86,190,191) and a.PO_NUMBER_ID=gbl.ref_val and gbl.entry_form=5001 and gbl.REF_FROM=56 and gbl.user_id=$user_id  group by a.po_number_id")->result(); //$sql


		//$sel_pos = "";
		$tna_task_data[0]['task_start_date'] = date("d-m-Y", time());
		$tna_task_data[0]['task_finish_date'] = date("d-m-Y", time());

		foreach ($tna_table as $srows) {
			$tna_task_data[$srows->PO_NUMBER_ID]['task_start_date'] = date("d-m-Y", strtotime($srows->TASK_START_DATE));
			$tna_task_data[$srows->PO_NUMBER_ID]['task_finish_date'] = date("d-m-Y", strtotime($srows->TASK_FINISH_DATE));
		}
		// $sqls = $this->db->query("SELECT c.PUB_SHIPMENT_DATE, c.GROUPING,c.JOB_NO_MST,c.PO_NUMBER,c.IS_CONFIRMED,b.STYLE_REF_NO, b.BUYER_NAME, c.ID, SUM(C.PO_QUANTITY) PO_QUANTITY from wo_po_details_master b,wo_po_break_down c, gbl_temp_engine gbl where c.job_no_mst=b.job_no and b.status_active=1 and c.id=gbl.ref_val and gbl.entry_form=5001 and gbl.REF_FROM=56 and gbl.user_id=$user_id group by c.GROUPING,c.JOB_NO_MST,c.po_number,c.IS_CONFIRMED,b.style_ref_no,b.buyer_name,c.id,c.pub_shipment_date")->result(); //$sql2 // and c.status_active=1
		//print_r($sqls);die;

		$sqls = $this->db->query("SELECT c.PUB_SHIPMENT_DATE, c.GROUPING,c.JOB_NO_MST,c.PO_NUMBER,c.IS_CONFIRMED,b.STYLE_REF_NO, b.BUYER_NAME, c.ID,  C.PO_QUANTITY from wo_po_details_master b,wo_po_break_down c, gbl_temp_engine gbl where c.job_no_mst=b.job_no and b.status_active=1 and c.id=gbl.ref_val and gbl.entry_form=5001 and gbl.REF_FROM=56 and gbl.user_id=$user_id")->result();
		//print_r($sqls);die;

		foreach ($sqls as $srows) {
			$wo_po_details[$srows->ID]['job_no'] = $srows->JOB_NO_MST;
			$wo_po_details[$srows->ID]['po_number'] = $srows->PO_NUMBER;
			$wo_po_details[$srows->ID]['style_ref'] = $srows->STYLE_REF_NO;
			$wo_po_details[$srows->ID]['buyer_name'] = $srows->BUYER_NAME;
			$wo_po_details[$srows->ID]['po_quantity'] += $srows->PO_QUANTITY;
			$wo_po_details[$srows->ID]['GROUPING'] = $srows->GROUPING;
			$wo_po_details[$srows->ID]['IS_CONFIRMED'] = $srows->IS_CONFIRMED;

			$wo_po_ship_date[$srows->ID] = $srows->PUB_SHIPMENT_DATE;
		}

		//start subcon

		$query_subcon = "SELECT b.ID AS PO_BREAKDOWN_ID , a.PARTY_ID, a.SUBCON_JOB,b.CUST_STYLE_REF, b.ORDER_NO, b.ORDER_QUANTITY,b.DELIVERY_DATE,b.MATERIAL_RECV_DATE FROM SUBCON_ORD_MST a, SUBCON_ORD_DTLS b, SUBCON_ORD_BREAKDOWN c, gbl_temp_engine gbl WHERE b.id=gbl.ref_val and gbl.entry_form=5001 and gbl.REF_FROM=56 and gbl.user_id=$user_id and a.ID = b.MST_ID AND a.ID = c.MST_ID AND a.ENTRY_FORM = 238 AND b.MAIN_PROCESS_ID = '5' AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND c.STATUS_ACTIVE = 1 AND c.IS_DELETED = 0";
		$table_subcon = $sqls = $this->db->query($query_subcon)->result();
		$subcon_details = Array();
		foreach($table_subcon as $row){
			$subcon_details[$row->PO_BREAKDOWN_ID]['SUBCON_JOB'] = $row->SUBCON_JOB;
			$subcon_details[$row->PO_BREAKDOWN_ID]['ORDER_NO'] = $row->ORDER_NO;
			$subcon_details[$row->PO_BREAKDOWN_ID]['CUST_STYLE_REF'] = $row->CUST_STYLE_REF;
			$subcon_details[$row->PO_BREAKDOWN_ID]['PARTY_ID'] = $row->PARTY_ID;
			$subcon_details[$row->PO_BREAKDOWN_ID]['ORDER_QUANTITY'] = $row->ORDER_QUANTITY;
			$subcon_details[$row->PO_BREAKDOWN_ID]['DELIVERY_DATE'] = $row->DELIVERY_DATE;
			$subcon_details[$row->PO_BREAKDOWN_ID]['MATERIAL_RECV_DATE'] = $row->MATERIAL_RECV_DATE;
			$subcon_shipdate[$row->PO_BREAKDOWN_ID] = $row->DELIVERY_DATE;
		}
		//print_r($subcon_details);
		//end subcon
		//print_r($query_subcon);die;
		foreach ($daywise_plan as $rows) {
			if($rows->ORDER_TYPE ==1){
				if ($wo_po_ship_date[$rows->PO_BREAK_DOWN_ID] == $planboard_po_shipdate[$rows->PO_BREAK_DOWN_ID][$rows->PLAN_ID]) {
					unset($planboard_po_shipdate[$rows->PO_BREAK_DOWN_ID][$rows->PLAN_ID]);
				}
			}else if($rows->ORDER_TYPE ==2){
				if ($subcon_shipdate[$rows->PO_BREAK_DOWN_ID] == $planboard_po_shipdate[$rows->PO_BREAK_DOWN_ID][$rows->PLAN_ID]) {
					unset($planboard_po_shipdate[$rows->PO_BREAK_DOWN_ID][$rows->PLAN_ID]);
				}
			}
			
		}
		//print_r($planboard_po_shipdate); die;
		$wo_po_details[0]['job_no'] = '';
		$wo_po_details[0]['po_number'] = '';
		$wo_po_details[0]['style_ref'] = '';
		$wo_po_details[0]['buyer_name'] = '';
		$wo_po_details[0]['po_quantity'] = '';

		$country_sql = "SELECT ID,COUNTRY_NAME from  LIB_COUNTRY";
		$countries = $this->db->query($country_sql)->result();
		$country_arr = array();
		foreach ($countries as $crows) {
			$country_arr[$crows->ID] = $crows->COUNTRY_NAME;
		}

		// ini_set('display_errors',0);

		$i = 0;
		$data_array = array();
		$urls = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$url_arr = explode('/', $urls);
		$actual_link = $url_arr[0] . "/" . $url_arr[1];


		//print_r($img);die;


		//print_r($plan_data);die;
		foreach ($plan_data as $rows) {

			$ROWS_PO_BREAK_DOWN_ID_ARR2 = $po_arr_by_plan[$rows->PLAN_ID];
			$ROWS_PO_BREAK_DOWN_ID_ARR = array_unique($po_arr_by_plan[$rows->PLAN_ID]);
			$ROWS_ITEM_NUMBER_ID_ARR = $item_arr_by_plan[$rows->PLAN_ID];
			$ROWS_SIZE_NUMBER_ID_ARR = $size_arr_by_plan[$rows->PLAN_ID];
			$ROWS_COLOR_NUMBER_ID_ARR = $color_arr_by_plan[$rows->PLAN_ID];
			$ROWS_COUNTRY_ID_ARR = $country_arr_by_plan[$rows->PLAN_ID];


			$line_plan_qty_total_arr[$rows->LINE_ID] += $rows->PLAN_QNTY;
			$line_plan_smv_min_arr[$rows->LINE_ID] += ($rows->PLAN_QNTY * $rows->SMV);



			//start without plan production qty distibute on plan line ...............
			$po_line_wise_plan_serial[end($ROWS_PO_BREAK_DOWN_ID_ARR)][$rows->LINE_ID] = $i;
			$po_line_wise_plan_id[end($ROWS_PO_BREAK_DOWN_ID_ARR)][$rows->LINE_ID] = $rows->PLAN_ID;
			//............................................end;

			// echo $rows->COLOR_NUMBER_ID; 
			// var_dump($po_line_color_production_arr[$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID]);die;	


			$production_line_qnty = 0;
			if ($plan_lavel_data_arr[0]->BULLETIN_TYPE == 2) { //color lavel
				if (!empty($production_arr) && (count($ROWS_PO_BREAK_DOWN_ID_ARR) > 0)) {
					foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $po_id) {
						foreach ($ROWS_COLOR_NUMBER_ID_ARR as $cid) {
							if($rows->ORDER_TYPE==1){
								if ($po_line_color_production_arr[1][(int)$po_id][$rows->LINE_ID][$cid]) {
									$production_line_qnty += $po_line_color_production_arr[1][(int)$po_id][$rows->LINE_ID][$cid];
								}
							}else if($rows->ORDER_TYPE==2){
								if ($po_line_color_production_arr[2][(int)$po_id][$rows->LINE_ID][$cid]) {
									$production_line_qnty += $po_line_color_production_arr[2][(int)$po_id][$rows->LINE_ID][$cid];
								}
							}
							
						}
					}
				}
			} else {
				if (!empty($production_arr) && (count($ROWS_PO_BREAK_DOWN_ID_ARR) > 0)) {
					foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $po_val) {
						$po_val = $po_val * 1;
						if ($production_arr[$po_val][$rows->LINE_ID]["PRODUCTION_QNTY"]) {
							$production_line_qnty += $production_arr[$po_val][$rows->LINE_ID]["PRODUCTION_QNTY"];
						}
					}
				}
			}
			//var_dump($production_arr);die;
			
			$plan_production_qnty_rest[$rows->PLAN_ID] = ($plan_production_qnty[$rows->PLAN_ID] - $blance_pro_qty_qnty[$rows->PLAN_ID]) * 1;
			
			
			

			$data_array[$i]["IS_PRODUCTION"] = ($plan_production_qnty_rest[$rows->PLAN_ID] <= 0) ? false : true;
			$data_array[$i]["THIS_PRODUCTION"] = $plan_production_qnty_rest[$rows->PLAN_ID];
			//$data_array[$i]["inv_status"] = $inv_status_by_plan[$rows->PLAN_ID];
			//print_r($plan_production_qnty_subcon);die;


			if (count($ROWS_PO_BREAK_DOWN_ID_ARR) == 0) {
				$ROWS_PO_BREAK_DOWN_ID_ARR = 0;
				$ROWS_COLOR_NUMBER_ID_ARR = 0;
				$ROWS_SIZE_NUMBER_ID_ARR = 0;
				$ROWS_ITEM_NUMBER_ID_ARR = 0;
			}

			//$plan_level = $rows->PLAN_LEVEL;

			// $color_name_arr = array();
			// foreach ($ROWS_COLOR_NUMBER_ID_ARR as $cid) {
			// 	$color_name_arr[$cid] = $this->get_field_value_by_attribute("LIB_COLOR", "COLOR_NAME", $cid);
			// }
			$color_name_arr = array();
			foreach ($ROWS_COLOR_NUMBER_ID_ARR as $cid) {
				$color_name_arr[$cid] = $lib_color_arr[$cid];
			}
			//print_r($color_name_arr);die;
			$size_name_arr = array();
			foreach ($ROWS_COLOR_NUMBER_ID_ARR as $cid) {
				$size_name_arr[$cid] = $lib_size_arr[$cid];
			}

			$color_name = implode(',', $color_name_arr);
			$size_name = implode(',', $size_name_arr);

			$multi_po = $rows->MULTI_PO;
			$all_subcon_buyer_name = array();
			$pub_ship_arr = array();
			$subcon_delivery_date_arr = array();
			if ($multi_po == 0) {
				$all_buyer = array();
				foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $v) {
					$buyer_name = $buyer_arr[$wo_po_details[$v]['buyer_name']];
					$all_buyer[$buyer_name] = $buyer_name;

					$subcon_buyer_name = $buyer_arr[$subcon_details[$v]['PARTY_ID']];
					$all_subcon_buyer_name[] = $subcon_buyer_name;

					$pub_ship_date = $pub_ship_date_arr_by_po[$v];
					$pub_ship_arr[] = $pub_ship_date;

					$subcon_delivery_date = $subcon_details[$v]['DELIVERY_DATE'];
					$subcon_delivery_date_arr[] = $subcon_delivery_date;
				}

				if($rows->ORDER_TYPE == 1){
					$data_array[$i]["BUYER_NAME"] = implode(', ', $all_buyer);
					$data_array[$i]["PUB_SHIP_DATES"] = implode(', ', $pub_ship_arr);
				}elseif($rows->ORDER_TYPE == 2){
					$data_array[$i]["BUYER_NAME"] = implode(', ', $all_subcon_buyer_name);
					$data_array[$i]["PUB_SHIP_DATES"] = implode(', ', $subcon_delivery_date_arr);
				}
				
			} else {
				$all_buyer = array();
				foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $v) {
					$buyer_name = $buyer_arr[$wo_po_details[$v]['buyer_name']];
					$all_buyer[$buyer_name] = $buyer_name;

					$subcon_buyer_name = $buyer_arr[$subcon_details[$v]['PARTY_ID']];
					$all_subcon_buyer_name[] = $subcon_buyer_name;

					$pub_ship_date = $pub_ship_date_arr_by_po[$v];
					$pub_ship_arr[] = $pub_ship_date;

					$subcon_delivery_date = $subcon_details[$v]['DELIVERY_DATE'];
					$subcon_delivery_date_arr[] = $subcon_delivery_date;
				}
				if($rows->ORDER_TYPE == 1){
					$data_array[$i]["BUYER_NAME"] = implode(', ', $all_buyer);
					$data_array[$i]["PUB_SHIP_DATES"] = implode(', ', $pub_ship_arr);
				}elseif($rows->ORDER_TYPE == 2){
					$data_array[$i]["BUYER_NAME"] = implode(', ', $all_subcon_buyer_name);
					$data_array[$i]["PUB_SHIP_DATES"] = implode(', ', $subcon_delivery_date_arr);
				}
			}

			$groupingArr = array();
			foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $v) {
				if ($wo_po_details[$v]['GROUPING'] != '') $groupingArr[] = $wo_po_details[$v]['GROUPING'];
			}

			//$proddate_cond = " '" . date("d-M-Y", strtotime($rows->START_DATE)) . "' and '" . date("d-M-Y", strtotime($rows->END_DATE)) . "'";
			//$lin = $rows->LINE_ID;

			$country_name = "";
			if (!empty($ROWS_COUNTRY_ID_ARR)) {
				foreach ($ROWS_COUNTRY_ID_ARR as $country) {
					if ($country > 0) {
						$country_name = $country_arr[$country] . ",";
					}
				}
			}
			//$production_qnty = $this->get_production_qnty_by_po_item($rows->PO_BREAK_DOWN_ID, $lin, $proddate_cond, 3);

			$data_array[$i]["INTER_REF"] = implode(',', $groupingArr);
			//$data_array[$i]["COLOR_NUMBER_ID"] = implode(',', $ROWS_COLOR_NUMBER_ID_ARR);
			$data_array[$i]["COLOR_NUMBER_ID"] = implode(',', array_unique($ROWS_COLOR_NUMBER_ID_ARR));
			$data_array[$i]["COLOR_NUMBER"] = (!empty($color_name)) ? $color_name : "";
			$data_array[$i]["SIZE_NUMBER_ID"] = implode(',', $ROWS_SIZE_NUMBER_ID_ARR);
			$data_array[$i]["SIZE_NUMBER"] = (!empty($size_name)) ? $size_name : "";
			//$multi_po= $rows->MULTI_PO ;
			$all_subcon_job = array();
			if ($multi_po == 0) {
				$all_jobs = array();

				foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $v) {
					$job_no = $wo_po_details[$v]['job_no'];
					$all_jobs[] = $job_no;

					$subcon_job = $subcon_details[$v]['SUBCON_JOB'];
					$all_subcon_job[] = $subcon_job;
				}

				if($rows->ORDER_TYPE == 1){
					$data_array[$i]["JOB_NO"] = implode(', ', $all_jobs);
				}elseif($rows->ORDER_TYPE == 2){
					$data_array[$i]["JOB_NO"] = implode(', ', $all_subcon_job);
				}
				
			} else {
				$all_jobs = array();

				foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $v) {
					$job_no = $wo_po_details[$v]['job_no'];
					//$all_jobs[$job_no] = $job_no;
					$all_jobs[] = $job_no;

					$subcon_job = $subcon_details[$v]['SUBCON_JOB'];
					$all_subcon_job[] = $subcon_job;
				}
				if($rows->ORDER_TYPE == 1){
					$data_array[$i]["JOB_NO"] = implode(', ', $all_jobs);
				}elseif($rows->ORDER_TYPE == 2){
					$data_array[$i]["JOB_NO"] = implode(', ', $all_subcon_job);
				}
			}

			$data_array[$i]["JOB_IMG"] = ($po_wise_img_location_arr[end($ROWS_PO_BREAK_DOWN_ID_ARR)]) ? "http://$actual_link/" . $po_wise_img_location_arr[end($ROWS_PO_BREAK_DOWN_ID_ARR)] : "";

			//$data_array[$i]["JOB_IMG"] = $this->get_job_images(implode(',', $ROWS_PO_BREAK_DOWN_ID_ARR), $actual_link);


			$data_array[$i]["PO_COMPANY_ID"] = $rows->PO_COMPANY_ID;
			
			$all_subcon_style = array();
			if ($multi_po == 0) {
				

				$all_style = array();
				foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $v) {
					$style_ref = $wo_po_details[$v]['style_ref'];
					//$all_style[$style_ref] = $style_ref;
					$all_style[] = $style_ref;

					$subcon_style = $subcon_details[$v]['CUST_STYLE_REF'];
					$all_subcon_style[] = $subcon_style;
				}
				if($rows->ORDER_TYPE == 1){
					$data_array[$i]["STYLE_REF_NO"] = implode(', ', $all_style);
				}elseif($rows->ORDER_TYPE == 2){
					$data_array[$i]["STYLE_REF_NO"] = implode(', ', $all_subcon_style);
				}
				
				
			} else {
				$all_style = array();
				
				
				foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $v) {
					$style_ref = $wo_po_details[$v]['style_ref'];
					$all_style[] = $style_ref;

					$subcon_style = $subcon_details[$v]['CUST_STYLE_REF'];
					$all_subcon_style[] = $subcon_style;
					//$all_style[$style_ref] = $style_ref;

					
				}
				if($rows->ORDER_TYPE == 1){
					$data_array[$i]["STYLE_REF_NO"] = implode(', ', $all_style);
				}elseif($rows->ORDER_TYPE == 2){
					$data_array[$i]["STYLE_REF_NO"] = implode(', ', $all_subcon_style);
				}
				
			}

			$data_array[$i]["PO_BREAK_DOWN_ID"] = implode(',', $ROWS_PO_BREAK_DOWN_ID_ARR2) . '';
			$all_subcon_po_number = array();
			if ($multi_po == 0) {

				$all_po = "";

				foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $v) {
					if ($all_po) {

						if (isset($wo_po_details[$v]['po_number'])) {

							$all_po .= ',' . $wo_po_details[$v]['po_number'];
						}
					} else {
						if (isset($wo_po_details[$v]['po_number'])) {
							$all_po .= $wo_po_details[$v]['po_number'];
						}
					}

					$subcon_po_number = $subcon_details[$v]['ORDER_NO'];
					$all_subcon_po_number[] = $subcon_po_number;
				}

				if($rows->ORDER_TYPE == 1){
					$data_array[$i]["PO_NUMBER"] = $all_po;
				}elseif($rows->ORDER_TYPE == 2){
					$data_array[$i]["PO_NUMBER"] = implode(', ', $all_subcon_po_number);
				}
				
			} else {

				$all_po = "";

				foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $v) {
					if ($all_po) {

						if ($wo_po_details[$v]['po_number']) {

							$all_po .= ',' . $wo_po_details[$v]['po_number'];
						}
						//return $all_po;

					} else {
						if ($wo_po_details[$v]['po_number']) {
							$all_po .= $wo_po_details[$v]['po_number'];
						}
					}

					$subcon_po_number = $subcon_details[$v]['ORDER_NO'];
					$all_subcon_po_number[] = $subcon_po_number;
				}
				if($rows->ORDER_TYPE == 1){
					$data_array[$i]["PO_NUMBER"] = $all_po;
				}elseif($rows->ORDER_TYPE == 2){
					$data_array[$i]["PO_NUMBER"] = implode(', ', $all_subcon_po_number);
				}
			}
			$all_subcon_po_quantity = array();
			if ($multi_po == 0) {

				$all_po_qnty = array();
				foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $v) {
					if ($wo_po_details[$v]['po_quantity']) {
						$all_po_qnty[$v] = $wo_po_details[$v]['po_quantity'];
					}

					$subcon_po_quantity = $subcon_details[$v]['ORDER_QUANTITY'];
					$all_subcon_po_quantity[] = $subcon_po_quantity;
				}

				if($rows->ORDER_TYPE == 1){
					$data_array[$i]["PO_QUANTITY"] = array_sum($all_po_qnty);
				}elseif($rows->ORDER_TYPE == 2){
					$data_array[$i]["PO_QUANTITY"] = array_sum($all_subcon_po_quantity);
				}
				
			} else {

				$all_po_qnty = array();
				foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $v) {
					if ($wo_po_details[$v]['po_quantity']) {
						$all_po_qnty[$v] = $wo_po_details[$v]['po_quantity'];
					}

					$subcon_po_quantity = $subcon_details[$v]['ORDER_QUANTITY'];
					$all_subcon_po_quantity[] = $subcon_po_quantity;
				}
				if($rows->ORDER_TYPE == 1){
					$data_array[$i]["PO_QUANTITY"] = array_sum($all_po_qnty);
				}elseif($rows->ORDER_TYPE == 2){
					$data_array[$i]["PO_QUANTITY"] = array_sum($all_subcon_po_quantity);
				}
			}
			$job_smv = ($rows->SMV) ? $rows->SMV : 0;
			$data_array[$i]["SMV"] = number_format($job_smv, 2);
			$data_array[$i]["line_id"] = $rows->LINE_ID;
			$data_array[$i]["plan_id"] = $rows->PLAN_ID;
			$data_array[$i]["SEQ_NO"] = $rows->SEQ_NO * 1;
			$data_array[$i]["PLAN_LEVEL"] = $rows->PLAN_LEVEL;
			$data_array[$i]["start_date"] = date("d-m-Y", strtotime($rows->START_DATE));
			$data_array[$i]["start_hour"] = $rows->START_HOUR;
			$data_array[$i]["end_date"] = date("d-m-Y", strtotime($rows->END_DATE));
			$data_array[$i]["end_hour"] = $rows->END_HOUR;
			$data_array[$i]["REMAINING_WORK_HOUR"] = $rows->REMAINING_WORK_HOUR;
			$data_array[$i]["duration"] = number_format($rows->DURATION, 2);
			$data_array[$i]["plan_qnty"] = $rows->PLAN_QNTY;
			$data_array[$i]["comp_level"] = $rows->COMP_LEVEL;
			if (isset($rows->NEXT_FIRST_DAY_OUTPUT)) {
				$data_array[$i]["next_first_day_output"] = $rows->NEXT_FIRST_DAY_OUTPUT;
			} else {
				$data_array[$i]["next_first_day_output"] = 0;
			}

			if (isset($rows->NEXT_INCREMENT)) {
				$data_array[$i]["next_increment"] = $rows->NEXT_INCREMENT;
			} else {
				$data_array[$i]["next_increment"] = 0;
			}

			if (isset($rows->NEXT_TERGET)) {
				$data_array[$i]["next_terget"] = $rows->NEXT_TERGET;
			} else {
				$data_array[$i]["next_terget"] = 0;
			}

			if (isset($rows->LEARING_ITERATOR)) {
				$data_array[$i]["learing_iterator"] = $rows->LEARING_ITERATOR;
			} else {
				$data_array[$i]["learing_iterator"] = 0;
			}

			$data_array[$i]["first_day_output"] = $rows->FIRST_DAY_OUTPUT;
			$data_array[$i]["increment_qty"] = $rows->INCREMENT_QTY;
			$data_array[$i]["terget"] = $rows->TERGET;
			$data_array[$i]["company_id"] = $rows->COMPANY_ID;
			$data_array[$i]["company_name"] = $comp[$rows->COMPANY_ID];
			if (isset($rows->LOCATION_ID)) {
				$data_array[$i]["location_id"] = $rows->LOCATION_ID;
			} else {
				$data_array[$i]["location_id"] = 0;
			}

			$data_array[$i]["COUNTRY_NAME"] = rtrim($country_name, ", ");
			$data_array[$i]["COUNTRY_ID"] = implode(',', $ROWS_COUNTRY_ID_ARR) . '';
			if (isset($location_arr[$rows->LOCATION_ID])) {
				$data_array[$i]["location_name"] = $location_arr[$rows->LOCATION_ID];
			} else {
				$data_array[$i]["location_name"] = "";
			}

			$data_array[$i]["item_number_id"] = implode(',', $ROWS_ITEM_NUMBER_ID_ARR) . '';
			if (count($ROWS_ITEM_NUMBER_ID_ARR)) {
				if (isset($garments_item[end($ROWS_ITEM_NUMBER_ID_ARR)])) {
					$data_array[$i]["item_name"] = $garments_item[end($ROWS_ITEM_NUMBER_ID_ARR)];
				} else {
					$data_array[$i]["item_name"] = "";
				}
			} else {
				$data_array[$i]["item_name"] = "";
			}
			$data_array[$i]["off_day_plan"] = $rows->OFF_DAY_PLAN;
			$data_array[$i]["order_complexity"] = $rows->ORDER_COMPLEXITY;
			$data_array[$i]["ship_date"] = date("d-m-Y", strtotime($rows->SHIP_DATE));
			$data_array[$i]["USE_LEARNING_CURVE"] = $rows->USE_LEARNING_CURVE;
			//$data_array[$i]["CURRENT_PRODUCTION_DATE"] = date("d-m-Y", strtotime($production_qnty['prd_date']));
			$data_array[$i]["CURRENT_PRODUCTION_DATE"] = "01-01-1970";
			$production_qnty['production_data_qnty'] = $production_line_qnty;
			$data_array[$i]["PRODUCTION_PERCENT"] = $production_qnty['production_data_qnty'];
			//$data_array[$i]["PRODUCTION_PERCENT"] =0;
			//echo $data_array[$i]["PRODUCTION_PERCENT"];die;

			$data_array[$i]["ALLOCATED_MP"] = $rows->ALLOCATED_MP ? $rows->ALLOCATED_MP : 0;
			$data_array[$i]["BYPASS_MP"] = $rows->BYPASS_MP ? $rows->BYPASS_MP : 0;
			$data_array[$i]["AUTO_TARGET"] = $rows->AUTO_TARGET ? $rows->AUTO_TARGET : 0;







			//print_r($inv_status_by_plan);die;
			//$first_booking_delivery_date[$rows->PLAN_ID];
			if (strtotime($today) < strtotime($first_booking_delivery_date[$rows->PLAN_ID])) {
				$data_array[$i]["TOP_BORDER_COLOR"] = "#ded3d3";
			} elseif (strtotime($today) == strtotime($first_booking_delivery_date[$rows->PLAN_ID]) && $inv_status_by_plan[$rows->PLAN_ID] == 1) {
				$data_array[$i]["TOP_BORDER_COLOR"] = "#3363ff";
			} elseif (strtotime($today) > strtotime($first_booking_delivery_date[$rows->PLAN_ID]) && $inv_status_by_plan[$rows->PLAN_ID] == 1) {
				$data_array[$i]["TOP_BORDER_COLOR"] = "#ffff00";
			} elseif (strtotime($today) >= strtotime($first_booking_delivery_date[$rows->PLAN_ID]) && $inv_status_by_plan[$rows->PLAN_ID] == 2) {
				$data_array[$i]["TOP_BORDER_COLOR"] = "#33cc33";
			} else {
				$data_array[$i]["TOP_BORDER_COLOR"] = "#ff0000";
			}

			// elseif ($rows->INCREMENT_QTY > 0) {
			// 	$data_array[$i]["TOP_BORDER_COLOR"] = $rows->TOP_BORDER_COLOR;
			// } else {
			// 	$data_array[$i]["TOP_BORDER_COLOR"] = "#FF9900";
			// }

			//print_r($lib_booking_delivery_date);die;
			//if ($plan_production_qnty[$rows->PLAN_ID] > 0) {
			if ($data_array[$i]["THIS_PRODUCTION"] > 0) {
				$data_array[$i]["BOTTOM_BORDER_COLOR"] = "#008000";
			} else {
				//print_r($data_array[$i]["THIS_PRODUCTION"]);die;
				$data_array[$i]["BOTTOM_BORDER_COLOR"] = $rows->BOTTOM_BORDER_COLOR;
			}


			if (strtotime($rows->SHIP_DATE) > strtotime($rows->END_DATE)) {
				$data_array[$i]["LEFT_COLOR"] = "#73CAD5";
				$data_array[$i]["RIGHT_COLOR"] = "#73CAD5";
			}
			if ($production_qnty['production_data_qnty'] < 1 && time() > strtotime($rows->END_DATE)) //No Production but date crossed
			{
				$data_array[$i]["LEFT_COLOR"] = "#909553";
				$data_array[$i]["RIGHT_COLOR"] = "#909553";
			}


			(!empty($tna_task_data[end($ROWS_PO_BREAK_DOWN_ID_ARR)]['task_finish_date'])) ? $tna_task_data[end($ROWS_PO_BREAK_DOWN_ID_ARR)]['task_finish_date'] : $tna_task_data[end($ROWS_PO_BREAK_DOWN_ID_ARR)]['task_finish_date'] = date("d-m-Y", strtotime("1971-01-01"));
			(!empty($tna_task_data[end($ROWS_PO_BREAK_DOWN_ID_ARR)]['task_start_date'])) ? $tna_task_data[end($ROWS_PO_BREAK_DOWN_ID_ARR)]['task_start_date'] : $tna_task_data[end($ROWS_PO_BREAK_DOWN_ID_ARR)]['task_start_date'] = date("d-m-Y", strtotime("1971-01-01"));

			//Change Strip COLOR ON TNA date if plan is created before TNA start Date
			if (strtotime($tna_task_data[$ROWS_PO_BREAK_DOWN_ID_ARR[0]]['task_start_date']) > strtotime($rows->START_DATE)) {
				$data_array[$i]["LEFT_COLOR"] = "#006699";
				$data_array[$i]["RIGHT_COLOR"] = "#006699";
			}


			if (strtotime($tna_task_data[end($ROWS_PO_BREAK_DOWN_ID_ARR)]['task_finish_date']) > strtotime($rows->START_DATE) && strtotime($tna_task_data[end($ROWS_PO_BREAK_DOWN_ID_ARR)]['task_finish_date']) < strtotime($rows->END_DATE)) // Partial plan TNA Date crossed
			{
				//$difference = round ((strtotime($rows->END_DATE) - strtotime($rows->SHIP_DATE))/(3600*24));
				$data_array[$i]["LEFT_COLOR"] = "#FF6600";
				$data_array[$i]["RIGHT_COLOR"] = "#FF6600";
				$data_array[$i]["DAYS"] = date_diff_days($tna_task_data[end($ROWS_PO_BREAK_DOWN_ID_ARR)]['task_finish_date'], $rows->END_DATE);
			} else if (strtotime($tna_task_data[end($ROWS_PO_BREAK_DOWN_ID_ARR)]['task_finish_date']) <= strtotime($rows->START_DATE)) // Full Plan TNA date crossed
			{
				$data_array[$i]["LEFT_COLOR"] = "#FF6600";
				$data_array[$i]["RIGHT_COLOR"] = "#FF6600";
			}

			if (strtotime($rows->SHIP_DATE) > strtotime($rows->START_DATE) && strtotime($rows->SHIP_DATE) < strtotime($rows->END_DATE)) // Partial Ship Date crossed
			{
				//$difference = round ((strtotime($rows->END_DATE) - strtotime($rows->SHIP_DATE))/(3600*24));
				$data_array[$i]["LEFT_COLOR"] = "RED";
				$data_array[$i]["RIGHT_COLOR"] = "RED";
				$data_array[$i]["DAYS"] = date_diff_days($rows->SHIP_DATE, $rows->END_DATE);
			}

			if (strtotime($rows->SHIP_DATE) <= strtotime($rows->START_DATE)) // Full Plan ship date crossed
			{
				$data_array[$i]["LEFT_COLOR"] = "RED";
				$data_array[$i]["RIGHT_COLOR"] = "RED";
			}

			if (strtotime($from_date) > strtotime($rows->START_DATE)) // Crossed date in board
			{
				$data_array[$i]["LEFT_COLOR"] = "#9C8AE3";
				$data_array[$i]["RIGHT_COLOR"] = "#9C8AE3";
			}

			$data_array[$i]["IS_CONFIRMED"] = $wo_po_details[end($ROWS_PO_BREAK_DOWN_ID_ARR)]['IS_CONFIRMED'];

			//$npo;
			$early_date = "01-01-1971";
			$delay_date = "01-01-1971";
			$st_date = "";
			$end_date = "";
			foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $v) {
				if (isset($tna_task_data[$v]['task_start_date'])) {
					$task_start_date = strtotime($tna_task_data[$v]['task_start_date']);
					if ($st_date == "") {
						$st_date = $task_start_date;
						$early_date = $tna_task_data[$v]['task_start_date'];
					} else {
						if ($task_start_date < $st_date) {
							$early_date = $tna_task_data[$v]['task_start_date'];
						}
					}

					if (isset($tna_task_data[$v]['task_finish_date'])) {
						$task_finish_date = strtotime($tna_task_data[$v]['task_finish_date']);
						if ($end_date == "") {
							$end_date = $task_finish_date;
							$delay_date = $tna_task_data[$v]['task_finish_date'];
						} else {
							if ($task_finish_date > $end_date) {
								$delay_date = $tna_task_data[$v]['task_finish_date'];
							}
						}
					}
				}
			}
			$data_array[$i]["TASK_START_DATE"] = $early_date;
			$data_array[$i]["TASK_END_DATE"] = $delay_date;
			$data_array[$i]["MERGE_TYPE"] = $rows->MERGE_TYPE;
			$data_array[$i]["MERGE_COMMENTS"] = $rows->MERGE_COMMENTS;
			$data_array[$i]["TABLE_LOCKED"] = $table_locked;
			$data_array[$i]["INSERT_DATE"] = date("d-m-Y", strtotime($rows->INSERT_DATE)); //$rows->MERGE_TYPE;
			$data_array[$i]["PRODUCTION_QNTY"] = $plan_production_qnty[$rows->PLAN_ID] * 1; //$production_line_qnty;
			$data_array[$i]["TOTAL_PRODUCTION"] = 0;
			$data_array[$i]["TOTAL_PRODUCTION_ITEM"] = 0;

			$data_array[$i]["IS_FREEZE"] = 0;



			if ($plan_lavel_data_arr[0]->BULLETIN_TYPE == 2) { //color lavel
				if (!empty($production_arr) && (count($ROWS_PO_BREAK_DOWN_ID_ARR) > 0)) {
					foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $po_val) {
						foreach ($ROWS_COLOR_NUMBER_ID_ARR as $color_id) {
							$po_val = $po_val * 1;
							$color_id = $color_id * 1;
							
							if($rows->ORDER_TYPE == 1){
								if ($po_line_color_production_arr[1][$po_val][$rows->LINE_ID][$color_id]) {
									$data_array[$i]["TOTAL_PRODUCTION"] += $po_line_color_production_arr[1][$po_val][$rows->LINE_ID][$color_id];
								}
							}elseif($rows->ORDER_TYPE == 2){
								if ($po_line_color_production_arr[2][$po_val][$rows->LINE_ID][$color_id]) {
									$data_array[$i]["TOTAL_PRODUCTION"] += $po_line_color_production_arr[2][$po_val][$rows->LINE_ID][$color_id];
								}								
							}
							
							//item qty..............................
							foreach ($ROWS_ITEM_NUMBER_ID_ARR as $itemID) {
								$itemID = $itemID * 1;
								if($rows->ORDER_TYPE == 1){
									if ($item_po_color_wise_productionArr[1][$itemID][$po_val][$color_id]) {
										$data_array[$i]["TOTAL_PRODUCTION_ITEM"] += $item_po_color_wise_productionArr[1][$itemID][$po_val][$color_id];
									}
								}elseif($rows->ORDER_TYPE == 2){
									if ($item_po_color_wise_productionArr[2][$itemID][$po_val][$color_id]) {
										$data_array[$i]["TOTAL_PRODUCTION_ITEM"] += $item_po_color_wise_productionArr[2][$itemID][$po_val][$color_id];
									}																		
								}								
							}
						}
					}
				}
				
			} else {
				
				foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $poID) {
					if($rows->ORDER_TYPE == 1){
						$data_array[$i]["TOTAL_PRODUCTION"] += $po_wise_productionArr[1][$poID];
					//item qty..............................
						foreach ($ROWS_ITEM_NUMBER_ID_ARR as $itemID) {
						$data_array[$i]["TOTAL_PRODUCTION_ITEM"] += $item_wise_productionArr[1][$itemID][$poID];
						}
					}elseif($rows->ORDER_TYPE == 2){
						$data_array[$i]["TOTAL_PRODUCTION"] += $po_wise_productionArr[2][$poID];
					//item qty..............................
						foreach ($ROWS_ITEM_NUMBER_ID_ARR as $itemID) {
						$data_array[$i]["TOTAL_PRODUCTION_ITEM"] += $item_wise_productionArr[2][$itemID][$poID];
						}
					}
					
				}
			}


			//  if($i  == 7){ print_r($ROWS_PO_BREAK_DOWN_ID_ARR);die;echo $data_array[7]["TOTAL_PRODUCTION"];die;}

			$data_array[$i]["PRODUCTION_DAY"] = $rows->LINE_ID;
			if (isset($rows->HALF)) {
				$data_array[$i]["HALF"] = $rows->HALF;
			} else {
				$data_array[$i]["HALF"] = 0;
			}

			$data_array[$i]["NOTES"] = $rows->NOTES;

			$data_array[$i]["CLOSING_STATUS"] = $rows->CLOSING_STATUS;
			$data_array[$i]["CLOSED_BY"] = $rows->CLOSED_BY;
			$data_array[$i]["CLOSING_DATE"] = $rows->CLOSING_DATE;
			$data_array[$i]["CLOSING_NOTE"] = $rows->CLOSING_NOTE;
			$data_array[$i]["RE_OPEN_DATE"] = $rows->RE_OPEN_DATE;
			$data_array[$i]["RE_OPENED_BY"] = $rows->RE_OPENED_BY;
			$data_array[$i]["RE_OPEN_NOTE"] = $rows->RE_OPEN_NOTE;

			$data_array[$i]["SET_DTLS_ID"] = ($rows->SET_DTLS_ID) ? $rows->SET_DTLS_ID : 0;
			$data_array[$i]["COLOR_SIZE_ID"] = ($rows->COLOR_SIZE_ID) ? $rows->COLOR_SIZE_ID : 0;
			//$data_array[$i]["BYPASS_MP"] = 0;




			//---------------
			if (!empty($gsd_data_arr['OPERATOR'][$data_array[$i]["JOB_NO"]])) {
				$data_array[$i]["EFFICIENCY"] = $gsd_data_arr['EFFICIENCY'][$data_array[$i]["JOB_NO"]];
				$data_array[$i]["TARGET"] = $gsd_data_arr['TARGET'][$data_array[$i]["JOB_NO"]];
				$data_array[$i]["HELPER"] = $gsd_data_arr['HELPER'][$data_array[$i]["JOB_NO"]];
				$data_array[$i]["MACHINE"] = $gsd_data_arr['MACHINE'][$data_array[$i]["JOB_NO"]];
				$data_array[$i]["OPERATOR"] = $gsd_data_arr['OPERATOR'][$data_array[$i]["JOB_NO"]];
				//$data_array[$i]["ALLOCATED_MP"] = $gsd_data_arr['ALLOCATED_MP'][$data_array[$i]["JOB_NO"]];
			} else {
				$data_array[$i]["EFFICIENCY"] = '';
				$data_array[$i]["TARGET"] = '';
				$data_array[$i]["HELPER"] = '';
				$data_array[$i]["MACHINE"] = '';
				$data_array[$i]["OPERATOR"] = '';
				//$data_array[$i]["ALLOCATED_MP"] = 0;
			}

			$data_array[$i]["LOCATION_X"] = $rows->LOCATION_X;
			$data_array[$i]["LOCATION_Y"] = $rows->LOCATION_Y;
			$data_array[$i]["MERGED_PLAN_ID"] = $rows->MERGED_PLAN_ID * 1;



			$data_array[$i]["REF_CHANGED"] = 0;
			$data_array[$i]["COLOR_CHANGED"] = 0;
			$data_array[$i]["VALUE_CHANGED"] = 0;

			if($rows->ORDER_TYPE == 1){
				foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $poID) {
					foreach ($ROWS_COLOR_NUMBER_ID_ARR as $colorID) {
	
						if ($changed_data_arr['COLOR_CHANGED'][1][$poID][$colorID]) {
							$data_array[$i]["REF_CHANGED"] = 1;
							$data_array[$i]["COLOR_CHANGED"] = 1;
							$data_array[$i]["RIGHT_COLOR"] = '#999999';
							$data_array[$i]["LEFT_COLOR"] = '#999999';
						} else if ($changed_data_arr['VALUE_CHANGED'][1][$poID][$colorID]) {
							$data_array[$i]["REF_CHANGED"] = 1;
							$data_array[$i]["VALUE_CHANGED"] = 1;
							$data_array[$i]["RIGHT_COLOR"] = '#999999';
							$data_array[$i]["LEFT_COLOR"] = '#999999';
						}
					}
				}
			}else if($rows->ORDER_TYPE == 2)
			{
				foreach ($ROWS_PO_BREAK_DOWN_ID_ARR as $poID) {
					foreach ($ROWS_COLOR_NUMBER_ID_ARR as $colorID) {
	
						if ($changed_data_arr['COLOR_CHANGED'][2][$poID][$colorID]) {
							$data_array[$i]["REF_CHANGED"] = 1;
							$data_array[$i]["COLOR_CHANGED"] = 1;
							$data_array[$i]["RIGHT_COLOR"] = '#999999';
							$data_array[$i]["LEFT_COLOR"] = '#999999';
						} else if ($changed_data_arr['VALUE_CHANGED'][2][$poID][$colorID]) {
							$data_array[$i]["REF_CHANGED"] = 1;
							$data_array[$i]["VALUE_CHANGED"] = 1;
							$data_array[$i]["RIGHT_COLOR"] = '#999999';
							$data_array[$i]["LEFT_COLOR"] = '#999999';
						}
					}
				}
			}
			

			//print_r($rows);die;
			if ($planboard_po_shipdate[$pobreakdown_id[$rows->PLAN_ID]][$rows->PLAN_ID]) {

				$data_array[$i]["SHIP_DATE_CHANGED"] = 1;
			} else {
				//print_r($pobreakdown_id[$rows->PLAN_ID]);die;
				$data_array[$i]["SHIP_DATE_CHANGED"] = 0;
			}


			$data_array[$i]["ITEM_NAME"] = $rows->ITEM_NAME;
			$data_array[$i]["ITEM_NUMBER_ID_H"] = $rows->ITEM_NUMBER_ID_H;
			$data_array[$i]["STYLE_REF_NO_H"] = $rows->STYLE_REF_NO;
			//$data_array[$i]["COLOR_NAME"]=$rows->COLOR_NAME;
			$data_array[$i]["COLOR_NUMBER_H"] = $rows->COLOR_NUMBER_H;
			//$data_array[$i]["PO_NUMBER_H"]=$rows->PO_NUMBER;
			$data_array[$i]["COLOR_NUMBER_ID_H"] = $rows->COLOR_NUMBER_ID_H;
			//$data_array[$i]["COLOR_NUMBER_ID"]=$rows->COLOR_NUMBER_ID;
			$data_array[$i]["JOB_NO_H"] = $rows->JOB_NO;
			$data_array[$i]["ITEM_NUMBER_ID_H"] = $rows->ITEM_NUMBER_ID_H;
			$data_array[$i]["PO_QUANTITY_H"] = $rows->PO_QUANTITY_H;
			$data_array[$i]["PO_BREAK_DOWN_ID_H"] = $rows->PO_BREAK_DOWN_ID_H;
			$data_array[$i]["PO_NUMBER_H"] = $rows->PO_NUMBER_H;
			$data_array[$i]["POWISE_QUANTITY_H"] = $rows->POWISE_QUANTITY_H;
			$data_array[$i]["PO_INITIAL_QTY_H"] = $rows->PO_INITIAL_QTY_H;
			$data_array[$i]["ORDER_TYPE"] = $rows->ORDER_TYPE;
			$data_array[$i]["PHD"] = $rows->PHD;

			$tmpArr = array();
			foreach (explode(',', $rows->PO_BREAK_DOWN_ID_H) as $pid) {
				foreach (explode(',', $rows->COLOR_NUMBER_ID_H) as $Cid) {
					$tmpArr[$Cid] = $po_wise_colour_production_arr[$rows->PLAN_ID][$pid][$Cid] * 1;
				}
			}

			$data_array[$i]["POWISE_PRODUCTION_QUANTITY_H"] = implode(',', $tmpArr);
			//$data_array[$i]["POWISE_PRODUCTION_QUANTITY_H"] = $po_wise_colour_production_arr;
			//print_r($rows);die;
			$i++;
		}
		//print_r($changed_data_arr);die;



		if ($plan_lavel_data_arr[0]->BULLETIN_TYPE != 2) { //not color lavel
			foreach ($po_line_wise_plan_blance as $pid => $dateRows) {
				foreach ($dateRows as $plan_date => $lineRows) {
					$extra_pro_qty = array_sum($temp_production_arr[$pid][$plan_date]);
					$totalLine = count($lineRows);
					$seq = 1;
					foreach ($lineRows as $lid => $bal) {
						$serial = $po_line_wise_plan_serial[$pid][$lid];
						$plan_id = $po_line_wise_plan_id[$pid][$lid];

						$dateKey = date('d-m-Y', strtotime($plan_date));
						if ($data_array[$serial]["start_date"] == $dateKey) {
							if ($totalLine == 1 || $totalLine == $seq) {
								$adjustQty = $extra_pro_qty;
							} else if ($extra_pro_qty < $bal) {
								$adjustQty = $extra_pro_qty;
							} else if ($extra_pro_qty > $bal) {
								$adjustQty = $bal;
								$extra_pro_qty = $extra_pro_qty - $bal;
							}

							$data_array[$serial]["THIS_PRODUCTION"] = $data_array[$serial]["THIS_PRODUCTION"] + $adjustQty;
							$plan_pro_qty_rest = $data_array[$serial]["THIS_PRODUCTION"];
							$data_array[$serial]["IS_PRODUCTION"] = ($plan_pro_qty_rest <= 0) ? false : true;
							$data_array[$serial]["THIS_PRODUCTION"] = ($plan_pro_qty_rest > 0) ? $plan_pro_qty_rest : 0;
							if ($data_array[$serial]["THIS_PRODUCTION"] > 0) {
								$data_array[$serial]["BOTTOM_BORDER_COLOR"] = "Green";
							}
						}
						$seq++;
					}
				}
			}
		}
		//...............................................................end;

		$i = 0;
		$line_id_qty_arr[$i] = array('LINE_ID' => 0, 'SMV_MIN' => 0, 'QNTY' => 0);
		foreach ($line_plan_qty_total_arr as $line_id => $qty) {
			$line_id_qty_arr[$i] = array(
				'LINE_ID' => $line_id,
				'SMV_MIN' => number_format($line_plan_smv_min_arr[$line_id], 2),
				'QNTY' 	=> $qty,
			);
			$i++;
		}

		$i = 0;
		$date_qty_arr[$i] = array('DATE' => '10-01-1971', 'SMV_MIN' => 0, 'QNTY' => 0);
		foreach ($date_plan_qty_total_arr as $date => $qty) {
			$date_qty_arr[$i] = array(
				'DATE' 	=> $date,
				'SMV_MIN' => number_format(array_sum($date_plan_smv_min_arr[$date]), 2),
				'QNTY' 	=> array_sum($qty),
			);
			$i++;
		}

		$this->deleteRowByAttribute('GBL_TEMP_ENGINE', array('ENTRY_FORM' => 5000, "USER_ID" => $user_id));
		$this->deleteRowByAttribute('GBL_TEMP_ENGINE', array('ENTRY_FORM' => 5001, "USER_ID" => $user_id));

		$dataArr = array(
			'DTLS' => $data_array,
			'LINE_WISE_PLAN_QTY' => $line_id_qty_arr,
			'DATE_WISE_PLAN_QTY' => $date_qty_arr
		);

		//print_r($dataArr);die;

		//echo $timediff = microtime(true) - $starttime ;die;
		//print_r( $plan_data); die;

		if (count($plan_data) > 0) {
			return $dataArr;
		} else {
			return 0;
		}
	}




    function get_changed_po_info($po_data_arr = array())
	{

		$po_id_arr = array();
		$plan_data_arr = array();
		$po_color_id_arr = array();
		foreach ($po_data_arr as $plan_po_color => $plan_qty) {
			list($order_type,$plan_id, $po_id, $color_id) = explode('*', $plan_po_color);
			if ($po_id) {
				$po_id_arr[$order_type][$po_id] = $po_id;
				$po_color_id_arr[$order_type][$po_id][$color_id] = $color_id;
				$plan_data_arr[$order_type][$po_id][$color_id] += $plan_qty;
			}
		}
		//print_r($plan_data_arr); die;
		$po_color_sql = $this->db->query("SELECT PO_BREAK_DOWN_ID,COLOR_NUMBER_ID,sum(PLAN_CUT_QNTY) as PLAN_CUT_QNTY from WO_PO_COLOR_SIZE_BREAKDOWN where STATUS_ACTIVE=1 and IS_DELETED=0 " . where_con_using_array($po_id_arr[1], 0, 'PO_BREAK_DOWN_ID') . "  group by PO_BREAK_DOWN_ID,COLOR_NUMBER_ID")->result(); //".where_con_using_array($color_id_arr,0,'COLOR_NUMBER_ID')."

		$subcon_po_color_sql = $this->db->query("SELECT ORDER_ID as PO_BREAK_DOWN_ID,COLOR_ID as COLOR_NUMBER_ID,sum(PLAN_CUT) as PLAN_CUT_QNTY FROM SUBCON_ORD_BREAKDOWN WHERE STATUS_ACTIVE=1 and IS_DELETED=0 ". where_con_using_array($po_id_arr[2], 0, 'ORDER_ID') . "  group by ORDER_ID,COLOR_ID")->result();
		// var_dump($plan_data_arr);die;
		//print_r($po_color_sql);die;


		foreach ($po_color_sql as $rows) {

			unset($po_color_id_arr[1][$rows->PO_BREAK_DOWN_ID][$rows->COLOR_NUMBER_ID]);
			//print_r($plan_data_arr); die;
			if ($plan_data_arr[1][$rows->PO_BREAK_DOWN_ID][$rows->COLOR_NUMBER_ID] <= $rows->PLAN_CUT_QNTY) {
				unset($plan_data_arr[1][$rows->PO_BREAK_DOWN_ID][$rows->COLOR_NUMBER_ID]);
			}
		}

		foreach ($subcon_po_color_sql as $rows) {

			unset($po_color_id_arr[2][$rows->PO_BREAK_DOWN_ID][$rows->COLOR_NUMBER_ID]);
			//print_r($plan_data_arr); die;
			if ($plan_data_arr[2][$rows->PO_BREAK_DOWN_ID][$rows->COLOR_NUMBER_ID] <= $rows->PLAN_CUT_QNTY) {
				unset($plan_data_arr[2][$rows->PO_BREAK_DOWN_ID][$rows->COLOR_NUMBER_ID]);
			}
		}

		//print_r($plan_data_arr);die;
		$data_arr = [
			'VALUE_CHANGED' => $plan_data_arr,
			'COLOR_CHANGED' => $po_color_id_arr,
		];

		//return array('VALUE_CHANGED' => $plan_data_arr, 'COLOR_CHANGED' => $po_color_id_arr,);
		return $data_arr;
	}



}