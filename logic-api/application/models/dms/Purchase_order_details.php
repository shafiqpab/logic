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

	// function purchase_order_details($category_id=0, $order_no="", $requ_id="",$company_id=0)
	// {
	// 	$query_lib_supplier = "SELECT ID,SUPPLIER_NAME FROM LIB_SUPPLIER ";
	// 	$table_lib_supplier = $this->db->query($query_lib_supplier)->result();
	// 	$lib_supplier = array();
	// 	foreach ($table_lib_supplier as $row) {
	// 		$lib_supplier[$row->ID] = $row->SUPPLIER_NAME;
	// 	}

	// 	if ($category_id>0) {
	// 		$where_con = " AND b.ITEM_CATEGORY=$category_id";
	// 	} else {
	// 		$where_con = "";
	// 	}

	// 	if ($order_no) {
	// 		$where_con .= " AND d.WO_NUMBER Like '%$order_no%'";
	// 		$where_con_booking = "a.BOOKING_NO Like '%$order_no%'";
			

	// 	}
    
		
	// 	if ($requ_id) {
	// 		$where_con .= " and a.ID in ($requ_id)";
	// 	}
	// 	$pay_mode = array(1 => "Credit", 2 => "Import", 3 => "In House", 4 => "Cash", 5 => "Within Group");
	
	// 	$query_INV_PURCHASE_REQUISITION_MST = "SELECT d.ID,d.WO_NUMBER,TO_CHAR(d.SUPPLIER_ID) as SUPPLIER_ID,d.WO_DATE,d.PAY_MODE FROM INV_PURCHASE_REQUISITION_MST a, INV_PURCHASE_REQUISITION_DTLS b, WO_NON_ORDER_INFO_DTLS c,WO_NON_ORDER_INFO_MST d 
	// 	WHERE a.ID = b.MST_ID AND b.ID = c.REQUISITION_DTLS_ID AND d.ID = c.MST_ID AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 
	// 	$where_con		
	// 	UNION ALL		
	// 	SELECT a.ID,a.BOOKING_NO as WO_NUMBER,TO_CHAR(a.SUPPLIER_ID) as SUPPLIER_ID,a.BOOKING_DATE as WO_DATE,a.PAY_MODE  FROM WO_BOOKING_MST a WHERE $where_con_booking		
	// 	Union ALL
	// 	SELECT a.ID,a.BOOKING_NO as WO_NUMBER,TO_CHAR(a.SUPPLIER_ID),a.BOOKING_DATE as WO_DATE,a.PAY_MODE FROM WO_NON_ORD_SAMP_BOOKING_MST a WHERE $where_con_booking";
	// 	//print_r($query_INV_PURCHASE_REQUISITION_MST);die;
		
		

	// 	$table_INV_PURCHASE_REQUISITION_MST = $this->db->query($query_INV_PURCHASE_REQUISITION_MST)->result();

	// 	foreach ($table_INV_PURCHASE_REQUISITION_MST as $row) {
	// 		$booking_array[$row->ID] = [
	// 			"ID" => $row->ID,
	// 			"WO_NUMBER" => $row->WO_NUMBER,				
	// 			"WO_DATE" => $row->WO_DATE,
	// 			"SUPPLIER_ID" => $row->SUPPLIER_ID,
	// 			"SUPPLIER_NAME" => $lib_supplier[$row->SUPPLIER_ID],
	// 			"PAY_MODE" => $pay_mode[$row->PAY_MODE],
	// 		];
	// 	}
	// 	$booking_array_2 = Array();
	// 	foreach($booking_array as $row){
	// 		$booking_array_2[]=$row;
	// 	}

	// 	return $booking_array_2;
	// }

	function purchase_order_details($category_id=0, $order_no="", $requ_id="",$company_id=0)
	{
		$query_lib_supplier = "SELECT ID,SUPPLIER_NAME FROM LIB_SUPPLIER ";
		$table_lib_supplier = $this->db->query($query_lib_supplier)->result();
		$lib_supplier = array();
		foreach ($table_lib_supplier as $row) {
			$lib_supplier[$row->ID] = $row->SUPPLIER_NAME;
		}

		if ($order_no) {
			$where_con = " AND d.WO_NUMBER Like '%$order_no%'";
			$where_con_booking = "a.BOOKING_NO Like '%$order_no%'";
			
		} else {
			$where_con = "";
			$where_con_booking = "ID  = 0";
		}

		if ($category_id>0) {
			$where_con.= " AND b.ITEM_CATEGORY = $category_id";
			$where_con_booking .= " AND  a.ITEM_CATEGORY = $category_id";
		}

		

		if ($requ_id) {
			$where_con .= " and a.ID in ($requ_id)";
		} else {
			$where_con .= "";
		}
		$pay_mode = array(1 => "Credit", 2 => "Import", 3 => "In House", 4 => "Cash", 5 => "Within Group");
	
		$query_INV_PURCHASE_REQUISITION_MST = "SELECT d.ID,d.WO_NUMBER,TO_CHAR(d.SUPPLIER_ID) as SUPPLIER_ID,d.WO_DATE,d.PAY_MODE FROM INV_PURCHASE_REQUISITION_MST a, INV_PURCHASE_REQUISITION_DTLS b, WO_NON_ORDER_INFO_DTLS c,WO_NON_ORDER_INFO_MST d 
		WHERE a.ID = b.MST_ID AND b.ID = c.REQUISITION_DTLS_ID AND d.ID = c.MST_ID AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 
		$where_con		
		UNION ALL		
		SELECT a.ID,a.BOOKING_NO as WO_NUMBER,TO_CHAR(a.SUPPLIER_ID) as SUPPLIER_ID,a.BOOKING_DATE as WO_DATE,a.PAY_MODE  FROM WO_BOOKING_MST a WHERE $where_con_booking		
		Union ALL
		SELECT a.ID,a.BOOKING_NO as WO_NUMBER,TO_CHAR(a.SUPPLIER_ID),a.BOOKING_DATE as WO_DATE,a.PAY_MODE FROM WO_NON_ORD_SAMP_BOOKING_MST a WHERE $where_con_booking";
		//print_r($query_INV_PURCHASE_REQUISITION_MST);die;
		
		

		$table_INV_PURCHASE_REQUISITION_MST = $this->db->query($query_INV_PURCHASE_REQUISITION_MST)->result();

		foreach ($table_INV_PURCHASE_REQUISITION_MST as $row) {
			$booking_array[$row->ID] = [
				"ID" => $row->ID,
				"WO_NUMBER" => $row->WO_NUMBER,				
				"WO_DATE" => $row->WO_DATE,
				"SUPPLIER_ID" => $row->SUPPLIER_ID,
				"SUPPLIER_NAME" => $lib_supplier[$row->SUPPLIER_ID],
				"PAY_MODE" => $pay_mode[$row->PAY_MODE],
			];
		}
		$booking_array_2 = Array();
		foreach($booking_array as $row){
			$booking_array_2[]=$row;
		}

		return $booking_array_2;
	}
}
