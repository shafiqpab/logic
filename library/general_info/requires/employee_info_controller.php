<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



//system id popup here----------------------//

if ($action=="load_drop_down_location_mst")
{
   echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id=$data and status_active=1 and is_deleted=0","id,location_name", 1, "-- Select Location --", $selected,"load_drop_down('requires/employee_info_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_division_mst', 'division_td_mst');load_drop_down('requires/employee_info_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_floor', 'floor_td_mst');load_table()" );
}



if ($action=="load_drop_down_division_mst")
{
	$data = explode("_",$data);
	//echo $data[0]; die;
   echo create_drop_down( "cbo_division_name", 150, "select id,division_name from lib_division where company_id=$data[0] and status_active=1 and is_deleted=0","id,division_name", 1, "-- Select Division --", $selected,"load_drop_down( 'requires/employee_info_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_department_mst', 'department_td_mst');load_table();" );
}


if ($action=="load_drop_down_floor")
{
	$data = explode("_",$data);
	//print_r($data);die();
	$sql="select id,floor_name from lib_prod_floor where company_id='$data[0]' and location_id='$data[1]' and status_active =1 and is_deleted=0 order by floor_name";
    echo create_drop_down( "cbo_floor_name", 150, $sql,"id,floor_name", 1, "-- Select Floor --", $selected,"load_table();");
	//echo $sql;die();
}

if ($action=="load_drop_down_table")
{
	$data = explode("_",$data);
	//print_r($data);
	$localtion = str_replace("'","",$data[1]);
	$floor = str_replace("'","",$data[2]);
	//print_r($floor);die();
	$location_cond = "";
	if(!empty($localtion))
	{
		$location_cond = " and location_name=$data[1]";
	}
	//echo $location_cond;die();
	$floor_cond = "";
	if(!empty($floor))
	{
		$floor_cond = " and floor_name=$data[2]";
	}
	$sql="select id,table_name from lib_table_entry where company_name='$data[0]' $location_cond $floor_cond and status_active =1 and table_type =1 and is_deleted=0 order by table_name";
    echo create_drop_down( "cbo_table_name", 150, $sql,"id,table_name", 1, "-- Select Table No --", $selected );
	//echo $sql;die();
	
}




if ($action=="load_drop_down_department_mst")
{
	$data = explode("_",$data);
  echo create_drop_down( "cbo_dept_name", 150, "select id,department_name from lib_department where division_id=$data[1] and status_active=1 and is_deleted=0","id,department_name", 1, "-- Select Department --", $selected,"load_drop_down( 'requires/employee_info_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_section_mst', 'section_td_mst');" ); 
}

if ($action=="load_drop_down_section_mst")
{
	$data = explode("_",$data);
  echo create_drop_down( "cbo_section_name", 150, "select id,section_name from lib_section where department_id=$data[1] and status_active=1 and is_deleted=0","id,section_name", 1, "-- Select Section --", $selected,"load_drop_down( 'requires/employee_info_controller',document.getElementById('cbo_company_name').value, 'load_drop_down_line_mst', 'line_no_td_mst');" ); 
}

if ($action=="load_drop_down_line_mst")
{
  echo create_drop_down( "txt_line_no", 150, "select id,line_name from lib_sewing_line where company_name='$data' and status_active=1 and is_deleted=0 order by company_name","id,line_name", 1, "-- Select Line --", $selected );
}


 
if ($action=="load_drop_down_location")
{
   echo create_drop_down( "cbo_location_name", 135, "select id,location_name from lib_location where company_id=$data and status_active=1 and is_deleted=0","id,location_name", 1, "-- Select Location --", $selected,"load_drop_down( 'employee_info_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_division', 'division_td');" );
}

if ($action=="load_drop_down_division")
{
   echo create_drop_down( "cbo_division_name", 135, "select id,division_name from lib_division where company_id=$data and status_active=1 and is_deleted=0","id,division_name", 1, "-- Select Division --", $selected,"load_drop_down( 'employee_info_controller',this.value, 'load_drop_down_department', 'department_td');" );
}

if ($action=="load_drop_down_department")
{
   echo create_drop_down( "cbo_dept_name", 135, "select id,department_name from lib_department where division_id=$data and status_active=1 and is_deleted=0","id,department_name", 1, "-- Select Department --", $selected,"load_drop_down( 'employee_info_controller',this.value, 'load_drop_down_section', 'section_td');" );
}

if ($action=="load_drop_down_section")
{
   echo create_drop_down( "cbo_section_name", 135, "select id,section_name from lib_section where department_id=$data and status_active=1 and is_deleted=0","id,section_name", 1, "-- Select Section --", $selected,"load_drop_down( 'employee_info_controller',document.getElementById('cbo_company_name').value, 'load_drop_down_line', 'line_no_td');" );
}

if ($action=="load_drop_down_line")
{
  echo create_drop_down( "txt_line_no", 135, "select id,line_name from lib_sewing_line where company_name=$data and status_active=1 and is_deleted=0 order by company_name","id,line_name", 1, "-- Select Line --", $selected ); 
}




if ($action=="emp_id_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(str)
	{
		$("#hidden_emp_number").val(str);
		parent.emailwindow.hide(); 
	}
</script>
</head>
<body>
    <div align="center" style="width:100%;" >
    <form name="search_emp_1"  id="search_emp_1" autocomplete="off">
    
        <table width="1060" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <th width="160" align="center">Company</th>
                <th width="135" align="center">Location</th>
                <th width="135" align="center">Division</th>
                <th width="135" align="center">Department</th>
                <th width="135" align="center">Section</th>
                <th width="135" align="center">Line NO</th>
            	<th width="135" align="center">Employee Code</th>
                <th width="90" align="center"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
            </thead>
        <tbody>
            <tr>
                 <td>
                 
					<? 
                    	echo create_drop_down( "cbo_company_name", 155, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected ,"load_drop_down( 'employee_info_controller', this.value, 'load_drop_down_location', 'location_td');");
                    ?>
                </td>
                 <td id="location_td">
					 <? 
						echo create_drop_down( "cbo_location_name", 135, $blank_array,"", 1, "-- Select Location --", $selected );
                    ?>
                </td>
                 <td id="division_td">
					 <? 
                    	echo create_drop_down( "cbo_division_name", 135,$blank_array ,"", 1, "-- Select Division --", $selected );
                    ?>
                </td> 
                <td id="department_td">
					<? 
						echo create_drop_down( "cbo_dept_name", 135,$blank_array ,"", 1, "-- Select Department --", $selected );
                    ?>
                </td>   
                <td id="section_td">
					<? 
						echo create_drop_down( "cbo_section_name", 135,$blank_array ,"", 1, "-- Select Section --", $selected );
                    ?>
                </td>
                <td id="line_no_td">				
					<? 
						echo create_drop_down( "txt_line_no", 135,$blank_array ,"", 1, "-- Select Line --", $selected );
                    ?>                   
                </td>
                <td>
					<input type="text" id="src_emp_code" name="src_emp_code" class="text_boxes" style="width:135px;" >
                </td> 
                <td>
                	<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_division_name').value+'_'+document.getElementById('cbo_dept_name').value+'_'+document.getElementById('cbo_section_name').value+'_'+document.getElementById('txt_line_no').value+'_'+document.getElementById('src_emp_code').value, 'create_emp_search_list_view', 'search_div', 'employee_info_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                </td>
            </tr>    
        </tbody>
         <input type="hidden" id="hidden_emp_number"  />
        </table>    
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_emp_search_list_view")
{
	$ex_data = explode("_",$data);
	$company = $ex_data[0];
	$location = $ex_data[1];
	$division = $ex_data[2];
	$department = $ex_data[3];
	$section = $ex_data[4];
	$line_no = $ex_data[5];
	$emp_code = $ex_data[6];


 	//$sql_cond="";
	if( $company!=0 )  $company=" and company_id=$company"; else  $company="";
	if( $location!=0 )  $location=" and location_id=$location"; else  $location="";
	if( $division!=0 )  $division=" and division_id=$division"; else  $division="";
	if( $department!=0 )  $department=" and department_id=$department"; else  $department="";
	if( $section!=0 )  $section=" and section_id=$section"; else  $section="";
	if( $line_no!=0 )  $line_no=" and line_no=$line_no"; else  $line_no="";
	if( $emp_code!=0 )  $emp_code=" and emp_code=$emp_code"; else  $emp_code="";
	
	/*$sql = "select emp_code,id_card_no,  designation_id, line_no, company_id, location_id, division_id,department_id,section_id from lib_employee	where status_active=1 and is_deleted=0  $company $location  $division $department $section $line_no $emp_code ";*/
	
	if($db_type==2 || $db_type==1 )
    	{
	      $sql = "select emp_code,id_card_no,(first_name||' '||middle_name|| '  ' || last_name) as emp_name,designation_id, line_no, company_id, location_id, division_id,department_id,section_id from lib_employee where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $line_no $emp_code";
	    }
		if($db_type==0)
		{
		  $sql = "select emp_code,id_card_no, concat(first_name,'  ',middle_name,last_name) as emp_name, designation_id, line_no, company_id, location_id, division_id,department_id,section_id from lib_employee where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $line_no $emp_code";
			
		}
		
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name');
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
	$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name');
	$line_no_arr=return_library_array( "select id, line_name from  lib_sewing_line",'id','line_name');
	$designation_arr=return_library_array( "select id, custom_designation from lib_designation",'id','custom_designation');
	
	$sample_arr = return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
	$arr=array(3=>$designation_arr,4=>$line_no_arr,5=>$company_arr,6=>$location_arr,7=>$division_arr,8=>$department_arr,9=>$section_arr);
	//function create_list_view( $table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr,  $show_sl, $field_printed_from_array_arr,  $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all )
		
		echo  create_list_view("list_view", "Emp Code,ID Card,Employee Name,Designation,Line No,Company,Location,Division,Department,Section", "80,140,120,110,110,110,110,110,110,80","1150","260",0, $sql, "js_set_value", "emp_code", "", 1, "0,0,0,designation_id,line_no,company_id,location_id,division_id,department_id,section_id", $arr , "emp_code,id_card_no,emp_name,designation_id,line_no,company_id,location_id,division_id,department_id,section_id", "employee_info_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0') ;
	exit();
}

 
if($action=="populate_master_from_data")
{
	
	$sql="select id,emp_code,first_name,middle_name,last_name,full_name_bangla,father_name,father_name_ban,mother_name,mother_name_ban,sex,birth_place,dob,age,religion,marital_status,blood_group,nationality,national_id,passport_id,emp_catagory,designation_level,designation_id,functional_sup,admin_sup,id_card_no,joining_date,confirmation_date,punch_card_no,remark,company_id,location_id,division_id,department_id,section_id,line_no,floor_id,table_no,status_active,in_charge,employee_status,operator,mobile_number from lib_employee  where emp_code=$data";
	//echo $sql;die();
	$res = sql_select($sql);
	foreach($res as $row)
	{		
		echo "document.getElementById('txt_emp_code').value = '".($row[csf("emp_code")])."';\n";
		echo "document.getElementById('txt_emp_name_fst').value = '".($row[csf("first_name")])."';\n";
		echo "document.getElementById('txt_emp_name_sec').value = '".($row[csf("middle_name")])."';\n";
		echo "document.getElementById('txt_emp_name_thir').value = '".($row[csf("last_name")])."';\n";
		echo "document.getElementById('txt_emp_name_ban').value = '".($row[csf("full_name_bangla")])."';\n";
		echo "document.getElementById('txt_father_name').value = '".($row[csf("father_name")])."';\n";
		echo "document.getElementById('txt_father_name_ban').value = '".($row[csf("father_name_ban")])."';\n";
		echo "document.getElementById('txt_mother_name').value = '".($row[csf("mother_name")])."';\n";
		echo "document.getElementById('txt_mother_name_ban').value = '".($row[csf("mother_name_ban")])."';\n";
		echo "document.getElementById('cbo_sex').value = '".($row[csf("sex")])."';\n";
		echo "document.getElementById('txt_birth_pla').value = '".($row[csf("birth_place")])."';\n";
		echo "document.getElementById('txt_dob').value = '".(change_date_format($row[csf("dob")]))."';\n";
		echo "document.getElementById('txt_age').value = '".($row[csf("age")])."';\n";
		echo "document.getElementById('cbo_religion').value = '".($row[csf("religion")])."';\n";
		echo "document.getElementById('cbo_marry').value = '".($row[csf("marital_status")])."';\n";
		echo "document.getElementById('cbo_blood_grp').value = '".($row[csf("blood_group")])."';\n";
		echo "document.getElementById('txt_natinality').value = '".($row[csf("nationality")])."';\n";
		echo "document.getElementById('txt_nation_id').value = '".($row[csf("national_id")])."';\n";
		echo "document.getElementById('txt_pass_no').value = '".($row[csf("passport_id")])."';\n";
		echo "document.getElementById('cbo_emp_cata').value = '".($row[csf("emp_catagory")])."';\n";
		echo "document.getElementById('cbo_status').value = '".($row[csf("status_active")])."';\n";
		echo "document.getElementById('cbo_design_lbl').value = '".($row[csf("designation_level")])."';\n";
		echo "document.getElementById('cbo_design').value = '".($row[csf("designation_id")])."';\n";
		echo "document.getElementById('cbo_function_sup').value = '".($row[csf("functional_sup")])."';\n";
		echo "document.getElementById('cbo_admin_sup').value = '".($row[csf("admin_sup")])."';\n";
		echo "document.getElementById('txt_id_card_no').value = '".($row[csf("id_card_no")])."';\n";
		echo "document.getElementById('txt_join_data').value = '".(change_date_format($row[csf("joining_date")]))."';\n";
		echo "document.getElementById('txt_con_data').value = '".(change_date_format($row[csf("confirmation_date")]))."';\n";
		echo "document.getElementById('txt_panch_ca_no').value = '".($row[csf("punch_card_no")])."';\n";
		echo "document.getElementById('txt_remarks').value = '".($row[csf("remark")])."';\n";
		echo "document.getElementById('cbo_status').value = '".($row[csf("employee_status")])."';\n";


		echo "document.getElementById('cbo_operator').value = '".($row[csf("operator")])."';\n";
		echo "document.getElementById('txt_mob_no').value = '".($row[csf("mobile_number")])."';\n";

		  
		//echo "document.getElementById('cbo_table_name').value = '".($row[csf("table_no")])."';\n";
		echo "document.getElementById('cbo_company_name').value = '".($row[csf("company_id")])."';\n";
		echo "document.getElementById('cbo_company_name').value = '".($row[csf("company_id")])."';\n";
		echo "load_drop_down( 'requires/employee_info_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location_mst', 'location_td_mst' );\n";
		//echo "load_drop_down( 'requires/employee_info_controller', document.getElementById('cbo_floor_name').value, 'load_drop_down_floor', 'floor_td_mst' );\n";
		echo "load_drop_down('requires/employee_info_controller','".$row[csf("company_id")]."' +'_'+'".$row[csf("location_id")]."', 'load_drop_down_floor', 'floor_td_mst');\n";

		 echo "load_drop_down('requires/employee_info_controller','".$row[csf("company_id")]."' +'_'+'".$row[csf("location_id")]."' +'_'+'".$row[csf("floor_id")]."', 'load_drop_down_table', 'td_table',);\n";
		echo "document.getElementById('cbo_table_name').value = '".$row[csf("table_no")]."';\n";


		echo "document.getElementById('cbo_floor_name').value = '".$row[csf("floor_id")]."';\n";

		echo "document.getElementById('cbo_location_name').value = '".($row[csf("location_id")])."';\n";
		echo "load_drop_down( 'requires/employee_info_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_division_mst', 'division_td_mst' );\n";

		echo "document.getElementById('cbo_division_name').value = '".($row[csf("division_id")])."';\n";
		echo "load_drop_down( 'requires/employee_info_controller',document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_division_name').value, 'load_drop_down_department_mst', 'department_td_mst');\n";
		echo "document.getElementById('cbo_dept_name').value = '".($row[csf("department_id")])."';\n";
		echo "load_drop_down( 'requires/employee_info_controller',document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_dept_name').value, 'load_drop_down_section_mst', 'section_td_mst');\n";
		echo "document.getElementById('cbo_section_name').value = '".($row[csf("section_id")])."';\n";
		echo "load_drop_down( 'requires/employee_info_controller',document.getElementById('cbo_company_name').value, 'load_drop_down_line_mst', 'line_no_td_mst');\n";
		echo "document.getElementById('txt_line_no').value = '".($row[csf("line_no")])."';\n";
		echo "load_drop_down( 'requires/employee_info_controller',document.getElementById('cbo_company_name').value, 'load_drop_down_line_mst', 'line_no_td_mst');\n";
		echo "document.getElementById('txt_line_no').value = '".($row[csf("line_no")])."';\n";
		echo "document.getElementById('update_id').value = '".($row[csf("line_no")])."';\n";
		echo "$('#update_id').val('".$row[csf("id")]."');\n";
		echo "document.getElementById('printBarcode').removeAttribute('class');\n";
        echo "document.getElementById('printBarcode').setAttribute('class', 'formbutton');\n";
		echo "document.getElementById('cbo_in_charge').value = '".($row[csf("in_charge")])."';\n";
		echo "set_multiselect('cbo_in_charge','0','1','".($row[csf("in_charge")])."','0');\n";
		echo "set_button_status(1, permission, 'fnc_emp_info',1,1);";
		// echo "set_multiselect('cbo_in_charge','0','1','".($inf[csf("in_charge")])."','0');\n"; 
	}
	exit();	
}
//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		//echo $cbo_floor_name;
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN");}
		
		//echo $txt_id_card_no;die;
		
		$duplicate=is_duplicate_field("id_card_no","lib_employee","id_card_no=$txt_id_card_no and company_id=$cbo_company_name and status_active=1 and is_deleted=0");
		if($duplicate==1)
		{
			echo "11**This supplier is exist for same item of this requisition.";
			disconnect($con);
			exit;
		}
		// txt_mob_no cbo_operator     operator mobile_number

		$flag=0;
		if(str_replace("'","",$update_id)=="")
		{
			$mst_id= return_next_id("id","lib_employee",1);
			$field_array_mst="id,emp_code,first_name,middle_name,last_name,full_name_bangla,father_name,father_name_ban,mother_name,mother_name_ban,sex,birth_place,dob,age,religion,marital_status,blood_group,nationality,national_id,passport_id,emp_catagory,designation_level,designation_id,functional_sup,admin_sup,id_card_no,joining_date,confirmation_date,punch_card_no,remark,company_id,location_id,division_id,department_id,section_id,line_no,floor_id,table_no,in_charge,inserted_by,insert_date,status_active,is_deleted,employee_status,operator,mobile_number";
			$data_array_mst="(".$mst_id.",".$mst_id.",".$txt_emp_name_fst.",".$txt_emp_name_sec.",".$txt_emp_name_thir.",".$txt_emp_name_ban.",".$txt_father_name.",".$txt_father_name_ban.",".$txt_mother_name.",".$txt_mother_name_ban.",".$cbo_sex.",".$txt_birth_pla.",".$txt_dob.",".$txt_age.",".$cbo_religion.",".$cbo_marry.",".$cbo_blood_grp.",".$txt_natinality.",".$txt_nation_id.",".$txt_pass_no.",".$cbo_emp_cata.",".$cbo_design_lbl.",".$cbo_design.",".$cbo_function_sup.",".$cbo_admin_sup.",".$txt_id_card_no.",".$txt_join_data.",".$txt_con_data.",".$txt_panch_ca_no.",".$txt_remarks.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_division_name.",".$cbo_dept_name.",".$cbo_section_name.",".$txt_line_no.",".$cbo_floor_name.",".$cbo_table_name.",".$cbo_in_charge.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,".$cbo_status.",".$cbo_operator.",".$txt_mob_no.")";
		
			$rID=sql_insert("lib_employee",$field_array_mst,$data_array_mst,1);
		}
		else
		{
			$mst_id=str_replace("'",'',$update_id);
			$field_array_mst="emp_code*first_name*middle_name*last_name*full_name_bangla*father_name*father_name_ban*mother_name*mother_name_ban*sex*birth_place*dob*age*religion*marital_status*blood_group*nationality*national_id*passport_id*emp_catagory*designation_level*designation_id*functional_sup*admin_sup*id_card_no*joining_date*confirmation_date*punch_card_no*remark*company_id*location_id*division_id*department_id*section_id*line_no*floor_id*table_no*contract_start_date*contract_end_date*updated_by*update_date*status_active*is_deleted*operator*mobile_number";
			$data_array_mst="".$mst_id."*".$txt_emp_name_fst."*".$txt_emp_name_sec."*".$txt_emp_name_thir."*".$txt_emp_name_ban."*".$txt_father_name."*".$txt_father_name_ban."*".$txt_mother_name."*".$txt_mother_name_ban."*".$cbo_sex."*".$txt_birth_pla."*".$txt_dob."*".$txt_age."*".$cbo_religion."*".$cbo_marry."*".$cbo_blood_grp."*".$txt_natinality."*".$txt_nation_id."*".$txt_pass_no."*".$cbo_emp_cata."*".$cbo_design_lbl."*".$cbo_design."*".$cbo_function_sup."*".$cbo_admin_sup."*".$txt_id_card_no."*".$txt_join_data."*".$txt_con_data."*".$txt_panch_ca_no."*".$txt_remarks."*".$cbo_company_name."*".$cbo_location_name."*".$cbo_division_name."*".$cbo_dept_name."*".$cbo_section_name."*".$txt_line_no."*".$cbo_floor_name."*".$cbo_table_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0*".$cbo_operator."*".$txt_mob_no."";
			 
			$rID=sql_update("lib_employee",$field_array_mst,$data_array_mst,"id",$update_id,1);
		}
		
		if($db_type==0)
		{
			if($rID)
			{
			   mysql_query("COMMIT");  
			   echo 0;
			}
			else
			{
			   mysql_query("ROLLBACK"); 
			   echo 10;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
		if($rID)
			{
			   oci_commit($con);  
			   echo 0;
			}
		else
			{
			   oci_rollback($con); 
			   echo 10;
			}
		}
		disconnect($con);
		die;
	}

	else if ($operation==1)   // Update Here=============================================================================
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$duplicate=is_duplicate_field("id_card_no","lib_employee","id!=$update_id and id_card_no=$txt_id_card_no and company_id=$cbo_company_name and status_active=1 and is_deleted=0");
		if($duplicate==1)
		{
			echo "11**This supplier is exist for same item of this requisition.";
			disconnect($con);
			exit;
		}
		
		if(str_replace("'",'',$update_id)!="")
		{
			$mst_id=str_replace("'",'',$update_id);
			$field_array_up="first_name*middle_name*last_name*full_name_bangla*father_name*father_name_ban*mother_name*mother_name_ban*sex*birth_place*dob*age*religion*marital_status*blood_group*nationality*national_id*passport_id*emp_catagory*designation_level*designation_id*functional_sup*admin_sup*id_card_no*joining_date*confirmation_date*punch_card_no*remark*company_id*location_id*floor_id*division_id*department_id*section_id*line_no*table_no*in_charge*contract_start_date*contract_end_date*updated_by*update_date*status_active*is_deleted*employee_status*operator*mobile_number";
			$data_array_up="".$txt_emp_name_fst."*".$txt_emp_name_sec."*".$txt_emp_name_thir."*".$txt_emp_name_ban."*".$txt_father_name."*".$txt_father_name_ban."*".$txt_mother_name."*".$txt_mother_name_ban."*".$cbo_sex."*".$txt_birth_pla."*".$txt_dob."*".$txt_age."*".$cbo_religion."*".$cbo_marry."*".$cbo_blood_grp."*".$txt_natinality."*".$txt_nation_id."*".$txt_pass_no."*".$cbo_emp_cata."*".$cbo_design_lbl."*".$cbo_design."*".$cbo_function_sup."*".$cbo_admin_sup."*".$txt_id_card_no."*".$txt_join_data."*".$txt_con_data."*".$txt_panch_ca_no."*".$txt_remarks."*".$cbo_company_name."*".$cbo_location_name."*".$cbo_floor_name."*".$cbo_division_name."*".$cbo_dept_name."*".$cbo_section_name."*".$txt_line_no."*".$cbo_table_name."*".$cbo_in_charge."*'".''."'*'".''."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0*".$cbo_status."*".$cbo_operator."*".$txt_mob_no."";
		}
		$rID = sql_update("lib_employee",$field_array_up,$data_array_up,"id",$update_id,1);
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo 1;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo 10;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			 if($rID )
			    {
					oci_commit($con);   
					echo 1;
				}
				else{
					oci_rollback($con);
					echo 10;
				}
		}
		disconnect($con);
		die;
		
	}
}



if($action=="employee_list_view")
{

	$sql = "select emp_code,id_card_no,(first_name||'  '||middle_name|| '  '||last_name) as emp_name,designation_id,line_no,company_id,location_id,floor_id,division_id,department_id,section_id,table_no,status_active from lib_employee where status_active=1 and is_deleted=0  order by emp_code";
	
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
	//$arr=array(2=>$designation_arr,3=>$line_no_arr,4=>$company_arr,5=>$location_arr,6=>$division_arr,7=>$department_arr,8=>$section_arr);
	$arr=array(3=>$designation_arr,4=>$line_no_arr,5=>$company_arr,6=>$location_arr,7=>$floor_arr,8=>$division_arr,9=>$department_arr,10=>$section_arr,11=>$table_arr,12=>$status_arr);
	echo  create_list_view("list_view", "Emp Code,ID Card No,Employee Name,Designation,Line No,Company,Location,Floor,Division,Department,Section,Table,Status", "80,80,140,120,110,110,110,100,110,110,110,110,110","1450","260",0, $sql, "get_php_form_data", "emp_code", "'populate_master_from_data','requires/employee_info_controller'", 1, "0,0,0,designation_id,line_no,company_id,location_id,floor_id,division_id,department_id,section_id,table_no,status_active", $arr , "emp_code,id_card_no,emp_name,designation_id,line_no,company_id,location_id,floor_id,division_id,department_id,section_id,table_no,status_active", "employee_info_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0');
	

}

if($action == 'print_barcode_employee') {
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code128.php');
	// require('../../../ext_resource/pdf/code39.php');

	$userid = $_SESSION['logic_erp']['user_id'];

	$data=explode("***",$data);
	$emp_id=$data[0];

	$employeeData = sql_select("select (first_name||'  '|| middle_name|| '  '||last_name) as emp_name, id_card_no from lib_employee where id=$emp_id");

	$pdf=new PDF_Code128('P', 'mm', array(70,40));
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(false);
	$pdf->SetFont('Arial', '', 7);

	$pdf->SetXY(5, 5);
	$pdf->Write(5, "Name: {$employeeData[0][csf('emp_name')]}");
	$pdf->SetXY(5, 10);
	$pdf->Write(5, "ID: {$employeeData[0][csf('id_card_no')]}");
	$pdf->SetXY(5, 18);
	$pdf->Code128(5, 18, $employeeData[0][csf('id_card_no')], 60, 10);
	// $pdf->Code39(5, 15, $employeeData[0][csf('id_card_no')]);
	$pdf->SetXY(5, 30);
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->Write(5, "{$employeeData[0][csf('id_card_no')]}");

	foreach (glob("*".$userid.".pdf") as $filename) {
		if(file_exists($filename)) {
			@unlink($filename);
		}
	}
	$name ='emp_barcode_'.date('j-M-Y_h-iA').'_'.$userid.'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

?>       