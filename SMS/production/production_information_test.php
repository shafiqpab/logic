
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

  

$date_cond	=" and a.RECEIVE_DATE between '".$previous_date."' and '".$current_date."' ";

  $sql = "SELECT a.ID,a.COMPANY_ID,b.PROD_ID,a.RECV_NUMBER_PREFIX_NUM, a.RECV_NUMBER, a.BOOKING_NO, a.BUYER_ID,a.KNITTING_SOURCE,a.KNITTING_COMPANY,a.RECEIVE_DATE, a.CHALLAN_NO, a.WITHIN_GROUP, (CASE WHEN A.KNITTING_SOURCE=1 THEN (b.GREY_RECEIVE_QNTY) ELSE 0 END) AS IN_HOUSE_QTY ,
	(CASE WHEN a.KNITTING_SOURCE=3 THEN (b.GREY_RECEIVE_QNTY) ELSE 0 END) AS OUTBOUND_QTY FROM INV_RECEIVE_MASTER a,PRO_GREY_PROD_ENTRY_DTLS b
	WHERE a.ID=b.MST_ID AND a.ENTRY_FORM=2  $date_cond AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 AND a.COMPANY_ID in($company_ids)  AND b.STATUS_ACTIVE=1 AND b.IS_DELETED=0
	 ORDER BY a.ID DESC";
   // echo $sql;die();
    $delivery_sql_res=sql_select($sql);
    $dataArr=array();
  
    foreach($delivery_sql_res as $row){
         $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];

        $dataArr[$row['COMPANY_ID']]['IN_HOUSE_QTY']+=$row['IN_HOUSE_QTY'];
        $dataArr[$row['COMPANY_ID']]['OUTBOUND_QTY']+=$row['OUTBOUND_QTY'];
        
        
      }

    

      $load_unload_cond = "and a.LOAD_UNLOAD_ID in(2)";
      $date_cond	=" and a.process_end_date between '".$previous_date."' and '".$current_date."' ";
    //Dying production
    $sql = "select distinct a.ID,a.COMPANY_ID,a.BATCH_ID,b.PROD_ID,a.BATCH_NO, a.PROCESS_END_DATE,a.BATCH_NO,c.EXTENTION_NO,(CASE WHEN c.EXTENTION_NO IS NULL THEN  (b.PRODUCTION_QTY) END)
    AS PROD_QTY,(CASE WHEN c.EXTENTION_NO IS NOT NULL THEN(b.PRODUCTION_QTY) END)
    AS PROCESS_PROD_QTY from PRO_FAB_SUBPROCESS a,PRO_FAB_SUBPROCESS_DTLS b,pro_batch_create_mst c where a.ID=b.MST_ID and a.BATCH_ID=c.ID  and a.IS_DELETED=0 $date_cond
    and a.COMPANY_ID in($company_ids) and a.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 $load_unload_cond order by a.ID";
     //echo  $sql;die;
    $dyeing_sql_res=sql_select($sql);
   

    foreach($dyeing_sql_res as $row){
      $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
      $dataArr[$row['COMPANY_ID']]['PROD_QTY']+=$row['PROD_QTY'];
      $dataArr[$row['COMPANY_ID']]['PROCESS_PROD_QTY']+=$row['PROCESS_PROD_QTY'];
    
    }

      // echo "<pre>";
      // print_r($dataArr); 
      //   echo "</pre>";die();  


//..........Finish Fabric Production.......

$date_cond	=" and a.RECEIVE_DATE between '".$previous_date."' and '".$current_date."' ";
$sql = "SELECT a.ID,a.COMPANY_ID, a.RECV_NUMBER, a.KNITTING_SOURCE, a.KNITTING_COMPANY, a.RECEIVE_DATE, a.STORE_ID, a.CHALLAN_NO, d.BATCH_NO, c.PRODUCTION_QTY
	FROM inv_receive_master a,pro_roll_details b, pro_finish_fabric_rcv_dtls c, pro_batch_create_mst d 
	WHERE a.ID=b.MST_ID and  a.COMPANY_ID in($company_ids) AND b.DTLS_ID=c.ID AND a.ID=c.MST_ID AND c.BATCH_ID=d.ID AND a.ENTRY_FORM=66 AND b.ENTRY_FORM=66  AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0  AND b.STATUS_ACTIVE=1 AND b.IS_DELETED=0 AND C.STATUS_ACTIVE=1    $date_cond  AND  c.IS_DELETED=0
	
	ORDER BY a.ID";
//echo $sql;die;

$finish_sql_res=sql_select($sql);

foreach($finish_sql_res as $row){
  $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
  $dataArr[$row['COMPANY_ID']]['PRODUCTION_QTY']+=$row['PRODUCTION_QTY'];
 
}

 
//..........Cutting Production.......
$date_cond	=" and a.PRODUCTION_DATE between '".$previous_date."' and '".$current_date."' ";
$sql="SELECT b.PRODUCTION_QNTY as CUT_QNTY,a.PRODUCTION_DATE,a.COMPANY_ID from pro_garments_production_mst a,pro_garments_production_dtls b where a.ID=b.MST_ID and a.COMPANY_ID in($company_ids) and a.production_type=1 and b.PRODUCTION_TYPE=1 and a.status_active=1 and a.is_deleted=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  $date_cond ";

//echo $sql;die;
   $finish_sql_res=sql_select($sql);
  foreach($finish_sql_res as $row){
  $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
   $dataArr[$row['COMPANY_ID']]['CUT_QNTY']+=$row['CUT_QNTY'];
    }
 
      

      //..........PRINT Production.......
      $date_cond	=" and a.DELIVERY_DATE between '".$previous_date."' and '".$current_date."' ";
      $sql = "SELECT a.ID, a.SYS_NUMBER,a.COMPANY_ID,b.PRODUCTION_QUANTITY as PRINT_QNTY ,a.DELIVERY_DATE from pro_gmts_delivery_mst a,pro_garments_production_mst b where a.ID=b.DELIVERY_MST_ID and  a.PRODUCTION_TYPE=3 and a.PRODUCTION_TYPE=b.PRODUCTION_TYPE and a.EMBEL_NAME=1 and a.COMPANY_ID in($company_ids) and a.status_active=1  and a.IS_DELETED=0 $date_cond  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 ";
      //echo $sql;die;

      $print_sql_res=sql_select($sql);
        foreach($print_sql_res as $row){
       $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
      $dataArr[$row['COMPANY_ID']]['PRINT_QNTY']+=$row['PRINT_QNTY'];
       
    }

      //  echo "<pre>";
      // print_r($dataArr); 
      //   echo "</pre>";die();  
      
      //..........Embro Production.......
      $date_cond	=" and a.DELIVERY_DATE between '".$previous_date."' and '".$current_date."' ";
      $sql = "select a.ID,a.COMPANY_ID, a.SYS_NUMBER, a.DELIVERY_DATE,b.PRODUCTION_QUANTITY as EMBRO_QNTY  from pro_gmts_delivery_mst a,PRO_GARMENTS_PRODUCTION_MST b where a.ID=b.DELIVERY_MST_ID and  a.PRODUCTION_TYPE=3 and a.EMBEL_NAME=2 and a.COMPANY_ID in($company_ids) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  $date_cond ";
      //echo $sql;die; 
       
      $embro_sql_res=sql_select($sql);
      foreach($embro_sql_res as $row){
      $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
      $dataArr[$row['COMPANY_ID']]['EMBRO_QNTY']+=$row['EMBRO_QNTY'];
          }

     //...Sewing Completed...
     $date_cond	=" and a.PRODUCTION_DATE between '".$previous_date."' and '".$current_date."' ";
      $sql = " SELECT b.PRODUCTION_QNTY as SEWING_QNTY,a.COMPANY_ID, a.PRODUCTION_DATE,a.PO_BREAK_DOWN_ID,a.ITEM_NUMBER_ID,a.COUNTRY_ID, d.COLOR_NUMBER_ID from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d where a.ID=b.MST_ID and b.COLOR_SIZE_BREAK_DOWN_ID=d.ID and a.COMPANY_ID in($company_ids) and b.PRODUCTION_TYPE=5  $date_cond  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and d.STATUS_ACTIVE in(1,2,3) and d.IS_DELETED=0 order by a.PO_BREAK_DOWN_ID,d.COLOR_NUMBER_ID ";
      //echo $sql;die;

      $sewing_sql_res=sql_select($sql);
      foreach($sewing_sql_res as $row){
        $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
      $dataArr[$row['COMPANY_ID']]['SEWING_QNTY']+=$row['SEWING_QNTY'];
          }

      //  echo "<pre>";
      // print_r($dataArr); 
      //   echo "</pre>";die();  


       //..........Poly  Production.......

       $date_cond	=" and a.PRODUCTION_DATE between '".$previous_date."' and '".$current_date."' ";

       $sql="SELECT a.id, a.PRODUCTION_DATE,a.COMPANY_ID, b.PRODUCTION_QNTY as POLY_QUANTITY from pro_garments_production_mst a,pro_garments_production_dtls b where a.ID=b.MST_ID and a.production_type='11' and b.PRODUCTION_TYPE='11'and  a.COMPANY_ID in($company_ids) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.status_active=1 and b.is_deleted=0  $date_cond  order by a.PRODUCTION_DATE, a.PRODUCTION_HOUR";
      
   //echo $sql;die; 
   $poly_sql_res=sql_select($sql);
   foreach($poly_sql_res as $row){
     $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
   $dataArr[$row['COMPANY_ID']]['POLY_QUANTITY']+=$row['POLY_QUANTITY'];
       }

  
 //..........Confirm and Projected Order.......(START)
 $date_cond	=" and a.PO_RECEIVED_DATE between '".$previous_date."' and '".$current_date."' ";

 $sql="SELECT a.JOB_NO_MST, sum(a.PO_QUANTITY) as PO_QUANTITY, sum(a.PO_QUANTITY*b.TOTAL_SET_QNTY) as PO_QUANTITY_PSC,
 sum(case when a.IS_CONFIRMED=1 then a.PO_QUANTITY*b.TOTAL_SET_QNTY else 0 end) as CONFIRM_QTY_PCS,
 sum(case when a.IS_CONFIRMED=2 then a.PO_QUANTITY*b.TOTAL_SET_QNTY else 0 end) as PROJEC_QTY_PCS,
 a.PACKING, b.COMPANY_NAME as COMPANY_ID , b.LOCATION_NAME, b.ORDER_UOM , b.TOTAL_SET_QNTY, a.PO_RECEIVED_DATE
 from wo_po_break_down a, wo_po_details_master b
 where a.JOB_ID=b.ID and  b.COMPANY_NAME in($company_ids)  $date_cond and a.STATUS_ACTIVE in(1,2,3) and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 
 group by a.JOB_NO_MST, a.PACKING, b.COMPANY_NAME, b.LOCATION_NAME, b.ORDER_UOM, b.TOTAL_SET_QNTY, a.PO_RECEIVED_DATE order by a.PO_RECEIVED_DATE";

//echo $sql;die;

$order_sql_res=sql_select($sql);
foreach($order_sql_res as $row){
$dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
$dataArr[$row['COMPANY_ID']]['CONFIRM_QTY_PCS']+=$row['CONFIRM_QTY_PCS'];
$dataArr[$row['COMPANY_ID']]['PROJEC_QTY_PCS']+=$row['PROJEC_QTY_PCS'];
}


$total_order = $dataArr[$row['COMPANY_ID']]['CONFIRM_QTY_PCS'] + $dataArr[$row['COMPANY_ID']]['PROJEC_QTY_PCS'];

//...EFR QTY..
$date_cond	=" and a.BOOKING_DATE between '".$previous_date."' and '".$current_date."' ";

$sql="SELECT a.COMPANY_ID, a.BOOKING_DATE, sum(b.GREY_FAB_QNTY) as SORT_GREY_QTY  from wo_booking_mst a,       wo_booking_dtls b where a.BOOKING_NO=b.BOOKING_NO and a.JOB_NO=b.JOB_NO and a.ENTRY_FORM=88 and  a.COMPANY_ID in($company_ids) $date_cond and a.ITEM_CATEGORY=2 and a.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.IS_DELETED=0 group by a.COMPANY_ID, a.BOOKING_DATE order by a.BOOKING_DATE";

   //echo $sql;die;
   $efr_sql_res=sql_select($sql);
   foreach($efr_sql_res as $row){
     $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
   $dataArr[$row['COMPANY_ID']]['SORT_GREY_QTY']+=$row['SORT_GREY_QTY'];
  
       }

  //...F. Fabric Deliver to Store [Kg]..
  $date_cond	=" and a.DELEVERY_DATE between '".$previous_date."' and '".$current_date."' ";
  $sql="SELECT SUM (b.QNTY) AS TOTAL_QTY,a.KNITTING_SOURCE,a.KNITTING_COMPANY as COMPANY_ID ,a.DELEVERY_DATE FROM pro_grey_prod_delivery_mst a, pro_roll_details b WHERE a.ENTRY_FORM = 67 AND a.ID = b.MST_ID $date_cond AND b.ENTRY_FORM = 67 and  a.knitting_company in($company_ids) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 GROUP BY a.knitting_source, a.knitting_company, a.delevery_date";

  //echo $sql;die;
  $feb_sql_res=sql_select($sql);
  foreach($feb_sql_res as $row){
    $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
  $dataArr[$row['COMPANY_ID']]['TOTAL_QTY']+=$row['TOTAL_QTY'];
 
      }       


// FF Received [Cutting] [Kg]
  $date_cond	=" and b.TRANSACTION_DATE between '".$previous_date."' and '".$current_date."' ";

 $sql="SELECT b.COMPANY_ID, b.TRANSACTION_DATE,sum(case when b.TRANSACTION_TYPE=2 and b.ITEM_CATEGORY=2 then b.CONS_QUANTITY else 0 end) as FIN_ROLL_ISSUE_QTY from inv_issue_master a, inv_transaction b where   a.ID=b.MST_ID  and b.ITEM_CATEGORY in(2) and b.COMPANY_ID in($company_ids)  $date_cond and b.TRANSACTION_TYPE in(2) and a.ENTRY_FORM=71 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 group by b.COMPANY_ID, b.TRANSACTION_DATE order by b.TRANSACTION_DATE";

 //echo $sql;die;
 $ff_sql_res=sql_select($sql);
 foreach($ff_sql_res as $row){
   $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
 $dataArr[$row['COMPANY_ID']]['FIN_ROLL_ISSUE_QTY']+=$row['FIN_ROLL_ISSUE_QTY'];

     }       

 // Dyeing Balance [Kg] [Req Qty]
 $date_cond	=" and a.BOOKING_DATE between '".$previous_date."' and '".$current_date."' ";

 $sql="SELECT   a.COMPANY_ID, a.BOOKING_DATE, b.grey_fab_qnty as REQ_QNTY from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no $date_cond
 and a.COMPANY_ID in($company_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";

 //echo $sql;die;
 $req_sql_res=sql_select($sql);
 foreach($req_sql_res as $row){
   $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
 $dataArr[$row['COMPANY_ID']]['REQ_QNTY']+=$row['REQ_QNTY'];

     }  

 // Dyeing Balance [Kg] [Todays Qty]
 $date_cond	=" and a.receive_date between '".$previous_date."' and '".$current_date."' ";
$sql="select a.COMPANY_ID, a.receive_date, sum(b.grey_receive_qnty) as CURRENT_PROD_QTY  from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.COMPANY_ID in($company_ids)  $date_cond and a.status_active=1 and a.is_deleted=0 and a.entry_form=2 and a.receive_basis=2   group by a.COMPANY_ID,a.receive_date";

 //echo $sql;die;
 $tod_sql_res=sql_select($sql);
 foreach($tod_sql_res as $row){
   $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
 $dataArr[$row['COMPANY_ID']]['CURRENT_PROD_QTY']+=$row['CURRENT_PROD_QTY'];

     }  
     
     $dyeing_Balance=$dataArr[$row['COMPANY_ID']]['REQ_QNTY']-$dataArr[$row['COMPANY_ID']]['CURRENT_PROD_QTY'];


   //Dyeing Finishing Balance [Kg]
   $date_cond	=" and a.batch_date between '".$previous_date."' and '".$current_date."' ";

 $sql="select a.batch_date as batch_date,a.company_id as COMPANY_ID,sum(b.batch_qnty) as BATCH_QTY
 from pro_batch_create_dtls b,pro_batch_create_mst a where b.mst_id=a.id $date_cond and a.COMPANY_ID in($company_ids) and a.status_active=1 and b.status_active=1
 group by a.batch_date,a.company_id";

 //echo $sql;die;
 $finish_sql_res=sql_select($sql);
   
 foreach($finish_sql_res as $row){
   $subpro_prod_qty=$row[csf("batch_qty")];
   $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
 $dataArr[$row['COMPANY_ID']]['BATCH_QTY']+=$row['BATCH_QTY'];
 $dataArr[$row['COMPANY_ID']]['PROD_QTY']+=$subpro_prod_qty;

     }  

 $finishing_Balance=$dataArr[$row['COMPANY_ID']]['BATCH_QTY']-$dataArr[$row['COMPANY_ID']]['PROD_QTY'];


 
   //Dyeing [White/Wash] [Kg]
   $date_cond	=" and f.process_end_date between '".$previous_date."' and '".$current_date."' ";

   $sql="SELECT  a.COMPANY_ID, SUM (b.BATCH_QNTY) AS WASH_QNTY,a.entry_form FROM pro_batch_create_dtls b,
   pro_fab_subprocess   f,pro_batch_create_mst a WHERE     f.batch_id = a.id AND f.batch_id = b.mst_id AND  a.company_id in($company_ids) $date_cond  AND a.id = b.mst_id and   a.color_range_id=7 AND f.load_unload_id = 2 AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0  AND f.status_active = 1 AND f.is_deleted = 0 GROUP BY a.company_id,a.entry_form";

   //echo $sql;die;
   $finish_sql_res=sql_select($sql);
     
   foreach($finish_sql_res as $row){
    
     $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
   $dataArr[$row['COMPANY_ID']]['WASH_QNTY']+=$row['WASH_QNTY'];

   }
  

        //Dyeing Balance [Kg]
   $date_cond	=" and a.process_end_date between '".$previous_date."' and '".$current_date."' ";
   $sql="SELECT a.company_id  AS COMPANY_ID,a.production_date AS process_end_date,SUM (c.batch_qnty) AS QNTY
   FROM pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c WHERE     a.batch_id = b.id
   AND a.entry_form = 35 AND a.load_unload_id = 2 AND b.id = c.mst_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND b.batch_against IN (1, 2) AND b.is_sales != 1 $date_cond AND a.company_id in($company_ids) GROUP BY a.company_id,a.production_date";

   //echo $sql;die;
   $dying_Balance_sql_res=sql_select($sql);
     
   foreach($dying_Balance_sql_res as $row){
    
     $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
   $dataArr[$row['COMPANY_ID']]['QNTY']+=$row['QNTY'];

   }

   $dying_Balance=$dataArr[$row['COMPANY_ID']]['QNTY']-$dataArr[$row['COMPANY_ID']]['QNTY'];

  
   
     //Cutting Balance [order qty]   
     $date_cond	=" and d.entry_date between '".$previous_date."' and '".$current_date."' ";

     $sql="SELECT d.working_company_id AS COMPANY_ID,d.LOCATION_ID,d.FLOOR_ID,sum(c.order_quantity ) AS CUTTING_ORDER_QUANTITY,e.gmt_item_id AS ITEM_ID FROM wo_po_color_size_breakdown c,ppl_cut_lay_mst d,ppl_cut_lay_dtls e WHERE d.id = e.mst_id AND d.job_no = c.job_no_mst and    c.status_active IN (1, 2, 3) AND c.is_deleted = 0 and d.working_company_id in($company_ids) AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0   $date_cond  group by d.working_company_id,d.LOCATION_ID,d.FLOOR_ID,e.gmt_item_id";

     //echo $sql;die;
     $cutting_order_sql_res=sql_select($sql);
       
     foreach($cutting_order_sql_res as $row){
      
       $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
     $dataArr[$row['COMPANY_ID']]['CUTTING_ORDER_QUANTITY']+=$row['CUTTING_ORDER_QUANTITY'];

     }
     
      //Cutting Balance [daily qty]  
      $date_cond	=" and d.entry_date between '".$previous_date."' and '".$current_date."' ";

      $sql="SELECT d.working_company_id AS COMPANY_ID, SUM(f.size_qty) as DAILY_QNTY FROM ppl_cut_lay_mst d, ppl_cut_lay_dtls e, ppl_cut_lay_bundle f WHERE     d.id = e.mst_id AND d.id = f.mst_id and d.working_company_id in($company_ids) AND e.id = f.dtls_id AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 AND f.status_active = 1 AND f.is_deleted = 0  $date_cond group by d.working_company_id";

     // echo $sql;die;
      $cutting_daily_qty_sql_res=sql_select($sql);
        
      foreach($cutting_daily_qty_sql_res as $row){
       
        $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
      $dataArr[$row['COMPANY_ID']]['DAILY_QNTY']+=$row['DAILY_QNTY'];
 
      }
      $cutting_Balance=$dataArr[$row['COMPANY_ID']]['CUTTING_ORDER_QUANTITY']-$dataArr[$row['COMPANY_ID']]['DAILY_QNTY'];


          //printing_Balance (prod_qty)
      $date_cond	=" and b.production_date between '".$previous_date."' and '".$current_date."' ";
      $sql="SELECT a.COMPANY_ID,b.production_date,SUM (b.qcpass_qty)     AS QCPASS_QTY FROM subcon_embel_production_mst a, subcon_embel_production_dtls b WHERE a.id = b.mst_id $date_cond  and a.COMPANY_ID in($company_ids) AND a.entry_form =223 AND a.is_deleted = 0 AND a.status_active = 1 AND b.is_deleted = 0 AND b.status_active = 1 GROUP BY a.COMPANY_ID,b.production_date";

      //echo $sql;die;
      $printing_Balance_prod_qty_sql_res=sql_select($sql);
        
      foreach($printing_Balance_prod_qty_sql_res as $row){
       
        $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
      $dataArr[$row['COMPANY_ID']]['QCPASS_QTY']+=$row['QCPASS_QTY'];
 
      }

  //printing_Balance (req_qty)
  $date_cond	=" and a.RECEIVE_DATE between '".$previous_date."' and '".$current_date."' ";
   $sql="SELECT a.id, b.order_quantity as JOB_QUANTITY, b.amount,a.RECEIVE_DATE,a.COMPANY_ID
   FROM subcon_ord_mst a, subcon_ord_dtls b WHERE a.id = b.mst_id and a.COMPANY_ID in($company_ids) and a.ENTRY_Form=204  $date_cond";

  // echo $sql;die;
   $printing_Balance_job_qty_sql_res=sql_select($sql);
     
   foreach($printing_Balance_job_qty_sql_res as $row){
   
   $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
   $dataArr[$row['COMPANY_ID']]['JOB_QUANTITY']+=$row['JOB_QUANTITY'];

  }
  $printing_Balance=$dataArr[$row['COMPANY_ID']]['QCPASS_QTY']-$dataArr[$row['COMPANY_ID']]['JOB_QUANTITY'];

// echo $printing_Balance;die;

//Embroidery Balance (qc_qty)
$date_cond	=" and b.production_date between '".$previous_date."' and '".$current_date."' ";
$sql="SELECT a.job_no,a.COMPANY_ID,SUM (b.qcpass_qty)     AS EMB_QCPASS_QTY FROM subcon_embel_production_mst a,
subcon_embel_production_dtls b WHERE     a.id = b.mst_id $date_cond AND a.entry_form =315 and  a.COMPANY_ID in($company_ids) AND a.is_deleted = 0 AND a.status_active = 1
AND b.is_deleted = 0 AND b.status_active = 1 GROUP BY a.job_no,a.COMPANY_ID";

$emb_Balance_job_qty_sql_res=sql_select($sql);
  
foreach($emb_Balance_job_qty_sql_res as $row){

$dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
$dataArr[$row['COMPANY_ID']]['EMB_QCPASS_QTY']+=$row['EMB_QCPASS_QTY'];

}

//Embroidery Balance (req_qty)
$date_cond	=" and a.RECEIVE_DATE between '".$previous_date."' and '".$current_date."' ";
$sql="SELECT a.id, a.COMPANY_ID,a.embellishment_job AS job_no,ROUND(SUM(b.order_quantity * 12))                AS EMB_REQ_QTY FROM subcon_ord_mst a,subcon_ord_dtls b WHERE     b.job_no_mst = a.embellishment_job AND a.id = b.mst_id AND a.entry_form = 311 AND a.company_id in($company_ids) $date_cond AND a.is_deleted = 0
GROUP BY a.id,a.embellishment_job,a.company_id";

// echo $sql;die;
$emb_Balance_job_qty_sql_res=sql_select($sql);
  
foreach($emb_Balance_job_qty_sql_res as $row){

$dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
$dataArr[$row['COMPANY_ID']]['EMB_REQ_QTY']+=$row['EMB_REQ_QTY'];

}
$embroydery_Balance=$dataArr[$row['COMPANY_ID']]['EMB_QCPASS_QTY']-$dataArr[$row['COMPANY_ID']]['EMB_REQ_QTY'];

//Sewing Balance [Pcs]
$date_cond	=" and d.production_date between '".$previous_date."' and '".$current_date."' ";
$sql=" select  a.COMPANY_NAME as COMPANY_ID,d.production_date,sum(case when d.production_type=4 and e.production_type=4 then e.production_qnty else 0 end ) as TODAY_SEWING_OUTPUT ,SUM ( CASE WHEN     d.production_type = 1 AND e.production_type = 1 THEN e.production_qnty ELSE 0 END) AS TOTAL_CUTTING FROM wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.id = b.job_id $date_cond AND b.id = c.po_break_down_id AND d.po_break_down_id = b.id AND d.po_break_down_id = c.po_break_down_id AND d.id = e.mst_id AND c.id = e.color_size_break_down_id
AND a.id = c.job_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0
AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0
AND e.status_active = 1 AND e.is_deleted = 0 AND a.company_name in($company_ids) AND b.shiping_status < 3
GROUP BY a.COMPANY_NAME,d.production_date";

//echo $sql;die;
$emb_Balance_job_qty_sql_res=sql_select($sql);

foreach($emb_Balance_job_qty_sql_res as $row){

$dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
$dataArr[$row['COMPANY_ID']]['TOTAL_CUTTING']+=$row['TOTAL_CUTTING'];
$dataArr[$row['COMPANY_ID']]['TODAY_SEWING_OUTPUT']+=$row['TODAY_SEWING_OUTPUT'];

}

$Sewing_balance=$dataArr[$row['COMPANY_ID']]['TOTAL_CUTTING']-$dataArr[$row['COMPANY_ID']]['TODAY_SEWING_OUTPUT'];


//Poly [Pcs] 
$date_cond	=" and a.production_date between '".$previous_date."' and '".$current_date."' ";

$sql="SELECT a.COMPANY_ID, a.PRODUCTION_DATE, c.job_no, a.production_type, (CASE WHEN TO_CHAR (a.production_hour, 'HH24:MI') >= '12:00' AND TO_CHAR (a.production_hour, 'HH24:MI') < '13:00' AND a.production_type = 11 THEN b.production_qnty ELSE 0 END) AS PROD_HOUR_POLY12 FROM pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_details_master c, wo_po_break_down d, wo_po_color_size_breakdown e WHERE a.id = b.mst_id AND a.po_break_down_id = d.id AND d.job_id = c.id AND d.job_id = e.job_id AND a.COMPANY_ID=3 and d.id = e.po_break_down_id AND b.color_size_break_down_id = e.id and a.production_type=11 AND a.status_active = 1 AND a.is_deleted = 0 AND b.is_deleted = 0 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 AND c.company_name in($company_ids) $date_cond";
//echo $sql;die;
$poly_qty_sql_res=sql_select($sql);
  
foreach($poly_qty_sql_res as $row){

$dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
$dataArr[$row['COMPANY_ID']]['PROD_HOUR_POLY12']+=$row['PROD_HOUR_POLY12'];

}

//Finishing Balance [Pcs](total Swing Output)
$date_cond	=" and a.production_date between '".$previous_date."' and '".$current_date."' ";

$sql="SELECT distinct a.id,a.COMPANY_ID,a.production_type,(CASE WHEN a.production_type = '5' THEN b.production_qnty END) AS TOT_SEWING_OUTPUT,a.production_date AS sewing_date FROM pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown  d,wo_po_details_master e WHERE  a.id = b.mst_id AND a.po_break_down_id = d.po_break_down_id AND b.color_size_break_down_id = d.id $date_cond AND a.production_type = 5 and  a.COMPANY_ID in($company_ids) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 ORDER BY a.production_date DESC";
//echo $sql;die;
$fin_sewing_sql_res=sql_select($sql);
     
   foreach($fin_sewing_sql_res as $row){
   
   $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
   $dataArr[$row['COMPANY_ID']]['TOT_SEWING_OUTPUT']+=$row['TOT_SEWING_OUTPUT'];
   }


//Finishing Balance [Pcs](Total Pack and Fin)
$date_cond	=" and d.production_date between '".$previous_date."' and '".$current_date."' ";
$sql="SELECT d.production_type,  SUM(CASE WHEN d.production_type = '8' THEN e.production_qnty ELSE 0 END) AS PACK_FIN_QUANTITY, a.COMPANY_NAME as COMPANY_ID FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, pro_garments_production_mst d, pro_garments_production_dtls e WHERE a.id = b.job_id AND b.id = c.po_break_down_id AND a.id = c.job_id AND b.id = d.po_break_down_id AND d.id = e.mst_id AND c.id = e.color_size_break_down_id AND c.po_break_down_id = d.po_break_down_id AND d.production_type = 8 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.COMPANY_NAME in($company_ids) AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 $date_cond group by d.production_type, a.COMPANY_NAME" ;
//echo $sql;die;
 $fin_pac_sql_res=sql_select($sql);
      
    foreach($fin_pac_sql_res as $row){
    
    $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
    $dataArr[$row['COMPANY_ID']]['PACK_FIN_QUANTITY']+=$row['PACK_FIN_QUANTITY'];
    }
$fin_Balance=$dataArr[$row['COMPANY_ID']]['TOT_SEWING_OUTPUT']-$dataArr[$row['COMPANY_ID']]['PACK_FIN_QUANTITY'];


//Shipment Delivery [Pcs]
$date_cond	=" and b.EX_FACTORY_DATE between '".$previous_date."' and '".$current_date."' ";
$sql="SELECT b.EX_FACTORY_DATE, a.company_id as COMPANY_ID , b.ex_factory_qnty as EX_FACTORY_QNTY , b.total_carton_qnty FROM pro_ex_factory_mst b, pro_ex_factory_delivery_mst a WHERE b.delivery_mst_id = a.id AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id in ($company_ids) $date_cond" ;
//echo $sql;die;
 $ship_qnty_sql_res=sql_select($sql);
      
    foreach($ship_qnty_sql_res as $row){
    
    $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
    $dataArr[$row['COMPANY_ID']]['EX_FACTORY_QNTY']+=$row['EX_FACTORY_QNTY'];
    }

    $good_in_hands=$dataArr[$row['COMPANY_ID']]['PACK_FIN_QUANTITY']-$dataArr[$row['COMPANY_ID']]['EX_FACTORY_QNTY'];

  
//Inspection Pass [Pcs] 
$date_cond	=" and inspection_date between '".$previous_date."' and '".$current_date."' ";
$sql="SELECT actual_po_id, id, po_break_down_id, inspected_by, inspection_company as COMPANY_ID, inspection_date, INSPECTION_QNTY, inspection_status, inspection_level, inspection_cause, comments FROM pro_buyer_inspection WHERE status_active = 1 AND is_deleted = 0  AND INSPECTION_COMPANY in ($company_ids)" ;
//echo $sql;die;
 $ship_qnty_sql_res=sql_select($sql);
      
    foreach($ship_qnty_sql_res as $row){
    
    $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
    $dataArr[$row['COMPANY_ID']]['INSPECTION_QNTY']+=$row['INSPECTION_QNTY'];
    }

    //Sewing total Input [Pcs] (END)
    $date_cond	=" and a.PRODUCTION_DATE between '".$previous_date."' and '".$current_date."' ";
$sql="SELECT a.COMPANY_ID, a.PRODUCTION_DATE, e.PLAN_CUT_QNTY, SUM(CASE WHEN a.production_type = 4 AND d.production_type = 4 THEN d.production_qnty ELSE 0 END) AS TODAY_SEW_INPUT FROM wo_po_details_master b, wo_po_break_down c, pro_garments_production_mst a, pro_garments_production_dtls d, wo_po_color_size_breakdown e WHERE b.job_no = c.job_no_mst AND c.id = a.po_break_down_id AND a.id = d.mst_id AND a.production_type IN (4, 5) AND d.production_type IN (4, 5)  AND d.color_size_break_down_id = e.id AND a.po_break_down_id = e.po_break_down_id AND a.status_active = 1 AND a.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND e.status_active = 1 AND e.is_deleted = 0 AND a.location IS NOT NULL AND a.location <> 0 AND d.color_size_break_down_id IS NOT NULL AND d.color_size_break_down_id <> 0 AND a.company_id in ($company_ids) $date_cond  GROUP BY a.company_id, a.PRODUCTION_DATE, e.plan_cut_qnty" ;
//echo $sql;die;
 $ship_qnty_sql_res=sql_select($sql);
      
    foreach($ship_qnty_sql_res as $row){
    
    $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
    $dataArr[$row['COMPANY_ID']]['PLAN_CUT_QNTY']+=$row['PLAN_CUT_QNTY'];
    $dataArr[$row['COMPANY_ID']]['TODAY_SEW_INPUT']+=$row['TODAY_SEW_INPUT'];
    }
    // echo "<pre>";
    // print_r($dataArr); 
    //   echo "</pre>";die();
    $sewing_input_bal=$dataArr[$row['COMPANY_ID']]['TODAY_SEW_INPUT']-$dataArr[$row['COMPANY_ID']]['PLAN_CUT_QNTY'];
 
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
  <?

   foreach ($dataArr as $company => $row) 
   {    
   
          ?>    <tr>
                <th class="no-border"><?=$company_lib[$row['COMPANY_ID']];?></th>
                
        
                 </tr>
                  <tr>
                  
                    <th class="no-border">between :<?=$previous_date?> and <?=$current_date?></th>
                  
                  </tr>
                  <tr>
                    <td>Total Order [Pcs]</td>
                    <td> <?=$total_order ;?></td>
                    
                  </tr> <tr>
                    <td>Confirm Order [Pcs] </td>
                    <td><?=$row['CONFIRM_QTY_PCS'];?></td>
                    
                  </tr> 
                  <tr>
                    <td>Projected Order [Pcs] </td>
                    <td><?=$row['PROJEC_QTY_PCS'];?></td>
                    
                  </tr>
                  <tr>
                    <td>EFR TTL [Grey] [Kg] </td>
                    <td><?=$row['SORT_GREY_QTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>F.Fabric Deliver to Store [Kg] </td>
                    <td><?=$row['TOTAL_QTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>FF Received [Cutting] [Kg] </td>
                    <td><?=$row['FIN_ROLL_ISSUE_QTY'];?></td>
                    
                  </tr>

                  <tr>
                    <td>Knitting Balance [Kg] </td>
                    <td><?=number_format($dyeing_Balance, 2);?></td>
                  </tr>
                  <tr>
                    <td>Dyeing Balance [Kg]</td>
                    <td><?=number_format($dying_Balance, 2);?></td>
                    
                  </tr>
                  <tr>
                    <td>Dyeing [White/Wash] [Kg] </td>
                    <td><?=$row['WASH_QNTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Dyeing Finishing [Kg] </td>
                    <td><?=number_format($finishing_Balance, 2);?></td>
                    
                  </tr>
                  <tr>
                    <td>Cutting Balance [Pcs]  </td>
                    <td><?=number_format($cutting_Balance, 2);?></td>
                    
                  </tr>
                  <tr>
                    <td>Print Balance [Pcs]</td>
                    <td><?=number_format($printing_Balance, 2);?></td>
                    
                  </tr>
                  <tr>
                    <td>Embroidery Balance [Pcs]</td>
                    <td><?=number_format($embroydery_Balance, 2);?></td>

                  </tr>
                  <tr>
                    <td>Sewing Balance [Pcs]</td>
                    <td><?=number_format($Sewing_balance, 2);?></td>

                  </tr>

                  <tr>
                    <td>Sewing Input [Pcs]</td>
                    <td><?=$row['PLAN_CUT_QNTY'];?></td>

                  </tr>

                  <tr>
                    <td>Sewing Input Balance [Pcs]</td>
                    <td><?=number_format($sewing_input_bal, 2);?></td>

                  </tr>

                  <tr>
                    <td>Poly [Pcs] </td>
                    <td><?=$row['PROD_HOUR_POLY12'];?></td>

                  </tr>

                  <tr>
                    <td>Packing And Finishing [Pcs]</td>
                    <td><?=$row['PACK_FIN_QUANTITY'];?></td>

                  </tr>
                  <tr>
                    <td>GMT F Received [Pcs]</td>
                    <td><?=$row['SEWING_QNTY'];?></td>
                    
                  </tr>

                  <tr>
                    <td>Finishing Balance [Pcs]</td>
                    <td><?=number_format($fin_Balance, 2);?></td>
                    
                  </tr>

                  <tr>
                    <td>Shipment Delivery [Pcs]</td>
                    <td><?=$row['EX_FACTORY_QNTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Ready Goods In hand [Pcs]</td>
                    <td><?=$good_in_hands;?></td>
                    
                  </tr>
                  <tr>
                    <td>Knitting Production [In-house].</td>
                    <td><?=$row['IN_HOUSE_QTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Inspection Pass [Pcs]</td>
                    <td><?=$row['INSPECTION_QNTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Knitting Production [Outside].</td>
                    <td><?=$row['OUTBOUND_QTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Dyeing Production</td>
                    <td><?=$row['PROD_QTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Dyeing Production [Re-process]</td>
                    <td><?=$row['PROCESS_PROD_QTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Finish Fabric Production</td>
                    <td><?=$row['PRODUCTION_QTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Cutting Production</td>
                    <td><?=$row['CUT_QNTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Print Production</td>
                    <td><?=$row['PRINT_QNTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Embroidery Production</td>
                    <td><?=$row['EMBRO_QNTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Sewing Completed</td>
                    <td><?=$row['SEWING_QNTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Garments Finishing &lt;Poly..&gt;</td>
                    <td><?=$row['POLY_QUANTITY'];?></td>
                    
                  </tr>
                 
          <?
   
        }   
               //$grandtotal_amount+=$row['receive_qnty'];
   
     // $i++;
  ?>
  </table>
</body>


</html>

