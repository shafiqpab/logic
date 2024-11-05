<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../../includes/common.php');


extract($_REQUEST);

//http://202.22.203.82/erp/auto_mail/woven/buyer_inquiry_woven_auto_mail.php?sys_id=OG-QIN-21-00023
//echo $job_id.'='.$email_id.'='.$cbo_company_name;die;
$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ", "id", "buyer_name");
$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
$user_maill_arr = return_library_array("select id,USER_EMAIL from USER_PASSWD", "id", "USER_EMAIL");


list($pre_mst_id, $mailId) = explode('__', $data);
$pre_mst_id = str_replace('*', ',', $pre_mst_id);
$mailArr[] = str_replace('*', ',', $mailId);

$sql_pre = "select  a.job_no,a.company_name, a.buyer_name, a.style_ref_no, b.costing_date, b.sew_smv, b.approved, b.inserted_by,d.po_number from wo_pre_cost_mst b,  wo_po_details_master a,wo_po_break_down d where a.id=b.job_id and a.id=d.job_id and b.id in($pre_mst_id)   and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and d.status_active=1   order by a.id";
//$toArr[]=trim($email_id);

$sql_result = sql_select($sql_pre);
foreach ($sql_result as $row) {
	$pre_job_arr[$row[csf('job_no')]]['style'] = $row[csf('style_ref_no')];
	$pre_job_arr[$row[csf('job_no')]]['buyer_name'] = $buyer_arr[$row[csf('buyer_name')]];
	$pre_job_arr[$row[csf('job_no')]]['inserted_by'] = $row[csf('inserted_by')];
	$pre_job_arr[$row[csf('job_no')]]['po_number'] .= $row[csf('po_number')] . ',';
	//$INSERTED_BY=$row[csf('inserted_by')];
	$company_name = $row[csf('company_name')];
}
$sql_cau = "select b.job_no,c.refusing_reason from refusing_cause_history c,wo_pre_cost_mst b where b.id=c.mst_id and b.id in($pre_mst_id)  and c.entry_form=15 order by c.id ";
$sql_result_cause = sql_select($sql_cau);
foreach ($sql_result_cause as $row) {
	$pre_job_arr[$row[csf('job_no')]]['refusing_reason'] = $row[csf('refusing_reason')];
}
unset($sql_result_cause);

//-----------------------	


$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=11 and a.MAIL_TYPE in(2,0) and b.mail_user_setup_id=c.id and a.company_id=$company_name AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
$mail_sql = sql_select($sql);
foreach ($mail_sql as $row) {
	$mailArr[] = $rows[EMAIL_ADDRESS];
}





/*$sql_team_mail="SELECT d.USER_EMAIL from USER_PASSWD d WHERE d.id = $INSERTED_BY $whereCon";
$sql_team_mail_result=sql_select($sql_team_mail);
foreach($sql_team_mail_result as $rows){
	$mailArr[]=$rows[USER_EMAIL];
}
*/
ob_start();

//if(count($sql_result)>0)

?>

<div style="width:650px; font-size:20px; font-weight:bold" align="center">
	<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
		<tr>
			<td width="450">
				<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td align="center" style="font-size:20px;"><?php echo $company_library[$cbo_company_name]; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table border="1" align="left" class="rpt_table" cellpadding="0" width="100%" cellspacing="0" rules="all">
		<caption>
			<b>
				Dear Concerned,<br>
				Your approval request against the following reference is denied.
			</b>
		</caption>
		<tr>
			<th align="left" style="font-size:20px;" width="20"><strong> SL.</strong></th>
			<th align="left" style="font-size:20px;" width="120">Job No</th>
			<th align="left" style="font-size:20px;" width="100">Style Ref.</th>
			<th align="left" style="font-size:20px;" width="100">Buyer</th>
			<th align="left" style="font-size:20px;" width="100">Order No</th>
			<th align="left" style="font-size:20px;" width="100">Deny cause</th>
		</tr>
		<?
		$k = 1;
		foreach ($pre_job_arr as $job_no => $val) {
			$mailArr[] = $user_maill_arr[$val[inserted_by]];
			$po_number = rtrim($val['po_number'], ',');
			$po_numberNos = implode(",", array_unique(explode(",", $po_number)));
		?>
			<tr>
				<td align="left" width="20" style="font-size:20px;"><strong>
						<? echo $k; ?>
					</strong> </td>
				<td align="left" width="120" style="font-size:20px;"><?php echo $job_no; ?></td>
				<td align="left" width="100" style="font-size:20px;">
					<p style=" word-break:break-all"><strong> <?php echo $val['style']; ?></strong></p>
				</td>
				<td align="left" width="100" style="font-size:20px;"> <?php echo $val['buyer_name']; ?> </td>
				<td align="left" width="100" style="font-size:20px;">
					<p style=" word-break:break-all"><strong><?php echo $po_numberNos; ?></strong></strong>
				</td>
				<td align="left" width="100" style="font-size:20px;">
					<p><?= $val['refusing_reason']; ?></p>
				</td>
			</tr>
		<?
			$k++;
		}
		?>

	</table>

</div>


<?
//$mstRow[BRAND_ID]

$message = ob_get_contents();
ob_clean();
$to = implode(',', $mailArr);



if ($_REQUEST['isview'] == 1) {
	$mail_item = 81;
	if ($to) {
		echo 'Mail Item:' . $form_list_for_mail[$mail_item] . '=>' . $to;
	} else {
		echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>" . $form_list_for_mail[$mail_item] . "</b>]<br>";
	}
	echo $message;
} else {
	require_once('../../mailer/class.phpmailer.php');
	require_once('../setting/mail_setting.php');
	$subject = "Pre costing approval mail.";
	$header = mailHeader();
	if ($to != "") echo sendMailMailer($to, $subject, $message, $from_mail);
}




?>