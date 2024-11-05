<?php

class Supply_chain_details extends CI_Model
{



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

	function supply_chain_details($po_breakdown_ids, $job_no)
	{
		
		$msg = "";
		$query_lib_color = "SELECT ID,COLOR_NAME FROM LIB_COLOR WHERE STATUS_ACTIVE = 1 and IS_DELETED =0";
		$table_lib_color = $this->db->query($query_lib_color)->result();
		$lib_color = array();
		foreach ($table_lib_color as $row) {
			$lib_color[$row->ID] = $row->COLOR_NAME;
		}

		$query_lib_body_part = "SELECT ID,BODY_PART_FULL_NAME FROM LIB_BODY_PART WHERE STATUS_ACTIVE = 1 and IS_DELETED =0";
		$table_body_part = $this->db->query($query_lib_body_part)->result();
		$lib_body_part = array();
		foreach ($table_body_part as $row) {
			$lib_body_part[$row->ID] = $row->BODY_PART_FULL_NAME;
		}

		$unit_of_measurement = array(1 => "Pcs", 2 => "Dzn", 3 => "Grs", 4 => "GG", 10 => "Mg", 11 => "Gm", 12 => "Kg", 13 => "Quintal", 14 => "Ton", 15 => "Lbs", 20 => "Km", 21 => "Hm", 22 => "Dm", 23 => "Mtr", 24 => "Dcm", 25 => "CM", 26 => "MM", 27 => "Yds", 28 => "Feet", 29 => "Inch", 30 => "CFT", 31 => "SFT", 40 => "Ltr", 41 => "ML", 50 => "Roll", 51 => "Coil", 52 => "Cone", 53 => "Bag", 54 => "Box", 55 => "Drum", 56 => "Bottle", 57 => "Pack", 58 => "Set", 59 => "Can", 60 => "Each", 61 => "Gallon", 62 => "Lachi", 63 => "Pair", 64 => "Lot", 65 => "Packet", 66 => "Pot", 67 => "Book", 68 => "Culind", 69 => "Ream", 70 => "Cft", 71 => "Syp", 72 => "K.V", 73 => "CU-M3", 74 => "Bundle", 75 => "Strip", 76 => "SQM", 77 => "Ounce", 78 => "Cylinder", 79 => "Course", 80 => "Sheet", 81 => "RFT", 82 => "Square Inch", 83 => "Carton", 84 => "Thane", 85 => "Gross Yds", 86 => "Jar", 87 => "Reel", 88 => "CBM", 89 => "Tub", 90 => "KVA", 91 => "KW", 92 => "Pallet", 93 => "Case", 94 => "Job", 95 => "KIT", 96 => "Watt-Peak");

		//TNA start
		$query_tna_report = "SELECT a.PO_NUMBER_ID,b.PO_NUMBER,a.TASK_START_DATE,a.TASK_FINISH_DATE,a.TASK_NUMBER FROM TNA_PROCESS_MST a, WO_PO_BREAK_DOWN b where a.PO_NUMBER_ID = b.ID and a.PO_NUMBER_ID in( $po_breakdown_ids ) and a.TASK_NUMBER in (31,305,206,310,301,306,70,71,309,300,73,179,180,184,84,186,187,86,190,191,80,184,185) AND a.STATUS_ACTIVE = 1 and a.IS_DELETED =0 AND b.STATUS_ACTIVE = 1 and b.IS_DELETED =0";
		$table_tna_report = $this->db->query($query_tna_report)->result();
		//print_r($query_tna_report);die;
		
		if (empty($table_tna_report)) {
			$msg .= "**TNA has no data**";
		} else {
			$tna_arr = array();

			$po_arr = array();
			foreach ($table_tna_report as $row) {
				$po_arr[$row->PO_NUMBER_ID] = $row->PO_NUMBER_ID;
			}
			$i = 0;
			
			//foreach ($po_arr as $row1) {

				foreach ($table_tna_report as $row2) {

					$FABRIC_WO_DATE_start = "";
					$FABRIC_WO_DATE_end = "";
					$f_fabric_inhouse_start = "";
					$f_fabric_inhouse_end = "";
					$sewing_acc_inhouse_start = "";
					$sewing_acc_inhouse_end = "";
					$fin_trims_inhouse_start = "";
					$fin_trims_inhouse_end = "";
					$cutting_tna_start = "";
					$cutting_tna_end = "";
					$sewing_tna_start = "";
					$sewing_tna_end = "";
					$pp_meeting_start = "";
					$pp_meeting_end = "";

					// if ($row1 == $row2->PO_NUMBER_ID) {

						$tna_arr[$row2->PO_NUMBER_ID]["PO_BREAK_DOWN_ID"] = $row2->PO_NUMBER_ID;
						$tna_arr[$row2->PO_NUMBER_ID]["PO_NUMBER"] = $row2->PO_NUMBER;


						if ($row2->TASK_NUMBER == 31 || $row2->TASK_NUMBER == 305 || $row2->TASK_NUMBER == 276) {

							$FABRIC_WO_DATE_start = $row2->TASK_START_DATE;

							if (empty($FABRIC_WO_DATE_start) || strtotime($FABRIC_WO_DATE_start) > strtotime($row2->TASK_START_DATE)) {
								$FABRIC_WO_DATE_start = $row2->TASK_START_DATE;
							}

							if (empty($FABRIC_WO_DATE_end) || strtotime($FABRIC_WO_DATE_end) < strtotime($row2->TASK_FINISH_DATE)) {
								$FABRIC_WO_DATE_end = $row2->TASK_FINISH_DATE;
							}

							$tna_arr[$row2->PO_NUMBER_ID]["FABRIC_WO_DATE_START"] = $FABRIC_WO_DATE_start;
							$tna_arr[$row2->PO_NUMBER_ID]["FABRIC_WO_DATE_END"] = $FABRIC_WO_DATE_end;
						}

						if ($row2->TASK_NUMBER == 73 || $row2->TASK_NUMBER == 179 || $row2->TASK_NUMBER == 180) {



							if (empty($f_fabric_inhouse_start) || strtotime($f_fabric_inhouse_start) > strtotime($row2->TASK_START_DATE)) {
								$f_fabric_inhouse_start = $row2->TASK_START_DATE;
							}

							if (empty($f_fabric_inhouse_end) || strtotime($f_fabric_inhouse_end) < strtotime($row2->TASK_FINISH_DATE)) {
								$f_fabric_inhouse_end = $row2->TASK_FINISH_DATE;
							}

							$tna_arr[$row2->PO_NUMBER_ID]["F_FABRIC_INHOUSE_START"] = $f_fabric_inhouse_start;
							$tna_arr[$row2->PO_NUMBER_ID]["F_FABRIC_INHOUSE_END"] = $f_fabric_inhouse_end;
						}

						if ($row2->TASK_NUMBER == 310 || $row2->TASK_NUMBER == 301 || $row2->TASK_NUMBER == 70) {



							if (empty($sewing_acc_inhouse_start) || strtotime($sewing_acc_inhouse_start) > strtotime($row2->TASK_START_DATE)) {
								$sewing_acc_inhouse_start = $row2->TASK_START_DATE;
							}

							if (empty($sewing_acc_inhouse_end) || strtotime($sewing_acc_inhouse_end) < strtotime($row2->TASK_FINISH_DATE)) {
								$sewing_acc_inhouse_end = $row2->TASK_FINISH_DATE;
							}

							$tna_arr[$row2->PO_NUMBER_ID]["SEWING_ACC_INHOUSE_START"] = $sewing_acc_inhouse_start;
							$tna_arr[$row2->PO_NUMBER_ID]["SEWING_ACC_INHOUSE_END"] = $sewing_acc_inhouse_end;
						}

						if ($row2->TASK_NUMBER == 309 || $row2->TASK_NUMBER == 300 || $row2->TASK_NUMBER == 71) {



							if (empty($fin_trims_inhouse_start) || strtotime($fin_trims_inhouse_start) > strtotime($row2->TASK_START_DATE)) {
								$fin_trims_inhouse_start = $row2->TASK_START_DATE;
							}

							if (empty($fin_trims_inhouse_end) || strtotime($fin_trims_inhouse_end) < strtotime($row2->TASK_FINISH_DATE)) {
								$fin_trims_inhouse_end = $row2->TASK_FINISH_DATE;
							}

							$tna_arr[$row2->PO_NUMBER_ID]["FIN_TRIMS_INHOUSE_START"] = $fin_trims_inhouse_start;
							$tna_arr[$row2->PO_NUMBER_ID]["FIN_TRIMS_INHOUSE_END"] = $fin_trims_inhouse_end;
						}

						if ($row2->TASK_NUMBER == 84 || $row2->TASK_NUMBER == 186 || $row2->TASK_NUMBER == 187) {



							if (empty($cutting_tna_start) || strtotime($cutting_tna_start) > strtotime($row2->TASK_START_DATE)) {
								$cutting_tna_start = $row2->TASK_START_DATE;
							}

							if (empty($cutting_tna_end) || strtotime($cutting_tna_end) < strtotime($row2->TASK_FINISH_DATE)) {
								$cutting_tna_end = $row2->TASK_FINISH_DATE;
							}

							$tna_arr[$row2->PO_NUMBER_ID]["CUTTING_TNA_START"] = $cutting_tna_start;
							$tna_arr[$row2->PO_NUMBER_ID]["CUTTING_TNA_END"] = $cutting_tna_end;
						}

						if ($row2->TASK_NUMBER == 86 || $row2->TASK_NUMBER == 190 || $row2->TASK_NUMBER == 191) {



							if (empty($sewing_tna_start) || strtotime($sewing_tna_start) > strtotime($row2->TASK_START_DATE)) {
								$sewing_tna_start = $row2->TASK_START_DATE;
							}

							if (empty($sewing_tna_end) || strtotime($sewing_tna_end) < strtotime($row2->TASK_FINISH_DATE)) {
								$sewing_tna_end = $row2->TASK_FINISH_DATE;
							}

							$tna_arr[$row2->PO_NUMBER_ID]["SEWING_TNA_START"] = $sewing_tna_start;
							$tna_arr[$row2->PO_NUMBER_ID]["SEWING_TNA_END"] = $sewing_tna_end;
						}

						if ($row2->TASK_NUMBER == 80 || $row2->TASK_NUMBER == 184 || $row2->TASK_NUMBER == 185) {



							if (empty($pp_meeting_start) || strtotime($pp_meeting_start) > strtotime($row2->TASK_START_DATE)) {
								$pp_meeting_start = $row2->TASK_START_DATE;
							}

							if (empty($pp_meeting_end) || strtotime($pp_meeting_end) < strtotime($row2->TASK_FINISH_DATE)) {
								$pp_meeting_end = $row2->TASK_FINISH_DATE;
							}

							$tna_arr[$row2->PO_NUMBER_ID]["PP_MEETING_START"] = $pp_meeting_start;
							$tna_arr[$row2->PO_NUMBER_ID]["PP_MEETING_END"] = $pp_meeting_end;
						}
					// }
				}
				//$i++;
			//}
			$tna_arr_2 =  array();
			foreach($tna_arr as $row){
				$tna_arr_2[] = $row;
			}
			//print_r($tna_arr);die;
			
		}
		

		//TNA End

		//Start Required Qnty

		if ($job_no != "") {
			$jobCond = "and a.job_no='$job_no'";
			$jobCondS = "and job_no='$job_no'";
			$po_con = " and b.ID in( $po_breakdown_ids)";
			$po_cons = " and b.PO_BREAK_DOWN_ID in ($po_breakdown_ids)";

		} else {
			$jobCond = "";
			$jobCondS = "";
			$po_con = "";
		}

		$sqlpo = "SELECT a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $jobCond $po_con";
		//echo $sqlpo; die; 
		//and a.job_no='$job_no'
		$sqlpoRes = sql_select($sqlpo);
		//print_r($sqlpoRes); die;
		$po_arr = array();
		$poCountryArr = array();
		$reqQtyAmtArr = array();
		$costingPerArr = array();
		$jobid = "";

		foreach ($sqlpoRes as $row) {

			//print_r($row->JOB_ID);die;
			$costingPerQty = 0;
			if ($row->COSTING_PER == 1) $costingPerQty = 12;
			elseif ($row->COSTING_PER == 2) $costingPerQty = 1;
			elseif ($row->COSTING_PER == 3) $costingPerQty = 24;
			elseif ($row->COSTING_PER == 4) $costingPerQty = 36;
			elseif ($row->COSTING_PER == 5) $costingPerQty = 48;
			else $costingPerQty = 0;

			$costingPerArr[$row->JOB_ID] = $costingPerQty;

			$po_arr[$row->JOB_ID][$row->ID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID]['poqty'] += $row->ORDER_QUANTITY;
			$po_arr[$row->JOB_ID][$row->ID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID]['planqty'] += $row->PLAN_CUT_QNTY;

			$po_arr[$row->JOB_ID][$row->ID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID]['county_id'] .= $row->COUNTRY_ID . ',';

			$poCountryArr[$row->JOB_ID][$row->ID][$row->ITEM_NUMBER_ID][$row->COUNTRY_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID]['poqty'] += $row->ORDER_QUANTITY;
			$poCountryArr[$row->JOB_ID][$row->ID][$row->ITEM_NUMBER_ID][$row->COUNTRY_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID]['planqty'] += $row->PLAN_CUT_QNTY;

			//$reqQtyAmtArr[$row->ID]['poqty'] += $row->ORDER_QUANTITY;
			//$reqQtyAmtArr[$row->ID]['planqty'] += $row->PLAN_CUT_QNTY;

			if ($jobid == "")
				$jobid = $row->JOB_ID;
			else
				$jobid .= ',' . $row->JOB_ID;
		}
		unset($sqlpoRes);
		$ujobid = array_unique(explode(",", $jobid));
		$cjobid = count($ujobid);
		$jobIds = implode(",", $ujobid);
		$jobidCond = '';
		$jobidCondition = '';

		if ($cjobid > 1000) {
			$jobidCond = " and (";
			$jobidCondition = " and (";
			$jobIdsArr = array_chunk(explode(",", $jobIds), 999);
			foreach ($jobIdsArr as $ids) {
				$ids = implode(",", $ids);
				$jobidCond .= " a.job_id in($ids) or";
				$jobidCondition .= " job_id in($ids) or";
			}
			$jobidCond = chop($jobidCond, 'or ');
			$jobidCond .= ")";

			$jobidCondition = chop($jobidCondition, 'or ');
			$jobidCondition .= ")";
		} else {
			if ($jobIds == "") {
				$jobidCond = "";
			} else {
				$jobidCond = " and a.job_id in($jobIds)";
			}
			if ($jobIds == "") {
				$jobidCondition = "";
			} else {
				$jobidCondition = " and job_id in($jobIds)";
			}
		}

		//Item Ratio Details
		$gmtsitemRatioSql = "SELECT job_id AS JOB_ID, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 $jobCondS $jobidCondition";
		$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
		$jobItemRatioArr = array();
		foreach ($gmtsitemRatioSqlRes as $row) {
			$jobItemRatioArr[$row->JOB_ID][$row->GMTS_ITEM_ID] = $row->SET_ITEM_RATIO;
		}
		unset($gmtsitemRatioSqlRes);

		//Contrast Details
		$sqlContrast = "SELECT a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobCond $jobidCond";

		$sqlContrastRes = sql_select($sqlContrast);
		$sqlContrastArr = array();
		foreach ($sqlContrastRes as $row) {
			$sqlContrastArr[$row->JOB_ID][$row->PRECOSTID][$row->COLOR_NUMBER_ID] = $row->CONTRAST_COLOR_ID;
		}
		unset($sqlContrastRes);

		//Stripe Details
		$sqlStripe = "SELECT a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobCond $jobidCond";

		$sqlStripeRes = sql_select($sqlStripe);
		$sqlStripeArr = array();
		foreach ($sqlStripeRes as $row) {
			$sqlStripeArr[$row->JOB_ID][$row->PRECOSTID][$row->COLOR_NUMBER_ID]['strip'][$row->STRIPE_COLOR] = $row->STRIPE_COLOR;
			$sqlStripeArr[$row->JOB_ID][$row->PRECOSTID][$row->COLOR_NUMBER_ID]['fabreq'][$row->STRIPE_COLOR] = $row->FABREQ;
		}


		//FABRIC PART:
		//====================================
		$sqlfab = "SELECT a.BODY_PART_ID,b.PO_BREAK_DOWN_ID,a.FAB_NATURE_ID,a.LIB_YARN_COUNT_DETER_ID,a.BUDGET_ON,a.job_id AS JOB_ID, a.id AS ID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE, a.COMPOSITION
		from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
		where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobCond $jobidCond $po_cons";
		//echo $sqlfab; die;
		$sqlfabRes = sql_select($sqlfab);
		$fabIdWiseGmtsDataArr = array();
		//print_r($sqlfab);die;
		foreach ($sqlfabRes as $row) {
			$poQty = $planQty = $costingPer = $itemRatio = $finReq = $greyReq = $finAmt = $greyAmt = 0;

			$fabIdWiseGmtsDataArr[$row->ID]['item'] = $row->ITEM_NUMBER_ID;
			$fabIdWiseGmtsDataArr[$row->ID]['fnature'] = $row->FAB_NATURE_ID;
			$fabIdWiseGmtsDataArr[$row->ID]['sensitive'] = $row->COLOR_SIZE_SENSITIVE;
			$fabIdWiseGmtsDataArr[$row->ID]['color_type'] = $row->COLOR_TYPE_ID;
			$fabIdWiseGmtsDataArr[$row->ID]['uom'] = $row->UOM;

			$poQty = $po_arr[$row->JOB_ID][$row->POID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID]['poqty'];
			$planQty = $po_arr[$row->JOB_ID][$row->POID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID]['planqty'];
			$costingPer = $costingPerArr[$row->JOB_ID];
			$itemRatio = $jobItemRatioArr[$row->JOB_ID][$row->ITEM_NUMBER_ID];

			if ($row->BUDGET_ON == 1) {
				$finReq = ($poQty / $itemRatio) * ($row->CONS / $costingPer);
			} else {
				$finReq = ($planQty / $itemRatio) * ($row->CONS / $costingPer);
			}


			//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
			if ($itemRatio != "") {

				$reqQtyAmtArr[$row->POID][$row->BODY_PART_ID][$row->FAB_NATURE_ID][$row->LIB_YARN_COUNT_DETER_ID][$row->COLOR_NUMBER_ID]['prodfin_qty'] += $finReq;
			}
		}
		//print_r($reqQtyAmtArr);die;
		if (empty($reqQtyAmtArr)) {
			$msg .= "**No Req Qnty Found**";
		}
		//print_r($reqQtyAmtArr);die;
		//End Required Qnty
		
		//booking start


		//$query_booking = "SELECT c.BODY_PART_ID,c.LIB_YARN_COUNT_DETER_ID,a.ID,b.PO_BREAK_DOWN_ID, a.ITEM_CATEGORY, b.GMTS_COLOR_ID,b.CONSTRUCTION,b.COPMPOSITION,b.DESCRIPTION,b.UOM, b.FIN_FAB_QNTY AS QUANTITY, a.FABRIC_SOURCE, b.COLOR_SIZE_TABLE_ID, b.FABRIC_COLOR_ID, a.DELIVERY_DATE, b.COLOR_TYPE,b.WO_QNTY,b.TRIM_GROUP FROM WO_BOOKING_MST a, WO_BOOKING_DTLS b,WO_PRE_COST_FABRIC_COST_DTLS c WHERE a.ID = b.BOOKING_MST_ID and b.PRE_COST_FABRIC_COST_DTLS_ID=c.ID AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 and b.BOOKING_TYPE = 1 and b.PO_BREAK_DOWN_ID in( $po_breakdown_ids)";
		$query_booking = "SELECT c.LIB_YARN_COUNT_DETER_ID as FABRIC_DESCRIPTION_ID,c.BODY_PART_ID as BODY_PART_ID,a.ID as BOOKING_ID,b.PO_BREAK_DOWN_ID, a.ITEM_CATEGORY, b.GMTS_COLOR_ID as COLOR_ID,b.CONSTRUCTION,c.UOM,b.COPMPOSITION, b.FIN_FAB_QNTY AS QUANTITY,a.DELIVERY_DATE FROM WO_BOOKING_MST a, WO_BOOKING_DTLS b,WO_PRE_COST_FABRIC_COST_DTLS c WHERE a.ID = b.BOOKING_MST_ID and b.PRE_COST_FABRIC_COST_DTLS_ID=c.ID AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 and b.BOOKING_TYPE = 1 and b.PO_BREAK_DOWN_ID in( $po_breakdown_ids)";
		// print_r($query_booking);
		// die; 

		$table_booking = $this->db->query($query_booking)->result();
		// print_r($query_booking);
		// die;
		if (empty($table_booking)) {
			$msg .= "**No Booking Data Found**";
		}
		$lib_booking_quantity = array();
		$lib_booking_delivery_date = array();
		foreach ($table_booking as $row) {

			$lib_booking_quantity[$row->BODY_PART_ID][$row->ITEM_CATEGORY][$row->FABRIC_DESCRIPTION_ID][$row->COLOR_ID] += $row->QUANTITY;

			if ($lib_booking_delivery_date[$row->BODY_PART_ID][$row->ITEM_CATEGORY][$row->FABRIC_DESCRIPTION_ID][$row->COLOR_ID] == null || strtotime($lib_booking_delivery_date[$row->BODY_PART_ID][$row->ITEM_CATEGORY][$row->FABRIC_DESCRIPTION_ID][$row->COLOR_ID]) < strtotime($row->DELIVERY_DATE)) {
				$lib_booking_delivery_date[$row->BODY_PART_ID][$row->ITEM_CATEGORY][$row->FABRIC_DESCRIPTION_ID][$row->COLOR_ID] = $row->DELIVERY_DATE;
			}
		}
		//print_r($query_booking);die;
		//Booking End
		//Finish fab Production check
		//$query_production_quantity = "SELECT a.FABRIC_DESCRIPTION_ID,f.ITEM_CATEGORY,a.BOOKING_ID,a.BODY_PART_ID,d.PO_BREAKDOWN_ID,c.ITEM_CATEGORY,a.COLOR_ID,a.RECEIVE_QNTY,c.RECEIVE_BASIS,c.TRANSACTION_TYPE,e.ITEM_DESCRIPTION,e.PRODUCT_NAME_DETAILS,a.UOM,f.ID as BOOKING_ID  FROM PRO_FINISH_FABRIC_RCV_DTLS a, PRO_BATCH_CREATE_MST b, INV_TRANSACTION c,ORDER_WISE_PRO_DETAILS d,PRODUCT_DETAILS_MASTER e, WO_BOOKING_MST f WHERE f.ID = a.BOOKING_ID and a.BATCH_ID = b.ID AND a.TRANS_ID = c.ID AND d.TRANS_ID = c.ID AND e.ID = d.PROD_ID AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND c.IS_DELETED = 0 AND c.STATUS_ACTIVE = 1 AND d.IS_DELETED = 0 AND d.STATUS_ACTIVE = 1 AND e.IS_DELETED = 0 AND e.STATUS_ACTIVE = 1 and d.PO_BREAKDOWN_ID in ($po_breakdown_ids)";
		$query_production_quantity = "SELECT a.fabric_description_id, d.id as BOOKING_ID, a.BODY_PART_ID, b.PO_BREAKDOWN_ID, e.item_category_id as  ITEM_CATEGORY, c.COLOR_ID, b.quantity as RECEIVE_QNTY, e.item_description,e.product_name_details, a.uom,d.DELIVERY_DATE from pro_finish_fabric_rcv_dtls a, order_wise_pro_details b, pro_batch_create_mst c, wo_booking_mst d, product_details_master e where a.trans_id=b.trans_id and a.batch_id=c.id and c.booking_no=d.booking_no and a.prod_id=e.id and b.entry_form in (7,37,68,17) and a.status_active=1 and b.status_active=1 and b.po_breakdown_id in ($po_breakdown_ids)";
		//  print_r($query_production_quantity);
		//  die;
		$table_production_quantity = $this->db->query($query_production_quantity)->result();
		$lib_production_quantity = array();
		foreach ($table_production_quantity as $row) {
			$lib_production_quantity[$row->BODY_PART_ID][$row->PO_BREAKDOWN_ID][$row->ITEM_CATEGORY][$row->COLOR_ID] += $row->RECEIVE_QNTY;
		}

		if (empty($table_production_quantity)) {
			$msg .= "**No Fabric Recv Found**";
		}
		//print_r($query_production_quantity);die;
		//END Finish fab Production check 
		
		//trims inv check
		$query_trims_entry_dtls = "SELECT c.PO_BREAKDOWN_ID, b.ITEM_CATEGORY, a.ITEM_GROUP_ID, a.RECEIVE_QNTY FROM INV_TRIMS_ENTRY_DTLS a, INV_TRANSACTION b, ORDER_WISE_PRO_DETAILS c WHERE a.TRANS_ID = b.ID AND b.ID = c.TRANS_ID AND b.ITEM_CATEGORY = 4 AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND c.IS_DELETED = 0 AND c.STATUS_ACTIVE = 1 and c.PO_BREAKDOWN_ID in ($po_breakdown_ids)";
		$table_trims_entry_dtls = $this->db->query($query_trims_entry_dtls)->result();
		$lib_trims_entry_dtls = array();
		//print_r($query_trims_entry_dtls);die;
		if (empty($table_trims_entry_dtls)) {
			$msg .= "**No data found in Trims entry table**";
		}

		foreach ($table_trims_entry_dtls as $row) {
			$lib_trims_entry_dtls[$row->PO_BREAKDOWN_ID][$row->ITEM_CATEGORY][$row->ITEM_GROUP_ID] += $row->RECEIVE_QNTY;
		}
		// print_r($table_trims_entry_dtls);
		// die;
		//end trims inv check
		$mat_inv = array();
		$req_qnty = 0;
		foreach ($reqQtyAmtArr as $row) {
			$req_qnty += $row['planqty'];
		}
		$fab_nature = [
			2 => "Knit Finish Fabrics",
			3 => "Woven Fabrics",
		];
		//print_r($table_booking);die;

		// foreach ($table_booking as $row) {
		// 	//print_r($row);die;
		// 	$inhouse_percent = ($lib_production_quantity[$row->BODY_PART_ID][$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->COLOR_ID][$row->BOOKING_ID] * 100) / $lib_booking_quantity[$row->BODY_PART_ID][$row->ITEM_CATEGORY][$row->FABRIC_DESCRIPTION_ID][$row->COLOR_ID];


		// 	$mat_inv[$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->COLOR_ID][] = [
		// 		"PO_BREAK_DOWN_ID" => $row->PO_BREAK_DOWN_ID,
		// 		"BODY_PART" => $lib_body_part[$row->BODY_PART_ID],
		// 		//"construction" => $row->CONSTRUCTION,
		// 		"COPMPOSITION" => $fab_nature[$row->ITEM_CATEGORY],
		// 		"DESCRIPTION" => $row->CONSTRUCTION.",".$row->COPMPOSITION,
		// 		"GMTS_COLOR_ID" => $lib_color[$row->COLOR_ID],
		// 		"UOM" => $unit_of_measurement[$row->UOM],
		// 		"REQ_QNTY" => number_format($reqQtyAmtArr[$row->BODY_PART_ID][$row->ITEM_CATEGORY][$row->FABRIC_DESCRIPTION_ID][$row->COLOR_ID]["prodfin_qty"],2),
		// 		"BOOKING_QNTY" => number_format($lib_booking_quantity[$row->BODY_PART_ID][$row->ITEM_CATEGORY][$row->FABRIC_DESCRIPTION_ID][$row->COLOR_ID],2),
		// 		"INV_QNTY" => number_format($lib_production_quantity[$row->BODY_PART_ID][$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->COLOR_ID][$row->BOOKING_ID],2),			
		// 		"BALANCE_QNTY" => number_format($lib_booking_quantity[$row->BODY_PART_ID][$row->ITEM_CATEGORY][$row->FABRIC_DESCRIPTION_ID][$row->COLOR_ID] - $lib_production_quantity[$row->BODY_PART_ID][$row->PO_BREAK_DOWN_ID][$row->ITEM_CATEGORY][$row->COLOR_ID][$row->BOOKING_ID],2),
		// 		"INHOUSE_PERCENT" => ($inhouse_percent) ? round($inhouse_percent) : 0,
		// 		"DELIVERY_DATE" => $lib_booking_delivery_date[$row->BODY_PART_ID][$row->ITEM_CATEGORY][$row->FABRIC_DESCRIPTION_ID][$row->COLOR_ID],
		// 		//"DELIVERY_DATE" => $row->DELIVERY_DATE,
		// 	];
		// }
		//print_r($sqlfabRes);die;
		foreach ($sqlfabRes as $row) {
			//print_r($row);die;
			if($lib_booking_quantity[$row->BODY_PART_ID][$row->FAB_NATURE_ID][$row->LIB_YARN_COUNT_DETER_ID][$row->COLOR_NUMBER_ID]){

				$inhouse_percent = ($lib_production_quantity[$row->BODY_PART_ID][$row->POID][$row->FAB_NATURE_ID][$row->COLOR_NUMBER_ID] * 100) / $lib_booking_quantity[$row->BODY_PART_ID][$row->FAB_NATURE_ID][$row->LIB_YARN_COUNT_DETER_ID][$row->COLOR_NUMBER_ID];
			}else{
				$inhouse_percent = 0;
			}

			//print_r($inhouse_percent);die;
			//$reqQtyAmtArr = ;


			$mat_inv[$row->BODY_PART_ID][$row->FAB_NATURE_ID][$row->CONSTRUCTION][$row->COLOR_NUMBER_ID] = [
				"PO_BREAK_DOWN_ID" => $row->POID,
				"BODY_PART" => $lib_body_part[$row->BODY_PART_ID],
				//"construction" => $row->CONSTRUCTION,
				"COPMPOSITION" => $fab_nature[$row->FAB_NATURE_ID],
				"DESCRIPTION" => $row->CONSTRUCTION.",".$row->COMPOSITION,
				"GMTS_COLOR_ID" => $lib_color[$row->COLOR_NUMBER_ID],
				"UOM" => $unit_of_measurement[$row->UOM],
				"REQ_QNTY" => number_format($reqQtyAmtArr[$row->POID][$row->BODY_PART_ID][$row->FAB_NATURE_ID][$row->LIB_YARN_COUNT_DETER_ID][$row->COLOR_NUMBER_ID]["prodfin_qty"],2)." ",
				"BOOKING_QNTY" => number_format($lib_booking_quantity[$row->BODY_PART_ID][$row->FAB_NATURE_ID][$row->LIB_YARN_COUNT_DETER_ID][$row->COLOR_NUMBER_ID],2)." ",
				"INV_QNTY" => number_format($lib_production_quantity[$row->BODY_PART_ID][$row->POID][$row->FAB_NATURE_ID][$row->COLOR_NUMBER_ID],2)." ",			
				"BALANCE_QNTY" => round($lib_booking_quantity[$row->BODY_PART_ID][$row->FAB_NATURE_ID][$row->LIB_YARN_COUNT_DETER_ID][$row->COLOR_NUMBER_ID] - $lib_production_quantity[$row->BODY_PART_ID][$row->POID][$row->FAB_NATURE_ID][$row->COLOR_NUMBER_ID]),
				"INHOUSE_PERCENT" =>($inhouse_percent > 100) ? "100+" :  round($inhouse_percent)." ",
				"DELIVERY_DATE" => ($lib_booking_delivery_date[$row->BODY_PART_ID][$row->FAB_NATURE_ID][$row->LIB_YARN_COUNT_DETER_ID][$row->COLOR_NUMBER_ID]) ? $lib_booking_delivery_date[$row->BODY_PART_ID][$row->FAB_NATURE_ID][$row->LIB_YARN_COUNT_DETER_ID][$row->COLOR_NUMBER_ID] : " ",
				//"DELIVERY_DATE" => $row->DELIVERY_DATE,
			];
		}
		
		
		//print_r($mat_inv);die;
		$mat_inv_2 = array();
		foreach ($mat_inv as $row) {
			foreach ($row as $row2) {
				foreach ($row2 as $row3) {
					foreach ($row3 as $row4) {
						//foreach($row4 as $row5){
							$mat_inv_2[] = $row4;
						//}
						
					}
				}
			}
		}



		//Start Trims 
		//start trims req qntity calculation 
		$sqlTrim="SELECT a.job_id AS JOB_ID, a.id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMOUNT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS CONS, b.tot_cons AS TOT_CONS, b.rate AS RATE, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
		from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b
		where 1=1 and a.id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobCond $jobidCond";
		//echo $sqlTrim; die;
		$sqlTrimRes = sql_select($sqlTrim);

		foreach($sqlTrimRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
			
			$costingPer=$costingPerArr[$row->JOB_ID];
			$itemRatio=$jobItemRatioArr[$row->JOB_ID][$row->ITEM_NUMBER_ID];
			
			$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row->JOB_ID][$row->POID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID]['county_id'])));
			//print_r($poCountryId);
			
			if($row->COUNTRY_ID_TRIMS=="" || $row->COUNTRY_ID_TRIMS==0)
			{
				$poQty=$po_arr[$row->JOB_ID][$row->POID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID]['poqty'];
				$planQty=$po_arr[$row->JOB_ID][$row->POID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID]['planqty'];
				
				$consQnty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
				$consTotQnty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
				
				$consAmt=$consQnty*$row->RATE;
				$consTotAmt=$consTotQnty*$row->RATE;
			}
			else
			{
				$countryIdArr=explode(",",$row->COUNTRY_ID_TRIMS);
				$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
				foreach($poCountryId as $countryId)
				{
					if(in_array($countryId, $countryIdArr))
					{
						$poQty=$poCountryArr[$row->JOB_ID][$row->POID][$row->ITEM_NUMBER_ID][$countryId][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID]['poqty'];
						$planQty=$poCountryArr[$row->JOB_ID][$row->POID][$row->ITEM_NUMBER_ID][$countryId][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID]['planqty'];
						$consQty=$consTotQty=0;
						
						$consQty=($poQty/$itemRatio)*($row->CONS/$costingPer);
						$consTotQty=($poQty/$itemRatio)*($row->TOT_CONS/$costingPer);
						
						$consQnty+=$consQty;
						$consTotQnty+=$consTotQty;
						//echo $poQty.'-'.$itemRatio.'-'.$row['CONS'].'-'.$costingPer.'<br>';
						
					}
				}
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			if($itemRatio!="")
			{
				//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimqty']+=$consQnty;
				$reqQtyAmtArr[$row->TRIM_GROUP]+=$consQnty;
				//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimtotqty']+=$consTotQnty;
				
				
			}
		}
		//print_r($reqQtyAmtArr);die;
		unset($sqlTrimRes); 
		//end req qntity calculation 


		$query_trim_book_qnty = "SELECT c.ITEM_NAME,c.ORDER_UOM,a.FABRIC_SOURCE,sum(b.WO_QNTY) as BOOKING_QNTY,c.TRIM_TYPE,b.TRIM_GROUP as TRIMS_ID,a.DELIVERY_DATE FROM WO_BOOKING_MST a, WO_BOOKING_DTLS b, LIB_ITEM_GROUP c WHERE a.ID = b.BOOKING_MST_ID and b.TRIM_GROUP = c.ID  and a.ITEM_CATEGORY =4 AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 and b.PO_BREAK_DOWN_ID in($po_breakdown_ids) GROUP BY a.ITEM_CATEGORY,a.FABRIC_SOURCE,b.TRIM_GROUP,c.ITEM_NAME,a.DELIVERY_DATE,c.ORDER_UOM,b.PO_BREAK_DOWN_ID,c.TRIM_TYPE";
		$table_trim_book_qnty = $this->db->query($query_trim_book_qnty)->result();
		$trims_book_qnty_arr = array();
		//print_r($query_trim_book_qnty);die;
		$trims_delivery_date = array();
		foreach ($table_trim_book_qnty as $row) {
			$trims_book_qnty_arr[$row->TRIMS_ID] += $row->BOOKING_QNTY;

			if(empty($trims_delivery_date[$row->TRIMS_ID])||$trims_delivery_date[$row->TRIMS_ID] < $row->DELIVERY_DATE){
				$trims_delivery_date[$row->TRIMS_ID] = $row->DELIVERY_DATE;
			}
			
		}
		if (empty($table_trim_book_qnty)) {
			$msg .= "**No Trims Booking Found**";
		}
		$query_req_qnty = "SELECT b.id as PO_BREAKDOWN_ID,SUM (c.plan_cut_qnty) as PLAN_CUT_QNTY  , d.TRIM_GROUP,f.ITEM_NAME,f.TRIM_TYPE,d.CONS_UOM from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e,LIB_ITEM_GROUP f where 1=1 and b.id in($po_breakdown_ids) and a.id=b.job_id and f.ID = d.TRIM_GROUP and b.id=c.po_break_down_id and a.id=d.job_id and d.id=e.wo_pre_cost_trim_cost_dtls_id and b.id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and e.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1) and c.is_deleted=0 and c.status_active in(1) and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 group by  b.id,d.trim_group,f.ITEM_NAME,f.TRIM_TYPE,d.CONS_UOM";
		$table_req_qnty = $this->db->query($query_req_qnty)->result();
		if (empty($table_req_qnty)) {
			$msg .= "**No req found for trims**";
		}
		$arr_req_qnty = array();
		foreach ($table_req_qnty as $row) {
			$arr_req_qnty[$row->TRIM_GROUP] += $row->PLAN_CUT_QNTY;
		}
		//print_r($table_req_qnty);die;
		$query_trims = "SELECT d.ITEM_NAME,a.ORDER_UOM,b.PO_BREAKDOWN_ID, sum(b.QUANTITY) as PROD_QNTRY,d.trim_type, e.PO_NUMBER,d.ID as TRIMS_ID FROM inv_transaction a,  order_wise_pro_details b, product_details_master c , lib_item_group d, WO_PO_BREAK_DOWN e where a.prod_id=c.id and b.trans_id=a.id and c.item_group_id=d.id and d.trim_type in (1,2) and b.entry_form in ( 24 ) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0 and b.po_breakdown_id in ($po_breakdown_ids) and b.po_breakdown_id = e.ID group by a.ORDER_UOM,b.po_breakdown_id,d.trim_type,d.TRIM_UOM,d.ITEM_NAME,e.PO_NUMBER,d.ID";
		//print_r($query_trims);die;
		$table_trims = $this->db->query($query_trims)->result();
		if (empty($table_trims)) {
			$msg .= "**No trims found in inv**";
		}
		//print_r($table_trims);die;
		$array_trims_type = [1 => "Sewing Trims", 2 => "Finishing Trims"];
		$sewing_trims = array();
		$finish_trims = array();
		$trims_quantity = array();

		foreach ($table_trims as $row) {
			$trims_quantity[$row->TRIM_TYPE][$row->TRIMS_ID] += $row->PROD_QNTRY;
		}
		
		

		foreach ($table_req_qnty as $row) {
			if($trims_book_qnty_arr[$row->TRIM_GROUP]){
				$in_house_percent =  ($trims_quantity[$row->TRIM_TYPE][$row->TRIM_GROUP]*100)/$trims_book_qnty_arr[$row->TRIM_GROUP];
			}else{
				$in_house_percent = 0;
			}
			//print_r($in_house_percent);die;
			if ($row->TRIM_TYPE == 1) {
				$sewing_trims[$row->TRIM_GROUP] = [
					"ITEM_NAME" => $row->ITEM_NAME,
					"ITEM_DESCRIPTION" => $array_trims_type[$row->TRIM_TYPE],
					"UOM" =>  $unit_of_measurement[$row->CONS_UOM],
					//"REQUIRED_QUANTITY" => number_format($arr_req_qnty[$row->TRIM_GROUP],2),
					"REQUIRED_QUANTITY" => number_format($reqQtyAmtArr[$row->TRIM_GROUP],2)." ",
					"BOOKING_QUANTITY" => number_format($trims_book_qnty_arr[$row->TRIM_GROUP],2)." ",
					"IN_HOUSE_QNTY" => number_format($trims_quantity[$row->TRIM_TYPE][$row->TRIM_GROUP],2)." ",
					"BALANCE_QNTY" => round($trims_book_qnty_arr[$row->TRIM_GROUP] - $trims_quantity[$row->TRIM_TYPE][$row->TRIM_GROUP]),
					"INHOUSE_PERCENT" => ($in_house_percent > 100) ? "100+" : round($in_house_percent)." ",
					"DELIVERY_DATE" => ($trims_delivery_date[$row->TRIM_GROUP])? $trims_delivery_date[$row->TRIM_GROUP] : " ",
				];
			} else if ($row->TRIM_TYPE == 2) {
				$finish_trims[$row->TRIM_GROUP] = [
					"ITEM_NAME" => $row->ITEM_NAME,
					"ITEM_DESCRIPTION" => $array_trims_type[$row->TRIM_TYPE],
					"UOM" =>  $unit_of_measurement[$row->CONS_UOM],
					//"REQUIRED_QUANTITY" => number_format($arr_req_qnty[$row->TRIM_GROUP],2),
					"REQUIRED_QUANTITY" => number_format($reqQtyAmtArr[$row->TRIM_GROUP],2)." ",
					"BOOKING_QUANTITY" => number_format($trims_book_qnty_arr[$row->TRIM_GROUP],2)." ",
					"IN_HOUSE_QNTY" => number_format($trims_quantity[$row->TRIM_TYPE][$row->TRIM_GROUP],2)." ",
					"BALANCE_QNTY" => round($trims_book_qnty_arr[$row->TRIM_GROUP] - $trims_quantity[$row->TRIM_TYPE][$row->TRIM_GROUP]),
					"INHOUSE_PERCENT" =>  ($in_house_percent > 100) ? "100+" : round($in_house_percent)." ",
					"DELIVERY_DATE" => ($trims_delivery_date[$row->TRIM_GROUP])?$trims_delivery_date[$row->TRIM_GROUP] : " ",
				];
			}
		}
		
		//print_r($sewing_trims);die;
		$sewing_trims_2 = array();
		foreach ($sewing_trims as $row) {
			$sewing_trims_2[] = $row;
		}
		$finish_trims_2 = array();
		foreach ($finish_trims as $row) {
			$finish_trims_2[] = $row;
		}
		//End Trims

		$result_set = [
			"TNA" => ($tna_arr_2) ? $tna_arr_2 : [],
			"FAB_INV" => ($mat_inv_2) ? $mat_inv_2 : [],
			"SEWING_TRIMS" => ($sewing_trims_2) ? $sewing_trims_2 : [],
			"FINISHING_TRIMS" => ($finish_trims_2) ? $finish_trims_2 : [],
			"MSG" => $msg,
		];
		//print_r($result_set);die;
		return $result_set;
	}
}
