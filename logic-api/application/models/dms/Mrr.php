<?php
class Mrr extends CI_Model
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

	function mrr($category_id,$mrr, $parchase_order,$pi_ids)
	{


		if ($category_id>0) {
			$where_con = " AND b.ITEM_CATEGORY=$category_id";
		} else {
			$where_con = "";
		}

		if ($mrr) {
			$where_con .= " AND b.RECV_NUMBER Like '%$mrr%' ";
		}

		if ($parchase_order) {

			$where_con .= "AND a.id in ($parchase_order)";
		}
		if ($pi_ids){
			$pi_con = " AND c.PI_ID in($pi_ids)";
		}
		
		$query_inv_receive = "SELECT a.ID AS WO_ID, a.WO_NUMBER, b.ID AS RCV_ID, b.RECV_NUMBER, b.CHALLAN_NO, b.RECEIVE_DATE,b.ITEM_CATEGORY FROM WO_NON_ORDER_INFO_MST a, INV_RECEIVE_MASTER b WHERE     a.id = b.booking_id AND a.PAY_MODE <> 2 AND b.receive_basis = 2 AND a.status_active = 1 AND b.status_active = 1 AND b.ENTRY_FORM in (1,20,4,37,17,24,350,631)
		$where_con 
		GROUP BY a.ID, a.WO_NUMBER, b.ID, b.RECV_NUMBER, b.CHALLAN_NO, b.RECEIVE_DATE,b.ITEM_CATEGORY
		UNION ALL 
		SELECT a.ID AS WO_ID, a.WO_NUMBER, b.ID     AS RCV_ID, b.RECV_NUMBER, b.CHALLAN_NO, b.RECEIVE_DATE,b.ITEM_CATEGORY FROM WO_NON_ORDER_INFO_MST a, COM_PI_ITEM_DETAILS c, INV_RECEIVE_MASTER b WHERE     a.id = c.WORK_ORDER_ID AND c.PI_ID = b.booking_id AND a.PAY_MODE = 2 AND b.receive_basis = 1 AND a.status_active = 1 AND b.status_active = 1 AND b.ENTRY_FORM in (1,20,4,37,17,24,350,631)
		$where_con $pi_con 
		GROUP BY a.ID, a.WO_NUMBER, b.ID, b.RECV_NUMBER, b.CHALLAN_NO, b.RECEIVE_DATE,b.ITEM_CATEGORY";

		$table_inv_receive = $this->db->query($query_inv_receive)->result();

		foreach ($table_inv_receive as $row) {
			$mrr_array[$row->RCV_ID] = [
				"ID" => $row->RCV_ID,
				"RECV_NUMBER" => $row->RECV_NUMBER,
				"CHALLAN_NO" => $row->CHALLAN_NO,
				"RECEIVE_DATE" => $row->RECEIVE_DATE,
				"WORK_ORDER_NO" => $row->WO_NUMBER,
				"WORK_ORDER_ID" => $row->WO_ID,
				"ITEM_CATEGORY" => $row->ITEM_CATEGORY,
			];
		}
		$mrr_array_2 = array();
		foreach ($mrr_array as $row) {
			$mrr_array_2[] = $row;
		}

		return $mrr_array_2;
	}

	function item_category_list($category_id=0)
	{	
		$item_category_type_arr= array();

		$cat_cond = ($category_id>0)?" and CATEGORY_ID=$category_id":"";
		$query_item_category ="select CATEGORY_ID, SHORT_NAME from  lib_item_category_list where status_active=1 and is_deleted=0 $cat_cond"; 
		$table_item_category = $this->db->query($query_item_category)->result();
		return $table_item_category;
	}
}
