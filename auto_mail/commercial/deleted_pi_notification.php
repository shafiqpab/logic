<?
include('../../includes/common.php');
include('../setting/mail_setting.php');

extract($_REQUEST);

// $current_date = change_date_format(date("Y-m-d",time()),'','',1);
// $previous_date = change_date_format(date('Y-m-d', strtotime('-1 day', time())),'','',1);


$time_stamp=time();
if($view_date){$time_stamp = strtotime($view_date);}
$previous_date = change_date_format(date('d-M-Y', strtotime('-1 day', $time_stamp)),'','',1);




$lib_supplier = return_library_array( "select id, SUPPLIER_NAME from LIB_SUPPLIER where  status_active=1 and is_deleted=0", "id", "SUPPLIER_NAME");
$lib_user = return_library_array("select id, user_full_name from user_passwd","id","user_full_name");

$lib_buyer = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
$lib_company = return_library_array('SELECT id,company_name FROM lib_company','id','company_name');




$sql = "SELECT a.ID, a.IMPORTER_ID,a.PI_NUMBER, a.PI_DATE,a.SUPPLIER_ID,d.BUYER_NAME as BUYER_ID, a.INSERTED_BY,b.UPDATED_BY,b.UPDATE_DATE,a.ITEM_CATEGORY_ID,b.AMOUNT,b.WORK_ORDER_NO,b.BUYER_STYLE_REF,c.JOB_NO_MST from com_pi_master_details a, COM_PI_ITEM_DETAILS b left join WO_PO_BREAK_DOWN c on b.order_id=c.id and c.STATUS_ACTIVE=1 and c.IS_DELETED=0  left join WO_PO_DETAILS_MASTER d on d.id=c.job_id where a.id=b.pi_id  and b.STATUS_ACTIVE=0 and b.IS_DELETED=1 and b.UPDATE_DATE between '$previous_date' and '$previous_date 11:59:59 PM'";
 //echo $sql;
$sql_res = sql_select($sql);
$dataArr = array();
foreach($sql_res as $row){
    $dataArr[$row['IMPORTER_ID']][] = $row;
}


foreach($lib_company as $company_id => $company_name){

ob_start();
?>
    <table border="1" rules="all">
        <thead>
            <tr>
                <th colspan="14">
                    <strong style="font-size:24px;"><?= $company_name;?></strong><br>
                    Deleted PI Nofication<br>
                    Date:<?= change_date_format($previous_date);?>
                </th>
            </tr>
            <tr bgcolor="#CCC">
                <th>SL</th>
                <th>PI System ID</th>	
                <th>PI Number</th>	
                <th>PI Date</th>	
                <th>Supplier Name</th>	
                <th>Buyer</th>	
                <th>Style</th>	
                <th>Job No</th>	
                <th>WO No</th>	
                <th>Item Category</th>	
                <th>Value</th>	
                <th>Insert By</th>	
                <th>Deleted By</th>	
                <th>Deleted Date & Time</th>
            </tr>
            
        </thead>
        <tbody>
            <?php
            $i=1;
            foreach($dataArr[$company_id] as $row){
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor;?>">
                <td align="center"><?= $i;?> </td>
                <td><?= $row['ID'];?></td>	
                <td><?= $row['PI_NUMBER'];?></td>	
                <td><?= change_date_format($row['PI_DATE']);?></td>	
                <td><?= $lib_supplier[$row['SUPPLIER_ID']];?></td>	
                <td><?= $lib_buyer[$row['BUYER_ID']];?></td>	
                <td><?= $row['BUYER_STYLE_REF'];?></td>	
                <td><?= $row['JOB_NO_MST'];?></td>	
                <td><?= $row['WORK_ORDER_NO'];?></td>	
                <td><?= $item_category[$row['ITEM_CATEGORY_ID']];?></td>	
                <td align="right"><?= number_format($row['AMOUNT'],2);?></td>	
                <td><?= $lib_user[$row['INSERTED_BY']];?></td>	
                <td><?= $lib_user[$row['UPDATED_BY']];?></td>	
                <td><?= date('d-m-Y h:i:s a',strtotime($row['UPDATE_DATE']));?></td>
            </tr>
            <?php
            $i++;
            }

            ?>
        </tbody>
    </table>
 
    <?

    $htmlBody = ob_get_contents();
    ob_clean();
   
 
    $mail_item=124;
    $toArr=array();
    $sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and b.mail_user_setup_id=c.id  and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
    $mail_sql_res=sql_select($sql);
    foreach($mail_sql_res as $row)
    {
        $toArr[$row['EMAIL_ADDRESS']] = $row['EMAIL_ADDRESS'];
    }
    $to=implode(',',$toArr);

    

   
    if($_REQUEST['isview']==1){
        if($to){
            echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
        }else{
            echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item];
        }
        echo $htmlBody;
    }
    else{
        $header=mailHeader();
        $subject="Deleted PI Notification";
        if($to){echo sendMailMailer( $to, $subject, $htmlBody, $from_mail );}
    }

}
        
 

?>





