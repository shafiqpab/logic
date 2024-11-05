<?

header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");

include('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "complexity_level_list_view") {
    $arr = array(4=> $row_status);
     echo create_list_view("list_view", "Level,First Day,Increment,Target,Status", "200,100,100,100,50", "650", "220", 1, "select level_type,first_day,increment_type,target,id,status from  lib_complexity_level where status_active=1 and  is_deleted=0 order by id asc", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,0,status", $arr, "level_type,first_day,increment_type,target,status", "../planning/requires/complexity_level_controller", 'setFilterGrid("list_view",-1);', '0,0,0,0,0');   
}
//function create_list_view($table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr, $show_sl, $field_printed_from_array_arr, $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all, $new_conn) {}
if ($action == "load_php_data_to_form") {
    
	
	$nameArray = sql_select("select level_type,first_day,increment_type,target,id,status from lib_complexity_level where id='$data'");
    foreach ($nameArray as $inf) {
        echo "document.getElementById('txt_level_type').value = '" . ($inf[csf("level_type")]) . "';\n";
        echo "document.getElementById('txt_first_day').value  = '" . ($inf[csf("first_day")]) . "';\n";
        echo "document.getElementById('txt_increment_type').value  = '" . ($inf[csf("increment_type")]) . "';\n";
        echo "document.getElementById('txt_target').value  = '" . ($inf[csf("target")]) . "';\n";
        echo "document.getElementById('cbo_status').value  = '" . ($inf[csf("status")]) . "';\n";
        echo "document.getElementById('update_id').value  = '" . ($inf[csf("id")]) . "';\n";
        echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_complexity_level',1);\n";
    }
}

if ($action == "save_update_delete") {
    //print_r($_POST);die;
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));
    //print_r($process);die;
    if ($operation == 0) {  // Insert Here
        if (is_duplicate_field("level_type", "lib_complexity_level", " level_type=$txt_level_type and first_day=$txt_first_day and increment_type=$txt_increment_type and target=$txt_target and is_deleted=0") == 1) {
            echo "11**0";
            die;
        } 
        else 
        {
            $con = connect();
            if ($db_type == 0) {
                mysql_query("BEGIN");
            }

            $id = return_next_id("id", " lib_complexity_level", 1);
            $field_array = "id,level_type,first_day,increment_type,target,inserted_by,inserted_date,status,status_active,is_deleted";
            $data_array = "(" . $id . "," . $txt_level_type . "," . $txt_first_day . "," . $txt_increment_type. "," . $txt_target . ","  . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_status . ",1,0)";
            //echo "10**INSERT INTO lib_complexity_level(".$field_array.") VALUES ".$data_array;die;
            $rID = sql_insert("lib_complexity_level", $field_array, $data_array, 0);
         //echo "$rID";
            if ($db_type == 0) {
                if ($rID) {
                    mysql_query("COMMIT");
                    echo "0**" . $rID;
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**" . $rID;
                }
            }
          //echo "$rID";
            if ($db_type == 2 || $db_type == 1) {
				 if($rID )
					{
						oci_commit($con);   
						echo "0**".$rID;
					}
				else{
						oci_rollback($con);
						echo "10**".$rID;
					}
            }
            disconnect($con);
            die;
        }
    } else if ($operation == 1) {   // Update Here
        // if (is_duplicate_field("level_type", "lib_complexity_level", " level_type=$txt_level_type and first_day=$txt_first_day and increment_type=$txt_increment_type and target=$txt_target and is_deleted=0") == 1) {
        //     echo "11**0";
        //     die;
       
            $con = connect();
            if ($db_type == 0) {
                mysql_query("BEGIN");
            }

            $field_array = "level_type*first_day*increment_type*target*updated_by*updated_date*status";
            $data_array = "" . $txt_level_type . "*" . $txt_first_day . "*" . $txt_increment_type . "*" . $txt_target . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $cbo_status . "";

            //echo "10**UPDATE INTO lib_complexity_level(".$field_array.") VALUES ".$data_array;die;
            $rID = sql_update("lib_complexity_level", $field_array, $data_array, "id", "" . $update_id . "", 0);
            //echo "10**".$rID_del;die;
            if ($db_type == 0) {
                if ($rID) {
                    mysql_query("COMMIT");
                    echo "1**" . $rID;
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**" . $rID;
                }
            }
            
            if ($db_type == 2 || $db_type == 1) {
			 if($rID )
			    {
					oci_commit($con);   
					echo "1**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			
       // }
    } else if ($operation == 2) {   // delete Here
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $field_array = "updated_by*updated_date*status_active*is_deleted";
        $data_array = "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'0'*'1'";

        $rID = sql_delete("lib_complexity_level", $field_array, $data_array, "id", "" . $update_id . "", 0);

        if ($db_type == 0) {
            if ($rID) {
                mysql_query("COMMIT");
                echo "1**" . $rID;
            } else {
                mysql_query("ROLLBACK");
                echo "10**" . $rID;
            }
        }
        
        if ($db_type == 2 || $db_type == 1) {
			 if($rID )
			    {
					oci_commit($con);   
					echo "2**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
		disconnect($con);
    }
}

?>