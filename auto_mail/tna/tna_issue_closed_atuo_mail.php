<?php
date_default_timezone_set("Asia/Dhaka");

include('../../includes/common.php');
//include('../mailer/class.phpmailer.php');
include('../setting/mail_setting.php');


date_default_timezone_set("Asia/Dhaka");
$file = 'tna_issue_closed_auto_mail_log.txt';
$previous_date = file_get_contents($file);
// $today = date("d-M-Y h:i:s a",time());
// file_put_contents($file, $today);
 

//$previous_date= date('d-M-Y', strtotime("-10 day"));
$current_date = date('d-M-Y h:i:s a', time());
$date_cond	=" and a.UPDATE_DATE between '".$previous_date."' and '".$current_date."'";


$company_library = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$buyer_library = return_library_array("select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
$user_arr = return_library_array("select id,user_full_name from user_passwd where valid=1", "id", "user_full_name");
$user_mail_arr = return_library_array("select id,USER_EMAIL from user_passwd where valid=1", "id", "USER_EMAIL");
$task_arr = return_library_array("select a.TASK_NAME,a.TASK_SHORT_NAME from LIB_TNA_TASK a where a.task_type=1 and a.STATUS_ACTIVE=1 and a.is_deleted=0", "TASK_NAME", 'TASK_SHORT_NAME');


$sql = "select c.COMPANY_NAME,a.TASK_ID,a.INSERTED_BY,a.UPDATED_BY,a.INSERT_DATE,a.UPDATE_DATE,c.JOB_NO,c.BUYER_NAME,b.id as PO_ID,b.PO_NUMBER,a.ISSUE_CLOSED,c.DEALING_MARCHANT from TNA_TASK_ISSUE_RAISED_CLOSED a,WO_PO_BREAK_DOWN b,WO_PO_DETAILS_MASTER c where c.id=b.job_id  and b.id=a.ORDER_ID and a.ISSUE_STATUS=2 and a.TASK_TYPE=1 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 $date_cond__ order by a.INSERTED_BY";
//echo $sql;die;
$sql_res = sql_select($sql);
$dataArr = array();
foreach ($sql_res as $rows) {
	$dataArr[$rows['INSERTED_BY']][] = $rows;
	$poDataArr[$rows['PO_ID']] = $rows['PO_ID'];
}

$dealing_marchant_arr = return_library_array("select ID,TEAM_MEMBER_EMAIL from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by TEAM_MEMBER_EMAIL", "ID", 'TEAM_MEMBER_EMAIL');


$bookingSql = "select PO_BREAK_DOWN_ID,BOOKING_NO from WO_BOOKING_DTLS where STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($poDataArr,0,'PO_BREAK_DOWN_ID')." and BOOKING_TYPE in(1)";
//echo $bookingSql;die;
$bookingSqlRes = sql_select($bookingSql);
$booking_arr = array();
foreach ($bookingSqlRes as $rows) {
	$booking_arr[$rows['PO_BREAK_DOWN_ID']][$rows['BOOKING_NO']] = $rows['BOOKING_NO'];
}



foreach ($dataArr as $user_id => $rowsArr) {
ob_start();
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
				<td colspan="5"><b>Issue Closed</b></td>
			</tr>
			<tr bgcolor="#CCC">
				<td>Date & Time</td>
				<td>Buyer</td>
				<td>Job</td>
				<td>Order</td>
				<td>FB.Booking</td>
				<td>Closed By</td>
				<td>Task Name</td>
				<td>Comment</td>
			</tr>
		</thead>
		<tbody>
			<?
			$i = 1;
			$toArr = array();
			foreach ($rowsArr as $rows) {
				$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
				if($dealing_marchant_arr[$rows['DEALING_MARCHANT']]){$toArr[$dealing_marchant_arr[$rows['DEALING_MARCHANT']]] = $dealing_marchant_arr[$rows['DEALING_MARCHANT']];}
			?>
				<tr bgcolor="<?= $bgcolor; ?>">
					<td><?= $rows['UPDATE_DATE']; ?></td>
					<td><?= $buyer_library[$rows['BUYER_NAME']]; ?></td>
					<td><?= $rows['JOB_NO']; ?></td>
					<td><?= $rows['PO_NUMBER']; ?></td>
					<td><?= implode(', ',$booking_arr[$rows['PO_ID']]); ?></td>
					<td><?= $user_arr[$rows['UPDATED_BY']]; ?></td>
					<td><?= $task_arr[$rows['TASK_ID']]; ?></td>
					<td><?= $rows['ISSUE_CLOSED']; ?></td>
				</tr>
			<?
				$i++;
			}

			?>
		</tbody>
	</table>
    <?

    $message = ob_get_contents();
    ob_clean();



   if($user_mail_arr[$user_id]){$toArr[$user_mail_arr[$user_id]] = $user_mail_arr[$user_id];}
	$to = implode(',',$toArr);
	//$to="beeresh@logicsoftbd.com,mis.ho@asrotex.com";

    $subject = "TNA Issue Closed Notification";
    $header = mailHeader();

    if ($_REQUEST['isview'] == 1) {
        if ($to) {
            echo 'Mail Item:' . $form_list_for_mail[$mail_item] . '=>' . $to;
        } else {
            echo "This user mail address not found.";
        }
        echo $message;
    } else {
        if ($to != "") {
            echo sendMailMailer($to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port);
            $today = date("d-M-Y h:i:s a",time());
            file_put_contents($file, $today);
        }
    }

}

?>