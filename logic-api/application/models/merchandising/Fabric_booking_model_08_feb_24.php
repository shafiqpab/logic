<?php
class Fabric_booking_model extends CI_Model
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

    function Fabric_booking(){
		//UOM lib
		$unit_of_measurement = array(1 => "Pcs", 2 => "Dzn", 3 => "Grs", 4 => "GG", 10 => "Mg", 11 => "Gm", 12 => "Kg", 13 => "Quintal", 14 => "Ton", 15 => "Lbs", 20 => "Km", 21 => "Hm", 22 => "Dm", 23 => "Mtr", 24 => "Dcm", 25 => "CM", 26 => "MM", 27 => "Yds", 28 => "Feet", 29 => "Inch", 30 => "CFT", 31 => "SFT", 40 => "Ltr", 41 => "ML", 50 => "Roll", 51 => "Coil", 52 => "Cone", 53 => "Bag", 54 => "Box", 55 => "Drum", 56 => "Bottle", 57 => "Pack", 58 => "Set", 59 => "Can", 60 => "Each", 61 => "Gallon", 62 => "Lachi", 63 => "Pair", 64 => "Lot", 65 => "Packet", 66 => "Pot", 67 => "Book", 68 => "Culind", 69 => "Ream", 70 => "Cft", 71 => "Syp", 72 => "K.V", 73 => "CU-M3", 74 => "Bundle", 75 => "Strip", 76 => "SQM", 77 => "Ounce", 78 => "Cylinder", 79 => "Course", 80 => "Sheet", 81 => "RFT", 82 => "Square Inch", 83 => "Carton", 84 => "Thane", 85 => "Gross Yds", 86 => "Jar", 87 => "Reel", 88 => "CBM",89=>"Tub",90=>"KVA",91=>"KW",92=>"Pallet",93=>"Case",94=>"Job",95=>"KIT");
		//UOM lib

		//currency lib
		$currency = array(1 => "BDT", 2 => "USD", 3 => "EURO", 4 => "CHF", 5 => "SGD", 6 => "Pound", 7 => "YEN");
		//currency lib end


		//print_r($lc_details[4736]["SC/LC"]);die;
		// $query_wo_booking = "SELECT a.BOOKING_NO, a.BOOKING_DATE, a.SUPPLIER_ID, a.DELIVERY_DATE, a.DELIVERY_ADDRESS, c.UOM, a.CURRENCY_ID, b.PO_BREAK_DOWN_ID, b.FIN_FAB_QNTY, c.ID AS FEBRIC_ID, c.FABRIC_DESCRIPTION, b.RATE, b.AMOUNT
		// FROM wo_booking_mst a, WO_BOOKING_DTLS b, wo_pre_cost_fabric_cost_dtls c WHERE c.id = b.PRE_COST_FABRIC_COST_DTLS_ID AND b.BOOKING_MST_ID = a.ID AND a.BOOKING_TYPE = 1 AND a.IS_SHORT = 2 AND a.ENTRY_FORM = 271 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND c.STATUS_ACTIVE = 1 AND c.IS_DELETED = 0 FETCH FIRST 10000 ROWS ONLY";
		//print_r($query_wo_booking);die;

        //$table_wo_booking = $this->db->query($query_wo_booking)->result();
		//print_r($table_wo_booking);die;

		//$this->deleteRowByAttribute('GBL_TEMP_ENGINE', array('ENTRY_FORM' =>10.01 , "USER_ID" => $user_id));

		// 	$query_invoice = "SELECT a.INVOICE_NO,b.PI_ID,b.IMPORT_INVOICE_ID FROM COM_IMPORT_INVOICE_MST a, COM_IMPORT_INVOICE_dtls b WHERE a.id = b.IMPORT_INVOICE_ID AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0";
		// 	$table_invoice = $this->db->query($query_invoice)->result();
		// 	$invoice_array = Array();
		// 	foreach($table_invoice as $row){
		// 		$invoice_array[$row->PI_ID][]=$row->INVOICE_NO;
		// 	}
		// 	//print_r($invoice_array);die;
		// 	$query_sales_contract = "SELECT c.CONTRACT_NO,c.CONVERTIBLE_TO_LC,a.BTB_SYSTEM_ID FROM COM_BTB_LC_MASTER_DETAILS  a, COM_BTB_EXPORT_LC_ATTACHMENT  b, COM_SALES_CONTRACT c WHERE a.id = b.IMPORT_MST_ID AND b.LC_SC_ID = c.id AND b.IS_LC_SC = 1";
		// 	$table_sales_contract = $this->db->query($query_sales_contract)->result();
		// 	$sales_contract = Array();
		// 	foreach($table_sales_contract as $row){
		// 		$sales_contract[$row->BTB_SYSTEM_ID][]=[
		// 			"sales_contract"=>$row->CONTRACT_NO,
		// 			"convertible_to_lc"=>$row->CONVERTIBLE_TO_LC,
		// 		];
		// 	} 
		// 	//print_r($sales_contract);die;
		// 	$query_export_lc = "SELECT c.EXPORT_LC_NO,a.BTB_SYSTEM_ID FROM COM_BTB_LC_MASTER_DETAILS     a, COM_BTB_EXPORT_LC_ATTACHMENT  b, COM_EXPORT_LC c WHERE a.id = b.IMPORT_MST_ID AND b.LC_SC_ID = c.id AND b.IS_LC_SC = 0";
		// 	$table_export_lc = $this->db->query($query_export_lc)->result();
		// 	//$export_lc = Array();
		// 	foreach($table_export_lc as $row){
		// 		$export_lc[$row->BTB_SYSTEM_ID][]=$row->EXPORT_LC_NO;
		// 	}

		// 	$query_PI_and_BTB = "SELECT a.ID,a.PI_NUMBER, b.WORK_ORDER_NO,d.BTB_SYSTEM_ID FROM com_pi_master_details a, com_pi_item_details b, COM_BTB_LC_PI c, COM_BTB_LC_MASTER_DETAILS  d WHERE  a.id = b.pi_id AND b.pi_id = c.pi_id AND c.COM_BTB_LC_MASTER_DETAILS_ID = d.id AND b.WORK_ORDER_NO is not null AND a.ITEM_CATEGORY_ID = 4";
		// 	$table_PI_and_BTB = $this->db->query($query_PI_and_BTB)->result();

		// 	$pi_bib = Array();
		// 	foreach($table_PI_and_BTB as $row){
		// 		if($invoice_array[$row->ID] == null){
		// 			continue;
		// 		}elseif($sales_contract[$row->BTB_SYSTEM_ID] == null){
		// 			continue;
		// 		}elseif($export_lc[$row->BTB_SYSTEM_ID]){
		// 			continue;
		// 		}
		// 		$pi_bib[$row->WORK_ORDER_NO][]=[
		// 			"pi_number"=>$row->PI_NUMBER,
		// 			"work_order"=>$row->WORK_ORDER_NO,
		// 			"btb_number"=>$row->BTB_SYSTEM_ID,
		// 			"invoice"=> $invoice_array[$row->ID],
		// 			"sales_contract"=> $sales_contract[$row->BTB_SYSTEM_ID],
		// 			"export_lc"=> $export_lc[$row->BTB_SYSTEM_ID],
		// 		];
		// 	}
		// 	//print_r($pi_bib);die;
		// 	$query_booking = "SELECT a.BOOKING_NO,b.PO_BREAK_DOWN_ID,c.PO_NUMBER, b.AMOUNT,b.RATE,b.UOM,b.TRIM_GROUP,a.BOOKING_DATE FROM WO_BOOKING_MST a, WO_BOOKING_DTLS b,WO_PO_BREAK_DOWN c WHERE     a.ID = b.BOOKING_MST_ID AND c.ID = b.PO_BREAK_DOWN_ID AND a.BOOKING_TYPE = 1 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0";
		// 	$table_booking = $this->db->query($query_booking)->result();
		// 	//print_r($table_booking);die;
		// 	$booking_array = Array();
		// 	foreach($table_booking as $row){
		// 		// if($pi_bib[$row->BOOKING_NO]==null){
		// 		// 	continue;
		// 		// }
		// 		$booking_array[$row->BOOKING_NO][$row->PO_BREAK_DOWN_ID] = [
		// 			"booking_no" => $row->BOOKING_NO,
		// 			"po_break_down_id" => $row->PO_BREAK_DOWN_ID,
		// 			"po_num" => $row->PO_NUMBER,
		// 			"amount" => $row->AMOUNT,
		// 			"rate" => $row->RATE,
		// 			"uom" => $row->UOM,
		// 			"TRIM_GROUP" => $row->TRIM_GROUP,
		// 			"BOOKING_DATE" => $row->BOOKING_DATE,
		// 			"pi_btb" => $pi_bib[$row->BOOKING_NO],
		// 		];
		// 	}
		// 	return $booking_array;

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
		$query_booking = "SELECT a.BOOKING_NO,b.PO_BREAK_DOWN_ID,c.PO_NUMBER, b.AMOUNT,b.RATE,b.UOM,a.CURRENCY_ID,b.TRIM_GROUP,a.BOOKING_DATE FROM WO_BOOKING_MST a, WO_BOOKING_DTLS b,WO_PO_BREAK_DOWN c WHERE a.ID = b.BOOKING_MST_ID AND c.ID = b.PO_BREAK_DOWN_ID AND a.BOOKING_TYPE = 1 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0";
		$table_booking = $this->db->query($query_booking)->result();

		$booking_array = Array();
		foreach($table_booking as $row){
			// if($pi_bib[$row->BOOKING_NO]==null){
			// 	continue;
			// }
			$booking_array[$row->BOOKING_NO][$row->PO_BREAK_DOWN_ID] = [
				"booking_no" => $row->BOOKING_NO,
				"po_break_down_id" => $row->PO_BREAK_DOWN_ID,
				"po_num" => $row->PO_NUMBER,
				"amount" => $row->AMOUNT,
				"rate" => $row->RATE,
				"uom" => $unit_of_measurement[$row->UOM],
				"currency" => $currency[$row->CURRENCY_ID],				
				"BOOKING_DATE" => $row->BOOKING_DATE,
				"pi_btb" => $pi_bib[$row->BOOKING_NO],
			];
		}
		return $booking_array;
     }
}