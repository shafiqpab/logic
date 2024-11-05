<?
include('../../includes/common.php');
// include('../../mailer/class.phpmailer.php');
// include('../setting/mail_setting.php');



$current_date = change_date_format(date("Y-m-d",time()),'','',1);
$previous_date = change_date_format(date('Y-m-d', strtotime('-1 day', time())),'','',1);
 
//$previous_date='31-Dec-2022';

$lib_company = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$lib_buyer = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
 
$team_leader_sql ="select ID,TEAM_LEADER_NAME,TEAM_LEADER_EMAIL from lib_marketing_team where  status_active =1 and is_deleted=0";
$team_leader_sql_res = sql_select($team_leader_sql);
foreach($team_leader_sql_res as $rows)
{
    $team_leader_data_arr['NAME'][$rows['ID']]=$rows['TEAM_LEADER_NAME'];
    $team_leader_data_arr['EMAIL'][$rows['ID']]=$rows['TEAM_LEADER_EMAIL'];
}

 
$sql ="select a.id as REALIZATION_ID,a.RECEIVED_DATE,a.INSERT_DATE,b.COMPANY_ID,b.BUYER_ID,b.BANK_REF_NO, d.BANK_NAME,c.ALL_ORDER_NO,e.INVOICE_NO,  e.NET_INVO_VALUE as INVOICE_VALUE
from com_export_proceed_realization a, com_export_doc_submission_mst b , com_export_doc_submission_invo c,LIB_BANK d,com_export_invoice_ship_mst e
where a.invoice_bill_id=b.id and b.id=c.doc_submission_mst_id and d.id=b.LIEN_BANK and e.id=c.invoice_id and b.entry_form=40 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
 


$sql_res = sql_select($sql);
$realization_id_arr=array();
foreach($sql_res as $rows)
{
    $realization_id_arr[$rows['REALIZATION_ID']]=$rows['REALIZATION_ID'];
    $po_id_arr[]=$rows['ALL_ORDER_NO'];
}
$po_id_arr = explode(',',implode(',',$po_id_arr));

//........................................................
$style_sql ="select b.id as PO_ID,a.STYLE_REF_NO,a.TEAM_LEADER from wo_po_break_down b,wo_po_details_master a where b.job_no_mst=a.job_no and a.status_active=1 and b.status_active=1 ".where_con_using_array($po_id_arr,0,'b.id')."";
$style_sql_res=sql_select($style_sql);
foreach($style_sql_res as $rows)
{
    $styel_data_arr['STYLE_REF_NO'][$rows['PO_ID']]=$rows['STYLE_REF_NO'];
    $styel_data_arr['TEAM_LEADER'][$rows['PO_ID']]= $team_leader_data_arr['NAME'][$rows['TEAM_LEADER']];
    $po_wise_team_leader_mail_arr[$rows['PO_ID']]=$team_leader_data_arr['EMAIL'][$rows['TEAM_LEADER']];
}



//.........................................................
$export_proceed_rlzn_sql = "select MST_ID,TYPE, ACCOUNT_HEAD, AC_LOAN_NO, DOCUMENT_CURRENCY, CONVERSION_RATE, DOMESTIC_CURRENCY, DISTRIBUTE_PERCENT from com_export_proceed_rlzn_dtls where status_active=1 and IS_DELETED=0 ".where_con_using_array($realization_id_arr,0,'mst_id')."";
//echo $export_proceed_rlzn_sql;die;
$export_proceed_rlzn_sql_res = sql_select($export_proceed_rlzn_sql);
$export_proceed_rlzn_data_arr=array();
foreach($export_proceed_rlzn_sql_res as $rows)
{
    $export_proceed_rlzn_data_arr[$rows['TYPE']][$rows['MST_ID']]+=$rows['DOCUMENT_CURRENCY'];
}

$htmlBoayArr=array();


?>


    <?
    foreach($sql_res as $rows)
    { 
        $tempStyle=array();$tempTeamLeader=array();$tempEmailArr=array();
        foreach(explode(',',$rows['ALL_ORDER_NO']) as $po_id){

            $tempStyle[$styel_data_arr['STYLE_REF_NO'][$po_id]]=$styel_data_arr['STYLE_REF_NO'][$po_id];
            $tempTeamLeader[$styel_data_arr['TEAM_LEADER'][$po_id]]=$styel_data_arr['TEAM_LEADER'][$po_id];
            $tempEmailArr[$po_wise_team_leader_mail_arr[$po_id]]=$po_wise_team_leader_mail_arr[$po_id];
        }

        $toMail = implode(',',$tempEmailArr);

 

    ob_start();
    ?>
        <table>
            <tr><thead><th colspan="3" align="center">
                <strong style="font-size:23 ;">Proceed Realization</strong> <br>
                Date: <?=change_date_format($previous_date);?>
            </th></thead></tr>     
                
                <tr><td><b>LC Company Name</b></td><td>:</td><td><?=$lib_company[$rows['COMPANY_ID']];?></td></tr>
                <tr><td><b>Buyers Name</b></td><td>:</td><td><?=$lib_buyer[$rows['BUYER_ID']];?></td></tr>
                <tr><td><b>Bank</b></td><td>:</td><td><?=$rows['BANK_NAME'];?></td></tr>
                <tr><td><b>Invoice Number</b></td><td>:</td><td><?= $rows['INVOICE_NO'];?></td></tr>
                <tr><td><b>Style Ref.</b></td><td>:</td><td><?=implode(',',$tempStyle);?></td></tr>
                <tr><td><b>Team Leader</b></td><td>:</td><td><?=implode(',',$tempTeamLeader);?></td></tr>
                <tr><td><b>Invoice Value [$]</b></td><td>:</td><td><?=$rows['INVOICE_VALUE'];?></td></tr>
                <tr><td><b>Realized Value</b></td><td>:</td><td><?=$export_proceed_rlzn_data_arr[1][$rows['REALIZATION_ID']];?></td></tr>
                <tr><td><b>Short Realized</b></td><td>:</td><td><?=$export_proceed_rlzn_data_arr[0][$rows['REALIZATION_ID']];?></td></tr>
                <tr><td><b>Bill No.</b></td><td>:</td><td><?=$rows['BANK_REF_NO'];?></td></tr>
                <tr><td><b>Realized Date</b></td><td>:</td><td><?=change_date_format($rows['RECEIVED_DATE']);?></td></tr>
                <tr><td><b>Insert Date</b></td><td>:</td><td><?=change_date_format($rows['INSERT_DATE']);?></td></tr>
        </table>
    <?
    $htmlBoayArr[$toMail]=ob_get_contents();
    ob_clean();
    }
   




    $mail_item=107;
    $toArr=array();
    $sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and b.mail_user_setup_id=c.id  and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
    $mail_sql2=sql_select($sql2);
    foreach($mail_sql2 as $row)
    {
        $toArr[$row[csf('email_address')]]=$row[csf('email_address')];
    }
    $to=implode(',',$toArr);

    $subject="Export Proceed Realization Auto Mail";

   
 

    foreach($htmlBoayArr as $to_mail=>$htmlBody){
        $to =($seletedMail)?($to_mail.','.$seletedMail):$to_mail;

        if($_REQUEST['isview']==1){
            if($to){
                echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
            }else{
                echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
            }
            echo $htmlBody;
        }
        else{
            $header=mailHeader();
            echo sendMailMailer( $to, $subject, $htmlBody, $from_mail );
        }
        
    }

?>





