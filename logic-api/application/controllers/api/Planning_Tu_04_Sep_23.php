<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package    CodeIgniter - Android Apps
 * @category   AndroidApps
 * @author     Jahid Hasan <jahid@logicsoftbd.com>
 * @copyright  2017 Logic Software Ltd Development Group
 */
require APPPATH . '/libraries/REST_Controller.php';

class Planning extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('planning_model');
	}

	function login_get() {
		if (!$this->get('user_id')) {
			$this->response('UserID Is Required', 400);
		}
		if (!$this->get('pwd')) {
			$this->response('Password Is Required', 400);
		}


		$userInfo = $this->planning_model->login($this->get('user_id'), $this->get('pwd'));
		$status = true;
		if (empty($userInfo)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $userInfo,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function logout_get() {
		if (!$this->get('user_id')) {
			$this->response('UserID Is Required', 400);
		}
		$logout=$this->planning_model->logout($this->get('user_id'));
		
		$status = true;
		if (empty($logout)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $logout,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
		
	}



	function get_data_by_plan_get() {

		if ($this->get('plan_id')<1) {
			$this->response('Plan Is Required', 400);
		}

		$plan_info = $this->planning_model->get_plan_data_by_id($this->get('plan_id'));

		$status = true;
		if (empty($plan_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,

			'resultset' => $plan_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function planner_list_get() {
		$data = $this->planning_model->planner_data();

		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,

			'resultset' => $data,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function load_list_get() {


		$internal_ref = str_replace('_slash_','/',$this->get('internal_ref'));

		if (!$this->get('year')) {
			$this->response('Year Is Required', 400);
		}
		if (!$this->get('plan_level')) {
			$this->response('plan_level Is Required', 400);
		}
		$cbo_company_mst = $this->get('company_id');
		
		if($cbo_company_mst>0){$companyCon=" and company_name='$cbo_company_mst'";}
		$by_pass = return_field_value("WORK_STUDY_INTEGRATED", "variable_settings_production", "variable_list=9 $companyCon", "WORK_STUDY_INTEGRATED");
 		
		$userInfo = $this->planning_model->load_list($this->get('company_id'), $this->get('buyer_id'), $this->get('job_no'), $this->get('txt_date_from'), $this->get('txt_date_to'), $this->get('garments_nature'), $this->get('job_prefix'), $this->get('year'), $this->get('search_type'), $this->get('po_id'), $this->get('date_type'), $this->get('plan_level'), $this->get('style_ref'), $this->get('ignore_tna'), $this->get('order_status'), $this->get('po_break_down_id'), $this->get('set_dtls_id'), $this->get('color_size_id'), $this->get('plan_full'), $this->get('smv_range'), $this->get('item_name'), $internal_ref, $this->get('department'), $this->get('sub_department'), $this->get('brand'), $this->get('season'), $this->get('season_year'), $this->get('ignore_full_prod'), $this->get('smv_type'), $this->get('plan_status'));

		$status = true;
		if (empty($userInfo)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'by_pass' => $by_pass,
			'resultset' => $userInfo,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function job_data_details_get() {


		$internal_ref = str_replace('_slash_','/',$this->get('internal_ref'));

		//http://59.152.60.149:8998/platform_v3.5/logic-api/index.php/api/planning/job_data_details/company_id/6/buyer_id/65/plan_level/1/job_prefix/17/txt_date_from/01-01-2021/txt_date_to/31-03-2022/date_type/2/year/2022/po_id/PO-SET/style_ref/set/ignore_tna/1/order_status/1/search_type/4/smv_range/1-5/item_name/T-Shirt/internal_ref/tuhin90/department/1/sub_department/77/brand/31/season/73/season_year/2021
		
		/*		if (!$this->get('company_id')) {
			$this->response('company Is Required', 400);
		}
		*/
		// if (!$this->get('job_no') && !$this->get('style_ref') && !$this->get('po_id') && !$this->get('job_prefix') && !$this->get('po_break_down_id') && !$this->get('smv_range') && !$this->get('smv_range')    ) {
		// 	if (!$this->get('txt_date_from') && ! $internal_ref && !$this->get('item_name') && !$this->get('buyer_id') && !$this->get('buyer_id')  && !$this->get('season_year') ) {
		// 		$this->response('date Range Is Required', 400);
		// 	}

		// }
		//echo "hi";die;


		/*if (!$this->get('date_type')) {
			//$this->response('Date Type Is Required', 400);
		}*/
		if (!$this->get('year')) {
			$this->response('Year Is Required', 400);
		}
		if (!$this->get('plan_level')) {
			$this->response('plan_level Is Required', 400);
		}
		$cbo_company_mst = $this->get('company_id');
		
		if($cbo_company_mst>0){$companyCon=" and company_name='$cbo_company_mst'";}
		$by_pass = return_field_value("WORK_STUDY_INTEGRATED", "variable_settings_production", "variable_list=9 $companyCon", "WORK_STUDY_INTEGRATED");
 

		
		$userInfo = $this->planning_model->job_data_details($this->get('company_id'), $this->get('buyer_id'), $this->get('job_no'), $this->get('txt_date_from'), $this->get('txt_date_to'), $this->get('garments_nature'), $this->get('job_prefix'), $this->get('year'), $this->get('search_type'), $this->get('po_id'), $this->get('date_type'), $this->get('plan_level'), $this->get('style_ref'), $this->get('ignore_tna'), $this->get('order_status'), $this->get('po_break_down_id'), $this->get('set_dtls_id'), $this->get('color_size_id'), $this->get('plan_full'), $this->get('smv_range'), $this->get('item_name'), $internal_ref, $this->get('department'), $this->get('sub_department'), $this->get('brand'), $this->get('season'), $this->get('season_year'), $this->get('ignore_full_prod'), $this->get('smv_type'), $this->get('plan_status'));

		$status = true;
		if (empty($userInfo)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'by_pass' => $by_pass,
			'resultset' => $userInfo,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function plan_info_get(){

		if (!$this->get('txt_date_from')) {
			$this->response('From Date Is Required', 400);
		}
		$cbo_company_mst = $this->get('company_id');
		
		$by_pass = return_field_value("WORK_STUDY_INTEGRATED", "variable_settings_production", "company_name='$cbo_company_mst' and variable_list=9", "WORK_STUDY_INTEGRATED");

		$plan_info = $this->planning_model->plan_info($this->get('company_id'), $this->get('location_id'), $this->get('floor_id'), $this->get('txt_date_from'), $this->get('user_id'));
		//print_r($plan_info);die;
		$status = true;
		if (empty($plan_info)) {
			$plan_info=array();
			$plan_info['DTLS'] = array();
			$plan_info['LINE_WISE_PLAN_QTY'] = array();
			$plan_info['DATE_WISE_PLAN_QTY'] = array();
		}
		$response = array(
			'status' => $status,
			'by_pass' => $by_pass,
			'resultset' => $plan_info['DTLS'],
			'line_wise_plan_qty' => $plan_info['LINE_WISE_PLAN_QTY'],
			'date_wise_plan_qty' => $plan_info['DATE_WISE_PLAN_QTY']
		);
		
		$this->response($response, 200);

	}

	function item_wise_line_efficiency_info_get() {

		if (!$this->get('company_id')) {
			$this->response('Company Is Required', 400);
		}

		if (!$this->get('item_id')) {
			$this->response('Item Is Required', 400);
		}

		$item_info = $this->planning_model->get_item_wise_line_efficiency_info($this->get('company_id'), $this->get('item_id'));

		$status = true;
		if (empty($item_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $item_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function color_meaning_get(){

		$color_data = $this->planning_model->get_color_meaning();

		$status = true;
		if (empty($color_data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $color_data,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function sewing_trend_graph_info_get(){

		if (!$this->get('company_id') && !$this->get('serving_company')) {
			$this->response('Company or Serving Company Is Required', 400);
		}

		$graph_data = $this->planning_model->get_sewing_trend_graph_info($this->get('company_id'), $this->get('serving_company'), $this->get('location'), $this->get('floor'));

		$status = true;
		if (empty($graph_data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $graph_data,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function knit_trend_graph_info_get() {

		if (!$this->get('company_id') && !$this->get('serving_company')) {
			$this->response('Company or Serving Company Is Required', 400);
		}

		$graph_data = $this->planning_model->get_knit_trend_graph_info($this->get('company_id'), $this->get('serving_company'), $this->get('location'), $this->get('floor'), $this->get('type'));

		$status = true;
		if (empty($graph_data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $graph_data,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function po_details_info_get() {

/*		if (!$this->get('company_id')) {
			$this->response('Company Is Required', 400);
		}
*/
		if (!$this->get('po_id')) {
			$this->response('Po Is Required', 400);
		}

		$po_info = $this->planning_model->po_details_info($this->get('company_id'), $this->get('po_id'));

		$status = true;
		if (empty($po_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $po_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function daywise_plan_info_get() {

		if (!$this->get('company_id')) {
			$this->response('Company Is Required', 400);
		} else if (!$this->get('po_id')) {
			$this->response('PO ID Is Required', 400);
		} else {
			$plan_info = $this->planning_model->get_daywise_plan_data_info($this->get('company_id'), $this->get('po_id'), $this->get('txt_date_from'), $this->get('txt_date_to'));

			$status = true;
			if (empty($plan_info)) {
				$status = false;
			}
			$response = array(
				'status' => $status,
				'resultset' => $plan_info,
			);
			if ($response) {
				$this->response($response, 200);
			} else {
				$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
			}
		}
	}

	function po_vs_plan_info_get() {

		if (!$this->get('company_id')) {
			$this->response('Company Is Required', 400);
		} else {
			$plan_info = $this->planning_model->po_vs_plan_info_data($this->get('company_id'), $this->get('txt_date_from'), $this->get('txt_date_to'));

			$status = true;
			if (empty($plan_info)) {
				$status = false;
			}
			$response = array(
				'status' => $status,
				'resultset' => $plan_info,
			);
			if ($response) {
				$this->response($response, 200);
			} else {
				$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
			}
		}
	}

	function line_po_day_wise_plan_info_get() {

		if (!$this->get('company_id')) {
			$this->response('Company Is Required', 400);
		} else if (!$this->get('po_id')) {
			$this->response('PO ID Is Required', 400);
		} else if (!$this->get('line_id')) {
			$this->response('Line ID Is Required', 400);
		} else if (!$this->get('plan_id')) {
			$this->response('Plan ID Is Required', 400);
		} else {
			$plan_info = $this->planning_model->get_line_po_wise_plan_data_info($this->get('company_id'), $this->get('po_id'), $this->get('line_id'), $this->get('plan_id'), $this->get('location_id'));

			$status = true;
			if (empty($plan_info)) {
				$status = false;
			}
			$response = array(
				'status' => $status,
				'resultset' => $plan_info,
			);
			if ($response) {
				$this->response($response, 200);
			} else {
				$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
			}
		}
	}

	function line_info_get() {
		/*
		if (!$this->get('company_id')) {
			$this->response('Company Is Required', 400);
		}
		
			else if (!$this->get('location_id'))
			{
				$this->response('Location Is Required', 400);
			}
			else if (!$this->get('floor_id')) {
				$this->response('Floor Is Required', 400);
		
		else {*/
			$line_info = $this->planning_model->line_info($this->get('company_id'), $this->get('location_id'), $this->get('floor_id'), $this->get('user'), $this->get('date'));

			$status = true;
			if (empty($line_info)) {
				$status = false;
			}
			$response = array(
				'status' => $status,
				'resultset' => $line_info,
			);
			if ($response) {
				$this->response($response, 200);
			} else {
				$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
			}
			
		//}
		
	}

	function week_info_get() {

		/*if (!$this->get('company_id')) {
			$this->response('Company Is Required', 400);
		} else {*/
			$week_info = $this->planning_model->get_week_list_info($this->get('company_id'), $this->get('txt_date_from'), $this->get('txt_date_to'));

			$status = true;
			if (empty($week_info)) {
				$status = false;
			}
			$response = array(
				'status' => $status,
				'resultset' => $week_info,
			);
			if ($response) {
				$this->response($response, 200);
			} else {
				$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
			}
		//}
	}
	function tna_info_get() {

		if (!$this->get('po_id')) {
			$this->response('PO ID Is Required', 400);
		} else {
			$tna_info = $this->planning_model->get_tna_info($this->get('po_id'));

			$status = true;
			if (empty($tna_info)) {
				$status = false;
			}
			$response = array(
				'status' => $status,
				'resultset' => $tna_info,
			);
			if ($response) {
				$this->response($response, 200);
			} else {
				$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
			}
		}
	}
	/**
	 * [production_info_get description]
	 * @return [type] [description]
	 */
	function production_info_get() {
		if (!$this->get('company_id')) {
			$this->response('Company Is Required', 400);
		} else {
			$production_info = $this->planning_model->get_production_data_info($this->get('company_id'), $this->get('po_id'));

			$status = true;
			if (empty($production_info)) {
				$status = false;
			}
			$response = array(
				'status' => $status,
				'resultset' => $production_info,
			);
			if ($response) {
				$this->response($response, 200);
			} else {
				$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
			}
		}
	}

	/**
	 * [production_info_get description]
	 * @return [type] [description]
	 */
	function country_list_get() {
		$country_list = $this->planning_model->get_country_list($this->get('country_id'));

		$status = true;
		if (empty($country_list)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $country_list,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	/**
	 * [create_plan_get description]
	 * @return [type] [description]
	 */
	

	function create_plan_archive_get(){

		if (!$this->get('back_day')) {
			$plan_id_arr['BACK_DAY']=1;
		}
		else{
			$plan_id_arr['BACK_DAY']=$this->get('back_day');
		}
		//$plan_id_arr['BACK_DAY']=$this->get('back_day');

		//echo $plan_id_arr['BACK_DAY'];die;

		$archive_info = $this->planning_model->create_plan_data_archive($plan_id_arr);
		$status = true;
		if (empty($archive_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $archive_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}

	}

	function archive_info_get(){

		if (!$this->get('date')) {
			$this->response('Date Is Required', 400);
		}
 
		$plan_id_arr['DATE']=$this->get('date');
		$archive_info = $this->planning_model->get_archive_data($plan_id_arr);
		$status = true;
		if (empty($archive_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $archive_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}		
	}

	function pro_qnty_before_planning_get(){

		if (!$this->get('start_date')) {
			$this->response('start_date Is Required', 400);
		}
		if (!$this->get('end_date')) {
			$this->response('start_date Is Required', 400);
		}


		$save_info = $this->planning_model->get_pro_qnty_before_planning($this->get('start_date'),$this->get('end_date'));
		$status = true;
		
		$response = array(
			'status' => $status,
			'resultset' => $save_info,
		);
		$this->response($response, 200);
	}

	//function create_plan_GET(){
	function create_plan_POST(){
		//$response_arr='{"Status":true,"is_archive":1,"SewingPlanBoard":[{"COUNTRY_ID":0,"ID":0,"PO_BREAK_DOWN_ID":"0","LINE_ID":309,"PLAN_ID":617,"MERGED_PLAN_ID":0,"START_DATE":"18-06-2023","END_DATE":"24-06-2023","DURATION":6.500,"PLAN_QNTY":4838,"COMP_LEVEL":0,"FIRST_DAY_OUTPUT":"30,40","INCREMENT_QTY":0,"TERGET":1875,"NEXT_FIRST_DAY_OUTPUT":0,"NEXT_INCREMENT":0,"NEXT_TERGET":0,"LEARING_ITERATOR":4,"INSERTED_BY":0,"INSERT_DATE":"01-01-0001","UPDATED_BY":165,"UPDATE_DATE":"20-06-2023","STATUS_ACTIVE":1,"IS_DELETED":0,"COMPANY_ID":9,"PO_COMPANY_ID":9,"LOCATION_ID":11,"SMV":8.0,"ITEM_NUMBER_ID":"0","ITEM_NAME":"ABC","OFF_DAY_PLAN":2,"ORDER_COMPLEXITY":2,"SHIP_DATE":"10-02-2023","START_HOUR":1.0,"END_HOUR":0.0,"PLAN_LEVEL":2,"USE_LEARNING_CURVE":true,"SEQ_NO":1,"CLOSING_STATUS":0,"CLOSED_BY":0,"CLOSING_DATE":null,"CLOSING_NOTE":null,"RE_OPEN_DATE":null,"RE_OPENED_BY":null,"RE_OPEN_NOTE":null,"MERGE_TYPE":null,"MERGE_COMMENTS":null,"ARCHIVED":false,"RowState":"update","HALF":1.0,"NOTEFORSTRIP":null,"PO_LEVEL":0,"SET_DTLS_ID":0,"COLOR_SIZE_ID":null,"ALLOCATED_MP":25,"BYPASS_MP":0,"BUYER_NAME":"ABC","STYLE_REF_NO":"123","COLOR_NUMBER":"RED,BLACK","PO_NUMBER":"3","REMAINING_WORK_HOUR":4.0,"idForMPO":0,"isParked":false,"AUTO_TARGET":1,"BALANCE_QTY":2700,"LocationX":1164,"LocationY":60,"COLOR_NUMBER_ID":"7,1","Ref_Changed":0,"Value_Changed":0,"Color_Changed":0,"IS_FREEZE":0,"JOB_NO":"MFG-23-00028","ITEM_NUMBER_ID_H":"165,165","PO_QUANTITY_H":"2700,4838","PO_BREAK_DOWN_ID_H":"71371,71371","PO_NUMBER_H":"3,3","COLOR_NUMBER_ID_H":"1,7","COLOR_NUMBER_H":"BLACK,RED","POWISE_QUANTITY_H":"0,4838"}],"SewingPlanBoardDtls":[{"ID":0,"PLAN_ID":617,"PLAN_DATE":"18-06-2023","PLAN_QNTY":675.0,"RowState":"update","isOffDay":null,"workHour":9.0,"SMV":8.0,"Efficiency":0.4,"LineId":0},{"ID":0,"PLAN_ID":617,"PLAN_DATE":"19-06-2023","PLAN_QNTY":750.0,"RowState":"update","isOffDay":null,"workHour":10.0,"SMV":8.0,"Efficiency":0.4,"LineId":0},{"ID":0,"PLAN_ID":617,"PLAN_DATE":"20-06-2023","PLAN_QNTY":750.0,"RowState":"update","isOffDay":null,"workHour":10.0,"SMV":8.0,"Efficiency":0.4,"LineId":0},{"ID":0,"PLAN_ID":617,"PLAN_DATE":"21-06-2023","PLAN_QNTY":750.0,"RowState":"update","isOffDay":null,"workHour":10.0,"SMV":8.0,"Efficiency":0.4,"LineId":0},{"ID":0,"PLAN_ID":617,"PLAN_DATE":"22-06-2023","PLAN_QNTY":750.0,"RowState":"update","isOffDay":null,"workHour":10.0,"SMV":8.0,"Efficiency":0.4,"LineId":0},{"ID":0,"PLAN_ID":617,"PLAN_DATE":"23-06-2023","PLAN_QNTY":750.0,"RowState":"update","isOffDay":null,"workHour":10.0,"SMV":8.0,"Efficiency":0.4,"LineId":0},{"ID":0,"PLAN_ID":617,"PLAN_DATE":"24-06-2023","PLAN_QNTY":413.0,"RowState":"update","isOffDay":null,"workHour":6.0,"SMV":8.0,"Efficiency":0.4,"LineId":0}],"SewingPlanBoardPOWise":[{"ID":0,"PLAN_ID":617,"PO_BREAK_DOWN_ID":"71371","PLAN_QNTY":0,"ITEM_NUMBER_ID":"165","SIZE_NUMBER_ID":null,"COLOR_NUMBER_ID":"1","SMV":8.0,"COUNTRY_ID":0,"JOB_NO":"MFG-23-00028","RowState":"update","MPOId":0,"COLOR_NUMBER":"BLACK","MERGE_TYPE":null,"SET_DTLS_ID":0,"COLOR_SIZE_ID":null,"PO_NUMBER":"3","PUB_SHIPMENT_DATE":"10-02-2023","PRODUCTION_QTY":null},{"ID":0,"PLAN_ID":617,"PO_BREAK_DOWN_ID":"71371","PLAN_QNTY":4838,"ITEM_NUMBER_ID":"165","SIZE_NUMBER_ID":null,"COLOR_NUMBER_ID":"7","SMV":8.0,"COUNTRY_ID":0,"JOB_NO":"MFG-23-00028","RowState":"update","MPOId":0,"COLOR_NUMBER":"RED","MERGE_TYPE":null,"SET_DTLS_ID":0,"COLOR_SIZE_ID":null,"PO_NUMBER":"3","PUB_SHIPMENT_DATE":"10-02-2023","PRODUCTION_QTY":null}],"LineCapacity":[]}';
		$response_arr = file_get_contents("php://input");
		$plan_info = $this->planning_model->create_plan($response_arr);

		$status = true;
		if (empty($plan_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $plan_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}


	function allocation_data_get() {
		$fromDate = "";
		$toDate = "";
		if ($this->get('from_date') != "" && $this->validateDate($this->get('from_date')) && $this->validateDate($this->get('to_date')) && $this->get('to_date') != "") {
			$fromDate = $this->get('from_date');
			$toDate = $this->get('to_date');
		} else if ($this->validateDate($this->get('from_date')) && !$this->validateDate($this->get('to_date'))) {
			$fromDate = $this->get('from_date');
			$toDate = $this->get('from_date');
		} else if (!$this->validateDate($this->get('from_date')) && $this->validateDate($this->get('to_date'))) {
			$fromDate = $this->get('to_date');
			$toDate = $this->get('to_date');
		}

		if ($this->get('company_id') == "") {
			$this->response('Company Is Required', 400);
		}
		if ($this->get('location_id') == "") {
			$this->response('Location Is Required', 400);
		}

		if ($fromDate == "" && $toDate == "") {
			$this->response('Date Range Is Required', 400);
		}

		if ($this->get('type') == "") {
			$this->response('Type Is Required', 400);
		}
		if ($this->compareDate($fromDate, $toDate) === 0) {
			$this->response('To date is greater than From date', 400);
		}
		$data = $this->planning_model->get_allocation_data_info($fromDate, $toDate, $this->get('company_id'), $this->get('location_id'), $this->get('type'));
		//$this->response($data);

		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}

	}

	function capacity_data_get() {
		$fromDate = "";
		$toDate = "";
		if ($this->get('from_date') != "" && $this->validateDate($this->get('from_date')) && $this->validateDate($this->get('to_date')) && $this->get('to_date') != "") {
			$fromDate = $this->get('from_date');
			$toDate = $this->get('to_date');
		} else if ($this->validateDate($this->get('from_date')) && !$this->validateDate($this->get('to_date'))) {
			$fromDate = $this->get('from_date');
			$toDate = $this->get('from_date');
		} else if (!$this->validateDate($this->get('from_date')) && $this->validateDate($this->get('to_date'))) {
			$fromDate = $this->get('to_date');
			$toDate = $this->get('to_date');
		}

		if ($this->get('company_id') == "") {
			$this->response('Company Is Required', 400);
		}
		if ($this->get('location_id') == "") {
			$this->response('Location Is Required', 400);
		}

		if ($fromDate == "" && $toDate == "") {
			$this->response('Date Range Is Required', 400);
		}

		if ($this->get('type') == "") {
			$this->response('Type Is Required', 400);
		}
		if ($this->compareDate($fromDate, $toDate) === 0) {
			$this->response('To date is greater than From date', 400);
		}
		$data = $this->planning_model->get_capacity_data_info($fromDate, $toDate, $this->get('company_id'), $this->get('location_id'), $this->get('type'));
		//$this->response($data);

		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}

	}

	function create_archive_post___off() {

		if (!$this->get('generate_date')) {
			$this->response('Date Is Required', 400);
		}

		$archive_data = $this->planning_model->get_archive_info($this->get('company'), $this->get('location'), $this->get('floor'), $this->get('generate_date'));

		$status = true;
		if (empty($archive_data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $archive_data,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function line_capacity_get() {

		if (!$this->get('from_date')) {
			$this->response('From Date Is Required', 400);
		}
		if (!$this->get('to_date')) {
			$this->response('To Date Is Required', 400);
		}

		$line_capacity_info = $this->planning_model->get_line_capacity_info($this->get('from_date'), $this->get('to_date'), $this->get('company_id'), $this->get('location_id'), $this->get('floor_id'));
		$status = true;
		if (empty($line_capacity_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $line_capacity_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}

	}

	function validateDate($date) {
		return (bool) strtotime($date);
	}
	function compareDate($fromDate, $toDate) {
		if (strtotime($fromDate) <= strtotime($toDate)) {
			return 1;
		} else {
			return 0;
		}
	}

	function efficiency_percentage_slab_get() {
		//http://192.168.11.20/platform/logic-api/index.php/api/planning/efficiency_percentage_slab/company_id/1/buyer_id/308/item_id/83

		$efficiency_percentage_slab_info = $this->planning_model->get_efficiency_percentage_slab_list_info($this->get('company_id'),$this->get('buyer_id'),$this->get('item_id'));
		$status = true;
		if (empty($efficiency_percentage_slab_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $efficiency_percentage_slab_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}

	}
	
	function po_tracking_get() {

		if (!$this->get('po_id')) {
			$this->response('PO Id Is Required', 400);
		}
		
		$efficiency_percentage_slab_info = $this->planning_model->po_tracking_list_info($this->get('po_id'));
		$status = true;
		if (empty($efficiency_percentage_slab_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $efficiency_percentage_slab_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}

	}
	
	
	function monthly_capacity_vs_allocated_order_get() {
		//http://192.168.11.23/platform-v3.1/logic-api/index.php/api/planning/monthly_capacity_vs_allocated_order/company_id/1/location_id/0/from_month_year/2-2020/to_month_year/2-2020
		
		$from_month_year=$this->get('from_month_year');
		$to_month_year=$this->get('to_month_year');
		

		if ($from_month_year == "" && $to_month_year == "") {
			$this->response('Month Range Is Required', 400);
		}

		
		$data = $this->planning_model->get_monthly_capacity_vs_allocated_order_info($from_month_year, $to_month_year, $this->get('company_id'), $this->get('location_id'));

		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}

	}
	
	function monthly_capacity_vs_plan_get() {
		//http://192.168.11.23/platform-v3.1/logic-api/index.php/api/planning/monthly_capacity_vs_plan/company_id/1/location_id/0/from_month_year/2-2020/to_month_year/2-2020
		
		$from_month_year=$this->get('from_month_year');
		$to_month_year=$this->get('to_month_year');
		

		if ($from_month_year == "" && $to_month_year == "") {
			$this->response('Month Range Is Required', 400);
		}

		
		$data = $this->planning_model->get_monthly_capacity_vs_plan_info($from_month_year, $to_month_year, $this->get('company_id'), $this->get('location_id'));

		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}

	}
	
	function monthly_plan_vs_booked_get() {
		//http://localhost/platform-v3.1/logic-api/index.php/api/planning/monthly_plan_vs_booked/company_id/1/location_id/0/from_month_year/2-2020/to_month_year/2-2020
		
		$from_month_year=$this->get('from_month_year');
		$to_month_year=$this->get('to_month_year');
		

		if ($from_month_year == "" && $to_month_year == "") {
			$this->response('Month Range Is Required', 400);
		}

		
		$data = $this->planning_model->get_monthly_plan_vs_booked_info($from_month_year, $to_month_year, $this->get('company_id'), $this->get('location_id'));

		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}

	}
	
	
	function monthly_plan_vs_booked_vs_capacity_get() {
//http://localhost/platform-v3.1/logic-api/index.php/api/planning/monthly_plan_vs_booked_vs_capacity/company_id/3/location_id/0/from_month_year/1-2020/to_month_year/2-2020		
		$from_month_year=$this->get('from_month_year');
		$to_month_year=$this->get('to_month_year');
		

		if ($from_month_year == "" && $to_month_year == "") {
			$this->response('Month Range Is Required', 400);
		}

		
		$data = $this->planning_model->get_monthly_plan_vs_booked_vs_capacity_info($from_month_year, $to_month_year, $this->get('company_id'), $this->get('location_id'));

		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}

	}
	
	
	
	
	function garments_item_get() {
		
		$data = $this->planning_model->get_garments_item_data();

		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}

	}
	
	function refresh_powise_get() {
		//echo "refresh_powise"; die;
		if (!$this->get('plan_id')) {
			$this->response('plan_id Is Required', 400);
		}
		
		$data = $this->planning_model->refresh_powise($this->get('plan_id'));

		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}

	}

	function powise_backup_get(){
		//print_r(5);die;
		$data = $this->planning_model->powise_backup($this->get('plan_id_from'),$this->get('plan_id_to'));

		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function monthly_plan_summary_get(){
		
		$start_date = $this->get('start_date');
		$end_date = $this->get('end_date');
		$company_id = $this->get('company_id');
		$user_id = $this->get('user_id');
		$location = $this->get('location');

		$data = $this->planning_model->monthly_plan_summary($start_date,$end_date,$company_id,$user_id,$location);

		$status = true;
		// if (empty($data)) {
		// 	$status = false;
		// }
		$response = array(
			'status' => $status,
			'resultset' => $data,
		);

		if ($data == 44) {
			//print_r(6);die;
			$this->response(
				array(
					'status' => true,
					'msg' => 'Already Approved',
					'resultset' => [],
			), 200);
			//$this->response($response, 200);
		}elseif($response) {
			//$this->response(array('errorMsg' => 'Error'), 404);
			$this->response($response, 200);
		}
	}


	function monthly_plan_summary_POST(){
		//$response_arr = '{"Status":true,"SNAPS":[{"Checked":true,"month_digi":9,"month":"September","year":2023,"quantity":26918,"sah":0.0,"sam":0.0,"no_of_lines":3,"avg_smv":10.67,"qnty_per_day":928.0,"days":29,"work_hours_per_day":0.0,"comments":"","Efficiency":0.0,"user_id":165,"location_id":11,"company_id":9,"approve_status":0,"Status":null}]}';
		$response_arr = file_get_contents("php://input");

		$data = $this->planning_model->monthly_plan_summary_post($response_arr);
		//print_r($data);die;
		$status = true;

		if($data==11){
			$response = array(
				'status' => $status,
				'msg' => "year not found in object",
				'resultset' => [],
			);
		}elseif($data==22){
			$response = array(
				'status' => $status,
				'msg' => "month not found in object",
				'resultset' => [],
			);
		}
		elseif($data==33){
			$response = array(
				'status' => $status,
				'msg' => "location not found in object",
				'resultset' => [],
			);
		}
		elseif($data==44){
			$response = array(
				'status' => $status,
				'msg' => "company not found in object",
				'resultset' => [],
			);
		}
		elseif($data==55){
			$response = array(
				'status' => $status,
				'msg' => "Full Approved",
				'resultset' => [],
			);
		}
		elseif($data==66){
			$response = array(
				'status' => $status,
				'msg' => "Partial Approved",
				'resultset' => [],
			);
		}
		elseif ($data==false) {
			$response = array(
				'status' => $status,
				'msg' => "failed",
				'resultset' => [],
			);
		}elseif($data==True){
			$response = array(
				'status' => $status,
				'msg' => "successfull",
				'resultset' => $data,
			);
		}else{
			$response = array(
				'status' => $status,
				'msg' => "unknown response",
				'resultset' => [],
			);
		}
		
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'custom error message'), 404);
		}
	}

	// function monthly_plan_data_get(){
		
	// 	$start_date = $this->get('start_date');
	// 	$end_date = $this->get('end_date');
	// 	$company_id = $this->get('company_id');
	// 	$user_id = $this->get('user_id');
	// 	$location = $this->get('location');

	// 	$data = $this->planning_model->monthly_plan_data($company_id,$location);

	// 	$status = true;
	// 	if (empty($data)) {
	// 		$status = false;
	// 	}
	// 	$response = array(
	// 		'status' => $status,
	// 		'resultset' => $data,
	// 	);
	// 	if ($response) {
	// 		$this->response($response, 200);
	// 	} else {
	// 		$this->response(array('errorMsg' => 'Error'), 404);
	// 	}
	// }

}
