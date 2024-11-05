
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
 //..........Confirm and Projected Order.......(START)
 $date_cond	=" and a.PO_RECEIVED_DATE between '".$previous_date."' and '".$current_date."' ";

 $sql="SELECT a.JOB_NO_MST, sum(a.PO_QUANTITY) as PO_QUANTITY, sum(a.PO_QUANTITY*b.TOTAL_SET_QNTY) as PO_QUANTITY_PSC,
 sum(case when a.IS_CONFIRMED=1 then a.PO_QUANTITY*b.TOTAL_SET_QNTY else 0 end) as CONFIRM_QTY_PCS,
 sum(case when a.IS_CONFIRMED=2 then a.PO_QUANTITY*b.TOTAL_SET_QNTY else 0 end) as PROJEC_QTY_PCS,
 a.PACKING, b.COMPANY_NAME as COMPANY_ID , b.LOCATION_NAME, b.ORDER_UOM , b.TOTAL_SET_QNTY, a.PO_RECEIVED_DATE
 from wo_po_break_down a, wo_po_details_master b
 where a.JOB_ID=b.ID and  b.COMPANY_NAME =3 AND a.po_received_date BETWEEN '01-Feb-2024' AND '1-Feb-2024' and a.STATUS_ACTIVE in(1,2,3) and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 
 group by a.JOB_NO_MST, a.PACKING, b.COMPANY_NAME, b.LOCATION_NAME, b.ORDER_UOM, b.TOTAL_SET_QNTY, a.PO_RECEIVED_DATE order by a.PO_RECEIVED_DATE";

//echo $sql;die;

$order_sql_res=sql_select($sql);
foreach($order_sql_res as $row){
$dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
$dataArr[$row['COMPANY_ID']]['CONFIRM_QTY_PCS']+=$row['CONFIRM_QTY_PCS'];
$dataArr[$row['COMPANY_ID']]['PROJEC_QTY_PCS']+=$row['PROJEC_QTY_PCS'];
}
// echo "<pre>";
// print_r($dataArr); 
//   echo "</pre>";die(); 

$total_order = $dataArr[$row['COMPANY_ID']]['CONFIRM_QTY_PCS'] + $dataArr[$row['COMPANY_ID']]['PROJEC_QTY_PCS'];

     

     
      $date_cond	=" and a.process_end_date between '".$previous_date."' and '".$current_date."' ";
    //Knitting [Kg]
    $sql = "select a.receive_date,a.knitting_company as COMPANY_ID, sum(b.grey_receive_qnty) as KNITTING_QNTY from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.receive_date between '01-Feb-2024' and '1-Feb-2024' and a.knitting_company=3 and a.entry_form=2 and a.is_deleted=0 group by a.receive_date,a.knitting_company order by a.receive_date";
     //echo  $sql;die;
    $knit_sql_res=sql_select($sql);
   

    foreach($knit_sql_res as $row){
      $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
      $dataArr[$row['COMPANY_ID']]['KNITTING_QNTY']+=$row['KNITTING_QNTY'];
      
    
    }

     

//..........dyeing qnty (fresh).......

$date_cond	=" and a.RECEIVE_DATE between '".$previous_date."' and '".$current_date."' ";
$sql = "select a.process_end_date as production_date, a.service_company as COMPANY_ID, a.floor_id,b.production_qty as DYEING_QNTY from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form=35 and b.load_unload_id=2 and a.result=1 and a.process_end_date between '03-Feb-2024' and '03-Feb-2024' and a.service_company=3";
//echo $sql;die;

$dyeing_sql_res=sql_select($sql);

foreach($dyeing_sql_res as $row){
  $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
  $dataArr[$row['COMPANY_ID']]['DYEING_QNTY']+=$row['DYEING_QNTY'];
 
}
 

      //..........F. Fabric Deliver to Store [Kg].......

$date_cond	=" and a.RECEIVE_DATE between '".$previous_date."' and '".$current_date."' ";
$sql = "SELECT a.KNITTING_COMPANY as COMPANY_ID,a.LOCATION_ID,a.DELEVERY_DATE,b.CURRENT_DELIVERY,a.ENTRY_FORM from PRO_GREY_PROD_DELIVERY_MST a, PRO_GREY_PROD_DELIVERY_DTLS b where a.id=b.mst_id and a.ENTRY_FORM in(54,67) and b.ENTRY_FORM in(54,67) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.knitting_company=3 and a.delevery_date BETWEEN '03-Feb-2024' AND '03-Feb-2024'";
//echo $sql;die;

$fab_del_sql_res=sql_select($sql);

foreach($fab_del_sql_res as $row){
  $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
  $dataArr[$row['COMPANY_ID']]['CURRENT_DELIVERY']+=$row['CURRENT_DELIVERY'];
 
}
 

      //..........Cutting Pcs.......

$date_cond	=" and a.RECEIVE_DATE between '".$previous_date."' and '".$current_date."' ";
$sql = "SELECT a.production_date,a.serving_company as COMPANY_ID, CASE WHEN a.production_type=1 THEN b.production_qnty ELSE 0 END AS CUTTING_QTY from pro_garments_production_mst a,pro_garments_production_dtls b WHERE a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_date between '01-Feb-2024' and '01-Feb-2024' and a.serving_company=3";
//echo $sql;die;

$fab_del_sql_res=sql_select($sql);

foreach($fab_del_sql_res as $row){
  $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
  $dataArr[$row['COMPANY_ID']]['CUTTING_QTY']+=$row['CUTTING_QTY'];
 
}
 // echo "<pre>";
      // print_r($dataArr); 
      //   echo "</pre>";die(); 
      
           //..........Print Pcs.......

$date_cond	=" and a.RECEIVE_DATE between '".$previous_date."' and '".$current_date."' ";
$sql = "SELECT a.company_id as COMPANY_ID, b.mst_id, b.id, b.buyer_po_id, b.qcpass_qty as PRINT_QNTY, b.color_size_id, b.remarks,b.production_date,a.entry_form ,a.company_id,a.LOCATION_ID FROM subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=222 and a.status_active=1 and a.is_deleted=0 and b.production_date between '01-Feb-2024' and '29-Feb-2024' and a.company_id in(3) and b.status_active=1 and b.is_deleted=0 order by b.production_date";
//echo $sql;die;

$print_sql_res=sql_select($sql);

foreach($print_sql_res as $row){
  $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
  $dataArr[$row['COMPANY_ID']]['PRINT_QNTY']+=$row['PRINT_QNTY'];
 
}


//..........emb Pcs.......

$date_cond	=" and a.RECEIVE_DATE between '".$previous_date."' and '".$current_date."' ";
$sql = "SELECT a.company_id as COMPANY_ID, b.mst_id, b.id, b.buyer_po_id, b.qcpass_qty as EMB_QNTY, b.color_size_id, b.remarks,b.production_date,a.entry_form ,a.company_id,a.LOCATION_ID FROM subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=315 and a.status_active=1 and a.is_deleted=0 and b.production_date between '01-Feb-2024' and '29-Feb-2024' and a.company_id in(3) and b.status_active=1 and b.is_deleted=0 order by b.production_date";
//echo $sql;die;

$emb_sql_res=sql_select($sql);

foreach($emb_sql_res as $row){
  $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
  $dataArr[$row['COMPANY_ID']]['EMB_QNTY']+=$row['EMB_QNTY'];
 
}


 //  echo "<pre>";
//       print_r($dataArr); 
//         echo "</pre>";die();  

//..........Iron.......

$date_cond	=" and a.RECEIVE_DATE between '".$previous_date."' and '".$current_date."' ";
$sql = "SELECT a.serving_company as COMPANY_ID, a.production_date,a.production_type, a.embel_name, sum(b.production_qnty) as IRON_QUANTITY from pro_garments_production_mst a ,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=b.production_type and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.production_date between '01-Feb-2024' and '29-Feb-2024' and a.serving_company=3  and  a.production_type=7 and a.is_deleted=0 group by a.production_date,a.serving_company,a.production_type, a.embel_name order by a.production_date";
//echo $sql;die;

$emb_sql_res=sql_select($sql);

foreach($emb_sql_res as $row){
  $dataArr[$row['COMPANY_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
  $dataArr[$row['COMPANY_ID']]['IRON_QUANTITY']+=$row['IRON_QUANTITY'];
 
}


  // echo "<pre>";
  //     print_r($dataArr); 
  //       echo "</pre>";die();  


  
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
                    <td>Knitting [Kg] </td>
                    <td><?=$row['KNITTING_QNTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Dyeing [Fresh] [Kg] </td>
                    <td><?=$row['DYEING_QNTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>F. Fabric Deliver to Store [Kg] </td>
                    <td><?=$row['CURRENT_DELIVERY'];?></td>
                    
                  </tr>

                  <tr>
                    <td>FF Received [Cutting] [Kg] </td>
                    <td>000</td>
                  </tr>
                  <tr>
                    <td>Cutting [Pcs] </td>
                    <td><?=$row['CUTTING_QTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Print [Pcs] </td>
                    <td><?=$row['PRINT_QNTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Embroidery [Pcs] </td>
                    <td><?=$row['EMB_QNTY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Iron [Pcs]  </td>
                    <td><?=$row['IRON_QUANTITY'];?></td>
                    
                  </tr>
                  <tr>
                    <td>Print WIP [Pcs]</td>
                    <td><?=number_format($printing_wip, 2);?></td>
                    
                  </tr>
                  <tr>
                    <td>Embroidery WIP [Pcs]</td>
                    <td><?=number_format($embroydery_wip, 2);?></td>

                  </tr>
                  <tr>
                    <td>Sewing Balance [Pcs]</td>
                    <td><?=number_format($Sewing_balance, 2);?></td>

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
                    <td>Finishing WIP [Pcs]</td>
                    <td><?=number_format($fin_wip, 2);?></td>
                    
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
                    <td></td>
                    
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

