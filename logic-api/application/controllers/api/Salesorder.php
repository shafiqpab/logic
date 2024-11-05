<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package    CodeIgniter - Android Apps
 * @category   AndroidApps
 * @author     Jahid Hasan <jahid@logicsoftbd.com>
 * @copyright  2017 Logic Software Ltd Development Group
 */
require APPPATH . '/libraries/REST_Controller.php';

class Salesorder extends REST_Controller {
	public $pc_date_time;
	function __construct() {
		parent::__construct();
		$this->load->model('salesorder_model');
		$this->pc_date_time = date("d-M-Y h:i:s A");
	}
	
	function order_get()
    {
		$fromDate="";
		$toDate="";
		if($this->get('from_date') !="" && $this->validateDate($this->get('from_date')) && $this->validateDate($this->get('to_date')) && $this->get('to_date') !=""){
			$fromDate=$this->get('from_date');
		    $toDate=$this->get('to_date');
		}
		else if($this->validateDate($this->get('from_date')) && !$this->validateDate($this->get('to_date'))){
			$fromDate=$this->get('from_date');
		    $toDate=$this->get('from_date');
		}
		else if(!$this->validateDate($this->get('from_date')) && $this->validateDate($this->get('to_date'))){
			$fromDate=$this->get('to_date');
		    $toDate=$this->get('to_date');
		}
		
		if ($fromDate=="" && $toDate == "" && $this->get('program_no')== "") 
		{
			$this->response('Date Range or Program No Is Required', 400);
		}
		if($this->compareDate($fromDate,$toDate)===0)
		{
			$this->response('To date is greater than From date', 400);
		}
		$data = $this->salesorder_model->get_sales_order_data_info($fromDate,$toDate,$this->get('program_no'));
        $this->response($data);
    }
	
	function plan_get()
    {
		$fromDate="";
		$toDate="";
		if($this->get('from_date') !="" && $this->validateDate($this->get('from_date')) && $this->validateDate($this->get('to_date')) && $this->get('to_date') !=""){
			$fromDate=$this->get('from_date');
		    $toDate=$this->get('to_date');
		}
		else if($this->validateDate($this->get('from_date')) && !$this->validateDate($this->get('to_date'))){
			$fromDate=$this->get('from_date');
		    $toDate=$this->get('from_date');
		}
		else if(!$this->validateDate($this->get('from_date')) && $this->validateDate($this->get('to_date'))){
			$fromDate=$this->get('to_date');
		    $toDate=$this->get('to_date');
		}
		
		$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

		$shift_name=array(1=>"A",2=>"B",3=>"C");
		//$knitting_plan_data=json_decode(file_get_contents('http://rms.careerclub.net/productions?_token=logic&from='.$fromDate.'&to='.$toDate),true);
		//$knitting_plan_data=json_decode(file_get_contents('http://49.0.39.93/tosrifa/productions?_token=logic&from='.$fromDate.'&to='.$toDate),true);
		$knitting_plan_data=json_decode(file_get_contents('http://192.168.100.26/tosrifa/productions?_token=logic&from='.$fromDate.'&to='.$toDate),true);
		if(empty($knitting_plan_data))
		{
			echo "No Data Found";die;
		}
		echo "<pre/>";
		print_r($knitting_plan_data);die;

		$fabric_store_auto_update=0;
		$cbo_store_name=0;
		$cbo_uom=12;
		$floor_id=12;
		$no_of_roll=0;
		

		foreach($knitting_plan_data['data'] as $mst_row_arr)
		{
			//echo 333;
			$company_name		=$mst_row_arr['Company Name'];
			$production_basis	=$mst_row_arr['Production Basis'];
			$production_date	=$mst_row_arr['Production Date'];
			$txt_receive_date	=date("d-M-Y",strtotime($mst_row_arr['Production Date']));
			$knitting_source	=$mst_row_arr['Knitting Source'];
			$knitting_company	=$mst_row_arr['Knitting Company'];
			$buyer_name			=$mst_row_arr['Buyer'];
			$production_id		=$mst_row_arr['Production_id'];
			$remarks			=$mst_row_arr['Remarks'];
			// fabic part
			$plan_id			=$mst_row_arr['Fabric Part']['Plan id'];
			$body_part			=$mst_row_arr['Fabric Part']['Body Part'];
			$uom				=$mst_row_arr['Fabric Part']['UOM'];
			$fabric_color		=$mst_row_arr['Fabric Part']['Fabric Color'];
			$fabric_description	=$mst_row_arr['Fabric Part']['Fabric Description'];
			$machine_gauge		=$mst_row_arr['Fabric Part']['Machine gauge'];
			$gsm				=$mst_row_arr['Fabric Part']['GSM'];
			$dia				=$mst_row_arr['Fabric Part']['Dia'];
			$receive_qnty		=$mst_row_arr['Fabric Part']['Grey Prod. Qnty'];
			$machine_dia		=$mst_row_arr['Fabric Part']['Machine Dia'];
			$machine_id			=return_field_value( "id", "lib_machine_name", "machine_no='".$mst_row_arr['Fabric Part']['Machine No']."' and category_id=1 and status_active=1 and is_deleted=0 and is_locked=0", "id" );//$mst_row_arr['Fabric 
		/*	$fabric_color='';
			foreach($mst_row_arr['Fabric Part']['Fabric Color'] as $clr)
			{
				if($clr!='')
				{
					$yarn_count.=return_field_value( "id", "lib_color", "color_name='".$clr."' and is_deleted=0 and status_active=1", "id" ).",";
				}
			}
			$yarn_count=chop($yarn_count,",");*/

			// yarn count from api
			$yarn_count='';
			foreach($mst_row_arr['Fabric Part']['Yarn Count'] as $count)
			{
				if($count!='')
				{
					$yarn_count.=return_field_value( "id", "lib_yarn_count", "yarn_count='".$count."' and is_deleted=0 and status_active=1", "id" ).",";
				}
			}
			$yarn_count=chop($yarn_count,",");
			
			// lot from api
			$yarn_lot='';
			foreach($mst_row_arr['Fabric Part']['Yarn Lot'] as $lot)
			{
				$yarn_lot=$lot.",";
			}
			$yarn_lot=chop($yarn_lot,",");
			
			$brand='';
			foreach($mst_row_arr['Fabric Part']['Brand'] as $p_brand)
			{
				$brand=$p_brand;//.",";
			}
			
			//$brand=chop($brand,",");
			
			// strich length from api
			$stritch_length='';
			foreach($mst_row_arr['Fabric Part']['Stitch Length'] as $s_length)
			{
				$stritch_length=$s_length.",";
			}
			
			$stritch_length=chop($stritch_length,",");
			$shift_id=0;
			
			$planning_data= $this->salesorder_model->get_plan_data_info($plan_id);
			
			foreach($planning_data as $p_data)
			{
				$company_id			=$p_data->COMPANY_ID;			
				$po_id				=$p_data->PO_ID;
				$bodypart_id		=$p_data->BODY_PART_ID;
				$color_id			=$p_data->FABRIC_COLOR;
				$detarmination_id	=$p_data->DETERMINATION_ID;
				$color_range 		=$p_data->COLOR_RANGE;
				$is_sales			=$p_data->IS_SALES;
				$knitting_com		=$p_data->KNITTING_PARTY;
				$knitting_source	=$p_data->KNITTING_SOURCE;
				$within_group		=$p_data->WITHIN_GROUP;			
				//$machine_id			=$p_data->MACHINE_ID;
				//$location_id		=$p_data->LOCATION_ID;
				$location_id		=5;
				$store_id=4;
				//$store_id=return_field_value( "a.id", "lib_store_location a, lib_store_location_category b", "a.id= b.store_location_id and a.company_id=$company_id and b.category_type=13 and a.status_active=1 and a.is_deleted=0", "id" );
				$plan_booking_no	=$p_data->BOOKING_NO;
				
				if($within_group==1)
				{
					$buyer_id_data= $this->salesorder_model->get_buyer_from_booking($plan_booking_no);
					$buyer_id=$buyer_id_data->BUYER_ID;
				}		
				else $buyer_id=$p_data->BUYER_ID;
			}
		
			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master","",0);
			$new_grey_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master","",1,$company_id,'GPE',2,date("Y",time()),13 ));
			
			// ##########################################################data for inv_receive_master############################################################################
			$knitting_master_data['ID'] 					= $id;
			$knitting_master_data['RECV_NUMBER_PREFIX'] 	= $new_grey_recv_system_id[1];
			$knitting_master_data['RECV_NUMBER_PREFIX_NUM'] = $new_grey_recv_system_id[2];
			$knitting_master_data['RECV_NUMBER'] 			= $new_grey_recv_system_id[0];
			$knitting_master_data['ENTRY_FORM'] 			= 2;
			$knitting_master_data['ITEM_CATEGORY'] 			= 13;
			$knitting_master_data['RECEIVE_BASIS'] 			=2;
			$knitting_master_data['COMPANY_ID'] 			= $company_id;
			$knitting_master_data['RECEIVE_DATE'] 			=$txt_receive_date;
			$knitting_master_data['CHALLAN_NO'] 			='';
			$knitting_master_data['BOOKING_ID'] 			= $plan_id;
			$knitting_master_data['BOOKING_NO'] 			= $plan_id;
			$knitting_master_data['BOOKING_WITHOUT_ORDER'] 	=0;
			$knitting_master_data['STORE_ID'] 				= $store_id;
			$knitting_master_data['LOCATION_ID'] 			= $location_id;
			$knitting_master_data['KNITTING_SOURCE'] 		= $knitting_source;
			$knitting_master_data['KNITTING_COMPANY'] 		= $knitting_com;
			$knitting_master_data['BUYER_ID'] 				= $buyer_id;
			$knitting_master_data['YARN_ISSUE_CHALLAN_NO'] 	="";
			$knitting_master_data['SUB_CONTRACT'] 			=0;
			$knitting_master_data['REMARKS'] 				= $remarks;
			$knitting_master_data['ROLL_MAINTAINED'] 		=1;
			
			$knitting_master_data['INSERTED_BY'] 			= 1001;
			$knitting_master_data['INSERT_DATE'] 			=$this->pc_date_time;
			$knitting_master_data['WITHIN_GROUP'] 			= $within_group;

			// ##########################################################data for product_details_master########################################################################
            if ($brand == "") 	$brand_id = 0;
			else				$brand_id = return_id($brand, $brand_arr, "LIB_BRAND", "BRAND_NAME","");
		
			$id_dtls = return_next_id_by_sequence("PRO_GREY_PROD_DTLS_PK_SEQ", "pro_grey_prod_entry_dtls","", 0);
			$roll_rate =0;
			$reject_qty=0;
			
			$roll_arr = return_library_array("select po_breakdown_id,max(roll_no) as roll_no from pro_roll_details where entry_form in(2,22,62)  and po_breakdown_id in (".str_replace("'","",$po_id).") and is_sales=$is_sales group by po_breakdown_id", 'po_breakdown_id', 'roll_no');
			$data_array_roll=array();
			$roll_key=0;
			$receive_qnty_actual=0;
			foreach($mst_row_arr["Fabric Part"]['Rolls'] as $rolls_row)
			{
				
				$barcode=$rolls_row['Barcode No'];

				$check_barcode=return_field_value( "id", "pro_roll_details", "barcode_no='".$barcode."' and entry_form=2 and is_deleted=0 and status_active=1", "id" );
				if($check_barcode=="")
				{
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", "",0);
					$order_qnty_roll_wise=$rolls_row['Roll Qty'];
					$roll_reject_qty=$rolls_row['Reject Qty'];
					if($rolls_row['Shift Name']!="")	$shift_id=array_search ($rolls_row['Shift Name'], $shift_name);
					$roll_amount 	=0;

					if(!empty($qc_name))
					{
						$emp_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
						$qc_name		=return_field_value( "id", "lib_employee", " company_id=".$company_id." and floor_id=".$floor_id." first_name='".$rolls_row['QC Name']."' ", "id" );
					}
					else
					{
						$qc_name="";
					}

					if(!empty($roll_arr[$po_id]))
					{
						$roll_no = $roll_arr[$po_id] + 1;
						$roll_arr[$po_id] += 1;	
					}
					else
					{
						$roll_arr[$po_id]= 1;
						$roll_no=1;	
					}
					
					$reject_qty+=$roll_reject_qty*1;
					$no_of_roll+=1;
					
					$barcode_string=str_split($barcode);
					
					$barcode_year=$barcode_string[0].$barcode_string[1];
				
					$barcode_suffix=($barcode_string[4].$barcode_string[5].$barcode_string[6].$barcode_string[7].$barcode_string[8].$barcode_string[9].$barcode_string[10])*1;
					
					$data_array_roll[$roll_key]['ID'] 						= $id_roll;
					$data_array_roll[$roll_key]['BARCODE_YEAR'] 			= $barcode_year;
					$data_array_roll[$roll_key]['BARCODE_SUFFIX_NO']		=$barcode_suffix;
					$data_array_roll[$roll_key]['BARCODE_NO'] 				= $barcode;
					$data_array_roll[$roll_key]['MST_ID'] 					= $id;
					$data_array_roll[$roll_key]['DTLS_ID'] 					=$id_dtls;
					$data_array_roll[$roll_key]['PO_BREAKDOWN_ID'] 			= $po_id;
					$data_array_roll[$roll_key]['ENTRY_FORM'] 				= 2;
					$data_array_roll[$roll_key]['QNTY'] 					=$order_qnty_roll_wise;
					$data_array_roll[$roll_key]['QC_PASS_QNTY'] 			= $order_qnty_roll_wise;
					$data_array_roll[$roll_key]['REJECT_QNTY'] 				= $roll_reject_qty;
					$data_array_roll[$roll_key]['ROLL_NO'] 					=$roll_no;
					$data_array_roll[$roll_key]['RATE'] 					=$roll_rate;
					$data_array_roll[$roll_key]['AMOUNT'] 					= $roll_amount;
					$data_array_roll[$roll_key]['BOOKING_NO'] 				=$plan_id;
					$data_array_roll[$roll_key]['BOOKING_WITHOUT_ORDER'] 	= 0;
					$data_array_roll[$roll_key]['RECEIVE_BASIS'] 			= 2;
					$data_array_roll[$roll_key]['IS_SALES']					= $is_sales;
					$data_array_roll[$roll_key]['INSERTED_BY'] 				=1001;
					$data_array_roll[$roll_key]['INSERT_DATE'] 				=$this->pc_date_time;
					$receive_qnty_actual									+=$order_qnty_roll_wise;
					$roll_key+=1;

				}
			}

			if(!empty($data_array_roll))
			{
				
					$sql = $this->db->query("select id, current_stock from product_details_master where company_id=$company_id and item_category_id=13 and detarmination_id=$detarmination_id and gsm='".$gsm."' and dia_width='".$dia."' and status_active=1 and is_deleted=0");
				$row_prod= $sql->result();
				$data_array_prod_update=array();
				$data_array_prod=array();
				if(count($row_prod)>0)
				{
					$prod_id = $row_prod[0]->ID;
					if($fabric_store_auto_update==1)
					{
						$stock_qnty = $row_prod[0]->CURRENT_STOCK;
						$curr_stock_qnty = $stock_qnty + $receive_qnty_actual;
						$avg_rate_per_unit = 0;
						$stock_value = 0;
						$data_array_prod_update['STORE_ID'] 			= $cbo_store_name;
						$data_array_prod_update['AVG_RATE_PER_UNIT'] 	= $avg_rate_per_unit;
						$data_array_prod_update['LAST_PURCHASED_QNTY'] 	= $receive_qnty_actual;
						$data_array_prod_update['CURRENT_STOCK'] 		= $curr_stock_qnty;
						$data_array_prod_update['STOCK_VALUE'] 			=$stock_value;
						$data_array_prod_update['BRAND'] 				=0;
						$data_array_prod_update['LOT'] 					= $yarn_lot;
						$data_array_prod_update['UPDATED_BY'] 			=1001;
						$data_array_prod_update['UPDATE_DATE'] 			= $this->pc_date_time;
					}
				}
				else {
					$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master","", 0);
					if ($fabric_store_auto_update == 1) {
						$stock_qnty = $receive_qnty_actual;
						$last_purchased_qnty = $receive_qnty_actual;
					} else {
						$stock_qnty = 0;
						$last_purchased_qnty = 0;
					}
	
					$avg_rate_per_unit = 0;
					$stock_value = 0;
					
					$prod_name_dtls = trim(str_replace("'", "", $fabric_description)) . ", " . trim(str_replace("'", "", $gsm)) . ", " . trim(str_replace("'", "", $dia));
					
					$data_array_prod['ID'] 						= $prod_id;
					$data_array_prod['COMPANY_ID'] 				= $company_id;
					$data_array_prod['STORE_ID'] 				= $cbo_store_name;
					$data_array_prod['ITEM_CATEGORY_ID'] 		= 13;
					$data_array_prod['DETARMINATION_ID'] 		= $detarmination_id;
					$data_array_prod['ITEM_DESCRIPTION'] 		=$fabric_description;
					$data_array_prod['PRODUCT_NAME_DETAILS']	=$prod_name_dtls;
					$data_array_prod['UNIT_OF_MEASURE'] 		= $cbo_uom;
					$data_array_prod['AVG_RATE_PER_UNIT'] 		=$avg_rate_per_unit;
					$data_array_prod['AVG_RATE_PER_UNIT'] 		= $avg_rate_per_unit;
					$data_array_prod['LAST_PURCHASED_QNTY'] 	= $last_purchased_qnty;
					$data_array_prod['CURRENT_STOCK'] 			=$stock_qnty;
					$data_array_prod['STOCK_VALUE'] 			=$stock_value;
					$data_array_prod['LOT'] 					= $yarn_lot;
					$data_array_prod['BRAND'] 					=$brand_id;
					$data_array_prod['GSM'] 					= $gsm;
					$data_array_prod['DIA_WIDTH'] 				= $dia;
					$data_array_prod['INSERTED_BY'] 			= 1001;
					$data_array_prod['INSERT_DATE'] 			=$this->pc_date_time;
				}
			// ##########################################################data for inv_transaction ########################################################################
				$data_array_trans=array();
				if ($fabric_store_auto_update == 1) {
					
					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction","",0);
		
					$order_rate = 0;
					$order_amount = 0;
					$cons_rate = 0;
					$cons_amount = 0;
				
					$data_array_trans['ID'] 					= $id_trans;
					$data_array_trans['MST_ID'] 				= $id;
					$data_array_trans['STORE_ID'] 				= $cbo_store_name;
					$data_array_trans['RECEIVE_BASIS'] 			= 2;
					$data_array_trans['PI_WO_BATCH_NO'] 		= $plan_id;
					$data_array_trans['BOOKING_WITHOUT_ORDER'] 	=0;
					$data_array_trans['COMPANY_ID'] 			= $company_id;
					$data_array_trans['PROD_ID'] 				=$prod_id;
					$data_array_trans['ITEM_CATEGORY'] 			= 13;
					$data_array_trans['TRANSACTION_TYPE'] 		=1;
					$data_array_trans['TRANSACTION_DATE'] 		=$txt_receive_date;
					$data_array_trans['STORE_ID'] 				= $store_id;
					$data_array_trans['BRAND_ID'] 				= $brand_id;
					$data_array_trans['ORDER_UOM'] 				= $cbo_uom;
					$data_array_trans['ORDER_QNTY'] 			=$receive_qnty_actual;
					$data_array_trans['ORDER_RATE'] 			=$order_rate;
					$data_array_trans['ORDER_AMOUNT'] 			= $order_amount;
					$data_array_trans['CONS_UOM'] 				=$cbo_uom;
					$data_array_trans['CONS_QUANTITY'] 			= $receive_qnty;	
					$data_array_trans['CONS_RATE'] 				= $cons_rate;
					$data_array_trans['CONS_AMOUNT'] 			= $cons_amount;
					$data_array_trans['BALANCE_QNTY'] 			=$receive_qnty_actual;
					$data_array_trans['BALANCE_AMOUNT'] 		=$cons_amount;
					$data_array_trans['FLOOR_ID'] 				= $floor_id;
					$data_array_trans['MACHINE_ID'] 			=$machine_id;
					$data_array_trans['INSERTED_BY'] 			= 1001;
					$data_array_trans['INSERT_DATE'] 			=$this->pc_date_time;
					$data_array_trans['CONS_QUANTITY_PCS'] 	="";
					//$data_array_trans['CONS_REJECT_QNTY'] = $cbo_store_name;
					//$data_array_trans['ROOM'] = $this->pc_date_time;
					//$data_array_trans['RACK'] = $avg_rate_per_unit;
					//$data_array_trans['SELF'] = $last_purchased_qnty;
					//$data_array_trans['BIN_BOX'] =$stock_qnty;
				}
				 else {
					$id_trans = 0;
				}
				
				
				$yarn_prod_id=0;
				$operator_name="";
				//$yarn_prod_id = explode(",", str_replace("'", "", $yarn_prod_id));
				//asort($yarn_prod_id);
				//$yarn_prod_id = implode(",", $yarn_prod_id);
				//$operator_name = str_replace("'", "", $cbo_operator_name);
				$rate = 0;
				$amount = 0;
				// ##########################################################data for pro_grey_prod_entry_dtls ########################################################################
	
				$data_array_dtls['ID'] 						= $id_dtls;
				$data_array_dtls['MST_ID'] 					= $id;
				$data_array_dtls['PROD_ID']					=$prod_id;
				$data_array_dtls['TRANS_ID'] 				= $id_trans;
				$data_array_dtls['BODY_PART_ID'] 			= $bodypart_id;
				$data_array_dtls['FEBRIC_DESCRIPTION_ID'] 	=$detarmination_id;
				$data_array_dtls['GSM'] 					= $gsm;
				$data_array_dtls['WIDTH'] 					= $dia;
				$data_array_dtls['NO_OF_ROLL'] 				=$no_of_roll;
				$data_array_dtls['ORDER_ID'] 				= $po_id;
				$data_array_dtls['GREY_RECEIVE_QNTY'] 		= $receive_qnty_actual;
				$data_array_dtls['REJECT_FABRIC_RECEIVE'] 	= $roll_reject_qty;
				$data_array_dtls['RATE'] 					=$rate;
				$data_array_dtls['AMOUNT'] 					=$amount;
				$data_array_dtls['UOM'] 					= $cbo_uom;
				$data_array_dtls['YARN_LOT'] 				=$yarn_lot;
				$data_array_dtls['YARN_COUNT'] 				= $yarn_count;
				$data_array_dtls['BRAND_ID'] 				= $brand_id;
				$data_array_dtls['SHIFT_NAME']				= $shift_id;
				$data_array_dtls['FLOOR_ID'] 				=$floor_id;
				$data_array_dtls['MACHINE_NO_ID'] 			=$machine_id;
				$data_array_dtls['COLOR_ID'] 				= $color_id;
				$data_array_dtls['COLOR_RANGE_ID'] 			=$color_range;
				$data_array_dtls['STITCH_LENGTH'] 			= $stritch_length;
				$data_array_dtls['MACHINE_DIA'] 			= $machine_dia;
				$data_array_dtls['MACHINE_GG'] 				=$machine_gauge;
				$data_array_dtls['ORDER_YARN_RATE'] 		= 0;
				$data_array_dtls['ORDER_KNITTING_CHARGE'] 	=0;
				$data_array_dtls['YARN_RATE'] 				= 0;
				$data_array_dtls['KNITING_CHARGE'] 			= 0;
				$data_array_dtls['OPERATOR_NAME'] 			=$qc_name;
				$data_array_dtls['YARN_PROD_ID'] 			="";
				$data_array_dtls['INSERTED_BY'] 			= 1001;
				$data_array_dtls['INSERT_DATE'] 			=$this->pc_date_time;
			
				//$data_array_dtls['GREY_RECEIVE_QNTY_PCS'] 	= $brand_id;
				//$data_array_dtls['REJECT_FABRIC_RECEIVE'] 	= $cbo_uom;
				//	$data_array_dtls['CONS_QUANTITY_PCS'] 	="";
				//$data_array_dtls['CONS_REJECT_QNTY'] = $cbo_store_name;
				//print_r($data_array_dtls);die;
			
				
		
				// ##########################################################data for order_wise_pro_details ########################################################################
	
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details","",0);
			
				$data_array_prop['ID'] 						= $id_prop;
				$data_array_prop['TRANS_ID'] 				= $id_trans;
				$data_array_prop['TRANS_TYPE'] 				= 1;
				$data_array_prop['ENTRY_FORM']				=2;
				$data_array_prop['DTLS_ID'] 				= $id_dtls;
				$data_array_prop['PO_BREAKDOWN_ID'] 		=$po_id;
				$data_array_prop['PROD_ID'] 				= $prod_id;
				$data_array_prop['QUANTITY'] 				= $receive_qnty_actual;
				$data_array_prop['QUANTITY_PCS'] 			=0;
				$data_array_prop['RETURNABLE_QNTY'] 		= $reject_qty;
				$data_array_prop['IS_SALES'] 				= $is_sales;
				$data_array_prop['INSERTED_BY'] 			= 1001;
				$data_array_prop['INSERT_DATE'] 			=$this->pc_date_time;
			
			
				$this->db->trans_start();
				$this->db->insert("INV_RECEIVE_MASTER", $knitting_master_data);
				if(count($data_array_prod_update)>0) $this->db->update("PRODUCT_DETAILS_MASTER", $data_array_prod_update,array('ID' => $prod_id));
				if(count($data_array_prod)>0) $this->db->insert("PRODUCT_DETAILS_MASTER", $data_array_prod);
				if(count($data_array_trans)>0)	$this->db->insert("INV_TRANSACTION", $data_array_trans);
				$this->db->insert("PRO_GREY_PROD_ENTRY_DTLS", $data_array_dtls);
				$this->db->insert_batch('PRO_ROLL_DETAILS', $data_array_roll);
				$this->db->insert("ORDER_WISE_PRO_DETAILS", $data_array_prop);
				$this->db->trans_complete();
				if ($this->db->trans_status() == TRUE) {
					echo "Data Save Sussessfully.  Production Id ".$new_grey_recv_system_id[0];
				} else {
					echo "Invalid Operation.";
				}	
			}
			else 
			{
				echo "No Data Found";
			}
			
		}
	//echo	count($data_array_prod_update);die;//	echo $this->db->last_query(); die();
		
		die;
	}
	
	function delevery_to_store_get()
    {
		$fromDate="";
		$toDate="";
		if($this->get('from_date') !="" && $this->validateDate($this->get('from_date')) && $this->validateDate($this->get('to_date')) && $this->get('to_date') !=""){
			$fromDate=$this->get('from_date');
		    $toDate=$this->get('to_date');
		}
		else if($this->validateDate($this->get('from_date')) && !$this->validateDate($this->get('to_date'))){
			$fromDate=$this->get('from_date');
		    $toDate=$this->get('from_date');
		}
		else if(!$this->validateDate($this->get('from_date')) && $this->validateDate($this->get('to_date'))){
			$fromDate=$this->get('to_date');
		    $toDate=$this->get('to_date');
		}
		
		$location_arr = return_library_array("select id, location_name from lib_location", 'location_name', 'id');
		$store_arr = return_library_array("select id, store_name from lib_store_location", 'store_name', 'id');
		$product_stock_arr = return_library_array("select id, current_stock from product_details_master", 'id', 'current_stock');
		
		//$knitting_porduction_data_api=json_decode(file_get_contents('http://rms.careerclub.net/productions/stored?_token=logic&from='.$fromDate.'&to='.$toDate),true);
		$knitting_porduction_data_api=json_decode(file_get_contents('http://192.168.100.26/tosrifa/productions/stored?_token=logic&from='.$fromDate.'&to='.$toDate),true);

		if(empty($knitting_porduction_data_api['data']))
		{
			echo "No Data Found";die;
		}

		$fabric_store_auto_update=0;
		$cbo_store_name=0;
		$cbo_uom=12;
		$floor_id=0;
		$no_of_roll=0;
		$txt_receive_date=date("d-M-Y",strtotime($fromDate));
		$barcode_arr=array();
		$barcode_data_arr=array();
		foreach($knitting_porduction_data_api['data'] as $mst_row_arr)
		{
			$barcode=$mst_row_arr['Barcode No'];
			$barcode_arr[$barcode]					=$barcode;
			$barcode_data_arr[$barcode]['qc_pass']	=$mst_row_arr['Roll Qty. QC PASS'];
			$barcode_data_arr[$barcode]['store']	=$mst_row_arr['Store Name'];
			$barcode_data_arr[$barcode]['location']	=$mst_row_arr['Location Name'];
			$barcode_data_arr[$barcode]['Room']		=$mst_row_arr['Room'];
			$barcode_data_arr[$barcode]['Rack']		=$mst_row_arr['Rack'];
			$barcode_data_arr[$barcode]['Shelf']	=$mst_row_arr['Shelf'];
			$barcode_data_arr[$barcode]['Bin']		=$mst_row_arr['Bin/Box'];
		}	


		$knitting_porduction_data= $this->salesorder_model->get_knitting_production_data_info($barcode_arr);
		$production_check_arr=array();
		$delivery_master_data=array();
		$inserted_system_id_arr=array();
		$inserted_receive_id_arr=array();
		$product_qty_arr=array();
		$roll_key=0;
		$master_key=0;
		$receive_qty=0;
		if(empty($knitting_porduction_data))
		{
			echo "No Data Found";die;
		}
		
		foreach($knitting_porduction_data as $p_data)
		{
			$company_id				=$p_data->COMPANY_ID;			
			$po_id					=$p_data->PO_BREAKDOWN_ID;
			$prod_id				=$p_data->PROD_ID;
			$production_mst_id		=$p_data->MST_ID;
			$production_dtls_id		=$p_data->DTLS_ID;
			$roll_id				=$p_data->ID;
			$roll_no				=$p_data->ROLL_NO;
			$detarmination_id		=$p_data->FEBRIC_DESCRIPTION_ID;
			$booking_no 			=$p_data->BOOKING_NO;
			$is_sales				=$p_data->IS_SALES;
			$knitting_com			=$p_data->KNITTING_COMPANY;
			$knitting_source		=$p_data->KNITTING_SOURCE;
					
			$barcode_no				=$p_data->BARCODE_NO;
			$qc_pass_qty			=$p_data->QC_PASS_QNTY;	
			$receive_qty			=$barcode_data_arr[$barcode_no]['qc_pass'];
			$room					=preg_replace('/[^0-9]/', '', $barcode_data_arr[$barcode_no]['Room']);	
			$rack					=$barcode_data_arr[$barcode_no]['Rack'];	
			$self					=preg_replace('/[^0-9]/', '', $barcode_data_arr[$barcode_no]['Shelf']);	
			$bin					=preg_replace('/[^0-9]/', '', $barcode_data_arr[$barcode_no]['Bin']);		
			$booking_without_order	=$p_data->BOOKING_WITHOUT_ORDER;
			$body_part_id			=$p_data->BODY_PART_ID;
			$gsm					=$p_data->GSM;
			$width_dia				=$p_data->WIDTH;
			$uom					=$p_data->UOM;
			$yarn_lot				=$p_data->YARN_LOT;
			$yarn_count				=$p_data->YARN_COUNT;
			$brand_id				=$p_data->BRAND_ID;
			$shift					=$p_data->SHIFT_NAME;
			$machine_id				=$p_data->MACHINE_NO_ID;
			$color_range_id			=$p_data->COLOR_RANGE_ID;
			$stich_length			=$p_data->STITCH_LENGTH;
			$color_id				=$p_data->COLOR_ID;
			//$floor_id				=$p_data->FLOOR_ID;
			$location_id			=5;
			$floor_id				=12;
			$store_id 				=4;
			/*if ($barcode_data_arr[$barcode_no]['location'] == "" || $barcode_data_arr[$barcode_no]['location'] == "__N/A__") 	$location_id = 0;
			else
			{
				if(in_array($barcode_data_arr[$barcode_no]['location'], $location_arr))
				{
					 $location_id =$location_arr[$barcode_data_arr[$barcode_no]['location']];  //$color_library[str_replace("'","",$$txtcolor)];
				}
			}
			
			$store_id=0;
			if ($barcode_data_arr[$barcode_no]['store'] == "" || $barcode_data_arr[$barcode_no]['store'] == "__N/A__") 	$store_id = 0;
			else
			{
				if(in_array($barcode_data_arr[$barcode_no]['store'], $location_arr))
				{
					 $store_id =$store_arr[$barcode_data_arr[$barcode_no]['store']];  //$color_library[str_replace("'","",$$txtcolor)];
				}
			}*/
			
			$search_key=$company_id."_".$knitting_com."_".$knitting_source."_".$location_id;
			if(!in_array($search_key,$production_check_arr))
			{	
			// ###################################################data for pro_grey_prod_delivery_mst############################################################
		
				$id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_MST_PK_SEQ", "pro_grey_prod_delivery_mst","",0);
				$new_mrr_number = explode("*", return_next_id_by_sequence("PRO_GREY_PROD_DELI_MST_PK_SEQ", "pro_grey_prod_delivery_mst","",1,$company_id,'GDSR',56,date("Y",time()),"" ));
				
				$delivery_master_data[$master_key]['ID'] 					= $id;
				$delivery_master_data[$master_key]['SYS_NUMBER_PREFIX'] 	= $new_mrr_number[1];
				$delivery_master_data[$master_key]['SYS_NUMBER_PREFIX_NUM'] = $new_mrr_number[2]*1;
				$delivery_master_data[$master_key]['SYS_NUMBER'] 			= $new_mrr_number[0];
				$delivery_master_data[$master_key]['DELEVERY_DATE'] 		=$txt_receive_date;
				$delivery_master_data[$master_key]['COMPANY_ID'] 			= $company_id;
				$delivery_master_data[$master_key]['LOCATION_ID'] 			= $location_id;
				$delivery_master_data[$master_key]['KNITTING_SOURCE'] 		= $knitting_source;
				$delivery_master_data[$master_key]['KNITTING_COMPANY'] 		= $knitting_com;
				$delivery_master_data[$master_key]['ENTRY_FORM'] 			= 56;
				//$delivery_master_data[$master_key]['ITEM_CATEGORY'] 			= 13;
				//$delivery_master_data['REMARKS'] 				= $barcode_data_arr[$barcode_no]['location'];
				//$delivery_master_data['FLOOR_IDS'] 				= $barcode_data_arr[$barcode_no]['location'];
				$delivery_master_data[$master_key]['INSERTED_BY'] 			= 1001;
				$delivery_master_data[$master_key]['INSERT_DATE'] 			=$this->pc_date_time;
				$production_check_arr[]=$search_key;
				$inserted_system_id_arr[$new_mrr_number[0]]=$new_mrr_number[0];
				
				
				$receive_mst_id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master","", 0);
				$new_grey_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master","",1,$company_id,"KNGFRR",58,date("Y",time()),13 ));
				$receive_master_data[$master_key]['ID'] 					= $receive_mst_id;
				$receive_master_data[$master_key]['RECV_NUMBER_PREFIX'] 	= $new_grey_recv_system_id[1];
				$receive_master_data[$master_key]['RECV_NUMBER_PREFIX_NUM'] = $new_grey_recv_system_id[2]*1;
				$receive_master_data[$master_key]['RECV_NUMBER'] 			= $new_grey_recv_system_id[0];
				$receive_master_data[$master_key]['ENTRY_FORM'] 			= 58;
				$receive_master_data[$master_key]['ITEM_CATEGORY'] 			= 13;
				$receive_master_data[$master_key]['RECEIVE_BASIS'] 			= 10;

				$receive_master_data[$master_key]['RECEIVE_DATE'] 			=$txt_receive_date;
				$receive_master_data[$master_key]['COMPANY_ID'] 			= $company_id;
				$receive_master_data[$master_key]['CHALLAN_NO'] 			= $new_mrr_number[0];

				$receive_master_data[$master_key]['BOOKING_ID'] 			= $id;
				$receive_master_data[$master_key]['BOOKING_NO'] 			= $new_mrr_number[0];
				$receive_master_data[$master_key]['BOOKING_WITHOUT_ORDER'] 	=0;
				
				$receive_master_data[$master_key]['STORE_ID'] 				= $store_id;
				$receive_master_data[$master_key]['LOCATION_ID'] 			= $location_id;
				$receive_master_data[$master_key]['KNITTING_SOURCE'] 		= $knitting_source;
				$receive_master_data[$master_key]['KNITTING_COMPANY'] 		= $knitting_com;
				$receive_master_data[$master_key]['YARN_ISSUE_CHALLAN_NO'] 	= "";
				$receive_master_data[$master_key]['REMARKS'] 				= "";
				$receive_master_data[$master_key]['FABRIC_NATURE'] 			= 2;
				$receive_master_data[$master_key]['INSERTED_BY'] 			= 1001;
				$receive_master_data[$master_key]['INSERT_DATE'] 			=$this->pc_date_time;
				$inserted_receive_id_arr[$new_grey_recv_system_id[0]]=$new_grey_recv_system_id[0];
				
				$master_key++;
			}
			
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details","", 0);
			$dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls","", 0);
			
			$data_array_dtls[$roll_key]['ID'] 						= $dtls_id;
			$data_array_dtls[$roll_key]['MST_ID'] 					= $id;
			$data_array_dtls[$roll_key]['ENTRY_FORM']				=56;
			$data_array_dtls[$roll_key]['GREY_SYS_ID'] 				= $production_mst_id;
			$data_array_dtls[$roll_key]['SYS_DTLS_ID'] 				= $production_dtls_id;
			$data_array_dtls[$roll_key]['DETERMINATION_ID'] 		=$detarmination_id;
			$data_array_dtls[$roll_key]['PRODUCT_ID'] 				= $prod_id;
			$data_array_dtls[$roll_key]['ORDER_ID'] 				= $po_id;
			$data_array_dtls[$roll_key]['ROLL_ID'] 					=$roll_id;
			$data_array_dtls[$roll_key]['BARCODE_NUM'] 				= $barcode_no;
			$data_array_dtls[$roll_key]['CURRENT_DELIVERY'] 		= $qc_pass_qty;
			$data_array_dtls[$roll_key]['INSERTED_BY'] 				= 1001;
			$data_array_dtls[$roll_key]['INSERT_DATE'] 				=$this->pc_date_time;
			
			$data_array_roll[$roll_key]['ID'] 						= $id_roll;
			$data_array_roll[$roll_key]['BARCODE_NO'] 				= $barcode_no;
			$data_array_roll[$roll_key]['MST_ID'] 					= $id;
			$data_array_roll[$roll_key]['DTLS_ID'] 					=$dtls_id;
			$data_array_roll[$roll_key]['PO_BREAKDOWN_ID'] 			= $po_id;
			$data_array_roll[$roll_key]['ENTRY_FORM'] 				= 56;
			$data_array_roll[$roll_key]['QNTY'] 					=$qc_pass_qty;
			$data_array_roll[$roll_key]['QC_PASS_QNTY'] 			= $qc_pass_qty;
			$data_array_roll[$roll_key]['ROLL_ID'] 					= $roll_id;
			$data_array_roll[$roll_key]['ROLL_NO'] 					=$roll_no;
			$data_array_roll[$roll_key]['BOOKING_NO'] 				=$booking_no;
			$data_array_roll[$roll_key]['BOOKING_WITHOUT_ORDER'] 	= $booking_without_order;
			$data_array_roll[$roll_key]['IS_SALES']					= $is_sales;
			$data_array_roll[$roll_key]['INSERTED_BY'] 				=1001;
			$data_array_roll[$roll_key]['INSERT_DATE'] 				=$this->pc_date_time;
			
			
			
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", "",0);
			$id_dtls_rec = return_next_id_by_sequence("PRO_GREY_PROD_DTLS_PK_SEQ", "pro_grey_prod_entry_dtls","", 0);
			$id_roll_rec = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details","", 0);
			
			$data_array_rcv_dtls[$roll_key]['ID'] 						= $id_dtls_rec;
			$data_array_rcv_dtls[$roll_key]['MST_ID'] 					= $receive_mst_id;
			$data_array_rcv_dtls[$roll_key]['TRANS_ID']					=$id_trans;
			$data_array_rcv_dtls[$roll_key]['PROD_ID'] 					= $prod_id;
			$data_array_rcv_dtls[$roll_key]['BODY_PART_ID'] 			= $body_part_id;//qqqqqqqq
			$data_array_rcv_dtls[$roll_key]['FEBRIC_DESCRIPTION_ID'] 	=$detarmination_id;
			$data_array_rcv_dtls[$roll_key]['GSM'] 						= $gsm;
			$data_array_rcv_dtls[$roll_key]['WIDTH'] 					= $width_dia;
			$data_array_rcv_dtls[$roll_key]['NO_OF_ROLL'] 				=1;
			$data_array_rcv_dtls[$roll_key]['ORDER_ID'] 				= $po_id;
			$data_array_rcv_dtls[$roll_key]['GREY_RECEIVE_QNTY'] 		= $receive_qty;
			//$data_array_rcv_dtls[$roll_key]['REJECT_FABRIC_RECEIVE'] 				= $production_dtls_id;
			$data_array_rcv_dtls[$roll_key]['UOM'] 						=$uom;
			$data_array_rcv_dtls[$roll_key]['YARN_LOT'] 				= $yarn_lot;
			$data_array_rcv_dtls[$roll_key]['YARN_COUNT'] 				= $yarn_count;
			$data_array_rcv_dtls[$roll_key]['BRAND_ID'] 				=$brand_id;
			$data_array_rcv_dtls[$roll_key]['SHIFT_NAME'] 				= $shift;
			$data_array_rcv_dtls[$roll_key]['FLOOR_ID'] 				= $floor_id;
			$data_array_rcv_dtls[$roll_key]['MACHINE_NO_ID'] 			= $machine_id;
			$data_array_rcv_dtls[$roll_key]['ROOM'] 					=$room;
			$data_array_rcv_dtls[$roll_key]['RACK'] 					= $rack;
			$data_array_rcv_dtls[$roll_key]['SELF'] 					= $self;
			$data_array_rcv_dtls[$roll_key]['BIN_BOX'] 					=$bin;
			$data_array_rcv_dtls[$roll_key]['COLOR_ID'] 				= $color_id;
			$data_array_rcv_dtls[$roll_key]['COLOR_RANGE_ID'] 			= $color_range_id;
			$data_array_rcv_dtls[$roll_key]['STITCH_LENGTH'] 			= $stich_length;
			//$data_array_rcv_dtls[$roll_key]['KNITING_CHARGE'] 		=$detarmination_id;
			$data_array_rcv_dtls[$roll_key]['INSERTED_BY'] 				= 1001;
			$data_array_rcv_dtls[$roll_key]['INSERT_DATE'] 				=$this->pc_date_time;
			
			
			
			$data_array_tran[$roll_key]['ID'] 						= $id_trans;
			//$data_array_tran[$roll_key]['BARCODE_NO'] 				= $barcode_no;
			$data_array_tran[$roll_key]['MST_ID'] 					= $receive_mst_id;
			$data_array_tran[$roll_key]['PI_WO_BATCH_NO'] 			=$id_dtls_rec;
			$data_array_tran[$roll_key]['PROD_ID'] 					= $prod_id;
			$data_array_tran[$roll_key]['COMPANY_ID'] 				= $company_id;
			$data_array_tran[$roll_key]['ITEM_CATEGORY'] 			=13;
			//$data_array_tran[$roll_key]['QC_PASS_QNTY'] 			= $receive_qty;
			$data_array_tran[$roll_key]['TRANSACTION_TYPE'] 		= 1;
			$data_array_tran[$roll_key]['TRANSACTION_DATE'] 		=$txt_receive_date;
			$data_array_tran[$roll_key]['STORE_ID'] 				=$store_id;
			$data_array_tran[$roll_key]['BRAND_ID'] 				= $brand_id;
			$data_array_tran[$roll_key]['ORDER_UOM']				= $uom;
			$data_array_tran[$roll_key]['ORDER_QNTY'] 				=$receive_qty;
			
			//$data_array_tran[$roll_key]['ORDER_RATE'] 				=$this->pc_date_time;
			//$data_array_tran[$roll_key]['ORDER_AMOUNT'] 						= $id_roll_rec;
			$data_array_tran[$roll_key]['CONS_UOM'] 				= $uom;
			//$data_array_tran[$roll_key]['CONS_UOM'] 				= $receive_mst_id;
			$data_array_tran[$roll_key]['CONS_QUANTITY'] 			=$receive_qty;
			$data_array_tran[$roll_key]['CONS_REJECT_QNTY'] 		= $po_id;
			$data_array_tran[$roll_key]['CONS_RATE'] 				= 0;
			$data_array_tran[$roll_key]['CONS_AMOUNT'] 				=0;
			$data_array_tran[$roll_key]['BALANCE_QNTY'] 			= $receive_qty;
			$data_array_tran[$roll_key]['BALANCE_AMOUNT'] 			=0;
			$data_array_tran[$roll_key]['FLOOR_ID'] 				=$floor_id;
			$data_array_tran[$roll_key]['MACHINE_ID'] 				=$machine_id;
			$data_array_tran[$roll_key]['ROOM'] 					= $room;
			$data_array_tran[$roll_key]['RACK']						= $rack;
			$data_array_tran[$roll_key]['SELF'] 					=$self;
			$data_array_tran[$roll_key]['BIN_BOX'] 					=$bin;
			$data_array_tran[$roll_key]['BOOKING_WITHOUT_ORDER'] 	=$booking_without_order;
			$data_array_tran[$roll_key]['INSERTED_BY'] 				=1001;
			$data_array_tran[$roll_key]['INSERT_DATE'] 				=$this->pc_date_time;
			
			
			
			$data_array_rcv_roll[$roll_key]['ID'] 						= $id_roll_rec;
			$data_array_rcv_roll[$roll_key]['BARCODE_NO'] 				= $barcode_no;
			$data_array_rcv_roll[$roll_key]['MST_ID'] 					= $receive_mst_id;
			$data_array_rcv_roll[$roll_key]['DTLS_ID'] 					=$id_dtls_rec;
			$data_array_rcv_roll[$roll_key]['PO_BREAKDOWN_ID'] 			= $po_id;
			$data_array_rcv_roll[$roll_key]['ENTRY_FORM'] 				= 58;
			$data_array_rcv_roll[$roll_key]['QNTY'] 					=$receive_qty;
			$data_array_rcv_roll[$roll_key]['QC_PASS_QNTY'] 			= $receive_qty;
			$data_array_rcv_roll[$roll_key]['ROLL_ID'] 					= $roll_id;
			$data_array_rcv_roll[$roll_key]['ROLL_NO'] 					=$roll_no;
			$data_array_rcv_roll[$roll_key]['BOOKING_NO'] 				=$booking_no;
			$data_array_rcv_roll[$roll_key]['BOOKING_WITHOUT_ORDER'] 	= $booking_without_order;
			$data_array_rcv_roll[$roll_key]['IS_SALES']					= $is_sales;
			$data_array_rcv_roll[$roll_key]['INSERTED_BY'] 				=1001;
			$data_array_rcv_roll[$roll_key]['INSERT_DATE'] 				=$this->pc_date_time;
			if(empty($product_qty_arr[$prod_id])){
				//echo $product_stock_arr[$prod_id];die;
				$product_qty_arr[$prod_id]								= $product_stock_arr[$prod_id];
			}
			$product_qty_arr[$prod_id]								+= $receive_qty;
			//die;
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details","", 0);
			$data_array_prop[$roll_key]['ID'] 						= $id_prop;
			$data_array_prop[$roll_key]['TRANS_ID'] 				= $id_trans;
			$data_array_prop[$roll_key]['TRANS_TYPE'] 				= 1;
			$data_array_prop[$roll_key]['ENTRY_FORM'] 				=58;
			$data_array_prop[$roll_key]['DTLS_ID'] 					= $id_dtls_rec;
			$data_array_prop[$roll_key]['PO_BREAKDOWN_ID'] 			= $po_id;
			$data_array_prop[$roll_key]['PROD_ID'] 					=$prod_id;
			$data_array_prop[$roll_key]['QUANTITY'] 				= $receive_qty;
			$data_array_prop[$roll_key]['IS_SALES'] 				= $is_sales;
			$data_array_prop[$roll_key]['INSERTED_BY'] 				=1001;
			$data_array_prop[$roll_key]['INSERT_DATE'] 				=$this->pc_date_time;
			
			$roll_key+=1;
			
		}
		
		foreach($product_qty_arr as $product_id=>$stock)
		{
			$update_product_data[$product_id]['ID']=$product_id;
			$update_product_data[$product_id]['CURRENT_STOCK']=$stock;
		}
		//print_r($data_array_roll);die;
		//print_r($product_qty_arr);die;
		//die;

	//echo	count($data_array_prod_update);die;//	echo $this->db->last_query(); die();
		$this->db->trans_start();
		
		$this->db->insert_batch("PRO_GREY_PROD_DELIVERY_MST", $delivery_master_data);
		$this->db->insert_batch("PRO_GREY_PROD_DELIVERY_DTLS", $data_array_dtls);
		$this->db->insert_batch('PRO_ROLL_DETAILS', $data_array_roll);
		
		$this->db->insert_batch('INV_RECEIVE_MASTER', $receive_master_data);
		$this->db->insert_batch("INV_TRANSACTION", $data_array_tran);
		$this->db->insert_batch("PRO_GREY_PROD_ENTRY_DTLS", $data_array_rcv_dtls);
		$this->db->insert_batch('PRO_ROLL_DETAILS', $data_array_rcv_roll);
		$this->db->update_batch('PRODUCT_DETAILS_MASTER', $update_product_data, 'ID');
		
		$this->db->insert_batch('ORDER_WISE_PRO_DETAILS', $data_array_prop);
		//echo $this->db->last_query(); die();
		$this->db->trans_complete();
        if ($this->db->trans_status() == TRUE) {
            echo "Data Save Sussessfully. Delivery Challan No : ".implode(",",$inserted_system_id_arr);
			echo "<br/> Received ID : ".implode(",",$inserted_receive_id_arr);
        } else {
            echo "Invalid Operation.";
        }
		die;
	}
	
	
	function validateDate($date)
	{
		 return (bool)strtotime($date);
	}
	function compareDate($fromDate, $toDate){
		if(strtotime($fromDate) <= strtotime($toDate)){
			return 1;
		}else{
			return 0;
		}
		
	}

	
 }
