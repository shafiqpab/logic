<?php
class Job_data_details_model extends CI_Model
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











	function Job_data_details($cbo_company_mst, $cbo_buyer_name = "0", $chk_job_wo_po = "0", $txt_date_from, $txt_date_to, $garments_nature = "", $txt_job_prifix = "", $cbo_year_selection, $cbo_string_search_type = "", $txt_order_search = "", $cbo_date_type, $plan_level = "0", $txt_style_ref = "", $ignore_tna = "0", $order_status = "0", $po_break_down_id = 0, $set_dtls_id = 0, $color_size_id = 0, $plan_full = 0, $smv_range = '', $item_name = '', $internal_ref = '', $department = 0, $sub_department = 0, $brand = 0, $season = 0, $season_year = 0, $ignore_full_prod = 0, $smv_type, $plan_status, $user_id, $order_type, $product_cate, $emb_filter)
	{
		
		$this->db->trans_begin();

		$search_types = str_replace("'", "", $cbo_string_search_type);
		$txt_style_ref = strtolower($txt_style_ref);
		$txt_order_search = strtolower($txt_order_search);
		$internal_ref = strtolower($internal_ref);

		if ($plan_status == 0) {
			//print_r($cbo_company_mst.'*'.$cbo_buyer_name.'*'.$chk_job_wo_po.'*'.$txt_job_prifix.'*'.$txt_order_search.'*'.$po_break_down_id.'*'.$smv_range.'*'.$internal_ref.'*'.$smv_type); die;
			if ($cbo_company_mst == 0 && $cbo_buyer_name  == 0 && $chk_job_wo_po  == 0 && $txt_job_prifix  == 0 && $txt_order_search  == 0 && $po_break_down_id == 0 && $smv_range == 0 && $internal_ref == 0 && $smv_type == 0) {
				$txt_date_from = date('Y-m-d', strtotime('-6 Month', time()));
				$txt_date_to = date('Y-m-d');
				$wo_date_range = "and b.PUB_SHIPMENT_DATE between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
				//print_r(1); die;
			}
		}
		//print_r($txt_date_to);die;
		$pro_cate_con = "";
		if ($product_cate) {
			$pro_cate_con = "and a.PRODUCT_CATEGORY= $product_cate";
		}
		//print_r($pro_cate_con);die;
		if (trim($smv_range)) {
			list($first_smv, $last_smv) = explode('-', $smv_range);
			$smv_cond_d = " and d.SMV_PCS>=$first_smv and d.SMV_PCS <=$last_smv";
			$smv_cond_c = " and c.SMV_PCS>=$first_smv and c.SMV_PCS <=$last_smv";
		} else {
			$smv_cond_d = "";
			$smv_cond_c = "";
		}

		$item_name_cond_d = "";
		$item_name_cond_c = "";
		$item_name = trim($item_name);
		if ($item_name) {
			$item_name_cond_d = " and d.GMTS_ITEM_ID=$item_name";
			$item_name_cond_c = " and c.GMTS_ITEM_ID=$item_name";
		}

		//echo $item_name_cond_c;die;

		if ($cbo_company_mst > 0) {
			$companyCon = " and company_name='$cbo_company_mst'";
		}

		$allow_capacity_allocation = return_field_value("capacity_allocation", "VARIABLE_SETTINGS_PRODUCTION", " variable_list=54 $companyCon", "capacity_allocation");
		//echo $allow_capacity_allocation;die;

		if ($allow_capacity_allocation == 1) {

			if (trim($po_break_down_id)) {
				$all_were_con = " and a.PO_NO in( $po_break_down_id )";
			}
			if ($txt_job_prifix) {
				$all_were_con .= " and a.job_no like '%" . $txt_job_prifix . "' ";
				//$all_were_con .= " and a.job_no like '%" . $txt_job_prifix . "'";
			}
			if ($cbo_company_mst > 0) {
				$companyCon = "and b.company_name=$cbo_company_mst";
			}

			$sql_allocationl = "SELECT b.company_name as FROM_COMPANY,b.location_name as FROM_LOCATION, a.company_id as TO_COMPANY, a.location_name as TO_LOCATION,  a.SMV, a.ALLOCATED_QTY, a.CUT_OFF_DATE,a.JOB_NO,a.PO_NO,a.ITEM FROM ppl_order_allocation_mst a,wo_po_details_master b,wo_po_details_mas_set_details d where a.job_no=b.job_no and d.job_no=a.job_no and d.GMTS_ITEM_ID=a.ITEM and d.JOB_ID=b.id and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1 $companyCon $all_were_con $smv_cond_d $item_name_cond_d";

			//echo $sql_allocationl;die;
			$sql_allocationlRes = $this->db->query($sql_allocationl)->result();
			//print_r($sql_allocationlRes);die;
			$allocationFromArr = array();
			$allocationToArr = array();
			foreach ($sql_allocationlRes as $row) {
				$allocationFromArr['PO'][$row->FROM_COMPANY][$row->JOB_NO][$row->PO_NO] += $row->ALLOCATED_QTY;
				$allocationToArr['PO'][$row->TO_COMPANY][$row->JOB_NO][$row->PO_NO] += $row->ALLOCATED_QTY;

				$allocationFromArr['JOB'][$row->FROM_COMPANY][$row->JOB_NO] += $row->ALLOCATED_QTY;
				$allocationToArr['JOB'][$row->TO_COMPANY][$row->JOB_NO] += $row->ALLOCATED_QTY;

				$allocationFromArr['ITEM'][$row->FROM_COMPANY][$row->JOB_NO][$row->PO_NO][$row->ITEM] += $row->ALLOCATED_QTY;
				$allocationToArr['ITEM'][$row->TO_COMPANY][$row->JOB_NO][$row->PO_NO][$row->ITEM] += $row->ALLOCATED_QTY;
			}
			unset($sql_allocationlRes);
		}

		//return $allocationFromArr;
		$data_array = array();
		$search_types = str_replace("'", "", $cbo_string_search_type);
		$txt_job_prifix = trim($txt_job_prifix);
		$txt_order_search = trim($txt_order_search);
		$txt_style_ref = trim($txt_style_ref);
		$internal_ref = trim($internal_ref);
		$is_confirmed_cond = "";
		if ($order_status) {
			$is_confirmed_cond .= " and b.is_confirmed='$order_status'";
		}

		$where_con = '';
		$where_con_d = '';
		if ($department) {
			$where_con .= " and a.PRODUCT_DEPT='$department'";
			$where_con_d .= " and d.PRODUCT_DEPT='$department'";
		}
		if ($sub_department) {
			$where_con .= " and a.PRO_SUB_DEP='$sub_department'";
			$where_con_d .= " and d.PRO_SUB_DEP='$sub_department'";
		}
		if ($season) {
			$where_con .= " and a.SEASON_BUYER_WISE='$season'";
			$where_con_d .= " and d.SEASON_BUYER_WISE='$season'";
		}

		if ($season_year) {
			$where_con .= " and a.SEASON_YEAR='$season_year'";
			$where_con_d .= " and d.SEASON_YEAR='$season_year'";
		}

		if ($brand) {
			$where_con .= " and a.BRAND_ID='$brand'";
			$where_con_d .= " and d.BRAND_ID='$brand'";
		}
		//gsd.........................................................................................................
		$gsd_were_con = '';
		if ($cbo_date_type != 1) {
			// Shipment Date
			if ($txt_date_from && $txt_date_to) {

				$gsd_were_con .= "and e.pub_shipment_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
			}
		}

		if ($plan_level == 5) {
			$gsd_were_con = str_replace("e.pub_shipment_date", "f.country_ship_date", $gsd_were_con);
		}

		if ($cbo_company_mst > 0) {
			$gsd_were_con .= "and d.company_name=$cbo_company_mst";
		}
		if ($order_status) {
			$gsd_were_con .= " and e.is_confirmed='$order_status'";
		}
		if ($cbo_buyer_name != 0) {
			$gsd_were_con .= " and d.buyer_name='$cbo_buyer_name'";
		}
		if ($txt_job_prifix) {
			$gsd_were_con .= " and d.job_no like '%" . $txt_job_prifix . "' ";
		}
		if (trim($txt_style_ref)) {
			if ($search_types == 1) {
				$gsd_were_con .= " and LOWER(d.style_ref_no) = '$txt_style_ref'";
				$subcon_style_con = "and LOWER(b.CUST_STYLE_REF) = '$txt_style_ref'";
			} else if ($search_types == 2) {
				$gsd_were_con .= " and LOWER(d.style_ref_no) like '%$txt_style_ref%'";
				$subcon_style_con = "and LOWER(b.CUST_STYLE_REF) like '$txt_style_ref%'";
			} else if ($search_types == 3) {
				$gsd_were_con .= " and LOWER(d.style_ref_no) like '%$txt_style_ref'";
				$subcon_style_con = "and LOWER(b.CUST_STYLE_REF) like '%$txt_style_ref'";
			} else if ($search_types == 4) {
				$gsd_were_con .= " and LOWER(d.style_ref_no) like '%$txt_style_ref%'";
				$subcon_style_con = "and LOWER(b.CUST_STYLE_REF) like '%$txt_style_ref%'";
			}
		}
		//echo $gsd_were_con;die;
		if (trim($internal_ref)) {
			if ($search_types == 1) {
				$gsd_were_con .= " and LOWER(e.GROUPING) = '$internal_ref'";
			} else if ($search_types == 2) {
				$gsd_were_con .= " and LOWER(e.GROUPING) like '%$internal_ref%'";
			} else if ($search_types == 3) {
				$gsd_were_con .= " and LOWER(e.GROUPING) like '%$internal_ref'";
			} else if ($search_types == 4) {
				$gsd_were_con .= " and LOWER(e.GROUPING) like '%$internal_ref%'";
			}
		}
		if (trim($txt_order_search)) {
			if ($search_types == 1) {
				$gsd_were_con .= " and LOWER(e.po_number) = '$txt_order_search'  ";
				$subcon_po_id_con = " AND LOWER(b.ID) = '$txt_order_search'  ";
			} else if ($search_types == 2) {
				$gsd_were_con .= " and LOWER(e.po_number) like '%$txt_order_search'  ";
				$subcon_po_id_con = " AND LOWER(b.ID) like '$txt_order_search%'  ";
			} else if ($search_types == 3) {
				$gsd_were_con .= " and LOWER(e.po_number) like '%$txt_order_search'  ";
				$subcon_po_id_con = " AND LOWER(b.ID) like '%$txt_order_search'  ";
			} else if ($search_types == 4) {
				$gsd_were_con .= " and LOWER(e.po_number) like '%$txt_order_search'  ";
				$subcon_po_id_con = " AND LOWER(b.ID) like '%$txt_order_search%'  ";
			}
		}
		if (trim($po_break_down_id)) {
			$gsd_were_con .= " and e.id in( $po_break_down_id )";
		}
		if (trim($color_size_id)) {
			$gsd_were_con .= " and f.id in( $color_size_id )";
		}
		if (trim($set_dtls_id)) {
			$gsd_were_con .= " and c.id in( $set_dtls_id )";
		}
		if ($cbo_year_selection && $txt_date_from == '' && $txt_date_to == '') {

			$yearCon_a = " and to_char(a.insert_date,'YYYY') = $cbo_year_selection";
		}
		if ($cbo_company_mst > 0) {
			$company_con = "and company_name='$cbo_company_mst'";
			$company = " and a.company_name='$cbo_company_mst'";
		}
		$by_pass = return_field_value("WORK_STUDY_INTEGRATED", "variable_settings_production", "variable_list=9 $company_con", "WORK_STUDY_INTEGRATED");

		//die($by_pass);
		if ($cbo_buyer_name > 0) {
			$buyer = " and a.buyer_name='$cbo_buyer_name'";
		} else {
			$buyer = "";
		}

		$order_cond = "";
		if (trim($txt_order_search)) {
			if ($search_types == 1) {
				$order_cond = " and LOWER(b.po_number) = '$txt_order_search'  ";
			} else if ($search_types == 2) {
				$order_cond = " and LOWER(b.po_number) like '%$txt_order_search%'  ";
			} else if ($search_types == 3) {
				$order_cond = " and LOWER(b.po_number) like '%$txt_order_search'  ";
			} else if ($search_types == 4) {
				$order_cond = " and LOWER(b.po_number) like '%$txt_order_search%'  ";
			}
		} else {
			$order_cond = "";
		}

		if (trim($po_break_down_id)) {
			$order_cond = " and b.id in( $po_break_down_id )";
		}
		if (trim($color_size_id)) {
			$color_size_cond = " and c.id in( $color_size_id )";
		} else {
			$color_size_cond = "";
		}
		if (trim($set_dtls_id)) {
			$set_cond = " and c.id in( $set_dtls_id )";
		} else {
			$set_cond = "";
		}
		$style_cond = "";
		if (trim($txt_style_ref)) {
			if ($search_types == 1) {
				$style_cond = " and LOWER(a.style_ref_no) = '$txt_style_ref'  ";
			} else if ($search_types == 2) {
				$style_cond = " and LOWER(a.style_ref_no) like '%$txt_style_ref%'  ";
			} else if ($search_types == 3) {
				$style_cond = " and LOWER(a.style_ref_no) like '%$txt_style_ref'  ";
			} else if ($search_types == 4) {
				$style_cond = " and LOWER(a.style_ref_no) like '%$txt_style_ref%'  ";
			}
		} else {
			$style_cond = "";
		}
		if (trim($internal_ref)) {
			if ($search_types == 1) {
				$internal_ref_con = " and LOWER(b.GROUPING) = '$internal_ref'  ";
			} else if ($search_types == 2) {
				$internal_ref_con = " and LOWER(b.GROUPING) like '%$internal_ref%'  ";
			} else if ($search_types == 3) {
				$internal_ref_con = " and LOWER(b.GROUPING) like '%$internal_ref'  ";
			} else if ($search_types == 4) {
				$internal_ref_con = " and LOWER(b.GROUPING) like '%$internal_ref%'  ";
			}
		} else {
			$internal_ref_con = "";
		}
		$job_cond = '';
		$tna_job = "";

		if ($txt_job_prifix) {

			if ($search_types == 1) {
				$job_cond = " and a.JOB_NO_PREFIX_NUM =" . $txt_job_prifix . " ";
				$tna_job = " and JOB_NO like '%" . $txt_job_prifix . "'";

				$subcon_job_prefix_con = "AND a.SUBCON_JOB = '$txt_job_prifix%'";
			} else if ($search_types == 2) {
				$job_cond = " and a.JOB_NO like '" . $txt_job_prifix . "%' ";
				$tna_job = " and JOB_NO like '" . $txt_job_prifix . "%'";

				$subcon_job_prefix_con = "AND a.SUBCON_JOB Like '$txt_job_prifix%'";
			} else if ($search_types == 3) {
				$job_cond = " and a.JOB_NO like '%" . $txt_job_prifix . "' ";
				$tna_job = " and JOB_NO like '%" . $txt_job_prifix . "'";

				$subcon_job_prefix_con = "AND a.SUBCON_JOB Like '%$txt_job_prifix'";
			} else if ($search_types == 4) {
				$job_cond = " and a.JOB_NO like '%" . $txt_job_prifix . "%' ";
				$tna_job = " and JOB_NO like '%" . $txt_job_prifix . "%'";

				$subcon_job_prefix_con = "AND a.SUBCON_JOB Like '%$txt_job_prifix%'";
			}
		}

		$shipment_date = "";
		$tna_date_cond = '';
		if ($cbo_date_type == 0) {
			// Shipment Date
			if ($txt_date_from && $txt_date_to) {

				$tna_date_cond = "and SHIPMENT_DATE between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
				$shipment_date = "and b.PUB_SHIPMENT_DATE between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
				$shipment_date .= "and b.PACK_HANDOVER_DATE between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
			} else {
				$tna_date_cond = "";
				$shipment_date = '';
			}
		} else if ($cbo_date_type == 2) {
			// Shipment Date
			if ($txt_date_from && $txt_date_to) {

				$shipment_date = "and b.PUB_SHIPMENT_DATE between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";

				//$subcon_deli_date = "AND b.DELIVERY_DATE between TO_DATE('$txt_date_from', 'DD/MM/YYYY') and TO_DATE('$txt_date_to', 'DD/MM/YYYY')";
				$subcon_deli_date = "AND b.DELIVERY_DATE between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";

				$subcon_deli_date_2 = "AND d.DELIVERY_DATE between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
			} else {
				$tna_date_cond = "";
				$shipment_date = '';
			}
		} else if ($cbo_date_type == 3) {
			if ($txt_date_from && $txt_date_to) {

				$shipment_date = "and b.PACK_HANDOVER_DATE between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
			} else {
				$tna_date_cond = "";
				$shipment_date = '';
			}
		} else {
			if ($txt_date_from && $txt_date_to) {

				$tna_date_cond = "and TASK_START_DATE between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
			} else {
				$tna_date_cond = "";
			}
		}

		$shipment_date2 = $shipment_date;

		if ($plan_level == 5 && $shipment_date) {
			$shipment_date2 = str_replace("b.PUB_SHIPMENT_DATE", "c.COUNTRY_SHIP_DATE ", $shipment_date);
		}
		$all_jobs_by_style_arr = array();
		$all_jobs_by_style_st = "";
		$conds = "";
		if ($txt_style_ref) {
			$conds .= " and  LOWER(style_ref_no) like '%$txt_style_ref%'";
		}

		if ($txt_order_search) {
			$conds .= " and  LOWER(po_number) like '%$txt_order_search%'";
		}

		if (trim($po_break_down_id)) {
			$conds .= " and b.id in( $po_break_down_id )";
		}
		if ($cbo_company_mst > 0) {
			$wo_com_con = "and a.company_name=$cbo_company_mst";
			$subcon_com_con = "AND a.COMPANY_ID = $cbo_company_mst";
		}
		if ($cbo_buyer_name > 0) {
			$wo_buyer_con = "and a.BUYER_NAME=$cbo_buyer_name";
			$subcon_buyer_con = "AND a.PARTY_ID = $cbo_buyer_name";
		}
		if ($chk_job_wo_po > 0) {
			$wo_job_prefix_con = "and a.job_no_prefix_num=$chk_job_wo_po";
		}
		if ($txt_job_prifix != 0) {
			$wo_txt_job_prefix_con = "and a.job_no_prefix_num=$txt_job_prifix";
		}
		// if ($txt_order_search != 0) {
		// 	$wo_po_con = "and b.po_number=$txt_order_search";
		// }

		$sql = "SELECT a.ID as JOB_ID, a.JOB_NO,b.id as PO_ID,d.GMTS_ITEM_ID,d.SMV_PCS,d.COMPLEXITY,d.QUOT_ID from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,wo_po_details_mas_set_details d where a.ID=b.JOB_ID and a.ID=c.JOB_ID and a.ID=d.JOB_ID and b.ID=c.PO_BREAK_DOWN_ID  and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and c.STATUS_ACTIVE=1  and a.IS_DELETED=0  and b.IS_DELETED=0  and c.IS_DELETED=0 $shipment_date $smv_cond_d $item_name_cond_d $yearCon_a $where_con $internal_ref_con $conds $job_cond $wo_date_range $wo_com_con $wo_buyer_con $wo_job_prefix_con $wo_txt_job_prefix_con  $smv_cond_d";
		//echo $sql;die;

		$jobs_sqls = $this->db->query($sql)->result();
		//print_r($jobs_sqls);die;
		$job_set_data = array();
		$quot_ids = array();
		$job_id_arr[0] = 0;

		$gbl_insert_array = array();
		//$max_tmp_id = $this->get_max_value("GBL_TEMP_ENGINE", "ID") + 1;

		foreach ($jobs_sqls as $values) {
			$all_jobs_by_style_arr[$values->JOB_NO] = $values->JOB_NO;
			$all_po_by_style_arr[$values->PO_ID] = $values->PO_ID;

			$job_set_data[$values->JOB_NO][$values->GMTS_ITEM_ID]['SMV_PCS'] = $values->SMV_PCS;
			$job_set_data[$values->JOB_NO][$values->GMTS_ITEM_ID]['COMPLEXITY'] = $values->COMPLEXITY;
			$job_set_data[$values->JOB_NO][$values->GMTS_ITEM_ID]['QUOT_ID'] = $values->QUOT_ID;
			$quot_ids[$values->QUOT_ID] = $values->QUOT_ID;
			$job_id_arr[$values->JOB_ID] = $values->JOB_ID;

			$gbl_insert_po_array[$values->PO_ID] = $values->PO_ID;
			$gbl_insertjob_array[$values->JOB_ID] = $values->JOB_ID;
			// $gbl_insert_po_array[$values->PO_ID] = array(

			// 	'USER_ID' => $user_id,
			// 	'REF_FROM' => 55,
			// 	'ENTRY_FORM' => 5002,
			// 	'REF_VAL' => $values->PO_ID
			// );

			// $gbl_insertjob_array[$values->JOB_ID] = array(

			// 	'USER_ID' => $user_id,
			// 	'REF_FROM' => 56,
			// 	'ENTRY_FORM' => 5002,
			// 	'REF_VAL' => $values->JOB_ID
			// );
			// $max_tmp_id++;
		}
		//print_r($jobs_sqls);die;
		//First delete  by 'ENTRY_FORM' => 5001 & self user data....................then insert ;
		$emblishment_name_array = array(1 => "Printing", 2 => "Embroidery", 3 => "Wash", 4 => "Special Works", 5 => "Gmts Dyeing", 6 => "Attachment", 99 => "Others");

		if (count($gbl_insert_po_array) || count($gbl_insertjob_array)) {
			$this->deleteRowByAttribute('GBL_TEMP_ENGINE', array('ENTRY_FORM' => 5002, "USER_ID" => $user_id, 'REF_FROM' => 55));
			$this->deleteRowByAttribute('GBL_TEMP_ENGINE', array('ENTRY_FORM' => 5002, "USER_ID" => $user_id, 'REF_FROM' => 56));
			//$this->db->insert_batch("GBL_TEMP_ENGINE", $gbl_insert_po_array);
			//$this->db->insert_batch("GBL_TEMP_ENGINE", $gbl_insertjob_array);
			//print_r(5);die; 
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 5002, 55, $gbl_insert_po_array, $empty_arr);
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 5002, 56, $gbl_insertjob_array, $empty_arr);
		}

		$query_emb = "SELECT a.id,a.JOB_ID, a.job_no, a.EMB_NAME from wo_pre_cost_embe_cost_dtls a,GBL_TEMP_ENGINE gbl where a.emb_name!=3 and a.JOB_ID=gbl.REF_VAL and gbl.ENTRY_FORM = 5002 and gbl.USER_ID =$user_id and gbl.REF_FROM = 56 and a.is_deleted=0";
		$table_emb = $this->db->query($query_emb)->result();
		//print_r($table_emb);die;
		$emb_table_arr = array();
		foreach ($table_emb as $row) {
			$emb_table_arr[$row->JOB_ID][] = $emblishment_name_array[$row->EMB_NAME];
		}

		//print_r($emb_table_arr);die; 
		//print_r($all_jobs_by_style_arr);die;
		$gsd_sql = "SELECT a.WORKING_HOUR, a.TOTAL_SMV, a.GMTS_ITEM_ID, a.STYLE_REF, b.RESOURCE_GSD, c.EFFICIENCY, c.ALLOCATED_MP, c.TARGET, a.BULLETIN_TYPE FROM ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b LEFT JOIN ppl_balancing_mst_entry c ON c.GSD_MST_ID = b.mst_id AND c.STATUS_ACTIVE = 1 AND c.IS_DELETED = 0 AND c.ALLOCATED_MP > 0 WHERE a.ID = b.MST_ID AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND a.BULLETIN_TYPE in(3,4) AND a.STYLE_REF IN (SELECT STYLE_REF_NO FROM wo_po_details_master c, GBL_TEMP_ENGINE d WHERE IS_DELETED = 0 AND STATUS_ACTIVE = 1 AND c.ID = d.REF_VAL AND d.ENTRY_FORM = 5002 AND d.REF_FROM = 56 AND d.USER_ID = $user_id ) ORDER BY a.ID ASC";
		//print_r($gsd_sql);die;


		//echo $gsd_sql; die;
		$gsd_array = array();
		$gsd_sql = sql_select($gsd_sql);
		foreach ($gsd_sql as $vals) {
			$gsd_array[$vals->STYLE_REF][$vals->GMTS_ITEM_ID]["SMV"] = $vals->TOTAL_SMV;
			$gsd_array[$vals->STYLE_REF][$vals->GMTS_ITEM_ID]["EFFICIENCY"] = $vals->EFFICIENCY;
			$gsd_array[$vals->STYLE_REF][$vals->GMTS_ITEM_ID]["ALLOCATED_MP"] = $vals->ALLOCATED_MP;
			$gsd_array[$vals->STYLE_REF][$vals->GMTS_ITEM_ID]["TARGET"] = $vals->TARGET;
			$gsd_array[$vals->STYLE_REF][$vals->GMTS_ITEM_ID]["WORKING_HOUR"] = $vals->WORKING_HOUR;
			$gsd_array[$vals->STYLE_REF][$vals->GMTS_ITEM_ID]["RESOURCE_GSD"][$vals->RESOURCE_GSD] = $vals->RESOURCE_GSD;
		}

		//print_r($gsd_array['WOrk Study']);die;
		//end gsd.................................................................
		if (count($all_jobs_by_style_arr)) {
			$all_jobs_by_style_st = "'" . implode("','", $all_jobs_by_style_arr) . "'";
		}
		$all_jobs_by_style_cond = "";
		if ($all_jobs_by_style_st) {
			$all_jobs_by_style_cond .= " and job_no in($all_jobs_by_style_st)";
		}

		if (count($all_jobs_by_style_arr) > 999) {
			$all_jobs_by_style_cond = "";
			$chnk = array_chunk($all_jobs_by_style_arr, 999);

			foreach ($chnk as $v) {
				$jobs = "'" . implode("','", $v) . "'";
				if ($all_jobs_by_style_cond == "") {
					$all_jobs_by_style_cond .= " and (job_no in($jobs)";
				} else {
					$all_jobs_by_style_cond .= " or  job_no in($jobs)";
				}
			}
			$all_jobs_by_style_cond .= ")";
		}
		//print_r($jobs);die;
		//return $tna_date_cond;

		if (trim($po_break_down_id)) {
			$tna_were_con = " and po_number_id in( $po_break_down_id )";
		} else {
			$tna_were_con = "";
		}
		//print_r(5);die;

		if ($order_type == 0 || $order_type == 2) {

			$query_subcon = "SELECT a.ID,a.COMPANY_ID,a.SUBCON_JOB,a.PARTY_ID,b.SMV,b.CUST_STYLE_REF,b.MAIN_PROCESS_ID,b.ORDER_QUANTITY,b.ORDER_NO,b.MATERIAL_RECV_DATE,c.COLOR_ID,c.SIZE_ID,c.PLAN_CUT,c.QNTY,b.DELIVERY_DATE,c.ITEM_ID,b.ID as PO_BREAKDOWN_ID FROM SUBCON_ORD_MST a, SUBCON_ORD_DTLS b, SUBCON_ORD_BREAKDOWN c WHERE a.ID = b.MST_ID AND a.ID = c.MST_ID $subcon_com_con $subcon_buyer_con AND a.ENTRY_FORM = 238  $subcon_deli_date AND b.MAIN_PROCESS_ID in('5','11')  AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND c.STATUS_ACTIVE = 1 AND c.IS_DELETED = 0 AND c.ORDER_ID = b.id  $subcon_job_prefix_con $subcon_po_id_con $subcon_style_con";
			$table_subcon = $this->db->query($query_subcon)->result();
			//print_r($query_subcon);die;

			$query_subcon_production = "SELECT a.ORDER_ID, a.GMTS_ITEM_ID , b.PROD_QNTY,  c.COLOR_ID, c.QNTY, d.DELIVERY_DATE,d.ID as PO_BREAKDOWN_ID FROM subcon_gmts_prod_dtls a,SUBCON_GMTS_PROD_COL_SZ b, subcon_ord_breakdown c, SUBCON_ORD_DTLS d WHERE d.id = c.ORDER_ID and a.ORDER_ID = d.ID AND  b.ORD_COLOR_SIZE_ID = c.ID AND b.DTLS_ID = a.id AND a.status_active = 1 AND a.is_deleted = 0 and a.PRODUCTION_TYPE=2 AND b.PRODUCTION_TYPE = 2 $subcon_com_con $subcon_deli_date_2";
			$table_subcon_production = $this->db->query($query_subcon_production)->result();
			//print_r($query_subcon_production);die;

			$subcon_production_qnty = array();
			foreach ($table_subcon_production as $row) {
				$subcon_production_qnty[$row->PO_BREAKDOWN_ID][$row->GMTS_ITEM_ID][$row->COLOR_ID] += $row->PROD_QNTY;
			}
			//print_r($subcon_production_qnty);die;
			$subcon_po_arr = array();
			foreach ($table_subcon as $row) {
				$subcon_po_arr[$row->PO_BREAKDOWN_ID] = $row->PO_BREAKDOWN_ID;
			}
			if (count($subcon_po_arr) > 0) {
				$all_po_by_style_arr = array_merge($all_po_by_style_arr, $subcon_po_arr);
			}
		}




		$tna_query = "SELECT MIN (TASK_START_DATE) AS TASK_START_DATE, MAX (TASK_FINISH_DATE) AS TASK_FINISH_DATE, PO_NUMBER_ID, JOB_NO, TASK_NUMBER FROM tna_process_mst a,GBL_TEMP_ENGINE b WHERE IS_DELETED = 0 AND STATUS_ACTIVE = 1 AND a.PO_NUMBER_ID = b.REF_VAL AND b.ENTRY_FORM = 5002 AND b.REF_FROM = 55 AND b.user_id = $user_id GROUP BY PO_NUMBER_ID, JOB_NO, TASK_NUMBER";

		// echo $tna_query;
		// die;
		$sql = $this->db->query($tna_query);
		$sqlResArr = $sql->result();
		$sel_pos_arr = array();
		$sel_jobs_arr = array();
		foreach ($sqlResArr as $srows) {
			if ($srows->TASK_NUMBER == 84 || $srows->TASK_NUMBER == 186 || $srows->TASK_NUMBER == 187) {
				$tna_task_data[$srows->PO_NUMBER_ID]['cut_task_start_date'] = date("d-m-Y", strtotime($srows->TASK_START_DATE));
				$tna_task_data[$srows->PO_NUMBER_ID]['cut_task_finish_date'] = date("d-m-Y", strtotime($srows->TASK_FINISH_DATE));
			}
			if ($srows->TASK_NUMBER == 86 || $srows->TASK_NUMBER == 190 || $srows->TASK_NUMBER == 191) {
				$tna_task_data[$srows->PO_NUMBER_ID]['task_start_date'] = date("d-m-Y", strtotime($srows->TASK_START_DATE));
				$tna_task_data[$srows->PO_NUMBER_ID]['task_finish_date'] = date("d-m-Y", strtotime($srows->TASK_FINISH_DATE));
				$sel_jobs_arr[$srows->JOB_NO] = $srows->JOB_NO;
				$sel_pos_arr[$srows->PO_NUMBER_ID] = $srows->PO_NUMBER_ID;
			}
		}
		//print_r($sel_jobs_arr);die;

		if (count($sel_pos_arr) == 0 && $ignore_tna == 0) {
			return $data_array;
			die;
		}
		//print_r($all_po_by_style_arr);die;
		$sql = "SELECT b.PLAN_ID,b.PLAN_QNTY,b.PO_BREAK_DOWN_ID,b.ITEM_NUMBER_ID,b.COLOR_NUMBER_ID,a.ORDER_TYPE from ppl_sewing_plan_board a, ppl_sewing_plan_board_powise b where a.PLAN_ID=b.PLAN_ID and a.IS_DELETED=0 and a.STATUS_ACTIVE=1";

		if (count($all_po_by_style_arr)) {
			$sql .= where_con_using_array($all_po_by_style_arr, 0, 'b.PO_BREAK_DOWN_ID');
		}

		if ($ignore_tna == 0) {
			$sql .= where_con_using_array($sel_pos_arr, 0, 'b.PO_BREAK_DOWN_ID');
			$prod_cond .= where_con_using_array($sel_pos_arr, 0, 'po_break_down_id');
		}
		$sql .= " group by b.PLAN_ID,b.PLAN_QNTY,b.PO_BREAK_DOWN_ID,b.ITEM_NUMBER_ID,b.COLOR_NUMBER_ID,a.ORDER_TYPE";
		//print_r($all_po_by_style_arr);die;
		//echo $sql;die;

		$sql = $this->db->query($sql);
		$planned_qnty = array();
		$planned_qnty_v2 = array();
		if ($sql->num_rows() > 0) {
			$sqlResArr = $sql->result();
			//print_r($sqlResArr); die;
			foreach ($sqlResArr as $srows) {
				if (!$srows->COLOR_NUMBER_ID) {
					$srows->COLOR_NUMBER_ID = 0;
				}
				//print_r("before"); die;
				//$planned_qnty[$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID][$srows->COLOR_NUMBER_ID] = 0;
				if ($srows->ORDER_TYPE == 1) {
					if (isset($planned_qnty[$srows->ORDER_TYPE][$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID][$srows->COLOR_NUMBER_ID])) {
						$planned_qnty[$srows->ORDER_TYPE][$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID][$srows->COLOR_NUMBER_ID] += $srows->PLAN_QNTY;
						//print_r("if"); die;
					} else {
						$planned_qnty[$srows->ORDER_TYPE][$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID][$srows->COLOR_NUMBER_ID] = $srows->PLAN_QNTY;
						//print_r("else"); die;
					}
				} else if ($srows->ORDER_TYPE == 2) {
					if (isset($planned_qnty[$srows->ORDER_TYPE][$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID][$srows->COLOR_NUMBER_ID])) {
						$planned_qnty[$srows->ORDER_TYPE][$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID][$srows->COLOR_NUMBER_ID] += $srows->PLAN_QNTY;
						//print_r("if"); die;
					} else {
						$planned_qnty[$srows->ORDER_TYPE][$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID][$srows->COLOR_NUMBER_ID] = $srows->PLAN_QNTY;
						//print_r("else"); die;
					}
				}
			}
		}
		//print_r($planned_qnty);die;
		$com_res = $this->db->query("select ID,COMPANY_NAME from lib_company where STATUS_ACTIVE =1 and IS_DELETED=0 and CORE_BUSINESS=1  order by COMPANY_NAME")->result();

		$buyer_res = $this->db->query("select a.ID,a.BUYER_NAME from  lib_buyer a, lib_buyer_tag_company b where a.ID=b.BUYER_ID  and a.STATUS_ACTIVE=1 and a.IS_DELETED=0")->result();

		$garment_res = $this->db->query("select ID,ITEM_NAME,PRODUCT_CODE FROM  lib_garment_item where STATUS_ACTIVE=1 and IS_DELETED=0 order by ITEM_NAME")->result();

		foreach ($com_res as $value) {
			$comp[$value->ID] = $value->COMPANY_NAME;
		}
		foreach ($buyer_res as $value) {
			$buyer_arr[$value->ID] = $value->BUYER_NAME;
		}
		foreach ($garment_res as $value) {
			$garments_item[$value->ID] = $value->ITEM_NAME;
			if($value->PRODUCT_CODE){
				$garments_item[$value->ID] = $value->ITEM_NAME."(".$value->PRODUCT_CODE.")";
			}
			
		}

		//print_r($garments_item);die;

		if ($ignore_tna == 1) {
			$prod_cond = "";
		}

		$sql = "SELECT PO_BREAK_DOWN_ID, SUM (PRODUCTION_QUANTITY) AS PRODUCTION_QUANTITY FROM pro_garments_production_mst a, GBL_TEMP_ENGINE b WHERE a.PO_BREAK_DOWN_ID = b.REF_VAL AND b.ENTRY_FORM = 5002 AND b.REF_FROM = 55 AND b.USER_ID = $user_id AND PRODUCTION_TYPE = 5 AND STATUS_ACTIVE = 1 AND IS_DELETED = 0 GROUP BY PO_BREAK_DOWN_ID";
		//print_r($sql);die;
		$sql_data = $this->db->query($sql)->result();
		$k = 0;

		foreach ($sql_data as $rows) {
			$production_details[$rows->PO_BREAK_DOWN_ID] = $rows->PRODUCTION_QUANTITY;
		}

		if ($ignore_tna == 0) {
			$jobs_cond = where_con_using_array($sel_jobs_arr, 1, 'JOB_NO');
		} else {
			$jobs_cond = " 1=1";
		}

		//echo $set_dtls_id;die;

		if (trim($set_dtls_id)) {
			$jobs_cond .= " and ID in( $set_dtls_id )";
		}


		$sql = " SELECT ID,DAY_TARGET,WORKING_HOUR,TOTAL_SMV,GMTS_ITEM_ID from ppl_gsd_entry_mst ";
		// Get Efficiecny %; WORKING_HOUR=1; target per hour,
		$sql = $this->db->query($sql)->result();
		$day_target = array();
		foreach ($sql as $srows) {
			$day_target[$srows->ID][$srows->GMTS_ITEM_ID]['DAY_TARGET'] = $srows->DAY_TARGET;
			$day_target[$srows->ID][$srows->GMTS_ITEM_ID]['WORKING_HOUR'] = $srows->WORKING_HOUR;
			$day_target[$srows->ID][$srows->GMTS_ITEM_ID]['TOTAL_SMV'] = $srows->TOTAL_SMV;
		}

		$country_sql = "SELECT ID,COUNTRY_NAME FROM LIB_COUNTRY";
		$countries = $this->db->query($country_sql)->result();
		$country_arr = array();
		foreach ($countries as $crows) {
			$country_arr[$crows->ID] = $crows->COUNTRY_NAME;
		}

		if ($ignore_tna == 0) {
			$tna_po_con = where_con_using_array($sel_pos_arr, 0, 'b.id');
		}
		//print_r($tna_po_con);die;
		if ($plan_level == 1) {
			$sql = "SELECT a.ID as JOB_ID,a.PRODUCT_CATEGORY,a.PRODUCT_DEPT,a.PRO_SUB_DEP,a.SEASON_BUYER_WISE,a.SEASON_YEAR,a.BRAND_ID,'' as COLOR_SIZE_ID,b.IS_CONFIRMED,a.JOB_NO_PREFIX_NUM, a.JOB_NO,a.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_QUANTITY,a.GARMENTS_NATURE, b.ID PO_BREAK_DOWN_ID,b.PO_NUMBER, b.PO_QUANTITY,b.SHIPMENT_DATE AS SHIPMENT_DATE,b.PUB_SHIPMENT_DATE AS PUB_SHIPMENT_DATE, sum(d.PLAN_CUT_QNTY) as PLAN_CUT, to_char(a.insert_date,'YYYY') as YEAR,b.ID,SET_BREAK_DOWN,TOTAL_SET_QNTY, SET_SMV,c.ID AS SET_DTLS_ID ,c.GMTS_ITEM_ID,c.SET_ITEM_RATIO,a.CLIENT_ID,c.SMV_PCS,b.GROUPING,b.PACK_HANDOVER_DATE,a.WORKING_COMPANY_ID  FROM wo_po_details_master a, wo_po_break_down b,wo_po_details_mas_set_details c,WO_PO_COLOR_SIZE_BREAKDOWN d where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.job_no_mst=c.job_no and a.status_active=1 and b.status_active=1 and b.SHIPING_STATUS<3 $str_shi $company $buyer $job_cond $set_cond $order_cond $shipment_date $style_cond $is_confirmed_cond $smv_cond_c $item_name_cond_c $internal_ref_con AND c.GMTS_ITEM_ID = d.ITEM_NUMBER_ID AND c.job_no = d.JOB_NO_MST and d.PO_BREAK_DOWN_ID=b.id AND d.IS_DELETED = 0 AND d.STATUS_ACTIVE = 1 $where_con $yearCon_a $tna_po_con $pro_cate_con";


			$sql .= " group by a.ID,a.PRODUCT_CATEGORY,a.PRODUCT_DEPT,a.PRO_SUB_DEP,a.SEASON_BUYER_WISE,a.SEASON_YEAR,a.BRAND_ID,b.IS_CONFIRMED, a.JOB_NO_PREFIX_NUM, a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME,  a.STYLE_REF_NO, a.JOB_QUANTITY,  a.GARMENTS_NATURE,  b.ID,  b.PO_NUMBER, b.PO_QUANTITY, b.SHIPMENT_DATE , b.PUB_SHIPMENT_DATE , TO_CHAR (a.insert_date, 'YYYY'), b.ID,  SET_BREAK_DOWN, TOTAL_SET_QNTY, SET_SMV, c.ID ,c.GMTS_ITEM_ID, c.SET_ITEM_RATIO,  a.CLIENT_ID,  c.SMV_PCS,  b.GROUPING, b.PACK_HANDOVER_DATE,a.WORKING_COMPANY_ID ";
		} else {
			if ($plan_level == 2) {
				$fields = ",c.COLOR_NUMBER_ID,b.PUB_SHIPMENT_DATE";
				$group_by = ",c.color_number_id,b.PUB_SHIPMENT_DATE";
			} else if ($plan_level == 3) {
				$fields = ",c.SIZE_NUMBER_ID,b.PUB_SHIPMENT_DATE";
				$group_by = ",c.size_number_id,b.PUB_SHIPMENT_DATE";
			} else if ($plan_level == 4) {
				$fields = ",c.COLOR_NUMBER_ID,c.SIZE_NUMBER_ID,c.COUNTRY_SHIP_DATE as PUB_SHIPMENT_DATE";
				$group_by = ",c.color_number_id,c.size_number_id,c.COUNTRY_SHIP_DATE";
			} else if ($plan_level == 5) {
				$fields = ",c.COUNTRY_ID,c.COUNTRY_SHIP_DATE as PUB_SHIPMENT_DATE";
				$group_by = ",c.COUNTRY_ID,c.COUNTRY_SHIP_DATE";
			} else if ($plan_level == 6) {
				$fields = ",c.COUNTRY_ID,c.COLOR_NUMBER_ID,c.COUNTRY_SHIP_DATE as PUB_SHIPMENT_DATE";
				$group_by = ",c.country_id,c.color_number_id,c.COUNTRY_SHIP_DATE";
			} else if ($plan_level == 7) {
				$fields = ",c.COUNTRY_ID,c.COLOR_NUMBER_ID,c.SIZE_NUMBER_ID,c.COUNTRY_SHIP_DATE as PUB_SHIPMENT_DATE";
				$group_by = ",c.country_id,c.color_number_id,c.size_number_id,c.COUNTRY_SHIP_DATE";
			} else if ($plan_level == 8) {
				$fields = ",c.COUNTRY_ID,c.SIZE_NUMBER_ID,c.COUNTRY_SHIP_DATE as PUB_SHIPMENT_DATE";
				$group_by = ",c.country_id,c.size_number_id,c.COUNTRY_SHIP_DATE";
			}

			if (trim($set_dtls_id)) {
				$set_cond = " and d.id in( $set_dtls_id )";
			} else {
				$set_cond = "";
			}

			//echo $job_cond;die;
			$sql = "SELECT a.ID as JOB_ID,a.PRODUCT_CATEGORY,a.PRODUCT_DEPT,a.PRO_SUB_DEP,a.SEASON_BUYER_WISE,a.SEASON_YEAR,a.BRAND_ID,listagg(c.id,',') within group (order by c.id) as COLOR_SIZE_ID,c.item_number_id,b.IS_CONFIRMED,a.JOB_NO_PREFIX_NUM, a.JOB_NO,A.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.CLIENT_ID,a.JOB_QUANTITY,a.GARMENTS_NATURE,b.ID PO_BREAK_DOWN_ID,b.PO_NUMBER,b.PACK_HANDOVER_DATE,b.SHIPMENT_DATE AS SHIPMENT_DATE,b.GROUPING, sum(c.PLAN_CUT_QNTY) as PLAN_CUT,to_char(a.insert_date,'YYYY') as YEAR,b.ID,SET_BREAK_DOWN,TOTAL_SET_QNTY,SET_SMV, sum(c.plan_cut_qnty) PLAN_CUT_QNTY,sum(c.order_quantity ) po_quantity,d.ID AS SET_DTLS_ID ,d.GMTS_ITEM_ID,d.SET_ITEM_RATIO,a.WORKING_COMPANY_ID,d.SMV_PCS $fields FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,wo_po_details_mas_set_details d where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=d.job_no and d.job_no=b.job_no_mst and d.GMTS_ITEM_ID=c.ITEM_NUMBER_ID and a.status_active=1 $color_size_cond and b.status_active=1 and c.status_active=1 and c.IS_DELETED=0 and b.SHIPING_STATUS<3 $str_shi $company $buyer $job_cond $set_cond $order_cond $shipment_date2 $style_cond $is_confirmed_cond $smv_cond_d $item_name_cond_d  $internal_ref_con $where_con $yearCon_a $tna_po_con $pro_cate_con";
		}
		if ($plan_level != 1) {
			$sql .= " group by a.ID,a.PRODUCT_CATEGORY,a.PRODUCT_DEPT,a.PRO_SUB_DEP,a.SEASON_BUYER_WISE,a.SEASON_YEAR,a.BRAND_ID,c.item_number_id, b.is_confirmed,a.job_no_prefix_num, a.job_no,a.CLIENT_ID,a.company_name,a.buyer_name, a.style_ref_no,a.job_quantity, b.id,b.po_number,b.PACK_HANDOVER_DATE,b.shipment_date,a.garments_nature,a.insert_date,b.id,b.GROUPING, set_break_down,total_set_qnty,set_smv,d.id,d.gmts_item_id,d.set_item_ratio,d.smv_pcs,a.WORKING_COMPANY_ID  $group_by";
		}
		$sql .= " order by a.job_no,b.id, b.shipment_date ";

		//echo $sql;die; 

		$sql_exe = $this->db->query($sql)->result();
		$i = 0;

		$poArr = array();
		foreach ($sql_exe as $poRow) {
			$poArr[$poRow->PO_BREAK_DOWN_ID] = $poRow->PO_BREAK_DOWN_ID;
		}




		$production_dtls_data_arr = $this->get_production_qnty_by_po_item(implode(',', $poArr), '', '', $plan_level, $user_id, $txt_date_from, $txt_date_to);
		$production_data_arr = $production_dtls_data_arr['PO_ITEM_DATA'];

		$is_confirmed_arr = array(0 => "", 1 => "Confirmed", 2 => "Projected", "" => "");
		//print_r($poArr);die;

		//library................................................
		$color_data_rs = $this->db->query("select ID,COLOR_NAME from lib_color where STATUS_ACTIVE=1 and IS_DELETED=0")->result();
		foreach ($color_data_rs as $colorRow) {
			$colorArr[$colorRow->ID] = $colorRow->COLOR_NAME;
		}

		//start booking delivery Date
		//$txt_job_prifix

		$query_lib_item = "SELECT ID,TRIM_TYPE,ITEM_NAME FROM LIB_ITEM_GROUP WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0";
		$table_lib_item = $this->db->query($query_lib_item)->result();
		$lib_item_group = array();
		$lib_item_group_name = array();
		foreach ($table_lib_item as $row) {
			$lib_item_group[$row->ID] = $row->TRIM_TYPE;
			$lib_item_group_name[$row->ID] = $row->ITEM_NAME;
		}
		// print_r(5);
		// die;

		$query_booking = "SELECT a.ID,a.BOOKING_NO,b.PO_BREAK_DOWN_ID, MAX (a.DELIVERY_DATE) AS DELIVERY_DATE, b.TRIM_GROUP, b.BOOKING_TYPE FROM WO_BOOKING_MST a, WO_BOOKING_DTLS b, GBL_TEMP_ENGINE c WHERE a.ID = b.BOOKING_MST_ID AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND b.PO_BREAK_DOWN_ID = c.REF_VAL AND c.ENTRY_FORM = 5002 AND c.REF_FROM = 55 AND c.USER_ID = $user_id GROUP BY b.PO_BREAK_DOWN_ID, b.TRIM_GROUP, b.BOOKING_TYPE,a.ID,a.BOOKING_NO";
		// print_r($query_booking);
		// die;
		$table_booking = $this->db->query($query_booking)->result();
		$lib_delivery_date = array();
		$temp_delivery_date = array();
		// print_r($table_booking);
		// die;
		$booking_no_Po_wise = array();
		foreach ($table_booking as $row) {

			$booking_no_Po_wise[$row->PO_BREAK_DOWN_ID][$row->ID] = $row->BOOKING_NO;

			if ($row->BOOKING_TYPE == 2) {
				$temp_delivery_date[$row->PO_BREAK_DOWN_ID][$row->BOOKING_TYPE][$lib_item_group[$row->TRIM_GROUP]][$row->TRIM_GROUP] = $row->DELIVERY_DATE;
			} elseif ($row->BOOKING_TYPE == 1) {
				$lib_delivery_date[$row->PO_BREAK_DOWN_ID] = "$row->DELIVERY_DATE";
			}
		}
		// print_r($booking_no_Po_wise);
		// die;
		unset($table_booking);

		//print_r($sql_exe);die;

		foreach ($sql_exe as $rows) {

			if ($plan_level == 2) {
				$productionQty = array_sum($production_data_arr[$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID]) * 1;
			} else {
				$productionQty = array_sum($production_data_arr[$rows->PO_BREAK_DOWN_ID]) * 1;
			}
			if ($rows->PLAN_CUT_QNTY <= $productionQty && $ignore_full_prod == 1) {
				continue;
			}
			//print_r($productionQty);die;
			//Assigning 0 to plan quantity after checking is it is null
			$planned_qnty_v2[1][$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID][$rows->COLOR_NUMBER_ID] += $planned_qnty[1][$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID][$rows->COLOR_NUMBER_ID] ? $planned_qnty[1][$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID][$rows->COLOR_NUMBER_ID] : 0;
			//print_r($planned_qnty[$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID][$rows->COLOR_NUMBER_ID]); die;

			//Unplanned sorting
			if ($planned_qnty_v2[1][$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID][$rows->COLOR_NUMBER_ID] > 0 and $plan_status == 1) {
				continue;
			}
			//Unplanned sorting 2
			if ($rows->PLAN_CUT_QNTY <= $productionQty and $plan_status == 1) {
				continue;
			}

			// if($rows->ID == 71484){
			// 	echo $planned_qnty[$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID][$rows->COLOR_NUMBER_ID]."*".$rows->PLAN_CUT_QNTY."*".$productionQty."*".$plan_status; die;
			// }

			//Partial Plan sorting 
			if ($rows->PLAN_CUT_QNTY <= $planned_qnty_v2[1][$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID][$rows->COLOR_NUMBER_ID] && $plan_status == 2) {
				continue;
			}
			//Partial Plan sorting 2
			if ($planned_qnty_v2[1][$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID][$rows->COLOR_NUMBER_ID] == 0 && $plan_status == 2) {
				continue;
			}
			//Partial Plan sorting 3 (here first value is yet_to_plan_v3)
			if ($rows->PLAN_CUT_QNTY - ($productionQty + $planned_qnty_v2[1][$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID][$rows->COLOR_NUMBER_ID]) <= 0 && $plan_status == 2) {
				continue;
			}
			//Full Plan Sorting 
			if ($rows->PLAN_CUT_QNTY > $planned_qnty_v2[1][$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID][$rows->COLOR_NUMBER_ID] && $plan_status == 3) {
				continue;
			}
			//$planned_qnty[$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID][$rows->COLOR_NUMBER_ID] == 0 && $plan_status == 2
			//|| $rows->PLAN_CUT_QNTY <= $planned_qnty[$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID][$rows->COLOR_NUMBER_ID] && $plan_status == 2

			if ($emb_filter == 1) {

				if (empty($emb_table_arr[$rows->JOB_ID])) {
					//print_r(5);die;
					continue;
				}
			} else if ($emb_filter == 2) {
				if ($emb_table_arr[$rows->JOB_ID]) {
					//print_r(5);die;
					continue;
				}
			}


			$cbo_company_mst = $rows->COMPANY_NAME;
			//allocation.............start;
			$FROM_PO_QTY = $allocationFromArr['PO'][$cbo_company_mst][$rows->JOB_NO][$rows->PO_BREAK_DOWN_ID];
			$To_PO_QTY = $allocationToArr['PO'][$cbo_company_mst][$rows->JOB_NO][$rows->PO_BREAK_DOWN_ID];

			$FROM_JOB_QTY = $allocationFromArr['JOB'][$cbo_company_mst][$rows->JOB_NO];
			$To_JOB_QTY = $allocationToArr['JOB'][$cbo_company_mst][$rows->JOB_NO];

			$FROM_ITEM_QTY = $allocationFromArr['ITEM'][$cbo_company_mst][$rows->JOB_NO][$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID];
			$To_ITEM_QTY = $allocationToArr['ITEM'][$cbo_company_mst][$rows->JOB_NO][$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID];
			$PO_QTY = $To_PO_QTY - $FROM_PO_QTY;
			$JOB_QTY = $To_JOB_QTY - $FROM_JOB_QTY;
			$ITEM_QTY = $To_ITEM_QTY - $FROM_ITEM_QTY;
			//allocation.............end;

			$setdata[0] = $rows->GMTS_ITEM_ID;
			$setdata[1] = $rows->SET_ITEM_RATIO;
			$setdata[2] = $rows->SMV_PCS;


			$color_index = 0;
			if ($plan_level == 2) {
				$color_index = $rows->COLOR_NUMBER_ID;
			}

			if (isset($planned_qnty_v2[1][$rows->ID][$setdata[0]][$color_index])) {
				$plan_qnty = $planned_qnty_v2[1][$rows->ID][$setdata[0]][$color_index];
			} else {
				$plan_qnty = 0;
			}


			//$plan_full=1;
			$YET_TO_PLAN = (($setdata[1] * $rows->PLAN_CUT) + $ITEM_QTY) - $plan_qnty * 1;
			if ($plan_full == 1 && $YET_TO_PLAN <= 0) {
				continue; //now show;
			}
			//print_r($rows->WORKING_COMPANY_ID);die;
			$data_array[$i]["COMPANY_ID"] = $rows->COMPANY_NAME;
			$data_array[$i]["W_COMPANY_ID"] = ($rows->WORKING_COMPANY_ID) ? $rows->WORKING_COMPANY_ID : -1 ;
			$data_array[$i]["ID"] = $rows->ID;
			$data_array[$i]["JOB_NO"] = $rows->JOB_NO;
			$data_array[$i]["product_cate"] = "$rows->PRODUCT_CATEGORY";
			//$data_array[$i]["product_cate"] = "2 ";
			$data_array[$i]["ORDER_TYPE"] = 1;

			$temp_delivery_date_str[$rows->PO_BREAK_DOWN_ID] = "";
			if ($lib_delivery_date[$rows->PO_BREAK_DOWN_ID]) {
				$temp_delivery_date_str[$rows->PO_BREAK_DOWN_ID] .= $lib_delivery_date[$rows->PO_BREAK_DOWN_ID];
			}
			if (max($temp_delivery_date[$rows->PO_BREAK_DOWN_ID][2][1])) {
				$temp_delivery_date_str[$rows->PO_BREAK_DOWN_ID] .= ", STD-" . max($temp_delivery_date[$rows->PO_BREAK_DOWN_ID][2][1]);
			}
			if (max($temp_delivery_date[$rows->PO_BREAK_DOWN_ID][2][2])) {
				$temp_delivery_date_str[$rows->PO_BREAK_DOWN_ID] .= ", FTD-" . max($temp_delivery_date[$rows->PO_BREAK_DOWN_ID][2][2]);
			}
			//print_r($row->PO_BREAK_DOWN_ID);die;
			//print_r($temp_delivery_date[$rows->PO_BREAK_DOWN_ID][2][1]);die;
			$booking_numbers = implode(",", $booking_no_Po_wise[$rows->PO_BREAK_DOWN_ID]);
			$data_array[$i]["BOOKING_NUMBERS"] = ($booking_numbers) ? $booking_numbers : "";
			$data_array[$i]["FD_DELIVERY_DATE"] = $lib_delivery_date[$rows->PO_BREAK_DOWN_ID];
			$data_array[$i]["STD_DELIVERY_DATE"] =  max($temp_delivery_date[$rows->PO_BREAK_DOWN_ID][2][1]);
			$data_array[$i]["FTD_DELIVERY_DATE"] = max($temp_delivery_date[$rows->PO_BREAK_DOWN_ID][2][2]);
			$data_array[$i]["CLIENT_ID"] = $rows->CLIENT_ID * 1;
			$data_array[$i]["CLIENT_NAME"] = $buyer_arr[$rows->CLIENT_ID];
			$data_array[$i]["YEAR"] = $rows->YEAR;
			if (isset($buyer_arr[$rows->BUYER_NAME])) {
				$data_array[$i]["BUYER_NAME"] = $buyer_arr[$rows->BUYER_NAME];
			} else {
				$data_array[$i]["BUYER_NAME"] = 0;
			}
			$data_array[$i]["BUYER_ID"] = $rows->BUYER_NAME;
			$data_array[$i]["STYLE_REF_NO"] = $rows->STYLE_REF_NO;
			$data_array[$i]["INTERNAL_REF"] = $rows->GROUPING;
			$data_array[$i]["JOB_QUANTITY"] = $rows->JOB_QUANTITY + $JOB_QTY;
			$data_array[$i]["PO_NUMBER"] = $rows->PO_NUMBER;
			$data_array[$i]["PACK_HANDOVER_DATE"] = date("d-m-Y", strtotime($rows->PACK_HANDOVER_DATE));

			$data_array[$i]["PO_BREAK_DOWN_ID"] = $rows->PO_BREAK_DOWN_ID;
			$data_array[$i]["PLAN_CUT_QNTY"] = $rows->PLAN_CUT_QNTY;

			$data_array[$i]["PO_QUANTITY"] = $rows->PO_QUANTITY;
			if (isset($is_confirmed_arr[$rows->IS_CONFIRMED])) {
				$data_array[$i]["ORDER_STATUS"] = $is_confirmed_arr[$rows->IS_CONFIRMED];
			} else {
				$data_array[$i]["ORDER_STATUS"] = "";
			}

			$data_array[$i]["ITEM_NAME"] = (!empty($garments_item[$setdata[0]])) ? $garments_item[$setdata[0]] : "";
			$data_array[$i]["ITEM_NUMBER_ID"] = (!empty($setdata[0])) ? $setdata[0] : "";
			$data_array[$i]["ITEM_QNTY"] = ($setdata[1] * $rows->PLAN_CUT) + $ITEM_QTY;

			$data_array[$i]["TNA_START_DATE"] = "01-01-1970";
			if (isset($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date'])) {
				$data_array[$i]["TNA_START_DATE"] = $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date'];
			}
			$data_array[$i]["TNA_FINISH_DATE"] = "01-01-1970";
			if (isset($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date'])) {
				$data_array[$i]["TNA_FINISH_DATE"] = $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date'];
			}
			$data_array[$i]["CUT_TNA_START_DATE"] = "01-01-1970";
			if (isset($tna_task_data[$rows->PO_BREAK_DOWN_ID]['cut_task_start_date'])) {
				$data_array[$i]["CUT_TNA_START_DATE"] = $tna_task_data[$rows->PO_BREAK_DOWN_ID]['cut_task_start_date'];
			}
			$data_array[$i]["CUT_TNA_FINISH_DATE"] = "01-01-1970";
			if (isset($tna_task_data[$rows->PO_BREAK_DOWN_ID]['cut_task_finish_date'])) {
				$data_array[$i]["CUT_TNA_FINISH_DATE"] = $tna_task_data[$rows->PO_BREAK_DOWN_ID]['cut_task_finish_date'];
			}

			if ($plan_level == 2) {
				$data_array[$i]["PRODUCTION_QNTY"] = $production_data_arr[$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID][$color_index] * 1;
			} else {
				$data_array[$i]["PRODUCTION_QNTY"] = $production_data_arr[$rows->PO_BREAK_DOWN_ID][$setdata[0]] * 1;
			}

			$data_array[$i]["PLAN_QNTY"] = $plan_qnty;
			//print_r($production_data_arr); die;


			$ProdBalforPlanqty = 0;
			if ($plan_qnty >= $data_array[$i]["PRODUCTION_QNTY"]) {
				$ProdBalforPlanqty = $plan_qnty - $data_array[$i]["PRODUCTION_QNTY"];
			}
			$blanceQty = $rows->PLAN_CUT - $data_array[$i]["PRODUCTION_QNTY"];
			//echo $ProdBalforPlanqty; die;
			if ($ProdBalforPlanqty > 0) {
				$YET_TO_PLAN = $blanceQty - $plan_qnty;
			} else {
				$YET_TO_PLAN = $blanceQty;
			}

			$yet_to_plan_v3 = $rows->PLAN_CUT_QNTY - ($data_array[$i]["PRODUCTION_QNTY"] + $plan_qnty);
			if ($yet_to_plan_v3 < 0) {
				$yet_to_plan_v3 = 0;
			}



			$data_array[$i]["YET_TO_PLAN"] = $yet_to_plan_v3;
			//$data_array[$i]["YET_TO_PLAN"] = ($rows->PLAN_CUT+$plan_before_production)-$plan_qnty;
			if ($by_pass == 1) {
				$data_array[$i]["SMV"] = number_format(($gsd_array[$rows->STYLE_REF_NO][$rows->GMTS_ITEM_ID]["SMV"] * 1), 2);
			} else {
				$data_array[$i]["SMV"] = number_format(($setdata[2] * 1), 2);
			}
			$data_array[$i]["PUB_SHIPMENT_DATE"] = date("d-m-Y", strtotime($rows->PUB_SHIPMENT_DATE));
			if ($plan_level == 2) {
				//$color_data = $this->db->query("select color_name from lib_color where id=$rows->COLOR_NUMBER_ID")->row();
				$data_array[$i]["COLOR_NUMBER"] = ($colorArr[$rows->COLOR_NUMBER_ID]) ? $colorArr[$rows->COLOR_NUMBER_ID] : "";
				$data_array[$i]["COLOR_NUMBER_ID"] = $rows->COLOR_NUMBER_ID;
			} else if ($plan_level == 3) {
				$size_data = $this->db->query("select SIZE_NAME from lib_size where ID=" . implode(',', $ROWS_SIZE_NUMBER_ID_ARR) . "")->row();
				$data_array[$i]["SIZE_NAME"] = "";
				if (isset($size_data->SIZE_NAME)) {
					$data_array[$i]["SIZE_NAME"] = $size_data->SIZE_NAME;
				}
				$data_array[$i]["SIZE_NUMBER_ID"] = implode(',', $ROWS_SIZE_NUMBER_ID_ARR);
			} else if ($plan_level == 4) {
				//$color_data = $this->db->query("select color_name from lib_color where id=$rows->COLOR_NUMBER_ID")->row();
				$size_data = $this->db->query("select SIZE_NAME from lib_size where ID=" . implode(',', $ROWS_SIZE_NUMBER_ID_ARR) . "")->row();
				$data_array[$i]["COLOR_NAME"] = ($colorArr[$rows->COLOR_NUMBER_ID]) ? $colorArr[$rows->COLOR_NUMBER_ID] : "";
				$data_array[$i]["COLOR_NUMBER_ID"] = $rows->COLOR_NUMBER_ID;
				$data_array[$i]["SIZE_NAME"] = "";
				if (isset($size_data->SIZE_NAME)) {
					$data_array[$i]["SIZE_NAME"] = $size_data->SIZE_NAME;
				}
				$data_array[$i]["SIZE_NUMBER_ID"] = implode(',', $ROWS_SIZE_NUMBER_ID_ARR);
			} else if ($plan_level == 5) {
				$data_array[$i]["COUNTRY_ID"] = $rows->COUNTRY_ID;
				$data_array[$i]["COUNTRY_NAME"] = $country_arr[$rows->COUNTRY_ID];
			} else if ($plan_level == 6) {
				$data_array[$i]["COUNTRY_ID"] = $rows->COUNTRY_ID;
				$data_array[$i]["COLOR_NUMBER_ID"] = $rows->COLOR_NUMBER_ID;
			} else if ($plan_level == 7) {
				$data_array[$i]["COUNTRY_ID"] = $rows->COUNTRY_ID;
				$data_array[$i]["COLOR_NUMBER_ID"] = $rows->COLOR_NUMBER_ID;
				$data_array[$i]["SIZE_NUMBER_ID"] = implode(',', $ROWS_SIZE_NUMBER_ID_ARR);
			} else if ($plan_level == 8) {
				$data_array[$i]["COUNTRY_ID"] = $rows->COUNTRY_ID;
				$data_array[$i]["SIZE_NUMBER_ID"] = implode(',', $ROWS_SIZE_NUMBER_ID_ARR);
			}

			$data_array[$i]["ORDER_COMPLEXITY"] = '1'; // = '1';//$rows->ORDER_COMPLEXITY; by Learning Curve or fixed method
			$style_id = $data_array[$i]["STYLE_REF_NO"];
			$item_id = $data_array[$i]["ITEM_NUMBER_ID"];
			$smv_qty_id = $data_array[$i]["SMV"];
			$order_qty_id = $data_array[$i]["PO_QUANTITY"];
			if (isset($gsd_array[$rows->STYLE_REF_NO][$rows->GMTS_ITEM_ID]["EFFICIENCY"])) {
				$data_array[$i]["EFFICIENCY"] = $gsd_array[$rows->STYLE_REF_NO][$rows->GMTS_ITEM_ID]["EFFICIENCY"];
			} else {
				$data_array[$i]["EFFICIENCY"] = "0";
			}

			$data_array[$i]["TARGET"] = "0";
			if (isset($gsd_array[$rows->STYLE_REF_NO][$rows->GMTS_ITEM_ID]["TARGET"])) {
				$data_array[$i]["TARGET"] = $gsd_array[$rows->STYLE_REF_NO][$rows->GMTS_ITEM_ID]["TARGET"];
			}

			$data_array[$i]["ALLOCATED_MP"] = "0";
			if (isset($gsd_array[$rows->STYLE_REF_NO][$rows->GMTS_ITEM_ID]["ALLOCATED_MP"])) {
				$data_array[$i]["ALLOCATED_MP"] = $gsd_array[$rows->STYLE_REF_NO][$rows->GMTS_ITEM_ID]["ALLOCATED_MP"];
			}

			if (isset($gsd_array[$rows->STYLE_REF_NO][$rows->GMTS_ITEM_ID]["RESOURCE_GSD"])) {
				$data_array[$i]["RESOURCE_GSD"] = implode(',', $gsd_array[$rows->STYLE_REF_NO][$rows->GMTS_ITEM_ID]["RESOURCE_GSD"]);
			} else {
				$data_array[$i]["RESOURCE_GSD"] = "0";
			}

			$gsd_smv_val = $data_array[$i]["SMV"];
			if (isset($gsd_array[$vals->STYLE_REF][$vals->GMTS_ITEM_ID]["WORKING_HOUR"])) {
				$data_array[$i]["WORKING_HOUR"] = $gsd_array[$vals->STYLE_REF][$vals->GMTS_ITEM_ID]["WORKING_HOUR"];
			} else {
				$data_array[$i]["WORKING_HOUR"] = "0";
			}

			if (isset($emb_table_arr[$rows->JOB_ID])) {
				$data_array[$i]["EMBLISHMENT"] = implode(',', $emb_table_arr[$rows->JOB_ID]);
			} else {
				$data_array[$i]["EMBLISHMENT"] = "";
			}
			//print_r($emb_table_arr[$rows->JOB_ID]);die;
			if (!$order_qty_id) {
				$order_qty_id = 0;
			}

			if (!$gsd_smv_val) {
				$gsd_smv_val = 0;
			}

			$slab = return_field_value("LEARNING_CUB_PERCENTAGE", "efficiency_percentage_slab", "company_id=$cbo_company_mst and status_active=1 and (SMV_LOWER_LIMIT<=$gsd_smv_val and SMV_UPPER_LIMIT>=$gsd_smv_val) and (ORDER_QTY_LOWER_LIMIT<= $order_qty_id and ORDER_QTY_UPPER_LIMIT>= $order_qty_id) ", "LEARNING_CUB_PERCENTAGE");
			//print_r("SELECT LEARNING_CUB_PERCENTAGE from efficiency_percentage_slab WHERE company_id=$cbo_company_mst and status_active=1 and (SMV_LOWER_LIMIT<=$gsd_smv_val and SMV_UPPER_LIMIT>=$gsd_smv_val) and (ORDER_QTY_LOWER_LIMIT<= $order_qty_id and ORDER_QTY_UPPER_LIMIT>= $order_qty_id) ");die;
			$data_array[$i]["COMPLEXITY_LEVEL"] = "4";
			$data_array[$i]["ITEM_COMPLEXITY_LEVEL"] = $job_set_data[$rows->JOB_NO][$rows->ITEM_NUMBER_ID]['COMPLEXITY'];
			$data_array[$i]["FIRST_DAY_OUTPUT"] = $slab;
			$data_array[$i]["INCREMENT"] = '100';
			$data_array[$i]["COLOR_SIZE_ID"] = $rows->COLOR_SIZE_ID;
			$data_array[$i]["SET_DTLS_ID"] = $rows->SET_DTLS_ID;
			$data_array[$i]["DEPARTMENT_ID"] = $rows->PRODUCT_DEPT;
			$data_array[$i]["SUB_DEPARTMENT_ID"] = $rows->PRO_SUB_DEP;
			$data_array[$i]["SEASON_ID"] = $rows->SEASON_BUYER_WISE;
			$data_array[$i]["SEASON_YEAR"] = $rows->SEASON_YEAR;
			$data_array[$i]["BRAND_ID"] = $rows->BRAND_ID;
			//$data_array[$i]["BRAND_ID"] = $rows->BRAND_ID;

			$i++;
		}
		//print_r($data_array);die;
		$subcon_arr = [];
		if (($order_type == 0 || $order_type == 2) && $product_cate == 0 && empty($internal_ref) && empty($department) && empty($sub_department) && empty($brand) && empty($season) && empty($emb_filter)) 
		{


			$job_qnty = array();
			//print_r($query_subcon);die;
			// $subcon_arr = array();
			foreach ($table_subcon as $row) {
				//start unplanned sorting
				if ($plan_status == 1 and $planned_qnty[2][$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID] > 0) {
					continue;
				}

				if ($row->PLAN_CUT <= $subcon_production_qnty[$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID] and $plan_status == 1) {
					continue;
				}
				//end unplanned sorting

				//Partial Plan sorting 
				if ($row->PLAN_CUT <= $planned_qnty[2][$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID] && $plan_status == 2) {
					continue;
				}
				//Partial Plan sorting 2
				if ($planned_qnty[2][$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID] == 0 && $plan_status == 2) {
					continue;
				}
				//Partial Plan sorting 3 (here first value is yet_to_plan_v3)
				if ($row->PLAN_CUT - ($subcon_production_qnty[$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID] + $planned_qnty[2][$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID]) <= 0 && $plan_status == 2) {
					continue;
				}

				//Full Plan Sorting 
				if ($row->PLAN_CUT > $planned_qnty[2][$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID] && $plan_status == 3) {
					continue;
				}

				$job_qnty[$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID] += $row->ORDER_QUANTITY;

				$subcon_arr[] = [
					"ORDER_TYPE" => 2,
					"ID" => $row->ID,
					"COMPANY_ID" => $row->COMPANY_ID,
					"W_COMPANY_ID" => -1,
					"JOB_NO" => $row->SUBCON_JOB,
					"product_cate" => "0",
					"PO_BREAK_DOWN_ID" => $row->PO_BREAKDOWN_ID,
					"PO_NUMBER" => $row->ORDER_NO,
					"BUYER_ID" => $row->PARTY_ID,
					"BUYER_NAME" => $buyer_arr[$row->PARTY_ID],
					"CLIENT_ID" => $row->PARTY_ID,
					"CLIENT_NAME" => $buyer_arr[$row->PARTY_ID],
					"BOOKING_NUMBERS" => "",
					"FD_DELIVERY_DATE" => $row->MATERIAL_RECV_DATE,
					"STD_DELIVERY_DATE" => $row->MATERIAL_RECV_DATE,
					"FTD_DELIVERY_DATE" => $row->MATERIAL_RECV_DATE,
					"YEAR" => 0,
					"STYLE_REF_NO" => ($row->CUST_STYLE_REF) ? $row->CUST_STYLE_REF : "",
					"INTERNAL_REF" => "",
					"JOB_QUANTITY" => ($job_qnty[$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID]) ? $job_qnty[$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID] : 0,
					"PO_QUANTITY" => ($row->ORDER_QUANTITY) ? $row->ORDER_QUANTITY : 0,
					"PLAN_CUT_QNTY" => ($row->PLAN_CUT) ? $row->PLAN_CUT : 0,
					"ITEM_QNTY" => ($row->QNTY) ? $row->QNTY : 0,
					"PLAN_QNTY" => $planned_qnty[2][$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID] ? $planned_qnty[2][$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID] : 0,
					"PRODUCTION_QNTY" =>  $subcon_production_qnty[$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID] ? $subcon_production_qnty[$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID] : 0,
					"YET_TO_PLAN" => $row->PLAN_CUT - ($subcon_production_qnty[$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID] + $planned_qnty[2][$row->PO_BREAKDOWN_ID][$row->ITEM_ID][$row->COLOR_ID]),
					"SMV" => $row->SMV,
					"ITEM_NUMBER_ID" => $row->ITEM_ID,
					"ITEM_NAME" => $garments_item[$row->ITEM_ID],
					"COLOR_NUMBER_ID" => $row->COLOR_ID,
					"COLOR_NUMBER" => $colorArr[$row->COLOR_ID],
					"PUB_SHIPMENT_DATE" => date('d-m-Y', strtotime($row->DELIVERY_DATE)),
					"PACK_HANDOVER_DATE" => "01-01-1970",
					"ORDER_STATUS" => "",
					"TNA_START_DATE" => "01-01-1970",
					"TNA_FINISH_DATE" => "01-01-1970",
					"CUT_TNA_START_DATE" => "01-01-1970",
					"CUT_TNA_FINISH_DATE" => "01-01-1970",
					"ORDER_COMPLEXITY" => 0,
					"EFFICIENCY" => 0,
					"TARGET" => 0,
					"ALLOCATED_MP" => 0,
					"RESOURCE_GSD" => 0,
					"WORKING_HOUR" => 0,
					"COMPLEXITY_LEVEL" => 0,
					"ITEM_COMPLEXITY_LEVEL" => 0,
					"FIRST_DAY_OUTPUT" => 0,
					"INCREMENT" => 0,
					"COLOR_SIZE_ID" => 0,
					"SET_DTLS_ID" => 0,
					"DEPARTMENT_ID" => 0,
					"SUB_DEPARTMENT_ID" => 0,
					"SEASON_ID" => 0,
					"SEASON_YEAR" => 0,
					"BRAND_ID" => 0,
					"EMBLISHMENT" => "",


				];
			}
		}



		//print_r(5);die;
		$this->deleteRowByAttribute('GBL_TEMP_ENGINE', array('ENTRY_FORM' => 5002, "USER_ID" => $user_id, 'REF_FROM' => 55));
		$this->deleteRowByAttribute('GBL_TEMP_ENGINE', array('ENTRY_FORM' => 5002, "USER_ID" => $user_id, 'REF_FROM' => 56));
		$this->db->trans_commit();
		//print_r($subcon_arr);die;
		if ($order_type == 0) {
			return $data_array = array_merge($subcon_arr, $data_array);
		} elseif ($order_type == 1) {
			return $data_array;
		} elseif ($order_type == 2) {
			return $subcon_arr;
		}
	}






	function get_production_qnty_by_po_item($po_ids, $lineid = '', $daterange = '', $array_type = 1, $user_id, $txt_date_from, $txt_date_to)
	{
		//print_r(5);die;
		$sel_pos2 = array_chunk(array_unique(explode(",", $po_ids)), 999);
		//print_r($sel_pos2);die;
		$p = 1;
		$po_id_cond = "";
		foreach ($sel_pos2 as $job_no_process) {
			$values = implode(',', $job_no_process);
			if (!$values) {
				$values = 0;
			}

			if ($p == 1) {
				$po_id_cond = " and (a.po_break_down_id in(" . $values . ")";
			} else {
				$po_id_cond .= " or a.po_break_down_id in (" . $values . ")";
			}

			$p++;
		}
		// if ($this->db->dbdriver == 'mysqli') {
		// 	$get_actual_resource_allocation = "select group_concat(id) as ids from prod_resource_mst where  line_number like '$lineid' and is_deleted=0";
		// } else {
		// 	$get_actual_resource_allocation = "select listagg(id,',') within group (order by id) as ids from prod_resource_mst where  line_number like '$lineid' and is_deleted=0";
		// }


		//$resource_id = $this->db->query($get_actual_resource_allocation)->row();
		//print_r($get_actual_resource_allocation);die;
		//$lineid = $resource_id->IDS;

		$po_id_cond .= ")";
		//$line_cond = '';
		$date_cond = '';

		// if ($lineid != 0) {
		// 	$line_cond = " and a.SEWING_LINE in ($lineid) ";
		// }

		// if ($txt_date_from != '' && $txt_date_to != '') {
		// 	$date_cond = " and a.PRODUCTION_DATE between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "' ";
		// }

		$production_data_arr = array();
		$production_sql = "SELECT a.PO_BREAK_DOWN_ID,a.ITEM_NUMBER_ID,b.COLOR_SIZE_BREAK_DOWN_ID,b.PRODUCTION_QNTY PRODUCTION_QUANTITY, a.SEWING_LINE SEWING_LINE, a.PRODUCTION_DATE PRODUCTION_DATE,c.COLOR_NUMBER_ID from pro_garments_production_mst a,pro_garments_production_dtls b,WO_PO_COLOR_SIZE_BREAKDOWN c,GBL_TEMP_ENGINE d where a.id=b.mst_id and b.COLOR_SIZE_BREAK_DOWN_ID=c.id AND a.PO_BREAK_DOWN_ID = d.REF_VAL AND d.ENTRY_FORM =5002 AND d.USER_ID = $user_id AND d.REF_FROM = 55 and a.production_type=5 and a.status_active=1 and a.is_deleted=0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 order by a.PRODUCTION_DATE ASC";
		//echo $production_sql;die;



		$production_data = $this->db->query($production_sql)->result();

		//print_r($production_sql);die;
		//  print_r($production_sql);
		//  die;
		//print_r($production_sql);die;
		if ($array_type == 1) // PO ITEM level
		{
			foreach ($production_data as $row) {
				if (isset($production_data_arr['PO_ITEM_DATA'][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID])) {
					$production_data_arr['PO_ITEM_DATA'][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID] += $row->PRODUCTION_QUANTITY;
				} else {
					$production_data_arr['PO_ITEM_DATA'][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID] = $row->PRODUCTION_QUANTITY;
				}

				$production_data_arr['PO_ITEM_LINE_DATE_DATA'][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID][$row->SEWING_LINE][$row->COLOR_NUMBER_ID][$row->PRODUCTION_DATE] += $row->PRODUCTION_QUANTITY;
			}
			return $production_data_arr;
		} else if ($array_type == 2) // line date level
		{
			foreach ($production_data as $row) {


				if (isset($production_data_arr['PO_ITEM_DATA'][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID])) {
					$production_data_arr['PO_ITEM_DATA'][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID] += $row->PRODUCTION_QUANTITY;
				} else {
					$production_data_arr['PO_ITEM_DATA'][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID] = $row->PRODUCTION_QUANTITY;
				}
				$production_data_arr['PO_ITEM_LINE_DATE_DATA'][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID][$row->SEWING_LINE][$row->COLOR_NUMBER_ID][$row->PRODUCTION_DATE] += $row->PRODUCTION_QUANTITY;
				$production_data_arr['Pro_Qnty_of_PO_ITEM_CLR'][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID] += $row->PRODUCTION_QUANTITY;
			}
			return $production_data_arr;
		} else if ($array_type == 3) // line date level
		{
			$production_data_qnty = 0;
			$prd_date = "";
			foreach ($production_data as $row) {
				$production_data_qnty += $row->PRODUCTION_QUANTITY;
				$prd_date = $row->PRODUCTION_DATE;
				$production_data_arr['PO_ITEM_LINE_DATE_DATA'][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID][$row->SEWING_LINE][$row->COLOR_NUMBER_ID][$row->PRODUCTION_DATE] += $row->PRODUCTION_QUANTITY;
			}
			$production_data_arr['PO_ITEM_DATA'] = array("production_data_qnty" => $production_data_qnty, "prd_date" => $prd_date);

			return $production_data_arr;
		}
	}
}
