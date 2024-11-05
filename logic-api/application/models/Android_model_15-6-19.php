<?php
include('grade_class.php');
include('company_class.php');
include('source_class.php');
include('common_class.php');
include('defect_class.php');
include('inch_class.php');
include('qc_dtls_class.php');

class Android_model extends CI_Model {

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

        /*$result = $this->db->get_where($tableName, $attribute)->row();
        if (!empty($result)):
            return $result->{$fieldName};
        else:
            return false;
        endif;*/
    }
    public function apps_login($phone)
    {
        $data_array=array();
        $sql="SELECT phone from apps_user where phone='$phone'";
        $data_sql=sql_select($sql);
        if(count($data_sql))
        {
          foreach($data_sql as $v)
          {
            $data_array["phone"]=$v->phone;
          }
          
        }
        return $data_array;

    }

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
      
      //$loc_sql = "select ID,LOCATION_NAME,COMPANY_ID from lib_location where status_active =1 and is_deleted=0 order by location_name";
       

      $com_res = $this->db->query($comp_sql)->result();
      // $loc_res = $this->db->query($loc_sql)->result();
      

      $data_arr['company_info'] = $com_res;
      //$data_arr['location_info'] = $loc_res;
      $data_arr['user_id'] = $user_id;
     
      return $data_arr;
    }
    public function company_and_source_data()
    {
        $data_arr=array();
        $comp=$this->company_list();
        $supplier=$this->supplier_list();
        $db_type=return_db_type();
        $machine_array=array();
        if($db_type==0)
        {
          $machine_array=return_library_array( "SELECT id, concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=4 and status_active=1 and is_deleted=0 and is_locked=0", "id", "machine_name");
        }
        else
        {
          $machine_array=return_library_array( "SELECT id, (machine_no || '-' || brand) as machine_name from lib_machine_name where category_id=4 and status_active=1 and is_deleted=0 and is_locked=0", "id", "machine_name");
        }
         

        $knitting_source =array(1=>"In-house",3=>"Out-bound Subcontract");
        $shift_name=array(1=>"A",2=>"B",3=>"C");
        $knitting_source_arr=array();
        foreach($knitting_source as $kk=>$vv)
        {
           $obj=new Source($kk, $vv );
           $knitting_source_arr[]=$obj;
        }

        $shift_arr=array();
        foreach($shift_name as $kk=>$vv)
        {
           $obj=new Source($kk, $vv );
           $shift_arr[]=$obj;
        }

        $machine_arr=array();
        foreach($machine_array as $kk=>$vv)
        {
           $obj=new Source($kk, $vv );
           $machine_arr[]=$obj;
        }


        $data_arr["company"]=$comp;
        $data_arr["supplier"]=$supplier;      
        $data_arr["source"]=$knitting_source_arr;      
        $data_arr["shift"]=$shift_arr;      
        $data_arr["machine"]=$machine_arr;      
        return $data_arr;  

    }

    public function company_list()
    {
       $data_array=array();
       $comp_sql = "select ID,COMPANY_NAME from lib_company comp where status_active =1 and is_deleted=0  order by company_name";
       foreach(sql_select($comp_sql) as $val)
       {
          $obj=new Company($val->ID, $val->COMPANY_NAME );
          $data_array[]=$obj;
       }
       return $data_array;
    }
    public function company_wise_loc_data($company=0)
    {
        $data_array=array();
        $loc_sql="SELECT ID,LOCATION_NAME from lib_location where status_active =1 and is_deleted=0 and company_id='$company' order by location_name";
       foreach(sql_select($loc_sql) as $val)
       {
          $obj=new Source($val->ID, $val->LOCATION_NAME);
          $data_array[]=$obj;
       }
       return $data_array;

    }


    public function loc_wise_floor_data($location=0)
    {
        $data_array=array();
        $floor_sql="SELECT ID,FLOOR_NAME from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$location' and production_process=5 order by floor_name";
       foreach(sql_select($floor_sql) as $val)
       {
          $obj=new Source($val->ID, $val->FLOOR_NAME);
          $data_array[]=$obj;
          
       }
       return $data_array;

    }

    public function sewing_barcode_data($company=0,$barcode="",$type)
    {
      $data_arr=array();
      $db_type=return_db_type();
      $scnbundle_nos_cond=" and b.barcode_no in ('$barcode')";
      $scanned_bundle_arr = return_library_array("SELECT b.BUNDLE_NO, b.BUNDLE_NO from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type='$type' and b.status_active=1 and b.is_deleted=0 $scnbundle_nos_cond", 'bundle_no', 'bundle_no');
      $size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
      $color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
      $country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
      $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
      $garments_item=return_library_array("select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name","id","item_name");

      $year_field = "";
      if ($db_type == 0) 
      {
        $year_field = "YEAR(f.insert_date)";
      } 
      else if ($db_type == 2)
      {
        $year_field = "to_char(f.insert_date,'YYYY')";
      }
      if($type==4)
      {
          $input_sql="SELECT barcode_no from pro_garments_production_dtls where status_active=1 and production_type=4 and barcode_no='$barcode'";
          $input_exist_data=sql_select($input_sql);
          if(count($input_exist_data)>0) return $data_arr;

      }
      else
      {
          $output_sql="SELECT sum(reject_qty+alter_qty+spot_qty)-sum(replace_qty) as qnty  from pro_garments_production_dtls where status_active=1 and production_type=5 and barcode_no='$barcode'   and is_rescan=0 having  (sum(reject_qty+alter_qty+spot_qty)-sum(replace_qty) )<=0  ";
           $output_exist_data=sql_select($output_sql);
          if(count($output_exist_data)>0) {return $data_arr;}

          $output_sql_rescan="SELECT sum(reject_qty+alter_qty+spot_qty)-sum(replace_qty) as qnty  from pro_garments_production_dtls where status_active=1 and production_type=5 and barcode_no='$barcode'   and is_rescan=0 having  sum(reject_qty+alter_qty+spot_qty) -sum(replace_qty)>0  ";
          $output_rescan_data=sql_select($output_sql_rescan);
          if(count($output_rescan_data)>0)
          {

            $sqls="SELECT c.COLOR_TYPE_ID, max(c.id) as prdid, d.id as COLORSIZEID, e.id as PO_ID, f.JOB_NO_PREFIX_NUM, MAX($year_field) as YEAR, f.BUYER_NAME, d.ITEM_NUMBER_ID, d.COUNTRY_ID, d.SIZE_NUMBER_ID, d.COLOR_NUMBER_ID, c.cut_no,c.BUNDLE_NO, sum(case when is_rescan=0 then c.reject_qty+c.spot_qty+c.alter_qty-c.replace_qty else 0 end )-sum(case when is_rescan=1 then production_qnty else 0 end) as PRODUCTION_QNTY, e.PO_NUMBER,c.BARCODE_NO,1 as IS_RESCAN from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$company' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.production_type =5   and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and c.barcode_no='$barcode'    group by c.COLOR_TYPE_ID,d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";

            $result = sql_select($sqls);
            foreach($result as $v)
            {

              $data_arr["bundle_no"]=$v->BUNDLE_NO;
              $data_arr["barcode_no"]=$v->BARCODE_NO;

              $data_arr["year"]=$v->YEAR;

              $data_arr["color_size_id"]=$v->COLORSIZEID;
              $data_arr["order_id"]=$v->PO_ID;
              $data_arr["item_id"]=$v->ITEM_NUMBER_ID;
              $data_arr["country_id"]=$v->COUNTRY_ID;
              $data_arr["size_id"]=$v->SIZE_NUMBER_ID;
              $data_arr["color_id"]=$v->COLOR_NUMBER_ID;
              $data_arr["cut_no"]=$v->CUT_NO;

              $data_arr["job_no"]=$v->JOB_NO_PREFIX_NUM;

              if(isset($buyer_arr[$v->BUYER_NAME]))
                $data_arr["buyer"]=$buyer_arr[$v->BUYER_NAME];
              else  $data_arr["buyer"]="";

              $data_arr["order_no"]=$v->PO_NUMBER;

              if(isset($garments_item[$v->ITEM_NUMBER_ID]))
                $data_arr["item"]=$garments_item[$v->ITEM_NUMBER_ID];
              else $data_arr["item"]="";

              if(isset($country_arr[$v->COUNTRY_ID]))
                $data_arr["country"]=$country_arr[$v->COUNTRY_ID];
              else $data_arr["country"]="";

              if(isset($color_arr[$v->COLOR_NUMBER_ID]))
                $data_arr["color"]=$color_arr[$v->COLOR_NUMBER_ID]; 
              else  $data_arr["color"]="";

              if(isset($size_arr[$v->SIZE_NUMBER_ID]))
                $data_arr["size"]=$size_arr[$v->SIZE_NUMBER_ID];
              else $data_arr["size"]="";

              $data_arr["qty"]=$v->PRODUCTION_QNTY;
              $data_arr["is_rescan"]=$v->IS_RESCAN;
              $data_arr["color_type_id"]=$v->COLOR_TYPE_ID;
            }

            return  $data_arr;
              
          }

      }
      $col_size_seq="SELECT color_size_break_down_id as IDS,CUT_NO from pro_garments_production_dtls where status_active=1 and is_deleted=0 and barcode_no='$barcode'";
      $col_size_seq_arr=array();
      $cut_arr=array();
      foreach(sql_select($col_size_seq) as $v)
      {
        $col_size_seq_arr[$v->IDS]=$v->IDS;
        $cut_arr[$v->CUT_NO]=$v->CUT_NO;
      }
      $ids=implode(",", $col_size_seq_arr);
      if(!$ids)$ids=0;

      $cut_nos="'".implode("','", $cut_arr)."'";
      if(!$cut_nos)$cut_nos="'0"."'";

      $source_sql="SELECT PRECEDING_OP from pro_production_sequence where CURRENT_OPERATION='$type' and COL_SIZE_ID in($ids) and CUTTING_NO in($cut_nos) ";
      $source_val=0;
      foreach(sql_select($source_sql) as $vl)
      {
        $source_val=$vl->PRECEDING_OP ;
      }
      $source_cond=$source_val;
      //if($type==4)$source_cond="1";else $source_cond="4";
      
       $sqls="SELECT  c.COLOR_TYPE_ID,  0 as IS_RESCAN,max(c.id) as prdid, d.id as COLORSIZEID, e.id as PO_ID, f.JOB_NO_PREFIX_NUM, MAX($year_field) as YEAR, f.BUYER_NAME, d.ITEM_NUMBER_ID, d.COUNTRY_ID, d.SIZE_NUMBER_ID, d.COLOR_NUMBER_ID, c.cut_no,c.BUNDLE_NO, sum(c.production_qnty) as PRODUCTION_QNTY, e.PO_NUMBER,c.BARCODE_NO from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$company' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.production_type =$source_cond   and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and c.barcode_no='$barcode'    group by c.COLOR_TYPE_ID, d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
      
        $result = sql_select($sqls);
        foreach($result as $v)
        {
           
          $data_arr["bundle_no"]=$v->BUNDLE_NO;
          $data_arr["barcode_no"]=$v->BARCODE_NO;

          $data_arr["year"]=$v->YEAR;

          $data_arr["color_size_id"]=$v->COLORSIZEID;
          $data_arr["order_id"]=$v->PO_ID;
          $data_arr["item_id"]=$v->ITEM_NUMBER_ID;
          $data_arr["country_id"]=$v->COUNTRY_ID;
          $data_arr["size_id"]=$v->SIZE_NUMBER_ID;
          $data_arr["color_id"]=$v->COLOR_NUMBER_ID;
          $data_arr["cut_no"]=$v->CUT_NO;

          $data_arr["job_no"]=$v->JOB_NO_PREFIX_NUM;

          if(isset($buyer_arr[$v->BUYER_NAME]))
          $data_arr["buyer"]=$buyer_arr[$v->BUYER_NAME];
          else  $data_arr["buyer"]="";

          $data_arr["order_no"]=$v->PO_NUMBER;

          if(isset($garments_item[$v->ITEM_NUMBER_ID]))
          $data_arr["item"]=$garments_item[$v->ITEM_NUMBER_ID];
          else $data_arr["item"]="";

          if(isset($country_arr[$v->COUNTRY_ID]))
          $data_arr["country"]=$country_arr[$v->COUNTRY_ID];
          else $data_arr["country"]="";

          if(isset($color_arr[$v->COLOR_NUMBER_ID]))
          $data_arr["color"]=$color_arr[$v->COLOR_NUMBER_ID]; 
          else  $data_arr["color"]="";

          if(isset($size_arr[$v->SIZE_NUMBER_ID]))
          $data_arr["size"]=$size_arr[$v->SIZE_NUMBER_ID];
          else $data_arr["size"]="";
          
          $data_arr["qty"]=$v->PRODUCTION_QNTY;
          $data_arr["is_rescan"]=$v->IS_RESCAN;
          $data_arr["color_type_id"]=$v->COLOR_TYPE_ID;
        }

       return  $data_arr;
    }
    public function sewing_line_data($company_id=0, $location=0, $floor=0,$issue_date="")
    {
      if($this->db->dbdriver=='mysqli') 
      {
        $db_type=0;
      }
      else
      {
        $db_type=2;

      }
      $new_arr=array(); 
      $line_array_new=array();

      $nameArray = sql_select("SELECT ID, AUTO_UPDATE from variable_settings_production where company_name='$company_id' and variable_list=23 and status_active=1 and is_deleted=0");
      $prod_reso_allocation =0;
      foreach($nameArray as $v)
         $prod_reso_allocation=$v->AUTO_UPDATE;

      $cond = "";
      if ($prod_reso_allocation == 1) 
      {
        $line_library = return_library_array("SELECT ID,LINE_NAME from lib_sewing_line", "id", "line_name");
        $line_array = array();

        if ($floor == 0 && $location != 0) $cond = " and a.location_id= $location";
        if ($floor != 0) $cond = " and a.floor_id= $floor";

        
        
        if($db_type==0)
        {
          $issue_date = date("Y-m-d",strtotime($issue_date));
        }
        else
        {
          $issue_date = change_date_format(date("Y-m-d",strtotime($issue_date)),'','',1,$db_type);
        }
        
        $cond.=" and b.pr_date='".$issue_date."'";


        if ($db_type == 0) 
        {
          $line_data = sql_select("SELECT A.ID, A.LINE_NUMBER from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num  order by a.prod_resource_num asc, a.id asc");
        } 
        else if ($db_type == 2 || $db_type == 1)
         {
          $line_data = sql_select("SELECT A.ID, A.LINE_NUMBER from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num  order by a.prod_resource_num asc, a.id asc");
        }
        
        
        $line_merge=9999; 
        foreach($line_data as $row)
        {
          $line='';
          $line_number=explode(",",$row->LINE_NUMBER);
          foreach($line_number as $val)
          {
            if(count($line_number)>1)
            {
              $line_merge++;
              $new_arr[$line_merge]=$row->ID;
            }
            else
              $new_arr[$line_library[$val]]=$row->ID;

            if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
          }
          $line_array[$row->ID]=$line;
        }
        if(!empty($new_arr))
        ksort($new_arr);
        foreach($new_arr as $key=>$v)
        {
           //$line_array_new[$v]=$line_array[$v];
           $obj=new Source($v , $line_array[$v]);
           $line_array_new[]=$obj;
        }
       return $line_array_new;
        
      } 
      else
      {
        $data_array=array();
        if ($floor == 0 && $location != 0) $cond = " and location_name= $location";
        if ($floor != 0) $cond = " and floor_name= $floor"; else  $cond = " and floor_name like('%%')";

        $sqls="SELECT ID,LINE_NAME from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name";
        foreach(sql_select($sqls) as $val)
        {
           $obj=new Source($val->ID, $val->LINE_NAME);
           $data_array[]=$obj;
          
        }
        return $data_array;

      }

    }
    public function supplier_list()
    {
       $data_array=array();
       $supp_sql = "SELECT a.ID,a.SUPPLIER_NAME from lib_supplier a,lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and  b.party_type in(22,23) order by a.supplier_name";
       $supp_arr=array();
       foreach(sql_select($supp_sql) as $val)
       {
          $obj=new Source($val->ID,$val->SUPPLIER_NAME);
          $supp_arr[]=$obj;
       }
       return  $supp_arr;
    }

    public function menu_details_data($user_id)
    {
       $module_sql="SELECT M_MOD_ID, MAIN_MODULE from main_module";
       $menu_sql="SELECT  M_MENU_ID, MENU_NAME from main_menu";
       $module_arr=array();
       $menu_arr=array();
       $module_arr[0]=0;
       foreach(sql_select($module_sql) as $val)
       {
         $module_arr[$val->M_MOD_ID]=$val->MAIN_MODULE;
       }
       $menu_arr[0]=0;
       foreach(sql_select($menu_sql) as $val)
       {
         $menu_arr[$val->M_MENU_ID]=$val->MENU_NAME;
       }

      $menu_data="SELECT  a.M_MODULE_ID,a.ROOT_MENU,a.SUB_ROOT_MENU,a.M_MENU_ID, a.MENU_NAME from main_menu a,user_priv_mst b where a.m_menu_id=b.main_menu_id and b.user_id='$user_id' and  a.is_mobile_menu=1  and a.status=1 and a.f_location is not null   group by a.M_MODULE_ID,a.ROOT_MENU,a.SUB_ROOT_MENU,a.M_MENU_ID, a.MENU_NAME ";
    
      $data_array=array();
      
      foreach(sql_select($menu_data) as $rows)
      {
        
        $data_array[$module_arr[$rows->M_MODULE_ID]."**".$rows->M_MODULE_ID][$rows->M_MENU_ID]=$rows->MENU_NAME;
        
      }
      return $data_array;


    }



    public function array_ref_data($compId="0",$arrs,$type,$qc_mst_tble_id)
    {
       //return $arrs;
      $db_type=return_db_type();
       $fabric_shade=array(1=>"A",2=>"B",3=>"C",4=>"D",5=>"E");
       $knit_defect_inchi_array=array(1=>'Defect=<3" : 1',2=>'Defect=<6" but >3" : 2',3=>'Defect=<9" but >6" : 3',4=>'Defect>9" : 4',5=>'Hole<1" : 2',6=>'Hole>1" : 4');

       //$defect_name_sql=sql_select("SELECT ID,DEFECT_NAME from  lib_defect_name where status_active=1 and is_deleted=0  ");
       $knit_defect_array=array(1=>"Hole",5=>"Loop",10=>"Press Off",15=>"Lycra Out",20=>"Lycra Drop",21=>"Lycra Out/Drop",25=>"Dust",30=>"Oil Spot",35=>"Fly Conta",40=>"Slub",45=>"Patta",50=>"Needle Break",55=>"Sinker Mark",60=>"Wheel Free",65=>"Count Mix",70=>"Yarn Contra",75=>"NEPS",80=>"Black Spot",85=>"Oil/Ink Mark",90=>"Set up",95=>"Pin Hole",100=>"Slub Hole",105=>"Needle Mark",110=>"Miss Yarn",115=>"Color Contra [Yarn]",120=>"Color/dye spot",125=>"friction mark",130=>"Pin out",135=>"softener spot",140=>"Dirty Spot",145=>"Rust Stain",150=>"Stop mark",155=>"Compacting Broken",160=>"Insect Spot",165=>"Grease spot",166=>"Knot");
       if($type==2)
       {
          $knit_defect_array=array(1=>"Hole", 5=>"Color Spot", 10=>"Insect Spot", 15=>"Yellow Spot", 20=>"Poly Conta", 25=>"Dust", 30=>"Oil Spot", 35=>"Fly Conta", 40=>"Slub", 45=>"Patta/Barrie Mark", 50=>"Cut/Joint", 55=>"Sinker Mark", 60=>"Print Mis", 65=>"Yarn Conta", 70=>"Slub Hole", 75=>"Softener Spot",     95=>"Dirty Stain", 100=>"NEPS", 105=>"Needle Drop", 110=>"Chem: Stain", 115=>"Cotton seeds", 120=>"Loop hole", 125=>"Dead Cotton", 130=>"Thick & Thin", 135=>"Rust Spot", 140=>"Needle Broken Mark", 145=>"Dirty Spot", 150=>"Side To Center Shade", 155=>"Bowing", 160=>"Uneven", 165=>"Yellow Writing", 170=>"Fabric Missing", 175=>"Dia Mark", 180=>"Miss Print", 185=>"Hairy", 190=>"G.S.M Hole", 195=>"Compacting Mark", 200=>"Rib Body Shade", 205=>"Running Shade", 210=>"Plastic Conta", 215=>"Crease mark", 220=>"Patches", 225=>"M/c Stoppage", 230=>"Needle Line", 235=>"Crample mark", 240=>"White Specks", 245=>"Mellange Effect", 250=>"Line Mark", 255=>"Loop Out", 260=>"Needle Broken"  );
          $defect_wise_others=array();
           
       }
       

       //return $defect_name_arr;
       $grade_arr=array();
       $knit_defect_arr=array();
       $defect_arr=array();
       if(!$compId)$compId=1;
      // $grade_sql=sql_select("SELECT b.RANGE_SERIAL,b.GRADE from  buyer_wise_grade_mst a,buyer_wise_grade_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.buyer_id='$buyer_id' ");
       $grade_sql="SELECT FABRIC_GRADE, GET_UPVALUE_FIRST,GET_UPVALUE_SECOND from variable_settings_production where COMPANY_NAME='$compId' AND VARIABLE_LIST = 36 and status_active=1 and is_deleted=0 " ;
       foreach(sql_select($grade_sql) as $v)
       {
          //$grade_arr[$v->RANGE_SERIAL]=$fabric_shade[$v->GRADE];
          for($kk=$v->GET_UPVALUE_FIRST;$kk<=$v->GET_UPVALUE_SECOND;$kk++)
          {
              $obj=new Grade($kk, $v->FABRIC_GRADE );
              $grade_arr[]=$obj;
          }
          

       }
       if($arrs)
       {
          foreach($knit_defect_array as $k=>$v)
          {
            $def_id=$k;
            if(isset($arrs[$def_id]["DEFECT_COUNT"]))
              $count=$arrs[$def_id]["DEFECT_COUNT"];
            else
            $count=0;
            if(isset($arrs[$def_id]["FOUND_IN_INCH"]))
            $inchs=$arrs[$def_id]["FOUND_IN_INCH"];
            else
            $inchs=0;
            if(isset($arrs[$def_id]["PENALTY_POINT"]))
            $ttl_point=$arrs[$def_id]["PENALTY_POINT"];
            else
               $ttl_point=0;
           
            $def_obj=new Defect($def_id,$v,$count,$inchs,$ttl_point );
            $defect_arr[]=$def_obj;
          }

       }
       else
       {
          if($qc_mst_tble_id)
          {
              $dtls_sql="SELECT  DEFECT_NAME, DEFECT_COUNT, FOUND_IN_INCH, PENALTY_POINT FROM pro_qc_result_dtls Where MST_ID  in($qc_mst_tble_id)";
              foreach(sql_select($dtls_sql) as $val)
              {
                $defect_wise_others[$val->DEFECT_NAME]["DEFECT_COUNT"]=$val->DEFECT_COUNT;
                $defect_wise_others[$val->DEFECT_NAME]["FOUND_IN_INCH"]=$val->FOUND_IN_INCH;
                $defect_wise_others[$val->DEFECT_NAME]["PENALTY_POINT"]=$val->PENALTY_POINT;
              }

              foreach($knit_defect_array as $k=>$v)
              {
                 $DEFECT_COUNT=0;
                 if(isset($defect_wise_others[$k]["DEFECT_COUNT"]))
                  $DEFECT_COUNT=$defect_wise_others[$k]["DEFECT_COUNT"];

                $FOUND_IN_INCH=0;
                 if(isset($defect_wise_others[$k]["FOUND_IN_INCH"]))
                  $FOUND_IN_INCH=$defect_wise_others[$k]["FOUND_IN_INCH"];


                $PENALTY_POINT=0;
                 if(isset($defect_wise_others[$k]["PENALTY_POINT"]))
                  $PENALTY_POINT=$defect_wise_others[$k]["PENALTY_POINT"];

                 $def_obj=new Defect($k ,$v, $DEFECT_COUNT,$FOUND_IN_INCH,$PENALTY_POINT );
                 $defect_arr[]=$def_obj;
              }

          }
          else 
          {
            foreach($knit_defect_array as $k=>$v)
            {
               $def_obj=new Defect($k ,$v,0,0,0 );
               $defect_arr[]=$def_obj;
            }
              

          }
          

       }

       

       foreach($knit_defect_inchi_array as $k=>$v)
       {
         $inch_obj=new INCH($k ,$v );
         $knit_defect_arr[]=$inch_obj;
       }

       


      $data_array=array("defect"=>$defect_arr,"grade"=>$grade_arr  );
       
      return $data_array;


    }
    public function machine_data()
    {
       $db_type=return_db_type();
        $machine_array=array();
        if($db_type==0)
        {
          $machine_array=return_library_array( "SELECT id, concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=4 and status_active=1 and is_deleted=0 and is_locked=0", "id", "machine_name");
        }
        else
        {
          $machine_array=return_library_array( "SELECT id, (machine_no || '-' || brand) as machine_name from lib_machine_name where category_id=4 and status_active=1 and is_deleted=0 and is_locked=0", "id", "machine_name");
        }
        $machine_arr=array();
        $kk=0;
        foreach($machine_array as $kk=>$vv)
        {
           //$obj=new Source($kk, $vv );
           $machine_arr[$kk]["id"]=$kk;
           $machine_arr[$kk]["name"]=$vv;
           $kk++;
       }
      return $machine_arr;

    }
    public function user_wise_menu_data($user_id)
    {
      $default_arr=array();
      $default_arr[0]["menu_name"]="";
      $default_arr[0]["location"]="";
      $default_arr[0]["save"]=0;
      $default_arr[0]["update"]=0;
      $default_arr[0]["delete"]=0;
      $default_arr[0]["show"]=0;
      $default_arr[0]["approve"]=0;

      $menu_sql="SELECT a.position, b.SHOW_PRIV, a.MENU_NAME,a.F_LOCATION, b.DELETE_PRIV, b.SAVE_PRIV,b.EDIT_PRIV, b.APPROVE_PRIV from main_menu a ,user_priv_mst b where a.M_MENU_ID=b.MAIN_MENU_ID and a.status=1 and a.is_mobile_menu=1 and b.user_id='$user_id' group by a.position,b.SHOW_PRIV, a.MENU_NAME,a.F_LOCATION, b.DELETE_PRIV, b.SAVE_PRIV,b.EDIT_PRIV, b.APPROVE_PRIV order by a.position asc ";
      $data_array=array();
      $arr_data=sql_select($menu_sql);
      if(count($arr_data)<=0)return $default_arr;
      $i=0;
      foreach($arr_data as $v)
      {
        if(isset($v->MENU_NAME))
        $data_array[$i]["menu_name"]=$v->MENU_NAME;
        else
        $data_array[$i]["menu_name"]="";
        if(isset($v->F_LOCATION))
        $data_array[$i]["location"]=$v->F_LOCATION;
        else $data_array[$i]["location"]="";
        if(isset($v->SAVE_PRIV))
        $data_array[$i]["save"]=$v->SAVE_PRIV;
        else $data_array[$i]["save"]=0;
        if(isset($v->EDIT_PRIV))
        $data_array[$i]["update"]=$v->EDIT_PRIV;
        else $data_array[$i]["update"]=0;
        if(isset($v->DELETE_PRIV))
        $data_array[$i]["delete"]=$v->DELETE_PRIV;
        else $data_array[$i]["delete"]=0;
        if(isset($v->SHOW_PRIV))
        $data_array[$i]["show"]=$v->SHOW_PRIV;
        else  $data_array[$i]["show"]=0;
        if(isset($v->APPROVE_PRIV))
        $data_array[$i]["approve"]=$v->APPROVE_PRIV;
        else $data_array[$i]["approve"]=0;
        $i++;
      }
      return $data_array;;

    }
    public function finish_barcode_data($barcode_no)
    {
       $return_array=array();        
       $scanned_barcode_array = array();
       $barcode_dtlsId_array = array();
       $barcode_rollTableId_array = array();
       $dtls_data_arr = array();
       //$db_type=return_db_type();
      $is_exists = sql_select("SELECT   barcode_no from pro_finish_fabric_rcv_dtls where status_active=1    and barcode_no in($barcode_no)  and is_deleted=0");

      if(count($is_exists)>0)
      {
          $sqls="SELECT  b.ROLL_WIDTH, b.ROLL_WEIGHT, b.ROLL_LENGTH,b.TOTAL_PENALTY_POINT, b.TOTAL_POINT, b.FABRIC_GRADE, b.COMMENTS, b.ROLL_STATUS,b.QC_DATE, a.PROD_ID ,b.id as QC_MST_ID ,a.TRANS_ID ,d.id as MST_ID,a.id as DTLS_ID, a.ORDER_ID as PO_BREAKDOWN_ID ,d.LOCATION_ID as LOCATION,d.KNITTING_LOCATION_ID as SERVICE_LOCATION,d.KNITTING_COMPANY as SERVING_COMPANY, d.SOURCE,d.COMPANY_ID, a.PROD_ID,a.GSM, a.WIDTH,  a.FABRIC_DESCRIPTION_ID,a.BODY_PART_ID,a.RECEIVE_QNTY,a.BATCH_ID,a.BARCODE_NO,b.ROLL_ID, b.ROLL_NO from inv_receive_master d, pro_finish_fabric_rcv_dtls a,pro_qc_result_mst b ,pro_qc_result_dtls c where d.id=a.mst_id   and d.status_active=1 and a.id=b.pro_dtls_id and b.id=c.mst_id and b.status_active=1 and b.entry_form=267 and c.status_active=1 and  a.status_active=1    and a.barcode_no in($barcode_no)  and a.is_deleted=0"; $qc_mst_tble_id=0;
          foreach(sql_select($sqls) as $row)
          { 

            $qc_mst_tble_id=$row->QC_MST_ID; 
            $return_array["index"]['mode'] ="update";
            if(isset($row->TOTAL_PENALTY_POINT))
              $return_array["index"]['total_penalty_point'] =$row->TOTAL_PENALTY_POINT;
            $return_array["index"]['total_penalty_point'] =0;
            if(isset($row->TOTAL_POINT))
              $return_array["index"]['total_point'] =$row->TOTAL_POINT;
            else $return_array["index"]['total_point'] =0;
            if(isset($row->FABRIC_GRADE))
              $return_array["index"]['fabric_grade'] =$row->FABRIC_GRADE;
            else $return_array["index"]['fabric_grade'] ="";
            if(isset($row->COMMENTS))
              $return_array["index"]['comments'] =$row->COMMENTS;
            else $return_array["index"]['comments'] ="";
            if(isset($row->ROLL_STATUS))
              $return_array["index"]['roll_status'] =$row->ROLL_STATUS;
            else   $return_array["index"]['roll_status'] =0;
            if(isset($row->QC_DATE))
              $return_array["index"]['qc_date'] =$row->QC_DATE;
            else 
            $return_array["index"]['qc_date'] ="";
            $return_array["index"]['mst_id'] =$row->MST_ID;
            $return_array["index"]['roll_weight'] =$row->ROLL_WEIGHT;
            $return_array["index"]['roll_length'] =$row->ROLL_LENGTH;
            $return_array["index"]['roll_width'] =$row->ROLL_WIDTH;
            $return_array["index"]['prod_id'] =$row->PROD_ID;
            $return_array["index"]['trans_id'] =$row->TRANS_ID;
            $return_array["index"]['dtls_id'] =$row->DTLS_ID;
            $return_array["index"]['qc_mst_id'] =$row->QC_MST_ID;
            $return_array["index"]['barcode_no'] =$row->BARCODE_NO;
            $return_array["index"]['roll_id'] = $row->ROLL_ID;
            $return_array["index"]['roll_no'] = $row->ROLL_NO;
            $return_array["index"]['batch_no'] = "";
            $return_array["index"]['color'] = "";
            $return_array["index"]['batch_id'] = $row->BATCH_ID; 
            $return_array["index"]['width_dia_id'] = 0; 
            $return_array["index"]['width_dia_val'] = "";
            $return_array["index"]['qc_pass_qty'] = $row->RECEIVE_QNTY;
            $return_array["index"]['prod_qnty'] = $row->RECEIVE_QNTY; 
            $return_array["index"]['body_part'] ="";
            $return_array["index"]['body_part_id'] = $row->BODY_PART_ID;
            $return_array["index"]['prod_id'] = $row->PROD_ID;
            $return_array["index"]['deter_d'] = $row->FABRIC_DESCRIPTION_ID;
            $return_array["index"]['gsm'] = $row->GSM;
            $return_array["index"]['width'] = $row->WIDTH;  
            $return_array["index"]['is_sales'] = 0;  
            $return_array["index"]['construction'] = "";
            $return_array["index"]['company_id'] = $row->COMPANY_ID; 
            $return_array["index"]['source'] = $row->SOURCE; 
            $return_array["index"]['serving_company'] = $row->SERVING_COMPANY; 
            $return_array["index"]['service_location'] = $row->SERVICE_LOCATION; 
            $return_array["index"]['location'] = $row->LOCATION; 
            $return_array["index"]['po_breakdown_id'] = $row->PO_BREAKDOWN_ID;
            $return_array["index"]['po_number'] = "";
            $return_array["index"]['job_number'] = "";
            $return_array["index"]['style_ref_no'] = "";
            $return_array["index"]['qnty'] = $row->RECEIVE_QNTY;
            $return_array["index"]['booking_without_order'] = 0;
            $return_array["index"]['booking_no'] ="";

          }
           if( count(sql_select($sqls))>0)$return_array["index"]["array_ref_data"]= $this->array_ref_data(0,"",2,$qc_mst_tble_id);
           return $return_array;
      }


       $all_extra_cond="";
        
      
       $composition=return_library_array("SELECT id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name","id","composition_name");
        $composition[0]=0;       
       $composition_arr=array();
       $constructtion_arr=array();
       $sql_deter="SELECT a.ID, a.CONSTRUCTION, b.COPMPOSITION_ID, b.PERCENT from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
       $data_array=sql_select($sql_deter);
       foreach( $data_array as $row )
       {
          $constructtion_arr[$row->ID]=$row->CONSTRUCTION;
          if(isset($composition_arr[$row->ID]))
          $composition_arr[$row->ID].=$composition[$row->COPMPOSITION_ID]." ".$row->PERCENT."% ";
          else
          {
            if(isset($composition[$row->COPMPOSITION_ID]))
             $composition_arr[$row->ID] =$composition[$row->COPMPOSITION_ID]." ".$row->PERCENT."% ";
            else $composition_arr[$row->ID] ="";
          }
       }
        //echo "<pre>";
        //print_r($constructtion_arr);
        //echo "</pre>";


       $fabric_typee=array(1=>"Open Width",2=>"Tubular",3=>"Needle Open");
       $body_part=return_library_array("SELECT id,body_part_full_name from  lib_body_part where status_active=1 and is_deleted=0 order by body_part_full_name","id","body_part_full_name");

       $color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

       $roll_split_id = sql_select("SELECT roll_id, barcode_no from pro_roll_details where ROLL_SPLIT_FROM > 0 AND ENTRY_FORM = 62 and barcode_no in($barcode_no) and status_active=1 and is_deleted=0");
       $roll_splt_before_batch_id = "";
       $split_roll_bar_bf_batch_arr = array();
       foreach ($roll_split_id as $row) 
       {
           if(isset($roll_splt_before_batch_id))
           $roll_splt_before_batch_id .= $row->ROLL_ID . ",";
           else $roll_splt_before_batch_id  = $row->ROLL_ID ;
           $split_roll_bar_bf_batch_arr[$row->ROLL_ID] = $row->BARCODE_NO;
       }

       $roll_splt_before_batch_id = chop($roll_splt_before_batch_id, ",");


       $sql_check_barcode_with_booking = sql_select("SELECT  c.BARCODE_NO FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9  and c.entry_form in(64) and c.status_active=1 and c.is_deleted=0 $all_extra_cond and c.barcode_no in($barcode_no)");
       $barcode_batch="";
       foreach ($sql_check_barcode_with_booking as $row)
       {
            $barcode_batch=$row->BARCODE_NO;
       }

       $sql_check_barcode_in_transfter = sql_select("SELECT  c.barcode_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9  and c.entry_form in(180) and c.status_active=1 and c.is_deleted=0 $all_extra_cond and c.barcode_no in($barcode_no)");


       foreach ($sql_check_barcode_in_transfter as $row)
       {
           $barcode_transfer=$row->BARCODE_NO;
       }


        if ($barcode_batch!="") // check latest batch creation for booking
        {
          if ($roll_splt_before_batch_id != "") 
          {

              if ($barcode_transfer!="") // check booking  transfer for booking
              {   
                $sql = "SELECT a.COMPANY_ID,a.KNITTING_SOURCE as SOURCE, a.KNITTING_COMPANY as SERVICE_COMPANY,  a.LOCATION_ID as LOCATION,  a.KNITTING_LOCATION_ID as SERVICE_LOCATION, b.PROD_ID, b.BODY_PART_ID, b.FEBRIC_DESCRIPTION_ID, b.GSM, b.WIDTH, c.BARCODE_NO, c.id as ROLL_ID, c.ROLL_NO, c.PO_BREAKDOWN_ID,c.QNTY,c.IS_SALES, c.roll_id as ROLL_ORIGIN_ID,c.BOOKING_WITHOUT_ORDER,c.BOOKING_NO, 1 as TYPE FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.is_sales and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and b.trans_id>0 and c.status_active=1 and c.is_deleted=0 $all_extra_cond and c.barcode_no in($barcode_no)
                union all
                SELECT a.COMPANY_ID,a.KNITTING_SOURCE as SOURCE, a.KNITTING_COMPANY as SERVICE_COMPANY,  a.LOCATION_ID as LOCATION,  a.KNITTING_LOCATION_ID as SERVICE_LOCATION,  b.PROD_ID, b.BODY_PART_ID, b.FEBRIC_DESCRIPTION_ID, b.GSM, b.WIDTH, c.BARCODE_NO, c.id as ROLL_ID, c.ROLL_NO, c.PO_BREAKDOWN_ID,c.QNTY,c.IS_SALES, c.id as ROLL_ORIGIN_ID,c.BOOKING_WITHOUT_ORDER,c.BOOKING_NO, 2 as TYPE from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) $all_extra_cond and c.status_active=1 and c.is_deleted=0 and c.roll_id=0 and c.id in($roll_splt_before_batch_id)";
              }
              else
              {   
                $sql = "SELECT a.COMPANY_ID,a.KNITTING_SOURCE as SOURCE, a.KNITTING_COMPANY as SERVICE_COMPANY,  a.LOCATION_ID as LOCATION,  a.KNITTING_LOCATION_ID as SERVICE_LOCATION, b.PROD_ID, b.BODY_PART_ID, b.FEBRIC_DESCRIPTION_ID, b.GSM, b.WIDTH, c.BARCODE_NO, c.id as ROLL_ID, c.ROLL_NO, c.PO_BREAKDOWN_ID,c.QNTY,c.IS_SALES,c.ROLL_ID as roll_origin_id,c.booking_without_order,c.booking_no, 1 as type FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and c.entry_form in(64) and c.status_active=1 and c.is_deleted=0 $all_extra_cond and c.barcode_no in($barcode_no) 
                union all 
                SELECT a.COMPANY_ID,a.KNITTING_SOURCE as SOURCE, a.KNITTING_COMPANY as SERVICE_COMPANY,  a.LOCATION_ID as LOCATION,  a.KNITTING_LOCATION_ID as SERVICE_LOCATION, b.PROD_ID, b.BODY_PART_ID, b.FEBRIC_DESCRIPTION_ID,b.GSM, b.WIDTH, c.BARCODE_NO, c.id as ROLL_ID, c.ROLL_NO, c.PO_BREAKDOWN_ID,c.QNTY,c.IS_SALES, c.id as ROLL_ORIGIN_ID,c.BOOKING_WITHOUT_ORDER,c.BOOKING_NO, 2 as TYPE 
                from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
                where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and c.entry_form in(64) and c.status_active=1 and c.is_deleted=0 and c.roll_id=0 $all_extra_cond and c.id in($roll_splt_before_batch_id)";
              }

          }

          else
          {

            $sql = "SELECT a.COMPANY_ID,a.KNITTING_SOURCE as SOURCE, a.KNITTING_COMPANY as SERVICE_COMPANY,  a.LOCATION_ID as LOCATION,  a.KNITTING_LOCATION_ID as SERVICE_LOCATION,  b.PROD_ID, b.BODY_PART_ID, b.FEBRIC_DESCRIPTION_ID, b.GSM, b.WIDTH, c.BARCODE_NO, c.id as ROLL_ID, c.ROLL_NO, c.PO_BREAKDOWN_ID, c.QNTY,c.IS_SALES, c.roll_id as ROLL_ORIGIN_ID,c.BOOKING_WITHOUT_ORDER,c.BOOKING_NO, 1 as TYPE FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9  and c.entry_form in(64)  and c.status_active=1 and c.is_deleted=0 $all_extra_cond and c.barcode_no in($barcode_no)";
          }
        }
        else
        {
          if ($roll_splt_before_batch_id != "")
          {
            $sql = "SELECT a.COMPANY_ID,a.KNITTING_SOURCE as SOURCE, a.KNITTING_COMPANY as SERVICE_COMPANY,  a.LOCATION_ID as LOCATION,  a.KNITTING_LOCATION_ID as SERVICE_LOCATION, b.PROD_ID, b.BODY_PART_ID, b.FEBRIC_DESCRIPTION_ID, b.GSM, b.WIDTH, c.BARCODE_NO, c.id as ROLL_ID, c.ROLL_NO, c.PO_BREAKDOWN_ID,c.QNTY,c.IS_SALES, c.roll_id as ROLL_ORIGIN_ID,c.BOOKING_WITHOUT_ORDER,c.BOOKING_NO, 1 as TYPE FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.is_sales and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and b.trans_id>0 and c.status_active=1 and c.is_deleted=0 $all_extra_cond and c.barcode_no in($barcode_no)
            union all
            SELECT a.COMPANY_ID,a.KNITTING_SOURCE as SOURCE, a.KNITTING_COMPANY as SERVICE_COMPANY,  a.LOCATION_ID as LOCATION,  a.KNITTING_LOCATION_ID as SERVICE_LOCATION, b.PROD_ID, b.BODY_PART_ID, b.FEBRIC_DESCRIPTION_ID, b.GSM, b.WIDTH, c.BARCODE_NO, c.id as ROLL_ID, c.ROLL_NO, c.PO_BREAKDOWN_ID,c.QNTY,c.IS_SALES, c.id as ROLL_ORIGIN_ID,c.BOOKING_WITHOUT_ORDER,c.BOOKING_NO, 2 as TYPE from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_id=0 $all_extra_cond and c.id in($roll_splt_before_batch_id)";
          }
          else
           {
            $sql = "SELECT a.COMPANY_ID,a.KNITTING_SOURCE as SOURCE, a.KNITTING_COMPANY as SERVICE_COMPANY,  a.LOCATION_ID as LOCATION,  a.KNITTING_LOCATION_ID as SERVICE_LOCATION, b.PROD_ID, b.BODY_PART_ID, b.FEBRIC_DESCRIPTION_ID, b.GSM, b.WIDTH, c.BARCODE_NO, c.id as ROLL_ID, c.ROLL_NO, c.PO_BREAKDOWN_ID, c.QNTY,c.IS_SALES, c.roll_id as ROLL_ORIGIN_ID,c.BOOKING_WITHOUT_ORDER,c.BOOKING_NO, 1 as TYPE FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and b.trans_id>0 $all_extra_cond and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_no)";
          }
        }


        //return $sql;
        $data_array = sql_select($sql);
        $poIDs="";$salesIDs="";
        foreach ($data_array as $row) 
        {
          if($row->IS_SALES == 1)
          {
            if(isset($salesIDs))
            $salesIDs.=$row->PO_BREAKDOWN_ID.',';
            else $salesIDs =$row->PO_BREAKDOWN_ID ;
          }
          else
          {
            if(isset($row->PO_BREAKDOWN_ID))
            $poIDs.=$row->PO_BREAKDOWN_ID.',';
            else $poIDs =$row->PO_BREAKDOWN_ID ;
          }
        }



        $poIDs_all=rtrim($poIDs,","); 
        $poIDs_alls=explode(",",$poIDs_all);
        $poIDs_alls=array_chunk($poIDs_alls,999); // chunk for PO ID
        $po_id_cond=" and";
        foreach($poIDs_alls as $dtls_id)
        {
          $ids=implode(',',$dtls_id);
          if(!$ids)$ids=0;
          if($po_id_cond==" and")  $po_id_cond.="(a.id in(".$ids.")"; else $po_id_cond.=" or a.id in(".$ids.")";
        }
        $po_id_cond.=")";
        
        $po_arr = array();
        $po_sql = sql_select("SELECT a.ID,a.PO_NUMBER,b.STYLE_REF_NO,a.JOB_NO_MST from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_id_cond");

         

        foreach($po_sql as $po_row)
        {
          $po_arr[$po_row->ID]['po_number'] = $po_row->PO_NUMBER;
          $po_arr[$po_row->ID]['job_number'] = $po_row->JOB_NO_MST;
          $po_arr[$po_row->ID]['style_ref_no'] = $po_row->STYLE_REF_NO;
        }

        $sales_arr=array();
        $sql_sales=sql_select("SELECT ID,JOB_NO,STYLE_REF_NO from fabric_sales_order_mst where status_active=1 and is_deleted=0");

        foreach ($sql_sales as $sales_row) 
        {
          $sales_arr[$sales_row->ID]["po_number"]      = $sales_row->JOB_NO;
          $sales_arr[$sales_row->ID]["style_ref_no"]     = $sales_row->STYLE_REF_NO;    
        }


        $transPoIds = sql_select("SELECT a.BARCODE_NO, a.PO_BREAKDOWN_ID from pro_roll_details a where a.entry_form=83 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in($barcode_no) and a.re_transfer=0");
        $po_ids_arr=array();
        $transPoIdsArr=array();
        foreach ($transPoIds as $rowP) 
        {
          $po_ids_arr[$rowP->PO_BREAKDOWN_ID] = $rowP->PO_BREAKDOWN_ID;
          $transPoIdsArr[$rowP->BARCODE_NO]['po_breakdown_id'] = $rowP->PO_BREAKDOWN_ID;
          if(isset($po_arr[$rowP->PO_BREAKDOWN_ID]['po_number']))
          {
            $transPoIdsArr[$rowP->BARCODE_NO]['po_number'] = $po_arr[$rowP->PO_BREAKDOWN_ID]['po_number'];
            $transPoIdsArr[$rowP->BARCODE_NO]['job_number'] = $po_arr[$rowP->PO_BREAKDOWN_ID]['job_number'];
            $transPoIdsArr[$rowP->BARCODE_NO]['style_ref_no'] = $po_arr[$rowP->PO_BREAKDOWN_ID]['style_ref_no'];

          }
          
          else
          {
            $transPoIdsArr[$rowP->BARCODE_NO]['po_number'] ="";
            $transPoIdsArr[$rowP->BARCODE_NO]['job_number'] ="";
            $transPoIdsArr[$rowP->BARCODE_NO]['style_ref_no'] ="";
          }   
          if(isset($sales_arr[$rowP->PO_BREAKDOWN_ID]['po_number']))
          {
             $transPoIdsArr[$rowP->BARCODE_NO]['po_number'] = $sales_arr[$rowP->PO_BREAKDOWN_ID]['po_number'];
             $transPoIdsArr[$rowP->BARCODE_NO]['style_ref_no'] = $sales_arr[$rowP->PO_BREAKDOWN_ID]['style_ref_no'];

          }
          else
          {
             $transPoIdsArr[$rowP->BARCODE_NO]['po_number'] = "";
             $transPoIdsArr[$rowP->BARCODE_NO]['style_ref_no'] = "";
          }

         
        }
        $batch_dtls_arr = array();
        $batch_barcode_arr = array();
        $sql = "SELECT a.ID, a.ENTRY_FORM, a.BATCH_NO, a.COLOR_ID, b.BARCODE_NO, b.WIDTH_DIA_TYPE, b.BATCH_QNTY FROM pro_batch_create_mst a, pro_batch_create_dtls b WHERE a.id=b.mst_id and a.entry_form in(0,66) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.barcode_no>0 and b.barcode_no in($barcode_no)";
        $result = sql_select($sql);
        foreach ($result as $row)
        {
          $batch_dtls_arr[$row->BARCODE_NO]['batch_id'] = $row->ID;
          $batch_dtls_arr[$row->BARCODE_NO]['batch_no'] = $row->BATCH_NO;
          $batch_dtls_arr[$row->BARCODE_NO]['color_id'] = $row->COLOR_ID;
          $batch_dtls_arr[$row->BARCODE_NO]['color'] = $color_arr[$row->COLOR_ID];
          $batch_dtls_arr[$row->BARCODE_NO]['entry_form'] = $row->ENTRY_FORM;
          $batch_dtls_arr[$row->BARCODE_NO]['width_dia_type'] = $row->WIDTH_DIA_TYPE;
          $batch_dtls_arr[$row->BARCODE_NO]['batch_qnty'] = $row->BATCH_QNTY;
          $batch_barcode_arr[$row->BARCODE_NO] = $row->BARCODE_NO;
        }

        $compacting_arr = array();
        $compacting_details_arr = array();
        $sql_compact = sql_select("SELECT a.BARCODE_NO,b.PRODUCTION_QTY from pro_roll_details a,pro_fab_subprocess_dtls b where b.roll_id=a.id and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.entry_page=33 and a.barcode_no in($barcode_no)");
        foreach ($sql_compact as $c_id) 
        {
          $compacting_arr[] = $c_id->BARCODE_NO;
          $compacting_details_arr[$c_id->BARCODE_NO]['prod_qty'] = $c_id->PRODUCTION_QTY;
        }
        //return $compacting_details_arr;



        $k=0;
        if(count($data_array)==0)return $return_array;
        foreach ($data_array as $row) 
        {

          if ($row->TYPE == 1) 
          {
            $b_code = $row->BARCODE_NO;
          } 
          else 
          {
            $b_code = $split_roll_bar_bf_batch_arr[$row->ROLL_ORIGIN_ID];
          }
          $production_qty=0;
          if( in_array( $b_code, $compacting_arr )) 
          {
            $production_qty=$compacting_details_arr[$b_code]['prod_qty'];
          }
          else
          {
            if(isset($batch_dtls_arr[$b_code]['batch_qnty']))
            $production_qty=$batch_dtls_arr[$b_code]['batch_qnty'];
          }
          $return_array["index"]['roll_weight'] =0;
          $return_array["index"]['roll_length'] =0;
          $return_array["index"]['roll_width'] =0;
          $return_array["index"]['mode'] = "save";
          $return_array["index"]['prod_id'] =0;
          $return_array["index"]['mst_id'] =0;
          $return_array["index"]['trans_id'] =0;
          $return_array["index"]['dtls_id'] =0;
          $return_array["index"]['qc_mst_id'] =0;
          $return_array["index"]['total_penalty_point'] =0;
          $return_array["index"]['total_point'] =0;
          $return_array["index"]['fabric_grade'] ="";
          $return_array["index"]['comments'] ="";
          $return_array["index"]['roll_status'] =0;
          $return_array["index"]['qc_date'] ="";
          $return_array["index"]['barcode_no'] = $b_code;
          $return_array["index"]['roll_id'] = $row->ROLL_ORIGIN_ID;
          $return_array["index"]['roll_no'] = $row->ROLL_NO;
         
          if(isset($batch_dtls_arr[$b_code]['batch_id']))
          {
            $return_array["index"]['batch_no'] = $batch_dtls_arr[$b_code]['batch_no'];
            $return_array["index"]['color'] = $batch_dtls_arr[$b_code]['color'];
            $return_array["index"]['batch_id'] = $batch_dtls_arr[$b_code]['batch_id'];
            $return_array["index"]['width_dia_id'] = $batch_dtls_arr[$b_code]['width_dia_type']; 
            $return_array["index"]['width_dia_val'] = $fabric_typee[$batch_dtls_arr[$b_code]['width_dia_type']];
            $return_array["index"]['qc_pass_qty'] = $batch_dtls_arr[$b_code]['batch_qnty'];

          }
          else
          {
            $return_array["index"]['batch_no'] ="";
            $return_array["index"]['color'] ="";
            $return_array["index"]['batch_id'] =0;
            $return_array["index"]['width_dia_id'] =0; 
            $return_array["index"]['width_dia_val'] =""; 
            $return_array["index"]['qc_pass_qty'] =0;
          }
           
          $return_array["index"]['prod_qnty'] = $production_qty; 
          if(isset($body_part[$row->BODY_PART_ID]))
          $return_array["index"]['body_part'] = $body_part[$row->BODY_PART_ID];
          else $return_array["index"]['body_part'] = "";
          $return_array["index"]['body_part_id'] = $row->BODY_PART_ID;
          $return_array["index"]['prod_id'] = $row->PROD_ID;
          $return_array["index"]['deter_d'] = $row->FEBRIC_DESCRIPTION_ID;
          $return_array["index"]['gsm'] = $row->GSM;
          $return_array["index"]['width'] = $row->WIDTH;          
          $cons_comp=$constructtion_arr[$row->FEBRIC_DESCRIPTION_ID].", ".$composition_arr[$row->FEBRIC_DESCRIPTION_ID];
          $return_array["index"]['is_sales'] = $row->IS_SALES;  
          $return_array["index"]['construction'] = $cons_comp;

          if(isset($row->COMPANY_ID))       
          $return_array["index"]['company_id'] = $row->COMPANY_ID; 
          else $return_array["index"]['company_id'] =0;  


          if(isset($row->SOURCE))       
          $return_array["index"]['source'] = $row->SOURCE; 
          else $return_array["index"]['source'] =0;    


          if(isset($row->SERVING_COMPANY))       
          $return_array["index"]['serving_company'] = $row->SERVING_COMPANY; 
          else $return_array["index"]['serving_company'] =0;    


          if(isset($row->SERVICE_LOCATION))       
          $return_array["index"]['service_location'] = $row->SERVICE_LOCATION; 
          else $return_array["index"]['service_location'] =0;    


          if(isset($row->LOCATION))       
          $return_array["index"]['location'] = $row->LOCATION; 
          else $return_array["index"]['location'] =0;    


          


          if (!isset($transPoIdsArr[$b_code])) 
          {
            $return_array["index"]['po_breakdown_id'] = $row->PO_BREAKDOWN_ID;
            $return_array["index"]['po_number'] = $po_arr[$row->PO_BREAKDOWN_ID]['po_number'];
            $return_array["index"]['job_number'] = $po_arr[$row->PO_BREAKDOWN_ID]['job_number'];
            $return_array["index"]['style_ref_no'] = $po_arr[$row->PO_BREAKDOWN_ID]['style_ref_no'];
          } 
          else
          {
            $return_array["index"]['po_breakdown_id'] = $transPoIdsArr[$b_code]['po_breakdown_id'];
            if(isset($po_arr[$transPoIdsArr[$b_code]['po_breakdown_id']]['po_number']))
            {
              $return_array["index"]['po_number'] = $po_arr[$transPoIdsArr[$b_code]['po_breakdown_id']]['po_number'];
              $return_array["index"]['job_number'] = $po_arr[$transPoIdsArr[$b_code]['po_breakdown_id']]['job_number'];

              $return_array["index"]['style_ref_no'] = $po_arr[$transPoIdsArr[$b_code]['po_breakdown_id']]['style_ref_no'];

            }
            else
            {
              $return_array["index"]['po_number'] = "";
              $return_array["index"]['job_number'] = "";
              $return_array["index"]['style_ref_no'] = "";
            }
            if(isset($sales_arr[$row->PO_BREAKDOWN_ID]['po_number']))
            {
              $return_array["index"]['po_number']  = $sales_arr[$row->PO_BREAKDOWN_ID]['po_number'];
              $return_array["index"]['style_ref_no'] = $sales_arr[$row->PO_BREAKDOWN_ID]['style_ref_no'];
              $return_array["index"]['job_number'] = "";

            }
            else
            {
              $return_array["index"]['po_number']  ="";
              $return_array["index"]['style_ref_no'] = "";
              $return_array["index"]['job_number'] = "";
            }

            
          }

          $return_array["index"]['qnty'] = number_format($row->QNTY, 2, '.', '');
          $return_array["index"]['booking_without_order'] = $row->BOOKING_WITHOUT_ORDER;
          if(isset($row->BOOKING_NO))
          $return_array["index"]['booking_no'] = $row->BOOKING_NO;
          else $return_array["index"]['booking_no'] ="";
          $barcode_array[$b_code] = $b_code;
          $k++;
        }
        $return_array["index"]["array_ref_data"]= $this->array_ref_data(0,"",2,0);
        //$return_array["index"]["machine_data"]=  $this->machine_data();
        return $return_array;
  
    }


    public function barcode_details_data($bar_code,$type=0)
    {
        $data_array=array();       
        $composition[0]=0;
        $composition=return_library_array("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name","id","composition_name");
        $supplier_arr = return_library_array("select id, short_name from  lib_supplier", "id", "short_name");

        $bar_code=trim($bar_code) ;
        $sqls="";
        if($type==2)
        {
           return $sqls="SELECT c.REJECT_QNTY,a.COMPANY_ID,a.BUYER_ID,a.ROLL_MAINTAINED, c.BARCODE_NO,c.MST_ID,c.DTLS_ID ,  b.PROD_ID, b.BODY_PART_ID, b.FABRIC_DESCRIPTION_ID as FEBRIC_DESCRIPTION_ID, b.MACHINE_NO_ID, b.GSM, b.WIDTH, b.COLOR_ID,0 as YARN_PROD_ID, 0 as YARN_PROD_ID,0 as YARN_LOT, 0 as YARN_COUNT , c.id as ROLL_ID, c.ROLL_NO,c.QC_PASS_QNTY_PCS as QNTY
              FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c 
              WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form =66 and c.entry_form =66 and c.status_active=1 and c.is_deleted=0 and c.barcode_no='$bar_code'";
         

        }
        else
        {
            $sqls = "SELECT  c.REJECT_QNTY,a.COMPANY_ID,a.BUYER_ID,a.ROLL_MAINTAINED, c.BARCODE_NO,c.MST_ID,c.DTLS_ID ,  b.PROD_ID, b.BODY_PART_ID, b.FEBRIC_DESCRIPTION_ID, b.MACHINE_NO_ID, b.GSM, b.WIDTH, b.COLOR_ID,b.YARN_PROD_ID, b.YARN_PROD_ID,b.YARN_LOT, b.YARN_COUNT , c.id as ROLL_ID, c.ROLL_NO, c.QNTY
              FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
              WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and c.status_active=1 and c.is_deleted=0 and c.barcode_no='$bar_code'";

        }
         $data_sql = sql_select($sqls);
        
        if(count($data_sql)<=0)return $data_array;
        $all_color=str_replace("'", "", $data_sql[0]->COLOR_ID);
        $buyerId=str_replace("'", "", $data_sql[0]->BUYER_ID);
        $compId=str_replace("'", "", $data_sql[0]->COMPANY_ID);

        if(!$all_color)$all_color=0;

        $color_sql="SELECT ID,COLOR_NAME from lib_color where id in($all_color)";        
        $color_arr=array();
        $color_arr[0]=0;

        $machine_sql="SELECT ID,DIA_WIDTH from lib_machine_name  ";        
        $machine_arr=array();
        $machine_arr[0]=0;
        foreach(sql_select($machine_sql) as $vals)
        {
          $machine_arr[$vals->ID]=$vals->DIA_WIDTH;
        }

        $color_names="";
        foreach(sql_select($color_sql) as $val)
        {
          if($color_names)$color_names.=",".$val->COLOR_NAME;
          else $color_names=$val->COLOR_NAME;
        }

        $sql_deter = "SELECT a.ID, a.CONSTRUCTION, b.COPMPOSITION_ID, b.PERCENT from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
        $data = sql_select($sql_deter);
        foreach ($data as $row) 
        {
          $constructtion_arr[$row->ID] = $row->CONSTRUCTION;
          if(isset($composition_arr[$row->ID]))
          {
            if(isset($composition[$row->COPMPOSITION_ID]))
              $composition_arr[$row->ID] .= $composition[$row->COPMPOSITION_ID] . " " . $row->PERCENT . "% ";
          }

          else
          {
            if(isset($composition[$row->COPMPOSITION_ID]))
              $composition_arr[$row->ID]  = $composition[$row->COPMPOSITION_ID] . " " . $row->PERCENT . "% ";
          }

        }

       $barcode_chk=return_field_value("barcode_no", "pro_qc_result_mst", "BARCODE_NO='$bar_code' and status_active=1 and is_deleted=0 ", "barcode_no") ;


       if(trim($barcode_chk))
       {

         $sql="SELECT  ROLL_STATUS, ID,QC_NAME, ROLL_WIDTH, ROLL_WEIGHT,  ROLL_LENGTH, REJECT_QNTY, QC_DATE, TOTAL_PENALTY_POINT, TOTAL_POINT, FABRIC_GRADE, COMMENTS FROM pro_qc_result_mst where status_active=1 and is_deleted=0 and barcode_no='$bar_code'" ;
          $data_array["index"]["MODE"]="update";
          foreach($data_sql as $rows)
          {

            $yarn_count_arr = array_unique(explode(",", $rows->YARN_COUNT  ));
            $all_yarn_count = "";
            foreach ($yarn_count_arr as $count_id) 
            {
              $all_yarn_count .= return_field_value("yarn_count", "lib_yarn_count", "id='$count_id'", "yarn_count") . ",";
            }
            $all_yarn_count = chop($all_yarn_count, ",");

            $data_array["index"]["MST_ID"]=trim($rows->MST_ID);
            $data_array["index"]["COMPANY_ID"]=$compId;
            $data_array["index"]["BUYER_ID"]=trim($buyerId);
            $data_array["index"]["DTLS_ID"]=trim($rows->DTLS_ID);
            if(isset($rows->ROLL_MAINTAINED))
            $data_array["index"]["ROLL_MAINTAINED"]=trim($rows->ROLL_MAINTAINED);
            else $data_array["index"]["ROLL_MAINTAINED"]=0;
            $data_array["index"]["BARCODE_NO"]=trim($rows->BARCODE_NO);
            if(isset($rows->ROLL_ID))
            $data_array["index"]["ROLL_ID"]=trim($rows->ROLL_ID);
            else $data_array["index"]["ROLL_ID"]=0;
            if(isset($rows->ROLL_NO))
            $data_array["index"]["ROLL_NO"]=trim($rows->ROLL_NO);
            else $data_array["index"]["ROLL_NO"]=0;
            if(isset($rows->GSM))
            $data_array["index"]["GSM"]=trim($rows->GSM);
            else $data_array["index"]["GSM"]=0;
            if(isset($rows->WIDTH))
            $data_array["index"]["DIA"]=trim($rows->WIDTH);  
            else  $data_array["index"]["DIA"]="";
            if(isset($machine_arr[$rows->MACHINE_NO_ID]))    
            $data_array["index"]["MC_DIA"]=trim($machine_arr[$rows->MACHINE_NO_ID]);
            else 
            $data_array["index"]["MC_DIA"]="";

            $composition_st="";
            if(isset($constructtion_arr[$rows->FEBRIC_DESCRIPTION_ID]))
              $composition_st.=$constructtion_arr[$rows->FEBRIC_DESCRIPTION_ID];
            if(isset($composition_arr[$rows->FEBRIC_DESCRIPTION_ID]))
              $composition_st.=' '.$composition_arr[$rows->FEBRIC_DESCRIPTION_ID];

            $yarn_prod_arr = array_filter(array_unique(explode(",", $rows->YARN_PROD_ID)));
            $all_supplier="";

            if(!empty($yarn_prod_arr))
            {
              $yarn_prod_sql = sql_select("select SUPPLIER_ID from product_details_master where item_category_id =1 and id in (". implode(",",$yarn_prod_arr).")");
              foreach ($yarn_prod_sql as $row) 
              {
                if($all_supplier) $all_supplier .=  ",". $supplier_arr[$row->SUPPLIER_ID] ;
                else
                 $all_supplier  = $supplier_arr[$row->SUPPLIER_ID] ;
             }
           }
           $all_supplier = implode(",",array_unique(explode(",",chop($all_supplier, ','))));



           $data_array["index"]["COLOR"]=trim($color_names);
           $data_array["index"]["CONSTRUCTION"]=trim($composition_st);          
           $data_array["index"]["YARN_COUNT"]=trim($all_yarn_count) ;
           $data_array["index"]["YARN_LOT"]=trim($rows->YARN_LOT);
           $data_array["index"]["SPINNING_MILL"]= trim($all_supplier);
           //$data_array["index"]["array_ref_data"]= $this->array_ref_data($buyerId);
         }
          foreach(sql_select($sql) as $v)
          {
            if(isset($v->QC_NAME))
            $data_array["index"]["QC_NAME"]=$v->QC_NAME;
            else
              $data_array["index"]["QC_NAME"]="";
            if(isset($v->ROLL_STATUS))
            $data_array["index"]["ROLL_STATUS"]=$v->ROLL_STATUS;
            else $data_array["index"]["ROLL_STATUS"]=0;
            $data_array["index"]["UPDATE_ID"]=$v->ID;
            $data_array["index"]["ROLL_KG"]=$v->ROLL_WEIGHT;
            $data_array["index"]["ROLL_INCH"]=$v->ROLL_WIDTH;
            $data_array["index"]["ROLL_YDS"]=$v->ROLL_LENGTH;
            if(isset($v->REJECT_QNTY))
            $data_array["index"]["REJECT_QNTY"]=$v->REJECT_QNTY;
            else
              $data_array["index"]["REJECT_QNTY"]=0;

            $data_array["index"]["TOTAL_PENALTY_POINT"]=$v->TOTAL_PENALTY_POINT;
            $data_array["index"]["TOTAL_POINT"]=$v->TOTAL_POINT;
            if(isset($v->FABRIC_GRADE))
            $data_array["index"]["FABRIC_GRADE"]=$v->FABRIC_GRADE;
            else
              $data_array["index"]["FABRIC_GRADE"]="";

            if(isset($v->COMMENTS))
            $data_array["index"]["COMMENTS"]=$v->COMMENTS;
            else
              $data_array["index"]["COMMENTS"]="";
            if($v->QC_DATE)
            $data_array["index"]["QC_DATE"]=date("d-m-Y",strtotime($v->QC_DATE));
            else $data_array["index"]["QC_DATE"]="";
           

            $mst_id=$v->ID;
            
          }
          $dtls_sql="SELECT  DEFECT_NAME,DEFECT_COUNT,FOUND_IN_INCH, PENALTY_POINT FROM pro_qc_result_dtls Where MST_ID = '$mst_id' ";
          $dtls_array=array();
          $defect_wise_val=array();
          foreach(sql_select($dtls_sql) as $vals)
          {
              //$qcDtlsObj =new QcDtls($vals->DEFECT_NAME,$vals->DEFECT_COUNT,$vals->FOUND_IN_INCH,$vals->PENALTY_POINT);
              //$dtls_array[]= $qcDtlsObj ;
              $defect_wise_val[$vals->DEFECT_NAME]["DEFECT_COUNT"]=$vals->DEFECT_COUNT;
              $defect_wise_val[$vals->DEFECT_NAME]["FOUND_IN_INCH"]=$vals->FOUND_IN_INCH;
              $defect_wise_val[$vals->DEFECT_NAME]["PENALTY_POINT"]=$vals->PENALTY_POINT;
          }
           //$data_array["index"]["dtls_obj"]=$dtls_array;
          $data_array["index"]["array_ref_data"]= $this->array_ref_data($compId,$defect_wise_val,1,0);

           return $data_array;

       }
       else
       {
         
        $i=0;
        foreach($data_sql as $rows)
        {

          $yarn_count_arr = array_unique(explode(",", $rows->YARN_COUNT  ));
          $all_yarn_count = "";
          foreach ($yarn_count_arr as $count_id) {
            $all_yarn_count .= return_field_value("yarn_count", "lib_yarn_count", "id='$count_id'", "yarn_count") . ",";
          }
          $all_yarn_count = chop($all_yarn_count, ",");
          $data_array["index"]["MODE"]="save";
          $data_array["index"]["MST_ID"]=trim($rows->MST_ID);
          $data_array["index"]["COMPANY_ID"]=$compId;
          $data_array["index"]["BUYER_ID"]=trim($buyerId);
          $data_array["index"]["DTLS_ID"]=trim($rows->DTLS_ID);
          if(isset($rows->ROLL_MAINTAINED))
          $data_array["index"]["ROLL_MAINTAINED"]=trim($rows->ROLL_MAINTAINED);
          else  $data_array["index"]["ROLL_MAINTAINED"]=0;
          $data_array["index"]["BARCODE_NO"]=trim($rows->BARCODE_NO);
          if(isset($rows->ROLL_ID))
          $data_array["index"]["ROLL_ID"]=trim($rows->ROLL_ID);
          else $data_array["index"]["ROLL_ID"]=0;
          if(isset($rows->ROLL_NO))
          $data_array["index"]["ROLL_NO"]=trim($rows->ROLL_NO);
          else $data_array["index"]["ROLL_NO"]=0;
          if(isset($rows->GSM))
          $data_array["index"]["GSM"]=trim($rows->GSM);
          else $data_array["index"]["GSM"]=0;
          if(isset($rows->WIDTH))
          $data_array["index"]["DIA"]=trim($rows->WIDTH); 
          else $data_array["index"]["DIA"]=""; 
          if(isset($machine_arr[$rows->MACHINE_NO_ID]))     
          $data_array["index"]["MC_DIA"]=trim($machine_arr[$rows->MACHINE_NO_ID]);
          else
            $data_array["index"]["MC_DIA"]="";
          $data_array["index"]["UPDATE_ID"]=0;
          if(isset($rows->REJECT_QNTY))
          $data_array["index"]["REJECT_QNTY"]=$rows->REJECT_QNTY;
          else $data_array["index"]["REJECT_QNTY"]=0;
          $data_array["index"]["ROLL_STATUS"]=0;
          $data_array["index"]["TOTAL_PENALTY_POINT"]=0;
          $data_array["index"]["TOTAL_POINT"]=0;
          $data_array["index"]["FABRIC_GRADE"]="";
          $data_array["index"]["COMMENTS"]="";
          $data_array["index"]["QC_DATE"]="";
           $data_array["index"]["QC_NAME"]="";

          $composition_st="";
          if(isset($constructtion_arr[$rows->FEBRIC_DESCRIPTION_ID]))
            $composition_st.=$constructtion_arr[$rows->FEBRIC_DESCRIPTION_ID];
          if(isset($composition_arr[$rows->FEBRIC_DESCRIPTION_ID]))
            $composition_st.=' '.$composition_arr[$rows->FEBRIC_DESCRIPTION_ID];

          $yarn_prod_arr = array_filter(array_unique(explode(",", $rows->YARN_PROD_ID)));
          $all_supplier="";

          if(!empty($yarn_prod_arr))
          {
            $yarn_prod_sql = sql_select("select SUPPLIER_ID from product_details_master where item_category_id =1 and id in (". implode(",",$yarn_prod_arr).")");
            foreach ($yarn_prod_sql as $row) 
            {
              if($all_supplier) $all_supplier .=  ",". $supplier_arr[$row->SUPPLIER_ID] ;
              else
               $all_supplier  = $supplier_arr[$row->SUPPLIER_ID] ;
           }
         }
         $all_supplier = implode(",",array_unique(explode(",",chop($all_supplier, ','))));



         $data_array["index"]["COLOR"]=trim($color_names);
         $data_array["index"]["CONSTRUCTION"]=trim($composition_st);
         $data_array["index"]["ROLL_KG"]=trim($rows->QNTY);
         $data_array["index"]["ROLL_INCH"]=0;
         $data_array["index"]["ROLL_YDS"]=0;
         $data_array["index"]["YARN_COUNT"]=trim($all_yarn_count) ;
         $data_array["index"]["YARN_LOT"]=trim($rows->YARN_LOT);
         $data_array["index"]["SPINNING_MILL"]= trim($all_supplier);
         $data_array["index"]["array_ref_data"]= $this->array_ref_data($compId,"",1,0);
         $i++;
         

       }
       return $data_array;

       }

      


    }


    public function barcode_report_data($bar_code) 
    {
        $data_array=array();       
        $composition[0]=0;
        $roll_status_arr = array(1 => 'QC Pass', 2 => 'Held Up', 3 => 'Reject');

        $composition=return_library_array("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name","id","composition_name");
        $supplier_arr = return_library_array("select id, short_name from  lib_supplier", "id", "short_name");
        $lib_buyer = return_library_array("select id, buyer_name from  lib_buyer", "id", "buyer_name");
        $lib_brand = return_library_array("select id, brand_name from  lib_brand", "id", "brand_name");
        $color_arr = return_library_array("select id, color_name from  lib_color", "id", "color_name");

        $bar_code=trim($bar_code) ;
       $data_sql = sql_select("SELECT a.RECV_NUMBER,a.RECEIVE_DATE, e.JOB_NO,e.STYLE_REF_NO, a.COMPANY_ID,a.BUYER_ID,a.ROLL_MAINTAINED, c.BARCODE_NO,c.MST_ID,c.DTLS_ID ,  b.PROD_ID, b.BODY_PART_ID, b.FEBRIC_DESCRIPTION_ID, b.MACHINE_NO_ID, b.GSM, b.WIDTH, b.COLOR_ID,b.YARN_PROD_ID, b.YARN_PROD_ID,b.YARN_LOT,b.BRAND_ID, b.YARN_COUNT , c.id as ROLL_ID, c.ROLL_NO, c.QNTY
          FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c ,wo_po_break_down d ,wo_po_details_master e 
          WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and c.status_active=1 and c.is_deleted=0 and d.id=c.po_breakdown_id  and e.job_no=d.job_no_mst and 
          e.status_active=1 and d.is_deleted=0 and c.barcode_no='$bar_code' group by  a.RECV_NUMBER,a.RECEIVE_DATE,e.JOB_NO,e.STYLE_REF_NO, a.COMPANY_ID,a.BUYER_ID,a.ROLL_MAINTAINED, c.BARCODE_NO,c.MST_ID,c.DTLS_ID ,  b.PROD_ID, b.BODY_PART_ID, b.FEBRIC_DESCRIPTION_ID, b.MACHINE_NO_ID, b.GSM, b.WIDTH, b.COLOR_ID,b.YARN_PROD_ID, b.YARN_PROD_ID,b.YARN_LOT, b.BRAND_ID,b.YARN_COUNT , c.id , c.ROLL_NO, c.QNTY ");
       
       if(count($data_sql)<=0)return $data_array;
       if(isset($lib_buyer[$data_sql[0]->BUYER_ID]))
       $data_array["BASIC_INFO"]["BUYER"]=$lib_buyer[$data_sql[0]->BUYER_ID];
       else  $data_array["BASIC_INFO"]["BUYER"]="";
       $data_array["BASIC_INFO"]["JOB"]=$data_sql[0]->JOB_NO;
       $data_array["BASIC_INFO"]["STYLE"]=$data_sql[0]->STYLE_REF_NO;
       //return $data_array;
        if(count($data_sql)<=0)return $data_array;
        $all_color=str_replace("'", "", $data_sql[0]->COLOR_ID);
        $buyerId=str_replace("'", "", $data_sql[0]->BUYER_ID);
        $compId=str_replace("'", "", $data_sql[0]->COMPANY_ID);

        if(!$all_color)$all_color=0;

        $color_sql="SELECT ID,COLOR_NAME from lib_color where id in($all_color)";        
         

        $machine_sql="SELECT ID,DIA_WIDTH from lib_machine_name  ";        
        $machine_arr=array();
        $machine_arr[0]=0;
        foreach(sql_select($machine_sql) as $vals)
        {
          $machine_arr[$vals->ID]=$vals->DIA_WIDTH;
        }

        $color_names="";
        foreach(sql_select($color_sql) as $val)
        {
          if($color_names)$color_names.=",".$val->COLOR_NAME;
          else $color_names=$val->COLOR_NAME;
        }

        $sql_deter = "SELECT a.ID, a.CONSTRUCTION, b.COPMPOSITION_ID, b.PERCENT from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
        $data = sql_select($sql_deter);
        foreach ($data as $row) 
        {
          $constructtion_arr[$row->ID] = $row->CONSTRUCTION;
          if(isset($composition_arr[$row->ID]))
          {
            if(isset($composition[$row->COPMPOSITION_ID]))
              $composition_arr[$row->ID] .= $composition[$row->COPMPOSITION_ID] . " " . $row->PERCENT . "% ";
          }

          else
          {
            if(isset($composition[$row->COPMPOSITION_ID]))
              $composition_arr[$row->ID]  = $composition[$row->COPMPOSITION_ID] . " " . $row->PERCENT . "% ";
          }

        }
        
         
        foreach($data_sql as $rows)
        {

          $yarn_count_arr = array_unique(explode(",", $rows->YARN_COUNT  ));
          $all_yarn_count = "";
          foreach ($yarn_count_arr as $count_id) 
          {
            $all_yarn_count .= return_field_value("yarn_count", "lib_yarn_count", "id='$count_id'", "yarn_count") . ",";
          }
          $all_yarn_count = chop($all_yarn_count, ",");


          $composition_st="";
          if(isset($constructtion_arr[$rows->FEBRIC_DESCRIPTION_ID]))
            $composition_st.=$constructtion_arr[$rows->FEBRIC_DESCRIPTION_ID];
          if(isset($composition_arr[$rows->FEBRIC_DESCRIPTION_ID]))
            $composition_st.=' '.$composition_arr[$rows->FEBRIC_DESCRIPTION_ID];

          $yarn_prod_arr = array_filter(array_unique(explode(",", $rows->YARN_PROD_ID)));
          $all_supplier="";

          if(!empty($yarn_prod_arr))
          {
            $yarn_prod_sql = sql_select("select SUPPLIER_ID from product_details_master where item_category_id =1 and id in (". implode(",",$yarn_prod_arr).")");
              foreach ($yarn_prod_sql as $row) 
              {
                if($all_supplier) $all_supplier .=  ",". $supplier_arr[$row->SUPPLIER_ID] ;
                else   $all_supplier  = $supplier_arr[$row->SUPPLIER_ID] ;
              }
          }

         $all_supplier = implode(",",array_unique(explode(",",chop($all_supplier, ','))));
         $data_array["KNITTING_INFO"]["PRODUCTION_ID"]=$rows->RECV_NUMBER;
         $data_array["KNITTING_INFO"]["DATE"]=$rows->RECEIVE_DATE;
         $data_array["YARN_INFO"]["DESCRIPTION"]=trim($composition_st);          
         $data_array["YARN_INFO"]["YARN_COUNT"]=trim($all_yarn_count) ;
         $data_array["YARN_INFO"]["LOT"]=trim($rows->YARN_LOT);
         if(isset($lib_brand[$rows->BRAND_ID]))
           $data_array["YARN_INFO"]["BRAND"]= $lib_brand[$rows->BRAND_ID];
         else  $data_array["YARN_INFO"]["BRAND"]="";

           //$data_array["index"]["array_ref_data"]= $this->array_ref_data($buyerId);
       }
          $qc_mst_sql=sql_select("SELECT  ROLL_STATUS, ID,QC_NAME, ROLL_WIDTH, ROLL_WEIGHT,  ROLL_LENGTH, REJECT_QNTY, QC_DATE, TOTAL_PENALTY_POINT, TOTAL_POINT, FABRIC_GRADE, COMMENTS FROM pro_qc_result_mst where status_active=1 and is_deleted=0 and barcode_no='$bar_code'");
          if(count($qc_mst_sql)<=0)
          {
            $data_array["QA_INFO"]["QC_NAME"]="";
            $data_array["QA_INFO"]["QC_STATUS"]=0;
            $data_array["QA_INFO"]["QC_DATE"]="";
            $data_array["QA_INFO"]["ROLL_WEIGHT"]=0;
            $data_array["QA_INFO"]["FABRIC_GRADE"]="";
             $data_array["QA_INFO"]["TOTAL_PENALTY_POINT"]=0;
             $data_array["QA_INFO"]["TOTAL_POINT"]=0;
          }
         foreach ($qc_mst_sql as $v)
          {

            if(isset($v->QC_NAME))
            $data_array["QA_INFO"]["QC_NAME"]=$v->QC_NAME;
            else $data_array["QA_INFO"]["QC_NAME"]="";

            if(isset($roll_status_arr[$v->ROLL_STATUS]))
            $data_array["QA_INFO"]["QC_STATUS"]=$roll_status_arr[$v->ROLL_STATUS];
            else $data_array["QA_INFO"]["QC_STATUS"]=0;

            if($v->QC_DATE)
            $data_array["QA_INFO"]["QC_DATE"]=date("d-m-Y",strtotime($v->QC_DATE));
            else $data_array["QA_INFO"]["QC_DATE"]="";
 
            $data_array["QA_INFO"]["ROLL_WEIGHT"]=$v->ROLL_WEIGHT;

            if(isset($v->FABRIC_GRADE))
            $data_array["QA_INFO"]["FABRIC_GRADE"]=$v->FABRIC_GRADE;
            else
              $data_array["QA_INFO"]["FABRIC_GRADE"]="";

            //$data_array["QA_INFO"]["ROLL_INCH"]=$v->ROLL_WIDTH;
            //$data_array["QA_INFO"]["ROLL_YDS"]=$v->ROLL_LENGTH;
            /*if(isset($v->REJECT_QNTY))
            $data_array["QA_INFO"]["REJECT_QNTY"]=$v->REJECT_QNTY;
            else
              $data_array["QA_INFO"]["REJECT_QNTY"]=0;*/

            $data_array["QA_INFO"]["TOTAL_PENALTY_POINT"]=$v->TOTAL_PENALTY_POINT;
            $data_array["QA_INFO"]["TOTAL_POINT"]=$v->TOTAL_POINT;
            

            /*if(isset($v->COMMENTS))
            $data_array["QA_INFO"]["COMMENTS"]=$v->COMMENTS;
            else
              $data_array["QA_INFO"]["COMMENTS"]="";*/ 
            
          }


          $batch_info=sql_select("SELECT a.COLOR_ID, a.BATCH_NO,a.BATCH_DATE   FROM pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and  a.status_active=1 and b.is_deleted=0 and b.barcode_no='$bar_code'");
          if(count($batch_info)<=0)
          {
            $data_array["BATCH_INFO"]["BATCH_NO"]="";
            $data_array["BATCH_INFO"]["BATCH_DATE"]=""; 
            $data_array["BATCH_INFO"]["COLOR_ID"]=""; 
          }
         foreach ($batch_info as $v)
          {

            if(isset($v->BATCH_NO))
            $data_array["BATCH_INFO"]["BATCH_NO"]=$v->BATCH_NO;
            else $data_array["BATCH_INFO"]["BATCH_NO"]="";

            if(isset($v->BATCH_DATE))
            $data_array["BATCH_INFO"]["BATCH_DATE"]= $v->BATCH_DATE;
            else $data_array["BATCH_INFO"]["BATCH_DATE"]=""; 


            if(isset($color_arr[$v->COLOR_ID]))
            $data_array["BATCH_INFO"]["COLOR_ID"]= $color_arr[$v->COLOR_ID];
            else $data_array["BATCH_INFO"]["COLOR_ID"]=""; 
            
          }
         return $data_array;
    }
    function tabwise_sewingline_data($mac="")
    {
      $data_array=array();
      $sqls=sql_select("SELECT  ID, COMPANY_ID, LOCATION_ID, FLOOR_ID, SEWING_LINE, MAC FROM tabwise_sewing_line Where MAC = '$mac' ORDER BY ID desc");
      $i=0;
      foreach($sqls as $v)
      {
        if($i==1)break;
         $data_array[$i]["company_id"]=$v->COMPANY_ID;
         $data_array[$i]["location_id"]=$v->LOCATION_ID;
         $data_array[$i]["floor_id"]=$v->FLOOR_ID;
         $data_array[$i]["sewing_line"]=$v->SEWING_LINE;
         $data_array[$i]["mac"]=$v->MAC;
          
         $i++;
      }
      return $data_array;
    }

    function create_tracking($save_obj)
    {


      $response_obj = json_decode($save_obj);  
      $mst_arr=array();
       
      $db_type=return_db_type();

      $mst_tbl="TRACKING_INFO";
      if($db_type==0)
      {
        $mst_tbl=strtolower($mst_tbl);

      }




      if($response_obj->status == true)
      {

        $this->db->trans_start(); 
        $phone=$response_obj->phone ;
        $latitude=$response_obj->latitude ;
        $longitude=$response_obj->longitude ;

        $pc_date_time = date("d-M-Y h:i:s A",time());
        if($db_type==0)    $pc_date_time = date("Y-m-d H:i:s",time()); 
        $id=return_next_id("id", $mst_tbl, "", "",$db_type);

        if($response_obj->mode== "save")
        {  

          $mst_arr = array(
            'phone'   => $phone,
            'latitude'  => $latitude,
            'longitude'  => $longitude,
            'insert_date'        => $pc_date_time 
            );
          $mst_arr['id'] = $id; 
          $this->insertData($mst_arr, $mst_tbl);

        }
        
        $this->db->trans_complete();
        if ($this->db->trans_status() == TRUE) 
        {
          return $resultset["status"] = "Successful";
        } 
        else
        {
          $resultset["status"] = "Failed";
        }
      }
      else
      {
        return $resultset["status"] = "Failed";
      }
    }

    function create_tabwise_line($save_obj)
    {


      $response_obj = json_decode($save_obj);  
      $mst_arr=array();
       
      $db_type=return_db_type();

      $mst_tbl="TABWISE_SEWING_LINE";
      if($db_type==0)
      {
        $mst_tbl=strtolower($mst_tbl);

      }




      if($response_obj->status == true)
      {

        $this->db->trans_start(); 
        $company_id=$response_obj->company_id ;
        $location_id=$response_obj->location_id ;
        $floor_id=$response_obj->floor_id ;
        $sewing_line=$response_obj->sewing_line ;
        $mac=$response_obj->mac ;
        

        $pc_date_time = date("d-M-Y h:i:s A",time());
        if($db_type==0)    $pc_date_time = date("Y-m-d H:i:s",time()); 
        $id=return_next_id("id", $mst_tbl, "", "",$db_type);

        if($response_obj->mode== "save")
        {  

          $mst_arr = array(
            'COMPANY_ID'   => $company_id,
            'LOCATION_ID'  => $location_id,
            'FLOOR_ID'  => $floor_id,
            'SEWING_LINE'  => $sewing_line,
            'MAC'  => $mac,
            'INSERT_DATE'        => $pc_date_time 
            );
          $mst_arr['ID'] = $id; 
          $this->insertData($mst_arr, $mst_tbl);

        }
        
        $this->db->trans_complete();
        if ($this->db->trans_status() == TRUE) 
        {
          return $resultset["status"] = "Successful";
        } 
        else
        {
          $resultset["status"] = "Failed";
        }
      }
      else
      {
        return $resultset["status"] = "Failed";
      }
    }





      


    function create_qc_result($save_obj)
    {

      //$save_obj='{"status":true,"mode":"save","UPDATE_ID":0,"data":{"index":{"BARCODE_NO":19023496688,"BUYER_ID":4,"COMPANY_ID":1,"DTLS_ID":1228897,"ROLL_MAINTAINED":1,"QC_DATE":"12-12-2012","ROLL_ID":8190553,"ROLL_NO":15,"QC_NAME":"test","ROLL_INCH":"5","ROLL_KG":22.6,"ROLL_YDS":1390.0779527557077,"TOTAL_PENALTY_POINT":"28","TOTAL_POINT":"14.5028","INSERTED_BY":1,"UPDATED_BY":1,"UPDATE_ID":1075,"REJECT_QNTY":"0.0","FABRIC_GRADE":"A","ROLL_STATUS":1,"COMMENTS":""},"list_data":[{"DEFECT_ID":11,"COUNT":4,"INCH_ID":6,"PENALTY":16},{"DEFECT_ID":15,"COUNT":3,"INCH_ID":4,"PENALTY":12}]}}';
      $response_obj = json_decode($save_obj);  
      //return $response_obj->status;
      $qc_mst_arr=array();
      $qc_dtls_arr=array();
      $db_type=return_db_type();
      if($db_type==0)
      {
        $mst_tbl="pro_qc_result_mst";
        $dtls_tbl="pro_qc_result_dtls";
      }
      else
      {
         $mst_tbl="PRO_QC_RESULT_MST"; 
         $dtls_tbl="PRO_QC_RESULT_DTLS";
      }
     


   if($response_obj->status == true)
   {
       
      $BARCODE_NO=$response_obj->data->index->BARCODE_NO ;
      $barcode_no="'".str_replace("'", "",$BARCODE_NO)."'";
      $is_exists = sql_select("SELECT   barcode_no from pro_qc_result_mst where status_active=1    and barcode_no in($barcode_no)  and is_deleted=0");
      if(count($is_exists)>0 && $response_obj->mode=='save')
      {
        return $resultset["status"] = "PleaseChangeMode";
      }


    $this->db->trans_start();
    $plan_to_delete = "";
    $qc_mst=return_next_id_by_sequence( "PRO_QC_RESULT_MST_SEQ","PRO_QC_RESULT_MST","","",0,"",0,0,0,0,0,0,0 );
    $rejectQty = $response_obj->data->index->REJECT_QNTY;
    $roll_status_id = $response_obj->data->index->ROLL_STATUS;
    $company_id=$response_obj->data->index->COMPANY_ID; 
    $variable_sqls = sql_select("SELECT AUTO_UPDATE from variable_settings_production where company_name =$company_id and variable_list in(47) and item_category_id=13 and is_deleted=0 and status_active=1");
    foreach($variable_sqls as $val)
    {
      $autoProductionQuantityUpdatebyQC=$val->AUTO_UPDATE;
    }
    $qnty = $response_obj->data->index->ROLL_KG ;;   
    $qc_qnty = ($roll_status_id!=2) ? ($qnty-$rejectQty) : $qnty;
    $DTLS_ID=$response_obj->data->index->DTLS_ID ;
    $ROLL_MAINTAINED=$response_obj->data->index->ROLL_MAINTAINED ;
    
    $QC_DATE=$response_obj->data->index->QC_DATE ;
    $ROLL_ID=$response_obj->data->index->ROLL_ID ;
    $ROLL_NO=$response_obj->data->index->ROLL_NO ;
    $QC_NAME=$response_obj->data->index->QC_NAME ;
    $COMMENTS=$response_obj->data->index->COMMENTS ;
    $ROLL_KG=$response_obj->data->index->ROLL_KG ;
    $ROLL_YDS=$response_obj->data->index->ROLL_YDS ;
    $ROLL_INCH=$response_obj->data->index->ROLL_INCH ;
    $TOTAL_PENALTY_POINT=$response_obj->data->index->TOTAL_PENALTY_POINT ;
    $TOTAL_POINT=$response_obj->data->index->TOTAL_POINT ;
    $FABRIC_GRADE=$response_obj->data->index->FABRIC_GRADE ;
    $INSERTED_BY=$response_obj->data->index->INSERTED_BY ;
    $UPDATED_BY=$response_obj->data->index->UPDATED_BY ;
    $update_id=$response_obj->data->index->UPDATE_ID ;
    if($db_type==0) 
    {
      $pc_date_time = date("Y-m-d H:i:s",time()); 
      $qc_dates_up=date("Y-m-d",strtotime($QC_DATE));
    }
    else
    {
      $pc_date_time = date("d-M-Y h:i:s A",time()); 
      $qc_dates_up=date("d-M-Y",strtotime($QC_DATE));

    } 
     
    if($response_obj->mode== "save")
    {                        
        $qc_mst_arr = array(
        'PRO_DTLS_ID'   => $DTLS_ID,
        'ROLL_MAINTAIN'  => $ROLL_MAINTAINED,
        'BARCODE_NO'  => $BARCODE_NO,
        'QC_DATE'        => $qc_dates_up,
        'ROLL_ID'  => $ROLL_ID,
        'ROLL_NO'  => $ROLL_NO,
        'QC_NAME'  => $QC_NAME,
        'COMMENTS'  => $COMMENTS,
        'ROLL_WEIGHT'  => $ROLL_KG,
        'ROLL_LENGTH'  => $ROLL_YDS,
        'ROLL_WIDTH'  => $ROLL_INCH,
        'REJECT_QNTY'  => $rejectQty,
        'TOTAL_PENALTY_POINT'  => $TOTAL_PENALTY_POINT,
        'TOTAL_POINT'  => $TOTAL_POINT,
        'ENTRY_FORM'  => 283,
        'ROLL_STATUS'  => $roll_status_id,
        'FABRIC_GRADE'  => $FABRIC_GRADE 
        );
        $qc_mst_arr['ID'] = $qc_mst;
        $qc_mst_arr['INSERTED_BY']   =$INSERTED_BY;
        $qc_mst_arr['INSERT_DATE']   = $pc_date_time;
        $qc_mst_arr['IS_TAB']   = 1;
        $this->insertData($qc_mst_arr, $mst_tbl);

    }
      else if($response_obj->mode == "update")
      {
        $qc_mst=$update_id;
        $qc_mst_arr_up = array( 
          'QC_DATE'        => $qc_dates_up,
          'QC_NAME'  => $QC_NAME,
          'COMMENTS'  => $COMMENTS,
          'ROLL_WEIGHT'  => $ROLL_KG,
          'ROLL_LENGTH'  => $ROLL_YDS,
          'ROLL_WIDTH'  => $ROLL_INCH,
          'REJECT_QNTY'  => $rejectQty,
          'TOTAL_PENALTY_POINT'  => $TOTAL_PENALTY_POINT,
          'TOTAL_POINT'  => $TOTAL_POINT,
          'ROLL_STATUS'  => $roll_status_id,
          'FABRIC_GRADE'  => $FABRIC_GRADE 
          );
          $qc_mst_arr_up['UPDATE_DATE']      = $pc_date_time;
          $qc_mst_arr_up['UPDATE_BY']       = $INSERTED_BY;
          $this->updateData($mst_tbl, $qc_mst_arr_up, array('ID' => $update_id));
         
      }
      else if($response_obj->mode == "delete")
      {
        //$plan_to_delete .= $response_obj->PLAN_ID . ",";
        //$this->deleteRowByAttribute('PRO_QC_RESULT_MST', array('ID' => $response_obj->PLAN_ID));
      }
      if($response_obj->mode == "update")
        {
          $this->db->query("delete from pro_qc_result_dtls where mst_id ='$update_id'");
        }  
      $dtls_data=$response_obj->data->list_data;
      foreach($dtls_data as $val)
      {
        $qc_dtls=return_next_id_by_sequence( "PRO_QC_RESULT_DTLS_SEQ","PRO_QC_RESULT_DTLS","","",0,"",0,0,0,0,0,0,0 );$qc_dtls_arr = array(
          'ID'   => $qc_dtls,
          'MST_ID'  => $qc_mst,
          'DEFECT_NAME'  => $val->DEFECT_ID,
          'DEFECT_COUNT'        => $val->COUNT,
          'FOUND_IN_INCH'  => $val->INCH_ID,
          'PENALTY_POINT'  => $val->PENALTY ,
          'INSERTED_BY'  => $INSERTED_BY ,
          'INSERT_DATE'  => $pc_date_time 
          );      
          $this->insertData($qc_dtls_arr, $dtls_tbl);  
      }

        
          
          



          if($autoProductionQuantityUpdatebyQC == 1 && $roll_status_id!=2)
          {
            $pro_roll_sql ="UPDATE pro_roll_details SET qnty=$qc_qnty,reject_qnty='$rejectQty' WHERE barcode_no = '$BARCODE_NO' AND entry_form=2 and dtls_id=$DTLS_ID"; 

            $rID3 = $this->db->query($pro_roll_sql);

            if($rID3)
            {
              $roll_qc_rj_result =sql_select("SELECT sum(qnty) as QC_QNTY,sum(reject_qnty) as REJECT_QNTY from pro_roll_details where dtls_id=$DTLS_ID and status_active=1 and is_deleted=0 and entry_form=2");
              foreach($roll_qc_rj_result as $v)
              {
                $grey_receive_qnty=$v->QC_QNTY;
                $reject_fabric_receive=$v->REJECT_QNTY;
              }

              $pro_grey_prod_sql ="UPDATE pro_grey_prod_entry_dtls SET grey_receive_qnty='$grey_receive_qnty',reject_fabric_receive='$reject_fabric_receive' WHERE id=$DTLS_ID";

              $rID4 = $this->db->query($pro_grey_prod_sql);
            }

          }
          if ($this->db->trans_status() === FALSE)
          {
            $this->db->trans_rollback();
            return $resultset["status"] = "Failed";
          }
          else
          {
            $this->db->trans_commit();
            $this->db->trans_complete();
            return $resultset["status"] = "Successful";
          }
  }
  else
  {
    return $resultset["status"] = "Failed";
  }
}



 function create_finish_qc_result($save_obj)
    {

     
       //$save_obj='{"status":true,"mode":"update","MST_ID":32168,"TRANS_ID":0,"DTLS_ID":5393,"QC_MST_ID":1038,"UPDATE_ID":0,"data":{"index":{"BARCODE_NO":18020005858,"BATCH_ID":5103,"BATCH_NO":"R208","BODY_PART_ID":4,"BOOKING_NO":0,"BOOKING_WITHOUT_ORDER":0,"COMPANY_ID":1,"SERVICE_COMPANY":1,"SOURCE":1,"SERVICE_LOCATION":11,"LOCATION":18,"MACHINE_ID":1,"SHIFT":1,"COLOR":"RED","CONS_COMP":"1X1 Rib, Cotton 100% ","DETER_ID":65,"DIA":"Tube","DIA_TYPE":1,"RECEIVE_DATE":22 ,"REJECT_QTY":22 ,"COMMENTS":22 ,"TOTAL_PENALTY_POINT":22 ,"TOTAL_POINT":22 ,"FABRIC_GRADE":22,"GSM":"220","RECEIVE_DATE":"02-02-2019","IS_SALES_ID":0,"ORDER_ID":0,"QC_PASS_QTY":"20","REJECT_QTY":0,"ROLL_ID":50514,"ROLL_NO":"4","ROLL_WGT":50514,"INSERTED_BY":1,"UPDATED_BY":1},"list_data":[{"DEFECT_ID":1,"COUNT":3,"INCH_ID":5,"PENALTY":6}]}}';
      $response_obj = json_decode($save_obj);  
      //return $response_obj->status;
      $inv_receive_arr=array();
      $transaction_arr=array();
      $qc_dtls_arr=array();
      $db_type=return_db_type();
      $new_array_color=array();
      $prod_data_array  = array();
      $prod_new_array  = array();
      $color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
     if($response_obj->status == true)
     {
          $BARCODE_NO=$response_obj->data->index->BARCODE_NO ;
          $barcode_no="'".str_replace("'", "",$BARCODE_NO)."'";
          $is_exists = sql_select("SELECT   barcode_no from pro_finish_fabric_rcv_dtls where status_active=1    and barcode_no in($barcode_no)  and is_deleted=0");
          if(count($is_exists)>0 && $response_obj->mode=='save')
          {
            return $resultset["status"] = "PleaseChangeMode";
          }
          $COMPANY_ID=$response_obj->data->index->COMPANY_ID ;
          $SERVICE_COMPANY=$response_obj->data->index->SERVICE_COMPANY ;
          $SOURCE=$response_obj->data->index->SOURCE ;
          $SERVICE_LOCATION=$response_obj->data->index->SERVICE_LOCATION ;
          $LOCATION=$response_obj->data->index->LOCATION ;
          $MACHINE_ID=$response_obj->data->index->MACHINE_ID ;
          $SHIFT=$response_obj->data->index->SHIFT ;
          $COLOR=$response_obj->data->index->COLOR ;
          $CONS_COMP=$response_obj->data->index->CONS_COMP ;
          $DETER_ID=$response_obj->data->index->DETER_ID ;
          $DIA=$response_obj->data->index->DIA ;
          $DIA_TYPE=$response_obj->data->index->DIA_TYPE ;
          $GSM=$response_obj->data->index->GSM ;
          $COMMENTS=$response_obj->data->index->COMMENTS ;
          $TOTAL_PENALTY_POINT=$response_obj->data->index->TOTAL_PENALTY_POINT ;
          $TOTAL_POINT=$response_obj->data->index->TOTAL_POINT ;
          $FABRIC_GRADE=$response_obj->data->index->FABRIC_GRADE ; 
          $IS_SALES_ID=$response_obj->data->index->IS_SALES_ID ;
          $ORDER_ID=$response_obj->data->index->ORDER_ID ;
          $QC_PASS_QTY=$response_obj->data->index->QC_PASS_QTY ;
          $REJECT_QTY=$response_obj->data->index->REJECT_QTY ;
          $ROLL_ID=$response_obj->data->index->ROLL_ID ;
          $ROLL_NO=$response_obj->data->index->ROLL_NO ; 
          $ROLL_WGT=$response_obj->data->index->ROLL_WGT ;
          $RECEIVE_DATE=$response_obj->data->index->RECEIVE_DATE ; 
          $INSERTED_BY=$response_obj->data->index->INSERTED_BY ; 
          $UPDATED_BY=$response_obj->data->index->UPDATED_BY ; 
          
          $BATCH_ID=$response_obj->data->index->BATCH_ID ;
          $BATCH_NO=$response_obj->data->index->BATCH_NO ;   
          $BODY_PART_ID=$response_obj->data->index->BODY_PART_ID ;
          $BOOKING_NO=$response_obj->data->index->BOOKING_NO ;
          $BOOKING_WITHOUT_ORDER=$response_obj->data->index->BOOKING_WITHOUT_ORDER ;     
          $MST_ID=$response_obj->MST_ID ;
          $PROD_ID=$response_obj->PROD_ID ;
          $TRANS_ID=$response_obj->TRANS_ID ;
          $DTLS_ID=$response_obj->DTLS_ID ;
          $QC_MST_ID=$response_obj->QC_MST_ID ;
          $UPDATE_ID=$response_obj->UPDATE_ID ;
            
          $ROLL_STATUS=$response_obj->data->index->ROLL_STATUS ;
          $ROLL_WIDTH=$response_obj->data->index->ROLL_WIDTH ;
          $ROLL_WEIGHT=$response_obj->data->index->ROLL_WEIGHT ;
          $ROLL_LENGTH=$response_obj->data->index->ROLL_LENGTH ;
          $variable_sqls = sql_select("SELECT AUTO_UPDATE from variable_settings_production where company_name =$COMPANY_ID and variable_list in(47) and item_category_id=2 and is_deleted=0 and status_active=1");
          $autoProductionQuantityUpdatebyQC=2;
          foreach($variable_sqls as $val)
          {
            $autoProductionQuantityUpdatebyQC=$val->AUTO_UPDATE;
          }

          if($db_type==0) 
          {
            $pc_date_time = date("Y-m-d H:i:s",time()); 
            $receive_date=date("Y-m-d",strtotime($RECEIVE_DATE));
          }
          else
          {
            $pc_date_time = date("d-M-Y h:i:s A",time()); 
            $receive_date=date("d-M-Y",strtotime($RECEIVE_DATE));

          } 
           $this->db->trans_start();  
          if($response_obj->mode== "update")
          {
                if($autoProductionQuantityUpdatebyQC == 1)
                {
                  $pro_roll_sql ="UPDATE pro_roll_details SET REJECT_QNTY='$REJECT_QTY',QC_PASS_QNTY='$QC_PASS_QTY' ,UPDATED_BY='$INSERTED_BY', UPDATE_DATE='$pc_date_time' WHERE DTLS_ID=$DTLS_ID and BARCODE_NO='$BARCODE_NO' and ENTRY_FORM=66 and ROLL_ID='$ROLL_ID'";
                  $rowIdRoll = $this->db->query($pro_roll_sql);

                  $pro_orderwise_sql ="UPDATE order_wise_pro_details SET RETURNABLE_QNTY='$REJECT_QTY',QUANTITY='$QC_PASS_QTY' ,UPDATED_BY='$INSERTED_BY', UPDATE_DATE='$pc_date_time' WHERE DTLS_ID=$DTLS_ID and PO_BREAKDOWN_ID='$ORDER_ID' and ENTRY_FORM=66 and PROD_ID='$PROD_ID' ";
                  $rowIdOrderwise = $this->db->query($pro_orderwise_sql);


                  $pro_dtls_sql ="UPDATE pro_finish_fabric_rcv_dtls SET REJECT_QTY='$REJECT_QTY',RECEIVE_QNTY='$QC_PASS_QTY' ,UPDATED_BY='$INSERTED_BY', UPDATE_DATE='$pc_date_time' WHERE id=$DTLS_ID and BARCODE_NO='$BARCODE_NO' and PROD_ID='$PROD_ID'";
                  $rowIdDtls = $this->db->query($pro_dtls_sql);

                  $pro_trans_sql ="UPDATE inv_transaction SET CONS_REJECT_QNTY='$REJECT_QTY',CONS_QUANTITY='$QC_PASS_QTY' ,UPDATED_BY='$INSERTED_BY', UPDATE_DATE='$pc_date_time' WHERE id='$TRANS_ID' and MST_ID='$MST_ID' and PROD_ID='$PROD_ID'";
                  $rowIdTrans = $this->db->query($pro_trans_sql);

                }
                $qc_mst_arr_up = array( 
                  'QC_DATE'        => $receive_date,
                   
                  'COMMENTS'  => $COMMENTS,
                  'ROLL_WEIGHT'  => $QC_PASS_QTY,
                  'ROLL_LENGTH'  => $ROLL_LENGTH,
                  'ROLL_WIDTH'  => $ROLL_WIDTH,
                  'REJECT_QNTY'  => $REJECT_QTY,
                  'TOTAL_PENALTY_POINT'  => $TOTAL_PENALTY_POINT,
                  'TOTAL_POINT'  => $TOTAL_POINT,
                  'ROLL_STATUS'  => $ROLL_STATUS,  
                  'FABRIC_GRADE'  => $FABRIC_GRADE 
                  );
                $qc_mst_arr_up['UPDATE_DATE']      = $pc_date_time;
                $qc_mst_arr_up['UPDATE_BY']       = $INSERTED_BY;
                $up_qc_row=$this->updateData(csf("PRO_QC_RESULT_MST",$db_type), $qc_mst_arr_up, array('ID' => $QC_MST_ID));
                $dtls_del=$this->db->query("delete from pro_qc_result_dtls where mst_id ='$QC_MST_ID'");
                $dtls_data=$response_obj->data->list_data;
                foreach($dtls_data as $val)
                {
                    $qc_dtls=return_next_id_by_sequence( "PRO_QC_RESULT_DTLS_SEQ",csf("PRO_QC_RESULT_DTLS",$db_type),"","",0,"",0,0,0,0,0,0,0 );
                    $qc_dtls_arr = array(
                      'ID'   => $qc_dtls,
                      'MST_ID'  => $QC_MST_ID,
                      'DEFECT_NAME'  => $val->DEFECT_ID,
                      'DEFECT_COUNT'        => $val->COUNT,
                      'FOUND_IN_INCH'  => $val->INCH_ID,
                      'PENALTY_POINT'  => $val->PENALTY ,
                      'INSERTED_BY'  => $INSERTED_BY ,
                      'INSERT_DATE'  => $pc_date_time 
                      );      
                      $this->insertData($qc_dtls_arr, csf("PRO_QC_RESULT_DTLS",$db_type));  
                }
                if ($this->db->trans_status() === FALSE)
                {
                  $this->db->trans_rollback();
                  return $resultset["status"] = "Failed";
                }
                else
                {
                    $this->db->trans_commit();
                    $this->db->trans_complete();
                    return $resultset["status"] = "Successful";
                }


              
                 

          }


            
          $company_id=$response_obj->data->index->COMPANY_ID;    
          $id_dtls=return_next_id_by_sequence( "PRO_FIN_FAB_RCV_DTLS_PK_SEQ", "pro_finish_fabric_rcv_dtls","","",0,"",0,0,0,0,0,0,0) ;
          $id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details","","",0,"",0,0,0,0,0,0,0);
          $id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details","","",0,"",0,0,0,0,0,0,0);
          $id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls","","",0,"",0,0,0,0,0,0,0);

          //$id=return_next_id_by_sequence( "INV_RECEIVE_MASTER_PK_SEQ","inv_receive_master","","",0,"",0,0,0,0,0,0,0 );
          $new_mrr_number = explode("*", return_next_id_by_sequence("", "inv_receive_master","",1,$company_id,"FFPR",66,date("Y",time()),0,0,0,0,0 ));



         
          $variable_sqls = sql_select("SELECT AUTO_UPDATE from variable_settings_production where company_name =$company_id and variable_list =15 and item_category_id=2 and is_deleted=0 and status_active=1");
          $fabric_store_auto_update=0;
          foreach($variable_sqls as $val)
          {
            $fabric_store_auto_update=$val->AUTO_UPDATE;
          }
           
          
     
          if($response_obj->mode== "save")
          {  
              $hour=date("h");  
              $mrr_sql="SELECT MST_ID from auto_mrr_maintain_tab where company_id='$COMPANY_ID' and source='$SOURCE' and serving_company='$SERVICE_COMPANY' and serving_location='$SERVICE_LOCATION' and mrr_date='$receive_date' and curr_hour='$hour'";  
              $mrr_arr=sql_select($mrr_sql);
              $today_sql=sql_select("SELECT MST_ID from auto_mrr_maintain_tab where mrr_date='$receive_date'");
              if(count($today_sql)==0)
              {
                $this->db->query("delete from  auto_mrr_maintain_tab where mrr_date<'$receive_date'");
              }
              if(count($mrr_arr)==0)                  
              {
                
                 $auto_mrr_id=return_next_id("id", "auto_mrr_maintain_tab", "", "",$db_type);
                 $id=return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ","inv_receive_master","","",0,"",0,0,0,0,0,0,0 ); 
                 $inv_receive_arr = array(               
                  'ID'  => $id,
                  'RECV_NUMBER_PREFIX'  => $new_mrr_number[1],
                  'RECV_NUMBER_PREFIX_NUM'  => $new_mrr_number[2],
                  'RECV_NUMBER'        => $new_mrr_number[0],
                  'RECEIVE_DATE'  => $receive_date,
                  'COMPANY_ID'  => $COMPANY_ID,
                  'KNITTING_SOURCE'  => $SOURCE,
                  'KNITTING_COMPANY'  => $SERVICE_COMPANY,
                  'ITEM_CATEGORY'  => 2,
                  'ENTRY_FORM'  => 66,
                  'CHALLAN_NO'  => 0,
                  'STORE_ID'  => 0,                
                  'LOCATION_ID'  => $LOCATION,
                  'KNITTING_LOCATION_ID'  => $SERVICE_LOCATION
                  );
                 $inv_receive_arr['INSERTED_BY']   =$INSERTED_BY;
                 $inv_receive_arr['INSERT_DATE']   = $pc_date_time;

                 $inv_rcv_id=$this->insertData($inv_receive_arr, csf("inv_receive_master",$db_type));

                 $auto_mrr_arr = array(
                  'ID'  => $auto_mrr_id,
                  'COMPANY_ID'  => $COMPANY_ID,
                  'SOURCE'  => $SOURCE,
                  'SERVING_COMPANY'        => $SERVICE_COMPANY,
                  'SERVING_LOCATION'  => $SERVICE_LOCATION,
                  'MRR_DATE'  => $receive_date,
                  'MST_ID'  => $id,
                  'MRR_NO'  => $new_mrr_number[0],
                  'CURR_HOUR'  => $hour  
                  );


                 $inv_rcv_id=$this->insertData($auto_mrr_arr, csf("AUTO_MRR_MAINTAIN_TAB",$db_type));


              }
              else 
              {
                 $id=$mrr_arr[0]->MST_ID;
              }

               

              $productDataArray = array();
              $stockArray = array();
              $productData = sql_select("SELECT ID, COMPANY_ID, DETARMINATION_ID, CURRENT_STOCK, GSM, DIA_WIDTH, COLOR from product_details_master where item_category_id=2 and status_active=1 and is_deleted=0");
              foreach ($productData as $row) 
              {
                $productDataArray[$row->COMPANY_ID][$row->DETARMINATION_ID][$row->GSM][$row->DIA_WIDTH][$row->COLOR] = $row->ID;
                $stockArray[$row->ID] = $row->CURRENT_STOCK;
              }


          }


          if (!in_array($COLOR, $new_array_color)) 
          {
             $color_id = return_id($COLOR, $color_arr, "lib_color", "color_name","");
              $new_array_color[$color_id] = $COLOR;
          } 
          else
            $color_id = array_search($COLOR, $new_array_color);
          if(isset($productDataArray[$company_id][$DETER_ID][$GSM][$DIA][$color_id]))
           $prod_id = $productDataArray[$company_id][$DETER_ID][$GSM][$DIA][$color_id];
          else $prod_id="";
           if (str_replace("'", "", $fabric_store_auto_update) == 1)
           {
              $stock_qnty = $QC_PASS_QTY;
              $last_purchased_qnty = $QC_PASS_QTY;
           } 
           else
           {
              $stock_qnty = 0;
              $last_purchased_qnty = 0;
           }


           $prod_name_dtls = trim($CONS_COMP) . ", " . trim($GSM) . ", " . trim($DIA);

           if ($prod_id == "") 
           {
              $dataString = $DETER_ID . "**" . $CONS_COMP . "**" . $prod_name_dtls . "**" . $color_id . "**" . trim($GSM) . "**" . trim($DIA);
              $prod_id = array_search($dataString, $prod_data_array);
              if ($prod_id == "") 
              {
                $product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master","","",0,"",0,0,0,0,0,0,0 );
                $prod_id = $product_id;
                $prod_data_array[$prod_id] = $dataString;
                $prod_new_array[$prod_id] = $stock_qnty;
            
              } 
              else 
              {
                if( $prod_new_array[$prod_id])
                $prod_new_array[$prod_id] += $stock_qnty;
                else $prod_new_array[$prod_id]  = $stock_qnty;
              }
          } 
          else
          {
            $current_stock = $stockArray[$prod_id] + $stock_qnty;
            $prod_id_array[] = $prod_id;
            //$data_array_prod_update[$prod_id] = explode("*", ($avg_rate_per_unit . "*'" . $last_purchased_qnty . "'*'" . $current_stock . "'*'" . $stock_value . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
          }

           

          if (str_replace("'", "", $fabric_store_auto_update) == 1) 
          {
              $order_rate = 0;
              $order_amount = 0;
              $cons_rate = 0;
              $cons_amount = 0;            
              $id_trans =return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction","","",0,"",0,0,0,0,0,0,0);
              $rate = 0;
              $amount = 0;
              $transaction_arr = array(               
                  'ID'  => $id_trans,
                  'MST_ID'  => $id,
                  'COMPANY_ID'        => $company_id,
                  'PROD_ID'  => $prod_id,
                  'ITEM_CATEGORY'  => 2,
                  'TRANSACTION_TYPE'  => 1,
                  'TRANSACTION_DATE'  => $receive_date,
                  'STORE_ID'  => 0,
                  'ORDER_UOM'  => 12,
                  'ORDER_QNTY'  => $QC_PASS_QTY,
                  'ORDER_RATE'  => $rate,                
                  'ORDER_AMOUNT'  => $amount,
                  'CONS_UOM'  => 12,
                  'CONS_QUANTITY'  => $QC_PASS_QTY,
                  'CONS_REJECT_QNTY'  => $REJECT_QTY,
                  'CONS_RATE'  => $rate,
                  'CONS_AMOUNT'  => $amount,
                  'BALANCE_QNTY'  => $QC_PASS_QTY,
                  'BALANCE_AMOUNT'  => $amount,
                  'MACHINE_ID'  => $MACHINE_ID,
                  'RACK'  => 0,
                  'SELF'  => 0 
                  );
                 
                $transaction_arr['INSERTED_BY']   =$INSERTED_BY;
                $transaction_arr['INSERT_DATE']   = $pc_date_time;               
               $trans_row_id=$this->insertData($transaction_arr, csf("inv_transaction",$db_type)); 
          } 
          else 
          {
            $id_trans = 0;
          } 
           $production_dtls_arr = array(               
                  'ID'  => $id_dtls,
                  'MST_ID'  => $id,
                  'TRANS_ID'        => $id_trans,
                  'PROD_ID'  => $prod_id,
                  'BATCH_ID'  => $BATCH_ID,
                  'BODY_PART_ID'  => $BODY_PART_ID,
                  'FABRIC_DESCRIPTION_ID'  => $DETER_ID,
                  'GSM'  => $GSM,
                  'WIDTH'  => $DIA,
                  'DIA_WIDTH_TYPE'  => $DIA_TYPE,
                  'COLOR_ID'  => $color_id,                
                  'PRODUCTION_QTY'  => $ROLL_WGT,
                  'RECEIVE_QNTY'  => $QC_PASS_QTY,
                  'REJECT_QTY'  => $REJECT_QTY,
                  'ORDER_ID'  => $ORDER_ID,
                  'MACHINE_NO_ID'  => $MACHINE_ID,
                  'SHIFT_NAME'  => $SHIFT,
                  'RACK_NO'  => 0,
                  'SHELF_NO'  => 0,
                  'ROLL_ID'  => $ROLL_ID,
                  'ROLL_NO'  => $ROLL_NO,
                  'IS_TAB'  => 1,
                  'BARCODE_NO'  => $BARCODE_NO 
                  );
                 
                $production_dtls_arr['INSERTED_BY']   =$INSERTED_BY;
                $production_dtls_arr['INSERT_DATE']   = $pc_date_time;               
               $prod_dlts_row_id=$this->insertData($production_dtls_arr, csf("pro_finish_fabric_rcv_dtls",$db_type));


               $roll_dtls_arr = array(               
                  'ID'  => $id_roll,
                  'BARCODE_NO'  => $BARCODE_NO,
                  'MST_ID'        => $id,
                  'DTLS_ID'  => $id_dtls,
                  'PO_BREAKDOWN_ID'  => $ORDER_ID,
                  'ENTRY_FORM'  => 66,
                  'QNTY'  => $ROLL_WGT,
                  'REJECT_QNTY'  => $REJECT_QTY,
                  'QC_PASS_QNTY'  => $QC_PASS_QTY,
                  'ROLL_NO'  => $ROLL_NO,
                  'ROLL_ID'  => $ROLL_ID,                
                  'IS_SALES'  => $IS_SALES_ID,
                  'BOOKING_WITHOUT_ORDER'  => $BOOKING_WITHOUT_ORDER,
                  'BOOKING_NO'  => $BOOKING_NO 
                  );
                 
                $roll_dtls_arr['INSERTED_BY']   =$INSERTED_BY;
                $roll_dtls_arr['INSERT_DATE']   = $pc_date_time;               
               $roll_dtls_row_id=$this->insertData($roll_dtls_arr,csf("pro_roll_details",$db_type) ); 
               $prop_dtls_arr = array(               
                  'ID'  => $id_prop,
                  'TRANS_ID'  => $id_trans,
                  'TRANS_TYPE'        => 1,
                  'ENTRY_FORM'  => 66,
                  'DTLS_ID'  => $id_dtls,
                  'PO_BREAKDOWN_ID'  => $ORDER_ID,
                  'PROD_ID'  => $prod_id,
                  'COLOR_ID'  => $color_id,
                  'QUANTITY'  => $QC_PASS_QTY,
                  'RETURNABLE_QNTY'  => $REJECT_QTY,
                  'IS_SALES'  => $IS_SALES_ID  
                  );
                 
                $prop_dtls_arr['INSERTED_BY']   =$INSERTED_BY;
                $prop_dtls_arr['INSERT_DATE']   = $pc_date_time;               
               $prop_dtls_row_id=$this->insertData($prop_dtls_arr,  csf("order_wise_pro_details",$db_type) );

               $avg_rate_per_unit = 0;
               $stock_value = 0;
               foreach ($prod_new_array as $prod_id => $current_stock) 
               {
                  $product_data = explode("**", $prod_data_array[$prod_id]);
                  $deterId = $product_data[0];
                  $consComp = trim($product_data[1]);
                  $prod_name_dtls = $product_data[2];
                  $color_id = $product_data[3];
                  $gsm = $product_data[4];
                  $dia = $product_data[5];
                  $last_purchased_qnty = $current_stock;

                  $product_dtls_mst_arr = array(               
                    'ID'  => $prod_id,
                    'COMPANY_ID'  => $COMPANY_ID,
                    'ITEM_CATEGORY_ID'        => 2,
                    'DETARMINATION_ID'  => $DETER_ID,
                    'ITEM_DESCRIPTION'  => $CONS_COMP,
                    'PRODUCT_NAME_DETAILS'  => $prod_name_dtls,
                    'UNIT_OF_MEASURE'  => 12,
                    'AVG_RATE_PER_UNIT'  => $avg_rate_per_unit,
                    'LAST_PURCHASED_QNTY'  => $last_purchased_qnty,
                    'CURRENT_STOCK'  => $current_stock,
                    'STOCK_VALUE'  => $stock_value  ,
                    'COLOR'  => $color_id  ,
                    'GSM'  => $gsm  ,
                    'DIA_WIDTH'  => $dia  ,
                    'ENTRY_FORM'  => 66
                    );

                  $product_dtls_mst_arr['INSERTED_BY']   =$INSERTED_BY;
                  $product_dtls_mst_arr['INSERT_DATE']   = $pc_date_time;               
                  $prod_dlts_mst_row_id=$this->insertData($product_dtls_mst_arr, csf("product_details_master",$db_type));
                 
              }
              $qc_name_by_id=return_field_value("user_name", "user_passwd", "id='$INSERTED_BY'  ", "user_name") ;

              $qc_mst=return_next_id_by_sequence( "PRO_QC_RESULT_MST_SEQ","PRO_QC_RESULT_MST","","",0,"",0,0,0,0,0,0,0 );
              $qc_mst_arr = array(
                'PRO_DTLS_ID'   => $id_dtls,
                'ROLL_MAINTAIN'  => 1,
                'BARCODE_NO'  => $BARCODE_NO,
                'QC_DATE'        => $receive_date,
                'ROLL_ID'  => $ROLL_ID,
                'ROLL_NO'  => $ROLL_NO,
                'QC_NAME'  => $qc_name_by_id,
                'COMMENTS'  => $COMMENTS,
                'ROLL_WEIGHT'  => $QC_PASS_QTY,
                'ROLL_LENGTH'  => $ROLL_LENGTH,
                'ROLL_WIDTH'  => $ROLL_WIDTH,
                'REJECT_QNTY'  => $REJECT_QTY,
                'ENTRY_FORM'  => 267,
                'TOTAL_PENALTY_POINT'  => $TOTAL_PENALTY_POINT,
                'TOTAL_POINT'  => $TOTAL_POINT, 
                'FABRIC_GRADE'  => $FABRIC_GRADE ,
                'ROLL_STATUS'  => $ROLL_STATUS
                );
              $qc_mst_arr['ID'] = $qc_mst;
              $qc_mst_arr['INSERTED_BY']   =$INSERTED_BY;
              $qc_mst_arr['INSERT_DATE']   = $pc_date_time;
              $qc_mst_arr['IS_TAB']   = 1;
              $this->insertData($qc_mst_arr,  csf("pro_qc_result_mst",$db_type));





          $dtls_data=$response_obj->data->list_data;
          foreach($dtls_data as $val)
          {
            $qc_dtls=return_next_id_by_sequence( "PRO_QC_RESULT_DTLS_SEQ","PRO_QC_RESULT_DTLS","","",0,"",0,0,0,0,0,0,0 );$qc_dtls_arr = array(
              'ID'   => $qc_dtls,
              'MST_ID'  => $qc_mst,
              'DEFECT_NAME'  => $val->DEFECT_ID,
              'DEFECT_COUNT'        => $val->COUNT,
              'FOUND_IN_INCH'  => $val->INCH_ID,
              'PENALTY_POINT'  => $val->PENALTY ,
              'INSERTED_BY'  => $INSERTED_BY , 
              'INSERT_DATE'  => $pc_date_time 
              );      
            $this->insertData($qc_dtls_arr, csf("pro_qc_result_dtls",$db_type));  
          } 
          $this->db->trans_complete();
          if ($this->db->trans_status() == TRUE) 
          {
            return $resultset["status"] = "Successful";
          } 
          else
          {
            $resultset["status"] = "Failed";
          }
     }
     else
     {
       return $resultset["status"] = "Failed";
     }
}




  
  
/**
* [create_plan for Plan CRUD]
* @param  [object] $plan_obj [description]
* @return [array]           [description]
*/




 function save_update_sewing_input($save_obj)
 {

      $db_type=return_db_type(); 
      //$save_obj='{"status":true,"mode":"save","production_type":5,"UPDATE_ID":0,"data":{"index":{"company_id":6,"location_id":13,"production_source":1,"serving_company":6,"floor_id":197,"sewing_line":361,"organic":"t5","user_id":1,"production_date":"27-1-2019","hour":"5:25","remarks":"utcutf","txt_system_id":""},"list_data":[{"cut_no":"UG-18-000071","bundle_no":"UG-18-71-19","barcode_no":"18990000006187","order_id":33733,"item_id":6,"country_id":4,"color_id":4960,"size_id":3,"color_size_id":299123,"qnty":4,"is_rescan":1,"color_type_id":0}]}}';
      $response_obj = json_decode($save_obj); 
      $qc_mst_arr=array();
      $qc_dtls_arr=array();     
      //$company_id=$response_obj->data->index->COMPANY_ID;  
      //$cbo_company_name=3;
      if($response_obj->status == true)
      {
        $production_types=$response_obj->production_type;
        if($production_types==4)$entry_forms=96; else $entry_forms=0;
        $mst_tbl_id=0;
        $dtls_tbl_id=0;
        $this->db->trans_start();
        $production_date = $response_obj->data->index->production_date ; 
        $remarks = $response_obj->data->index->remarks; 
        $txt_reporting_hour = $response_obj->data->index->hour; 
        if($db_type==0) 
        {
           $year_cond = "YEAR(insert_date)";
           $pc_date_time = date("Y-m-d H:i:s",time()); 
           $production_date= date("Y-m-d",strtotime($production_date));
           $txt_reporting_hour=str_replace("'","",$production_date)." ".str_replace("'","",$txt_reporting_hour);
           //$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')"; 
           $txt_reporting_hour=  date("Y-m-d H:i:s",strtotime($txt_reporting_hour)); 
        }
        else
        {
           $year_cond="to_char(insert_date,'YYYY')";
           $pc_date_time = date("d-M-Y h:i:s A",time()); 
           $production_date= date("d-M-Y",strtotime($production_date)); 
           $txt_reporting_hour=str_replace("'","",$production_date)." ".str_replace("'","",$txt_reporting_hour);
           $txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')"; 

        }
        $cbo_company_name = $response_obj->data->index->company_id ;  
        
        $location_id = $response_obj->data->index->location_id ; 
        $production_source = $response_obj->data->index->production_source ; 
        $serving_company = $response_obj->data->index->serving_company ; 
        $floor_id = $response_obj->data->index->floor_id ; 
        $sewing_line = $response_obj->data->index->sewing_line ; 
        $organic = $response_obj->data->index->organic ; 
        $user_id = $response_obj->data->index->user_id ;  
        $txt_system_id = $response_obj->data->index->txt_system_id ; 
         
        
 
         
        
         
        if (str_replace("'", "", $txt_system_id) == "") 
        {
            if($production_types==4) $mrr_sty="SWI"; else $mrr_sty="SWO";

            $new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst","",1,$cbo_company_name,$mrr_sty,0,date("Y",time()),0,0,$production_types,0,0 ));
            
            $mst_id=return_next_id_by_sequence( "pro_gmts_delivery_mst_seq","pro_gmts_delivery_mst_seq","","",0,"",0,0,0,0,0,0,0 );
            $challan_no =(int) $new_sys_number[2];
            $txt_challan_no = $new_sys_number[0];
             
            $bundle_mst_arr = array(
                  'ID'   => $mst_id,
                  'SYS_NUMBER_PREFIX'  => $new_sys_number[1],
                  'SYS_NUMBER_PREFIX_NUM'  =>  (int) $new_sys_number[2],
                   'SYS_NUMBER'  =>  $new_sys_number[0],
                  'DELIVERY_DATE'        => $production_date,                 
                  'COMPANY_ID'  =>  $cbo_company_name,
                  'PRODUCTION_TYPE'  => $production_types,
                  'LOCATION_ID'  => $location_id,
                  'DELIVERY_BASIS'  => 3,
                  'PRODUCTION_SOURCE'  => $production_source,
                  'SERVING_COMPANY'  => $serving_company,
                  'FLOOR_ID'  => $floor_id,
                  'SEWING_LINE'  => $sewing_line,
                  'ORGANIC'  => $organic,
                  'ENTRY_FORM'  => $entry_forms,                  
                  'INSERTED_BY'  =>  $user_id ,
                  'INSERT_DATE'  =>  $pc_date_time  
                  );
            $mrr_tbl_id=$this->insertData($bundle_mst_arr, "pro_gmts_delivery_mst");
             

        } 
        else 
        {
          
            $bundle_mst_arr_up = array( 
            'DELIVERY_DATE'        => $production_date,                 
            'COMPANY_ID'  =>  $cbo_company_name, 
            'LOCATION_ID'  => $location_id, 
            'PRODUCTION_SOURCE'  => $production_source,
            'SERVING_COMPANY'  => $serving_company,
            'FLOOR_ID'  => $floor_id,
            'SEWING_LINE'  => $sewing_line,
            'ORGANIC'  => $organic ,
            'UPDATED_BY'  =>  $user_id ,
            'UPDATED_BY'  =>  $pc_date_time  
            );

            $mst_id = str_replace("'", "", $txt_system_id);             
            $this->updateData('pro_gmts_delivery_mst', $bundle_mst_arr_up, array('ID' => $mst_id));

        }

        $mstArr = array();
        $dtlsArr = array();
        $colorSizeArr = array();
        $mstIdArr = array();
        $colorSizeIdArr = array();

        $bundleCutArr = array();
        $color_type_arr = array();
        $is_rescan_arr = array();
        $cutArr = array();
        $dtlsArrColorSize = array();
        $bundleRescanArr = array();
        $bundleBarcodeArr = array();
        $duplicate_bundle = array();
        $bundleCheckArr = array();
        $all_cut_no_arr = array();
        $dtls_data=$response_obj->data->list_data;

        foreach($dtls_data as $v)
        {   
          $bundleCheck=$v->bundle_no;
          $cutNo=$v->cut_no;
          $is_rescan=$v->is_rescan;
          if($is_rescan!=1)
          {
            $bundleCheckArr[trim($bundleCheck)]=trim($bundleCheck); 
          }
          $all_cut_no_arr[$cutNo]=$cutNo;
        }



        $bundle="'".implode("','",$bundleCheckArr)."'";
        $receive_sql="SELECT c.barcode_no,c.BUNDLE_NO from pro_garments_production_dtls c where  c.bundle_no  in ($bundle)  and c.production_type='$production_types' and c.status_active=1 and c.is_deleted=0 and (c.is_rescan=0 or c.is_rescan is null)"; ;
        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {

          $duplicate_bundle[trim($row->BUNDLE_NO)]=trim($row->BUNDLE_NO);
        }

        foreach ($dtls_data as $val) 
        {
            $cutNo=$val->cut_no;
            $color_type_id=$val->color_type_id;
            $bundleNo =$val->bundle_no;
            $barcodeNo=$val->barcode_no;
            $orderId = $val->order_id;
            $gmtsitemId =$val->item_id;
            $countryId =$val->country_id;
            $colorId =$val->color_id;
            $sizeId =$val->size_id;
            $colorSizeId =$val->color_size_id;
            $qty = $val->qnty;
            $checkRescan=$val->is_rescan;
            if(! isset($duplicate_bundle[trim($bundleNo)]) )
            {
              $bundleCutArr[$bundleNo]=$cutNo;
              $color_type_arr[$bundleNo]=$color_type_id; 
              $is_rescan_arr[$bundleNo]=$checkRescan;
              $cutArr[$orderId][$gmtsitemId][$countryId]=$cutNo;
              if(isset($mstArr[$orderId][$gmtsitemId][$countryId]))
              $mstArr[$orderId][$gmtsitemId][$countryId] += $qty;
              else $mstArr[$orderId][$gmtsitemId][$countryId]  = $qty;
              $colorSizeArr[$bundleNo] = $orderId . "**" . $gmtsitemId . "**" . $countryId;
              if(isset($dtlsArr[$bundleNo]))
              $dtlsArr[$bundleNo] += $qty;
              else $dtlsArr[$bundleNo]  = $qty;
              $dtlsArrColorSize[$bundleNo] = $colorSizeId;
              $bundleRescanArr[$bundleNo]=$checkRescan;
              $bundleBarcodeArr[$bundleNo]=$barcodeNo;
            }

        } 
   
        if($response_obj->mode== "save")
        {
          

                foreach ($mstArr as $orderId => $orderData) 
                {
                    foreach ($orderData as $gmtsItemId => $gmtsItemIdData) 
                    {
                        foreach ($gmtsItemIdData as $countryId => $qty) 
                        {
                            $id=return_next_id_by_sequence("pro_gar_production_mst_seq","pro_garments_production_mst","","",0,"",0,0,0,0,0,0,0 );
                            
                            $mst_part_data = array(
                              'ID'   => $id,
                              'DELIVERY_MST_ID'  => $mst_id,
                              'CUT_NO'  =>  $cutArr[$orderId][$gmtsItemId][$countryId],
                              'COMPANY_ID'  =>  $cbo_company_name,
                              'GARMENTS_NATURE'        => 2,                 
                              'CHALLAN_NO'  =>  $challan_no,
                              'PO_BREAK_DOWN_ID'  => $orderId,
                              'ITEM_NUMBER_ID'  =>  $gmtsItemId,
                              'COUNTRY_ID'  => $countryId,
                              'PRODUCTION_SOURCE'  => $production_source,
                              'SERVING_COMPANY'  => $serving_company,
                              'LOCATION'  => $location_id,
                              'PRODUCTION_DATE'  => $production_date,
                              'PRODUCTION_QUANTITY'  => $qty,
                              'PRODUCTION_TYPE'  => $production_types,
                              'ENTRY_BREAK_DOWN_TYPE'  =>  3,                               
                              'REMARKS'  =>  $remarks  ,
                             
                              'FLOOR_ID'  =>  $floor_id  ,
                              'SEWING_LINE'  =>  $sewing_line  ,
                              'PROD_RESO_ALLO'  =>  1  ,
                              'ENTRY_FORM'  =>  $entry_forms  ,
                              'IS_TAB'  => 1,
                              'INSERTED_BY'  =>  $user_id  ,
                              'INSERT_DATE'  =>  $pc_date_time   
                              );
                          $mst_tbl_id=$this->insertData($mst_part_data, "pro_garments_production_mst");
                          $mstIdArr[$orderId][$gmtsItemId][$countryId] = $id;
                          if($mst_tbl_id && $production_types==5)
                          {
                            $this->db->query("update pro_garments_production_mst set production_hour=$txt_reporting_hour where id ='$id'");
                          }
                            
                        }
                    }
                }


                 foreach ($dtlsArr as $bundle_no => $qty)
                  {

                    $colorSizedData = explode("**", $colorSizeArr[$bundle_no]);
                    $gmtsMstId = $mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
                     $cut_no=$bundleCutArr[$bundle_no];
                     $color_type_ids=$color_type_arr[$bundle_no];
                     $is_rescan_id=$is_rescan_arr[$bundle_no];
                    $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls","","",0,"",0,0,0,0,0,0,0 );
                     

                    $dtls_part_data = array(
                              'ID'   => $dtls_id,
                              'DELIVERY_MST_ID'  => $mst_id,
                              'MST_ID'  =>  $gmtsMstId,
                              'PRODUCTION_TYPE'  =>  $production_types,
                              'COLOR_SIZE_BREAK_DOWN_ID'=> $dtlsArrColorSize[$bundle_no],                 
                              'PRODUCTION_QNTY'  =>  $qty,
                              'CUT_NO'  => $cut_no,
                              'BUNDLE_NO'  =>  $bundle_no,
                              'ENTRY_FORM'  => $entry_forms,
                              'BARCODE_NO'  =>  $bundleBarcodeArr[$bundle_no],
                              'IS_RESCAN'  =>  $is_rescan_id,
                              'COLOR_TYPE_ID'  => $color_type_ids   
                              );
                     $dtls_tbl_id=$this->insertData($dtls_part_data, "pro_garments_production_dtls");                 
                }



        }
        if($response_obj->mode == "update")
        {
            $this->db->query("delete from pro_garments_production_dtls where mst_id ='$update_id'");
        } 
          
        
        if ($this->db->trans_status() == TRUE) 
        {
          if($mst_tbl_id && $dtls_tbl_id)
          {
             $this->db->trans_commit();
             $this->db->trans_complete();
             return $resultset["status"] = "Successful";
          }
          else
          {
             $this->db->trans_rollback();
             $this->db->trans_complete();
             return $resultset["status"] = "Failed";
          }
          
        }
        else
        {
          $resultset["status"] = "Failed";
          $this->db->trans_complete();
        }
     }
    else
    {
      return $resultset["status"] = "Failed";
    }
}

 
}
