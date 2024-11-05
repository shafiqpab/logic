<?php
//$active_cunnection = file_get_contents('mail_config.txt');
include(base_path('auto_mail/setting/config.php'));
//require(base_path('mailer/class.phpmailer.php'));

require(base_path('vendor/autoload.php'));
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function mailHeader()
{
	global $active_cunnection;
	list($user,$pass,$sender,$host,$port,$secure_port,$send_to,$is_smtp)=explode('_split_',$active_cunnection);
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: '.$sender.'' . "\r\n"; // Sender's Email Address
	$headers .= 'Return-Path: Admin <'.$sender.'> /n'; // Indicates Return-path
	$headers .= 'Reply-To: Admin <'.$sender.'> /n'; // Reply-to Address
	$headers .= 'X-Mailer: PHP/' . phpversion(); // For X-Mailer
	return $headers;
}

function sendMailMailer($to, $sub, $html, $from,$attachmentArr=array(),$return_int=0){

	global $active_cunnection;
	list($user,$pass,$sender,$host,$port,$secure_port,$send_to,$is_smtp,$smtp_debug,$smtp_auth)=explode('_split_',$active_cunnection);

	$mail = new PHPMailer();
	if($is_smtp==1){
		$mail->IsSMTP();
	}
	$mail->SMTPDebug = ($smtp_debug == true ) ? 3 : $smtp_debug;
	$mail->SMTPAuth =  ($smtp_auth == true ) ? true : false; // authentication enabled
	if($secure_port){
		$mail->SMTPSecure = $secure_port; 
	}//Encryption method secure transfer enabled REQUIRED for Gmail [incoming port]

	$mail->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
		)
	);
	
	$mail->Host = $host;
	$mail->Port = $port; // Outgoing port
	$mail->IsHTML(true);
	$mail->Username = $user;
	$mail->Password = $pass;
	$mail->CharSet 	= 'UTF-8';

	if( $from=="" ){$from=$user;}
	list($user_name,$domain)=explode('@',$sender);
	
	$mail->setFrom( $from,$user_name);
	$mail->addReplyTo( $from,$user_name);
	$mail->Subject = $sub;
	$mail->AltBody = 'MAIL GENERATED FROM PLATFORM SERVER';
	$mail->Body = $html;
	$tos=explode(",",$to);
	
	for($i=0; $i<count($tos); $i++)
	{
		if( $tos[$i]!=""){
			list($user_name,$domain)=explode('@',$tos[$i]);
			$mail->AddAddress($tos[$i],$user_name);
			//$mail->addBCC($tos[$i],$user_name);
			//$mail->addCC($tos[$i],$user_name);
		}
		//if( $tos[$i]!="") $mail->AddAddress($tos[$i],"PLATFORM USER");
	}
	
	
	
	foreach($attachmentArr as $attFileStr)
	{
		list($attFilePath,$attFileName)=explode('**',$attFileStr);
		//$att_file_arr=array('../file_upload/sales_contract_entry_86_3622.xlsx**sales_contract_entry_86_3622.xlsx');
		if($attFilePath!=''){$mail->addAttachment($attFilePath,$attFileName);}
	}
		

		//echo $abc;die;

	if (!$mail->send()) {
		if($return_int==0){return "****Mail Not Sent.---".date("Y-m-d h:i:s a");}
		else{return 0;}
	} else {
		if($return_int==0){return "****Mail Sent.---".date("Y-m-d h:i:s a");}
		else{return 1;}
	}
}





?>