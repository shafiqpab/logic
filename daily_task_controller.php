<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('includes/common.php');
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_team_member")
{
	echo create_drop_down( "cbo_team_member_id", 150, "select id,team_member_name from  team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();	
}


 

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	$txt_end_minutes=$process[0]["txt_end_minutes"];
	$update_id=$process[0]["update_id"];
	$cbo_type=$process[0]["cbo_type"];
	$txt_issue_details=$process[0]["txt_issue_details"];
	$txt_receive_date=$process[0]["txt_receive_date"];
	$txt_comments=$process[0]["txt_comments"];
	$txt_sys_id=$process[0]["txt_sys_id"];
	$operation=$process[0]["operation"];
	$cbo_company_id=$process[0]["cbo_company_id"];
	$cbo_team_leader_id=$process[0]["cbo_team_leader_id"];
	$cbo_team_member_id=$process[0]["cbo_team_member_id"];
	
	
	$txt_end_minutes=str_replace("'", "", $txt_end_minutes);
	$update_id=str_replace("'", "", $update_id);
	$cbo_type=str_replace("'", "", $cbo_type);
	$txt_receive_date=str_replace("'", "", $txt_receive_date);
	$txt_sys_id=str_replace("'", "", $txt_sys_id);

	
	//extract(check_magic_quote_gpc( $process ));
	
	//echo "h";die;

	if ($operation==0)  // Insert Here
	{
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$system_id="";	
		if($update_id=="")
		{
			

			if($db_type==0) $year_cond=" and YEAR(insert_date)"; else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";

			$id = return_next_id_by_sequence("daily_task_mst_seq", "daily_task_mst", $con);
		
			$new_system_id = explode("*", return_next_id_by_sequence("daily_task_mst_seq", "daily_task_mst",$con,1,$cbo_company_id,date("Y",time())));
			$field_array="id, issue_number_prefix, issue_number_prefix_num, issue_num, task_date, minutes_type, minutes, activity_detials, comments,company_id,team_leader_id,team_member_id, inserted_by,user_id, insert_date,status_active, is_deleted";
			
			//print_r($new_system_id);die;
			
			$system_id=$new_system_id[0];

			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."','".$txt_receive_date."',".$cbo_type.",".$txt_end_minutes.",".$txt_issue_details.",".$txt_comments.",".$cbo_company_id.",".$cbo_team_leader_id.",".$cbo_team_member_id.",".$_SESSION['logic_erp']['user_id'].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		}

		
		//echo "insert into daily_task_mst (".$field_array.") values ".$data_array; die;

		
		
		if(str_replace("'","",$update_id)=="")
		{
		 $rID=sql_insert("daily_task_mst",$field_array,$data_array,0);
		 if($rID) $flag=1;else $flag=0;
		}
		else
		{
		$rID=sql_update("daily_task_mst",$field_array,$data_array,"id","".$update_id."",1);
		 if($rID) $flag=1;else $flag=0;	
		}
			
	
		 
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$system_id);	
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$system_id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$system_id);	
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$system_id);
			}
		}
		disconnect($con);
		
	
		
		
	}
	if ($operation==1)  // Update Here
	{
		 $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
		if($db_type==0)
		{
			if ($txt_receive_date!="") $txt_receive_date  = change_date_format($txt_receive_date, "yyyy-mm-dd", "-");
		}

		if($db_type==2)
		{
			if ($txt_receive_date!="") $txt_receive_date  = change_date_format($txt_receive_date, "yyyy-mm-dd", "-",1);
		}

		$field_array = "task_date*minutes_type*minutes*activity_detials*comments*company_id*team_leader_id*team_member_id";
		$data_array ="'".$txt_receive_date."'*".$cbo_type . "*". $txt_end_minutes . "*" . $txt_issue_details."*".$txt_comments."*".$cbo_company_id."*".$cbo_team_leader_id."*".$cbo_team_member_id;
		
		 $rID=sql_update("daily_task_mst",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$update_id)."**".$txt_sys_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id)."**".$txt_sys_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".$txt_sys_id;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id)."**".$txt_sys_id;
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
		
		$field_array="update_date*status_active*is_deleted";
	    $data_array="'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("daily_task_mst",$field_array,$data_array,"id",$update_id);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$update_id);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id);
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


if($action=='openmypage_sys_opup'){

	echo load_html_head_contents("Daily Task Pop up","", 1, 1, $unicode);
	?>
	<script>

		function js_set_value(data) {
			$('#hidden_data').val(data);
			parent.emailwindow.hide();
		}
		

	</script>
	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="300" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			        <thead>
			            <tr>
			                
			                <th colspan="2">Task Date</th>
			                <th>&nbsp;</th>
			            </tr>
			        </thead>
			        <tbody>
			            <tr class="general">
			                
			               
			               <input type="hidden" name="hidden_data" id="hidden_data">
			                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From" /></td>
			                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To" /></td>
			                <td align="center">
			                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_task_search_list_view', 'search_div', 'daily_task_controller', 'setFilterGrid(\'list_view\',-1)') " style="width:100px;" /></td>
			            </tr>
			           
			        </tbody>
			    </table>
    			<div id="search_div"> </div>
    		</form>
   		</div>
	</body>
	<script src="includes/functions_bottom.js" type="text/javascript"></script>
	</html>

	<?
	exit();

}
if ($action=="create_task_search_list_view")
{
	$data=explode('_',$data);
	

	$search_field_cond = '';
	
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $date_condition  = "and task_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $date_condition ="";
	}

	if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $date_condition  = "and task_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $date_condition ="";
	}


	$user_condition=" and user_id=".$_SESSION['logic_erp']['user_id'];

    $sql= "select id,issue_num,activity_detials,minutes,minutes_type,comments,task_date,user_id,company_id,team_leader_id,team_member_id from daily_task_mst where status_active=1 and is_deleted=0  $user_condition $date_condition";
	 //echo $sql;
	$result = sql_select($sql);
	?>
	<table class="rpt_table" id="rpt_tablelist_view" rules="all" width="600" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="35">SL No</th>
                <th width="100">Issue No</th>
                <th width="300">Description</th>
                <th width="60">Minute</th>
                <th width="60">Task date</th>
                <th >Type</th>
                
            </tr>
        </thead>
		<tbody>
			<?
			$minutes_type_arr=array(1=>"Idle",2=>"Active");
			$i=0;
			foreach($result as $row )
			{
				$i++;
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$data=$row[csf('id')]."__".$row[csf('issue_num')]."__".$row[csf('activity_detials')]."__".$row[csf('minutes')]."__".$row[csf('minutes_type')]."__".$row[csf('comments')]."__".change_date_format($row[csf('task_date')])."__".change_date_format($row[csf('task_date')])."__".$row[csf('company_id')]."__".$row[csf('team_leader_id')]."__".$row[csf('team_member_id')];
            ?>
            <tr onClick="js_set_value('<? echo $data; ?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
                <td ><? echo $i; ?></td>
                
                <td ><p><? echo $row[csf('issue_num')]; ?></p></td>
                <td ></p><? echo $row[csf('activity_detials')]; ?></td>
                <td align="center"  ><? echo $row[csf('minutes')]; ?></td>
                <td align="center"><?php echo change_date_format($row[csf('task_date')]); ?></td>
                <td align="center" ><? echo $minutes_type_arr[$row[csf('minutes_type')]]; ?></td>
            </tr>
            <?
			}
			?>
        </tbody>
    	
	</table>
    
	<?
	exit();
}

