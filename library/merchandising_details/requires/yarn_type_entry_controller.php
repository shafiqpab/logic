<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)
	{
		if (is_duplicate_field( "yarn_type_id", "lib_yarn_type", " yarn_type_id=$cbo_yarn_type_id and status_active=1 and is_deleted=0" ) == 1)
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
			
		
			$id=return_next_id( "id", "lib_yarn_type", 1 );
			$field_array="id, yarn_type_id, yarn_type_short_name, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$cbo_yarn_type_id.",".$txt_yarn_type_short_name.",'".$user_id."','".$pc_date_time."',".$cbo_status.",0)"; 
			$rID=sql_insert("lib_yarn_type",$field_array,$data_array,1); 
			
			if($db_type==0)
			{
				if($rID)
				{
					mysql_query("COMMIT");
					echo "0**".$txt_task_name."**0";
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "5**"."0"."**0";
				}
			}
			if($db_type==2 || $db_type==1 )
			{
			
			if($rID)
				{
					oci_commit($con); 
					echo "0**".$txt_task_name."**0";
				}
				else
				{
					oci_rollback($con);
					echo "5**"."0"."**0";
				}
			}
			disconnect($con);
			die;
		}
	}
	else if ($operation==1)   // Update here------------------------------------------Update here------------------------------------------
	{
		if (is_duplicate_field( "yarn_type_id", "lib_yarn_type", " yarn_type_id=$cbo_yarn_type_id and id<>$update_id and status_active=1 and is_deleted=0" ) == 1)
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
			
			$field_array="yarn_type_id*yarn_type_short_name*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_yarn_type_id."*".$txt_yarn_type_short_name."*'".$user_id."'*'".$pc_date_time."'*".$cbo_status."*0"; 
			$rID=sql_update("lib_yarn_type",$field_array,$data_array,"id","".$update_id."",1);
			
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
		
	else if($operation==2)	//Delete here----------------------------------------------Delete here--------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";
		$rID=sql_delete("lib_yarn_type",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID )
			{
			mysql_query("COMMIT");
			echo "2**".str_replace("'","",$txt_task_name)."**0";  
			}
			else
			{
			mysql_query("ROLLBACK"); 
			echo "7**".str_replace("'","",$txt_task_name)."**1";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
		if($rID )
			{
			oci_commit($con);
			echo "2**".str_replace("'","",$txt_task_name)."**0";  
			}
		else
			{
			oci_rollback($con); 
			echo "7**".str_replace("'","",$txt_task_name)."**1";
			}
		}
		disconnect($con);
		die;
	}
} 

if ($action=="report_settings_yarn_type")
{
	
	
	
	
	$sql="select ID, YARN_TYPE_ID, YARN_TYPE_SHORT_NAME,STATUS_ACTIVE from lib_yarn_type where is_deleted=0 order by YARN_TYPE_SHORT_NAME";

	?>
    	<div style="width:auto;" align="center" id="yarn_type_list">
    	<fieldset>
    	<div style="width:620px;">
        <table align="left" width="600" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
    		<thead>
            	<th width="35" align="center">SL</th>
            	<th width="200">Yarn Type Name</th>
            	<th width="200">Short Name</th>
            	<th>Status</th>
        	</thead>
        </table> 
        </div>
        
   		<div style="overflow-y:scroll; max-height:260px; width:620px;"  align="center">
     	<table align="left" id="tbl_task_list" width="600" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">  
    	<?
		
		$i=0;
		$data_array=sql_select( $sql );
		foreach($data_array as $data)
		{
			$i++;
			$bgcolor=( $i%2==0 ? "#E9F3FF" : "#FFFFFF" );
		?>
        	<tbody>
         		<tr bgcolor="<? echo $bgcolor; ?>"style="cursor:pointer" onclick="get_php_form_data(<? echo $data[ID]; ?>,'set_update_form_data','requires/yarn_type_entry_controller')">
            		<td  align="center" width="35"><? echo $i; ?></td>
            		<td width="200"><? echo  $yarn_type_for_entry[$data[YARN_TYPE_ID]]; ?></td>
            		<td width="200"><? echo $data[YARN_TYPE_SHORT_NAME]; ?></td>
            		<td align="center"><? echo $row_status[$data[STATUS_ACTIVE]]; ?></td>
         		</tr>
         	</tbody>
    	<?
		}
		?>
    
   		</table>
   		</div>
    	</fieldset>
    	</div>
     
	<? 
}



if ($action=="yarn_type_list")
{
	
	$save_yarn_type_arr = return_library_array("select YARN_TYPE_ID,STATUS_ACTIVE from lib_yarn_type where is_deleted=0","YARN_TYPE_ID","STATUS_ACTIVE");
	?>
        
    <span style="background:#33CC00; padding:0 7px; border-radius:9px; cursor:pointer;"></span>&nbsp; Active&nbsp;                 
    <span style="background:#FFF000; padding:0 7px; border-radius:9px; cursor:pointer;"></span> &nbsp;Inactive &nbsp;                 
    <span style="background:#FF0000; padding:0 7px; border-radius:9px; cursor:pointer;"></span> &nbsp;Cancelled                 
        
        <fieldset>
    	<div style="width:250px;">
        <table align="left" width="250" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
    		<thead>
            	<th width="35" align="center">ID</th>
            	<th>Yarn Type</th>
        	</thead>
        </table> 
        </div>
        
   		<div style="overflow-y:scroll; max-height:410px; width:270px;"  align="center">
     	<table align="left" id="tbl_yarn_type_list" width="250" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">  
    	<?
		
		$i=0;
		foreach($yarn_type_for_entry as $yarn_type_id=>$yarn_type_for_entry)
		{
			$i++;
			$bgcolor=( $i%2==0 ? "#E9F3FF" : "#FFFFFF" );
			$onclickFunction=$onclickFunction='onclick="alert(\'Duplicate Select Not Allowed.\');"';
			
			if($save_yarn_type_arr[$yarn_type_id]==1){$bgcolor="#33CC00";}
			else if($save_yarn_type_arr[$yarn_type_id]==2){$bgcolor="#FFF000";}
			else if($save_yarn_type_arr[$yarn_type_id]==3){$bgcolor="#FF0000";}
			else{$onclickFunction='onclick="fn_set_yarn_type('.$yarn_type_id.');"';}
			
		?>
        	<tbody>
         		<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" <? echo $onclickFunction;?>>
            		<td  align="center" width="35"><? echo $yarn_type_id; ?></td>
            		<td><? echo $yarn_type_for_entry; ?></td>
         		</tr>
         	</tbody>
    	<?
		}
		?>
    
   		</table>
   		</div>
    	</fieldset>
     
	<? 
}



if ($action=="set_update_form_data")
{
	
	$data_arr=sql_select("select ID, YARN_TYPE_ID, YARN_TYPE_SHORT_NAME,STATUS_ACTIVE from lib_yarn_type where id='$data'");
	foreach ($data_arr as $row)
	{
		echo "document.getElementById('cbo_yarn_type_id').value='".$row[YARN_TYPE_ID]."';\n";
		echo "document.getElementById('txt_yarn_type_short_name').value='".$row[YARN_TYPE_SHORT_NAME]."';\n";
		echo "document.getElementById('cbo_status').value='".$row[STATUS_ACTIVE]."';\n";
		echo "document.getElementById('update_id').value='".$row[ID]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."','fnc_yarn_type_entry',1);\n"; 
	}
}

?>
