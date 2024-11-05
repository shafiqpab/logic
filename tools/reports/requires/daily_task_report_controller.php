<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );


if ($action=="report_generate")  // Item Description wise Search
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_user_id=str_replace("'","",$cbo_user_id);
	$txt_issue_no=str_replace("'","",$txt_issue_no);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

		
		if($db_type==2)
		{
			if( $date_from && $date_to){$wherCon.= " and TASK_DATE between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";}
		}
		else
		{
			if( $date_from && $date_to){$wherCon.= " and  TASK_DATE between '".$date_from."' and '".$date_to."'";}
	
		}
	
		if($txt_issue_no!=''){$wherCon.=" and ISSUE_NUMBER_PREFIX_NUM like '%$txt_issue_no%'";}
		if($cbo_user_id!=0){$wherCon.=" and user_id=$cbo_user_id";}
		

	
		$sql="select ID,USER_ID,ISSUE_NUM,TASK_DATE,ACTIVITY_DETIALS,MINUTES_TYPE,COMMENTS,MINUTES from DAILY_TASK_MST where IS_DELETED=0 and STATUS_ACTIVE=1 $wherCon order by TASK_DATE";
		$sql_result=sql_select( $sql );
		$dataArr=array();
		foreach($sql_result as $row)
		{
			$dataArr[$row[USER_ID]][$row[TASK_DATE]][]=$row;
		
		}
	
//echo $sql;
	ob_start();	
	?>
	     <div style="width:920px;">
         <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" align="left" >
            <thead>
                <th width="35">SL</th>
                <th width="80">Task Date</th>
                <th width="100">Issue No</th>
                <th width="250">Activity Details</th>
                <th width="80">Minutes</th>
                <th width="80">Minutes Type</th>
                <th>Comments</th>

            </thead>
            </table>
	     <div style="width:920px; overflow-y:scroll; float:left; max-height:350px; font-size:12px; overflow-x:hidden;" id="scroll_body">
		     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table"  id="table_body" align="left" >
					<? $i=1;$s=1;
					$minutTypeArr=array(1=>'Idle',2=>'Active');
					foreach($dataArr as $userId=>$userDataArr)
					{
						echo "<tr bgcolor='#CCC'><td colspan='7'><b>User Name:</b>".$user_arr[$userId]."</td></tr>";
						
						foreach($userDataArr as $task_date=>$darRows)
						{
							
							$totalMinute=0;
							$totalActiveMinute=0;
							$totalIdleMinute=0;
							
							?>
							
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td valign="middle" align="center" rowspan='<?= count($darRows);?>' width='35'><?= $s;?></td>
								<td width='80' valign="middle" align="center" rowspan='<?= count($darRows);?>'><?= change_date_format($task_date);?></td>
                            <?
							$falg=0;
							foreach($darRows as $rows)
							{
							 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							 
							 if($falg==1){?>
								 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trr_<? echo $i; ?>">
								<? }
							 ?>
							
							   <td width="100" align="center"><p><?= $rows[ISSUE_NUM]; ?></p></td>
							   <td width="250"><p><?= $rows[ACTIVITY_DETIALS]; ?></p></td>
							   <td width="80" align="center"><p><?= $rows[MINUTES]; ?></p></td>
							   <td width="80" align="center"><p><?= $minutTypeArr[$rows[MINUTES_TYPE]]; ?></p></td>
							   <td><p><?= $rows[COMMENTS]; ?></p></td>
							</tr>
							<?
							$i++;$falg=1;
							
								$totalMinute+=$rows[MINUTES];
								
								if($rows[MINUTES_TYPE]==2){$totalActiveMinute+=$rows[MINUTES];}
								if($rows[MINUTES_TYPE]==1){$totalIdleMinute+=$rows[MINUTES];}
								
							}
							
							$s++;
							
							echo "<tr bgcolor='#AAC'>
								<td align='right' colspan='4'><b>Total Active Time:</b></td>
								<td align='center'>".$totalActiveMinute."</td>
								<td></td>
								<td></td>
							</tr>";
							echo "<tr bgcolor='#CAA'>
								<td align='right' colspan='4'><b>Total Idle Time:</b></td>
								<td align='center'>".$totalIdleMinute."</td>
								<td></td>
								<td></td>
							</tr>";
							echo "<tr bgcolor='#CFF'>
								<td align='right' colspan='4'><b>Total use Time:</b></td>
								<td align='center'>".$totalMinute."</td>
								<td></td>
								<td></td>
							</tr>";
							
							$activeTime=($totalMinute/480)*100;
							
							if($activeTime<70){$bcolor="#FFFF00";}else{$bcolor="#3A7C1A";}
							if($activeTime<70){$fcolor="#FF0000";}else{$fcolor="#FFF";}
							
							
							
							echo "<tr bgcolor='".$bcolor."'>
								<td align='right' colspan='4'><b>Standard Total work Time:</b></td>
								<td style='color:".$fcolor."' align='center' title='".number_format($activeTime,2)."'><b>480</b></td>
								<td></td>
								<td></td>
							</tr>";
							
						}
						
					}
					?>
			        
		        </table>
	    </div>
	    </div>
    
		<?
		
		$html=ob_get_contents();
		ob_clean();

		foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$name.".xls";
		$create_new_doc = fopen($filename, 'w');	
		$is_created = fwrite($create_new_doc,$html);
		echo $html."**".$filename;
		exit();	


}

?>