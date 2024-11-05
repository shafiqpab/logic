<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

$search_by_arr=array(1=>"Date Wise Report",2=>"Wait For Heat Setting",5=>"Wait For Singeing",3=>"Wait For Dyeing",4=>"Wait For Re-Dyeing");

if($action=="load_drop_down_buyer")
{
    echo load_html_head_contents("Buyer Info", "../../../../", 1, 1,'','','');
    extract($_REQUEST);
    $data=explode('_',$data);
    $company=$data[0];
    if($company>0)
    {
        echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,2,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
    }
    else
    {
        echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","");
    }
    exit();
}

if($action=="generated_report")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $company = str_replace("'","",$cbo_company_name);
    $buyer = str_replace("'","",$cbo_buyer_name);
    //$batch_type = 0; 
    $cbo_based_on = str_replace("'","",$cbo_based_on);
    $year = str_replace("'","",$cbo_year);
    $txt_date_from = str_replace("'","",$txt_date_from);
    $txt_date_to = str_replace("'","",$txt_date_to);
    $fso_number_show = str_replace("'","",$fso_number_show);
    $fso_number = str_replace("'","",$fso_number);

    if ($buyer==0) $buyer_cond=""; else $buyer_cond="  and a.po_buyer='$buyer'";
    if ($fso_number==0) $all_fso_no_cond=""; else $all_fso_no_cond="  and a.id in($fso_number)";

    if($db_type==0) $year_field_by="and YEAR(b.insert_date)"; else if($db_type==2) $year_field_by=" and to_char(b.insert_date,'YYYY')";
    if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";

    if($txt_date_from && $txt_date_to && $cbo_based_on>0)
    {
        if($db_type==0)
        {
            $date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
            $date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
            if ($cbo_based_on==1) 
            {
                $dates_com="and b.issue_date between '$date_from' and '$date_to'";
            }
            if($cbo_based_on==2)
            {
                $dates_com2=" and b.insert_date between '".$date_from."' and '".$date_to." 23:59:59' ";
            }
            
        }
        if($db_type==2)
        {
            $date_from=change_date_format($txt_date_from,'','',1);
            $date_to=change_date_format($txt_date_to,'','',1);
           if ($cbo_based_on==1) 
            {
                $dates_com="and b.issue_date between '$date_from' and '$date_to'";
            }
            if($cbo_based_on==2)
            {
                $dates_com=" and b.insert_date between '".$date_from."' and '".$date_to." 11:59:59 PM' ";
            }
        }
    }
    // echo $dates_com.'='.$cbo_based_on;die;
    $user_name_arr = return_library_array( "select id, user_name from user_passwd",'id','user_name');
    $season_name_arr = return_library_array( "select id,season_name from lib_buyer_season",'id','season_name');
    $supplier_arr = return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name",'id','supplier_name');

    $con = connect();
    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_name and entry_form in (1234,1235)");
    oci_commit($con);

    $issue_sql="SELECT b.issue_date, d.id as dtls_id, b.issue_number, b.company_id, b.knit_dye_source, b.knit_dye_company, b.issue_purpose, a.id as fso_id, a.po_buyer, a.buyer_id, a.within_group, a.po_job_no, a.style_ref_no, a.season_id, a.job_no as fso_no, a.sales_booking_no, b.remarks, b.inserted_by, b.insert_date , b.updated_by, b.update_date, b.id as issue_id , e.barcode_no, f.detarmination_id, f.item_description, f.dia_width, f.gsm, e.qnty as issue_qty
    from fabric_sales_order_mst a, inv_issue_master b, inv_transaction c, inv_grey_fabric_issue_dtls d, pro_roll_details e, product_details_master f
    where b.id=e.mst_id and b.id=c.mst_id and c.id=d.trans_id and b.id=d.mst_id and d.id=e.dtls_id and c.prod_id=f.id and e.po_breakdown_id=a.id and e.is_sales=1 and b.is_deleted=0 
    and b.entry_form in(61) and e.entry_form in(61) and e.is_returned=0 
    and a.company_id=$company $dates_com $all_fso_no_cond $buyer_cond $year_cond 
    and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.status_active=1 and e.is_deleted=0";// and b.issue_number='TIL-KGIR-21-00005'
    // echo $issue_sql;die;
    $roll_issue_data=sql_select($issue_sql);
    $issue_roll_arr = array();
    foreach($roll_issue_data as $val)
    {
        $issue_roll_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
    }
    fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 1234, 1,$issue_roll_arr, $empty_arr);
    oci_commit($con);
    /*$issue_roll = implode(",", $issue_roll_arr);
    $issue_roll_cond="";
    if($issue_roll)
    {
        $issue_roll = implode(",",array_filter(array_unique(explode(",", $issue_roll))));
        $issue_roll_arr = explode(",", $issue_roll);
        if($db_type==0)
        {
            $issue_roll_cond = " and a.id in ($issue_roll )";
        }
        else
        {
            if(count($issue_roll_arr)>999)
            {
                $issue_roll_chunk_arr=array_chunk($issue_roll_arr, 999);
                $issue_roll_cond=" and (";
                foreach ($issue_roll_chunk_arr as $value)
                {
                    $issue_roll_cond .=" c.barcode_no in (".implode(",", $value).") or ";
                }
                $issue_roll_cond=chop($issue_roll_cond,"or ");
                $issue_roll_cond.=")";
            }
            else
            {
                $issue_roll_cond = " and c.barcode_no in ($issue_roll )";
            }
        }
    }*/
    // echo  $issue_roll_cond;die;

    $production_sql="SELECT b.body_part_id, c.barcode_no from pro_grey_prod_entry_dtls b, pro_roll_details c, GBL_TEMP_ENGINE d where b.id=c.dtls_id  and c.barcode_no=d.ref_val and d.user_id=$user_name and d.entry_form=1234 and d.ref_from=1 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
    $production_data=sql_select($production_sql);
    $barcode_wise_body_part = array();
    foreach($production_data as $row)
    {
        $barcode_wise_body_part[$row[csf("barcode_no")]] = $row[csf("body_part_id")];
    }

    $roll_data_arr = array();
    foreach($roll_issue_data as $row)
    {
        $body_part_no=$barcode_wise_body_part[$row[csf("barcode_no")]];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['issue_date']=$row[csf("issue_date")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['issue_number']=$row[csf("issue_number")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['company_id']=$row[csf("company_id")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['knit_dye_source']=$row[csf("knit_dye_source")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['knit_dye_company']=$row[csf("knit_dye_company")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['issue_purpose']=$row[csf("issue_purpose")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['po_buyer']=$row[csf("po_buyer")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['buyer_id']=$row[csf("buyer_id")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['within_group']=$row[csf("within_group")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['po_job_no']=$row[csf("po_job_no")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['style_ref_no']=$row[csf("style_ref_no")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['season_id']=$row[csf("season_id")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['fso_no']=$row[csf("fso_no")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['sales_booking_no']=$row[csf("sales_booking_no")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['remarks']=$row[csf("remarks")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['inserted_by']=$row[csf("inserted_by")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['insert_date']=$row[csf("insert_date")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['updated_by']=$row[csf("updated_by")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['update_date']=$row[csf("update_date")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['item_description']=$row[csf("item_description")];
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['roll_count']++;

        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['issue_qty']+=$row[csf("issue_qty")];        
        $roll_data_arr[$row[csf("issue_id")]][$row[csf("fso_id")]][$body_part_no][$row[csf("detarmination_id")]][$row[csf("dia_width")]][$row[csf("gsm")]]['dtls_id'].=$row[csf("dtls_id")].'*';        

        $sales_ord_wise_fso_arr[$row[csf("fso_id")]]=$row[csf("fso_id")];
    }
    // echo "<pre>";print_r($roll_data_arr);die;
    fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 1235, 2,$sales_ord_wise_fso_arr, $empty_arr);
    oci_commit($con);

    /*$fso_nos = implode(",", $sales_ord_wise_fso_arr);
    $fso_no_cond="";
    if($fso_nos)
    {
        $fso_nos = implode(",",array_filter(array_unique(explode(",", $fso_nos))));
        $fso_nos_arr = explode(",", $fso_nos);
        if($db_type==0)
        {
            $fso_no_cond = " and a.id in ($fso_nos )";
        }
        else
        {
            if(count($fso_nos_arr)>999)
            {
                $fso_nos_chunk_arr=array_chunk($fso_nos_arr, 999);
                $fso_no_cond=" and (";
                foreach ($fso_nos_chunk_arr as $value)
                {
                    $fso_no_cond .="a.id in (".implode(",", $value).") or ";
                }
                $fso_no_cond=chop($fso_no_cond,"or ");
                $fso_no_cond.=")";
            }
            else
            {
                $fso_no_cond = " and a.id in ($fso_nos )";
            }
        }
    }*/

    $recv_batch_sql="SELECT a.challan_no, a.receive_date, a.recv_number, c.qnty as batch_qty, c.barcode_no, a.inserted_by, a.insert_date, a.updated_by, a.update_date, b.order_id as fso_id, b.body_part_id, b.febric_description_id as detar_id, b.width, b.gsm
    from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c, GBL_TEMP_ENGINE d 
    where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id  and c.barcode_no=d.ref_val and d.user_id=$user_name and d.entry_form=1234 and d.ref_from=1
    and a.entry_form=62 and c.entry_form=62 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0";
    //echo  $recv_batch_sql;
    $recv_batch_data=sql_select($recv_batch_sql);
    $recv_batch_arr=array();
    $recv_challan_no_arr=array();
    foreach ($recv_batch_data as $rows)
    {
        $recv_challan_no_arr[$rows[csf("challan_no")]]['challan_no']=$rows[csf("challan_no")];

        $recv_batch_arr[$rows[csf("challan_no")]][$rows[csf("fso_id")]][$rows[csf("body_part_id")]][$rows[csf("detar_id")]][$rows[csf("width")]][$rows[csf("gsm")]]['receive_date']=$rows[csf("receive_date")];
        $recv_batch_arr[$rows[csf("challan_no")]][$rows[csf("fso_id")]][$rows[csf("body_part_id")]][$rows[csf("detar_id")]][$rows[csf("width")]][$rows[csf("gsm")]]['recv_number']=$rows[csf("recv_number")];
        $recv_batch_arr[$rows[csf("challan_no")]][$rows[csf("fso_id")]][$rows[csf("body_part_id")]][$rows[csf("detar_id")]][$rows[csf("width")]][$rows[csf("gsm")]]['batch_qty']+=$rows[csf("batch_qty")];
        $recv_batch_arr[$rows[csf("challan_no")]][$rows[csf("fso_id")]][$rows[csf("body_part_id")]][$rows[csf("detar_id")]][$rows[csf("width")]][$rows[csf("gsm")]]['inserted_by']=$rows[csf("inserted_by")];
        $recv_batch_arr[$rows[csf("challan_no")]][$rows[csf("fso_id")]][$rows[csf("body_part_id")]][$rows[csf("detar_id")]][$rows[csf("width")]][$rows[csf("gsm")]]['insert_date']=$rows[csf("insert_date")];
        $recv_batch_arr[$rows[csf("challan_no")]][$rows[csf("fso_id")]][$rows[csf("body_part_id")]][$rows[csf("detar_id")]][$rows[csf("width")]][$rows[csf("gsm")]]['updated_by']=$rows[csf("updated_by")];
        $recv_batch_arr[$rows[csf("challan_no")]][$rows[csf("fso_id")]][$rows[csf("body_part_id")]][$rows[csf("detar_id")]][$rows[csf("width")]][$rows[csf("gsm")]]['update_date']=$rows[csf("update_date")];

        $recv_batch_arr[$rows[csf("challan_no")]][$rows[csf("fso_id")]][$rows[csf("body_part_id")]][$rows[csf("detar_id")]][$rows[csf("width")]][$rows[csf("gsm")]]['batch_roll_count']++;
    }
    // echo "<pre>";print_r($recv_batch_arr);die;

    $job_fso_chk=array();$job_from_fso_arr=array();
    $job_from_fso =  sql_select("SELECT c.booking_no,c.booking_type,c.is_short, b.job_no_prefix_num,b.job_no, a.job_no as fso_no, d.short_booking_type from GBL_TEMP_ENGINE e, fabric_sales_order_mst a, wo_booking_dtls c,wo_po_details_master b, wo_booking_mst d where a.sales_booking_no=c.booking_no and c.job_no=b.job_no and a.company_id=$company  and e.ref_val=a.id and e.user_id=$user_name and e.entry_form=1235 and e.ref_from=2  and a.within_group=1 and a.booking_id = d.id and c.booking_no = d.booking_no
    union all
    select b.booking_no,4 as booking_type,0 as is_short,0 as job_no_prefix_num,null as job_no, a.job_no as fso_no, null as short_booking_type from  GBL_TEMP_ENGINE e, fabric_sales_order_mst a,wo_non_ord_samp_booking_mst b where a.within_group=1 and a.sales_booking_no=b.booking_no and  a.company_id=$company  and e.ref_val=a.id and e.user_id=$user_name and e.entry_form=1235 and e.ref_from=2 $fso_no_cond");//$fso_no_cond
    foreach ($job_from_fso as $val)
    {
        if($job_fso_chk[$val[csf("fso_no")]][$val[csf("job_no")]] == "")
        {
            $job_fso_chk[$val[csf("fso_no")]][$val[csf("job_no")]] = $val[csf("job_no")];
            $job_from_fso_arr[$val[csf("fso_no")]]["job_no"] .= $val[csf("job_no_prefix_num")].",";

            $short_booking_type_arr[$val[csf("booking_no")]]=$short_booking_type[$val[csf("short_booking_type")]];
            if($val[csf("booking_type")]==1 && $val[csf("is_short")]==2)
            {
                $booking_type_arr[$val[csf("booking_no")]]="Main";
            }
            else if($val[csf("booking_type")]==1 && $val[csf("is_short")]==1)
            {
                $booking_type_arr[$val[csf("booking_no")]]="Short";
            }
            else if($val[csf("booking_type")]==4)
            {
                $booking_type_arr[$val[csf("booking_no")]]="Sample";
            }
        }
    }

    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_name and entry_form in (1234,1235)");
    oci_commit($con);
    
    ob_start();
    ?>
    <style type="text/css">
        .word_wrap_break {
            word-break: break-all;
            word-wrap: break-word;
        }
    </style>
    <div align="left">
        <fieldset style="width:2815px;">
            <?
            if(count($roll_issue_data)>0)
            {
                ?>
                <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong>
                <br><b>
                    <?
                    $date_head="";
                    if( $date_from)
                    {
                        $date_head .= change_date_format($date_from).' To ';
                    }
                    if( $date_to)
                    {
                        $date_head .= change_date_format($date_to);
                    }
                    echo $date_head;
                    ?> </b>
                </div>
                <?
            }
            else
            {
                echo "<b>Data Not Found</b>";
            }
            ?>
            <div align="left">
                <table class="rpt_table" width="3395" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th colspan="28">Grey Fabric Information</th>
                            <th colspan="13">Batch Receive Status</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th width="30" class="word_wrap_break">SL</th>
                            <th width="75" class="word_wrap_break">Grey Issue Date</th>
                            <th width="100" class="word_wrap_break">Issue Challan No</th>
                            <th width="80" class="word_wrap_break">Company</th>
                            <th width="80" class="word_wrap_break">Source</th>
                            <th width="80" class="word_wrap_break">Dyeing Company</th>
                            <th width="80" class="word_wrap_break">Issue Purpose</th>
                            <th width="80" class="word_wrap_break">Buyer</th>
                            <th width="60" class="word_wrap_break">Job No</th>
                            <th width="70" class="word_wrap_break">Style No</th>
                            <th width="70" class="word_wrap_break">Season</th>
                            <th width="110" class="word_wrap_break">FSO No</th>
                            <th width="120" class="word_wrap_break">Fabric Booking No.</th>
                            <th width="50" class="word_wrap_break">Booking Type</th>
                            <th width="70" class="word_wrap_break">Body Part</th>
                            <th width="100" class="word_wrap_break">Construction</th>
                            <th width="150" class="word_wrap_break">Fab. Composition</th>
                            <th width="50" class="word_wrap_break">Dia/ Width</th>
                            <th width="50" class="word_wrap_break">GSM</th>
                            <th width="70" class="word_wrap_break">Grey Issue Qty.</th>
                            <th width="50" class="word_wrap_break">No of Roll</th>
                            <th width="50" class="word_wrap_break">Remarks</th>

                            <th width="100" class="word_wrap_break">Insert User Name</th>
                            <th width="100" class="word_wrap_break">Insert Date</th>
                            <th width="100" class="word_wrap_break">Insert Time</th>
                            <th width="100" class="word_wrap_break">Update User Name</th>
                            <th width="100" class="word_wrap_break">Last Update Date</th>
                            <th width="100" class="word_wrap_break">Last Update Time</th>

                            <th width="70" class="word_wrap_break">Batch receive Date</th>
                            <th width="100" class="word_wrap_break">Batch receive Challan No</th>
                            <th width="70" class="word_wrap_break">Receive Qty.</th>
                            <th width="70" class="word_wrap_break">Receive Balance</th>
                            <th width="40" class="word_wrap_break">No of Roll</th>
                            <th width="70" class="word_wrap_break">Receive Status</th>
                            <th class="word_wrap_break" width="40">Execution Days</th>
                            <th class="word_wrap_break" width="100">Insert User Name</th>

                            <th class="word_wrap_break" width="100">Insert Date</th>
                            <th class="word_wrap_break" width="100">Insert Time</th>
                            <th class="word_wrap_break" width="100">Update User Name</th>
                            <th class="word_wrap_break" width="100">Last Update Date</th>
                            <th class="word_wrap_break" width="100">Last Update Time</th>
                        </tr>
                    </thead>
                </table>
                <div style=" max-height:350px; width:3415px; overflow-y:scroll;" id="scroll_body">
                    <table class="rpt_table" id="table_body" width="3395" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <tbody>
                            <?
                            $i=1;$total_issue_qty=$total_roll_count=$total_rcv_batch_qty=$total_receive_balance=$total_batch_roll_count=0;
                            $batch_receive_date="";
                            foreach ($roll_data_arr as $issue_id_key => $issue_val)
                            {
                                foreach ($issue_val as $fso_id_key => $fso_id_val)
                                {
                                    foreach ($fso_id_val as $body_part_key => $body_part_val)
                                    {
                                        foreach ($body_part_val as $detar_id_key => $detar_id_val) 
                                        {
                                            foreach ($detar_id_val as $dia_width_key => $dia_width_val)
                                            {
                                                foreach ($dia_width_val as $gsm_key => $row)
                                                {
                                                    $batch_issue_number=$recv_challan_no_arr[$row['issue_number']]['challan_no'];
                                                    /*$sta="";
                                                    if ($batch_issue_number=="") 
                                                    {
                                                        echo $sta='Full Pending';
                                                    }*/
                                                    $batch_receive_date=$recv_batch_arr[$row['issue_number']][$fso_id_key][$body_part_key][$detar_id_key][$dia_width_key][$gsm_key]['receive_date'];
                                                    $batch_recv_number=$recv_batch_arr[$row['issue_number']][$fso_id_key][$body_part_key][$detar_id_key][$dia_width_key][$gsm_key]['recv_number'];
                                                    $rcv_batch_qty=$recv_batch_arr[$row['issue_number']][$fso_id_key][$body_part_key][$detar_id_key][$dia_width_key][$gsm_key]['batch_qty'];
                                                    $batch_roll_count=$recv_batch_arr[$row['issue_number']][$fso_id_key][$body_part_key][$detar_id_key][$dia_width_key][$gsm_key]['batch_roll_count'];
                                                    $issue_qty=number_format($row['issue_qty'],2,'.','');
                                                    $batch_rcv_qty=number_format($rcv_batch_qty,2,'.','');
                                                    $receive_balance=$issue_qty-$batch_rcv_qty;
                                                    
                                                    //$recv_status = ($receive_balance>0) ? "Partial Receive" : "Full" ;
                                                    $recv_status="";
                                                    if ($batch_issue_number=="") 
                                                    {
                                                        $recv_status ='Full Pending';
                                                    }
                                                    else if($receive_balance>0)
                                                    {
                                                        $recv_status = "Partial Receive";
                                                    }
                                                    else
                                                    {
                                                        $recv_status = "Full";
                                                    }

                                                    $batch_insert_user=$recv_batch_arr[$row['issue_number']][$fso_id_key][$body_part_key][$detar_id_key][$dia_width_key][$gsm_key]['inserted_by'];
                                                    $batch_insert_date=$recv_batch_arr[$row['issue_number']][$fso_id_key][$body_part_key][$detar_id_key][$dia_width_key][$gsm_key]['insert_date'];
                                                    $batch_update_name=$recv_batch_arr[$row['issue_number']][$fso_id_key][$body_part_key][$detar_id_key][$dia_width_key][$gsm_key]['updated_by'];
                                                    $batch_update_date=$recv_batch_arr[$row['issue_number']][$fso_id_key][$body_part_key][$detar_id_key][$dia_width_key][$gsm_key]['update_date'];

                                                    $issue_date=change_date_format($row['issue_date']);
                                                    $batch_recv_date=change_date_format($batch_receive_date);
                                                    $date1 = strtotime($issue_date);
                                                    $date2 = strtotime($batch_recv_date);
                                                    $execution_days= ($date2 - $date1)/60/60/24; 
                                                    //echo $execution_days+1;

                                                    $desc = explode(",", $row['item_description']);
                                                    $data=$row['company_id'].'*'.$row['issue_number'].'*'.$issue_id_key;
                                                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                                    ?>
                                                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                                                        <td class="word_wrap_break" width="30"><? echo $i; ?></td>
                                                        <td class="word_wrap_break" align="center" width="75"><p><? echo change_date_format($row['issue_date']); ?></p></td>
                                                        
                                                        <?
                                                        echo "<td class='word_wrap_break' width='100'><p><a href='../../grey_fabric/requires/grey_fabric_issue_roll_wise_controller.php?data=".$data."&action=sales_roll_issue_challan_print' target='_blank'> ".$row['issue_number']." </a></p></td>";
                                                        ?>

                                                        <td  width="80"><p><? echo $company_library[$row['company_id']]; ?></p></td>
                                                        <td width="80"><p class="word_wrap_break"><? echo $knitting_source[$row['knit_dye_source']]; ?></p></td>
                                                        <td width="80"><p class="word_wrap_break"><? 
                                                        if ($row['knit_dye_source']==1) 
                                                        {
                                                            echo $company_library[$row['knit_dye_company']];
                                                        }
                                                        else
                                                        {
                                                            echo $supplier_arr[$row['knit_dye_company']];
                                                        } 
                                                        ?></p></td>
                                                        <td width="80"><p class="word_wrap_break"><? echo $yarn_issue_purpose[$row['issue_purpose']]; ?></p></td>
                                                        <td width="80">
                                                            <p class="word_wrap_break">
                                                            <?
                                                                if($row['within_group'] == 1)
                                                                {
                                                                    $buyer_id = $row['po_buyer'];
                                                                }
                                                                else
                                                                {
                                                                    $buyer_id = $row['buyer_id'];
                                                                }
                                                                echo $buyer_arr[$buyer_id];
                                                            ?>
                                                            </p>
                                                        </td>
                                                        <td class="word_wrap_break" width="60" align="center"><p><? echo chop($row["po_job_no"],","); ?></p></td>
                                                        <td width="70"><p class="word_wrap_break"><? echo $row['style_ref_no']; ?></p></td>
                                                        <td class="word_wrap_break" width="70" align="center"><p><? echo $season_name_arr[$row['season_id']]; ?></p></td>
                                                        <td class="word_wrap_break" width="110"><p><?  echo $row['fso_no']; ?></p></td>
                                                        <td class="word_wrap_break" width="120"><p><? echo $row['sales_booking_no']; ?></p></td>
                                                        <td class="word_wrap_break" width="50"><p><? echo $booking_type_arr[$row['sales_booking_no']]; ?></p></td>
                                                        <td class="word_wrap_break" width="70"><p><? echo $body_part[$body_part_key]; ?></p></td>
                                                        <td width="100" align="center"><p class="word_wrap_break"><? echo $desc[0]; ?></p></td>
                                                        <td width="150" align="center"><p class="word_wrap_break"><? echo $desc[1]; ?></p></td>
                                                        <td width="50" align="center"><p class="word_wrap_break"><? echo $dia_width_key; ?></p></td>
                                                        <td width="50" align="center"><p class="word_wrap_break"><? echo $gsm_key; ?></p></td>
                                                        <td class="word_wrap_break" width="70" align="right"><p><? echo $issue_qty; ?></p></td>
                                                        <td width="50" align="center"><p class="word_wrap_break"><a  href="##" onClick="issue_roll_popup('<? echo $issue_id_key; ?>','<? echo $fso_id_key; ?>','<? echo rtrim($row['dtls_id'],'*'); ?>', 'roll_issue_popup')"><? echo $row['roll_count']; ?></a></p></td>
                                                        <td width="50" align="center"><p class="word_wrap_break"><? echo $row['remarks']; ?></p></td>
                                                        <td width="100" align="center"><p class="word_wrap_break"><? echo $user_name_arr[$row['inserted_by']]; ?></p></td>
                                                        <td width="100" align="center"><p class="word_wrap_break"><? echo change_date_format($row['insert_date']); ?></p></td>
                                                        <td width="100" align="center"><p class="word_wrap_break"><? echo date('h:i:s a',strtotime($row['insert_date'])); ?></p></td>
                                                        <td width="100" align="center"><p class="word_wrap_break"><? echo $user_name_arr[$row['updated_by']]; ?></p></td>
                                                        <td class="word_wrap_break" width="100" align="center"><? echo change_date_format($row['update_date']); ?></td> 
                                                        <td class="word_wrap_break" width="100" align="center"><? if($row['update_date']!="") echo date('h:i:s a',strtotime($row['update_date'])); ?></td>

                                                        <td class="word_wrap_break" align="center" width="70"><? echo change_date_format($batch_receive_date);  ?></td>
                                                        <td class="word_wrap_break"  width="100"><p><? echo $batch_recv_number; ?></p></td>
                                                        <td class="word_wrap_break" align="right" width="70"><? echo $batch_rcv_qty; ?></td>
                                                        
                                                        <td class="word_wrap_break" align="right" width="70" title="Grey Issue Qty - Receive Qty, <? echo $issue_qty.'-'.$batch_rcv_qty; ?>">
                                                            <?
                                                            echo number_format($receive_balance,2,'.',''); 
                                                            ?>
                                                        </td>

                                                        <td class="word_wrap_break" width="40" align="center"><? echo $batch_roll_count;?></td>
                                                        <td class="word_wrap_break" align="center" width="70"><? echo $recv_status;?></td>
                                                        <td class="word_wrap_break" width="40" align="center"><p><? 
                                                        if($batch_recv_date!="") { echo $execution_days+1; } ?></p></td>
                                                        <td width="100" class="word_wrap_break" align="center"><? echo $user_name_arr[$batch_insert_user];?></td>
                                                        <td width="100" class="word_wrap_break" align="center"><? echo change_date_format($batch_insert_date);?></td>
                                                        <td width="100" class="word_wrap_break" align="center"><? if($batch_insert_date!="") echo date('h:i:s a',strtotime($batch_insert_date)); ?></td>
                                                        <td width="100" class="word_wrap_break" align="center"><? echo $user_name_arr[$batch_update_name];?></td>
                                                        <td width="100" class="word_wrap_break" align="center"><? echo change_date_format($batch_update_date);?></td>
                                                        <td width="100" class="word_wrap_break" align="center"><? if($batch_update_date!="") echo date('h:i:s a',strtotime($batch_update_date));?></td>
                                                    </tr>
                                                    <?
                                                    $i++;
                                                    $total_issue_qty+=$row['issue_qty'];
                                                    $total_roll_count+=$row['roll_count'];
                                                    $total_rcv_batch_qty+=$rcv_batch_qty;
                                                    $total_receive_balance+=$receive_balance;
                                                    $total_batch_roll_count+=$batch_roll_count;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <table class="rpt_table" width="3395" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <tr>
                            <th width="30" class="word_wrap_break"></th>
                            <th width="75" class="word_wrap_break"></th>
                            <th width="100" class="word_wrap_break"></th>
                            <th width="80" class="word_wrap_break"></th>
                            <th width="80" class="word_wrap_break"></th>
                            <th width="80" class="word_wrap_break"></th>
                            <th width="80" class="word_wrap_break"></th>
                            <th width="80" class="word_wrap_break"></th>
                            <th width="60" class="word_wrap_break"></th>
                            <th width="70" class="word_wrap_break"></th>
                            <th width="70" class="word_wrap_break"></th>
                            <th width="110" class="word_wrap_break"></th>
                            <th width="120" class="word_wrap_break"></th>
                            <th width="50" class="word_wrap_break"></th>
                            <th width="70" class="word_wrap_break"></th>
                            <th width="100" class="word_wrap_break"></th>
                            <th width="150" class="word_wrap_break"></th>
                            <th width="50" class="word_wrap_break"></th>
                            <th width="50">Total:</th>
                            <th width="70" align="right"><? echo number_format($total_issue_qty,4); ?></th>
                            <th width="50" align="center"><? echo $total_roll_count; ?></th>
                            <th width="50" class="word_wrap_break"></th>

                            <th width="100" class="word_wrap_break"></th>
                            <th width="100" class="word_wrap_break"></th>
                            <th width="100" class="word_wrap_break"></th>
                            <th width="100" class="word_wrap_break"></th>
                            <th width="100" class="word_wrap_break"></th>
                            <th width="100" class="word_wrap_break"></th>

                            <th width="70" class="word_wrap_break"></th>
                            <th width="100" class="word_wrap_break"></th>
                            <th width="70" class="word_wrap_break" align="right"><? echo number_format($total_rcv_batch_qty,4); ?></th>
                            <th width="70" class="word_wrap_break" align="right"><? echo number_format($total_receive_balance,4); ?></th>
                            <th width="40" class="word_wrap_break"><? echo $total_batch_roll_count; ?></th>
                            <th width="70" class="word_wrap_break"></th>
                            <th class="word_wrap_break" width="40"></th>
                            <th class="word_wrap_break" width="100"></th>

                            <th class="word_wrap_break" width="100"></th>
                            <th class="word_wrap_break" width="100"></th>
                            <th class="word_wrap_break" width="100"></th>
                            <th class="word_wrap_break" width="100"></th>
                            <th class="word_wrap_break" width="100"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </fieldset>
    </div>
    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_name*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
}

if ($action == "roll_issue_popup") 
{
    echo load_html_head_contents("Report Info", "../../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <div align="center" id="data_panel" style="width:100%">
        <script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('report_container').innerHTML);
                d.close();
            }
        </script>
        <div id="exc"></div>
    </div>
    <fieldset style="width:380px; margin-left:7px">        
        <?    
        //echo $dtls_id.'<br>';        
        $dtls_id = implode(',', explode("*", $dtls_id)) ;
        //print_r($dtls_id);
        $issue_popup_sql="SELECT  e.roll_no, e.barcode_no, e.qnty as roll_wgt
        from fabric_sales_order_mst a, inv_issue_master b, inv_transaction c, inv_grey_fabric_issue_dtls d, pro_roll_details e
        where b.id=e.mst_id and b.id=c.mst_id and c.id=d.trans_id and b.id=d.mst_id and d.id=e.dtls_id  and e.po_breakdown_id=a.id and e.is_sales=1 and b.is_deleted=0 
        and b.entry_form in(61) and e.entry_form in(61) and e.is_returned=0 
        and b.id=$issue_id and a.id=$fso_id and d.id in($dtls_id)
        and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0";
        //echo $issue_popup_sql;//die;
        $issue_popup_data=sql_select($issue_popup_sql);
        $issue_roll_arr = array();
        foreach($issue_popup_data as $val)
        {
            $issue_roll_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
        }
        $issue_roll = implode(",", $issue_roll_arr);
        $issue_roll_cond="";
        if($issue_roll)
        {
            $issue_roll = implode(",",array_filter(array_unique(explode(",", $issue_roll))));
            $issue_roll_arr = explode(",", $issue_roll);
            if($db_type==0)
            {
                $issue_roll_cond = " and a.id in ($issue_roll )";
            }
            else
            {
                if(count($issue_roll_arr)>999)
                {
                    $issue_roll_chunk_arr=array_chunk($issue_roll_arr, 999);
                    $issue_roll_cond=" and (";
                    foreach ($issue_roll_chunk_arr as $value)
                    {
                        $issue_roll_cond .=" c.barcode_no in (".implode(",", $value).") or ";
                    }
                    $issue_roll_cond=chop($issue_roll_cond,"or ");
                    $issue_roll_cond.=")";
                }
                else
                {
                    $issue_roll_cond = " and c.barcode_no in ($issue_roll )";
                }
            }
        }
        // echo  $issue_roll_cond;die;
        $recv_batch_sql="SELECT c.barcode_no
        from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c 
        where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id $issue_roll_cond
        and a.entry_form=62 and c.entry_form=62 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 ";
        // echo $recv_batch_sql;die;
        $recv_batch_data=sql_select($recv_batch_sql);
        $recv_batch_arr=array();
        foreach ($recv_batch_data as $rows)
        {
            $recv_batch_arr[$rows[csf("barcode_no")]]['barcode_no']=$rows[csf("barcode_no")];
        }

        $table_width=350;
        $div_width=370;
        $table_width2=350;
        ob_start();
        ?>
        <div id="report_container">
            <div style="width:<? echo $table_width+20; ?>px; float:left;">
                <table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
                    <thead>
                        <tr>
                            <th width="40">SL</th>
                            <th width="40">Roll No</th>
                            <th width="100">Barcode No</th>
                            <th width="70">Roll Wgt.</th>
                            <th width="">Status</th>
                        </tr>
                    </thead>
                </table>
                
                <div style="width:<? echo $div_width; ?>px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body">
                <table width="<? echo $table_width2; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show6" style="float:left;">
                    <tbody>
                    <?
                    $i=1;
                    $total_roll_wgt=0;
                    foreach ($issue_popup_data as $batch_no_key => $row)
                    {
                        $status="";
                        if($recv_batch_arr[$row[csf("barcode_no")]]['barcode_no']=="")
                        {
                            $status="Pending";
                        }
                        else{
                            $status="Received";
                        }
                        ?>                      
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
                            <td width="40" align="center"><p><? echo $i; ?></p></td>
                            <td width="40" align="center"><p><? echo $row[csf('roll_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('barcode_no')]; ?></p></td>
                            <td width="70" align="right"><p><? echo $row[csf('roll_wgt')]; ?></p></td>
                            <td width="" align="center"><p><? echo $status; ?></p></td>
                        </tr>
                        <?
                        $i++;
                        $total_roll_wgt+=$row[csf('roll_wgt')];
                    }
                    ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </fieldset>  
    <?        
    foreach (glob("$user_name*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    $filename=$user_name."_".$name.".xls";
    //echo "$total_data####$filename####$reportType";
    $filename=$filename;

    ?>
    <script>
        document.getElementById('exc').innerHTML='<a href="<? echo $filename;?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>';
    </script>
    <?
    exit();
}


if ($action == "FSO_No_popup")
{
    echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>

    <script>
        var hide_fso_id='<? echo $hide_fso_id; ?>';
        var selected_id = new Array, selected_name = new Array();

        function check_all_data(is_checked)
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ ) {
                js_set_value( i );
            }
        }

        function toggle( x, origColor )
        {
            var newColor = 'yellow';
            if ( x.style )
            {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }

        function set_all()
        {
            var old=document.getElementById('txt_fso_row_id').value;
            if(old!="")
            {
                old=old.split(",");
                for(var i=0; i<old.length; i++)
                {
                    js_set_value( old[i] )
                }
            }
        }

        function js_set_value( str)
        {

            toggle( document.getElementById( 'search' + str ), '#FFFFCC' );


            if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 )
            {
                selected_id.push( $('#txt_individual_id' + str).val() );
                selected_name.push( $('#txt_individual' + str).val() );

            }
            else
            {
                for( var i = 0; i < selected_id.length; i++ )
                {
                    if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i, 1 );
            }
            var id =''; var name = '';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
            }
            id = id.substr( 0, id.length - 1 );
            name = name.substr( 0, name.length - 1 );

            $('#hide_fso_id').val( id );
            $('#hide_fso_no').val( name );
        }

    </script>

</head>
<body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset style="width:710px;">
                <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                    <thead>
                        <th>Company</th>
                        <th>Buyer Name</th>
                        <th>Job Year</th>
                        <th>Within Group</th>
                        <th>FSO NO.</th>
                        <th>Booking NO.</th>
                        <th>Grey Issue Challan No</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
                        <input type="hidden" name="hide_fso_no" id="hide_fso_no" value="" />
                        <input type="hidden" name="hide_fso_id" id="hide_fso_id" value="" />

                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?
                                echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_id, "",0 );
                                ?>
                            </td>
                            <td align="center">
                                <?
                                echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company=$cbo_company_id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
                                ?>
                            </td>
                            <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                            <td><? echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "-- Select --", $selected, "",0,"" );?></td>
                            <td>
                                <input type="text" style="width:100px" class="text_boxes" name="txt_fso_no" id="txt_fso_no" />
                            </td>
                            <td>
                                <input type="text" style="width:100px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
                            </td>
                            <td>
                                <input type="text" style="width:100px" class="text_boxes" name="txt_issue_no" id="txt_issue_no" />
                            </td>
                            <td align="center">
                                <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+'<? echo $hidden_fso_id;?>'+'**'+document.getElementById('txt_issue_no').value, 'create_fso_no_search_list_view', 'search_div', 'batch_receive_wip_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-top:15px" id="search_div"></div>
            </fieldset>
        </form>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_fso_no_search_list_view")
{
    $data=explode('**',$data);
    // print_r($data);
    $company_id=$data[0];
    $buyer_id=$data[1];
    $year=$data[2];
    $within_group=$data[3];
    $fso_no=trim($data[4]);
    $booking_no=trim($data[5]);
    $hidden_fso_id=trim($data[6]);
    $issue_no=trim($data[7]);

    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

    $search_cond = "";

    if($data[1]==0)
    {
        if ($_SESSION['logic_erp']["data_level_secured"]==1)
        {
            if($_SESSION['logic_erp']["buyer_id"]!="")
            {
                $buyer_cond_with_1=" and c.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
                $buyer_cond_with_2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
            }
            else
            {
                $buyer_cond_with_1 =  "";
                $buyer_cond_with_2 =  "";
            }
        }
        else
        {
            $buyer_cond_with_1 =  "";
            $buyer_cond_with_2 =  "";
        }
    }
    else
    {
        $buyer_cond_with_1 =  " and c.buyer_id=$data[1]";
        $buyer_cond_with_2 =  " and a.buyer_id=$data[1]";
    }


    if($fso_no != "")
    {
        $search_cond .= " and a.job_no like '%$fso_no%'" ;
    }
    if($issue_no != "")
    {
        $search_cond .= " and e.issue_number like '%$issue_no%'" ;
    }
    if($booking_no != "")
    {
        $search_cond .= " and a.sales_booking_no like '%$booking_no%'" ;
    }
    if($db_type==0)
    {
        if($year!=0) $search_cond .=" and YEAR(a.insert_date)=$year"; else $search_cond .="";
    }
    else if($db_type==2)
    {
        $year_field_con=" and to_char(a.insert_date,'YYYY')";
        if($year!=0) $search_cond .=" $year_field_con=$year"; else $search_cond .="";
    }
    // echo $search_cond; die;
    $sql_2 ="SELECT a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id, e.issue_number 
    from fabric_sales_order_mst a, fabric_sales_order_dtls b, pro_roll_details d, inv_issue_master e
    where a.id = b.mst_id and a.id=d.po_breakdown_id and d.mst_id=e.id  and d.entry_form=61 and e.entry_form=61 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '2' $buyer_cond_with_2
    group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id, e.issue_number 
    order by id desc";

    $sql_1 = "SELECT a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id, e.issue_number 
    from fabric_sales_order_mst a, fabric_sales_order_dtls b ,wo_booking_mst c, pro_roll_details d, inv_issue_master e
    where a.id = b.mst_id and a.sales_booking_no = c.booking_no and a.id=d.po_breakdown_id and d.mst_id=e.id  and d.entry_form=61 and e.entry_form=61 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1
    and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '1' $buyer_cond_with_1
    group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id, e.issue_number ";

    if($within_group == 1)
    {
        $sql = $sql_1 ;
    }
    else if($within_group == 2)
    {
        $sql = $sql_2;
    }
    else
    {
        $sql = $sql_1." union all ". $sql_2 ;
    }
    //echo $sql;
    ?>

    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" align="left">
        <thead>
            <th width="40">SL</th>
            <th width="130">Company</th>
            <th width="120">Buyer</th>
            <th width="150">FSO No</th>
            <th width="100">Booking No</th>
            <th width="">Grey Issue Challan No</th>
        </thead>
    </table>
    <div style="width:700px; overflow-y:scroll; max-height:250px; float: left;" id="buyer_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="682" class="rpt_table" id="tbl_list_search" >
            <?php
            $i=1; $fso_row_id="";
            $nameArray=sql_select( $sql );
            foreach ($nameArray as $selectResult)
            {
                if ($i%2==0)
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
                ?>

                <tr height="20" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                    <td width="40" align="center"><?php echo "$i"; ?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>
                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $selectResult[csf('job_no')]; ?>"/>
                </td>
                <td width="130"><p><?php echo $company_arr[$selectResult[csf('company_id')]]; ?></p></td>
                <td width="120" title="<? echo $selectResult[csf('buyer_id')];?>"><?php echo $buyer_arr[$selectResult[csf('buyer_id')]]; ?></td>
                <td width="150"><p><?php echo $selectResult[csf('job_no')];?></p></td>
                <td width="100"><?php echo $selectResult[csf('sales_booking_no')];?></td>
                <td width=""><?php echo $selectResult[csf('issue_number')];?></td>
            </tr>
            <?
            $i++;
        }
        ?>
    </table>
</div>

<table width="600" cellspacing="0" cellpadding="0" style="border:none" align="left">
    <tr>
        <td align="center" height="30" valign="bottom">
            <div style="width:100%">
                <div style="width:50%; float:left" align="left">
                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                </div>
                <div style="width:50%; float:left" align="left">
                    <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                </div>
            </div>
        </td>
    </tr>
</table>

<?
exit();
}


if ($action == "FSO_No_popup________")
{
    echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>

    <script>
        var hide_fso_id='<? echo $hide_fso_id; ?>';
        /*var selected_id = new Array, selected_name = new Array();

        function check_all_data(is_checked)
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ ) {
                js_set_value( i );
            }
        }

        function toggle( x, origColor )
        {
            var newColor = 'yellow';
            if ( x.style )
            {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }

        function set_all()
        {
            var old=document.getElementById('txt_fso_row_id').value;
            if(old!="")
            {
                old=old.split(",");
                for(var i=0; i<old.length; i++)
                {
                    js_set_value( old[i] )
                }
            }
        }

        function js_set_value( str)
        {

            toggle( document.getElementById( 'search' + str ), '#FFFFCC' );


            if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 )
            {
                selected_id.push( $('#txt_individual_id' + str).val() );
                selected_name.push( $('#txt_individual' + str).val() );

            }
            else
            {
                for( var i = 0; i < selected_id.length; i++ )
                {
                    if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i, 1 );
            }
            var id =''; var name = '';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
            }
            id = id.substr( 0, id.length - 1 );
            name = name.substr( 0, name.length - 1 );

            $('#hide_fso_id').val( id );
            $('#hide_fso_no').val( name );
        }*/
        var selected_id = new Array; var selected_name = new Array;
        
        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ )
            {
                $('#tr_'+i).trigger('click'); 
            }
        }
        
        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }
        
        function js_set_value( str ) {
            
            if (str!="") str=str.split("_");
             
            toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
            //alert(str[1]);
            var str2;
            if (str2!="") str2=str[1].split("**");
            alert(str2);
            if( jQuery.inArray( str[1], selected_id ) == -1 ) 
            {   
                alert(selected_id);
                selected_id.push( str[1] );
                selected_name.push( str[2] );                
            }
            else 
            {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == str[1] ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i, 1 );
            }
            var id = ''; var name = '';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
            }
            
            id = id.substr( 0, id.length - 1 );
            name = name.substr( 0, name.length - 1 );
            //alert(id);
            //id=id.split("**");
            //alert(id);
            $('#hide_fso_id').val( id );
            $('#hide_fso_no').val( name );
        }

    </script>

</head>
<body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset style="width:710px;">
                <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                    <thead>
                        <th>Company</th>
                        <th>Buyer Name</th>
                        <th>Job Year</th>
                        <th>Within Group</th>
                        <th>FSO NO.</th>
                        <th>Booking NO.</th>
                        <th>Grey Issue Challan No</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
                        <input type="hidden" name="hide_fso_no" id="hide_fso_no" value="" />
                        <input type="hidden" name="hide_fso_id" id="hide_fso_id" value="" />

                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?
                                echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_id, "",0 );
                                ?>
                            </td>
                            <td align="center">
                                <?
                                echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company=$cbo_company_id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
                                ?>
                            </td>
                            <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                            <td><? echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "-- Select --", $selected, "",0,"" );?></td>
                            <td>
                                <input type="text" style="width:100px" class="text_boxes" name="txt_fso_no" id="txt_fso_no" />
                            </td>
                            <td>
                                <input type="text" style="width:100px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
                            </td>
                            <td>
                                <input type="text" style="width:100px" class="text_boxes" name="txt_issue_no" id="txt_issue_no" />
                            </td>
                            <td align="center">
                                <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+'<? echo $hidden_fso_id;?>'+'**'+document.getElementById('txt_issue_no').value, 'create_fso_no_search_list_view', 'search_div', 'batch_receive_wip_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-top:15px" id="search_div"></div>
            </fieldset>
        </form>
    </div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_fso_no_search_list_view______")
{
    $data=explode('**',$data);
    // print_r($data);
    $company_id=$data[0];
    $buyer_id=$data[1];
    $year=$data[2];
    $within_group=$data[3];
    $fso_no=trim($data[4]);
    $booking_no=trim($data[5]);
    $hidden_fso_id=trim($data[6]);
    $issue_no=trim($data[7]);

    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

    $search_cond = "";

    if($data[1]==0)
    {
        if ($_SESSION['logic_erp']["data_level_secured"]==1)
        {
            if($_SESSION['logic_erp']["buyer_id"]!="")
            {
                $buyer_cond_with_1=" and c.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
                $buyer_cond_with_2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
            }
            else
            {
                $buyer_cond_with_1 =  "";
                $buyer_cond_with_2 =  "";
            }
        }
        else
        {
            $buyer_cond_with_1 =  "";
            $buyer_cond_with_2 =  "";
        }
    }
    else
    {
        $buyer_cond_with_1 =  " and c.buyer_id=$data[1]";
        $buyer_cond_with_2 =  " and a.buyer_id=$data[1]";
    }


    if($fso_no != "")
    {
        $search_cond .= " and a.job_no like '%$fso_no%'" ;
    }
    if($issue_no != "")
    {
        $search_cond .= " and e.issue_number like '%$issue_no%'" ;
    }
    if($booking_no != "")
    {
        $search_cond .= " and a.sales_booking_no like '%$booking_no%'" ;
    }
    if($db_type==0)
    {
        if($year!=0) $search_cond .=" and YEAR(a.insert_date)=$year"; else $search_cond .="";
    }
    else if($db_type==2)
    {
        $year_field_con=" and to_char(a.insert_date,'YYYY')";
        if($year!=0) $search_cond .=" $year_field_con=$year"; else $search_cond .="";
    }
    // echo $search_cond; die;
    $sql_2 ="SELECT a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id, e.issue_number 
    from fabric_sales_order_mst a, fabric_sales_order_dtls b, pro_roll_details d, inv_issue_master e
    where a.id = b.mst_id and a.id=d.po_breakdown_id and d.mst_id=e.id  and d.entry_form=61 and e.entry_form=61 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '2' $buyer_cond_with_2
    group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id, e.issue_number 
    order by id desc";

    $sql_1 = "SELECT a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id, e.issue_number 
    from fabric_sales_order_mst a, fabric_sales_order_dtls b ,wo_booking_mst c, pro_roll_details d, inv_issue_master e
    where a.id = b.mst_id and a.sales_booking_no = c.booking_no and a.id=d.po_breakdown_id and d.mst_id=e.id  and d.entry_form=61 and e.entry_form=61 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1
    and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '1' $buyer_cond_with_1
    group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id, e.issue_number ";

    if($within_group == 1)
    {
        $sql = $sql_1 ;
    }
    else if($within_group == 2)
    {
        $sql = $sql_2;
    }
    else
    {
        $sql = $sql_1." union all ". $sql_2 ;
    }
    //echo $sql;
    ?>

    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" align="left">
        <thead>
            <th width="40">SL</th>
            <th width="130">Company</th>
            <th width="120">Buyer</th>
            <th width="150">FSO No</th>
            <th width="100">Booking No</th>
            <th width="">Grey Issue Challan No</th>
        </thead>
    </table>
    <div style="width:700px; overflow-y:scroll; max-height:250px; float: left;" id="buyer_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="682" class="rpt_table" id="tbl_list_search" >
            <?php
            $i=1; $fso_row_id="";
            $nameArray=sql_select( $sql );
            foreach ($nameArray as $selectResult)
            {
                if ($i%2==0)
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
                ?>

                <tr height="20" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i.'_'.$selectResult[csf('id')].'**'.$i.'_'.$selectResult[csf('job_no')].'_'.$selectResult[csf('issue_number')];?>')">
                    <td width="40" align="center"><?php echo "$i"; ?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>
                    <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $selectResult[csf('job_no')]; ?>"/>
                    </td>
                    <td width="130"><p><?php echo $company_arr[$selectResult[csf('company_id')]]; ?></p></td>
                    <td width="120" title="<? echo $selectResult[csf('buyer_id')];?>"><?php echo $buyer_arr[$selectResult[csf('buyer_id')]]; ?></td>
                    <td width="150"><p><?php echo $selectResult[csf('job_no')];?></p></td>
                    <td width="100"><?php echo $selectResult[csf('sales_booking_no')];?></td>
                    <td width=""><?php echo $selectResult[csf('issue_number')];?></td>
                </tr>
            <?
            $i++;
        }
        ?>
    </table>
</div>

<table width="600" cellspacing="0" cellpadding="0" style="border:none" align="left">
    <tr>
        <td align="center" height="30" valign="bottom">
            <div style="width:100%">
                <div style="width:50%; float:left" align="left">
                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                </div>
                <div style="width:50%; float:left" align="left">
                    <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                </div>
            </div>
        </td>
    </tr>
</table>

<?
exit();
}
?>