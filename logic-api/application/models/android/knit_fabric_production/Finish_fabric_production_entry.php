<?php

class Finish_fabric_production_entry extends CI_Model {

	function __construct() {
		error_reporting(0);
		parent::__construct();
	}

    function writeFile($fileName,$txt){
		$file="note_url_script/objectData/".$fileName.".text";
		$current = file_get_contents($file);
		$current .= $txt."\n..........".date('d-m-Y h:i:s a',time()).".........\n\n";
		file_put_contents($file, $current);
	 }

	
	public function observation_finish_batch_data($search_data,$is_batch_dtls=0) {
		$return_array = array();
		
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
		$composition = return_library_array("SELECT id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name", "id", "composition_name");
		
		$composition[0] = 0;
		$composition_arr = array();
		$constructtion_arr = array();
		$sql_deter = "SELECT a.ID, a.CONSTRUCTION, b.COPMPOSITION_ID, b.PERCENT from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array = sql_select($sql_deter);
		foreach ($data_array as $row) {
			$constructtion_arr[$row->ID] = $row->CONSTRUCTION;
			if (isset($composition_arr[$row->ID])) {
				$composition_arr[$row->ID] .= $composition[$row->COPMPOSITION_ID] . " " . $row->PERCENT . "% ";
			} else {
				if (isset($composition[$row->COPMPOSITION_ID])) {
					$composition_arr[$row->ID] = $composition[$row->COPMPOSITION_ID] . " " . $row->PERCENT . "% ";
				} else {
					$composition_arr[$row->ID] = "";
				}

			}
		}		
		
		
		if($is_batch_dtls==1){
			
			$is_exists = sql_select("select a.ID AS RECV_ID,a.RECV_NUMBER,b.BUYER_ID,b.FABRIC_DESCRIPTION_ID,b.BATCH_ID,b.ID AS PRO_DTLS_ID,c.BATCH_NO,b.PROD_ID from INV_RECEIVE_MASTER a,PRO_FINISH_FABRIC_RCV_DTLS b,PRO_BATCH_CREATE_MST c where a.id=b.mst_id and a.ENTRY_FORM = 7 and c.id=b.batch_id and c.BATCH_NO='".trim($search_data)."'");
			foreach($is_exists as $row){
				$batchDataArr[]=array(
					RECV_NUMBER=>$row->RECV_NUMBER,
					FABRIC_DESCRIPTION_ID=>$composition_arr[$row->FABRIC_DESCRIPTION_ID],
					RECV_ID=>$row->RECV_ID,
					PRO_DTLS_ID=>$row->PRO_DTLS_ID,
					BATCH_NO=>$row->BATCH_NO,
					BATCH_ID=>$row->BATCH_ID,
					PROD_ID=>$row->PROD_ID,
					BUYER_ID=>$row->BUYER_ID,
					BUYER_NAME=>$buyer_arr[$row->BUYER_ID]
				);
				
			}
			return $batchDataArr;
		}
		else{
			
			$body_part = return_library_array("SELECT id,body_part_full_name from  lib_body_part where status_active=1 and is_deleted=0 order by body_part_full_name", "id", "body_part_full_name");
			$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
			$machine_arr = return_library_array("select id, MACHINE_NO from LIB_MACHINE_NAME where status_active=1 and is_deleted=0", "id", "MACHINE_NO");
			
			
			$is_exists = sql_select("select b.BATCH_ID,b.BUYER_ID,c.BATCH_NO,c.BOOKING_NO from PRO_FINISH_FABRIC_RCV_DTLS b,PRO_BATCH_CREATE_MST c where c.id=b.batch_id and b.id='".trim($search_data)."'");
			foreach($is_exists as $row){
				$batchDataArr[$row->BATCH_ID]['BATCH_NO']=$row->BATCH_NO;
				$batchDataArr[$row->BATCH_ID]['BOOKING_NO']=$row->BOOKING_NO;
				$buyerArr[$row->BUYER_ID]=$row->BUYER_ID;
			}
			
			
			$fabric_shade = array(1 => "A", 2 => "B", 3 => "C", 4 => "D", 5 => "E");
			$buyer_point=sql_select( "SELECT A.BUYER_ID,B.RANGE_SERIAL,B.GRADE FROM BUYER_WISE_GRADE_MST A,BUYER_WISE_GRADE_DTLS B WHERE A.ID=B.MST_ID AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND A.BUYER_ID in(".implode(',',$buyerArr).")");
			$buyer_point_arr=array();
			foreach($buyer_point as $key=>$value)
			{
				$buyer_grade_arr[$value->BUYER_ID]=$fabric_shade[$value->GRADE];
			}					
			//return $buyer_grade_arr;
			
			if (count($is_exists) > 0) {
				$sqls = "SELECT  A.MACHINE_NO_ID,a.BUYER_ID,d.RECV_NUMBER,a.id as DTLS_ID, a.ORDER_ID as PO_BREAKDOWN_ID ,a.NO_OF_ROLL,a.TRANS_ID ,b.PRO_DTLS_ID,b.ROLL_WIDTH, b.ROLL_LENGTH,b.TOTAL_PENALTY_POINT, b.TOTAL_POINT, b.FABRIC_GRADE, b.COMMENTS, b.ROLL_STATUS,b.QC_DATE, a.PROD_ID ,b.id as QC_MST_ID ,d.id as MST_ID,d.LOCATION_ID as LOCATION,d.KNITTING_LOCATION_ID as SERVICE_LOCATION,d.KNITTING_COMPANY, d.SOURCE,d.COMPANY_ID, a.PROD_ID,a.GSM, a.WIDTH,  a.FABRIC_DESCRIPTION_ID,a.BODY_PART_ID,a.BATCH_ID,a.BARCODE_NO,b.ROLL_ID, b.ROLL_NO,a.RECEIVE_QNTY,b.ROLL_WEIGHT
				from INV_RECEIVE_MASTER d, PRO_FINISH_FABRIC_RCV_DTLS a,PRO_QC_RESULT_MST b ,pro_qc_result_dtls c 
				where d.id=a.mst_id   and d.status_active=1 and a.id=b.pro_dtls_id and b.id=c.mst_id and b.status_active=1 and c.status_active=1 and  a.status_active=1 and a.id=$search_data  and a.is_deleted=0  and d.entry_form=7";
				
				
				$sqls_result=sql_select($sqls);
				$production_qty=0;$qc_pass_qty=0;
				foreach ($sqls_result as $row) {
					$production_qty_arr[$row->DTLS_ID]=$row->RECEIVE_QNTY;
					$qc_pass_qty_arr[$row->DTLS_ID][$row->QC_MST_ID]=$row->ROLL_WEIGHT;
					$qcPassRollArr[$row->ROLL_NO]=$row->ROLL_NO;
					
					$qcDataArr[$row->DTLS_ID]=array(
						RECV_NUMBER=>$row->RECV_NUMBER,
						MST_ID=>$row->MST_ID,
						DTLS_ID=>$row->DTLS_ID,
						QC_MST_ID=>$row->QC_MST_ID,
						BUYER_ID=>$row->BUYER_ID,
						MACHINE_NO_ID=>$row->MACHINE_NO_ID,
						TOTAL_PENALTY_POINT=>$row->TOTAL_PENALTY_POINT,
						TOTAL_POINT=>$row->TOTAL_POINT,
						FABRIC_GRADE=>$row->FABRIC_GRADE,
						COMMENTS=>$row->COMMENTS,
						ROLL_STATUS=>$row->ROLL_STATUS,
						QC_DATE=>$row->QC_DATE,
						NO_OF_ROLL=>$row->NO_OF_ROLL,
						ROLL_WEIGHT=>$row->ROLL_WEIGHT,
						ROLL_LENGTH=>$row->ROLL_LENGTH,
						ROLL_WIDTH=>$row->ROLL_WIDTH*1,
						PROD_ID=>$row->PROD_ID,
						TRANS_ID=>$row->TRANS_ID,
						BARCODE_NO=>$row->BARCODE_NO,
						ROLL_ID=>$row->ROLL_ID*1,
						ROLL_NO=>$row->ROLL_NO,
						BATCH_ID=>$row->BATCH_ID,
						BODY_PART_ID=>$row->BODY_PART_ID,
						FABRIC_DESCRIPTION_ID=>$row->FABRIC_DESCRIPTION_ID,
						GSM=>$row->GSM,
						WIDTH=>$row->WIDTH,
						SOURCE=>$row->SOURCE,
						COMPANY_ID=>$row->COMPANY_ID,
						LOCATION=>$row->LOCATION,
						KNITTING_COMPANY=>$row->KNITTING_COMPANY,
						SERVICE_LOCATION=>$row->SERVICE_LOCATION,
						PO_BREAKDOWN_ID=>$row->PO_BREAKDOWN_ID
						
					);
				}
				
				
				$qc_mst_tble_id = 0;
				if (count($sqls_result) > 0 and $qc_update_id >0)//update............
				{
					foreach ($qcDataArr as $row) {
						$qc_mst_tble_id = $row[QC_MST_ID];
						$production_qty=$production_qty_arr[$row[DTLS_ID]];
						$qc_pass_qty=array_sum($qc_pass_qty_arr[$row[DTLS_ID]]);
						
						$return_array["index"]['mode'] = "update";
						$return_array["index"]['receive_no'] = $row[RECV_NUMBER];
						$return_array["index"]['mst_id'] = $row[MST_ID];
						$return_array["index"]['dtls_id'] = $row[DTLS_ID];
						$return_array["index"]['qc_mst_id'] = $row[QC_MST_ID];
						$return_array["index"]['buyer_id'] = $row[BUYER_ID];
						$return_array["index"]['buyer_name'] = $buyer_arr[$row[BUYER_ID]];
						$return_array["index"]['machine_no'] = $machine_arr[$row[MACHINE_NO_ID]].'';
						$return_array["index"]['machine_id'] = $row[MACHINE_NO_ID];
						$return_array["index"]['total_penalty_point'] = $row[TOTAL_PENALTY_POINT];
						$return_array["index"]['buyer_grade'] = $buyer_grade_arr[$row[BUYER_ID]];
						
						if (isset($row[TOTAL_POINT])) {
							$return_array["index"]['total_point'] = $row[TOTAL_POINT];
						} else {
							$return_array["index"]['total_point'] = 0;
						}
		
						if (isset($row[FABRIC_GRADE])) {
							$return_array["index"]['fabric_grade'] = $row[FABRIC_GRADE];
						} else {
							$return_array["index"]['fabric_grade'] = "";
						}
		
						if (isset($row[COMMENTS])) {
							$return_array["index"]['comments'] = $row[COMMENTS]." ";
						} else {
							$return_array["index"]['comments'] = "";
						}
		
						if (isset($row[ROLL_STATUS])) {
							$return_array["index"]['roll_status'] = $row[ROLL_STATUS];
						} else {
							$return_array["index"]['roll_status'] = 0;
						}
		
						if (isset($row[QC_DATE])) {
							$return_array["index"]['qc_date'] = $row[QC_DATE];
						} else {
							$return_array["index"]['qc_date'] = "";
						}
		
						
						$return_array["index"]['no_of_roll'] = $row[NO_OF_ROLL];
						$return_array["index"]['qc_pass_total_roll'] = count($qcPassRollArr);
						
						$return_array["index"]['roll_weight'] = $row[ROLL_WEIGHT];
						$return_array["index"]['roll_length'] = $row[ROLL_LENGTH];
						$return_array["index"]['roll_width'] = $row[ROLL_WIDTH];
						$return_array["index"]['prod_id'] = $row[PROD_ID];
						$return_array["index"]['trans_id'] = $row[TRANS_ID];
						$return_array["index"]['barcode_no'] = $row[BARCODE_NO];
						$return_array["index"]['roll_id'] = $row[ROLL_ID];
						$return_array["index"]['roll_no'] = $row[ROLL_NO];
						$return_array["index"]['batch_id'] = $row[BATCH_ID];
						$return_array["index"]['batch_no'] = $batchDataArr[$row[BATCH_ID]][BATCH_NO];
						$return_array["index"]['booking_no'] = $batchDataArr[$row[BATCH_ID]][BOOKING_NO];
						$return_array["index"]['color'] = "";
						$return_array["index"]['width_dia_id'] = 0;
						$return_array["index"]['width_dia_val'] = "";
						$return_array["index"]['prod_qnty'] = $production_qty;
						$return_array["index"]['qc_pass_qty'] = $qc_pass_qty;
	
						if($row[BODY_PART_ID]){
							$return_array["index"]['body_part_id'] = $row[BODY_PART_ID];
							$return_array["index"]['body_part'] = $body_part[$row[BODY_PART_ID]];
						}
						else
						{
							$return_array["index"]['body_part_id'] = 0;
							$return_array["index"]['body_part'] = "";
						}
						$return_array["index"]['fab_des_id'] = $row[FABRIC_DESCRIPTION_ID];
						$return_array["index"]['fab_des'] = $composition_arr[$row[FABRIC_DESCRIPTION_ID]];
						$return_array["index"]['gsm'] = $row[GSM];
						$return_array["index"]['width'] = $row[WIDTH];
						$return_array["index"]['is_sales'] = 0;
						$return_array["index"]['construction'] = "";
						$return_array["index"]['source'] = $row[SOURCE];
						
						$return_array["index"]['company_id'] = $row[COMPANY_ID];
						$return_array["index"]['location'] = $row[LOCATION];
						$return_array["index"]['serving_company'] = $row[KNITTING_COMPANY];
						$return_array["index"]['service_location'] = $row[SERVICE_LOCATION];
						$return_array["index"]['po_breakdown_id'] = $row[PO_BREAKDOWN_ID];
						$return_array["index"]['po_number'] = "";
						$return_array["index"]['job_number'] = "";
						$return_array["index"]['style_ref_no'] = "";
						$return_array["index"]['booking_without_order'] = 0;
						$return_array["index"]["array_ref_data"] = $this->finish_batch_ref_data_array(0, "", 2, $qc_mst_tble_id,$row[BUYER_ID]);
	
						$i++;
					}
					return $return_array;
				}
				else if(count($sqls_result) > 0)//save............
				{
					foreach ($qcDataArr as $row) {
						$qc_mst_tble_id = $row[QC_MST_ID];
						$production_qty=$production_qty_arr[$row[DTLS_ID]];
						$qc_pass_qty=array_sum($qc_pass_qty_arr[$row[DTLS_ID]]);
						
						
						$return_array["index"]['mode'] = "save";
						$return_array["index"]['receive_no'] = $row[RECV_NUMBER];
						$return_array["index"]['mst_id'] = $row[MST_ID];
						$return_array["index"]['dtls_id'] = $row[DTLS_ID];
						$return_array["index"]['qc_mst_id'] = $qc_mst_tble_id;
						$return_array["index"]['buyer_id'] = $row[BUYER_ID];
						$return_array["index"]['buyer_name'] = $buyer_arr[$row[BUYER_ID]];
						$return_array["index"]['machine_no'] = $machine_arr[$row[MACHINE_NO_ID]].'';
						$return_array["index"]['machine_id'] = $row[MACHINE_NO_ID];
						$return_array["index"]['total_penalty_point'] = 0;
						$return_array["index"]['buyer_grade'] = $buyer_grade_arr[$row[BUYER_ID]];
						
						if (isset($row[TOTAL_POINT])) {
							$return_array["index"]['total_point'] = 0;
						} else {
							$return_array["index"]['total_point'] = 0;
						}
		
						if (isset($row[FABRIC_GRADE])) {
							$return_array["index"]['fabric_grade'] = $row[FABRIC_GRADE];
						} else {
							$return_array["index"]['fabric_grade'] = "";
						}
		
						$return_array["index"]['comments'] = "";
		
						if (isset($row[ROLL_STATUS])) {
							$return_array["index"]['roll_status'] = $row[ROLL_STATUS];
						} else {
							$return_array["index"]['roll_status'] = 0;
						}
		
						$return_array["index"]['qc_date'] = "";
						
						$return_array["index"]['no_of_roll'] = $row[NO_OF_ROLL];
						$return_array["index"]['qc_pass_total_roll'] = count($qcPassRollArr);
						
						$return_array["index"]['roll_weight'] = $row[ROLL_WEIGHT];
						$return_array["index"]['roll_length'] = $row[ROLL_LENGTH];
						$return_array["index"]['roll_width'] = $row[ROLL_WIDTH];
						$return_array["index"]['prod_id'] = $row[PROD_ID];
						$return_array["index"]['trans_id'] = $row[TRANS_ID];
						$return_array["index"]['barcode_no'] = $row[BARCODE_NO];
						$return_array["index"]['roll_id'] = $row[ROLL_ID];
						$return_array["index"]['roll_no'] = $row[ROLL_NO];
						$return_array["index"]['batch_id'] = $row[BATCH_ID];
						$return_array["index"]['batch_no'] = $batchDataArr[$row[BATCH_ID]][BATCH_NO];
						$return_array["index"]['booking_no'] = $batchDataArr[$row[BATCH_ID]][BOOKING_NO];
						$return_array["index"]['color'] = "";
						$return_array["index"]['width_dia_id'] = 0;
						$return_array["index"]['width_dia_val'] = "";
						$return_array["index"]['prod_qnty'] = $production_qty;
						$return_array["index"]['qc_pass_qty'] = $qc_pass_qty;
	
						if($row[BODY_PART_ID]){
							$return_array["index"]['body_part_id'] = $row[BODY_PART_ID];
							$return_array["index"]['body_part'] = $body_part[$row[BODY_PART_ID]];
						}
						else
						{
							$return_array["index"]['body_part_id'] = 0;
							$return_array["index"]['body_part'] = "";
						}
						$return_array["index"]['fab_des_id'] = $row[FABRIC_DESCRIPTION_ID];
						$return_array["index"]['fab_des'] = $composition_arr[$row[FABRIC_DESCRIPTION_ID]];
						$return_array["index"]['gsm'] = $row[GSM];
						$return_array["index"]['width'] = $row[WIDTH];
						$return_array["index"]['is_sales'] = 0;
						$return_array["index"]['construction'] = "";
						$return_array["index"]['source'] = $row[SOURCE];
						
						$return_array["index"]['company_id'] = $row[COMPANY_ID];
						$return_array["index"]['location'] = $row[LOCATION];
						$return_array["index"]['serving_company'] = $row[KNITTING_COMPANY];
						$return_array["index"]['service_location'] = $row[SERVICE_LOCATION];
						$return_array["index"]['po_breakdown_id'] = $row[PO_BREAKDOWN_ID];
						$return_array["index"]['po_number'] = "";
						$return_array["index"]['job_number'] = "";
						$return_array["index"]['style_ref_no'] = "";
						$return_array["index"]['booking_without_order'] = 0;
						$return_array["index"]["array_ref_data"] = $this->finish_batch_ref_data_array(0, "", 2, '',$row[BUYER_ID]);
	
						$i++;
					}
					return $return_array;
				}
				else//save............
				{
					
					$finishSqlResult = sql_select("select a.RECV_NUMBER,a.ID,a.COMPANY_ID,a.KNITTING_COMPANY,b.PROD_ID,b.FABRIC_DESCRIPTION_ID,b.WIDTH,a.RECV_NUMBER,b.BATCH_ID,b.GSM,b.ROLL_ID,b.ROLL_NO,b.BARCODE_NO,b.COLOR_ID,b.IS_SALES,b.TRANS_ID,b.RECEIVE_QNTY,b.BODY_PART_ID,b.QC_QNTY,b.REMARKS,b.NO_OF_ROLL,b.BUYER_ID,b.ID AS PRO_DTLS_ID,c.BATCH_NO,b.MACHINE_NO_ID from INV_RECEIVE_MASTER a,PRO_FINISH_FABRIC_RCV_DTLS b,PRO_BATCH_CREATE_MST c where a.id=b.mst_id and a.ENTRY_FORM = 7 and c.id=b.batch_id and b.id=".trim($search_data)."");
					
					foreach($finishSqlResult as $row){
						$return_array["index"]['mode'] = "save";
						$return_array["index"]['receive_no'] = $row->RECV_NUMBER;
						$return_array["index"]['mst_id'] = $row->ID;
						$return_array["index"]['dtls_id'] = $row->PRO_DTLS_ID;
						$return_array["index"]['buyer_id'] = $row->BUYER_ID;
						$return_array["index"]['buyer_name'] = $buyer_arr[$row->BUYER_ID];
						$return_array["index"]['machine_no'] = $machine_arr[$row->MACHINE_NO_ID].'';
						$return_array["index"]['machine_id'] = $row->MACHINE_NO_ID;
						$return_array["index"]['buyer_grade'] = $buyer_grade_arr[$row->BUYER_ID];

						$return_array["index"]['qc_mst_id'] = 0;
						$return_array["index"]['qc_pass_total_roll'] = 0;
						$return_array["index"]['no_of_roll'] = $row->NO_OF_ROLL*1;
						$return_array["index"]['roll_weight'] = 0;
						$return_array["index"]['roll_length'] = 0;
						$return_array["index"]['roll_width'] = $row->WIDTH;
						$return_array["index"]['prod_id'] = $row->PROD_ID;
						$return_array["index"]['trans_id'] = $row->TRANS_ID;
						$return_array["index"]['total_penalty_point'] = 0;
						$return_array["index"]['total_point'] = 0;
						$return_array["index"]['fabric_grade'] = "";
						$return_array["index"]['comments'] = '';
						$return_array["index"]['roll_status'] = 0;
						$return_array["index"]['qc_date'] = "";
						$return_array["index"]['barcode_no'] = $row->BARCODE_NO;
						$return_array["index"]['roll_id'] = $row->ROLL_ID;
						$return_array["index"]['roll_no'] = $row->ROLL_NO;
						
						$return_array["index"]['batch_id'] = $row->BATCH_ID;
						$return_array["index"]['batch_no'] = $batchDataArr[$row->BATCH_ID][BATCH_NO];
						$return_array["index"]['booking_no'] = $batchDataArr[$row->BATCH_ID][BOOKING_NO];
						if($row->COLOR_ID){
							$return_array["index"]['color'] = $color_arr[$row->COLOR_ID];
						}
						else
						{
							$return_array["index"]['color'] = '';
						}
						$return_array["index"]['width_dia_id'] = 0;
						$return_array["index"]['width_dia_val'] = "";
						$return_array["index"]['qc_pass_qty'] = 0;
						$return_array["index"]['prod_qnty'] = $row->RECEIVE_QNTY*1;
						if($row->BODY_PART_ID){
							$return_array["index"]['body_part_id'] = $row->BODY_PART_ID;
							$return_array["index"]['body_part'] = $body_part[$row->BODY_PART_ID];
						}
						else
						{
							$return_array["index"]['body_part_id'] = 0;
							$return_array["index"]['body_part'] = "";
						}
			
						$return_array["index"]['prod_id'] = $row->PROD_ID;
						$return_array["index"]['fab_des_id'] = $row->FABRIC_DESCRIPTION_ID;
						$return_array["index"]['fab_des'] = $composition_arr[$row->FABRIC_DESCRIPTION_ID];
						$return_array["index"]['gsm'] = $row->GSM;
						$return_array["index"]['width'] = $row->WIDTH;
					
						$return_array["index"]['is_sales'] = $row->IS_SALES;
						$return_array["index"]['construction'] = '';
			
			
						if (isset($row->SOURCE)) {
							$return_array["index"]['source'] = $row->SOURCE;
						} else {
							$return_array["index"]['source'] = 0;
						}
			
						if (isset($row->COMPANY_ID)) {
							$return_array["index"]['company_id'] = $row->COMPANY_ID;
						} else {
							$return_array["index"]['company_id'] = 0;
						}
						if (isset($row->LOCATION)) {
							$return_array["index"]['location'] = $row->LOCATION;
						} else {
							$return_array["index"]['location'] = 0;
						}
						
						if (isset($row->KNITTING_COMPANY)) {
							$return_array["index"]['serving_company'] = $row->KNITTING_COMPANY;
						} else {
							$return_array["index"]['serving_company'] = 0;
						}
			
						if (isset($row->SERVICE_LOCATION)) {
							$return_array["index"]['service_location'] = $row->SERVICE_LOCATION;
						} else {
							$return_array["index"]['service_location'] = 0;
						}
						$return_array["index"]['po_breakdown_id'] = '';
						$return_array["index"]['po_number'] = "";
						$return_array["index"]['job_number'] = "";
						$return_array["index"]['style_ref_no'] = "";
						$return_array["index"]['po_number'] = "";
						$return_array["index"]['style_ref_no'] = "";
						$return_array["index"]['job_number'] = "";
						$return_array["index"]['booking_without_order'] = 0;
					}				
					
					if(count($finishSqlResult)>0){
					$return_array["index"]["array_ref_data"] = $this->finish_batch_ref_data_array(0, "", 2, '',$row->BUYER_ID);
					}

					
					return $return_array;
					$i++;
				
				
				}//end else;
	
				
			}
			
			
			
		}

	}

	public function finish_batch_ref_data_array($compId = "0", $arrs, $type, $qc_mst_tble_id,$buyer_id = "") {

		$this->load->model('android/common/array_function');
		$shade_array = $this->array_function->get_fabric_shade_array();
		$fabric_shade=array();
		foreach($shade_array[data_arr] as $row){
			$fabric_shade[$row['key']]=$row[value];
		}
		//$fabric_shade = array(1 => "A", 2 => "B", 3 => "C", 4 => "D", 5 => "E");
		
		
		
		$defect_inchi_array = $this->array_function->get_knit_finish_defect_inchi_array();
		$knit_defect_inchi_array=array();
		foreach($defect_inchi_array[data_arr] as $row){
			$knit_defect_inchi_array[$row['key']]=$row[value];
		}
		//$knit_defect_inchi_array = array(1 => 'Defect=<3" : 1', 2 => 'Defect=<6" but >3" : 2', 3 => 'Defect=<9" but >6" : 3', 4 => 'Defect>9" : 4', 5 => 'Hole<1" : 2', 6 => 'Hole>1" : 4');

		
		$qc_defect_array = $this->array_function->get_ovservation_knit_finish_qc_defect_array();
		$ovservation_knit_defect_array=array();
		foreach($qc_defect_array[data_arr] as $row){
			$ovservation_knit_defect_array[$row['key']]=$row[value];
		}
		//$ovservation_knit_defect_array=array(1=>"Fly Conta",2=>"PP conta",3=>"Patta/Barrie",4=>"Needle Mark",5=>"Sinker Mark",6=>"thick-thin",7=>"neps/knot",8=>"white speck",9=>"Black Speck",10=>"Star Mark",11=>"Dia/Edge Mark",12=>"Dead fibre",13=>"Running shade",14=>"Hairiness",15=>"crease mark",16=>"Uneven",17=>"Padder Crease",18=>"Absorbency",19=>"Bowing",20=>"Handfeel",21=>"Dia Up-down",22=>"Cut hole",23=>"Snagging/Pull out",24=>"Pin Hole",25=>"Bad Smell",26=>"Bend Mark");
		
		
			
		$knit_defect_array = return_library_array("select defect_name, short_name from  lib_defect_name where type=1 order by defect_name", "defect_name", "short_name");


		//return $defect_name_arr;
		$grade_arr = array();
		$knit_defect_arr = array();
		$defect_arr = array();
		$observation_arr = array();

		if (!$compId) {$compId = 1;}

		/* $grade_sql = "SELECT FABRIC_GRADE, GET_UPVALUE_FIRST,GET_UPVALUE_SECOND from variable_settings_production where COMPANY_NAME='$compId' AND VARIABLE_LIST = 36 and status_active=1 and is_deleted=0 ";
		foreach (sql_select($grade_sql) as $v) {
			for ($kk = $v->GET_UPVALUE_FIRST; $kk <= $v->GET_UPVALUE_SECOND; $kk++) {
				$grade_arr[] = array(
					serial=>$kk,
					grade=>$v->FABRIC_GRADE
				);
			}
		} */

		$buyer_point=sql_select( "SELECT A.BUYER_ID,B.RANGE_SERIAL,B.GRADE FROM BUYER_WISE_GRADE_MST A,BUYER_WISE_GRADE_DTLS B WHERE A.ID=B.MST_ID AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND A.BUYER_ID =$buyer_id order by B.RANGE_SERIAL");
		foreach($buyer_point as $key=>$value)
		{
			$grade_arr[] = array(
				serial=>$value->RANGE_SERIAL,
				grade=>$fabric_shade[$value->GRADE]
			);
		}	


		if ($arrs) {
			foreach ($knit_defect_array as $k => $v) {
				$def_id = $k;
				if (isset($arrs[$def_id]["DEFECT_COUNT"])) {
					$count = $arrs[$def_id]["DEFECT_COUNT"];
				} else {
					$count = 0;
				}

				if (isset($arrs[$def_id]["FOUND_IN_INCH"])) {
					$inchs = $arrs[$def_id]["FOUND_IN_INCH"];
				} else {
					$inchs = 0;
				}

				if (isset($arrs[$def_id]["PENALTY_POINT"])) {
					$ttl_point = $arrs[$def_id]["PENALTY_POINT"];
				} else {
					$ttl_point = 0;
				}

				$defect_arr[] =array(
				   ID=>$def_id,
				   DEFECT_NAME=>$v,
				   DEFECT_COUNT=>$count,
				   FOUND_IN_INCH=>$inchs,
				   PENALTY_POINT=>$ttl_point,
				);
			}

		}
		else {
			if ($qc_mst_tble_id) {
				$dtls_sql = "SELECT  DEFECT_NAME, DEFECT_COUNT, FOUND_IN_INCH, PENALTY_POINT FROM pro_qc_result_dtls Where MST_ID  in($qc_mst_tble_id)";
				foreach (sql_select($dtls_sql) as $val) {
					$defect_wise_others[$val->DEFECT_NAME]["DEFECT_COUNT"] = $val->DEFECT_COUNT;
					$defect_wise_others[$val->DEFECT_NAME]["FOUND_IN_INCH"] = $val->FOUND_IN_INCH;
					$defect_wise_others[$val->DEFECT_NAME]["PENALTY_POINT"] = $val->PENALTY_POINT;
				}

				foreach ($knit_defect_array as $k => $v) {
					$DEFECT_COUNT = 0;
					if (isset($defect_wise_others[$k]["DEFECT_COUNT"])) {
						$DEFECT_COUNT = $defect_wise_others[$k]["DEFECT_COUNT"];
					}

					$FOUND_IN_INCH = 0;
					if (isset($defect_wise_others[$k]["FOUND_IN_INCH"])) {
						$FOUND_IN_INCH = $defect_wise_others[$k]["FOUND_IN_INCH"];
					}

					$PENALTY_POINT = 0;
					if (isset($defect_wise_others[$k]["PENALTY_POINT"])) {
						$PENALTY_POINT = $defect_wise_others[$k]["PENALTY_POINT"];
					}

					$defect_arr[] =array(
					   ID=>$k,
					   DEFECT_NAME=>$v,
					   DEFECT_COUNT=>$DEFECT_COUNT,
					   FOUND_IN_INCH=>$FOUND_IN_INCH,
					   PENALTY_POINT=>$PENALTY_POINT
					);
					
				}

			} else {
				foreach ($knit_defect_array as $k => $v) {
					$defect_arr[] =array(
					   ID=>$k,
					   DEFECT_NAME=>$v,
					   DEFECT_COUNT=>0,
					   FOUND_IN_INCH=>0,
					   PENALTY_POINT=>0
					);
					
				}

			}

		}


		if ($qc_mst_tble_id) {
			$dtls_sql2 = "SELECT ID, DEFECT_NAME, FOUND_IN_INCH,DEPARTMENT FROM PRO_QC_RESULT_DTLS WHERE MST_ID in($qc_mst_tble_id) AND STATUS_ACTIVE=1 AND IS_DELETED=0 AND  FORM_TYPE =2";

			$observation_data_arr = array();
			foreach (sql_select($dtls_sql2) as $val) {
				$observation_data_arr[$val->DEFECT_NAME]["FOUND_IN_INCH"] = $val->FOUND_IN_INCH;
				$observation_data_arr[$val->DEFECT_NAME]["DEPARTMENT"] = $val->DEPARTMENT;
			}

			foreach ($ovservation_knit_defect_array as $k => $v) {
				$FOUND_IN_INCH = 0;
				if (isset($observation_data_arr[$k]["FOUND_IN_INCH"])) {
					$FOUND_IN_INCH = $observation_data_arr[$k]["FOUND_IN_INCH"];
				}

				$DEPARTMENT = 0;
				if (isset($observation_data_arr[$k]["DEPARTMENT"])) {
					$DEPARTMENT = $observation_data_arr[$k]["DEPARTMENT"];
				}
				$observation_arr[] =array(
				   ID=>$k,
				   DEFECT_NAME=>$v,
				   FOUND_IN_INCH=>$FOUND_IN_INCH,
				   DEPARTMENT=>$DEPARTMENT
				);
			}
		}
		else {
			foreach ($ovservation_knit_defect_array as $k => $v) {
				$observation_arr[] =array(
				   ID=>$k,
				   DEFECT_NAME=>$v,
				   FOUND_IN_INCH=>0,
				   DEPARTMENT=>0
				);
				
				
			}

		}

		foreach ($knit_defect_inchi_array as $k => $v) {
			$knit_defect_arr[] =array(
			   ID=>$k,
			   INCH_NAME=>$v
			);
			
			
		}
		$data_array = array("defect" => $defect_arr,"grade" => $grade_arr, 'observation' => $observation_arr);
		return $data_array;

	}


	public function save_update_finish_fabric_qc_by_batch($save_obj) {
		$response_obj = json_decode($save_obj);
		 //return $response_obj;
		if ($this->db->dbdriver == 'mysqli') {
			$pc_date_time = date("Y-m-d H:i:s", time());
		} else {
			$pc_date_time = date("d-M-Y h:i:s A", time());
		}
		
		
		if ($response_obj->status == true) {
			$INSERTED_BY = $response_obj->data->index->INSERTED_BY;
			$MST_ID = $response_obj->MST_ID;
			$DTLS_ID = $response_obj->DTLS_ID;
			$QC_MST_ID = $response_obj->QC_MST_ID;
			$BATCH_ID = $response_obj->data->index->BATCH_ID;
			$ROLL_WT_KG = $response_obj->data->index->ROLL_WT_KG;
			$ROLL_WT_KG = $response_obj->data->index->ROLL_WT_KG;
			$ACTUAL_GSM = $response_obj->data->index->ACTUAL_GSM;
			$ACTUAL_DIA = $response_obj->data->index->ACTUAL_DIA;
			$TOTAL_PENALTY_POINT = $response_obj->data->index->TOTAL_PENALTY_POINT;
			$TOTAL_POINT = $response_obj->data->index->TOTAL_POINT;
			$FABRIC_GRADE = $response_obj->data->index->FABRIC_GRADE;
			$LENGTH_PERCENT = $response_obj->data->index->LENGTH_PERCENT;
			$WIDTH_PERCENT = $response_obj->data->index->WIDTH_PERCENT;
			$TWISTING_PERCENT = $response_obj->data->index->TWISTING_PERCENT;
			$ROLL_WT_KG = $response_obj->data->index->ROLL_WT_KG;
			$ROLL_WT_YDS = $response_obj->data->index->ROLL_WT_YDS;
			$COMMENTS = $response_obj->data->index->COMMENTS;
			$REJECT_QTY = $response_obj->data->index->REJECT_QTY;
			
			$already_qc=0;$already_roll=0;
			//if($QC_MST_ID!=0){
				$already_qc=return_field_value("sum(roll_weight) as roll_weight", "pro_qc_result_mst", "PRO_DTLS_ID=$DTLS_ID and status_active=1 and is_deleted=0", "roll_weight") ;
				$already_roll=return_field_value("count(ID) as ids", "pro_qc_result_mst", "PRO_DTLS_ID=$DTLS_ID and status_active=1 and is_deleted=0", "ids") ;
				$nu_of_roll_count = $this->db->query("select count(b.id) as cnt from pro_finish_fabric_rcv_dtls a,pro_qc_result_mst b where a.id=b.pro_dtls_id and a.status_active=1 and b.status_active=1 and a.batch_id=".$BATCH_ID)->row();

				$nu_of_roll_count=$nu_of_roll_count->CNT*1;
				
			//}
			
			$finish_dtls_arr = sql_select("SELECT RECEIVE_QNTY, NO_OF_ROLL, REJECT_QTY, GREY_USED_QTY from PRO_FINISH_FABRIC_RCV_DTLS where status_active=1 and id=$DTLS_ID  and is_deleted=0");
			foreach($finish_dtls_arr as $row) {
				$no_of_roll = $row->NO_OF_ROLL;
				$qc_pass_qty = $row->RECEIVE_QNTY;
				$reject_qty_production = $row->REJECT_QTY;
				$grey_used_qty = $row->GREY_USED_QTY;
			}
		
			$this->db->trans_start();
			
			if ($response_obj->mode == "save") {
				
				if($no_of_roll<($already_roll+1))
				{
					return $resultset["status"] = "Roll over than Production No of Roll";
				}
				if($qc_pass_qty<($already_qc+$ROLL_WT_KG))
				{
					return $resultset["status"] = "Weight Qnty over than Qc Pass Qty";
				}
				
				
				$qc_mst_id = return_next_id_by_sequence("PRO_QC_RESULT_MST_SEQ", "PRO_QC_RESULT_MST", "", "", 0, "", 0, 0, 0, 0, 0, 0, 0);
				$auto_roll_gen=$nu_of_roll_count+1;
				$qc_mst_arr = array(
					ID =>$qc_mst_id, 
					PRO_DTLS_ID =>$DTLS_ID, 
					ROLL_MAINTAIN =>0, 
					ROLL_ID =>'', 
					ROLL_NO =>$auto_roll_gen,
					ACTUAL_GSM =>$ACTUAL_GSM,
					ACTUAL_DIA =>$ACTUAL_DIA,
					LENGTH_PERCENT =>$LENGTH_PERCENT,
					WIDTH_PERCENT =>$WIDTH_PERCENT,
					TWISTING_PERCENT =>$TWISTING_PERCENT,
					ROLL_WEIGHT =>$ROLL_WT_KG,
					REJECT_QNTY =>$REJECT_QTY,
					ROLL_LENGTH =>$ROLL_WT_YDS, 
					TOTAL_PENALTY_POINT =>$TOTAL_PENALTY_POINT, 
					TOTAL_POINT =>$TOTAL_POINT, 
					FABRIC_GRADE =>$FABRIC_GRADE, 
					COMMENTS =>$COMMENTS, 
					INSERTED_BY =>$INSERTED_BY, 
					INSERT_DATE =>$pc_date_time
				);
				
				//print_r($qc_mst_arr);die;
				
				$this->db->insert("PRO_QC_RESULT_MST",$qc_mst_arr);
				
				
				// Defect.......................
					$defect_list_data = $response_obj->data->DEFECT_LIST;
					foreach ($defect_list_data as $val) {
					$qc_dtls_id = return_next_id_by_sequence("PRO_QC_RESULT_DTLS_SEQ", "PRO_QC_RESULT_DTLS", "", "", 0, "", 0, 0, 0, 0, 0, 0, 0);
					$qc_defect_dtls_arr = array(
						'ID' => $qc_dtls_id,
						'MST_ID' => $qc_mst_id,
						'DEFECT_NAME' => $val->DEFECT_ID,
						'DEFECT_COUNT' => $val->COUNT,
						'FOUND_IN_INCH' => $val->INCH_ID,
						'PENALTY_POINT' => $val->PENALTY,
						'INSERTED_BY' => $INSERTED_BY,
						'INSERT_DATE' => $pc_date_time,
					);
					$this->db->insert("PRO_QC_RESULT_DTLS",$qc_defect_dtls_arr);
				}
				// Observasion................
				$obs_dtls_data = $response_obj->data->OBSERVATION_LIST;
				foreach ($obs_dtls_data as $val) {
					$qc_dtls_id = return_next_id_by_sequence("PRO_QC_RESULT_DTLS_SEQ", "PRO_QC_RESULT_DTLS", "", "", 0, "", 0, 0, 0, 0, 0, 0, 0);
					$qc_obs_dtls_arr = array(
						'ID' => $qc_dtls_id,
						'MST_ID' => $qc_mst_id,
						'DEFECT_NAME' => $val->OBS_ID,
						'FOUND_IN_INCH' => $val->OBS_INCH,
						'DEPARTMENT' => $val->OBS_DEPARTMENT,
						'FORM_TYPE' => 2,
						'INSERTED_BY' => $INSERTED_BY,
						'INSERT_DATE' => $pc_date_time,
					);
					$this->db->insert('PRO_QC_RESULT_DTLS',$qc_obs_dtls_arr);
				}

				//update dtls table to adjust reject quantity here
				$reject_qty_production = $reject_qty_production + $REJECT_QTY;
				$production_dtls_mst_arr = array(
					REJECT_QTY =>$reject_qty_production,
					UPDATE_DATE =>$pc_date_time, 
					UPDATED_BY =>$INSERTED_BY
				);

				$this->db->update("PRO_FINISH_FABRIC_RCV_DTLS", $production_dtls_mst_arr, array('ID' => $DTLS_ID));

				
				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					return $resultset["status"] = "Save Failed";
				} else {
					$this->db->trans_commit();
					$this->db->trans_complete();
					return $resultset["status"] = "Save Successful";
				}
				
				
			}//save;
			else if($response_obj->mode == "update"){
				
				if($no_of_roll<($already_roll))
				{
					return "Roll over than Production No of Roll";
				}
				if($qc_pass_qty<($already_qc+$ROLL_WT_KG))
				{
					return "Weight Qnty over than Qc Pass Qty";
				}
				
				
				$qc_mst_arr = array(
					ROLL_MAINTAIN =>0, 
					ACTUAL_GSM =>$ACTUAL_GSM,
					ACTUAL_DIA =>$ACTUAL_DIA,
					LENGTH_PERCENT =>$LENGTH_PERCENT,
					WIDTH_PERCENT =>$WIDTH_PERCENT,
					TWISTING_PERCENT =>$TWISTING_PERCENT,
					ROLL_WEIGHT =>$ROLL_WT_KG,
					REJECT_QNTY =>$REJECT_QTY,
					ROLL_LENGTH =>$ROLL_WT_YDS, 
					TOTAL_PENALTY_POINT =>$TOTAL_PENALTY_POINT, 
					TOTAL_POINT =>$TOTAL_POINT, 
					FABRIC_GRADE =>$FABRIC_GRADE, 
					UPDATE_DATE =>$pc_date_time, 
					UPDATE_BY =>$INSERTED_BY
				);
				$up_mst = $this->db->update("PRO_QC_RESULT_MST", $qc_mst_arr, array('ID' => $QC_MST_ID));
				
				// Defect.......................
					$dtls_del = $this->db->query("DELETE from PRO_QC_RESULT_DTLS WHERE MST_ID =$QC_MST_ID");

					$defect_list_data = $response_obj->data->DEFECT_LIST;
					foreach ($defect_list_data as $val) {
					$qc_dtls_id = return_next_id_by_sequence("PRO_QC_RESULT_DTLS_SEQ", "PRO_QC_RESULT_DTLS", "", "", 0, "", 0, 0, 0, 0, 0, 0, 0);
					$qc_defect_dtls_arr = array(
						'ID' => $qc_dtls_id,
						'MST_ID' => $QC_MST_ID,
						'DEFECT_NAME' => $val->DEFECT_ID,
						'DEFECT_COUNT' => $val->COUNT,
						'FOUND_IN_INCH' => $val->INCH_ID,
						'PENALTY_POINT' => $val->PENALTY,
						'INSERTED_BY' => $INSERTED_BY,
						'INSERT_DATE' => $pc_date_time,
					);
					$this->db->insert('PRO_QC_RESULT_DTLS',$qc_defect_dtls_arr);
				}
				// Observasion................
				$obs_dtls_data = $response_obj->data->OBSERVATION_LIST;
				foreach ($obs_dtls_data as $val) {
					$qc_dtls_id = return_next_id_by_sequence("PRO_QC_RESULT_DTLS_SEQ", "PRO_QC_RESULT_DTLS", "", "", 0, "", 0, 0, 0, 0, 0, 0, 0);
					$qc_obs_dtls_arr = array(
						'ID' => $qc_dtls_id,
						'MST_ID' => $QC_MST_ID,
						'DEFECT_NAME' => $val->OBS_ID,
						'FOUND_IN_INCH' => $val->OBS_INCH,
						'DEPARTMENT' => $val->OBS_DEPARTMENT,
						'FORM_TYPE' => 2,
						'INSERTED_BY' => $INSERTED_BY,
						'INSERT_DATE' => $pc_date_time,
					);
					$this->db->insert('PRO_QC_RESULT_DTLS',$qc_obs_dtls_arr);
				}
			
				
				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					return $resultset["status"] = "Update Failed";
				} else {
					$this->db->trans_commit();
					$this->db->trans_complete();
					return $resultset["status"] = "Update Successful";
				}
				
				
				
			}//update
			
			
		}
		else
		{
			return 0;
		}

	}//end method;


















}//class;
