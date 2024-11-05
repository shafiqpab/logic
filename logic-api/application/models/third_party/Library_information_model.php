<?php
class Library_information_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
		error_reporting(0);
	}

	function get_max_value($tableName, $fieldName)
	{
		return $this->db->select_max($fieldName)->get($tableName)->row()->{$fieldName};
	}

	function buyer_info($buyer_id = 0,$buyer_name = "")
	{
		$query_conditions = "";

		if($buyer_id != 0){
			$buyer_id = trim($buyer_id);
			$query_conditions .= " and ID = $buyer_id";
		}

		if($buyer_name != ""){
			$buyer_name = trim($buyer_name);
			$query_conditions .= " and BUYER_NAME LIKE '%$buyer_name%'";
		}
		
		
		$query = "select ID,BUYER_NAME,SHORT_NAME from LIB_BUYER where STATUS_ACTIVE =1 and IS_DELETED=0 $query_conditions order by ID desc";
		//echo $query; die;
		$lib_buyer_arr = $this->db->query($query)->result();
		
		return $lib_buyer_arr;
	}


	function model_info($source_id,$source_name)
	{
		$where_con = '';
		if ($source_id) {
			$where_con.= "and a.ID = '$source_id'";
		}
		if ($source_name) {
			$where_con.= "and a.JOB_NO = '$source_name'";
		}
		//print_r($model_id);die;
		$query = "SELECT a.ID,a.JOB_NO,a.STYLE_REF_NO,a.REMARKS,a.BUYER_NAME AS BUYER_ID,d.BUYER_NAME,c.GMTS_ITEM_ID,e.ITEM_NAME from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b, WO_PO_DETAILS_MAS_SET_DETAILS c, LIB_BUYER d,LIB_GARMENT_ITEM e WHERE b.JOB_ID = a.ID AND d.ID = a.BUYER_NAME AND c.JOB_ID = b.JOB_ID AND c.GMTS_ITEM_ID = e.ID AND b.SHIPING_STATUS !=3 AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE=1 $where_con";
		//echo $query; die;
		$lib_model_arr = $this->db->query($query)->result();
		//print_r($lib_model_arr); die;
		$return_array = array();
		foreach ($lib_model_arr as $row) {
			$return_array[] = array(
				'source_id' => $row->ID,
				'source_name' => $row->JOB_NO,
				'model_name' => $row->STYLE_REF_NO,
				'model_code' => $row->STYLE_REF_NO,
				'artical_no' => ($row->DETAILS_REMARKS) ? $row->DETAILS_REMARKS : 0,
				'model_group_id' => $row->GMTS_ITEM_ID,
				'model_group' => $row->ITEM_NAME,
				'customer_id' => $row->BUYER_ID,
				'customer_name' => $row->BUYER_NAME,
			);
		}

		return $return_array;
	}

	function order_info($source_id)
	{	
		//Color Query start
		$query_LIB_COLOR = "select ID,COLOR_NAME FROM LIB_COLOR where STATUS_ACTIVE = 1 AND IS_DELETED = 0";
		$table_LIB_COLOR = $this->db->query($query_LIB_COLOR)->result();

		foreach($table_LIB_COLOR as $row){
			$lib_color[$row->ID] = [$row->COLOR_NAME];
		}
		//Color Query end

		//Size Query start
		$query_LIB_SIZE = "select ID,SIZE_NAME FROM LIB_SIZE where STATUS_ACTIVE = 1 AND IS_DELETED = 0";
		$table_LIB_SIZE = $this->db->query($query_LIB_SIZE)->result();

		foreach($table_LIB_SIZE as $row){
			$lib_size[$row->ID] = [$row->SIZE_NAME];
		}
		//Size Query end
		
		$query_order = "SELECT b.ID, b.PO_NUMBER,b.PO_QUANTITY,b.PO_RECEIVED_DATE, a.STYLE_REF_NO, a.BUYER_NAME as BUYER_ID, d.BUYER_NAME, b.SHIPMENT_DATE, c.PO_BREAK_DOWN_ID, c.COLOR_NUMBER_ID, c.SIZE_NUMBER_ID,b.PLAN_CUT, c.PLAN_CUT_QNTY FROM WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b, WO_PO_COLOR_SIZE_BREAKDOWN c,LIB_BUYER d WHERE a.ID = b.JOB_ID AND b.ID = c.PO_BREAK_DOWN_ID AND b.IS_DELETED = 0 AND c.IS_DELETED = 0 AND c.IS_DELETED = 0 AND b.SHIPING_STATUS != 3 AND a.BUYER_NAME = d.ID and JOB_NO = '$source_id' ";
		//echo $query; die;
		$lib_model_arr = $this->db->query($query_order)->result();
		//print_r($lib_model_arr); die;
		$return_array = Array();
		foreach($lib_model_arr as $row){

			$quantity_array[$row->ID][]=[
				'color_id'=> $row->COLOR_NUMBER_ID,
				'color'=> $lib_color[$row->COLOR_NUMBER_ID][0],
				'size_id'=> $row->SIZE_NUMBER_ID,
				'size'=> $lib_size[$row->SIZE_NUMBER_ID][0],
				'plan_cut_quantity'=> $row->PLAN_CUT_QNTY
			];

		}

		foreach($lib_model_arr as $row){

			$return_array[$row->ID]= Array(
				'source_id'=>$row->ID,
				'order_number'=>$row->PO_NUMBER,
				'order_group'=>0,
				'order_group_id'=>0,
				'model_id '=>$row->STYLE_REF_NO,
				'customer_id'=>$row->BUYER_ID,
				'customer_name'=>$row->BUYER_NAME,
				'order_quantity'=>$row->PO_QUANTITY,
				'order_receive_date'=>$row->PO_RECEIVED_DATE,
				'order_deadline'=>date("Y-m-d", strtotime($row->SHIPMENT_DATE)),
				'quantity'=>$quantity_array[$row->ID],
				//'quantity'=>$row->PLAN_CUT,
				
			);		
		
		}
		return $return_array;
	}

	function create_employee($response_arr)
	{	
		$response_obj = json_decode($response_arr,true);

		if($response_obj){
			$EMP_CODE = $response_obj['employee_code'];
			$FIRST_NAME = $response_obj['employee_name'];
			$DOB = $response_obj['date_of_birth'];
			$IMG = $response_obj['image'];

			$query_employee = "SELECT EMP_CODE,FIRST_NAME,DOB FROM LIB_EMPLOYEE Where EMP_CODE = $EMP_CODE";

			$employee=$this->db->query($query_employee)->result();
			//print_r($employee); die;
			//image processing

			$img = str_replace(' ', '+', $IMG);
			$data = base64_decode($img);
			file_put_contents("./resources/images/employee_image/$EMP_CODE.png", $data);
			$IMG = "./resources/images/employee_image/$EMP_CODE.png";

			//End image processing

			if(empty($employee)){

				$ID = $this->get_max_value("LIB_EMPLOYEE", "ID") + 1;
				$DOB =  date("d-M-Y", strtotime($DOB));
				$insert_employee_query = "INSERT INTO LIB_EMPLOYEE (ID,EMP_CODE,FIRST_NAME,DOB,IMG) VALUES ('$ID','$EMP_CODE','$FIRST_NAME','$DOB','$IMG')";
				$result=$this->db->query($insert_employee_query);

				if($result){
					return $return_data = [
							"employee_code" => $EMP_CODE,
							"employee_name" => $FIRST_NAME,
							"date_of_birth" => $DOB,
							"image" => $IMG,
							"Status"		=> "Successful"
						];
				}else{
					$return_data =["Status"=> "Failed"];
				}
			}else{
				return $return_data =["Status"=> "Employee Already Exits"];
			}
						
		}else{
			return $return_data =["Status"=> "Post Object is empty"];
		}
	}

	function knit_order_image($source_id)
	{
		$query_image_path = "SELECT MASTER_TBLE_ID,IMAGE_LOCATION FROM COMMON_PHOTO_LIBRARY where FORM_NAME = 'knit_order_entry' and MASTER_TBLE_ID = '$source_id'";	
		$table_image_path=$this->db->query($query_image_path)->result();

		$result_array = Array();
		//$image_fullpath = "file_upload/1684064119Capture.JPG";
		
		foreach($table_image_path as $row){
			//$image_fullpath = $server_path.$row->IMAGE_LOCATION;
			$img_path = str_replace('logic-api/',$row->IMAGE_LOCATION,base_url());
			//print_r($img_path);die;
			$image_data = file_get_contents($img_path);
			if($image_data){
				$base64_image = base64_encode($image_data);
				//print_r($base64_image);die;
				$result_array[$row->MASTER_TBLE_ID][] = [
					"job_no" => $row->MASTER_TBLE_ID,
					"image" => $base64_image
				];
			}						
		}
		return $result_array;
	}
}