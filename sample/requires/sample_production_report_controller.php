<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_name=$_SESSION['logic_erp']['user_id'];
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
$dealing_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0",'id','team_member_name');
$sample_name_arr=return_library_array( "select id,sample_name  from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
$size_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
$season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0","id","season_name");

if ($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );        
    exit();
}
if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=14 and report_id=160 and is_deleted=0 and status_active=1");
    echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
    echo "print_report_button_setting('".$print_report_format."');\n";
    exit(); 
}
if($action=="report_generate")
{ 
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    $company_name       = str_replace("'", "", $cbo_company_name);
    $sample_stage       = str_replace("'", "", $cbo_sample_stage);
    $buyer_name         = str_replace("'", "", $cbo_buyer_name);
    $dealing_merchant   = str_replace("'", "", $cbo_dealing_merchant);
    $sample_year        = str_replace("'", "", $cbo_year); 
    $txt_booking_no     = str_replace("'", "", $txt_booking_no);
    $req_no             = str_replace("'", "", $txt_req_no);
    $style_ref          = str_replace("'", "", $txt_style_ref);
    $group              = str_replace("'", "", $txt_internal_ref);
    $date_from          = str_replace("'", "", $txt_date_from);
    $date_to            = str_replace("'", "", $txt_date_to);
    $type            = str_replace("'", "", $excel_type);

    if($type==0)
    {
            $int_ref_arr=return_library_array( "select booking_no, grouping from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0","booking_no","grouping");

            $year_cond="";
            if($db_type==2)
            {
                $year_cond=($sample_year)? " and  to_char(a.insert_date,'YYYY')=$sample_year" : " ";
            }
            else
            {
                $year_cond=($sample_year)? " and year(a.insert_date)=$sample_year" : " ";
            }

            if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and a.company_id=$cbo_company_name";
            if(str_replace("'","",$cbo_sample_stage)==0) $sample_stages=""; else $sample_stages=" and a.sample_stage_id=$cbo_sample_stage";
            if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
            if(str_replace("'","",trim($cbo_dealing_merchant))==0) $dealing_merchant=""; else $dealing_merchant=" and a.dealing_marchant=$cbo_dealing_merchant";
            if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
            else $txt_date=" and a.requisition_date between $txt_date_from and $txt_date_to";
            if(str_replace("'","",$txt_req_no)=="") $req_no=""; else $req_no=" and a.requisition_number_prefix_num like '%$req_no%' ";
            // =================================== booking ========================
            if(str_replace("'","",$cbo_sample_stage)==1)
            {
                if(str_replace("'","",$txt_booking_no)=="") $booking_no=""; else $booking_no=" and a.quotation_id in(select id from wo_po_details_master where job_no like '%$txt_booking_no%' and company_name=$cbo_company_name)";
            }
            elseif(str_replace("'","",$cbo_sample_stage)==2)
            {
                if(str_replace("'","",$txt_booking_no)=="") $booking_no=""; else $booking_no=" and a.id in(select style_id from wo_non_ord_samp_booking_dtls where booking_no like '%$txt_booking_no%')";
            }
            // ======================================== int ref ==============================
            if(str_replace("'","",$cbo_sample_stage)==1)
            {
                if(str_replace("'","",$txt_internal_ref)=="") $internal_ref=""; else $internal_ref=" and a.quotation_id in(select id from wo_po_details_master where job_no like '%$group%' and company_name=$cbo_company_name)";
            }
            elseif(str_replace("'","",$cbo_sample_stage)==2)
            {
                if(str_replace("'","",$txt_internal_ref)=="") $internal_ref=""; else $internal_ref=" and a.id in(select b.style_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.grouping like '%$group%' and a.booking_no=b.booking_no)";
            }
        
            $fromDate   = change_date_format( str_replace("'","",trim($txt_date_from)) );
            $toDate     = change_date_format( str_replace("'","",trim($txt_date_to)) );
            $style      = str_replace("'", "", $txt_style_ref);
            if($style=='') $style_ref="";else $style_ref=" and a.style_ref_no like '%$style%'";
                        
            if($db_type==0) $yearCond="YEAR(a.insert_date)"; else if($db_type==2) $yearCond="to_char(a.insert_date,'YYYY')";
            
            // =============================================== main qery ================================================         
            $sql="SELECT a.id,a.quotation_id,a.dealing_marchant,a.buyer_ref, a.requisition_number_prefix_num, $yearCond as year, a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.sample_name, b.gmts_item_id, sum(b.sample_prod_qty) as req_qty from sample_development_mst a,sample_development_dtls b where a.id=b.sample_mst_id and a.entry_form_id in(117,203,449) and b.entry_form_id in(117,203,449) and a.status_active=1 and b.is_deleted=0 and b.status_active=1  $company_name $buyer_name $sample_stages $dealing_merchant $booking_no $req_no $job_no $internal_ref $style_ref $file_no $order_no $year_cond group by a.id,a.quotation_id,a.dealing_marchant, a.buyer_ref,a.requisition_number_prefix_num, a.insert_date, a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.sample_name, b.gmts_item_id order by a.requisition_number_prefix_num DESC"; //$txt_date
      
            
            $sql_res=sql_select($sql);
            if(count($sql_res)==0)
            {
                ?>
                <div style="font-weight: bold;color: red;text-align: center;font-size: 20px;">Data Not Found! Please Try Again.</div>
                <?
                die();
            }
            $sample_mst_id_arr = array();
            foreach ($sql_res as $val) 
            {
                $sample_mst_id_arr[$val[csf('id')]] = $val[csf('id')];
            }
            $sample_mst_ids = implode(",", $sample_mst_id_arr);
            if(count($sample_mst_id_arr)>999)
            {
                $chunk_arr=array_chunk($sample_mst_id_arr,999);
                foreach($chunk_arr as $val)
                {
                    $ids=implode(",", $val);
                    if($sample_mst_cond=="") $sample_mst_cond.=" and ( a.sample_development_id in ($ids) ";
                    else
                        $sample_mst_cond.=" or   a.sample_development_id in ($ids) "; 
                }
                $sample_mst_cond.=") ";

            }
            else
            {
                $sample_mst_cond.=" and a.sample_development_id in ($sample_mst_ids) ";
            }

            // ===================================== fabrication ====================================
            $sample_mst_cond_fab = str_replace("a.sample_development_id", "sample_mst_id", $sample_mst_cond);
            $sql = "SELECT SAMPLE_MST_ID,GMTS_ITEM_ID,FABRIC_DESCRIPTION from SAMPLE_DEVELOPMENT_FABRIC_ACC where status_active=1 and is_deleted=0 $sample_mst_cond_fab";
            $res = sql_select($sql);
            $fab_data_array = array();
            foreach ($res as  $val) 
            {
                $fab_data_array[$val['SAMPLE_MST_ID']][$val['GMTS_ITEM_ID']] = $val['FABRIC_DESCRIPTION'];
            }

            // ======================================= production qnty =======================================
            $prod_date_cond = str_replace("a.requisition_date", "b.sewing_date", $txt_date);
            $prod_sql="SELECT  a.sample_development_id as id, b.sample_name, b.item_number_id as item_id, b.entry_form_id, sum(b.qc_pass_qty) AS qc_pass_qty, sum(b.reject_qty) AS reject from sample_sewing_output_mst a, sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and b.entry_form_id in (127,128,130,131,337,338) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name $prod_date_cond $sample_mst_cond group by  a.sample_development_id, b.sample_name, b.item_number_id, b.entry_form_id"; 
            // echo $prod_sql;die();
            $prod_sql_res = sql_select($prod_sql);
            $production_data = array();
            foreach($prod_sql_res as $val)
            {
                $production_data[$val[csf("id")]][$val[csf("sample_name")]][$val[csf("item_id")]][$val[csf("entry_form_id")]]['good_qty'] = $val[csf("qc_pass_qty")];
                $production_data[$val[csf("id")]][$val[csf("sample_name")]][$val[csf("item_id")]][$val[csf("entry_form_id")]]['rej_qty'] = $val[csf("reject")];
            }
            // echo "<pre>";print_r($production_data);die;
            
            // ================================== delivery =====================================
            $del_date_cond = str_replace("a.requisition_date", "b.delivery_date", $txt_date);
            $sample_mst_ex_cond = str_replace("a.sample_development_id", "b.sample_development_id", $sample_mst_cond);
            $delv_qty_arr=array(); 
            $delv_qty_mkt_arr=array(); 
            
            $delv_qty_sql=sql_select("SELECT b.sample_development_id, b.sample_name, b.gmts_item_id, sum(b.ex_factory_qty) as qc_pass_qty from sample_ex_factory_mst a, sample_ex_factory_dtls b where a.id=b.sample_ex_factory_mst_id and  a.entry_form_id in(132) and b.entry_form_id in(132) and a.status_active=1 and b.status_active=1 $sample_mst_ex_cond $del_date_cond group by  b.sample_development_id, b.sample_name, b.gmts_item_id ");

            foreach ($delv_qty_sql as  $result)
            {
               $delv_qty_arr[$result[csf('sample_development_id')]][$result[csf('sample_name')]][$result[csf('gmts_item_id')]]=$result[csf('qc_pass_qty')];
            }

            $delv_qty_sql=sql_select("SELECT b.sample_development_id, b.sample_name, b.gmts_item_id, sum(b.ex_factory_qty) as qc_pass_qty from sample_ex_factory_mst a, sample_ex_factory_dtls b where a.id=b.sample_ex_factory_mst_id and  a.entry_form_id in(396) and b.entry_form_id in(396) and a.status_active=1 and b.status_active=1 $sample_mst_ex_cond $del_date_cond group by  b.sample_development_id, b.sample_name, b.gmts_item_id ");

            foreach ($delv_qty_sql as  $result)
            {
               $delv_qty_mkt_arr[$result[csf('sample_development_id')]][$result[csf('sample_name')]][$result[csf('gmts_item_id')]]=$result[csf('qc_pass_qty')];
            }

            // ====================================== booking =================================
            $booking_sql=sql_select("SELECT a.id,b.booking_no from wo_po_details_master a,wo_booking_mst b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
            foreach($booking_sql as $booking_value)
            {
                $booking_arr[$booking_value[csf("id")]]=$booking_value[csf("booking_no")];
            }

            $booking_without_order_sql=sql_select("SELECT b.style_id,a.booking_no from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1  group by  b.style_id,a.booking_no");
            foreach($booking_without_order_sql as $vals)
            {
                $booking_without_order_arr[$vals[csf("style_id")]]=$vals[csf("booking_no")];
            }

            // ==================================== fin fab rcv ===================================
            // $rcv = "SELECT a.booking_no,b. from inv_receive_master a, inv_transaction b where a.id=b.mst_id and "
            
            ob_start();
            ?>
            <div style="padding: 10px; width: 1710px;">
                <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1690" rules="all" id="table_header" align="left">
                    <thead>
                        <tr>
                            <th width="30">SL </th>
                            <th width="95">Requisition No</th>
                            <th width="110">Booking No</th>
                            <th width="45">Year</th>
                            <th width="90">File No.</th>
                            <th width="90">Int. Ref.</th>
                            <th width="100">Buyer</th>
                            <th width="90">Style Ref.</th>
                            <th width="110">Sample Name</th>
                            <th width="110">Garments Item</th>
                            <th width="80">Fab. Rcv Qty</th>
                            <th width="80">Issue Qty</th>
                            <th width="110">Fabrication</th>
                            <th width="70">Season</th>
                            <th width="80">Req. Qty.</th>
                            <th width="80">Cutting Qty.</th>
                            <th width="80">Embl. Qty.</th>
                            <th width="80">Sewing Qty.</th>
                            <th width="80">Rej. Qty</th>
                            <th width="80">Delivery To MKT.</th>
                            <th width="80">Delivery Qty.</th>
                        </tr>
                    </thead>
                </table>
                <div style="max-height:320px; overflow-y:auto; width:1710px; float: left;" id="scroll_body">
                    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1690" rules="all" id="table_body" align="left"> 
                        <tbody>
                        <?
                        
                        $i=1;
                        $gr_req_qty = 0;
                        $gr_cut_qty = 0;
                        $gr_emb_qty = 0;
                        $gr_sew_qty = 0;
                        $gr_rej_qty = 0;
                        $gr_del_qty = 0;
                        foreach ($sql_res as $key => $value) 
                        {
                            $cutting_qty     = $production_data[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][127]['good_qty'];
                            $cutting_rej_qty = $production_data[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][127]['rej_qty'];

                            $embl_qty       = $production_data[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][128]['good_qty'];
                            $embl_rej_qty   = $production_data[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][128]['rej_qty'];

                            $sewing_qty     = $production_data[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][130]['good_qty'];
                            $sewing_rej_qty = $production_data[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][130]['rej_qty'];
                            $total_rej      = $cutting_rej_qty + $embl_rej_qty + $sewing_rej_qty;

                            $delivery_qty   = $delv_qty_arr[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];
                            $delv_qty_mkt = $delv_qty_mkt_arr[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];
                            if($cutting_qty !=0 || $embl_qty !=0 || $sewing_qty !=0 || $delivery_qty !=0 || $delv_qty_mkt !=0)
                            {
                                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                    <td width="30" align="center"><? echo $i; ?></td>
                                    <td width="95" align="center"><? echo  $value[csf('requisition_number_prefix_num')]; ?></td>
                                    <td width="110" align="center">
                                        <? 
                                        if($value[csf('sample_stage_id')]==1) echo $booking_arr[$value[csf("quotation_id")]];  
                                        else echo $booking_without_order_arr[$value[csf("id")]];
                                        ?>
                                    </td>
                                    <td width="45" align="center"><? echo  $value[csf('year')] ; ?></td>
                                    <td width="90"><?= $value[csf("buyer_ref")];?></td>
                                    <td width="90">
                                        <? 
                                        if($value[csf('sample_stage_id')]==1) echo $booking_arr[$value[csf("buyer_ref")]];  
                                        else echo $int_ref_arr[$booking_without_order_arr[$value[csf("id")]]];
                                        ?>                          
                                    </td>
                                    <td width="100"><p><? echo $buyer_arr[$value[csf('buyer_name')]]; ?></p></td>
                                    <td width="90"><p><? echo  $value[csf('style_ref_no')] ; ?></p></td>
                                    <td width="110"><p><? echo $sample_name_arr[$value[csf('sample_name')]]; ?></p></td>
                                    <td width="110"><p><? echo $garments_item[$value[csf('gmts_item_id')]]; ?></p></td>
                                    <td width="80"><p><? //echo $garments_item[$value[csf('gmts_item_id')]]; ?></p></td>
                                    <td width="80"><p><? //echo $garments_item[$value[csf('gmts_item_id')]]; ?></p></td>
                                    <td width="110"><p><? echo $fab_data_array[$value[csf('id')]][$value[csf('gmts_item_id')]]; ?></p></td>
                                    <td width="70"><? echo $season_arr[$value[csf('season')]]; ?></td>
                                    <td align="right" width="80">
                                        <a href="javascript:void()" onclick="open_popup('<? echo $value[csf('id')]; ?>_<? echo $value[csf('sample_name')]; ?>_<? echo $value[csf('gmts_item_id')]; ?>','Req Qnty Popup','req_qty_popup')">
                                            <? echo number_format($value[csf('req_qty')],0); ?>
                                        </a>
                                    </td>
                                    <td align="right" width="80">
                                        <a href="javascript:void()" onclick="open_popup('<? echo $value[csf('id')]; ?>_<? echo $value[csf('sample_name')]; ?>_<? echo $value[csf('gmts_item_id')]; ?>_<? echo $date_from;?>_<? echo $date_to;?>','Cutting Qnty Popup','cut_qty_popup')">
                                            <? echo number_format($cutting_qty,0); ?>
                                        </a>
                                    </td>
                                    <td align="right" width="80">
                                        <a href="javascript:void()" onclick="open_popup('<? echo $value[csf('id')];?>_<? echo $value[csf('sample_name')]; ?>_<? echo $value[csf('gmts_item_id')]; ?>_<? echo $date_from;?>_<? echo $date_to;?>','Embl Qnty Popup','embl_qty_popup')">
                                            <? echo number_format($embl_qty,0); ?>
                                        </a>
                                    </td>
                                    <td align="right" width="80">
                                        <a href="javascript:void()" onclick="open_popup('<? echo $value[csf('id')]; ?>_<? echo $value[csf('sample_name')]; ?>_<? echo $value[csf('gmts_item_id')]; ?>_<? echo $date_from;?>_<? echo $date_to;?>','Sewing Qnty Popup','sew_qty_popup')">
                                            <? echo number_format($sewing_qty,0); ?>
                                        </a>
                                    </td>
                                    <td align="right" width="80">
                                        <a href="javascript:void()" onclick="open_popup('<? echo $value[csf('id')]; ?>_<? echo $value[csf('sample_name')]; ?>_<? echo $value[csf('gmts_item_id')]; ?>_<? echo $date_from;?>_<? echo $date_to;?>','Reject Qnty Popup','rej_qty_popup')">
                                            <? echo number_format($total_rej,0); ?>
                                        </a>
                                    </td>
                                    <td align="right" width="80">
                                        <a href="javascript:void()" onclick="open_popup('<? echo $value[csf('id')]; ?>_<? echo $value[csf('sample_name')]; ?>_<? echo $value[csf('gmts_item_id')]; ?>_<? echo $date_from;?>_<? echo $date_to;?>','Delivery Qnty Popup','del_mkt_qty_popup')">
                                            <? echo number_format($delv_qty_mkt,0); ?>
                                        </a>
                                    </td>    
                                    <td align="right" width="80">
                                        <a href="javascript:void()" onclick="open_popup('<? echo $value[csf('id')]; ?>_<? echo $value[csf('sample_name')]; ?>_<? echo $value[csf('gmts_item_id')]; ?>_<? echo $date_from;?>_<? echo $date_to;?>','Delivery Qnty Popup','del_qty_popup')">
                                            <? echo number_format($delivery_qty,0); ?>
                                        </a>
                                    </td>                                  
                                </tr>
                                <?
                                $i++;
                                $gr_req_qty += $value[csf('req_qty')];
                                $gr_cut_qty += $cutting_qty;
                                $gr_emb_qty += $embl_qty;
                                $gr_sew_qty += $sewing_qty;
                                $gr_rej_qty += $total_rej;
                                $gr_del_qty += $delivery_qty;
                                $del_qty_mkt += $delv_qty_mkt;
                            }
                        }   
                        ?>
                        </tbody>        
                    </table>
                </div>
                <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1690" rules="all" id="table_bottom" align="left"> 
                    <tfoot>
                        <tr>
                            <th width="30"></th>
                            <th width="95"></th>
                            <th width="110"></th>
                            <th width="45"></th>
                            <th width="90"></th>
                            <th width="90"></th>
                            <th width="100"></th>
                            <th width="90"></th>
                            <th width="110"></th>
                            <th width="110"></th>
                            <th width="80" align="right"><? echo number_format($gr_fab_rcv_qty,0); ?></th>
                            <th width="80" align="right"><? echo number_format($gr_fab_issue_qty,0); ?></th>
                            <th width="110"></th>
                            <th width="70"></th>
                            <th width="80" align="right"><? echo number_format($gr_req_qty,0); ?></th>
                            <th width="80" align="right"><? echo number_format($gr_cut_qty,0); ?></th>
                            <th width="80" align="right"><? echo number_format($gr_emb_qty,0); ?></th>
                            <th width="80" align="right"><? echo number_format($gr_sew_qty,0); ?></th>
                            <th width="80" align="right"><? echo number_format($gr_rej_qty,0); ?></th>
                            <th width="80" align="right"><? echo number_format($del_qty_mkt,0); ?></th>
                            <th width="80" align="right"><? echo number_format($gr_del_qty,0); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?
    }
    else if($type==1)
    {
            $int_ref_arr=return_library_array( "select booking_no, grouping from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0","booking_no","grouping");

            $year_cond="";
            if($db_type==2)
            {
                $year_cond=($sample_year)? " and  to_char(a.insert_date,'YYYY')=$sample_year" : " ";
            }
            else
            {
                $year_cond=($sample_year)? " and year(a.insert_date)=$sample_year" : " ";
            }

            if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and a.company_id=$cbo_company_name";
            if(str_replace("'","",$cbo_sample_stage)==0) $sample_stages=""; else $sample_stages=" and a.sample_stage_id=$cbo_sample_stage";
            if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
            if(str_replace("'","",trim($cbo_dealing_merchant))==0) $dealing_merchant=""; else $dealing_merchant=" and a.dealing_marchant=$cbo_dealing_merchant";
            if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
            else $txt_date=" and a.requisition_date between $txt_date_from and $txt_date_to";
            if(str_replace("'","",$txt_req_no)=="") $req_no=""; else $req_no=" and a.requisition_number_prefix_num like '%$req_no%' ";
            // =================================== booking ========================
            if(str_replace("'","",$cbo_sample_stage)==1)
            {
                if(str_replace("'","",$txt_booking_no)=="") $booking_no=""; else $booking_no=" and a.quotation_id in(select id from wo_po_details_master where job_no like '%$txt_booking_no%' and company_name=$cbo_company_name)";
            }
            elseif(str_replace("'","",$cbo_sample_stage)==2)
            {
                if(str_replace("'","",$txt_booking_no)=="") $booking_no=""; else $booking_no=" and a.id in(select style_id from wo_non_ord_samp_booking_dtls where booking_no like '%$txt_booking_no%')";
            }
            // ======================================== int ref ==============================
            if(str_replace("'","",$cbo_sample_stage)==1)
            {
                if(str_replace("'","",$txt_internal_ref)=="") $internal_ref=""; else $internal_ref=" and a.quotation_id in(select id from wo_po_details_master where job_no like '%$group%' and company_name=$cbo_company_name)";
            }
            elseif(str_replace("'","",$cbo_sample_stage)==2)
            {
                if(str_replace("'","",$txt_internal_ref)=="") $internal_ref=""; else $internal_ref=" and a.id in(select b.style_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.grouping like '%$group%' and a.booking_no=b.booking_no)";
            }
            
            // echo $booking_no;
            // if(str_replace("'","",$txt_internal_ref)=="") $internal_ref=""; else $internal_ref=" and a.quotation_id in(select a.id from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.grouping like '%$group%'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name)";    
            // if(str_replace("'","",$txt_internal_ref)=="") $internal_ref=""; else $internal_ref=" and a.buyer_ref like '%$group%' ";    

            $fromDate   = change_date_format( str_replace("'","",trim($txt_date_from)) );
            $toDate     = change_date_format( str_replace("'","",trim($txt_date_to)) );
            $style      = str_replace("'", "", $txt_style_ref);
            if($style=='') $style_ref="";else $style_ref=" and a.style_ref_no like '%$style%'";
                        
            if($db_type==0) $yearCond="YEAR(a.insert_date)"; else if($db_type==2) $yearCond="to_char(a.insert_date,'YYYY')";
            
            // =============================================== main qery ================================================         
           $sql="SELECT a.id,a.quotation_id,a.dealing_marchant,a.buyer_ref, a.requisition_number_prefix_num, $yearCond as year, a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.sample_name, b.gmts_item_id, (b.sample_prod_qty) as req_qty,c.body_part_id,c.gsm,c.determination_id,(c.grey_fab_qnty) as grey_fab_qnty,c.fabric_description,b.id as b_id, c.id as c_id from sample_development_mst a,sample_development_dtls b,sample_development_fabric_acc c where a.id=b.sample_mst_id and a.id=c.sample_mst_id  and c.sample_name=b.sample_name and c.gmts_item_id=b.gmts_item_id and a.entry_form_id in(117,203,449) and b.entry_form_id in(117,203,449) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.body_part_id in (1,10,11,20,100,109,390) $company_name $buyer_name $sample_stages $dealing_merchant $booking_no $req_no $job_no $internal_ref $style_ref $file_no $order_no $year_cond  order by a.requisition_number_prefix_num DESC"; //$txt_date
             //echo $sql;
            
            $sql_res=sql_select($sql);
            if(count($sql_res)==0)
            {
                ?>
                <div style="font-weight: bold;color: red;text-align: center;font-size: 20px;">Data Not Found! Please Try Again.</div>
                <?
                die();
            }
            $sample_mst_id_arr = array();
            $sample_grouping_data=array();
            $yarn_count_id_arr=array();
            foreach ($sql_res as $val) 
            {
                $sample_mst_id_arr[$val[csf('id')]] = $val[csf('id')];
                if(!empty($val[csf('determination_id')]))
                {
                    array_push($yarn_count_id_arr, $val[csf('determination_id')]);
                }
               
            }
            $sample_mst_ids = implode(",", $sample_mst_id_arr);
            if(count($sample_mst_id_arr)>999)
            {
                $chunk_arr=array_chunk($sample_mst_id_arr,999);
                foreach($chunk_arr as $val)
                {
                    $ids=implode(",", $val);
                    if($sample_mst_cond=="") $sample_mst_cond.=" and ( a.sample_development_id in ($ids) ";
                    else
                        $sample_mst_cond.=" or   a.sample_development_id in ($ids) "; 
                }
                $sample_mst_cond.=") ";

            }
            else
            {
                $sample_mst_cond.=" and a.sample_development_id in ($sample_mst_ids) ";
            }

            // ===================================== fabrication ====================================
            $sample_mst_cond_fab = str_replace("a.sample_development_id", "sample_mst_id", $sample_mst_cond);
            $sql = "SELECT SAMPLE_MST_ID,GMTS_ITEM_ID,FABRIC_DESCRIPTION from SAMPLE_DEVELOPMENT_FABRIC_ACC where status_active=1 and is_deleted=0 $sample_mst_cond_fab";
            $res = sql_select($sql);
            $fab_data_array = array();
            foreach ($res as  $val) 
            {
                $fab_data_array[$val['SAMPLE_MST_ID']][$val['GMTS_ITEM_ID']] = $val['FABRIC_DESCRIPTION'];
            }

            // ======================================= production qnty =======================================
            $prod_date_cond = str_replace("a.requisition_date", "b.sewing_date", $txt_date);
            $prod_sql="SELECT  a.sample_development_id as id, b.sample_name, b.item_number_id as item_id, b.entry_form_id, sum(b.qc_pass_qty) AS qc_pass_qty, sum(b.reject_qty) AS reject from sample_sewing_output_mst a, sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and b.entry_form_id in (127,128,130,131,337,338) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name $prod_date_cond $sample_mst_cond group by  a.sample_development_id, b.sample_name, b.item_number_id, b.entry_form_id"; 
            // echo $prod_sql;die();
            $prod_sql_res = sql_select($prod_sql);
            $production_data = array();
            foreach($prod_sql_res as $val)
            {
                $production_data[$val[csf("id")]][$val[csf("sample_name")]][$val[csf("item_id")]][$val[csf("entry_form_id")]]['good_qty'] = $val[csf("qc_pass_qty")];
                $production_data[$val[csf("id")]][$val[csf("sample_name")]][$val[csf("item_id")]][$val[csf("entry_form_id")]]['rej_qty'] = $val[csf("reject")];
            }
            // echo "<pre>";print_r($production_data);die;
            
            // ================================== delivery =====================================
            $del_date_cond = str_replace("a.requisition_date", "b.delivery_date", $txt_date);
            $sample_mst_ex_cond = str_replace("a.sample_development_id", "b.sample_development_id", $sample_mst_cond);
            $delv_qty_arr=array(); 
            $delv_qty_sql=sql_select("SELECT b.sample_development_id, b.sample_name, b.gmts_item_id, sum(b.ex_factory_qty) as qc_pass_qty from sample_ex_factory_mst a, sample_ex_factory_dtls b where a.id=b.sample_ex_factory_mst_id and  a.entry_form_id in( 132,396) and b.entry_form_id in( 132,396) and a.status_active=1 and b.status_active=1 $sample_mst_ex_cond $del_date_cond group by  b.sample_development_id, b.sample_name, b.gmts_item_id ");

            foreach ($delv_qty_sql as  $result)
            {
               $delv_qty_arr[$result[csf('sample_development_id')]][$result[csf('sample_name')]][$result[csf('gmts_item_id')]]=$result[csf('qc_pass_qty')];
            }
            // ====================================== booking =================================
            $booking_sql=sql_select("SELECT a.id,b.booking_no from wo_po_details_master a,wo_booking_mst b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
            foreach($booking_sql as $booking_value)
            {
                $booking_arr[$booking_value[csf("id")]]=$booking_value[csf("booking_no")];
            }

            $booking_without_order_sql=sql_select("SELECT b.style_id,a.booking_no from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1  group by  b.style_id,a.booking_no");
            foreach($booking_without_order_sql as $vals)
            {
                $booking_without_order_arr[$vals[csf("style_id")]]=$vals[csf("booking_no")];
            }

            // ==================================== fin fab rcv ===================================
            // $rcv = "SELECT a.booking_no,b. from inv_receive_master a, inv_transaction b where a.id=b.mst_id and "
             $yarn_count_id_cond=where_con_using_array(array_unique($yarn_count_id_arr),0,"a.id");
             $count_sql="SELECT a.id,listagg(cast(c.yarn_count as varchar2(4000)),',') within group (order by a.id) AS yarn_count
                        FROM lib_yarn_count_determina_mst a,
                             lib_yarn_count_determina_dtls b,
                             lib_yarn_count c
                       WHERE     a.id = b.mst_id
                             AND b.count_id = c.id
                             AND a.status_active = 1
                             AND a.is_deleted = 0
                             AND b.status_active = 1
                             AND b.is_deleted = 0
                             AND c.status_active = 1
                             AND c.is_deleted = 0
                             $yarn_count_id_cond
                    GROUP BY  a.id";
            //echo $count_sql;
            $count_res=sql_select($count_sql);
            $count_data=array();
            foreach ($count_res as $row) 
            {
                $count_data[$row[csf('id')]]=$row[csf('yarn_count')];
            }
            $b_id_arr=array();
            $c_id_arr=array();
            $other_id_arr=array();
            foreach ($sql_res as $val) 
            {
                $sample_mst_id_arr[$val[csf('id')]] = $val[csf('id')];
                $grouping_item=$val[csf('id')]."***".$val[csf('requisition_number_prefix_num')]."***".$val[csf('style_ref_no')]."***".$val[csf('sample_name')]."***".$val[csf('gmts_item_id')]."***".$val[csf('body_part_id')];
               
                $booking_txt='';
                 if($val[csf('sample_stage_id')]==1) $booking_txt= $booking_arr[$val[csf("quotation_id")]];  
                 else  $booking_txt= $booking_without_order_arr[$val[csf("id")]];

                 $internal_ref_txt='';
                if($val[csf('sample_stage_id')]==1) $internal_ref_txt= $booking_arr[$val[csf("buyer_ref")]];  
                else $internal_ref_txt= $int_ref_arr[$booking_without_order_arr[$val[csf("id")]]];

                $sample_grouping_data[$grouping_item]['booking_no'].=$booking_txt."***";
                $sample_grouping_data[$grouping_item]['determination_id'].=$val[csf('determination_id')]."***";
                $sample_grouping_data[$grouping_item]['count'].=$count_data[$val[csf('determination_id')]]."***";
                $sample_grouping_data[$grouping_item]['buyer_ref'].=$val[csf("buyer_ref")]."***";
                $sample_grouping_data[$grouping_item]['internal_ref'].=$internal_ref_txt."***";
                $sample_grouping_data[$grouping_item]['buyer_name'].=$buyer_arr[$val[csf('buyer_name')]]."***";
                $sample_grouping_data[$grouping_item]['season'].=$season_arr[$val[csf('season')]]."***";
                $sample_grouping_data[$grouping_item]['fabric_description'].=$val[csf('fabric_description')]."***";
                $sample_grouping_data[$grouping_item]['gsm'].=$val[csf('gsm')]."***";
                if (!in_array($val[csf('b_id')], $b_id_arr))
                {
                    $sample_grouping_data[$grouping_item]['req_qty']+=$val[csf('req_qty')];
                }
                if (!in_array($val[csf('c_id')], $c_id_arr))
                {
                     $sample_grouping_data[$grouping_item]['grey_fab_qnty']+=$val[csf('grey_fab_qnty')];
                }
                $other_id=$val[csf('id')]."***".$val[csf('sample_name')]."***".$val[csf('gmts_item_id')];
                if (!in_array($other_id, $other_id_arr))
                {
                    $sample_grouping_data[$grouping_item]['delivery_qty']+=$delv_qty_arr[$val[csf('id')]][$val[csf('sample_name')]][$val[csf('gmts_item_id')]];
                    $sample_grouping_data[$grouping_item]['sewing_qty']+= $production_data[$val[csf('id')]][$val[csf('sample_name')]][$val[csf('gmts_item_id')]][130]['good_qty'];
                }
               
               
                array_push($b_id_arr, $val[csf('b_id')]);
                array_push($c_id_arr, $val[csf('c_id')]);
                array_push($other_id_arr, $other_id);
               

            }
            unset($b_id_arr);
            unset($c_id_arr);
            unset($other_id_arr);
           
            ob_start();
            ?>
            <div style="padding: 10px; width: 1510px;">
                <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1490" rules="all" id="table_header" align="left">
                    <thead>
                        <tr>
                            <th style="word-break: break-all;" width="30">SL </th>
                            <th style="word-break: break-all;" width="95">Requisition No</th>
                            <th style="word-break: break-all;" width="110">Booking No</th>
                            <th style="word-break: break-all;" width="90">File No.</th>
                            <th style="word-break: break-all;" width="90">Int. Ref.</th>
                            <th style="word-break: break-all;" width="100">Buyer</th>
                            <th style="word-break: break-all;" width="90">Style Ref.</th>
                            <th style="word-break: break-all;" width="110">Sample Name</th>
                            <th style="word-break: break-all;" width="110">Garments Item</th>
                            <th style="word-break: break-all;" width="110">Fabrication</th>
                            <th style="word-break: break-all;" width="90">GSM</th>
                            <th style="word-break: break-all;" width="120">Count </th>
                            <th style="word-break: break-all;" width="80">Req. G.Fab.<br>Qty</th>
                            <th style="word-break: break-all;" width="80">Req. Qty.</th>
                            <th style="word-break: break-all;" width="80">Sewing Qty.</th>
                            <th style="word-break: break-all;" width="80">Delivery Qty.</th>
                        </tr>
                    </thead>
                </table>
                <div style="max-height:320px; overflow-y:auto; width:1510px; float: left;" id="scroll_body">
                    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1490" rules="all" id="table_body" align="left"> 
                        <tbody>
                        <?
                        
                        $i=1;
                        $gr_req_qty = 0;
                        $gr_cut_qty = 0;
                        $gr_emb_qty = 0;
                        $gr_sew_qty = 0;
                        $gr_rej_qty = 0;
                        $gr_del_qty = 0;
                        foreach ($sample_grouping_data as $grouping_item => $value) 
                        {
                            
                            if($value['sewing_qty'] !=0  || $value['delivery_qty'] !=0 )
                            {
                                $grouping_data=explode("***", $grouping_item);
                               
                                $id=$grouping_data[0];
                                $requisition_number_prefix_num=$grouping_data[1];
                                $style_ref_no=$grouping_data[2];
                                $sample_name=$grouping_data[3];
                                $gmts_item_id=$grouping_data[4];
                                $body_part_id=$grouping_data[5];

                                $determination_id_arr=array_unique(explode("***", chop($value['determination_id'],"***")));
                                
                                
                                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                ?>


                         
                           
                         
                            
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                    <td style="word-break: break-all;"  width="30" align="center"><? echo $i; ?></td>
                                    <td style="word-break: break-all;"  width="95" align="center"><? echo  $requisition_number_prefix_num; ?></td>
                                    <td style="word-break: break-all;"  width="110" align="center">
                                        <? 
                                        echo implode(", ", array_unique(explode("***", chop($value['booking_no'],"***"))));
                                        ?>
                                    </td>
                                  
                                    <td style="word-break: break-all;"  width="90"><?= implode(", ", array_unique(explode("***", chop($value['buyer_ref'],"***"))));?></td>
                                    <td style="word-break: break-all;"  width="90">
                                        <? 

                                        echo implode(", ", array_unique(explode("***", chop($value['internal_ref'],"***"))));
                                        ?>                          
                                    </td>
                                    <td style="word-break: break-all;"  width="100"><p><?  echo implode(", ", array_unique(explode("***", chop($value['buyer_name'],"***")))); ?></p></td>
                                    <td style="word-break: break-all;"  width="90"><p><? echo  $style_ref_no ; ?></p></td>
                                    <td style="word-break: break-all;"  width="110"><p><? echo $sample_name_arr[$sample_name]; ?></p></td>
                                    <td style="word-break: break-all;"  width="110"><p><? echo $garments_item[$gmts_item_id]; ?></p></td>
                                    <td style="word-break: break-all;"  width="110"><p><? echo implode(", ", array_unique(explode("***", chop($value['fabric_description'],"***")))); ?></p></td>
                                    <td style="word-break: break-all;"  width="90"><p><? echo implode(", ", array_unique(explode("***", chop($value['gsm'],"***"))));; ?></p></td>
                                    <td style="word-break: break-all;"  width="120"><p><? echo implode(", ", array_unique(explode("***", chop($value['count'],"***")))); ?></p></td>
                                    <td style="word-break: break-all;"  width="80" align="right"><p><? echo number_format($value['grey_fab_qnty'],2); ?></p></td>
                                  
                                    <td style="word-break: break-all;"  align="right" width="80">
                                        <a href="javascript:void()" onclick="open_popup('<? echo $id; ?>_<? echo $sample_name; ?>_<? echo $gmts_item_id; ?>','Req Qnty Popup','req_qty_popup')">
                                            <? echo number_format($value['req_qty'],0); ?>
                                        </a>
                                    </td>
                                   
                                    
                                    <td style="word-break: break-all;"  align="right" width="80">
                                        <a href="javascript:void()" onclick="open_popup('<? echo $id; ?>_<? echo $sample_name; ?>_<? echo $gmts_item_id; ?>_<? echo $date_from;?>_<? echo $date_to;?>','Sewing Qnty Popup','sew_qty_popup')">
                                            <? echo number_format($value['sewing_qty'],0); ?>
                                        </a>
                                    </td>
                                    
                                    <td style="word-break: break-all;"  align="right" width="80">
                                        <a href="javascript:void()" onclick="open_popup('<? echo $id; ?>_<? echo $sample_name; ?>_<? echo $gmts_item_id; ?>_<? echo $date_from;?>_<? echo $date_to;?>','Delivery Qnty Popup','del_qty_popup')">
                                            <? echo number_format($value['delivery_qty'],0); ?>
                                        </a>
                                    </td>   
                                </tr>
                                <?
                                $i++;
                                $gr_req_qty += $value['req_qty'];
                                $grey_fab_qnty += $value['grey_fab_qnty'];
                                $gr_sew_qty += $value['sewing_qty'];
                                $gr_del_qty += $value['delivery_qty'];
                            }
                            
                        }   
                        ?>
                        </tbody>        
                    </table>
                </div>
                <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1490" rules="all" id="table_bottom" align="left"> 
                    <tfoot>
                        
                        <tr>
                            <th width="30"></th>
                            <th width="95"></th>
                            <th width="110"></th>
                            <th width="90"></th>
                            <th width="90"></th>
                            <th width="100"></th>
                            <th width="90"></th>
                            <th width="110"></th>
                            <th width="110"></th>
                            <th width="110"></th>
                            <th width="90"></th>
                            <th width="120"></th>
                            <th width="80" align="right"><? echo number_format($grey_fab_qnty,0); ?></th>
                            <th width="80" align="right"><? echo number_format($gr_req_qty,0); ?></th>
                            <th width="80" align="right"><? echo number_format($gr_sew_qty,0); ?></th>
                            <th width="80" align="right"><? echo number_format($gr_del_qty,0); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
           <?
    }

   
    $html = ob_get_contents();
    ob_clean(); 
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w') or die('can not open');    
    $is_created = fwrite($create_new_doc,$html) or die('can not write');
    echo "$html****$filename";
    exit();
}

if($action=='req_qty_popup')   
{   
    extract($_REQUEST); 
    echo load_html_head_contents("Sample Prod. Qty", "../../", 1, 1,$unicode,'','');
    $data_ex = explode("_", $data);
    $mst_id = $data_ex[0];
    $smpl_id = $data_ex[1];
    $itm_id = $data_ex[2];

    $sql= "SELECT a.sample_name, a.sample_color,b.size_id,sum(b.bh_qty+b.plan_qty +b.dyeing_qty+ b.test_qty +b.self_qty) as total_qty from sample_development_dtls a,sample_development_size b where a.sample_mst_id=b.mst_id and a.id=b.dtls_id and a.entry_form_id in(117,449,203) and  a.status_active=1 and a.is_deleted=0 and a.sample_mst_id=$mst_id and a.sample_name=$smpl_id and a.gmts_item_id=$itm_id group by a.sample_name, a.sample_color,b.size_id order by b.size_id"; //and a.entry_form_id in(117,203)
     //echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("sample_color")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("total_qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("sample_name")]][$val[csf("sample_color")]] = $val[csf("sample_color")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 290+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Sample Req. Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $sample_id => $color_data) 
            {
                foreach ($color_data as $color_id => $row) 
                {    
                    $total_sizeqnty=0;            
                    ?>                         
                    <tr>
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                        <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                        <?
                            foreach($size_all_arr as $key=>$val)
                            {
                                ?>
                                <td width="50" align="right"><? echo $color_size_wise_qnty[$color_id][$key]['order_quantity'] ;?></td>
                                <?
                                $total_sizeqnty += $color_size_wise_qnty[$color_id][$key]['order_quantity'];
                                $gr_size_total[$key] += $color_size_wise_qnty[$color_id][$key]['order_quantity'];
                            }
                        ?>
                        <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                    </tr>
         
                     <?
                     $i++;
                 }
             }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="3" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>
     
    <?
    exit();
}

if($action=='cut_qty_popup')   
{   
    extract($_REQUEST); 
    echo load_html_head_contents("Sample Prod. Qty", "../../", 1, 1,$unicode,'','');
    $data_ex    = explode("_", $data);
    $mst_id     = $data_ex[0];
    $smpl_id    = $data_ex[1];
    $itm_id     = $data_ex[2];
    $date_from  = $data_ex[3];
    $date_to    = $data_ex[4];
    $date_cond = "";
    if($date_from !="" && $date_to !="")
    {
        $date_cond = " and b.sewing_date between '$date_from' and '$date_to'";
    }
    // $sql= "SELECT a.sample_name, a.sample_color,b.size_id,sum(b.bh_qty+b.plan_qty +b.dyeing_qty+ b.test_qty +b.self_qty) as total_qty from sample_development_dtls a,sample_development_size b where a.sample_mst_id=b.mst_id and a.id=b.dtls_id and a.entry_form_id in(117,203) and  a.status_active=1 and a.is_deleted=0 and a.sample_mst_id=$mst_id and a.sample_name=$smpl_id and a.gmts_item_id=$itm_id group by a.sample_name, a.sample_color,b.size_id order by a.sample_color,b.size_id";
    $sql="SELECT b.sample_name,b.sewing_date,c.color_id, c.size_id,sum(c.size_pass_qty) as qty from sample_sewing_output_mst a, sample_sewing_output_dtls b,sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id=127 and c.entry_form_id=127 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_development_id=$mst_id and b.item_number_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,b.sewing_date,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("sewing_date")]][$val[csf("color_id")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("sewing_date")]][$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 370+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Cutting Production Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="80" >Production Date</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $prod_date => $date_data) 
            {
                foreach ($date_data as $sample_id => $color_data) 
                {
                    foreach ($color_data as $color_id => $row) 
                    {    
                        $total_sizeqnty=0;            
                        ?>                         
                        <tr>
                            <td width="30" align="center"><? echo $i; ?></td>
                            <td width="100" align="center"><? echo  change_date_format($prod_date); ?></td>
                            <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                            <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                            <?
                                foreach($size_all_arr as $key=>$val)
                                {
                                    ?>
                                    <td width="50" align="right"><? echo $color_size_wise_qnty[$prod_date][$color_id][$key]['order_quantity'] ;?></td>
                                    <?
                                    $total_sizeqnty += $color_size_wise_qnty[$prod_date][$color_id][$key]['order_quantity'];
                                    $gr_size_total[$key] += $color_size_wise_qnty[$prod_date][$color_id][$key]['order_quantity'];
                                }
                            ?>
                            <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                        </tr>
             
                         <?
                         $i++;
                     }
                }
            }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="4" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>
     
    <?
    exit();
}

if($action=='sew_qty_popup')   
{   
    extract($_REQUEST); 
    echo load_html_head_contents("Sewing Prod. Qty", "../../", 1, 1,$unicode,'','');
    $data_ex = explode("_", $data);
    $mst_id = $data_ex[0];
    $smpl_id = $data_ex[1];
    $itm_id = $data_ex[2];
    $date_from  = $data_ex[3];
    $date_to    = $data_ex[4];
    $date_cond = "";
    if($date_from !="" && $date_to !="")
    {
        $date_cond = " and b.sewing_date between '$date_from' and '$date_to'";
    }
    
    $sql="SELECT b.sample_name,b.sewing_date,c.color_id, c.size_id,sum(c.size_pass_qty) as qty from sample_sewing_output_mst a, sample_sewing_output_dtls b,sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id=130 and c.entry_form_id=130 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_development_id=$mst_id and b.item_number_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,b.sewing_date,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("sewing_date")]][$val[csf("color_id")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("sewing_date")]][$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 370+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Sewing Production Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="80" >Production Date</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $prod_date => $date_data) 
            {
                foreach ($date_data as $sample_id => $color_data) 
                {
                    foreach ($color_data as $color_id => $row) 
                    {    
                        $total_sizeqnty=0;            
                        ?>                         
                        <tr>
                            <td width="30" align="center"><? echo $i; ?></td>
                            <td width="100" align="center"><? echo  change_date_format($prod_date); ?></td>
                            <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                            <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                            <?
                                foreach($size_all_arr as $key=>$val)
                                {
                                    ?>
                                    <td width="50" align="right"><? echo $color_size_wise_qnty[$prod_date][$color_id][$key]['order_quantity'] ;?></td>
                                    <?
                                    $total_sizeqnty += $color_size_wise_qnty[$prod_date][$color_id][$key]['order_quantity'];
                                    $gr_size_total[$key] += $color_size_wise_qnty[$prod_date][$color_id][$key]['order_quantity'];
                                }
                            ?>
                            <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                        </tr>
             
                        <?
                        $i++;
                    }
                }
            }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="4" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>
     
    <?
    exit();
}

if($action=='embl_qty_popup')   
{   
    extract($_REQUEST); 
    echo load_html_head_contents("Sewing Prod. Qty", "../../", 1, 1,$unicode,'','');
    $data_ex = explode("_", $data);
    $mst_id = $data_ex[0];
    $smpl_id = $data_ex[1];
    $itm_id = $data_ex[2];
    $date_from  = $data_ex[3];
    $date_to    = $data_ex[4];
    $date_cond = "";
    if($date_from !="" && $date_to !="")
    {
        $date_cond = " and b.sewing_date between '$date_from' and '$date_to'";
    }
    
    $sql="SELECT b.sample_name,b.embel_name,b.embel_type,b.sewing_date,c.color_id, c.size_id,sum(c.size_pass_qty) as qty from sample_sewing_output_mst a, sample_sewing_output_dtls b,sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id=128 and c.entry_form_id=128 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_development_id=$mst_id and b.item_number_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,b.embel_name,b.embel_type,b.sewing_date,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {   
        $color_size_wise_qnty[$val[csf("sewing_date")]][$val[csf("embel_name")]][$val[csf("embel_type")]][$val[csf("sample_name")]][$val[csf("color_id")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("sewing_date")]][$val[csf("embel_name")]][$val[csf("embel_type")]][$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 590+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Embl. Production Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="100" >Production Date</th>
                <th width="100" >Emb Name</th>
                <th width="100" >Emb Type</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $prod_date => $date_data) 
            {
                foreach ($date_data as $embl_name_id => $embl_name_data) 
                {
                    foreach ($embl_name_data as $emble_type_id => $emble_type_data) 
                    {
                        foreach ($emble_type_data as $sample_id => $color_data) 
                        {
                            foreach ($color_data as $color_id => $row) 
                            {    
                                $total_sizeqnty=0;  
                                 
                                if($embl_name_id==1) $emb_type=$emblishment_print_type;
                                else if($embl_name_id==2) $emb_type=$emblishment_embroy_type;
                                else if($embl_name_id==3) $emb_type=$emblishment_wash_type;
                                else if($embl_name_id==4) $emb_type=$emblishment_spwork_type;
                                else if($embl_name_id==5) $emb_type=$emblishment_gmts_type;
                                else $emb_type="";         
                                ?>                         
                                <tr>
                                    <td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100" align="center"><? echo  change_date_format($prod_date); ?></td>
                                    <td width="100" align="center"><? echo  $emblishment_name_array[$embl_name_id]; ?></td>
                                    <td width="100" align="center"><? echo  $emb_type[$emble_type_id]; ?></td>
                                    <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                                    <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                                    <?
                                        foreach($size_all_arr as $key=>$val)
                                        {
                                            ?>
                                            <td width="50" align="right"><? echo $color_size_wise_qnty[$prod_date][$embl_name_id][$emble_type_id][$sample_id][$color_id][$key]['order_quantity'] ;?></td>
                                            <?
                                            $total_sizeqnty += $color_size_wise_qnty[$prod_date][$embl_name_id][$emble_type_id][$sample_id][$color_id][$key]['order_quantity'];
                                            $gr_size_total[$key] += $color_size_wise_qnty[$prod_date][$embl_name_id][$emble_type_id][$sample_id][$color_id][$key]['order_quantity'];
                                        }
                                    ?>
                                    <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                                </tr>
                     
                                 <?
                                 $i++;
                            }
                        }
                    }
                }
            }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="6" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>
     
    <?
    exit();
}

if($action=='rej_qty_popup')   
{   
    extract($_REQUEST); 
    echo load_html_head_contents("Reject Prod. Qty", "../../", 1, 1,$unicode,'','');
    $data_ex = explode("_", $data);
    $mst_id = $data_ex[0];
    $smpl_id = $data_ex[1];
    $itm_id = $data_ex[2];
    $date_from  = $data_ex[3];
    $date_to    = $data_ex[4];
    $date_cond = "";
    if($date_from !="" && $date_to !="")
    {
        $date_cond = " and b.sewing_date between '$date_from' and '$date_to'";
    }

    // $sql= "SELECT a.sample_name, a.sample_color,b.size_id,sum(b.bh_qty+b.plan_qty +b.dyeing_qty+ b.test_qty +b.self_qty) as total_qty from sample_development_dtls a,sample_development_size b where a.sample_mst_id=b.mst_id and a.id=b.dtls_id and a.entry_form_id in(117,203) and  a.status_active=1 and a.is_deleted=0 and a.sample_mst_id=$mst_id and a.sample_name=$smpl_id and a.gmts_item_id=$itm_id group by a.sample_name, a.sample_color,b.size_id order by a.sample_color,b.size_id";
    $sql="SELECT b.sample_name,c.color_id, c.size_id,sum(c.size_rej_qty) as qty from sample_sewing_output_mst a, sample_sewing_output_dtls b,sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id=127 and c.entry_form_id=127 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_development_id=$mst_id and b.item_number_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("color_id")]][$val[csf("size_id")]]["rej_qty"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 290+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Cutting Reject Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Rej. Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $sample_id => $color_data) 
            {
                foreach ($color_data as $color_id => $row) 
                {    
                    $total_sizeqnty=0;            
                    ?>                         
                    <tr>
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                        <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                        <?
                            foreach($size_all_arr as $key=>$val)
                            {
                                ?>
                                <td width="50" align="right"><? echo $color_size_wise_qnty[$color_id][$key]['rej_qty'] ;?></td>
                                <?
                                $total_sizeqnty += $color_size_wise_qnty[$color_id][$key]['rej_qty'];
                                $gr_size_total[$key] += $color_size_wise_qnty[$color_id][$key]['rej_qty'];
                            }
                        ?>
                        <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                    </tr>
         
                     <?
                     $i++;
                 }
             }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="3" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>

    <!-- ==================================== ====================================-->
    <?
    $sql="SELECT b.sample_name,c.color_id, c.size_id,sum(c.size_rej_qty) as qty from sample_sewing_output_mst a, sample_sewing_output_dtls b,sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id=130 and c.entry_form_id=130 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_development_id=$mst_id and b.item_number_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("color_id")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 290+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Sewing Reject Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $sample_id => $color_data) 
            {
                foreach ($color_data as $color_id => $row) 
                {    
                    $total_sizeqnty=0;            
                    ?>                         
                    <tr>
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                        <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                        <?
                            foreach($size_all_arr as $key=>$val)
                            {
                                ?>
                                <td width="50" align="right"><? echo $color_size_wise_qnty[$color_id][$key]['order_quantity'] ;?></td>
                                <?
                                $total_sizeqnty += $color_size_wise_qnty[$color_id][$key]['order_quantity'];
                                $gr_size_total[$key] += $color_size_wise_qnty[$color_id][$key]['order_quantity'];
                            }
                        ?>
                        <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                    </tr>
         
                     <?
                     $i++;
                 }
             }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="3" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>  

    <!-- =================================== ================================== -->  
    <? 
    $sql="SELECT b.sample_name,c.color_id, c.size_id,sum(c.size_rej_qty) as qty from sample_sewing_output_mst a, sample_sewing_output_dtls b,sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id=128 and c.entry_form_id=128 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_development_id=$mst_id and b.item_number_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("color_id")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 290+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Embl Reject Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $sample_id => $color_data) 
            {
                foreach ($color_data as $color_id => $row) 
                {    
                    $total_sizeqnty=0;            
                    ?>                         
                    <tr>
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                        <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                        <?
                            foreach($size_all_arr as $key=>$val)
                            {
                                ?>
                                <td width="50" align="right"><? echo $color_size_wise_qnty[$color_id][$key]['order_quantity'] ;?></td>
                                <?
                                $total_sizeqnty += $color_size_wise_qnty[$color_id][$key]['order_quantity'];
                                $gr_size_total[$key] += $color_size_wise_qnty[$color_id][$key]['order_quantity'];
                            }
                        ?>
                        <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                    </tr>
         
                     <?
                     $i++;
                 }
             }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="3" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>
    
    <?
    exit();
}

if($action=='del_qty_popup')   
{   
    extract($_REQUEST); 
    echo load_html_head_contents("Delivery Prod. Qty", "../../", 1, 1,$unicode,'','');
    $data_ex = explode("_", $data);
    $mst_id = $data_ex[0];
    $smpl_id = $data_ex[1];
    $itm_id = $data_ex[2];
    $date_from  = $data_ex[3];
    $date_to    = $data_ex[4];
    $date_cond = "";
    if($date_from !="" && $date_to !="")
    {
        $date_cond = " and b.delivery_date between '$date_from' and '$date_to'";
    }
    
    $sql="SELECT b.sample_name,b.delivery_date,c.color_id, c.size_id,sum(c.size_pass_qty) as qty from sample_ex_factory_mst a, sample_ex_factory_dtls b,sample_ex_factory_colorsize c where a.id=b.sample_ex_factory_mst_id and a.id=c.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.entry_form_id in( 132) and c.entry_form_id in( 132) and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.sample_development_id=$mst_id and b.gmts_item_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,b.delivery_date,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("delivery_date")]][$val[csf("color_id")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("delivery_date")]][$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 370+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Delivery Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="80" >Delivery Date</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $del_date => $del_data) 
            {
                foreach ($del_data as $sample_id => $color_data) 
                {
                    foreach ($color_data as $color_id => $row) 
                    {    
                        $total_sizeqnty=0;            
                        ?>                         
                        <tr>
                            <td width="30" align="center"><? echo $i; ?></td>
                            <td width="100" align="center"><? echo  change_date_format($del_date); ?></td>
                            <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                            <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                            <?
                                foreach($size_all_arr as $key=>$val)
                                {
                                    ?>
                                    <td width="50" align="right"><? echo $color_size_wise_qnty[$del_date][$color_id][$key]['order_quantity'] ;?></td>
                                    <?
                                    $total_sizeqnty += $color_size_wise_qnty[$del_date][$color_id][$key]['order_quantity'];
                                    $gr_size_total[$key] += $color_size_wise_qnty[$del_date][$color_id][$key]['order_quantity'];
                                }
                            ?>
                            <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                        </tr>
             
                        <?
                        $i++;
                    }
                }
            }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="4" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>
     
    <?
    exit();
} 

if($action=='del_mkt_qty_popup')   
{   
    extract($_REQUEST); 
    echo load_html_head_contents("Delivery Prod. Qty", "../../", 1, 1,$unicode,'','');
    $data_ex = explode("_", $data);
    $mst_id = $data_ex[0];
    $smpl_id = $data_ex[1];
    $itm_id = $data_ex[2];
    $date_from  = $data_ex[3];
    $date_to    = $data_ex[4];
    $date_cond = "";
    if($date_from !="" && $date_to !="")
    {
        $date_cond = " and b.delivery_date between '$date_from' and '$date_to'";
    }
    
    $sql="SELECT b.sample_name,b.delivery_date,c.color_id, c.size_id,sum(c.size_pass_qty) as qty from sample_ex_factory_mst a, sample_ex_factory_dtls b,sample_ex_factory_colorsize c where a.id=b.sample_ex_factory_mst_id and a.id=c.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.entry_form_id in(396) and c.entry_form_id in(396) and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.sample_development_id=$mst_id and b.gmts_item_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,b.delivery_date,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("delivery_date")]][$val[csf("color_id")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("delivery_date")]][$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 370+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Delivery Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="80" >Delivery Date</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $del_date => $del_data) 
            {
                foreach ($del_data as $sample_id => $color_data) 
                {
                    foreach ($color_data as $color_id => $row) 
                    {    
                        $total_sizeqnty=0;            
                        ?>                         
                        <tr>
                            <td width="30" align="center"><? echo $i; ?></td>
                            <td width="100" align="center"><? echo  change_date_format($del_date); ?></td>
                            <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                            <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                            <?
                                foreach($size_all_arr as $key=>$val)
                                {
                                    ?>
                                    <td width="50" align="right"><? echo $color_size_wise_qnty[$del_date][$color_id][$key]['order_quantity'] ;?></td>
                                    <?
                                    $total_sizeqnty += $color_size_wise_qnty[$del_date][$color_id][$key]['order_quantity'];
                                    $gr_size_total[$key] += $color_size_wise_qnty[$del_date][$color_id][$key]['order_quantity'];
                                }
                            ?>
                            <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                        </tr>
             
                        <?
                        $i++;
                    }
                }
            }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="4" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>
     
    <?
    exit();
} 
?>