<?
date_default_timezone_set("Asia/Dhaka");

require_once('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();

$current_date = change_date_format(date("Y-m-d H:i:s", strtotime(add_time(date("H:i:s", $strtotime), 0))), '', '', 1);
// if ($_REQUEST['view_date']) {
//     $current_date = change_date_format(date("Y-m-d H:i:s", strtotime($_REQUEST['view_date'])), '', '', 1);
// }

$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('100 day', strtotime($current_date))), '', '', 1);

$date_cond    = " and EXPIRY_DATE between '" . $current_date . "' and '" . $previous_date . "'";
$second_conn_db_expare_sql = "SELECT USERNAME, ACCOUNT_STATUS, LOCK_DATE, EXPIRY_DATE FROM dba_users where EXPIRY_DATE is not null and ACCOUNT_STATUS='OPEN' $date_cond";
$second_conn_db_expare_sql_res = sql_select($second_conn_db_expare_sql);

ob_start();
?>
<table border="1" rules="all">
    <tr bgcolor="#CCC">
        <th>SL</th>
        <th>USER NAME</th>
        <th>STATUS</th>
        <th>EXPIRY DATE</th>
        <th>DAY LEFT</th>
    </tr>
    <?
    $i = 1;
    $flag=0;
    foreach ($second_conn_db_expare_sql_res as $row) {
        $from = date_create(date("Y-d-m", time()));
        $to = date_create($row['EXPIRY_DATE']);
        $diff = date_diff($from, $to);
        $diffDays = $diff->format("%R%a");
        $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
    ?>
        <tr bgcolor="<?= $bgcolor; ?>">
            <td><?= $i; ?></td>
            <td><?= $row['USERNAME']; ?></td>
            <td align="center"><?= $row['ACCOUNT_STATUS']; ?></td>
            <td align="center"><?= $row['EXPIRY_DATE']; ?></td>
            <td align="center"><?= $diffDays; ?></td>
        </tr>
    <?
        $flag=1;
        $i++;
    }
    ?>
</table>

<?
$emailBody = ob_get_contents();
ob_clean();

$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=117 and b.mail_user_setup_id=c.id and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
$mail_sql = sql_select($sql);
$mailArr = array();
foreach ($mail_sql as $row) {
    $mailArr[$row['EMAIL_ADDRESS']] = $row['EMAIL_ADDRESS'];
}
$to = implode(',', $mailArr);


if ($_REQUEST['isview'] == 1) {
    $mail_item = 117;
    if ($to) {
        echo 'Mail Item:' . $form_list_for_mail[$mail_item] . '=>' . $to;
    } else {
        echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>" . $form_list_for_mail[$mail_item] . "</b>]<br>";
    }
    echo $emailBody;
} else {
    $subject = "DB Expiry Notification";
    $header = mailHeader();
    if($flag==1){echo sendMailMailer($to, $subject, $emailBody,'');}
}


?>