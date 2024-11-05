<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../../includes/common.php');

extract($_REQUEST);

$company_library = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$buyer_library = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
$dealing_merchant_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0", "id", "team_member_name");

$previous_date = change_date_format(date('Y-m-d', strtotime('-1 day', time())), '', '', 1);
$date_cond	= " and a.APPROVED_DATE between '" . $previous_date . "' and '" . $previous_date . " 11:59:59 PM'";

$sql = "select a.JOB_NO,a.APPROVED_DATE,a.APPROVED ,b.COMPANY_NAME,b.BUYER_NAME,b.STYLE_REF_NO, b.DEALING_MARCHANT,b.REMARKS,c.PO_RECEIVED_DATE   from wo_pre_cost_mst a,wo_po_details_master b,WO_PO_BREAK_DOWN c where a.job_id=b.id and b.id=c.job_id and a.APPROVED = 1 and a.IS_DELETED=0 and b.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 $date_cond";
//echo $sql;

$sqlResult =  sql_select($sql);
foreach ($sqlResult as $row) {
	$dataArr[$row[COMPANY_NAME]][$row[JOB_NO]] = array(
		JOB_NO => $row[JOB_NO],
		APPROVED_DATE => $row[APPROVED_DATE],
		APPROVED => $row[APPROVED],
		BUYER_NAME => $row[BUYER_NAME],
		STYLE_REF_NO => $row[STYLE_REF_NO],
		DEALING_MARCHANT => $row[DEALING_MARCHANT],
		DEALING_MARCHANT => $row[DEALING_MARCHANT],
		REMARKS => $row[REMARKS],
	);

	$shipDateDataArr[$row[COMPANY_NAME]][$row[JOB_NO]][$row[PO_RECEIVED_DATE]] = $row[PO_RECEIVED_DATE];
}




foreach ($company_library as $company_id => $company_name) {
	ob_start();
?>
	<table border="1" rules="all">
		<thead>
			<th colspan="8"><strong style="font-size:18px"><?= $company_name; ?></strong><br />Date: <?= $previous_date; ?></th>
		</thead>
		<tr bgcolor="#CCCCCC">
			<th>Job No</th>
			<th>Buyer</th>
			<th>Style Ref.</th>
			<th>Dealing Merchant</th>
			<th>Shipment Date</th>
			<th>Approved Date and Time</th>
			<th>Approved Status</th>
			<th>Remarks</th>
		</tr>
		<? foreach ($dataArr[$company_id] as $rows) { ?>
			<tr>
				<td><?= $rows[JOB_NO]; ?></td>
				<td><?= $buyer_library[$rows[BUYER_NAME]]; ?></td>
				<td><?= $rows[STYLE_REF_NO]; ?></td>
				<td><?= $dealing_merchant_arr[$rows[DEALING_MARCHANT]]; ?></td>
				<td>
					<p><?= implode(', ', $shipDateDataArr[$company_id][$rows[JOB_NO]]); ?></p>
				</td>
				<td align="center"><?= date('d-m-Y h:i:s a', strtotime($rows[APPROVED_DATE])); ?></td>
				<td><?= 'Full Approved' //$rows[APPROVED];
					?></td>
				<td>
					<p><?= $rows[REMARKS]; ?></p>
				</td>
			</tr>
		<? } ?>
	</table><br />



<?
	$message = ob_get_contents();
	ob_clean();

	$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where a.id=b.mail_group_mst_id and b.mail_user_setup_id=c.id  and a.mail_item=80 and a.COMPANY_ID=$company_id and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql = sql_select($sql);
	foreach ($mail_sql as $row) {
		if ($row[EMAIL_ADDRESS]) {
			$toArr[$row[EMAIL_ADDRESS]] = $row[EMAIL_ADDRESS];
		}
	}
	$to = implode(',', $toArr);



	//echo $message;

	if ($_REQUEST['isview'] == 1) {
		$mail_item = 80;
		if ($to) {
			echo 'Mail Item:' . $form_list_for_mail[$mail_item] . '=>' . $to;
		} else {
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>" . $form_list_for_mail[$mail_item] . "</b>]<br>";
		}
		echo $message;
	} else {
		require_once('../../mailer/class.phpmailer.php');
		require_once('../setting/mail_setting.php');
		$header = mailHeader();
		$subject = "Cost Sheet Approval Status";
		if ($to != "") {
			echo sendMailMailer($to, $subject, $message, $from_mail);
		}
	}
}



?>