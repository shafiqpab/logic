<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="color_list_view")
{
			$arr=array (2=>$row_status);
			echo  create_list_view ( "list_view", "Size Name,Sequence,Status", "200,100,100","450","220",0, "select  size_name,sequence,status_active,id from   lib_size where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,status_active", $arr , "size_name,sequence,status_active", "../merchandising_details/requires/size_entry_controller", 'setFilterGrid("list_view",-1);' ) ;
}
if ($action=="load_php_data_to_form")
{
	
	$nameArray=sql_select( "select  size_name,sequence,status_active,id from   lib_size where is_deleted=0 and id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_size_name').value = '".($inf[csf("size_name")])."';\n";  
		echo "document.getElementById('txt_sequence').value = '".($inf[csf("sequence")])."';\n";   
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_size_info',1);\n";  
	}
}

if ($action=="save_update_delete")
{  

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
		{
			if(is_duplicate_field( "size_name", " lib_size", "LOWER(size_name)=LOWER($txt_size_name) and is_deleted=0" ) == 1)
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
				$id=return_next_id( "id", "lib_size", 1 ) ;
				$field_array="id,size_name,sequence,inserted_by,insert_date,status_active,is_deleted";
				
				$data_array="(".$id.",".trim(strtoupper($txt_size_name)).",".$txt_sequence.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
				
				//Insert Data in lib_color_tag_buyer Table----------------------------------------
				
				/*$id_lib_color_tag_buyer=return_next_id( "id", "lib_color_tag_buyer", 1 );
				$data_array_buyer="";
				$tag_buyer=explode(',',str_replace("'","",$cbo_tag_buyer));
				for($i=0; $i<count($tag_buyer); $i++)
				{
					//if($id_lib_color_tag_buyer=="") $id_lib_color_tag_buyer=return_next_id( "id", "lib_buyer_party_type", 1 ); else $id_lib_color_tag_buyer=$id_lib_color_tag_buyer+1;
					if($i==0) $add_comma=""; else $add_comma=",";
					$data_array_buyer.="$add_comma(".$id_lib_color_tag_buyer.",".$id.",".$tag_buyer[$i].")";
					$id_lib_color_tag_buyer++;
				}*/
				//$field_array_buyer="id, color_id, buyer_id";
				$rID=sql_insert("lib_size",$field_array,$data_array,0);
				//$rID_1=sql_insert("lib_color_tag_buyer",$field_array_buyer,$data_array_buyer,1);
			
			
				//----------------------------------------------------------------------------------
			   
				if($db_type==0)
				{
					if($rID)
					{
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
					if($rID)
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
		
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//txt_size_name
		$field_array="size_name*sequence*updated_by*update_date*status_active";
	    $data_array="".trim(strtoupper($txt_size_name))."*".$txt_sequence."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."";
		//echo  $data_array;die;
		
		
		
		//Insert Data in lib_color_tag_buyer Table----------------------------------------
	
		/*$id_lib_color_tag_buyer=return_next_id( "id", "lib_color_tag_buyer", 1 );
		$data_array_buyer="";
		$tag_buyer=explode(',',str_replace("'","",$cbo_tag_buyer));
		for($i=0; $i<count($tag_buyer); $i++)
		{
			//if($id_lib_color_tag_buyer=="") $id_lib_color_tag_buyer=return_next_id( "id", "lib_buyer_party_type", 1 ); else $id_lib_color_tag_buyer=$id_lib_color_tag_buyer+1;
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array_buyer.="$add_comma(".$id_lib_color_tag_buyer.",".$update_id.",".$tag_buyer[$i].")";
			$id_lib_color_tag_buyer++;
		}
		$field_array_buyer="id,color_id,buyer_id";
		$rID=sql_update("lib_color",$field_array,$data_array,"id","".$update_id."",0);
		$rID1=execute_query( "delete from lib_color_tag_buyer where  color_id = $update_id",0);*/
		//$rID_1=sql_insert("lib_color_tag_buyer",$field_array_buyer,$data_array_buyer,1);
			$rID=sql_update("lib_size",$field_array,$data_array,"id","".$update_id."",0);
			//echo "10**".$rID.'=='.$rID1.'=='.$rID_1;die;
	
		//----------------------------------------------------------------------------------
		
		if($db_type==0)
		{
			if($rID)
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
	        if($rID)
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
	

else if ($operation==2)   // Delete Here
	{
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_size",$field_array,$data_array,"id","".$update_id."",1);
		
		
		
		if($db_type==0)
		{
			if($rID )
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