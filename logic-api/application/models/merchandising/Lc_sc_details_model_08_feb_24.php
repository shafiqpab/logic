<?php
class Lc_sc_details_model extends CI_Model
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

    function lc_sc_details(){

        $query_lib_buyer = "SELECT ID,BUYER_NAME FROM LIB_BUYER";
        $table_lib_buyer = $this->db->query($query_lib_buyer)->result();
        $lib_buyer=array();
        foreach($table_lib_buyer as $row){
            $lib_buyer[$row->ID]=$row->BUYER_NAME;
        }

        $query_lib_location = "SELECT ID,LOCATION_NAME FROM LIB_LOCATION";
        $table_lib_location = $this->db->query($query_lib_location)->result();
        $lib_location=array();
        foreach($table_lib_location as $row){
            $lib_location[$row->ID]=$row->LOCATION_NAME;
        }
        //print_r($table_lib_location);die;
        $query_lib_garment_item = "SELECT ID,ITEM_NAME FROM LIB_GARMENT_ITEM";
        $table_lib_garment_item = $this->db->query($query_lib_garment_item)->result();
        $lib_gmts_item=array();
        foreach($table_lib_garment_item as $row){
            $lib_gmts_item[$row->ID]=$row->ITEM_NAME;
        }

        $query_lib_pro_dept = "SELECT ID,DEPARTMENT_NAME FROM LIB_DEPARTMENT_NAME";
        $table_lib_pro_dept = $this->db->query($query_lib_pro_dept)->result();
        $lib_pro_dept=array();
        foreach($table_lib_pro_dept as $row){
            $lib_pro_dept[$row->ID]=$row->DEPARTMENT_NAME;
        }

        $unit_of_measurement = array(1 => "Pcs", 2 => "Dzn", 3 => "Grs", 4 => "GG", 10 => "Mg", 11 => "Gm", 12 => "Kg", 13 => "Quintal", 14 => "Ton", 15 => "Lbs", 20 => "Km", 21 => "Hm", 22 => "Dm", 23 => "Mtr", 24 => "Dcm", 25 => "CM", 26 => "MM", 27 => "Yds", 28 => "Feet", 29 => "Inch", 30 => "CFT", 31 => "SFT", 40 => "Ltr", 41 => "ML", 50 => "Roll", 51 => "Coil", 52 => "Cone", 53 => "Bag", 54 => "Box", 55 => "Drum", 56 => "Bottle", 57 => "Pack", 58 => "Set", 59 => "Can", 60 => "Each", 61 => "Gallon", 62 => "Lachi", 63 => "Pair", 64 => "Lot", 65 => "Packet", 66 => "Pot", 67 => "Book", 68 => "Culind", 69 => "Ream", 70 => "Cft", 71 => "Syp", 72 => "K.V", 73 => "CU-M3", 74 => "Bundle", 75 => "Strip", 76 => "SQM", 77 => "Ounce", 78 => "Cylinder", 79 => "Course", 80 => "Sheet", 81 => "RFT", 82 => "Square Inch", 83 => "Carton", 84 => "Thane", 85 => "Gross Yds", 86 => "Jar", 87 => "Reel", 88 => "CBM",89=>"Tub",90=>"KVA",91=>"KW",92=>"Pallet",93=>"Case",94=>"Job",95=>"KIT");

        
        $query= "SELECT i.EX_FACTORY_QNTY,g.JOB_NO, b.PO_BREAKDOWN_ID, h.PO_NUMBER, h.PO_RECEIVED_DATE, h.PUB_SHIPMENT_DATE, g.BUYER_NAME, i.ITEM_NUMBER_ID, a.INVOICE_NO, a.LC_SC_ID, a.INVOICE_NO, c.EXPORT_LC_NO, c.INTERNAL_FILE_NO_BK, e.CONTRACT_NO, e.BANK_FILE_NO, e.INTERNAL_FILE_NO, e.CONVERTIBLE_TO_LC, g.LOCATION_NAME, g.PRODUCT_DEPT, h.PO_TOTAL_PRICE, h.PO_QUANTITY, h.UNIT_PRICE, g.ORDER_UOM, e.CONTRACT_NO FROM COM_EXPORT_INVOICE_SHIP_MST    a, COM_EXPORT_INVOICE_SHIP_DTLS   b, COM_EXPORT_LC                  c, COM_EXPORT_LC_ORDER_INFO       d, COM_SALES_CONTRACT             e, COM_SALES_CONTRACT_ORDER_INFO  f, WO_PO_DETAILS_MASTER           g, WO_PO_BREAK_DOWN               h, PRO_EX_FACTORY_MST             i WHERE     a.id = b.MST_ID AND c.ID = d.COM_EXPORT_LC_ID AND b.PO_BREAKDOWN_ID = d.WO_PO_BREAK_DOWN_ID AND e.ID = f.COM_SALES_CONTRACT_ID AND f.WO_PO_BREAK_DOWN_ID = b.PO_BREAKDOWN_ID AND g.ID = h.JOB_ID AND b.PO_BREAKDOWN_ID = h.ID AND i.PO_BREAK_DOWN_ID = b.PO_BREAKDOWN_ID AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND c.STATUS_ACTIVE = 1 AND c.IS_DELETED = 0 AND d.STATUS_ACTIVE = 1 AND d.IS_DELETED = 0 AND e.STATUS_ACTIVE = 1 AND e.IS_DELETED = 0 AND f.STATUS_ACTIVE = 1 AND f.IS_DELETED = 0 AND g.STATUS_ACTIVE = 1 AND g.IS_DELETED = 0 AND h.STATUS_ACTIVE = 1 AND h.IS_DELETED = 0 AND i.STATUS_ACTIVE = 1 AND i.IS_DELETED = 0";
        $table = $this->db->query($query)->result();
        //print_r($table);die;

        $return_data = Array();
        foreach($table as $row) {

            $return_data[]=[
                "job_no" => $row->JOB_NO, 
                "po_id" => $row->PO_BREAKDOWN_ID,
                "po_number" => $row->PO_NUMBER,
                "order_date" => $row->PO_RECEIVED_DATE,
                "shipment_date" => $row->PUB_SHIPMENT_DATE,
                "buyer_id" => $row->BUYER_NAME,
                "buyer_name" => $lib_buyer[$row->BUYER_NAME],
                "location" => $lib_location[$row->LOCATION_NAME],
                "item" =>  $lib_gmts_item[$row->ITEM_NUMBER_ID],
                "item_category" =>  $lib_pro_dept[$row->PRODUCT_DEPT],
                "price_list" => $row->PO_TOTAL_PRICE,
                "order_qty" => $row->PO_QUANTITY,
                "ship_qty" => $row->EX_FACTORY_QNTY,
                "UOM" => $unit_of_measurement[$row->ORDER_UOM],
                "unit_price_usd" => $row->UNIT_PRICE,
                "extended_price" => $row->UNIT_PRICE * $row->PO_QUANTITY,
                "sales_contract" => $row->CONTRACT_NO,
                "bank_file_no" => $row->BANK_FILE_NO,
                "commercial_file_no" => $row->BANK_FILE_NO,
                "foreign_bill_number" => $row->INVOICE_NO,
            ];
        }
        return $return_data;
    }
}