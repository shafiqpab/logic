<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="search_list_view")
{
	$arr=array (0=>$party_type_supplier,3=>$party_type_supplier,4=>$row_status); 
	echo  create_list_view ( "list_view", "Task Catagory, Task Name,Short Name,Task Link,Status", "150,150,150,200,100","850","220",0, "select task_category,task_name,task_name_short,task_link,status_active,id from tna_lib_task_details where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "task_category,0,0,task_link,status_active", $arr , "task_category,task_name,task_name_short,task_link,status_active", "requires/task_library_controller", 'setFilterGrid("list_view",-1);' ) ;
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,task_category,task_name,task_name_short,task_link,status_active from  tna_lib_task_details where id='$data'" );
	foreach ($nameArray as $inf)
	{	//cbo_task_catagory,txt_task_name,txt_short_name,cbo_task_link,cbo_status
		echo "document.getElementById('cbo_task_catagory').value  = '".mysql_real_escape_string($inf[csf("task_category")])."';\n";    
		echo "document.getElementById('txt_task_name').value      = '".mysql_real_escape_string($inf[csf("task_name")])."';\n";
		echo "document.getElementById('txt_short_name').value     = '".mysql_real_escape_string($inf[csf("task_name_short")])."';\n";    
		echo "document.getElementById('cbo_task_link').value      = '".mysql_real_escape_string($inf[csf("task_link")])."';\n";
	    echo "document.getElementById('cbo_status').value         = '".mysql_real_escape_string($inf[csf("status_active")])."';\n";	
		echo "document.getElementById('update_id').value          = '".mysql_real_escape_string($inf[csf("id")])."';\n";	
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_tna_task_info',1);\n";  
	}
}

//task_category,task_name_id,task_name,task_name_short,task_link
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "task_category", "tna_lib_task_details", "task_category=$cbo_task_catagory and task_name=$txt_task_name and task_name_short=$txt_short_name  and is_deleted=0" ) == 1)
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
			//cbo_task_catagory,txt_task_name,txt_short_name,cbo_task_link,cbo_status
			$id=return_next_id( "id", "tna_lib_task_details", 1 ) ;
			$field_array="id,task_category,task_name,task_name_short,task_link,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$cbo_task_catagory.",".$txt_task_name.",".$txt_short_name.",".$cbo_task_link.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			$rID=sql_insert("tna_lib_task_details",$field_array,$data_array,1);
			//echo  $rID; die;
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
				echo $rID.'**0';
			}
			disconnect($con);
			die;
		}
	}
	 
	else if ($operation==1)   // Update Here
	{
		 
		if (is_duplicate_field( "task_category", "tna_lib_task_details", "task_category=$cbo_task_catagory and task_name=$txt_task_name and id!=$update_id and task_name_short=$txt_short_name and is_deleted=0" ) == 1)
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
			
		    $field_array="task_name*task_name_short*inserted_by*insert_date*status_active*is_deleted";
			$data_array="".$txt_task_name."*".$txt_short_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";
			$rID=sql_update("tna_lib_task_details",$field_array,$data_array,"id","".$update_id."",1);
			
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
			echo "1**".$rID;
			}
			disconnect($con);
			die;
		}
		
	}
	else if ($operation==2)   // Delete Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("tna_lib_task_details",$field_array,$data_array,"id","".$update_id."",1);	
	 
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		disconnect($con);
		if($db_type==2 || $db_type==1 )
		{
	    echo "2**".$rID;
		}
		
	}
}


?>