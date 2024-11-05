<?
session_start();
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.washes.php');

extract($_REQUEST);
if ($_SESSION['logic_erp']['user_id'] == "") 
{
    header("location:login.php");
    die;
}

if($action=="print_button_variable_setting")
{
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=11 and report_id=72 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit(); 
}

if ($action == "load_drop_down_buyer") 
{
    echo create_drop_down("cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/shipment_date_wise_wp_report_controller', this.value, 'load_drop_down_season', 'season_td');");
    exit();
}

$buyer_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
$company_short_name_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
$imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where file_type=1", 'master_tble_id', 'image_location');
//$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");

$SqlResult = sql_select("select id, team_member_name,member_contact_no from lib_mkt_team_member_info");
foreach ($SqlResult as $row) 
{
    if ($row[csf('member_contact_no')]) 
    {
        $phone = "<br>Ph:" . $row[csf('member_contact_no')];
    } 
    else 
    {
        $phone = "";
    }
    $dealing_merchant_array[$row[csf('id')]] = $row[csf('team_member_name')] . $phone;
}

if ($action == "report_generate_without_header") 
{
    $company_name   = str_replace("'", "", $cbo_company_name);
    $buyer_name     = str_replace("'", "", $cbo_buyer_name);
    $year_id        = str_replace("'", "", $cbo_year);    
    $job_no         = str_replace("'", "", $txt_job_no);
    $txt_style_ref  = str_replace("'", "", $txt_style_ref);
    $txt_order_no   = str_replace("'", "", $txt_order_no);
    $shipmentStatus= str_replace("'", "", $cbo_shipment_status);
    $orderStatus   = str_replace("'","", $cbo_order_status);
    $status         = str_replace("'", "", $cbo_status);
    $report_type    = str_replace("'", "", $cbo_report_type);
	$date_category  = str_replace("'", "", $cbo_date_category);    

    /*===============================================================================/
    /                              Create Library Array                              /
    /============================================================================== */
    $team_library   = return_library_array("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0", "id", "team_leader_name");
    $merchant_library   = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0", "id", "team_member_name");
    $country_library= return_library_array("select id, country_name from  lib_country", "id", "country_name");
    $buyer_library  = return_library_array("select id, buyer_name from  lib_buyer", "id", "buyer_name");
    $color_library  = return_library_array("select id, color_name from  lib_color", "id", "color_name");
    $size_library   = return_library_array("select id, size_name from  lib_size", "id", "size_name");
    $company_library= return_library_array("select id, company_name from  lib_company", "id", "company_name");
    $sub_dep_library= return_library_array("select id,sub_department_name from lib_pro_sub_deparatment status_active =1 and is_deleted=0", "id", "sub_department_name");
    $season_library = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", "id", "season_name");
    $brand_library = return_library_array("select id, brand_name from lib_buyer_brand brand where status_active =1 and is_deleted=0", "id", "brand_name");
    $sub_dep_library = return_library_array("select id,sub_department_name from lib_pro_sub_deparatment where status_active =1 and is_deleted=0", "id", "sub_department_name");
    $user_library = return_library_array("select id,user_full_name from user_passwd where status_active =1 and is_deleted=0", "id", "user_full_name");
    $bank_library = return_library_array("select bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1", "id", "bank_name");

    $fab_material=array(1=>"Organic",2=>"BCI");

    /*===============================================================================/
    /                                   Query Condition                              /
    /============================================================================== */
    $sqlCond = "";
    $sqlCond = ($company_name) ? " and a.company_name=$company_name" : "";
    $sqlCond .= ($buyer_name) ? " and a.buyer_name=$buyer_name" : "";
    $sqlCond .= ($year_id) ? " and to_char(a.insert_date,'YYYY')=$year_id" : "";
    $sqlCond .= ($job_no!="") ? " and a.job_no_prefix_num=$job_no" : "";
    $sqlCond .= ($txt_style_ref!="") ? " and a.style_ref_no='$txt_style_ref'" : "";
    $sqlCond .= ($txt_order_no!="") ? " and b.po_number='$txt_order_no'" : "";
    $sqlCond .= ($orderStatus) ? " and b.is_confirmed=$orderStatus" : "";
    $sqlCond .= ($status) ? " and b.status_active=$status" : "";

    if($shipmentStatus)
    {
        if ($shipmentStatus==4) 
        {
            $sqlCond .= " and b.shiping_status in(1,2)";
        }
        else
        {
            $sqlCond .= " and b.shiping_status=$shipmentStatus";
        }
    }

    if(str_replace("'", "", $txt_date_from) !="" && str_replace("'", "", $txt_date_to) !="")
    {
        switch ($date_category) 
        {
            case 1:
                $sqlCond .= " and b.shipment_date between $txt_date_from and $txt_date_to";
                break;

            case 2:
                $sqlCond .= " and b.shipment_date between $txt_date_from and $txt_date_to";
                break;    
            
            default:
                $sqlCond .= " and b.factory_received_date between $txt_date_from and $txt_date_to";
                break;
        }
    }

    /*===============================================================================/
    /                                  MAIN QUERY                                    /
    /============================================================================== */
    $sql = "SELECT a.id,a.job_no, a.company_name,a.style_owner,a.buyer_name,a.style_ref_no,a.requisition_no,a.style_description,a.repeat_job_no,a.season_buyer_wise,a.product_dept,a.product_code,a.pro_sub_dep,a.order_uom,  a.product_category,a.brand_id,a.region,a.sustainability_standard,a.fab_material,a.qlty_label,a.quality_level,(a.job_quantity*a.total_set_qnty) as job_qty_pcs,    to_char(a.insert_date,'YYYY') as year,a.ship_mode,a.set_smv,a.team_leader,a.dealing_marchant,a.factory_marchant,a.inserted_by,a.total_price,a.qlty_label,

        b.is_confirmed,b.id as po_id,b.po_number,b.excess_cut,(b.po_quantity*a.total_set_qnty) as po_quantity,b.unit_price,b.po_total_price,to_char(b.insert_date,'DD-MM-YYYY') as po_insert_date,to_char(b.po_received_date,'DD-MM-YYYY') as po_received_date,to_char(b.factory_received_date,'DD-MM-YYYY') as factory_received_date,to_char(b.pub_shipment_date,'DD-MM-YYYY') as pub_shipment_date,to_char(b.shipment_date,'DD-MM-YYYY') as shipment_date,to_char(b.txt_etd_ldd,'DD-MM-YYYY') as txt_etd_ldd,b.shiping_status,(b.pub_shipment_date-b.po_received_date) as lead_time,(b.pub_shipment_date - trunc(sysdate)) AS days_in_hand,

        c.country_id,c.item_number_id,c.color_number_id,c.size_number_id,c.order_quantity,c.plan_cut_qnty,to_char(c.cutup_date,'DD-MM-YYYY') as cutup_date,to_char(c.country_ship_date,'DD-MM-YYYY') as country_ship_date

        from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 $sqlCond";
    // echo $sql;die();
    $sql_res = sql_select($sql);
    if(count($sql_res)==0)
    {
        ?>
        <center>
            <div style="width: 80%;" class="alert alert-danger">Data not found.Please try again.</div>
        </center>
        <?
        die();
    }
    $data_array = array();
    $job_id_array = array();
    $job_no_array = array();
    $po_array = array();
    $po_wise_data = array();
    $po_wise_job_arr = array();
    $po_chk_array = array();
    $po_wise_unit_price_array = array();
    foreach ($sql_res as $val) 
    {
        $data_array[$val['JOB_NO']]['company_name'] = $val['COMPANY_NAME'];
        $data_array[$val['JOB_NO']]['buyer_name'] = $val['BUYER_NAME'];
        $data_array[$val['JOB_NO']]['wo_com_id'] = $val['STYLE_OWNER'];
        $data_array[$val['JOB_NO']]['year'] = $val['YEAR'];
        $data_array[$val['JOB_NO']]['style'] = $val['STYLE_REF_NO'];
        $data_array[$val['JOB_NO']]['req_no'] = $val['REQUISITION_NO'];
        $data_array[$val['JOB_NO']]['repeat_job_no'] = $val['REPEAT_JOB_NO'];
        $data_array[$val['JOB_NO']]['style_des'] = $val['STYLE_DESCRIPTION'];
        $data_array[$val['JOB_NO']]['season'] = $val['SEASON_BUYER_WISE'];
        $data_array[$val['JOB_NO']]['product_dept'] = $val['PRODUCT_DEPT'];
        $data_array[$val['JOB_NO']]['product_code'] = $val['PRODUCT_CODE'];
        $data_array[$val['JOB_NO']]['pro_sub_dep'] = $val['PRO_SUB_DEP'];
        $data_array[$val['JOB_NO']]['product_category'] = $val['PRODUCT_CATEGORY'];
        $data_array[$val['JOB_NO']]['brand_id'] = $val['BRAND_ID'];
        $data_array[$val['JOB_NO']]['region'] = $val['REGION'];
        $data_array[$val['JOB_NO']]['sustainability_standard'] = $val['SUSTAINABILITY_STANDARD'];
        $data_array[$val['JOB_NO']]['fab_material'] = $val['FAB_MATERIAL'];
        $data_array[$val['JOB_NO']]['order_nature'] = $val['QLTY_LABEL'];
        $data_array[$val['JOB_NO']]['quality_level'] = $val['QUALITY_LEVEL'];
        $data_array[$val['JOB_NO']]['qlty_label'] = $val['QLTY_LABEL'];
        $data_array[$val['JOB_NO']]['job_qty_pcs'] = $val['JOB_QTY_PCS'];
        $data_array[$val['JOB_NO']]['order_uom'] = $val['ORDER_UOM'];
        $data_array[$val['JOB_NO']]['ship_mode'] = $val['SHIP_MODE'];
        $data_array[$val['JOB_NO']]['set_smv'] = $val['SET_SMV'];
        $data_array[$val['JOB_NO']]['team_leader'] = $val['TEAM_LEADER'];
        $data_array[$val['JOB_NO']]['dealing_marchant'] = $val['DEALING_MARCHANT'];
        $data_array[$val['JOB_NO']]['factory_marchant'] = $val['FACTORY_MARCHANT'];
        $data_array[$val['JOB_NO']]['inserted_by'] = $val['INSERTED_BY'];
        $data_array[$val['JOB_NO']]['total_price'] = $val['TOTAL_PRICE'];

        $data_array[$val['JOB_NO']]['po_number'] .= $val['PO_NUMBER'].",";
        $data_array[$val['JOB_NO']]['order_status'] .= $val['IS_CONFIRMED'].",";
        $po_wise_data[$val['JOB_NO']]['excess_cut'] .= $val['PO_NUMBER']."=".$val['EXCESS_CUT']."**";

        $data_array[$val['JOB_NO']]['country_id'] .= $val['COUNTRY_ID'].",";
        $data_array[$val['JOB_NO']]['item_id'] .= $val['ITEM_NUMBER_ID'].",";
        $data_array[$val['JOB_NO']]['color_id'] .= $val['COLOR_NUMBER_ID'].",";
        $data_array[$val['JOB_NO']]['size_id'] .= $val['SIZE_NUMBER_ID'].",";
        $data_array[$val['JOB_NO']]['order_quantity'] += $val['ORDER_QUANTITY'];
        $data_array[$val['JOB_NO']]['plan_cut_qnty'] += $val['PLAN_CUT_QNTY'];
        if(!in_array($val['PO_ID'], $po_chk_array))
        {
            $data_array[$val['JOB_NO']]['po_quantity'] += $val['PO_QUANTITY'];
            $data_array[$val['JOB_NO']]['order_value'] += $val['PO_TOTAL_PRICE'];
            $data_array[$val['JOB_NO']]['unit_price'] .= ($data_array[$val['JOB_NO']]['unit_price']=="") ? $val['UNIT_PRICE'] : ", ".$val['UNIT_PRICE'];
            $data_array[$val['JOB_NO']]['po_insert_date'] .= ($data_array[$val['JOB_NO']]['po_insert_date']=="") ? $val['PO_INSERT_DATE'] : ", ".$val['PO_INSERT_DATE'];
            $data_array[$val['JOB_NO']]['po_received_date'] .= ($data_array[$val['JOB_NO']]['po_received_date']=="") ? $val['PO_RECEIVED_DATE'] : ", ".$val['PO_RECEIVED_DATE'];
            $data_array[$val['JOB_NO']]['factory_received_date'] .= ($data_array[$val['JOB_NO']]['factory_received_date']=="") ? $val['FACTORY_RECEIVED_DATE'] : ", ".$val['FACTORY_RECEIVED_DATE'];
            $data_array[$val['JOB_NO']]['pub_shipment_date'] .= ($data_array[$val['JOB_NO']]['pub_shipment_date']=="") ? $val['PUB_SHIPMENT_DATE'] : ", ".$val['PUB_SHIPMENT_DATE'];
            $data_array[$val['JOB_NO']]['shipment_date'] .= ($data_array[$val['JOB_NO']]['shipment_date']=="") ? $val['SHIPMENT_DATE'] : ", ".$val['SHIPMENT_DATE'];
            $data_array[$val['JOB_NO']]['txt_etd_ldd'] .= ($data_array[$val['JOB_NO']]['txt_etd_ldd']=="") ? $val['TXT_ETD_LDD'] : ", ".$val['TXT_ETD_LDD'];
            $data_array[$val['JOB_NO']]['shipment_status'] .= ($data_array[$val['JOB_NO']]['shipment_status']=="") ? $shipment_status[$val['SHIPING_STATUS']] : ", ".$shipment_status[$val['SHIPING_STATUS']];
            $data_array[$val['JOB_NO']]['lead_time'] .= ($data_array[$val['JOB_NO']]['lead_time']=="") ? $val['LEAD_TIME'] : ", ".$val['LEAD_TIME'];
            $data_array[$val['JOB_NO']]['days_in_hand'] .= ($data_array[$val['JOB_NO']]['days_in_hand']=="") ? $val['DAYS_IN_HAND'] : ", ".$val['DAYS_IN_HAND'];

            $po_chk_array[$val['PO_ID']] = $val['PO_ID'];
        }
        $data_array[$val['JOB_NO']]['cutup_date'] .= ($data_array[$val['JOB_NO']]['cutup_date']=="") ? $val['CUTUP_DATE'] : ",".$val['CUTUP_DATE'];
        $data_array[$val['JOB_NO']]['country_ship_date'] .= ($data_array[$val['JOB_NO']]['country_ship_date']=="") ? $val['COUNTRY_SHIP_DATE'] : ",".$val['COUNTRY_SHIP_DATE'];


        $job_id_array[$val['ID']] = $val['ID'];
        $job_no_array[$val['JOB_NO']] = $val['JOB_NO'];
        $po_array[$val['PO_ID']] = $val['PO_ID'];
        $po_wise_job_arr[$val['PO_ID']] = $val['JOB_NO'];
        $po_wise_unit_price_array[$val['PO_ID']] = $val['UNIT_PRICE'];
    }

    unset($sql_res);
    // echo "<pre>";print_r($po_array);echo "</pre>";die();
    /*===============================================================================/
    /                                  Job Image                                     /
    /============================================================================== */
    $job_cond = where_con_using_array($job_no_array,1,"master_tble_id");

    $imge_arr = return_library_array("SELECT master_tble_id,image_location from common_photo_library where file_type=1 $job_cond", 'master_tble_id', 'image_location');

    /*===============================================================================/
    /                                  Embel Name                                    /
    /============================================================================== */
    $job_cond = where_con_using_array($job_id_array,1,"job_id");
    $sql = "SELECT job_no,emb_name from wo_pre_cost_embe_cost_dtls where status_active=1 and is_deleted=0 and emb_type!=0 $job_cond";
    // echo $sql;
    $res = sql_select($sql);
    $emb_name_arr = array();
    $emb_id_chk_arr = array();
    foreach ($res as $val) 
    {
        if(!in_array($val['EMB_NAME'], $emb_id_chk_arr[$val['JOB_NO']]))
        {
            $emb_name_arr[$val['JOB_NO']] .= ($emb_name_arr[$val['JOB_NO']]=="") ? $emblishment_name_array[$val['EMB_NAME']] : ",".$emblishment_name_array[$val['EMB_NAME']];
            $emb_id_chk_arr[$val['JOB_NO']][$val['EMB_NAME']] = $val['EMB_NAME'];
        }
    }

    /*===============================================================================/
    /                                Conversion Name                                 /
    /============================================================================== */
    $job_cond = where_con_using_array($job_id_array,1,"job_id");
    $sql = "SELECT job_no,cons_process from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 $job_cond";
    // echo $sql;
    $res = sql_select($sql);
    $conv_name_arr = array();
    $conv_id_chk_arr = array();
    foreach ($res as $val) 
    {
        if(!in_array($val['CONS_PROCESS'], $conv_id_chk_arr[$val['JOB_NO']]))
        {
            $conv_name_arr[$val['JOB_NO']] .= ($conv_name_arr[$val['JOB_NO']]=="") ? $conversion_cost_head_array[$val['CONS_PROCESS']] : ",".$conversion_cost_head_array[$val['CONS_PROCESS']];
            $conv_id_chk_arr[$val['JOB_NO']][$val['CONS_PROCESS']] = $val['CONS_PROCESS'];
        }
    }
    // echo "<pre>";print_r($conv_id_chk_arr);die();
    /*===============================================================================/
    /                                  ACTUAL PO                                     /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"po_break_down_id");
    $sql = "SELECT job_no, acc_po_no FROM wo_po_acc_po_info where status_active=1 and is_deleted=0 $po_cond";
    $res = sql_select($sql);
    $acc_po_array = array();
    $acc_po_chk = array();
    foreach ($res as $key => $val) 
    {
        if(!in_array($val['ACC_PO_NO'], $acc_po_chk[$val['JOB_NO']]))
        {
            $acc_po_array[$val['JOB_NO']]['acc_po'] .= ($acc_po_array[$val['JOB_NO']]['acc_po']=="") ? $val['ACC_PO_NO'] : ", ".$val['ACC_PO_NO'];
            $acc_po_chk[$val['JOB_NO']][$val['ACC_PO_NO']] = $val['ACC_PO_NO'];
        }
    }

    /*===============================================================================/
    /                                  Booking Data                                  /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"b.po_break_down_id");
    $sql = "SELECT a.entry_form,a.process, b.job_no,a.item_category,to_char(a.booking_date,'DD-MM-YYYY') as booking_date,to_char(a.delivery_date,'DD-MM-YYYY') as delivery_date,a.booking_no,a.booking_no_prefix_num, b.po_break_down_id as po_id,a.booking_type,a.fabric_source,a.is_short,a.revised_no, b.fin_fab_qnty,b.grey_fab_qnty,a.is_approved,b.dia_width,b.gsm_weight,b.process_loss_percent,b.construction,b.copmposition,a.uom from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $booking_data_array = array();
    $pur_booking_data_array = array();
    $service_booking_data_array = array();
    $booking_qty_array = array();
    foreach ($res as $val) 
    {
        if($val['ITEM_CATEGORY']==2)
        {
            if($val['FABRIC_SOURCE']==1)
            {
            
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['booking_date'] .= $val['BOOKING_DATE'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['booking_no'] .= $val['BOOKING_NO'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['uom'] .= $val['UOM'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['delivery_date'] .= $val['DELIVERY_DATE'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['revised_no'] .= $val['REVISED_NO'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['is_approved'] .= $val['IS_APPROVED'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['dia_width'] .= $val['DIA_WIDTH'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['gsm_weight'] .= $val['GSM_WEIGHT'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['process_loss'] .= $val['PROCESS_LOSS_PERCENT'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['construction'] .= $val['CONSTRUCTION']."__";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['copmposition'] .= $val['COPMPOSITION']."__";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['fin_fab_qnty'] += $val['FIN_FAB_QNTY'];
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['grey_fab_qnty'] += $val['GREY_FAB_QNTY'];
            }
            else
            {
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['booking_date'] .= $val['BOOKING_DATE'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['booking_no'] .= $val['BOOKING_NO'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['uom'] .= $val['UOM'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['delivery_date'] .= $val['DELIVERY_DATE'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['revised_no'] .= $val['REVISED_NO'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['is_approved'] .= $val['IS_APPROVED'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['dia_width'] .= $val['DIA_WIDTH'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['gsm_weight'] .= $val['GSM_WEIGHT'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['process_loss'] .= $val['PROCESS_LOSS_PERCENT'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['construction'] .= $val['CONSTRUCTION']."__";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['copmposition'] .= $val['COPMPOSITION']."__";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['fin_fab_qnty'] += $val['FIN_FAB_QNTY'];
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['grey_fab_qnty'] += $val['GREY_FAB_QNTY'];
            }
            $booking_qty_array[$po_wise_job_arr[$val['PO_ID']]]['grey_fab_qnty'] += $val['GREY_FAB_QNTY'];
            $booking_qty_array[$po_wise_job_arr[$val['PO_ID']]]['fin_fab_qnty'] += $val['FIN_FAB_QNTY'];
        }

        if($val['ENTRY_FORM']==228)
        {
            $service_booking_data_array[$val['JOB_NO']]['knitting'] = $val['BOOKING_NO_PREFIX_NUM'];
        }

        if($val['ENTRY_FORM']==229)
        {
            $service_booking_data_array[$val['JOB_NO']]['dyeing'] = $val['BOOKING_NO_PREFIX_NUM'];
        }

        if($val['PROCESS']==35)
        {
            $service_booking_data_array[$val['JOB_NO']]['aop'] = $val['BOOKING_NO_PREFIX_NUM'];
        }
       
    }
    // echo "<pre>";print_r($booking_data_array);echo "</pre>";die();
    /*===============================================================================/
    /                                    Work order data                             /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"d.id");
    $sql="SELECT a.job_no,c.emb_name, f.booking_no from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d, wo_pre_cos_emb_co_avg_con_dtls e, wo_booking_dtls f
    where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.job_no=f.job_no and c.id=e.pre_cost_emb_cost_dtls_id and d.id=e.po_break_down_id and e.pre_cost_emb_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=6 and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $work_order_data_array = array();
    foreach ($res as $val) 
    {
        $bek = explode("-", $val['BOOKING_NO']);
        $work_order_data_array[$val['JOB_NO']][$val['EMB_NAME']] = $bek[3];
        
    }
    // echo "<pre>";print_r($work_order_data_array);die;
    /*===============================================================================/
    /                            YARN DYEING WORK ORDER DATA                         /
    /============================================================================== */
    $job_id_cond = where_con_using_array($job_id_array,0,"b.job_no_id");
    $sql="SELECT b.job_no,a.yarn_dyeing_prefix_num from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
    where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $job_id_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $yd_work_order_data_array = array();
    foreach ($res as $val) 
    {
        $yd_work_order_data_array[$val['JOB_NO']] = $val['YARN_DYEING_PREFIX_NUM'];
        
    }
    /*===============================================================================/
    /                                   YARN Allocation                              /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_break_down_id");
    $sql = "SELECT a.job_no,a.is_dyied_yarn,a.qnty from inv_material_allocation_dtls a where a.status_active=1 and a.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $yarn_allocation_array = array();
    foreach ($res as $val) 
    {
        $yarn_allocation_array[$val['JOB_NO']][$val['IS_DYIED_YARN']] += $val['QNTY'];
    }
    /*===============================================================================/
    /                                      Textile Data                              /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_breakdown_id");
    $sql = "SELECT a.po_breakdown_id,a.entry_form,a.trans_type,a.quantity from order_wise_pro_details a where a.status_active=1 and a.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $textile_data_array = array();
    $transfer_data_array = array();
    foreach ($res as $val) 
    {
        $textile_data_array[$po_wise_job_arr[$val['PO_BREAKDOWN_ID']]][$val['ENTRY_FORM']] += $val['QUANTITY'];
        $transfer_data_array[$po_wise_job_arr[$val['PO_BREAKDOWN_ID']]][$val['ENTRY_FORM']][$val['TRANS_TYPE']] += $val['QUANTITY'];
    }
    // echo "<pre>";print_r($textile_data_array);die;

    /*===============================================================================/
    /                             Yarn Rcv From Dyeing                               /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"c.po_breakdown_id");
    $sql = "SELECT c.po_breakdown_id as po_id, c.QUANTITY  from inv_receive_master a, inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and b.item_category=1 and a.entry_form=1 and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in(2) $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $yarn_rcv_from_dyeing_array = array();
    foreach ($res as $val) 
    {
        $yarn_rcv_from_dyeing_array[$po_wise_job_arr[$val['PO_ID']]] += $val['QUANTITY'];
    }
    // echo "<pre>";print_r($yarn_rcv_from_dyeing_array);die;

    /*===============================================================================/
    /                               knitting plan data                               /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_id");
    $sql="SELECT a.program_qnty,a.po_id from ppl_planning_entry_plan_dtls a where a.status_active=1 and a.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $program_data_array = array();
    foreach ($res as $val) 
    {
        $program_data_array[$po_wise_job_arr[$val['PO_ID']]] += $val['PROGRAM_QNTY'];
    }

    /*===============================================================================/
    /                               knitting production                              /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"c.po_breakdown_id");
    $sql="SELECT a.knitting_source,c.po_breakdown_id,c.quantity from inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form=2 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $knitting_data_array = array();
    foreach ($res as $val) 
    {
        $knitting_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['KNITTING_SOURCE']] += $val['PROGRAM_QNTY'];
    }

    /*===============================================================================/
    /                               Dyed yarn issue                                  /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"c.po_breakdown_id");
    $sql = "SELECT c.po_breakdown_id as po_id, c.QUANTITY  from inv_issue_master a, inv_transaction b,order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and d.id=c.prod_id and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.dyed_type=1 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $dyed_yarn_issue_array = array();
    foreach ($res as $val) 
    {
        $dyed_yarn_issue_array[$po_wise_job_arr[$val['PO_ID']]] += $val['QUANTITY'];
    }
    // echo "<pre>";print_r($dyed_yarn_issue_array);die;

    /*===============================================================================/
    /                                Roll Data                         
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_breakdown_id");
    $sql = "SELECT a.po_breakdown_id,a.entry_form,a.qnty from pro_roll_details a where a.status_active=1 and a.is_deleted=0  $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $roll_data_array = array();
    foreach ($res as $val) 
    {
        $roll_data_array[$po_wise_job_arr[$val['PO_BREAKDOWN_ID']]][$val['ENTRY_FORM']] += $val['QNTY'];
    }

    /*===============================================================================/
    /                                Fab Dyeing Prodduction                          /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_id");
    $sql = "SELECT c.load_unload_id,a.po_id,c.batch_qty,c.production_qty from pro_batch_create_dtls a, pro_fab_subprocess b,pro_fab_subprocess_dtls c where a.mst_id=b.batch_id and b.id=c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.load_unload_id=1 and b.result=1 and b.entry_form=35 $po_cond";
    // echo $sql;die;
    $res = sql_select($sql);
    $fab_dyeing_array = array();
    foreach ($res as $val) 
    {
        $fab_dyeing_array[$po_wise_job_arr[$val['PO_ID']]] += $val['PRODUCTION_QTY'];
    }

    /*===============================================================================/
    /                                  Finish Fabric Issue                           /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"c.po_breakdown_id");
    $sql = "SELECT c.po_breakdown_id as po_id,c.quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.entry_form=71 and trans_type=2 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $fin_fab_data_array = array();
    foreach ($res as $val) 
    {
        $fin_fab_data_array[$po_wise_job_arr[$val['PO_ID']]]['qty'] + $val['QUANTITY'];
    }

    /*===============================================================================/
    /                                   Cut & Lay data                               /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.order_id");
    $sql = "SELECT b.entry_date, a.order_id, a.bundle_no,a.size_qty from ppl_cut_lay_bundle a,ppl_cut_lay_mst b where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond";
    // echo $sql;die;
    $res = sql_select($sql);
    $lay_data_array = array();
    foreach ($res as $val) 
    {
        $lay_data_array[$po_wise_job_arr[$val['ORDER_ID']]]['entry_date'] .= $val['ENTRY_DATE'].",";
        $lay_data_array[$po_wise_job_arr[$val['ORDER_ID']]]['qty'] += $val['SIZE_QTY'];
        $lay_data_array[$po_wise_job_arr[$val['ORDER_ID']]]['no_of_bndl']++;
    }

    /*===============================================================================/
    /                                  Gmts Production                               /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_break_down_id");
    $sql = "SELECT a.production_source,a.serving_company, a.production_date,a.production_type,a.po_break_down_id as po_id,b.production_qnty,a.embel_name from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond order by a.production_date";
    // echo $sql;
    $res = sql_select($sql);
    $gmts_data_array = array();
    $embel_data_array = array();
    foreach ($res as $val) 
    {
        $gmts_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']]['date'] .= $val['PRODUCTION_DATE'].",";
        $gmts_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']]['qty'] += $val['PRODUCTION_QNTY'];

        $embel_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['date'] .= $val['PRODUCTION_DATE'].",";
        $embel_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['source'] .= $val['PRODUCTION_SOURCE'].",";
        $embel_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['wo_com_id'] .= $val['SERVING_COMPANY'].",";
        $embel_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['qty'] += $val['PRODUCTION_QNTY'];
    }
    // echo "<pre>";print_r($gmts_data_array);echo "</pre>";die();

    /*===============================================================================/
    /                            Printing & Emb Order Data                           /
    /============================================================================== */
    // 204=printing, 311=emb
    $po_cond = where_con_using_array($po_array,0,"b.buyer_po_no");
    $sql = "SELECT a.entry_form, b.buyer_po_no as po_id, b.order_quantity from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_deleted=0 and a.within_group=1 and a.entry_form in(204,311) $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $print_emb_order_data_array = array();
    foreach ($res as $val) 
    {
        $print_emb_order_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['ENTRY_FORM']]['qty'] += $val['ORDER_QUANTITY'];
    }

    /*===============================================================================/
    /                                 Buyer Inspection                               /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_break_down_id");
    $sql = "SELECT a.job_no,a.inspection_qnty,to_char(a.inspection_date,'DD-MM-YYYY') as inspection_date from pro_buyer_inspection a where a.status_active=1 and a.is_deleted=0 $po_cond";
    $res = sql_select($sql);
    $buyer_insp_data = array();
    foreach ($res as $val) 
    {
        $buyer_insp_data[$val['JOB_NO']]['qty'] += $val['INSPECTION_QNTY'];
        $buyer_insp_data[$val['JOB_NO']]['date'] = $val['INSPECTION_DATE'];
    }

    /*===============================================================================/
    /                                  ex-factory data                               /
    /============================================================================== */
    $sql = "SELECT a.id,a.po_break_down_id as po_id,a.ex_factory_date,(case when a.entry_form !=85 then b.production_qnty else 0 end) as production_qnty,(case when a.entry_form =85 then b.production_qnty else 0 end) as return_qnty,a.total_carton_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $ex_data_array = array();
    $id_chk_array = array();
    foreach ($res as $val) 
    {
        $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['ex_date'] .= $val['EX_FACTORY_DATE'].",";
        $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['ex_qty'] += $val['PRODUCTION_QNTY'];
        $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['rtn_qty'] += $val['RETURN_QNTY'];
        $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['ex_value'] += ($val['PRODUCTION_QNTY']*$po_wise_unit_price_array[$val['PO_ID']]);
        $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['ex_rtn_value'] += ($val['RETURN_QNTY']*$po_wise_unit_price_array[$val['PO_ID']]);
        if(!in_array($val['ID'], $id_chk_array))
        {
            $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['cartot_qnty'] += $val['TOTAL_CARTON_QNTY'];
            $id_chk_array[$val['ID']] = $val['ID'];
        }
    }
    // echo "<pre>";print_r($ex_data_array);die();
    /*===============================================================================/
    /                                 Export Invoice                                 /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"b.po_breakdown_id");
    $sql = "SELECT b.po_breakdown_id as po_id,b.current_invoice_qnty,b.current_invoice_value,a.invoice_no from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $po_cond";
    $res = sql_select($sql);
    $invice_data = array();
    foreach ($res as $val) 
    {
        $invice_data[$po_wise_job_arr[$val['PO_ID']]]['inv_qty']+=$val['CURRENT_INVOICE_QNTY'];
        $invice_data[$po_wise_job_arr[$val['PO_ID']]]['inv_value']+=$val['CURRENT_INVOICE_VALUE'];
    }

    /*===============================================================================/
    /                                      LC/SC data                                /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"b.wo_po_break_down_id");
    $sql = "SELECT b.wo_po_break_down_id as po_id,a.lien_bank,a.pay_term,a.internal_file_no,a.tenor,max(c.amendment_no) as amendment_no from com_export_lc a,com_export_lc_order_info b, com_export_lc_amendment c where a.id=b.com_export_lc_id AND a.export_lc_no = c.export_lc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond group by b.wo_po_break_down_id,a.lien_bank,a.pay_term,a.internal_file_no,a.tenor
    UNION ALL
    SELECT b.wo_po_break_down_id as po_id,a.lien_bank,a.pay_term,a.internal_file_no,a.tenor,max(c.amendment_no) as amendment_no from com_sales_contract a,com_sales_contract_order_info b,com_sales_contract_amendment c where a.id=b.com_sales_contract_id and a.contract_no=c.contract_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond group by b.wo_po_break_down_id,a.lien_bank,a.pay_term,a.internal_file_no,a.tenor
    ";
    // echo $sql;die();
    $res = sql_select($sql);
    $lc_sc_data_array = array();
    foreach ($res as $val) 
    {
        $lc_sc_data_array[$po_wise_job_arr[$val['PO_ID']]]['lien_bank'] .= $bank_library[$val['LIEN_BANK']].",";
        $lc_sc_data_array[$po_wise_job_arr[$val['PO_ID']]]['pay_term'] .= $pay_term[$val['PAY_TERM']].",";
        $lc_sc_data_array[$po_wise_job_arr[$val['PO_ID']]]['internal_file_no'] .= $val['INTERNAL_FILE_NO'].",";
        $lc_sc_data_array[$po_wise_job_arr[$val['PO_ID']]]['tenor'] .= $val['TENOR'].",";
        $lc_sc_data_array[$po_wise_job_arr[$val['PO_ID']]]['amendment_no'] .= $val['AMENDMENT_NO'].",";
    }
    // echo "<pre>";print_r($lc_sc_data_array);die();

    /*===============================================================================/
    /                                      TNA Data                                  /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_number_id");
    $sql = "SELECT a.po_number_id,a.task_number,max(a.task_start_date) as start_date,max(a.task_finish_date) as end_date,max(a.actual_start_date) as actual_start_date,max(a.actual_finish_date) as actual_finish_date,b.job_no_mst from tna_process_mst a,wo_po_break_down b where a.po_number_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.task_start_date is not null and b.po_quantity>0 $po_cond and a.task_type=1 group by a.po_number_id,a.task_number,b.job_no_mst";
    // echo $sql;die();
    $res = sql_select($sql);
    $tnaDateArray = array();
    foreach ($res as $val) 
    {
        $tnaDateArray[$val['JOB_NO_MST']][$val['TASK_NUMBER']]['start_date']          = $val['START_DATE'];
        $tnaDateArray[$val['JOB_NO_MST']][$val['TASK_NUMBER']]['end_date']            = $val['END_DATE'];
        $tnaDateArray[$val['JOB_NO_MST']][$val['TASK_NUMBER']]['actual_start_date']   = $val['ACTUAL_START_DATE'];
        $tnaDateArray[$val['JOB_NO_MST']][$val['TASK_NUMBER']]['actual_finish_date']  = $val['ACTUAL_FINISH_DATE'];
    }

    // echo "<pre>";print_r($tnaDateArray);die();
    /*===============================================================================/
    /                                    Budget Data                                 /
    /============================================================================== */
    $job_id_cond = where_con_using_array($job_id_array,0,"a.job_id");
    $sql = "SELECT a.job_no,to_char(a.costing_date,'DD-MM-YYYY') as costing_date,a.approved,b.cm_cost from wo_pre_cost_mst a,wo_pre_cost_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_id=b.job_id $job_id_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $budget_data_array = array();
    foreach ($res as $val) 
    {
        $budget_data_array[$val['JOB_NO']]['costing_date'] = $val['COSTING_DATE'];
        $budget_data_array[$val['JOB_NO']]['approved'] = $val['APPROVED'];
        $budget_data_array[$val['JOB_NO']]['cm_cost'] = $val['CM_COST'];
    }

    /*===============================================================================/
    /                              Getting data from class                           /
    /============================================================================== */
    $poIDS = implode(",", $po_array);
    $condition= new condition();     
    $condition->po_id_in($poIDS);     
    $condition->init();
    // $fabric= new fabric($condition);
    $yarn= new yarn($condition);
    $yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
    $yarn_qty_amount_arr=$yarn->getJobWiseYarnQtyAndAmountArray();

    $yarnDataWithFabricidArr=$yarn->get_By_Precostfabricdtlsid_YarnQtyAmountArray();

    $fabric= new fabric($condition);
    $fabricAmoutByFabricSource= $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
    $fabricQtyByFabricSource= $fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish_purchase();
    
    $fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
    $fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
    $conversion= new conversion($condition);
    $conversion_costing_arr_process=$conversion->getAmountArray_by_job();
    $conv_qty_job_process= $conversion->getQtyArray_by_jobAndProcess();
    $conv_amount_job_process= $conversion->getAmountArray_by_jobAndProcess();
    $con_qty_fabric_process = $conversion->getQtyArray_by_fabricAndProcess();
    $con_amount_fabric_process = $conversion->getAmountArray_by_fabricAndProcess();

    $trims= new trims($condition);
    $trims_costing_arr=$trims->getAmountArray_by_job();
    $trims_qty_arr=$trims->getQtyArray_by_job();

    $emblishment= new emblishment($condition);
    $emblishment_costing_arr=$emblishment->getAmountArray_by_job();
    $emb_qty_job_name_arr = $emblishment->getQtyArray_by_jobAndEmbname();
    $emb_amount_job_name_arr = $emblishment->getAmountArray_by_jobAndEmbname();

    $wash= new wash($condition);
    $emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
    $wash_qty_job_name_arr =$wash->getQtyArray_by_jobAndEmbname();
    $wash_amount_job_name_arr =$wash->getAmountArray_by_jobAndEmbname();


    $commercial= new commercial($condition);
    $commercial_costing_arr=$commercial->getAmountArray_by_job();
    $commission= new commision($condition);
    $commission_costing_arr=$commission->getAmountArray_by_job();
    $other= new other($condition);
    $other_costing_arr=$other->getAmountArray_by_job();


    /*===============================================================================/
    /                        Functin for getting max min date                        /
    /============================================================================== */
    function get_max_min_date($date_arr)
    {
        $date_arr = explode(",", implode(",", $date_arr));
        $date_array = array();
        for ($i = 0; $i < count($date_arr); $i++)
        {
            if ($i == 0)
            {
                $max_date = date('d-m-Y', strtotime($date_arr[$i]));
                $min_date = date('d-m-Y', strtotime($date_arr[$i]));
                $date_array['max_date'] = $max_date;
                $date_array['min_date'] = $min_date;
            }
            elseif ($i != 0)
            {
                $new_date = date('d-m-Y', strtotime($date_arr[$i]));
                if ($new_date > $max_date)
                {
                    $max_date = $new_date;
                    $date_array['max_date'] = $max_date;
                }
                elseif ($new_date < $min_date)
                {
                    $min_date = $new_date;
                    $date_array['min_date'] = $min_date;
                }
            }
        }
        return $date_array;
    }



    $tbl_width = 25330;

    ob_start();
    ?>
    <fieldset>
        <div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
            <h2>Order Tracking Report</h2>
            <h2>Company : <?=$company_arr[$company_name]; ?></h2>
            <h2>Date : <?=change_date_format($date_from); ?>To<?=change_date_format($date_to); ?> </h2>
        </div>
        <style type="text/css">
            table thead tr td{
                padding: 5px;text-align: center;font-weight: bold;font-size: 16px;
            }
            .zoom {
              padding: 0px;
              transition: transform .2s; 
              width: 30px;
              height: 30px;
              margin: 0 auto;
              z-index: 9999 !important;
              overflow: hidden !important;
              visibility: visible;
              position: relative;
            }

            .zoom:hover {
              transform: scale(10); 

              z-index: 9999 !important;
              overflow: hidden !important;
              visibility: visible;
            }
        </style>
        <div class="report-container-part">
            <!-- ================================= report header ================================== -->
            <table cellspacing="0" border="1" class="rpt_table"   rules="all" width="<?=$tbl_width;?>"  align="center" id="table_header_1">
                <thead>                    
                    <tr>
                        <th width="30"><p>Sl.</p></th>
                        <th width="60"><p>Order Status</p></th>
                        <th width="100"><p>LC Company</p></th>
                        <th width="100"><p>Working Company</p></th>
                        <th width="80"><p>Image</p></th>
                        <th width="100"><p>Buyer</p></th>
                        <th width="50"><p>Year</p></th>
                        <th width="90"><p>Job</p></th>
                        <th width="80"><p>Repeat Job No</p></th>
                        <th width="80"><p>Sample Req. No</p></th>
                        <th width="80"><p>Style Ref</p></th>
                        <th width="80"><p>Style Desc</p></th>
                        <th width="80"><p>Gmts Item</p></th>
                        <th width="80"><p>Season</p></th>
                        <th width="80"><p>Prod Dept</p></th>
                        <th width="80"><p>Prod. Dept Code / Class</p></th>
                        <th width="80"><p>Sub Dept</p></th>
                        <th width="80"><p>Brand</p></th>
                        <th width="80"><p>Region</p></th>
                        <th width="80"><p>Prod. Catg</p></th>
                        <th width="80"><p>Country</p></th>
                        <th width="80"><p>Sustainability Standard</p></th>
                        <th width="80"><p>Fabric Material</p></th>
                        <th width="80"><p>Order Nature</p></th>
                        <th width="80"><p>Quality Label</p></th>
                        <th width="80"><p>Embilshement Name</p></th>
                        <th width="80"><p>Service Name</p></th>
                        <th width="80"><p>Order/PO No</p></th>
                        <th width="80"><p>Actual Order/PO No</p></th>
                        <th width="80"><p>GMT Color</p></th>
                        <th width="80"><p>Size</p></th>
                        <th width="80"><p>Ex Cut %</p></th>
                        <th width="80"><p>Order Qnty [Pcs]</p></th>
                        <th width="80"><p>Break Down Order Qnty [Pcs]</p></th>
                        <th width="80"><p>Order Qnty with Cut% [Pcs]</p></th>
                        <th width="80"><p>Order Qnty [Uom]</p></th>
                        <th width="80"><p>Uom</p></th>
                        <th width="80"><p>Per Unit Price</p></th>
                        <th width="80"><p>Order Value</p></th>
                        <th width="80"><p>PO Insert Date</p></th>
                        <th width="80"><p>PO Receive Date</p></th>
                        <th width="80"><p>Factory Receive Date</p></th>
                        <th width="80"><p>1st Cut Date</p></th>
                        <th width="80"><p>Last Cut Date</p></th>
                        <th width="80"><p>1st Sew Date</p></th>
                        <th width="80"><p>Last Sew Date</p></th>
                        <th width="80"><p>1st Finish Date</p></th>
                        <th width="80"><p>Last Finish Date</p></th>
                        <th width="80"><p>Insp. Offer Date</p></th>
                        <th width="80"><p>Insp. Date</p></th>
                        <th width="80"><p>Pub. Shipment Date</p></th>
                        <th width="80"><p>First Shipment Date </p></th>
                        <th width="80"><p>Org. Shipment Date </p></th>
                        <th width="80"><p>ETD/LDD Date</p></th>
                        <th width="80"><p>Cut-off Date</p></th>
                        <th width="80"><p>Country Shipment Date</p></th>
                        <th width="80"><p>RFI Plan Date</p></th>
                        <th width="80"><p>Shipment Mode</p></th>
                        <th width="80"><p>Lead Time</p></th>
                        <th width="80"><p>Days in Hand</p></th>
                        <th width="80"><p>Commercial File No</p></th>
                        <th width="80"><p>Lien Bank</p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p>Ex. LC/SC Amendment No(Last)</p></th>
                        <th width="80"><p>Pay Term</p></th>
                        <th width="80"><p>Tenor</p></th>
                        <th width="80"><p>First Shipment Date</p></th>
                        <th width="80"><p>Last Shipment Date</p></th>
                        <th width="80"><p>Shipment Qnty</p></th>
                        <th width="80"><p>Shipment Value</p></th>
                        <th width="80"><p>Excess Shipment Qnty</p></th>
                        <th width="80"><p>Excess Shipment Value</p></th>
                        <th width="80"><p>Short Shipment Qnty</p></th>
                        <th width="80"><p>Short Shipment Value</p></th>
                        <th width="80"><p>Shipping Status</p></th>
                        <th width="80"><p>SMV</p></th>
                        <th width="80"><p>Total SMV</p></th>
                        <th width="80"><p>CM</p></th>
                        <th width="80"><p>Team Leader</p></th>
                        <th width="80"><p>Dealing Merchandiser</p></th>
                        <th width="80"><p>Factory Merchandiser</p></th>
                        <th width="80"><p>Insert User</p></th>
                        <th width="80"><p>Lab Dip No</p></th>
                        <th width="80"><p>Buyer Sample Status</p></th>
                        <th width="80"><p>Order Status</p></th>
                        <th width="80"><p>Referance Closed </p></th>
                        <th width="80"><p>Emb. Work Order No</p></th>
                        <th width="80"><p>Service Work Order No</p></th>

                        <th width="80"><p>Budget Date</p></th>
                        <th width="80"><p>Budget Value</p></th>
                        <th width="80"><p>Open Value</p></th>
                        <th width="80"><p>Approved</p></th>
                        <th width="80"><p>Amendment No</p></th>
                        
                        <th width="80"><p>M Fabric Booking Date</p></th>
                        <th width="80"><p>M Fabric Booking No</p></th>
                        <th width="80"><p>Fabric Delivery Date</p></th>
                        <th width="80"><p>Amendment No</p></th>
                        <th width="80"><p>Amendment Date</p></th>
                        <th width="80"><p>Approved Status</p></th>
                        <th width="80"><p>Yarn Description</p></th>
                        <th width="80"><p>Fabric Construction</p></th>
                        <th width="80"><p>Fabric Composition</p></th>
                        <th width="80"><p>GSM</p></th>
                        <th width="80"><p>Dia/Width</p></th>
                        <th width="80"><p>Finish Quantity</p></th>
                        <th width="80"><p>Process Loss%</p></th>
                        <th width="80"><p>Grey Qnty Kg</p></th>

                        <th width="80"><p>Fabric Booking Date</p></th>
                        <th width="80"><p>Fabric Booking No</p></th>
                        <th width="80"><p>Fabric Delivery Date</p></th>
                        <th width="80"><p>Fabrication</p></th>
                        <th width="80"><p>Uom</p></th>
                        <th width="80"><p>Qnty</p></th>
                        <th width="80"><p>Approved Status</p></th>

                        <th width="80"><p>Fabric Booking Date</p></th>
                        <th width="80"><p>Fabric Booking No</p></th>
                        <th width="80"><p>Fabric Delivery Date</p></th>
                        <th width="80"><p>Fabrication</p></th>
                        <th width="80"><p>Uom</p></th>
                        <th width="80"><p>Finish Quantity</p></th>
                        <th width="80"><p>Process Loss%</p></th>
                        <th width="80"><p>Grey Qnty</p></th>
                        <th width="80"><p>Approved Status</p></th>

                        <th width="80" ><p>Trims/Acc Status</p></th>

                        <th width="80"><p>Grey Yarn Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Yarn Allocation Qnty</p></th>
                        <th width="80"><p>Yarn Excess Allocation Qnty</p></th>
                        <th width="80"><p>Yarn Received Qnty</p></th>
                        <th width="80"><p>Yarn Receive Balance Qnty</p></th>
                        <th width="80"><p>Yarn Issued Qnty</p></th>
                        <th width="80"><p>Yarn Issue Balance Qnty</p></th>
                        <th width="80"><p>Yarn Excess Issued Qty </p></th>

                        <th width="80"><p>Dyed Yarn Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Dyed Yarn Allocation Qnty</p></th>
                        <th width="80"><p>Dyed Yarn Excess Allocation Qnty</p></th>
                        <th width="80"><p>Dyed Yarn Received Qnty</p></th>
                        <th width="80"><p>Dyed Yarn Receive Balance Qnty</p></th>
                        <th width="80"><p>Dyed Yarn Issued Qnty</p></th>
                        <th width="80"><p>Dyed Yarn Issue Balance Qnty</p></th>
                        <th width="80"><p>Dyed Yarn Excess Issued Qty </p></th>

                        <th width="80"><p>Dyed Yarn Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Service Work Order Qty</p></th>
                        <th width="80"><p>Yarn Dyed Produciton Qty</p></th>
                        <th width="80"><p>Yarn Dyed Produciton WIP Qty</p></th>
                        <th width="80"><p>Yarn Dyed Delivery Qty</p></th>
                        <th width="80"><p>Yarn Dyed Challan No</p></th>
                        <th width="80"><p>Yarn Dyed Challan Date</p></th>

                        <th width="80"><p>Knitting Require with EFR Qnty</p></th>
                        <th width="80"><p>Knitting Plan Qnty</p></th>
                        <th width="80"><p>Knitting Plan WIP Qnty</p></th>
                        <th width="80"><p>Knitting Work Order No</p></th>
                        <th width="80"><p>Knitting Production Qnty [Inbound]</p></th>
                        <th width="80"><p>Knitting Production Qnty [Outbound]</p></th>
                        <th width="80"><p>TTL Knitting Production Qnty </p></th>
                        <th width="80"><p>TTL Knitting Excess Production Qnty</p></th>
                        <th width="80"><p>TTL Knitting Production WIP Qnty</p></th>
                        <th width="80"><p>Price/KG [TK]</p></th>
                        <th width="80"><p>Total Price [TK]</p></th>
                        <th width="80"><p>Knitting Grey Fabric Delivery Qnty To Store</p></th>
                        <th width="80"><p>Closing Status</p></th>

                        <th width="80"><p>Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Normal Received Qnty</p></th>
                        <th width="80"><p>Normal Receive Excess Qnty</p></th>
                        <th width="80"><p>Receive Balance Qnty</p></th>
                        <th width="80"><p>Transfer Received Qnty</p></th>
                        <th width="80"><p>TTL Fabric Received Qnty</p></th>
                        <th width="80"><p>Normal Issued Qnty</p></th>
                        <th width="80"><p>Trasfered Issued Qnty</p></th>
                        <th width="80"><p>TTL Fabric Issued Qnty</p></th>
                        <th width="80"><p>Stock In Hand</p></th>

                        <th width="80"><p>Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Fabric Received by Batch </p></th>
                        <th width="80"><p>Batch Qnty</p></th>
                        <th width="80"><p>Production Qnty</p></th>
                        <th width="80"><p>Production Excess Qnty</p></th>
                        <th width="80"><p>Production WIP Qnty</p></th>

                        <th width="80"><p>Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Production Qnty</p></th>
                        <th width="80"><p>Production Excess Qnty</p></th>
                        <th width="80"><p>Production WIP Qnty</p></th>
                        <th width="80"><p>Process Name</p></th>
                        <th width="80"><p>QC Received Qnty</p></th>
                        <th width="80"><p>AOP Issue Qnty</p></th>
                        <th width="80"><p>AOP Receive Qnty</p></th>
                        <th width="80"><p>AOP Receive WIP Qnty</p></th>
                        <th width="80"><p>QC Delivery Qnty To Store</p></th>
                        <th width="80"><p>QC Stock in Hand</p></th>
                        <th width="80"><p>Closing Status</p></th>

                        <th width="80"><p>Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Normal Received Qnty</p></th>
                        <th width="80"><p>Normal Receive Excess Qnty</p></th>
                        <th width="80"><p>Receive Balance Qnty</p></th>
                        <th width="80"><p>Transfer Received Qnty</p></th>
                        <th width="80"><p>TTL Received Qnty</p></th>
                        <th width="80"><p>Normal Issued Qnty</p></th>
                        <th width="80"><p>Trasfered Issued Qnty</p></th>
                        <th width="80"><p>TTL Issued Qnty</p></th>
                        <th width="80"><p>Stock In Hand</p></th>

                        <th width="80"><p>Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Fabric Receive Qnty By Cutting</p></th>
                        <th width="80"><p>Excess Receive Qnty By Cutting </p></th>
                        <th width="80"><p>Receive Balance Qnty By Cutting</p></th>
                        <th width="80"><p>Cutting Status</p></th>
                        <th width="80"><p>Oreder Qnty</p></th>
                        <th width="80"><p>Excess Cutting %</p></th>
                        <th width="80"><p>Order Qnty with C%</p></th>
                        <th width="80"><p>Cutting Bundle Qnty</p></th>
                        <th width="80"><p>Cutting Lay Qnty</p></th>
                        <th width="80"><p>Cutting Production [QC] Qnty</p></th>
                        <th width="80"><p>Excess Cutting Qnty</p></th>
                        <th width="80"><p>Cutting Production Balance Qnty</p></th>
                        <th width="80"><p>Input Qnty</p></th>
                        <th width="80"><p>Input WIP Qnty</p></th>
                        <th width="80"><p>Print Send Qnty</p></th>
                        <th width="80"><p>Print Receive Qnty</p></th>
                        <th width="80"><p>Print WIP Qnty</p></th>
                        <th width="80"><p>Print Supplier</p></th>
                        <th width="80"><p>EMB Send Qnty</p></th>
                        <th width="80"><p>EMB Receive Qnty</p></th>
                        <th width="80"><p>EMB WIP Balance Qnty</p></th>
                        <th width="80"><p>EMB Supplier</p></th>

                        <th width="80"><p>Require Qnty</p></th>
                        <th width="80"><p>Order Qnty</p></th>
                        <th width="80"><p>Order Balance Qnty</p></th>
                        <th width="80"><p>Production [QC] Qnty</p></th>
                        <th width="80"><p>Production Excess Qnty</p></th>
                        <th width="80"><p>Production WIP Qnty</p></th>
                        <th width="80"><p>Delivery Qnty To Cutting</p></th>

                        <th width="80"><p>Require Qnty</p></th>
                        <th width="80"><p>Work Order Qnty</p></th>
                        <th width="80"><p>Work Order Balance Qnty</p></th>
                        <th width="80"><p>Production [QC] Qnty</p></th>
                        <th width="80"><p>Production Excess Qnty</p></th>
                        <th width="80"><p>Production WIP Qnty</p></th>
                        <th width="80"><p>Delivery Qnty To Cutting</p></th>

                        <th width="80"><p>Require Qnty</p></th>
                        <th width="80"><p>Input Qnty</p></th>
                        <th width="80"><p>Input Excess Qnty</p></th>
                        <th width="80"><p>Input Balance Qnty</p></th>
                        <th width="80"><p>Production [Output] Qnty</p></th>
                        <th width="80"><p>Production Excess Qnty</p></th>
                        <th width="80"><p>Production WIP Qnty</p></th>

                        <th width="80"><p>Require Qnty</p></th>
                        <th width="80"><p>Iron Qnty</p></th>
                        <th width="80"><p>HANGTAG Qnty</p></th>
                        <th width="80"><p>POLY Qnty</p></th>
                        <th width="80"><p>Finish Quantity</p></th>
                        <th width="80"><p>Balance Qty</p></th>
                        <th width="80"><p>Inspection Offer Date</p></th>
                        <th width="80"><p>Inspection Date</p></th>
                        <th width="80"><p>Inspection Qnty</p></th>

                        <th width="80"><p>Carton Qty</p></th>
                        <th width="80"><p>Shipment Qnty</p></th>
                        <th width="80"><p>Invoice Qty</p></th>
                        <th width="80"><p>Shipment Value</p></th>
                        <th width="80"><p>Invoice Value</p></th>
                        <th width="80"><p>Net Invoice Value</p></th>
                        <th width="80"><p>Excess Shipment Qnty</p></th>
                        <th width="80"><p>Excess Shipment Value </p></th>
                        <th width="80"><p>Short Shipment Qnty</p></th>
                        <th width="80"><p>Short Shipment Value</p></th>
                        <th width="80"><p>Shipment Return Qnty</p></th>
                        <th width="80"><p>Shipment Return Value</p></th>
                        <th width="80"><p>First Shipment Date</p></th>
                        <th width="80"><p>Last Shipment Date</p></th>
                        <th width="80"><p>Discount / Penalty / Claim Amount</p></th>
                        <th width="80"><p>Reason of Discount / Penalty / Claim </p></th>

                        <th width="80"><p>Budget Date</p></th>
                        <th width="80"><p>Budget Yarn Cost</p></th>
                        <th width="80"><p>Actual Yarn Cost</p></th>
                        <th width="80"><p>Budget Finish YD Cost</p></th>
                        <th width="80"><p>Actual Finish YD Cost</p></th>
                        <th width="80"><p>Budget Knitting Cost</p></th>
                        <th width="80"><p>Actual Knitting Cost</p></th>
                        <th width="80"><p>Budget Dyeing & Finishing Cost</p></th>
                        <th width="80"><p>Actual Dyeing & Finishing Cost</p></th>
                        <th width="80"><p>Budget Fabric Purchase Cost</p></th>
                        <th width="80"><p>Actual Fabric Purchase Cost</p></th>
                        <th width="80"><p>Budget AOP Cost</p></th>
                        <th width="80"><p>Actual AOP Cost</p></th>
                        <th width="80"><p>Budget Print Cost</p></th>
                        <th width="80"><p>Actual Print Cost</p></th>
                        <th width="80"><p>Budget Emb Cost</p></th>
                        <th width="80"><p>Actual Emb Cost</p></th>
                        <th width="80"><p>Budget Wash Cost</p></th>
                        <th width="80"><p>Actual Wash Cost</p></th>
                        <th width="80"><p>Budget Trims Cost</p></th>
                        <th width="80"><p>Actual Trims Cost</p></th>
                        <th width="80"><p>Budget MISC Cost</p></th>
                        <th width="80"><p>Actual MISC Cost</p></th>
                        <th width="80"><p>Budget CM</p></th>
                        <th width="80"><p>Actual CM</p></th>

                        <th width="80"><p>Lead Time [Templete]</p></th>
                        <th width="80"><p>Yarn Receive Start</p></th>
                        <th width="80"><p>Yarn Receive End</p></th>
                        <th width="80"><p>Knitting Start</p></th>
                        <th width="80"><p>Knitting End</p></th>
                        <th width="80"><p>Dyeing Start</p></th>
                        <th width="80"><p>Dyeing End</p></th>
                        <th width="80"><p>Finish Fabric Prod. Start</p></th>
                        <th width="80"><p>Finish Fabric Prod. End</p></th>
                        <th width="80"><p>Trims Receive Start</p></th>
                        <th width="80"><p>Trims Receive End</p></th>
                        <th width="80"><p>Cutting Start</p></th>
                        <th width="80"><p>Cutting End</p></th>
                        <th width="80"><p>Print Start</p></th>
                        <th width="80"><p>Print End</p></th>
                        <th width="80"><p>Emb Start</p></th>
                        <th width="80"><p>Emb End</p></th>
                        <th width="80"><p>Sewing Start</p></th>
                        <th width="80"><p>Sewing End</p></th>
                        <th width="80"><p>GMT Finihsing Start</p></th>
                        <th width="80"><p>GMT Finihsing End</p></th>
                        <th width="80"><p>Inspection Start</p></th>
                        <th width="80"><p>Inspection End</p></th>
                        <th width="80"><p>Shipment  Start</p></th>
                        <th width="80"><p>Shipment  End</p></th>
                    </tr>
                </thead>
            </table>
            <!-- =================================== report body ==================================== -->
            <div style=" max-height:300px; width:<?=$tbl_width+20;?>px; overflow-y:scroll;" id="scroll_body">
                <table cellspacing="0" border="1" class="rpt_table"   rules="all" width="<?=$tbl_width;?>"  align="center" id="table_body">
                    <tbody>
                        <?
                        $i=1;
                        $gr_job_total = 0;
                        $gr_po_total = 0;
                        $gr_color_size_total = 0;
                        $gr_plan_cut_total = 0;
                        $gr_order_value_total = 0;

                        foreach ($data_array as $job_key => $row) 
                        {   
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";   
                            $ship_excess_qty = max($ex_data_array[$job_key]['ex_qty'] - $row['order_quantity'],0);
                            $ship_excess_val = max($ex_data_array[$job_key]['ex_value'] - $row['order_value'],0); 
                            $ship_short_qty = max($row['order_quantity'] - $ex_data_array[$job_key]['ex_qty'],0);
                            $ship_short_val = max($row['order_value'] - $ex_data_array[$job_key]['ex_value'],0);

                            // =============================== for budget data ===========================
                            $finishing_arr = array('209','165','33','94','63','171','65','170','156','179','200','208','127','125','84','68','128','190','242','240','192','172','90','218','67','197','73','66','185','142');
                            $total_finishing_amount=0;
                            $total_finishing_qty=0;
                            $other_cost_attr = array('inspection','freight','certificate_pre_cost','deffdlc_cost','design_cost','studio_cost','common_oh','interest_cost','incometax_cost','depr_amor_pre_cost');
                            $total_other_cost = 0;
                            foreach ($other_cost_attr as $attr) {
                                $total_other_cost+=$other_costing_arr[$job_key][$attr];
                            }
                            $misc_cost=$other_costing_arr[$job_key]['lab_test']+$commercial_costing_arr[$job_key]+$commission_costing_arr[$job_key]+$total_other_cost;

                            foreach ($finishing_arr as $fid) {
                                $total_finishing_amount += array_sum($conv_amount_job_process[$job_key][$fid]);
                                $total_finishing_qty += array_sum($conv_qty_job_process[$job_key][$fid]);
                            }

                            $total_fabic_cost=0;
                            if(count($conv_amount_job_process[$job_key][31])>0){
                                $total_fabic_cost+=array_sum($conv_amount_job_process[$job_key][31])/array_sum($conv_qty_job_process[$job_key][31]);
                            }
                            $total_fabric_amount +=array_sum($conv_amount_job_process[$job_key][31]);
                            $total_fabric_per +=array_sum($conv_amount_job_process[$job_key][31])/$order_values*100;
                            if(count($conv_amount_job_process[$job_key][30])>0){
                                $total_fabic_cost+=array_sum($conv_amount_job_process[$job_key][30])/array_sum($conv_qty_job_process[$job_key][30]);
                            }
                            if($yarn_qty_amount_arr[$job_key]['amount']!=''){
                                $total_fabic_cost+=$yarn_qty_amount_arr[$job_key]['amount']/$yarn_qty_amount_arr[$job_key]['qty'];
                            }
                            $total_fabric_amount +=$yarn_qty_amount_arr[$job_key]['amount']; 
                            $total_fabric_per +=$yarn_qty_amount_arr[$job_key]['amount']/$order_values*100;
                            if($total_finishing_amount!=0){
                                $total_fabic_cost+=$total_finishing_amount/$total_finishing_qty;
                            } 
                            $total_fabric_amount +=$total_finishing_amount;
                            $total_fabric_per +=$total_finishing_amount/$order_values*100;
                            $total_fabric_amount +=array_sum($conv_amount_job_process[$job_key][30]);
                            $total_fabric_per +=array_sum($conv_amount_job_process[$job_key][30])/$order_values*100;
                            if(count($conv_amount_job_process[$job_key][35])>0){
                                $total_fabic_cost+=array_sum($conv_amount_job_process[$job_key][35])/array_sum($conv_qty_job_process[$job_key][35]);
                            }
                            $total_fabric_amount +=array_sum($conv_amount_job_process[$job_key][35]); 
                            $total_fabric_per +=array_sum($conv_amount_job_process[$job_key][35])/$order_values*100;
                            if(count($conv_amount_job_process[$job_key][1])>0){
                                $total_fabic_cost+=array_sum($conv_amount_job_process[$job_key][1])/array_sum($conv_qty_job_process[$job_key][1]);
                            }
                            $total_fabric_amount +=array_sum($conv_amount_job_process[$job_key][1]);
                            $total_fabric_per +=array_sum($conv_amount_job_process[$job_key][1])/$order_values*100; 

                            $purchase_amount = array_sum($fabricAmoutByFabricSource['knit']['grey'][$job_key])+array_sum($fabricAmoutByFabricSource['woven']['grey'][$job_key]);
                            $purchase_qty = array_sum($fabricQtyByFabricSource['knit']['grey'][$job_key])+array_sum($fabricQtyByFabricSource['woven']['grey'][$job_key]);

                            $ather_emb_attr = array(4,5,6,99);
                            foreach ($ather_emb_attr as $att) {
                                $others_emb_amount += $emb_amount_job_name_arr[$job_key][$att];
                                $others_emb_qty += $emb_qty_job_name_arr[$job_key][$att];
                            }
                            $knitting_amount_summ=''; $dyeing_amount_summ=''; $yds_amount_summ=''; $aop_amount_summ='';
                            if(count($conv_amount_job_process[$job_key][1])>0) {
                                $knitting_amount_summ = fn_number_format(array_sum($conv_amount_job_process[$job_key][1]),2);
                            }
                            $yarn_amount_summ = $yarn_qty_amount_arr[$job_key]['amount'];
                            $print_amount_summ = $emb_amount_job_name_arr[$job_key][1];      
                            $emb_amount_summ= $emb_amount_job_name_arr[$job_key][2];
                            $wash_amount_summ = $wash_amount_job_name_arr[$job_key][3];
                            if(count($conv_amount_job_process[$job_key][31])>0) {
                                $dyeing_amount_summ=  array_sum($conv_amount_job_process[$job_key][31]);
                            }
                            if(count($conv_amount_job_process[$job_key][30])>0) {
                                $yds_amount_summ = array_sum($conv_amount_job_process[$job_key][30]);
                            }
                            if(count($conv_amount_job_process[$job_key][35])>0) {
                                $aop_amount_summ = array_sum($conv_amount_job_process[$job_key][35]);
                            }
                            
                            $total_budget_value = $yarn_amount_summ+$total_finishing_amount+$print_amount_summ+$trims_costing_arr[$job_key]+$yds_amount_summ+$aop_amount_summ+$emb_amount_summ+$knitting_amount_summ+$purchase_amount+$wash_amount_summ+$other_costing_arr[$job_key]['cm_cost']+$dyeing_amount_summ+$others_emb_amount+$misc_cost;
                            $open_value = $row['total_price']-$total_budget_value;
                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
                                <td width="30">.<p><?=$i;?></p></td>
                                <td width="60">
                                    <p>
                                        <?
                                        $order_status_arr = array_unique(array_filter(explode(",", $row['order_status'])));
                                        $order_status_name = "";
                                        foreach ($order_status_arr as $val) 
                                        {
                                            $order_status_name .= ($order_status_name=="") ? $order_status[$val] : ", ".$order_status[$val];
                                        }
                                        echo $order_status_name;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="100"><p><?=$company_library[$row['company_name']];?></p></td>
                                <td width="100"><p><?=$company_library[$row['wo_com_id']];?></p></td>
                                <td width="80"><img class="zoom" src='../../../<?= $imge_arr[$job_key]; ?>' height='40' width='50' /></td>
                                <td width="100"><p><?=$buyer_library[$row['buyer_name']];?></p></td>
                                <td width="50" align="center"><p><?=$row['year'];?></p></td>
                                <td width="90"><p><?=$job_key;?></p></td>
                                <td width="80"><p><?=$row['repeat_job_no'];?></p></td>
                                <td width="80"><p><?=$row['req_no'];?></p></td>
                                <td width="80"><p><?=$row['style'];?></p></td>
                                <td width="80"><p><?=$row['style_des'];?></p></td>
                                <td width="80">
                                    <p>
                                        <?
                                        $item_id_arr = array_unique(array_filter(explode(",", $row['item_id'])));
                                        $item_name = "";
                                        foreach ($item_id_arr as $val) 
                                        {
                                            $item_name .= ($item_name=="") ? $garments_item[$val] : ", ".$garments_item[$val];
                                        }
                                        echo $item_name;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80"><p><?=$season_library[$row['season']];?></p></td>
                                <td width="80"><p><?=$product_dept[$row['product_dept']];?></p></td>
                                <td width="80"><p><?=$row['product_code'];?></p></td>
                                <td width="80"><p><?=$sub_dep_library[$row['pro_sub_dep']];?></p></td>
                                <td width="80"><p><?=$brand_library[$row['brand_id']];?></p></td>
                                <td width="80"><p><?=$region[$row['region']];?></p></td>
                                <td width="80"><p><?=$product_category[$row['product_category']];?></p></td>
                                <td width="80">
                                    <p>
                                        <?
                                        $country_id_arr = array_unique(array_filter(explode(",", $row['country_id'])));
                                        $country_name = "";
                                        foreach ($country_id_arr as $val) 
                                        {
                                            $country_name .= ($country_name=="") ? $country_library[$val] : ", ".$country_library[$val];
                                        }
                                        echo $country_name;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80"><p><?=$sustainability_standard[$row['sustainability_standard']];?></p></td>
                                <td width="80"><p><?=$fab_material[$row['fab_material']];?></p></td>
                                <td width="80"><p><?=$fbooking_order_nature[$row['quality_level']];?></p></td>
                                <td width="80"><p><?=$quality_label[$row['qlty_label']];?></p></td>
                                <td width="80"><p><?=$emb_name_arr[$job_key];?></p></td>
                                <td width="80"><p><?=$conv_name_arr[$job_key];?></p></td>
                                <td width="80">
                                    <p>
                                        <?
                                        $po_number_arr = array_unique(array_filter(explode(",", $row['po_number'])));
                                        $po_name = "";
                                        foreach ($po_number_arr as $val) 
                                        {
                                            $po_name .= ($po_name=="") ? $val : ", ".$val;
                                        }
                                        echo $po_name;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80"><p><?=$acc_po_array[$job_key]['acc_po'];?></p></td>
                                <td width="80">
                                    <p>
                                        <?
                                        $color_arr = array_unique(array_filter(explode(",", $row['color_id'])));
                                        $color_name = "";
                                        foreach ($color_arr as $val) 
                                        {
                                            $color_name .= ($color_name=="") ? $color_library[$val] : ", ".$color_library[$val];
                                        }
                                        echo $color_name;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80">
                                    <p>
                                        <?
                                        $size_arr = array_unique(array_filter(explode(",", $row['size_id'])));
                                        $size_name = "";
                                        foreach ($size_arr as $val) 
                                        {
                                            $size_name .= ($size_name=="") ? $size_library[$val] : ", ".$size_library[$val];
                                        }
                                        echo $size_name;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80">
                                    <p>
                                        <?
                                        $excess_cut_arr = array_unique(array_filter(explode("**", $po_wise_data[$job_key]['excess_cut'])));
                                        $ex_cut = "";
                                        foreach ($excess_cut_arr as $val) 
                                        {
                                            $ex_cut .= ($ex_cut=="") ? "(".$val.")" : ",(".$val.")";
                                        }
                                        echo $ex_cut;
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="right"><p><?=number_format($row['job_qty_pcs'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($row['order_quantity'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($row['plan_cut_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($row['po_quantity'],0);?></p></td>
                                <td width="80" align="center"><p><?=$unit_of_measurement[$row['order_uom']];?></p></td>
                                <td width="80" align="right"><p><?=$row['unit_price'];?></p></td>
                                <td width="80" align="right"><p><?=number_format($row['order_value'],0);?></p></td>
                                <td width="80" align="center"><p><?=$row['po_insert_date'];?></p></td>
                                <td width="80" align="center"><p><?=$row['po_received_date'];?></p></td>
                                <td width="80" align="center"><p><?=$row['factory_received_date'];?></p></td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $lay_date_arr = array_unique(array_filter(explode(",", $lay_data_array[$job_key]['entry_date'])));
                                        $lay_date = get_max_min_date($lay_date_arr);
                                        echo ($lay_date['min_date']!="") ? change_date_format($lay_date['min_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        echo ($lay_date['max_date']!="") ? change_date_format($lay_date['max_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $sew_out_date_arr = array_unique(array_filter(explode(",", $gmts_data_array[$job_key][5]['date'])));
                                        $sew_out_date = get_max_min_date($sew_out_date_arr);
                                        echo ($sew_out_date['min_date']!="") ? change_date_format($sew_out_date['min_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        echo ($sew_out_date['max_date']!="") ? change_date_format($sew_out_date['max_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $gmt_fin_date_arr = array_unique(array_filter(explode(",", $gmts_data_array[$job_key][8]['date'])));
                                        $gmt_fin_date = get_max_min_date($gmt_fin_date_arr);
                                        echo ($gmt_fin_date['min_date']!="") ? change_date_format($gmt_fin_date['min_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        echo ($gmt_fin_date['max_date']!="") ? change_date_format($gmt_fin_date['max_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$row['pub_shipment_date'];?></p></td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $ex_date_array = array_unique(array_filter(explode(",", $ex_data_array[$job_key]['ex_date'])));
                                        $ex_date = get_max_min_date($ex_date_array);
                                        // echo min($ex_date_array);
                                        echo ($ex_date['min_date']!="") ? change_date_format($ex_date['min_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80"><p><?=$row['shipment_date'];?></p></td>
                                <td width="80"><p><?=$row['txt_etd_ldd'];?></p></td>
                                <td width="80"><p><?=$cutup_date = implode(",", array_unique(array_filter(explode(",", $row['cutup_date']))));?></p></td>
                                <td width="80"><p><?=$coun_ship_date = implode(",", array_unique(array_filter(explode(",", $row['country_ship_date']))));?></p></td>
                                <td width="80"><p><?=$row['pub_shipment_date'];?></p></td>
                                <td width="80"><p><?=$shipment_mode[$row['ship_mode']];?></p></td>
                                <td width="80"><p><?=$row['lead_time'];?></p></td>
                                <td width="80"><p><?=$row['days_in_hand'];?></p></td>
                                <td width="80">
                                    <p>
                                        <?
                                        $com_file = implode(",",array_unique(array_filter(explode(",",$lc_sc_data_array[$job_key]['internal_file_no']))));
                                        echo $com_file;
                                        ?>                                            
                                    </p>
                                </td>                                
                                <td width="80">
                                    <p>
                                        <?
                                        $lien_bank = implode(",",array_unique(array_filter(explode(",",$lc_sc_data_array[$job_key]['lien_bank']))));
                                        echo $lien_bank;
                                        ?>                                            
                                    </p>
                                </td>
                                <td width="80"><p><?=$a;?></p></td>                                
                                <td width="80">
                                    <p>
                                        <?
                                        $amendment_no = implode(",",array_unique(array_filter(explode(",",$lc_sc_data_array[$job_key]['amendment_no']))));
                                        echo $amendment_no;
                                        ?>                                            
                                    </p>
                                </td>                               
                                <td width="80">
                                    <p>
                                        <?
                                        $pay_term = implode(",",array_unique(array_filter(explode(",",$lc_sc_data_array[$job_key]['pay_term']))));
                                        echo $pay_term;
                                        ?>                                            
                                    </p>
                                </td>                                
                                <td width="80">
                                    <p>
                                        <?
                                        $tenor = implode(",",array_unique(array_filter(explode(",",$lc_sc_data_array[$job_key]['tenor']))));
                                        echo $tenor;
                                        ?>                                            
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        echo ($ex_date['min_date']!="") ? change_date_format($ex_date['min_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        echo ($ex_date['max_date']!="") ? change_date_format($ex_date['max_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['ex_qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['ex_value'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ship_excess_qty,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ship_excess_val,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ship_short_qty,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ship_short_val,0);?></p></td>
                                <td width="80" align="center"><p><?=$row['shipment_status'];?></p></td>
                                <td width="80" align="right"><p><?=$row['set_smv'];?></p></td>
                                <td width="80" align="right"><p><?=$row['set_smv'];?></p></td>
                                <td width="80"><p><?=$budget_data_array[$job_key]['cm_cost'];?></p></td>
                                <td width="80"><p><?=$team_library[$row['team_leader']];?></p></td>
                                <td width="80"><p><?=$merchant_library[$row['dealing_marchant']];?></p></td>
                                <td width="80"><p><?=$merchant_library[$row['factory_marchant']];?></p></td>
                                <td width="80"><p><?=$user_library[$row['inserted_by']];?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80">
                                    <a href="javascript:void(0);" onclick="show_popup('sample_status', '<?=$job_key; ?>', '920px',1)">
                                        View
                                    </a>
                                </td>
                                <td width="80">
                                    <a href="javascript:void(0);" onclick="show_popup('order_status', '<?=$job_key; ?>', '320px',2)">
                                        View
                                    </a>
                                </td>
                                <td width="80">
                                    <a href="javascript:void(0);" onclick="show_popup('closing_status', '<?=$job_key; ?>', '320px',3)">
                                        View
                                    </a>
                                </td>
                                <td width="80">
                                    <p>
                                        <?
                                            echo ($work_order_data_array[$job_key][1]) ? "Printing : ".$work_order_data_array[$job_key][1] : "";
                                            echo ($work_order_data_array[$job_key][2]) ? "<br>Embroidery : ".$work_order_data_array[$job_key][2] : "";
                                            echo ($work_order_data_array[$job_key][3]) ? "<br>Wash : ".$work_order_data_array[$job_key][3] : "";
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80">
                                    <p>
                                        <?
                                            echo "AOP : ".$service_booking_data_array[$job_key]['aop'];
                                            echo "<br>Yarn Dyeing : ".$yd_work_order_data_array[$job_key];
                                            echo "<br>Knitting : ".$service_booking_data_array[$job_key]['knitting'];
                                            echo "<br>Fab. Dyeing : ".$service_booking_data_array[$job_key]['dyeing'];
                                        ?>
                                        
                                    </p>
                                </td>



                                <td width="80"><p><?=$budget_data_array[$job_key]['costing_date'];?></p></td>
                                <td width="80" align="right"><p><?=number_format($total_budget_value,2);?></p></td>
                                <td width="80" align="right"><p><?=number_format($open_value,0);?></p></td>
                                <td width="80"><p><?=($budget_data_array[$job_key]['approved']==1) ? "Approved" : "Not Approved";?></p></td>
                                <td width="80"><p><?=number_format($a,0);?></p></td>


                                
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $booking_date_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['booking_date'])));
                                        $booking_date = "";
                                        foreach ($booking_date_arr as $val) 
                                        {
                                            $booking_date .= ($booking_date=="") ? $val : ", ".$val;
                                        }
                                        echo $booking_date;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $booking_no_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['booking_no'])));
                                        $booking_no = "";
                                        foreach ($booking_no_arr as $val) 
                                        {
                                            $booking_no .= ($booking_no=="") ? $val : ", ".$val;
                                        }
                                        echo $booking_no;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $delivery_date_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['delivery_date'])));
                                        $delivery_date = "";
                                        foreach ($delivery_date_arr as $val) 
                                        {
                                            $delivery_date .= ($delivery_date=="") ? $val : ", ".$val;
                                        }
                                        echo $delivery_date;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $revised_no_arr = array_unique(explode(",", $booking_data_array[$job_key][1][2]['revised_no']));
                                        $revised_no = "";
                                        foreach ($revised_no_arr as $val) 
                                        {
                                            $revised_no .= ($revised_no=="") ? $val : ", ".$val;
                                        }
                                        echo $revised_no;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $revised_date_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['revised_date'])));
                                        $revised_date = "";
                                        foreach ($revised_date_arr as $val) 
                                        {
                                            $revised_date .= ($revised_date=="") ? $val : ", ".$val;
                                        }
                                        echo $revised_date;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $is_approved_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['is_approved'])));
                                        $is_approved = "";
                                        foreach ($is_approved_arr as $val) 
                                        {
                                            $is_approved .= ($is_approved=="") ? $approval_type_arr[$val] : ", ".$approval_type_arr[$val];
                                        }
                                        echo ($is_approved=="") ? " Not Approved" : $is_approved;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $construction_arr = array_unique(array_filter(explode("__", $booking_data_array[$job_key][1][2]['construction'])));
                                        $construction = "";
                                        foreach ($construction_arr as $val) 
                                        {
                                            $construction .= ($construction=="") ? $val : ", ".$val;
                                        }
                                        echo $construction;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $copmposition_arr = array_unique(array_filter(explode("__", $booking_data_array[$job_key][1][2]['copmposition'])));
                                        $copmposition = "";
                                        foreach ($copmposition_arr as $val) 
                                        {
                                            $copmposition .= ($copmposition=="") ? $val : ", ".$val;
                                        }
                                        echo $copmposition;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $gsm_weight_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['gsm_weight'])));
                                        $gsm_weight = "";
                                        foreach ($gsm_weight_arr as $val) 
                                        {
                                            $gsm_weight .= ($gsm_weight=="") ? $val : ", ".$val;
                                        }
                                        echo $gsm_weight;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $dia_width_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['dia_width'])));
                                        $dia_width = "";
                                        foreach ($dia_width_arr as $val) 
                                        {
                                            $dia_width .= ($dia_width=="") ? $val : ", ".$val;
                                        }
                                        echo $dia_width;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][2]['fin_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][2]['process_loss'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][2]['grey_fab_qnty'],0);?></p></td>



                                
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $booking_date_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1]['booking_date'])));
                                        $booking_date = "";
                                        foreach ($booking_date_arr as $val) 
                                        {
                                            $booking_date .= ($booking_date=="") ? $val : ", ".$val;
                                        }
                                        echo $booking_date;
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $booking_no_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1]['booking_no'])));
                                        $booking_no = "";
                                        foreach ($booking_no_arr as $val) 
                                        {
                                            $booking_no .= ($booking_no=="") ? $val : ", ".$val;
                                        }
                                        echo $booking_no;
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $delivery_date_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1]['delivery_date'])));
                                        $delivery_date = "";
                                        foreach ($delivery_date_arr as $val) 
                                        {
                                            $delivery_date .= ($delivery_date=="") ? $val : ", ".$val;
                                        }
                                        echo $delivery_date;
                                        ?>                                        
                                    </p>
                                </td>                                
                                <td width="80">
                                    <p>
                                        <?
                                        $construction_arr = array_unique(array_filter(explode("__", $pur_booking_data_array[$job_key][1]['construction'])));
                                        $construction = "";
                                        foreach ($construction_arr as $val) 
                                        {
                                            $construction .= ($construction=="") ? $val : ", ".$val;
                                        }
                                        echo $construction;
                                        ?>                                        
                                    </p>
                                </td>                                                               
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $uom_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1]['uom'])));
                                        $uom = "";
                                        foreach ($uom_arr as $val) 
                                        {
                                            $uom .= ($uom=="") ? $unit_of_measurement[$val] : ", ".$unit_of_measurement[$val];
                                        }
                                        echo $uom;
                                        ?>                                        
                                    </p>
                                </td>                                                                                               
                                <td width="80" align="right">
                                    <p>
                                        <?
                                        $fin_fab_qnty_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1]['fin_fab_qnty'])));
                                        $fin_fab_qnty = "";
                                        foreach ($fin_fab_qnty_arr as $val) 
                                        {
                                            $fin_fab_qnty .= ($fin_fab_qnty=="") ? $val : ", ".$val;
                                        }
                                        echo $fin_fab_qnty;
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $is_approved_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1][2]['is_approved'])));
                                        $is_approved = "";
                                        foreach ($is_approved_arr as $val) 
                                        {
                                            $is_approved .= ($is_approved=="") ? $approval_type_arr[$val] : ", ".$approval_type_arr[$val];
                                        }
                                        echo ($is_approved=="") ? " Not Approved" : $is_approved;
                                        ?>
                                        
                                    </p>
                                </td>

                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $booking_date_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][1]['booking_date'])));
                                        $booking_date = "";
                                        foreach ($booking_date_arr as $val) 
                                        {
                                            $booking_date .= ($booking_date=="") ? $val : ", ".$val;
                                        }
                                        echo $booking_date;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $booking_no_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][1]['booking_no'])));
                                        $booking_no = "";
                                        foreach ($booking_no_arr as $val) 
                                        {
                                            $booking_no .= ($booking_no=="") ? $val : ", ".$val;
                                        }
                                        echo $booking_no;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $delivery_date_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][1]['delivery_date'])));
                                        $delivery_date = "";
                                        foreach ($delivery_date_arr as $val) 
                                        {
                                            $delivery_date .= ($delivery_date=="") ? $val : ", ".$val;
                                        }
                                        echo $delivery_date;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $construction_arr = array_unique(array_filter(explode("__", $booking_data_array[$job_key][1][1]['construction'])));
                                        $construction = "";
                                        foreach ($construction_arr as $val) 
                                        {
                                            $construction .= ($construction=="") ? $val : ", ".$val;
                                        }
                                        echo $construction;
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $uom_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][1]['uom'])));
                                        $uom = "";
                                        foreach ($uom_arr as $val) 
                                        {
                                            $uom .= ($uom=="") ? $unit_of_measurement[$val] : ", ".$unit_of_measurement[$val];
                                        }
                                        echo $uom;
                                        ?>                                        
                                    </p>
                                </td>                                
                                <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][1]['fin_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][1]['process_loss'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][1]['grey_fab_qnty'],0);?></p></td>                                
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $is_approved_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][1]['is_approved'])));
                                        $is_approved = "";
                                        foreach ($is_approved_arr as $val) 
                                        {
                                            $is_approved .= ($is_approved=="") ? $approval_type_arr[$val] : ", ".$approval_type_arr[$val];
                                        }
                                        echo ($is_approved=="") ? " Not Approved" : $is_approved;
                                        ?>
                                        
                                    </p>
                                </td>


                                
                                <td width="80"><p><a href="javascript:void(0);">View</a></p></td>



                                <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['grey_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($yarn_allocation_array[$job_key][2],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty']-$yarn_allocation_array[$job_key][2]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($textile_data_array[$job_key][3],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty']-$textile_data_array[$job_key][3]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty']-$textile_data_array[$job_key][3]),0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($yarn_allocation_array[$job_key][1],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty']-$yarn_allocation_array[$job_key][1]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($yarn_rcv_from_dyeing_array[$job_key],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($dyed_yarn_issue_array[$job_key],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['grey_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($program_data_array[$job_key],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty']-$program_data_array[$job_key]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($knitting_data_array[$job_key][1],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($knitting_data_array[$job_key][3],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($knitting_data_array[$job_key][1]+$knitting_data_array[$job_key][3]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($knitting_data_array[$job_key][1]-$knitting_data_array[$job_key][3]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($knitting_data_array[$job_key][1]-$knitting_data_array[$job_key][3]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][56],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['grey_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][58],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty'] - $roll_data_array[$job_key][58]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty'] - $roll_data_array[$job_key][58]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][82][5],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($roll_data_array[$job_key][58]+$transfer_data_array[$job_key][82][5]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][61],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][82][6],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($roll_data_array[$job_key][61]+$transfer_data_array[$job_key][82][6]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format((($roll_data_array[$job_key][58]+$transfer_data_array[$job_key][82][5]) -($roll_data_array[$job_key][61]+$transfer_data_array[$job_key][82][6])) ,0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['fin_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($textile_data_array[$job_key][62],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][64],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($fab_dyeing_array[$job_key],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($textile_data_array[$job_key][62] - $fab_dyeing_array[$job_key]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($textile_data_array[$job_key][62] - $fab_dyeing_array[$job_key]),0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['fin_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($textile_data_array[$job_key][66],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['fin_fab_qnty'] - $textile_data_array[$job_key][66]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($fab_dyeing_array[$job_key] - $textile_data_array[$job_key][66]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][63],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][65],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($roll_data_array[$job_key][63] - $roll_data_array[$job_key][65]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][67],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['fin_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][68],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['fin_fab_qnty']-$roll_data_array[$job_key][68]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['fin_fab_qnty']-$roll_data_array[$job_key][68]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][134][5],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][134][5],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($textile_data_array[$job_key][71],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][134][6],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][134][6],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($roll_data_array[$job_key][68] - $textile_data_array[$job_key][71]),0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($fin_fab_data_array[$job_key]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($row['job_qty_pcs'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($row['plan_cut_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($lay_data_array[$job_key]['no_of_bndl'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($lay_data_array[$job_key]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][1]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['job_qty_pcs']-$gmts_data_array[$job_key][1]['qty']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['job_qty_pcs']-$gmts_data_array[$job_key][1]['qty']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][4]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($embel_data_array[$job_key][2][1]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($embel_data_array[$job_key][3][1]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="left"><p><?=number_format($company_library[$embel_data_array[$job_key][2][1]['wo_com_id']],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($embel_data_array[$job_key][2][2]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($embel_data_array[$job_key][3][2]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="left"><p><?=$company_library[$embel_data_array[$job_key][2][2]['wo_com_id']];?></p></td>



                                
                                <td width="80" align="right"><p><?=number_format($emb_qty_job_name_arr[$job_key][1],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($print_emb_order_data_array[$job_key][204]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($emb_qty_job_name_arr[$job_key][2],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($print_emb_order_data_array[$job_key][311]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($row['job_qty_pcs'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][4]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['plan_cut_qnty']-$gmts_data_array[$job_key][4]['qty']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][4]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][5]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['plan_cut_qnty']-$gmts_data_array[$job_key][5]['qty']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['plan_cut_qnty']-$gmts_data_array[$job_key][5]['qty']),0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($row['job_qty_pcs'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][7]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][11]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][8]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['plan_cut_qnty']-$gmts_data_array[$job_key][8]['qty']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=$buyer_insp_data[$job_key]['date'];?></p></td>
                                <td width="80" align="right"><p><?=number_format($buyer_insp_data[$job_key]['qty'],0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['cartot_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['ex_qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($invice_data[$job_key]['inv_qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['ex_value'],2);?></p></td>
                                <td width="80" align="right"><p><?=number_format($invice_data[$job_key]['inv_value'],2);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['job_qty_pcs']-$ex_data_array[$job_key]['ex_qty']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['total_price']-$ex_data_array[$job_key]['ex_value']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['job_qty_pcs']-$ex_data_array[$job_key]['ex_qty']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['total_price']-$ex_data_array[$job_key]['ex_value']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['rtn_qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['ex_rtn_value'],0);?></p></td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $ex_data_array = array_unique(array_filter(explode(",", $ex_data_array[$job_key]['ex_date'])));
                                        $ex_date = get_max_min_date($ex_data_array);
                                        echo ($ex_date['min_date']!="") ? change_date_format($ex_date['min_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $ex_data_array = array_unique(array_filter(explode(",", $ex_data_array[$job_key]['ex_date'])));
                                        $ex_date = get_max_min_date($ex_data_array);
                                        echo ($ex_date['max_date']!="") ? change_date_format($ex_date['max_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>



                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>



                                
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                            </tr>
                            <?
                            $i++;
                            $gr_job_total += $row['job_qty_pcs'];
                            $gr_po_total += $row['order_quantity'];
                            $gr_color_size_total += $row['po_quantity'];
                            $gr_plan_cut_total += $row['plan_cut_qnty'];
                            $gr_order_value_total += $row['order_value'];
                        }
                        unset($data_array);
                        ?>                        
                    </tbody>
                </table>
            </div>
            <!-- =============================== report footer ================================ -->
            <table cellspacing="0" border="1" class="rpt_table"   rules="all" width="<?=$tbl_width;?>"  align="center">
                <tfoot>                    
                    <tr>
                        <th width="30"><p></p></th>
                        <th width="60"><p></p></th>
                        <th width="100"><p></p></th>
                        <th width="100"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="100"><p></p></th>
                        <th width="50"><p></p></th>
                        <th width="90"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p>Grand Total</p></th>
                        <th width="80"><p><?=number_format($gr_job_total,0);?></p></th>
                        <th width="80"><p><?=number_format($gr_po_total,0);?></p></th>
                        <th width="80"><p><?=number_format($gr_plan_cut_total,0);?></p></th>
                        <th width="80"><p><?=number_format($gr_color_size_total,0);?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                    </tr>
                </tfoot>
            </table>
        </div>        
    </fieldset>
    <script type="text/javascript">
        // $("#zoom").elevateZoom({easing : true});
    </script>
    <?   

}

if ($action == "report_generate") 
{
    $company_name   = str_replace("'", "", $cbo_company_name);
    $buyer_name     = str_replace("'", "", $cbo_buyer_name);
    $year_id        = str_replace("'", "", $cbo_year);    
    $job_no         = str_replace("'", "", $txt_job_no);
    $txt_style_ref  = str_replace("'", "", $txt_style_ref);
    $txt_order_no   = str_replace("'", "", $txt_order_no);
    $shipmentStatus= str_replace("'", "", $cbo_shipment_status);
    $orderStatus   = str_replace("'","", $cbo_order_status);
    $status         = str_replace("'", "", $cbo_status);
    $report_type    = str_replace("'", "", $cbo_report_type);
    $date_category  = str_replace("'", "", $cbo_date_category);    

    /*===============================================================================/
    /                              Create Library Array                              /
    /============================================================================== */
    $team_library   = return_library_array("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0", "id", "team_leader_name");
    $merchant_library   = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0", "id", "team_member_name");
    $country_library= return_library_array("select id, country_name from  lib_country", "id", "country_name");
    $buyer_library  = return_library_array("select id, buyer_name from  lib_buyer", "id", "buyer_name");
    $color_library  = return_library_array("select id, color_name from  lib_color", "id", "color_name");
    $size_library   = return_library_array("select id, size_name from  lib_size", "id", "size_name");
    $company_library= return_library_array("select id, company_name from  lib_company", "id", "company_name");
    $sub_dep_library= return_library_array("select id,sub_department_name from lib_pro_sub_deparatment status_active =1 and is_deleted=0", "id", "sub_department_name");
    $season_library = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", "id", "season_name");
    $brand_library = return_library_array("select id, brand_name from lib_buyer_brand brand where status_active =1 and is_deleted=0", "id", "brand_name");
    $sub_dep_library = return_library_array("select id,sub_department_name from lib_pro_sub_deparatment where status_active =1 and is_deleted=0", "id", "sub_department_name");
    $user_library = return_library_array("select id,user_full_name from user_passwd where status_active =1 and is_deleted=0", "id", "user_full_name");
    $bank_library = return_library_array("select bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1", "id", "bank_name");

    $fab_material=array(1=>"Organic",2=>"BCI");

    /*===============================================================================/
    /                                   Query Condition                              /
    /============================================================================== */
    $sqlCond = "";
    $sqlCond = ($company_name) ? " and a.company_name=$company_name" : "";
    $sqlCond .= ($buyer_name) ? " and a.buyer_name=$buyer_name" : "";
    $sqlCond .= ($year_id) ? " and to_char(a.insert_date,'YYYY')=$year_id" : "";
    $sqlCond .= ($job_no!="") ? " and a.job_no_prefix_num=$job_no" : "";
    $sqlCond .= ($txt_style_ref!="") ? " and a.style_ref_no='$txt_style_ref'" : "";
    $sqlCond .= ($txt_order_no!="") ? " and b.po_number='$txt_order_no'" : "";
    $sqlCond .= ($orderStatus) ? " and b.is_confirmed=$orderStatus" : "";
    $sqlCond .= ($status) ? " and b.status_active=$status" : "";

    if($shipmentStatus)
    {
        if ($shipmentStatus==4) 
        {
            $sqlCond .= " and b.shiping_status in(1,2)";
        }
        else
        {
            $sqlCond .= " and b.shiping_status=$shipmentStatus";
        }
    }

    if(str_replace("'", "", $txt_date_from) !="" && str_replace("'", "", $txt_date_to) !="")
    {
        switch ($date_category) 
        {
            case 1:
                $sqlCond .= " and b.shipment_date between $txt_date_from and $txt_date_to";
                break;

            case 2:
                $sqlCond .= " and b.shipment_date between $txt_date_from and $txt_date_to";
                break;    
            
            default:
                $sqlCond .= " and b.factory_received_date between $txt_date_from and $txt_date_to";
                break;
        }
    }

    /*===============================================================================/
    /                                  MAIN QUERY                                    /
    /============================================================================== */
    $sql = "SELECT a.id,a.job_no, a.company_name,a.style_owner,a.buyer_name,a.style_ref_no,a.requisition_no,a.style_description,a.repeat_job_no,a.season_buyer_wise,a.product_dept,a.product_code,a.pro_sub_dep,a.order_uom,  a.product_category,a.brand_id,a.region,a.sustainability_standard,a.fab_material,a.qlty_label,a.quality_level,(a.job_quantity*a.total_set_qnty) as job_qty_pcs,    to_char(a.insert_date,'YYYY') as year,a.ship_mode,a.set_smv,a.team_leader,a.dealing_marchant,a.factory_marchant,a.inserted_by,a.total_price,a.qlty_label,

        b.is_confirmed,b.id as po_id,b.po_number,b.excess_cut,(b.po_quantity*a.total_set_qnty) as po_quantity,b.unit_price,b.po_total_price,to_char(b.insert_date,'DD-MM-YYYY') as po_insert_date,to_char(b.po_received_date,'DD-MM-YYYY') as po_received_date,to_char(b.factory_received_date,'DD-MM-YYYY') as factory_received_date,to_char(b.pub_shipment_date,'DD-MM-YYYY') as pub_shipment_date,to_char(b.shipment_date,'DD-MM-YYYY') as shipment_date,to_char(b.txt_etd_ldd,'DD-MM-YYYY') as txt_etd_ldd,b.shiping_status,(b.pub_shipment_date-b.po_received_date) as lead_time,(b.pub_shipment_date - trunc(sysdate)) AS days_in_hand,

        c.country_id,c.item_number_id,c.color_number_id,c.size_number_id,c.order_quantity,c.plan_cut_qnty,to_char(c.cutup_date,'DD-MM-YYYY') as cutup_date,to_char(c.country_ship_date,'DD-MM-YYYY') as country_ship_date

        from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 $sqlCond";
    // echo $sql;die();
    $sql_res = sql_select($sql);
    if(count($sql_res)==0)
    {
        ?>
        <center>
            <div style="width: 80%;" class="alert alert-danger">Data not found.Please try again.</div>
        </center>
        <?
        die();
    }
    $data_array = array();
    $job_id_array = array();
    $job_no_array = array();
    $po_array = array();
    $po_wise_data = array();
    $po_wise_job_arr = array();
    $po_chk_array = array();
    $po_wise_unit_price_array = array();
    foreach ($sql_res as $val) 
    {
        $data_array[$val['JOB_NO']]['company_name'] = $val['COMPANY_NAME'];
        $data_array[$val['JOB_NO']]['buyer_name'] = $val['BUYER_NAME'];
        $data_array[$val['JOB_NO']]['wo_com_id'] = $val['STYLE_OWNER'];
        $data_array[$val['JOB_NO']]['year'] = $val['YEAR'];
        $data_array[$val['JOB_NO']]['style'] = $val['STYLE_REF_NO'];
        $data_array[$val['JOB_NO']]['req_no'] = $val['REQUISITION_NO'];
        $data_array[$val['JOB_NO']]['repeat_job_no'] = $val['REPEAT_JOB_NO'];
        $data_array[$val['JOB_NO']]['style_des'] = $val['STYLE_DESCRIPTION'];
        $data_array[$val['JOB_NO']]['season'] = $val['SEASON_BUYER_WISE'];
        $data_array[$val['JOB_NO']]['product_dept'] = $val['PRODUCT_DEPT'];
        $data_array[$val['JOB_NO']]['product_code'] = $val['PRODUCT_CODE'];
        $data_array[$val['JOB_NO']]['pro_sub_dep'] = $val['PRO_SUB_DEP'];
        $data_array[$val['JOB_NO']]['product_category'] = $val['PRODUCT_CATEGORY'];
        $data_array[$val['JOB_NO']]['brand_id'] = $val['BRAND_ID'];
        $data_array[$val['JOB_NO']]['region'] = $val['REGION'];
        $data_array[$val['JOB_NO']]['sustainability_standard'] = $val['SUSTAINABILITY_STANDARD'];
        $data_array[$val['JOB_NO']]['fab_material'] = $val['FAB_MATERIAL'];
        $data_array[$val['JOB_NO']]['order_nature'] = $val['QLTY_LABEL'];
        $data_array[$val['JOB_NO']]['quality_level'] = $val['QUALITY_LEVEL'];
        $data_array[$val['JOB_NO']]['qlty_label'] = $val['QLTY_LABEL'];
        $data_array[$val['JOB_NO']]['job_qty_pcs'] = $val['JOB_QTY_PCS'];
        $data_array[$val['JOB_NO']]['order_uom'] = $val['ORDER_UOM'];
        $data_array[$val['JOB_NO']]['ship_mode'] = $val['SHIP_MODE'];
        $data_array[$val['JOB_NO']]['set_smv'] = $val['SET_SMV'];
        $data_array[$val['JOB_NO']]['team_leader'] = $val['TEAM_LEADER'];
        $data_array[$val['JOB_NO']]['dealing_marchant'] = $val['DEALING_MARCHANT'];
        $data_array[$val['JOB_NO']]['factory_marchant'] = $val['FACTORY_MARCHANT'];
        $data_array[$val['JOB_NO']]['inserted_by'] = $val['INSERTED_BY'];
        $data_array[$val['JOB_NO']]['total_price'] = $val['TOTAL_PRICE'];

        $data_array[$val['JOB_NO']]['po_number'] .= $val['PO_NUMBER'].",";
        $data_array[$val['JOB_NO']]['order_status'] .= $val['IS_CONFIRMED'].",";
        $po_wise_data[$val['JOB_NO']]['excess_cut'] .= $val['PO_NUMBER']."=".$val['EXCESS_CUT']."**";

        $data_array[$val['JOB_NO']]['country_id'] .= $val['COUNTRY_ID'].",";
        $data_array[$val['JOB_NO']]['item_id'] .= $val['ITEM_NUMBER_ID'].",";
        $data_array[$val['JOB_NO']]['color_id'] .= $val['COLOR_NUMBER_ID'].",";
        $data_array[$val['JOB_NO']]['size_id'] .= $val['SIZE_NUMBER_ID'].",";
        $data_array[$val['JOB_NO']]['order_quantity'] += $val['ORDER_QUANTITY'];
        $data_array[$val['JOB_NO']]['plan_cut_qnty'] += $val['PLAN_CUT_QNTY'];
        if(!in_array($val['PO_ID'], $po_chk_array))
        {
            $data_array[$val['JOB_NO']]['po_quantity'] += $val['PO_QUANTITY'];
            $data_array[$val['JOB_NO']]['order_value'] += $val['PO_TOTAL_PRICE'];
            $data_array[$val['JOB_NO']]['unit_price'] .= ($data_array[$val['JOB_NO']]['unit_price']=="") ? $val['UNIT_PRICE'] : ", ".$val['UNIT_PRICE'];
            $data_array[$val['JOB_NO']]['po_insert_date'] .= ($data_array[$val['JOB_NO']]['po_insert_date']=="") ? $val['PO_INSERT_DATE'] : ", ".$val['PO_INSERT_DATE'];
            $data_array[$val['JOB_NO']]['po_received_date'] .= ($data_array[$val['JOB_NO']]['po_received_date']=="") ? $val['PO_RECEIVED_DATE'] : ", ".$val['PO_RECEIVED_DATE'];
            $data_array[$val['JOB_NO']]['factory_received_date'] .= ($data_array[$val['JOB_NO']]['factory_received_date']=="") ? $val['FACTORY_RECEIVED_DATE'] : ", ".$val['FACTORY_RECEIVED_DATE'];
            $data_array[$val['JOB_NO']]['pub_shipment_date'] .= ($data_array[$val['JOB_NO']]['pub_shipment_date']=="") ? $val['PUB_SHIPMENT_DATE'] : ", ".$val['PUB_SHIPMENT_DATE'];
            $data_array[$val['JOB_NO']]['shipment_date'] .= ($data_array[$val['JOB_NO']]['shipment_date']=="") ? $val['SHIPMENT_DATE'] : ", ".$val['SHIPMENT_DATE'];
            $data_array[$val['JOB_NO']]['txt_etd_ldd'] .= ($data_array[$val['JOB_NO']]['txt_etd_ldd']=="") ? $val['TXT_ETD_LDD'] : ", ".$val['TXT_ETD_LDD'];
            $data_array[$val['JOB_NO']]['shipment_status'] .= ($data_array[$val['JOB_NO']]['shipment_status']=="") ? $shipment_status[$val['SHIPING_STATUS']] : ", ".$shipment_status[$val['SHIPING_STATUS']];
            $data_array[$val['JOB_NO']]['lead_time'] .= ($data_array[$val['JOB_NO']]['lead_time']=="") ? $val['LEAD_TIME'] : ", ".$val['LEAD_TIME'];
            $data_array[$val['JOB_NO']]['days_in_hand'] .= ($data_array[$val['JOB_NO']]['days_in_hand']=="") ? $val['DAYS_IN_HAND'] : ", ".$val['DAYS_IN_HAND'];

            $po_chk_array[$val['PO_ID']] = $val['PO_ID'];
        }
        $data_array[$val['JOB_NO']]['cutup_date'] .= ($data_array[$val['JOB_NO']]['cutup_date']=="") ? $val['CUTUP_DATE'] : ",".$val['CUTUP_DATE'];
        $data_array[$val['JOB_NO']]['country_ship_date'] .= ($data_array[$val['JOB_NO']]['country_ship_date']=="") ? $val['COUNTRY_SHIP_DATE'] : ",".$val['COUNTRY_SHIP_DATE'];


        $job_id_array[$val['ID']] = $val['ID'];
        $job_no_array[$val['JOB_NO']] = $val['JOB_NO'];
        $po_array[$val['PO_ID']] = $val['PO_ID'];
        $po_wise_job_arr[$val['PO_ID']] = $val['JOB_NO'];
        $po_wise_unit_price_array[$val['PO_ID']] = $val['UNIT_PRICE'];
    }

    unset($sql_res);
    // echo "<pre>";print_r($po_array);echo "</pre>";die();
    /*===============================================================================/
    /                                  Job Image                                     /
    /============================================================================== */
    $job_cond = where_con_using_array($job_no_array,1,"master_tble_id");

    $imge_arr = return_library_array("SELECT master_tble_id,image_location from common_photo_library where file_type=1 $job_cond", 'master_tble_id', 'image_location');

    /*===============================================================================/
    /                                  Embel Name                                    /
    /============================================================================== */
    $job_cond = where_con_using_array($job_id_array,1,"job_id");
    $sql = "SELECT job_no,emb_name from wo_pre_cost_embe_cost_dtls where status_active=1 and is_deleted=0 and emb_type!=0 $job_cond";
    // echo $sql;
    $res = sql_select($sql);
    $emb_name_arr = array();
    $emb_id_chk_arr = array();
    foreach ($res as $val) 
    {
        if(!in_array($val['EMB_NAME'], $emb_id_chk_arr[$val['JOB_NO']]))
        {
            $emb_name_arr[$val['JOB_NO']] .= ($emb_name_arr[$val['JOB_NO']]=="") ? $emblishment_name_array[$val['EMB_NAME']] : ",".$emblishment_name_array[$val['EMB_NAME']];
            $emb_id_chk_arr[$val['JOB_NO']][$val['EMB_NAME']] = $val['EMB_NAME'];
        }
    }

    /*===============================================================================/
    /                                Conversion Name                                 /
    /============================================================================== */
    $job_cond = where_con_using_array($job_id_array,1,"job_id");
    $sql = "SELECT job_no,cons_process from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 $job_cond";
    // echo $sql;
    $res = sql_select($sql);
    $conv_name_arr = array();
    $conv_id_chk_arr = array();
    foreach ($res as $val) 
    {
        if(!in_array($val['CONS_PROCESS'], $conv_id_chk_arr[$val['JOB_NO']]))
        {
            $conv_name_arr[$val['JOB_NO']] .= ($conv_name_arr[$val['JOB_NO']]=="") ? $conversion_cost_head_array[$val['CONS_PROCESS']] : ",".$conversion_cost_head_array[$val['CONS_PROCESS']];
            $conv_id_chk_arr[$val['JOB_NO']][$val['CONS_PROCESS']] = $val['CONS_PROCESS'];
        }
    }
    // echo "<pre>";print_r($conv_id_chk_arr);die();
    /*===============================================================================/
    /                                  ACTUAL PO                                     /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"po_break_down_id");
    $sql = "SELECT job_no, acc_po_no FROM wo_po_acc_po_info where status_active=1 and is_deleted=0 $po_cond";
    $res = sql_select($sql);
    $acc_po_array = array();
    $acc_po_chk = array();
    foreach ($res as $key => $val) 
    {
        if(!in_array($val['ACC_PO_NO'], $acc_po_chk[$val['JOB_NO']]))
        {
            $acc_po_array[$val['JOB_NO']]['acc_po'] .= ($acc_po_array[$val['JOB_NO']]['acc_po']=="") ? $val['ACC_PO_NO'] : ", ".$val['ACC_PO_NO'];
            $acc_po_chk[$val['JOB_NO']][$val['ACC_PO_NO']] = $val['ACC_PO_NO'];
        }
    }

    /*===============================================================================/
    /                                  Booking Data                                  /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"b.po_break_down_id");
    $sql = "SELECT a.entry_form,a.process, b.job_no,a.item_category,to_char(a.booking_date,'DD-MM-YYYY') as booking_date,to_char(a.delivery_date,'DD-MM-YYYY') as delivery_date,a.booking_no,a.booking_no_prefix_num, b.po_break_down_id as po_id,a.booking_type,a.fabric_source,a.is_short,a.revised_no, b.fin_fab_qnty,b.grey_fab_qnty,a.is_approved,b.dia_width,b.gsm_weight,b.process_loss_percent,b.construction,b.copmposition,a.uom from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $booking_data_array = array();
    $pur_booking_data_array = array();
    $service_booking_data_array = array();
    $booking_qty_array = array();
    foreach ($res as $val) 
    {
        if($val['ITEM_CATEGORY']==2)
        {
            if($val['FABRIC_SOURCE']==1)
            {
            
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['booking_date'] .= $val['BOOKING_DATE'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['booking_no'] .= $val['BOOKING_NO'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['uom'] .= $val['UOM'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['delivery_date'] .= $val['DELIVERY_DATE'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['revised_no'] .= $val['REVISED_NO'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['is_approved'] .= $val['IS_APPROVED'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['dia_width'] .= $val['DIA_WIDTH'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['gsm_weight'] .= $val['GSM_WEIGHT'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['process_loss'] .= $val['PROCESS_LOSS_PERCENT'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['construction'] .= $val['CONSTRUCTION']."__";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['copmposition'] .= $val['COPMPOSITION']."__";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['fin_fab_qnty'] += $val['FIN_FAB_QNTY'];
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['grey_fab_qnty'] += $val['GREY_FAB_QNTY'];
            }
            else
            {
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['booking_date'] .= $val['BOOKING_DATE'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['booking_no'] .= $val['BOOKING_NO'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['uom'] .= $val['UOM'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['delivery_date'] .= $val['DELIVERY_DATE'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['revised_no'] .= $val['REVISED_NO'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['is_approved'] .= $val['IS_APPROVED'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['dia_width'] .= $val['DIA_WIDTH'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['gsm_weight'] .= $val['GSM_WEIGHT'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['process_loss'] .= $val['PROCESS_LOSS_PERCENT'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['construction'] .= $val['CONSTRUCTION']."__";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['copmposition'] .= $val['COPMPOSITION']."__";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['fin_fab_qnty'] += $val['FIN_FAB_QNTY'];
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['grey_fab_qnty'] += $val['GREY_FAB_QNTY'];
            }
            $booking_qty_array[$po_wise_job_arr[$val['PO_ID']]]['grey_fab_qnty'] += $val['GREY_FAB_QNTY'];
            $booking_qty_array[$po_wise_job_arr[$val['PO_ID']]]['fin_fab_qnty'] += $val['FIN_FAB_QNTY'];
        }

        if($val['ENTRY_FORM']==228)
        {
            $service_booking_data_array[$val['JOB_NO']]['knitting'] = $val['BOOKING_NO_PREFIX_NUM'];
        }

        if($val['ENTRY_FORM']==229)
        {
            $service_booking_data_array[$val['JOB_NO']]['dyeing'] = $val['BOOKING_NO_PREFIX_NUM'];
        }

        if($val['PROCESS']==35)
        {
            $service_booking_data_array[$val['JOB_NO']]['aop'] = $val['BOOKING_NO_PREFIX_NUM'];
        }
       
    }
    // echo "<pre>";print_r($booking_data_array);echo "</pre>";die();
    /*===============================================================================/
    /                                    Work order data                             /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"d.id");
    $sql="SELECT a.job_no,c.emb_name, f.booking_no from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d, wo_pre_cos_emb_co_avg_con_dtls e, wo_booking_dtls f
    where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.job_no=f.job_no and c.id=e.pre_cost_emb_cost_dtls_id and d.id=e.po_break_down_id and e.pre_cost_emb_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=6 and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $work_order_data_array = array();
    foreach ($res as $val) 
    {
        $bek = explode("-", $val['BOOKING_NO']);
        $work_order_data_array[$val['JOB_NO']][$val['EMB_NAME']] = $bek[3];
        
    }
    // echo "<pre>";print_r($work_order_data_array);die;
    /*===============================================================================/
    /                            YARN DYEING WORK ORDER DATA                         /
    /============================================================================== */
    $job_id_cond = where_con_using_array($job_id_array,0,"b.job_no_id");
    $sql="SELECT b.job_no,a.yarn_dyeing_prefix_num from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
    where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $job_id_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $yd_work_order_data_array = array();
    foreach ($res as $val) 
    {
        $yd_work_order_data_array[$val['JOB_NO']] = $val['YARN_DYEING_PREFIX_NUM'];
        
    }
    /*===============================================================================/
    /                                   YARN Allocation                              /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_break_down_id");
    $sql = "SELECT a.job_no,a.is_dyied_yarn,a.qnty from inv_material_allocation_dtls a where a.status_active=1 and a.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $yarn_allocation_array = array();
    foreach ($res as $val) 
    {
        $yarn_allocation_array[$val['JOB_NO']][$val['IS_DYIED_YARN']] += $val['QNTY'];
    }
    /*===============================================================================/
    /                                      Textile Data                              /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_breakdown_id");
    $sql = "SELECT a.po_breakdown_id,a.entry_form,a.trans_type,a.quantity from order_wise_pro_details a where a.status_active=1 and a.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $textile_data_array = array();
    $transfer_data_array = array();
    foreach ($res as $val) 
    {
        $textile_data_array[$po_wise_job_arr[$val['PO_BREAKDOWN_ID']]][$val['ENTRY_FORM']] += $val['QUANTITY'];
        $transfer_data_array[$po_wise_job_arr[$val['PO_BREAKDOWN_ID']]][$val['ENTRY_FORM']][$val['TRANS_TYPE']] += $val['QUANTITY'];
    }
    // echo "<pre>";print_r($textile_data_array);die;

    /*===============================================================================/
    /                             Yarn Rcv From Dyeing                               /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"c.po_breakdown_id");
    $sql = "SELECT c.po_breakdown_id as po_id, c.QUANTITY  from inv_receive_master a, inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and b.item_category=1 and a.entry_form=1 and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in(2) $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $yarn_rcv_from_dyeing_array = array();
    foreach ($res as $val) 
    {
        $yarn_rcv_from_dyeing_array[$po_wise_job_arr[$val['PO_ID']]] += $val['QUANTITY'];
    }
    // echo "<pre>";print_r($yarn_rcv_from_dyeing_array);die;

    /*===============================================================================/
    /                               knitting plan data                               /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_id");
    $sql="SELECT a.program_qnty,a.po_id from ppl_planning_entry_plan_dtls a where a.status_active=1 and a.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $program_data_array = array();
    foreach ($res as $val) 
    {
        $program_data_array[$po_wise_job_arr[$val['PO_ID']]] += $val['PROGRAM_QNTY'];
    }

    /*===============================================================================/
    /                               knitting production                              /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"c.po_breakdown_id");
    $sql="SELECT a.knitting_source,c.po_breakdown_id,c.quantity from inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form=2 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $knitting_data_array = array();
    foreach ($res as $val) 
    {
        $knitting_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['KNITTING_SOURCE']] += $val['PROGRAM_QNTY'];
    }

    /*===============================================================================/
    /                               Dyed yarn issue                                  /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"c.po_breakdown_id");
    $sql = "SELECT c.po_breakdown_id as po_id, c.QUANTITY  from inv_issue_master a, inv_transaction b,order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and d.id=c.prod_id and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.dyed_type=1 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $dyed_yarn_issue_array = array();
    foreach ($res as $val) 
    {
        $dyed_yarn_issue_array[$po_wise_job_arr[$val['PO_ID']]] += $val['QUANTITY'];
    }
    // echo "<pre>";print_r($dyed_yarn_issue_array);die;

    /*===============================================================================/
    /                                Roll Data                         
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_breakdown_id");
    $sql = "SELECT a.po_breakdown_id,a.entry_form,a.qnty from pro_roll_details a where a.status_active=1 and a.is_deleted=0  $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $roll_data_array = array();
    foreach ($res as $val) 
    {
        $roll_data_array[$po_wise_job_arr[$val['PO_BREAKDOWN_ID']]][$val['ENTRY_FORM']] += $val['QNTY'];
    }

    /*===============================================================================/
    /                                Fab Dyeing Prodduction                          /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_id");
    $sql = "SELECT c.load_unload_id,a.po_id,c.batch_qty,c.production_qty from pro_batch_create_dtls a, pro_fab_subprocess b,pro_fab_subprocess_dtls c where a.mst_id=b.batch_id and b.id=c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.load_unload_id=1 and b.result=1 and b.entry_form=35 $po_cond";
    // echo $sql;die;
    $res = sql_select($sql);
    $fab_dyeing_array = array();
    foreach ($res as $val) 
    {
        $fab_dyeing_array[$po_wise_job_arr[$val['PO_ID']]] += $val['PRODUCTION_QTY'];
    }

    /*===============================================================================/
    /                                  Finish Fabric Issue                           /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"c.po_breakdown_id");
    $sql = "SELECT c.po_breakdown_id as po_id,c.quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.entry_form=71 and trans_type=2 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $fin_fab_data_array = array();
    foreach ($res as $val) 
    {
        $fin_fab_data_array[$po_wise_job_arr[$val['PO_ID']]]['qty'] + $val['QUANTITY'];
    }

    /*===============================================================================/
    /                                   Cut & Lay data                               /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.order_id");
    $sql = "SELECT b.entry_date, a.order_id, a.bundle_no,a.size_qty from ppl_cut_lay_bundle a,ppl_cut_lay_mst b where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond";
    // echo $sql;die;
    $res = sql_select($sql);
    $lay_data_array = array();
    foreach ($res as $val) 
    {
        $lay_data_array[$po_wise_job_arr[$val['ORDER_ID']]]['entry_date'] .= $val['ENTRY_DATE'].",";
        $lay_data_array[$po_wise_job_arr[$val['ORDER_ID']]]['qty'] += $val['SIZE_QTY'];
        $lay_data_array[$po_wise_job_arr[$val['ORDER_ID']]]['no_of_bndl']++;
    }

    /*===============================================================================/
    /                                  Gmts Production                               /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_break_down_id");
    $sql = "SELECT a.production_source,a.serving_company, a.production_date,a.production_type,a.po_break_down_id as po_id,b.production_qnty,a.embel_name from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond order by a.production_date";
    // echo $sql;
    $res = sql_select($sql);
    $gmts_data_array = array();
    $embel_data_array = array();
    foreach ($res as $val) 
    {
        $gmts_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']]['date'] .= $val['PRODUCTION_DATE'].",";
        $gmts_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']]['qty'] += $val['PRODUCTION_QNTY'];

        $embel_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['date'] .= $val['PRODUCTION_DATE'].",";
        $embel_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['source'] .= $val['PRODUCTION_SOURCE'].",";
        $embel_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['wo_com_id'] .= $val['SERVING_COMPANY'].",";
        $embel_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['qty'] += $val['PRODUCTION_QNTY'];
    }
    // echo "<pre>";print_r($gmts_data_array);echo "</pre>";die();

    /*===============================================================================/
    /                            Printing & Emb Order Data                           /
    /============================================================================== */
    // 204=printing, 311=emb
    $po_cond = where_con_using_array($po_array,0,"b.buyer_po_no");
    $sql = "SELECT a.entry_form, b.buyer_po_no as po_id, b.order_quantity from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_deleted=0 and a.within_group=1 and a.entry_form in(204,311) $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $print_emb_order_data_array = array();
    foreach ($res as $val) 
    {
        $print_emb_order_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['ENTRY_FORM']]['qty'] += $val['ORDER_QUANTITY'];
    }

    /*===============================================================================/
    /                                 Buyer Inspection                               /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_break_down_id");
    $sql = "SELECT a.job_no,a.inspection_qnty,to_char(a.inspection_date,'DD-MM-YYYY') as inspection_date from pro_buyer_inspection a where a.status_active=1 and a.is_deleted=0 $po_cond";
    $res = sql_select($sql);
    $buyer_insp_data = array();
    foreach ($res as $val) 
    {
        $buyer_insp_data[$val['JOB_NO']]['qty'] += $val['INSPECTION_QNTY'];
        $buyer_insp_data[$val['JOB_NO']]['date'] = $val['INSPECTION_DATE'];
    }

    /*===============================================================================/
    /                                  ex-factory data                               /
    /============================================================================== */
    $sql = "SELECT a.id,a.po_break_down_id as po_id,a.ex_factory_date,(case when a.entry_form !=85 then b.production_qnty else 0 end) as production_qnty,(case when a.entry_form =85 then b.production_qnty else 0 end) as return_qnty,a.total_carton_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $ex_data_array = array();
    $id_chk_array = array();
    foreach ($res as $val) 
    {
        $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['ex_date'] .= $val['EX_FACTORY_DATE'].",";
        $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['ex_qty'] += $val['PRODUCTION_QNTY'];
        $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['rtn_qty'] += $val['RETURN_QNTY'];
        $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['ex_value'] += ($val['PRODUCTION_QNTY']*$po_wise_unit_price_array[$val['PO_ID']]);
        $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['ex_rtn_value'] += ($val['RETURN_QNTY']*$po_wise_unit_price_array[$val['PO_ID']]);
        if(!in_array($val['ID'], $id_chk_array))
        {
            $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['cartot_qnty'] += $val['TOTAL_CARTON_QNTY'];
            $id_chk_array[$val['ID']] = $val['ID'];
        }
    }
    // echo "<pre>";print_r($ex_data_array);die();
    /*===============================================================================/
    /                                 Export Invoice                                 /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"b.po_breakdown_id");
    $sql = "SELECT b.po_breakdown_id as po_id,b.current_invoice_qnty,b.current_invoice_value,a.invoice_no from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $po_cond";
    $res = sql_select($sql);
    $invice_data = array();
    foreach ($res as $val) 
    {
        $invice_data[$po_wise_job_arr[$val['PO_ID']]]['inv_qty']+=$val['CURRENT_INVOICE_QNTY'];
        $invice_data[$po_wise_job_arr[$val['PO_ID']]]['inv_value']+=$val['CURRENT_INVOICE_VALUE'];
    }

    /*===============================================================================/
    /                                      LC/SC data                                /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"b.wo_po_break_down_id");
    $sql = "SELECT b.wo_po_break_down_id as po_id,a.lien_bank,a.pay_term,a.internal_file_no,a.tenor,max(c.amendment_no) as amendment_no from com_export_lc a,com_export_lc_order_info b, com_export_lc_amendment c where a.id=b.com_export_lc_id AND a.export_lc_no = c.export_lc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond group by b.wo_po_break_down_id,a.lien_bank,a.pay_term,a.internal_file_no,a.tenor
    UNION ALL
    SELECT b.wo_po_break_down_id as po_id,a.lien_bank,a.pay_term,a.internal_file_no,a.tenor,max(c.amendment_no) as amendment_no from com_sales_contract a,com_sales_contract_order_info b,com_sales_contract_amendment c where a.id=b.com_sales_contract_id and a.contract_no=c.contract_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond group by b.wo_po_break_down_id,a.lien_bank,a.pay_term,a.internal_file_no,a.tenor
    ";
    // echo $sql;die();
    $res = sql_select($sql);
    $lc_sc_data_array = array();
    foreach ($res as $val) 
    {
        $lc_sc_data_array[$po_wise_job_arr[$val['PO_ID']]]['lien_bank'] .= $bank_library[$val['LIEN_BANK']].",";
        $lc_sc_data_array[$po_wise_job_arr[$val['PO_ID']]]['pay_term'] .= $pay_term[$val['PAY_TERM']].",";
        $lc_sc_data_array[$po_wise_job_arr[$val['PO_ID']]]['internal_file_no'] .= $val['INTERNAL_FILE_NO'].",";
        $lc_sc_data_array[$po_wise_job_arr[$val['PO_ID']]]['tenor'] .= $val['TENOR'].",";
        $lc_sc_data_array[$po_wise_job_arr[$val['PO_ID']]]['amendment_no'] .= $val['AMENDMENT_NO'].",";
    }
    // echo "<pre>";print_r($lc_sc_data_array);die();

    /*===============================================================================/
    /                                      TNA Data                                  /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_number_id");
    $sql = "SELECT a.po_number_id,a.task_number,max(a.task_start_date) as start_date,max(a.task_finish_date) as end_date,max(a.actual_start_date) as actual_start_date,max(a.actual_finish_date) as actual_finish_date,b.job_no_mst from tna_process_mst a,wo_po_break_down b where a.po_number_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.task_start_date is not null and b.po_quantity>0 $po_cond and a.task_type=1 group by a.po_number_id,a.task_number,b.job_no_mst";
    // echo $sql;die();
    $res = sql_select($sql);
    $tnaDateArray = array();
    foreach ($res as $val) 
    {
        $tnaDateArray[$val['JOB_NO_MST']][$val['TASK_NUMBER']]['start_date']          = $val['START_DATE'];
        $tnaDateArray[$val['JOB_NO_MST']][$val['TASK_NUMBER']]['end_date']            = $val['END_DATE'];
        $tnaDateArray[$val['JOB_NO_MST']][$val['TASK_NUMBER']]['actual_start_date']   = $val['ACTUAL_START_DATE'];
        $tnaDateArray[$val['JOB_NO_MST']][$val['TASK_NUMBER']]['actual_finish_date']  = $val['ACTUAL_FINISH_DATE'];
    }

    // echo "<pre>";print_r($tnaDateArray);die();
    /*===============================================================================/
    /                                    Budget Data                                 /
    /============================================================================== */
    $job_id_cond = where_con_using_array($job_id_array,0,"a.job_id");
    $sql = "SELECT a.job_no,to_char(a.costing_date,'DD-MM-YYYY') as costing_date,a.approved,b.cm_cost from wo_pre_cost_mst a,wo_pre_cost_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_id=b.job_id $job_id_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $budget_data_array = array();
    foreach ($res as $val) 
    {
        $budget_data_array[$val['JOB_NO']]['costing_date'] = $val['COSTING_DATE'];
        $budget_data_array[$val['JOB_NO']]['approved'] = $val['APPROVED'];
        $budget_data_array[$val['JOB_NO']]['cm_cost'] = $val['CM_COST'];
    }

    /*===============================================================================/
    /                              Getting data from class                           /
    /============================================================================== */
    $poIDS = implode(",", $po_array);
    $condition= new condition();     
    $condition->po_id_in($poIDS);     
    $condition->init();
    // $fabric= new fabric($condition);
    $yarn= new yarn($condition);
    $yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
    $yarn_qty_amount_arr=$yarn->getJobWiseYarnQtyAndAmountArray();

    $yarnDataWithFabricidArr=$yarn->get_By_Precostfabricdtlsid_YarnQtyAmountArray();

    $fabric= new fabric($condition);
    $fabricAmoutByFabricSource= $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
    $fabricQtyByFabricSource= $fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish_purchase();
    
    $fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
    $fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
    $conversion= new conversion($condition);
    $conversion_costing_arr_process=$conversion->getAmountArray_by_job();
    $conv_qty_job_process= $conversion->getQtyArray_by_jobAndProcess();
    $conv_amount_job_process= $conversion->getAmountArray_by_jobAndProcess();
    $con_qty_fabric_process = $conversion->getQtyArray_by_fabricAndProcess();
    $con_amount_fabric_process = $conversion->getAmountArray_by_fabricAndProcess();

    $trims= new trims($condition);
    $trims_costing_arr=$trims->getAmountArray_by_job();
    $trims_qty_arr=$trims->getQtyArray_by_job();

    $emblishment= new emblishment($condition);
    $emblishment_costing_arr=$emblishment->getAmountArray_by_job();
    $emb_qty_job_name_arr = $emblishment->getQtyArray_by_jobAndEmbname();
    $emb_amount_job_name_arr = $emblishment->getAmountArray_by_jobAndEmbname();

    $wash= new wash($condition);
    $emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
    $wash_qty_job_name_arr =$wash->getQtyArray_by_jobAndEmbname();
    $wash_amount_job_name_arr =$wash->getAmountArray_by_jobAndEmbname();


    $commercial= new commercial($condition);
    $commercial_costing_arr=$commercial->getAmountArray_by_job();
    $commission= new commision($condition);
    $commission_costing_arr=$commission->getAmountArray_by_job();
    $other= new other($condition);
    $other_costing_arr=$other->getAmountArray_by_job();


    /*===============================================================================/
    /                        Functin for getting max min date                        /
    /============================================================================== */
    function get_max_min_date($date_arr)
    {
        $date_arr = explode(",", implode(",", $date_arr));
        $date_array = array();
        for ($i = 0; $i < count($date_arr); $i++)
        {
            if ($i == 0)
            {
                $max_date = date('d-m-Y', strtotime($date_arr[$i]));
                $min_date = date('d-m-Y', strtotime($date_arr[$i]));
                $date_array['max_date'] = $max_date;
                $date_array['min_date'] = $min_date;
            }
            elseif ($i != 0)
            {
                $new_date = date('d-m-Y', strtotime($date_arr[$i]));
                if ($new_date > $max_date)
                {
                    $max_date = $new_date;
                    $date_array['max_date'] = $max_date;
                }
                elseif ($new_date < $min_date)
                {
                    $min_date = $new_date;
                    $date_array['min_date'] = $min_date;
                }
            }
        }
        return $date_array;
    }



    $tbl_width = 25330;

    ob_start();
    ?>
    <fieldset>
        <div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
            <h2>Order Tracking Report</h2>
            <h2>Company : <?=$company_arr[$company_name]; ?></h2>
            <h2>Date : <?=change_date_format($date_from); ?>To<?=change_date_format($date_to); ?> </h2>
        </div>
        <style type="text/css">
            table thead tr td{
                padding: 5px;text-align: center;font-weight: bold;font-size: 16px;
            }
            .zoom {
              padding: 0px;
              transition: transform .2s; 
              width: 30px;
              height: 30px;
              margin: 0 auto;
              z-index: 9999 !important;
              overflow: hidden !important;
              visibility: visible;
              position: relative;
            }

            .zoom:hover {
              transform: scale(10); 

              z-index: 9999 !important;
              overflow: hidden !important;
              visibility: visible;
            }
        </style>
        <div class="report-container-part">
            <!-- ================================= report header ================================== -->
            <table cellspacing="0" border="1" class="rpt_table"   rules="all" width="<?=$tbl_width;?>"  align="center" id="table_header">
                <thead>
                    <tr style="">
                        <td style="background: #95DAC1;" colspan="88">Order Information</td>
                        <td style="background: #FACE7F;" colspan="5">Budget Information</td>
                        <td style="background: #5C7AEA;" colspan="14">Main Fabric Booking Information</td>
                        <td style="background: #F56FAD;" colspan="7">Purchase Fabric Booking Information</td>
                        <td style="background: #A2CDCD;" colspan="9">Short/EFR Fabric Booking Information</td>
                        <th width="80" rowspan="2"><p>Trims/Acc Status</p></th>
                        <td style="background: #F0D9FF;" colspan="8">Grey Yarn Status</td>
                        <td style="background: #B1E693;" colspan="8">Dyed Yarn Status</td>
                        <td style="background: #63B4B8;" colspan="7">Yarn Dyed Production Status</td>
                        <td style="background: #E05D5D;" colspan="13">Knitting Status</td>
                        <td style="background: #C8C6C6;" colspan="10">Grey Fabric Store Status</td>
                        <td style="background: #BFA2DB;" colspan="6">Dyeing Status</td>
                        <td style="background: #6B7AA1;" colspan="12">Dyeing Finishing Status</td>
                        <td style="background: #C1CFC0;" colspan="10">Finish Fabric Store Status</td>
                        <td style="background: #8FC1D4;" colspan="23">Cutting Status</td>
                        <td style="background: #FFF5B7;" colspan="7">Print Status</td>
                        <td style="background: #FF449F;" colspan="7">Embroidery Status</td>
                        <td style="background: #EBA83A;" colspan="7">Sewing Status</td>
                        <td style="background: #B5FFD9;" colspan="9">Garments Finishing Status</td>
                        <td style="background: #E798AE;" colspan="16">Shipment Status</td>
                        <td style="background: #867AE9;" colspan="25">Budget VS Actual</td>
                        <td style="background: #C449C2;" colspan="25">Order Activities/TNA</td>
                    </tr>
                    <tr>
                        <th width="30"><p>Sl.</p></th>
                        <th width="60"><p>Order Status</p></th>
                        <th width="100"><p>LC Company</p></th>
                        <th width="100"><p>Working Company</p></th>
                        <th width="80"><p>Image</p></th>
                        <th width="100"><p>Buyer</p></th>
                        <th width="50"><p>Year</p></th>
                        <th width="90"><p>Job</p></th>
                        <th width="80"><p>Repeat Job No</p></th>
                        <th width="80"><p>Sample Req. No</p></th>
                        <th width="80"><p>Style Ref</p></th>
                        <th width="80"><p>Style Desc</p></th>
                        <th width="80"><p>Gmts Item</p></th>
                        <th width="80"><p>Season</p></th>
                        <th width="80"><p>Prod Dept</p></th>
                        <th width="80"><p>Prod. Dept Code / Class</p></th>
                        <th width="80"><p>Sub Dept</p></th>
                        <th width="80"><p>Brand</p></th>
                        <th width="80"><p>Region</p></th>
                        <th width="80"><p>Prod. Catg</p></th>
                        <th width="80"><p>Country</p></th>
                        <th width="80"><p>Sustainability Standard</p></th>
                        <th width="80"><p>Fabric Material</p></th>
                        <th width="80"><p>Order Nature</p></th>
                        <th width="80"><p>Quality Label</p></th>
                        <th width="80"><p>Embilshement Name</p></th>
                        <th width="80"><p>Service Name</p></th>
                        <th width="80"><p>Order/PO No</p></th>
                        <th width="80"><p>Actual Order/PO No</p></th>
                        <th width="80"><p>GMT Color</p></th>
                        <th width="80"><p>Size</p></th>
                        <th width="80"><p>Ex Cut %</p></th>
                        <th width="80"><p>Order Qnty [Pcs]</p></th>
                        <th width="80"><p>Break Down Order Qnty [Pcs]</p></th>
                        <th width="80"><p>Order Qnty with Cut% [Pcs]</p></th>
                        <th width="80"><p>Order Qnty [Uom]</p></th>
                        <th width="80"><p>Uom</p></th>
                        <th width="80"><p>Per Unit Price</p></th>
                        <th width="80"><p>Order Value</p></th>
                        <th width="80"><p>PO Insert Date</p></th>
                        <th width="80"><p>PO Receive Date</p></th>
                        <th width="80"><p>Factory Receive Date</p></th>
                        <th width="80"><p>1st Cut Date</p></th>
                        <th width="80"><p>Last Cut Date</p></th>
                        <th width="80"><p>1st Sew Date</p></th>
                        <th width="80"><p>Last Sew Date</p></th>
                        <th width="80"><p>1st Finish Date</p></th>
                        <th width="80"><p>Last Finish Date</p></th>
                        <th width="80"><p>Insp. Offer Date</p></th>
                        <th width="80"><p>Insp. Date</p></th>
                        <th width="80"><p>Pub. Shipment Date</p></th>
                        <th width="80"><p>First Shipment Date </p></th>
                        <th width="80"><p>Org. Shipment Date </p></th>
                        <th width="80"><p>ETD/LDD Date</p></th>
                        <th width="80"><p>Cut-off Date</p></th>
                        <th width="80"><p>Country Shipment Date</p></th>
                        <th width="80"><p>RFI Plan Date</p></th>
                        <th width="80"><p>Shipment Mode</p></th>
                        <th width="80"><p>Lead Time</p></th>
                        <th width="80"><p>Days in Hand</p></th>
                        <th width="80"><p>Commercial File No</p></th>
                        <th width="80"><p>Lien Bank</p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p>Ex. LC/SC Amendment No(Last)</p></th>
                        <th width="80"><p>Pay Term</p></th>
                        <th width="80"><p>Tenor</p></th>
                        <th width="80"><p>First Shipment Date</p></th>
                        <th width="80"><p>Last Shipment Date</p></th>
                        <th width="80"><p>Shipment Qnty</p></th>
                        <th width="80"><p>Shipment Value</p></th>
                        <th width="80"><p>Excess Shipment Qnty</p></th>
                        <th width="80"><p>Excess Shipment Value</p></th>
                        <th width="80"><p>Short Shipment Qnty</p></th>
                        <th width="80"><p>Short Shipment Value</p></th>
                        <th width="80"><p>Shipping Status</p></th>
                        <th width="80"><p>SMV</p></th>
                        <th width="80"><p>Total SMV</p></th>
                        <th width="80"><p>CM</p></th>
                        <th width="80"><p>Team Leader</p></th>
                        <th width="80"><p>Dealing Merchandiser</p></th>
                        <th width="80"><p>Factory Merchandiser</p></th>
                        <th width="80"><p>Insert User</p></th>
                        <th width="80"><p>Lab Dip No</p></th>
                        <th width="80"><p>Buyer Sample Status</p></th>
                        <th width="80"><p>Order Status</p></th>
                        <th width="80"><p>Referance Closed </p></th>
                        <th width="80"><p>Emb. Work Order No</p></th>
                        <th width="80"><p>Service Work Order No</p></th>

                        <th width="80"><p>Budget Date</p></th>
                        <th width="80"><p>Budget Value</p></th>
                        <th width="80"><p>Open Value</p></th>
                        <th width="80"><p>Approved</p></th>
                        <th width="80"><p>Amendment No</p></th>
                        
                        <th width="80"><p>M Fabric Booking Date</p></th>
                        <th width="80"><p>M Fabric Booking No</p></th>
                        <th width="80"><p>Fabric Delivery Date</p></th>
                        <th width="80"><p>Amendment No</p></th>
                        <th width="80"><p>Amendment Date</p></th>
                        <th width="80"><p>Approved Status</p></th>
                        <th width="80"><p>Yarn Description</p></th>
                        <th width="80"><p>Fabric Construction</p></th>
                        <th width="80"><p>Fabric Composition</p></th>
                        <th width="80"><p>GSM</p></th>
                        <th width="80"><p>Dia/Width</p></th>
                        <th width="80"><p>Finish Quantity</p></th>
                        <th width="80"><p>Process Loss%</p></th>
                        <th width="80"><p>Grey Qnty Kg</p></th>

                        <th width="80"><p>Fabric Booking Date</p></th>
                        <th width="80"><p>Fabric Booking No</p></th>
                        <th width="80"><p>Fabric Delivery Date</p></th>
                        <th width="80"><p>Fabrication</p></th>
                        <th width="80"><p>Uom</p></th>
                        <th width="80"><p>Qnty</p></th>
                        <th width="80"><p>Approved Status</p></th>

                        <th width="80"><p>Fabric Booking Date</p></th>
                        <th width="80"><p>Fabric Booking No</p></th>
                        <th width="80"><p>Fabric Delivery Date</p></th>
                        <th width="80"><p>Fabrication</p></th>
                        <th width="80"><p>Uom</p></th>
                        <th width="80"><p>Finish Quantity</p></th>
                        <th width="80"><p>Process Loss%</p></th>
                        <th width="80"><p>Grey Qnty</p></th>
                        <th width="80"><p>Approved Status</p></th>

                        <th width="80"><p>Grey Yarn Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Yarn Allocation Qnty</p></th>
                        <th width="80"><p>Yarn Excess Allocation Qnty</p></th>
                        <th width="80"><p>Yarn Received Qnty</p></th>
                        <th width="80"><p>Yarn Receive Balance Qnty</p></th>
                        <th width="80"><p>Yarn Issued Qnty</p></th>
                        <th width="80"><p>Yarn Issue Balance Qnty</p></th>
                        <th width="80"><p>Yarn Excess Issued Qty </p></th>

                        <th width="80"><p>Dyed Yarn Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Dyed Yarn Allocation Qnty</p></th>
                        <th width="80"><p>Dyed Yarn Excess Allocation Qnty</p></th>
                        <th width="80"><p>Dyed Yarn Received Qnty</p></th>
                        <th width="80"><p>Dyed Yarn Receive Balance Qnty</p></th>
                        <th width="80"><p>Dyed Yarn Issued Qnty</p></th>
                        <th width="80"><p>Dyed Yarn Issue Balance Qnty</p></th>
                        <th width="80"><p>Dyed Yarn Excess Issued Qty </p></th>

                        <th width="80"><p>Dyed Yarn Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Service Work Order Qty</p></th>
                        <th width="80"><p>Yarn Dyed Produciton Qty</p></th>
                        <th width="80"><p>Yarn Dyed Produciton WIP Qty</p></th>
                        <th width="80"><p>Yarn Dyed Delivery Qty</p></th>
                        <th width="80"><p>Yarn Dyed Challan No</p></th>
                        <th width="80"><p>Yarn Dyed Challan Date</p></th>

                        <th width="80"><p>Knitting Require with EFR Qnty</p></th>
                        <th width="80"><p>Knitting Plan Qnty</p></th>
                        <th width="80"><p>Knitting Plan WIP Qnty</p></th>
                        <th width="80"><p>Knitting Work Order No</p></th>
                        <th width="80"><p>Knitting Production Qnty [Inbound]</p></th>
                        <th width="80"><p>Knitting Production Qnty [Outbound]</p></th>
                        <th width="80"><p>TTL Knitting Production Qnty </p></th>
                        <th width="80"><p>TTL Knitting Excess Production Qnty</p></th>
                        <th width="80"><p>TTL Knitting Production WIP Qnty</p></th>
                        <th width="80"><p>Price/KG [TK]</p></th>
                        <th width="80"><p>Total Price [TK]</p></th>
                        <th width="80"><p>Knitting Grey Fabric Delivery Qnty To Store</p></th>
                        <th width="80"><p>Closing Status</p></th>

                        <th width="80"><p>Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Normal Received Qnty</p></th>
                        <th width="80"><p>Normal Receive Excess Qnty</p></th>
                        <th width="80"><p>Receive Balance Qnty</p></th>
                        <th width="80"><p>Transfer Received Qnty</p></th>
                        <th width="80"><p>TTL Fabric Received Qnty</p></th>
                        <th width="80"><p>Normal Issued Qnty</p></th>
                        <th width="80"><p>Trasfered Issued Qnty</p></th>
                        <th width="80"><p>TTL Fabric Issued Qnty</p></th>
                        <th width="80"><p>Stock In Hand</p></th>

                        <th width="80"><p>Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Fabric Received by Batch </p></th>
                        <th width="80"><p>Batch Qnty</p></th>
                        <th width="80"><p>Production Qnty</p></th>
                        <th width="80"><p>Production Excess Qnty</p></th>
                        <th width="80"><p>Production WIP Qnty</p></th>

                        <th width="80"><p>Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Production Qnty</p></th>
                        <th width="80"><p>Production Excess Qnty</p></th>
                        <th width="80"><p>Production WIP Qnty</p></th>
                        <th width="80"><p>Process Name</p></th>
                        <th width="80"><p>QC Received Qnty</p></th>
                        <th width="80"><p>AOP Issue Qnty</p></th>
                        <th width="80"><p>AOP Receive Qnty</p></th>
                        <th width="80"><p>AOP Receive WIP Qnty</p></th>
                        <th width="80"><p>QC Delivery Qnty To Store</p></th>
                        <th width="80"><p>QC Stock in Hand</p></th>
                        <th width="80"><p>Closing Status</p></th>

                        <th width="80"><p>Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Normal Received Qnty</p></th>
                        <th width="80"><p>Normal Receive Excess Qnty</p></th>
                        <th width="80"><p>Receive Balance Qnty</p></th>
                        <th width="80"><p>Transfer Received Qnty</p></th>
                        <th width="80"><p>TTL Received Qnty</p></th>
                        <th width="80"><p>Normal Issued Qnty</p></th>
                        <th width="80"><p>Trasfered Issued Qnty</p></th>
                        <th width="80"><p>TTL Issued Qnty</p></th>
                        <th width="80"><p>Stock In Hand</p></th>

                        <th width="80"><p>Require with Short/EFR Qnty</p></th>
                        <th width="80"><p>Fabric Receive Qnty By Cutting</p></th>
                        <th width="80"><p>Excess Receive Qnty By Cutting </p></th>
                        <th width="80"><p>Receive Balance Qnty By Cutting</p></th>
                        <th width="80"><p>Cutting Status</p></th>
                        <th width="80"><p>Oreder Qnty</p></th>
                        <th width="80"><p>Excess Cutting %</p></th>
                        <th width="80"><p>Order Qnty with C%</p></th>
                        <th width="80"><p>Cutting Bundle Qnty</p></th>
                        <th width="80"><p>Cutting Lay Qnty</p></th>
                        <th width="80"><p>Cutting Production [QC] Qnty</p></th>
                        <th width="80"><p>Excess Cutting Qnty</p></th>
                        <th width="80"><p>Cutting Production Balance Qnty</p></th>
                        <th width="80"><p>Input Qnty</p></th>
                        <th width="80"><p>Input WIP Qnty</p></th>
                        <th width="80"><p>Print Send Qnty</p></th>
                        <th width="80"><p>Print Receive Qnty</p></th>
                        <th width="80"><p>Print WIP Qnty</p></th>
                        <th width="80"><p>Print Supplier</p></th>
                        <th width="80"><p>EMB Send Qnty</p></th>
                        <th width="80"><p>EMB Receive Qnty</p></th>
                        <th width="80"><p>EMB WIP Balance Qnty</p></th>
                        <th width="80"><p>EMB Supplier</p></th>

                        <th width="80"><p>Require Qnty</p></th>
                        <th width="80"><p>Order Qnty</p></th>
                        <th width="80"><p>Order Balance Qnty</p></th>
                        <th width="80"><p>Production [QC] Qnty</p></th>
                        <th width="80"><p>Production Excess Qnty</p></th>
                        <th width="80"><p>Production WIP Qnty</p></th>
                        <th width="80"><p>Delivery Qnty To Cutting</p></th>

                        <th width="80"><p>Require Qnty</p></th>
                        <th width="80"><p>Work Order Qnty</p></th>
                        <th width="80"><p>Work Order Balance Qnty</p></th>
                        <th width="80"><p>Production [QC] Qnty</p></th>
                        <th width="80"><p>Production Excess Qnty</p></th>
                        <th width="80"><p>Production WIP Qnty</p></th>
                        <th width="80"><p>Delivery Qnty To Cutting</p></th>

                        <th width="80"><p>Require Qnty</p></th>
                        <th width="80"><p>Input Qnty</p></th>
                        <th width="80"><p>Input Excess Qnty</p></th>
                        <th width="80"><p>Input Balance Qnty</p></th>
                        <th width="80"><p>Production [Output] Qnty</p></th>
                        <th width="80"><p>Production Excess Qnty</p></th>
                        <th width="80"><p>Production WIP Qnty</p></th>

                        <th width="80"><p>Require Qnty</p></th>
                        <th width="80"><p>Iron Qnty</p></th>
                        <th width="80"><p>HANGTAG Qnty</p></th>
                        <th width="80"><p>POLY Qnty</p></th>
                        <th width="80"><p>Finish Quantity</p></th>
                        <th width="80"><p>Balance Qty</p></th>
                        <th width="80"><p>Inspection Offer Date</p></th>
                        <th width="80"><p>Inspection Date</p></th>
                        <th width="80"><p>Inspection Qnty</p></th>

                        <th width="80"><p>Carton Qty</p></th>
                        <th width="80"><p>Shipment Qnty</p></th>
                        <th width="80"><p>Invoice Qty</p></th>
                        <th width="80"><p>Shipment Value</p></th>
                        <th width="80"><p>Invoice Value</p></th>
                        <th width="80"><p>Net Invoice Value</p></th>
                        <th width="80"><p>Excess Shipment Qnty</p></th>
                        <th width="80"><p>Excess Shipment Value </p></th>
                        <th width="80"><p>Short Shipment Qnty</p></th>
                        <th width="80"><p>Short Shipment Value</p></th>
                        <th width="80"><p>Shipment Return Qnty</p></th>
                        <th width="80"><p>Shipment Return Value</p></th>
                        <th width="80"><p>First Shipment Date</p></th>
                        <th width="80"><p>Last Shipment Date</p></th>
                        <th width="80"><p>Discount / Penalty / Claim Amount</p></th>
                        <th width="80"><p>Reason of Discount / Penalty / Claim </p></th>

                        <th width="80"><p>Budget Date</p></th>
                        <th width="80"><p>Budget Yarn Cost</p></th>
                        <th width="80"><p>Actual Yarn Cost</p></th>
                        <th width="80"><p>Budget Finish YD Cost</p></th>
                        <th width="80"><p>Actual Finish YD Cost</p></th>
                        <th width="80"><p>Budget Knitting Cost</p></th>
                        <th width="80"><p>Actual Knitting Cost</p></th>
                        <th width="80"><p>Budget Dyeing & Finishing Cost</p></th>
                        <th width="80"><p>Actual Dyeing & Finishing Cost</p></th>
                        <th width="80"><p>Budget Fabric Purchase Cost</p></th>
                        <th width="80"><p>Actual Fabric Purchase Cost</p></th>
                        <th width="80"><p>Budget AOP Cost</p></th>
                        <th width="80"><p>Actual AOP Cost</p></th>
                        <th width="80"><p>Budget Print Cost</p></th>
                        <th width="80"><p>Actual Print Cost</p></th>
                        <th width="80"><p>Budget Emb Cost</p></th>
                        <th width="80"><p>Actual Emb Cost</p></th>
                        <th width="80"><p>Budget Wash Cost</p></th>
                        <th width="80"><p>Actual Wash Cost</p></th>
                        <th width="80"><p>Budget Trims Cost</p></th>
                        <th width="80"><p>Actual Trims Cost</p></th>
                        <th width="80"><p>Budget MISC Cost</p></th>
                        <th width="80"><p>Actual MISC Cost</p></th>
                        <th width="80"><p>Budget CM</p></th>
                        <th width="80"><p>Actual CM</p></th>

                        <th width="80"><p>Lead Time [Templete]</p></th>
                        <th width="80"><p>Yarn Receive Start</p></th>
                        <th width="80"><p>Yarn Receive End</p></th>
                        <th width="80"><p>Knitting Start</p></th>
                        <th width="80"><p>Knitting End</p></th>
                        <th width="80"><p>Dyeing Start</p></th>
                        <th width="80"><p>Dyeing End</p></th>
                        <th width="80"><p>Finish Fabric Prod. Start</p></th>
                        <th width="80"><p>Finish Fabric Prod. End</p></th>
                        <th width="80"><p>Trims Receive Start</p></th>
                        <th width="80"><p>Trims Receive End</p></th>
                        <th width="80"><p>Cutting Start</p></th>
                        <th width="80"><p>Cutting End</p></th>
                        <th width="80"><p>Print Start</p></th>
                        <th width="80"><p>Print End</p></th>
                        <th width="80"><p>Emb Start</p></th>
                        <th width="80"><p>Emb End</p></th>
                        <th width="80"><p>Sewing Start</p></th>
                        <th width="80"><p>Sewing End</p></th>
                        <th width="80"><p>GMT Finihsing Start</p></th>
                        <th width="80"><p>GMT Finihsing End</p></th>
                        <th width="80"><p>Inspection Start</p></th>
                        <th width="80"><p>Inspection End</p></th>
                        <th width="80"><p>Shipment  Start</p></th>
                        <th width="80"><p>Shipment  End</p></th>
                    </tr>
                </thead>
            </table>
            <!-- =================================== report body ==================================== -->
            <div style=" max-height:300px; width:<?=$tbl_width+20;?>px; overflow-y:scroll;" id="scroll_body">
                <table cellspacing="0" border="1" class="rpt_table"   rules="all" width="<?=$tbl_width;?>"  align="center" id="table_body">
                    <tbody>
                        <?
                        $i=1;
                        $gr_job_total = 0;
                        $gr_po_total = 0;
                        $gr_color_size_total = 0;
                        $gr_plan_cut_total = 0;
                        $gr_order_value_total = 0;

                        foreach ($data_array as $job_key => $row) 
                        {   
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";   
                            $ship_excess_qty = max($ex_data_array[$job_key]['ex_qty'] - $row['order_quantity'],0);
                            $ship_excess_val = max($ex_data_array[$job_key]['ex_value'] - $row['order_value'],0); 
                            $ship_short_qty = max($row['order_quantity'] - $ex_data_array[$job_key]['ex_qty'],0);
                            $ship_short_val = max($row['order_value'] - $ex_data_array[$job_key]['ex_value'],0);

                            // =============================== for budget data ===========================
                            $finishing_arr = array('209','165','33','94','63','171','65','170','156','179','200','208','127','125','84','68','128','190','242','240','192','172','90','218','67','197','73','66','185','142');
                            $total_finishing_amount=0;
                            $total_finishing_qty=0;
                            $other_cost_attr = array('inspection','freight','certificate_pre_cost','deffdlc_cost','design_cost','studio_cost','common_oh','interest_cost','incometax_cost','depr_amor_pre_cost');
                            $total_other_cost = 0;
                            foreach ($other_cost_attr as $attr) {
                                $total_other_cost+=$other_costing_arr[$job_key][$attr];
                            }
                            $misc_cost=$other_costing_arr[$job_key]['lab_test']+$commercial_costing_arr[$job_key]+$commission_costing_arr[$job_key]+$total_other_cost;

                            foreach ($finishing_arr as $fid) {
                                $total_finishing_amount += array_sum($conv_amount_job_process[$job_key][$fid]);
                                $total_finishing_qty += array_sum($conv_qty_job_process[$job_key][$fid]);
                            }

                            $total_fabic_cost=0;
                            if(count($conv_amount_job_process[$job_key][31])>0){
                                $total_fabic_cost+=array_sum($conv_amount_job_process[$job_key][31])/array_sum($conv_qty_job_process[$job_key][31]);
                            }
                            $total_fabric_amount +=array_sum($conv_amount_job_process[$job_key][31]);
                            $total_fabric_per +=array_sum($conv_amount_job_process[$job_key][31])/$order_values*100;
                            if(count($conv_amount_job_process[$job_key][30])>0){
                                $total_fabic_cost+=array_sum($conv_amount_job_process[$job_key][30])/array_sum($conv_qty_job_process[$job_key][30]);
                            }
                            if($yarn_qty_amount_arr[$job_key]['amount']!=''){
                                $total_fabic_cost+=$yarn_qty_amount_arr[$job_key]['amount']/$yarn_qty_amount_arr[$job_key]['qty'];
                            }
                            $total_fabric_amount +=$yarn_qty_amount_arr[$job_key]['amount']; 
                            $total_fabric_per +=$yarn_qty_amount_arr[$job_key]['amount']/$order_values*100;
                            if($total_finishing_amount!=0){
                                $total_fabic_cost+=$total_finishing_amount/$total_finishing_qty;
                            } 
                            $total_fabric_amount +=$total_finishing_amount;
                            $total_fabric_per +=$total_finishing_amount/$order_values*100;
                            $total_fabric_amount +=array_sum($conv_amount_job_process[$job_key][30]);
                            $total_fabric_per +=array_sum($conv_amount_job_process[$job_key][30])/$order_values*100;
                            if(count($conv_amount_job_process[$job_key][35])>0){
                                $total_fabic_cost+=array_sum($conv_amount_job_process[$job_key][35])/array_sum($conv_qty_job_process[$job_key][35]);
                            }
                            $total_fabric_amount +=array_sum($conv_amount_job_process[$job_key][35]); 
                            $total_fabric_per +=array_sum($conv_amount_job_process[$job_key][35])/$order_values*100;
                            if(count($conv_amount_job_process[$job_key][1])>0){
                                $total_fabic_cost+=array_sum($conv_amount_job_process[$job_key][1])/array_sum($conv_qty_job_process[$job_key][1]);
                            }
                            $total_fabric_amount +=array_sum($conv_amount_job_process[$job_key][1]);
                            $total_fabric_per +=array_sum($conv_amount_job_process[$job_key][1])/$order_values*100; 

                            $purchase_amount = array_sum($fabricAmoutByFabricSource['knit']['grey'][$job_key])+array_sum($fabricAmoutByFabricSource['woven']['grey'][$job_key]);
                            $purchase_qty = array_sum($fabricQtyByFabricSource['knit']['grey'][$job_key])+array_sum($fabricQtyByFabricSource['woven']['grey'][$job_key]);

                            $ather_emb_attr = array(4,5,6,99);
                            foreach ($ather_emb_attr as $att) {
                                $others_emb_amount += $emb_amount_job_name_arr[$job_key][$att];
                                $others_emb_qty += $emb_qty_job_name_arr[$job_key][$att];
                            }
                            $knitting_amount_summ=''; $dyeing_amount_summ=''; $yds_amount_summ=''; $aop_amount_summ='';
                            if(count($conv_amount_job_process[$job_key][1])>0) {
                                $knitting_amount_summ = fn_number_format(array_sum($conv_amount_job_process[$job_key][1]),2);
                            }
                            $yarn_amount_summ = $yarn_qty_amount_arr[$job_key]['amount'];
                            $print_amount_summ = $emb_amount_job_name_arr[$job_key][1];      
                            $emb_amount_summ= $emb_amount_job_name_arr[$job_key][2];
                            $wash_amount_summ = $wash_amount_job_name_arr[$job_key][3];
                            if(count($conv_amount_job_process[$job_key][31])>0) {
                                $dyeing_amount_summ=  array_sum($conv_amount_job_process[$job_key][31]);
                            }
                            if(count($conv_amount_job_process[$job_key][30])>0) {
                                $yds_amount_summ = array_sum($conv_amount_job_process[$job_key][30]);
                            }
                            if(count($conv_amount_job_process[$job_key][35])>0) {
                                $aop_amount_summ = array_sum($conv_amount_job_process[$job_key][35]);
                            }
                            
                            $total_budget_value = $yarn_amount_summ+$total_finishing_amount+$print_amount_summ+$trims_costing_arr[$job_key]+$yds_amount_summ+$aop_amount_summ+$emb_amount_summ+$knitting_amount_summ+$purchase_amount+$wash_amount_summ+$other_costing_arr[$job_key]['cm_cost']+$dyeing_amount_summ+$others_emb_amount+$misc_cost;
                            $open_value = $row['total_price']-$total_budget_value;
                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
                                <td width="30">.<p><?=$i;?></p></td>
                                <td width="60">
                                    <p>
                                        <?
                                        $order_status_arr = array_unique(array_filter(explode(",", $row['order_status'])));
                                        $order_status_name = "";
                                        foreach ($order_status_arr as $val) 
                                        {
                                            $order_status_name .= ($order_status_name=="") ? $order_status[$val] : ", ".$order_status[$val];
                                        }
                                        echo $order_status_name;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="100"><p><?=$company_library[$row['company_name']];?></p></td>
                                <td width="100"><p><?=$company_library[$row['wo_com_id']];?></p></td>
                                <td width="80"><img class="zoom" src='../../../<?= $imge_arr[$job_key]; ?>' height='40' width='50' /></td>
                                <td width="100"><p><?=$buyer_library[$row['buyer_name']];?></p></td>
                                <td width="50" align="center"><p><?=$row['year'];?></p></td>
                                <td width="90"><p><?=$job_key;?></p></td>
                                <td width="80"><p><?=$row['repeat_job_no'];?></p></td>
                                <td width="80"><p><?=$row['req_no'];?></p></td>
                                <td width="80"><p><?=$row['style'];?></p></td>
                                <td width="80"><p><?=$row['style_des'];?></p></td>
                                <td width="80">
                                    <p>
                                        <?
                                        $item_id_arr = array_unique(array_filter(explode(",", $row['item_id'])));
                                        $item_name = "";
                                        foreach ($item_id_arr as $val) 
                                        {
                                            $item_name .= ($item_name=="") ? $garments_item[$val] : ", ".$garments_item[$val];
                                        }
                                        echo $item_name;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80"><p><?=$season_library[$row['season']];?></p></td>
                                <td width="80"><p><?=$product_dept[$row['product_dept']];?></p></td>
                                <td width="80"><p><?=$row['product_code'];?></p></td>
                                <td width="80"><p><?=$sub_dep_library[$row['pro_sub_dep']];?></p></td>
                                <td width="80"><p><?=$brand_library[$row['brand_id']];?></p></td>
                                <td width="80"><p><?=$region[$row['region']];?></p></td>
                                <td width="80"><p><?=$product_category[$row['product_category']];?></p></td>
                                <td width="80">
                                    <p>
                                        <?
                                        $country_id_arr = array_unique(array_filter(explode(",", $row['country_id'])));
                                        $country_name = "";
                                        foreach ($country_id_arr as $val) 
                                        {
                                            $country_name .= ($country_name=="") ? $country_library[$val] : ", ".$country_library[$val];
                                        }
                                        echo $country_name;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80"><p><?=$sustainability_standard[$row['sustainability_standard']];?></p></td>
                                <td width="80"><p><?=$fab_material[$row['fab_material']];?></p></td>
                                <td width="80"><p><?=$fbooking_order_nature[$row['quality_level']];?></p></td>
                                <td width="80"><p><?=$quality_label[$row['qlty_label']];?></p></td>
                                <td width="80"><p><?=$emb_name_arr[$job_key];?></p></td>
                                <td width="80"><p><?=$conv_name_arr[$job_key];?></p></td>
                                <td width="80">
                                    <p>
                                        <?
                                        $po_number_arr = array_unique(array_filter(explode(",", $row['po_number'])));
                                        $po_name = "";
                                        foreach ($po_number_arr as $val) 
                                        {
                                            $po_name .= ($po_name=="") ? $val : ", ".$val;
                                        }
                                        echo $po_name;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80"><p><?=$acc_po_array[$job_key]['acc_po'];?></p></td>
                                <td width="80">
                                    <p>
                                        <?
                                        $color_arr = array_unique(array_filter(explode(",", $row['color_id'])));
                                        $color_name = "";
                                        foreach ($color_arr as $val) 
                                        {
                                            $color_name .= ($color_name=="") ? $color_library[$val] : ", ".$color_library[$val];
                                        }
                                        echo $color_name;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80">
                                    <p>
                                        <?
                                        $size_arr = array_unique(array_filter(explode(",", $row['size_id'])));
                                        $size_name = "";
                                        foreach ($size_arr as $val) 
                                        {
                                            $size_name .= ($size_name=="") ? $size_library[$val] : ", ".$size_library[$val];
                                        }
                                        echo $size_name;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80">
                                    <p>
                                        <?
                                        $excess_cut_arr = array_unique(array_filter(explode("**", $po_wise_data[$job_key]['excess_cut'])));
                                        $ex_cut = "";
                                        foreach ($excess_cut_arr as $val) 
                                        {
                                            $ex_cut .= ($ex_cut=="") ? "(".$val.")" : ",(".$val.")";
                                        }
                                        echo $ex_cut;
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="right"><p><?=number_format($row['job_qty_pcs'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($row['order_quantity'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($row['plan_cut_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($row['po_quantity'],0);?></p></td>
                                <td width="80" align="center"><p><?=$unit_of_measurement[$row['order_uom']];?></p></td>
                                <td width="80" align="right"><p><?=$row['unit_price'];?></p></td>
                                <td width="80" align="right"><p><?=number_format($row['order_value'],0);?></p></td>
                                <td width="80" align="center"><p><?=$row['po_insert_date'];?></p></td>
                                <td width="80" align="center"><p><?=$row['po_received_date'];?></p></td>
                                <td width="80" align="center"><p><?=$row['factory_received_date'];?></p></td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $lay_date_arr = array_unique(array_filter(explode(",", $lay_data_array[$job_key]['entry_date'])));
                                        $lay_date = get_max_min_date($lay_date_arr);
                                        echo ($lay_date['min_date']!="") ? change_date_format($lay_date['min_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        echo ($lay_date['max_date']!="") ? change_date_format($lay_date['max_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $sew_out_date_arr = array_unique(array_filter(explode(",", $gmts_data_array[$job_key][5]['date'])));
                                        $sew_out_date = get_max_min_date($sew_out_date_arr);
                                        echo ($sew_out_date['min_date']!="") ? change_date_format($sew_out_date['min_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        echo ($sew_out_date['max_date']!="") ? change_date_format($sew_out_date['max_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $gmt_fin_date_arr = array_unique(array_filter(explode(",", $gmts_data_array[$job_key][8]['date'])));
                                        $gmt_fin_date = get_max_min_date($gmt_fin_date_arr);
                                        echo ($gmt_fin_date['min_date']!="") ? change_date_format($gmt_fin_date['min_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        echo ($gmt_fin_date['max_date']!="") ? change_date_format($gmt_fin_date['max_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$row['pub_shipment_date'];?></p></td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $ex_date_array = array_unique(array_filter(explode(",", $ex_data_array[$job_key]['ex_date'])));
                                        $ex_date = get_max_min_date($ex_date_array);
                                        // echo min($ex_date_array);
                                        echo ($ex_date['min_date']!="") ? change_date_format($ex_date['min_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80"><p><?=$row['shipment_date'];?></p></td>
                                <td width="80"><p><?=$row['txt_etd_ldd'];?></p></td>
                                <td width="80"><p><?=$cutup_date = implode(",", array_unique(array_filter(explode(",", $row['cutup_date']))));?></p></td>
                                <td width="80"><p><?=$coun_ship_date = implode(",", array_unique(array_filter(explode(",", $row['country_ship_date']))));?></p></td>
                                <td width="80"><p><?=$row['pub_shipment_date'];?></p></td>
                                <td width="80"><p><?=$shipment_mode[$row['ship_mode']];?></p></td>
                                <td width="80"><p><?=$row['lead_time'];?></p></td>
                                <td width="80"><p><?=$row['days_in_hand'];?></p></td>
                                <td width="80">
                                    <p>
                                        <?
                                        $com_file = implode(",",array_unique(array_filter(explode(",",$lc_sc_data_array[$job_key]['internal_file_no']))));
                                        echo $com_file;
                                        ?>                                            
                                    </p>
                                </td>                                
                                <td width="80">
                                    <p>
                                        <?
                                        $lien_bank = implode(",",array_unique(array_filter(explode(",",$lc_sc_data_array[$job_key]['lien_bank']))));
                                        echo $lien_bank;
                                        ?>                                            
                                    </p>
                                </td>
                                <td width="80"><p><?=$a;?></p></td>                                
                                <td width="80">
                                    <p>
                                        <?
                                        $amendment_no = implode(",",array_unique(array_filter(explode(",",$lc_sc_data_array[$job_key]['amendment_no']))));
                                        echo $amendment_no;
                                        ?>                                            
                                    </p>
                                </td>                               
                                <td width="80">
                                    <p>
                                        <?
                                        $pay_term = implode(",",array_unique(array_filter(explode(",",$lc_sc_data_array[$job_key]['pay_term']))));
                                        echo $pay_term;
                                        ?>                                            
                                    </p>
                                </td>                                
                                <td width="80">
                                    <p>
                                        <?
                                        $tenor = implode(",",array_unique(array_filter(explode(",",$lc_sc_data_array[$job_key]['tenor']))));
                                        echo $tenor;
                                        ?>                                            
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        echo ($ex_date['min_date']!="") ? change_date_format($ex_date['min_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        echo ($ex_date['max_date']!="") ? change_date_format($ex_date['max_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['ex_qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['ex_value'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ship_excess_qty,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ship_excess_val,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ship_short_qty,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ship_short_val,0);?></p></td>
                                <td width="80" align="center"><p><?=$row['shipment_status'];?></p></td>
                                <td width="80" align="right"><p><?=$row['set_smv'];?></p></td>
                                <td width="80" align="right"><p><?=$row['set_smv'];?></p></td>
                                <td width="80"><p><?=$budget_data_array[$job_key]['cm_cost'];?></p></td>
                                <td width="80"><p><?=$team_library[$row['team_leader']];?></p></td>
                                <td width="80"><p><?=$merchant_library[$row['dealing_marchant']];?></p></td>
                                <td width="80"><p><?=$merchant_library[$row['factory_marchant']];?></p></td>
                                <td width="80"><p><?=$user_library[$row['inserted_by']];?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80">
                                    <a href="javascript:void(0);" onclick="show_popup('sample_status', '<?=$job_key; ?>', '920px',1)">
                                        View
                                    </a>
                                </td>
                                <td width="80">
                                    <a href="javascript:void(0);" onclick="show_popup('order_status', '<?=$job_key; ?>', '320px',2)">
                                        View
                                    </a>
                                </td>
                                <td width="80">
                                    <a href="javascript:void(0);" onclick="show_popup('closing_status', '<?=$job_key; ?>', '320px',3)">
                                        View
                                    </a>
                                </td>
                                <td width="80">
                                    <p>
                                        <?
                                            echo ($work_order_data_array[$job_key][1]) ? "Printing : ".$work_order_data_array[$job_key][1] : "";
                                            echo ($work_order_data_array[$job_key][2]) ? "<br>Embroidery : ".$work_order_data_array[$job_key][2] : "";
                                            echo ($work_order_data_array[$job_key][3]) ? "<br>Wash : ".$work_order_data_array[$job_key][3] : "";
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80">
                                    <p>
                                        <?
                                            echo "AOP : ".$service_booking_data_array[$job_key]['aop'];
                                            echo "<br>Yarn Dyeing : ".$yd_work_order_data_array[$job_key];
                                            echo "<br>Knitting : ".$service_booking_data_array[$job_key]['knitting'];
                                            echo "<br>Fab. Dyeing : ".$service_booking_data_array[$job_key]['dyeing'];
                                        ?>
                                        
                                    </p>
                                </td>



                                <td width="80"><p><?=$budget_data_array[$job_key]['costing_date'];?></p></td>
                                <td width="80" align="right"><p><?=number_format($total_budget_value,2);?></p></td>
                                <td width="80" align="right"><p><?=number_format($open_value,0);?></p></td>
                                <td width="80"><p><?=($budget_data_array[$job_key]['approved']==1) ? "Approved" : "Not Approved";?></p></td>
                                <td width="80"><p><?=number_format($a,0);?></p></td>


                                
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $booking_date_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['booking_date'])));
                                        $booking_date = "";
                                        foreach ($booking_date_arr as $val) 
                                        {
                                            $booking_date .= ($booking_date=="") ? $val : ", ".$val;
                                        }
                                        echo $booking_date;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $booking_no_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['booking_no'])));
                                        $booking_no = "";
                                        foreach ($booking_no_arr as $val) 
                                        {
                                            $booking_no .= ($booking_no=="") ? $val : ", ".$val;
                                        }
                                        echo $booking_no;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $delivery_date_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['delivery_date'])));
                                        $delivery_date = "";
                                        foreach ($delivery_date_arr as $val) 
                                        {
                                            $delivery_date .= ($delivery_date=="") ? $val : ", ".$val;
                                        }
                                        echo $delivery_date;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $revised_no_arr = array_unique(explode(",", $booking_data_array[$job_key][1][2]['revised_no']));
                                        $revised_no = "";
                                        foreach ($revised_no_arr as $val) 
                                        {
                                            $revised_no .= ($revised_no=="") ? $val : ", ".$val;
                                        }
                                        echo $revised_no;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $revised_date_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['revised_date'])));
                                        $revised_date = "";
                                        foreach ($revised_date_arr as $val) 
                                        {
                                            $revised_date .= ($revised_date=="") ? $val : ", ".$val;
                                        }
                                        echo $revised_date;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $is_approved_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['is_approved'])));
                                        $is_approved = "";
                                        foreach ($is_approved_arr as $val) 
                                        {
                                            $is_approved .= ($is_approved=="") ? $approval_type_arr[$val] : ", ".$approval_type_arr[$val];
                                        }
                                        echo ($is_approved=="") ? " Not Approved" : $is_approved;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $construction_arr = array_unique(array_filter(explode("__", $booking_data_array[$job_key][1][2]['construction'])));
                                        $construction = "";
                                        foreach ($construction_arr as $val) 
                                        {
                                            $construction .= ($construction=="") ? $val : ", ".$val;
                                        }
                                        echo $construction;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $copmposition_arr = array_unique(array_filter(explode("__", $booking_data_array[$job_key][1][2]['copmposition'])));
                                        $copmposition = "";
                                        foreach ($copmposition_arr as $val) 
                                        {
                                            $copmposition .= ($copmposition=="") ? $val : ", ".$val;
                                        }
                                        echo $copmposition;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $gsm_weight_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['gsm_weight'])));
                                        $gsm_weight = "";
                                        foreach ($gsm_weight_arr as $val) 
                                        {
                                            $gsm_weight .= ($gsm_weight=="") ? $val : ", ".$val;
                                        }
                                        echo $gsm_weight;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $dia_width_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['dia_width'])));
                                        $dia_width = "";
                                        foreach ($dia_width_arr as $val) 
                                        {
                                            $dia_width .= ($dia_width=="") ? $val : ", ".$val;
                                        }
                                        echo $dia_width;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][2]['fin_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][2]['process_loss'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][2]['grey_fab_qnty'],0);?></p></td>



                                
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $booking_date_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1]['booking_date'])));
                                        $booking_date = "";
                                        foreach ($booking_date_arr as $val) 
                                        {
                                            $booking_date .= ($booking_date=="") ? $val : ", ".$val;
                                        }
                                        echo $booking_date;
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $booking_no_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1]['booking_no'])));
                                        $booking_no = "";
                                        foreach ($booking_no_arr as $val) 
                                        {
                                            $booking_no .= ($booking_no=="") ? $val : ", ".$val;
                                        }
                                        echo $booking_no;
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $delivery_date_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1]['delivery_date'])));
                                        $delivery_date = "";
                                        foreach ($delivery_date_arr as $val) 
                                        {
                                            $delivery_date .= ($delivery_date=="") ? $val : ", ".$val;
                                        }
                                        echo $delivery_date;
                                        ?>                                        
                                    </p>
                                </td>                                
                                <td width="80">
                                    <p>
                                        <?
                                        $construction_arr = array_unique(array_filter(explode("__", $pur_booking_data_array[$job_key][1]['construction'])));
                                        $construction = "";
                                        foreach ($construction_arr as $val) 
                                        {
                                            $construction .= ($construction=="") ? $val : ", ".$val;
                                        }
                                        echo $construction;
                                        ?>                                        
                                    </p>
                                </td>                                                               
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $uom_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1]['uom'])));
                                        $uom = "";
                                        foreach ($uom_arr as $val) 
                                        {
                                            $uom .= ($uom=="") ? $unit_of_measurement[$val] : ", ".$unit_of_measurement[$val];
                                        }
                                        echo $uom;
                                        ?>                                        
                                    </p>
                                </td>                                                                                               
                                <td width="80" align="right">
                                    <p>
                                        <?
                                        $fin_fab_qnty_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1]['fin_fab_qnty'])));
                                        $fin_fab_qnty = "";
                                        foreach ($fin_fab_qnty_arr as $val) 
                                        {
                                            $fin_fab_qnty .= ($fin_fab_qnty=="") ? $val : ", ".$val;
                                        }
                                        echo $fin_fab_qnty;
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $is_approved_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1][2]['is_approved'])));
                                        $is_approved = "";
                                        foreach ($is_approved_arr as $val) 
                                        {
                                            $is_approved .= ($is_approved=="") ? $approval_type_arr[$val] : ", ".$approval_type_arr[$val];
                                        }
                                        echo ($is_approved=="") ? " Not Approved" : $is_approved;
                                        ?>
                                        
                                    </p>
                                </td>

                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $booking_date_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][1]['booking_date'])));
                                        $booking_date = "";
                                        foreach ($booking_date_arr as $val) 
                                        {
                                            $booking_date .= ($booking_date=="") ? $val : ", ".$val;
                                        }
                                        echo $booking_date;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $booking_no_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][1]['booking_no'])));
                                        $booking_no = "";
                                        foreach ($booking_no_arr as $val) 
                                        {
                                            $booking_no .= ($booking_no=="") ? $val : ", ".$val;
                                        }
                                        echo $booking_no;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $delivery_date_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][1]['delivery_date'])));
                                        $delivery_date = "";
                                        foreach ($delivery_date_arr as $val) 
                                        {
                                            $delivery_date .= ($delivery_date=="") ? $val : ", ".$val;
                                        }
                                        echo $delivery_date;
                                        ?>
                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $construction_arr = array_unique(array_filter(explode("__", $booking_data_array[$job_key][1][1]['construction'])));
                                        $construction = "";
                                        foreach ($construction_arr as $val) 
                                        {
                                            $construction .= ($construction=="") ? $val : ", ".$val;
                                        }
                                        echo $construction;
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $uom_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][1]['uom'])));
                                        $uom = "";
                                        foreach ($uom_arr as $val) 
                                        {
                                            $uom .= ($uom=="") ? $unit_of_measurement[$val] : ", ".$unit_of_measurement[$val];
                                        }
                                        echo $uom;
                                        ?>                                        
                                    </p>
                                </td>                                
                                <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][1]['fin_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][1]['process_loss'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][1]['grey_fab_qnty'],0);?></p></td>                                
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $is_approved_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][1]['is_approved'])));
                                        $is_approved = "";
                                        foreach ($is_approved_arr as $val) 
                                        {
                                            $is_approved .= ($is_approved=="") ? $approval_type_arr[$val] : ", ".$approval_type_arr[$val];
                                        }
                                        echo ($is_approved=="") ? " Not Approved" : $is_approved;
                                        ?>
                                        
                                    </p>
                                </td>


                                
                                <td width="80"><p><a href="javascript:void(0);">View</a></p></td>



                                <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['grey_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($yarn_allocation_array[$job_key][2],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty']-$yarn_allocation_array[$job_key][2]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($textile_data_array[$job_key][3],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty']-$textile_data_array[$job_key][3]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty']-$textile_data_array[$job_key][3]),0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($yarn_allocation_array[$job_key][1],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty']-$yarn_allocation_array[$job_key][1]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($yarn_rcv_from_dyeing_array[$job_key],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($dyed_yarn_issue_array[$job_key],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['grey_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($program_data_array[$job_key],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty']-$program_data_array[$job_key]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($knitting_data_array[$job_key][1],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($knitting_data_array[$job_key][3],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($knitting_data_array[$job_key][1]+$knitting_data_array[$job_key][3]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($knitting_data_array[$job_key][1]-$knitting_data_array[$job_key][3]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($knitting_data_array[$job_key][1]-$knitting_data_array[$job_key][3]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][56],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['grey_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][58],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty'] - $roll_data_array[$job_key][58]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty'] - $roll_data_array[$job_key][58]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][82][5],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($roll_data_array[$job_key][58]+$transfer_data_array[$job_key][82][5]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][61],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][82][6],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($roll_data_array[$job_key][61]+$transfer_data_array[$job_key][82][6]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format((($roll_data_array[$job_key][58]+$transfer_data_array[$job_key][82][5]) -($roll_data_array[$job_key][61]+$transfer_data_array[$job_key][82][6])) ,0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['fin_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($textile_data_array[$job_key][62],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][64],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($fab_dyeing_array[$job_key],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($textile_data_array[$job_key][62] - $fab_dyeing_array[$job_key]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($textile_data_array[$job_key][62] - $fab_dyeing_array[$job_key]),0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['fin_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($textile_data_array[$job_key][66],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['fin_fab_qnty'] - $textile_data_array[$job_key][66]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($fab_dyeing_array[$job_key] - $textile_data_array[$job_key][66]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][63],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][65],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($roll_data_array[$job_key][63] - $roll_data_array[$job_key][65]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][67],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['fin_fab_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][68],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['fin_fab_qnty']-$roll_data_array[$job_key][68]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['fin_fab_qnty']-$roll_data_array[$job_key][68]),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][134][5],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][134][5],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($textile_data_array[$job_key][71],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][134][6],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][134][6],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($roll_data_array[$job_key][68] - $textile_data_array[$job_key][71]),0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($fin_fab_data_array[$job_key]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($row['job_qty_pcs'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($row['plan_cut_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($lay_data_array[$job_key]['no_of_bndl'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($lay_data_array[$job_key]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][1]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['job_qty_pcs']-$gmts_data_array[$job_key][1]['qty']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['job_qty_pcs']-$gmts_data_array[$job_key][1]['qty']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][4]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($embel_data_array[$job_key][2][1]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($embel_data_array[$job_key][3][1]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="left"><p><?=number_format($company_library[$embel_data_array[$job_key][2][1]['wo_com_id']],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($embel_data_array[$job_key][2][2]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($embel_data_array[$job_key][3][2]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="left"><p><?=$company_library[$embel_data_array[$job_key][2][2]['wo_com_id']];?></p></td>



                                
                                <td width="80" align="right"><p><?=number_format($emb_qty_job_name_arr[$job_key][1],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($print_emb_order_data_array[$job_key][204]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($emb_qty_job_name_arr[$job_key][2],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($print_emb_order_data_array[$job_key][311]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($row['job_qty_pcs'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][4]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['plan_cut_qnty']-$gmts_data_array[$job_key][4]['qty']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][4]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][5]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['plan_cut_qnty']-$gmts_data_array[$job_key][5]['qty']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['plan_cut_qnty']-$gmts_data_array[$job_key][5]['qty']),0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($row['job_qty_pcs'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][7]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][11]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][8]['qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['plan_cut_qnty']-$gmts_data_array[$job_key][8]['qty']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=$buyer_insp_data[$job_key]['date'];?></p></td>
                                <td width="80" align="right"><p><?=number_format($buyer_insp_data[$job_key]['qty'],0);?></p></td>



                                <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['cartot_qnty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['ex_qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($invice_data[$job_key]['inv_qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['ex_value'],2);?></p></td>
                                <td width="80" align="right"><p><?=number_format($invice_data[$job_key]['inv_value'],2);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['job_qty_pcs']-$ex_data_array[$job_key]['ex_qty']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['total_price']-$ex_data_array[$job_key]['ex_value']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['job_qty_pcs']-$ex_data_array[$job_key]['ex_qty']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format(($row['total_price']-$ex_data_array[$job_key]['ex_value']),0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['rtn_qty'],0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['ex_rtn_value'],0);?></p></td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $ex_data_array = array_unique(array_filter(explode(",", $ex_data_array[$job_key]['ex_date'])));
                                        $ex_date = get_max_min_date($ex_data_array);
                                        echo ($ex_date['min_date']!="") ? change_date_format($ex_date['min_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="center">
                                    <p>
                                        <?
                                        $ex_data_array = array_unique(array_filter(explode(",", $ex_data_array[$job_key]['ex_date'])));
                                        $ex_date = get_max_min_date($ex_data_array);
                                        echo ($ex_date['max_date']!="") ? change_date_format($ex_date['max_date']) : "";
                                        ?>                                        
                                    </p>
                                </td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>



                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>



                                
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                                <td width="80"><p><?=$a;?></p></td>
                            </tr>
                            <?
                            $i++;
                            $gr_job_total += $row['job_qty_pcs'];
                            $gr_po_total += $row['order_quantity'];
                            $gr_color_size_total += $row['po_quantity'];
                            $gr_plan_cut_total += $row['plan_cut_qnty'];
                            $gr_order_value_total += $row['order_value'];
                        }
                        unset($data_array);
                        ?>                        
                    </tbody>
                </table>
            </div>
            <!-- =============================== report footer ================================ -->
            <table cellspacing="0" border="1" class="rpt_table"   rules="all" width="<?=$tbl_width;?>"  align="center">
                <tfoot>                    
                    <tr>
                        <th width="30"><p></p></th>
                        <th width="60"><p></p></th>
                        <th width="100"><p></p></th>
                        <th width="100"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="100"><p></p></th>
                        <th width="50"><p></p></th>
                        <th width="90"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p></p></th>
                        <th width="80"><p>Grand Total</p></th>
                        <th width="80"><p><?=number_format($gr_job_total,0);?></p></th>
                        <th width="80"><p><?=number_format($gr_po_total,0);?></p></th>
                        <th width="80"><p><?=number_format($gr_plan_cut_total,0);?></p></th>
                        <th width="80"><p><?=number_format($gr_color_size_total,0);?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>

                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                        <th width="80"><p><?=$a;?></p></th>
                    </tr>
                </tfoot>
            </table>
        </div>        
    </fieldset>
    <script type="text/javascript">
        // $("#zoom").elevateZoom({easing : true});
    </script>
    <?   

}

if ($action == "sample_status") 
{
    $color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
    $sampleArr = return_library_array("select id, sample_name from lib_sample", 'id', 'sample_name');
    echo load_html_head_contents("Sample Approve Details", "../../../../", 1, 1, $unicode, '', '');

    /*$expData = explode('_', $job_number);
    $job_number = $expData[0];
    $po_id = $expData[1];*/
    $po_arr = return_library_array("SELECT id, po_number from wo_po_break_down where job_no_mst='$job_number'", 'id', 'po_number');
    ?>
    <div style="width:100%" align="center">
        <fieldset style="width:900px">
            <table width="600">
                <?
                $job_sql = sql_select("SELECT job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
                foreach ($job_sql as $row_job)
                {
                    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Sample Approval Details</strong></td>    
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td> 
                        <td align="left"  width="200"><? echo $job_number; ?></td> 
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>  
                        <td align="left"><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td> 
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td> 
                        <td align="left"><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td> 
                        <td align="right"><strong>Style Ref No</strong> : </td> 
                        <td align="left"><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr> 
                    <tr>
                        <td colspan="4" height="15"></td>                   
                    </tr>   
                    <? 
                } 
                ?>         
            </table>
            <div style="width:100%;" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                        <tr>
                            <th width="35">SL</th>
                            <th width="100">PO Number</th>
                            <th width="90">Sample Type</th>
                            <th width="90">Color Name</th>
                            <th width="80">Target Date</th>
                            <th width="80">To Factory</th>
                            <th width="80">To Buyer</th>
                            <th width="70">Status</th>
                            <th width="80">Approval Date</th>
                            <th width="70">Delay Day</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:270px; overflow-y:scroll" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
                    $i = 0;
                    if ($db_type == 0)
                        $date_diff = "DATEDIFF(approval_status_date,target_approval_date)";
                    else
                        $date_diff = "trunc(approval_status_date-target_approval_date)";
                    $sql = "SELECT a.sample_type_id,b.color_number_id,b.po_break_down_id, target_approval_date, send_to_factory_date, submitted_to_buyer, approval_status, approval_status_date, sample_comments, $date_diff as delay_day from wo_po_sample_approval_info a, wo_po_color_size_breakdown b where a.color_number_id=b.id and a.job_no_mst='$job_number' and a.current_status=1 and a.is_deleted=0 and a.status_active=1 order by a.sample_type_id,b.color_number_id";
                    //echo $sql; //and a.approval_status<>0
                    $apprv_sql = sql_select($sql);
                    foreach ($apprv_sql as $row) 
                    {
                        $i++;
                        if ($i % 2 == 0)
                            $bgcolor = "#EFEFEF";
                        else
                            $bgcolor = "#FFFFFF";

                        if ($row[csf("delay_day")] > 0) {
                            $td_color = "red";
                            $delay_day = $row[csf("delay_day")];
                        } else {
                            $td_color = "";
                            $delay_day = "&nbsp;";
                        }
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="35"><? echo $i; ?></td>
                            <td width="100"><p><? echo $po_arr[$row[csf("po_break_down_id")]]; ?></p></td>
                            <td width="90"><p><? echo $sampleArr[$row[csf("sample_type_id")]]; ?></p></td> 
                            <td width="90"><p><? echo $color_arr[$row[csf("color_number_id")]]; ?></p></td>
                            <td align="center" width="80"><?
                        if ($row[csf("target_approval_date")] == "0000-00-00" || $row[csf("target_approval_date")] == "")
                            echo "&nbsp;";
                        else
                            echo change_date_format($row[csf("target_approval_date")]);
                        ?>&nbsp;</td>
                            <td align="center" width="80"><?
                        if ($row[csf("send_to_factory_date")] == "0000-00-00" || $row[csf("send_to_factory_date")] == "")
                            echo "&nbsp;";
                        else
                            echo change_date_format($row[csf("send_to_factory_date")]);
                        ?>&nbsp;</td>
                            <td align="center" width="80"><?
                        if ($row[csf("submitted_to_buyer")] == "0000-00-00" || $row[csf("submitted_to_buyer")] == "")
                            echo "&nbsp;";
                        else
                            echo change_date_format($row[csf("submitted_to_buyer")]);
                        ?>&nbsp;</td>
                            <td align="center" width="70"><? echo $approval_status[$row[csf("approval_status")]]; ?>&nbsp;</td>
                            <td align="center" width="80"><?
                        if ($row[csf("approval_status_date")] == "0000-00-00" || $row[csf("approval_status_date")] == "")
                            echo "&nbsp;";
                        else
                            echo change_date_format($row[csf("approval_status_date")]);
                        ?>&nbsp;</td>
                            <td width="70" bgcolor="<? echo $td_color; ?>" align="right"><? echo $delay_day; ?>&nbsp;&nbsp;</td>
                            <td><p><? echo $row[csf("sample_comments")]; ?>&nbsp;</p></td>
                        </tr>
                        <? 
                    } 
                    ?>
                </table>
            </div> 
        </fieldset>
    </div> 
    <?
    exit();
}

if ($action == "order_status") 
{
    echo load_html_head_contents("Sample Approve Details", "../../../../", 1, 1, $unicode, '', '');

    ?>
    <div style="width:100%" align="center">
        <fieldset style="width:300px">        
            <div style="width:100%;" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                        <tr>
                            <th width="35">SL</th>
                            <th width="100">PO Number</th>
                            <th width="90">Status</th>
                        </tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:270px; overflow-y:auto" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
                    $i = 0;
                    $sql = "SELECT po_number, is_confirmed from wo_po_break_down where job_no_mst='$job_number'";
                    //echo $sql; //and a.approval_status<>0
                    $res = sql_select($sql);
                    foreach ($res as $row) 
                    {
                        $i++;
                        if ($i % 2 == 0)
                            $bgcolor = "#EFEFEF";
                        else
                            $bgcolor = "#FFFFFF";
                       
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="35"><? echo $i; ?></td>
                            <td width="100"><p><?=$row['PO_NUMBER']; ?></p></td>
                            <td width="90"><p><?=$row_status[$row['IS_CONFIRMED']]; ?></p></td> 
                        </tr>
                        <? 
                    } 
                    ?>
                </table>
            </div> 
        </fieldset>
    </div> 
    <?
    exit();
}

if ($action == "closing_status") 
{
    echo load_html_head_contents("Sample Approve Details", "../../../../", 1, 1, $unicode, '', '');

    ?>
    <div style="width:100%" align="center">
        <fieldset style="width:300px">        
            <div style="width:100%;" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                        <tr>
                            <th width="35">SL</th>
                            <th width="100">PO Number</th>
                            <th width="60">Status</th>
                            <th width="60">Date</th>
                        </tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:270px; overflow-y:auto" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
                    $i = 0;
                    $sql = "SELECT po_number, shiping_status from wo_po_break_down where job_no_mst='$job_number'";
                    //echo $sql; //and a.approval_status<>0
                    $res = sql_select($sql);
                    foreach ($res as $row) 
                    {
                        $i++;
                        if ($i % 2 == 0)
                            $bgcolor = "#EFEFEF";
                        else
                            $bgcolor = "#FFFFFF";
                       
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="35"><? echo $i; ?></td>
                            <td width="100"><p><?=$row['PO_NUMBER']; ?></p></td>
                            <td width="60"><p><?=$shipment_status[$row['SHIPING_STATUS']]; ?></p></td> 
                            <td width="60"><p></p></td> 
                        </tr>
                        <? 
                    } 
                    ?>
                </table>
            </div> 
        </fieldset>
    </div> 
    <?
    exit();
}

if($action=='save_excel_column')
{
    $exp=explode("*_*", $data);
    
    $search_data=explode("*__*", $exp[1]);
    $cbo_company_name=$search_data[0];
    $cbo_buyer_name=str_replace("'", "", $search_data[1]);
    $cbo_year=str_replace("'", "", $search_data[2]);
    $txt_job_no=str_replace("'", "", $search_data[3]);
    $txt_style_ref=str_replace("'", "", $search_data[4]);
    $txt_order_no=str_replace("'", "", $search_data[5]);
    $cbo_shipment_status=str_replace("'", "", $search_data[6]);
    $cbo_status=str_replace("'", "", $search_data[7]);
    $cbo_order_status=str_replace("'", "", $search_data[8]);
    $cbo_report_type=str_replace("'", "", $search_data[9]);
    $cbo_date_category=str_replace("'", "", $search_data[10]);
    $txt_date_from=str_replace("'", "", $search_data[11]);
    $txt_date_to=str_replace("'", "", $search_data[12]);
    $cbo_year_selection=str_replace("'", "", $search_data[13]);

    if($db_type==0) {
        $txt_date_from = date("Y-m-d",strtotime($txt_date_from));
        $txt_date_to = date("Y-m-d",strtotime($txt_date_to));
    }
    else {
        $txt_date_from = date("d-M-Y",strtotime($txt_date_from));
        $txt_date_to = date("d-M-Y",strtotime($txt_date_to));
    }

    $data=$exp[0];
    $data=array_filter(explode("***", $data));
    $con = connect();
    if($db_type==0)
    {
        mysql_query("BEGIN");
    }
    $id = return_next_id_by_sequence("order_tracking_report_history_mst_seq", "order_tracking_report_history_mst", $con);
    $field_array = "id,company_id,cbo_buyer_name,cbo_year,txt_job_no,txt_style_ref,txt_order_no,cbo_shipment_status,cbo_status,cbo_order_status,cbo_report_type,cbo_date_category,txt_date_from,txt_date_to,cbo_year_selection,inserted_by,insert_date";
    $data_array = "(" . $id .','.$cbo_company_name.",'".$cbo_buyer_name."','".$cbo_year."','".$txt_job_no."','".$txt_style_ref."','".$txt_order_no."','".$cbo_shipment_status."','".$cbo_status."','".$cbo_order_status."','".$cbo_report_type."','".$cbo_date_category."','".$txt_date_from."','".$txt_date_to."','".$cbo_year_selection. "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";    
    $data_array_dtls = "";
    foreach ($data as $key => $value)
    {
        $dtls_id = return_next_id_by_sequence("order_tracking_report_history_dtls_seq", "order_tracking_report_history_dtls", $con);
        if ($data_array_dtls != "") $data_array_dtls .= ",";
        $column_name=str_replace(array("&", "*", "(", ")", "=","'","_","\r", "\n",'"','#'), "", $value);
        $data_array_dtls .= "(" . $dtls_id . "," . $id . "," . $_SESSION['logic_erp']['user_id']. ",'" . $column_name . "')";  
    } 
    $del1=execute_query("DELETE FROM order_tracking_report_history_mst WHERE inserted_by = ".$_SESSION['logic_erp']['user_id']);
    $del2=execute_query("DELETE FROM order_tracking_report_history_dtls WHERE user_id = ".$_SESSION['logic_erp']['user_id']);
   
    $dtls_field_array = "id,mst_id,user_id,column_name";
    $rID=sql_insert("order_tracking_report_history_mst",$field_array,$data_array,0);
    $rID1=sql_insert("order_tracking_report_history_dtls",$dtls_field_array,$data_array_dtls,0);  
    if($db_type==0)
    {
        if($rID  && $rID1 && $del1 && $del2){
            mysql_query("COMMIT");
            echo "0**".count($data);
        }
        else{
            mysql_query("ROLLBACK");
            echo "10**".$rID."**".$rID1."**".$del1."**".$del2;
        }
    }
    else if($db_type==2 || $db_type==1 )
    {
        if($rID  && $rID1 && $del1 && $del2){
            oci_commit($con);
            echo "0**".count($data);
        }
        else{
            oci_rollback($con);
            echo "10**".$rID."**".$rID1."**".$del1."**".$del2."** insert into order_tracking_report_history_dtls (".$dtls_field_array.") values".$data_array_dtls;
        }
    }
    disconnect($con);
}

if ($action == "report_excel_generate") 
{
    $user_id=$_SESSION['logic_erp']['user_id'];
    $sql="SELECT a.id,a.company_id,a.inserted_by,a.insert_date,a.cbo_buyer_name,a.cbo_year,a.txt_job_no,a.txt_style_ref,a.txt_order_no,a.cbo_shipment_status,a.cbo_order_status,a.cbo_status,a.cbo_report_type,a.cbo_date_category,b.column_name from order_tracking_report_history_mst a, order_tracking_report_history_dtls b where a.id=b.mst_id and a.inserted_by=$user_id";
    //echo $sql;
    $result=sql_select($sql);
    $column_name=array();
    $user_company_wise=array();
    $dynamic_table_width=0;
    $column_name['']='';
    foreach ($result as $row)
    {
        $column=$row[csf('column_name')];
        $column_name[$column]=$row[csf('column_name')];
    
        if($row[csf('column_name')]=='Sl.')
        {
            $dynamic_table_width+=30;
        }
        else if($row[csf('column_name')]=='Order Status')
        {
            $dynamic_table_width+=60;
        }
        else if($row[csf('column_name')]=='LC Company' || $row[csf('column_name')]=='Working Company' || $row[csf('column_name')]=='Buyer')
        {
            $dynamic_table_width+=100;
        }
        else if($row[csf('column_name')]=='Year')
        {
            $dynamic_table_width+=50;
        }
        else if($row[csf('column_name')]=='Job')
        {
            $dynamic_table_width+=90;
        }
        else{
             $dynamic_table_width+=80;
        }
        //$user_company_wise[$row[csf('inserted_by')]][$row[csf('company_id')]][$row[csf('insert_date')]][$row[csf('column_name')]]=1;
        $company_name   = str_replace("'", "", $row[csf('company_id')]);
        $buyer_name     = str_replace("'", "", $row[csf('cbo_buyer_name')]);
        $year_id        = str_replace("'", "", $row[csf('cbo_year')]);    
        $job_no         = str_replace("'", "", $row[csf('txt_job_no')]);
        $txt_style_ref  = str_replace("'", "", $row[csf('txt_style_ref')]);
        $txt_order_no   = str_replace("'", "", $row[csf('txt_order_no')]);
        $shipmentStatus= str_replace("'", "", $row[csf('cbo_shipment_status')]);
        $orderStatus   = str_replace("'","", $row[csf('cbo_order_status')]);
        $status         = str_replace("'", "", $row[csf('cbo_status')]);
        $report_type    = str_replace("'", "", $row[csf('cbo_report_type')]);
        $date_category  = str_replace("'", "", $row[csf('cbo_date_category')]);
    }

   // print_r($column_name);

    

    /*===============================================================================/
    /                              Create Library Array                              /
    /============================================================================== */
    $team_library   = return_library_array("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0", "id", "team_leader_name");
    $merchant_library   = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0", "id", "team_member_name");
    $country_library= return_library_array("select id, country_name from  lib_country", "id", "country_name");
    $buyer_library  = return_library_array("select id, buyer_name from  lib_buyer", "id", "buyer_name");
    $color_library  = return_library_array("select id, color_name from  lib_color", "id", "color_name");
    $size_library   = return_library_array("select id, size_name from  lib_size", "id", "size_name");
    $company_library= return_library_array("select id, company_name from  lib_company", "id", "company_name");
    $sub_dep_library= return_library_array("select id,sub_department_name from lib_pro_sub_deparatment status_active =1 and is_deleted=0", "id", "sub_department_name");
    $season_library = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", "id", "season_name");
    $brand_library = return_library_array("select id, brand_name from lib_buyer_brand brand where status_active =1 and is_deleted=0", "id", "brand_name");
    $sub_dep_library = return_library_array("select id,sub_department_name from lib_pro_sub_deparatment where status_active =1 and is_deleted=0", "id", "sub_department_name");
    $user_library = return_library_array("select id,user_full_name from user_passwd where status_active =1 and is_deleted=0", "id", "user_full_name");
    $bank_library = return_library_array("select bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1", "id", "bank_name");

    $fab_material=array(1=>"Organic",2=>"BCI");

    /*===============================================================================/
    /                                   Query Condition                              /
    /============================================================================== */
    $sqlCond = "";
    $sqlCond = !empty($company_name) ? " and a.company_name=$company_name" : "";
    $sqlCond .= !empty($buyer_name) ? " and a.buyer_name=$buyer_name" : "";
    $sqlCond .= !empty($year_id) ? " and to_char(a.insert_date,'YYYY')=$year_id" : "";
    $sqlCond .= !empty($job_no!="") ? " and a.job_no_prefix_num=$job_no" : "";
    $sqlCond .= !empty($txt_style_ref!="") ? " and a.style_ref_no='$txt_style_ref'" : "";
    $sqlCond .= !empty($txt_order_no!="") ? " and b.po_number='$txt_order_no'" : "";
    $sqlCond .= !empty($orderStatus) ? " and b.is_confirmed=$orderStatus" : "";
    $sqlCond .= !empty($status) ? " and b.status_active=$status" : "";

    if($shipmentStatus)
    {
        if ($shipmentStatus==4) 
        {
            $sqlCond .= " and b.shiping_status in(1,2)";
        }
        else
        {
            $sqlCond .= " and b.shiping_status=$shipmentStatus";
        }
    }

    if(str_replace("'", "", $txt_date_from) !="" && str_replace("'", "", $txt_date_to) !="")
    {
        switch ($date_category) 
        {
            case 1:
                $sqlCond .= " and b.shipment_date between $txt_date_from and $txt_date_to";
                break;

            case 2:
                $sqlCond .= " and b.shipment_date between $txt_date_from and $txt_date_to";
                break;    
            
            default:
                $sqlCond .= " and b.factory_received_date between $txt_date_from and $txt_date_to";
                break;
        }
    }

    /*===============================================================================/
    /                                  MAIN QUERY                                    /
    /============================================================================== */
    $sql = "SELECT a.id,a.job_no, a.company_name,a.style_owner,a.buyer_name,a.style_ref_no,a.requisition_no,a.style_description,a.repeat_job_no,a.season_buyer_wise,a.product_dept,a.product_code,a.pro_sub_dep,a.order_uom,  a.product_category,a.brand_id,a.region,a.sustainability_standard,a.fab_material,a.qlty_label,a.quality_level,(a.job_quantity*a.total_set_qnty) as job_qty_pcs,    to_char(a.insert_date,'YYYY') as year,a.ship_mode,a.set_smv,a.team_leader,a.dealing_marchant,a.factory_marchant,a.inserted_by,a.total_price,a.qlty_label,

        b.is_confirmed,b.id as po_id,b.po_number,b.excess_cut,(b.po_quantity*a.total_set_qnty) as po_quantity,b.unit_price,b.po_total_price,to_char(b.insert_date,'DD-MM-YYYY') as po_insert_date,to_char(b.po_received_date,'DD-MM-YYYY') as po_received_date,to_char(b.factory_received_date,'DD-MM-YYYY') as factory_received_date,to_char(b.pub_shipment_date,'DD-MM-YYYY') as pub_shipment_date,to_char(b.shipment_date,'DD-MM-YYYY') as shipment_date,to_char(b.txt_etd_ldd,'DD-MM-YYYY') as txt_etd_ldd,b.shiping_status,(b.pub_shipment_date-b.po_received_date) as lead_time,(b.pub_shipment_date - trunc(sysdate)) AS days_in_hand,

        c.country_id,c.item_number_id,c.color_number_id,c.size_number_id,c.order_quantity,c.plan_cut_qnty,to_char(c.cutup_date,'DD-MM-YYYY') as cutup_date,to_char(c.country_ship_date,'DD-MM-YYYY') as country_ship_date

        from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 $sqlCond";
    // echo $sql;die();
    $sql_res = sql_select($sql);
    if(count($sql_res)==0)
    {
        ?>
        <center>
            <div style="width: 80%;" class="alert alert-danger">Data not found.Please try again.</div>
        </center>
        <?
        die();
    }
    $data_array = array();
    $job_id_array = array();
    $job_no_array = array();
    $po_array = array();
    $po_wise_data = array();
    $po_wise_job_arr = array();
    $po_chk_array = array();
    $po_wise_unit_price_array = array();
    foreach ($sql_res as $val) 
    {
        $data_array[$val['JOB_NO']]['company_name'] = $val['COMPANY_NAME'];
        $data_array[$val['JOB_NO']]['buyer_name'] = $val['BUYER_NAME'];
        $data_array[$val['JOB_NO']]['wo_com_id'] = $val['STYLE_OWNER'];
        $data_array[$val['JOB_NO']]['year'] = $val['YEAR'];
        $data_array[$val['JOB_NO']]['style'] = $val['STYLE_REF_NO'];
        $data_array[$val['JOB_NO']]['req_no'] = $val['REQUISITION_NO'];
        $data_array[$val['JOB_NO']]['repeat_job_no'] = $val['REPEAT_JOB_NO'];
        $data_array[$val['JOB_NO']]['style_des'] = $val['STYLE_DESCRIPTION'];
        $data_array[$val['JOB_NO']]['season'] = $val['SEASON_BUYER_WISE'];
        $data_array[$val['JOB_NO']]['product_dept'] = $val['PRODUCT_DEPT'];
        $data_array[$val['JOB_NO']]['product_code'] = $val['PRODUCT_CODE'];
        $data_array[$val['JOB_NO']]['pro_sub_dep'] = $val['PRO_SUB_DEP'];
        $data_array[$val['JOB_NO']]['product_category'] = $val['PRODUCT_CATEGORY'];
        $data_array[$val['JOB_NO']]['brand_id'] = $val['BRAND_ID'];
        $data_array[$val['JOB_NO']]['region'] = $val['REGION'];
        $data_array[$val['JOB_NO']]['sustainability_standard'] = $val['SUSTAINABILITY_STANDARD'];
        $data_array[$val['JOB_NO']]['fab_material'] = $val['FAB_MATERIAL'];
        $data_array[$val['JOB_NO']]['order_nature'] = $val['QLTY_LABEL'];
        $data_array[$val['JOB_NO']]['quality_level'] = $val['QUALITY_LEVEL'];
        $data_array[$val['JOB_NO']]['qlty_label'] = $val['QLTY_LABEL'];
        $data_array[$val['JOB_NO']]['job_qty_pcs'] = $val['JOB_QTY_PCS'];
        $data_array[$val['JOB_NO']]['order_uom'] = $val['ORDER_UOM'];
        $data_array[$val['JOB_NO']]['ship_mode'] = $val['SHIP_MODE'];
        $data_array[$val['JOB_NO']]['set_smv'] = $val['SET_SMV'];
        $data_array[$val['JOB_NO']]['team_leader'] = $val['TEAM_LEADER'];
        $data_array[$val['JOB_NO']]['dealing_marchant'] = $val['DEALING_MARCHANT'];
        $data_array[$val['JOB_NO']]['factory_marchant'] = $val['FACTORY_MARCHANT'];
        $data_array[$val['JOB_NO']]['inserted_by'] = $val['INSERTED_BY'];
        $data_array[$val['JOB_NO']]['total_price'] = $val['TOTAL_PRICE'];

        $data_array[$val['JOB_NO']]['po_number'] .= $val['PO_NUMBER'].",";
        $data_array[$val['JOB_NO']]['order_status'] .= $val['IS_CONFIRMED'].",";
        $po_wise_data[$val['JOB_NO']]['excess_cut'] .= $val['PO_NUMBER']."=".$val['EXCESS_CUT']."**";

        $data_array[$val['JOB_NO']]['country_id'] .= $val['COUNTRY_ID'].",";
        $data_array[$val['JOB_NO']]['item_id'] .= $val['ITEM_NUMBER_ID'].",";
        $data_array[$val['JOB_NO']]['color_id'] .= $val['COLOR_NUMBER_ID'].",";
        $data_array[$val['JOB_NO']]['size_id'] .= $val['SIZE_NUMBER_ID'].",";
        $data_array[$val['JOB_NO']]['order_quantity'] += $val['ORDER_QUANTITY'];
        $data_array[$val['JOB_NO']]['plan_cut_qnty'] += $val['PLAN_CUT_QNTY'];
        if(!in_array($val['PO_ID'], $po_chk_array))
        {
            $data_array[$val['JOB_NO']]['po_quantity'] += $val['PO_QUANTITY'];
            $data_array[$val['JOB_NO']]['order_value'] += $val['PO_TOTAL_PRICE'];
            $data_array[$val['JOB_NO']]['unit_price'] .= ($data_array[$val['JOB_NO']]['unit_price']=="") ? $val['UNIT_PRICE'] : ", ".$val['UNIT_PRICE'];
            $data_array[$val['JOB_NO']]['po_insert_date'] .= ($data_array[$val['JOB_NO']]['po_insert_date']=="") ? $val['PO_INSERT_DATE'] : ", ".$val['PO_INSERT_DATE'];
            $data_array[$val['JOB_NO']]['po_received_date'] .= ($data_array[$val['JOB_NO']]['po_received_date']=="") ? $val['PO_RECEIVED_DATE'] : ", ".$val['PO_RECEIVED_DATE'];
            $data_array[$val['JOB_NO']]['factory_received_date'] .= ($data_array[$val['JOB_NO']]['factory_received_date']=="") ? $val['FACTORY_RECEIVED_DATE'] : ", ".$val['FACTORY_RECEIVED_DATE'];
            $data_array[$val['JOB_NO']]['pub_shipment_date'] .= ($data_array[$val['JOB_NO']]['pub_shipment_date']=="") ? $val['PUB_SHIPMENT_DATE'] : ", ".$val['PUB_SHIPMENT_DATE'];
            $data_array[$val['JOB_NO']]['shipment_date'] .= ($data_array[$val['JOB_NO']]['shipment_date']=="") ? $val['SHIPMENT_DATE'] : ", ".$val['SHIPMENT_DATE'];
            $data_array[$val['JOB_NO']]['txt_etd_ldd'] .= ($data_array[$val['JOB_NO']]['txt_etd_ldd']=="") ? $val['TXT_ETD_LDD'] : ", ".$val['TXT_ETD_LDD'];
            $data_array[$val['JOB_NO']]['shipment_status'] .= ($data_array[$val['JOB_NO']]['shipment_status']=="") ? $shipment_status[$val['SHIPING_STATUS']] : ", ".$shipment_status[$val['SHIPING_STATUS']];
            $data_array[$val['JOB_NO']]['lead_time'] .= ($data_array[$val['JOB_NO']]['lead_time']=="") ? $val['LEAD_TIME'] : ", ".$val['LEAD_TIME'];
            $data_array[$val['JOB_NO']]['days_in_hand'] .= ($data_array[$val['JOB_NO']]['days_in_hand']=="") ? $val['DAYS_IN_HAND'] : ", ".$val['DAYS_IN_HAND'];

            $po_chk_array[$val['PO_ID']] = $val['PO_ID'];
        }
        $data_array[$val['JOB_NO']]['cutup_date'] .= ($data_array[$val['JOB_NO']]['cutup_date']=="") ? $val['CUTUP_DATE'] : ",".$val['CUTUP_DATE'];
        $data_array[$val['JOB_NO']]['country_ship_date'] .= ($data_array[$val['JOB_NO']]['country_ship_date']=="") ? $val['COUNTRY_SHIP_DATE'] : ",".$val['COUNTRY_SHIP_DATE'];


        $job_id_array[$val['ID']] = $val['ID'];
        $job_no_array[$val['JOB_NO']] = $val['JOB_NO'];
        $po_array[$val['PO_ID']] = $val['PO_ID'];
        $po_wise_job_arr[$val['PO_ID']] = $val['JOB_NO'];
        $po_wise_unit_price_array[$val['PO_ID']] = $val['UNIT_PRICE'];
    }

    unset($sql_res);
    // echo "<pre>";print_r($po_array);echo "</pre>";die();
    /*===============================================================================/
    /                                  Job Image                                     /
    /============================================================================== */
    $job_cond = where_con_using_array($job_no_array,1,"master_tble_id");

    $imge_arr = return_library_array("SELECT master_tble_id,image_location from common_photo_library where file_type=1 $job_cond", 'master_tble_id', 'image_location');

    /*===============================================================================/
    /                                  Embel Name                                    /
    /============================================================================== */
    $job_cond = where_con_using_array($job_id_array,1,"job_id");
    $sql = "SELECT job_no,emb_name from wo_pre_cost_embe_cost_dtls where status_active=1 and is_deleted=0 and emb_type!=0 $job_cond";
    // echo $sql;
    $res = sql_select($sql);
    $emb_name_arr = array();
    $emb_id_chk_arr = array();
    foreach ($res as $val) 
    {
        if(!in_array($val['EMB_NAME'], $emb_id_chk_arr[$val['JOB_NO']]))
        {
            $emb_name_arr[$val['JOB_NO']] .= ($emb_name_arr[$val['JOB_NO']]=="") ? $emblishment_name_array[$val['EMB_NAME']] : ",".$emblishment_name_array[$val['EMB_NAME']];
            $emb_id_chk_arr[$val['JOB_NO']][$val['EMB_NAME']] = $val['EMB_NAME'];
        }
    }

    /*===============================================================================/
    /                                Conversion Name                                 /
    /============================================================================== */
    $job_cond = where_con_using_array($job_id_array,1,"job_id");
    $sql = "SELECT job_no,cons_process from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 $job_cond";
    // echo $sql;
    $res = sql_select($sql);
    $conv_name_arr = array();
    $conv_id_chk_arr = array();
    foreach ($res as $val) 
    {
        if(!in_array($val['CONS_PROCESS'], $conv_id_chk_arr[$val['JOB_NO']]))
        {
            $conv_name_arr[$val['JOB_NO']] .= ($conv_name_arr[$val['JOB_NO']]=="") ? $conversion_cost_head_array[$val['CONS_PROCESS']] : ",".$conversion_cost_head_array[$val['CONS_PROCESS']];
            $conv_id_chk_arr[$val['JOB_NO']][$val['CONS_PROCESS']] = $val['CONS_PROCESS'];
        }
    }
    // echo "<pre>";print_r($conv_id_chk_arr);die();
    /*===============================================================================/
    /                                  ACTUAL PO                                     /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"po_break_down_id");
    $sql = "SELECT job_no, acc_po_no FROM wo_po_acc_po_info where status_active=1 and is_deleted=0 $po_cond";
    $res = sql_select($sql);
    $acc_po_array = array();
    $acc_po_chk = array();
    foreach ($res as $key => $val) 
    {
        if(!in_array($val['ACC_PO_NO'], $acc_po_chk[$val['JOB_NO']]))
        {
            $acc_po_array[$val['JOB_NO']]['acc_po'] .= ($acc_po_array[$val['JOB_NO']]['acc_po']=="") ? $val['ACC_PO_NO'] : ", ".$val['ACC_PO_NO'];
            $acc_po_chk[$val['JOB_NO']][$val['ACC_PO_NO']] = $val['ACC_PO_NO'];
        }
    }

    /*===============================================================================/
    /                                  Booking Data                                  /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"b.po_break_down_id");
    $sql = "SELECT a.entry_form,a.process, b.job_no,a.item_category,to_char(a.booking_date,'DD-MM-YYYY') as booking_date,to_char(a.delivery_date,'DD-MM-YYYY') as delivery_date,a.booking_no,a.booking_no_prefix_num, b.po_break_down_id as po_id,a.booking_type,a.fabric_source,a.is_short,a.revised_no, b.fin_fab_qnty,b.grey_fab_qnty,a.is_approved,b.dia_width,b.gsm_weight,b.process_loss_percent,b.construction,b.copmposition,a.uom from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $booking_data_array = array();
    $pur_booking_data_array = array();
    $service_booking_data_array = array();
    $booking_qty_array = array();
    foreach ($res as $val) 
    {
        if($val['ITEM_CATEGORY']==2)
        {
            if($val['FABRIC_SOURCE']==1)
            {
            
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['booking_date'] .= $val['BOOKING_DATE'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['booking_no'] .= $val['BOOKING_NO'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['uom'] .= $val['UOM'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['delivery_date'] .= $val['DELIVERY_DATE'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['revised_no'] .= $val['REVISED_NO'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['is_approved'] .= $val['IS_APPROVED'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['dia_width'] .= $val['DIA_WIDTH'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['gsm_weight'] .= $val['GSM_WEIGHT'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['process_loss'] .= $val['PROCESS_LOSS_PERCENT'].",";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['construction'] .= $val['CONSTRUCTION']."__";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['copmposition'] .= $val['COPMPOSITION']."__";
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['fin_fab_qnty'] += $val['FIN_FAB_QNTY'];
                $booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']][$val['IS_SHORT']]['grey_fab_qnty'] += $val['GREY_FAB_QNTY'];
            }
            else
            {
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['booking_date'] .= $val['BOOKING_DATE'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['booking_no'] .= $val['BOOKING_NO'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['uom'] .= $val['UOM'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['delivery_date'] .= $val['DELIVERY_DATE'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['revised_no'] .= $val['REVISED_NO'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['is_approved'] .= $val['IS_APPROVED'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['dia_width'] .= $val['DIA_WIDTH'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['gsm_weight'] .= $val['GSM_WEIGHT'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['process_loss'] .= $val['PROCESS_LOSS_PERCENT'].",";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['construction'] .= $val['CONSTRUCTION']."__";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['copmposition'] .= $val['COPMPOSITION']."__";
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['fin_fab_qnty'] += $val['FIN_FAB_QNTY'];
                $pur_booking_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['BOOKING_TYPE']]['grey_fab_qnty'] += $val['GREY_FAB_QNTY'];
            }
            $booking_qty_array[$po_wise_job_arr[$val['PO_ID']]]['grey_fab_qnty'] += $val['GREY_FAB_QNTY'];
            $booking_qty_array[$po_wise_job_arr[$val['PO_ID']]]['fin_fab_qnty'] += $val['FIN_FAB_QNTY'];
        }

        if($val['ENTRY_FORM']==228)
        {
            $service_booking_data_array[$val['JOB_NO']]['knitting'] = $val['BOOKING_NO_PREFIX_NUM'];
        }

        if($val['ENTRY_FORM']==229)
        {
            $service_booking_data_array[$val['JOB_NO']]['dyeing'] = $val['BOOKING_NO_PREFIX_NUM'];
        }

        if($val['PROCESS']==35)
        {
            $service_booking_data_array[$val['JOB_NO']]['aop'] = $val['BOOKING_NO_PREFIX_NUM'];
        }
       
    }
    // echo "<pre>";print_r($booking_data_array);echo "</pre>";die();
    /*===============================================================================/
    /                                    Work order data                             /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"d.id");
    $sql="SELECT a.job_no,c.emb_name, f.booking_no from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d, wo_pre_cos_emb_co_avg_con_dtls e, wo_booking_dtls f
    where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.job_no=f.job_no and c.id=e.pre_cost_emb_cost_dtls_id and d.id=e.po_break_down_id and e.pre_cost_emb_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=6 and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $work_order_data_array = array();
    foreach ($res as $val) 
    {
        $bek = explode("-", $val['BOOKING_NO']);
        $work_order_data_array[$val['JOB_NO']][$val['EMB_NAME']] = $bek[3];
        
    }
    // echo "<pre>";print_r($work_order_data_array);die;
    /*===============================================================================/
    /                            YARN DYEING WORK ORDER DATA                         /
    /============================================================================== */
    $job_id_cond = where_con_using_array($job_id_array,0,"b.job_no_id");
    $sql="SELECT b.job_no,a.yarn_dyeing_prefix_num from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
    where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $job_id_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $yd_work_order_data_array = array();
    foreach ($res as $val) 
    {
        $yd_work_order_data_array[$val['JOB_NO']] = $val['YARN_DYEING_PREFIX_NUM'];
        
    }
    /*===============================================================================/
    /                                   YARN Allocation                              /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_break_down_id");
    $sql = "SELECT a.job_no,a.is_dyied_yarn,a.qnty from inv_material_allocation_dtls a where a.status_active=1 and a.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $yarn_allocation_array = array();
    foreach ($res as $val) 
    {
        $yarn_allocation_array[$val['JOB_NO']][$val['IS_DYIED_YARN']] += $val['QNTY'];
    }
    /*===============================================================================/
    /                                      Textile Data                              /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_breakdown_id");
    $sql = "SELECT a.po_breakdown_id,a.entry_form,a.trans_type,a.quantity from order_wise_pro_details a where a.status_active=1 and a.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $textile_data_array = array();
    $transfer_data_array = array();
    foreach ($res as $val) 
    {
        $textile_data_array[$po_wise_job_arr[$val['PO_BREAKDOWN_ID']]][$val['ENTRY_FORM']] += $val['QUANTITY'];
        $transfer_data_array[$po_wise_job_arr[$val['PO_BREAKDOWN_ID']]][$val['ENTRY_FORM']][$val['TRANS_TYPE']] += $val['QUANTITY'];
    }
    // echo "<pre>";print_r($textile_data_array);die;

    /*===============================================================================/
    /                             Yarn Rcv From Dyeing                               /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"c.po_breakdown_id");
    $sql = "SELECT c.po_breakdown_id as po_id, c.QUANTITY  from inv_receive_master a, inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and b.item_category=1 and a.entry_form=1 and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in(2) $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $yarn_rcv_from_dyeing_array = array();
    foreach ($res as $val) 
    {
        $yarn_rcv_from_dyeing_array[$po_wise_job_arr[$val['PO_ID']]] += $val['QUANTITY'];
    }
    // echo "<pre>";print_r($yarn_rcv_from_dyeing_array);die;

    /*===============================================================================/
    /                               knitting plan data                               /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_id");
    $sql="SELECT a.program_qnty,a.po_id from ppl_planning_entry_plan_dtls a where a.status_active=1 and a.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $program_data_array = array();
    foreach ($res as $val) 
    {
        $program_data_array[$po_wise_job_arr[$val['PO_ID']]] += $val['PROGRAM_QNTY'];
    }

    /*===============================================================================/
    /                               knitting production                              /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"c.po_breakdown_id");
    $sql="SELECT a.knitting_source,c.po_breakdown_id,c.quantity from inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form=2 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $knitting_data_array = array();
    foreach ($res as $val) 
    {
        $knitting_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['KNITTING_SOURCE']] += $val['PROGRAM_QNTY'];
    }

    /*===============================================================================/
    /                               Dyed yarn issue                                  /  
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"c.po_breakdown_id");
    $sql = "SELECT c.po_breakdown_id as po_id, c.QUANTITY  from inv_issue_master a, inv_transaction b,order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and d.id=c.prod_id and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.dyed_type=1 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $dyed_yarn_issue_array = array();
    foreach ($res as $val) 
    {
        $dyed_yarn_issue_array[$po_wise_job_arr[$val['PO_ID']]] += $val['QUANTITY'];
    }
    // echo "<pre>";print_r($dyed_yarn_issue_array);die;

    /*===============================================================================/
    /                                Roll Data                         
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_breakdown_id");
    $sql = "SELECT a.po_breakdown_id,a.entry_form,a.qnty from pro_roll_details a where a.status_active=1 and a.is_deleted=0  $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $roll_data_array = array();
    foreach ($res as $val) 
    {
        $roll_data_array[$po_wise_job_arr[$val['PO_BREAKDOWN_ID']]][$val['ENTRY_FORM']] += $val['QNTY'];
    }

    /*===============================================================================/
    /                                Fab Dyeing Prodduction                          /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_id");
    $sql = "SELECT c.load_unload_id,a.po_id,c.batch_qty,c.production_qty from pro_batch_create_dtls a, pro_fab_subprocess b,pro_fab_subprocess_dtls c where a.mst_id=b.batch_id and b.id=c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.load_unload_id=1 and b.result=1 and b.entry_form=35 $po_cond";
    // echo $sql;die;
    $res = sql_select($sql);
    $fab_dyeing_array = array();
    foreach ($res as $val) 
    {
        $fab_dyeing_array[$po_wise_job_arr[$val['PO_ID']]] += $val['PRODUCTION_QTY'];
    }

    /*===============================================================================/
    /                                  Finish Fabric Issue                           /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"c.po_breakdown_id");
    $sql = "SELECT c.po_breakdown_id as po_id,c.quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.entry_form=71 and trans_type=2 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $fin_fab_data_array = array();
    foreach ($res as $val) 
    {
        $fin_fab_data_array[$po_wise_job_arr[$val['PO_ID']]]['qty'] + $val['QUANTITY'];
    }

    /*===============================================================================/
    /                                   Cut & Lay data                               /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.order_id");
    $sql = "SELECT b.entry_date, a.order_id, a.bundle_no,a.size_qty from ppl_cut_lay_bundle a,ppl_cut_lay_mst b where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond";
    // echo $sql;die;
    $res = sql_select($sql);
    $lay_data_array = array();
    foreach ($res as $val) 
    {
        $lay_data_array[$po_wise_job_arr[$val['ORDER_ID']]]['entry_date'] .= $val['ENTRY_DATE'].",";
        $lay_data_array[$po_wise_job_arr[$val['ORDER_ID']]]['qty'] += $val['SIZE_QTY'];
        $lay_data_array[$po_wise_job_arr[$val['ORDER_ID']]]['no_of_bndl']++;
    }

    /*===============================================================================/
    /                                  Gmts Production                               /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_break_down_id");
    $sql = "SELECT a.production_source,a.serving_company, a.production_date,a.production_type,a.po_break_down_id as po_id,b.production_qnty,a.embel_name from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond order by a.production_date";
    // echo $sql;
    $res = sql_select($sql);
    $gmts_data_array = array();
    $embel_data_array = array();
    foreach ($res as $val) 
    {
        $gmts_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']]['date'] .= $val['PRODUCTION_DATE'].",";
        $gmts_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']]['qty'] += $val['PRODUCTION_QNTY'];

        $embel_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['date'] .= $val['PRODUCTION_DATE'].",";
        $embel_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['source'] .= $val['PRODUCTION_SOURCE'].",";
        $embel_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['wo_com_id'] .= $val['SERVING_COMPANY'].",";
        $embel_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['qty'] += $val['PRODUCTION_QNTY'];
    }
    // echo "<pre>";print_r($gmts_data_array);echo "</pre>";die();

    /*===============================================================================/
    /                            Printing & Emb Order Data                           /
    /============================================================================== */
    // 204=printing, 311=emb
    $po_cond = where_con_using_array($po_array,0,"b.buyer_po_no");
    $sql = "SELECT a.entry_form, b.buyer_po_no as po_id, b.order_quantity from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_deleted=0 and a.within_group=1 and a.entry_form in(204,311) $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $print_emb_order_data_array = array();
    foreach ($res as $val) 
    {
        $print_emb_order_data_array[$po_wise_job_arr[$val['PO_ID']]][$val['ENTRY_FORM']]['qty'] += $val['ORDER_QUANTITY'];
    }

    /*===============================================================================/
    /                                 Buyer Inspection                               /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_break_down_id");
    $sql = "SELECT a.job_no,a.inspection_qnty,to_char(a.inspection_date,'DD-MM-YYYY') as inspection_date from pro_buyer_inspection a where a.status_active=1 and a.is_deleted=0 $po_cond";
    $res = sql_select($sql);
    $buyer_insp_data = array();
    foreach ($res as $val) 
    {
        $buyer_insp_data[$val['JOB_NO']]['qty'] += $val['INSPECTION_QNTY'];
        $buyer_insp_data[$val['JOB_NO']]['date'] = $val['INSPECTION_DATE'];
    }

    /*===============================================================================/
    /                                  ex-factory data                               /
    /============================================================================== */
    $sql = "SELECT a.id,a.po_break_down_id as po_id,a.ex_factory_date,(case when a.entry_form !=85 then b.production_qnty else 0 end) as production_qnty,(case when a.entry_form =85 then b.production_qnty else 0 end) as return_qnty,a.total_carton_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $po_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $ex_data_array = array();
    $id_chk_array = array();
    foreach ($res as $val) 
    {
        $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['ex_date'] .= $val['EX_FACTORY_DATE'].",";
        $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['ex_qty'] += $val['PRODUCTION_QNTY'];
        $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['rtn_qty'] += $val['RETURN_QNTY'];
        $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['ex_value'] += ($val['PRODUCTION_QNTY']*$po_wise_unit_price_array[$val['PO_ID']]);
        $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['ex_rtn_value'] += ($val['RETURN_QNTY']*$po_wise_unit_price_array[$val['PO_ID']]);
        if(!in_array($val['ID'], $id_chk_array))
        {
            $ex_data_array[$po_wise_job_arr[$val['PO_ID']]]['cartot_qnty'] += $val['TOTAL_CARTON_QNTY'];
            $id_chk_array[$val['ID']] = $val['ID'];
        }
    }
    // echo "<pre>";print_r($ex_data_array);die();
    /*===============================================================================/
    /                                 Export Invoice                                 /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"b.po_breakdown_id");
    $sql = "SELECT b.po_breakdown_id as po_id,b.current_invoice_qnty,b.current_invoice_value,a.invoice_no from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $po_cond";
    $res = sql_select($sql);
    $invice_data = array();
    foreach ($res as $val) 
    {
        $invice_data[$po_wise_job_arr[$val['PO_ID']]]['inv_qty']+=$val['CURRENT_INVOICE_QNTY'];
        $invice_data[$po_wise_job_arr[$val['PO_ID']]]['inv_value']+=$val['CURRENT_INVOICE_VALUE'];
    }

    /*===============================================================================/
    /                                      LC/SC data                                /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"b.wo_po_break_down_id");
    $sql = "SELECT b.wo_po_break_down_id as po_id,a.lien_bank,a.pay_term,a.internal_file_no,a.tenor,max(c.amendment_no) as amendment_no from com_export_lc a,com_export_lc_order_info b, com_export_lc_amendment c where a.id=b.com_export_lc_id AND a.export_lc_no = c.export_lc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond group by b.wo_po_break_down_id,a.lien_bank,a.pay_term,a.internal_file_no,a.tenor
    UNION ALL
    SELECT b.wo_po_break_down_id as po_id,a.lien_bank,a.pay_term,a.internal_file_no,a.tenor,max(c.amendment_no) as amendment_no from com_sales_contract a,com_sales_contract_order_info b,com_sales_contract_amendment c where a.id=b.com_sales_contract_id and a.contract_no=c.contract_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond group by b.wo_po_break_down_id,a.lien_bank,a.pay_term,a.internal_file_no,a.tenor
    ";
    // echo $sql;die();
    $res = sql_select($sql);
    $lc_sc_data_array = array();
    foreach ($res as $val) 
    {
        $lc_sc_data_array[$po_wise_job_arr[$val['PO_ID']]]['lien_bank'] .= $bank_library[$val['LIEN_BANK']].",";
        $lc_sc_data_array[$po_wise_job_arr[$val['PO_ID']]]['pay_term'] .= $pay_term[$val['PAY_TERM']].",";
        $lc_sc_data_array[$po_wise_job_arr[$val['PO_ID']]]['internal_file_no'] .= $val['INTERNAL_FILE_NO'].",";
        $lc_sc_data_array[$po_wise_job_arr[$val['PO_ID']]]['tenor'] .= $val['TENOR'].",";
        $lc_sc_data_array[$po_wise_job_arr[$val['PO_ID']]]['amendment_no'] .= $val['AMENDMENT_NO'].",";
    }
    // echo "<pre>";print_r($lc_sc_data_array);die();

    /*===============================================================================/
    /                                      TNA Data                                  /
    /============================================================================== */
    $po_cond = where_con_using_array($po_array,0,"a.po_number_id");
    $sql = "SELECT a.po_number_id,a.task_number,max(a.task_start_date) as start_date,max(a.task_finish_date) as end_date,max(a.actual_start_date) as actual_start_date,max(a.actual_finish_date) as actual_finish_date,b.job_no_mst from tna_process_mst a,wo_po_break_down b where a.po_number_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.task_start_date is not null and b.po_quantity>0 $po_cond and a.task_type=1 group by a.po_number_id,a.task_number,b.job_no_mst";
    // echo $sql;die();
    $res = sql_select($sql);
    $tnaDateArray = array();
    foreach ($res as $val) 
    {
        $tnaDateArray[$val['JOB_NO_MST']][$val['TASK_NUMBER']]['start_date']          = $val['START_DATE'];
        $tnaDateArray[$val['JOB_NO_MST']][$val['TASK_NUMBER']]['end_date']            = $val['END_DATE'];
        $tnaDateArray[$val['JOB_NO_MST']][$val['TASK_NUMBER']]['actual_start_date']   = $val['ACTUAL_START_DATE'];
        $tnaDateArray[$val['JOB_NO_MST']][$val['TASK_NUMBER']]['actual_finish_date']  = $val['ACTUAL_FINISH_DATE'];
    }

    // echo "<pre>";print_r($tnaDateArray);die();
    /*===============================================================================/
    /                                    Budget Data                                 /
    /============================================================================== */
    $job_id_cond = where_con_using_array($job_id_array,0,"a.job_id");
    $sql = "SELECT a.job_no,to_char(a.costing_date,'DD-MM-YYYY') as costing_date,a.approved,b.cm_cost from wo_pre_cost_mst a,wo_pre_cost_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_id=b.job_id $job_id_cond";
    // echo $sql;die();
    $res = sql_select($sql);
    $budget_data_array = array();
    foreach ($res as $val) 
    {
        $budget_data_array[$val['JOB_NO']]['costing_date'] = $val['COSTING_DATE'];
        $budget_data_array[$val['JOB_NO']]['approved'] = $val['APPROVED'];
        $budget_data_array[$val['JOB_NO']]['cm_cost'] = $val['CM_COST'];
    }

    /*===============================================================================/
    /                              Getting data from class                           /
    /============================================================================== */
    $poIDS = implode(",", $po_array);
    $condition= new condition();     
    $condition->po_id_in($poIDS);     
    $condition->init();
    // $fabric= new fabric($condition);
    $yarn= new yarn($condition);
    $yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
    $yarn_qty_amount_arr=$yarn->getJobWiseYarnQtyAndAmountArray();

    $yarnDataWithFabricidArr=$yarn->get_By_Precostfabricdtlsid_YarnQtyAmountArray();

    $fabric= new fabric($condition);
    $fabricAmoutByFabricSource= $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
    $fabricQtyByFabricSource= $fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish_purchase();
    
    $fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
    $fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
    $conversion= new conversion($condition);
    $conversion_costing_arr_process=$conversion->getAmountArray_by_job();
    $conv_qty_job_process= $conversion->getQtyArray_by_jobAndProcess();
    $conv_amount_job_process= $conversion->getAmountArray_by_jobAndProcess();
    $con_qty_fabric_process = $conversion->getQtyArray_by_fabricAndProcess();
    $con_amount_fabric_process = $conversion->getAmountArray_by_fabricAndProcess();

    $trims= new trims($condition);
    $trims_costing_arr=$trims->getAmountArray_by_job();
    $trims_qty_arr=$trims->getQtyArray_by_job();

    $emblishment= new emblishment($condition);
    $emblishment_costing_arr=$emblishment->getAmountArray_by_job();
    $emb_qty_job_name_arr = $emblishment->getQtyArray_by_jobAndEmbname();
    $emb_amount_job_name_arr = $emblishment->getAmountArray_by_jobAndEmbname();

    $wash= new wash($condition);
    $emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
    $wash_qty_job_name_arr =$wash->getQtyArray_by_jobAndEmbname();
    $wash_amount_job_name_arr =$wash->getAmountArray_by_jobAndEmbname();


    $commercial= new commercial($condition);
    $commercial_costing_arr=$commercial->getAmountArray_by_job();
    $commission= new commision($condition);
    $commission_costing_arr=$commission->getAmountArray_by_job();
    $other= new other($condition);
    $other_costing_arr=$other->getAmountArray_by_job();


    /*===============================================================================/
    /                        Functin for getting max min date                        /
    /============================================================================== */
    function get_max_min_date($date_arr)
    {
        $date_arr = explode(",", implode(",", $date_arr));
        $date_array = array();
        for ($i = 0; $i < count($date_arr); $i++)
        {
            if ($i == 0)
            {
                $max_date = date('d-m-Y', strtotime($date_arr[$i]));
                $min_date = date('d-m-Y', strtotime($date_arr[$i]));
                $date_array['max_date'] = $max_date;
                $date_array['min_date'] = $min_date;
            }
            elseif ($i != 0)
            {
                $new_date = date('d-m-Y', strtotime($date_arr[$i]));
                if ($new_date > $max_date)
                {
                    $max_date = $new_date;
                    $date_array['max_date'] = $max_date;
                }
                elseif ($new_date < $min_date)
                {
                    $min_date = $new_date;
                    $date_array['min_date'] = $min_date;
                }
            }
        }
        return $date_array;
    }

   

    //$tbl_width = 25330;
    $tbl_width = $dynamic_table_width;

    ob_start();
    ?>
    <fieldset>
        <div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
            <h2>Order Tracking Report</h2>
            <h2>Company : <?=$company_arr[$company_name]; ?></h2>
            <h2>Date : <?=change_date_format($date_from); ?>To<?=change_date_format($date_to); ?> </h2>
        </div>
        <style type="text/css">
            table thead tr td{
                padding: 5px;text-align: center;font-weight: bold;font-size: 16px;
            }
            .zoom {
              padding: 0px;
              transition: transform .2s; 
              width: 30px;
              height: 30px;
              margin: 0 auto;
              z-index: 9999 !important;
              overflow: hidden !important;
              visibility: visible;
              position: relative;
            }

            .zoom:hover {
              transform: scale(10); 

              z-index: 9999 !important;
              overflow: hidden !important;
              visibility: visible;
            }
        </style>
        <div class="report-container-part">
            <!-- ================================= report header ================================== -->
            <table cellspacing="0" border="1" class="rpt_table"   rules="all" width="<?=$tbl_width;?>"  align="center" id="table_header_1">
                <thead>
                    
                    <tr>
                        <?php if(!empty($column_name['Sl.'])): ?>
                            <th width="30" style="word-wrap: break-word;"><p>Sl.</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order Status'])): ?>
                            <th width="60" style="word-wrap: break-word;"><p>Order Status</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['LC Company'])): ?>
                            <th width="100" style="word-wrap: break-word;"><p>LC Company</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Working Company'])): ?>
                            <th width="100" style="word-wrap: break-word;"><p>Working Company</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Image'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Image</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Buyer'])): ?>
                            <th width="100" style="word-wrap: break-word;"><p>Buyer</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Year'])): ?>
                            <th width="50" style="word-wrap: break-word;"><p>Year</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Job'])): ?>
                            <th width="90" style="word-wrap: break-word;"><p>Job</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Repeat Job No'])): ?>
                        <th width="80" style="word-wrap: break-word;"><p>Repeat Job No</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Sample Req. No'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Sample Req. No</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Style Ref'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Style Ref</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Style Desc'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Style Desc</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Gmts Item'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Gmts Item</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Season'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Season</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Prod Dept'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Prod Dept</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Prod. Dept Code / Class'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Prod. Dept Code / Class</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Sub Dept'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Sub Dept</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Brand'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Brand</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Region'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Region</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Prod. Catg'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Prod. Catg</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Country'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Country</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Sustainability Standard'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Sustainability Standard</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Fabric Material'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Fabric Material</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order Nature'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Order Nature</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Quality Label'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Quality Label</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Embilshement Name'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Embilshement Name</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Service Name'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Service Name</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order/PO No'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Order/PO No</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Actual Order/PO No'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Actual Order/PO No</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['GMT Color'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>GMT Color</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Size'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Size</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Ex Cut %'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Ex Cut %</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order Qnty [Pcs]'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Order Qnty [Pcs]</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Break Down Order Qnty [Pcs]'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Break Down Order Qnty [Pcs]</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order Qnty with Cut% [Pcs]'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Order Qnty with Cut% [Pcs]</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order Qnty [Uom]'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Order Qnty [Uom]</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Uom'])): ?>
                            <th width="80" style="word-wrap: break-word;"><p>Uom</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Per Unit Price'])): ?>
                            <th width="80"><p>Per Unit Price</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order Value'])): ?>
                            <th width="80"><p>Order Value</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['PO Insert Date'])): ?>
                            <th width="80"><p>PO Insert Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['PO Receive Date'])): ?>
                            <th width="80"><p>PO Receive Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Factory Receive Date'])): ?>
                            <th width="80"><p>Factory Receive Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['1st Cut Date'])): ?>
                            <th width="80"><p>1st Cut Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Last Cut Date'])): ?>
                            <th width="80"><p>Last Cut Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['1st Sew Date'])): ?>
                            <th width="80"><p>1st Sew Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Last Sew Date'])): ?>
                            <th width="80"><p>Last Sew Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['1st Finish Date'])): ?>
                            <th width="80"><p>1st Finish Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Last Finish Date'])): ?>
                            <th width="80"><p>Last Finish Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Insp. Offer Date'])): ?>
                            <th width="80"><p>Insp. Offer Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Insp. Date'])): ?>
                            <th width="80"><p>Insp. Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Pub. Shipment Date'])): ?>
                            <th width="80"><p>Pub. Shipment Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['First Shipment Date'])): ?>
                            <th width="80"><p>First Shipment Date </p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Org. Shipment Date'])): ?>
                            <th width="80"><p>Org. Shipment Date </p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['ETD/LDD Date'])): ?>
                            <th width="80"><p>ETD/LDD Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Cut-off Date'])): ?>
                            <th width="80"><p>Cut-off Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Country Shipment Date'])): ?>
                            <th width="80"><p>Country Shipment Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['RFI Plan Date'])): ?>
                            <th width="80"><p>RFI Plan Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Shipment Mode'])): ?>
                            <th width="80"><p>Shipment Mode</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Lead Time'])): ?>
                            <th width="80"><p>Lead Time</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Days in Hand'])): ?>
                            <th width="80"><p>Days in Hand</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Commercial File No'])): ?>
                            <th width="80"><p>Commercial File No</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Lien Bank'])): ?>
                            <th width="80"><p>Lien Bank</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Ex. LC/SC Amendment No(Last)'])): ?>
                            <th width="80"><p>Ex. LC/SC Amendment No(Last)</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Pay Term'])): ?>
                            <th width="80"><p>Pay Term</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Tenor'])): ?>
                            <th width="80"><p>Tenor</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['First Shipment Date'])): ?>
                            <th width="80"><p>First Shipment Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Last Shipment Date'])): ?>
                            <th width="80"><p>Last Shipment Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Shipment Qnty'])): ?>
                            <th width="80"><p>Shipment Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Shipment Value'])): ?>
                            <th width="80"><p>Shipment Value</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Excess Shipment Qnty'])): ?>
                            <th width="80"><p>Excess Shipment Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Excess Shipment Value'])): ?>
                            <th width="80"><p>Excess Shipment Value</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Short Shipment Qnty'])): ?>
                            <th width="80"><p>Short Shipment Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Short Shipment Value'])): ?>
                            <th width="80"><p>Short Shipment Value</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Shipping Status'])): ?>
                            <th width="80"><p>Shipping Status</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['SMV'])): ?>
                            <th width="80"><p>SMV</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Total SMV'])): ?>
                            <th width="80"><p>Total SMV</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['CM'])): ?>
                            <th width="80"><p>CM</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Team Leader'])): ?>
                            <th width="80"><p>Team Leader</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Dealing Merchandiser'])): ?>
                            <th width="80"><p>Dealing Merchandiser</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Factory Merchandiser'])): ?>
                            <th width="80"><p>Factory Merchandiser</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Insert User'])): ?>
                            <th width="80"><p>Insert User</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Lab Dip No'])): ?>
                            <th width="80"><p>Lab Dip No</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Buyer Sample Status'])): ?>
                            <th width="80"><p>Buyer Sample Status</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order Status'])): ?>
                            <th width="80"><p>Order Status</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Referance Closed'])): ?>
                            <th width="80"><p>Referance Closed</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Emb. Work Order No'])): ?>
                            <th width="80"><p>Emb. Work Order No</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Service Work Order No'])): ?>
                            <th width="80"><p>Service Work Order No</p></th>
                        <?php endif ?>


                        <?php if(!empty($column_name['Budget Date'])): ?>
                            <th width="80"><p>Budget Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Budget Value'])): ?>
                            <th width="80"><p>Budget Value</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Open Value'])): ?>
                            <th width="80"><p>Open Value</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Approved'])): ?>
                            <th width="80"><p>Approved</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Amendment No'])): ?>
                            <th width="80"><p>Amendment No</p></th>
                        <?php endif ?>


                        <?php if(!empty($column_name['M Fabric Booking Date'])): ?>
                            <th width="80"><p>M Fabric Booking Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['M Fabric Booking No'])): ?>
                            <th width="80"><p>M Fabric Booking No</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Fabric Delivery Date'])): ?>
                            <th width="80"><p>Fabric Delivery Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Amendment No'])): ?>
                            <th width="80"><p>Amendment No</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Amendment Date'])): ?>
                            <th width="80"><p>Amendment Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Approved Status'])): ?>
                            <th width="80"><p>Approved Status</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Yarn Description'])): ?>
                            <th width="80"><p>Yarn Description</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Fabric Construction'])): ?>
                            <th width="80"><p>Fabric Construction</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Fabric Composition'])): ?>
                            <th width="80"><p>Fabric Composition</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['GSM'])): ?>
                            <th width="80"><p>GSM</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Dia/Width'])): ?>
                            <th width="80"><p>Dia/Width</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Finish Quantity'])): ?>
                            <th width="80"><p>Finish Quantity</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Process Loss%'])): ?>
                            <th width="80"><p>Process Loss%</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Grey Qnty Kg'])): ?>
                            <th width="80"><p>Grey Qnty Kg</p></th>
                        <?php endif ?>


                        <?php if(!empty($column_name['Fabric Booking Date'])): ?>
                            <th width="80"><p>Fabric Booking Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Fabric Booking No'])): ?>
                            <th width="80"><p>Fabric Booking No</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Fabric Delivery Date'])): ?>
                            <th width="80"><p>Fabric Delivery Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Fabrication'])): ?>
                            <th width="80"><p>Fabrication</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Uom'])): ?>
                            <th width="80"><p>Uom</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Qnty'])): ?>
                            <th width="80"><p>Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Approved Status'])): ?>
                            <th width="80"><p>Approved Status</p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Fabric Booking Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Fabric Booking No</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Fabric Delivery Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Fabrication</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Uom</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Finish Quantity</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Process Loss%</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Grey Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Approved Status</p></th>
                        <?php endif ?>


                        <?php if(!empty($column_name[''])): ?>
                            <th width="80" ><p>Trims/Acc Status</p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Grey Yarn Require with Short/EFR Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Yarn Allocation Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Yarn Excess Allocation Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Yarn Received Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Yarn Receive Balance Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Yarn Issued Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Yarn Issue Balance Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Yarn Excess Issued Qty </p></th>
                        <?php endif ?>


                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Dyed Yarn Require with Short/EFR Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Dyed Yarn Allocation Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Dyed Yarn Excess Allocation Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Dyed Yarn Received Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Dyed Yarn Receive Balance Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Dyed Yarn Issued Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Dyed Yarn Issue Balance Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Dyed Yarn Excess Issued Qty </p></th>
                        <?php endif ?>


                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Dyed Yarn Require with Short/EFR Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Service Work Order Qty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Yarn Dyed Produciton Qty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Yarn Dyed Produciton WIP Qty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Yarn Dyed Delivery Qty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Yarn Dyed Challan No</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Yarn Dyed Challan Date</p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Knitting Require with EFR Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Knitting Plan Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Knitting Plan WIP Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Knitting Work Order No</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Knitting Production Qnty [Inbound]</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Knitting Production Qnty [Outbound]</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>TTL Knitting Production Qnty </p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>TTL Knitting Excess Production Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>TTL Knitting Production WIP Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Price/KG [TK]</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Total Price [TK]</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Knitting Grey Fabric Delivery Qnty To Store</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Closing Status</p></th>
                        <?php endif ?>


                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Require with Short/EFR Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Normal Received Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Normal Receive Excess Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Receive Balance Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Transfer Received Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>TTL Fabric Received Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Normal Issued Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Trasfered Issued Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>TTL Fabric Issued Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Stock In Hand</p></th>
                        <?php endif ?>




                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Require with Short/EFR Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Fabric Received by Batch </p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Batch Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Production Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Production Excess Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Production WIP Qnty</p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Require with Short/EFR Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Production Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Production Excess Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Production WIP Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Process Name</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>QC Received Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>AOP Issue Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>AOP Receive Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>AOP Receive WIP Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>QC Delivery Qnty To Store</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>QC Stock in Hand</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Closing Status</p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Require with Short/EFR Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Normal Received Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Normal Receive Excess Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Receive Balance Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Transfer Received Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>TTL Received Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Normal Issued Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Trasfered Issued Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>TTL Issued Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Stock In Hand</p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Require with Short/EFR Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Fabric Receive Qnty By Cutting</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Excess Receive Qnty By Cutting </p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Receive Balance Qnty By Cutting</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Cutting Status</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Oreder Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Excess Cutting %</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Order Qnty with C%</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Cutting Bundle Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Cutting Lay Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Cutting Production [QC] Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Excess Cutting Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Cutting Production Balance Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Input Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Input WIP Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Print Send Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Print Receive Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Print WIP Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Print Supplier</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>EMB Send Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>EMB Receive Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>EMB WIP Balance Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>EMB Supplier</p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Require Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Order Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Order Balance Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Production [QC] Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Production Excess Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Production WIP Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Delivery Qnty To Cutting</p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Require Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Work Order Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Work Order Balance Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Production [QC] Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Production Excess Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Production WIP Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Delivery Qnty To Cutting</p></th>
                        <?php endif ?>


                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Require Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Input Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Input Excess Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Input Balance Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Production [Output] Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Production Excess Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Production WIP Qnty</p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Require Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Iron Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>HANGTAG Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>POLY Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Finish Quantity</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Balance Qty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Inspection Offer Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Inspection Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Inspection Qnty</p></th>
                        <?php endif ?>




                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Carton Qty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Shipment Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Invoice Qty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Shipment Value</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Invoice Value</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Net Invoice Value</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Excess Shipment Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Excess Shipment Value </p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Short Shipment Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Short Shipment Value</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Shipment Return Qnty</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Shipment Return Value</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>First Shipment Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Last Shipment Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Discount / Penalty / Claim Amount</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Reason of Discount / Penalty / Claim </p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Budget Date</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Budget Yarn Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Actual Yarn Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Budget Finish YD Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Actual Finish YD Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Budget Knitting Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Actual Knitting Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Budget Dyeing & Finishing Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Actual Dyeing & Finishing Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Budget Fabric Purchase Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Actual Fabric Purchase Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Budget AOP Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Actual AOP Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Budget Print Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Actual Print Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Budget Emb Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Actual Emb Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Budget Wash Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Actual Wash Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Budget Trims Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Actual Trims Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Budget MISC Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Actual MISC Cost</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Budget CM</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Actual CM</p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Lead Time [Templete]</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Yarn Receive Start</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Yarn Receive End</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Knitting Start</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Knitting End</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Dyeing Start</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Dyeing End</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Finish Fabric Prod. Start</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Finish Fabric Prod. End</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Trims Receive Start</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Trims Receive End</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Cutting Start</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Cutting End</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Print Start</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Print End</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Emb Start</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Emb End</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Sewing Start</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Sewing End</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>GMT Finihsing Start</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>GMT Finihsing End</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Inspection Start</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Inspection End</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Shipment  Start</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p>Shipment  End</p></th>
                        <?php endif ?>
                    </tr>
                </thead>
            </table>
            <!-- =================================== report body ==================================== -->
            <div style=" max-height:300px; width:<?=$tbl_width+20;?>px; overflow-y:scroll;" id="scroll_body">
                <table cellspacing="0" border="1" class="rpt_table"   rules="all" width="<?=$tbl_width;?>"  align="center" id="table_body">
                    <tbody>
                        <?
                        $i=1;
                        $gr_job_total = 0;
                        $gr_po_total = 0;
                        $gr_color_size_total = 0;
                        $gr_plan_cut_total = 0;
                        $gr_order_value_total = 0;

                        foreach ($data_array as $job_key => $row) 
                        {   
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";   
                            $ship_excess_qty = max($ex_data_array[$job_key]['ex_qty'] - $row['order_quantity'],0);
                            $ship_excess_val = max($ex_data_array[$job_key]['ex_value'] - $row['order_value'],0); 
                            $ship_short_qty = max($row['order_quantity'] - $ex_data_array[$job_key]['ex_qty'],0);
                            $ship_short_val = max($row['order_value'] - $ex_data_array[$job_key]['ex_value'],0);

                            // =============================== for budget data ===========================
                            $finishing_arr = array('209','165','33','94','63','171','65','170','156','179','200','208','127','125','84','68','128','190','242','240','192','172','90','218','67','197','73','66','185','142');
                            $total_finishing_amount=0;
                            $total_finishing_qty=0;
                            $other_cost_attr = array('inspection','freight','certificate_pre_cost','deffdlc_cost','design_cost','studio_cost','common_oh','interest_cost','incometax_cost','depr_amor_pre_cost');
                            $total_other_cost = 0;
                            foreach ($other_cost_attr as $attr) {
                                $total_other_cost+=$other_costing_arr[$job_key][$attr];
                            }
                            $misc_cost=$other_costing_arr[$job_key]['lab_test']+$commercial_costing_arr[$job_key]+$commission_costing_arr[$job_key]+$total_other_cost;

                            foreach ($finishing_arr as $fid) {
                                $total_finishing_amount += array_sum($conv_amount_job_process[$job_key][$fid]);
                                $total_finishing_qty += array_sum($conv_qty_job_process[$job_key][$fid]);
                            }

                            $total_fabic_cost=0;
                            if(count($conv_amount_job_process[$job_key][31])>0){
                                $total_fabic_cost+=array_sum($conv_amount_job_process[$job_key][31])/array_sum($conv_qty_job_process[$job_key][31]);
                            }
                            $total_fabric_amount +=array_sum($conv_amount_job_process[$job_key][31]);
                            $total_fabric_per +=array_sum($conv_amount_job_process[$job_key][31])/$order_values*100;
                            if(count($conv_amount_job_process[$job_key][30])>0){
                                $total_fabic_cost+=array_sum($conv_amount_job_process[$job_key][30])/array_sum($conv_qty_job_process[$job_key][30]);
                            }
                            if($yarn_qty_amount_arr[$job_key]['amount']!=''){
                                $total_fabic_cost+=$yarn_qty_amount_arr[$job_key]['amount']/$yarn_qty_amount_arr[$job_key]['qty'];
                            }
                            $total_fabric_amount +=$yarn_qty_amount_arr[$job_key]['amount']; 
                            $total_fabric_per +=$yarn_qty_amount_arr[$job_key]['amount']/$order_values*100;
                            if($total_finishing_amount!=0){
                                $total_fabic_cost+=$total_finishing_amount/$total_finishing_qty;
                            } 
                            $total_fabric_amount +=$total_finishing_amount;
                            $total_fabric_per +=$total_finishing_amount/$order_values*100;
                            $total_fabric_amount +=array_sum($conv_amount_job_process[$job_key][30]);
                            $total_fabric_per +=array_sum($conv_amount_job_process[$job_key][30])/$order_values*100;
                            if(count($conv_amount_job_process[$job_key][35])>0){
                                $total_fabic_cost+=array_sum($conv_amount_job_process[$job_key][35])/array_sum($conv_qty_job_process[$job_key][35]);
                            }
                            $total_fabric_amount +=array_sum($conv_amount_job_process[$job_key][35]); 
                            $total_fabric_per +=array_sum($conv_amount_job_process[$job_key][35])/$order_values*100;
                            if(count($conv_amount_job_process[$job_key][1])>0){
                                $total_fabic_cost+=array_sum($conv_amount_job_process[$job_key][1])/array_sum($conv_qty_job_process[$job_key][1]);
                            }
                            $total_fabric_amount +=array_sum($conv_amount_job_process[$job_key][1]);
                            $total_fabric_per +=array_sum($conv_amount_job_process[$job_key][1])/$order_values*100; 

                            $purchase_amount = array_sum($fabricAmoutByFabricSource['knit']['grey'][$job_key])+array_sum($fabricAmoutByFabricSource['woven']['grey'][$job_key]);
                            $purchase_qty = array_sum($fabricQtyByFabricSource['knit']['grey'][$job_key])+array_sum($fabricQtyByFabricSource['woven']['grey'][$job_key]);

                            $ather_emb_attr = array(4,5,6,99);
                            foreach ($ather_emb_attr as $att) {
                                $others_emb_amount += $emb_amount_job_name_arr[$job_key][$att];
                                $others_emb_qty += $emb_qty_job_name_arr[$job_key][$att];
                            }
                            $knitting_amount_summ=''; $dyeing_amount_summ=''; $yds_amount_summ=''; $aop_amount_summ='';
                            if(count($conv_amount_job_process[$job_key][1])>0) {
                                $knitting_amount_summ = fn_number_format(array_sum($conv_amount_job_process[$job_key][1]),2);
                            }
                            $yarn_amount_summ = $yarn_qty_amount_arr[$job_key]['amount'];
                            $print_amount_summ = $emb_amount_job_name_arr[$job_key][1];      
                            $emb_amount_summ= $emb_amount_job_name_arr[$job_key][2];
                            $wash_amount_summ = $wash_amount_job_name_arr[$job_key][3];
                            if(count($conv_amount_job_process[$job_key][31])>0) {
                                $dyeing_amount_summ=  array_sum($conv_amount_job_process[$job_key][31]);
                            }
                            if(count($conv_amount_job_process[$job_key][30])>0) {
                                $yds_amount_summ = array_sum($conv_amount_job_process[$job_key][30]);
                            }
                            if(count($conv_amount_job_process[$job_key][35])>0) {
                                $aop_amount_summ = array_sum($conv_amount_job_process[$job_key][35]);
                            }
                            
                            $total_budget_value = $yarn_amount_summ+$total_finishing_amount+$print_amount_summ+$trims_costing_arr[$job_key]+$yds_amount_summ+$aop_amount_summ+$emb_amount_summ+$knitting_amount_summ+$purchase_amount+$wash_amount_summ+$other_costing_arr[$job_key]['cm_cost']+$dyeing_amount_summ+$others_emb_amount+$misc_cost;
                            $open_value = $row['total_price']-$total_budget_value;
                            ?>
                            <tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
                                <?php if(!empty($column_name['Sl.'])): ?>
                                    <td width="30">.<p><?=$i;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Order Status'])): ?>
                                    <td width="60">
                                            <p>
                                                <?
                                                $order_status_arr = array_unique(array_filter(explode(",", $row['order_status'])));
                                                $order_status_name = "";
                                                foreach ($order_status_arr as $val) 
                                                {
                                                    $order_status_name .= ($order_status_name=="") ? $order_status[$val] : ", ".$order_status[$val];
                                                }
                                                echo $order_status_name;
                                                ?>
                                                
                                            </p>
                                        </td>
                                <?php endif ?>

                                <?php if(!empty($column_name['LC Company'])): ?>
                                    <td width="100"><p><?=$company_library[$row['company_name']];?></p></td>
                                <?php endif ?>
                                
                                <?php if(!empty($column_name['Working Company'])): ?>
                                    <td width="100"><p><?=$company_library[$row['wo_com_id']];?></p></td>
                                <?php endif ?>
                                
                                <?php if(!empty($column_name['Image'])): ?>
                                    <td width="80"><img class="zoom" src='../../../<?= $imge_arr[$job_key]; ?>' height='40' width='50' /></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Buyer'])): ?>
                                    <td width="100"><p><?=$buyer_library[$row['buyer_name']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Year'])): ?>
                                    <td width="50" align="center"><p><?=$row['year'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Job'])): ?>
                                    <td width="90"><p><?=$job_key;?></p></td>
                                <?php endif ?>
                                
                                <?php if(!empty($column_name['Repeat Job No'])): ?>
                                    <td width="80"><p><?=$row['repeat_job_no'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Sample Req. No'])): ?>
                                    <td width="80"><p><?=$row['req_no'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Style Ref'])): ?>
                                    <td width="80"><p><?=$row['style'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Style Desc'])): ?>
                                    <td width="80"><p><?=$row['style_des'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Gmts Item'])): ?>
                                    <td width="80">
                                        <p>
                                            <?
                                            $item_id_arr = array_unique(array_filter(explode(",", $row['item_id'])));
                                            $item_name = "";
                                            foreach ($item_id_arr as $val) 
                                            {
                                                $item_name .= ($item_name=="") ? $garments_item[$val] : ", ".$garments_item[$val];
                                            }
                                            echo $item_name;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Season'])): ?>
                                    <td width="80"><p><?=$season_library[$row['season']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Prod Dept'])): ?>
                                    <td width="80"><p><?=$product_dept[$row['product_dept']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Prod. Dept Code / Class'])): ?>
                                    <td width="80"><p><?=$row['product_code'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Sub Dept'])): ?>
                                    <td width="80"><p><?=$sub_dep_library[$row['pro_sub_dep']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Brand'])): ?>
                                    <td width="80"><p><?=$brand_library[$row['brand_id']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Region'])): ?>
                                    <td width="80"><p><?=$region[$row['region']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Prod. Catg'])): ?>
                                    <td width="80"><p><?=$product_category[$row['product_category']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Country'])): ?>
                                    <td width="80">
                                        <p>
                                            <?
                                            $country_id_arr = array_unique(array_filter(explode(",", $row['country_id'])));
                                            $country_name = "";
                                            foreach ($country_id_arr as $val) 
                                            {
                                                $country_name .= ($country_name=="") ? $country_library[$val] : ", ".$country_library[$val];
                                            }
                                            echo $country_name;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Sustainability Standard'])): ?>
                                    <td width="80"><p><?=$sustainability_standard[$row['sustainability_standard']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Fabric Material'])): ?>
                                    <td width="80"><p><?=$fab_material[$row['fab_material']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Order Nature'])): ?>
                                    <td width="80"><p><?=$fbooking_order_nature[$row['quality_level']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Quality Label'])): ?>
                                    <td width="80"><p><?=$quality_label[$row['qlty_label']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Embilshement Name'])): ?>
                                    <td width="80"><p><?=$emb_name_arr[$job_key];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Service Name'])): ?>
                                    <td width="80"><p><?=$conv_name_arr[$job_key];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Order/PO No'])): ?>
                                    <td width="80">
                                        <p>
                                            <?
                                            $po_number_arr = array_unique(array_filter(explode(",", $row['po_number'])));
                                            $po_name = "";
                                            foreach ($po_number_arr as $val) 
                                            {
                                                $po_name .= ($po_name=="") ? $val : ", ".$val;
                                            }
                                            echo $po_name;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Actual Order/PO No'])): ?>
                                    <td width="80"><p><?=$acc_po_array[$job_key]['acc_po'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['GMT Color'])): ?>
                                    <td width="80">
                                        <p>
                                            <?
                                            $color_arr = array_unique(array_filter(explode(",", $row['color_id'])));
                                            $color_name = "";
                                            foreach ($color_arr as $val) 
                                            {
                                                $color_name .= ($color_name=="") ? $color_library[$val] : ", ".$color_library[$val];
                                            }
                                            echo $color_name;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Size'])): ?>
                                    <td width="80">
                                        <p>
                                            <?
                                            $size_arr = array_unique(array_filter(explode(",", $row['size_id'])));
                                            $size_name = "";
                                            foreach ($size_arr as $val) 
                                            {
                                                $size_name .= ($size_name=="") ? $size_library[$val] : ", ".$size_library[$val];
                                            }
                                            echo $size_name;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Ex Cut %'])): ?>
                                    <td width="80">
                                        <p>
                                            <?
                                            $excess_cut_arr = array_unique(array_filter(explode("**", $po_wise_data[$job_key]['excess_cut'])));
                                            $ex_cut = "";
                                            foreach ($excess_cut_arr as $val) 
                                            {
                                                $ex_cut .= ($ex_cut=="") ? "(".$val.")" : ",(".$val.")";
                                            }
                                            echo $ex_cut;
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Order Qnty [Pcs]'])): ?>
                                    <td width="80" align="right"><p><?=number_format($row['job_qty_pcs'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Break Down Order Qnty [Pcs]'])): ?>
                                    <td width="80" align="right"><p><?=number_format($row['order_quantity'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Order Qnty with Cut% [Pcs]'])): ?>
                                    <td width="80" align="right"><p><?=number_format($row['plan_cut_qnty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Order Qnty [Uom]'])): ?>
                                    <td width="80" align="right"><p><?=number_format($row['po_quantity'],0);?></p></td>
                                <?php endif ?>
                                <?php if(array_key_exists('Uom', $column_name)): ?>
                                    <td width="80" align="center"><p><?=$unit_of_measurement[$row['order_uom']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Per Unit Price'])): ?>
                                    <td width="80" align="right"><p><?=$row['unit_price'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Order Value'])): ?>
                                    <td width="80" align="right"><p><?=number_format($row['order_value'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['PO Insert Date'])): ?>
                                    <td width="80" align="center"><p><?=$row['po_insert_date'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['PO Receive Date'])): ?>
                                    <td width="80" align="center"><p><?=$row['po_received_date'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Factory Receive Date'])): ?>
                                    <td width="80" align="center"><p><?=$row['factory_received_date'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['1st Cut Date'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $lay_date_arr = array_unique(array_filter(explode(",", $lay_data_array[$job_key]['entry_date'])));
                                            $lay_date = get_max_min_date($lay_date_arr);
                                            echo ($lay_date['min_date']!="") ? change_date_format($lay_date['min_date']) : "";
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Last Cut Date'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            echo ($lay_date['max_date']!="") ? change_date_format($lay_date['max_date']) : "";
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['1st Sew Date'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $sew_out_date_arr = array_unique(array_filter(explode(",", $gmts_data_array[$job_key][5]['date'])));
                                            $sew_out_date = get_max_min_date($sew_out_date_arr);
                                            echo ($sew_out_date['min_date']!="") ? change_date_format($sew_out_date['min_date']) : "";
                                            ?>                                        
                                        </p>
                                    </td>   
                                <?php endif ?>
                                <?php if(!empty($column_name['Last Sew Date'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            echo ($sew_out_date['max_date']!="") ? change_date_format($sew_out_date['max_date']) : "";
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['1st Finish Date'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $gmt_fin_date_arr = array_unique(array_filter(explode(",", $gmts_data_array[$job_key][8]['date'])));
                                            $gmt_fin_date = get_max_min_date($gmt_fin_date_arr);
                                            echo ($gmt_fin_date['min_date']!="") ? change_date_format($gmt_fin_date['min_date']) : "";
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Last Finish Date'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            echo ($gmt_fin_date['max_date']!="") ? change_date_format($gmt_fin_date['max_date']) : "";
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Insp. Offer Date'])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Insp. Date'])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Pub. Shipment Date'])): ?>
                                    <td width="80"><p><?=$row['pub_shipment_date'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['First Shipment Date'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $ex_date_array = array_unique(array_filter(explode(",", $ex_data_array[$job_key]['ex_date'])));
                                            $ex_date = get_max_min_date($ex_date_array);
                                            // echo min($ex_date_array);
                                            echo ($ex_date['min_date']!="") ? change_date_format($ex_date['min_date']) : "";
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Org. Shipment Date'])): ?>
                                    <td width="80"><p><?=$row['shipment_date'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['ETD/LDD Date'])): ?>
                                    <td width="80"><p><?=$row['txt_etd_ldd'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Cut-off Date'])): ?>
                                    <td width="80"><p><?=$cutup_date = implode(",", array_unique(array_filter(explode(",", $row['cutup_date']))));?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Country Shipment Date'])): ?>
                                    <td width="80"><p><?=$coun_ship_date = implode(",", array_unique(array_filter(explode(",", $row['country_ship_date']))));?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['RFI Plan Date'])): ?>
                                    <td width="80"><p><?=$row['pub_shipment_date'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Shipment Mode'])): ?>
                                    <td width="80"><p><?=$shipment_mode[$row['ship_mode']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Lead Time'])): ?>
                                    <td width="80"><p><?=$row['lead_time'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Days in Hand'])): ?>
                                    <td width="80"><p><?=$row['days_in_hand'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Commercial File No'])): ?>
                                    <td width="80">
                                        <p>
                                            <?
                                            $com_file = implode(",",array_unique(array_filter(explode(",",$lc_sc_data_array[$job_key]['internal_file_no']))));
                                            echo $com_file;
                                            ?>                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Lien Bank'])): ?>                                
                                    <td width="80">
                                        <p>
                                            <?
                                            $lien_bank = implode(",",array_unique(array_filter(explode(",",$lc_sc_data_array[$job_key]['lien_bank']))));
                                            echo $lien_bank;
                                            ?>                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Ex. LC/SC Amendment NoLast'])): ?>                                
                                    <td width="80">
                                        <p>
                                            <?
                                            $amendment_no = implode(",",array_unique(array_filter(explode(",",$lc_sc_data_array[$job_key]['amendment_no']))));
                                            echo $amendment_no;
                                            ?>                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Pay Term'])): ?>                               
                                    <td width="80">
                                        <p>
                                            <?
                                            $pay_term = implode(",",array_unique(array_filter(explode(",",$lc_sc_data_array[$job_key]['pay_term']))));
                                            echo $pay_term;
                                            ?>                                            
                                        </p>
                                    </td>     
                                <?php endif ?>
                                <?php if(!empty($column_name['Tenor'])): ?>                               
                                    <td width="80">
                                        <p>
                                            <?
                                            $tenor = implode(",",array_unique(array_filter(explode(",",$lc_sc_data_array[$job_key]['tenor']))));
                                            echo $tenor;
                                            ?>                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['First Shipment Date'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            echo ($ex_date['min_date']!="") ? change_date_format($ex_date['min_date']) : "";
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Last Shipment Date'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            echo ($ex_date['max_date']!="") ? change_date_format($ex_date['max_date']) : "";
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Shipment Qnty'])): ?>
                                    <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['ex_qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Shipment Value'])): ?>
                                    <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['ex_value'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Excess Shipment Qnty'])): ?>
                                    <td width="80" align="right"><p><?=number_format($ship_excess_qty,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Excess Shipment Value'])): ?>
                                    <td width="80" align="right"><p><?=number_format($ship_excess_val,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Short Shipment Qnty'])): ?>
                                    <td width="80" align="right"><p><?=number_format($ship_short_qty,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Short Shipment Value'])): ?>
                                    <td width="80" align="right"><p><?=number_format($ship_short_val,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Shipping Status'])): ?>
                                    <td width="80" align="center"><p><?=$row['shipment_status'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['SMV'])): ?>
                                    <td width="80" align="right"><p><?=$row['set_smv'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Total SMV'])): ?>
                                    <td width="80" align="right"><p><?=$row['set_smv'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['CM'])): ?>
                                    <td width="80"><p><?=$budget_data_array[$job_key]['cm_cost'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Team Leader'])): ?>
                                    <td width="80"><p><?=$team_library[$row['team_leader']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Dealing Merchandiser'])): ?>
                                    <td width="80"><p><?=$merchant_library[$row['dealing_marchant']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Factory Merchandiser'])): ?>
                                    <td width="80"><p><?=$merchant_library[$row['factory_marchant']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Insert User'])): ?>
                                    <td width="80"><p><?=$user_library[$row['inserted_by']];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Lab Dip No'])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Buyer Sample Status'])): ?>
                                    <td width="80">
                                        <a href="javascript:void(0);" onclick="show_popup('sample_status', '<?=$job_key; ?>', '920px',1)">
                                            View
                                        </a>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Order Status'])): ?>
                                    <td width="80">
                                        <a href="javascript:void(0);" onclick="show_popup('order_status', '<?=$job_key; ?>', '320px',2)">
                                            View
                                        </a>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Referance Closed'])): ?>
                                    <td width="80">
                                        <a href="javascript:void(0);" onclick="show_popup('closing_status', '<?=$job_key; ?>', '320px',3)">
                                            View
                                        </a>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Emb. Work Order No'])): ?>
                                    <td width="80">
                                        <p>
                                            <?
                                                echo ($work_order_data_array[$job_key][1]) ? "Printing : ".$work_order_data_array[$job_key][1] : "";
                                                echo ($work_order_data_array[$job_key][2]) ? "<br>Embroidery : ".$work_order_data_array[$job_key][2] : "";
                                                echo ($work_order_data_array[$job_key][3]) ? "<br>Wash : ".$work_order_data_array[$job_key][3] : "";
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Service Work Order No'])): ?>
                                    <td width="80">
                                        <p>
                                            <?
                                                echo "AOP : ".$service_booking_data_array[$job_key]['aop'];
                                                echo "<br>Yarn Dyeing : ".$yd_work_order_data_array[$job_key];
                                                echo "<br>Knitting : ".$service_booking_data_array[$job_key]['knitting'];
                                                echo "<br>Fab. Dyeing : ".$service_booking_data_array[$job_key]['dyeing'];
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Budget Date'])): ?>
                                    <td width="80"><p><?=$budget_data_array[$job_key]['costing_date'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Budget Value'])): ?>
                                    <td width="80" align="right"><p><?=number_format($total_budget_value,2);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Open Value'])): ?>
                                    <td width="80" align="right"><p><?=number_format($open_value,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Approved'])): ?>
                                    <td width="80"><p><?=($budget_data_array[$job_key]['approved']==1) ? "Approved" : "Not Approved";?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Amendment No'])): ?>
                                    <td width="80"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['M Fabric Booking Date'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $booking_date_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['booking_date'])));
                                            $booking_date = "";
                                            foreach ($booking_date_arr as $val) 
                                            {
                                                $booking_date .= ($booking_date=="") ? $val : ", ".$val;
                                            }
                                            echo $booking_date;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['M Fabric Booking No'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $booking_no_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['booking_no'])));
                                            $booking_no = "";
                                            foreach ($booking_no_arr as $val) 
                                            {
                                                $booking_no .= ($booking_no=="") ? $val : ", ".$val;
                                            }
                                            echo $booking_no;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Fabric Delivery Date'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $delivery_date_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['delivery_date'])));
                                            $delivery_date = "";
                                            foreach ($delivery_date_arr as $val) 
                                            {
                                                $delivery_date .= ($delivery_date=="") ? $val : ", ".$val;
                                            }
                                            echo $delivery_date;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Amendment No'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $revised_no_arr = array_unique(explode(",", $booking_data_array[$job_key][1][2]['revised_no']));
                                            $revised_no = "";
                                            foreach ($revised_no_arr as $val) 
                                            {
                                                $revised_no .= ($revised_no=="") ? $val : ", ".$val;
                                            }
                                            echo $revised_no;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Amendment Date'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $revised_date_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['revised_date'])));
                                            $revised_date = "";
                                            foreach ($revised_date_arr as $val) 
                                            {
                                                $revised_date .= ($revised_date=="") ? $val : ", ".$val;
                                            }
                                            echo $revised_date;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Approved Status'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $is_approved_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['is_approved'])));
                                            $is_approved = "";
                                            foreach ($is_approved_arr as $val) 
                                            {
                                                $is_approved .= ($is_approved=="") ? $approval_type_arr[$val] : ", ".$approval_type_arr[$val];
                                            }
                                            echo ($is_approved=="") ? " Not Approved" : $is_approved;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Yarn Description'])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Fabric Construction'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $construction_arr = array_unique(array_filter(explode("__", $booking_data_array[$job_key][1][2]['construction'])));
                                            $construction = "";
                                            foreach ($construction_arr as $val) 
                                            {
                                                $construction .= ($construction=="") ? $val : ", ".$val;
                                            }
                                            echo $construction;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Fabric Composition'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $copmposition_arr = array_unique(array_filter(explode("__", $booking_data_array[$job_key][1][2]['copmposition'])));
                                            $copmposition = "";
                                            foreach ($copmposition_arr as $val) 
                                            {
                                                $copmposition .= ($copmposition=="") ? $val : ", ".$val;
                                            }
                                            echo $copmposition;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['GSM'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $gsm_weight_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['gsm_weight'])));
                                            $gsm_weight = "";
                                            foreach ($gsm_weight_arr as $val) 
                                            {
                                                $gsm_weight .= ($gsm_weight=="") ? $val : ", ".$val;
                                            }
                                            echo $gsm_weight;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Dia/Width'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $dia_width_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][2]['dia_width'])));
                                            $dia_width = "";
                                            foreach ($dia_width_arr as $val) 
                                            {
                                                $dia_width .= ($dia_width=="") ? $val : ", ".$val;
                                            }
                                            echo $dia_width;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Finish Quantity'])): ?>
                                    <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][2]['fin_fab_qnty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Process Loss%'])): ?>
                                    <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][2]['process_loss'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Grey Qnty Kg'])): ?>
                                    <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][2]['grey_fab_qnty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Fabric Booking Date'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $booking_date_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1]['booking_date'])));
                                            $booking_date = "";
                                            foreach ($booking_date_arr as $val) 
                                            {
                                                $booking_date .= ($booking_date=="") ? $val : ", ".$val;
                                            }
                                            echo $booking_date;
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Fabric Booking No'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $booking_no_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1]['booking_no'])));
                                            $booking_no = "";
                                            foreach ($booking_no_arr as $val) 
                                            {
                                                $booking_no .= ($booking_no=="") ? $val : ", ".$val;
                                            }
                                            echo $booking_no;
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Fabric Delivery Date'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $delivery_date_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1]['delivery_date'])));
                                            $delivery_date = "";
                                            foreach ($delivery_date_arr as $val) 
                                            {
                                                $delivery_date .= ($delivery_date=="") ? $val : ", ".$val;
                                            }
                                            echo $delivery_date;
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Fabrication'])): ?>                                
                                    <td width="80">
                                        <p>
                                            <?
                                            $construction_arr = array_unique(array_filter(explode("__", $pur_booking_data_array[$job_key][1]['construction'])));
                                            $construction = "";
                                            foreach ($construction_arr as $val) 
                                            {
                                                $construction .= ($construction=="") ? $val : ", ".$val;
                                            }
                                            echo $construction;
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Uom'])): ?>                                                               
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $uom_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1]['uom'])));
                                            $uom = "";
                                            foreach ($uom_arr as $val) 
                                            {
                                                $uom .= ($uom=="") ? $unit_of_measurement[$val] : ", ".$unit_of_measurement[$val];
                                            }
                                            echo $uom;
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Qnty'])): ?>                                                                                               
                                    <td width="80" align="right">
                                        <p>
                                            <?
                                            $fin_fab_qnty_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1]['fin_fab_qnty'])));
                                            $fin_fab_qnty = "";
                                            foreach ($fin_fab_qnty_arr as $val) 
                                            {
                                                $fin_fab_qnty .= ($fin_fab_qnty=="") ? $val : ", ".$val;
                                            }
                                            echo $fin_fab_qnty;
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name['Approved Status'])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $is_approved_arr = array_unique(array_filter(explode(",", $pur_booking_data_array[$job_key][1][2]['is_approved'])));
                                            $is_approved = "";
                                            foreach ($is_approved_arr as $val) 
                                            {
                                                $is_approved .= ($is_approved=="") ? $approval_type_arr[$val] : ", ".$approval_type_arr[$val];
                                            }
                                            echo ($is_approved=="") ? " Not Approved" : $is_approved;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $booking_date_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][1]['booking_date'])));
                                            $booking_date = "";
                                            foreach ($booking_date_arr as $val) 
                                            {
                                                $booking_date .= ($booking_date=="") ? $val : ", ".$val;
                                            }
                                            echo $booking_date;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $booking_no_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][1]['booking_no'])));
                                            $booking_no = "";
                                            foreach ($booking_no_arr as $val) 
                                            {
                                                $booking_no .= ($booking_no=="") ? $val : ", ".$val;
                                            }
                                            echo $booking_no;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $delivery_date_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][1]['delivery_date'])));
                                            $delivery_date = "";
                                            foreach ($delivery_date_arr as $val) 
                                            {
                                                $delivery_date .= ($delivery_date=="") ? $val : ", ".$val;
                                            }
                                            echo $delivery_date;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $construction_arr = array_unique(array_filter(explode("__", $booking_data_array[$job_key][1][1]['construction'])));
                                            $construction = "";
                                            foreach ($construction_arr as $val) 
                                            {
                                                $construction .= ($construction=="") ? $val : ", ".$val;
                                            }
                                            echo $construction;
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $uom_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][1]['uom'])));
                                            $uom = "";
                                            foreach ($uom_arr as $val) 
                                            {
                                                $uom .= ($uom=="") ? $unit_of_measurement[$val] : ", ".$unit_of_measurement[$val];
                                            }
                                            echo $uom;
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>                                
                                    <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][1]['fin_fab_qnty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][1]['process_loss'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($booking_data_array[$job_key][1][1]['grey_fab_qnty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>                                
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $is_approved_arr = array_unique(array_filter(explode(",", $booking_data_array[$job_key][1][1]['is_approved'])));
                                            $is_approved = "";
                                            foreach ($is_approved_arr as $val) 
                                            {
                                                $is_approved .= ($is_approved=="") ? $approval_type_arr[$val] : ", ".$approval_type_arr[$val];
                                            }
                                            echo ($is_approved=="") ? " Not Approved" : $is_approved;
                                            ?>
                                            
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><a href="javascript:void(0);">View</a></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['grey_fab_qnty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($yarn_allocation_array[$job_key][2],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty']-$yarn_allocation_array[$job_key][2]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($textile_data_array[$job_key][3],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty']-$textile_data_array[$job_key][3]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty']-$textile_data_array[$job_key][3]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($yarn_allocation_array[$job_key][1],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty']-$yarn_allocation_array[$job_key][1]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($yarn_rcv_from_dyeing_array[$job_key],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($dyed_yarn_issue_array[$job_key],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>


                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>


                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['grey_fab_qnty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($program_data_array[$job_key],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty']-$program_data_array[$job_key]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($knitting_data_array[$job_key][1],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($knitting_data_array[$job_key][3],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($knitting_data_array[$job_key][1]+$knitting_data_array[$job_key][3]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($knitting_data_array[$job_key][1]-$knitting_data_array[$job_key][3]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($knitting_data_array[$job_key][1]-$knitting_data_array[$job_key][3]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][56],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>


                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['grey_fab_qnty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][58],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty'] - $roll_data_array[$job_key][58]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['grey_fab_qnty'] - $roll_data_array[$job_key][58]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][82][5],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($roll_data_array[$job_key][58]+$transfer_data_array[$job_key][82][5]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][61],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][82][6],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($roll_data_array[$job_key][61]+$transfer_data_array[$job_key][82][6]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format((($roll_data_array[$job_key][58]+$transfer_data_array[$job_key][82][5]) -($roll_data_array[$job_key][61]+$transfer_data_array[$job_key][82][6])) ,0);?></p></td>
                                <?php endif ?>


                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['fin_fab_qnty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($textile_data_array[$job_key][62],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][64],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($fab_dyeing_array[$job_key],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($textile_data_array[$job_key][62] - $fab_dyeing_array[$job_key]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($textile_data_array[$job_key][62] - $fab_dyeing_array[$job_key]),0);?></p></td>
                                <?php endif ?>


                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['fin_fab_qnty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($textile_data_array[$job_key][66],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['fin_fab_qnty'] - $textile_data_array[$job_key][66]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($fab_dyeing_array[$job_key] - $textile_data_array[$job_key][66]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][63],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][65],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($roll_data_array[$job_key][63] - $roll_data_array[$job_key][65]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][67],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>




                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($booking_qty_array[$job_key]['fin_fab_qnty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($roll_data_array[$job_key][68],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['fin_fab_qnty']-$roll_data_array[$job_key][68]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($booking_qty_array[$job_key]['fin_fab_qnty']-$roll_data_array[$job_key][68]),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][134][5],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][134][5],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($textile_data_array[$job_key][71],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][134][6],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($transfer_data_array[$job_key][134][6],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($roll_data_array[$job_key][68] - $textile_data_array[$job_key][71]),0);?></p></td>
                                <?php endif ?>




                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($fin_fab_data_array[$job_key]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($row['job_qty_pcs'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($row['plan_cut_qnty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($lay_data_array[$job_key]['no_of_bndl'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($lay_data_array[$job_key]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][1]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($row['job_qty_pcs']-$gmts_data_array[$job_key][1]['qty']),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($row['job_qty_pcs']-$gmts_data_array[$job_key][1]['qty']),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][4]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($embel_data_array[$job_key][2][1]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($embel_data_array[$job_key][3][1]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="left"><p><?=number_format($company_library[$embel_data_array[$job_key][2][1]['wo_com_id']],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($embel_data_array[$job_key][2][2]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($embel_data_array[$job_key][3][2]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="left"><p><?=$company_library[$embel_data_array[$job_key][2][2]['wo_com_id']];?></p></td>
                                <?php endif ?>





                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($emb_qty_job_name_arr[$job_key][1],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($print_emb_order_data_array[$job_key][204]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>




                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($emb_qty_job_name_arr[$job_key][2],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($print_emb_order_data_array[$job_key][311]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>




                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($row['job_qty_pcs'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][4]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($row['plan_cut_qnty']-$gmts_data_array[$job_key][4]['qty']),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][4]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][5]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($row['plan_cut_qnty']-$gmts_data_array[$job_key][5]['qty']),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($row['plan_cut_qnty']-$gmts_data_array[$job_key][5]['qty']),0);?></p></td>
                                <?php endif ?>



                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($row['job_qty_pcs'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][7]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][11]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($gmts_data_array[$job_key][8]['qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($row['plan_cut_qnty']-$gmts_data_array[$job_key][8]['qty']),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=$buyer_insp_data[$job_key]['date'];?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($buyer_insp_data[$job_key]['qty'],0);?></p></td>
                                <?php endif ?>




                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['cartot_qnty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['ex_qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($invice_data[$job_key]['inv_qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['ex_value'],2);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($invice_data[$job_key]['inv_value'],2);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($row['job_qty_pcs']-$ex_data_array[$job_key]['ex_qty']),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($row['total_price']-$ex_data_array[$job_key]['ex_value']),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($row['job_qty_pcs']-$ex_data_array[$job_key]['ex_qty']),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format(($row['total_price']-$ex_data_array[$job_key]['ex_value']),0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['rtn_qty'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($ex_data_array[$job_key]['ex_rtn_value'],0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $ex_data_array = array_unique(array_filter(explode(",", $ex_data_array[$job_key]['ex_date'])));
                                            $ex_date = get_max_min_date($ex_data_array);
                                            echo ($ex_date['min_date']!="") ? change_date_format($ex_date['min_date']) : "";
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="center">
                                        <p>
                                            <?
                                            $ex_data_array = array_unique(array_filter(explode(",", $ex_data_array[$job_key]['ex_date'])));
                                            $ex_date = get_max_min_date($ex_data_array);
                                            echo ($ex_date['max_date']!="") ? change_date_format($ex_date['max_date']) : "";
                                            ?>                                        
                                        </p>
                                    </td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80" align="right"><p><?=number_format($a,0);?></p></td>
                                <?php endif ?>





                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>





                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                <?php if(!empty($column_name[''])): ?>
                                    <td width="80"><p><?=$a;?></p></td>
                                <?php endif ?>
                                
                            </tr>
                            <?
                            $i++;
                            $gr_job_total += $row['job_qty_pcs'];
                            $gr_po_total += $row['order_quantity'];
                            $gr_color_size_total += $row['po_quantity'];
                            $gr_plan_cut_total += $row['plan_cut_qnty'];
                            $gr_order_value_total += $row['order_value'];
                        }
                        unset($data_array);
                        ?>                        
                    </tbody>
                </table>
            </div>
            <!-- =============================== report footer ================================ -->
            <table cellspacing="0" border="1" class="rpt_table"   rules="all" width="<?=$tbl_width;?>"  align="center">
                <tfoot>                    
                    <tr>
                        <?php if(!empty($column_name['Sl.'])): ?>
                            <th width="30"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order Status'])): ?>
                            <th width="60"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['LC Company'])): ?>
                            <th width="100"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Working Company'])): ?>
                            <th width="100"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Image'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Buyer'])): ?>
                            <th width="100"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Year'])): ?>
                            <th width="50"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Job'])): ?>
                            <th width="90"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Repeat Job No'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Sample Req. No'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>    
                        <?php if(!empty($column_name['Style Ref'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>

                        <?php if(!empty($column_name['Style Desc'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Gmts Item'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Season'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Prod Dept'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Prod. Dept Code / Class'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Sub Dept'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Brand'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Region'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Prod. Catg'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Country'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Sustainability Standard'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Fabric Material'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order Nature'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Quality Label'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Embilshement Name'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Service Name'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order/PO No'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Actual Order/PO No'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['GMT Color'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Size'])): ?>
                            <th width="80"><p></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Ex Cut %'])): ?>
                            <th width="80"><p>Grand Total</p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order Qnty [Pcs]'])): ?>
                            <th width="80"><p><?=number_format($gr_job_total,0);?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Break Down Order Qnty [Pcs]'])): ?>
                             <th width="80"><p><?=number_format($gr_po_total,0);?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order Qnty with Cut% [Pcs]'])): ?>
                           <th width="80"><p><?=number_format($gr_plan_cut_total,0);?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order Qnty [Uom]'])): ?>
                            <th width="80"><p><?=number_format($gr_color_size_total,0);?></p></th>
                        <?php endif ?>
                        <?php if(array_key_exists('Uom', $column_name)): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Per Unit Price'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order Value'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['PO Insert Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                       
                        <?php if(!empty($column_name['PO Receive Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Factory Receive Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                       <?php if(!empty($column_name['1st Cut Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Last Cut Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['1st Sew Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Last Sew Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                       <?php if(!empty($column_name['1st Finish Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Last Finish Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Insp. Offer Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Insp. Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Pub. Shipment Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['First Shipment Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Org. Shipment Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['ETD/LDD Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Cut-off Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Country Shipment Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['RFI Plan Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Shipment Mode'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Lead Time'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Days in Hand'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Commercial File No'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Lien Bank'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                       <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Ex. LC/SC Amendment NoLast'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Pay Term'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Tenor'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['First Shipment Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Last Shipment Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Shipment Qnty'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Shipment Value'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Excess Shipment Qnty'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Excess Shipment Value'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Short Shipment Qnty'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Short Shipment Value'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Shipping Status'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['SMV'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Total SMV'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                       <?php if(!empty($column_name['CM'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Team Leader'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Dealing Merchandiser'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Factory Merchandiser'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Insert User'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Lab Dip No'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Buyer Sample Status'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Order Status'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Referance Closed'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Emb. Work Order No'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Service Work Order No'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Budget Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name['Budget Value'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Open Value'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Approved'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Amendment No'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['M Fabric Booking Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name['M Fabric Booking No'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Fabric Delivery Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Amendment No'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Amendment Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Approved Status'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Yarn Description'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Fabric Construction'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Fabric Composition'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['GSM'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Dia/Width'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Finish Quantity'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Process Loss%'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Grey Qnty Kg'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Fabric Booking Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>





                        <?php if(!empty($column_name['Fabric Booking No'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Fabric Delivery Date'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Fabrication'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Uom'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Qnty'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name['Approved Status'])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>


                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                           <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>




                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>




                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>




                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>




                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>




                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>




                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>




                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>



                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>




                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>




                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>




                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        <?php if(!empty($column_name[''])): ?>
                            <th width="80"><p><?=$a;?></p></th>
                        <?php endif ?>
                        
                    </tr>
                </tfoot>
            </table>
        </div>        
    </fieldset>
    <script type="text/javascript">
        // $("#zoom").elevateZoom({easing : true});
    </script>
    <? 

    foreach (glob("$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    //$filename=$user_id."_".$name.".xls";
    echo "$total_data####$filename";  

}

?>	
