<?php
    date_default_timezone_set("Asia/Dhaka");
    extract($_REQUEST);
 
    require_once('../includes/common.php');
    require_once('../mailer/class.phpmailer.php');
    //require '../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
    require_once('setting/mail_setting.php');

    $comp_lib = return_library_array("select id, COMPANY_SHORT_NAME as company_name from lib_company where status_active=1 and is_deleted=0 ", "id","company_name");
    $bank_lib = return_library_array("select id, BANK_NAME from LIB_BANK where  status_active=1 and is_deleted=0","id", "BANK_NAME");
    $supplier_lib = return_library_array("select id, SUPPLIER_NAME from LIB_SUPPLIER where  status_active=1 and is_deleted=0","id", "SUPPLIER_NAME");
 
    $current_date = change_date_format(date("Y-M-d H:i:s",time()),'','',1);

    $current_date =($view_date)?$view_date:$current_date;

    $previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
    $where_con	= " and a.LC_DATE between '".$previous_date."' and '".$previous_date."'";
    //$where_con	= " and a.INSERT_DATE between '".$previous_date."' and '".$previous_date." 11:59:59 pm'";

    $sql = "select a.ID,a.IMPORTER_ID,a.BTB_SYSTEM_ID,a.LC_NUMBER,a.APPLICATION_DATE,a.LC_DATE,a.ISSUING_BANK_ID,a.SUPPLIER_ID,a.LC_TYPE_ID, b.IS_LC_SC,b.LC_SC_ID,sum(b.CURRENT_DISTRIBUTION) as LC_VALUE from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.is_deleted=0 and b.status_active=1 $where_con group by a.ID,a.IMPORTER_ID,a.BTB_SYSTEM_ID,a.LC_NUMBER,a.APPLICATION_DATE,a.LC_DATE,a.ISSUING_BANK_ID,a.SUPPLIER_ID,a.LC_TYPE_ID, b.IS_LC_SC,b.LC_SC_ID";
    //echo $sql;
	$data_array=sql_select($sql); 
    $dataArr=array();$lc_sc_id_arr=array();
    foreach($data_array as $row){
        $dataArr[$row['IMPORTER_ID']][$row['ID']]=$row;
        $lc_sc_id_arr[$row["IS_LC_SC"]][$row["LC_SC_ID"]]=$row['LC_SC_ID'];
        //$lcsc_wise_sys_arr[$row['LC_SC_ID']]=$row['ID'];
    }






    $sql_sc="select 1 as TYPE,ID,contract_no as LC_SC_NO,contract_value as VALUE from com_sales_contract where STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($lc_sc_id_arr[1],0,'id')."";
    $sql_lc="select  0 as TYPE,ID,export_lc_no as LC_SC_NO, lc_value as VALUE from com_export_lc where STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($lc_sc_id_arr[0],0,'id')."";
    $sql =$sql_sc.' UNION ALL '.$sql_lc;
   //echo $sql;die;
    $lcscSql=sql_select($sql);
    foreach($lcscSql as $row){
        if($row['TYPE']==1){
            $lc_sc_arr['LC_SC_NO'][$row['ID']][$row['LC_SC_NO']]=$row['LC_SC_NO'];
           // $lc_sc_arr['VALUE'][$lcsc_wise_sys_arr[$row['ID']]][$row['LC_SC_NO']]=$row['VALUE'];
        }
        else if($row['TYPE']==0){
            $lc_sc_arr['LC_SC_NO'][$row['ID']][$row['LC_SC_NO']]=$row['LC_SC_NO'];
            //$lc_sc_arr['VALUE'][$lcsc_wise_sys_arr[$row['ID']]][$row['LC_SC_NO']]=$row['VALUE'];
        }

    }


 



	
 //echo "<pre>";
   // print_r($lc_sc_arr['LC_SC_NO']);die;
  
 
 	//$pi_no=return_field_value("PI_NUMBER","COM_PI_MASTER_DETAILS"," status_active=1 and is_deleted=0 and id=".$row[PI_ID]."");

 
ob_start();		
    ?>
<table border="1" rules="all">
    <tr>
        <th colspan="11">
            <div>BTB Forwarding and LC Open Notification Report on Date: <?=change_date_format($previous_date);?></div>
        </th>
    </tr>
    <tr bgcolor="#CCC">
        <th>SL</th>
        <th>Beneficiary Name</th>
        <th>System ID</th>
        <th>Application Date</th>
        <th>LC Date</th>
        <th>Issuing Bank</th>
        <th>Supplier Name</th>
        <th>BTB L/C No</th>
        <th>BTB L/C Type</th>
        <th>L/C Value</th>
        <th>SC/LC No</th>
    </tr>
    <?	
    foreach($comp_lib as $company_id=>$company_name){	
        ?>
        <?
                $i=1;
                $totalLCVal=0;
                $flag=0;
                foreach($dataArr[$company_id] as $row){
                    $bgcolor=($i%2==0)?"#EEE":"#FFF";
                // $row['LC_VALUE']=array_sum($lc_sc_arr['VALUE'][$row['ID']]);
                ?>
                    <tr bgcolor="<?=$bgcolor;?>">
                        <td><?=$i;?></td>
                        <td><?=$comp_lib[$row['IMPORTER_ID']];?></td>
                        <td><?=$row['BTB_SYSTEM_ID'];?></td>
                        <td align="center"><?=change_date_format($row['APPLICATION_DATE']);?></td>
                        <td align="center"><?=change_date_format($row['LC_DATE']);?></td>
                        <td><?=$bank_lib[$row['ISSUING_BANK_ID']];?></td>
                        <td><?=$supplier_lib[$row['SUPPLIER_ID']];?></td>
                        <td><?=$row['LC_NUMBER'];?></td>
                        <td><?=$lc_type[$row['LC_TYPE_ID']];?></td>
                        <td align="right"><?=number_format($row['LC_VALUE'],2);?></td>
                        <td><?= implode(', ',$lc_sc_arr['LC_SC_NO'][$row['LC_SC_ID']]);?></th>
                    </tr>
                    <?
                $totalLCVal+=$row['LC_VALUE'];
                $i++;$flag=1;
                }
                if( $flag == 1){
                ?>
                <tr align="right">
                    <th colspan="9">Total</th>
                    <th><?=number_format($totalLCVal,2);?></th>
                    <th></th>
                </tr>
        <?	
                }
    }

    ?>
</table>
<?php	
 $message = ob_get_contents();
	ob_clean();


 
	$toMailArr=array();

	$sql = "SELECT c.email_address as MAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=118  and b.mail_user_setup_id=c.id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and a.MAIL_TYPE=1";//and 
   // echo $sql;
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if($row['MAIL']){$toMailArr[$row['MAIL']]=$row['MAIL']; }
	}
 	
	$to=implode(',',$toMailArr);
	$subject = "BTB Forwarding and LC Open Notification";
	$header=mailHeader();
	
	if($_REQUEST['isview']==1){
		$mail_item=118;
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);}
	}



	
 
	
?>