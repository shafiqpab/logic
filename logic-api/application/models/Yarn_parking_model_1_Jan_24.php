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
	{
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
		$user_credentials = "SELECT UNIT_ID, COMPANY_LOCATION_ID,IS_PLANNER FROM user_passwd Where ID='$user_id' ";
		$all_comp = 0;
		$all_loc = 0;
		foreach (sql_select($user_credentials) as $v) {
			$all_comp = $v->UNIT_ID;
			$all_loc = $v->COMPANY_LOCATION_ID;
			$is_planner = $v->IS_PLANNER;
		}

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


		$loc_sql = "SELECT b.ID,b.LOCATION_NAME,b.COMPANY_ID from lib_location  b, lib_company a  where a.id=b.company_id and b.status_active=1 and b.is_deleted=0 $wher_loc_con_b and  a.status_active =1 and a.is_deleted=0 and a.CORE_BUSINESS=1 order by b.location_name";


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
		/*
		$complexity_level_data[0]['fdout'] = 0;
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
            $query_yarn_grn = "SELECT a.ID,a.RECV_NUMBER,a.COMPANY_ID,b.IS_QC_PASS,a.IS_APPROVED,a.INSERT_DATE,a.RECEIVE_BASIS,a.RECEIVE_PURPOSE,a.LOAN_PARTY,a.BOOKING_NO,a.CHALLAN_NO,a.EXCHANGE_RATE,a.CURRENCY_ID,a.SUPPLIER_ID ,a.STORE_ID,a.SOURCE,b.ID as DTLS_ID,b.WO_PI_ID,b.LOT,b.WO_PI_DTLS_ID,b.WO_PI_NO,b.YARN_COUNT,b.YARN_COMP_TYPE1ST,b.YARN_COMP_PERCENT1ST,b.YARN_TYPE,b.COLOR_NAME,b.UOM,b.WO_PI_QUANTITY,b.COLOR_NAME,b.PARKING_QUANTITY,b.LOSE_CONE,b.RATE,b.AVG_RATE,b.AMOUNT,b.CONS_AMOUNT,b.NO_OF_BAG,b.CONE_PER_BAG,b.WEIGHT_PER_BAG,b.WEIGHT_CONE,b.INSERTED_BY,b.BRAND_NAME FROM INV_RECEIVE_MASTER a,QUARANTINE_PARKING_DTLS b 
		    WHERE a.ID = b.MST_ID and a.ENTRY_FORM = 529 and a.ITEM_CATEGORY = 1 and a.STATUS_ACTIVE=1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE =1 and b.IS_DELETED = 0 and a.RECV_NUMBER = '$grn_no'";
            //print_r($query_yarn_grn);die;
            $table_yarn_grn = $this->db->query($query_yarn_grn)->result();
            $rfids = array();
            $mst = array();
            $dtls = array();
            
            if (!empty($table_yarn_grn)) 
            {
                $query_check_inv_rcv_mst = "SELECT irm.ID FROM INV_RECEIVE_MASTER irm JOIN INV_RECEIVE_MASTER irm2 ON irm.EMP_ID = irm2.ID WHERE irm2.RECV_NUMBER = '$grn_no'";
                $table_inv_old_row = $this->db->query($query_check_inv_rcv_mst)->row();

                if(!empty($table_inv_old_row)){
                    $query_rfids = "SELECT * FROM RFID_YARN_DTLS WHERE MST_ID = $table_inv_old_row->ID and TRANS_TYPE = 1 AND ITEM_CATEGORY = 1 and ENTRY_FORM = 1 and STATUS_ACTIVE = 1 and IS_DELETED = 0";
                    $table_rfids = $this->db->query($query_rfids)->result();

                    if(!empty($table_rfids)){
                        foreach($table_rfids as $row){
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
        
                
                //print_r($table_yarn_grn);die;
                foreach ($table_yarn_grn as $row) {
                    if($row->IS_QC_PASS==0){
                        $msg="Required ID is not QC Passed";
                        continue;
                    }elseif($row->IS_APPROVED==0){
                        $msg="Required ID is not Approved";
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
                        "RFIDS" => $rfids[$row->DTLS_ID],
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

        public function grn_wise_yarn_data_for_issue($req_no,$basis_id)
        {
            // print_r(5);
            // die;
            //$basis_id = 3;
            $msg = "";
            $success_status = 0;
            $rec_basis = [3=>'REQUISITION',8 =>'DEMAND',1 =>'BOOKING'];
            if($basis_id==3){
                $query_product = "SELECT a.PROD_ID,c.COMPANY_ID,c.LOCATION_ID,c.RECEIVE_BASIS,a.ID,a.REQUISITION_NO,c.ORDER_UOM,b.LOT,b.PRODUCT_NAME_DETAILS,b.YARN_TYPE,b.COLOR,SUM(a.YARN_QNTY) as REQ_QNTY,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX FROM ppl_yarn_requisition_entry a,product_details_master b,INV_TRANSACTION c WHERE a.STATUS_ACTIVE = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0 and c.STATUS_ACTIVE = 1 and c.IS_DELETED = 0 and a.REQUISITION_NO = $req_no and a.PROD_ID = b.ID and c.REQUISITION_NO = a.REQUISITION_NO group by b.LOT,b.PRODUCT_NAME_DETAILS,b.YARN_TYPE,b.COLOR,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,a.REQUISITION_NO,a.ID,c.COMPANY_ID,c.RECEIVE_BASIS,a.PROD_ID,c.ORDER_UOM,c.LOCATION_ID";
                $table_product = $this->db->query($query_product)->result();
                //print_r($query_product);die;
                $requisition_query = "SELECT a.COMPANY_ID,3 as RCV_BASIS,a.booking_no,b.KNITTING_PARTY as KNITTING_COMPANY,a.BUYER_ID,c.REQUISITION_NO,c.prod_id,c.REQUISITION_DATE,sum(c.yarn_qnty) as yarn_qnty,c.ID as REQUISI_ID,b.KNITTING_SOURCE,d.SUPPLIER_ID,e.JOB_NO
                from PPL_PLANNING_INFO_ENTRY_MST a, PPL_PLANNING_INFO_ENTRY_DTLS b, PPL_YARN_REQUISITION_ENTRY c,PRODUCT_DETAILS_MASTER d,INV_TRANSACTION e
                where a.id=b.mst_id and b.id=c.knit_id and d.ID = c.PROD_ID and e.MST_ID = c.ID
                and c.requisition_no='$req_no' 
                and c.status_active=1 and c.is_deleted=0
                group by a.company_id,a.booking_no,c.requisition_no,c.prod_id,c.requisition_date,a.BUYER_ID,b.KNITTING_PARTY,c.ID,b.KNITTING_SOURCE,d.SUPPLIER_ID,e.JOB_NO";
                //print_r($requisition_query);die;
                $table = $this->db->query($requisition_query)->result();
                //print_r($query_product);die;
                
            }else if($basis_id==8){  
                // print_r(5);
                // die;
                $demand_query = "SELECT 8 as RCV_BASIS, e.COMPANY_ID,e.ID as DEMAND_ID,e.DEMAND_DATE,e.KNITTING_COMPANY,d.PROD_ID,a.BUYER_ID, c.REQUISITION_NO, sum(c.YARN_QNTY) as YARN_QNTY,  e.DEMAND_SYSTEM_NO, sum(d.yarn_demand_qnty) demand_qnty from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c, ppl_yarn_demand_reqsn_dtls d, ppl_yarn_demand_entry_mst e where a.id=b.mst_id and b.id=c.knit_id and c.id=d.requisition_id and d.mst_id=e.id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and e.status_active=1 and e.demand_system_no = '$req_no' group by a.buyer_id, c.requisition_no, c.prod_id, d.id, e.demand_system_no,e.ID,d.PROD_ID,e.DEMAND_DATE,e.COMPANY_ID,e.KNITTING_COMPANY";
                $table = $this->db->query($demand_query)->result();

                $requisition_query = "SELECT a.COMPANY_ID,3 as RCV_BASIS,a.BOOKING_NO,b.KNITTING_PARTY as KNITTING_COMPANY,a.BUYER_ID,c.REQUISITION_NO,c.prod_id,c.REQUISITION_DATE,sum(c.yarn_qnty) as yarn_qnty,c.ID as REQUISI_ID,b.KNITTING_SOURCE,d.SUPPLIER_ID,e.JOB_NO
                from PPL_PLANNING_INFO_ENTRY_MST a, PPL_PLANNING_INFO_ENTRY_DTLS b, PPL_YARN_REQUISITION_ENTRY c,PRODUCT_DETAILS_MASTER d,INV_TRANSACTION e
                where a.id=b.mst_id and b.id=c.knit_id and d.ID = c.PROD_ID and e.MST_ID = c.ID
                and c.requisition_no='$req_no' 
                and c.status_active=1 and c.is_deleted=0
                group by a.company_id,a.booking_no,c.requisition_no,c.prod_id,c.requisition_date,a.BUYER_ID,b.KNITTING_PARTY,c.ID,b.KNITTING_SOURCE,d.SUPPLIER_ID,e.JOB_NO";
                //print_r($table);die;
                $prod_id_arr = array();
                foreach($table as $row){
                    $prod_id_arr[$row->PROD_ID]=$row->PROD_ID;
                }
                $prod_ids_str = implode(',',$prod_id_arr);
                //$req_no = $table[0]->DEMAND_ID;
                $query_product = "SELECT a.id as PROD_ID, a.product_name_details, a.color, a.lot,a.yarn_count_id,a.yarn_comp_type1st,a.yarn_comp_percent1st,a.YARN_TYPE,b.REQUISITION_ID,b.REQUISITION_NO,d.DEMAND_SYSTEM_NO,b.YARN_DEMAND_QNTY,c.BALANCE_QNTY,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,c.SUPPLIER_ID from product_details_master a,ppl_yarn_demand_reqsn_dtls b,INV_TRANSACTION c,ppl_yarn_demand_entry_mst d where c.PROD_ID = a.ID and b.PROD_ID = a.ID and d.ID = b.MST_ID and a.item_category_id=1 and c.TRANSACTION_TYPE = 1 and a.STATUS_ACTIVE = 1 and a.IS_DELETED = 0  and c.STATUS_ACTIVE = 1 and c.IS_DELETED = 0 and a.id in($prod_ids_str)";
                $table_product = $this->db->query($query_product)->result();

                

            }else if($basis_id==1){

                $requisition_query = "SELECT a.YDW_NO,a.COMPANY_ID, 1 as RCV_BASIS,a.BOOKING_DATE,a.SUPPLIER_ID,b.PRODUCT_ID,e.PO_BUYER,b.YARN_COLOR from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b left join wo_po_details_master c on b.job_no_id=c.id left join wo_non_ord_samp_booking_mst d on b.booking_no=d.booking_no left join fabric_sales_order_mst e on e.job_no=b.job_no 
                where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
                and a.ydw_no = '$req_no' 
                 and a.entry_form in(41,42,114,125,135)";
                //print_r($requisition_query);die;
                $table = $this->db->query($requisition_query)->result();


                $prod_id_arr = array();
                foreach($table as $row){
                    $prod_id_arr[$row->PRODUCT_ID]=$row->PRODUCT_ID;
                }
                $prod_ids_str = implode(',',$prod_id_arr);
                //$req_no = $table[0]->DEMAND_ID;
                $query_product = "SELECT a.ID as PROD_ID, a.product_name_details, a.COLOR, a.LOT,a.YARN_COUNT_ID,a.YARN_COMP_TYPE1ST,a.YARN_COMP_PERCENT1ST,a.YARN_TYPE,d.ID as YDW_ID,d.YDW_NO,b.YARN_WO_QTY,c.BALANCE_QNTY,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,c.SUPPLIER_ID,c.ORDER_UOM from PRODUCT_DETAILS_MASTER a,WO_YARN_DYEING_DTLS b,INV_TRANSACTION c,WO_YARN_DYEING_MST d where c.PROD_ID = a.ID and b.PRODUCT_ID = a.ID and d.ID = b.MST_ID and a.item_category_id=1 and c.TRANSACTION_TYPE = 1 and a.STATUS_ACTIVE = 1 and a.IS_DELETED = 0 and c.STATUS_ACTIVE = 1 and c.IS_DELETED = 0 and a.id in($prod_ids_str)";
                //print_r($query_product);die;
                $table_product = $this->db->query($query_product)->result();
            }
            
            
            //print_r($table_product);die;
            //$data = [];
            if($table){
                $lib_color = return_library_arr("SELECT ID,COLOR_NAME FROM lib_color WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0","ID","COLOR_NAME");
                $lib_yarn_type = return_library_arr("SELECT YARN_TYPE_ID,YARN_TYPE_SHORT_NAME FROM LIB_YARN_TYPE WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0","YARN_TYPE_ID","YARN_TYPE_SHORT_NAME");
                $lib_supplier = return_library_arr("SELECT ID,SUPPLIER_NAME FROM LIB_SUPPLIER WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0","ID","SUPPLIER_NAME");

                $lib_com = return_library_arr("SELECT ID,COMPANY_NAME FROM LIB_COMPANY WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0","ID","COMPANY_NAME");
                $lib_buyer = return_library_arr("SELECT ID,BUYER_NAME FROM LIB_BUYER WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0","ID","BUYER_NAME");
                $lib_sample = return_library_arr("SELECT ID,SAMPLE_NAME FROM LIB_SAMPLE WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0","ID","SAMPLE_NAME");


                $lib_floor_room_rack = return_library_arr("SELECT FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME FROM LIB_FLOOR_ROOM_RACK_MST WHERE STATUS_ACTIVE = 1 and IS_DELETED = 0","FLOOR_ROOM_RACK_ID","FLOOR_ROOM_RACK_NAME");

               

                //print_r($table);die;
                if($basis_id==3){
                    $products_arr = [];
                    foreach($table_product as $row){
                        $products_arr[] = [
                            "PROD_ID" => $row->PROD_ID,
                            "SYS_ID" => $row->ID,
                            "SYS_NO" => $row->REQUISITION_NO,
                            "LOT_NO" => $row->LOT,
                            "PRODUCT_DETAILS" => $row->PRODUCT_NAME_DETAILS,
                            "YARN_TYPE_ID" => $row->YARN_TYPE,
                            "YARN_TYPE" => $lib_yarn_type[$row->YARN_TYPE],
                            "COLOR_ID" => $row->COLOR,
                            "COLOR_NAME" => $lib_color[$row->COLOR],
                            "REQ_QNTY" => $row->REQ_QNTY,
                            "LOCATION_ID" => $row->LOCATION_ID,
                            "CURRENT_STOCK" => $row->CURRENT_STOCK,
                            "FLOOR_ID" => $lib_floor_room_rack[$row->FLOOR_ID],
                            "FLOOR_NAME" => $row->FLOOR_ID,
                            "ROOM_ID" => $row->ROOM,
                            "ROOM" => $lib_floor_room_rack[$row->ROOM],
                            "RACK_ID" => $row->RACK,
                            "RACK" => $lib_floor_room_rack[$row->RACK],
                            "SHELF_ID" => $row->SELF,
                            "SHELF" => $lib_floor_room_rack[$row->SELF],
                            "BIN_ID" => $row->BIN_BOX,
                            "BIN" => $lib_floor_room_rack[$row->BIN_BOX],
                            "SUPPLIER_ID" =>  $row->SUPPLIER_ID,                      
                            "SUPPLIER_NAME" =>  $lib_supplier[$row->SUPPLIER_ID],                      
                            "UOM_ID" =>  $row->ORDER_UOM,                      
                            "UOM_NAME" =>  $row->ORDER_UOM,
                        ];
                    }

                    $mst = array();
                    foreach($table as $row){
                        $mst = [
                            "COMPANY_ID" => $row->COMPANY_ID,
                            "COMPANY_NAME" => $lib_com[$row->COMPANY_ID],
                            "RECEIVE_BASIS_ID" => $row->RCV_BASIS,
                            "ISSUE_PERPOSE" => 0,
                            "ISSUE_DATE" => date('d-M-Y',time()),
                            "KNITTING_SOURCE" =>$row->KNITTING_SOURCE,
                            "ISSUE_TO" => 0,
                            "LOCATION_ID" => 0,
                            "SUPPLIER_ID" => $row->SUPPLIER_ID,
                            "SUPPLIER_NAME" => $lib_supplier[$row->SUPPLIER_ID],
                            "CHALLAN_PROGRAM_NO" => "",
                            "LOAN_PARTY_ID" => 0,
                            "SAMPLE_TYPE" => 0,
                            "STYLE_REF" => "",
                            "BUYER_JOB_NO" => $row->JOB_NO,
                            "SERVICE_BOOKING" => "",
                            "READY_TO_APPROVED" => 0,
                            "ATTENTION" => "",
                            "SYSTEM_NUMBER" => $row->REQUISITION_NO,
                            "BOOKING_ID" => $row->REQUISITION_NO,
                            "BOOKING_NUMBER" => $row->REQUISITION_NO,
                            "RECEIVE_BASIS" => $rec_basis[$row->RCV_BASIS],
                            "ISSUE_DATE" => $row->REQUISITION_DATE,
                            "KNITTING_COMPANY_ID" => $row->KNITTING_COMPANY,
                            "KNITTING_COMPANY" => $lib_com[$row->KNITTING_COMPANY],
                            "BUYER_ID" =>$row->BUYER_ID,
                            "BUYER_NAME" =>$lib_buyer[$row->BUYER_ID],
                            "PRODUCTS" => $products_arr,                        
                            "LIB_SAMPLE" => $lib_sample,                        
                        ];                                     
                    }
                }else if($basis_id==8)
                {
                    $products_arr = [];
                    foreach($table_product as $row){
                        $products_arr[] = [
                            "PROD_ID" => $row->PROD_ID,
                            "SYS_ID" => $row->REQUISITION_ID,
                            "SYS_NO" => $row->REQUISITION_NO,
                            "LOT_NO" => $row->LOT,
                            "PRODUCT_DETAILS" => $row->PRODUCT_NAME_DETAILS,
                            "YARN_TYPE" => $lib_yarn_type[$row->YARN_TYPE],
                            "COLOR" => $lib_color[$row->COLOR],
                            "REQ_QNTY" => $row->YARN_DEMAND_QNTY,
                            "CURRENT_STOCK" => $row->BALANCE_QNTY,
                            "FLOOR" => $lib_floor_room_rack[$row->FLOOR_ID],
                            "ROOM" => $lib_floor_room_rack[$row->ROOM],
                            "RACK" => $lib_floor_room_rack[$row->RACK],
                            "SHELF" => $lib_floor_room_rack[$row->SELF],
                            "BIN" => $lib_floor_room_rack[$row->BIN_BOX],
                            "SUPPLIER" =>  $lib_supplier[$row->SUPPLIER_ID],                      
                            "UOM" =>  $row->ORDER_UOM,                      
                        ];      
                    }
                    //$date = date('d-M-Y',time());
                    //print_r($date);die;
                    $mst = array();
                    foreach($table as $row){
                        $mst = [
                            "COMPANY_ID" => $lib_com[$row->COMPANY_ID],
                            "RECEIVE_BASIS" => $rec_basis[$row->RCV_BASIS],
                            "COMPANY_NAME" => $lib_com[$row->COMPANY_ID],
                            "ISSUE_PERPOSE" => 0,
                            "ISSUE_DATE" => "",
                            "KNITTING_SOURCE" =>0,
                            "ISSUE_TO" => 0,
                            "LOCATION_ID" => 0,
                            "SUPPLIER_ID" => $row->SUPPLIER_ID,
                            "SUPPLIER_NAME" => $lib_supplier[$row->SUPPLIER_ID],
                            "CHALLAN_PROGRAM_NO" => "",
                            "LOAN_PARTY_ID" => 0,
                            "SAMPLE_TYPE" => 0,
                            "STYLE_REF" => "",
                            "BUYER_JOB_NO" => $row->JOB_NO,
                            "SERVICE_BOOKING" => "",
                            "READY_TO_APPROVED" => 0,
                            "ATTENTION" => "",
                            "SYSTEM_NUMBER" => $row->DEMAND_SYSTEM_NO,
                            "BOOKING_ID" => $row->REQUISITION_NO,
                            "BOOKING_NUMBER" => $row->REQUISITION_NO,
                            "ISSUE_DATE" => $row->REQUISITION_DATE,
                            "KNITTING_COMPANY_ID" => $row->KNITTING_COMPANY,
                            "KNITTING_COMPANY" => $lib_com[$row->KNITTING_COMPANY],
                            "BUYER_ID" =>$row->BUYER_ID,
                            "BUYER_NAME" =>$lib_buyer[$row->BUYER_ID],
                            "PRODUCTS" => $products_arr,                                            
                        ];                                     
                    }
                }else if($basis_id==1){
                    $products_arr = [];
                        foreach($table_product as $row){
                            $products_arr[] = [
                                "PROD_ID" => $row->PROD_ID,
                                "SYS_ID" => $row->YDW_ID,
                                "SYS_NO" => $row->YDW_NO,
                                "LOT_NO" => $row->LOT,
                                "PRODUCT_DETAILS" => $row->PRODUCT_NAME_DETAILS,
                                "YARN_TYPE" => $lib_yarn_type[$row->YARN_TYPE],
                                "COLOR" => $lib_color[$row->COLOR],
                                "REQ_QNTY" => $row->YARN_WO_QTY,
                                "CURRENT_STOCK" => $row->BALANCE_QNTY,
                                "FLOOR" => $lib_floor_room_rack[$row->FLOOR_ID],
                                "ROOM" => $lib_floor_room_rack[$row->ROOM],
                                "RACK" => $lib_floor_room_rack[$row->RACK],
                                "SHELF" => $lib_floor_room_rack[$row->SELF],
                                "BIN" => $lib_floor_room_rack[$row->BIN_BOX],
                                "SUPPLIER" =>  $lib_supplier[$row->SUPPLIER_ID],                      
                                "UOM" =>  $row->ORDER_UOM,
                            ];      
                        }

                        $mst = array();
                        foreach($table as $row){
                            $mst = [
                                "COMPANY_ID" => $lib_com[$row->COMPANY_ID],
                                "COMPANY_NAME" => $lib_com[$row->COMPANY_ID],
                                "RECEIVE_BASIS" => $rec_basis[$row->RCV_BASIS],                                
                                "ISSUE_PERPOSE" => 0,
                                "ISSUE_DATE" => "",
                                "KNITTING_SOURCE" =>0,
                                "ISSUE_TO" => 0,
                                "LOCATION_ID" => 0,
                                "SUPPLIER_ID" => $row->SUPPLIER_ID,
                                "SUPPLIER_NAME" => $lib_supplier[$row->SUPPLIER_ID],
                                "CHALLAN_PROGRAM_NO" => "",
                                "LOAN_PARTY_ID" => 0,
                                "SAMPLE_TYPE" => 0,
                                "STYLE_REF" => "",
                                "BUYER_JOB_NO" => $row->JOB_NO,
                                "SERVICE_BOOKING" => "",
                                "READY_TO_APPROVED" => 0,
                                "ATTENTION" => "",
                                "SYSTEM_NUMBER" => $row->YDW_NO,
                                "BOOKING_ID" => $row->REQUISITION_NO,
                                "BOOKING_NUMBER" => $row->REQUISITION_NO,
                                "ISSUE_DATE" => $row->REQUISITION_DATE,
                                "KNITTING_COMPANY_ID" => $row->KNITTING_COMPANY,
                                "KNITTING_COMPANY" => $lib_com[$row->KNITTING_COMPANY],
                                "BUYER_ID" =>$row->BUYER_ID,
                                "BUYER_NAME" =>$lib_buyer[$row->BUYER_ID],
                                "PRODUCTS" => $products_arr,                        
                                "LIB_SAMPLE" => $lib_sample,                      
                            ];                                      
                        }
                }
                
                //print_r($table);die;
                
                return $mst;
            }
        }

        public function grn_wise_yarn_data_save($response_arr)
        {
            $status = 0;
            $msg = "";
            $this->db->trans_begin();
            $response_obj = json_decode($response_arr);

            $query_quarantine_park_dtls  = "SELECT * FROM QUARANTINE_PARKING_DTLS WHERE ID = $response_obj->DTLS_ID and STATUS_ACTIVE = 1 and IS_DELETED = 0";
            $table_quarantine_park_dtls = $this->db->query($query_quarantine_park_dtls)->row();

            if (empty($table_quarantine_park_dtls)) {
                $msg .= "  ***No Quanrantine dtls data Found";
                return [];
            }

            $query_inv_rec_mst = "SELECT * FROM INV_RECEIVE_MASTER WHERE ID = $response_obj->MASTER_ID and STATUS_ACTIVE = 1 and IS_DELETED = 0";
            $table_inv_rec_mst = $this->db->query($query_inv_rec_mst)->row();
            if (empty($table_inv_rec_mst)) {
                $msg .= "  ***No Inv Rec mst data Found";
                return [];
            }

            $new_trims_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", "", 1, $table_inv_rec_mst->COMPANY_ID, 'YRV', 1, date("Y", time()), 1));
            //print_r($new_trims_recv_system_id);die;

           
            $query_check_inv_rcv_mst = "SELECT ID,RECV_NUMBER FROM INV_RECEIVE_MASTER WHERE EMP_ID = $response_obj->MASTER_ID";
            $table_inv_old_row = $this->db->query($query_check_inv_rcv_mst)->row();

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



            $inv_trans_id = return_next_id('ID', 'INV_TRANSACTION');

            //print_r($inv_trans_id);die;


            $parking_save_arr = [
                "ID" => $inv_trans_id,
                "MST_ID" => $inv_rec_mst_id,
                "IS_EXCEL" => 1,
                "RECEIVE_BASIS" => 19,
                "PI_WO_BATCH_NO" => $table_quarantine_park_dtls->WO_PI_ID,
                "PI_WO_REQ_DTLS_ID" => $table_quarantine_park_dtls->WO_PI_DTLS_ID,
                "COMPANY_ID" => $table_inv_rec_mst->COMPANY_ID,
                "SUPPLIER_ID" => $table_inv_rec_mst->SUPPLIER_ID,
                "INSERTED_BY" =>  $response_obj->USER_ID,
                "TRANSACTION_TYPE" => 1,
                "TRANSACTION_DATE" => date("d-M-Y", time()),
                "INSERT_DATE" => date("d-M-Y", time()),
                "ENTRY_FORM" => 1,
                "IS_DELETED" => 0,
                "STATUS_ACTIVE" => 1,
                "PRODUCT_CODE" => $table_quarantine_park_dtls->PRODUCT_CODE,
                "ORDER_UOM" => $table_quarantine_park_dtls->UOM,
                "ORDER_QNTY" => $table_quarantine_park_dtls->PARKING_QUANTITY,
                "ORDER_RATE" => $table_quarantine_park_dtls->RATE,
                "DYE_CHARGE" => $table_quarantine_park_dtls->DYEING_CHARGE,
                "CONS_AVG_RATE" => $table_quarantine_park_dtls->AVG_RATE,
                "ORDER_AMOUNT" => $table_quarantine_park_dtls->AMOUNT,
                "CONS_AMOUNT" => $table_quarantine_park_dtls->CONS_AMOUNT,
                "NO_OF_BAGS" => $table_quarantine_park_dtls->NO_OF_BAG,
                "CONE_PER_BAG" => $table_quarantine_park_dtls->CONE_PER_BAG,
                "NO_LOOSE_CONE" => $table_quarantine_park_dtls->LOSE_CONE,
                "WEIGHT_PER_BAG" => $table_quarantine_park_dtls->WEIGHT_PER_BAG,
                "WEIGHT_PER_CONE" => $table_quarantine_park_dtls->WEIGHT_CONE,
                "REMARKS" => $table_quarantine_park_dtls->REMARKS,
                "ITEM_CATEGORY" => $table_quarantine_park_dtls->ITEM_CATEGORY_ID,
                "ORDER_ILE" => $table_quarantine_park_dtls->ILE_PERCENT,
                "STORE_ID" => $table_inv_rec_mst->STORE_ID,
                "FLOOR_ID" => $table_quarantine_park_dtls->FLOOR_ID,
                "ROOM" => $table_quarantine_park_dtls->ROOM,
                "RACK" => $table_quarantine_park_dtls->RACK,
                "BIN_BOX" => $table_quarantine_park_dtls->BIN_BOX,
                "GREY_QUANTITY" => $table_quarantine_park_dtls->GREY_QUANTITY,
                "PROD_ID" => $prodMSTID,
                "ORIGIN_PROD_ID" => $prodMSTID,
                "CONS_UOM" => $table_quarantine_park_dtls->UOM,
                "CONS_QUANTITY" => $table_quarantine_park_dtls->CONS_AMOUNT,
                "CONS_RATE" => $table_quarantine_park_dtls->AVG_RATE,
                "BALANCE_QNTY" => $table_quarantine_park_dtls->PARKING_QUANTITY,
                "BALANCE_AMOUNT" => $table_quarantine_park_dtls->CONS_AMOUNT,
                "ORDER_ILE_COST" => 0,
                "SELF" => 0,
                "PARKING_DTLS_ID" => $table_quarantine_park_dtls->ID,
                "CONS_ILE" => $table_quarantine_park_dtls->AVG_RATE * $table_quarantine_park_dtls->AVG_RATE,
                "CONS_ILE_COST" => $table_quarantine_park_dtls->AVG_RATE * $table_quarantine_park_dtls->AVG_RATE,
            ];


            $this->insertData($parking_save_arr, "INV_TRANSACTION");
            $msg .= "  ***New trans id = $inv_trans_id";
            //print_r($parking_save_arr);die;
            $yarn_rfid_dtls_id = return_next_id('ID', 'RFID_YARN_DTLS');
            $rfid_save_arr = array();
            foreach ($response_obj->RFID as  $row) {

                $rfid_save_arr[] = [
                    'ID' => $yarn_rfid_dtls_id,
                    'MST_ID' => $inv_rec_mst_id,
                    'TRANS_ID' => $inv_trans_id,
                    'PARKING_DTLS_ID' => $table_quarantine_park_dtls->ID,
                    'TRANS_TYPE' => 1,
                    'ITEM_CATEGORY' => 1,
                    'RFID_NO' => $row->EPCID,
                    'ENTRY_FORM' => 1,
                    'INSERT_DATE' => date("d-M-Y", time()),
                    'STATUS_ACTIVE' => 1,
                    'IS_DELETED' => 0,
                ];
                $yarn_rfid_dtls_id++;
            }
            //print_r($rfid_save_arr);die;
            $status = $this->db->insert_batch("RFID_YARN_DTLS", $rfid_save_arr);
            $msg .= "  ***New RFID id Starts FROM  = $yarn_rfid_dtls_id";

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
            $data = $this->createObj_grn_wise_yarn_data_for_issue_save($response_arr);
            
            $success_status = 0;
            $pub_msg = "";
            $pc_date_time = date('d-M-Y h:i:s a', time());
            $return_data = [
                "SUCCESS_STATUS" => $success_status,
                "PUB_MSG" => $pub_msg,
            ];
            //$response_arr=json_decode($response_arr, true);
            extract($data);

            //print_r($cbo_company_id);die;           
            $txt_issue_qnty=str_replace("'", "", $txt_issue_qnty);
            $update_id=str_replace("'", "", $update_id);
            
            $variable_store_wise_rate=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$cbo_company_id and variable_list=47 and item_category_id=1 and status_active=1 and is_deleted=0","auto_transfer_rcv");
            
            if($variable_store_wise_rate != 1) $variable_store_wise_rate=2;
            
            $store_wise_cond = ($variable_store_wise_rate==1) ? " and store_id=$cbo_store_name" : " ";

            $max_trans_query = sql_select_arr("SELECT max(case when transaction_type in (1,4,5) then transaction_date else null end) as max_date, max(id) as max_id from inv_transaction where prod_id=$txt_prod_id $store_wise_cond and item_category=1 and status_active=1 and transaction_type in (1,4,5)");
            //and transaction_type in (1,4,5)
            //print_r($max_trans_query);die;
            
            //print_r($max_trans_query[0]["MAX_DATE"]);die;
            if(!empty($max_trans_query))
            {
                $max_recv_date = $max_trans_query[0]['max_date'];
        
                if($max_recv_date!="")
                {
                    $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
                    $issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
        
                    if ($issue_date < $max_recv_date)
                    {
                        $pub_msg.= "20**Issue Date Can not Be Less Than Last Receive Date Of This Lot.\nIssue Date $issue_date \nReceived date $max_recv_date ";
                        return $pub_msg;
                     
                    }
                }
        
                if($operation == 1 || $operation == 2)
                {
                    $currentTransId = (int)str_replace("'","",$update_id);
                    $max_trans_id = (int)$max_trans_query[0]['max_id'];
        
                    if ( $max_trans_id > $currentTransId )
                    {
                        $pub_msg.= "20**Transaction found of this store and product ";
                        return $pub_msg;
                    }
                }
            }
        
            //=====Ref Closing-========
            $issue_basis_id=str_replace("'", "", $cbo_basis);
            if($issue_basis_id==3)
            {   
                $req_no_id=str_replace("'", "", $txt_req_no);
                $req_sql = "SELECT c.knit_id as prog_no
                from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c
                where a.id=b.mst_id and b.id=c.knit_id and c.requisition_no='$req_no_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.knit_id";
                
                $req_result = sql_select_arr($req_sql,1);
                $prog_no=$req_result[0]['PROG_NO'];
                //print_r($prog_no);die;
                //$sql_ref="select inv_pur_req_mst_id from inv_reference_closing where inv_pur_req_mst_id=$txt_booking_no_id and  reference_type==2";
                $sql_ref="select ref_closing_status,id as prog_no from ppl_planning_info_entry_dtls where id=$prog_no  and status_active=1";
                $prod_result=sql_select_arr($sql_ref,1);
                $ref_closing_status=$prod_result[0]['ref_closing_status'];
                if($ref_closing_status==1)
                {
                    $pub_msg.= "23**This Req. against prog no is closed";
                    return $pub_msg;
                }
            }
        
        
            
        
            // check variable settings if allocation is available or not
            $variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=18 and item_category_id = 1","");
        
            $variable_set_smn_allocation = return_field_value("smn_allocation", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=18 and item_category_id = 1","smn_allocation");
        
            $is_auto_allocation_from_requisition = return_field_value("auto_allocate_yarn_from_requis", "variable_settings_production", "company_name=$cbo_company_id and variable_list=6 and status_active=1 and is_deleted=0", "auto_allocate_yarn_from_requis");
        
            //---- booking basis control start
            $variable_set_booking_basis_control = sql_select_arr("select yes_no,tolerant_percent as over_percentage from variable_settings_inventory where company_name = $cbo_company_id and variable_list = 46 and item_category_id = 1 and is_deleted = 0 and status_active = 1");
        
            $over_issue_status = preg_replace('/\s+/', '', $variable_set_booking_basis_control[0]['yes_no']); // For all whitespace (including tabs and line ends)
            $over_percentage =  ($variable_set_booking_basis_control[0]['over_percentage']=="")?0:$variable_set_booking_basis_control[0]['over_percentage'];
        
            if( ($variable_set_allocation != 1) && ($over_issue_status==1) && (str_replace("'", "", $cbo_issue_purpose)==1) )
            {
                $req_booking_sql = sql_select_arr("SELECT sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.booking_no=$txt_booking_no");
                $req_booking_qnty = number_format($req_booking_sql[0]['qnty'],2,'.','');
                $over_percentage_qty = number_format((($req_booking_qnty/100)*$over_percentage),2,'.','');
                $allowed_qnty = number_format(($req_booking_qnty+$over_percentage_qty),2,'.','');
        
                if($operation == 1) {$update_id_cond =" and b.id <> $update_id";}
        
                $previous_issue_sql="SELECT LISTAGG(a.issue_number_prefix_num, ',') WITHIN GROUP (ORDER BY a.id) as issue_mrr,sum(b.cons_quantity) as issue_qnty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_basis=1 and a.issue_purpose=1 and a.item_category =1 and a.booking_no='$txt_booking_no' and a.booking_id=$txt_booking_id and a.entry_form=3 and b.transaction_type=2 and a.status_active =1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $update_id_cond";
                $previous_issue_sql_result = sql_select_arr($previous_issue_sql);
                $previous_total_issue_qnty = $previous_issue_sql_result[0]['issue_qnty'];
                $issue_mrr = $previous_issue_sql_result[0]['issue_mrr'];
        
                $total_issue_rtn_qty = return_field_value("sum(b.cons_quantity) total_issue_rtn", "inv_receive_master a, inv_transaction b", "a.id=b.mst_id and b.item_category=1 and b.transaction_type=4 and a.booking_no=$txt_booking_no and a.booking_id=$txt_booking_id and a.receive_basis=1  and a.status_active =1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0", "total_issue_rtn");
        
                $total_issue_rtn_sql = "SELECT LISTAGG(a.recv_number_prefix_num, ',') WITHIN GROUP (ORDER BY a.id) as return_mrr, sum(b.cons_quantity) total_issue_rtn from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.item_category=1 and b.transaction_type=4 and a.booking_no=$txt_booking_no and a.booking_id=$txt_booking_id and a.receive_basis=1  and a.status_active =1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
        
                $total_issue_rtn_sql_result = sql_select_arr($total_issue_rtn_sql);
                $total_issue_rtn_qty = $total_issue_rtn_sql_result[0]['total_issue_rtn'];
                $return_mrr = $total_issue_rtn_sql_result[0]['return_mrr'];
        
                $actual_previous_issue_qty = ($previous_total_issue_qnty-$total_issue_rtn_qty);
                $total_issue_qnty = number_format(($actual_previous_issue_qty + $txt_issue_qnty),2,'.','');
        
                //echo "10**".$total_issue_qnty."==".$allowed_qnty."per".$over_percentage_qty; die();
                $msg = "";
                if($allowed_qnty<$total_issue_qnty)
                {
                    if($over_percentage>0)
                    {
                        $msg = "\nOver percentage =$over_percentage%";
                    }
        
                    if($total_issue_qnty>0)
                    {
                        $msg .= "\nIssue No=$issue_mrr\nIssue quantity=$previous_total_issue_qnty";
                    }
        
                    if($total_issue_rtn_qty>0)
                    {
                        $msg .= "\nIssue return No=$return_mrr\nIssue return quantity=$total_issue_rtn_qty";
                    }
        
                    $pub_msg.= "   20**Issue quantity can not be greater than booking required quantity\nBooking required quantity=$req_booking_qnty".$msg;
                    return $pub_msg;
                }
            }
            //---- booking basis control end
            //echo "10**".$over_issue_status."hh"; die();
            
            
            $wo_purpose = array(2,7,15,12,38,44,46,50,51);
            // IF BASIS WORK ORDER ISSUE CAN NOT BE GREATER THAN WO QUANTITY
            if( str_replace("'", "", $cbo_basis) == 1 && in_array(str_replace("'", "", $cbo_issue_purpose), $wo_purpose))
            {
                $wo_entry_form = str_replace("'", "", $txt_entry_form);
                $wo_no = str_replace("'", "", $txt_booking_no);
        
                if(str_replace("'", "", $cbo_issue_purpose)==2) // dyeing color
                {
                    $dyeing_color_id = str_replace("'", "", $cbo_dyeing_color);
                    $dyeing_color_cond = "and b.dyeing_color_id=$dyeing_color_id";
                }
        
                $yarn_dyeing_info=sql_select_arr("SELECT a.booking_without_order,a.is_sales from wo_yarn_dyeing_mst a where a.ydw_no='$wo_no' and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0");
                $is_sales = $yarn_dyeing_info[0]["IS_SALES"];
                $is_with_order_yarn_service_work_order = $yarn_dyeing_info[0]["BOOKING_WITHOUT_ORDER"];
        
                // 41,114,125,94,340
                if( $wo_entry_form ==41 || $wo_entry_form== 125 || ( $wo_entry_form == 94 && $is_with_order_yarn_service_work_order==1) )
                {
                    $previous_issue_qnty = return_field_value("sum(c.quantity) as issue_qnty", "inv_issue_master a, inv_transaction b,order_wise_pro_details c,wo_po_break_down d", "a.id=b.mst_id and a.booking_no=$txt_booking_no and a.booking_id=$txt_booking_id and a.issue_basis=1 and b.prod_id=$txt_prod_id and b.id=c.trans_id and c.trans_type=2 and c.po_breakdown_id=d.id and a.entry_form=3 and b.transaction_type=2 and a.status_active =1 and a.is_deleted=0  and b.status_active =1 and b.is_deleted=0 and d.job_no_mst=$job_no $dyeing_color_cond","issue_qnty");
                }
                else
                {
                    $job_no_cond = (str_replace("'", "", $job_no)!="")?" and b.job_no = $job_no":"";
        
                    if( $wo_entry_form == 94 && $is_sales==1)
                    {
                        $previous_issue_qnty = return_field_value("sum(b.cons_quantity) as issue_qnty", "inv_issue_master a, inv_transaction b", "a.id=b.mst_id and a.booking_no=$txt_booking_no and a.booking_id=$txt_booking_id and a.issue_basis=1 and b.prod_id=$txt_prod_id and b.job_no=$job_no and a.entry_form=3 and b.transaction_type=2 and a.status_active =1 and a.is_deleted=0  and b.status_active =1 and b.is_deleted=0 $dyeing_color_cond $job_no_cond", "issue_qnty");
                    }
                    else
                    {
                        $previous_issue_qnty = return_field_value("sum(b.cons_quantity) as issue_qnty", "inv_issue_master a, inv_transaction b", "a.id=b.mst_id and a.booking_no=$txt_booking_no and a.booking_id=$txt_booking_id and a.issue_basis=1 and b.prod_id=$txt_prod_id and a.entry_form=3 and b.transaction_type=2 and a.status_active =1 and a.is_deleted=0  and b.status_active =1 and b.is_deleted=0 $dyeing_color_cond $job_no_cond", "issue_qnty");
                    }
                }
                
                $total_issue_rtn_qty = return_field_value("sum(b.cons_quantity) total_issue_rtn", "inv_receive_master a, inv_transaction b", "a.id=b.mst_id and b.item_category=1 and b.transaction_type=4 and a.booking_id=$txt_booking_id and a.receive_basis in(1) and b.prod_id=$txt_prod_id  and b.status_active=1", "total_issue_rtn");
        
                $actualPreviousIssue = ($previous_issue_qnty-$total_issue_rtn_qty);
        
                if ( $operation == 0 )
                {
                    if ( ($txt_issue_qnty+$actualPreviousIssue) > str_replace("'", "", $hdn_wo_qnty) )
                    {
                        $balance = str_replace("'", "", $hdn_wo_qnty)-$actualPreviousIssue;
                        $pub_msg.= "11**Issue Quantity can not be greater than Work Order quantity.\nWork Order Quantity = ".str_replace("'", "", $hdn_wo_qnty)."\nCummulative Issue = ".$actualPreviousIssue."\nBalance = ".$balance;
                        return $pub_msg;
                    }
                }
                else if ( $operation == 1 )
                {
        
                    if($wo_entry_form==125 || $wo_entry_form==340 || $wo_entry_form==114)
                    {
                        $wo_qnty = return_field_value("yarn_wo_qty", "wo_yarn_dyeing_dtls", "mst_id=$txt_booking_id and yarn_color=$cbo_dyeing_color and count=$cbo_yarn_count and yarn_type=$cbo_yarn_type and status_active=1 and is_deleted=0","");//and product_id=$txt_prod_id
                    }
                    else
                    {
                        $wo_qnty = return_field_value("yarn_wo_qty", "wo_yarn_dyeing_dtls", "mst_id=$txt_booking_id and yarn_color=$cbo_dyeing_color and product_id=$txt_prod_id and status_active=1 and is_deleted=0","");
                    }
        
                    $hidden_p_issue_qnty = str_replace("'", "", $hidden_p_issue_qnty);
                    $iss_qnty = ($actualPreviousIssue - $hidden_p_issue_qnty) + ($hidden_p_issue_qnty - ($hidden_p_issue_qnty - $txt_issue_qnty));
        
                    if ( $iss_qnty > str_replace("'", "", $wo_qnty) )
                    {
                        $balance = str_replace("'", "", $wo_qnty) - $iss_qnty;
                        $pub_msg.= "11**Issue Quantity can not be greater than Work Order quantity.\nWork Order Quantity = ".str_replace("'", "", $wo_qnty)."\nCummulative Issue = ".$iss_qnty."\nBalance = ".$balance;
                        return $pub_msg;
                    }
                }
            }
            //echo "11**Failed $iss_qnty";die;
            
            if($operation == 0 || $operation == 1)
            {
                if($operation == 0)
                {
                    if ( $txt_issue_qnty > str_replace("'", "", $txt_current_stock) )
                    {
                        $pub_msg.= "11**Issue Quantity can not be greater than Current Stock quantity.\nCurrent Stock = " . str_replace("'", "", $txt_current_stock);
                        return $pub_msg;
                    }
                }
                else if($operation == 1)
                {
                    $prevIssueQty = return_field_value("cons_quantity", "inv_transaction" ," item_category=1 and transaction_type=2 and prod_id=$txt_prod_id and id=$update_id and store_id=$cbo_store_name and status_active=1 and is_deleted=0","cons_quantity");
        
                     $currentStock = number_format(str_replace("'", "", $txt_current_stock)+$prevIssueQty,2,'.','');
        
                    if ( number_format($txt_issue_qnty,2,'.','') > ($currentStock+$prevIssueQty) )
                    {
                        $pub_msg.= "11**Issue Quantity can not be greater than Current Stock quantity.\nCurrent Stock = " . $currentStock;
                        return $pub_msg;
                    }
                }
            }
            //print_r($saved_knitting_company);die;
            if( ($operation == 0 || $operation == 1) && (str_replace("'", "", $txt_system_no)!="") ) // once save
            {
                if (  ( str_replace("'", "", $cbo_knitting_company) != str_replace("'", "", $saved_knitting_company) )  )
                {
                    $pub_msg.= "20**Party mixing is not allowed in same issue number";
                    return $pub_msg;
                }
            }
            //print_r(6);die;
            //######### this stock item store level and calculate rate ########//
            $update_conds="";
            if($update_id > 0) $update_conds=" and id <> $update_id";
            $store_stock_sql="SELECT sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as BALANCE_STOCK, sum((case when transaction_type in(1,4,5) then store_amount else 0 end)-(case when transaction_type in(2,3,6) then store_amount else 0 end)) as BALANCE_AMT
            from inv_transaction
            where status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name $update_conds";
            //echo "20**$store_stock_sql";disconnect($con);die;
            $store_stock_sql_result=sql_select_arr($store_stock_sql);
            $store_item_rate=0;
            if($store_stock_sql_result[0]["BALANCE_AMT"]!=0 && $store_stock_sql_result[0]["BALANCE_STOCK"]!=0)
            {
                $store_item_rate=$store_stock_sql_result[0]["BALANCE_AMT"]/$store_stock_sql_result[0]["BALANCE_STOCK"];
            }
            $store_wise_stock = $store_stock_sql_result[0]["BALANCE_STOCK"];
            $issue_store_value = $store_item_rate*$txt_issue_qnty;


            //            
            //print_r($txt_system_no);die;
            //Insert Here---------------------------------------------------------------------------------------------------------------------
            if ($operation == 0) // Insert Here----------------------------------------------------------
            {
                //---------------Check Duplicate product in Same MRR number ------------------------//
                $requigitionCond = "";
                if (str_replace("'", "", $cbo_basis) == 8) $requigitionCond = " and a.buyer_id=" . $cbo_buyer_name . " and b.demand_no=" . $txt_req_no . " and b.requisition_no = ".$hdn_req_no;

                if (str_replace("'", "", $cbo_basis) == 3) $requigitionCond = " and a.buyer_id=" . $cbo_buyer_name . " and b.requisition_no=" . $txt_req_no . "";

                if ( (str_replace("'", "", $cbo_basis) == 3 || str_replace("'", "", $cbo_basis) == 8) && $txt_system_no !="" )
                {
                    $duplicate = is_duplicate_field("b.id", "inv_issue_master a, inv_transaction b", "a.id=b.mst_id and a.issue_number='$txt_system_no' and b.prod_id=$txt_prod_id  and b.transaction_type=2 and a.status_active=1 and b.status_active=1".$requigitionCond);

                    if ($duplicate == 1 && str_replace("'", "", $txt_system_no) != "")
                    {
                        $pub_msg.= "20**Duplicate Product is Not Allow in Same Issue Number.";
                        return $pub_msg;
                    }
                }
                //print_r(7);die;
                //------------------------------Check Brand END---------------------------------------//
                if (str_replace("'", "", $txt_system_no) != "") //new insert cbo_ready_to_approved
                {
                    $check_in_gate_pass = return_field_value("sys_number", "inv_gate_pass_mst", "challan_no='$txt_system_no' and status_active=1 and is_deleted=0", "sys_number");
                    if ($check_in_gate_pass != "") {
                        $pub_msg.="20**Gate Pass found.\nGate Pass ID = $check_in_gate_pass";
                        return $pub_msg;
                    }
                }

                //check for Requistion and Demand
                if ( (str_replace("'", "", $cbo_basis) == 3 || str_replace("'", "", $cbo_basis) == 8) && ( str_replace("'", "", $cbo_issue_purpose) == 1 || str_replace("'", "", $cbo_issue_purpose) == 4 || ( $variable_set_smn_allocation == 1 && str_replace("'", "", $cbo_issue_purpose) == 8) ) )
                {
                    $req_con = (str_replace("'", "", $cbo_basis) == 8)?" and demand_id=$demand_id and requisition_no=$hdn_req_no":" and requisition_no=$txt_req_no";

                    $total_pre_issue_qty = return_field_value("sum(cons_quantity) total_issue", "inv_transaction", "item_category=1 and transaction_type=2 $req_con and prod_id=$txt_prod_id  and status_active=1", "total_issue");

                    $total_issue_rtn_qty = return_field_value("sum(b.cons_quantity) total_issue_rtn", "inv_receive_master a, inv_transaction b", "a.id=b.mst_id and b.item_category=1 and b.transaction_type=4 and a.booking_id=$hdn_req_no and a.receive_basis in(3,8) and b.prod_id=$txt_prod_id  and b.status_active=1", "total_issue_rtn");

                    if(str_replace("'", "", $cbo_basis) == 8)
                    {
                        $total_req_qty = return_field_value("sum(yarn_demand_qnty) as total_req_qty", "ppl_yarn_demand_reqsn_dtls", "requisition_no=$hdn_req_no and mst_id=$demand_id and prod_id=$txt_prod_id and status_active=1", "total_req_qty");
                        $basis_label = "Demand";
                    }
                    else
                    {
                        $total_req_qty = return_field_value("sum(yarn_qnty) as total_req_qty", "ppl_yarn_requisition_entry", "requisition_no=$txt_req_no and prod_id=$txt_prod_id and status_active=1", "total_req_qty");
                        $basis_label = "Requisition";
                    }

                    $total_req_qty = number_format($total_req_qty,2,".","");
                    $total_pre_issue_qty = number_format($total_pre_issue_qty,2,".","");
                    $total_issue_rtn_qty = number_format($total_issue_rtn_qty,2,".","");
                    $txt_issue_qnty = number_format($txt_issue_qnty,2,".","");
                    $issueQtyZs = (($total_pre_issue_qty - $total_issue_rtn_qty) + $txt_issue_qnty);
                    //print_r($txt_issue_qnty);die;
                    /*
                    | if issue qty is greater than requisition qty and
                    | difference between issue qty and requisition qty is greater than 1 then
                    | system will give the following msg and execution will be stop
                    */
                    //print_r($total_req_qty);die;
                    if($issueQtyZs > $total_req_qty && ($issueQtyZs - $total_req_qty)>1)
                    {
                        $pub_msg.= "11**Issue Quantity can not be greater than $basis_label Quantity.\n$basis_label quantity = ".$total_req_qty . "\nBefore Issue = ". number_format($total_pre_issue_qty,2) . "\nAvailable quantity = " . ($total_req_qty - $total_pre_issue_qty);
                        return $pub_msg;
                    }
                }

                //product master table information
                $sql = sql_select_arr("select supplier_id, avg_rate_per_unit, current_stock, stock_value, allocated_qnty, available_qnty, dyed_type, is_twisted from product_details_master where id=$txt_prod_id and item_category_id=1");

                // transaction wise stock check
                $trans_sql = sql_select_arr("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end) -(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal_qnty from inv_transaction where status_active=1 and is_deleted=0 and prod_id=$txt_prod_id");

                //Store level transaction wise stock check
                $trans_store_sql = sql_select_arr("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end) -(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal_qnty from inv_transaction where status_active=1 and is_deleted=0 and prod_id=$txt_prod_id and store_id=$cbo_store_name");

                if($sql[0]['CURRENT_STOCK']<=0 || $trans_sql[0]['BAL_QNTY']<=0 || $trans_store_sql[0]['BAL_QNTY']<=0)
                {
                    $pub_msg.= "11**Stock quantity is not available";
                    return $pub_msg;
                }

                $avg_rate = $stock_qnty = $stock_value = $allocated_qnty = $available_qnty = 0;
                $supplier_id_for_tran = '';
                foreach ($sql as $result)
                {
                    $avg_rate = $result["AVG_RATE_PER_UNIT"];
                    $stock_qnty = $result["CURRENT_STOCK"];
                    $stock_value = $result["STOCK_VALUE"];
                    $allocated_qnty = $result["ALLOCATED_QNTY"];
                    $available_qnty = $result["AVAILABLE_QNTY"];
                    $supplier_id_for_tran = $result["SUPPLIER_ID"];
                    $dyed_type = $result["DYED_TYPE"];
                    $is_twisted = $result["IS_TWISTED"];
                }
                
                // If allocation is allowed
                // echo "20**".$variable_set_allocation;die;
                if ($variable_set_allocation == 1)
                {
                    if ( (str_replace("'", "", $cbo_basis) == 3 || str_replace("'", "", $cbo_basis) == 8) && ( str_replace("'", "", $cbo_issue_purpose) == 1 || str_replace("'", "", $cbo_issue_purpose) == 4  || ( $variable_set_smn_allocation == 1 && str_replace("'", "", $cbo_issue_purpose) == 8) ) )
                    {
                        $store_balance = $allocated_qnty;
                        $msg_qnt = "\nAllocation Quantity = ".$store_balance;
                        $msg_label = "Allocation";
                    }
                    else if ( str_replace("'", "", $cbo_basis) == 1 && ( str_replace("'", "", $cbo_issue_purpose) == 2 || str_replace("'", "", $cbo_issue_purpose) == 7 || str_replace("'", "", $cbo_issue_purpose) == 12 || str_replace("'", "", $cbo_issue_purpose) == 15 || str_replace("'", "", $cbo_issue_purpose) == 38 || str_replace("'", "", $cbo_issue_purpose) == 44 || str_replace("'", "", $cbo_issue_purpose) == 46 || str_replace("'", "", $cbo_issue_purpose) == 50 || str_replace("'", "", $cbo_issue_purpose) == 51 ) )
                    {
                        if (str_replace("'", "", $txt_entry_form) == 42 ||  str_replace("'", "", $txt_entry_form) == 114) // Without order
                        {
                            if($variable_set_smn_allocation==1) // sample booking allocation
                            {
                                $store_balance = $allocated_qnty;
                                $msg_label = "Allocation";
                                $msg_qnt = "\nAllocation Quantity = ".$store_balance;
                            }
                            else
                            {
                                $store_balance = $available_qnty;
                                $msg_label = "Available";
                                $msg_qnt = "\nAvailable Quantity = ".$store_balance;
                            }
                        }
                        else if( str_replace("'", "", $txt_entry_form) == 94 || str_replace("'", "", $txt_entry_form) == 340 ) // service wo
                        {
                            if ( str_replace("'", "", $job_no) != "" ) // With order
                            {
                                $store_balance = $allocated_qnty;
                                $msg_label = "Allocation";
                                $msg_qnt = "\nAllocation Quantity = ".$store_balance;
                            }
                            else // without order
                            {
                                /*if($variable_set_smn_allocation==1) // sample booking allocation
                                {
                                    $store_balance = $allocated_qnty;
                                    $msg_label = "Allocation";
                                    $msg_qnt = "\nAllocation Quantity = ".$store_balance;
                                }
                                else
                                {*/
                                    $store_balance = $available_qnty;
                                    $msg_label = "Available";
                                    $msg_qnt = "\nAvailable Quantity = ".$store_balance;
                                //}
                            }
                        }
                        else
                        {
                            $store_balance = $allocated_qnty;
                            $msg_label = "Allocation";
                            $msg_qnt = "\nAllocation Quantity = ".$store_balance;
                        }
                    }
                    else
                    {
                        $store_balance = $available_qnty;
                        $msg_label = "Available";
                        $msg_qnt = "\nAvailable Quantity = ".$store_balance;
                    }
                }
                else
                {
                    $store_balance = $available_qnty;
                    $msg_label = "Available";
                    $msg_qnt = "\nAvailable Quantity = ".$store_balance;
                }

                $store_balance = number_format($store_balance,2,".","");
                $availableChk = ( $store_balance >= $txt_issue_qnty ) ? true : false;

                $pub_msg.= "Issue Quantity can not be greater than $msg_label quantity.".$msg_qnt;
                if ($availableChk == false)
                {
                    $this->db->trans_rollback();
                    
                    $pub_msg.= "11**" . $msg;
                    return $pub_msg;
                }
               
                //if LIFO/FIFO then START -----------------------------------------//
                $field_array = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
                $update_array = "balance_qnty*balance_amount*updated_by*update_date";
                $cons_rate = 0;
                $data_array = "";
                $updateID_array = array();
                $update_data = array();
                $issueQnty = $txt_issue_qnty;
                // check variable settings issue method(LIFO/FIFO)
                $isLIFOfifo = '';
                $check_allocation = '';
                $sql_variable = sql_select_arr("SELECT store_method,allocation,variable_list from variable_settings_inventory where company_name=$cbo_company_id and variable_list in(17,18) and item_category_id=1 and status_active=1 and is_deleted=0");
                foreach ($sql_variable as $row)
                {
                    if ($row['variable_list'] == 17)
                    {
                        $isLIFOfifo = $row['store_method'];
                    }
                    else if ($row['variable_list'] == 18)
                    {
                        $check_allocation = $row['allocation'];
                    }
                    
                }
                //print_r($sql_variable);die;
                
                $transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", "");
                
                if ($isLIFOfifo == 2) $cond_lifofifo = " DESC"; else $cond_lifofifo = " ASC";
                // Trans type: 1=>"Receive",4=>"Issue Return",5=>"Item Transfer Receive"
                $sql_result = sql_select_arr("SELECT id,cons_rate,balance_qnty,balance_amount from inv_transaction where prod_id=$txt_prod_id and store_id=$cbo_store_name and balance_qnty>0 and transaction_type in (1,4,5) and item_category=1 and status_active=1 order by transaction_date,id $cond_lifofifo");

                if(!empty($sql_result))
                {
                    foreach ($sql_result as $result)
                    {
                        $recv_trans_id = $result["ID"]; // this row will be updated
                        $balance_qnty = $result["BALANCE_QNTY"];
                        $balance_amount = $result["BALANCE_AMOUNT"];
                        $cons_rate = $result["CONS_RATE"];
                        $issueQntyBalance = $balance_qnty - $issueQnty; // minus issue qnty
                        $issueStockBalance = $balance_amount - ($issueQnty * $cons_rate);
                        
                        if ($issueQntyBalance >= 0)
                        {
                            $amount = $issueQnty * $cons_rate;
                            //for insert
                            $mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", "");
                            if ($data_array != "") $data_array .= ",";
                            $data_array .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $transactionID . ",3," . $txt_prod_id . "," . $issueQnty . "," . number_format($cons_rate,10,'.','') . "," . number_format($amount,8,'.','') . ",'" . $user_id . "','" . $pc_date_time . "')";
                            //for update
                            $updateID_array[] = $recv_trans_id;
                            $update_data[$recv_trans_id] = explode("*", ("" . $issueQntyBalance . "*" . $issueStockBalance . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
                            break;
                        }
                        else if ($issueQntyBalance < 0)
                        {
                            //$issueQntyBalance = $balance_qnty+$issueQntyBalance; // adjust issue qnty
                            //$issueQntyBalance = $issueQntyBalance-$balance_qnty;
                            $issueQntyBalance = $issueQnty - $balance_qnty;
                            $issueQnty = $balance_qnty;
                            $amount = $issueQnty * $cons_rate;

                            //for insert
                            $mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
                            if ($data_array != "") $data_array .= ",";
                            $data_array .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $transactionID . ",3," . $txt_prod_id . "," . $balance_qnty . "," . number_format($cons_rate,10,'.','') . "," . number_format($amount,8,'.','') . ",'" . $user_id . "','" . $pc_date_time . "')";
                            //for update
                            $updateID_array[] = $recv_trans_id;
                            $update_data[$recv_trans_id] = explode("*", ("0*0*'" . $user_id . "'*'" . $pc_date_time . "'"));
                            $issueQnty = $issueQntyBalance;
                        }

                    }//end foreach
                }
                else
                {
                    $this->db->trans_rollback();
                    $pub_msg.= "11**" . "Mrr wise Balance is zero for this lot and store";
                    return $pub_msg;
                }
                $mrrWiseIssueID = true;
                $upTrID = true;
                // LIFO/FIFO then END-----------------------------------------------//
                

                // ------- order wise proportion start here ------------------------//
                $proportQ = true;
                $data_array_prop = "";
                $save_string = explode(",", str_replace("'", "", $save_data));

                if (count($save_string) > 0 && str_replace("'", "", $save_data) != "")
                {
                    $field_array_proportionate = "id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,issue_purpose,returnable_qnty,is_sales,inserted_by,insert_date";
                    //order_wise_pro_details table data insert START-----//

                    $po_array = $po_id_arr = array();
                    for ($i = 0; $i < count($save_string); $i++)
                    {
                        $order_dtls = explode("**", $save_string[$i]);
                        $order_id = $order_dtls[0];
                        $order_qnty = $order_dtls[1];
                        $returnable_qnty = $order_dtls[2];
                        $po_id_arr[$order_id] = $order_id;

                        if (array_key_exists($order_id, $po_array))
                        {
                            $po_array[$order_id] += $order_qnty;
                            $po_rt_array[$order_id] += $returnable_qnty;
                        }
                        else
                        {
                            $po_array[$order_id] = $order_qnty;
                            $po_rt_array[$order_id] = $returnable_qnty;
                        }
                    }
                    //echo "10**";
                    $is_salesOrder = 0;
                    if (str_replace("'", "", $cbo_basis) == 3 || str_replace("'", "", $cbo_basis) == 8)
                    {
                        if(str_replace("'", "", $cbo_basis) == 3)
                        {
                            $requisition_cond =" and b.requisition_no=$txt_req_no";
                        }
                        else{
                            $requisition_cond =" and b.requisition_no=$hdn_req_no";
                        }

                        $planning_info = sql_select_arr("SELECT a.is_sales,a.booking_no from ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b where a.dtls_id=b.knit_id and b.prod_id = $txt_prod_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $requisition_cond group by a.is_sales,a.booking_no");

                        $is_salesOrder = $planning_info[0]["IS_SALES"];
                        $requisition_booking = $planning_info[0]["BOOKING_NO"];
                    }

                    $order_wise_allocation_validation = 0;
                    //print_r($variable_set_allocation);die;
                    if ($variable_set_allocation == 1)
                    {
                        if ( (str_replace("'", "", $cbo_basis) == 3 || str_replace("'", "", $cbo_basis) == 8) && (str_replace("'", "", $cbo_issue_purpose) == 1 || str_replace("'", "", $cbo_issue_purpose) == 4) )
                        {
                            $order_wise_allocation_validation = ($is_auto_allocation_from_requisition==1)?0:1;

                            $requisition_booking_cond = ($requisition_booking!="")?"and a.booking_no='$requisition_booking'":"";
                            $allocation_sql = "SELECT b.po_break_down_id,sum(b.qnty) order_wise_allocation from inv_material_allocation_mst a,inv_material_allocation_dtls b where a.id=b.mst_id and a.item_id=$txt_prod_id and b.po_break_down_id in(".implode(",",$po_id_arr).") $requisition_booking_cond and a.status_active=1 and b.status_active=1 group by b.po_break_down_id";
                        }
                        else if (str_replace("'", "", $cbo_basis) == 1 && (str_replace("'", "", $cbo_issue_purpose) == 2 || str_replace("'", "", $cbo_issue_purpose) == 15 || str_replace("'", "", $cbo_issue_purpose) == 50 || str_replace("'", "", $cbo_issue_purpose) == 51 || str_replace("'", "", $cbo_issue_purpose) == 38 || str_replace("'", "", $cbo_issue_purpose) == 44 || str_replace("'", "", $cbo_issue_purpose) == 46 || str_replace("'", "", $cbo_issue_purpose) == 7 || str_replace("'", "", $cbo_issue_purpose) == 12 ) )
                        {
                            $wo_id = str_replace("'", "", $txt_booking_id);
                            $wo_job_no = str_replace("'", "", $job_no);

                            $wo_booking_no = return_field_value(" (fab_booking_no || booking_no) fab_booking_no ","wo_yarn_dyeing_dtls","mst_id ='".$wo_id."' and job_no ='".$wo_job_no."' and is_deleted=0 and status_active=1 group by fab_booking_no,booking_no","fab_booking_no");
                            $wo_booking_cond = ($wo_booking_no!="")?"and a.booking_no='$wo_booking_no'":"";
                            //print_r($wo_booking_cond);die;
                            if ( $variable_set_smn_allocation ==1 && str_replace("'", "", $txt_entry_form) == 42 || str_replace("'", "", $txt_entry_form) == 114 || str_replace("'", "", $txt_entry_form) == 135)
                            {
                                $order_wise_allocation_validation = ($is_auto_allocation_from_requisition==1)?0:1;

                                $allocation_sql = "SELECT b.po_break_down_id,sum(b.qnty) order_wise_allocation from inv_material_allocation_mst a,inv_material_allocation_dtls b where a.id=b.mst_id and a.item_id=$txt_prod_id and b.po_break_down_id in(".implode(",",$po_id_arr).") and a.status_active=1 and b.status_active=1 $wo_booking_cond group by b.po_break_down_id";
                            }
                            else if ( str_replace("'", "", $txt_entry_form) == 94 || str_replace("'", "", $txt_entry_form) == 340)
                            {
                                if (str_replace("'", "", $job_no) != "" )
                                {
                                    $order_wise_allocation_validation = ($is_auto_allocation_from_requisition==1)?0:1;

                                    $allocation_sql = "SELECT b.po_break_down_id,sum(b.qnty) order_wise_allocation from inv_material_allocation_mst a,inv_material_allocation_dtls b where a.id=b.mst_id and a.item_id=$txt_prod_id and b.po_break_down_id in(".implode(",",$po_id_arr).") and a.status_active=1 and b.status_active=1 $wo_booking_cond group by b.po_break_down_id";
                                }
                            }
                            else
                            {
                                $order_wise_allocation_validation = ($is_auto_allocation_from_requisition==1)?0:1;
                                $allocation_sql = "SELECT b.po_break_down_id,sum(b.qnty) order_wise_allocation from inv_material_allocation_mst a,inv_material_allocation_dtls b where a.id=b.mst_id and a.item_id=$txt_prod_id and b.po_break_down_id in(".implode(",",$po_id_arr).") and a.status_active=1 and b.status_active=1 $wo_booking_cond group by b.po_break_down_id";
                            }

                        }

                        $allocation_result = sql_select_arr($allocation_sql);
                        $order_wise_allocation_arr = array();
                        $total_allocation_qnty=0;
                        foreach ($allocation_result as $allocation_row) {
                            $order_wise_allocation_arr[$allocation_row["PO_BREAK_DOWN_ID"]] = $allocation_row["ORDER_WISE_ALLOCATION"];
                        }
                    }

                    if (str_replace("'", "", $txt_entry_form) == 135)
                    {
                        $is_salesOrder = 1;
                    }

                    if(str_replace("'", "", $txt_entry_form) == 94 || str_replace("'", "", $txt_entry_form) == 340)
                    {
                        $yarn_dyeing_info=sql_select_arr("SELECT entry_form,is_sales from wo_yarn_dyeing_mst where status_active=1 and ydw_no=$txt_booking_no and company_id=$cbo_company_id");
                        $is_salesOrder = $yarn_dyeing_info[0]['is_sales'];
                    }

                    $wo_job_cond = ($wo_job_no!="")?" and b.job_no='".$wo_job_no."'":"";
                    $wo_fabric_booking_cond = (str_replace("'","",$hdn_fabric_booking_no)!="")?" and b.booking_no=$hdn_fabric_booking_no":"";
                    $requisition_cond = ($hdn_req_no!="")?" and b.requisition_no=".$hdn_req_no."":"";

                    $order_wise_issue_sql = "SELECT b.mst_id as issue_id,b.issue_id as rtn_issue_id, a.trans_type,a.trans_id,a.po_breakdown_id, a.quantity, a.returnable_qnty,b.requisition_no,b.job_no
                    from order_wise_pro_details a, inv_transaction b
                    where a.entry_form =3  and a.trans_type = 2 and a.status_active=1 and a.prod_id=$txt_prod_id and a.trans_id=b.id and b.status_active=1 and b.transaction_type=2 $wo_job_cond $requisition_cond $wo_fabric_booking_cond";

                    $order_wise_result = sql_select_arr($order_wise_issue_sql);

                    foreach ($order_wise_result as $order_row)
                    {
                        $order_wise_issue_arr[$order_row["PO_BREAKDOWN_ID"]][$order_row["REQUISITION_NO"]] += $order_row["QUANTITY"];
                    }

                    $order_wise_issue_rtn_sql = sql_select_arr("SELECT b.mst_id as issue_id,b.issue_id as rtn_issue_id, a.trans_type,a.trans_id,a.po_breakdown_id, a.quantity, a.returnable_qnty,b.requisition_no,b.job_no
                        from order_wise_pro_details a, inv_transaction b
                        where a.entry_form = 9 and a.trans_type = 4 and a.status_active=1 and a.prod_id=$txt_prod_id and a.trans_id=b.id and b.status_active=1 and b.transaction_type=4 $wo_job_cond $requisition_cond");

                    foreach ($order_wise_issue_rtn_sql as $order_row)
                    {
                        $order_wise_issue_ret_arr[$order_row["PO_BREAKDOWN_ID"]] += $order_row["QUANTITY"];
                    }

                    $i = 0;
                    //echo "10**";
                    foreach ($po_array as $key => $val)
                    {
                        if ($i > 0) $data_array_prop .= ",";

                        $order_id = $key;
                        $order_qnty = number_format($val,2,".","");
                        $req_no = str_replace("'", "", $hdn_req_no);
                        $returnable_qnty = $po_rt_array[$key];

                        $order_wise_allocation_qnty = number_format($order_wise_allocation_arr[$order_id] - ($order_wise_issue_arr[$order_id][$req_no]-$order_wise_issue_ret_arr[$order_id]),2,".","");

                        if ($variable_set_allocation == 1 && $order_wise_allocation_validation==1 && $dyed_type!=1)
                        {
                            if($order_qnty > $order_wise_allocation_qnty)
                            {
                                //echo "10**".$order_qnty .">". $order_wise_allocation_qnty."<br>";
                                $pub_msg.= "   Issue Quantity can not be greater than Allocation quantity of this Order.\nAllocation in this Order = ".$order_wise_allocation_arr[$order_id]."\nTotal Issue = " . $order_wise_issue_arr[$order_id][$req_no] . "\nTotal Issue Return = " . $order_wise_issue_ret_arr[$order_id] . "\nBalance = $order_wise_allocation_qnty";

                                $this->db->trans_rollback();
                                $pub_msg.="11**" . $msg;
                                return $pub_msg;
                            }
                        }
                        //print_r(5);die;
                        $id_proport = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details","");
                        $data_array_prop .= "(" . $id_proport . "," . $transactionID . ",2,3," . $order_id . "," . $txt_prod_id . "," . $order_qnty . "," . $cbo_issue_purpose . ",'" . $returnable_qnty . "','" . $is_salesOrder . "'," . $user_id . ",'" . $pc_date_time . "')";
                        $i++;
                    }
                }
                //end if

                if (str_replace("'", "", $cbo_basis) == 4)
                {
                    $field_array_proportionate = "id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,issue_purpose,returnable_qnty,is_sales,inserted_by,insert_date";
                    $id_proport = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", "");
                    $data_array_prop = "(" . $id_proport . "," . $transactionID . ",2,3," . $txt_booking_id . "," . $txt_prod_id . "," . $txt_issue_qnty . "," . $cbo_issue_purpose . "," . $txt_returnable_qty . ",1," . $user_id . ",'" . $pc_date_time . "')";
                }
                //order_wise_pro_details table data insert END -----//
                ($txt_buyer_job_no)? $txt_buyer_job_no : $txt_buyer_job_no = "NULL";
                ($txt_style_ref)? $txt_style_ref : $txt_style_ref = "NULL";
                ($txt_booking_id)? $txt_booking_id : $txt_booking_id = $txt_req_no;
                ($txt_booking_no)? $txt_booking_no : $txt_booking_no = $txt_req_no;
                ($txt_remarks)? $txt_remarks : $txt_remarks = "NULL";
                ($txt_service_booking_no)? $txt_service_booking_no : $txt_service_booking_no = "NULL";
                ($txt_attention)? $txt_attention : $txt_attention = "NULL";
                ($txt_challan_no)? $txt_challan_no : $txt_challan_no = "NULL";
                //yarn issue master table entry here START---------------------------------------//
                if (str_replace("'", "", $txt_system_no) == "") //new insert cbo_ready_to_approved
                {
                    if ($db_type == 0) $year_cond = "YEAR(insert_date)";
                    else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
                    else $year_cond = "";//defined Later

                    $id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
                    $new_mrr_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_id,'YIS',3,date("Y",time()),1 ));

                    $field_array_mst = "id,issue_number_prefix, issue_number_prefix_num, issue_number, issue_basis, issue_purpose, entry_form, item_category, company_id, location_id, supplier_id, store_id, buyer_id, buyer_job_no, style_ref, booking_id, booking_no,service_booking_no, issue_date, sample_type, knit_dye_source, knit_dye_company, challan_no, loan_party, remarks, ready_to_approve, attention, inserted_by, insert_date";
                    $data_array_mst = "(" . $id . ",'" . $new_mrr_number[1] . "','" . $new_mrr_number[2] . "','" . $new_mrr_number[0] . "'," . $cbo_basis . "," . $cbo_issue_purpose . ",3,1," . $cbo_company_id . "," . $cbo_location_id . "," . $cbo_supplier . "," . $cbo_store_name . "," . $cbo_buyer_name . "," . $txt_buyer_job_no . "," . $txt_style_ref . "," . $txt_booking_id . "," . $txt_booking_no . "," . $txt_service_booking_no . ",'" . $txt_issue_date . "'," . $cbo_sample_type . "," . $cbo_knitting_source . "," . $cbo_knitting_company . "," . $txt_challan_no . "," . $cbo_loan_party . "," . $txt_remarks . "," . $cbo_ready_to_approved . "," . $txt_attention . ",'" . $user_id . "','" . $pc_date_time . "')";
                }
                else //update
                {
                    $new_mrr_number[0] = str_replace("'", "", $txt_system_no);
                    $id = return_field_value("id", "inv_issue_master", "issue_number='$txt_system_no'","");
                    //print_r($id);die;
                    $field_array_mst = "issue_basis*issue_purpose*entry_form*item_category*company_id*location_id*supplier_id*store_id*buyer_id*buyer_job_no*style_ref*booking_id*booking_no*service_booking_no*issue_date*sample_type*knit_dye_source*knit_dye_company*challan_no*loan_party*remarks*ready_to_approve*attention*updated_by*update_date";
                    $data_array_mst = "" . $cbo_basis . "*" . $cbo_issue_purpose . "*3*1*" . $cbo_company_id . "*" . $cbo_location_id . "*" . $cbo_supplier . "*" . $cbo_store_name . "*" . $cbo_buyer_name . "*" . $txt_buyer_job_no . "*" . $txt_style_ref . "*" . $txt_booking_id . "*" . $txt_booking_no . "*" . $txt_service_booking_no . "*'" . $txt_issue_date . "'*" . $cbo_sample_type . "*" . $cbo_knitting_source . "*" . $cbo_knitting_company . "*" . $txt_challan_no . "*" . $cbo_loan_party . "*" . $txt_remarks . "*" . $cbo_ready_to_approved . "*" . $txt_attention . "*'" . $user_id . "'*'" . $pc_date_time . "'";
                    $id = str_replace("'", "", $update_id_mst);
                }
                //yarn issue master table entry here END---------------------------------------//

                //for transaction log
                $log_ref_id = $id;
                $log_ref_number = $new_mrr_number[0];
                $log_entry_form = 3;
                $log_prod_id = $txt_prod_id;

                /******** original product id check start ********/
                $origin_prod_id = return_field_value("origin_prod_id", "inv_transaction", "prod_id=$txt_prod_id and status_active=1 and store_id=$cbo_store_name and balance_qnty>0 and transaction_type in (1,4,5) and item_category=1", "origin_prod_id");
                /******** original product id check end ********/

                //inventory TRANSACTION table data entry START----------------------------------------------------------//
                //$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
                $txt_issue_qnty = str_replace("'", "", $txt_issue_qnty);
                $issue_stock_value = $avg_rate * $txt_issue_qnty;

                $basis = str_replace("'", "", $cbo_basis);
                $demand_system_no = ($basis==8)?str_replace("'", "", $txt_req_no):'';
                if(str_replace("'", "", $hdn_req_no)!="")
                    $txt_req_no = str_replace("'", "", $hdn_req_no);
                //print_r($txt_returnable_qty);die;
                $txt_req_no=str_replace("'", "", $txt_req_no);
                $fabric_booking_no = $requisition_booking = $wo_booking_no;
                $field_array_trans = "id,mst_id,requisition_no,receive_basis,company_id,supplier_id,prod_id,origin_prod_id,dyeing_color_id,item_category,transaction_type,transaction_date,store_id,brand_id,cons_uom,cons_quantity,return_qnty,item_return_qty,cons_rate,cons_amount,no_of_bags,cone_per_bag,weight_per_bag,weight_per_cone,floor_id,room,rack,self,bin_box,using_item,job_no,inserted_by,insert_date,btb_lc_id,demand_id,demand_no,remarks,wo_id,pi_wo_batch_no,booking_no,store_rate,store_amount,IS_EXCEL";
                $data_array_trans = "(" . $transactionID . "," . $id . ",'" . $txt_req_no . "'," . $cbo_basis . "," . $cbo_company_id . ",'" . $supplier_id_for_tran . "'," . $txt_prod_id . ",'" . $origin_prod_id . "'," . $cbo_dyeing_color . ",1,2,'" . $txt_issue_date . "'," . $cbo_store_name . "," . $cbo_brand . "," . $cbo_uom . "," . $txt_issue_qnty . "," . $txt_returnable_qty . "," . $extra_quantity . "," . number_format($avg_rate,10,'.','') . "," . number_format($issue_stock_value,8,'.','') . "," . $txt_no_bag . "," . $txt_no_cone . "," . $txt_weight_per_bag . "," . $txt_weight_per_cone . "," . $cbo_floor . "," . $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_bin . "," . $cbo_item . "," . $job_no . ",'" . $user_id . "','" . $pc_date_time . "'," . $txt_btb_lc_id . "," . $demand_id . ",'" . $demand_system_no . "'," . $txt_remarks_dtls . "," . $txt_wo_id . "," . $txt_pi_id . ",'" . $fabric_booking_no . "',".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').",2)";
                //
                //remarks

                //inventory TRANSACTION table data entry  END----------------------------------------------------------//

                //weighted and average rate START here------------------------//
                //product master table data UPDATE START----------------------//
                $currentStock = $stock_qnty - $txt_issue_qnty;
                $StockValue = $stock_value - ($txt_issue_qnty * $avg_rate);
                //$avgRate	 	= number_format($StockValue/$currentStock,10,'.','');
                $avgRate = number_format($avg_rate, '.', '');
                //newly added code here =============================================

                //item allocation----------------------------------------------------
                $allocated_qnty_balance = 0;
                $available_qnty_balance = 0;
                // if yarn allocation variable set to yes
                if ($variable_set_allocation == 1)
                {
                    if ( ( str_replace("'", "", $cbo_basis) == 3 || str_replace("'", "", $cbo_basis) == 8 ) && (str_replace("'", "", $cbo_issue_purpose) == 1 || str_replace("'", "", $cbo_issue_purpose) == 4 || ( $variable_set_smn_allocation == 1 && str_replace("'", "", $cbo_issue_purpose) == 8) ) )
                    {
                        $allocated_qnty_balance = $allocated_qnty - $txt_issue_qnty;
                        $available_qnty_balance = $available_qnty;
                    }
                    else if ( str_replace("'", "", $cbo_basis) == 1 && ( str_replace("'", "", $cbo_issue_purpose) == 2 || str_replace("'", "", $cbo_issue_purpose) == 7 || str_replace("'", "", $cbo_issue_purpose) == 12 || str_replace("'", "", $cbo_issue_purpose) == 15 || str_replace("'", "", $cbo_issue_purpose) == 38 || str_replace("'", "", $cbo_issue_purpose) == 44 || str_replace("'", "", $cbo_issue_purpose) == 46  || str_replace("'", "", $cbo_issue_purpose) == 50 || str_replace("'", "", $cbo_issue_purpose) == 51 ) )
                    {

                        if (str_replace("'", "", $txt_entry_form) == 42 || str_replace("'", "", $txt_entry_form) == 114) // Without order
                        {
                            if($variable_set_smn_allocation==1) // sample booking allocation
                            {
                                $allocated_qnty_balance = $allocated_qnty - $txt_issue_qnty;
                                $available_qnty_balance = $available_qnty;
                            }
                            else
                            {
                                $allocated_qnty_balance = $allocated_qnty;
                                $available_qnty_balance = $available_qnty - $txt_issue_qnty;
                            }
                        }
                        else if(str_replace("'", "", $txt_entry_form) == 94 || str_replace("'", "", $txt_entry_form) == 340) // service wo
                        {
                            if (str_replace("'", "", $job_no) != "" ) // with order
                            {
                                $allocated_qnty_balance = $allocated_qnty - $txt_issue_qnty;
                                $available_qnty_balance = $available_qnty;
                            }
                            else // without order
                            {
                                /*
                                if($variable_set_smn_allocation==1) // sample booking allocation
                                {
                                    $allocated_qnty_balance = $allocated_qnty - $txt_issue_qnty;
                                    $available_qnty_balance = $available_qnty;
                                }
                                else
                                {*/
                                    $allocated_qnty_balance = $allocated_qnty;
                                    $available_qnty_balance = $available_qnty - $txt_issue_qnty;
                                //}
                            }
                        }
                        else // 41,125,135
                        {
                            $allocated_qnty_balance = $allocated_qnty - $txt_issue_qnty;
                            $available_qnty_balance = $available_qnty;
                        }
                    }
                    else
                    {
                        $allocated_qnty_balance = $allocated_qnty;
                        $available_qnty_balance = $available_qnty - $txt_issue_qnty;
                    }
                }
                else
                {
                    $allocated_qnty_balance = $allocated_qnty;
                    $available_qnty_balance = $available_qnty - $txt_issue_qnty;
                }

                if ($allocated_qnty_balance == "") $allocated_qnty_balance = 0;
                if ($available_qnty_balance == "") $available_qnty_balance = 0;

                $field_array_prod = "last_issued_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
                $data_array_prod = "" . $txt_issue_qnty . "*" . $currentStock . "*" . number_format($StockValue, 8, '.', '') . "*" . $allocated_qnty_balance . "*" . $available_qnty_balance . "*'" . $user_id . "'*'" . $pc_date_time . "'";

                //for transaction log
                $log_current_stock = $currentStock;
                $log_allocated_qty = $allocated_qnty_balance;
                $log_available_qty = $available_qnty_balance;

                //------------------ product_details_master END--------------//
                $store_up_id=0;
                if($variable_store_wise_rate == 1)
                {
                    $sql_store = sql_select_arr("SELECT id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_yarn_qty_dtls where status_active=1 and prod_id=$txt_prod_id and category_id=1 and store_id=$cbo_store_name and company_id=$cbo_company_id");

                    if(count($sql_store)<1)
                    {
                        $pub_msg.= "20**No Data Found.";
                        return $pub_msg;
                    }
                    elseif(count($sql_store)>1)
                    {
                        $pub_msg.= "20**Duplicate Product is Not Allow in Same REF Number.";
                        return $pub_msg;
                    }
                    else
                    {
                        $store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
                        foreach($sql_store as $result)
                        {
                            $store_up_id=$result["ID"];
                            $store_presentStock	=$result["CURRENT_STOCK"];
                            $store_presentStockValue =$result["STOCK_VALUE"];
                            $store_presentAvgRate	=$result["AVG_RATE_PER_UNIT"];
                        }

                        $field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date";
                        $currentStock_store		=$store_presentStock-$txt_issue_qnty;
                        $currentValue_store		=$store_presentStockValue-$issue_store_value;
                        $data_array_store= "".$txt_issue_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
                    }
                }

                //weighted and average rate END here-------------------------//

                $this->db->trans_begin();
                if (str_replace("'", "", $txt_system_no) == "")
                {
                    $rID = sql_insert("inv_issue_master", $field_array_mst, $data_array_mst, 0);
                }
                else
                {
                    $rID = sql_update("inv_issue_master", $field_array_mst, $data_array_mst, "id", $id, 0);
                }
                //print_r($id);die;
                //echo "10**INSERT INTO inv_transaction (".$field_array_trans.") VALUES ".$data_array_trans.""; die;
                //echo "10**INSERT INTO inv_mrr_wise_issue_details (".$field_array.") VALUES ".$data_array.""; die;
                $transID = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);
                $mrrWiseIssueID = sql_insert("inv_mrr_wise_issue_details", $field_array, $data_array, 0);
                $upTrID = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array, $update_data, $updateID_array),0);
                $prodUpdate = sql_update("product_details_master", $field_array_prod, $data_array_prod, "id", $txt_prod_id, 0);
                //print_r($field_array);die;
                $proportQ=$storeRID=true;
                if ($data_array_prop != "")
                {
                    //echo "10**INSERT INTO order_wise_pro_details (".$field_array_proportionate.") VALUES ".$data_array_prop.""; die;
                    $proportQ = sql_insert("order_wise_pro_details", $field_array_proportionate, $data_array_prop, 0);
                }

                if($store_up_id>0 && $variable_store_wise_rate == 1)
                {
                    $storeRID=sql_update("inv_store_wise_yarn_qty_dtls",$field_array_store,$data_array_store,"id",$store_up_id,1);
                }

                //echo "10**".$rID. "&&". $transID. "&&".$prodUpdate. "&&". $proportQ . "&&". $mrrWiseIssueID . "&&". $upTrID . "&&". $storeRID;oci_rollback($con);disconnect($con); die;

                if ($db_type == 0)
                {
                    if ($rID && $transID && $prodUpdate && $proportQ && $mrrWiseIssueID && $upTrID && $storeRID)
                    {   $pub_msg = "";
                        $this->db->trans_commit();
                        $pub_msg.= "  0**" . $new_mrr_number[0] . "**" . $id. "**" . str_replace("'", "", $cbo_knitting_company);
                    }
                    else
                    {
                        $this->db->trans_rollback();
                        $pub_msg.="   10**" . $new_mrr_number[0] . "**" . $id. "**" . str_replace("'", "", $cbo_knitting_company);
                    }
                }
                else if ($db_type == 2 || $db_type == 1)
                {
                    if ($rID && $transID && $prodUpdate && $proportQ && $mrrWiseIssueID && $upTrID && $storeRID)
                    {
                        //for transaction log
                        $log_data['entry_form'] = $log_entry_form;
                        $log_data['ref_id'] = $log_ref_id;
                        $log_data['ref_number'] = $log_ref_number;
                        $log_data['product_id'] = $log_prod_id;
                        $log_data['current_stock'] = $log_current_stock;
                        $log_data['allocated_qty'] = $log_allocated_qty;
                        $log_data['available_qty'] = $log_available_qty;
                        $log_data['dyed_type'] = $dyed_type;
                        $log_data['insert_date'] = $pc_date_time;
                        $allocation_log = manage_allocation_transaction_log($log_data);

                        if($allocation_log)
                        {   $pub_msg = "commit";
                            $this->db->trans_commit();
                            $pub_msg.= "0**" . $new_mrr_number[0] . "**" . $id. "**" . str_replace("'", "", $cbo_knitting_company);
                            $success_status = 200;
                        }
                        else
                        {
                            $pub_msg="rollback";
                            $this->db->trans_rollback();
                            $pub_msg.= "10**" . $new_mrr_number[0] . "**" . $id. "**" . str_replace("'", "", $cbo_knitting_company);
                        }

                    }
                    else
                    {
                        $this->db->trans_rollback();
                        $pub_msg.= "10**0";
                    }
                }
                return $pub_msg;
            }
            
        }

        public function createObj_grn_wise_yarn_data_for_issue_save($obj){
            $obj_arr = json_decode($obj);
            //print_r($obj_arr->BASIS);die;
            $basis_id = $obj_arr->BASIS;
            $req_no = $obj_arr->SYSTEM_NO;
            $user_id = $obj_arr->USER_ID;
            $dtls_id = $obj_arr->DTLS_ID;
            $issue_perpose = $obj_arr->ISSUE_PERPOSE;
            $location_id = $obj_arr->LOCATION_ID;
            $challan_no = $obj_arr->CHALLAN_NO;
            $mst_remarks = $obj_arr->MST_REMARKS;
            $issue_qnty = $obj_arr->ISSUE_QNTY;
            $return_qty = $obj_arr->RETURN_QTY;
            $no_of_bag = $obj_arr->NO_OF_BAG;
            $no_of_cone = $obj_arr->NO_OF_CONE;
            $weight_per_bag = $obj_arr->WEIGHT_PER_BAG;
            $weight_per_cone = $obj_arr->WEIGHT_PER_CONE;
            $ready_to_approved = $obj_arr->READY_TO_APPROVED;
            $attention = $obj_arr->ATTENTION;
            $dtls_remarks = $obj_arr->DTLS_REMARKS;
            //print_r($basis_id);die;
    
    
            if($basis_id==3){
                $query_product = "SELECT a.PROD_ID,c.COMPANY_ID,c.LOCATION_ID,c.RECEIVE_BASIS,a.ID,a.REQUISITION_NO,c.ORDER_UOM,b.LOT,b.PRODUCT_NAME_DETAILS,b.YARN_TYPE,b.COLOR,SUM(a.YARN_QNTY) as REQ_QNTY,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,b.SUPPLIER_ID as PRO_SUPPLIER_ID,c.STORE_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,b.YARN_COUNT_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID FROM PPL_YARN_REQUISITION_ENTRY a,PRODUCT_DETAILS_MASTER b,INV_TRANSACTION c WHERE a.STATUS_ACTIVE = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0 and c.STATUS_ACTIVE = 1 and c.IS_DELETED = 0 and a.PROD_ID = $dtls_id
                 and a.PROD_ID = b.ID and c.REQUISITION_NO = a.REQUISITION_NO group by b.LOT,b.PRODUCT_NAME_DETAILS,b.COLOR,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,a.REQUISITION_NO,a.ID,c.COMPANY_ID,c.RECEIVE_BASIS,a.PROD_ID,c.ORDER_UOM,c.LOCATION_ID,c.STORE_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.YARN_TYPE,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID,b.SUPPLIER_ID,b.YARN_COUNT_ID";
                $table_product = $this->db->query($query_product)->row();
                //print_r($table_product);die;
                $requisition_query = "SELECT a.COMPANY_ID,3 as RCV_BASIS,a.BOOKING_NO,b.KNITTING_PARTY as KNITTING_COMPANY,a.BUYER_ID,c.REQUISITION_NO,c.prod_id,c.REQUISITION_DATE,sum(c.YARN_QNTY) as YARN_QNTY,c.ID as REQUISI_ID,b.KNITTING_SOURCE,b.KNITTING_PARTY
                from PPL_PLANNING_INFO_ENTRY_MST a, PPL_PLANNING_INFO_ENTRY_DTLS b, PPL_YARN_REQUISITION_ENTRY c
                where a.ID=b.MST_ID and b.ID=c.KNIT_ID 
                and c.requisition_no='$req_no' 
                and c.status_active=1 and c.is_deleted=0
                group by a.company_id,a.booking_no,c.requisition_no,c.prod_id,c.requisition_date,a.buyer_id,b.KNITTING_PARTY,c.ID,b.KNITTING_SOURCE,b.KNITTING_PARTY";
                //print_r($query_product);die;
                $table = $this->db->query($requisition_query)->row();
                //print_r($query_product);die;
                $poId_issueQnty_returnQnty = "";
                $poIds = "";
            
             return  $response_arr = [
                    "db_type"=> 1,
                    "operation"=> 0,
                    "user_id"=> $user_id,
                    "txt_system_no"=> "NULL",
                    "cbo_company_id"=> $table_product->COMPANY_ID,
                    "cbo_basis"=> $table_product->RECEIVE_BASIS,
                    "cbo_issue_purpose"=> $issue_perpose,
                    "txt_issue_date"=> date('d-M-Y',time()),
                    "txt_booking_no"=> "NULL",
                    "txt_booking_id"=> "NULL",
                    "cbo_location_id"=> $location_id,
                    "cbo_knitting_source"=> $table->KNITTING_SOURCE,
                    "cbo_knitting_company"=> $table->KNITTING_COMPANY,
                    "cbo_supplier"=> $table->SUPPLIER_ID,
                    "cbo_store_name"=> $table->STORE_ID,
                    "txt_challan_no"=> $challan_no,
                    "cbo_loan_party"=> 0,
                    "cbo_buyer_name"=> $table->BUYER_ID,
                    "txt_style_ref"=> "NULL",
                    "txt_buyer_job_no"=> "NULL",
                    "cbo_sample_type"=> "0",
                    "txt_remarks"=> $mst_remarks,
                    "txt_req_no"=> $table->REQUISITION_NO,
                    "txt_lot_no"=> $table_product->LOT,
                    "cbo_yarn_count"=> $table_product->YARN_COUNT_ID,
                    "cbo_color"=> 0,
                    "cbo_floor"=> $table_product->FLOOR_ID,
                    "cbo_room"=> $table_product->ROOM,
                    "txt_issue_qnty"=> $issue_qnty,
                    "txt_returnable_qty"=> $return_qty,
                    "txt_composition"=> $table_product->YARN_COMP_TYPE1ST,
                    "cbo_brand"=> $table_product->BRAND,
                    "txt_rack"=> $table_product->RACK,
                    "txt_no_bag"=> $no_of_bag,
                    "txt_no_cone"=> $no_of_cone,
                    "txt_weight_per_bag"=> $weight_per_bag,
                    "txt_weight_per_cone"=> $weight_per_cone,
                    "cbo_yarn_type"=> $table_product->YARN_TYPE,
                    "cbo_dyeing_color"=> 0,
                    "txt_shelf"=> $table_product->SELF,
                    "txt_current_stock"=> $table_product->CURRENT_STOCK,
                    "cbo_uom"=> $table_product->UNIT_OF_MEASURE,
                    "cbo_item"=> $table_product->ITEM_CATEGORY_ID,
                    "update_id_mst"=> 0,
                    "update_id"=> "NULL",
                    "save_data"=> $poId_issueQnty_returnQnty,
                    "all_po_id"=> $poIds,
                    "txt_prod_id"=> $table_product->PROD_ID,
                    "job_no"=> "NULL",
                    "cbo_ready_to_approved"=> $ready_to_approved,
                    "cbo_supplier_lot"=> $table_product->PRO_SUPPLIER_ID,
                    "txt_btb_lc_id"=> "NULL",
                    "extra_quantity"=> 0,
                    "txt_entry_form"=> 0,
                    "hidden_p_issue_qnty"=> 0,
                    "hdn_wo_qnty"=> "NULL",
                    "txt_service_booking_no"=> "NULL",
                    "demand_id"=> 0,
                    "hdn_req_no"=> $table->REQUISITION_NO,
                    "original_save_data"=> "NULL",
                    "cbo_bin"=> $table_product->BIN_BOX,
                    "saved_knitting_company"=> $table->KNITTING_PARTY,
                    "txt_attention"=> $attention,
                    "txt_remarks_dtls"=> $dtls_remarks,
                    "txt_wo_id"=> "NULL",
                    "txt_pi_id"=> "NULL",
                    "hdn_fabric_booking_no"=> "NULL",
                ];
                //print_r($response_arr);die;


            }else if($basis_id==8){  
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
                //print_r($demand_query);die;
                $prod_id_arr = array();
                foreach($table as $row){
                    $prod_id_arr[$row->PROD_ID]=$row->PROD_ID;
                }
                $prod_ids_str = implode(',',$prod_id_arr);
                $req_no = $table[0]->DEMAND_ID;

                $query_product = "SELECT a.PROD_ID,c.COMPANY_ID,c.LOCATION_ID,c.RECEIVE_BASIS,a.ID,a.REQUISITION_NO,c.ORDER_UOM,b.LOT,b.PRODUCT_NAME_DETAILS,b.YARN_TYPE,b.COLOR,SUM(a.YARN_QNTY) as REQ_QNTY,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,b.SUPPLIER_ID as PRO_SUPPLIER_ID,c.STORE_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,b.YARN_COUNT_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID FROM PPL_YARN_REQUISITION_ENTRY a,PRODUCT_DETAILS_MASTER b,INV_TRANSACTION c WHERE a.STATUS_ACTIVE = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0 and c.STATUS_ACTIVE = 1 and c.IS_DELETED = 0 and a.PROD_ID = $dtls_id
                and a.PROD_ID = b.ID and c.REQUISITION_NO = a.REQUISITION_NO group by b.LOT,b.PRODUCT_NAME_DETAILS,b.COLOR,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,a.REQUISITION_NO,a.ID,c.COMPANY_ID,c.RECEIVE_BASIS,a.PROD_ID,c.ORDER_UOM,c.LOCATION_ID,c.STORE_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.YARN_TYPE,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID,b.SUPPLIER_ID,b.YARN_COUNT_ID";
               
                $table_product = $this->db->query($query_product)->row();
    
                $poId_issueQnty_returnQnty = "";
                $poIds = "";

               return $response_arr = [
                    "db_type"=> 1,
                    "operation"=> 0,
                    "user_id"=> $user_id,
                    "txt_system_no"=> "NULL",
                    "cbo_company_id"=> $table_product->COMPANY_ID,
                    "cbo_basis"=> $table_product->RECEIVE_BASIS,
                    "cbo_issue_purpose"=> $issue_perpose,
                    "txt_issue_date"=> date('d-M-Y',time()),
                    "txt_booking_no"=> "NULL",
                    "txt_booking_id"=> "NULL",
                    "cbo_location_id"=> $location_id,
                    "cbo_knitting_source"=> $table->KNITTING_SOURCE,
                    "cbo_knitting_company"=> $table->KNITTING_COMPANY,
                    "cbo_supplier"=> $table->SUPPLIER_ID,
                    "cbo_store_name"=> $table->STORE_ID,
                    "txt_challan_no"=> $challan_no,
                    "cbo_loan_party"=> 0,
                    "cbo_buyer_name"=> $table->BUYER_ID,
                    "txt_style_ref"=> "NULL",
                    "txt_buyer_job_no"=> "NULL",
                    "cbo_sample_type"=> "0",
                    "txt_remarks"=> $mst_remarks,
                    "txt_req_no"=> $table->DEMAND_SYSTEM_NO,
                    "txt_lot_no"=> $table_product->LOT,
                    "cbo_yarn_count"=> $table_product->YARN_COUNT_ID,
                    "cbo_color"=> 0,
                    "cbo_floor"=> $table_product->FLOOR_ID,
                    "cbo_room"=> $table_product->ROOM,
                    "txt_issue_qnty"=> $issue_qnty,
                    "txt_returnable_qty"=> $return_qty,
                    "txt_composition"=> $table_product->YARN_COMP_TYPE1ST,
                    "cbo_brand"=> $table_product->BRAND,
                    "txt_rack"=> $table_product->RACK,
                    "txt_no_bag"=> $no_of_bag,
                    "txt_no_cone"=> $no_of_cone,
                    "txt_weight_per_bag"=> $weight_per_bag,
                    "txt_weight_per_cone"=> $weight_per_cone,
                    "cbo_yarn_type"=> $table_product->YARN_TYPE,
                    "cbo_dyeing_color"=> 0,
                    "txt_shelf"=> $table_product->SELF,
                    "txt_current_stock"=> $table_product->CURRENT_STOCK,
                    "cbo_uom"=> $table_product->UNIT_OF_MEASURE,
                    "cbo_item"=> $table_product->ITEM_CATEGORY_ID,
                    "update_id_mst"=> 0,
                    "update_id"=> "NULL",
                    "save_data"=> $poId_issueQnty_returnQnty,
                    "all_po_id"=> $poIds,
                    "txt_prod_id"=> $table_product->PROD_ID,
                    "job_no"=> "NULL",
                    "cbo_ready_to_approved"=> $ready_to_approved,
                    "cbo_supplier_lot"=> $table_product->PRO_SUPPLIER_ID,
                    "txt_btb_lc_id"=> "NULL",
                    "extra_quantity"=> 0,
                    "txt_entry_form"=> 0,
                    "hidden_p_issue_qnty"=> 0,
                    "hdn_wo_qnty"=> "NULL",
                    "txt_service_booking_no"=> "NULL",
                    "demand_id"=> $table->DEMAND_ID,
                    "hdn_req_no"=> $table->REQUISITION_NO,
                    "original_save_data"=> "NULL",
                    "cbo_bin"=> $table_product->BIN_BOX,
                    "saved_knitting_company"=> $table->KNITTING_PARTY,
                    "txt_attention"=> $attention,
                    "txt_remarks_dtls"=> $dtls_remarks,
                    "txt_wo_id"=> "NULL",
                    "txt_pi_id"=> "NULL",
                    "hdn_fabric_booking_no"=> "NULL",
                ];
    
            }else if($basis_id==1){
    
                $requisition_query = "SELECT a.COMPANY_ID, 1 as RCV_BASIS,a.BOOKING_DATE,a.SUPPLIER_ID,b.PRODUCT_ID,e.PO_BUYER,b.YARN_COLOR,a.SUPPLIER_ID,a.YDW_NO,a.ENTRY_FORM,b.JOB_NO,c.BUYER_NAME,a.SOURCE from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b left join wo_po_details_master c on b.job_no_id=c.id left join wo_non_ord_samp_booking_mst d on b.booking_no=d.booking_no left join fabric_sales_order_mst e on e.job_no=b.job_no 
                where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
                and a.ydw_no = '$req_no' 
                and a.entry_form in(41,42,114,125,135)";
                //print_r($requisition_query);die;
                $table = $this->db->query($requisition_query)->result();
    
    
                $prod_id_arr = array();
                foreach($table as $row){
                    $prod_id_arr[$row->PRODUCT_ID]=$row->PRODUCT_ID;
                }
                $prod_ids_str = implode(',',$prod_id_arr);
                //$req_no = $table[0]->DEMAND_ID;
                $query_product = "SELECT a.PROD_ID,c.COMPANY_ID,c.LOCATION_ID,c.RECEIVE_BASIS,a.ID,a.REQUISITION_NO,c.ORDER_UOM,b.LOT,b.PRODUCT_NAME_DETAILS,b.YARN_TYPE,b.COLOR,SUM(a.YARN_QNTY) as REQ_QNTY,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,b.SUPPLIER_ID as PRO_SUPPLIER_ID,c.STORE_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,b.YARN_COUNT_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID FROM PPL_YARN_REQUISITION_ENTRY a,PRODUCT_DETAILS_MASTER b,INV_TRANSACTION c WHERE a.STATUS_ACTIVE = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0 and c.STATUS_ACTIVE = 1 and c.IS_DELETED = 0 and a.PROD_ID = $dtls_id
                and a.PROD_ID = b.ID and c.REQUISITION_NO = a.REQUISITION_NO group by b.LOT,b.PRODUCT_NAME_DETAILS,b.COLOR,b.CURRENT_STOCK, c.RETURN_QNTY,c.SUPPLIER_ID,c.FLOOR_ID,c.ROOM,c.RACK,c.SELF,c.BIN_BOX,a.REQUISITION_NO,a.ID,c.COMPANY_ID,c.RECEIVE_BASIS,a.PROD_ID,c.ORDER_UOM,c.LOCATION_ID,c.STORE_ID,b.YARN_COMP_TYPE1ST,b.BRAND,b.YARN_TYPE,b.UNIT_OF_MEASURE,b.ITEM_CATEGORY_ID,b.SUPPLIER_ID,b.YARN_COUNT_ID";
                //print_r($query_product);die;
                $table_product = $this->db->query($query_product)->result();

                $poId_issueQnty_returnQnty = "";
                $poIds = "";

                return $response_arr = [
                    "db_type"=> 1,
                    "operation"=> 0,
                    "user_id"=> $user_id,
                    "txt_system_no"=> "NULL",
                    "cbo_company_id"=> $table_product->COMPANY_ID,
                    "cbo_basis"=> $table_product->RECEIVE_BASIS,
                    "cbo_issue_purpose"=> $issue_perpose,
                    "txt_issue_date"=> date('d-M-Y',time()),
                    "txt_booking_no"=> $table_product->YDW_NO,
                    "txt_booking_id"=> $table_product->YDW_ID,
                    "cbo_location_id"=> $location_id,
                    "cbo_knitting_source"=> $table->SOURCE,
                    "cbo_knitting_company"=> $table->SUPPLIER_ID,
                    "cbo_supplier"=> $table->SUPPLIER_ID,
                    "cbo_store_name"=> $table->STORE_ID,
                    "txt_challan_no"=> $challan_no,
                    "cbo_loan_party"=> 0,
                    "cbo_buyer_name"=> $table->BUYER_NAME,
                    "txt_style_ref"=> "NULL",
                    "txt_buyer_job_no"=> $table->JOB_NO,
                    "cbo_sample_type"=> "0",
                    "txt_remarks"=> $mst_remarks,
                    "txt_req_no"=> $table->REQUISITION_NO,
                    "txt_lot_no"=> $table_product->LOT,
                    "cbo_yarn_count"=> $table_product->YARN_COUNT_ID,
                    "cbo_color"=> 0,
                    "cbo_floor"=> $table_product->FLOOR_ID,
                    "cbo_room"=> $table_product->ROOM,
                    "txt_issue_qnty"=> $issue_qnty,
                    "txt_returnable_qty"=> $return_qty,
                    "txt_composition"=> $table_product->YARN_COMP_TYPE1ST,
                    "cbo_brand"=> $table_product->BRAND,
                    "txt_rack"=> $table_product->RACK,
                    "txt_no_bag"=> $no_of_bag,
                    "txt_no_cone"=> $no_of_cone,
                    "txt_weight_per_bag"=> $weight_per_bag,
                    "txt_weight_per_cone"=> $weight_per_cone,
                    "cbo_yarn_type"=> $table_product->YARN_TYPE,
                    "cbo_dyeing_color"=> 0,
                    "txt_shelf"=> $table_product->SELF,
                    "txt_current_stock"=> $table_product->CURRENT_STOCK,
                    "cbo_uom"=> $table_product->UNIT_OF_MEASURE,
                    "cbo_item"=> $table_product->ITEM_CATEGORY_ID,
                    "update_id_mst"=> 0,
                    "update_id"=> "NULL",
                    "save_data"=> $poId_issueQnty_returnQnty,
                    "all_po_id"=> $poIds,
                    "txt_prod_id"=> $table_product->PROD_ID,
                    "job_no"=> $table->JOB_NO,
                    "cbo_ready_to_approved"=> $ready_to_approved,
                    "cbo_supplier_lot"=> $table_product->PRO_SUPPLIER_ID,
                    "txt_btb_lc_id"=> "NULL",
                    "extra_quantity"=> 0,
                    "txt_entry_form"=> $table->ENTRY_FORM,
                    "hidden_p_issue_qnty"=> 0,
                    "hdn_wo_qnty"=> "NULL",
                    "txt_service_booking_no"=> "NULL",
                    "demand_id"=> 0,
                    "hdn_req_no"=> $table->YDW_NO,
                    "original_save_data"=> "NULL",
                    "cbo_bin"=> $table_product->BIN_BOX,
                    "saved_knitting_company"=> $table->SUPPLIER_ID,
                    "txt_attention"=> $attention,
                    "txt_remarks_dtls"=> $dtls_remarks,
                    "txt_wo_id"=> "NULL",
                    "txt_pi_id"=> "NULL",
                    "hdn_fabric_booking_no"=> "NULL",
                ];
            }

                //basis = 1 
		// $response_arr = '{
		// 	--"db_type": 1,
		// 	--"operation": 0, --default 0
		// 	"user_id": 1,
		// 	--"cbo_company_id": "1", --company_id from trans table
		// 	--"cbo_basis": "3", --inv master table er basis 
		// 	--"txt_issue_date": "26-Dec-2023", --Today date gone to trans table
		// 	**--"txt_booking_no": "Null", -- WO_YARN_DYEING_MST er YDW_NO
		// 	**--"txt_booking_id": "Null",-- WO_YARN_DYEING_MST er ID
		// 	--"cbo_location_id": "108", -- ppl_planning_info_entry_dtls er location id, if not found then user will give it. Knitting source "inhouse" hole location lagbei. ppl_planning_info_entry_dtls this table give the knitting source column
		// 	--"cbo_knitting_source": "1", -- ppl_planning_info_entry_dtls er knitting source
		// }';
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
