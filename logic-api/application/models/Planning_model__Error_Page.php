<?php
class Planning_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

    /**
     * [get_max_value description]
     * @param  [string] $tableName [defining name of the table]
     * @param  [string] $fieldName [defining name of the table column]
     * @return [integer]            [return max value of the table column]
     */
    function get_max_value($tableName, $fieldName) {
    	return $this->db->select_max($fieldName)->get($tableName)->row()->{$fieldName};
    }

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
<<<<<<< HEAD
        if( ($attribute*1)>0)
        {
            $query = $this->db->query('select '. $tableName.'.'.$fieldName .' from '.$tableName.' where id='.$this->db->escape($attribute));
            $result =$query->row();
            if (!empty($result)):
                return $result->{$fieldName};
            else:
                return false;
            endif;
        }
        
=======
    	if( ($attribute*1)>0)
    	{
    		$query = $this->db->query('select '. $tableName.'.'.$fieldName .' from '.$tableName.' where id='.$this->db->escape($attribute));
    		$result =$query->row();
    		if (!empty($result)):
    			return $result->{$fieldName};
    		else:
    			return false;
    		endif;
    	}

>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb
        /*$result = $this->db->get_where($tableName, $attribute)->row();
        if (!empty($result)):
            return $result->{$fieldName};
        else:
            return false;
        endif;*/
    }

<<<<<<< HEAD
     public function login($user_id, $password) {
        $query = $this->db->query('select user_passwd.id AS "ID",user_passwd.password "PASSWORD" from user_passwd where user_name='.$this->db->escape($user_id));
        if ($query->num_rows() == 1) {
            $user_info = $query->row();
        // return false;
            if($user_info->PASSWORD==$this->encrypt($password))
            {
                    return $this->get_menu_by_privilege($user_info->ID);
                } else {
                    return false;
            }
        }
    }
    
    public function logout( $user_id ) {
        $query = $this->db->query('update planning_board_status set board_status=0 where user_id='.$this->db->escape($user_id));
    }
    
    
    public function encrypt( $string ) 
    {  
        // Retrun String after Ecryption
        // Here $string= Given Text to be encrypted, 
        $key="logic_erp_2011_2012_platform";
        $result = ''; 
        for($i=0; $i<strlen($string); $i++) {
            $char = substr($string, $i, 1); 
            $keychar = substr($key, ($i % strlen($key))-1, 1); 
            $char = chr(ord($char)+ord($keychar)); 
            $result.=$char; 
        }       
        return base64_encode($result); 
    }

    public function get_menu_by_privilege($user_id) {
        $comp_sql = "select ID,COMPANY_NAME from lib_company comp where status_active =1 and is_deleted=0  order by company_name";
        $buyer_sql = "select a.ID,a.BUYER_NAME,SHORT_NAME,b.TAG_COMPANY COMPANY_ID from lib_buyer a,lib_buyer_tag_company b where a.id=b.buyer_id  and a.status_active=1 and a.is_deleted=0 order by a.BUYER_NAME";
        $loc_sql = "select ID,LOCATION_NAME,COMPANY_ID from lib_location where status_active =1 and is_deleted=0 order by location_name";
        $floor_sql = "select ID,FLOOR_NAME,LOCATION_ID,COMPANY_ID from  lib_prod_floor where  production_process=5 and status_active =1 and is_deleted=0 order by floor_serial_no";
        $line_sql = "select ID,LINE_NAME from lib_sewing_line where status_active= 1 and is_deleted = 0 order by sewing_line_serial ";
        
        
        $com_res = $this->db->query($comp_sql)->result();
        $buyer_res = $this->db->query($buyer_sql)->result();
        $loc_res = $this->db->query($loc_sql)->result();
        $floor_res = $this->db->query($floor_sql)->result();
        $line_res = $this->db->query($line_sql)->result();
=======
    public function login($user_id, $password) {
    	$query = $this->db->query('select user_passwd.id AS "ID",user_passwd.password "PASSWORD" from user_passwd where user_name='.$this->db->escape($user_id));
    	if ($query->num_rows() == 1) {
    		$user_info = $query->row();
		// return false;
    		if($user_info->PASSWORD==$this->encrypt($password))
    		{
    			return $this->get_menu_by_privilege($user_info->ID);
    		} else {
    			return false;
    		}
    	}
    }

    public function logout( $user_id ) {
    	$query = $this->db->query('update planning_board_status set board_status=0 where user_id='.$this->db->escape($user_id));
    }


    public function encrypt( $string ) 
    {  
		// Retrun String after Ecryption
		// Here $string= Given Text to be encrypted, 
    	$key="logic_erp_2011_2012_platform";
    	$result = ''; 
    	for($i=0; $i<strlen($string); $i++) {
    		$char = substr($string, $i, 1); 
    		$keychar = substr($key, ($i % strlen($key))-1, 1); 
    		$char = chr(ord($char)+ord($keychar)); 
    		$result.=$char; 
    	}		
    	return base64_encode($result); 
    }

    public function get_menu_by_privilege($user_id) {
    	$comp_sql = "select ID,COMPANY_NAME from lib_company comp where status_active =1 and is_deleted=0  order by company_name";
    	$buyer_sql = "select a.ID,a.BUYER_NAME,SHORT_NAME,b.TAG_COMPANY COMPANY_ID from lib_buyer a,lib_buyer_tag_company b where a.id=b.buyer_id  and a.status_active=1 and a.is_deleted=0 order by a.BUYER_NAME";
    	$loc_sql = "select ID,LOCATION_NAME,COMPANY_ID from lib_location where status_active =1 and is_deleted=0 order by location_name";
    	$floor_sql = "select ID,FLOOR_NAME,LOCATION_ID,COMPANY_ID from  lib_prod_floor where  production_process=5 and status_active =1 and is_deleted=0 order by floor_serial_no";
    	$line_sql = "select ID,LINE_NAME from lib_sewing_line where status_active= 1 and is_deleted = 0 order by sewing_line_serial ";


    	$com_res = $this->db->query($comp_sql)->result();
    	$buyer_res = $this->db->query($buyer_sql)->result();
    	$loc_res = $this->db->query($loc_sql)->result();
    	$floor_res = $this->db->query($floor_sql)->result();
    	$line_res = $this->db->query($line_sql)->result();
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb
        /*foreach ($com_res as $value) {
            $data_arr['company_info'][$value->ID] = $value->COMPANY_NAME;
        }
        foreach ($buyer_res as $value) {
            $data_arr['buyer_info'][$value->ID] = $value->BUYER_NAME;
        }
        foreach ($loc_res as $value) {
            $data_arr['location_info'][$value->ID] = $value->LOCATION_NAME;
        }
        foreach ($floor_res as $value) {
            $data_arr['floor_info'][$value->ID] = $value->FLOOR_NAME;
        }
        foreach ($line_res as $value) {
            $data_arr['line_info'][$value->ID] = $value->LINE_NAME;
        }*/
<<<<<<< HEAD
        //$floor_res[0]="All floor";
         $data_arr['floor_info'][0] ["ID"]=0;
         $data_arr['floor_info'][0] ["FLOOR_NAME"]="All Floor";
         $data_arr['floor_info'][0] ["LOCATION_ID"]=0;
         $data_arr['floor_info'][0] ["COMPANY_ID"]=0;
         
        // $fl=0;
        /*foreach ($floor_res as $value) {
=======
		//$floor_res[0]="All floor";
        $data_arr['floor_info'][0] ["ID"]=0;
        $data_arr['floor_info'][0] ["FLOOR_NAME"]="All Floor";
        $data_arr['floor_info'][0] ["LOCATION_ID"]=0;
        $data_arr['floor_info'][0] ["COMPANY_ID"]=0;

		// $fl=0;
		/*foreach ($floor_res as $value) {
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb
            $data_arr['floor_info'][$value->ID] = $value->FLOOR_NAME;
            
        }*/
<<<<<<< HEAD
        
=======

>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb
        $data_arr['company_info'] = $com_res;
        $data_arr['buyer_info'] = $buyer_res;
        $data_arr['location_info'] = $loc_res;
        $data_arr['floor_info'] = $floor_res;
<<<<<<< HEAD
    //  $data_arr['line_info'] = $floor_res;
=======
		//	$data_arr['line_info'] = $floor_res;
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb
        $data_arr['user_id'] = $user_id;

        $complexity_level = array(0 => "", 1 => "Basic", 2 => "Fancy", 3 => "Critical", 4 => "Average");
        $complexity_level_data[0]['fdout'] = 0;
        $complexity_level_data[0]['increment'] = 0;
        $complexity_level_data[0]['target'] = 0; ///complexity_levels       
        $complexity_level_data[1]['fdout'] = 1000;
        $complexity_level_data[1]['increment'] = 100;
        $complexity_level_data[1]['target'] = 1200;
        $complexity_level_data[2]['fdout'] = 800;
        $complexity_level_data[2]['increment'] = 100;
        $complexity_level_data[2]['target'] = 1200;
        $complexity_level_data[3]['fdout'] = 600;
        $complexity_level_data[3]['increment'] = 100;
        $complexity_level_data[3]['target'] = 1200; ///complexity_levels        
        $complexity_level_data[4]['fdout'] = 880;
        $complexity_level_data[4]['increment'] = 100;
        $complexity_level_data[4]['target'] = 1100; ///complexity_levels
        $data_arr['complexity']['type_tmp'][1] = "Learning effect by fixed Quantity";
        $data_arr['complexity']['type_tmp'][2] = "Learning effect by Efficiency Percentage";
<<<<<<< HEAD
        //$data_arr['variable_settings'][0] = 1; // 0=work study integration
        
=======
		//$data_arr['variable_settings'][0] = 1; // 0=work study integration

>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb
        foreach ($complexity_level as $key => $val) {
        	$data_arr['complexity']['level'][$key] = $val;
        }

        foreach ($complexity_level_data as $m_key => $value) {
        	foreach ($value as $key => $val) {
        		$data_arr['complexity']['level_data'][$m_key][$key] = $val;
        	}
        }
        return $data_arr;
    }

    function get_job_data_info($cbo_company_mst, $cbo_buyer_name = "0", $chk_job_wo_po = "0", $txt_date_from, $txt_date_to, $garments_nature = "", $txt_job_prifix = "", $cbo_year_selection, $cbo_string_search_type = "", $txt_order_search = "", $cbo_date_type,$plan_level="0", $txt_style_ref="") {
    	$data_array = array();

        //$shipment_date = $company = $buyer = $job_cond = $order_cond = '';
<<<<<<< HEAD
        $company = " and a.company_name='$cbo_company_mst'";
        if ($cbo_buyer_name>0)
            $buyer = " and a.buyer_name='$cbo_buyer_name'";
        else
            $buyer = "";
        if (trim($txt_order_search))
            $order_cond = " and b.po_number like '%$txt_order_search%'  ";
        else
            $order_cond = "";
        
        $style_cond = "";
        if (trim($txt_style_ref))
            $style_cond = " and a.style_ref_no like '%$txt_style_ref%'  ";
        else
            $style_cond = "";   
        
        $job_cond='';
        $tna_job = ""; 
        
         if (trim($txt_job_prifix) != "")
         {
              $job_cond = " and a.job_no like '%".$txt_job_prifix."%' "; //else  $job_cond=""; 
              $tna_job = " and job_no like '%" . $txt_job_prifix . "%'";
         }
         
        $shipment_date = "";
        $tna_date_cond='';
        if ($cbo_date_type != 1) { // Shipment Date
            if ($txt_date_from != "" && $txt_date_to != ""){
                if($this->db->dbdriver=='mysqli'){
                    $tna_date_cond = "and shipment_date between '" . date("Y-m-d", strtotime($txt_date_from)) . "' and '" . date("Y-m-d", strtotime($txt_date_to)) . "'";
                    $shipment_date = "and b.pub_shipment_date between '" . date("Y-m-d", strtotime($txt_date_from)) . "' and '" . date("Y-m-d", strtotime($txt_date_to)) . "'";
                }else{
                    $tna_date_cond = "and shipment_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
                    $shipment_date = "and b.pub_shipment_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
                }
            }
            else{
                $tna_date_cond = "";
                $shipment_date='';
            }
        }
        else {
            if ($txt_date_from != "" && $txt_date_to != ""){
                if($this->db->dbdriver=='mysqli'){
                    $tna_date_cond = "and task_start_date between '" . date("Y-m-d", strtotime($txt_date_from)) . "' and '" . date("Y-m-d", strtotime($txt_date_to)) . "'";
                }else{
                    $tna_date_cond = "and task_start_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
                }
            }
            else
                $tna_date_cond = "";
=======
    	$company = " and a.company_name='$cbo_company_mst'";
    	if ($cbo_buyer_name>0)
    		$buyer = " and a.buyer_name='$cbo_buyer_name'";
    	else
    		$buyer = "";
    	if (trim($txt_order_search))
    		$order_cond = " and b.po_number like '%$txt_order_search%'  ";
    	else
    		$order_cond = "";

    	$style_cond = "";
    	if (trim($txt_style_ref))
    		$style_cond = " and a.style_ref_no like '%$txt_style_ref%'  ";
    	else
    		$style_cond = "";	

    	$job_cond='';
    	$tna_job = ""; 

    	if (trim($txt_job_prifix) != "" && trim($txt_job_prifix) != 0)
    	{
              $job_cond = " and a.job_no like '%".$txt_job_prifix."%' "; //else  $job_cond=""; 
              $tna_job = " and job_no like '%" . $txt_job_prifix . "%'";
          }

          $shipment_date = "";
          $tna_date_cond='';
        if ($cbo_date_type != 1) { // Shipment Date
        	if ($txt_date_from != "" && $txt_date_to != ""){
        		if($this->db->dbdriver=='mysqli'){
        			$tna_date_cond = "and shipment_date between '" . date("Y-m-d", strtotime($txt_date_from)) . "' and '" . date("Y-m-d", strtotime($txt_date_to)) . "'";
        			$shipment_date = "and b.pub_shipment_date between '" . date("Y-m-d", strtotime($txt_date_from)) . "' and '" . date("Y-m-d", strtotime($txt_date_to)) . "'";
        		}else{
        			$tna_date_cond = "and shipment_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
        			$shipment_date = "and b.pub_shipment_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
        		}
        	}
        	else{
        		$tna_date_cond = "";
        		$shipment_date='';
        	}
        }
        else {
        	if ($txt_date_from != "" && $txt_date_to != ""){
        		if($this->db->dbdriver=='mysqli'){
        			$tna_date_cond = "and task_start_date between '" . date("Y-m-d", strtotime($txt_date_from)) . "' and '" . date("Y-m-d", strtotime($txt_date_to)) . "'";
        		}else{
        			$tna_date_cond = "and task_start_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
        		}
        	}
        	else
        		$tna_date_cond = "";
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb
        }

        $sql = $this->db->query("select min(task_start_date) as TASK_START_DATE,max(task_finish_date) as TASK_FINISH_DATE,PO_NUMBER_ID,JOB_NO from tna_process_mst where is_deleted=0 and status_active=1 $tna_job $tna_date_cond and task_number=86 group by po_number_id,JOB_NO");

        $sel_pos = "";
        $jobs='';
<<<<<<< HEAD
        $sel_jobs_arr ="";// array();
        $sel_jobs= array();
        foreach ($sql->result() as $srows) {
            $tna_task_data[$srows->PO_NUMBER_ID]['task_start_date'] = date("d-m-Y", strtotime($srows->TASK_START_DATE));
            $tna_task_data[$srows->PO_NUMBER_ID]['task_finish_date'] = date("d-m-Y", strtotime($srows->TASK_FINISH_DATE));
            if ($sel_pos == ""){
                $sel_pos = $srows->PO_NUMBER_ID;
                $sel_jobs_arr =$srows->JOB_NO;
            }
            else{
                $sel_pos .= "," . $srows->PO_NUMBER_ID;
                $sel_jobs_arr .= "," .$srows->JOB_NO;
            }
=======
        $sel_jobs_arr ="";
        $sel_jobs= array();
        foreach ($sql->result() as $srows) {
        	$tna_task_data[$srows->PO_NUMBER_ID]['task_start_date'] = date("d-m-Y", strtotime($srows->TASK_START_DATE));
        	$tna_task_data[$srows->PO_NUMBER_ID]['task_finish_date'] = date("d-m-Y", strtotime($srows->TASK_FINISH_DATE));
        	if ($sel_pos == ""){
        		$sel_pos = $srows->PO_NUMBER_ID;
        		$sel_jobs_arr =$srows->JOB_NO;
        	}
        	else{
        		$sel_pos .= "," . $srows->PO_NUMBER_ID;
        		$sel_jobs_arr .= "," .$srows->JOB_NO;
        	}
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb
        }
        $sel_jobs=explode(",",$sel_jobs_arr);
        if ($sel_pos == "") {
        	return $data_array;
        	die;
        }
<<<<<<< HEAD
        
=======

>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb
        // FOR ORACLE 
        $sel_pos2 = array_chunk(array_unique(explode(",", $sel_pos)), 999);
        $sql = "select b.PLAN_QNTY,b.PO_BREAK_DOWN_ID,b.ITEM_NUMBER_ID from ppl_sewing_plan_board a, ppl_sewing_plan_board_powise b where a.plan_id=b.plan_id  and ";
        $p = 1;
        foreach ($sel_pos2 as $job_no_process) {
        	if ($p == 1)
        		$sql .= " (b.po_break_down_id in(" . implode(',', $job_no_process) . ")";
        	else
        		$sql .= " or b.po_break_down_id in(" . implode(',', $job_no_process) . ")";
        	$p++;
        }
        $sql .= ")";

        $sql = $this->db->query($sql);
        $planned_qnty = array();
        if ($sql->num_rows() > 0) {
<<<<<<< HEAD
            foreach ($sql->result() as $srows) {
                if (isset($planned_qnty[$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID])) {
                        $planned_qnty[$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID] += $srows->PLAN_QNTY;
                    } else {
                        $planned_qnty[$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID] = $srows->PLAN_QNTY;
                    }
                }
=======
        	foreach ($sql->result() as $srows) {
        		if (isset($planned_qnty[$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID])) {
        			$planned_qnty[$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID] += $srows->PLAN_QNTY;
        		} else {
        			$planned_qnty[$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID] = $srows->PLAN_QNTY;
        		}
        	}
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb
        }
        // print_r($planned_qnty);die;
        // return $planned_qnty;

        $com_res = $this->db->query("select ID,COMPANY_NAME from lib_company comp where status_active =1 and is_deleted=0  order by company_name")->result();
        $buyer_res = $this->db->query("select a.ID,a.BUYER_NAME from  lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id  and a.status_active=1 and a.is_deleted=0")->result();
        $garment_res = $this->db->query("select ID,ITEM_NAME from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name")->result();

        foreach ($com_res as $value) {
<<<<<<< HEAD
            $comp[$value->ID] = $value->COMPANY_NAME;
       }
        foreach ($buyer_res as $value) {
            $buyer_arr[$value->ID] = $value->BUYER_NAME;
        }
        foreach ($garment_res as $value) {
            $garments_item[$value->ID] = $value->ITEM_NAME;
        }
       
    $garments_nature = 2;

    $sql = "select PO_BREAK_DOWN_ID,sum(production_quantity) as PRODUCTION_QUANTITY from   pro_garments_production_mst where production_type=5 and po_break_down_id in (" . $sel_pos . ") and status_active=1 and is_deleted=0   group by po_break_down_id";

    $sql_data = $this->db->query($sql)->result();
    $k = 0;
    
    foreach ($sql_data as $rows) {
        $production_details[$rows->PO_BREAK_DOWN_ID] = $rows->PRODUCTION_QUANTITY;
    }

        //Oracle queary 
//echo "select ID,GMTS_ITEM_ID,SMV_PCS,COMPLEXITY,QUOT_ID,JOB_NO from  WO_PO_DETAILS_MAS_SET_DETAILS where JOB_NO in ('".implode("','",$sel_jobs)."')"; die;
$set_re = $this->db->query("select ID,GMTS_ITEM_ID,SMV_PCS,COMPLEXITY,QUOT_ID,JOB_NO from  WO_PO_DETAILS_MAS_SET_DETAILS where JOB_NO in ('".implode("','",$sel_jobs)."')")->result();
 
$job_set_data=array();
$quot_ids=array();
foreach ($set_re as $value) {
    $job_set_data[$value->JOB_NO][$value->GMTS_ITEM_ID]['SMV_PCS'] = $value->SMV_PCS;
    $job_set_data[$value->JOB_NO][$value->GMTS_ITEM_ID]['COMPLEXITY'] = $value->COMPLEXITY;
    $job_set_data[$value->JOB_NO][$value->GMTS_ITEM_ID]['QUOT_ID'] = $value->QUOT_ID;
    //$job_set_data[$value->JOB_NO][$value->GMTS_ITEM_ID]['SMV_PCS'] = $value->SMV_PCS;
    $quot_ids[$value->QUOT_ID]=$value->QUOT_ID;
}
=======
        	$comp[$value->ID] = $value->COMPANY_NAME;
        }
        foreach ($buyer_res as $value) {
        	$buyer_arr[$value->ID] = $value->BUYER_NAME;
        }
        foreach ($garment_res as $value) {
        	$garments_item[$value->ID] = $value->ITEM_NAME;
        }

        $garments_nature = 2;

        $sql = "select PO_BREAK_DOWN_ID,sum(production_quantity) as PRODUCTION_QUANTITY from   pro_garments_production_mst where production_type=5 and po_break_down_id in (" . $sel_pos . ") and status_active=1 and is_deleted=0   group by po_break_down_id";

        $sql_data = $this->db->query($sql)->result();
        $k = 0;

        foreach ($sql_data as $rows) {
        	$production_details[$rows->PO_BREAK_DOWN_ID] = $rows->PRODUCTION_QUANTITY;
        }

        //Oracle queary 
        $set_re = $this->db->query("select ID,GMTS_ITEM_ID,SMV_PCS,COMPLEXITY,QUOT_ID,JOB_NO from  WO_PO_DETAILS_MAS_SET_DETAILS where JOB_NO in ('".implode("','",$sel_jobs)."')")->result();

        $job_set_data=array();
        $quot_ids=array();
        foreach ($set_re as $value) {
        	$job_set_data[$value->JOB_NO][$value->GMTS_ITEM_ID]['SMV_PCS'] = $value->SMV_PCS;
        	$job_set_data[$value->JOB_NO][$value->GMTS_ITEM_ID]['COMPLEXITY'] = $value->COMPLEXITY;
        	$job_set_data[$value->JOB_NO][$value->GMTS_ITEM_ID]['QUOT_ID'] = $value->QUOT_ID;
			//$job_set_data[$value->JOB_NO][$value->GMTS_ITEM_ID]['SMV_PCS'] = $value->SMV_PCS;
        	$quot_ids[$value->QUOT_ID]=$value->QUOT_ID;
        }
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb

        $sel_pos2 = array_chunk(array_unique(explode(",", $sel_pos)), 999);

<<<<<<< HEAD
$sql = " select ID,DAY_TARGET,WORKING_HOUR,TOTAL_SMV,GMTS_ITEM_ID from  ppl_gsd_entry_mst "; ///where id in (".implode(",",$quot_ids).")
// Get Efficiecny %; WORKING_HOUR=1; target per hour, 
$sql = $this->db->query($sql)->result();
$day_target=array();
foreach ($sql as $srows) {
    $day_target[$srows->ID][$srows->GMTS_ITEM_ID]['DAY_TARGET'] = $srows->DAY_TARGET;
    $day_target[$srows->ID][$srows->GMTS_ITEM_ID]['WORKING_HOUR'] = $srows->WORKING_HOUR; // from librarray
    $day_target[$srows->ID][$srows->GMTS_ITEM_ID]['TOTAL_SMV'] = $srows->TOTAL_SMV;
}
=======
        $sql = " select ID,DAY_TARGET,WORKING_HOUR,TOTAL_SMV,GMTS_ITEM_ID from  ppl_gsd_entry_mst ";
		// Get Efficiecny %; WORKING_HOUR=1; target per hour, 
        $sql = $this->db->query($sql)->result();
        $day_target=array();
        foreach ($sql as $srows) {
        	$day_target[$srows->ID][$srows->GMTS_ITEM_ID]['DAY_TARGET'] = $srows->DAY_TARGET;
        	$day_target[$srows->ID][$srows->GMTS_ITEM_ID]['WORKING_HOUR'] = $srows->WORKING_HOUR;
        	$day_target[$srows->ID][$srows->GMTS_ITEM_ID]['TOTAL_SMV'] = $srows->TOTAL_SMV;
        }
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb

        $str_shi = '';

        $sel_pos2 = array_chunk(array_unique(explode(",", $sel_pos)), 999);

<<<<<<< HEAD
if($plan_level == 1){
    if($this->db->dbdriver=='mysqli'){
    $sql = "select a.JOB_NO_PREFIX_NUM, a.JOB_NO,a.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_QUANTITY,a.GARMENTS_NATURE, b.ID PO_BREAK_DOWN_ID,b.PO_NUMBER, b.PO_QUANTITY,b.SHIPMENT_DATE AS SHIPMENT_DATE,b.PUB_SHIPMENT_DATE AS PUB_SHIPMENT_DATE, b.PLAN_CUT, YEAR(a.insert_date) as YEAR,b.ID,SET_BREAK_DOWN,TOTAL_SET_QNTY, SET_SMV from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$garments_nature and a.status_active=1 and b.status_active=1 $str_shi $company $buyer $job_cond $order_cond $shipment_date $style_cond"; 
    }else{
            $sql = "select a.JOB_NO_PREFIX_NUM, a.JOB_NO,a.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_QUANTITY,a.GARMENTS_NATURE, b.ID PO_BREAK_DOWN_ID,b.PO_NUMBER, b.PO_QUANTITY,b.SHIPMENT_DATE AS SHIPMENT_DATE,b.PUB_SHIPMENT_DATE AS PUB_SHIPMENT_DATE, b.PLAN_CUT, to_char(a.insert_date,'YYYY') as YEAR,b.ID,SET_BREAK_DOWN,TOTAL_SET_QNTY, SET_SMV from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$garments_nature and a.status_active=1 and b.status_active=1 $str_shi $company $buyer $job_cond $order_cond $shipment_date $style_cond"; 

    }
}else{
    if($plan_level == 2){
        $fields = ",c.COLOR_NUMBER_ID";
        $group_by = ",c.color_number_id";
    }else if($plan_level == 3){
        $fields = ",c.SIZE_NUMBER_ID";
        $group_by = ",c.size_number_id";
    } else if($plan_level == 4){
        $fields = ",c.COLOR_NUMBER_ID,c.SIZE_NUMBER_ID";
        $group_by = ",c.color_number_id,c.size_number_id";
    } else if($plan_level == 5){
        $fields = ",c.COUNTRY_ID";
        $group_by = ",c.country_id";
    }
    else if($plan_level == 6){
        $fields = ",c.COUNTRY_ID,c.COLOR_NUMBER_ID";
        $group_by = ",c.country_id,c.color_number_id";
    }
    else if($plan_level == 7){
        $fields = ",c.COUNTRY_ID,c.COLOR_NUMBER_ID,c.SIZE_NUMBER_ID";
        $group_by = ",c.country_id,c.color_number_id,c.size_number_id";
    }
    else if($plan_level == 8){
        $fields = ",c.COUNTRY_ID,c.SIZE_NUMBER_ID";
        $group_by = ",c.country_id,c.size_number_id";
    }
    if($this->db->dbdriver=='mysqli'){
    $sql = "select a.JOB_NO_PREFIX_NUM, a.JOB_NO,A.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_QUANTITY,a.GARMENTS_NATURE,b.ID PO_BREAK_DOWN_ID,b.PO_NUMBER,b.PO_QUANTITY,b.SHIPMENT_DATE AS SHIPMENT_DATE,b.PUB_SHIPMENT_DATE AS PUB_SHIPMENT_DATE, b.PLAN_CUT,YEAR(a.insert_date) as YEAR,b.ID,SET_BREAK_DOWN,TOTAL_SET_QNTY,SET_SMV, sum(c.plan_cut_qnty) PLAN_CUT_QNTY $fields from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.garments_nature=$garments_nature and a.status_active=1 $str_shi $company $buyer $job_cond $order_cond $shipment_date $style_cond"; 
    }else{
            $sql = "select a.JOB_NO_PREFIX_NUM, a.JOB_NO,A.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_QUANTITY,a.GARMENTS_NATURE,b.ID PO_BREAK_DOWN_ID,b.PO_NUMBER,b.PO_QUANTITY,b.SHIPMENT_DATE AS SHIPMENT_DATE,b.PUB_SHIPMENT_DATE AS PUB_SHIPMENT_DATE, b.PLAN_CUT,to_char(a.insert_date,'YYYY') as YEAR,b.ID,SET_BREAK_DOWN,TOTAL_SET_QNTY,SET_SMV, sum(c.plan_cut_qnty) PLAN_CUT_QNTY $fields from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.garments_nature=$garments_nature and a.status_active=1 $str_shi $company $buyer $job_cond $order_cond $shipment_date $style_cond"; 

    }
}
=======
        if($plan_level == 1){
        	if($this->db->dbdriver=='mysqli'){
        		$sql = "select a.JOB_NO_PREFIX_NUM, a.JOB_NO,a.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_QUANTITY,a.GARMENTS_NATURE, b.ID PO_BREAK_DOWN_ID,b.PO_NUMBER, b.PO_QUANTITY,b.SHIPMENT_DATE AS SHIPMENT_DATE,b.PUB_SHIPMENT_DATE AS PUB_SHIPMENT_DATE, b.PLAN_CUT, YEAR(a.insert_date) as YEAR,b.ID,SET_BREAK_DOWN,TOTAL_SET_QNTY, SET_SMV from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$garments_nature and a.status_active=1 and b.status_active=1 $str_shi $company $buyer $job_cond $order_cond $shipment_date $style_cond"; 
        	}else{
        		$sql = "select a.JOB_NO_PREFIX_NUM, a.JOB_NO,a.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_QUANTITY,a.GARMENTS_NATURE, b.ID PO_BREAK_DOWN_ID,b.PO_NUMBER, b.PO_QUANTITY,b.SHIPMENT_DATE AS SHIPMENT_DATE,b.PUB_SHIPMENT_DATE AS PUB_SHIPMENT_DATE, b.PLAN_CUT, to_char(a.insert_date,'YYYY') as YEAR,b.ID,SET_BREAK_DOWN,TOTAL_SET_QNTY, SET_SMV from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$garments_nature and a.status_active=1 and b.status_active=1 $str_shi $company $buyer $job_cond $order_cond $shipment_date $style_cond"; 
        	}

        }else{
        	if($plan_level == 2){
        		$fields = ",c.COLOR_NUMBER_ID";
        		$group_by = ",c.color_number_id";
        	}else if($plan_level == 3){
        		$fields = ",c.SIZE_NUMBER_ID";
        		$group_by = ",c.size_number_id";
        	} else if($plan_level == 4){
        		$fields = ",c.COLOR_NUMBER_ID,c.SIZE_NUMBER_ID";
        		$group_by = ",c.color_number_id,c.size_number_id";
        	} else if($plan_level == 5){
        		$fields = ",c.COUNTRY_ID";
        		$group_by = ",c.country_id";
        	}
        	else if($plan_level == 6){
        		$fields = ",c.COUNTRY_ID,c.COLOR_NUMBER_ID";
        		$group_by = ",c.country_id,c.color_number_id";
        	}
        	else if($plan_level == 7){
        		$fields = ",c.COUNTRY_ID,c.COLOR_NUMBER_ID,c.SIZE_NUMBER_ID";
        		$group_by = ",c.country_id,c.color_number_id,c.size_number_id";
        	}
        	else if($plan_level == 8){
        		$fields = ",c.COUNTRY_ID,c.SIZE_NUMBER_ID";
        		$group_by = ",c.country_id,c.size_number_id";
        	}
        	if($this->db->dbdriver=='mysqli'){
        		$sql = "select a.JOB_NO_PREFIX_NUM, a.JOB_NO,A.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_QUANTITY,a.GARMENTS_NATURE,b.ID PO_BREAK_DOWN_ID,b.PO_NUMBER,b.PO_QUANTITY,b.SHIPMENT_DATE AS SHIPMENT_DATE,b.PUB_SHIPMENT_DATE AS PUB_SHIPMENT_DATE, b.PLAN_CUT,YEAR(a.insert_date) as YEAR,b.ID,SET_BREAK_DOWN,TOTAL_SET_QNTY,SET_SMV, sum(c.plan_cut_qnty) PLAN_CUT_QNTY $fields from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.garments_nature=$garments_nature and a.status_active=1 $str_shi $company $buyer $job_cond $order_cond $shipment_date $style_cond"; 
        	}else{
        		$sql = "select a.JOB_NO_PREFIX_NUM, a.JOB_NO,A.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_QUANTITY,a.GARMENTS_NATURE,b.ID PO_BREAK_DOWN_ID,b.PO_NUMBER,b.PO_QUANTITY,b.SHIPMENT_DATE AS SHIPMENT_DATE,b.PUB_SHIPMENT_DATE AS PUB_SHIPMENT_DATE, b.PLAN_CUT,to_char(a.insert_date,'YYYY') as YEAR,b.ID,SET_BREAK_DOWN,TOTAL_SET_QNTY,SET_SMV, sum(c.plan_cut_qnty) PLAN_CUT_QNTY $fields from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.garments_nature=$garments_nature and a.status_active=1 $str_shi $company $buyer $job_cond $order_cond $shipment_date $style_cond"; 
        	}
        }

        $p = 1;
        foreach ($sel_pos2 as $job_no_process) {
        	if ($p == 1)
        		$sql .= " and (b.id in(" . implode(',', $job_no_process) . ")";
        	else
        		$sql .= " or b.id in(" . implode(',', $job_no_process) . ")";

        	$p++;


        }
        $sql .= ")";     
        if($plan_level != 1){
        	$sql .= " group by a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name, a.style_ref_no,a.job_quantity, b.id,b.po_number,b.po_quantity,b.shipment_date,b.pub_shipment_date,a.garments_nature, b.plan_cut,a.insert_date,b.id, set_break_down,total_set_qnty,set_smv $group_by";
        }
        $sql .= " order by b.shipment_date ";
        $sql_exe = $this->db->query($sql)->result();
        $i = 0;
        $production_data_arr = $this->get_production_qnty_by_po_item( $sel_pos, '', '', 1 );

        foreach ($sql_exe as $rows) {
        	$set = explode("__", $rows->SET_BREAK_DOWN);

        	foreach ($set as $setdtls) {
        		$setdata = explode("_", $setdtls);
        		$data_array[$i]["ID"] = $rows->ID;
        		$data_array[$i]["JOB_NO"] = $rows->JOB_NO;
        		$data_array[$i]["YEAR"] = $rows->YEAR;
        		$data_array[$i]["BUYER_NAME"] = $buyer_arr[$rows->BUYER_NAME];
        		$data_array[$i]["STYLE_REF_NO"] = $rows->STYLE_REF_NO;
        		$data_array[$i]["JOB_QUANTITY"] = $rows->JOB_QUANTITY;
        		$data_array[$i]["PO_NUMBER"] = $rows->PO_NUMBER;
        		$data_array[$i]["PO_BREAK_DOWN_ID"] = $rows->PO_BREAK_DOWN_ID;
        		$data_array[$i]["ITEM_NAME"] = (!empty($garments_item[$setdata[0]]))?$garments_item[$setdata[0]]:"";
        		$data_array[$i]["ITEM_NUMBER_ID"] = (!empty($setdata[0]))?$setdata[0]:"";
        		$data_array[$i]["ITEM_QNTY"] = $setdata[1] * $rows->PLAN_CUT;

        		$data_array[$i]["TNA_START_DATE"] = $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date'];
        		$data_array[$i]["TNA_FINISH_DATE"] = $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date'];
        		if(isset($production_data_arr[$rows->PO_BREAK_DOWN_ID][$setdata[0]])){
        			$data_array[$i]["PRODUCTION_QNTY"] = $production_data_arr[$rows->PO_BREAK_DOWN_ID][$setdata[0]];
        		}

        		if (isset($planned_qnty[$rows->ID][$setdata[0]])) {
        			$plan_qnty =  $planned_qnty[$rows->ID][$setdata[0]];
        		}else{
        			$plan_qnty = "";
        		}  

        		$data_array[$i]["PLAN_QNTY"] = $plan_qnty;
        		$data_array[$i]["YET_TO_PLAN"] = ($setdata[1] * $rows->PLAN_CUT) - $plan_qnty*1;

        		$data_array[$i]["SMV"] = $setdata[2];
        		$data_array[$i]["PUB_SHIPMENT_DATE"] = date("d-m-Y", strtotime($rows->PUB_SHIPMENT_DATE)) ;
        		if($plan_level == 2){
        			$color_data=$this->db->query("select color_name from lib_color where id=$rows->COLOR_NUMBER_ID")->row();
        			$data_array[$i]["COLOR_NAME"]       = $color_data->COLOR_NAME;
        			$data_array[$i]["COLOR_NUMBER_ID"]  = $rows->COLOR_NUMBER_ID;
        		}else if($plan_level == 3){
        			$size_data=$this->db->query("select SIZE_NAME from lib_size where id=$rows->SIZE_NUMBER_ID")->row();
        			$data_array[$i]["SIZE_NAME"]        = $size_data->SIZE_NAME;
        			$data_array[$i]["SIZE_NUMBER_ID"]   = $rows->SIZE_NUMBER_ID;
        		}else if($plan_level == 4){
        			$color_data=$this->db->query("select color_name from lib_color where id=$rows->COLOR_NUMBER_ID")->row();
        			$size_data=$this->db->query("select SIZE_NAME from lib_size where id=$rows->SIZE_NUMBER_ID")->row();
        			$data_array[$i]["COLOR_NAME"]       = $color_data->COLOR_NAME;
        			$data_array[$i]["COLOR_NUMBER_ID"]    = $rows->COLOR_NUMBER_ID;
        			$data_array[$i]["SIZE_NAME"]        = $size_data->SIZE_NAME;
        			$data_array[$i]["SIZE_NUMBER_ID"]     = $rows->SIZE_NUMBER_ID;
        		}else if($plan_level == 5){
        			$data_array[$i]["COUNTRY_ID"]         = $rows->COUNTRY_ID;
        		}else if($plan_level == 6){
        			$data_array[$i]["COUNTRY_ID"]         = $rows->COUNTRY_ID;
        			$data_array[$i]["COLOR_NUMBER_ID"]    = $rows->COLOR_NUMBER_ID;
        		}else if($plan_level == 7){
        			$data_array[$i]["COUNTRY_ID"]         = $rows->COUNTRY_ID;
        			$data_array[$i]["COLOR_NUMBER_ID"]    = $rows->COLOR_NUMBER_ID;
        			$data_array[$i]["SIZE_NUMBER_ID"]     = $rows->SIZE_NUMBER_ID;
        		}else if($plan_level == 8){
        			$data_array[$i]["COUNTRY_ID"]         = $rows->COUNTRY_ID;
        			$data_array[$i]["SIZE_NUMBER_ID"]     = $rows->SIZE_NUMBER_ID;
        		}

				$data_array[$i]["ORDER_COMPLEXITY"]   ='1'; // = '1';//$rows->ORDER_COMPLEXITY; by Learning  Curve or fixed method
				$data_array[$i]["COMPLEXITY_LEVEL"]    ='4'; // = '1';//$rows->COMPLEXITY_LEVEL; fancy, critical, 
				$data_array[$i]["FIRST_DAY_OUTPUT"]    ='50,60,80'; // = '50,60,80';//$rows->FIRST_DAY_OUTPUT;
				$data_array[$i]["INCREMENT"]    ='100'; // ='100';// $rows->INCREMENT;
				$data_array[$i]["TERGET"]    ='2000'; // = '2000';//$rows->TERGET;

				$i++;

			}
		}
		return $data_array;
	}
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb

	function get_production_qnty_by_po_item( $po_ids, $lineid='', $daterange='', $array_type=1 ){
		$sel_pos2 = array_chunk(array_unique(explode(",", $po_ids)), 999);
		$p = 1;
		$po_id_cond="";
		foreach ($sel_pos2 as $job_no_process) {
			if ($p == 1)
				$po_id_cond = " and (a.po_break_down_id in(" . implode(',', $job_no_process) . ")";
			else
				$po_id_cond .= " or a.po_break_down_id in (" . implode(',', $job_no_process) . ")";

			$p++;


<<<<<<< HEAD
        
}
 $sql .= ")";     
if($plan_level != 1){
    $sql .= " group by a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name, a.style_ref_no,a.job_quantity, b.id,b.po_number,b.po_quantity,b.shipment_date,b.pub_shipment_date,a.garments_nature, b.plan_cut,a.insert_date,b.id, set_break_down,total_set_qnty,set_smv $group_by";
}
$sql .= " order by b.shipment_date ";
// echo $sql;die;
$sql_exe = $this->db->query($sql)->result();
$i = 0;
//$production_data_arr=$this->get_production_qnty_by_po_item($sel_pos); 
 $production_data_arr =   $this->get_production_qnty_by_po_item( $sel_pos, '', '', 1 );
// print_r($production_data_arr); die;
foreach ($sql_exe as $rows) {
    $set = explode("__", $rows->SET_BREAK_DOWN);
    /*if($this->db->dbdriver=='mysqli'){
        $set = explode("__", $rows->set_break_down);
    }else{
        $set = explode("__", $rows->SET_BREAK_DOWN);
    }*/
    foreach ($set as $setdtls) {
        $setdata = explode("_", $setdtls);
        $data_array[$i]["ID"] = $rows->ID;
        $data_array[$i]["JOB_NO"] = $rows->JOB_NO;
        $data_array[$i]["YEAR"] = $rows->YEAR;
        $data_array[$i]["BUYER_NAME"] = $buyer_arr[$rows->BUYER_NAME];
        $data_array[$i]["STYLE_REF_NO"] = $rows->STYLE_REF_NO;//."::".$txt_job_prifix;
        $data_array[$i]["JOB_QUANTITY"] = $rows->JOB_QUANTITY;
        $data_array[$i]["PO_NUMBER"] = $rows->PO_NUMBER;
        $data_array[$i]["PO_BREAK_DOWN_ID"] = $rows->PO_BREAK_DOWN_ID;
        $data_array[$i]["ITEM_NAME"] = (!empty($garments_item[$setdata[0]]))?$garments_item[$setdata[0]]:"";
        $data_array[$i]["ITEM_NUMBER_ID"] = (!empty($setdata[0]))?$setdata[0]:"";
        $data_array[$i]["ITEM_QNTY"] = $setdata[1] * $rows->PLAN_CUT;

        $data_array[$i]["TNA_START_DATE"] = $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date'];
        $data_array[$i]["TNA_FINISH_DATE"] = $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date'];
        if(isset($production_data_arr[$rows->PO_BREAK_DOWN_ID][$setdata[0]])){
        $data_array[$i]["PRODUCTION_QNTY"] = $production_data_arr[$rows->PO_BREAK_DOWN_ID][$setdata[0]];
        }

        if (isset($planned_qnty[$rows->ID][$setdata[0]])) {
            $plan_qnty =  $planned_qnty[$rows->ID][$setdata[0]];
        }else{
            $plan_qnty = "";
        }  

        $data_array[$i]["PLAN_QNTY"] = $plan_qnty;
        //$data_array[$i]["PRODUCTION_QNTY"] = $PRODUCTION_QNTY;
         
        $data_array[$i]["YET_TO_PLAN"] = ($setdata[1] * $rows->PLAN_CUT) - $plan_qnty*1;

        $data_array[$i]["SMV"] = $setdata[2];
        $data_array[$i]["PUB_SHIPMENT_DATE"] = date("d-m-Y", strtotime($rows->PUB_SHIPMENT_DATE)) ;
        if($plan_level == 2){
            $color_data=$this->db->query("select color_name from lib_color where id=$rows->COLOR_NUMBER_ID")->row();
            $data_array[$i]["COLOR_NAME"]       = $color_data->COLOR_NAME;
            $data_array[$i]["COLOR_NUMBER_ID"]  = $rows->COLOR_NUMBER_ID;
        }else if($plan_level == 3){
            $size_data=$this->db->query("select SIZE_NAME from lib_size where id=$rows->SIZE_NUMBER_ID")->row();
            $data_array[$i]["SIZE_NAME"]        = $size_data->SIZE_NAME;
            $data_array[$i]["SIZE_NUMBER_ID"]   = $rows->SIZE_NUMBER_ID;
        }else if($plan_level == 4){
            $color_data=$this->db->query("select color_name from lib_color where id=$rows->COLOR_NUMBER_ID")->row();
            $size_data=$this->db->query("select SIZE_NAME from lib_size where id=$rows->SIZE_NUMBER_ID")->row();
            $data_array[$i]["COLOR_NAME"]       = $color_data->COLOR_NAME;
            $data_array[$i]["COLOR_NUMBER_ID"]    = $rows->COLOR_NUMBER_ID;
            $data_array[$i]["SIZE_NAME"]        = $size_data->SIZE_NAME;
            $data_array[$i]["SIZE_NUMBER_ID"]     = $rows->SIZE_NUMBER_ID;
        }else if($plan_level == 5){
            $data_array[$i]["COUNTRY_ID"]         = $rows->COUNTRY_ID;
        }else if($plan_level == 6){
            $data_array[$i]["COUNTRY_ID"]         = $rows->COUNTRY_ID;
            $data_array[$i]["COLOR_NUMBER_ID"]    = $rows->COLOR_NUMBER_ID;
        }else if($plan_level == 7){
            $data_array[$i]["COUNTRY_ID"]         = $rows->COUNTRY_ID;
            $data_array[$i]["COLOR_NUMBER_ID"]    = $rows->COLOR_NUMBER_ID;
            $data_array[$i]["SIZE_NUMBER_ID"]     = $rows->SIZE_NUMBER_ID;
        }else if($plan_level == 8){
            $data_array[$i]["COUNTRY_ID"]         = $rows->COUNTRY_ID;
            $data_array[$i]["SIZE_NUMBER_ID"]     = $rows->SIZE_NUMBER_ID;
        }
        
        
        $data_array[$i]["ORDER_COMPLEXITY"]   ='1'; // = '1';//$rows->ORDER_COMPLEXITY; by Learning  Curve or fixed method
        $data_array[$i]["COMPLEXITY_LEVEL"]    ='4'; // = '1';//$rows->COMPLEXITY_LEVEL; fancy, critical, 
        $data_array[$i]["FIRST_DAY_OUTPUT"]    ='50,60,80'; // = '50,60,80';//$rows->FIRST_DAY_OUTPUT;
        $data_array[$i]["INCREMENT"]    ='100'; // ='100';// $rows->INCREMENT;
        $data_array[$i]["TERGET"]    ='2000'; // = '2000';//$rows->TERGET;
        //$data_array[$i]["SIZE_NUMBER_ID"]     = $rows->SIZE_NUMBER_ID;
        //,,,,
        
        $i++;
         
    }
}
return $data_array;
}

function get_production_qnty_by_po_item( $po_ids, $lineid='', $daterange='', $array_type=1 ){
    $sel_pos2 = array_chunk(array_unique(explode(",", $po_ids)), 999);
    $p = 1;
    $po_id_cond="";
    foreach ($sel_pos2 as $job_no_process) {
        if ($p == 1)
            $po_id_cond = " and (a.po_break_down_id in(" . implode(',', $job_no_process) . ")";
        else
            $po_id_cond .= " or a.po_break_down_id in (" . implode(',', $job_no_process) . ")";
    
        $p++;
    
              
    }
    $po_id_cond .= ")"; 
    $line_cond= '';
    $date_cond='';
    
    if( $lineid!=0 )
        $line_cond= " and a.SEWING_LINE in ($lineid) ";
    if( $daterange!='' )
        $date_cond= " and a.PRODUCTION_DATE between $daterange ";
        
     
    $production_data_arr=array();;
    $production_sql="select a.PO_BREAK_DOWN_ID,a.ITEM_NUMBER_ID,b.COLOR_SIZE_BREAK_DOWN_ID,b.PRODUCTION_QNTY PRODUCTION_QUANTITY, a.SEWING_LINE SEWING_LINE,a.PRODUCTION_DATE  PRODUCTION_DATE from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=5 $po_id_cond and a.status_active=1 and a.is_deleted=0 $line_cond $date_cond order by a.PRODUCTION_DATE ASC";
    $production_data=$this->db->query($production_sql)->result();
    if( $array_type==1)// PO ITEM level
    {
        foreach($production_data as $row)
        {
            if(isset($production_data_arr[$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID])){
                $production_data_arr[$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID]+=$row->PRODUCTION_QUANTITY;
            }else{
                $production_data_arr[$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID]=$row->PRODUCTION_QUANTITY;
            }
        }
         return $production_data_arr;
    }
    else if( $array_type==2)// line date level
    {
        foreach($production_data as $row)
        {
            if(isset($production_data_arr[$row->SEWING_LINE][$row->PRODUCTION_DATE])){
                $production_data_arr[$row->SEWING_LINE][$row->PRODUCTION_DATE]+=$row->PRODUCTION_QUANTITY;
            }else{
                $production_data_arr[$row->SEWING_LINE][$row->PRODUCTION_DATE]=$row->PRODUCTION_QUANTITY;
            }
        }
         return $production_data_arr;
    }
    else if( $array_type==3)// line date level
    { $production_data_qnty=0;
     $prd_date="";
        foreach($production_data as $row)
        {
             
            $production_data_qnty +=$row->PRODUCTION_QUANTITY;
             $prd_date=$row->PRODUCTION_DATE;
        }
          $production_data_arr =array("production_data_qnty"=>($production_data_qnty),"prd_date"=>$prd_date);
          return $production_data_arr;
    }
   
}

function get_production_qnty_info_by_plan_level($company_id,$job_no,$po_id,$item_id,$plan_level,$color_num_id,$size_num_id,$resource_allocation_type,$line_id,$line_names_ids,$line_allocated){
    $company_cond = " and company_id='$company_id'";
    $po_id_cond = "";
    if($po_id != "")
    {
        $po_id_cond = " and po_break_down_id='$po_id'";
    }
    $color_size_sql="select ID,JOB_NO_MST,PO_BREAK_DOWN_ID,ITEM_NUMBER_ID,COLOR_NUMBER_ID,SIZE_NUMBER_ID,COUNTRY_ID from wo_po_color_size_breakdown where po_break_down_id='$po_id'";
    $color_size_data=$this->db->query($color_size_sql)->result();
    $color_size_data_arr = array();
    foreach($color_size_data as $row)
    {
        $color_size_data_arr[$row->JOB_NO_MST][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID][]=$row->ID;
        /*if($this->db->dbdriver=='mysqli'){
        $color_size_data_arr[$row->job_no_mst][$row->po_break_down_id][$row->item_number_id][$row->color_number_id][$row->size_number_id][]=$row->id;
        }else{
        $color_size_data_arr[$row->JOB_NO_MST][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID][]=$row->ID;
        }*/
    }
    $production_data_arr=array();
    if($resource_allocation_type != 1)
    {
        $production_sql="select a.ID,a.PO_BREAK_DOWN_ID,a.PRODUCTION_DATE,a.SEWING_LINE,a.COMPANY_ID,a.LOCATION,b.COLOR_SIZE_BREAK_DOWN_ID,sum(b.production_qnty) PRODUCTION_QUANTITY from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=5 $po_id_cond and a.status_active=1 and a.is_deleted=0   and a.sewing_line in (".implode(",",$line_names_ids).") group by a.id,a.production_date,a.po_break_down_id, a.sewing_line,a.company_id,a.location,b.color_size_break_down_id order by a.sewing_line,a.po_break_down_id, a.production_date";
        $production_data=$this->db->query($production_sql)->result();
        foreach($production_data as $row)
        {
            $production_data_arr[$row->SEWING_LINE][$row->COLOR_SIZE_BREAK_DOWN_ID]=$row->PRODUCTION_QUANTITY;
            /*if($this->db->dbdriver=='mysqli'){
                $production_data_arr[$row->sewing_line][$row->color_size_break_down_id]=$row->production_quantity;
            }else{
                $production_data_arr[$row->SEWING_LINE][$row->color_size_break_down_id]=$row->PRODUCTION_QUANTITY;
            }*/
        }
    }
    else
    {
        $production_sql="select a.ID,a.PO_BREAK_DOWN_ID,a.PRODUCTION_DATE,a.SEWING_LINE,a.COMPANY_ID,a.LOCATION,b.COLOR_SIZE_BREAK_DOWN_ID,SUM(b.production_qnty) PRODUCTION_QUANTITY from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=5 $po_id_cond and a.status_active=1 and a.is_deleted=0 and a.sewing_line in (".implode(",",$line_allocated).") group by a.id,a.production_date,a.po_break_down_id, a.sewing_line,a.company_id,a.location,b.color_size_break_down_id order by a.sewing_line,a.po_break_down_id, a.production_date";
        $production_data=$this->db->query($production_sql)->result();
        foreach($production_data as $row)
        {
            $production_data_arr[$row->SEWING_LINE][$row->COLOR_SIZE_BREAK_DOWN_ID] = $row->PRODUCTION_QUANTITY;
            /*if($this->db->dbdriver=='mysqli'){
            $production_data_arr[$row->sewing_line][$row->color_size_break_down_id] = $row->production_quantity;
            }else{
            $production_data_arr[$row->SEWING_LINE][$row->COLOR_SIZE_BREAK_DOWN_ID] = $row->PRODUCTION_QUANTITY;
            }*/
        }
    }
=======
		}
		$po_id_cond .= ")"; 
		$line_cond= '';
		$date_cond='';

		if( $lineid!=0 )
			$line_cond= " and a.SEWING_LINE in ($lineid) ";
		if( $daterange!='' )
			$date_cond= " and a.PRODUCTION_DATE between $daterange ";


		$production_data_arr=array();;
		$production_sql="select a.PO_BREAK_DOWN_ID,a.ITEM_NUMBER_ID,b.COLOR_SIZE_BREAK_DOWN_ID,b.PRODUCTION_QNTY PRODUCTION_QUANTITY, a.SEWING_LINE SEWING_LINE,a.PRODUCTION_DATE  PRODUCTION_DATE from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=5 $po_id_cond and a.status_active=1 and a.is_deleted=0 $line_cond $date_cond order by a.PRODUCTION_DATE ASC";
		$production_data=$this->db->query($production_sql)->result();
		if( $array_type==1)// PO ITEM level
		{
			foreach($production_data as $row)
			{
				if(isset($production_data_arr[$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID])){
					$production_data_arr[$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID]+=$row->PRODUCTION_QUANTITY;
				}else{
					$production_data_arr[$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID]=$row->PRODUCTION_QUANTITY;
				}
			}
			return $production_data_arr;
		}
		else if( $array_type==2)// line date level
		{
			foreach($production_data as $row)
			{
				if(isset($production_data_arr[$row->SEWING_LINE][$row->PRODUCTION_DATE])){
					$production_data_arr[$row->SEWING_LINE][$row->PRODUCTION_DATE]+=$row->PRODUCTION_QUANTITY;
				}else{
					$production_data_arr[$row->SEWING_LINE][$row->PRODUCTION_DATE]=$row->PRODUCTION_QUANTITY;
				}
			}
			return $production_data_arr;
		}
		else if( $array_type==3)// line date level
		{ $production_data_qnty=0;
			$prd_date="";
			foreach($production_data as $row)
			{

				$production_data_qnty +=$row->PRODUCTION_QUANTITY;
				$prd_date=$row->PRODUCTION_DATE;
			}
			$production_data_arr =array("production_data_qnty"=>($production_data_qnty),"prd_date"=>$prd_date);
			return $production_data_arr;
		}

	}

	function get_production_qnty_info_by_plan_level($company_id,$job_no,$po_id,$item_id,$plan_level,$color_num_id,$size_num_id,$resource_allocation_type,$line_id,$line_names_ids,$line_allocated){
		$company_cond = " and company_id='$company_id'";
		$po_id_cond = "";
		if($po_id != "")
		{
			$po_id_cond = " and po_break_down_id='$po_id'";
		}
		$color_size_sql="select ID,JOB_NO_MST,PO_BREAK_DOWN_ID,ITEM_NUMBER_ID,COLOR_NUMBER_ID,SIZE_NUMBER_ID,COUNTRY_ID from wo_po_color_size_breakdown where po_break_down_id='$po_id'";
		$color_size_data=$this->db->query($color_size_sql)->result();
		$color_size_data_arr = array();
		foreach($color_size_data as $row)
		{
			$color_size_data_arr[$row->JOB_NO_MST][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID][]=$row->ID;
			/*if($this->db->dbdriver=='mysqli'){
		        $color_size_data_arr[$row->job_no_mst][$row->po_break_down_id][$row->item_number_id][$row->color_number_id][$row->size_number_id][]=$row->id;
				}else{
				$color_size_data_arr[$row->JOB_NO_MST][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID][]=$row->ID;
			}*/
		}
		$production_data_arr=array();
		if($resource_allocation_type != 1)
		{
			$production_sql="select a.ID,a.PO_BREAK_DOWN_ID,a.PRODUCTION_DATE,a.SEWING_LINE,a.COMPANY_ID,a.LOCATION,b.COLOR_SIZE_BREAK_DOWN_ID,sum(b.production_qnty) PRODUCTION_QUANTITY from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=5 $po_id_cond and a.status_active=1 and a.is_deleted=0   and a.sewing_line in (".implode(",",$line_names_ids).") group by a.id,a.production_date,a.po_break_down_id, a.sewing_line,a.company_id,a.location,b.color_size_break_down_id order by a.sewing_line,a.po_break_down_id, a.production_date";
			$production_data=$this->db->query($production_sql)->result();
			foreach($production_data as $row)
			{
				$production_data_arr[$row->SEWING_LINE][$row->COLOR_SIZE_BREAK_DOWN_ID]=$row->PRODUCTION_QUANTITY;
			/*if($this->db->dbdriver=='mysqli'){
				$production_data_arr[$row->sewing_line][$row->color_size_break_down_id]=$row->production_quantity;
			}else{
				$production_data_arr[$row->SEWING_LINE][$row->color_size_break_down_id]=$row->PRODUCTION_QUANTITY;
			}*/
		}
	}
	else
	{
		$production_sql="select a.ID,a.PO_BREAK_DOWN_ID,a.PRODUCTION_DATE,a.SEWING_LINE,a.COMPANY_ID,a.LOCATION,b.COLOR_SIZE_BREAK_DOWN_ID,SUM(b.production_qnty) PRODUCTION_QUANTITY from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=5 $po_id_cond and a.status_active=1 and a.is_deleted=0 and a.sewing_line in (".implode(",",$line_allocated).") group by a.id,a.production_date,a.po_break_down_id, a.sewing_line,a.company_id,a.location,b.color_size_break_down_id order by a.sewing_line,a.po_break_down_id, a.production_date";
		$production_data=$this->db->query($production_sql)->result();
		foreach($production_data as $row)
		{
			$production_data_arr[$row->SEWING_LINE][$row->COLOR_SIZE_BREAK_DOWN_ID] = $row->PRODUCTION_QUANTITY;
			/*if($this->db->dbdriver=='mysqli'){
	            $production_data_arr[$row->sewing_line][$row->color_size_break_down_id] = $row->production_quantity;
				}else{
				$production_data_arr[$row->SEWING_LINE][$row->COLOR_SIZE_BREAK_DOWN_ID] = $row->PRODUCTION_QUANTITY;
			}*/
		}
	}
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb

	$production_qnty = 0;
	if($plan_level == 2){
		$color_num_ids = explode(",",$color_num_id);
		foreach ($color_num_ids as $color_num_row) {
         	//echo $job_no . "**" . $po_id . "**" . $item_id . "**" . $color_num_row;
			foreach( $color_size_data_arr[$job_no][$po_id][$item_id][$color_num_row] as $size=>$csid)
			{
				if(isset($production_data_arr[$po_id][$line_id])){
					$production_qnty += $production_data_arr[$line_id][$csid];
				}
			}
		}
	}

	$size_num_ids = explode(",",$size_num_id);
	$job_nos = explode(",",$job_no);
	$po_ids = explode(",",$po_id);
	$item_ids = explode(",",$item_id);
	if($plan_level == 3){        
		foreach ($size_num_ids as $key => $size_num_row) {
			foreach( $color_size_data_arr[$job_nos[$key]][$po_ids[$key]][$item_ids[$key]] as $color=>$size_id) {
				foreach ($size_id as $size => $csid) {
					if($size_num_row == $size){
						$production_qnty += $production_data_arr[$line_id][$csid];
					}
				}
			}
		}
	}

	if($plan_level == 4){
		$color_num_ids = explode(",",$color_num_id);
		foreach ($color_num_ids as $key => $color_num_row) {
			foreach($color_size_data_arr[$job_nos[$key]][$po_ids[$key]][$item_ids[$key]][$color_num_row][$size_num_ids[$key]] as $csid)
			{
				if(!empty($production_data_arr)){
					$production_qnty += $production_data_arr[$line_id][$csid];
				}else{
					$production_qnty += 0;
				}
			}
		}
	}
	return $production_qnty;
}

function get_plan_data_info($company_id,$location_id,$floor_id,$txt_date_from, $user_id,$auto_balancing=1) 
{
<<<<<<< HEAD
    $floor_cond_res= '';
    $floor_cond_line= '';
    $floor_cond_line_sts= '';
    if($floor_id>0)
    { 
        $floor_cond_res= " and floor_id='$floor_id' ";
        $floor_cond_line= " and floor_name='$floor_id' ";
        //$floor_cond_line_sts= " and floor_name in ($floor_id,0) ";
    }
    $user_arr=array();
    $user_arr_type=array();
    $location_res = $this->db->query("select ID,USER_NAME,IS_PLANNER from user_passwd ")->result();
    foreach ($location_res as $value) 
    {
        $user_arr[$value->ID] = $value->USER_NAME;
        $user_arr_type[$value->ID] = $value->IS_PLANNER;
    }
    $user_arr[0] = '';
    $table_locked='';
    $need_to_update=0;
    $need_to_insert=1;
    $max_id=0;
     
    $ppl_sewing_plan_board_dtls_data =array();
    if($user_arr_type[$user_id]==1)
    {
        $sql_line="select BOARD_STATUS,USER_ID from planning_board_status where company_name='$company_id' and location_name='$location_id'  $floor_cond_line_sts order by BOARD_STATUS asc";
        $new_line_resource= $this->db->query($sql_line)->result();
        foreach($new_line_resource as $ids=>$vals){
            if($vals->USER_ID!=$user_id)
            {
                if( $vals->BOARD_STATUS==1 )
                    $table_locked=$user_arr[$vals->USER_ID];
            }
            else
            {
                if($vals->BOARD_STATUS!=1)
                    $need_to_update=1;
                else
                    $need_to_insert=0;
            }
        }
        if($table_locked=='') // need to lock board for this user
        {
            if($need_to_update==0 && $need_to_insert==1) // New Insert
            {
                $max_id  = $this->get_max_value("PLANNING_BOARD_STATUS","ID") + 1;
                $ppl_sewing_plan_board_dtls_data = array(
                            'ID'        => $max_id,
                            'COMPANY_NAME'   => $company_id,
                            'LOCATION_NAME' =>$location_id,
                            'FLOOR_NAME' => $floor_id,
                            'USER_ID' => $user_id,
                            'BOARD_STATUS' => 1
                            );
                            $this->insertData($ppl_sewing_plan_board_dtls_data, "PLANNING_BOARD_STATUS");
            }
            else{
                $this->db->query("update planning_board_status set board_status=1 where company_name='$company_id' and location_name='$location_id' and user_id=$user_id  $floor_cond_line");
            }
        }
    }
    else
        $table_locked='VISITOR';
     
    //$table_locked='';
    $line_names_ids=array();
    $sql_line="select ID,LINE_NAME from lib_sewing_line where company_name='$company_id' and location_name='$location_id' $floor_cond_line order by sewing_line_serial";
    
    $new_line_resource= $this->db->query($sql_line)->result();
    foreach($new_line_resource as $ids=>$vals){
        $line_names_ids[$vals->ID]=$vals->ID;
    }
    //print_r($line_names_ids);die;
    if(count($line_names_ids)<1)
    {
        die;
    }
    $company_cond = " and company_id='$company_id'";
    $po_id_cond = "";
=======
	$floor_cond_res= '';
	$floor_cond_line= '';
	$floor_cond_line_sts= '';
	if($floor_id>0)
	{ 
		$floor_cond_res= " and floor_id='$floor_id' ";
		$floor_cond_line= " and floor_name='$floor_id' ";
		//$floor_cond_line_sts= " and floor_name in ($floor_id,0) ";
	}
	$user_arr=array();
	$user_arr_type=array();
	$location_res = $this->db->query("select ID,USER_NAME,IS_PLANNER from user_passwd ")->result();
	foreach ($location_res as $value) 
	{
		$user_arr[$value->ID] = $value->USER_NAME;
		$user_arr_type[$value->ID] = $value->IS_PLANNER;
	}
	$user_arr[0] = '';
	$table_locked='';
	$need_to_update=0;
	$need_to_insert=1;
	$max_id=0;

	$ppl_sewing_plan_board_dtls_data =array();
	if($user_arr_type[$user_id]==1)
	{
		$sql_line="select BOARD_STATUS,USER_ID from planning_board_status where company_name='$company_id' and location_name='$location_id'  $floor_cond_line_sts order by BOARD_STATUS asc";
		$new_line_resource= $this->db->query($sql_line)->result();
		foreach($new_line_resource as $ids=>$vals){
			if($vals->USER_ID!=$user_id)
			{
				if( $vals->BOARD_STATUS==1 )
					$table_locked=$user_arr[$vals->USER_ID];
			}
			else
			{
				if($vals->BOARD_STATUS!=1)
					$need_to_update=1;
				else
					$need_to_insert=0;
			}
		}
		if($table_locked=='') // need to lock board for this user
		{
			if($need_to_update==0 && $need_to_insert==1) // New Insert
			{
				$max_id  = $this->get_max_value("PLANNING_BOARD_STATUS","ID") + 1;
				$ppl_sewing_plan_board_dtls_data = array(
					'ID'        => $max_id,
					'COMPANY_NAME'   => $company_id,
					'LOCATION_NAME' =>$location_id,
					'FLOOR_NAME' => $floor_id,
					'USER_ID' => $user_id,
					'BOARD_STATUS' => 1
				);
				$this->insertData($ppl_sewing_plan_board_dtls_data, "PLANNING_BOARD_STATUS");
			}
			else{
				$this->db->query("update planning_board_status set board_status=1 where company_name='$company_id' and location_name='$location_id' and user_id=$user_id  $floor_cond_line");
			}
		}
	}
	else
		$table_locked='VISITOR';

	//$table_locked='';
	$line_names_ids=array();
	$sql_line="select ID,LINE_NAME from lib_sewing_line where company_name='$company_id' and location_name='$location_id' $floor_cond_line order by sewing_line_serial";
	
	$new_line_resource= $this->db->query($sql_line)->result();
	foreach($new_line_resource as $ids=>$vals){
		$line_names_ids[$vals->ID]=$vals->ID;
	}
	//print_r($line_names_ids);die;
	if(count($line_names_ids)<1)
	{
		die;
	}
	$company_cond = " and company_id='$company_id'";
	$po_id_cond = "";
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb

	$from_date=date("Y-m-d", strtotime($txt_date_from));

<<<<<<< HEAD
    $days_forward=120;
    function add_date($orgDate,$days)
    {
        $cd = strtotime($orgDate);
        $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$days,date('Y',$cd)));
        return $retDAY;
    }

    $to_date=add_date($from_date,$days_forward);
    if($this->db->dbdriver=='mysqli'){
   $plan_sql="select a.ID,a.LINE_ID,a.PLAN_ID,a.START_DATE,a.START_HOUR,a.END_DATE,a.END_HOUR,a.DURATION,a.PLAN_QNTY,a.COMP_LEVEL,a.FIRST_DAY_OUTPUT, a.INCREMENT_QTY,a.TERGET,a.DAY_WISE_PLAN,a.COMPANY_ID,a.LOCATION_ID,a.OFF_DAY_PLAN,a.ORDER_COMPLEXITY,a.SHIP_DATE, EXTRA_PARAM,a.PLAN_LEVEL,a.FIRST_DAY_CAPACITY,a.LAST_DAY_CAPACITY,a.SEQ_NO,a.PO_COMPANY_ID,a.USE_LEARNING_CURVE,a.CURRENT_PRODUCTION_DATE,a.PRODUCTION_PERCENT, a.TOP_BORDER_COLOR,a.BOTTOM_BORDER_COLOR,a.LEFT_COLOR,a.RIGHT_COLOR,1   AS JOB_NO,GROUP_CONCAT(b.PO_BREAK_DOWN_ID)  AS PO_BREAK_DOWN_ID,GROUP_CONCAT(b.ITEM_NUMBER_ID) AS ITEM_NUMBER_ID, GROUP_CONCAT(b.SIZE_NUMBER_ID)  AS SIZE_NUMBER_ID, GROUP_CONCAT(b.COLOR_NUMBER_ID) AS COLOR_NUMBER_ID, GROUP_CONCAT(b.COUNTRY_ID)  AS COUNTRY_ID,a.MERGE_COMMENTS,a.MERGE_TYPE,a.INSERT_DATE  from ppl_sewing_plan_board a,ppl_sewing_plan_board_powise b where a.plan_id=b.plan_id and a.company_id='$company_id'  and a.line_id in( ".implode(",",$line_names_ids).") and (a.start_date between STR_TO_DATE('".$from_date."', '%Y-%m-%d')  and STR_TO_DATE('".$to_date."','%Y-%m-%d')   or a.end_date between STR_TO_DATE('".$from_date."','%Y-%m-%d')  and STR_TO_DATE('".$to_date."','%Y-%m-%d')  or ( a.start_date < STR_TO_DATE('".$from_date."','%Y-%m-%d')  and a.end_date> STR_TO_DATE('".$to_date."','%Y-%m-%d')))  and a.status_active=1 group by a.id,a.line_id,a.plan_id,a.start_date,a.start_hour,a.end_date,a.end_hour,a.duration, a.plan_qnty,a.comp_level,a.first_day_output, a.increment_qty,a.terget,a.day_wise_plan,a.company_id,a.location_id,a.item_number_id, a.off_day_plan,a.order_complexity,a.ship_date, extra_param,a.plan_level,a.first_day_capacity,a.last_day_capacity,a.seq_no,a.po_company_id,a.use_learning_curve,a.current_production_date,a.production_percent, a.top_border_color,a.bottom_border_color,a.left_color,a.right_color,  a.INSERT_DATE ";
 
        }else{
             $plan_sql="select a.id,a.line_id,a.plan_id,a.start_date,a.start_hour,a.end_date,a.end_hour,a.duration,a.plan_qnty,a.comp_level,a.first_day_output, a.increment_qty,a.terget,a.day_wise_plan,a.company_id,a.location_id,a.off_day_plan,a.order_complexity,a.ship_date, extra_param,a.plan_level,a.first_day_capacity,a.last_day_capacity,a.seq_no,a.po_company_id,1 as use_learning_curve,a.current_production_date,a.production_percent, a.top_border_color,a.bottom_border_color,a.left_color,a.right_color,1 as job_no,listagg(b.po_break_down_id, ',') within group (order by b.po_break_down_id) as po_break_down_id,listagg(b.item_number_id, ',') within group (order by b.item_number_id) as item_number_id, listagg(b.size_number_id, ',') within group (order by b.size_number_id) as size_number_id, listagg(b.color_number_id, ',') within group (order by b.color_number_id) as color_number_id, listagg(b.country_id, ',') within group (order by b.country_id) as country_id, a.MERGE_COMMENTS,a.MERGE_TYPE,a.INSERT_DATE   from ppl_sewing_plan_board a,ppl_sewing_plan_board_powise b  where a.plan_id=b.plan_id  and a.company_id='$company_id' $po_id_cond and a.line_id in( ".implode(",",$line_names_ids).") and (a.start_date between to_date('".$from_date."','yyyy-mm-dd')  and to_date('".$to_date."','yyyy-mm-dd')   or a.end_date between to_date('".$from_date."','yyyy-mm-dd')  and to_date('".$to_date."','yyyy-mm-dd')  or ( a.start_date < to_date('".$from_date."','yyyy-mm-dd')  and a.end_date> to_date('".$to_date."','yyyy-mm-dd')))  and a.status_active=1 group by a.id,a.line_id,a.plan_id,a.start_date,a.start_hour,a.end_date,a.end_hour,a.duration, a.plan_qnty,a.comp_level,a.first_day_output, a.increment_qty,a.terget,a.day_wise_plan,a.company_id,a.location_id,a.item_number_id, a.off_day_plan,a.order_complexity,a.ship_date, extra_param,a.plan_level,a.first_day_capacity,a.last_day_capacity,a.seq_no,a.po_company_id,a.use_learning_curve,a.current_production_date,a.production_percent, a.top_border_color,a.bottom_border_color,a.left_color,a.right_color , a.MERGE_COMMENTS,a.MERGE_TYPE,a.INSERT_DATE ";
        }
=======
	$days_forward=120;
	$days_backward=30;
	function add_date($orgDate,$days,$type)
	{
		$cd = strtotime($orgDate);
		if($type == 1){
			$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$days,date('Y',$cd)));
		}else{
			$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)-$days,date('Y',$cd)));
		}
		return $retDAY;
	}

	$from_date=add_date($from_date,$days_backward,0);
	$to_date=add_date($from_date,$days_forward,1);
	if($this->db->dbdriver=='mysqli'){
		$plan_sql="select a.ID,a.LINE_ID,a.PLAN_ID,a.START_DATE,a.START_HOUR,a.END_DATE,a.END_HOUR,a.DURATION,a.PLAN_QNTY,a.COMP_LEVEL,a.FIRST_DAY_OUTPUT, a.INCREMENT_QTY,a.TERGET,a.DAY_WISE_PLAN,a.COMPANY_ID,a.LOCATION_ID,a.OFF_DAY_PLAN,a.ORDER_COMPLEXITY,a.SHIP_DATE, EXTRA_PARAM,a.PLAN_LEVEL,a.FIRST_DAY_CAPACITY,a.LAST_DAY_CAPACITY,a.SEQ_NO,a.PO_COMPANY_ID,a.USE_LEARNING_CURVE,a.CURRENT_PRODUCTION_DATE,a.PRODUCTION_PERCENT, a.TOP_BORDER_COLOR,a.BOTTOM_BORDER_COLOR,a.LEFT_COLOR,a.RIGHT_COLOR,1   AS JOB_NO,GROUP_CONCAT(b.PO_BREAK_DOWN_ID)  AS PO_BREAK_DOWN_ID,GROUP_CONCAT(b.ITEM_NUMBER_ID) AS ITEM_NUMBER_ID, GROUP_CONCAT(b.SIZE_NUMBER_ID)  AS SIZE_NUMBER_ID, GROUP_CONCAT(b.COLOR_NUMBER_ID) AS COLOR_NUMBER_ID, GROUP_CONCAT(b.COUNTRY_ID)  AS COUNTRY_ID,a.MERGE_COMMENTS,a.MERGE_TYPE,a.INSERT_DATE  from ppl_sewing_plan_board a,ppl_sewing_plan_board_powise b where a.plan_id=b.plan_id and a.company_id='$company_id'  and a.line_id in( ".implode(",",$line_names_ids).") and (a.start_date between STR_TO_DATE('".$from_date."', '%Y-%m-%d')  and STR_TO_DATE('".$to_date."','%Y-%m-%d')   or a.end_date between STR_TO_DATE('".$from_date."','%Y-%m-%d')  and STR_TO_DATE('".$to_date."','%Y-%m-%d')  or ( a.start_date < STR_TO_DATE('".$from_date."','%Y-%m-%d')  and a.end_date> STR_TO_DATE('".$to_date."','%Y-%m-%d')))  and a.status_active=1 group by a.id,a.line_id,a.plan_id,a.start_date,a.start_hour,a.end_date,a.end_hour,a.duration, a.plan_qnty,a.comp_level,a.first_day_output, a.increment_qty,a.terget,a.day_wise_plan,a.company_id,a.location_id,a.item_number_id, a.off_day_plan,a.order_complexity,a.ship_date, extra_param,a.plan_level,a.first_day_capacity,a.last_day_capacity,a.seq_no,a.po_company_id,a.use_learning_curve,a.current_production_date,a.production_percent, a.top_border_color,a.bottom_border_color,a.left_color,a.right_color,  a.INSERT_DATE ";

	}else{
		$plan_sql="select a.id,a.line_id,a.plan_id,a.start_date,a.start_hour,a.end_date,a.end_hour,a.duration,a.plan_qnty,a.comp_level,a.first_day_output, a.increment_qty,a.terget,a.day_wise_plan,a.company_id,a.location_id,a.off_day_plan,a.order_complexity,a.ship_date, extra_param,a.plan_level,a.first_day_capacity,a.last_day_capacity,a.seq_no,a.po_company_id,1 as use_learning_curve,a.current_production_date,a.production_percent, a.top_border_color,a.bottom_border_color,a.left_color,a.right_color,1 as job_no,listagg(b.po_break_down_id, ',') within group (order by b.po_break_down_id) as po_break_down_id,listagg(b.item_number_id, ',') within group (order by b.item_number_id) as item_number_id, listagg(b.size_number_id, ',') within group (order by b.size_number_id) as size_number_id, listagg(b.color_number_id, ',') within group (order by b.color_number_id) as color_number_id, listagg(b.country_id, ',') within group (order by b.country_id) as country_id, a.MERGE_COMMENTS,a.MERGE_TYPE,a.INSERT_DATE   from ppl_sewing_plan_board a,ppl_sewing_plan_board_powise b  where a.plan_id=b.plan_id  and a.company_id='$company_id' $po_id_cond and a.line_id in( ".implode(",",$line_names_ids).") and (a.start_date between to_date('".$from_date."','yyyy-mm-dd')  and to_date('".$to_date."','yyyy-mm-dd')   or a.end_date between to_date('".$from_date."','yyyy-mm-dd')  and to_date('".$to_date."','yyyy-mm-dd')  or ( a.start_date < to_date('".$from_date."','yyyy-mm-dd')  and a.end_date> to_date('".$to_date."','yyyy-mm-dd')))  and a.status_active=1 group by a.id,a.line_id,a.plan_id,a.start_date,a.start_hour,a.end_date,a.end_hour,a.duration, a.plan_qnty,a.comp_level,a.first_day_output, a.increment_qty,a.terget,a.day_wise_plan,a.company_id,a.location_id,a.item_number_id, a.off_day_plan,a.order_complexity,a.ship_date, extra_param,a.plan_level,a.first_day_capacity,a.last_day_capacity,a.seq_no,a.po_company_id,a.use_learning_curve,a.current_production_date,a.production_percent, a.top_border_color,a.bottom_border_color,a.left_color,a.right_color , a.MERGE_COMMENTS,a.MERGE_TYPE,a.INSERT_DATE ";
	}
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb
  //echo $plan_sql; die;

<<<<<<< HEAD
    foreach ($com_res as $value)
    {
        $comp[$value->ID] = $value->COMPANY_NAME;
    }
=======
	$plan_data= $this->db->query($plan_sql)->result();
	$com_res = $this->db->query("select ID,COMPANY_NAME from lib_company where status_active =1 and is_deleted=0  order by company_name")->result();

	foreach ($com_res as $value)
	{
		$comp[$value->ID] = $value->COMPANY_NAME;
	}
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb

	$location_res = $this->db->query("select ID,LOCATION_NAME from lib_location  where status_active =1 and is_deleted=0  order by location_name")->result();

<<<<<<< HEAD
    foreach ($location_res as $value) 
    {
        $location_arr[$value->ID] = $value->LOCATION_NAME;
    }
    $garment_res = $this->db->query("select ID,ITEM_NAME from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name")->result();

    foreach ($garment_res as $value) 
    {
        $garments_item[$value->ID] = $value->ITEM_NAME;
    }
    $npos=array();
    $is_plan=0;
    foreach ($plan_data as $rows) 
    {
        //if($rows->PO_BREAK_DOWN_ID>0)
            $npos[$rows->PO_BREAK_DOWN_ID]=$rows->PO_BREAK_DOWN_ID*1;
            $is_plan=1;
    }
    
    $p = 1;
    $sel_pos2 = array_chunk($npos, 999);
    $sql='';
    $sql2='';
    foreach ($sel_pos2 as $job_no_process) {
        if ($p == 1)
        {
            $sql .= " and (PO_NUMBER_ID in(" . implode(',', $job_no_process) . ")";
            $sql2 .= " and (c.id in(" . implode(',', $job_no_process) . ")";
        }
        else
        {
            $sql .= " or PO_NUMBER_ID in(" . implode(',', $job_no_process) . ")";
            $sql2 .= " or (c.id in(" . implode(',', $job_no_process) . ")";
        }
    
        $p++;
    }
     $sql .= ")";   
      $sql2 .= ")";   
    
    if($is_plan==0){ 
        $sql='';
        $sql2=''; 
    } 
    
    $sqls = $this->db->query("select min(task_start_date) as TASK_START_DATE,max(task_finish_date) as TASK_FINISH_DATE,PO_NUMBER_ID from tna_process_mst where is_deleted=0 and status_active=1  and task_number=86 $sql group by po_number_id");
    
    $sel_pos = "";
        $tna_task_data[0]['task_start_date'] =date("d-m-Y", time());
        $tna_task_data[0]['task_finish_date'] =date("d-m-Y", time());
        
    foreach ($sqls->result() as $srows) {
        $tna_task_data[$srows->PO_NUMBER_ID]['task_start_date'] = date("d-m-Y", strtotime($srows->TASK_START_DATE));
        $tna_task_data[$srows->PO_NUMBER_ID]['task_finish_date'] = date("d-m-Y", strtotime($srows->TASK_FINISH_DATE));
    }
    
    
     $sqls = $this->db->query("select c.PO_NUMBER,b.STYLE_REF_NO,b.BUYER_NAME,c.ID from  wo_po_details_master b,wo_po_break_down c where    c.job_no_mst=b.job_no  $sql2");
 
    foreach ($sqls->result() as $srows) {
        $wo_po_details[$srows->ID]['job_no'] = $srows->ID;
        $wo_po_details[$srows->ID]['po_number'] = $srows->PO_NUMBER;
        $wo_po_details[$srows->ID]['style_ref'] = $srows->STYLE_REF_NO;
        $wo_po_details[$srows->ID]['buyer_name'] = $srows->BUYER_NAME;
    }
        $wo_po_details[0]['job_no'] = '';
        $wo_po_details[0]['po_number'] = '';
        $wo_po_details[0]['style_ref'] = '';
        $wo_po_details[0]['buyer_name'] = '';
        
    // ini_set('display_errors',0); 
    $i = 0;
    $npo='';
    foreach ($plan_data as $rows) 
    {
         $npo=implode(",",array_unique(explode(",",$rows->PO_BREAK_DOWN_ID)));
         if( $npo < 0) 
         {
            $rows->PO_BREAK_DOWN_ID=0;
            $rows->COLOR_NUMBER_ID=0;
            $rows->SIZE_NUMBER_ID=0;
            $rows->ITEM_NUMBER_ID=0;
         }
        
        $plan_level = $rows->PLAN_LEVEL;
        $color_name = $this->get_field_value_by_attribute("LIB_COLOR", "COLOR_NAME",$rows->COLOR_NUMBER_ID);
        $size_name  = $this->get_field_value_by_attribute("LIB_SIZE", "SIZE_NAME", $rows->SIZE_NUMBER_ID);
        $buyer_name  = $this->get_field_value_by_attribute("LIB_BUYER", "BUYER_NAME", $wo_po_details[$rows->PO_BREAK_DOWN_ID]['buyer_name']);//$rows->BUYER_NAME); //
        
        if($this->db->dbdriver=='mysqli'){
            $proddate_cond = " '" . date("Y-m-d", strtotime($rows->START_DATE)) . "' and '" . date("Y-m-d", strtotime($rows->END_DATE)) . "'";
        }else{
            $proddate_cond = " '" . date("d-M-Y", strtotime($rows->START_DATE)) . "' and '" . date("d-M-Y", strtotime($rows->END_DATE)) . "'";
        }
        $lin=0;
        if(isset($res_all_number[$rows->LINE_ID])){
            $lin=$res_all_number[$rows->LINE_ID];
        }
         
        $production_qnty =   $this->get_production_qnty_by_po_item( $rows->PO_BREAK_DOWN_ID, $lin, $proddate_cond, 3 );
        
        $data_array[$i]["COLOR_NUMBER_ID"]  = $rows->COLOR_NUMBER_ID;
        $data_array[$i]["COLOR_NUMBER"]  = (!empty($color_name))?$color_name:"";
        $data_array[$i]["SIZE_NUMBER_ID"]  = $rows->SIZE_NUMBER_ID;
        $data_array[$i]["SIZE_NUMBER"]  = (!empty($size_name))?$size_name:"";
         $data_array[$i]["JOB_NO"] = $wo_po_details[$rows->PO_BREAK_DOWN_ID]['job_no'];//$rows->JOB_NO;
        $data_array[$i]["PO_COMPANY_ID"] = $rows->PO_COMPANY_ID;
        $data_array[$i]["STYLE_REF_NO"] = $wo_po_details[$rows->PO_BREAK_DOWN_ID]['style_ref'];//$rows->PLAN_ID; //
        $data_array[$i]["BUYER_NAME"] = $buyer_name;
        $data_array[$i]["PO_BREAK_DOWN_ID"] = $rows->PO_BREAK_DOWN_ID;
        $data_array[$i]["PO_NUMBER"] =$wo_po_details[$rows->PO_BREAK_DOWN_ID]['po_number'];//  $rows->PO_NUMBER;//        
        $data_array[$i]["line_id"] = $rows->LINE_ID;        
        $data_array[$i]["plan_id"] = $rows->PLAN_ID;
        $data_array[$i]["SEQ_NO"] = $rows->SEQ_NO;
        $data_array[$i]["PLAN_LEVEL"] = $rows->PLAN_LEVEL;
        $data_array[$i]["start_date"] = date("d-m-Y", strtotime($rows->START_DATE));
        $data_array[$i]["start_hour"] = $rows->START_HOUR;
        $data_array[$i]["end_date"] = date("d-m-Y", strtotime($rows->END_DATE));
        $data_array[$i]["end_hour"] = $rows->END_HOUR;
        $data_array[$i]["duration"] = $rows->DURATION;
        $data_array[$i]["plan_qnty"] = $rows->PLAN_QNTY;
        $data_array[$i]["comp_level"] = $rows->COMP_LEVEL;
        $data_array[$i]["first_day_output"] = $rows->FIRST_DAY_OUTPUT;
        $data_array[$i]["increment_qty"] = $rows->INCREMENT_QTY;
        $data_array[$i]["terget"] = $rows->TERGET;
        $data_array[$i]["company_id"] = $rows->COMPANY_ID;
        $data_array[$i]["company_name"] = $comp[$rows->COMPANY_ID];
        $data_array[$i]["location_id"] = $rows->LOCATION_ID;

        $data_array[$i]["location_name"] = $location_arr[$rows->LOCATION_ID];
        $data_array[$i]["item_number_id"] =  $rows->ITEM_NUMBER_ID;
        if(isset($garments_item[$rows->ITEM_NUMBER_ID])){
            $data_array[$i]["item_name"]  =  $garments_item[$rows->ITEM_NUMBER_ID];
        }
        $data_array[$i]["off_day_plan"] = $rows->OFF_DAY_PLAN;
        $data_array[$i]["order_complexity"] = $rows->ORDER_COMPLEXITY;
        $data_array[$i]["ship_date"] = date("d-m-Y", strtotime($rows->SHIP_DATE));
        $data_array[$i]["USE_LEARNING_CURVE"] = $rows->USE_LEARNING_CURVE;
        $data_array[$i]["CURRENT_PRODUCTION_DATE"] = date("d-m-Y", strtotime($production_qnty['prd_date']));//$rows->CURRENT_PRODUCTION_DATE;
        $data_array[$i]["PRODUCTION_PERCENT"] =$production_qnty['production_data_qnty'];// number_format(((100*$production_qnty['production_data_qnty'])/$rows->PLAN_QNTY),0)."%";//$rows->PRODUCTION_PERCENT;
        
        if( $rows->INCREMENT_QTY>0 )
            $data_array[$i]["TOP_BORDER_COLOR"] = $rows->TOP_BORDER_COLOR;
        else
            $data_array[$i]["TOP_BORDER_COLOR"] ="#FF9900";
        
        if ($production_qnty['production_data_qnty']>0)
            $data_array[$i]["BOTTOM_BORDER_COLOR"] ="Green";
        else
            $data_array[$i]["BOTTOM_BORDER_COLOR"] =  $rows->BOTTOM_BORDER_COLOR;
        
        //$data_array[$i]["LEFT_COLOR"] = $rows->LEFT_COLOR;
        //$data_array[$i]["RIGHT_COLOR"] = $rows->RIGHT_COLOR;
        if( strtotime($rows->SHIP_DATE) > strtotime($rows->END_DATE) ) //Fresh Plan Condirion
        {
            $data_array[$i]["LEFT_COLOR"] = "#73CAD5";//$rows->LEFT_COLOR;
            $data_array[$i]["RIGHT_COLOR"] ="#73CAD5";// $rows->RIGHT_COLOR;
        }
        if(  $production_qnty['production_data_qnty']<1 &&  time()  > strtotime($rows->END_DATE) ) //No Production but date crossed
        {
            $data_array[$i]["LEFT_COLOR"] = "#909553";//$rows->LEFT_COLOR;
            $data_array[$i]["RIGHT_COLOR"] ="#909553";// $rows->RIGHT_COLOR;
        }
        (!empty($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']))? $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']:$tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']= date("d-m-Y", strtotime("1971-01-01"));
        (!empty($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date']))? $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date']:$tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date']= date("d-m-Y", strtotime("1971-01-01"));
        
        if( strtotime($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']) > strtotime($rows->START_DATE) && strtotime($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']) < strtotime($rows->END_DATE)) // Partial plan TNA Date crossed
        {
            //$difference = round ((strtotime($rows->END_DATE) - strtotime($rows->SHIP_DATE))/(3600*24));
            $data_array[$i]["LEFT_COLOR"] = "#73CAD5";//$rows->LEFT_COLOR;
            $data_array[$i]["RIGHT_COLOR"] ="#FF6600";// $rows->RIGHT_COLOR;
        }
        if( strtotime($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']) <= strtotime($rows->START_DATE) ) // Full Plan TNA date crossed
        {
            $data_array[$i]["LEFT_COLOR"] = "#FF6600";//$rows->LEFT_COLOR;
            $data_array[$i]["RIGHT_COLOR"] ="#FF6600";// $rows->RIGHT_COLOR;
        }
        
        if( strtotime($rows->SHIP_DATE) > strtotime($rows->START_DATE) && strtotime($rows->SHIP_DATE) < strtotime($rows->END_DATE)) // Partial Ship Date crossed
        {
            //$difference = round ((strtotime($rows->END_DATE) - strtotime($rows->SHIP_DATE))/(3600*24));
            $data_array[$i]["LEFT_COLOR"] = "#73CAD5";//$rows->LEFT_COLOR;
            $data_array[$i]["RIGHT_COLOR"] ="RED";// $rows->RIGHT_COLOR;
        }
        if( strtotime($rows->SHIP_DATE) <= strtotime($rows->START_DATE) ) // Full Plan ship date crossed
        {
            $data_array[$i]["LEFT_COLOR"] = "RED";//$rows->LEFT_COLOR;
            $data_array[$i]["RIGHT_COLOR"] ="RED";// $rows->RIGHT_COLOR;
        }
        
        if( strtotime($from_date) > strtotime($rows->START_DATE) ) // Crossed date in board 
        {
            $data_array[$i]["LEFT_COLOR"] = "#9C8AE3";//$rows->LEFT_COLOR;
            $data_array[$i]["RIGHT_COLOR"] ="#9C8AE3";// $rows->RIGHT_COLOR;
        }
        
        $data_array[$i]["TASK_START_DATE"] =$tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date'] ;
        $data_array[$i]["TASK_END_DATE"] =$tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date'] ;
        
        $data_array[$i]["MERGE_TYPE"] =   $rows->MERGE_TYPE;
        $data_array[$i]["MERGE_COMMENTS"] =  $rows->MERGE_COMMENTS;
        $data_array[$i]["TABLE_LOCKED"] =  $table_locked;
        
        $data_array[$i]["INSERT_DATE"] =    date("d-m-Y", strtotime($rows->INSERT_DATE));//$rows->MERGE_TYPE;
        
        $data_array[$i]["PRODUCTION_QNTY"]  = $production_qnty['production_data_qnty'];
        $data_array[$i]["PRODUCTION_DAY"] = $rows->LINE_ID;
        $i++;
    }
    
    if(count($plan_data)>0)
    {
        return $data_array;
    }
    else
    {
        return 0;
    }
=======
	foreach ($location_res as $value) 
	{
		$location_arr[$value->ID] = $value->LOCATION_NAME;
	}
	$garment_res = $this->db->query("select ID,ITEM_NAME from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name")->result();

	foreach ($garment_res as $value) 
	{
		$garments_item[$value->ID] = $value->ITEM_NAME;
	}
	$npos=$line_id_arr=array();
	$is_plan=0;
	foreach ($plan_data as $rows) 
	{
		//if($rows->PO_BREAK_DOWN_ID>0)
		$npos[$rows->PO_BREAK_DOWN_ID]=$rows->PO_BREAK_DOWN_ID*1;
		$line_id_arr[] = $rows->LINE_ID*1;
		$is_plan=1;
	}


	$p = 1;
	$sel_pos2 = array_chunk($npos, 999);
	$sql=$sql2=$sql3='';
	foreach ($sel_pos2 as $job_no_process) {
		if ($p == 1)
		{
			$sql .= " and (PO_NUMBER_ID in(" . implode(',', $job_no_process) . ")";
			$sql2 .= " and (c.id in(" . implode(',', $job_no_process) . ")";
			$sql3 .= " and (a.PO_BREAK_DOWN_ID in(" . implode(',', $job_no_process) . ")";
		}
		else
		{
			$sql .= " or PO_NUMBER_ID in(" . implode(',', $job_no_process) . ")";
			$sql2 .= " or (c.id in(" . implode(',', $job_no_process) . ")";
			$sql3 .= " or (a.PO_BREAK_DOWN_ID in(" . implode(',', $job_no_process) . ")";
		}

		$p++;
	}
	$sql .= ")";   
	$sql2 .= ")";   
	$sql3 .= ")";   
	
	if($is_plan==0){ 
		$sql='';
		$sql2=''; 
		$sql3=''; 
	} 

	if(!empty($line_id_arr)){
		$production_sql="select a.PO_BREAK_DOWN_ID,a.ITEM_NUMBER_ID,b.COLOR_SIZE_BREAK_DOWN_ID,b.PRODUCTION_QNTY PRODUCTION_QUANTITY, a.SEWING_LINE SEWING_LINE,a.PRODUCTION_DATE PRODUCTION_DATE from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=5 $sql3 and a.status_active=1 and a.is_deleted=0 order by a.PRODUCTION_DATE ASC";
		$production_result = $this->db->query($production_sql)->result();;
		$production_arr=array();
		foreach ($production_result as $production_row) {
			$production_arr[$production_row->PO_BREAK_DOWN_ID][$production_row->SEWING_LINE]=$production_row->SEWING_LINE;
		}
	}
	
	$sqls = $this->db->query("select min(task_start_date) as TASK_START_DATE,max(task_finish_date) as TASK_FINISH_DATE,PO_NUMBER_ID from tna_process_mst where is_deleted=0 and status_active=1  and task_number=86 $sql group by po_number_id");
	
	$sel_pos = "";
	$tna_task_data[0]['task_start_date'] =date("d-m-Y", time());
	$tna_task_data[0]['task_finish_date'] =date("d-m-Y", time());

	foreach ($sqls->result() as $srows) {
		$tna_task_data[$srows->PO_NUMBER_ID]['task_start_date'] = date("d-m-Y", strtotime($srows->TASK_START_DATE));
		$tna_task_data[$srows->PO_NUMBER_ID]['task_finish_date'] = date("d-m-Y", strtotime($srows->TASK_FINISH_DATE));
	}
	
	
	$sqls = $this->db->query("select c.PO_NUMBER,b.STYLE_REF_NO,b.BUYER_NAME,c.ID from  wo_po_details_master b,wo_po_break_down c where    c.job_no_mst=b.job_no  $sql2");

	foreach ($sqls->result() as $srows) {
		$wo_po_details[$srows->ID]['job_no'] = $srows->ID;
		$wo_po_details[$srows->ID]['po_number'] = $srows->PO_NUMBER;
		$wo_po_details[$srows->ID]['style_ref'] = $srows->STYLE_REF_NO;
		$wo_po_details[$srows->ID]['buyer_name'] = $srows->BUYER_NAME;
	}
	$wo_po_details[0]['job_no'] = '';
	$wo_po_details[0]['po_number'] = '';
	$wo_po_details[0]['style_ref'] = '';
	$wo_po_details[0]['buyer_name'] = '';

	// ini_set('display_errors',0); 
	$i = 0;
	$npo='';
	$data_array=array();
	foreach ($plan_data as $rows) 
	{
		if($auto_balancing==1){
			if(!empty($production_arr) && $production_arr[$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID]!=""){
				$npo=implode(",",array_unique(explode(",",$rows->PO_BREAK_DOWN_ID)));
				if( $npo < 0) 
				{
					$rows->PO_BREAK_DOWN_ID=0;
					$rows->COLOR_NUMBER_ID=0;
					$rows->SIZE_NUMBER_ID=0;
					$rows->ITEM_NUMBER_ID=0;
				}

				$plan_level = $rows->PLAN_LEVEL;
				$color_name = $this->get_field_value_by_attribute("LIB_COLOR", "COLOR_NAME",$rows->COLOR_NUMBER_ID);
				$size_name  = $this->get_field_value_by_attribute("LIB_SIZE", "SIZE_NAME", $rows->SIZE_NUMBER_ID);
				$buyer_name  = $this->get_field_value_by_attribute("LIB_BUYER", "BUYER_NAME", $wo_po_details[$rows->PO_BREAK_DOWN_ID]['buyer_name']);

				if($this->db->dbdriver=='mysqli'){
					$proddate_cond = " '" . date("Y-m-d", strtotime($rows->START_DATE)) . "' and '" . date("Y-m-d", strtotime($rows->END_DATE)) . "'";
				}else{
					$proddate_cond = " '" . date("d-M-Y", strtotime($rows->START_DATE)) . "' and '" . date("d-M-Y", strtotime($rows->END_DATE)) . "'";
				}
				$lin=0;
				if(isset($res_all_number[$rows->LINE_ID])){
					$lin=$res_all_number[$rows->LINE_ID];
				}

				$production_qnty =   $this->get_production_qnty_by_po_item( $rows->PO_BREAK_DOWN_ID, $lin, $proddate_cond, 3 );

				$data_array[$i]["COLOR_NUMBER_ID"]  = $rows->COLOR_NUMBER_ID;
				$data_array[$i]["COLOR_NUMBER"]  = (!empty($color_name))?$color_name:"";
				$data_array[$i]["SIZE_NUMBER_ID"]  = $rows->SIZE_NUMBER_ID;
				$data_array[$i]["SIZE_NUMBER"]  = (!empty($size_name))?$size_name:"";
	         $data_array[$i]["JOB_NO"] = $wo_po_details[$rows->PO_BREAK_DOWN_ID]['job_no'];//$rows->JOB_NO;
	         $data_array[$i]["PO_COMPANY_ID"] = $rows->PO_COMPANY_ID;
	        $data_array[$i]["STYLE_REF_NO"] = $wo_po_details[$rows->PO_BREAK_DOWN_ID]['style_ref'];//$rows->PLAN_ID; //
	        $data_array[$i]["BUYER_NAME"] = $buyer_name;
	        $data_array[$i]["PO_BREAK_DOWN_ID"] = $rows->PO_BREAK_DOWN_ID;
	        $data_array[$i]["PO_NUMBER"] =$wo_po_details[$rows->PO_BREAK_DOWN_ID]['po_number'];//  $rows->PO_NUMBER;//        
	        $data_array[$i]["line_id"] = $rows->LINE_ID;        
	        $data_array[$i]["plan_id"] = $rows->PLAN_ID;
	        $data_array[$i]["SEQ_NO"] = $rows->SEQ_NO;
	        $data_array[$i]["PLAN_LEVEL"] = $rows->PLAN_LEVEL;
	        $data_array[$i]["start_date"] = date("d-m-Y", strtotime($rows->START_DATE));
	        $data_array[$i]["start_hour"] = $rows->START_HOUR;
	        $data_array[$i]["end_date"] = date("d-m-Y", strtotime($rows->END_DATE));
	        $data_array[$i]["end_hour"] = $rows->END_HOUR;
	        $data_array[$i]["duration"] = $rows->DURATION;
	        $data_array[$i]["plan_qnty"] = $rows->PLAN_QNTY;
	        $data_array[$i]["comp_level"] = $rows->COMP_LEVEL;
	        $data_array[$i]["first_day_output"] = $rows->FIRST_DAY_OUTPUT;
	        $data_array[$i]["increment_qty"] = $rows->INCREMENT_QTY;
	        $data_array[$i]["terget"] = $rows->TERGET;
	        $data_array[$i]["company_id"] = $rows->COMPANY_ID;
	        $data_array[$i]["company_name"] = $comp[$rows->COMPANY_ID];
	        $data_array[$i]["location_id"] = $rows->LOCATION_ID;

	        $data_array[$i]["location_name"] = $location_arr[$rows->LOCATION_ID];
	        $data_array[$i]["item_number_id"] =  $rows->ITEM_NUMBER_ID;
	        if(isset($garments_item[$rows->ITEM_NUMBER_ID])){
	        	$data_array[$i]["item_name"]  =  $garments_item[$rows->ITEM_NUMBER_ID];
	        }
	        $data_array[$i]["off_day_plan"] = $rows->OFF_DAY_PLAN;
	        $data_array[$i]["order_complexity"] = $rows->ORDER_COMPLEXITY;
	        $data_array[$i]["ship_date"] = date("d-m-Y", strtotime($rows->SHIP_DATE));
	        $data_array[$i]["USE_LEARNING_CURVE"] = $rows->USE_LEARNING_CURVE;
			$data_array[$i]["CURRENT_PRODUCTION_DATE"] = date("d-m-Y", strtotime($production_qnty['prd_date']));//$rows->CURRENT_PRODUCTION_DATE;
			$data_array[$i]["PRODUCTION_PERCENT"] =$production_qnty['production_data_qnty'];// number_format(((100*$production_qnty['production_data_qnty'])/$rows->PLAN_QNTY),0)."%";//$rows->PRODUCTION_PERCENT;
			
			if( $rows->INCREMENT_QTY>0 )
				$data_array[$i]["TOP_BORDER_COLOR"] = $rows->TOP_BORDER_COLOR;
			else
				$data_array[$i]["TOP_BORDER_COLOR"] ="#FF9900";
			
			if ($production_qnty['production_data_qnty']>0)
				$data_array[$i]["BOTTOM_BORDER_COLOR"] ="Green";
			else
				$data_array[$i]["BOTTOM_BORDER_COLOR"] =  $rows->BOTTOM_BORDER_COLOR;
			
			//$data_array[$i]["LEFT_COLOR"] = $rows->LEFT_COLOR;
			//$data_array[$i]["RIGHT_COLOR"] = $rows->RIGHT_COLOR;
			if( strtotime($rows->SHIP_DATE) > strtotime($rows->END_DATE) ) //Fresh Plan Condirion
			{
				$data_array[$i]["LEFT_COLOR"] = "#73CAD5";//$rows->LEFT_COLOR;
				$data_array[$i]["RIGHT_COLOR"] ="#73CAD5";// $rows->RIGHT_COLOR;
			}
			if(  $production_qnty['production_data_qnty']<1 &&  time()  > strtotime($rows->END_DATE) ) //No Production but date crossed
			{
				$data_array[$i]["LEFT_COLOR"] = "#909553";//$rows->LEFT_COLOR;
				$data_array[$i]["RIGHT_COLOR"] ="#909553";// $rows->RIGHT_COLOR;
			}
			(!empty($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']))? $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']:$tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']= date("d-m-Y", strtotime("1971-01-01"));
			(!empty($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date']))? $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date']:$tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date']= date("d-m-Y", strtotime("1971-01-01"));
			
			if( strtotime($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']) > strtotime($rows->START_DATE) && strtotime($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']) < strtotime($rows->END_DATE)) // Partial plan TNA Date crossed
			{
				//$difference = round ((strtotime($rows->END_DATE) - strtotime($rows->SHIP_DATE))/(3600*24));
				$data_array[$i]["LEFT_COLOR"] = "#73CAD5";//$rows->LEFT_COLOR;
				$data_array[$i]["RIGHT_COLOR"] ="#FF6600";// $rows->RIGHT_COLOR;
			}
			if( strtotime($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']) <= strtotime($rows->START_DATE) ) // Full Plan TNA date crossed
			{
				$data_array[$i]["LEFT_COLOR"] = "#FF6600";//$rows->LEFT_COLOR;
				$data_array[$i]["RIGHT_COLOR"] ="#FF6600";// $rows->RIGHT_COLOR;
			}
			
			if( strtotime($rows->SHIP_DATE) > strtotime($rows->START_DATE) && strtotime($rows->SHIP_DATE) < strtotime($rows->END_DATE)) // Partial Ship Date crossed
			{
				//$difference = round ((strtotime($rows->END_DATE) - strtotime($rows->SHIP_DATE))/(3600*24));
				$data_array[$i]["LEFT_COLOR"] = "#73CAD5";//$rows->LEFT_COLOR;
				$data_array[$i]["RIGHT_COLOR"] ="RED";// $rows->RIGHT_COLOR;
			}
			if( strtotime($rows->SHIP_DATE) <= strtotime($rows->START_DATE) ) // Full Plan ship date crossed
			{
				$data_array[$i]["LEFT_COLOR"] = "RED";//$rows->LEFT_COLOR;
				$data_array[$i]["RIGHT_COLOR"] ="RED";// $rows->RIGHT_COLOR;
			}
			
			if( strtotime($from_date) > strtotime($rows->START_DATE) ) // Crossed date in board 
			{
				$data_array[$i]["LEFT_COLOR"] = "#9C8AE3";//$rows->LEFT_COLOR;
				$data_array[$i]["RIGHT_COLOR"] ="#9C8AE3";// $rows->RIGHT_COLOR;
			}
			
			$data_array[$i]["TASK_START_DATE"] =$tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date'] ;
			$data_array[$i]["TASK_END_DATE"] =$tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date'] ;
			
			$data_array[$i]["MERGE_TYPE"] =   $rows->MERGE_TYPE;
			$data_array[$i]["MERGE_COMMENTS"] =  $rows->MERGE_COMMENTS;
			$data_array[$i]["TABLE_LOCKED"] =  $table_locked;
			
			$data_array[$i]["INSERT_DATE"] =    date("d-m-Y", strtotime($rows->INSERT_DATE));//$rows->MERGE_TYPE;
			
			$data_array[$i]["PRODUCTION_QNTY"]  = $production_qnty['production_data_qnty'];
			$data_array[$i]["PRODUCTION_DAY"] = $rows->LINE_ID;
			$i++;

		}
	}else{
		$npo=implode(",",array_unique(explode(",",$rows->PO_BREAK_DOWN_ID)));
        if( $npo < 0) 
        {
         $rows->PO_BREAK_DOWN_ID=0;
         $rows->COLOR_NUMBER_ID=0;
         $rows->SIZE_NUMBER_ID=0;
         $rows->ITEM_NUMBER_ID=0;
     }

     $plan_level = $rows->PLAN_LEVEL;
     $color_name = $this->get_field_value_by_attribute("LIB_COLOR", "COLOR_NAME",$rows->COLOR_NUMBER_ID);
     $size_name  = $this->get_field_value_by_attribute("LIB_SIZE", "SIZE_NAME", $rows->SIZE_NUMBER_ID);
     $buyer_name  = $this->get_field_value_by_attribute("LIB_BUYER", "BUYER_NAME", $wo_po_details[$rows->PO_BREAK_DOWN_ID]['buyer_name']);

     if($this->db->dbdriver=='mysqli'){
         $proddate_cond = " '" . date("Y-m-d", strtotime($rows->START_DATE)) . "' and '" . date("Y-m-d", strtotime($rows->END_DATE)) . "'";
     }else{
         $proddate_cond = " '" . date("d-M-Y", strtotime($rows->START_DATE)) . "' and '" . date("d-M-Y", strtotime($rows->END_DATE)) . "'";
     }
     $lin=0;
     if(isset($res_all_number[$rows->LINE_ID])){
         $lin=$res_all_number[$rows->LINE_ID];
     }

     $production_qnty =   $this->get_production_qnty_by_po_item( $rows->PO_BREAK_DOWN_ID, $lin, $proddate_cond, 3 );

     $data_array[$i]["COLOR_NUMBER_ID"]  = $rows->COLOR_NUMBER_ID;
     $data_array[$i]["COLOR_NUMBER"]  = (!empty($color_name))?$color_name:"";
     $data_array[$i]["SIZE_NUMBER_ID"]  = $rows->SIZE_NUMBER_ID;
     $data_array[$i]["SIZE_NUMBER"]  = (!empty($size_name))?$size_name:"";
	         $data_array[$i]["JOB_NO"] = $wo_po_details[$rows->PO_BREAK_DOWN_ID]['job_no'];//$rows->JOB_NO;
	         $data_array[$i]["PO_COMPANY_ID"] = $rows->PO_COMPANY_ID;
	        $data_array[$i]["STYLE_REF_NO"] = $wo_po_details[$rows->PO_BREAK_DOWN_ID]['style_ref'];//$rows->PLAN_ID; //
	        $data_array[$i]["BUYER_NAME"] = $buyer_name;
	        $data_array[$i]["PO_BREAK_DOWN_ID"] = $rows->PO_BREAK_DOWN_ID;
	        $data_array[$i]["PO_NUMBER"] =$wo_po_details[$rows->PO_BREAK_DOWN_ID]['po_number'];//  $rows->PO_NUMBER;//        
	        $data_array[$i]["line_id"] = $rows->LINE_ID;        
	        $data_array[$i]["plan_id"] = $rows->PLAN_ID;
	        $data_array[$i]["SEQ_NO"] = $rows->SEQ_NO;
	        $data_array[$i]["PLAN_LEVEL"] = $rows->PLAN_LEVEL;
	        $data_array[$i]["start_date"] = date("d-m-Y", strtotime($rows->START_DATE));
	        $data_array[$i]["start_hour"] = $rows->START_HOUR;
	        $data_array[$i]["end_date"] = date("d-m-Y", strtotime($rows->END_DATE));
	        $data_array[$i]["end_hour"] = $rows->END_HOUR;
	        $data_array[$i]["duration"] = $rows->DURATION;
	        $data_array[$i]["plan_qnty"] = $rows->PLAN_QNTY;
	        $data_array[$i]["comp_level"] = $rows->COMP_LEVEL;
	        $data_array[$i]["first_day_output"] = $rows->FIRST_DAY_OUTPUT;
	        $data_array[$i]["increment_qty"] = $rows->INCREMENT_QTY;
	        $data_array[$i]["terget"] = $rows->TERGET;
	        $data_array[$i]["company_id"] = $rows->COMPANY_ID;
	        $data_array[$i]["company_name"] = $comp[$rows->COMPANY_ID];
	        $data_array[$i]["location_id"] = $rows->LOCATION_ID;

	        $data_array[$i]["location_name"] = $location_arr[$rows->LOCATION_ID];
	        $data_array[$i]["item_number_id"] =  $rows->ITEM_NUMBER_ID;
	        if(isset($garments_item[$rows->ITEM_NUMBER_ID])){
	        	$data_array[$i]["item_name"]  =  $garments_item[$rows->ITEM_NUMBER_ID];
	        }
	        $data_array[$i]["off_day_plan"] = $rows->OFF_DAY_PLAN;
	        $data_array[$i]["order_complexity"] = $rows->ORDER_COMPLEXITY;
	        $data_array[$i]["ship_date"] = date("d-m-Y", strtotime($rows->SHIP_DATE));
	        $data_array[$i]["USE_LEARNING_CURVE"] = $rows->USE_LEARNING_CURVE;
			$data_array[$i]["CURRENT_PRODUCTION_DATE"] = date("d-m-Y", strtotime($production_qnty['prd_date']));//$rows->CURRENT_PRODUCTION_DATE;
			$data_array[$i]["PRODUCTION_PERCENT"] =$production_qnty['production_data_qnty'];// number_format(((100*$production_qnty['production_data_qnty'])/$rows->PLAN_QNTY),0)."%";//$rows->PRODUCTION_PERCENT;
			
			if( $rows->INCREMENT_QTY>0 )
				$data_array[$i]["TOP_BORDER_COLOR"] = $rows->TOP_BORDER_COLOR;
			else
				$data_array[$i]["TOP_BORDER_COLOR"] ="#FF9900";
			
			if ($production_qnty['production_data_qnty']>0)
				$data_array[$i]["BOTTOM_BORDER_COLOR"] ="Green";
			else
				$data_array[$i]["BOTTOM_BORDER_COLOR"] =  $rows->BOTTOM_BORDER_COLOR;
			
			//$data_array[$i]["LEFT_COLOR"] = $rows->LEFT_COLOR;
			//$data_array[$i]["RIGHT_COLOR"] = $rows->RIGHT_COLOR;
			if( strtotime($rows->SHIP_DATE) > strtotime($rows->END_DATE) ) //Fresh Plan Condirion
			{
				$data_array[$i]["LEFT_COLOR"] = "#73CAD5";//$rows->LEFT_COLOR;
				$data_array[$i]["RIGHT_COLOR"] ="#73CAD5";// $rows->RIGHT_COLOR;
			}
			if(  $production_qnty['production_data_qnty']<1 &&  time()  > strtotime($rows->END_DATE) ) //No Production but date crossed
			{
				$data_array[$i]["LEFT_COLOR"] = "#909553";//$rows->LEFT_COLOR;
				$data_array[$i]["RIGHT_COLOR"] ="#909553";// $rows->RIGHT_COLOR;
			}
			(!empty($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']))? $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']:$tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']= date("d-m-Y", strtotime("1971-01-01"));
			(!empty($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date']))? $tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date']:$tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date']= date("d-m-Y", strtotime("1971-01-01"));
			
			if( strtotime($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']) > strtotime($rows->START_DATE) && strtotime($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']) < strtotime($rows->END_DATE)) // Partial plan TNA Date crossed
			{
				//$difference = round ((strtotime($rows->END_DATE) - strtotime($rows->SHIP_DATE))/(3600*24));
				$data_array[$i]["LEFT_COLOR"] = "#73CAD5";//$rows->LEFT_COLOR;
				$data_array[$i]["RIGHT_COLOR"] ="#FF6600";// $rows->RIGHT_COLOR;
			}
			if( strtotime($tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date']) <= strtotime($rows->START_DATE) ) // Full Plan TNA date crossed
			{
				$data_array[$i]["LEFT_COLOR"] = "#FF6600";//$rows->LEFT_COLOR;
				$data_array[$i]["RIGHT_COLOR"] ="#FF6600";// $rows->RIGHT_COLOR;
			}
			
			if( strtotime($rows->SHIP_DATE) > strtotime($rows->START_DATE) && strtotime($rows->SHIP_DATE) < strtotime($rows->END_DATE)) // Partial Ship Date crossed
			{
				//$difference = round ((strtotime($rows->END_DATE) - strtotime($rows->SHIP_DATE))/(3600*24));
				$data_array[$i]["LEFT_COLOR"] = "#73CAD5";//$rows->LEFT_COLOR;
				$data_array[$i]["RIGHT_COLOR"] ="RED";// $rows->RIGHT_COLOR;
			}
			if( strtotime($rows->SHIP_DATE) <= strtotime($rows->START_DATE) ) // Full Plan ship date crossed
			{
				$data_array[$i]["LEFT_COLOR"] = "RED";//$rows->LEFT_COLOR;
				$data_array[$i]["RIGHT_COLOR"] ="RED";// $rows->RIGHT_COLOR;
			}
			
			if( strtotime($from_date) > strtotime($rows->START_DATE) ) // Crossed date in board 
			{
				$data_array[$i]["LEFT_COLOR"] = "#9C8AE3";//$rows->LEFT_COLOR;
				$data_array[$i]["RIGHT_COLOR"] ="#9C8AE3";// $rows->RIGHT_COLOR;
			}
			
			$data_array[$i]["TASK_START_DATE"] =$tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_start_date'] ;
			$data_array[$i]["TASK_END_DATE"] =$tna_task_data[$rows->PO_BREAK_DOWN_ID]['task_finish_date'] ;
			
			$data_array[$i]["MERGE_TYPE"] =   $rows->MERGE_TYPE;
			$data_array[$i]["MERGE_COMMENTS"] =  $rows->MERGE_COMMENTS;
			$data_array[$i]["TABLE_LOCKED"] =  $table_locked;
			
			$data_array[$i]["INSERT_DATE"] =    date("d-m-Y", strtotime($rows->INSERT_DATE));//$rows->MERGE_TYPE;
			
			$data_array[$i]["PRODUCTION_QNTY"]  = $production_qnty['production_data_qnty'];
			$data_array[$i]["PRODUCTION_DAY"] = $rows->LINE_ID;
			$i++;
     }
 }

 if(count($plan_data)>0)
 {
     return $data_array;
 }
 else
 {
     return 0;
 }
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb

}

function get_daywise_plan_data_info($company_id,$po_id,$txt_date_from="", $txt_date_to="")
{
	$company_cond = " and a.company_id='$company_id'";
	$po_id_cond = " and a.po_break_down_id='$po_id'";
	if ($txt_date_from != "" && $txt_date_to != "")
	{
		$plan_date = "and b.plan_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
	}
	else
	{ 
		$plan_date = "";
	}
	$daywise_sql="select a.PO_BREAK_DOWN_ID,a.LINE_ID,a.START_DATE,a.END_DATE,a.PLAN_QNTY,a.FIRST_DAY_OUTPUT,a.INCREMENT_QTY,a.TERGET ,a.COMPANY_ID, a.LOCATION_ID,a.ITEM_NUMBER_ID ,a.OFF_DAY_PLAN,a.ORDER_COMPLEXITY,b.PLAN_DATE,b.PLAN_QNTY,b.PLAN_ID  from ppl_sewing_plan_board a,ppl_sewing_plan_board_dtls b where a.plan_id=b.plan_id and a.is_deleted=0 and a.status_active=1 $po_id_cond $company_cond $plan_date";
        //echo $daywise_sql;
	$daywise_plan = $this->db->query($daywise_sql)->result();

	$com_res = $this->db->query("select ID,COMPANY_NAME from lib_company where status_active =1 and is_deleted=0  order by company_name")->result();

<<<<<<< HEAD
    foreach ($com_res as $value) 
    {
        $comp[$value->ID] = $value->COMPANY_NAME;
        /*if($this->db->dbdriver=='mysqli'){
            $comp[$value->id] = $value->company_name;

        }else{
            $comp[$value->ID] = $value->COMPANY_NAME;
        }*/
    }
    $location_res = $this->db->query("select ID,LOCATION_NAME from lib_location  where status_active =1 and is_deleted=0  order by location_name")->result();

    foreach ($location_res as $value) 
    {
        $location_arr[$value->ID] = $value->LOCATION_NAME;
        /*if($this->db->dbdriver=='mysqli'){
        $location_arr[$value->id] = $value->location_name;
        }else{
        $location_arr[$value->ID] = $value->LOCATION_NAME;
        }*/
    }
=======
	foreach ($com_res as $value) 
	{
		$comp[$value->ID] = $value->COMPANY_NAME;
		/*if($this->db->dbdriver=='mysqli'){
			$comp[$value->id] = $value->company_name;

		}else{
			$comp[$value->ID] = $value->COMPANY_NAME;
		}*/
	}
	$location_res = $this->db->query("select ID,LOCATION_NAME from lib_location  where status_active =1 and is_deleted=0  order by location_name")->result();

	foreach ($location_res as $value) 
	{
		$location_arr[$value->ID] = $value->LOCATION_NAME;
		/*if($this->db->dbdriver=='mysqli'){
        $location_arr[$value->id] = $value->location_name;
		}else{
		$location_arr[$value->ID] = $value->LOCATION_NAME;
	}*/
}
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb


$garment_res = $this->db->query("select ID,ITEM_NAME from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name")->result();

<<<<<<< HEAD
    foreach ($garment_res as $value) 
    {
        $garments_item[$value->ID] = $value->ITEM_NAME;
        /*if($this->db->dbdriver=='mysqli'){
        $garments_item[$value->id] = $value->item_name;
        }else{
        $garments_item[$value->ID] = $value->ITEM_NAME;
        }*/
    }


    $i = 0;
    foreach ($daywise_plan as $rows) 
    {
        $data_array[$i]["plan_date"] = date("d-m-Y", strtotime($rows->PLAN_DATE)) ;
        $data_array[$i]["plan_id"] = $rows->PLAN_ID;
        $data_array[$i]["plan_qnty"] = $rows->PLAN_QNTY;
        $data_array[$i]["po_break_down_id"] = $rows->PO_BREAK_DOWN_ID;
        $data_array[$i]["line_id"] = $rows->LINE_ID;
        $data_array[$i]["start_date"] =date("d-m-Y", strtotime($rows->START_DATE)) ;
        $data_array[$i]["end_date"] =date("d-m-Y", strtotime($rows->END_DATE)) ;
       // $data_array[$i]["production_quantity"] = $rows->PLAN_QNTY;
        $data_array[$i]["first_day_output"] = $rows->FIRST_DAY_OUTPUT;
        $data_array[$i]["increment_qty"] = $rows->INCREMENT_QTY;
        $data_array[$i]["terget"] = $rows->TERGET;
        $data_array[$i]["company_id"] = $rows->COMPANY_ID;
        $data_array[$i]["company_name"] = $comp[$rows->COMPANY_ID];
        $data_array[$i]["location_id"] = $rows->LOCATION_ID;
        $data_array[$i]["location_name"] = $location_arr[$rows->LOCATION_ID];
        $data_array[$i]["item_number_id"] = $garments_item[$rows->ITEM_NUMBER_ID];
        $data_array[$i]["off_day_plan"] = $rows->OFF_DAY_PLAN;
        $data_array[$i]["order_complexity"] = $rows->ORDER_COMPLEXITY;
        /*if($this->db->dbdriver=='mysqli'){
=======
foreach ($garment_res as $value) 
{
	$garments_item[$value->ID] = $value->ITEM_NAME;
		/*if($this->db->dbdriver=='mysqli'){
        $garments_item[$value->id] = $value->item_name;
		}else{
		$garments_item[$value->ID] = $value->ITEM_NAME;
	}*/
}


$i = 0;
foreach ($daywise_plan as $rows) 
{
	$data_array[$i]["plan_date"] = date("d-m-Y", strtotime($rows->PLAN_DATE)) ;
	$data_array[$i]["plan_id"] = $rows->PLAN_ID;
	$data_array[$i]["plan_qnty"] = $rows->PLAN_QNTY;
	$data_array[$i]["po_break_down_id"] = $rows->PO_BREAK_DOWN_ID;
	$data_array[$i]["line_id"] = $rows->LINE_ID;
	$data_array[$i]["start_date"] =date("d-m-Y", strtotime($rows->START_DATE)) ;
	$data_array[$i]["end_date"] =date("d-m-Y", strtotime($rows->END_DATE)) ;
       // $data_array[$i]["production_quantity"] = $rows->PLAN_QNTY;
	$data_array[$i]["first_day_output"] = $rows->FIRST_DAY_OUTPUT;
	$data_array[$i]["increment_qty"] = $rows->INCREMENT_QTY;
	$data_array[$i]["terget"] = $rows->TERGET;
	$data_array[$i]["company_id"] = $rows->COMPANY_ID;
	$data_array[$i]["company_name"] = $comp[$rows->COMPANY_ID];
	$data_array[$i]["location_id"] = $rows->LOCATION_ID;
	$data_array[$i]["location_name"] = $location_arr[$rows->LOCATION_ID];
	$data_array[$i]["item_number_id"] = $garments_item[$rows->ITEM_NUMBER_ID];
	$data_array[$i]["off_day_plan"] = $rows->OFF_DAY_PLAN;
	$data_array[$i]["order_complexity"] = $rows->ORDER_COMPLEXITY;
		/*if($this->db->dbdriver=='mysqli'){
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb
        $data_array[$i]["plan_date"] = date("d-m-y", strtotime($rows->plan_date)) ;
        $data_array[$i]["plan_id"] = $rows->plan_id;
        $data_array[$i]["plan_qnty"] = $rows->plan_qnty;
        $data_array[$i]["po_break_down_id"] = $rows->po_break_down_id;
        $data_array[$i]["line_id"] = $rows->line_id;
        $data_array[$i]["start_date"] =date("d-m-y", strtotime($rows->start_date)) ;
        $data_array[$i]["end_date"] =date("d-m-y", strtotime($rows->end_date)) ;
        $data_array[$i]["plan_qnty"] = $rows->plan_qnty;
        $data_array[$i]["first_day_output"] = $rows->first_day_output;
        $data_array[$i]["increment_qty"] = $rows->increment_qty;
        $data_array[$i]["terget"] = $rows->terget;
        $data_array[$i]["company_id"] = $rows->company_id;
        $data_array[$i]["company_name"] = $comp[$rows->company_id];
        $data_array[$i]["location_id"] = $rows->location_id;
        $data_array[$i]["location_name"] = $location_arr[$rows->location_id];
        $data_array[$i]["item_number_id"] = $garments_item[$rows->item_number_id];
        $data_array[$i]["off_day_plan"] = $rows->off_day_plan;
        $data_array[$i]["order_complexity"] = $rows->order_complexity;
        }else{
        $data_array[$i]["plan_date"] = date("d-m-Y", strtotime($rows->PLAN_DATE)) ;
        $data_array[$i]["plan_id"] = $rows->PLAN_ID;
        $data_array[$i]["plan_qnty"] = $rows->PLAN_QNTY;
        $data_array[$i]["po_break_down_id"] = $rows->PO_BREAK_DOWN_ID;
        $data_array[$i]["line_id"] = $rows->LINE_ID;
        $data_array[$i]["start_date"] =date("d-m-Y", strtotime($rows->START_DATE)) ;
        $data_array[$i]["end_date"] =date("d-m-Y", strtotime($rows->END_DATE)) ;
        $data_array[$i]["plan_qnty"] = $rows->PLAN_QNTY;
        $data_array[$i]["first_day_output"] = $rows->FIRST_DAY_OUTPUT;
        $data_array[$i]["increment_qty"] = $rows->INCREMENT_QTY;
        $data_array[$i]["terget"] = $rows->TERGET;
        $data_array[$i]["company_id"] = $rows->COMPANY_ID;
        $data_array[$i]["company_name"] = $comp[$rows->COMPANY_ID];
        $data_array[$i]["location_id"] = $rows->LOCATION_ID;
        $data_array[$i]["location_name"] = $location_arr[$rows->LOCATION_ID];
        $data_array[$i]["item_number_id"] = $garments_item[$rows->ITEM_NUMBER_ID];
        $data_array[$i]["off_day_plan"] = $rows->OFF_DAY_PLAN;
        $data_array[$i]["order_complexity"] = $rows->ORDER_COMPLEXITY;
<<<<<<< HEAD
        }*/
=======
    }*/
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb

    $i++;
}

if(count($daywise_plan)>0)
{
	return $data_array;
}
else
{
	return 0;
}


}

function get_line_list_info($company_id,$location_id="0",$floor_id="0")
{
	$resource_allocation_type_sql=$this->db->query("select AUTO_UPDATE from variable_settings_production where company_name='$company_id' and variable_list=23 ")->row();
	$resource_allocation_type=1;
	$company_cond = " and company_id='$company_id'";
	if(($location_id*1)!="0"){$location_cond=" and location_name='$location_id'";} else{ $location_cond="";}

	if(($floor_id*1)!="0") $floor_cond = " and floor_name='$floor_id'"; else {$floor_cond = "";}
	if(($floor_id*1) !="0")$floor_cond_sewing = " and floor_name='$floor_id'"; else {$floor_cond_sewing = "";}
	$line_res=$this->db->query("select ID LINE_ID,LINE_NAME LINE_NAME,SEWING_LINE_SERIAL from lib_sewing_line where company_name='$company_id' $location_cond  $floor_cond_sewing")->result();
        /*$line_names=array(); 
        foreach ($line_res as $value) 
        {
            $line_names[$value->ID] = $value->LINE_NAME;
        }

        $new_line_res=$this->db->query("select ID,LINE_NUMBER,LOCATION_ID,FLOOR_ID from prod_resource_mst where company_id='$company_id' $location_cond $floor_cond and is_deleted=0 order by ID")->result();
        //return $new_line_res;
        $new_line_resource=array();
        $i=0;
        foreach($new_line_res as $key=>$line) 
        {
            $nline=$line->LINE_NUMBER;
            $lines=explode(",",trim($nline));
                $ln="";
            
                foreach($lines as $lineid) 
                {
                    if(isset($line_names[trim($lineid)])){
                        $ln = ($ln=="")?$line_names[trim($lineid)]:",".$line_names[trim($lineid)];                        
                    }
                }
                $new_line_resource[$i]['LINE_ID']=$line->ID;
                $new_line_resource[$i]['LINE_NAME']=trim($ln,",");
                //$new_line_resource[$line->ID]=trim($ln,",");
             
            $i++;
        }  */
         /*if(count($new_line_resource)>0)
         {
           if( $resource_allocation_type==1 )
                return $line_res;
            else
              $line_res;
         }
         else
         {
            return 0;
        }*/
        return $line_res;

    }

    function get_week_list_info($company_id,$txt_date_from="", $txt_date_to="")
    {
<<<<<<< HEAD
        $company_cond = " and comapny_id='$company_id'";
        
        function add_date($orgDate,$days)
        {
            $cd = strtotime($orgDate);
            $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$days,date('Y',$cd)));
            return $retDAY;
        }

        if($txt_date_from=='') $txt_date_from=date('d-m-Y',time());
        if($txt_date_to=='') $txt_date_to=add_date($txt_date_from,120);
        
        if ($txt_date_from != "" && $txt_date_to != "")
        {
            if($this->db->dbdriver=='mysqli'){
            $date_calc = "and date_calc between '" . date("Y-m-d", strtotime($txt_date_from)) . "' and '" . date("Y-m-d", strtotime($txt_date_to)) . "'";
            }else{
                  $date_calc = "and date_calc between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
            }
        }
        else
        { 
            $date_calc = "";
        }
        if($this->db->dbdriver=='mysqli'){
        $sql="select a.MST_ID,a.MONTH_ID,DATE_FORMAT(a.date_calc,'%d-%m-%Y') date_calc,case when a.day_status = 2 then 'Closed' else 'Open' end as DAY_STATUS,COMAPNY_ID,CAPACITY_SOURCE,LOCATION_ID from lib_capacity_calc_dtls a, lib_capacity_calc_mst b where b.id=a.mst_id $company_cond $date_calc and day_status=2";
        }else{
        $sql="select a.MST_ID,a.MONTH_ID,to_char(a.date_calc,'DD-MM-YYYY') DATE_CALC,case when a.day_status = 2 then 'Closed' else 'Open' end as DAY_STATUS,COMAPNY_ID,CAPACITY_SOURCE,LOCATION_ID from lib_capacity_calc_dtls a, lib_capacity_calc_mst b where b.id=a.mst_id $company_cond $date_calc and day_status=2";
        }
        $sql_data= $this->db->query($sql)->result();
=======
    	$company_cond = " and comapny_id='$company_id'";

    	function add_date($orgDate,$days)
    	{
    		$cd = strtotime($orgDate);
    		$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$days,date('Y',$cd)));
    		return $retDAY;
    	}

    	if($txt_date_from=='') $txt_date_from=date('d-m-Y',time());
    	if($txt_date_to=='') $txt_date_to=add_date($txt_date_from,120);

    	if ($txt_date_from != "" && $txt_date_to != "")
    	{
    		if($this->db->dbdriver=='mysqli'){
    			$date_calc = "and date_calc between '" . date("Y-m-d", strtotime($txt_date_from)) . "' and '" . date("Y-m-d", strtotime($txt_date_to)) . "'";
    		}else{
    			$date_calc = "and date_calc between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
    		}
    	}
    	else
    	{ 
    		$date_calc = "";
    	}
    	if($this->db->dbdriver=='mysqli'){
    		$sql="select a.MST_ID,a.MONTH_ID,DATE_FORMAT(a.date_calc,'%d-%m-%Y') date_calc,case when a.day_status = 2 then 'Closed' else 'Open' end as DAY_STATUS,COMAPNY_ID,CAPACITY_SOURCE,LOCATION_ID from lib_capacity_calc_dtls a, lib_capacity_calc_mst b where b.id=a.mst_id $company_cond $date_calc and day_status=2";
    	}else{
    		$sql="select a.MST_ID,a.MONTH_ID,to_char(a.date_calc,'DD-MM-YYYY') DATE_CALC,case when a.day_status = 2 then 'Closed' else 'Open' end as DAY_STATUS,COMAPNY_ID,CAPACITY_SOURCE,LOCATION_ID from lib_capacity_calc_dtls a, lib_capacity_calc_mst b where b.id=a.mst_id $company_cond $date_calc and day_status=2";
    	}
    	$sql_data= $this->db->query($sql)->result();
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb
        /*$day_status=array(1=>"Open",2=>"Closed");
        foreach($sql_data as $row)
        {
            if(isset($day_status[$row->DAY_STATUS]))
            {
                $day_st[date("d-m-Y", strtotime($row->DATE_CALC))]= $day_status[$row->DAY_STATUS];
            }
            $day_status_days[date("d-m-Y", strtotime($row->DATE_CALC))]=date("d-m-Y", strtotime($row->DATE_CALC));

        }*/
        return $sql_data;

    }

    function get_tna_info($po_id)
    {
<<<<<<< HEAD
        $po_cond = " and po_number_id='$po_id'";
        $sql="select min(task_start_date) as TASK_START_DATE, max(task_finish_date) as TASK_FINISH_DATE, PO_NUMBER_ID  from tna_process_mst where is_deleted=0 and status_active=1  $po_cond and task_number=86   group by po_number_id ";
        $sql_data= $this->db->query($sql)->result();
        $sel_pos="";
        $i=0;
        foreach($sql_data as $row)
        {
            $tna_task_data[$i]['PO_NUMBER_ID']=$row->PO_NUMBER_ID;
            $tna_task_data[$i]['TASK_START_DATE']= date("d-m-Y", strtotime($row->TASK_START_DATE)) ;
            $tna_task_data[$i]['TASK_FINISH_DATE']=date("d-m-Y", strtotime($row->TASK_FINISH_DATE)) ;
            if($sel_pos=="") $sel_pos=$row->PO_NUMBER_ID; else $sel_pos .=",".$row->PO_NUMBER_ID;
            
            /*if($this->db->dbdriver=='mysqli'){
            $tna_task_data[$i]['po_number_id']=$row->po_number_id;
            $tna_task_data[$i]['task_start_date']= date("d-m-y", strtotime($row->task_start_date)) ;
            $tna_task_data[$i]['task_finish_date']=date("d-m-y", strtotime($row->task_finish_date)) ;
            
            if($sel_pos=="") $sel_pos=$row->po_number_id; else $sel_pos .=",".$row->po_number_id;
            }else{
            $tna_task_data[$i]['PO_NUMBER_ID']=$row->PO_NUMBER_ID;
            $tna_task_data[$i]['TASK_START_DATE']= date("d-m-Y", strtotime($row->TASK_START_DATE)) ;
            $tna_task_data[$i]['TASK_FINISH_DATE']=date("d-m-Y", strtotime($row->TASK_FINISH_DATE)) ;
            
            if($sel_pos=="") $sel_pos=$row->PO_NUMBER_ID; else $sel_pos .=",".$row->PO_NUMBER_ID;
            }*/
            $i++;
        }
        if( $sel_pos=="" )
        {
=======
    	$po_cond = " and po_number_id='$po_id'";
    	$sql="select min(task_start_date) as TASK_START_DATE, max(task_finish_date) as TASK_FINISH_DATE, PO_NUMBER_ID  from tna_process_mst where is_deleted=0 and status_active=1  $po_cond and task_number=86   group by po_number_id ";
    	$sql_data= $this->db->query($sql)->result();
    	$sel_pos="";
    	$i=0;
    	foreach($sql_data as $row)
    	{
    		$tna_task_data[$i]['PO_NUMBER_ID']=$row->PO_NUMBER_ID;
    		$tna_task_data[$i]['TASK_START_DATE']= date("d-m-Y", strtotime($row->TASK_START_DATE)) ;
    		$tna_task_data[$i]['TASK_FINISH_DATE']=date("d-m-Y", strtotime($row->TASK_FINISH_DATE)) ;
    		if($sel_pos=="") $sel_pos=$row->PO_NUMBER_ID; else $sel_pos .=",".$row->PO_NUMBER_ID;

			/*if($this->db->dbdriver=='mysqli'){
			$tna_task_data[$i]['po_number_id']=$row->po_number_id;
			$tna_task_data[$i]['task_start_date']= date("d-m-y", strtotime($row->task_start_date)) ;
			$tna_task_data[$i]['task_finish_date']=date("d-m-y", strtotime($row->task_finish_date)) ;
			
			if($sel_pos=="") $sel_pos=$row->po_number_id; else $sel_pos .=",".$row->po_number_id;
			}else{
			$tna_task_data[$i]['PO_NUMBER_ID']=$row->PO_NUMBER_ID;
			$tna_task_data[$i]['TASK_START_DATE']= date("d-m-Y", strtotime($row->TASK_START_DATE)) ;
			$tna_task_data[$i]['TASK_FINISH_DATE']=date("d-m-Y", strtotime($row->TASK_FINISH_DATE)) ;
			
			if($sel_pos=="") $sel_pos=$row->PO_NUMBER_ID; else $sel_pos .=",".$row->PO_NUMBER_ID;
		}*/
		$i++;
	}
	if( $sel_pos=="" )
	{
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb
            //return "Sorry! No PO found for planning in TNA process.";
		return array('errorMsg' => 'Sorry! No PO found for planning in TNA process.');
	} 
	return $tna_task_data;

}

<<<<<<< HEAD
    function get_production_data_info($company_id,$po_id)
    {
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
            /*if($this->db->dbdriver=='mysqli'){
            $data_arr['company_info'][$value->id] = $value->company_name;
            }else{
            $data_arr['company_info'][$value->ID] = $value->COMPANY_NAME;
            }*/
        }
        
        foreach ($loc_res as $value) {
            $data_arr['location_info'][$value->ID] = $value->LOCATION_NAME;
            /*if($this->db->dbdriver=='mysqli'){
            $data_arr['location_info'][$value->id] = $value->location_name;
            }else{
            $data_arr['location_info'][$value->ID] = $value->LOCATION_NAME;
            }*/
        }
        
        foreach ($line_res as $value) {
            $data_arr['line_info'][$value->ID] = $value->LINE_NAME;
            /*if($this->db->dbdriver=='mysqli'){
            $data_arr['line_info'][$value->id] = $value->line_name;
            }else{
            $data_arr['line_info'][$value->ID] = $value->LINE_NAME;
            }*/
        }
        
        $production_sql="select PO_BREAK_DOWN_ID,SUM(PRODUCTION_QUANTITY) AS PRODUCTION_QUANTITY,PRODUCTION_DATE,SEWING_LINE,COMPANY_ID,LOCATION from   pro_garments_production_mst where production_type=5 $po_cond  $company_cond  and status_active=1 and is_deleted=0     group by production_date,po_break_down_id,sewing_line, company_id,location order by sewing_line,po_break_down_id,production_date";
        $production_data= $this->db->query($production_sql)->result();

        $i=0;
        foreach($production_data as $rows)
        {
                $data_array[$i]["po_break_down_id"] = $rows->PO_BREAK_DOWN_ID;
                $data_array[$i]["sewing_line"] =  $data_arr['line_info'][$rows->SEWING_LINE] ;
                $data_array[$i]["company_id"] = $rows->COMPANY_ID;
                $data_array[$i]["company_name"] =  $data_arr['company_info'][$rows->COMPANY_ID];
                $data_array[$i]["location_id"] = $rows->LOCATION;
                $data_array[$i]["location_name"] =  $data_arr['location_info'][$rows->LOCATION];
                $data_array[$i]["production_date"] = date("d-m-Y", strtotime($rows->PRODUCTION_DATE)) ; 
                
            /*if($this->db->dbdriver=='mysqli'){
                $data_array[$i]["po_break_down_id"] = $rows->po_break_down_id;
                $data_array[$i]["sewing_line"] =  $data_arr['line_info'][$rows->sewing_line] ;
                $data_array[$i]["company_id"] = $rows->company_id;
                $data_array[$i]["company_name"] =  $data_arr['company_info'][$rows->company_id];
                $data_array[$i]["location_id"] = $rows->location;
                $data_array[$i]["location_name"] =  $data_arr['location_info'][$rows->location];
                $data_array[$i]["production_date"] = date("d-m-Y", strtotime($rows->production_date)) ; 
            }else{
                $data_array[$i]["po_break_down_id"] = $rows->PO_BREAK_DOWN_ID;
                $data_array[$i]["sewing_line"] =  $data_arr['line_info'][$rows->SEWING_LINE] ;
                $data_array[$i]["company_id"] = $rows->COMPANY_ID;
                $data_array[$i]["company_name"] =  $data_arr['company_info'][$rows->COMPANY_ID];
                $data_array[$i]["location_id"] = $rows->LOCATION;
                $data_array[$i]["location_name"] =  $data_arr['location_info'][$rows->LOCATION];
                $data_array[$i]["production_date"] = date("d-m-Y", strtotime($rows->PRODUCTION_DATE)) ; 
            }*/
            $i++;      
        }
        return $data_array;
        }
        
        /**
        * [create_plan for Plan CRUD]
        * @param  [object] $plan_obj [description]
        * @return [array]           [description]
        */
        function create_plan($plan_obj){
            $response_obj = json_decode($plan_obj);
            if($response_obj->Status == true){
                $this->db->trans_start();
                $plan_to_delete = "";
                $plan_ids = array();
                $plan_id = $this->get_max_value("PPL_SEWING_PLAN_BOARD","PLAN_ID") + 1;
                $max_id  = $this->get_max_value("PPL_SEWING_PLAN_BOARD","ID") + 1;
                
                if(!isset($sewing_plan_row->MERGE_TYPE))  $sewing_plan_row->MERGE_TYPE='';
                if(!isset($sewing_plan_row->MERGE_COMMENTS))  $sewing_plan_row->MERGE_COMMENTS='';
                
                foreach ($response_obj->SewingPlanBoard as $sewing_plan_row) {
                    if($this->db->dbdriver=='mysqli'){
                    $ppl_sewing_plan_board_data = array(
                    'LINE_ID'           => $sewing_plan_row->LINE_ID,
                    'PO_BREAK_DOWN_ID'  => $sewing_plan_row->PO_BREAK_DOWN_ID,
                    'START_DATE'        => date("Y-m-d",strtotime($sewing_plan_row->START_DATE)),
                    'START_HOUR'        => $sewing_plan_row->START_HOUR,
                    'END_DATE'          => date("Y-m-d",strtotime($sewing_plan_row->END_DATE)),
                    'END_HOUR'          => $sewing_plan_row->END_HOUR,
                    'DURATION'          => $sewing_plan_row->DURATION,
                    'PLAN_QNTY'         => $sewing_plan_row->PLAN_QNTY,
                    'COMP_LEVEL'        => $sewing_plan_row->COMP_LEVEL,
                    'FIRST_DAY_OUTPUT'  => $sewing_plan_row->FIRST_DAY_OUTPUT,
                    'INCREMENT_QTY'     => $sewing_plan_row->INCREMENT_QTY,
                    'TERGET'            => $sewing_plan_row->TERGET,
                    'INSERTED_BY'       => $sewing_plan_row->INSERTED_BY,
                    'COMPANY_ID'        => $sewing_plan_row->COMPANY_ID,
                    'LOCATION_ID'       => $sewing_plan_row->LOCATION_ID,
                    'ITEM_NUMBER_ID'    => $sewing_plan_row->ITEM_NUMBER_ID,
                    'OFF_DAY_PLAN'      => $sewing_plan_row->OFF_DAY_PLAN,
                    'ORDER_COMPLEXITY'  => $sewing_plan_row->ORDER_COMPLEXITY,
                    'SHIP_DATE'         => date("Y-m-d",strtotime($sewing_plan_row->SHIP_DATE)),
                    'PLAN_LEVEL'        => $sewing_plan_row->PLAN_LEVEL,
                    'SEQ_NO'            => $sewing_plan_row->SEQ_NO,
                    'PO_COMPANY_ID'     => $sewing_plan_row->PO_COMPANY_ID,
                    'MERGE_TYPE'        =>  ($sewing_plan_row->MERGE_TYPE == '' ? "" : $sewing_plan_row->MERGE_TYPE), //($sewing_plan_row->MERGE_TYPE,
                    'MERGE_COMMENTS'    => ($sewing_plan_row->MERGE_COMMENTS == '' ? "" : $sewing_plan_row->MERGE_COMMENTS)//$sewing_plan_row->MERGE_COMMENTS
                    );
                    'FIRST_DAY_CAPACITY,LAST_DAY_CAPACITY,SEQ_NO,PO_COMPANY_ID';
                    }else{
                    $ppl_sewing_plan_board_data = array(
                    'LINE_ID'           => $sewing_plan_row->LINE_ID,
                    'PO_BREAK_DOWN_ID'  => $sewing_plan_row->PO_BREAK_DOWN_ID,
                    'START_DATE'        => date("d-M-Y",strtotime($sewing_plan_row->START_DATE)),
                    'START_HOUR'        => $sewing_plan_row->START_HOUR,
                    'END_DATE'          => date("d-M-Y",strtotime($sewing_plan_row->END_DATE)),
                    'END_HOUR'          => $sewing_plan_row->END_HOUR,
                    'DURATION'          => $sewing_plan_row->DURATION,
                    'PLAN_QNTY'         => $sewing_plan_row->PLAN_QNTY,
                    'COMP_LEVEL'        => $sewing_plan_row->COMP_LEVEL,
                    'FIRST_DAY_OUTPUT'  => $sewing_plan_row->FIRST_DAY_OUTPUT,
                    'INCREMENT_QTY'     => $sewing_plan_row->INCREMENT_QTY,
                    'TERGET'            => $sewing_plan_row->TERGET,
                    'INSERTED_BY'       => $sewing_plan_row->INSERTED_BY,
                    'COMPANY_ID'        => $sewing_plan_row->COMPANY_ID,
                    'LOCATION_ID'       => $sewing_plan_row->LOCATION_ID,
                    'ITEM_NUMBER_ID'    => $sewing_plan_row->ITEM_NUMBER_ID,
                    'OFF_DAY_PLAN'      => $sewing_plan_row->OFF_DAY_PLAN,
                    'ORDER_COMPLEXITY'  => $sewing_plan_row->ORDER_COMPLEXITY,
                    'SHIP_DATE'         => date("d-M-Y",strtotime($sewing_plan_row->SHIP_DATE)),
                    'PLAN_LEVEL'        => $sewing_plan_row->PLAN_LEVEL,
                    'SEQ_NO'            => $sewing_plan_row->SEQ_NO,
                    'PO_COMPANY_ID'     => $sewing_plan_row->PO_COMPANY_ID,
                    'MERGE_TYPE'        =>  (trim($sewing_plan_row->MERGE_TYPE) == '' ? "" :  $sewing_plan_row->MERGE_TYPE ), //($sewing_plan_row->MERGE_TYPE,
                    'MERGE_COMMENTS'    => (trim($sewing_plan_row->MERGE_COMMENTS) == '' ? "" :  $sewing_plan_row->MERGE_COMMENTS )//$sewing_plan_row->MERGE_COMMENTS
                    );
                    'FIRST_DAY_CAPACITY,LAST_DAY_CAPACITY,SEQ_NO,PO_COMPANY_ID';
                    }
                    if($sewing_plan_row->RowState == "add"){
                        if($this->db->dbdriver=='mysqli'){                    
                        $ppl_sewing_plan_board_data['PLAN_ID'] = $plan_id;
                        $ppl_sewing_plan_board_data['ID'] = $max_id++;
                        $ppl_sewing_plan_board_data['INSERTED_BY']   = $sewing_plan_row->INSERTED_BY;
                        $ppl_sewing_plan_board_data['INSERT_DATE']   = date("Y-m-d");
                        $this->insertData($ppl_sewing_plan_board_data, "PPL_SEWING_PLAN_BOARD");
                        $plan_ids[$sewing_plan_row->PLAN_ID] = $plan_id;
                        }else{
                        $ppl_sewing_plan_board_data['PLAN_ID'] = $plan_id;
                        $ppl_sewing_plan_board_data['ID'] = $max_id++;
                        $ppl_sewing_plan_board_data['INSERTED_BY']   = $sewing_plan_row->INSERTED_BY;
                        $ppl_sewing_plan_board_data['INSERT_DATE']   = date("d-M-Y");
                        $this->insertData($ppl_sewing_plan_board_data, "PPL_SEWING_PLAN_BOARD");
                        $plan_ids[$sewing_plan_row->PLAN_ID] = $plan_id;
                        }
                    }else if($sewing_plan_row->RowState == "update"){
                        if($this->db->dbdriver=='mysqli'){ 
                        $plan_ids[$sewing_plan_row->PLAN_ID] = $sewing_plan_row->PLAN_ID;
                        $ppl_sewing_plan_board_data['UPDATE_DATE']      = date("Y-m-d");
                        $ppl_sewing_plan_board_data['UPDATED_BY']       = $sewing_plan_row->UPDATED_BY;
                        $plan_to_delete .= $sewing_plan_row->PLAN_ID . ",";
                        $this->updateData('PPL_SEWING_PLAN_BOARD', $ppl_sewing_plan_board_data, array('PLAN_ID' => $sewing_plan_row->PLAN_ID));
                        }else{
                        $plan_ids[$sewing_plan_row->PLAN_ID] = $sewing_plan_row->PLAN_ID;
                        $ppl_sewing_plan_board_data['UPDATE_DATE']      = date("d-M-Y");
                        $ppl_sewing_plan_board_data['UPDATED_BY']       = $sewing_plan_row->UPDATED_BY;
                        $plan_to_delete .= $sewing_plan_row->PLAN_ID . ",";
                        $this->updateData('PPL_SEWING_PLAN_BOARD', $ppl_sewing_plan_board_data, array('PLAN_ID' => $sewing_plan_row->PLAN_ID));
                        }
                    }else if($sewing_plan_row->RowState == "delete"){
                        $plan_to_delete .= $sewing_plan_row->PLAN_ID . ",";
                        $this->deleteRowByAttribute('PPL_SEWING_PLAN_BOARD', array('ID' => $sewing_plan_row->PLAN_ID));
                    }
                    $plan_id++;
                }
                
                $plan_to_delete = rtrim($plan_to_delete,",");
                if($plan_to_delete != ""){
                    // delete all child table rows by PLAN_ID
                    $this->db->query("delete from PPL_SEWING_PLAN_BOARD_DTLS where PLAN_ID in($plan_to_delete)");
                    $this->db->query("delete from PPL_SEWING_PLAN_BOARD_POWISE where PLAN_ID in($plan_to_delete)");
                }
                //MERGE_TYPE,MERGE_COMMENTS
                $max_plan_dtls_id = $this->get_max_value("PPL_SEWING_PLAN_BOARD_DTLS","ID") + 1;
                foreach ($response_obj->SewingPlanBoardDtls as $sewing_plan_dtls_row) {
                    if($sewing_plan_dtls_row->RowState != "delete"){
                        if($this->db->dbdriver=='mysqli'){
                            $planning_id = ($sewing_plan_dtls_row->RowState == "add") ? $plan_ids[$sewing_plan_dtls_row->PLAN_ID] : $sewing_plan_dtls_row->PLAN_ID;
                            $ppl_sewing_plan_board_dtls_data = array(
                            'ID'        => $max_plan_dtls_id++,
                            'PLAN_ID'   => $planning_id,
                            'PLAN_DATE' => date("Y-m-d",strtotime($sewing_plan_dtls_row->PLAN_DATE)),
                            'PLAN_QNTY' => $sewing_plan_dtls_row->PLAN_QNTY
                            );
                            $this->insertData($ppl_sewing_plan_board_dtls_data, "PPL_SEWING_PLAN_BOARD_DTLS");
                        }else{
                            $planning_id = ($sewing_plan_dtls_row->RowState == "add") ? $plan_ids[$sewing_plan_dtls_row->PLAN_ID] : $sewing_plan_dtls_row->PLAN_ID;
                            $ppl_sewing_plan_board_dtls_data = array(
                            'ID'        => $max_plan_dtls_id++,
                            'PLAN_ID'   => $planning_id,
                            'PLAN_DATE' => date("d-M-Y",strtotime($sewing_plan_dtls_row->PLAN_DATE)),
                            'PLAN_QNTY' => $sewing_plan_dtls_row->PLAN_QNTY
                            );
                            $this->insertData($ppl_sewing_plan_board_dtls_data, "PPL_SEWING_PLAN_BOARD_DTLS");
                        }
                    }
                }
                //print_r($ppl_sewing_plan_board_data);
                //die;
                $max_plan_po_id = $this->get_max_value("PPL_SEWING_PLAN_BOARD_POWISE","ID") + 1;
                foreach ($response_obj->SewingPlanBoardPOWise as $sewing_plan_po_row) {
                    if($sewing_plan_po_row->RowState != "delete"){
                        $planning_id = ($sewing_plan_po_row->RowState == "add") ? $plan_ids[$sewing_plan_po_row->PLAN_ID] : $sewing_plan_po_row->PLAN_ID;
                        $ppl_sewing_plan_board_po_data = array(
                        'ID'                => $max_plan_po_id++,
                        'PLAN_ID'           => $planning_id,
                        'PO_BREAK_DOWN_ID'  => $sewing_plan_po_row->PO_BREAK_DOWN_ID,
                        'PLAN_QNTY'         => $sewing_plan_po_row->PLAN_QNTY,
                        'ITEM_NUMBER_ID'    => $sewing_plan_po_row->ITEM_NUMBER_ID,
                        'COLOR_NUMBER_ID'   => $sewing_plan_po_row->COLOR_NUMBER_ID,
                        'SIZE_NUMBER_ID'    => $sewing_plan_po_row->SIZE_NUMBER_ID,
                        'JOB_NO'            => $sewing_plan_po_row->JOB_NO
                        );
                        $this->insertData($ppl_sewing_plan_board_po_data, "PPL_SEWING_PLAN_BOARD_POWISE");
                    }
                }
                $this->db->trans_complete();
                if ($this->db->trans_status() == TRUE) {
                    return $resultset["status"] = "Successful";
                } else {
                    $resultset["status"] = "Failed";
                }
            }else{
                return $resultset["status"] = "Failed";
            }
        }
    
    function get_capacity_and_alocation_data_info($fromDate,$toDate,$company,$type)
    {
        if($fromDate && $toDate){
             $date=" and b.date_calc between '".date('d-M-Y',strtotime($fromDate))."' and '".date('d-M-Y',strtotime($toDate))."'";
         }else{
             $date="";
         }
         
         if($company){
             $companycond=" and a.comapny_id = '".$company."'";
         }else{
             $companycond="";
         }
        
         $rows=array();
         $sql = $this->db->query("
SELECT a.ID,a.COMAPNY_ID, b.ID,B.MST_ID,b.MONTH_ID,b.DATE_CALC,b.DAY_STATUS,b.NO_OF_LINE,b.CAPACITY_MIN,b.CAPACITY_PCS from  lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id $companycond $date order by b.date_calc");
         foreach( $sql->result() as $row){
             if($type==1){
                 if(isset($data[$row->DATE_CALC])){
                     $data[$row->DATE_CALC]+=$row->CAPACITY_PCS;
                     }else{
                         $data[$row->DATE_CALC]=$row->CAPACITY_PCS;
                 }
             }
             if($type==2){
                 $date = new DateTime($row->DATE_CALC);
                 $week = $date->format("W");
                 if(isset($data[$week])){
                 $data[$week]+=$row->CAPACITY_PCS;
                 }else{
                     $data[$week]=$row->CAPACITY_PCS;
                 }
             }
              if($type==3){
                  if(isset($data[date("M-Y",strtotime($row->DATE_CALC))])){
                         $data[date("M-Y",strtotime($row->DATE_CALC))]+=$row->CAPACITY_PCS;
                  }else{
                        $data[date("M-Y",strtotime($row->DATE_CALC))]=$row->CAPACITY_PCS;
                  }
             }
         }
         $alocation=$this->get_alocation_data_info($fromDate,$toDate,$company,$type);
         return array("Capacity"=>$data,"Alocation"=>$alocation);
          //return array("Capacity"=>$data);
    } 
    function get_alocation_data_info($fromDate,$toDate,$company,$type)
    {
        if($fromDate && $toDate){
             $date=" and b.date_name between '".date('d-M-Y',strtotime($fromDate))."' and '".date('d-M-Y',strtotime($toDate))."'";
         }else{
             $date="";
         }
         
         if($company){
             $companycond=" and a.company_id = '".$company."'";
         }else{
             $companycond="";
         }
         
        $sql = $this->db->query("
SELECT a.ID,a.COMPANY_ID, b.DATE_NAME,b.QTY,b.SMV from  ppl_order_allocation_mst a, ppl_order_allocation_dtls b where a.id=b.mst_id $companycond $date order by b.date_name");
         $data=array();
         foreach( $sql->result() as $row){
             if($type==1){
                 if(isset($data[$row->DATE_NAME])){
                 $data[$row->DATE_NAME]+=$row->QTY;
                 }else{
                     $data[$row->DATE_NAME]=$row->QTY;
                 }
             }
             if($type==2){
                $date = new DateTime($row->DATE_NAME);
                $week = $date->format("W");
                if(isset($data[$week])){
                $data[$week]+=$row->QTY;
                }else{
                $data[$week]=$row->QTY;
                }
              }
              if($type==3){
                  if(isset($data[date("M-Y",strtotime($row->DATE_NAME))])){
                       $data[date("M-Y",strtotime($row->DATE_NAME))]+=$row->QTY;
                  }else{
                       $data[date("M-Y",strtotime($row->DATE_NAME))]=$row->QTY;
                  }
              }
         }
         return $data;
    }
}
=======
function get_production_data_info($company_id,$po_id)
{
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
			/*if($this->db->dbdriver=='mysqli'){
			$data_arr['company_info'][$value->id] = $value->company_name;
			}else{
			$data_arr['company_info'][$value->ID] = $value->COMPANY_NAME;
		}*/
	}

	foreach ($loc_res as $value) {
		$data_arr['location_info'][$value->ID] = $value->LOCATION_NAME;
			/*if($this->db->dbdriver=='mysqli'){
			$data_arr['location_info'][$value->id] = $value->location_name;
			}else{
			$data_arr['location_info'][$value->ID] = $value->LOCATION_NAME;
		}*/
	}

	foreach ($line_res as $value) {
		$data_arr['line_info'][$value->ID] = $value->LINE_NAME;
			/*if($this->db->dbdriver=='mysqli'){
			$data_arr['line_info'][$value->id] = $value->line_name;
			}else{
			$data_arr['line_info'][$value->ID] = $value->LINE_NAME;
		}*/
	}

	$production_sql="select PO_BREAK_DOWN_ID,SUM(PRODUCTION_QUANTITY) AS PRODUCTION_QUANTITY,PRODUCTION_DATE,SEWING_LINE,COMPANY_ID,LOCATION from   pro_garments_production_mst where production_type=5 $po_cond  $company_cond  and status_active=1 and is_deleted=0     group by production_date,po_break_down_id,sewing_line, company_id,location order by sewing_line,po_break_down_id,production_date";
	$production_data= $this->db->query($production_sql)->result();

	$i=0;
	foreach($production_data as $rows)
	{
		$data_array[$i]["po_break_down_id"] = $rows->PO_BREAK_DOWN_ID;
		$data_array[$i]["sewing_line"] =  $data_arr['line_info'][$rows->SEWING_LINE] ;
		$data_array[$i]["company_id"] = $rows->COMPANY_ID;
		$data_array[$i]["company_name"] =  $data_arr['company_info'][$rows->COMPANY_ID];
		$data_array[$i]["location_id"] = $rows->LOCATION;
		$data_array[$i]["location_name"] =  $data_arr['location_info'][$rows->LOCATION];
		$data_array[$i]["production_date"] = date("d-m-Y", strtotime($rows->PRODUCTION_DATE)) ; 

			/*if($this->db->dbdriver=='mysqli'){
				$data_array[$i]["po_break_down_id"] = $rows->po_break_down_id;
				$data_array[$i]["sewing_line"] =  $data_arr['line_info'][$rows->sewing_line] ;
				$data_array[$i]["company_id"] = $rows->company_id;
				$data_array[$i]["company_name"] =  $data_arr['company_info'][$rows->company_id];
				$data_array[$i]["location_id"] = $rows->location;
				$data_array[$i]["location_name"] =  $data_arr['location_info'][$rows->location];
				$data_array[$i]["production_date"] = date("d-m-Y", strtotime($rows->production_date)) ; 
			}else{
				$data_array[$i]["po_break_down_id"] = $rows->PO_BREAK_DOWN_ID;
				$data_array[$i]["sewing_line"] =  $data_arr['line_info'][$rows->SEWING_LINE] ;
				$data_array[$i]["company_id"] = $rows->COMPANY_ID;
				$data_array[$i]["company_name"] =  $data_arr['company_info'][$rows->COMPANY_ID];
				$data_array[$i]["location_id"] = $rows->LOCATION;
				$data_array[$i]["location_name"] =  $data_arr['location_info'][$rows->LOCATION];
				$data_array[$i]["production_date"] = date("d-m-Y", strtotime($rows->PRODUCTION_DATE)) ; 
			}*/
			$i++;      
		}
		return $data_array;
	}

		/**
		* [create_plan for Plan CRUD]
		* @param  [object] $plan_obj [description]
		* @return [array]           [description]
		*/
		function create_plan($plan_obj){
			$response_obj = json_decode($plan_obj);
			if($response_obj->Status == true){
				$this->db->trans_start();
				$plan_to_delete = "";
				$plan_ids = array();
				$plan_id = $this->get_max_value("PPL_SEWING_PLAN_BOARD","PLAN_ID") + 1;
				$max_id  = $this->get_max_value("PPL_SEWING_PLAN_BOARD","ID") + 1;
				
				if(!isset($sewing_plan_row->MERGE_TYPE))  $sewing_plan_row->MERGE_TYPE='';
				if(!isset($sewing_plan_row->MERGE_COMMENTS))  $sewing_plan_row->MERGE_COMMENTS='';
				
				foreach ($response_obj->SewingPlanBoard as $sewing_plan_row) {
					if($this->db->dbdriver=='mysqli'){
						$ppl_sewing_plan_board_data = array(
							'LINE_ID'           => $sewing_plan_row->LINE_ID,
							'PO_BREAK_DOWN_ID'  => $sewing_plan_row->PO_BREAK_DOWN_ID,
							'START_DATE'        => date("Y-m-d",strtotime($sewing_plan_row->START_DATE)),
							'START_HOUR'        => $sewing_plan_row->START_HOUR,
							'END_DATE'          => date("Y-m-d",strtotime($sewing_plan_row->END_DATE)),
							'END_HOUR'          => $sewing_plan_row->END_HOUR,
							'DURATION'          => $sewing_plan_row->DURATION,
							'PLAN_QNTY'         => $sewing_plan_row->PLAN_QNTY,
							'COMP_LEVEL'        => $sewing_plan_row->COMP_LEVEL,
							'FIRST_DAY_OUTPUT'  => $sewing_plan_row->FIRST_DAY_OUTPUT,
							'INCREMENT_QTY'     => $sewing_plan_row->INCREMENT_QTY,
							'TERGET'            => $sewing_plan_row->TERGET,
							'INSERTED_BY'       => $sewing_plan_row->INSERTED_BY,
							'COMPANY_ID'        => $sewing_plan_row->COMPANY_ID,
							'LOCATION_ID'       => $sewing_plan_row->LOCATION_ID,
							'ITEM_NUMBER_ID'    => $sewing_plan_row->ITEM_NUMBER_ID,
							'OFF_DAY_PLAN'      => $sewing_plan_row->OFF_DAY_PLAN,
							'ORDER_COMPLEXITY'  => $sewing_plan_row->ORDER_COMPLEXITY,
							'SHIP_DATE'         => date("Y-m-d",strtotime($sewing_plan_row->SHIP_DATE)),
							'PLAN_LEVEL'        => $sewing_plan_row->PLAN_LEVEL,
							'SEQ_NO'            => $sewing_plan_row->SEQ_NO,
							'PO_COMPANY_ID'     => $sewing_plan_row->PO_COMPANY_ID,
					'MERGE_TYPE'     	=>  ($sewing_plan_row->MERGE_TYPE == '' ? "" : $sewing_plan_row->MERGE_TYPE), //($sewing_plan_row->MERGE_TYPE,
					'MERGE_COMMENTS'    => ($sewing_plan_row->MERGE_COMMENTS == '' ? "" : $sewing_plan_row->MERGE_COMMENTS)//$sewing_plan_row->MERGE_COMMENTS
				);
						'FIRST_DAY_CAPACITY,LAST_DAY_CAPACITY,SEQ_NO,PO_COMPANY_ID';
					}else{
						$ppl_sewing_plan_board_data = array(
							'LINE_ID'           => $sewing_plan_row->LINE_ID,
							'PO_BREAK_DOWN_ID'  => $sewing_plan_row->PO_BREAK_DOWN_ID,
							'START_DATE'        => date("d-M-Y",strtotime($sewing_plan_row->START_DATE)),
							'START_HOUR'        => $sewing_plan_row->START_HOUR,
							'END_DATE'          => date("d-M-Y",strtotime($sewing_plan_row->END_DATE)),
							'END_HOUR'          => $sewing_plan_row->END_HOUR,
							'DURATION'          => $sewing_plan_row->DURATION,
							'PLAN_QNTY'         => $sewing_plan_row->PLAN_QNTY,
							'COMP_LEVEL'        => $sewing_plan_row->COMP_LEVEL,
							'FIRST_DAY_OUTPUT'  => $sewing_plan_row->FIRST_DAY_OUTPUT,
							'INCREMENT_QTY'     => $sewing_plan_row->INCREMENT_QTY,
							'TERGET'            => $sewing_plan_row->TERGET,
							'INSERTED_BY'       => $sewing_plan_row->INSERTED_BY,
							'COMPANY_ID'        => $sewing_plan_row->COMPANY_ID,
							'LOCATION_ID'       => $sewing_plan_row->LOCATION_ID,
							'ITEM_NUMBER_ID'    => $sewing_plan_row->ITEM_NUMBER_ID,
							'OFF_DAY_PLAN'      => $sewing_plan_row->OFF_DAY_PLAN,
							'ORDER_COMPLEXITY'  => $sewing_plan_row->ORDER_COMPLEXITY,
							'SHIP_DATE'         => date("d-M-Y",strtotime($sewing_plan_row->SHIP_DATE)),
							'PLAN_LEVEL'        => $sewing_plan_row->PLAN_LEVEL,
							'SEQ_NO'            => $sewing_plan_row->SEQ_NO,
							'PO_COMPANY_ID'     => $sewing_plan_row->PO_COMPANY_ID,
					'MERGE_TYPE'     	=>  (trim($sewing_plan_row->MERGE_TYPE) == '' ? "" :  $sewing_plan_row->MERGE_TYPE ), //($sewing_plan_row->MERGE_TYPE,
					'MERGE_COMMENTS'    => (trim($sewing_plan_row->MERGE_COMMENTS) == '' ? "" :  $sewing_plan_row->MERGE_COMMENTS )//$sewing_plan_row->MERGE_COMMENTS
				);
						'FIRST_DAY_CAPACITY,LAST_DAY_CAPACITY,SEQ_NO,PO_COMPANY_ID';
					}
					if($sewing_plan_row->RowState == "add"){
						if($this->db->dbdriver=='mysqli'){                    
							$ppl_sewing_plan_board_data['PLAN_ID'] = $plan_id;
							$ppl_sewing_plan_board_data['ID'] = $max_id++;
							$ppl_sewing_plan_board_data['INSERTED_BY']   = $sewing_plan_row->INSERTED_BY;
							$ppl_sewing_plan_board_data['INSERT_DATE']   = date("Y-m-d");
							$this->insertData($ppl_sewing_plan_board_data, "PPL_SEWING_PLAN_BOARD");
							$plan_ids[$sewing_plan_row->PLAN_ID] = $plan_id;
						}else{
							$ppl_sewing_plan_board_data['PLAN_ID'] = $plan_id;
							$ppl_sewing_plan_board_data['ID'] = $max_id++;
							$ppl_sewing_plan_board_data['INSERTED_BY']   = $sewing_plan_row->INSERTED_BY;
							$ppl_sewing_plan_board_data['INSERT_DATE']   = date("d-M-Y");
							$this->insertData($ppl_sewing_plan_board_data, "PPL_SEWING_PLAN_BOARD");
							$plan_ids[$sewing_plan_row->PLAN_ID] = $plan_id;
						}
					}else if($sewing_plan_row->RowState == "update"){
						if($this->db->dbdriver=='mysqli'){ 
							$plan_ids[$sewing_plan_row->PLAN_ID] = $sewing_plan_row->PLAN_ID;
							$ppl_sewing_plan_board_data['UPDATE_DATE']      = date("Y-m-d");
							$ppl_sewing_plan_board_data['UPDATED_BY']       = $sewing_plan_row->UPDATED_BY;
							$plan_to_delete .= $sewing_plan_row->PLAN_ID . ",";
							$this->updateData('PPL_SEWING_PLAN_BOARD', $ppl_sewing_plan_board_data, array('PLAN_ID' => $sewing_plan_row->PLAN_ID));
						}else{
							$plan_ids[$sewing_plan_row->PLAN_ID] = $sewing_plan_row->PLAN_ID;
							$ppl_sewing_plan_board_data['UPDATE_DATE']      = date("d-M-Y");
							$ppl_sewing_plan_board_data['UPDATED_BY']       = $sewing_plan_row->UPDATED_BY;
							$plan_to_delete .= $sewing_plan_row->PLAN_ID . ",";
							$this->updateData('PPL_SEWING_PLAN_BOARD', $ppl_sewing_plan_board_data, array('PLAN_ID' => $sewing_plan_row->PLAN_ID));
						}
					}else if($sewing_plan_row->RowState == "delete"){
						$plan_to_delete .= $sewing_plan_row->PLAN_ID . ",";
						$this->deleteRowByAttribute('PPL_SEWING_PLAN_BOARD', array('ID' => $sewing_plan_row->PLAN_ID));
					}
					$plan_id++;
				}
				
				$plan_to_delete = rtrim($plan_to_delete,",");
				if($plan_to_delete != ""){
					// delete all child table rows by PLAN_ID
					$this->db->query("delete from PPL_SEWING_PLAN_BOARD_DTLS where PLAN_ID in($plan_to_delete)");
					$this->db->query("delete from PPL_SEWING_PLAN_BOARD_POWISE where PLAN_ID in($plan_to_delete)");
				}
				//MERGE_TYPE,MERGE_COMMENTS
				$max_plan_dtls_id = $this->get_max_value("PPL_SEWING_PLAN_BOARD_DTLS","ID") + 1;
				foreach ($response_obj->SewingPlanBoardDtls as $sewing_plan_dtls_row) {
					if($sewing_plan_dtls_row->RowState != "delete"){
						if($this->db->dbdriver=='mysqli'){
							$planning_id = ($sewing_plan_dtls_row->RowState == "add") ? $plan_ids[$sewing_plan_dtls_row->PLAN_ID] : $sewing_plan_dtls_row->PLAN_ID;
							$ppl_sewing_plan_board_dtls_data = array(
								'ID'        => $max_plan_dtls_id++,
								'PLAN_ID'   => $planning_id,
								'PLAN_DATE' => date("Y-m-d",strtotime($sewing_plan_dtls_row->PLAN_DATE)),
								'PLAN_QNTY' => $sewing_plan_dtls_row->PLAN_QNTY
							);
							$this->insertData($ppl_sewing_plan_board_dtls_data, "PPL_SEWING_PLAN_BOARD_DTLS");
						}else{
							$planning_id = ($sewing_plan_dtls_row->RowState == "add") ? $plan_ids[$sewing_plan_dtls_row->PLAN_ID] : $sewing_plan_dtls_row->PLAN_ID;
							$ppl_sewing_plan_board_dtls_data = array(
								'ID'        => $max_plan_dtls_id++,
								'PLAN_ID'   => $planning_id,
								'PLAN_DATE' => date("d-M-Y",strtotime($sewing_plan_dtls_row->PLAN_DATE)),
								'PLAN_QNTY' => $sewing_plan_dtls_row->PLAN_QNTY
							);
							$this->insertData($ppl_sewing_plan_board_dtls_data, "PPL_SEWING_PLAN_BOARD_DTLS");
						}
					}
				}
				//print_r($ppl_sewing_plan_board_data);
				//die;
				$max_plan_po_id = $this->get_max_value("PPL_SEWING_PLAN_BOARD_POWISE","ID") + 1;
				foreach ($response_obj->SewingPlanBoardPOWise as $sewing_plan_po_row) {
					if($sewing_plan_po_row->RowState != "delete"){
						$planning_id = ($sewing_plan_po_row->RowState == "add") ? $plan_ids[$sewing_plan_po_row->PLAN_ID] : $sewing_plan_po_row->PLAN_ID;
						$ppl_sewing_plan_board_po_data = array(
							'ID'                => $max_plan_po_id++,
							'PLAN_ID'           => $planning_id,
							'PO_BREAK_DOWN_ID'  => $sewing_plan_po_row->PO_BREAK_DOWN_ID,
							'PLAN_QNTY'         => $sewing_plan_po_row->PLAN_QNTY,
							'ITEM_NUMBER_ID'    => $sewing_plan_po_row->ITEM_NUMBER_ID,
							'COLOR_NUMBER_ID'   => $sewing_plan_po_row->COLOR_NUMBER_ID,
							'SIZE_NUMBER_ID'    => $sewing_plan_po_row->SIZE_NUMBER_ID,
							'JOB_NO'            => $sewing_plan_po_row->JOB_NO
						);
						$this->insertData($ppl_sewing_plan_board_po_data, "PPL_SEWING_PLAN_BOARD_POWISE");
					}
				}
				$this->db->trans_complete();
				if ($this->db->trans_status() == TRUE) {
					return $resultset["status"] = "Successful";
				} else {
					$resultset["status"] = "Failed";
				}
			}else{
				return $resultset["status"] = "Failed";
			}
		}

		function get_capacity_and_alocation_data_info($fromDate,$toDate,$company,$type)
		{
			if($fromDate && $toDate){
				$date=" and b.date_calc between '".date('d-M-Y',strtotime($fromDate))."' and '".date('d-M-Y',strtotime($toDate))."'";
			}else{
				$date="";
			}

			if($company){
				$companycond=" and a.comapny_id = '".$company."'";
			}else{
				$companycond="";
			}

			$rows=array();
			$sql = $this->db->query("
				SELECT a.ID,a.COMAPNY_ID, b.ID,B.MST_ID,b.MONTH_ID,b.DATE_CALC,b.DAY_STATUS,b.NO_OF_LINE,b.CAPACITY_MIN,b.CAPACITY_PCS from  lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id $companycond $date order by b.date_calc");
			foreach( $sql->result() as $row){
				if($type==1){
					if(isset($data[$row->DATE_CALC])){
						$data[$row->DATE_CALC]+=$row->CAPACITY_PCS;
					}else{
						$data[$row->DATE_CALC]=$row->CAPACITY_PCS;
					}
				}
				if($type==2){
					$date = new DateTime($row->DATE_CALC);
					$week = $date->format("W");
					if(isset($data[$week])){
						$data[$week]+=$row->CAPACITY_PCS;
					}else{
						$data[$week]=$row->CAPACITY_PCS;
					}
				}
				if($type==3){
					if(isset($data[date("M-Y",strtotime($row->DATE_CALC))])){
						$data[date("M-Y",strtotime($row->DATE_CALC))]+=$row->CAPACITY_PCS;
					}else{
						$data[date("M-Y",strtotime($row->DATE_CALC))]=$row->CAPACITY_PCS;
					}
				}
			}
			$alocation=$this->get_alocation_data_info($fromDate,$toDate,$company,$type);
			return array("Capacity"=>$data,"Alocation"=>$alocation);
		  //return array("Capacity"=>$data);
		} 
		function get_alocation_data_info($fromDate,$toDate,$company,$type)
		{
			if($fromDate && $toDate){
				$date=" and b.date_name between '".date('d-M-Y',strtotime($fromDate))."' and '".date('d-M-Y',strtotime($toDate))."'";
			}else{
				$date="";
			}

			if($company){
				$companycond=" and a.company_id = '".$company."'";
			}else{
				$companycond="";
			}

			$sql = $this->db->query("
				SELECT a.ID,a.COMPANY_ID, b.DATE_NAME,b.QTY,b.SMV from  ppl_order_allocation_mst a, ppl_order_allocation_dtls b where a.id=b.mst_id $companycond $date order by b.date_name");
			$data=array();
			foreach( $sql->result() as $row){
				if($type==1){
					if(isset($data[$row->DATE_NAME])){
						$data[$row->DATE_NAME]+=$row->QTY;
					}else{
						$data[$row->DATE_NAME]=$row->QTY;
					}
				}
				if($type==2){
					$date = new DateTime($row->DATE_NAME);
					$week = $date->format("W");
					if(isset($data[$week])){
						$data[$week]+=$row->QTY;
					}else{
						$data[$week]=$row->QTY;
					}
				}
				if($type==3){
					if(isset($data[date("M-Y",strtotime($row->DATE_NAME))])){
						$data[date("M-Y",strtotime($row->DATE_NAME))]+=$row->QTY;
					}else{
						$data[date("M-Y",strtotime($row->DATE_NAME))]=$row->QTY;
					}
				}
			}
			return $data;
		}
	}
>>>>>>> 0096f7ce6e6585bea95fba5b4f1816aac7c71afb
