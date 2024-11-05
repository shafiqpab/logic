<?php
class LC_model extends CI_Model
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

	function Lc($pi_no,$lc_number,$pi_id){
		//print_r('6');die;
		//UOM lib
		//$unit_of_measurement = array(1 => "Pcs", 2 => "Dzn", 3 => "Grs", 4 => "GG", 10 => "Mg", 11 => "Gm", 12 => "Kg", 13 => "Quintal", 14 => "Ton", 15 => "Lbs", 20 => "Km", 21 => "Hm", 22 => "Dm", 23 => "Mtr", 24 => "Dcm", 25 => "CM", 26 => "MM", 27 => "Yds", 28 => "Feet", 29 => "Inch", 30 => "CFT", 31 => "SFT", 40 => "Ltr", 41 => "ML", 50 => "Roll", 51 => "Coil", 52 => "Cone", 53 => "Bag", 54 => "Box", 55 => "Drum", 56 => "Bottle", 57 => "Pack", 58 => "Set", 59 => "Can", 60 => "Each", 61 => "Gallon", 62 => "Lachi", 63 => "Pair", 64 => "Lot", 65 => "Packet", 66 => "Pot", 67 => "Book", 68 => "Culind", 69 => "Ream", 70 => "Cft", 71 => "Syp", 72 => "K.V", 73 => "CU-M3", 74 => "Bundle", 75 => "Strip", 76 => "SQM", 77 => "Ounce", 78 => "Cylinder", 79 => "Course", 80 => "Sheet", 81 => "RFT", 82 => "Square Inch", 83 => "Carton", 84 => "Thane", 85 => "Gross Yds", 86 => "Jar", 87 => "Reel", 88 => "CBM",89=>"Tub",90=>"KVA",91=>"KW",92=>"Pallet",93=>"Case",94=>"Job",95=>"KIT");
		//UOM lib

		//currency lib
		//$currency = array(1 => "BDT", 2 => "USD", 3 => "EURO", 4 => "CHF", 5 => "SGD", 6 => "Pound", 7 => "YEN");
		//currency lib end

        
        
		if($pi_no){
            $pi_no = str_replace(',',"','",$pi_no);
            $where_con = " and c.PI_NUMBER in ('".$pi_no."') ";
        }else{
            $where_con = "";
        }

		if($lc_number){
			$where_con .="And a.LC_NUMBER Like '%$lc_number%'";
		}
		if($pi_id){
			$where_con .="And c.ID in ($pi_id)";
		}
		
		//print_r($pi_bib);die;
		// $query_lc = "SELECT * FROM COM_BTB_LC_MASTER_DETAILS a,COM_BTB_LC_PI b,COM_PI_MASTER_DETAILS c WHERE a.ID = b.COM_BTB_LC_MASTER_DETAILS_ID and b.PI_ID = c.ID AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 $where_con";

		$query_lc = "SELECT a.ID, a.LC_NUMBER, a.LC_DATE FROM COM_BTB_LC_MASTER_DETAILS a, COM_BTB_LC_PI b, COM_PI_MASTER_DETAILS c WHERE     a.ID = b.COM_BTB_LC_MASTER_DETAILS_ID AND b.PI_ID = c.ID AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 $where_con";
        //print_r($query_pi);die;
		$table_lc = $this->db->query($query_lc)->result();
		
		foreach($table_lc as $row){
			$lc_array[$row->ID] = [
				"ID" => $row->ID,
				"LC_NUMBER" => $row->LC_NUMBER,
				"LC_DATE" => $row->LC_DATE,
			];
		}
		$lc_array_2 = Array();
		foreach($lc_array as $row){
			$lc_array_2[] = $row;
		}
		
		return $lc_array_2;
		
	}	
}