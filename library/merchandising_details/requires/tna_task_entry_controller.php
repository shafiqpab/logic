<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_id=$_SESSION['logic_erp']['user_id'];

if ( $action=="load_drop_down_page_link" )
{
	echo create_drop_down( "cbo_page_link", 210, "select m_menu_id,menu_name from  main_menu where m_module_id='$data' and report_menu=0 and status=1 and f_location<>'' ","m_menu_id,menu_name", 1, "-- Select Link Page --", $selected, "",0 );	
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)	// Insert Here-----------------------------Insert Here------------------------
	{
		if (is_duplicate_field( "task_name", "lib_tna_task", " task_name=$txt_task_name and  task_type=$cbo_task_type and status_active=1 and is_deleted=0" ) == 1)
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
			$field_array="id, task_catagory, task_type, task_name, task_short_name, module_name, link_page, penalty,row_status,completion_percent, task_sequence_no, task_group, task_group_sequence, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$cbo_task_catagory.",".$cbo_task_type.",".$txt_task_name.",".$txt_short_name.",".$cbo_module_name.",".$cbo_page_link.",".$txt_Penalty.",".$cbo_row_status.",".$txt_completion_percent.",".$txt_task_sequence.",'".str_replace("'","",$txt_task_group)."',".$txt_group_sequence.",'".$user_id."','".$pc_date_time."',".'1'.",0)"; 
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
		if (is_duplicate_field( "task_name", "lib_tna_task", " task_name=$txt_task_name and  task_type=$cbo_task_type and id<>$update_id and status_active=1 and is_deleted=0" ) == 1)
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
			
			$field_array="task_catagory*task_type*task_name*task_short_name*module_name*link_page*penalty*completion_percent*task_sequence_no*task_group*task_group_sequence*row_status*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_task_catagory."*".$cbo_task_type."*".$txt_task_name."*".$txt_short_name."*".$cbo_module_name."*".$cbo_page_link."*".$txt_Penalty."*".$txt_completion_percent."*".$txt_task_sequence."*".$txt_task_group."*".$txt_group_sequence."*".$cbo_row_status."*'".$user_id."'*'".$pc_date_time."'*".$cbo_row_status."*0"; 
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
	
	$sql="select id, task_name, task_short_name ,task_sequence_no, task_catagory, module_name, link_page,penalty,row_status,task_type,task_group,task_group_sequence from lib_tna_task where is_deleted=0 and task_type=$data order by task_group,task_sequence_no";
	
	//echo $sql;die;

	?>
    	<div style="width:auto;" align="center" id="tna_task_list">
    	<fieldset>
    	<div style="width:670px;">
        <table align="left" width="650" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
    		<thead>
            <tr>
                <th colspan="8">
					<?
                    echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
                    ?> 
                </th>
            </tr>
              <tr>  
                <th width="30" align="center">SL</th>
            	<th width="70">Task Type</th>
            	<th width="180">Task Name</th>
            	<th width="100">Task Short Name</th>
            	<th width="50">Penalty</th>
            	<th width="40">Seq No</th>
            	<th>Group</th>
            	<th width="45">Group Seq</th>
             </tr>
        </thead>
        </table> 
        </div>
        
   		<div style="overflow-y:scroll; max-height:200px; width:670px;"  align="center">
     	<table align="left" id="tbl_task_list" width="650" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">  
    	<?
		
		$i=0;
		$data_array=sql_select( $sql );
		foreach($data_array as $data)
		{
			$i++;
			$bgcolor=( $i%2==0 ? "#E9F3FF" : "#FFFFFF" );
		?>
        	<tbody>
         		<tr bgcolor="<? echo $bgcolor; ?>"style="cursor:pointer" onclick="get_php_form_data(<? echo $data[csf('id')]; ?>,'set_update_form_data','requires/tna_task_entry_controller')">
            		<td  align="center" width="30"><? echo $i; ?></td>
            		<td width="70"><? echo  $template_type_arr[$data[csf('task_type')]]; ?></td>
            		<td width="180"><? echo  $tna_task_name[$data[csf('task_name')]]; ?></td>
            		<td width="100" title="<? echo $data[csf('task_short_name')]; ?>"><? echo $data[csf('task_short_name')]; ?></td>
            		<td width="50" align="right"><? echo number_format($data[csf('penalty')],2); ?></td>
            		<td width="40" align="center"><? echo $data[csf('task_sequence_no')]; ?></td>
            		<td><? echo $data[csf('task_group')]; ?></td>
            		<td width="45" align="center"><? echo $data[csf('task_group_sequence')]; ?></td>
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



if ($action=="tna_task_list")
{
	//echo $data;die;
	//$save_task_arr = return_library_array("select task_name,status_active from lib_tna_task where is_deleted=0","task_name","status_active");
	$save_task_sql_result=sql_select("select TASK_NAME,STATUS_ACTIVE,TASK_TYPE from lib_tna_task where is_deleted=0");
	foreach ($save_task_sql_result as $row)
	{
		$save_task_arr[$row[TASK_TYPE]][$row[TASK_NAME]]=$row[STATUS_ACTIVE];	
	}
	
	
	
	
	?>
        
    <span style="background:#33CC00; padding:0 7px; border-radius:9px; cursor:pointer;"></span>&nbsp; Active&nbsp;                 
    <span style="background:#FFF000; padding:0 7px; border-radius:9px; cursor:pointer;"></span> &nbsp;Inactive &nbsp;                 
    <span style="background:#FF0000; padding:0 7px; border-radius:9px; cursor:pointer;"></span> &nbsp;Cancelled                 
        
        <fieldset>
    	<div style="width:250px;">
        <table align="left" width="250" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
    		<thead>
            	<th width="35" align="center">SL</th>
            	<th><?= $template_type_arr[$data];?> Task Name</th>
        	</thead>
        </table> 
        </div>
        
   		<div style="overflow-y:scroll; max-height:410px; width:270px;"  align="center">
     	<table align="left" id="tbl_tna_task_list" width="250" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">  
    	<?
		
		$task_arr[1]=array(1,91,30,87,100,180,129,179,123,22,25,34,167,51,198,83,173,152,269,168,47,120,121,128,263,29,13,194,134,189,11,28,252,31,70,84,86,88,72,224,21,262,14,251,250,26,258,132,223,185,177,181,90,2,229,210,85,46,32,5,4,191,260,24,133,143,255,193,81,183,184,270,61,110,74,48,8,23,17,63,125,257,226,242,135,175,37,212,268,50,71,101,45,41,80,52,40,33,261,130,178,188,165,131,228,254,136,266,265,200,199,64,36,7,18,259,3,190,142,192,196,195,169,171,89,27,236,221,186,172,176,267,60,12,122,73,9,10,187,127,126,82,15,16,256,166,197,219,150,174,62,19,159,170,271,272,273,274,275,276,277,278,279,300,301,302,305,306,307,308,309,310,311,312,313,314,264,315,316,317,318,319,320 , 321,322,323,324,325,326,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,343,344,345,346,347,352,353,243,354);//knit
		
		$task_arr[2]=array(51,52,167,47,48,218,213,217,207,209,205,72,210,211,46,215,201,74,63,239,61,206,212,40,50,80,199,214,200,33,64,203,216,202,10,73,62,60,204,208,346);//textile
		$task_arr[3]=array(244,247,245,51,25,167,47,120,121,13,177,70,14,249,183,32,46,240,5,17,246,110,242,48,131,80,178,101,243,241,130,182,10,15,12,60,346,71,20,89,90,88,348,349,350,351,86,270,212);//sweater 

		$task_arr[4]=array(1,2,5,7,8,9,10,12,13,18,18,19,25,26,27,30,31,32,34,36,37,40,45,46,47,61,64,70,71,72,73,74,80,83,84,85,86,87,88,89,90,100,101,110,120,125,126,128,129,132,133,135,152,159,165,167,168,173,177,185,189,194,195,198,200,210,212,219,221,226,229,250,251,254,256,258,268,270,271,273,274,275,276,277,279,300,301,346);//Lingerie
		$task_arr[6]=array(1,91,30,87,100,180,129,179,123,22,25,34,167,51,198,83,173,152,269,168,47,120,121,128,263,29,13,194,134,189,11,28,252,31,70,84,86,88,72,224,21,262,14,251,250,26,258,132,223,185,177,181,90,2,229,210,85,46,32,5,4,191,260,24,133,143,255,193,81,183,184,270,61,110,74,48,8,23,17,63,125,257,226,242,135,175,37,212,268,50,71,101,45,41,80,52,40,33,261,130,178,188,165,131,228,254,136,266,265,200,199,64,36,7,18,259,3,190,142,192,196,195,169,171,89,27,236,221,186,172,176,267,60,12,122,73,9,10,187,127,126,82,15,16,256,166,197,219,150,174,62,19,159,170,271,272,273,274,275,276,277,278,279,300,301,302,305,306,307,308,309,310,311,346,322,243,354);//woven
		
		//$task_arr[0]=$task_arr[1]+$task_arr[2]+$task_arr[3];
		
		$i=0;
		foreach($task_arr[$data] as $task_id)
		{
			$i++;
			$bgcolor=( $i%2==0 ? "#E9F3FF" : "#FFFFFF" );
			$onclickFunction=$onclickFunction='onclick="alert(\'Duplicate Select Not Allowed.\');"';
			
			if($save_task_arr[$data][$task_id]==1){$bgcolor="#33CC00";}
			else if($save_task_arr[$data][$task_id]==2){$bgcolor="#FFF000";}
			else if($save_task_arr[$data][$task_id]==3){$bgcolor="#FF0000";}
			else{$onclickFunction='onclick="set_task('.$task_id.','.$data.');"';}
			
		?>
        	<tbody>
         		<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" <? echo $onclickFunction;?>>
            		<td  align="center" width="35"><? echo $i; ?></td>
            		<td title="Task Id:<?= $task_id; ?>"><? echo $tna_task_name[$task_id]; ?></td>
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
	$data_arr=sql_select("select id, task_name, task_short_name ,task_sequence_no,completion_percent,task_type, task_catagory, module_name, link_page,penalty,row_status,task_group,task_group_sequence from lib_tna_task where id='$data'");
	foreach ($data_arr as $row)
	{
		echo "document.getElementById('cbo_task_catagory').value='".$row[csf("task_catagory")]."';\n";
		//echo "load_drop_down('requires/tna_task_entry_controller', document.getElementById('cbo_task_catagory').value, 'load_drop_down_task_category', 'task_name_td');\n";
		echo "document.getElementById('txt_task_name').value='".$row[csf("task_name")]."';\n";
		echo "document.getElementById('txt_completion_percent').value='".$row[csf("completion_percent")]."';\n";
		echo "document.getElementById('txt_short_name').value='".$row[csf("task_short_name")]."';\n";
		echo "document.getElementById('cbo_module_name').value='".$row[csf("module_name")]."';\n";
		echo "load_drop_down('requires/tna_task_entry_controller', document.getElementById('cbo_module_name').value, 'load_drop_down_page_link', 'page_link_td');\n";
		echo "document.getElementById('cbo_page_link').value='".$row[csf("link_page")]."';\n";
		echo "document.getElementById('txt_Penalty').value='".number_format($row[csf('penalty')],2)."';\n";
		echo "document.getElementById('cbo_row_status').value='".$row[csf("row_status")]."';\n";
		echo "document.getElementById('update_id').value='".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_task_type').value='".$row[csf("task_type")]."';\n";
		echo "document.getElementById('txt_task_sequence').value='".$row[csf("task_sequence_no")]."';\n";
		
		echo "document.getElementById('txt_task_group').value='".$row[csf("task_group")]."';\n";
		echo "document.getElementById('txt_group_sequence').value='".$row[csf("task_group_sequence")]."';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."','fnc_tna_task_entry',1);\n"; 
		
		
		//echo "document.getElementById('cbo_task_type').disabled = true;\n";
		
	}
}

?>
