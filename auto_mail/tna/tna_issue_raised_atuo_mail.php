<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
require_once('../setting/mail_setting.php');

// $previous_date= date('d-M-Y', strtotime("-1 day"));
// $current_date = date('d-M-Y', time());
// $date_cond	=" and insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";

$company_library = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$buyer_library = return_library_array("select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
$user_arr = return_library_array("select id,user_full_name from user_passwd where valid=1", "id", "user_full_name");
$task_arr = return_library_array("select a.TASK_NAME,a.TASK_SHORT_NAME from LIB_TNA_TASK a where a.task_type=1 and a.STATUS_ACTIVE=1 and a.is_deleted=0", "TASK_NAME", 'TASK_SHORT_NAME');


$sql = "select c.COMPANY_NAME,a.TASK_ID,a.INSERTED_BY,a.INSERT_DATE,c.JOB_NO,c.BUYER_NAME,b.id as PO_ID,b.PO_NUMBER,a.ISSUE_RAISED from TNA_TASK_ISSUE_RAISED_CLOSED a,WO_PO_BREAK_DOWN b,WO_PO_DETAILS_MASTER c where c.id=b.job_id  and b.id=a.ORDER_ID and a.ISSUE_STATUS=1 and a.TASK_TYPE=1 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0";
$sql_res = sql_select($sql);
$dataArr = array();
foreach ($sql_res as $rows) {
	$dataArr[$rows['COMPANY_NAME']][] = $rows;
	$poDataArr[$rows['PO_ID']] = $rows['PO_ID'];

}


$bookingSql = "select PO_BREAK_DOWN_ID,BOOKING_NO from WO_BOOKING_DTLS where STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($poDataArr,0,'PO_BREAK_DOWN_ID')." and BOOKING_TYPE in(1)";
//echo $bookingSql;die;
$bookingSqlRes = sql_select($bookingSql);
$booking_arr = array();
foreach ($bookingSqlRes as $rows) {
	$booking_arr[$rows['PO_BREAK_DOWN_ID']][$rows['BOOKING_NO']] = $rows['BOOKING_NO'];
}

ob_start();
?>
<div>
	<b>Dear Sir,</b><br>
	Please check below issue management status.<br>
</div>
<?
foreach ($company_library as $company_id => $company_name) {
	if(count($dataArr[$company_id])==0){continue;}
?>
	<table border="1" rules="all">
		<thead>
			<tr>
				<td colspan="8" align="center">
					<b>Issue Management</b>
					<div><b><?= $company_name; ?></b></div>
					<small><b>Date & Time: <?= date("d-m-Y h:i:s a"); ?></b></small>
				</td>
			</tr>
			<tr>
				<td colspan="3"><b>Issue Status</b></td>
				<td colspan="5"><b>Issue Raised</b></td>
			</tr>
			<tr bgcolor="#CCC">
				<td>Date & Time</td>
				<td>Buyer</td>
				<td>Job</td>
				<td>Order</td>
				<td>FB.Booking</td>
				<td>Raised By</td>
				<td>Task Name</td>
				<td>Comment</td>
			</tr>
		</thead>
		<tbody>
			<?
			$i = 1;

			foreach ($dataArr[$company_id] as $rows) {
				$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";

			?>
				<tr bgcolor="<?= $bgcolor; ?>">
					<td><?= $rows['INSERT_DATE']; ?></td>
					<td><?= $buyer_library[$rows['BUYER_NAME']]; ?></td>
					<td><?= $rows['JOB_NO']; ?></td>
					<td><?= $rows['PO_NUMBER']; ?></td>
					<td><?= implode(', ',$booking_arr[$rows['PO_ID']]); ?></td>
					<td><?= $user_arr[$rows['INSERTED_BY']]; ?></td>
					<td><?= $task_arr[$rows['TASK_ID']]; ?></td>
					<td><?= $rows['ISSUE_RAISED']; ?></td>
				</tr>
			<?
				$i++;
			}

			?>
		</tbody>
	</table><br>
<?
}
$message = ob_get_contents();
ob_clean();


$mail_item = 116;

$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=$mail_item and b.mail_user_setup_id=c.id and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";


$mail_sql = sql_select($sql);
$mailArr = array();
foreach ($mail_sql as $row) {
	$mailArr[$row['EMAIL_ADDRESS']] = $row['EMAIL_ADDRESS'];
}
$to = implode(',', $mailArr);

$subject = "TNA Issue Raised Notification";
$header = mailHeader();

if ($_REQUEST['isview'] == 1) {
	if ($to) {
		echo 'Mail Item:' . $form_list_for_mail[$mail_item] . '=>' . $to;
	} else {
		echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>" . $form_list_for_mail[$mail_item] . "</b>]<br>";
	}
	echo $message;
} else {
	if ($to != "") {
		echo sendMailMailer($to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port);
	}
}



?>