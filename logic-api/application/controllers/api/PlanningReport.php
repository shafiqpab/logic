<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package    CodeIgniter - Android Apps
 * @category   AndroidApps
 * @author     Jahid Hasan <jahid@logicsoftbd.com>
 * @copyright  2017 Logic Software Ltd Development Group
 */
require APPPATH . '/libraries/REST_Controller.php';

class PlanningReport extends REST_Controller {

	function __construct() {
		parent::__construct();
		error_reporting(0);
		$this->load->model('planning/report/Plan_consistent_report_model');
	}

	
	
	function plan_consistent_report_get(){

		//logic-api/index.php/api/PlanningReport/plan_consistent_report/company_id/9/job_no/0/gmts_item_id/0/date_type/2/start_date/1-1-2021/end_date/1-12-2023
		
		// $company_id = $this->get('company_id');
		// if($company_id==0){
		// 	$this->response(array('errorMsg' => 'Please select company'), 404);
		// }
		
		// $dataArr=array(
		// 	'COMPANY_ID' => $this->get('company_id'),
		// 	'JOB_NO' => $this->get('job_no'),
		// 	'BUYER_ID' => $this->get('buyer_id'),
		// 	'GMTS_ITEM_ID' => $this->get('gmts_item_id'),
		// 	'DATE_TYPE' => $this->get('date_type'),
		// 	'START_DATE' => $this->get('start_date')?date('d-M-Y',strtotime($this->get('start_date'))):'',
		// 	'END_DATE' => $this->get('end_date')?date('d-M-Y',strtotime($this->get('end_date'))):'',
		// 	'PLAN_STATUS' => $this->get('plan_status'),
		// 	'po' => $this->get('po'),
		// ); 

		$start_date = $this->get('start_date') ? date('d-M-Y',strtotime($this->get('start_date'))) : date('d-M-Y', strtotime('-6 Month', time()));
		$end_date = $this->get('end_date') ? date('d-M-Y',strtotime($this->get('end_date'))): date('d-M-Y');

		//print_r($dataArr);die;
 
		$rs = $this->Plan_consistent_report_model->get_plan_consistent_report($this->get('company_id'),$this->get('job_no'),$this->get('gmts_item_id'),$this->get('date_type'),$start_date,$end_date,$this->get('plan_status'),$this->get('buyer_id'),$this->get('po'));

		$response = array(
			'status' => true,
			'resultset' => $rs,
			'msg' => " ",
		);		
		$this->response($response, 200);
	}	
}
