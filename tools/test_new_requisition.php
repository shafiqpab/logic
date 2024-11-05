<?php
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("User Creation", "../", $filter, 1, $unicode, '', '');
?>

</head>

<body onLoad="set_hotkey()">
    <div align="center">
        <?php echo load_freeze_divs("../", $permission); ?>
        <form name="usercreationform_1" id="usercreationform_1" autocomplete="off">
            <fieldset style="width:390px;">
                <legend> Test Info</legend>
                <div style="width:100%;"  align="center">
                    <table width="90%">
                        <tr>
                            <td>User ID</td>
                            <td>
                                <input type="text" name="txt_user_id" id="txt_user_id"class="text_boxes" style="width:220px"  tabindex="2"  />
                            </td>
                        </tr>

                        <tr>
                            <td>Full User Name</td>
                            <td>
                                <input type="text" name="txt_full_user_name" id="txt_full_user_name" class="text_boxes" style="width:220px"  tabindex="4" />
                            </td>
                        </tr>

                        <tr>
                            <td>Designation</td>
                            <td>
                                <?php
                                echo create_drop_down("cbo_designation", 232, "select id,custom_designation from lib_designation where status_active=1 and is_deleted=0 order by custom_designation", "id,custom_designation", 1, "-- Select Designation--", $selected);
                                //  create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>User Level</td>
                            <td>
                                <select name="cbo_user_level" id="cbo_user_level" class="combo_boxes" style="width:232px"  tabindex="5" >
                                    <option value="0">-- Select --</option>
                                    <option value="1">General User</option>
                                    <option value="2">Admin User</option>
                                    <option value="3">Demo User</option>
                                    <option value="4">Buyer User</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Expiry Date</td>
                            <td>
                                <input type="text" size="12" name="txt_exp_date" id="txt_exp_date" class="datepicker"  style="width:80px"  tabindex="8" />
                                &nbsp;Status
                                <select name="cbo_user_sts" id="cbo_user_sts" class="combo_boxes" tabindex="9" style="width:92px">
                                    <option value="1">Active</option>
                                    <option value="2">Inactive</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td  height="30" align="center" valign="bottom" colspan="2">  
                                <?php echo load_submit_buttons($permission, "fnc_user_creation", 0, 0, "reset_form('usercreationform_1','','')", 1); ?>
                            </td>
                        </tr>

                    </table>
                </div>
            </fieldset>
        </form>
    </div>

</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>