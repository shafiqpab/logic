<?php
class Pi_info extends CI_Model
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

	function Pi_info($pi_no, $work_order)
	{
		//print_r('6');die;
		//UOM lib
		//$unit_of_measurement = array(1 => "Pcs", 2 => "Dzn", 3 => "Grs", 4 => "GG", 10 => "Mg", 11 => "Gm", 12 => "Kg", 13 => "Quintal", 14 => "Ton", 15 => "Lbs", 20 => "Km", 21 => "Hm", 22 => "Dm", 23 => "Mtr", 24 => "Dcm", 25 => "CM", 26 => "MM", 27 => "Yds", 28 => "Feet", 29 => "Inch", 30 => "CFT", 31 => "SFT", 40 => "Ltr", 41 => "ML", 50 => "Roll", 51 => "Coil", 52 => "Cone", 53 => "Bag", 54 => "Box", 55 => "Drum", 56 => "Bottle", 57 => "Pack", 58 => "Set", 59 => "Can", 60 => "Each", 61 => "Gallon", 62 => "Lachi", 63 => "Pair", 64 => "Lot", 65 => "Packet", 66 => "Pot", 67 => "Book", 68 => "Culind", 69 => "Ream", 70 => "Cft", 71 => "Syp", 72 => "K.V", 73 => "CU-M3", 74 => "Bundle", 75 => "Strip", 76 => "SQM", 77 => "Ounce", 78 => "Cylinder", 79 => "Course", 80 => "Sheet", 81 => "RFT", 82 => "Square Inch", 83 => "Carton", 84 => "Thane", 85 => "Gross Yds", 86 => "Jar", 87 => "Reel", 88 => "CBM",89=>"Tub",90=>"KVA",91=>"KW",92=>"Pallet",93=>"Case",94=>"Job",95=>"KIT");
		//UOM lib

		//currency lib
		//$currency = array(1 => "BDT", 2 => "USD", 3 => "EURO", 4 => "CHF", 5 => "SGD", 6 => "Pound", 7 => "YEN");
		//currency lib end



		if ($pi_no) {
			//$pi_no = str_replace(',', "','", $pi_no);
			//$where_con = " AND PI_NUMBER in ('" . $pi_no . "') ";
			$where_con = " AND PI_NUMBER Like '%$pi_no%'";
		} else {
			$where_con = "";
		}

		if ($work_order) {
			$where_con .= "AND b.WORK_ORDER_ID in ($work_order)";
		}

		//print_r($pi_bib);die;
		$query_pi = "SELECT a.ID,a.PI_NUMBER,a.PI_DATE FROM COM_PI_MASTER_DETAILS a,COM_PI_ITEM_DETAILS b WHERE a.ID = b.PI_ID and a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1  $where_con";
		//print_r($query_pi);die;
		$table_pi = $this->db->query($query_pi)->result();
		foreach ($table_pi as $row) {
			$pi_array[$row->ID] = [
				"ID" => $row->ID,
				"PI_NUMBER" => $row->PI_NUMBER,
				"PI_DATE" => $row->PI_DATE,
			];
		}
		$pi_array_2 = Array();
		foreach($pi_array as $row){
			$pi_array_2[]=$row;
		}


		return $pi_array_2;
	}
}
