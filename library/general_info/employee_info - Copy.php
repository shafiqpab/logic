<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Employee Info", "../../", 1, 1,$unicode,'','');

?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';

function fnc_emp_info(operation)
{
	/*if( form_validation('cbo_company_name*cbo_item_category*txt_sent_by*txt_sent_to*txt_receive_date*txt_start_hours*txt_start_minuties*txt_item_description*txt_quantity*cbo_uom*txt_rate','Company Name*Item Catagory*Sent By*Sent To*Out Date*Out Time*Out Time*Item Description*Quantity*UOM*Rate')==false )
	{
		return;
	}*/	
	var dataString = "txt_emp_code*txt_emp_name_fst*txt_emp_name_sec*txt_emp_name_thir*txt_emp_name_ban*txt_father_name*txt_father_name_ban*txt_mother_name*txt_mother_name_ban*cbo_sex*txt_birth_pla*txt_dob*txt_age*cbo_religion*cbo_marry*cbo_blood_grp*txt_natinality*txt_nation_id*txt_pass_no*cbo_emp_cata*cbo_design_lbl*cbo_design*cbo_function_sup*cbo_admin_sup*txt_id_card_no*txt_join_data*txt_con_data*txt_panch_ca_no*txt_remarks*cbo_company_name*cbo_location_name*cbo_division_name*cbo_dept_name*cbo_section_name*txt_line_no*update_id";
 	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../");
	alert(data);
	freeze_window(operation);
	http.open("POST","requires/employee_info_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_emp_info_reponse;
}


function fnc_emp_info_reponse()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
		if(response[0]==0|| response[0]==1)
		{
			show_msg(trim(response[0]));
			var ms_id=document.getElementById("update_id").value=response[1];
			
			/*$("#txt_system_id").val(response[2]);
			$("#update_id").val(response[3]);
			show_list_view(response[2],'show_dtls_list_view','list_container','requires/get_out_entry_controller','');
			reset_form('','','txt_item_description*cbo_uom*txt_quantity*txt_rate*txt_amount*txt_remarks','','','');*/
			set_button_status(1, permission, 'fnc_capasity_allocation',1,1);
			release_freezing();
		}
		else if(response[0]==10)
		{
			set_button_status(0, permission, 'fnc_capasity_allocation',1);
			alert("\"Select Year\" mendatory to save data");
			show_msg(trim(response[0]));
			release_freezing();
			return;
		}
		
 	}
}

</script>
</head>
<body onLoad="set_hotkey()">
<div align="center" style="width:1100px;">
<? echo load_freeze_divs ("../../",$permission);  ?>

    <fieldset style="width:1100px;">
    <legend>Basic Info</legend>
    <table cellpadding="0" cellspacing="2" width="1100px">
    	 <tr>
        	<td align="right">Employee code:</td>
            <td colspan="5"><input type="text" id="txt_emp_code" name="txt_emp_code" class="text_boxes" style="width:150px;" placeholder="Double Click To Search"/></td><input type="text" id="update_id" name="update_id">
        </tr>
    	<tr>
        	<td width="180px" align="right">Employee Name:</td>
            <td width="185px"><input type="text" id="txt_emp_name_fst" name="txt_emp_name" class="text_boxes" style="width:150px;" placeholder="First Name"/></td>
            <td width="180px"><input type="text" id="txt_emp_name_sec" name="txt_emp_name" class="text_boxes" style="width:150px;" placeholder="Middle Name"/></td>
            <td width="185px"><input type="text" id="txt_emp_name_thir" name="txt_emp_name" class="text_boxes" style="width:150px;" placeholder="Last Name"/></td>
            <td width="180px" align="right">Employee Name(bangla):</td>
            <td width="185px"><input type="text" id="txt_emp_name_ban" name="txt_emp_name_ban" class="text_boxes" style="width:150px;" placeholder="First Name"/></td>
        </tr>
        <tr>
        	<td align="right">Father's Name:</td>
            <td><input type="text" id="txt_father_name" name="txt_father_name" class="text_boxes" style="width:150px;"/></td>
            <td align="right">Father's Name(bangla):</td>
            <td><input type="text" id="txt_father_name_ban" name="txt_father_name_ban" class="text_boxes" style="width:150px;"/></td>
            
            
        </tr>
        <tr>
        	<td align="right">Mother's Name:</td>
            <td><input type="text" id="txt_mother_name" name="txt_mother_name" class="text_boxes" style="width:150px;"/></td>
            <td align="right">Mother's Name(bangla):</td>
            <td><input type="text" id="txt_mother_name_ban" name="txt_mother_name_ban" class="text_boxes" style="width:150px;"/></td>
            <td align="right">Sex:</td>
            <td>
			<? 
				$sex_arr=array(1=>"Male",2=>"Female");
            	echo create_drop_down( "cbo_sex", 165, $sex_arr,"", 1, "-- Select Gender --", $selected );
            ?>
            </td>
        </tr>
        <tr>
        	<td align="right">Birth Place:</td>
            <td><input type="text" id="txt_birth_pla" name="txt_birth_pla" class="text_boxes" style="width:150px;"/></td>
            <td align="right">Date of Birth:</td>
            <td><input type="text" id="txt_dob" name="txt_dob" class="text_boxes" style="width:150px;"/></td>
            <td align="right">Age:</td>
            <td><input type="txt" id="txt_age" name="txt_age" class="text_boxes" style="width:150px;"/></td>
            <td></td>
        </tr>
        <tr>
        	<td align="right">Religion:</td>
            <td>
            <? 
				$religion_arr=array(1=>"Islam",2=>"Hindu",3=>"Christan",4=>"Buddhist",5=>"Others");
            	echo create_drop_down( "cbo_religion", 165,$religion_arr,"",1, "-- Select Religion --", $selected );
            ?>
            </td>
            <td align="right">Marital Status:</td>
            <td>
            <? 
				$marry_arr=array(1=>"Single",2=>"Married",3=>"Separated",4=>"Widow");
            	echo create_drop_down( "cbo_marry", 165,$marry_arr,"",1, "-- Select Status --", $selected );
            ?>
            </td>
            <td align="right">Blood Group:</td>
            <td>
			<? 
				$blood_group_arr=array(1=>"A+",2=>"A-",3=>"B+",4=>"B-",5=>"AB+",6=>"AB-",7=>"O+",8=>"O-");
            	echo create_drop_down( "cbo_blood_grp", 165,$blood_group_arr,"",1, "-- Select Group --", $selected );
            ?>
            </td>
        </tr>
        <tr>
        	<td align="right">Nationality:</td>
            <td><input type="text" id="txt_natinality" name="txt_natinality" class="text_boxes" style="width:150px;"/></td>
            <td align="right">National Id:</td>
            <td><input type="text" id="txt_nation_id" name="txt_nation_id" class="text_boxes" style="width:150px;"/></td>
            <td align="right">Passport No:</td>
            <td><input type="text" id="txt_pass_no" name="txt_pass_no" class="text_boxes" style="width:150px;"/></td>
        </tr>
        <tr>
        	<td align="right">Employee Category:</td>
            <td>
             <? 
				$emp_catagory_arr=array(1=>"Top Management",2=>"Mid Management",3=>"Non Management",4=>"Contractual");
            	echo create_drop_down( "cbo_emp_cata", 165,$emp_catagory_arr,"",1, "-- Select Category --", $selected );
            ?>
            </td>
            <td align="right">Designation Lebel:</td>
            <td>
            <? 
            	echo create_drop_down( "cbo_design_lbl", 165, "select id,system_designation from lib_designation where status_active=1 and is_deleted=0","id,system_designation", 1, "-- Select Designation Lebel--", $selected );
            ?>
            </td>
            <td align="right">Designation:</td>
            <td>
            <? 
            	echo create_drop_down( "cbo_design", 165, "select id,custom_designation from lib_designation where status_active=1 and is_deleted=0","id,custom_designation", 1, "-- Select Designation--", $selected );
            ?>
            </td>
        </tr>
        <tr>
        	<td align="right">Functional Superior:</td>
            <td>
            <? 
            	echo create_drop_down( "cbo_function_sup", 165, "select id,custom_designation from lib_designation where status_active=1 and is_deleted=0","id,custom_designation", 1, "-- Select Superior--", $selected );
            ?>
            </td>
            <td align="right">Admin Superior:</td>
            <td>
            <? 
            	echo create_drop_down( "cbo_admin_sup", 165, "select id,custom_designation from lib_designation where status_active=1 and is_deleted=0","id,custom_designation", 1, "-- Select Admin Superior--", $selected );
            ?>
            </td>
            <td align="right">Id Card No:</td>
            <td><input type="text" id="txt_id_card_no" name="txt_id_card_no" class="text_boxes" style="width:150px;"/></td>
        </tr>
        <tr>
        	<td align="right">Joining Date:</td>
            <td><input type="text" id="txt_join_data" name="txt_join_data" class="text_boxes" style="width:150px;"/></td>
            <td align="right">Confirmation Date:</td>
            <td><input type="text" id="txt_con_data" name="txt_con_data" class="text_boxes" style="width:150px;"/></td>
            <td align="right">Punch Card No:</td>
            <td><input type="text" id="txt_panch_ca_no" name="txt_panch_ca_no" class="text_boxes" style="width:150px;"/></td>
        </tr>
         <tr>
        	<td align="right">Remarks:</td>
            <td colspan="5"><input type="text" id="txt_remarks" name="txt_remarks" class="text_boxes" style="width:883px;"/></td>
        </tr>
        

    </table>
    
    
    <table cellpadding="0" cellspacing="2" width="1100px" class="rpt_table">
        <thead>
            <th width="185px">Company</th>
            <th width="180px">Location</th>
            <th width="185px">Division</th>
            <th width="180px">Department</th>
            <th width="185px">Section</th>
            <th width="180px">Line No.</th>
        </thead>
        <tbody>
            <tr>
                <td align="center">
                <? 
                echo create_drop_down( "cbo_company_name", 165, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected );
                ?>
                </td>
                <td align="center">
                <? 
                echo create_drop_down( "cbo_location_name", 165, "select id,location_name from lib_location where status_active=1 and is_deleted=0","id,location_name", 1, "-- Select Location --", $selected );
                ?>
                </td>
                <td align="center">
                <? 
                echo create_drop_down( "cbo_division_name", 165, "select id,division_name from lib_division where status_active=1 and is_deleted=0","id,division_name", 1, "-- Select Division --", $selected );
                ?>
                </td>
                <td align="center">
                <? 
                echo create_drop_down( "cbo_dept_name", 165, "select id,department_name from lib_department where status_active=1 and is_deleted=0","id,department_name", 1, "-- Select Department --", $selected );
                ?>
                </td>
                <td align="center">
                <? 
                echo create_drop_down( "cbo_section_name", 165, "select id,section_name from lib_section where status_active=1 and is_deleted=0","id,section_name", 1, "-- Select Section --", $selected );
                ?>
                </td>
                <td align="center">
                <input type="text" id="txt_line_no" name="txt_line_no" class="text_boxes" style="width:165px;"/>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="6" valign="middle" class="button_container">
                <? 
                echo load_submit_buttons( $permission, "fnc_emp_info", 0,0,"",1);
                ?>
                </td>
            </tr>
        </tbody>
    </table>
 
    </fieldset>

                      
</div>
</body>
</html>