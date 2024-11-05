<?php
/*-------------------------------------------- Comments
Purpose         :   Sample requisition with booking Woven auto mail
Functionality   :   
JS Functions    :
Created by      :   Al-Hasan
Creation date   :   13-12-2023
Updated by      :
Update date     :  
QC Performed BY :
QC Date         :
Comment*/

date_default_timezone_set("Asia/Dhaka");
require_once('../../includes/common.php');
require_once('../setting/mail_setting.php');

//$company_library = return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0", "id", "company_short_name",$con);
$company_library = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
$company_city = return_library_array("select id, city from lib_company", "id", "city");
$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");

$lib_location = return_library_array("SELECT id, location_name FROM lib_location", "id", "location_name"); 
$floor_arr = return_library_array( "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1", "id", "floor_name");
$current_time = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = date("d-M-Y", $current_time);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))), '', '', 1);
// $date_condition	=" and b.insert_date between '".$prev_date."' and '".$prev_date."'";
// $date_condition= " and a.insert between '".$prev_date."' and '".$prev_date."' ";

$date_condition	=" and a.insert_date between '".$prev_date."' and '".$prev_date." 11:59:59 PM'";

//----------------------------------------------------
//$previous_date1= "01-Dec-2023";
//$previous_date2= "31-Dec-2023";
//$date_condition1= " and a.insert_date between '".$previous_date1."' and '".$previous_date2." 11:59:59' ";

// Cut and Lay Qty--------------------
$cut_lay_sql = "SELECT a.id, a.job_no, a.job_year, a.working_company_id as company_id, a.floor_id, a.location_id, b.id as cut_dlts_id,b.MARKER_QTY, b.order_ids FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b WHERE a.id=b.mst_id $date_condition and a.status_active=1 and a.is_deleted=0";

//  echo $cut_lay_sql;die;

$cut_lay_res = sql_select($cut_lay_sql);
$cut_and_lay_data_arr = array();
$cut_lay_dlts_floor_arr = array();
$cut_lay_dlts_qnty_arr = array();
foreach($cut_lay_res as $row)
{
    $cut_and_lay_data_arr[$row['COMPANY_ID']][$row['LOCATION_ID']] += $row['MARKER_QTY'];
    $cut_lay_dlts_qnty_arr[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_ID']] += $row['MARKER_QTY'];

    $company_location_arr[$row['COMPANY_ID']][$row['LOCATION_ID']] += $row['MARKER_QTY'];
   // entry_break_down_type=3
}

$subcon_cut_lay_sql = "SELECT a.id, a.company_id, a.location_id, a.floor_id, a.production_qnty, a.production_type,a.entry_break_down_type FROM subcon_gmts_prod_dtls a WHERE production_type in (1,2,3,4,7,9,10)  and a.status_active=1 and a.is_deleted=0 $date_condition";

// echo "<pre>";
// print_r($company_location_arr);die;
// echo $subcon_cut_lay_sql;die;

$subcon_cut_lay_res = sql_select($subcon_cut_lay_sql);
$subcon_cut_and_lay_data_arr = array();
$subcon_cut_and_lay_data_delts_arr = array();

$subcon_sewing_arr = array();
$subcon_sewing_delts_arr = array();

$subcon_sewing_out_arr = array();
$subcon_sewing_out_delts_arr = array();

$subcon_wash_send_arr = array();
$subcon_wash_send_delts_arr = array();

$subcon_wash_receive_arr = array();
$subcon_wash_receive_delts_arr = array();

$subcon_wash_finishing_arr = array();
$subcon_wash_finishing_delts_arr = array();

$subcon_wash_packing_arr = array();
$subcon_wash_packing_delts_arr = array();


foreach($subcon_cut_lay_res as $row)
{
    if($row['PRODUCTION_TYPE']==1){
        $subcon_cut_and_lay_data_arr[$row['COMPANY_ID']][$row['LOCATION_ID']] += $row['PRODUCTION_QNTY'];
        $subcon_cut_and_lay_data_delts_arr[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
    }
    if($row['PRODUCTION_TYPE']==7){
        $subcon_sewing_arr[$row['COMPANY_ID']][$row['LOCATION_ID']] += $row['PRODUCTION_QNTY'];
        $subcon_sewing_delts_arr[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
    }

    if($row['PRODUCTION_TYPE']==2){
        $subcon_sewing_out_arr[$row['COMPANY_ID']][$row['LOCATION_ID']] += $row['PRODUCTION_QNTY'];
        $subcon_sewing_out_delts_arr[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
    }
    if($row['PRODUCTION_TYPE']==9){
        $subcon_wash_send_arr[$row['COMPANY_ID']][$row['LOCATION_ID']] += $row['PRODUCTION_QNTY'];
        $subcon_wash_send_delts_arr[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
    }
    if($row['PRODUCTION_TYPE']==10){
        $subcon_wash_receive_arr[$row['COMPANY_ID']][$row['LOCATION_ID']] += $row['PRODUCTION_QNTY'];
        $subcon_wash_receive_delts_arr[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
    }
    if($row['PRODUCTION_TYPE']==3){
        $subcon_wash_finishing_arr[$row['COMPANY_ID']][$row['LOCATION_ID']] += $row['PRODUCTION_QNTY'];
        $subcon_wash_finishing_delts_arr[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
    }
    if($row['PRODUCTION_TYPE']==4){
        $subcon_wash_packing_arr[$row['COMPANY_ID']][$row['LOCATION_ID']] += $row['PRODUCTION_QNTY'];
        $subcon_wash_packing_delts_arr[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
    }
   
}

$subcon_delivery_mst_sql = "SELECT a.id, b.mst_id,a.company_id,a.location_id, b.delivery_qty FROM subcon_delivery_mst a, subcon_gmts_delivery_dtls b WHERE a.id=b.mst_id $date_condition";
// echo $subcon_delivery_mst_sql;die;
$subcon_delivery_mst_res = sql_select($subcon_delivery_mst_sql);
$subcon_delivery_mst_arr = array();
foreach($subcon_delivery_mst_res as $row)
{
    $subcon_delivery_mst_arr[$row['COMPANY_ID']][$row['LOCATION_ID']] += $row['DELIVERY_QTY'];
}
// echo "<pre>";
// print_r($subcon_delivery_mst_arr);die;



//subcon_gmts_prod_dtls

 //echo "<pre>";
 //print_r($cut_lay_dlts_qnty_arr);die;
// echo $date_condition;die;

$cut_lay_sql = "SELECT a.id, a.serving_company as company_id, a.floor_id,a.location, b.production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls b WHERE a.id=b.mst_id and  b.production_type=1 and a.status_active=1 and a.is_deleted=0 $date_condition";
//echo $cut_lay_sql;die;

$cut_lay_res = sql_select($cut_lay_sql);
$cutting_qc_data_arr = array();
$cutting_qc_floor_arr = array();
$cutting_qc_qnty_arr = array();
foreach($cut_lay_res as $row){
    $cutting_qc_data_arr[$row['COMPANY_ID']][$row['LOCATION']] += $row['PRODUCTION_QNTY'];
    $company_location_arr[$row['COMPANY_ID']][$row['LOCATION']] += $row['PRODUCTION_QNTY'];
    // $cutting_qc_floor_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];
    $cutting_qc_qnty_arr[$row['COMPANY_ID']][$row['LOCATION']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
}



//$rr = array_merge($cutting_qc_data_arr, $cut_and_lay_data_arr);



// Sewing In----------
$sewing_in_sql = "SELECT a.id, a.serving_company as company_id, a.floor_id, a.location, b.production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls b WHERE a.id=b.mst_id and  b.production_type=4 and a.status_active=1 and a.is_deleted=0 $date_condition";
// echo $sewing_in_sql;die;
$sewing_in_res = sql_select($sewing_in_sql);

$sewing_in_data_arr = array();
$sewing_in_dlts_arr = array();
$sewing_in_qnty_arr = array();
foreach($sewing_in_res as $row){
    $sewing_in_data_arr[$row['COMPANY_ID']][$row['LOCATION']] += $row['PRODUCTION_QNTY'];

    $company_location_arr[$row['COMPANY_ID']][$row['LOCATION']] += $row['PRODUCTION_QNTY'];
 
    // $sewing_in_dlts_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];
    $sewing_in_qnty_arr[$row['COMPANY_ID']][$row['LOCATION']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
}
 

 
// Sewing Out
$sewing_out_sql = "SELECT a.id, a.serving_company as company_id, a.floor_id, a.location, b.production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls b WHERE a.id=b.mst_id and  b.production_type=5 and a.status_active=1 and a.is_deleted=0 $date_condition";
$sewing_out_res = sql_select($sewing_out_sql);
$sewing_out_data_arr = array();
$sewing_out_dlts_arr = array();
$sewing_out_qnty_arr = array();
foreach($sewing_out_res as $row){
   // $sewing_out_data_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
    $sewing_out_data_arr[$row['COMPANY_ID']][$row['LOCATION']] += $row['PRODUCTION_QNTY'];

    $company_location_arr[$row['COMPANY_ID']][$row['LOCATION']] += $row['PRODUCTION_QNTY'];

    //$sewing_out_dlts_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];
    $sewing_out_qnty_arr[$row['COMPANY_ID']][$row['LOCATION']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
}


/// Wash Send ---------
//$wash_send_sql = "SELECT a.id, a.serving_company as company_id, a.floor_id, a.location, b.production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls b WHERE a.id=b.mst_id and a.EMBEL_NAME=3 and b.production_type=2 and a.status_active=1 and a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 $date_condition";


$wash_send_sql = "SELECT a.id, a.SENDING_COMPANY AS company_id, a.floor_id, a.SENDING_LOCATION,
b.production_qnty
FROM pro_garments_production_mst a, pro_garments_production_dtls b
WHERE a.id = b.mst_id
AND a.EMBEL_NAME = 3
AND b.production_type = 2
AND a.status_active = 1
AND a.is_deleted = 0
AND b.status_active = 1
AND b.is_deleted = 0 $date_condition";
// echo $wash_send_sql ;die;
  
 
$wash_send_res = sql_select($wash_send_sql);
$wash_send_data_arr = array();
$wash_send_dlts_arr = array();
$wash_send_qnty_arr = array();
foreach($wash_send_res as $row){
   // $sewing_out_data_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
   $wash_send_data_arr[$row['COMPANY_ID']][$row['SENDING_LOCATION']] += $row['PRODUCTION_QNTY'];
   $company_location_arr[$row['COMPANY_ID']][$row['SENDING_LOCATION']] += $row['PRODUCTION_QNTY'];

    // $wash_send_dlts_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];
    $wash_send_qnty_arr[$row['COMPANY_ID']][$row['SENDING_LOCATION']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
}

//echo "<pre>";
//print_r($wash_send_data_arr);die;




// Wash Received--------
$wash_received_sql = "SELECT a.id, a.SENDING_COMPANY as company_id, a.floor_id, a.SENDING_LOCATION, b.production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls b WHERE a.id=b.mst_id and  a.EMBEL_NAME=3 and b.production_type=3 and a.status_active=1 and a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 $date_condition";
//echo $wash_received_sql;die;
$wash_received_res = sql_select($wash_received_sql);
$wash_received_data_arr = array();
$wash_received_dlts_arr = array();
$wash_received_qnty_arr = array();
foreach($wash_received_res as $row){
    $wash_received_data_arr[$row['COMPANY_ID']][$row['SENDING_LOCATION']] += $row['PRODUCTION_QNTY'];
    
    $company_location_arr[$row['COMPANY_ID']][$row['SENDING_LOCATION']] += $row['PRODUCTION_QNTY'];
    
  
    // $wash_received_dlts_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];
    $wash_received_qnty_arr[$row['COMPANY_ID']][$row['SENDING_LOCATION']][$row['FLOOR_ID']] +=  $row['PRODUCTION_QNTY'];
     
}


// Finishing (Getup Pass)--------
$finishing_getpass_sql = "SELECT a.id, a.serving_company as company_id, a.floor_id, a.location, b.production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls b WHERE a.id=b.mst_id and  b.production_type=7 and a.status_active=1 and a.is_deleted=0 $date_condition";
$finishing_getpass_res = sql_select($finishing_getpass_sql);
$finishing_getpass_data_arr = array();
$finishing_getpass_dlts_arr = array();
$finishing_getpass_qnty_arr = array();
foreach($finishing_getpass_res as $row){
    $finishing_getpass_data_arr[$row['COMPANY_ID']][$row['LOCATION']] += $row['PRODUCTION_QNTY'];
    $company_location_arr[$row['COMPANY_ID']][$row['LOCATION']] += $row['PRODUCTION_QNTY']; 

    // $finishing_getpass_dlts_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];
    $finishing_getpass_qnty_arr[$row['COMPANY_ID']][$row['LOCATION']][$row['FLOOR_ID']] +=$row['PRODUCTION_QNTY'];
}



// Packing/Carton ----------------- 
$packing_carton_sql = "SELECT a.id, a.serving_company as company_id, a.floor_id, a.location, b.production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls b WHERE a.id=b.mst_id and  b.production_type=8 and a.status_active=1 and a.is_deleted=0 $date_condition";
$packing_carton_res = sql_select($packing_carton_sql);
$packing_carton_data_arr = array();
$packing_carton_dlts_arr = array();
$packing_carton_qnty_arr = array();
foreach($packing_carton_res as $row){
    $packing_carton_data_arr[$row['COMPANY_ID']][$row['LOCATION']] += $row['PRODUCTION_QNTY'];
    $company_location_arr[$row['COMPANY_ID']][$row['LOCATION']] += $row['PRODUCTION_QNTY']; 

    // $packing_carton_dlts_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] =  $row['FLOOR_ID'];
    $packing_carton_qnty_arr[$row['COMPANY_ID']][$row['LOCATION']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
}

 

// Ex-Factory......... 
$ex_factory_sql = "SELECT a.id, a.ex_factory_qnty, b.DELIVERY_LOCATION_ID, b.company_id FROM pro_ex_factory_mst a, pro_ex_factory_delivery_mst b WHERE a.delivery_mst_id=b.id $date_condition and b.entry_form!=85 and a.status_active=1 and a.is_deleted=0";
//echo $ex_factory_sql;die;

//$ex_factory_sql = "SELECT a.id, a.ex_factory_qnty, a.location, b.delivery_company_id as company_id FROM pro_ex_factory_mst a, pro_ex_factory_delivery_mst b WHERE a.delivery_mst_id=b.id $date_condition and b.entry_form!=85 and a.status_active=1 and a.is_deleted=0";
//echo $ex_factory_sql ;die;
$ex_factory_res = sql_select($ex_factory_sql);
$ex_factory_data_arr = array();
$ex_factory_dlts_arr = array();
$ex_factory_qnty_arr = array();
foreach($ex_factory_res as $row){
    $ex_factory_data_arr[$row['COMPANY_ID']][$row['DELIVERY_LOCATION_ID']] += $row['EX_FACTORY_QNTY'];
    $company_location_arr[$row['COMPANY_ID']][$row['DELIVERY_LOCATION_ID']] += $row['EX_FACTORY_QNTY']; 

    // $ex_factory_dlts_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];
    $ex_factory_qnty_arr[$row['COMPANY_ID']][$row['DELIVERY_LOCATION_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];    
}
// echo "<pre>";
// print_r($company_location_arr);die;
ob_start();
?>
<body>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
th, td {
  padding: 10px;
}
</style> 
<h2 style="text-align:center">Unit Wise Garments Production Report (Date-<?= change_date_format($prev_date);?>)</h2>


<table style="width:100%">
    <thead>
        <tr>
            <th>Garments Production</th>
            <?php
            foreach($company_location_arr as $key=>$row){?>
                <th style="background-color: #0088cc;color:white;width:200px" colspan="<?php echo count($row);?>"><?php echo $company_library[$key];?></th>
            <? } ?>
            <th style="background-color: #0088cc;color:white">Group Total</th>
        </tr>
    </thead>
    <tbody> 
        <tr>
            <th style="color:red">Location</th>
            <?php
            foreach($company_location_arr as $row){
                foreach($row as $key=>$data){?>
            <td><?php echo $lib_location[$key];?></td>
            <?php } } ?>
            <td></td> 
        </tr>
        <tr>
            <td style="color:#FFFFFF;background-color:#a3a3c2"><strong>Cut and Lay Qty</strong></td>
            <?php
            $total = 0;
            foreach($company_location_arr as $com_id => $row){
                foreach($row as $key=>$data){?>
            <td align="right"><?php echo $cut_and_lay_data_arr[$com_id][$key];?></td>
            <?php $total += $cut_and_lay_data_arr[$com_id][$key];} } ?>
            <td align="right"><?php echo $total;?></td> 
        </tr>
        <tr>
            <td style="color:#FFFFFF;background-color:#33ccff"><strong>Cutting QC</strong></td>
            <?php
            $total = 0;
            foreach($company_location_arr as $com_id => $row){
                foreach($row as $key=>$data){?>
            <td align="right"><?php echo $cutting_qc_data_arr[$com_id][$key]+$subcon_cut_and_lay_data_arr[$com_id][$key];?></td>
            <?php $total +=$cutting_qc_data_arr[$com_id][$key]+$subcon_cut_and_lay_data_arr[$com_id][$key];} } ?>
            <td align="right"><?php echo $total;?></td> 
        </tr>
        <tr>
            <td style="color:#FFFFFF;background-color:#1a75ff"><strong>Sewing In</strong></td>
            <?php
            $total = 0;
            foreach($company_location_arr as $com_id => $row){
                foreach($row as $key=>$data){?>
            <td align="right"><?php echo $sewing_in_data_arr[$com_id][$key]+$subcon_sewing_arr[$com_id][$key];?></td>
            <?php $total +=$sewing_in_data_arr[$com_id][$key]+$subcon_sewing_arr[$com_id][$key];} } ?>
            <td align="right"><?php echo $total;?></td>
        </tr>
        <tr>
            <td style="color:#FFFFFF;background-color:#75a3a3"><strong>Sewing Out</strong></td>
            <?php
            $total = 0;
            foreach($company_location_arr as $com_id => $row){
                foreach($row as $key=>$data){?>
            <td align="right"><?php echo $sewing_out_data_arr[$com_id][$key]+$subcon_sewing_out_arr[$com_id][$key];?></td>
            <?php $total +=$sewing_out_data_arr[$com_id][$key]+$subcon_sewing_out_arr[$com_id][$key];} } ?>
            <td align="right"><?php echo $total;?></td>
        </tr>
        <tr>
            <td style="color:#FFFFFF;background-color:#53c68c"><strong>Wash Send</strong></td>
            <?php
            $total = 0;
            foreach($company_location_arr as $com_id => $row){
                foreach($row as $key=>$data){?>
            <td align="right"><?php echo $wash_send_data_arr[$com_id][$key]+$subcon_wash_send_arr[$com_id][$key];?></td>
            <?php $total +=$wash_send_data_arr[$com_id][$key]+$subcon_wash_send_arr[$com_id][$key];} } ?>
            <td align="right"><?php echo $total;?></td>
        </tr>
        <tr>
            <td style="color:#FFFFFF;background-color:#5c5cd6"><strong>Wash Received</strong></td>
            <?php
            $total = 0;
            foreach($company_location_arr as $com_id => $row){
                foreach($row as $key=>$data){?>
            <td align="right"><?php echo $wash_received_data_arr[$com_id][$key]+$subcon_wash_receive_arr[$com_id][$key];?></td>
            <?php $total +=$wash_received_data_arr[$com_id][$key]+$subcon_wash_receive_arr[$com_id][$key];} } ?>
            <td align="right"><?php echo $total;?></td>
        </tr>
        <tr>
            <td style="color:#FFFFFF;background-color:#99cc00"><strong>Finishing (Getup Pass)</strong></td>
            <?php
            $total = 0;
            foreach($company_location_arr as $com_id => $row){
                foreach($row as $key=>$data){?>
            <td align="right"><?php echo $finishing_getpass_data_arr[$com_id][$key]+$subcon_wash_finishing_arr[$com_id][$key];?></td>
            <?php $total +=$finishing_getpass_data_arr[$com_id][$key]+$subcon_wash_finishing_arr[$com_id][$key];} } ?>
            <td align="right"><?php echo $total;?></td>
        </tr>
        <tr>
            <td style="color:#FFFFFF;background-color:#009999"><strong>Packing/Carton</strong></td>
            <?php
            $total = 0;
            foreach($company_location_arr as $com_id => $row){
                foreach($row as $key=>$data){?>
            <td align="right"><?php echo $packing_carton_data_arr[$com_id][$key]+$subcon_wash_packing_arr[$com_id][$key];?></td>
            <?php $total += $packing_carton_data_arr[$com_id][$key]+$subcon_wash_packing_arr[$com_id][$key];} } ?>
            <td align="right"><?php echo $total;?></td>
        </tr>
        <tr>
            <td style="color:#FFFFFF;background-color:#069137"><strong>Ex-Factory</strong></td>
            <?php
            $total = 0;
            foreach($company_location_arr as $com_id => $row){
                foreach($row as $key=>$data){?>
            <td align="right"><?php echo $ex_factory_data_arr[$com_id][$key]+$subcon_delivery_mst_arr[$com_id][$key];?></td>
            <?php $total += $ex_factory_data_arr[$com_id][$key]+$subcon_delivery_mst_arr[$com_id][$key];} } ?>
            <td align="right"><?php echo $total;?></td>
        </tr>
         
    </tbody>
</table>
<br>
<?php
//  colspan="<?= count($row);? >" 
foreach($company_location_arr as $key=>$row){?>
<table style="width:100%">
    <tr>
        <th colspan="<?= count($cut_lay_dlts_qnty_arr[$key])+2 + count($cutting_qc_qnty_arr[$key])+2 + count($sewing_in_qnty_arr[$key][$key2])+2 + count($sewing_out_qnty_arr[$key][$key2])+2 + count($wash_send_qnty_arr[$key][$key2])+2 + count($wash_received_qnty_arr[$key][$key2])+2 + count($finishing_getpass_qnty_arr[$key][$key2])+2 + count($packing_carton_qnty_arr[$key][$key2])+2 + count($ex_factory_qnty_arr[$key][$key2])+2;?>"><?= $company_library[$key];?></th>
    </tr> 
    <?php
    $i = 1;
    foreach($row as $key2=>$data){?>
    <tr>
        <th rowspan="2">Sl</th>
        <th rowspan="2" style="color:red">Location</th>
        <th style="color:#FFFFFF;background-color:#a3a3c2" colspan="<?= count($cut_lay_dlts_qnty_arr[$key][$key2])+1;?>">Cut and Lay Qty</th>
        <th style="color:#FFFFFF;background-color:#33ccff" colspan="<?= count($cutting_qc_qnty_arr[$key][$key2])+1;?>">Cutting QC</th>
        <th style="color:#FFFFFF;background-color:#1a75ff" colspan="<?= count($sewing_in_qnty_arr[$key][$key2])+1;?>">Sewing In</th>
        <th style="color:#FFFFFF;background-color:#75a3a3" colspan="<?= count($sewing_out_qnty_arr[$key][$key2])+1;?>">Sewing Out</th>
        <th style="color:#FFFFFF;background-color:#53c68c" colspan="<?= count($wash_send_qnty_arr[$key][$key2])+1;?>">Wash Send</th>
        <th style="color:#FFFFFF;background-color:#5c5cd6" colspan="<?= count($wash_received_qnty_arr[$key][$key2])+1;?>">Wash Received</th>
        <th style="color:#FFFFFF;background-color:#99cc00" colspan="<?= count($finishing_getpass_qnty_arr[$key][$key2])+1;?>">Finishing (Getup Pass)</th>
        <th style="color:#FFFFFF;background-color:#009999" colspan="<?= count($packing_carton_qnty_arr[$key][$key2])+1;?>">Packing/Carton</th>
        <th style="color:#FFFFFF;background-color:#069137" colspan="<?= count($ex_factory_qnty_arr[$key][$key2])+1;?>">Ex-Factory</th>
    </tr>
    
    <tr>
        <?php
        foreach($cut_lay_dlts_qnty_arr[$key][$key2] as $key3=>$floor_id){
        ?>
            <th  style="text-align:left;"><?= $floor_arr[$key3];?></th>
        <?php
        }
        ?>
        <th style="text-align:center;">Total</th>


        <?php
        foreach($cutting_qc_qnty_arr[$key][$key2] as $key3=>$floor_id){
        ?>
            <th style="text-align:left;"><?= $floor_arr[$key3];?></th>
        <?php
        }
        ?>
        <th style="text-align:center;">Total</th>

        <?php
        foreach($sewing_in_qnty_arr[$key][$key2] as $key3=>$floor_id){
        ?>
            <th style="text-align:left;"><?= $floor_arr[$key3];?></th>
        <?php
        }
        ?>
        <th style="text-align:center;">Total</th>

        <?php
        foreach($sewing_out_qnty_arr[$key][$key2] as $key3=>$floor_id){
        ?>
            <th style="text-align:left;"><?= $floor_arr[$key3];?></th>
        <?php
        }
        ?>
        <th style="text-align:center;">Total</th>

        <?php
        foreach($wash_send_qnty_arr[$key][$key2] as $key3=>$floor_id){
        ?>
            <th style="text-align:left;"><?= $floor_arr[$key3];?></th>
        <?php
        }
        ?>
        <th style="text-align:center;">Total</th>


        <?php
        foreach($wash_received_qnty_arr[$key][$key2] as $key3=>$floor_id){
        ?>
            <th style="text-align:left;"><?= $floor_arr[$key3];?></th>
        <?php
        }
        ?>
        <th style="text-align:center;">Total</th>
 
        <?php
        foreach($finishing_getpass_qnty_arr[$key][$key2] as $key3=>$floor_id){
        ?>
            <th style="text-align:left;"><?= $floor_arr[$key3];?></th>
        <?php
        }
        ?>
        <th style="text-align:center;">Total</th>

        <?php
        foreach($packing_carton_qnty_arr[$key][$key2] as $key3=>$floor_id){
        ?>
            <th style="text-align:left;"><?= $floor_arr[$key3];?></th>
        <?php
        }
        ?>
        <th style="text-align:center;">Total</th>

        <?php
        foreach($ex_factory_qnty_arr[$key][$key2] as $key3=>$floor_id){
        ?>
            <th style="text-align:left;"><?= $floor_arr[$key3];?></th>
        <?php
        }
        ?>
        <th style="text-align:center;">Total</th>
    </tr>
 
    <tr>
        <th><?= $i;?></th>
        <th style="text-align:left"><?php echo $lib_location[$key2];?></th>
        <?php
        $total_cut_lay_dlts_qnty = 0;
        foreach($cut_lay_dlts_qnty_arr[$key][$key2] as $floor_qty){
            // print_r($subcon_cut_and_lay_data_delts_arr[$key][$key2][$key3]);die;  
        ?>
            <td align="right"><?= $floor_qty;?></td>
        <?php
        $total_cut_lay_dlts_qnty += $floor_qty;
        }
        ?> 
        <th align="right"><?= $total_cut_lay_dlts_qnty;?></th>


        <?php
        $total_cut_lay_dlts_qnty = 0;
        foreach($cutting_qc_qnty_arr[$key][$key2] as $floor_qty){
        ?>
            <td align="right"><?= $floor_qty+$subcon_cut_and_lay_data_delts_arr[$key][$key2][$key3];?></td>
        <?php
        $total_cut_lay_dlts_qnty += $floor_qty+$subcon_cut_and_lay_data_delts_arr[$key][$key2][$key3];
        }
        ?> 
        <th align="right"><?= $total_cut_lay_dlts_qnty;?></th>



        <?php
        $total_sewing_in_qnty = 0;
        foreach($sewing_in_qnty_arr[$key][$key2] as $floor_qty){
        ?>
            <td align="right"><?= $floor_qty+$subcon_sewing_delts_arr[$key][$key2][$key3];?></td>
        <?php
        $total_sewing_in_qnty += $floor_qty+$subcon_sewing_delts_arr[$key][$key2][$key3];
        }
        ?> 
        <th align="right"><?= $total_sewing_in_qnty;?></th>



        <?php
        $total_sewing_out_qnty = 0;
        foreach($sewing_out_qnty_arr[$key][$key2] as $floor_qty){
        ?>
            <td align="right"><?= $floor_qty+$subcon_sewing_out_delts_arr[$key][$key2][$key3];?></td>
        <?php
        $total_sewing_out_qnty += $floor_qty+$subcon_sewing_out_delts_arr[$key][$key2][$key3];
        }
        ?> 
        <th align="right"><?= $total_sewing_out_qnty;?></th>


        

        <?php
        $total_wash_send_qnty = 0;
        foreach($wash_send_qnty_arr[$key][$key2] as $floor_qty){
        ?>
            <td align="right"><?= $floor_qty+$subcon_wash_send_delts_arr[$key][$key2][$key3];?></td>
        <?php
        $total_wash_send_qnty += $floor_qty+$subcon_wash_send_delts_arr[$key][$key2][$key3];
        }
        ?> 
        <th align="right"><?= $total_wash_send_qnty;?></th>


        <?php
        $total_wash_received_qnty = 0;
        foreach($wash_received_qnty_arr[$key][$key2] as $floor_qty){
        ?>
            <td align="right"><?= $floor_qty+$subcon_wash_receive_delts_arr[$key][$key2][$key3];?></td>
        <?php
        $total_wash_received_qnty += $floor_qty+$subcon_wash_receive_delts_arr[$key][$key2][$key3];
        }
        ?> 
        <th align="right"><?= $total_wash_received_qnty;?></th>


        <?php
        $total_getpass_qnty = 0;
        foreach($finishing_getpass_qnty_arr[$key][$key2] as $floor_qty){
        ?>
            <td align="right"><?= $floor_qty+$subcon_wash_finishing_delts_arr[$key][$key2][$key3];?></td>
        <?php
        $total_getpass_qnty += $floor_qty+$subcon_wash_finishing_delts_arr[$key][$key2][$key3];
 
        }
        ?> 
        <th align="right"><?= $total_getpass_qnty;?></th>


        <?php
        $total_carton_qnty = 0;
        foreach($packing_carton_qnty_arr[$key][$key2] as $floor_qty){
        ?>
            <td align="right"><?= $floor_qty+$subcon_wash_packing_delts_arr[$key][$key2][$key3];?></td>
        <?php
        $total_carton_qnty += $floor_qty+$subcon_wash_packing_delts_arr[$key][$key2][$key3];
        }
        ?> 
        <th align="right"><?= $total_carton_qnty;?></th>

        <?php
        $total_factory_qnty = 0;
        foreach($ex_factory_qnty_arr[$key][$key2] as $floor_qty){
        ?>
            <td align="right"><?= $floor_qty;?></td>
        <?php
        $total_factory_qnty += $floor_qty;
        }
        ?> 
        <th align="right"><?= $total_factory_qnty;?></th>
 
    </tr>
    <?php
    $i++;
    }
    ?>
</table>
<br>
<?php } ?> 
 <?php
    $message = ob_get_contents();
    ob_clean();
	$mail_item = 33;
	$to='';
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=33 and b.mail_user_setup_id=c.id and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql2 = sql_select($sql2, '', '', '', $con);
	foreach($mail_sql2 as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
 
 	//$to='muktobani@gmail.com';
	$subject = "Unit Wise Garments Production Report";
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