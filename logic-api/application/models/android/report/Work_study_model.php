<?php
class Work_study_model extends CI_Model {

	function __construct() {
		error_reporting(0);
		parent::__construct();
	}

    function getOpertionData($style_ref, $gmts_item_id, $bulletin_type_id=4){
		$gsd_sql = "SELECT a.id,a.STYLE_REF, b.LIB_SEWING_ID,c.OPERATION_NAME  FROM ppl_gsd_entry_mst a, PPL_GSD_ENTRY_DTLS b,lib_sewing_operation_entry c WHERE a.id = b.mst_id and b.LIB_SEWING_ID=c.id AND a.status_active = 1 AND a.is_deleted = 0 AND b.STATUS_ACTIVE = 1 and a.id in(select max(id) from ppl_gsd_entry_mst where STYLE_REF = '$style_ref' AND STATUS_ACTIVE = 1 and is_deleted = 0 and a.gmts_item_id=$gmts_item_id and BULLETIN_TYPE=$bulletin_type_id)";
	   
		$gsd_sql_res = sql_select($gsd_sql);

		$opertion_data_arr = array();
		foreach ($gsd_sql_res as $rows) {

			$opertion_data_arr[]=[
				"OPERATION_ID" => $rows->LIB_SEWING_ID,
				"OPERATION_NAME" => $rows->OPERATION_NAME 
			];
		 
		}
        
        return $opertion_data_arr;
    }
}