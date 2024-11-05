<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];
include('../../includes/common.php');


if ($action=="load_team_member")
{
	echo create_drop_down( "cbo_team_member_id", 150, "select id,team_member_name from team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();	
}



if ($action=="report_generate")
{
	
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_team_leader_id=str_replace("'","",$cbo_team_leader_id);
	$cbo_team_member_id=str_replace("'","",$cbo_team_member_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	$team_member_arr=return_library_array( "select id,team_member_name from team_member_info where status_active =1 and is_deleted=0 order by team_member_name",'id','team_member_name');

	
	if($txt_date_from !='' && $txt_date_to !=''){
		if($db_type==0)
		{
			$txt_date_from = date("Y-m-d",strtotime($txt_date_from));
			$txt_date_to = date("Y-m-d",strtotime($txt_date_to));
		}
		else
		{
			$txt_date_from = date("d-M-Y",strtotime($txt_date_from));
			$txt_date_to = date("d-M-Y",strtotime($txt_date_to));
		}
		$where_con .=" and task_date between '$txt_date_from' and '$txt_date_to'";
	}
	
	if($cbo_company_id !=0){
		$where_con .=" and company_id = '$cbo_company_id'";
	}
	if($cbo_team_leader_id !=0){
		$where_con .=" and team_leader_id = '$cbo_team_leader_id'";
	}
	if($cbo_team_member_id !=0){
		$where_con .=" and team_member_id = '$cbo_team_member_id'";
	}
	

    $sql= "select ID,ISSUE_NUM,ACTIVITY_DETIALS,MINUTES,MINUTES_TYPE,COMMENTS,TASK_DATE,USER_ID,COMPANY_ID,TEAM_LEADER_ID,TEAM_MEMBER_ID ,VARIFIED_MINUTES,RE_COMMENTS FROM DAILY_TASK_MST where status_active=1 and is_deleted=0  $where_con";
	   //echo $sql;
	$result = sql_select($sql);
	$width=1000;
	?>
	<div style="text-align:left; width:<?= $width+30; ?>px; margin:5px auto;">
    <table width="<?= $width; ?>" class="rpt_table" id="rpt_tablelist_view" rules="all" cellspacing="2" cellpadding="2" border="0" align="left">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">Team Member</th>
                <th width="70">Task Date</th>
                <th width="120">Task ID</th>
                <th width="300">Activity Details</th>
                <th width="60">Minutes Type</th>
                <th width="80">Minutes</th>
                <th width="80">Varified Minutes</th>
                <th>Re-Comments</th>
            </tr>
        </thead>
	</table>
	<div style="overflow-y:scroll; max-height:360px; width:<?= $width+20; ?>px; float:left;" id="scroll_body">
    <table width="<?= $width; ?>" class="rpt_table" id="rpt_tablelist_view" rules="all" cellspacing="2" cellpadding="2" border="0" align="left">
		<tbody>
			<?
			$minutes_type_arr=array(1=>"Idle",2=>"Active");
			$i=0;
			foreach($result as $row )
			{
				$i++;
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$total_minute+=$row[MINUTES];
				$total_varified_minute+=$row[VARIFIED_MINUTES];
				
				
            ?>
            <tr style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="100" ><p><? echo $team_member_arr[$row[TEAM_MEMBER_ID]]; ?></p></td>
                <td width="70" align="center"><p><? echo change_date_format($row[TASK_DATE]); ?></p></td>
                <td width="120" align="center"  ><? echo $row[ISSUE_NUM]; ?></td>
                <td width="300" align="center"><p><?php echo $row[ACTIVITY_DETIALS]; ?></p></td>
                <td width="60" align="center" ><? echo $minutes_type_arr[$row[MINUTES_TYPE]]; ?></td>
                <td width="80" align="center"><?php echo $row[MINUTES]; ?></td>
                <td width="80" align="center" >
                	<input type="hidden" name="update_id[]" id="update_id[]" value="<?= $row[ID];?>" readonly />
                    <input type="text" name="txt_minutes[]" id="txt_minutes[]" class="text_boxes_numeric" placeholder="Minutes" value="<?php echo $row[VARIFIED_MINUTES]; ?>" style="width:65px;" onKeyUp="calculate_minute()" />
                </td>
                <td align="center" >
                	<input type="text" name="txt_re_comments[]" id="txt_re_comments[]" class="text_boxes" value="<?php echo $row[RE_COMMENTS]; ?>" style="width:90%" />
                </td>
                
            </tr>
            <?
			}
			?>
        </tbody>
	</table>
    </div>
	
    <table width="<?= $width; ?>" class="rpt_table" rules="all" cellspacing="2" cellpadding="2" border="0" align="left">
        <tfoot>
            <tr>
                <th width="30"></td>
                <th width="100"></th>
                <th width="70"></th>
                <th width="120"></th>
                <th width="300"></th>
                <th width="60"></th>
                <th width="80"><?= $total_minute;?></th>
                <th width="80" id="td_varified_minutes"><?= $total_varified_minute;?></th>
                <th></th>
            </tr>
        </tfoot>
	</table>
    
    <table width="<?= $width; ?>">
         <tr>
            <td colspan="9" align="center">
                <? echo load_submit_buttons( $permission, "fn_daily_activities", 1,0,"alert();",1);?>
            </td>
        </tr>
    </table>

    </div>
	<?
	exit();
}




//-------------------------------

if ($action == "save_update_delete")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	
	if ($operation == 0)//save
	{}
	else if ($operation == 1)//update
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$update_id_str=str_replace("'","", $update_id_str);
		$minutes_str=str_replace("'","", $minutes_str);
		$re_comments_str=str_replace("'","", $re_comments_str);
		
		$update_id_arr=explode('__',$update_id_str);
		$minutes_arr=explode('__',$minutes_str);
		$re_comments_arr=explode('__',$re_comments_str);
		for($i=0;$i<count($update_id_arr);$i++){
			$id_arr[] = $update_id_arr[$i];
			$data_array_dtls_update[$update_id_arr[$i]] = explode("*",(($minutes_arr[$i]*1)."*'".$re_comments_arr[$i]."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}

		$field_array_dtls_update = "varified_minutes*re_comments*updated_by*update_date";
		$up_sql=bulk_update_sql_statement("daily_task_mst", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr);
		$rID = execute_query($up_sql);
		//echo "10**".$up_sql;die;		
			
		if ($db_type == 0)//mysql
		{
			if ($rID)
			{
				mysql_query("COMMIT");
				echo "1**";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";

			}
		}
		else if ($db_type == 2 || $db_type == 1)//oci
		{
			if ($rID)
			{
				oci_commit($con);
				echo "1**";
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