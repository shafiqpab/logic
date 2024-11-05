<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package    CodeIgniter - Android Apps
 * @category   AndroidApps
 * @author     Jahid Hasan <jahid@logicsoftbd.com>
 * @copyright  2017 Logic Software Ltd Development Group
 */
require APPPATH . '/libraries/REST_Controller.php';

class Plan_delete_controller extends REST_Controller {

	function __construct() {
		parent::__construct();
		error_reporting(0);
		$this->load->model('planning/report/Plan_delete_model');
	}

	
	
	function plan_delete_get(){ 

		$plan_id = $this->get('plan_id');
		$user_id = $this->get('user_id');
		
		if(!$plan_id || !$user_id){
			return $this->response(array('errorMsg' => "user_id and plan_id is required"), 404);
		}
		
		$result = $this->Plan_delete_model->plan_delete($plan_id,$user_id);


		
		$response = array(
			'result' => $result,
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Data not found'), 404);
		}
        
    }
}