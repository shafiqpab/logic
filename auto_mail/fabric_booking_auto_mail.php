<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

$company_library=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$buyer_library=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
$supplier_library = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
$user_library=return_library_array("select id,user_full_name from user_passwd where valid=1","id","user_full_name");


$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();


$tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d",$strtotime),1))),'','',1);
$day_after_tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d",$strtotime),2))),'','',1);

$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d",time()),0))),'','',1);
$current_date = ($_REQUEST['view_date']) ?$_REQUEST['view_date'] : $current_date;
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 


$booking_date = "and b.insert_date between '$prev_date' and '$prev_date 11:59:59 pm'";
$booking_sql = "SELECT a.INSERTED_BY,a.COMPANY_ID,a.BOOKING_NO, a.BOOKING_DATE,a.DELIVERY_DATE,a.SUPPLIER_ID, a.BUYER_ID, c.JOB_NO,c.STYLE_REF_NO,a.ITEM_CATEGORY, a.FABRIC_SOURCE,a.PAY_MODE,d.GROUPING from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c,wo_po_break_down d where a.booking_no=b.booking_no and b.job_no=c.job_no  and c.job_no=d.job_no_mst  and a.item_category=2 and a.fabric_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 $booking_date group by a.INSERTED_BY,a.COMPANY_ID,a.BOOKING_NO, a.BOOKING_DATE,a.DELIVERY_DATE,a.SUPPLIER_ID, a.BUYER_ID, c.JOB_NO,c.STYLE_REF_NO,a.ITEM_CATEGORY, a.FABRIC_SOURCE,a.PAY_MODE,d.GROUPING ";
$booking_sql_res = sql_select($booking_sql);
$dataArr=array();
foreach ($booking_sql_res as $rows) {
    $dataArr[$rows['COMPANY_ID']][]=$rows;

}


foreach($company_library as $compid=>$compname)
{
    ob_start();		

	?>

    <table border="1" rules="all">
        <tr>
            <td colspan="13">
                <strong><?=$compname;?></strong>  Date:<?=$prev_date;?>
            </td>
        </tr>
        <tr bgcolor="#CCC">
            <th>SL</th>
            <th>Booking No</th>
            <th>Booking Date</th>
            <th>Delivery Date</th>
            <th>Supplier Name</th>
            <th>Buyer Name</th>
            <th>Job No</th>
            <th>Style Ref.</th>
            <th>Internal Ref</th>
            <th>Item Category</th>
            <th>Fabric Source</th>
            <th>Pay Mode</th>
            <th>Inserted By</th>
        </tr>
    <?
    $i=1;
    foreach ($dataArr[$compid] as $rows) {
        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
    ?>
        <tr bgcolor="<?=$bgcolor;?>">
            <td><?=$i;?></td>
            <td><?=$rows['BOOKING_NO'];?></td>
            <td><?=change_date_format($rows['BOOKING_DATE']);?></td>
            <td><?=change_date_format($rows['DELIVERY_DATE']);?></td>
            <td><?=$supplier_library[$rows['SUPPLIER_ID']];?></td>
            <td><?=$buyer_library[$rows['BUYER_ID']];?></td>
            <td><?=$rows['JOB_NO'];?></td>
            <td><?= $rows["STYLE_REF_NO"]; ?></td>
            <td><?= $rows["GROUPING"]; ?></td>
            <td><?= $item_category[$rows["ITEM_CATEGORY"]];?></td>
            <td><?= $fabric_source[$rows["FABRIC_SOURCE"]];?></td>
            <td><?= $pay_mode[$rows["PAY_MODE"]];?></td>
            <td><?= $user_library[$rows["INSERTED_BY"]];?></td>
        </tr>
        <?
        $i++;
        
    }
    ?>
     </table>
	
<?		    
 
	$message=ob_get_contents();
	ob_clean();
    $mailArr=array();
	$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=110 and b.mail_user_setup_id=c.id and a.COMPANY_ID=$compid  and   A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 group by c.email_address";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		$mailArr[$row['EMAIL_ADDRESS']]=$row['EMAIL_ADDRESS']; 
	}
	$to=implode(',',$mailArr);
	
    

	
	if($_REQUEST['isview']==1){
        $mail_item=110;
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
        $header=mailHeader();
        $subject="Daily Fabric Booking Auto Mail";
		if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);

  	}


	//echo $message;

}
	
	





?> 