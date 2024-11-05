<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../../includes/common.php');
extract($_REQUEST);

$user_maill_arr = return_library_array("select id,USER_EMAIL from USER_PASSWD", "id", "USER_EMAIL");
$supplier_arr = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0", "id", "team_member_name");

list($sysId, $mailId) = explode('__', $data);
$sysId = str_replace('*', ',', $sysId);
$mailArr[] = str_replace('*', ',', $mailId);



$sql = "  SELECT a.WO_NUMBER, a.SUPPLIER_ID,a.INSERTED_BY, c.APPROVAL_CAUSE FROM wo_non_order_info_mst a,  wo_non_order_info_dtls b,  FABRIC_BOOKING_APPROVAL_CAUSE c WHERE a.id = b.mst_id  AND a.id = c.booking_id AND b.item_category_id in (5,6,7,23)  AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0  AND c.entry_form = 3  AND a.entry_form = 145 AND a.id IN ($sysId) GROUP BY a.WO_NUMBER, a.SUPPLIER_ID,a.INSERTED_BY, c.APPROVAL_CAUSE";

$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=67 and a.MAIL_TYPE in(2,0) and b.mail_user_setup_id=c.id and a.company_id=$company_name AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
$mail_sql = sql_select($sql);
foreach ($mail_sql as $row) {
	$mailArr[] = $rows[EMAIL_ADDRESS];
}
//echo $sql;die;


$sql_dtls = sql_select($sql);
ob_start();
?>
<b>Dear Concerned,</b> <br />
Your approval request against the following reference is denied.

<table rules="all" border="1">
	<tr bgcolor="#CCCCCC">
		<td>SL</td>
		<td>Purchase Order No</td>
		<td>Supplier Name</td>
		<td>Deny cause</td>
	</tr>

	<?php
	$i = 1;
	foreach ($sql_dtls as $row) {
		$mailArr[$row[INSERTED_BY]] = $user_maill_arr[$row[INSERTED_BY]];

	?>
		<tr>
			<td><?= $i; ?></td>
			<td><?= $row[WO_NUMBER] ?></td>
			<td><?= $supplier_arr[$row[SUPPLIER_ID]] ?></td>
			<td><?= $row[APPROVAL_CAUSE] ?></td>
		</tr>
	<?php } ?>
</table>
<?

$message = ob_get_contents();
ob_clean();

$to = implode(',', $mailArr);




if ($_REQUEST['isview'] == 1) {
	$mail_item = 67;
	if ($to) {
		echo 'Mail Item:' . $form_list_for_mail[$mail_item] . '=>' . $to;
	} else {
		echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>" . $form_list_for_mail[$mail_item] . "</b>]<br>";
	}
	echo $message;
} else {
	$header = mailHeader();
	require_once('../../mailer/class.phpmailer.php');
	require_once('../setting/mail_setting.php');
	$header = mailHeader();
	if ($to != "") echo sendMailMailer($to, $subject, $message, $from_mail);
}



//echo $to;







?>