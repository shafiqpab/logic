<?php 
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');
	
//$company_library=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$lib_buyer=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");

$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),0))),'','',1);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', $strtotime)),'','',1); 

	 
	
	$prev_date=$current_date;
	//$current_date="24-Mar-2021";
	
 	
	$bookingHistorySql="SELECT max(APPROVED_NO)-1 as APPROVED_NO, JOB_NO,  BOOKING_NO, max(INSERT_DATE) as INSERT_DATE FROM WO_BOOKING_DTLS_HSTRY WHERE     approved_no <> 1 and revised_date <= '$prev_date' and BOOKING_NO in(select BOOKING_NO from WO_BOOKING_MST_HSTRY where revised_date='$prev_date') AND status_active = 1 AND is_deleted = 0 group by  JOB_NO,  BOOKING_NO order by BOOKING_NO";
		 //echo $bookingHistorySql;die;// PO_BREAK_DOWN_ID,
	$bookingHistorySqlRes=sql_select($bookingHistorySql);
	$jobArr=array();
	foreach($bookingHistorySqlRes as $row)
	{
		$jobArr[$row[JOB_NO]]=$row[JOB_NO];
	}
	
	
/*	$nonOrderbookingHistorySql="SELECT max(APPROVED_NO)-1 as APPROVED_NO, 0 as JOB_NO,  BOOKING_NO, max(INSERT_DATE) as INSERT_DATE FROM wo_nonord_samyar_dtlhstry WHERE     approved_no <> 1 and BOOKING_NO in(select BOOKING_NO from WO_NONORD_SAMBOO_MSTHTRY where revised_date='$prev_date')
         AND status_active = 1 AND is_deleted = 0 group by   BOOKING_NO order by BOOKING_NO
		 ";
*/		 
		 $nonOrderbookingHistorySql="SELECT max(b.APPROVED_NO)-1 as APPROVED_NO, 0 as JOB_NO, b.BOOKING_NO, max(b.INSERT_DATE) as INSERT_DATE,BUYER_ID  FROM WO_NONORD_SAMBOO_MSTHTRY a,wo_nonord_samyar_dtlhstry b WHERE b.approved_no <> 1 and a.BOOKING_NO=b.BOOKING_NO and b.revised_date='$prev_date' AND a.status_active = 1 AND a.is_deleted = 0 group by b.BOOKING_NO,BUYER_ID order by b.BOOKING_NO ";
		 
		 //echo $nonOrderbookingHistorySql;
	$nonOrderbookingHistorySqlRes=sql_select($nonOrderbookingHistorySql);
	
	
	
	$jobSql=" select COMPANY_NAME,JOB_NO,BUYER_NAME,STYLE_REF_NO from wo_po_details_master where status_active=1 and is_deleted=0 ".where_con_using_array($jobArr,1,'JOB_NO')." ";
	 //echo $jobSql; 
	$jobSqlRes=sql_select($jobSql);
	$jobArr=array();
	foreach($jobSqlRes as $row)
	{
		$jobDataArr[$row[JOB_NO]][BUYER_NAME]=$lib_buyer[$row[BUYER_NAME]];
		$jobDataArr[$row[JOB_NO]][STYLE_REF_NO]=$row[STYLE_REF_NO];
		$comArr[$row[COMPANY_NAME]]=$row[COMPANY_NAME];
	}
	

	
	ob_start();
	?>
    <table border="1" rules="all" cellpadding="5">
        <thead>
            <tr>
                <th colspan="4">
                <strong style="font-size:18px">Fabric Booking Revised</strong><br />
                Date:<?= change_date_format($prev_date);?>
                </th>
            </tr>
            <tr>
                <th>Buyer</th>
                <th>Style</th>
                <th>Booking NO</th>
                <th>Revise NO</th>
            </tr>
        </thead>
     <?
	
	foreach($bookingHistorySqlRes as $row)
	{
		?>
        
        <tr>
            <td><?= $jobDataArr[$row[JOB_NO]][BUYER_NAME];?></td>
            <td><?= $jobDataArr[$row[JOB_NO]][STYLE_REF_NO];?></td>
            <td><?= $row[BOOKING_NO];?></td>
            <td align="center"><?= $row[APPROVED_NO];?></td>
        </tr>
        <?
	}
	
	foreach($nonOrderbookingHistorySqlRes as $row)
	{
		?>
        
        <tr>
            <td><?= $lib_buyer[$row[BUYER_ID]];?></td>
            <td><?= $jobDataArr[$row[JOB_NO]][STYLE_REF_NO];?></td>
            <td><?= $row[BOOKING_NO];?></td>
            <td align="center"><?= $row[APPROVED_NO];?></td>
        </tr>
        <?
	}
	?>
    </table>
    <?
	$message="";
	$message=ob_get_contents();
	ob_clean();
	
	

	$to="";	
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=23 and b.mail_user_setup_id=c.id and a.company_id in(".implode(',',$comArr).")  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1";
	//echo $sql;
	
	$mail_sql=sql_select($sql);
	$mailArr=array();
	foreach($mail_sql as $row)
	{
		$mailArr[$row[csf('email_address')]]=$row[csf('email_address')]; 
	}
	$to=implode(',',$mailArr);
 	$subject = "Revised booking list";
	$header=mailHeader();
	//if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);
	//echo $message;
	if($_REQUEST['isview']==1){
		echo $message;
	}
	else{
		if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);
	}



	
	





?> 