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

 

    $sql="select c.COMPANY_ID,c.SHIP_APP_REQ_PREFIX,c.SHIP_APP_REQ_NUM,c.SHIP_APP_REQ_NO,c.PO_BREAK_DOWN_ID,c.EROSION_TYPE,c.SHIP_APP_REQ_DATE,c.EROSION_DATE,c.EROSION_VALUE,c.TO_BE_SHIPPED_QTY,c.EXPECTED_SHIP_DATE,c.THE_PROBLEMS,c.ROOT_CAUSES,c.CORRECTIVE_ACTION_PLANS,c.PRECAUTIONERY_FUTURE_PLANS,b.PUB_SHIPMENT_DATE,b.SHIPMENT_DATE, b.PO_NUMBER,b.PO_RECEIVED_DATE,b.PO_QUANTITY,a.GMTS_ITEM_ID,a.DEALING_MARCHANT,a.TEAM_LEADER,a.FACTORY_MARCHANT,a.TOTAL_SET_QNTY, a.JOB_NO, a.BUYER_NAME, a.STYLE_REF_NO,d.EMBEL_TYPE,e.SEW_SMV from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst e,erosion_entry c left join pro_garments_production_mst d on c.PO_BREAK_DOWN_ID=d.PO_BREAK_DOWN_ID   where a.job_no=b.job_no_mst  and a.job_no=e.job_no  and b.id=c.PO_BREAK_DOWN_ID and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.JOB_NO='RpC-22-00424' ";
    //echo $sql;die();
    $dataArr=sql_select($sql);
   // print_r($dataArr);

    //$row=$data_array[0];

$erosion_type=array(1=>"Discount Shipment",2=>"Sea-Air Shipment",3=>"Air Shipment");
$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$buyer_library_arr=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");$lib_teamleader=return_library_array( "select id,team_leader_name from lib_team_mst", "id", "team_leader_name");
$lib_dealing_merchant=return_library_array( "select id,team_member_name from lib_mkt_team_member_info ", "id", "team_member_name");
	
	$lib_factory_merchant=return_library_array( "select id,team_member_name from lib_mkt_team_member_info ", "id", "team_member_name");
	
	$po_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row['PO_RECEIVED_DATE']))), date("Y-m-d",strtotime(change_date_format($row['SHIPMENT_DATE']))) );
	   
	$booking_arr=return_library_array( "select BOOKING_NO from WO_BOOKING_DTLS where PO_BREAK_DOWN_ID={$row[PO_BREAK_DOWN_ID]} and BOOKING_TYPE=1 and STATUS_ACTIVE=1 and IS_DELETED=0", "BOOKING_NO", "BOOKING_NO");
//$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");

//$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');



?>


<!DOCTYPE html>
<html>



<body>

<style>

/* td {
  /* font-size: 12px; */
  /* height: 100px; 
  text-align: center;
  word-wrap: break-word; */

/* }
th{

  /* font-size: 12px; */
/* } */ 




</style>



<body>
    <table border="1" rules="all">
        <tr>
          <th>To</th>
          <td>value</td>
         
        </tr>
        <tr>
         <th>From</th>
          <td>..</td>
        
        </tr>
        <tr>
         <th>Subject</th>
          <td>...</td>
         
        </tr>
      </table><br>

      <p>
        Dear Sir, <br>
        Please check below erosion request for your electronic approval <br>
        Click the below link to approve or reject with comments

      </p>

      <table border="1" rules="all">
      <?
    
     //$grandtotal_amount=0;
			  
    foreach ($dataArr as $row) 
    {  
  ?>
        <tr>
          <th>Company Name</th>
          <td><? echo $company_arr[$row[csf("COMPANY_ID")]]; ?></td>
         
        </tr>
        <tr>
         <th>Buyer Name</th>
          <td><? echo $buyer_library_arr[$row[csf("BUYER_NAME")]]; ?></td>
        
        </tr>
       
        <tr>
            <th>Erosion Date</th>
             <td><? echo $row[csf('EROSION_DATE')];?></td>
            
           </tr>
           <tr>
            <th>Erosion No.</th>
             <td><? echo $row[csf('SHIP_APP_REQ_NO')];?></td>
            
           </tr>
           <tr>
            <th>Erosion Type</th>
             <td><? echo $erosion_type[$row[csf("EROSION_TYPE")]]; ?></td>
            
           </tr>
           <tr>
            <th>Erosion Value</th>
             <td><? echo $row[csf('EROSION_VALUE')];?></td>
            
           </tr>
           <tr>
            <th>Team Leader</th>
             <td><? echo $lib_teamleader[$row[csf("TEAM_LEADER")]]; ?></td>
            
           </tr><tr>
            <th>Dealing Merchant</th>
             <td><? echo $lib_dealing_merchant[$row[csf("DEALING_MARCHANT")]]; ?></td>
            
           </tr>
           <tr>
            <th>Factory Merchant</th>
             <td><? echo $lib_factory_merchant[$row[csf("FACTORY_MARCHANT")]]; ?></td>
            
           </tr><tr>
            <th>Job No.</th>
             <td><? echo $row[csf('JOB_NO')];?></td>
            
           </tr>
           <tr>
            <th>Budget Profit</th>
             <td>...</td>
            
           </tr>
           <tr>
            <th>Profit After Erosion</th>
             <td>...</td>
            
           </tr>
           <?
   
   //$grandtotal_amount+=$row['receive_qnty'];
   }
     // $i++;
  ?>
      </table>
</body>

<?
$message=ob_get_contents();
ob_clean();

$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=103 and b.mail_user_setup_id=c.id and a.company_id=$compid AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
$mail_sql=sql_select($sql);
foreach($mail_sql as $row)
{
  $toArr[$row[csf('email_address')]]=$row[csf('email_address')]; 
}

$to=implode(',',$toArr);

$subject = " Erosion Entry ";				
$header=mailHeader();
if($_REQUEST['isview']==1){
   echo $to.$message;
}
else{
  if($to!="")echo sendMailMailer( $to, $subject, $messageTitle.$message, $from_mail);
}
//echo $message;
 

?>





</body>
</html>

