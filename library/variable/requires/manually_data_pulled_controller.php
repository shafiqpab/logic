<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="on_change_data")
{
	
	$data_pulled_table_name=array(5=>"Section",6=>"Department",7=>"Division");
	asort($data_pulled_table_name);
	
	if ($data==1)
	{ 
  	?>
        <fieldset>
            <legend>Manually Table Data Pulled</legend>
             <div style="width:500px;" align="left">
                <table cellspacing="0" width="100%" >
                    <tr>
                       <td width="150" align="center" id="order_quantityStart">Table Name</td>
                       <td width="250">
                            <?php echo create_drop_down( "cbo_table_name", 240,$data_pulled_table_name,"", 1, "-- Select --", "", "clear_field();","","","","","3" ); ?>
                        </td>
                        
                    </tr>
                </table>
            </div>	
             <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                       <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                            <input type="button" name="process" class="formbutton" style="width:150px;" onClick="fnc_tna_process(7)" value="Start Process">
                        </td>					
                    </tr>
                    <tr><td colspan="4" id="missing_po"></td></tr>
                </table>
            </div>
        </fieldset>
	<?php
	}
}//end change on data condition

function special_character_remove($string)/*This is test purpose function. Reaz*/
{
	$specialCarFindArr=array("(",")",":","\\","/");
	$specialCarReflArr=array("[","]","","","");
	if($string !="")
	{
		$correctionDataArr = str_replace( $specialCarFindArr, $specialCarReflArr, $string );
		/*
		$correctionDataArr = "";
		$rowDataArr = explode("*",$string);
		foreach($rowDataArr as $val){
			$supplier_name = str_replace( $specialCarFindArr,$specialCarReflArr, $val );
			if($correctionDataArr) $correctionDataArr.="*";
			$correctionDataArr.=$supplier_name;
		}
		*/
		return $correctionDataArr; die;
	}
}



if ($action=="data_proces")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$new_conn=integration_params(2);
	
	
	/*$hrm_com_loc_div_dep_sec=sql_select( "select a.id as company_id, b.id as location_id, c.id as division_id, d.id as department_id, e.id as section_id    from lib_company a,  lib_location b,  lib_division c, lib_department d, lib_section e where a.id=b.company_id and b.id=c.location_id and c.id=d.division_id and d.id=e.department_id and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.is_deleted=0 and e.is_deleted=0 order by a.id, b.id,c.id,d.id,e.id",'',$new_conn );
	foreach($hrm_com_loc_div_dep_sec as $v){
		$com_lib_arr[$v[csf('location_id')]]= $v[csf('company_id')];
		$div_lib_arr[$v[csf('company_id')]]= $v[csf('division_id')];
		$dept_lib_arr[$v[csf('division_id')]]= $v[csf('department_id')];
		$sec_lib_arr[$v[csf('department_id')]]= $v[csf('section_id')];
		
	}*/
	
	$hrm_com_loc_div_dep_sec=sql_select( "select a.id as company_id, b.id as location_id  from lib_company a,  lib_location b where a.id=b.company_id  and a.is_deleted=0 and b.is_deleted=0  order by a.id, b.id",'',$new_conn );
	foreach($hrm_com_loc_div_dep_sec as $v){
		$com_lib_arr[$v[csf('location_id')]]= $v[csf('company_id')];
	}
	
	
	if($cbo_table_name==5) //Section
	{
		
		$already_pulled_ids= return_library_array("SELECT id FROM lib_section_test WHERE is_deleted=0 order by id","id","id");
		//echo "10**";print_r($already_pulled_ids);die;
		
		$field_array="id,section_name,department_id,remark,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted,is_locked";
		$nameArray=sql_select( "select * from lib_section where is_deleted=0 order by id",'',$new_conn );
		foreach ($nameArray as $row)
		{
			if( !in_array($row[csf("id")],$already_pulled_ids))
			{
				if($data_array!="") $data_array.=",";
				$data_array.="(".$row[csf("id")].",'".$row[csf("section_name")]."','".$row[csf("department_id")]."','".$row[csf("remark")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."','".$row[csf("is_locked")]."')";
			}
			
		}
		//echo "10**$data_array";die;
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$rID5=$d5=1;
		if($data_array!="")
		{
			//$d5=execute_query( "delete from lib_section_test" ); //lib_section
			$rID5=sql_insert("lib_section_test",$field_array,$data_array,0); //lib_section
		}
		
		
		if($db_type==0)
		{
			if($rID5==1 && $d5==1){
				
				mysql_query("COMMIT"); 
				echo "0**Section";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**Section";
			}
		}
		else if($db_type==2)
		{
			if($rID5==1 && $d5==1)
			{
				oci_commit($con);
				echo "0**Section";
			}
			else
			{
				oci_rollback($con);
				echo "10**Section";
			}
		}
	}
	else if($cbo_table_name==6)	//Department
	{
		
		$already_pulled_ids= return_library_array("SELECT id FROM lib_department_test WHERE is_deleted=0 order by id","id","id");
		$field_array="id,department_name,division_id,contact_person,contact_no,country_id,website,email,short_name,address,remark,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted,is_locked";
		
		$nameArray=sql_select( "select * from lib_department where is_deleted=0 order by id",'',$new_conn );
		
		foreach ($nameArray as $row)
		{
			if( !in_array($row[csf("id")],$already_pulled_ids))
			{
				if($data_array!="") $data_array.=",";
				$data_array.="(".$row[csf("id")].",'".$row[csf("department_name")]."','".$row[csf("division_id")]."','".$row[csf("contact_person")]."','".$row[csf("contact_no")]."','".$row[csf("country_id")]."','".$row[csf("website")]."','".$row[csf("email")]."','".$row[csf("short_name")]."','".$row[csf("address")]."','".$row[csf("remark")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."','".$row[csf("is_locked")]."')";
			}
			
		}
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$rID6=$d6=1;
		if($data_array!="")
		{
			//$d6=execute_query( "delete from lib_department_test" ); //lib_department
			$rID6=sql_insert("lib_department_test",$field_array,$data_array,0); //lib_department
		}
		
		if($db_type==0)
		{
			if($rID6==1){
				
				mysql_query("COMMIT"); 
				echo "0**Department";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**Department";
			}
		}
		else if($db_type==2)
		{
			if($rID6==1)
			{
				oci_commit($con);
				echo "0**Department";
			}
			else
			{
				oci_rollback($con);
				echo "10**Department";
			}
		}
	}
	else if($cbo_table_name==7)	//Division 
	{
		
		$already_pulled_ids= return_library_array("SELECT id FROM lib_division_test WHERE is_deleted=0 order by id","id","id");
		
		$field_array="id,company_id,division_name,location_id,remark,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted,is_locked";
		
		$nameArray=sql_select( "select * from lib_division where is_deleted=0 order by id",'',$new_conn );
		foreach ($nameArray as $row)
		{
			if( !in_array($row[csf("id")],$already_pulled_ids))
			{
				if($data_array!="") $data_array.=",";
				$data_array.="(".$row[csf("id")].",'".$com_lib_arr[$row[csf("location_id")]]."','".$row[csf("division_name")]."','".$row[csf("location_id")]."','".$row[csf("remark")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."','".$row[csf("is_locked")]."')";
			}
			
		}
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//echo "10**insert into lib_division($field_array)values".$data_array;die;	
		$rID7=$d7=1;
		if($data_array!="")
		{
			//$d7		= execute_query( "delete from lib_division_test" );//lib_division
			$rID7	= sql_insert("lib_division_test",$field_array,$data_array,0);//lib_division
		}
		
		if($db_type==0)
		{
			if($rID7==1){
				
				mysql_query("COMMIT"); 
				echo "0**Division";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**Division";

			}
		}
		else if($db_type==2)
		{
			if($rID7==1)
			{
				oci_commit($con);
				echo "0**Division";
			}
			else
			{
				oci_rollback($con);
				echo "10**Division";
			}
		}
	}
	disconnect($con);	
	die;
}
?>