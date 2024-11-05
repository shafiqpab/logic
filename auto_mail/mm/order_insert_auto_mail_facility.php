<?php
require_once('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
require_once('../setting/mail_setting.php');

$buyer_short_name_library = return_library_array("SELECT id, short_name FROM lib_buyer WHERE status_active=1", 'id', 'short_name');
$company_library = return_library_array("SELECT id, company_short_name FROM lib_company", "id", "company_short_name");
$team_name_library = return_library_array("SELECT id, team_name FROM lib_marketing_team", "id", "team_name");
$team_member_library = return_library_array("SELECT id, team_member_name FROM lib_mkt_team_member_info", "id", "team_member_name");
$buyer_name_library = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
//print_r($team_member_library);die;
//LIB_MKT_TEAM_MEMBER_INFO

$time = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = change_date_format(date("Y-m-d",strtotime(add_time(date("H:i:s",$time),0))),'','',1);
$previous_date = change_date_format(date('Y-m-d', strtotime('-1 day', $time)),'','',1);
// wo_po_details_master
// wo_po_break_down
// wo_po_color_size_breakdown

// echo "===============";die;
?>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
        }
    </style>
    <?php
    ob_start();	
    foreach($company_library as $company_id=>$company_name)
    {
        // $previous_date1= "01-Dec-2023";
        // $previous_date2= "21-Dec-2023";
        // $date_range =  "and a.insert_date between '$previous_date' and '$previous_date'";
        // $date_range= " and b.insert_date between '".$previous_date." 11:59:59 AM ' and '".$previous_date." 11:59:59 PM' ";
        // $date_range .=" and b.insert_date between '".$previous_date."' and '".$previous_date." 23:59:59'";

        $date_range= " and b.insert_date between '".$previous_date." 11:59:59 AM ' and '".$previous_date." 11:59:59 PM' ";
 
        $sql = "SELECT a.id as po_dlts_mst_id, a.job_no, a.company_name, a.working_company_id, a.buyer_name, a.style_ref_no, a.product_dept, a.team_leader, a.factory_marchant,a.dealing_marchant, a.set_smv, a.insert_date, a.order_repeat_no, a.gmts_item_id, b.id as po_break_mst_id, b.job_no_mst, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.shipment_date, b.pack_handover_date, b.is_confirmed FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $date_range and a.company_name='$company_id' order by a.id";
        //echo $sql;
        $sql_res = sql_select($sql);
        ?>
        <?php
        if(count($sql_res)>0){
        ?>
        <table width="1100" >
            <tr class="form_caption">
                <h2 align="center">Daily Order Insert History Report (Date: <?= $previous_date;?>)</h2>
                <p align="center">Comapny Name: <?= $company_name;?><p>
            </tr>
        </table>
        <table style="width:90%" cellpadding="0" cellspacing="0" border="1">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Lc Company</th>
                    <th>Manu. Company</th>
                    <th>Team Name</th>
                    <th>Team Member</th>
                    <th>Job No</th>
                    <th>Buyer</th>
                    <th>Style Ref.</th>
                    <th>PO No</th>
                    <th>Product Dept.</th>
                    <th>Item</th>
                    <th>SMV</th>
                    <th>Order Qty(Pcs)</th>
                    <th>Minute</th>
                    <th>Po Insert Date</th>
                    <th>Po Receive Date</th>
                    <th>PHD. Date</th>
                    <th>Ship Date</th>
                    <th>Lead Time</th>
                    <th>Order Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                $total_quantity = 0;
                $total_minutes = 0;
                foreach($sql_res as $row){
                    $date1 = strtotime($row['PO_RECEIVED_DATE']);
                    $date2 = strtotime($row['SHIPMENT_DATE']);
                    $diff = $date2 - $date1;
                    $total_days = floor($diff / (60 * 60 * 24));
                ?>
                <tr>  
                    <td><?= $i;?></td>
                    <td><?= $company_library[$row['COMPANY_NAME']];?></td>
                    <td><?= $company_library[$row['WORKING_COMPANY_ID']];?></td>
                    <td><?= $team_name_library[$row['TEAM_LEADER']];?></td>
                    <td><?= $team_member_library[$row['DEALING_MARCHANT']];?></td>
                    <td><?= $row['JOB_NO'];?></td>
                    <td><?= $buyer_name_library[$row['BUYER_NAME']];?></td>
                    <td><?= $row['STYLE_REF_NO'];?></td>
                    <td><?= $row['PO_NUMBER'];?></td>
                    <td><?= $product_dept[$row['PRODUCT_DEPT']];?></td>
                    <td><?= $garments_item[$row['GMTS_ITEM_ID']];?></td>
                    <td><?= $row['SET_SMV'];?></td>
                    <td><?= $row['PO_QUANTITY'];?></td>
                    <td><?= $row['SET_SMV']*$row['PO_QUANTITY'];?></td>
                    <td><?= $row['INSERT_DATE'];?></td>
                    <td><?= $row['PO_RECEIVED_DATE'];?></td>
                    <td><?= $row['PACK_HANDOVER_DATE'];?></td>
                    <td><?= $row['SHIPMENT_DATE'];?></td>
                    <td><?= $total_days;?></td>
                    <td><?= $order_status[$row['IS_CONFIRMED']];?>/<?php if($row['ORDER_REPEAT_NO']!=0) echo 'Repeat'; else{ echo 'New'; };?></td>
                </tr>
                <?php
                $total_quantity +=$row['PO_QUANTITY'];
                $total_minutes +=$row['SET_SMV']*$row['PO_QUANTITY'];
                $i++;
                }
                ?>           
            </tbody>
            <tfoot>
                <tr>  
                    <th colspan="12" align="right">Total</th>
                    <th><?= $total_quantity;?></th> 
                    <th><?= $total_minutes;?></th>
                </tr>
            </tfoot>
        </table>
        <?php
        }
    }

    $message = ob_get_contents();
    ob_clean();

	$mail_item = 144;
	$to='';
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and b.mail_user_setup_id=c.id and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql2 = sql_select($sql2, '', '', '', $con);
	foreach($mail_sql2 as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
 
 	// $to='muktobani@gmail.com';
    // echo $to;die;
	$subject = "Daily Order Insert Report";
	$header = mailHeader();
    if($_REQUEST['isview']==1){
        if($to){
            echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
        }else{
            echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
        }
        echo  $message;
    }
    else{
        if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
    }
?>