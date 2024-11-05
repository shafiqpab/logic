<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0);  
    exit();
}

if($action=="job_no_popup")
{
    echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>

    <script>
        var selected_id = new Array, selected_name = new Array();

        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ )
            {
                $('#tr_'+i).trigger('click'); 
            }
        }

        function toggle( x, origColor )
        {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }

        function js_set_value(id)
        {
            var str=id.split("_");
            toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
            var strdt=str[2];
            str=str[1];

            if( jQuery.inArray(  str , selected_id ) == -1 ) {
                selected_id.push( str );
                selected_name.push( strdt );
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == str  ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i,1 );
            }
            var id = '';
            var ddd='';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                ddd += selected_name[i] + ',';
            }
            id = id.substr( 0, id.length - 1 );
            ddd = ddd.substr( 0, ddd.length - 1 );
            $('#hide_job_id').val( id );
            $('#hide_job_no').val( ddd );
        }
    </script>

    </head>
        <body>
            <div align="center">
                <form name="styleRef_form" id="styleRef_form">
                    <fieldset style="width:580px;">
                        <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                            <thead>
                                <tr>
                                    <th width="150" colspan="2"> </th>
                                        <th><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                                    <th width="150" colspan="2"> </th>
                                </tr>
                                <tr>
                                    <th>Buyer</th>
                                    <th>Job Year</th>
                                    <th>Search By</th>
                                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                                    <th>
                                        <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                                        <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                                        <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td align="center">
                                        <?
                                        echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
                                        ?>
                                    </td>
                                    <td>
                                        <? echo create_drop_down( "cbo_job_year", 70, create_year_array(),"", 1,"-- All --", $cbo_year, "",0,"" ); ?>
                                    </td>
                                    <td align="center">
                                        <?
                                        $search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order",4=>"Booking");
                                        $dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
                                        echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                                        ?>
                                    </td>
                                    <td align="center" id="search_by_td">
                                        <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                                    </td>
                                    <td align="center">
                                        <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_job_year').value+'**'+document.getElementById('cbo_string_search_type').value, 'create_job_no_search_list_view', 'search_div', 'grey_fabric_stock_report_for_youth_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_job_no_search_list_view")
{
    $data=explode('**',$data);
    $company_id=$data[0];
    $year_id=$data[4];
    $search_type=$data[5];

    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

    

    if($data[1]==0)
    {
        if ($_SESSION['logic_erp']["data_level_secured"]==1)
        {
            if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
        }
        else
        {
            $buyer_id_cond="";
        }
    }
    else
    {
        $buyer_id_cond=" and buyer_name=$data[1]";
    }

    $search_by=$data[2];
    $search_string="%".trim($data[3])."%";

    if($search_by==1) 
    {
        // $search_field="a.job_no";
        $search_field="a.job_no_prefix_num";
    }
    else if($search_by==2)
    {
       $search_field="a.style_ref_no"; 
    }
    else if($search_by==3)
    {
        $search_field="b.po_number"; 
    }
    else 
    {
        $search_field="c.booking_no";
    }

    $search_cond=""; $order_cond=""; $style_cond="";
    if($search_type==1)
    {
        if (str_replace("'","",$data[3])!="") $search_cond=" and $search_field='$data[3]' ";
        // if (str_replace("'","",$data[3])!="") $order_cond=" and b.po_number = '$data[3]'  ";
        // if (trim($data[3])!="") $style_cond=" and a.style_ref_no ='$data[3]'";
    }
    else if($search_type==2)
    {
        if (str_replace("'","",$data[3])!="") $search_cond=" and $search_field like '$data[3]%' ";
        // if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '$data[6]%'  ";
        // if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '$data[7]%'  ";
    }
    else if($search_type==3)
    {
        if (str_replace("'","",$data[3])!="") $search_cond=" and $search_field like '%$data[3]' ";
        // if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]'  ";
        // if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]'";
    }
    else if($search_type==4 || $search_type==0)
    {
        if (str_replace("'","",$data[3])!="") $search_cond=" and $search_field like '%$data[3]%' ";
        // if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  ";
        // if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]%'";
    }
    // echo $search_cond;die;

    if($db_type==0)
    {
        if($year_id!=0) $year_search_cond=" and year(a.insert_date)=$year_id"; else $year_search_cond="";
        $year_cond= "year(a.insert_date)as year";
    }
    else if($db_type==2)
    {
        if($year_id!=0) $year_search_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
        $year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
    }

    /*$sql= "SELECT id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_cond 
    from wo_po_details_master
    where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond $month_cond order by job_no DESC";*/
    
    $sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_cond, c.booking_no, c.booking_type
    from wo_po_details_master a, wo_po_break_down  b, wo_booking_mst c
    where  a.job_no=b.job_no_mst and a.job_no=c.job_no and b.job_no_mst=c.job_no and c.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $year_search_cond $month_cond and c.booking_type in(1,4) and c.is_short in(1,2)
    group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.insert_date, c.booking_no, c.booking_type order by a.job_no desc";
    // echo $sql;
    $arr=array (0=>$buyer_arr);
    echo create_list_view("tbl_list_search", "Buyer Name,Year,Job No,Style,Booking No", "100,60,60,90,90","540","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "buyer_name,0,0,0,0", $arr , "buyer_name,year,job_no_prefix_num,style_ref_no,booking_no", "",'','0,0,0,0,0','',1) ;
    exit();
}

if($action=="generated_report")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $company = str_replace("'","",$cbo_company_name);
    $cbo_buyer_id = str_replace("'","",$cbo_buyer_id);
    $year = str_replace("'","",$cbo_year);
    $txt_job_no = str_replace("'","",$txt_job_no);
    $txt_job_id = str_replace("'","",$txt_job_id);
    $txt_int_ref = str_replace("'","",$txt_int_ref);
    $cbo_value_with = str_replace("'","",$cbo_value_with);

    if($txt_job_id == ""){
        $job_no_cond = ($txt_job_no != "") ? " and a.job_no_prefix_num in($txt_job_no)" : "";
    }else{
        $job_no_cond = " and a.id in($txt_job_id)";
    }

    $int_ref_cond = ($txt_int_ref != "") ? " and b.grouping='$txt_int_ref'" : "";

    $buyer_cond = ($cbo_buyer_id != 0) ? " and a.buyer_name=$cbo_buyer_id" : "";

    if($db_type==0) 
    {
        $year_field_by="and YEAR(a.insert_date)"; 
    }
    else if($db_type==2) 
    {
        $year_field_by=" and to_char(a.insert_date,'YYYY')";
    }
    if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";

    $company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

    $con = connect();
    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (100,102)");
    execute_query("delete from tmp_barcode_no where userid=$user_id");
    oci_commit($con);

    // ============================================= PO Start =====================================
    $po_sql="SELECT a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.grouping as int_ref, c.booking_no, b.shipment_date, c.dia_width, c.gsm_weight, c.fabric_color_id, c.fin_fab_qnty, c.grey_fab_qnty, d.body_part_id, d.lib_yarn_count_deter_id as deter_id
    from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst e, wo_pre_cost_fabric_cost_dtls d 
    where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and c.booking_no=e.booking_no and c.pre_cost_fabric_cost_dtls_id=d.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.booking_type in(1,4) and e.status_active=1 and e.is_deleted=0 and a.company_name=$company $year_cond $job_no_cond $buyer_cond $int_ref_cond";
    // echo $po_sql;die;// and a.job_no='RpC-22-00785'
    $po_result=sql_select($po_sql);
    foreach($po_result as $row)
    {
        $main_data_arr[$row[csf("job_no")]][$row[csf("deter_id")]]['body_part_id'].=$row[csf('body_part_id')].',';
        $main_data_arr[$row[csf("job_no")]][$row[csf("deter_id")]]['gsm_weight'].=$row[csf('gsm_weight')].',';
        $main_data_arr[$row[csf("job_no")]][$row[csf("deter_id")]]['dia_width'].=$row[csf('dia_width')].',';
        $main_data_arr[$row[csf("job_no")]][$row[csf("deter_id")]]['int_ref'].=$row[csf('int_ref')].',';
        $main_data_arr[$row[csf("job_no")]][$row[csf("deter_id")]]['style_ref_no']=$row[csf('style_ref_no')];
        $main_data_arr[$row[csf("job_no")]][$row[csf("deter_id")]]['buyer_id']=$row[csf('buyer_name')];
        $main_data_arr[$row[csf("job_no")]][$row[csf("deter_id")]]['requQnty']+=$row[csf('grey_fab_qnty')];

        $po_id_arr[$row[csf("id")]] =$row[csf("id")];
    }
    
    fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 100, 1,$po_id_arr, $empty_arr); // po id insert
    oci_commit($con);
    // echo "<pre>";print_r($po_id_arr);die;
    // ============================================= PO End =====================================

    // ============================================= Receive start ==============================
    $sqlRcvRollQty = "SELECT d.job_no_mst, d.grouping as int_ref, d.shipment_date, e.entry_form, e.po_breakdown_id, h.qnty as rcv_qty, h.barcode_no 
    from gbl_temp_engine t, wo_po_break_down d, order_wise_pro_details e, pro_grey_prod_entry_dtls g, pro_roll_details h 
    where d.id = e.po_breakdown_id  and e.dtls_id=g.id  and g.id = h.dtls_id and t.ref_val=d.id and t.user_id=$user_id and t.entry_form=100 and t.ref_from=1 and e.status_active = 1 and e.is_deleted = 0 and e.entry_form in(2,22,58,84) and e.trans_type in(1,4) and e.trans_id > 0 and h.entry_form in(2,22,58,84) and h.is_sales<>1"; 
    // echo $sqlRcvRollQty; die;
    $sqlRcvRollrSlt = sql_select($sqlRcvRollQty);
    foreach($sqlRcvRollrSlt as $row)
    {
        $barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
    }
    // unset($sqlRcvRollRslt);    
    // ============================================= Receive End ==============================

    // ============================================= Transfer in Start ========================
    $trans_query="SELECT d.job_no_mst, d.grouping as int_ref, d.shipment_date, e.entry_form, e.po_breakdown_id, e.trans_type, h.qnty AS roll_rcv_qty, h.barcode_no
    FROM GBL_TEMP_ENGINE t, wo_po_break_down d, order_wise_pro_details e, inv_transaction f, inv_item_transfer_dtls g, pro_roll_details h 
    WHERE  t.REF_VAL=d.id and d.id = e.po_breakdown_id and e.trans_id = f.id and e.dtls_id = g.id and g.id = h.dtls_id and t.user_id=$user_id and t.ENTRY_FORM=100 and t.REF_FROM=1 and e.status_active = 1 AND e.is_deleted = 0 AND e.entry_form in(82,83) and h.entry_form in(82,83) AND e.trans_type IN(5,6) AND f.status_active = 1 AND f.is_deleted = 0 AND g.status_active = 1 AND g.is_deleted = 0 and h.is_sales<>1 AND f.company_id=$company "; 
    // echo $trans_query;die;
    $trans_query_result = sql_select($trans_query);
    foreach($trans_query_result as $row) // Transfered barcode insert into tmp_barcode_no table
    {
        $barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
    }
    // echo "<pre>";print_r($barcode_arr);
    //fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 102, 3,$barcode_arr, $empty_arr); // barcode insert
    //oci_commit($con);
    // ============================================= Transfer in End ========================

    $barcode_arr = array_filter($barcode_arr);
    if(count($barcode_arr ) >0 ) // production
    {
        foreach($barcode_arr as $barcode)
        {
            execute_query("INSERT INTO tmp_barcode_no(barcode_no,userid,entry_form) VALUES(".$barcode.", ".$user_id.",999)");
        }
        oci_commit($con);

        $production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.original_gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
        from pro_grey_prod_entry_dtls a,pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
        where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2) and b.entry_form in(2) and c.receive_basis in(2) and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid=$user_id and d.entry_form=999");

        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] =$row[csf("febric_description_id")];
        }
    }
    // echo "<pre>";print_r($prodBarcodeData);die;
    
    // ====================================== Receive Data array start ===========================
    foreach($sqlRcvRollrSlt as $row) // Receive data array
    {
        $deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];       

        if($row[csf('entry_form')]  == 84)
        {
            //$issueReturnQty += $row[csf('rcv_qty')];
            $issueReturnArr[$row[csf("job_no_mst")]][$deter_id]['issueReturnQty'] += $row[csf('rcv_qty')];
        }
        else
        {
            $rcvQtyArr[$row[csf("job_no_mst")]][$deter_id]['rcvQty'] += $row[csf('rcv_qty')];
            $rcvQtyArr[$row[csf("job_no_mst")]][$deter_id]['program'] .= $prodBarcodeData[$row[csf('barcode_no')]]["prog_book"].',';
        }
    }
    // echo "<pre>";print_r($rcvQtyArr);die;
    // ====================================== Receive Data array end ===========================

    // ====================================== Transfer Data array end ===========================
    foreach($trans_query_result as $row)
    {
        $deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];

        if($row[csf('trans_type')] == 5)
        {
            //$transferInQty += $row[csf('rcv_qty')];
            $transfer_in_qty_arr[$row[csf("job_no_mst")]][$deter_id]['transferInQty'] += $row[csf("roll_rcv_qty")];
        }
        if($row[csf('trans_type')] == 6)
        {
            //$transferOutQty += $row[csf('rcv_qty')];
            $trans_out_qty_arr[$row[csf("job_no_mst")]][$deter_id]['transferOutQty'] += $row[csf("roll_rcv_qty")];
        }
    }
    // ====================================== Transfer Data array end ===========================

    // ==================================== Roll Issue query ====================================
    //===== For Roll Splitting After Issue start ============
    $split_chk_sql = sql_select("SELECT D.BARCODE_NO, D.QNTY 
    from pro_roll_split C, pro_roll_details D, GBL_TEMP_ENGINE E 
    where C.entry_form = 75 and C.split_from_id = D.roll_split_from and C.status_active = 1 and D.status_active = 1 and D.PO_BREAKDOWN_ID=e.REF_VAL and e.user_id=$user_id and e.ENTRY_FORM=100 and e.REF_FROM=1");

    if(!empty($split_chk_sql))
    {
        foreach ($split_chk_sql as $val)
        {
            $split_barcode_arr[$val['BARCODE_NO']]= $val['BARCODE_NO'];
            if ($split_barcode_check[$val['BARCODE_NO']]=="") 
            {
                $split_barcode_check[$val['BARCODE_NO']]=$val['BARCODE_NO'];
                $split_barcode=$val['BARCODE_NO'];
                execute_query("INSERT INTO tmp_barcode_no(barcode_no,userid,entry_form) VALUES(".$split_barcode.", ".$user_id.",777)");
            }
        }
        oci_commit($con);

        $split_ref_sql = sql_select("SELECT A.BARCODE_NO, A.QNTY, A.ROLL_ID, B.BARCODE_NO AS MOTHER_BARCODE 
            from tmp_barcode_no t, pro_roll_details A, pro_roll_details B 
            where t.barcode_no=a.barcode_no and t.userid=$user_id and t.entry_form=777 and A.entry_form = 61 and A.roll_id = B.id and A.status_active =1 and B.status_active=1");
        if(!empty($split_ref_sql))
        {
            foreach ($split_ref_sql as $value)
            {
                $mother_barcode_arr[$value['BARCODE_NO']] = $value['MOTHER_BARCODE'];
            }
        }
    }
    unset($split_chk_sql);
    unset($split_ref_sql);
    // ======== For Roll Splitting After Issue end =========

    $iss_qty_sql="SELECT d.job_no_mst, d.grouping as int_ref, d.shipment_date, c.po_breakdown_id, c.barcode_no, e.transaction_date, c.qnty, c.entry_form
    from gbl_temp_engine f, wo_po_break_down d, pro_roll_details c, inv_grey_fabric_issue_dtls b, inv_transaction e
    where f.ref_val=d.id and f.ref_val=c.po_breakdown_id and c.dtls_id=b.id and b.trans_id=e.id and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.booking_without_order = 0 and e.transaction_type=2 and f.user_id=$user_id and f.entry_form=100 and f.ref_from=1 ";
    // echo $iss_qty_sql;
    $issue_info=sql_select($iss_qty_sql);
    foreach($issue_info as $row)
    {
        $deter_id=$prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"];

        $mother_barcode_no = $mother_barcode_arr[$row[csf("barcode_no")]];
        if($mother_barcode_no != "")
        {
            $deter_id=$prodBarcodeData[$mother_barcode_no]["febric_description_id"];
        }
        $issue_qty_arr[$row[csf("job_no_mst")]][$deter_id]['issueQty'] += $row[csf("qnty")];
    }
    
    // echo "</pre>" print_r($issue_arr); echo "</pre>";
    unset($issue_info);
    // ==================================== Roll Issue query ====================================

    // ===================================== construction and copmposition start ==========================
    $sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent, b.type_id 
    from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b 
    where a.id=b.mst_id and a.status_active!=0 and a.is_deleted!=1  and b.status_active!=0 and b.is_deleted!=1";
    $deter_array=sql_select($sql_deter);
    if(count($deter_array)>0)
    {
        foreach($deter_array as $row )
        {
            if(array_key_exists($row[csf('id')],$composition_arr))
            {
                $composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
            }
            else
            {
                $composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
            }

            $constuction_arr[$row[csf('id')]]=$row[csf('construction')];
        }
    }
    unset($deter_array);
    // ===================================== construction and copmposition end ============================
    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (100,102)");
    execute_query("delete from tmp_barcode_no where userid=$user_id");
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
        <fieldset style="width:1825px;">
        <?
        if(count($main_data_arr)>0)
        {
            ?>
            <div  align="center"> <strong> <? echo $company_arr[$company]; ?> </strong>
            <br>
            </div>
            <?
        }
        else
        {
            echo "<b>Data Not Found</b>";
        }
        ?>
        <div align="left">
            <table class="rpt_table" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <thead>
                    <tr>
                        <th width="30" rowspan="2">SL</th>
                        <!-- <th width="100" rowspan="2">Ship date</th> -->
                        <th width="100" rowspan="2">Ref. No</th>
                        <th width="100" rowspan="2">Job Number</th>
                        <th width="100" rowspan="2">Style</th>
                        <th width="100" rowspan="2">Buyer Name</th>
                        <th width="100" rowspan="2">Body Part </th>
                        <th width="100" rowspan="2">Construction</th>
                        <th width="180" rowspan="2">Composition</th>
                        <th width="60" rowspan="2">Dia</th>
                        <th width="60" rowspan="2">GSM</th>
                        <th width="100" rowspan="2">Req. Qty [KG]</th>

                        <th colspan="5">Receive Details</th>
                        <th colspan="5">Issue Details</th>
                    </tr>
                    <tr>
                        <th width="80">Recv. Qty.</th>
                        <th width="80">Issue Return Qty.</th>
                        <th width="80">Transf. In Qty.</th>
                        <th width="80">Total Recv.</th>
                        <th width="80">Receive Balance</th>

                        <th width="80">Issue Qty.</th>
                        <th width="80">Receive Return Qty.</th>
                        <th width="80">Transf. Out Qty.</th>
                        <th width="80">Total Issue</th>
                        <th width="">Total Stock Qty (KG)</th>
                    </tr>
                </thead>
            </table>
            <div style=" max-height:350px; width:1900px; overflow-y:scroll;" id="scroll_body">
                <table class="rpt_table" id="table_body" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tbody>
                        <?
                        // echo "<pre>";print_r($main_data_arr);
                        $i=1;$k=1;$group_by_arr=array();
                        $gtot_requQnty=$gtot_rcvQty=$gtot_issueReturnQty=$gtot_transferInQty=$gtot_recv=$gtot_recv_balance=$gtot_issue_qty=$gtot_transferOutQty=$gtot_total_issue=$gtot_stock_qty=0;

                        $job_tot_requQnty=$job_tot_rcvQty=$job_tot_issueReturnQty=$job_tot_transferInQty=$job_tot_recv=$job_tot_recv_balance=$job_tot_issue_qty=$job_tot_transferOutQty=$job_tot_total_issue=$job_tot_stock_qty=0;
                        foreach ($main_data_arr as $job_no_key => $job_no_val)
                        {
                            foreach ($job_no_val as $deterId => $row)
                            {
                                $rcvQty=$rcvQtyArr[$job_no_key][$deterId]['rcvQty'];
                                $issueReturnQty=$issueReturnArr[$job_no_key][$deterId]['issueReturnQty'];
                                $transferInQty=$transfer_in_qty_arr[$job_no_key][$deterId]['transferInQty'];
                                $total_recv=$rcvQty+$issueReturnQty+$transferInQty;

                                $issue_qty=$issue_qty_arr[$job_no_key][$deterId]['issueQty'];
                                $transferOutQty=$trans_out_qty_arr[$job_no_key][$deterId]['transferOutQty'];
                                $total_issue=$issue_qty+$transferOutQty;
                                $stock_qty=$total_recv-$total_issue;
                                $stock_qty=number_format($stock_qty,2,".","");
                                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                
                                if($cbo_value_with==1 || ($cbo_value_with==2 && number_format($stock_qty,4) > 0))    
                                {
                                    $int_ref =implode(",",array_filter(array_unique(explode(",", $row['int_ref']))));
                                    $dia =implode(",",array_filter(array_unique(explode(",", $row['dia_width']))));
                                    $gsm =implode(",",array_filter(array_unique(explode(",", $row['gsm_weight']))));

                                    $body_part_id_arr = array_unique(array_filter(explode(",", $row['body_part_id'])));
                                    $body_part_name = "";
                                    foreach ($body_part_id_arr as $bid)
                                    {
                                        $body_part_name .= ($body_part_name =="") ? $body_part[$bid] :  ",". $body_part[$bid];
                                    }
                                    $body_part_name =implode(",",array_filter(array_unique(explode(",", $body_part_name))));

                                    if (!in_array($job_no_key,$group_by_arr) )
                                    {
                                        if($k!=1)
                                        {
                                            ?>  
                                            <tr class="tbl_bottom">
                                                <td width="30"></td>
                                                <td width="100"></td>
                                                <td width="100"></td>
                                                <td width="100"></td>
                                                <td width="100"></td>
                                                <td width="100"></td>
                                                <td width="100"></td>
                                                <td width="180"></td>
                                                <td width="60"></td>
                                                <td width="60" align="right">Job Total:</td>
                                                <td width="100" align="right"><? echo number_format($job_tot_requQnty,2,".",""); ?></td>
                                                <td width="80" align="right"><? echo number_format($job_tot_rcvQty,2,".",""); ?></td>
                                                <td width="80" align="right"><? echo number_format($job_tot_issueReturnQty,2,".",""); ?></td>
                                                <td width="80" align="right"><? echo number_format($job_tot_transferInQty,2,".",""); ?></td>
                                                <td width="80" align="right"><? echo number_format($job_tot_recv,2,".",""); ?></td>
                                                <td width="80" align="right"><? echo number_format($job_tot_recv_balance,2,".",""); ?></td>

                                                <td width="80" align="right"><? echo number_format($job_tot_issue_qty,2,".",""); ?></td>
                                                <td width="80" align="right"></td>
                                                <td width="80" align="right"><? echo number_format($job_tot_transferOutQty,2,".",""); ?></td>
                                                <td width="80" align="right"><? echo number_format($job_tot_total_issue,2,".",""); ?></td>
                                                <td width="" align="right"><? echo number_format($job_tot_stock_qty,2,".",""); ?></td>
                                            </tr>
                                            <?
                                            unset($job_tot_requQnty);unset($job_tot_rcvQty);unset($job_tot_issueReturnQty);unset($job_tot_transferInQty);unset($job_tot_recv);unset($job_tot_recv_balance);unset($job_tot_issue_qty);unset($job_tot_transferOutQty);unset($job_tot_total_issue);unset($job_tot_stock_qty);
                                        }
                                        $group_by_arr[]=$job_no_key; 
                                        $k++; 
                                    }
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                                        <td width="30"><? echo $i; ?></td>
                                        <td width="100" class="word_wrap_break"><? echo $int_ref; ?></td>
                                        <td width="100" class="word_wrap_break"><? echo $job_no_key;?></td>
                                        <td width="100" class="word_wrap_break"><? echo $row['style_ref_no']; ?></td>
                                        <td width="100" class="word_wrap_break" title="<? echo $row['buyer_id']; ?>"><? echo $buyer_arr[$row['buyer_id']]; ?></td>
                                        <td width="100" class="word_wrap_break" title="<? echo $row['body_part_id']; ?>"><? echo $body_part_name;?></td>
                                        <td width="100" class="word_wrap_break" title="<? echo $deterId; ?>"><? echo $constuction_arr[$deterId]; ?></td>
                                        <td width="180" class="word_wrap_break"><? echo $composition_arr[$deterId]; ?></td>
                                        <td width="60" class="word_wrap_break"><? echo $dia; ?></td>
                                        <td width="60" class="word_wrap_break"><? echo $gsm; ?></td>
                                        <td width="100" class="word_wrap_break" align="right"><? echo number_format($row['requQnty'],2,".",""); ?></td>

                                        <td width="80" class="word_wrap_break" align="right"><? echo number_format($rcvQty,2,".",""); ?></td>
                                        <td width="80" class="word_wrap_break" align="right"><? echo number_format($issueReturnQty,2,".",""); ?></td>
                                        <td width="80" class="word_wrap_break" align="right"><? echo number_format($transferInQty,2,".",""); ?></td>
                                        <td width="80" class="word_wrap_break" align="right"><? echo number_format($total_recv,2,".",""); ?></td>
                                        <td width="80" class="word_wrap_break" align="right"><? echo number_format($row['requQnty']-$total_recv,2,".","");?></td>

                                        <td width="80" class="word_wrap_break" align="right"><? echo number_format($issue_qty,2,".",""); ?></td>
                                        <td width="80" class="word_wrap_break" align="right"></td>
                                        <td width="80" class="word_wrap_break" align="right"><? echo number_format($transferOutQty,2,".",""); ?></td>
                                        <td width="80" class="word_wrap_break" align="right"><? echo number_format($total_issue,2,".",""); ?></td>
                                        <td width="" class="word_wrap_break" align="right"><? echo number_format($stock_qty,2,".",""); ?></td>
                                    </tr>
                                    <?
                                    $i++;
                                    $gtot_requQnty+=$row['requQnty'];
                                    $gtot_rcvQty+=$rcvQty;
                                    $gtot_issueReturnQty+=$issueReturnQty;
                                    $gtot_transferInQty+=$transferInQty;
                                    $gtot_recv+=$total_recv;
                                    $gtot_recv_balance+=$row['requQnty']-$total_recv;
                                    $gtot_issue_qty+=$issue_qty;
                                    $gtot_transferOutQty+=$transferOutQty;
                                    $gtot_total_issue+=$total_issue;
                                    $gtot_stock_qty+=$stock_qty;

                                    $job_tot_requQnty+=$row['requQnty'];
                                    $job_tot_rcvQty+=$rcvQty;
                                    $job_tot_issueReturnQty+=$issueReturnQty;
                                    $job_tot_transferInQty+=$transferInQty;
                                    $job_tot_recv+=$total_recv;
                                    $job_tot_recv_balance+=$row['requQnty']-$total_recv;
                                    $job_tot_issue_qty+=$issue_qty;
                                    $job_tot_transferOutQty+=$transferOutQty;
                                    $job_tot_total_issue+=$total_issue;
                                    $job_tot_stock_qty+=$stock_qty;
                                }
                            }
                        }
                        ?>
                        <tr class="tbl_bottom">
                            <td width="30"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="180"></td>
                            <td width="60"></td>
                            <td width="60" align="right">Job Total:</td>
                            <td width="100" align="right"><? echo number_format($job_tot_requQnty,2,".",""); ?></td>
                            <td width="80" align="right"><? echo number_format($job_tot_rcvQty,2,".",""); ?></td>
                            <td width="80" align="right"><? echo number_format($job_tot_issueReturnQty,2,".",""); ?></td>
                            <td width="80" align="right"><? echo number_format($job_tot_transferInQty,2,".",""); ?></td>
                            <td width="80" align="right"><? echo number_format($job_tot_recv,2,".",""); ?></td>
                            <td width="80" align="right"><? echo number_format($job_tot_recv_balance,2,".",""); ?></td>

                            <td width="80" align="right"><? echo number_format($job_tot_issue_qty,2,".",""); ?></td>
                            <td width="80" align="right"></td>
                            <td width="80" align="right"><? echo number_format($job_tot_transferOutQty,2,".",""); ?></td>
                            <td width="80" align="right"><? echo number_format($job_tot_total_issue,2,".",""); ?></td>
                            <td width="" align="right"><? echo number_format($job_tot_stock_qty,2,".",""); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <table class="rpt_table" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tfoot>
                    <tr>
                        <th width="30"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="180"></th>
                        <th width="60"></th>
                        <th width="60" align="right">G. Total:</th>
                        <th width="100" align="right"><? echo number_format($gtot_requQnty,2,".",""); ?></th>
                        <th width="80" align="right"><? echo number_format($gtot_rcvQty,2,".",""); ?></th>
                        <th width="80" align="right"><? echo number_format($gtot_issueReturnQty,2,".",""); ?></th>
                        <th width="80" align="right"><? echo number_format($gtot_transferInQty,2,".",""); ?></th>
                        <th width="80" align="right"><? echo number_format($gtot_recv,2,".",""); ?></th>
                        <th width="80" align="right"><? echo number_format($gtot_recv_balance,2,".",""); ?></th>

                        <th width="80" align="right"><? echo number_format($gtot_issue_qty,2,".",""); ?></th>
                        <th width="80" align="right"></th>
                        <th width="80" align="right"><? echo number_format($gtot_transferOutQty,2,".",""); ?></th>
                        <th width="80" align="right"><? echo number_format($gtot_total_issue,2,".",""); ?></th>
                        <th width="" align="right"><? echo number_format($gtot_stock_qty,2,".",""); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        </fieldset>
    </div>
    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w') or die('Cant open');
    $is_created = fwrite($create_new_doc, $html) or die('Cant open');
    echo "$html##$filename##$report_type";
    exit();
}
?>