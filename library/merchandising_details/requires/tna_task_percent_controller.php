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
 
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)	// Insert Here-----------------------------Insert Here------------------------
	{
		
		
		if (is_duplicate_field( "task_id", " tna_task_entry_percentage", "buyer_id=$cbo_buyer_name and task_id=$txt_task_name and status_active=1 and is_deleted=0" ) == 1)
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
			
			//$pc_date_time=0;
			$id=return_next_id( "id", " tna_task_entry_percentage", 1 );    
			$field_array="id,task_id,buyer_id,start_percent,end_percent,notice_before,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$txt_task_name.",".$cbo_buyer_name.",".$txt_start_percent.",".$txt_end_percent.",".$txt_notice_before.",'".$user_id."','".$pc_date_time."',".'1'.",0)"; 
		    // echo "11**0++++++".$data_array; die;
			$rID=sql_insert("tna_task_entry_percentage",$field_array,$data_array,1); 
			
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
		//echo "11**0"." task_catagory=$cbo_task_catagory and task_name=$txt_task_name and id<>$update_id";die;
		if (is_duplicate_field( "task_id", " tna_task_entry_percentage", " buyer_id=$cbo_buyer_name and task_id=$txt_task_name and id<>$update_id" ) == 1)
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
			
			$field_array="task_id*buyer_id*start_percent*end_percent*notice_before*updated_by*update_date*status_active*is_deleted";
			$data_array="".$txt_task_name."*".$cbo_buyer_name."*".$txt_start_percent."*".$txt_end_percent."*".$txt_notice_before."*".$user_id."*'".$pc_date_time."'*".'1'."*0"; 
			$rID=sql_update(" tna_task_entry_percentage",$field_array,$data_array,"id","".$update_id."",1);
			
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
	
		$field_array="updated_by*status_active*is_deleted";
		$data_array="'".$user_id."'*0*1";
		$rID=sql_delete(" tna_task_entry_percentage",$field_array,$data_array,"id","".$update_id."",1);
		
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
	
	
	$sample_atask = return_library_array("select task_name,task_short_name from  lib_tna_task ","task_name","task_short_name");
	$buyer_name = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
  
	
	$sql="select id, task_id,buyer_id,start_percent,end_percent,notice_before from  tna_task_entry_percentage where is_deleted=0 order by id";

	?>
    	<div style="width:auto;" align="center" id="tna_task_list">
    	<fieldset>
    	<div style="width:720px;">
        <table align="center" width="720" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
    		<thead>
            	<th width="50" align="center">SL</th>
                <th width="150">Buyer Name</th>
            	<th width="180">Task Name</th>
            	<th width="100">Start Percent</th>
            	<th width="100">End Percent</th>
            	<th>Notice Before</th>
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
				/*switch($data[csf('task_catagory')])
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
		     
			break; 
			
	}*/
			//$tna_task_name=$tna_task_name;
		//print_r($tna_task_name);
			$i++;
			$bgcolor=( $i%2==0 ? "#E9F3FF" : "#FFFFFF" );
		?>
        	<tbody>    	
         		<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onclick="get_php_form_data(<? echo $data[csf('id')]; ?>,'set_update_form_data','requires/tna_task_percent_controller')">
            		<td  align="center" width="50"><? echo  $i; ?></td>
         			<td width="150"><? echo  $buyer_name[$data[csf('buyer_id')]]; ?></td>
            		<td width="180"><? echo  $sample_atask[$data[csf('task_id')]];  ?></td>
            		<td width="100"  align="right" title="<? echo $data[csf('start_percent')]; ?>"><? echo $data[csf('start_percent')]; ?></td>
            		<td width="100" align="right"><? echo $data[csf('end_percent')]; ?></td>
            		<td align="right"><? echo $data[csf('notice_before')]; ?></td>
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
	$data_arr=sql_select("select id, task_id,buyer_id,start_percent,end_percent,notice_before,status_active from  tna_task_entry_percentage  where id='$data'");
	foreach ($data_arr as $row)
	{
		echo "document.getElementById('cbo_buyer_name').value='".$row[csf("buyer_id")]."';\n";
		//echo "load_drop_down('requires/tna_task_entry_controller', document.getElementById('cbo_task_catagory').value, 'load_drop_down_task_category', 'task_name_td');\n";
		echo "document.getElementById('txt_task_name').value='".$row[csf("task_id")]."';\n";
		echo "document.getElementById('txt_start_percent').value='".$row[csf("start_percent")]."';\n";
		echo "document.getElementById('txt_end_percent').value='".$row[csf("end_percent")]."';\n";
		echo "document.getElementById('txt_notice_before').value='".$row[csf("notice_before")]."';\n";
		 
		echo "document.getElementById('cbo_row_status').value='".$row[csf("status_active")]."';\n";
		echo "document.getElementById('update_id').value='".$row[csf("id")]."';\n";
		//echo "document.getElementById('chk_task_type').value='".$row[csf("task_type")]."';\n";
		 
		echo "set_button_status(1, '".$_SESSION['page_permission']."','fnc_tna_task_entry',1);\n"; 
	}
}

?>
