<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package    CodeIgniter - Android Apps
 * @category   AndroidApps
 * @author     Jahid Hasan <jahid@logicsoftbd.com>
 * @copyright  2017 Logic Software Ltd Development Group
 */
require APPPATH . '/libraries/REST_Controller.php';

class Array_fnc extends REST_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('android/common/array_function');
	}

	function fabric_shade_get(){ 

		$barcode_info = $this->array_function->get_fabric_shade_array();
		$status = true;
		if (empty($barcode_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $barcode_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	function department_get(){ 

		$barcode_info = $this->array_function->get_department_array();
		$status = true;
		if (empty($barcode_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $barcode_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	function knit_defect_inchi_get() { 

		$barcode_info = $this->array_function->get_knit_finish_defect_inchi_array();
		$status = true;
		if (empty($barcode_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $barcode_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	function ovservation_knit_finish_defect_inchi_get() { 

		$barcode_info = $this->array_function->get_ovservation_knit_finish_defect_inchi_array();
		$status = true;
		if (empty($barcode_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $barcode_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	function knit_finish_qc_defect_get() { 

		$barcode_info = $this->array_function->get_knit_finish_qc_defect_array();
		$status = true;
		if (empty($barcode_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $barcode_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	function ovservation_knit_finish_qc_defect_get() { 

		$barcode_info = $this->array_function->get_ovservation_knit_finish_qc_defect_array();
		$status = true;
		if (empty($barcode_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $barcode_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	 
 }
