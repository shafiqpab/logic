<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_page_link")
{
	echo create_drop_down( "cbo_page_link", 210, "select m_menu_id,menu_name from  main_menu where m_module_id='$data' and report_menu=0 and status=1 and f_location<>'' ","m_menu_id,menu_name", 1, "-- Select Link Page --", $selected, "",0 );	
}

if ($action=="load_drop_down_task_category")
{
	$sample_approval_task = return_library_array("select id,sample_name from lib_sample ","id","sample_name");
	$trims_approval_task = $trim_type;//return_library_array("select id,item_name from lib_item_group","id","item_name");
	$ttask=$category_wise_task_array[$data]; $dd=$ttask;
	//echo $dd;die;
	
	//echo create_drop_down( "txt_task_name", 380, $dd, "", 1, "-- Select --", $selected, "", 0 );	
	
	
	//die;
	switch($data)
	{
		case 1:			//General
			echo create_drop_down( "txt_task_name", 380, $general_task,"", 1, "-- Select General Task --", $selected, "",0 );	
			break;
		case 5:			//Sample Approval
			echo create_drop_down( "txt_task_name", 380, "select id,sample_name from  lib_sample ","id,sample_name", 1, "-- Select Sample --", $selected, "",0 );	
			break;
			
		case 6:			//Lab Dip Approval
			echo create_drop_down( "txt_task_name", 380, $lapdip_task_name,"", 1, "-- Select LabDip Approval --", $selected, "",0 );	
			break;
			
		case 7:			//Trims Approval
			echo create_drop_down( "txt_task_name", 380, "select id,item_name from lib_item_group where item_category=4 and status_active =1 and is_deleted=0","id,item_name", 1, "-- Select Item --", $selected, "",0 );
			break;
			
		case 8:			//Embellishment Approval
			echo create_drop_down( "txt_task_name", 380, $emblishment_name_array,"", 1, "-- Select Embellishment Approval --", $selected, "",0 );	
			break;
			
		case 0:			//-- Select Catagory--
			echo  '<input type="text" name="txt_task_name" id="txt_task_name" class="text_boxes" style="width:350px" title="TNA Task Name" />	';
			break;
		case 9:			//Test Approval
			echo create_drop_down( "txt_task_name", 380, $test_approval_task,"", 1, "-- Select Test Approval --", $selected, "",0 );	
			break;
		case 15:		//Purchase
			echo create_drop_down( "txt_task_name", 380, $purchase_task,"", 1, "-- Select Purchase --", $selected, "",0 );	
			break;
		case 20:		//Material Receive
			echo create_drop_down( "txt_task_name", 380, $material_receive_task,"", 1, "-- Select Material --", $selected, "",0 );	
			break;
		case 25:		//Fabric Production
			echo create_drop_down( "txt_task_name", 380, $fabric_production_task,"", 1, "-- Select Fabric Task --", $selected, "",0 );	
			break;
		case 26:		//Garments Production
			echo create_drop_down( "txt_task_name", 380, $garments_production_task,"", 1, "-- Select Garments Task --", $selected, "",0 );	
			break;
		case 30:		//Inspection
			echo create_drop_down( "txt_task_name", 380, $inspection_task,"", 1, "-- Select Inspection --", $selected, "",0 );	
			break;
		case 35:		//Export
			echo create_drop_down( "txt_task_name", 380, $export_task,"", 1, "-- Select Export --", $selected, "",0 );	
			break; 
	}
} 

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)	// Insert Here-----------------------------Insert Here------------------------
	{
		
		
		if (is_duplicate_field( "task_name", "lib_tna_task", "task_catagory=$cbo_task_catagory and task_name=$txt_task_name and task_type=$chk_task_type and status_active=1 and is_deleted=0" ) == 1)
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
			
		
			$id=return_next_id( "id", "lib_tna_task", 1 );
			$field_array="id, task_catagory, task_name, task_short_name,task_type, module_name, link_page, penalty,row_status,completion_percent, task_sequence_no, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$cbo_task_catagory.",".$txt_task_name.",".$txt_short_name.",".$chk_task_type.",".$cbo_module_name.",".$cbo_page_link.",".$txt_Penalty.",".$cbo_row_status.",".$txt_completion_percent.",".$txt_task_sequence.",'".$user_id."','".$pc_date_time."',".'1'.",0)"; 
			$rID=sql_insert("lib_tna_task",$field_array,$data_array,1); 
			
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
		if (is_duplicate_field( "task_name", "lib_tna_task", " task_catagory=$cbo_task_catagory and task_name=$txt_task_name and task_type=$chk_task_type and id<>$update_id" ) == 1)
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
			
			$field_array="task_catagory*task_name*task_short_name*task_type*module_name*link_page*penalty*completion_percent*task_sequence_no*row_status*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_task_catagory."*".$txt_task_name."*".$txt_short_name."*".$chk_task_type."*".$cbo_module_name."*".$cbo_page_link."*".$txt_Penalty."*".$txt_completion_percent."*".$txt_task_sequence."*".$cbo_row_status."*'".$user_id."'*'".$pc_date_time."'*".'1'."*0"; 
			$rID=sql_update("lib_tna_task",$field_array,$data_array,"id","".$update_id."",1);
			
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
		$rID=sql_delete("lib_tna_task",$field_array,$data_array,"id","".$update_id."",1);
		
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

if ($action=="report_settings_tna_task")
{
	
	
	$sample_approval_task = return_library_array("select id,sample_name from lib_sample ","id","sample_name");
	$trims_approval_task =return_library_array("select id,item_name from lib_item_group  where item_category=4 and status_active =1 and is_deleted=0","id","item_name");
	
	
	//die;

	
	
	$sql="select id, task_name, task_short_name ,task_sequence_no, task_catagory, module_name, link_page,penalty,row_status,task_type from lib_tna_task where is_deleted=0 order by id";

	?>
    	<div style="width:auto;" align="center" id="tna_task_list">
    	<fieldset>
    	<div style="width:720px;">
        <table align="center" width="720" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
    		<thead>
            	<th width="50" align="center">SL</th>
         		<th width="150">Task Category</th>
            	<th width="180">Task Name</th>
            	<th width="150">Task Short Name</th>
            	<th width="80">Penalty</th>
            	<th>Sequence No</th>
        	</thead>
        </table> 
        </div>
        
   		<div style="overflow-y:scroll; max-height:200px; width:720px;"  align="center">
     	<table align="center" id="tbl_task_list" width="700" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">  
    	<?
		
		$i=0;
		$data_array=sql_select( $sql );
		foreach($data_array as $data)
		{
				switch($data[csf('task_catagory')])
	{
		case 1:			//General
			$tna_task_name=$general_task;	
			break;
		case 5:			//Sample Approval
			$tna_task_name=$sample_approval_task;	
			break;
			
		case 6:			//Lab Dip Approval
			$tna_task_name=$lapdip_task_name;	
			break;
			
		case 7:			//Trims Approval
			$tna_task_name=$trims_approval_task;	
			break;
			
		case 8:			//Embellishment Approval
			$tna_task_name=$emblishment_name_array;	
			break;
			
		case 0:			//-- Select Catagory--
			$tna_task_name=$blank_array;	
			break;
		case 9:			//Test Approval
			$tna_task_name=$test_approval_task;	
			break;
		case 15:		//Purchase
			$tna_task_name=$purchase_task;	
			break;
		case 20:		//Material Receive
			$tna_task_name=$material_receive_task;	
			break;
		case 25:		//Fabric Production
		   $tna_task_name=$fabric_production_task;	
			break;
		case 26:		//Garments Production
		    $tna_task_name=$garments_production_task;
			break;
		case 30:		//Inspection
		 $tna_task_name=$inspection_task;
			break;
		case 35:		//Export
		     $tna_task_name=$export_task;
			break; 
			
	}
			
		//print_r($tna_task_name);
			$i++;
			$bgcolor=( $i%2==0 ? "#E9F3FF" : "#FFFFFF" );
		?>
        	<tbody>
         		<tr bgcolor="<? echo $bgcolor; ?>"style="cursor:pointer" onclick="get_php_form_data(<? echo $data[csf('id')]; ?>,'set_update_form_data','requires/tna_task_entry_controller')">
            		<td  align="center" width="50"><? echo $data[csf('id')]; ?></td>
         			<td width="150"><? echo $tna_task_catagory[$data[csf('task_catagory')]]; ?></td>
            		<td width="180"><? echo  $tna_task_name[$data[csf('task_name')]];
					// echo $tna_task_array[$data['task_catagory'].$data['task_name']] ?></td>
            		<td width="150" title="<? echo $data[csf('task_short_name')]." ".$lapdip_task_name[$data[csf('task_type')]]; ?>"><? echo $data[csf('task_short_name')]." ".$lapdip_task_name[$data[csf('task_type')]]; ?></td>
            		<td width="80" align="right"><? echo number_format($data[csf('penalty')],2); ?></td>
            		<td align="right"><? echo $data[csf('task_sequence_no')]; ?></td>
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

if ($action=="set_update_form_data")
{
	$data_arr=sql_select("select id, task_name, task_short_name ,task_sequence_no,completion_percent,task_type, task_catagory, module_name, link_page,penalty,row_status from lib_tna_task where id='$data'");
	foreach ($data_arr as $row)
	{
		echo "document.getElementById('cbo_task_catagory').value='".$row[csf("task_catagory")]."';\n";
		echo "load_drop_down('requires/tna_task_entry_controller', document.getElementById('cbo_task_catagory').value, 'load_drop_down_task_category', 'task_name_td');\n";
		echo "document.getElementById('txt_task_name').value='".$row[csf("task_name")]."';\n";
		echo "document.getElementById('txt_completion_percent').value='".$row[csf("completion_percent")]."';\n";
		echo "document.getElementById('txt_short_name').value='".$row[csf("task_short_name")]."';\n";
		echo "document.getElementById('cbo_module_name').value='".$row[csf("module_name")]."';\n";
		echo "load_drop_down('requires/tna_task_entry_controller', document.getElementById('cbo_module_name').value, 'load_drop_down_page_link', 'page_link_td');\n";
		echo "document.getElementById('cbo_page_link').value='".$row[csf("link_page")]."';\n";
		echo "document.getElementById('txt_Penalty').value='".number_format($row[csf('penalty')],2)."';\n";
		echo "document.getElementById('cbo_row_status').value='".$row[csf("row_status")]."';\n";
		echo "document.getElementById('update_id').value='".$row[csf("id")]."';\n";
		echo "document.getElementById('chk_task_type').value='".$row[csf("task_type")]."';\n";
		echo "document.getElementById('txt_task_sequence').value='".$row[csf("task_sequence_no")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."','fnc_tna_task_entry',1);\n"; 
	}
}

?>
