<?

//Title : only for test by Reaz Uddin




session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("User Privilege", "../", 1, 0, $unicode,'','');
?>

<script language="javascript">
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  	 
var permission='<?php echo $permission; ?>';
    
function fnc_test_employee(operation)
{
	if (form_validation('txt_full_name','Full txt_emp_code')==false)
	{
		return;
	}
	else
	{
		//'txt_employee_id*txt_full_name*txt_emp_code*txt_company*txt_emaile*txt_address*txt_joining_date*sex*education*status_active'
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_full_name*txt_emp_code*txt_company*cbo_designation*txt_email*txt_address*txt_joining_date*sex*education*status_active*update_id',"../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/hello_test_controller",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_on_submit_reponse;
	}
}

function fnc_on_submit_reponse()
{
	if(http.readyState == 4) 
		{
			//alert(http.responseText); return;
			var reponse=http.responseText.split('**');
			show_msg(trim(reponse[0]));
			show_list_view('','load_php_data_to_form','list_view','requires/hello_test_controller','setFilterGrid("list_view",-1)');
			reset_form("usercreationform_1","","","","");
			set_button_status(0, permission, 'fnc_test_employee',1);
			release_freezing();
	
		}
}
</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center">
        <?php echo load_freeze_divs("../", $permission); ?>
        <form name="usercreationform_1" id="usercreationform_1" autocomplete="off">
            <fieldset style="width:500px;">
                <legend> Test Employee Information </legend>
                <div style="width:100%;"  align="center">
                    <table width="90%">

                        <tr>
                            <td>Full Name :</td>
                            <td>
                                <input type="text" name="txt_full_name" id="txt_full_name" class="text_boxes" style="width:220px"/>
                                <input type="hidden" name="update_id" id="update_id"class="text_boxes_numeric" style="width:220px"/>
                            </td>
                        </tr>

                        <tr>
                            <td>Employee Code :</td>
                            <td>
                                <input type="text" name="txt_emp_code" id="txt_emp_code" class="text_boxes_numeric" style="width:220px"/>
                            </td>
                        </tr>

                        <tr>
                            <td>Company :</td>
                            <td>
                                <input type="text" name="txt_company" id="txt_company" class="text_boxes" style="width:220px"/>
                            </td>
                        </tr>

                        <tr>
                            <td>Designation :</td>
                            <td>
                                <?php
                                   echo create_drop_down( "cbo_designation", 232, "select id,custom_designation from lib_designation where status_active=1 and is_deleted=0 order by custom_designation","id,custom_designation", 1, "-- Select Designation--", $selected );
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Email :</td>
                            <td>
                                <input type="email" name="txt_email" id="txt_email" class="text_boxes" style="width:220px"/>
                            </td>
                        </tr>

                        <tr>
                            <td>Address :</td>
                            <td>
                                <input type="text" name="txt_address" id="txt_address" class="text_boxes" style="width:220px"/>
                            </td>
                        </tr>
 <!--id full_name emp_code company designation email address joining_date sex education inserted_by inserte_date updated_by update_date is_deleted status_active -->
                        <tr>
                            <td>Joining Date :</td>
                            <td>
                                <input type="text" name="txt_joining_date" id="txt_joining_date" class="datepicker" style="width:171px"/>
                            </td>
                        </tr>

                        <tr>
                            <td>Sex :</td>
                            <td>
                                <select name="sex" id="sex" class="combo_boxes" style="width:232px">
                                    <option value="0">-- Select --</option>
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Education :</td>
                            <td>
                                <input type="text" name="education" id="education" class="text_boxes" style="width:221px">
                            </td>
                        </tr>

                        <tr>
                            <td>Status Active :</td>
                            <td>
                                <select name="status_active" id="status_active" class="combo_boxes" style="width:232px">
                                    <option value="0">-- Select --</option>
                                    <option value="1">Active</option>
                                    <option value="0">In Active</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td  height="30" align="center" valign="bottom" colspan="2">  
                                <?php echo load_submit_buttons($permission, "fnc_test_employee", 0, 0, "reset_form('usercreationform_1','','')", 1); ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </fieldset>
        </form>
        <fieldset style="width:1000px; margin-top:10px">
            <legend> All Employee </legend>
            <form>
                <div style="width:1000px; margin-top:10px" id="list_view" align="left">
                <?php
				   $designation_arr = return_library_array("select id, system_designation from lib_designation","id","system_designation");
				   $SexStatus_arr= array(1=>'Male',2=>'Female');
				   $Status_arr= array(0=>'In Active',1=>'Active');
					$arr=array (3=>$designation_arr,7=>$SexStatus_arr, 9=>$Status_arr);
					echo  create_list_view ( "list_view", "Full Name,Employee Code,Company,Designation,Email,Address,Joining Date,Sex,Education,Status", "100,100,80,100,100,100,100,50,80","990","220",1, "select id,full_name,emp_code,company,designation,email,address,joining_date,sex,education,status_active from tbl_test_employee_reaz", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,designation,0,0,0,sex,0,status_active", $arr , "full_name,emp_code,company,designation,email,address,joining_date,sex,education,status_active", "requires/test_employee_reaz_controller", 'setFilterGrid("list_view",-1);' );
                ?>
                </div>
            </form>
        </fieldset>
    </div>
</body>

<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>