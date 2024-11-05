<?php

header('Content-type:text/html; charset=utf-8');
session_start();

require_once('../../../includes/common.php');
if (!function_exists('pre')) 
{
    function pre($array){
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }
}

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$show_extra_list = 1;
if($action=="report_generate")
{
    $process = array(&$_POST);
    // ================================================================================
    //                                LIBRARY ARRAY
    // ================================================================================
    $lib_line_arr  = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
    $lib_floor_arr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	// pre($process);die;
	extract(check_magic_quote_gpc( $process )); 
    $company_id = return_field_value('id','lib_company',"company_name='$comp_name'");
    $location_id = return_field_value('id','lib_location',"location_name='$location_name' and company_id=$company_id");
    $unit_id = return_field_value('id','lib_prod_floor',"floor_name='$unit_name'");
    // echo " company_id = $company_id <br> location_id = $location_id <br> unit_id = $unit_id";die;
    // ================================================================================
    //                                 PROD RESOURCE DATA
    // ================================================================================
    $current_date = date('d-M-Y');
    // $current_date = '19-Oct-2023';

    $sql_cond = "";
    $sql_cond .= $company_id    ?   " and a.company_id=$company_id "    : "";
    $sql_cond .= $location_id   ?   " and a.location_id=$location_id "  : "";
    $sql_cond .= $unit_id       ?   " and a.floor_id=$unit_id "         : "";
    $date_cond = "and c.pr_date='$current_date'";
    // echo $date_cond; die;
    $resource_sql = "SELECT a.id as line,a.floor_id,a.line_number,c.target_per_hour as target,c.working_hour,b.target_efficiency as target_effi,TO_CHAR(d.prod_start_time, 'HH24') as prod_start_hour,TO_CHAR(d.lunch_start_time, 'HH24') as lunch_start_hour from prod_resource_mst a, prod_resource_dtls_mast b,prod_resource_dtls c,prod_resource_dtls_time d where a.id=b.mst_id and b.id=c.mast_dtl_id and c.mast_dtl_id=d.mast_dtl_id and d.shift_id=1  $sql_cond $date_cond and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 ";
    // echo $resource_sql; die;
    $resource_sql_res =sql_select($resource_sql);
    $line_arr = array();
    $data_arr = array();
    foreach ($resource_sql_res as $v) 
    {
        $exp_line =explode(',',$v['LINE_NUMBER']);
        
        $line_number_arr = array();
        foreach ($exp_line as $line) {
            $line_number_arr[$line]=$lib_line_arr[$line];
        }

        $start_time     = $v['PROD_START_HOUR'].":00" ;
        $end_time_cal   = $v['PROD_START_HOUR']+ $v['WORKING_HOUR']; 
        $end_time       = $end_time_cal .':00';
        $lunch_start_time = $v['LUNCH_START_HOUR'].':00';
        $lunch_end_time = ($v['LUNCH_START_HOUR']+1).':00';
        $current_time   = date('H:i');
        

        // Convert the times to seconds using strtotime() function
        $start_seconds = strtotime($start_time);
        $end_seconds   = strtotime($end_time);
        $lunch_start_seconds = strtotime($lunch_start_time);
        $lunch_end_seconds = strtotime($lunch_end_time);
        
        $current_seconds = strtotime($current_time);
        if ($current_seconds > $end_seconds) 
        {
            $current_seconds = $end_seconds;
        }
 
        $diff_seconds = $current_seconds - $start_seconds; 
        $a=0;
        if ($current_seconds >= $lunch_start_seconds) 
        {
            if ($current_seconds < $lunch_end_seconds) 
            {
                $diff_seconds -= ($current_seconds - $lunch_start_seconds);
            }
            else
            {
                $diff_seconds -= ($lunch_end_seconds - $lunch_start_seconds);
            }
        }
        $working_min = $diff_seconds / 60;
        $target_per_min = $v['TARGET']/60;
        $current_target =  $working_min *   $target_per_min; 

        $line_arr [$v['LINE']]= $v['LINE'];
        $data_arr[$v['LINE']]['TARGET_EFFI']   = $v['TARGET_EFFI'];
        $data_arr[$v['LINE']]['TARGET']        = $current_target;
        $data_arr[$v['LINE']]['TARGET_TITLE']   = "(".$working_min. " X ".  $target_per_min .")";
        $data_arr[$v['LINE']]['FLOOR_ID']      = $v['FLOOR_ID'];
        $data_arr[$v['LINE']]['LINE_NUMBER']   = implode(',',$line_number_arr);
    }
    // pre($data_arr);die;
    // ================================================================================
    //                                 PRODUCTION DATA
    // ================================================================================
    // echo  $resource_sql; die;
    $prod_cond = "";
    $prod_cond .= $company_id    ?   " and a.company_id=$company_id "    : "";
    $prod_cond .= $location_id   ?   " and a.location=$location_id "  : ""; 
    $date_cond = " and a.PRODUCTION_DATE='$current_date'";
    $line_cond = where_con_using_array($line_arr,0,'a.sewing_line'); 

    $prod_sql = "SELECT a.sewing_line as line,b.production_qnty as prod_qty,b.alter_qty,b.spot_qty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id $prod_cond $date_cond $line_cond and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
    // echo  $prod_sql; die;
    $prod_sql_res =sql_select($prod_sql);
    $prod_data = array();
    foreach ($prod_sql_res as $v) 
    { 
        $prod_qty   = $v['PROD_QTY']    ?? 0 ; 
        $alter_qty  = $v['ALTER_QTY']   ?? 0 ; 
        $spot_qty   = $v['SPOT_QTY']    ?? 0 ; 
        $alter_spot_qty = $alter_qty + $spot_qty;
        $rft_qty    = $prod_qty  - $alter_spot_qty;

        $data_arr[$v['LINE']]['PROD_QTY']   += $prod_qty;
        $data_arr[$v['LINE']]['DHU']        += $alter_spot_qty ;
        $data_arr[$v['LINE']]['RFT_QTY']    += $rft_qty ;

    }
    // pre($data_arr);
    // ================================================================================
    //                                      DATA MAKING
    // ================================================================================
    $line_perfomance_array = array();
    foreach ($data_arr as $line => $v) 
    {
        $actual_prod = $v['PROD_QTY'];
        $dhu         = $v['DHU'];
        $target      = $v['TARGET'];
        $target_effi = $v['TARGET_EFFI'];
        $rft_target  = 100;
        
        $ach_per    = ($actual_prod / $target)*100;
        $ach_per_title    = "(".$actual_prod ."/". $target.") X 100";
        $rft_ach    = 100 - ($dhu/$actual_prod*100);
        $effi_ach   = ($actual_prod * $target_effi)/$target ;
        $effi_ach_title   = "(Actual Production X Efficiency) / Target \n\n(".$actual_prod ."X". $target_effi.") / ".$target;
        $data_arr[$line]['RFT_ACH'] = $rft_ach ;

        $kpi = ($ach_per * 0.3) + ($effi_ach * 0.4) + ($rft_ach * 0.4) ;
        $kpi = is_nan( $kpi) ? 0 : $kpi; 
        // if ($v['PROD_QTY']) 
        // { 
            $line_perfomance_array[$line]['FLOOR_ID']     = $v['FLOOR_ID'];
            $line_perfomance_array[$line]['LINE_NUMBER']  = $v['LINE_NUMBER'];
            $line_perfomance_array[$line]['TARGET']       = $v['TARGET'];
            $line_perfomance_array[$line]['TARGET_TITLE'] = "Formula = (Working Min / Target Per Min)  \n\n".$v['TARGET_TITLE']; 
            $line_perfomance_array[$line]['PROD_QTY']     = $v['PROD_QTY'];
            $line_perfomance_array[$line]['ACH_PER']      = $ach_per; 
            $line_perfomance_array[$line]['ACH_PER_TITLE'] = "Formula = (Actual Production / Target) X 100 \n\n$ach_per_title"; 
            $line_perfomance_array[$line]['EFFI']         = $effi_ach ;//$v['TARGET_EFFI']
            $line_perfomance_array[$line]['EFFI_TITLE']   = $effi_ach_title ;
            $line_perfomance_array[$line]['DHU']          = $v['DHU']; 
            $line_perfomance_array[$line]['KPI']          = $kpi;  

            $line_wise_kpi[$line]= $kpi; 
        // }
    } 
    arsort($line_wise_kpi); // Sort Desc depends on KPI 
    // pre($line_perfomance_array);die;

    $top7_line   = array_slice($line_wise_kpi, 0, 7, true);
    $last7_line  = array_slice($line_wise_kpi, -7, 7, true);
    $others      = array_slice($line_wise_kpi, 7, -7, true);
    $total_others = count($others);



    $others_line = array_slice($line_wise_kpi, 7, -7, true);
    // pre($others_line);die; 
    
    ?>   
        <style>
            body{
                background: #000000;
            } 
            .title-box{
                margin-top: 2%;
                border: 2px solid #0984bd;
                /* width: 70%; */
                text-align: center;
                margin-bottom: 3%;
            }
            .page-title{
                color: #4598bf;
                font-size: 3vw;
                padding: 10px;
                
            }
            .report-container{
                width: 100%;
                display: flex; 
                margin-top: 1%;
                text-align: left;
                justify-content: space-between;
            }
            .left-title{
                color: #bdd158;
                font-size: 2.5vw;
                margin-bottom: 10px;
            }
            .right-title{
                color: #d16458;
                font-size: 2.5vw;
                margin-bottom: 10px;
            }
            .top-perfomance{
                width: 49.5%;
                
            }
            .top-perfomance h1, .low-perfomance h1{
                text-align: center;
            }
            .top-perfomance-table{
                position: relative;
            }
            .top-perfomance-table::after{
                content: "";
                position: absolute;
                width: 2px;
                height: 70%;
                background: #0984bd;
                top: 50%;
                right: -1.2%;
                transform: translate(-50%,-50%);
            }
            .divider{
                /* height: 300px; */
                width: .1%;
                background: #292782;

            }
            .low-perfomance{
                width: 49.5%;
            }
            table {
                width: 100%;
            } 
            table,td,th{
                border: 1px solid #fff;
                border-collapse: collapse;
                text-align: center;
                color: #fff;
                font-size: 2vw;
                line-height: 2.7vw;
            }
            table thead tr th
            {
                color: #ada118;
            }
            .top-perfomance-table tr td:nth-child(3) 
            {
                color: #97e35d;
            }
            .low-perfomance-table tr td:nth-child(3) 
            {
                color: #e3685d;
            }
            .extra-list{
                margin-top:1.5%;
            }
            .ex-line{
                color: #ada118;
            }
        </style>  

        <div class="title-box">
            <h1 class="page-title">Line Ranking</h1>
        </div>  
        <div class="report-container" >
            <div class="top-perfomance">
                <h1 class="left-title">Top Perfoming line</h1>
                <table class="top-perfomance-table" >
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Floor</th>
                            <th>Line</th>
                            <th>Target</th>
                            <th>Actual</th>
                            <th>Ach %</th>
                            <th>Effi %</th>
                            <th>DHU</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                            $i = 0; 
                            foreach ($top7_line as $line => $kpi) 
                            {   
                                $v = $line_perfomance_array[$line];
                                ?>
                                    <tr>
                                        <td><?= ++$i ?></td> 
                                        <td><?= $lib_floor_arr[$v['FLOOR_ID']] ?></td>
                                        <td><?= $v['LINE_NUMBER'] ?></td>
                                        <td title="<?= $v['TARGET_TITLE'] ?>" ><?= round($v['TARGET'],0) ?></td> 
                                        <td><?= $v['PROD_QTY'] ?></td> 
                                        <td title="<?= $v['ACH_PER_TITLE'] ?>" ><?= number_format($v['ACH_PER'],0 ) ?>%</td>  
                                        <td title="<?= $v['EFFI_TITLE'] ?>" ><?= number_format($v['EFFI'],0 ) ?>%</td>  
                                        <td title="KPI= <?= $kpi ?>"><?= $v['DHU'] ?></td>  
                                    </tr>  
                                <? 
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="low-perfomance">
                <h1 class="right-title">Low Perfoming line</h1>
                <table class="low-perfomance-table" >
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Floor</th>
                            <th>Line</th>
                            <th>Target</th>
                            <th>Actual</th>
                            <th>Ach %</th>
                            <th>Effi %</th>
                            <th>DHU</th>
                        </tr>
                    </thead>
                    <tbody>
                        <? 
                            $j = $i+ count($others_line) ;
                            foreach ($last7_line as $line => $kpi) 
                            {   
                                $v = $line_perfomance_array[$line]; 
                                ?>
                                    <tr>
                                        <td><?= ++$j ?></td> 
                                        <td><?= $lib_floor_arr[$v['FLOOR_ID']] ?></td>
                                        <td><?= $v['LINE_NUMBER'] ?></td>
                                        <td title="<?= $v['TARGET_TITLE'] ?>" ><?= round($v['TARGET'],0) ?></td> 
                                        <td><?= $v['PROD_QTY'] ?></td> 
                                        <td title="<?= $v['ACH_PER_TITLE'] ?>" ><?= number_format($v['ACH_PER'],0 ) ?>%</td>  
                                        <td title="<?= $v['EFFI_TITLE'] ?>" ><?= number_format($v['EFFI'],0 ) ?>%</td>  
                                        <td title="KPI= <?= $kpi ?>"><?= $v['DHU'] ?></td>  
                                    </tr>  
                                <? 
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="extra-list">
            <table>
                <tr>
                    <? 
                        foreach ($others_line as $line => $kpi) 
                        {   
                            $v = $line_perfomance_array[$line];
                            ?>
                                <td> <span class="ex-serial"> <?= ++$i  ?>. </span> <span class="ex-line"> <?= $v['LINE_NUMBER'] ?> </span> </td> 
                                    
                            <?
                            if ($i==21) {
                                break;
                            }
                        }
                    ?>
                </tr>
            </table>
        </div>
    <?
}
?>
