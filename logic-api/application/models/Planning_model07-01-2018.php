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
        $result = $this->db->get_where($tableName, $attribute)->row();
        if (!empty($result)):
            return $result->{$fieldName};
        else:
            return false;
        endif;
    }

    public function login($user_id, $password) {
        $this->db->select("USER_PASSWD.*");
        $this->db->from("USER_PASSWD");
        $this->db->where("USER_NAME", "$user_id");
     // $this->db->where("PASSWORD", "$password");
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            $user_info = $query->row();

            return $this->get_menu_by_privilege($user_info->ID);
        } else {
            return false;
        }
    }

    public function get_menu_by_privilege($user_id) {
        $comp_sql = "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name";
        $buyer_sql = "select a.id,a.buyer_name,short_name,b.tag_company company_id from lib_buyer a,lib_buyer_tag_company b where a.id=b.buyer_id  and a.status_active=1 and a.is_deleted=0";
        $loc_sql = "select ID,LOCATION_NAME,COMPANY_ID from lib_location where status_active =1 and is_deleted=0 order by location_name";
        $floor_sql = "select ID,FLOOR_NAME,LOCATION_ID,COMPANY_ID from  lib_prod_floor where  production_process=5 and status_active =1 and is_deleted=0 order by floor_serial_no";
        $line_sql = "select id,line_name from lib_sewing_line where status_active= 1 and is_deleted = 0 order by sewing_line_serial ";
        $com_res = $this->db->query($comp_sql)->result();
        $buyer_res = $this->db->query($buyer_sql)->result();
        $loc_res = $this->db->query($loc_sql)->result();
        $floor_res = $this->db->query($floor_sql)->result();
        $line_res = $this->db->query($line_sql)->result();
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
        $data_arr['company_info'] = $com_res;
        $data_arr['buyer_info'] = $buyer_res;
        $data_arr['location_info'] = $loc_res;
        $data_arr['floor_info'] = $floor_res;
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

    function get_job_data_info($cbo_company_mst, $cbo_buyer_name = "0", $chk_job_wo_po = "0", $txt_date_from, $txt_date_to, $garments_nature = "", $txt_job_prifix = "", $cbo_year_selection, $cbo_string_search_type = "", $txt_order_search = "", $cbo_date_type,$plan_level="0") {
        $data_array = array();

        //$shipment_date = $company = $buyer = $job_cond = $order_cond = '';
        $company = " and a.company_name='$cbo_company_mst'";
        if ($cbo_buyer_name)
            $buyer = " and a.buyer_name='$cbo_buyer_name'";
        else
            $buyer = "";
        if ($txt_order_search)
            $order_cond = " and b.po_number like '%$txt_order_search%'  ";
        else
            $order_cond = "";
        $year_cond = " and to_char(a.insert_date,'YYYY')=$cbo_year_selection";

        if ($txt_date_from != "" && $txt_date_to != "")
            $shipment_date = "and b.pub_shipment_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
        else
            $shipment_date = "";

        if ($cbo_date_type == 1) { // Shipment Date
            if ($txt_date_from != "" && $txt_date_to != "")
                $tna_date_cond = "and shipment_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
            else
                $tna_date_cond = "";
        }
        else {
            if ($txt_date_from != "" && $txt_date_to != "")
                $tna_date_cond = "and task_start_date between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
            else
                $tna_date_cond = "";
        }

        $order_cond = "";
        $job_cond = "";

        if ($cbo_string_search_type == 1) {
            if (str_replace("'", "", $txt_job_prifix) != "")
                $job_cond = " and a.job_no='$txt_job_prifix'";
            if (trim($txt_order_search) != "")
                $order_cond = " and b.po_number='$txt_order_search'  "; //else  $order_cond=""; 
        }
        else if ($cbo_string_search_type == 4 || $cbo_string_search_type == 0) {
            if (str_replace("'", "", $txt_job_prifix) != "")
                $job_cond = " and a.job_no like '%$txt_job_prifix%' "; //else  $job_cond=""; 
            if (trim($txt_order_search) != "")
                $order_cond = " and b.po_number like '%$txt_order_search%'  ";
        }

        else if ($cbo_string_search_type == 2) {
            if (str_replace("'", "", $txt_job_prifix) != "")
                $job_cond = " and a.job_no like '$txt_job_prifix%' "; //else  $job_cond=""; 
            if (trim($txt_order_search) != "")
                $order_cond = " and b.po_number like '$txt_order_search%'  ";
        }

        else if ($cbo_string_search_type == 3) {
            if (str_replace("'", "", $txt_job_prifix) != "")
                $job_cond = " and a.job_no like '%$txt_job_prifix'  "; //else  $job_cond=""; 
            if (trim($txt_order_search) != "")
                $order_cond = " and b.po_number like '%$txt_order_search'  ";
        }
        if (str_replace("'", "", $txt_job_prifix) != "")
            $tna_job = " and job_no like '%" . $txt_job_prifix . "%'";
        else
            $tna_job = "";
     // echo "select min(task_start_date) as task_start_date,max(task_finish_date) as task_finish_date,po_number_id from tna_process_mst where is_deleted=0 and status_active=1 $tna_job $tna_date_cond and task_number=86 group by po_number_id"; die;
        $sql = $this->db->query("select min(task_start_date) as task_start_date,max(task_finish_date) as task_finish_date,po_number_id from tna_process_mst where is_deleted=0 and status_active=1 $tna_job $tna_date_cond and task_number=86 group by po_number_id");

        $sel_pos = "";
        foreach ($sql->result() as $srows) {
            $tna_task_data[$srows->PO_NUMBER_ID]['task_start_date'] = date("d-m-Y", strtotime($srows->TASK_START_DATE));
            $tna_task_data[$srows->PO_NUMBER_ID]['task_finish_date'] = date("d-m-Y", strtotime($srows->TASK_FINISH_DATE));

            if ($sel_pos == "")
                $sel_pos = $srows->PO_NUMBER_ID;
            else
                $sel_pos .= "," . $srows->PO_NUMBER_ID;
            //if($sel_pos=="") $sel_pos=$srows[csf("po_number_id")]; else $sel_pos .=",".$srows[csf("po_number_id")];
        }

        if ($sel_pos == "") {
            return $data_array;
            die;
        }

        // FOR ORACLE 
        $sel_pos2 = array_chunk(array_unique(explode(",", $sel_pos)), 999);

        $sql = "select b.plan_qnty,b.po_break_down_id,b.item_number_id from ppl_sewing_plan_board a, ppl_sewing_plan_board_powise b where a.plan_id=b.plan_id  and ";

        $p = 1;
        foreach ($sel_pos2 as $job_no_process) {
            if ($p == 1)
                $sql .= " (b.po_break_down_id in(" . implode(',', $job_no_process) . ")";
            else
                $sql .= " or b.po_break_down_id in(" . implode(',', $job_no_process) . ")";
            $p++;
        }
        $sql .= ")";
        //
       // echo $sql; die;
        $sql = $this->db->query($sql);
        $planned_qnty = array();
        if ($sql->num_rows() > 0) {
            foreach ($sql->result() as $srows) {
                if (isset($planned_qnty[$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID])) {
                    $planned_qnty[$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID] += $srows->PLAN_QNTY;
                } else {
                    $planned_qnty[$srows->PO_BREAK_DOWN_ID][$srows->ITEM_NUMBER_ID] = $srows->PLAN_QNTY;
                }
            }
        }
		// print_r($planned_qnty);die;
        // return $planned_qnty;

        $com_res = $this->db->query("select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name")->result();
        $buyer_res = $this->db->query("select a.id,a.buyer_name from  lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id  and a.status_active=1 and a.is_deleted=0")->result();
        $garment_res = $this->db->query("select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name")->result();

        foreach ($com_res as $value) {
            $comp[$value->ID] = $value->COMPANY_NAME;
        }
        foreach ($buyer_res as $value) {
            $buyer_arr[$value->ID] = $value->BUYER_NAME;
        }
        foreach ($garment_res as $value) {
            $garments_item[$value->ID] = $value->ITEM_NAME;
        }
        $item_category = array(1 => "Yarn",
            2 => "Knit Finish Fabrics",
            3 => "Woven Fabrics",
            4 => "Accessories",
            5 => "Chemicals",
            6 => "Dyes",
            7 => "Auxilary Chemicals",
            8 => "Spare Parts",
            9 => "Machinaries",
            10 => "Other Capital Items",
            11 => "Stationaries",
            12 => "Services - Fabric",
            13 => 'Grey Fabric(Knit)',
            14 => 'Grey Fabric(woven)',
            15 => 'Electrical',
            16 => 'Maintenance',
            17 => 'Medical',
            18 => 'ICT Equipment', // previous in ICT
            19 => 'Print & Publication',
            20 => 'Utilities & Lubricants',
            21 => 'Construction Materials',
            22 => 'Printing Chemicals & Dyes', //Chemicals & Dyes in spinning
            23 => 'Dyes Chemicals & Auxilary Chemicals', // previous 22 in spinning
            24 => 'Services - Yarn Dyeing ',
            25 => 'Services - Embellishment',
            28 => 'Cut Panel',
            30 => 'Garments',
            31 => 'Services Lab Test',
            32 => 'Vehicle Components',
            33 => 'Others',
            34 => 'Painting Goods',
            35 => 'Plumbing and Sanitary Goods',
            36 => 'Safety and Security',
            37 => 'Food and Grocery',
            38 => 'Needles',
            39 => 'WTP and ETP Machinery', //previous ETP in 3rdversion3.1
            40 => 'Spare Parts - Mechanical', // previous 9  in spinning
            41 => 'Spare Parts - Electrical', // previous 15 in spinning
            42 => 'Cotton', // previous 34 in spinning
            43 => 'Synthetic Fibre', // previous 35  in spinning
            44 => 'Packing Materials', // previous 36  in spinning
            45 => 'Factory Machinery', // new add
            46 => 'Iron Dril Machinery Machinery', // new add
            47 => 'Felt Machinery', // new add
            48 => 'Dosing Motor Pump', // new add
            49 => 'Centrifugal Water Pump', // new add
            50 => 'Flack Machinery', // new add
            51 => 'Bag Sewing Machine', // new add
            52 => 'Batter Cabinet', // new add
            53 => 'TV', // new add
            54 => 'Finishing Machinery', // new add
            55 => 'Compresser Machinery', // new add
            56 => 'Sewing Machinery', // new add
            57 => 'Embroidery Machinery', // new add
            58 => 'Washing Machinery', // new add
            59 => 'Cutting Machinery', // new add
            60 => 'Knitting Machinery', // new add
            61 => 'Printing Machinery', // new add
            62 => 'Laboratory Machinery', // new add
            63 => 'PMD Machinery', // new add
            64 => 'Dyeing Machinery', // new add
            65 => 'Oil and Gas Generator', // new add
            66 => 'Fabric Spreader Machinery', // new add
            67 => 'Consumable', // new add
            68 => 'ICT Consumable',
            69 => 'Furniture', // new add
            70 => 'Fixture', // new add
            71 => 'Service Knitting', // new add
            72 => 'Service Dyeing', // new add
            73 => 'Service Heat Setting', // new add
            74 => 'Service All Over Print', // new add
            75 => 'Service Squeezing', // new add
            76 => 'Service Stentering', // new add
            77 => 'Service Open Compacting', // new add
            78 => 'Service Singeing', // new add
            79 => 'Service Fabric Finishing', // new add
            80 => 'Blow Room',
            81 => 'Carding',
            82 => 'Draw Frame',
            83 => 'Lap Former',
            84 => 'Comber',
            85 => 'Simplex',
            86 => 'Ring',
            87 => 'Autocone',
            88 => 'Conditioning',
            89 => 'AC Plant',
            90 => 'Chiller',
            91 => 'Substation',
            92 => 'Pump',
            93 => 'Cooling Tower',
            94 => 'Vehicle', // new add
            95 => 'Fabric Sales Order' // new add
        );
$arr = array(2 => $comp, 3 => $buyer_arr, 9 => $item_category);
$garments_nature = 2;

$sql = "select po_break_down_id,sum(production_quantity) as production_quantity from   pro_garments_production_mst where production_type=5 ";
$p = 1;
        foreach ($sel_pos2 as $job_no_process) {
            if ($p == 1)
                $sql .= "  and (po_break_down_id in(" . implode(',', $job_no_process) . ")";
            else
                $sql .= " or po_break_down_id in(" . implode(',', $job_no_process) . ")";
            $p++;
        }
        $sql .= ")";
$sql .= "	  and status_active=1 and is_deleted=0   group by po_break_down_id	 ";
 //echo $sql; die;
 
$sql_data = $this->db->query($sql)->result();
$k = 0;

foreach ($sql_data as $rows) {
    $production_details[$rows->PO_BREAK_DOWN_ID] = $rows->PRODUCTION_QUANTITY;
}

        //Oracle queary 

$sel_pos2 = array_chunk(array_unique(explode(",", $sel_pos)), 999);

$sql = " select day_target,po_dtls_id,gmts_item_id from  ppl_gsd_entry_mst where ";

$p = 1;
foreach ($sel_pos2 as $job_no_process) {
    if ($p == 1)
        $sql .= " (po_dtls_id in(" . implode(',', $job_no_process) . ")";
    else
        $sql .= " or po_dtls_id in(" . implode(',', $job_no_process) . ")";
    $p++;
}
$sql .= ")";

$sql .= " ";

        //echo $sql;
$sql = $this->db->query($sql)->result();

foreach ($sql as $srows) {
    $day_target[$srows->PO_DTLS_ID][$srows->GMTS_ITEM_ID] = $srows->DAY_TARGET;
}
$str_shi = '';
if ($chk_job_wo_po != 0)
    $str_shi = " and b.shiping_status!=3 ";

        // Oracle query
$sel_pos = array_chunk(array_unique(explode(",", $sel_pos)), 999);

if($plan_level == 1){
    $sql = "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.id PO_BREAK_DOWN_ID,b.po_number, b.po_quantity,b.shipment_date as shipment_date,b.pub_shipment_date as pub_shipment_date,a.garments_nature, b.plan_cut, to_char(a.insert_date,'YYYY') as year,b.id,set_break_down,total_set_qnty, set_smv from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$garments_nature and a.status_active=1 and b.status_active=1 $str_shi $company $buyer $job_cond $order_cond";            
}else{
    if($plan_level == 2){
        $fields = ",c.color_number_id";
        $group_by = ",c.color_number_id";
    }else if($plan_level == 3){
        $group_by = ",c.size_number_id";
        $fields = ",c.size_number_id";
    } else if($plan_level == 4){
        $fields = ",c.color_number_id,c.size_number_id";
        $group_by = ",c.color_number_id,c.size_number_id";
    } else if($plan_level == 5){
        $fields = ",c.country_id";
        $group_by = ",c.country_id";
    }
    else if($plan_level == 6){
        $fields = ",c.country_id,c.color_number_id";
        $group_by = ",c.country_id,c.color_number_id";
    }
    else if($plan_level == 7){
        $fields = ",c.country_id,c.color_number_id,c.size_number_id";
        $group_by = ",c.country_id,c.color_number_id,c.size_number_id";
    }
    else if($plan_level == 8){
        $fields = ",c.country_id,c.size_number_id";
        $group_by = ",c.country_id,c.size_number_id";
    }
    $sql = "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id PO_BREAK_DOWN_ID,b.po_number,b.po_quantity,b.shipment_date as shipment_date,b.pub_shipment_date as pub_shipment_date, a.garments_nature,b.plan_cut,to_char(a.insert_date,'YYYY') as year,b.id,set_break_down,total_set_qnty,set_smv, sum(c.plan_cut_qnty) plan_cut_qnty $fields from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.garments_nature=$garments_nature and a.status_active=1 $str_shi $company $buyer $job_cond $order_cond";            
}

$p = 1;
foreach ($sel_pos as $job_no_process) {
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
    //echo $sql; die;
$sql_exe = $this->db->query($sql)->result();
$i = 0;

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
		$data_array[$i]["PRODUCTION_QNTY"] =0;// $this->get_production_qnty_by_po_item($rows->PO_BREAK_DOWN_ID,$setdata[0],$plan_level);

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
        $i++;
    }
}
return $data_array;
}
function get_production_qnty_by_po_item($po_id,$item_id,$plan_level){
    $po_id_cond =$item_cond= "";
    if($po_id != "")
    {
        $po_id_cond = " and po_break_down_id=$po_id";
    }
	if($item_id != "")
    {
        $item_cond = " and a.ITEM_NUMBER_ID=$item_id";
    }
	
    $color_size_sql="select ID,JOB_NO_MST,PO_BREAK_DOWN_ID,ITEM_NUMBER_ID,COLOR_NUMBER_ID,SIZE_NUMBER_ID,COUNTRY_ID from wo_po_color_size_breakdown where po_break_down_id='$po_id'";
    $color_size_data=$this->db->query($color_size_sql)->result();
    $color_size_data_arr = array();
    foreach($color_size_data as $row)
    {
        $color_size_data_arr[$row->JOB_NO_MST][$row->PO_BREAK_DOWN_ID][$row->ITEM_NUMBER_ID][$row->COLOR_NUMBER_ID][$row->SIZE_NUMBER_ID][]=$row->ID;
    }
    //$production_data_arr=array();
	$production_data_arr=0;
    $production_sql="select a.po_break_down_id,b.color_size_break_down_id,B.PRODUCTION_QNTY production_quantity,a.ITEM_NUMBER_ID from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=5 $po_id_cond and a.status_active=1 and a.is_deleted=0  $item_cond order by a.po_break_down_id";
	$production_data=$this->db->query($production_sql)->result();
	foreach($production_data as $row)
	{
		///$production_data_arr[$row->SEWING_LINE][$row->color_size_break_down_id]=$row->PRODUCTION_QUANTITY;
		$production_data_arr+=$row->PRODUCTION_QUANTITY;
	}
 
    return $production_data_arr;
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
    }
    $production_data_arr=array();
    if($resource_allocation_type != 1)
    {
        $production_sql="select a.id,a.po_break_down_id,a.production_date,a.sewing_line,a.company_id,a.location,b.color_size_break_down_id,sum(B.PRODUCTION_QNTY) production_quantity from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=5 $po_id_cond and a.status_active=1 and a.is_deleted=0   and a.sewing_line in (".implode(",",$line_names_ids).") group by a.id,a.production_date,a.po_break_down_id, a.sewing_line,a.company_id,a.location,b.color_size_break_down_id order by a.sewing_line,a.po_break_down_id, a.production_date";
        $production_data=$this->db->query($production_sql)->result();
        foreach($production_data as $row)
        {
            $production_data_arr[$row->SEWING_LINE][$row->color_size_break_down_id]=$row->PRODUCTION_QUANTITY;
        }
    }
    else
    {
        $production_sql="select A.ID,A.PO_BREAK_DOWN_ID,A.PRODUCTION_DATE,A.SEWING_LINE,A.COMPANY_ID,A.LOCATION,B.COLOR_SIZE_BREAK_DOWN_ID,SUM(B.PRODUCTION_QNTY) PRODUCTION_QUANTITY from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=5 $po_id_cond and a.status_active=1 and a.is_deleted=0 and a.sewing_line in (".implode(",",$line_allocated).") group by a.id,a.production_date,a.po_break_down_id, a.sewing_line,a.company_id,a.location,b.color_size_break_down_id order by a.sewing_line,a.po_break_down_id, a.production_date";
        $production_data=$this->db->query($production_sql)->result();
        foreach($production_data as $row)
        {
            $production_data_arr[$row->SEWING_LINE][$row->COLOR_SIZE_BREAK_DOWN_ID] = $row->PRODUCTION_QUANTITY;
        }
    }

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

function get_plan_data_info($company_id,$location_id,$floor_id,$txt_date_from) 
{
    $resource_allocation_type_sql=$this->db->query("select auto_update from variable_settings_production where company_name='$company_id' and variable_list=23")->row();
    $resource_allocation_type=$resource_allocation_type_sql->AUTO_UPDATE;
    if( $resource_allocation_type==1 )
    {
        $new_line_res="select id,line_number from prod_resource_mst where company_id='$company_id' and location_id='$location_id' and floor_id='$floor_id' and is_deleted=0 order by id ";
        $new_line_res=$this->db->query($new_line_res)->result();
        $sewing_resource=array();
        foreach($new_line_res as $value)
        {
            $sewing_resource[$value->ID] = $value->LINE_NUMBER;
            $res_all[$value->LINE_NUMBER]=$value->ID;
            $line_allocated[$value->ID]=$value->ID;
        }
    }

    $sql_line="select id,line_name from lib_sewing_line where company_name='$company_id' and location_name='$location_id' and floor_name='$floor_id' order by sewing_line_serial";
    $new_line_resource= $this->db->query($sql_line)->result();
    foreach($new_line_resource as $ids=>$vals)
        $line_names_ids[$vals->ID]=$vals->ID;


    $company_cond = " and company_id='$company_id'";
    $po_id_cond = "";
    /*if($po_id != "")
    {
        $po_id_cond = " and po_break_down_id='$po_id'";
    }*/
    $from_date=date("Y-m-d", strtotime($txt_date_from));

    $days_forward=120;
    function add_date($orgDate,$days)
    {
        $cd = strtotime($orgDate);
        $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$days,date('Y',$cd)));
        return $retDAY;
    }

    $to_date=add_date($from_date,$days_forward);


    if(count($line_names_ids)<1) { echo "Please use Resource Allocation for Line."; die;}
        //$plan_sql="select id,line_id,po_break_down_id,plan_id,start_date,start_hour,end_date,end_hour,duration,plan_qnty,comp_level,first_day_output,increment_qty,terget,day_wise_plan,company_id,location_id,item_number_id,off_day_plan,order_complexity,ship_date,extra_param from ppl_sewing_plan_board where company_id='$company_id' $po_id_cond  and line_id in ( ".implode(",",$line_names_ids)." ) and (start_date between TO_DATE('".$from_date."','YYYY-MM-DD')  and TO_DATE('".$to_date."','YYYY-MM-DD')   or  end_date between TO_DATE('".$from_date."','YYYY-MM-DD')  and TO_DATE('".$to_date."','YYYY-MM-DD')  or ( start_date < TO_DATE('".$from_date."','YYYY-MM-DD')  and end_date> TO_DATE('".$to_date."','YYYY-MM-DD'))) order by po_break_down_id";
    $plan_sql="select A.ID,A.LINE_ID,A.PLAN_ID,A.START_DATE,A.START_HOUR,A.END_DATE,A.END_HOUR,A.DURATION,A.PLAN_QNTY,A.COMP_LEVEL,A.FIRST_DAY_OUTPUT, A.INCREMENT_QTY,A.TERGET,A.DAY_WISE_PLAN,A.COMPANY_ID,A.LOCATION_ID,A.OFF_DAY_PLAN,A.ORDER_COMPLEXITY,A.SHIP_DATE, EXTRA_PARAM,A.PLAN_LEVEL,A.FIRST_DAY_CAPACITY,A.LAST_DAY_CAPACITY,A.SEQ_NO,A.PO_COMPANY_ID,A.USE_LEARNING_CURVE,A.CURRENT_PRODUCTION_DATE,A.PRODUCTION_PERCENT, A.TOP_BORDER_COLOR,A.BOTTOM_BORDER_COLOR,A.LEFT_COLOR,A.RIGHT_COLOR,LISTAGG(D.JOB_NO, ',') WITHIN GROUP (ORDER BY D.JOB_NO) AS JOB_NO,LISTAGG(B.PO_BREAK_DOWN_ID, ',') WITHIN GROUP (ORDER BY B.PO_BREAK_DOWN_ID) AS PO_BREAK_DOWN_ID,LISTAGG(B.ITEM_NUMBER_ID, ',') WITHIN GROUP (ORDER BY B.ITEM_NUMBER_ID) AS ITEM_NUMBER_ID, LISTAGG(B.SIZE_NUMBER_ID, ',') WITHIN GROUP (ORDER BY B.SIZE_NUMBER_ID) AS SIZE_NUMBER_ID, LISTAGG(B.COLOR_NUMBER_ID, ',') WITHIN GROUP (ORDER BY B.COLOR_NUMBER_ID) AS COLOR_NUMBER_ID, LISTAGG(B.COUNTRY_ID, ',') WITHIN GROUP (ORDER BY B.COUNTRY_ID) AS COUNTRY_ID,C.PO_NUMBER,D.STYLE_REF_NO,D.BUYER_NAME from ppl_sewing_plan_board a,ppl_sewing_plan_board_powise b,wo_po_break_down c,wo_po_details_master d where a.plan_id=b.plan_id and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.company_id='$company_id' $po_id_cond and a.line_id in( ".implode(",",$line_names_ids).") and (a.start_date between TO_DATE('".$from_date."','YYYY-MM-DD')  and TO_DATE('".$to_date."','YYYY-MM-DD')   or a.end_date between TO_DATE('".$from_date."','YYYY-MM-DD')  and TO_DATE('".$to_date."','YYYY-MM-DD')  or ( a.start_date < TO_DATE('".$from_date."','YYYY-MM-DD')  and a.end_date> TO_DATE('".$to_date."','YYYY-MM-DD'))) group by a.id,a.line_id,a.plan_id,a.start_date,a.start_hour,a.end_date,a.end_hour,a.duration, a.plan_qnty,a.comp_level,a.first_day_output, a.increment_qty,a.terget,a.day_wise_plan,a.company_id,a.location_id,a.item_number_id, a.off_day_plan,a.order_complexity,a.ship_date, extra_param,a.plan_level,a.first_day_capacity,a.last_day_capacity,a.seq_no,a.po_company_id,a.use_learning_curve,a.current_production_date,a.production_percent, a.top_border_color,a.bottom_border_color,a.left_color,a.right_color,c.po_number,d.style_ref_no,d.buyer_name";

    $plan_data= $this->db->query($plan_sql)->result();
    $com_res = $this->db->query("select id,company_name from lib_company where status_active =1 and is_deleted=0  order by company_name")->result();

    foreach ($com_res as $value)
    {
        $comp[$value->ID] = $value->COMPANY_NAME;
    }

    $location_res = $this->db->query("select id,location_name from lib_location  where status_active =1 and is_deleted=0  order by location_name")->result();

    foreach ($location_res as $value) 
    {
        $location_arr[$value->ID] = $value->LOCATION_NAME;
    }
    $garment_res = $this->db->query("select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name")->result();

    foreach ($garment_res as $value) 
    {
        $garments_item[$value->ID] = $value->ITEM_NAME;
    }

    $i = 0;
    foreach ($plan_data as $rows) 
    {
        $plan_level = $rows->PLAN_LEVEL;
        $color_name = $this->get_field_value_by_attribute("LIB_COLOR", "COLOR_NAME", array("ID"=>$rows->COLOR_NUMBER_ID));
        $size_name  = $this->get_field_value_by_attribute("LIB_SIZE", "SIZE_NAME", array("ID"=>$rows->SIZE_NUMBER_ID));
        $buyer_name  = $this->get_field_value_by_attribute("LIB_BUYER", "BUYER_NAME", array("ID"=>$rows->BUYER_NAME));
        $production_qnty = $this->get_production_qnty_info_by_plan_level($rows->COMPANY_ID,$rows->JOB_NO,$rows->PO_BREAK_DOWN_ID,$rows->ITEM_NUMBER_ID,$plan_level,$rows->COLOR_NUMBER_ID,$rows->SIZE_NUMBER_ID,$resource_allocation_type,$rows->LINE_ID,$line_names_ids,$line_allocated);

        $data_array[$i]["COLOR_NUMBER_ID"]  = $rows->COLOR_NUMBER_ID;
        $data_array[$i]["COLOR_NUMBER"]  = (!empty($color_name))?$color_name:"";
        $data_array[$i]["SIZE_NUMBER_ID"]  = $rows->SIZE_NUMBER_ID;
        $data_array[$i]["SIZE_NUMBER"]  = (!empty($size_name))?$size_name:"";
        $data_array[$i]["JOB_NO"] = $rows->JOB_NO;
        $data_array[$i]["PO_COMPANY_ID"] = $rows->PO_COMPANY_ID;
        $data_array[$i]["STYLE_REF_NO"] = $rows->STYLE_REF_NO;
        $data_array[$i]["BUYER_NAME"] = $buyer_name;
        $data_array[$i]["PO_BREAK_DOWN_ID"] = $rows->PO_BREAK_DOWN_ID;
        $data_array[$i]["PO_NUMBER"] = $rows->PO_NUMBER;        
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
		$data_array[$i]["CURRENT_PRODUCTION_DATE"] =  date("d-m-Y", strtotime("17-Dec-2017"));///$rows->CURRENT_PRODUCTION_DATE;
		$data_array[$i]["PRODUCTION_PERCENT"] ="50%";// $rows->PRODUCTION_PERCENT; 
		
		$data_array[$i]["TOP_BORDER_COLOR"] =  "#339900";// $rows->TOP_BORDER_COLOR; //"#339900";//  Reserved for future use
		//if($production_qnty>0)
			//$data_array[$i]["BOTTOM_BORDER_COLOR"] = "#339900";//$rows->BOTTOM_BORDER_COLOR;
		//else
			$data_array[$i]["BOTTOM_BORDER_COLOR"] =  "#339900";//$rows->BOTTOM_BORDER_COLOR;
		 //date("d-m-Y", strtotime($rows->SHIP_DATE));
		  //date("d-m-Y", strtotime($rows->END_DATE));
		  $start = strtotime($rows->SHIP_DATE);
          $end = strtotime($rows->END_DATE);
          $days_between = ceil(abs($end - $start) / 86400);
		 
		$data_array[$i]["LEFT_COLOR"] = "#73CAD5";//$rows->LEFT_COLOR;
		$data_array[$i]["RIGHT_COLOR"] = "#FF0000";//$rows->RIGHT_COLOR;
		$data_array[$i]["LEFT_PERCENT"] = 80;//$rows->LEFT_COLOR;
		$data_array[$i]["RIGHT_PERCENT"] = 20;//$rows->RIGHT_COLOR;
		
		
        //if(isset($production_data_arr[$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID])){
                //$data_array[$i]["production_quantity"]  = $production_data_arr[$rows->PO_BREAK_DOWN_ID][$rows->LINE_ID];
            //$data_array[$i]["production_quantity"]  = $production_qnty;
        //}
        $data_array[$i]["PRODUCTION_QNTY"]  = $production_qnty;
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
    $daywise_sql="select a.po_break_down_id,a.line_id,a.start_date,a.end_date,a.plan_qnty,a.first_day_output,a.increment_qty,a.terget ,a.company_id, a.location_id,a.item_number_id ,a.off_day_plan,a.order_complexity,b.plan_date,b.plan_qnty,b.plan_id  from ppl_sewing_plan_board a,ppl_sewing_plan_board_dtls b where a.plan_id=b.plan_id and a.is_deleted=0 and a.status_active=1 $po_id_cond $company_cond $plan_date";
        //echo $daywise_sql;
    $daywise_plan = $this->db->query($daywise_sql)->result();

    $com_res = $this->db->query("select id,company_name from lib_company where status_active =1 and is_deleted=0  order by company_name")->result();

    foreach ($com_res as $value) 
    {
        $comp[$value->ID] = $value->COMPANY_NAME;
    }
    $location_res = $this->db->query("select id,location_name from lib_location  where status_active =1 and is_deleted=0  order by location_name")->result();

    foreach ($location_res as $value) 
    {
        $location_arr[$value->ID] = $value->LOCATION_NAME;
    }


    $garment_res = $this->db->query("select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name")->result();

    foreach ($garment_res as $value) 
    {
        $garments_item[$value->ID] = $value->ITEM_NAME;
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
    $resource_allocation_type_sql=$this->db->query("select auto_update from variable_settings_production where company_name='$company_id' and variable_list=23 ")->row();
    $resource_allocation_type=1;
    $company_cond = " and company_id='$company_id'";
    if(($location_id*1)!="0"){$location_cond=" and location_name='$location_id'";} else{ $location_cond="";}

    if(($floor_id*1)!="0") $floor_cond = " and floor_name='$floor_id'"; else {$floor_cond = "";}
    if(($floor_id*1) !="0")$floor_cond_sewing = " and floor_name='$floor_id'"; else {$floor_cond_sewing = "";}
    $line_res=$this->db->query("select id line_id,line_name LINE_NAME,sewing_line_serial from lib_sewing_line where company_name='$company_id' $location_cond  $floor_cond_sewing")->result();
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
        $company_cond = " and comapny_id='$company_id'";
        if ($txt_date_from != "" && $txt_date_to != "")
        {
            $date_calc = "and date_calc between '" . date("d-M-Y", strtotime($txt_date_from)) . "' and '" . date("d-M-Y", strtotime($txt_date_to)) . "'";
        }
        else
        { 
            $date_calc = "";
        }
        $sql="select a.mst_id,a.month_id,to_char(a.date_calc,'DD-MM-YYYY') date_calc,case when a.day_status = 2 then 'Closed' else 'Open' end as day_status,comapny_id,capacity_source,location_id from lib_capacity_calc_dtls a, lib_capacity_calc_mst b where b.id=a.mst_id $company_cond $date_calc and day_status=2";
        $sql_data= $this->db->query($sql)->result();
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
        $po_cond = " and po_number_id='$po_id'";
        $sql="select min(task_start_date) as task_start_date, max(task_finish_date) as task_finish_date, po_number_id  from tna_process_mst where is_deleted=0 and status_active=1  $po_cond and task_number=86   group by po_number_id ";
        $sql_data= $this->db->query($sql)->result();
        $sel_pos="";
        $i=0;
        foreach($sql_data as $row)
        {

            $tna_task_data[$i]['PO_NUMBER_ID']=$row->PO_NUMBER_ID;
            $tna_task_data[$i]['TASK_START_DATE']= date("d-m-Y", strtotime($row->TASK_START_DATE)) ;
            $tna_task_data[$i]['TASK_FINISH_DATE']=date("d-m-Y", strtotime($row->TASK_FINISH_DATE)) ;

            if($sel_pos=="") $sel_pos=$row->PO_NUMBER_ID; else $sel_pos .=",".$row->PO_NUMBER_ID;
            $i++;
        }
        if( $sel_pos=="" )
        {
            //return "Sorry! No PO found for planning in TNA process.";
            return array('errorMsg' => 'Sorry! No PO found for planning in TNA process.');
        } 
        return $tna_task_data;

    }

    function get_production_data_info($company_id,$po_id)
    {
        $company_cond = " and company_id='$company_id'";
        $po_cond = " and po_break_down_id='$po_id'";
        $comp_sql = "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name";
        $loc_sql = "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name";
        $line_sql = "select id,line_name from lib_sewing_line where status_active= 1 and is_deleted = 0 order by sewing_line_serial ";
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

        $production_sql="select po_break_down_id,sum(production_quantity) as production_quantity,production_date,sewing_line,company_id,location from   pro_garments_production_mst where production_type=5 $po_cond  $company_cond  and status_active=1 and is_deleted=0     group by production_date,po_break_down_id,sewing_line, company_id,location order by sewing_line,po_break_down_id,production_date";
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
            foreach ($response_obj->SewingPlanBoard as $sewing_plan_row) {
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
                    'PO_COMPANY_ID'     => $sewing_plan_row->PO_COMPANY_ID
                );
                'FIRST_DAY_CAPACITY,LAST_DAY_CAPACITY,SEQ_NO,PO_COMPANY_ID';
                if($sewing_plan_row->RowState == "add"){                    
                    $ppl_sewing_plan_board_data['PLAN_ID'] = $plan_id;
                    $ppl_sewing_plan_board_data['ID'] = $max_id++;
                    $ppl_sewing_plan_board_data['INSERTED_BY']   = $sewing_plan_row->INSERTED_BY;
                    $ppl_sewing_plan_board_data['INSERT_DATE']   = date("d-M-Y");
                    $this->insertData($ppl_sewing_plan_board_data, "PPL_SEWING_PLAN_BOARD");
                    $plan_ids[$sewing_plan_row->PLAN_ID] = $plan_id;
                }else if($sewing_plan_row->RowState == "update"){
                    $plan_ids[$sewing_plan_row->PLAN_ID] = $sewing_plan_row->PLAN_ID;
                    $ppl_sewing_plan_board_data['UPDATE_DATE']      = date("d-M-Y");
                    $ppl_sewing_plan_board_data['UPDATED_BY']       = $sewing_plan_row->UPDATED_BY;
                    $plan_to_delete .= $sewing_plan_row->PLAN_ID . ",";
                    $this->updateData('PPL_SEWING_PLAN_BOARD', $ppl_sewing_plan_board_data, array('PLAN_ID' => $sewing_plan_row->PLAN_ID));
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

            $max_plan_dtls_id = $this->get_max_value("PPL_SEWING_PLAN_BOARD_DTLS","ID") + 1;
            foreach ($response_obj->SewingPlanBoardDtls as $sewing_plan_dtls_row) {
                if($sewing_plan_dtls_row->RowState != "delete"){
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
SELECT a.id,a.comapny_id, b.id,b.mst_id,b.month_id,b.date_calc,b.day_status,b.no_of_line,b.capacity_min,b.capacity_pcs from  lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id $companycond $date order by b.date_calc");
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
		// $alocation=$this->get_alocation_data_info($fromDate,$toDate,$company,$type);
		 //return array("Capacity"=>$data,"Alocation"=>$alocation);
		  return array("Capacity"=>$data);
	} 
	function get_alocation_data_info($fromDate,$toDate,$company,$type)
	{
		if($fromDate && $toDate){
			 $date=" and b.date_name between '".date('d-M-Y',strtotime($fromDate))."' and '".date('d-M-Y',strtotime($toDate))."'";
		 }else{
			 $date="";
		 }
		 
		 if($company){
			 $companycond=" and a.comapny_id = '".$company."'";
		 }else{
			 $companycond="";
		 }
		 
		$sql = $this->db->query("
SELECT a.id,a.comapny_id, b.date_name,b.qty,b.smv from  ppl_order_allocation_mst a, ppl_order_allocation_dtls b where a.id=b.mst_id $companycond $date order by b.date_name");
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
