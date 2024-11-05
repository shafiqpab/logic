<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create for employee
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	02-11-2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Employee Info", "../../", 1, 1,$unicode,1,'');

?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';


// popup for employee ID----------------------
function openmypage_emp_pop()
{
	page_link='requires/employee_info_controller.php?action=emp_id_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Employee Popup', 'width=1200px, height=400px, center=1, resize=0, scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("hidden_emp_number");
		var response=theemail.value.split('_');
		//var emp_num=this.contentDoc.getElementById("hidden_emp_number").value; // system number
		if(theemail.value!="")
		{
			freeze_window(5);
			$("#txt_emp_code").val(response[0]);
			get_php_form_data(response[0], "populate_master_from_data", "requires/employee_info_controller" );
			release_freezing();
		}
	}
}

function fnc_emp_info(operation)
{
	if( form_validation('txt_emp_name_fst*cbo_sex*cbo_design*txt_id_card_no*cbo_company_name','First Name *Gender*Designation*Id Card No*Company')==false )
	{
		return;save_update_delete&operation
	}
	var dataString =  "txt_emp_code*txt_emp_name_fst*txt_emp_name_sec*txt_emp_name_thir*txt_emp_name_ban*txt_father_name*txt_father_name_ban*txt_mother_name*txt_mother_name_ban*cbo_sex*txt_birth_pla*txt_dob*txt_age*cbo_religion*cbo_marry*cbo_blood_grp*txt_natinality*txt_nation_id*txt_pass_no*cbo_emp_cata*cbo_design_lbl*cbo_design*cbo_function_sup*cbo_admin_sup*txt_id_card_no*txt_join_data*txt_con_data*txt_panch_ca_no*txt_remarks*txt_mob_no*cbo_operator*cbo_company_name*cbo_location_name*cbo_division_name*cbo_dept_name*cbo_section_name*txt_line_no*cbo_floor_name*cbo_table_name*cbo_in_charge*update_id*cbo_status";
 	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
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
	
		if(response[0]==0 || response[0]==1)
		{
			show_msg(trim(response[0]));
            show_list_view('','employee_list_view','list_container','requires/employee_info_controller','setFilterGrid("list_view",-1)');
			var ms_id=document.getElementById("update_id").value=response[1];
			document.getElementById("txt_emp_code").value=response[1];
 			reset_form('frm_emp_info','','','','','');
			set_button_status(0, permission, 'fnc_emp_info',1,1);
 			release_freezing();
		}
		if(response[0]==11)
		{
			alert("Id Card Number Should not be Duplicate");
			//set_button_status(0, permission, 'fnc_emp_info',1,1);
			release_freezing();
		}

 	}
}

function fnResetForm()
{
	set_button_status(0, permission, 'fnc_emp_info',1);
	reset_form('frm_emp_info','','','','','');
}

function showEmpBarcode() {
    var data = document.getElementById('update_id').value;
    if(data) {
        freeze_window(3);
        var url=return_ajax_request_value(data, 'print_barcode_employee', 'requires/employee_info_controller');
        window.open(url,"##");
        release_freezing();
    } else {
        alert('Please select an Employee first');
    }
}

function load_table()
{

    load_drop_down( 'requires/employee_info_controller', $("#cbo_company_name").val()+'_'+$("#cbo_location_name").val()+'_'+$("#cbo_floor_name").val(), 'load_drop_down_table', 'td_table');
}

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="frm_emp_info" id="frm_emp_info"  autocomplete="off">
     <div style="width:1100px;">
    <fieldset style="width:1100px;">
    <legend>Basic Info</legend>
    <table cellpadding="0" cellspacing="2" width="1100px" align="center">
    	 <tr>
        	<td align="right">Employee code:</td>
            <td colspan="5"><input type="text" id="txt_emp_code" name="txt_emp_code" class="text_boxes" style="width:150px;" placeholder="Double Click To Search" onDblClick="openmypage_emp_pop();" readonly/></td><input type="hidden" id="update_id" name="update_id">
        </tr>
    	<tr>
        	<td width="180px" align="right" class="must_entry_caption">&nbsp;Employee Name:</td>
            <td width="185px"><input type="text" id="txt_emp_name_fst" name="txt_emp_name_fst" class="text_boxes" style="width:150px;" placeholder="First Name"/></td>
            <td width="180px"><input type="text" id="txt_emp_name_sec" name="txt_emp_name_sec" class="text_boxes" style="width:150px;" placeholder="Middle Name"/></td>
            <td width="185px"><input type="text" id="txt_emp_name_thir" name="txt_emp_name_thir" class="text_boxes" style="width:150px;" placeholder="Last Name"/></td>
            <td width="180px" align="right">Employee Name(bangla):</td>
            <td width="185px"><input type="text" id="txt_emp_name_ban" name="txt_emp_name_ban" class="text_boxes" style="width:150px;" placeholder="First Name(bangla)"/></td>
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
            <td align="right" class="must_entry_caption">&nbsp;Sex:</td>
            <td>
			<? 
			$sex_arr=array(1=>"Male",2=>"Female");
            echo create_drop_down( "cbo_sex", 162, $sex_arr,"", 1, "-- Select Gender --", $selected );
            ?>
            </td>
        </tr>
        <tr>
        	<td align="right">Birth Place:</td>
            <td><input type="text" id="txt_birth_pla" name="txt_birth_pla" class="text_boxes" style="width:150px;"/></td>
            <td align="right">Date of Birth:</td>
            <td><input type="text" id="txt_dob" name="txt_dob" class="datepicker" style="width:150px;"/></td>
            <td align="right">Age:</td>
            <td><input type="txt" id="txt_age" name="txt_age" class="text_boxes" style="width:150px;"/></td>
            <td></td>
        </tr>
        <tr>
        	<td align="right">Religion:</td>
            <td>
            <? 
			$religion_arr=array(1=>"Islam",2=>"Hindu",3=>"Christan",4=>"Buddhist",5=>"Others");
            echo create_drop_down( "cbo_religion", 162,$religion_arr,"",1, "-- Select Religion --", $selected );
            ?>
            </td>
            <td align="right">Marital Status:</td>
            <td>
            <? 
			$marry_arr=array(1=>"Single",2=>"Married",3=>"Separated",4=>"Widow");
            echo create_drop_down( "cbo_marry", 162,$marry_arr,"",1, "-- Select Status --", $selected );
            ?>
            </td>
            <td align="right">Blood Group:</td>
            <td>
			<? 
			$blood_group_arr=array(1=>"A+",2=>"A-",3=>"B+",4=>"B-",5=>"AB+",6=>"AB-",7=>"O+",8=>"O-");
            echo create_drop_down( "cbo_blood_grp", 162,$blood_group_arr,"",1, "-- Select Group --", $selected );
            ?>
            </td>
        </tr>
        <tr>
        	<td align="right">Nationality:</td>
            <td><input type="text" id="txt_natinality" name="txt_natinality" class="text_boxes" style="width:150px;"/></td>
            <td align="right">National Id:</td>
            <td><input type="text" id="txt_nation_id" name="txt_nation_id" class="text_boxes_numeric" style="width:150px;"/></td>
            <td align="right">Passport No:</td>
            <td><input type="text" id="txt_pass_no" name="txt_pass_no" class="text_boxes_numeric" style="width:150px;"/></td>
        </tr>
        <tr>
        	<td align="right">Employee Category:</td>
            <td>
             <? 
			$emp_catagory_arr=array(1=>"Top Management",2=>"Mid Management",3=>"Non Management",4=>"Contractual");
            echo create_drop_down( "cbo_emp_cata", 162,$emp_catagory_arr,"",1, "-- Select Category --", $selected );
            ?>
          </td>
            <td align="right" >Designation Lebel:</td>
            <td>
            <? 
            echo create_drop_down( "cbo_design_lbl", 162, "select id,system_designation from lib_designation where status_active=1 and is_deleted=0 order by system_designation asc","id,system_designation", 1, "-- Select Designation Lebel--", $selected );
            ?>
            </td>
            <td align="right" class="must_entry_caption">Designation:</td>
            <td>
            <? 
            echo create_drop_down( "cbo_design", 162, "select id,custom_designation from lib_designation where status_active=1 and is_deleted=0 order by custom_designation asc","id,custom_designation", 1, "-- Select Designation--", $selected );
            ?>
            </td>
        </tr>
        <tr>
        	<td align="right">Functional Superior:</td>
            <td>
            <? 
            echo create_drop_down( "cbo_function_sup", 162, "select id,custom_designation from lib_designation where status_active=1 and is_deleted=0 order by custom_designation asc","id,custom_designation", 1, "-- Select Superior--", $selected );
            ?>
            </td>
            <td align="right">Admin Superior:</td>
            <td>
            <?
            echo create_drop_down( "cbo_admin_sup", 162, "select id,custom_designation from lib_designation where status_active=1 and is_deleted=0 order by custom_designation asc","id,custom_designation", 1, "-- Select Admin Superior--", $selected );
            ?>
            </td>
            <td align="right" class="must_entry_caption">Id Card No:</td>
            <td><input type="text" id="txt_id_card_no" name="txt_id_card_no" class="text_boxes" style="width:150px;"/></td>
        </tr>
        <tr>
        	<td align="right">Joining Date:</td>
            <td><input type="text" id="txt_join_data" name="txt_join_data" class="datepicker" style="width:150px;"/></td>
            <td align="right">Confirmation Date:</td>
            <td><input type="text" id="txt_con_data" name="txt_con_data" class="datepicker" style="width:150px;"/></td>
            <td align="right">Punch Card No:</td>
            <td><input type="text" id="txt_panch_ca_no" name="txt_panch_ca_no" class="text_boxes_numeric" style="width:150px;"/></td>
        </tr>
        <tr>
        	<td align="right">Remarks:</td>
            <td ><input type="text" id="txt_remarks" name="txt_remarks" class="text_boxes" style="width:150px;"/></td>
            <td align="right"> Mobile bank operator and number:</td>
            <td>
                <div style="display: flex;">
                <? 
                $operator_arr=array(1=>"Bkash",2=>"Rocket");
                echo create_drop_down( "cbo_operator",100,$operator_arr,"",1, "-- Select Operator --", $selected );
                ?>
                <input type="text" id="txt_mob_no" name="txt_mob_no" class="text_boxes_numeric" style="width:100px;"/>
                </div>
            </td>
            <td align="right"> Status:</td>
            <td >
            <? 
			$status_arr=array(1=>"Active",2=>"Inactive");
            echo create_drop_down( "cbo_status",160,$status_arr,"",1, "-- Select  --", $selected );
            ?>
          </td>
        </tr>
        <tr>
        	<td align="right">In-Charge:</td>
           <td> <? 
				$in_charge_arr=array(1=>"Knitting",2=>"Dyeing", 3=>"Finishing", 4=>"Sewing", 5=>"Recipe");
                
            	echo create_drop_down( "cbo_in_charge",161,$in_charge_arr,"","", "-- Select  --", $selected );
            ?></td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="2" width="1255px" class="rpt_table">
        <thead>
            <th width="155px" class="must_entry_caption">&nbsp;Company</th>
            <th width="155px">Location</th>
            <th width="155px">Floor</th>
            <th width="155px">Division</th>
            <th width="155px">Department</th>
            <th width="155px">Section</th>
            <th width="155px">Table No</th>
        	<th width="155px" >Line No.</th>
        </thead>
        <tbody>
            <tr>
                <td align="center">
                <? 
                echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/employee_info_controller', this.value, 'load_drop_down_location_mst', 'location_td_mst');load_table();");
                
                    ?>


                </td>
                <td align="center" id="location_td_mst">
                <? 
                	echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected );
                ?>
                </td>
                <td align="center" id="floor_td_mst">
                <? 
                    echo create_drop_down( "cbo_floor_name", 150, $blank_array,"", 1, "-- Select Floor --", $selected );
                ?>
                </td>
                <td align="center" id="division_td_mst">
                <? 
				echo create_drop_down( "cbo_division_name", 150, $blank_array,"", 1, "-- Select Division --", $selected );
                ?>
                </td>
                <td align="center" id="department_td_mst">
                <? 
				echo create_drop_down( "cbo_dept_name", 150, $blank_array,"", 1, "-- Select Department --", $selected );
                ?>
                </td>
                <td align="center" id="section_td_mst">
                <? 
                echo create_drop_down( "cbo_section_name", 150,$blank_array ,"", 1, "-- Select Section --", $selected );
                ?>
               </td>
               <td align="center" id="td_table">
                <? 
                echo create_drop_down( "cbo_table_name", 150,$blank_array ,"", 1, "-- Select table No --", $selected );
                ?>
               </td>
               <td align="center" id="line_no_td_mst">
                <? 
                echo create_drop_down( "txt_line_no", 150,$blank_array ,"", 1, "-- Select Line --", $selected );
                ?>
                
                <!--<input type="text" id="txt_line_no" name="txt_line_no" class="text_boxes" style="width:165px;"/>-->
                </td>
            </tr>
            <tr>
                <td align="center" colspan="7" valign="middle" class="button_container">
                    <? 
                        echo load_submit_buttons( $permission, "fnc_emp_info", 0,0,"fnResetForm()",0);
                    ?>
                    <input type="button" value="Print Barcode" name="printBarcode" style="width:80px;" id="printBarcode" onclick="showEmpBarcode();" class="formbutton_disabled" />
                </td>
            </tr>
      </tbody>
    </table>
    <div id=""></div>
  </fieldset>
  </div>
  </form>
  <br>
  <fieldset style="width:1100px;">
  <form>
    <div style="width:1080px;" id="list_container">
    <?
	
	$sql = "select emp_code,id_card_no,(first_name||'  '||middle_name|| '  '||last_name) as emp_name,designation_id,line_no,company_id,location_id,floor_id,division_id,department_id,section_id,table_no,status_active,employee_status from lib_employee where status_active=1 and is_deleted=0  order by emp_code";
	
	//echo $sql; die;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
    $floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
	$division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name');
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
	$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name');
	$line_no_arr=return_library_array( "select id, line_name from  lib_sewing_line",'id','line_name');
	$designation_arr=return_library_array( "select id, custom_designation from lib_designation",'id','custom_designation');
    $table_arr=return_library_array( "select id,table_name from lib_table_entry",'id','table_name');

	
	$sample_arr = return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
    $status_arr=array(1=>"Active",2=>"Inactive");
    
	
	$arr=array(3=>$designation_arr,4=>$line_no_arr,5=>$company_arr,6=>$location_arr,7=>$floor_arr,8=>$division_arr,9=>$department_arr,10=>$section_arr,11=>$table_arr,12=>$status_arr);
	echo  create_list_view("list_view", "Emp Code,ID Card No,Employee Name,Designation,Line No,Company,Location,Floor,Division,Department,Section,Table,Status", "80,80,140,120,110,110,110,100,110,110,110,110,110","1450","260",0, $sql, "get_php_form_data", "emp_code", "'populate_master_from_data','requires/employee_info_controller'", 1, "0,0,0,designation_id,line_no,company_id,location_id,floor_id,division_id,department_id,section_id,table_no,employee_status", $arr , "emp_code,id_card_no,emp_name,designation_id,line_no,company_id,location_id,floor_id,division_id,department_id,section_id,table_no,employee_status", "employee_info_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0');
	
	?>
    </div>
  </form>
  </fieldset>

</div>
</body>
<script>
	set_multiselect('cbo_in_charge','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript">
</script>


	

</html>