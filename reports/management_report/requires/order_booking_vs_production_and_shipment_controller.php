<?php
//--------------------------------------------------------------------------------------------------------------------
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header('location:login.php');
include('../../../includes/common.php'); 
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_level=$_SESSION['logic_erp']["user_level"];
$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
$item_arr=return_library_array( "select id,item_name from lib_garment_item", "id", "item_name"  );
$user_arr=return_library_array( "select id,user_name from user_passwd", "id", "user_name"  ); 
$unit_lib=$unit_of_measurement; 
// pre($unit_lib); die;
function team_arr(){
    $team_sql = "SELECT team_leader_name as t_member, team_name,id FROM LIB_MARKETING_TEAM";
    $team_result =  sql_select($team_sql);   
    $team_arr = [];
    foreach($team_result as $team ){
        $team_arr[$team['ID']]['TEAM_MEMBER'] = $team['T_MEMBER'];
        $team_arr[$team['ID']]['TEAM_NAME'] = $team['TEAM_NAME'];
    }
    return $team_arr;
}
function is_divideable($val){
    return (!is_infinite($val) && !is_null($val) && !is_nan($val) && $val !='' && $val > 0);
}

function pre($array){
    echo "<pre>";
    print_r($array);
    echo "<pre>"; 
}
if ($action=="load_drop_down_buyer")
{  
	echo create_drop_down( "cbo_buyer_name", 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}
if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_buyer_name','0','0','','0');\n";
    exit();
}
if($action=="booking_qty_popup")
{
    echo load_html_head_contents("Popup Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    $width = "1180";
    $team_array =  team_arr();
    // pre($_REQUEST); die;
	// echo $lc_company;die;
    $lc_company = str_replace("'",'',$lc_company);
    $wo_company = str_replace("'",'',$wo_company);
    $buyer = str_replace("'",'',$buyer);

    $con_condition = '';
    if( $lc_company !=0 )
    {
    $con_condition .= "and a.company_name=$lc_company" ;
    }  
    if($wo_company !=0 )
    {
    $con_condition .= "and a.working_company_id in($wo_company)" ;
    }   
    if($buyer !=0)
    {
    $con_condition .= "and a.buyer_name in($buyer)" ;
    }   
    if($month !='')
    {
        $con_condition .= "and to_char(b.pub_shipment_date,'MON-YYYY')='$month'" ;
    }
    if($form_date !='' && $to_date !='')
    {
        $con_condition .= "and b.pub_shipment_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'" ;
    }  
    //   echo $con_condition;  die;
    $sql = "SELECT a.id,a.team_leader,a.company_name,a.buyer_name,a.style_ref_no as style,b.id as po_id,b.po_number,a.job_no,c.gmts_item_id,to_char(b.insert_date,'DD-MM-YYYY') as insert_date, to_char(b.pub_shipment_date,'DD-MM-YYYY') as pub_shipment_date,(b.po_quantity*a.total_set_qnty) as po_quantity,a.order_uom,b.unit_price,b.po_total_price,c.smv_pcs,b.is_confirmed,a.inserted_by FROM wo_po_details_master a,wo_po_break_down b,wo_po_details_mas_set_details c WHERE a.id = b.job_id and a.id = c.job_id and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted =0 and b.is_confirmed in(1,2)  $con_condition ORDER BY b.pub_shipment_date ASC";  
    // echo $sql; die;
    $result =  sql_select($sql);   
    $data = [];
    $job_id_arr = []; 
    foreach($result  as $res)
    {
        $job_id_arr[$res['ID']] = $res['ID'];  
        $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['COMPANY_NAME'] =  $res['COMPANY_NAME'];
        $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['TEAM_ID'] =  $res['TEAM_LEADER'];
            
        $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['BUYER_NAME'] =  $res['BUYER_NAME'];
        $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['STYLE'] =  $res['STYLE'];
        $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['JOB_NO'] =  $res['JOB_NO'];
        $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['INSERT_DATE'] =  $res['INSERT_DATE'];
        $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['PUB_SHIPMENT_DATE'] =  $res['PUB_SHIPMENT_DATE'];
        // $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['SEW_EFFI'] =  $res['SEW_EFFI'];
        $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['PO_QUANTITY'] +=  $res['PO_QUANTITY'];
        $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['ORDER_UOM'] =  $res['ORDER_UOM'];
        $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['UNIT_PRICE'] =  $res['UNIT_PRICE'];
        $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['VALUE'] =  $res['PO_TOTAL_PRICE'];
        $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['SMV_PCS'] =  $res['SMV_PCS'];
        $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['IS_CONFIRMED'] =  $res['IS_CONFIRMED'];
        // $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['COSTING_PER'] =  $res['COSTING_PER'];
        $data[$res['ID']][$res['PO_NUMBER']][$res['GMTS_ITEM_ID']]['INSERTED_BY'] =  $res['INSERTED_BY'];
        
    }
    // pre($job_id_arr);die; 
    $con = connect(); 
    fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 91111, 1,$job_id_arr, $empty_arr); 
    oci_commit($con);  
    $cm_sql =  "SELECT a.job_id,a.cm_cost FROM WO_PRE_COST_DTLS a, GBL_TEMP_ENGINE b WHERE a.job_id = b.ref_val and  b.user_id=$user_id and b.entry_form=91111 and  a.status_active=1 and a.is_deleted =0 ";

    $pre_cost_sql =  "SELECT a.job_id,a.sew_effi_percent as sew_effi,a.costing_per FROM WO_PRE_COST_MST a, GBL_TEMP_ENGINE b WHERE a.job_id = b.ref_val and  b.user_id=$user_id and b.entry_form=91111 and  a.status_active=1 and a.is_deleted =0 ";

    $cm_cost_res = sql_select($cm_sql); 
    $pre_cost_res = sql_select($pre_cost_sql);  
    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form = 91111");
    oci_commit($con);  
    // oci_commit($con); 
    // echo $sql; die; 
    $cm_cost_arr=[];
    foreach($cm_cost_res as   $v)
    {
        $cm_cost_arr[$v['JOB_ID']] = $v['CM_COST'] ;
    }
    $pre_cost_arr=[];
    foreach($pre_cost_res as   $v)
    {
        $pre_cost_arr[$v['JOB_ID']] ['SEW_EFFI'] = $v['SEW_EFFI'];
        $pre_cost_arr[$v['JOB_ID']] ['COSTING_PER'] = $v['COSTING_PER'];
    }
	?>
	 <style>
        .tableFixHead          { height: 100vh; }
        .tableFixHead thead th { position: sticky;   top: 0; z-index: 99; }
     </style>
    </head>
    <body>
    <div align="center" style="width:<?= $width ?>px;">
        <form name="styleRef_form" id="styleRef_form">  
            <fieldset style="width:<?= $width ?>px;"  class="tableFixHead"> 
                <table cellspacing="0" width="2000" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                    <thead>
                        <tr >
                            <th  width='20'>SL</th>
                            <th  width='100'>LC Company</th>
                            <th  width='80'>Team Name</th>
                            <th  width='80'>Team Member</th>
                            <th  width='80'>Buyer</th>
                            <th  width='100'>Style</th>
                            <th  width='40'>Order No</th>
                            <th  width='80'>Job No</th>
                            <th  width='80'>Item</th>
                            <th  width='60'>Insert Date</th>
                            <th  width='60'>Ship Date</th>
                            <th  width='20'>Sewing Effi%</th>
                            <th  width='50'>Order Qty (Pcs)</th>
                            <th  width='40'>Unit Price</th>
                            <th  width='40'>UOM</th>
                            <th  width='60'>Value</th>
                            <th  width='40'>SMV</th>
                            <th  width='60'>Minute</th>
                            <th  width='60'>CM/Pcs</th>
                            <th  width='60'>CM Value</th>
                            <th  width='50'>Order Status</th>
                            <th  width='80'>Insert By</th>
                        </tr>
                        <?php
                        $i = 0;
                        $total_order_qty = 0;
                        $total_minute = 0;
                        $total_cm_value = 0;
                        $total_value = 0;
                        if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
                        foreach($data as $job_id=>$d)
                        { 
                            foreach($d as $po_num=>$e)
                            {  
                                foreach($e as $item=>$v)
                                {  
                                    // ['SEW_EFFI']['COSTING_PER']
                                    $order_qty  = $v['PO_QUANTITY'];
                                    $total_order_qty += $order_qty;
                                    $smv = $v['SMV_PCS']; 
                                    $sew_effi = number_format( $pre_cost_arr[$job_id]['SEW_EFFI'],2);
                                    $cost_per =  $pre_cost_arr[$job_id]['COSTING_PER'] ;
                                    $cm_cost = $cm_cost_arr[$job_id] ;
                                    $cm_cal = $cm_cost / $cost_per;
                                    $cm  = is_nan($cm_cal) ? 0  : $cm_cal ;
                                    $cm_value_cal = $order_qty * $cm ;
                                    $cm_value = is_nan($cm_value_cal) ? 0 : $cm_value_cal;
                                    $minutes = $smv * $order_qty;
                                    $total_minute +=  $minutes;
                                    $total_cm_value += $cm_value ;
                                    $total_value +=  $v['VALUE'];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">

                                        <td width='20'> <?= ++$i ?> </td>
                                        <td width='100'> <?=$company_arr[$v['COMPANY_NAME']] ?> </td>
                                        <td width='80'> <?= $team_array[$v['TEAM_ID']]['TEAM_NAME']  ?> </td>
                                        <td width='80'> <?= $team_array[$v['TEAM_ID']]['TEAM_MEMBER'] ?> </td>
                                        <td width='80'> <?= $buyer_arr[$v['BUYER_NAME']] ?> </td>
                                        <td width='120'> <?= $v['STYLE'] ?> </td>
                                        <td width='40'> <?= $po_num  ?> </td>
                                        <td width='80'> <?= $v['JOB_NO'] ?> </td>
                                        <td width='80'> <?= $item_arr[$item] ?> </td>
                                        <td width='60' align="right"> <?= $v['INSERT_DATE'] ?> </td>
                                        <td width='60' align="right"> <?= $v['PUB_SHIPMENT_DATE'] ?> </td>
                                        <td width='20' align="right"> <?= number_format($sew_effi,2) ?> </td>
                                        <td width='50' align="right"> <?= $order_qty ?> </td>
                                        <td width='40' align="right"> <?= number_format( $v['UNIT_PRICE'] ,2 ) ?> </td>
                                        <td width='40' align="center"> <?= $unit_lib[$v['ORDER_UOM']] ?> </td>
                                        <td width='60' align="right"> <?= number_format( $v['VALUE'],2) ?> </td>
                                        <td width='40' align="right"> <?= $smv ?> </td>
                                        <td width='60' align="right"> <?= number_format($minutes,2) ?> </td>
                                        <td width='60' align="right"> <?= number_format($cm,2)?> </td>
                                        <td width='60' align="right"> <?= number_format($cm_value,2) ?> </td>
                                        <td width='50'> <?= $v['IS_CONFIRMED'] == 1 ? 'Confirmed': 'Projection' ?> </td>
                                        <td width='80'> <?= $user_arr[$v['INSERTED_BY']]  ?> </td> 
                                    </tr>
                                    <?php
                                      $i++;
                                }
                            }
                        }
                        ?>
                        <tr style="background: #dccdcd;">
                            <td colspan="12" align="left" >Grand Total </td>
                            <td align="right"> <b> <?= $total_order_qty ?> </b> </td>
                            <td colspan="2"></td>
                            <td align="right"> <?= number_format( $total_value,2) ?></td>
                            <td></td>
                            <td align="right">  <b> <?= number_format($total_minute,2) ?> </b> </td>
                            <td></td>
                            <td align="right">  <b><?= number_format($total_cm_value,2) ?> </b></td>
                            <td colspan="2"></td>
                        </tr>
                    </thead>
                </table> 
            </fieldset>
	    </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
} 
if($action=="prod_qty_popup")
{
    echo load_html_head_contents("Popup Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    $width = "1180";
    $team_array =  team_arr();
    // pre($team_array); die;
	// echo $lc_company;die;
    $lc_company = str_replace("'",'',$lc_company);
    $wo_company = str_replace("'",'',$wo_company);
    $buyer = str_replace("'",'',$buyer);

    $con_condition = '';
    if( $lc_company !=0 )
    {
    $con_condition .= "and a.company_name=$lc_company" ;
    }  
    if($wo_company !=0 )
    {
    $con_condition .= "and a.working_company_id in($wo_company)" ;
    }   
    if($buyer !=0)
    {
    $con_condition .= "and a.buyer_name in($buyer)" ;
    }      
    if($month !='')
    {
        $con_condition .= "and to_char(b.pub_shipment_date,'MON-YYYY')='$month'" ;
    }
    if($form_date !='' && $to_date !='')
    {
        $con_condition .= "and b.pub_shipment_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'" ;
    }    
    $sql = "SELECT a.id,d.production_date,d.company_id,a.buyer_name,a.style_ref_no as style,(b.po_quantity*a.total_set_qnty) as po_quantity,a.job_no,c.gmts_item_id,to_char(b.pub_shipment_date,'DD-MM-YYYY') as pub_shipment_date,d.production_quantity,a.avg_unit_price as unit_price,c.smv_pcs,b.id as po_id,d.sewing_line as line_no,b.po_number FROM wo_po_details_master a,wo_po_break_down b,wo_po_details_mas_set_details c,pro_garments_production_mst d WHERE a.id = b.job_id and a.id = c.job_id and d.PO_BREAK_DOWN_ID = b.id and d.production_type= 5 and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted =0 and b.is_confirmed in(1,2) and d.status_active=1 and d.is_deleted =0 $con_condition ORDER BY pub_shipment_date ASC"; 

    $result =  sql_select($sql);   
    $data = [];
    $job_id_arr = []; 
    $sewing_line_arr = []; 
    foreach($result  as $res)
    {
        $job_id_arr[$res['ID']] = $res['ID'];  
        $sewing_line_arr[$res['LINE_NO']] = $res['LINE_NO'];  
        $data[$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['PRODUCTION_DATE'] =  $res['PRODUCTION_DATE'];
        $data[$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['LINE_NO'] =  $res['LINE_NO'];
        $data[$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['COMPANY_ID'] =  $res['COMPANY_ID'];
        $data[$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['BUYER_NAME'] =  $res['BUYER_NAME'];
        $data[$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['PO_NUMBER'] =  $res['PO_NUMBER'];
        $data[$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['STYLE'] =  $res['STYLE'];
        $data[$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['JOB_NO'] =  $res['JOB_NO']; 
        $data[$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['PUB_SHIPMENT_DATE'] =  $res['PUB_SHIPMENT_DATE'];
        $data[$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['PRODUCTION_QUANTITY'] +=  $res['PRODUCTION_QUANTITY'];  
        $data[$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['UNIT_PRICE'] =  $res['UNIT_PRICE'];
        $data[$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['SMV_PCS'] =  $res['SMV_PCS'];
        $data[$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['MINUTE'] +=  $res['PRODUCTION_QUANTITY'] * $res['SMV_PCS'];
        $data[$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['VALUE'] +=   $res['PRODUCTION_QUANTITY'] * $res['UNIT_PRICE'];  
        
    }
     
    $con = connect(); 
    fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 900, 1,$job_id_arr, $empty_arr);  
    oci_commit($con);   
    // // die;
    $cm_sql =  "SELECT a.job_id,a.cm_cost FROM WO_PRE_COST_DTLS a, GBL_TEMP_ENGINE b WHERE a.job_id = b.ref_val and  b.user_id=$user_id and b.entry_form=900 and  a.status_active=1 and a.is_deleted =0 "; 
    $pre_cost_sql =  "SELECT a.job_id,a.sew_effi_percent as sew_effi,a.costing_per FROM WO_PRE_COST_MST a, GBL_TEMP_ENGINE b WHERE a.job_id = b.ref_val and  b.user_id=$user_id and b.entry_form=900 and  a.status_active=1 and a.is_deleted =0 ";
    $cm_cost_res = sql_select($cm_sql);   
    $pre_cost_res = sql_select($pre_cost_sql); 
    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form = 900");
    oci_commit($con); 
     
    $pro_con = where_con_using_array($sewing_line_arr,1,'b.line_number');
    $prod_dtls_sql =  "SELECT a.man_power,a.working_hour,b.line_number FROM prod_resource_dtls a,prod_resource_mst b WHERE a.mst_id = b.id and a.is_deleted =0 and b.is_deleted =0 $pro_con ";
    $prod_dtls_res = sql_select($prod_dtls_sql);   
    // echo $prod_dtls_sql; die;
    $cm_cost_arr=[];
    foreach($cm_cost_res as   $v)
    {
        $cm_cost_arr[$v['JOB_ID']] = $v['CM_COST'];
    } 
    $prod_dtls_arr=[];
    foreach($prod_dtls_res as   $v)
    {
        $prod_dtls_arr[$v['LINE_NUMBER']]['MAN_POWER'] = $v['MAN_POWER'];
        $prod_dtls_arr[$v['LINE_NUMBER']]['WORKING_HOUR'] = $v['WORKING_HOUR'];
    } 
    $pre_cost_arr=[];
    foreach($pre_cost_res as   $v)
    {
        $pre_cost_arr[$v['JOB_ID']] = $v['COSTING_PER'];
    }
    // pre($prod_dtls_res);die;
    ?>
    <style>
        .tableFixHead          { height: 100vh; }
        .tableFixHead thead th { position: sticky;   top: 0; z-index: 99; }
     </style>
    </head>
    <body>
    <div align="center" style="width:<?= $width ?>px;">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset style="width:<?= $width ?>px;" class="tableFixHead">
                <table cellspacing="0" width="1320" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                    <thead>
                        <tr >
                            <th  width='30'>SL</th>
                            <th  width='60'>Production Date</th>
                            <th  width='120'>LC Company</th>
                            <th  width='80'>Buyer</th>
                            <th  width='100'>Style</th>
                            <th  width='80'>Order No</th>
                            <th  width='80'>Job No</th>
                            <th  width='80'>Item</th>
                            <th  width='60'>Ship Date</th>
                            <th  width='50'>Production Qty</th>
                            <th  width='50'>SMV</th>
                            <th  width='60'>Minute</th>
                            <th  width='60'>CM/Pcs</th>
                            <th  width='60'>CM Value</th>
                            <th  width='50'>Unit Price</th>
                            <th  width='60'>Value</th>
                            <th  width='50'>Working HR</th>
                            <th  width='50'>Manpower</th>
                            <th  width='50'>Avl Minit</th>
                            <th  width='50'>Sewing Effi%</th> 
                        </tr>
                        <?php
                        $i = 0;
                        $total_prod_qty = 0;
                        $total_minute = 0;
                        $total_cm_value = 0;
                        $total_value = 0;
                        $total_avl_minit = 0;
                        if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
                        foreach($data as $job_id=>$d)
                        { 
                            foreach($d as $po_num=>$e)
                            {  
                                foreach($e as $item=>$v)
                                {    
                                    $cost_per =  $pre_cost_arr[$job_id]; 
                                    $cm_cost = $cm_cost_arr[$job_id];
                                    $cm_cal =  $cm_cost / $cost_per;
                                    $cm  = is_nan($cm_cal) ?0 : $cm_cal;
                                    $cm_value_cal =  $cm * $v['PRODUCTION_QUANTITY'];
                                    $cm_value = is_nan($cm_value_cal) ?0 : $cm_value_cal;
                                    $man_power =  $prod_dtls_arr[$v['LINE_NO']]['MAN_POWER'];
                                    $working_hour =  $prod_dtls_arr[$v['LINE_NO']]['WORKING_HOUR'];
                                    $minute = $v['MINUTE'];
                                    $avil_minute =  $working_hour * $man_power;
                                    $sew_effi = number_format( ($avil_minute / $minute) * 100,2 );

                                    $total_prod_qty += $v['PRODUCTION_QUANTITY'];
                                    $total_value += $v['VALUE'];
                                    $total_minute += $minute;
                                    $total_cm_value += $cm_value;
                                    $total_avl_minit += $avil_minute;
                                    // echo $cm_value;
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                        
                                        <td width='30'> <?= ++$i ?> </td>
                                        <td width='60' align="right"> <?= $v['PRODUCTION_DATE'] ?> </td>
                                        <td width='120'> <?=$company_arr[$v['COMPANY_ID']] ?> </td> 
                                        <td width='80'> <?= $buyer_arr[$v['BUYER_NAME']] ?> </td>
                                        <td width='100'> <?= $v['STYLE'] ?> </td>
                                        <td width='80'> <?= $v['PO_NUMBER']  ?> </td>
                                        <td width='80'> <?= $v['JOB_NO'] ?> </td>
                                        <td width='80'> <?= $item_arr[$item] ?> </td>
                                        <td width='60' align="right"> <?= $v['PUB_SHIPMENT_DATE'] ?> </td>
                                        <td width='50' align="right"> <?= $v['PRODUCTION_QUANTITY'] ?> </td>
                                        <td width='50' align="right"> <?= number_format($v['SMV_PCS'],2) ?></td>
                                        <td width='60' align="right"> <?= number_format($minute,2)  ?></td>
                                        <td width='60' align="right"> <?= number_format($cm,2)    ?> </td>
                                        <td width='60' align="right"> <?= number_format($cm_value,2)  ?> </td>
                                        <td width='50' align="right"> <?= $v['UNIT_PRICE'] ?> </td>
                                        <td width='60' align="right"> <?= number_format( $v['VALUE'],2) ?> </td>
                                        <td width='50' align="right"> <?= $working_hour ?> </td>
                                        <td width='50' align="right"> <?= $man_power ?> </td>
                                        <td width='50' align="right"> <?= $avil_minute ?> </td>
                                        <td width='50' align="right"> <?= $sew_effi ?> </td> 
                                    </tr>
                                    <?php
                                    $i++;
                                }
                            }
                        }
                        ?>
                        <tr style="background: #dccdcd; font-weight:bold; ">
                            <td colspan="9" align="left">Grand Total </td>
                            <td align="right"><?= $total_prod_qty ?></td>
                            <td></td>
                            <td align="right"><?= number_format($total_minute,2) ?></td>
                            <td></td>
                            <td align="right"><?= number_format($total_cm_value,2)  ?></td>
                            <td></td>
                            <td align="right"> <?= number_format( $total_value,2) ?></td>
                            <td colspan="2"></td>
                            <td align="right"> <?= number_format( $total_avl_minit,2) ?></td>
                            <td align="right"> <?= number_format($total_avl_minit/$total_minute,2) ?></td>
                        </tr>
                    
                </table> 
            </fieldset>
        </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit(); 
}
if($action=="prod_balance_popup")
{
    echo load_html_head_contents("Popup Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    $width = "1180";
    $lc_company = str_replace("'",'',$lc_company);
    $wo_company = str_replace("'",'',$wo_company);
    $buyer = str_replace("'",'',$buyer);
  

    $con_condition = '';
    if( $lc_company !=0 )
    {
    $con_condition .= "and a.company_name=$lc_company" ;
    }  
    if($wo_company !=0 )
    {
    $con_condition .= "and a.working_company_id in($wo_company)" ;
    }   
    if($buyer !=0)
    {
    $con_condition .= "and a.buyer_name in($buyer)" ;
    }      
    if($month !='')
    {
        $con_condition .= "and to_char(b.pub_shipment_date,'MON-YYYY')='$month'" ;
    }
    if($form_date !='' && $to_date !='')
    {
        $con_condition .= "and b.pub_shipment_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'" ;
    }    
    $sql = "SELECT a.id,a.buyer_name,a.company_name,a.style_ref_no as style,b.id as po_id,b.po_number,a.job_no,to_char(b.pub_shipment_date,'DD-MM-YYYY') as pub_shipment_date,to_char(b.insert_date,'YYYY') as job_year,(b.po_quantity*a.total_set_qnty) as po_quantity,a.order_uom FROM wo_po_details_master a,wo_po_break_down b,wo_po_details_mas_set_details c WHERE a.id = b.job_id  and a.id = c.job_id  and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted =0 and b.is_confirmed in(1,2)  $con_condition ORDER BY b.pub_shipment_date ASC";      
    // echo $sql;die;
    $result =  sql_select($sql);   
    $data_array = [];
    $job_id_arr = []; 
    $sewing_line_arr = []; 
    $po_id_arr = array();
    foreach($result  as $res)
    {
        $po_id_arr[$res['PO_ID']] = $res['PO_ID'];    
        $data_array[$res['ID']][$res['PO_ID']]['COMPANY_ID'] =  $res['COMPANY_NAME'];
        $data_array[$res['ID']][$res['PO_ID']]['BUYER_NAME'] =  $res['BUYER_NAME'];
        $data_array[$res['ID']][$res['PO_ID']]['STYLE'] =  $res['STYLE'];
        $data_array[$res['ID']][$res['PO_ID']]['JOB_NO'] =  $res['JOB_NO']; 
        $data_array[$res['ID']][$res['PO_ID']]['YEAR'] =  $res['JOB_YEAR']; 
        $data_array[$res['ID']][$res['PO_ID']]['ORDER_UOM'] =  $res['ORDER_UOM'];
        $data_array[$res['ID']][$res['PO_ID']]['SHIP_DATE'] =  $res['PUB_SHIPMENT_DATE'];
        $data_array[$res['ID']][$res['PO_ID']]['po_number'] =  $res['PO_NUMBER'];
        
        $data_array[$res['ID']][$res['PO_ID']]['PO_QUANTITY'] += $res['PO_QUANTITY'];

        
    }
    $po_id_cond = where_con_using_array($po_id_arr,0,"po_break_down_id");
    $sql = "SELECT c.po_break_down_id as po_id,c.production_type,c.production_quantity FROM pro_garments_production_mst c  WHERE c.status_Active=1 and c.production_type in (1,4,5)   $po_id_cond "; 
    // echo $sql;
    // print_r($po_id_arr);die;
    $result = sql_select($sql);
    foreach($result  as $res)
    {
        $prod_data_array[$res['PO_ID']][$res['PRODUCTION_TYPE']] +=  $res['PRODUCTION_QUANTITY']; 
       
    }
    // echo $sql; die;
    // pre($data_array);die;
    ?>
    <style>
        .tableFixHead          { height: 100vh; }
        .tableFixHead thead th { position: sticky;   top: 0; z-index: 99; }
     </style>
    </head>
    <body>
    <div align="center" style="width:<?= $width ?>px;">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset style="width:<?= $width ?>px;"  class="tableFixHead">
                <table cellspacing="0" width="1320" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                    <thead>
                        <tr >
                            <th  width='30'>SL</th> 
                            <th  width='130'>Company</th>
                            <th  width='90'>Buyer</th>
                            <th  width='110'>Style</th>
                            <th  width='80'>Job No</th>
                            <th  width='30'>Year</th>
                            <th  width='80'>Order No</th>
                            <th  width='80'>Order Qty</th>
                            <th  width='40'>UOM</th> 
                            <th  width='70'>Ship Date</th>
                            <th  width='80'>Cutting Qty</th>
                            <th  width='80'>Cutting Balance Qty</th>
                            <th  width='80'>Sewing Input Qty</th>
                            <th  width='80'>Sewing Input Balance</th>
                            <th  width='80'>Sewing Output Qty</th>
                            <th  width='80'>Sewing WIP</th>
                            <th  width='80'>Production Balance</th> 
                        </tr>
                    </thead> 
                    <tbody>
                        <?php
                        $i = 0;
                        $total_po_qty = 0;
                        $total_cut_qty = 0;
                        $total_swe_in = 0;
                        $total_swe_out = 0; 
                        if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
                        foreach($data_array as $job_id=>$job_data)
                        {  
                            foreach($job_data as $po_id=>$v)
                            {     
                                $po_qty = $v['PO_QUANTITY'];
                                $cut_qty = $prod_data_array[$po_id][1];
                                $sew_input = $prod_data_array[$po_id][4];
                                $sew_out = $prod_data_array[$po_id][5];
                                $cut_balance = $po_qty - $cut_qty ;
                                $sew_input_blnc = $cut_qty - $sew_input;
                                $sew_out_blnc = $sew_input - $sew_out;
                                $prod_blnc = $po_qty - $sew_out;

                                $total_po_qty +=   $po_qty;
                                $total_cut_qty += $cut_qty;
                                $total_swe_in += $sew_input;
                                $total_swe_out += $sew_out; 
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    
                                    <td width='30'> <?= ++$i ?> </td>
                                    <td width='130'> <?=$company_arr[$v['COMPANY_ID']] ?> </td>  
                                    <td width='90'> <?= $buyer_arr[$v['BUYER_NAME']] ?> </td>
                                    <td width='110'> <?= $v['STYLE'] ?> </td>
                                    <td width='80'> <?= $v['JOB_NO'] ?> </td>
                                    <td width='30'> <?= $v['YEAR'] ?> </td>
                                    <td width='80'> <?= $v['po_number']  ?> </td>
                                    <td width='80' align="right"> <?= $po_qty  ?> </td>
                                    <td width='40' align="center"> <?= $unit_lib[$v['ORDER_UOM']] ?> </td>
                                    <td width='70' align="right"> <?= $v['SHIP_DATE'] ?> </td> 
                                    <td width='80' align="right"> <?= $cut_qty ?> </td>
                                    <td width='80' align="right"> <?= $cut_balance  ?> </td>
                                    <td width='80' align="right"> <?= $sew_input  ?></td>
                                    <td width='80' align="right"> <?= $sew_input_blnc ?></td>
                                    <td width='80' align="right"> <?= $sew_out   ?> </td>
                                    <td width='80' align="right"> <?= $sew_out_blnc ?> </td>
                                    <td width='80' align="right"> <?= $prod_blnc ?> </td>
                                </tr>
                                <?php
                                $i++;
                            } 
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr style="background: #dccdcd; font-weight:bold; ">
                            <th colspan="7" align="left">Grand Total </th>
                            <th align="right"><?= $total_po_qty ?></th>
                            <th colspan="2"></th>
                            <th align="right"><?= $total_cut_qty ?></th> 
                            <th align="right"><?= $total_po_qty  - $total_cut_qty  ?></th>  
                            <th align="right"><?= $total_swe_in ?></th> 
                            <th align="right"><?= $total_cut_qty  - $total_swe_in  ?></th>  
                            <th align="right"><?= $total_swe_out ?></th>  
                            <th align="right"><?= $total_swe_in  - $total_swe_out  ?></th>  
                            <th align="right"><?= $total_po_qty  - $total_swe_out  ?></th>  
                        </tr>
                    </tfoot>
                </table> 
            </fieldset>
        </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit(); 
}
if($action=="shipment_qty_popup")
{
    echo load_html_head_contents("Popup Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    $width = "1180";
    $lc_company = str_replace("'",'',$lc_company);
    $wo_company = str_replace("'",'',$wo_company);
    $buyer = str_replace("'",'',$buyer);

    $con_condition = '';
    if( $lc_company !=0 )
    {
    $con_condition .= "and a.company_name=$lc_company" ;
    }  
    if($wo_company !=0 )
    {
    $con_condition .= "and a.working_company_id in($wo_company)" ;
    }   
    if($buyer !=0)
    {
    $con_condition .= "and a.buyer_name in($buyer)" ;
    }     
    if($month !='')
    {
        $con_condition .= "and to_char(b.pub_shipment_date,'MON-YYYY')='$month'" ;
    }
    if($form_date !='' && $to_date !='')
    {
        $con_condition .= "and b.pub_shipment_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'" ;
    }    
    $sql = "SELECT a.id,a.company_name,a.buyer_name,a.style_ref_no,b.po_number,a.job_no,c.gmts_item_id,to_char(b.pub_shipment_date,'DD/MM/YYYY')as ship_date,c.smv_pcs,b.unit_price,d.ex_factory_qnty,to_char(d.ex_factory_date,'DD/MM/YYYY')as ex_factory_date  FROM wo_po_details_master a,wo_po_break_down b,wo_po_details_mas_set_details c,pro_ex_factory_mst d WHERE a.id = b.job_id and a.id = c.job_id and b.id = d.po_break_down_id and  a.status_active=1 and b.status_active=1 and d.status_active=1 and b.is_confirmed in(1,2) and a.is_deleted =0 and b.is_deleted =0 and d.is_deleted =0 $con_condition ORDER BY d.ex_factory_date ASC";     
    // echo $sql;
    $result =  sql_select($sql);   
    $data = [];
    $job_id_arr = [];  
    foreach($result  as $res)
    {
        $job_id_arr[$res['ID']] = $res['ID'];    
        $data[$res['ID']][$res['PO_NUMBER']]['COMPANY_NAME'] =  $res['COMPANY_NAME'];
        $data[$res['ID']][$res['PO_NUMBER']]['BUYER_NAME'] =  $res['BUYER_NAME'];
        $data[$res['ID']][$res['PO_NUMBER']]['STYLE'] =  $res['STYLE_REF_NO'];
        $data[$res['ID']][$res['PO_NUMBER']]['PO_ID'] =  $res['PO_ID'];
        $data[$res['ID']][$res['PO_NUMBER']]['JOB_NO'] =  $res['JOB_NO']; 
        $data[$res['ID']][$res['PO_NUMBER']]['ITEM'] =  $res['GMTS_ITEM_ID']; 
        $data[$res['ID']][$res['PO_NUMBER']]['SHIP_DATE'] =  $res['SHIP_DATE'];
        $data[$res['ID']][$res['PO_NUMBER']]['SMV_PCS'] =   $res['SMV_PCS'];  
        $data[$res['ID']][$res['PO_NUMBER']]['UNIT_PRICE'] =   $res['UNIT_PRICE'];  
        $data[$res['ID']][$res['PO_NUMBER']]['EX_FACT_QTY'] +=   $res['EX_FACTORY_QNTY'];  
        $data[$res['ID']][$res['PO_NUMBER']]['EX_FACT_DATE'] =   $res['EX_FACTORY_DATE'];  
    }


    $con = connect(); 
    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form =899");
    oci_commit($con); 

    fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 899, 1,$job_id_arr, $empty_arr); 
    oci_commit($con);  
    //for ex_factory_qnty/Shipment Qty 
    $cm_sql =  "SELECT a.job_id,a.cm_cost FROM WO_PRE_COST_DTLS a, GBL_TEMP_ENGINE b WHERE a.job_id = b.ref_val and  b.user_id=$user_id and b.entry_form=899 and  a.status_active=1 and a.is_deleted =0 ";
    $pre_cost_sql =  "SELECT a.job_id,a.sew_effi_percent as sew_effi,a.costing_per FROM WO_PRE_COST_MST a, GBL_TEMP_ENGINE b WHERE a.job_id = b.ref_val and  b.user_id=$user_id and b.entry_form=899 and  a.status_active=1 and a.is_deleted =0 ";
    $cm_cost_res = sql_select($cm_sql); 
    $pre_cost_res = sql_select($pre_cost_sql);
    //    echo $ex_fact_res;die;
    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form = 899");
    oci_commit($con); 
    // echo $sql; die;
    // pre($data);die;
    $cm_cost_arr=[];
    foreach($cm_cost_res as   $v)
    {
        $cm_cost_arr[$v['JOB_ID']] = $v['CM_COST'];
    } 
    $pre_cost_arr=[];
    foreach($pre_cost_res as   $v)
    {
        $pre_cost_arr[$v['JOB_ID']] = $v['COSTING_PER'];
    }
    // pre( $po_id_arr); die;   
    ?>
     <style>
        .tableFixHead          { height: 100vh; }
        .tableFixHead thead th { position: sticky;   top: 0; z-index: 99; }
     </style>
    </head>
    <body>
    <div align="center" style="width:<?= $width ?>px;">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset style="width:<?= $width ?>px;" class="tableFixHead">
                <table cellspacing="0" width="1320" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                    <thead>
                        <tr >
                            <th width='30'>SL</th> 
                            <th width='60'>Ex-Factory Date</th>
                            <th width='100'>LC Company</th>
                            <th width='80'>Buyer</th>
                            <th width='120'>Style</th>
                            <th width='80'>Order No</th>
                            <th width='60'>Job No</th>
                            <th width='70'>Item</th>
                            <th width='60'>Ship Date</th> 
                            <th width='60'>Ex-Factory Qty</th>
                            <th width='40'>SMV</th>
                            <th width='60'>Minute</th>
                            <th width='40'>CM</th>
                            <th width='80'>CM Value</th>
                            <th width='40'>Unit Price</th>
                            <th width='80'>Value</th> 
                        </tr>
                        <?php
                        $i = 0;
                        $total_ex_fact_qty = 0;
                        $total_cm_value = 0;
                        $total_minutes ='';
                        $total_value = 0; 
                        if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
                        foreach($data as $job_id=>$d)
                        {  
                            foreach($d as $po_num=>$v)
                            {  
                                $ex_fact_qty = $v['EX_FACT_QTY'] ; 
                                
                                $smv = $v['SMV_PCS'];
                                $unit_price = $v['UNIT_PRICE'];
                                $minutes = ($ex_fact_qty *  $smv);
                                $cost_per = $pre_cost_arr[$job_id];
                                $cm_cal =  $cm_cost_arr[$job_id] / $cost_per ;
                                $cm = is_nan($cm_cal) ? 0 : $cm_cal;
                                $cm_value_cal =  $ex_fact_qty *  $cm;
                                $cm_value = is_nan($cm_value_cal) ? 0 : $cm_value_cal;
                                $value = $ex_fact_qty * $unit_price;

                                $total_ex_fact_qty += $ex_fact_qty;
                                $total_cm_value += $cm_value;
                                $total_minutes += $minutes;
                                $total_value += $value;
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    
                                    <td width='30'> <?= ++$i ?> </td>
                                    <td width='60'> <?= $v['EX_FACT_DATE'] ?> </td>
                                    <td width='100'> <?=$company_arr[$v['COMPANY_NAME']] ?> </td>  
                                    <td width='80'> <?= $buyer_arr[$v['BUYER_NAME']] ?> </td>
                                    <td width='120'> <?= $v['STYLE'] ?> </td>
                                    <td width='80'> <?= $po_num ?> </td>
                                    <td width='60'> <?= $v['JOB_NO'] ?> </td>
                                    <td width='70'> <?= $item_arr[$v['ITEM']] ?> </td>
                                    <td width='60' align="right"> <?= $v['SHIP_DATE'] ?> </td> 
                                    <td width='60' align="right"> <?= $ex_fact_qty ?> </td>
                                    <td width='40' align="right"> <?= $smv ?> </td>
                                    <td width='60' align="right"> <?= $minutes ?> </td>
                                    <td width='40' align="right"> <?= number_format($cm,2)  ?> </td>
                                    <td width='80' align="right"> <?= number_format($cm_value,2) ?> </td>
                                    <td width='40' align="right"> <?= number_format($unit_price,2)  ?> </td>
                                    <td width='80' align="right"> <?= number_format($value,2)  ?></td>
                                </tr>
                                <?php
                                $i++;
                            } 
                        }
                        ?>
                        <tr style="background: #dccdcd; font-weight:bold; ">
                            <td colspan="9" align="left">Grand Total </td>
                            <td align="right"><?= $total_ex_fact_qty ?></td>
                            <td> </td> 
                            <td align="right"><?= $total_minutes ?></td>  
                            <td> </td> 
                            <td align="right"><?= number_format($total_cm_value,2) ?></td> 
                            <td> </td> 
                            <td align="right"><?= number_format($total_value,2) ?></td>   
                        </tr>
                    </thead>
                </table> 
            </fieldset>
        </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit(); 
}
if($action=="shipment_balance_popup")
{
    echo load_html_head_contents("Popup Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    $width = "1180";
    $lc_company = str_replace("'",'',$lc_company);
    $wo_company = str_replace("'",'',$wo_company);
    $buyer = str_replace("'",'',$buyer);
    // echo $buyer; die;
    $con_condition = '';
    if( $lc_company !=0 )
    {
    $con_condition .= "and a.company_name=$lc_company" ;
    }  
    if($wo_company !=0 )
    {
    $con_condition .= "and a.working_company_id in($wo_company)" ;
    }   
    if($buyer !=0)
    {
    $con_condition .= "and a.buyer_name in($buyer)" ;
    }     
    if($month !='')
    {
        $con_condition .= "and to_char(b.pub_shipment_date,'MON-YYYY')='$month'" ;
    }
    if($form_date !='' && $to_date !='')
    {
        $con_condition .= "and b.pub_shipment_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'" ;
    } 
    
    $sql = "SELECT a.id,a.buyer_name,a.company_name,a.style_ref_no as style,b.id as po_id,b.po_number,a.job_no,to_char(b.pub_shipment_date,'DD-MM-YYYY') as pub_shipment_date,to_char(b.insert_date,'YYYY') as job_year,(b.po_quantity*a.total_set_qnty) as po_quantity,a.order_uom FROM wo_po_details_master a,wo_po_break_down b,wo_po_details_mas_set_details c WHERE a.id = b.job_id  and a.id = c.job_id  and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted =0 and b.is_confirmed in(1,2)  $con_condition ORDER BY b.pub_shipment_date ASC";   
         
    // echo $sql ; die;
    $result =  sql_select($sql);   
    $data_array = [];
    $po_id_arr = [];  
    foreach($result  as $res)
    {
        $po_id_arr[$res['PO_ID']] = $res['PO_ID'];   
        $data_array[$res['ID']][$res['PO_ID']]['COMPANY_ID'] =  $res['COMPANY_NAME'];
        $data_array[$res['ID']][$res['PO_ID']]['BUYER_NAME'] =  $res['BUYER_NAME'];
        $data_array[$res['ID']][$res['PO_ID']]['STYLE'] =  $res['STYLE'];
        $data_array[$res['ID']][$res['PO_ID']]['JOB_NO'] =  $res['JOB_NO']; 
        $data_array[$res['ID']][$res['PO_ID']]['YEAR'] =  $res['JOB_YEAR']; 
        $data_array[$res['ID']][$res['PO_ID']]['ORDER_UOM'] =  $res['ORDER_UOM'];
        $data_array[$res['ID']][$res['PO_ID']]['SHIP_DATE'] =  $res['PUB_SHIPMENT_DATE'];
        $data_array[$res['ID']][$res['PO_ID']]['PO_NUMBER'] =  $res['PO_NUMBER'];
        
        $data_array[$res['ID']][$res['PO_ID']]['PO_QUANTITY'] += $res['PO_QUANTITY'];       
    }
    // echo $sql; die;
    // pre($data);die;
    $con = connect(); 
    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form =999 ");
    oci_commit($con); 

    fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 999, 1,$po_id_arr, $empty_arr); 
    oci_commit($con);   
    
    //for ex_factory_qnty/Shipment Qty 
    $ex_fact =  "SELECT a.ex_factory_qnty, a.po_break_down_id as po_id FROM pro_ex_factory_mst a, GBL_TEMP_ENGINE b WHERE a.po_break_down_id = b.ref_val and  b.user_id=$user_id and b.entry_form=999 and  a.status_active=1 and a.is_deleted =0";
    $ex_fact_res = sql_select($ex_fact); 
    //    echo $ex_fact_res;die;
    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form = 999 ");
    oci_commit($con); 

    // print_r($po_arr) ;die;

    $ex_fac_arr=[];
    foreach($ex_fact_res as $v)
    {
        $ex_fac_arr[$v['PO_ID']] += $v['EX_FACTORY_QNTY'];
    }

    $po_id_cond = where_con_using_array($po_id_arr,0,"po_break_down_id");
    $sql = "SELECT c.po_break_down_id as po_id,c.production_type,c.production_quantity FROM pro_garments_production_mst c  WHERE c.status_Active=1 and c.is_deleted = 0 and c.production_type in (1,4,5,8)   $po_id_cond "; 
    // echo $sql;
    $result = sql_select($sql);
    foreach($result  as $res)
    {
        $prod_data_array[$res['PO_ID']][$res['PRODUCTION_TYPE']] +=  $res['PRODUCTION_QUANTITY']; 
       
    }
    ?>
    <style>
        .tableFixHead          { height: 100vh; }
        .tableFixHead thead th { position: sticky;   top: 0; z-index: 99; }
     </style>
    </head>
    <body>
        <div align="center" style="width:<?= $width ?>px;">
            <form name="styleRef_form" id="styleRef_form"> 
                <fieldset style="width:<?= $width ?>px;" class="tableFixHead">
                    <table cellspacing="0" width="1500" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                        <thead>
                            <tr >
                                <th  width='30'>SL</th> 
                                <th  width='130'>Company</th>
                                <th  width='90'>Buyer</th>
                                <th  width='110'>Style</th>
                                <th  width='80'>Job No</th>
                                <th  width='30'>Year</th>
                                <th  width='80'>Order No</th>
                                <th  width='80'>Order Qty</th>
                                <th  width='30'>UOM</th> 
                                <th  width='70'>Ship Date</th>
                                <th  width='80'>Cutting Qty</th>
                                <th  width='80'>Cutting Balance Qty</th>
                                <th  width='80'>Sewing Input Qty</th>
                                <th  width='80'>Sewing Input Balance</th>
                                <th  width='80'>Sewing Output Qty</th>
                                <th  width='80'>Sewing WIP</th>
                                <th  width='80'>Production Balance</th> 
                                <th  width='80'>Finish Qty</th> 
                                <th  width='80'>Finish Balance</th> 
                                <th  width='80'>Ex- Factory Qty</th> 
                                <th  width='80'>Ex-Factory WIP</th> 
                                <th  width='80'>Ex-fac. Bal.</th> 
                            </tr>
                            <?php
                            $i = 0;
                            $total_po_qty = 0;
                            $total_cut_qty = 0;
                            $total_swe_in = 0;
                            $total_swe_out = 0; 
                            $total_finish_qty = 0; 
                            $total_finish_blnc = 0; 
                            $total_ex_fact_qty = 0; 
                            $total_ex_fact_wip = 0; 
                            $total_ex_fact_blnc = 0; 
                            if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
                            foreach($data_array as $job_id=>$job_array)
                            {  
                                foreach($job_array as $po_id=>$v)
                                {     
                                    $po_qty = $v['PO_QUANTITY'];
                                    $cut_qty = $prod_data_array[$po_id][1];
                                    $sew_input = $prod_data_array[$po_id][4];
                                    $sew_out = $prod_data_array[$po_id][5];
                                    $finish_qty = $prod_data_array[$po_id][8];
                                    $ex_fact_qty =  $ex_fac_arr[$po_id];

                                    $cut_balance = $po_qty - $cut_qty ;
                                    $sew_input_blnc = $cut_qty - $sew_input;
                                    $sew_out_blnc = $sew_input - $sew_out;
                                    $prod_blnc = $po_qty - $sew_out;
                                    $finish_blnc = $sew_out - $finish_qty;
                                    $ex_fact_wip = $finish_qty - $ex_fact_qty;
                                    $ex_fact_blnc = $po_qty - $ex_fact_qty;

                                    $total_po_qty +=   $po_qty;
                                    $total_cut_qty += $cut_qty;
                                    $total_swe_in += $sew_input;
                                    $total_swe_out += $sew_out; 
                                    $total_finish_qty += $finish_qty;
                                    $total_finish_blnc += $finish_blnc;
                                    $total_ex_fact_qty += $ex_fact_qty;
                                    $total_ex_fact_wip += $ex_fact_wip;
                                    $total_ex_fact_blnc += $ex_fact_blnc;
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                        
                                        <td width='30'> <?= ++$i ?> </td>
                                        <td width='130'> <?=$company_arr[$v['COMPANY_ID']] ?> </td>  
                                        <td width='90'> <?= $buyer_arr[$v['BUYER_NAME']] ?> </td>
                                        <td width='110'> <?= $v['STYLE'] ?> </td>
                                        <td width='80'> <?= $v['JOB_NO'] ?> </td>
                                        <td width='30'> <?= $v['YEAR'] ?> </td>
                                        <td width='80'> <?= $v['PO_NUMBER']   ?> </td>
                                        <td width='80' align="right"> <?= $po_qty  ?> </td>
                                        <td width='30' align="center"> <?= $unit_lib[$v['ORDER_UOM']] ?> </td>
                                        <td width='70' align="right"> <?= $v['SHIP_DATE'] ?> </td> 
                                        <td width='80' align="right"> <?= round($cut_qty) ?> </td>
                                        <td width='80' align="right"> <?= round($cut_balance)  ?> </td>
                                        <td width='80' align="right"> <?= round($sew_input)  ?></td>
                                        <td width='80' align="right"> <?= round($sew_input_blnc) ?></td>
                                        <td width='80' align="right"> <?= round($sew_out)   ?> </td>
                                        <td width='80' align="right"> <?= round($sew_out_blnc) ?> </td>
                                        <td width='80' align="right"> <?= round($prod_blnc) ?> </td>
                                        <td width='80' align="right"> <?= round($finish_qty) ?> </td>
                                        <td width='80' align="right"> <?= round($finish_blnc) ?> </td>
                                        <td width='80' align="right"> <?= round($ex_fact_qty) ?> </td>
                                        <td width='80' align="right"> <?= round($ex_fact_wip) ?> </td>
                                        <td width='80' align="right"> <?= round($ex_fact_blnc) ?> </td>
                                    </tr>
                                    <?php
                                    $i++;
                                } 
                            }
                            ?>
                            <tr style="background: #dccdcd; font-weight:bold; ">
                                <td colspan="7" align="left">Grand Total </td>
                                <td align="right"><?= round($total_po_qty) ?></td>
                                <td colspan="2"></td>
                                <td align="right"><?= round($total_cut_qty) ?></td> 
                                <td align="right"><?= round($total_po_qty  - $total_cut_qty) ?></td>  
                                <td align="right"><?= round($total_swe_in) ?></td> 
                                <td align="right"><?= round($total_cut_qty  - $total_swe_in) ?></td>  
                                <td align="right"><?= round($total_swe_out) ?></td>  
                                <td align="right"><?= round($total_swe_in - $total_swe_out) ?></td>  
                                <td align="right"><?= round($total_po_qty - $total_swe_out) ?></td>  
                                <td align="right"><?= round($total_finish_qty) ?></td>  
                                <td align="right"><?= round($total_finish_blnc) ?></td>  
                                <td align="right"><?= round($total_ex_fact_qty) ?></td>  
                                <td align="right"><?= round($total_ex_fact_wip) ?></td>  
                                <td align="right"><?= round($total_ex_fact_blnc) ?></td>  
                            </tr>
                        </thead>
                    </table> 
                </fieldset>
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit(); 
}
if($action == 'generate_report') 
{  
    ob_start();
    $process = array( &$_POST );
    // echo "<pre>";
    // print_r($process ) ;
    // die; 
	extract(check_magic_quote_gpc( $process )); 
    $company_id = str_replace("'",'',$cbo_lc_company_name); 
    $cbo_lc_company_name = str_replace("'",'',$cbo_lc_company_name); 
    $cbo_work_company_name = str_replace("'",'',$cbo_work_company_name); 
    $cbo_buyer_name = str_replace("'",'',$cbo_buyer_name); 
    $form_date = change_date_format( str_replace("'","",trim($txt_date_from)) ); 
    $to_date = change_date_format( str_replace("'","",trim($txt_date_to)) ); 
    // echo $cbo_lc_company_name; 
    echo load_html_head_contents('Search', '../../../', 1, 1, '', '', '');

                        // For Month Wise Booking Vs Production
	if($type == 1) 
    {
        $con_condition = '';
        if( $cbo_lc_company_name !=0 )
        {
        $con_condition .= "and a.company_name=$cbo_lc_company_name" ;
        }  
        if($cbo_work_company_name !=0 )
        {
        $con_condition .= "and a.working_company_id in($cbo_work_company_name) " ;
        }   
        if($cbo_buyer_name != 0 )
        {
        $con_condition .= "and a.buyer_name in($cbo_buyer_name)" ;
        }   
        if($txt_date_from && $txt_date_to)
        {
        $con_condition .= "and b.pub_shipment_date between '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'" ;
        }  
        //   echo $con_condition;  die;
        $sql = "SELECT a.id,a.avg_unit_price ,(b.po_quantity*a.total_set_qnty) as po_quantity,c.smv_pcs,c.gmts_item_id,b.is_confirmed,to_char(b.pub_shipment_date,'MON-YYYY')as month_year , b.id as po_id FROM wo_po_details_master a,wo_po_break_down b,wo_po_details_mas_set_details c WHERE a.status_active=1 and b.status_active=1 and b.is_confirmed in(1,2) and a.is_deleted =0 and b.is_deleted =0  and a.id = b.job_id and a.id = c.job_id  $con_condition ORDER BY pub_shipment_date ASC";  
            
        //   echo $sql; die;
        $result =  sql_select($sql); 
        $data = [];
        $job_id_arr = [];
        $po_arr = [];
        foreach($result  as $res)
        {
            $job_id_arr[$res['ID']] = $res['ID'];
            $po_arr[$res['PO_ID']] = $res['PO_ID'];
            $data[$res['MONTH_YEAR']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['PO_QUANTITY'] +=  $res['PO_QUANTITY'];
            $data[$res['MONTH_YEAR']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['MINUTE'] +=  $res['PO_QUANTITY'] * $res['SMV_PCS']; 
            $data[$res['MONTH_YEAR']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['FOB'] +=  $res['PO_QUANTITY'] * $res['AVG_UNIT_PRICE']; 
            $data[$res['MONTH_YEAR']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['SMV_PCS'] += $res['SMV_PCS']; 
            $data[$res['MONTH_YEAR']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['UNIT_PRICE'] += $res['AVG_UNIT_PRICE']; 
        }
        //    print_r($po_arr); 
        $con = connect(); 
        execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in(899,999) ");
        oci_commit($con); 

        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 899, 1,$job_id_arr, $empty_arr);
        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 999, 1,$po_arr, $empty_arr);  
        oci_commit($con);   

        $cm_sql =  "SELECT a.job_id,a.cm_cost FROM WO_PRE_COST_DTLS a, GBL_TEMP_ENGINE b WHERE a.job_id = b.ref_val and  b.user_id=$user_id and b.entry_form=899 and  a.status_active=1 and a.is_deleted =0 ";
        $cm_cost_res = sql_select($cm_sql); 

        $pre_cost_sql =  "SELECT a.job_id,a.costing_per FROM WO_PRE_COST_MST a, GBL_TEMP_ENGINE b WHERE a.job_id = b.ref_val and  b.user_id=$user_id and b.entry_form=899 and   a.status_active=1 and a.is_deleted =0 and a.costing_per>0 ";
        $pre_cost_res = sql_select($pre_cost_sql);  
        //    echo $cm_sql; die; 

        //for production Qty 
        $prod_sql =  "SELECT a.production_quantity as prod_qty, a.po_break_down_id as po_id FROM pro_garments_production_mst a, GBL_TEMP_ENGINE b WHERE a.production_type= 5 and a.po_break_down_id = b.ref_val and  b.user_id=$user_id and b.entry_form=999 and  a.status_active=1 and a.is_deleted =0";
        $prod_sql_res = sql_select($prod_sql); 
        //    echo $prod_sql;die;
        execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in(899,999) ");
        oci_commit($con); 
   
        // print_r($po_arr) ;die;
   
        $cm_cost_arr=[];
        foreach($cm_cost_res as   $v)
        {
                $cm_cost_arr[$v['JOB_ID']] = $v['CM_COST'];
        }
            //  for prod qty
        $porod_qty_arr=[];
        foreach($prod_sql_res as   $v)
        {
            $porod_qty_arr[$v['PO_ID']] += $v['PROD_QTY'];
        }
        /*   echo "<pre>";
            print_r( $data) ;
            die;  */ 
        $pre_cost_arr=[];
        foreach($pre_cost_res as   $v)
        { 
            $pre_cost_arr[$v['JOB_ID']] += $v['COSTING_PER'];
        }
     /*    echo $pre_cost_arr[35968]; die;
          echo "<pre>";
            print_r( $pre_cost_arr) ;
            die;  */
        $month_wise_data_arr = [] ; 
        foreach($data as $month => $m )
        {
            foreach ($m as $is_confirmed => $c){
                foreach ($c as $job_id =>$job){ 
                    foreach ($job as $po_id =>$po){
                        foreach ($po as $item => $v){
                            $prod_qty = $porod_qty_arr[$po_id];
                            $cm_val =  $cm_cost_arr[$job_id];
                            $po_qty=  $v['PO_QUANTITY'];
                            $cost_per=  $pre_cost_arr[$job_id];
                            $cm = is_divideable($cost_per)? ($po_qty *( $cm_val / $cost_per)) : 0;
                            // for booking
                            $month_wise_data_arr[$month][$is_confirmed]['BOOKING']['PO_QUANTITY'] += $v['PO_QUANTITY'];  
                            $month_wise_data_arr[$month][$is_confirmed]['BOOKING']['MINUTES'] += $v['MINUTE'];  
                            $month_wise_data_arr[$month][$is_confirmed]['BOOKING']['FOB'] += $v['FOB'];  
                            $month_wise_data_arr[$month][$is_confirmed]['BOOKING']['CM'] += $cm ;  
                            // for production
                            $month_wise_data_arr[$month][$is_confirmed]['PROD']['PROD_QTY'] += $prod_qty;  
                            $month_wise_data_arr[$month][$is_confirmed]['PROD']['MINUTES'] += $prod_qty *  $v['SMV_PCS'] ;  
                            $month_wise_data_arr[$month][$is_confirmed]['PROD']['FOB'] +=  $prod_qty * $v['UNIT_PRICE'];  
                            $month_wise_data_arr[$month][$is_confirmed]['PROD']['CM'] += $cm ;  
                        } 
                    }
                }
            } 
        }    
       /*  echo "<pre>" ;
        print_r($month_wise_data_arr);
        die; */
        ?>
        <style>
            #report_table tbody{
                background: #fff;
            }
            .bg-gray{
                background: #dccdcd;
            }
        </style>
        <body>
            <div align="center"  id="scroll_body"> 
                <div style="margin-bottom:20px">
                    <h3>Month Wise Order Booking Vs Production</h3>
                    <p>Company Name : <span id='Company Name'> <?= $company_arr[$company_id] ?></span></p>
                    <p>Date Range : <span id='data_range'> <?=  $form_date ." To ".  $to_date ?> </span></p>
                </div>
                <fieldset style="width:1200px;"> 
                    <table width="1200" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="report_table">
                        <!-- Booking -->
                        <thead>
                            <tr>
                                <th colspan="13" >Booking</th>
                            </tr>
                            <tr>
                                <th rowspan="2" width='80px'>Month</th>
                                <th colspan="4" >Confirm</th>
                                <th colspan="4">Projection</th>
                                <th colspan="4">Total</th>
                            </tr> 
                            <tr>
                                <th  width='80px'>Qty</th>
                                <th  width='80px'>Minute</th>
                                <th  width='80px'>CM</th>
                                <th  width='80px'>Value</th>
                                <th  width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                            </tr> 
                        </thead> 
                        <tbody>
                            <?php  
                                $i =1;
                                $gt_qty_conf= $gt_min_conf =  $gt_cm_conf = $gt_fob_conf = $gt_qty_not_conf =  $gt_min_not_conf = $gt_cm_not_conf =  $gt_fob_not_conf = 0;  
                                foreach( $month_wise_data_arr as $m => $v)
                                {
                                    $gt_qty_conf += $v[1]['BOOKING']['PO_QUANTITY'];
                                    $gt_min_conf += $v[1]['BOOKING']['MINUTES'];
                                    $gt_cm_conf += $v[1]['BOOKING']['CM'];
                                    $gt_fob_conf += $v[1]['BOOKING']['FOB'];

                                    $gt_qty_not_conf += $v[2]['BOOKING']['PO_QUANTITY']; 
                                    $gt_min_not_conf += $v[2]['BOOKING']['MINUTES']; 
                                    $gt_cm_not_conf += $v[2]['BOOKING']['CM']; 
                                    $gt_fob_not_conf += $v[2]['BOOKING']['FOB']; 

                                    $total_po_qty =  $v[1]['BOOKING']['PO_QUANTITY'] + $v[2]['BOOKING']['PO_QUANTITY'];
                                    $total_minutes =  $v[1]['BOOKING']['MINUTES'] + $v[2]['BOOKING']['MINUTES'];
                                    $total_cm =  $v[1]['BOOKING']['CM'] + $v[2]['BOOKING']['CM'];
                                    $total_fob =  $v[1]['BOOKING']['FOB'] + $v[2]['BOOKING']['FOB'];
                                   ?>
                                <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td width='80px'><?= $m  ?></td>
                                    <td width='80px' align="right"> <a onclick="open_popup('booking_qty_popup','<?= $m ?>','Booking Quantity')" href="javascript:void(0)"><?= $v[1]['BOOKING']['PO_QUANTITY']  ?></a>  </td>
                                    <td width='80px' align="right"><?= round($v[1]['BOOKING']['MINUTES'])  ?></td>
                                    <td width='80px' align="right"><?= round($v[1]['BOOKING']['CM'])  ?></td>
                                    <td width='80px' align="right"><?= round($v[1]['BOOKING']['FOB'])  ?></td>
                                    <td width='80px' align="right"><?= $v[2]['BOOKING']['PO_QUANTITY']  ?></td>
                                    <td width='80px' align="right"><?= round($v[2]['BOOKING']['MINUTES']) ?></td>
                                    <td width='80px' align="right"><?= round($v[2]['BOOKING']['CM'])  ?></td>
                                    <td width='80px' align="right"><?= round($v[2]['BOOKING']['FOB'])  ?></td>
                                    <td width='80px' align="right"><?= $total_po_qty ?></td>
                                    <td width='80px' align="right"><?= round($total_minutes) ?></td>
                                    <td width='80px' align="right"><?= round($total_cm) ?></td>
                                    <td width='80px' align="right"><?= round($total_fob)  ?></td>
                                </tr>
                            <?php
                                $i++;
                                }
                                // echo '<pre>';
                                // print_r($gt);
                                // die;
                            ?> 
                            <tr class="bg-gray">
                                <td width='80px'> <b> Total </b> </td>
                                <td width='80px' align="right"> <b> <?= $gt_qty_conf ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $gt_min_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round( $gt_cm_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($gt_fob_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $gt_qty_not_conf ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $gt_min_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $gt_cm_not_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round($gt_fob_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $gt_qty_conf +  $gt_qty_not_conf ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($gt_min_conf +  $gt_min_not_conf ) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($gt_cm_conf +  $gt_cm_not_conf ) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $gt_fob_conf + $gt_fob_not_conf) ?> </b> </td>
                            </tr>
                        </tbody>
                        <!-- Production -->
                        <thead>
                            <tr>
                                <th colspan="13">Production</th>
                            </tr>
                            <tr>
                                <th rowspan="2" width='80px'>Month</th>
                                <th colspan="4">Confirm</th>
                                <th colspan="4">Projection</th>
                                <th colspan="4">Total</th>
                            </tr>
                            <tr>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                            </tr> 
                        </thead> 
                        <tbody>
                            <?php  
                                $i =1;
                                $prod_gt_qty_conf= $prod_gt_min_conf =  $prod_gt_cm_conf = $prod_gt_fob_conf = $prod_gt_qty_not_conf =  $prod_gt_min_not_conf = $prod_gt_cm_not_conf =  $prod_gt_fob_not_conf = 0;  
                                foreach( $month_wise_data_arr as $m => $v)
                                {  
                                    // print_r($v);
                                    //     echo  $v[1]['BOOKING']['FOB'];
                                    //   die;
                                    $prod_gt_qty_conf += $v[1]['PROD']['PROD_QTY'];
                                    $prod_gt_min_conf += $v[1]['PROD']['MINUTES'];
                                    $prod_gt_cm_conf += $v[1]['PROD']['CM'];
                                    $prod_gt_fob_conf += $v[1]['PROD']['FOB'];

                                    $prod_gt_qty_not_conf += $v[2]['PROD']['PROD_QTY']; 
                                    $prod_gt_min_not_conf += $v[2]['PROD']['MINUTES']; 
                                    $prod_gt_cm_not_conf += $v[2]['PROD']['CM']; 
                                    $prod_gt_fob_not_conf += $v[2]['PROD']['FOB']; 

                                    $prod_total_po_qty =  $v[1]['PROD']['PROD_QTY'] + $v[2]['PROD']['PROD_QTY'];
                                    $prod_total_minutes =  $v[1]['PROD']['MINUTES'] + $v[2]['PROD']['MINUTES'];
                                    $prod_total_cm =  $v[1]['PROD']['CM'] + $v[2]['PROD']['CM'];
                                    $prod_total_fob =  $v[1]['PROD']['FOB'] + $v[2]['PROD']['FOB'];
                                 ?>
                                    <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                        <td width='80px'><?= $m  ?></td>
                                        <td width='80px' align="right"> <a onclick="open_popup('prod_qty_popup','<?= $m ?>','Production Quantity')" href="javascript:void(0)"><?= $v[1]['PROD']['PROD_QTY']  ?> </a> </td>
                                        <td width='80px' align="right"><?= round($v[1]['PROD']['MINUTES'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[1]['PROD']['CM'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[1]['PROD']['FOB'])  ?></td>
                                        <td width='80px' align="right"><?= $v[2]['PROD']['PROD_QTY']  ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['PROD']['MINUTES']) ?></td>
                                        <td width='80px' align="right"><?= $v[2]['PROD']['CM']  ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['PROD']['FOB'])  ?></td>
                                        <td width='80px' align="right"><?= $prod_total_po_qty ?></td>
                                        <td width='80px' align="right"><?= round($prod_total_minutes) ?></td>
                                        <td width='80px' align="right"><?= round($prod_total_cm) ?></td>
                                        <td width='80px' align="right"><?= round($prod_total_fob)  ?></td>
                                    </tr>
                                <?php
                                $i++;
                                } 
                                ?> 
                                    <tr class="bg-gray">
                                        <td width='80px'> <b> Total </b> </td>
                                        <td width='80px' align="right" > <b> <?= $prod_gt_qty_conf ?> </b> </td>
                                        <td width='80px' align="right" > <b> <?= round( $prod_gt_min_conf) ?></b> </td>
                                        <td width='80px' align="right" > <b> <?= round( $prod_gt_cm_conf) ?> </b> </td>
                                        <td width='80px' align="right" > <b> <?= round($prod_gt_fob_conf) ?> </b> </td>
                                        <td width='80px' align="right" > <b> <?= $prod_gt_qty_not_conf ?> </b> </td>
                                        <td width='80px' align="right" > <b><?= round( $prod_gt_min_not_conf) ?> </b> </td>
                                        <td width='80px' align="right" > <b> <?= round( $prod_gt_cm_not_conf) ?></b> </td>
                                        <td width='80px' align="right" > <b> <?= round($prod_gt_fob_not_conf) ?> </b> </td>
                                        <td width='80px' align="right" > <b> <?= $prod_gt_qty_conf + $prod_gt_qty_not_conf  ?> </b> </td>
                                        <td width='80px' align="right" > <b> <?= round($prod_gt_min_conf +  $prod_gt_min_not_conf ) ?> </b> </td>
                                        <td width='80px' align="right" > <b> <?= round($prod_gt_cm_conf +  $prod_gt_cm_not_conf ) ?> </b> </td>
                                        <td width='80px' align="right" > <b>  <?= round( $prod_gt_fob_conf + $prod_gt_fob_not_conf) ?> </b> </td>
                                    </tr>
                        </tbody>
                        <!-- balance -->
                        <thead>
                            <tr>
                                <th colspan="13">Balance</th>
                            </tr>
                            <tr>
                                <th rowspan="2" width='80px'>Month</th>
                                <th colspan="4">Confirm</th>
                                <th colspan="4">Projection</th>
                                <th colspan="4">Total</th>
                            </tr>
                            <tr>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                            </tr> 
                        </thead> 
                        <tbody>
                            <?php
                                $i=1;
                                $blnc_gt_qty_conf= $blnc_gt_min_conf =  $blnc_gt_cm_conf = $blnc_gt_fob_conf = $blnc_gt_qty_not_conf =  $blnc_gt_min_not_conf = $blnc_gt_cm_not_conf =  $blnc_gt_fob_not_conf = 0;  
                                foreach( $month_wise_data_arr as $m => $v)
                                {
                                    
                                    $comp_qty = $v[1]['BOOKING']['PO_QUANTITY'] - $v[1]['PROD']['PROD_QTY']  ;
                                    $comp_minutes = $v[1]['BOOKING']['MINUTES'] - $v[1]['PROD']['MINUTES']  ;
                                    $comp_cm = $v[1]['BOOKING']['CM'] - $v[1]['PROD']['CM'] ;
                                    $comp_fob = $v[1]['BOOKING']['FOB'] - $v[1]['PROD']['FOB'] ;
                                    $not_comp_qty = $v[2]['BOOKING']['PO_QUANTITY'] - $v[2]['PROD']['PROD_QTY']  ;
                                    $not_comp_minutes = $v[2]['BOOKING']['MINUTES'] - $v[2]['PROD']['MINUTES']  ;
                                    $not_comp_cm = $v[2]['BOOKING']['CM'] - $v[2]['PROD']['CM'] ;
                                    $not_comp_fob = $v[2]['BOOKING']['FOB'] - $v[2]['PROD']['FOB'] ;

                                    $blnc_gt_qty_conf += $comp_qty ;
                                    $blnc_gt_min_conf += $comp_minutes ;
                                    $blnc_gt_cm_conf +=  $comp_cm  ;
                                    $blnc_gt_fob_conf += $comp_fob ;
                                    $blnc_gt_qty_not_conf += $not_comp_qty  ;
                                    $blnc_gt_min_not_conf +=  $not_comp_minutes;
                                    $blnc_gt_cm_not_conf +=  $not_comp_cm ;
                                    $blnc_gt_fob_not_conf +=  $not_comp_fob;
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3rd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_3rd<? echo $i; ?>">
                                        <td width='80px'><?= $m ?></td>
                                        <td width='80px' align="right"><a onclick="open_popup('prod_balance_popup','<?= $m ?>','Production Balance')" href="javascript:void(0)"><?=  $comp_qty  ?> </a></td>
                                        <td width='80px' align="right"><?= round($comp_minutes) ?></td>
                                        <td width='80px' align="right"><?= round($comp_cm) ?></td> 
                                        <td width='80px' align="right"><?= round($comp_fob) ?></td> 
                                        <td width='80px' align="right"><?= $not_comp_qty ?></td>
                                        <td width='80px' align="right"><?= round($not_comp_minutes) ?></td>
                                        <td width='80px' align="right"><?= round($not_comp_cm) ?></td> 
                                        <td width='80px' align="right"><?= round($not_comp_fob) ?></td>  
                                        <td width='80px' align="right"><?= $comp_qty + $not_comp_qty ?></td>
                                        <td width='80px' align="right"><?= round($comp_minutes + $not_comp_minutes ) ?></td>
                                        <td width='80px' align="right"><?= round($comp_cm + $not_comp_cm ) ?></td>
                                        <td width='80px' align="right"><?= round($comp_fob + $not_comp_fob ) ?></td> 
                                    </tr> 
                            <?php
                                $i++;
                                }
                            ?>
                            <tr class="bg-gray">
                                <td width='80px'> <b> Total </b> </td>
                                <td width='80px' align="right"> <b> <?= $blnc_gt_qty_conf ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $blnc_gt_min_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round( $blnc_gt_cm_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($blnc_gt_fob_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $blnc_gt_qty_not_conf ?> </b> </td>
                                <td width='80px' align="right"> <b><?= round( $blnc_gt_min_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $blnc_gt_cm_not_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round($blnc_gt_fob_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $blnc_gt_qty_conf + $blnc_gt_qty_not_conf  ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($blnc_gt_min_conf + $blnc_gt_min_not_conf ) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($blnc_gt_cm_conf + $blnc_gt_cm_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b>  <?= round( $blnc_gt_fob_conf + $blnc_gt_fob_not_conf ) ?> </b> </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset> 
            </div>
        </body> 
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>           
        </html> 
        <?php        
                        // For Month Wise Booking Vs Shipmint  
	} 
    else if($type == 2) 
    {
        $con_condition = '';
        if( $cbo_lc_company_name !=0 ){
        $con_condition .= "and a.company_name=$cbo_lc_company_name" ;
        }  
        if($cbo_work_company_name !=0 )
        {
        $con_condition .= "and a.working_company_id in($cbo_work_company_name) " ;
        }   
        if($cbo_buyer_name != 0 )
        {
        $con_condition .= "and a.buyer_name in($cbo_buyer_name)" ;
        }     
        if($txt_date_from && $txt_date_to){
        $con_condition .= "and b.pub_shipment_date between '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'" ;
        }  
        //   echo $con_condition;  die;
        $sql = "SELECT a.id,a.avg_unit_price ,(b.po_quantity*a.total_set_qnty) as po_quantity,c.smv_pcs,c.gmts_item_id,b.is_confirmed,to_char(b.pub_shipment_date,'MON-YYYY')as month_year , b.id as po_id FROM wo_po_details_master a,wo_po_break_down b,wo_po_details_mas_set_details c WHERE a.id = b.job_id and a.id = c.job_id and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted =0 and b.is_confirmed in(1,2) $con_condition ORDER BY pub_shipment_date ASC";  

        // echo $sql; die;
        $result =  sql_select($sql); 
        $data = [];
        $job_id_arr = [];
        $po_arr = [];
        foreach($result  as $res)
        {
                $job_id_arr[$res['ID']] = $res['ID'];
                $po_arr[$res['PO_ID']] = $res['PO_ID'];
                $data[$res['MONTH_YEAR']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['PO_QUANTITY'] +=  $res['PO_QUANTITY'];
                $data[$res['MONTH_YEAR']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['MINUTE'] +=  $res['PO_QUANTITY'] * $res['SMV_PCS']; 
                $data[$res['MONTH_YEAR']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['FOB'] +=  $res['PO_QUANTITY'] * $res['AVG_UNIT_PRICE']; 
                $data[$res['MONTH_YEAR']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['SMV_PCS'] += $res['SMV_PCS']; 
                $data[$res['MONTH_YEAR']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['UNIT_PRICE'] += $res['AVG_UNIT_PRICE']; 
        }
        //    print_r($po_arr); 
   
        $con = connect(); 
        execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in(899,999) ");
        oci_commit($con); 

        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 899, 1,$job_id_arr, $empty_arr);
        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 999, 1,$po_arr, $empty_arr); 
        oci_commit($con);   

        $pre_cost_sql =  "SELECT a.job_id,a.costing_per FROM WO_PRE_COST_MST a, GBL_TEMP_ENGINE b WHERE a.job_id = b.ref_val and  b.user_id=$user_id and b.entry_form=899 and  a.status_active=1 and a.is_deleted =0 and a.costing_per > 0";
        $pre_cost_res = sql_select($pre_cost_sql); 

        $cm_sql =  "SELECT a.job_id,a.cm_cost FROM WO_PRE_COST_DTLS a, GBL_TEMP_ENGINE b WHERE a.job_id = b.ref_val and  b.user_id=$user_id and b.entry_form=899 and  a.status_active=1 and a.is_deleted =0 ";
        $cm_cost_res = sql_select($cm_sql); 
        //    echo $cm_sql; die; 

        //for ex_factory_qnty/Shipment Qty 
        $ex_fact =  "SELECT a.ex_factory_qnty, a.po_break_down_id as po_id FROM pro_ex_factory_mst a, GBL_TEMP_ENGINE b WHERE a.po_break_down_id = b.ref_val and  b.user_id=$user_id and b.entry_form=999 and  a.status_active=1 and a.is_deleted =0";
        $ex_fact_res = sql_select($ex_fact); 
        //    echo $ex_fact_res;die;
        execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in(899,999) ");
        oci_commit($con); 
   
        // print_r($po_arr) ;die;
   
        $cm_cost_arr=[];
        foreach($cm_cost_res as   $v){
            $cm_cost_arr[$v['JOB_ID']] = $v['CM_COST'];
        }
            //  for shipment qty
        $ex_fac_arr=[];
        foreach($ex_fact_res as   $v)
        {
            $ex_fac_arr[$v['PO_ID']] += $v['EX_FACTORY_QNTY'];
        }
        $pre_cost_arr=[];
        foreach($pre_cost_res as   $v)
        { 
            $pre_cost_arr[$v['JOB_ID']] += $v['COSTING_PER'];
        }
        /*   echo "<pre>";
        print_r( $data) ;
        die;  */
        $month_wise_data_arr = [] ; 
        foreach($data as $month => $m ){
            foreach ($m as $is_confirmed => $c){
                foreach ($c as $job_id =>$job){ 
                    foreach ($job as $po_id =>$po){
                        foreach ($po as $item => $v){
                            $ex_fac_qty =  $ex_fac_arr[$po_id];
                            $cm_val =  $cm_cost_arr[$job_id];
                            $po_qty=  $v['PO_QUANTITY'];
                            $cost_per=  $pre_cost_arr[$job_id];
                            $booking_cm = is_divideable($cost_per) ? ($po_qty *( $cm_val / $cost_per)) : 0;
                            $shipment_cm =   is_divideable($cost_per) ? ($ex_fac_qty *( $cm_val / $cost_per)) : 0;
                            // for booking
                            $month_wise_data_arr[$month][$is_confirmed]['BOOKING']['PO_QUANTITY'] += $v['PO_QUANTITY'];  
                            $month_wise_data_arr[$month][$is_confirmed]['BOOKING']['MINUTES'] += $v['MINUTE'];  
                            $month_wise_data_arr[$month][$is_confirmed]['BOOKING']['FOB'] += $v['FOB'];  
                            $month_wise_data_arr[$month][$is_confirmed]['BOOKING']['CM'] +=  $booking_cm ;  
                            // for shipment
                            $month_wise_data_arr[$month][$is_confirmed]['SHIPMENT']['EX_FACTORY_QNTY'] += $ex_fac_qty;  
                            $month_wise_data_arr[$month][$is_confirmed]['SHIPMENT']['MINUTES'] += $ex_fac_qty *  $v['SMV_PCS'] ;  
                            $month_wise_data_arr[$month][$is_confirmed]['SHIPMENT']['FOB'] +=  $ex_fac_qty * $v['UNIT_PRICE'];  
                            $month_wise_data_arr[$month][$is_confirmed]['SHIPMENT']['CM'] += $shipment_cm;  
                        } 
                    }
                }
            } 
        }     
      ?>
        <style>
            #report_table tbody{
                background: #fff;
            }
            .bg-gray{
                background: #dccdcd;
            }
        </style>
        <body>
            <div align="center"  id="scroll_body"> 
                <div style="margin-bottom:20px">
                    <h3>Month Wise Order Booking Vs Shipment</h3>
                    <p>Company Name : <span id='Company Name'> <?= $company_arr[$company_id] ?></span></p>
                    <p>Date Range : <span id='data_range'> <?=  $form_date ." To ".  $to_date ?> </span></p>
                </div>
                <fieldset style="width:1200px;"> 
                    <table width="1200" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="report_table">
                        <!-- Booking -->
                        <thead>
                            <tr>
                                <th colspan="13" >Booking</th>
                            </tr>
                            <tr>
                                <th rowspan="2" width='80px'>Month</th>
                                <th colspan="4" >Confirm</th>
                                <th colspan="4">Projection</th>
                                <th colspan="4">Total</th>
                            </tr> 
                            <tr>
                                <th  width='80px'>Qty</th>
                                <th  width='80px'>Minute</th>
                                <th  width='80px'>CM</th>
                                <th  width='80px'>Value</th>
                                <th  width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                            </tr> 
                        </thead> 
                        <tbody>
                            <?php  
                                $i =1;
                                $gt_qty_conf= $gt_min_conf =  $gt_cm_conf = $gt_fob_conf = $gt_qty_not_conf =  $gt_min_not_conf = $gt_cm_not_conf =  $gt_fob_not_conf = 0;  
                                foreach( $month_wise_data_arr as $m => $v)
                                {  
                                    // echo "<pre>";
                                    // print_r( $v);
                                    // die;
                                    $gt_qty_conf += $v[1]['BOOKING']['PO_QUANTITY'];
                                    $gt_min_conf += $v[1]['BOOKING']['MINUTES'];
                                    $gt_cm_conf += $v[1]['BOOKING']['CM'];
                                    $gt_fob_conf += $v[1]['BOOKING']['FOB'];

                                    $gt_qty_not_conf += $v[2]['BOOKING']['PO_QUANTITY']; 
                                    $gt_min_not_conf += $v[2]['BOOKING']['MINUTES']; 
                                    $gt_cm_not_conf += $v[2]['BOOKING']['CM']; 
                                    $gt_fob_not_conf += $v[2]['BOOKING']['FOB']; 

                                    $total_po_qty =  $v[1]['BOOKING']['PO_QUANTITY'] + $v[2]['BOOKING']['PO_QUANTITY'];
                                    $total_minutes =  $v[1]['BOOKING']['MINUTES'] + $v[2]['BOOKING']['MINUTES'];
                                    $total_cm =  $v[1]['BOOKING']['CM'] + $v[2]['BOOKING']['CM'];
                                    $total_fob =  $v[1]['BOOKING']['FOB'] + $v[2]['BOOKING']['FOB'];
                                    ?>
                                    <tr   bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1st<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1st<? echo $i; ?>">
                                        <td width='80px'><?= $m  ?></td>
                                        <td width='80px' align="right"><a onclick="open_popup('booking_qty_popup','<?= $m ?>','Booking Quantity')" href="javascript:void(0)"><?= $v[1]['BOOKING']['PO_QUANTITY']  ?></a> </td>
                                        <td width='80px' align="right"><?= round($v[1]['BOOKING']['MINUTES'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[1]['BOOKING']['CM'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[1]['BOOKING']['FOB'])  ?></td>
                                        <td width='80px' align="right"><?= $v[2]['BOOKING']['PO_QUANTITY']  ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['BOOKING']['MINUTES']) ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['BOOKING']['CM'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['BOOKING']['FOB'])  ?></td>
                                        <td width='80px' align="right"><?= $total_po_qty ?></td>
                                        <td width='80px' align="right"><?= round($total_minutes) ?></td>
                                        <td width='80px' align="right"><?= round($total_cm) ?></td>
                                        <td width='80px' align="right"><?= round($total_fob)  ?></td>
                                    </tr>
                            <?php
                                $i++;
                                }
                                // echo '<pre>';
                                // print_r($gt);
                                // die;
                            ?> 
                            <tr class="bg-gray">
                                <td width='80px'> <b> Total </b> </td>
                                <td width='80px' align="right"> <b> <?= $gt_qty_conf ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $gt_min_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round( $gt_cm_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($gt_fob_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $gt_qty_not_conf ?> </b> </td>
                                <td width='80px' align="right"> <b><?= round( $gt_min_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $gt_cm_not_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round($gt_fob_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $gt_qty_conf +  $gt_qty_not_conf ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($gt_min_conf +  $gt_min_not_conf ) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($gt_cm_conf +  $gt_cm_not_conf ) ?> </b> </td>
                                <td width='80px' align="right"> <b>  <?= round( $gt_fob_conf + $gt_fob_not_conf) ?> </b> </td>
                            </tr>
                        </tbody>
                        <!-- Shipment -->
                        <thead>
                            <tr>
                                <th colspan="13">Shipment</th>
                            </tr>
                            <tr>
                                <th rowspan="2" width='80px'>Month</th>
                                <th colspan="4">Confirm</th>
                                <th colspan="4">Projection</th>
                                <th colspan="4">Total</th>
                            </tr>
                            <tr>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                            </tr> 
                        </thead> 
                        <tbody>
                            <?php  
                                $i =1;
                                $ship_gt_qty_conf= $ship_gt_min_conf =  $ship_gt_cm_conf = $ship_gt_fob_conf = $ship_gt_qty_not_conf =  $ship_gt_min_not_conf = $ship_gt_cm_not_conf =  $ship_gt_fob_not_conf = 0;  
                                foreach( $month_wise_data_arr as $m => $v)
                                {  
                                    // print_r($v);
                                    //     echo  $v[1]['BOOKING']['FOB'];
                                    //   die;
                                    $ship_gt_qty_conf += $v[1]['SHIPMENT']['EX_FACTORY_QNTY'];
                                    $ship_gt_min_conf += $v[1]['SHIPMENT']['MINUTES'];
                                    $ship_gt_cm_conf += $v[1]['SHIPMENT']['CM'];
                                    $ship_gt_fob_conf += $v[1]['SHIPMENT']['FOB'];

                                    $ship_gt_qty_not_conf += $v[2]['SHIPMENT']['EX_FACTORY_QNTY']; 
                                    $ship_gt_min_not_conf += $v[2]['SHIPMENT']['MINUTES']; 
                                    $ship_gt_cm_not_conf += $v[2]['SHIPMENT']['CM']; 
                                    $ship_gt_fob_not_conf += $v[2]['SHIPMENT']['FOB']; 

                                    $ship_total_po_qty =  $v[1]['SHIPMENT']['EX_FACTORY_QNTY'] + $v[2]['SHIPMENT']['EX_FACTORY_QNTY'];
                                    $ship_total_minutes =  $v[1]['SHIPMENT']['MINUTES'] + $v[2]['SHIPMENT']['MINUTES'];
                                    $ship_total_cm =  $v[1]['SHIPMENT']['CM'] + $v[2]['SHIPMENT']['CM'];
                                    $ship_total_fob =  $v[1]['SHIPMENT']['FOB'] + $v[2]['SHIPMENT']['FOB'];
                                 ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                        <td width='80px'><?= $m  ?></td>
                                        <td width='80px' align="right"> <a onclick="open_popup('shipment_qty_popup','<?= $m ?>','Shipment Quantity')" href="javascript:void(0)"><?= $v[1]['SHIPMENT']['EX_FACTORY_QNTY']   ?></a>   </td>
                                        <td width='80px' align="right"><?= round($v[1]['SHIPMENT']['MINUTES'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[1]['SHIPMENT']['CM'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[1]['SHIPMENT']['FOB'])  ?></td>
                                        <td width='80px' align="right"><?= $v[2]['SHIPMENT']['EX_FACTORY_QNTY']  ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['SHIPMENT']['MINUTES']) ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['SHIPMENT']['CM'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['SHIPMENT']['FOB'])  ?></td>
                                        <td width='80px' align="right"><?= $ship_total_po_qty ?></td>
                                        <td width='80px' align="right"><?= round($ship_total_minutes) ?></td>
                                        <td width='80px' align="right"><?= round($ship_total_cm) ?></td>
                                        <td width='80px' align="right"><?= round($ship_total_fob)  ?></td>
                                    </tr>
                            <?php
                            $i++;
                                }
                                // echo '<pre>';
                                // print_r($gt);
                                // die;
                            ?> 
                            <tr class="bg-gray">
                                <td width='80px'> <b> Total </b> </td>
                                <td width='80px' align="right"> <b> <?= $ship_gt_qty_conf ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $ship_gt_min_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round( $ship_gt_cm_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($ship_gt_fob_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $ship_gt_qty_not_conf ?> </b> </td>
                                <td width='80px' align="right"> <b><?= round( $ship_gt_min_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $ship_gt_cm_not_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round($ship_gt_fob_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $ship_gt_qty_conf + $ship_gt_qty_not_conf  ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($ship_gt_min_conf +  $ship_gt_min_not_conf ) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($ship_gt_cm_conf +  $ship_gt_cm_not_conf ) ?> </b> </td>
                                <td width='80px' align="right"> <b>  <?= round( $ship_gt_fob_conf + $ship_gt_fob_not_conf) ?> </b> </td>
                            </tr>
                        </tbody>
                        <!-- balance -->
                        <thead>
                            <tr>
                                <th colspan="13">Balance</th>
                            </tr>
                            <tr>
                                <th rowspan="2" width='80px'>Month</th>
                                <th colspan="4">Confirm</th>
                                <th colspan="4">Projection</th>
                                <th colspan="4">Total</th>
                            </tr>
                            <tr>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                            </tr> 
                        </thead> 
                        <tbody>
                            <?php
                                $i =1;
                                $blnc_gt_qty_conf= $blnc_gt_min_conf =  $blnc_gt_cm_conf = $blnc_gt_fob_conf = $blnc_gt_qty_not_conf =  $blnc_gt_min_not_conf = $blnc_gt_cm_not_conf =  $blnc_gt_fob_not_conf = 0;  
                                foreach( $month_wise_data_arr as $m => $v)
                                {
                                    
                                    $comp_qty = $v[1]['BOOKING']['PO_QUANTITY'] - $v[1]['SHIPMENT']['EX_FACTORY_QNTY']  ;
                                    $comp_minutes = $v[1]['BOOKING']['MINUTES'] - $v[1]['SHIPMENT']['MINUTES']  ;
                                    $comp_cm = $v[1]['BOOKING']['CM'] - $v[1]['SHIPMENT']['CM'] ;
                                    $comp_fob = $v[1]['BOOKING']['FOB'] - $v[1]['SHIPMENT']['FOB'] ;
                                    $not_comp_qty = $v[2]['BOOKING']['PO_QUANTITY'] - $v[2]['SHIPMENT']['EX_FACTORY_QNTY']  ;
                                    $not_comp_minutes = $v[2]['BOOKING']['MINUTES'] - $v[2]['SHIPMENT']['MINUTES']  ;
                                    $not_comp_cm = $v[2]['BOOKING']['CM'] - $v[2]['SHIPMENT']['CM'] ;
                                    $not_comp_fob = $v[2]['BOOKING']['FOB'] - $v[2]['SHIPMENT']['FOB'] ;

                                    $blnc_gt_qty_conf += $comp_qty ;
                                    $blnc_gt_min_conf += $comp_minutes ;
                                    $blnc_gt_cm_conf +=  $comp_cm  ;
                                    $blnc_gt_fob_conf += $comp_fob ;
                                    $blnc_gt_qty_not_conf += $not_comp_qty  ;
                                    $blnc_gt_min_not_conf +=  $not_comp_minutes;
                                    $blnc_gt_cm_not_conf +=  $not_comp_cm ;
                                    $blnc_gt_fob_not_conf +=  $not_comp_fob;
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3rd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_3rd<? echo $i; ?>">
                                        <td width='80px'><?= $m ?></td>
                                        <td width='80px' align="right"> <a onclick="open_popup('shipment_balance_popup','<?= $m ?>','Shipment Balance Quantity')" href="javascript:void(0)"><?= $comp_qty  ?></a></td>
                                        <td width='80px' align="right"><?= round($comp_minutes) ?></td>
                                        <td width='80px' align="right"><?= round($comp_cm) ?></td> 
                                        <td width='80px' align="right"><?= round($comp_fob) ?></td> 
                                        <td width='80px' align="right"><?= $not_comp_qty ?></td>
                                        <td width='80px' align="right"><?= round($not_comp_minutes) ?></td>
                                        <td width='80px' align="right"><?= round($not_comp_cm) ?></td> 
                                        <td width='80px' align="right"><?= round($not_comp_fob) ?></td>  
                                        <td width='80px' align="right"><?= $comp_qty + $not_comp_qty ?></td>
                                        <td width='80px' align="right"><?= round($comp_minutes + $not_comp_minutes ) ?></td>
                                        <td width='80px' align="right"><?= round($comp_cm + $not_comp_cm ) ?></td>
                                        <td width='80px' align="right"><?= round($comp_fob + $not_comp_fob ) ?></td> 
                                    </tr> 
                            <?php
                            $i++;
                                }
                            ?>
                            <tr class="bg-gray">
                                <td width='80px'> <b> Total </b> </td>
                                <td width='80px' align="right"> <b> <?= $blnc_gt_qty_conf ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $blnc_gt_min_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round( $blnc_gt_cm_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($blnc_gt_fob_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $blnc_gt_qty_not_conf ?> </b> </td>
                                <td width='80px' align="right"> <b><?= round( $blnc_gt_min_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $blnc_gt_cm_not_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round($blnc_gt_fob_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $blnc_gt_qty_conf + $blnc_gt_qty_not_conf  ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($blnc_gt_min_conf + $blnc_gt_min_not_conf ) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($blnc_gt_cm_conf + $blnc_gt_cm_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b>  <?= round( $blnc_gt_fob_conf + $blnc_gt_fob_not_conf ) ?> </b> </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset> 
            </div>
        </body> 
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>           
        </html> 
        <?php        
                        // For Buyer Wise Booking Vs Production
	}
    else if($type == 3) 
    {
        $con_condition = '';
        if( $cbo_lc_company_name !=0 )
        {
        $con_condition .= "and a.company_name=$cbo_lc_company_name" ;
        }  
        if($cbo_work_company_name !=0 )
        {
        $con_condition .= "and a.working_company_id in($cbo_work_company_name) " ;
        }   
        if($cbo_buyer_name != 0 )
        {
        $con_condition .= "and a.buyer_name in($cbo_buyer_name)" ;
        }     
        if($txt_date_from && $txt_date_to)
        {
        $con_condition .= "and b.pub_shipment_date between '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'" ;
        }  
        //   echo $con_condition;  die;
        $sql = "SELECT a.id,a.avg_unit_price ,(b.po_quantity*a.total_set_qnty) as po_quantity,c.smv_pcs,c.gmts_item_id,b.is_confirmed,a.buyer_name, b.id as po_id FROM wo_po_details_master a,wo_po_break_down b,wo_po_details_mas_set_details c WHERE a.id = b.job_id and a.id = c.job_id and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted =0 and b.is_confirmed in(1,2) $con_condition ORDER BY a.buyer_name ASC";  
            
        // echo $sql; die;
        $result =  sql_select($sql); 
        $data = [];
        $job_id_arr = [];
        $po_arr = [];
        foreach($result  as $res)
        {
            $job_id_arr[$res['ID']] = $res['ID'];
            $po_arr[$res['PO_ID']] = $res['PO_ID'];
            $data[$res['BUYER_NAME']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['PO_QUANTITY'] +=  $res['PO_QUANTITY'];
            $data[$res['BUYER_NAME']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['MINUTE'] +=  $res['PO_QUANTITY'] * $res['SMV_PCS']; 
            $data[$res['BUYER_NAME']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['FOB'] +=  $res['PO_QUANTITY'] * $res['AVG_UNIT_PRICE']; 
            $data[$res['BUYER_NAME']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['SMV_PCS'] += $res['SMV_PCS']; 
            $data[$res['BUYER_NAME']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['UNIT_PRICE'] += $res['AVG_UNIT_PRICE']; 
        }
        //    print_r($po_arr); 
    
        $con = connect(); 
        execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in(899,999) ");
        oci_commit($con); 

        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 899, 1,$job_id_arr, $empty_arr);
        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 999, 1,$po_arr, $empty_arr); 
        oci_commit($con);   
        $cm_sql =  "SELECT a.job_id,a.cm_cost FROM WO_PRE_COST_DTLS a, GBL_TEMP_ENGINE b WHERE a.job_id = b.ref_val and  b.user_id=$user_id and b.entry_form=899 and  a.status_active=1 and a.is_deleted =0 ";
        $cm_cost_res = sql_select($cm_sql); 
        //    echo $cm_sql; die; 

        //for production Qty 
        $prod_sql =  "SELECT a.production_quantity as prod_qty, a.po_break_down_id as po_id FROM pro_garments_production_mst a, GBL_TEMP_ENGINE b WHERE a.production_type= 5 and a.po_break_down_id = b.ref_val and  b.user_id=$user_id and b.entry_form=999 and  a.status_active=1 and a.is_deleted =0";
        $prod_sql_res = sql_select($prod_sql); 

        $pre_cost_sql =  "SELECT a.job_id, a.costing_per FROM WO_PRE_COST_MST a, GBL_TEMP_ENGINE b WHERE a.job_id = b.ref_val and  b.user_id=$user_id and b.entry_form=899 and a.costing_per >0 and  a.status_active=1 and a.is_deleted =0 ";
        $pre_cost_res = sql_select($pre_cost_sql);  
        //    echo $prod_sql;die;
        execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in(899,999) ");
        oci_commit($con); 
   
        // print_r($po_arr) ;die;
   
        $cm_cost_arr=[];
        foreach($cm_cost_res as   $v)
        {
            $cm_cost_arr[$v['JOB_ID']] = $v['CM_COST'];
        }
            //  for prod qty
        $porod_qty_arr=[];
        foreach($prod_sql_res as   $v)
        {
            $porod_qty_arr[$v['PO_ID']] += $v['PROD_QTY'];
        }
        $pre_cost_arr=[];
        foreach($pre_cost_res as   $v)
        { 
            $pre_cost_arr[$v['JOB_ID']] += $v['COSTING_PER'];
        } 
        /*   echo "<pre>";
            print_r( $data) ;
            die;  */
        $buyer_wise_data_arr = [] ; 
        foreach($data as $buyer => $m )
        {
            foreach ($m as $is_confirmed => $c){
                foreach ($c as $job_id =>$job){ 
                    foreach ($job as $po_id =>$po){
                        foreach ($po as $item => $v){
                            $prod_qty = $porod_qty_arr[$po_id];
                            $cm_val =  $cm_cost_arr[$job_id] ;
                            $po_qty=  $v['PO_QUANTITY'] ;
                            $cost_per= $pre_cost_arr[$job_id] ; 
                            $booking_cm =  (is_divideable($cost_per))?  ($po_qty *( $cm_val / $cost_per)) : 0;
                            $prod_cm = (is_divideable($cost_per))?($prod_qty *( $cm_val / $cost_per)) : 0;
                            // for booking
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['BOOKING']['PO_QUANTITY'] += $v['PO_QUANTITY'];  
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['BOOKING']['MINUTES'] += $v['MINUTE'];  
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['BOOKING']['FOB'] += $v['FOB'];  
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['BOOKING']['CM'] += $booking_cm* 1 ;  
                            // for production
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['PROD']['PROD_QTY'] += $prod_qty;  
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['PROD']['MINUTES'] += $prod_qty *  $v['SMV_PCS'] ;  
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['PROD']['FOB'] +=  $prod_qty * $v['UNIT_PRICE'];  
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['PROD']['CM'] += $prod_cm* 1;  
                        } 
                    }
                }
            } 
        }     
        ?>
        <style>
            #report_table tbody{
                background: #fff;
            }
            .bg-gray{
                background: #dccdcd;
            }
        </style>
        <body>
            <div align="center"  id="scroll_body"> 
                <div style="margin-bottom:20px">
                    <h3>Buyer Wise Order Booking Vs Production</h3>
                    <p>LC Company Name : <span id='Company Name'> <?= $company_arr[$company_id] ?></span></p>
                    <p>Date Range : <span id='data_range'> <?=  $form_date ." To ".  $to_date ?> </span></p>
                </div>
                <fieldset style="width:1200px;"> 
                    <table width="1200" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="report_table">
                        <!-- Booking -->
                        <thead>
                            <tr>
                                <th colspan="13">Booking</th>
                            </tr>
                            <tr>
                                <th rowspan="2" width='80px'>Buyer Name</th>
                                <th colspan="4" >Confirm</th>
                                <th colspan="4">Projection</th>
                                <th colspan="4">Total</th>
                            </tr> 
                            <tr>
                                <th  width='80px'>Qty</th>
                                <th  width='80px'>Minute</th>
                                <th  width='80px'>CM</th>
                                <th  width='80px'>Value</th>
                                <th  width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                            </tr> 
                        </thead> 
                        <tbody>
                            <?php  
                                $i =1;
                                $gt_qty_conf= $gt_min_conf =  $gt_cm_conf = $gt_fob_conf = $gt_qty_not_conf =  $gt_min_not_conf = $gt_cm_not_conf =  $gt_fob_not_conf = 0;  
                                foreach( $buyer_wise_data_arr as $b => $v)
                                {  
                                    // echo "<pre>";
                                    // print_r( $v);
                                    // die;
                                    $gt_qty_conf += $v[1]['BOOKING']['PO_QUANTITY'];
                                    $gt_min_conf += $v[1]['BOOKING']['MINUTES'];
                                    $gt_cm_conf += $v[1]['BOOKING']['CM'];
                                    $gt_fob_conf += $v[1]['BOOKING']['FOB'];

                                    $gt_qty_not_conf += $v[2]['BOOKING']['PO_QUANTITY']; 
                                    $gt_min_not_conf += $v[2]['BOOKING']['MINUTES']; 
                                    $gt_cm_not_conf += $v[2]['BOOKING']['CM']; 
                                    $gt_fob_not_conf += $v[2]['BOOKING']['FOB']; 

                                    $total_po_qty =  $v[1]['BOOKING']['PO_QUANTITY'] + $v[2]['BOOKING']['PO_QUANTITY'];
                                    $total_minutes =  $v[1]['BOOKING']['MINUTES'] + $v[2]['BOOKING']['MINUTES'];
                                    $total_cm =  $v[1]['BOOKING']['CM'] + $v[2]['BOOKING']['CM'];
                                    $total_fob =  $v[1]['BOOKING']['FOB'] + $v[2]['BOOKING']['FOB'];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1st<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1st<? echo $i; ?>">
                                        <td width='80px'><?= $buyer_arr[$b] ?></td>
                                        <td width='80px' align="right"><a onclick="open_popup('booking_qty_popup','','Booking Quantity','<?= $b ?>')" href="javascript:void(0)"><?= $v[1]['BOOKING']['PO_QUANTITY']  ?></a></td>
                                        <td width='80px' align="right"><?= round($v[1]['BOOKING']['MINUTES'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[1]['BOOKING']['CM'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[1]['BOOKING']['FOB'])  ?></td>
                                        <td width='80px' align="right"><?= $v[2]['BOOKING']['PO_QUANTITY']  ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['BOOKING']['MINUTES']) ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['BOOKING']['CM'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['BOOKING']['FOB'])  ?></td>
                                        <td width='80px' align="right"><?= $total_po_qty ?></td>
                                        <td width='80px' align="right"><?= round($total_minutes) ?></td>
                                        <td width='80px' align="right"><?= round($total_cm) ?></td>
                                        <td width='80px' align="right"><?= round($total_fob)  ?></td>
                                    </tr>
                            <?php
                            $i++;
                                }
                                // echo '<pre>';
                                // print_r($gt);
                                // die;
                            ?> 
                            <tr class="bg-gray">
                                <td width='80px'> <b> Total </b> </td>
                                <td width='80px' align="right"> <b> <?= $gt_qty_conf ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $gt_min_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round( $gt_cm_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($gt_fob_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $gt_qty_not_conf ?> </b> </td>
                                <td width='80px' align="right"> <b><?= round( $gt_min_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $gt_cm_not_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round($gt_fob_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $gt_qty_conf +  $gt_qty_not_conf ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($gt_min_conf +  $gt_min_not_conf ) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($gt_cm_conf +  $gt_cm_not_conf ) ?> </b> </td>
                                <td width='80px' align="right"> <b>  <?= round( $gt_fob_conf + $gt_fob_not_conf) ?> </b> </td>
                            </tr>
                        </tbody>
                        <!-- Production -->
                        <thead>
                            <tr>
                                <th colspan="13">Production</th>
                            </tr>
                            <tr>
                                <th rowspan="2" width='80px'>Buyer Name</th>
                                <th colspan="4">Confirm</th>
                                <th colspan="4">Projection</th>
                                <th colspan="4">Total</th>
                            </tr>
                            <tr>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                            </tr> 
                        </thead> 
                        <tbody>
                            <?php 
                                $i =1; 
                                $prod_gt_qty_conf= $prod_gt_min_conf =  $prod_gt_cm_conf = $prod_gt_fob_conf = $prod_gt_qty_not_conf =  $prod_gt_min_not_conf = $prod_gt_cm_not_conf =  $prod_gt_fob_not_conf = 0;  
                                foreach( $buyer_wise_data_arr as $b => $v)
                                {  
                                    // print_r($v);
                                    //     echo  $v[1]['BOOKING']['FOB'];
                                    //   die;
                                    $prod_gt_qty_conf += $v[1]['PROD']['PROD_QTY'];
                                    $prod_gt_min_conf += $v[1]['PROD']['MINUTES'];
                                    $prod_gt_cm_conf += $v[1]['PROD']['CM'];
                                    $prod_gt_fob_conf += $v[1]['PROD']['FOB'];

                                    $prod_gt_qty_not_conf += $v[2]['PROD']['PROD_QTY']; 
                                    $prod_gt_min_not_conf += $v[2]['PROD']['MINUTES']; 
                                    $prod_gt_cm_not_conf += $v[2]['PROD']['CM']; 
                                    $prod_gt_fob_not_conf += $v[2]['PROD']['FOB']; 

                                    $prod_total_po_qty =  $v[1]['PROD']['PROD_QTY'] + $v[2]['PROD']['PROD_QTY'];
                                    $prod_total_minutes =  $v[1]['PROD']['MINUTES'] + $v[2]['PROD']['MINUTES'];
                                    $prod_total_cm =  $v[1]['PROD']['CM'] + $v[2]['PROD']['CM'];
                                    $prod_total_fob =  $v[1]['PROD']['FOB'] + $v[2]['PROD']['FOB'];
                                 ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                        <td width='80px'><?= $buyer_arr[$b] ?></td>
                                        <td width='80px' align="right"><a onclick="open_popup('prod_qty_popup','','Production Quantity','<?= $b ?>')" href="javascript:void(0)"><?= $v[1]['PROD']['PROD_QTY']  ?> </a></td>
                                        <td width='80px' align="right"><?= round($v[1]['PROD']['MINUTES'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[1]['PROD']['CM'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[1]['PROD']['FOB'])  ?></td>
                                        <td width='80px' align="right"><?= $v[2]['PROD']['PROD_QTY']  ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['PROD']['MINUTES']) ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['PROD']['CM'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['PROD']['FOB'])  ?></td>
                                        <td width='80px' align="right"><?= $prod_total_po_qty ?></td>
                                        <td width='80px' align="right"><?= round($prod_total_minutes) ?></td>
                                        <td width='80px' align="right"><?= round($prod_total_cm) ?></td>
                                        <td width='80px' align="right"><?= round($prod_total_fob)  ?></td>
                                    </tr>
                                <?php
                                $i++;
                                } 
                                ?> 
                                    <tr class="bg-gray">
                                        <td width='80px'> <b> Total </b> </td>
                                        <td width='80px' align="right"> <b> <?= $prod_gt_qty_conf ?> </b> </td>
                                        <td width='80px' align="right"> <b> <?= round( $prod_gt_min_conf) ?></b> </td>
                                        <td width='80px' align="right"> <b> <?= round( $prod_gt_cm_conf) ?> </b> </td>
                                        <td width='80px' align="right"> <b> <?= round($prod_gt_fob_conf) ?> </b> </td>
                                        <td width='80px' align="right"> <b> <?= $prod_gt_qty_not_conf ?> </b> </td>
                                        <td width='80px' align="right"> <b> <?= round( $prod_gt_min_not_conf) ?> </b> </td>
                                        <td width='80px' align="right"> <b> <?= round( $prod_gt_cm_not_conf) ?></b> </td>
                                        <td width='80px' align="right"> <b> <?= round($prod_gt_fob_not_conf) ?> </b> </td>
                                        <td width='80px' align="right"> <b> <?= $prod_gt_qty_conf + $prod_gt_qty_not_conf  ?> </b> </td>
                                        <td width='80px' align="right"> <b> <?= round($prod_gt_min_conf +  $prod_gt_min_not_conf ) ?> </b> </td>
                                        <td width='80px' align="right"> <b> <?= round($prod_gt_cm_conf +  $prod_gt_cm_not_conf ) ?> </b> </td>
                                        <td width='80px' align="right"> <b> <?= round( $prod_gt_fob_conf + $prod_gt_fob_not_conf) ?> </b> </td>
                                    </tr>
                        </tbody>
                        <!-- balance -->
                        <thead>
                            <tr>
                                <th colspan="13">Balance</th>
                            </tr>
                            <tr>
                                <th rowspan="2" width='80px'>Buyer Name</th>
                                <th colspan="4">Confirm</th>
                                <th colspan="4">Projection</th>
                                <th colspan="4">Total</th>
                            </tr>
                            <tr>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                            </tr> 
                        </thead> 
                        <tbody>
                            <?php
                                $i =1;
                                $blnc_gt_qty_conf= $blnc_gt_min_conf =  $blnc_gt_cm_conf = $blnc_gt_fob_conf = $blnc_gt_qty_not_conf =  $blnc_gt_min_not_conf = $blnc_gt_cm_not_conf =  $blnc_gt_fob_not_conf = 0;  
                                foreach( $buyer_wise_data_arr as $b => $v)
                                {
                                    
                                    $comp_qty = $v[1]['BOOKING']['PO_QUANTITY'] - $v[1]['PROD']['PROD_QTY']  ;
                                    $comp_minutes = $v[1]['BOOKING']['MINUTES'] - $v[1]['PROD']['MINUTES']  ;
                                    $comp_cm = $v[1]['BOOKING']['CM'] - $v[1]['PROD']['CM'] ;
                                    $comp_fob = $v[1]['BOOKING']['FOB'] - $v[1]['PROD']['FOB'] ;
                                    $not_comp_qty = $v[2]['BOOKING']['PO_QUANTITY'] - $v[2]['PROD']['PROD_QTY']  ;
                                    $not_comp_minutes = $v[2]['BOOKING']['MINUTES'] - $v[2]['PROD']['MINUTES']  ;
                                    $not_comp_cm = $v[2]['BOOKING']['CM'] - $v[2]['PROD']['CM'] ;
                                    $not_comp_fob = $v[2]['BOOKING']['FOB'] - $v[2]['PROD']['FOB'] ;

                                    $blnc_gt_qty_conf += $comp_qty ;
                                    $blnc_gt_min_conf += $comp_minutes ;
                                    $blnc_gt_cm_conf +=  $comp_cm  ;
                                    $blnc_gt_fob_conf += $comp_fob ;
                                    $blnc_gt_qty_not_conf += $not_comp_qty  ;
                                    $blnc_gt_min_not_conf +=  $not_comp_minutes;
                                    $blnc_gt_cm_not_conf +=  $not_comp_cm ;
                                    $blnc_gt_fob_not_conf +=  $not_comp_fob;
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3rd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_3rd<? echo $i; ?>">
                                        <td width='80px'> <?= $buyer_arr[$b] ?></td>
                                        <td width='80px' align="right"> <a onclick="open_popup('prod_balance_popup','','Production Balance','<?= $b ?>')" href="javascript:void(0)"><?=  $comp_qty  ?> </a> </td>
                                        <td width='80px' align="right"><?= round($comp_minutes) ?></td>
                                        <td width='80px' align="right"><?= round($comp_cm) ?></td> 
                                        <td width='80px' align="right"><?= round($comp_fob) ?></td> 
                                        <td width='80px' align="right"><?= $not_comp_qty ?></td>
                                        <td width='80px' align="right"><?= round($not_comp_minutes) ?></td>
                                        <td width='80px' align="right"><?= round($not_comp_cm) ?></td> 
                                        <td width='80px' align="right"><?= round($not_comp_fob) ?></td>  
                                        <td width='80px' align="right"><?= $comp_qty + $not_comp_qty ?></td>
                                        <td width='80px' align="right"><?= round($comp_minutes + $not_comp_minutes ) ?></td>
                                        <td width='80px' align="right"><?= round($comp_cm + $not_comp_cm ) ?></td>
                                        <td width='80px' align="right"><?= round($comp_fob + $not_comp_fob ) ?></td> 
                                    </tr> 
                            <?php
                                $i++;
                                }
                                ?>
                                <tr class="bg-gray">
                                    <td width='80px'> <b> Total </b> </td>
                                    <td width='80px' align="right"> <b> <?= $blnc_gt_qty_conf ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= round( $blnc_gt_min_conf) ?></b> </td>
                                    <td width='80px' align="right"> <b> <?= round( $blnc_gt_cm_conf) ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= round($blnc_gt_fob_conf) ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= $blnc_gt_qty_not_conf ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= round( $blnc_gt_min_not_conf) ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= round( $blnc_gt_cm_not_conf) ?></b> </td>
                                    <td width='80px' align="right"> <b> <?= round($blnc_gt_fob_not_conf) ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= $blnc_gt_qty_conf + $blnc_gt_qty_not_conf  ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= round($blnc_gt_min_conf + $blnc_gt_min_not_conf ) ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= round($blnc_gt_cm_conf + $blnc_gt_cm_not_conf) ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= round( $blnc_gt_fob_conf + $blnc_gt_fob_not_conf ) ?> </b> </td>
                                </tr>
                        </tbody>
                    </table>
                </fieldset> 
            </div>
        </body> 
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>           
        </html> 
        <?php     
                        // For Buyer Wise Booking Vs Shipmint     
	}
    else if($type == 4) 
    {
        $con_condition = '';
        if( $cbo_lc_company_name !=0 ){
        $con_condition .= "and a.company_name=$cbo_lc_company_name" ;
        }  
        if($cbo_work_company_name !=0 )
        {
        $con_condition .= "and a.working_company_id in($cbo_work_company_name) " ;
        }   
        if($cbo_buyer_name != 0 )
        {
        $con_condition .= "and a.buyer_name in($cbo_buyer_name)" ;
        }    
        if($txt_date_from && $txt_date_to){
        $con_condition .= "and b.pub_shipment_date between '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'" ;
        }  
        //   echo $con_condition;  die;
        $sql = "SELECT a.id,a.avg_unit_price ,(b.po_quantity*a.total_set_qnty) as po_quantity,c.smv_pcs,c.gmts_item_id,b.is_confirmed,a.buyer_name , b.id as po_id FROM wo_po_details_master a,wo_po_break_down b,wo_po_details_mas_set_details c WHERE a.id = b.job_id and a.id = c.job_id and a.status_active=1 and a.is_deleted =0  and b.status_active=1 and b.is_deleted =0 and b.is_confirmed in(1,2) $con_condition ORDER BY pub_shipment_date ASC";  

        // echo $sql; die;
        $result =  sql_select($sql); 
        $data = [];
        $job_id_arr = [];
        $po_arr = [];
        foreach($result  as $res)
        {
            $job_id_arr[$res['ID']] = $res['ID'];
            $po_arr[$res['PO_ID']] = $res['PO_ID'];
            $data[$res['BUYER_NAME']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['PO_QUANTITY'] +=  $res['PO_QUANTITY'];
            $data[$res['BUYER_NAME']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['MINUTE'] +=  $res['PO_QUANTITY'] * $res['SMV_PCS']; 
            $data[$res['BUYER_NAME']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['FOB'] +=  $res['PO_QUANTITY'] * $res['AVG_UNIT_PRICE']; 
            $data[$res['BUYER_NAME']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['SMV_PCS'] += $res['SMV_PCS']; 
            $data[$res['BUYER_NAME']][$res['IS_CONFIRMED']][$res['ID']][$res['PO_ID']][$res['GMTS_ITEM_ID']]['UNIT_PRICE'] += $res['AVG_UNIT_PRICE']; 
        }
        //    print_r($po_arr); 
   
        $con = connect(); 
        execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in(899,999) ");
        oci_commit($con); 

        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 899, 1,$job_id_arr, $empty_arr);
        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 999, 1,$po_arr, $empty_arr); 
        oci_commit($con);   
        $cm_sql =  "SELECT a.job_id,a.cm_cost FROM WO_PRE_COST_DTLS a, GBL_TEMP_ENGINE b WHERE a.job_id = b.ref_val and  b.user_id=$user_id and b.entry_form=899 and  a.status_active=1 and a.is_deleted =0 ";
        $cm_cost_res = sql_select($cm_sql); 
        //    echo $cm_sql; die; 
        $pre_cost_sql =  "SELECT a.job_id, a.costing_per FROM WO_PRE_COST_MST a, GBL_TEMP_ENGINE b WHERE a.job_id = b.ref_val and  b.user_id=$user_id and b.entry_form=899 and a.costing_per >0 and  a.status_active=1 and a.is_deleted =0 ";
        $pre_cost_res = sql_select($pre_cost_sql);   

        //for ex_factory_qnty/Shipment Qty 
        $ex_fact =  "SELECT a.ex_factory_qnty, a.po_break_down_id as po_id FROM pro_ex_factory_mst a, GBL_TEMP_ENGINE b WHERE a.po_break_down_id = b.ref_val and  b.user_id=$user_id and b.entry_form=999 and  a.status_active=1 and a.is_deleted =0";
        $ex_fact_res = sql_select($ex_fact); 
        //    echo $ex_fact_res;die;
        execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in(899,999) ");
        oci_commit($con); 
   
        // print_r($po_arr) ;die;
   
        $cm_cost_arr=[];
        foreach($cm_cost_res as   $v){
            $cm_cost_arr[$v['JOB_ID']] = $v['CM_COST'];
        }
            //  for shipment qty
        $ex_fac_arr=[];
        foreach($ex_fact_res as   $v)
        {
            $ex_fac_arr[$v['PO_ID']] += $v['EX_FACTORY_QNTY'];
        }
        $pre_cost_arr=[];
        foreach($pre_cost_res as   $v)
        { 
            $pre_cost_arr[$v['JOB_ID']] += $v['COSTING_PER'];
        } 
        /*   echo "<pre>";
        print_r( $data) ;
        die;  */
        $buyer_wise_data_arr = [] ; 
        foreach($data as $buyer => $m ){
            foreach ($m as $is_confirmed => $c){
                foreach ($c as $job_id =>$job){ 
                    foreach ($job as $po_id =>$po){
                        foreach ($po as $item => $v){
                            $ex_fac_qty = $ex_fac_arr[$po_id];
                            $cm_val =  $cm_cost_arr[$job_id] ;
                            $po_qty=  $v['PO_QUANTITY'] ;
                            $cost_per= $pre_cost_arr[$job_id] ; 
                            $booking_cm =  (is_divideable($cost_per))?  ($po_qty *( $cm_val / $cost_per)) : 0;
                            $shipment_cm = (is_divideable($cost_per))?($ex_fac_qty *( $cm_val / $cost_per)) : 0;
                            // for booking
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['BOOKING']['PO_QUANTITY'] += $v['PO_QUANTITY'];  
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['BOOKING']['MINUTES'] += $v['MINUTE'];  
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['BOOKING']['FOB'] += $v['FOB'];  
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['BOOKING']['CM'] +=  $booking_cm  ;  
                            // for shipment
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['SHIPMENT']['EX_FACTORY_QNTY'] += $ex_fac_qty;  
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['SHIPMENT']['MINUTES'] += $ex_fac_qty *  $v['SMV_PCS'] ;  
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['SHIPMENT']['FOB'] +=  $ex_fac_qty * $v['UNIT_PRICE'];  
                            $buyer_wise_data_arr[$buyer][$is_confirmed]['SHIPMENT']['CM'] += $shipment_cm ;  
                        } 
                    }
                }
            } 
        }     
      ?>
        <style>
            #report_table tbody{
                background: #fff;
            }
            .bg-gray{
                background: #dccdcd;
            }
        </style>
        <body>
            <div align="center"  id="scroll_body"> 
                <div style="margin-bottom:20px">
                    <h3>Buyer Wise Order Booking Vs Shipment</h3>
                    <p>Company Name : <span id='Company Name'> <?= $company_arr[$company_id] ?></span></p>
                    <p>Date Range : <span id='data_range'> <?=  $form_date ." To ".  $to_date ?> </span></p>
                </div>
                <fieldset style="width:1200px;"> 
                    <table width="1200" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="report_table">
                        <!-- Booking -->
                        <thead>
                            <tr>
                                <th colspan="13" >Booking</th>
                            </tr>
                            <tr>
                                <th rowspan="2" width='80px'>Buyer Name</th>
                                <th colspan="4" >Confirm</th>
                                <th colspan="4">Projection</th>
                                <th colspan="4">Total</th>
                            </tr> 
                            <tr>
                                <th  width='80px'>Qty</th>
                                <th  width='80px'>Minute</th>
                                <th  width='80px'>CM</th>
                                <th  width='80px'>Value</th>
                                <th  width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                            </tr> 
                        </thead> 
                        <tbody>
                            <?php  
                                $i= 0;
                                $gt_qty_conf= $gt_min_conf =  $gt_cm_conf = $gt_fob_conf = $gt_qty_not_conf =  $gt_min_not_conf = $gt_cm_not_conf =  $gt_fob_not_conf = 0;  
                                foreach( $buyer_wise_data_arr as $b => $v)
                                {  
                                    // echo "<pre>";
                                    // print_r( $v);
                                    // die;
                                    $gt_qty_conf += $v[1]['BOOKING']['PO_QUANTITY'];
                                    $gt_min_conf += $v[1]['BOOKING']['MINUTES'];
                                    $gt_cm_conf += $v[1]['BOOKING']['CM'];
                                    $gt_fob_conf += $v[1]['BOOKING']['FOB'];

                                    $gt_qty_not_conf += $v[2]['BOOKING']['PO_QUANTITY']; 
                                    $gt_min_not_conf += $v[2]['BOOKING']['MINUTES']; 
                                    $gt_cm_not_conf += $v[2]['BOOKING']['CM']; 
                                    $gt_fob_not_conf += $v[2]['BOOKING']['FOB']; 

                                    $total_po_qty =  $v[1]['BOOKING']['PO_QUANTITY'] + $v[2]['BOOKING']['PO_QUANTITY'];
                                    $total_minutes =  $v[1]['BOOKING']['MINUTES'] + $v[2]['BOOKING']['MINUTES'];
                                    $total_cm =  $v[1]['BOOKING']['CM'] + $v[2]['BOOKING']['CM'];
                                    $total_fob =  $v[1]['BOOKING']['FOB'] + $v[2]['BOOKING']['FOB'];
                            ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1st<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1st<? echo $i; ?>">
                                        <td width='80px'><?= $buyer_arr[$b] ?></td>
                                        <td width='80px' align="right"><a onclick="open_popup('booking_qty_popup','','Booking Quantity','<?= $b ?>')" href="javascript:void(0)"><?= $v[1]['BOOKING']['PO_QUANTITY']  ?></a></td>
                                        <td width='80px' align="right"><?= round($v[1]['BOOKING']['MINUTES'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[1]['BOOKING']['CM'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[1]['BOOKING']['FOB'])  ?></td>
                                        <td width='80px' align="right"><?= $v[2]['BOOKING']['PO_QUANTITY']  ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['BOOKING']['MINUTES']) ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['BOOKING']['CM'])  ?></td>
                                        <td width='80px' align="right"><?= round($v[2]['BOOKING']['FOB'])  ?></td>
                                        <td width='80px' align="right"><?= $total_po_qty ?></td>
                                        <td width='80px' align="right"><?= round($total_minutes) ?></td>
                                        <td width='80px' align="right"><?= round($total_cm) ?></td>
                                        <td width='80px' align="right"><?= round($total_fob)  ?></td>
                                    </tr>
                                <?php
                                $i++;
                                }
                                // echo '<pre>';
                                // print_r($gt);
                                // die;
                            ?> 
                                <tr class="bg-gray">
                                    <td width='80px'> <b> Total </b> </td>
                                    <td width='80px' align="right"> <b> <?= $gt_qty_conf ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= round( $gt_min_conf) ?></b> </td>
                                    <td width='80px' align="right"> <b> <?= round( $gt_cm_conf) ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= round($gt_fob_conf) ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= $gt_qty_not_conf ?> </b> </td>
                                    <td width='80px' align="right"> <b><?= round( $gt_min_not_conf) ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= round( $gt_cm_not_conf) ?></b> </td>
                                    <td width='80px' align="right"> <b> <?= round($gt_fob_not_conf) ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= $gt_qty_conf +  $gt_qty_not_conf ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= round($gt_min_conf +  $gt_min_not_conf ) ?> </b> </td>
                                    <td width='80px' align="right"> <b> <?= round($gt_cm_conf +  $gt_cm_not_conf ) ?> </b> </td>
                                    <td width='80px' align="right"> <b>  <?= round( $gt_fob_conf + $gt_fob_not_conf) ?> </b> </td>
                                </tr>
                        </tbody>
                        <!-- Shipment -->
                        <thead>
                            <tr>
                                <th colspan="13">Shipment</th>
                            </tr>
                            <tr>
                                <th rowspan="2" width='80px'>Buyer Name</th>
                                <th colspan="4">Confirm</th>
                                <th colspan="4">Projection</th>
                                <th colspan="4">Total</th>
                            </tr>
                            <tr>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                            </tr> 
                        </thead> 
                        <tbody>
                            <?php
                                $i = 1;  
                                $ship_gt_qty_conf= $ship_gt_min_conf =  $ship_gt_cm_conf = $ship_gt_fob_conf = $ship_gt_qty_not_conf =  $ship_gt_min_not_conf = $ship_gt_cm_not_conf =  $ship_gt_fob_not_conf = 0;  
                                foreach( $buyer_wise_data_arr as $b => $v)
                                {  
                                    // print_r($v);
                                //     echo  $v[1]['BOOKING']['FOB'];
                                //   die;
                                    $ship_gt_qty_conf += $v[1]['SHIPMENT']['EX_FACTORY_QNTY'];
                                    $ship_gt_min_conf += $v[1]['SHIPMENT']['MINUTES'];
                                    $ship_gt_cm_conf += $v[1]['SHIPMENT']['CM'];
                                    $ship_gt_fob_conf += $v[1]['SHIPMENT']['FOB'];

                                    $ship_gt_qty_not_conf += $v[2]['SHIPMENT']['EX_FACTORY_QNTY']; 
                                    $ship_gt_min_not_conf += $v[2]['SHIPMENT']['MINUTES']; 
                                    $ship_gt_cm_not_conf += $v[2]['SHIPMENT']['CM']; 
                                    $ship_gt_fob_not_conf += $v[2]['SHIPMENT']['FOB']; 

                                    $ship_total_po_qty =  $v[1]['SHIPMENT']['EX_FACTORY_QNTY'] + $v[2]['SHIPMENT']['EX_FACTORY_QNTY'];
                                    $ship_total_minutes =  $v[1]['SHIPMENT']['MINUTES'] + $v[2]['SHIPMENT']['MINUTES'];
                                    $ship_total_cm =  $v[1]['SHIPMENT']['CM'] + $v[2]['SHIPMENT']['CM'];
                                    $ship_total_fob =  $v[1]['SHIPMENT']['FOB'] + $v[2]['SHIPMENT']['FOB'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                <td width='80px'> <?= $buyer_arr[$b]  ?> </td>
                                <td width='80px' align="right"> <a onclick="open_popup('shipment_qty_popup','','Shipment Quantity'),'<?= $b ?>'" href="javascript:void(0)"><?= $v[1]['SHIPMENT']['EX_FACTORY_QNTY']   ?> </a></td>
                                <td width='80px' align="right"> <?= round($v[1]['SHIPMENT']['MINUTES'])  ?> </td>
                                <td width='80px' align="right"> <?= round($v[1]['SHIPMENT']['CM'])  ?> </td>
                                <td width='80px' align="right"> <?= round($v[1]['SHIPMENT']['FOB'])  ?> </td>
                                <td width='80px' align="right"> <?= $v[2]['SHIPMENT']['EX_FACTORY_QNTY']  ?> </td>
                                <td width='80px' align="right"> <?= round($v[2]['SHIPMENT']['MINUTES']) ?> </td>
                                <td width='80px' align="right"> <?= round($v[2]['SHIPMENT']['CM'])  ?> </td>
                                <td width='80px' align="right"> <?= round($v[2]['SHIPMENT']['FOB'])  ?> </td>
                                <td width='80px' align="right"> <?= $ship_total_po_qty ?> </td>
                                <td width='80px' align="right"> <?= round($ship_total_minutes) ?> </td>
                                <td width='80px' align="right"> <?= round($ship_total_cm) ?> </td>
                                <td width='80px' align="right"> <?= round($ship_total_fob)  ?> </td>
                            </tr>
                            <?php
                            $i++;
                                }
                                // echo '<pre>';
                                // print_r($gt);
                                // die;
                            ?> 
                            <tr class="bg-gray">
                                <td width='80px'> <b> Total </b> </td>
                                <td width='80px' align="right"> <b> <?= $ship_gt_qty_conf ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $ship_gt_min_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round( $ship_gt_cm_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($ship_gt_fob_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $ship_gt_qty_not_conf ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $ship_gt_min_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $ship_gt_cm_not_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round($ship_gt_fob_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $ship_gt_qty_conf + $ship_gt_qty_not_conf  ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($ship_gt_min_conf +  $ship_gt_min_not_conf ) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($ship_gt_cm_conf +  $ship_gt_cm_not_conf ) ?> </b> </td>
                                <td width='80px' align="right"> <b>  <?= round( $ship_gt_fob_conf + $ship_gt_fob_not_conf) ?> </b> </td>
                            </tr>
                        </tbody>
                        <!-- balance -->
                        <thead>
                            <tr>
                                <th colspan="13">Balance</th>
                            </tr>
                            <tr>
                                <th rowspan="2" width='80px'>Buyer Name</th>
                                <th colspan="4">Confirm</th>
                                <th colspan="4">Projection</th>
                                <th colspan="4">Total</th>
                            </tr>
                            <tr>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                                <th width='80px'>Qty</th>
                                <th width='80px'>Minute</th>
                                <th width='80px'>CM</th>
                                <th width='80px'>Value</th>
                            </tr> 
                        </thead> 
                        <tbody>
                            <?php
                                $i=1;
                                $blnc_gt_qty_conf= $blnc_gt_min_conf =  $blnc_gt_cm_conf = $blnc_gt_fob_conf = $blnc_gt_qty_not_conf =  $blnc_gt_min_not_conf = $blnc_gt_cm_not_conf =  $blnc_gt_fob_not_conf = 0;  
                                foreach( $buyer_wise_data_arr as $b => $v)
                                {
                                    
                                    $comp_qty = $v[1]['BOOKING']['PO_QUANTITY'] - $v[1]['SHIPMENT']['EX_FACTORY_QNTY']  ;
                                    $comp_minutes = $v[1]['BOOKING']['MINUTES'] - $v[1]['SHIPMENT']['MINUTES']  ;
                                    $comp_cm = $v[1]['BOOKING']['CM'] - $v[1]['SHIPMENT']['CM'] ;
                                    $comp_fob = $v[1]['BOOKING']['FOB'] - $v[1]['SHIPMENT']['FOB'] ;
                                    $not_comp_qty = $v[2]['BOOKING']['PO_QUANTITY'] - $v[2]['SHIPMENT']['EX_FACTORY_QNTY']  ;
                                    $not_comp_minutes = $v[2]['BOOKING']['MINUTES'] - $v[2]['SHIPMENT']['MINUTES']  ;
                                    $not_comp_cm = $v[2]['BOOKING']['CM'] - $v[2]['SHIPMENT']['CM'] ;
                                    $not_comp_fob = $v[2]['BOOKING']['FOB'] - $v[2]['SHIPMENT']['FOB'] ;

                                    $blnc_gt_qty_conf += $comp_qty ;
                                    $blnc_gt_min_conf += $comp_minutes ;
                                    $blnc_gt_cm_conf +=  $comp_cm  ;
                                    $blnc_gt_fob_conf += $comp_fob ;
                                    $blnc_gt_qty_not_conf += $not_comp_qty  ;
                                    $blnc_gt_min_not_conf +=  $not_comp_minutes;
                                    $blnc_gt_cm_not_conf +=  $not_comp_cm ;
                                    $blnc_gt_fob_not_conf +=  $not_comp_fob;
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3rd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_3rd<? echo $i; ?>">
                                <td width='80px'><?= $buyer_arr[$b] ?></td>
                                <td width='80px' align="right"><a onclick="open_popup('shipment_balance_popup','','Shipment Balance','<?= $b ?>')" href="javascript:void(0)"><?= $comp_qty ?></a></td>
                                <td width='80px' align="right"><?= round($comp_minutes) ?></td>
                                <td width='80px' align="right"><?= round($comp_cm) ?></td> 
                                <td width='80px' align="right"><?= round($comp_fob) ?></td> 
                                <td width='80px' align="right"><?= $not_comp_qty ?></td>
                                <td width='80px' align="right"><?= round($not_comp_minutes) ?></td>
                                <td width='80px' align="right"><?= round($not_comp_cm) ?></td> 
                                <td width='80px' align="right"><?= round($not_comp_fob) ?></td>  
                                <td width='80px' align="right"><?= $comp_qty + $not_comp_qty ?></td>
                                <td width='80px' align="right"><?= round($comp_minutes + $not_comp_minutes ) ?></td>
                                <td width='80px' align="right"><?= round($comp_cm + $not_comp_cm ) ?></td>
                                <td width='80px' align="right"><?= round($comp_fob + $not_comp_fob ) ?></td> 
                            </tr> 
                            <?php
                            $i++;
                                }
                            ?>
                            <tr class="bg-gray">
                                <td width='80px'> <b> Total </b> </td>
                                <td width='80px' align="right"> <b> <?= $blnc_gt_qty_conf ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $blnc_gt_min_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round( $blnc_gt_cm_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($blnc_gt_fob_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $blnc_gt_qty_not_conf ?> </b> </td>
                                <td width='80px' align="right"> <b><?= round( $blnc_gt_min_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round( $blnc_gt_cm_not_conf) ?></b> </td>
                                <td width='80px' align="right"> <b> <?= round($blnc_gt_fob_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= $blnc_gt_qty_conf + $blnc_gt_qty_not_conf  ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($blnc_gt_min_conf + $blnc_gt_min_not_conf ) ?> </b> </td>
                                <td width='80px' align="right"> <b> <?= round($blnc_gt_cm_conf + $blnc_gt_cm_not_conf) ?> </b> </td>
                                <td width='80px' align="right"> <b>  <?= round( $blnc_gt_fob_conf + $blnc_gt_fob_not_conf ) ?> </b> </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset> 
            </div>
        </body> 
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>           
        </html> 
        <?php        
	}  
}  
    foreach (glob($user_id."_*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$is_created = fwrite($create_new_excel,ob_get_contents());
	//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	echo "####".$name;
	exit();
?>  