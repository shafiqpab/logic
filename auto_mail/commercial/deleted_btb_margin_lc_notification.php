<?
include('../../includes/common.php');
include('../setting/mail_setting.php');
extract($_REQUEST);


    $time_stamp=time();
    if($view_date){$time_stamp = strtotime($view_date);}
    $previous_date = change_date_format(date('d-M-Y', strtotime('-1 day', $time_stamp)),'','',1);



    $lib_supplier = return_library_array( "select id, SUPPLIER_NAME from LIB_SUPPLIER where  status_active=1 and is_deleted=0", "id", "SUPPLIER_NAME");
    $lib_user = return_library_array("select id, user_full_name from user_passwd","id","user_full_name");

    $lib_buyer = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
    $lib_company = return_library_array('SELECT id,company_name FROM lib_company','id','company_name');


    $sql = "select a.BTB_SYSTEM_ID,a.IMPORTER_ID,a.LC_TYPE_ID,a.SUPPLIER_ID,a.LC_NUMBER,a.PI_ID,a.PI_VALUE,a.INSERT_DATE,a.LAST_SHIPMENT_DATE, a.INSERTED_BY,a.UPDATED_BY,a.UPDATE_DATE 
    from COM_BTB_LC_MASTER_DETAILS a  where a.STATUS_ACTIVE=0 and a.IS_DELETED=1 and a.UPDATE_DATE between '$previous_date' and '$previous_date 11:59:59 PM' ";
    //echo $sql;die;
    $sql_res = sql_select($sql);
    $dataArr = array();
    $pi_id_arr = array();
    foreach($sql_res as $row){
        $dataArr[$row['IMPORTER_ID']][] = $row;
        $pi_id_arr[] = $row['PI_ID'];
    }

    $pi_id_arr = array_unique(explode(',',implode(',',$pi_id_arr)));

    $pi_sql = "select b.ID,b.PI_NUMBER from com_pi_master_details b where b.STATUS_ACTIVE=1 and b.IS_DELETED=0 ".where_con_using_array($pi_id_arr,0,'b.id')."";
    //echo $pi_sql;
    $pi_sql_res = sql_select($pi_sql);
    $piDataArr = array();
    foreach($pi_sql_res as $row){
        $piDataArr[$row['ID']] = $row['PI_NUMBER'];
    }

 

//$lib_company=[1=>1];
foreach($lib_company as $company_id => $company_name){

ob_start();
?>
    <table border="1" rules="all">
        <thead>
            <tr>
                <th colspan="12">
                    <strong style="font-size:24px;"><?= $company_name;?></strong><br>
                    Deleted BTB Margin LC Notification<br>
                    Date:<?= change_date_format($previous_date);?>
                </th>
            </tr>
            <tr bgcolor="#CCC">
                <th>SL</th>
                <th>System ID</th>	
                <th>L/C Type</th>	
                <th>Supplier Name</th>	
                <th>L/C Number</th>	
                <th>PI Number</th>	
                <th>PI Value</th>	
                <th>L/C Opening Date And Time</th>		
                <th>Shipment Date</th>	
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

                $tempPiArr=array();
                foreach (explode(',',$row['PI_ID']) as  $PI_ID) {
                    $tempPiArr[$PI_ID]=$piDataArr[$PI_ID];
                }
                


            ?>
            <tr bgcolor="<? echo $bgcolor;?>">
                <td align="center"><?= $i;?> </td>
                <td><?= $row['BTB_SYSTEM_ID'];?></td>	
                <td><?= $lc_type[$row['LC_TYPE_ID']];?></td>	
                <td><?= $lib_supplier[$row['SUPPLIER_ID']];?></td>	
                <td><?= $row['LC_NUMBER'];?></td>	
                <td><?= implode(', ',$tempPiArr);?></td>	
                <td align="right"><?= $row['PI_VALUE'];?></td>	
                <td align="center"><?= date('d-m-Y h:i:s a',strtotime($row['INSERT_DATE']));?></td>	
                <td align="center"><?= change_date_format($row['LAST_SHIPMENT_DATE']);?></td>	
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
   
 
    $mail_item=126;
    $toArr=array();
    $sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and b.mail_user_setup_id=c.id  and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
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
        $subject="Deleted BTB Margin LC Notification";
        if($to){echo sendMailMailer( $to, $subject, $htmlBody, $from_mail );}
    }

}
        
 

?>





