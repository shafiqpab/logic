<?php
class Dms_in_out_bills extends CI_Model
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

	function Dms_in_out_bills($in_bill_no,$out_bill_no){
		//print_r('6');die;
		//UOM lib
		$unit_of_measurement = array(1 => "Pcs", 2 => "Dzn", 3 => "Grs", 4 => "GG", 10 => "Mg", 11 => "Gm", 12 => "Kg", 13 => "Quintal", 14 => "Ton", 15 => "Lbs", 20 => "Km", 21 => "Hm", 22 => "Dm", 23 => "Mtr", 24 => "Dcm", 25 => "CM", 26 => "MM", 27 => "Yds", 28 => "Feet", 29 => "Inch", 30 => "CFT", 31 => "SFT", 40 => "Ltr", 41 => "ML", 50 => "Roll", 51 => "Coil", 52 => "Cone", 53 => "Bag", 54 => "Box", 55 => "Drum", 56 => "Bottle", 57 => "Pack", 58 => "Set", 59 => "Can", 60 => "Each", 61 => "Gallon", 62 => "Lachi", 63 => "Pair", 64 => "Lot", 65 => "Packet", 66 => "Pot", 67 => "Book", 68 => "Culind", 69 => "Ream", 70 => "Cft", 71 => "Syp", 72 => "K.V", 73 => "CU-M3", 74 => "Bundle", 75 => "Strip", 76 => "SQM", 77 => "Ounce", 78 => "Cylinder", 79 => "Course", 80 => "Sheet", 81 => "RFT", 82 => "Square Inch", 83 => "Carton", 84 => "Thane", 85 => "Gross Yds", 86 => "Jar", 87 => "Reel", 88 => "CBM",89=>"Tub",90=>"KVA",91=>"KW",92=>"Pallet",93=>"Case",94=>"Job",95=>"KIT");
		//UOM lib

		//currency lib
		$currency = array(1 => "BDT", 2 => "USD", 3 => "EURO", 4 => "CHF", 5 => "SGD", 6 => "Pound", 7 => "YEN");
		//currency lib end

		if($in_bill_no){
            $where_con = " AND BILL_NO LIKE '%$in_bill_no%' ";
        }else{
            $where_con = "";
        }

        if($out_bill_no){
            $where_con_2 = "AND BILL_NO LIKE '%$out_bill_no%' ";
        }else{
            $where_con_2 = "";
        }

		
		//print_r($pi_bib);die;
		$query_SUBCON_INBOUND_BILL_MST = "SELECT * FROM SUBCON_INBOUND_BILL_MST WHERE IS_DELETED = 0 AND STATUS_ACTIVE = 1  $where_con";
		$table_SUBCON_INBOUND_BILL_MST = $this->db->query($query_SUBCON_INBOUND_BILL_MST)->result();

        $query_SUBCON_OUTBOUND_BILL_MST = "SELECT * FROM SUBCON_OUTBOUND_BILL_MST WHERE IS_DELETED = 0 AND STATUS_ACTIVE = 1   $where_con_2";
		$table_SUBCON_OUTBOUND_BILL_MST = $this->db->query($query_SUBCON_OUTBOUND_BILL_MST)->result();


		return $booking_array =[
            "in_bills" => $table_SUBCON_INBOUND_BILL_MST,
            "out_bills"=> $table_SUBCON_OUTBOUND_BILL_MST
            ];
		
	}	
}