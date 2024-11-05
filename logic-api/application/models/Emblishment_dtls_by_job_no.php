<?php

class Emblishment_dtls_by_job_no extends CI_Model
{



	function __construct()
	{
		parent::__construct();
		error_reporting(0);
		ini_set('precision', 8);
	}
	/**
	 * [get_max_value description]
	 * @param  [string] $tableName [defining name of the table]
	 * @param  [string] $fieldName [defining name of the table column]
	 * @return [integer]            [return max value of the table column]
	 */
	function get_max_value($tableName, $fieldName)
	{
		return $this->db->select_max($fieldName)->get($tableName)->row()->{$fieldName};
	}

	/**
	 * [insertDataWithReturn description]
	 * @param  [string] $tableName [defining name of the table]
	 * @param  [array] $post [defining data to be inserted]
	 * @return [boolean]            [TRUE/FALSE]
	 */
	function insertData($post, $tableName)
	{
		$this->db->trans_start();
		$this->db->insert($tableName, $post);
		$this->db->trans_complete();
		if ($this->db->trans_status() == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * [updateData description]
	 * @param  [string] $tableName [defining name of the table]
	 * @param  [array] $data [defining data to be updated]
	 * @param  [type] $condition [defining the condition for update]
	 * @return [boolean]            [TRUE/FALSE]
	 */
	function updateData($tableName, $data, $condition)
	{
		$this->db->trans_start();
		$this->db->update($tableName, $data, $condition);
		$this->db->trans_complete();
		if ($this->db->trans_status() == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * [deleteRowByAttribute description]
	 * @param  [string] $tableName [defining name of the table]
	 * @param  [array] $data [value by which row will be deleted]
	 * @return [boolean]            [TRUE/FALSE]
	 */
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
	/**
	 * [get_field_value_by_attribute description]
	 * @param  [type] $tableName [description]
	 * @param  [type] $fieldName [description]
	 * @param  [type] $attribute [description]
	 * @return [type]            [description]
	 */
	function get_field_value_by_attribute($tableName, $fieldName, $attribute)
	{
		$attribute = str_replace("'", "", $attribute);
		if (($attribute * 1) > 0) {
			$query = $this->db->query('select ' . $tableName . '.' . $fieldName . ' from ' . $tableName . ' where id in(' . $attribute . ')');
			$result = $query->row();
			if (!empty($result)) :
				return $result->{$fieldName};
			else :
				return false;
			endif;
		}
	}

	function Emblishment_dtls_by_job_no($job_no)
	{
		//print_r(44);die;
		$msg = "";

		$emblishment_name_array = array(1 => "Printing", 2 => "Embroidery", 3 => "Wash", 4 => "Special Works", 5 => "Gmts Dyeing",6 => "Attachment", 99 => "Others");

		$query_lib_body_part = "SELECT ID,BODY_PART_FULL_NAME FROM LIB_BODY_PART WHERE STATUS_ACTIVE = 1 and IS_DELETED =0";
		$table_body_part = $this->db->query($query_lib_body_part)->result();
		$lib_body_part = array();
		foreach ($table_body_part as $row) {
			$lib_body_part[$row->ID] = $row->BODY_PART_FULL_NAME;
		}

		$query_emb_lib = "SELECT EMB_ID,EMB_TYPE,EMB_NAME FROM LIB_EMBELLISHMENT_NAME WHERE STATUS_ACTIVE = 1 and IS_DELETED =0";
		$table_emb_lib = $this->db->query($query_emb_lib)->result();
		$lib_emb = array();
		foreach ($table_emb_lib as $row) {
			$lib_emb[$row->EMB_TYPE][$row->EMB_ID] = $row->EMB_NAME;
		}
		//print_r($lib_emb[1][99]);die;
		$query_emb = "SELECT id, JOB_NO, EMB_NAME, EMB_TYPE, BODY_PART_ID, country, supplier_id, is_apply_last_update,cons_dzn_gmts, rate, amount, status_active, budget_on from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and is_deleted=0";
		$table_emb = $this->db->query($query_emb)->result();
		//print_r($table_emb);die;
		// $emb_table_arr = array();
		// foreach($table_emb as $row){
		// 	$emb_table_arr[$row->JOB_ID][] = $emblishment_name_array[$row->EMB_NAME];
		// }

		$data = array();
		foreach($table_emb as $row){
			$data[] = [
				"JOB_NO" => $row->JOB_NO,
				"NAME" => $emblishment_name_array[$row->EMB_NAME],
				"TYPE" => $lib_emb[$row->EMB_NAME][$row->EMB_TYPE],
				"BODY_PART" => $lib_body_part[$row->BODY_PART_ID]
			];
		}
		return $data;
	}
}
