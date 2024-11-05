<?
require_once('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
//require '../vendor/autoload.php';
require_once('../setting/mail_setting.php');

 ob_start();?>

<? include('html/garments.html'); ?><br>
<? include('html/textile.html'); ?><br>
<? include('html/machine_summary.html'); ?><br>
<? include('html/textile_rms_buyer_segregation.html'); ?>

<?
$html = ob_get_contents();
ob_clean();



$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=18 AND a.MAIL_TYPE=1 and b.mail_user_setup_id=c.id and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	 
	
$mail_sql2=sql_select($sql2, '', '', '', $con);
foreach($mail_sql2 as $row)
{
    $emailArr[$row[csf('email_address')]]=$row[csf('email_address')];
}

$to=implode(',',$emailArr);

//$to="erpsupport@urmigroup.net,sarker@logicsoftbd.com";
$subject="Daily Report";
$message="";
$header=mailHeader();
if($_REQUEST['isview']==1){
    echo $to.$html;
}
else{
    
    if($to!=""){echo sendMailMailer( $to, $subject, $html, $from_mail );}
}



?>
