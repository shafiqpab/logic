<?php
include 'grade_class.php';
include 'observation_class.php';
include 'company_class.php';
include 'source_class.php';
include 'common_class.php';
include 'defect_class.php';
include 'inch_class.php';
include 'qc_dtls_class.php';

class Yarn_parking_model extends CI_Model
{

    function __construct()
    {
        error_reporting(0);

        parent::__construct();
    }

    /**
     * [get_max_value description]
     * @param  [string] $tableName [defining name of the table]
     * @param  [string] $fieldName [defining name of the table column]
     * @return [integer]            [return max value of the table column]
     */

    function writeFile($fileName, $txt)
    {
        $file = "objectData/" . $fileName . '_' . date('d-m-Y') . ".txt";
        $current = $txt . "\n.........." . date('d-m-Y h:i:s a', time()) . ".........\n\n";
        $myfile = fopen($file, "a");
        fwrite($myfile, $current);
        fclose($myfile);
    }


    function getDuration($user_id, $text)
    {
        $file = "note_url_script/objectData/user_activity_file_" . $user_id . ".txt";
        $myfile = fopen($file, "r");
        list($barcode, $time_stamp) = explode(',', fgets($myfile));
        $duration = time() - $time_stamp;
        fclose($myfile);

        $myfile = fopen($file, "w");
        fwrite($myfile, $text . ',' . time());
        fclose($myfile);
        $duration = ($barcode == $text) ? $duration : 50;
        return $duration;
    }






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
        if (($attribute * 1) > 0) {
            $query = $this->db->query('select ' . $tableName . '.' . $fieldName . ' from ' . $tableName . ' where id=' . $this->db->escape($attribute));
            $result = $query->row();
            if (!empty($result)) :
                return $result->{$fieldName};
            else :
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

    // ***********Akh Test ********
    function findAllRow($tableName)
    {
        return $this->db->get($tableName)->result();
    }

    function findByAttribute($tableName, $attribute)
    {
        return $this->db->get_where($tableName, $attribute)->row();
    }

    function getFieldsByAttribute($tableName, $fields, $attribute)
    {
        $this->db->select("$fields");
        return $this->db->get_where($tableName, $attribute)->row();
    }

    function getAllFieldsByAttribute($tableName, $fields, $attribute)
    {
        $this->db->select("$fields");
        return $this->db->get_where($tableName, $attribute)->result();
    }





    public function encrypt($string)
    {
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


    public function login($user_id, $password)
    { //print_r(4);die;
        // $string = 'select user_passwd.id AS "ID",user_passwd.password "PASSWORD" from user_passwd where user_name=' . $this->db->escape($user_id);
        // echo $string; die;
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

    public function get_menu_by_privilege($user_id)
    {
        $data_arr['financial_parameter'] = $this->get_financial_parameter_setup();
        $data_arr['supplier'] = $this->db->query("SELECT ID,SUPPLIER_NAME from LIB_SUPPLIER where STATUS_ACTIVE = 1 and IS_DELETED = 0")->result();
        $data_arr['sample_type'] = $this->db->query("SELECT ID,SUPPLIER_NAME from LIB_SUPPLIER where STATUS_ACTIVE = 1 and IS_DELETED = 0")->result();
        $user_credentials = "SELECT UNIT_ID, COMPANY_LOCATION_ID,IS_PLANNER FROM user_passwd Where ID='$user_id' ";
        $all_comp = 0;
        $all_loc = 0;
        foreach (sql_select($user_credentials) as $v) {
            $all_comp = $v->UNIT_ID;
            $all_loc = $v->COMPANY_LOCATION_ID;
            $is_planner = $v->IS_PLANNER;
        }

        $yarn_issue_purpose = array(1 => "Knitting", 2 => "Yarn Dyeing", 3 => "Sales", 4 => "Sample With Order", 5 => "Loan", 6 => "Sample-material", 7 => "Yarn Test", 8 => "Sample Without Order", 9 => "Sewing Production", 10 => "Fabric Test", 11 => "Fabric Dyeing", 12 => "Reconning", 13 => "Machine Wash", 14 => "Topping", 15 => "Twisting", 16 => "Grey Yarn", 26 => "Damage", 27 => "Pilferage", 28 => "Expired", 29 => "Stolen", 30 => "Audit/Adjustment", 31 => "Scrap Store", 32 => "ETP", 33 => "WTP", 34 => "Wash", 35 => "Re Wash", 36 => "Sewing", 37 => "Dyeing", 38 => "Re-Waxing", 39 => "Moisturizing", 40 => "Lab Test", 41 => "Cutting", 42 => "Finishing", 43 => "Dyed Yarn Purchase", 44 => "Re Process", 45 => "Used Cone Sale", 46 => "Dryer", 47 => "Linking", 48 => "Boiler", 49 => "Generator", 50 => "Doubling", 51 => "Punda", 52 => "AOP", 53 => "Production", 54 => "Narrow Fabric", 56 => "General Use", 58 => "RND", 59 => "Sample", 60 => "Expose", 61 => "Gmts Wash", 62 => "Continuous Machine", 63 => "Waxing", 64 => "Extra Purpose", 65 => "Washing", 66 => "ECR", 67 => "Admin", 68 => "Printing", 69 => "RMG", 70 => "Green Agro", 71 => "QAD", 72 => "CIVIL", 73 => "Maintenance", 74 => "Trims Production", 75 => "Yarn Production", 76 => "R-O Plant", 77 => "Print", 78 => "Other/Adjustment", 79 => "Recycling", 80 => "Leftover", 81 => "Mercerization", 82 => "Singeing", 83 => "Embroidery");
        $issue_purpose = array();
        for ($i = 1; $i <= count($yarn_issue_purpose); $i++) {
            $issue_purpose[] = [
                "ID" => $i,
                "NAME" => $yarn_issue_purpose[$i],
            ];
        }
        $data_arr['issue_perpose'] = $issue_purpose;

        if ($all_comp) {
            $wher_com_con = "and id in($all_comp)";
            $wher_com_con2 = "and company_id in($all_comp)";
            $wher_com_con3 = "and company_name in($all_comp)";
        }
        if ($all_loc) {
            $wher_loc_con_b = "and b.id in($all_loc)";
            $wher_loc_con = "and LOCATION_ID in($all_loc)";
            $wher_loc_con2 = "and location_name in($all_loc)";
        }


        $plan_lavel_data_arr = $this->db->query("SELECT BULLETIN_TYPE  from variable_settings_production where variable_list=12 and IS_DELETED=0 and STATUS_ACTIVE=1")->result();
        $data_arr['plan_level'] = $plan_lavel_data_arr[0]->BULLETIN_TYPE;

        $sw_planning_qty_lmt = $this->db->query("SELECT SEWING_PCQ,SEWING_VALUE  from variable_settings_production where variable_list=159 and IS_DELETED=0 and STATUS_ACTIVE=1 order by COMPANY_NAME ASC")->result();

        if ($sw_planning_qty_lmt[0]->SEWING_PCQ == 1) {
            $data_arr['sw_planning_qty_lmt'] = $sw_planning_qty_lmt[0]->SEWING_VALUE;
        } else {
            $data_arr['sw_planning_qty_lmt'] = 0;
        }


        $table_lib_company = $this->db->query("SELECT ID, COMPANY_NAME FROM LIB_COMPANY where IS_DELETED=0 and STATUS_ACTIVE=1")->result();
        foreach ($table_lib_company as $row) {
            $lib_company_arr[$row->ID] = $row->COMPANY_NAME;
        }
        //print_r($lib_company_arr);die;
        $table_with_push = $this->db->query("select AUTO_BALANCING  from variable_settings_production where variable_list=158 and IS_DELETED=0 and STATUS_ACTIVE=1 ORDER BY COMPANY_NAME ASC")->result();
        $data_arr['auto_push'] = $table_with_push[0]->AUTO_BALANCING;

        $comp_sql = "SELECT ID,COMPANY_NAME from lib_company   where status_active =1 and is_deleted=0 and CORE_BUSINESS=1 order by company_name";
        $com_res = $this->db->query($comp_sql)->result();
        $erp_com_cnt = count($com_res);


        $loc_sql = "SELECT b.ID,b.LOCATION_NAME,b.COMPANY_ID from lib_location  b, lib_company a  where a.id=b.company_id and b.status_active=1 and b.is_deleted=0 and  a.status_active =1 and a.is_deleted=0 and a.CORE_BUSINESS=1";


        $line_sql = "SELECT USER_IDS,FLOOR_NAME from lib_sewing_line where status_active= 1 and is_deleted = 0 $wher_loc_con2 $wher_com_con3";
        $line_res = $this->db->query($line_sql)->result();
        $floorArr = array();
        foreach ($line_res as $lineRow) {
            if (in_array($user_id, explode(',', $lineRow->USER_IDS)) == true) {
                $floorArr[$lineRow->FLOOR_NAME] = $lineRow->FLOOR_NAME;
            }
        }


        $floor_sql = "SELECT ID,FLOOR_NAME,LOCATION_ID,COMPANY_ID from  lib_prod_floor where production_process=5 $wher_com_con2 $wher_loc_con and status_active =1 and is_deleted=0 " . where_con_using_array($floorArr, 0, 'id') . " order by floor_serial_no";
        //echo $floor_sql;die;



        $erp_loc = $this->db->query("SELECT count(ID) as ERP_LOC from lib_location  where status_active =1 and is_deleted=0 ")->result();
        $erp_loc_cnt = $erp_loc[0]->ERP_LOC;

        $erp_floor = $this->db->query("SELECT count(ID) as ERP_FLOOR from lib_prod_floor  where status_active =1 and is_deleted=0 ")->result();
        $erp_floor_cnt = $erp_floor[0]->ERP_FLOOR;

        $erp_line = $this->db->query("SELECT count(ID) as ERP_LINE from lib_sewing_line  where status_active =1 and is_deleted=0 ")->result();
        $erp_line_cnt = $erp_line[0]->ERP_LINE;

        $count_sql = "SELECT  COMPANY_COUNT, LOCATION_COUNT, FLOOR_COUNT, LINE_COUNT FROM company_loc_flr_line_count where status_active=1 and id in(select max(id ) from  company_loc_flr_line_count where status_active=1) ";


        $buyer_sql = "SELECT a.ID,a.BUYER_NAME,SHORT_NAME,b.TAG_COMPANY as COMPANY_ID from lib_buyer a,lib_buyer_tag_company b ,lib_buyer_party_type c where a.id=b.buyer_id and a.id=c.buyer_id and c.party_type in(1,3,21,90)  and a.status_active=1 and a.is_deleted=0 group by a.ID,a.BUYER_NAME,SHORT_NAME,b.TAG_COMPANY order by a.BUYER_NAME";





        //echo $comp_sql;die;

        $complexity_level_sql = "SELECT  ID, LEVEL_TYPE, FIRST_DAY, INCREMENT_TYPE, TARGET,  STATUS FROM lib_complexity_level where status_active=1 order by id asc ";
        //$com_res = $this->db->query($comp_sql)->result();
        $count_res = $this->db->query($count_sql)->result();
        $buyer_res = $this->db->query($buyer_sql)->result();
        $loc_res = $this->db->query($loc_sql)->result();
        $floor_res = $this->db->query($floor_sql)->result();
        //$line_res = $this->db->query($line_sql)->result();
        $complexity_level_res = $this->db->query($complexity_level_sql)->result();

        $data_arr['floor_info'][0]["ID"] = 0;
        $data_arr['floor_info'][0]["FLOOR_NAME"] = "All Floor";
        $data_arr['floor_info'][0]["LOCATION_ID"] = 0;
        $data_arr['floor_info'][0]["COMPANY_ID"] = 0;



        $data_arr['company_info'] = $com_res;

        $data_arr['is_planner'] = $is_planner;

        //USER_ACTIVITIES_SETUP table data
        $table_USER_ACTIVITIES_SETUP = $this->db->query("SELECT ACTIVITIES_ID, USER_ID from USER_ACTIVITIES_SETUP where STATUS_ACTIVE = 1 and IS_DELETED = 0 and USER_ID = $user_id")->result();

        $USER_ACTIVITIES_SETUP = array();
        foreach ($table_USER_ACTIVITIES_SETUP as $row) {
            $USER_ACTIVITIES_SETUP[$row->USER_ID][$row->ACTIVITIES_ID] = 1;
        }

        if ($is_planner == 1) {
            $data_arr['is_admin_planner'] = ($USER_ACTIVITIES_SETUP[$user_id][1]) ? 1 : 0;
            $data_arr['is_can_lock'] = ($USER_ACTIVITIES_SETUP[$user_id][2]) ? 1 : 0;
        } else {
            $data_arr['is_admin_planner'] = 0;
            $data_arr['is_can_lock'] = 0;
        }
        // end USER_ACTIVITIES_SETUP table data

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
        //print_r(5);die;
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

        $integrated_arr = $this->db->query("SELECT COMPANY_NAME,WORK_STUDY_INTEGRATED, SMV_TYPE FROM VARIABLE_SETTINGS_PRODUCTION  WHERE VARIABLE_LIST = 9 AND  STATUS_ACTIVE = 1 ORDER BY COMPANY_NAME ASC")->result();

        for ($i = 0; $i < count($integrated_arr); $i++) {
            $variable_setup[$i]['COMPANY_ID'] = $integrated_arr[$i]->COMPANY_NAME;
            $variable_setup[$i]['INTEGRATED'] = $integrated_arr[0]->WORK_STUDY_INTEGRATED;
            $variable_setup[$i]['SMV_TYPE'] = $integrated_arr[0]->SMV_TYPE ? $integrated_arr[0]->SMV_TYPE : 0;
        }

        $data_arr['WORK_STUDY_INTEGRATED'] = $variable_setup;

        $complexity_level = array(0 => "", 1 => "Basic", 2 => "Fancy", 3 => "Critical", 4 => "Average");

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



        $brandSql = "select ID,BUYER_ID,BRAND_NAME from LIB_BUYER_BRAND where STATUS_ACTIVE=1 and IS_DELETED=0 order by BUYER_ID,BRAND_NAME";
        $brandSqlArr = $this->db->query($brandSql)->result();
        foreach ($brandSqlArr as $rows) {
            $data_arr['brand'][] = array(
                'BRAND_ID' => $rows->ID,
                'BRAND_NAME' => $rows->BRAND_NAME,
                'BUYER_ID' => $rows->BUYER_ID,
            );
        }



        $seasonSql = "select ID,BUYER_ID,SEASON_NAME from LIB_BUYER_SEASON where STATUS_ACTIVE=1 and IS_DELETED=0 order by BUYER_ID,SEASON_NAME";
        $seasonSqlArr = $this->db->query($seasonSql)->result();
        foreach ($seasonSqlArr as $rows) {
            $data_arr['season'][] = array(
                'SEASON_ID' => $rows->ID,
                'SEASON_NAME' => $rows->SEASON_NAME,
                'BUYER_ID' => $rows->BUYER_ID,
            );
        }

        //$product_category = array(1 => "Outwears", 2 => "Lingerie", 3 => "Sweater", 4 => "Socks", 5 => "Fabric", 6 => "Top", 7 => "Bottom", 8 => "Denim", 9 => "Blazer");



        $data_arr['product_cate'] = [['ID' => 1, 'SHORT_NAME' => "Outwears"], ['ID' => 2, 'SHORT_NAME' => "Lingerie"], ['ID' => 3, 'SHORT_NAME' => "Sweater"], ['ID' => 4, 'SHORT_NAME' => "Socks"], ['ID' => 5, 'SHORT_NAME' => "Fabric"], ['ID' => 6, 'SHORT_NAME' => "Top"], ['ID' => 7, 'SHORT_NAME' => "Bottom"], ['ID' => 8, 'SHORT_NAME' => "Denim"], ['ID' => 9, 'SHORT_NAME' => "Blazer"]];



        $departmentSqlArr = array(1 => "Mens", 2 => "Ladies", 3 => "Teenage-Girls", 4 => "Teenage-Boys", 5 => "Kids-Boys", 6 => "Infant", 7 => "Unisex", 8 => "Kids-Girls", 9 => "Baby", 10 => "Kids", 11 => "Women", 12 => "Infant Boy", 13 => "Infant Girls", 14 => "Toddler Boys", 15 => "Toddler Girls", 16 => "New Born", 17 => "Pet", 18 => "CHILDREN", 19 => "ACTIVE", 20 => "ABM", 21 => "NIGHTWEAR", 22 => "Older girls", 23 => "Girls", 24 => "Older Boys", 25 => "Boys", 26 => "Mini Boys", 27 => "Mini Girls", 28 => "Baby Girls", 29 => "Baby Boys", 30 => "BT Boys", 31 => "BT Girls", 32 => "CIN", 33 => "School Polo", 34 => "BIG BOYS", 35 => "BIG GIRLS", 36 => "Underwear", 37 => "Girls Set", 38 => "Girls Playwear", 39 => "Boys Playwear", 40 => "Girls Sleepwear", 41 => "Boys Sleepwear", 42 => "HI n BYE");
        foreach ($departmentSqlArr as $key => $val) {
            $data_arr['department'][] = array(
                'DEPARTMENT_ID' => $key,
                'DEPARTMENT_NAME' => $val,
            );
        }

        $subDepartmentSql = "select ID,BUYER_ID,DEPARTMENT_ID,SUB_DEPARTMENT_NAME from LIB_PRO_SUB_DEPARATMENT where STATUS_ACTIVE=1 and IS_DELETED=0 order by BUYER_ID,DEPARTMENT_ID,SUB_DEPARTMENT_NAME";
        //print_r($subDepartmentSql);die;
        $subDepartmentSqlArr = $this->db->query($subDepartmentSql)->result();
        foreach ($subDepartmentSqlArr as $rows) {
            $data_arr['sub_department'][] = array(
                'BUYER_ID' => $rows->BUYER_ID,
                'DEPARTMENT_ID' => $rows->DEPARTMENT_ID,
                'SUB_DEPARTMENT_ID' => $rows->ID,
                'SUB_DEPARTMENT_NAME' => $rows->SUB_DEPARTMENT_NAME,

            );
        }



        $variableSql = "select COMPANY_NAME,PLANNING_BOARD_STRIP_CAPTION from VARIABLE_SETTINGS_PRODUCTION where STATUS_ACTIVE=1 and IS_DELETED=0 and VARIABLE_LIST = 4 order by COMPANY_NAME";
        $variableSqlArr = $this->db->query($variableSql)->result();
        $arr = array(1 => 'Style Ref', 2 => 'Int. Ref', 3 => 'Job No', 4 => 'Order No', 5 => 'Buyer Name', 6 => 'Plan Quantity');
        foreach ($variableSqlArr as $rows) {
            $PLANNING_BOARD_STRIP_CAPTION_ARR = explode(',', $rows->PLANNING_BOARD_STRIP_CAPTION);
            foreach ($PLANNING_BOARD_STRIP_CAPTION_ARR as $pbsci) {
                if ($pbsci) {
                    $data_arr['planning_board_strip_caption'][] = array(
                        'COMPANY_ID' => $rows->COMPANY_NAME,
                        'CAPTION_NAME' => $arr[$pbsci],
                        'CAPTION_ID' => $pbsci
                    );
                }
            }
        }


        foreach ($arr as $key => $val) {
            $data_arr['planning_board_strip_caption_arr'][] = array(
                'CAPTION_NAME' => $val,
                'CAPTION_ID' => $key
            );
        }



        return $data_arr;
    }

    public function get_financial_parameter_setup()
    {
        $variableSql = "SELECT COMPANY_NAME,YARN_ISS_WITH_SERV_APP  FROM VARIABLE_ORDER_TRACKING WHERE VARIABLE_LIST=67";
        $variableSqlRes = $this->db->query($variableSql)->result();

        if (count($variableSqlRes)) {
            $currentDate = date('d-M-Y');
            $dataArr = array();
            $sql = '';
            foreach ($variableSqlRes as $rows) {
                $company_id = $rows->COMPANY_NAME;
                if ($sql != '') {
                    $sql .= ' union all ';
                }
                if ($rows->YARN_ISS_WITH_SERV_APP == 1) {
                    $sql .= "SELECT a.COMPANY_ID ,b.LOCATION_ID,ROUND(b.WORKING_HOUR) as WORKING_HOUR from lib_standard_cm_entry a,LIB_STANDARD_CM_ENTRY_DTLS b where a.id=b.mst_id  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and a.id in(select max(id) FROM lib_standard_cm_entry where COMPANY_ID = $company_id and  (APPLYING_PERIOD_TO_DATE <='$currentDate' or APPLYING_PERIOD_DATE <='$currentDate')   and  IS_DELETED=0 and  STATUS_ACTIVE=1 group by COMPANY_ID)";
                } else {
                    //$sql .= "select COMPANY_ID, 0 as LOCATION_ID,WORKING_HOUR  from lib_standard_cm_entry where id in(select max(id) from lib_standard_cm_entry where   COMPANY_ID = $company_id and '$currentDate' between APPLYING_PERIOD_DATE and APPLYING_PERIOD_TO_DATE group by COMPANY_ID)";
                    $sql .= "SELECT COMPANY_ID, 0 as LOCATION_ID,ROUND(WORKING_HOUR) as WORKING_HOUR  from lib_standard_cm_entry where id in(select max(id) from lib_standard_cm_entry where   COMPANY_ID = $company_id and  (APPLYING_PERIOD_TO_DATE <='$currentDate' or APPLYING_PERIOD_DATE <='$currentDate') and IS_DELETED=0 and  STATUS_ACTIVE=1 group by COMPANY_ID)";
                }
            }
            $dataArr = $this->db->query($sql)->result();
        }

        // echo $sql;die;

        return $dataArr;
    }







    public function grn_wise_yarn_data($grn_no)
    {
        // print_r(5);
        // die;
        $msg = "";
        $success_status = 0;
        $query_yarn_grn = "SELECT a.ID,a.RECV_NUMBER,a.COMPANY_ID,b.IS_QC_PASS,a.IS_APPROVED,a.INSERT_DATE,a.RECEIVE_BASIS,a.RECEIVE_PURPOSE,a.LOAN_PARTY,a.BOOKING_NO,a.CHALLAN_NO,a.EXCHANGE_RATE,a.CURRENCY_ID,a.SUPPLIER_ID ,a.STORE_ID,a.SOURCE,b.ID as DTLS_ID,b.WO_PI_ID,b.LOT,b.WO_PI_DTLS_ID,b.WO_PI_NO,b.YARN_COUNT,b.YARN_COMP_TYPE1ST,b.YARN_COMP_PERCENT1ST,b.YARN_TYPE,b.COLOR_NAME,b.UOM,b.WO_PI_QUANTITY,b.COLOR_NAME,b.PARKING_QUANTITY,b.LOSE_CONE,b.RATE,b.AVG_RATE,b.AMOUNT,b.CONS_AMOUNT,b.NO_OF_BAG,b.CONE_PER_BAG,b.WEIGHT_PER_BAG,b.WEIGHT_CONE,b.INSERTED_BY,b.BRAND_NAME FROM INV_RECEIVE_MASTER a,QUARANTINE_PARKING_DTLS b WHERE a.ID = b.MST_ID and a.ENTRY_FORM = 529 and a.ITEM_CATEGORY = 1 and a.STATUS_ACTIVE=1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE =1 and b.IS_DELETED = 0 and a.RECV_NUMBER = '$grn_no'";
        //print_r($query_yarn_grn);die;
        $table_yarn_grn = $this->db->query($query_yarn_grn)->result();
        $rfids = array();
        $mst = array();
        $dtls = array();

        if (!empty($table_yarn_grn)) {
            $query_check_inv_rcv_mst = "SELECT irm.ID FROM INV_RECEIVE_MASTER irm JOIN INV_RECEIVE_MASTER irm2 ON irm.EMP_ID = irm2.ID WHERE irm2.RECV_NUMBER = '$grn_no'";
            $table_inv_old_row = $this->db->query($query_check_inv_rcv_mst)->row();

            if (!empty($table_inv_old_row)) {
                $query_rfids = "SELECT * FROM RFID_YARN_DTLS WHERE RCV_MST_ID = $table_inv_old_row->ID and TRANS_TYPE = 1 AND ITEM_CATEGORY = 1 and ENTRY_FORM = 1 and STATUS_ACTIVE = 1 and IS_DELETED = 0";
                $table_rfids = $this->db->query($query_rfids)->result();
                //print_r($query_rfids);die;
                if (!empty($table_rfids)) {
                    foreach ($table_rfids as $row) {
                        $rfids[$row->PARKING_DTLS_ID][] = [
                            "ID" => $row->ID,
                            "MST_ID" => $row->MST_ID,
                            "PARKING_DTLS_ID" => $row->PARKING_DTLS_ID,
                            "RFID" => $row->RFID_NO,
                        ];
                    }
                }
            }


            $company_query = $this->db->query("SELECT ID,COMPANY_NAME FROM LIB_COMPANY WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0 ")->result();
            $company_arr = array();
            foreach ($company_query as $row) {
                $company_arr[$row->ID] = $row->COMPANY_NAME;
            }

            $yarn_count = $this->db->query("SELECT ID,YARN_COUNT FROM LIB_YARN_COUNT WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0 ")->result();
            $yarn_count_arr = array();
            foreach ($yarn_count as $row) {
                $yarn_count_arr[$row->ID] = $row->YARN_COUNT;
            }

            $lib_yarn_type = $this->db->query("SELECT YARN_TYPE_ID,YARN_TYPE_SHORT_NAME FROM LIB_YARN_TYPE WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0 ")->result();
            $lib_yarn_type_arr = array();
            foreach ($lib_yarn_type as $row) {
                $lib_yarn_type_arr[$row->YARN_TYPE_ID] = $row->YARN_TYPE_SHORT_NAME;
            }

            $lib_composition = $this->db->query("SELECT ID,COMPOSITION_NAME FROM lib_composition_array WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0 ")->result();
            $composition_array = array();
            foreach ($lib_composition as $row) {
                $composition_array[$row->ID] = $row->COMPOSITION_NAME;
            }

            $lib_color = $this->db->query("SELECT ID,COLOR_NAME FROM LIB_COLOR WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0 ")->result();
            $color_array = array();
            foreach ($lib_color as $row) {
                $color_array[$row->ID] = $row->COLOR_NAME;
            }

            $lib_supplier = $this->db->query("SELECT ID,SUPPLIER_NAME FROM LIB_SUPPLIER WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0 ")->result();
            $supplier_array = array();
            foreach ($lib_supplier as $row) {
                $supplier_array[$row->ID] = $row->SUPPLIER_NAME;
            }

            $lib_store = $this->db->query("SELECT ID,STORE_NAME FROM LIB_STORE_LOCATION WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0 ")->result();
            $store_array = array();
            foreach ($lib_store as $row) {
                $store_array[$row->ID] = $row->STORE_NAME;
            }

            $unit_of_measurement = array(1 => "Pcs", 2 => "Dzn", 3 => "Grs", 4 => "GG", 10 => "Mg", 11 => "Gm", 12 => "Kg", 13 => "Quintal", 14 => "Ton", 15 => "Lbs", 20 => "Km", 21 => "Hm", 22 => "Dm", 23 => "Mtr", 24 => "Dcm", 25 => "CM", 26 => "MM", 27 => "Yds", 28 => "Feet", 29 => "Inch", 30 => "CFT", 31 => "SFT", 40 => "Ltr", 41 => "ML", 50 => "Roll", 51 => "Coil", 52 => "Cone", 53 => "Bag", 54 => "Box", 55 => "Drum", 56 => "Bottle", 57 => "Pack", 58 => "Set", 59 => "Can", 60 => "Each", 61 => "Gallon", 62 => "Lachi", 63 => "Pair", 64 => "Lot", 65 => "Packet", 66 => "Pot", 67 => "Book", 68 => "Culind", 69 => "Ream", 70 => "Cft", 71 => "Syp", 72 => "K.V", 73 => "CU-M3", 74 => "Bundle", 75 => "Strip", 76 => "SQM", 77 => "Ounce", 78 => "Cylinder", 79 => "Course", 80 => "Sheet", 81 => "RFT", 82 => "Square Inch", 83 => "Carton", 84 => "Thane", 85 => "Gross Yds", 86 => "Jar", 87 => "Reel", 88 => "CBM", 89 => "Tub", 90 => "KVA", 91 => "KW", 92 => "Pallet", 93 => "Case", 94 => "Job", 95 => "KIT", 96 => "Watt-Peak");

            $yarn_issue_purpose = array(1 => "Knitting", 2 => "Yarn Dyeing", 3 => "Sales", 4 => "Sample With Order", 5 => "Loan", 6 => "Sample-material", 7 => "Yarn Test", 8 => "Sample Without Order", 9 => "Sewing Production", 10 => "Fabric Test", 11 => "Fabric Dyeing", 12 => "Reconning", 13 => "Machine Wash", 14 => "Topping", 15 => "Twisting", 16 => "Grey Yarn", 26 => "Damage", 27 => "Pilferage", 28 => "Expired", 29 => "Stolen", 30 => "Audit/Adjustment", 31 => "Scrap Store", 32 => "ETP", 33 => "WTP", 34 => "Wash", 35 => "Re Wash", 36 => "Sewing", 37 => "Dyeing", 38 => "Re-Waxing", 39 => "Moisturizing", 40 => "Lab Test", 41 => "Cutting", 42 => "Finishing", 43 => "Dyed Yarn Purchase", 44 => "Re Process", 45 => "Used Cone Sale", 46 => "Dryer", 47 => "Linking", 48 => "Boiler", 49 => "Generator", 50 => "Doubling", 51 => "Punda", 52 => "AOP", 53 => "Production", 54 => "Narrow Fabric", 56 => "General Use", 58 => "RND", 59 => "Sample", 60 => "Expose", 61 => "Gmts Wash", 62 => "Continuous Machine", 63 => "Waxing", 64 => "Extra Purpose", 65 => "Washing", 66 => "ECR", 67 => "Admin", 68 => "Printing", 69 => "RMG", 70 => "Green Agro", 71 => "QAD", 72 => "CIVIL", 73 => "Maintenance", 74 => "Trims Production", 75 => "Yarn Production", 76 => "R-O Plant", 77 => "Print", 78 => "Other/Adjustment", 79 => "Recycling", 80 => "Leftover", 81 => "Mercerization", 82 => "Singeing", 83 => "Embroidery");

            $currency = array(1 => "BDT", 2 => "USD", 3 => "EURO", 4 => "CHF", 5 => "SGD", 6 => "Pound", 7 => "YEN");

            $rec_basis = [
                1 => "PI Based",
                2 => "WO/Booking Based",
                4 => "Independent",
            ];

            $source_arr = [
                1 => "Import Foreign",
                2 => "EPZ",
                3 => "Import Local",
            ];

            $success_status = 200;


            //print_r($rfids);die;
            foreach ($table_yarn_grn as $row) {
                if ($row->IS_QC_PASS == 0) {
                    $msg = "Required ID is not QC Passed";
                    continue;
                } elseif ($row->IS_APPROVED == 0) {
                    $msg = "Required ID is not Approved";
                    continue;
                }
                $mst = [
                    "ID" => $row->ID,
                    "RECV_NUMBER" => $row->RECV_NUMBER,
                    "COMPANY_ID" => $row->COMPANY_ID,
                    "COMPANY_NAME" => $company_arr[$row->COMPANY_ID],
                    "RECEIVE_PURPOSE_ID" => $row->RECEIVE_PURPOSE,
                    "RECEIVE_PURPOSE" => $yarn_issue_purpose[$row->RECEIVE_PURPOSE],
                    "RECEIVE_BASIS_ID" => $row->RECEIVE_BASIS,
                    "RECEIVE_BASIS" => $rec_basis[$row->RECEIVE_BASIS],
                    "BOOKING_NO" => $row->BOOKING_NO,
                    "SUPPLIER_ID" => $row->SUPPLIER_ID,
                    "SUPPLIER_NAME" => $supplier_array[$row->SUPPLIER_ID],
                    "SOURCE_ID" => $row->SOURCE,
                    "SOURCE_NAME" => $source_arr[$row->SOURCE],
                    "PARKING_DATE" => date("d-M-Y", strtotime($row->INSERT_DATE)),

                    "LOAN_PARTY_ID" => $row->LOAN_PARTY,
                    "LOAN_PARTY" => ($supplier_array[$row->LOAN_PARTY]) ? $supplier_array[$row->LOAN_PARTY] : "",

                    "CHALLAN_NO" => $row->CHALLAN_NO,
                    "STORE_ID" => $row->STORE_ID,
                    "STORE_NAME" => ($store_array[$row->STORE_ID]) ? $store_array[$row->STORE_ID] : "",
                    "EXCHANGE_RATE" => $row->EXCHANGE_RATE,
                    "CURRENCY_ID" => $row->CURRENCY_ID,
                    "CURRENCY_NAME" => $currency[$row->CURRENCY_ID],
                    "PUB_MSG" => $msg,
                    // "SUCCESS_STATUS" => $success_status,

                ];
                $dtls[] = [
                    "DTLS_ID" => $row->DTLS_ID,
                    "NO_OF_BAG" => $row->NO_OF_BAG,
                    "WEIGHT_PER_BAG" => number_format($row->WEIGHT_PER_BAG, 2),
                    "LOOSE_BAG_WT" => number_format($row->PARKING_QUANTITY - ($row->WEIGHT_PER_BAG * $row->NO_OF_BAG), 2),
                    "CON_PER_BAG" => $row->CONE_PER_BAG,
                    "LOOSE_CON" => $row->LOSE_CONE,
                    "WT_PER_CON" => number_format($row->PARKING_QUANTITY / (($row->CONE_PER_BAG * $row->NO_OF_BAG) + $row->LOSE_CONE)),
                    "LOT" => $row->LOT,
                    "BRAND_NAME" => ($row->BRAND_NAME) ? $row->BRAND_NAME : "",
                    "COLOR_ID" => $row->COLOR_NAME,
                    "COLOR_NAME" => $color_array[$row->COLOR_NAME],
                    "YARN_TYPE_ID" => $row->YARN_TYPE,
                    "YARN_TYPE" => $lib_yarn_type_arr[$row->YARN_TYPE],
                    "YARN_COMP_TYPE_ID" => $row->YARN_COMP_TYPE1ST,
                    "YARN_COMP_TYPE" => $composition_array[$row->YARN_COMP_TYPE1ST],
                    "YARN_COUNT_ID" => $row->YARN_COUNT,
                    "YARN_COUNT" => $yarn_count_arr[$row->YARN_COUNT],
                    "UOM_ID" => $row->UOM,
                    "UOM" => $unit_of_measurement[$row->UOM],
                    "WO_PI_QUANTITY" => $row->WO_PI_QUANTITY,
                    "PARKING_QUANTITY" => number_format($row->PARKING_QUANTITY, 2),
                    "RATE" => $row->RATE,
                    "AVG_RATE" => $row->AVG_RATE,
                    "AMOUNT" => $row->AMOUNT,
                    "CONS_AMOUNT" => number_format($row->CONS_AMOUNT, 2),
                    "WEIGHT_CONE" => number_format($row->WEIGHT_CONE, 2),
                    "INSERTED_BY" => $row->INSERTED_BY,
                    "RFIDS" => ($rfids[$row->DTLS_ID]) ?? [],
                ];
            }
        } else {
            $msg .= "No Data Found on this GRN";
            //$success_status = 0;
        }

        $mst["YARN_RECEIVE_DTLS"] = $dtls;

        $mst["SUCCESS_STATUS"] = $success_status;
        $mst["PUB_MSG"] = (empty($mst["YARN_RECEIVE_DTLS"])) ? $msg : "";
        //print_r($mst);die;
        return $mst;
    }

    public function grn_wise_yarn_data_for_issue($req_no, $basis_id, $issue_purpose)
    {
        // print_r(5);
        // die;
        //$basis_id = 3;
        $msg = "";
        $success_status = 0;
        $rec_basis = [3 => 'REQUISITION', 8 => 'DEMAND', 1 => 'BOOKING'];
        if ($basis_id == 3) {
            //requisition
            //$query_requisition_mst = "SELECT a.PROD_ID,a.REQUISITION_NO,a.YARN_QNTY,c.COMPANY_ID, 3 as RCV_BASIS,c.BOOKING_NO,b.KNITTING_SOURCE,c.BUYER_ID,c.PO_ID,c.IS_SALES,d.WITHIN_GROUP,d.PO_BUYER,b.KNITTING_PARTY   FROM ppl_yarn_requisition_entry a,PPL_PLANNING_INFO_ENTRY_DTLS b,PPL_PLANNING_ENTRY_PLAN_DTLS c,FABRIC_SALES_ORDER_MST d WHERE a.KNIT_ID = b.ID and c.DTLS_ID = b.ID and d.ID = c.PO_ID and a.IS_DELETED =0 and a.STATUS_ACTIVE = 1 and a.REQUISITION_NO = $req_no";
            $query_requisition_mst = "SELECT a.PROD_ID,a.REQUISITION_NO,a.YARN_QNTY,c.COMPANY_ID, 3 as RCV_BASIS,c.BOOKING_NO,b.KNITTING_SOURCE,b.LOCATION_ID,c.BUYER_ID,c.PO_ID,c.IS_SALES, b.KNITTING_PARTY FROM ppl_yarn_requisition_entry a,PPL_PLANNING_INFO_ENTRY_DTLS b,PPL_PLANNING_ENTRY_PLAN_DTLS c
            WHERE a.KNIT_ID = b.ID and c.DTLS_ID = b.ID 
            and a.IS_DELETED =0 and a.STATUS_ACTIVE = 1 and a.REQUISITION_NO = $req_no";
            $table = $this->db->query($query_requisition_mst)->result();
            //print_r($query_requisition_mst);die;
            $prod_ids_arr = array();
            $prod_ids_str = "";
            $req_qnty_by_prod_id = array();
            foreach ($table as $row) {
                $prod_ids_arr[$row->PROD_ID] = $row->PROD_ID;
                $req_qnty_by_prod_id[$row->PROD_ID] = $row->YARN_QNTY;
            }
            $prod_ids_str = implode(",", $prod_ids_arr);
            // print_r($prod_ids_str);
            // die;

            //$query_product_dtls_table = "SELECT a.ID as PROD_ID,a.LOT as LOT_NO,a.PRODUCT_NAME_DETAILS as PRODUCT_DETAILS,a.YARN_TYPE as YARN_TYPE_ID,a.COLOR as COLOR_ID,a.CURRENT_STOCK,a.SUPPLIER_ID,b.COLOR_NAME,c.SUPPLIER_NAME,d.YARN_QNTY as REQ_QNTY FROM PRODUCT_DETAILS_MASTER a,LIB_COLOR b,LIB_SUPPLIER c,ppl_yarn_requisition_entry d WHERE a.COLOR = b.ID and c.ID =a.SUPPLIER_ID and a.ID = d.PROD_ID and a.IS_DELETED =0 and a.STATUS_ACTIVE = 1 and a.ID in ($prod_ids_str)";
            $query_product_dtls_table = "SELECT a.company_id, a.ID AS PROD_ID, a.LOT AS LOT_NO, a.PRODUCT_NAME_DETAILS AS PRODUCT_DETAILS, a.YARN_COUNT_ID, a.YARN_COMP_TYPE1ST, a.YARN_TYPE AS YARN_TYPE_ID, a.brand, a.COLOR AS COLOR_ID, a.UNIT_OF_MEASURE, a.CURRENT_STOCK, a.SUPPLIER_ID, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) as rcv, sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as issue, SUM ( (CASE WHEN transaction_type IN (1, 4, 5) THEN cons_quantity ELSE 0 END) - (CASE WHEN transaction_type IN (2, 3, 6) THEN cons_quantity ELSE 0 END)) AS available_qnty FROM product_details_master a, inv_transaction b WHERE a.id = b.prod_id AND a.item_category_id = 1 AND b.item_category = 1 AND a.ID IN ($prod_ids_str) AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 GROUP BY a.company_id, a.ID, a.LOT, a.PRODUCT_NAME_DETAILS, a.YARN_COUNT_ID, a.YARN_COMP_TYPE1ST, a.YARN_TYPE, a.brand, a.COLOR, a.UNIT_OF_MEASURE, a.CURRENT_STOCK, a.SUPPLIER_ID, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box";
            $table_product_dtls = $this->db->query($query_product_dtls_table)->result();

            $products_arr = $table_product_dtls;
        } else if ($basis_id == 8) {
            // print_r(5);
            // die;
            $demand_query = "SELECT a.PROD_ID,a.REQUISITION_NO,a.YARN_QNTY,c.COMPANY_ID, 8 as RCV_BASIS,c.BOOKING_NO,b.KNITTING_SOURCE,c.BUYER_ID,c.PO_ID,c.IS_SALES,d.WITHIN_GROUP,d.PO_BUYER,b.KNITTING_PARTY,f.ID as DEMAND_ID,f.DEMAND_SYSTEM_NO
            FROM ppl_yarn_requisition_entry a,PPL_PLANNING_INFO_ENTRY_DTLS b,PPL_PLANNING_ENTRY_PLAN_DTLS c,FABRIC_SALES_ORDER_MST d,ppl_yarn_demand_reqsn_dtls e,ppl_yarn_demand_entry_mst f WHERE a.KNIT_ID = b.ID and c.DTLS_ID = b.ID and d.ID = c.PO_ID and a.IS_DELETED =0 and a.STATUS_ACTIVE = 1 and a.ID = e.REQUISITION_ID and e.MST_ID = f.ID
            and f.DEMAND_SYSTEM_NO = '$req_no'";
            $table = $this->db->query($demand_query)->result();


            //print_r($table);die;
            $prod_id_arr = array();
            foreach ($table as $row) {
                $prod_id_arr[$row->PROD_ID] = $row->PROD_ID;
            }
            $prod_ids_str = implode(',', $prod_id_arr);
            //$req_no = $table[0]->DEMAND_ID;
            $query_product_dtls_table = "SELECT a.ID as PROD_ID,a.LOT as LOT_NO,a.PRODUCT_NAME_DETAILS as PRODUCT_DETAILS,a.YARN_TYPE as YARN_TYPE_ID,a.COLOR as COLOR_ID,a.CURRENT_STOCK,a.SUPPLIER_ID,b.COLOR_NAME,c.SUPPLIER_NAME,d.YARN_QNTY as REQ_QNTY FROM PRODUCT_DETAILS_MASTER a,LIB_COLOR b,LIB_SUPPLIER c,ppl_yarn_requisition_entry d WHERE a.COLOR = b.ID and c.ID =a.SUPPLIER_ID and a.ID = d.PROD_ID and a.IS_DELETED =0 and a.STATUS_ACTIVE = 1 and a.ID in ($prod_ids_str)";
            $table_product_dtls = $this->db->query($query_product_dtls_table)->result();

            $products_arr = $table_product_dtls;
        } else if ($basis_id == 1) {

            $requisition_query = "SELECT a.YDW_NO,a.COMPANY_ID, 1 as RCV_BASIS,a.BOOKING_DATE,a.SUPPLIER_ID,b.PRODUCT_ID as PROD_ID,e.PO_BUYER,b.YARN_COLOR,c.JOB_NO,b.FAB_BOOKING_NO from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b left join wo_po_details_master c on b.job_no_id=c.id left join wo_non_ord_samp_booking_mst d on b.booking_no=d.booking_no left join fabric_sales_order_mst e on e.job_no=b.job_no 
            where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
            and a.ydw_no = '$req_no' and a.entry_form in(41,42,114,125,135)";
            //$query_requisition_mst = "SELECT a.PROD_ID,a.REQUISITION_NO,a.YARN_QNTY,c.COMPANY_ID, 3 as RCV_BASIS,c.BOOKING_NO,b.KNITTING_SOURCE,c.BUYER_ID,c.PO_ID,c.IS_SALES,d.WITHIN_GROUP,d.PO_BUYER,b.KNITTING_PARTY   FROM ppl_yarn_requisition_entry a,PPL_PLANNING_INFO_ENTRY_DTLS b,PPL_PLANNING_ENTRY_PLAN_DTLS c,FABRIC_SALES_ORDER_MST d WHERE a.KNIT_ID = b.ID and c.DTLS_ID = b.ID and d.ID = c.PO_ID and a.IS_DELETED =0 and a.STATUS_ACTIVE = 1 and a.REQUISITION_NO = $req_no";
            //print_r($requisition_query);die;
            $table = $this->db->query($requisition_query)->result();


            $prod_id_arr = array();
            foreach ($table as $row) {
                $prod_id_arr[$row->PROD_ID] = $row->PROD_ID;
            }
            $prod_ids_str = implode(',', $prod_id_arr);
            //$req_no = $table[0]->DEMAND_ID;
            $query_product_dtls_table = "SELECT a.ID as PROD_ID,a.LOT as LOT_NO,a.PRODUCT_NAME_DETAILS as PRODUCT_DETAILS,a.YARN_TYPE as YARN_TYPE_ID,a.COLOR as COLOR_ID,a.CURRENT_STOCK,a.SUPPLIER_ID,b.COLOR_NAME,c.SUPPLIER_NAME,d.YARN_QNTY as REQ_QNTY FROM PRODUCT_DETAILS_MASTER a,LIB_COLOR b,LIB_SUPPLIER c,ppl_yarn_requisition_entry d WHERE a.COLOR = b.ID and c.ID =a.SUPPLIER_ID and a.ID = d.PROD_ID and a.IS_DELETED =0 and a.STATUS_ACTIVE = 1 and a.ID in ($prod_ids_str)";
            $table_product_dtls = $this->db->query($query_product_dtls_table)->result();

            $products_arr = $table_product_dtls;
        }


        //print_r($table_product);die;
        //$data = [];
        if ($table) {
            //$lib_color = return_library_arr("SELECT ID,COLOR_NAME FROM lib_color WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0", "ID", "COLOR_NAME");
            //$lib_yarn_type = return_library_arr("SELECT YARN_TYPE_ID,YARN_TYPE_SHORT_NAME FROM LIB_YARN_TYPE WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0", "YARN_TYPE_ID", "YARN_TYPE_SHORT_NAME");
            $lib_supplier = return_library_arr("SELECT ID,SUPPLIER_NAME FROM LIB_SUPPLIER WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0", "ID", "SUPPLIER_NAME");
            $lib_com = return_library_arr("SELECT ID,COMPANY_NAME FROM LIB_COMPANY WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0", "ID", "COMPANY_NAME");
            $lib_buyer = return_library_arr("SELECT ID,BUYER_NAME FROM LIB_BUYER WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0", "ID", "BUYER_NAME");



            //$lib_floor_room_rack = return_library_arr("SELECT FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME FROM LIB_FLOOR_ROOM_RACK_MST WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0", "FLOOR_ROOM_RACK_ID", "FLOOR_ROOM_RACK_NAME");



            //print_r($table);die;
            if ($basis_id == 3) {

                $mst = array();
                //foreach ($table as $row) {
                $mst = [
                    "COMPANY_ID" => $table[0]->COMPANY_ID,
                    "COMPANY_NAME" => $lib_com[$table[0]->COMPANY_ID],
                    "RECEIVE_BASIS_ID" => $table[0]->RCV_BASIS,
                    "ISSUE_PERPOSE" => $issue_purpose,
                    "ISSUE_DATE" => date('d-M-Y'),
                    "KNITTING_SOURCE" => $table[0]->KNITTING_SOURCE,
                    "LOCATION_ID" => $table[0]->LOCATION_ID,
                    "SUPPLIER_ID" => $table[0]->SUPPLIER_ID,
                    "SUPPLIER_NAME" => $lib_supplier[$table[0]->SUPPLIER_ID],
                    "CHALLAN_PROGRAM_NO" => "",
                    "LOAN_PARTY_ID" => 0,
                    "SAMPLE_TYPE" => 0,
                    "STYLE_REF" => "",
                    "BUYER_JOB_NO" => $table[0]->JOB_NO,
                    "SERVICE_BOOKING" => "",
                    "READY_TO_APPROVED" => 0,
                    "ATTENTION" => "",
                    "SYSTEM_NUMBER" => $table[0]->REQUISITION_NO,
                    "BOOKING_ID" => $table[0]->REQUISITION_NO,
                    "BOOKING_NUMBER" => $table[0]->BOOKING_NO,
                    "DEMAND_ID" => 0,
                    "RECEIVE_BASIS" => $rec_basis[$table[0]->RCV_BASIS],
                    "ISSUE_DATE" => $table[0]->REQUISITION_DATE,
                    "KNITTING_COMPANY_ID" => $table[0]->KNITTING_PARTY,
                    "KNITTING_COMPANY" => $lib_com[$table[0]->KNITTING_PARTY],
                    "BUYER_ID" => ($table[0]->IS_SALES == 1 && $table[0]->WITHIN_GROUP == 1) ? $table[0]->PO_BUYER : $table[0]->BUYER_ID,
                    "BUYER_NAME" => ($table[0]->IS_SALES == 1 && $table[0]->WITHIN_GROUP == 1) ? $lib_buyer[$table[0]->PO_BUYER] : $lib_buyer[$table[0]->BUYER_ID],
                    "PRODUCTS" => $products_arr,

                ];
                //}
                //print_r($mst);die;
            } else if ($basis_id == 8) {
                $mst = array();
                //foreach ($table as $row) {
                $mst = [
                    "COMPANY_ID" => $table[0]->COMPANY_ID,
                    "COMPANY_NAME" => $lib_com[$table[0]->COMPANY_ID],
                    "RECEIVE_BASIS_ID" => $table[0]->RCV_BASIS,
                    "ISSUE_PERPOSE" => 0,
                    "ISSUE_DATE" => date('d-M-Y'),
                    "KNITTING_SOURCE" => $table[0]->KNITTING_SOURCE,
                    "LOCATION_ID" => 0,
                    "SUPPLIER_ID" => $table[0]->SUPPLIER_ID,
                    "SUPPLIER_NAME" => $lib_supplier[$table[0]->SUPPLIER_ID],
                    "CHALLAN_PROGRAM_NO" => "",
                    "LOAN_PARTY_ID" => 0,
                    "SAMPLE_TYPE" => 0,
                    "STYLE_REF" => "",
                    "BUYER_JOB_NO" => $table[0]->JOB_NO,
                    "SERVICE_BOOKING" => "",
                    "READY_TO_APPROVED" => 0,
                    "ATTENTION" => "",
                    "SYSTEM_NUMBER" => $table[0]->DEMAND_SYSTEM_NO,
                    "BOOKING_ID" => "",
                    "BOOKING_NUMBER" => $table[0]->BOOKING_NO,
                    "DEMAND_ID" => $table[0]->DEMAND_ID,
                    "RECEIVE_BASIS" => $rec_basis[$table[0]->RCV_BASIS],
                    "ISSUE_DATE" => $table[0]->REQUISITION_DATE,
                    "KNITTING_COMPANY_ID" => $table[0]->KNITTING_PARTY,
                    "KNITTING_COMPANY" => $lib_com[$table[0]->KNITTING_PARTY],
                    "BUYER_ID" => ($table[0]->IS_SALES == 1 && $table[0]->WITHIN_GROUP == 1) ? $table[0]->PO_BUYER : $table[0]->BUYER_ID,
                    "BUYER_NAME" => ($table[0]->IS_SALES == 1 && $table[0]->WITHIN_GROUP == 1) ? $lib_buyer[$table[0]->PO_BUYER] : $lib_buyer[$table[0]->BUYER_ID],
                    "PRODUCTS" => $products_arr,

                ];
            } else if ($basis_id == 1) {
                $mst = array();
                //foreach ($table as $row) {
                $mst = [
                    "COMPANY_ID" => $table[0]->COMPANY_ID,
                    "COMPANY_NAME" => $lib_com[$table[0]->COMPANY_ID],
                    "RECEIVE_BASIS_ID" => $table[0]->RCV_BASIS,
                    "ISSUE_PERPOSE" => 0,
                    "ISSUE_DATE" => date('d-M-Y', time()),
                    "KNITTING_SOURCE" => "",
                    "ISSUE_TO" => 0,
                    "LOCATION_ID" => 0,
                    "SUPPLIER_ID" => $table[0]->SUPPLIER_ID,
                    "SUPPLIER_NAME" => $lib_supplier[$table[0]->SUPPLIER_ID],
                    "CHALLAN_PROGRAM_NO" => "",
                    "LOAN_PARTY_ID" => 0,
                    "SAMPLE_TYPE" => 0,
                    "STYLE_REF" => "",
                    "BUYER_JOB_NO" => $table[0]->JOB_NO,
                    "SERVICE_BOOKING" => "",
                    "READY_TO_APPROVED" => 0,
                    "ATTENTION" => "",
                    "SYSTEM_NUMBER" => $table[0]->YDW_NO,
                    "BOOKING_ID" => "",
                    "BOOKING_NUMBER" => $table[0]->FAB_BOOKING_NO,
                    "DEMAND_ID" => 0,
                    "RECEIVE_BASIS" => $rec_basis[$table[0]->RCV_BASIS],
                    "ISSUE_DATE" => $table[0]->BOOKING_DATE,
                    "KNITTING_COMPANY_ID" => 0,
                    "KNITTING_COMPANY" => "",
                    "BUYER_ID" => $table[0]->PO_BUYER,
                    "BUYER_NAME" => $lib_buyer[$table[0]->PO_BUYER],
                    "PRODUCTS" => $products_arr,

                ];
            }

            return $mst;
        }
    }

    public function grn_wise_yarn_data_save($response_arr)
    {
        $status = 0;
        $msg = "";


        $response_obj = json_decode($response_arr);
        $count_rfid = 0;
        $count_rfid = count($response_obj->RFID);

        $query_saved_rfid = "SELECT RFID_NO FROM RFID_YARN_DTLS WHERE PARKING_MST_ID = $response_obj->MASTER_ID and STATUS_ACTIVE = 1 and IS_DELETED = 0";
        $table_saved_rfid = $this->db->query($query_saved_rfid)->result();

        $count_saved_rfid = 0;
        $count_saved_rfid = count($table_saved_rfid);
        // print_r($response_obj->MASTER_ID);
        // die;
        $query_quarantine_park_dtls  = "SELECT * FROM QUARANTINE_PARKING_DTLS WHERE ID = $response_obj->DTLS_ID and STATUS_ACTIVE = 1 and IS_DELETED = 0";
        $table_quarantine_park_dtls = $this->db->query($query_quarantine_park_dtls)->row();

        if (empty($table_quarantine_park_dtls)) {
            $msg .= "  ***No Quanrantine dtls data Found";
            return [];
        } else {
            $msg .= "  ***Quanrantine dtls ID $table_quarantine_park_dtls->ID";
        }

        if (($table_quarantine_park_dtls->NO_OF_BAG + $table_quarantine_park_dtls->NO_OF_LOOSE_BAG) < ($count_rfid + $count_saved_rfid)) {
            $msg .= "  ***Total RFID is more than mentioned in GRN";
            return [];
        }

        $query_inv_rec_mst = "SELECT * FROM INV_RECEIVE_MASTER WHERE ID = $response_obj->MASTER_ID and STATUS_ACTIVE = 1 and IS_DELETED = 0";
        $table_inv_rec_mst = $this->db->query($query_inv_rec_mst)->row();
        if (empty($table_inv_rec_mst)) {
            $msg .= "  ***No Inv Rec mst data Found";
            return [];
        } else {
            $msg .= "  ***Inv Rec mst data ID $table_inv_rec_mst->ID";
        }
        //print_r(5);die;
        $new_trims_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", "", 1, $table_inv_rec_mst->COMPANY_ID, 'YRV', 1, date("Y", time()), 1));
        //print_r($new_trims_recv_system_id);die;


        $query_check_inv_rcv_mst = "SELECT ID,RECV_NUMBER FROM INV_RECEIVE_MASTER WHERE EMP_ID = $response_obj->MASTER_ID";
        $table_inv_old_row = $this->db->query($query_check_inv_rcv_mst)->row();

        $this->db->trans_begin();
        if (empty($table_inv_old_row)) {
            $msg .= "  ***New inv_rcv_mst system id = $new_trims_recv_system_id[0]";

            $inv_rec_mst_id = return_next_id('ID', 'INV_RECEIVE_MASTER');
            $msg .= "  ***New inv_mst_id = $inv_rec_mst_id";
            $table_inv_rec_mst->EMP_ID = $table_inv_rec_mst->ID; //grn_wo_pi_id
            $table_inv_rec_mst->RCVD_BOOK_NO = $table_inv_rec_mst->RECV_NUMBER; //grn_wo_pi_no
            $table_inv_rec_mst->ID = $inv_rec_mst_id;
            $table_inv_rec_mst->RECV_NUMBER_PREFIX_NUM = round($new_trims_recv_system_id[2]);
            $table_inv_rec_mst->RECV_NUMBER_PREFIX = $new_trims_recv_system_id[1];
            $table_inv_rec_mst->RECV_NUMBER = $new_trims_recv_system_id[0];
            $table_inv_rec_mst->ENTRY_FORM = 1;
            $table_inv_rec_mst->INSERTED_BY = $response_obj->USER_ID;
            $table_inv_rec_mst->INSERT_DATE = date("d-M-Y", time());
            $table_inv_rec_mst->IS_MULTI = 1;
            $table_inv_rec_mst->RECEIVE_BASIS = 19;
            $table_inv_rec_mst->IS_RFID = 1;


            $status = $this->insertData($table_inv_rec_mst, "INV_RECEIVE_MASTER");
        } else {
            $inv_rec_mst_id = $table_inv_old_row->ID;
            $table_inv_rec_mst->UPDATED_BY = $response_obj->USER_ID;
            $table_inv_rec_mst->UPDATE_DATE = date("d-M-Y", time());
            $this->db->query("UPDATE INV_RECEIVE_MASTER SET UPDATED_BY=$response_obj->USER_ID, UPDATE_DATE='" . date("d-M-Y h:i:s a", time()) . "' WHERE ID = $table_inv_old_row->ID");
            $msg .= "  ***Old inv_rcv_mst system id = $table_inv_old_row->RECV_NUMBER";
            $msg .= "  ***old inv_rcv_mst_id = $inv_rec_mst_id";
            //print_r("***");
        }
        //print_r($table_inv_rec_mst);die;



        //print_r($table_quarantine_park_dtls);die;
        $check_product = $this->return_product_id(
            $table_quarantine_park_dtls->YARN_COUNT,
            $table_quarantine_park_dtls->YARN_COMP_TYPE1ST,
            0,
            $table_quarantine_park_dtls->YARN_COMP_PERCENT1ST,
            0,
            $table_quarantine_park_dtls->YARN_TYPE,
            $table_quarantine_park_dtls->COLOR_NAME,
            "$table_quarantine_park_dtls->LOT",
            0,
            $table_inv_rec_mst->COMPANY_ID,
            $table_inv_rec_mst->SUPPLIER_ID,
            0,
            $table_quarantine_park_dtls->UOM,
            $table_quarantine_park_dtls->YARN_TYPE,
            $table_quarantine_park_dtls->YARN_COMP_TYPE1ST,
            $table_inv_rec_mst->RECEIVE_PURPOSE,
            0
        );

        $expString = explode("***", $check_product);
        if ($expString[0] == true && $expString[0] != "") {

            $prodMSTID = $expString[1];
            $msg .= "  ***prod_id= $prodMSTID";
        } else {
            //print_r($expString);die;
            $field_array_prod_insert = $expString[1];
            $data_array_prod_insert = $expString[2];

            $insertR = sql_insert("product_details_master", $field_array_prod_insert, $data_array_prod_insert, 0);
            $prodMSTID = $expString[3];
            $msg .= "  ***New prod_id= $prodMSTID";
        }



        //$inv_trans_id = return_next_id('ID', 'INV_TRANSACTION');

        //print_r($inv_trans_id);die;


        // $parking_save_arr = [
        //     "ID" => $inv_trans_id,
        //     "MST_ID" => $inv_rec_mst_id,
        //     "IS_EXCEL" => 1,
        //     "RECEIVE_BASIS" => 19,
        //     "PI_WO_BATCH_NO" => $table_quarantine_park_dtls->WO_PI_ID,
        //     "PI_WO_REQ_DTLS_ID" => $table_quarantine_park_dtls->WO_PI_DTLS_ID,
        //     "COMPANY_ID" => $table_inv_rec_mst->COMPANY_ID,
        //     "SUPPLIER_ID" => $table_inv_rec_mst->SUPPLIER_ID,
        //     "INSERTED_BY" =>  $response_obj->USER_ID,
        //     "TRANSACTION_TYPE" => 1,
        //     "TRANSACTION_DATE" => date("d-M-Y", time()),
        //     "INSERT_DATE" => date("d-M-Y", time()),
        //     "ENTRY_FORM" => 1,
        //     "IS_DELETED" => 0,
        //     "STATUS_ACTIVE" => 1,
        //     "PRODUCT_CODE" => $table_quarantine_park_dtls->PRODUCT_CODE,
        //     "ORDER_UOM" => $table_quarantine_park_dtls->UOM,
        //     "ORDER_QNTY" => $table_quarantine_park_dtls->PARKING_QUANTITY,
        //     "ORDER_RATE" => $table_quarantine_park_dtls->RATE,
        //     "DYE_CHARGE" => $table_quarantine_park_dtls->DYEING_CHARGE,
        //     "CONS_AVG_RATE" => $table_quarantine_park_dtls->AVG_RATE,
        //     "ORDER_AMOUNT" => $table_quarantine_park_dtls->AMOUNT,
        //     "CONS_AMOUNT" => $table_quarantine_park_dtls->CONS_AMOUNT,
        //     "NO_OF_BAGS" => $table_quarantine_park_dtls->NO_OF_BAG,
        //     "CONE_PER_BAG" => $table_quarantine_park_dtls->CONE_PER_BAG,
        //     "NO_LOOSE_CONE" => $table_quarantine_park_dtls->LOSE_CONE,
        //     "WEIGHT_PER_BAG" => $table_quarantine_park_dtls->WEIGHT_PER_BAG,
        //     "WEIGHT_PER_CONE" => $table_quarantine_park_dtls->WEIGHT_CONE,
        //     "REMARKS" => $table_quarantine_park_dtls->REMARKS,
        //     "ITEM_CATEGORY" => $table_quarantine_park_dtls->ITEM_CATEGORY_ID,
        //     "ORDER_ILE" => $table_quarantine_park_dtls->ILE_PERCENT,
        //     "STORE_ID" => $table_inv_rec_mst->STORE_ID,
        //     "FLOOR_ID" => $table_quarantine_park_dtls->FLOOR_ID,
        //     "ROOM" => $table_quarantine_park_dtls->ROOM,
        //     "RACK" => $table_quarantine_park_dtls->RACK,
        //     "BIN_BOX" => $table_quarantine_park_dtls->BIN_BOX,
        //     "GREY_QUANTITY" => $table_quarantine_park_dtls->GREY_QUANTITY,
        //     "PROD_ID" => $prodMSTID,
        //     "ORIGIN_PROD_ID" => $prodMSTID,
        //     "CONS_UOM" => $table_quarantine_park_dtls->UOM,
        //     "CONS_QUANTITY" => $table_quarantine_park_dtls->CONS_AMOUNT,
        //     "CONS_RATE" => $table_quarantine_park_dtls->AVG_RATE,
        //     "BALANCE_QNTY" => $table_quarantine_park_dtls->PARKING_QUANTITY,
        //     "BALANCE_AMOUNT" => $table_quarantine_park_dtls->CONS_AMOUNT,
        //     "ORDER_ILE_COST" => 0,
        //     "SELF" => 0,
        //     "PARKING_DTLS_ID" => $table_quarantine_park_dtls->ID,
        //     "CONS_ILE" => $table_quarantine_park_dtls->AVG_RATE * $table_quarantine_park_dtls->AVG_RATE,
        //     "CONS_ILE_COST" => $table_quarantine_park_dtls->AVG_RATE * $table_quarantine_park_dtls->AVG_RATE,
        // ];


        // $this->insertData($parking_save_arr, "INV_TRANSACTION");
        //$msg .= "  ***New trans id = $inv_trans_id";
        //print_r($parking_save_arr);die;
        $rfid_mst_id = return_next_id('ID', 'RFID_YARN_MST');
        $rfid_mst_save_obj = [
            "ID" => $rfid_mst_id,
            "RCV_MST_ID" => $inv_rec_mst_id,
            "PARKING_DTLS_ID" => $response_obj->DTLS_ID,
            "PROD_ID" => $prodMSTID,
            "ITEM_CATEGORY" => 1,
            "INSERT_DATE" => date("d-M-Y"),
            "STATUS_ACTIVE" => 1,
            "IS_DELETED" => 0,
        ];

        $this->insertData($rfid_mst_save_obj, "RFID_YARN_MST");

        $check_rfid_mst_insert_status = $this->db->query("SELECT * FROM RFID_YARN_MST WHERE ID = $rfid_mst_id")->row();
        if ($check_rfid_mst_insert_status) {
            $msg .= "  ***RFID MST inserted Successfully new ID = $rfid_mst_id";
        }
        // RFID wise loose beg weight getting
        $query_quarentine_loose_rfid = "SELECT * FROM QUARANTINE_PAR_DTLS_LOOSE_RFID WHERE MST_ID = $response_obj->MASTER_ID and DTLS_ID = $response_obj->DTLS_ID and STATUS_ACTIVE = 1 and IS_DELETED = 0";
        $table_quarentine_loose_rfid = $this->db->query($query_quarentine_loose_rfid)->result();
        $loose_rfid_weight = array();
        foreach ($table_quarentine_loose_rfid as $row) {
            $loose_rfid_weight[$row->RFID_NO] = $row->LOOSE_BAG_WEIGHT;
        }
        // End RFID wise loose beg weight getting
        $yarn_rfid_dtls_id = return_next_id('ID', 'RFID_YARN_DTLS');
        $rfid_save_arr = array();
        $weight_per_bag = 0;
        $rfid_arr = array();
        foreach ($response_obj->RFID as  $row) {
            if ($loose_rfid_weight[$row->EPCID]) {
                $weight_per_bag = $loose_rfid_weight[$row->EPCID];
            } else {
                $weight_per_bag = $table_quarantine_park_dtls->WEIGHT_PER_BAG;
            }
            $rfid_arr[$row->EPCID] = $row->EPCID;

            $rfid_save_arr[] = [
                'ID' => $yarn_rfid_dtls_id,
                'RCV_MST_ID' => $inv_rec_mst_id,
                'RFID_MST_ID' => $rfid_mst_id,
                'BAG_WEIGHT' => $weight_per_bag,
                //'TRANS_ID' => $inv_trans_id,
                'PARKING_DTLS_ID' => $table_quarantine_park_dtls->ID,
                'TRANS_TYPE' => 1,
                'ITEM_CATEGORY' => 1,
                'RFID_NO' => $row->EPCID,
                'ENTRY_FORM' => 1,
                'PARKING_MST_ID' => $response_obj->MASTER_ID,
                'INSERT_DATE' => date("d-M-Y", time()),
                'STATUS_ACTIVE' => 1,
                'IS_DELETED' => 0,
            ];
            $yarn_rfid_dtls_id++;
        }
        $rfid_str = "'" . implode("','", $rfid_arr) . "'";
        //print_r($rfid_save_arr);die;
        $status = $this->db->insert_batch("RFID_YARN_DTLS", $rfid_save_arr);

        $this->db->query("UPDATE LIB_RFID_MST set TRANS_STATUS = 1 WHERE RFID_NUMBER in($rfid_str)");

        $check_rfid_dtls_insert_status = $this->db->query("SELECT * FROM RFID_YARN_DTLS WHERE ID = $yarn_rfid_dtls_id")->row();
        if ($check_rfid_dtls_insert_status) {
            $msg .= "  ***New RFID DTLS id ends at $yarn_rfid_dtls_id";
        }


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $msg .= " ***Status = Rollbacked";
        } else {
            $this->db->trans_commit();
            //$this->db->trans_rollback();
            $msg .= " ***Status = Commited";
        }
        return $msg;
    }

    public function grn_wise_yarn_data_for_issue_save($response_arr)
    {
        //$data = $this->createObj_grn_wise_yarn_data_for_issue_save($response_arr);
        $response_arr = json_decode($response_arr);


        //print_r($response_arr->MASTER_ID);die;
        $req_no = $response_arr->MASTER_ID;
        $user_id = $response_arr->USER_ID;
        $prod_id = $response_arr->DTLS_ID;

        $requisition_query = "SELECT a.COMPANY_ID,3 as RCV_BASIS,a.BOOKING_NO,b.KNITTING_PARTY as KNITTING_COMPANY,a.BUYER_ID,c.REQUISITION_NO,c.PROD_ID,c.REQUISITION_DATE,sum(c.YARN_QNTY) as YARN_QNTY,c.ID as REQUISI_ID,b.KNITTING_SOURCE,b.KNITTING_PARTY,b.LOCATION_ID
        from PPL_PLANNING_INFO_ENTRY_MST a, PPL_PLANNING_INFO_ENTRY_DTLS b, PPL_YARN_REQUISITION_ENTRY c
        where a.ID=b.MST_ID and b.ID=c.KNIT_ID and c.requisition_no='$req_no' and c.status_active=1 and c.is_deleted=0
        group by a.company_id,a.booking_no,c.requisition_no,c.prod_id,c.requisition_date,a.buyer_id,b.KNITTING_PARTY,c.ID,b.KNITTING_SOURCE,b.KNITTING_PARTY,b.LOCATION_ID";
        //print_r($query_product);die;
        $req_table = $this->db->query($requisition_query)->row();
        

        $query_product_dtls_table = "SELECT a.COMPANY_ID, a.ID AS PROD_ID, a.LOT AS LOT_NO, a.PRODUCT_NAME_DETAILS AS PRODUCT_DETAILS, a.YARN_COUNT_ID, a.YARN_COMP_TYPE1ST, a.YARN_TYPE AS YARN_TYPE_ID, a.BRAND, a.COLOR AS COLOR_ID, a.UNIT_OF_MEASURE, a.CURRENT_STOCK, a.SUPPLIER_ID, b.STORE_ID, b.floor_id, b.room, b.rack, b.self, b.bin_box, sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) as rcv, sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as issue, SUM ( (CASE WHEN transaction_type IN (1, 4, 5) THEN cons_quantity ELSE 0 END) - (CASE WHEN transaction_type IN (2, 3, 6) THEN cons_quantity ELSE 0 END)) AS available_qnty FROM product_details_master a, inv_transaction b WHERE a.id = b.prod_id AND a.item_category_id = 1 AND b.item_category = 1 AND a.ID = $prod_id AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 GROUP BY a.company_id, a.ID, a.LOT, a.PRODUCT_NAME_DETAILS, a.YARN_COUNT_ID, a.YARN_COMP_TYPE1ST, a.YARN_TYPE, a.brand, a.COLOR, a.UNIT_OF_MEASURE, a.CURRENT_STOCK, a.SUPPLIER_ID, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box";
        $table_product_dtls = $this->db->query($query_product_dtls_table)->result();
        
        // print_r($requisition_query);
        // die;

        $cbo_company_id = $req_table->COMPANY_ID;
        $cbo_location_id = $req_table->LOCATION_ID;
        $cbo_supplier = $table_product_dtls[0]->SUPPLIER_ID;
        $cbo_buyer_name = $req_table->BUYER_ID;
        $date = date("d-M-Y");
        $cbo_knitting_source = $req_table->KNITTING_SOURCE;
        $cbo_knitting_company = $req_table->KNITTING_COMPANY;
        $txt_challan_no = 0;
        $cbo_loan_party = 0;
        $txt_remarks = 0;
        $cbo_ready_to_approved = 0;
        $txt_attention = 0;
        $pc_date_time = date("d-M-Y h:i:s a");

        $txt_req_no =  $req_table->REQUISITION_NO;
        $cbo_basis = 3;
        $supplier_id_for_tran = $table_product_dtls[0]->SUPPLIER_ID;
        $txt_prod_id = $prod_id;
        $origin_prod_id = $prod_id;
        $table_product_dtls = $table_product_dtls[0]->BRAND;

        //$field_array_mst = "id,issue_number_prefix, issue_number_prefix_num, issue_number, issue_basis, issue_purpose, entry_form, item_category, company_id, location_id, supplier_id, store_id, buyer_id, buyer_job_no, style_ref, booking_id, booking_no,service_booking_no, issue_date, sample_type, knit_dye_source, knit_dye_company, challan_no, loan_party, remarks, ready_to_approve, attention, inserted_by, insert_date";


        //$field_array_mst = "id,issue_number_prefix, issue_number_prefix_num, issue_number, issue_basis, issue_purpose, entry_form, item_category, company_id, location_id, supplier_id, store_id, buyer_id, buyer_job_no, style_ref, booking_id, booking_no,service_booking_no, issue_date, sample_type, knit_dye_source, knit_dye_company, challan_no, loan_party, remarks, ready_to_approve, attention, inserted_by, insert_date";


        //start RFID SAVE
        $rfid_save_arr = array();
        foreach ($response_arr->RFID as $row) {
            $rfid_arr[$row->EPCID] = $row->EPCID;
        }
        //print_r($rfid_arr);die;
        if ($rfid_arr) {
            $rfid_str = "'" . implode("','", $rfid_arr) . "'";
            // $query_rfid_dtls = "SELECT * FROM RFID_YARN_DTLS WHERE IS_DELETED = 0 and STATUS_ACTIVE = 1 and RFID_NO in ('$rfid_str')";
            // $table_rfid_dtls = sql_select_arr($query_rfid_dtls);

            // $count = count($table_rfid_dtls);
        }

        $query_trans_table = "SELECT b.RFID_NO,a.PROD_ID,b.BAG_WEIGHT,a.STORE_ID,a.FLOOR_ID, a.ROOM, a.RACK, a.SELF, a.BIN_BOX,a.CONS_RATE FROM inv_transaction a, rfid_yarn_dtls b WHERE a.id = b.trans_id AND a.transaction_type = 1 AND b.trans_type = 1 AND b.is_current = 1 and a.prod_id =$prod_id and b.rfid_no in($rfid_str) group by b.RFID_NO,a.PROD_ID,b.BAG_WEIGHT,a.STORE_ID, a.ROOM, a.RACK, a.SELF, a.BIN_BOX,a.CONS_RATE,a.FLOOR_ID";
        $table_trans_table = $this->db->query($query_trans_table)->result();
       
        $rfid_trans_data = array();
        $rfid_details_data = array();
        $number_of_bag = array();
        $rate_by_rfid = array();
        $check_location = array();
        $weight_per_bag = array();
        foreach ($table_trans_table as $row) {

            $rfid_details_data[$row->STORE_ID][$row->FLOOR_ID][$row->ROOM][$row->RACK][$row->SELF][$row->BIN_BOX]['NO_OF_BAG'] += 1;
            $rfid_details_data[$row->STORE_ID][$row->FLOOR_ID][$row->ROOM][$row->RACK][$row->SELF][$row->BIN_BOX]['BAG_WEIGHT'] = $row->BAG_WEIGHT;
            $rfid_details_data[$row->STORE_ID][$row->FLOOR_ID][$row->ROOM][$row->RACK][$row->SELF][$row->BIN_BOX]['CONS_RATE'] = $row->CONS_RATE;
            $rfid_details_data[$row->STORE_ID][$row->FLOOR_ID][$row->ROOM][$row->RACK][$row->SELF][$row->BIN_BOX]['RFID_NO'] = $row->RFID_NO;
        }


        $field_array_mst = "id,issue_number_prefix, issue_number_prefix_num, issue_number, issue_basis, issue_purpose, entry_form, item_category, company_id, location_id, supplier_id, store_id, buyer_id, buyer_job_no, style_ref, booking_id, booking_no,service_booking_no, issue_date, sample_type, knit_dye_source, knit_dye_company, challan_no, loan_party, remarks, ready_to_approve, attention, inserted_by, insert_date";

        //$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
        $id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master");
        $new_mrr_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", "", 1, $cbo_company_id, 'YIS', 3, date("Y", time()), 1));

        $data_array_mst = "(" . $id . ",'" . $new_mrr_number[1] . "','" . $new_mrr_number[2] . "','" . $new_mrr_number[0] . "'," . 3 . "," . 1 . ",3,1," . $cbo_company_id . "," . $cbo_location_id . "," . $cbo_supplier . "," . 0 . "," . $cbo_buyer_name . "," . 0 . "," . 0 . "," . 0 . "," . 0 . "," . 0 . ",'" . $date . "'," . 0 . "," . $cbo_knitting_source . "," . $cbo_knitting_company . "," . $txt_challan_no . "," . $cbo_loan_party . "," . $txt_remarks . "," . $cbo_ready_to_approved . "," . $txt_attention . ",'" . $user_id . "','" . $pc_date_time . "')";





        $data_array_trans = "";
        $field_array_trans = "id,mst_id,requisition_no,receive_basis,company_id,supplier_id,prod_id,origin_prod_id,dyeing_color_id,item_category,transaction_type,transaction_date,store_id,brand_id,cons_uom,cons_quantity,return_qnty,item_return_qty,cons_rate,cons_amount,no_of_bags,cone_per_bag,weight_per_bag,weight_per_cone,floor_id,room,rack,self,bin_box,using_item,job_no,inserted_by,insert_date,btb_lc_id,demand_id,demand_no,remarks,wo_id,pi_wo_batch_no,booking_no,IS_EXCEL";

        $rfid_wise_trans_id = array();

        $i = 0;
        
        foreach ($rfid_details_data as $store_id => $floor_arr) {
            foreach ($floor_arr as $floor_id => $room_arr) {
                foreach ($room_arr as $roomID => $rack_arr) {
                    foreach ($rack_arr as $rackID => $self_arr) {
                        foreach ($self_arr as $selfID => $bing_box_arr) {
                            foreach ($bing_box_arr as $bing_box_id => $bag_weight) {

                                //$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
                                $transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction");


                                $no_of_bag = $rfid_details_data[$store_id][$floor_id][$roomID][$rackID][$selfID][$bing_box_id]['NO_OF_BAG'];
                                $weight = $rfid_details_data[$store_id][$floor_id][$roomID][$rackID][$selfID][$bing_box_id]['BAG_WEIGHT'];
                                $RFID_NO = $rfid_details_data[$store_id][$floor_id][$roomID][$rackID][$selfID][$bing_box_id]['RFID_NO'];
                                $CONS_RATE = $rfid_details_data[$store_id][$floor_id][$roomID][$rackID][$selfID][$bing_box_id]['CONS_RATE'];
                                $txt_issue_qnty = ($no_of_bag * $weight);

                                $rfid_wise_trans_id[$RFID_NO] = $transactionID;

                                if ($i > 0) $data_array_trans .= ",";
                                $data_array_trans .= "(" . $transactionID . "," . $id . ",'" . $txt_req_no . "'," . $cbo_basis . "," . $cbo_company_id . ",'" . $supplier_id_for_tran . "'," . $txt_prod_id . ",'" . $origin_prod_id . "'," . 0 . ",1,2,'" . $date . "'," . $store_id . "," . 0 . "," . 0 . "," . $txt_issue_qnty . "," . 0 . "," . 0 . "," . $CONS_RATE . "," . 0 . ", $no_of_bag ," . 0 . "," . $weight . "," . 0 . "," . $floor_id . "," . $roomID . "," . $rackID . "," . $selfID . "," . $bing_box_id . "," . 1 . "," . 0 . ",'" . $user_id . "','" . $pc_date_time . "'," . 0 . "," . 0 . ",'" . 0 . "'," . 0 . "," . 0 . "," . 0 . "," . 0 . ",2)";
                                $i++;
                                //print_r($RFID_NO);die;
                                //echo "$store_id**$floor_id**$roomID**$rackID**$selfID**$bing_box_id";die;
                            }
                        }
                    }
                }
            }
        }
        
       $rfid_id =  return_next_id('ID', 'RFID_YARN_DTLS');
       //print_r($rfid_id);die;

        $rfid_save_array = array();
        foreach ($table_trans_table as $row) {
            $rfid_save_array[$row->RFID_NO] = [
                "ID" => $rfid_id,
                "RCV_MST_ID" => $id,
                "TRANS_ID" => $rfid_wise_trans_id[$row->RFID_NO],
                "TRANS_TYPE" => 2,
                "ITEM_CATEGORY" => 1,
                "RFID_NO" => $row->RFID_NO,
                "ENTRY_FORM" => 1,
                "STATUS_ACTIVE" => 1,
                "IS_DELETED" => 0,
                "BAG_WEIGHT" => $row->BAG_WEIGHT,
                "INSERT_DATE" => date("d-M-Y h:i:s a"),
            ];
            $rfid_id++;
        }




        //print_r(5);die;

        //weighted and average rate END here-------------------------//

        $this->db->trans_start();

        $rID = sql_insert("inv_issue_master", $field_array_mst, $data_array_mst, 0);


        $transID = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);



        $this->db->insert_batch("RFID_YARN_DTLS", $rfid_save_array);

        
        if ($this->db->trans_status() == TRUE) {
            $this->db->trans_commit();
            $this->db->trans_complete();
            return TRUE;
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    // public function createObj_grn_wise_yarn_data_for_issue_save($obj)
    // {
    //     // print_r($obj);
    //     // die;
    //     $obj_arr = json_decode($obj);
    //     print_r($obj_arr->PRODUCTS[0]);
    //     die;
    //     $basis_id = $obj_arr->BASIS;
    //     $req_no = $obj_arr->SYSTEM_NO;
    //     $user_id = $obj_arr->USER_ID;
    //     $dtls_id = $obj_arr->DTLS_ID;
    //     $issue_perpose = $obj_arr->ISSUE_PERPOSE;
    //     $location_id = $obj_arr->LOCATION_ID;
    //     $challan_no = $obj_arr->CHALLAN_NO;
    //     $mst_remarks = $obj_arr->MST_REMARKS;
    //     //$issue_qnty = $obj_arr->ISSUE_QNTY;
    //     $return_qty = $obj_arr->RETURN_QTY;
    //     //$no_of_bag = $obj_arr->NO_OF_BAG;
    //     $no_of_cone = $obj_arr->NO_OF_CONE;
    //     $weight_per_bag = $obj_arr->WEIGHT_PER_BAG;
    //     $weight_per_cone = $obj_arr->WEIGHT_PER_CONE;
    //     $ready_to_approved = $obj_arr->READY_TO_APPROVED;
    //     $attention = $obj_arr->ATTENTION;
    //     $dtls_remarks = $obj_arr->DTLS_REMARKS;
    //     //print_r($basis_id);die;
    //     $rfid_arr = array();
    //     foreach ($obj_arr->RFID as $row) {
    //         $rfid_arr[$row->EPCID] = $row->EPCID;
    //     }
    //     $rfids_str = "'" . implode("','", $rfid_arr) . "'";
    //     $query_rfid = "SELECT RFID_NO,BAG_WEIGHT FROM RFID_YARN_DTLS WHERE RFID_NO in ($rfids_str) and TRANS_TYPE = 1 and STATUS_ACTIVE = 1";
    //     $table_rfid = $this->db->query($query_rfid)->result();
    //     $no_of_bag = count($rfid_arr);
    //     $issue_qnty = 0;
    //     foreach ($table_rfid as $row) {
    //         $issue_qnty += $row->BAG_WEIGHT;
    //     }
    //     // print_r($issue_qnty_2);
    //     // die;


    //     if ($basis_id == 3) {
    //         // $query_product = "SELECT a.PROD_ID,c.COMPANY_ID,c.LOCATION_ID,c.RECEIVE_BASIS,a.ID,a.REQUISITION_NO,c.ORDER_UOM,b.LOT,b.PRODUCT_NAME_DETAILS,b.YARN_TYPE,b.COLOR,SUM(a.YARN_QNTY) as REQ_QNTY,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,b.SUPPLIER_ID as PRO_SUPPLIER_ID,c.STORE_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,b.YARN_COUNT_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID FROM PPL_YARN_REQUISITION_ENTRY a,PRODUCT_DETAILS_MASTER b,INV_TRANSACTION c WHERE a.STATUS_ACTIVE = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0 and c.STATUS_ACTIVE = 1 and c.IS_DELETED = 0 and a.PROD_ID = $dtls_id
    //         // and a.PROD_ID = b.ID and c.REQUISITION_NO = a.REQUISITION_NO group by b.LOT,b.PRODUCT_NAME_DETAILS,b.COLOR,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,a.REQUISITION_NO,a.ID,c.COMPANY_ID,c.RECEIVE_BASIS,a.PROD_ID,c.ORDER_UOM,c.LOCATION_ID,c.STORE_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.YARN_TYPE,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID,b.SUPPLIER_ID,b.YARN_COUNT_ID";
    //         // $table_product = $this->db->query($query_product)->row();
    //         //print_r($table_product);die;
    //         $requisition_query = "SELECT a.COMPANY_ID,3 as RCV_BASIS,a.BOOKING_NO,b.KNITTING_PARTY as KNITTING_COMPANY,a.BUYER_ID,c.REQUISITION_NO,c.prod_id,c.REQUISITION_DATE,sum(c.YARN_QNTY) as YARN_QNTY,c.ID as REQUISI_ID,b.KNITTING_SOURCE,b.KNITTING_PARTY
    //         from PPL_PLANNING_INFO_ENTRY_MST a, PPL_PLANNING_INFO_ENTRY_DTLS b, PPL_YARN_REQUISITION_ENTRY c
    //         where a.ID=b.MST_ID and b.ID=c.KNIT_ID 
    //         and c.requisition_no='$req_no' 
    //         and c.status_active=1 and c.is_deleted=0
    //         group by a.company_id,a.booking_no,c.requisition_no,c.prod_id,c.requisition_date,a.buyer_id,b.KNITTING_PARTY,c.ID,b.KNITTING_SOURCE,b.KNITTING_PARTY";
    //         //print_r($query_product);die;
    //         $table = $this->db->query($requisition_query)->row();
    //         //print_r($query_product);die;
    //         $poId_issueQnty_returnQnty = "";
    //         $poIds = "";

    //         return  $response_arr = [
    //             "db_type" => 1,
    //             "operation" => 0,
    //             "user_id" => $user_id,
    //             "txt_system_no" => "NULL",
    //             "cbo_company_id" => $obj_arr->PRODUCTS[0]->COMPANY_ID,
    //             "cbo_basis" => 3,
    //             "cbo_issue_purpose" => $issue_perpose,
    //             "txt_issue_date" => date('d-M-Y'),
    //             "txt_booking_no" => "NULL",
    //             "txt_booking_id" => "NULL",
    //             "cbo_location_id" => $location_id,
    //             "cbo_knitting_source" => $table->KNITTING_SOURCE,
    //             "cbo_knitting_company" => $table->KNITTING_COMPANY,
    //             "cbo_supplier" => $table->SUPPLIER_ID,
    //             "txt_challan_no" => $challan_no,
    //             "cbo_loan_party" => 0,
    //             "cbo_buyer_name" => $table->BUYER_ID,
    //             "txt_style_ref" => "NULL",
    //             "txt_buyer_job_no" => "NULL",
    //             "cbo_sample_type" => "0",
    //             "txt_remarks" => $mst_remarks,
    //             "txt_req_no" => $table->REQUISITION_NO,
    //             "txt_lot_no" => $obj_arr->PRODUCTS[0]->LOT_NO,
    //             "cbo_yarn_count" => $obj_arr->PRODUCTS[0]->YARN_COUNT_ID,
    //             "cbo_color" => 0,
    //             "cbo_store_name" => $obj_arr->PRODUCTS[0]->STORE_ID,
    //             "cbo_floor" => $obj_arr->PRODUCTS[0]->FLOOR_ID,
    //             "cbo_room" => $obj_arr->PRODUCTS[0]->ROOM,
    //             "txt_rack" => $obj_arr->PRODUCTS[0]->RACK,
    //             "txt_shelf" => $obj_arr->PRODUCTS[0]->SELF,
    //             "cbo_bin" => $obj_arr->PRODUCTS[0]->BIN_BOX,
    //             "txt_issue_qnty" => $issue_qnty,
    //             "txt_returnable_qty" => $return_qty,
    //             "txt_composition" => $obj_arr->PRODUCTS[0]->YARN_COMP_TYPE1ST,
    //             "cbo_brand" => $obj_arr->PRODUCTS[0]->BRAND,
    //             "txt_no_bag" => $no_of_bag,
    //             "txt_no_cone" => $no_of_cone,
    //             "txt_weight_per_bag" => $weight_per_bag,
    //             "txt_weight_per_cone" => $weight_per_cone,
    //             "cbo_yarn_type" => $obj_arr->PRODUCTS[0]->YARN_TYPE_ID,
    //             "cbo_dyeing_color" => 0,
    //             "txt_current_stock" => $obj_arr->PRODUCTS[0]->AVAILABLE_QNTY,
    //             "cbo_uom" => $obj_arr->PRODUCTS[0]->UNIT_OF_MEASURE,
    //             "cbo_item" => 1,
    //             "update_id_mst" => 0,
    //             "update_id" => "NULL",
    //             "save_data" => $poId_issueQnty_returnQnty,
    //             "all_po_id" => $poIds,
    //             "txt_prod_id" => $obj_arr->PRODUCTS[0]->PROD_ID,
    //             "job_no" => "NULL",
    //             "cbo_ready_to_approved" => $ready_to_approved,
    //             "cbo_supplier_lot" => $obj_arr->PRODUCTS[0]->SUPPLIER_ID,
    //             "txt_btb_lc_id" => "NULL",
    //             "extra_quantity" => 0,
    //             "txt_entry_form" => 0,
    //             "hidden_p_issue_qnty" => 0,
    //             "hdn_wo_qnty" => "NULL",
    //             "txt_service_booking_no" => "NULL",
    //             "demand_id" => 0,
    //             "hdn_req_no" => $table->REQUISITION_NO,
    //             "original_save_data" => "NULL",
    //             "saved_knitting_company" => $table->KNITTING_PARTY,
    //             "txt_attention" => $attention,
    //             "txt_remarks_dtls" => $dtls_remarks,
    //             "txt_wo_id" => "NULL",
    //             "txt_pi_id" => "NULL",
    //             "hdn_fabric_booking_no" => "NULL",
    //         ];
    //         //print_r($response_arr);die;


    //     } else if ($basis_id == 8) {
    //         // print_r(5);
    //         // die;
    //         $demand_query = "SELECT a.COMPANY_ID,3 as RCV_BASIS,a.BOOKING_NO,b.KNITTING_PARTY as KNITTING_COMPANY,a.BUYER_ID,c.REQUISITION_NO,c.PROD_ID,c.REQUISITION_DATE,sum(c.YARN_QNTY) as YARN_QNTY,c.ID as REQUISI_ID,b.KNITTING_SOURCE,b.KNITTING_PARTY,e.DEMAND_SYSTEM_NO,e.ID as DEMAND_ID,e.KNITTING_COMPANY
    //             from PPL_PLANNING_INFO_ENTRY_MST a, PPL_PLANNING_INFO_ENTRY_DTLS b, PPL_YARN_REQUISITION_ENTRY c,PPL_YARN_DEMAND_ENTRY_DTLS d,PPL_YARN_DEMAND_ENTRY_MST e
    //             where a.ID=b.MST_ID and b.ID=c.KNIT_ID and d.REQUISITION_NO = c.requisition_no and e.ID = d.MST_ID
    //             --and c.requisition_no='$req_no' 
    //             and e.DEMAND_SYSTEM_NO = '$req_no'
    //             and c.status_active=1 and c.is_deleted=0
    //             group by a.company_id,a.booking_no,c.requisition_no,c.prod_id,c.requisition_date,a.buyer_id,b.KNITTING_PARTY,c.ID,b.KNITTING_SOURCE,b.KNITTING_PARTY,e.DEMAND_SYSTEM_NO,e.ID,e.KNITTING_COMPANY";

    //         //print_r($demand_query);die;
    //         $table = $this->db->query($demand_query)->row();
    //         //print_r($table->DEMAND_ID);die;
    //         $prod_id_arr = array();
    //         foreach ($table as $row) {
    //             $prod_id_arr[$row->PROD_ID] = $row->PROD_ID;
    //         }
    //         $prod_ids_str = implode(',', $prod_id_arr);
    //         $req_no = $table->DEMAND_ID;

    //         $query_product = "SELECT a.PROD_ID,c.COMPANY_ID,c.LOCATION_ID,c.RECEIVE_BASIS,a.ID,a.REQUISITION_NO,c.ORDER_UOM,b.LOT,b.PRODUCT_NAME_DETAILS,b.YARN_TYPE,b.COLOR,SUM(a.YARN_QNTY) as REQ_QNTY,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,b.SUPPLIER_ID as PRO_SUPPLIER_ID,c.STORE_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,b.YARN_COUNT_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID FROM PPL_YARN_REQUISITION_ENTRY a,PRODUCT_DETAILS_MASTER b,INV_TRANSACTION c WHERE a.STATUS_ACTIVE = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0 and c.STATUS_ACTIVE = 1 and c.IS_DELETED = 0 and a.PROD_ID = $dtls_id
    //             and a.PROD_ID = b.ID and c.REQUISITION_NO = a.REQUISITION_NO group by b.LOT,b.PRODUCT_NAME_DETAILS,b.COLOR,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,a.REQUISITION_NO,a.ID,c.COMPANY_ID,c.RECEIVE_BASIS,a.PROD_ID,c.ORDER_UOM,c.LOCATION_ID,c.STORE_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.YARN_TYPE,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID,b.SUPPLIER_ID,b.YARN_COUNT_ID";

    //         $table_product = $this->db->query($query_product)->row();

    //         $poId_issueQnty_returnQnty = "";
    //         $poIds = "";

    //         return $response_arr = [
    //             "db_type" => 1,
    //             "operation" => 0,
    //             "user_id" => $user_id,
    //             "txt_system_no" => "NULL",
    //             "cbo_company_id" => $table_product->COMPANY_ID,
    //             "cbo_basis" => $table_product->RECEIVE_BASIS,
    //             "cbo_issue_purpose" => $issue_perpose,
    //             "txt_issue_date" => date('d-M-Y', time()),
    //             "txt_booking_no" => "NULL",
    //             "txt_booking_id" => "NULL",
    //             "cbo_location_id" => $location_id,
    //             "cbo_knitting_source" => $table->KNITTING_SOURCE,
    //             "cbo_knitting_company" => $table->KNITTING_COMPANY,
    //             "cbo_supplier" => $table->SUPPLIER_ID,
    //             "cbo_store_name" => $table->STORE_ID,
    //             "txt_challan_no" => $challan_no,
    //             "cbo_loan_party" => 0,
    //             "cbo_buyer_name" => $table->BUYER_ID,
    //             "txt_style_ref" => "NULL",
    //             "txt_buyer_job_no" => "NULL",
    //             "cbo_sample_type" => "0",
    //             "txt_remarks" => $mst_remarks,
    //             "txt_req_no" => $table->DEMAND_SYSTEM_NO,
    //             "txt_lot_no" => $table_product->LOT,
    //             "cbo_yarn_count" => $table_product->YARN_COUNT_ID,
    //             "cbo_color" => 0,
    //             "cbo_floor" => $table_product->FLOOR_ID,
    //             "cbo_room" => $table_product->ROOM,
    //             "txt_issue_qnty" => $issue_qnty,
    //             "txt_returnable_qty" => $return_qty,
    //             "txt_composition" => $table_product->YARN_COMP_TYPE1ST,
    //             "cbo_brand" => $table_product->BRAND,
    //             "txt_rack" => $table_product->RACK,
    //             "txt_no_bag" => $no_of_bag,
    //             "txt_no_cone" => $no_of_cone,
    //             "txt_weight_per_bag" => $weight_per_bag,
    //             "txt_weight_per_cone" => $weight_per_cone,
    //             "cbo_yarn_type" => $table_product->YARN_TYPE,
    //             "cbo_dyeing_color" => 0,
    //             "txt_shelf" => $table_product->SELF,
    //             "txt_current_stock" => $table_product->CURRENT_STOCK,
    //             "cbo_uom" => $table_product->UNIT_OF_MEASURE,
    //             "cbo_item" => $table_product->ITEM_CATEGORY_ID,
    //             "update_id_mst" => 0,
    //             "update_id" => "NULL",
    //             "save_data" => $poId_issueQnty_returnQnty,
    //             "all_po_id" => $poIds,
    //             "txt_prod_id" => $table_product->PROD_ID,
    //             "job_no" => "NULL",
    //             "cbo_ready_to_approved" => $ready_to_approved,
    //             "cbo_supplier_lot" => $table_product->PRO_SUPPLIER_ID,
    //             "txt_btb_lc_id" => "NULL",
    //             "extra_quantity" => 0,
    //             "txt_entry_form" => 0,
    //             "hidden_p_issue_qnty" => 0,
    //             "hdn_wo_qnty" => "NULL",
    //             "txt_service_booking_no" => "NULL",
    //             "demand_id" => $table->DEMAND_ID,
    //             "hdn_req_no" => $table->REQUISITION_NO,
    //             "original_save_data" => "NULL",
    //             "cbo_bin" => $table_product->BIN_BOX,
    //             "saved_knitting_company" => $table->KNITTING_PARTY,
    //             "txt_attention" => $attention,
    //             "txt_remarks_dtls" => $dtls_remarks,
    //             "txt_wo_id" => "NULL",
    //             "txt_pi_id" => "NULL",
    //             "hdn_fabric_booking_no" => "NULL",
    //         ];
    //     } else if ($basis_id == 1) {

    //         $requisition_query = "SELECT a.COMPANY_ID, 1 as RCV_BASIS,a.BOOKING_DATE,a.SUPPLIER_ID,b.PRODUCT_ID,e.PO_BUYER,b.YARN_COLOR,a.SUPPLIER_ID,a.YDW_NO,a.ENTRY_FORM,b.JOB_NO,c.BUYER_NAME,a.SOURCE from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b left join wo_po_details_master c on b.job_no_id=c.id left join wo_non_ord_samp_booking_mst d on b.booking_no=d.booking_no left join fabric_sales_order_mst e on e.job_no=b.job_no 
    //             where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
    //             and a.ydw_no = '$req_no' 
    //             and a.entry_form in(41,42,114,125,135)";
    //         //print_r($requisition_query);die;
    //         $table = $this->db->query($requisition_query)->result();


    //         $prod_id_arr = array();
    //         foreach ($table as $row) {
    //             $prod_id_arr[$row->PRODUCT_ID] = $row->PRODUCT_ID;
    //         }
    //         $prod_ids_str = implode(',', $prod_id_arr);
    //         //$req_no = $table[0]->DEMAND_ID;
    //         $query_product = "SELECT a.PROD_ID,c.COMPANY_ID,c.LOCATION_ID,c.RECEIVE_BASIS,a.ID,a.REQUISITION_NO,c.ORDER_UOM,b.LOT,b.PRODUCT_NAME_DETAILS,b.YARN_TYPE,b.COLOR,SUM(a.YARN_QNTY) as REQ_QNTY,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,b.SUPPLIER_ID as PRO_SUPPLIER_ID,c.STORE_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,b.YARN_COUNT_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID FROM PPL_YARN_REQUISITION_ENTRY a,PRODUCT_DETAILS_MASTER b,INV_TRANSACTION c WHERE a.STATUS_ACTIVE = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0 and c.STATUS_ACTIVE = 1 and c.IS_DELETED = 0 and a.PROD_ID = $dtls_id
    //             and a.PROD_ID = b.ID and c.REQUISITION_NO = a.REQUISITION_NO group by b.LOT,b.PRODUCT_NAME_DETAILS,b.COLOR,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,a.REQUISITION_NO,a.ID,c.COMPANY_ID,c.RECEIVE_BASIS,a.PROD_ID,c.ORDER_UOM,c.LOCATION_ID,c.STORE_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.YARN_TYPE,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID,b.SUPPLIER_ID,b.YARN_COUNT_ID";
    //         //print_r($query_product);die;
    //         $table_product = $this->db->query($query_product)->result();

    //         $poId_issueQnty_returnQnty = "";
    //         $poIds = "";

    //         return $response_arr = [
    //             "db_type" => 1,
    //             "operation" => 0,
    //             "user_id" => $user_id,
    //             "txt_system_no" => "NULL",
    //             "cbo_company_id" => $table_product->COMPANY_ID,
    //             "cbo_basis" => $table_product->RECEIVE_BASIS,
    //             "cbo_issue_purpose" => $issue_perpose,
    //             "txt_issue_date" => date('d-M-Y', time()),
    //             "txt_booking_no" => $table_product->YDW_NO,
    //             "txt_booking_id" => $table_product->YDW_ID,
    //             "cbo_location_id" => $location_id,
    //             "cbo_knitting_source" => $table->SOURCE,
    //             "cbo_knitting_company" => $table->SUPPLIER_ID,
    //             "cbo_supplier" => $table->SUPPLIER_ID,
    //             "cbo_store_name" => ($table->STORE_ID) ? $table->STORE_ID : 0,
    //             "txt_challan_no" => $challan_no,
    //             "cbo_loan_party" => 0,
    //             "cbo_buyer_name" => $table->BUYER_NAME,
    //             "txt_style_ref" => "NULL",
    //             "txt_buyer_job_no" => $table->JOB_NO,
    //             "cbo_sample_type" => "0",
    //             "txt_remarks" => $mst_remarks,
    //             "txt_req_no" => $table->REQUISITION_NO,
    //             "txt_lot_no" => $table_product->LOT,
    //             "cbo_yarn_count" => $table_product->YARN_COUNT_ID,
    //             "cbo_color" => 0,
    //             "cbo_floor" => $table_product->FLOOR_ID,
    //             "cbo_room" => $table_product->ROOM,
    //             "txt_issue_qnty" => $issue_qnty,
    //             "txt_returnable_qty" => $return_qty,
    //             "txt_composition" => $table_product->YARN_COMP_TYPE1ST,
    //             "cbo_brand" => $table_product->BRAND,
    //             "txt_rack" => $table_product->RACK,
    //             "txt_no_bag" => $no_of_bag,
    //             "txt_no_cone" => $no_of_cone,
    //             "txt_weight_per_bag" => $weight_per_bag,
    //             "txt_weight_per_cone" => $weight_per_cone,
    //             "cbo_yarn_type" => $table_product->YARN_TYPE,
    //             "cbo_dyeing_color" => 0,
    //             "txt_shelf" => $table_product->SELF,
    //             "txt_current_stock" => $table_product->CURRENT_STOCK,
    //             "cbo_uom" => $table_product->UNIT_OF_MEASURE,
    //             "cbo_item" => $table_product->ITEM_CATEGORY_ID,
    //             "update_id_mst" => 0,
    //             "update_id" => "NULL",
    //             "save_data" => $poId_issueQnty_returnQnty,
    //             "all_po_id" => $poIds,
    //             "txt_prod_id" => $table_product->PROD_ID,
    //             "job_no" => $table->JOB_NO,
    //             "cbo_ready_to_approved" => $ready_to_approved,
    //             "cbo_supplier_lot" => $table_product->PRO_SUPPLIER_ID,
    //             "txt_btb_lc_id" => "NULL",
    //             "extra_quantity" => 0,
    //             "txt_entry_form" => $table->ENTRY_FORM,
    //             "hidden_p_issue_qnty" => 0,
    //             "hdn_wo_qnty" => "NULL",
    //             "txt_service_booking_no" => "NULL",
    //             "demand_id" => 0,
    //             "hdn_req_no" => $table->YDW_NO,
    //             "original_save_data" => "NULL",
    //             "cbo_bin" => $table_product->BIN_BOX,
    //             "saved_knitting_company" => $table->SUPPLIER_ID,
    //             "txt_attention" => $attention,
    //             "txt_remarks_dtls" => $dtls_remarks,
    //             "txt_wo_id" => "NULL",
    //             "txt_pi_id" => "NULL",
    //             "hdn_fabric_booking_no" => "NULL",
    //         ];
    //     }

    //     //basis = 1 
    //     // $response_arr = '{
    //     // 	--"db_type": 1,
    //     // 	--"operation": 0, --default 0
    //     // 	"user_id": 1,
    //     // 	--"cbo_company_id": "1", --company_id from trans table
    //     // 	--"cbo_basis": "3", --inv master table er basis 
    //     // 	--"txt_issue_date": "26-Dec-2023", --Today date gone to trans table
    //     // 	**--"txt_booking_no": "Null", -- WO_YARN_DYEING_MST er YDW_NO
    //     // 	**--"txt_booking_id": "Null",-- WO_YARN_DYEING_MST er ID
    //     // 	--"cbo_location_id": "108", -- ppl_planning_info_entry_dtls er location id, if not found then user will give it. Knitting source "inhouse" hole location lagbei. ppl_planning_info_entry_dtls this table give the knitting source column
    //     // 	--"cbo_knitting_source": "1", -- ppl_planning_info_entry_dtls er knitting source
    //     // }';
    // }

    public function createObj_grn_wise_yarn_data_for_issue_save($obj)
    {
        // print_r($obj);
        // die;
        $obj_arr = json_decode($obj);
        print_r($obj_arr->PRODUCTS[0]);
        die;
        $basis_id = $obj_arr->BASIS;
        $req_no = $obj_arr->SYSTEM_NO;
        $user_id = $obj_arr->USER_ID;
        $dtls_id = $obj_arr->DTLS_ID;
        $issue_perpose = $obj_arr->ISSUE_PERPOSE;
        $location_id = $obj_arr->LOCATION_ID;
        $challan_no = $obj_arr->CHALLAN_NO;
        $mst_remarks = $obj_arr->MST_REMARKS;
        //$issue_qnty = $obj_arr->ISSUE_QNTY;
        $return_qty = $obj_arr->RETURN_QTY;
        //$no_of_bag = $obj_arr->NO_OF_BAG;
        $no_of_cone = $obj_arr->NO_OF_CONE;
        $weight_per_bag = $obj_arr->WEIGHT_PER_BAG;
        $weight_per_cone = $obj_arr->WEIGHT_PER_CONE;
        $ready_to_approved = $obj_arr->READY_TO_APPROVED;
        $attention = $obj_arr->ATTENTION;
        $dtls_remarks = $obj_arr->DTLS_REMARKS;
        //print_r($basis_id);die;
        $rfid_arr = array();
        foreach ($obj_arr->RFID as $row) {
            $rfid_arr[$row->EPCID] = $row->EPCID;
        }
        $rfids_str = "'" . implode("','", $rfid_arr) . "'";
        $query_rfid = "SELECT RFID_NO,BAG_WEIGHT FROM RFID_YARN_DTLS WHERE RFID_NO in ($rfids_str) and TRANS_TYPE = 1 and STATUS_ACTIVE = 1";
        $table_rfid = $this->db->query($query_rfid)->result();
        $no_of_bag = count($rfid_arr);
        $issue_qnty = 0;
        foreach ($table_rfid as $row) {
            $issue_qnty += $row->BAG_WEIGHT;
        }
        // print_r($issue_qnty_2);
        // die;


        if ($basis_id == 3) {

            // $table_product = $this->db->query($query_product)->row();
            //print_r($table_product);die;
            $requisition_query = "SELECT a.COMPANY_ID,3 as RCV_BASIS,a.BOOKING_NO,b.KNITTING_PARTY as KNITTING_COMPANY,a.BUYER_ID,c.REQUISITION_NO,c.prod_id,c.REQUISITION_DATE,sum(c.YARN_QNTY) as YARN_QNTY,c.ID as REQUISI_ID,b.KNITTING_SOURCE,b.KNITTING_PARTY
            from PPL_PLANNING_INFO_ENTRY_MST a, PPL_PLANNING_INFO_ENTRY_DTLS b, PPL_YARN_REQUISITION_ENTRY c
            where a.ID=b.MST_ID and b.ID=c.KNIT_ID 
            and c.requisition_no='$req_no' 
            and c.status_active=1 and c.is_deleted=0
            group by a.company_id,a.booking_no,c.requisition_no,c.prod_id,c.requisition_date,a.buyer_id,b.KNITTING_PARTY,c.ID,b.KNITTING_SOURCE,b.KNITTING_PARTY";
            //print_r($query_product);die;
            $table = $this->db->query($requisition_query)->row();

            $query_product_dtls_table = "SELECT a.company_id, a.ID AS PROD_ID, a.LOT AS LOT_NO, a.PRODUCT_NAME_DETAILS AS PRODUCT_DETAILS, a.YARN_COUNT_ID, a.YARN_COMP_TYPE1ST, a.YARN_TYPE AS YARN_TYPE_ID, a.brand, a.COLOR AS COLOR_ID, a.UNIT_OF_MEASURE, a.CURRENT_STOCK, a.SUPPLIER_ID, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) as rcv, sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as issue, SUM ( (CASE WHEN transaction_type IN (1, 4, 5) THEN cons_quantity ELSE 0 END) - (CASE WHEN transaction_type IN (2, 3, 6) THEN cons_quantity ELSE 0 END)) AS available_qnty FROM product_details_master a, inv_transaction b WHERE a.id = b.prod_id AND a.item_category_id = 1 AND b.item_category = 1 AND a.ID IN ($prod_ids_str) AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 GROUP BY a.company_id, a.ID, a.LOT, a.PRODUCT_NAME_DETAILS, a.YARN_COUNT_ID, a.YARN_COMP_TYPE1ST, a.YARN_TYPE, a.brand, a.COLOR, a.UNIT_OF_MEASURE, a.CURRENT_STOCK, a.SUPPLIER_ID, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box";
            $table_product_dtls = $this->db->query($query_product_dtls_table)->result();
            //print_r($query_product);die;
            $poId_issueQnty_returnQnty = "";
            $poIds = "";

            return  $response_arr = [
                "db_type" => 1,
                "operation" => 0,
                "user_id" => $user_id,
                "txt_system_no" => "NULL",
                "cbo_company_id" => $obj_arr->PRODUCTS[0]->COMPANY_ID,
                "cbo_basis" => 3,
                "cbo_issue_purpose" => $issue_perpose,
                "txt_issue_date" => date('d-M-Y'),
                "txt_booking_no" => "NULL",
                "txt_booking_id" => "NULL",
                "cbo_location_id" => $location_id,
                "cbo_knitting_source" => $table->KNITTING_SOURCE,
                "cbo_knitting_company" => $table->KNITTING_COMPANY,
                "cbo_supplier" => $table->SUPPLIER_ID,
                "txt_challan_no" => $challan_no,
                "cbo_loan_party" => 0,
                "cbo_buyer_name" => $table->BUYER_ID,
                "txt_style_ref" => "NULL",
                "txt_buyer_job_no" => "NULL",
                "cbo_sample_type" => "0",
                "txt_remarks" => $mst_remarks,
                "txt_req_no" => $table->REQUISITION_NO,
                "txt_lot_no" => $obj_arr->PRODUCTS[0]->LOT_NO,
                "cbo_yarn_count" => $obj_arr->PRODUCTS[0]->YARN_COUNT_ID,
                "cbo_color" => 0,
                "cbo_store_name" => $obj_arr->PRODUCTS[0]->STORE_ID,
                "cbo_floor" => $obj_arr->PRODUCTS[0]->FLOOR_ID,
                "cbo_room" => $obj_arr->PRODUCTS[0]->ROOM,
                "txt_rack" => $obj_arr->PRODUCTS[0]->RACK,
                "txt_shelf" => $obj_arr->PRODUCTS[0]->SELF,
                "cbo_bin" => $obj_arr->PRODUCTS[0]->BIN_BOX,
                "txt_issue_qnty" => $issue_qnty,
                "txt_returnable_qty" => $return_qty,
                "txt_composition" => $obj_arr->PRODUCTS[0]->YARN_COMP_TYPE1ST,
                "cbo_brand" => $obj_arr->PRODUCTS[0]->BRAND,
                "txt_no_bag" => $no_of_bag,
                "txt_no_cone" => $no_of_cone,
                "txt_weight_per_bag" => $weight_per_bag,
                "txt_weight_per_cone" => $weight_per_cone,
                "cbo_yarn_type" => $obj_arr->PRODUCTS[0]->YARN_TYPE_ID,
                "cbo_dyeing_color" => 0,
                "txt_current_stock" => $obj_arr->PRODUCTS[0]->AVAILABLE_QNTY,
                "cbo_uom" => $obj_arr->PRODUCTS[0]->UNIT_OF_MEASURE,
                "cbo_item" => 1,
                "update_id_mst" => 0,
                "update_id" => "NULL",
                "save_data" => $poId_issueQnty_returnQnty,
                "all_po_id" => $poIds,
                "txt_prod_id" => $obj_arr->PRODUCTS[0]->PROD_ID,
                "job_no" => "NULL",
                "cbo_ready_to_approved" => $ready_to_approved,
                "cbo_supplier_lot" => $obj_arr->PRODUCTS[0]->SUPPLIER_ID,
                "txt_btb_lc_id" => "NULL",
                "extra_quantity" => 0,
                "txt_entry_form" => 0,
                "hidden_p_issue_qnty" => 0,
                "hdn_wo_qnty" => "NULL",
                "txt_service_booking_no" => "NULL",
                "demand_id" => 0,
                "hdn_req_no" => $table->REQUISITION_NO,
                "original_save_data" => "NULL",
                "saved_knitting_company" => $table->KNITTING_PARTY,
                "txt_attention" => $attention,
                "txt_remarks_dtls" => $dtls_remarks,
                "txt_wo_id" => "NULL",
                "txt_pi_id" => "NULL",
                "hdn_fabric_booking_no" => "NULL",
            ];
            //print_r($response_arr);die;


        } else if ($basis_id == 8) {
            // print_r(5);
            // die;
            $demand_query = "SELECT a.COMPANY_ID,3 as RCV_BASIS,a.BOOKING_NO,b.KNITTING_PARTY as KNITTING_COMPANY,a.BUYER_ID,c.REQUISITION_NO,c.PROD_ID,c.REQUISITION_DATE,sum(c.YARN_QNTY) as YARN_QNTY,c.ID as REQUISI_ID,b.KNITTING_SOURCE,b.KNITTING_PARTY,e.DEMAND_SYSTEM_NO,e.ID as DEMAND_ID,e.KNITTING_COMPANY
                from PPL_PLANNING_INFO_ENTRY_MST a, PPL_PLANNING_INFO_ENTRY_DTLS b, PPL_YARN_REQUISITION_ENTRY c,PPL_YARN_DEMAND_ENTRY_DTLS d,PPL_YARN_DEMAND_ENTRY_MST e
                where a.ID=b.MST_ID and b.ID=c.KNIT_ID and d.REQUISITION_NO = c.requisition_no and e.ID = d.MST_ID
                --and c.requisition_no='$req_no' 
                and e.DEMAND_SYSTEM_NO = '$req_no'
                and c.status_active=1 and c.is_deleted=0
                group by a.company_id,a.booking_no,c.requisition_no,c.prod_id,c.requisition_date,a.buyer_id,b.KNITTING_PARTY,c.ID,b.KNITTING_SOURCE,b.KNITTING_PARTY,e.DEMAND_SYSTEM_NO,e.ID,e.KNITTING_COMPANY";

            //print_r($demand_query);die;
            $table = $this->db->query($demand_query)->row();
            //print_r($table->DEMAND_ID);die;
            $prod_id_arr = array();
            foreach ($table as $row) {
                $prod_id_arr[$row->PROD_ID] = $row->PROD_ID;
            }
            $prod_ids_str = implode(',', $prod_id_arr);
            $req_no = $table->DEMAND_ID;

            $query_product = "SELECT a.PROD_ID,c.COMPANY_ID,c.LOCATION_ID,c.RECEIVE_BASIS,a.ID,a.REQUISITION_NO,c.ORDER_UOM,b.LOT,b.PRODUCT_NAME_DETAILS,b.YARN_TYPE,b.COLOR,SUM(a.YARN_QNTY) as REQ_QNTY,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,b.SUPPLIER_ID as PRO_SUPPLIER_ID,c.STORE_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,b.YARN_COUNT_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID FROM PPL_YARN_REQUISITION_ENTRY a,PRODUCT_DETAILS_MASTER b,INV_TRANSACTION c WHERE a.STATUS_ACTIVE = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0 and c.STATUS_ACTIVE = 1 and c.IS_DELETED = 0 and a.PROD_ID = $dtls_id
                and a.PROD_ID = b.ID and c.REQUISITION_NO = a.REQUISITION_NO group by b.LOT,b.PRODUCT_NAME_DETAILS,b.COLOR,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,a.REQUISITION_NO,a.ID,c.COMPANY_ID,c.RECEIVE_BASIS,a.PROD_ID,c.ORDER_UOM,c.LOCATION_ID,c.STORE_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.YARN_TYPE,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID,b.SUPPLIER_ID,b.YARN_COUNT_ID";

            $table_product = $this->db->query($query_product)->row();

            $poId_issueQnty_returnQnty = "";
            $poIds = "";

            return $response_arr = [
                "db_type" => 1,
                "operation" => 0,
                "user_id" => $user_id,
                "txt_system_no" => "NULL",
                "cbo_company_id" => $table_product->COMPANY_ID,
                "cbo_basis" => $table_product->RECEIVE_BASIS,
                "cbo_issue_purpose" => $issue_perpose,
                "txt_issue_date" => date('d-M-Y', time()),
                "txt_booking_no" => "NULL",
                "txt_booking_id" => "NULL",
                "cbo_location_id" => $location_id,
                "cbo_knitting_source" => $table->KNITTING_SOURCE,
                "cbo_knitting_company" => $table->KNITTING_COMPANY,
                "cbo_supplier" => $table->SUPPLIER_ID,
                "cbo_store_name" => $table->STORE_ID,
                "txt_challan_no" => $challan_no,
                "cbo_loan_party" => 0,
                "cbo_buyer_name" => $table->BUYER_ID,
                "txt_style_ref" => "NULL",
                "txt_buyer_job_no" => "NULL",
                "cbo_sample_type" => "0",
                "txt_remarks" => $mst_remarks,
                "txt_req_no" => $table->DEMAND_SYSTEM_NO,
                "txt_lot_no" => $table_product->LOT,
                "cbo_yarn_count" => $table_product->YARN_COUNT_ID,
                "cbo_color" => 0,
                "cbo_floor" => $table_product->FLOOR_ID,
                "cbo_room" => $table_product->ROOM,
                "txt_issue_qnty" => $issue_qnty,
                "txt_returnable_qty" => $return_qty,
                "txt_composition" => $table_product->YARN_COMP_TYPE1ST,
                "cbo_brand" => $table_product->BRAND,
                "txt_rack" => $table_product->RACK,
                "txt_no_bag" => $no_of_bag,
                "txt_no_cone" => $no_of_cone,
                "txt_weight_per_bag" => $weight_per_bag,
                "txt_weight_per_cone" => $weight_per_cone,
                "cbo_yarn_type" => $table_product->YARN_TYPE,
                "cbo_dyeing_color" => 0,
                "txt_shelf" => $table_product->SELF,
                "txt_current_stock" => $table_product->CURRENT_STOCK,
                "cbo_uom" => $table_product->UNIT_OF_MEASURE,
                "cbo_item" => $table_product->ITEM_CATEGORY_ID,
                "update_id_mst" => 0,
                "update_id" => "NULL",
                "save_data" => $poId_issueQnty_returnQnty,
                "all_po_id" => $poIds,
                "txt_prod_id" => $table_product->PROD_ID,
                "job_no" => "NULL",
                "cbo_ready_to_approved" => $ready_to_approved,
                "cbo_supplier_lot" => $table_product->PRO_SUPPLIER_ID,
                "txt_btb_lc_id" => "NULL",
                "extra_quantity" => 0,
                "txt_entry_form" => 0,
                "hidden_p_issue_qnty" => 0,
                "hdn_wo_qnty" => "NULL",
                "txt_service_booking_no" => "NULL",
                "demand_id" => $table->DEMAND_ID,
                "hdn_req_no" => $table->REQUISITION_NO,
                "original_save_data" => "NULL",
                "cbo_bin" => $table_product->BIN_BOX,
                "saved_knitting_company" => $table->KNITTING_PARTY,
                "txt_attention" => $attention,
                "txt_remarks_dtls" => $dtls_remarks,
                "txt_wo_id" => "NULL",
                "txt_pi_id" => "NULL",
                "hdn_fabric_booking_no" => "NULL",
            ];
        } else if ($basis_id == 1) {

            $requisition_query = "SELECT a.COMPANY_ID, 1 as RCV_BASIS,a.BOOKING_DATE,a.SUPPLIER_ID,b.PRODUCT_ID,e.PO_BUYER,b.YARN_COLOR,a.SUPPLIER_ID,a.YDW_NO,a.ENTRY_FORM,b.JOB_NO,c.BUYER_NAME,a.SOURCE from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b left join wo_po_details_master c on b.job_no_id=c.id left join wo_non_ord_samp_booking_mst d on b.booking_no=d.booking_no left join fabric_sales_order_mst e on e.job_no=b.job_no 
                where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
                and a.ydw_no = '$req_no' 
                and a.entry_form in(41,42,114,125,135)";
            //print_r($requisition_query);die;
            $table = $this->db->query($requisition_query)->result();


            $prod_id_arr = array();
            foreach ($table as $row) {
                $prod_id_arr[$row->PRODUCT_ID] = $row->PRODUCT_ID;
            }
            $prod_ids_str = implode(',', $prod_id_arr);
            //$req_no = $table[0]->DEMAND_ID;
            $query_product = "SELECT a.PROD_ID,c.COMPANY_ID,c.LOCATION_ID,c.RECEIVE_BASIS,a.ID,a.REQUISITION_NO,c.ORDER_UOM,b.LOT,b.PRODUCT_NAME_DETAILS,b.YARN_TYPE,b.COLOR,SUM(a.YARN_QNTY) as REQ_QNTY,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,b.SUPPLIER_ID as PRO_SUPPLIER_ID,c.STORE_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,b.YARN_COUNT_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID FROM PPL_YARN_REQUISITION_ENTRY a,PRODUCT_DETAILS_MASTER b,INV_TRANSACTION c WHERE a.STATUS_ACTIVE = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0 and c.STATUS_ACTIVE = 1 and c.IS_DELETED = 0 and a.PROD_ID = $dtls_id
                and a.PROD_ID = b.ID and c.REQUISITION_NO = a.REQUISITION_NO group by b.LOT,b.PRODUCT_NAME_DETAILS,b.COLOR,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,a.REQUISITION_NO,a.ID,c.COMPANY_ID,c.RECEIVE_BASIS,a.PROD_ID,c.ORDER_UOM,c.LOCATION_ID,c.STORE_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.YARN_TYPE,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID,b.SUPPLIER_ID,b.YARN_COUNT_ID";
            //print_r($query_product);die;
            $table_product = $this->db->query($query_product)->result();

            $poId_issueQnty_returnQnty = "";
            $poIds = "";

            return $response_arr = [
                "db_type" => 1,
                "operation" => 0,
                "user_id" => $user_id,
                "txt_system_no" => "NULL",
                "cbo_company_id" => $table_product->COMPANY_ID,
                "cbo_basis" => $table_product->RECEIVE_BASIS,
                "cbo_issue_purpose" => $issue_perpose,
                "txt_issue_date" => date('d-M-Y', time()),
                "txt_booking_no" => $table_product->YDW_NO,
                "txt_booking_id" => $table_product->YDW_ID,
                "cbo_location_id" => $location_id,
                "cbo_knitting_source" => $table->SOURCE,
                "cbo_knitting_company" => $table->SUPPLIER_ID,
                "cbo_supplier" => $table->SUPPLIER_ID,
                "cbo_store_name" => ($table->STORE_ID) ? $table->STORE_ID : 0,
                "txt_challan_no" => $challan_no,
                "cbo_loan_party" => 0,
                "cbo_buyer_name" => $table->BUYER_NAME,
                "txt_style_ref" => "NULL",
                "txt_buyer_job_no" => $table->JOB_NO,
                "cbo_sample_type" => "0",
                "txt_remarks" => $mst_remarks,
                "txt_req_no" => $table->REQUISITION_NO,
                "txt_lot_no" => $table_product->LOT,
                "cbo_yarn_count" => $table_product->YARN_COUNT_ID,
                "cbo_color" => 0,
                "cbo_floor" => $table_product->FLOOR_ID,
                "cbo_room" => $table_product->ROOM,
                "txt_issue_qnty" => $issue_qnty,
                "txt_returnable_qty" => $return_qty,
                "txt_composition" => $table_product->YARN_COMP_TYPE1ST,
                "cbo_brand" => $table_product->BRAND,
                "txt_rack" => $table_product->RACK,
                "txt_no_bag" => $no_of_bag,
                "txt_no_cone" => $no_of_cone,
                "txt_weight_per_bag" => $weight_per_bag,
                "txt_weight_per_cone" => $weight_per_cone,
                "cbo_yarn_type" => $table_product->YARN_TYPE,
                "cbo_dyeing_color" => 0,
                "txt_shelf" => $table_product->SELF,
                "txt_current_stock" => $table_product->CURRENT_STOCK,
                "cbo_uom" => $table_product->UNIT_OF_MEASURE,
                "cbo_item" => $table_product->ITEM_CATEGORY_ID,
                "update_id_mst" => 0,
                "update_id" => "NULL",
                "save_data" => $poId_issueQnty_returnQnty,
                "all_po_id" => $poIds,
                "txt_prod_id" => $table_product->PROD_ID,
                "job_no" => $table->JOB_NO,
                "cbo_ready_to_approved" => $ready_to_approved,
                "cbo_supplier_lot" => $table_product->PRO_SUPPLIER_ID,
                "txt_btb_lc_id" => "NULL",
                "extra_quantity" => 0,
                "txt_entry_form" => $table->ENTRY_FORM,
                "hidden_p_issue_qnty" => 0,
                "hdn_wo_qnty" => "NULL",
                "txt_service_booking_no" => "NULL",
                "demand_id" => 0,
                "hdn_req_no" => $table->YDW_NO,
                "original_save_data" => "NULL",
                "cbo_bin" => $table_product->BIN_BOX,
                "saved_knitting_company" => $table->SUPPLIER_ID,
                "txt_attention" => $attention,
                "txt_remarks_dtls" => $dtls_remarks,
                "txt_wo_id" => "NULL",
                "txt_pi_id" => "NULL",
                "hdn_fabric_booking_no" => "NULL",
            ];
        }
    }

    function return_product_id($yarncount, $composition_one, $composition_two, $percentage_one, $percentage_two, $yarntype, $color, $yarnlot, $prodCode, $company, $supplier, $store, $uom, $yarn_type, $composition, $cbo_receive_purpose, $hdnPayMode)
    {

        $composition_one = str_replace("'", "", $composition_one);
        $composition_two = str_replace("'", "", $composition_two);
        $percentage_one = str_replace("'", "", $percentage_one);
        $percentage_two = str_replace("'", "", $percentage_two);
        $yarntype = str_replace("'", "", $yarntype);
        $color = str_replace("'", "", $color);
        $yarncount = str_replace("'", "", $yarncount);
        if ($percentage_one == "") $percentage_one = 0;
        if ($percentage_two == "") $percentage_two = 0;
        $cbo_receive_purpose = str_replace("'", "", $cbo_receive_purpose);
        if ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38 || $cbo_receive_purpose == 43 || $cbo_receive_purpose == 46 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51) $dyed_type = 1;
        else $dyed_type = 2;
        if ($cbo_receive_purpose == 15) $is_twisted = 1;
        else $is_twisted = 0;

        //for pay mode
        $payMode = str_replace("'", "", $hdnPayMode);
        $is_within_group = 0;
        if ($payMode == 3 || $payMode == 5) {
            $is_within_group = 1;
        }

        //NOTE :- Yarn category array ID=1
        $conp2_cond = "";
        if ($composition_two != "") $conp2_cond = " and yarn_comp_type2nd=$composition_two and yarn_comp_percent2nd=$percentage_two";
        $whereCondition = "yarn_count_id=$yarncount and yarn_comp_type1st=$composition_one and yarn_comp_percent1st=$percentage_one $conp2_cond and yarn_type=$yarntype and color=$color and company_id=$company and supplier_id=$supplier and item_category_id=1 and lot='$yarnlot' and status_active=1 and is_deleted=0"; //and store_id=$store
        $prodMSTID = return_field_value("id", "product_details_master", "$whereCondition", "");
        //return "select id from product_details_master where $whereCondition";die;
        $insertResult = true;
        if ($prodMSTID == false || $prodMSTID == "") {
            // new product create here--------------------------//
            $yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
            $color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

            $compositionPart = $composition[$composition_one] . " " . $percentage_one;
            if ($percentage_two != 0) {
                $compositionPart .= " " . $composition[$composition_two] . " " . $percentage_two;
            }

            //$yarn_count.','.$composition.','.$ytype.','.$color;
            $product_name_details = $yarn_count_arr[$yarncount] . " " . $compositionPart . " " . $yarn_type[$yarntype] . " " . $color_name_arr[$color];
            $product_name_details = str_replace(array("\r", "\n"), '', $product_name_details);

            $prodMSTID = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
            $field_array = "id,company_id,supplier_id,item_category_id,product_name_details,lot,item_code,unit_of_measure,yarn_count_id,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color,dyed_type,inserted_by,insert_date,is_twisted,is_within_group";
            $data_array = "(" . $prodMSTID . "," . $company . "," . $supplier . ",1,'" . $product_name_details . "','" . $yarnlot . "'," . $prodCode . "," . $uom . "," . $yarncount . "," . $composition_one . "," . $percentage_one . ",'" . $composition_two . "','" . $percentage_two . "'," . $yarntype . "," . $color . ",'" . $dyed_type . "','" . $user_id . "','" . $pc_date_time . "'," . $is_twisted . "," . $is_within_group . ")";
            //echo $field_array."<br>".$data_array."--".$product_name_details;die;
            $insertResult = false;
            //$insertResult = sql_insert("product_details_master",$field_array,$data_array,1);
        }
        if ($insertResult == true) {
            return $insertResult . "***" . $prodMSTID;
        } else {
            return $insertResult . "***" . $field_array . "***" . $data_array . "***" . $prodMSTID;
        }
    }
}//end class;
