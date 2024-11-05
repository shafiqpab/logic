<?php

class purchase_requisition_details extends CI_Model
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

	function purchase_requisition_details($requ_no,$catg_id){
		//print_r('6');die;
		$item_category_type_arr=array();
		$query_item_category ="select CATEGORY_ID, SHORT_NAME from  lib_item_category_list where status_active=1 and is_deleted=0"; 
		$table_item_category = $this->db->query($query_item_category)->result();
		foreach($table_item_category as $row){
			$item_category_type_arr[$row->CATEGORY_ID]=$row->SHORT_NAME;
		}
		

		if($requ_no){
            $where_con = " AND a.REQU_NO Like '%$requ_no%' ";
        }else{
            $where_con = "";
        }

		if($catg_id){
            $where_con.= " AND b.ITEM_CATEGORY = $catg_id ";
        }else{
            $where_con.= "";
        }

		
		$query_INV_PURCHASE_REQUISITION_MST = "SELECT a.ID,a.REQU_NO,a.REQUISITION_DATE,b.ITEM_CATEGORY FROM INV_PURCHASE_REQUISITION_MST a,INV_PURCHASE_REQUISITION_DTLS b WHERE a.ID = b.MST_ID AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 $where_con";	
		//print_r($query_INV_PURCHASE_REQUISITION_MST);die;
		$table_INV_PURCHASE_REQUISITION_MST = $this->db->query($query_INV_PURCHASE_REQUISITION_MST)->result();


		// $return = Array();
		foreach($table_INV_PURCHASE_REQUISITION_MST as $row){
			
			$purchase_requisition[$row->ID] = [
				"ID" => $row->ID,
				"REQU_NO" => $row->REQU_NO,
				"REQUISITION_DATE" => $row->REQUISITION_DATE,
				"ITEM_CATEGORY_ID" => $row->ITEM_CATEGORY,
				"ITEM_CATEGORY_NAME" => $item_category_type_arr[$row->ITEM_CATEGORY],
			];
		}
		$purchase_requisition_2 = Array();
		foreach($purchase_requisition as $row){
			$purchase_requisition_2[] = $row;
		}
		return $purchase_requisition_2;
		
	}		
}