<?php
date_default_timezone_set("Asia/Dhaka");
extract($_REQUEST);
 //http://localhost/platform_v3.5/auto_mail/btb_margin_lc_amendment_auto_mail.php?data=reza@abc.com**2622&action=mail_action

require_once('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
require '../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
require_once('setting/mail_setting.php');

$comp_lib = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id","company_name");
$bank_lib = return_library_array("select id, BANK_NAME from LIB_BANK where  status_active=1 and is_deleted=0","id", "BANK_NAME");
	
list($mail,$sys_id)=explode('**',$data);	

	$data_array=sql_select("select ID, LC_NUMBER, SUPPLIER_ID, IMPORTER_ID, LC_DATE, APPLICATION_DATE, LAST_SHIPMENT_DATE, LC_EXPIRY_DATE, ITEM_BASIS_ID, LC_VALUE, CURRENCY_ID, ISSUING_BANK_ID, ITEM_CATEGORY_ID, TENOR, TOLERANCE, INCO_TERM_ID, INCO_TERM_PLACE, DELIVERY_MODE_ID, PORT_OF_LOADING, PORT_OF_DISCHARGE, REMARKS, PARTIAL_SHIPMENT, PI_ID, PI_VALUE, PAYTERM_ID,PI_ENTRY_FORM from com_btb_lc_master_details where id='$sys_id'"); 
	$row=$data_array[0];
	
	$amendment_data_array = sql_select("SELECT AMENDMENT_NO, AMENDMENT_DATE,BTB_LC_VALUE FROM com_btb_lc_amendment WHERE btb_id='$sys_id' and amendment_no<>0 and status_active=1 and is_deleted=0 and is_original=0");
	foreach($amendment_data_array as $amendRow){
		$row[AMENDMENT_NO][]=$amendRow[AMENDMENT_NO];
		$row[AMENDMENT_DATE][]=change_date_format($amendRow[AMENDMENT_DATE]);
		$row[BTB_LC_VALUE]+=$amendRow[BTB_LC_VALUE];
	}
	
 	
 	$pi_no=return_field_value("PI_NUMBER","COM_PI_MASTER_DETAILS"," status_active=1 and is_deleted=0 and id=".$row[PI_ID]."");

	$imgSql="select INSERT_DATE from common_photo_library where form_name in('BTBMargin LC Amendment') and is_deleted=0 and MASTER_TBLE_ID='$sys_id'  order by INSERT_DATE desc";
	
	//echo $imgSql;die;
	$imgSqlResult=sql_select($imgSql);
	$imgRows=$imgSqlResult[0];
	
	
	
ob_start();	
	
	
?>	
<table cellpadding="5">
    <tr><td colspan="3"><b>Dear Sir,</b></td></tr>									
    <tr><td colspan="3">One Back to Back LC has been opened by <?=$comp_lib[$row[IMPORTER_ID]];?>. in favor of your company from <?=$bank_lib[$row[ISSUING_BANK_ID]];?>.</td></tr>										
    <tr><td width="190">Beneficiary</td> <td width="15">:</td><td><?=$comp_lib[$row[IMPORTER_ID]];?></td></tr>							
    <tr><td>L/C Number</td> <td>:</td><td><?=$row[LC_NUMBER];?></td></tr>							
    <tr><td>L/C Opening Date</td> <td>:</td><td><?=change_date_format($row[LC_DATE]);?></td></tr>							
    <tr><td>File Tagging Date</td> <td>:</td><td><?=change_date_format($imgRows[INSERT_DATE]);?></td></tr>						
    <tr><td>Amendment no</td> <td>:</td><td><?=implode(',',$row[AMENDMENT_NO]);?></td></tr>							
    <tr><td>Amendment Date</td> <td>:</td><td><?=implode(',',$row[AMENDMENT_DATE]);?></td></tr>							
    <tr><td>PI No</td> <td>:</td><td><?=$pi_no;?></td></tr>							
    <tr><td>PI Value</td> <td>:</td><td><?=number_format($row[PI_VALUE],2);?></td></tr>							
    <tr><td>Increased / Decreased Value</td> <td>:</td><td><?=number_format($row[BTB_LC_VALUE],2);?></td></tr>							
    <tr><td>Shipment Date</td> <td>:</td><td><?=change_date_format($row[LAST_SHIPMENT_DATE]);?></td></tr>						
    <tr><td>Expiry Date</td> <td>:</td><td><?=change_date_format($row[LC_EXPIRY_DATE]);?></td></tr>							
    <tr><td colspan="3">If there is any query or concern, please talk to concerned merchandisers in the factory directly.									
    <br />Please do not respond to this address since it is a system generated e-mail. </td></tr>										
</table>

<?	
	

	$message=ob_get_contents();
	ob_clean();
	$toMailArr=array();
	$toMailArr[]=$mail;

	$sql = "SELECT c.email_address as MAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=72 and a.company_id=".$row[IMPORTER_ID]." and b.mail_user_setup_id=c.id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and a.MAIL_TYPE=2";//and 
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if($row[MAIL]){$toMailArr[$row[MAIL]]=$row[MAIL]; }
	}
 	
	$to=implode(',',$toMailArr);
	$subject = "BTB Margin LC Amendment Notification";
	$header=mailHeader();
	//$to="reza@logicsoftbd.com";
	//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);}
	//echo $message;

	if($_REQUEST['isview']==1){
		echo $to.$message;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);}
	}

	
	exit();
	
?>