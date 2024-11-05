<?php

date_default_timezone_set("Asia/Dhaka");
//require_once('../../mailer/class.phpmailer.php');
require '../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
require('../../includes/common.php');
require('../setting/mail_setting.php');
ob_start();

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

  $current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);	
	$next_date = change_date_format(date('Y-m-d H:i:s', strtotime('+1 day', strtotime($current_date))),'','',1); 	
	$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 	
	$date_cond	=" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";

	


// $sql="select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.style_description,a.order_uom,a.set_break_down,a.gmts_item_id,a.total_set_qnty, sum(b.po_quantity) as po_quantity   from wo_po_details_master a, wo_po_break_down b where  a.job_no ='".$data."' and a.job_no=b.job_no_mst $gro_field";
foreach($company_arr as $company_id=>$company_name){
  $sql= "SELECT a.id,a.shipment_date,a.po_number, sum(a.po_quantity) as po_quantity,SUM ( b.inspection_qnty ) as insp_qty,b.inspection_status,b.ins_reason,b.comments,b.inspection_date,b.inspected_by,c.job_no,c.company_name,c.buyer_name,c.style_ref_no,c.style_description,c.gmts_item_id from wo_po_details_master c,wo_po_break_down a left join pro_buyer_inspection b on  a.id=b.po_break_down_id and b.status_active=1  where  c.id=a.job_id and a.status_active=1  and a.is_deleted=0 AND c.company_name =$company_id and to_date(to_char(b.inspection_date, 'DD-MON-YYYY')) BETWEEN '$previous_date' AND '$previous_date'   group by  a.id, a.po_number, a.shipment_date, b.inspection_date,b.inspection_status,b.ins_reason,b.comments,b.inspected_by, c.job_no, c.company_name, c.buyer_name, c.style_ref_no, c.style_description, c.gmts_item_id ORDER BY a.po_number ";

  //echo $sql;die();



  $company_library_arr=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0 and CORE_BUSINESS=1 ","id","company_name");

  $buyer_library_arr=return_library_array("select id,short_name from lib_buyer","id","short_name");
  $color_arr=return_library_array("select id,color_name from lib_color","id","color_name");
  $company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");

  $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

  $dataArr=sql_select($sql);

 ?>


  <!DOCTYPE html>
  <html>



  <body>

  <style>

  td {
    /* font-size: 12px; */
    height: 100px; 
    text-align: center;
    word-wrap: break-word;

  }
 th{

  /* font-size: 12px; */
 } 




 </style>

<h2 style="text-align:center; line-height: 1; margin-bottom: 0;"><? echo $company_arr[$company_id]; ?></h2>
<h2 style="text-align:center; font-size: 14px; line-height: 0;"><?php echo "Date: " . date('d-m-Y', strtotime($previous_date)); ?></h2>

 <table border="1" rules="all" >

      
  <tr>
    <th>SL. No</th>
    <th>Job Number</th>
    <th>Buyer Name</th>
    <th>Style Name</th>
    <th>Order No</th>
    <th>Item Name</th>
    <th>Ship Date</th>
    <th>Order Qty</th>
    <th>Inspec. Qty</th>
    <th>Passed Qty</th>
    <th>Yet to Inspect</th>
    <th>Inspec. Status</th>
    <th>Inspection Date</th>
    <th>Fail Count</th>
    <th>Fail Reason</th>
    <th>Inspection By</th>
    <th>Comments</th>
   
  </tr>

  <?
    $i=1;
    $total_inspect=0;
     $total_passed=0;
     $total_yet_to=0;
			  
    foreach ($dataArr as $row) 
    {  
  ?>
  <tr>
    <?
    $yet_to_inspect=$row[csf('po_quantity')]-$row[csf('insp_qty')];
   // $passed_qty=$row[csf('insp_qty')]- $yet_to_inspect;
    //echo $passed_qty;die();
    ?>
    <td><? echo $i;?></td>
    <td><? echo $row[csf('job_no')];?></td>
    <td><? echo $buyer_library_arr[$row[csf("buyer_name")]]; ?></td>
    <td><? echo $row[csf('style_ref_no')];?></td>
    <td><? echo $row[csf('po_number')];?></td>
    <td><? echo $garments_item[$row[csf('gmts_item_id')]];?></td>
    <td><? echo change_date_format($row[csf('shipment_date')]);?></td>
    <td ><? echo $row[csf('po_quantity')];?></td>
    <td><? echo $row[csf('insp_qty')];?></td>
    <td><? echo $row[csf('insp_qty')];?></td>
    <td> <? echo $yet_to_inspect?></td>
    <td><? echo $inspection_status[$row[csf('inspection_status')]];?></td>
    <td><? echo change_date_format($row[csf('inspection_date')]);?></td>
    <td><?if($row[csf('inspection_status')]==1){echo 0;} else echo 1;?></td>
    <td><? echo $row[csf('ins_reason')];?></td>
    <td><? echo $inspected_by_arr[$row[csf('inspected_by')]];?></td>
    <td><? echo $row[csf('comments')];?></td>
    
  </tr>
  <?
   $i++;
   $total_inspect+=$row[csf('insp_qty')];
   $tot_po+=$row[csf('po_quantity')];
   $total_passed+=$row[csf('insp_qty')];
   $yet_to_inspect+= $yet_to_inspect;
   $tot_insp_qty=$tot_po-$total_inspect;

   }
     // $i++;
  ?>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>Total:</td>
    <td></td>
    <td><?= number_format($total_inspect,2);?></td>
    <td><?= number_format($total_inspect,2);?></td>
    <td><?= number_format($tot_insp_qty,2);?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>

  </tr>

  
 </table>

    <?
    $message=ob_get_contents();
    ob_clean();

    $sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=121 and b.mail_user_setup_id=c.id and a.company_id=$company_id AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";

     //echo $sql;die();
    $mail_sql=sql_select($sql);
    foreach($mail_sql as $row)
    {
      $toArr[$row[csf('email_address')]]=$row[csf('email_address')]; 
    }

    $to=implode(',',$toArr);

    $subject = "Daily Buyer Inspection";				
    $header=mailHeader();
   
    if($_REQUEST['isview']==1){
      $mail_item = 121;
      if($to){
        echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
      }else{
        echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
      }
      echo $message;

    }
    else{
      if($to!="")echo sendMailMailer( $to, $subject, $messageTitle.$message, $from_mail);
    }
    //echo $message;
 
}
?>





</body>
</html>

