 <?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
 
include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="search_list_view")
{
	 
	$arr=array (1=>$count_system_arr,2=>$number_of_filament_arr,3=>$yarn_spinning_system_arr,4=>$row_status);
	echo  create_list_view ( "list_view,tbl_scroll_body", "Yarn Count Name,Count System,Number Of Filament,Yarn Spinning System,Status,Sequence No", "150,100,80,80,80","730","220",0, "select id,yarn_count,sequence_no,status_active,yarn_spinning_system,yarn_finish,yarn_color_code,yarn_type,number_of_filament,count_system,yarn_fibre,yarn_fibre_type,yarn_color from lib_yarn_count where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,count_system,number_of_filament,yarn_spinning_system,status_active,0", $arr , "yarn_count,count_system,number_of_filament,yarn_spinning_system,status_active,sequence_no", "../item_details/requires/yarn_count_controller_v2", 'setFilterGrid("list_view",-1);' ) ;
	 
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,yarn_count,sequence_no,status_active,yarn_spinning_system,yarn_finish ,yarn_color_code,yarn_type, number_of_filament,count_system,yarn_fibre,yarn_fibre_type,yarn_color from  lib_yarn_count where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_yarn_count').value = '".($inf[csf("yarn_count")])."';\n";    
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('txt_sequence').value  = '".($inf[csf("sequence_no")])."';\n";

		echo "document.getElementById('cbo_yarn_spinning_system').value  = '".($inf[csf("yarn_spinning_system")])."';\n";
		//echo "document.getElementById('cbo_yarn_finish').value  = '".($inf[csf("yarn_finish")])."';\n";
		//echo "document.getElementById('cbo_yarn_type').value  = '".($inf[csf("yarn_type")])."';\n";
		echo "document.getElementById('cbo_number_of_filament').value  = '".($inf[csf("number_of_filament")])."';\n";
		echo "document.getElementById('cbo_count_system').value  = '".($inf[csf("count_system")])."';\n";
		//echo "document.getElementById('cbo_yarn_fibre').value  = '".($inf[csf("yarn_fibre")])."';\n";
		//echo "document.getElementById('cbo_yarn_fibre_type').value  = '".($inf[csf("yarn_fibre_type")])."';\n";
		//echo "document.getElementById('cbo_yarn_color').value  = '".($inf[csf("yarn_color")])."';\n";

		//echo "document.getElementById('txt_yarn_color_code').value  = '".($inf[csf("yarn_color_code")])."';\n";

		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_count_info',1);\n";  
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$txt_yarn_color_code=str_replace("'", "", $txt_yarn_color_code);
	//echo "11**0 **select yarn_count from lib_yarn_count yarn_count=$txt_yarn_count and sequence_no=$txt_sequence and sequence_no=$txt_sequence and yarn_spinning_system=$cbo_yarn_spinning_system and yarn_finish=$cbo_yarn_finish and yarn_color_code='".$txt_yarn_color_code."' and yarn_type=$cbo_yarn_type and number_of_filament=$cbo_number_of_filament and count_system=$cbo_count_system and yarn_fibre=$cbo_yarn_fibre and yarn_fibre_type=$cbo_yarn_fibre_type and yarn_color=$cbo_yarn_color and is_deleted=0";die;
	//echo $_SESSION['menu_id'];die;
	if ($operation==0)  // Insert Here
	{
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		if (is_duplicate_field( "yarn_count", "lib_yarn_count", "yarn_count=$txt_yarn_count and sequence_no=$txt_sequence and sequence_no=$txt_sequence and yarn_spinning_system=$cbo_yarn_spinning_system and number_of_filament=$cbo_number_of_filament and count_system=$cbo_count_system   and is_deleted=0" ) == 1)
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
			check_table_status( $_SESSION['menu_id'],1);
			$id=return_next_id( "id", "  lib_yarn_count", 0 ) ;

			//txt_yarn_count*cbo_status*txt_sequence*update_id*cbo_count_system*cbo_number_of_filament*cbo_yarn_spinning_system

			
			$field_array="id,yarn_count,sequence_no,yarn_spinning_system,number_of_filament,count_system,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$txt_yarn_count.",".$txt_sequence.",".$cbo_yarn_spinning_system.",".$cbo_number_of_filament.",".$cbo_count_system.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)"; 
			//echo "insert into lib_yarn_count($field_array)values".$data_array;die;
			$rID=sql_insert("lib_yarn_count",$field_array,$data_array,1);
			check_table_status( $_SESSION['menu_id'],0);
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**insert into lib_yarn_count($field_array)values".$data_array;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				if($rID )
				{
					oci_commit($con);  
					echo "0**".$rID;
				}
			else
				{
					oci_rollback($con);
					echo "10**".$rID."**insert into lib_yarn_count($field_array)values".$data_array;
				}
			}
			disconnect($con);
			die;
		}
	}
	
	else if ($operation==1)   // Update Here
	{
		if (is_duplicate_field( "yarn_count", "lib_yarn_count", "yarn_count=$txt_yarn_count and id!=$update_id and is_deleted=0" ) == 1)
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
			
			$field_array="yarn_count*sequence_no*yarn_spinning_system*number_of_filament*count_system*updated_by*update_date*status_active*is_deleted";
			$data_array="".$txt_yarn_count."*".$txt_sequence."*".$cbo_yarn_spinning_system."*".$cbo_number_of_filament."*".$cbo_count_system."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";
			
			$rID=sql_update("lib_yarn_count",$field_array,$data_array,"id","".$update_id."",1);
			
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
	
	else if ($operation==2)   // Update Here
	{
		/*$unique_check1 = is_duplicate_field( "id", "wo_po_yarn_info_details", "yarn_count_id=$update_id and status_active=1" );
		$unique_check2 = is_duplicate_field( "id", "wo_projected_order_child", "yarn_count_id=$update_id and status_active=1" );
		$unique_check3 = is_duplicate_field( "id", "wo_non_order_info_dtls", "Yarn_count_id 	=$update_id and status_active=1" );
		$unique_check4 = is_duplicate_field( "id", "inv_product_info_details", "yarn_count=$update_id and status_active=1" );*/
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_yarn_count",$field_array,$data_array,"id","".$update_id."",1);
		
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


?>