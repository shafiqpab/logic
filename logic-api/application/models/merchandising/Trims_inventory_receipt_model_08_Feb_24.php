<?php

class Trims_inventory_receipt_model extends CI_Model
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

	function Trims_inventory_receipt($start_date,$end_date,$booking_no)
	{
		//print_r(5);die;
		//UOM lib
		$unit_of_measurement = array(1 => "Pcs", 2 => "Dzn", 3 => "Grs", 4 => "GG", 10 => "Mg", 11 => "Gm", 12 => "Kg", 13 => "Quintal", 14 => "Ton", 15 => "Lbs", 20 => "Km", 21 => "Hm", 22 => "Dm", 23 => "Mtr", 24 => "Dcm", 25 => "CM", 26 => "MM", 27 => "Yds", 28 => "Feet", 29 => "Inch", 30 => "CFT", 31 => "SFT", 40 => "Ltr", 41 => "ML", 50 => "Roll", 51 => "Coil", 52 => "Cone", 53 => "Bag", 54 => "Box", 55 => "Drum", 56 => "Bottle", 57 => "Pack", 58 => "Set", 59 => "Can", 60 => "Each", 61 => "Gallon", 62 => "Lachi", 63 => "Pair", 64 => "Lot", 65 => "Packet", 66 => "Pot", 67 => "Book", 68 => "Culind", 69 => "Ream", 70 => "Cft", 71 => "Syp", 72 => "K.V", 73 => "CU-M3", 74 => "Bundle", 75 => "Strip", 76 => "SQM", 77 => "Ounce", 78 => "Cylinder", 79 => "Course", 80 => "Sheet", 81 => "RFT", 82 => "Square Inch", 83 => "Carton", 84 => "Thane", 85 => "Gross Yds", 86 => "Jar", 87 => "Reel", 88 => "CBM", 89 => "Tub", 90 => "KVA", 91 => "KW", 92 => "Pallet", 93 => "Case", 94 => "Job", 95 => "KIT");
		//UOM lib

		//currency lib
		$currency = array(1 => "BDT", 2 => "USD", 3 => "EURO", 4 => "CHF", 5 => "SGD", 6 => "Pound", 7 => "YEN");
		//currency lib end

		//$this->deleteRowByAttribute('GBL_TEMP_ENGINE', array('ENTRY_FORM' => 10.01, "USER_ID" => $user_id));

		$query_supplier = "SELECT ID,SUPPLIER_NAME FROM LIB_SUPPLIER ";
		$table_supplier = $this->db->query($query_supplier)->result();
		$lib_supplier = array();
		foreach ($table_supplier as $row) {
			$lib_supplier[$row->ID] = $row->SUPPLIER_NAME;
		}

		$query_product = "SELECT ID,PRODUCT_NAME_DETAILS FROM PRODUCT_DETAILS_MASTER ";
		$table_product = $this->db->query($query_product)->result();
		$lib_product = array();
		foreach ($table_product as $row) {
			$lib_product[$row->ID] = $row->PRODUCT_NAME_DETAILS;
		}
		//print_r($lib_supplier);die;
		$query_invoice = "SELECT a.INVOICE_NO,b.PI_ID,b.IMPORT_INVOICE_ID FROM COM_IMPORT_INVOICE_MST a, COM_IMPORT_INVOICE_dtls b WHERE a.id = b.IMPORT_INVOICE_ID AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0";
		$table_invoice = $this->db->query($query_invoice)->result();
		$invoice_array = array();
		foreach ($table_invoice as $row) {
			$invoice_array[$row->PI_ID][] = $row->INVOICE_NO;
		}
		//print_r($invoice_array);die;
		$query_sales_contract = "SELECT c.CONTRACT_NO,c.CONVERTIBLE_TO_LC,a.BTB_SYSTEM_ID FROM COM_BTB_LC_MASTER_DETAILS  a, COM_BTB_EXPORT_LC_ATTACHMENT  b, COM_SALES_CONTRACT c WHERE a.id = b.IMPORT_MST_ID AND b.LC_SC_ID = c.id AND b.IS_LC_SC = 1";
		$table_sales_contract = $this->db->query($query_sales_contract)->result();
		$sales_contract = array();
		foreach ($table_sales_contract as $row) {
			$sales_contract[$row->BTB_SYSTEM_ID][$row->CONTRACT_NO] = [
				"sales_contract" => $row->CONTRACT_NO,
				"convertible_to_lc" => $row->CONVERTIBLE_TO_LC,
			];
		}
		//print_r($booking_no);die;
		$query_export_lc = "SELECT c.EXPORT_LC_NO,a.BTB_SYSTEM_ID FROM COM_BTB_LC_MASTER_DETAILS     a, COM_BTB_EXPORT_LC_ATTACHMENT  b, COM_EXPORT_LC c WHERE a.id = b.IMPORT_MST_ID AND b.LC_SC_ID = c.id AND b.IS_LC_SC = 0";
		$table_export_lc = $this->db->query($query_export_lc)->result();
		//$export_lc = Array();
		foreach ($table_export_lc as $row) {
			$export_lc[$row->BTB_SYSTEM_ID][$row->EXPORT_LC_NO] = $row->EXPORT_LC_NO;
		}

		if($booking_no){
			$where_con = " AND b.WORK_ORDER_NO LIKE '%$booking_no%' ";
		}
		
		$query_PI_and_BTB = "SELECT a.ID,a.PI_NUMBER, b.WORK_ORDER_NO,d.BTB_SYSTEM_ID FROM com_pi_master_details a, com_pi_item_details b, COM_BTB_LC_PI c, COM_BTB_LC_MASTER_DETAILS  d WHERE  a.id = b.pi_id AND b.pi_id = c.pi_id AND c.COM_BTB_LC_MASTER_DETAILS_ID = d.id AND a.ITEM_CATEGORY_ID = 4 ";
		$table_PI_and_BTB = $this->db->query($query_PI_and_BTB)->result();
		//print_r($table_PI_and_BTB);die;
		$pi_bib = array();
		foreach ($table_PI_and_BTB as $row) {
			
			$pi_bib[$row->WORK_ORDER_NO][$row->PI_NUMBER] = [
				"pi_number" => $row->PI_NUMBER,
				"work_order" => $row->WORK_ORDER_NO,
				"btb_number" => $row->BTB_SYSTEM_ID,
				"invoice" => $invoice_array[$row->ID],
				"sales_contract" => $sales_contract[$row->BTB_SYSTEM_ID],
				"export_lc" => $export_lc[$row->BTB_SYSTEM_ID],
			];
		}
		//print_r($booking_no);die;
		//$where_con = "";
		if ($start_date && $end_date) {
			$where_con = "AND a.RECEIVE_DATE between TO_DATE('$start_date', 'MM/DD/YYYY') and TO_DATE('$end_date', 'MM/DD/YYYY')";
			
		} else if ($booking_no) {
			$where_con = "AND a.BOOKING_NO LIKE '%$booking_no%'";
			
		}else{
			$where_con = "";
		}
		
		$query_receive = "SELECT a.RECV_NUMBER, a.RECEIVE_DATE, a.BOOKING_ID, a.BOOKING_NO, a.CURRENCY_ID, b.COMPANY_ID, b.SUPPLIER_ID, b.PROD_ID, b.ORDER_UOM, b.ORDER_RATE, b.ORDER_QNTY, b.ORDER_AMOUNT FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b WHERE a.ID = b.MST_ID AND b.TRANSACTION_TYPE = 1 AND b.ITEM_CATEGORY = 4 AND a.ITEM_CATEGORY = 4 $where_con and  a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0";
		$table_receive = $this->db->query($query_receive)->result();
		//print_r($query_booking);die;
		$return_array = array();
		foreach ($table_receive as $row) {
			$return_array[$row->RECV_NUMBER][$row->PROD_ID] = [
                "receipt_no" => $row->RECV_NUMBER,
                "booking_no" => $row->BOOKING_NO,
                "product" => $lib_product[$row->PROD_ID],
                "supplier_id" => $row->SUPPLIER_ID,
                "supplier" => $lib_supplier[$row->SUPPLIER_ID],
                "receive_date" => $row->RECEIVE_DATE,
                "currency" => $currency[$row->CURRENCY_ID],
                "uom" => $unit_of_measurement[$row->ORDER_UOM],
                "rate" => $row->ORDER_RATE,
                "quantity" => $row->ORDER_QNTY,
                "amount" => $row->ORDER_AMOUNT,
				"pi" => $pi_bib[$row->BOOKING_NO],
            ];
			
		}
		return $return_array;
	}
}

		
