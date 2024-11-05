<?php

date_default_timezone_set("Asia/Dhaka");
// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require_once('setting/mail_setting.php');
ob_start();

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s", $strtotime),0))),'','',1);	
$next_date = change_date_format(date('Y-m-d H:i:s', strtotime('+1 day', strtotime($current_date))),'','',1); 	
$date_cond	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";

foreach($company_arr as $company_id=>$company_name){	

$sql="select a.id,a.company_id,a.job_no,a.buyer_id,a.sales_booking_no,a.booking_date,a.delivery_date,a.style_ref_no,a.sales_order_type,a.customer_buyer,b.body_part_id,b.color_id,b.color_type_id,b.fabric_desc,b.gsm_weight,b.color_range_id,b.finish_qty,b.avg_rate,b.amount from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company_id and to_date(to_char(a.booking_date, 'DD-MON-YYYY')) BETWEEN '$current_date' AND '$next_date' group by a.id,a.job_no,a.buyer_id,a.sales_booking_no,a.booking_date,a.delivery_date,a.style_ref_no,a.sales_order_type,a.company_id,a.customer_buyer,b.body_part_id,b.color_id,b.color_type_id,b.fabric_desc,b.gsm_weight,b.color_range_id,b.finish_qty,b.avg_rate,b.amount ";

//echo $sql;die();

$body_part_arr = return_library_array("select id,body_part_full_name from  lib_body_part where status_active=1 and is_deleted=0 order by body_part_full_name", "id", "body_part_full_name");

$company_library_arr=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0 and CORE_BUSINESS=1 ","id","company_name");

$buyer_library_arr=return_library_array("select id,short_name from lib_buyer","id","short_name");
$color_arr=return_library_array("select id,color_name from lib_color","id","color_name");
//$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");

//$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

$dataArr=sql_select($sql);

?>''


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

<h2 style="text-align:center ;"><? echo $company_arr[$company_id]; ?></h2>
<h2 style="text-align:center ;"> Date: <?=date('d-m-Y',strtotime($current_date));?></h2>

<table border="1" rules="all" >

      
  <tr>
    <th>SL. No</th>
    <th>Booking Date</th>
    <th>Delivery Date</th>
    <th>Sales Order Number</th>
    <th>Party Name</th>
    <th>Customer Buyer</th>
    <th>Booking Number</th>
    <th>Style Ref</th>
    <th>Sales Order Type</th>
    <th>Body Parts</th>
    <th>Color Type</th>
    <th>Fabric Description</th>
    <th>Fabric GSM</th>
    <th>Color</th>
    <th>Color Range</th>
    <th>Finish Qty [KG</th>
    <th>Avg Rate</th>
    <th>Amount</th>
  </tr>
  <?
    $i=1;
     //$grandtotal_amount=0;
			  
    foreach ($dataArr as $row) 
    {  
  ?>

  <tr>
    <td><? echo $i;?></td>
    <td><? echo change_date_format($row[csf('booking_date')]);?></td>
    <td><? echo change_date_format($row[csf('delivery_date')]);?></td>
    <td><? echo $row[csf('job_no')];?></td>
    <td><? echo $buyer_library_arr[$row[csf("buyer_id")]]; ?></td>
    <td><? echo $row[csf('customer_buyer')];?></td>
    <td><? echo $row[csf('sales_booking_no')];?></td>
    <td ><? echo $row[csf('style_ref_no')];?></td>
    <td><? echo $sales_order_type_arr[$row[csf("sales_order_type")]]; ?></td>
    <td><? echo $body_part_arr[$row[csf("body_part_id")]]; ?></td>
    <td><? echo $color_type[$row[csf("color_type_id")]]; ?></td>
    <td><? echo $row[csf('fabric_desc')];?></td>
    <td><? echo $row[csf('gsm_weight')];?></td>
    <td><? echo $color_arr[$row[csf("color_id")]]; ?></td>
    <td><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
    <td><? echo $row[csf('finish_qty')];?></td>
    <td><? echo $row[csf('avg_rate')];?></td>
    <td><? echo $row[csf('amount')];?>8</td>
  </tr>

  <?
   $i++;
   //$grandtotal_amount+=$row['receive_qnty'];
   }
     // $i++;
  ?>
  
</table>

<?
$message=ob_get_contents();
ob_clean();

$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=102 and b.mail_user_setup_id=c.id and a.company_id=$compid AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
$mail_sql=sql_select($sql);
foreach($mail_sql as $row)
{
  $toArr[$row[csf('email_address')]]=$row[csf('email_address')]; 
}

$to=implode(',',$toArr);

$subject = " Daily Ex-factory Schedule ";				
$header=mailHeader();
if($_REQUEST['isview']==1){
   echo $to.$message;
}
else{
  if($to!="")echo sendMailMailer( $to, $subject, $messageTitle.$message, $from_mail);
}
//echo $message;
 
}
?>





</body>
</html>

