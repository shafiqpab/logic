<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package    CodeIgniter - Android Apps
 * @category   AndroidApps
 * @author     Jahid Hasan <jahid@logicsoftbd.com>
 * @copyright  2017 Logic Software Ltd Development Group
 */
require APPPATH . '/libraries/REST_Controller.php';

class Android extends REST_Controller {


	function __construct() {
		parent::__construct();
		//$this->load->model('android_model');
	}

	function version_check_get() { 
		$this->load->model('android_model');
		$company_image = $this->android_model->get_company_image();
		
		$filename = "../ext_resource/smart_track/apk_version.txt";
		$myfile = fopen($filename, "r") or die("Unable to open file!");
		$file_content = explode("**", fread($myfile,filesize($filename)));
		fclose($myfile);
		$apk_path = "../ext_resource/smart_track/" . $file_content[1];
		$response['data'] = array(
			'status' => 'success',
			'app_name' => 'smart-track',
			'company_image' => $company_image,
			"version" => $file_content[0],
			"app_url" => $apk_path
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function version_check_post() { 
		
		$data = json_decode(file_get_contents("php://input"), true);
		$fileName  =  $_FILES['apk']['name'];
		$tempPath  =  $_FILES['apk']['tmp_name'];
		$version =  $_POST['version'];

		if(empty($fileName))
		{
			echo json_encode(array("message" => "APK not found", "status" => false));
		}
		else
		{
			$upload_path = '../ext_resource/smart_track/'; 
			$fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION)); 
				
			$valid_extensions = array('apk'); 
							
			if(in_array($fileExt, $valid_extensions))
			{
				if(move_uploaded_file($tempPath, $upload_path . $fileName))
				{
					$msg = array("message" => 'APK uploaded successfully.', "status" => true);
					$status = true;

					$file = $upload_path . "apk_version.txt";
					$contents = $version . "**" . $fileName;

					if (file_exists($upload_path . $fileName)) {
						file_put_contents($file, $contents);
					}
					else
					{
						$myfile = fopen($file, "a");
						fwrite($myfile, $contents);
						fclose($myfile);
					}
				}
				else
				{
					$msg = array("message" => 'APK upload failed.', "status" => false);
					$status = false;
				}
			}
			else
			{	
				$msg = array("message" => "Image extension is not valid. Allowed extensions are JPG, JPEG, PNG & GIF.", "status" => false);
				$status = false;
			}
		}

		
		$response['data'] = array(
			'status' => 'success',
			'app_name' => 'smart-track',
			"version" => $version,
			"app_url" => $upload_path . $fileName
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function version_check_report_get() { 
		$this->load->model('android_model');
		$company_image = $this->android_model->get_company_image();
		
		$filename = "../ext_resource/smart_track/apk_version_report.txt";
		$myfile = fopen($filename, "r") or die("Unable to open file!");
		$file_content = explode("**", fread($myfile,filesize($filename)));
		fclose($myfile);
		$apk_path = "../ext_resource/smart_track/" . $file_content[1];
		$response['data'] = array(
			'status' => 'success',
			'app_name' => 'smart-track',
			'company_image' => $company_image,
			"version" => $file_content[0],
			"app_url" => $apk_path
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function version_check_report_post() { 
		
		$data = json_decode(file_get_contents("php://input"), true);
		$fileName  =  $_FILES['apk']['name'];
		$tempPath  =  $_FILES['apk']['tmp_name'];
		$version =  $_POST['version'];

		if(empty($fileName))
		{
			echo json_encode(array("message" => "APK not found", "status" => false));
		}
		else
		{
			$upload_path = '../ext_resource/smart_track/'; 
			$fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION)); 
				
			$valid_extensions = array('apk'); 
							
			if(in_array($fileExt, $valid_extensions))
			{
				if(move_uploaded_file($tempPath, $upload_path . $fileName))
				{
					$msg = array("message" => 'APK uploaded successfully.', "status" => true);
					$status = true;

					$file = $upload_path . "apk_version_report.txt";
					$contents = $version . "**" . $fileName;

					if (file_exists($upload_path . $fileName)) {
						file_put_contents($file, $contents);
					}
					else
					{
						$myfile = fopen($file, "a");
						fwrite($myfile, $contents);
						fclose($myfile);
					}
				}
				else
				{
					$msg = array("message" => 'APK upload failed.', "status" => false);
					$status = false;
				}
			}
			else
			{	
				$msg = array("message" => "Image extension is not valid. Allowed extensions are JPG, JPEG, PNG & GIF.", "status" => false);
				$status = false;
			}
		}

		
		$response['data'] = array(
			'status' => 'success',
			'app_name' => 'smart-track',
			"version" => $version,
			"app_url" => $upload_path . $fileName
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function login_get() { 
		
		$this->load->model('android_model');
		if (!$this->get('user_id')) {
			$this->response('UserID Is Required', 400);
		}
		if (!$this->get('pwd')) {
			$this->response('Password Is Required', 400);
		}

		$userInfo = $this->android_model->login($this->get('user_id'), $this->get('pwd'), $this->get('device_id'),$this->get('fcm_token'),$this->get('device_type'));
		$status = true;
		if (empty($userInfo)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $userInfo
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function apps_login_get()
	{ 
		$this->load->model('android_model');
		if (!$this->get('phone'))
		{
			$this->response('Phone Number Is Required', 400);
		}


		$userInfo = $this->android_model->apps_login($this->get('phone'));
		$status = true;
		if (empty($userInfo))
		{
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $userInfo
			);
		if ($response) 
		{
			$this->response($response, 200);
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}


	
	function logout_get() {
		$this->load->model('android_model');
		if (!$this->get('user_id')) {
			$this->response('UserID Is Required', 400);
		}
		$this->android_model->logout( $this->get('user_id') );
	}
	//***********AKH********************
	
	function menu_details_app_get()
	{
		if (!$this->get('user_id')) {
			$this->response('UserID Is Required', 400);
		}

		$menu_info = $this->android_model->get_menu_by_app_privilege($this->get('user_id'));
		$status = true;
		if (empty($menu_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $menu_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	//*************AKH****End***************

	function menu_details_get() {
		$this->load->model('android_model'); 
		if (!$this->get('user_id')) {
			$this->response('UserID Is Required', 400);
		}
		 

		$menu_info = $this->android_model->menu_details_data($this->get('user_id'));
		$status = true;
		if (empty($menu_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $menu_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function task_status_get() 
	{
		$this->load->model('android_model');
		if (!$this->get('user_id')) {
			$this->response('UserID Is Required', 400);
		}
		
		$data = $this->android_model->task_details_data($this->get('user_id'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}


	function array_ref_data_get() 
	{
		$this->load->model('android_model');
		$data = $this->android_model->array_ref_data($this->get('buyer_id'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function company_and_source_list_get() 
	{
		
		$this->load->model('android_model');
		$data = $this->android_model->company_and_source_data();
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			//$this->response($response, 200);
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	 
	
	function company_wise_location_get() 
	{
		$this->load->model('android_model');
		if (!$this->get('company_id'))
		{
			$this->response('Company Is Required', 400);
		}

		$data = $this->android_model->company_wise_loc_data($this->get('company_id'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function shift_arr_get()
	{
		$shift_name = [1=>"A", 2=>"B", 3=>"C"];
		$shift_arr = array();
		$i=0;
		foreach ($shift_name as $key => $shift) {
			$shift_arr[$i]["SHIFT_ID"] = $key;
			$shift_arr[$i]["SHIFT_NAME"] = $shift;
			$i++;
		}
		$status = true;
		$response = array(
			'status' => $status,
			'resultset' => $shift_arr
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid Request'), 404);
		}
	}
	function machine_arr_get()
	{
		$this->load->model('android_model');
		$machine_info = $this->android_model->get_machine_arr();
		$status = true;
		if (empty($machine_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $machine_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid Request'), 404);
		}
	}

	function machine_arr_v1_get()
	{
		$machine_info = $this->android_model->get_machine_arr_v1();
		$status = true;
		if (empty($machine_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $machine_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid Request'), 404);
		}
	}

	function company_wise_location_v1_get()
	{
		$this->load->model('android_model');
		if (!$this->get('company_id'))
		{
			$this->response('Company Is Required', 400);
		}

		$data = $this->android_model->company_wise_loc_data_v1($this->get('company_id'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function location_wise_floor_v1_get()
	{	//print_r(1);die;
		$this->load->model('android_model');
		if (!$this->get('location_id'))
		{
			$this->response('Location Is Required', 400);
		}
		if (!$this->get('production_process'))
		{
			$this->response('Production Process Is Required', 400);
		}

		$data = $this->android_model->loc_wise_floor_data_v1($this->get('location_id'),$this->get('production_process'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function location_wise_floor_get() 
	{
		$this->load->model('android_model');
		if (!$this->get('location_id'))
		{
			$this->response('Location Is Required', 400);
		}

		$data = $this->android_model->loc_wise_floor_data($this->get('location_id'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	function sewing_line_v1_get()
	{
		$this->load->model('android_model');
		if (!$this->get('company_id'))
		{
			$this->response('Company Is Required', 400);
		}

		if (!$this->get('location_id'))
		{
			$this->response('Location Is Required', 400);
		}

		if (!$this->get('floor_id'))
		{
			$this->response('Floor Is Required', 400);
		}

		if (!$this->get('input_date'))
		{
			$this->response('Date Is Required', 400);
		}

		$data = $this->android_model->sewing_line_data_v1($this->get('company_id'),$this->get('location_id'),$this->get('floor_id'),$this->get('input_date'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}


	function sewing_line_get() 
	{
		$this->load->model('android_model');
		if (!$this->get('company_id'))
		{
			$this->response('Company Is Required', 400);
		}

		if (!$this->get('location_id'))
		{
			$this->response('Location Is Required', 400);
		}


		if (!$this->get('floor_id'))
		{
			$this->response('Floor Is Required', 400);
		}

		if (!$this->get('issue_date'))
		{
			$this->response('Issue date Is Required', 400);
		}

		$data = $this->android_model->sewing_line_data($this->get('company_id'),$this->get('location_id'),$this->get('floor_id'),$this->get('issue_date'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	
	function sewing_barcode_scan_get() 
	{
		$this->load->model('android_model');
		if (!$this->get('company_id'))
		{
			$this->response('Company Is Required', 400);
		}
		if (!$this->get('location'))
		{
			$this->response('Location Is Required', 400);
		}
		if (!$this->get('floor'))
		{
			$this->response('Floor Is Required', 400);
		}
		if (!$this->get('line'))
		{
			$this->response('Line Is Required', 400);
		}

		if (!$this->get('barcode'))
		{
			$this->response('Barcode Is Required', 400);
		}
		if (!$this->get('type'))
		{
			$this->response('Production Type Is Required', 400);
		}

		 

		$data = $this->android_model->sewing_barcode_data($this->get('company_id'),$this->get('location'),$this->get('floor'),$this->get('line'),$this->get('barcode'),$this->get('type'));
		//print_r($data);die;
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			//$this->response($response, 200);
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 

			
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	
	//new page for sabbir................
	function sewing_input_output_get() 
	{
		
		$this->load->model('android/knit_garments_production/sewing_line_input_output','sewing_line_input_output');
		
		if (!$this->get('company_id'))
		{
			$this->response('Company Is Required', 400);
		}

		if (!$this->get('barcode'))
		{
			$this->response('Barcode Is Required', 400);
		}
		if (!$this->get('type'))
		{
			$this->response('Production Type Is Required', 400);
		}


		$data = $this->sewing_line_input_output->sewing_input_output_data($this->get('company_id'),$this->get('barcode'),$this->get('type'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			//$this->response($response, 200);
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 

			
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	
	function defect_type_get() 
	{
		$this->load->model('android_model');
		if (!$this->get('defect_type_id'))
		{
			$this->response('Defect Type Is Required', 400);
		}

		if (!$this->get('entry_form'))
		{
			$this->response('Entry Form Is Required', 400);
		}
		 

		$data = $this->android_model->defect_type_data($this->get('defect_type_id'),$this->get('entry_form'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			//$this->response($response, 200);
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 

			
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	
	function home_data_get()
	{
		$this->load->model('android_model');
		if (!$this->get('company'))
		{
			$this->response('Company Is Required', 400);
		}
 
		if (!$this->get('location'))
		{
			$this->response('Location Is Required', 400);
		}

		if (!$this->get('floor'))
		{
			$this->response('Floor Is Required', 400);
		}

		if (!$this->get('line'))
		{
			$this->response('Line Is Required', 400);
		}
		 

		$data = $this->android_model->home_data($this->get('company'),$this->get('location'),$this->get('floor'),$this->get('line'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			//$this->response($response, 200);
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 

			
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	

	//sewing line input output save update...................................
	function save_update_sewing_input_output_post()
    {
		$this->load->model('android/knit_garments_production/sewing_line_input_output','sewing_line_input_output');
		//$response_arr='{"status":true,"mode":"save","production_type":12,"UPDATE_ID":0,"data":{"index":{"company_id":0,"location_id":18,"production_source":1,"serving_company":3,"floor_id":126,"sewing_line":167,"organic":"","user_id":1,"production_date":"23-11-2019","hour":"","remarks":"","txt_system_id":"","mac":"AC:AF:B9:5A:34:CE"},"list_data":[{"cut_no":"FAL-19-000001","bundle_no":"FAL-19-1-6","barcode_no":"19990000000838","order_id":34119,"item_id":1,"country_id":4,"color_id":2,"size_id":1,"color_size_id":301591,"qnty":8,"is_rescan":0,"color_type_id":0}]}}';  
		
		
		
		$response_arr = file_get_contents("php://input");
		//$this->android_model->writeFile('sewing_line_input_output',$response_arr);//write object history
		$save_info = $this->sewing_line_input_output->save_update_sewing_line_input_output($response_arr);

     	$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
    }
	

	//new page for sabbir after_line................
	function sewing_input_output_by_challan_get() 
	{
		//http://localhost/platform-v3.1/logic-api/index.php/api/android/sewing_input_output_by_challan/company_id/1/challan/FAL-SWI-20-00056/type/12
		
		$this->load->model('android/knit_garments_production/Sewing_line_input_output_by_challan','sewing_line_input_output_by_challan');
		
		if (!$this->get('company_id'))
		{
			$this->response('Company Is Required', 400);
		}

		if (!$this->get('challan'))
		{
			$this->response('Challan Is Required', 400);
		}
		if (!$this->get('type'))
		{
			$this->response('Production Type Is Required', 400);
		}


		$data = $this->sewing_line_input_output_by_challan->sewing_line_input_output_by_challan_data($this->get('company_id'),$this->get('challan'),$this->get('type'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			//$this->response($response, 200);
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 

			
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	function save_update_sewing_input_output_by_challan_post()
    {
		$this->load->model('android/knit_garments_production/Sewing_line_input_output_by_challan','sewing_line_input_output_by_challan');	
		$response_arr = file_get_contents("php://input");

		
		
		//$response_arr='{"status":true,"mode":"save","production_type":12,"UPDATE_ID":0,"data":{"index":{"company_id":1,"location_id":1,"production_source":1,"serving_company":1,"floor_id":92,"sewing_line":511,"organic":"","user_id":1,"production_date":"19-04-2020","hour":"","remarks":"","txt_system_id":"","mac":""},"list_data":[{"cut_no":"FAL-20-000077","bundle_no":"FAL-20-77-3","barcode_no":"20990000010970","order_id":"7","item_id":"83","country_id":"13","color_id":"7","size_id":"2","color_size_id":"332304","qnty":"12","is_rescan":"0","color_type_id":"0"}]}}';  
		
		writeFiles('sewing_line_inputoutput_by_challan',$response_arr);//write object history	
		$save_info = $this->sewing_line_input_output_by_challan->save_update_sewing_line_input_output_by_challan($response_arr);

     	$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
    }
	



	function barcode_details_get()//kniting
	{ 
		$this->load->model('android_model');
		if (!$this->get('code'))
		{
			$this->response('Barcode No Is Required', 400);
		}
		 

		$barcode_info = $this->android_model->barcode_details_data($this->get('code'),$this->get('type'));
		$status = true;$msg="";
		if (empty($barcode_info)) {
			$status = false;
			$msg ="Data Not Found";
			$barcode_info=null;
		}

		$barcode_info_arr = explode("**",$barcode_info);
		if($barcode_info_arr[0] =="delivery")
		{
			$status = false;
			$msg =$barcode_info_arr[1];
			$barcode_info=null;
		}

		$response = array(
			'status' => $status,
			'msg'	=> $msg,
			'data' => $barcode_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}


	function finish_barcode_details_get()
	{ 
		$this->load->model('android_model');
		 
		if (!$this->get('code'))
		{
			$this->response('Barcode No Is Required', 400);
		}
		 

		$barcode_info = $this->android_model->finish_barcode_data($this->get('code') );
		$status = true;
		$shade_msg="";
		if ($barcode_info =="Shade not matched") 
		{
			$status = false;
			$shade_msg =$barcode_info;
			$barcode_info="";
		}

		if (empty($barcode_info)) {
			$status = false;
		}

		if($shade_msg ==""){

			$response = array(
				'status' => $status,
				'shade_msg'=> $shade_msg,
				'data' => $barcode_info
			);
		}
		else
		{
			$response = array(
				'status' => $status,
				'shade_msg'=> $shade_msg,
			);
		}

		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
			//$this->response($response, 200);
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	
	//kniting with observation
	function observation_kniting_barcode_details_get()
	{ 
		//logic-api/index.php/api/android/observation_kniting_barcode_details/code/17020002989/type/2
		
		$this->load->model('android_model');
		if (!$this->get('code'))
		{
			$this->response('Barcode No Is Required', 400);
		}
		 

		$barcode_info = $this->android_model->observation_kniting_barcode_data($this->get('code'),$this->get('type'));
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
	
	function observation_finish_barcode_details_get()
	{ 
		$this->load->model('android_model'); 
		
		if (!$this->get('code'))
		{
			$this->response('Barcode No Is Required', 400);
		}


		$barcode_info = $this->android_model->observation_finish_barcode_data($this->get('code') );
		$status = true;
		if (empty($barcode_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $barcode_info
		);
		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	function observation_finish_batch_barcode_details_get()
	{ 
		 
		$this->load->model('android/knit_fabric_production/finish_fabric_production_entry','finish_fabric_production_entry');
		if (!$this->get('code'))
		{
			$this->response('Batch No Is Required', 400);
		}
		 

		$barcode_info = $this->finish_fabric_production_entry->observation_finish_batch_data($this->get('code'),$this->get('is_batch_dtls') );
		$status = true;
		if (empty($barcode_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $barcode_info
		);
		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	
	
	function machine_data_get()
	{ 
		$this->load->model('android_model'); 
		
		$machine_info = $this->android_model->machine_data();
		$status = true;
		if (empty($machine_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $machine_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}



	function user_wise_menu_get()
	{ 
		$this->load->model('android_model');
		if (!$this->get('user_id'))
		{
			$this->response('User Id Is Required', 400);
		}
		 

		$user_info = $this->android_model->user_wise_menu_data($this->get('user_id'));
		$status = true;
		if (empty($user_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $user_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}



	function barcode_report_get()
	{ 
		$this->load->model('android_model');
		if (!$this->get('code'))
		{
			$this->response('Barcode No Is Required', 400);
		}
		 

		$barcode_info = $this->android_model->barcode_report_data($this->get('code'));
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

	function tabwise_sewingline_get()
	{ 
		$this->load->model('android_model');
		if (!$this->get('mac'))
		{
			$this->response('MAC  Is Required', 400);
		}
		 

		$barcode_info = $this->android_model->tabwise_sewingline_data($this->get('mac'));
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

	function sewing_output_tab_configuration_variable_get()
	{
		$this->load->model('android_model');

		$variable_info = $this->android_model->sewing_output_tab_configuration_variable($this->get('company_id'));
		$status = true;
		if (empty($variable_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' 	 => $variable_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function tabwise_sewingline_style_item_get()
	{ 
		$this->load->model('android_model');		 
		if (!$this->get('line_id'))
		{
			$this->response('Line Id Is Required', 400);
		}

		$barcode_info = $this->android_model->tabwise_sewingline_style_item_data($this->get('company_id'), $this->get('location_id'), $this->get('floor_id'), $this->get('line_id'), $this->get('job_no'), $this->get('po_number'), $this->get('internal_ref'), $this->get('style_ref'));
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


     function create_tracking_info_post()
     {
     	$this->load->model('android_model');
		//$response_arr='{"status":true,"mode":"save","phone":"1674194142","latitude":"23.32","longitude":"9.36"}';
     	$response_arr = file_get_contents("php://input");
     	$save_info = $this->android_model->create_tracking($response_arr);

     	$status = true;
     	if (empty($save_info))
     	{
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     		);
     	if ($response) 
     	{
     		$this->response($response, 200);
     	} 
     	else 
     	{
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
     }
     
	 function create_tabwise_line_post()
     {
     	$this->load->model('android_model');
     	
		$response_arr = file_get_contents("php://input");
     	//$response_arr='{"status":true,"mode":"save","company_id":"3","location_id":"18","floor_id":"1029","mac":"32332233","sewing_line":"824", "job_id": 1,"job_no": 1, "po_break_down_id": 1,"po_number": 1,"item_number_id": 1,"country_id": 1, "operation_ids":"1142,1177,1150,1211"}';
     	$save_info = $this->android_model->create_tabwise_line($response_arr);

     	$status = true;
     	if (empty($save_info))
     	{
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     		);
     	if ($response) 
     	{
     		$this->response($response, 200);
     	} 
     	else 
     	{
     		$this->response(array('errorMsg' => 'Failed'), 404);
     	}
     }


	 //kniting for all.........................................
	 function create_qc_result_post(){
		 $this->load->model('android_model');
/*     	$response_arr='{"status":true,"mode":"update","UPDATE_ID":1024,"data":{"index":{"BARCODE_NO":19020000365,"BUYER_ID":65,"COMPANY_ID":1,"DTLS_ID":32740,"ROLL_MAINTAINED":1,"QC_DATE":"13-02-2019","ROLL_ID":56196,"ROLL_NO":9,"QC_NAME":"Logic","ROLL_INCH":"25","ROLL_KG":5,"ROLL_YDS":53.819389763779526,"TOTAL_PENALTY_POINT":"12","TOTAL_POINT":"32.1074","INSERTED_BY":1,"UPDATED_BY":1,"UPDATE_ID":1024,"REJECT_QNTY":"0.0","FABRIC_GRADE":"B","ROLL_STATUS":1,"COMMENTS":"  "},"list_data":[{"DEFECT_ID":1,"COUNT":3,"INCH_ID":5,"PENALTY":6},{"DEFECT_ID":5,"COUNT":3,"INCH_ID":2,"PENALTY":6}],"obs_list_data":[{"OBS_ID":1,"OBS_INCH":5,"OBS_DEPARTMENT":1},{"OBS_ID":2,"OBS_INCH":2,"OBS_DEPARTMENT":1},{"OBS_ID":3,"OBS_INCH":1,"OBS_DEPARTMENT":1},{"OBS_ID":4,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":5,"OBS_INCH":2,"OBS_DEPARTMENT":1},{"OBS_ID":6,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":7,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":8,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":9,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":10,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":11,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":12,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":13,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":14,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":15,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":16,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":17,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":18,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":19,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":20,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":21,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":22,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":23,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":24,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":25,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":26,"OBS_INCH":0,"OBS_DEPARTMENT":1}]}}
';*/
     	
		
		
		$response_arr = file_get_contents("php://input");
		$this->android_model->writeFile('kniting',$response_arr);//write object history

     	$save_info = $this->android_model->create_qc_result($response_arr);

     	$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
     }
     
	 function create_finish_qc_result_post(){
     	 
     	$this->load->model('android_model');
		$response_arr = file_get_contents("php://input");
     	//$response_arr= '{"status":true,"mode":"save","MST_ID":0,"PROD_ID":33813,"TRANS_ID":0,"DTLS_ID":0,"QC_MST_ID":0,"UPDATE_ID":0,"data":{"index":{"BARCODE_NO":"21020004681","BATCH_ID":14665,"BATCH_NO":"Batch-556-999","BODY_PART_ID":227,"BOOKING_NO":0,"BOOKING_WITHOUT_ORDER":0,"COMPANY_ID":3,"SERVICE_COMPANY":1,"SOURCE":1,"SERVICE_LOCATION":11,"LOCATION":18,"MACHINE_ID":0,"SHIFT":0,"COLOR":"7","ROLL_WIDTH":"30","ROLL_WEIGHT":"35","ROLL_LENGTH":"20","CONS_COMP":"[Body + Sleeve Placket + Collar ]","DETER_ID":769,"DIA":"0","DIA_TYPE":1,"GSM":"200","IS_SALES_ID":0,"ORDER_ID":56400,"QC_PASS_QTY":34,"REJECT_QTY":"1","ROLL_ID":139196,"ROLL_NO":"7","ROLL_WGT":34,"WGT_LOST":"5.0","RECEIVE_DATE":"21-04-2022","COMMENTS":"","INSERTED_BY":1,"UPDATED_BY":1,"ROLL_STATUS":1,"TOTAL_PENALTY_POINT":"4","TOTAL_POINT":"24.0000","FABRIC_GRADE":"A"},"list_data":[{"DEFECT_ID":55,"COUNT":1,"INCH_ID":5,"PENALTY":2},{"DEFECT_ID":35,"COUNT":1,"INCH_ID":1,"PENALTY":1},{"DEFECT_ID":40,"COUNT":1,"INCH_ID":1,"PENALTY":1}]}}';
     	
		
		
		$this->android_model->writeFile('finish_qc',$response_arr);//write object history
		
		$save_info = $this->android_model->create_finish_qc_result($response_arr);

     	$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
     }

	 
	 //save kniting with observation.........................................
	 function create_observation_kniting_qc_result_post(){
		 $this->load->model('android_model');
     /*	$response_arr='{"status":true,"mode":"update","UPDATE_ID":1183,"data":{"index":{"BARCODE_NO":19020005841,"BUYER_ID":171,"COMPANY_ID":6,"DTLS_ID":37053,"ROLL_MAINTAINED":1,"QC_DATE":"01-01-1970","ROLL_ID":79793,"ROLL_NO":33,"QC_NAME":"gyby","ROLL_INCH":"5","ROLL_KG":20,"ROLL_YDS":956.7891513560805,"TOTAL_PENALTY_POINT":"19","TOTAL_POINT":"14.2978","INSERTED_BY":1,"UPDATED_BY":1,"UPDATE_ID":1183,"REJECT_QNTY":"0.0","FABRIC_GRADE":"A","ROLL_STATUS":1,"COMMENTS":"8"},"list_data":[{"DEFECT_ID":1,"COUNT":5,"INCH_ID":5,"PENALTY":10},{"DEFECT_ID":2,"COUNT":3,"INCH_ID":2,"PENALTY":6},{"DEFECT_ID":3,"COUNT":3,"INCH_ID":1,"PENALTY":3}],"obs_list_data":[{"OBS_ID":23,"OBS_INCH":2,"OBS_DEPARTMENT":1},{"OBS_ID":24,"OBS_INCH":2,"OBS_DEPARTMENT":1},{"OBS_ID":25,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":26,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":27,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":28,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":29,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":30,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":31,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":32,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":33,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":34,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":35,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":36,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":37,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":38,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":39,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":40,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":41,"OBS_INCH":0,"OBS_DEPARTMENT":1},{"OBS_ID":42,"OBS_INCH":0,"OBS_DEPARTMENT":1}]}}';*/
		
		$response_arr = file_get_contents("php://input");
		$this->android_model->writeFile('kniting_with_observation',$response_arr);//write object history
		
		$save_info = $this->android_model->create_observation_kniting_qc_result($response_arr);

     	$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
     }

     function create_observation_finish_qc_result_post(){
		 $this->load->model('android_model');
     	 
     	$response_arr = file_get_contents("php://input");
     	
	/*	$response_arr='{"status":true,"mode":"update","MST_ID":36516,"PROD_ID":19715,"TRANS_ID":0,"DTLS_ID":7473,"QC_MST_ID":1239,"UPDATE_ID":0,"data":{"index":{"BARCODE_NO":"19020005841","BATCH_ID":7701,"BATCH_NO":"","BODY_PART_ID":14,"BOOKING_NO":0,"BOOKING_WITHOUT_ORDER":0,"COMPANY_ID":6,"SERVICE_COMPANY":1,"SOURCE":1,"SERVICE_LOCATION":11,"LOCATION":18,"MACHINE_ID":0,"SHIFT":0,"COLOR":"","ROLL_WIDTH":"58","ROLL_WEIGHT":"22","ROLL_LENGTH":"90.7300","CONS_COMP":"","DETER_ID":19,"DIA":"66","DIA_TYPE":0,"GSM":"180","IS_SALES_ID":0,"ORDER_ID":1093,"QC_PASS_QTY":"22","REJECT_QTY":"9","ROLL_ID":79793,"ROLL_NO":"33","ROLL_WGT":13,"RECEIVE_DATE":"01-OCT-19","COMMENTS":"","INSERTED_BY":1,"UPDATED_BY":1,"ROLL_STATUS":1,"TOTAL_PENALTY_POINT":"21","TOTAL_POINT":"14.3662","FABRIC_GRADE":"A"},"list_data":[{"DEFECT_ID":1,"COUNT":4,"INCH_ID":5,"PENALTY":8},{"DEFECT_ID":5,"COUNT":3,"INCH_ID":2,"PENALTY":6},{"DEFECT_ID":10,"COUNT":3,"INCH_ID":1,"PENALTY":3},{"DEFECT_ID":260,"COUNT":4,"INCH_ID":1,"PENALTY":4}],"obs_list_data":[{"OBS_ID":1,"OBS_INCH":2,"OBS_DEPARTMENT":2},{"OBS_ID":2,"OBS_INCH":2,"OBS_DEPARTMENT":1},{"OBS_ID":3,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":4,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":5,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":6,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":7,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":8,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":9,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":10,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":11,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":12,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":13,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":14,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":15,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":16,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":17,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":18,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":19,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":20,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":21,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":22,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":23,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":24,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":25,"OBS_INCH":0,"OBS_DEPARTMENT":0},{"OBS_ID":26,"OBS_INCH":6,"OBS_DEPARTMENT":4}]}}';*/
		
		
     	$save_info = $this->android_model->create_observation_finish_qc_result($response_arr);

     	$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
     }
     
	 function save_update_finish_fabric_qc_by_batch_post(){
		$this->load->model('android/knit_fabric_production/finish_fabric_production_entry','finish_fabric_production_entry');
     	 
     	$response_arr = file_get_contents("php://input");
     	
		/* $response_arr='{"status":true,"mode":"save","MST_ID":"59687","DTLS_ID":"15777","QC_MST_ID":"0","data":{"index":{"RECEIVE_NO":"UG-FFPE-22-00010","BATCH_ID":"15556","BATCH_NO":"TAB QC","BUYER_ID":"12","QC_NAME":"Test","ACTUAL_DIA":"60","ACTUAL_GSM":"120","ROLL_WT_KG":"1","ROLL_WT_YDS":"5.9800","REJECT_QTY":"3","GSM":"160","DIA":"60","FEBRIC_DES_ID":"117","MACHINE_ID":"1049","LENGTH_PERCENT":"","WIDTH_PERCENT":"","TWISTING_PERCENT":"","COMMENTS":"","INSERTED_BY":1,"TOTAL_PENALTY_POINT":"6","TOTAL_POINT":"3612.0401","FABRIC_GRADE":"A"},"DEFECT_LIST":[{"DEFECT_ID":1,"COUNT":2,"INCH_ID":5,"PENALTY":4},{"DEFECT_ID":105,"COUNT":2,"INCH_ID":1,"PENALTY":2}],"OBSERVATION_LIST":[{"OBS_ID":1,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":2,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":3,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":4,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":5,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":6,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":7,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":8,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":9,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":10,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":11,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":12,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":13,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":14,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":15,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":16,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":17,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":18,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":19,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":20,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":21,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":22,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":23,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":24,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":25,"OBS_INCH":"1","OBS_DEPARTMENT":0},{"OBS_ID":26,"OBS_INCH":"1","OBS_DEPARTMENT":0}]}}'; */
		
		$this->finish_fabric_production_entry->writeFile('finish_fabric_qc_by_batch',$response_arr);//write object history
     	
		
		
		$save_info = $this->finish_fabric_production_entry->save_update_finish_fabric_qc_by_batch($response_arr);

     	$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
     }


	
	 
	 
	 function save_update_sewing_input_POST()
     {
		 $this->load->model('android_model');
		  
		// $response_arr='{"status":true,"mode":"save","production_type":5,"UPDATE_ID":0,"data":{"index":{"company_id":0,"shift_id":2,"location_id":84,"production_source":1,"serving_company":3,"floor_id":444,"sewing_line":538,"organic":"","user_id":1,"production_date":"20-09-2021","hour":"07-46","remarks":"test","txt_system_id":"","mac":"86:89:8B:78:C1:0E"},"list_data":[{"cut_no":"FAL-21-000056","bundle_no":"FAL-21-56-5","barcode_no":"21990000008171","order_id":47957,"item_id":6,"country_id":4,"color_id":7,"size_id":2,"color_size_id":410194,"qnty":50,"reject":2,"alter":0,"spot":0,"replace":0,"qc_qnty":51,"is_rescan":0,"color_type_id":0}]}}'; 

		//$response_arr='{"status":true,"mode":"save","production_type":5,"UPDATE_ID":0,"data":{"index":{"company_id":1,"shift_id":1,"location_id":130,"production_source":1,"serving_company":1,"floor_id":584,"sewing_line":759,"organic":"","user_id":"374","production_date":"18-06-2023","hour":"12-51","remarks":"no","txt_system_id":"","mac":"AC:AF:B9:5A:34:CE"},"actual_reject":"106*1__108*1","actual_alter":"100*1__102*1","actual_spot":"","list_data":[{"cut_no":"FAL-23-000263","bundle_no":"FAL-23-263-9","barcode_no":"23990000048623","order_id":77171,"item_id":"179","country_id":"245","color_id":7,"size_id":3,"color_size_id":710193,"qnty":25,"reject":1,"alter":1,"spot":0,"replace":0,"qc_qnty":23,"is_rescan":0,"color_type_id":0,"sewing_input_line":759}]}}';   



		
		// if($this->input->cookie('remember_me',true) ==1){
		// 	$response = array(
		// 		'status' => true,
		// 		'resultset' => $resultset["status"] = "দুঃখিত! ডেটা সংরক্ষিত হয়নি । পুনরায় চেস্টা করুন।"
		// 	);
		// 	if ($response) {
		// 		$this->response($response, 200);
		// 	}
		// 	exit();	
		// }
		// $this->input->set_cookie(array('name'   => 'remember_me','value'  => 1, 'expire' => '3', 'secure' => TRUE));
	 

		$response_arr = file_get_contents("php://input");

		//$this->android_model->writeFile('sewing_output',$response_arr);//write object history
		$save_info = $this->android_model->save_update_sewing_input($response_arr);

     	$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
     }

	function save_update_sewing_input_gross_post()
    {
		 $this->load->model('android_model');
		  
		//$response_arr='{"status":true,"production_type":5,"mode":"save","UPDATE_ID":0,"data":{"index":{"company_id":9,"shift_id":1,"location_id":108,"production_source":1,"serving_company":9,"floor_id":682,"sewing_line":595,"organic":"","user_id":"165","production_date":"29-03-2023","hour":"16-31","remarks":"no","txt_system_id":"","mac":"AC:AF:B9:5A:34:CE"},"actual_reject":"","actual_alter":"","actual_spot":"","list_data":[{"order_id":74060,"item_id":196,"country_id":10,"color_id":12758,"size_id":29851,"color_size_id":673031,"qnty":1,"reject":0,"alter":0,"spot":0,"good":0,"rectified":1,"qc_qnty":0,"color_type_id":0,"sewing_input_line":595,"dtls_id":"628363"}]}}'; 

		// $response_arr='{"status":true,"mode":"save","production_type":5,"UPDATE_ID":0,"data":{"index":{"company_id":0,"shift_id":1,"location_id":18,"production_source":1,"serving_company":3,"floor_id":126,"sewing_line":190,"organic":"","user_id":1,"production_date":"09-06-2022","hour":"19-25","remarks":"no","txt_system_id":"","mac":"02:15:B2:00:00:00"},"actual_reject":"1*1__2*2__5*3__42*3","actual_alter":"","actual_spot":"1*1__2*3","list_data":[{"cut_no":"RpC-22-000012","bundle_no":"RpC-22-12-4","barcode_no":"22990000031352","order_id":57731,"item_id":2,"country_id":23,"color_id":89,"size_id":2,"color_size_id":495786,"qnty":25,"reject":0,"alter":0,"spot":0,"replace":0,"qc_qnty":25,"is_rescan":0,"color_type_id":1,"sewing_input_line":593,"dtls_id":628363}]}}';   

		$response_arr = file_get_contents("php://input");
		$this->android_model->writeFile('sewing_output',$response_arr);//write object history

		$save_info = $this->android_model->save_update_sewing_input_gross($response_arr);

     	$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
    } 

	function save_update_sewing_input_gross_rectified_post()
    {
		 $this->load->model('android_model');
		  
		//$response_arr='{"status":true,"production_type":5,"mode":"save","UPDATE_ID":0,"data":{"index":{"company_id":9,"shift_id":1,"location_id":108,"production_source":1,"serving_company":9,"floor_id":682,"sewing_line":595,"organic":"","user_id":"165","production_date":"29-03-2023","hour":"16-31","remarks":"no","txt_system_id":"","mac":"AC:AF:B9:5A:34:CE"},"actual_reject":"","actual_alter":"","actual_spot":"","list_data":[{"order_id":74060,"item_id":196,"country_id":10,"color_id":12758,"size_id":29851,"color_size_id":673031,"qnty":1,"reject":0,"alter":0,"spot":0,"good":0,"rectified":1,"qc_qnty":0,"color_type_id":0,"sewing_input_line":595,"dtls_id":"628363"}]}}'; 

		// $response_arr='{"status":true,"mode":"save","production_type":5,"UPDATE_ID":0,"data":{"index":{"company_id":0,"shift_id":1,"location_id":18,"production_source":1,"serving_company":3,"floor_id":126,"sewing_line":190,"organic":"","user_id":1,"production_date":"09-06-2022","hour":"19-25","remarks":"no","txt_system_id":"","mac":"02:15:B2:00:00:00"},"actual_reject":"1*1__2*2__5*3__42*3","actual_alter":"","actual_spot":"1*1__2*3","list_data":[{"cut_no":"RpC-22-000012","bundle_no":"RpC-22-12-4","barcode_no":"22990000031352","order_id":57731,"item_id":2,"country_id":23,"color_id":89,"size_id":2,"color_size_id":495786,"qnty":25,"reject":0,"alter":0,"spot":0,"replace":0,"qc_qnty":25,"is_rescan":0,"color_type_id":1,"sewing_input_line":593,"dtls_id":628363}]}}';   

		$response_arr = file_get_contents("php://input");
		$this->android_model->writeFile('sewing_output',$response_arr);//write object history

		$save_info = $this->android_model->save_update_sewing_input_gross_rectified($response_arr);

     	$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
    } 
	
	function save_update_sewing_input_gross_gmts_rectified_post()
    {
		 $this->load->model('android_model');
		  
		$response_arr = file_get_contents("php://input");
		$this->android_model->writeFile('sewing_output',$response_arr);//write object history

		$save_info = $this->android_model->save_update_sewing_input_gross_gmts_rectified($response_arr);

     	$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
    }
	
	//summary report api.................................................
	
	function exfactory_details_get()
	{ 
		$this->load->model('android_model');
		if (!$this->get('company_id'))
		{
			$this->response('Company ID Is Required', 400);
		}
		
		$exfactory_info = $this->android_model->get_exfactory_details($this->get('company_id'),$this->get('txt_date_from'),$this->get('txt_date_to'));
		
		$status = true;
		if (empty($exfactory_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $exfactory_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function pending_shipment_monitoring_get()
	{ 
		$this->load->model('android_model');
		
		if (!$this->get('company_id'))
		{
			$this->response('Company ID Is Required', 400);
		}
		if (!$this->get('demand_date'))
		{
			$this->response('Demand date is required', 400);
		}

		
		// if(!$this->get('txt_demand_date')){
		// 	$txt_demand_date = date("Y-m-d", time());
		// }
		//var_dump(date("Y", strtotime($this->get('demand_date'))));die;

		if (strtotime($this->get('demand_date')) < strtotime(date("Y", strtotime($this->get('demand_date'))) . "-06-30"))
			$start_date = (date("Y", strtotime($this->get('demand_date'))) - 1) . "-07-01";
		else
			$start_date = date("Y", strtotime($this->get('demand_date'))) . "-07-01";
		//echo $start_date;die;
		
		if(strtotime($this->get('demand_date')) >= strtotime($start_date)){
			$pending_shipment_info = $this->android_model->get_pending_shipment_monitoring_report($this->get('company_id'),$start_date, $this->get('demand_date'));
		}else{
			$pending_shipment_info = $this->android_model->get_pending_shipment_monitoring_report($this->get('company_id'),$this->get('demand_date'), $start_date);
		}
		
		$status = true;
		if (empty($pending_shipment_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $pending_shipment_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	
	function shipment_pending_get()
	{ 
		$this->load->model('android_model');
		if (!$this->get('company_id'))
		{
			$this->response('Company Is Required', 400);
		}
		
		$shipment_pending_info = $this->android_model->get_shipment_pending($this->get('company_id'),$this->get('year'),$this->get('date_category'));
		
		$status = true;
		if (empty($shipment_pending_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $shipment_pending_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	
	function shipment_schedule_management_get()
	{ 
		$this->load->model('android_model');
		if (!$this->get('company_id'))
		{
			$this->response('Company Is Required', 400);
		}
		
		if (!$this->get('date_category'))
		{
			$this->response('Date Category Is Required', 400);
		}
		
		if (!$this->get('start_date'))
		{
			$this->response('Start Date Is Required', 400);
		}
		
		if (!$this->get('end_date'))
		{
			$this->response('End Date Is Required', 400);
		}
		
		

		$shipment_pending_info = $this->android_model->get_shipment_schedule_management($this->get('company_id'),$this->get('date_category'),$this->get('start_date'),$this->get('end_date'));
		
		$status = true;
		if (empty($shipment_pending_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $shipment_pending_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	function consolidated_order_summery_get()
	{ 
		
		$this->load->model('android_model');
		if (!$this->get('company_id'))
		{
			$this->response('Company ID Is Required', 400);
		}
		if (!$this->get('from_date'))
		{
			$this->response('Date is Required', 400);
		}
		if (!$this->get('to_date'))
		{
			$this->response('Date is Required', 400);
		}
		
		if(!$this->get('date_type')){
			$date_type =1;
		}else{
			$date_type =$this->get('date_type');
		}
		
		$consolidated_summery_data = $this->android_model->get_consolidated_order_summery_data($this->get('company_id'),$this->get('from_date'),$this->get('to_date'),$date_type);
		
		
		$status = true;
		if (empty($consolidated_summery_data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $consolidated_summery_data
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	
	function sewing_pending_get()
	{ 
		$this->load->model('android/report/sewing_pending_report');
		if (!$this->get('company_id'))
		{
			$this->response('Company  Is Required', 400);
		}

		$barcode_info = $this->sewing_pending_report->get_sewing_pending($this->get('company_id'),$this->get('location_id'),$this->get('floor_id'),$this->get('line_id'),$this->get('start_date'),$this->get('end_date'));
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
	
	
	function daily_ex_factory_report_get()//AST Group
	{ 
		
		$this->load->model('android/report/daily_ex_factory_report');
		if (!$this->get('company_id'))
		{
			$this->response('Company  Is Required', 400);
		}

		$barcode_info = $this->daily_ex_factory_report->get_daily_ex_factory_report($this->get('company_id'),$this->get('start_date'),$this->get('end_date'));
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

	function pending_shipment_monitoring_report_get()//AST Group
	{		
		$this->load->model('android/report/pending_shipment_monitoring_report');
		
		$ship_monitoring_info = $this->pending_shipment_monitoring_report->get_pending_shipment_monitoring_report($this->get('company_id'),$this->get('start_date'),$this->get('end_date'));
		$status = true;
		if (empty($ship_monitoring_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $ship_monitoring_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}	
	
	function production_process_by_barcode_get()
	{		
		if (!$this->get('barcode'))
		{
			$this->response('Barcode  Is Required', 400);
		}
		
		$this->load->model('android/report/production_process_by_barcode_report');
		$ship_monitoring_info = $this->production_process_by_barcode_report->get_production_process_by_barcode_report($this->get('barcode'));
		$status = true;
		if (empty($ship_monitoring_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $ship_monitoring_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}	
	
	
	function monthly_capacity_vs_allocated_order_get()
	{										
		//http://192.168.11.20/platform/logic-api/index.php/api/android/monthly_capacity_vs_allocated_order/company_id/3/location_id/0/from_month_year/1-2020/to_month_year/1-2020

		
		$from_month_year=$this->get('from_month_year');
		$to_month_year=$this->get('to_month_year');

		if ($from_month_year == "" && $to_month_year == "") {
			$this->response('Month Range Is Required', 400);
		}
		
		
		$this->load->model('android/report/monthly_capacity_vs_allocated_order');
		$ship_monitoring_info = $this->monthly_capacity_vs_allocated_order->get_monthly_capacity_vs_allocated_order_info($from_month_year, $to_month_year, $this->get('company_id'), $this->get('location_id'));
		$status = true;
		if (empty($ship_monitoring_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $ship_monitoring_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	
	function monthly_capacity_vs_plan_get()
	{		

//http://192.168.11.20/platform/logic-api/index.php/api/android/monthly_capacity_vs_plan/company_id/3/location_id/0/from_month_year/1-2020/to_month_year/1-2020


		$from_month_year=$this->get('from_month_year');
		$to_month_year=$this->get('to_month_year');

		if ($from_month_year == "" && $to_month_year == "") {
			$this->response('Month Range Is Required', 400);
		}
		
		$this->load->model('android/report/monthly_capacity_vs_plan');
		$ship_monitoring_info = $this->monthly_capacity_vs_plan->get_monthly_capacity_vs_plan_info($from_month_year, $to_month_year, $this->get('company_id'), $this->get('location_id'));
		$status = true;
		if (empty($ship_monitoring_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $ship_monitoring_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}


	function monthly_plan_vs_booked_get()
	{
			//http://192.168.11.20/platform/logic-api/index.php/api/android/monthly_plan_vs_booked/company_id/3/location_id/0/from_month_year/1-2020/to_month_year/1-2020
	
		$from_month_year=$this->get('from_month_year');
		$to_month_year=$this->get('to_month_year');

		if ($from_month_year == "" && $to_month_year == "") {
			$this->response('Month Range Is Required', 400);
		}
		
		$this->load->model('android/report/monthly_plan_vs_booked');
		$ship_monitoring_info = $this->monthly_plan_vs_booked->get_monthly_plan_vs_booked_info($from_month_year, $to_month_year, $this->get('company_id'), $this->get('location_id'));
		$status = true;
		if (empty($ship_monitoring_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $ship_monitoring_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}


	function monthly_plan_vs_booked_vs_capacity_get()
	{		


//http://192.168.11.20/platform/logic-api/index.php/api/android/monthly_plan_vs_booked_vs_capacity/company_id/3/location_id/0/from_month_year/1-2020/to_month_year/1-2020

		
		$from_month_year=$this->get('from_month_year');
		$to_month_year=$this->get('to_month_year');

		if ($from_month_year == "" && $to_month_year == "") {
			$this->response('Month Range Is Required', 400);
		}
		
		$this->load->model('android/report/monthly_plan_vs_booked_vs_capacity');
		$ship_monitoring_info = $this->monthly_plan_vs_booked_vs_capacity->get_monthly_plan_vs_booked_vs_capacity_info($from_month_year, $to_month_year, $this->get('company_id'), $this->get('location_id'));
		$status = true;
		if (empty($ship_monitoring_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $ship_monitoring_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	
	
	
	
	//Approval API.............................
	
	function all_approval_get()
	{		

//http://platform-v3.1/logic-api/index.php/api/android/all_approval/user_id/1/company_id/0/page_id/410/app_id/0
//http://localhost/platform-v3.1/logic-api/index.php/api/android/all_approval/user_id/1/company_id/1/page_id/1056/app_id/0
		
		if(!$this->get('user_id')){$this->response('User Id  Is Required', 400);}
		
		$this->load->model('android/approval/all_approval');
		
		$data_info = $this->all_approval->get_all_approval($this->get('user_id'),$this->get('company_id'),$this->get('page_id'),$this->get('app_id'));
		$status = true;
		if (empty($data_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $data_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	

	function save_update_all_approval_post()
    {
		$this->load->model('android/approval/all_approval');
		$response_arr = file_get_contents("php://input");
		
 
//$response_arr='{"status":true,"mode":"save","UPDATE_ID":0,"data":{"index":{"company_id":"3","app_id":"14263","page_id":"1257","user_id":1}}}';  
 		
		
		//$this->android_model->writeFile('all_approval',$response_arr);//write object history
		$save_info = $this->all_approval->save_update_all_approval($response_arr);

     	$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
    }

	
	 //---------------------------
 	 function save_update_sewing_multy_qr_post()
     {
		 
		 $this->load->model('android_model');
		 
		 //$response_arr='{"status":true,"mode":"save","production_type":5,"UPDATE_ID":0,"data":{"index":{"company_id":0,"location_id":18,"production_source":1,"serving_company":1,"floor_id":126,"sewing_line":191,"organic":"","user_id":1,"production_date":"08-06-2020","hour":"","remarks":"","txt_system_id":"","mac":"AC:AF:B9:5A:34:CE"},"list_data":[{"date":"08-06-2020","barcode_no":"20990000011384"},{"date":"08-06-2020","barcode_no":"17990000000615"}]}}';  
		
		$response_arr = file_get_contents("php://input");
		$response_obj = json_decode($response_arr);

		$serving_company=$response_obj->data->index->serving_company;
		$production_type=$response_obj->production_type;
		
		
		
		foreach ($response_obj->data->list_data as $val) {
			$dataArr=$this->android_model->sewing_barcode_data($serving_company,$val->barcode_no,$production_type);
			
			//print_r($dataArr);die;
			$i=0;
			
			$status = false;
			if($dataArr[message_bng]==''){
				$customDataArr=array();
				$customDataArr['status']=true;
				$customDataArr['mode']='save';
				$customDataArr['production_type']=$response_obj->production_type;
				$customDataArr['UPDATE_ID']=0;
				$customDataArr['data']['index']['company_id']=$response_obj->data->index->company_id;
				$customDataArr['data']['index']['location_id']=$response_obj->data->index->location_id;
				$customDataArr['data']['index']['production_source']=$response_obj->data->index->production_source;
				$customDataArr['data']['index']['serving_company']=$response_obj->data->index->serving_company;
				$customDataArr['data']['index']['floor_id']=$response_obj->data->index->floor_id;
				$customDataArr['data']['index']['sewing_line']=$response_obj->data->index->sewing_line;
				$customDataArr['data']['index']['organic']=$response_obj->data->index->organic;
				$customDataArr['data']['index']['user_id']=$response_obj->data->index->user_id;
				$customDataArr['data']['index']['production_date']=$response_obj->data->index->production_date;
				$customDataArr['data']['index']['hour']=$response_obj->data->index->hour;
				$customDataArr['data']['index']['remarks']=$response_obj->data->index->remarks;
				$customDataArr['data']['index']['txt_system_id']=$response_obj->data->index->txt_system_id;
				$customDataArr['data']['index']['mac']=$response_obj->data->index->mac;
				
				
				$customDataArr['data']['list_data'][$i]['cut_no']=$dataArr[cut_no];
				$customDataArr['data']['list_data'][$i]['bundle_no']=$dataArr[bundle_no];
				$customDataArr['data']['list_data'][$i]['barcode_no']=$val->barcode_no;
				$customDataArr['data']['list_data'][$i]['order_id']=$dataArr[order_id];
				$customDataArr['data']['list_data'][$i]['item_id']=$dataArr[item_id];
				$customDataArr['data']['list_data'][$i]['country_id']=$dataArr[country_id];
				$customDataArr['data']['list_data'][$i]['color_id']=$dataArr[color_id];
				$customDataArr['data']['list_data'][$i]['size_id']=$dataArr[size_id];
				$customDataArr['data']['list_data'][$i]['color_size_id']=$dataArr[color_size_id];
				$customDataArr['data']['list_data'][$i]['qnty']=$dataArr[qty];
				$customDataArr['data']['list_data'][$i]['reject']=0;
				$customDataArr['data']['list_data'][$i]['alter']=0;
				$customDataArr['data']['list_data'][$i]['spot']=0;
				$customDataArr['data']['list_data'][$i]['replace']=0;
				$customDataArr['data']['list_data'][$i]['qc_qnty']=$dataArr[qty];
				$customDataArr['data']['list_data'][$i]['is_rescan']=0;
				$customDataArr['data']['list_data'][$i]['color_type_id']=$dataArr[color_type_id];
				$custom_response_arr=json_encode($customDataArr);
				$save_info = $this->android_model->save_update_sewing_input($custom_response_arr);
				
				if (!empty($save_info)) {
					$status = true;
				}

				
			}
			
			
		}
		
		

		// $response_arr='{"status":true,"mode":"save","production_type":5,"UPDATE_ID":0,"data":{"index":{"company_id":0,"location_id":1,"production_source":1,"serving_company":1,"floor_id":92,"sewing_line":511,"organic":"","user_id":1,"production_date":"27-01-2020","hour":"17-14","remarks":"","txt_system_id":"","mac":"AC:AF:B9:5A:34:CE"},"list_data":[{"cut_no":"FAL-20-000028","bundle_no":"FAL-20-28-3","barcode_no":"20990000003353","order_id":38406,"item_id":104,"country_id":12,"color_id":7,"size_id":1,"color_size_id":325242,"qnty":12,"reject":1,"alter":2,"spot":3,"replace":0,"qc_qnty":6,"is_rescan":0,"color_type_id":0}]}}';  

     	
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
     }
	
	
	function linking_input_output_by_barcode_get() 
	{
		$this->load->model('android_model');
		if (!$this->get('company_id'))
		{
			$this->response('Company Is Required', 400);
		}

		if (!$this->get('barcode'))
		{
			$this->response('Barcode Is Required', 400);
		}
		if (!$this->get('type'))
		{
			$this->response('Production Type Is Required', 400);
		}

		 

		$data = $this->android_model->linking_input_output_by_barcode_data($this->get('company_id'),$this->get('barcode'),$this->get('type'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			//$this->response($response, 200);
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 

			
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	function save_update_linking_input_output_post()
    {
		 $this->load->model('android_model');
		 //$response_arr='{"status":true,"mode":"save","production_type":55,"UPDATE_ID":0,"data":{"index":{"company_id":0,"location_id":85,"production_source":1,"serving_company":20,"floor_id":448,"sewing_line":508,"organic":"","user_id":0,"production_date":"04-01-2021","hour":"","remarks":"","txt_system_id":"","mac":"2E:B9:0D:FB:AE:20"},"list_data":[{"cut_no":"SSL-21-000001","bundle_no":"SSL-21-1-16","barcode_no":"21990000000693","order_id":44749,"item_id":146,"country_id":16,"color_id":7,"size_id":1,"color_size_id":381380,"qnty":6,"is_rescan":0,"color_type_id":0}]}}';  

		$response_arr = file_get_contents("php://input");
		$this->android_model->writeFile('linking_in_out',$response_arr);//write object history
		
		$save_info = $this->android_model->save_update_linking_input_output_data($response_arr);

     	$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
     }
	
	
	
	
	
	function shift_duration_data_get()
	{ 
		$this->load->model('android_model'); 
		
		$shift_duration_info = $this->android_model->shift_duration_data();
		$status = true;
		if (empty($shift_duration_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $shift_duration_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	
	function company_wise_floor_get() 
	{
		//echo "DSSSSSSSSSSSSSSSSSSS";die;
		$this->load->model('android_model');
		if (!$this->get('company_id'))
		{
			$this->response('Company Is Required', 400);
		}

		$data = $this->android_model->company_wise_floor_data($this->get('company_id'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid Floor'), 404);
		}
	}
	function company_floor_machine_get() 
	{
		$this->load->model('android_model');
		if (!$this->get('floor_id'))
		{
			$this->response('Floor Is Required', 400);
		}

		$data = $this->android_model->company_floor_wise_mc_data($this->get('floor_id'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid Floor'), 404);
		}
	}
 
	//============**********Slitting,Stentering,Compacting Finish Prod*******************======
	
	function company_wise_fin_floor_get() 
	{
		//echo "DSSSSSSSSSSSSSSSSSSS";die;
		$this->load->model('android_model');
		if (!$this->get('company_id'))
		{
			$this->response('Company Is Required', 400);
		}

		$data = $this->android_model->company_wise_fin_floor_data($this->get('company_id'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Floor not populated.'), 404);
		}
	}
	function company_floor_fin_machine_get() 
	{
		$this->load->model('android_model');
		if (!$this->get('floor_id'))
		{
			$this->response('Floor Is Required', 400);
		}

		$data = $this->android_model->company_floor_wise_fin_mc_data($this->get('floor_id'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Machine not populated.'), 404);
		}
	}
	
	//============**********Finish Prod*******************======
	function finish_prod_dtls_list_view_get()  //Finish Production
	{
		
		$this->load->model('android_model');
		$data = $this->android_model->finish_prod_dtls_list_view_data($this->get('batch_id'),$this->get('entry_form_no'));
	
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			//$this->response($response, 200);
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
		} else {
			$this->response(array('errorMsg' => 'Data does not populated'), 404);
		}
	}
	
	function finish_prod_company_defualt_data_get()  //Finish Production
	{
		
		$this->load->model('android_model');
		$data = $this->android_model->finish_prod_company_defualt_data($this->get('entry_form_no'));
	
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			//$this->response($response, 200);
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
		} else {
			$this->response(array('errorMsg' => 'Data does not populated'), 404);
		}
	}
	function fin_prod_batch_scan_data_list_get()  //Finish Prod Batch Scan
	{
		
		$this->load->model('android_model');
		$data = $this->android_model->fin_prod_batch_scan_data($this->get('batch_no'),$this->get('entry_form_no'));
	
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			//$this->response($response, 200);
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
		} else {
			$this->response(array('errorMsg' => 'Data does not populated'), 404);
		}
	}
	function save_update_fin_production_post()
     {
		 $this->load->model('android_model');//
	
		
		
		/*$response_arr='{"status":true, "mode":"save", "MST_ID":"0", "UPDATE_ID":"0", "data":{"index":{"BATCH_ID":"16646","BATCH_NO":"NAVY-07 AOP","TRIMS_WGT":"","COMPANY_ID":"3","SERVICE_COMPANY":"3","ENTRY_FORM_NO":"48","PRODUCTION_TYPE":"","PROCESS_ID":"17","NEXT_PROCESS_ID":"107","RESULT":"11","PRODUCTION_DATE":"26-08-2022","PROCESS_START_DATE":"26-08-2022","PROCESS_END_DATE":"26-08-2022","START_HOURS":"1","START_MINUTES":"1","START_MINUTES":"1","END_HOURS":"11","END_MINUTES":"10","SHIFT_NAME":"2","ADVANCED_PROD_QTY":"110","FLOOR":"3","MACHINE_NAME":"12","WIDTH_SHRINKAGE":"30","LENGTH_SHRINKAGE":"45","PINNING":"","FEED_IN":"1","STRETCH":"45","SPEED":"90","STEAM":"40","OVER_FEED":"","TEMPARATURE":"","CHEMICAL_NAME":"","IS_RE_DYEING":"0","REMARK":"","USER_ID":"1"}, "list_data":[{"CHECKED":1,"PROD_ID":55997,"FIN_DIA":"","ROLL_NO":89,"ROLL_ID":168487,"NO_OF_ROLL":"","BATCH_QNTY":25,"PROD_QTY":25,"BARCODE_NO":22020003210,"DIA_TYPE":1,"DIA_WIDTH":34,"GSM":160,"CONS_COMP":"Single Jersey,Cotton 100%, 160, 34"},{"CHECKED":1,"PROD_ID":55997,"FIN_DIA":"","ROLL_NO":91,"ROLL_ID":168489,"NO_OF_ROLL":"","BATCH_QNTY":15,"PROD_QTY":15,"BARCODE_NO":22020003212,"DIA_TYPE":1,"DIA_WIDTH":34,"GSM":160,"CONS_COMP":"Single Jersey,Cotton 100%, 160, 34"},{"CHECKED":1,"PROD_ID":55997,"FIN_DIA":"","ROLL_NO":90,"ROLL_ID":168488,"NO_OF_ROLL":"","BATCH_QNTY":10,"PROD_QTY":10,"BARCODE_NO":22020003211,"DIA_TYPE":1,"DIA_WIDTH":34,"GSM":160,"CONS_COMP":"Single Jersey,Cotton 100%, 160, 34"}  ] } }';*/
		 
		
			 
		
			// print_r($response_arr);die;
		
		 $response_arr = file_get_contents("php://input");
		$this->android_model->writeFile('fin_production',$response_arr);//write object history

		$save_info = $this->android_model->save_update_fin_production($response_arr);
		
		$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Data is not save successfully'), 404);
     	}
		  
		  
	 }
	
	//======================End Slitting,Stentering,Compacting Finish Prod============
	function dying_production_load_list_get()  //Prod Production
	{
		
		$this->load->model('android_model');
		$data = $this->android_model->dying_company_source_load_data();
	
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			//$this->response($response, 200);
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
		} else {
			$this->response(array('errorMsg' => 'Data does not populated'), 404);
		}
	}
	function save_update_dyeing_production_post()
     {
		 $this->load->model('android_model');
		 
		//$response_arr='{"status":true, "mode":"save", "MST_ID":"0", "UPDATE_ID":"0", "data":{"index":{"BATCH_ID":16216,"BATCH_NO":"RpC-BC-22-00124","FUNCTIONAL_NO":"", "EXTENTION_NO":"", "JOB_NO":"RpC-22-00346", "PO_NO":"101", "FILE_NO":"", "REF_NO":"", "BUYER":"", "COLOR_ID":"", "BATCH_TYPE":"", "LOADING":"1", "DYEING_TYPE":"1", "COMPANY":"17", "SERVICE_COMPANY":"17", "PROCESS_NAME":"31", "BTB_LTB":"2", "PROCESS_START_DATE":"2-07-2022", "END_HOURS":"22", "END_MINUTES":"25", "PRODUCTION_DATE":"2-07-2022", "PROCESS_END_DATE":"2-07-2022", "RESULT":"1", "SHIFT_NAME":"2", "WATER_FLOW":"333", "FLOOR":"357", "MACHINE_NAME":"1007", "MULTI_BATCH_LOADING":"1", "HOUR_LOAD_METER":"1", "FABRIC_TYPE":"1", "RESPONSIBILITY_DEPT":"2","USER_ID":"999" }, "list_data":[{"CHECKED":0,"PROD_ID":55001,"CONS_COMPS":"Single Jersey  Cotton 100%","GSM":160,"DIA_WIDTH":60,"FABRIC_TYPEE":"","FABRIC_TYPEE_ID":1,"ROLL_ID":"","BARCODE_NO":"","BATCH_QNTY":50,"BATCH_ROLLNO":"","PROD_QTY":50,"PROD_QTY_READONLY":0}] } }';
		
		/*$response_arr='{"status":true, "mode":"save", "MST_ID":"0", "UPDATE_ID":"0", "data":{"index":{"BATCH_ID":16071,"BATCH_NO":"RpC-BC-22-00106","FUNCTIONAL_NO":"55555", "EXTENTION_NO":"", "JOB_NO":"RpC-22-00346", "PO_NO":"101", "FILE_NO":"", "REF_NO":"", "BUYER":"", "COLOR_ID":"", "BATCH_TYPE":"", "LOADING":"1", "DYEING_TYPE":"1", "COMPANY":"17", "SERVICE_COMPANY":"17", "PROCESS_NAME":"31", "BTB_LTB":"2", "PROCESS_START_DATE":"22-06-2022", "END_HOURS":"22", "END_MINUTES":"25", "PRODUCTION_DATE":"22-06-2022", "PROCESS_END_DATE":"22-06-2022", "RESULT":"1", "SHIFT_NAME":"2", "WATER_FLOW":"333", "FLOOR":"44", "MACHINE_NAME":"21", "MULTI_BATCH_LOADING":"1", "HOUR_LOAD_METER":"1", "FABRIC_TYPE":"1", "RESPONSIBILITY_DEPT":"2","USER_ID":"1" }, "list_data":[{"PROD_ID":"33322", "CONS_COMPS":"Single Jersey Cotton 100%", "GSM":"233", "DIA_WIDTH":"75", "FABRIC_TYPEE":"", "FABRIC_TYPEE_ID":"1", "ROLL_ID":"165366", "BARCODE_NO":"22020002602", "BATCH_QNTY":"36", "BATCH_ROLLNO":"4", "PROD_QTY":"36", "PROD_QTY_READONLY":""}, {"PROD_ID":"33322","CONS_COMPS":"Single Jersey Cotton 100%", "GSM":"233", "DIA_WIDTH":"75", "FABRIC_TYPEE":"", "FABRIC_TYPEE_ID":"1", "ROLL_ID":"165363", "BARCODE_NO":"22020002599", "BATCH_QNTY":"50", "BATCH_ROLLNO":"1", "PROD_QTY":"50", "PROD_QTY_READONLY":""}, {"PROD_ID":"33322","CONS_COMPS":"Single Jersey Cotton 100%", "GSM":"45", "DIA_WIDTH":"75", "FABRIC_TYPEE":"", "FABRIC_TYPEE_ID":"1", "ROLL_ID":"165365", "BARCODE_NO":"22020002601", "BATCH_QNTY":"50", "BATCH_ROLLNO":"3", "PROD_QTY":"50", "PROD_QTY_READONLY":""}, {"PROD_ID":"33322","CONS_COMPS":"Single Jersey Cotton 100%", "GSM":"200", "DIA_WIDTH":"75", "FABRIC_TYPEE":"", "FABRIC_TYPEE_ID":"1", "ROLL_ID":"165364", "BARCODE_NO":"22020002600", "BATCH_QNTY":"50", "BATCH_ROLLNO":"2", "PROD_QTY":"50", "PROD_QTY_READONLY":""} ] } }';*/
		 //$response_arr='{"status":true, "mode":"save", "MST_ID":"0", "UPDATE_ID":"0", "data":{"index":{"BATCH_ID":16071,"BATCH_NO":"RpC-BC-22-00106","FUNCTIONAL_NO":"55555", "EXTENTION_NO":"", "JOB_NO":"RpC-22-00346", "PO_NO":"101", "FILE_NO":"", "REF_NO":"", "BUYER":"", "COLOR_ID":"", "BATCH_TYPE":"", "LOADING":"2", "DYEING_TYPE":"1", "COMPANY":"17", "SERVICE_COMPANY":"17", "PROCESS_NAME":"31", "BTB_LTB":"2", "PROCESS_START_DATE":"22-06-2022", "END_HOURS":"22", "END_MINUTES":"25", "PRODUCTION_DATE":"22-06-2022", "PROCESS_END_DATE":"22-06-2022", "RESULT":"1", "SHIFT_NAME":"2", "WATER_FLOW":"333", "FLOOR":"44", "MACHINE_NAME":"21", "MULTI_BATCH_LOADING":"1", "HOUR_LOAD_METER":"1", "FABRIC_TYPE":"1", "RESPONSIBILITY_DEPT":"2","USER_ID":"1" }, "list_data":[{"CHECKED": 1, "PROD_ID": 55980, "CONS_COMPS": "1X1 Rib Cotton 100%", "GSM": 200, "DIA_WIDTH": 55, "FABRIC_TYPEE": "", "FABRIC_TYPEE_ID": 1, "ROLL_ID": 166560, "BARCODE_NO": 22020002801, "BATCH_QNTY": 5, "BATCH_ROLLNO": 3, "PROD_QTY": 5, "PROD_QTY_READONLY": "readonly"}, {"CHECKED": 1, "PROD_ID": 55980, "CONS_COMPS": "1X1 Rib Cotton 100%", "GSM": 200, "DIA_WIDTH": 55, "FABRIC_TYPEE": "", "FABRIC_TYPEE_ID": 1, "ROLL_ID": 166562, "BARCODE_NO": 22020002803, "BATCH_QNTY": 9, "BATCH_ROLLNO": 5, "PROD_QTY": 9, "PROD_QTY_READONLY": "readonly"}, {"CHECKED": 1, "PROD_ID": 55980, "CONS_COMPS": "1X1 Rib Cotton 100%", "GSM": 200, "DIA_WIDTH": 55, "FABRIC_TYPEE": "", "FABRIC_TYPEE_ID": 1, "ROLL_ID": 166558, "BARCODE_NO": 22020002799, "BATCH_QNTY": 22, "BATCH_ROLLNO": 1, "PROD_QTY": 22, "PROD_QTY_READONLY": "readonly"}, {"CHECKED": 1, "PROD_ID": 55980, "CONS_COMPS": "1X1 Rib Cotton 100%", "GSM": 200, "DIA_WIDTH": 55, "FABRIC_TYPEE": "", "FABRIC_TYPEE_ID": 1, "ROLL_ID": 166559, "BARCODE_NO": 22020002800, "BATCH_QNTY": 4, "BATCH_ROLLNO": 2, "PROD_QTY": 4, "PROD_QTY_READONLY": "readonly"}, {"CHECKED": 1, "PROD_ID": 55980, "CONS_COMPS": "1X1 Rib Cotton 100%", "GSM": 200, "DIA_WIDTH": 55, "FABRIC_TYPEE": "", "FABRIC_TYPEE_ID": 1, "ROLL_ID": 166561, "BARCODE_NO": 22020002802, "BATCH_QNTY": 7, "BATCH_ROLLNO": 4, "PROD_QTY": 7, "PROD_QTY_READONLY": "readonly"}, {"CHECKED": 1, "PROD_ID": 55980, "CONS_COMPS": "1X1 Rib Cotton 100%", "GSM": 200, "DIA_WIDTH": 55, "FABRIC_TYPEE": "", "FABRIC_TYPEE_ID": 1, "ROLL_ID": 166563, "BARCODE_NO": 22020002804, "BATCH_QNTY": 7, "BATCH_ROLLNO": 6, "PROD_QTY": 7, "PROD_QTY_READONLY": "readonly"} ] } }';
		
		 $response_arr = file_get_contents("php://input");
		$this->android_model->writeFile('dyeing_production',$response_arr);//write object history

		$save_info = $this->android_model->save_update_dyeing_production($response_arr);
		
		$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Data is not save successfully'), 404);
     	}
		  
		  
	 }
	
	function dying_prod_batch_scan_list_get()  //Dying Prod Batch Scan
	{
		
		$this->load->model('android_model');
		$data = $this->android_model->dying_prod_batch_scan_load_data($this->get('batch_no'),$this->get('load_unload'),$this->get('dyeing_type_id'));
	
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			//$this->response($response, 200);
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
		} else {
			$this->response(array('errorMsg' => 'Data does not populated'), 404);
		}
	}
	function dying_prod_functional_batch_scan_list_get()  //Dying Prod functional Batch Scan 
	{
		
		$this->load->model('android_model');
		$data = $this->android_model->dying_prod_functional_batch_scan_data($this->get('load_unload'),$this->get('functional_no'));
	
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			//$this->response($response, 200);
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
		} else {
			$this->response(array('errorMsg' => 'Data does not populated'), 404);
		}
	}
	//================******Dyeing End********=================	
	
	//************Akh***************
	function roll_receive_post()
	{
		$this->load->model('android_model');
		//die();
		$response_arr = file_get_contents("php://input");

		//$response_arr = '{"resultset":{"MasterPart":{"COMPANY_ID":18,"DELEVERY_DATE":"29-NOV-22","DELIVERY_ID":10677,"KNITTING_COMPANY_ID":18,"KNITTING_SOURCE":"In-house","KNITTING_SOURCE_ID":1,"LOCATION_ID":107,"STORE_ID":442,"SYS_NUMBER":"LSL-GDSR-22-00093","SYS_NUMBER_PREFIX_NUM":93},"DtlsPart":[{"BARCODE_NO":"22020008170","BODY_PART_ID":31,"BODY_PART_NAME":"Back Flap","BOOKING_ID":14618,"BOOKING_NO":14618,"BOOKING_WITHOUT_ORDER":0,"BRAND_ID":2316,"BUYER_ID":107,"BUYER_NAME":"AT","COLOR_ID":14,"COLOR_NAME":"BLUE","COLOR_RANGE_ID":5,"COLOR_RANGE_NAME":"Average Color","COMPOSITION":"Cotton 100% ","CONSTRUCTION":"Single Jersey","DETER_ID":448,"DTLS_ID":57595,"FLOOR_ID":576,"GSM":250,"KNITTING_COMPANY":"Logic Software Ltd","MACHINE_NO_ID":1357,"PO_BREAKDOWN_ID":3527,"PO_NUMBER":"S054077A","PROD_ID":21534,"PRODUCTION_BASIS":2,"PRODUCTION_BASIS_NAME":"Knitting Plan","QNTY":15,"REJECT_QNTY":5,"ROLL_ID":186806,"ROLL_NO":6,"SHIFT_NAME":1,"STITCH_LENGTH":1322222.0,"UOM":12,"WIDTH":60,"YARN_COUNT":3.0,"YARN_LOT":"NTR-SNR"}]},"status":"true"}';

		$receive_info = $this->android_model->roll_receive($response_arr);
		$status = true;
		if (empty($receive_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $receive_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function swing_input_output_post()
	{
		$response_arr = file_get_contents("php://input");

		//$response_arr = '{"resultset":{"DtlsPart":[{"ALTER":1,"ALTER_STR":"","BUYER":"Sainsburys","BARCODE_NO":"20970000125089","BUNDLE_NO":"AKDL-20-3214-2","COLOR":"NAVY","COLOR_ID":56,"COLOR_SIZE_ID":470710,"COLOR_TYPE_ID":0,"COUNTRY":"United Kingdom","COUNTRY_ID":260,"CUT_NO":"AKDL-20-003214","COMPANY_ID":1,"FLOOR_ID":4,"IS_RESCAN":0,"ITEM":"Trouser","ITEM_ID":22,"JOB_NO":"1274","LOCATION_ID":1,"ORDER_ID":8189,"ORDER_NO":"2292620","PRODUCTION_TYPE":5,"QTY":15,"QC_QNTY":14,"REJECT":0,"REPLACE":0,"SEWING_LINE":31,"SIZE":"14","SIZE_ID":12,"SPOT":0,"SPOT_STR":"","YEAR":2019}],"MasterPart":{"COMPANY_ID":1,"CUT_NO":"AKDL-20-003214","ENTRY_DATE":"04-Feb-2020","FLOOR_ID":4,"HOUR":"10:00","LOCATION_ID":1,"PRODUCTION_TYPE":5,"SEWING_LINE":31}},"status":"true"}';

		$issue_info = $this->android_model->swing_input_output_insert($response_arr);
		$status = true;
		if (empty($issue_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $issue_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function swing_input_output_v1_POST()
	{
		$response_arr = file_get_contents("php://input");

		//$response_arr = '{"resultset":{"DtlsPart":[{"ALTER":1,"ALTER_STR":"","BUYER":"Sainsburys","BARCODE_NO":"20970000125089","BUNDLE_NO":"AKDL-20-3214-2","COLOR":"NAVY","COLOR_ID":56,"COLOR_SIZE_ID":470710,"COLOR_TYPE_ID":0,"COUNTRY":"United Kingdom","COUNTRY_ID":260,"CUT_NO":"AKDL-20-003214","COMPANY_ID":1,"FLOOR_ID":4,"IS_RESCAN":0,"ITEM":"Trouser","ITEM_ID":22,"JOB_NO":"1274","LOCATION_ID":1,"ORDER_ID":8189,"ORDER_NO":"2292620","PRODUCTION_TYPE":5,"QTY":15,"QC_QNTY":14,"REJECT":0,"REPLACE":0,"SEWING_LINE":31,"SIZE":"14","SIZE_ID":12,"SPOT":0,"SPOT_STR":"","YEAR":2019}],"MasterPart":{"COMPANY_ID":1,"CUT_NO":"AKDL-20-003214","ENTRY_DATE":"04-Feb-2020","FLOOR_ID":4,"HOUR":"10:00","LOCATION_ID":1,"PRODUCTION_TYPE":5,"SEWING_LINE":31}},"status":"true"}';
		$this->load->model('android_model');
		$issue_info = $this->android_model->swing_input_output_insert_v1($response_arr);
		$status = true;
		if (empty($issue_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $issue_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function get_all_reject_name_get()
	{
		$this->load->model('android_model');
		$purpose_arr = $this->android_model->get_all_reject_name();
		$status = true;
		if (empty($purpose_arr)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $purpose_arr
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function get_all_reject_name_v1_get()
	{
		//$this->load->model('android_model');
		$this->load->model('android_model');
		$purpose_arr = $this->android_model->get_all_reject_name_v1();
		$status = true;
		if (empty($purpose_arr)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $purpose_arr
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function sew_fin_alter_defect_type_get()
	{
		$this->load->model('android_model');
		$purpose_arr = $this->android_model->sew_fin_alter_defect_type();
		$status = true;
		if (empty($purpose_arr)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $purpose_arr
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function sew_fin_alter_defect_type_v1_get()
	{
		$this->load->model('android_model');
		$purpose_arr = $this->android_model->sew_fin_alter_defect_type_v1();
		$status = true;
		if (empty($purpose_arr)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $purpose_arr
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function sew_fin_spot_defect_type_get()
	{
		$this->load->model('android_model');
		$purpose_arr = $this->android_model->sew_fin_spot_defect_type();
		$status = true;
		if (empty($purpose_arr)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $purpose_arr
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function sew_fin_spot_defect_type_v1_get()
	{
		$this->load->model('android_model');
		$purpose_arr = $this->android_model->sew_fin_spot_defect_type_v1();
		$status = true;
		if (empty($purpose_arr)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $purpose_arr
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function qc_barcode_scan_get()
	{
		if (!$this->get('cutting_no'))
		{
			$this->response('Cutting No is Required', 400);
		}

		$data = $this->android_model->qc_barcode_data($this->get('cutting_no'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response);


		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function qc_bundle_scan_get()
	{
		$this->load->model('android_model');
		if (!$this->get('bundle_no'))
		{
			$this->response('Bundle No is Required', 400);
		}

		$data = $this->android_model->qc_bundle_data($this->get('bundle_no'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response);


		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	function bundle_qc_post()
	{
		$response_arr = file_get_contents("php://input");

		//$response_arr = '{"resultset":{"DtlsPart":[{"BUNDLE_DATA":[{"BARCODE_NO":"19970000935749","BUNDLE_NO":"AKDL-19-21415-1","COUNTRY_ID":4,"DEFECT_STR":"4*1,6*1","NUMBER_END":5,"NUMBER_START":1,"QC_PASS_QTY":3,"REJECT_QNTY":2,"REPLACE_QNTY":0,"SIZE_ID":11},{"BARCODE_NO":"19970000935750","BUNDLE_NO":"AKDL-19-21415-2","COUNTRY_ID":4,"DEFECT_STR":"1*2,1*1","NUMBER_END":10,"NUMBER_START":6,"QC_PASS_QTY":5,"REJECT_QNTY":0,"REPLACE_QNTY":0,"SIZE_ID":11},{"BARCODE_NO":"19970000935751","BUNDLE_NO":"AKDL-19-21415-3","COUNTRY_ID":4,"DEFECT_STR":"1*2,1*1","NUMBER_END":12,"NUMBER_START":11,"QC_PASS_QTY":2,"REJECT_QNTY":0,"REPLACE_QNTY":0,"SIZE_ID":11},{"BARCODE_NO":"19970000935752","BUNDLE_NO":"AKDL-19-21415-4","COUNTRY_ID":4,"DEFECT_STR":"1*2,1*1","NUMBER_END":17,"NUMBER_START":13,"QC_PASS_QTY":5,"REJECT_QNTY":0,"REPLACE_QNTY":0,"SIZE_ID":11},{"BARCODE_NO":"19970000935753","BUNDLE_NO":"AKDL-19-21415-5","COUNTRY_ID":4,"DEFECT_STR":"1*2,1*1","NUMBER_END":22,"NUMBER_START":18,"QC_PASS_QTY":5,"REJECT_QNTY":0,"REPLACE_QNTY":0,"SIZE_ID":11},{"BARCODE_NO":"19970000935754","BUNDLE_NO":"AKDL-19-21415-6","COUNTRY_ID":4,"DEFECT_STR":"1*2,1*1","NUMBER_END":24,"NUMBER_START":23,"QC_PASS_QTY":2,"REJECT_QNTY":0,"REPLACE_QNTY":0,"SIZE_ID":11}],"COLOR_ID":16,"GMT_ITEM_ID":1,"ORDER_ID":7932}],"MasterPart":{"COMPANY_ID":1,"CUT_DTLS_ID":67247,"CUT_MST_ID":32346,"CUTTING_NO":"AKDL-19-021415","END_TIME":":","ENTRY_DATE":"Fri Dec 13 00:49:31 GMT+06:00 2019","FLOOR_ID":1,"JOB_NO":"AKDL-19-01072","LOCATION_ID":1,"QC_DATE":"13-Dec-2019","QC_HOUR":"22:10","START_TIME":":","TABLE_NO":"11001","WIDTH_DIA":"1"}},"isFirst":false,"status":"true"}';

		$issue_info = $this->android_model->bundle_qc_insert($response_arr);
		$status = true;
		if (empty($issue_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $issue_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function qc_bundle_post()
	{
		$this->load->model('android_model');
		$response_arr = file_get_contents("php://input");

		//$response_arr = '{"resultset":{"BundleNos":{"BUNDLE_NO":"AKDL-19-21413-1,AKDL-19-21413-2"},"DtlsPart":[{"BUNDLE_DATA":[{"BARCODE_NO":"19970000935749","BUNDLE_NO":"AKDL-19-21415-1","COUNTRY_ID":4,"DEFECT_STR":"4*1,6*1","NUMBER_END":5,"NUMBER_START":1,"QC_PASS_QTY":3,"REJECT_QNTY":2,"REPLACE_QNTY":0,"SIZE_ID":11},{"BARCODE_NO":"19970000935750","BUNDLE_NO":"AKDL-19-21415-2","COUNTRY_ID":4,"DEFECT_STR":"1*2,1*1","NUMBER_END":10,"NUMBER_START":6,"QC_PASS_QTY":5,"REJECT_QNTY":0,"REPLACE_QNTY":0,"SIZE_ID":11}],"COLOR_ID":16,"GMT_ITEM_ID":1,"ORDER_ID":7932}],"MasterPart":{"COMPANY_ID":1,"CUT_DTLS_ID":67247,"CUT_MST_ID":32346,"CUTTING_NO":"AKDL-19-021415","END_TIME":":","ENTRY_DATE":"Fri Dec 13 00:49:31 GMT+06:00 2019","FLOOR_ID":1,"JOB_NO":"AKDL-19-01072","LOCATION_ID":1,"QC_DATE":"13-Dec-2019","QC_HOUR":"0","START_TIME":":","TABLE_NO":"11001","WIDTH_DIA":"1"}},"isFirst":false,"status":"true"}';

		$issue_info = $this->android_model->qc_bundle_insert($response_arr);
		$status = true;
		if (empty($issue_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $issue_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function print_emb_sp_barcode_data_get()
	{
		if (!$this->get('barcode_no'))
		{
			$this->response('Barcode Is Required', 400);
		}

		if (!$this->get('type'))
		{
			$this->response('Type Is Required', 400);
		}

		if($this->get('action') == 1)
		{
			$data = $this->android_model->print_emb_sp_barcode_data(1,$this->get('barcode_no'),$this->get('type'));
		}
		else
		{
			$data = $this->android_model->print_emb_sp_barcode_data_receive(1,$this->get('barcode_no'),$this->get('type'));
		}

		$status = true;
		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK));


		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function print_emb_sp_barcode_data_receive_get()
	{
		if (!$this->get('barcode_no'))
		{
			$this->response('Barcode Is Required', 400);
		}

		if (!$this->get('type'))
		{
			$this->response('Type Is Required', 400);
		}

		$data = $this->android_model->print_emb_sp_barcode_data_receive(1,$this->get('barcode_no'),$this->get('type'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK));


		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function refference_data_get()
	{
		if (!$this->get('type'))
		{
			$this->response('Type Is Required', 400);
		}
		$data = $this->android_model->refference_data($this->get('type'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	
	function print_emb_sp_save_post()
	{
		$response_arr = file_get_contents("php://input");

		//$response_arr = '{"status":true,"resultset":{"MasterPart":{"COMPANY_ID":1,"PRODUCTION_TYPE":2,"LOCATION_ID":1,"FLOOR_ID":20,"EMBEL_ID":1,"EMBEL_NAME":1,"EMBEL_TYPE":1,"BODY_PART":84,"DELIVERY_DATE":"18-NOV-2019"},"DtlsPart":[{"CUT_NO":"AKDL-19-004011","BUNDLE_NO":"AKDL-19-4011-28","BARCODE_NO":19970000141193,"YEAR":2019,"COLOR_SIZE_ID":368935,"ORDER_ID":7066,"COUNTRY_ID":84,"SIZE_ID":32,"COLOR_ID":57,"JOB_NO":409,"COLOR_TYPE_ID":null,"BUYER":"Tchibo","ORDER_NO":821855,"ITEM_ID":1,"ITEM_NAME":"T-Shirt-Long Sleeve","QNTY":40,"RE_SCAN":0}]}}';
		//$response_arr = '{"status":true,"resultset":{"MasterPart":{"COMPANY_ID":1,"PRODUCTION_TYPE":2,"LOCATION_ID":1,"FLOOR_ID":4,"EMBEL_ID":2,"EMBEL_NAME":2,"EMBEL_TYPE":1,"BODY_PART":84,"DELIVERY_DATE":"18-NOV-2019"},"DtlsPart":[{"CUT_NO":"AKDL-19-004011","BUNDLE_NO":"AKDL-19-4011-23","BARCODE_NO":19970000141188,"YEAR":2019,"COLOR_SIZE_ID":368935,"ORDER_ID":7066,"COUNTRY_ID":84,"SIZE_ID":32,"COLOR_ID":57,"JOB_NO":409,"COLOR_TYPE_ID":null,"BUYER":"Tchibo","ORDER_NO":821855,"ITEM_ID":1,"ITEM_NAME":"T-Shirt-Long Sleeve","QNTY":40,"RE_SCAN":0}]}}';
		//$response_arr = '{"status":true,"resultset":{"MasterPart":{"COMPANY_ID":1,"PRODUCTION_TYPE":2,"LOCATION_ID":1,"FLOOR_ID":23,"EMBEL_ID":4,"EMBEL_NAME":4,"EMBEL_TYPE":1,"BODY_PART":84,"DELIVERY_DATE":"18-NOV-2019"},"DtlsPart":[{"CUT_NO":"AKDL-19-004011","BUNDLE_NO":"AKDL-19-4011-30","BARCODE_NO":19970000141195,"YEAR":2019,"COLOR_SIZE_ID":368935,"ORDER_ID":7066,"COUNTRY_ID":84,"SIZE_ID":32,"COLOR_ID":57,"JOB_NO":409,"COLOR_TYPE_ID":null,"BUYER":"Tchibo","ORDER_NO":821855,"ITEM_ID":1,"ITEM_NAME":"T-Shirt-Long Sleeve","QNTY":40,"RE_SCAN":0}]}}';

		$issue_info = $this->android_model->print_emb_sp_insert($response_arr);
		$status = true;
		if (empty($issue_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $issue_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function print_emb_sp_receive_save_post()
	{
		$response_arr = file_get_contents("php://input");

		//$response_arr = '{"status":true,"resultset":{"MasterPart":{"COMPANY_ID":1,"PRODUCTION_TYPE":2,"LOCATION_ID":1,"FLOOR_ID":20,"EMBEL_ID":1,"EMBEL_NAME":1,"EMBEL_TYPE":1,"BODY_PART":84,"DELIVERY_DATE":"18-NOV-2019"},"DtlsPart":[{"CUT_NO":"AKDL-19-004011","BUNDLE_NO":"AKDL-19-4011-28","BARCODE_NO":19970000141193,"YEAR":2019,"COLOR_SIZE_ID":368935,"ORDER_ID":7066,"COUNTRY_ID":84,"SIZE_ID":32,"COLOR_ID":57,"JOB_NO":409,"COLOR_TYPE_ID":null,"BUYER":"Tchibo","ORDER_NO":821855,"ITEM_ID":1,"ITEM_NAME":"T-Shirt-Long Sleeve","QNTY":40,"RE_SCAN":0}]}}';
		//$response_arr = '{"status":true,"resultset":{"MasterPart":{"COMPANY_ID":1,"PRODUCTION_TYPE":3,"LOCATION_ID":1,"FLOOR_ID":20,"EMBEL_ID":2,"EMBEL_NAME":2,"EMBEL_TYPE":1,"BODY_PART":84,"DELIVERY_DATE":"18-NOV-2019","CHALLAN_ID":124},"DtlsPart":[{"CUT_NO":"AKDL-19-004011","BUNDLE_NO":"AKDL-19-4011-135","BARCODE_NO":19970000141300,"YEAR":2019,"COLOR_SIZE_ID":368939,"ORDER_ID":7066,"COUNTRY_ID":84,"SIZE_ID":32,"COLOR_ID":57,"JOB_NO":409,"COLOR_TYPE_ID":null,"BUYER":"Tchibo","ORDER_NO":821855,"ITEM_ID":1,"ITEM_NAME":"T-Shirt-Long Sleeve","QNTY":40,"RE_SCAN":0}]}}';
		//$response_arr = '{"status":true,"resultset":{"MasterPart":{"COMPANY_ID":1,"PRODUCTION_TYPE":2,"LOCATION_ID":1,"FLOOR_ID":23,"EMBEL_ID":4,"EMBEL_NAME":4,"EMBEL_TYPE":1,"BODY_PART":84,"DELIVERY_DATE":"18-NOV-2019"},"DtlsPart":[{"CUT_NO":"AKDL-19-004011","BUNDLE_NO":"AKDL-19-4011-30","BARCODE_NO":19970000141195,"YEAR":2019,"COLOR_SIZE_ID":368935,"ORDER_ID":7066,"COUNTRY_ID":84,"SIZE_ID":32,"COLOR_ID":57,"JOB_NO":409,"COLOR_TYPE_ID":null,"BUYER":"Tchibo","ORDER_NO":821855,"ITEM_ID":1,"ITEM_NAME":"T-Shirt-Long Sleeve","QNTY":40,"RE_SCAN":0}]}}';

		$issue_info = $this->android_model->print_emb_sp_insert_receive($response_arr);
		$status = true;
		if (empty($issue_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $issue_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	} 
	function fin_fab_barcode_scan_get()
	{
		$this->load->model('android_model');
		if (!$this->get('barcode_no'))
		{
			$this->response('Barcode Is Required', 400);
		}

		$data = $this->android_model->finish_fab_production_barcode_data($this->get('barcode_no'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK));


		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function fin_fab_barcode_print_get()
	{
		/*if (!$this->get('barcode_no') || $this->get('batch_no'))
		{
			$this->response('Batch/Barcode Is Required', 400);
		}*/
		$this->load->model('android_model');
		$data = $this->android_model->finish_fab_production_barcode_print_data($this->get('barcode_no'), $this->get('batch_no'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK));


		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function fin_fab_barcode_save_post()
	{
		$response_arr = file_get_contents("php://input");
		/*echo "<pre>";
		print_r($response_arr);die;*/
		//$response_arr = '{"resultset":{"MasterPart":{"COMPANY_ID":1,"SERVICE_SOURCE":1,"SERVICE_COMPANY":1,"RECEIVE_DATE":"26-Dec-2019","USER_ID":1},"DtlsPart":[{"BARCODE_NO":20020000079,"BATCH_ID":64348,"COLOR_ID":16,"PROD_ID":12566,"DETARMINATION_ID":10,"ITEM_DESCRIPTION":"S/J,Organic 100%, 130, 70","GSM":130,"WIDTH":70,"WIDTH_DIA_TYPE":1,"BODY_PART_ID":1,"PO_BREAKDOWN_ID":7114,"MACHINE_NO_ID":1,"SHIFT_NAME":"","ROLL_ID":7981877,"ROLL_NO":8,"BOOKING_WITHOUT_ORDER":0,"PRODUCTION_QTY":30,"QC_PASS_QNTY":30,"REJECT_QNTY":30,"PO_ID":7114,"PO_NUMBER":0,"JOB_NO":"AKDL-20-00023","STYLE":0}]},"status":"true"}';

		
		$this->load->model('android_model');
		$issue_info = $this->android_model->finish_fabric_production_store($response_arr);
		$status = true;
		if (empty($issue_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $issue_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function fin_fab_barcode_scan_for_result_entry_get()
	{
		if (!$this->get('barcode_no'))
		{
			$this->response('Barcode Is Required', 400);
		}

		$data = $this->android_model->fin_fab_barcode_scan_for_result($this->get('barcode_no'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK));


		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function fabric_grade_get()
	{
		$result_info = $this->android_model->fin_fab_grade();
		$status = true;
		$response = array(
			'status' => $status,
			'resultset' => $result_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid Request'), 404);
		}
	}
	function fin_fab_qc_result_entry_post()
	{
		$response_arr = file_get_contents("php://input");
		/*echo "<pre>";
		print_r($response_arr);die;*/
		//$response_arr = '{"resultset":{"MasterPart":{"COMPANY_ID":1,"DTLS_ID":122301,"PROD_ID":87813,"BARCODE_NO":20020000546,"ROLL_ID":7984827,"ROLL_NO":44,"QNTY":30,"REJECT_QNTY":0,"QC_DATE":"28-Nov-2020","QC_NAME":"Panna","AC_ROLL_WIDTH":30,"ROLL_WGT":30,"ROLL_LENGTH":215.2776,"AC_GSM":200,"ROLL_STATUS":1,"TOTAL_PANALTY":2.00,"TOTAL_POINT":1.1148,"FABRIC_GRADE":"A","FABRIC_SHADE":"A","COMMENTS":"A"},"DtlsPart":[{"DEFECT_ID":1,"DEFECT_NAME":"Hole","DEFECT_COUNT":1,"FOUND_IN_INCH":5,"FOUND_IN_INCH_POINT":2,"PENALTY_POINT":2,}]},"status":"true"}';

		$result_info = $this->android_model->fin_fab_barcode_result_entry_store($response_arr);
		$status = true;
		if (empty($result_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $result_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid Attempt'), 404);
		}
	}
	function finish_roll_issue_scan_get()
	{
		if (!$this->get('barcode_no')) {
			$this->response('Barcode No Is Required', 400);
		}

		$data = $this->android_model->finish_roll_issue_scan_data($this->get('barcode_no'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response);


		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function get_finish_location_get()
	{
		$store_info = $this->android_model->get_finish_fab_location();

		$status = true;
		if (empty($store_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $store_info
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function get_finish_store_get()
	{
		$store_info = $this->android_model->get_finish_fab_store();

		$status = true;
		if (empty($store_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $store_info
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function get_finish_location_wise_store_get()
	{
		if (!$this->get('location_id')) {
			$this->response('Location Is Required', 400);
		}
		$store_info = $this->android_model->get_finish_location_wise_store($this->get('location_id'));

		$status = true;
		if (empty($store_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $store_info
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function finish_roll_issue_save_post()
	{
		$response_arr = file_get_contents("php://input");
		/*echo "<pre>";
		print_r($response_arr);die;*/
		//$response_arr = '{"status":true,"resultset":{"BarcodeNos":{"BARCODE_NO":21020000019},"MasterPart":{"COMPANY_ID":1,"ISSUE_DATE":"01-Oct-2020","ISSUE_PURPOSE":9,"BATCH_ID":1,"INSERTED_BY":1},"DtlsPart":[{"BARCODE_NO":21020000019,'RECEIVE_BASIS':9,'PI_WO_BATCH_NO':444502,'BOOKING_WITHOUT_ORDER':0,'BOOKING_NO':'AKDL-FFRR-21-00003','COMPANY_ID':1,'PROD_ID':87852,'GMT_ITEM_ID':87852,'BODY_PART_ID':87852,'COLOR_ID':16,'GMT_ITEM_ID':0,'PO_ID':7114,'ITEM_CATEGORY':2,'TRANSACTION_TYPE':2,'TRANSACTION_DATE':"13-Feb-2021",'STORE_ID':3,'QNTY':16,'RATE':0,'INSERTED_BY':999,'INSERT_DATE':"13-Feb-2021"}]}}';

		$result_info = $this->android_model->finish_roll_issue_save($response_arr);
		$status = true;
		if (empty($result_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $result_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid Attempt'), 404);
		}
	}
	function finish_roll_rcv_scan_get()
	{
		if (!$this->get('challan_no')) {
			$this->response('Challan No Is Required', 400);
		}

		$data = $this->android_model->finish_roll_rcv_scan_data($this->get('challan_no'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response);


		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function finish_roll_rcv_save_post()
	{
		$response_arr = file_get_contents("php://input");
		/*echo "<pre>";
		print_r($response_arr);die;*/
		//$response_arr = '{"status":true,"resultset":{"MasterPart":{"COMPANY_ID":1,"CHALLAN_NO":"AKDL-FDSR-20-00011","RECV_DATE":"01-Oct-2020","LOCATION_ID":1,"STORE_ID":10,"INSERTED_BY":1,"PRODUCT_IDS":87823},"DtlsPart":[{"20020000248":21020000019,'BATCH_ID':64389,'BOOKING_NO':'AKDL-Fb-20-00013','BOOKING_WITHOUT_ORDER':0,'COMPANY_ID':1,'PROD_ID':87823,'BODYPART_ID':1,'COLOR_ID':16,'COLOR_NAME':"WHITE",'PO_ID':7122,'ROLL_ID':7982394,'ROLL_NO':61,'ITEM_CATEGORY':2,'TRANSACTION_TYPE':2,'TRANSACTION_DATE':"22-Feb-2021",'CONS_QUANTITY':28,'CURRENT_WEIGHT':28,'REJECT_QNTY':0,'GREY_RATE':0,'DYEING_CHARGE':0,'REPROCESS':0,'PREV_REPROCESS':0,'INSERTED_BY':999,'INSERT_DATE':"22-Feb-2021"}]}}';

		$result_info = $this->android_model->finish_roll_rcv_save($response_arr);
		$status = true;
		if (empty($result_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $result_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid Attempt'), 404);
		}
	}
	/* function knitting_qc_result_entry_scan_get()
	{
		if (!$this->get('barcode_no'))
		{
			$this->response('Barcode Is Required', 400);
		}

		$data = $this->android_model->knitting_qc_result_entry_scan($this->get('barcode_no'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK));


		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	} */
	function knitting_qc_result_entry_scan_get()
	{
		$this->load->model('android_model');
		if (!$this->get('barcode_no'))
		{
			$this->response('Barcode Is Required', 400);
		}

		$data = $this->android_model->knitting_qc_result_entry_scan($this->get('barcode_no'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK));


		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function norsel_print_response_post()
	{
		$response_arr = file_get_contents("php://input");

		//$response_arr = '{"print_status" : true, "printer":"prntr1", "reference_value" : "20220211111", "response_msg" : "Some problem arise", "operator_id":"1000001" }';

		$response_info = $this->android_model->norsel_print_response($response_arr);
		$status = true;
		if (empty($response_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $response_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}



	// AKH End
	
	function image_upload_post()
	{
		//$input = file_get_contents('php://input');
		$data = json_decode(file_get_contents("php://input"), true);
		$bundle_no =  $_POST['bundle_id'];
		$defect_type =  $_POST['defect_type'];
		$fileName  =  $_FILES['fabric_image']['name'];
		$tempPath  =  $_FILES['fabric_image']['tmp_name'];
		$fileSize  =  $_FILES['fabric_image']['size'];

		if(empty($fileName))
		{
			echo json_encode(array("message" => "Image not found", "status" => false));
		}
		else
		{
			// set upload folder path 
			$upload_path = 'resources/images/sewing_defects/'; 
			// get image extension
			$fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION)); 
				
			// valid image extensions
			$valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); 
							
			// allow valid image file formats
			if(in_array($fileExt, $valid_extensions))
			{
				// move file from system temporary path to our upload folder path 
				$new_file_name = $bundle_no . "_" . $defect_type . "." . $fileExt;
				if(move_uploaded_file($tempPath, $upload_path . $new_file_name))
				{
					$msg = array("message" => 'Image uploaded successfully.', "status" => true);
					$status = true;
				}
				else
				{
					$msg = array("message" => 'Image upload failed.', "status" => false);
					$status = false;
				}
				
			}
			else
			{		
				$msg = array("message" => "Image extension is not valid. Allowed extensions are JPG, JPEG, PNG & GIF.", "status" => false);
				$status = false;
			}
		}

		$response = array(
			'status' => $status,
			'resultset' => $msg,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}

		//parse_str($input, $params);
		print_r($fileName);
	}

	function grey_fab_wo_get()
	{
		$this->load->model('android_model');
		
		/* if (!$this->get('job'))
		{
			$this->response('JOb No Is Required', 400);
		} */
		
		$job_no="";
		if ($this->get('job'))
		{
			$job_no = trim($this->get('job'));
		}


		$barcode_info = $this->android_model->grey_fab_wo_data($job_no);
		$status = true;

		if (empty($barcode_info)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'data' => $barcode_info
		);
		

		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
			//$this->response($response, 200);
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function finish_fab_wo_get()
	{
		$this->load->model('android_model');
		
		/* if (!$this->get('job'))
		{
			$this->response('JOb No Is Required', 400);
		} */

		$job_no="";
		if ($this->get('job'))
		{
			$job_no = trim($this->get('job'));
		}
		

		$barcode_info = $this->android_model->finish_fab_wo_data($job_no);
		$status = true;

		if (empty($barcode_info)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'data' => $barcode_info
		);
		

		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
			//$this->response($response, 200);
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function machine_list_get()
	{
		$this->load->model('android_model');
		
		
		$machine_info = $this->android_model->machine_list_data();
		$status = true;

		if (empty($machine_info)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'data' => $machine_info
		);
		

		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
			//$this->response($response, 200);
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function yarn_issue_get()
	{
		$this->load->model('android_model');
		
		/* if (!$this->get('job'))
		{
			$this->response('JOb No Is Required', 400);
		} */

		$job_no="";
		if ($this->get('job'))
		{
			$job_no = trim($this->get('job'));
		}
		

		$yarn_info = $this->android_model->yarn_issue_data($job_no);
		$status = true;

		if (empty($yarn_info)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'data' => $yarn_info
		);
		

		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
			//$this->response($response, 200);
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function knitting_production_get()
	{
		$this->load->model('android_model');
		
		/* if (!$this->get('job'))
		{
			$this->response('JOb No Is Required', 400);
		} */

		$job_no="";
		if ($this->get('job'))
		{
			$job_no = trim($this->get('job'));
		}
		

		$knit_info = $this->android_model->knitting_production_data($job_no);
		$status = true;

		if (empty($knit_info)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'data' => $knit_info
		);
		

		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
			//$this->response($response, 200);
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function grey_recv_get()
	{
		$this->load->model('android_model');
		
		/* if (!$this->get('job'))
		{
			$this->response('JOb No Is Required', 400);
		} */

		$job_no="";
		if ($this->get('job'))
		{
			$job_no = trim($this->get('job'));
		}
		

		$grey_rcv_info = $this->android_model->grey_recv_data($job_no);
		$status = true;

		if (empty($grey_rcv_info)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'data' => $grey_rcv_info
		);
		

		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
			//$this->response($response, 200);
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function batch_header_get()
	{
		$this->load->model('android_model');
		
		/* if (!$this->get('job'))
		{
			$this->response('JOb No Is Required', 400);
		} */

		$job_no="";
		if ($this->get('job'))
		{
			$job_no = trim($this->get('job'));
		}
		

		$batch_header_info = $this->android_model->batch_header_data($job_no);
		$status = true;

		if (empty($batch_header_info)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'data' => $batch_header_info
		);
		

		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
			//$this->response($response, 200);
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function batch_details_get()
	{
		$this->load->model('android_model');
		
		/* if (!$this->get('job'))
		{
			$this->response('JOb No Is Required', 400);
		} */

		$job_no="";
		if ($this->get('job'))
		{
			$job_no = trim($this->get('job'));
		}
		

		$batch_details_info = $this->android_model->batch_details_data($job_no);
		$status = true;

		if (empty($batch_details_info)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'data' => $batch_details_info
		);
		

		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
			//$this->response($response, 200);
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function batch_create_get()
	{
		$this->load->model('android_model');
		
		/* if (!$this->get('job'))
		{
			$this->response('JOb No Is Required', 400);
		} */

		$job_no="";
		if ($this->get('job'))
		{
			$job_no = trim($this->get('job'));
		}
		

		$batch_details_info = $this->android_model->batch_create_data($job_no);
		$status = true;

		if (empty($batch_details_info)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'data' => $batch_details_info
		);
		

		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
			//$this->response($response, 200);
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function dyeing_production_get()
	{
		$this->load->model('android_model');
		
		/* if (!$this->get('job'))
		{
			$this->response('JOb No Is Required', 400);
		} */

		$job_no="";
		if ($this->get('job'))
		{
			$job_no = trim($this->get('job'));
		}
		

		$dyeing_info = $this->android_model->dyeing_production_data($job_no);
		$status = true;

		if (empty($dyeing_info)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'data' => $dyeing_info
		);
		

		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
			//$this->response($response, 200);
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function bundle_wise_sewing_barcode_scan_get() 
	{
		$this->load->model('android_model');
		if (!$this->get('barcode_no'))
		{
			$this->response('Barcode Is Required', 400);
		}
		if (!$this->get('operation_id'))
		{
			$this->response('operation_id Is Required', 400);
		}
		if (!$this->get('OPERATOR_ID'))
		{
			$this->response('OPERATOR_ID Is Required', 400);
		}
		$data = $this->android_model->bundle_wise_sewing_barcode_data($this->get('barcode_no'),$this->get('operation_id'),$this->get('OPERATOR_ID'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response)
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function bundle_wise_sewing_barcode_post()
	{
		$this->load->model('android_model');

		// $response_arr = array(
		// 	'company_id' => $this->input->post('company_id'),
		// 	'cut_no' => $this->input->post('cut_no'),
		// 	'item_id' => $this->input->post('item_id'),
		// 	'qty' => $this->input->post('qty'),
		// 	'bundle_no' => $this->input->post('bundle_no'),
		// 	'barcode_no' => $this->input->post('barcode_no'),
		// 	'qty' => $this->input->post('qty'),
		// 	'color_size_id' => $this->input->post('color_size_id'),
		// 	'color_id' => $this->input->post('color_id'),
		// 	'size_id' => $this->input->post('size_id'),
		// 	'order_id' => $this->input->post('order_id'),
		// 	'job_id' => $this->input->post('job_id'),
		// 	'job_no' => $this->input->post('job_no'),
		// 	'buyer_id' => $this->input->post('buyer_id'),
		// 	'country_id' => $this->input->post('country_id'),
		// 	'operator_id' => $this->input->post('operator_id'),
		// 	'lib_operation_id' => $this->input->post('lib_operation_id'),
		// 	'operation_start' => $this->input->post('operation_start'),
		// 	'operation_end' => $this->input->post('operation_end'),
		// 	'line_id' => $this->input->post('line_id'),
		// 	'ws_id' => $this->input->post('ws_id'),
		// );
		$response_arr = file_get_contents("php://input");
		//$response_arr = '{"company_id": "your_company_id_value", "cut_no": "your_cut_no_value", "item_id": "your_item_id_value", "qty": "your_qty_value", "bundle_no": "your_bundle_no_value", "barcode_no": "your_barcode_no_value", "color_size_id": "your_color_size_id_value", "color_id": "your_color_id_value", "size_id": "your_size_id_value", "order_id": "your_order_id_value", "job_id": "your_job_id_value", "job_no": "your_job_no_value", "buyer_id": "your_buyer_id_value", "country_id": "your_country_id_value", "operator_id": "your_operator_id_value", "lib_operation_id": "your_lib_operation_id_value", "operation_start": "your_operation_start_value", "operation_end": "your_operation_end_value", "line_id": "your_line_id_value", "ws_id": "your_ws_id_value" }';
		//print_r($response_arr);die;
		//return $response_arr;
		$issue_info = $this->android_model->bundle_wise_sewing_barcode($response_arr);
		$status = true;
		if (empty($issue_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $issue_info
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function get_roll_info_for_receive_get()
	{
		$this->load->model('android_model');
		if (!$this->get('delivery_challan')) {
			$this->response('Delivery Challan No Is Required', 400);
		}

		$roll_info = $this->android_model->get_roll_info_for_receive($this->get('delivery_challan'));

		$status = true;
		if (empty($roll_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $roll_info
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function get_all_store_get()
	{
		$this->load->model('android_model');
		$roll_info = $this->android_model->get_all_store_data();

		$status = true;
		if (empty($roll_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $roll_info
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function get_roll_info_for_issue_get()
	{
		$this->load->model('android_model');
		if (!$this->get('barcode_no')) {
			$this->response('Barcode No Is Required', 400);
		}

		$roll_info = $this->android_model->get_roll_info_for_issue($this->get('barcode_no'));

		$status = true;
		if (empty($roll_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $roll_info
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function get_all_issue_purpose_get()
	{
		$this->load->model('android_model');
        // $purpose_arr = array();
        // $issue_purpose = array(3 => "Sales", 4 => "Sample With Order", 8 => "Sample Without Order", 11 => "Fabric Dyeing", 26 => "Damage", 29 => "Stolen", 30 => "Adjustment");
        // $i = 0;
        // foreach ($issue_purpose as $key => $purpose)
        // {
        //     $purpose_arr[$i]["MasterPart"]['ID'] = $key;
        //     $purpose_arr[$i]["MasterPart"]['PURPOSE'] = $purpose;
        //     $i++;
        // }

        //$purpose_arr = json_encode($purpose_arr);
		$purpose_arr = $this->android_model->get_all_issue_purpose();
		$status = true;
		if (empty($purpose_arr)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $purpose_arr
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}


	function roll_issue_post()
	{
		$this->load->model('android_model');
		$response_arr = file_get_contents("php://input");

		/* $response_arr = '{"resultset":{"MasterPart":{"COMPANY_ID":18,"DELIVERY_ID":0,"ISSUE_PURPOSE":3,"KNITTING_COMPANY_ID":18,"KNITTING_SOURCE":"In-house","KNITTING_SOURCE_ID":1,"LOCATION_ID":0,"SYS_NUMBER_PREFIX_NUM":0},"DtlsPart":[{"BARCODE_NO":"22020008378","BIN_BOX_ID":"0","BODY_PART_ID":"206","BODY_PART_NAME":"BK NECK + SLV TAPE","BOOKING_ID":"10696","BOOKING_NO":"null","BOOKING_WITHOUT_ORDER":"1","BRAND_ID":"1304","BUYER_ID":"107","BUYER_NAME":"AT","COLOR_ID":"","COLOR_RANGE_ID":"1","COLOR_RANGE_NAME":"Dark Color","COMPOSITION":"Cotton 65% Polyester 35% ","CONSTRUCTION":"Single Jersey","DETER_ID":"63","DTLS_ID":"57795","FLOOR_ID":"0","GSM":"120","KNITTING_COMPANY":"Logic Software Ltd","MACHINE_NO_ID":"1357","PO_BREAKDOWN_ID":"3553","PROD_ID":"61593","PRODUCTION_BASIS":"null","QNTY":"8","RACK_ID":"0","REJECT_QNTY":"0","ROLL_ID":"187959","ROLL_NO":"3","ROOM_ID":"0","SAMP_BOOKING":"LSL-SMN-22-00002","SHELF_ID":"0","SHIFT_NAME":"null","STITCH_LENGTH":"3.5","STORE_ID":"279","UOM":"null","WIDTH":"70","YARN_COUNT":"3.0","YARN_LOT":"hasan05", "YARN_RATE":"452.25", "KNITING_CHARGE":"10", "ROLL_RATE":"462.25", "IS_SALES":"0", "USER_ID":"165"}]},"status":"true"}'; */

		$issue_info = $this->android_model->roll_Issue($response_arr);
		$status = true;
		if (empty($issue_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $issue_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function get_barcode_info_get()
	{
		$this->load->model('android_model');
		if (!$this->get('delivery_challan') && !$this->get('barcode')) {
			$this->response('Delivery Challan No Or Barcode No Is Required', 400);
		}

		$delivery_challan=""; $barcode_no="";
		if ($this->get('delivery_challan'))
		{
			$delivery_challan = trim($this->get('delivery_challan'));
		}

		if ($this->get('barcode'))
		{
			$barcode_no = trim($this->get('barcode'));
		}

		$roll_info = $this->android_model->get_barcode_info($delivery_challan,$barcode_no);

		$status = true;
		if (empty($roll_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $roll_info
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function roll_delivery_barcode_receive_post()
	{
		$this->load->model('android_model');
		$response_arr = file_get_contents("php://input");

		//$response_arr = '{"status":true,"resultset":{"MasterPart":{"COMPANY_ID":18,"USER_ID":"165","RECEIVE_DATE":"26-Dec-2022","DELIVERY_NUMBER":"LSL-GDSR-22-00107","STORE_ROOM_RACK_ID":"500"},"DtlsPart":[{"BARCODE_NO":"22020008678"},{"BARCODE_NO":"22020008679"}]}}';

		$receive_info = $this->android_model->roll_delivery_barcode_receive($response_arr);
		$status = true;
		if (empty($receive_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $receive_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function kd_plan_status_post()
	{
		$this->load->model('android_model');
		$response_arr = file_get_contents("php://input");

		//$response_arr='{"status":true, "row_id":"FFL-05-0029"}';

		$receive_info = $this->android_model->kd_plan_status($response_arr);
		$status = true;
		if (empty($receive_info)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $receive_info,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function ws_operation_get(){
		$this->load->model('android/report/Work_study_model');

		$style_ref = $this->get('style_ref');
		$gmts_item_id = $this->get('gmts_item_id');
		$bulletin_type_id = $this->get('bulletin_type_id');

		
		if(!$style_ref){
			return $this->response(array('errorMsg' => "Style Ref. is required"), 404);
		}

		if(!$gmts_item_id){
			return $this->response(array('errorMsg' => "Garments Item is required"), 404);
		}
		
		$query_rows = $this->Work_study_model->getOpertionData($style_ref, $gmts_item_id, $bulletin_type_id);
		
		$response = array(
			'result' => $query_rows,
		);

		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Data not found'), 404);
		}
        
    }

	function style_wise_operation_list_get(){
		$PO_BREAK_DOWN_ID = "";//$this->get('PO_BREAK_DOWN_ID');
		$ITEM_NUMBER_ID = $this->get('ITEM_NUMBER_ID');
		//$COUNTRY_ID = $this->get('COUNTRY_ID');
		$JOB_NO_MST = $this->get('JOB_NO_MST');
		$LINE_ID = $this->get('LINE_ID');
		$STYLE_NUMBER = $this->get('STYLE_NUMBER');
		$USER_ID = $this->get('USER_ID');

		$this->load->model('Android_model');
		$data = $this->Android_model->style_wise_operation_list_model($JOB_NO_MST,$PO_BREAK_DOWN_ID,$ITEM_NUMBER_ID,$LINE_ID,$STYLE_NUMBER,$USER_ID);//$COUNTRY_ID,
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

	function active_qc_session_list_get(){
		$this->load->model('Android_model');
		$data = $this->Android_model->active_qc_session_list($JOB_NO_MST,$PO_BREAK_DOWN_ID,$ITEM_NUMBER_ID,$COUNTRY_ID,$LINE_ID,$STYLE_NUMBER);
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

	function color_size_wise_in_out_get(){
		$COLOR_SIZE_ID = $this->get('COLOR_SIZE_ID');
		$LINE_ID = $this->get('LINE_ID');
		$PO_BREAK_DOWN_ID = $this->get('PO_BREAK_DOWN_ID');
		$ITEM_NUMBER_ID = $this->get('ITEM_NUMBER_ID');
		$COUNTRY_ID = $this->get('COUNTRY_ID');
		$USER_ID = $this->get('USER_ID');

		$this->load->model('Android_model');
		$data = $this->Android_model->color_size_wise_in_out($COLOR_SIZE_ID, $LINE_ID, $PO_BREAK_DOWN_ID, $ITEM_NUMBER_ID,$COUNTRY_ID,$USER_ID);
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

	function type_wise_in_out_get(){
		//http://localhost/platform-v3.5/logic-api/index.php/api/Android/type_wise_in_out?PO_BREAK_DOWN_ID=75205&ITEM_NUMBER_ID=179&LINE_ID=595&USER_ID=374&COLOR_ID=1&TYPE=GOOD&COUNTRY_ID=10

		$TYPE = $this->get('TYPE');
		$COLOR_ID = $this->get('COLOR_ID');
		$LINE_ID = $this->get('LINE_ID');
		$PO_BREAK_DOWN_ID = $this->get('PO_BREAK_DOWN_ID');
		$ITEM_NUMBER_ID = $this->get('ITEM_NUMBER_ID');
		$COUNTRY_ID = $this->get('COUNTRY_ID');
		$USER_ID = $this->get('USER_ID');

		$this->load->model('Android_model');
		$data = $this->Android_model->type_wise_in_out($TYPE, $COLOR_ID, $LINE_ID, $PO_BREAK_DOWN_ID, $ITEM_NUMBER_ID,$COUNTRY_ID,$USER_ID);
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

	function defected_details_list_to_rectify_get()
	{ 
		$this->load->model('android_model');

		if (!$this->get('COLOR_ID'))
		{
			$this->response('Color Id Is Required', 400);
		}

		if (!$this->get('PO_BREAK_DOWN_ID'))
		{
			$this->response('PO Breakdown Id Is Required', 400);
		}

		if (!$this->get('LINE_ID'))
		{
			$this->response('Line Id Is Required', 400);
		}

		if (!$this->get('USER_ID'))
		{
			$this->response('User Id Is Required', 400);
		}

		if (!$this->get('GMT_ITEM_ID'))
		{
			$this->response('Garments Item Id Is Required', 400);
		}

		/*if (!$this->get('LINE_ID'))
		{
			$this->response('Line Id Is Required', 400);
		}*/

		$result = $this->android_model->defected_details_list_to_rectify($this->get('JOB_ID'), $this->get('PO_BREAK_DOWN_ID'), $this->get('COLOR_ID'), $this->get('LINE_ID'), $this->get('USER_ID'), $this->get('GMT_ITEM_ID'));
		$status = true;
		if (empty($result)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $result
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function defected_gmts_details_list_to_rectify_get()
	{ 
		$this->load->model('android_model');

		if (!$this->get('COLOR_ID'))
		{
			$this->response('Color Id Is Required', 400);
		}

		if (!$this->get('PO_BREAK_DOWN_ID'))
		{
			$this->response('PO Breakdown Id Is Required', 400);
		}

		if (!$this->get('LINE_ID'))
		{
			$this->response('Line Id Is Required', 400);
		}

		if (!$this->get('USER_ID'))
		{
			$this->response('User Id Is Required', 400);
		}

		if (!$this->get('GMT_ITEM_ID'))
		{
			$this->response('Garments Item Id Is Required', 400);
		}

		$result = $this->android_model->defected_gmts_details_list_to_rectify($this->get('JOB_ID'), $this->get('PO_BREAK_DOWN_ID'), $this->get('COLOR_ID'), $this->get('LINE_ID'), $this->get('USER_ID'), $this->get('GMT_ITEM_ID'));
		$status = true;
		if (empty($result)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $result
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function inactive_sewing_config_post() { 
		
		$this->load->model('android_model');
		$response_arr = file_get_contents("php://input");
		//$response_arr='{"status":true, "row_id":"FFL-05-0029"}';
		$save_info = $this->android_model->inactive_sewing_config($response_arr);

     	$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
	}

	function fab_hanger_archive_meeting_POST() { 
		
		$this->load->model('android_model');
		$response_arr = file_get_contents("php://input");
		//$response_arr='{"BUYER_NAME":1, "MEETING_MINUTES":"Will order soon","INSERTED_BY":2,"ARCHIVE_ID":[{"ID": 123},{"ID": 124}]}';
		$save_info = $this->android_model->fab_hanger_archive_meeting($response_arr);

     	$status = true;
     	if (empty($save_info)) {
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
     	}
	}

	function sewing_barcode_scan_v2_get()
	{ 
		$this->load->model('Android_model');
		if (!$this->get('barcode_no'))
		{
			$this->response('Barcode Is Required', 400);
		}
		if (!$this->get('type'))
		{
			$this->response('Production Type Is Required', 400);
		}

		$data = $this->Android_model->sewing_barcode_scan_v2(1,$this->get('barcode_no'),$this->get('type'));
		$status = true;
		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK));


		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function approval_menu_by_privilege_get()
	{ 
		
		if (!$this->get('user_id')) {
			$this->response('UserID Is Required', 400);
		}
		
		$user_id = $this->get('user_id');
		$menu_sql = "SELECT A.MENU_NAME AS MENU, A.F_LOCATION AS MENU_LINK, A.M_MENU_ID AS MENU_ID, C.USER_FULL_NAME AS FULL_NAME, C.ID AS USER_LOGIN_ID, A.SLNO AS SLNO, D.IS_SEEN AS IS_SEEN, COUNT (D.ID) AS NOTIFICATIONS, E.IS_ACTIVE FROM MAIN_MENU A INNER JOIN USER_PRIV_MST B ON A.M_MENU_ID = B.MAIN_MENU_ID INNER JOIN USER_PASSWD C ON B.USER_ID = C.ID LEFT JOIN APPROVAL_NOTIFICATION_ENGINE D ON A.M_MENU_ID = D.M_MENU_ID AND D.USER_ID = C.ID AND D.IS_APPROVED != 1 AND D.IS_SEEN != 1 AND D.STATUS_ACTIVE = 1 LEFT JOIN APPROVAL_NOTI_MENU_SETTING E ON  A.M_MENU_ID  = E.MENU_ID AND E.USER_ID = $user_id WHERE B.USER_ID = $user_id AND A.STATUS = 1 AND A.M_MODULE_ID = 12 AND A.IS_MOBILE_MENU = 1 AND B.VALID = 1 GROUP BY A.MENU_NAME, A.F_LOCATION, A.M_MENU_ID, C.USER_FULL_NAME, C.ID, A.SLNO, D.IS_SEEN, E.IS_ACTIVE ORDER BY A.SLNO ASC";

		$menu_result = sql_select($menu_sql);

		

		if (!empty($menu_result)) {
			foreach ($menu_result as $menu) {
				$menu_arr[] = array(
					'MENU' => $menu->MENU,
					'MENU_LINK' => $menu->MENU_LINK,
					'MENU_ID' => $menu->MENU_ID,
					'FULL_NAME' => $menu->FULL_NAME,
					'USER_LOGIN_ID' => $menu->USER_LOGIN_ID,
					'SLNO' => $menu->SLNO,
					'NOTIFICATIONS' => $menu->NOTIFICATIONS,
					'IS_SEEN' => $menu->IS_SEEN,
					'IS_ACTIVE' => $menu->IS_ACTIVE,
				);
			}
		}

		$status = true;
		if (empty($menu_arr)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $menu_arr
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function notification_details_get()
	{
 
		if (!$this->get('user_id')) {
			$this->response('UserID Is Required', 400);
		}
		if (!$this->get('menu_id')) {
			$this->response('Menu Id Is Required', 400);
		}
		$entry_form = return_field_value("ENTRY_FORM","ELECTRONIC_APPROVAL_SETUP","page_id=".$this->get('menu_id'),"ENTRY_FORM");
		if($entry_form == 1)
		{
			$this->load->model('android/approval/Approval_notifications');
			$userInfo = $this->Approval_notifications->get_notification_details($this->get('user_id'),$this->get('menu_id'));
		}
		else if($entry_form == 77)
		{ 
			$this->load->model('android/approval/Knit_precosting_approval_notification');
			$userInfo = $this->Knit_precosting_approval_notification->get_notification_details($this->get('user_id'),$this->get('menu_id'));
		}
		else if($entry_form == 59)
		{ 	//print_r(5);die;
			$this->load->model('android/approval/Gate_pass_approval_notification');
			$userInfo = $this->Gate_pass_approval_notification->get_notification_details($this->get('user_id'),$this->get('menu_id'));
		}
		
		
		$status = true;
		if (empty($userInfo)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $userInfo
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function add_counting_get()
	{
		// echo 555;die;
		$this->load->model('android/approval/Approval_notifications');
		if (!$this->get('user_id')) {
			$this->response('UserID Is Required', 400);
		}
		$this->Approval_notifications->insert_counting($this->get('user_id'));
	}

	function approve_from_apps_post()
	{ 
		
		$entry_form = return_field_value("ENTRY_FORM","ELECTRONIC_APPROVAL_SETUP","page_id=".$this->input->post('menu_id'),"ENTRY_FORM");
		
		$response_arr = array(
			'menu_id' => $this->input->post('menu_id'),
			'user_id' => $this->input->post('user_id'),
			'ref_id' => $this->input->post('ref_id'),
			'entry_form' => $entry_form
		);

		//print_r($response_arr);die;
		//echo $entry_form;die;


		if($entry_form == 1)
		{
			$this->load->model('android/approval/Approval_notifications');
			$save_info = $this->Approval_notifications->approve_from_apps($response_arr);
		}
		else if($entry_form == 77)
		{
			$this->load->model('android/approval/Knit_precosting_approval_notification');
			$save_info = $this->Knit_precosting_approval_notification->approve_from_apps($response_arr);
		}
		else if($entry_form == 59)
		{
			$this->load->model('android/approval/Gate_pass_approval_notification');
			$save_info = $this->Gate_pass_approval_notification->approve_from_apps($response_arr);
		}
		
		


		$status = true;
     	if (empty($save_info))
		{
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Data is not approved successfully'), 404);
     	}
	}

	function unapprove_from_apps_post()
	{ 		
		$entry_form = return_field_value("ENTRY_FORM","ELECTRONIC_APPROVAL_SETUP","page_id=".$this->input->post('menu_id'),"ENTRY_FORM");

		$response_arr = array(
			'menu_id' => $this->input->post('menu_id'),
			'user_id' => $this->input->post('user_id'),
			'ref_id' => $this->input->post('ref_id'),
			'entry_form' => $entry_form
		);

		

		if($entry_form == 1)
		{
			$this->load->model('android/approval/Approval_notifications');
			$save_info = $this->Approval_notifications->unapprove_from_apps($response_arr);
		}
		else if($entry_form == 77)
		{

			$this->load->model('android/approval/Knit_precosting_approval_notification');
			$save_info = $this->Knit_precosting_approval_notification->unapprove_from_apps($response_arr);

		}
		else if($entry_form == 59)
		{

			$this->load->model('android/approval/Gate_pass_approval_notification');
			$save_info = $this->Gate_pass_approval_notification->unapprove_from_apps($response_arr);

		}
	
		

		$status = true;
     	if (empty($save_info))
		{
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Data is not unapproved successfully'), 404);
     	}
	}

	function deny_approve_from_apps_POST()
	{ 
		//$this->load->model('android/approval/Approval_notifications');
		// $response_arr = array(
		// 	'menu_id' => $this->input->post('menu_id'),
		// 	'user_id' => $this->input->post('user_id'),
		// 	'ref_id' => $this->input->post('ref_id'),
		// 	'message' => $this->input->post('message'),
		// );

		//$menu_id = $this->get('menu_id');
		$menu_id = $this->input->post('menu_id');

		$entry_form =  return_field_value("ENTRY_FORM","ELECTRONIC_APPROVAL_SETUP","page_id=$menu_id","ENTRY_FORM");

		$response_arr = array(
			'menu_id' => $this->input->post('menu_id'),
			'user_id' => $this->input->post('user_id'),
			'ref_id' => $this->input->post('ref_id'),
			'message' => $this->input->post('message'),
			'entry_form' => $entry_form,
		);
		
		if($entry_form == 1)
		{
			$this->load->model('android/approval/Approval_notifications');
			$save_info = $this->Approval_notifications->deny_approve_from_apps($response_arr);
		}
		else if($entry_form == 77)
		{
			$this->load->model('android/approval/Knit_precosting_approval_notification');
			$save_info = $this->Knit_precosting_approval_notification->deny_approve_from_apps($response_arr);
		}
		else if($entry_form == 59)
		{
			$this->load->model('android/approval/Gate_pass_approval_notification');
			$save_info = $this->Gate_pass_approval_notification->deny_approve_from_apps($response_arr);
		}
		
		

		$status = true;
     	if (empty($save_info))
		{
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Data is not unapproved successfully'), 404);
     	}
	}

	function get_electronic_approval_user_get()
	{
		$this->load->model('android/approval/Approval_notifications');
		if (!$this->get('user_id')) {
			$this->response('UserID Is Required', 400);
		}
		$save_info= $this->Approval_notifications->getElectronicApprovalUser(3,1,[],$this->get('user_id'));
		$status = true;
     	if (empty($save_info))
		{
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Data is not unapproved successfully'), 404);
     	}
	}

	function logout_from_apps_post()
	{
		
		$user_id = $this->input->post('user_id');
		$device_id = $this->input->post('device_id');
		$this->db->trans_strict(TRUE);
        $this->db->trans_begin();
		try
		{
			$FCM_TOKEN =  return_field_value("FCM_TOKEN AS FCM_TOKEN","APPROVAL_NOTI_USER_DEVICES","USER_ID=$user_id and DEVICE_ID = '".$device_id."'","FCM_TOKEN");
			if(empty($FCM_TOKEN))
			{
				$save_info = ['status'=>'ok','message'=>'Success'];
			}

			$res = execute_query("DELETE APPROVAL_NOTI_USER_DEVICES  WHERE  USER_ID = $user_id and DEVICE_ID = '".$device_id."'");
			if($res !=1)
			{
				throw new Exception($res);
			}

			if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                $save_info = ['status'=>'fail','message'=>'Failed'];
            }
            else
            {
                $this->db->trans_commit();
                $save_info = ['status'=>'ok','message'=>'Success'];
                
            }
				
		}
		catch( Exception $e)
		{
			$this->db->trans_rollback();
			$save_info = ['status'=>'fail','message'=>$e->getMessage()];
		}
        


		$status = true;
     	if (empty($save_info))
		{
     		$status = false;
     	}
     	$response = array(
     		'status' => $status,
     		'resultset' => $save_info
     	);
     	if ($response) {
     		$this->response($response, 200);
     	} else {
     		$this->response(array('errorMsg' => 'Data is not approved successfully'), 404);
     	}
	}

	
	function send_email_check_get()
	{ 
		
		//$this->load->model('android_model');
		$this->load->model('android/approval/Approval_notifications');
		
		$userInfo = $this->Approval_notifications->send_email_check();
		$status = true;
		if (empty($userInfo)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'data' => $userInfo
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}
	function knit_qc_defect_array_get()
	{
		/*$knit_defect_array = array(1 => "Hole", 5 => "Loop", 10 => "Press Off", 15 => "Lycra Out", 20 => "Lycra Drop", 21 => "Lycra Out/Drop", 25 => "Dust", 30 => "Oil Spot", 35 => "Fly Conta", 40 => "Slub", 45 => "Patta", 50 => "Needle Break", 55 => "Sinker Mark", 60 => "Wheel Free", 65 => "Count Mix", 70 => "Yarn Contra", 75 => "NEPS", 80 => "Black Spot", 85 => "Oil/Ink Mark", 90 => "Set up", 95 => "Pin Hole", 100 => "Slub Hole", 105 => "Needle Mark", 110 => "Miss Yarn", 115 => "Color Contra [Yarn]", 120 => "Color/dye spot", 125 => "friction mark", 130 => "Pin out", 135 => "softener spot", 140 => "Dirty Spot", 145 => "Rust Stain", 150 => "Stop mark", 155 => "Compacting Broken", 160 => "Insect Spot", 165 => "Grease spot", 166 => "Knot", 167 => "Tara",168 =>"Contamination",169 =>"Thick and Thin" );*/

		$knit_defect_array=array(1=>"Hole",5=>"Loop", 105 => "Needle Mark",30=>"Oil Spot",168 =>"Contamination", 45=>"Patta",15=>"Lycra Out", 90 => "Set up",169=>"Thick & Thin", 40 => "Slub", 20 => "Lycra Drop");

		$defect_arr = array();
		$i=0;
		foreach ($knit_defect_array as $key => $defect) {
			$defect_arr[$i]["DEFECT_ID"] = $key;
			$defect_arr[$i]["DEFECT_NAME"] = $defect;
			$i++;
		}
		$status = true;
		$response = array(
			'status' => $status,
			'resultset' => $defect_arr
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid Request'), 404);
		}
	}

	function program_create_get()
	{
		$this->load->model('android_model');
		
		/* if (!$this->get('job'))
		{
			$this->response('JOb No Is Required', 400);
		} */

		$job_no="";
		if ($this->get('job'))
		{
			$job_no = trim($this->get('job'));
		}
		

		$barcode_info = $this->android_model->program_create_data($job_no);
		$status = true;

		if (empty($barcode_info)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'data' => $barcode_info
		);
		

		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
			//$this->response($response, 200);
		}
		else
		{
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function knit_defect_inchi_array_get()
	{
		//$knit_defect_inchi_array = array(1 => 'Defect=<3" : 1', 2 => 'Defect=<6" but >3" : 2', 3 => 'Defect=<9" but >6" : 3', 4 => 'Defect>9" : 4', 5 => 'Hole<1" : 2', 6 => 'Hole>1" : 4');
		$knit_defect_inchi_array = array(1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '2', 6 => '4');

		$defect_inch_arr = array();
		$i=0;
		foreach ($knit_defect_inchi_array as $key => $defect) {
			$defect_inch_arr[$i]["DEFECT_INCH_ID"] = $key;
			$defect_inch_arr[$i]["DEFECT_INCH_NAME"] = $defect;
			$i++;
		}
		$status = true;
		$response = array(
			'status' => $status,
			'resultset' => $defect_inch_arr
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid Request'), 404);
		}
	}

	function sewing_output_configaration_level_get()
	{
		$this->load->model('android_model');

		$data = $this->android_model->sewing_output_configaration_level($this->get('company_id'));
		$status = true;

		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'data' => $data
		);
		

		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
		}
		else
		{
			$this->response(array('errorMsg' => 'Not Found'), 404);

		}
	}

	// function inventory_library_get()
	// {
	// 	$company_id = $this->get('company_id');

	// 	$floor_get = $this->get('floor_get');
	// 	$room_get = $this->get('room_get');
	// 	$rack_get = $this->get('rack_get');
	// 	$shelf_get = $this->get('shelf_get');

	// 	$floor_id = $this->get('floor_id');
	// 	$room_id = $this->get('room_id');
	// 	$rack_id = $this->get('rack_id');
	// 	$shelf_id = $this->get('shelf_id');
		

	// 	$this->load->model('android_model');

	// 	$data = $this->android_model->inventory_library($company_id,$floor_get,$room_get,$rack_get,$shelf_get,$floor_id,$room_id,$rack_id,$shelf_id);
	// 	$status = true;

	// 	if (empty($data)) {
	// 		$status = false;
	// 	}

	// 	$response = array(
	// 		'status' => $status,
	// 		'data' => $data
	// 	);

	// 	if ($response) 
	// 	{
	// 		return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
	// 	}
	// 	else
	// 	{
	// 		$this->response(array('errorMsg' => 'Not Found'), 404);
	// 	}
	// }

	function bundle_data_for_cutting_store_receive_get()
	{
		$barcode_no = $this->get('barcode_no');
		

		$this->load->model('android_model');

		$data = $this->android_model->bundle_data_for_cutting_store_receive($barcode_no);
		$status = true;

		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'data' => $data
		);

		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
		}
		else
		{
			$this->response(array('errorMsg' => 'Not Found'), 404);
		}
	}

	public function bundle_data_save_for_cutting_store_POST(){

		$response_arr = file_get_contents("php://input");
		//$response_arr='[{ "BUNDLE_NO": "AF-12234", "BARCODE_NO": 1223, "PO_BREAKDOWN_ID": 1223, "PO_NUMBER": "avc", "BUYER_ID": 1224, "COMPANY_ID": 123, "QC_PASS_QNTY": 20, "SIZE_ID": 1223, "COLOR_NUMBER_ID": 1223, "COLOR_NAME": "agv", "CUTTING_FLOOR_ID": 1223, "FL_RO_RACK_DTL_ID": 1223, "PRODUCTION_QNTY": 1223, "RECEIVE_QNTY_KG": 10, "USER_ID": 2 }, { "BUNDLE_NO": "AF-12234", "BARCODE_NO": 1224, "PO_BREAKDOWN_ID": 1223, "PO_NUMBER": "avc", "BUYER_ID": 1223, "COMPANY_ID": 123, "QC_PASS_QNTY": 20, "SIZE_ID": 1223, "COLOR_NUMBER_ID": 1223, "COLOR_NAME": "agv", "CUTTING_FLOOR_ID": 1223, "FL_RO_RACK_DTL_ID": 1223, "PRODUCTION_QNTY": 1223, "RECEIVE_QNTY_KG": 10, "USER_ID": 2 }]';

		$this->load->model('android_model');

		$data = $this->android_model->bundle_data_save_for_cutting_store($response_arr);
		$status = true;
		if (empty($data)) {
			$status = false;
		}
		$response = array(
			'status' => $status,
			'resultset' => $data
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function bundle_data_from_receive_rack_get()
	{
		$barcode_no = $this->get('barcode_no');
		

		$this->load->model('android_model');

		$data = $this->android_model->bundle_data_from_receive_rack($barcode_no);
		$status = true;

		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'data' => $data
		);

		if ($response) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
		}
		else
		{
			$this->response(array('errorMsg' => 'Not Found'), 404);
		}
	}

	function bundle_data_issue_from_recv_rack_POST()
	{	$response_arr = file_get_contents("php://input");		

		$this->load->model('android_model');

		$data = $this->android_model->bundle_data_issue_from_recv_rack($response_arr);
		//$status = true;

		if ($data) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($data,JSON_NUMERIC_CHECK)); 
		}
		else
		{
			$this->response(array('errorMsg' => 'Not Found'), 404);
		}
	}

	function grn_wise_yarn_data_get()
	{
		$grn_no = $this->get('grn_no');

		$this->load->model('android_model');

		$data = $this->android_model->grn_wise_yarn_data($grn_no);
		$status = true;

		if (empty($data)) {
			$status = false;
		}

		$response = array(
			'status' => $status,
			'data' => $data
		);

		if ($response)
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
		}
		else
		{
			$this->response(array('errorMsg' => 'Not Found'), 404);
		}
	}

	public function grn_wise_yarn_data_save_POST()
	{
		$response_arr = file_get_contents("php://input");
		//$response_arr = '{"status":true,"MASTER_ID":"79836","USER_ID":"1","RECV_NUMBER":"OG-YGRN-23-00032","DTLS_ID":"999","RFID":[{"EPCID":"303ACB034005D8A09DFD8973"},{"EPCID":"303ACB034005D8A09DFD8974"}]}';
		//print_r(5);die;
		$this->load->model('android_model');

		//$this->load->model('yarn_parking/Grn_wise_yarn_data');

		$data = $this->android_model->grn_wise_yarn_data_save_v2($response_arr);
		//$data = $this->Grn_wise_yarn_data->grn_wise_yarn_data_save_v2($response_arr);
		//$status = true;
		$result = [
			"STATUS" => 200,
			"msg" => $data,
		];

		if ($result) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($result,JSON_NUMERIC_CHECK)); 
		}
		else
		{
			$this->response(array('errorMsg' => 'Not Found'), 404);
		}
	}

	public function rfid_yarn_store_location_update_POST()
	{
		$response_arr = file_get_contents("php://input");
		//$response_arr = '{ "status": true, "MASTER_ID": 79981, "USER_ID": 1, "DTLS_ID": 1004, "RFID": 55, "FLOOR": 1, "ROOM": 1, "RACK": 1, "SHELF": 1, "BIN": 1 }';
		//print_r(5);die;
		$this->load->model('android_model');

		//$this->load->model('yarn_parking/Grn_wise_yarn_data');

		$data = $this->android_model->rfid_yarn_store_location_update($response_arr);
		//$data = $this->Grn_wise_yarn_data->grn_wise_yarn_data_save_v2($response_arr);
		//$status = true;
		$result = [
			"STATUS" => 200,
			"msg" => $data,
		];

		if ($result) 
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($result,JSON_NUMERIC_CHECK)); 
		}
		else
		{
			$this->response(array('errorMsg' => 'Not Found'), 404);
		}
	}

	public function purchase_req_dtls_by_mst_id_get()
	{
		$req_mst_id = $this->get('req_mst_id');
		$this->load->model('android_model');
		$response = $this->android_model->purchase_req_dtls_by_mst_id($req_mst_id);


		if ($response)
		{
			return $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response,JSON_NUMERIC_CHECK)); 
		}
		else
		{
			$this->response(array('errorMsg' => 'Not Found'), 404);
		}

	}
}