<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
include('../../../includes/common.php');
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$permission = $_SESSION['page_permission'];

if ($action == "save_update_delete") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    if ($operation == 0)  // Insert Here
    {
        if (is_duplicate_field("company_id", "mm_yr_fab_budget_mst", "company_id=$cbo_company_name and year=$cbo_budgeted_year and team_leader=$cbo_team_leader and location=$cbo_location_name  and is_deleted=0") == 1) {
            echo "11**0";
            die;
        }

        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $id = return_next_id("id", "mm_yr_fab_budget_mst", 1);
        $dtls_id = return_next_id("id", "mm_yr_fab_budget_dtls", 1);
        $break_down_id = return_next_id("id", "mm_yr_fab_budget_brkdwn", 1);

        $field_array = "id, company_id, location, team_leader, year, starting_month, inserted_by, insert_date, status_active, is_deleted";
        $field_array_dtls = "id, mst_id, buyer_id, budget_qty, inserted_by, insert_date, status_active, is_deleted";
        $field_array_break_down = "id, mst_id, dtls_id, buyer_id, month, year, budget_qty, inserted_by, insert_date, status_active, is_deleted";
        $data_array = "(" . $id . "," . $cbo_company_name . "," . $cbo_location_name . "," . $cbo_team_leader . "," . $cbo_budgeted_year . "," . $cbo_starting_month . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";

        $month_break_down_data = explode("___", $month_br);


        $year_total_data = explode(",", $year_total);
        $data_array_dtls = "";
        $data_array_break_down = "";
        foreach ($year_total_data as $buyer_data) {
            $buyer_wise_data = explode("*", $buyer_data);
            $buyer_id = $buyer_wise_data[0];
            $buyer_tot_budget_qty = $buyer_wise_data[1];

            if ($buyer_tot_budget_qty > 0) {
                if ($data_array_dtls != "") $data_array_dtls .= ',';
                $data_array_dtls .= "(" . $dtls_id . "," . $id . "," . $buyer_id . "," . $buyer_tot_budget_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";

                foreach ($month_break_down_data as $buyer_data) {
                    $buyer_wise_break_data = explode("#", $buyer_data);
                    $buyer_break_id = $buyer_wise_break_data[0];
                    $month_wise_budget_qty = explode("*", $buyer_wise_break_data[1]);

                    if ($buyer_id == $buyer_break_id) {
                        foreach ($month_wise_budget_qty as $month_data) {
                            $month_info = explode("_", $month_data);
                            $year = $month_info[0];
                            $month = $month_info[1];
                            $month_qty = $month_info[2];
                            if ($month_qty > 0) {
                                if ($data_array_break_down != "") $data_array_break_down .= ',';
                                $data_array_break_down .= "(" . $break_down_id . "," . $id . "," . $dtls_id . "," . $buyer_break_id . "," . $month . "," . $year . "," . $month_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
                                $break_down_id++;
                            }
                        }
                    }
                }
                $dtls_id++;
            }
        }

        $rID = sql_insert("mm_yr_fab_budget_mst", $field_array, $data_array, 1);
        $rID2 = $rID3 = true;
        if ($data_array_dtls != "") $rID2 = sql_insert("mm_yr_fab_budget_dtls", $field_array_dtls, $data_array_dtls, 1);
        if ($data_array_break_down) $rID3 = sql_insert("mm_yr_fab_budget_brkdwn", $field_array_break_down, $data_array_break_down, 1);
        // echo "10**".$rID .'&&'. $rID2 .'&&'. $rID3;
        // echo "10**INSERT INTO mm_yr_fab_budget_mst (".$field_array.") VALUES ".$data_array;die;
        if ($db_type == 0) {
            if ($rID && $rID2 && $rID3) {
                mysql_query("COMMIT");
                echo "0**" . $id;
            } else {
                mysql_query("ROLLBACK");
                echo "10**" . $id;
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($rID && $rID2 && $rID3) {
                oci_commit($con);
                echo "0**" . $id;
            } else {
                oci_rollback($con);
                echo "10**" . $id;
            }
        }
        disconnect($con);
        die;
    } else if ($operation == 1)   // Update Here
    {
        /* if (is_duplicate_field( "company_id", "mm_yr_fab_budget_mst", "company_id=$cbo_company_name and year=$cbo_budgeted_year and id!=$update_id and team_leader=$cbo_team_leader and location=$cbo_location_name and is_deleted=0 and id<>$update_id" ) == 1)
		{
			echo "11**0"; die;
		} */

        $con = connect();

        //$id=return_next_id( "id", "mm_yr_fab_budget_mst", 1 );
        $dtls_id = return_next_id("id", "mm_yr_fab_budget_dtls", 1);
        $break_down_id = return_next_id("id", "mm_yr_fab_budget_brkdwn", 1);

        $field_array_update = "location*team_leader*updated_by*update_date*status_active*is_deleted";
        $data_array_update = "" . $cbo_location_name . "*" . $cbo_team_leader . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . "1" . "*0";

        $field_array_dtls = "id, mst_id, buyer_id, budget_qty, inserted_by, insert_date, status_active, is_deleted";
        $field_array_break_down = "id, mst_id, dtls_id, buyer_id, month, year, budget_qty, inserted_by, insert_date, status_active, is_deleted";
        $data_array = "(" . $id . "," . $cbo_company_name . "," . $cbo_location_name . "," . $cbo_team_leader . "," . $cbo_budgeted_year . "," . $cbo_starting_month . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";

        //print_r($month_br);
        $month_break_down_data = explode("___", $month_br);


        $year_total_data = explode(",", $year_total);
        $data_array_dtls = "";
        $data_array_break_down = "";
        foreach ($year_total_data as $buyer_data) {
            $buyer_wise_data = explode("*", $buyer_data);
            $buyer_id = $buyer_wise_data[0];
            $buyer_tot_budget_qty = $buyer_wise_data[1];

            if ($buyer_tot_budget_qty > 0) {
                if ($data_array_dtls != "") $data_array_dtls .= ',';
                $data_array_dtls .= "(" . $dtls_id . "," . $update_id . "," . $buyer_id . "," . $buyer_tot_budget_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";

                foreach ($month_break_down_data as $buyer_data) {
                    $buyer_wise_break_data = explode("#", $buyer_data);
                    $buyer_break_id = $buyer_wise_break_data[0];
                    $month_wise_budget_qty = explode("*", $buyer_wise_break_data[1]);

                    // echo "10**".$buyer_id."=".$buyer_break_id;
                    // print_r($month_wise_budget_qty);die;

                    if ($buyer_id == $buyer_break_id) {
                        foreach ($month_wise_budget_qty as $month_data) {
                            $month_info = explode("_", $month_data);
                            $year = $month_info[0];
                            $month = $month_info[1];
                            $month_qty = $month_info[2];
                            if ($month_qty > 0) {
                                if ($data_array_break_down != "") $data_array_break_down .= ',';
                                $data_array_break_down .= "(" . $break_down_id . "," . $update_id . "," . $dtls_id . "," . $buyer_break_id . "," . $month . "," . $year . "," . $month_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
                                $break_down_id++;
                            }
                        }
                    }
                }
                $dtls_id++;
            }
        }
        //echo "10**".$data_array_break_down;die;
        $rID = sql_update("mm_yr_fab_budget_mst", $field_array_update, $data_array_update, "id", "" . $update_id . "", 1);
        $rID3 = $rID4 = true;
        $rID1 = execute_query("delete from mm_yr_fab_budget_dtls where mst_id =" . $update_id . "", 0);
        $rID2 = execute_query("delete from mm_yr_fab_budget_brkdwn where mst_id =" . $update_id . "", 0);
        if ($data_array_dtls != "") $rID3 = sql_insert("mm_yr_fab_budget_dtls", $field_array_dtls, $data_array_dtls, 0);
        if ($data_array_break_down) $rID4 = sql_insert("mm_yr_fab_budget_brkdwn", $field_array_break_down, $data_array_break_down, 0);

        
        // echo "10**INSERT INTO mm_yr_fab_budget_mst (".$field_array.") VALUES ".$data_array;die;

        if ($db_type == 0) {
            if ($rID && $rID1 && $rID2 && $rID3 && $rID4) {
                mysql_query("COMMIT");
                echo "1**" . $update_id;
            } else {
                mysql_query("ROLLBACK");
                echo "10**" . $update_id;
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($rID && $rID1 && $rID2 && $rID3 && $rID4) {
                oci_commit($con);
                echo "1**" . $update_id;
            } else {
                oci_rollback($con);
                echo "10**" . $update_id;
            }
        }
        disconnect($con);
        die;
    }
}


// for location dropdown against company 
if ($action == "load_drop_down_location") {
    echo create_drop_down("cbo_location_name", 150, "select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name", 'id,location_name', 1, '--- Select Location ---', 0,);
    exit();
}


// for sales starting month against company 
if ($action == "select_sales_starting_month") {
    $nameArray = sql_select("select sales_year_started,id from  variable_order_tracking where company_name='$data' and variable_list=12 order by id");
    //echo $nameArray;
    //echo $data;
    if (count($nameArray) > 0) $is_update = 1;
    else $is_update = 0;
    // echo $nameArray[0][csf('sales_year_started')];

    echo create_drop_down("cbo_starting_month", 150, $months, '', 0, '---- Select ----', $nameArray[0][csf('sales_year_started')], "", 1);
    exit();
}



// for the buyer wise table 
if ($action == "show_budget_list_buyer_wise") {

    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));
    // echo "action found";
    $company_id = str_replace("'", "", $company_id);
    $budget_year = str_replace("'", "", $budget_year);
    $location = str_replace("'", "", $location);
    $team_leader = str_replace("'", "", $team_leader);
    // echo gettype($company_id);
    // echo gettype($company_name );
    $buyer_arr = return_library_array("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id", "buyer_name");

    $sql = "select a.id as MST_ID, b.id as DTLS_ID, b.BUYER_ID, b.BUDGET_QTY from mm_yr_fab_budget_mst a, mm_yr_fab_budget_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.location=$location and a.team_leader=$team_leader and a.year=$budget_year and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    $sql_res = sql_select($sql);
    //echo count($sql_res).jahid;
    $buyer_wise_budget_qty_arr = array();
    $mst_id='';
    foreach ($sql_res as $row) {
        $buyer_wise_budget_qty_arr[$row['BUYER_ID']]['budget_qty'] = $row['BUDGET_QTY'];
        $buyer_wise_budget_qty_arr[$row['BUYER_ID']]['dtls_id'] = $row['DTLS_ID'];
        $mst_id=$row['MST_ID'];
    }
    //querry for previous year data

    $sql_break = "select b.BUYER_ID, b.MONTH, b.YEAR, b.BUDGET_QTY from mm_yr_fab_budget_mst a, MM_YR_FAB_BUDGET_BRKDWN b where a.id=b.mst_id and a.id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    $sql_break_res = sql_select($sql_break);
    //echo count($sql_res).jahid;
    $buyer_wise_break_data_arr = array();
    foreach ($sql_break_res as $row) {
        $buyer_wise_break_data_arr[$row['BUYER_ID']] .= $row['YEAR'].'_'.$row['MONTH'].'_'.$row['BUDGET_QTY'].'*';
    }

    $previous_year = $budget_year - 1;
    $sql_pre = "select b.id as DTLS_ID, b.BUYER_ID, b.BUDGET_QTY from mm_yr_fab_budget_mst a, mm_yr_fab_budget_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.location=$location and a.team_leader=$team_leader and a.year=$previous_year and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    $sql_pre_res = sql_select($sql_pre);
    // echo $sql_pre;
    $buyer_wise_budget_qty_arr_pre = array();
    foreach ($sql_pre_res as $row) {
        $buyer_wise_budget_qty_arr_pre[$row['BUYER_ID']]['budget_qty'] = $row['BUDGET_QTY'];
        $buyer_wise_budget_qty_arr_pre[$row['BUYER_ID']]['dtls_id'] = $row['DTLS_ID'];       
    }

    //print_r($buyer_wise_budget_qty_arr);
    $company = sql_select("select company_name from lib_company where id=$company_name");
    $comp_name = $company[0]["COMPANY_NAME"];


    //  echo create_list_view("buyer_wise_sample_budget", "Buyer Name,Current Year Budgeted Qty (Kg),Previous Year Budgeted Qty (Kg),Previous Year Booking Qty (Kg)", "120,130,130,120","500","260",0, $buyer_arr , "js_set_value", "", "", 0, "buyer_name", $arr , "buyer_name", "",'','0,0,0,3,3','',0) ;
    //print_r('print');exit
    ob_start();
?>
    <!-- <h1>report container</h1> -->
    <form name="sample_fabric_budget_2" id="sample_fabric_budget_2">
        <fieldset style="width: 800px; margin:auto; margin-top:15px;">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" style="margin-bottom:10px" id="buyer_data_tbl">
                <thead>
                    <th width="100">Buyer</th>
                    <th width="100">Current Year Budgeted Qty (Kg)</th>
                    <th width="100">Previous Year Budgeted Qty (Kg)</th>
                    <th width="100">Previous Year Booking Qty (Kg)</th>
                </thead>
                <?
                $tot_buyer_budget_qty = 0;
                foreach ($buyer_arr as $buyer => $buyer_name) {
                    $buyer_budget_qty = 0;
                    $pre_buyer_budget_qty = 0;
                    $dtls_id = "";
                    if ($buyer_wise_budget_qty_arr[$buyer]) $buyer_budget_qty = $buyer_wise_budget_qty_arr[$buyer]['budget_qty'];
                    if ($buyer_wise_budget_qty_arr_pre[$buyer]) $pre_buyer_budget_qty = $buyer_wise_budget_qty_arr_pre[$buyer]['budget_qty'];
                    $dtls_id = $buyer_wise_budget_qty_arr[$buyer]['dtls_id'];
                    $break_down_data=trim($buyer_wise_break_data_arr[$buyer],'*');
                ?>
                    <tr bgcolor="#FFFFFF">
                        <td align="center" style="padding-right:3px"><? echo $buyer_name; ?>
                            <input type="hidden" id="break_down_data_<? echo $buyer; ?>" value="<? echo $break_down_data; ?>">
                        </td>
                        <td>
                            <input title="double click to open details" readonly align="center" type="text" id="yearTotal_<? echo $buyer; ?>" name="current_year_budget_qty_id[]" class="text_boxes_numeric" style="width:95%;" value="<? echo $buyer_budget_qty; ?>" ondblClick="current_year_budget_popup('<? echo $buyer ?>','<? echo $dtls_id ?>','<? echo $mst_id ?>')" placeholder="Double Click" />
                        </td>
                        <td>
                            <input readonly align="center" type="text" id="PreYearTotal_<? echo $buyer; ?>" name="previous_year_budget_qty_id[]" class="text_boxes_numeric" style="width:95%;" value="<? echo $pre_buyer_budget_qty; ?>" />
                        </td>
                        <td align="center" style="padding-right:3px"><input type="text" class="text_boxes_numeric" style="width: 97%;"></td>
                    </tr>
                <?
                    $tot_buyer_budget_qty += $buyer_budget_qty;
                }
                ?>
                <tr>
                    <td>Total</td>
                    <td align="right"><input type="text" id="buyer_total" class="text_boxes_numeric" style="width:95%;" value="<? echo $tot_buyer_budget_qty; ?>"></td>
                    <td align="right"><input type="text" id="buyer_total_pre" class="text_boxes_numeric" style="width:95%;" value="0"></td>
                    <td align="right">0
                        <input type="hidden" id="btn_status" value="<?= count($sql_res);?>" />
                        <input type="hidden" id="hid_update_id" value="<?= $mst_id;?>" />
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>

<?

    exit();
}

// for the year wise budget for a buyer 
if ($action == "budget_month_popup") {
    echo load_html_head_contents("Budget Deatails", "../../../", 1, 1, '', '1', '');
    extract($_REQUEST);
    //echo $mst_id;die;
    $company_name = $companyID;
    $selected_year = $budgeted_year;
    $dtls_id = $dtls_id;
    $location = $location;
    $teamLeader = $team_leader;
    $selected_pre_year = $selected_year - 1;
    // echo "hahaha".$teamLeader;

    if ($selected_year == 1) {
        $s = "2020";
        $e = "2021";
    } elseif ($selected_year == 2) {
        $s = "2021";
        $e = "2022";
    } elseif ($selected_year == 3) {
        $s = "2022";
        $e = "2023";
    } elseif ($selected_year == 4) {
        $s = "2023";
        $e = "2024";
    } elseif ($selected_year == 5) {
        $s = "2024";
        $e = "2025";
    }

    // echo $selected_year;
    $nameArray = sql_select("select sales_year_started,id from  variable_order_tracking where company_name='$company_name' and variable_list=12 order by id");
    $starting_month = $nameArray[0][csf('sales_year_started')];

    $sql = "select MONTH, YEAR, BUDGET_QTY from mm_yr_fab_budget_brkdwn where mst_id=$mst_id and BUYER_ID=$buyer and status_active=1 and is_deleted=0";
    $sql_res = sql_select($sql);
    //echo $sql;
    $month_wise_budget_qty_arr = array();
    foreach ($sql_res as $row) {
        $month_wise_budget_qty_arr[$row['MONTH']]['budget_qty'] = $row['BUDGET_QTY'];
    }
    $buyer_condition ="";
    if(trim(str_replace("'","",$buyer))>0) {
        $buyer_condition =" and d.BUYER_ID=$buyer";
    }
    $sql_pre = "select b.MONTH, b.YEAR, b.BUDGET_QTY from mm_yr_fab_budget_mst m, mm_yr_fab_budget_dtls d, mm_yr_fab_budget_brkdwn b where m.company_id=$company_name and m.location=$location and m.team_leader=$teamLeader and m.year=$selected_pre_year and m.id=b.mst_id and d.mst_id=b.mst_id and b.dtls_id=d.id and b.status_active=1 and b.is_deleted=0 $buyer_condition";

    //echo $sql_pre;
    $month_wise_budget_qty_pre = array();
    $sql_ex = sql_select($sql_pre);
    foreach ($sql_ex as $rowData) {
        $month_wise_budget_qty_pre[$rowData['MONTH']]['budget_qty'] = $rowData['BUDGET_QTY'];
    }
    //echo "<pre>";print_r($month_wise_budget_qty_pre);die;


    // echo "starting:". $starting_month."<br>";
    // $month_list = array(1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May", 6 => "June", 7 => "July", 8 => "August", 9 => "September", 10 => "October", 11 => "November", 12 => "December");

    $count = 1;
    if ($starting_month == "") $starting_month = 1;
    $i = $starting_month;
    // $y = "";
    $y = $s;
    $month_list_arrray = array();
    // echo "starting_year: ".$s."<br>";
    // echo "Ending year:".$e."<br>";
    $jan_pass = false;
    while ($count <= 12) {
        if ($i > 12) {
            $i = $i - 12;
            $jan_pass = true;
        }
        if ($jan_pass) {
            $y = $e;
        }
        // echo $y."<br>";
        // echo $month_list[$i]."/".$y."<br>";
        //array_push($month_list_arrray, $i . "/" . $y);
        $month_list_arrray[$i]['month'] = $i;
        $month_list_arrray[$i]['year'] = $y;
        $i++;
        $count++;
    }

?>

    </head>
    <script>
        function add_year_total() {
            var total = 0;
            for (let i = 1; i <= 12; i++) {
                var data = document.getElementById("month_budget_qty_" + i).value;
                var data_conv = parseFloat(data);
                total += data_conv;
            }
            document.getElementById("year_total_result").innerHTML = total;
        }

        function add_previous_year_total() {
            var total = 0;
            for (let i = 1; i <= 12; i++) {
                var data = document.getElementById("previous_month_data_" + i).value;
                var data_conv = parseFloat(data);
                total += data_conv;

            }
            // document.getElementById("previous_year_total_result").innerHTML = total;

        }

        function set_all_data() {
            var row_num = $('#tbl_details tbody tr').length;
            var total_data = "";
            let total_budget_qty = 0;
            for (var i = 1; i <= row_num; i++) {
                var month_data = $("#month_data_" + i).attr('title');
                var month_budget_qty = $("#month_budget_qty_" + i).val();
                if (total_data == "") total_data += month_data + "_" + month_budget_qty;
                else total_data += "*" + month_data + "_" + month_budget_qty;
                total_budget_qty += month_budget_qty * 1;

            }
            $("#hidden_all_data").val(total_data);
            $("#hidden_total_budget_qty").val(total_budget_qty);

            parent.emailwindow.hide();
        }
        function copy_value(value,field_id,i)
		{
			var copy_val=document.getElementById('copy_val').checked;
			// alert(copy_val);
		
			var rowCount = $('#tbl_details tr').length-2;
			// var copy_basis=document.getElementById('copy_basis').value;
            // alert(rowCount);
            if(copy_val){
                for(var j=1; j<=rowCount; j++){
                    field_id_name = field_id+j;
                    document.getElementById(field_id_name).value = value;
                }
            }
		}
    </script>
    </head>
    <form name="month_details_1" id="month_details_1">
        <fieldset style="width:390px">
            <input type="hidden" id="hidden_all_data">
            <input type="hidden" id="hidden_total_budget_qty">
            <table width="550" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="tbl_details">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Current Year Budgeted Qty (Kg) <b>Copy</b> : <input type="checkbox" id="copy_val" name="copy_val" checked/></th>
                        <th>Previous Year Budgeted Qty (Kg)</th>
                    </tr>
                </thead>
                <tbody>

                    <?
                    $id_increment = 1;
                    $tot_budget_qty = 0;
                    // $tot_budget_qty_pre = 0;
                    foreach ($month_list_arrray as $val) {
                        $budget_qty = 0;
                        $pre_budget_qty = 0;
                        $data = $val['year'] . '_' . $val['month'];
                        if ($month_wise_budget_qty_arr[$val['month']]['budget_qty']) $budget_qty = $month_wise_budget_qty_arr[$val['month']]['budget_qty'];
                        // if ($month_wise_budget_qty_arr[$val['month']]['budget_qty']) $pre_budget_qty = $month_wise_budget_qty_pre[$val['month']]['budget_qty'];
                    ?>
                        <tr bgcolor="#FFFFFF">
                            <td align="left" style="padding-right:3px" id="month_data_<? echo $id_increment; ?>" title="<? echo $data; ?>"><? echo $months[$val['month']] . '/' . $val['year']; ?></td>
                            <td align="right">
                                <input oninput="copy_value(this.value, 'month_budget_qty_',<?= $id_increment ?>);add_year_total();" style="width: 95%;" type="numeric" align="right" value="<? echo $budget_qty ?>" class="text_boxes_numeric" id="month_budget_qty_<? echo $id_increment; ?>">
                            </td>
                            <td align="right" style="padding-right:3px"><input onchange="add_previous_year_total()" style="width: 97%;" value="<?= $month_wise_budget_qty_pre[$val['month']]['budget_qty']; ?>" type="text" class="text_boxes_numeric" id="previous_month_data_<? echo $id_increment; ?>" readonly></td>
                        </tr>
                    <?
                        $id_increment++;
                        $tot_budget_qty += $budget_qty;
                        $tot_budget_pre_qty += $month_wise_budget_qty_pre[$val['month']]['budget_qty'];
                        // $tot_budget_qty_pre += $pre_budget_qty;
                    } ?>


                </tbody>
                <tfoot>
                    <tr>
                        <td align="left">Total</td>
                        <td align="right" id="year_total_result"><? echo $tot_budget_qty; ?></td>
                        <td align="right" id="previous_year_total_result"><? echo $tot_budget_pre_qty; ?></td>
                    </tr>
                </tfoot>
            </table>

            <table width="550" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%">
                            <div style="width:50%;" align="center">
                                <input type="button" name="close" onClick="set_all_data();" class="formbutton" value="Close" style="width:100px" />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script type="text/javascript">
        setFilterGrid('table_body', -1);
    </script>
<?
}

?>