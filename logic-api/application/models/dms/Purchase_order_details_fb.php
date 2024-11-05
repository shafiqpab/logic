<?php
class purchase_order_details_fb extends CI_Model
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

	function purchase_order_details_fb($booking_no, $booking_ids)
	{
		//print_r('6');die;
		$query_lib_supplier = "SELECT ID,SUPPLIER_NAME FROM LIB_SUPPLIER ";
		$table_lib_supplier = $this->db->query($query_lib_supplier)->result();
		$lib_supplier = array();
		foreach ($table_lib_supplier as $row) {
			$lib_supplier[$row->ID] = $row->SUPPLIER_NAME;
		}

		$where_con = "";
		if ($booking_no) {
			$where_con .= " AND BOOKING_NO Like '%$booking_no%'";
		} else {
			$where_con .= "";
		}

		if ($booking_ids) {
			$where_con .= " and ID in ($booking_ids)";
		} else {
			$where_con .= "";
		}


		//print_r($pi_bib);die;
		$query_booking_mst = "SELECT ID,SUPPLIER_ID,BOOKING_DATE,BOOKING_NO from WO_BOOKING_MST WHERE IS_DELETED = 0 AND STATUS_ACTIVE = 1   $where_con";
		$table_booking_mst = $this->db->query($query_booking_mst)->result();
		//  print_r($query_INV_PURCHASE_REQUISITION_MST);
		//  die;
		foreach ($table_booking_mst as $row) {
			$booking_array[$row->ID] = [
				"ID" => $row->ID,
				"WO_NUMBER" => $row->BOOKING_NO,				
				"WO_DATE" => $row->BOOKING_DATE,
				"SUPPLIER_ID" => $row->SUPPLIER_ID,
				"SUPPLIER_NAME" => $lib_supplier[$row->SUPPLIER_ID],
			];
		}
		$booking_array_2 = Array();
		foreach($booking_array as $row){
			$booking_array_2[]=$row;
		}

		return $booking_array_2;
	}
}
