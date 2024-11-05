<?php
date_default_timezone_set("Asia/Dhaka");
extract($_REQUEST);

$req_url_arr=explode('/',$_SERVER['REQUEST_URI']);
$base_path = $_SERVER['SERVER_NAME'].'/'.$req_url_arr[1];
//http://59.152.60.250:14080/teamerp/auto_mail/btb_margin_lc_auto_mail.php?sys_id=GKD-BTB-20-00049

require_once('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
//require '../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
require_once('setting/mail_setting.php');

$comp_lib = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id","company_name");
$bank_lib = return_library_array("select id, BANK_NAME from LIB_BANK where  status_active=1 and is_deleted=0","id", "BANK_NAME");
$supplier_lib = return_library_array("select c.supplier_name,c.id from lib_supplier c where  c.status_active=1 and c.is_deleted=0","id", "supplier_name");
$supplier_mail = return_library_array("select c.EMAIL,c.id from lib_supplier c where  c.status_active=1 and c.is_deleted=0","id", "EMAIL");

	$sql="select A.ID,a.PI_ID,A.BTB_SYSTEM_ID,A.LC_NUMBER,A.ISSUING_BANK_ID,A.CONFIRMING_BANK,A.IMPORTER_ID,A.PI_VALUE,A.LAST_SHIPMENT_DATE,A.LC_DATE,A.SUPPLIER_ID,a.INSERT_DATE  from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.BTB_SYSTEM_ID='$sys_id'";//   and a.invoice_no='123123'
	$result=sql_select($sql);
	$row=$result[0];
	
	if($sys_id==''){echo "BTB SYSTEM ID WRONG";exit();}
	if($row[LC_NUMBER]==''){echo "LC Number Not Found. Please Update by Valied LC Number.";exit();}
	
	
	
	$arrayPIData=explode(',',$row['PI_ID']);
	$arrayData[]=$row['BTB_SYSTEM_ID'];
	


	
	
	$imgSql="select FILE_TYPE,IMAGE_LOCATION,REAL_FILE_NAME, MASTER_TBLE_ID, FORM_NAME,INSERT_DATE from common_photo_library where form_name in('proforma_invoice','BTBMargin LC') and is_deleted=0  ".where_con_using_array($arrayData,1,'MASTER_TBLE_ID')."";//'quotation_entry',
	$imgSqlResult=sql_select($imgSql);
	foreach($imgSqlResult as $rows){
		$att_file_arr[]='../'.$rows['IMAGE_LOCATION'].'**'.$rows['REAL_FILE_NAME'];
		$file_date_time_arr[]=$rows['INSERT_DATE'];
	}
	
	$pi_number_arr = return_library_array("select id, pi_number from com_pi_master_details where status_active=1 and is_deleted=0 ".where_con_using_array($arrayPIData,0,'id')." ", "id","pi_number");

 


	
	if($row['LAST_SHIPMENT_DATE']!=''){$row['LAST_SHIPMENT_DATE']=date("d/m/Y",strtotime($row['LAST_SHIPMENT_DATE']));}
	ob_start();
	echo "<b>Dear Sir</b>,<br><br> 
	
	One Back to Back LC has been opened by <b>".$comp_lib[$row['IMPORTER_ID']]."</b>  in favor of your company from <b>".$bank_lib[$row['ISSUING_BANK_ID']]."</b>.<br><br>
	
	L/C Number: <b>".$row['LC_NUMBER']."</b><br>
	Beneficiary: <b>".$supplier_lib[$row['SUPPLIER_ID']]."</b><br>
	PI Number: <b>".implode(',',$pi_number_arr)."</b><br>
	PI Value: <b>".number_format($row['PI_VALUE'],2)."</b><br>
	L/C Opening Date And Time: <b>".date("d/m/Y h:i:s A",strtotime($row['INSERT_DATE']))."</b> <br>
	Shipment Date: <b>".$row['LAST_SHIPMENT_DATE']."</b><br>
	File Attach Date: <b>".implode(',',$file_date_time_arr)."</b><br><br>
	
	If there is any query or concern, please talk to concerned merchandisers in the factory directly.<br> 
	Please do not respond to this address since it is a system generated e-mail.
	";
	
	$message=ob_get_contents();
	ob_clean();
	



	$toMailArr[]=return_field_value("b.USER_EMAIL","COM_PI_MASTER_DETAILS a, USER_PASSWD b","b.id=a.INSERTED_BY ".where_con_using_array($arrayPIData,0,'a.id')."", "USER_EMAIL");
	
	$toMailArr[]=$supplier_mail[$row['SUPPLIER_ID']];
	
	$to="";	
	$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=37 and a.company_id=".$row['IMPORTER_ID']." and b.mail_user_setup_id=c.id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1";//and 
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $rows)
	{
		if($rows['EMAIL_ADDRESS']){$toMailArr[]=$rows['EMAIL_ADDRESS']; }
	}
 	
	$to=implode(',',$toMailArr);
	$subject = "BTB Margin LC Notification [".$comp_lib[$row['IMPORTER_ID']]."]";
	$header=mailHeader();
 
	if($_REQUEST['isview']==1){
		$mail_item=37;
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);}
	}


	
	exit();
	
?>