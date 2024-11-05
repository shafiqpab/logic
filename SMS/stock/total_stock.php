
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

  

//$date_cond	=" and a.RECEIVE_DATE between '".$previous_date."' and '".$current_date."' ";
//rcv_total_opening (yarn stock)
  $sql = "SELECT a.id AS trans_id, a.prod_id, a.COMPANY_ID, a.CONS_QUANTITY FROM inv_transaction a, inv_receive_master c WHERE a.mst_id = c.id AND a.item_category = 1 AND a.transaction_type IN (1, 4) AND a.TRANSACTION_DATE <= '" . $current_date . "' AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND a.company_id  in($company_ids)";
   // echo $sql;die();
    $cons_sql_res=sql_select($sql);
    $dataArr=array();
  
    foreach($cons_sql_res as $row){
         $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
        $dataArr[$row['COMPANY_ID']]['CONS_QUANTITY']+=$row['CONS_QUANTITY'];
    
      }
      
      // echo "<pre>";
      // print_r($dataArr); 
      //   echo "</pre>";die();  


      
      //$date_cond	=" and a.process_end_date between '".$previous_date."' and '".$current_date."' ";
    // trans_in_total_opening (yarn stock)/transfer_in_qty
    $sql = "SELECT a.id AS trans_id, a.COMPANY_ID, a.cons_quantity as OPENING_QUANTITY FROM inv_transaction a, inv_item_transfer_mst c WHERE a.mst_id = c.id AND a.transaction_type =5 AND a.item_category = 1 AND a.company_id in($company_ids) AND a.transaction_date <= '" . $current_date . "' AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND c.transfer_criteria = 1";
     //echo  $sql;die;
    $opening_sql_res=sql_select($sql);
   

    foreach($opening_sql_res as $row){
      $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
      $dataArr[$row['COMPANY_ID']]['OPENING_QUANTITY']+=$row['OPENING_QUANTITY'];
    
    }

      // echo "<pre>";
      // print_r($dataArr); 
      //   echo "</pre>";die();  


//// issue_total_opening (yarn stock)
//$date_cond	=" and a.RECEIVE_DATE between '".$previous_date."' and '".$current_date."' ";
$sql = "SELECT a.prod_id, a.COMPANY_ID, a.transaction_date, a.cons_quantity as ISSUE_OPENING_QUANTITY  FROM inv_transaction a, inv_issue_master c WHERE a.mst_id = c.id AND a.item_category = 1 AND a.transaction_type IN (2, 3) AND a.transaction_date <= '" . $current_date . "' AND a.company_id in($company_ids) AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0";
//echo $sql;die;

$issue_opening__sql_res=sql_select($sql);

foreach($issue_opening__sql_res as $row){
  $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
  $dataArr[$row['COMPANY_ID']]['ISSUE_OPENING_QUANTITY']+=$row['ISSUE_OPENING_QUANTITY'];
 
}
//  echo "<pre>";
//       print_r($dataArr); 
//         echo "</pre>";die();  
 
//..........trans_out_total_opening (yarn stock)/transfer_out_qty;.......
$date_cond	=" and a.PRODUCTION_DATE between '".$previous_date."' and '".$current_date."' ";
$sql="SELECT a.id AS trans_id, a.COMPANY_ID, a.cons_quantity as OUT_OPENING_QUANTITY FROM inv_transaction a, inv_item_transfer_mst c WHERE a.mst_id = c.id AND a.transaction_type =6 AND a.item_category = 1 AND a.company_id in($company_ids) AND a.transaction_date <= '" . $current_date . "' AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND c.transfer_criteria = 1 ";

//echo $sql;die;
   $finish_sql_res=sql_select($sql);
  foreach($finish_sql_res as $row){
  $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
   $dataArr[$row['COMPANY_ID']]['OUT_OPENING_QUANTITY']+=$row['OUT_OPENING_QUANTITY'];
    }
 
    $openingbalance=($dataArr[$row['COMPANY_ID']]['CONS_QUANTITY'] + $dataArr[$row['COMPANY_ID']]['OPENING_QUANTITY']) - ($dataArr[$row['COMPANY_ID']]['ISSUE_OPENING_QUANTITY'] + $dataArr[$row['COMPANY_ID']]['OUT_OPENING_QUANTITY']);
  // echo  $openingbalance;

      //..........Total recieve.......
      $date_cond	=" and a.DELIVERY_DATE between '".$previous_date."' and '".$current_date."' ";
      $sql = "SELECT a.id AS trans_id, a.COMPANY_ID, (CASE WHEN a.transaction_type=1 AND c.receive_purpose!=5 THEN (a.cons_quantity) ELSE 0 END) AS PURCHASE_QTY, (CASE WHEN a.transaction_type=1 AND c.receive_purpose=5 THEN (a.cons_quantity) ELSE 0 END) AS LOAN_QTY, (CASE WHEN a.transaction_type=4 AND c.knitting_source=1 THEN (a.cons_quantity) ELSE 0 END) AS RCV_INSIDE_QTY, (CASE WHEN a.transaction_type=4 AND c.knitting_source!=1 THEN (a.cons_quantity) ELSE 0 END) AS RCV_OUTSIDE_QTY, (CASE WHEN a.receive_basis=30 THEN (a.cons_quantity) ELSE 0 END) AS ADJUSTMENT_QTY FROM inv_transaction a, inv_receive_master c WHERE a.mst_id = c.id AND a.item_category = 1 AND a.transaction_date <= '" . $current_date . "' AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND a.company_id in($company_ids) ";
     // echo $sql;die;

      $rcv_sql_res=sql_select($sql);
        foreach($rcv_sql_res as $row){
       $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
      $dataArr[$row['COMPANY_ID']]['PURCHASE_QTY']+=$row['PURCHASE_QTY'];
      $dataArr[$row['COMPANY_ID']]['LOAN_QTY']+=$row['LOAN_QTY'];
      $dataArr[$row['COMPANY_ID']]['RCV_INSIDE_QTY']+=$row['RCV_INSIDE_QTY'];
      $dataArr[$row['COMPANY_ID']]['RCV_OUTSIDE_QTY']+=$row['RCV_OUTSIDE_QTY'];
      $dataArr[$row['COMPANY_ID']]['ADJUSTMENT_QTY']+=$row['ADJUSTMENT_QTY'];
       
    }

      //  echo "<pre>";
      // print_r($dataArr); 
      //   echo "</pre>";die();  
      $totalRcv=$dataArr[$row['COMPANY_ID']]['PURCHASE_QTY'] + $dataArr[$row['COMPANY_ID']]['LOAN_QTY'] + $dataArr[$row['COMPANY_ID']]['RCV_INSIDE_QTY'] + $dataArr[$row['COMPANY_ID']]['RCV_OUTSIDE_QTY'] + $dataArr[$row['COMPANY_ID']]['ADJUSTMENT_QTY'] + $dataArr[$row['COMPANY_ID']]['OPENING_QUANTITY'];
      //echo $totalRcv;die; 

      //..........Total Issue.......
      $date_cond	=" and a.DELIVERY_DATE between '".$previous_date."' and '".$current_date."' ";
      $sql = "SELECT a.company_id, a.transaction_date, (CASE WHEN a.transaction_type = 2 AND c.knit_dye_source = 1 AND c.issue_purpose != 5 THEN (a.cons_quantity) ELSE 0 END) AS ISSUE_INSIDE_QTY, (CASE WHEN a.transaction_type = 2 AND c.knit_dye_source != 1 AND c.issue_purpose != 5 THEN (a.cons_quantity) ELSE 0 END) AS ISSUE_OUTSIDE_QTY, (CASE WHEN a.transaction_type = 3 AND c.entry_form = 8 THEN (a.cons_quantity) ELSE 0 END) AS RCV_RETURN_QTY, (CASE WHEN a.transaction_type = 2 AND c.issue_purpose = 5 THEN (a.cons_quantity) ELSE 0 END) AS ISSUE_LOAN_QTY, (CASE WHEN a.receive_basis = 30 THEN (a.cons_quantity) ELSE 0 END) AS ISSUE_ADJUSTMENT_QTY FROM inv_transaction a, inv_issue_master c WHERE a.mst_id = c.id AND a.item_category = 1 AND a.transaction_date <= '" . $current_date . "' AND a.company_id in($company_ids) AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 ";
      //echo $sql;die; 
       
      $yarn_issue_sql_res=sql_select($sql);
      foreach($yarn_issue_sql_res as $row){
      $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
      $dataArr[$row['COMPANY_ID']]['ISSUE_INSIDE_QTY']+=$row['ISSUE_INSIDE_QTY'];
      $dataArr[$row['COMPANY_ID']]['ISSUE_OUTSIDE_QTY']+=$row['ISSUE_OUTSIDE_QTY'];
      $dataArr[$row['COMPANY_ID']]['RCV_RETURN_QTY']+=$row['RCV_RETURN_QTY'];
      $dataArr[$row['COMPANY_ID']]['ISSUE_LOAN_QTY']+=$row['ISSUE_LOAN_QTY'];
      $dataArr[$row['COMPANY_ID']]['ISSUE_ADJUSTMENT_QTY']+=$row['ISSUE_ADJUSTMENT_QTY'];
          }

          $totalIssue=$dataArr[$row['COMPANY_ID']]['ISSUE_INSIDE_QTY'] + $dataArr[$row['COMPANY_ID']]['ISSUE_OUTSIDE_QTY'] + $dataArr[$row['COMPANY_ID']]['RCV_RETURN_QTY'] + $dataArr[$row['COMPANY_ID']]['ISSUE_LOAN_QTY'] + $dataArr[$row['COMPANY_ID']]['ISSUE_ADJUSTMENT_QTY'] + $dataArr[$row['COMPANY_ID']]['OUT_OPENING_QUANTITY'];

          $yarn_stock = $openingBalance + $totalRcv - $totalIssue;
          // echo $yarn_stock;die;
          
     //...Yarn Dyeing Stock [rcv_total_opening]...
     $date_cond	=" and a.PRODUCTION_DATE between '".$previous_date."' and '".$current_date."' ";
      $sql = " SELECT a.id AS trans_id, a.prod_id, a.COMPANY_ID, b.dyed_type, a.cons_quantity as DYEING_STOCK_QTY FROM inv_transaction a, product_details_master b, inv_receive_master c WHERE a.mst_id = c.id AND a.prod_id = b.id AND a.item_category = 1 AND a.transaction_type IN (1, 4) AND a.transaction_date <= '" . $current_date . "' AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND b.dyed_type = 1 AND c.is_deleted = 0 AND a.company_id in($company_ids) ";
      //echo $sql;die;

      $dying_stock_sql_res=sql_select($sql);
      foreach($dying_stock_sql_res as $row){
        $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
      $dataArr[$row['COMPANY_ID']]['DYEING_STOCK_QTY']+=$row['DYEING_STOCK_QTY'];
          }

      //  echo "<pre>";
      // print_r($dataArr); 
      //   echo "</pre>";die();  


       //............Yarn Dyeing Stock [trans_in_total_opening].......

       $date_cond	=" and a.PRODUCTION_DATE between '".$previous_date."' and '".$current_date."' ";

       $sql="SELECT a.id AS trans_id,a.COMPANY_ID, a.prod_id, b.dyed_type, a.buyer_id, a.transaction_type, a.transaction_date, a.cons_quantity as TRANS_IN_TOTAL_OPENING_QTY, a.cons_rate, a.cons_amount FROM inv_transaction a, product_details_master b, inv_item_transfer_mst c WHERE a.mst_id = c.id AND a.prod_id = b.id AND a.transaction_type = 5 AND a.item_category = 1 AND a.transaction_date <= '" . $current_date . "' AND a.status_active = 1 AND b.dyed_type = 1  and a.COMPANY_ID in($company_ids) AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND c.transfer_criteria = 1";
      
      //echo $sql;die; 
   $trans_in_total_opening_sql_res=sql_select($sql);
   foreach($trans_in_total_opening_sql_res as $row){
     $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
   $dataArr[$row['COMPANY_ID']]['TRANS_IN_TOTAL_OPENING_QTY']+=$row['TRANS_IN_TOTAL_OPENING_QTY'];
       }
      //    echo "<pre>";
      // print_r($dataArr); 
      //   echo "</pre>";die();  
  
 //.......Yarn Dyeing Stock...(issue_total_opening_rate)
 $date_cond	=" and a.PO_RECEIVED_DATE between '".$previous_date."' and '".$current_date."' ";

 $sql="SELECT a.prod_id, b.dyed_type, a.COMPANY_ID, a.cons_quantity as ISSUE_TOTAL_OPENING_RATE_QTY FROM inv_transaction a, product_details_master b, inv_issue_master c WHERE a.mst_id = c.id AND a.prod_id = b.id AND a.item_category = 1 AND a.transaction_type IN (2, 3) AND a.transaction_date <= '" . $current_date . "' AND a.status_active = 1 AND a.is_deleted = 0 AND b.dyed_type = 1 AND a.COMPANY_ID in ($company_ids) AND c.status_active = 1 AND c.is_deleted = 0";

 //echo $sql;die;

$issue_total_opening_rate_sql_res=sql_select($sql);
foreach($issue_total_opening_rate_sql_res as $row){
$dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
$dataArr[$row['COMPANY_ID']]['ISSUE_TOTAL_OPENING_RATE_QTY']+=$row['ISSUE_TOTAL_OPENING_RATE_QTY'];
}

 

//............Yarn Dyeing Stock [trans_out_total_opening].........
$date_cond	=" and a.BOOKING_DATE between '".$previous_date."' and '".$current_date."' ";

$sql="SELECT a.id AS trans_id,a.COMPANY_ID, a.prod_id, b.dyed_type, a.buyer_id, a.transaction_type, a.transaction_date, a.cons_quantity as TRANS_OUT_TOTAL_OPENING_QTY, a.cons_rate, a.cons_amount FROM inv_transaction a, product_details_master b, inv_item_transfer_mst c WHERE a.mst_id = c.id AND a.prod_id = b.id AND a.transaction_type = 6 AND a.item_category = 1 AND a.transaction_date <= '" . $current_date . "' AND a.status_active = 1 AND b.dyed_type = 1  and a.COMPANY_ID in ($company_ids) AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND c.transfer_criteria = 1";
      
//echo $sql;die; 
$trans_out_total_opening_sql_res=sql_select($sql);
foreach($trans_out_total_opening_sql_res as $row){
$dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
$dataArr[$row['COMPANY_ID']]['TRANS_OUT_TOTAL_OPENING_QTY']+=$row['TRANS_OUT_TOTAL_OPENING_QTY'];
  
}
  // echo "<pre>";
  //     print_r($dataArr); 
  //       echo "</pre>";die();  

  $stock_openingBalance= ($dataArr[$row['COMPANY_ID']]['DYEING_STOCK_QTY'] + $dataArr[$row['COMPANY_ID']]['TRANS_IN_TOTAL_OPENING_QTY']) - ($dataArr[$row['COMPANY_ID']]['ISSUE_TOTAL_OPENING_RATE_QTY'] + $dataArr[$row['COMPANY_ID']]['TRANS_OUT_TOTAL_OPENING_QTY']);
   //echo $stock_openingBalance;die;

  //...Yarn Dyeing Stock [total Recieve]..
  $date_cond	=" and a.DELEVERY_DATE between '".$previous_date."' and '".$current_date."' ";
  $sql="SELECT a.id AS trans_id, a.COMPANY_ID, b.dyed_type, (CASE WHEN a.transaction_type = 1 AND c.receive_purpose != 5 THEN (a.cons_quantity) ELSE 0 END) AS STOCK_PURCHASE_QTY, (CASE WHEN a.transaction_type = 1 AND c.receive_purpose = 5 THEN (a.cons_quantity) ELSE 0 END) AS STOCK_LOAN_QTY, (CASE WHEN a.transaction_type = 4 AND c.knitting_source = 1 THEN (a.cons_quantity) ELSE 0 END) AS STOCK_RCV_INSIDE_QTY, (CASE WHEN a.transaction_type = 4 AND c.knitting_source != 1 THEN (a.cons_quantity) ELSE 0 END) AS STOCK_RCV_OUTSIDE_QTY FROM inv_transaction a,product_details_master b, inv_receive_master c WHERE a.mst_id = c.id AND a.prod_id = b.id AND a.item_category = 1 and b.dyed_type=1 AND a.transaction_date <= '" . $current_date . "' AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND a.company_id in ($company_ids)";

  //echo $sql;die;
  $total_stock_rcv_sql_res=sql_select($sql);
  foreach($total_stock_rcv_sql_res as $row){
    $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
  $dataArr[$row['COMPANY_ID']]['STOCK_PURCHASE_QTY']+=$row['STOCK_PURCHASE_QTY'];
  $dataArr[$row['COMPANY_ID']]['STOCK_LOAN_QTY']+=$row['STOCK_LOAN_QTY'];
  $dataArr[$row['COMPANY_ID']]['STOCK_RCV_INSIDE_QTY']+=$row['STOCK_RCV_INSIDE_QTY'];
  $dataArr[$row['COMPANY_ID']]['STOCK_RCV_OUTSIDE_QTY']+=$row['STOCK_RCV_OUTSIDE_QTY'];
 
      }       
  //  echo "<pre>";
  //     print_r($dataArr); 
  //       echo "</pre>";die();  

//...Yarn Dyeing Stock [total Recieve][transfer_in_qty].
  $date_cond	=" and b.TRANSACTION_DATE between '".$previous_date."' and '".$current_date."' ";

 $sql="SELECT a.id AS trans_id, a.COMPANY_ID, b.dyed_type, a.cons_quantity as STOCK_TRANSFER_IN_QTY  FROM inv_transaction a, product_details_master b, inv_item_transfer_mst c WHERE a.mst_id = c.id AND a.prod_id = b.id AND a.transaction_type=5 AND a.item_category = 1 AND a.transaction_date <= '" . $current_date . "' AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND b.dyed_type=1 and a.COMPANY_ID in ($company_ids) AND c.is_deleted = 0 AND c.transfer_criteria = 1";

 //echo $sql;die;
 $stock_transfer_in_qty_sql_res=sql_select($sql);
 foreach($stock_transfer_in_qty_sql_res as $row){
   $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
   $dataArr[$row['COMPANY_ID']]['STOCK_TRANSFER_IN_QTY']+=$row['STOCK_TRANSFER_IN_QTY'];

     }  

    $stock_rcv=$dataArr[$row['COMPANY_ID']]['STOCK_PURCHASE_QTY'] + $dataArr[$row['COMPANY_ID']]['STOCK_LOAN_QTY'] + $dataArr[$row['COMPANY_ID']]['STOCK_RCV_INSIDE_QTY']  + $dataArr[$row['COMPANY_ID']]['STOCK_RCV_OUTSIDE_QTY'] + $dataArr[$row['COMPANY_ID']]['STOCK_TRANSFER_IN_QTY'];
    //echo $stock_rcv;die;

 // //...Yarn Dyeing Stock [total Issue]..
 $date_cond	=" and a.BOOKING_DATE between '".$previous_date."' and '".$current_date."' ";

 $sql="SELECT a.company_id, b.dyed_type, a.transaction_date, (CASE WHEN a.transaction_type = 2 AND c.knit_dye_source = 1 AND c.issue_purpose != 5 THEN (a.cons_quantity) ELSE 0 END) AS STOCK_ISSUE_INSIDE_QTY, (CASE WHEN a.transaction_type = 2 AND c.knit_dye_source != 1 AND c.issue_purpose != 5 THEN (a.cons_quantity) ELSE 0 END) AS STOCK_ISSUE_OUTSIDE_QTY, (CASE WHEN a.transaction_type = 3 AND c.entry_form = 8 THEN (a.cons_quantity) ELSE 0 END) AS STOCK_ISSUE_RCV_RETURN_QTY, (CASE WHEN a.transaction_type = 2 AND c.issue_purpose = 5 THEN (a.cons_quantity) ELSE 0 END) AS STOCK_ISSUE_ISSUE_LOAN_QTY FROM inv_transaction a, product_details_master b, inv_issue_master c WHERE a.mst_id = c.id AND a.prod_id = b.id AND a.item_category = 1 AND a.transaction_date <= '" . $current_date . "' AND a.company_id in ($company_ids) AND b.dyed_type=1 AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0";

 //echo $sql;die;
 $tot_stock_issue_req_sql_res=sql_select($sql);
 foreach($tot_stock_issue_req_sql_res as $row){
   $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
 $dataArr[$row['COMPANY_ID']]['STOCK_ISSUE_INSIDE_QTY']+=$row['STOCK_ISSUE_INSIDE_QTY'];
 $dataArr[$row['COMPANY_ID']]['STOCK_ISSUE_OUTSIDE_QTY']+=$row['STOCK_ISSUE_OUTSIDE_QTY'];
 $dataArr[$row['COMPANY_ID']]['STOCK_ISSUE_RCV_RETURN_QTY']+=$row['STOCK_ISSUE_RCV_RETURN_QTY'];
 $dataArr[$row['COMPANY_ID']]['STOCK_ISSUE_ISSUE_LOAN_QTY']+=$row['STOCK_ISSUE_ISSUE_LOAN_QTY'];

     }  

      


 //...Yarn Dyeing Stock [total issue][transfer_out_qty].
 $date_cond	=" and b.TRANSACTION_DATE between '".$previous_date."' and '".$current_date."' ";

 $sql="SELECT a.id AS trans_id, a.COMPANY_ID, b.dyed_type, a.cons_quantity as STOCK_TRANSFER_OUT_QTY  FROM inv_transaction a, product_details_master b, inv_item_transfer_mst c WHERE a.mst_id = c.id AND a.prod_id = b.id AND a.transaction_type=6 AND a.item_category = 1 AND a.transaction_date <= '" . $current_date . "' AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND b.dyed_type=1 and a.COMPANY_ID in ($company_ids) AND c.is_deleted = 0 AND c.transfer_criteria = 1";

 //echo $sql;die;
 $stock_transfer_out_qty_sql_res=sql_select($sql);
 foreach($stock_transfer_out_qty_sql_res as $row){
   $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
   $dataArr[$row['COMPANY_ID']]['STOCK_TRANSFER_OUT_QTY']+=$row['STOCK_TRANSFER_OUT_QTY'];

     }  

    $stock_totalIssue=$dataArr[$row['COMPANY_ID']]['STOCK_ISSUE_INSIDE_QTY'] + $dataArr[$row['COMPANY_ID']]['STOCK_ISSUE_OUTSIDE_QTY'] + $dataArr[$row['COMPANY_ID']]['STOCK_ISSUE_RCV_RETURN_QTY'] + $dataArr[$row['COMPANY_ID']]['STOCK_ISSUE_ISSUE_LOAN_QTY'] + $dataArr[$row['COMPANY_ID']]['STOCK_TRANSFER_OUT_QTY'];


    $yarn_dyeing_stock=$stock_openingBalance + $stock_rcv - $stock_totalIssue;
     
   //echo $yarn_dyeing_stock;die;

   //GF Stock [Running Order] 
   $date_cond	=" and a.batch_date between '".$previous_date."' and '".$current_date."' ";

 $sql="SELECT b.trans_type, c.shiping_status,a.COMPANY_ID, b.po_breakdown_id, b.prod_id, (CASE WHEN b.trans_type = 1 THEN (b.quantity) ELSE 0 END) AS RCV_QTY, (CASE WHEN b.trans_type = 2 THEN (b.quantity) ELSE 0 END) AS ISS_QTY, (CASE WHEN b.trans_type = 3 THEN (b.quantity) ELSE 0 END) AS RCV_RATE_QTY, (CASE WHEN b.trans_type = 4 THEN (b.quantity) ELSE 0 END) AS ISSUE_RATE_QTY, (CASE WHEN b.trans_type = 5 THEN (b.quantity) ELSE 0 END) AS TRANS_IN_QTY, (CASE WHEN b.trans_type = 6 THEN (b.quantity) ELSE 0 END) AS TRANS_OUT_QTY FROM inv_transaction a, order_wise_pro_details b, wo_po_break_down c WHERE a.id = b.trans_id and b.po_breakdown_id = c.id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND b.entry_form IN (2, 22, 13, 16, 45, 51, 58, 61, 80, 81, 82, 83, 84, 110, 183) AND a.item_category = 13 and a.company_id in ($company_ids) AND a.transaction_type IN (1, 2, 3, 4, 5, 6) AND a.transaction_date <= '" . $current_date . "' and c.shiping_status <> 3 and c.status_active = 1";

 //echo $sql;die;
 $gf_sql_res=sql_select($sql);
   //print_r($gf_sql_res);
 foreach($gf_sql_res as $row){
   $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
 $dataArr[$row['COMPANY_ID']]['RCV_QTY']+=$row['RCV_QTY'];
 $dataArr[$row['COMPANY_ID']]['ISS_QTY']+=$row['ISS_QTY'];
 $dataArr[$row['COMPANY_ID']]['RCV_RATE_QTY']+=$row['RCV_RATE_QTY'];
 $dataArr[$row['COMPANY_ID']]['ISSUE_RATE_QTY']+=$row['ISSUE_RATE_QTY'];
 $dataArr[$row['COMPANY_ID']]['TRANS_IN_QTY']+=$row['TRANS_IN_QTY'];
 $dataArr[$row['COMPANY_ID']]['TRANS_OUT_QTY']+=$row['TRANS_OUT_QTY'];
 

     }  

     $recv_tot_qty=$dataArr[$row['COMPANY_ID']]['RCV_QTY'] + $dataArr[$row['COMPANY_ID']]['ISSUE_RATE_QTY'] + $$dataArr[$row['COMPANY_ID']]['TRANS_IN_QTY'];
     

     $iss_tot_qty=$dataArr[$row['COMPANY_ID']]['ISS_QTY']+$dataArr[$row['COMPANY_ID']]['RCV_RATE_QTY']+$dataArr[$row['COMPANY_ID']]['TRANS_OUT_QTY'];


     $stock_qty=$recv_tot_qty - $iss_tot_qty;

   //echo $stock_qty;die;
      // echo "<pre>";
      // print_r($dataArr); 
      //   echo "</pre>";die();  

 


 
   //GF TTL (TOTAL ISSUE ANF RCV QTY)
   $date_cond	=" and f.process_end_date between '".$previous_date."' and '".$current_date."' ";

   $sql="SELECT b.trans_type,a.COMPANY_ID, b.po_breakdown_id, b.prod_id, (CASE WHEN b.trans_type = 1 THEN (b.quantity) ELSE 0 END) AS GF_RCV_QTY, (CASE WHEN b.trans_type = 2 THEN (b.quantity) ELSE 0 END) AS GF_ISS_QTY, (CASE WHEN b.trans_type = 3 THEN (b.quantity) ELSE 0 END) AS GF_RCV_RATE_QTY, (CASE WHEN b.trans_type = 4 THEN (b.quantity) ELSE 0 END) AS GF_ISSUE_RATE_QTY, (CASE WHEN b.trans_type = 5 THEN (b.quantity) ELSE 0 END) AS GF_TRANS_IN_QTY, (CASE WHEN b.trans_type = 6 THEN (b.quantity) ELSE 0 END) AS GF_TRANS_OUT_QTY FROM inv_transaction a, order_wise_pro_details b WHERE a.id = b.trans_id  AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND b.entry_form IN (2, 22, 13, 16, 45, 51, 58, 61, 80, 81, 82, 83, 84, 110, 183) AND a.item_category = 13 and a.company_id in ($company_ids) AND a.transaction_type IN (1, 2, 3, 4, 5, 6) AND a.transaction_date <= '" . $current_date . "'";

   //echo $sql;die;
   $gff_sql_res=sql_select($sql);
   
     
   foreach($gff_sql_res as $row){
      $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
      $dataArr[$row['COMPANY_ID']]['GF_RCV_QTY']+=$row['GF_RCV_QTY'];
     $dataArr[$row['COMPANY_ID']]['GF_ISS_QTY']+=$row['GF_ISS_QTY'];
     $dataArr[$row['COMPANY_ID']]['GF_RCV_RATE_QTY']+=$row['GF_RCV_RATE_QTY'];
     $dataArr[$row['COMPANY_ID']]['GF_ISSUE_RATE_QTY']+=$row['GF_ISSUE_RATE_QTY'];
     $dataArr[$row['COMPANY_ID']]['GF_TRANS_IN_QTY']+=$row['GF_TRANS_IN_QTY'];
     $dataArr[$row['COMPANY_ID']]['GF_TRANS_OUT_QTY']+=$row['GF_TRANS_OUT_QTY'];
   
  
       }  

      //  echo "<pre>";
      // print_r($dataArr); 
      //   echo "</pre>";die();  

  
       $gf_recv_tot_qty=$dataArr[$row['COMPANY_ID']]['GF_RCV_QTY'] + $dataArr[$row['COMPANY_ID']]['GF_ISSUE_RATE_QTY'] + $$dataArr[$row['COMPANY_ID']]['GF_TRANS_IN_QTY'];
       
  
       $gf_iss_tot_qty=$dataArr[$row['COMPANY_ID']]['GF_ISS_QTY']+$dataArr[$row['COMPANY_ID']]['GF_RCV_RATE_QTY']+$dataArr[$row['COMPANY_ID']]['GF_TRANS_OUT_QTY'];
  
  

        //Batch Received
   $date_cond	=" and a.receive_date between '".$previous_date."' and '".$current_date."' ";

   $sql="SELECT a.id, a.COMPANY_ID, a.receive_date, SUM(b.qc_pass_qnty) AS QC_PASS_QNTY FROM inv_receive_mas_batchroll a, pro_roll_details b, pro_grey_batch_dtls c WHERE a.id = b.mst_id AND a.id = c.mst_id AND c.id = b.dtls_id AND a.entry_form = 62 AND c.is_deleted = 0 AND c.status_active = 1 AND a.is_deleted = 0 AND a.status_active = 1 AND a.company_id in ($company_ids) $date_cond  GROUP BY a.id, a.receive_date, a.company_id";
   //echo $sql;die;
   
   $dying_wip_sql_res=sql_select($sql);
     
   foreach($dying_wip_sql_res as $row){
    
     $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
   $dataArr[$row['COMPANY_ID']]['QC_PASS_QNTY']+=$row['QC_PASS_QNTY'];

   }

    // echo "<pre>";
    //   print_r($dataArr); 
    //     echo "</pre>";die();  

  
   
     //FF Stock [rec qty]   
     $date_cond	=" and d.entry_date between '".$previous_date."' and '".$current_date."' ";

     $sql="SELECT a.id AS job_id, a.company_name as COMPANY_ID , (CASE WHEN d.entry_form IN (7,37,66,68) THEN d.quantity ELSE 0 END) AS FF_RECEIVE_QNTY, (CASE WHEN d.entry_form IN (52, 126) THEN d.quantity ELSE 0 END) AS FF_ISSUE_RTN_QNTY, (CASE WHEN d.entry_form IN (14,15,134,306) AND d.trans_type = 5 THEN d.quantity ELSE 0 END) AS FF_TRANS_IN_QNTY, (CASE WHEN d.entry_form IN (46) THEN d.quantity ELSE 0 END) AS ISSUE_RCV_RTN_QNTY, (CASE WHEN d.entry_form IN (14,15,134,306) AND d.trans_type = 6 THEN d.quantity ELSE 0 END) AS FF_TRNS_OUT_QNTY,(case when d.entry_form in (18,71) then d.quantity else 0 end) as FF_ISSUE_QNTY FROM wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d WHERE a.id = b.job_id AND d.po_breakdown_id = b.id AND c.id = d.trans_id AND a.company_name in($company_ids) AND d.entry_form IN (7,14,15,18,37,46,52,66,68,71,126,134,306) AND c.item_category = 2 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND c.transaction_date  <= '" . $current_date . "' AND a.company_name in($company_ids) ORDER BY a.buyer_name, b.GROUPING, a.job_no, d.color_id, c.transaction_date";

     //echo $sql;die;
     $ff_recv_sql_res=sql_select($sql);
       
     foreach($ff_recv_sql_res as $row){
      
       $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
     $dataArr[$row['COMPANY_ID']]['FF_RECEIVE_QNTY']+=$row['FF_RECEIVE_QNTY'];
     $dataArr[$row['COMPANY_ID']]['FF_ISSUE_QNTY']+=$row['FF_ISSUE_QNTY'];
     $dataArr[$row['COMPANY_ID']]['FF_ISSUE_RTN_QNTY']+=$row['FF_ISSUE_RTN_QNTY'];
     $dataArr[$row['COMPANY_ID']]['FF_TRANS_IN_QNTY']+=$row['FF_TRANS_IN_QNTY'];
     $dataArr[$row['COMPANY_ID']]['FF_TRNS_OUT_QNTY']+=$row['FF_TRNS_OUT_QNTY'];
     $dataArr[$row['COMPANY_ID']]['ISSUE_RCV_RTN_QNTY']+=$row['ISSUE_RCV_RTN_QNTY'];

     }
      //  echo "<pre>";
      // print_r($dataArr); 
      //   echo "</pre>";die();  

     $rec_qty_cal= $dataArr[$row['COMPANY_ID']]['FF_RECEIVE_QNTY'] + $dataArr[$row['COMPANY_ID']]['FF_ISSUE_RTN_QNTY'] + $dataArr[$row['COMPANY_ID']]['FF_TRANS_IN_QNTY'];


     $iss_qty_cal=$dataArr[$row['COMPANY_ID']]['FF_ISSUE_QNTY'] + $dataArr[$row['COMPANY_ID']]['ISSUE_RCV_RTN_QNTY']+ $dataArr[$row['COMPANY_ID']]['FF_TRNS_OUT_QNTY'];

     $ff_stock=$rec_qty_cal-$iss_qty_cal;

     $ff_total_recv= $dataArr[$row['COMPANY_ID']]['FF_RECEIVE_QNTY'] + $dataArr[$row['COMPANY_ID']]['FF_ISSUE_RTN_QNTY'];

     $ff_total_issue=$dataArr[$row['COMPANY_ID']]['FF_ISSUE_QNTY'] + $dataArr[$row['COMPANY_ID']]['ISSUE_RCV_RTN_QNTY'];

    //echo  $ff_stock;die;
   

      //FF Received [Sample]
      $date_cond	=" and b.transaction_date between '".$previous_date."' and '".$current_date."' ";
      $date_cond1	=" and a.transfer_date between '".$previous_date."' and '".$current_date."' ";

      $sql="SELECT * FROM (SELECT a.entry_form,e.COMPANY_ID, d.barcode_no, d.po_breakdown_id, d.qnty, b.store_id, c.febric_description_id AS detar_id, c.body_part_id, c.color_id, c.gsm, c.width, c.stitch_length, c.machine_dia, c.machine_gg, c.yarn_count, b.cons_uom AS uom, MAX (b.transaction_date) AS max_date, b.prod_id, e.booking_no, SUM (CASE WHEN b.cons_uom = 12 THEN d.qnty ELSE 0 END) AS QNTYKG, SUM (CASE WHEN b.cons_uom = 1 THEN d.qnty ELSE 0 END) AS QNTYPCS, SUM (CASE WHEN b.cons_uom = 27 THEN d.qnty ELSE 0 END) AS qntyyds, 0 AS QNTYKGIN, 0 AS qntypcsIn, 0 AS qntyydsIn FROM inv_receive_master a, inv_transaction b, pro_grey_prod_entry_dtls c, wo_non_ord_samp_booking_mst e, pro_roll_details d WHERE a.id = c.mst_id AND a.id = b.mst_id AND b.id = c.trans_id AND a.entry_form IN (58) AND d.po_breakdown_id = e.id AND a.id = d.mst_id AND c.id = d.dtls_id AND d.entry_form IN (58) AND d.booking_without_order = 1 AND c.status_active = 1 AND c.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 AND a.status_active = 1 AND e.booking_type=4 AND a.is_deleted = 0 AND e.company_id in($company_ids) $date_cond GROUP BY a.entry_form,e.company_id, d.barcode_no, d.po_breakdown_id, d.qnty, b.store_id, c.febric_description_id, c.body_part_id, c.color_id, c.gsm, c.width, c.stitch_length, c.machine_dia, c.machine_gg, c.yarn_count, b.cons_uom, b.prod_id, e.booking_no UNION ALL SELECT a.entry_form,c.COMPANY_ID, d.barcode_no, d.po_breakdown_id, d.qnty, b.to_store AS store_id, b.feb_description_id AS detar_id, b.body_part_id, TO_CHAR (b.color_id) AS color_id, b.gsm, b.dia_width AS width, b.stitch_length, NULL AS machine_dia, NULL AS machine_gg, b.y_count AS yarn_count, e.unit_of_measure AS uom, MAX (a.transfer_date) AS max_date, b.to_prod_id AS prod_id, c.booking_no, 0 AS qntykg, 0 AS qntypcs, 0 AS qntyyds, SUM (CASE WHEN e.unit_of_measure = 12 THEN d.qnty ELSE 0 END) AS QNTYKGIN, SUM (CASE WHEN e.unit_of_measure = 1 THEN d.qnty ELSE 0 END) AS qntypcsIn, SUM (CASE WHEN e.unit_of_measure = 27 THEN d.qnty ELSE 0 END) AS qntyydsIn FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, wo_non_ord_samp_booking_mst c, pro_roll_details d, product_details_master e WHERE a.to_company = 17 AND a.id = b.mst_id AND c.id = a.to_order_id AND a.id = d.mst_id AND b.id = d.dtls_id AND b.to_prod_id = e.id AND d.entry_form IN (110, 180) AND c.company_id in($company_ids) $date_cond1 AND a.transfer_criteria IN (6, 8) AND a.entry_form IN (110, 180) AND a.item_category = 13 AND a.status_active = 1 AND c.booking_type=4 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 GROUP BY a.entry_form,c.company_id, d.barcode_no, d.po_breakdown_id, d.qnty, b.to_store, b.feb_description_id, b.body_part_id, b.color_id, b.gsm, b.dia_width, b.stitch_length, b.y_count, e.unit_of_measure, b.to_prod_id, c.booking_no) ORDER BY detar_id, color_id ASC";

     //echo $sql;die;
      $ff_samp_qty_sql_res=sql_select($sql);
        
      foreach($ff_samp_qty_sql_res as $row){
       
        $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
      $dataArr[$row['COMPANY_ID']]['QNTYKG']+=$row['QNTYKG'];
      $dataArr[$row['COMPANY_ID']]['QNTYKGIN']+=$row['QNTYKGIN'];
 
      }
      //   echo "<pre>";
      // print_r($dataArr); 
      //   echo "</pre>";die();  


          //FF Received [Sample]
      $date_cond	=" and e.transaction_date between '".$previous_date."' and '".$current_date."' ";
      $sql="SELECT c.po_breakdown_id,e.COMPANY_ID, c.barcode_no, a.unit_of_measure as uom, max(e.transaction_date) as max_date, sum(case when a.unit_of_measure=12 then c.qnty else 0 end) as QNTYKGISSUERTN, sum(case when a.unit_of_measure=1 then c.qnty else 0 end) as qntypcsIssueRtn, sum(case when a.unit_of_measure=27 then c.qnty else 0 end) as qntyydsIssueRtn from pro_roll_details c, inv_receive_master d, inv_transaction e,pro_grey_prod_entry_dtls f, product_details_master a, tmp_barcode_no b where c.entry_form=84 and c.mst_id = d.id and d.id = e.mst_id and c.dtls_id=f.id and e.id=f.trans_id and F.PROD_ID=a.id and C.BARCODE_NO=B.BARCODE_NO and d.entry_form=84 and c.status_active=1 and c.is_deleted=0 and e.transaction_type=4 and e.item_category=13 and e.status_active =1 and e.COMPANY_ID in($company_ids) and e.is_deleted=0 $date_cond  and f.status_active =1 and b.userid=165 and f.is_deleted=0 group by c.po_breakdown_id,e.company_id, c.barcode_no, a.unit_of_measure";

      //echo $sql;die;
      $printing_wip_prod_qty_sql_res=sql_select($sql);
        
      foreach($printing_wip_prod_qty_sql_res as $row){
       
        $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
      $dataArr[$row['COMPANY_ID']]['QNTYKGISSUERTN']+=$row['QNTYKGISSUERTN'];
 
      }

      $total_recv_kg=$dataArr[$row['COMPANY_ID']]['QNTYKG'] + $dataArr[$row['COMPANY_ID']]['QNTYKGIN'] + $dataArr[$row['COMPANY_ID']]['QNTYKGISSUERTN'];

      //echo $total_recv_kg;die;

      // echo $total_recv_kg;die;
      //   echo "<pre>";
      // print_r($dataArr); 
      //   echo "</pre>";die();  

  //FF Received [Sample][issue]
  $date_cond	=" and e.transaction_date between '".$previous_date."' and '".$current_date."' ";
   $sql="SELECT c.po_breakdown_id,a.COMPANY_ID, c.barcode_no, e.store_id AS store_name, MAX(e.transaction_date) AS max_date, a.unit_of_measure AS uom, SUM(CASE WHEN a.unit_of_measure = 12 THEN c.qnty ELSE 0 END) AS QNTYKGISSUE, SUM(CASE WHEN a.unit_of_measure = 1 THEN c.qnty ELSE 0 END) AS qntypcsIssue, SUM(CASE WHEN a.unit_of_measure = 27 THEN c.qnty ELSE 0 END) AS qntyydsIssue FROM pro_roll_details c, inv_grey_fabric_issue_dtls d, inv_transaction e, wo_non_ord_samp_booking_mst f, tmp_barcode_no b, product_details_master a WHERE c.dtls_id = d.id AND c.mst_id = d.mst_id AND c.po_breakdown_id = f.id AND d.trans_id = e.id AND c.barcode_no = b.barcode_no AND f.booking_type=4 AND b.userid = 165 and a.COMPANY_ID in($company_ids) $date_cond   AND e.transaction_type = 2 AND e.prod_id = a.id AND c.entry_form = 61 AND c.status_active = 1 AND c.is_deleted = 0 AND c.booking_without_order = 1 GROUP BY c.po_breakdown_id,a.COMPANY_ID, c.barcode_no, c.qnty, e.store_id, a.unit_of_measure";

   //echo $sql;die;
   $printing_wip_job_qty_sql_res=sql_select($sql);
     
   foreach($printing_wip_job_qty_sql_res as $row){
   
   $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
   $dataArr[$row['COMPANY_ID']]['QNTYKGISSUE']+=$row['QNTYKGISSUE'];

  }
    //  echo "<pre>";
    //   print_r($dataArr); 
    //     echo "</pre>";die();  

// // echo $printing_wip;die;

//FF Received [Sample][trans_out]
$date_cond	=" and d.transfer_date between '".$previous_date."' and '".$current_date."' ";
$sql="SELECT d.from_order_id AS po_breakdown_id, c.COMPANY_ID, c.barcode_no, e.unit_of_measure AS uom, MAX(d.transfer_date) AS max_date, SUM(CASE WHEN e.unit_of_measure = 12 THEN c.qnty ELSE 0 END) AS QNTYKGTRANSOUT, SUM(CASE WHEN e.unit_of_measure = 1 THEN c.qnty ELSE 0 END) AS qntypcsTransOut, SUM(CASE WHEN e.unit_of_measure = 27 THEN c.qnty ELSE 0 END) AS qntyydsTransOut FROM inv_item_transfer_dtls b, pro_roll_details c, inv_item_transfer_mst d, product_details_master e, wo_non_ord_samp_booking_mst f WHERE b.id = c.dtls_id AND c.po_breakdown_id = f.id AND b.from_prod_id = e.id AND d.transfer_criteria IN (7, 8) AND c.entry_form IN (183, 180) AND c.status_active = 1 AND c.is_deleted = 0 AND b.status_active = 1 and c.COMPANY_ID in($company_ids) AND b.is_deleted = 0 AND c.booking_without_order = 1 AND b.mst_id = d.id AND d.entry_form IN (183, 180) GROUP BY d.from_order_id, c.barcode_no, e.unit_of_measure, c.COMPANY_ID";
//echo $sql;die;
$emb_wip_job_qty_sql_res=sql_select($sql);
  
foreach($emb_wip_job_qty_sql_res as $row){

$dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
$dataArr[$row['COMPANY_ID']]['QNTYKGTRANSOUT']+=$row['QNTYKGTRANSOUT'];

}

$total_issue_kg=$dataArr[$row['COMPANY_ID']]['QNTYKGISSUE'] + $dataArr[$row['COMPANY_ID']]['QNTYKGTRANSOUT'];


$stock_in_hand_kg=$total_recv_kg - $total_issue_kg;
//echo $stock_in_hand_kg;die;

//FF Stock [Leftover] (rcv)
$date_cond	=" and b.transaction_date between '".$previous_date."' and '".$current_date."' ";
$date_cond2	=" and a.transfer_date between '".$previous_date."' and '".$current_date."' ";

$sql="SELECT * FROM (SELECT a.entry_form,a.COMPANY_ID, SUM(CASE WHEN f.unit_of_measure = 12 THEN d.qnty ELSE 0 END) AS QNTYFFKG, SUM(CASE WHEN f.unit_of_measure = 1 THEN d.qnty ELSE 0 END) AS qntypcs, SUM(CASE WHEN f.unit_of_measure = 27 THEN d.qnty ELSE 0 END) AS qntyyds, 0 AS FF_QNTYKGIN, 0 AS qntypcsin, 0 AS qntyydsin FROM inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c, pro_roll_details d, pro_batch_create_mst e, product_details_master f WHERE a.entry_form IN (68) AND a.id = b.mst_id AND b.id = c.trans_id AND c.id = d.dtls_id AND d.entry_form IN (68) AND d.status_active = 1 AND d.is_deleted = 0 AND a.company_id in($company_ids)  $date_cond  AND c.batch_id = e.id AND c.prod_id = f.id AND a.status_active = 1 AND c.status_active = 1 AND d.status_active = 1 AND b.status_active = 1 AND d.is_sales <> 1 GROUP BY a.entry_form,a.company_id UNION ALL SELECT a.entry_form,a.company_id, 0 AS FF_QNTYKG, 0 AS qntypcs, 0 AS qntyyds, SUM(CASE WHEN e.unit_of_measure = 12 THEN d.qnty ELSE 0 END) AS FF_QNTYKGIN, SUM(CASE WHEN e.unit_of_measure = 1 THEN d.qnty ELSE 0 END) AS qntypcsIn, SUM(CASE WHEN e.unit_of_measure = 27 THEN d.qnty ELSE 0 END) AS qntyydsIn FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details d, product_details_master e, pro_batch_create_mst c WHERE a.id = b.mst_id AND b.id = d.dtls_id AND b.from_prod_id = e.id AND B.BATCH_ID = c.id AND a.entry_form IN (134) AND d.entry_form IN (134) AND a.transfer_criteria IN (1, 2, 4) AND d.status_active = 1 AND d.is_deleted = 0 AND NVL(d.booking_without_order, 0) = 0 AND a.company_id in($company_ids)  $date_cond2 GROUP BY a.entry_form,a.company_id)  ";

 //echo $sql;die;
$ff_stock_lftover_qty_sql_res=sql_select($sql);
  
foreach($ff_stock_lftover_qty_sql_res as $row){

$dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
$dataArr[$row['COMPANY_ID']]['QNTYFFKG']+=$row['QNTYFFKG'];
$dataArr[$row['COMPANY_ID']]['FF_QNTYKGIN']+=$row['FF_QNTYKGIN'];
$dataArr[$row['COMPANY_ID']]['FF_QNTYKG']+=$row['FF_QNTYKG'];

}

    $ff_tot_rcv=$dataArr[$row['COMPANY_ID']]['QNTYFFKG'] + $dataArr[$row['COMPANY_ID']]['FF_QNTYKGIN'];
   
//FF Stock [Leftover] (issue)
$date_cond	=" and e.transaction_date between '".$previous_date."' and '".$current_date."' ";

$sql=" SELECT c.po_breakdown_id, e.COMPANY_ID, c.barcode_no, e.store_id AS store_name, MAX(e.transaction_date) AS max_date, a.unit_of_measure AS uom, SUM(CASE WHEN a.unit_of_measure = 12 THEN c.qnty ELSE 0 END) AS FF_QNTYKGISSUE, SUM(CASE WHEN a.unit_of_measure = 1 THEN c.qnty ELSE 0 END) AS qntypcsIssue, SUM(CASE WHEN a.unit_of_measure = 27 THEN c.qnty ELSE 0 END) AS qntyydsIssue FROM pro_roll_details c, inv_finish_fabric_issue_dtls d, inv_transaction e, tmp_barcode_no b, product_details_master a WHERE c.dtls_id = d.id AND c.mst_id = d.mst_id AND d.trans_id = e.id AND c.barcode_no = b.barcode_no AND b.userid = 165 AND e.transaction_type = 2 AND e.prod_id = a.id AND c.entry_form = 71 AND c.status_active = 1 AND c.is_deleted = 0 AND c.booking_without_order = 0 AND e.COMPANY_ID in($company_ids) $date_cond GROUP BY c.po_breakdown_id, e.COMPANY_ID, c.barcode_no, c.qnty, e.store_id, a.unit_of_measure";

//echo $sql;die;
$emb_wip_job_qty_sql_res=sql_select($sql);

foreach($emb_wip_job_qty_sql_res as $row){

$dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
$dataArr[$row['COMPANY_ID']]['FF_QNTYKGISSUE']+=$row['FF_QNTYKGISSUE'];


}


$ff_ttl_issue=$dataArr[$row['COMPANY_ID']]['FF_QNTYKGISSUE'] + $dataArr[$row['COMPANY_ID']]['FF_QNTYKG']+=$row['FF_QNTYKG'];


$ff_leftover_qty=$ff_tot_rcv - $ff_ttl_issue;
//echo $ff_leftover_qty;die;

//Poly [Pcs] 


  
 ?>

<!DOCTYPE html>
<html>

<body>
  <table>
  <?

   foreach ($dataArr as $company => $row) 
   {    
   
          ?>    <tr>
                <th><?=$company_lib[$row['COMPANY_ID']];?></th>
                <th></th>
        
                 </tr> 
                  <tr>
                  
                    <th>between :<?=$previous_date?> and <?=$current_date?></th>
                  
                  </tr>
                  <tr>
                    <td>Yarn Stock.</td>
                    <td> <?=$yarn_stock ;?></td>
                    
                  </tr> <tr>
                    <td>Yarn TTL Received </td>
                    <td><?=number_format($totalRcv, 2);?>;</td>
                    
                  </tr> 
                  <tr>
                    <td>Yarn TTL Issued </td>
                    <td><?=number_format($totalIssue, 2);?></td>
                    
                  </tr>
                  <tr>
                    <td>Yarn Dyeing Stock [Dyed Yarn]  </td>
                    <td><?=number_format($stock_openingBalance, 2);?></td>
                    
                  </tr>

                  <tr>
                    <td>GF Stock [Running Order]  </td>
                    <td><?=number_format($stock_qty, 2);?></td>
                    
                  </tr>

                  <tr>
                    <td>GF TTL Received  </td>
                    <td><?=number_format($gf_recv_tot_qty, 2);?></td>
                    
                  </tr>
                  <tr>
                    <td>GF TTL Issued  </td>
                    <td><?=number_format($gf_iss_tot_qty, 2);?></td>
                    
                  </tr>
                  <tr>
                    <td>GF TTL Transfer Received  </td>
                    <td><?=$row['GF_TRANS_IN_QTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>GF TTL Transfer Issued  </td>
                    <td><?=$row['GF_TRANS_OUT_QTY'];?></td>
                    
                  </tr>

                  <tr>
                    <td>Batch Received </td>
                    <td><?=$row['QC_PASS_QNTY'];?></td>
                    
                  </tr>

                  <tr>
                    <td>FF Stock </td>
                    <td><?=number_format($ff_stock, 2);?></td>
                    
                  </tr>
                  <tr>
                    <td>FF TTL Received </td>
                    <td><?=number_format($ff_total_recv, 2);?></td>
                    
                  </tr>

                  <tr>
                    <td>FF TTL Issued</td>
                    <td><?=number_format($ff_total_issue, 2);?></td>
                    
                  </tr>
                  <tr>
                    <td>FF TTL Trans. Received </td>
                    <td><?=$row['FF_TRANS_IN_QNTY'];?></td> 
                    
                  </tr><tr>
                    <td>FF TTL Trans. Issued.</td>
                    <td><?=$row['FF_TRNS_OUT_QNTY'];?></td>
                    
                  </tr>

                  </tr><tr>
                    <td>FF Received [Sample]</td>
                    <td><?=number_format($stock_in_hand_kg, 2);?></td>
                    
                  </tr>

                  </tr><tr>
                    <td>FF Stock [Leftover]</td>
                    <td><?=number_format($ff_leftover_qty, 2);?></td>
                    
                  </tr>
                 
                 
          <?
   
        }   
               //$grandtotal_amount+=$row['receive_qnty'];
   
     // $i++;
  ?>
  </table>
</body>


</html>

