<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="marchant_team_info_list_view")
{
		 
		 	 $arr=array (5=>$row_status);
			//$sql= "select a.id,a.team_name,a.team_leader_name,a.status_active,count(b.team_id) as team from lib_marketing_team a, lib_mkt_team_member_info b  where a.id=b.team_id and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.team_name, a.team_leader_name order by a.id";
			 $sql= "select team_name,team_leader_name,team_leader_desig,team_leader_email,total_member,status_active,id from lib_marketing_team where is_deleted=0 order by team_name";
			 echo  create_list_view ( "list_view", "Team Name,Team Leader Name,Designation,Email,Total Member,Status", "150,200,100,150,55","800","220",0, $sql, "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,0,0,0,0,status_active", $arr , "team_name,team_leader_name,team_leader_desig,team_leader_email,total_member,status_active", "../merchandising_details/requires/marchant_team_info_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,1,0' ) ;

}
//second list view
     else if ($action=="marchant_team_info_det_list_view")
        {
		if ($data!="")
		{
	   $sql= "select id,team_member_name,designation,team_member_email,team_id,member_contact_no,status_active from lib_mkt_team_member_info where team_id ='$data'  and is_deleted=0 order by id";
		}
		else
		{
			$sql= "select id,team_member_name,designation,team_member_email,team_id,member_contact_no,status_active from lib_mkt_team_member_info where is_deleted=0 order by id";
		}
		
		$team_name_arr=return_library_array( "select id, team_name from lib_marketing_team",'id','team_name');
		$arr=array (3=>$team_name_arr,4=>$row_status);
		echo  create_list_view ( "list_view1", "Member Name,Designation,Email,Team Name,Status", "150,150,200,150,50","800","120",0, $sql, "get_php_form_data", "id","'load_php_data_to_form_marchant_team_info_det'", 1, "0,0,0,team_id,status_active", $arr , "team_member_name,designation,team_member_email,team_id,status_active", "../merchandising_details/requires/marchant_team_info_controller", 'setFilterGrid("list_view1",-1);' ) ;	 
}

else if ($action=="load_php_data_to_form")
{
$nameArray=sql_select( "select id,team_name,team_leader_name,team_leader_desig,team_leader_email,capacity_basic,capacity_smv,team_contact_no,status_active,lib_mkt_team_member_info_id from lib_marketing_team  where id='$data'" );
		foreach ($nameArray as $inf)
		{
			echo "document.getElementById('txt_team_name').value  = '".($inf[csf("team_name")])."';\n";    
			echo "document.getElementById('txt_team_leader_name').value  = '".($inf[csf("team_leader_name")])."';\n"; 
			echo "document.getElementById('txt_team_leader_desig').value  = '".($inf[csf("team_leader_desig")])."';\n";
			echo "document.getElementById('txt_team_leader_email').value  = '".($inf[csf("team_leader_email")])."';\n"; 
			echo "document.getElementById('txt_capacity_smv_leader').value  = '".($inf[csf("capacity_smv")])."';\n"; 
			echo "document.getElementById('txt_capacity_basic_leader').value  = '".($inf[csf("capacity_basic")])."';\n"; 
			echo "document.getElementById('txt_team_contact_no').value  = '".($inf[csf("team_contact_no")])."';\n"; 
			echo "document.getElementById('cbo_team_status').value  = '".($inf[csf("status_active")])."';\n";  
			echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
			echo "document.getElementById('id_lib_mkt_team_member_info').value  = '".($inf[csf("lib_mkt_team_member_info_id")])."';\n"; 
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_marchant_team_info',1);\n"; 
			echo "show_list_view('".$inf[csf("id")]."', 'marchant_team_info_det_list_view', 'member_list_view', '../merchandising_details/requires/marchant_team_info_controller', 'setFilterGrid(\'list_view1\',-1)');\n";  
		}
}


else if ($action=="load_php_data_to_form_marchant_team_info_det")
{
		$nameArray=sql_select( "select id,team_member_name,team_member_email,designation,team_id,capacity_smv_member,member_contact_no,capacity_basic_member,status_active from lib_mkt_team_member_info where id='$data'" );
		foreach ($nameArray as $inf)
		{
			  
			echo "document.getElementById('txt_member_name').value = '".($inf[csf("team_member_name")])."';\n";    
		    echo "document.getElementById('txt_member_designation').value  = '".($inf[csf("designation")])."';\n"; 
			echo "document.getElementById('txt_member_email').value  = '".($inf[csf("team_member_email")])."';\n";
			
			echo "document.getElementById('txt_capacity_smv_member').value  = '".($inf[csf("capacity_smv_member")])."';\n"; 
			echo "document.getElementById('txt_capacity_smv_member').value  = '".($inf[csf("capacity_smv_member")])."';\n"; 
			echo "document.getElementById('txt_member_contact_no').value  = '".($inf[csf("member_contact_no")])."';\n";  
			echo "document.getElementById('cbo_team_member_status').value  = '".($inf[csf("status_active")])."';\n";  
			echo "document.getElementById('update_id_dtl').value  = '".($inf[csf("id")])."';\n"; 
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_marchant_team_info_det',2);\n"; 
		}
}

else if ($action=="save_update_delete")
{
	
	   $process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 
	
		if ($operation==0)  // Insert Here
		{
				if (is_duplicate_field( "team_name", "lib_marketing_team", "team_name=$txt_team_name and is_deleted=0" ) == 1)
				{
					echo "11**0"; die;
				}
				else
				{
						$con = connect();
						if($db_type==0)
						{
							mysql_query("BEGIN");
						}
						$id=return_next_id( "id", "lib_marketing_team", 1 ) ;
						$id_lib_mkt_team_member_info=return_next_id( "id", "lib_mkt_team_member_info", 1 ) ;
						$field_array="id,team_name,team_leader_name, team_leader_desig,team_leader_email,lib_mkt_team_member_info_id,capacity_smv,capacity_basic,team_contact_no,inserted_by, insert_date,status_active, is_deleted";
						$data_array="(".$id.",".$txt_team_name.",".$txt_team_leader_name.",".$txt_team_leader_desig.",".$txt_team_leader_email." ,".$id_lib_mkt_team_member_info.",".$txt_capacity_smv_leader.",".$txt_capacity_basic_leader.",".$txt_team_contact_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_team_status.",0)";
						$rID=sql_insert("lib_marketing_team",$field_array,$data_array,0);
						//echo $rID; die;
					
					    $field_array1="id,designation,team_member_name,team_member_email,team_id,capacity_smv_member,capacity_basic_member,member_contact_no,inserted_by,insert_date,status_active,is_deleted";
					    $data_array1="(".$id_lib_mkt_team_member_info.",".$txt_team_leader_desig.",".$txt_team_leader_name.", ".$txt_team_leader_email.",".$id.",".$txt_capacity_smv_leader.",".$txt_capacity_basic_leader.",".$txt_team_contact_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_team_status.",0)";
					    $rID1=sql_insert("lib_mkt_team_member_info",$field_array1,$data_array1,1);
							//echo "insert into lib_mkt_team_member_info ($field_array1) values $data_array1";die;
						//echo $rID; die;
						if($db_type==0)
						{
							 if($rID && $rID1 )
							{
								mysql_query("COMMIT");  
								echo "0**".$rID."**".$id;
							}
							else
							{
								mysql_query("ROLLBACK"); 
								echo "10**".$rID;
							}
						}
						
						if($db_type==2 || $db_type==1 )
						{
						    if($rID && $rID1 )
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
		}
		
		if ($operation==1)  // Update Here
		{
			    if (is_duplicate_field( "team_name", "lib_marketing_team", "team_name=$txt_team_name and id!=$update_id and is_deleted=0" ) == 1)
				{
					echo "11**0"; die;
				}
				else
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}
					$field_array1="team_name*team_leader_name*team_leader_desig*team_leader_email*capacity_smv*capacity_basic*team_contact_no*updated_by*update_date*status_active*is_deleted";
					$data_array1="".$txt_team_name."*".$txt_team_leader_name."*".$txt_team_leader_desig."*".$txt_team_leader_email."*".$txt_capacity_smv_leader."*".$txt_capacity_basic_leader."*".$txt_team_contact_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_team_status."*0";
					$rID=sql_update("lib_marketing_team",$field_array1,$data_array1,"id","".$update_id."",0);
					
					$field_array="designation*team_member_name*team_member_email*team_id*capacity_smv_member*capacity_basic_member*member_contact_no*updated_by*update_date*status_active*is_deleted";
					$data_array="".$txt_team_leader_desig."*".$txt_team_leader_name."* ".$txt_team_leader_email."*".$update_id."*".$txt_capacity_smv_leader."*".$txt_capacity_basic_leader."*".$txt_team_contact_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_team_status."*0";
					$rID1=sql_update("lib_mkt_team_member_info",$field_array,$data_array,"id","".$id_lib_mkt_team_member_info."",1);
					
					if($db_type==0)
					{
						if($rID && $rID1)
						{
							mysql_query("COMMIT");  
							echo "1**".$rID."**".str_replace("'","",$update_id);
						}
						else
						{
							mysql_query("ROLLBACK"); 
							echo "10**".$rID;
						}
					}
					if($db_type==2 || $db_type==1 )
					{
					if($rID && $rID1)
						{
							oci_commit($con);   
							echo "1**".$rID."**".str_replace("'","",$update_id);
						}
					else
						{
							oci_rollback($con); 
							echo "10**".$rID;
						}
					}
					disconnect($con);
				}
		}
		
		if ($operation==2)  // Delete Here
		{
				if (is_duplicate_field( "team_leader", "wo_po_details_master", "team_leader=$update_id and is_deleted=0" ) == 1)
				{
					echo "13**0"."**".str_replace("'","",$update_id); die;
				}
				else
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}
					$field_array="updated_by*update_date*status_active*is_deleted";
					$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
					$rID=sql_delete("lib_marketing_team",$field_array,$data_array,"id","".$update_id."",0);
					$rID=sql_delete("lib_mkt_team_member_info",$field_array,$data_array,"id","".$id_lib_mkt_team_member_info."",1);
					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");  
							echo "2**".$rID."**".str_replace("'","",$update_id);
						}
						else
						{
							mysql_query("ROLLBACK"); 
							echo "10**".$rID."**".str_replace("'","",$update_id);
						}
					}
					if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);   
							echo "2**".$rID."**".str_replace("'","",$update_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID."**".str_replace("'","",$update_id);
						}
					}
					disconnect($con);
		        }
		}
}



else if ($action=="save_update_delete_dtl")
{       
       $process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 
	
		if ($operation==0)  // Insert Here
		{
				if (is_duplicate_field( "id", "lib_mkt_team_member_info", "team_member_name=$txt_member_name and designation=$txt_member_designation and team_member_email=$txt_member_email and team_id=$update_id  and is_deleted=0" ) == 1)
				{
					echo "11**0"; die;
				}
				else
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}
					$id=return_next_id( "id", "lib_mkt_team_member_info", 1 ) ;
					$field_array="id,designation,team_member_name,team_member_email,team_id,capacity_smv_member,capacity_basic_member,member_contact_no,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id.",".$txt_member_designation.",".$txt_member_name.", ".$txt_member_email.",".$update_id.",".$txt_capacity_smv_member.",".$txt_capacity_basic_member.",".$txt_member_contact_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_team_member_status.",0)";
					//$rID=sql_insert("lib_mkt_team_member_info",$field_array,$data_array,0);
					$total_member=return_field_value("count(team_id)","lib_mkt_team_member_info","team_id=$update_id and is_deleted=0");
					$field_array1="total_member";
					$data_array1="".$total_member."";
					$rID=sql_insert("lib_mkt_team_member_info",$field_array,$data_array,0);
					$rID1=sql_update("lib_marketing_team",$field_array1,$data_array1,"id","".$update_id."",1);
					//echo $rID; die;
					if($db_type==0)
					{
					  if($rID && $rID1)
						{
							mysql_query("COMMIT");  
							echo "0**".$rID."**".$id;
						}
						else
						{
							mysql_query("ROLLBACK"); 
							echo "10**".$rID;
						}
					}
					
					if($db_type==2 || $db_type==1 )
					{
						 if($rID && $rID1)
						{
							oci_commit($con); 
							echo "0**".$rID."**".$id;
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
		}
		
		if ($operation==1)  //Update Here
		{
				if (is_duplicate_field( "id", "lib_mkt_team_member_info", "team_member_name=$txt_member_name and designation=$txt_member_designation and team_member_email=$txt_member_email and  team_id=$update_id and id!=$update_id_dtl  and is_deleted=0" ) == 1)
				{
					echo "11**0"; die;
				}
				if (is_duplicate_field( "id", "lib_marketing_team", "lib_mkt_team_member_info_id=$update_id_dtl and is_deleted=0" ) == 1)
				{
					echo "14**0"; die;
				}
				else
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}
					$field_array="designation*team_member_name*team_member_email*team_id*capacity_smv_member*capacity_basic_member*member_contact_no*updated_by*update_date*status_active*is_deleted";
					$data_array="".$txt_member_designation."*".$txt_member_name."* ".$txt_member_email."*".$update_id."*".$txt_capacity_smv_member."*".$txt_capacity_basic_member."*".$txt_member_contact_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_team_member_status."*0";
					$rID=sql_update("lib_mkt_team_member_info",$field_array,$data_array,"id","".$update_id_dtl."",1);
					
					/*$field_array="team_leader_name*team_leader_desig*team_leader_email*updated_by*update_date*status_active*is_deleted";
					$data_array="".$txt_member_name."*".$txt_member_designation."*".$txt_member_email."*".$_SESSION['logic_erp']['user_id']."*'".$date."'*".$cbo_team_member_status."*0";
					$rID=sql_update("lib_marketing_team",$field_array,$data_array,"lib_mkt_team_member_info_id","".$update_id_dtl."",1);*/
					//echo $rID; die;
					if($db_type==0)
					{
					if($rID )
					{
						mysql_query("COMMIT");  
						echo "1**".$rID;
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
					}
					if($db_type==2 || $db_type==1 )
					{
					 if($rID )
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
				}
		}
		
		if ($operation==2)  // Delete Here
		{
				if (is_duplicate_field( "dealing_marchant", "wo_po_details_master", "dealing_marchant=$update_id_dtl and is_deleted=0" ) == 1)
				{
					echo "13**0"; die;
				}
				if (is_duplicate_field( "id", "lib_marketing_team", "lib_mkt_team_member_info_id=$update_id_dtl and is_deleted=0" ) == 1)
				{
					echo "13**0"; die;
				}
				else
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}
					$field_array1="updated_by*update_date*status_active*is_deleted";
					$data_array1="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
					//$rID=sql_delete("lib_mkt_team_member_info",$field_array,$data_array,"id","".$update_id_dtl."",0);
					$total_member=return_field_value("count(team_id)","lib_mkt_team_member_info","team_id=$update_id and is_deleted=0");
					$field_array="total_member";
					$data_array="".$total_member."";
					$rID=sql_delete("lib_mkt_team_member_info",$field_array1,$data_array1,"id","".$update_id_dtl."",0);
					$rID1=sql_update("lib_marketing_team",$field_array,$data_array,"id","".$update_id."",1);
					if($db_type==0)
					{
					if($rID && $rID1 )
					{
						mysql_query("COMMIT");  
						echo "2**".$rID;
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
					}
					if($db_type==2 || $db_type==1 )
					{
						if($rID && $rID1 )
							{
								oci_commit($con);    
								echo "2**".$rID;
							}
						else
							{
								oci_rollback($con);
								echo "10**".$rID;
							}
					}
					disconnect($con);
				}
		}
}
?>