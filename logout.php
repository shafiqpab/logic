<body onLoad="setTimeout('window.close()', 5);">
	<?php 
	session_start();
	include('includes/common.php');
	
	//$pc_time= add_time(date("H:i:s",time()),360);  
	//$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));
	
	if($db_type==0) $pc_time = date("Y-m-d H:i:s",time()); 
	else $pc_time = date("d-M-Y h:i:s A",time()); 
	
	if($db_type==0) $pc_date = date("Y-m-d",time());
	else $pc_date = date("d-M-Y",time());
		
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="logout_date,logout_time";
		$data_array="'".$pc_date."','".$pc_time."'";
		//$rID=sql_update("login_history",$field_array,$data_array,"id","'".$_SESSION['logic_erp']["history_id"]."'",1);
		$rID=execute_query("update login_history set logout_time='$pc_time',logout_date='$pc_date' where id='".$_SESSION['logic_erp']["history_id"]."'");		
		
		 
			if($db_type==0)
			{
				if($rID){
					mysql_query("COMMIT");  
					//echo "0****".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					//echo "10****".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
			 	if($rID)
				{
					oci_commit($con);   
					//echo "0****".$rID;
				}
				else{
					oci_rollback($con);
					//echo "10****".$rID;
				}
			}
		 
		
		disconnect($con);
	 
	//hr_admin-report-hrm-includes-tmp_report_file folder file delete
	//punch report file
	$temp_file_name_in_out=$_SESSION['temp_file_name_in_out'];
	//monthly_attn_summery_audit file
	$temp_file_name_audit=$_SESSION['temp_file_name_audit'];
	//unlink('hr_admin/report/hrm/includes/tmp_report_file/'.$temp_file_name_in_out);
	//unlink('hr_admin/report/hrm/includes/tmp_report_file/'.$temp_file_name_audit);
	
	unset($_SESSION['logic_erp']);
	//session_destroy();
	$_SESSION['logic_erp']['user_id'] = '';
	$_SESSION['logic_erp']['data'] = '';
	$_SESSION['project_url']='';
	//echo $_SESSION['logic_erp_graph'][1]."<br>";
	//$_SESSION['logic_erp']["fuad"] = '';
	//unset($_SESSION['logic_erp_graph']["fuad"]);

	echo '<center><br><br><br><br><div style="background:#FFFF00; width:200px; color:#CC0033; font-weight:bold; border:#990033 1px solid;">Thank you for Using this Software</div></center>';
	//header("location: index.php");
	?>
	<script>
		localStorage.setItem('login_notification','');
	</script>
	<?
	header("location: login.php");
	?>
</body>