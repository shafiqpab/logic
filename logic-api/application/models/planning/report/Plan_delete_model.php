<?php
class plan_delete_model extends CI_Model
{



	function plan_delete($plan_id,$user_id)
	{
		$current_date = date('d-M-Y');
		//$user_id = 12;
		//$plan_id = 712;
		$plan_table_row = $this->db->query("SELECT PLAN_ID FROM PPL_SEWING_PLAN_BOARD where PLAN_ID = $plan_id")->result();
		
		$this->db->trans_start(); 


		if($plan_table_row[0]->PLAN_ID == $plan_id){
			$this->db->query("UPDATE PPL_SEWING_PLAN_BOARD SET IS_DELETED = 1, STATUS_ACTIVE = 0, UPDATED_BY = $user_id, UPDATE_DATE ='$current_date' WHERE PLAN_ID = $plan_id");
		}else{
			return "Plan Not Found";
		}
		

		$this->db->trans_complete();

		if ($this->db->trans_status() == TRUE) {
			return "Transition_Completed";
		} else {
			return FALSE;
		}
		
	}
}
