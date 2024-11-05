<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
extract($_REQUEST);



if($action=="tna_task_list_view")
{
	if($data==0){echo "<td colspan='4' align='center'>Please Select Company</td>";die();}
	
	$sql="select id,company_id,task_id,is_plan_manual,is_actual_manual from tna_manual_permission where company_id=$data";
	$result = sql_select( $sql );
	foreach( $result as $row ) 
	{
		$manual_status_arr[$row[csf('task_id')]]['plan']=$row[csf('is_plan_manual')];
		$manual_status_arr[$row[csf('task_id')]]['actual']=$row[csf('is_actual_manual')];
	}//echo $data;
	
	
		
	$i=1;
	foreach($tna_task_name as $task_id=>$task_name){
	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
	$plan_status=($manual_status_arr[$task_id]['plan'])?$manual_status_arr[$task_id]['plan']:2;
	$actual_status=($manual_status_arr[$task_id]['actual'])?$manual_status_arr[$task_id]['actual']:2;
	?>
	<tr bgcolor="<? echo $bgcolor; ?>">
		<td align="center"><? echo $i;?></td>
		<td title="Task ID: <? echo $task_id;?>">
		<input type="hidden" id="txt_tna_task_id_<? echo $task_id;?>" value="<? echo $task_id;?>">
		<? echo $task_name;?>
		</td>
		<td align="center">
			<? echo create_drop_down( "cbo_plan_".$task_id, 80,array(1=>'Manual',2=>'Auto'),"", 0, "", $plan_status,""); ?>
		</td>
		<td align="center">
			<? echo create_drop_down( "cbo_actual_".$task_id, 80,array(1=>'Manual',2=>'Auto'),"", 0, "", $actual_status,""); ?>
		</td>
	</tr>
	<? 
	$i++;
	} 
	?>
    			
    <td colspan="4" align="center" class="button_container">
        <? 
		
		$save_update=($result[0][csf('is_plan_manual')]=='')?0:1;
		
        echo load_submit_buttons($_SESSION['page_permission'], "fnc_tna_manual_permission", $save_update,0 ,"reset_form('tnamanualpermission_1','','')",1);
        ?>
        
    </td>				

<?	
	die; 	 
}







if($action=="save_update_delete")
{
		
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$cbo_company=str_replace("'",'',$cbo_company);
	$data_string_arr=explode('*',$data_string);
	
	if ($operation==0)  
	{		
		$con = connect();
		if($db_type==0)
		{
			  mysql_query("BEGIN");
		}
		 
		$id=return_next_id( "id", "tna_manual_permission", 1 ) ;
		$field_array="id, company_id,task_id,is_plan_manual, is_actual_manual";
		$data_array=array();
		foreach($data_string_arr as $data_string){
			list($task_id,$plan_status,$actual_status)=explode('_',$data_string);
			$data_array[]="(".$id.",".$cbo_company.",".$task_id.",".$plan_status.",".$actual_status.")";
			$id++;
		}
		$rID=sql_insert("tna_manual_permission",$field_array,implode(',',$data_array),1);
		
			
		if($db_type==0)
		{
			  if($rID)
			  {
				  mysql_query("COMMIT");  
				  echo "0**".str_replace("'", '', $id);
			  }
			  else
			  {
				  mysql_query("ROLLBACK"); 
				  echo "10**";
			  }
		}
		if($db_type==1 || $db_type==2 )
		{
			if($rID)
			{
				  oci_commit($con);
				  echo "0**".str_replace("'", '', $id);
			}
			else
			{
				  oci_rollback($con);
				  echo "10**";
			}
		}
		
		disconnect($con);
		die;
	}
	
	if ($operation==1)  
	{		
		$con = connect();
		if($db_type==0)
		{
			  mysql_query("BEGIN");
		}
		 
		$id=return_next_id( "id", "tna_manual_permission", 1 ) ;
		$field_array="id, company_id,task_id,is_plan_manual, is_actual_manual";
		$data_array=array();
		foreach($data_string_arr as $data_string){
			list($task_id,$plan_status,$actual_status)=explode('_',$data_string);
			$data_array[]="(".$id.",".$cbo_company.",".$task_id.",".$plan_status.",".$actual_status.")";
			$id++;
		}
		
		$rID2=execute_query("DELETE FROM tna_manual_permission WHERE company_id=$cbo_company");
		$rID=sql_insert("tna_manual_permission",$field_array,implode(',',$data_array),1);
		
			
		if($db_type==0)
		{
			  if($rID && $rID2)
			  {
				  mysql_query("COMMIT");  
				  echo "1**".str_replace("'", '', $id);
			  }
			  else
			  {
				  mysql_query("ROLLBACK"); 
				  echo "10**";
			  }
		}
		if($db_type==1 || $db_type==2 )
		{
			if($rID && $rID2)
			{
				  oci_commit($con);
				  echo "1**".str_replace("'", '', $id);
			}
			else
			{
				  oci_rollback($con);
				  echo "10**";
			}
		}
		
		disconnect($con);
		die;
	}
	
	
	
	
}












?>

