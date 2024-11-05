<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package    CodeIgniter 
 * @category   Third party APIs
 * @author     Reza & Sadekur Rahman 
 * @copyright  2023 Logic Software Ltd Development Group
 */
require APPPATH . '/libraries/REST_Controller.php';



class Dms extends REST_Controller {

	function __construct() {
		parent::__construct();
	}

    function purchase_requisition_details_get() {
        //print_r('5');die;
		$this->load->model('dms/purchase_requisition_details');

		$requ_no = $this->get("requ_no");
		$catg_id = $this->get("catg_id");
		
		$data = $this->purchase_requisition_details->purchase_requisition_details($requ_no,$catg_id);

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

    function purchase_order_details_get() {
        //print_r('5');die;
		$this->load->model('dms/purchase_order_details');

		$catg_id = $this->get("catg_id");
		$order_no = $this->get("order_no");
		$requ_id = $this->get("requ_id");
		$company_id = $this->get("company_id");
		
		$data = $this->purchase_order_details->purchase_order_details($catg_id, $order_no, $requ_id,$company_id);
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

	function purchase_order_details_fb_get() {
        //print_r('5');die;
		$this->load->model('dms/purchase_order_details_fb');

		$booking_no = $this->get("booking_no");
		$booking_ids = $this->get("booking_ids");
		
		$data = $this->purchase_order_details_fb->purchase_order_details_fb($booking_no,$booking_ids);

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

    function mrr_get() {
        //print_r('5');die;
		$this->load->model('dms/Mrr');

		$mrr = $this->get("mrr_no");
		$parchase_order = $this->get("parchase_order_ids");
		$pi_ids = $this->get("pi_ids");
		$catg_id = $this->get("catg_id");
		//print_r($parchase_order);die;
		$data = $this->Mrr->Mrr($catg_id,$mrr,$parchase_order,$pi_ids);

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

    function dms_in_out_bills_get() {
        //print_r('5');die;
		$this->load->model('dms/Dms_in_out_bills');

		$in_bill_no = $this->get("in_bill_no");
		$out_bill_no = $this->get("out_bill_no");
		
		$data = $this->Dms_in_out_bills->dms_in_out_bills($in_bill_no,$out_bill_no);

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

    function Pi_info_get() {
        //print_r('5');die;
		$this->load->model('dms/Pi_info');

		$catg_id = $this->get("catg_id");
		$pi_no = $this->get("pi_no");
		$work_order = $this->get("work_order");
		
		$data = $this->Pi_info->Pi_info($catg_id, $pi_no, $work_order);

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

	function lc_get() {
		$this->load->model('dms/Lc_model');

		$catg_id = $this->get("catg_id");
		$pi_no = $this->get("pi_no");
		$pi_id = $this->get("pi_id");
		$lc_number = $this->get("lc_number");
		
		$data = $this->Lc_model->Lc($catg_id,$pi_no,$lc_number,$pi_id);

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

	function item_category_list_get() {
        //print_r('5');die;
		$this->load->model('dms/Mrr');

		$catg_id = $this->get("catg_id");
		//print_r($parchase_order);die;
		$data = $this->Mrr->item_category_list($catg_id);

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