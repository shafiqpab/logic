<?
header('Content-type:text/html; charset=utf-8');
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];

if($action === 'load_drop_down_location')
{
    echo create_drop_down('cbo_location', 110, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name", 'id,location_name', 1, '-- Select --', $selected, "load_drop_down( 'requires/linein_out_qc_display_report_controller', document.getElementById('cbo_location').value, 'load_drop_down_floor', 'floor_td' ); get_php_form_data( document.getElementById('cbo_location').value, 'eval_multi_select', 'requires/linein_out_qc_display_report_controller' );", 0);
    exit();
}

if($action === 'load_drop_down_floor') 
{ 
    echo create_drop_down('cbo_floor', 110, "select id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name", 'id,floor_name', 1, '-- Select --', $selected, '', 0 );
    exit();      
}

if($action === 'eval_multi_select') 
{
    echo "set_multiselect('cbo_floor','0','0','','0');\n";
    exit();
}

if($action === 'load_drop_down_buyer') 
{
    echo create_drop_down('cbo_buyer_name', 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, '-- Select --', $selected, '' );        
    exit();
}

if($action === 'load_drop_down_line') 
{
    $explode_data = explode('_',$data);
    $prod_reso_allo = return_field_value('auto_update', 'variable_settings_production', "company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
    $txt_date = $explode_data[3];
    
    $cond = '';
    if($prod_reso_allo==1)
    {
        $line_library = return_library_array("select id, line_name from lib_sewing_line where company_name=$explode_data[2] and status_active=1 and is_deleted=0", 'id', 'line_name');
        $line_array = array();
        
        if($txt_date == '')
        {
            if($explode_data[0] == 0 && $explode_data[1] != 0) $cond = " and location_id = $explode_data[1]";
            if($explode_data[0] != 0) $cond = " and floor_id = $explode_data[0]";
            $line_data = sql_select("select id, line_number from prod_resource_mst where company_id=$explode_data[2] and is_deleted=0 $cond");
        }
        else
        {
            if($explode_data[0] == 0 && $explode_data[1] != 0) $cond = " and a.location_id = $explode_data[1]";
            if($explode_data[0] != 0) $cond = " and a.floor_id = $explode_data[0]";
            if($db_type == 0) $data_format = "and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."'";
            if($db_type == 2) $data_format = "and b.pr_date='".change_date_format($txt_date,'','',1)."'";
    
            $line_data = sql_select("SELECT a.ID, a.LINE_NUMBER from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and company_id=$explode_data[2] and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }
        
        foreach($line_data as $row)
        {
            $line = '';
            $line_number = explode(',', $row['LINE_NUMBER']);
            foreach($line_number as $val)
            {
                if($line == '') $line = $line_library[$val]; 
                else $line .= ','.$line_library[$val];
            }
            $line_array[$row['ID']] = $line;
        }

        echo create_drop_down('cbo_line', 110, $line_array, '', 1, '-- Select --', $selected, '', 0, 0);
    }
    else
    {
        if($explode_data[0] == 0 && $explode_data[1] != 0) $cond = " and location_name = $explode_data[1]";
        if($explode_data[0] != 0) $cond = " and floor_name = $explode_data[0]";

        echo create_drop_down('cbo_line', 110, "select id, line_name from lib_sewing_line where is_deleted=0 and status_active=1 and company_name=$explode_data[2] and floor_name !=0 $cond order by line_name", 'id,line_name', 1, '-- Select --', $selected, '', 0, 0);
    }
    exit();
}

if($action === 'report_generate') 
{    
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    $cbo_company_name= str_replace("'", '', trim($cbo_company_name));
    $cbo_location    = str_replace("'", '', trim($cbo_location));
    $cbo_floor       = str_replace("'", '', trim($cbo_floor));
    $cbo_line        = str_replace("'", '', trim($cbo_line));
    $cbo_buyer_name  = str_replace("'", '', trim($cbo_buyer_name));
    $txt_job_no      = str_replace("'", '', trim($txt_job_no));
    $txt_order_no    = str_replace("'", '', trim($txt_order_no));
    $txt_style_no    = str_replace("'", '', trim($txt_style_no));    
    $condition = '';

    if($cbo_location != 0) $condition .= " and a.location=$cbo_location";
    if($cbo_floor != '') $condition .= " and a.floor_id in($cbo_floor)";
    if($cbo_line != 0) $condition .= " and a.sewing_line=$cbo_line";
    if($cbo_buyer_name != 0) $condition .= " and b.buyer_name=$cbo_buyer_name";
    if($txt_job_no != '') $condition .= " and b.JOB_NO_PREFIX_NUM=$txt_job_no";
    if($txt_order_no != '') $condition .= " and c.po_number like '%$txt_order_no'";
    if($txt_style_no != '') $condition .= " and b.style_ref_no like '%$txt_style_no'";

    $company_library = return_library_array('select id, company_name from lib_company', 'id', 'company_name');
    $company_short_library = return_library_array('select id, company_short_name from lib_company', 'id', 'company_short_name');
    $buyer_short_library = return_library_array('select id, short_name from lib_buyer where status_active=1 and is_deleted=0', 'id', 'short_name');
    $location_library = return_library_array('select id, location_name from lib_location where status_active=1 and is_deleted=0', 'id', 'location_name');
    $floor_library = return_library_array("select id, floor_name from lib_prod_floor where company_id=$cbo_company_name and status_active=1 and is_deleted=0", 'id', 'floor_name');
    $line_library = return_library_array("select id, line_name from lib_sewing_line where company_name=$cbo_company_name and status_active=1 and is_deleted=0", 'id', 'line_name');
    $lineArr = return_library_array("select id, line_name from lib_sewing_line where company_name=$cbo_company_name and status_active=1 and is_deleted=0 order by id", 'id', 'line_name');
    $prod_reso_line_arr = return_library_array("select id, line_number from prod_resource_mst where company_id=$cbo_company_name and is_deleted=0", 'id', 'line_number');

    /*if($db_type==2) {
        $prod_reso_arr=return_library_array("SELECT a.ID, b.LINE_NAME from prod_resource_mst a, lib_sewing_line b where REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id and a.company_id=$cbo_company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.floor_name, b.sewing_line_serial", 'id', 'line_name' );
    } else if($db_type==0) {
        $prod_reso_arr=return_library_array( "SELECT a.ID, b.LINE_NAME from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id and a.company_id=$cbo_company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.floor_name, b.sewing_line_serial", 'id', 'line_name');
    }*/

    $txt_date = str_replace("'", '', trim($txt_date));
    if ($db_type==0)
    {
        $txt_date = date('Y-m-d', strtotime(str_replace("'", '',  $txt_date)));
        $txt_date_cond = " and a.production_date='$txt_date'";
    }
    else
    {
        $txt_date = date('d-M-Y', strtotime(str_replace("'", '',  $txt_date)));
        $txt_date_cond = " and a.production_date='$txt_date'";
    }

    $prod_resource_array=array();
    
    $sql_dataArray="SELECT a.ID, a.LINE_NUMBER, a.FLOOR_ID, b.PR_DATE, b.TARGET_PER_HOUR, b.WORKING_HOUR, b.MAN_POWER, b.STYLE_REF_ID, b.ACTIVE_MACHINE, b.OPERATOR,  b.HELPER, c.TARGET_EFFICIENCY from prod_resource_mst a,  prod_resource_dtls b, prod_resource_dtls_mast c where a.id=b.mst_id and b.mast_dtl_id=c.id and a.company_id=$cbo_company_name and b.pr_date='$txt_date'";
    $dataArray=sql_select($sql_dataArray); 
  
    foreach($dataArray as $row)
    {
        $prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['TARGET_PER_HOUR'] = $row['TARGET_PER_HOUR'];
        $prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['WORKING_HOUR'] = $row['WORKING_HOUR'];
        $prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['ACTIVE_MACHINE'] = $row['ACTIVE_MACHINE'];
        $prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['TARGET_EFFICIENCY'] = $row['TARGET_EFFICIENCY'];
        $prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['OPERATOR'] = $row['OPERATOR'];
        $prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['HELPER'] = $row['HELPER'];
        $prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['MAN_POWER'] = $row['MAN_POWER'];
        $prod_resource_array[$row['ID']][change_date_format($row['PR_DATE'])]['TPD'] = $row['TARGET_PER_HOUR']*$row['WORKING_HOUR'];
    }
    unset($dataArray);

    /*$start_time_arr = array();
    if($db_type == 0) {
        $start_time_data_arr=sql_select("SELECT COMPANY_NAME, SHIFT_ID, TIME_FORMAT( prod_start_time, '%H:%i' ) as PROD_START_TIME, TIME_FORMAT( lunch_start_time, '%H:%i' ) as LUNCH_START_TIME from variable_settings_production where company_name=$cbo_company_name and shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
    } else {
        $start_time_data_arr = sql_select("SELECT COMPANY_NAME, SHIFT_ID, TO_CHAR(prod_start_time,'HH24:MI') as PROD_START_TIME, TO_CHAR(lunch_start_time,'HH24:MI') as LUNCH_START_TIME from variable_settings_production where company_name=$cbo_company_name and shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");   
    }

    foreach($start_time_data_arr as $row) {
        $start_time_arr[$row['SHIFT_ID']]['PST'] = $row['PROD_START_TIME'];
        $start_time_arr[$row['SHIFT_ID']]['LST'] = $row['LUNCH_START_TIME'];
    }
    
    $prod_start_hour = $start_time_arr[1]['PST'];
    if($prod_start_hour=='')*/


    $prod_start_hour = '09:00';

    $start_time = explode(':', $prod_start_hour);
    $hour = substr($start_time[0],1,1);
    $minutes = $start_time[1];
    $last_hour = 23;
    //echo $hour;
    $start_hour_arr = array();    
    $start_hour = $prod_start_hour;
    $start_hour_arr[$hour] = $start_hour;
    for($j=$hour; $j<$last_hour; $j++)
    {       
        $start_hour = add_time($start_hour,60);
        $start_hour_arr[$j+1] = substr($start_hour,0,5);
    }
    $start_hour_arr[$j+1] = '23:59';
    //echo '<pre>';print_r($start_hour_arr);die;
    ob_start();        
    ?>

    <div style="width:100%">
        <div style="width:100%; font-weight:bold;"> <? echo $floor_library[$cbo_floor]; ?>
            <h3>Company Name : <? echo $company_library[$cbo_company_name]; ?>
                <br>Sewing Production Status (<? echo date('l, F d', strtotime($txt_date)); ?>)
            </h3>
        </div>

        <?
        $i=1;
            
        if($db_type == 0)
        {

            $sql = "SELECT a.COMPANY_ID, a.LOCATION, a.FLOOR_ID, a.PROD_RESO_ALLO, a.PRODUCTION_DATE, a.SEWING_LINE, b.JOB_NO_PREFIX_NUM, b.JOB_NO, b.STYLE_REF_NO, b.BUYER_NAME, a.ITEM_NUMBER_ID, b.GMTS_ITEM_ID, b.set_break_down as SMV_PCS_SET, group_concat(distinct(a.po_break_down_id)) as PO_BREAK_DOWN_ID, group_concat(distinct(c.po_number)) as PO_NUMBER,
                sum(a.production_quantity) as GOOD_QNTY,
                sum(a.alter_qnty) as ALTER_QNTY,
                sum(a.spot_qnty) as SPOT_QNTY,
                sum(a.reject_qnty) as REJECT_QNTY,";
            $first=1;
            
            for($h=$hour; $h<$last_hour; $h++)
            {
                $bg_hour  = $start_hour_arr[$h];
                $end_hour = substr(add_time($start_hour_arr[$h],60),0,5);
                $production_hour= 'PRODUCTION_HOUR'.substr($bg_hour,0,2);
                $prod_hour      = 'PROD_HOUR'.substr($bg_hour,0,2);
                $alter_hour     = 'ALTER_HOUR'.substr($bg_hour,0,2);
                $spot_hour      = 'SPOT_HOUR'.substr($bg_hour,0,2);
                $reject_hour    = 'REJECT_HOUR'.substr($bg_hour,0,2);
                $prod_in_hour   = 'PROD_IN_HOUR'.substr($bg_hour,0,2);
                $prod_out_hour  = 'PROD_OUT_HOUR'.substr($bg_hour,0,2);
                if($first==1)
                {
                    $sql .= " max(CASE WHEN a.production_hour<'$end_hour' and a.production_type=13 and a.entry_form=349 THEN a.production_hour END) AS $production_hour,
                        sum(CASE WHEN a.production_hour<'$end_hour' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
                        sum(CASE WHEN a.production_hour<'$end_hour' and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
                        sum(CASE WHEN a.production_hour<'$end_hour' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
                        sum(CASE WHEN a.production_hour<'$end_hour' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,
                        sum(CASE WHEN a.insert_date<'$end_hour' and a.production_type=12 and a.entry_form=348 THEN production_quantity else 0 END) AS $prod_in_hour,
                        sum(CASE WHEN a.production_hour<'$end_hour' and a.production_type=13 and a.entry_form=349 THEN production_quantity else 0 END) AS $prod_out_hour,";
                }
                else
                {
                    $sql.=" max(CASE WHEN a.production_hour>='$bg_hour' and a.production_hour<'$end_hour' and a.production_type=13 and a.entry_form=349 THEN a.production_hour END) AS $production_hour,
                        sum(CASE WHEN a.production_hour>='$bg_hour' and  a.production_hour<'$end_hour' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
                        sum(CASE WHEN a.production_hour>='$bg_hour' and a.production_hour<'$end_hour' and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
                        sum(CASE WHEN a.production_hour>='$bg_hour' and a.production_hour<'$end_hour' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
                        sum(CASE WHEN a.production_hour>='$bg_hour' and a.production_hour<'$end_hour' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,
                        sum(CASE WHEN a.insert_date>='$bg_hour' and  a.insert_date<'$end_hour' and a.production_type=12 and a.entry_form=348 THEN production_quantity else 0 END) AS $prod_in_hour,
                        sum(CASE WHEN a.production_hour>='$bg_hour' and  a.production_hour<'$end_hour' and a.production_type=13 and a.entry_form=349 THEN production_quantity else 0 END) AS $prod_out_hour,";
                }
                $first=$first+1;
            }
            $production_hour= 'PRODUCTION_HOUR'.$last_hour;
            $prod_hour      = 'PROD_HOUR'.$last_hour;
            $alter_hour     = 'ALTER_HOUR'.$last_hour;
            $spot_hour      = 'SPOT_HOUR'.$last_hour;
            $reject_hour    = 'REJECT_HOUR'.$last_hour;
            $prod_in_hour   = 'PROD_IN_HOUR'.$last_hour;
            $prod_out_hour  = 'PROD_OUT_HOUR'.$last_hour;
            $sql .= " max(CASE WHEN a.production_hour>='$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=13 and a.entry_form=349 THEN a.production_hour END) AS $production_hour,
                sum(CASE WHEN  a.production_hour>='$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
                sum(CASE WHEN  a.production_hour>='$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
                sum(CASE WHEN  a.production_hour>='$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
                sum(CASE WHEN  a.production_hour>='$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,
                sum(CASE WHEN a.insert_date>='$start_hour_arr[$last_hour]' and a.insert_date<='$start_hour_arr[24]' and a.production_type=12 and a.entry_form=348 THEN production_quantity else 0 END) AS $prod_in_hour,
                sum(CASE WHEN a.production_hour>='$start_hour_arr[$last_hour]' and a.production_hour<='$start_hour_arr[24]' and a.production_type=13 and a.entry_form=349 THEN production_quantity else 0 END) AS $prod_out_hour";
        
            $sql .= " from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
                where a.company_id=$cbo_company_name $condition $txt_date_cond and a.production_type in(5,12,13) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
                group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no, b.job_no_prefix_num, b.style_ref_no, b.buyer_name, a.item_number_id, b.gmts_item_id, b.set_break_down, a.location, a.floor_id, a.production_date 
                order by a.floor_id, a.sewing_line";
            //echo $sql;die;//$txt_date
        }   
        else
        {

            $sql = "SELECT a.COMPANY_ID, a.LOCATION, a.FLOOR_ID, a.PROD_RESO_ALLO, a.PRODUCTION_DATE, a.SEWING_LINE, b.JOB_NO_PREFIX_NUM, b.JOB_NO, b.STYLE_REF_NO, b.BUYER_NAME, a.ITEM_NUMBER_ID, b.GMTS_ITEM_ID, b.set_break_down as SMV_PCS_SET, listagg(a.po_break_down_id,',') within group (order by po_break_down_id) as PO_BREAK_DOWN_ID, listagg(c.po_number,',') within group (order by po_number) as PO_NUMBER,
                sum(a.production_quantity) as GOOD_QNTY,
                sum(a.alter_qnty) as ALTER_QNTY,
                sum(a.spot_qnty) as SPOT_QNTY, 
                sum(a.reject_qnty) as REJECT_QNTY,";
            $first=1;
            for($h=$hour; $h<$last_hour; $h++)
            {
                $bg_hour = $start_hour_arr[$h];
                $end_hour = substr(add_time($start_hour_arr[$h],60),0,5);
                //echo $end.'system';
                $production_hour= 'PRODUCTION_HOUR'.substr($bg_hour,0,2);
                $prod_hour      = 'PROD_HOUR'.substr($bg_hour,0,2);
                $alter_hour     = 'ALTER_HOUR'.substr($bg_hour,0,2);
                $spot_hour      = 'SPOT_HOUR'.substr($bg_hour,0,2);
                $reject_hour    = 'REJECT_HOUR'.substr($bg_hour,0,2);
                $prod_in_hour   = 'PROD_IN_HOUR'.substr($bg_hour,0,2);
                $prod_out_hour  = 'PROD_OUT_HOUR'.substr($bg_hour,0,2);  
                if($first==1)
                {
                    $sql .= " max(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end_hour' and a.production_type=13 and a.entry_form=349 THEN TO_CHAR(a.production_hour,'HH24:MI') END) AS $production_hour,
                        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end_hour' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
                        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end_hour' and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
                        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end_hour' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
                        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end_hour' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,
                        sum(CASE WHEN TO_CHAR(a.insert_date,'HH24:MI')<'$end_hour' and a.production_type=12 and a.entry_form=348 THEN production_quantity else 0 END) AS $prod_in_hour,
                        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')<'$end_hour' and a.production_type=13 and a.entry_form=349 THEN production_quantity else 0 END) AS $prod_out_hour,";
                }
                else
                {
                    $sql .= " max(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg_hour' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end_hour' and a.production_type=13 and a.entry_form=349 THEN TO_CHAR(a.production_hour,'HH24:MI') END) AS $production_hour,
                        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg_hour' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end_hour' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
                        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg_hour' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end_hour' and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
                        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg_hour' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end_hour' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
                        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg_hour' and  TO_CHAR(a.production_hour,'HH24:MI')<'$end_hour' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,
                        sum(CASE WHEN TO_CHAR(a.insert_date,'HH24:MI')>='$bg_hour' and TO_CHAR(a.insert_date,'HH24:MI')<'$end_hour' and a.production_type=12 and a.entry_form=348 THEN production_quantity else 0 END) AS $prod_in_hour,
                        sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg_hour' and TO_CHAR(a.production_hour,'HH24:MI')<'$end_hour' and a.production_type=13 and a.entry_form=349 THEN production_quantity else 0 END) AS $prod_out_hour,";
                }
                $first=$first+1;
            }
            $production_hour= 'PRODUCTION_HOUR'.$last_hour;
            $prod_hour      = 'PROD_HOUR'.$last_hour;
            $alter_hour     = 'ALTER_HOUR'.$last_hour;
            $spot_hour      = 'SPOT_HOUR'.$last_hour;
            $reject_hour    = 'REJECT_HOUR'.$last_hour;
            $prod_in_hour   = 'PROD_IN_HOUR'.$last_hour;
            $prod_out_hour  = 'PROD_OUT_HOUR'.$last_hour;
            $sql .= " max(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=13 and entry_form=349 THEN TO_CHAR(a.production_hour,'HH24:MI') END) AS $production_hour,
                sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_quantity else 0 END) AS $prod_hour,
                sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN alter_qnty else 0 END) AS $alter_hour,
                sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN reject_qnty else 0 END) AS $reject_hour,
                sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN spot_qnty else 0 END) AS $spot_hour,
                sum(CASE WHEN  TO_CHAR(a.insert_date,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.insert_date,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=12 and a.entry_form=348 THEN production_quantity else 0 END) AS $prod_in_hour,
                sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=13 and a.entry_form=349 THEN production_quantity else 0 END) AS $prod_out_hour";
                                                                
            $sql .= " from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c
                where a.company_id=$cbo_company_name $condition $txt_date_cond and a.production_type in(5,12,13) and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
                group by a.company_id, a.prod_reso_allo, a.sewing_line, b.job_no, b.job_no_prefix_num, b.style_ref_no, b.buyer_name, a.item_number_id, b.gmts_item_id, b.set_break_down, a.location, a.floor_id, a.production_date 
                order by a.floor_id, a.sewing_line";  
        }
        //echo $sql; //die; //and a.entry_form=349

        $result = sql_select($sql);
        $production_data = array();
        $poId = '';
        foreach($result as $row)
        {
            $order_number = implode(',',array_flip(array_flip(explode(',',$row['PO_BREAK_DOWN_ID']))));
            //echo $order_number;
            //echo $row['PRODUCTION_HOUR09'];
            $poId .= $order_number.',';

            if($row['PROD_RESO_ALLO']==1)
            {
                $line_resource_mst_arr=explode(',',$prod_reso_line_arr[$row['SEWING_LINE']]);
                $line_name = '';

                foreach($line_resource_mst_arr as $resource_id)
                {
                    $line_name .= $lineArr[$resource_id].', ';
                }

                $line_name = chop($line_name,' , ');

                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['LOCATION']=$row['LOCATION'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['FLOOR_ID']=$row['FLOOR_ID'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PROD_RESO_ALLO']=$row['PROD_RESO_ALLO'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PRODUCTION_DATE']=$row['PRODUCTION_DATE'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['SEWING_LINE']=$row['SEWING_LINE'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['JOB_NO_PREFIX_NUM']=$row['JOB_NO_PREFIX_NUM'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['JOB_NO']=$row['JOB_NO'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ITEM_NUMBER_ID'].=$row['ITEM_NUMBER_ID'].',';
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['SMV_PCS_SET']=$row['SMV_PCS_SET'].',';
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PO_BREAK_DOWN_ID']=$row['PO_BREAK_DOWN_ID'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PO_NUMBER']=$row['PO_NUMBER'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['GOOD_QNTY']+=$row['GOOD_QNTY'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ALTER_QNTY']+=$row['ALTER_QNTY'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['SPOT_QNTY']+=$row['SPOT_QNTY'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['REJECT_QNTY']+=$row['REJECT_QNTY'];

                for($h=$hour; $h<=$last_hour; $h++)
                {
                    $bg_hour = $start_hour_arr[$h];
                    $production_hour= 'PRODUCTION_HOUR'.substr($bg_hour,0,2);
                    $prod_hour      = 'PROD_HOUR'.substr($bg_hour,0,2);
                    $alter_hour     = 'ALTER_HOUR'.substr($bg_hour,0,2);
                    $spot_hour      = 'SPOT_HOUR'.substr($bg_hour,0,2);
                    $reject_hour    = 'REJECT_HOUR'.substr($bg_hour,0,2);
                    $prod_in_hour   = 'PROD_IN_HOUR'.substr($bg_hour,0,2);
                    $prod_out_hour  = 'PROD_OUT_HOUR'.substr($bg_hour,0,2);
                    //$insert_date2 = "'".$row[$insert_date]."'";
                    $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$production_hour"]=$row["$production_hour"];
                    $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$prod_hour"]+=$row["$prod_hour"];
                    $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$alter_hour"]+=$row["$alter_hour"];
                    $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$spot_hour"]+=$row["$spot_hour"];
                    $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$reject_hour"]+=$row["$reject_hour"];
                    $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$prod_in_hour"]+=$row["$prod_in_hour"];
                    $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$prod_out_hour"]+=$row["$prod_out_hour"];
                }
            }
            else
            {
                $line_name=$lineArr[$row['SEWING_LINE']];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['LOCATION']=$row['LOCATION'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['FLOOR_ID']=$row['FLOOR_ID'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PROD_RESO_ALLO']=$row['PROD_RESO_ALLO'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PRODUCTION_DATE']=$row['PRODUCTION_DATE'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['SEWING_LINE']=$row['SEWING_LINE'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['JOB_NO_PREFIX_NUM']=$row['JOB_NO_PREFIX_NUM'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['JOB_NO']=$row['JOB_NO'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ITEM_NUMBER_ID'].=$row['ITEM_NUMBER_ID'].',';
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['SMV_PCS_SET']=$row['SMV_PCS_SET'].',';
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PO_BREAK_DOWN_ID']=$row['PO_BREAK_DOWN_ID'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PO_NUMBER']=$row['PO_NUMBER'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['GOOD_QNTY']+=$row['GOOD_QNTY'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ALTER_QNTY']+=$row['ALTER_QNTY'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['SPOT_QNTY']+=$row['SPOT_QNTY'];
                $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['REJECT_QNTY']+=$row['REJECT_QNTY'];

                for($h=$hour; $h<=$last_hour; $h++)
                {
                    $bg_hour = $start_hour_arr[$h];
                    $production_hour= 'PRODUCTION_HOUR'.substr($bg_hour,0,2);
                    $prod_hour      = 'PROD_HOUR'.substr($bg_hour,0,2);
                    $alter_hour     = 'ALTER_HOUR'.substr($bg_hour,0,2);
                    $spot_hour      = 'SPOT_HOUR'.substr($bg_hour,0,2);
                    $reject_hour    = 'REJECT_HOUR'.substr($bg_hour,0,2);
                    $prod_in_hour   = 'PROD_IN_HOUR'.substr($bg_hour,0,2);
                    $prod_out_hour  = 'PROD_OUT_HOUR'.substr($bg_hour,0,2);
                    $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$production_hour"]+=$row["$production_hour"];
                    $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$prod_hour"]+=$row["$prod_hour"];
                    $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$alter_hour"]+=$row["$alter_hour"];
                    $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$spot_hour"]+=$row["$spot_hour"];
                    $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$reject_hour"]+=$row["$reject_hour"];
                    $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$prod_in_hour"]+=$row["$prod_in_hour"];
                    $production_data[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]["$prod_out_hour"]+=$row["$prod_out_hour"];
                    
                }
            }   
        }
        ksort($production_data);
        //echo '<pre>';print_r($production_data);
        //echo $poId;
        $poIds=chop($poId,',');
        ?>
        <div style="text-align: center; color: red; font-size: 18px;">
            <? 
                if ($poIds == '') {
                    echo "Production are not started !!";
                    die;
                }
            ?>
        </div>
        <?
        // ===========FOR SEWING DATA(TODAY,TOTAL)==================
        if($db_type==0)
        {

            $prod_qnty_data = "SELECT a.FLOOR_ID, a.LOCATION, a.PROD_RESO_ALLO, a.SEWING_LINE, a.PO_BREAK_DOWN_ID, a.ITEM_NUMBER_ID, b.JOB_NO, 
            sum(case when a.production_type=12 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as TOTAL_SEWING_INPUT, 
            sum(case when a.production_type=13 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as TOTAL_SEWING_OUTPUT, 
            sum(case when a.production_type=12 and a.production_date='$txt_date' then a.production_quantity else 0 END) as TODAY_SEWING_INPUT
            FROM pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c 
            WHERE a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.company_id=$cbo_company_name and a.po_break_down_id in($poIds) and a.entry_form in(348,349) and a.production_type in(12,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
            GROUP BY a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id, a.item_number_id, b.job_no";            
        } 
        else
        {
            
            $prod_qnty_data = "SELECT a.FLOOR_ID, a.LOCATION, a.PROD_RESO_ALLO, a.SEWING_LINE, a.PO_BREAK_DOWN_ID, a.ITEM_NUMBER_ID, b.JOB_NO, 
            sum(case when a.production_type=12 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as TOTAL_SEWING_INPUT, 
            sum(case when a.production_type=13 and a.production_date<='$txt_date' then a.production_quantity else 0 END) as TOTAL_SEWING_OUTPUT, 
            sum(case when a.production_type=12 and a.production_date='$txt_date' then a.production_quantity else 0 END) as TODAY_SEWING_INPUT
            FROM pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c 
            WHERE a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.company_id=$cbo_company_name and a.po_break_down_id in($poIds) and a.entry_form in(348,349) and a.production_type in(12,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
            GROUP BY a.floor_id, a.location, a.prod_reso_allo, a.sewing_line, a.po_break_down_id, a.item_number_id, b.job_no";
        }
        //echo $prod_qnty_data;
        $prod_qnty_data_arr = array();
        $prod_qnty_data_res = sql_select($prod_qnty_data);
        foreach($prod_qnty_data_res as $row)
        {   
            if($row['PROD_RESO_ALLO']==1)
            {           
                $line_resource_mst_arr = explode(',',$prod_reso_line_arr[$row['SEWING_LINE']]);
                $line_name = '';
                foreach($line_resource_mst_arr as $resource_id)
                {
                    $line_name .= $lineArr[$resource_id].', ';
                }

                $line_name = chop($line_name,' , ');
                $prod_qnty_data_arr[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['TOTAL_SEWING_INPUT'] = $row['TOTAL_SEWING_INPUT'];
                $prod_qnty_data_arr[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['TOTAL_SEWING_OUTPUT'] = $row['TOTAL_SEWING_OUTPUT'];
                $prod_qnty_data_arr[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['TODAY_SEWING_INPUT'] = $row['TODAY_SEWING_INPUT'];
                $prod_qnty_data_arr[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['TODAY_SEWING_OUTPUT'] = $row['TODAY_SEWING_OUTPUT'];
            }
            else
            {
                $line_name = $lineArr[$row['SEWING_LINE']];
                $prod_qnty_data_arr[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['TOTAL_SEWING_INPUT'] = $row['TOTAL_SEWING_INPUT'];
                $prod_qnty_data_arr[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['TOTAL_SEWING_OUTPUT'] = $row['TOTAL_SEWING_OUTPUT'];
                $prod_qnty_data_arr[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['TODAY_SEWING_INPUT'] = $row['TODAY_SEWING_INPUT'];
                $prod_qnty_data_arr[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['TODAY_SEWING_OUTPUT'] = $row['TODAY_SEWING_OUTPUT'];
            }
        }
        
        $fr_data_arr=array();
        //$txt_date = date("Y-m-d", strtotime(str_replace("'", "",  $txt_date)));
        $fr_sql="select ID, FRDATE, LINE, STYLE, DESCRIPTION, PRODUCT_TYPE, ORDER_NO, COLOR, PLAN_QTY from fr_import where frdate='$txt_date'";
        //echo $fr_sql; die;
        $fr_sql_res = sql_select($fr_sql);
        foreach($fr_sql_res as $row)
        {
            $ex_job=explode('::',$row['STYLE']);
            $fr_data_arr[$row['LINE']][$ex_job[0]][$row['ORDER_NO']]['ISFR']=$row['COLOR'];
        }
        unset($fr_sql_res);    
     
        ?> 
        <style type="text/css">
            hr{
                border: 0;
                background-color: #000;
                height: 1px;
            }
        </style>                 

        <div width="100%">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                
                <?   
                //table header calculation
                $totalGoodQnty=$totalAlterQnty=$totalSpotQnty=$totalRejectQnty=0;
                $totalProdInQnty=$totalProdOutQnty=0;              
                
                $prod_08=$prod_09=$prod_10=$prod_11=$prod_12=$prod_13=$prod_14=$prod_15=0;
                $prod_16=$prod_17=$prod_18=$prod_19=$prod_20=$prod_21=$prod_22=$prod_23=0;
                $prod_in_08=$prod_in_09=$prod_in_10=$prod_in_11=$prod_in_12=$prod_in_13=$prod_in_14=$prod_in_15=0;
                $prod_in_16=$prod_in_17=$prod_in_18=$prod_in_19=$prod_in_20=$prod_in_21=$prod_in_22=$prod_in_23=0;
                $prod_out_08=$prod_out_09=$prod_out_10=$prod_out_11=$prod_out_12=$prod_out_13=$prod_out_14=$prod_out_15=0;
                $prod_out_16=$prod_out_17=$prod_out_18=$prod_out_19=$prod_out_20=$prod_out_21=$prod_out_22=$prod_out_23=0;
                
                foreach($production_data as $flowre_id=>$value)
                {
                    ksort($value);
                    foreach($value as $line_name=>$gmts_val)
                    {
                        foreach($gmts_val as $job_id=>$val)
                        {
                            foreach($val as $gmts_id => $row)
                            {
                                $total_hterget += $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['TARGET_PER_HOUR'];  
                                //h terget calculation
                                $today_output += $prod_qnty_data_arr[$flowre_id][$line_name][$job_id][$gmts_id]['TODAY_SEWING_OUTPUT'];

                                for($k=$hour; $k<=$last_hour; $k++)
                                {
                                    $prod_hour    = 'PROD_HOUR'.substr($start_hour_arr[$k],0,2);
                                    $alter_hour   = 'ALTER_HOUR'.substr($start_hour_arr[$k],0,2);
                                    $spot_hour    = 'SPOT_HOUR'.substr($start_hour_arr[$k],0,2);
                                    $reject_hour  = 'REJECT_HOUR'.substr($start_hour_arr[$k],0,2);
                                    $prod_in_hour = 'PROD_IN_HOUR'.substr($start_hour_arr[$k],0,2);
                                    $prod_out_hour= 'PROD_OUT_HOUR'.substr($start_hour_arr[$k],0,2);
                                    $totalGoodQnty   += $row[$prod_hour];
                                    $totalAlterQnty  += $row[$alter_hour];
                                    $totalSpotQnty   += $row[$spot_hour];
                                    $totalRejectQnty += $row[$reject_hour];
                                    $totalProdInQnty += $row[$prod_in_hour];
                                    $totalProdOutQnty+= $row[$prod_out_hour];                                 
                                }
                                //echo $totalGoodQnt.'system';

                                $prod_08 += $row['PROD_HOUR08'];$prod_09 += $row['PROD_HOUR09'];
                                $prod_10 += $row['PROD_HOUR10'];$prod_11 += $row['PROD_HOUR11'];
                                $prod_12 += $row['PROD_HOUR12'];$prod_13 += $row['PROD_HOUR13'];
                                $prod_14 += $row['PROD_HOUR14'];$prod_15 += $row['PROD_HOUR15'];
                                $prod_16 += $row['PROD_HOUR16'];$prod_17 += $row['PROD_HOUR17'];
                                $prod_18 += $row['PROD_HOUR18'];$prod_19 += $row['PROD_HOUR19'];
                                $prod_20 += $row['PROD_HOUR20'];$prod_21 += $row['PROD_HOUR21'];
                                $prod_22 += $row['PROD_HOUR22'];$prod_23 += $row['PROD_HOUR23'];

                                $prod_in_08 += $row['PROD_IN_HOUR08'];$prod_in_09 += $row['PROD_IN_HOUR09'];
                                $prod_in_10 += $row['PROD_IN_HOUR10'];$prod_in_11 += $row['PROD_IN_HOUR11'];
                                $prod_in_12 += $row['PROD_IN_HOUR12'];$prod_in_13 += $row['PROD_IN_HOUR13'];
                                $prod_in_14 += $row['PROD_IN_HOUR14'];$prod_in_15 += $row['PROD_IN_HOUR15'];
                                $prod_in_16 += $row['PROD_IN_HOUR16'];$prod_in_17 += $row['PROD_IN_HOUR17'];
                                $prod_in_18 += $row['PROD_IN_HOUR18'];$prod_in_19 += $row['PROD_IN_HOUR19'];
                                $prod_in_20 += $row['PROD_IN_HOUR20'];$prod_in_21 += $row['PROD_IN_HOUR21'];
                                $prod_in_22 += $row['PROD_IN_HOUR22'];$prod_in_23 += $row['PROD_IN_HOUR23'];

                                $prod_out_08 += $row['PROD_OUT_HOUR08'];$prod_out_09 += $row['PROD_OUT_HOUR09'];
                                $prod_out_10 += $row['PROD_OUT_HOUR10'];$prod_out_11 += $row['PROD_OUT_HOUR11'];
                                $prod_out_12 += $row['PROD_OUT_HOUR12'];$prod_out_13 += $row['PROD_OUT_HOUR13'];
                                $prod_out_14 += $row['PROD_OUT_HOUR14'];$prod_out_15 += $row['PROD_OUT_HOUR15'];
                                $prod_out_16 += $row['PROD_OUT_HOUR16'];$prod_out_17 += $row['PROD_OUT_HOUR17'];
                                $prod_out_18 += $row['PROD_OUT_HOUR18'];$prod_out_19 += $row['PROD_OUT_HOUR19'];
                                $prod_out_20 += $row['PROD_OUT_HOUR20'];$prod_out_21 += $row['PROD_OUT_HOUR21'];
                                $prod_out_22 += $row['PROD_OUT_HOUR22'];$prod_out_23 += $row['PROD_OUT_HOUR23'];
                            }
                        }
                    }
                } 
                ?>
                <thead>                         
                    <tr>
                        <th rowspan="2" width="3%" style="vertical-align:middle; word-break:break-all" align="center">Line</th>
                        <th rowspan="2" width="10%" style="vertical-align:middle; word-break:break-all" align="center">Order Description</th>
                        <th width="4%" style="vertical-align:middle; word-break:break-all" align="center">WIP</th>
                        <th width="4%" style="vertical-align:middle; word-break:break-all" align="center">H.Target</th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all" align="center">Optr</th>
                        <th rowspan="2" width="3%" style="vertical-align:middle; word-break:break-all" align="center">SMV</th>
                        <th rowspan="2" width="5%" style="vertical-align:middle; word-break:break-all" align="center">Status</th>
                        <? 
                        for($k=$hour; $k<=$last_hour; $k++)
                        {
                            $cur_hour=substr($start_hour_arr[$k],0,2);
                            $cur_prod = 'prod_'.$cur_hour;
                            $cur_prod_in = 'prod_in_'.$cur_hour;
                            $cur_prod_out = 'prod_out_'.$cur_hour;
                            if ($cur_hour < '18')
                            {   
                                ?>
                                <th width="3%" style="vertical-align:middle; word-break:break-all; <? if (date('H') == $cur_hour) { ?> background-color: #F00; background-image: none; color: #FFF;<? } ?>;"><? echo substr($start_hour_arr[$k],0,5); ?></th>
                                <?
                            }
                            else
                            {
                                if ($$cur_prod != 0 || $$cur_prod_in != 0 || $$cur_prod_out != 0)
                                {
                                    ?>
                                    <th width="3%" style="vertical-align:middle; word-break:break-all; <? if (date('H') == $cur_hour) { ?> background-color: #F00; background-image: none; color: #FFF;<? } ?>"><? echo substr($start_hour_arr[$k],0,5); ?></th>
                                    <?
                                }
                            }   
                        }
                        ?>                                          

                        <th width="4%" style="vertical-align:middle; word-break:break-all" align="center">Total Prod</th>
                        <th width="4%" style="vertical-align:middle; word-break:break-all" align="center">Total QC</th>
                        <th width="4%" style="vertical-align:middle; word-break:break-all" align="center">Reject</th>
                        <th rowspan="2" width="4%" style="vertical-align:middle; word-break:break-all" align="center">Day Target</th>
                        <th rowspan="2" width="4%" style="vertical-align:middle; word-break:break-all" align="center">Current Achv %</th>
                    </tr>
                    <tr>
                        <th width="4%" style="vertical-align:middle; word-break:break-all" align="center">Input</th>
                        <th width="4%" style="vertical-align:middle; word-break:break-all" align="center">Eff%</th>
                        <th width="3%" style="vertical-align:middle; word-break:break-all" align="center">Hlpr</th>        
                        
                        <?
                        $percent_cal_arr=array();
                        for($k=$hour; $k<=$last_hour; $k++)
                        {
                            $cur_hour=substr($start_hour_arr[$k],0,2);
                            $cur_prod = 'prod_'.$cur_hour;
                            $cur_prod_in = 'prod_in_'.$cur_hour;
                            $cur_prod_out = 'prod_out_'.$cur_hour;
                            $cur_percent_cal = 'percent_cal_'.$cur_hour;
                            if ($cur_hour < '18')
                            {                       
                                ?>
                                <th width="3%" style="vertical-align:middle; word-break:break-all;" title="Total Line Output and (Total Line Output*100/H Target)%">
                                <?
                                    $$cur_percent_cal = $$cur_prod_out*100/$total_hterget;
                                    array_push($percent_cal_arr, $$cur_percent_cal);                         
                                    if ($$cur_prod_out != 0){
                                        echo $$cur_prod_out.'/'.(number_format($$cur_percent_cal)).'%';
                                    } else {
                                       echo 0;
                                    }
                                ?>
                                </th>
                                <?
                            }
                            else 
                            {
                                //$cur_prod = 'prod_'.$cur_hour;
                                //$width = 18;
                                if ($$cur_prod != 0 || $$cur_prod_in != 0 || $$cur_prod_out != 0)
                                {                           
                                    ?>
                                    <th width="3%" style="vertical-align:middle; word-break:break-all;" title="Total Line Output and (Total Line Output*100/H Target)%">
                                        <?
                                        $$cur_percent_cal = $$cur_prod_out*100/$total_hterget;
                                        array_push($percent_cal_arr, $$cur_percent_cal);                         
                                        if ($$cur_prod_out != 0){
                                            echo $$cur_prod_out.'/'.(number_format($$cur_percent_cal)).'%';
                                        } else {
                                           echo 0;
                                        }
                                        ?>
                                    </th>
                                    <?
                                }   
                            }       
                        }                                          
                        ?>                        
                       
                        <th width="4%" style="vertical-align:middle; word-break:break-all;" title="Grand Total Prod Qnty and (Grand Total Prod Qnty/(Total Percent/Count Prod Hour))">
                            <?
                                $count = 0;
                                $percent_sum = 0;
                                foreach ($percent_cal_arr as $value) {
                                    if ($value != 0) {
                                        $count++;
                                        $percent_sum = $percent_sum + $value;
                                    }
                                } 

                                $percent_avg = $percent_sum/$count;
                                if ($totalProdOutQnty != '') {
                                    echo $totalProdOutQnty.'/'.number_format($percent_avg).'%';
                                } else {
                                    echo 0;
                                }                                                      
                            ?>
                        </th>
                        <th width="4%" style="vertical-align:middle; word-break:break-all;"><?= $totalGoodQnty; ?></th>
                        <th width="4%" style="vertical-align:middle; word-break:break-all;">Alter/Spot</th>
                    </tr>
                </thead> 

                <tbody> 
                <?
                
                foreach($production_data as $flowre_id=>$value)
                {
                    ksort($value);
                    foreach($value as $line_name=>$gmts_val)
                    {
                        foreach($gmts_val as $job_id=>$val)
                        {
                            foreach($val as $gmts_id => $row)
                            {
                                $totalGoodQnty=$totalAlterQnty=$totalSpotQnty=$totalRejectQnty=0;
                                $totalProdInQnty=$totalProdOutQnty=0;
                                if ($i%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';   
                                $today_input = $prod_qnty_data_arr[$flowre_id][$line_name][$job_id][$gmts_id]['TODAY_SEWING_INPUT'];
                                $today_output = $prod_qnty_data_arr[$flowre_id][$line_name][$job_id][$gmts_id]['TODAY_SEWING_OUTPUT'];
                                $total_input = $prod_qnty_data_arr[$flowre_id][$line_name][$job_id][$gmts_id]['TOTAL_SEWING_INPUT'];
                                $total_output = $prod_qnty_data_arr[$flowre_id][$line_name][$job_id][$gmts_id]['TOTAL_SEWING_OUTPUT'];                                

                                $order_number=implode(',',array_unique(explode(',',$row[('PO_NUMBER')])));
                                //$grouping=implode(',',array_unique(explode(',',$row[('GROUPING')])));
                                //$file_no=implode(',',array_unique(explode(',',$row[('FILE_NO')])));
                                $operator = $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['OPERATOR'];
                                $helper = $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['HELPER'];
                                $target_per_hour = $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['TARGET_PER_HOUR'];
                                $target_efficiency = $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['TARGET_EFFICIENCY'];
                                $day_target = $prod_resource_array[$row['SEWING_LINE']][change_date_format($row['PRODUCTION_DATE'])]['TPD'];
                                
                                $is_fr=$fr_data_arr[$line_name][$row['JOB_NO']][$order_number]['ISFR'];
                                $frline_tdcolor='';
                                
                                if($is_fr=='') {
                                    $frline_tdcolor='#F00';
                                    $frline_fontcolor='#FFF';
                                }
                                ?>
                                <tr bgcolor="<?= $bgcolor; ?>" onclick="change_color('tr_1nd<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_1nd<?= $i; ?>">
                                    <td width="3%" bgcolor="<? echo $frline_tdcolor; ?>" style="vertical-align:middle; word-break:break-all" align="center"><p style="color: <? echo $frline_fontcolor; ?>; font-weight: bold;"><?= $line_name; ?></p></td>
                                    <td width="10%" style="vertical-align:middle; word-break:break-all" align="center" title='Bname=<?= $buyer_short_library[$row['BUYER_NAME']]; ?> Job=<?= $row['JOB_NO_PREFIX_NUM']; ?> Style=<?= $row['STYLE_REF_NO']; ?> Order=<?= $order_number; ?> Item=<?= $garments_item[$gmts_id]; ?>'><p><? echo $buyer_short_library[$row['BUYER_NAME']].', '.$row['JOB_NO_PREFIX_NUM'].', '.$row['STYLE_REF_NO'].', '.$order_number.', '.$garments_item[$gmts_id]; ?></p></td>


                                    <td width="4%" style="vertical-align:middle; word-break:break-all;" align="center" title="<? echo 'Total Input='.$total_input.' and Total Output='.$total_output; ?>">
                                        <p>
                                        <?
                                            $wip = ($total_input - $total_output);
                                            if ($wip==0 && $today_input==0) {
                                                echo '';
                                            } else {
                                                echo $wip.'<br>'.$today_input;
                                            }
                                        ?>
                                        </p>
                                    </td>

                                    <td width="4%" style="vertical-align:middle; word-break:break-all" align="center"><?= $target_per_hour.'<br/>'.$target_efficiency; ?></td>
                                    
                                    <td width="3%" style="vertical-align:middle; word-break:break-all" align="center"><p>
                                        <?                                        
                                        if ($operator == '' && $helper == '') {
                                            echo '';
                                        } elseif ($operator == '' && $helper != '') {
                                            echo '0'.'<br>'.$helper;
                                        } elseif ($operator != '' && $helper == '') {
                                            echo $operator.'<br>'.'0';
                                        } else {    
                                            echo $operator.'<br>'.$helper;
                                        }                                           
                                        ?></p>
                                    </td>

                                    <td width="3%" style="vertical-align:middle; word-break:break-all" align="center"><p>
                                        <? 
                                            $smv_pcs_string=chop($row['SMV_PCS_SET'],',');
                                            $smv_string_arr=explode('__',$smv_pcs_string);
                                            foreach($smv_string_arr as $gmtsId)
                                            {                   
                                                $smv_arr=explode('_',$gmtsId);
                                                if($smv_arr[0] == $gmts_id){
                                                    echo $total_smv = number_format($smv_arr[2],2);
                                                }
                                            }  
                                        ?></p>
                                    </td>
                                   <!--  <td width="6%"><p>Alter<hr>Spot<hr>Reject</p></td> --> 
                                    <td width="5%">Line Input<hr>Line Output<hr>Awaiting QC<hr>QC Pass<hr>Alter<hr>Spot<hr>Reject</td>
                      
                                    <?
                                    for($k=$hour; $k<=$last_hour; $k++)
                                    {
                                        //$production_hour= 'PRODUCTION_HOUR'.substr($start_hour_arr[$k],0,2);
                                        $prod_hour      = 'PROD_HOUR'.substr($start_hour_arr[$k],0,2);
                                        $alter_hour     = 'ALTER_HOUR'.substr($start_hour_arr[$k],0,2);
                                        $spot_hour      = 'SPOT_HOUR'.substr($start_hour_arr[$k],0,2);
                                        $reject_hour    = 'REJECT_HOUR'.substr($start_hour_arr[$k],0,2);
                                        $prod_in_hour   = 'PROD_IN_HOUR'.substr($start_hour_arr[$k],0,2);
                                        $prod_out_hour  = 'PROD_OUT_HOUR'.substr($start_hour_arr[$k],0,2);
                                        $production_hour= $row[$production_hour];
                                        $qc_pass= $row[$prod_hour];
                                        $alter  = $row[$alter_hour];
                                        $spot   = $row[$spot_hour];
                                        $reject = $row[$reject_hour];
                                        $sewing_input_qnty  = $row[$prod_in_hour];
                                        $sewing_output_qnty = $row[$prod_out_hour];
                                        $awaiting_qc = $sewing_output_qnty - $qc_pass;
                                        $totalGoodQnty   += $row[$prod_hour];
                                        $totalAlterQnty  += $row[$alter_hour];
                                        $totalSpotQnty   += $row[$spot_hour];
                                        $totalRejectQnty += $row[$reject_hour];
                                        $totalProdInQnty += $row[$prod_in_hour];
                                        $totalProdOutQnty+= $row[$prod_out_hour];
                                        //echo $production_hour.'system';

                                        if ($qc_pass == 0) $qc_pass = '&nbsp';
                                        if ($alter == 0) $alter = '&nbsp';
                                        if ($spot == 0) $spot = '&nbsp';
                                        if ($reject == 0) $reject = '&nbsp';
                                        if ($sewing_input_qnty == 0) $sewing_input_qnty = '&nbsp';
                                        if ($sewing_output_qnty == 0) $sewing_output_qnty = '&nbsp';
                                        if ($awaiting_qc == 0) $awaiting_qc = '&nbsp';

                                        $cur_hour=substr($start_hour_arr[$k],0,2);
                                        $cur_prod = 'prod_'.$cur_hour;
                                        $cur_prod = 'prod_'.$cur_hour;
                                        $cur_prod_in = 'prod_in_'.$cur_hour;
                                        $cur_prod_out = 'prod_out_'.$cur_hour;
                                        //if ($$cur_prod != 0 || $$cur_prod_in != 0 || $$cur_prod_out != 0)
                                        $cur_percent_cal = 'percent_cal_'.$cur_hour;

                                        if ($cur_hour < '18')
                                        {
                                            ?>
                                            <td width="3%" style="vertical-align:middle;word-break:break-all" align="center">
                                                <?= $sewing_input_qnty; ?><hr><?= $sewing_output_qnty; ?><hr><?= $awaiting_qc; ?><hr><?= $qc_pass; ?><hr><?= $alter; ?><hr><?= $spot; ?><hr><?= $reject; ?>
                                            </td>
                                            <?
                                        }
                                        else
                                        {
                                            //$cur_prod = 'prod_'.$cur_hour;
                                            if ($$cur_prod != 0 || $$cur_prod_in != 0 || $$cur_prod_out != 0)
                                            {                                       
                                                ?>
                                                <td width="3%" style="vertical-align:middle; word-break:break-all" align="center">
                                                    <?= $sewing_input_qnty; ?><hr><?= $sewing_output_qnty; ?><hr><?= $awaiting_qc; ?><hr><?= $qc_pass; ?><hr><?= $alter; ?><hr><?= $spot; ?><hr><?= $reject; ?>
                                                </td>
                                                <?
                                            }   
                                        }
                                    }
                                   
                                    ?>
                                    <td width="4%" style="vertical-align:middle; word-break:break-all" align="center" title="Total Line Output"><?= $totalProdOutQnty; ?></td>
                                    <td width="4%" style="vertical-align:middle; word-break:break-all" align="center" title="Total QC Pass"><?= $totalGoodQnty; ?></td>
                                    
                                    <td width="4%" style="vertical-align:middle; word-break:break-all" align="center">
                                        <? 
                                            if ($totalRejectQnty == 0 && $totalAlterQnty == 0 && $totalSpotQnty == 0)
                                                echo '';
                                            else
                                                echo $totalRejectQnty.'<br/>'.$totalAlterQnty.'/'.$totalSpotQnty;  
                                        ?>
                                    </td>


                                    <td width="4%" style="vertical-align:middle; word-break:break-all" align="center">
                                        <?= $day_target; ?></td>
                                    <td width="4%" style="vertical-align:middle; word-break:break-all" align="center" title="(Total Line Output x 100)/(Running Hour with Minutes from nine(9) x H.Target)">
                                        <? 
                                        for($k=$hour; $k<=$last_hour; $k++)
                                        {
                                            $cur_hour = substr($start_hour_arr[$k],0,2);
                                            $production_hour = 'PRODUCTION_HOUR'.substr($start_hour_arr[$k],0,2);
                                            $production_hour = $row[$production_hour];

                                            $ex_production_hour = explode(':',$production_hour);
                                            if (date('H') == $cur_hour)
                                            {
                                                if ($ex_production_hour[0] >= 9)
                                                {
                                                    $running_hour_with_min = ($ex_production_hour[0]-9)*60+$ex_production_hour[1];
                                                    //echo $ex_production_hour[0];
                                                    $running_htarget = $running_hour_with_min*$target_per_hour;
                                                    //echo $totalProdOutQnty;
                                                    $current_achivement = $totalProdOutQnty*100/$running_htarget;
                                                    echo number_format($current_achivement,2).'%';
                                                }
                                            }                                            
                                        }
                                        
                                        ?>
                                    </td>                                     
                                </tr>                                                              
                                                                
                                <?
                                $i++;                              
                            }
                        }   
                    }
                }
                ?>
                </tbody>
            </table>
        </div>

    </div><!-- end main div -->

    <?
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
}
?>