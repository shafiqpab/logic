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

	function Lc($category_id=0, $pi_no="", $lc_number="", $pi_id=""){
		
		if ($category_id>0) {
			$where_con = " AND c.ITEM_CATEGORY_ID=$category_id";
		} else {
			$where_con = "";
		}

		if($pi_no){
            $pi_no = str_replace(',',"','",$pi_no);
            $where_con .= " and c.PI_NUMBER in ('".$pi_no."') ";
        }

		if($lc_number){
			$where_con .="And a.LC_NUMBER Like '%$lc_number%'";
		}
		if($pi_id){
			$where_con .="And c.ID in ($pi_id)";
		}
		
		
	
		$query_lc = "SELECT a.ID, a.LC_NUMBER, a.LC_DATE FROM COM_BTB_LC_MASTER_DETAILS a, COM_BTB_LC_PI b, COM_PI_MASTER_DETAILS c WHERE a.ID = b.COM_BTB_LC_MASTER_DETAILS_ID AND b.PI_ID = c.ID AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 $where_con";
		
        
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