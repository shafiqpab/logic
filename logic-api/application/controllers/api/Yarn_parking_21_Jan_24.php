<?php


defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package    CodeIgniter - Android Apps
 * @category   AndroidApps
 * @author     Jahid Hasan <jahid@logicsoftbd.com>
 * @copyright  2017 Logic Software Ltd Development Group
 */
require APPPATH . '/libraries/REST_Controller.php';

class Yarn_parking extends REST_Controller {


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

	function login_get()
	{
		if (!$this->get('user_id')) {
			$this->response('UserID Is Required', 400);
		}
		if (!$this->get('pwd')) {
			$this->response('Password Is Required', 400);
		}

		try {
			$this->load->model('Yarn_parking_model');
			$userInfo = $this->Yarn_parking_model->login($this->get('user_id'), $this->get('pwd'));
		} catch (Exception $e) {
			$msg = $e;
			$userInfo = [];
		}


		if (empty($userInfo)) {
		}
		$response = array(
			'status' => true,
			'resultset' => $userInfo,
			'msg' => $msg,
		);
		if ($response) {
			$this->response($response, 200);
		} else {
			$this->response(array('errorMsg' => 'Invalid User ID or Password'), 404);
		}
	}

	function grn_wise_yarn_data_get()
	{
		$grn_no = $this->get('grn_no');
		

		$this->load->model('Yarn_parking_model');

		$data = $this->Yarn_parking_model->grn_wise_yarn_data($grn_no);
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
	function grn_wise_yarn_data_for_issue_get()
	{	//print_r(4);die;
		$req_no = $this->get('req_no');
		$issue_basis = $this->get('issue_basis');
		$issue_purpose = $this->get('issue_purpose');
		

		$this->load->model('Yarn_parking_model');

		$data = $this->Yarn_parking_model->grn_wise_yarn_data_for_issue($req_no,$issue_basis,$issue_purpose);
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

	function grn_wise_yarn_data_for_issue_save_POST(){
		$obj = file_get_contents("php://input");
		// $obj = '{
		// 	"STATUS":"TRUE",
		// 	"MASTER_ID": "",
		// 	"USER_ID": "1",
		// 	"RECV_NUMBER": "",
		// 	"DTLS_ID": "123",
		// 	"ISSUE_PERPOSE": "",
		// 	"LOCATION_ID": "",
		// 	"CHALLAN_NO": "",
		// 	"MST_REMARKS": "",
		// 	"ISSUE_QNTY": "",
		// 	"RETURN_QTY": "",
		// 	"NO_OF_BAG": "",
		// 	"NO_OF_CONE": "",
		// 	"WEIGHT_PER_BAG": "",
		// 	"WEIGHT_PER_CONE": "",
		// 	"READY_TO_APPROVED": "",
		// 	"ATTENTION": "",
		// 	"DTLS_REMARKS": "",
		// 	"BASIS": 8,
		// 	"SYSTEM_NO": "OG-YDE-23-00121",
		// 	"RFID": [
		// 		{
		// 			"EPCID": "303ACB034005D8A09DFD897B"
		// 		},
		// 		{
		// 			"EPCID": "303ACB034005D8A09DFD8976"
		// 		}
		// 	]
		// }';

		//print_r($basis_id);die;

		//basis 3 requisition
		// $response_arr = '{
		// 	--"cbo_company_id": "1", --company_id from trans table
		// 	--"cbo_basis": "3", --inv master table er basis 
		// 	--"cbo_issue_purpose": "1", --first time user input, 2nd time inv_issue master er issue perpose
		// 	--"txt_issue_date": "26-Dec-2023", --Today date gone to trans table
		// 	--"txt_booking_no": "Null", -- ai basis er null e jabe
		// 	--"txt_booking_id": "Null",-- ai basis er null e jabe
		// 	--"cbo_location_id": "108", -- ppl_planning_info_entry_dtls er location id, if not found then user will give it. Knitting source "inhouse" hole location lagbei. ppl_planning_info_entry_dtls this table give the knitting source column
		// 	--"cbo_knitting_source": "1", -- ppl_planning_info_entry_dtls er knitting source
		// 	--"cbo_knitting_company": "9", --ppl_planning_info_entry_dtls er knitting party
		// 	--"cbo_supplier": "573", --from product_details_master er supplier
		// 	--"cbo_store_name": "232", --from trans table store id
		// 	--"txt_challan_no": "NULL", --user input
		// 	--"cbo_loan_party": "0", -- always 0 as default
		// 	--"cbo_buyer_name": "65", --ppl_planning_info_entry_dtls er buyer_id
		// 	--"txt_style_ref": "NULL", --default null always
		// 	--"txt_buyer_job_no": "NULL", --default null always
		// 	--"cbo_sample_type": "0", --default null always
		// 	--"txt_remarks": "NULL", --input from client from mst part
		// 	--"txt_req_no": "13751", --ppl_yarn_requisition_entry er requisition number
		// 	--"txt_lot_no": "A%20Free-3", -- product_details_master er lot
		// 	--"cbo_yarn_count": "8", --product_details_master er yarn_count_id
		// 	--"cbo_color": "0", --for this basis 0 default
		// 	--"cbo_floor": "0", -- from trans table
		// 	--"cbo_room": "0", --from trans table
		// 	--"txt_issue_qnty": "15", --input from user
		// 	--"txt_returnable_qty": "0", --input from user
		// 	--"txt_composition": "100%25%20Cotton%20100", ----product_details_master er yarn_comp_type1st
		// 	--"cbo_brand": "3575", -- ----product_details_master er brand
		// 	--"txt_rack": "0", -- from trans table
		// 	--"txt_no_bag": "NULL", --input from user
		// 	--"txt_no_cone": "NULL", --input from user
		// 	--"txt_weight_per_bag": "0", --input from user
		// 	--"txt_weight_per_cone": "0", --input from user
		// 	--"cbo_yarn_type": "1", ----product_details_master er yarn_type
		// 	--"cbo_dyeing_color": "0",  --for this basis 0 default
		// 	--"txt_shelf": "0", -- from trans table
		// 	--"txt_current_stock": "23500", ----product_details_master er current_stock
		// 	--"cbo_uom": "12", --product_details_master er unite_of_messure
		// 	--"cbo_item": "0", --product_details_master er item_category_id
		// 	--"update_id_mst": "0", --first time 0
		// 	--"update_id": "NULL", --first time null
		// 	--"save_data": "5337**15.00**", --po**issue_qnty**return_qnty,po**issue_qnty**return_qnty,po**issue_qnty**return_qnty, (po taken from ppl_planning_info_entry_dtls)
		// 	--"all_po_id": "5337", -- ppl_planning_info_entry_dtls po_id comma seperated if multitle
		// 	--"txt_prod_id": "77139", ---product_details_master er ID
		// 	--"job_no": "NULL", --default null always
		// 	--"cbo_ready_to_approved": "2", --user input
		// 	--"cbo_supplier_lot": "0", -----product_details_master er supplier_id
		// 	--"txt_btb_lc_id": "NULL", --default null always
		// 	--"extra_quantity": 0, --default null always
		// 	--"txt_entry_form": "0", -- --default null always
		// 	--"hidden_p_issue_qnty": "0", ----default null always
		// 	--"hdn_wo_qnty": "NULL",----default null always
		// 	--"txt_service_booking_no": "NULL", ----default null always
		// 	--"demand_id": "0", ----default null always
		// 	--"hdn_req_no": "13751", --requisition number
		// 	--"original_save_data": "NULL", --default null always
		// 	--"cbo_bin": "0", -- from trans table
		// 	--"saved_knitting_company": "9", --planning entry detls table er knitting_party 
		// 	--"txt_attention": "NULL", --user input
		// 	--"txt_remarks_dtls": "NULL", --user input from dtls part
		// 	--"txt_wo_id": "NULL", ---default null always
		// 	--"txt_pi_id": "NULL", ---default null always
		// 	--"hdn_fabric_booking_no": "NULL" ---default null always
		// }';

		//basis = 8 //  Demand  
		// $response_arr = '{
		// 	--"db_type": 1,
		// 	--"operation": 0, --default 0
		// 	"user_id": 1,
		// 	--"txt_system_no": "NULL", -- first time null 2nd time inv_issue master er issue number column
		// 	--"cbo_company_id": "1", --company_id from trans table
		// 	--"cbo_basis": "3", --inv master table er basis 
		// 	--"cbo_issue_purpose": "1", --first time user input, 2nd time inv_issue master er issue perpose
		// 	--"txt_issue_date": "26-Dec-2023", --Today date gone to trans table
		// 	--"txt_booking_no": "Null", -- ai basis er null e jabe
		// 	--"txt_booking_id": "Null",-- ai basis er null e jabe
		// 	--"cbo_location_id": "108", -- ppl_planning_info_entry_dtls er location id, if not found then user will give it. Knitting source "inhouse" hole location lagbei. ppl_planning_info_entry_dtls this table give the knitting source column
		// 	--"cbo_knitting_source": "1", -- ppl_planning_info_entry_dtls er knitting source
		// 	--"cbo_knitting_company": "9", --ppl_planning_info_entry_dtls er knitting party
		// 	--"cbo_supplier": "573", --from product_details_master er supplier
		// 	--"cbo_store_name": "232", --from trans table store id
		// 	--"txt_challan_no": "NULL", --user input
		// 	--"cbo_loan_party": "0", -- always 0 as default
		// 	--"cbo_buyer_name": "65", --ppl_planning_info_entry_dtls er buyer_id
		// 	--"txt_style_ref": "NULL", --default null always
		// 	--"txt_buyer_job_no": "NULL", --default null always
		// 	--"cbo_sample_type": "0", --default null always
		// 	--"txt_remarks": "NULL", --input from client from mst part
		// 	**--"txt_req_no": "13751", --Demand Number
		// 	--"txt_lot_no": "A%20Free-3", -- product_details_master er lot
		// 	--"cbo_yarn_count": "8", --product_details_master er yarn_count_id
		// 	--"cbo_color": "0", --for this basis 0 default
		// 	--"cbo_floor": "0", -- from trans table
		// 	--"cbo_room": "0", --from trans table
		// 	--"txt_issue_qnty": "15", --input from user
		// 	--"txt_returnable_qty": "0", --input from user
		// 	--"txt_composition": "100%25%20Cotton%20100", ----product_details_master er yarn_comp_type1st
		// 	--"cbo_brand": "3575", -- ----product_details_master er brand
		// 	--"txt_rack": "0", -- from trans table
		// 	--"txt_no_bag": "NULL", --input from user
		// 	--"txt_no_cone": "NULL", --input from user
		// 	--"txt_weight_per_bag": "0", --input from user
		// 	--"txt_weight_per_cone": "0", --input from user
		// 	--"cbo_yarn_type": "1", ----product_details_master er yarn_type
		// 	--"cbo_dyeing_color": "0",  --for this basis 0 default
		// 	--"txt_shelf": "0", -- from trans table
		// 	--"txt_current_stock": "23500", ----product_details_master er current_stock
		// 	--"cbo_uom": "12", --product_details_master er unite_of_messure
		// 	--"cbo_item": "0", --product_details_master er item_category_id
		// 	--"update_id_mst": "0", --first time 0
		// 	--"update_id": "NULL", --first time null
		// 	--"save_data": "5337**15.00**", --po**issue_qnty**return_qnty,po**issue_qnty**return_qnty,po**issue_qnty**return_qnty, (po taken from ppl_planning_info_entry_dtls)
		// 	--"all_po_id": "5337", -- ppl_planning_info_entry_dtls po_id comma seperated if multitle
		// 	--"txt_prod_id": "77139", ---product_details_master er ID
		// 	--"job_no": "NULL", --default null always
		// 	--"cbo_ready_to_approved": "2", --user input
		// 	--"cbo_supplier_lot": "0", -----product_details_master er supplier_id
		// 	--"txt_btb_lc_id": "NULL", --default null always
		// 	--"extra_quantity": 0, --default null always
		// 	--"txt_entry_form": "0", -- --default null always
		// 	--"hidden_p_issue_qnty": "0", ----default null always
		// 	--"hdn_wo_qnty": "NULL",----default null always
		// 	--"txt_service_booking_no": "NULL", ----default null always
		// 	***--"demand_id": "0", ----demand id
		// 	--"hdn_req_no": "13751", --requisition number
		// 	--"original_save_data": "NULL", --default null always
		// 	--"cbo_bin": "0", -- from trans table
		// 	--"saved_knitting_company": "9", --planning entry detls table er knitting_party 
		// 	--"txt_attention": "NULL", --user input
		// 	--"txt_remarks_dtls": "NULL", --user input from dtls part
		// 	--"txt_wo_id": "NULL", ---default null always
		// 	--"txt_pi_id": "NULL", ---default null always
		// 	--"hdn_fabric_booking_no": "NULL" ---default null always
		// }';

		//basis = 1 
		// $response_arr = '{
		// 	--"db_type": 1,
		// 	--"operation": 0, --default 0
		// 	"user_id": 1,
		// 	--"txt_system_no": "NULL", -- first time null 2nd time inv_issue master er issue number column
		// 	--"cbo_company_id": "1", --company_id from trans table
		// 	--"cbo_basis": "3", --inv master table er basis 
		// 	--"cbo_issue_purpose": "1", --first time user input, 2nd time inv_issue master er issue perpose
		// 	--"txt_issue_date": "26-Dec-2023", --Today date gone to trans table
		// 	**--"txt_booking_no": "Null", -- WO_YARN_DYEING_MST er YDW_NO
		// 	**--"txt_booking_id": "Null",-- WO_YARN_DYEING_MST er ID
		// 	--"cbo_location_id": "108", -- ppl_planning_info_entry_dtls er location id, if not found then user will give it. Knitting source "inhouse" hole location lagbei. ppl_planning_info_entry_dtls this table give the knitting source column
		// 	--"cbo_knitting_source": "1", -- ppl_planning_info_entry_dtls er knitting source
		// 	--"cbo_knitting_company": "9", --source 1 or 5 then knitting company will be company id from master else  Master table er supplier
		// 	--"cbo_supplier": "573", --from product table er supplier
		// 	--"cbo_store_name": "232", --from trans table store id
		// 	--"txt_challan_no": "NULL", --user input
		// 	--"cbo_loan_party": "0", -- always 0 as default
		// 	--"cbo_buyer_name": "65", --ppl_planning_info_entry_dtls er buyer_id
		// 	--"txt_style_ref": "NULL", --default null always
		// 	--"txt_buyer_job_no": "NULL", --dtls table er job_NO
		// 	--"cbo_sample_type": "0", --default null always
		// 	--"txt_remarks": "NULL", --input from client from mst part
		// 	**--"txt_req_no": "", --default null always
		// 	--"txt_lot_no": "A%20Free-3", -- product_details_master er lot
		// 	--"cbo_yarn_count": "8", --product_details_master er yarn_count_id
		// 	--"cbo_color": "0", --default null always
		// 	--"cbo_floor": "0", -- from trans table
		// 	--"cbo_room": "0", --from trans table
		// 	--"txt_issue_qnty": "15", --input from user
		// 	--"txt_returnable_qty": "0", --input from user
		// 	--"txt_composition": "100%25%20Cotton%20100", ----product_details_master er yarn_comp_type1st
		// 	--"cbo_brand": "3575", -- ----product_details_master er brand
		// 	--"txt_rack": "0", -- from trans table
		// 	--"txt_no_bag": "NULL", --input from user
		// 	--"txt_no_cone": "NULL", --input from user
		// 	--"txt_weight_per_bag": "0", --input from user
		// 	--"txt_weight_per_cone": "0", --input from user
		// 	--"cbo_yarn_type": "1", ----product_details_master er yarn_type
		// 	--"cbo_dyeing_color": "0",  --dtls table er yarn_color
		// 	--"txt_shelf": "0", --from trans table
		// 	--"txt_current_stock": "23500", --product_details_master er current_stock
		// 	--"cbo_uom": "12", --product_details_master er unite_of_messure
		// 	--"cbo_item": "0", --product_details_master er item_category_id
		// 	--"update_id_mst": "0", --first time 0
		// 	--"update_id": "NULL", --first time null
		// 	--"save_data": "5337**15.00**", --po**issue_qnty**return_qnty,po**issue_qnty**return_qnty,po**issue_qnty**return_qnty, (po taken from ppl_planning_info_entry_dtls)
		// 	--"all_po_id": "5337", -- ppl_planning_info_entry_dtls po_id comma seperated if multitle
		// 	--"txt_prod_id": "77139", ---product_details_master er ID
		// 	--"job_no": "NULL", --dtls er job_NO
		// 	--"cbo_ready_to_approved": "2", --user input
		// 	--"cbo_supplier_lot": "0", -----product_details_master er supplier_id
		// 	--"txt_btb_lc_id": "NULL", --default null always
		// 	--"extra_quantity": 0, --default null always
		// 	--"txt_entry_form": "0", -- master table er entry form
		// 	--"hidden_p_issue_qnty": "0", ----default null always
		// 	--"hdn_wo_qnty": "NULL",----default null always
		// 	--"txt_service_booking_no": "NULL", ----default null always
		// 	***--"demand_id": "0", --Default Null
		// 	--"hdn_req_no": "13751", --requisition number
		// 	--"original_save_data": "NULL", --default null always
		// 	--"cbo_bin": "0", -- from trans table
		// 	--"saved_knitting_company": "9", --planning entry detls table er knitting_party 
		// 	--"txt_attention": "NULL", --user input
		// 	--"txt_remarks_dtls": "NULL", --user input from dtls part
		// 	--"txt_wo_id": "NULL", ---default null always
		// 	--"txt_pi_id": "NULL", ---default null always
		// 	--"hdn_fabric_booking_no": "NULL" ---default null always
		// }';
		$this->load->model('Yarn_parking_model');
		//$this->Yarn_parking_model->writeFile("grn_wise_yarn_data_for_issue_save", $response_arr);

		$data = $this->Yarn_parking_model->grn_wise_yarn_data_for_issue_save($obj);
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

	function grn_wise_yarn_data_save_POST(){
		$response_arr = file_get_contents("php://input");
		//$response_arr = '{"status":true,"MASTER_ID":"80511","USER_ID":"1","RECV_NUMBER":"RpC-YGRN-24-00001","DTLS_ID":"1025","RFID":[{"EPCID":"303ACB034005D8A09DFD8973"},{"EPCID":"303ACB034005D8A09DFD8974"}]}';
		$this->load->model('Yarn_parking_model');
		
		$this->Yarn_parking_model->writeFile("Grn_wise_yarn_data_save", $response_arr);

		$data = $this->Yarn_parking_model->grn_wise_yarn_data_save($response_arr);
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
}