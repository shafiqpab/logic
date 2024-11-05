<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="search_list_view")
{
	$cbo_emb_type=$data;
	$sql="select  emb_id,emb_name,emb_type,status_active,id from LIB_EMBELLISHMENT_NAME where is_deleted=0 and emb_type=$cbo_emb_type order by emb_type";
	
	//-----------------------------------------------------
	$nameArray=sql_select( $sql );
	if(count($nameArray)<1){
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id", "LIB_EMBELLISHMENT_NAME", 1 ) ;
		
		$embArr[1]=$emblishment_print_type;
		$embArr[2]=$emblishment_embroy_type;
		$embArr[3]=$emblishment_wash_type;
		//print_r($embArr[$cbo_emb_type]);die;
		
		$flag=0;
		foreach($embArr[$cbo_emb_type] as $emb_id=>$txt_emb_name){
			
			$field_array="id,emb_id,emb_name,emb_type,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$emb_id.",'".$txt_emb_name."',".$cbo_emb_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("LIB_EMBELLISHMENT_NAME",$field_array,$data_array,1);
			if($rID){$flag=1;}else{$flag=0;}
			
			$id++;
		}
			if($db_type==0)
			{
				if($flag && count($nameArray)<1){
					mysql_query("COMMIT");  
					//echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					//echo "10**".$rID;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
			if($flag && count($nameArray)<1 )
			    {
					oci_commit($con);   
					//echo "0**".$rID;
				}
				else{
					oci_rollback($con);
					//echo "10**".$rID;
				}
			}
			disconnect($con);
	
	}
	//-------------------------------------------------
	$arr=array (2=>$emblishment_name_array,3=>$row_status);
	echo  create_list_view ( "list_view", "Emb ID,Emb Name,Emb Type,Status", "50,200,100,100","520","220",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,emb_type,status_active", $arr , "emb_id,emb_name,emb_type,status_active", "../merchandising_details/requires/embellishment_entry_controller", 'setFilterGrid("list_view",-1);' ) ;
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select emb_name,emb_type,status_active,id from LIB_EMBELLISHMENT_NAME where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_emb_name').value = '".($inf[csf("emb_name")])."';\n";    
		echo "document.getElementById('cbo_emb_type').value  = '".($inf[csf("emb_type")])."';\n"; 
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_embellishment_entry',1);\n";  

	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "emb_name", "LIB_EMBELLISHMENT_NAME", "emb_name=$txt_emb_name and emb_type=$cbo_emb_type and is_deleted=0" ) == 1)
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
			$id=return_next_id( "id", "LIB_EMBELLISHMENT_NAME", 1 ) ;
			$emb_id=return_next_id( "EMB_ID", "LIB_EMBELLISHMENT_NAME where emb_type=$cbo_emb_type", 1 ) ;
			
			$field_array="id,emb_id,emb_name,emb_type,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$emb_id.",".$txt_emb_name.",".$cbo_emb_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			$rID=sql_insert("LIB_EMBELLISHMENT_NAME",$field_array,$data_array,1);
			
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
		
		if (is_duplicate_field( "emb_name", "LIB_EMBELLISHMENT_NAME", "emb_name=$txt_emb_name and emb_type=$cbo_emb_type and id!=$update_id and is_deleted=0" ) == 1)
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
			
			$field_array="emb_name*emb_type*updated_by*update_date*status_active";
			$data_array="".$txt_emb_name."*".$cbo_emb_type."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."";
			
			//echo "10**".$data_array;die;
			
			$rID=sql_update("LIB_EMBELLISHMENT_NAME",$field_array,$data_array,"id","".$update_id."",1);
			//echo $rID; die;
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
		}
		
	}
	
	else if ($operation==2)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("LIB_EMBELLISHMENT_NAME",$field_array,$data_array,"id","".$update_id."",1);
		
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


?>