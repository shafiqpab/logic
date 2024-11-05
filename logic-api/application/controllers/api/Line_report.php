<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package    CodeIgniter - Android Apps
 * @category   AndroidApps
 * @author     Jahid Hasan <jahid@logicsoftbd.com>
 * @copyright  2017 Logic Software Ltd Development Group
 */
require APPPATH . '/libraries/REST_Controller.php';

class Line_report extends REST_Controller {

	function __construct() {
		parent::__construct();
		error_reporting(0);
		$this->load->model('planning/report/Available_capacity_hours_model');
	}

	
	
	function available_capacity_hours_get(){ 

		 $company_id = $this->get('company_id');
		 $location_id = $this->get('location_id');
		 $sew_floor = $this->get('sew_floor_id');
		 $sew_line = $this->get('sew_line_id');
		 $start_date = $this->get('start_date');
		 $end_date = $this->get('end_date');
		// $user_id = $this->get('user_id');
		
		if(!$company_id){
			return $this->response(array('errorMsg' => "company_id is required"), 404);
		}
		if(!$location_id){
			return $this->response(array('errorMsg' => "location_id is required"), 404);
		}
		// if(!$sew_floor){
		// 	return $this->response(array('errorMsg' => "sew_floor is required"), 404);
		// }
		// if(!$sew_line){
		// 	return $this->response(array('errorMsg' => "sew_line is required"), 404);
		// }
		if(!$start_date){
			return $this->response(array('errorMsg' => "start_date is required"), 404);
		}
		if(!$end_date){ 
			return $this->response(array('errorMsg' => "end_date is required"), 404);
		}
		
		
		$dataArr = [
			'company_id' => $company_id,
			'start_date' => $start_date,
			'location_id' => $location_id,
			'sew_floor' => $sew_floor,
			'sew_line' => $sew_line,
			'end_date' => $end_date
		];


		//('company_id':$company_id,$location_id,$sew_floor,$sew_line,$start_date,$end_date);

		$query_rows = $this->Available_capacity_hours_model->Available_capacity_hours($dataArr);

		$status = true;
		if ($query_rows == "capacity_false") {			

			$response = array(
				'status' => $status,
				'message' => "No data found in Capacity table",
				'resultset' => array(),
			);
		}elseif($query_rows == "planning_false"){

			$response = array(
				'status' => $status,
				'message' => "No data found in Planning table",
				'resultset' => array(),
			);
		}else{

			$response = array(
				'status' => $status,
				'message' => "success",
				'resultset' => $query_rows,
			);
		}
		

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Data not found'), 404);
		}
        
    }
}