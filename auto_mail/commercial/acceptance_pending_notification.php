<?
include('../../includes/common.php');
//require_once('../../mailer/class.phpmailer.php');
require('../setting/mail_setting.php');
$current_date = date("d-M-Y",time());
$previous_date = change_date_format(date('Y-m-d', strtotime('-765 day', time())),'','',1);

if(date("D",time())!='Sun'){echo "This mail will be send only day of Sunday";die;}


$lib_company = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

// $lib_buyer = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
 
 $dateCon=" and e.INVOICE_DATE between '$previous_date' and '$current_date'";

$sql ="SELECT 
sum(a.CURRENT_ACCEPTANCE_VALUE) as  CURRENT_ACCEPTANCE_VALUE,b.IMPORTER_ID, b.SUPPLIER_ID,b.lc_value as BTB_LC_VALUE, b.lc_number as BTB_LC_NO,b.LC_DATE,b.TENOR,b.TOLERANCE,e.DOCUMENT_VALUE,e.INVOICE_NO,e.INVOICE_DATE,e.BILL_OF_ENTRY_NO,e.BILL_NO,e.BILL_DATE,e.COMPANY_ACC_DATE,e.BANK_ACC_DATE,
 0 as type
	from COM_IMPORT_INVOICE_MST e,com_import_invoice_dtls a, com_btb_lc_master_details b 
	left join com_btb_export_lc_attachment c on b.id=c.import_mst_id and c.is_lc_sc=0 and c.status_active=1 and c.is_deleted=0 
	left join com_export_lc d on c.lc_sc_id=d.id and c.is_lc_sc=0
	where e.id=a.IMPORT_INVOICE_ID and a.btb_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $dateCon  and b.TENOR>0  
    group by b.IMPORTER_ID, b.SUPPLIER_ID,b.lc_value , b.lc_number,b.LC_DATE,b.TENOR,b.TOLERANCE,e.DOCUMENT_VALUE,e.INVOICE_NO,e.INVOICE_DATE,e.BILL_OF_ENTRY_NO,e.BILL_NO,e.BILL_DATE,e.COMPANY_ACC_DATE,e.BANK_ACC_DATE
    order by b.IMPORTER_ID";
 //echo $sql;die;
 //
   // 
  

$sql_res = sql_select($sql);
$dataArr=array();
$cumiltiveValuArr=array();$btb_lc_total_arr=array();
foreach($sql_res as $rows)
{
    
    $dataArr[]=$rows;
    $cumiltiveValuArr[$rows['BTB_LC_NO']] += $rows['CURRENT_ACCEPTANCE_VALUE'];
    $btb_lc_total_arr[$rows['BTB_LC_NO']] += $rows['DOCUMENT_VALUE'];
}
 
ob_start();

    ?>
        <table border="1" rules="all">
            <thead style="background-color:#ccc;">
                <th>Importer</th>	
                <th>Supplier Name</th>	
                <th>BTB LC Number</th>	
                <th>BTB LC Date</th>	
                <th>BTB LC Value</th>	
                <th>Invoice No.</th> 	
                <th>Date</th>	
                <th>BL/AWB</th>	
                <th>Date</th>	
                <th>Bill Of Entry</th>	
                <th>Date</th>	
                <th>Accpt. Value</th>	
                <th>Date</th>	
                <th>Cum.Accpt. Value</th>	
                <th>Balance Value</th>	
                <th>Tenor Days</th>	
                <th>Pass Days</th>
            </thead>
            <tbody>
                <? 
                $cum_accpt_value_arr=array();
                foreach($dataArr as $row){
                   $btb_lc_total_amount = $btb_lc_total_arr[$row['BTB_LC_NO']];

                    $row['CURR_DATE']=date('d-M-Y',time());
                    $pass_day = datediff( 'd', $row['LC_DATE'],$row['CURR_DATE']);
                    if($pass_day<=45){continue;}
                    
                    $row['TOLERANCE']=($row['TOLERANCE'])?$row['TOLERANCE']:5;
                    $tolerance_val = ($row['TOLERANCE']*$row['BTB_LC_VALUE'])/100;
                    $bal = ($row['BTB_LC_VALUE']-$tolerance_val);
                  if($btb_lc_total_amount>= $bal ){continue;}


                    $row['CURRENT_ACCEPTANCE_VALUE'] = $cumiltiveValuArr[$row['BTB_LC_NO']];

                    $cum_accpt_value_arr[$row['BTB_LC_NO']]+=$row['DOCUMENT_VALUE'];
          
                ?>
              
                <tr>
                    <td><?=$lib_company[$row['IMPORTER_ID']];?></td>
                    <td><?=$supplier_lib[$row['SUPPLIER_ID']];?></td>
                    <td><?=$row['BTB_LC_NO'];?></td>
                    <td><?=change_date_format($row['LC_DATE']);?></td>
                    <td align="right" title="Tolerance val: <?= $tolerance_val;?>"><?=number_format($row['BTB_LC_VALUE'],2);?></td>
                    <td><?=$row['INVOICE_NO'];?></td>
                    <td><?=change_date_format($row['INVOICE_DATE']);?></td>
                    <td><?=$row['BILL_NO'];?></td>
                    <td><?=$row['BILL_DATE'];?></td>
                    <td><?=$row['BILL_OF_ENTRY_NO'];?></td>
                    <td><?=change_date_format($row['COMPANY_ACC_DATE']);?></td>
                    <td align="right"><?=number_format($row['DOCUMENT_VALUE'],2);?></td>
                    <!-- $row['CURRENT_ACCEPTANCE_VALUE'] -->
                    <td><?=change_date_format($row['BANK_ACC_DATE']);?></td>
                    <td align="right"><?=number_format($cum_accpt_value_arr[$row['BTB_LC_NO']],2);//$row['CURRENT_ACCEPTANCE_VALUE']?></td>
                    <td align="right"><?=number_format($row['BTB_LC_VALUE']-$cum_accpt_value_arr[$row['BTB_LC_NO']],2);//$row['CURRENT_ACCEPTANCE_VALUE']?></td>
                    <td align="center"><?=$row['TENOR'];?></td>
                    <td align="center"><?=$pass_day;?></td>
                </tr>
                <?
                }
                ?>
            </tbody>
        </table>

    <style>
        table{ font-size:14px;}
    </style>

    <?

    $htmlBody=ob_get_contents();
    ob_clean();

    $mail_item=109;
    $toArr=array();
    $sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and b.mail_user_setup_id=c.id  and a.IS_DELETED=0 and a.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
    $mail_sql2=sql_select($sql2);
    foreach($mail_sql2 as $row)
    {
        $toArr[$row[csf('email_address')]]=$row[csf('email_address')];
    }
    $to=implode(',',$toArr);

    $subject="Acceptance Pending Auto mail";

    $header=mailHeader();



    if($_REQUEST['isview']==1){
        if($to){
            echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
        }else{
            echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
        }
        echo $htmlBody;
    }
    else{
     if($to!=""){echo sendMailMailer( $to, $subject, $htmlBody, $from_mail );}
    }
    
 

?>





