<?php
class Plan_consistent_report_model extends CI_Model {

	// function __construct() {
	// 	parent::__construct();
	// 	error_reporting(0);
	// }

	function get_max_value($tableName, $fieldName) {
		return $this->db->select_max($fieldName)->get($tableName)->row()->{$fieldName};
	}


	function get_plan_consistent_report($company_id,$job_no,$gmts_item_id,$date_type,$start_date,$end_date,$plan_status,$buyer_id,$po)
	{

		//lib array......................................
		$company_arr = array();
		$com_res = $this->db->query("select ID,COMPANY_NAME from lib_company where status_active =1 and is_deleted=0  order by company_name")->result();
		foreach ($com_res as $value) {
			$company_arr[$value->ID] = $value->COMPANY_NAME;
		}

		$buyer_arr = array();
		$buyer_res = $this->db->query("select ID,BUYER_NAME from lib_buyer where status_active =1 and is_deleted=0  order by BUYER_NAME")->result();
		foreach ($buyer_res as $value) {
			$buyer_arr[$value->ID] = $value->BUYER_NAME;
		}
 
		$color_arr = array();
		$color_res = $this->db->query("SELECT ID,COLOR_NAME from lib_color where status_active=1")->result();
		foreach ($color_res as $value) {
			$color_arr[$value->ID] = $value->COLOR_NAME;
		}
		
		$garment_res = $this->db->query("select ID,ITEM_NAME from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name")->result();
		$garments_item_arr=array();
		foreach ($garment_res as $value) {
			$garments_item_arr[$value->ID] = $value->ITEM_NAME;
		}

		if($company_id){
			$where_con = "and COMPANY_NAME=$company_id";
		}else{
			$where_con = "";
		}
		$sewing_line_res = $this->db->query("select ID,LINE_NAME from  lib_sewing_line where status_active=1 and is_deleted=0 $where_con order by LINE_NAME")->result();
		$sewing_line_arr=array();
		foreach ($sewing_line_res as $value) {
			$sewing_line_arr[$value->ID] = $value->LINE_NAME;
		}
		//print_r($sewing_line_arr);die;
		//where con......................................
		
		if($company_id)
		{
			$whereCon=" and a.COMPANY_NAME=$company_id";
		}
		if($job_no)
		{
			$whereCon.=" and a.JOB_NO like('%$job_no')";
		}
		if($po)
		{
			$whereCon.="  and b.PO_NUMBER like('$po%')";
		}
		if($gmts_item_id)
		{
			$whereCon.=" and c.GMTS_ITEM_ID='$gmts_item_id'";
		}
		if($buyer_id)
		{
			$whereCon.=" and a.BUYER_NAME='$buyer_id'";
		}
		
		if($date_type==1 && $start_date != '' && $end_date != '')
		{
			$whereCon.=" and b.ID in(select PO_NUMBER_ID from TNA_PROCESS_MST where TASK_TYPE=1 and TASK_NUMBER in (84,86,190,191)  and TASK_START_DATE >= '$start_date' and  TASK_FINISH_DATE <='$end_date')";
		}
		else if($date_type==2 && $start_date != '' && $end_date != '')
		{
			$whereCon.=" and b.ID in(select b.PO_BREAK_DOWN_ID from PPL_SEWING_PLAN_BOARD a,PPL_SEWING_PLAN_BOARD_POWISE b,PPL_SEWING_PLAN_BOARD_DTLS c where a.plan_id=b.plan_id  and  b.plan_id=c.plan_id  and c.PLAN_DATE between '$start_date' and '$end_date' ";
			if($company_id)
			{ 
				$whereCon.=" and a.COMPANY_ID=$company_id)";
			}else{
				$whereCon.=" ) ";
			}
			//print_r($whereCon); die;
		}
		else if($date_type==3 && $start_date != '' && $end_date != '')
		{
			$whereCon.=" and b.SHIPMENT_DATE between '$start_date' and '$end_date'";
		}

		 //return $whereCon;

		//main query......................................
		$sql = "SELECT a.JOB_NO_PREFIX_NUM, a.JOB_NO,a.COMPANY_NAME as COMPANY_NAME,a.BUYER_NAME, b.ID as PO_BREAK_DOWN_ID,b.PO_NUMBER,b.SHIPMENT_DATE AS SHIPMENT_DATE,b.PUB_SHIPMENT_DATE, b.PLAN_CUT,c.GMTS_ITEM_ID,c.SET_ITEM_RATIO,c.SMV_PCS,d.ORDER_QUANTITY,d.PLAN_CUT_QNTY,d.COLOR_NUMBER_ID from wo_po_details_master a, wo_po_break_down b,wo_po_details_mas_set_details c,WO_PO_COLOR_SIZE_BREAKDOWN d where a.id=b.job_id and a.id=c.job_id and b.job_id=c.job_id and a.id=d.job_id and b.job_id=d.job_id and c.job_id=d.job_id and b.id=d.PO_BREAK_DOWN_ID and c.GMTS_ITEM_ID=d.ITEM_NUMBER_ID and a.status_active=1 and b.status_active=1  and d.status_active=1 and d.IS_DELETED=0 $whereCon";// and a.JOB_NO = 'RpC-23-00047'
		//print_r($sql); die;

		$sql_data = sql_select($sql);
		$dataArr=array();
		$poArr=array(0);
		//print_r($poArr); die;
		foreach ($sql_data as $row) {
			$key=$row->JOB_NO.'**'.$row->PO_BREAK_DOWN_ID.'**'.$row->GMTS_ITEM_ID;

			$dataArr[$key]['JOB_NO_PREFIX_NUM']=$row->JOB_NO_PREFIX_NUM;
			$dataArr[$key]['JOB_NO']=$row->JOB_NO;
			$dataArr[$key]['PO_BREAK_DOWN_ID']=$row->PO_BREAK_DOWN_ID;
			$dataArr[$key]['PO_NUMBER']=$row->PO_NUMBER;
			$dataArr[$key]['COMPANY_ID']=$row->COMPANY_NAME;
			$dataArr[$key]['BUYER_ID']=$row->BUYER_NAME;
			$dataArr[$key]['GMTS_ITEM_ID']=$row->GMTS_ITEM_ID;
			$dataArr[$key]['SMV_PCS']=$row->SMV_PCS;
			$dataArr[$key]['SHIPMENT_DATE']=$row->PUB_SHIPMENT_DATE;
		
			$dataArr[$key]['ORDER_QUANTITY']+=$row->ORDER_QUANTITY;
			$dataArr[$key]['PLAN_CUT_QNTY']+=$row->PLAN_CUT_QNTY;
			$dataArr[$key]['COLOR_NUMBER_ID'][$row->COLOR_NUMBER_ID]=$color_arr[$row->COLOR_NUMBER_ID];
			$poArr[$row->PO_BREAK_DOWN_ID]=$row->PO_BREAK_DOWN_ID;
		}

		//print_r($dataArr);die;

		//tna query................................
		// $tna_sql="select PO_NUMBER_ID,min(TASK_START_DATE) as TASK_START_DATE,max(TASK_FINISH_DATE) as TASK_FINISH_DATE from TNA_PROCESS_MST where TASK_TYPE=1 and TASK_NUMBER in (86) ".where_con_using_array($poArr,0,'PO_NUMBER_ID')." group by PO_NUMBER_ID";

		$tna_sql="select PO_NUMBER_ID,min(TASK_START_DATE) as TASK_START_DATE,max(TASK_FINISH_DATE) as TASK_FINISH_DATE from TNA_PROCESS_MST where TASK_TYPE=1 and TASK_NUMBER in (86,190,191) ".where_con_using_array($poArr,0,'PO_NUMBER_ID')." group by PO_NUMBER_ID";
		//print_r($tna_sql); die;
		$tna_sql_res = sql_select($tna_sql);
		$tna_data_arr=array();
		foreach($tna_sql_res as $row){
			$tna_data_arr[$row->PO_NUMBER_ID]['TASK_START_DATE']=$row->TASK_START_DATE;
			$tna_data_arr[$row->PO_NUMBER_ID]['TASK_FINISH_DATE']=$row->TASK_FINISH_DATE;
		}
		//print_r($tna_data_arr);die;

		//plan query................................

		if($company_id){
			$where_con = "and a.COMPANY_ID=$company_id";
		}else{
			$where_con = "";
		}
		
		$plan_sql="select a.LINE_ID,b.PO_BREAK_DOWN_ID,a.START_DATE as PLAN_START_DATE,a.END_DATE as PLAN_END_DATE,a.PLAN_QNTY AS PLAN_QNTY,sum(c.WORKING_HOUR) as WORKING_HOUR from PPL_SEWING_PLAN_BOARD a,PPL_SEWING_PLAN_BOARD_POWISE b,PPL_SEWING_PLAN_BOARD_DTLS c where a.plan_id=b.plan_id and  b.plan_id=c.plan_id $where_con".where_con_using_array($poArr,0,'b.PO_BREAK_DOWN_ID')." group by a.LINE_ID,b.PO_BREAK_DOWN_ID,a.START_DATE ,a.END_DATE ,a.PLAN_QNTY";
		//print_r($plan_sql); die;
		$plan_sql_res = sql_select($plan_sql);
		$plan_data_arr=array();
		foreach($plan_sql_res as $row){
			$plan_data_arr[$row->PO_BREAK_DOWN_ID]['PLAN_START_DATE']=$row->PLAN_START_DATE;
			$plan_data_arr[$row->PO_BREAK_DOWN_ID]['PLAN_END_DATE'] = 
				(strtotime($row->PLAN_END_DATE) > strtotime($plan_data_arr[$row->PO_BREAK_DOWN_ID]['PLAN_END_DATE']))  ? 
					$row->PLAN_END_DATE
						: 
					$plan_data_arr[$row->PO_BREAK_DOWN_ID]['PLAN_END_DATE'];

			$plan_data_arr[$row->PO_BREAK_DOWN_ID]['PLAN_QNTY']+=$row->PLAN_QNTY;
			$plan_data_arr[$row->PO_BREAK_DOWN_ID]['WORKING_HOUR']+=$row->WORKING_HOUR;
			$plan_data_arr[$row->PO_BREAK_DOWN_ID]['LINE_ID'][$row->LINE_ID]=$sewing_line_arr[$row->LINE_ID];
		}
		//print_r($sewing_line_arr); die;
 

		//production query................................
		
		$production_sql="select PO_BREAK_DOWN_ID,ITEM_NUMBER_ID,PRODUCTION_QUANTITY from pro_garments_production_mst where PRODUCTION_TYPE=5 and STATUS_ACTIVE=1 and IS_DELETED=0  ".where_con_using_array($poArr,0,'PO_BREAK_DOWN_ID')."";
		 //print_r($production_sql); die;
		$production_sql_res = sql_select($production_sql);
		$production_data_arr=array();
		foreach($production_sql_res as $row){
			$production_data_arr[$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID]['PRODUCTION_QUANTITY']=$row->PRODUCTION_QUANTITY;
		}
 
		//print_r($dataArr);die;

		$data_array=array();
		$i=0;
		//$a = 0; $j = 0; $k = 0;
		//print_r($plan_data_arr); die;
		foreach($dataArr as $row){
			//echo gettype( $plan_data_arr[$row['PO_BREAK_DOWN_ID']]['PLAN_QNTY']*1);die;
			if($plan_status==3 && $plan_data_arr[$row['PO_BREAK_DOWN_ID']]['PLAN_QNTY'] < $row['PLAN_CUT_QNTY'])
			{//$a++;
				continue; //this will result a unplan and partial plan without full planned list
			}
			else if($plan_status==1 && ($plan_data_arr[$row['PO_BREAK_DOWN_ID']]['PLAN_QNTY']*1) > 0)
			{//$j++;
				continue;  //this will result a unplanned list
			}
			else if($plan_status==2 && ($plan_data_arr[$row['PO_BREAK_DOWN_ID']]['PLAN_QNTY'] >= $row['PLAN_CUT_QNTY']) || $plan_status==2 && ($plan_data_arr[$row['PO_BREAK_DOWN_ID']]['PLAN_QNTY']<=0) )
			{//$k++;	
				//print_r($row);die;			 
				continue;  //this will result a partial plan list				
			}
			
			
			$data_array[$i]['JOB_NO']=$row['JOB_NO'];
			$data_array[$i]['JOB_NO_PREFIX_NUM']=$row['JOB_NO_PREFIX_NUM'];
			$data_array[$i]['PO_NUMBER']=$row['PO_NUMBER'];
			$data_array[$i]['COMPANY_NAME']=$company_arr[$row['COMPANY_ID']];
			$data_array[$i]['BUYER_ID']=$row['BUYER_ID'];
			$data_array[$i]['BUYER_NAME']=$buyer_arr[$row['BUYER_ID']];
			$data_array[$i]['ORDER_QTY_PCS']=$row['ORDER_QUANTITY'];
			$data_array[$i]['PLAN_CUT_QTY_PCS']=$row['PLAN_CUT_QNTY'];
			$data_array[$i]['GMTS_ITEM']=$garments_item_arr[$row['GMTS_ITEM_ID']];
			$data_array[$i]['SMV_PCS']=$row['SMV_PCS'];
			$data_array[$i]['SHIPMENT_DATE']=$row['SHIPMENT_DATE'];
			$data_array[$i]['GMTS_COLOR']=implode(', ',$row['COLOR_NUMBER_ID']);
			$data_array[$i]['TNA_START_DATE']=($tna_data_arr[$row['PO_BREAK_DOWN_ID']]['TASK_START_DATE']) ?? "01-01-1970";
			$data_array[$i]['TNA_END_DATE']=($tna_data_arr[$row['PO_BREAK_DOWN_ID']]['TASK_FINISH_DATE']) ?? "01-01-1970";

			$data_array[$i]['PLAN_START_DATE']=($plan_data_arr[$row['PO_BREAK_DOWN_ID']]['PLAN_START_DATE']) ?? "01-01-1970";
			$data_array[$i]['PLAN_END_DATE']=($plan_data_arr[$row['PO_BREAK_DOWN_ID']]['PLAN_END_DATE']) ?? "01-01-1970";

			//$data_array[$i]['PO_BREAK_DOWN_ID']=$plan_data_arr[$row['PO_BREAK_DOWN_ID']]['PLAN_END_DATE'];


			$data_array[$i]['PLAN_QTY']=($plan_data_arr[$row['PO_BREAK_DOWN_ID']]['PLAN_QNTY']) ?? 0;
			$data_array[$i]['PLAN_HOUR']=($plan_data_arr[$row['PO_BREAK_DOWN_ID']]['WORKING_HOUR']) ?? 0;

			$data_array[$i]['START_VARIENCE']=date_diff_days($data_array[$i]['TNA_START_DATE'],$data_array[$i]['PLAN_START_DATE']);
			$data_array[$i]['END_VARIENCE']=date_diff_days($data_array[$i]['TNA_END_DATE'],$data_array[$i]['PLAN_END_DATE']);

			$tna_start_date = date("Y-m-d", strtotime($data_array[$i]['TNA_START_DATE']));
			$plan_start_date = date("Y-m-d", strtotime($data_array[$i]['PLAN_START_DATE']));

			$tna_end_date = date("Y-m-d", strtotime($data_array[$i]['TNA_END_DATE']));
			$plan_end_date = date("Y-m-d", strtotime($data_array[$i]['PLAN_END_DATE']));

			//print_r($plan_data_arr);die;
			$data_array[$i]['LINE_NAME']=(count($plan_data_arr[$row['PO_BREAK_DOWN_ID']]['LINE_ID'])>0) ? implode(',',$plan_data_arr[$row['PO_BREAK_DOWN_ID']]['LINE_ID']) :"";

			$start_varience_status=(strtotime($data_array[$i]['TNA_START_DATE'])<=strtotime($data_array[$i]['PLAN_START_DATE']) && strtotime($data_array[$i]['PLAN_START_DATE']) < strtotime($data_array[$i]['TNA_END_DATE'])) ? 1 : 0;
			$end_varience_status=(strtotime($data_array[$i]['TNA_END_DATE'])>=strtotime($data_array[$i]['PLAN_END_DATE']) && strtotime($data_array[$i]['PLAN_END_DATE']) > strtotime($data_array[$i]['TNA_START_DATE'])) ? 1 : 0;
			//print_r($data_array[$i]['TNA_END_DATE']);
			//print_r($data_array[$i]['PLAN_END_DATE']);die;
			$data_array[$i]['START_CONSISTENCY']=($start_varience_status==1) ? "Consistent":"Inconsistent";
			$data_array[$i]['END_CONSISTENCY']=($end_varience_status ==1) ? "Consistent":"Inconsistent";


			$data_array[$i]['PRODUCTION_QTY_PCS']=($production_data_arr[$row['PO_BREAK_DOWN_ID']][$row['GMTS_ITEM_ID']]['PRODUCTION_QUANTITY']) ?? 0;


			if($data_array[$i]['PLAN_CUT_QTY_PCS']>$data_array[$i]['PLAN_QTY'] && $data_array[$i]['PLAN_QTY']>0){$data_array[$i]['PLAN_STATUS']="Partially Planned";}
			else if($data_array[$i]['PLAN_QTY']<=0){$data_array[$i]['PLAN_STATUS']="Unplanned";}
			else{$data_array[$i]['PLAN_STATUS']="Full Planned";}

			if($data_array[$i]['TNA_START_DATE']=="01-01-1970"){
				$data_array[$i]['START_VARIENCE'] = 0;
			}elseif($data_array[$i]['START_CONSISTENCY']=="Inconsistent"){
				$data_array[$i]['START_VARIENCE']= -(date_diff_days($data_array[$i]['TNA_START_DATE'],$data_array[$i]['PLAN_START_DATE']));
			}else{
				$data_array[$i]['START_VARIENCE']= date_diff_days($data_array[$i]['TNA_START_DATE'],$data_array[$i]['PLAN_START_DATE']);
			}

			if($data_array[$i]['TNA_END_DATE']=="01-01-1970"){
				$data_array[$i]['END_VARIENCE'] = 0;
			}elseif($data_array[$i]['END_CONSISTENCY']=="Inconsistent"){
				$data_array[$i]['END_VARIENCE']= -(date_diff_days($data_array[$i]['TNA_END_DATE'],$data_array[$i]['PLAN_END_DATE']));
			}else{
				$data_array[$i]['END_VARIENCE']= date_diff_days($data_array[$i]['TNA_END_DATE'],$data_array[$i]['PLAN_END_DATE']);
			}

		$i++; 
		}
		//print_r($i."*".$a."*".$j."*".$k); die;
		//print_r($data_array); die;
		return $data_array;

	}







	/**
	 * [get_max_value description]
	 * @param  [string] $tableName [defining name of the table]
	 * @param  [string] $fieldName [defining name of the table column]
	 * @return [integer]            [return max value of the table column]
	 */


	/**
	 * [insertDataWithReturn description]
	 * @param  [string] $tableName [defining name of the table]
	 * @param  [array] $post [defining data to be inserted]
	 * @return [boolean]            [TRUE/FALSE]
	 */
	function insertData($post, $tableName) {
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
	function updateData($tableName, $data, $condition) {
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
	function deleteRowByAttribute($tableName, $attribute) {
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
	function get_field_value_by_attribute($tableName, $fieldName, $attribute) {
		$attribute = str_replace("'", "", $attribute);
		if (($attribute * 1) > 0) {
			$query = $this->db->query('select ' . $tableName . '.' . $fieldName . ' from ' . $tableName . ' where id in(' . $attribute . ')');
			$result = $query->row();
			if (!empty($result)):
				return $result->{$fieldName};
			else:
				return false;
			endif;
		}

		/*$result = $this->db->get_where($tableName, $attribute)->row();
			        if (!empty($result)):
			            return $result->{$fieldName};
			        else:
			            return false;
		*/
	}

	public function login($user_id, $password) {

		$query = $this->db->query('select user_passwd.id AS "ID",user_passwd.password "PASSWORD" from user_passwd where user_name=' . $this->db->escape($user_id));

		if ($query->num_rows() == 1) {
			$user_info = $query->row();
			//return false;
			if ($user_info->PASSWORD == $this->encrypt($password)) {
				return $this->get_menu_by_privilege($user_info->ID);
			} else {
				return false;
			}
		}
	}

	public function logout($user_id) {
		return $query = $this->db->query('update planning_board_status set board_status=0 where user_id=' . $this->db->escape($user_id));
	}

	public function encrypt($string) {
		// Retrun String after Ecryption
		// Here $string= Given Text to be encrypted,
		$key = "logic_erp_2011_2012_platform";
		$result = '';
		for ($i = 0; $i < strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			$result .= $char;
		}
		return base64_encode($result);
	}

	public function get_menu_by_privilege($user_id) {
		$user_credentials = "SELECT UNIT_ID, COMPANY_LOCATION_ID,IS_PLANNER FROM user_passwd Where ID='$user_id' ";
		$all_comp = 0;
		$all_loc = 0;
		foreach (sql_select($user_credentials) as $v) {
			$all_comp = $v->UNIT_ID;
			$all_loc = $v->COMPANY_LOCATION_ID;
			$is_planner = $v->IS_PLANNER;
		}
		if (!$all_comp) {
			$all_comp = 0;
		}

		if (!$all_loc) {
			$all_loc = 0;
		}

		$plan_lavel_data_arr = $this->db->query("SELECT BULLETIN_TYPE  from variable_settings_production where variable_list=12")->result();
		$data_arr['plan_level'] = $plan_lavel_data_arr[0]->BULLETIN_TYPE;

		if ($all_comp) {
			$comp_sql = "SELECT ID,COMPANY_NAME from lib_company   where status_active =1 and is_deleted=0  and id in($all_comp) order by company_name";
		} else {
			$comp_sql = "SELECT ID,COMPANY_NAME from lib_company   where status_active =1 and is_deleted=0  order by company_name";
		}

		$erp_com = $this->db->query("SELECT count(ID) as ERP_COM from lib_company   where status_active =1 and is_deleted=0 ")->result();
		$erp_com_cnt = $erp_com[0]->ERP_COM;

		$erp_loc = $this->db->query("SELECT count(ID) as ERP_LOC from lib_location  where status_active =1 and is_deleted=0 ")->result();
		$erp_loc_cnt = $erp_loc[0]->ERP_LOC;

		$erp_floor = $this->db->query("SELECT count(ID) as ERP_FLOOR from lib_prod_floor  where status_active =1 and is_deleted=0 ")->result();
		$erp_floor_cnt = $erp_floor[0]->ERP_FLOOR;

		$erp_line = $this->db->query("SELECT count(ID) as ERP_LINE from lib_sewing_line  where status_active =1 and is_deleted=0 ")->result();
		$erp_line_cnt = $erp_line[0]->ERP_LINE;

		$count_sql = "SELECT  COMPANY_COUNT, LOCATION_COUNT, FLOOR_COUNT, LINE_COUNT FROM company_loc_flr_line_count where status_active=1 and id in(select max(id ) from  company_loc_flr_line_count where status_active=1) ";
		$buyer_sql = "SELECT a.ID,a.BUYER_NAME,SHORT_NAME,b.TAG_COMPANY as COMPANY_ID from lib_buyer a,lib_buyer_tag_company b ,lib_buyer_party_type c where a.id=b.buyer_id and a.id=c.buyer_id and c.party_type in(1,3)  and a.status_active=1 and a.is_deleted=0 group by a.ID,a.BUYER_NAME,SHORT_NAME,b.TAG_COMPANY order by a.BUYER_NAME";

		if ($all_loc) {
			$loc_sql = "SELECT b.ID,b.LOCATION_NAME,b.COMPANY_ID from lib_location  b, lib_company a  where a.id=b.company_id and b.status_active=1 and b.is_deleted=0 and b.id in($all_loc) and  a.status_active =1 and a.is_deleted=0 order by b.location_name";
		} else {
			$loc_sql = "SELECT b.ID,b.LOCATION_NAME,b.COMPANY_ID from lib_location  b, lib_company a  where a.id=b.company_id and b.status_active=1 and b.is_deleted=0  and  a.status_active =1 and a.is_deleted=0 order by b.location_name";
		}

		if ($all_comp && $all_loc) {
			$floor_sql = "SELECT ID,FLOOR_NAME,LOCATION_ID,COMPANY_ID from  lib_prod_floor where company_id in($all_comp) and LOCATION_ID in($all_loc) and production_process=5 and status_active =1 and is_deleted=0 order by floor_serial_no";
			$line_sql = "SELECT ID,LINE_NAME from lib_sewing_line where company_name in ($all_comp) and location_name in($all_loc) and status_active= 1 and is_deleted = 0 order by sewing_line_serial ";
		} else if ($all_comp) {
			$floor_sql = "SELECT ID,FLOOR_NAME,LOCATION_ID,COMPANY_ID from  lib_prod_floor where company_id in($all_comp)  and production_process=5 and status_active =1 and is_deleted=0 order by floor_serial_no";
			$line_sql = "SELECT ID,LINE_NAME from lib_sewing_line where company_name in ($all_comp)  and status_active= 1 and is_deleted = 0 order by sewing_line_serial ";
		} else if ($all_loc) {
			$floor_sql = "SELECT ID,FLOOR_NAME,LOCATION_ID,COMPANY_ID from  lib_prod_floor where  production_process=5 and status_active =1  and LOCATION_ID in($all_loc) and is_deleted=0 order by floor_serial_no";
			$line_sql = "SELECT ID,LINE_NAME from lib_sewing_line where location_name in($all_loc) and status_active= 1 and is_deleted = 0 order by sewing_line_serial ";
		} else {
			$floor_sql = "SELECT ID,FLOOR_NAME,LOCATION_ID,COMPANY_ID from  lib_prod_floor where  production_process=5 and status_active =1  and is_deleted=0 order by floor_serial_no";
			$line_sql = "SELECT ID,LINE_NAME from lib_sewing_line where status_active= 1 and is_deleted = 0 order by sewing_line_serial ";

		}

		$complexity_level_sql = "SELECT  ID, LEVEL_TYPE, FIRST_DAY, INCREMENT_TYPE, TARGET,  STATUS FROM lib_complexity_level where status_active=1 order by id asc ";
		$com_res = $this->db->query($comp_sql)->result();
		$count_res = $this->db->query($count_sql)->result();
		$buyer_res = $this->db->query($buyer_sql)->result();
		$loc_res = $this->db->query($loc_sql)->result();
		$floor_res = $this->db->query($floor_sql)->result();
		$line_res = $this->db->query($line_sql)->result();
		$complexity_level_res = $this->db->query($complexity_level_sql)->result();

		$data_arr['floor_info'][0]["ID"] = 0;
		$data_arr['floor_info'][0]["FLOOR_NAME"] = "All Floor";
		$data_arr['floor_info'][0]["LOCATION_ID"] = 0;
		$data_arr['floor_info'][0]["COMPANY_ID"] = 0;

		$data_arr['company_info'] = $com_res;

		$data_arr['is_planner'] = $is_planner;

		$erp_count_arr['COMPANY_COUNT'] = $erp_com_cnt;
		$erp_count_arr['LOCATION_COUNT'] = $erp_loc_cnt;
		$erp_count_arr['FLOOR_COUNT'] = $erp_floor_cnt;
		$erp_count_arr['LINE_COUNT'] = $erp_line_cnt;
		$manual_count_arr = array();
		foreach ($count_res as $vals) {
			$manual_count_arr['COMPANY_COUNT'] = $vals->COMPANY_COUNT;
			$manual_count_arr['LOCATION_COUNT'] = $vals->LOCATION_COUNT;
			$manual_count_arr['FLOOR_COUNT'] = $vals->FLOOR_COUNT;
			$manual_count_arr['LINE_COUNT'] = $vals->LINE_COUNT;
		}
		$data_arr['manual_count'] = $manual_count_arr;
		$data_arr['erp_count'] = $erp_count_arr;

		$data_arr['buyer_info'] = $buyer_res;
		$data_arr['location_info'] = $loc_res;
		$data_arr['floor_info'] = $floor_res;
		//$data_arr['complexity_level'] = $complexity_level_res;
		$data_arr['user_id'] = $user_id;
		$complexity_level_data[0]['fdout'] = 0;
		$complexity_level_data[0]['increment'] = 0;
		$complexity_level_data[0]['target'] = 0;
		$ind = 1;
		foreach ($complexity_level_res as $v) {
			$complexity_level_data[$ind]['fdout'] = $v->FIRST_DAY;
			$complexity_level_data[$ind]['increment'] = $v->INCREMENT_TYPE;
			$complexity_level_data[$ind]['target'] = $v->TARGET;
			$ind++;
		}

		//$line_capacity_arr = $this->db->query("SELECT ID,LINE_ID, EXTEND_HOUR, EXTEND_DATE,STATE from LINE_CAPACITY" )->result();
		//$data_arr['LINE_CAPACITY']=$line_capacity_arr;

		$integrated_arr = $this->db->query("SELECT COMPANY_NAME,WORK_STUDY_INTEGRATED as INTEGRATED FROM VARIABLE_SETTINGS_PRODUCTION  WHERE VARIABLE_LIST = 9   AND  STATUS_ACTIVE = 1  AND  STATUS_ACTIVE = 1")->result();
		$data_arr['WORK_STUDY_INTEGRATED'] = $integrated_arr;

		$complexity_level = array(0 => "", 1 => "Basic", 2 => "Fancy", 3 => "Critical", 4 => "Average");
		/*$complexity_level_data[0]['fdout'] = 0;
			        $complexity_level_data[0]['increment'] = 0;
			        $complexity_level_data[0]['target'] = 0;
			        $complexity_level_data[1]['fdout'] = 1000;
			        $complexity_level_data[1]['increment'] = 100;
			        $complexity_level_data[1]['target'] = 1200;
			        $complexity_level_data[2]['fdout'] = 800;
			        $complexity_level_data[2]['increment'] = 100;
			        $complexity_level_data[2]['target'] = 1200;
			        $complexity_level_data[3]['fdout'] = 600;
			        $complexity_level_data[3]['increment'] = 100;
			        $complexity_level_data[3]['target'] = 1200;
			        $complexity_level_data[4]['fdout'] = 880;
			        $complexity_level_data[4]['increment'] = 100;
		*/
		$data_arr['complexity']['type_tmp'][1] = "Learning effect by fixed Quantity";
		$data_arr['complexity']['type_tmp'][2] = "Learning effect by Efficiency Percentage";
		$data_arr['complexity']['level'][0] = 0;
		$ind = 1;
		foreach ($complexity_level_res as $key => $val) {
			$data_arr['complexity']['level'][$ind] = $val->LEVEL_TYPE;
			$ind++;
		}

		foreach ($complexity_level_data as $m_key => $value) {
			foreach ($value as $key => $val) {
				$data_arr['complexity']['level_data'][$m_key][$key] = $val;
			}
		}
		return $data_arr;
	}

	function get_color_meaning() {
		$data_array = array();
		/*$arr=array("#FF9900"=>"No Complexity","#FF00FF"=>"Basic Compl.","#00FF00"=>"Fancy Compl.","#0000FF"=>"Critical Compl","#FF0000"=>"Average Compl.","#F2B7E2"=>"Selected Plan","#73CAD5"=>"Fresh Plan","#FFFFA8"=>"Offday Board","#9C8AE3"=>"Crossed plan","#C6C600"=>"Production","#FF0000"=>"TNA Crossed","#909553"=>"No Production");*/
		$arr = array("#73CAD5" => "Fresh Plan", "#FFFFA8" => "Offday Board", "#9C8AE3" => "Crossed plan", "#269b03" => "Production Found", "#FF0000" => "Ship Date Crossed", "#FF6600" => "TNA Crossed", "#909553" => "No Production");
		$kk = 0;
		foreach ($arr as $key => $val) {
			$data_array[$kk]["code"] = $key;
			$data_array[$kk]["meaning"] = $val;
			$kk++;
		}
		return $data_array;
	}

	function planner_data() {
		$data_array = array();
		$arr = sql_select("SELECT USER_NAME,  USER_FULL_NAME,  CREATED_ON FROM user_passwd Where is_planner = 1 ");
		$kk = 0;
		foreach ($arr as $key => $val) {
			$data_array[$kk]["user_name"] = $val->USER_NAME;
			$data_array[$kk]["real_name"] = $val->USER_FULL_NAME;
			$data_array[$kk]["created_date"] = date("d-m-Y", strtotime($val->CREATED_ON));
			$kk++;
		}
		return $data_array;
	}

	function po_details_info($company_id, $po_id) {
		
		$this->load->model('Pre_cost');
		$precostData=$this->Pre_cost->get_knit_gray_finish_fabric_qty_by_order($po_id);
		
		/*function add_date($orgDate,$days,$type)
			        {
			            $cd = strtotime($orgDate);
			            if($type == 1){
			                $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$days,date('Y',$cd)));
			            }else{
			                $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)-$days,date('Y',$cd)));
			            }
			            return $retDAY;
		*/

		$sql_po = "SELECT  (case when a.garments_nature=2 then 'knit' else 'woven' end ) as GARMENTS_NATURE,  A.JOB_NO,A.COMPANY_NAME,A.LOCATION_NAME,A.BUYER_NAME,A.STYLE_REF_NO,A.JOB_QUANTITY, B.PO_NUMBER,B.PO_QUANTITY,B.SHIPMENT_DATE,a.LOCATION_NAME,
       B.PLAN_CUT,B.PO_RECEIVED_DATE,PUB_SHIPMENT_DATE
       from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst
       and b.id in($po_id)";
		$sql_data = array();
		$podtls = array();
		$sql_data = sql_select($sql_po);
		$job_arr = $sql_data;
		foreach ($sql_data as $row) {

			$podtls['location_name'] = $row->LOCATION_NAME;
			$podtls['job_no'] = $row->JOB_NO;
			$podtls['style_ref_no'] = $row->STYLE_REF_NO;
			$podtls['po_number'] = $row->PO_NUMBER;
			$podtls['po_received_date'] = date('d-m-Y', strtotime($row->PO_RECEIVED_DATE));
			$podtls['shipment_date'] = date('d-m-Y', strtotime($row->SHIPMENT_DATE));
			$podtls['po_quantity'] = $row->PO_QUANTITY;
			$podtls['plan_cut'] = $row->PLAN_CUT;
			$podtls['garments_nature'] = $row->GARMENTS_NATURE;
			$podtls['pub_shipment_date'] = date('d-m-Y', strtotime($row->PUB_SHIPMENT_DATE));
		}

		$sql_data = array();
		$tnadata = array();
		$sql = "SELECT TASK_NUMBER,TASK_START_DATE,TASK_FINISH_DATE,ACTUAL_START_DATE,ACTUAL_FINISH_DATE from tna_process_mst where po_number_id in($po_id) and task_number in (12,60,64,70,84,86,190,191) order by task_number";
		$sql_data = sql_select($sql);
		foreach ($sql_data as $row) {
			$tnadata[$row->TASK_NUMBER]['start'] = date('d-m-Y', strtotime($row->TASK_START_DATE));
			$tnadata[$row->TASK_NUMBER]['end'] = date('d-m-Y', strtotime($row->TASK_FINISH_DATE));
		}
		/*$sql = "SELECT PO_BREAK_DOWN_ID,sum(plan_cut_qnty) as PLAN_CUT_QNTY,sum(kint_fin_fab_qnty) as KINT_FIN_FAB_QNTY,sum(kint_grey_fab_qnty) as KINT_GREY_FAB_QNTY,sum(woven_fin_fab_qnty) as WOVEN_FIN_FAB_QNTY,sum(woven_grey_fab_qnty) as WOVEN_GREY_FAB_QNTY,sum(yarn_qnty) as YARN_QNTY,sum(conv_qnty) as CONV_QNTY,sum(trim_qty) as TRIM_QTY,sum(emb_cons) as EMB_CONS, sum(wash_cons) as WASH_CONS,sum(kint_grey_fab_qnty_prod) as KINT_GREY_FAB_QNTY_PROD,sum(kint_fin_fab_qnty_prod) as KINT_FIN_FAB_QNTY_PROD,sum(wash_cons) as WASH_CONS,sum(emb_cons) as emb_cons FROM wo_bom_process WHERE  po_break_down_id in ($po_id) group by po_break_down_id";
		$data_array = array();
		$tna_task_update_data = array();
		$data_array = sql_select($sql);
		foreach ($data_array as $row) {
			$tna_task_update_data[60]['reqqnty'] = $row->KINT_GREY_FAB_QNTY_PROD;
			$tna_task_update_data[64]['reqqnty'] = $row->KINT_FIN_FAB_QNTY_PROD;
			$tna_task_update_data[84]['reqqnty'] = $podtls['po_quantity'];
			$tna_task_update_data[86]['reqqnty'] = $podtls['po_quantity'];
		}*/
		$tna_task_update_data[60]['reqqnty'] = $precostData[knit][gray];
		$tna_task_update_data[64]['reqqnty'] = ($precostData[knit][finish])?$precostData[knit][finish]:$precostData[woven][finish];
		$tna_task_update_data[84]['reqqnty'] = $podtls['po_quantity'];
		$tna_task_update_data[86]['reqqnty'] = $podtls['po_quantity'];


		$sql = "SELECT PO_BREAK_DOWN_ID, min(production_date) as MIND,max(production_date) as MAXD, PRODUCTION_TYPE,sum(production_quantity) as PRODUCTION_QUANTITY FROM  pro_garments_production_mst  WHERE po_break_down_id in ($po_id )   group by po_break_down_id,production_type";
		$result = sql_select($sql);
		foreach ($result as $row) {
			$tsktype = 0;
			if ($row->PRODUCTION_TYPE == 1) {
				$tsktype = 84;
			} else if ($row->PRODUCTION_TYPE == 3) {
				$tsktype = 85;
			} else if ($row->PRODUCTION_TYPE == 5) {
				$tsktype = 86;
			} else if ($row->PRODUCTION_TYPE == 7) {
				$tsktype = 87;
			} else if ($row->PRODUCTION_TYPE == 8) {
				$tsktype = 88;
			} else if ($row->PRODUCTION_TYPE == 10) {
				$tsktype = 87;
			}

			$tna_task_update_data[$tsktype]['max_start_date'] = date('d-m-Y', strtotime($row->MAXD));
			$tna_task_update_data[$tsktype]['min_start_date'] = date('d-m-Y', strtotime($row->MIND));
			$tna_task_update_data[$tsktype]['doneqnty'] = $row->PRODUCTION_QUANTITY;
		}
		//return $tna_task_update_data;

		$production_days = return_field_value("count(distinct(production_date)) as id", "pro_garments_production_mst", "po_break_down_id in($po_id) and production_type=5 group by  po_break_down_id", "id");
		$daily_production = 0;
		if (isset($tna_task_update_data[86]['doneqnty']) && $production_days) {
			$daily_production = $tna_task_update_data[86]['doneqnty'] / $production_days;
		}

		$sql = "SELECT b.PO_BREAKDOWN_ID,b.ENTRY_FORM, min(a.receive_date)  as MINDATE, max(a.receive_date) as  MAXDATE, sum(quantity) as PROD_QNTRY FROM inv_receive_master a,  order_wise_pro_details b, pro_grey_prod_entry_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 2 ) and b.po_breakdown_id in ($po_id) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
		$data_array = sql_select($sql);
		foreach ($data_array as $row) {
			$tna_task_update_data[60]['max_start_date'] = date('d-m-Y', strtotime($row->MAXDATE));
			$tna_task_update_data[60]['min_start_date'] = date('d-m-Y', strtotime($row->MINDATE));
			$tna_task_update_data[60]['doneqnty'] = $row->PROD_QNTRY;
		}

		$sql = "SELECT b.PO_BREAKDOWN_ID,b.ENTRY_FORM, min(a.receive_date) MINDATE, max(a.receive_date) MAXDATE, sum(quantity) as PROD_QNTRY
        FROM inv_receive_master a,  order_wise_pro_details b, pro_finish_fabric_rcv_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 7 ) and b.po_breakdown_id in  ($po_id) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";

		$data_array = sql_select($sql);
		foreach ($data_array as $row) {
			$tna_task_update_data[64]['max_start_date'] = date('d-m-Y', strtotime($row->MAXDATE));
			$tna_task_update_data[64]['min_start_date'] = date('d-m-Y', strtotime($row->MINDATE));
			$tna_task_update_data[64]['doneqnty'] = $row->PROD_QNTRY;
		}

		$sql = "SELECT b.PO_BREAKDOWN_ID, min(a.transaction_date) as MINDATE, max(a.transaction_date) as  MAXDATE, sum(quantity) as PROD_QNTRY,d.TRIM_TYPE FROM inv_transaction a,  order_wise_pro_details b, product_details_master c , lib_item_group d where a.prod_id=c.id and b.trans_id=a.id and c.item_group_id=d.id and d.trim_type in (1,2) and b.entry_form in ( 24 ) and b.po_breakdown_id in ($po_id) group by b.po_breakdown_id,d.trim_type order by b.po_breakdown_id";
		   //return $sql;

		$data_array = sql_select($sql);
		foreach ($data_array as $row) {
			$entry = ($row->TRIM_TYPE == 1 ? 70 : 71);

			$tna_task_update_data[$entry]['max_start_date'] = date('d-m-Y', strtotime($row->MAXDATE));
			$tna_task_update_data[$entry]['min_start_date'] = date('d-m-Y', strtotime($row->MINDATE));
			$tna_task_update_data[$entry]['doneqnty'] = $row->PROD_QNTRY;
		}
		
		 //return $tna_task_update_data[70]['doneqnty'];
		
		$line_enganed = return_field_value("count(distinct(line_id)) as id", "ppl_sewing_plan_board", "po_break_down_id in($po_id) group by  po_break_down_id", "id");
		$balance = 0;
		if (isset($tna_task_update_data[86]['doneqnty'])) {
			$balance = $podtls['plan_cut'] - $tna_task_update_data[86]['doneqnty'];
		}
		$days_required = 0;
		if ($daily_production) {
			$days_required = ceil($balance / $daily_production);
		}

		$to_be_end = add_date(date("Y-m-d", time()), $days_required, 1);
		if (!isset($podtls['pub_shipment_date'])) {
			$podtls['pub_shipment_date'] = null;
		}

		$late_early = datediff("d", $to_be_end, $podtls['pub_shipment_date']);
		if ($late_early < 3) {
			$color = "red";
		}
		$late_early = $late_early - 2;
		// return $tna_task_update_data;

		$location_res = $this->db->query("select ID,LOCATION_NAME from lib_location  where status_active =1 and is_deleted=0  order by location_name")->result();

		foreach ($location_res as $value) {
			$location_arr[$value->ID] = $value->LOCATION_NAME;
		}
		$data_array = array();
		$i = 0;
		if (count($tnadata) > 0 || count($result) > 0 || count($job_arr) > 0) {
			
			
			if (isset($tnadata[60]['start'])) {
				$data_array[$i]["KNITTING_START"] = $tnadata[60]['start'];
			} else {
				$data_array[$i]["KNITTING_START"] = null;
			}

			if (isset($tnadata[60]['end'])) {
				$data_array[$i]["KNITTING_END"] = $tnadata[60]['end'];
			} else {
				$data_array[$i]["KNITTING_END"] = null;
			}
			
			if (isset($tna_task_update_data[60]['reqqnty'])) {
				$data_array[$i]["KNITTING_QTY_REQUIRED"] = number_format($tna_task_update_data[60]['reqqnty'], 0, '.', '');
			} else {
				$data_array[$i]["KNITTING_QTY_REQUIRED"] = null;
			}

			if (isset($tna_task_update_data[60]['doneqnty'])) {
				$data_array[$i]["KNITTING_QTY_AVAILABLE"] = number_format($tna_task_update_data[60]['doneqnty'], 0, '.', '');
			} else {
				$data_array[$i]["KNITTING_QTY_AVAILABLE"] = null;
			}

			if (isset($tnadata[64]['start'])) {
				$data_array[$i]["FIN_FAB_PROD_START"] = $tnadata[64]['start'];
			} else {
				$data_array[$i]["FIN_FAB_PROD_START"] = null;
			}

			if (isset($tnadata[64]['end'])) {
				$data_array[$i]["FIN_FAB_PROD_END"] = $tnadata[64]['end'];
			} else {
				$data_array[$i]["FIN_FAB_PROD_END"] = null;
			}

			if (isset($tna_task_update_data[64]['reqqnty'])) {
				$data_array[$i]["FIN_FAB_QTY_REQUIRED"] = number_format($tna_task_update_data[64]['reqqnty'], 0, '.', '');
			} else {
				$data_array[$i]["FIN_FAB_QTY_REQUIRED"] = null;
			}

			if (isset($tna_task_update_data[64]['doneqnty'])) {
				$data_array[$i]["FIN_FAB_QTY_AVAILABLE"] = number_format($tna_task_update_data[64]['doneqnty'], 0, '.', '');
			} else {
				$data_array[$i]["FIN_FAB_QTY_AVAILABLE"] = null;
			}

			if (isset($tnadata[12]['start'])) {
				$data_array[$i]["PP_START"] = $tnadata[12]['start'];
			} else {
				$data_array[$i]["PP_START"] = null;
			}

			if (isset($tnadata[12]['end'])) {
				$data_array[$i]["PP_END"] = $tnadata[12]['end'];
			} else {
				$data_array[$i]["PP_END"] = null;
			}

			if (isset($tnadata[70]['start'])) {
				$data_array[$i]["SEW_TRIM_REV_START"] = $tnadata[70]['start'];
			} else {
				$data_array[$i]["SEW_TRIM_REV_START"] = null;
			}

			if (isset($tnadata[70]['end'])) {
				$data_array[$i]["SEW_TRIM_REV_END"] = $tnadata[70]['end'];
			} else {
				$data_array[$i]["SEW_TRIM_REV_END"] = null;
			}

			if ($tna_task_update_data[70]['reqqnty']>0) {
				$data_array[$i]["SEW_TRIM_REV_REQUIRED"] = number_format($tna_task_update_data[70]['reqqnty'], 0, '.', '');
			} else {
				$data_array[$i]["SEW_TRIM_REV_REQUIRED"] = null;
			}

			if (isset($tnadata[84]['start'])) {
				$data_array[$i]["CUT_START"] = $tnadata[84]['start'];
			} else {
				$data_array[$i]["CUT_START"] = null;
			}

			if (isset($tnadata[84]['end'])) {
				$data_array[$i]["CUT_END"] = $tnadata[84]['end'];
			} else {
				$data_array[$i]["CUT_END"] = null;
			}

			if (isset($tna_task_update_data[84]['reqqnty'])) {
				$data_array[$i]["CUT_REQUIRED"] = number_format($tna_task_update_data[84]['reqqnty'], 0, '.', '');
			} else {
				$data_array[$i]["CUT_REQUIRED"] = null;
			}

			if (isset($tna_task_update_data[84]['doneqnty'])) {
				$data_array[$i]["CUT_AVAILABLE"] = number_format($tna_task_update_data[84]['doneqnty'], 0, '.', '');
			} else {
				$data_array[$i]["CUT_AVAILABLE"] = null;
			}

			if (isset($tnadata[86]['start'])) {
				$data_array[$i]["SEWING_START"] = $tnadata[86]['start'];
			} else {
				$data_array[$i]["SEWING_START"] = null;
			}

			if (isset($tnadata[86]['end'])) {
				$data_array[$i]["SEWING_END"] = $tnadata[86]['end'];
			} else {
				$data_array[$i]["SEWING_END"] = null;
			}

			if (isset($tna_task_update_data[86]['reqqnty'])) {
				$data_array[$i]["SEWING_REQUIRED"] = number_format($tna_task_update_data[86]['reqqnty'], 0, '.', '');
			} else {
				$data_array[$i]["SEWING_REQUIRED"] = null;
			}

			if (isset($tna_task_update_data[86]['doneqnty'])) {
				$data_array[$i]["SEWING_AVAILABLE"] = number_format($tna_task_update_data[86]['doneqnty'], 0, '.', '');
			} else {
				$data_array[$i]["SEWING_AVAILABLE"] = null;
			}

			$data_array[$i]["LINE_ENGAGED"] = $line_enganed;
			$data_array[$i]["PLAN_CUT"] = $podtls['plan_cut'];
			$data_array[$i]["garments_nature"] = $podtls['garments_nature'];
			$data_array[$i]["SEW_PROD_DAY"] = round($daily_production);
			$data_array[$i]["TO_BE_END"] = date('d-m-Y', strtotime($to_be_end));
			$data_array[$i]["EARLY_LATE_BY"] = $late_early;
			if (isset($location_arr[$podtls['location_name']])) {
				$data_array[$i]["location_name"] = $location_arr[$podtls['location_name']];
			} else {
				$data_array[$i]["location_name"] = null;
			}

			$data_array[$i]["job_no"] = $podtls['job_no'];
			$data_array[$i]["style_ref_no"] = $podtls['style_ref_no'];
			$data_array[$i]["po_number"] = $podtls['po_number'];
			$data_array[$i]["po_received_date"] = date('d-m-Y', strtotime($podtls['po_received_date']));
			$data_array[$i]["shipment_date"] = date('d-m-Y', strtotime($podtls['shipment_date']));
			$data_array[$i]["pub_shipment_date"] = date('d-m-Y', strtotime($podtls['pub_shipment_date']));

			$data_array[$i]["po_quantity"] = $podtls['po_quantity'];
			$data_array[$i]["plan_cut"] = $podtls['plan_cut'];

			$i++;

		}
		$null_array = array();
		if (count($data_array) > 0) {

			return $data_array;
		} else {
			return $null_array;
		}

	}

	function get_item_wise_line_efficiency_info($company_id, $item_id) {
		//$db_type=2;
		$prod_reso_allo = get_resource_allocation_variable($company_id);
		$current_date_time = date('d-m-Y H:i');
		$ex_date_time = explode(" ", $current_date_time);
		$current_date = $ex_date_time[0];
		//$originalDate = "2010-03-21";
		$txt_date = "04-09-2010 ";
		$search_prod_date = date("d-m-Y", strtotime($txt_date));
		// $search_prod_date=change_date_format(str_replace("'","",$txt_date),'yyyy-mm-dd');

		$date_cond = "";
		$date_format = "";
		/*if($this->db->dbdriver=='mysqli')
			        {
			            $date_cond=" and production_date='" . date("Y-m-d", strtotime($txt_date)) . "' ";
			            $date_format=  date("Y-m-d", strtotime($txt_date)) ;
			        }
			        else
			        {
			            $date_cond = " and production_date='" . date("d-M-Y", strtotime($txt_date)) . "'";
			            $date_format = date("d-M-y", strtotime($txt_date));
			            $date_format_arr=explode("-", $date_format);
			            $date_format=$date_format_arr[0].'-'.strtoupper($date_format_arr[1]).'-'.$date_format_arr[2];
		*/

		$comp = array();
		$com_res = $this->db->query("select ID,COMPANY_NAME from lib_company where status_active =1 and is_deleted=0  order by company_name")->result();

		foreach ($com_res as $value) {
			$comp[$value->ID] = $value->COMPANY_NAME;
		}

		$sewing_line_sql = $this->db->query("select ID,LINE_NAME from lib_sewing_line")->result();
		$line_array = array();
		foreach ($sewing_line_sql as $value) {
			$line_array[$value->ID] = $value->LINE_NAME;
		}
		// return $line_array;
		$resource_arr = array();
		$resource_sql = "SELECT ID,LINE_NUMBER from prod_resource_mst    ";
		$resource_data = $this->db->query($resource_sql)->result();

		foreach ($resource_data as $vals) {
			$id = $vals->ID;
			$all_line = trim($vals->LINE_NUMBER);
			$all_line_arr = explode(",", $all_line);
			$l_name = "";
			foreach ($all_line_arr as $l_id) {
				if (isset($line_array[$l_id])) {
					$line = $line_array[$l_id];
					if ($l_name == "") {
						$l_name .= $line_array[$l_id];
					} else {
						$l_name .= ',' . $line_array[$l_id];
					}

				}

			}
			$resource_arr[$id] = $l_name;
		}

		if ($prod_reso_allo == 1) {
			$prod_resource_array = array();
			$data_sql = "SELECT a.ID, a.LOCATION_ID, a.FLOOR_ID, a.LINE_NUMBER, b.ACTIVE_MACHINE, b.PR_DATE, b.MAN_POWER, b.OPERATOR, b.HELPER, b.SMV_ADJUST, b.SMV_ADJUST_TYPE, b.LINE_CHIEF, b.TARGET_PER_HOUR, b.WORKING_HOUR, c.FROM_DATE, c.TO_DATE, c.CAPACITY as MC_CAPACITY from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=c.mst_id and a.id=b.mst_id and c.id=b.mast_dtl_id    "; //--and pr_date=$txt_date
			$dataArray = $this->db->query($data_sql)->result();

			foreach ($dataArray as $val) {
				$prod_resource_array[$val->ID][$val->PR_DATE]['man_power'] = $val->MAN_POWER;
				$prod_resource_array[$val->ID][$val->PR_DATE]['operator'] = $val->OPERATOR;
				$prod_resource_array[$val->ID][$val->PR_DATE]['helper'] = $val->HELPER;
				$prod_resource_array[$val->ID][$val->PR_DATE]['terget_hour'] = $val->TARGET_PER_HOUR;
				$prod_resource_array[$val->ID][$val->PR_DATE]['working_hour'] = $val->WORKING_HOUR;
				$prod_resource_array[$val->ID][$val->PR_DATE]['tpd'] = $val->TARGET_PER_HOUR * $val->WORKING_HOUR;
				$prod_resource_array[$val->ID][$val->PR_DATE]['day_start'] = $val->FROM_DATE;
				$prod_resource_array[$val->ID][$val->PR_DATE]['day_end'] = $val->TO_DATE;
				$prod_resource_array[$val->ID][$val->PR_DATE]['capacity'] = $val->MC_CAPACITY;
				$prod_resource_array[$val->ID][$val->PR_DATE]['smv_adjust'] = $val->SMV_ADJUST;
				$prod_resource_array[$val->ID][$val->PR_DATE]['smv_adjust_type'] = $val->SMV_ADJUST_TYPE;
			}
		}
		//return $prod_resource_array;
		$smv_source = return_field_value("smv_source", "variable_settings_production", "company_name in ($company_id) and variable_list=25 and status_active=1 and is_deleted=0", "smv_source");

		if ($smv_source == 3) {
			$sql_item = "SELECT b.ID, a.SAM_STYLE, a.GMTS_ITEM_ID from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and a.gmts_item_id ='$item_id'";
			$resultItem = $this->db->query($sql_item)->result();

			foreach ($resultItem as $itemData) {
				$item_smv_array[$itemData->ID][$itemData->GMTS_ITEM_ID] = $itemData->SAM_STYLE;
			}
		} else {
			$sql_item = "SELECT b.ID, a.SET_BREAK_DOWN, c.GMTS_ITEM_ID, c.SET_ITEM_RATIO, SMV_PCS, SMV_PCS_PRECOST from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and c.gmts_item_id='$item_id' ";
			$resultItem = $this->db->query($sql_item)->result();
			foreach ($resultItem as $itemData) {
				if ($smv_source == 1) {
					$item_smv_array[$itemData->ID][$itemData->GMTS_ITEM_ID] = $itemData->SMV_PCS;
				} else if ($smv_source == 2) {
					$item_smv_array[$itemData->ID][$itemData->GMTS_ITEM_ID] = $itemData->SMV_PCS_PRECOST;
				}
			}
		}

		$all_cond = "";
		$all_cond .= " and company_id='$company_id'";
		$all_cond .= " and item_number_id='$item_id'";
		$line_info = "SELECT PRODUCTION_DATE, SEWING_LINE,PO_BREAK_DOWN_ID,ITEM_NUMBER_ID,sum(PRODUCTION_QUANTITY) as QNTY  from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=5 and sewing_line<>0      $all_cond  and prod_reso_allo=1   group by PRODUCTION_DATE, SEWING_LINE,PO_BREAK_DOWN_ID,ITEM_NUMBER_ID ";
		$line_data = $this->db->query($line_info)->result();
		$prod_start_cond = "";
		$line_data_arr = array();
		$line_wise_produce_min = array();
		$line_wise_efficiency = array();
		$line_wise_efficiency2 = array();
		if ($this->db->dbdriver == 'mysqli') {
			$prod_start_cond = " prod_start_time";
		} else {
			$prod_start_cond = " TO_CHAR(prod_start_time,'HH24:MI')";
		}

		$variable_start_time_arr = '';
		$prod_start_time = "SELECT $prod_start_cond as PROD_START_TIME from variable_settings_production where company_name in($company_id) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1";
		$prod_start_time = $this->db->query($prod_start_time)->result();
		foreach ($prod_start_time as $row) {
			$ex_time = explode(" ", $row->PROD_START_TIME);
			$variable_start_time_arr = $row->PROD_START_TIME;
		}

		$current_date_time = date('d-m-Y H:i');
		$ex_date_time = explode(" ", $current_date_time);
		$current_date = $ex_date_time[0];
		$current_time = $ex_date_time[1];
		$ex_time = explode(":", $current_time);

		//$search_prod_date=change_date_format(str_replace("'","",$txt_date),'yyyy-mm-dd');
		$current_eff_min = ($ex_time[0] * 60) + $ex_time[1];

		$variable_time = explode(":", $variable_start_time_arr);
		if (isset($variable_time[1])) {
			$vari_min = ($variable_time[0] * 60) + $variable_time[1];
		} else {
			$vari_min = ($variable_time[0] * 60);
		}

		$difa_time = explode(".", number_format(($current_eff_min - $vari_min) / 60, 2)); //datediff("",$ctime,$variable_start_time_arr);
		$dif_time = $difa_time[0];

		$all_po_array = array();
		$po_days_run_array = array();
		$po_item_wise_eff_arr = array();
		foreach ($line_data as $vals) {
			if (isset($po_days_run_array[$vals->PO_BREAK_DOWN_ID][$vals->PRODUCTION_DATE])) {
				$po_days_run_array[$vals->PO_BREAK_DOWN_ID][$vals->PRODUCTION_DATE] += 1;

			} else {
				$po_days_run_array[$vals->PO_BREAK_DOWN_ID][$vals->PRODUCTION_DATE] = 1;
			}
		}
		//return count($po_days_run_array[33211]);
		foreach ($line_data as $vals) {

			$all_po_array[$vals->PO_BREAK_DOWN_ID] = $vals->PO_BREAK_DOWN_ID;
			if ($this->db->dbdriver == 'mysqli') {
				$date_format = date("Y-m-d", strtotime($vals->PRODUCTION_DATE));
			} else {

				$date_format = date("d-M-y", strtotime($vals->PRODUCTION_DATE));
				$date_format_arr = explode("-", $date_format);
				$date_format = $date_format_arr[0] . '-' . strtoupper($date_format_arr[1]) . '-' . $date_format_arr[2];
			}

			$line_data_arr[$vals->SEWING_LINE] = $vals->SEWING_LINE;
			$smv_adjustmet_type = "";
			if (isset($prod_resource_array[$vals->SEWING_LINE][$date_format]['smv_adjust_type'])) {
				$smv_adjustmet_type = $prod_resource_array[$vals->SEWING_LINE][$date_format]['smv_adjust_type'];
			}

			$total_adjustment = 0;
			if (str_replace("'", "", $smv_adjustmet_type) == 1) {
				$total_adjustment = $prod_resource_array[$vals->SEWING_LINE][$date_format]['smv_adjust'];
			} else if (str_replace("'", "", $smv_adjustmet_type) == 2) {
				$total_adjustment = ($prod_resource_array[$vals->SEWING_LINE][$date_format]['smv_adjust']) * (-1);
			}
			//$cla_cur_time=$prod_resource_array[$vals->SEWING_LINE][$date_format]['working_hour'];
			if ($current_date == $search_prod_date) {
				$prod_wo_hour = "";
				if (isset($prod_resource_array[$vals->SEWING_LINE][$date_format]['working_hour'])) {
					$prod_wo_hour = $prod_resource_array[$vals->SEWING_LINE][$date_format]['working_hour'];
				}

				if ($dif_time < $prod_wo_hour) {

					//$current_wo_time=$dif_hour_min;
					$cla_cur_time = $dif_time;
				} else {

					//$current_wo_time=$prod_wo_hour;
					$cla_cur_time = $prod_wo_hour;
				}
			} else {

				//$current_wo_time=$prod_resource_array[$l_id][$pr_date]['working_hour'];
				if (isset($prod_resource_array[$vals->SEWING_LINE][$date_format]['working_hour'])) {
					$cla_cur_time = $prod_resource_array[$vals->SEWING_LINE][$date_format]['working_hour'];
				}

			}
			// return  $cla_cur_time;

			$efficiency_min = "";
			if (isset($prod_resource_array[$vals->SEWING_LINE][$date_format]['man_power'])) {
				$efficiency_min = $total_adjustment + ($prod_resource_array[$vals->SEWING_LINE][$date_format]['man_power']) * $cla_cur_time * 60;
			}
			if (empty($item_smv_array[trim($vals->PO_BREAK_DOWN_ID)][trim($vals->ITEM_NUMBER_ID)])) {
				$line_wise_produce_min[$vals->SEWING_LINE][] = ($vals->QNTY * 1) * 0;
				$smv_qnty = 0;

			} else {
				$line_wise_produce_min[$vals->SEWING_LINE][] = ($vals->QNTY * 1) * ($item_smv_array[trim($vals->PO_BREAK_DOWN_ID)][trim($vals->ITEM_NUMBER_ID)] * 1);
				$smv_qnty = ($vals->QNTY * 1) * ($item_smv_array[trim($vals->PO_BREAK_DOWN_ID)][trim($vals->ITEM_NUMBER_ID)] * 1);
			}

			$line_wise_efficiency[$vals->SEWING_LINE][] = $efficiency_min;
			$line_wise_efficiency2[$vals->SEWING_LINE] = $efficiency_min;
			if ($efficiency_min) {
				if (isset($po_item_wise_eff_arr[$vals->SEWING_LINE])) {
					if (isset($efficiency_min)) {
						$po_item_wise_eff_arr[$vals->SEWING_LINE] += number_format(($smv_qnty * 100 / $efficiency_min) / count($po_days_run_array[$vals->PO_BREAK_DOWN_ID]), 2);
					}

				} else {
					if (count($po_days_run_array[$vals->PO_BREAK_DOWN_ID])) {
						if (isset($efficiency_min)) {
							$po_item_wise_eff_arr[$vals->SEWING_LINE] = number_format(($smv_qnty * 100 / $efficiency_min) / count($po_days_run_array[$vals->PO_BREAK_DOWN_ID]), 2);
						}

					}

				}
			} else {
				$po_item_wise_eff_arr[$vals->SEWING_LINE] = 0;
			}

		}
		//return   $efficiency_min;

		$data_array = array();
		$i = 0;
		$arrs = array();
		foreach ($line_data_arr as $keys => $rows) {
			$data_array[$i]["LINE_ID"] = $keys;
			if (isset($resource_arr[$keys])) {
				$data_array[$i]["LINE_NAME"] = $resource_arr[$keys];
			} else {
				$data_array[$i]["LINE_NAME"] = "";
			}

			//$data_array[$i]["LINE_NAME"]  = $resource_arr[$keys];
			if (isset($line_wise_efficiency2[$keys])) {
				/*$eff=$line_wise_efficiency2[$keys];
					                if($eff)
					                {
					                    if(count($line_wise_produce_min[$keys])>1)
					                    {
					                        $arrs=$line_wise_produce_min[$keys] ;
					                        $values=0;
					                        for($j=0;$j<count($arrs);$j++)
					                        {

					                            $v=$arrs[$j]*100;
					                            $values+=$v/$eff;

					                        }
					                        //$data_array[$i]["EFFICIENCY"]  =number_format($values,2);
					                    }
					                    else
					                    {
					                        $smv_qnty= $line_wise_produce_min[$keys][0]*100;
					                        //$data_array[$i]["EFFICIENCY"]  =number_format($smv_qnty/$eff,2);

					                    }

					                }
					                else
					                {
					                    //$data_array[$i]["EFFICIENCY"]  ="";
					                }
				*/
				$data_array[$i]["EFFICIENCY"] = number_format($po_item_wise_eff_arr[$keys] / count($all_po_array), 2) . " ";

			} else {

				$data_array[$i]["EFFICIENCY"] = "";
			}

			$i++;

		}
		if (count($data_array) > 0) {
			$sort = array();
			foreach ($data_array as $k => $v) {
				$sort['EFFICIENCY'][$k] = $v['EFFICIENCY'];
			}

			array_multisort($sort['EFFICIENCY'], SORT_DESC, $data_array);

		}

		if (count($line_data_arr) > 0) {
			return $data_array;
		} else {
			return 0;
		}

	}
	function get_knit_trend_graph_info($company = 0, $pro_company = 0, $location = 0, $floor = 0, $types = 0) {
		if ($this->db->dbdriver == 'mysqli') {
			$db_type = 0;
		} else {
			$db_type = 2;
		}

		$company_name = $company;
		if ($location != "" and $location != 0) {
			$location_cond = "and location_id=$location ";
		} else {
			$location_cond = "";
		}

		$smv_source = return_field_value("smv_source", "variable_settings_production", "company_name in ($company) and variable_list=25 and status_active=1 and is_deleted=0", "smv_source");
		if ($smv_source == "") {
			$smv_source = 0;
		} else {
			$smv_source = $smv_source;
		}

		if ($pro_company) {$companyCon = "and company_id=$pro_company";} else { $companyCon = "and company_id=$company";}
		$machine_arr = array();
		$machine_id_arr = array();
		$machine_dyeing_id_arr = array();

		$machine_sql_arr = sql_select("select id, prod_capacity, category_id from lib_machine_name where category_id in(1,2) $companyCon and is_deleted=0 and status_active=1 $location_cond");
		foreach ($machine_sql_arr as $machineRow) {
			$machine_arr[$machineRow->ID] = $machineRow->PROD_CAPACITY;

			if ($machineRow->CATEGORY_ID == 1) {
				$machine_id_arr[] = $machineRow->ID;
			} else {
				$machine_dyeing_id_arr[] = $machineRow->ID;
			}
		}

		$idle_machine_array = array();
		$sql_machine_idle = sql_select("select machine_entry_tbl_id, from_date, to_date from pro_cause_of_machine_idle where machine_idle_cause in(1,2,3,6,7,8) and is_deleted=0 and status_active=1");
		foreach ($sql_machine_idle as $idleRow) {
			$from_date = date("Y-m-d", strtotime($idleRow->FROM_DATE));
			$to_date = date("Y-m-d", strtotime($idleRow->TO_DATE));
			$datediff_n = datediff('d', $from_date, $to_date);
			for ($k = 0; $k < $datediff_n; $k++) {
				$newdate_n = add_date(str_replace("'", "", $from_date), $k, 1);
				if (isset($idle_machine_array[$newdate_n])) {
					$idle_machine_array[$newdate_n] .= $idleRow->MACHINE_ENTRY_TBL_ID . ",";
				} else {
					$idle_machine_array[$newdate_n] = $idleRow->MACHINE_ENTRY_TBL_ID;
				}

			}
		}

		$datediff = 30;
		$today = date('Y-m-d'); //$today='2014-06-04';
		if ($db_type == 0) {
			$firstDate = date("Y-m-d", strtotime("-29 day", strtotime($today)));
			$lastDate = date("Y-m-d", strtotime($today));
		} else {
			$firstDate = date("d-M-Y", strtotime("-29 day", strtotime($today)));
			$lastDate = date("d-M-Y", strtotime($today));
		}

		for ($j = 0; $j < $datediff; $j++) {
			$newdate = add_date($firstDate, $j, 1);
			$date_array[$j] = date("d-M", strtotime($newdate));
		}

		$yarn_stock_array = array();
		$knit_array = array();
		$dye_array = array();

		if ($pro_company) {$companyConKnit = "and a.knitting_company=$pro_company";} else { $companyConKnit = "and a.company_id=$company";}

		$sql_knit = " SELECT a.RECEIVE_DATE,sum(b.grey_receive_qnty) as QNTY  FROM inv_receive_master a, pro_grey_prod_entry_dtls b WHERE a.id=b.mst_id $companyConKnit and a.item_category=13 and a.entry_form=2 and a.knitting_source=1 and a.receive_date<='$lastDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.receive_date";

		$knitData = sql_select($sql_knit);
		$knit_data_array = array();
		foreach ($knitData as $val) {
			$knit_data_array[$val->RECEIVE_DATE] = $val->QNTY;
		}

		$sql_dye = " SELECT c.PROCESS_END_DATE ,sum( b.batch_qnty) as QNTY from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form in(35,38) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.process_end_date<='$lastDate' and c.result in(1,5) group by c.process_end_date ";
		$dyeData = sql_select($sql_dye);
		$dye_data_array = array();
		foreach ($dyeData as $val) {
			$dye_data_array[$val->PROCESS_END_DATE] = $val->QNTY;
		}

		$data_array = array();
		$kk = 0;

		for ($j = 0; $j < $datediff; $j++) {
			$newdate = add_date($firstDate, $j, 1);
			$prod_date_old = date("Y-m-d", strtotime($newdate));
			$prod_date = strtoupper(date("d-M-y", strtotime($newdate)));

			$data_array[$kk]["date"] = $prod_date;
			$dye_machine_capacity = 0;
			$machine_capacity = 0;
			$idle_machine = array();
			$active_machine = array();
			$dye_active_machine = array();
			if ($types == 1) {

				if (isset($idle_machine_array[$prod_date_old])) {
					$idle_machine = explode(",", substr($idle_machine_array[$prod_date_old], 0, -1));

				}
				$active_machine = array_diff($machine_id_arr, $idle_machine);

				foreach ($active_machine as $machine) {
					if (isset($machine_arr[$machine])) {
						$machine_capacity += $machine_arr[$machine];
					}

				}

				if (isset($knit_data_array[$prod_date])) {
					$data_array[$kk]["knit"] = $knit_data_array[$prod_date];
				} else {
					$data_array[$kk]["knit"] = 0;
				}

				$data_array[$kk]["capacity"] = $machine_capacity;
			} else {
				$dye_active_machine = array_diff($machine_dyeing_id_arr, $idle_machine);

				foreach ($dye_active_machine as $machine_dye) {
					$dye_machine_capacity += $machine_arr[$machine_dye];
				}

				if (isset($dye_data_array[$prod_date])) {
					$data_array[$kk]["dyeing"] = $dye_data_array[$prod_date];
				} else {
					$data_array[$kk]["dyeing"] = 0;
				}

				$data_array[$kk]["capacity"] = $dye_machine_capacity;
			}
			$kk++;

		}
		return $data_array;

	}

	function get_sewing_trend_graph_info($company_id = 0, $serving_company = 0, $location = 0, $floor = 0) {
		$company = $company_id;
		$pro_company = $serving_company;
		if ($company) {$company = $company;} else { $company = "";}

		$datediff = 30;
		$today = date('Y-m-d');
		if ($this->db->dbdriver == 'mysqli') {
			$db_type = 0;
		} else {
			$db_type = 2;
		}

		if ($db_type == 0) {
			$firstDate = date("Y-m-d", strtotime("-29 day", strtotime($today)));
			$lastDate = date("Y-m-d", strtotime($today));
		} else {
			$firstDate = date("d-M-Y", strtotime("-29 day", strtotime($today)));
			$lastDate = date("d-M-Y", strtotime($today));
		}
		$date_array = array();

		for ($j = 0; $j < $datediff; $j++) {
			$newdate = add_date($firstDate, $j, 1);
			$date_array[$j] = date("d-M", strtotime($newdate));
		}
		//return ($date_array);
		if ($pro_company) {$companyCon = "and serving_company=$pro_company";} else { $companyCon = "and company_id=$company";}
		if ($location) {
			$location_cond_effi = "and location=$location ";
		} else {
			$location_cond_effi = "";
		}

		if ($floor) {
			$floor_con = "and floor_id=$floor ";
		} else {
			$floor_con = "";
		}

		$effi_data_arr = array();
		$po_id_arr = array();
		$sewProdArr = array();
		$proSql = "SELECT A.PRODUCTION_DATE, A.SEWING_LINE, A.PROD_RESO_ALLO, A.PO_BREAK_DOWN_ID, A.ITEM_NUMBER_ID, sum(b.PRODUCTION_QNTY) as PRODUCTION_QUANTITY
  from pro_garments_production_mst a, pro_garments_production_dtls b
  where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type=5 and b.production_type=5 $companyCon $location_cond_effi $floor_con and a.prod_reso_allo=1 and a.production_date between '$firstDate' and '$lastDate' group by a.production_date, a.sewing_line, a.prod_reso_allo, a.po_break_down_id, a.item_number_id";

		$sew_data_arr = sql_select($proSql);
		foreach ($sew_data_arr as $row) {
			$production_date = date("Y-m-d", strtotime($row->PRODUCTION_DATE));
			if (isset($effi_data_arr[$production_date])) {
				$effi_data_arr[$production_date] .= $row->SEWING_LINE . "**" . $row->PRODUCTION_QUANTITY . "**" . $row->PO_BREAK_DOWN_ID . "**" . $row->ITEM_NUMBER_ID . "**" . $row->PROD_RESO_ALLO . ",";

			} else {
				$effi_data_arr[$production_date] = $row->SEWING_LINE . "**" . $row->PRODUCTION_QUANTITY . "**" . $row->PO_BREAK_DOWN_ID . "**" . $row->ITEM_NUMBER_ID . "**" . $row->PROD_RESO_ALLO . ",";
			}

			$po_id_arr[$row->PO_BREAK_DOWN_ID] = $row->PO_BREAK_DOWN_ID;
		}
		//return $effi_data_arr;

		if ($location) {
			$location_cond_subcon = "and a.location_id=$location ";
		} else {
			$location_cond_subcon = "";
		}

		if ($pro_company) {$companyCon = "and a.company_id=$pro_company";} else { $companyCon = "and a.company_id=$company";}
		if ($floor) {
			$floor_con = "and a.floor_id=$floor ";
		} else {
			$floor_con = "";
		}

		$subConProd_arr = array();
		$subAchvSmv_arr = array();
		$sql_subconProd = "SELECT a.GMTS_ITEM_ID as ITEM_NUMBER_ID,a.LINE_ID as SEWING_LINE,a.ORDER_ID, a.PRODUCTION_DATE, a.PRODUCTION_QNTY, b.SMV ,a.PROD_RESO_ALLO FROM subcon_gmts_prod_dtls a, subcon_ord_dtls b WHERE a.order_id=b.id $companyCon and a.production_date between '$firstDate' and '$lastDate' and a.production_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond_subcon  $floor_con";
		$subconProdData = sql_select($sql_subconProd);
		foreach ($subconProdData as $row) {
			$production_date = date("Y-m-d", strtotime($row->PRODUCTION_DATE));
			if (isset($sewProdArr[$production_date])) {
				$sewProdArr[$production_date] += $row->PRODUCTION_QNTY;
			} else {
				$sewProdArr[$production_date] = $row->PRODUCTION_QNTY;
			}

			if (isset($subAchvSmv_arr[$production_date])) {
				$subAchvSmv_arr[$production_date] += $row->PRODUCTION_QNTY * $row->SMV;
			} else {
				$subAchvSmv_arr[$production_date] = $row->PRODUCTION_QNTY * $row->SMV;
			}

			if (isset($effi_data_arr[$production_date])) {
				$effi_data_arr[$production_date] .= $row->SEWING_LINE . "**" . $row->PRODUCTION_QNTY . "**" . $row->ORDER_ID . "**" . $row->ITEM_NUMBER_ID . "**" . $row->PROD_RESO_ALLO . ",";
			} else {
				$effi_data_arr[$production_date] = $row->SEWING_LINE . "**" . $row->PRODUCTION_QNTY . "**" . $row->ORDER_ID . "**" . $row->ITEM_NUMBER_ID . "**" . $row->PROD_RESO_ALLO . ",";
			}

		}

		$company_name = $company;
		$smv_source = return_field_value("smv_source", "variable_settings_production", "company_name in ($company) and variable_list=25 and status_active=1 and is_deleted=0", "smv_source");
		if ($smv_source == "") {
			$smv_source = 0;
		} else {
			$smv_source = $smv_source;
		}

		if ($location) {
			$location_cond_item = "and a.location_name=$location ";
		} else {
			$location_cond_item = "";
		}

		$item_smv_array = array();
		if ($smv_source == 3) {
			$sql_item = "SELECT b.ID, a.SAM_STYLE, a.GMTS_ITEM_ID from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem = sql_select($sql_item);
			foreach ($resultItem as $itemData) {
				$item_smv_array[$itemData->ID][$itemData->GMTS_ITEM_ID] = $itemData->SAM_STYLE;
			}
		} else {

			if ($pro_company) {
				$whereCon = "";
				$po_list_arr = array_chunk($po_id_arr, 999);
				$p = 1;
				foreach ($po_list_arr as $po_process) {
					if ($p == 1) {$whereCon .= " and ( b.id in(" . implode(',', $po_process) . ")";} else { $whereCon .= " or b.id in(" . implode(',', $po_process) . ")";}
					$p++;
				}
				if ($whereCon) {
					$whereCon .= ")";
				}

			} else {
				$whereCon = "and a.company_name=$company";
			}

			$sql_item = "SELECT b.ID, a.SET_BREAK_DOWN, c.GMTS_ITEM_ID, c.SET_ITEM_RATIO, SMV_PCS, SMV_PCS_PRECOST from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no $whereCon and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
			$resultItem = sql_select($sql_item);
			foreach ($resultItem as $itemData) {

				$item_smv_array[$itemData->ID][$itemData->GMTS_ITEM_ID]['smv_pcs'] = $itemData->SMV_PCS;
				$item_smv_array[$itemData->ID][$itemData->GMTS_ITEM_ID]['smv_pcs_precost'] = $itemData->SMV_PCS_PRECOST;
			}
		}
		//var_dump($item_smv_array);

		if ($pro_company) {$companyCon = "and a.company_id=$pro_company";} else { $companyCon = "and a.company_id=$company";}
		if ($location) {
			$location_cond = "and a.location_id=$location ";
		} else {
			$location_cond = "";
		}

		if ($floor) {
			$floor_con = "and a.floor_id=$floor ";
		} else {
			$floor_con = "";
		}

		$tpdArr = array();
		$tsmvArr = array();
		$tpd_data_arr = sql_select("SELECT a.ID, b.PR_DATE, b.MAN_POWER, b.SMV_ADJUST,b.SMV_ADJUST_TYPE, b.TARGET_PER_HOUR, b.WORKING_HOUR from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id $companyCon and b.pr_date between '$firstDate' and '$lastDate' and b.is_deleted=0 and c.is_deleted=0 $location_cond $floor_con group by a.id, b.pr_date, b.man_power, b.smv_adjust,b.smv_adjust_type, b.target_per_hour, b.working_hour ");
		foreach ($tpd_data_arr as $row) {
			$production_date = date("Y-m-d", strtotime($row->PR_DATE));
			if (isset($tsmvArr[$production_date][$row->ID])) {
				$tsmvArr[$production_date][$row->ID] += $row->MAN_POWER * $row->WORKING_HOUR * 60;
			} else {
				$tsmvArr[$production_date][$row->ID] = $row->MAN_POWER * $row->WORKING_HOUR * 60;
			}

			if ($row->SMV_ADJUST_TYPE == 1) {
				if (isset($tsmvArr[$production_date][$row->ID])) {
					$tsmvArr[$production_date][$row->ID] += $row->SMV_ADJUST;
				} else {
					$tsmvArr[$production_date][$row->ID] += $row->SMV_ADJUST;
				}

			} else if ($row->SMV_ADJUST_TYPE == 2) {
				if (isset($tsmvArr[$production_date][$row->ID])) {
					$tsmvArr[$production_date][$row->ID] -= $row->SMV_ADJUST;
				} else {
					$tsmvArr[$production_date][$row->ID] = $row->SMV_ADJUST;
				}

			}
		}

		$prod_capacity_arr = array();
		$dyeing_capacity_arr = array();
		$kk = 0;
		for ($j = 0; $j < $datediff; $j++) {
			$newdate = add_date($firstDate, $j, 1);
			$prod_date = date("Y-m-d", strtotime($newdate));

			$achv_smv = 0;
			$today_smv = 0;
			if (isset($effi_data_arr[$prod_date])) {
				$effi_data = explode(",", substr($effi_data_arr[$prod_date], 0, -1));
			} else {
				$effi_data = array();
			}

			foreach ($effi_data as $data) {
				//return $data;
				$data = explode("**", $data);
				$sewing_line = $data[0];
				$production_quantity = $data[1];
				$prod_reso_allo = $data[4];
				if ($prod_reso_allo == 1) {
					if (isset($tsmvArr[$prod_date][$sewing_line])) {
						$today_smv_arr[$prod_date][$sewing_line] = $tsmvArr[$prod_date][$sewing_line];
					} else {
						$today_smv_arr[$prod_date][$sewing_line] = 0;
					}

				}

				$po_break_down_id = $data[2];
				$item_number_id = $data[3];

				$item_smv = 0;
				if ($smv_source == 2) {
					if (isset($item_smv_array[$po_break_down_id][$item_number_id]['smv_pcs_precost'])) {
						$item_smv = $item_smv_array[$po_break_down_id][$item_number_id]['smv_pcs_precost'];
					} else {
						$item_smv = 0;
					}

				} else if ($smv_source == 3) {
					if (isset($item_smv_array[$po_break_down_id][$item_number_id])) {
						$item_smv = $item_smv_array[$po_break_down_id][$item_number_id];
					} else {
						$item_smv = 0;
					}

				} else {

					if (isset($item_smv_array[$po_break_down_id][$item_number_id]['smv_pcs'])) {
						$item_smv = $item_smv_array[$po_break_down_id][$item_number_id]['smv_pcs'];
					} else {
						$item_smv = 0;
					}

				}
				$achv_smv += $production_quantity * $item_smv;
			}
			if (isset($subAchvSmv_arr[$prod_date])) {
				$achv_smv += $subAchvSmv_arr[$prod_date];
			}

			if (isset($today_smv_arr[$prod_date])) {
				$today_smv = array_sum($today_smv_arr[$prod_date]);
			} else {
				$today_smv = 0;
			}

			if ($today_smv) {
				$today_aff_perc = $achv_smv / $today_smv * 100;
			} else {
				$today_aff_perc = 0;
			}

			$format_date = date("d-M", strtotime($prod_date));
			$effi_perc_arr[$kk]["date"] = $format_date;
			$effi_perc_arr[$kk]["qty"] = number_format($today_aff_perc, 2, '.', '');
			$kk++;

		}
		return $effi_perc_arr;

		$data_array = array();
		$i = 0;
		$arrs = array();
		foreach ($line_data_arr as $keys => $rows) {
			$data_array[$i]["LINE_ID"] = $keys;
			if (isset($resource_arr[$keys])) {
				$data_array[$i]["LINE_NAME"] = $resource_arr[$keys];
			} else {
				$data_array[$i]["LINE_NAME"] = "";
			}

			//$data_array[$i]["LINE_NAME"]  = $resource_arr[$keys];
			if (isset($line_wise_efficiency2[$keys])) {
				/*$eff=$line_wise_efficiency2[$keys];
					                if($eff)
					                {
					                    if(count($line_wise_produce_min[$keys])>1)
					                    {
					                        $arrs=$line_wise_produce_min[$keys] ;
					                        $values=0;
					                        for($j=0;$j<count($arrs);$j++)
					                        {

					                            $v=$arrs[$j]*100;
					                            $values+=$v/$eff;

					                        }
					                        //$data_array[$i]["EFFICIENCY"]  =number_format($values,2);
					                    }
					                    else
					                    {
					                        $smv_qnty= $line_wise_produce_min[$keys][0]*100;
					                        //$data_array[$i]["EFFICIENCY"]  =number_format($smv_qnty/$eff,2);

					                    }

					                }
					                else
					                {
					                    //$data_array[$i]["EFFICIENCY"]  ="";
					                }
				*/
				$data_array[$i]["EFFICIENCY"] = number_format($po_item_wise_eff_arr[$keys] / count($all_po_array), 2) . " ";

			} else {

				$data_array[$i]["EFFICIENCY"] = "";
			}

			$i++;

		}

		if (count($line_data_arr) > 0) {
			return $data_array;
		} else {
			return 0;
		}

	}

	function get_plan_data_by_id($plan_id = 0) {
		$data_array = array();
		$sqls = "SELECT  b.PO_NUMBER,a.ID, a.PLAN_ID, a.PO_BREAK_DOWN_ID,  a.PLAN_QNTY, a.PO_DTLS, a.ITEM_NUMBER_ID,a.COUNTRY_ID, a.COLOR_NUMBER_ID, a.SIZE_NUMBER_ID, a.JOB_NO, a.PO_ID,SMV,b.PUB_SHIPMENT_DATE FROM ppl_sewing_plan_board_powise a,WO_PO_BREAK_DOWN b where PLAN_ID='$plan_id' and a.PO_BREAK_DOWN_ID=b.id";
		$data_sql = sql_select($sqls);
		$color_sql = sql_select("SELECT ID,COLOR_NAME from lib_color where status_active=1");
		$color_arr = array();
		$color_arr[0] = "";
		foreach ($color_sql as $v) {
			$color_arr[$v->ID] = $v->COLOR_NAME;
		}

		if (!count($data_sql)) {
			return $data_array;
		}
		$k = 0;
		foreach ($data_sql as $v) {
			$data_array[$k]["ID"] = $v->ID;
			$data_array[$k]["PLAN_ID"] = $v->PLAN_ID;
			$data_array[$k]["PO_BREAK_DOWN_ID"] = $v->PO_BREAK_DOWN_ID;
			$data_array[$k]["PO_NUMBER"] = $v->PO_NUMBER;
			$data_array[$k]["PLAN_QNTY"] = $v->PLAN_QNTY;
			$data_array[$k]["PO_DTLS"] = $v->PO_DTLS;
			$data_array[$k]["ITEM_NUMBER_ID"] = $v->ITEM_NUMBER_ID;
			$data_array[$k]["COUNTRY_ID"] = $v->COUNTRY_ID;
			$data_array[$k]["COLOR_NUMBER_ID"] = $v->COLOR_NUMBER_ID;
			$data_array[$k]["PUB_SHIPMENT_DATE"] = date('d-m-Y', strtotime($v->PUB_SHIPMENT_DATE));
			if (isset($color_arr[$v->COLOR_NUMBER_ID])) {
				$data_array[$k]["COLOR_NAME"] = $color_arr[$v->COLOR_NUMBER_ID];
			} else {
				$data_array[$k]["COLOR_NAME"] = "";
			}

			$data_array[$k]["SIZE_NUMBER_ID"] = $v->SIZE_NUMBER_ID;
			$data_array[$k]["JOB_NO"] = $v->JOB_NO;
			$data_array[$k]["PO_ID"] = $v->PO_ID;
			$data_array[$k]["SMV"] = $v->SMV;

			$k++;
		}
		return $data_array;
	}

	function get_job_data_info($cbo_company_mst, $cbo_buyer_name = "0", $chk_job_wo_po = "0", $txt_date_from, $txt_date_to, $garments_nature = "", $txt_job_prifix = "", $cbo_year_selection, $cbo_string_search_type = "", $txt_order_search = "", $cbo_date_type, $plan_level = "0", $txt_style_ref = "", $ignore_tna = "0", $order_status = "0", $po_break_down_id = 0, $set_dtls_id = 0, $color_size_id = 0) {

		
		$allow_capacity_allocation = return_field_value("capacity_allocation", "VARIABLE_SETTINGS_PRODUCTION", "company_name='$cbo_company_mst' and variable_list=54", "capacity_allocation");
		if($allow_capacity_allocation==1){
			
			if (trim($po_break_down_id)) {
				$all_were_con = " and a.PO_NO in( $po_break_down_id )";
	
			}
			if ($txt_job_prifix) {
				$all_were_con .= " and a.job_no like '%" . $txt_job_prifix . "%' "; 
				$all_were_con .= " and a.job_no like '%" . $txt_job_prifix . "%'";
			}
			
			$sql_allocationl="select b.company_name as FROM_COMPANY,b.location_name as FROM_LOCATION, a.company_id as TO_COMPANY, a.location_name as TO_LOCATION,  a.SMV, a.ALLOCATED_QTY, a.CUT_OFF_DATE,a.JOB_NO,a.PO_NO,a.ITEM from ppl_order_allocation_mst a,wo_po_details_master b where a.job_no=b.job_no and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1 and b.company_name=$cbo_company_mst $all_were_con";// and b.pub_shipment_date between '$s_date' and '$e_date'
			//return $sql_allocationl;
			$sql_allocationlRes=$this->db->query($sql_allocationl)->result();
			$allocationArr=array();
			foreach($sql_allocationlRes as $row)
			{
				$allocationFromArr[PO][$row->FROM_COMPANY][$row->JOB_NO][$row->PO_NO]+=$row->ALLOCATED_QTY;
				$allocationToArr[PO][$row->TO_COMPANY][$row->JOB_NO][$row->PO_NO]+=$row->ALLOCATED_QTY;
				
				$allocationFromArr[JOB][$row->FROM_COMPANY][$row->JOB_NO]+=$row->ALLOCATED_QTY;
				$allocationToArr[JOB][$row->TO_COMPANY][$row->JOB_NO]+=$row->ALLOCATED_QTY;
				
				$allocationFromArr[ITEM][$row->FROM_COMPANY][$row->JOB_NO][$row->PO_NO][$row->ITEM]+=$row->ALLOCATED_QTY;
				$allocationToArr[ITEM][$row->TO_COMPANY][$row->JOB_NO][$row->PO_NO][$row->ITEM]+=$row->ALLOCATED_QTY;

			}
			unset($sql_allocationlRes);	
		
		}
			
		//return $allocationFromArr;


		
		$data_array = array();
		$search_types = str_replace("'", "", $cbo_string_search_type);
		$txt_job_prifix = trim($txt_job_prifix);
		$txt_order_search = trim($txt_order_search);
		$txt_style_ref = trim($txt_style_ref);
		$is_confirmed_cond = "";
		if ($order_status) {
			$is_confirmed_cond .= " and b.is_confirmed='$order_status'";
		}

		//gsd.....................................................................
		$gsd_were_con = '';
		if ($cbo_date_type != 1) {
			// Shipment Date
			if ($txt_date_from && $txt_date_to) {
				if ($this->db->dbdriver == 'mysqli') {
					$gsd_were_con .= "and e.pub_shipment_date between '" . date("Y-m-d", strtotime($txt_date_from)) . "' and '" . date("Y-m-d", strtotime($txt_date_to)) . "'";
				} else {
					$gsd_were_con .= "and e.pub_shipment_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
				}
			}

		}

		if ($plan_level == 5) {
			$gsd_were_con = str_replace("e.pub_shipment_date", "f.country_ship_date", $gsd_were_con);
		}

		if ($txt_job_prifix || $txt_order_search || $txt_style_ref) {
			$gsd_were_con = '';
		}

		$gsd_were_con .= "and d.company_name=$cbo_company_mst";

		if ($order_status) {
			$gsd_were_con .= " and e.is_confirmed='$order_status'";
		}

		if ($cbo_buyer_name != 0) {$gsd_were_con .= " and d.buyer_name='$cbo_buyer_name'";}
		//if($txt_job_prifix){$gsd_were_con .=" and d.job_no_prefix_num=$txt_job_prifix ";}

		if ($txt_job_prifix) {
			$gsd_were_con .= " and d.job_no like '%" . $txt_job_prifix . "%' "; //else  $job_cond="";
			$gsd_were_con .= " and d.job_no like '%" . $txt_job_prifix . "%'";
		}

		if (trim($txt_style_ref)) {
			if ($search_types == 1) {
				$gsd_were_con .= " and d.style_ref_no = '$txt_style_ref'  ";
			} else if ($search_types == 2) {
				$gsd_were_con .= " and d.style_ref_no like '%$txt_style_ref%'  ";
			} else if ($search_types == 3) {
				$gsd_were_con .= " and d.style_ref_no like '%$txt_style_ref'  ";
			} else if ($search_types == 4) {
				$gsd_were_con .= " and d.style_ref_no like '%$txt_style_ref%'  ";
			}

		}

		if (trim($txt_order_search)) {
			if ($search_types == 1) {
				$gsd_were_con .= " and e.po_number = '$txt_order_search'  ";
			} else if ($search_types == 2) {
				$gsd_were_con .= " and e.po_number like '%$txt_order_search%'  ";
			} else if ($search_types == 3) {
				$gsd_were_con .= " and e.po_number like '%$txt_order_search'  ";
			} else if ($search_types == 4) {
				$gsd_were_con .= " and e.po_number like '%$txt_order_search%'  ";
			}

		}
		if (trim($po_break_down_id)) {
			$gsd_were_con .= " and e.id in( $po_break_down_id )";

		}
		if (trim($color_size_id)) {
			$gsd_were_con .= " and f.id in( $color_size_id )";
		}

		if (trim($set_dtls_id)) {
			$gsd_were_con .= " and c.id in( $set_dtls_id )";
		}

		$gsd_sql = "SELECT a.WORKING_HOUR, a.TOTAL_SMV, a.GMTS_ITEM_ID, a.STYLE_REF ,b.EFFICIENCY,b.ALLOCATED_MP,b.TARGET,c.JOB_NO from ppl_gsd_entry_mst a ,ppl_balancing_mst_entry b,wo_po_details_mas_set_details c,wo_po_details_master d ,wo_po_break_down e,wo_po_color_size_breakdown f  where  a.id= b.gsd_mst_id and  a.gmts_item_id = c.gmts_item_id and a.style_ref=d.style_ref_no and d.job_no=e.job_no_mst  and e.id=f.po_break_down_id $gsd_were_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  order by a.id desc ";

		//return $gsd_sql;

		$gsd_array = array();
		foreach (sql_select($gsd_sql) as $vals) {
			$gsd_array[$vals->JOB_NO][$vals->STYLE_REF][$vals->GMTS_ITEM_ID]["SMV"] = $vals->TOTAL_SMV;
			$gsd_array[$vals->JOB_NO][$vals->STYLE_REF][$vals->GMTS_ITEM_ID]["EFFICIENCY"] = $vals->EFFICIENCY;
			$gsd_array[$vals->JOB_NO][$vals->STYLE_REF][$vals->GMTS_ITEM_ID]["ALLOCATED_MP"] = $vals->ALLOCATED_MP;
			$gsd_array[$vals->JOB_NO][$vals->STYLE_REF][$vals->GMTS_ITEM_ID]["TARGET"] = $vals->TARGET;
			$gsd_array[$vals->JOB_NO][$vals->STYLE_REF][$vals->GMTS_ITEM_ID]["WORKING_HOUR"] = $vals->WORKING_HOUR;
		}
		//end gsd.................................................................

		//print_r($gsd_array);die;
		$by_pass = return_field_value("WORK_STUDY_INTEGRATED", "variable_settings_production", "company_name='$cbo_company_mst' and variable_list=9", "WORK_STUDY_INTEGRATED");

		$company = " and a.company_name='$cbo_company_mst'";
		if ($cbo_buyer_name > 0) {
			$buyer = " and a.buyer_name='$cbo_buyer_name'";
		} else {
			$buyer = "";
		}

		$order_cond = "";
		if (trim($txt_order_search)) {
			if ($search_types == 1) {
				$order_cond = " and b.po_number = '$txt_order_search'  ";
			} else if ($search_types == 2) {
				$order_cond = " and b.po_number like '%$txt_order_search%'  ";
			} else if ($search_types == 3) {
				$order_cond = " and b.po_number like '%$txt_order_search'  ";
			} else if ($search_types == 4) {
				$order_cond = " and b.po_number like '%$txt_order_search%'  ";
			}

		} else {
			$order_cond = "";
		}

		if (trim($po_break_down_id)) {
			$order_cond = " and b.id in( $po_break_down_id )";

		}
		if (trim($color_size_id)) {
			$color_size_cond = " and c.id in( $color_size_id )";
		} else {
			$color_size_cond = "";
		}
		if (trim($set_dtls_id)) {
			$set_cond = " and c.id in( $set_dtls_id )";
		} else {
			$set_cond = "";
		}

		$style_cond = "";
		if (trim($txt_style_ref)) {
			if ($search_types == 1) {
				$style_cond = " and a.style_ref_no = '$txt_style_ref'  ";
			} else if ($search_types == 2) {
				$style_cond = " and a.style_ref_no like '%$txt_style_ref%'  ";
			} else if ($search_types == 3) {
				$style_cond = " and a.style_ref_no like '%$txt_style_ref'  ";
			} else if ($search_types == 4) {
				$style_cond = " and a.style_ref_no like '%$txt_style_ref%'  ";
			}

		} else {
			$style_cond = "";
		}

		$job_cond = '';
		$tna_job = "";

		if ($txt_job_prifix) {
			$job_cond = " and a.job_no like '%" . $txt_job_prifix . "%' "; //else  $job_cond="";
			$tna_job = " and job_no like '%" . $txt_job_prifix . "%'";
		}

		$shipment_date = "";
		$tna_date_cond = '';
		if ($cbo_date_type != 1) {
			// Shipment Date
			if ($txt_date_from && $txt_date_to) {
				if ($this->db->dbdriver == 'mysqli') {
					$tna_date_cond = "and shipment_date between '" . date("Y-m-d", strtotime($txt_date_from)) . "' and '" . date("Y-m-d", strtotime($txt_date_to)) . "'";
					$shipment_date = "and b.pub_shipment_date between '" . date("Y-m-d", strtotime($txt_date_from)) . "' and '" . date("Y-m-d", strtotime($txt_date_to)) . "'";
				} else {
					$tna_date_cond = "and shipment_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
					$shipment_date = "and b.pub_shipment_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
				}
			} else {
				$tna_date_cond = "";
				$shipment_date = '';
			}
		} else {
			if ($txt_date_from && $txt_date_to) {
				if ($this->db->dbdriver == 'mysqli') {
					$tna_date_cond = "and task_start_date between '" . date("Y-m-d", strtotime($txt_date_from)) . "' and '" . date("Y-m-d", strtotime($txt_date_to)) . "'";
				} else {
					$tna_date_cond = "and task_start_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
				}
			} else {
				$tna_date_cond = "";
			}

		}

		//return $txt_order_search;

		if ($txt_job_prifix || $txt_order_search || $txt_style_ref) {
			$tna_date_cond = "";
			$shipment_date = '';
		}
		$shipment_date2 = $shipment_date;
		if ($plan_level == 5 && $shipment_date) {
			$shipment_date2 = str_replace("b.pub_shipment_date", "c.country_ship_date ", $shipment_date);
		}
		$all_jobs_by_style_arr = array();
		$all_jobs_by_style_st = "";
		$conds = "";
		if ($txt_style_ref) {
			$conds .= " and  style_ref_no like '%$txt_style_ref%'";
		}

		if ($txt_order_search) {
			$conds .= " and  po_number like '%$txt_order_search%'";
		}

		if (trim($po_break_down_id)) {$conds .= " and b.id in( $po_break_down_id )";}

		$jobs_sqls = $this->db->query("SELECT a.JOB_NO from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active=1  $conds group by a.job_no ")->result();
		foreach ($jobs_sqls as $values) {
			$all_jobs_by_style_arr[$values->JOB_NO] = $values->JOB_NO;
		}
		if (count($all_jobs_by_style_arr)) {
			$all_jobs_by_style_st = "'" . implode("','", $all_jobs_by_style_arr) . "'";
		}
		$all_jobs_by_style_cond = "";
		if ($all_jobs_by_style_st) {
			$all_jobs_by_style_cond .= " and job_no in($all_jobs_by_style_st)";
		}

		if (count($all_jobs_by_style_arr) > 999) {
			$all_jobs_by_style_cond = "";
			$chnk = array_chunk($all_jobs_by_style_arr, 999);

			foreach ($chnk as $v) {
				$jobs = "'" . implode("','", $v) . "'";
				if ($all_jobs_by_style_cond == "") {
					$all_jobs_by_style_cond .= " and (job_no in($jobs)";
				} else {
					$all_jobs_by_style_cond .= " or  job_no in($jobs)";
				}

			}
			$all_jobs_by_style_cond .= ")";

		}
		//return $tna_date_cond;

		if (trim($po_break_down_id)) {
			$tna_were_con = " and po_number_id in( $po_break_down_id )";
		} else {
			$tna_were_con = "";
		}

		$sql = $this->db->query("select min(task_start_date) as TASK_START_DATE,max(task_finish_date) as TASK_FINISH_DATE,PO_NUMBER_ID,JOB_NO from tna_process_mst where is_deleted=0 and status_active=1 $all_jobs_by_style_cond $tna_job $tna_date_cond $tna_were_con and task_number in(86,190,191) group by po_number_id,JOB_NO");

		$sel_pos = "";
		$jobs = '';
		$sel_jobs_arr = "";
		$sel_jobs = array();
		foreach ($sql->result() as $srows) {
			$tna_task_data[$srows->PO_NUMBER_ID]['task_start_date'] = date("d-m-Y", strtotime($srows->TASK_START_DATE));
			$tna_task_data[$srows->PO_NUMBER_ID]['task_finish_date'] = date("d-m-Y", strtotime($srows->TASK_FINISH_DATE));
			if ($sel_pos == "") {
				$sel_pos = $srows->PO_NUMBER_ID;
				$sel_jobs_arr = $srows->JOB_NO;
			} else {
				$sel_pos .= "," . $srows->PO_NUMBER_ID;
				$sel_jobs_arr .= "," . $srows->JOB_NO;
			}
		}
		$sel_jobs = explode(",", $sel_jobs_arr);
		if ($sel_pos == "" && $ignore_tna == 0) {
			return $data_array;
			die;
		}

		// FOR ORACLE
		$sel_pos2 = array_chunk(array_unique(explode(",", $sel_pos)), 900);
		$sql = "SELECT b.PLAN_QNTY,b.PO_BREAK_DOWN_ID,b.ITEM_NUMBER_ID,b.COLOR_NUMBER_ID from ppl_sewing_plan_board a, ppl_sewing_plan_board_powise b where a.plan_id=b.plan_id and a.is_deleted=0 and a.status_active=1 ";

		if ($ignore_tna == 0) {
			$p = 1;
			$prod_cond = " and (";
			foreach ($sel_pos2 as $job_no_process) {
				$ids = implode(',', $job_no_process);
				if ($p == 1) {

					$sql .= " and (b.po_break_down_id in(" . implode(',', $job_no_process) . ")";
					$prod_cond .= "   po_break_down_id in($ids)";
				} else {

					$sql .= " or b.po_break_down_id in(" . implode(',', $job_no_process) . ")";
					$prod_cond .= " or  po_break_down_id in($ids)";
				}
				$p++;
			}
			$sql .= ")";
			$prod_cond .= ")";

		}

		//return $sql;

		$sql = $this->db->query($sql);
		$planned_qnty = array();
		if ($sql->num_rows() > 0) {

			foreach ($sql->result() as $srows) {
				if (!$srows->COLOR_NUMBER_ID) {
					$srows->COLOR_NUMBER_ID = 0;
				}

				if (isset($planned_qnty[$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID][$srows->COLOR_NUMBER_ID])) {
					$planned_qnty[$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID][$srows->COLOR_NUMBER_ID] += $srows->PLAN_QNTY;
				} else {
					$planned_qnty[$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID][$srows->COLOR_NUMBER_ID] = $srows->PLAN_QNTY;
				}
			}
		}
		// print_r($planned_qnty);die;
		// return $planned_qnty;

		$com_res = $this->db->query("select ID,COMPANY_NAME from lib_company comp where status_active =1 and is_deleted=0  order by company_name")->result();
		$buyer_res = $this->db->query("select a.ID,a.BUYER_NAME from  lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id  and a.status_active=1 and a.is_deleted=0")->result();
		$garment_res = $this->db->query("select ID,ITEM_NAME from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name")->result();

		foreach ($com_res as $value) {
			$comp[$value->ID] = $value->COMPANY_NAME;
		}
		foreach ($buyer_res as $value) {
			$buyer_arr[$value->ID] = $value->BUYER_NAME;
		}
		foreach ($garment_res as $value) {
			$garments_item[$value->ID] = $value->ITEM_NAME;
		}

		$garments_nature = 2;

		if ($ignore_tna == 1) {
			$prod_cond = "";
		}

		$sql = "select PO_BREAK_DOWN_ID,sum(production_quantity) as PRODUCTION_QUANTITY from   pro_garments_production_mst where production_type=5 $prod_cond and status_active=1 and is_deleted=0   group by po_break_down_id";

		$sql_data = $this->db->query($sql)->result();
		$k = 0;

		foreach ($sql_data as $rows) {
			$production_details[$rows->PO_BREAK_DOWN_ID] = $rows->PRODUCTION_QUANTITY;
		}

		//Oracle queary
		//Oracle queary
		$chunk = array_chunk($sel_jobs, 999);
		if ($ignore_tna == 0) {
			$jobs_cond = "";
			foreach ($chunk as $vals) {
				$jobs_id = "'" . implode("','", $vals) . "'";
				if ($jobs_cond == "") {
					$jobs_cond .= "    JOB_NO in ($jobs_id) ";
				} else {
					$jobs_cond .= " or  JOB_NO in ($jobs_id) ";
				}

			}

		} else {
			$jobs_cond = " 1=1";
		}

		if (trim($set_dtls_id)) {
			$jobs_cond .= " and id in( $set_dtls_id )";
		}

		$set_re = $this->db->query("SELECT ID,GMTS_ITEM_ID,SMV_PCS,COMPLEXITY,QUOT_ID,JOB_NO from  WO_PO_DETAILS_MAS_SET_DETAILS where $jobs_cond ")->result();

		$job_set_data = array();
		$quot_ids = array();
		foreach ($set_re as $value) {
			$job_set_data[$value->JOB_NO][$value->GMTS_ITEM_ID]['SMV_PCS'] = $value->SMV_PCS;
			$job_set_data[$value->JOB_NO][$value->GMTS_ITEM_ID]['COMPLEXITY'] = $value->COMPLEXITY;
			$job_set_data[$value->JOB_NO][$value->GMTS_ITEM_ID]['QUOT_ID'] = $value->QUOT_ID;
			//$job_set_data[$value->JOB_NO][$value->GMTS_ITEM_ID]['SMV_PCS'] = $value->SMV_PCS;
			$quot_ids[$value->QUOT_ID] = $value->QUOT_ID;
		}

		$sel_pos2 = array_chunk(array_unique(explode(",", $sel_pos)), 999);

		$sql = " select ID,DAY_TARGET,WORKING_HOUR,TOTAL_SMV,GMTS_ITEM_ID from  ppl_gsd_entry_mst ";
		// Get Efficiecny %; WORKING_HOUR=1; target per hour,
		$sql = $this->db->query($sql)->result();
		$day_target = array();
		foreach ($sql as $srows) {
			$day_target[$srows->ID][$srows->GMTS_ITEM_ID]['DAY_TARGET'] = $srows->DAY_TARGET;
			$day_target[$srows->ID][$srows->GMTS_ITEM_ID]['WORKING_HOUR'] = $srows->WORKING_HOUR;
			$day_target[$srows->ID][$srows->GMTS_ITEM_ID]['TOTAL_SMV'] = $srows->TOTAL_SMV;
		}

		$country_sql = "select ID,COUNTRY_NAME from  LIB_COUNTRY";
		$countries = $this->db->query($country_sql)->result();
		$country_arr = array();
		foreach ($countries as $crows) {
			$country_arr[$crows->ID] = $crows->COUNTRY_NAME;
		}

		$str_shi = '';

		$sel_pos2 = array_chunk(array_unique(explode(",", $sel_pos)), 999);

		if ($plan_level == 1) {
			if ($this->db->dbdriver == 'mysqli') {
				$sql = "SELECT '' as COLOR_SIZE_ID,b.IS_CONFIRMED,a.JOB_NO_PREFIX_NUM, a.JOB_NO,a.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_QUANTITY,a.GARMENTS_NATURE, b.ID PO_BREAK_DOWN_ID,b.PO_NUMBER, b.PO_QUANTITY,b.SHIPMENT_DATE AS SHIPMENT_DATE,b.PUB_SHIPMENT_DATE AS PUB_SHIPMENT_DATE, b.PLAN_CUT, YEAR(a.insert_date) as YEAR,b.ID,SET_BREAK_DOWN,TOTAL_SET_QNTY, SET_SMV,c.ID AS SET_DTLS_ID ,c.GMTS_ITEM_ID,c.SET_ITEM_RATIO,c.SMV_PCS from wo_po_details_master a, wo_po_break_down b,wo_po_details_mas_set_details c where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.job_no_mst=c.job_no and a.status_active=1 and b.status_active=1 $str_shi $company $buyer $job_cond $order_cond $shipment_date $set_cond $style_cond $is_confirmed_cond";
			} else {
				$sql = "SELECT '' as COLOR_SIZE_ID,b.IS_CONFIRMED,a.JOB_NO_PREFIX_NUM, a.JOB_NO,a.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_QUANTITY,a.GARMENTS_NATURE, b.ID PO_BREAK_DOWN_ID,b.PO_NUMBER, b.PO_QUANTITY,b.SHIPMENT_DATE AS SHIPMENT_DATE,b.PUB_SHIPMENT_DATE AS PUB_SHIPMENT_DATE, b.PLAN_CUT, to_char(a.insert_date,'YYYY') as YEAR,b.ID,SET_BREAK_DOWN,TOTAL_SET_QNTY, SET_SMV,c.ID AS SET_DTLS_ID ,c.GMTS_ITEM_ID,c.SET_ITEM_RATIO,c.SMV_PCS from wo_po_details_master a, wo_po_break_down b,wo_po_details_mas_set_details c where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.job_no_mst=c.job_no and a.status_active=1 and b.status_active=1 $str_shi $company $buyer $job_cond $set_cond $order_cond $shipment_date $style_cond $is_confirmed_cond";
			}

		} else {
			if ($plan_level == 2) {
				$fields = ",c.COLOR_NUMBER_ID";
				$group_by = ",c.color_number_id";
			} else if ($plan_level == 3) {
				$fields = ",c.SIZE_NUMBER_ID";
				$group_by = ",c.size_number_id";
			} else if ($plan_level == 4) {
				$fields = ",c.COLOR_NUMBER_ID,c.SIZE_NUMBER_ID";
				$group_by = ",c.color_number_id,c.size_number_id";
			} else if ($plan_level == 5) {
				$fields = ",c.COUNTRY_ID";
				$group_by = ",c.COUNTRY_ID";
			} else if ($plan_level == 6) {
				$fields = ",c.COUNTRY_ID,c.COLOR_NUMBER_ID";
				$group_by = ",c.country_id,c.color_number_id";
			} else if ($plan_level == 7) {
				$fields = ",c.COUNTRY_ID,c.COLOR_NUMBER_ID,c.SIZE_NUMBER_ID";
				$group_by = ",c.country_id,c.color_number_id,c.size_number_id";
			} else if ($plan_level == 8) {
				$fields = ",c.COUNTRY_ID,c.SIZE_NUMBER_ID";
				$group_by = ",c.country_id,c.size_number_id";
			}

			if (trim($set_dtls_id)) {
				$set_cond = " and d.id in( $set_dtls_id )";
			} else {
				$set_cond = "";
			}

			if ($this->db->dbdriver == 'mysqli') {
				$sql = "SELECT group_concat(c.id) as COLOR_SIZE_ID,c.item_number_id,b.IS_CONFIRMED,a.JOB_NO_PREFIX_NUM, a.JOB_NO,A.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_QUANTITY,a.GARMENTS_NATURE,b.ID PO_BREAK_DOWN_ID,b.PO_NUMBER,b.SHIPMENT_DATE AS SHIPMENT_DATE,c.COUNTRY_SHIP_DATE AS PUB_SHIPMENT_DATE,sum(c.PLAN_CUT_QNTY) as PLAN_CUT,YEAR(a.insert_date) as YEAR,b.ID,SET_BREAK_DOWN,TOTAL_SET_QNTY,SET_SMV, sum(c.plan_cut_qnty) PLAN_CUT_QNTY,sum(c.order_quantity ) po_quantity,d.ID AS SET_DTLS_ID ,d.GMTS_ITEM_ID,d.SET_ITEM_RATIO,d.SMV_PCS $fields from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,wo_po_details_mas_set_details d where a.job_no=b.job_no_mst and a.job_no=d.job_no and d.job_no=b.job_no_mst and b.id=c.po_break_down_id  and a.status_active=1 $color_size_cond  and b.status_active=1 and c.status_active=1 $str_shi $company $buyer $job_cond $set_cond $order_cond $shipment_date2 $style_cond $is_confirmed_cond";
			} else {
				$sql = "SELECT listagg(c.id,',') within group (order by c.id) as COLOR_SIZE_ID,c.item_number_id,b.IS_CONFIRMED,a.JOB_NO_PREFIX_NUM, a.JOB_NO,A.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_QUANTITY,a.GARMENTS_NATURE,b.ID PO_BREAK_DOWN_ID,b.PO_NUMBER,b.SHIPMENT_DATE AS SHIPMENT_DATE,c.COUNTRY_SHIP_DATE AS PUB_SHIPMENT_DATE, sum(c.PLAN_CUT_QNTY) as PLAN_CUT,to_char(a.insert_date,'YYYY') as YEAR,b.ID,SET_BREAK_DOWN,TOTAL_SET_QNTY,SET_SMV, sum(c.plan_cut_qnty) PLAN_CUT_QNTY,sum(c.order_quantity ) po_quantity,d.ID AS SET_DTLS_ID ,d.GMTS_ITEM_ID,d.SET_ITEM_RATIO,d.SMV_PCS $fields from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,wo_po_details_mas_set_details d where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=d.job_no and d.job_no=b.job_no_mst and a.status_active=1 $color_size_cond and b.status_active=1 and c.status_active=1 $str_shi $company $buyer $job_cond $set_cond $order_cond $shipment_date2 $style_cond $is_confirmed_cond";
			}
		}
		//return  $sql;

		if ($ignore_tna == 0) {
			$p = 1;
			foreach ($sel_pos2 as $job_no_process) {
				if ($p == 1) {
					$sql .= " and (b.id in(" . implode(',', $job_no_process) . ")";
				} else {
					$sql .= " or b.id in(" . implode(',', $job_no_process) . ")";
				}

				$p++;

			}
			$sql .= ")";

		}

		if ($plan_level != 1) {
			$sql .= " group by c.item_number_id, b.is_confirmed,a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name, a.style_ref_no,a.job_quantity, b.id,b.po_number,b.shipment_date,c.country_ship_date,a.garments_nature,a.insert_date,b.id, set_break_down,total_set_qnty,set_smv,d.id,d.gmts_item_id,d.set_item_ratio,d.smv_pcs $group_by";
		}
		$sql .= " order by a.job_no,b.id, b.shipment_date ";
		//return $sql;
		$sql_exe = $this->db->query($sql)->result();
		$i = 0;

		$production_data_arr = $this->get_production_qnty_by_po_item($sel_pos, '', '', 1);
		$is_confirmed_arr = array(0 => "", 1 => "Confirmed", 2 => "Projected", "" => "");

		foreach ($sql_exe as $rows) {
			
			//allocation.............start;
			$FROM_PO_QTY=$allocationFromArr[PO][$cbo_company_mst][$rows->JOB_NO][$rows->PO_BREAK_DOWN_ID];
			$To_PO_QTY=$allocationToArr[PO][$cbo_company_mst][$rows->JOB_NO][$rows->PO_BREAK_DOWN_ID];
			
			$FROM_JOB_QTY=$allocationFromArr[JOB][$cbo_company_mst][$rows->JOB_NO];
			$To_JOB_QTY=$allocationToArr[JOB][$cbo_company_mst][$rows->JOB_NO];
			
			$FROM_ITEM_QTY=$allocationFromArr[ITEM][$cbo_company_mst][$rows->JOB_NO][$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID];
			$To_ITEM_QTY=$allocationToArr[ITEM][$cbo_company_mst][$rows->JOB_NO][$rows->PO_BREAK_DOWN_ID][$rows->GMTS_ITEM_ID];
			$PO_QTY=$To_PO_QTY-$FROM_PO_QTY;
			$JOB_QTY=$To_JOB_QTY-$FROM_JOB_QTY;
			$ITEM_QTY=$To_ITEM_QTY-$FROM_ITEM_QTY;
			//allocation.............end;

			$setdata[0] = $rows->GMTS_ITEM_ID;
			$setdata[1] = $rows->SET_ITEM_RATIO;
			$setdata[2] = $rows->SMV_PCS;

			$data_array[$i]["ID"] = $rows->ID;
			$data_array[$i]["JOB_NO"] = $rows->JOB_NO;
			$data_array[$i]["YEAR"] = $rows->YEAR;
			if (isset($buyer_arr[$rows->BUYER_NAME])) {
				$data_array[$i]["BUYER_NAME"] = $buyer_arr[$rows->BUYER_NAME];
			} else {
				$data_array[$i]["BUYER_NAME"] = 0;
			}

			$data_array[$i]["BUYER_ID"] = $rows->BUYER_NAME;

			$data_array[$i]["STYLE_REF_NO"] = $rows->STYLE_REF_NO;
			$data_array[$i]["JOB_QUANTITY"] = $rows->JOB_QUANTITY + $JOB_QTY;
			$data_array[$i]["PO_NUMBER"] = $rows->PO_NUMBER;
			$data_array[$i]["PO_BREAK_DOWN_ID"] = $rows->PO_BREAK_DOWN_ID;
			$data_array[$i]["PO_QUANTITY"] = ($rows->PO_QUANTITY / $rows->TOTAL_SET_QNTY) + $PO_QTY;
			if (isset($is_confirmed_arr[$rows->IS_CONFIRMED])) {
				$data_array[$i]["ORDER_STATUS"] = $is_confirmed_arr[$rows->IS_CONFIRMED];
			} else {
				$data_array[$i]["ORDER_STATUS"] = "";
			}

			$data_array[$i]["ITEM_NAME"] = (!empty($garments_item[$setdata[0]])) ? $garments_item[$setdata[0]] : "";
			$data_array[$i]["ITEM_NUMBER_ID"] = (!empty($setdata[0])) ? $setdata[0] : "";
			$data_array[$i]["ITEM_QNTY"] = ($setdata[1] * $rows->PLAN_CUT) + $ITEM_QTY;

			if (isset($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date'])) {
				$data_array[$i]["TNA_START_DATE"] = $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date'];
			} else {
				$data_array[$i]["TNA_START_DATE"] = "01-01-1970";
			}

			if (isset($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date'])) {
				$data_array[$i]["TNA_FINISH_DATE"] = $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date'];
			} else {
				$data_array[$i]["TNA_FINISH_DATE"] = "01-01-1970";
			}

			if (isset($production_data_arr[$rows->PO_BREAK_DOWN_ID][$setdata[0]])) {
				$data_array[$i]["PRODUCTION_QNTY"] = $production_data_arr[$rows->PO_BREAK_DOWN_ID][$setdata[0]];
			}

			$color_index = 0;
			if ($plan_level == 2) {
				$color_index = $rows->COLOR_NUMBER_ID;
			}

			if (isset($planned_qnty[$rows->ID][$setdata[0]][$color_index])) {
				$plan_qnty = $planned_qnty[$rows->ID][$setdata[0]][$color_index];
			} else {
				$plan_qnty = 0;
			}

			$data_array[$i]["PLAN_QNTY"] = $plan_qnty;

			$data_array[$i]["YET_TO_PLAN"] = (($setdata[1] * $rows->PLAN_CUT)+$ITEM_QTY) - $plan_qnty * 1;

			$data_array[$i]["SMV"] = $setdata[2];
			$data_array[$i]["PUB_SHIPMENT_DATE"] = date("d-m-Y", strtotime($rows->PUB_SHIPMENT_DATE));
			if ($plan_level == 2) {
				$color_data = $this->db->query("select color_name from lib_color where id=$rows->COLOR_NUMBER_ID")->row();
				if (isset($color_data->COLOR_NAME)) {
					$data_array[$i]["COLOR_NAME"] = $color_data->COLOR_NAME;
				} else {
					$data_array[$i]["COLOR_NAME"] = "";
				}

				$data_array[$i]["COLOR_NUMBER_ID"] = $rows->COLOR_NUMBER_ID;
			} else if ($plan_level == 3) {
				$size_data = $this->db->query("select SIZE_NAME from lib_size where id=$rows->SIZE_NUMBER_ID")->row();
				if (isset($size_data->SIZE_NAME)) {
					$data_array[$i]["SIZE_NAME"] = $size_data->SIZE_NAME;
				} else {
					$data_array[$i]["SIZE_NAME"] = "";
				}

				$data_array[$i]["SIZE_NUMBER_ID"] = $rows->SIZE_NUMBER_ID;
			} else if ($plan_level == 4) {
				$color_data = $this->db->query("select color_name from lib_color where id=$rows->COLOR_NUMBER_ID")->row();
				$size_data = $this->db->query("select SIZE_NAME from lib_size where id=$rows->SIZE_NUMBER_ID")->row();
				if (isset($color_data->COLOR_NAME)) {
					$data_array[$i]["COLOR_NAME"] = $color_data->COLOR_NAME;
				}

				$data_array[$i]["COLOR_NAME"] = "";
				$data_array[$i]["COLOR_NUMBER_ID"] = $rows->COLOR_NUMBER_ID;
				if (isset($size_data->SIZE_NAME)) {
					$data_array[$i]["SIZE_NAME"] = $size_data->SIZE_NAME;
				} else {
					$data_array[$i]["SIZE_NAME"] = "";
				}

				$data_array[$i]["SIZE_NUMBER_ID"] = $rows->SIZE_NUMBER_ID;
			} else if ($plan_level == 5) {
				$data_array[$i]["COUNTRY_ID"] = $rows->COUNTRY_ID;
				$data_array[$i]["COUNTRY_NAME"] = $country_arr[$rows->COUNTRY_ID];
			} else if ($plan_level == 6) {
				$data_array[$i]["COUNTRY_ID"] = $rows->COUNTRY_ID;
				$data_array[$i]["COLOR_NUMBER_ID"] = $rows->COLOR_NUMBER_ID;
			} else if ($plan_level == 7) {
				$data_array[$i]["COUNTRY_ID"] = $rows->COUNTRY_ID;
				$data_array[$i]["COLOR_NUMBER_ID"] = $rows->COLOR_NUMBER_ID;
				$data_array[$i]["SIZE_NUMBER_ID"] = $rows->SIZE_NUMBER_ID;
			} else if ($plan_level == 8) {
				$data_array[$i]["COUNTRY_ID"] = $rows->COUNTRY_ID;
				$data_array[$i]["SIZE_NUMBER_ID"] = $rows->SIZE_NUMBER_ID;
			}

			$data_array[$i]["ORDER_COMPLEXITY"] = '1'; // = '1';//$rows->ORDER_COMPLEXITY; by Learning  Curve or fixed method
			$style_id = $data_array[$i]["STYLE_REF_NO"];
			$item_id = $data_array[$i]["ITEM_NUMBER_ID"];
			$smv_qty_id = $data_array[$i]["SMV"];
			$order_qty_id = $data_array[$i]["PO_QUANTITY"];
			if (isset($gsd_array[$rows->JOB_NO][$style_id][$item_id]["EFFICIENCY"])) {
				$data_array[$i]["EFFICIENCY"] = $gsd_array[$rows->JOB_NO][$style_id][$item_id]["EFFICIENCY"];
			} else {
				$data_array[$i]["EFFICIENCY"] = "0";
			}

			if (isset($gsd_array[$rows->JOB_NO][$style_id][$item_id]["TARGET"])) {
				$data_array[$i]["TARGET"] = $gsd_array[$rows->JOB_NO][$style_id][$item_id]["TARGET"];
			} else {
				$data_array[$i]["TARGET"] = "0";
			}

			if (isset($gsd_array[$rows->JOB_NO][$style_id][$item_id]["ALLOCATED_MP"])) {
				$data_array[$i]["ALLOCATED_MP"] = $gsd_array[$rows->JOB_NO][$style_id][$item_id]["ALLOCATED_MP"];
			} else {
				$data_array[$i]["ALLOCATED_MP"] = "0";
			}

			if (isset($gsd_array[$rows->JOB_NO][$style_id][$item_id]["SMV"])) {
				$data_array[$i]["GSD_SMV"] = $gsd_array[$rows->JOB_NO][$style_id][$item_id]["SMV"];
			} else {
				$data_array[$i]["GSD_SMV"] = "0";
			}

			$gsd_smv_val = $data_array[$i]["GSD_SMV"];
			if (isset($gsd_array[$rows->JOB_NO][$style_id][$item_id]["WORKING_HOUR"])) {
				$data_array[$i]["WORKING_HOUR"] = $gsd_array[$rows->JOB_NO][$style_id][$item_id]["WORKING_HOUR"];
			} else {
				$data_array[$i]["WORKING_HOUR"] = "0";
			}

			if (!$order_qty_id) {
				$order_qty_id = 0;
			}

			if (!$gsd_smv_val) {
				$gsd_smv_val = 0;
			}

			$slab = return_field_value("LEARNING_CUB_PERCENTAGE", "efficiency_percentage_slab", "company_id=$cbo_company_mst and status_active=1 and (SMV_LOWER_LIMIT<=$gsd_smv_val and SMV_UPPER_LIMIT>=$gsd_smv_val) and (ORDER_QTY_LOWER_LIMIT<= $order_qty_id and ORDER_QTY_UPPER_LIMIT>= $order_qty_id) ", "LEARNING_CUB_PERCENTAGE");

			$data_array[$i]["COMPLEXITY_LEVEL"] = '4'; // = '1';//$rows->COMPLEXITY_LEVEL; fancy, critical,
			//$data_array[$i]["FIRST_DAY_OUTPUT"]    ='50,60,80'; // = '50,60,80';//$rows->FIRST_DAY_OUTPUT;
			$data_array[$i]["FIRST_DAY_OUTPUT"] = $slab; // = '50,60,80';//$rows->FIRST_DAY_OUTPUT;
			$data_array[$i]["INCREMENT"] = '100'; // ='100';// $rows->INCREMENT;
			//$data_array[$i]["TERGET"]    ='2000'; // = '2000';//$rows->TERGET;
			//$data_array[$i]["BY_PASS"]    =$by_pass; // = '2000';//$rows->TERGET;

			$data_array[$i]["COLOR_SIZE_ID"] = $rows->COLOR_SIZE_ID;
			$data_array[$i]["SET_DTLS_ID"] = $rows->SET_DTLS_ID;

			$i++;

			//}
		}
		return $data_array;
	}

	function get_production_qnty_by_po_item($po_ids, $lineid = '', $daterange = '', $array_type = 1) {
		$sel_pos2 = array_chunk(array_unique(explode(",", $po_ids)), 999);
		$p = 1;
		$po_id_cond = "";
		foreach ($sel_pos2 as $job_no_process) {
			$values = implode(',', $job_no_process);
			if (!$values) {
				$values = 0;
			}

			if ($p == 1) {
				$po_id_cond = " and (a.po_break_down_id in(" . $values . ")";
			} else {
				$po_id_cond .= " or a.po_break_down_id in (" . $values . ")";
			}

			$p++;
		}
		if ($this->db->dbdriver == 'mysqli') {
			$get_actual_resource_allocation = "select group_concat(id) as ids from prod_resource_mst where  line_number like '$lineid' and is_deleted=0";

		} else {
			$get_actual_resource_allocation = "select listagg(id,',') within group (order by id) as ids from prod_resource_mst where  line_number like '$lineid' and is_deleted=0";
		}

		$resource_id = $this->db->query($get_actual_resource_allocation)->row();
		$lineid = $resource_id->IDS;

		$po_id_cond .= ")";
		$line_cond = '';
		$date_cond = '';

		if ($lineid != 0) {
			$line_cond = " and a.SEWING_LINE in ($lineid) ";
		}

		if ($daterange != '') {
			$date_cond = " and a.PRODUCTION_DATE between $daterange ";
		}

		$production_data_arr = array();
		$production_sql = "select a.PO_BREAK_DOWN_ID,a.ITEM_NUMBER_ID,b.COLOR_SIZE_BREAK_DOWN_ID,b.PRODUCTION_QNTY PRODUCTION_QUANTITY, a.SEWING_LINE SEWING_LINE,a.PRODUCTION_DATE PRODUCTION_DATE from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=5 $po_id_cond and a.status_active=1 and a.is_deleted=0 $line_cond $date_cond order by a.PRODUCTION_DATE ASC";
		$production_data = $this->db->query($production_sql)->result();
		if ($array_type == 1) // PO ITEM level
		{
			foreach ($production_data as $row) {
				if (isset($production_data_arr[$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID])) {
					$production_data_arr[$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID] += $row->PRODUCTION_QUANTITY;
				} else {
					$production_data_arr[$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID] = $row->PRODUCTION_QUANTITY;
				}
			}
			return $production_data_arr;
		} else if ($array_type == 2) // line date level
		{
			foreach ($production_data as $row) {
				if (isset($production_data_arr[$row->SEWING_LINE][$row->PRODUCTION_DATE])) {
					$production_data_arr[$row->SEWING_LINE][$row->PRODUCTION_DATE] += $row->PRODUCTION_QUANTITY;
				} else {
					$production_data_arr[$row->SEWING_LINE][$row->PRODUCTION_DATE] = $row->PRODUCTION_QUANTITY;
				}
			}
			return $production_data_arr;
		} else if ($array_type == 3) // line date level
		{
			$production_data_qnty = 0;
			$prd_date = "";
			foreach ($production_data as $row) {

				$production_data_qnty += $row->PRODUCTION_QUANTITY;
				$prd_date = $row->PRODUCTION_DATE;
			}
			$production_data_arr = array("production_data_qnty" => ($production_data_qnty), "prd_date" => $prd_date);
			return $production_data_arr;
		}

	}

	function get_production_qnty_info_by_plan_level($company_id, $job_no, $po_id, $item_id, $plan_level, $color_num_id, $size_num_id, $resource_allocation_type, $line_id, $line_names_ids, $line_allocated) {
		$company_cond = " and company_id='$company_id'";
		$po_id_cond = "";
		if ($po_id != "") {
			$po_id_cond = " and po_break_down_id='$po_id'";
		}
		$color_size_sql = "select ID,JOB_NO_MST,PO_BREAK_DOWN_ID,ITEM_NUMBER_ID,COLOR_NUMBER_ID,SIZE_NUMBER_ID,COUNTRY_ID from wo_po_color_size_breakdown where po_break_down_id='$po_id'";
		$color_size_data = $this->db->query($color_size_sql)->result();
		$color_size_data_arr = array();
		foreach ($color_size_data as $row) {
			$color_size_data_arr[$row->JOB_NO_MST][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID][] = $row->ID;
			/*if($this->db->dbdriver=='mysqli'){
				                $color_size_data_arr[$row->job_no_mst][$row->po_break_down_id][$row->item_number_id][$row->color_number_id][$row->size_number_id][]=$row->id;
				                }else{
				                $color_size_data_arr[$row->JOB_NO_MST][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID][]=$row->ID;
			*/
		}
		$production_data_arr = array();
		if ($resource_allocation_type != 1) {
			$production_sql = "select a.ID,a.PO_BREAK_DOWN_ID,a.PRODUCTION_DATE,a.SEWING_LINE,a.COMPANY_ID,a.LOCATION,b.COLOR_SIZE_BREAK_DOWN_ID,sum(b.production_qnty) PRODUCTION_QUANTITY from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=5 $po_id_cond and a.status_active=1 and a.is_deleted=0   and a.sewing_line in (" . implode(",", $line_names_ids) . ") group by a.id,a.production_date,a.po_break_down_id, a.sewing_line,a.company_id,a.location,b.color_size_break_down_id order by a.sewing_line,a.po_break_down_id, a.production_date";
			$production_data = $this->db->query($production_sql)->result();
			foreach ($production_data as $row) {
				$production_data_arr[$row->SEWING_LINE][$row->COLOR_SIZE_BREAK_DOWN_ID] = $row->PRODUCTION_QUANTITY;
				/*if($this->db->dbdriver=='mysqli'){
					                $production_data_arr[$row->sewing_line][$row->color_size_break_down_id]=$row->production_quantity;
					            }else{
					                $production_data_arr[$row->SEWING_LINE][$row->color_size_break_down_id]=$row->PRODUCTION_QUANTITY;
				*/
			}
		} else {
			$production_sql = "select a.ID,a.PO_BREAK_DOWN_ID,a.PRODUCTION_DATE,a.SEWING_LINE,a.COMPANY_ID,a.LOCATION,b.COLOR_SIZE_BREAK_DOWN_ID,SUM(b.production_qnty) PRODUCTION_QUANTITY from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=5 $po_id_cond and a.status_active=1 and a.is_deleted=0 and a.sewing_line in (" . implode(",", $line_allocated) . ") group by a.id,a.production_date,a.po_break_down_id, a.sewing_line,a.company_id,a.location,b.color_size_break_down_id order by a.sewing_line,a.po_break_down_id, a.production_date";
			$production_data = $this->db->query($production_sql)->result();
			foreach ($production_data as $row) {
				$production_data_arr[$row->SEWING_LINE][$row->COLOR_SIZE_BREAK_DOWN_ID] = $row->PRODUCTION_QUANTITY;
				/*if($this->db->dbdriver=='mysqli'){
					                $production_data_arr[$row->sewing_line][$row->color_size_break_down_id] = $row->production_quantity;
					                }else{
					                $production_data_arr[$row->SEWING_LINE][$row->COLOR_SIZE_BREAK_DOWN_ID] = $row->PRODUCTION_QUANTITY;
				*/
			}
		}

		$production_qnty = 0;
		if ($plan_level == 2) {
			$color_num_ids = explode(",", $color_num_id);
			foreach ($color_num_ids as $color_num_row) {
				//echo $job_no . "**" . $po_id . "**" . $item_id . "**" . $color_num_row;
				foreach ($color_size_data_arr[$job_no][$po_id][$item_id][$color_num_row] as $size => $csid) {
					if (isset($production_data_arr[$po_id][$line_id])) {
						$production_qnty += $production_data_arr[$line_id][$csid];
					}
				}
			}
		}

		$size_num_ids = explode(",", $size_num_id);
		$job_nos = explode(",", $job_no);
		$po_ids = explode(",", $po_id);
		$item_ids = explode(",", $item_id);
		if ($plan_level == 3) {
			foreach ($size_num_ids as $key => $size_num_row) {
				foreach ($color_size_data_arr[$job_nos[$key]][$po_ids[$key]][$item_ids[$key]] as $color => $size_id) {
					foreach ($size_id as $size => $csid) {
						if ($size_num_row == $size) {
							$production_qnty += $production_data_arr[$line_id][$csid];
						}
					}
				}
			}
		}

		if ($plan_level == 4) {
			$color_num_ids = explode(",", $color_num_id);
			foreach ($color_num_ids as $key => $color_num_row) {
				foreach ($color_size_data_arr[$job_nos[$key]][$po_ids[$key]][$item_ids[$key]][$color_num_row][$size_num_ids[$key]] as $csid) {
					if (!empty($production_data_arr)) {
						$production_qnty += $production_data_arr[$line_id][$csid];
					} else {
						$production_qnty += 0;
					}
				}
			}
		}
		return $production_qnty;
	}

	function get_job_images($po_id, $url) {
		if (!$po_id) {
			$po_id = 0;
		}

		$images_sql = "SELECT a.ID,b.IMAGE_LOCATION from WO_PO_BREAK_DOWN a, COMMON_PHOTO_LIBRARY b where a.job_no_mst=b.MASTER_TBLE_ID and a.status_active in(1,2,3) and a.is_deleted=0 and a.id in($po_id) and b.file_type=1  order by b.id asc  ";

		$data_array = $this->db->query($images_sql)->result();
		$value = "";
		$i = 0;
		if (!empty($data_array)) {
			foreach ($data_array as $vals) {
				if ($i == 0) {
					$value = "http://$url/" . $vals->IMAGE_LOCATION;
				}
				$i++;

			}
		}
		return $value;
	}

	function get_plan_data_info($company_id, $location_id, $floor_id, $txt_date_from, $user_id, $auto_balancing = 1) {

		if ($location_id > 0) {$locCon = " and LOCATION_NAME in(0,$location_id)";}
		if ($floor_id > 0) {$florCon = " and FLOOR_NAME in(0,$floor_id)";}

		$board_sql = $this->db->query("select ID,USER_ID,LOCATION_NAME,FLOOR_NAME from planning_board_status where BOARD_STATUS=1 $locCon $florCon ")->result();
		foreach ($board_sql as $rows) {
			$board_location_arr[$rows->LOCATION_NAME] = $rows->LOCATION_NAME;
			$board_floor_arr[$rows->FLOOR_NAME] = $rows->FLOOR_NAME;
		}
		$locations = '';
		$floors = '';
		if (count($board_location_arr) > 0 && $location_id > 0) {
			$locations = ',' . implode(',', $board_location_arr);
		}
		if (count($board_floor_arr) > 0 && $floor_id > 0) {
			$floors = ',' . implode(',', $board_floor_arr);
		}

		$floor_cond_res = '';
		$floor_cond_line = '';
		$floor_cond_line_sts = '';
		if ($floor_id > 0) {
			$floor_cond_res = " and floor_id='$floor_id' ";
			$floor_cond_line = " and floor_name='$floor_id' ";
			$floor_cond_line_sts = " and floor_name in ($floor_id $floors) ";
		}

		$user_arr = array();
		$user_arr_type = array();
		$location_res = $this->db->query("select ID,USER_NAME,IS_PLANNER from user_passwd ")->result();
		foreach ($location_res as $value) {
			$user_arr[$value->ID] = $value->USER_NAME;
			$user_arr_type[$value->ID] = $value->IS_PLANNER;
		}
		$user_arr[0] = '';
		$table_locked = '';
		$need_to_update = 0;
		$need_to_insert = 1;
		$max_id = 0;

		$ppl_sewing_plan_board_dtls_data = array();

		$loc_cond = "";
		$loc_cond_lock = "";
		if ($location_id > 0) {
			$loc_cond_lock = " and location_name in($location_id $locations)";
			$loc_cond .= " and location_name='$location_id' ";
		}

		if ($user_arr_type[$user_id] == 1) {

			$sql_line = "SELECT a.BOARD_STATUS,a.USER_ID ,b.IS_PLANNER from planning_board_status a,user_passwd b  where b.id=a.user_id and b.IS_PLANNER=1 and  a.company_name='$company_id' $loc_cond_lock $floor_cond_line_sts order by a.BOARD_STATUS asc";

			//return $sql_line;

			$new_line_resource = $this->db->query($sql_line)->result();

			foreach ($new_line_resource as $ids => $vals) {
				if ($vals->USER_ID != $user_id) {
					if ($vals->BOARD_STATUS == 1) {
						$table_locked = $user_arr[$vals->USER_ID];
					}

				} else {
					if ($vals->BOARD_STATUS != 1) {
						$need_to_update = 1;
					} else {
						$need_to_insert = 0;
					}

				}
			}

			//return $table_locked;

			if ($table_locked == '') // need to lock board for this user
			{
				if ($need_to_update == 0 && $need_to_insert == 1) // New Insert
				{
					$max_id = $this->get_max_value("PLANNING_BOARD_STATUS", "ID") + 1;
					$ppl_sewing_plan_board_dtls_data = array(
						'ID' => $max_id,
						'COMPANY_NAME' => $company_id,
						'LOCATION_NAME' => $location_id,
						'FLOOR_NAME' => $floor_id,
						'USER_ID' => $user_id,
						'BOARD_STATUS' => 1,
					);
					$this->insertData($ppl_sewing_plan_board_dtls_data, "PLANNING_BOARD_STATUS");
				} else {
					$this->db->query("update planning_board_status set board_status=1 where company_name='$company_id' $loc_cond and user_id=$user_id  $floor_cond_line");

				}
			}
		} else {
			$table_locked = 'VISITOR';
		}

		//---------------ws_gsd

		$is_integrated = return_field_value("WORK_STUDY_INTEGRATED", "variable_settings_production", "company_name='$company_id' and variable_list=9", "WORK_STUDY_INTEGRATED");

		if ($is_integrated == 1) {
			$machineSql = "select B.GSD_MST_ID,sum(case when b.resource_gsd  in(40,41,43,44,48,55,68,69,70) then b.layout_mp  end) as TOT_HELPER,
	 sum(case when b.resource_gsd  not in(40,41,43,44,48,68,69,53,54,55,56,70) then b.layout_mp  end) as TOT_OP_MEC
	from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b  where a.id=b.mst_id  and a.balancing_page=1 and a.is_deleted=0  and b.is_deleted=0  and a.status_active = 1  and b.status_active = 1  group by b.gsd_mst_id"; // and a.gsd_mst_id=367
			$machine_result = $this->db->query($machineSql)->result();
			$machine_data_arr = array();
			foreach ($machine_result as $row) {
				$machine_data_arr['HELPER'][$row->GSD_MST_ID] = $row->TOT_HELPER;
				$machine_data_arr['MACHINE'][$row->GSD_MST_ID] = $row->TOT_OP_MEC;
				$machine_data_arr['OPERATOR'][$row->GSD_MST_ID] = $row->TOT_OP_MEC;
			}

			$gsdSql = "select A.ID, C.JOB_NO, B.EFFICIENCY, B.TARGET,b.ALLOCATED_MP from ppl_gsd_entry_mst a,  ppl_balancing_mst_entry b,  wo_po_details_mas_set_details c where a.id = b.gsd_mst_id and c.quot_id = a.id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and  b.balancing_page=2 order by a.id desc"; //and a.id = 367
			$gsd_result = $this->db->query($gsdSql)->result();
			$gsd_data_arr = array();
			foreach ($gsd_result as $row) {
				$gsd_data_arr['EFFICIENCY'][$row->JOB_NO] = $row->EFFICIENCY;
				$gsd_data_arr['TARGET'][$row->JOB_NO] = $row->TARGET;
				$gsd_data_arr['HELPER'][$row->JOB_NO] = $machine_data_arr['HELPER'][$row->ID];
				$gsd_data_arr['MACHINE'][$row->JOB_NO] = $machine_data_arr['MACHINE'][$row->ID];
				$gsd_data_arr['OPERATOR'][$row->JOB_NO] = $machine_data_arr['OPERATOR'][$row->ID];
				//$gsd_data_arr['ALLOCATED_MP'][$row->JOB_NO]=$row->ALLOCATED_MP;
			}
		}

		//$table_locked='';
		$line_names_ids = array();
		$sql_line = "select ID,LINE_NAME from lib_sewing_line where company_name='$company_id' $loc_cond $floor_cond_line order by sewing_line_serial";
		//return $sql_line;

		$new_line_resource = $this->db->query($sql_line)->result();
		foreach ($new_line_resource as $ids => $vals) {
			$line_names_ids[$vals->ID] = $vals->ID;
		}
		//print_r($line_names_ids);die;
		$null_arr = array();
		if (count($line_names_ids) < 1) {
			return $null_arr;
		}

		$job_smv_sql = sql_select("SELECT   JOB_NO, GMTS_ITEM_ID,  SMV_PCS FROM wo_po_details_mas_set_details  ");
		$job_smv_arr = array();
		foreach ($job_smv_sql as $v) {
			$job_smv_arr[$v->JOB_NO][$v->GMTS_ITEM_ID] = $v->SMV_PCS;
		}

		$company_cond = " and company_id='$company_id'";
		$po_id_cond = "";

		$from_date = date("Y-m-d", strtotime($txt_date_from));
		$tmp_from_date = $from_date;
		$days_forward = 120;
		$days_backward = 65;
		/*function add_date($orgDate,$days,$type)
			    {
			        $cd = strtotime($orgDate);
			        if($type == 1){
			            $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$days,date('Y',$cd)));
			        }else{
			            $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)-$days,date('Y',$cd)));
			        }
			        return $retDAY;
		*/

		$from_date = add_date($from_date, $days_backward, 0);
		$to_date = add_date($tmp_from_date, $days_forward, 1);
		if ($this->db->dbdriver == 'mysqli') {
			$plan_sql = "SELECT a.set_dtls_id,a.color_size_id,b.SMV, a.PO_BREAK_DOWN_ID as multi_po, a.HALF, a.ID,a.LINE_ID,a.PLAN_ID,a.START_DATE,a.START_HOUR,a.END_DATE,a.END_HOUR,a.DURATION,a.PLAN_QNTY,a.COMP_LEVEL,a.FIRST_DAY_OUTPUT, a.NEXT_FIRST_DAY_OUTPUT,
   a.NEXT_INCREMENT, a.NEXT_TERGET, a.LEARING_ITERATOR,a.INCREMENT_QTY,a.TERGET,a.DAY_WISE_PLAN,a.COMPANY_ID,a.LOCATION_ID,a.OFF_DAY_PLAN,a.ORDER_COMPLEXITY,a.SHIP_DATE, EXTRA_PARAM,a.PLAN_LEVEL,a.FIRST_DAY_CAPACITY,a.LAST_DAY_CAPACITY,a.SEQ_NO,a.PO_COMPANY_ID,a.USE_LEARNING_CURVE,a.CURRENT_PRODUCTION_DATE,a.PRODUCTION_PERCENT, a.TOP_BORDER_COLOR,a.BOTTOM_BORDER_COLOR,a.LEFT_COLOR,a.RIGHT_COLOR,1   AS JOB_NO,GROUP_CONCAT(b.PO_BREAK_DOWN_ID)  AS PO_BREAK_DOWN_ID,GROUP_CONCAT(b.ITEM_NUMBER_ID) AS ITEM_NUMBER_ID, GROUP_CONCAT(b.SIZE_NUMBER_ID)  AS SIZE_NUMBER_ID, GROUP_CONCAT(b.COLOR_NUMBER_ID) AS COLOR_NUMBER_ID, GROUP_CONCAT(b.COUNTRY_ID)  AS COUNTRY_ID,a.MERGE_COMMENTS,a.MERGE_TYPE,a.INSERT_DATE ,a.NOTES,a.CLOSING_STATUS,a.CLOSED_BY,a.CLOSING_DATE,a.CLOSING_NOTE,a.RE_OPEN_DATE,a.RE_OPENED_BY,a.RE_OPEN_NOTE,a.ALLOCATED_MP,a.BYPASS_MP,a.remaining_work_hour,a.AUTO_TARGET from ppl_sewing_plan_board a,ppl_sewing_plan_board_powise b where a.plan_id=b.plan_id and a.company_id='$company_id'  and a.line_id in( " . implode(",", $line_names_ids) . ") and (a.start_date between STR_TO_DATE('" . $from_date . "', '%Y-%m-%d')  and STR_TO_DATE('" . $to_date . "','%Y-%m-%d')   or a.end_date between STR_TO_DATE('" . $from_date . "','%Y-%m-%d')  and STR_TO_DATE('" . $to_date . "','%Y-%m-%d')  or ( a.start_date < STR_TO_DATE('" . $from_date . "','%Y-%m-%d')  and a.end_date> STR_TO_DATE('" . $to_date . "','%Y-%m-%d')))  and a.status_active=1 group by  b.SMV,a.PO_BREAK_DOWN_ID, a.half,a.id,a.line_id,a.plan_id,a.start_date,a.start_hour,a.end_date,a.end_hour,a.duration, a.plan_qnty,a.comp_level,a.first_day_output, a.next_first_day_output,
   a.next_increment, a.next_terget,a.LEARING_ITERATOR, a.increment_qty,a.terget,a.day_wise_plan,a.company_id,a.location_id,a.item_number_id, a.off_day_plan,a.order_complexity,a.ship_date, extra_param,a.plan_level,a.first_day_capacity,a.last_day_capacity,a.seq_no,a.po_company_id,a.use_learning_curve,a.current_production_date,a.production_percent, a.top_border_color,a.bottom_border_color,a.left_color,a.right_color,  a.INSERT_DATE ,a.NOTES,a.CLOSING_STATUS,a.CLOSED_BY,a.CLOSING_DATE,a.CLOSING_NOTE,a.RE_OPEN_DATE,a.RE_OPENED_BY,a.RE_OPEN_NOTE,a.set_dtls_id,a.color_size_id,a.ALLOCATED_MP,a.BYPASS_MP,a.remaining_work_hour,a.AUTO_TARGET";

		} else {
			$plan_sql = "SELECT  a.set_dtls_id,a.color_size_id,b.SMV,a.PO_BREAK_DOWN_ID as multi_po,a.learing_iterator, a.half, a.id,a.line_id,a.plan_id,a.start_date,a.start_hour,a.end_date,a.end_hour,a.duration,a.plan_qnty,a.comp_level,a.first_day_output, a.next_first_day_output,
   a.next_increment, a.next_terget, a.increment_qty,a.terget,a.day_wise_plan,a.company_id,a.location_id,a.off_day_plan,a.order_complexity,a.ship_date, a.extra_param,a.plan_level,a.first_day_capacity,a.last_day_capacity,a.seq_no,a.po_company_id,1 as use_learning_curve,a.current_production_date,a.production_percent, a.top_border_color,a.bottom_border_color,a.left_color,a.right_color,1 as job_no,listagg(b.po_break_down_id, ',') within group (order by b.po_break_down_id) as po_break_down_id,listagg(b.item_number_id, ',') within group (order by b.item_number_id) as item_number_id, listagg(b.size_number_id, ',') within group (order by b.size_number_id) as size_number_id, listagg(b.color_number_id, ',') within group (order by b.color_number_id) as color_number_id, listagg(b.country_id, ',') within group (order by b.country_id) as country_id, a.MERGE_COMMENTS,a.MERGE_TYPE,a.INSERT_DATE  ,a.NOTES,a.CLOSING_STATUS,a.CLOSED_BY,a.CLOSING_DATE,a.CLOSING_NOTE,a.RE_OPEN_DATE,a.RE_OPENED_BY,a.RE_OPEN_NOTE,a.ALLOCATED_MP,a.BYPASS_MP,a.remaining_work_hour,a.AUTO_TARGET from ppl_sewing_plan_board a,ppl_sewing_plan_board_powise b  where a.plan_id=b.plan_id   and a.company_id='$company_id' $po_id_cond and a.line_id in( " . implode(",", $line_names_ids) . ") and (a.start_date between to_date('" . $from_date . "','yyyy-mm-dd')  and to_date('" . $to_date . "','yyyy-mm-dd')   or a.end_date between to_date('" . $from_date . "','yyyy-mm-dd')  and to_date('" . $to_date . "','yyyy-mm-dd')  or ( a.start_date < to_date('" . $from_date . "','yyyy-mm-dd')  and a.end_date> to_date('" . $to_date . "','yyyy-mm-dd')))  and a.status_active=1        group by  b.SMV,a.PO_BREAK_DOWN_ID,a.half,a.id,a.line_id,a.plan_id,a.start_date,a.start_hour,a.end_date,a.end_hour,a.duration, a.plan_qnty,a.comp_level,a.first_day_output,  a.next_first_day_output, a.LEARING_ITERATOR,
   a.next_increment, a.next_terget,a.increment_qty,a.terget,a.day_wise_plan,a.company_id,a.location_id,a.item_number_id, a.off_day_plan,a.order_complexity,a.ship_date, extra_param,a.plan_level,a.first_day_capacity,a.last_day_capacity,a.seq_no,a.po_company_id,a.use_learning_curve,a.current_production_date,a.production_percent, a.top_border_color,a.bottom_border_color,a.left_color,a.right_color , a.MERGE_COMMENTS,a.MERGE_TYPE,a.INSERT_DATE ,a.NOTES,a.CLOSING_STATUS,a.CLOSED_BY,a.CLOSING_DATE,a.CLOSING_NOTE,a.RE_OPEN_DATE,a.RE_OPENED_BY,a.RE_OPEN_NOTE,a.set_dtls_id,a.color_size_id,a.ALLOCATED_MP,a.BYPASS_MP,a.remaining_work_hour,a.AUTO_TARGET order by a.id desc  ";
		} //     and a.plan_id=2009
		//echo $plan_sql;die;
		//return array(DTLS=>$plan_sql);

		$plan_data = $this->db->query($plan_sql)->result();
		$com_res = $this->db->query("select ID,COMPANY_NAME from lib_company where status_active =1 and is_deleted=0  order by company_name")->result();

		foreach ($com_res as $value) {
			$comp[$value->ID] = $value->COMPANY_NAME;
		}

		$location_res = $this->db->query("select ID,LOCATION_NAME from lib_location  where status_active =1 and is_deleted=0  order by location_name")->result();

		foreach ($location_res as $value) {
			$location_arr[$value->ID] = $value->LOCATION_NAME;
		}
		$garment_res = $this->db->query("select ID,ITEM_NAME from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name")->result();

		foreach ($garment_res as $value) {
			$garments_item[$value->ID] = $value->ITEM_NAME;
		}

		$buyer_res = $this->db->query("select ID,BUYER_NAME from  lib_buyer where status_active=1 and is_deleted=0 order by BUYER_NAME")->result();

		foreach ($buyer_res as $value) {
			$buyer_arr[$value->ID] = $value->BUYER_NAME;
		}

		$resource_res = $this->db->query("select ID,LINE_NUMBER from  PROD_RESOURCE_MST   ")->result();

		foreach ($resource_res as $value) {
			$resource_arr[$value->ID] = $value->LINE_NUMBER;
		}

		$npos = $line_id_arr = array();
		$is_plan = 0;
		foreach ($plan_data as $rows) {
			//if($rows->PO_BREAK_DOWN_ID>0)
			$npos[$rows->PO_BREAK_DOWN_ID] = $rows->PO_BREAK_DOWN_ID;
			$line_id_arr[] = $rows->LINE_ID * 1;
			$is_plan = 1;
		}

		$p = 1;
		$sel_pos2 = array_chunk($npos, 999);
		$sql = $sql2 = $sql3 = '';
		foreach ($sel_pos2 as $job_no_process) {
			if ($p == 1) {
				$sql .= " and (PO_NUMBER_ID in(" . implode(',', $job_no_process) . ")";
				$sql2 .= " and (c.id in(" . implode(',', $job_no_process) . ")";
				$sql3 .= " and (a.PO_BREAK_DOWN_ID in(" . implode(',', $job_no_process) . ")";
			} else {
				$sql .= " or PO_NUMBER_ID in(" . implode(',', $job_no_process) . ")";
				$sql2 .= " or (c.id in(" . implode(',', $job_no_process) . ")";
				$sql3 .= " or (a.PO_BREAK_DOWN_ID in(" . implode(',', $job_no_process) . ")";
			}
			$sql4 = " and a.SEWING_LINE in(" . implode(',', $line_id_arr) . ")";

			$p++;
		}
		$sql .= ")";
		$sql2 .= ")";
		$sql3 .= ")";

		if ($is_plan == 0) {
			$sql = '';
			$sql2 = '';
			$sql3 = '';
		}

		$prod_reso_allo = get_resource_allocation_variable($company_id);

		$production_arr = array();
		if (!empty($line_id_arr)) {
			$production_sql = "select a.PRODUCTION_DATE,a.PO_BREAK_DOWN_ID, a.SEWING_LINE,sum(b.PRODUCTION_QNTY) PRODUCTION_QNTY, a.ITEM_NUMBER_ID from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id   and a.production_type=5 $sql3  and a.status_active=1 and a.is_deleted=0 group by a.PRODUCTION_DATE,a.po_break_down_id, a.SEWING_LINE, a.ITEM_NUMBER_ID";

			$production_result = $this->db->query($production_sql)->result();
			if (!empty($production_result)) {
				foreach ($production_result as $production_row) {
					$po_wise_productionArr[$production_row->PO_BREAK_DOWN_ID] += $production_row->PRODUCTION_QNTY;
					$item_wise_productionArr[$production_row->ITEM_NUMBER_ID][$production_row->PO_BREAK_DOWN_ID] += $production_row->PRODUCTION_QNTY;
					//if (isset($resource_arr[$production_row->SEWING_LINE])) {
					if ($prod_reso_allo == 1) {
						$lines = $resource_arr[$production_row->SEWING_LINE];
						$line_arr = explode(",", $lines);
						foreach ($line_arr as $vv) {
							$production_arr[$production_row->PO_BREAK_DOWN_ID][$vv]["SEWING_LINE"] = $vv;
							$production_arr[$production_row->PO_BREAK_DOWN_ID][$vv]["PRODUCTION_QNTY"] += $production_row->PRODUCTION_QNTY;
							$date_production_arr[$production_row->PO_BREAK_DOWN_ID][$vv][$production_row->PRODUCTION_DATE] += $production_row->PRODUCTION_QNTY;
						}

					} else {
						$production_arr[$production_row->PO_BREAK_DOWN_ID][$production_row->SEWING_LINE]["SEWING_LINE"] = $production_row->SEWING_LINE;
						$production_arr[$production_row->PO_BREAK_DOWN_ID][$production_row->SEWING_LINE]["PRODUCTION_QNTY"] += $production_row->PRODUCTION_QNTY;
						$date_production_arr[$production_row->PO_BREAK_DOWN_ID][$production_row->SEWING_LINE][$production_row->PRODUCTION_DATE] += $production_row->PRODUCTION_QNTY;
					}

				}
			}
		}

		//------------------

		foreach ($plan_data as $rows) {
			$allPlanIdArr[$rows->PLAN_ID] = $rows->PLAN_ID;
		}

		$daywise_sql = "SELECT b.IS_OFFDAY, b.WORKING_HOUR, c.PO_BREAK_DOWN_ID,a.LINE_ID,a.START_DATE,a.END_DATE,a.PLAN_QNTY,a.FIRST_DAY_OUTPUT,a.INCREMENT_QTY,a.TERGET ,a.COMPANY_ID, a.LOCATION_ID,a.ITEM_NUMBER_ID ,a.OFF_DAY_PLAN,a.ORDER_COMPLEXITY,b.PLAN_DATE,b.PLAN_QNTY,b.PLAN_ID  from ppl_sewing_plan_board a,ppl_sewing_plan_board_dtls b,PPL_SEWING_PLAN_BOARD_POWISE c where a.plan_id=c.plan_id and  b.plan_id=c.plan_id and a.plan_id=b.plan_id and a.is_deleted=0 and a.status_active=1 ";

		$data_list_arr = array_chunk($allPlanIdArr, 999);
		$p = 1;
		foreach ($data_list_arr as $po_process) {
			if ($p == 1) {$daywise_sql .= " and ( a.plan_id in(" . implode(',', $po_process) . ")";} else { $daywise_sql .= " or a.plan_id in(" . implode(',', $po_process) . ")";}
			$p++;
		}
		if (count($data_list_arr) > 0) {
			$daywise_sql .= ")";
		}

		$daywise_plan = $this->db->query($daywise_sql)->result();
		$plan_production_qnty = array();
		$po_production_qnty = array();
		foreach ($daywise_plan as $rows) {
			$production_line_qnty = 0;
			if ($date_production_arr[$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID][$rows->PLAN_DATE]) {
				$plan_production_qnty[$rows->PLAN_ID] += $date_production_arr[$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID][$rows->PLAN_DATE];
				//$po_production_qnty[$rows->PO_BREAK_DOWN_ID] += $date_production_arr[$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID][$rows->PLAN_DATE];
			}
			$date_plan_qty_total_arr[$rows->PLAN_DATE] += $rows->PLAN_QNTY;
			//$po_production_qnty[$rows->PO_BREAK_DOWN_ID] += $date_production_arr[$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID][$rows->PLAN_DATE];

		}

		// return array(DTLS=>$plan_production_qnty);

		//-----------------

		$sqls = $this->db->query("select min(task_start_date) as TASK_START_DATE,max(task_finish_date) as TASK_FINISH_DATE,PO_NUMBER_ID from tna_process_mst where is_deleted=0 and status_active=1  and task_number in(86,190,191) $sql group by po_number_id");

		$sel_pos = "";
		$tna_task_data[0]['task_start_date'] = date("d-m-Y", time());
		$tna_task_data[0]['task_finish_date'] = date("d-m-Y", time());

		foreach ($sqls->result() as $srows) {
			$tna_task_data[$srows->PO_NUMBER_ID]['task_start_date'] = date("d-m-Y", strtotime($srows->TASK_START_DATE));
			$tna_task_data[$srows->PO_NUMBER_ID]['task_finish_date'] = date("d-m-Y", strtotime($srows->TASK_FINISH_DATE));
		}
		$sqls = $this->db->query("select c.JOB_NO_MST,C.PO_NUMBER,B.STYLE_REF_NO,B.BUYER_NAME,C.ID,SUM(C.PO_QUANTITY) PO_QUANTITY from wo_po_details_master b,wo_po_break_down c where c.job_no_mst=b.job_no and b.status_active=1 and c.status_active=1 $sql2 group by c.JOB_NO_MST,c.po_number,b.style_ref_no,b.buyer_name,c.id");

		foreach ($sqls->result() as $srows) {
			$wo_po_details[$srows->ID]['job_no'] = $srows->JOB_NO_MST;
			$wo_po_details[$srows->ID]['po_number'] = $srows->PO_NUMBER;
			$wo_po_details[$srows->ID]['style_ref'] = $srows->STYLE_REF_NO;
			$wo_po_details[$srows->ID]['buyer_name'] = $srows->BUYER_NAME;
			$wo_po_details[$srows->ID]['po_quantity'] = $srows->PO_QUANTITY;
		}

		$wo_po_details[0]['job_no'] = '';
		$wo_po_details[0]['po_number'] = '';
		$wo_po_details[0]['style_ref'] = '';
		$wo_po_details[0]['buyer_name'] = '';
		$wo_po_details[0]['po_quantity'] = '';

		$country_sql = "select ID,COUNTRY_NAME from  LIB_COUNTRY";
		$countries = $this->db->query($country_sql)->result();
		$country_arr = array();
		foreach ($countries as $crows) {
			$country_arr[$crows->ID] = $crows->COUNTRY_NAME;
		}

		// ini_set('display_errors',0);

		//return $production_arr;

		$i = 0;
		$npo = '';
		$data_array = array();
		$urls = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$url_arr = explode('/', $urls);
		$actual_link = $url_arr[0] . "/" . $url_arr[1];
		foreach ($plan_data as $rows) {

			$line_plan_qty_total_arr[$rows->LINE_ID] += $rows->PLAN_QNTY;
			//$date_plan_qty_total_arr[$rows->START_DATE]+=$rows->PLAN_QNTY;

			//$production_line_qnty= (!empty($production_arr) && ($rows->PO_BREAK_DOWN_ID>0))?$production_arr[$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID]["PRODUCTION_QNTY"]:0;
			$production_line_qnty = 0;
			$all_smv = 0;

			if (!empty($production_arr) && ($rows->PO_BREAK_DOWN_ID > 0)) {
				foreach (array_unique(explode(",", $rows->PO_BREAK_DOWN_ID)) as $po_val) {

					if ($production_arr[$po_val][$rows->LINE_ID]["PRODUCTION_QNTY"]) {
						//$production_line_qnty = $production_arr[$po_val][$rows->LINE_ID]["PRODUCTION_QNTY"];
						$production_line_qnty += $production_arr[$po_val][$rows->LINE_ID]["PRODUCTION_QNTY"];
					}
				}
			}
			//$production_line_qnty= (!empty($production_arr) && ($rows->PO_BREAK_DOWN_ID>0))?$production_arr[$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID]["PRODUCTION_QNTY"]:0;
			if (1 == 1) {
				//$npo = implode(",", explode(",", $rows->PO_BREAK_DOWN_ID));
				$npo = $rows->PO_BREAK_DOWN_ID;

				$data_array[$i]["IS_PRODUCTION"] = ($plan_production_qnty[$rows->PLAN_ID] == 0) ? false : true;
				$data_array[$i]["THIS_PRODUCTION"] = $plan_production_qnty[$rows->PLAN_ID] * 1;

				if ($npo < 0) {
					$rows->PO_BREAK_DOWN_ID = 0;
					$rows->COLOR_NUMBER_ID = 0;
					$rows->SIZE_NUMBER_ID = 0;
					$rows->ITEM_NUMBER_ID = 0;
				}

				$plan_level = $rows->PLAN_LEVEL;

				$color_name_arr = array();
				foreach (explode(',', $rows->COLOR_NUMBER_ID) as $cid) {
					$color_name_arr[$cid] = $this->get_field_value_by_attribute("LIB_COLOR", "COLOR_NAME", $cid);
				}

				$size_name_arr = array();
				foreach (explode(',', $rows->SIZE_NUMBER_ID) as $sid) {
					$size_name_arr[$sid] = $this->get_field_value_by_attribute("LIB_SIZE", "SIZE_NAME", $sid);
				}

				//$color_name = $this->get_field_value_by_attribute("LIB_COLOR", "COLOR_NAME", $rows->COLOR_NUMBER_ID);
				//$size_name = $this->get_field_value_by_attribute("LIB_SIZE", "SIZE_NAME", $rows->SIZE_NUMBER_ID);
				$color_name = implode(',', $color_name_arr);
				$size_name = implode(',', $size_name_arr);

				$multi_po = $rows->MULTI_PO;
				if ($multi_po == 0) {
					$all_buyer = array();
					$npo_arr = explode(',', $npo);
					foreach ($npo_arr as $v) {
						$buyer_name = $buyer_arr[$wo_po_details[$v]['buyer_name']];
						$all_buyer[$buyer_name] = $buyer_name;
					}
					$data_array[$i]["BUYER_NAME"] = implode(', ', $all_buyer);
				} else {
					$all_buyer = array();
					$npo_arr1 = explode(',', $rows->PO_BREAK_DOWN_ID);
					foreach ($npo_arr1 as $v) {
						$buyer_name = $buyer_arr[$wo_po_details[$v]['buyer_name']];
						$all_buyer[$buyer_name] = $buyer_name;
					}
					$data_array[$i]["BUYER_NAME"] = implode(', ', $all_buyer);

				}
				if ($this->db->dbdriver == 'mysqli') {
					$proddate_cond = " '" . date("Y-m-d", strtotime($rows->START_DATE)) . "' and '" . date("Y-m-d", strtotime($rows->END_DATE)) . "'";
				} else {
					$proddate_cond = " '" . date("d-M-Y", strtotime($rows->START_DATE)) . "' and '" . date("d-M-Y", strtotime($rows->END_DATE)) . "'";
				}

				$lin = $rows->LINE_ID;

				$country_ids = explode(",", $rows->COUNTRY_ID);
				$country_name = "";
				if (!empty($country_ids)) {
					foreach ($country_ids as $country) {
						if ($country > 0) {
							$country_name = $country_arr[$country] . ",";
						}
					}
				}

				$production_qnty = $this->get_production_qnty_by_po_item($rows->PO_BREAK_DOWN_ID, $lin, $proddate_cond, 3);

				$data_array[$i]["COLOR_NUMBER_ID"] = $rows->COLOR_NUMBER_ID;
				$data_array[$i]["COLOR_NUMBER"] = (!empty($color_name)) ? $color_name : "";
				$data_array[$i]["SIZE_NUMBER_ID"] = $rows->SIZE_NUMBER_ID;
				$data_array[$i]["SIZE_NUMBER"] = (!empty($size_name)) ? $size_name : "";
				//$multi_po= $rows->MULTI_PO ;

				if ($multi_po == 0) {

					$all_jobs = array();

					$npo_arr = explode(',', $npo);
					$items = implode(",", array_unique(explode(",", $rows->ITEM_NUMBER_ID)));
					foreach ($npo_arr as $v) {
						$job_no = $wo_po_details[$v]['job_no'];
						//$all_jobs[$job_no] = $job_no;
						$all_jobs[] = $job_no;
					}
					$data_array[$i]["JOB_NO"] = implode(', ', $all_jobs);
				} else {
					$all_jobs = array();
					$npo_arr = array_unique(explode(",", $rows->PO_BREAK_DOWN_ID));
					foreach ($npo_arr as $v) {
						$job_no = $wo_po_details[$v]['job_no'];
						//$all_jobs[$job_no] = $job_no;
						$all_jobs[] = $job_no;
					}

					$data_array[$i]["JOB_NO"] = implode(', ', $all_jobs);

				}

				$data_array[$i]["JOB_IMG"] = $this->get_job_images($rows->PO_BREAK_DOWN_ID, $actual_link);

				$data_array[$i]["PO_COMPANY_ID"] = $rows->PO_COMPANY_ID;

				if ($multi_po == 0) {

					$all_style = array();
					$npo_arr = explode(',', $npo);
					foreach ($npo_arr as $v) {
						$style_ref = $wo_po_details[$v]['style_ref'];
						//$all_style[$style_ref] = $style_ref;
						$all_style[] = $style_ref;
					}
					$data_array[$i]["STYLE_REF_NO"] = implode(', ', $all_style);
				} else {
					$all_style = array();
					$npo_arr = array_unique(explode(",", $rows->PO_BREAK_DOWN_ID));
					foreach ($npo_arr as $v) {
						$style_ref = $wo_po_details[$v]['style_ref'];
						//$all_style[$style_ref] = $style_ref;
						$all_style[] = $style_ref;
					}
					$data_array[$i]["STYLE_REF_NO"] = implode(', ', $all_style);

				}

				$data_array[$i]["PO_BREAK_DOWN_ID"] = $rows->PO_BREAK_DOWN_ID;

				if ($multi_po == 0) {

					$all_po = "";

					$npo_arr = explode(',', $npo);
					foreach ($npo_arr as $v) {
						if ($all_po) {

							if (isset($wo_po_details[$v]['po_number'])) {

								$all_po .= ',' . $wo_po_details[$v]['po_number'];
							}
							//return $all_po;

						} else {
							if (isset($wo_po_details[$v]['po_number'])) {
								$all_po .= $wo_po_details[$v]['po_number'];
							}

						}
					}
					$data_array[$i]["PO_NUMBER"] = $all_po;
				} else {
					/*if (isset($wo_po_details[$rows->PO_BREAK_DOWN_ID]['po_number'])) {
							$data_array[$i]["PO_NUMBER"] = $wo_po_details[$rows->PO_BREAK_DOWN_ID]['po_number'];
						} else {
							$data_array[$i]["PO_NUMBER"] = "";
					*/
					$all_po = "";

					$npo_arr = array_unique(explode(",", $rows->PO_BREAK_DOWN_ID));
					foreach ($npo_arr as $v) {
						if ($all_po) {

							if ($wo_po_details[$v]['po_number']) {

								$all_po .= ',' . $wo_po_details[$v]['po_number'];
							}
							//return $all_po;

						} else {
							if ($wo_po_details[$v]['po_number']) {
								$all_po .= $wo_po_details[$v]['po_number'];
							}

						}
					}
					$data_array[$i]["PO_NUMBER"] = $all_po;

				}

				if ($multi_po == 0) {

					$all_po_qnty = 0;
					$npo_arr = explode(',', $npo);
					foreach ($npo_arr as $v) {
						if ($wo_po_details[$v]['po_quantity']) {
							$all_po_qnty += $wo_po_details[$v]['po_quantity'];
						}

					}
					$data_array[$i]["PO_QUANTITY"] = $all_po_qnty;
				} else {
					/*if (isset($wo_po_details[$rows->PO_BREAK_DOWN_ID]['po_quantity'])) {
							$data_array[$i]["PO_QUANTITY"] = $wo_po_details[$rows->PO_BREAK_DOWN_ID]['po_quantity'];
						} else {
							$data_array[$i]["PO_QUANTITY"] = "";
					*/
					$all_po_qnty = 0;
					$npo_arr = array_unique(explode(",", $rows->PO_BREAK_DOWN_ID));
					foreach ($npo_arr as $v) {
						if ($wo_po_details[$v]['po_quantity']) {
							$all_po_qnty += $wo_po_details[$v]['po_quantity'];
						}

					}
					$data_array[$i]["PO_QUANTITY"] = $all_po_qnty;

				}
				$job_smv = ($rows->SMV) ? $rows->SMV : 0;
				$data_array[$i]["SMV"] = number_format($job_smv, 2);
				$data_array[$i]["line_id"] = $rows->LINE_ID;
				$data_array[$i]["plan_id"] = $rows->PLAN_ID;
				$data_array[$i]["SEQ_NO"] = $rows->SEQ_NO;
				$data_array[$i]["PLAN_LEVEL"] = $rows->PLAN_LEVEL;
				$data_array[$i]["start_date"] = date("d-m-Y", strtotime($rows->START_DATE));
				$data_array[$i]["start_hour"] = $rows->START_HOUR;
				$data_array[$i]["end_date"] = date("d-m-Y", strtotime($rows->END_DATE));
				$data_array[$i]["end_hour"] = $rows->END_HOUR;
				$data_array[$i]["REMAINING_WORK_HOUR"] = $rows->REMAINING_WORK_HOUR;
				$data_array[$i]["duration"] = $rows->DURATION;
				$data_array[$i]["plan_qnty"] = $rows->PLAN_QNTY;
				$data_array[$i]["comp_level"] = $rows->COMP_LEVEL;
				if (isset($rows->NEXT_FIRST_DAY_OUTPUT)) {
					$data_array[$i]["next_first_day_output"] = $rows->NEXT_FIRST_DAY_OUTPUT;
				} else {
					$data_array[$i]["next_first_day_output"] = 0;
				}

				if (isset($rows->NEXT_INCREMENT)) {
					$data_array[$i]["next_increment"] = $rows->NEXT_INCREMENT;
				} else {
					$data_array[$i]["next_increment"] = 0;
				}

				if (isset($rows->NEXT_TERGET)) {
					$data_array[$i]["next_terget"] = $rows->NEXT_TERGET;
				} else {
					$data_array[$i]["next_terget"] = 0;
				}

				if (isset($rows->LEARING_ITERATOR)) {
					$data_array[$i]["learing_iterator"] = $rows->LEARING_ITERATOR;
				} else {
					$data_array[$i]["learing_iterator"] = 0;
				}

				$data_array[$i]["first_day_output"] = $rows->FIRST_DAY_OUTPUT;
				$data_array[$i]["increment_qty"] = $rows->INCREMENT_QTY;
				$data_array[$i]["terget"] = $rows->TERGET;
				$data_array[$i]["company_id"] = $rows->COMPANY_ID;
				$data_array[$i]["company_name"] = $comp[$rows->COMPANY_ID];
				if (isset($rows->LOCATION_ID)) {
					$data_array[$i]["location_id"] = $rows->LOCATION_ID;
				} else {
					$data_array[$i]["location_id"] = 0;
				}

				$data_array[$i]["COUNTRY_NAME"] = rtrim($country_name, ", ");
				$data_array[$i]["COUNTRY_ID"] = $rows->COUNTRY_ID;
				if (isset($location_arr[$rows->LOCATION_ID])) {
					$data_array[$i]["location_name"] = $location_arr[$rows->LOCATION_ID];
				} else {
					$data_array[$i]["location_name"] = "";
				}

				$data_array[$i]["item_number_id"] = $rows->ITEM_NUMBER_ID;
				if (isset($rows->ITEM_NUMBER_ID)) {
					$items = implode(',', array_unique(explode(",", $rows->ITEM_NUMBER_ID)));
					if (isset($garments_item[$items])) {
						$data_array[$i]["item_name"] = $garments_item[$items];
					} else {
						$data_array[$i]["item_name"] = "";
					}

				} else {
					$data_array[$i]["item_name"] = "";
				}
				$data_array[$i]["off_day_plan"] = $rows->OFF_DAY_PLAN;
				$data_array[$i]["order_complexity"] = $rows->ORDER_COMPLEXITY;
				$data_array[$i]["ship_date"] = date("d-m-Y", strtotime($rows->SHIP_DATE));
				$data_array[$i]["USE_LEARNING_CURVE"] = $rows->USE_LEARNING_CURVE;
				//$data_array[$i]["CURRENT_PRODUCTION_DATE"] = date("d-m-Y", strtotime($production_qnty['prd_date']));
				$data_array[$i]["CURRENT_PRODUCTION_DATE"] = "01-01-1970";
				$production_qnty['production_data_qnty'] = $production_line_qnty;
				$data_array[$i]["PRODUCTION_PERCENT"] = $production_qnty['production_data_qnty'];
				//$data_array[$i]["PRODUCTION_PERCENT"] =0;

				$data_array[$i]["ALLOCATED_MP"] = $rows->ALLOCATED_MP ? $rows->ALLOCATED_MP : 0;
				$data_array[$i]["BYPASS_MP"] = $rows->BYPASS_MP ? $rows->BYPASS_MP : 0;
				$data_array[$i]["AUTO_TARGET"] = $rows->AUTO_TARGET ? $rows->AUTO_TARGET : 0;

				if ($rows->INCREMENT_QTY > 0) {
					$data_array[$i]["TOP_BORDER_COLOR"] = $rows->TOP_BORDER_COLOR;
				} else {
					$data_array[$i]["TOP_BORDER_COLOR"] = "#FF9900";
				}

				if ($production_qnty['production_data_qnty'] > 0) {
					$data_array[$i]["BOTTOM_BORDER_COLOR"] = "Green";
				} else {
					$data_array[$i]["BOTTOM_BORDER_COLOR"] = $rows->BOTTOM_BORDER_COLOR;
				}

				if (strtotime($rows->SHIP_DATE) > strtotime($rows->END_DATE)) {
					$data_array[$i]["LEFT_COLOR"] = "#73CAD5";
					$data_array[$i]["RIGHT_COLOR"] = "#73CAD5";
				}
				if ($production_qnty['production_data_qnty'] < 1 && time() > strtotime($rows->END_DATE)) //No Production but date crossed
				{
					$data_array[$i]["LEFT_COLOR"] = "#909553"; //$rows->LEFT_COLOR;
					$data_array[$i]["RIGHT_COLOR"] = "#909553"; // $rows->RIGHT_COLOR;
				}
				(!empty($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date'])) ? $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date'] : $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date'] = date("d-m-Y", strtotime("1971-01-01"));
				(!empty($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date'])) ? $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date'] : $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date'] = date("d-m-Y", strtotime("1971-01-01"));

				if (strtotime($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']) > strtotime($rows->START_DATE) && strtotime($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']) < strtotime($rows->END_DATE)) // Partial plan TNA Date crossed
				{
					//$difference = round ((strtotime($rows->END_DATE) - strtotime($rows->SHIP_DATE))/(3600*24));
					$data_array[$i]["LEFT_COLOR"] = "#73CAD5"; //$rows->LEFT_COLOR;
					$data_array[$i]["RIGHT_COLOR"] = "#FF6600"; // $rows->RIGHT_COLOR;
					$data_array[$i]["DAYS"] = date_diff_days($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date'], $rows->END_DATE);
				}

				if (strtotime($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']) <= strtotime($rows->START_DATE)) // Full Plan TNA date crossed
				{
					$data_array[$i]["LEFT_COLOR"] = "#FF6600"; //$rows->LEFT_COLOR;
					$data_array[$i]["RIGHT_COLOR"] = "#FF6600"; // $rows->RIGHT_COLOR;
				}

				if (strtotime($rows->SHIP_DATE) > strtotime($rows->START_DATE) && strtotime($rows->SHIP_DATE) < strtotime($rows->END_DATE)) // Partial Ship Date crossed
				{
					//$difference = round ((strtotime($rows->END_DATE) - strtotime($rows->SHIP_DATE))/(3600*24));
					$data_array[$i]["LEFT_COLOR"] = "RED"; //$rows->LEFT_COLOR;
					$data_array[$i]["RIGHT_COLOR"] = "RED"; // $rows->RIGHT_COLOR;
					$data_array[$i]["DAYS"] = date_diff_days($rows->SHIP_DATE, $rows->END_DATE);
				}

				if (strtotime($rows->SHIP_DATE) <= strtotime($rows->START_DATE)) // Full Plan ship date crossed
				{
					$data_array[$i]["LEFT_COLOR"] = "RED"; //$rows->LEFT_COLOR;
					$data_array[$i]["RIGHT_COLOR"] = "RED"; // $rows->RIGHT_COLOR;
				}

				if (strtotime($from_date) > strtotime($rows->START_DATE)) // Crossed date in board
				{
					$data_array[$i]["LEFT_COLOR"] = "#9C8AE3"; //$rows->LEFT_COLOR;
					$data_array[$i]["RIGHT_COLOR"] = "#9C8AE3"; // $rows->RIGHT_COLOR;
				}
				//$npo;
				$npo_arr = array_unique(explode(",", $npo));
				$early_date = "01-01-1971";
				$delay_date = "01-01-1971";
				$st_date = "";
				$end_date = "";
				foreach ($npo_arr as $v) {
					if (isset($tna_task_data[$v]['task_start_date'])) {
						$task_start_date = strtotime($tna_task_data[$v]['task_start_date']);
						if ($st_date == "") {
							$st_date = $task_start_date;
							$early_date = $tna_task_data[$v]['task_start_date'];
						} else {
							if ($task_start_date < $st_date) {
								$early_date = $tna_task_data[$v]['task_start_date'];
							}

						}

						if (isset($tna_task_data[$v]['task_finish_date'])) {
							$task_finish_date = strtotime($tna_task_data[$v]['task_finish_date']);
							if ($end_date == "") {
								$end_date = $task_finish_date;
								$delay_date = $tna_task_data[$v]['task_finish_date'];
							} else {
								if ($task_finish_date > $end_date) {
									$delay_date = $tna_task_data[$v]['task_finish_date'];
								}

							}

						}

					}

				}
				$data_array[$i]["TASK_START_DATE"] = $early_date;
				$data_array[$i]["TASK_END_DATE"] = $delay_date;

				$data_array[$i]["MERGE_TYPE"] = $rows->MERGE_TYPE;
				$data_array[$i]["MERGE_COMMENTS"] = $rows->MERGE_COMMENTS;
				$data_array[$i]["TABLE_LOCKED"] = $table_locked;

				$data_array[$i]["INSERT_DATE"] = date("d-m-Y", strtotime($rows->INSERT_DATE)); //$rows->MERGE_TYPE;

				$data_array[$i]["PRODUCTION_QNTY"] = $production_line_qnty;

				$data_array[$i]["TOTAL_PRODUCTION"] = 0;
				$data_array[$i]["TOTAL_PRODUCTION_ITEM"] = 0;
				foreach (array_unique(explode(",", $rows->PO_BREAK_DOWN_ID)) as $poID) {
					//$data_array[$i]["TOTAL_PRODUCTION"] += $po_production_qnty[$poID];
					$data_array[$i]["TOTAL_PRODUCTION"] += $po_wise_productionArr[$poID];
				
					//item qty..............................
					foreach (array_unique(explode(",", $rows->ITEM_NUMBER_ID)) as $itemID) {
						$data_array[$i]["TOTAL_PRODUCTION_ITEM"] += $item_wise_productionArr[$itemID][$poID];
					}
				}
				
				
				
/*				$data_array[$i]["TOTAL_PRODUCTION_ITEM"] = 0;
				foreach (array_unique(explode(",", $rows->ITEM_NUMBER_ID)) as $itemID) {
					$data_array[$i]["TOTAL_PRODUCTION_ITEM"] += $item_wise_productionArr[$itemID];
				}
*/				
				

				$data_array[$i]["PRODUCTION_DAY"] = $rows->LINE_ID;
				if (isset($rows->HALF)) {
					$data_array[$i]["HALF"] = $rows->HALF;
				} else {
					$data_array[$i]["HALF"] = 0;
				}

				$data_array[$i]["NOTES"] = $rows->NOTES;

				$data_array[$i]["CLOSING_STATUS"] = $rows->CLOSING_STATUS;
				$data_array[$i]["CLOSED_BY"] = $rows->CLOSED_BY;
				$data_array[$i]["CLOSING_DATE"] = $rows->CLOSING_DATE;
				$data_array[$i]["CLOSING_NOTE"] = $rows->CLOSING_NOTE;
				$data_array[$i]["RE_OPEN_DATE"] = $rows->RE_OPEN_DATE;
				$data_array[$i]["RE_OPENED_BY"] = $rows->RE_OPENED_BY;
				$data_array[$i]["RE_OPEN_NOTE"] = $rows->RE_OPEN_NOTE;

				$data_array[$i]["SET_DTLS_ID"] = ($rows->SET_DTLS_ID) ? $rows->SET_DTLS_ID : 0;
				$data_array[$i]["COLOR_SIZE_ID"] = ($rows->COLOR_SIZE_ID) ? $rows->COLOR_SIZE_ID : 0;
				//$data_array[$i]["BYPASS_MP"] = 0;

				//---------------
				if (!empty($gsd_data_arr['OPERATOR'][$data_array[$i]["JOB_NO"]])) {
					$data_array[$i]["EFFICIENCY"] = $gsd_data_arr['EFFICIENCY'][$data_array[$i]["JOB_NO"]];
					$data_array[$i]["TARGET"] = $gsd_data_arr['TARGET'][$data_array[$i]["JOB_NO"]];
					$data_array[$i]["HELPER"] = $gsd_data_arr['HELPER'][$data_array[$i]["JOB_NO"]];
					$data_array[$i]["MACHINE"] = $gsd_data_arr['MACHINE'][$data_array[$i]["JOB_NO"]];
					$data_array[$i]["OPERATOR"] = $gsd_data_arr['OPERATOR'][$data_array[$i]["JOB_NO"]];
					//$data_array[$i]["ALLOCATED_MP"] = $gsd_data_arr['ALLOCATED_MP'][$data_array[$i]["JOB_NO"]];
				} else {
					$data_array[$i]["EFFICIENCY"] = '';
					$data_array[$i]["TARGET"] = '';
					$data_array[$i]["HELPER"] = '';
					$data_array[$i]["MACHINE"] = '';
					$data_array[$i]["OPERATOR"] = '';
					//$data_array[$i]["ALLOCATED_MP"] = 0;
				}

				$i++;

			}
		}

		foreach ($line_plan_qty_total_arr as $line_id => $qty) {
			$line_id_qty_arr[] = array(
				LINE_ID => $line_id,
				QNTY => $qty,
			);
		}

		foreach ($date_plan_qty_total_arr as $date => $qty) {
			$date_qty_arr[] = array(
				DATE => $date,
				QNTY => $qty,
			);
		}

		$dataArr = array('DTLS' => $data_array, 'LINE_WISE_PLAN_QTY' => $line_id_qty_arr, 'DATE_WISE_PLAN_QTY' => $date_qty_arr);

		if (count($plan_data) > 0) {
			//return $data_array;
			return $dataArr;
		} else {
			return 0;
		}

	}

	function get_daywise_plan_data_info($company_id, $po_id, $txt_date_from = "", $txt_date_to = "") {
		$company_cond = " and a.company_id='$company_id'";
		$po_id_cond = " and a.po_break_down_id='$po_id'";
		if ($txt_date_from != "" && $txt_date_to != "") {
			$plan_date = "and b.plan_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
		} else {
			$plan_date = "";
		}
		$daywise_sql = "SELECT b.IS_OFFDAY, b.WORKING_HOUR, a.PO_BREAK_DOWN_ID,a.LINE_ID,a.START_DATE,a.END_DATE,a.PLAN_QNTY,a.FIRST_DAY_OUTPUT,a.INCREMENT_QTY,a.TERGET ,a.COMPANY_ID, a.LOCATION_ID,a.ITEM_NUMBER_ID ,a.OFF_DAY_PLAN,a.ORDER_COMPLEXITY,b.PLAN_DATE,b.PLAN_QNTY,b.PLAN_ID  from ppl_sewing_plan_board a,ppl_sewing_plan_board_dtls b where a.plan_id=b.plan_id and a.is_deleted=0 and a.status_active=1 $po_id_cond $company_cond $plan_date";
		$daywise_plan = $this->db->query($daywise_sql)->result();

		$com_res = $this->db->query("select ID,COMPANY_NAME from lib_company where status_active =1 and is_deleted=0  order by company_name")->result();

		foreach ($com_res as $value) {
			$comp[$value->ID] = $value->COMPANY_NAME;
		}
		$location_res = $this->db->query("select ID,LOCATION_NAME from lib_location  where status_active =1 and is_deleted=0  order by location_name")->result();
		$location_arr[0] = "";
		foreach ($location_res as $value) {
			$location_arr[$value->ID] = $value->LOCATION_NAME;
		}

		$garment_res = $this->db->query("select ID,ITEM_NAME from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name")->result();

		foreach ($garment_res as $value) {
			$garments_item[$value->ID] = $value->ITEM_NAME;
		}

		$i = 0;
		foreach ($daywise_plan as $rows) {
			$data_array[$i]["plan_date"] = date("d-m-Y", strtotime($rows->PLAN_DATE));
			$data_array[$i]["plan_id"] = $rows->PLAN_ID;
			$data_array[$i]["plan_qnty"] = $rows->PLAN_QNTY;
			$data_array[$i]["po_break_down_id"] = $rows->PO_BREAK_DOWN_ID;
			$data_array[$i]["line_id"] = $rows->LINE_ID;
			$data_array[$i]["start_date"] = date("d-m-Y", strtotime($rows->START_DATE));
			$data_array[$i]["end_date"] = date("d-m-Y", strtotime($rows->END_DATE));
			$data_array[$i]["first_day_output"] = $rows->FIRST_DAY_OUTPUT;
			$data_array[$i]["increment_qty"] = $rows->INCREMENT_QTY;
			$data_array[$i]["terget"] = $rows->TERGET;
			$data_array[$i]["company_id"] = $rows->COMPANY_ID;
			$data_array[$i]["company_name"] = $comp[$rows->COMPANY_ID];
			$data_array[$i]["location_id"] = $rows->LOCATION_ID;
			if (isset($location_arr[$rows->LOCATION_ID])) {
				$data_array[$i]["location_name"] = $location_arr[$rows->LOCATION_ID];
			} else {
				$data_array[$i]["location_name"] = "";
			}

			$data_array[$i]["item_number_id"] = $garments_item[$rows->ITEM_NUMBER_ID];
			$data_array[$i]["off_day_plan"] = $rows->OFF_DAY_PLAN;
			$data_array[$i]["order_complexity"] = $rows->ORDER_COMPLEXITY;
			$data_array[$i]["is_offday"] = $rows->IS_OFFDAY;
			$data_array[$i]["working_hour"] = $rows->WORKING_HOUR;
			$i++;
		}

		if (count($daywise_plan) > 0) {
			return $data_array;
		} else {
			return 0;
		}
	}

	function po_vs_plan_info_data($company_id, $txt_date_from = "", $txt_date_to = "") {
		$company_cond = " and a.company_name='$company_id'";
		$company_cond2 = " and a.company_id='$company_id'";
		$plan_date = "";
		$ship_date = "";
		if ($txt_date_from != "" && $txt_date_to != "") {
			$ship_date = " and b.pub_shipment_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
			$plan_date = " and b.plan_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
		}

		$color_size_sql = "SELECT b.ID,sum(c.ORDER_QUANTITY) as QNTY from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and  b.id=c.po_break_down_id and a.status_active=1 and b.is_deleted=0 and c.status_active=1  $company_cond $ship_date group by b.id";
		$color_size_data = $this->db->query($color_size_sql)->result();

		$plan_query = "SELECT c.PO_BREAK_DOWN_ID, sum(b.PLAN_QNTY) as QNTY from  ppl_sewing_plan_board a,ppl_sewing_plan_board_dtls b,ppl_sewing_plan_board_powise c where a.plan_id=b.plan_id and b.plan_id=c.plan_id $company_cond2 $plan_date group by c.PO_BREAK_DOWN_ID ";
		$plan_arr = array();
		foreach (sql_select($plan_query) as $vv) {
			$plan_arr[$vv->PO_BREAK_DOWN_ID] = $vv->QNTY;
		}

		$i = 0;
		foreach ($color_size_data as $rows) {
			$data_array[$i]["ID"] = $rows->ID;
			$data_array[$i]["order_qnty"] = $rows->QNTY;
			if (isset($plan_arr[$rows->ID])) {
				$data_array[$i]["plan_qnty"] = $plan_arr[$rows->ID];
			} else {
				$data_array[$i]["plan_qnty"] = 0;
			}

			$i++;
		}
		$null_arr = array();
		if (count($color_size_data) > 0) {
			return $data_array;
		} else {
			return $null_arr;
		}
	}

	function get_line_po_wise_plan_data_info($company_id, $po_id = "", $line_id, $plan_id) {
		$production_arr = array();
		$comp = array();
		$company_cond = " and a.company_id='$company_id'";
		$po_id_cond = " and a.po_break_down_id in($po_id)";
		$line_id_cond = " and a.line_id='$line_id'";
		$plan_id_cond = " and a.plan_id='$plan_id'";
		$urls = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$url_arr = explode('/', $urls);
		$actual_link = $url_arr[0] . "/" . $url_arr[1];
		$multi_po = 0;
		if (strpos($po_id, ',') !== false) {
			$multi_po = 1;
		}

		$prod_reso_allo = get_resource_allocation_variable($company_id);

		if ($prod_reso_allo == 1) {

			$production_sql = "SELECT a.PO_BREAK_DOWN_ID,sum(a.PRODUCTION_QUANTITY) AS PRODUCTION_QUANTITY,a.PRODUCTION_DATE,b.LINE_NUMBER,b.COMPANY_ID from  pro_garments_production_mst a,prod_resource_mst b where a.sewing_line=b.id and  a.production_type=5 and a.serving_company=$company_id and a.po_break_down_id in($po_id) and b.line_number like '%$line_id%' and a.status_active=1 and a.is_deleted=0 group by a.PO_BREAK_DOWN_ID,a.PRODUCTION_DATE,b.LINE_NUMBER,b.COMPANY_ID order by b.line_number ,a.po_break_down_id,a.production_date";

		} else {
			$production_sql = "SELECT a.PO_BREAK_DOWN_ID,sum(a.PRODUCTION_QUANTITY) AS PRODUCTION_QUANTITY,a.PRODUCTION_DATE,b.id as LINE_NUMBER,b.COMPANY_NAME from  pro_garments_production_mst a,lib_sewing_line b where a.sewing_line=b.id and  a.production_type=5 and a.serving_company=$company_id and a.po_break_down_id in($po_id) and b.id = '$line_id' and a.status_active=1 and a.is_deleted=0 group by a.PO_BREAK_DOWN_ID,a.PRODUCTION_DATE,b.id,b.COMPANY_NAME order by b.id ,a.po_break_down_id,a.production_date";
		}
		//return $production_sql;
		$production_data = $this->db->query($production_sql)->result();

		if (!empty($production_data)) {
			foreach ($production_data as $pr_row) {

				list($LINE_NUMBER) = explode(',', $pr_row->LINE_NUMBER);
				if ($multi_po == 0) {
					$production_arr[$pr_row->PO_BREAK_DOWN_ID][$LINE_NUMBER][$pr_row->PRODUCTION_DATE] = $pr_row->PRODUCTION_QUANTITY;
				} else {
					if (empty($production_arr[0][$LINE_NUMBER][$pr_row->PRODUCTION_DATE])) {
						$production_arr[0][$LINE_NUMBER][$pr_row->PRODUCTION_DATE] = 0;
					}
					$production_arr[0][$LINE_NUMBER][$pr_row->PRODUCTION_DATE] += $pr_row->PRODUCTION_QUANTITY;
				}

			}
		}

		$com_res = $this->db->query("select ID,COMPANY_NAME from lib_company where status_active =1 and is_deleted=0  order by company_name")->result();
		if (!empty($com_res)) {
			foreach ($com_res as $value) {
				$comp[$value->ID] = $value->COMPANY_NAME;
			}
		}
		if (strpos($po_id, ",")) {
			$po_id_cond = "";
		}

		//return $production_arr;

		$daywise_sql = "SELECT b.IS_OFFDAY, b.WORKING_HOUR, a.PO_BREAK_DOWN_ID,a.LINE_ID,a.START_DATE,a.END_DATE,a.PLAN_QNTY,a.FIRST_DAY_OUTPUT,a.INCREMENT_QTY,a.TERGET ,a.COMPANY_ID, a.LOCATION_ID,a.ITEM_NUMBER_ID ,a.OFF_DAY_PLAN,a.ORDER_COMPLEXITY,b.PLAN_DATE,b.PLAN_QNTY,b.PLAN_ID  from ppl_sewing_plan_board a,ppl_sewing_plan_board_dtls b where a.plan_id=b.plan_id and a.is_deleted=0 and a.status_active=1 $po_id_cond $company_cond $line_id_cond $plan_id_cond";
		$daywise_plan = $this->db->query($daywise_sql)->result();
		$i = 0;

		foreach ($daywise_plan as $rows) {
			$production_line_qnty = 0;
			if (!empty($production_arr)) {
				if (isset($production_arr[$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID][$rows->PLAN_DATE])) {
					if ($multi_po == 0) {
						$production_line_qnty = $production_arr[$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID][$rows->PLAN_DATE];
					} else {
						$production_line_qnty = $production_arr[0][$rows->LINE_ID][$rows->PLAN_DATE];
					}

				}
			}

			$data_array[$i]["PLAN_DATE"] = date("d-m-Y", strtotime($rows->PLAN_DATE));
			$data_array[$i]["PLAN_ID"] = $rows->PLAN_ID;
			$data_array[$i]["PLAN_QNTY"] = $rows->PLAN_QNTY;
			$data_array[$i]["PO_BREAK_DOWN_ID"] = $rows->PO_BREAK_DOWN_ID;
			$data_array[$i]["JOB_IMG"] = $this->get_job_images($rows->PO_BREAK_DOWN_ID, $actual_link);

			$data_array[$i]["LINE_ID"] = $rows->LINE_ID;
			//$data_array[$i]["START_DATE"] =date("d-m-Y", strtotime($rows->START_DATE)) ;
			//$data_array[$i]["END_DATE"] =date("d-m-Y", strtotime($rows->END_DATE)) ;
			$data_array[$i]["COMPANY_ID"] = $rows->COMPANY_ID;
			$data_array[$i]["COMPANY_NAME"] = $comp[$rows->COMPANY_ID];
			$data_array[$i]["PRODUCTION_QNTY"] = $production_line_qnty;
			$data_array[$i]["IS_OFFDAY"] = $rows->IS_OFFDAY;
			$data_array[$i]["WORKING_HOUR"] = $rows->WORKING_HOUR;
			$i++;
		}

		if (count($daywise_plan) > 0) {
			return $data_array;
		} else {
			return 0;
		}
	}

	function get_line_list_info($company_id, $location_id = "0", $floor_id = "0") {
		$resource_allocation_type_sql = $this->db->query("select AUTO_UPDATE from variable_settings_production where company_name='$company_id' and variable_list=23 ")->row();
		$resource_allocation_type = 1;
		$location_cond = "";
		$floor_cond_sewing = "";
		$company_cond = " and a.company_id='$company_id'";
		if (($location_id * 1) != "0") {$location_cond = " and a.location_name='$location_id'";} else { $location_cond = "";}

		if (($floor_id * 1) != "0") {
			$floor_cond = " and floor_name='$floor_id'";
		} else { $floor_cond = "";}
		if (($floor_id * 1) != "0") {
			$floor_cond_sewing = " and a.floor_name='$floor_id'";
		} else { $floor_cond_sewing = "";}

		$line_res = $this->db->query("SELECT a.ID as LINE_ID,a.LINE_NAME as LINE_NAME,a.SEWING_LINE_SERIAL,a.LOCATION_NAME as LOCATION_ID, a.FLOOR_NAME as FLOOR_ID ,b.COMPANY_NAME,c.FLOOR_NAME,d.LOCATION_NAME,case when a.MAN_POWER is not null or a.MAN_POWER <> '' then  a.MAN_POWER else 0 end as   MAN_POWER  from lib_sewing_line a,lib_company b,lib_prod_floor c,lib_location d where a.company_name=b.id and b.id=c.company_id and a.FLOOR_NAME=c.id and d.id=c.location_id and d.id=a.location_name and b.id=d.company_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and  a.company_name='$company_id' $location_cond  $floor_cond_sewing group by a.ID ,a.LINE_NAME ,a.SEWING_LINE_SERIAL,a.LOCATION_NAME  , a.FLOOR_NAME  ,b.COMPANY_NAME,c.FLOOR_NAME,d.LOCATION_NAME ,a.MAN_POWER  order by a.FLOOR_NAME,a.sewing_line_serial")->result();
		return $line_res;

	}

	function get_country_list() {
		return $this->db->query("select ID COUNTRY_ID,COUNTRY_NAME from LIB_COUNTRY where status_active=1 and is_deleted=0")->result();
	}

	function get_week_list_info($company_id, $txt_date_from = "", $txt_date_to = "") {
		$company_cond = " and comapny_id='$company_id'";

		if ($txt_date_from == '') {
			$txt_date_from = date('d-m-Y', time());
			$bk_from = $txt_date_from;
			$txt_date_from = add_date($txt_date_from, 60, 0);
		}

		if ($txt_date_to == '') {
			$txt_date_to = add_date($bk_from, 120, 1);
		}

		if ($txt_date_from != "" && $txt_date_to != "") {
			if ($this->db->dbdriver == 'mysqli') {
				$date_calc = "and date_calc between '" . date("Y-m-d", strtotime($txt_date_from)) . "' and '" . date("Y-m-d", strtotime($txt_date_to)) . "'";
			} else {
				$date_calc = "and date_calc between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
			}
		} else {
			$date_calc = "";
		}
		if ($this->db->dbdriver == 'mysqli') {
			$sql = "SELECT a.MST_ID,a.MONTH_ID,DATE_FORMAT(a.date_calc,'%d-%m-%Y') date_calc,case when a.day_status = 2 then 'Closed' else 'Open' end as DAY_STATUS,COMAPNY_ID,CAPACITY_SOURCE,LOCATION_ID from lib_capacity_calc_dtls a, lib_capacity_calc_mst b where b.id=a.mst_id $company_cond $date_calc and day_status=2 and b.status_active=1";
		} else {
			$sql = "SELECT a.MST_ID,a.MONTH_ID,to_char(a.date_calc,'DD-MM-YYYY') DATE_CALC,case when a.day_status = 2 then 'Closed' else 'Open' end as DAY_STATUS,COMAPNY_ID,CAPACITY_SOURCE,LOCATION_ID from lib_capacity_calc_dtls a, lib_capacity_calc_mst b where b.id=a.mst_id $company_cond $date_calc and day_status=2  and b.status_active=1";
		}
		$sql_data = $this->db->query($sql)->result();
		return $sql_data;

	}

	function get_efficiency_percentage_slab_list_info($company_id = "") {
		if ($company_id != "") {
			$company_cond = " and a.company_id='$company_id'";
		} else {
			$company_cond = '';
		}

		$sql = "select A.COMPANY_ID,A.LOCATION_ID,A.GMTS_ITEM_ID,A.BUYER_ID,B.SMV_LOWER_LIMIT,B.SMV_UPPER_LIMIT,B.LEARNING_CUB_PERCENTAGE from efficiency_percentage_slab_mst a, efficiency_percentage_slab_dtl b where a.id=b.mst_id $company_cond  and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0";
		$sql_data = $this->db->query($sql)->result();
		return $sql_data;

	}

	function get_tna_info($po_id) {
		$po_cond = " and po_number_id='$po_id'";
		$sql = "select min(task_start_date) as TASK_START_DATE, max(task_finish_date) as TASK_FINISH_DATE, PO_NUMBER_ID  from tna_process_mst where is_deleted=0 and status_active=1  $po_cond and task_number in(86,190,191)   group by po_number_id ";
		$sql_data = $this->db->query($sql)->result();
		$sel_pos = "";
		$i = 0;
		foreach ($sql_data as $row) {
			$tna_task_data[$i]['PO_NUMBER_ID'] = $row->PO_NUMBER_ID;
			$tna_task_data[$i]['TASK_START_DATE'] = date("d-m-Y", strtotime($row->TASK_START_DATE));
			$tna_task_data[$i]['TASK_FINISH_DATE'] = date("d-m-Y", strtotime($row->TASK_FINISH_DATE));
			if ($sel_pos == "") {
				$sel_pos = $row->PO_NUMBER_ID;
			} else {
				$sel_pos .= "," . $row->PO_NUMBER_ID;
			}

			$i++;
		}
		if ($sel_pos == "") {
			return array('errorMsg' => 'Sorry! No PO found for planning in TNA process.');
		}
		return $tna_task_data;

	}

	function get_production_data_info($company_id, $po_id) {
		$company_cond = " and company_id='$company_id'";
		$po_cond = " and po_break_down_id='$po_id'";
		$comp_sql = "select ID,COMPANY_NAME from lib_company comp where status_active =1 and is_deleted=0  order by company_name";
		$loc_sql = "select ID,LOCATION_NAME from lib_location where status_active =1 and is_deleted=0 order by location_name";
		$line_sql = "select ID,LINE_NAME from lib_sewing_line where status_active= 1 and is_deleted = 0 order by sewing_line_serial ";
		$com_res = $this->db->query($comp_sql)->result();
		$loc_res = $this->db->query($loc_sql)->result();
		$line_res = $this->db->query($line_sql)->result();
		foreach ($com_res as $value) {
			$data_arr['company_info'][$value->ID] = $value->COMPANY_NAME;
		}

		foreach ($loc_res as $value) {
			$data_arr['location_info'][$value->ID] = $value->LOCATION_NAME;
		}

		foreach ($line_res as $value) {
			$data_arr['line_info'][$value->ID] = $value->LINE_NAME;
		}

		$production_sql = "select PO_BREAK_DOWN_ID,SUM(PRODUCTION_QUANTITY) AS PRODUCTION_QUANTITY,PRODUCTION_DATE,SEWING_LINE,COMPANY_ID,LOCATION from   pro_garments_production_mst where production_type=5 $po_cond  $company_cond  and status_active=1 and is_deleted=0     group by production_date,po_break_down_id,sewing_line, company_id,location order by sewing_line,po_break_down_id,production_date";
		$production_data = $this->db->query($production_sql)->result();

		$i = 0;
		foreach ($production_data as $rows) {
			$data_array[$i]["po_break_down_id"] = $rows->PO_BREAK_DOWN_ID;
			$data_array[$i]["sewing_line"] = $data_arr['line_info'][$rows->SEWING_LINE];
			$data_array[$i]["company_id"] = $rows->COMPANY_ID;
			$data_array[$i]["company_name"] = $data_arr['company_info'][$rows->COMPANY_ID];
			$data_array[$i]["location_id"] = $rows->LOCATION;
			$data_array[$i]["location_name"] = $data_arr['location_info'][$rows->LOCATION];
			$data_array[$i]["production_date"] = date("d-m-Y", strtotime($rows->PRODUCTION_DATE));
			$i++;
		}
		return $data_array;
	}

/**
 * [create_plan for Plan CRUD]
 * @param  [object] $plan_obj [description]
 * @return [array]           [description]
 */
	function create_plan($plan_obj) {
		$response_obj = json_decode($plan_obj);
		//echo $response_obj->Status;die;
		if ($response_obj->Status == 1) {
			//echo "i am here";die;
			$this->db->trans_start();
			$plan_to_delete = "";
			$plan_ids = array();
			$plan_id = $this->get_max_value("PPL_SEWING_PLAN_BOARD", "PLAN_ID") + 1;
			$max_id = $this->get_max_value("PPL_SEWING_PLAN_BOARD", "ID") + 1;
			$tmp_plan = 0;
			$index = 0;
			foreach ($response_obj->SewingPlanBoard as $sewing_plan_row) {
				if (!isset($sewing_plan_row->MERGE_TYPE)) {
					$sewing_plan_row->MERGE_TYPE = '';
				}

				if (!isset($sewing_plan_row->MERGE_COMMENTS)) {
					$sewing_plan_row->MERGE_COMMENTS = '';
				}

				if ($this->db->dbdriver == 'mysqli') {
					$ppl_sewing_plan_board_data = array(
						'LINE_ID' => $sewing_plan_row->LINE_ID,
						'PO_BREAK_DOWN_ID' => $sewing_plan_row->PO_BREAK_DOWN_ID,
						'START_DATE' => date("Y-m-d", strtotime($sewing_plan_row->START_DATE)),
						'START_HOUR' => $sewing_plan_row->START_HOUR,
						'REMAINING_WORK_HOUR' => $sewing_plan_row->REMAINING_WORK_HOUR,
						'END_DATE' => date("Y-m-d", strtotime($sewing_plan_row->END_DATE)),
						'END_HOUR' => $sewing_plan_row->END_HOUR,
						'DURATION' => $sewing_plan_row->DURATION,
						'PLAN_QNTY' => $sewing_plan_row->PLAN_QNTY,
						'COMP_LEVEL' => $sewing_plan_row->COMP_LEVEL,
						'FIRST_DAY_OUTPUT' => $sewing_plan_row->FIRST_DAY_OUTPUT,
						'NEXT_FIRST_DAY_OUTPUT' => $sewing_plan_row->NEXT_FIRST_DAY_OUTPUT,
						'NEXT_INCREMENT' => $sewing_plan_row->NEXT_INCREMENT,
						'NEXT_TERGET' => $sewing_plan_row->NEXT_TERGET,
						'LEARING_ITERATOR' => $sewing_plan_row->LEARING_ITERATOR,
						'INCREMENT_QTY' => $sewing_plan_row->INCREMENT_QTY,
						'TERGET' => $sewing_plan_row->TERGET,
						'INSERTED_BY' => $sewing_plan_row->INSERTED_BY,
						'COMPANY_ID' => $sewing_plan_row->COMPANY_ID,
						'LOCATION_ID' => $sewing_plan_row->LOCATION_ID,
						'ITEM_NUMBER_ID' => $sewing_plan_row->ITEM_NUMBER_ID,
						'OFF_DAY_PLAN' => $sewing_plan_row->OFF_DAY_PLAN,
						'ORDER_COMPLEXITY' => $sewing_plan_row->ORDER_COMPLEXITY,
						'SHIP_DATE' => date("Y-m-d", strtotime($sewing_plan_row->SHIP_DATE)),
						//'SHIP_DATE' => $sewing_plan_row->SHIP_DATE,
						'PLAN_LEVEL' => $sewing_plan_row->PLAN_LEVEL,
						'PO_LEVEL' => $sewing_plan_row->PO_LEVEL,
						'SEQ_NO' => $sewing_plan_row->SEQ_NO,
						'HALF' => $sewing_plan_row->HALF,

						'CLOSING_STATUS' => $sewing_plan_row->CLOSING_STATUS,
						'CLOSED_BY' => $sewing_plan_row->CLOSED_BY,
						'CLOSING_DATE' => ($sewing_plan_row->CLOSING_DATE != '') ? date("Y-m-d", strtotime($sewing_plan_row->CLOSING_DATE)) : '',
						'CLOSING_NOTE' => $sewing_plan_row->CLOSING_NOTE,

						'RE_OPEN_DATE' => ($sewing_plan_row->RE_OPEN_DATE != '') ? date("Y-m-d", strtotime($sewing_plan_row->RE_OPEN_DATE)) : '',
						'RE_OPENED_BY' => $sewing_plan_row->RE_OPENED_BY,
						'RE_OPEN_NOTE' => ($sewing_plan_row->RE_OPEN_NOTE == '') ? "" : $sewing_plan_row->RE_OPEN_NOTE,

						'NOTES' => ($sewing_plan_row->NOTEFORSTRIP == '') ? '' : $sewing_plan_row->NOTEFORSTRIP,
						'PO_COMPANY_ID' => $sewing_plan_row->PO_COMPANY_ID,
						'MERGE_TYPE' => ($sewing_plan_row->MERGE_TYPE == '' ? "" : $sewing_plan_row->MERGE_TYPE),
						'PREV_PLAN_ID' => ($sewing_plan_row->PREV_PLAN_ID == '' ? "0" : $tmp_plan),
						'MERGE_COMMENTS' => ($sewing_plan_row->MERGE_COMMENTS == '' ? "" : $sewing_plan_row->MERGE_COMMENTS),
						'SET_DTLS_ID' => ($sewing_plan_row->SET_DTLS_ID) ? $sewing_plan_row->SET_DTLS_ID : 0,
						'COLOR_SIZE_ID' => ($sewing_plan_row->COLOR_SIZE_ID) ? $sewing_plan_row->COLOR_SIZE_ID : 0,

						'ALLOCATED_MP' => ($sewing_plan_row->ALLOCATED_MP) ? $sewing_plan_row->ALLOCATED_MP : 0,
						'BYPASS_MP' => ($sewing_plan_row->BYPASS_MP) ? $sewing_plan_row->BYPASS_MP : 0,
						'AUTO_TARGET' => ($sewing_plan_row->AUTO_TARGET) ? $sewing_plan_row->AUTO_TARGET : 0,

					);
					//'FIRST_DAY_CAPACITY,LAST_DAY_CAPACITY,SEQ_NO,PO_COMPANY_ID';
				} else {
					$ppl_sewing_plan_board_data = array(
						'LINE_ID' => $sewing_plan_row->LINE_ID,
						'PO_BREAK_DOWN_ID' => $sewing_plan_row->PO_BREAK_DOWN_ID,
						'START_DATE' => date("d-M-Y", strtotime($sewing_plan_row->START_DATE)),
						'START_HOUR' => $sewing_plan_row->START_HOUR,
						'REMAINING_WORK_HOUR' => $sewing_plan_row->REMAINING_WORK_HOUR,
						'END_DATE' => date("d-M-Y", strtotime($sewing_plan_row->END_DATE)),
						'END_HOUR' => $sewing_plan_row->END_HOUR,
						'DURATION' => $sewing_plan_row->DURATION,
						'PLAN_QNTY' => $sewing_plan_row->PLAN_QNTY,
						'COMP_LEVEL' => $sewing_plan_row->COMP_LEVEL,
						'NEXT_FIRST_DAY_OUTPUT' => $sewing_plan_row->NEXT_FIRST_DAY_OUTPUT,
						'NEXT_INCREMENT' => $sewing_plan_row->NEXT_INCREMENT,
						'NEXT_TERGET' => $sewing_plan_row->NEXT_TERGET,
						'LEARING_ITERATOR' => $sewing_plan_row->LEARING_ITERATOR,
						'FIRST_DAY_OUTPUT' => $sewing_plan_row->FIRST_DAY_OUTPUT,
						'INCREMENT_QTY' => $sewing_plan_row->INCREMENT_QTY,
						'TERGET' => $sewing_plan_row->TERGET,
						'INSERTED_BY' => $sewing_plan_row->INSERTED_BY,
						'COMPANY_ID' => $sewing_plan_row->COMPANY_ID,
						'LOCATION_ID' => $sewing_plan_row->LOCATION_ID,
						'ITEM_NUMBER_ID' => $sewing_plan_row->ITEM_NUMBER_ID,
						'OFF_DAY_PLAN' => $sewing_plan_row->OFF_DAY_PLAN,
						'ORDER_COMPLEXITY' => $sewing_plan_row->ORDER_COMPLEXITY,
						'SHIP_DATE' => date("d-M-Y", strtotime($sewing_plan_row->SHIP_DATE)),
						//'SHIP_DATE' => $sewing_plan_row->SHIP_DATE,
						'PLAN_LEVEL' => $sewing_plan_row->PLAN_LEVEL,
						'PO_LEVEL' => $sewing_plan_row->PO_LEVEL,
						'SEQ_NO' => $sewing_plan_row->SEQ_NO,
						'HALF' => $sewing_plan_row->HALF,

						'CLOSING_STATUS' => $sewing_plan_row->CLOSING_STATUS,
						'CLOSED_BY' => ($sewing_plan_row->CLOSED_BY == '') ? 0 : $sewing_plan_row->CLOSED_BY,
						'CLOSING_DATE' => ($sewing_plan_row->CLOSING_DATE != '') ? date("d-M-Y", strtotime($sewing_plan_row->CLOSING_DATE)) : '',
						'CLOSING_NOTE' => ($sewing_plan_row->CLOSING_NOTE == "") ? "" : $sewing_plan_row->CLOSING_NOTE,
						'RE_OPEN_DATE' => ($sewing_plan_row->RE_OPEN_DATE != '') ? date("d-M-Y", strtotime($sewing_plan_row->RE_OPEN_DATE)) : '',
						'RE_OPENED_BY' => ($sewing_plan_row->RE_OPENED_BY == '') ? 0 : $sewing_plan_row->RE_OPENED_BY,
						'RE_OPEN_NOTE' => ($sewing_plan_row->RE_OPEN_NOTE == '') ? "" : $sewing_plan_row->RE_OPEN_NOTE,

						'NOTES' => ($sewing_plan_row->NOTEFORSTRIP == '') ? '' : $sewing_plan_row->NOTEFORSTRIP,
						'PO_COMPANY_ID' => $sewing_plan_row->PO_COMPANY_ID,
						'MERGE_TYPE' => (trim($sewing_plan_row->MERGE_TYPE) == '' ? "" : $sewing_plan_row->MERGE_TYPE),
						'PREV_PLAN_ID' => ($sewing_plan_row->MERGE_TYPE == '' ? "0" : $tmp_plan),
						'MERGE_COMMENTS' => (trim($sewing_plan_row->MERGE_COMMENTS) == '' ? "" : $sewing_plan_row->MERGE_COMMENTS),

						'SET_DTLS_ID' => ($sewing_plan_row->SET_DTLS_ID) ? $sewing_plan_row->SET_DTLS_ID : 0,
						'COLOR_SIZE_ID' => ($sewing_plan_row->COLOR_SIZE_ID) ? $sewing_plan_row->COLOR_SIZE_ID : 0,
						'ALLOCATED_MP' => ($sewing_plan_row->ALLOCATED_MP) ? $sewing_plan_row->ALLOCATED_MP : 0,
						'BYPASS_MP' => ($sewing_plan_row->BYPASS_MP) ? $sewing_plan_row->BYPASS_MP : 0,
						'AUTO_TARGET' => ($sewing_plan_row->AUTO_TARGET) ? $sewing_plan_row->AUTO_TARGET : 0,

					);
					//'FIRST_DAY_CAPACITY,LAST_DAY_CAPACITY,SEQ_NO,PO_COMPANY_ID';
				}

				//return $ppl_sewing_plan_board_data;

				if ($sewing_plan_row->RowState == "add") {
					if ($this->db->dbdriver == 'mysqli') {
						$ppl_sewing_plan_board_data['PLAN_ID'] = $plan_id;
						$ppl_sewing_plan_board_data['ID'] = $max_id++;
						$ppl_sewing_plan_board_data['INSERTED_BY'] = $sewing_plan_row->INSERTED_BY;
						$ppl_sewing_plan_board_data['INSERT_DATE'] = date("Y-m-d");
						$this->insertData($ppl_sewing_plan_board_data, "PPL_SEWING_PLAN_BOARD");
						$plan_ids[$sewing_plan_row->PLAN_ID] = $plan_id;
					} else {
						$ppl_sewing_plan_board_data['PLAN_ID'] = $plan_id;
						$ppl_sewing_plan_board_data['ID'] = $max_id++;
						$ppl_sewing_plan_board_data['INSERTED_BY'] = $sewing_plan_row->INSERTED_BY;
						$ppl_sewing_plan_board_data['INSERT_DATE'] = date("d-M-Y");
						$this->insertData($ppl_sewing_plan_board_data, "PPL_SEWING_PLAN_BOARD");
						$plan_ids[$sewing_plan_row->PLAN_ID] = $plan_id;
					}
				} else if ($sewing_plan_row->RowState == "update") {
					if ($this->db->dbdriver == 'mysqli') {
						$plan_ids[$sewing_plan_row->PLAN_ID] = $sewing_plan_row->PLAN_ID;
						$ppl_sewing_plan_board_data['UPDATE_DATE'] = date("Y-m-d");
						$ppl_sewing_plan_board_data['UPDATED_BY'] = $sewing_plan_row->UPDATED_BY;
						$plan_to_delete .= $sewing_plan_row->PLAN_ID . ",";
						$this->updateData('PPL_SEWING_PLAN_BOARD', $ppl_sewing_plan_board_data, array('PLAN_ID' => $sewing_plan_row->PLAN_ID));
					} else {
						$plan_ids[$sewing_plan_row->PLAN_ID] = $sewing_plan_row->PLAN_ID;
						$ppl_sewing_plan_board_data['UPDATE_DATE'] = date("d-M-Y");
						$ppl_sewing_plan_board_data['UPDATED_BY'] = $sewing_plan_row->UPDATED_BY;
						$plan_to_delete .= $sewing_plan_row->PLAN_ID . ",";
						$this->updateData('PPL_SEWING_PLAN_BOARD', $ppl_sewing_plan_board_data, array('PLAN_ID' => $sewing_plan_row->PLAN_ID));
					}
				} else if ($sewing_plan_row->RowState == "delete") {
					$plan_to_delete .= $sewing_plan_row->PLAN_ID . ",";
					$this->deleteRowByAttribute('PPL_SEWING_PLAN_BOARD', array('PLAN_ID' => $sewing_plan_row->PLAN_ID));
				}
				if ($index == 0) {
					$tmp_plan = $plan_id;
				}

				$plan_id++;
				$index++;

			}

			// return $ppl_sewing_plan_board_data;

			$plan_to_delete = rtrim($plan_to_delete, ",");
			if ($plan_to_delete != "") {
				// delete all child table rows by PLAN_ID
				$this->db->query("delete from PPL_SEWING_PLAN_BOARD_DTLS where PLAN_ID in($plan_to_delete)");
				$this->db->query("delete from PPL_SEWING_PLAN_BOARD_POWISE where PLAN_ID in($plan_to_delete)");
			}
			//MERGE_TYPE,MERGE_COMMENTS
			$max_plan_dtls_id = $this->get_max_value("PPL_SEWING_PLAN_BOARD_DTLS", "ID") + 1;
			$dtls_plan_delete = "";
			foreach ($response_obj->SewingPlanBoardDtls as $sewing_plan_dtls_row) {
				if ($sewing_plan_dtls_row->RowState == "delete" || $sewing_plan_dtls_row->RowState == "update") {
					if ($dtls_plan_delete) {
						$dtls_plan_delete .= ',' . $sewing_plan_dtls_row->PLAN_ID;
					} else {
						$dtls_plan_delete .= $sewing_plan_dtls_row->PLAN_ID;
					}

				}

			}
			if ($dtls_plan_delete) {
				$this->db->query("delete from  ppl_sewing_plan_board_dtls where plan_id in($dtls_plan_delete)");
			}

			foreach ($response_obj->SewingPlanBoardDtls as $sewing_plan_dtls_row) {
				if ($sewing_plan_dtls_row->RowState != "delete") {

					if ($this->db->dbdriver == 'mysqli') {
						$planning_id = ($sewing_plan_dtls_row->RowState == "add") ? $plan_ids[$sewing_plan_dtls_row->PLAN_ID] : $sewing_plan_dtls_row->PLAN_ID;
						$ppl_sewing_plan_board_dtls_data = array(
							'ID' => $max_plan_dtls_id++,
							'PLAN_ID' => $planning_id,
							'PLAN_DATE' => date("Y-m-d", strtotime($sewing_plan_dtls_row->PLAN_DATE)),
							'PLAN_QNTY' => $sewing_plan_dtls_row->PLAN_QNTY,
							'IS_OFFDAY' => $sewing_plan_dtls_row->isOffDay,
							'WORKING_HOUR' => $sewing_plan_dtls_row->workHour,
						);
						$this->insertData($ppl_sewing_plan_board_dtls_data, "PPL_SEWING_PLAN_BOARD_DTLS");
					} else {
						$planning_id = ($sewing_plan_dtls_row->RowState == "add") ? $plan_ids[$sewing_plan_dtls_row->PLAN_ID] : $sewing_plan_dtls_row->PLAN_ID;
						$ppl_sewing_plan_board_dtls_data = array(
							'ID' => $max_plan_dtls_id++,
							'PLAN_ID' => $planning_id,
							'PLAN_DATE' => date("d-M-Y", strtotime($sewing_plan_dtls_row->PLAN_DATE)),
							'PLAN_QNTY' => $sewing_plan_dtls_row->PLAN_QNTY,
							'IS_OFFDAY' => $sewing_plan_dtls_row->isOffDay,
							'WORKING_HOUR' => $sewing_plan_dtls_row->workHour,
						);
						$this->insertData($ppl_sewing_plan_board_dtls_data, "PPL_SEWING_PLAN_BOARD_DTLS");
					}
				}
			}
			//print_r($ppl_sewing_plan_board_data);
			//die;
			$max_plan_po_id = $this->get_max_value("PPL_SEWING_PLAN_BOARD_POWISE", "ID") + 1;

			if ($this->db->dbdriver == 'mysqli') {$dateFormat = "Y-m-d";} else { $dateFormat = "d-M-Y";}

			foreach ($response_obj->SewingPlanBoardPOWise as $sewing_plan_po_row) {
				if ($sewing_plan_po_row->RowState != "delete") {
					$planning_id = ($sewing_plan_po_row->RowState == "add") ? $plan_ids[$sewing_plan_po_row->PLAN_ID] : $sewing_plan_po_row->PLAN_ID;
					$ppl_sewing_plan_board_po_data = array(
						'ID' => $max_plan_po_id++,
						'PLAN_ID' => $planning_id,
						'PO_BREAK_DOWN_ID' => $sewing_plan_po_row->PO_BREAK_DOWN_ID,
						'PLAN_QNTY' => $sewing_plan_po_row->PLAN_QNTY,
						'ITEM_NUMBER_ID' => $sewing_plan_po_row->ITEM_NUMBER_ID,
						'COLOR_NUMBER_ID' => $sewing_plan_po_row->COLOR_NUMBER_ID,
						'PUB_SHIPMENT_DATE' => date($dateFormat, strtotime($sewing_plan_po_row->PUB_SHIPMENT_DATE)),

						'SIZE_NUMBER_ID' => $sewing_plan_po_row->SIZE_NUMBER_ID,
						'JOB_NO' => $sewing_plan_po_row->JOB_NO,
						'SMV' => $sewing_plan_po_row->SMV,
						'COUNTRY_ID' => $sewing_plan_po_row->COUNTRY_ID,
						'SET_DTLS_ID' => ($sewing_plan_po_row->SET_DTLS_ID) ? $sewing_plan_po_row->SET_DTLS_ID : 0,
						'COLOR_SIZE_ID' => ($sewing_plan_po_row->COLOR_SIZE_ID) ? $sewing_plan_po_row->COLOR_SIZE_ID : 0,

					);
					$this->insertData($ppl_sewing_plan_board_po_data, "PPL_SEWING_PLAN_BOARD_POWISE");
				}
			}

			$line_capacity_id = $this->get_max_value("LINE_CAPACITY", "ID") + 1;
			foreach ($response_obj->LineCapacity as $line_capacity_row) {

				if ($this->db->dbdriver == 'mysqli') {
					$date_format = "Y-m-d";
				} else {
					$date_format = "d-M-Y";
				}

				if ($line_capacity_row->STATE == "Add") {
					$line_capacity_data_arr = array(
						'ID' => $line_capacity_id++,
						'LINE_ID' => $line_capacity_row->LINE_ID,
						'EXTEND_HOUR' => $line_capacity_row->EXTEND_HOUR,
						'EXTEND_DATE' => date($date_format, strtotime($line_capacity_row->EXTEND_DATE)),
						'STATE' => $line_capacity_row->STATE,
					);
					//$this->db->query("delete from LINE_CAPACITY where LINE_ID=".$line_capacity_row->LINE_ID." and EXTEND_DATE='".date($date_format,strtotime($line_capacity_row->EXTEND_DATE))."'");
					$this->insertData($line_capacity_data_arr, "LINE_CAPACITY");
				} else if ($line_capacity_row->STATE == "Update") {
					$line_capacity_data_arr = array(
						'LINE_ID' => $line_capacity_row->LINE_ID,
						'EXTEND_HOUR' => $line_capacity_row->EXTEND_HOUR,
						'EXTEND_DATE' => date($date_format, strtotime($line_capacity_row->EXTEND_DATE)),
						'STATE' => $line_capacity_row->STATE,
					);

					$this->updateData('LINE_CAPACITY', $line_capacity_data_arr, array('ID' => $line_capacity_row->ID));
				}

			}

			$this->db->trans_complete();
			if ($this->db->trans_status() == TRUE) {
				return $resultset["status"] = "Successful";
			} else {
				$resultset["status"] = "34Failed";
			}
		} else {
			return $resultset["status"] = "Failed";
		}
	}

	public function get_archive_info($company = 0, $location = 0, $floor = 0, $generate_date = "") {
		$month_year = explode("-", date("Y-m-d", strtotime($generate_date)));
		$month_format = "'" . $month_year[1] . "-" . $month_year[0] . "'";

		$month_check = return_field_value("count(plan_id) as plan_id ", "ppl_sewing_plan_board_arc", "MONTH_FORMAT=$month_format and  status_active=1 group by plan_id", "plan_id");

		if ($month_check > 0) {
			return false;
		}

		$d = cal_days_in_month(CAL_GREGORIAN, $month_year[1], $month_year[0]);
		$generate_date_from = "01-" . $month_year[1] . "-" . $month_year[0];
		$generate_date_to = $d . "-" . $month_year[1] . "-" . $month_year[0];
		$all_cond = "";
		if ($company) {
			$all_cond .= " and company_id ='$company'";
		}

		if ($location) {
			$all_cond .= " and location_id ='$location'";
		}

		//if($floor)$all_cond.=" and company_id ='$company'";

		if ($this->db->dbdriver == 'mysqli') {
			$generate_date_cond = " and ( start_date between '" . date("Y-m-d", strtotime($generate_date_from)) . "' and '" . date("Y-m-d", strtotime($generate_date_to)) . "' or end_date between '" . date("Y-m-d", strtotime($generate_date_from)) . "' and '" . date("Y-m-d", strtotime($generate_date_to)) . "')";
		} else {
			$generate_date_cond = " and ( start_date between '" . date("d-M-Y", strtotime($generate_date_from)) . "' and '" . date("d-M-Y", strtotime($generate_date_to)) . "' or end_date between '" . date("d-M-Y", strtotime($generate_date_from)) . "' and '" . date("d-M-Y", strtotime($generate_date_to)) . "')";
		}
		$plan_mst = "SELECT $month_format ,  PO_BREAK_DOWN_ID, LINE_ID,    PLAN_ID, START_DATE, START_HOUR,    END_DATE, END_HOUR, DURATION,    PLAN_QNTY, COMP_LEVEL, FIRST_DAY_OUTPUT,    INCREMENT_QTY, TERGET, INSERTED_BY,    INSERT_DATE, UPDATED_BY, UPDATE_DATE,    STATUS_ACTIVE, IS_DELETED, DAY_WISE_PLAN,    COMPANY_ID, LOCATION_ID, ITEM_NUMBER_ID,    OFF_DAY_PLAN, ORDER_COMPLEXITY, SHIP_DATE,    EXTRA_PARAM, PLAN_LEVEL, FIRST_DAY_CAPACITY,    LAST_DAY_CAPACITY, SEQ_NO, PO_COMPANY_ID,    USE_LEARNING_CURVE, CURRENT_PRODUCTION_DATE, PRODUCTION_PERCENT,    TOP_BORDER_COLOR, BOTTOM_BORDER_COLOR, LEFT_COLOR,    RIGHT_COLOR, MERGE_TYPE, MERGE_COMMENTS,    HALF from ppl_sewing_plan_board where status_active=1 $generate_date_cond $all_cond";

		$mst_res = $this->db->query("INSERT INTO ppl_sewing_plan_board_arc(MONTH_FORMAT , PO_BREAK_DOWN_ID, LINE_ID,    PLAN_ID, START_DATE, START_HOUR,    END_DATE, END_HOUR, DURATION,    PLAN_QNTY, COMP_LEVEL, FIRST_DAY_OUTPUT,    INCREMENT_QTY, TERGET, INSERTED_BY,    INSERT_DATE, UPDATED_BY, UPDATE_DATE,    STATUS_ACTIVE, IS_DELETED, DAY_WISE_PLAN,    COMPANY_ID, LOCATION_ID, ITEM_NUMBER_ID,    OFF_DAY_PLAN, ORDER_COMPLEXITY, SHIP_DATE,    EXTRA_PARAM, PLAN_LEVEL, FIRST_DAY_CAPACITY,    LAST_DAY_CAPACITY, SEQ_NO, PO_COMPANY_ID,    USE_LEARNING_CURVE, CURRENT_PRODUCTION_DATE, PRODUCTION_PERCENT,    TOP_BORDER_COLOR, BOTTOM_BORDER_COLOR, LEFT_COLOR,    RIGHT_COLOR, MERGE_TYPE, MERGE_COMMENTS,    HALF) $plan_mst");

		// return 23;

		$all_plan_array = array();
		$plan_mst_sql = sql_select($plan_mst);
		foreach ($plan_mst_sql as $val) {
			$all_plan_array[$val->PLAN_ID] = $val->PLAN_ID;
		}
		if ($mst_res) {

			foreach ($all_plan_array as $plan_id) {
				$plan_dtls = "SELECT  PLAN_ID, PLAN_DATE, PLAN_QNTY from ppl_sewing_plan_board_dtls where plan_id='$plan_id'";
				$this->db->query("INSERT INTO ppl_sewing_plan_board_dtls_arc( PLAN_ID, PLAN_DATE, PLAN_QNTY) $plan_dtls");

				$plan_po = "SELECT   PLAN_ID, PO_BREAK_DOWN_ID,   PLAN_QNTY, PO_DTLS, ITEM_NUMBER_ID,   COUNTRY_ID, COLOR_NUMBER_ID, SIZE_NUMBER_ID,  JOB_NO, PO_ID from ppl_sewing_plan_board_powise where plan_id='$plan_id'";
				$this->db->query("INSERT INTO ppl_sewing_plan_board_po_arc( PLAN_ID, PO_BREAK_DOWN_ID,   PLAN_QNTY, PO_DTLS, ITEM_NUMBER_ID,   COUNTRY_ID, COLOR_NUMBER_ID, SIZE_NUMBER_ID,  JOB_NO, PO_ID) $plan_po");

			}
		}
		return true;

	}

	function get_allocation_data_info($fromDate, $toDate, $company, $location, $type) {
		if ($fromDate && $toDate) {
			$date = " and b.date_name between '" . date('d-M-Y', strtotime($fromDate)) . "' and '" . date('d-M-Y', strtotime($toDate)) . "'";
		} else {
			$date = "";
		}

		if ($company) {
			$companycond = " and a.company_id = '" . $company . "'";
		} else {
			$companycond = "";
		}

		$loccond = "";
		if ($location) {
			$loccond = " and a.location_name = '" . $location . "'";
		} else {
			$loccond = "";
		}

		$sql = $this->db->query("
        SELECT a.ID,a.COMPANY_ID, b.DATE_NAME,b.QTY,b.SMV from  ppl_order_allocation_mst a, ppl_order_allocation_dtls b where a.id=b.mst_id $companycond $date $loccond order by b.date_name");
		$data = array();
		foreach ($sql->result() as $row) {
			if ($type == 1) {
				$date_format = date("d-m-Y", strtotime($row->DATE_NAME));
				if (isset($data[$date_format]["qty"])) {
					$data[$date_format]["qty"] += $row->QTY;
				} else {
					$data[$date_format]["qty"] = $row->QTY;
				}

				if (isset($data[$date_format]["smv"])) {
					$data[$date_format]["smv"] += $row->SMV;
				} else {
					$data[$date_format]["smv"] = $row->SMV;
				}

			}
			if ($type == 2) {
				$date = new DateTime($row->DATE_NAME);
				$week = $date->format("W");
				if (isset($data[$week]["qty"])) {
					$data[$week]["qty"] += $row->QTY;
				} else {
					$data[$week]["qty"] = $row->QTY;
				}

				if (isset($data[$week]["smv"])) {
					$data[$week]["smv"] += $row->SMV;
				} else {
					$data[$week]["smv"] = $row->SMV;
				}

			}
			if ($type == 3) {
				if (isset($data[date("M-Y", strtotime($row->DATE_NAME))]["qty"])) {
					$data[date("M-Y", strtotime($row->DATE_NAME))]["qty"] += $row->QTY;
				} else {
					$data[date("M-Y", strtotime($row->DATE_NAME))]["qty"] = $row->QTY;
				}

				if (isset($data[date("M-Y", strtotime($row->DATE_NAME))]["smv"])) {
					$data[date("M-Y", strtotime($row->DATE_NAME))]["smv"] += $row->SMV;
				} else {
					$data[date("M-Y", strtotime($row->DATE_NAME))]["smv"] = $row->SMV;
				}

			}
		}
		$k = 0;
		$data_arr = array();
		if (count($data) > 0) {
			foreach ($data as $key => $val) {
				$data_arr[$k]["date"] = $key;
				$data_arr[$k]["qty"] = $val["qty"];
				$data_arr[$k]["smv"] = $val["smv"];
				$k++;
			}

		}
		return $data;

	}

	
	
	function get_capacity_data_info($fromDate, $toDate, $company, $location, $type) {
		if ($fromDate && $toDate) {

			if ($this->db->dbdriver == 'mysqli') {
				$date = " and b.date_calc between '" . date("Y-m-d", strtotime($fromDate)) . "' and '" . date("Y-m-d", strtotime($toDate)) . "'";

			} else {
				$date = " and b.date_calc between '" . date('d-M-Y', strtotime($fromDate)) . "' and '" . date('d-M-Y', strtotime($toDate)) . "'";
			}

		} else {
			$date = "";
		}

		if ($company) {
			$companycond = " and a.comapny_id = '" . $company . "'";
		} else {
			$companycond = "";
		}
		$loccond = "";
		if ($location) {
			$loccond = " and a.location_id = '" . $location . "'";
		} else {
			$loccond = "";
		}
		$allocation_data = $this->get_allocation_data_info($fromDate, $toDate, $company, $location, $type);
		//return $allocation_data;

		$rows = array();
		$sql = $this->db->query("
        SELECT a.ID,a.COMAPNY_ID, b.ID,B.MST_ID,b.MONTH_ID,b.DATE_CALC,b.DAY_STATUS,b.NO_OF_LINE,b.CAPACITY_MIN,b.CAPACITY_PCS from  lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id $loccond $companycond $date order by b.date_calc");
		$data = array();
		$data_qty = array();
		$data_smv = array();
		foreach ($sql->result() as $row) {
			if ($type == 1) {
				$date_format = date("d-m-Y", strtotime($row->DATE_CALC));
				if (isset($data[$date_format])) {
					$data[$date_format] += $row->CAPACITY_PCS;
					if (isset($allocation_data[$date_format]["qty"])) {
						$data_qty[$date_format] += $allocation_data[$date_format]["qty"];
					}
					if (isset($allocation_data[$date_format]["smv"])) {
						$data_smv[$date_format] += $allocation_data[$date_format]["smv"];
					}

				} else {
					$data[$date_format] = $row->CAPACITY_PCS;
					$data[$date_format] = $row->CAPACITY_PCS;
					if (isset($allocation_data[$date_format]["qty"])) {
						$data_qty[$date_format] = $allocation_data[$date_format]["qty"];
					}
					if (isset($allocation_data[$date_format]["smv"])) {
						$data_smv[$date_format] = $allocation_data[$date_format]["smv"];
					}

				}
			}
			if ($type == 2) {
				$date = new DateTime($row->DATE_CALC);
				$week = $date->format("W");
				if (isset($data[$week])) {
					$data[$week] += $row->CAPACITY_PCS;
					if (isset($allocation_data[$week]["qty"])) {
						$data_qty[$week] += $allocation_data[$week]["qty"];
					}

					if (isset($allocation_data[$week]["smv"])) {
						$data_smv[$week] += $allocation_data[$week]["smv"];
					}

				} else {
					$data[$week] = $row->CAPACITY_PCS;
					if (isset($allocation_data[$week]["qty"])) {
						$data_qty[$week] = $allocation_data[$week]["qty"];
					}

					if (isset($allocation_data[$week]["smv"])) {
						$data_smv[$week] = $allocation_data[$week]["smv"];
					}

				}
			}
			if ($type == 3) {
				if (isset($data[date("M-Y", strtotime($row->DATE_CALC))])) {
					$formats = date("M-Y", strtotime($row->DATE_CALC));
					$data[$formats] += $row->CAPACITY_PCS;
					if (isset($allocation_data[$formats]["qty"])) {
						$data_qty[$formats] += $allocation_data[$formats]["qty"];
					}

					if (isset($allocation_data[$formats]["smv"])) {
						$data_smv[$formats] += $allocation_data[$formats]["smv"];
					}

				} else {
					$formats = date("M-Y", strtotime($row->DATE_CALC));
					$data[date("M-Y", strtotime($row->DATE_CALC))] = $row->CAPACITY_PCS;
					if (isset($allocation_data[$formats]["qty"])) {
						$data_qty[$formats] = $allocation_data[$formats]["qty"];
					}

					if (isset($allocation_data[$formats]["smv"])) {
						$data_smv[$formats] = $allocation_data[$formats]["smv"];
					}

				}
			}
		}

		$data_arr = array();
		$k = 0;
		if (count($data) > 0) {
			foreach ($data as $key => $val) {
				$data_arr[$k]["date"] = $key;
				$data_arr[$k]["capacity_pcs"] = $val;
				if (isset($data_smv[$key])) {
					$data_arr[$k]["smv"] = $data_smv[$key];
				} else {
					$data_arr[$k]["smv"] = 0;
				}

				if (isset($data_qty[$key])) {
					$data_arr[$k]["qty"] = $data_qty[$key];
				} else {
					$data_arr[$k]["qty"] = 0;
				}

				$k++;
			}

		}
		return $data_arr;
	}

	
	function get_line_capacity_info($fromDate = '', $toDate = '', $company_id = 0, $location_id = 0, $floor_id = 0) {
		if ($this->db->dbdriver == 'mysqli') {
			$dateFormat = "Y-m-d";
		} else {
			$dateFormat = "d-M-Y";
		}

		$fromDate = date($dateFormat, strtotime($fromDate));
		$toDate = date($dateFormat, strtotime($toDate));

		if ($company_id > 0) {$whereCon .= " and b.COMPANY_NAME=$company_id";}
		if ($location_id > 0) {$whereCon .= " and b.LOCATION_NAME=$location_id";}
		if ($floor_id > 0) {$whereCon .= " and b.FLOOR_NAME=$floor_id";}

		//$sql = "SELECT ID,LINE_ID, EXTEND_HOUR, EXTEND_DATE,STATE from LINE_CAPACITY where EXTEND_DATE between '$fromDate' and '$toDate'";
		$sql = "SELECT a.ID,a.LINE_ID, a.EXTEND_HOUR, a.EXTEND_DATE,a.STATE from LINE_CAPACITY a,LIB_SEWING_LINE b where  b.ID=a.LINE_ID and a.EXTEND_DATE between '$fromDate' and '$toDate' $whereCon";

		$data_arr['LINE_CAPACITY'] = $this->db->query($sql)->result();
		return $data_arr;
	}

	function po_tracking_list_info($po_id = "") {
		$production_arr = array();
		$comp = array();
		$po_id_cond = " and c.po_break_down_id in($po_id)";

		//$prod_reso_allo = get_resource_allocation_variable($company_id);

		$prod_reso_allo_sql = "select COMPANY_NAME,AUTO_UPDATE from variable_settings_production where  variable_list=23 and is_deleted=0 and status_active=1";
		$prod_reso_allo_data = $this->db->query($prod_reso_allo_sql)->result();
		foreach ($prod_reso_allo_data as $row) {
			$prod_reso_allo_arr[$row->AUTO_UPDATE][$row->COMPANY_NAME] = $row->COMPANY_NAME;
		}

		if (count($prod_reso_allo_arr[1]) > 0) {
			$production_sql = "SELECT a.PO_BREAK_DOWN_ID,sum(a.PRODUCTION_QUANTITY) AS PRODUCTION_QUANTITY,a.FLOOR_ID,b.LINE_NUMBER,b.COMPANY_ID from  pro_garments_production_mst a,prod_resource_mst b where a.sewing_line=b.id and  a.production_type=5 and a.serving_company in(" . implode(',', $prod_reso_allo_arr[1]) . ") and a.po_break_down_id in($po_id) and b.line_number like '%$line_id%' and a.status_active=1 and a.is_deleted=0 group by a.PO_BREAK_DOWN_ID,b.LINE_NUMBER,a.FLOOR_ID,b.COMPANY_ID order by b.line_number ,a.po_break_down_id";
			$production_data = $this->db->query($production_sql)->result();
			if (!empty($production_data)) {
				foreach ($production_data as $pr_row) {
					list($LINE_NUMBER) = explode(",", $pr_row->LINE_NUMBER);
					$production_arr[$pr_row->PO_BREAK_DOWN_ID][$LINE_NUMBER] = $pr_row->PRODUCTION_QUANTITY;
				}
			}
		}

		if (count($prod_reso_allo_arr[2]) > 0) {
			$production_sql = "SELECT a.PO_BREAK_DOWN_ID,sum(a.PRODUCTION_QUANTITY) AS PRODUCTION_QUANTITY,a.FLOOR_ID,b.id as LINE_NUMBER,b.COMPANY_NAME from  pro_garments_production_mst a,lib_sewing_line b where a.sewing_line=b.id and  a.production_type=5 and a.serving_company in(" . implode(',', $prod_reso_allo_arr[2]) . ") and a.po_break_down_id in($po_id) and b.id = '$line_id' and a.status_active=1 and a.is_deleted=0 group by a.PO_BREAK_DOWN_ID,b.id,a.FLOOR_ID,b.COMPANY_NAME order by b.id ,a.po_break_down_id";
			$production_data = $this->db->query($production_sql)->result();
			if (!empty($production_data)) {
				foreach ($production_data as $pr_row) {
					$production_arr[$pr_row->PO_BREAK_DOWN_ID][$pr_row->LINE_NUMBER] = $pr_row->PRODUCTION_QUANTITY;
				}
			}
		}
		//return $production_sql;

		$com_res = $this->db->query("select ID,COMPANY_NAME from lib_company where status_active =1 and is_deleted=0  order by company_name")->result();

		foreach ($com_res as $value) {
			$comp[$value->ID] = $value->COMPANY_NAME;
		}

		$location_res = $this->db->query("select ID,LOCATION_NAME from lib_location  where status_active =1 and is_deleted=0  order by location_name")->result();

		foreach ($location_res as $value) {
			$location_arr[$value->ID] = $value->LOCATION_NAME;
		}

		$floor_res = $this->db->query("select ID,FLOOR_NAME from LIB_PROD_FLOOR  where status_active =1 and is_deleted=0  order by FLOOR_NAME")->result();

		foreach ($floor_res as $value) {
			$floor_arr[$value->ID] = $value->FLOOR_NAME;
		}

		//$plan_sql = "SELECT  a.COMPANY_ID,a.PO_BREAK_DOWN_ID,a.LINE_ID,a.START_DATE,a.END_DATE,a.PLAN_QNTY, a.LOCATION_ID,a.ITEM_NUMBER_ID,b.FLOOR_NAME,B.LINE_NAME  from ppl_sewing_plan_board a,LIB_SEWING_LINE b where b.id=a.LINE_ID and a.is_deleted=0 and a.status_active=1 $po_id_cond $company_cond order by a.COMPANY_ID";

		/*$plan_sql = "SELECT  a.COMPANY_ID,a.PO_BREAK_DOWN_ID,a.LINE_ID,a.START_DATE,a.END_DATE,a.PLAN_QNTY, a.LOCATION_ID,a.ITEM_NUMBER_ID,b.FLOOR_NAME,B.LINE_NAME  from ppl_sewing_plan_board a left join LIB_SEWING_LINE b on b.id=a.LINE_ID  and a.COMPANY_ID=b.COMPANY_name  where a.is_deleted=0 and a.status_active=1 $po_id_cond order by a.COMPANY_ID";*/
		
		$plan_sql = "SELECT  a.COMPANY_ID,c.PO_BREAK_DOWN_ID,a.LINE_ID,a.START_DATE,a.END_DATE,c.PLAN_QNTY, a.LOCATION_ID,a.ITEM_NUMBER_ID,b.FLOOR_NAME,B.LINE_NAME  
from ppl_sewing_plan_board a left join PPL_SEWING_PLAN_BOARD_POWISE c on a.PLAN_ID = c.PLAN_ID left join LIB_SEWING_LINE b on b.id=a.LINE_ID  and a.COMPANY_ID=b.COMPANY_name  where a.is_deleted=0 and a.status_active=1  
$po_id_cond 
order by a.COMPANY_ID";
		
		$plan_sql_result = $this->db->query($plan_sql)->result();

		foreach ($plan_sql_result as $rows) {
			$floor_id = $production_line_qnty = 0;
			$production_line_qnty = $production_arr[$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID];

			$plan_data_array[$rows->PO_BREAK_DOWN_ID][] = array(
				"COMPANY_ID" => $rows->COMPANY_ID,
				"COMPANY_NAME" => $comp[$rows->COMPANY_ID],
				"LOCATION_ID" => $rows->LOCATION_ID,
				"LOCATION_NAME" => $location_arr[$rows->LOCATION_ID] ? $location_arr[$rows->LOCATION_ID] : "All Location",
				"FLOOR" => $rows->FLOOR_NAME,
				"FLOOR_NAME" => $floor_arr[$rows->FLOOR_NAME] ? $floor_arr[$rows->FLOOR_NAME] : "All Floor",
				"LINE_ID" => $rows->LINE_ID,
				"LINE_NAME" => $rows->LINE_NAME,
				"START_DATE" => $rows->START_DATE,
				"END_DATE" => $rows->END_DATE,
				"PRODUCTION_QNTY" => $production_line_qnty * 1,
				"PLAN_QNTY" => $rows->PLAN_QNTY,
			);
		}
		//return $plan_data_array;
		
		$job_sql = "SELECT  a.JOB_NO,a.STYLE_REF_NO,b.id as PO_ID,b.PO_NUMBER,b.PO_QUANTITY,b.PLAN_CUT,PUB_SHIPMENT_DATE as SHIPMENT_DATE from WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b where a.id=b.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.id in($po_id)";
		$job_sql_result = $this->db->query($job_sql)->result();
		foreach ($job_sql_result as $rows) {
			$data_array[] = array(
				"JOB_NO" => $rows->JOB_NO,
				"STYLE_REF_NO" => $rows->STYLE_REF_NO,
				"PO_ID" => $rows->PO_ID,
				"PO_NUMBER" => $rows->PO_NUMBER,
				"PO_QUANTITY" => $rows->PO_QUANTITY,
				"PLAN_CUT" => $rows->PLAN_CUT,
				"PUB_SHIPMENT_DATE" => $rows->SHIPMENT_DATE,
				"PLAN_DTLS" => $plan_data_array[$rows->PO_ID],
			);
		}

		if (count($data_array) > 0) {
			return $data_array;
		} else {
			return 0;
		}
	}




	function get_monthly_capacity_vs_allocated_order_info($fromDate, $toDate, $company, $location) {
		
		list($start_month,$start_year)=explode('-',$fromDate);
		list($end_month,$end_year)=explode('-',$toDate);
		
		
		$cbo_company_name=str_replace("'","",$company);
		$cbo_location_id=str_replace("'","",$location);
		$cbo_year_name=str_replace("'","",$start_year);
		$cbo_month=str_replace("'","",$start_month);
		$cbo_month_end=str_replace("'","",$end_month);
		$cbo_end_year_name=str_replace("'","",$end_year);
		
		$com_res = $this->db->query("select ID,COMPANY_NAME from lib_company where status_active =1 and is_deleted=0  order by company_name")->result();
		foreach ($com_res as $rows) {
			$companyArr[$rows->ID] = $rows->COMPANY_NAME;
		}

		$loc_res = $this->db->query("select ID,LOCATION_NAME from lib_location where status_active =1 and is_deleted=0  order by LOCATION_NAME")->result();
		foreach ($loc_res as $rows) {
			$locationArr[$rows->ID] = $rows->LOCATION_NAME;
		}


		$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_year_name);
		$s_date=$cbo_year_name."-".$cbo_month."-"."01";
		$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;
		
		
		
		
		if ($this->db->dbdriver != 'mysqli') {
			$s_date = date("d-M-Y", strtotime($s_date));
			$e_date = date("d-M-Y", strtotime($e_date));
	
		}
	

		$tot_month = datediff( 'm', $s_date,$e_date);
	
	
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($s_date,$i);
			$month_arr[]=date("Y-m",strtotime($next_month));
			$fullMonthArr[]=date("M-Y",strtotime($next_month));
		}
	
		$date_cond="AND b.pub_shipment_date between '$s_date' and '$e_date'";
		if($cbo_company_name!=0){$company_con=" AND a.company_name=$cbo_company_name";}
		if($cbo_location_id!=0){$location_con=" AND a.LOCATION_NAME=$cbo_location_id";}
		
		$sql_con_po="SELECT a.job_no, a.COMPANY_NAME, a.buyer_name, b.PUB_SHIPMENT_DATE, b.shipment_date, b.IS_CONFIRMED, c.order_quantity as po_quantity,a.SET_SMV,a.LOCATION_NAME
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		WHERE a.job_no=b.job_no_mst AND b.id=c.po_break_down_id $company_con $location_con $date_cond and a.status_active=1 and b.is_confirmed=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.company_name";
		
		$poQtyArr=array();
		$sql_data_po =$this->db->query($sql_con_po)->result();
		foreach( $sql_data_po as $row_po)
		{
			//$companyByJobArr[$row_po[csf("job_no")]]=$row_po[csf("company_name")];
			$monthKey=date("Y-m",strtotime($row_po->PUB_SHIPMENT_DATE));
			$key=$row_po->COMPANY_NAME.'__'.$row_po->LOCATION_NAME;
			$poMinuteArr[$key][$monthKey][$row_po->IS_CONFIRMED]+=($row_po->PO_QUANTITY*$row_po->SET_SMV);
		}
		unset($sql_data_po);
		
		
		
		
		
	
		if($cbo_company_name!=0){$company_con2=" AND a.COMAPNY_ID=$cbo_company_name";}
		if($cbo_location_id!=0){$location_con2=" AND a.LOCATION_ID=$cbo_location_id";}
		$sql_capacity="Select a.COMAPNY_ID,a.LOCATION_ID, b.CAPACITY_MIN,b.DATE_CALC from lib_capacity_calc_mst a, lib_capacity_calc_dtls b  where a.id=b.mst_id $company_con2 and a.status_active=1 and a.is_deleted=0 AND a.YEAR between '$cbo_year_name' and '$cbo_end_year_name' and b.DATE_CALC between '$s_date' and '$e_date'";
		$sql_capacityRes=$this->db->query($sql_capacity)->result();
		$capacityArr=array();
		foreach($sql_capacityRes as $row)
		{
			$monthKey=date("Y-m",strtotime($row->DATE_CALC));
			$key=$row->COMAPNY_ID.'__'.$row->LOCATION_ID;
			$capacityArr[$key][$monthKey]+=$row->CAPACITY_MIN;
		}
		unset($sql_capacityRes);	

		
		
		
		$sql_allocationl="select b.company_name as FROM_COMPANY,b.location_name as FROM_LOCATION, a.company_id as TO_COMPANY, a.location_name as TO_LOCATION,  a.SMV, a.ALLOCATED_QTY, a.CUT_OFF_DATE from ppl_order_allocation_mst a,wo_po_details_master b where a.job_no=b.job_no and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1 and a.cut_off_date between '$s_date' and '$e_date'";
		//echo $sql_allocationl;die;
		$sql_allocationlRes=$this->db->query($sql_allocationl)->result();
		$allocationArr=array();
		foreach($sql_allocationlRes as $row)
		{
			$monthKey=date("Y-m",strtotime($row->CUT_OFF_DATE));
			
			$keyFrom=$row->FROM_COMPANY.'__'.$row->FROM_LOCATION;
			$allocationFromArr[$keyFrom][$monthKey]+=($row->ALLOCATED_QTY*$row->SMV);
	
			$keyTo=$row->TO_COMPANY.'__'.$row->TO_LOCATION;
			$allocationToArr[$keyTo][$monthKey]+=($row->ALLOCATED_QTY*$row->SMV);
		}
		unset($sql_allocationlRes);		
		
		
		
		$monthArr=array();
		$i=0;
		foreach($poMinuteArr as $company_location=>$dataRows)
		{
			list($company_id,$location_id)=explode('__',$company_location);
			
			 
				foreach($month_arr as $monthVal)
				{
					$capacity=$capacityArr[$company_location][$monthVal];
				
					$confirmQty=$dataRows[$monthVal][1]+($allocationToArr[$company_location][$monthVal]-$allocationFromArr[$company_location][$monthVal]);
					
					
					$monthArr[$i]['company_id']=$company_id;
					$monthArr[$i]['company_name']=$companyArr[$company_id];
					
					$monthArr[$i]['location_id']=$location_id;
					$monthArr[$i]['location_name']=$locationArr[$location_id];
					
					$monthArr[$i]['month']=$monthVal;
					$monthArr[$i]['capacity_minute']=$capacity*1;
					$monthArr[$i]['allocated_minute']=$confirmQty*1;
					$i++;
				}
			
			
		}
						
		return $monthArr;
	
	}



	function get_monthly_capacity_vs_plan_info($fromDate, $toDate, $company, $location) {
		
		list($start_month,$start_year)=explode('-',$fromDate);
		list($end_month,$end_year)=explode('-',$toDate);
		
		
		$cbo_company_name=str_replace("'","",$company);
		$cbo_location_id=str_replace("'","",$location);
		$cbo_year_name=str_replace("'","",$start_year);
		$cbo_month=str_replace("'","",$start_month);
		$cbo_month_end=str_replace("'","",$end_month);
		$cbo_end_year_name=str_replace("'","",$end_year);
		
		$com_res = $this->db->query("select ID,COMPANY_NAME from lib_company where status_active =1 and is_deleted=0  order by company_name")->result();
		foreach ($com_res as $rows) {
			$companyArr[$rows->ID] = $rows->COMPANY_NAME;
		}

		$loc_res = $this->db->query("select ID,LOCATION_NAME from lib_location where status_active =1 and is_deleted=0  order by LOCATION_NAME")->result();
		foreach ($loc_res as $rows) {
			$locationArr[$rows->ID] = $rows->LOCATION_NAME;
		}


		$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_year_name);
		$s_date=$cbo_year_name."-".$cbo_month."-"."01";
		$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;
		
		
		
		
		if ($this->db->dbdriver != 'mysqli') {
			$s_date = date("d-M-Y", strtotime($s_date));
			$e_date = date("d-M-Y", strtotime($e_date));
	
		}
	

		$tot_month = datediff( 'm', $s_date,$e_date);
	
	
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($s_date,$i);
			$month_arr[]=date("Y-m",strtotime($next_month));
			$fullMonthArr[]=date("M-Y",strtotime($next_month));
		}
	
		$date_cond="AND b.pub_shipment_date between '$s_date' and '$e_date'";
		if($cbo_company_name!=0){$company_con=" AND a.company_name=$cbo_company_name";}
		if($cbo_location_id!=0){$location_con=" AND a.LOCATION_NAME=$cbo_location_id";}
		
	
		if($cbo_company_name!=0){$company_con2=" AND a.COMAPNY_ID=$cbo_company_name";}
		if($cbo_location_id!=0){$location_con2=" AND a.LOCATION_ID=$cbo_location_id";}
		$sql_capacity="Select a.COMAPNY_ID,a.LOCATION_ID, b.CAPACITY_MIN,b.DATE_CALC from lib_capacity_calc_mst a, lib_capacity_calc_dtls b  where a.id=b.mst_id $company_con2 and a.status_active=1 and a.is_deleted=0 AND a.YEAR between '$cbo_year_name' and '$cbo_end_year_name' and b.DATE_CALC between '$s_date' and '$e_date'";
		$sql_capacityRes=$this->db->query($sql_capacity)->result();
		$capacityArr=array();
		foreach($sql_capacityRes as $row)
		{
			$monthKey=date("Y-m",strtotime($row->DATE_CALC));
			$key=$row->COMAPNY_ID.'__'.$row->LOCATION_ID;
			$capacityArr[$key][$monthKey]+=$row->CAPACITY_MIN;
		}
		unset($sql_capacityRes);	
		
	
	$sql_plan="SELECT (a.SMV_PCS * c.plan_qnty) AS PLAN_MINIT, c.COMPANY_ID, c.LOCATION_ID, b.PUB_SHIPMENT_DATE, b.PO_QUANTITY, pd.PLAN_DATE FROM wo_po_break_down b, WO_PO_DETAILS_MAS_SET_DETAILS a, ppl_sewing_plan_board_powise  pp, ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c WHERE a.job_no = b.job_no_mst  AND b.id = pp.po_break_down_id AND pp.plan_id = pd.plan_id AND c.ITEM_NUMBER_ID=a.GMTS_ITEM_ID and pp.ITEM_NUMBER_ID=a.GMTS_ITEM_ID and c.ITEM_NUMBER_ID=a.GMTS_ITEM_ID AND pp.plan_id = c.plan_id AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 and pd.PLAN_DATE between '$s_date' and '$e_date'";
	
	 //return $sql_plan;die;
	$sql_planRes=$this->db->query($sql_plan)->result();
	$planMiniArr=array();
	foreach($sql_planRes as $row)
	{
		$monthKey=date("Y-m",strtotime($row->PLAN_DATE));

		$key=$row->COMPANY_ID.'__'.$row->LOCATION_ID;
		$planMiniArr[$key][$monthKey]+=$row->PLAN_MINIT;
	}
	unset($sql_planRes);
		
		
		
		
		
		$monthArr=array();
		$i=0;
		foreach($capacityArr as $company_location=>$dataRows)
		{
			list($company_id,$location_id)=explode('__',$company_location);
			
			 
				foreach($month_arr as $monthVal)
				{
					$capacity=$capacityArr[$company_location][$monthVal];
					$plan=$planMiniArr[$company_location][$monthVal];
					
					$monthArr[$i]['company_id']=$company_id;
					$monthArr[$i]['company_name']=$companyArr[$company_id];
					
					$monthArr[$i]['location_id']=$location_id;
					$monthArr[$i]['location_name']=$locationArr[$location_id];
					
					$monthArr[$i]['month']=$monthVal;
					$monthArr[$i]['capacity_minute']=$capacity*1;
					$monthArr[$i]['plan_minute']=$plan*1;
					$i++;
				}
			
			
		}
						
		return $monthArr;
	
	}


	function get_monthly_plan_vs_booked_info($fromDate, $toDate, $company, $location) {
		
		list($start_month,$start_year)=explode('-',$fromDate);
		list($end_month,$end_year)=explode('-',$toDate);
		
		
		$cbo_company_name=str_replace("'","",$company);
		$cbo_location_id=str_replace("'","",$location);
		$cbo_year_name=str_replace("'","",$start_year);
		$cbo_month=str_replace("'","",$start_month);
		$cbo_month_end=str_replace("'","",$end_month);
		$cbo_end_year_name=str_replace("'","",$end_year);
		
		$com_res = $this->db->query("select ID,COMPANY_NAME from lib_company where status_active =1 and is_deleted=0  order by company_name")->result();
		foreach ($com_res as $rows) {
			$companyArr[$rows->ID] = $rows->COMPANY_NAME;
		}

		$loc_res = $this->db->query("select ID,LOCATION_NAME from lib_location where status_active =1 and is_deleted=0  order by LOCATION_NAME")->result();
		foreach ($loc_res as $rows) {
			$locationArr[$rows->ID] = $rows->LOCATION_NAME;
		}

		$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_year_name);
		$s_date=$cbo_year_name."-".$cbo_month."-"."01";
		$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;
		
		if ($this->db->dbdriver != 'mysqli') {
			$s_date = date("d-M-Y", strtotime($s_date));
			$e_date = date("d-M-Y", strtotime($e_date));
		}
	
		$tot_month = datediff( 'm', $s_date,$e_date);
	
	
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($s_date,$i);
			$month_arr[]=date("Y-m",strtotime($next_month));
			$fullMonthArr[]=date("M-Y",strtotime($next_month));
		}
	
		$date_cond="AND b.pub_shipment_date between '$s_date' and '$e_date'";
		if($cbo_company_name!=0){$company_con=" AND a.company_name=$cbo_company_name";}
		if($cbo_location_id!=0){$location_con=" AND a.LOCATION_NAME=$cbo_location_id";}
		
	
		$sql_con_po="SELECT a.job_no, a.COMPANY_NAME, a.buyer_name, b.PUB_SHIPMENT_DATE, b.shipment_date, b.IS_CONFIRMED, c.order_quantity as po_quantity,a.SET_SMV,a.LOCATION_NAME
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		WHERE a.job_no=b.job_no_mst AND b.id=c.po_break_down_id $company_con $location_con $date_cond and a.status_active=1 and b.is_confirmed=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.company_name";
		
		$poQtyArr=array();
		$sql_data_po =$this->db->query($sql_con_po)->result();
		foreach( $sql_data_po as $row_po)
		{
			$monthKey=date("Y-m",strtotime($row_po->PUB_SHIPMENT_DATE));
			$key=$row_po->COMPANY_NAME.'__'.$row_po->LOCATION_NAME;
			$poMinuteArr[$key][$monthKey][$row_po->IS_CONFIRMED]+=($row_po->PO_QUANTITY*$row_po->SET_SMV);
		}
		unset($sql_data_po);	
		//return $poMinuteArr;
	
		$sql_allocationl="select b.company_name as FROM_COMPANY,b.location_name as FROM_LOCATION, a.company_id as TO_COMPANY, a.location_name as TO_LOCATION,  a.SMV, a.ALLOCATED_QTY, a.CUT_OFF_DATE from ppl_order_allocation_mst a,wo_po_details_master b where a.job_no=b.job_no and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1 and a.cut_off_date between '$s_date' and '$e_date'";
		//echo $sql_allocationl;die;
		$sql_allocationlRes=$this->db->query($sql_allocationl)->result();
		$allocationArr=array();
		foreach($sql_allocationlRes as $row)
		{
			$monthKey=date("Y-m",strtotime($row->CUT_OFF_DATE));
			
			$keyFrom=$row->FROM_COMPANY.'__'.$row->FROM_LOCATION;
			$allocationFromArr[$keyFrom][$monthKey]+=($row->ALLOCATED_QTY*$row->SMV);
	
			$keyTo=$row->TO_COMPANY.'__'.$row->TO_LOCATION;
			$allocationToArr[$keyTo][$monthKey]+=($row->ALLOCATED_QTY*$row->SMV);
		}
		unset($sql_allocationlRes);		
		//return $allocationToArr;
	
	/*
		if($cbo_company_name!=0){$company_con2=" AND a.COMAPNY_ID=$cbo_company_name";}
		if($cbo_location_id!=0){$location_con2=" AND a.LOCATION_ID=$cbo_location_id";}
		$sql_capacity="Select a.COMAPNY_ID,a.LOCATION_ID, b.CAPACITY_MIN,b.DATE_CALC from lib_capacity_calc_mst a, lib_capacity_calc_dtls b  where a.id=b.mst_id $company_con2 and a.status_active=1 and a.is_deleted=0 AND a.YEAR between '$cbo_year_name' and '$cbo_end_year_name' and b.DATE_CALC between '$s_date' and '$e_date'";
		$sql_capacityRes=$this->db->query($sql_capacity)->result();
		$capacityArr=array();
		foreach($sql_capacityRes as $row)
		{
			$monthKey=date("Y-m",strtotime($row->DATE_CALC));
			$key=$row->COMAPNY_ID.'__'.$row->LOCATION_ID;
			$capacityArr[$key][$monthKey]+=$row->CAPACITY_MIN;
		}
		unset($sql_capacityRes);	*/
		
	
	$sql_plan="SELECT (a.SMV_PCS * c.plan_qnty) AS PLAN_MINIT, c.COMPANY_ID, c.LOCATION_ID, b.PUB_SHIPMENT_DATE, b.PO_QUANTITY, pd.PLAN_DATE FROM wo_po_break_down b, WO_PO_DETAILS_MAS_SET_DETAILS a, ppl_sewing_plan_board_powise  pp, ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c WHERE a.job_no = b.job_no_mst  AND b.id = pp.po_break_down_id AND pp.plan_id = pd.plan_id AND c.ITEM_NUMBER_ID=a.GMTS_ITEM_ID and pp.ITEM_NUMBER_ID=a.GMTS_ITEM_ID and c.ITEM_NUMBER_ID=a.GMTS_ITEM_ID AND pp.plan_id = c.plan_id AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 and pd.PLAN_DATE between '$s_date' and '$e_date'";
	
	 //return $sql_plan;die;
	$sql_planRes=$this->db->query($sql_plan)->result();
	$planMiniArr=array();
	foreach($sql_planRes as $row)
	{
		$monthKey=date("Y-m",strtotime($row->PLAN_DATE));

		$key=$row->COMPANY_ID.'__'.$row->LOCATION_ID;
		$planMiniArr[$key][$monthKey]+=$row->PLAN_MINIT;
	}
	unset($sql_planRes);
		
		
		
		
		
		$monthArr=array();
		$i=0;
		foreach($poMinuteArr as $company_location=>$dataRows)
		{
			list($company_id,$location_id)=explode('__',$company_location);
			
			 
				foreach($month_arr as $monthVal)
				{
					//$capacity=$capacityArr[$company_location][$monthVal];
					$plan=$planMiniArr[$company_location][$monthVal];
					$booked=$dataRows[$monthVal][1]+($allocationToArr[$company_location][$monthVal]-$allocationFromArr[$company_location][$monthVal]);
					
					
					
					$monthArr[$i]['company_id']=$company_id;
					$monthArr[$i]['company_name']=$companyArr[$company_id];
					$monthArr[$i]['location_id']=$location_id;
					$monthArr[$i]['location_name']=$locationArr[$location_id];
					
					$monthArr[$i]['month']=$monthVal;
					//$monthArr[$i]['capacity_minute']=$capacity*1;
					$monthArr[$i]['plan_minute']=$plan*1;
					$monthArr[$i]['booked_minute']=$booked*1;
					$i++;
				}
			
			
		}
						
		return $monthArr;
	
	
	
	
	
	}


	function get_monthly_plan_vs_booked_vs_capacity_info($fromDate, $toDate, $company, $location) {
		
		list($start_month,$start_year)=explode('-',$fromDate);
		list($end_month,$end_year)=explode('-',$toDate);
		
		
		$cbo_company_name=str_replace("'","",$company);
		$cbo_location_id=str_replace("'","",$location);
		$cbo_year_name=str_replace("'","",$start_year);
		$cbo_month=str_replace("'","",$start_month);
		$cbo_month_end=str_replace("'","",$end_month);
		$cbo_end_year_name=str_replace("'","",$end_year);
		
		$com_res = $this->db->query("select ID,COMPANY_NAME from lib_company where status_active =1 and is_deleted=0  order by company_name")->result();
		foreach ($com_res as $rows) {
			$companyArr[$rows->ID] = $rows->COMPANY_NAME;
		}

		$loc_res = $this->db->query("select ID,LOCATION_NAME from lib_location where status_active =1 and is_deleted=0  order by LOCATION_NAME")->result();
		foreach ($loc_res as $rows) {
			$locationArr[$rows->ID] = $rows->LOCATION_NAME;
		}

		$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_year_name);
		$s_date=$cbo_year_name."-".$cbo_month."-"."01";
		$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;
		
		if ($this->db->dbdriver != 'mysqli') {
			$s_date = date("d-M-Y", strtotime($s_date));
			$e_date = date("d-M-Y", strtotime($e_date));
		}
	
		$tot_month = datediff( 'm', $s_date,$e_date);
	
	
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($s_date,$i);
			$month_arr[]=date("Y-m",strtotime($next_month));
			$fullMonthArr[]=date("M-Y",strtotime($next_month));
		}
	
		$date_cond="AND b.pub_shipment_date between '$s_date' and '$e_date'";
		if($cbo_company_name!=0){$company_con=" AND a.company_name=$cbo_company_name";}
		if($cbo_location_id!=0){$location_con=" AND a.LOCATION_NAME=$cbo_location_id";}
		
	
		$sql_con_po="SELECT a.job_no, a.COMPANY_NAME, a.buyer_name, b.PUB_SHIPMENT_DATE, b.shipment_date, b.IS_CONFIRMED, c.order_quantity as po_quantity,a.SET_SMV,a.LOCATION_NAME
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		WHERE a.job_no=b.job_no_mst AND b.id=c.po_break_down_id $company_con $location_con $date_cond and a.status_active=1 and b.is_confirmed=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.company_name";
		
		$poQtyArr=array();
		$sql_data_po =$this->db->query($sql_con_po)->result();
		foreach( $sql_data_po as $row_po)
		{
			$monthKey=date("Y-m",strtotime($row_po->PUB_SHIPMENT_DATE));
			$key=$row_po->COMPANY_NAME.'__'.$row_po->LOCATION_NAME;
			$poMinuteArr[$key][$monthKey][$row_po->IS_CONFIRMED]+=($row_po->PO_QUANTITY*$row_po->SET_SMV);
		}
		unset($sql_data_po);	
		//return $poMinuteArr;
	
		$sql_allocationl="select b.company_name as FROM_COMPANY,b.location_name as FROM_LOCATION, a.company_id as TO_COMPANY, a.location_name as TO_LOCATION,  a.SMV, a.ALLOCATED_QTY, a.CUT_OFF_DATE from ppl_order_allocation_mst a,wo_po_details_master b where a.job_no=b.job_no and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1 and a.cut_off_date between '$s_date' and '$e_date'";
		//echo $sql_allocationl;die;
		$sql_allocationlRes=$this->db->query($sql_allocationl)->result();
		$allocationArr=array();
		foreach($sql_allocationlRes as $row)
		{
			$monthKey=date("Y-m",strtotime($row->CUT_OFF_DATE));
			
			$keyFrom=$row->FROM_COMPANY.'__'.$row->FROM_LOCATION;
			$allocationFromArr[$keyFrom][$monthKey]+=($row->ALLOCATED_QTY*$row->SMV);
	
			$keyTo=$row->TO_COMPANY.'__'.$row->TO_LOCATION;
			$allocationToArr[$keyTo][$monthKey]+=($row->ALLOCATED_QTY*$row->SMV);
		}
		unset($sql_allocationlRes);		
		//return $allocationToArr;
	
	
		if($cbo_company_name!=0){$company_con2=" AND a.COMAPNY_ID=$cbo_company_name";}
		if($cbo_location_id!=0){$location_con2=" AND a.LOCATION_ID=$cbo_location_id";}
		
		$sql_capacity="Select a.COMAPNY_ID,a.LOCATION_ID, b.CAPACITY_MIN,b.DATE_CALC from lib_capacity_calc_mst a, lib_capacity_calc_dtls b  where a.id=b.mst_id $company_con2 and a.status_active=1 and a.is_deleted=0 AND a.YEAR between '$cbo_year_name' and '$cbo_end_year_name' and b.DATE_CALC between '$s_date' and '$e_date' $location_con2";
		$sql_capacityRes=$this->db->query($sql_capacity)->result();
		$capacityArr=array();
		foreach($sql_capacityRes as $row)
		{
			$monthKey=date("Y-m",strtotime($row->DATE_CALC));
			$key=$row->COMAPNY_ID.'__'.$row->LOCATION_ID;
			$capacityArr[$key][$monthKey]+=$row->CAPACITY_MIN;
		}
		unset($sql_capacityRes);	
		
	
		if($cbo_company_name!=0){$company_con3=" AND c.COMPANY_ID=$cbo_company_name";}
		if($cbo_location_id!=0){$location_con3=" AND c.LOCATION_ID=$cbo_location_id";}
	
	$sql_plan="SELECT (a.SMV_PCS * c.plan_qnty) AS PLAN_MINIT, c.COMPANY_ID, c.LOCATION_ID, b.PUB_SHIPMENT_DATE, b.PO_QUANTITY, pd.PLAN_DATE FROM wo_po_break_down b, WO_PO_DETAILS_MAS_SET_DETAILS a, ppl_sewing_plan_board_powise  pp, ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c WHERE a.job_no = b.job_no_mst  AND b.id = pp.po_break_down_id AND pp.plan_id = pd.plan_id AND c.ITEM_NUMBER_ID=a.GMTS_ITEM_ID and pp.ITEM_NUMBER_ID=a.GMTS_ITEM_ID and c.ITEM_NUMBER_ID=a.GMTS_ITEM_ID AND pp.plan_id = c.plan_id AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 and pd.PLAN_DATE between '$s_date' and '$e_date' $company_con3 $location_con3";
	
	 //return $sql_plan;die;
	$sql_planRes=$this->db->query($sql_plan)->result();
	$planMiniArr=array();
	foreach($sql_planRes as $row)
	{
		$monthKey=date("Y-m",strtotime($row->PLAN_DATE));

		$key=$row->COMPANY_ID.'__'.$row->LOCATION_ID;
		$planMiniArr[$key][$monthKey]+=$row->PLAN_MINIT;
	}
	unset($sql_planRes);
		
		
		
		
		
		$monthArr=array();
		$i=0;
		foreach($capacityArr as $company_location=>$dataRows)
		{
			list($company_id,$location_id)=explode('__',$company_location);
			
			 
				foreach($month_arr as $monthVal)
				{
					$capacity=$capacityArr[$company_location][$monthVal];
					$plan=$planMiniArr[$company_location][$monthVal];
					$booked=$poMinuteArr[$company_location][$monthVal][1]+($allocationToArr[$company_location][$monthVal]-$allocationFromArr[$company_location][$monthVal]);
					
					$monthArr[$i]['company_id']=$company_id;
					$monthArr[$i]['company_name']=$companyArr[$company_id];
					$monthArr[$i]['location_id']=$location_id;
					$monthArr[$i]['location_name']=$locationArr[$location_id];
					
					$monthArr[$i]['month']=$monthVal;
					$monthArr[$i]['capacity_minute']=$capacity*1;
					$monthArr[$i]['plan_minute']=$plan*1;
					$monthArr[$i]['booked_minute']=$booked*1;
					$i++;
				}
			
		}
						
		return $monthArr;
	}













}