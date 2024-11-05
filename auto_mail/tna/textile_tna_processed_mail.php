<?php

date_default_timezone_set("Asia/Dhaka");
//include('../mailer/class.phpmailer.php');
include('../../includes/common.php');
include('../setting/mail_setting.php');


$file = 'mail_log.txt';
$current = file_get_contents($file);
$current .= "TNA-PROCESS-Mail :: Date & Time: ".date("d-m-Y H:i:s",time())."\n";
file_put_contents($file, $current);




$company_library=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
// $dealing_marchant = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
// $marchant_contact_no = return_library_array("select id,member_contact_no from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","member_contact_no");

$task_short_name = return_library_array("select task_name,task_short_name from lib_tna_task where status_active=1 and is_deleted=0 and task_type=2","task_name","task_short_name");


$mktTeamSqlRes=sql_select("select ID,TEAM_MEMBER_NAME,MEMBER_CONTACT_NO from lib_mkt_team_member_info where status_active=1 and is_deleted=0");
foreach($mktTeamSqlRes as $row)
{
	$dealing_marchant[$row['ID']] = $row['TEAM_MEMBER_NAME'];
	$marchant_contact_no[$row['ID']] = $row['MEMBER_CONTACT_NO'];
}

$userSqlRes=sql_select("select ID,USER_EMAIL,BUYER_ID,UNIT_ID from user_passwd where valid=1");
foreach($userSqlRes as $row)
{
	$user_email_arr[$row['ID']] = $row['USER_EMAIL'];
	$user_buyer_arr[$row['ID']] = $row['BUYER_ID'];
	$user_company_arr[$row['ID']] = $row['UNIT_ID'];
}


// $user_email_arr = return_library_array("select id,user_email from user_passwd where valid=1 ","id","user_email");
// $user_buyer_arr = return_library_array("select id,buyer_id from user_passwd where valid=1","id","buyer_id");
// $user_company_arr = return_library_array("select id,unit_id from user_passwd where valid=1","id","unit_id");


$mail_type_wise_data=array();
$res=sql_select("select id,user_id,tna_task_id,mail_type from tna_mail_setup where status_active=1 and is_deleted=0 AND TASK_TYPE=2");
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




if($db_type==0)
{
	$tomorrow = date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d",time()),1)));
	$day_after_tomorrow = date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d",time()),2)));
	$current_date = date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d",time()),0)));
	$prev_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
	$actual_date="='0000-00-00'";
}
else
{
	$tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d",time()),1))),'','',1);
	$day_after_tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d",time()),2))),'','',1);
	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d",time()),0))),'','',1);
	$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 
	$actual_date="is null";
}


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
		if($user_company_arr[$user_id] == ''){$user_company_arr[$user_id] = implode(',',array_keys($company_library)); }
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
	$sql_dtls="select b.dealing_marchant,a.id,a.task_number,b.buyer_name,a.job_no,a.po_number_id,a.shipment_date,a.task_start_date,b.style_ref_no,d.po_number,d.GROUPING,(d.po_quantity*b.total_set_qnty) as po_qty_pcs from tna_process_mst a, wo_po_details_master b, lib_tna_task c, wo_po_break_down d where a.job_no=b.job_no and a.po_number_id=d.id  and c.task_name=a.task_number and b.job_no=d.job_no_mst and a.task_number in(".$task_id_string.") and a.task_start_date between '".$current_date."' and '".$day_after_tomorrow."' and a.actual_start_date $actual_date   and b.company_name=$compid $user_company_con $user_buyer_con and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.status_active!=3 and d.shiping_status !=3 and d.is_confirmed=1 order by c.task_sequence_no";
	$nameArray_dtls=sql_select($sql_dtls);
	foreach($nameArray_dtls as $rows)
	{
		$TaskStartingReminderArr[$rows[csf('dealing_marchant')]][]=$rows;
	}
				

			ob_start();
			$flag=0;
			?>
			<table width="1020">
				<tr>
					<td height="20" valign="top" align="left">Dear Sir,</td>
				</tr>
				<tr>
					<td align="left">As per TNA Schedule, following task(s) is/are supposed to start. Please check everything is ready or not.</td>
				</tr>

				<tr>
					<td valign="top" align="left">
						<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
							<tr>
								<td colspan="12" height="40"><span><? echo $company_name; ?></span><br /><strong>Task Starting Reminder</strong></td>
							</tr>
							<?  
							$flag=0;
							foreach($TaskStartingReminderArr as $dealing_marchant_id=>$dealing_marchant_row)
							{ 
								$j++;
								?>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Dealing Merchant :</strong></td>
									<td colspan="5">&nbsp;&nbsp;&nbsp;<strong><? echo $dealing_marchant[$dealing_marchant_id]; ?></strong></td>
									<td>Mobile :</td>
									<td colspan="4">&nbsp;&nbsp;&nbsp;<strong><? echo $marchant_contact_no[$dealing_marchant_id]; ?></strong></td>
								</tr>
								<tr bgcolor="#DDDDDD">
									<td width="34"><strong>SL</strong></td>
									<td width="130"><strong>Task Name</strong></td>
									<td width="115"><strong>Buyer</strong></td>
									<td width="100"><strong>Job No</strong></td>
									<td width="141"><strong>Style Ref.</strong></td>
									<td width="100"><strong>Internal Ref</strong></td>
									<td width="100"><strong>Order</strong></td>
                                    <td width="100"><strong>Order Qty (Pcs)</strong></td>
									<td width="80"><strong>Ship Date</strong></td>
									<td width="93"><strong><? echo change_date_format($current_date); ?></strong></td>
									<td width="86"><strong><? echo change_date_format($tomorrow); ?></strong></td>
									<td width="90"><strong><? echo change_date_format($day_after_tomorrow); ?></strong></td>
								</tr>
								<?
								$i=0;
								foreach($dealing_marchant_row as $row_dtls)
								{
									$i++;
									?>
									<tr>
										<td><? echo $i; ?></td>
										<td><? echo $task_short_name[$row_dtls[csf('task_number')]]; ?></td>
										<td><? echo $buyer_library[$row_dtls[csf('buyer_name')]]; ?></td>
										<td><? echo $row_dtls[csf('job_no')]; ?></td>
										<td><? echo $row_dtls[csf('style_ref_no')]; ?></td>
										<td><? echo $row_dtls['GROUPING']; ?></td>
										<td><? echo $row_dtls[csf('po_number')]; ?></td> 
                                        <td align="right"><? echo $row_dtls[csf('po_qty_pcs')]; ?></td> 
										<td><? echo change_date_format($row_dtls[csf('shipment_date')]); ?></td>
										<td>
											<?
											if(change_date_format($current_date)==change_date_format($row_dtls[csf('task_start_date')]))  
												echo change_date_format($row_dtls[csf('task_start_date')]); 
											?>
										</td>
										<td>
											<?
											if(change_date_format($tomorrow)==change_date_format($row_dtls[csf('task_start_date')]))  
												echo change_date_format($row_dtls[csf('task_start_date')]); 
											?>
										</td>
										<td>
											<?
											if(change_date_format($day_after_tomorrow)==change_date_format($row_dtls[csf('task_start_date')]))  
												echo change_date_format($row_dtls[csf('task_start_date')]); 
											?>
										</td>
									</tr>
									<? $flag=1;
								}

							}
							?>
						</table>
					</td>
				</tr>
			</table>
			<?

			$message=ob_get_contents();
			ob_clean();


			$to="";
			$subject="Task Starting Reminder as per TNA Schedule.";
			$to=$user_email_arr[$user_id];
			$header=mailHeader();
			//if($to!="" && $flag==1){echo sendMailMailer( $to, $subject, $message, $from_mail );}
			//echo $message;
			if($_REQUEST['isview']==1){
				if($to){
					echo $to;
				}else{
					echo "Mail address not set. [Please set mail from  TNA Mail Setup]<br>";
				}
				echo $message;
			}
			else{
				if($to!="" && $flag==1){echo sendMailMailer( $to, $subject, $message, $from_mail );}
			}
	
		}
	}
	
} // End Company

 


foreach($company_library as $compid=>$company_name)
{
	foreach($mail_type_wise_data[2] as $user_id => $task_id){
		if($user_company_arr[$user_id] == ''){$user_company_arr[$user_id] = implode(',',array_keys($company_library)); }
		$companyArr=explode(',',$user_company_arr[$user_id]);
		if(in_array($compid,$companyArr)){

			$task_id_string = implode(',',$task_id);
			$user_company=str_replace('undefined',0,$user_company_arr[$user_id]);
			//$user_company_con=" and b.company_name in(".$user_company.")";
			$user_buyer=str_replace('undefined',0,$user_buyer_arr[$user_id]);
			if($user_buyer_arr[$user_id]){$user_buyer_con=" and b.buyer_name in(".$user_buyer.")";}
			else{$user_buyer_con="";}
			if($user_company_arr[$user_id]){$user_company_con=" and b.company_name in(".$user_company.")";}
			else{$user_company_con="";}
			
	$sql_dtls="select b.dealing_marchant,a.id,a.task_number,b.buyer_name,a.job_no,a.po_number_id,a.shipment_date,a.task_finish_date,b.style_ref_no,d.po_number,d.GROUPING,(d.po_quantity*b.total_set_qnty) as po_qty_pcs from tna_process_mst a, wo_po_details_master b, lib_tna_task c, wo_po_break_down d where a.job_no=b.job_no and a.po_number_id=d.id  and c.task_name=a.task_number and b.job_no=d.job_no_mst  and a.task_number in(".$task_id_string.") and a.task_finish_date between '".$current_date."' and '".$day_after_tomorrow."' and b.company_name=$compid $user_company_con $user_buyer_con and a.actual_finish_date $actual_date and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.status_active !=3 and d.shiping_status !=3 and d.is_confirmed=1 order by c.task_sequence_no";
	$nameArray_dtls=sql_select($sql_dtls);
	$TaskComplitionReminderArr=array();
	foreach($nameArray_dtls as $rows)
	{
		$TaskComplitionReminderArr[$rows[csf('dealing_marchant')]][]=$rows;
	}
			
		
			
			
			ob_start();
			$flag=0;
			?> 

			<table width="1020">
				<tr>
					<td height="20" valign="top" align="left">Dear Sir,</td>
				</tr>
				<tr>
					<td align="left">As per TNA Schedule, following task(s) is/are supposed to complete. Please check work progress. You can take help of work progress report available in ERP (Report-> Merchandising report->Work progress report).</td>
				</tr>
				<tr>
					<td valign="top" align="left">
						<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
							<tr>
								<td colspan="12" height="40"><span><? echo $company_name; ?></span><br /><strong>Task Complition Reminder</strong></td>
							</tr>
							<?
							$flag=0;
							foreach($TaskComplitionReminderArr as $dealing_marchant_id=>$dealing_marchant_row)
							{
								$j++;
								?>
								<tr bgcolor="#CCCCCC"><td colspan="2"><strong>Dealing Merchant :</strong></td>
									<td colspan="5">&nbsp;&nbsp;&nbsp;<strong><? echo $dealing_marchant[$dealing_marchant_id]; ?></strong></td>
									<td>Mobile :</td>
									<td colspan="4">&nbsp;&nbsp;&nbsp;<strong><? echo $marchant_contact_no[$dealing_marchant_id]; ?></strong></td>

								</tr>
								<tr>
									<td width="30"><strong>SL</strong></td>
									<td width="130"><strong>Task Name</strong></td>
									<td width="130"><strong>Buyer</strong></td>
									<td width="100"><strong>Job No</strong></td>
									<td><strong>Style Ref.</strong></td>
									<td><strong>Internal Ref</strong></td>
									<td width="110"><strong>Order</strong></td>
									<td width="100"><strong>Order Qty (Pcs)</strong></td>
									<td width="80"><strong>Ship Date</strong></td>
									<td width="80"><strong><? echo change_date_format($current_date); ?></strong></td>
									<td width="80"><strong><? echo change_date_format($tomorrow); ?></strong></td>
									<td width="90"><strong><? echo change_date_format($day_after_tomorrow); ?></strong></td>
								</tr>
								<?
								$i=0;
								foreach($dealing_marchant_row as $row_dtls)
								{
									if($task_short_name[$row_dtls[csf('task_number')]]){
										$i++;
										?>
										<tr>
											<td><? echo $i; ?></td>
											<td><? echo $task_short_name[$row_dtls[csf('task_number')]]; ?></td>
											<td><? echo $buyer_library[$row_dtls[csf('buyer_name')]]; ?></td>
											<td><? echo $row_dtls[csf('job_no')]; ?></td>
											<td><? echo $row_dtls[csf('style_ref_no')]; ?></td>
											<td><? echo $row_dtls['GROUPING']; ?></td>
											<td><? echo $row_dtls[csf('po_number')]; ?></td> 
											<td align="right"><? echo $row_dtls[csf('po_qty_pcs')]; ?></td> 
											<td><?  echo change_date_format($row_dtls[csf('shipment_date')]); ?></td>
											<td>
												<?
												if(change_date_format($current_date)==change_date_format($row_dtls[csf('task_start_date')]))  
													echo change_date_format($row_dtls[csf('task_start_date')]); 
												?>
											</td>
											<td>
												<?
												if(change_date_format($tomorrow)==change_date_format($row_dtls[csf('task_start_date')]))  
													echo change_date_format($row_dtls[csf('task_start_date')]); 
												?>
											</td>
											<td>
												<?
												if(change_date_format($day_after_tomorrow)==change_date_format($row_dtls[csf('task_start_date')]))
													echo change_date_format($row_dtls[csf('task_start_date')]); 
												?>
											</td>
										</tr>
										<? $flag=1;
									}

								}

							}
							?>
						</table>
					</td>
				</tr>
			</table>
			<?
			$subject="-Task completion reminder as per TNA Schedule.";

	 
			$message=ob_get_contents();
			ob_clean();
			
			$to='';
			$to=$user_email_arr[$user_id];
			$header=mailHeader();
			//if($to!="" && $flag==1){echo sendMailMailer( $to, $subject, $message, $from_mail);}
			if($_REQUEST['isview']==1){
				if($to){
					echo $to;
				}else{
					echo "Mail address not set. [Please set mail from  TNA Mail Setup]<br>";
				}
				echo $message;
			}
			else{
				if($to!="" && $flag==1){echo sendMailMailer( $to, $subject, $message, $from_mail);}
			}
	
			//echo $message;
		}
	}
}//comapny end; 

 

foreach($company_library as $compid=>$company_name)
{
	foreach($mail_type_wise_data[3] as $user_id => $task_id){
		if($user_company_arr[$user_id] == ''){$user_company_arr[$user_id] = implode(',',array_keys($company_library)); }
		$companyArr=explode(',',$user_company_arr[$user_id]);
		if(in_array($compid,$companyArr)){

			$task_id_string = implode(',',$task_id);
			$user_company=str_replace('undefined',0,$user_company_arr[$user_id]);
			//$user_company_con=" and b.company_name in(".$user_company.")";
			$user_buyer=str_replace('undefined',0,$user_buyer_arr[$user_id]);
			if($user_buyer_arr[$user_id]){$user_buyer_con=" and b.buyer_name in(".$user_buyer.")";}
			else{$user_buyer_con="";}
			if($user_company_arr[$user_id]){$user_company_con=" and b.company_name in(".$user_company.")";}
			else{$user_company_con="";}
	
	$YesterdayStartPendingArr=array();
	$sql_dtls="select b.dealing_marchant,a.id,a.task_number,b.buyer_name,a.job_no,a.po_number_id,a.shipment_date,b.style_ref_no,d.po_number,d.GROUPING,(d.po_quantity*b.total_set_qnty) as po_qty_pcs from tna_process_mst a, wo_po_details_master b, lib_tna_task c, wo_po_break_down d where a.job_no=b.job_no and a.po_number_id=d.id  and c.task_name=a.task_number and b.job_no=d.job_no_mst and a.task_number in(".$task_id_string.") and a.task_start_date='".$prev_date."' and a.actual_start_date $actual_date and b.company_name=$compid $user_company_con $user_buyer_con and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.status_active !=3 and d.shiping_status !=3 and d.is_confirmed=1 order by c.task_sequence_no";
	$nameArray_dtls=sql_select($sql_dtls);
	foreach($nameArray_dtls as $rows)
	{
		$YesterdayStartPendingArr[$rows[csf('dealing_marchant')]][]=$rows;
	}
	


			ob_start();
			?> 

			<table width="1000">
				<tr>
					<td height="20" align="left" valign="top">Dear Sir,</td>
				</tr>
				<tr>
					<td align="left">As per TNA Schedule, following task(s) of &nbsp;(&nbsp;Date :&nbsp;<? echo change_date_format($prev_date); ?>&nbsp;) has/have got pending to start. Please find out the root cause of pending and take action to overcome.</td>
				</tr>
				<tr>
					<td valign="top" align="left">
						<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
							<tr>
								<td colspan="9" height="40"><span><? echo $company_name; ?></span><br />
									<strong>Yesterday  Start Pending Of &nbsp;(&nbsp;Date :&nbsp;<? echo change_date_format($prev_date); ?>&nbsp;)</strong></td>
								</tr>
								<? 
								
								$flag=0;
								foreach($YesterdayStartPendingArr as $dealing_marchant_id=>$dealing_marchant_row)
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
										<td width="110"><strong>Buyer</strong></td>
										<td width="100"><strong>Job No</strong></td>
										<td width="150"><strong>Style Ref.</strong></td>
										<td width="150"><strong>Internal Ref</strong></td>
										<td width="120"><strong>Order</strong></td>
                                        <td width="120"><strong>Order Qty (Pcs)</strong></td>
										<td width="80"><strong>Ship Date</strong></td>
										<td><strong>Comments</strong></td>
									</tr>
									<?
									$i=0;
									foreach($dealing_marchant_id as $row_dtls)
									{
										
										$tna_process_coments=$commentsArr[$row_dtls[csf('job_no')]][$row_dtls[csf('po_number_id')]][$row_dtls[csf('task_number')]];

										if($task_short_name[$row_dtls[csf('task_number')]]){
											$i++;
											?>
											<tr>
												<td><? echo $i; ?></td>
												<td><? echo $task_short_name[$row_dtls[csf('task_number')]]; ?></td>
												<td><? echo $buyer_library[$row_dtls[csf('buyer_name')]]; ?></td>
												<td><? echo $row_dtls[csf('job_no')]; ?></td>
												<td><? echo $row_dtls[csf('style_ref_no')]; ?></td>
												<td><? echo $row_dtls['GROUPING']; ?></td>
												<td><? echo $row_dtls[csf('po_number')]; ?></td>
                                                <td align="right"><? echo $row_dtls[csf('po_qty_pcs')]; ?></td> 
												<td><?  echo change_date_format($row_dtls[csf('shipment_date')]); ?></td>
												<td><? echo $tna_process_coments; ?></td>
											</tr>
											<? $flag=1;
										}
									}

								}
								?>
							</table>
						</td>
					</tr>
				</table>
				<?
				


				$message=ob_get_contents();
				ob_clean();

				$header=mailHeader();
				$to=$user_email_arr[$user_id];
				$subject="-Yesterday Start pending for ( Date :".change_date_format($prev_date).") as per TNA Schedule.";
				
				if($_REQUEST['isview']==1){
					if($to){
						echo $to;
					}else{
						echo "Mail address not set. [Please set mail from  TNA Mail Setup]<br>";
					}
					echo $message;
				}
				else{
					if($to!="" && $flag==1){echo sendMailMailer( $to, $subject, $message, $from_mail);}
				}
		
			}
		}

} // End Company

 


foreach($company_library as $compid=>$company_name)
{
	foreach($mail_type_wise_data[4] as $user_id => $task_id){
		if($user_company_arr[$user_id] == ''){$user_company_arr[$user_id] = implode(',',array_keys($company_library)); }
		$companyArr=explode(',',$user_company_arr[$user_id]);
		if(in_array($compid,$companyArr)){

			$task_id_string = implode(',',$task_id);
			$user_company=str_replace('undefined',0,$user_company_arr[$user_id]);
			//$user_company_con=" and b.company_name in(".$user_company.")";
			$user_buyer=str_replace('undefined',0,$user_buyer_arr[$user_id]);
			if($user_buyer_arr[$user_id]){$user_buyer_con=" and b.buyer_name in(".$user_buyer.")";}
			else{$user_buyer_con="";}
			if($user_company_arr[$user_id]){$user_company_con=" and b.company_name in(".$user_company.")";}
			else{$user_company_con="";}
			
			
	$sql_dtls="select b.dealing_marchant,a.id,a.task_number,b.buyer_name,a.job_no,a.po_number_id,a.shipment_date,b.style_ref_no,d.po_number,d.GROUPING,(d.po_quantity*b.total_set_qnty) as po_qty_pcs from tna_process_mst a, wo_po_details_master b, lib_tna_task c, wo_po_break_down d where a.job_no=b.job_no and a.po_number_id=d.id  and c.task_name=a.task_number and b.job_no=d.job_no_mst and a.task_number in(".$task_id_string.") and a.task_finish_date='".$prev_date."'  and a.actual_finish_date $actual_date and b.company_name=$compid $user_company_con $user_buyer_con and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and d.status_active !=3 and d.shiping_status !=3 and d.is_confirmed=1 order by c.task_sequence_no";
	$nameArray_dtls=sql_select($sql_dtls);
	$YesterdayCompletionPendingArr=array();
	foreach($nameArray_dtls as $rows)
	{
		$YesterdayCompletionPendingArr[$rows[csf('dealing_marchant')]][]=$rows;
	}
				
			
			
			ob_start();
			?> 

			<table width="1000">
				<tr>
					<td height="20" align="left" valign="top">Dear Sir,</td>
				</tr>
				<tr>
					<td align="left">As per TNA schedule, following task(s) of &nbsp;(&nbsp;Date :&nbsp;<? echo change_date_format($prev_date); ?>&nbsp;) has/have got pending to complete. Please find out the root cause of pending and take action to overcome.</td>
				</tr>
				<tr>
					<td valign="top" align="left">
						<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
							<tr>
								<td colspan="10" height="40"><strong><span><? echo $company_name; ?></span><br />
									Yesterday  Completion Pending Of &nbsp;(&nbsp;Date :&nbsp;<? echo change_date_format($prev_date); ?>&nbsp;)</strong></td>
								</tr>
								<?
								
								$flag=0;
								foreach($YesterdayCompletionPendingArr as $dealing_marchant_id=>$dealing_marchant_row)
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
										<td width="110"><strong>Task Name</strong></td>
										<td width="110"><strong>Buyer</strong></td>
										<td width="100"><strong>Job No</strong></td>
										<td width="120"><strong>Style Ref.</strong></td>
										<td width="120"><strong>Internal Ref</strong></td>
										<td width="100"><strong>Order</strong></td>
                                        <td width="100"><strong>Order Qty (PCS)</strong></td>
										<td width="80"><strong>Ship Date</strong></td>
										<td><strong>Comments</strong></td>
									</tr>
									<?
									$i=0;
									
									foreach($dealing_marchant_row as $row_dtls)
									{
										if($task_short_name[$row_dtls[csf('task_number')]]){

											$tna_process_coments=$commentsArr[$row_dtls[csf('job_no')]][$row_dtls[csf('po_number_id')]][$row_dtls[csf('task_number')]];											
											
											$i++;
											?>
											<tr>
												<td><? echo $i; ?></td>
												<td><? echo $task_short_name[$row_dtls[csf('task_number')]]; ?></td>
												<td><? echo $buyer_library[$row_dtls[csf('buyer_name')]]; ?></td>
												<td><? echo $row_dtls[csf('job_no')]; ?></td>
												<td><? echo $row_dtls[csf('style_ref_no')]; ?></td>
												<td><? echo $row_dtls['GROUPING']; ?></td>
												<td><? echo $row_dtls[csf('po_number')]; ?></td> 
                                                <td align="right"><? echo $row_dtls[csf('po_qty_pcs')]; ?></td>
												<td><?  echo change_date_format($row_dtls[csf('shipment_date')]); ?></td>
												<td><? echo $tna_process_coments; ?></td>
											</tr>
											<? $flag=1;
										}
									}

								}
								?>
							</table>
						</td>
					</tr>
				</table>
				<?
				
				$message=ob_get_contents();
				ob_clean();


				$header=mailHeader();
				$to=$user_email_arr[$user_id];
				$subject="-Yesterday completion pending for ( Date :".change_date_format($prev_date).")  as per TNA schedule.";

				if($_REQUEST['isview']==1){
					if($to){
						echo $to;
					}else{
						echo "Mail address not set. [Please set mail from  TNA Mail Setup]<br>";
					}
					echo $message;
				}
				else{
					if($to!="" && $flag==1){echo sendMailMailer( $to, $subject, $message, $from_mail);}
				}
		
			}
		}

} // End Company

 

foreach($company_library as $compid=>$company_name)
{
	foreach($mail_type_wise_data[5] as $user_id => $task_id){
		if($user_company_arr[$user_id] == ''){$user_company_arr[$user_id] = implode(',',array_keys($company_library)); }
		$companyArr=explode(',',$user_company_arr[$user_id]);
		if(in_array($compid,$companyArr)){


			$task_id_string = implode(',',$task_id);

			$user_company=str_replace('undefined',0,$user_company_arr[$user_id]);
			//$user_company_con=" and b.company_name in(".$user_company.")";
			$user_buyer=str_replace('undefined',0,$user_buyer_arr[$user_id]);
			if($user_buyer_arr[$user_id]){$user_buyer_con=" and b.buyer_name in(".$user_buyer.")";}
			else{$user_buyer_con="";}
			if($user_company_arr[$user_id]){$user_company_con=" and b.company_name in(".$user_company.")";}
			else{$user_company_con="";}

	$sql_dtls="select b.dealing_marchant,a.id,a.task_number,b.buyer_name,a.job_no,a.po_number_id,a.shipment_date,b.style_ref_no,a.task_start_date,d.po_number,d.GROUPING,(d.po_quantity*b.total_set_qnty) as po_qty_pcs from tna_process_mst a, wo_po_details_master b, lib_tna_task c, wo_po_break_down d where a.job_no=b.job_no  and a.po_number_id=d.id and c.task_name=a.task_number and b.job_no=d.job_no_mst and a.task_number in(".$task_id_string.") and a.task_start_date <='".$current_date."' and a.actual_start_date $actual_date and b.company_name=$compid $user_company_con $user_buyer_con and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.status_active=1 and d.shiping_status !=3 and d.is_confirmed=1 order by c.task_sequence_no";
	// echo $sql_dtls;die;
	$nameArray_dtls=sql_select($sql_dtls);
	$TotalTaskStartPendingAsOnTodayArr=array();
	foreach($nameArray_dtls as $rows)
	{
		$TotalTaskStartPendingAsOnTodayArr[$rows[csf('dealing_marchant')]][]=$rows;	
	}
	

			ob_start();
			?> 

			<table width="1000">
				<tr>
					<td height="20" align="left" valign="top">Dear Sir,</td>
				</tr>
				<tr>
					<td align="left">As per TNA schedule, all the pending tasks to start have been mentioned below</td>
				</tr>
				<tr>
					<td valign="top" align="left">
						<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
							<tr>
								<td colspan="12" height="40"><span><? echo $company_name; ?></span><br /><strong>Total Task Start Pending As On Today</strong></td>
							</tr>
							<?
							$flag=0;
							foreach($TotalTaskStartPendingAsOnTodayArr as $dealing_marchant_id=>$dealing_marchant_row)
							{ 
								$j++;
								?>
								<tr bgcolor="#CCCCCC">
									<td colspan="3"><strong>Dealing Merchant :</strong></td>
									<td colspan="4">&nbsp;&nbsp;&nbsp;<strong><? echo $dealing_marchant[$dealing_marchant_id]; ?></strong></td>
									<td>Mobile :</td>
									<td colspan="4">&nbsp;&nbsp;&nbsp;<strong><? echo $marchant_contact_no[$dealing_marchant_id]; ?></strong></td>
								</tr>
								<tr>
									<td width="30"><strong>SL</strong></td>
									<td width="130"><strong>Task Name</strong></td>
									<td width="100"><strong>Buyer</strong></td>
									<td width="100"><strong>Job No</strong></td>
									<td width="100"><strong>Style Ref.</strong></td>
									<td width="100"><strong>Internal Ref</strong></td>
									<td width="100"><strong>Order</strong></td>
                                    <td width="100"><strong>Order Qty (Pcs)</strong></td>
									<td width="100"><strong>Ship Date</strong></td>
									<td width="80"><strong>Plan Start Date</strong></td>
									<td width="50"><strong>Over Due Days</strong></td>
									<td><strong>comments</strong></td>
								</tr>
								<?
								$i=0;
								foreach($dealing_marchant_row as $row_dtls)
								{
									if($task_short_name[$row_dtls[csf('task_number')]]){
										$i++;
										$current_date1 = strtotime("$current_date");

										$task_start_date = strtotime($row_dtls[csf('task_start_date')]);
										$dateDiff = $current_date1-$task_start_date;
										$fullDays = floor($dateDiff/(60*60*24));
										$tna_process_coments=$commentsArr[$row_dtls[csf('job_no')]][$row_dtls[csf('po_number_id')]][$row_dtls[csf('task_number')]];											
										?>
										<tr>
											<td><? echo $i; ?></td>
											<td><? echo $task_short_name[$row_dtls[csf('task_number')]]; ?></td>
											<td><? echo $buyer_library[$row_dtls[csf('buyer_name')]]; ?></td>
											<td><? echo $row_dtls[csf('job_no')]; ?></td>
											<td><? echo $row_dtls[csf('style_ref_no')]; ?></td>
											<td><? echo $row_dtls['GROUPING']; ?></td>
											<td><? echo $row_dtls[csf('po_number')]; ?></td> 
                                            <td align="right"><? echo $row_dtls[csf('po_qty_pcs')]; ?></td> 
											<td><? echo change_date_format($row_dtls[csf('shipment_date')]); ?></td>
											<td><? echo change_date_format($row_dtls[csf('task_start_date')]); ?></td>
											<td align="center"><? echo $fullDays; ?></td>
											<td><? echo $tna_process_coments; ?></td>
										</tr>
										<?

										$flag=1;
									}

								}

							}
							?>
						</table>
					</td>
				</tr>
			</table>
			<?
		 

			$message=ob_get_contents();
			ob_clean();
 

			$to=$user_email_arr[$user_id];
			$subject="Task start pending as on today as per TNA schedule";
			$header=mailHeader();	


			
			if($_REQUEST['isview']==1){
				if($to){
					echo $to;
				}else{
					echo "Mail address not set. [Please set mail from  TNA Mail Setup]<br>";
				}
				echo  $message;
			}
			else{
				if($to!="" && $flag==1){echo sendMailMailer( $to, $subject, $message, $from_mail);}
			}
			
	
		}

	}
 
} // End Company

 

foreach($company_library as $compid=>$company_name)
{
	foreach($mail_type_wise_data[6] as $user_id => $task_id){
		if($user_company_arr[$user_id] == ''){$user_company_arr[$user_id] = implode(',',array_keys($company_library)); }
		$companyArr=explode(',',$user_company_arr[$user_id]);
		if(in_array($compid,$companyArr)){

			$task_id_string = implode(',',$task_id);
			$user_company=str_replace('undefined',0,$user_company_arr[$user_id]);
			//$user_company_con=" and b.company_name in(".$user_company.")";
			$user_buyer=str_replace('undefined',0,$user_buyer_arr[$user_id]);
			if($user_buyer_arr[$user_id]){$user_buyer_con=" and b.buyer_name in(".$user_buyer.")";}
			else{$user_buyer_con="";}
			if($user_company_arr[$user_id]){$user_company_con=" and b.company_name in(".$user_company.")";}
			else{$user_company_con="";}

	$TotalTaskCompletionPendingAsOnTodayArr=array();
	$sql_dtls="select b.dealing_marchant,a.id,a.task_number,b.buyer_name,a.job_no,a.po_number_id,a.shipment_date,b.style_ref_no,a.task_finish_date,d.po_number,d.GROUPING,(d.po_quantity*b.total_set_qnty) as po_qty_pcs from tna_process_mst a, wo_po_details_master b, lib_tna_task c, wo_po_break_down d where a.job_no=b.job_no  and a.po_number_id=d.id  and c.task_name=a.task_number and b.job_no=d.job_no_mst and a.task_number in(".$task_id_string.") and a.task_finish_date <='".$current_date."' and a.actual_finish_date $actual_date and b.company_name=$compid $user_company_con $user_buyer_con and a.is_deleted=0 and a.status_active=1 and d.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.shiping_status !=3 and d.status_active !=3  and d.is_confirmed=1 order by c.task_sequence_no";
	$nameArray_dtls=sql_select($sql_dtls);
	foreach($nameArray_dtls as $rows)
	{
		$TotalTaskCompletionPendingAsOnTodayArr[$rows[csf('dealing_marchant')]][]=$rows;
	}


			ob_start();
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
								<td colspan="11" height="40"><span><? echo $company_name; ?></span><br /><strong>Total Task Completion Pending As On Today</strong></td>
							</tr>
							<?
							$flag=0;
							foreach($TotalTaskCompletionPendingAsOnTodayArr as $dealing_marchant_id=>$dealing_marchant_row)
							{
								$j++;
								?>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Dealing Merchant :</strong></td>
									<td colspan="5">&nbsp;&nbsp;&nbsp;<strong><? echo $dealing_marchant[$dealing_marchant_id]; ?></strong></td>
									<td>Mobile :</td>
									<td colspan="3">&nbsp;&nbsp;&nbsp;<strong><? echo $marchant_contact_no[$dealing_marchant_id]; ?></strong></td>
								</tr>
								<tr>
									<td width="30"><strong>SL</strong></td>
									<td width="130"><strong>Task Name</strong></td>
									<td width="100"><strong>Buyer</strong></td>
									<td width="100"><strong>Job No</strong></td>
									<td width="100"><strong>Style Ref.</strong></td>
									<td width="100"><strong>Internal Ref</strong></td>
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
											<td><? echo $row_dtls['GROUPING']; ?></td>
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



			$message=ob_get_contents();
			ob_clean();

			$to="";
			$subject=" Task completion pending as on today as per TNA schedule";
			$header=mailHeader();
			$to=$user_email_arr[$user_id];
			if($_REQUEST['isview']==1){
				if($to){
					echo $to;
				}else{
					echo "Mail address not set. [Please set mail from  TNA Mail Setup]<br>";
				}
				echo $message;
			}
			else{
				if($to!="" && $flag==1){echo sendMailMailer( $to, $subject, $message, $from_mail);}
			}
	
			//echo $message;
		}
	}


} // End Company

?> 