<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 140, "select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name","id,location_name", 1, "--Select Location--", "","load_drop_down( 'requires/incentive_scheme_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_department', 'department_td')");
}


if ($action=="load_drop_down_department")
{	
		
	if($db_type==2 || $db_type==1 )// for Oracle 
	{
	$data=explode('_',$data);
	echo create_drop_down( "cbo_department_id", 140, "select distinct a.id,a.department_name,b.department_id from  lib_department a,lib_employee b where b.company_id='$data[0]' and b.location_id='$data[1]' and a.id=b.department_id and a.is_deleted=0  and a.status_active=1  order by a.department_name",'id,department_name', 1, '--Select Department--', 0, "load_drop_down( 'requires/incentive_scheme_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_id').value+'_'+this.value, 'load_drop_down_section', 'section_td' )");
	}
	else if($db_type==0) // For mysql
	{ 
			$data=explode('_',$data);
	echo create_drop_down( "cbo_department_id", 140, "select a.id,a.department_name,b.department_id from  lib_department a,lib_employee b where b.company_id='$data[0]' and b.location_id='$data[1]' and a.id=b.department_id and a.is_deleted=0  and a.status_active=1 group by b.department_id order by a.department_name",'id,department_name', 1, '--Select Department--', 0, "load_drop_down( 'requires/incentive_scheme_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_id').value+'_'+this.value, 'load_drop_down_section', 'section_td' )");
		
	}
}//group by b.department_id

if ($action=="load_drop_down_section")
{	if($db_type==2 || $db_type==1 )// for Oracle
	{
	$data=explode('_',$data);
	echo create_drop_down( "cbo_section_id", 140, "select distinct a.id,a.section_name,b.section_id from  lib_section a,lib_employee b where b.company_id='$data[0]' and b.location_id='$data[1]' and b.department_id='$data[2]' and a.is_deleted=0 and a.status_active=1 order by a.section_name",'id,section_name', 1, '--Select Section--', 0, "load_drop_down( 'requires/incentive_scheme_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_id').value+'_'+document.getElementById('cbo_department_id').value+'_'+this.value, 'load_drop_down_designation', 'designation_td' )");
	}
	else if($db_type==0)
	{
		
		$data=explode('_',$data);
	echo create_drop_down( "cbo_section_id", 140, "select a.id,a.section_name,b.section_id from  lib_section a,lib_employee b where b.company_id='$data[0]' and b.location_id='$data[1]' and b.department_id='$data[2]' and a.is_deleted=0 and a.status_active=1 group by b.section_id order by a.section_name",'id,section_name', 1, '--Select Section--', 0, "load_drop_down( 'requires/incentive_scheme_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_id').value+'_'+document.getElementById('cbo_department_id').value+'_'+this.value, 'load_drop_down_designation', 'designation_td' )");
	}
}//group by b.section_id

if ($action=="load_drop_down_designation")
{
	if($db_type==2 || $db_type==1 )// for Oracle
	{
	$data=explode('_',$data);
	echo create_drop_down( "cbo_designation_id", 140, "select distinct a.id,a.custom_designation,b.designation_id from  lib_designation a, lib_employee b where  a.id=b.designation_id  and a.is_deleted=0  and a.status_active=1 order by a.custom_designation",'id,custom_designation', 1, '--Select Designation--', 0, "");//b.company_id='$data[0]' and b.location_id='$data[1]' and b.department_id='$data[2]' and b.section_id='$data[3]' and 
	//group by b.designation_id 
	}
	else if($db_type==0)	//for mysql
	{
		$data=explode('_',$data);
	echo create_drop_down( "cbo_designation_id", 140, "select a.id,a.custom_designation,b.designation_id from  lib_designation a, lib_employee b where  a.id=b.designation_id  and a.is_deleted=0  and a.status_active=1 group by b.designation_id order by a.custom_designation",'id,custom_designation', 1, '--Select Designation--', 0, "");//b.company_id='$data[0]' and b.location_id='$data[1]' and b.department_id='$data[2]' and b.section_id='$data[3]' and 
	//group by b.designation_id 
	}
}

if ($action=="show_dtls_list_view")
{
	$data=explode('__',$data);
	$sql_data = "select id, company_id, location_id, department_id, section_id, designation_id  from lib_incentive_scheme_mst where company_id=$data[0]  and status_active=1 and is_deleted=0 $sql_cond";//and designation_id=$data[4]
	//echo $sql_data;
	$sql_data_exe=sql_select($sql_data);
	$companyArr=return_library_array( "select id,company_name from lib_company", "id", "company_name" );
	$locationArr=return_library_array( "select id,location_name from lib_location", "id", "location_name" );
	$departmentArr=return_library_array( "select id,department_name from lib_department", "id", "department_name" );
	$sectionArr=return_library_array( "select id,section_name from  lib_section", "id", "section_name" );
	$designationArr=return_library_array( "select id,custom_designation from lib_designation", "id", "custom_designation" );
	
	 $arr=array(0=>$companyArr,1=>$locationArr,2=>$departmentArr,3=>$sectionArr,4=>$designationArr);  
	 
     echo  create_list_view ( "list_view", "Company,Location,Department,Section,Designation", "100,100,100,100,150","600","250",1,$sql_data, "get_php_form_data", "id","'load_php_data_to_form'", 1, "company_id,location_id,department_id,section_id,designation_id", $arr , "company_id,location_id,department_id,section_id,designation_id", "requires/incentive_scheme_controller", 'setFilterGrid("list_view",-1);','') ;
}

if($action=="load_php_data_to_form")
{
	$nameArray=sql_select("select id,company_id,location_id,department_id,section_id,designation_id from lib_incentive_scheme_mst where id='$data' and status_active=1 and is_deleted=0");// a.designation_id=$data[4] and    	 b
	
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_company_id').value = '".($inf[csf("company_id")])."';\n";    
		echo "document.getElementById('cbo_location_id').value = '".($inf[csf("location_id")])."';\n";    
		echo "document.getElementById('cbo_department_id').value  = '".($inf[csf("department_id")])."';\n"; 
		echo "document.getElementById('cbo_section_id').value  = '".($inf[csf("section_id")])."';\n";
		echo "document.getElementById('cbo_designation_id').value  = '".($inf[csf("designation_id")])."';\n";
		
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "update_incentive_data( ".$inf[csf("id")]." );";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_incentive_scheme',1);\n";  
	}
}

if ($action=="show_incentive_list")
{
	$i=0;
	$sql_result =sql_select("SELECT id,lower_limit,uper_limit,taka_day from lib_incentive_scheme_dtls where mst_id='$data'");
	$num_rows=count($sql_result);
	foreach ($sql_result as $row)
	{
		$i++;
		?>
        <tr id="tr_<? echo $i; ?>">
            <td width="60"><input type="hidden" name="delid_<? echo $i; ?>" id="delid_<? echo $i; ?>" style="width:60px" />
                <input type="text" name="txtlowerlimit_<? echo $i; ?>" id="txtlowerlimit_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf("lower_limit")]; ?>" style="width:60px" /><input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" value="<? echo $row[csf("id")]; ?>" style="width:60px" />
            </td>
            <td width="60"><input type="text" name="txtuperlimit_<? echo $i; ?>" id="txtuperlimit_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf("uper_limit")]; ?>" style="width:60px" />
            </td>
             <td width="60"><input type="text" name="txttakaday_<? echo $i; ?>" id="txttakaday_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf("taka_day")]; ?>" style="width:60px" /> </td>
            <td width="80">
                <input type="button" id="increaseconversion_<? echo $i; ?>" style="width:35px" class="formbutton" value="+" onClick="add_share_row(<? echo $i; ?>)"/>
                <input type="button" id="decreaseconversion_<? echo $i; ?>" style="width:35px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>,'tbl_share_details_entry')"/></td>
        </tr>
       <?
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // ===========================Insert Here==================================
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "lib_incentive_scheme_mst", 1 ) ; 
			 
			$field_array="id,company_id,location_id,department_id,section_id,designation_id,inserted_by,insert_date,status_active,is_deleted"; 			 
			
			$data_array="(".$id.",".$cbo_company_id.",".$cbo_location_id.",".$cbo_department_id.",".$cbo_section_id.",".$cbo_designation_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			
			
			$id_dtls=return_next_id( "id", " lib_incentive_scheme_dtls",1); 
			$field_array_dtls="id,mst_id,lower_limit,uper_limit,taka_day,inserted_by,insert_date,status_active,is_deleted"; 	
			for($i=1; $i<=$tot_row; $i++)
			{
				$txtlowerlimit="txtlowerlimit_".$i;
				$txtuperlimit="txtuperlimit_".$i;
				$txttakaday="txttakaday_".$i;
				$updateiddtls="updateiddtls_".$i;
				
				if ($i!=1) $data_array_dtls .=",";
				$data_array_dtls .="(".$id_dtls.",".$id.",".$$txtlowerlimit.",".$$txtuperlimit.",".$$txttakaday.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".'1'.",0)";
				$id_dtls=$id_dtls+1;
			}
			$rID=sql_insert("lib_incentive_scheme_mst",$field_array,$data_array,0);
			//echo "insert into lib_incentive_scheme_mst (".$field_array_dtls.") values ".$data_array_dtls;die;
			$rID1=sql_insert("lib_incentive_scheme_dtls",$field_array_dtls,$data_array_dtls,1);			
			if($db_type==0)
			{
				if($rID && $rID1 )
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
				 if($rID && $rID1)
					{
						oci_commit($con);   
						echo "0**".$rID;
					}
				else{
						oci_rollback($con);
						echo "10**".$rID;
					}
			}
			disconnect($con);
			die;
	}
	else if ($operation==1)  
	{
		$con = connect();
		if($db_type==0)
		{
		mysql_query("BEGIN");
		} 
		$id=str_replace("'",'',$update_id);
		
		$field_array="location_id*department_id*section_id*designation_id*updated_by*update_date";
		$data_array="".$cbo_location_id."*".$cbo_department_id."*".$cbo_section_id."*".$cbo_designation_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array_dtls="id,mst_id,lower_limit,uper_limit,taka_day,inserted_by,insert_date,status_active,is_deleted";
		$field_array_up ="lower_limit*uper_limit*taka_day*updated_by*update_date";
		$add_comma=0;
		$id_dtls=return_next_id( "id", " lib_incentive_scheme_dtls",1); 
		for($i=1; $i<=$tot_row; $i++)
		{
			$txtlowerlimit="txtlowerlimit_".$i;
			$txtuperlimit="txtuperlimit_".$i;
			$txttakaday="txttakaday_".$i;
			$updateiddtls="updateiddtls_".$i;
			$delid="delid_".$i;
			
            if ($i!=1) $data_array_dtls .=",";
			$data_array_dtls .="(".$id_dtls.",".$id.",".$$txtlowerlimit.",".$$txtuperlimit.",".$$txttakaday.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".'1'.",0)";
				$id_dtls=$id_dtls+1;
				
			
		}
		//print_r($data_array_up);die;
		$delete=execute_query("delete from lib_incentive_scheme_dtls where mst_id=".$update_id."");
		$rID=sql_update("lib_incentive_scheme_mst",$field_array,$data_array,"id",$id,0);  	
		$rID3=sql_insert("lib_incentive_scheme_dtls",$field_array_dtls,$data_array_dtls,1);
	

		if($db_type==0)
		  {
			  if($rID && $rID3)
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
				  if($rID && $rID3)
					{
						oci_commit($con);   
						echo "1**".$rID;
					}
				else{
						oci_rollback($con);
						echo "10**".$rID;
					}
			}
			disconnect($con);
			die;
	} 
}
?>