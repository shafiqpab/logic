 <?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
 
include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="search_list_view")
{
	$arr=array (1=>$row_status);
        $sql = "select id,yarn_brand,sequence_no,status_active from lib_yarn_brand where is_deleted=0";
	//echo  create_list_view ( "list_view", "Yarn Brand Name,Status,Sequence No", "150,190,50","450","220",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,status_active,0", $arr , "yarn_count,status_active,sequence_no", "../item_details/requires/yarn_brand_controller", 'setFilterGrid("list_view",-1);' ) ;
        echo create_list_view("list_view", "Yarn Brand Name,Status,Sequence No", "150,190,50", "450", "220", 0, "select id,yarn_brand,sequence_no,status_active from lib_yarn_brand where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,status_active,0", $arr, "yarn_brand,status_active,sequence_no", "../item_details/requires/yarn_brand_controller", 'setFilterGrid("list_view",-1);');
}

if ($action=="load_php_data_to_form")
{
    $qq="select id,yarn_brand,sequence_no,status_active from  lib_yarn_brand where id='$data'";
	$nameArray=sql_select( "select id,yarn_brand,sequence_no,status_active from  lib_yarn_brand where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_yarn_brand').value = '".($inf[csf("yarn_brand")])."';\n";    
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('txt_sequence').value  = '".($inf[csf("sequence_no")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_count_info',1);\n";  
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	
	//echo $_SESSION['menu_id'];die;
	if ($operation==0)  // Insert Here
	{
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		if (is_duplicate_field( "yarn_brand", "lib_yarn_brand", "yarn_brand=$txt_yarn_brand and is_deleted=0" ) == 1)
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
			$id=return_next_id( "id", "  lib_yarn_brand", 0 ) ;
			
			$field_array="id,yarn_brand,sequence_no,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$txt_yarn_brand.",".$txt_sequence.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)"; 
			//echo "insert into lib_yarn_count($field_array)values".$data_array;die;
			$rID=sql_insert("lib_yarn_brand",$field_array,$data_array,1);
			check_table_status( $_SESSION['menu_id'],0);
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
	
	else if ($operation==1)   // Update Here
	{
		if (is_duplicate_field( "yarn_brand", "lib_yarn_brand", "yarn_brand=$txt_yarn_brand and id!=$update_id and is_deleted=0" ) == 1)
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
			
			$field_array="yarn_brand*sequence_no*updated_by*update_date*status_active*is_deleted";
			$data_array="".$txt_yarn_brand."*".$txt_sequence."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";
			
			$rID=sql_update("lib_yarn_brand",$field_array,$data_array,"id","".$update_id."",1);
			
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
		
		$rID=sql_delete("lib_yarn_brand",$field_array,$data_array,"id","".$update_id."",1);
		
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