<?
	header('Content-type:text/html; charset=utf-8');
	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	include('../../../includes/common.php');
	$permission=$_SESSION['page_permission'];
	$user_id=$_SESSION['logic_erp']['user_id'];

	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];

	if ($action=="save_update_delete")
	{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if ($operation==0)  // Insert Here
		{
			// duplicate check based on section, reason type, reason
			if (is_duplicate_field( "id", "lib_re_process_reason_entry", "section=$cbo_section_name and reason_type=$cbo_reason_type and reason=$txt_reason and status=$cbo_status and is_deleted=0" ) == 1)
			{
				
				echo "11**0"; disconnect($con); die;
			}
			else
			{
				
				$id=return_next_id( "id", "lib_re_process_reason_entry", 1 ) ;
				$field_array="id,section,reason_type,reason,status,inserted_by,insert_date,status_active, is_deleted";
				$data_array="(".$id.",".$cbo_section_name.",".$cbo_reason_type.",".$txt_reason.",".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
				$rID=sql_insert("lib_re_process_reason_entry",$field_array,$data_array,1);
				if($db_type==0)
				{
					if($rID ){
						mysql_query("COMMIT");  
						echo "0**".$rID;
					}
					else{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
				}
				if($db_type==2 || $db_type==1 )
				{
					if($rID )
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
		else if ($operation==1)   // Update Here
		{
			// duplicate check based on section, reason type, reason
			if (is_duplicate_field( "id", "lib_re_process_reason_entry", "section=$cbo_section_name and reason_type=$cbo_reason_type and reason=$txt_reason and status=$cbo_status and status_active=1 and is_deleted=0 and id <> $update_id " ) == 1)
			{
				echo "11**0"; disconnect($con); die;
			}
			
			else
			{
				$field_array="section*reason_type*reason*status*updated_by*update_date";
				$data_array="".$cbo_section_name."*".$cbo_reason_type."*".$txt_reason."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$rID=sql_update("lib_re_process_reason_entry",$field_array,$data_array,"id","".$update_id."",1);
				if($db_type==0)
				{
					if($rID ){
						mysql_query("COMMIT");  
						echo "1**".$rID;
					}
					else{
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
			die;
			}
		}
		else if ($operation==2)   // delete Here
		{

			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			
			$rID=sql_delete("lib_re_process_reason_entry",$field_array,$data_array,"id","".$update_id."",1);
			
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "1**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
				{
				if($rID )
					{
						oci_commit($con);   
						echo "2**".$rID;
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

	if ($action=="reason_list_view")
	{ 
		$section_arr = array(1=>"Knitting",2=>"Dyeing", 3=>"Finishing", 4=>"Others");
		$reason_type_arr = array(1=>"In-Charge",2=>"Others");
		$reason_status_arr = array(1=>"Active",2=>"Inactive");
		$arr = array(0 => $section_arr, 1 => $reason_type_arr, 3=> $reason_status_arr);	
		echo  create_list_view ( "list_view", "Section Name,Reason Type, Reason, Status", "120,150,180,80","600","150",1, "SELECT id, section,reason_type, reason,status  from lib_re_process_reason_entry where is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "section,reason_type,0,status", $arr , "section,reason_type,reason,status", "../production/requires/re_process_reason_entry_controller", 'setFilterGrid("list_view",-1);' ) ;
	}

	if ($action=="load_php_data_to_form")//load list view data to the form
	{
		$nameArray=sql_select( "SELECT  id, section,reason_type,reason, status  from lib_re_process_reason_entry where id='$data'" );
		foreach ($nameArray as $inf)
		{
			
			echo "document.getElementById('cbo_section_name').value  = '".($inf[csf("section")])."';\n"; 
			echo "document.getElementById('txt_reason').value  = '".($inf[csf("reason")])."';\n";
			echo "document.getElementById('cbo_reason_type').value  = '".($inf[csf("reason_type")])."';\n";
			echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_re_process_reason_entry',1);\n";
			echo "document.getElementById('cbo_status').value  = '".($inf[csf("status")])."';\n";
		}
	}
?>