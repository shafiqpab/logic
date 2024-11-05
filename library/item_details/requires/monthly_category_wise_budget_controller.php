<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    if ($operation==0)  // Insert Here
    {
            $all_category_id = explode(',', $categoryid);
            $all_category_id_unique = array_unique($all_category_id);
            if(count($all_category_id) != count($all_category_id_unique)){
                echo "20**Duplicate category not allowed in same applying period!";
                die;
            }

            $sql_check_existence = sql_select("select id from lib_category_budget_mst where company_id = $cbo_company_name and to_char(applying_date_from, 'dd-mm-YYYY') = '".change_date_format(str_replace("'", "", $txt_from_date))."' and to_char(applying_date_to, 'dd-mm-YYYY') = '".change_date_format(str_replace("'", "", $txt_to_date))."' and status_active = 1 and is_deleted = 0");
            if (count($sql_check_existence) > 0){
                echo "20**Already data exists for this company in this applying period!";
                die;
            }
            $con = connect();
            if($db_type==0)
            {
                mysql_query("BEGIN");
            }
            if(str_replace("'", "", $txt_system_id) != ""){
                $field_array_mst_update = "remarks*updated_user*updated_date";
                $data_array_mst_update = "" . $txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
                $update_id = str_replace("'", "", $txt_system_id);
                $sql_get_budgeted_cat = sql_select("select a.id, a.company_id, a.applying_date_from, a.applying_date_to, a.currency_id, b.category_id, b.budget_amount from lib_category_budget_mst a, lib_category_budget_dtls b  where a.id = b.mst_id  and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.id = $update_id");
                $budgeted_category_arr = [];
                foreach ($sql_get_budgeted_cat as $row){
                    $conversion_date=change_date_format($row[csf('applying_date_to')], "d-M-y", "-",1);
                    $currency_rate=set_conversion_rate($row[csf('currency_id')], $conversion_date, $row[csf('company_id')]);
                    $budgeted_category_arr[$row[csf('category_id')]] = $row[csf('budget_amount')]*$currency_rate; //convert bdt
                }
                $firstday = change_date_format($sql_get_budgeted_cat[0][csf('applying_date_from')]);
                $lastday = change_date_format($sql_get_budgeted_cat[0][csf('applying_date_to')]);
                $sql_get_cat_amount = sql_select("select a.CBO_CURRENCY, a.REQUISITION_DATE, b.ITEM_CATEGORY, sum(b.AMOUNT) as AMOUNT from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id = b.mst_id and a.COMPANY_ID = ".$sql_get_budgeted_cat[0][csf('company_id')]." and a.status_active = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0
                and a.REQUISITION_DATE BETWEEN to_date('$firstday', 'DD-MM-YY') and to_date('$lastday', 'DD-MM-YY') and b.item_category in (".implode(",", array_keys($budgeted_category_arr)).") group by a.CBO_CURRENCY, b.ITEM_CATEGORY, a.REQUISITION_DATE");
                $already_created_requ_amount = [];
                foreach ($sql_get_cat_amount as $row){
                    $conversion_date=change_date_format($row[csf('requisition_date')], "d-M-y", "-",1);
                    $currency_rate=set_conversion_rate($row[csf('cbo_currency')], $conversion_date, $sql_get_budgeted_cat[0][csf('company_id')]);
                    $already_created_requ_amount[$row[csf('ITEM_CATEGORY')]] += $row['AMOUNT']*$currency_rate;
                }
                $errorrRept55 = "";
                foreach (array_keys($budgeted_category_arr) as $val){
                    if(in_array($val, $all_category_id_unique)===false){
                        if(in_array($val, array_keys($already_created_requ_amount)) === true){
                            $errorrRept55 .= $general_item_category[$val].", ";
                        }
                    }
                }
                if($errorrRept55 != ""){
                    echo "55**Category (".trim($errorrRept55, ', ').") remove is not allowed, because already requisition created using (".trim($errorrRept55, ', ').")!";
                    die;
                }

                $field_array_dtls_insert = "id, mst_id, category_id, budget_amount, inserted_user, insert_date, status_active, is_deleted";
                $field_array_dtls_update = "category_id*budget_amount*updated_user*updated_date";
                $field_array_dtls_delete = "status_active*is_deleted*updated_user*updated_date";
                $dtlsRow_array = explode(',', $dtlrow);
                $data_array_dtls_insert = "";
                $get_all_id = return_library_array("select id, id as dtls_id from lib_category_budget_dtls where mst_id = $update_id and is_deleted = 0 and status_Active = 1", "id", "dtls_id");

                $dtls_id_insert = return_next_id("id", "LIB_CATEGORY_BUDGET_DTLS", 1);
                $deleted_id = []; $data_array_dtls_update = []; $data_array_dtls_delete = []; $errorrRept56 = "";
                foreach ($dtlsRow_array as $i){
                    $category = "cbo_category_name_".$i;
                    $amount = "txt_amount_".$i;
                    $dtls_update_id = "dtls_id_".$i;

                    if(str_replace("'", "", $$dtls_update_id) != ""){
                        if(in_array(str_replace("'", "", $$dtls_update_id), $get_all_id)){
                            $update_id_arr[]=str_replace("'", "", $$dtls_update_id);
                            $deleted_id[str_replace("'", "", $$dtls_update_id)] = str_replace("'", "", $$dtls_update_id);
                            $data_array_dtls_update[str_replace("'", "", $$dtls_update_id)]=explode("*",("".$$category."*".$$amount."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
                        }
                    }else{
                        if(str_replace("'", "", $$category) > 0 && str_replace("'", "", $$amount) > 0 ){
                            if ($data_array_dtls_insert != "") $data_array_dtls_insert .=",";
                            $data_array_dtls_insert .= "(". $dtls_id_insert .",". $update_id .",". $$category .",". $$amount ."," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
                            $dtls_id_insert += 1;
                        }
                    }
                    if($already_created_requ_amount[str_replace("'", "", $$category)] > str_replace("'", "", $$amount)){
                        $errorrRept56 .= $general_item_category[str_replace("'", "", $$category)].", ";
                    }
                }
                if($errorrRept56 != ""){
                    echo "56**Category (".trim($errorrRept56, ', ').") budget amount should be greater than already created requisition amount!";
                    die;
                }
                $rID3 = 1;
                $deleted_id_com = array_diff($get_all_id, $deleted_id);
                if(count($deleted_id_com) > 0){
                    foreach ($deleted_id_com as $id){
                        $delete_id_arr[]=$id;
                        $data_array_dtls_delete[$id]=explode("*",("0*1*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
                    }
                }

                $rID = sql_update("LIB_CATEGORY_BUDGET_MST", $field_array_mst_update, $data_array_mst_update, "id", "" .$update_id."");
                $rID1 = 1;
                if(count($data_array_dtls_update) > 0){
                    $rID1=execute_query(bulk_update_sql_statement("LIB_CATEGORY_BUDGET_DTLS","id",$field_array_dtls_update,$data_array_dtls_update,$update_id_arr),0);
                }
                $rID2 = 1;
                if($data_array_dtls_insert != ""){
                    $rID2 = sql_insert("LIB_CATEGORY_BUDGET_DTLS", $field_array_dtls_insert, $data_array_dtls_insert, 1);
                }
                if(count($data_array_dtls_delete) > 0){
                    $rID3=execute_query(bulk_update_sql_statement("LIB_CATEGORY_BUDGET_DTLS","id",$field_array_dtls_delete,$data_array_dtls_delete,$delete_id_arr),0);
                }

                if ($db_type == 0) {
                    if ($rID && $rID1 && $rID2 && $rID3) {
                        mysql_query("COMMIT");
                        echo "1**".$update_id;
                    } else {
                        mysql_query("ROLLBACK");
                        echo "10**0";
                    }
                }
                if ($db_type == 2 || $db_type == 1) {
                    if ($rID && $rID1 && $rID2 && $rID3) {
                        oci_commit($con);
                        echo "1**".$update_id;
                    } else {
                        oci_rollback($con);
                        echo "10**0";
                    }
                }
            }else {
                $sql_get_cat_amount = sql_select("select a.CBO_CURRENCY, a.REQUISITION_DATE, b.ITEM_CATEGORY, sum(b.AMOUNT) as AMOUNT from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id = b.mst_id and a.COMPANY_ID = $cbo_company_name and a.status_active = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0
                and a.REQUISITION_DATE BETWEEN to_date($txt_from_date, 'DD-MM-YY') and to_date($txt_to_date, 'DD-MM-YY') and b.item_category in (".implode(",", $all_category_id_unique).") group by a.CBO_CURRENCY, b.ITEM_CATEGORY, a.REQUISITION_DATE");

                $already_created_requ_amount = [];
                foreach ($sql_get_cat_amount as $row){
                    $conversion_date=change_date_format($row[csf('requisition_date')], "d-M-y", "-",1);
                    $currency_rate=set_conversion_rate($row[csf('cbo_currency')], $conversion_date, $sql_get_budgeted_cat[0][csf('company_id')]);
                    $already_created_requ_amount[$row[csf('ITEM_CATEGORY')]] += $row['AMOUNT']*$currency_rate;
                }
                $id = return_next_id("id", "LIB_CATEGORY_BUDGET_MST", 1);
                $field_array_mst = "id, company_id, applying_date_from, applying_date_to, currency_id, remarks, inserted_user, insert_date, status_active, is_deleted";
                $data_array_mst = "(" . $id . "," . $cbo_company_name . "," . $txt_from_date . "," . $txt_to_date . "," . $cbo_currency_name . "," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";

                $field_array_dtls = "id, mst_id, category_id, budget_amount, inserted_user, insert_date, status_active, is_deleted";
                $dtlsRow_array = explode(',', $dtlrow);
                $data_array_dtls = "";
                $dtls_id = return_next_id("id", "LIB_CATEGORY_BUDGET_DTLS", 1);
                foreach ($dtlsRow_array as $i) {
                    $category = "cbo_category_name_" . $i;
                    $amount = "txt_amount_" . $i;
                    if (str_replace("'", "", $$category) > 0 && str_replace("'", "", $$amount) > 0) {
                        if ($data_array_dtls != "") $data_array_dtls .= ",";
                        $data_array_dtls .= "(" . $dtls_id . "," . $id . "," . $$category . "," . $$amount . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
                        $dtls_id += 1;
                    }
                    if($already_created_requ_amount[str_replace("'", "", $$category)] > str_replace("'", "", $$amount)){
                        $errorrRept56 .= $general_item_category[str_replace("'", "", $$category)].", ";
                    }
                }
                if($errorrRept56 != ""){
                    echo "56**Category (".trim($errorrRept56, ', ').") budget amount should be greater than already created requisition amount!";
                    die;
                }
                $rID = sql_insert("LIB_CATEGORY_BUDGET_MST", $field_array_mst, $data_array_mst, 1);
                $rID1 = sql_insert("LIB_CATEGORY_BUDGET_DTLS", $field_array_dtls, $data_array_dtls, 1);

                if ($db_type == 0) {
                    if ($rID && $rID1) {
                        mysql_query("COMMIT");
                        echo "0**" . $id;
                    } else {
                        mysql_query("ROLLBACK");
                        echo "10**0";
                    }
                } else if ($db_type == 2 || $db_type == 1) {
                    if ($rID && $rID1) {
                        oci_commit($con);
                        echo "0**" . $id;
                    } else {
                        oci_rollback($con);
                        echo "10**0";
                    }
                }
            }
            disconnect($con);
            die;
    }
    elseif ($operation == 1)   // Update Here
    {
            $con = connect();
            if ($db_type == 0) {
                mysql_query("BEGIN");
            }
            $all_category_id = explode(',', $categoryid);
            $all_category_id_unique = array_unique($all_category_id);
            if(count($all_category_id) != count($all_category_id_unique)){
                echo "20**Duplicate category not allowed in same applying period!";
                die;
            }
            $update_id = str_replace("'", "", $txt_system_id);
            $sql_get_budgeted_cat = sql_select("select a.id, a.company_id, a.applying_date_from, a.applying_date_to, a.currency_id, b.category_id, b.budget_amount from lib_category_budget_mst a, lib_category_budget_dtls b  where a.id = b.mst_id  and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.id = $update_id");
            $budgeted_category_arr = [];
            foreach ($sql_get_budgeted_cat as $row){
                $conversion_date=change_date_format($row[csf('applying_date_to')], "d-M-y", "-",1);
                $currency_rate=set_conversion_rate($row[csf('currency_id')], $conversion_date, $row[csf('company_id')]);
                $budgeted_category_arr[$row[csf('category_id')]] = $row[csf('budget_amount')]*$currency_rate; //convert bdt
            }
            $firstday = change_date_format($sql_get_budgeted_cat[0][csf('applying_date_from')]);
            $lastday = change_date_format($sql_get_budgeted_cat[0][csf('applying_date_to')]);
            $sql_get_cat_amount = sql_select("select a.CBO_CURRENCY, a.REQUISITION_DATE, b.ITEM_CATEGORY, sum(b.AMOUNT) as AMOUNT from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id = b.mst_id and a.COMPANY_ID = ".$sql_get_budgeted_cat[0][csf('company_id')]." and a.status_active = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0
                and a.REQUISITION_DATE BETWEEN to_date('$firstday', 'DD-MM-YY') and to_date('$lastday', 'DD-MM-YY') and b.item_category in (".implode(",", array_keys($budgeted_category_arr)).") group by a.CBO_CURRENCY, b.ITEM_CATEGORY, a.REQUISITION_DATE");
            $already_created_requ_amount = [];
            foreach ($sql_get_cat_amount as $row){
                $conversion_date=change_date_format($row[csf('requisition_date')], "d-M-y", "-",1);
                $currency_rate=set_conversion_rate($row[csf('cbo_currency')], $conversion_date, $sql_get_budgeted_cat[0][csf('company_id')]);
                $already_created_requ_amount[$row[csf('ITEM_CATEGORY')]] += $row['AMOUNT']*$currency_rate;
            }
            $errorrRept55 = "";
            foreach (array_keys($budgeted_category_arr) as $val){
                if(in_array($val, $all_category_id_unique)===false){
                    if(in_array($val, array_keys($already_created_requ_amount)) === true){
                        $errorrRept55 .= $general_item_category[$val].", ";
                    }
                }
            }
            if($errorrRept55 != ""){
                echo "55**Category (".trim($errorrRept55, ', ').") remove is not allowed, because already requisition created using (".trim($errorrRept55, ', ').")!";
                die;
            }

            $field_array_mst_update = "remarks*updated_user*updated_date";
            $data_array_mst_update = "" . $txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

            $field_array_dtls_insert = "id, mst_id, category_id, budget_amount, inserted_user, insert_date, status_active, is_deleted";
            $field_array_dtls_update = "category_id*budget_amount*updated_user*updated_date";
            $field_array_dtls_delete = "status_active*is_deleted*updated_user*updated_date";
            $dtlsRow_array = explode(',', $dtlrow);
            $data_array_dtls_insert = "";
            $get_all_id = return_library_array("select id, id as dtls_id from lib_category_budget_dtls where mst_id = $update_id and is_deleted = 0 and status_Active = 1", "id", "dtls_id");

            $dtls_id_insert = return_next_id("id", "LIB_CATEGORY_BUDGET_DTLS", 1);
            $deleted_id = []; $data_array_dtls_update = []; $data_array_dtls_delete = []; $errorrRept56 = "";
            foreach ($dtlsRow_array as $i){
                $category = "cbo_category_name_".$i;
                $amount = "txt_amount_".$i;
                $dtls_update_id = "dtls_id_".$i;

                if(str_replace("'", "", $$dtls_update_id) != ""){
                     if(in_array(str_replace("'", "", $$dtls_update_id), $get_all_id)){
                         $update_id_arr[]=str_replace("'", "", $$dtls_update_id);
                         $deleted_id[str_replace("'", "", $$dtls_update_id)] = str_replace("'", "", $$dtls_update_id);
                         $data_array_dtls_update[str_replace("'", "", $$dtls_update_id)]=explode("*",("".$$category."*".$$amount."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
                     }
                }else{
                    if(str_replace("'", "", $$category) > 0 && str_replace("'", "", $$amount) > 0 ){
                        if ($data_array_dtls_insert != "") $data_array_dtls_insert .=",";
                        $data_array_dtls_insert .= "(". $dtls_id_insert .",". $update_id .",". $$category .",". $$amount ."," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
                        $dtls_id_insert += 1;
                    }
                }
                if($already_created_requ_amount[str_replace("'", "", $$category)] > str_replace("'", "", $$amount)){
                    $errorrRept56 .= $general_item_category[str_replace("'", "", $$category)].", ";
                }
            }
            if($errorrRept56 != ""){
                echo "56**Category (".trim($errorrRept56, ', ').") budget amount should be greater than already created requisition amount!";
                die;
            }

            $rID3 = 1;
            $deleted_id_com = array_diff($get_all_id, $deleted_id);
            if(count($deleted_id_com) > 0){
               foreach ($deleted_id_com as $id){
                   $delete_id_arr[]=$id;
                   $data_array_dtls_delete[$id]=explode("*",("0*1*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
               }
            }

            $rID = sql_update("LIB_CATEGORY_BUDGET_MST", $field_array_mst_update, $data_array_mst_update, "id", "" .$update_id."");
            $rID1 = 1;
            if(count($data_array_dtls_update) > 0){
                $rID1=execute_query(bulk_update_sql_statement("LIB_CATEGORY_BUDGET_DTLS","id",$field_array_dtls_update,$data_array_dtls_update,$update_id_arr),0);
            }
            $rID2 = 1;
            if($data_array_dtls_insert != ""){
                $rID2 = sql_insert("LIB_CATEGORY_BUDGET_DTLS", $field_array_dtls_insert, $data_array_dtls_insert, 1);
            }
            if(count($data_array_dtls_delete) > 0){
                $rID3=execute_query(bulk_update_sql_statement("LIB_CATEGORY_BUDGET_DTLS","id",$field_array_dtls_delete,$data_array_dtls_delete,$delete_id_arr),0);
            }

            if ($db_type == 0) {
                if ($rID && $rID1 && $rID2 && $rID3) {
                    mysql_query("COMMIT");
                    echo "1**".$update_id;
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**0";
                }
            }
            if ($db_type == 2 || $db_type == 1) {
                if ($rID && $rID1 && $rID2 && $rID3) {
                    oci_commit($con);
                    echo "1**".$update_id;
                } else {
                    oci_rollback($con);
                    echo "10**0";
                }
            }
            disconnect($con);
            die;
    }
    elseif ($operation == 2)   // Delete Here
    {
        $update_id = str_replace("'", "", $txt_system_id);
        $field_array_mst_update = 'status_active*is_deleted';
        $data_array_mst_update = '0*1';
        $rID = sql_update("LIB_CATEGORY_BUDGET_MST", $field_array_mst_update, $data_array_mst_update, "id", $update_id,1);
        $rID1 = sql_update("LIB_CATEGORY_BUDGET_DTLS", $field_array_mst_update, $data_array_mst_update, "mst_id", $update_id,1);
        if ($db_type == 0) {
            if ($rID && $rID1) {
                mysql_query("COMMIT");
                echo "2**1";
            } else {
                mysql_query("ROLLBACK");
                echo "10**0";
            }
        }
        if ($db_type == 2 || $db_type == 1) {
            if ($rID && $rID1) {
                oci_commit($con);
                echo "2**1";
            } else {
                oci_rollback($con);
                echo "10**0";
            }
        }
        disconnect($con);
        die;
    }
}

if($action == "load_php_data_to_form"){
    $sql_data = sql_select("SELECT id, company_id, to_char(applying_date_from, 'dd-mm-YYYY') as applying_date_from, to_char(applying_date_to, 'dd-mm-YYYY') as applying_date_to, currency_id, remarks from LIB_CATEGORY_BUDGET_MST where id = $data and is_deleted=0 and status_active = 1  order by id desc");
    if(count($sql_data) > 0){
        echo "$('#txt_system_id').val(".$sql_data[0][csf('id')].");\n";
        echo "$('#cbo_company_name').val(".$sql_data[0][csf('company_id')].");\n";
        echo "$('#cbo_currency_name').val(".$sql_data[0][csf('currency_id')].");\n";
        echo "$('#txt_from_date').val('".$sql_data[0][csf('applying_date_from')]."');\n";
        echo "$('#txt_to_date').val('".$sql_data[0][csf('applying_date_to')]."');\n";
        echo "$('#txt_remarks').val('".$sql_data[0][csf('remarks')]."');\n";
        echo "disable_fields('cbo_company_name*txt_from_date*txt_to_date');\n";
        echo "set_button_status(1, permission, 'fnc_category_wise_budget',1);\n";
    }
    $firstday = change_date_format($sql_data[0][csf('applying_date_from')]);
    $lastday = change_date_format($sql_data[0][csf('applying_date_to')]);

    $sql_data_dtls = sql_select("select id, category_id, budget_amount from lib_category_budget_dtls where status_active = 1 and is_deleted = 0 and mst_id = $data order by id");
    //echo "select id, category_id, budget_amount from lib_category_budget_dtls where status_ative = 1 and is_deleted = 0 and mst_id = $data";
    $all_category = [];
    foreach ($sql_data_dtls as $k => $row){
        $all_category[$row[csf('category_id')]] = $row[csf('category_id')];
    }
    $sql_get_cat_amount = sql_select("select b.ITEM_CATEGORY from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id = b.mst_id and a.COMPANY_ID = ".$sql_data[0][csf('company_id')]." and a.status_active = 1 and a.IS_DELETED = 0 and b.STATUS_ACTIVE = 1 and b.IS_DELETED = 0
                and a.REQUISITION_DATE BETWEEN to_date('$firstday', 'DD-MM-YY') and to_date('$lastday', 'DD-MM-YY') and b.item_category in (".implode(",", $all_category).") group by b.ITEM_CATEGORY");
    $already_created_requ_amount = [];
    foreach ($sql_get_cat_amount as $row){
        $already_created_requ_amount[$row[csf('ITEM_CATEGORY')]] = $row[csf('ITEM_CATEGORY')];
    }
    $data_appender = "";
    if(count($sql_data_dtls) > 0){
        $i = 1;
        foreach ($sql_data_dtls as $k => $row){
            $data_appender .= '<tr id="row_'.$i.'" class="row"><td align="center" class="sl_col">'.$i.'</td>';
            if(in_array($row[csf('category_id')], $already_created_requ_amount)) {
                $data_appender .= '<td align="center">' . create_drop_down("cbo_category_name_$i", 190, $general_item_category, 0, 1, "-- Select Category --", $row[csf('category_id')], "", 1) . '</td>';
            }else{
                $data_appender .= '<td align="center">' . create_drop_down("cbo_category_name_$i", 190, $general_item_category, "", 1, "-- Select Category --", $row[csf('category_id')], "") . '</td>';
            }
            $data_appender .= '<td align="center"><input type="text" name="txt_amount_'.$i.'" id="txt_amount_'.$i.'" style="width:180px" class="text_boxes_numeric"  value="'.$row[csf('budget_amount')].'" placeholder="Write"/></td>';
            $data_appender .= '<td align="center"><input type="hidden" name="dtls_id_'.$i.'" id="dtls_id_'.$i.'" value="'.$row[csf('id')].'">';
            if(in_array($row[csf('category_id')], $already_created_requ_amount)) {
                if (count($sql_data_dtls) == 1) {
                    $data_appender .= '<input type="button" id="increase_' . $i . '" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onclick="fnc_addRow(' . $i . ')">';
                    $data_appender .= '<input type="button" id="decrease_' . $i . '" name="decrease[]" style="width:30px; cursor: not-allowed; opacity: .4;" class="formbuttonplasminus" value="-">';
                } else if (count($sql_data_dtls) == $i) {
                    $data_appender .= '<input type="button" id="increase_' . $i . '" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onclick="fnc_addRow(' . $i . ')">';
                    $data_appender .= '<input type="button" id="decrease_' . $i . '" name="decrease[]" style="width:30px; cursor: not-allowed; opacity: .4;" class="formbuttonplasminus" value="-" >';
                } else {
                    $data_appender .= '<input type="button" id="increase_' . $i . '" name="increase[]" style="width:30px; cursor: not-allowed; opacity: .4;" class="formbuttonplasminus" value="+" onclick="fnc_addRow(' . $i . ')">';
                    $data_appender .= '<input type="button" id="decrease_' . $i . '" name="decrease[]" style="width:30px; cursor: not-allowed; opacity: .4;" class="formbuttonplasminus" value="-" >';
                }
            }else{
                if (count($sql_data_dtls) == 1) {
                    $data_appender .= '<input type="button" id="increase_' . $i . '" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onclick="fnc_addRow(' . $i . ')">';
                    $data_appender .= '<input type="button" id="decrease_' . $i . '" name="decrease[]" style="width:30px; cursor: not-allowed; opacity: .4;" class="formbuttonplasminus" value="-" onclick="fnc_removeRow(' . $i . ')">';
                } else if (count($sql_data_dtls) == $i) {
                    $data_appender .= '<input type="button" id="increase_' . $i . '" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onclick="fnc_addRow(' . $i . ')">';
                    $data_appender .= '<input type="button" id="decrease_' . $i . '" name="decrease[]" style="width:30px;" class="formbuttonplasminus" value="-" onclick="fnc_removeRow(' . $i . ')">';
                } else {
                    $data_appender .= '<input type="button" id="increase_' . $i . '" name="increase[]" style="width:30px; cursor: not-allowed; opacity: .4;" class="formbuttonplasminus" value="+" onclick="fnc_addRow(' . $i . ')">';
                    $data_appender .= '<input type="button" id="decrease_' . $i . '" name="decrease[]" style="width:30px;" class="formbuttonplasminus" value="-" onclick="fnc_removeRow(' . $i . ')">';
                }

            }
            $data_appender .='</td></tr>';
            $i++;
        }
    }else{
        $i = 1;
        $data_appender .= '<tr id="row_'.$i.'" class="row"><td align="center" class="sl_col">'.$i.'</td>';
        $data_appender .= '<td align="center">'.create_drop_down( "cbo_category_name_$i", 190, $general_item_category, "", 1, "-- Select Category", "", "").'</td>';
        $data_appender .= '<td align="center"><input type="text" name="txt_amount_'.$i.'" id="txt_amount_'.$i.'" style="width:180px" class="text_boxes_numeric"  value="" placeholder="Write"/></td>';
        $data_appender .= '<td align="center"><input type="hidden" name="dtls_id_'.$i.'" id="dtls_id_'.$i.'" value="">';
        $data_appender .= '<input type="button" id="increase_'.$i.'" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onclick="fnc_addRow('.$i.')">';
        $data_appender .= '<input type="button" id="decrease_'.$i.'" name="decrease[]" style="width:30px; cursor: not-allowed; opacity: .4;" class="formbuttonplasminus" value="-" onclick="fnc_removeRow('.$i.')">';
        $data_appender .='</td></tr>';
    }
    echo  "$('#table_body_1').html('".$data_appender."');\n";
    exit();
}


if ($action == "budget_list_view"){
    $company_name=return_library_array("select id, company_name from lib_company where status_active = 1 and is_deleted = 0","id","company_name");
    $arr = array(0=>$company_name, 4=>$currency);
    echo  create_list_view ( "list_view", "Company, Year, Period From, Period To, Currency", "140,80,100,100,100","570","220",0, "SELECT id, company_id, to_char(insert_date, 'YYYY') as insert_year, to_char(applying_date_from, 'dd-mm-YYYY') as applying_date_from, to_char(applying_date_to, 'dd-mm-YYYY') as applying_date_to, currency_id from LIB_CATEGORY_BUDGET_MST where is_deleted = 0 and status_active = 1 order by id desc", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,0,0,0,currency_id", $arr , "company_id,insert_year,applying_date_from,applying_date_to,currency_id", "requires/monthly_category_wise_budget_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0' ) ;
    exit();
}
