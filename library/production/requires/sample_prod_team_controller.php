<?

header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");

include('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];

if ($action == "sewing_operation_list_view") {
    $location_arr = return_library_array("select id, location_name from lib_location where status_active=1", "id", "location_name");
    
	$arr = array(1=>$project_type_arr,2 => $location_arr, 5 => $row_status);
	$sql ="select team_name,product_category,location_name,no_of_members,team_efficiency,status,id from  lib_sample_production_team where is_deleted=0";
    echo create_list_view("list_view", "Team Name,Product Category,Location,No of Member,Team Efficiency,Status", "200,150,100,60,60", "750", "220", 1, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,product_category,location_name,0,0,status", $arr, "team_name,product_category,location_name,no_of_members,team_efficiency,status", "requires/sample_prod_team_controller", 'setFilterGrid("list_view",-1);', '0,0,0,0,0,0');        
}

if ($action == "load_php_data_to_form") {
    // this query will fetch all data from the table into the view page
    $nameArray = sql_select("select id, team_name, location_name, no_of_members, team_efficiency, status,product_category,style_capacity,email from lib_sample_production_team where is_deleted=0 and id='$data'");
    foreach ($nameArray as $inf) {
        echo "document.getElementById('update_id').value = '" .$inf[csf("id")]. "';\n";
        echo "document.getElementById('txt_teamname').value  = '" . $inf[csf("team_name")] . "';\n";
        echo "document.getElementById('cbo_location').value  = '" . $inf[csf("location_name")] . "';\n";
        echo "document.getElementById('txt_no_of_member').value  = '" . $inf[csf("no_of_members")] . "';\n";
        echo "document.getElementById('txt_team_efficiency').value  = '" . $inf[csf("team_efficiency")] . "';\n";
        echo "document.getElementById('cbo_status').value  = '" . $inf[csf("status")]. "';\n";
		
        echo "document.getElementById('cbo_product_category').value  = '" . $inf[csf("product_category")]. "';\n";
        echo "document.getElementById('txt_style_capacity').value  = '" . $inf[csf("style_capacity")]. "';\n";
        echo "document.getElementById('txt_team_email').value  = '" . $inf[csf("email")]. "';\n";
		
		
        echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_sample_prduction',1);\n";
    }
}

if ($action == "save_update_delete") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    if ($operation == 0) {
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $id = return_next_id("id", "lib_sample_production_team", 1);
        $field_array = "id,team_name,location_name,no_of_members,team_efficiency,status,product_category,style_capacity,inserted_by,insert_date,email,status_active,is_deleted";
        $data_array = "(" . $id . "," . $txt_teamname . "," . $cbo_location . "," . $txt_no_of_member . "," . $txt_team_efficiency . "," . $cbo_status . "," .$cbo_product_category . "," . $txt_style_capacity . "," .$user_id . ",'" . $pc_date_time . "'," .  $txt_team_email . "," .$cbo_status . ",0)";
        
		//echo "10** insert into lib_sample_production_team $field_array value" . $data_array;die;
		
		
		$rID = sql_insert("lib_sample_production_team", $field_array, $data_array, 1);

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
            if ($rID) {
                oci_commit($con);
                echo "0**" . $rID;
            } else {
                oci_rollback($con);
                echo "10**" . $rID;
            }
        }
        disconnect($con);
        die;
        //}
    } else if ($operation == 1) { 
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $field_array = "team_name*location_name*no_of_members*team_efficiency*status*product_category*style_capacity*updated_by*update_date*email*status_active*is_deleted";
        $data_array = "" . $txt_teamname . "*" . $cbo_location . "*" . $txt_no_of_member . "*" . $txt_team_efficiency . "*" . $cbo_status . "*" . $cbo_product_category . "*" .$txt_style_capacity . "*" .$user_id . "*'" . $pc_date_time. "'*" .  $txt_team_email . "*" . $cbo_status . "*0";

         //echo "10**" ;print_r($data_array);die;
		
		
		$rID = sql_update("lib_sample_production_team", $field_array, $data_array, "id", "" . $update_id . "", 1);

        if ($db_type == 0) {
            if ($rID) {
                mysql_query("COMMIT");
                echo "1**" . str_replace("'", "", $update_id);
            } else {
                mysql_query("ROLLBACK");
                echo "10**" . str_replace("'", "", $update_id);
            }
        }
        if ($db_type == 2 || $db_type == 1) {
            if ($rID) {
                oci_commit($con);
                echo "1**" . str_replace("'", "", $update_id);
            } else {
                oci_rollback($con);
                echo "10**" . str_replace("'", "", $update_id);
            }
        }
        disconnect($con);
        die;
        //}
    } else if ($operation == 2) {   // delete Here
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $field_array = "updated_by*update_date*status_active*is_deleted";
        $data_array = "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'0'*'1'";

        $rID = sql_delete("lib_sample_production_team", $field_array, $data_array, "id", "" . $update_id . "", 1);

        if ($db_type == 0) {
            if ($rID) {
                mysql_query("COMMIT");
                echo "2**" . $rID;
            } else {
                mysql_query("ROLLBACK");
                echo "10**" . $rID;
            }
        }

        if ($db_type == 2 || $db_type == 1) {
            if ($rID) {
                oci_commit($con);
                echo "2**" . str_replace("'", "", $update_id);
            } else {
                oci_rollback($con);
                echo "10**" . str_replace("'", "", $update_id);
            }
        }

        disconnect($con);
    }
}
?>