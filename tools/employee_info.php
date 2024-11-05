<?php
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("User Privilege", "../", 1, 0, $unicode, '', '');
?>
<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";
    var permission = '<?php echo $permission; ?>';

// popup for employee ID----------------------
    function fnc_emp_info(operation)
    {

        if (form_validation('txt_emp_name_fst*cbo_sex*cbo_design*txt_id_card_no*cbo_company_name', 'First Name *Gender*Designation*Id Card No*Company') == false)
        {
            return;
        }
        var dataString = "txt_emp_code*txt_emp_name_fst*txt_emp_name_sec*txt_emp_name_thir*txt_emp_name_ban*txt_father_name*txt_father_name_ban*txt_mother_name*txt_mother_name_ban*cbo_sex*txt_birth_pla*txt_dob*txt_age*cbo_religion*cbo_marry*cbo_blood_grp*txt_natinality*txt_nation_id*txt_pass_no*cbo_emp_cata*cbo_design_lbl*cbo_design*cbo_function_sup*cbo_admin_sup*txt_id_card_no*txt_join_data*txt_con_data*txt_panch_ca_no*txt_remarks*cbo_company_name*cbo_location_name*cbo_division_name*cbo_dept_name*cbo_section_name*txt_line_no*update_id";
        var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string(dataString, "../");
        freeze_window(operation);
        http.open("POST", "requires/employee_info_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_emp_info_reponse;
    }

    function fnc_emp_info_reponse()
    {
        if (http.readyState == 4)
        {
            release_freezing();
            return;
            var response = trim(http.responseText).split('**');

            if (response[0] == 0 || response[0] == 1)
            {
                show_msg(trim(response[0]));
                var ms_id = document.getElementById("update_id").value = response[1];
                document.getElementById("txt_emp_code").value = response[1];
                show_list_view(response[2], 'create_emp_list_view', 'list_container', 'requires/employee_info_controller', 'setFilterGrid("list_view",-1)');
                reset_form('frm_emp_info', '', '', '', '', '');
                set_button_status(0, permission, 'fnc_emp_info', 1, 1);
                //function set_button_status(is_update, permission, submit_func, btn_id, show_print)
                release_freezing();
            }
            if (response[0] == 11)
            {
                alert("Id Card Number Should not be Duplicate");
                //set_button_status(0, permission, 'fnc_emp_info',1,1);
                release_freezing();
            }

        }
    }
</script>
</head>

<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
        <?php echo load_freeze_divs("../", $permission); ?>
        <form name="frm_emp_info" id="frm_emp_info"  autocomplete="off">
            <div style="width:500px;">
                <fieldset style="width:500px;">
                    <legend>Basic Info</legend>
                    <table cellpadding="0" cellspacing="2" width="500px" align="center">
                        <tr>
                            <td width="180px" align="right" class="must_entry_caption">&nbsp;Full Name:</td>
                            <td width="185px"><input type="text" id="txt_full_name" name="txt_full_name" class="text_boxes" style="width:250px;" placeholder="Full Name"/></td>
                        </tr>
                        <tr>
                            <td align="right">Date of Birth:</td>
                            <td><input type="text" id="txt_dob" name="txt_dob" class="datepicker" style="width:170px;"/></td>
                        </tr>
                        <tr>
                            <td align="right">Religion:</td>
                            <td>
                                <?php
                                $religion_arr = array(1 => "Islam", 2 => "Hindu", 3 => "Christan", 4 => "Buddhist", 5 => "Others");
                                echo create_drop_down("cbo_religion", 250, $religion_arr, "", 1, "-- Select Religion --", $selected);
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td align="right" >Designation:</td>
                            <td>
                                <?php
                                echo create_drop_down("cbo_design_lbl", 250, "select id,system_designation from lib_designation where status_active=1 and is_deleted=0 order by system_designation asc", "id,system_designation", 1, "-- Select Designation Lebel--", $selected);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">Present Address:</td>
                            <td colspan="5">
                                <input type="text" id="present_address" name="present_address" class="text_boxes" style="width:250px;" placeholder="Double Click To Search" onDblClick="openmypage_emp_pop();" readonly/>
                            </td>
                        <input type="hidden" id="update_id" name="update_id">
                        </tr>
                    </table>
                    <div id=""></div>
                </fieldset>
            </div>
        </form>
        <br>
        <fieldset style="width:835px;">
            <form>
                <div style="width:835px;" id="list_container">
                    <?php
                    echo create_list_view("list_view", "Full Name,Date of Birth,Relegion,Designation,Present Address", "200,150,100,100,240", "825", "260", 0, $sql, "get_php_form_data", "emp_code", "'populate_master_from_data','requires/employee_info_controller'", 1, "0,0,0,designation_id,line_no,company_id,location_id,division_id,department_id,section_id", $arr, "emp_code,id_card_no,emp_name,designation_id,line_no,company_id,location_id,division_id,department_id,section_id", "employee_info_controller", 'setFilterGrid("list_view",-1);', '0,0,0,0,0,0,0,0,0');
                    ?>
                </div>
            </form>
        </fieldset>

    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>