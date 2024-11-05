<?

header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");

include('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "sewing_operation_list_view") {
    $arr = array(2 => $unit_of_measurement, 3 => $production_resource);
    echo create_list_view("list_view", "Operation Name,Rate,UOM,Resources,Operator SMV,Helper SMV,Total SMV", "200,60,80,120,80,80", "750", "220", 1, "select operation_name,rate,uom,resource_sewing,operator_smv,helper_smv,total_smv,id from  lib_sewing_operation_entry where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,uom,resource_sewing", $arr, "operation_name,rate,uom,resource_sewing,operator_smv,helper_smv,total_smv", "../production/requires/sewing_operation_controller", 'setFilterGrid("list_view",-1);', '0,2,0,0,2,2,2');    
}


if ($action == "load_php_data_to_form") {
	$nameArray = sql_select("select operation_name,rate,uom,resource_sewing,operator_smv,helper_smv,total_smv,status_active,id from  lib_sewing_operation_entry where id='$data'");
    foreach ($nameArray as $inf) {
        echo "document.getElementById('txt_operation').value = '" . ($inf[csf("operation_name")]) . "';\n";
        echo "document.getElementById('txt_rate').value  = '" . ($inf[csf("rate")]) . "';\n";
        echo "document.getElementById('cbo_uom').value  = '" . ($inf[csf("uom")]) . "';\n";
        echo "document.getElementById('cbo_resource').value  = '" . ($inf[csf("resource_sewing")]) . "';\n";
        echo "document.getElementById('txt_operator_smv').value  = '" . ($inf[csf("operator_smv")]) . "';\n";
        echo "document.getElementById('txt_helper_smv').value  = '" . ($inf[csf("helper_smv")]) . "';\n";
        echo "document.getElementById('txt_total_smv').value  = '" . ($inf[csf("total_smv")]) . "';\n";
        echo "document.getElementById('cbo_status').value  = '" . ($inf[csf("status_active")]) . "';\n";
        echo "document.getElementById('update_id').value  = '" . ($inf[csf("id")]) . "';\n";
        echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_sewing_operation_entry',1);\n";
    }
}

if ($action == "save_update_delete") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    if ($operation == 0) {  // Insert Here
        if (is_duplicate_field("operation_name", "lib_sewing_operation_entry", " operation_name=$txt_operation and rate=$txt_rate and uom=$cbo_uom and resource_sewing=$cbo_resource and operator_smv=$txt_operator_smv and helper_smv=$txt_helper_smv and is_deleted=0") == 1) {
            echo "11**0";
            die;
        } else {
            $con = connect();
            if ($db_type == 0) {
                mysql_query("BEGIN");
            }

            $id = return_next_id("id", " lib_sewing_operation_entry", 1);
            $field_array = "id,operation_name,rate,uom,resource_sewing,operator_smv,helper_smv,total_smv,inserted_by,insert_date,status_active,is_deleted";
            $data_array = "(" . $id . "," . $txt_operation . "," . $txt_rate . "," . $cbo_uom . "," . $cbo_resource . "," . $txt_operator_smv . "," . $txt_helper_smv . "," . $txt_total_smv . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_status . ",0)";
            $rID = sql_insert("lib_sewing_operation_entry", $field_array, $data_array, 1);

            if ($db_type == 0) {
                if ($rID) {
                    mysql_query("COMMIT");
                    echo "0**" . $rID;
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**" . $rID;
                }
            }

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
        if (is_duplicate_field("operation_name", "lib_sewing_operation_entry", " operation_name=$txt_operation and rate=$txt_rate and uom=$cbo_uom and resource_sewing=$cbo_resource and operator_smv=$txt_operator_smv and helper_smv=$txt_helper_smv and is_deleted=0") == 1) {
            echo "11**0";
            die;
        } else {
            $con = connect();
            if ($db_type == 0) {
                mysql_query("BEGIN");
            }

            $field_array = "operation_name*rate*uom*resource_sewing*operator_smv*helper_smv*total_smv*updated_by*update_date*status_active*is_deleted";
            $data_array = "" . $txt_operation . "*" . $txt_rate . "*" . $cbo_uom . "*" . $cbo_resource . "*" . $txt_operator_smv . "*" . $txt_helper_smv . "*" . $txt_total_smv . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $cbo_status . "*0";


            $rID = sql_update("lib_sewing_operation_entry", $field_array, $data_array, "id", "" . $update_id . "", 1);

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
			
        }
    } else if ($operation == 2) {   // delete Here
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $field_array = "updated_by*update_date*status_active*is_deleted";
        $data_array = "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'0'*'1'";

        $rID = sql_delete("lib_sewing_operation_entry", $field_array, $data_array, "id", "" . $update_id . "", 1);

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