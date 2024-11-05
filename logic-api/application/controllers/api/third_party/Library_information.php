<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package    CodeIgniter 
 * @category   Third party APIs
 * @author     Reza & Sadekur Rahman 
 * @copyright  2023 Logic Software Ltd Development Group
 */
require APPPATH . '/libraries/REST_Controller.php';



class Library_information extends REST_Controller {

	function __construct() {
		parent::__construct();
	}

	function buyer_info_get() {

		$this->load->model('third_party/Library_information_model');

		$buyer_id = $this->get("buyer_id");
		$buyer_name = $this->get("buyer_name");
		
		$data = $this->Library_information_model->buyer_info($buyer_id, $buyer_name);

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

	function model_info_get() {

		$this->load->model('third_party/Library_information_model');

		$source_id = $this->get("source_id");
		$source_name = $this->get("source_name");

		
		$data = $this->Library_information_model->model_info($source_id,$source_name);

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

	function order_info_get() {
		
		$this->load->model('third_party/Library_information_model');

		$source_id = $this->get("source_id");
		
		
		$data = $this->Library_information_model->order_info($source_id);

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

	function create_employee_POST() {

		$this->load->model('third_party/Library_information_model');

		//$response_arr='{"employee_code":"12366","employee_name":"Arif","date_of_birth":"12/4/2001","image":"my_img.jpg"}';
		
		$response_arr = file_get_contents("php://input");
		
		$data = $this->Library_information_model->create_employee($response_arr);

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

	function knit_order_image_GET() {

		$this->load->model('third_party/Library_information_model');
		$source_id = $this->get("source_id");
		
		$data = $this->Library_information_model->knit_order_image($source_id);

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