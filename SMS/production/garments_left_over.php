
<?php

date_default_timezone_set("Asia/Dhaka");
// require_once('../mailer/class.phpmailer.php');
require_once('../../includes/common.php');
//require_once('setting/mail_setting.php');
ob_start();
  $company_lib =return_library_array( "select id, COMPANY_SHORT_NAME from lib_company where status_active=1 and is_deleted=0 ", "id", "COMPANY_SHORT_NAME");

   $company_ids = implode(',',array_keys($company_lib));

  $strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();

  // $current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s", $strtotime),0))),'','',1);	
	// $next_date = change_date_format(date('Y-m-d H:i:s', strtotime('+1 day', strtotime($current_date))),'','',1); 	
	// $date_cond	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";

  $current_date = change_date_format(date("Y-m-d H:i:s",time()),'','',1);
  $previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);  

  

 $date_cond	=" and a.leftover_date between '".$previous_date."' and '".$current_date."' ";
   //GMT Leftover TTL Received 
   $sql = "SELECT a.id, a.COMPANY_ID, b.TOTAL_LEFT_OVER_RECEIVE FROM pro_leftover_gmts_rcv_mst a,
   pro_leftover_gmts_rcv_dtls b, wo_po_break_down c WHERE a.id = b.mst_id AND
   b.po_break_down_id = c.id AND a.status_active = 1 AND a.is_deleted = 0 AND a.company_id in($company_ids) $date_cond";
   //echo $sql;die();
    $lft_recive_sql_res=sql_select($sql);
    $dataArr=array();
  
    foreach($lft_recive_sql_res as $row){
         $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
        $dataArr[$row['COMPANY_ID']]['TOTAL_LEFT_OVER_RECEIVE']+=$row['TOTAL_LEFT_OVER_RECEIVE'];
  
      }


    //GMT Leftover  TTL Issued
    $sql = "SELECT a.id, a.COMPANY_ID, a.order_type, b.TOTAL_ISSUE FROM pro_leftover_gmts_issue_mst a,
    pro_leftover_gmts_issue_dtls b WHERE a.id = b.mst_id AND a.status_active = 1 AND a.COMPANY_ID in($company_ids) $date_cond AND a.is_deleted = 0";
    // echo  $sql;die;
    $dyeing_sql_res=sql_select($sql);
   

    foreach($dyeing_sql_res as $row){
      $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
      $dataArr[$row['COMPANY_ID']]['TOTAL_ISSUE']+=$row['TOTAL_ISSUE'];
    
    }
     // echo "<pre>";
      // print_r($dataArr); 
      //   echo "</pre>";die();  

    $garments_leftover_stock=$dataArr[$row['COMPANY_ID']]['TOTAL_LEFT_OVER_RECEIVE']-$dataArr[$row['COMPANY_ID']]['TOTAL_ISSUE'];
     //echo $garments_leftover_stock;die;





     
 ?>

<!DOCTYPE html>
<html>
<style>
  table {
    border-collapse: collapse;
    width: 30%;
  }
  th, td {
    border: 1px solid black;
    padding: 8px;
    text-align: left;
  }
  .no-border {
    border: none;
  }
</style>


<body>
  <table>
  <th  class="no-border">between :<?=$previous_date?> and <?=$current_date?></th>
  <?

   foreach ($dataArr as $company => $row) 
   {    
   
          ?>    <tr>
                <th  class="no-border"><?=$company_lib[$row['COMPANY_ID']];?></th>

        
                 </tr>
                  <tr>
                  
                  
                  </tr>
                  <tr>
                    <td>Garments Leftover Stock </td>
                    <td><?=$garments_leftover_stock;?></td> 
                    
                  </tr> 
                  <tr>
                    <td>GMT Leftover TTL Received </td>
                    <td><?=$row['TOTAL_LEFT_OVER_RECEIVE'];?></td> 
                    
                  </tr> 
                  <tr>
                    <td>GMT Leftover TTL Issued. </td>
                    <td><?=$row['TOTAL_ISSUE'];?></td>
                    
                  </tr> 

                  <tr>
                    <td>GMT Leftover  Add </td>
                    <td><?=$garments_leftover_stock;?></td>
                    
                  </tr> 

                  <tr>
                    <td>GMT Leftover  Issue</td>
                    <td><?=$garments_leftover_stock;?></td>
                    
                  </tr> 
                
              
          <?
   
        }   
               //$grandtotal_amount+=$row['receive_qnty'];
   
     // $i++;
  ?>
  </table>
</body>


</html>

