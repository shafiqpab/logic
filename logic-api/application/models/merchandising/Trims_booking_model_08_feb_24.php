<?php

class Trims_booking_model extends CI_Model
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

	function deleteRowByAttribute($tableName, $attribute)
	{
		$this->db->trans_start();
		$this->db->delete($tableName, $attribute);
		$this->db->trans_complete();
		if ($this->db->trans_status() == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function Trims_booking($user_id = 10.01){
		//print_r('6');die;
		//UOM lib
		$unit_of_measurement = array(1 => "Pcs", 2 => "Dzn", 3 => "Grs", 4 => "GG", 10 => "Mg", 11 => "Gm", 12 => "Kg", 13 => "Quintal", 14 => "Ton", 15 => "Lbs", 20 => "Km", 21 => "Hm", 22 => "Dm", 23 => "Mtr", 24 => "Dcm", 25 => "CM", 26 => "MM", 27 => "Yds", 28 => "Feet", 29 => "Inch", 30 => "CFT", 31 => "SFT", 40 => "Ltr", 41 => "ML", 50 => "Roll", 51 => "Coil", 52 => "Cone", 53 => "Bag", 54 => "Box", 55 => "Drum", 56 => "Bottle", 57 => "Pack", 58 => "Set", 59 => "Can", 60 => "Each", 61 => "Gallon", 62 => "Lachi", 63 => "Pair", 64 => "Lot", 65 => "Packet", 66 => "Pot", 67 => "Book", 68 => "Culind", 69 => "Ream", 70 => "Cft", 71 => "Syp", 72 => "K.V", 73 => "CU-M3", 74 => "Bundle", 75 => "Strip", 76 => "SQM", 77 => "Ounce", 78 => "Cylinder", 79 => "Course", 80 => "Sheet", 81 => "RFT", 82 => "Square Inch", 83 => "Carton", 84 => "Thane", 85 => "Gross Yds", 86 => "Jar", 87 => "Reel", 88 => "CBM",89=>"Tub",90=>"KVA",91=>"KW",92=>"Pallet",93=>"Case",94=>"Job",95=>"KIT");
		//UOM lib

		//currency lib
		$currency = array(1 => "BDT", 2 => "USD", 3 => "EURO", 4 => "CHF", 5 => "SGD", 6 => "Pound", 7 => "YEN");
		//currency lib end

		//$this->deleteRowByAttribute('GBL_TEMP_ENGINE', array('ENTRY_FORM' =>10.01 , "USER_ID" => $user_id));

		$query_invoice = "SELECT a.INVOICE_NO,b.PI_ID,b.IMPORT_INVOICE_ID FROM COM_IMPORT_INVOICE_MST a, COM_IMPORT_INVOICE_dtls b WHERE a.id = b.IMPORT_INVOICE_ID AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0";
		$table_invoice = $this->db->query($query_invoice)->result();
		$invoice_array = Array();
		foreach($table_invoice as $row){
			$invoice_array[$row->PI_ID][]=$row->INVOICE_NO;
		}

		$query_sales_contract = "SELECT c.CONTRACT_NO,c.CONVERTIBLE_TO_LC,a.BTB_SYSTEM_ID FROM COM_BTB_LC_MASTER_DETAILS  a, COM_BTB_EXPORT_LC_ATTACHMENT  b, COM_SALES_CONTRACT c WHERE a.id = b.IMPORT_MST_ID AND b.LC_SC_ID = c.id AND b.IS_LC_SC = 1";
		$table_sales_contract = $this->db->query($query_sales_contract)->result();
		$sales_contract = Array();
		foreach($table_sales_contract as $row){
			$sales_contract[$row->BTB_SYSTEM_ID][]=[
				"sales_contract"=>$row->CONTRACT_NO,
				"convertible_to_lc"=>$row->CONVERTIBLE_TO_LC,
			];
		}

		$query_export_lc = "SELECT c.EXPORT_LC_NO,a.BTB_SYSTEM_ID FROM COM_BTB_LC_MASTER_DETAILS     a, COM_BTB_EXPORT_LC_ATTACHMENT  b, COM_EXPORT_LC c WHERE a.id = b.IMPORT_MST_ID AND b.LC_SC_ID = c.id AND b.IS_LC_SC = 0";
		$table_export_lc = $this->db->query($query_export_lc)->result();
		//$export_lc = Array();
		foreach($table_export_lc as $row){
			$export_lc[$row->BTB_SYSTEM_ID][]=$row->EXPORT_LC_NO;
		}

		$query_PI_and_BTB = "SELECT a.ID,a.PI_NUMBER, b.WORK_ORDER_NO,d.BTB_SYSTEM_ID FROM com_pi_master_details a, com_pi_item_details b, COM_BTB_LC_PI c, COM_BTB_LC_MASTER_DETAILS  d WHERE  a.id = b.pi_id AND b.pi_id = c.pi_id AND c.COM_BTB_LC_MASTER_DETAILS_ID = d.id AND b.WORK_ORDER_NO is not null AND a.ITEM_CATEGORY_ID = 4";
		$table_PI_and_BTB = $this->db->query($query_PI_and_BTB)->result();

		$pi_bib = Array();
		foreach($table_PI_and_BTB as $row){
			if($invoice_array[$row->ID] == null){
				continue;
			}elseif($sales_contract[$row->BTB_SYSTEM_ID] == null){
				continue;
			}elseif($export_lc[$row->BTB_SYSTEM_ID]){
				continue;
			}
			$pi_bib[$row->WORK_ORDER_NO][]=[
				"pi_number"=>$row->PI_NUMBER,
				"work_order"=>$row->WORK_ORDER_NO,
				"btb_number"=>$row->BTB_SYSTEM_ID,
				"invoice"=> $invoice_array[$row->ID],
				"sales_contract"=> $sales_contract[$row->BTB_SYSTEM_ID],
				"export_lc"=> $export_lc[$row->BTB_SYSTEM_ID],
			];
		}
		//print_r($pi_bib);die;
		$query_booking = "SELECT a.BOOKING_NO,b.PO_BREAK_DOWN_ID,c.PO_NUMBER, b.AMOUNT,b.RATE,b.UOM,a.CURRENCY_ID,b.TRIM_GROUP,a.BOOKING_DATE FROM WO_BOOKING_MST a, WO_BOOKING_DTLS b,WO_PO_BREAK_DOWN c WHERE     a.ID = b.BOOKING_MST_ID AND c.ID = b.PO_BREAK_DOWN_ID AND a.BOOKING_TYPE = 2 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0";
		$table_booking = $this->db->query($query_booking)->result();

		$booking_array = Array();
		foreach($table_booking as $row){
			if($pi_bib[$row->BOOKING_NO]==null){
				continue;
			}
			$booking_array[$row->BOOKING_NO][$row->PO_BREAK_DOWN_ID] = [
				"booking_no" => $row->BOOKING_NO,
				"po_break_down_id" => $row->PO_BREAK_DOWN_ID,
				"po_num" => $row->PO_NUMBER,
				"amount" => $row->AMOUNT,
				"rate" => $row->RATE,
				"uom" => $unit_of_measurement[$row->UOM],
				"currency" => $currency[$row->CURRENCY_ID],
				"TRIM_GROUP" => $row->TRIM_GROUP,
				"BOOKING_DATE" => $row->BOOKING_DATE,
				"pi_btb" => $pi_bib[$row->BOOKING_NO],
			];
		}
		return $booking_array;
		
	}	
}
// class Trims_booking_model extends CI_Model
// {
// 	function __construct()
// 	{
// 		parent::__construct();
// 		error_reporting(0);
// 	}

// 	function get_max_value($tableName, $fieldName)
// 	{
// 		return $this->db->select_max($fieldName)->get($tableName)->row()->{$fieldName};
// 	}

// 	function deleteRowByAttribute($tableName, $attribute)
// 	{
// 		$this->db->trans_start();
// 		$this->db->delete($tableName, $attribute);
// 		$this->db->trans_complete();
// 		if ($this->db->trans_status() == TRUE) {
// 			return TRUE;
// 		} else {
// 			return FALSE;
// 		}
// 	}

//     function Trims_booking($user_id = 10.01){
// 		//print_r('6');die;
// 		//UOM lib
// 		$unit_of_measurement = array(1 => "Pcs", 2 => "Dzn", 3 => "Grs", 4 => "GG", 10 => "Mg", 11 => "Gm", 12 => "Kg", 13 => "Quintal", 14 => "Ton", 15 => "Lbs", 20 => "Km", 21 => "Hm", 22 => "Dm", 23 => "Mtr", 24 => "Dcm", 25 => "CM", 26 => "MM", 27 => "Yds", 28 => "Feet", 29 => "Inch", 30 => "CFT", 31 => "SFT", 40 => "Ltr", 41 => "ML", 50 => "Roll", 51 => "Coil", 52 => "Cone", 53 => "Bag", 54 => "Box", 55 => "Drum", 56 => "Bottle", 57 => "Pack", 58 => "Set", 59 => "Can", 60 => "Each", 61 => "Gallon", 62 => "Lachi", 63 => "Pair", 64 => "Lot", 65 => "Packet", 66 => "Pot", 67 => "Book", 68 => "Culind", 69 => "Ream", 70 => "Cft", 71 => "Syp", 72 => "K.V", 73 => "CU-M3", 74 => "Bundle", 75 => "Strip", 76 => "SQM", 77 => "Ounce", 78 => "Cylinder", 79 => "Course", 80 => "Sheet", 81 => "RFT", 82 => "Square Inch", 83 => "Carton", 84 => "Thane", 85 => "Gross Yds", 86 => "Jar", 87 => "Reel", 88 => "CBM",89=>"Tub",90=>"KVA",91=>"KW",92=>"Pallet",93=>"Case",94=>"Job",95=>"KIT");
// 		//UOM lib

// 		//currency lib
// 		$currency = array(1 => "BDT", 2 => "USD", 3 => "EURO", 4 => "CHF", 5 => "SGD", 6 => "Pound", 7 => "YEN");
// 		//currency lib end

// 		$this->deleteRowByAttribute('GBL_TEMP_ENGINE', array('ENTRY_FORM' =>10.01 , "USER_ID" => $user_id));
		
// 		//start
// 		// $query_lc_details = "SELECT a.CONVERTIBLE_TO_LC, 
// 		// b.WO_PO_BREAK_DOWN_ID, 
// 		// a.CONTRACT_NO             AS SALES_CONTRACT, 
// 		// a.BANK_FILE_NO            AS SALES_BANK_FILE_NO, 
// 		// a.INTERNAL_FILE_NO_BK     AS SALES_INTERNAL_FILE_NO, 
// 		// c.EXPORT_LC_NO, 
// 		// c.BANK_FILE_NO            AS LC_BANK_FILE_NO, 
// 		// c.INTERNAL_FILE_NO_BK    AS LC_INTERNAL_FILE_NO, 
// 		// e.INVOICE_NO 
// 		// FROM COM_SALES_CONTRACT a, COM_SALES_CONTRACT_ORDER_INFO  b, COM_EXPORT_LC c, COM_EXPORT_LC_ORDER_INFO d, COM_EXPORT_INVOICE_SHIP_MST  e WHERE a.ID = b.COM_SALES_CONTRACT_ID AND c.ID = d.COM_EXPORT_LC_ID AND b.WO_PO_BREAK_DOWN_ID = d.WO_PO_BREAK_DOWN_ID AND e.LC_SC_ID = a.ID";

// 		$query_lc_details = "SELECT a.CONVERTIBLE_TO_LC, b.WO_PO_BREAK_DOWN_ID, a.CONTRACT_NO AS SALES_CONTRACT, a.BANK_FILE_NO AS SALES_BANK_FILE_NO, a.INTERNAL_FILE_NO_BK AS SALES_INTERNAL_FILE_NO, e.INVOICE_NO FROM COM_SALES_CONTRACT a, COM_SALES_CONTRACT_ORDER_INFO  b left JOIN COM_EXPORT_LC_ORDER_INFO d  ON b.WO_PO_BREAK_DOWN_ID = d.WO_PO_BREAK_DOWN_ID, COM_EXPORT_INVOICE_SHIP_MST e WHERE a.ID = b.COM_SALES_CONTRACT_ID AND e.LC_SC_ID = a.ID";
// 		$table_lc_details = $this->db->query($query_lc_details)->result();

// 		$max_tmp_id = $this->get_max_value("GBL_TEMP_ENGINE", "ID") + 1;
// 		//print_r($max_tmp_id);die;
// 		$lc_details = Array();
// 		$temp_po = Array();
// 		foreach($table_lc_details as $row){
// 			if($row->CONVERTIBLE_TO_LC!=1){

// 				$temp_po[$row->WO_PO_BREAK_DOWN_ID] = array(
// 					'ID' => $max_tmp_id,
// 					'USER_ID' => $user_id,
// 					'REF_FROM' => 10.01,
// 					'ENTRY_FORM' => 10.01,
// 					'REF_VAL' => $row->WO_PO_BREAK_DOWN_ID
// 				);
// 				$max_tmp_id++;

// 				$lc_details[$row->WO_PO_BREAK_DOWN_ID] = [
// 					"SC/LC" => $row->SALES_CONTRACT,
// 					"BANK_FILE_NO" => $row->SALES_BANK_FILE_NO,
// 					"INTERNAL_FILE_NO" => $row->SALES_INTERNAL_FILE_NO,
// 					"proforma_invoice" => $row->INVOICE_NO
// 				];
// 			}else{

// 				$temp_po[$row->WO_PO_BREAK_DOWN_ID] = Array(
// 					'ID' => $max_tmp_id,
// 					'USER_ID' => $user_id,
// 					'REF_FROM' => 10.01,
// 					'ENTRY_FORM' => 10.01,
// 					'REF_VAL' => $row->WO_PO_BREAK_DOWN_ID
// 				);
// 				$max_tmp_id++;

// 				// $lc_details[$row->WO_PO_BREAK_DOWN_ID] = [
// 				// 	"SC/LC" => $row->EXPORT_LC_NO,
// 				// 	"BANK_FILE_NO" => $row->LC_BANK_FILE_NO,
// 				// 	"INTERNAL_FILE_NO" => $row->LC_INTERNAL_FILE_NO,
// 				// 	"proforma_invoice" => $row->INVOICE_NO
// 				// ];

// 				$lc_details[$row->WO_PO_BREAK_DOWN_ID] = [
// 					"SC/LC" => "1",
// 					"BANK_FILE_NO" => "1",
// 					"INTERNAL_FILE_NO" => "1",
// 					"proforma_invoice" => "1"
// 				];
// 			}
// 		}
// 		//die;
// 		//print_r($temp_po);die;
// 		//end
// 		$this->db->insert_batch("GBL_TEMP_ENGINE", $temp_po);
		
// 		$query_trims_booking = "SELECT g.DELIVERY_ADDRESS,g.BOOKING_DATE,f.BOOKING_NO,a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id                 AS bom_trim_id, c.trim_group, c.description        AS description_pre_cost, c.brand_sup_ref      AS brand_sup_ref_precost, c.country, c.rate,g.UOM, d.id                 AS po_id, d.po_number, d.po_quantity        AS plan_cut, MIN (e.id)           AS id, e.po_break_down_id, AVG (e.cons)         AS cons, SUM (f.wo_qnty)      AS cu_woq, SUM (f.amount) AS cu_amount, f.id AS booking_id, f.sensitivity, f.delivery_date, f.description        AS description, f.brand_supplier     AS brand_sup_ref, f.is_workable FROM wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e, wo_booking_dtls f,WO_BOOKING_MST g,GBL_TEMP_ENGINE h WHERE e.po_break_down_id = h.REF_VAL AND a.job_no = b.job_no AND a.job_no = c.job_no AND a.job_no = d.job_no_mst AND a.job_no = e.job_no AND a.job_no = f.job_no AND c.id = e.wo_pre_cost_trim_cost_dtls_id AND d.id = e.po_break_down_id AND e.wo_pre_cost_trim_cost_dtls_id = f.pre_cost_fabric_cost_dtls_id AND e.po_break_down_id = f.po_break_down_id AND g.ID = f.BOOKING_MST_ID AND f.booking_type = 2 AND a.garments_nature = 3 AND d.is_deleted = 0 AND d.status_active = 1 AND f.status_active = 1 AND f.is_deleted = 0 and h.USER_ID = 10.10 AND h.ENTRY_FORM = 10.10 GROUP BY a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, f.id, f.sensitivity, f.delivery_date, f.description, f.brand_supplier, f.is_workable,f.BOOKING_NO,g.BOOKING_DATE,g.UOM,g.DELIVERY_ADDRESS ORDER BY d.id, c.id";

//         $table_trims_booking = $this->db->query($query_trims_booking)->result();
// 		//print_r($table_trims_booking);die;
// 		foreach($table_trims_booking as $row){
// 			//print_r($row->PO_BREAK_DOWN_ID);die;
// 			$trims_details[$row->BOOKING_NO][$row->TRIM_GROUP] = [
// 				"booking_no" => $row->BOOKING_NO,
// 				"po_no" => $row->PO_NUMBER,
// 				"booking_date" =>$row->BOOKING_DATE,
// 				"supplier_name" => $row->BRAND_SUP_REF,
// 				"delivery_date" => $row->DELIVERY_DATE,
// 				"delivery_address" => $row->DELIVERY_ADDRESS,
// 				"trims_group" => $row->TRIM_GROUP,
// 				"total_price" => number_format($row->CU_AMOUNT,2),
// 				"uom" => $row->UOM,
// 				"woq" => number_format($row->CU_WOQ,2),
// 				"rate" => number_format($row->RATE,2),
// 				"amount" => number_format($row->CU_AMOUNT),
// 				"description" => $row->DESCRIPTION,
// 				"po_break_down_id" => $row->PO_BREAK_DOWN_ID,
// 			];
// 		}
		
// 		$query_trims_booking_mst = "SELECT a.BOOKING_NO,b.PO_BREAK_DOWN_ID FROM WO_BOOKING_MST a,WO_BOOKING_DTLS b,GBL_TEMP_ENGINE c WHERE a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 AND b.PO_BREAK_DOWN_ID = c.REF_VAL AND a.ID = b.PO_BREAK_DOWN_ID and c.USER_ID = 10.10 AND c.ENTRY_FORM = 10.10";
// 		$table_trims_booking_mst = $this->db->query($query_trims_booking_mst)->result();
// 		//print_r($table_trims_booking_mst);die;
// 		foreach($table_trims_booking_mst as $row){
// 			if( $lc_details[$row->PO_BREAK_DOWN_ID] == null){
// 				continue;
// 			}
// 			$return_array[$row->BOOKING_NO] = [
// 				"booking_number" => $row->BOOKING_NO,
// 				"PO_BREAK_DOWN_ID" => $row->PO_BREAK_DOWN_ID,
// 				"invoice" => $lc_details[$row->PO_BREAK_DOWN_ID],
// 				"trims_details " => $trims_details[$row->BOOKING_NO],
// 			];
// 		}
		
// 		//print_r($lc_details);die;

// 		return $return_array;
//     }
// }