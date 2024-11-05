<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package    CodeIgniter 
 * @category   Third party APIs
 * @author     Reza & Sadekur Rahman 
 * @copyright  2023 Logic Software Ltd Development Group
 */
require APPPATH . '/libraries/REST_Controller.php';



class Lc_sc_details extends REST_Controller {

	function __construct() {
		parent::__construct();
	}

    function shipment_entry_get() {
        //print_r('5');die;
		$this->load->model('merchandising/lc_sc_details_model');

		// $buyer_id = $this->get("buyer_id");
		// $buyer_name = $this->get("buyer_name");
		
		$data = $this->lc_sc_details_model->lc_sc_details();

		$status = true;
		
		if (empty($data)) {
			$status = false;
			$data = array();
		}

		$response = array(
			'status' => $status,
			'resultset' => $data,
		);

		$this->response($response, 200);
	}

	function fabric_booking_get() {
        //print_r('5');die;
		$booking_no = $this->get("booking_no");
		$this->load->model('merchandising/Fabric_booking_model');

		
		$data = $this->Fabric_booking_model->Fabric_booking($booking_no);

		$status = true;
		
		if (empty($data)) {
			$status = false;
			$data = array();
		}

		$response = array(
			'status' => $status,
			'resultset' => $data,
		);

		$this->response($response, 200);
	}

	function trims_booking_get() {
        
		$this->load->model('merchandising/Trims_booking_model');
		$booking_no = $this->get("booking_no");
		//print_r($booking_no);die;
		
		$data = $this->Trims_booking_model->Trims_booking($booking_no);

		$status = true;
		
		if (empty($data)) {
			$status = false;
			$data = array();
		}

		$response = array(
			'status' => $status,
			'resultset' => $data,
		);

		$this->response($response, 200);
	}

	function trims_inventory_receipt_get() {
        
		$this->load->model('merchandising/Trims_inventory_receipt_model');

		if($booking_no = $this->get("booking_no")){
			//$this->response("git", 200);
		}else{
			if(!$start_date = $this->get("start_date")){
				$this->response("start date required", 200);
			}
	
			if(!$end_date = $this->get("end_date")){
				$this->response("end date required", 200);
			}
		}		 //3/16/2019
		//print_r($booking_no);die;
		
		$data = $this->Trims_inventory_receipt_model->Trims_inventory_receipt($start_date,$end_date,$booking_no);

		$status = true;
		
		if (empty($data)) {
			$status = false;
			$data = array();
		}

		$response = array(
			'status' => $status,
			'resultset' => $data,
		);

		$this->response($response, 200);
	}

	function fabric_inventory_receipt_get() {
        
		$this->load->model('merchandising/Fabric_inventory_receipt_model');

		if($booking_no = $this->get("booking_no")){
			//$this->response("git", 200);
		}else{
			if(!$start_date = $this->get("start_date")){
				$this->response("start date required", 200);
			}
	
			if(!$end_date = $this->get("end_date")){
				$this->response("end date required", 200);
			}
		}		 //3/16/2019
		//print_r($booking_no);die;
		
		$data = $this->Fabric_inventory_receipt_model->Fabric_inventory_receipt($start_date,$end_date,$booking_no);

		$status = true;
		
		if (empty($data)) {
			$status = false;
			$data = array();
		}

		$response = array(
			'status' => $status,
			'resultset' => $data,
		);

		$this->response($response, 200);
	}
}