<?php
class purchase_order_details extends CI_Model
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

	function purchase_order_details($order_no, $requ_id)
	{
		//print_r('6');die;
		$query_lib_supplier = "SELECT ID,SUPPLIER_NAME FROM LIB_SUPPLIER ";
		$table_lib_supplier = $this->db->query($query_lib_supplier)->result();
		$lib_supplier = array();
		foreach ($table_lib_supplier as $row) {
			$lib_supplier[$row->ID] = $row->SUPPLIER_NAME;
		}

		$where_con = "";
		if ($order_no) {
			$where_con .= " AND d.WO_NUMBER Like '%$order_no%'";
		} else {
			$where_con .= "";
		}

		if ($requ_id) {
			$where_con .= " and a.ID in ($requ_id)";
		} else {
			$where_con .= "";
		}


		//print_r($pi_bib);die;
		$query_INV_PURCHASE_REQUISITION_MST = "SELECT d.ID,d.WO_NUMBER,d.SUPPLIER_ID,d.WO_DATE FROM INV_PURCHASE_REQUISITION_MST a, INV_PURCHASE_REQUISITION_DTLS b, WO_NON_ORDER_INFO_DTLS c,WO_NON_ORDER_INFO_MST d WHERE a.ID = b.MST_ID AND b.ID = c.REQUISITION_DTLS_ID AND d.ID = c.MST_ID AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1   $where_con";
		$table_INV_PURCHASE_REQUISITION_MST = $this->db->query($query_INV_PURCHASE_REQUISITION_MST)->result();
		//  print_r($query_INV_PURCHASE_REQUISITION_MST);
		//  die;
		foreach ($table_INV_PURCHASE_REQUISITION_MST as $row) {
			$booking_array[$row->ID] = [
				"ID" => $row->ID,
				"WO_NUMBER" => $row->WO_NUMBER,				
				"WO_DATE" => $row->WO_DATE,
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
