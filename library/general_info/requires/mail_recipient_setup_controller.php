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
   echo create_drop_down( "recipient_name", 224, "select id,designation from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id,designation", 1, "--- Select Name ---", 0, "get_php_form_data(this.value,'load_email','requires/mail_recipient_setup_controller');" );
   
   
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



if($action=="mail_recipient_from_data")
{


$sql="select id,user_type,user_id,email_address,insert_date from user_mail_address where status_active=1 and is_deleted=0 and id=$data";
	$res = sql_select($sql);
	foreach($res as $row)
	{	
		echo"load_drop_down( 'requires/mail_recipient_setup_controller', '".$row[csf("user_type")]."', 'load_drop_down_location_rn', 'location_td_rn');";
		echo "$('#update_id').val('".$row[csf("id")]."');\n";
		echo "$('#user_type').val('".$row[csf("user_type")]."');\n";
		echo "$('#recipient_name').val('".$row[csf("user_id")]."');\n";	
		echo "$('#email_address').val('".$row[csf("email_address")]."');\n";
		echo "set_button_status(1, permission, 'mail_recipient',1,1);";
		}
	exit();	
}
?>


 
 
 
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
		
		 $duplicate=is_duplicate_field("user_id","user_mail_address","user_id=$recipient_name and status_active=1 and is_deleted=0");
		  if($duplicate==1)
		  {
			  echo "11**This email id is exist for same item of this requisition.";
			  disconnect($con);
			  exit;
		  }
		
		
		if(str_replace("'","",$update_id)=="")
		{
			$id= return_next_id("id","user_mail_address",1);
			
			//$exp_recipient_name=explode('*',$recipient_name);
			//$recipient_name=($exp_recipient_name[1]!="")? str_replace("'",'',$exp_recipient_name[0]):$recipient_name;
			
			$field_array_mst="id,user_type,user_id,email_address,inserted_by,insert_date,status_active,is_deleted";
			$data_array_mst="(".$id.",".$user_type.",".$recipient_name.",".$email_address.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			$rID=sql_insert("user_mail_address",$field_array_mst,$data_array_mst,1);
			//$return_no=str_replace("'",'',$txt_system_id);
		}
		
		
		if($db_type==0)
		{
			if( $rID)
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
				if( $rID)
					{
					oci_commit($con);  
					echo 0;
					}
					else
					{
					oci_rollback($con);
					echo 10;
					}
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
			$mst_id=str_replace("'",'',$update_id);
			
			$field_array_up="user_type*user_id*email_address*updated_by*update_date";
			$data_array_up="".$user_type."*".$recipient_name."*".$email_address."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		
			$rID=sql_update("user_mail_address",$field_array_up,$data_array_up,"id",$update_id,1);

		if($db_type==0)
		{
			if($rID)
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



		if ($operation==2)  // Delete Here
		{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				$field_array="updated_by*update_date*status_active*is_deleted";
				$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
				$rID=sql_delete("user_mail_address",$field_array,$data_array,"id","".$update_id."",1);
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");  
						echo 2;
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo 10;
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


<?


if($action=="create_user_emsil_list_view")
{ 
 
?>
<table class="rpt_table" border="1" cellspacing="0" cellpadding="0" rules="all">
    <thead>
        <th width="50">SL No</th>
        <th width="100">User Type</th>
        <th width="150">Recipient Name</th>
        <th width="290">Email Address</th>
    </thead>
    <tbody>
<? 


$result=sql_select("select id,user_type,user_id,email_address from user_mail_address where status_active=1 and is_deleted=0");
$sl=1;
foreach($result as $list_rows){
 ?>    
    <tr onClick="get_php_form_data(<? echo $list_rows[csf('id')]; ?>,'mail_recipient_from_data','requires/mail_recipient_setup_controller')" style="cursor:pointer;">
        <td><? echo $sl; ?> </td>
        <td><? echo $mail_user_type[$list_rows[csf('user_type')]]; ?> </td>
        <td><? 
		
		if($list_rows[csf('user_type')]==1) echo $list_rows[csf('user_id')];
		else if($list_rows[csf('user_type')]==2)
			echo $lib_designation[$list_rows[csf('user_id')]];
		else if($list_rows[csf('user_type')]==3)
			echo $lib_user[$list_rows[csf('user_id')]];
		 ?></td>
        <td><? echo $list_rows[csf('email_address')]; ?> </td>
    </tr>
<? $sl++; } ?>    
</tbody>  
</table>    
    
	
<? } ?>