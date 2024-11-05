<?php

    date_default_timezone_set("Asia/Dhaka");

	// require_once('../mailer/class.phpmailer.php');
	require_once('../includes/common.php');
	require_once('setting/mail_setting.php');
	

	$company_library=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	
	$dealing_marchant_sql=sql_select("select id,team_member_name,team_member_email,member_contact_no from lib_mkt_team_member_info where status_active=1 and is_deleted=0");
	foreach($dealing_marchant_sql as $row)
	{
		$marchant_contact_no[$row[csf('id')]]=$row[csf('member_contact_no')];
		$dealing_marchant[$row[csf('id')]]=$row[csf('team_member_name')];
		$dealing_marchant_mail[$row[csf('id')]]=$row[csf('team_member_email')];
	}
	
	$task_short_name = return_library_array("select task_name,task_short_name from lib_tna_task where status_active=1 and is_deleted=0","task_name","task_short_name");
	
	
	
	$coments_sql=sql_select("select job_id,order_id,task_id,comments from tna_progress_comments");
	foreach($coments_sql as $row)
	{
		$key=$row[csf('job_id')].$row[csf('order_id')].$row[csf('task_id')];
		$tna_process_coments_arr[$key]=$row[csf('comments')];
	}

	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	$tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),1))),'','',1);
	$day_after_tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),2))),'','',1);
	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),0))),'','',1);
	$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 
	$actual_date="is null";
	
	 
foreach($company_library as $compid=>$company_name){
//mail type=1 ....................................................................
				
		$dealing_marchant_data_arr=array();		
		$sql="select b.dealing_marchant,a.id,a.task_number,b.buyer_name,a.job_no,a.po_number_id,a.shipment_date,a.task_start_date,b.style_ref_no,d.po_number from tna_process_mst a, wo_po_details_master b, lib_tna_task c, wo_po_break_down d where a.job_no=b.job_no and a.po_number_id=d.id  and c.task_name=a.task_number and b.job_no=d.job_no_mst and a.task_start_date between '".$current_date."' and '".$day_after_tomorrow."' and a.actual_start_date $actual_date and b.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.status_active!=3 and d.shiping_status !=3 and d.is_confirmed=1 order by b.dealing_marchant,c.task_sequence_no";
		$dealing_marchant_data_result=sql_select($sql);
		foreach($dealing_marchant_data_result as $row)
		{
			$dealing_marchant_data_arr[$row[csf('dealing_marchant')]][]=$row;	
		}

		
		$flag=0;
		
		$flag=0;
		foreach($dealing_marchant_data_arr as $dealing_marchant_id=>$data_rows)
		{ 
			
		ob_start();
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
							<td colspan="10" height="40"><span><? echo $company_name; ?></span><br /><strong>Task Starting Reminder</strong></td>
						</tr>
							<tr><td height="30" valign="top" colspan="10">&nbsp;</td></tr>
							<tr bgcolor="#CCCCCC">
								<td colspan="2"><strong>Dealing Merchant :</strong></td>
								<td colspan="4">&nbsp;&nbsp;&nbsp;<strong><? echo $dealing_marchant[$dealing_marchant_id]; ?></strong></td>
								<td>Mobile :</td>
								<td colspan="3">&nbsp;&nbsp;&nbsp;<strong><? echo $marchant_contact_no[$dealing_marchant_id]; ?></strong></td>
							</tr>
							<tr bgcolor="#DDDDDD">
								<td width="34"><strong>SL</strong></td>
								<td width="130"><strong>Task Name</strong></td>
								<td width="115"><strong>Buyer</strong></td>
								<td width="100"><strong>Job No</strong></td>
								<td width="141"><strong>Style Ref.</strong></td>
								<td width="100"><strong>Order</strong></td>
								<td width="80"><strong>Ship Date</strong></td>
								<td width="93"><strong><? echo change_date_format($current_date); ?></strong></td>
								<td width="86"><strong><? echo change_date_format($tomorrow); ?></strong></td>
								<td width="90"><strong><? echo change_date_format($day_after_tomorrow); ?></strong></td>
							</tr>
							<?
							$i=0;

							foreach($data_rows as $row_dtls)
							{
								$i++;
								?>
								<tr>
									<td><? echo $i; ?></td>
									<td><? echo $task_short_name[$row_dtls[csf('task_number')]]; ?></td>
									<td><? echo $buyer_library[$row_dtls[csf('buyer_name')]]; ?></td>
									<td><? echo $row_dtls[csf('job_no')]; ?></td>
									<td><? echo $row_dtls[csf('style_ref_no')]; ?></td>
									<td><? echo $row_dtls[csf('po_number')]; ?></td> 
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
						?>
					</table>
				</td>
			</tr>
		</table>
		<?
		$to="";$message="";
		$subject="Task Starting Reminder as per TNA Schedule.";
		$message=ob_get_contents();
		ob_clean();
		$to=$dealing_marchant_mail[$dealing_marchant_id];
		$header=mail_header();
		//if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message, $from_mail );}
		//echo $message;
		if($_REQUEST['isview']==1){
			echo $message;
		}
		else{
		  if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message, $from_mail );}
	
		}
	
		}
	
}

foreach($company_library as $compid=>$company_name){
//mail type=2 ....................................................................
			
	$sql="select b.dealing_marchant,a.id,a.task_number,b.buyer_name,a.job_no,a.po_number_id,a.shipment_date,a.task_finish_date,b.style_ref_no,d.po_number from tna_process_mst a, wo_po_details_master b, lib_tna_task c, wo_po_break_down d where a.job_no=b.job_no and a.po_number_id=d.id  and c.task_name=a.task_number and b.job_no=d.job_no_mst  and a.task_finish_date between '".$current_date."' and '".$day_after_tomorrow."'  and b.company_name=$compid  and a.actual_finish_date $actual_date and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.status_active !=3 and d.shiping_status !=3 and d.is_confirmed=1 order by b.dealing_marchant,c.task_sequence_no";
	$sql_result=sql_select($sql);
	$dealing_marchant_data_arr=array();	
	foreach($sql_result as $row)
	{
		$dealing_marchant_data_arr[$row[csf('dealing_marchant')]][]=$row;	
	}
		
		
		$flag=0;
		
		$flag=0;
		foreach($dealing_marchant_data_arr as $dealing_marchant_id=>$data_rows)
		{
		ob_start();
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
								<td colspan="10" height="40"><span><? echo $company_name; ?></span><br /><strong>Task Complition Reminder</strong></td>
							</tr>
								<tr><td height="30" valign="top" colspan="10">&nbsp;</td></tr>
								<tr bgcolor="#CCCCCC"><td colspan="2"><strong>Dealing Merchant :</strong></td><td colspan="4">&nbsp;&nbsp;&nbsp;<strong><? echo $dealing_marchant[$dealing_marchant_id]; ?></strong></td>
									<td>Mobile :</td>
									<td colspan="3">&nbsp;&nbsp;&nbsp;<strong><? echo $marchant_contact_no[$dealing_marchant_id]; ?></strong></td>

								</tr>
								<tr>
									<td width="34"><strong>SL</strong></td>
									<td width="133"><strong>Task Name</strong></td>
									<td width="130"><strong>Buyer</strong></td>
									<td width="100"><strong>Job No</strong></td>
									<td width="115"><strong>Style Ref.</strong></td>
									<td width="115"><strong>Order</strong></td>
									<td width="80"><strong>Ship Date</strong></td>
									<td width="86"><strong><? echo change_date_format($current_date); ?></strong></td>
									<td width="86"><strong><? echo change_date_format($tomorrow); ?></strong></td>
									<td width="90"><strong><? echo change_date_format($day_after_tomorrow); ?></strong></td>
								</tr>
								<?
								$i=0;
								foreach($data_rows as $row_dtls)
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
											<td><? echo $row_dtls[csf('po_number')]; ?></td> 
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
							?>
						</table>
					</td>
				</tr>
			</table>
			<?
		$subject="Task completion reminder as per TNA Schedule.";

		$message="";
		$message=ob_get_contents();
		ob_clean();
		$to=$dealing_marchant_mail[$dealing_marchant_id];
		$header=mail_header();
		//if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message, $from_mail);}
		if($_REQUEST['isview']==1){
			echo $message;
		}
		else{
			if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message, $from_mail);}
	
		}
	
		//echo $message;
	}
} 


foreach($company_library as $compid=>$company_name){
//mail type=3 ....................................................................
	
	$sql="select b.dealing_marchant,a.id,a.task_number,b.buyer_name,a.job_no,a.po_number_id,a.shipment_date,b.style_ref_no,d.po_number from tna_process_mst a, wo_po_details_master b, lib_tna_task c, wo_po_break_down d where a.job_no=b.job_no and a.po_number_id=d.id  and c.task_name=a.task_number and b.job_no=d.job_no_mst  and a.task_start_date='".$prev_date."'  and a.actual_start_date $actual_date and b.company_name=$compid  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.status_active !=3 and d.shiping_status !=3 and d.is_confirmed=1 order by b.dealing_marchant,c.task_sequence_no";
	$sql_result=sql_select($sql);
	$dealing_marchant_data_arr=array();	
	foreach($sql_result as $row)
	{
		$dealing_marchant_data_arr[$row[csf('dealing_marchant')]][]=$row;	
	}
			
		
		$flag=0;
		
			$flag=0;
			foreach($dealing_marchant_data_arr as $dealing_marchant_id=>$data_rows)
			{
			ob_start();
		?> 

		<table width="976">
			<tr>
				<td width="968" height="20" align="left" valign="top">Dear Sir,</td>
			</tr>
			<tr>
				<td align="left">As per TNA Schedule, following task(s) of &nbsp;(&nbsp;Date :&nbsp;<? echo change_date_format($prev_date); ?>&nbsp;) has/have got pending to start. Please find out the root cause of pending and take action to overcome.</td>
			</tr>
			<tr>
				<td valign="top" align="left">
					<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tr>
							<td colspan="8" height="40"><span><? echo $company_name; ?></span><br />
								<strong>Yesterday  Start Pending Of &nbsp;(&nbsp;Date :&nbsp;<? echo change_date_format($prev_date); ?>&nbsp;)</strong></td>
							</tr>
								<tr><td height="30" valign="top" colspan="8">&nbsp;</td></tr>
								<tr bgcolor="#CCCCCC"><td colspan="2"><strong>Dealing Merchant :</strong></td>
									<td colspan="3">&nbsp;&nbsp;&nbsp;<strong><? echo $dealing_marchant[$dealing_marchant_id]; ?></strong></td>
									<td>Mobile :</td>
									<td colspan="2">&nbsp;&nbsp;&nbsp;<strong><? echo $marchant_contact_no[$dealing_marchant_id]; ?></strong></td>
								</tr>
								<tr>
									<td width="34"><strong>SL</strong></td>
									<td width="134"><strong>Task Name</strong></td>
									<td width="114"><strong>Buyer</strong></td>
									<td width="100"><strong>Job No</strong></td>
									<td width="156"><strong>Style Ref.</strong></td>
									<td width="123"><strong>Order</strong></td>
									<td width="80"><strong>Ship Date</strong></td>
									<td><strong>Comments</strong></td>
								</tr>
								<?
								$i=0;
								foreach($data_rows as $row_dtls)
								{

									$key=$row_dtls[csf('job_no')].$row_dtls[csf('po_number_id')].$row_dtls[csf('task_number')];
									$tna_process_coments==$tna_process_coments_arr[$key];
									
									

									if($task_short_name[$row_dtls[csf('task_number')]]){
										$i++;
										?>
										<tr>
											<td><? echo $i; ?></td>
											<td><? echo $task_short_name[$row_dtls[csf('task_number')]]; ?></td>
											<td><? echo $buyer_library[$row_dtls[csf('buyer_name')]]; ?></td>
											<td><? echo $row_dtls[csf('job_no')]; ?></td>
											<td><? echo $row_dtls[csf('style_ref_no')]; ?></td>
											<td><? echo $row_dtls[csf('po_number')]; ?></td> 
											<td><?  echo change_date_format($row_dtls[csf('shipment_date')]); ?></td>
											<td><? echo $tna_process_coments; ?></td>
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
		$subject="Yesterday Start pending for ( Date :".change_date_format($prev_date).") as per TNA Schedule.";

		$message="";
		$message=ob_get_contents();
		ob_clean();
		$header=mail_header();
		$to=$dealing_marchant_mail[$dealing_marchant_id];
		//if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message, $from_mail);}
		if($_REQUEST['isview']==1){
			echo $message;
		}
		else{
			if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message, $from_mail);}
	
		}
	
		//echo $message;
	}

} // End Company

foreach($company_library as $compid=>$company_name){
//mail type=4 ....................................................................

	$sql="select b.dealing_marchant,a.id,a.task_number,b.buyer_name,a.job_no,a.po_number_id,a.shipment_date,b.style_ref_no,d.po_number from tna_process_mst a, wo_po_details_master b, lib_tna_task c, wo_po_break_down d where a.job_no=b.job_no and a.po_number_id=d.id  and c.task_name=a.task_number and b.job_no=d.job_no_mst  and a.task_finish_date='".$prev_date."'  and a.actual_finish_date $actual_date and b.company_name=$compid  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and d.status_active !=3 and d.shiping_status !=3 and d.is_confirmed=1 order by b.dealing_marchant,c.task_sequence_no";
	$sql_result=sql_select($sql);
	$dealing_marchant_data_arr=array();	
	foreach($sql_result as $row)
	{
		$dealing_marchant_data_arr[$row[csf('dealing_marchant')]][]=$row;	
	}
			
			
			
			
			
			
			$flag=0;
			$flag=0;
			foreach($dealing_marchant_data_arr as $dealing_marchant_id=>$data_rows)
			{
				ob_start();
			?> 

			<table width="979">
				<tr>
					<td width="971" height="20" align="left" valign="top">Dear Sir,</td>
				</tr>
				<tr>
					<td align="left">As per TNA schedule, following task(s) of &nbsp;(&nbsp;Date :&nbsp;<? echo change_date_format($prev_date); ?>&nbsp;) has/have got pending to complete. Please find out the root cause of pending and take action to overcome.</td>
				</tr>
				<tr>
					<td valign="top" align="left">
						<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
							<tr>
								<td colspan="8" height="40"><strong><span><? echo $company_name; ?></span><br />
									Yesterday  Completion Pending Of &nbsp;(&nbsp;Date :&nbsp;<? echo change_date_format($prev_date); ?>&nbsp;)</strong></td>
								</tr>
									<tr><td height="30" valign="top" colspan="8">&nbsp;</td></tr>
									<tr bgcolor="#CCCCCC">
										<td colspan="2"><strong>Dealing Merchant :</strong></td>
										<td colspan="3">&nbsp;&nbsp;&nbsp;<strong><? echo $dealing_marchant[$dealing_marchant_id]; ?></strong></td>
										<td>Mobile :</td>
										<td colspan="2">&nbsp;&nbsp;&nbsp;<strong><? echo $marchant_contact_no[$dealing_marchant_id]; ?></strong></td> 
									</tr>
									<tr>
										<td width="34"><strong>SL</strong></td>
										<td width="146"><strong>Task Name</strong></td>
										<td width="122"><strong>Buyer</strong></td>
										<td width="100"><strong>Job No</strong></td>
										<td width="145"><strong>Style Ref.</strong></td>
										<td width="125"><strong>Order</strong></td>
										<td width="80"><strong>Ship Date</strong></td>
										<td><strong>Comments</strong></td>
									</tr>
									<?
									$i=0;
									foreach($data_rows as $row_dtls)
									{
										if($task_short_name[$row_dtls[csf('task_number')]]){

											//$tna_process_coments=return_field_value("comments"," tna_progress_comments"," job_id='{$row_dtls[csf('job_no')]}' and order_id={$row_dtls[csf('po_number_id')]} and task_id={$row_dtls[csf('task_number')]}");
											
										$key=$row_dtls[csf('job_no')].$row_dtls[csf('po_number_id')].$row_dtls[csf('task_number')];
										$tna_process_coments==$tna_process_coments_arr[$key];
											
											
											$i++;
											?>
											<tr>
												<td><? echo $i; ?></td>
												<td><? echo $task_short_name[$row_dtls[csf('task_number')]]; ?></td>
												<td><? echo $buyer_library[$row_dtls[csf('buyer_name')]]; ?></td>
												<td><? echo $row_dtls[csf('job_no')]; ?></td>
												<td><? echo $row_dtls[csf('style_ref_no')]; ?></td>
												<td><? echo $row_dtls[csf('po_number')]; ?></td> 
												<td><?  echo change_date_format($row_dtls[csf('shipment_date')]); ?></td>
												<td><? echo $tna_process_coments; ?></td>
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

				

		$subject="Yesterday completion pending for ( Date :".change_date_format($prev_date).")  as per TNA schedule.";

		$message="";
		$message=ob_get_contents();
		ob_clean();
		$header=mail_header();
		$to=$dealing_marchant_mail[$dealing_marchant_id];
		//if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message, $from_mail);}
		if($_REQUEST['isview']==1){
			echo $message;
		}
		else{
			if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message, $from_mail);}
	
		}
	
	//echo $message;
	}
} // End Company


foreach($company_library as $compid=>$company_name){
//mail type=5 ....................................................................

	$sql_dtls="select b.dealing_marchant,a.id,a.task_number,b.buyer_name,a.job_no,a.po_number_id,a.shipment_date,b.style_ref_no,a.task_start_date,d.po_number from tna_process_mst a, wo_po_details_master b, lib_tna_task c, wo_po_break_down d where a.job_no=b.job_no  and a.po_number_id=d.id and c.task_name=a.task_number and b.job_no=d.job_no_mst  and a.task_start_date <='".$current_date."'  and a.actual_start_date $actual_date and b.company_name=$compid  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.status_active=1 and d.shiping_status !=3 and d.is_confirmed=1 order by b.dealing_marchant,c.task_sequence_no";
	$sql_result=sql_select($sql);
	$dealing_marchant_data_arr=array();	
	foreach($sql_result as $row)
	{
		$dealing_marchant_data_arr[$row[csf('dealing_marchant')]][]=$row;	
	}



			
			$flag=0;
			foreach($dealing_marchant_data_arr as $dealing_marchant_id=>$data_rows)
			{ 
			ob_start();
			?> 

			<table width="985">
				<tr>
					<td width="977" height="20" align="left" valign="top">Dear Sir,</td>
				</tr>
				<tr>
					<td align="left">As per TNA schedule, all the pending tasks to start have been mentioned below</td>
				</tr>
				<tr>
					<td valign="top" align="left">
						<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
							<tr>
								<td colspan="10" height="40"><span><? echo $company_name; ?></span><br /><strong>Total Task Start Pending As On Today</strong></td>
							</tr>
								<tr><td height="30" valign="top" colspan="10">&nbsp;</td></tr>
								<tr bgcolor="#CCCCCC">
									<td colspan="3"><strong>Dealing Merchant :</strong></td>
									<td colspan="3">&nbsp;&nbsp;&nbsp;<strong><? echo $dealing_marchant[$dealing_marchant_id]; ?></strong></td>
									<td>Mobile :</td>
									<td colspan="3">&nbsp;&nbsp;&nbsp;<strong><? echo $marchant_contact_no[$dealing_marchant_id]; ?></strong></td>
								</tr>
								<tr>
									<td width="27"><strong>SL</strong></td>
									<td width="132"><strong>Task Name</strong></td>
									<td width="104"><strong>Buyer</strong></td>
									<td width="100"><strong>Job No</strong></td>
									<td width="97"><strong>Style Ref.</strong></td>
									<td width="93"><strong>Order</strong></td>
									<td width="94"><strong>Ship Date</strong></td>

									<td width="80"><strong>Plan Start Date</strong></td>
									<td width="50"><strong>Over Due Days</strong></td>
									<td><strong>comments</strong></td>
								</tr>
								<?
								$i=0;

								foreach($data_rows as $row_dtls)
								{

									if($task_short_name[$row_dtls[csf('task_number')]]){
										$i++;
										$current_date1 = strtotime("$current_date");

										$task_start_date = strtotime($row_dtls[csf('task_start_date')]);
										$dateDiff = $current_date1-$task_start_date;
										$fullDays = floor($dateDiff/(60*60*24));
										
										$key=$row_dtls[csf('job_no')].$row_dtls[csf('po_number_id')].$row_dtls[csf('task_number')];
										$tna_process_coments==$tna_process_coments_arr[$key];
										?>
										<tr>
											<td><? echo $i; ?></td>
											<td><? echo $task_short_name[$row_dtls[csf('task_number')]]; ?></td>
											<td><? echo $buyer_library[$row_dtls[csf('buyer_name')]]; ?></td>
											<td><? echo $row_dtls[csf('job_no')]; ?></td>
											<td><? echo $row_dtls[csf('style_ref_no')]; ?></td>
											<td><? echo $row_dtls[csf('po_number')]; ?></td> 
											<td><?  echo change_date_format($row_dtls[csf('shipment_date')]); ?></td>

											<td><?  echo change_date_format($row_dtls[csf('task_start_date')]); ?></td>
											<td><?  echo $fullDays; ?></td>
											<td><?  echo $tna_process_coments; ?></td>
										</tr>
										<?

										$flag=1;
									}

								}

							
							?>
						</table>
					</td>
				</tr>
			</table>
			<?


		$message="";$to="";
		$header=mail_header();
		$subject="Task start pending as on today as per TNA schedule";
		$message=ob_get_contents();
		ob_clean();
		$to=$dealing_marchant_mail[$dealing_marchant_id];
		//if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message, $from_mail);}
		if($_REQUEST['isview']==1){
			echo $message;
		}
		else{
			if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message, $from_mail);}
	
		}
	
		//echo $message;
	}
} // End Company

foreach($company_library as $compid=>$company_name){
//mail type=5 ....................................................................
	
	$sql_dtls="select b.dealing_marchant,a.id,a.task_number,b.buyer_name,a.job_no,a.po_number_id,a.shipment_date,b.style_ref_no,a.task_finish_date,d.po_number from tna_process_mst a, wo_po_details_master b, lib_tna_task c, wo_po_break_down d where a.job_no=b.job_no  and a.po_number_id=d.id  and c.task_name=a.task_number and b.job_no=d.job_no_mst and a.task_finish_date <='".$current_date."' and a.actual_finish_date $actual_date and b.company_name=$compid  and a.is_deleted=0 and a.status_active=1 and d.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.shiping_status !=3 and d.status_active !=3  and d.is_confirmed=1 order by b.dealing_marchant,c.task_sequence_no";
	$sql_result=sql_select($sql);
	$dealing_marchant_data_arr=array();	
	foreach($sql_result as $row)
	{
		$dealing_marchant_data_arr[$row[csf('dealing_marchant')]][]=$row;	
	}



			
			$flag=0;
			foreach($dealing_marchant_data_arr as $dealing_marchant_id=>$data_rows)
			{
			ob_start();
			?> 


			<table width="967">
				<tr>
					<td width="959" height="20" align="left" valign="top">Dear Sir,</td>
				</tr>
				<tr>
					<td align="left">As per TNA schedule, all the pending tasks to complete have been mentioned below.</td>
				</tr>
				<tr>
					<td valign="top" align="left">
						<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
							<tr>
								<td colspan="9" height="40"><span><? echo $company_name; ?></span><br /><strong>Total Task Completion Pending As On Today</strong></td>
							</tr>
								<tr><td height="30" valign="top" colspan="9">&nbsp;</td></tr>
								<tr bgcolor="#CCCCCC">
									<td colspan="2"><strong>Dealing Merchant :</strong></td>
									<td colspan="4">&nbsp;&nbsp;&nbsp;<strong><? echo $dealing_marchant[$dealing_marchant_id]; ?></strong></td>
									<td>Mobile :</td>
									<td colspan="2">&nbsp;&nbsp;&nbsp;<strong><? echo $marchant_contact_no[$dealing_marchant_id]; ?></strong></td>
								</tr>
								<tr>
									<td width="28"><strong>SL</strong></td>
									<td width="136"><strong>Task Name</strong></td>
									<td width="109"><strong>Buyer</strong></td>
									<td width="100"><strong>Job No</strong></td>
									<td width="101"><strong>Style Ref.</strong></td>
									<td width="105"><strong>Order</strong></td>
									<td width="103"><strong>Ship Date</strong></td>

									<td width="80"><strong>Plan Finish Date</strong></td>
									<td><strong>Over Due Days</strong></td>
								</tr>
								<?
								$i=0;
								foreach($data_rows as $row_dtls)
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
											<td><?  echo change_date_format($row_dtls[csf('shipment_date')]); ?></td>

											<td><?  echo change_date_format($row_dtls[csf('task_finish_date')]); ?></td>
											<td><?  echo $fullDays; ?></td>
										</tr>
										<?  $flag=1;  
									}
								}
							?>
						</table>
					</td>
				</tr>
			</table>
			<?
			$to="";$message="";
			

			$subject="Task completion pending as on today as per TNA schedule";

			$message="";
			$message=ob_get_contents();
			ob_clean();
			$header=mail_header();
			$to=$dealing_marchant_mail[$dealing_marchant_id];
			//if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message, $from_mail);}
			if($_REQUEST['isview']==1){
				echo $message;
			}
			else{
				if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message, $from_mail);}
		
			}
		
	//echo $message;
	}


} // End Company

?> 