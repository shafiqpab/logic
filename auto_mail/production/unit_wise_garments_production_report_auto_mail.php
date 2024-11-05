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
$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$lib_location = return_library_array( "SELECT COMPANY_ID, LOCATION_NAME FROM LIB_LOCATION WHERE IS_DELETED=0 AND STATUS_ACTIVE=1", "COMPANY_ID", "LOCATION_NAME");
$floor_arr = return_library_array( "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1", "id", "floor_name");
// LIB_LOCATION COMPANY_ID
$current_time = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = date("d-M-Y", $current_time);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))), '', '', 1);
// $date_condition	=" and b.insert_date between '".$prev_date."' and '".$prev_date."'";
$date_condition= " and a.insert_date between '".$prev_date."' and '".$prev_date." 11:59:59 PM' ";


//----------------------------------------------------
//$previous_date1= "01-Dec-2023";
//$previous_date2= "31-Dec-2023";
//$date_condition1= " and a.insert_date between '".$previous_date1."' and '".$previous_date2." 11:59:59' ";


//Cut and Lay Qty--------------------
$cut_lay_sql = "SELECT a.id, a.job_no, a.job_year, a.working_company_id as company_id, a.floor_id, b.id as cut_dlts_id, b.order_ids, c.id as bundle_id, c.size_qty,c.order_id FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c WHERE a.id=b.mst_id and b.mst_id=c.mst_id  $date_condition and a.status_active=1 and a.is_deleted=0"; 
// echo $cut_lay_sql;die;
$cut_lay_res = sql_select($cut_lay_sql);
$cut_and_lay_data_arr = array();
$cut_lay_dlts_floor_arr = array();
$cut_lay_dlts_qnty_arr = array();
foreach($cut_lay_res as $row){
    $cut_and_lay_data_arr[$row['COMPANY_ID']] += $row['SIZE_QTY'];
    $cut_lay_dlts_floor_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];
    $cut_lay_dlts_qnty_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] += $row['SIZE_QTY'];
}
//echo "<pre>";
//print_r($cut_and_lay_data_arr);die;


//Cutting QC--------------------
//$cut_lay_sql = "SELECT a.id, a.job_no, a.job_year, a.working_company_id as company_id, a.floor_id, b.id as cut_dlts_id, b.order_ids, c.id as bundle_id, c.size_qty,c.order_id FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c WHERE a.id=b.mst_id and b.mst_id=c.mst_id  $date_condition and a.status_active=1 and a.is_deleted=0"; 


//$cut_lay_sql = "SELECT a.id, a.job_no, a.job_year, a.working_company_id as company_id, b.id, b.order_ids, c.id as cutting_id, c.qc_pass_qty FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b, pro_gmts_cutting_qc_dtls c WHERE a.id=b.mst_id and b.mst_id=c.mst_id $date_condition and a.status_active=1 and a.is_deleted=0"; 


$cut_lay_sql = "SELECT a.id, a.serving_company as company_id, a.floor_id, b.production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls b WHERE a.id=b.mst_id and  b.production_type=1 and a.status_active=1 and a.is_deleted=0 $date_condition";
//echo $cut_lay_sql;die;

$cut_lay_res = sql_select($cut_lay_sql);
$cutting_qc_data_arr = array();
$cutting_qc_floor_arr = array();
$cutting_qc_qnty_arr = array();
foreach($cut_lay_res as $row){
    $cutting_qc_data_arr[$row['COMPANY_ID']] += $row['PRODUCTION_QNTY'];
    $cutting_qc_floor_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];
    $cutting_qc_qnty_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
}
  
// Sewing In----------
$sewing_in_sql = "SELECT a.id, a.serving_company as company_id, a.floor_id, b.production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls b WHERE a.id=b.mst_id and  b.production_type=4 and a.status_active=1 and a.is_deleted=0 $date_condition";
//echo $sewing_in_sql;die;
$sewing_in_res = sql_select($sewing_in_sql);

$sewing_in_data_arr = array();
$sewing_in_dlts_arr = array();
$sewing_in_qnty_arr = array();
foreach($sewing_in_res as $row){
    $sewing_in_data_arr[$row['COMPANY_ID']] += $row['PRODUCTION_QNTY'];
    $sewing_in_dlts_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];
    $sewing_in_qnty_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
}
 
 
// Sewing Out
$sewing_out_sql = "SELECT a.id, a.serving_company as company_id, a.floor_id, b.production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls b WHERE a.id=b.mst_id and  b.production_type=5 and a.status_active=1 and a.is_deleted=0 $date_condition";
$sewing_out_res = sql_select($sewing_out_sql);
$sewing_out_data_arr = array();
$sewing_out_dlts_arr = array();
$sewing_out_qnty_arr = array();
foreach($sewing_out_res as $row){
   // $sewing_out_data_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
    $sewing_out_data_arr[$row['COMPANY_ID']] += $row['PRODUCTION_QNTY'];
    $sewing_out_dlts_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];
    $sewing_out_qnty_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
    
}
 
/// Wash Send ---------
$wash_send_sql = "SELECT a.id, a.serving_company as company_id, a.floor_id, b.production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls b WHERE a.id=b.mst_id and a.EMBEL_NAME=3 and b.production_type=2 and a.status_active=1 and a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 $date_condition";
//echo $wash_send_sql ;die;
$wash_send_res = sql_select($wash_send_sql);
$wash_send_data_arr = array();
$wash_send_dlts_arr = array();
$wash_send_qnty_arr = array();
foreach($wash_send_res as $row){
   // $sewing_out_data_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
    $wash_send_data_arr[$row['COMPANY_ID']] += $row['PRODUCTION_QNTY'];
    $wash_send_dlts_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];
    $wash_send_qnty_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
}
  

// Wash Received--------
$wash_received_sql = "SELECT a.id, a.serving_company as company_id, a.floor_id, b.production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls b WHERE a.id=b.mst_id and  a.EMBEL_NAME=3 and b.production_type=3 and a.status_active=1 and a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 $date_condition";
$wash_received_res = sql_select($wash_received_sql);
$wash_received_data_arr = array();
$wash_received_dlts_arr = array();
$wash_received_qnty_arr = array();
foreach($wash_received_res as $row){
    $wash_received_data_arr[$row['COMPANY_ID']] += $row['PRODUCTION_QNTY'];
    $wash_received_dlts_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];
    $wash_received_qnty_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] +=  $row['PRODUCTION_QNTY'];
     
}
  

// Finishing (Getup Pass)--------
$finishing_getpass_sql = "SELECT a.id, a.serving_company as company_id, a.floor_id, b.production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls b WHERE a.id=b.mst_id and  b.production_type=7 and a.status_active=1 and a.is_deleted=0 $date_condition";
$finishing_getpass_res = sql_select($finishing_getpass_sql);
$finishing_getpass_data_arr = array();
$finishing_getpass_dlts_arr = array();
$finishing_getpass_qnty_arr = array();
foreach($finishing_getpass_res as $row){
    $finishing_getpass_data_arr[$row['COMPANY_ID']] += $row['PRODUCTION_QNTY'];
    $finishing_getpass_dlts_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];
    $finishing_getpass_qnty_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] +=$row['PRODUCTION_QNTY'];
}

 
// Packing/Carton ----------------- 
$packing_carton_sql = "SELECT a.id, a.serving_company as company_id, a.floor_id, b.production_qnty FROM pro_garments_production_mst a, pro_garments_production_dtls b WHERE a.id=b.mst_id and  b.production_type=8 and a.status_active=1 and a.is_deleted=0 $date_condition";
$packing_carton_res = sql_select($packing_carton_sql);
$packing_carton_data_arr = array();
$packing_carton_dlts_arr = array();
$packing_carton_qnty_arr = array();
foreach($packing_carton_res as $row){
    $packing_carton_data_arr[$row['COMPANY_ID']] += $row['PRODUCTION_QNTY'];
    $packing_carton_dlts_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] =  $row['FLOOR_ID'];
    $packing_carton_qnty_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] += $row['PRODUCTION_QNTY'];
}

 

// Ex-Factory......... 
$ex_factory_sql = "SELECT a.id, a.ex_factory_qnty, b.delivery_company_id as company_id FROM pro_ex_factory_mst a, pro_ex_factory_delivery_mst b WHERE a.delivery_mst_id=b.id $date_condition and b.entry_form!=85 and a.status_active=1 and a.is_deleted=0";
$ex_factory_res = sql_select($ex_factory_sql);
$ex_factory_data_arr = array();
$ex_factory_dlts_arr = array();
$ex_factory_qnty_arr = array();
foreach($ex_factory_res as $row){
    $ex_factory_data_arr[$row['COMPANY_ID']] += $row['EX_FACTORY_QNTY'];
    $ex_factory_dlts_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];
    $ex_factory_qnty_arr[$row['COMPANY_ID']][$row['FLOOR_ID']] = $row['FLOOR_ID'];    
}

//echo $date_condition;die;
ob_start();
?>
<body>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        .border-none th{
            border:none;
        }
        .border-none td{
            border:none;
        }
    </style>  
<h2 style="text-align:center">Unit Wise Garments Production Report (Date-<?= change_date_format($prev_date);?>)</h2>
<table style="width:80%">
    <tr>
        <th>Sl</th>
        <th>Garments Production</th>
        <?php
        foreach($company_library as $company_id => $company)
        {
        ?>
            <th style="background-color: #0088cc;color:white"><?= $company;?>(<?= $lib_location[$company_id];?>)</th>
        <?php 
        } 
        ?> 
        <th>Group Total</th>
    </tr> 
    <tr>
        <th>1</th>
        <th style="text-align:left">Cut and Lay Qty</th>
        <?php
        $total_cut_lay_qty = 0;
        foreach($company_library as $company_id => $company)
        {
            ?>
            <td style="text-align:right"><?= $cut_and_lay_data_arr[$company_id];?></td>
            <?php
            $total_cut_lay_qty += $cut_and_lay_data_arr[$company_id];
        }
        ?>
         
        <td style="text-align:right"><?= $total_cut_lay_qty;?></td>
    </tr> 
    <tr>
        <th>2</th>
        <th style="text-align:left">Cutting QC</th>
        <?php
        $total_qc_qty = 0;
        foreach($company_library as $company_id => $company)
        {
            ?>
            <td style="text-align:right"><?= $cutting_qc_data_arr[$company_id];?></td>
            <?php
            $total_qc_qty += $cutting_qc_data_arr[$company_id];
        }
        ?>
         
        <td style="text-align:right"><?= $total_qc_qty;?></td>
    </tr>
    <tr>
        <th>3</th>
        <th style="text-align:left">Sewing In</th>
        <?php
        $total_sewing_in_qty = 0;
        foreach($company_library as $company_id => $company)
        {
            ?>
            <td style="text-align:right"><?= $sewing_in_data_arr[$company_id];?></td>
            <?php
            $total_sewing_in_qty += $sewing_in_data_arr[$company_id];
        }
        ?>
         
        <td style="text-align:right"><?= $total_sewing_in_qty;?></td>
    </tr>
    
    <tr>
        <th>4</th>
        <th style="text-align:left">Sewing Out</th>
        <?php
        $total_sewing_out_qty = 0;
        foreach($company_library as $company_id => $company)
        {
            ?>
            <td style="text-align:right"><?= $sewing_out_data_arr[$company_id];?></td>
            <?php
            $total_sewing_out_qty += $sewing_out_data_arr[$company_id];
        }
        ?>
         
        <td style="text-align:right"><?= $total_sewing_out_qty;?></td>
    </tr>
    <tr>
        <th>5</th>
        <th style="text-align:left">Wash Send</th>
        <?php
        $total_wash_send_qty = 0;
        foreach($company_library as $company_id => $company)
        {
            ?>
            <td style="text-align:right"><?= $wash_send_data_arr[$company_id];?></td>
            <?php
            $total_wash_send_qty += $wash_send_data_arr[$company_id];
        }
        ?>
        <td style="text-align:right"><?= $total_wash_send_qty;?></td>
    </tr>

    <tr>
        <th>6</th>
        <th style="text-align:left">Wash Received</th>
        <?php
        $total_wash_received_qty = 0;
        foreach($company_library as $company_id => $company)
        {
            ?>
            <td style="text-align:right"><?= $wash_received_data_arr[$company_id];?></td>
            <?php
            $total_wash_received_qty += $wash_received_data_arr[$company_id];
        }
        ?>
        <td style="text-align:right"><?= $total_wash_received_qty;?></td>
    </tr>
    <tr>
        <th>7</th>
        <th style="text-align:left">Finishing (Getup Pass)</th>
        <?php
        $total_finishing_getpass_qty = 0;
        foreach($company_library as $company_id => $company)
        {
            ?>
            <td style="text-align:right"><?= $finishing_getpass_data_arr[$company_id];?></td>
            <?php
            $total_finishing_getpass_qty += $finishing_getpass_data_arr[$company_id];
        }
        ?>
        <td style="text-align:right"><?= $total_finishing_getpass_qty;?></td>
    </tr>
    <tr>
        <th>8</th>
        <th style="text-align:left">Packing/Carton</th>
        
        <?php
        $total_packing_carton_qty = 0;
        foreach($company_library as $company_id => $company)
        {
            ?>
            <td style="text-align:right"><?= $packing_carton_data_arr[$company_id];?></td>
            <?php
            $total_packing_carton_qty += $packing_carton_data_arr[$company_id];
        }
        ?>
        <td style="text-align:right"><?= $total_packing_carton_qty;?></td>
    </tr>

    <tr>
        <th>9</th>
        <th style="text-align:left">Ex-Factory</th>

        <?php
        $total_ex_factory_qty = 0;
        foreach($company_library as $company_id => $company)
        {
            ?>
            <td style="text-align:right"><?= $ex_factory_data_arr[$company_id];?></td>
            <?php
            $total_ex_factory_qty += $ex_factory_data_arr[$company_id];
        }
        ?>
        <td style="text-align:right"><?= $total_ex_factory_qty;?></td>
    </tr>
    
</table>
<br><br>
<?php
$i = 1;
//print_r($company_library);die;
foreach($company_library as $company_id => $company)
{

    $one1 = count($cut_lay_dlts_floor_arr[$company_id])+1;
    $one2 = count($cutting_qc_floor_arr[$company_id])+1;
    $one3 = count($sewing_in_dlts_arr[$company_id])+1;
    $one4 = count($sewing_out_dlts_arr[$company_id])+1;
    $one5 = count($wash_send_dlts_arr[$company_id])+1;
    $one6 = count($wash_received_dlts_arr[$company_id])+1;
    $one66 = count($finishing_getpass_dlts_arr[$company_id])+1;
    $one7 = count($finishing_getpass_dlts_arr[$company_id])+1;
    $one8 = count($packing_carton_dlts_arr[$company_id])+1;
    $one9 = count($ex_factory_dlts_arr[$company_id])+1;

    $total_colspan = $one1+$one2+$one3+$one4+$one5+$one6+$one66+$one7+$one8+$one9+2;
 
    ?>
    

        <table style="width:90%">
            <tr>
                <th colspan="<?= $total_colspan;?>"><?= $company;?></th>
            </tr> 
            <tr>
                <th rowspan="2">Sl</th>
                <th rowspan="2">Location</th>
                <th colspan="<?= count($cut_lay_dlts_floor_arr[$company_id])+1;?>">Cut and Lay Qty</th>
                <th colspan="<?= count($cutting_qc_floor_arr[$company_id])+1;?>">Cutting QC</th>
                <th colspan="<?= count($sewing_in_dlts_arr[$company_id])+1;?>">Sewing In</th>
                <th colspan="<?= count($sewing_out_dlts_arr[$company_id])+1;?>">Sewing Out</th>
                <th colspan="<?= count($wash_send_dlts_arr[$company_id])+1;?>">Wash Send</th>
                <th colspan="<?= count($wash_received_dlts_arr[$company_id])+1;?>">Wash Received</th>
                <th colspan="<?= count($finishing_getpass_dlts_arr[$company_id])+1;?>">Finishing (Getup Pass)</th>
                <th colspan="<?= count($packing_carton_dlts_arr[$company_id])+1;?>">Packing/Carton</th>
                <th colspan="<?= count($ex_factory_dlts_arr[$company_id])+1;?>">Ex-Factory</th>
            </tr>
            <tr>

                <?php
                foreach($cut_lay_dlts_floor_arr[$company_id] as $floor_id){
                ?>
                    <th style="text-align:left;"><?= $floor_arr[$floor_id];?></th>
                <?php
                }
                ?> 
                <th style="text-align:left;">Total</th>


                <?php
                foreach($cutting_qc_floor_arr[$company_id] as $floor_id){
                ?>
                    <th style="text-align:left;"><?= $floor_arr[$floor_id];?></th>
                <?php
                }
                ?>
                <th style="text-align:left;">Total</th>


                <?php
                foreach($sewing_in_dlts_arr[$company_id] as $floor_id){
                ?>
                    <th style="text-align:left;"><?= $floor_arr[$floor_id];?></th>
                <?php
                }
                ?>
                <th style="text-align:left;">Total</th>

                <?php
                foreach($sewing_out_dlts_arr[$company_id] as $floor_id){
                ?>
                    <th style="text-align:left;"><?= $floor_arr[$floor_id];?></th>
                <?php
                }
                ?>
                <th style="text-align:left;">Total</th>

                <?php
                foreach($wash_send_dlts_arr[$company_id] as $floor_id){
                ?>
                    <th style="text-align:left;"><?= $floor_arr[$floor_id];?></th>
                <?php
                }
                ?>
                <th style="text-align:left;">Total</th>
                 
                <?php
                foreach($wash_received_dlts_arr[$company_id] as $floor_id){
                ?>
                    <th style="text-align:left;"><?= $floor_arr[$floor_id];?></th>
                <?php
                }
                ?>
                <th style="text-align:left;">Total</th>

                <?php
                foreach($finishing_getpass_dlts_arr[$company_id] as $floor_id){
                ?>
                    <th style="text-align:left;"><?= $floor_arr[$floor_id];?></th>
                <?php
                }
                ?>
                <th style="text-align:left;">Total</th>
                 


                <?php
                foreach($packing_carton_dlts_arr[$company_id] as $floor_id){
                ?>
                    <th style="text-align:left;"><?= $floor_arr[$floor_id];?></th>
                <?php
                }
                ?>
                <th style="text-align:left;">Total</th>

 

                <?php
                foreach($ex_factory_dlts_arr[$company_id] as $floor_id){
                ?>
                    <th style="text-align:left;"><?= $floor_arr[$floor_id];?></th>
                <?php
                }
                ?>
                <th style="text-align:left;">Total</th>

  
            </tr>

            <tr>
                <th>1</th>
                <th style="text-align:left"><?= $lib_location[$company_id];?></th>

                <?php
                $total_cut_lay_dlts_qnty = 0;
                foreach($cut_lay_dlts_qnty_arr[$company_id] as $floor_qty){
                ?>
                    <td style="text-align:left;"><?= $floor_qty;?></td>
                <?php
                $total_cut_lay_dlts_qnty += $floor_qty;
                }
                ?> 
                <th style="text-align:right"><?= $total_cut_lay_dlts_qnty;?></th>


                <?php
                $total_cutting_qc_qnty = 0;
                foreach($cutting_qc_qnty_arr[$company_id] as $floor_qty){
                ?>
                    <td style="text-align:left;"><?= $floor_qty;?></td>
                <?php
                $total_cutting_qc_qnty += $floor_qty;
                }
                ?> 
                <th style="text-align:right"><?= $total_cutting_qc_qnty;?></th>

                
                <?php
                $total_sewing_in_qnty = 0;
                foreach($sewing_in_qnty_arr[$company_id] as $floor_qty){
                ?>
                    <td style="text-align:left;"><?= $floor_qty;?></td>
                <?php
                $total_sewing_in_qnty += $floor_qty;
                }
                ?> 
                <th style="text-align:right"><?= $total_sewing_in_qnty;?></th>

                <?php
                $total_sewing_out_qnty = 0;
                foreach($sewing_out_qnty_arr[$company_id] as $floor_qty){
                ?>
                    <td style="text-align:left;"><?= $floor_qty;?></td>
                <?php
                $total_sewing_out_qnty += $floor_qty;
                }
                ?> 
                <th style="text-align:right"><?= $total_sewing_out_qnty;?></th>

                <?php
                $total_wash_send_qnty = 0;
                foreach($wash_send_qnty_arr[$company_id] as $floor_qty){
                ?>
                    <td style="text-align:left;"><?= $floor_qty;?></td>
                <?php
                $total_wash_send_qnty += $floor_qty;
                }
                ?> 
                <th style="text-align:right"><?= $total_wash_send_qnty;?></th>

                <?php
                $total_wash_received_qnty = 0;
                foreach($wash_received_qnty_arr[$company_id] as $floor_qty){
                ?>
                    <td style="text-align:left;"><?= $floor_qty;?></td>
                <?php
                $total_wash_received_qnty += $floor_qty;
                }
                ?> 
                <th style="text-align:right"><?= $total_wash_received_qnty;?></th>

                <?php
                $total_finishing_getpass_qnty = 0;
                foreach($finishing_getpass_qnty_arr[$company_id] as $floor_qty){
                ?>
                    <td style="text-align:left;"><?= $floor_qty;?></td>
                <?php
                $total_finishing_getpass_qnty += $floor_qty;
                }
                ?> 
                <th style="text-align:right"><?= $total_finishing_getpass_qnty;?></th>


                <?php
                $total_packing_carton_qnty = 0;
                foreach($packing_carton_qnty_arr[$company_id] as $floor_qty){
                ?>
                    <td style="text-align:left;"><?= $floor_qty;?></td>
                <?php
                $total_packing_carton_qnty += $floor_qty;
                }
                ?> 
                <th style="text-align:right"><?= $total_packing_carton_qnty;?></th>


                <?php
                $total_ex_factory_dlts = 0;
                foreach($ex_factory_dlts_arr[$company_id] as $floor_qty){
                ?>
                    <td style="text-align:left;"><?= $floor_qty;?></td>
                <?php
                $total_ex_factory_dlts += $floor_qty;
                }
                ?> 
                <th style="text-align:right"><?= $total_ex_factory_dlts;?></th>

 
                    
  
            </tr>
        </table>
        <br>
    </body>
<?php
}
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