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
		$this->load->model('merchandising/Fabric_booking_model');

		
		$data = $this->Fabric_booking_model->Fabric_booking();

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

		
		$data = $this->Trims_booking_model->Trims_booking();

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