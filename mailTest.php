<?php
date_default_timezone_set("Asia/Dhaka");

require_once('includes/common.php');
require_once('mailer/class.phpmailer.php');

	

	$to="muktobani@gmail.com";
	$subject = "Test Subject";
	$message = 'Hellow Bangladesh';
	$header = mail_header();
	if($to!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );

       
    
?> 