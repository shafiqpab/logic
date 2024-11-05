<?
include('../../includes/common.php');
include('../setting/mail_setting.php');
extract($_REQUEST);


$time_stamp=time();
if($view_date){$time_stamp = strtotime($view_date);}
$previous_date = change_date_format(date('d-M-Y', strtotime('-1 day', $time_stamp)),'','',1);

// $user_maill_arr=return_library_array("select id,USER_EMAIL from USER_PASSWD","id","USER_EMAIL");
$lib_com_arr=return_library_array("select id, COMPANY_NAME from LIB_COMPANY","id", "COMPANY_NAME");
$supplier_arr = return_library_array("select id,SUPPLIER_NAME from LIB_SUPPLIER where status_active=1 and is_deleted=0","id","SUPPLIER_NAME");

$piFor_array=array(1=>"BTB", 2=>"Margin LC", 3=>"Fund Buildup", 4=>"TT/Pay Order", 5=>"FTT", 6=>"FDD");
$priority_array=array(1=>"Normal", 2=>"Urgent", 3=>"Critical");
 

ob_start();
foreach ($lib_com_arr as $company_id => $company_name)
{

    $sql="SELECT a.NET_TOTAL_AMOUNT,a.IMPORTER_ID,A.ID,a.PI_DATE,a.ITEM_CATEGORY_ID,a.PI_NUMBER,a.SUPPLIER_ID,a.INSERTED_BY,a.INTERNAL_FILE_NO,a.LC_SC_NO,a.PAY_TERM,a.PI_FOR,a.REMARKS,a.PRIORITY_ID,a.LC_REQ_DATE  FROM com_pi_master_details a,  com_pi_item_details b WHERE a.id=b.pi_id AND a.IMPORTER_ID='$company_id' AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 and a.APPROVED=1 and a.APPROVED_DATE between '$previous_date' and '$previous_date 11:59:59 PM' GROUP BY a.NET_TOTAL_AMOUNT,A.ID,a.IMPORTER_ID,a.PI_DATE,a.ITEM_CATEGORY_ID,a.PI_NUMBER,a.SUPPLIER_ID,a.INSERTED_BY,a.INTERNAL_FILE_NO,a.LC_SC_NO,a.PAY_TERM,a.PI_FOR,a.REMARKS,a.PRIORITY_ID,a.LC_REQ_DATE ";
    //echo $sql;die;
    $sql_dtls=sql_select($sql);

 ?>
    <table width="1300"  cellspacing="0" border="0">
        <tr>
            <td colspan="18" align="center">
                <strong>Company: <?= $company_name; ?></strong>
            </td>
        </tr>
        <tr>
            <td colspan="18" align="center">
                <b style="font-size:14px;">PI Approval Notification :<?= date("d-m-Y",strtotime($previous_date));  ?></b>
            </td>
        </tr>
    </table>
    <b>Dear Concerned,</b><br/>	
    Below PI has been approved, Please check.<br/>
    <table align="center">
        <tr>
            <th>
                Approved PI List<br>
                Date:<?= $previous_date;?>
            </th>
        </tr>
    </table>
    <table rules="all" border="1">
        <tr bgcolor="#CCCCCC">
            <td>SL</td>
            <td>Company</td>
            <td>System ID</td>
            <td>Item Category</td>
            <td>PI Receive Date</td>
            <td>PI No</td>
            <td>PI Value</td>
            <td>Supplier</td>
            <td>Internal File No</td>
            <td>LC/SC</td>
            <td>Pay Term</td>
            <td>Pi For</td>
            <td>Priority</td>
            <td>LC Required Date</td>
            <td>Remarks</td>
        </tr>
        <?php 
        $i=1;
        $company_arr=array();
        foreach($sql_dtls as $row){
            $company_arr[$row['IMPORTER_ID']]=$row['IMPORTER_ID'];

            ?>
            <tr>
                <td><?= $i;?></td>
                <td><?= $lib_com_arr[$row['IMPORTER_ID']];?></td>
                <td><?= $row['ID'];?></td>
                <td><?= $item_category[$row['ITEM_CATEGORY_ID']];?></td>
                <td><?= change_date_format($row['PI_DATE']);?></td>
                <td><?= $row['PI_NUMBER']?></td>
                <td><?= number_format($row['NET_TOTAL_AMOUNT'],4);?></td>
                <td><?= $supplier_arr[$row['SUPPLIER_ID']];?></td>
                <td><?= $row['INTERNAL_FILE_NO'];?></td>
                <td><?= $row['LC_SC_NO'];?></td>
                <td><?= $pay_term[$row['PAY_TERM']];?></td>
                <td><?= $piFor_array[$row['PI_FOR']];?></td>
                <td><?= $priority_array[$row['PRIORITY_ID']];?></td>
                <td align="center"><?= change_date_format($row['LC_REQ_DATE']);?></td>
                <td><?= $row['REMARKS'];?></td>
            </tr>
        <?php
        $i++;
        }
        ?>
    </table>
    <?php

    $htmlBody = ob_get_contents();
    ob_clean();

    $toArr = array();

    $mail_item = 130;
    $sql = "SELECT a.COMPANY_ID, c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and b.mail_user_setup_id=c.id   and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.company_id='$company_id' and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and a.MAIL_TEMPLATE<>1";
    // echo $sql;die;
    //and a.company_id in(".implode(',',$company_arr).") 
    $mail_sql=sql_select($sql);
    foreach($mail_sql as $rows)
    {
        if($rows['EMAIL_ADDRESS']){$toArr[$rows['EMAIL_ADDRESS']]=$rows['EMAIL_ADDRESS']; }
    }

    $to = implode(',',$toArr);

    $header=mailHeader();
    $subject = "PI Approval";


    if($_REQUEST['isview']==1){
        if($to){
            echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
        }else{
            echo "Mail address not set [$company_name] [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
        }
        echo $htmlBody;
    }
    else{
        if($to!=""){echo sendMailMailer( $to, $subject, $htmlBody, $from_mail );}
    }
}