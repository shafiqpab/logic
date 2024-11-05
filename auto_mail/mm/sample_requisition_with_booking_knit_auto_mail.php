<?php
/*-------------------------------------------- Comments
Purpose         :   Sample requisition with booking Woven auto mail
Functionality   :   
JS Functions    :
Created by      :   Al-Hasan
Creation date   :   06-12-2023
Updated by      :
Update date     :  
QC Performed BY :
QC Date         :
Comment*/

date_default_timezone_set("Asia/Dhaka");
require_once('../../includes/common.php');
require_once('../setting/mail_setting.php');


$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$dealing_merchant_library = return_library_array("select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");

$current_time = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = date("d-M-Y", $current_time);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))), '', '', 1);
$date_condition	=" and requisition_date between '".$prev_date."' and '".$prev_date."'";

$i = 1;
foreach($company_library as $company_id => $company)
{ 
    ob_start();
    ?>
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
    <?php
    $sql = "SELECT id, requisition_number, company_id, style_ref_no, style_desc, buyer_name, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date, season_year, brand_id, internal_ref, revised_no as revised FROM sample_development_mst WHERE company_id='$company_id' and entry_form_id=203 and is_deleted=0 and status_active=1 $date_condition ORDER BY id DESC";

    $dataArray = sql_select($sql); 
    $id = $dataArray[0][csf('id')];
    
    $booking_sqls = "SELECT a.booking_no, a.is_approved, a.currency_id, a.fabric_source, a.pay_mode, a.team_leader, a.dealing_marchant, a.ready_to_approved,a.supplier_id, a.booking_date, a.revised_no, a.style_desc, a.attention, a.revised_number FROM  wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b WHERE a.booking_no = b.booking_no and b.style_id = '$id' and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 group by a.booking_no, a.is_approved, a.currency_id, a.fabric_source, a.pay_mode, a.team_leader, a.dealing_marchant, a.ready_to_approved, a.supplier_id, a.booking_date, a.revised_no, a.style_desc, a.attention, a.revised_number";
    // echo $booking_sqls;
    $booking_res = sql_select($booking_sqls);
    $booking_no = $booking_res[0]['BOOKING_NO'];

    if(count($dataArray)>0)
    {
        // Sample Products Quantity
        $dtls_sql = "SELECT id, sample_mst_id, sample_color, sample_prod_qty, delv_end_date, embellishment_status FROM sample_development_dtls WHERE entry_form_id=203 and sample_mst_id='$id' and is_deleted=0 and status_active=1";
        $dtls_res = sql_select($dtls_sql);
        $sample_product_qty_arr = array();
        $sample_del_end_date_arr = array();
        foreach($dtls_res as $row)
        {
            $sample_product_qty_arr[$row['SAMPLE_MST_ID']] += $row['SAMPLE_PROD_QTY'];
            $sample_del_end_date_arr[$row['SAMPLE_MST_ID']] = $row['DELV_END_DATE'];
        } 

        // Fabric Required Quantity
        $fabric_sql = "SELECT id, sample_mst_id, required_qty FROM sample_development_fabric_acc WHERE sample_mst_id='$id' and form_type=1 and  is_deleted=0  and status_active=1";
        $fabric_res = sql_select($fabric_sql);
        $fabric_required_qty_arr = array();
        foreach($fabric_res as $row)
        {
            $fabric_required_qty_arr[$row['SAMPLE_MST_ID']] += $row['REQUIRED_QTY'];
        }
        
        // Embellishment 
        $embellishment_sql = "SELECT id, sample_mst_id, name_re FROM sample_development_fabric_acc WHERE sample_mst_id='$id' and form_type=3 and  is_deleted=0  and status_active=1";
        $embellishment_res = sql_select($embellishment_sql);
        $embellishment_arr = array();
        foreach($embellishment_res as $row)
        {
            $embellishment_arr[$row['ID']][$row['SAMPLE_MST_ID']] = $row['NAME_RE'];
        }
        
    ?>
    <body>
    <table class="border-none" style="width:100%;border: none">
            <tr>
                <th style="text-align: center;" colspan="14"><h1><?= $company_library[$company_id]; ?></h1></th>
            <tr>
            <tr>
                <td style="text-align: center;" colspan="14">
                   <?php
                    $val = sql_select("SELECT plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website FROM lib_company WHERE id = $company_id");
                    echo ($val[0][csf('level_no')])? $val[0][csf('level_no')].',': "";
                    echo ($val[0][csf('road_no')]) ? $val[0][csf('road_no')].',': "";
                    echo ($val[0][csf('block_no')]) ? $val[0][csf('block_no')].',': "";
                    echo ($val[0][csf('city')]) ? $val[0][csf('city')].',': "";
                    echo ($val[0][csf('zip_code')]) ? $val[0][csf('zip_code')].',': "";
                    echo ($val[0][csf('province')]) ? $val[0][csf('province')].',': "";
                    echo($val[0][csf('country_id')]) ? $country_arr[$val[0][csf('country_id')]]: "";
                    echo ($val[0][csf('email')]) ? "</br>". $val[0][csf('email')].',': "</br>";
                    echo($val[0][csf('website')]) ? $val[0][csf('website')]: "";
                    ?>
                </td>
            <tr>
        </table>
        <br>
        <table class="border-none" style="width:100%;border: none">
            <tr>
                <th style="text-align: center;" colspan="14">Sample Requisition with Booking Knit</th>
            <tr>
        </table>
        <br>
        <table style="width:100%">
            <tr>
                <th style="text-align: left;" colspan="14">Daily Sample Requisition</th>
            <tr>
            <tr>
                <th>Sl</th>
                <th>Requisition No</th>
                <th>Booking No</th>
                <th>Date</th>
                <th>Company</th>
                <th>Dealing Merchant</th>
                <th>Buyer</th>
                <th>Season</th>
                <th>Style Ref</th>
                <th>Sample Qty</th>
                <th>Fabric Qty</th>
                <th>Embellishment</th>
                <th>Confirm Del. End Date</th>
                <th>Remarks</th>
            </tr>
            <tr>
                <td><?= $i;?></td>
                <td><?= $dataArray[0]['REQUISITION_NUMBER'];?></td>
                <td><?= $booking_no;?></td>
                <td><?= change_date_format($dataArray[0]['REQUISITION_DATE']);?></td>
                <td><?= $company_library[$dataArray[0]['COMPANY_ID']]; ?></td>
                <td><?= $dealing_merchant_library[$dataArray[0]['DEALING_MARCHANT']];?></td>
                <td><?= $buyer_library[$dataArray[0]['BUYER_NAME']];?></td>
                <td><?= $dataArray[0]['SEASON'];?></td>
                <td><?= $dataArray[0]['STYLE_REF_NO'];?></td>
                <td><?= $sample_product_qty_arr[$dataArray[0]['ID']];?></td>
                <td><?= $fabric_required_qty_arr[$dataArray[0]['ID']];?></td>
                <td>
                    <?php
                    $my_string = '';
                    foreach($embellishment_arr as $ids){
                        foreach($ids as $row){
                            $my_string .= $emblishment_name_array[$row].',';
                        }
                    }
                    echo $string = rtrim($my_string, ',');
                    ?> 
                </td>
                <td><?= change_date_format($sample_del_end_date_arr[$dataArray[0]['ID']]);?></td>
                <td><?= $dataArray[0]['REMARKS'];?></td>
            </tr>
             
        </table>
    </body>
    <br>
    <?php 
    $i++;
    }
    
} 
?>