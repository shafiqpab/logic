<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$lib_designation=return_library_array( "select id,designation from lib_mkt_team_member_info where status_active=1 and is_deleted=0", "id", "designation"  );
$lib_user=return_library_array( "select id,user_name from user_passwd", "id", "user_name"  );

//system id popup here----------------------//

if ($action=="load_drop_down_location_rn")
{
	//$selected=$exdata[0];
  $selected=0;
  if($data=='1'){echo'<input type="text" id="recipient_name" name="recipient_name" class="text_boxes" style="width:212px;"/>';}
  elseif($data=='2')
  {
   echo create_drop_down( "recipient_name", 224, "select id,designation from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id,designation", 1, "--- Select Name ---", 0, "get_php_form_data(this.value,'load_email','requires/email_address_setup_controller');" );
   
   
  }
  else{
    echo create_drop_down( "recipient_name", 224, "select id,user_name from user_passwd where valid=1","id,user_name", 1, "--- Select Name ---", 0, "" );
  }
}







?>
 
 
 
 
 
<?php

if($action=="load_email")
{
$sql="select team_member_email from lib_mkt_team_member_info where id=$data";
	$res = sql_select($sql);
	
	foreach($res as $row)
	{	
		echo "$('#email_address').val('".$row[csf("team_member_email")]."');\n";
		}
	exit();	

}



if($action=="mail_recipient_group_from_data")
{
	list($mail_item,$company_id )=explode('__',$data);
	//$result=sql_select("select id,email_address from user_mail_address where status_active=1 and is_deleted=0");
	$res=sql_select("select id,mail_id,status_active from mail_group_mst where mail_item=$mail_item and company_id=$company_id and is_deleted=0");
	
			echo "$('#update_vlues').val('".$res[0][csf("mail_id")]."');\n";	
			echo "$('#update_id').val('".$res[0][csf("id")]."');\n";	
			echo "$('#status').val('".$res[0][csf("status_active")]."');\n";	
		if($res[0][csf("id")]){
			echo "set_button_status(1, permission, 'mail_recipient_group',1,1);";
			}
		else{
			echo "set_button_status(0, permission, 'mail_recipient_group',1,1);";
			}
}//mail_recipient_group_from_data end; ?>


 
 
 
 <?php    
//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN");}
			
			//$exp_recipient_name=explode('*',$recipient_name);
			//$recipient_name=($exp_recipient_name[1]!="")? str_replace("'",'',$exp_recipient_name[0]):$recipient_name;
		/*
		 $duplicate=is_duplicate_field("company_id","mail_group_mst","company_id=$company_name and status_active=1 and is_deleted=0");
		  if($duplicate==1)
		  {
			  echo "11**This email id is exist for same item of this requisition.";
			  exit;
		  }
		*/
		
		if(str_replace("'","",$update_id)=="")
		{
			$id= return_next_id("id","mail_group_mst",1);
			$field_array_mst="id, company_id, mail_item,mail_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, is_locked";
			$data_array_mst="(".$id.",".$company_id.",".$mail_item.",".$you_have_selected.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,0,".$status.",0,0)";
			$rID=sql_insert("mail_group_mst",$field_array_mst,$data_array_mst,1);
		}
		
		
		if($db_type==0)
		{
			if( $rID)
			{
			mysql_query("COMMIT");  
			echo "0**".str_replace("'",'',$id);
			}
			else
			{
			mysql_query("ROLLBACK"); 
			echo "10**".str_replace("'",'',$id);
			}
		}
		if($db_type==2 || $db_type==1 )
						{
							echo "0**"; //.$rID."**".$id;
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

		if(str_replace("'",'',$update_id)!="")
		{
			$update_id=str_replace("'",'',$update_id);
			$field_array_up="company_id*mail_item*mail_id*updated_by*update_date*status_active";
			$data_array_up="".$company_id."*".$mail_item."*".$you_have_selected."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$status."";
		}
			$rID=sql_update("mail_group_mst",$field_array_up,$data_array_up,"id",$update_id,1);

		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$rID);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$rID);
			}
		}
		if($db_type==2 || $db_type==1 )
						{
							echo "1**"; //.$rID."**".$id;
						}
						disconnect($con);
						die;
	}



		if ($operation==2)  // Delete Here
		{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				$update_id=str_replace("'",'',$update_id);
				$field_array="updated_by*update_date*status_active*is_deleted";
				$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
				$rID=sql_update("mail_group_mst",$field_array,$data_array,"id","".$update_id."",1);
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
				disconnect($con);
				if($db_type==2 || $db_type==1 )
				{
					echo "2**".$update_id;
				}
		}





}

?>

