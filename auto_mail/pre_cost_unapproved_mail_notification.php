<?php
date_default_timezone_set("Asia/Dhaka");
// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
extract($_REQUEST);
// var returnValue=return_global_ajax_value(reponse[2], 'weven_gmts_quotation_unapproved_mail', '', '../../../auto_mail/pre_cost_unapproved_mail_notification');


//$action='weven_gmts_pre_cost_unapproved_mail';

if($action=='weven_gmts_pre_cost_unapproved_mail'){
	

	//$data=10380;
	$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name",$con);
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name",$con);

	$query = "select  a.JOB_NO,b.APPROVAL_CAUSE,c.COMPANY_NAME,c.BUYER_NAME from wo_pre_cost_mst a,fabric_booking_approval_cause b,wo_po_details_master c where a.id=b.booking_id and a.job_no=c.job_no and a.id=$data";
	$queryResult =  sql_select($query);

	$emailBody =  'Dear Sir,<br>';
	$emailBody .=  "For unavoidable reason this Job has to be un-approved. Please see details below:";
	
	$emailBody .=  "Job No : ".$queryResult[0][JOB_NO]."</b>";
	$emailBody .=  "Company Name : ".$company_library[$queryResult[0][COMPANY_NAME]]."</b>";
	$emailBody .=  "Buyer Name : ".$buyer_library[$queryResult[0][BUYER_NAME]]."</b>";
	$emailBody .=  "Style Ref : ".$queryResult[0][STYLE_REF]."</b>";
	$emailBody .=  "Un-approve request : ".$queryResult[0][APPROVAL_CAUSE]."</b>";

	$emailBody .= "<br>Kindly review and Un-approve.<br><br><br>";
	$emailBody .= "Thanks & best regards.<br>";
	
	
	$sql2 = "select a.user_id,b.user_email from electronic_approval_setup a,user_passwd b where a.user_id=b.id and a.company_id=".$queryResult[0][COMPANY_NAME]." and a.page_id=428 and a.is_deleted=0";	
	$mail_sql2=sql_select($sql2);
	foreach($mail_sql2 as $row)
	{
		if ($to=="") {
			$to=$row[csf('user_email')];
		}  else{
			 $to=$to.",".$row[csf('user_email')];
		}

	}

	$subject="Pre-Costing Un-approved Notification";
	//if($to!=""){echo send_mail_mailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Not Send';}
	if($_REQUEST['isview']==1){
		echo $emailBody;
	}
	else{
		if($to!=""){echo send_mail_mailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Not Send';}
	}


exit();	
}


if($action=='weven_gmts_quotation_unapproved_mail'){
	

	//$data=10380;
	$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name",$con);

	$query = "select  a.ID,a.STYLE_REF,b.APPROVAL_CAUSE,a.COMPANY_ID,a.BUYER_ID from wo_price_quotation a,fabric_booking_approval_cause b where a.id=b.booking_id and a.id=$data";
	
	 //echo $query;die;
	$queryResult =  sql_select($query);

	$emailBody =  'Dear Sir,<br>';
	$emailBody .=  "For unavoidable reason this quotation has to be un-approved. Please see details below:";
	
	$emailBody .=  "Quotation ID : ".$queryResult[0][ID]."</b>";
	$emailBody .=  "Company Name : ".$company_library[$queryResult[0][COMPANY_ID]]."</b>";
	$emailBody .=  "Buyer Name : ".$buyer_library[$queryResult[0][BUYER_ID]]."</b>";
	$emailBody .=  "Style Ref : ".$queryResult[0][STYLE_REF]."</b>";
	$emailBody .=  "Un-approve request : ".$queryResult[0][APPROVAL_CAUSE]."</b>";

	$emailBody .= "<br>Kindly review and Un-approve.<br><br><br>";
	$emailBody .= "Thanks & best regards.<br>";
	
	
	$sql2 = "select a.user_id,b.user_email from electronic_approval_setup a,user_passwd b where a.user_id=b.id and a.company_id=".$queryResult[0][COMPANY_ID]." and a.page_id=427 and a.is_deleted=0";	
	$mail_sql2=sql_select($sql2);
	foreach($mail_sql2 as $row)
	{
		if ($to=="") {
			$to=$row[csf('user_email')];
		}  else{
			 $to=$to.",".$row[csf('user_email')];
		}

	}

	$subject="Quotation Un-approved Notification";
//	if($to!=""){echo send_mail_mailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Not Send';}
	if($_REQUEST['isview']==1){
		echo $emailBody;
	}
	else{
		if($to!=""){echo send_mail_mailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Not Send';
	}


exit();	
}

if($action=='weven_gmts_partial_fb_unapproved_mail'){
	

	//$data=10380;
	$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name",$con);

	$query = "select  a.BOOKING_NO,b.APPROVAL_CAUSE,a.COMPANY_ID from wo_booking_mst a,fabric_booking_approval_cause b where a.id=b.booking_id and a.id=$data";
	
	 //echo $query;die;
	$queryResult =  sql_select($query);

	$emailBody =  'Dear Sir,<br>';
	
	$emailBody .=  "Booking No : <b>".$queryResult[0][BOOKING_NO]."</b> Need to Un-approved due to <b>".$queryResult[0][APPROVAL_CAUSE]."</b>";


	$emailBody .= "<br>Kindly review and advise Un-approved please.<br><br><br>";
	$emailBody .= "Thanks.<br>";
	$emailBody .= "Kind regards<br>";
	
	
	$sql2 = "select a.user_id,b.user_email from electronic_approval_setup a,user_passwd b where a.user_id=b.id and a.company_id=".$queryResult[0][COMPANY_ID]." and a.page_id=410 and a.is_deleted=0";	
	$mail_sql2=sql_select($sql2);
	foreach($mail_sql2 as $row)
	{
		if ($to=="") {
			$to=$row[csf('user_email')];
		}  else{
			 $to=$to.",".$row[csf('user_email')];
		}

	}

	$subject="Partial Fabric Booking Un-approved Notification";
	//if($to!=""){echo send_mail_mailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Not Send';}
	if($_REQUEST['isview']==1){
		echo $emailBody;
	}
	else{
		if($to!=""){echo send_mail_mailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Not Send';}
	}


exit();	
}






?>
