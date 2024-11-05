<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
require_once('../setting/mail_setting.php');

$company_name_library = return_library_array("SELECT id, COMPANY_NAME from lib_company where status_active=1 and is_deleted=0", "id", "COMPANY_NAME",$con);
$company_library = return_library_array("SELECT id, company_short_name from lib_company where status_active=1 and is_deleted=0", "id", "company_short_name",$con);
$floor_arr = return_library_array("SELECT id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1", "id", "floor_name");
$lib_location = return_library_array("SELECT ID,LOCATION_NAME FROM LIB_LOCATION WHERE IS_DELETED=0 AND STATUS_ACTIVE=1", "ID", "LOCATION_NAME");
$buyer_library 		=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
$lib_supplier_tag_company = return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and a.id in (select  supplier_id from  lib_supplier_party_type where party_type in(30,31,32)) group by a.id, a.supplier_name order by a.supplier_name", "id", "supplier_name");

// print_r($lib_supplier_tag_company);die;

$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
if($_REQUEST['view_date']){
    $current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
}
$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
$date_condition	=" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";

$sql = "SELECT a.id, a.SYS_NUMBER, a.COMPANY_ID, a.CHALLAN_NO, a.BUYER_ID, a.DELIVERY_DATE, a.INSERT_DATE, a.DRIVER_NAME, a.TRUCK_NO, a.LOCK_NO, a.MOBILE_NO, a.FORWARDER,a.DL_NO, b.id AS po_ex_mst_id, b.ex_factory_date, b.ex_factory_qnty, b.DELIVERY_MST_ID, b.SHIPING_MODE, b.TOTAL_CARTON_QNTY, b.INCO_TERMS, c.ID AS PO_BREACK_DOWN_ID, c.UNIT_PRICE, c.PO_QUANTITY, c.PO_NUMBER, d.STYLE_REF_NO, d.ORDER_UOM, d.ORDER_UOM, d.gmts_item_id FROM PRO_EX_FACTORY_DELIVERY_MST a, PRO_EX_FACTORY_MST  b, WO_PO_BREAK_DOWN c, WO_PO_DETAILS_MASTER d WHERE a.id = b.DELIVERY_MST_ID AND b.PO_BREAK_DOWN_ID = c.id AND c.job_id = d.id and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted =0 and c.status_active=1 and c.is_deleted =0  $date_condition";
//echo $sql;die;

//$sql = "SELECT b.id AS po_ex_mst_id, b.ex_factory_date, b.ex_factory_qnty, b.DELIVERY_MST_ID, b.SHIPING_MODE, b.TOTAL_CARTON_QNTY, b.INCO_TERMS FROM PRO_EX_FACTORY_DELIVERY_MST a, PRO_EX_FACTORY_MST  b WHERE $date_condition"; PO_NUMBER

//echo $sql;die;


$last_dat_ex_factory_res = sql_select($sql);
$DetailsDataArr = array(); 
$ChallanDetailsDataArr = array();
$challan_details_ex_factory_qnty = array();
$challan_details_curr_ship_value = array();
$ChallanDetailsShippingModeDataArr = array();
$ChallanDetailsIncoTermDataArr = array();
$ChallanDetailsCFNameDataArr = array();
$ChallanDetailslockNoDataArr = array();
$ChallanDetailsTruckNoDataArr = array();
$ChallanDetailsDriverNameDataArr = array();
$ChallanDetailsMobileNoDataArr = array();
$ChallanDetailsDlNoDataArr = array();
$challan_details_carton_qnty_value = array();
$SummaryPOQtyDataArr2 = array();
 
$SummaryDataArr = array();
$poArr = array();
foreach($last_dat_ex_factory_res as $row){
   
    //$SummaryDataArr[$row['COMPANY_ID']][$row['BUYER_ID']]['po_quantity'] += $row['EX_FACTORY_QNTY'];
    $SummaryDataArr[$row['COMPANY_ID']][$row['BUYER_ID']]['ex_factory_qnty'] += $row['EX_FACTORY_QNTY'];
    $SummaryDataArr[$row['COMPANY_ID']][$row['BUYER_ID']]['po_values'] = $row['PO_QUANTITY']*$row['UNIT_PRICE'];
    $SummaryDataArr[$row['COMPANY_ID']][$row['BUYER_ID']]['current_ship_value'] += $row['EX_FACTORY_QNTY']*$row['UNIT_PRICE'];
    $poArr[$row['PO_BREACK_DOWN_ID']] = $row['PO_BREACK_DOWN_ID'];

    $DetailsDataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']] = $row;
    $SummaryPOQtyDataArr2[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']] = $row['PO_QUANTITY'];
    $SummaryPOValueDataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']] = $row['PO_QUANTITY']*$row['UNIT_PRICE'];
 
    $ChallanDetailsDataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']][$row['SYS_NUMBER']] = $row['SYS_NUMBER'];
    $challan_details_ex_factory_qnty[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']]['ex_factory_qnty'] += $row['EX_FACTORY_QNTY'];
    $challan_details_curr_ship_value[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']]['curr_ship_value'] += $row['PO_QUANTITY']*$row['UNIT_PRICE'];
    $challan_details_carton_qnty_value[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']]['carton_qnty'] += $row['TOTAL_CARTON_QNTY'];
    

    
    // $ChallanDetailsShippingModeDataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']][$row['SYS_NUMBER']] += $row['TOTAL_CARTON_QNTY'];
    $ChallanDetailsShippingModeDataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']][$row['SYS_NUMBER']] = $row['SHIPING_MODE'];
    $ChallanDetailsIncoTermDataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']][$row['SYS_NUMBER']] = $row['INCO_TERMS'];
    $ChallanDetailsCFNameDataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']][$row['SYS_NUMBER']] = $row['FORWARDER'];
    $ChallanDetailslockNoDataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']][$row['SYS_NUMBER']] = $row['LOCK_NO'];
    $ChallanDetailsTruckNoDataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']][$row['SYS_NUMBER']] = $row['TRUCK_NO'];
    $ChallanDetailsDriverNameDataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']][$row['SYS_NUMBER']] = $row['DRIVER_NAME'];
    $ChallanDetailsMobileNoDataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']][$row['SYS_NUMBER']] = $row['MOBILE_NO'];
    $ChallanDetailsDlNoDataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']][$row['SYS_NUMBER']] = $row['DL_NO'];
}

   //echo "<pre>";
//.print_r($SummaryDataArr3);die;

$po_id = implode(",",$poArr); 

$total_sip_sql = "SELECT a.id, a.buyer_id, a.insert_date,a.COMPANY_ID, b.PO_BREAK_DOWN_ID, b.ex_factory_qnty,c.unit_price,c.PO_QUANTITY,c.PO_NUMBER FROM PRO_EX_FACTORY_DELIVERY_MST a, PRO_EX_FACTORY_MST b, WO_PO_BREAK_DOWN  c WHERE a.id = b.DELIVERY_MST_ID and  b.PO_BREAK_DOWN_ID = c.id  AND b.PO_BREAK_DOWN_ID IN($po_id)";
//echo $total_sip_sql;die;
$total_sip_res = sql_select($total_sip_sql);
$buyer_order_qnty_arr = array();
$order_qnty_arr = array();
$buyer_order_qnty_arr2 = array();
$order_ship_value_arr = array(); 
 

foreach($total_sip_res as $row){
    $buyer_order_qnty_arr[$row['COMPANY_ID']][$row['BUYER_ID']] += $row['EX_FACTORY_QNTY'];
    $order_qnty_arr[$row['PO_BREAK_DOWN_ID']] += $row['EX_FACTORY_QNTY'];
    $buyer_order_qnty_arr2[$row['COMPANY_ID']][$row['BUYER_ID']] += $row['EX_FACTORY_QNTY']*$row['UNIT_PRICE'];
    $order_ship_value_arr[$row['COMPANY_ID']][$row['PO_BREAK_DOWN_ID']] += $row['EX_FACTORY_QNTY']*$row['UNIT_PRICE'];
}
 
//$total_pos_sql = "SELECT a.id, a.buyer_id, a.insert_date,a.COMPANY_ID, b.PO_BREAK_DOWN_ID, b.ex_factory_qnty,c.unit_price,c.PO_QUANTITY,c.PO_NUMBER FROM PRO_EX_FACTORY_DELIVERY_MST a, PRO_EX_FACTORY_MST b, WO_PO_BREAK_DOWN  c WHERE c.id=b.PO_BREAK_DOWN_ID and  b.DELIVERY_MST_ID= a.id AND b.PO_BREAK_DOWN_ID IN($po_id)";

//echo $total_sip_sql;die;

// $total_pos_sql = "SELECT   c.unit_price,  c.PO_QUANTITY, c.PO_NUMBER, c.job_id, e.BUYER_NAME
//   FROM  WO_PO_BREAK_DOWN   c, WO_PO_DETAILS_MASTER   e WHERE  c.job_id=e.id and  c.ID IN ($po_id)";

//   echo $total_pos_sql;die;

// $total_po_res = sql_select($total_pos_sql);
// $SummaryPOWiseQntyDataArr = array(); 
// foreach($total_po_res as $row){ 
//     $SummaryPOWiseQntyDataArr[$row['COMPANY_ID']][$row['BUYER_ID']][$row['PO_NUMBER']]['po_quantity'] += $row['PO_QUANTITY'];
// }


//     echo "<pre>";
//     print_r($SummaryPOWiseQntyDataArr);die;
//    //echo "<pre>";
   //print_r($challan_details_total_ship_qty2);die;

$sql = "SELECT a.id, a.SYS_NUMBER, a.COMPANY_ID, a.CHALLAN_NO, a.BUYER_ID, a.DELIVERY_DATE, a.INSERT_DATE, a.DRIVER_NAME, a.TRUCK_NO, a.LOCK_NO, a.MOBILE_NO, a.FORWARDER,a.DL_NO, b.id AS po_ex_mst_id, b.ex_factory_date, b.ex_factory_qnty, b.DELIVERY_MST_ID, b.SHIPING_MODE, b.TOTAL_CARTON_QNTY, b.INCO_TERMS, c.ID AS PO_BREACK_DOWN_ID, c.UNIT_PRICE, c.PO_QUANTITY, c.PO_NUMBER, d.STYLE_REF_NO, d.ORDER_UOM, d.ORDER_UOM, d.gmts_item_id FROM PRO_EX_FACTORY_DELIVERY_MST a, PRO_EX_FACTORY_MST  b, WO_PO_BREAK_DOWN c, WO_PO_DETAILS_MASTER d WHERE a.id = b.DELIVERY_MST_ID AND b.PO_BREAK_DOWN_ID = c.id AND c.job_id = d.id";
    $dat_ex_factory_res = sql_select($sql);
    $challan_details_total_ship_qty = array();
   // $challan_details_total_ship_qty2 = array();
    foreach($dat_ex_factory_res as $row){ 
        $challan_details_total_ship_qty[$row['PO_NUMBER']]['total_ship_qty'] += $row['EX_FACTORY_QNTY'];
      //  $challan_details_total_ship_qty2[$row['BUYER_ID']]['total_ship_qty2'] += $row['EX_FACTORY_QNTY'];
    }



foreach($company_library as $compid=>$compname)
{
    if($SummaryDataArr[$compid])
    {
        ob_start();
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
        <div id="scroll_body" align="center" style="height:auto; width:<?= $width;?>px; margin:0 auto; padding:0;">
            <table width="100%" cellpadding="0" cellspacing="0" id="caption" align="center">
                <tr style="background-color: aliceblue;">
                    <td align="center" width="100%" colspan="<?= $count_data; ?>" class="form_caption" >
                       <strong style="font-size:18px"><?= $company_name_library[$compid];?></strong>
                    </td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<?= $count_data; ?>" class="form_caption" ><strong style="font-size:14px">Last Day Ex-Factory Status</strong></td>
                </tr>
                <tr>  -
                    <td align="center" width="100%" colspan="<?= $count_data; ?>" class="form_caption" ><strong style="font-size:14px">
                    <?= change_date_format($previous_date);?></strong>
                    </td>
                </tr>  
            </table>
            <div align="center" style="height:auto; width:<?= $width;?>px;">
                <table border="1" width="100%" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
                    <thead>
                        <tr style="background-color: cornsilk;">
                            <th>Buyer</th>
                            <th>PO Qty</th>
                            <th>Current Ship Qty</th>
                            <th>Total Ship Qty</th>
                            <th>Balance Qty</th>
                            <th>PO Value</th>
                            <th>Current Ship Value</th>
                            <th>Total Ship Value</th>
                            <th>Balance Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($SummaryDataArr[$compid] as $key=>$row){
                            ?>
                            <tr>
                                <td><?= $buyer_library[$key];?></td>
                                <td>
                                <?php
                                    $total = 0;
                                    foreach($SummaryPOQtyDataArr2[$compid][$key] as $pos0Qnty)
                                    {
                                        $total += $pos0Qnty;
                                    }
                                    echo $total;
                                    ?>
                                </td>
                                <td><?= $row['ex_factory_qnty'];?></td>
                                <td><?= $buyer_order_qnty_arr[$compid][$key];?></td>
                                <td><?= $total-$buyer_order_qnty_arr[$compid][$key];?></td>
                                <td>
                                    <?php
                                    $total_po_value = 0;
                                    foreach($SummaryPOValueDataArr[$compid][$key] as $pos0Qnty)
                                    {
                                        $total_po_value += $pos0Qnty;
                                    }
                                    echo $total_po_value;
                                    ?>
                                </td> 
                                <td><?= $row['current_ship_value'];?></td>
                                <td><?= $buyer_order_qnty_arr2[$compid][$key];?></td>
                                <td><?= $total_po_value-$buyer_order_qnty_arr2[$compid][$key];?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <br>
                <br>
                <table border="1" width="100%" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
                    <thead>
                        <tr style="background-color: cornsilk;">
                            <th>Sl</th>
                            <th>BU</th>
                            <th>Ex-Fac. Date</th>
                            <th>Challan NO</th>
                            <th>Buyer Name</th>
                            <th>Style Ref.</th>
                            <th>Order NO</th>
                            <th>UOM</th>
                            <th>Unit Price</th>
                            <th>Order Qty</th>
                            <th>Cur.Ex-Fact. Qty</th>
                            <th>Curr. Ship Value</th>
                            <th>Total Ship Qty</th>
                            <th>Total Ship Value</th>
                            <th>Item Name</th>
                            <th>Shipping Mode</th>
                            <th>Carton Qty</th>
                            <th>Inco Term</th>
                            <th>C & F Name</th>
                            <th>Lock No</th>
                            <th>Vehicle No</th>
                            <th>Driver Name</th>
                            <th>Mobile No</th>
                            <th>DL No</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($DetailsDataArr[$compid] as $buyer_id=>$ponumberArr)
                        {
                            $i=1;
                            foreach($ponumberArr as $key=>$row)
                            {
                                ?>
                                <tr>
                                    <td><?= $i;?></td>
                                    <td><?= $company_library[$row['COMPANY_ID']];?></td>
                                    <td><?= $row['EX_FACTORY_DATE'];?></td>
                                    <td><?= implode(', ', $ChallanDetailsDataArr[$compid][$buyer_id][$row['PO_NUMBER']]);?></td>
                                    <td><?= $buyer_library[$row['BUYER_ID']];?></td>
                                    <td><?= $row['STYLE_REF_NO'];?></td>
                                    <td><?= $row['PO_NUMBER'];?></td>
                                    <td><?= $unit_of_measurement[$row['ORDER_UOM']];?></td>
                                    <td><?= $row['UNIT_PRICE'];?></td>
                                    <td><?= $row['PO_QUANTITY'];?></td>
                                    <td><?= $challan_details_ex_factory_qnty[$compid][$buyer_id][$row['PO_NUMBER']]['ex_factory_qnty'];?></td>
                                    <td><?= $challan_details_ex_factory_qnty[$compid][$buyer_id][$row['PO_NUMBER']]['ex_factory_qnty']*$row['UNIT_PRICE'];?></td>
                                    <td><?= $challan_details_total_ship_qty[$row['PO_NUMBER']]['total_ship_qty'];?></td>
                                    <td><?= $challan_details_total_ship_qty[$row['PO_NUMBER']]['total_ship_qty']*$row['UNIT_PRICE'];?></td>
  
                                    <td>
                                    <?
                                    $gmts_item_name="";
                                    $gmts_item_id=explode(',',$row[('GMTS_ITEM_ID')]);
                                    for($j=0; $j<count($gmts_item_id); $j++)
                                    {
                                        $gmts_item_name.= $garments_item[$gmts_item_id[$j]].",";
                                    }
                                    echo rtrim($gmts_item_name,",");
                                    ?>
                                    </td>
                                    
                                    <td>
                                    <?
                                    $shipment = '';
                                    foreach($ChallanDetailsShippingModeDataArr[$compid][$buyer_id][$row['PO_NUMBER']] as $mode){
                                        $shipment .= $shipment_mode[$mode].',';
                                    }
                                    echo rtrim($shipment, ",");
                                    ?>
                                    </td> 
 
                                    
                                    <td><?= $challan_details_carton_qnty_value[$compid][$buyer_id][$row['PO_NUMBER']]['carton_qnty'];?></td>
  
                                    <td>
                                    <?
                                    $incoterms = '';
                                    foreach($ChallanDetailsIncoTermDataArr[$compid][$buyer_id][$row['PO_NUMBER']] as $inco){
                                        $incoterms .= $incoterm[$inco].',';
                                    }
                                    echo rtrim($incoterms, ",");
                                    ?>
                                    </td> 

                                    <td>
                                    <?
                                    $incoterms = '';
                                    foreach($ChallanDetailsCFNameDataArr[$compid][$buyer_id][$row['PO_NUMBER']] as $inco){
                                        $incoterms .= $lib_supplier_tag_company[$inco].',';
                                    }
                                    echo rtrim($incoterms, ",");
                                    ?>
                                    </td>

                                    <td>
                                    <?
                                    $lock_no_text = '';
                                    foreach($ChallanDetailslockNoDataArr[$compid][$buyer_id][$row['PO_NUMBER']] as $lock_no){
                                        $lock_no_text .= $lock_no.',';
                                    }
                                    echo rtrim($lock_no_text, ",");
                                    ?>
                                    </td>

                                    <td>
                                    <?
                                    $truck_no_text = '';
                                    foreach($ChallanDetailsTruckNoDataArr[$compid][$buyer_id][$row['PO_NUMBER']] as $truck_no){
                                        $truck_no_text .= $truck_no.',';
                                    }
                                    echo rtrim($truck_no_text, ",");
                                    ?>
                                    </td>

                                    <td>
                                    <?
                                    $driver_name_text = '';
                                    foreach($ChallanDetailsDriverNameDataArr[$compid][$buyer_id][$row['PO_NUMBER']] as $driver_name){
                                        $driver_name_text .= $driver_name.',';
                                    }
                                    echo rtrim($driver_name_text, ",");
                                    ?>
                                    </td>

                                    <td>
                                    <?
                                    $mobile_no_text = '';
                                    foreach($ChallanDetailsMobileNoDataArr[$compid][$buyer_id][$row['PO_NUMBER']] as $mobile_no){
                                        $mobile_no_text .= $mobile_no.',';
                                    }
                                    echo rtrim($mobile_no_text, ",");
                                    ?>
                                    </td>
                                    <td>
                                    <?
                                    $dl_no_text = '';
                                    foreach($ChallanDetailsDlNoDataArr[$compid][$buyer_id][$row['PO_NUMBER']] as $dl_no){
                                        $dl_no_text .= $dl_no.',';
                                    }
                                    echo rtrim($dl_no_text, ",");
                                    ?>
                                    </td> 
                                </tr>
                                <?php
                            $i++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <br>
	    <?
        $message = ob_get_contents();
        ob_clean();
        $mail_item = 146;
        $to='';
        $sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and b.mail_user_setup_id=c.id and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
        $mail_sql2 = sql_select($sql2, '', '', '', $con);
        foreach($mail_sql2 as $row)
        {
            if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
        }
    
        //$to='muktobani@gmail.com';
        $subject = "Last Day Ex-Factory Status";
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
    }
}
?>