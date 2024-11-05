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

	function Pi_info($category_id=0, $pi_no="", $work_order="")
	{
		if ($category_id>0) {
			$where_con = " AND b.ITEM_CATEGORY_ID=$category_id";
		} else {
			$where_con = "";
		}

		if ($pi_no) {
			$where_con .= " AND PI_NUMBER Like '%$pi_no%'";
		}

		if ($work_order) {
			$where_con .= "AND b.WORK_ORDER_ID in ($work_order)";
		}

		
		$query_pi = "SELECT a.ID,a.PI_NUMBER,a.PI_DATE FROM COM_PI_MASTER_DETAILS a,COM_PI_ITEM_DETAILS b WHERE a.ID = b.PI_ID and a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1  $where_con";

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
