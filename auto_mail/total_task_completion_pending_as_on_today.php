<?php

date_default_timezone_set("Asia/Dhaka");
// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require_once('setting/mail_setting.php');

$file = 'mail_log.txt';
$current = file_get_contents($file);
$current .= "TNA-PROCESS-Mail :: Date & Time: ".date("d-m-Y H:i:s",time())."\n";
file_put_contents($file, $current);




$company_library=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
$dealing_marchant = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");

$marchant_contact_no = return_library_array("select id,member_contact_no from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","member_contact_no");

$task_short_name = return_library_array("select task_name,task_short_name from lib_tna_task where status_active=1 and is_deleted=0","task_name","task_short_name");


$user_email_arr = return_library_array("select id,user_email from user_passwd where valid=1 ","id","user_email");
$user_buyer_arr = return_library_array("select id,buyer_id from user_passwd where valid=1","id","buyer_id");
$user_company_arr = return_library_array("select id,unit_id from user_passwd where valid=1","id","unit_id");


$mail_type_wise_data=array();
$res=sql_select("select id,user_id,tna_task_id,mail_type from tna_mail_setup where status_active=1 and is_deleted=0");
foreach($res as $row)
{
	foreach(explode(',',$row[csf('mail_type')]) as $type_id){
		foreach(explode(',',$row[csf('user_id')]) as $user_id){
			foreach(explode(',',$row[csf('tna_task_id')]) as $task_id){
				$mail_type_wise_data[$type_id][$user_id][$task_id]=$task_id;
				$task_arr[$task_id]=$task_id;
			}
		}
	}
}

$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),1))),'','',1);
$day_after_tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),2))),'','',1);
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),0))),'','',1);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 
$actual_date="is null";
 

$comments_sql="select job_id,order_id,task_id,comments from tna_progress_comments where is_deleted=0 and status_active=1";
$comments_sql_result=sql_select($comments_sql);
foreach($comments_sql_result as $rows)
{
	$commentsArr[$rows[csf('job_id')]][$rows[csf('order_id')]][$rows[csf('task_id')]]=$rows[csf('comments')];
}
	




foreach($company_library as $compid=>$company_name)
{
	//print_r($mail_type_wise_data[1]);
	foreach($mail_type_wise_data[1] as $user_id => $task_id){
		$companyArr=explode(',',$user_company_arr[$user_id]);
		if(in_array($compid,$companyArr)){
			$task_id_string = implode(',',$task_id);
			$user_company=str_replace('undefined',0,$user_company_arr[$user_id]);
			//$user_company_con=" and b.company_name in(".$user_company.")";
			$user_buyer=str_replace('undefined',0,$user_buyer_arr[$user_id]);
			//$user_buyer_con=" and b.buyer_name in(".$user_buyer.")";
			
			if($user_buyer_arr[$user_id]){$user_buyer_con=" and b.buyer_name in(".$user_buyer.")";}
			else{$user_buyer_con="";}
			if($user_company_arr[$user_id]){$user_company_con=" and b.company_name in(".$user_company.")";}
			else{$user_company_con="";}

			
	$TaskStartingReminderArr=array();		
	$sql_dtls="select b.dealing_marchant,a.id,a.task_number,b.buyer_name,a.job_no,a.po_number_id,a.shipment_date,a.task_start_date,b.style_ref_no,d.po_number,(d.po_quantity*b.total_set_qnty) as po_qty_pcs from tna_process_mst a, wo_po_details_master b, lib_tna_task c, wo_po_break_down d where a.job_no=b.job_no and a.po_number_id=d.id  and c.task_name=a.task_number and b.job_no=d.job_no_mst and a.task_number in(".$task_id_string.") and a.task_start_date between '".$current_date."' and '".$day_after_tomorrow."' and a.actual_start_date $actual_date   and b.company_name=$compid $user_company_con $user_buyer_con and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.status_active!=3 and d.shiping_status !=3 and d.is_confirmed=1 order by c.task_sequence_no";
	$nameArray_dtls=sql_select($sql_dtls);
	foreach($nameArray_dtls as $rows)
	{
		$TaskStartingReminderArr[$rows[csf('dealing_marchant')]][]=$rows;
	}
				
			
			
			ob_start();
			$flag=0;
			?>
			<table width="1000">
				<tr>
					<td height="20" align="left" valign="top">Dear Sir,</td>
				</tr>
				<tr>
					<td align="left">As per TNA schedule, all the pending tasks to complete have been mentioned below.</td>
				</tr>
				<tr>
					<td valign="top" align="left">
						<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
							<tr>
								<td colspan="10" height="40"><span><? echo $company_name; ?></span><br /><strong>Total Task Completion Pending As On Today</strong></td>
							</tr>
							<?
							$flag=0;
							foreach($TotalTaskCompletionPendingAsOnTodayArr as $dealing_marchant_id=>$dealing_marchant_row)
							{
								$j++;
								?>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Dealing Merchant :</strong></td>
									<td colspan="4">&nbsp;&nbsp;&nbsp;<strong><? echo $dealing_marchant[$dealing_marchant_id]; ?></strong></td>
									<td>Mobile :</td>
									<td colspan="3">&nbsp;&nbsp;&nbsp;<strong><? echo $marchant_contact_no[$dealing_marchant_id]; ?></strong></td>
								</tr>
								<tr>
									<td width="30"><strong>SL</strong></td>
									<td width="130"><strong>Task Name</strong></td>
									<td width="100"><strong>Buyer</strong></td>
									<td width="100"><strong>Job No</strong></td>
									<td width="100"><strong>Style Ref.</strong></td>
									<td width="100"><strong>Order</strong></td>
									<td width="100"><strong>Order Qty (Pcs)</strong></td>
									<td width="100"><strong>Ship Date</strong></td>

									<td width="80"><strong>Plan Finish Date</strong></td>
									<td><strong>Over Due Days</strong></td>
								</tr>
								<?
								$i=0;
								foreach($dealing_marchant_row as $row_dtls)
								{
									if($task_short_name[$row_dtls[csf('task_number')]]){
										$i++;

										$current_date1 = strtotime("$current_date");

										$task_finish_date = strtotime($row_dtls[csf('task_finish_date')]);
										$dateDiff = $current_date1-$task_finish_date;
										$fullDays = floor($dateDiff/(60*60*24));
										?>
										<tr>
											<td><? echo $i; ?></td>
											<td><? echo $task_short_name[$row_dtls[csf('task_number')]]; ?></td>
											<td><? echo $buyer_library[$row_dtls[csf('buyer_name')]]; ?></td>
											<td><? echo $row_dtls[csf('job_no')]; ?></td>
											<td><? echo $row_dtls[csf('style_ref_no')]; ?></td>
											<td><? echo $row_dtls[csf('po_number')]; ?></td> 
											<td align="right"><? echo $row_dtls[csf('po_qty_pcs')]; ?></td> 
											<td><? echo change_date_format($row_dtls[csf('shipment_date')]); ?></td>

											<td><? echo change_date_format($row_dtls[csf('task_finish_date')]); ?></td>
											<td align="center"><? echo $fullDays; ?></td>
										</tr>
										<?  $flag=1;  
									}
								}

							}
							?>
						</table>
					</td>
				</tr>
			</table>
			<?
			$to="";$message="";
			$subject=" Task completion pending as on today as per TNA schedule";

			$message="";
			$message=ob_get_contents();
			ob_clean();
			$header=mail_header();
			$to=$user_email_arr[$user_id];
			//if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message, $from_mail);}
			//echo $message;
			if($_REQUEST['isview']==1){
				echo $message;
			}
			else{
				if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message, $from_mail);}
			}
	
		}
	}


} // End Company

?> 