<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_name = $_SESSION['logic_erp']['user_id'];
$user_id = $_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------


if($action=="print_button_variable_setting")
{
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=7 and report_id=149 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit();
}


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/date_wise_production_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/date_wise_production_report_controller' );",0 );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 0, "-- Select --", $selected, "",0 );
	exit();
}
if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_floor','0','0','','0');\n";
    exit();
}
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
	exit();
}

if($action=="report_generate")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
    $company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name");
    $buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
    $supplier_short_library=return_library_array( "select id,short_name from lib_supplier", "id", "short_name");
    $location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name");
    $floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
    $line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
    $prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');


    $garments_nature=str_replace("'","",$cbo_garments_nature);
    if($garments_nature==1)$garments_nature="";
    $type = str_replace("'","",$cbo_type);
    $location = str_replace("'","",$cbo_location);
    $cbo_floor = str_replace("'","",$cbo_floor);
    $file_no = str_replace("'","",$txt_file_no);
    $internal_ref = str_replace("'","",$txt_internal_ref);
    $excel_type = str_replace("'","",$excel_type);
    if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and b.company_name=$cbo_company_name";
    if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
    if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
    else $txt_date=" and production_date between $txt_date_from and $txt_date_to";
    $fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
    $toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
    if ($location==0) $location_cond=""; else $location_cond=" and location=".$location." ";
    if($cbo_floor=="") $floor_name="";else $floor_name=" and floor_id in($cbo_floor)";
    //echo $floor_name;
    //echo $location_cond;
    if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
    if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

    $all_order_sewQty_array=array();
    if($type==1)
    {
        $colspn='50';
    }
    else if($type==2)
    {
        $colspn='50';
    }

    if(str_replace("'","",trim($txt_date_from))!="" || str_replace("'","",trim($txt_date_to))!="")
    {
        $all_po_ids_array=return_library_array( "SELECT po_break_down_id  from pro_garments_production_mst
                where  status_active=1 and is_deleted=0  $txt_date group by po_break_down_id ", "po_break_down_id", "po_break_down_id" );
        // ============================= store data in gbl table ==============================
        $con = connect();
        execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in(1,2) and ENTRY_FORM=41");
        oci_commit($con);

        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 41, 1, $all_po_ids_array, $empty_arr);//Po ID
        disconnect($con);

        /* $all_po_ids=implode(",", $all_po_ids_array);

        if($all_po_ids)
        {
            if($db_type==2 && count($all_po_ids_array)>999)
            {
                $chnk_arr=array_chunk($all_po_ids_array, 999);
                $all_po_ids_cond="";
                foreach($chnk_arr as $v)
                {
                    $vals=implode(",", $v);
                    if($all_po_ids_cond=="") $all_po_ids_cond.=" and (b.id in ($vals) ";
                    else
                        $all_po_ids_cond.=" or b.id in ($vals) ";
                }
                if($all_po_ids_cond)    $all_po_ids_cond.=")";

            }
            else
                $all_po_ids_cond=" and b.id in($all_po_ids) ";
        } */
    }


    ob_start();

    if($excel_type==0 || $excel_type==10)
    {
        ?>
        <div>
        <table width="3530" cellspacing="0">
            <tr class="form_caption" style="border:none;">
                    <td colspan="50" align="center" style="border:none;font-size:14px; font-weight:bold" >Date Wise Production Report</td>
             </tr>
            <tr style="border:none;">
                    <td colspan="50" align="center" style="border:none; font-size:16px; font-weight:bold">
                        Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
                    </td>
              </tr>
              <tr style="border:none;">
                    <td colspan="50" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? echo "From $fromDate To $toDate" ;?>
                    </td>
              </tr>
        </table>
        <div style="max-height:225px; overflow-y:scroll; width:1648px" >
        <table width="1630" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
            <thead>
                <tr>
                    <th width="30" style="word-break: break-all;word-wrap: break-word; "><p>Sl.</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Buyer Name</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Cut Qty</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Sent to Print</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Rev Print</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Sent to Emb</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Rev Emb</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Sent to Gmt</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Recv Gmt</p></th>

                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Sent to Wash</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Rev Wash</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Sent to Sp. Works</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Rev Sp. Works</p></th>

                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Cut Delv. Qty</p></th>


                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Sew Input(Inhouse)</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Sew Input (Outbound)</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Sew Output (Inhouse)</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Sew Output (Outbound)</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Total Iron</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " width="80"><p>Total Re-Iron</p></th>
                    <th style="word-break: break-all;word-wrap: break-word; " ><p>Total Finish</p></th>
                 </tr>
            </thead>
        </table>
        <table cellspacing="0" border="1" class="rpt_table"  width="1630" rules="all" id="" >
        <?
        $job_arr=array();
        if($db_type==0) $year_cond="YEAR(a.insert_date)";
        else if($db_type==2) $year_cond="to_char(a.insert_date,'YYYY')";

        $locCond = str_replace("location", "a.location_name", $location_cond);
        if(count($all_po_ids_array) >0 )
        {
            $job_sql="SELECT a.id as job_id, a.job_no_prefix_num, a.job_no, a.buyer_name, $year_cond as year, a.style_ref_no, b.id, b.po_number, b.grouping, b.file_no, (b.unit_price/a.total_set_qnty) as unit_price , (b.po_quantity*a.total_set_qnty) as po_quantity, a.total_set_qnty as ratio, a.order_uom from wo_po_details_master a, wo_po_break_down b,GBL_TEMP_ENGINE tmp where a.id=b.job_id and b.id=tmp.ref_val and tmp.entry_form=41  and tmp.user_id=$user_id and tmp.ref_from=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and a.company_name=$cbo_company_name $buyer_name $file_no_cond $internal_ref_cond  $locCond";
        }
        else
        {
            $job_sql="SELECT a.id as job_id,a.job_no_prefix_num, a.job_no, a.buyer_name, $year_cond as year, a.style_ref_no, b.id, b.po_number, b.grouping, b.file_no, (b.unit_price/a.total_set_qnty) as unit_price , (b.po_quantity*a.total_set_qnty) as po_quantity, a.total_set_qnty as ratio, a.order_uom from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and a.company_name=$cbo_company_name $buyer_name $file_no_cond $internal_ref_cond $locCond";
        }
        $job_sql_res=sql_select($job_sql); $tot_rows=0; $poIds='';
        //echo $job_sql;die;
        //die;
        $job_mst_array=array();
        $po_id_array = array();
        $job_id_array = array();
        foreach($job_sql_res as $row)
        {
            $tot_rows++;
            $poIds.=$row[csf("id")].",";
            $po_id_array[$row[csf("id")]] = $row[csf("id")];
            $job_id_array[$row[csf("job_id")]] = $row[csf("job_id")];
            $job_arr[$row[csf("id")]]=$row[csf("job_no_prefix_num")].'!!'.$row[csf("job_no")].'!!'.$row[csf("buyer_name")].'!!'.$row[csf("style_ref_no")].'!!'.$row[csf("po_number")].'!!'.$row[csf("grouping")].'!!'.$row[csf("file_no")].'!!'.$row[csf("unit_price")].'!!'.$row[csf("po_quantity")].'!!'.$row[csf("ratio")].'!!'.$row[csf("order_uom")].'!!'.$row[csf("year")];
            $job_mst_array[$row[csf("id")]]=$row[csf("job_no")];
        }
        unset($job_sql_res);
        // ============================= store data in gbl table ==============================
        $con = connect();
        execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in(1,2) and ENTRY_FORM=41");
        oci_commit($con);

        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 41, 1, $po_id_array, $empty_arr);//Po ID
        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 41, 2, $job_id_array, $empty_arr);//Job ID
        disconnect($con);

        $job_item_smv = sql_select("SELECT a.id, b.gmts_item_id, b.finsmv_pcs, b.smv_pcs from wo_po_break_down a, wo_po_details_mas_set_details b, GBL_TEMP_ENGINE tmp where a.job_id=b.job_id and b.job_id=tmp.ref_val and tmp.entry_form=41  and tmp.user_id=$user_id and tmp.ref_from=2 and a.status_active=1");
        $job_item_smv_arr=array();
        foreach($job_item_smv as $row)
        {
            $job_item_smv_arr[$row[csf('id')]][$row[csf('gmts_item_id')]]['finsmv_pcs']=$row[csf('finsmv_pcs')];
            $job_item_smv_arr[$row[csf('id')]][$row[csf('gmts_item_id')]]['smv_pcs']=$row[csf('smv_pcs')];
        }
        unset($job_item_smv);

        $costing_per_id_library=return_library_array( "SELECT a.job_no, a.costing_per from wo_pre_cost_mst a,GBL_TEMP_ENGINE tmp where a.job_id=tmp.ref_val and tmp.entry_form=41  and tmp.user_id=$user_id and tmp.ref_from=2", "job_no", "costing_per");

    //$booking_no_fin=return_library_array( "select id, booking_no from wo_booking_dtls", "id", "booking_no");


    $avg_unit_price_arr=return_library_array( "SELECT a.job_no, a.avg_unit_price from wo_po_details_master a,GBL_TEMP_ENGINE tmp where a.id=tmp.ref_val and tmp.entry_form=41  and tmp.user_id=$user_id and tmp.ref_from=2", "job_no", "avg_unit_price");
    $tot_cost_arr=array();
    $tot_cm_cost_arr=array();
    $pre_cost_arr = sql_select("SELECT a.job_no, a.cm_cost, a.cm_for_sipment_sche,a.margin_pcs_set from wo_pre_cost_dtls a,GBL_TEMP_ENGINE tmp where a.job_id=tmp.ref_val and tmp.entry_form=41  and tmp.user_id=$user_id and tmp.ref_from=2");
    foreach($pre_cost_arr as $row)
    {
        $tot_cost_arr[$row[csf('job_no')]]=$row[csf('cm_for_sipment_sche')];
        $tot_cm_cost_arr[$row[csf('job_no')]]=$row[csf('cm_cost')];
        $tot_margin_pcs_arr[$row[csf('job_no')]]=$row[csf('margin_pcs_set')];
    }
    unset($pre_cost_arr);

    $dtlssqls = "SELECT a.job_no,sum( case when a.commission>0 then a.commission else 0 end ) as commission,( sum( case when a.fabric_cost>0 then a.fabric_cost  else 0 end  )+ sum( case when a.trims_cost>0 then a.trims_cost  else 0 end  )+ sum(case when a.embel_cost>0 then a.embel_cost  else 0 end  ) + sum(case when a.wash_cost>0 then a.wash_cost  else 0 end  ) + sum(case when a.comm_cost>0 then a.comm_cost  else 0 end )    +   sum(case when a.lab_test>0 then a.lab_test  else 0 end ) + sum(case when a.inspection>0 then a.inspection  else 0 end ) +  sum(case when a.freight>0 then a.freight  else 0 end  ) + sum(case when a.currier_pre_cost>0 then a.currier_pre_cost  else 0 end ) + sum(case when a.certificate_pre_cost>0 then a.certificate_pre_cost  else 0 end ) +    sum( case when a.interest_cost>0 then a.interest_cost  else 0 end ) + sum(case when a.incometax_cost>0 then a.incometax_cost  else 0 end ) + sum(case when a.deffdlc_cost>0 then a.deffdlc_cost  else 0 end )) as total_val from wo_pre_cost_dtls a,GBL_TEMP_ENGINE tmp where a.job_id=tmp.ref_val and tmp.entry_form=41  and tmp.user_id=$user_id and tmp.ref_from=2 and status_active=1 and is_deleted=0   group by job_no";
    foreach(sql_select($dtlssqls) as $vals)
    {
        $commission=$vals[csf('commission')];
        $job_no=$vals[csf('job_no')];
        $grfobv_dzn=number_format($avg_unit_price_arr[$job_no]*12,2);


         $net_fob_value=$grfobv_dzn-$commission;

        //$total_material_cost=$fabric_cost+$trims_cost+$embel_cost+$wash_cost+$comm_cost+$lab_test+$inspection+$freight+$currier_pre_cost+$certificate_pre_cost+$interest_cost+$incometax_cost+$deffdlc_cost;
        $total_material_cost=$vals[csf("total_val")];

        $cm_value_contribution_margine[$job_no]=$net_fob_value-$total_material_cost;

		//echo $job_no.'='.$total_material_cost.';';

    }
    //print_r($cm_value_contribution_margine);die;

            $poIds_cond="";
            $poIds_cond2="";
            //if ($file_no!="" || $internal_ref!="")
            //{
                $poIds=chop($poIds,',');
                if($db_type==2 && $tot_rows>1000)
                {
                    $poIds_cond=" and (";
                    $poIds_cond2=" and (";
                    $poIdsArr=array_chunk(explode(",",$poIds),999);
                    foreach($poIdsArr as $ids)
                    {
                        $ids=implode(",",$ids);
                        $poIds_cond.=" po_break_down_id in ($ids) or ";
                        $poIds_cond2.=" c.po_break_down_id in ($ids) or ";
                    }
                    $poIds_cond=chop($poIds_cond,'or ');
                    $poIds_cond.=")";
                    $poIds_cond2=chop($poIds_cond2,'or ');
                    $poIds_cond2.=")";
                }
                else
                {
                    $poIds_cond=" and po_break_down_id in ($poIds)";
                    $poIds_cond2=" and c.po_break_down_id in ($poIds)";
                }
            //}
            $allOrderIDs='';
            $buyer_fullQty_arr=array();
            $prod_date_qty_arr=array();
            $prod_dlfl_qty_arr=array();
            $all_data_arr=array();
            $job_wise_qnty_array=array();
            $sql_dtls="SELECT a.location, a.company_id,a.serving_company, a.floor_id, a.sewing_line, a.po_break_down_id,a.re_production_qty, a.production_date, a.item_number_id, a.production_type, a.production_source, a.embel_name, max(a.prod_reso_allo) as prod_reso_allo, sum(b.production_qnty) as production_quantity, sum(a.reject_qnty) as reject_qnty, a.carton_qty as carton_qty
            from pro_garments_production_mst a,pro_garments_production_dtls b,GBL_TEMP_ENGINE tmp
            where a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and tmp.entry_form=41  and tmp.user_id=$user_id and tmp.ref_from=1 and a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $txt_date $floor_name  $garmentsNature
            group by a.location, a.company_id,a.serving_company, a.floor_id, a.sewing_line, a.po_break_down_id,a.re_production_qty, a.production_date, a.item_number_id, a.production_type, a.production_source, a.embel_name, a.carton_qty
            order by production_date ASC";
            // echo $sql_dtls;die();
            $sql_dtls_res=sql_select($sql_dtls);
            //echo $sql_dtls;  die;//$poIds_cond
            if($type==1)
            {
                foreach($sql_dtls_res as $row)
                {
                    if($row[csf("production_type")]==5)
                    {
                        $job_wise_qnty_array[$job_mst_array[$row[csf("po_break_down_id")]]] +=$row[csf("production_quantity")];

                    }
                    $job_wise_prod_date_array[$job_mst_array[$row[csf("po_break_down_id")]]] =$row[csf("production_date")];
                    $buyer_name_dat="";
                    $all_job_data=$job_arr[$row[csf("po_break_down_id")]];
                    $ex_job_data=explode('!!',$all_job_data);

                    $buyer_name_dat=$ex_job_data[2];
                    $allOrderIDs.=$row[csf("po_break_down_id")].',';
                    //Buyer Wise Summary array start
                    $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                    $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                    $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                    $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                    $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];
                    //Details array start
                    $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                    $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                    $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                    $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                    $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
                    $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
                    $all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]].=$row[csf("item_number_id")].'**'.$row[csf("company_id")].'__';
                    $po_date_array[$row[csf("po_break_down_id")]][$row[csf("production_date")]]=$row[csf("po_break_down_id")];
                    if($row[csf("production_type")]==5)
                    {
                        $po_info_array[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("production_source")]].=$row[csf("serving_company")].",";
                    }
                }
            }
            else
            {
                foreach($sql_dtls_res as $row)
                {
                    if($row[csf("production_type")]==5)
                    {
                        $job_wise_qnty_array[$job_mst_array[$row[csf("po_break_down_id")]]] +=$row[csf("production_quantity")];

                    }
                    $job_wise_prod_date_array[$job_mst_array[$row[csf("po_break_down_id")]]] =$row[csf("production_date")];
                    $buyer_name_dat="";
                    $all_job_data=$job_arr[$row[csf("po_break_down_id")]];
                    $ex_job_data=explode('!!',$all_job_data);

                    $buyer_name_dat=$ex_job_data[2];
                    $allOrderIDs.=$row[csf("po_break_down_id")].',';
                    //Buyer Wise Summary array start
                    $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                    $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                    $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                    $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                    $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];
                    //Details array start
                    if($row[csf("sewing_line")]=="") $row[csf("sewing_line")]=0;

                    if($row[csf("floor_id")]=="") $row[csf("floor_id")]=0;

                    if($row[csf("location")]=="") $row[csf("location")]=0;

                    $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                    $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                    $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                    $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                    $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
                    $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];

                    $all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]].=$row[csf("sewing_line")].'##'.$row[csf("company_id")].'##'.$row[csf("production_source")].'##'.$row[csf("prod_reso_allo")].'__';
                    $po_date_array[$row[csf("po_break_down_id")]][$row[csf("production_date")]]=$row[csf("po_break_down_id")];
                }
            }
            //print_r($prod_dlfl_qty_arr[6152]['02-JAN-16'][4]); die;
            unset($sql_dtls_res);
            // echo "<pre>";print_r($po_info_array);die;



            $allOrderID=chop($allOrderIDs,',');
            $allpoIDarr_unique=explode(',',$allOrderID);
            $poIDSS="";
            foreach(array_unique($allpoIDarr_unique) as $poIDS)
            {
                 if (!empty($poIDS)) { $poIDSS.=$poIDS.','; }
            }
            $PO_unique= chop($poIDSS,',');
            //echo $PO_unique;
            if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")
            {
                $txt_date2="";
            }
            else
            {
                $txt_date2=" and a.cut_delivery_date between $txt_date_from and $txt_date_to";

            }
            if($PO_unique==""){ echo "<div style='text-align:center;color:red;font-size:18px;'>Data Not Found!</div>";die();}

            $cut_delv_qty_sql=sql_select("SELECT a.po_break_down_id,a.buyer_id,a.cut_delivery_date as production_date,sum(b.production_qnty) as cut_delivery_qnty
            from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,GBL_TEMP_ENGINE tmp
            where a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and tmp.entry_form=41  and tmp.user_id=$user_id and tmp.ref_from=1  $txt_date2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type=9 and b.production_type=9 group by a.po_break_down_id,a.buyer_id,a.cut_delivery_date");


            $cut_delv_qty_arr=array();
            $cut_delv_qty_buyer_arr=array();

            foreach($cut_delv_qty_sql as $row)
            {
                if($po_date_array[$row[csf("po_break_down_id")]][$row[csf("production_date")]]!="")
                {
                    $cut_delv_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]]["cut_delivery_qnty"] +=$row[csf("cut_delivery_qnty")];
                    $cut_delv_qty_buyer_arr[$row[csf("buyer_id")]]["cut_delivery_buyer_qnty"]+=$row[csf("cut_delivery_qnty")];
                }

            }


            $smv_qty_arr=array();
            $sql_smv="SELECT a.job_no, a.gmts_item_id,a.smv_pcs from wo_po_details_mas_set_details a,GBL_TEMP_ENGINE tmp where a.job_id=tmp.ref_val and tmp.entry_form=41  and tmp.user_id=$user_id and tmp.ref_from=2";
            $data_smv_array=sql_select($sql_smv);
            foreach($data_smv_array as $row)
            {
                $smv_qty_arr[$row[csf("job_no")]][$row[csf("gmts_item_id")]]=$row[csf("smv_pcs")];
            }

            $booking_fin_qty_arr=array();
            $sql_booking="SELECT c.job_no, c.po_break_down_id as po_id, b.item_number_id, c.fin_fab_qnty from wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls b, GBL_TEMP_ENGINE tmp where b.id=c.pre_cost_fabric_cost_dtls_id and c.job_no=b.job_no and b.job_id=tmp.ref_val and tmp.entry_form=41  and tmp.user_id=$user_id and tmp.ref_from=2 and c.booking_type=1";
            $data_booking_array=sql_select($sql_booking);
            foreach($data_booking_array as $row)
            {
                $prod_date=$job_wise_prod_date_array[$job_mst_array[$row[csf("po_id")]]];
                $booking_fin_qty_arr[$row[csf("job_no")]][$row[csf("item_number_id")]]['fin_fab_qnty']+=$row[csf("fin_fab_qnty")];
            }

            $con = connect();
            execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in(1,2) and ENTRY_FORM=41");
            oci_commit($con);
            disconnect($con);

            //die;
            //$cm_per_dzn=return_library_array( "select job_no, cm_for_sipment_sche from wo_pre_cost_dtls where is_deleted=0 and status_active=1",'job_no','cm_for_sipment_sche');
            $b=1; //date_wise Summary
            $sum_embGmt_qty=$sum_embGmtRec_qty=0;
            $sum_cut_delv=0;
            foreach($buyer_fullQty_arr as $buyer_id=>$buyer_data)
            {
                if ($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $cutting_qty=$printing_qty=$printreceived_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewOut_inQty=$sewOut_outQty=$iron_qty=$reIron_qty=$finish_qty=0;
                $cutting_qty=$buyer_data['1']['0']['pQty'];
                $printing_qty=$buyer_data['2']['1']['embQty'];
                $printreceived_qty=$buyer_data['3']['1']['embQty'];
                $emb_qty=$buyer_data['2']['2']['embQty'];
                $embRec_qty=$buyer_data['3']['2']['embQty'];

                $embGmt_qty=$buyer_data['2']['5']['embQty'];
                $embGmtRec_qty=$buyer_data['3']['5']['embQty'];

                $wash_qty=$buyer_data['2']['3']['embQty'];
                $washRec_qty=$buyer_data['3']['3']['embQty'];
                $special_qty=$buyer_data['2']['4']['embQty'];
                $specialRec_qty=$buyer_data['3']['4']['embQty'];
                $sewIn_inQty=$buyer_data['4']['1']['sQty'];
                $sewIn_outQty=$buyer_data['4']['3']['sQty'];
                $sewOut_inQty=$buyer_data['5']['1']['sQty'];
                $sewOut_outQty=$buyer_data['5']['3']['sQty'];
                $iron_qty=$buyer_data['7']['0']['pQty'];
                $reIron_qty=$buyer_data['7']['0']['reQty'];
                $finish_qty=$buyer_data['8']['0']['pQty'];

                $cutDelvBuyer_qty=$cut_delv_qty_buyer_arr[$buyer_id]["cut_delivery_buyer_qnty"];
                $sum_cut_delv+=$cutDelvBuyer_qty;

                //$cut_delv_qty_arr[$row[csf("po_break_down_id")]]["cut_delivery_qnty"]

                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $b; ?>">
                    <td width="30"><? echo $b;?></td>
                    <td width="80"><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
                    <td width="80" align="right"><? echo number_format($cutting_qty); ?></td>
                    <td width="80" align="right"><? echo number_format($printing_qty); ?></td>
                    <td width="80" align="right"><? echo number_format($printreceived_qty); ?></td>
                    <td width="80" align="right"><? echo number_format($emb_qty); ?></td>
                    <td width="80" align="right"><? echo number_format($embRec_qty); ?></td>

                    <td width="80" align="right"><? echo number_format($embGmt_qty); ?></td>
                    <td width="80" align="right"><? echo number_format($embGmtRec_qty); ?></td>

                    <td width="80" align="right"><? echo number_format($wash_qty); ?></td>
                    <td width="80" align="right"><? echo number_format($washRec_qty);  ?></td>
                    <td width="80" align="right"><? echo number_format($special_qty); ?></td>
                    <td width="80" align="right"><? echo number_format($specialRec_qty);  ?></td>

                    <td width="80" align="right"><? echo number_format($cutDelvBuyer_qty); ?></td>

                    <td width="80" align="right"><? echo number_format($sewIn_inQty); ?></td>
                    <td width="80" align="right"><? echo number_format($sewIn_outQty); ?></td>
                    <td width="80" align="right"><? echo number_format($sewOut_inQty); ?></td>
                    <td width="80" align="right"><? echo number_format($sewOut_outQty); ?></td>
                    <td width="80" align="right"><? echo number_format($iron_qty); ?></td>
                    <td width="80" align="right"><? echo number_format($reIron_qty); ?></td>
                    <td  align="right"><? echo number_format($finish_qty); ?></td>
                </tr>
                <?
                $sumCutting_qty+=$cutting_qty;
                $sumPrinting_qty+=$printing_qty;
                $sumPrintreceived_qty+=$printreceived_qty;
                $sumEmb_qty+=$emb_qty;
                $sumEmbRec_qty+=$embRec_qty;
                $sumWash_qty+=$wash_qty;
                $sumWashRec_qty+=$washRec_qty;
                $sumSpecial_qty+=$special_qty;
                $sumSpecialRec_qty+=$specialRec_qty;
                $sumSewIn_inQty+=$sewIn_inQty;
                $sumSewIn_outQty+=$sewIn_outQty;
                $sumSewOut_inQty+=$sewOut_inQty;
                $sumSewOut_outQty+=$sewOut_outQty;
                $sumIron_qty+=$iron_qty;
                $sumReIron_qty+=$reIron_qty;
                $sumFinish_qty+=$finish_qty;

                $sum_embGmt_qty+=$embGmt_qty;
                $sum_embGmtRec_qty+=$embGmtRec_qty;
                $b++;
            }
            ?>
            </table>
            <table border="1" class="tbl_bottom"  width="1630" rules="all" id="" >
                 <tr>
                    <td width="30">&nbsp;</td>
                    <td width="80">Total</td>
                    <td width="80"><? echo number_format($sumCutting_qty); ?></td>
                    <td width="80"><? echo number_format($sumPrinting_qty); ?></td>
                    <td width="80"><? echo number_format($sumPrintreceived_qty); ?></td>
                    <td width="80"><? echo number_format($sumEmb_qty); ?></td>
                    <td width="80"><? echo number_format($sumEmbRec_qty); ?></td>

                    <td width="80"><? echo number_format($sum_embGmt_qty); ?></td>
                    <td width="80"><? echo number_format($sum_embGmtRec_qty); ?></td>

                    <td width="80"><? echo number_format($sumWash_qty); ?></td>
                    <td width="80"><? echo number_format($sumWashRec_qty); ?></td>
                    <td width="80"><? echo number_format($sumSpecial_qty); ?></td>
                    <td width="80"><? echo number_format($sumSpecialRec_qty); ?></td>

                    <td width="80"><? echo number_format($sum_cut_delv);?></td>
                    <td width="80"><? echo number_format($sumSewIn_inQty); ?></td>
                    <td width="80"><? echo number_format($sumSewIn_outQty); ?></td>
                    <td width="80"><? echo number_format($sumSewOut_inQty); ?></td>
                    <td width="80"><? echo number_format($sumSewOut_outQty); ?></td>
                    <td width="80"><? echo number_format($sumIron_qty); ?></td>
                    <td width="80"><? echo number_format($sumReIron_qty); ?></td>
                    <td><? echo number_format($sumFinish_qty); ?></td>
                 </tr>
             </table>
        </div>
        <div style="clear:both"></div>
        <br /> <?
        if($type==1) //--------------------------------------------Show Date Wise
        {
            ?>
            <table width="5242" cellspacing="0" border="1" class="rpt_table" rules="all" id="">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80">Company</th>
                        <th width="60">Job No</th>
                        <th width="60">Year</th>
                        <th width="130">Order Number</th>
                        <th width="100">Unit Price</th>
                        <th width="70">Sewing SMV</th>
                        <th width="70">Finish SMV</th>
                        <th width="70">Buyer Name</th>
                        <th width="140">Style Name</th>
                        <th width="80">Order Qty</th>
                        <th width="100">File No</th>
                        <th width="100">Internal Ref</th>
                        <th width="150">Item Name</th>
                        <th width="100">Production Date</th>
                        <th width="80">Cutting (In-house)</th>
                        <th width="80">Cutting (Outside)</th>
                        <th width="80">Sent to Print</th>
                        <th width="80">Rev Print</th>
                        <th width="80">Sent to Emb</th>
                        <th width="80">Rev Emb</th>
                        <th width="80">Sent to Gmts Dyeing</th>
                        <th width="80">Rev. Gmts Dyeing</th>

                        <th width="80">Sent to Wash</th>
                        <th width="80">Rev Wash</th>
                        <th width="80">Sent to Sp. Works</th>
                        <th width="80">Rev Sp. Works</th>

                        <th width="80">Cut Delv. Qty</th>


                        <th width="80">Sewing In (Inhouse)</th>
                        <th width="80">Sewing In (Out-bound)</th>
                        <th width="80">Total Sewing Input</th>

                        <th width="80">Sewing Out (Inhouse)</th>
                        <th width="80">Sewing Out (Out-bound)</th>
                        <th width="80">Total Sewing Out</th>
                        <th width="80">Sewing SAH(Inhouse)</th>
                        <th width="80">Sewing SAH(Out-bound)</th>
                        <th width="80">Sewing SAH</th>
                        <th width="80">Sewing Company</th>

                        <th width="80">Iron Qty (Inhouse)</th>
                        <th width="80">Iron Qty (Out-bound)</th>
                        <th width="80">Total Iron Qty</th>
                        <th width="80">Total Iron SMV</th>
                        <th width="80">Re-Iron Qty </th>
                        <th width="80">Total Finishing Qty</th>
                        <th width="80">Today Carton</th>
                        <th width="80">In Prod/Dzn</th>
                        <th width="80">Out Prod/Dzn</th>
                        <th width="80">Total Prod/Dzn</th>
                        <th width="100" title="((Cm Value(dzn)/Costing Per)*Sewing Out)">In CM Value</th>
                        <th width="100" title="((Cm Value(dzn)/Costing Per)*Sewing Out)">Out CM Value</th>
                        <th width="100">Total CM Value</th>
                        <th width="100">FOB value (Inhouse)</th>
                        <th width="100">FOB value (Out-bound)</th>
                        <th width="100">FOB value (On Sewing Out Total)</th>
                        <th width="100">In CM Cost</th>
                        <th width="100">Out CM Cost</th>
                        <th width="100">Total CM Cost</th>
                        <th width="100">Turnover</th>
                        <th width="100">Avg Booking Con (Fin)</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style="max-height:425px; overflow-y:scroll; width:5260px" id="scroll_body">
                        <table cellspacing="0" border="1" class="rpt_table"  width="5242" rules="all" id="table_body" >
                            <?
                            $i=1;
                            //print_r($booking_fin_qty_arr);
                            // echo "<pre>";
                            // print_r($data_smv_array);die;

                            $tot_embgmtIssue_qty=$tot_embgmtRec_qty=0;
                            foreach($all_data_arr as $po_id=>$po_data)
                            {
                                foreach($po_data as $prod_date=>$prod_date_data)
                                {
                                    $ex_itemdata='';
                                    $ex_itemdata=array_filter(array_unique(explode('__',$prod_date_data)));
                                    foreach($ex_itemdata as $data_all)
                                    {
                                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                        $buyer_name_dat=""; $job_no_pre=''; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no=''; $unit_price=''; $po_qty=''; $ratio=''; $order_uom=''; $job_year=''; $all_job_data=''; $all_job_data='';
                                        $all_job_data=$job_arr[$po_id];

                                        $ex_job_data=explode('!!',$all_job_data);

                                        $job_no_pre=$ex_job_data[0];
                                        $job_no=$ex_job_data[1];
                                        $buyer_name_dat=$ex_job_data[2];
                                        $style_ref=$ex_job_data[3];
                                        $po_no=$ex_job_data[4];
                                        $ref_no=$ex_job_data[5];
                                        $file_no=$ex_job_data[6];
                                        $unit_price=number_format($ex_job_data[7],2);
                                        $po_qty=$ex_job_data[8];
                                        $ratio=$ex_job_data[9];
                                        $order_uom=$ex_job_data[10];
                                        $job_year=$ex_job_data[11];

                                        $item_id=''; $company_id='';
                                        $ex_data=array_filter(explode('**',$data_all));
                                        $item_id=$ex_data[0];
                                        $company_id=$ex_data[1];
                                        $serving_company=$ex_data[2];

                                        $cutting_qty=$cuttingIn_qty=$cuttingOut_qty=$print_qty=$printRec_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewing_input_total=$sewOut_inQty=$sewOut_outQty=$sewing_output_total=$ironIn_qty=$ironOut_qty=$iron_qty_total=$reIron_qty=$finishIn_qty=$finishOut_qty=$finishing_qty=$carton_qty=$rejFinish_qty=$rejSewing_qty=$reject_Qty=0;
                                        $cutting_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['1']['0']['pQty'];
                                        $cuttingIn_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['1']['1']['sQty'];
                                        $cuttingOut_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['1']['3']['sQty'];
                                        $print_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['2']['1']['embQty'];
                                        $printRec_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['3']['1']['embQty'];
                                        $emb_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['2']['2']['embQty'];
                                        $embRec_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['3']['2']['embQty'];

                                        $embgmtRec_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['3']['5']['embQty'];
                                        $embgmtIssue_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['2']['5']['embQty'];

                                        $wash_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['2']['3']['embQty'];
                                        $washRec_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['3']['3']['embQty'];
                                        $special_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['2']['4']['embQty'];
                                        $specialRec_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['3']['4']['embQty'];
                                        $sewIn_inQty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['4']['1']['sQty'];
                                        $sewIn_outQty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['4']['3']['sQty'];
                                        $sewOut_inQty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['5']['1']['sQty'];
                                        $sewOut_outQty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['5']['3']['sQty'];
                                        $ironIn_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['7']['1']['sQty'];
                                        $ironOut_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['7']['3']['sQty'];
                                        $reIron_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['7']['0']['reQty'];
                                        $finishIn_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['8']['1']['sQty'];
                                        $finishOut_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['8']['3']['sQty'];
                                        $carton_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['0']['0']['crtQty'];
                                        $rejFinish_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['8']['0']['rejectQty'];
                                        $rejSewing_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['5']['0']['rejectQty'];

                                        $cutDelvQty=$cut_delv_qty_arr[$po_id][$prod_date]["cut_delivery_qnty"];

                                        $serving_company = $po_info_array[$po_id][$prod_date][1];
                                        $serving_company_out = $po_info_array[$po_id][$prod_date][3];
                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                            <td width="30"><? echo $i;?></td>
                                            <td width="80"><p><? echo $company_short_library[$company_id]; ?></p></td>
                                            <td width="60" align="center"><p><? echo $job_no_pre;?></p></td>
                                            <td width="60" align="center"><p><? echo $job_year; ?></p></td>
                                            <td width="130"><p><a href="##" onClick="openmypage_order(<? echo $po_id;?>,<? echo $item_id;?>,'orderQnty_popup');" ><? echo $po_no; ?></a></p></td>
                                            <td width="100" align="center"><p><? echo $unit_price; ?></p></td>
                                            <td width="70" align="center"><p><? echo $job_item_smv_arr[$po_id][$item_id]['smv_pcs']; ?></p></td>
                                            <td width="70" align="center"><p><? echo $job_item_smv_arr[$po_id][$item_id]['finsmv_pcs']; ?></p></td>
                                            <td width="70"><p><? echo $buyer_short_library[$buyer_name_dat]; ?></p></td>
                                            <td width="140"><p><? echo $style_ref ?></p></td>
                                            <td width="80" align="right"><p><? echo number_format($po_qty,0); ?></p></td>
                                            <td width="100" align="center"><p><? echo $file_no; ?></p></td>
                                            <td width="100" align="center"><p><? echo $ref_no; ?></p></td>
                                            <td width="150"><p><? echo $garments_item[$item_id]; ?></p></td>
                                            <td width="100" align="center"><p><? echo '&nbsp;'.change_date_format($prod_date); ?></p></td>
                                            <td width="80" align="right"><a href="##" onClick="openmypage_popup('<? echo $po_id; ?>','<? echo $prod_date; ?>','<? echo $item_id;?>','1','Cutting Info','cutting_popup');" ><? echo $cuttingIn_qty; ?></a></td>
                                            <td width="80" align="right"><a href="##" onClick="openmypage_popup('<? echo $po_id; ?>','<? echo $prod_date; ?>','<? echo $item_id;?>','3','Cutting Info','cutting_popup');" ><? echo $cuttingOut_qty; ?></a></td>
                                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Printing Issue Info','printing_issue_popup');" ><? echo $print_qty; ?></a></td>
                                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Priniting Receive Info','printing_receive_popup');" ><? echo $printRec_qty; ?></a></td>
                                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Embroidery Issue Info','embroi_issue_popup');" ><? echo $emb_qty; ?></a></td>
                                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Embroidery Receive Info','embroi_receive_popup');" ><? echo $embRec_qty; ?></a></td>

                                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'2','Embroidery Receive Info','embroi_gmt_dyeing_popup');" ><? echo $embgmtIssue_qty; ?></a></td>

                                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','Embroidery Receive Info','embroi_gmt_dyeing_popup');" ><? echo $embgmtRec_qty; ?></a></td>

                                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Wash Issue Info','wash_issue_popup');" ><? echo $wash_qty; ?></a></td>
                                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Wash Receive Info','wash_receive_popup');" ><? echo $washRec_qty; ?></a></td>
                                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Special Works Issue Info','sp_issue_popup');" ><? echo $special_qty; ?></a></td>
                                            <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','Special Works Receive Info','sp_receive_popup');" ><? echo $specialRec_qty; ?></a></td>

                                            <td width="80" align="right"><? echo $cutDelvQty; ?></td>

                                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','4','sewingQnty_popup');" ><? echo $sewIn_inQty; ?></a></td>
                                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','4','sewingQnty_popup');" ><? echo $sewIn_outQty; ?></a></td>
                                            <td width="80" align="right"><? $sewing_input_total=$sewIn_inQty+$sewIn_outQty; echo $sewing_input_total; ?></td>
                                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','5','sewingQnty_popup');" ><? echo $sewOut_inQty; ?></a></td>
                                            <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','5','sewingQnty_popup');" ><? echo $sewOut_outQty; ?></a></td>
                                            <td width="80" align="right"><? $sewing_output_total=$sewOut_inQty+$sewOut_outQty; echo $sewing_output_total; ?></td>
                                                <?
                                                $sew_out_smv=($sewOut_outQty*$job_item_smv_arr[$po_id][$item_id]['smv_pcs'])/60;
                                                $sew_in_smv=($sewOut_inQty*$job_item_smv_arr[$po_id][$item_id]['smv_pcs'])/60;
                                                ?>
                                            <td width="80" align="right"><? echo number_format($sew_in_smv,4); ?></td>
                                            <td width="80" align="right"><? echo number_format($sew_out_smv,4); ?></td>

                                            <td width="80" align="right"><?
                                                // $sew_out_smv=($sewOut_outQty*$job_item_smv_arr[$po_id][$item_id]['smv_pcs'])/60;
                                                // $sew_in_smv=($sewOut_inQty*$job_item_smv_arr[$po_id][$item_id]['smv_pcs'])/60;
                                                //echo number_format($sah_out+$sah_in,4);
                                                echo number_format($sew_out_smv+$sew_in_smv,4);
                                                ?></td>

                                                <td width="80" align="left">
                                                    <?
                                                    $wo_com_name = "";
                                                    $wo_com_ex = array_filter(array_unique(explode(",", $serving_company)));
                                                    foreach ($wo_com_ex as $val)
                                                    {
                                                        $wo_com_name .= ($wo_com_name=="") ? $company_short_library[$val] : ", ".$company_short_library[$val];
                                                    }

                                                    $wo_com_out_ex = array_filter(array_unique(explode(",", $serving_company_out)));
                                                    foreach ($wo_com_out_ex as $val)
                                                    {
                                                        $wo_com_name .= ($wo_com_name=="") ? $supplier_short_library[$val] : ", ".$supplier_short_library[$val];
                                                    }
                                                    echo $wo_com_name;
                                                    /*if(isset($company_short_library[$serving_company]) && isset($supplier_short_library[$serving_company_out]))
                                                    {
                                                        echo $company_short_library[$serving_company].", ".$supplier_short_library[$serving_company_out];
                                                    }
                                                    elseif(isset($company_short_library[$serving_company]))
                                                    {
                                                        echo $company_short_library[$serving_company];
                                                    }
                                                    elseif(isset($supplier_short_library[$serving_company_out]))
                                                    {
                                                        echo $supplier_short_library[$serving_company_out];
                                                    }*/
                                                    ?>

                                                </td>

                                                <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','0','ironQnty_popup');" ><? echo $ironIn_qty; ?></a></td>
                                                <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','0','ironQnty_popup');" ><? echo $ironOut_qty; ?></a></td>
                                                <td width="80" align="right"><? $iron_qty_total=$ironIn_qty+$ironOut_qty; echo $iron_qty_total; ?> </td>
                                                <td width="80" align="right"><? $iron_smv=($iron_qty_total*$job_item_smv_arr[$po_id][$item_id]['finsmv_pcs']); echo $iron_smv; ?></td>
                                                <td width="80" align="right"><? echo $reIron_qty; ?></td>
                                                <td width="80" align="right"><? $finishing_qty=$finishIn_qty+$finishOut_qty; echo $finishing_qty; ?></td>
                                                <td width="80" align="right"><? echo $carton_qty; ?></td>
                                                <?
                                                $in_prod_dzn=$sewOut_inQty/ 12; $total_in_prod_dzn+=$in_prod_dzn;
                                                $out_prod_dzn=$sewOut_outQty/ 12; $total_out_prod_dzn+=$out_prod_dzn;
                                                $prod_dzn=$sewing_output_total/ 12; $total_prod_dzn+=$prod_dzn;

                                                $dzn_qnty=0; $in_cm_value=0; $out_cm_value=0; $cm_value=0; $in_cm_cost=0; $out_cm_cost=0; $cm_cost=0;
                                                if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
                                                else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
                                                else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
                                                else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
                                                else $dzn_qnty=1;
                                        //echo $dzn_qnty."***";
                                                $cm_cost_margin=($tot_cm_cost_arr[$job_no]/$dzn_qnty)+$tot_margin_pcs_arr[$job_no];
                                                $dzn_qnty=$dzn_qnty*$ratio;

                                                $in_cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewOut_inQty;
                                                $total_in_cm_value+=$in_cm_value;

                                                $out_cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewOut_outQty;
                                                $total_out_cm_value+=$out_cm_value;

                                                $cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_output_total;
                                                $total_cm_value+=$cm_value;

                                                $in_cm_cost=($tot_cm_cost_arr[$job_no]/$dzn_qnty)*$sewOut_inQty;
                                                $total_in_cm_cost+=$in_cm_cost;

                                                $out_cm_cost=($tot_cm_cost_arr[$job_no]/$dzn_qnty)*$sewOut_outQty;
                                                $total_out_cm_cost+=$out_cm_cost;

                                                $cm_cost=($tot_cm_cost_arr[$job_no]/$dzn_qnty)*$sewing_output_total;
                                                $total_cm_cost+=$cm_cost;
                                                ?>
                                                <td width="80" align="right"><? if($in_prod_dzn!=0) echo number_format($in_prod_dzn,2); else echo "0"; ?></td>
                                                <td width="80" align="right"><? if($out_prod_dzn!=0) echo number_format($out_prod_dzn,2); else echo "0"; ?></td>
                                                <td width="80" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>
                                                <td width="100" align="right" title="((Cm Value(dzn)/Costing Per)*Sewing Out"><? echo number_format($in_cm_value,2,'.',''); ?></td>
                                                <td width="100" align="right" title="((Cm Value(dzn)/Costing Per)*Sewing Out"><? echo number_format($out_cm_value,2,'.',''); ?></td>
                                                <td width="100" align="right" title="(Budget Wise CM For Shipment Schedule/Dzn Qty)*Total Sewing Out"><? echo   number_format($cm_value,2,'.','');?></td>

                                                <td width="100" align="right"><? echo number_format($unit_price*$sewOut_inQty,2,'.','');?></td>
                                                <td width="100" align="right"><? echo number_format($unit_price*$sewOut_outQty,2,'.','');?></td>

                                                <td width="100" align="right" title="Job Wise Unit Price*Total Sewing Out"><? echo number_format($unit_price*$sewing_output_total,2,'.',''); $total_cm_value+=$unit_price*$sewing_output_total; ?></td>
                                                <td width="100" align="right" ><? echo number_format($in_cm_cost,2,'.',''); ?></td>
                                                <td width="100" align="right" ><? echo number_format($out_cm_cost,2,'.',''); ?></td>
                                                <td width="100" align="right" title="(Budget Wise CM Cost/Dzn Qnty)*Total Sewing Out"><? echo number_format($cm_cost,2,'.',''); ?></td>
                                                <td width="100" align="right" title="[Finish Qty * {(Budget Wise CM Cost/ Dzn Qty) + Budget Wise Margin Pcs Per Set}]"><? echo number_format($finishing_qty*$cm_cost_margin,2,'.',''); ?></td>

                                                <td width="100" align="right"><? echo number_format($booking_fin_qty_arr[$job_no][$item_id]['fin_fab_qnty'],4); ?></td>

                                                <td align="center"><a href="##"  onclick="openmypage_remark(<? echo $po_id;?>,'date_wise_production_report');" > View </a></td>
                                            </tr>
                                            <?
                                            $all_order_sewQty_array[$po_id]+=$sewing_output_total;
                                            $tot_cutting_qty+=$cutting_qty;
                                            $tot_cutIn_qty+=$cuttingIn_qty;
                                            $tot_cutOut_qty+=$cuttingOut_qty;
                                            $tot_print_qty+=$print_qty;
                                            $tot_printRec_qty+=$printRec_qty;
                                            $tot_emb_qty+=$emb_qty;
                                            $tot_embRec_qty+=$embRec_qty;
                                            $tot_wash_qty+=$wash_qty;
                                            $tot_washRec_qty+=$washRec_qty;
                                            $tot_special_qty+=$special_qty;
                                            $tot_specialRec_qty+=$specialRec_qty;
                                            $tot_sewIn_inQty+=$sewIn_inQty;
                                            $tot_sewIn_outQty+=$sewIn_outQty;
                                            $tot_sewing_input+=$sewing_input_total;
                                            $tot_sewOut_inQty+=$sewOut_inQty;
                                            $tot_sewOut_outQty+=$sewOut_outQty;
                                            $tot_sewing_output+=$sewing_output_total;
                                            $tot_ironIn_qty+=$ironIn_qty;
                                            $tot_ironOut_qty+=$ironOut_qty;
                                            $tot_iron_qty+=$iron_qty_total;
                                            $tot_reIron_qty+=$reIron_qty;
                                            $tot_finishIn_qty+=$finishIn_qty;
                                            $tot_finishOut_qty+=$finishOut_qty;
                                            $tot_finishing_qty+=$finishing_qty;
                                            $tot_carton_qty+=$carton_qty;
                                            $total_prod_dzn+=$prod_dzn;
                                            $tot_rejFinish_qty+=$rejFinish_qty;
                                            $tot_rejSewing_qty+=$rejSewing_qty;
                                            $tot_reject_Qty+=$reject_Qty;

                                            $tot_embgmtIssue_qty+=$embgmtIssue_qty;
                                            $tot_embgmtRec_qty+=$embgmtRec_qty;
                                            $tot_cutDelvQty+=$cutDelvQty;
                                            $i++;
                                        }
                                    }
                        }//end foreach 1st
                        ?>
                    </table>
            </div>
                <table border="1" class="tbl_bottom"  width="5242" rules="all" id="" >
                    <tr>
                        <td width="30">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td width="130">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="140">Total</td>
                        <td width="80" align="right" id="total_order_qty"></td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="150">&nbsp;</td>
                        <td width="100"></td>
                        <td width="80" align="right" id="total_cut_inhouse"><? echo $tot_cutIn_qty;?></td>
                        <td width="80" align="right" id="total_cut_outside"><? echo $tot_cutOut_qty;?></td>
                        <td width="80" align="right" id="total_printissue_td"><? echo $tot_print_qty; ?></td>
                        <td width="80" align="right" id="total_printrcv_td"><? echo $tot_printRec_qty; ?></td>
                        <td width="80" align="right" id="total_emb_iss"><? echo $tot_emb_qty; ?></td>
                        <td width="80" align="right" id="total_emb_re"><? echo $tot_embRec_qty;  ?></td>
                        <td width="80" align="right" id="total_emb_sent_gmt"><? echo $tot_embgmtIssue_qty;  ?></td>
                        <td width="80" align="right" id="total_emb_rec_gmt"><? echo $tot_embgmtRec_qty;  ?></td>
                        <td width="80" align="right" id="total_wash_iss"><? echo $tot_wash_qty; ?></td>
                        <td width="80" align="right" id="total_wash_re"><? echo $tot_washRec_qty;  ?></td>
                        <td width="80" align="right" id="total_sp_iss"><? echo $tot_special_qty; ?></td>
                        <td width="80" align="right" id="total_sp_re"><? echo $tot_specialRec_qty;  ?></td>


                        <td width="80" align="right" id="total_cut_delv_qty_td"><? echo $tot_cutDelvQty; ?></td>


                        <td width="80" align="right" id="total_sewin_inhouse_td"><? echo $tot_sewIn_inQty; ?></td>
                        <td width="80" align="right" id="total_sewin_outbound_td"><? echo $tot_sewIn_outQty; ?></td>
                        <td width="80" align="right" id="total_sewin_td"><? echo $tot_sewing_input; ?></td>
                        <td width="80" align="right" id="total_sewout_inhouse_td"><? echo $tot_sewOut_inQty; ?></td>
                        <td width="80" align="right" id="total_sewout_outbound_td"><? echo $tot_sewOut_outQty; ?></td>
                        <td width="80" align="right" id="total_sewout_td"><? echo $tot_sewing_output; ?></td>


                        <td width="80" align="right" id="total_sewout_sah_in_td"><? //echo $tot_sewing_output; ?></td>
                        <td width="80" align="right" id="total_sewout_sah_out_td"><? //echo $tot_sewing_output; ?></td>

                        <td width="80" id="total_sah_td">&nbsp;</td>
                        <td width="80" id="">&nbsp;</td>
                        <td width="80" align="right" id="total_in_iron_td"><? echo $tot_ironIn_qty; ?></td>
                        <td width="80" align="right" id="total_out_iron_td"><? echo $tot_ironOut_qty; ?></td>
                        <td width="80" align="right" id="total_iron_td"><? echo $tot_iron_qty; ?></td>
                        <td width="80" align="right" id="total_iron_smv_td"><? echo $total_iron_smv; ?></td>
                        <td width="80" align="right" id="total_re_iron_td"><? echo $tot_reIron_qty; ?></td>
                        <td width="80" align="right" id="total_finish_td"><? echo $tot_finishing_qty; ?></td>
                        <td width="80" align="right" id="total_carton_td"><? echo $tot_carton_qty; ?></td>
                        <td width="80" align="right" id="value_total_in_prod_dzn_td"><? echo number_format($total_in_prod_dzn,2); ?></td>
                        <td width="80" align="right" id="value_total_out_prod_dzn_td"><? echo number_format($total_out_prod_dzn,2); ?></td>
                        <td width="80" align="right" id="value_total_prod_dzn_td"><?  echo number_format($total_prod_dzn,2); ?></td>
                        <td width="100" align="right" id="value_total_in_cm_value_td"><? echo number_format($total_in_cm_value,2); ?></td>
                        <td width="100" align="right" id="value_total_out_cm_value_td"><? echo number_format($total_out_cm_value,2); ?></td>
                        <td width="100" align="right" id="value_total_cm_value_td"><? echo number_format($total_cm_value,2); ?></td>

                        <td width="100" align="right" id="value_total_fob_in_value"><? //echo number_format($total_cm_value,2); ?></td>
                        <td width="100" align="right" id="value_total_fob_out_value"><? //echo number_format($total_cm_value,2); ?></td>

                        <td width="100" align="right" id="value_total_fob_value"><? echo number_format($total_fob_value,2); ?></td>
                        <td width="100" align="right" id="value_total_in_cm_cost"><? echo number_format($total_in_cm_cost,2); ?></td>
                        <td width="100" align="right" id="value_total_out_cm_cost"><? echo number_format($total_out_cm_cost,2); ?></td>
                        <td width="100" align="right" id="value_total_cm_cost"><? echo number_format($total_cm_cost,2); ?></td>
                        <td width="100">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                   </tr>
                </table>

            <?
        }// end if condition of type //-----------Show Date Location Floor & Line Wise------------------------
        else if($type==2)
        {
            ob_start();
            ?>
            <table width="4762" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                <thead>
                    <th width="30">SL</th>
                    <th width="80">Company</th>
                    <th width="60">Job No</th>
                    <th width="60">Year</th>
                    <th width="130">Order Number</th>
                    <th width="60">Unit Price</th>
                    <th width="70">Sewing SMV</th>
                    <th width="70">Iron SMV</th>
                    <th width="70">Buyer Name</th>
                    <th width="130">Style Name</th>
                    <th width="100">File No</th>
                    <th width="100">Internal Ref</th>
                    <th width="130">Item Name</th>
                    <th width="100">Production Date</th>
                    <th width="100">Status</th>
                    <th width="100">Location</th>
                    <th width="100">Floor</th>
                    <th width="100">Sewing Line No</th>
                    <th width="80">Cutting (In-house)</th>
                    <th width="80">Cutting (Outside)</th>
                    <th width="80">Sent to Print</th>
                    <th width="80">Rev Print</th>
                    <th width="80">Sent to Emb</th>
                    <th width="80">Rev Emb</th>

                    <th width="80">Sent to Gmts Dyeing</th>
                    <th width="80"> Rev. Gmts Dyeing</th>

                    <th width="80">Sent to Wash</th>
                    <th width="80">Rev Wash</th>
                    <th width="80">Sent to Sp. Works</th>
                    <th width="80">Rev Sp. Works</th>

                    <th width="80">Cut Delv. Qty</th>

                    <th width="80">Sewing In (Inhouse)</th>
                    <th width="80">Sewing In (Out-bound)</th>
                    <th width="80">Total Sewing Input</th>
                    <th width="80">Sewing Out (Inhouse)</th>
                    <th width="80">Sewing Out (Out-bound)</th>
                    <th width="80">Total Sewing Out</th>
                    <th width="80">FOB Value</th>
                    <th width="80">Iron Qty (Inhouse)</th>
                    <th width="80">Iron Qty (Out-bound)</th>
                    <th width="80">Total Iron Qty </th>
                    <th width="80">Total Iron SMV </th>
                    <th width="80">Re-Iron Qty </th>
                    <th width="80">Total Finishing Qty</th>
                    <th width="80">Today Carton</th>

                    <th width="80">In Prod/Dzn</th>
                    <th width="80">Out Prod/Dzn</th>
                    <th width="80">Total Prod/Dzn</th>
                    <th width="100">In CM Value</th>
                    <th width="100">Out CM Value</th>
                    <th width="100">Total CM Value</th>
                    <th width="100">In CM Cost</th>
                    <th width="100">Out CM Cost</th>
                    <th width="100">Total CM Cost</th>
                    <th>Remarks</th>
                </thead>
            </table>
            <div style="max-height:425px; overflow-y:scroll; width:4780px" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="4762" rules="all" id="table_body" >
            <?
            $i=1;
            $tot_embgmtIssue_qty=$tot_embgmtRec_qty=0;
            foreach($all_data_arr as $po_id=>$po_data)
            {
                foreach($po_data as $prod_date=>$prod_date_data)
                {
                    foreach($prod_date_data as $item_id=>$item_data)
                    {
                        foreach($item_data as $location_id=>$location_data)
                        {
                            foreach($location_data as $floor_id=>$floor_data)
                            {
                                $ex_linedata='';
                                $ex_linedata=array_filter(array_unique(explode('__',$floor_data)));
                                foreach($ex_linedata as $data_all)
                                {
                                    //$row[csf("sewing_line")].'##'.$row[csf("company_id")].'##'.$row[csf("production_source")].'##'.$row[csf("prod_reso_allo")].'__';

                                    $buyer_name_dat=""; $job_no_pre=''; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no=''; $unit_price=''; $po_qty=''; $ratio=''; $order_uom=''; $job_year=''; $all_job_data=''; $all_job_data='';
                                    $all_job_data=$job_arr[$po_id];

                                    $ex_job_data=explode('!!',$all_job_data);

                                    $job_no_pre=$ex_job_data[0];
                                    $job_no=$ex_job_data[1];
                                    $buyer_name_dat=$ex_job_data[2];
                                    $style_ref=$ex_job_data[3];
                                    $po_no=$ex_job_data[4];
                                    $ref_no=$ex_job_data[5];
                                    $file_no=$ex_job_data[6];
                                    $unit_price=number_format($ex_job_data[7],2);
                                    $po_qty=$ex_job_data[8];
                                    $ratio=$ex_job_data[9];
                                    $order_uom=$ex_job_data[10];
                                    $job_year=$ex_job_data[11];

                                    $line_id=''; $company_id=''; $prod_source=''; $resource_allo='';
                                    $ex_data=array_filter(explode('##',$data_all));
                                    //echo $floor_id.'='; //die;
                                    if($ex_data[0]=="") $line_id=0;
                                    else $line_id=$ex_data[0];
                                    $company_id=$ex_data[1];
                                    $prod_source=$ex_data[2];
                                    $resource_allo=$ex_data[3];

                                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                                    $cuttingIn_qty=$cuttingOut_qty=$print_qty=$printRec_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewing_input_total=$sewOut_inQty=$sewOut_outQty=$sewing_output_total=$ironIn_qty=$ironOut_qty=$iron_qty_total=$reIron_qty=$finishIn_qty=$finishOut_qty=$finishing_qty=$carton_qty=$rejFinish_qty=$rejSewing_qty=$reject_Qty=0;
                                    $cuttingIn_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['1']['1']['sQty'];
                                    $cuttingOut_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['1']['3']['sQty'];
                                    $print_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['2']['1']['embQty'];
                                    $printRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['3']['1']['embQty'];
                                    $emb_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['2']['2']['embQty'];
                                    $embRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['3']['2']['embQty'];

                                    $emb_gmt_issue_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['2']['5']['embQty'];
                                    $emb_gmt_recv_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['3']['5']['embQty'];

                                    $wash_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['2']['3']['embQty'];
                                    $washRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['3']['3']['embQty'];
                                    $special_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['2']['4']['embQty'];
                                    $specialRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['3']['4']['embQty'];

                                    $sewIn_inQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['4']['1']['sQty'];
                                    $sewIn_outQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['4']['3']['sQty'];
                                    $sewOut_inQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['5']['1']['sQty'];
                                    $sewOut_outQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['5']['3']['sQty'];

                                    $ironIn_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['7']['1']['sQty'];
                                    $ironOut_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['7']['3']['sQty'];
                                    $reIron_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['7']['0']['reQty'];
                                    $finishIn_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['8']['1']['sQty'];
                                    $finishOut_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['8']['3']['sQty'];
                                    $carton_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['0']['0']['crtQty'];
                                    $rejFinish_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['8']['0']['rejectQty'];
                                    $rejSewing_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['5']['0']['rejectQty'];
                                    $cutDelvQty=$cut_delv_qty_arr[$po_id]["cut_delivery_qnty"];

                                    $sewing_line='';
                                    if($resource_allo==1)
                                    {
                                        $line_number=explode(",",$prod_reso_arr[$line_id]);
                                        foreach($line_number as $val)
                                        {
                                            if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
                                        }
                                    }
                                    else $sewing_line=$line_library[$line_id];
                                    //$item_smv=$smv_qty_arr[$pro_date_sql_row[csf("job_no_mst")]][$pro_date_sql_row[csf("item_number_id")]];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                        <td width="30"><? echo $i;?></td>
                                        <td width="80"><p><? echo $company_short_library[$company_id]; ?></p></td>
                                        <td width="60" align="center"><p><? echo $job_no_pre; ?></p></td>
                                        <td width="60" align="center"><p><? echo $job_year;?></p></td>
                                        <td width="130"><p><a href="##" onClick="openmypage_order(<? echo $po_id;?>,<? echo $item_id; ?>,'orderQnty_popup');" ><? echo $po_no; ?></a></p></td>
                                        <td width="60" align="center"><p><? echo $unit_price; ?></p></td>
                                        <td width="70" align="center"><p><? echo $job_item_smv_arr[$po_id][$item_id]["smv_pcs"]; ?></p></td>
                                        <td width="70" align="center"><p><? echo $job_item_smv_arr[$po_id][$item_id]["finsmv_pcs"]; ?></p></td>
                                        <td width="70"><p><? echo $buyer_short_library[$buyer_name_dat]; ?></p></td>
                                        <td width="130"><p><? echo $style_ref; ?></p></td>
                                        <td width="100"><p><? echo $file_no; ?></p></td>
                                        <td width="100"><p><? echo $ref_no; ?></p></td>
                                        <td width="130"><p><? echo $garments_item[$item_id]; ?></p></td>
                                        <td width="100" align="center"><p><? echo '&nbsp;'.change_date_format($prod_date); ?></p></td>
                                        <td width="100"><p><? echo $knitting_source[$prod_source]; ?></p></td>
                                        <td width="100"><p><? echo $location_library[$location_id]; ?></p></td>
                                        <td width="100"><p><? echo $floor_library[$floor_id]; ?></p></td>
                                        <td width="100" align="center" title="<? echo $line_id; ?>"><p><? echo $sewing_line; ?></p></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location('<? echo $po_id; ?>','<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location_id;?>','<? echo $floor_id;?>','<? echo $line_id;?>','1','Cutting Info','cutting_popup_location');" ><? echo $cuttingIn_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location('<? echo $po_id; ?>','<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location_id;?>','<? echo $floor_id;?>','<? echo $line_id;?>','3','Cutting Info','cutting_popup_location');"><? echo $cuttingOut_qty;?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'1','Printing Issue Info','printing_issue_popup_location');"><? echo $print_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'3','Printing Receive Info','printing_receive_popup_location');" ><? echo $printRec_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'1','Embroidery Issue Info','embroi_issue_popup_location');" ><? echo $emb_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'3','Embroidery Receive Info','embroi_receive_popup_location');" ><? echo $embRec_qty; ?></a></td>

                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'2','Embroidery Issue Info','embroi_gmt_dyeing_popup_location');" ><? echo $emb_gmt_issue_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'3','Embroidery Receive Info','embroi_gmt_dyeing_popup_location');" ><? echo $emb_gmt_recv_qty; ?></a></td>

                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'1','Wash Issue Info','wash_issue_popup_location');" ><? echo $wash_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'3','Wash Receive Info','wash_receive_popup_location');" ><? echo $washRec_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'1','Spetial Work Info','sp_issue_popup_location');" ><? echo $special_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>,'3','Spetial Work Info','sp_receive_popup_location');" ><? echo $specialRec_qty; ?></a></td>

                                         <td width="80" align="right"><? echo $cutDelvQty; ?></td>


                                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','4','sewingQnty_popup');" ><? echo $sewIn_inQty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','4','sewingQnty_popup');" ><? echo $sewIn_outQty; ?></a></td>
                                        <td width="80" align="right"><? $sewing_input_total=$sewIn_inQty+$sewIn_outQty; echo $sewing_input_total; ?></td>
                                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','5','sewingQnty_popup',<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $line_id;?>);" ><? echo $sewOut_inQty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','5','sewingQnty_popup');" ><? echo $sewOut_outQty; ?></a></td>
                                        <td width="80" align="right"><? $sewing_output_total=$sewOut_inQty+$sewOut_outQty; echo $sewing_output_total; ?></td>
                                        <td width="80" align="right"><? $fob_value=($sewOut_inQty+$sewOut_outQty)*$unit_price; echo $fob_value; ?></td>
                                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','0','ironQnty_popup');" ><? echo $ironIn_qty; ?></a></td>
                                        <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','0','ironQnty_popup');" ><? echo $ironOut_qty; ?></a></td>
                                        <td width="80" align="right"><? $iron_qty_total=$ironIn_qty+$ironOut_qty; echo $iron_qty_total; ?></td>
                                        <td width="80" align="right"><? $iron_smv=($iron_qty_total*$job_item_smv_arr[$po_id][$item_id]['finsmv_pcs']); echo $iron_smv; ?></td>
                                        <td width="80" align="right"><? echo $reIron_qty; ?></td>
                                        <td width="80" align="right"><? $finishing_qty=$finishIn_qty+$finishOut_qty; echo $finishing_qty; ?></td>
                                        <td width="80" align="right"><? echo $carton_qty; ?></td>
                                        <?
                                        $in_prod_dzn=$sewOut_inQty/12;
                                        $total_in_prod_dzn+=$in_prod_dzn;

                                        $out_prod_dzn=$sewOut_outQty/12;
                                        $total_out_prod_dzn+=$out_prod_dzn;

                                        $prod_dzn=$sewing_output_total/ 12 ;
                                        $total_prod_dzn+=$prod_dzn;

                                        $dzn_qnty=0; $cm_value=0; $cm_cost=0;
                                        if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
                                        else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
                                        else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
                                        else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
                                        else $dzn_qnty=1;

                                        $dzn_qnty=$dzn_qnty*$pro_date_sql_row[csf('ratio')];

                                        $in_cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewOut_inQty;
                                        $total_in_cm_value+=$in_cm_value;

                                        $out_cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewOut_outQty;
                                        $total_out_cm_value+=$out_cm_value;

                                        $cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_output_total;
                                        $total_cm_value+=$cm_value;

                                        $in_cm_cost=($tot_cm_cost_arr[$job_no]/$dzn_qnty)*$sewOut_inQty;
                                        $total_in_cm_cost+=$in_cm_cost;

                                        $out_cm_cost=($tot_cm_cost_arr[$job_no]/$dzn_qnty)*$sewOut_outQty;
                                        $total_out_cm_cost+=$out_cm_cost;

                                        $cm_cost=($tot_cm_cost_arr[$job_no]/$dzn_qnty)*$sewing_output_total;
                                        $total_cm_cost+=$cm_cost;
                                        ?>
                                        <td width="80" align="right"><? if($prod_dzn!=0) echo number_format($in_prod_dzn,2); else echo "0"; ?></td>
                                        <td width="80" align="right"><? if($prod_dzn!=0) echo number_format($out_prod_dzn,2); else echo "0"; ?></td>
                                        <td width="80" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>
                                        <td align="right" width="100"><? echo number_format($in_cm_value,2,'.',''); ?></td>
                                        <td align="right" width="100"><? echo number_format($out_cm_value,2,'.',''); ?></td>
                                        <td align="right" width="100" title="(Budget Wise CM For Shipment Schedule/Dzn Qnty)*Total Sewing Out"><? echo number_format($cm_value,2,'.',''); ?></td>
                                        <td align="right" width="100"><? echo number_format($in_cm_cost,2,'.',''); ?></td>
                                        <td align="right" width="100"><? echo number_format($out_cm_cost,2,'.',''); ?></td>
                                        <td align="right" width="100" title="(Budget Wise CM Cost/Dzn Qnty)*Total Sewing Out"><? echo number_format($cm_cost,2,'.',''); ?></td>
                                        <td align="center"><a href="##" onclick="openmypage_remark(<? echo $po_id;?>,'date_wise_production_report');" > Veiw </a></td>
                                    </tr>
                                    <?
                                    $all_order_sewQty_array[$po_id]+=$sewing_output_total;
                                    $tot_cuttingIn_qty+=$cuttingIn_qty;
                                    $tot_cuttingOut_qty+=$cuttingOut_qty;

                                    $tot_print_qty+=$print_qty;
                                    $tot_printRec_qty+=$printRec_qty;
                                    $tot_emb_qty+=$emb_qty;
                                    $tot_embRec_qty+=$embRec_qty;
                                    $tot_wash_qty+=$wash_qty;
                                    $tot_washRec_qty+=$washRec_qty;
                                    $tot_special_qty+=$special_qty;
                                    $tot_specialRec_qty+=$specialRec_qty;
                                    $tot_sewIn_inQty+=$sewIn_inQty;
                                    $tot_sewIn_outQty+=$sewIn_outQty;
                                    $tot_sewing_input+=$sewing_input_total;
                                    $tot_sewOut_inQty+=$sewOut_inQty;
                                    $tot_sewOut_outQty+=$sewOut_outQty;
                                    $tot_sewing_output+=$sewing_output_total;
                                    $tot_fob_value+=$fob_value;
                                    $tot_ironIn_qty+=$ironIn_qty;
                                    $tot_ironOut_qty+=$ironOut_qty;
                                    $tot_iron_qty+=$iron_qty_total;
                                    $tot_reIron_qty+=$reIron_qty;
                                    $tot_finishIn_qty+=$finishIn_qty;
                                    $tot_finishOut_qty+=$finishOut_qty;
                                    $tot_finishing_qty+=$finishing_qty;
                                    $tot_carton_qty+=$carton_qty;
                                    $tot_rejFinish_qty+=$rejFinish_qty;
                                    $tot_rejSewing_qty+=$rejSewing_qty;
                                    $tot_reject_Qty+=$reject_Qty;

                                    $tot_embgmtIssue_qty+=$emb_gmt_issue_qty;
                                    $tot_embgmtRec_qty+=$emb_gmt_recv_qty;
                                    $tot_cutDelvQty+=$cutDelvQty;
                                    $i++;
                                }
                            }
                        }
                    }
                }
            }//end foreach 1st

            ?>
            </table>
            </div>
            <table border="1" class="tbl_bottom"  width="4762" rules="all" id="" >
                <tr>
                    <td width="30">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100" align="right">Total</td>
                    <td width="80" align="right" id="total_cut_inhouse"><? echo $tot_cuttingIn_qty;?></td>
                    <td width="80" align="right" id="total_cut_outside"><? echo $tot_cuttingOut_qty;?></td>
                    <td width="80" align="right" id="total_printissue_td"><? echo $tot_print_qty; ?> </td>
                    <td width="80" align="right" id="total_printrcv_td"><?  echo $tot_printRec_qty; ?></td>
                    <td width="80" align="right" id="total_emb_iss"><? echo $tot_emb_qty; ?></td>
                    <td width="80" align="right" id="total_emb_re"><?  echo $tot_embRec_qty;  ?></td>

                     <td width="80" align="right" id="total_emb_gmt_iss"><? echo $tot_embgmtIssue_qty; ?></td>
                    <td width="80" align="right" id="total_emb_gmt_re"><?  echo $tot_embgmtRec_qty;  ?></td>

                    <td width="80" align="right" id="total_wash_iss"><? echo $tot_wash_qty; ?></td>
                    <td width="80" align="right" id="total_wash_re"><?  echo $tot_washRec_qty;  ?></td>
                    <td width="80" align="right" id="total_sp_iss"><? echo $tot_special_qty; ?></td>
                    <td width="80" align="right" id="total_sp_re"><?  echo $tot_specialRec_qty;  ?></td>

                    <td width="80" align="right" id="total_cut_delv_qty_td"><? echo $tot_cutDelvQty; ?></td>


                    <td width="80" align="right" id="total_sewin_inhouse_td"><? echo $tot_sewIn_inQty; ?></td>
                    <td width="80" align="right" id="total_sewin_outbound_td"><? echo $tot_sewIn_outQty; ?></td>
                    <td width="80" align="right" id="total_sewin_td"><? echo $tot_sewing_input;  ?> </td>
                    <td width="80" align="right" id="total_sewout_inhouse_td"><? echo $tot_sewOut_inQty; ?></td>
                    <td width="80" align="right" id="total_sewout_outbound_td"><? echo $tot_sewOut_outQty; ?></td>
                    <td width="80" align="right" id="total_sewout_td"><? echo $tot_sewing_output; ?> </td>
                    <td width="80" align="right" id="total_fob_td"><? echo $tot_fob_value; ?> </td>

                    <td width="80" align="right" id="total_iron_in_td"><? echo $tot_ironIn_qty; ?></td>
                    <td width="80" align="right" id="total_iron_out_td"><? echo $tot_ironOut_qty; ?></td>

                    <td width="80" align="right" id="total_iron_td"><?  echo $tot_iron_qty; ?></td>
                    <td width="80" align="right" id="total_iron_smv_td"><?  echo $total_iron_smv; ?></td>
                    <td width="80" align="right" id="total_re_iron_td"><?  echo $tot_reIron_qty; ?></td>
                    <td width="80" align="right" id="total_finish_td"><?  echo $tot_finishing_qty; ?></td>
                    <td width="80" align="right" id="total_carton_td"><? echo $tot_carton_qty; ?></td>
                    <td width="80" align="right" id="value_total_in_prod_dzn_td"><?  echo number_format($total_in_prod_dzn,2); ?></td>
                    <td width="80" align="right" id="value_total_out_prod_dzn_td"><?  echo number_format($total_out_prod_dzn,2); ?></td>
                    <td width="80" align="right" id="value_total_prod_dzn_td"><?  echo number_format($total_prod_dzn,2); ?></td>
                    <td width="100" align="right" id="value_total_in_cm_value_td"><? echo number_format($total_in_cm_value,2); ?> </td>
                    <td width="100" align="right" id="value_total_out_cm_value_td"><? echo number_format($total_out_cm_value,2); ?></td>
                    <td width="100" align="right" id="value_total_cm_value_td"><? echo number_format($total_cm_value,2); ?></td>
                    <td width="100" align="right" id="value_total_in_cm_cost"><? echo number_format($total_in_cm_cost,2); ?></td>
                    <td width="100" align="right" id="value_total_out_cm_cost"><? echo number_format($total_out_cm_cost,2); ?></td>
                    <td width="100" align="right" id="value_total_cm_cost"><? echo number_format($total_cm_cost,2); ?></td>
                    <td width="">&nbsp;</td>
                </tr>
            </table>
            </div>
            <?
        }// end if condition of type
        //-------------------------------------------END Date Location Floor & Line Wise------------------------
        //-------------------------------------------Show Line Wise------------------------
        else if($type==3)
        {
            ob_start();

            $prod_reso_allocation=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=23 and status_active=1 and is_deleted=0");
            $floor_cond = str_replace("floor_id", "c.floor_id", $floor_name);
            $buyer_cond = str_replace("a.buyer_name", "b.buyer_name", $buyer_name);
            if($prod_reso_allocation==1)
            {
                $sql="SELECT a.job_no_mst,a.po_number,a.po_quantity,a.unit_price,b.order_uom,b.buyer_name,
                        c.po_break_down_id,c.location,c.production_date,c.sewing_line,c.prod_reso_allo,sum(c.production_quantity) as production_quantity
                    from
                        wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c, prod_resource_mst d
                    where
                        a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_cond $floor_cond $location_cond  $file_no_cond $internal_ref_cond and c.production_type=5 and  c.production_source=1 and d.id=c.sewing_line group by a.job_no_mst,a.po_number,a.po_quantity,a.unit_price,b.order_uom,b.buyer_name,
                        c.po_break_down_id,c.location,c.production_date,c.sewing_line,c.prod_reso_allo,d.line_number order by d.line_number,c.production_date";//$garments_nature
            }
            else
            {
                $sql="SELECT a.job_no_mst,a.po_number,a.po_quantity,a.unit_price,b.order_uom,b.buyer_name,
                        c.po_break_down_id,c.location,c.production_date,c.sewing_line,c.prod_reso_allo,sum(c.production_quantity) as production_quantity
                    from
                        wo_po_break_down a,wo_po_details_master b, pro_garments_production_mst c, lib_sewing_line d
                    where
                        a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $txt_date $company_name $buyer_cond $floor_cond $location_cond  $file_no_cond $internal_ref_cond and c.production_type=5 and  c.production_source=1 and d.id=c.sewing_line group by a.job_no_mst,a.po_number,a.po_quantity,a.unit_price,b.order_uom,b.buyer_name,
                        c.po_break_down_id,c.location,c.production_date,c.sewing_line,c.prod_reso_allo,d.line_name order by d.line_name,c.production_date";//$garments_nature
            }
            // echo $sql;
            $pro_date_sql=sql_select($sql);

            foreach($pro_date_sql as $rows)
            {
                $key=$rows[csf('sewing_line')];
                $pro_data[$rows[csf('sewing_line')]][]=array(
                    'po_break_down_id'=>$rows[csf('po_break_down_id')],
                    'po_number'=>$rows[csf('po_number')],
                    'buyer_name'=>$rows[csf('buyer_name')],
                    'po_quantity'=>$rows[csf('po_quantity')],
                    'production_quantity'=>$rows[csf('production_quantity')],
                    'production_date'=>$rows[csf('production_date')],
                    'location'=>$rows[csf('location')],
                    'sewing_line'=>$rows[csf('sewing_line')],
                    'unit_price'=>$rows[csf('unit_price')],
                    'order_uom'=>$rows[csf('order_uom')],
                    'job_no_mst'=>$rows[csf('job_no_mst')],
                    'prod_reso_allo'=>$rows[csf('prod_reso_allo')]
                );
                $job_arr[$rows[csf('job_no_mst')]]=$rows[csf('job_no_mst')];
            }

            //var_dump($pro_data);
            // echo $job_id=implode(',',$job_arr);die();
            $job_id="'" . implode ( "', '", $job_arr ) . "'";
            $sql="SELECT a.job_no,a.costing_per,a.sew_smv,b.margin_pcs_set,b.cm_cost from wo_pre_cost_mst a,wo_pre_cost_dtls b where a.job_no=b.job_no and a.job_no in($job_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

            $pro_cm_sql=sql_select($sql);
            // die('go to hell');
            foreach($pro_cm_sql as $row)
            {

                if($row[csf('costing_per')]==1)$pcs=12;
                if($row[csf('costing_per')]==2)$pcs=1;
                if($row[csf('costing_per')]==3)$pcs=24;
                if($row[csf('costing_per')]==4)$pcs=36;
                if($row[csf('costing_per')]==5) $pcs=48;
                $margin_pcs=($row[csf('margin_pcs_set')]/$pcs);
                $cm_cost_pcs=$row[csf('cm_cost')]/($row[csf('sew_smv')]*$pcs);

                $job_cm[$row[csf('job_no')]]=$cm_cost_pcs+$margin_pcs;
            }
            //var_dump($job_cm);
            ?>
            <table width="1250" class="rpt_table" border="1" rules="all" id="table_header_1">
                <thead>
                    <th width="30">Sl.</th>
                    <th width="170">Order Number</th>
                    <th width="100">Buyer Name</th>
                    <th width="100">PO Quantity</th>
                    <th width="100">Production Date</th>
                    <th width="100">Location</th>
                    <th width="100">Sewing Line No</th>
                    <th width="100">Sewing Output</th>
                    <th width="70">Prod/Dzn</th>
                    <th width="80">Total CM</th>
                    <th width="100">Prod. Value</th>
                    <th width="100">Remarks</th>
                </thead>
            </table>
            <div style="max-height:415px; overflow-y:scroll; width:1270px;" id="scroll_body">
                <table width="1250" class="rpt_table" border="1" rules="all">
                    <?

                    $j=1;
                    foreach($pro_data as $line_id=>$pro_date_sql)
                    {
                        $total_sewingout_qnty=0; $total_prod_dzn=0; $total_cm_value=0;  $total_prod_val=0;
                        $i=1;
                        foreach($pro_date_sql as $rows)
                        {
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                            $total_sewingout_qnty+=$rows['production_quantity'];
                            $total_prod_dzn+=$rows['production_quantity'] / 12 ;
                            $total_prod_val+=$rows['production_quantity'] * $rows['unit_price'];
                            $total_cm_value+=number_format($job_cm[$rows['job_no_mst']]*$rows['production_quantity'],2);

                            $sewing_line='';
                            if($rows['prod_reso_allo']==1)
                            {
                                $line_number=explode(",",$prod_reso_arr[$rows['sewing_line']]);
                                foreach($line_number as $val)
                                {
                                    if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
                                }
                            }
                            else $sewing_line=$line_library[$rows['sewing_line']];

                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i.'_'.$j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i.'_'.$j; ?>">
                                <td width="31" align="center"><? echo $i;?></td>
                                <td width="170"><? echo $rows['po_number']; ?></td>
                                <td width="100"><? echo $buyer_short_library[$rows['buyer_name']]; ?></td>
                                <td align="right" width="100"><? echo $rows['po_quantity']; ?></td>
                                <td width="100" align="center"><? echo '&nbsp;'.change_date_format($rows['production_date']); ?></td>
                                <td align="left" width="100"><? echo $location_library[$rows['location']]; ?></td>
                                <td align="right" width="100"><? echo $sewing_line; ?></td>
                                <td align="right" width="100"><? echo $rows['production_quantity']; ?></td>
                                <td align="right" width="70">
                                    <?
                                        $prod_dzn=$rows['production_quantity'] / 12 ;
                                        echo number_format($prod_dzn,2);
                                    ?>
                                </td>
                                <td align="right" width="80"><? echo number_format($job_cm[$rows['job_no_mst']]*$rows['production_quantity'],2);?></td>
                                <td align="right" width="100">
                                    <?
                                        $prod_val=$rows['production_quantity'] * $rows['unit_price'];
                                        echo number_format($prod_val,2);
                                    ?>
                                </td>
                                <td align="right" width="100"></td>
                            </tr>

                            <?
                            $all_order_sewQty_array[$rows['po_break_down_id']]+=$rows['production_quantity'];
                            $i++;
                        }
                    //var_dump($all_order_sewQty_array);
                    ?>
                        <tr bgcolor="#CCCCFF">
                            <th width="31"></th>
                            <th width="170"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100" align="right">Sub Total:</th>
                            <th width="100" align="right">
                                <?
                                    echo number_format($total_sewingout_qnty,2);
                                    $grand_total_sewing+=$total_sewingout_qnty;
                                ?>
                            </th>


                            <th width="70" align="right">
                                <?
                                    echo number_format($total_prod_dzn,2);
                                    $grand_total_prod_dzn+=$total_prod_dzn;
                                ?>
                            </th>
                            <th width="80" align="right">
                                <?
                                    echo number_format($total_cm_value,2);
                                    $grand_total_cm_value+=$total_cm_value;
                                ?>
                            </th>

                            <th width="100" align="right">
                            <?php
                                echo number_format($total_prod_val,2);
                                $grand_total_prod_val+=$total_prod_val;
                                $total_prod_val=0;
                             ?>
                             </th>
                            <th width="100" align="right"></th>
                        </tr>
                    <?php

                    $j++;
                    }

                    ?>
                 </table>
              </div>
             <table width="1250" class="rpt_table" border="1" rules="all">
                <tfoot>
                    <tr>
                        <th width="31"></th>
                        <th width="170"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100">Grand Total:</th>
                        <th width="100" align="right" id="total_cut_qnty">
                            <?
                                echo number_format($grand_total_sewing,2);
                            ?>
                        </th>
                        <th width="70" align="right" id="total_cut_qnty">
                            <?
                                echo number_format($grand_total_prod_dzn,2);
                            ?>
                        </th>
                        <th width="80" align="right" id="total_cut_qnty">
                            <?
                                echo number_format($grand_total_cm_value,2);
                            ?>
                        </th>
                        <th width="100" align="right"><?php echo number_format($grand_total_prod_val,2); ?></th>
                        <th width="100" align="right" id="total_iron_input_qnty"></th>
                    </tr>
                </tfoot>
             </table>
             </div>
            <?
            }//end line wise;

            $fab_cost_arr=array();
            $sql_pre=sql_select("select job_no, costing_per_id, fabric_cost, trims_cost, embel_cost, wash_cost, comm_cost, commission, lab_test, freight, inspection, currier_pre_cost, cm_cost, total_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0");
            foreach($sql_pre as $val)
            {
                $fab_cost_arr[$val[csf('job_no')]]['costing_per_id']=$val[csf('costing_per_id')];
                $fab_cost_arr[$val[csf('job_no')]]['fabric_cost']=$val[csf('fabric_cost')];
                $fab_cost_arr[$val[csf('job_no')]]['trims_cost']=$val[csf('trims_cost')];
                $fab_cost_arr[$val[csf('job_no')]]['embel_cost']=$val[csf('embel_cost')];
                $fab_cost_arr[$val[csf('job_no')]]['wash_cost']=$val[csf('wash_cost')];
                $fab_cost_arr[$val[csf('job_no')]]['comm_cost']=$val[csf('comm_cost')];
                $fab_cost_arr[$val[csf('job_no')]]['commission']=$val[csf('commission')];
                $fab_cost_arr[$val[csf('job_no')]]['lab_test']=$val[csf('lab_test')];
                $fab_cost_arr[$val[csf('job_no')]]['freight']=$val[csf('freight')];
                $fab_cost_arr[$val[csf('job_no')]]['inspection']=$val[csf('inspection')];
                $fab_cost_arr[$val[csf('job_no')]]['currier_pre_cost']=$val[csf('currier_pre_cost')];
                $fab_cost_arr[$val[csf('job_no')]]['cm_cost']=$val[csf('cm_cost')];
                $fab_cost_arr[$val[csf('job_no')]]['total_cost']=$val[csf('total_cost')];
            }
            $all_po_id=""; $all_job_no=""; $job_no="";
            foreach($all_order_sewQty_array as $id=>$val)
            {
                if($all_po_id=="") $all_po_id=$id; else $all_po_id.=','.$id;
            }
            $job_arr=array();

            $job_sql="SELECT a.job_no_prefix_num, a.job_no, a.avg_unit_price, a.total_set_qnty as ratio, b.plan_cut, b.id, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0   and b.status_active=1 and b.is_deleted=0 and b.id in ($all_po_id)
             group by  a.job_no_prefix_num, a.job_no, a.avg_unit_price, a.total_set_qnty , b.plan_cut, b.id, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.po_total_price order by b.pub_shipment_date,b.id";
            //echo $job_sql;
            $job_result=sql_select($job_sql);
            $tot_fab_cost=0; $tot_trim_cost=0; $tot_emble_cost=0; $tot_wash_cost=0; $tot_commercial_cost=0; $tot_commission_cost=0; $tot_test_cost=0; $tot_freight_cost=0; $tot_inspection_cost=0; $tot_currier_cost=0;
            $cm_cost_values_total=0;

            $job_check_array=array();
            foreach($job_result as $row)
            {
                $order_value+=$row[csf('po_total_price')];
                $jobs=$row[csf('job_no')];
                $sewing_output_tot =$job_wise_qnty_array[$jobs];
                $prod_qty=$all_order_sewQty_array[$row[csf('id')]];
                $dzn_qnty=0;
                //$costing_per_id=$fab_cost_arr[$row[csf('job_no')]]['costing_per_id'];
                $costing_per_id=$costing_per_id_library[$jobs];
                if($costing_per_id==1) $dzn_qnty=12;
                else if($costing_per_id==3) $dzn_qnty=12*2;
                else if($costing_per_id==4) $dzn_qnty=12*3;
                else if($costing_per_id==5) $dzn_qnty=12*4;
                else $dzn_qnty=1;
                $dzn_qnty=$dzn_qnty*$row[csf('ratio')];
                if(!in_array($jobs, $job_check_array))
                {
                    $cm_cost_values_total+=($tot_cost_arr[$jobs]/$dzn_qnty)*$sewing_output_tot ;
                    //echo "string ".($tot_cm_cost_arr[$jobs]/$dzn_qnty)*$sewing_output_tot;
                    $job_check_array[$jobs]=$jobs;

                }


                $fab_cost=($fab_cost_arr[$row[csf('job_no')]]['fabric_cost']/$dzn_qnty)*$prod_qty;
                $tot_fab_cost+=$fab_cost;

                $trim_cost=($fab_cost_arr[$row[csf('job_no')]]['trims_cost']/$dzn_qnty)*$prod_qty;
                $tot_trim_cost+=$trim_cost;

                $emble_cost=($fab_cost_arr[$row[csf('job_no')]]['embel_cost']/$dzn_qnty)*$prod_qty;
                $tot_emble_cost+=$emble_cost;

                $wash_cost=($fab_cost_arr[$row[csf('job_no')]]['wash_cost']/$dzn_qnty)*$prod_qty;
                $tot_wash_cost+=$wash_cost;

                $commercial_cost=($fab_cost_arr[$row[csf('job_no')]]['comm_cost']/$dzn_qnty)*$prod_qty;
                $tot_commercial_cost+=$commercial_cost;

                $commission_cost=($fab_cost_arr[$row[csf('job_no')]]['commission']/$dzn_qnty)*$prod_qty;
                $tot_commission_cost+=$commission_cost;

                $test_cost=($fab_cost_arr[$row[csf('job_no')]]['lab_test']/$dzn_qnty)*$prod_qty;
                $tot_test_cost+=$test_cost;

                $freight_cost=($fab_cost_arr[$row[csf('job_no')]]['freight']/$dzn_qnty)*$prod_qty;
                $tot_freight_cost+=$freight_cost;

                $inspection_cost=($fab_cost_arr[$row[csf('job_no')]]['inspection']/$dzn_qnty)*$prod_qty;
                $tot_inspection_cost+=$inspection_cost;

                $currier_cost=($fab_cost_arr[$row[csf('job_no')]]['currier_pre_cost']/$dzn_qnty)*$prod_qty;
                $tot_currier_cost+=$currier_cost;
            }


            $total_cost=$tot_fab_cost+$tot_trim_cost+$tot_emble_cost+$tot_wash_cost+$tot_commercial_cost+$tot_commission_cost+$tot_test_cost+$tot_freight_cost+$tot_inspection_cost+$tot_currier_cost;
            $cm_value=$order_value-$total_cost;

            $tot_fab_cost_per=($tot_fab_cost/$order_value)*100;
            $tot_trim_cost_per=($tot_trim_cost/$order_value)*100;
            $tot_emble_cost_per=($tot_emble_cost/$order_value)*100;
            $tot_wash_cost_per=($tot_wash_cost/$order_value)*100;
            $tot_commercial_cost_per=($tot_commercial_cost/$order_value)*100;
            $tot_commission_cost_per=($tot_commission_cost/$order_value)*100;
            $tot_test_cost_per=($tot_test_cost/$order_value)*100;
            $tot_freight_cost_per=($tot_freight_cost/$order_value)*100;
            $tot_inspection_cost_per=($tot_inspection_cost/$order_value)*100;
            $tot_currier_cost_per=($tot_currier_cost/$order_value)*100;
            $total_cost_per=($total_cost/$order_value)*100;
            $total_value_per=($order_value/$order_value)*100;
            $total_cm_per=($cm_cost_values_total/$order_value)*100;

            $style=$bgcolor="#E9F3FF";
            $style1=$bgcolor="#FFFFFF";
            ?>
            <br />

                <table width="450" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th colspan="3">Budget Summary </th>
                        </tr>
                        <tr>
                            <th width="150">Particulars</th><th width="145">Amount</th><th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr bgcolor="<? echo $style; ?>">
                            <td>Fabric Cost</td>
                            <td align="right"><? echo number_format($tot_fab_cost,2); ?></td>
                            <td align="right"><? echo number_format($tot_fab_cost_per,3); ?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                            <td>Trims Cost</td>
                            <td align="right"><? echo number_format($tot_trim_cost,2); ?></td>
                            <td align="right"><? echo number_format($tot_trim_cost_per,3); ?></td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                            <td>Embellish Cost</td>
                            <td align="right"><? echo number_format($tot_emble_cost,2); ?></td>
                            <td align="right"><? echo number_format($tot_emble_cost_per,3); ?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                            <td>Gmts.Wash Cost</td>
                            <td align="right"><? echo number_format($tot_wash_cost,2); ?></td>
                            <td align="right"><? echo number_format($tot_wash_cost_per,3); ?></td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                            <td>Commercial Cost</td>
                            <td align="right"><? echo number_format($tot_commercial_cost,2); ?></td>
                            <td align="right"><? echo number_format($tot_commercial_cost_per,3); ?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                            <td>Commision Cost</td>
                            <td align="right"><? echo number_format($tot_commission_cost,2); ?></td>
                            <td align="right"><? echo number_format($tot_commission_cost_per,2); ?></td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                            <td>Testing Cost</td>
                            <td align="right"><? echo number_format($tot_test_cost,2); ?></td>
                            <td align="right"><? echo number_format($tot_test_cost_per,2); ?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                            <td>Freight Cost</td>
                            <td align="right"><? echo number_format($tot_freight_cost,2); ?></td>
                            <td align="right"><? echo number_format($tot_freight_cost_per,2); ?></td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                            <td>Inspection Cost</td>
                            <td align="right"><? echo number_format($tot_inspection_cost,2); ?></td>
                            <td align="right"><? echo number_format($tot_inspection_cost_per,2); ?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                            <td>Courier Cost</td>
                            <td align="right"><? echo number_format($tot_currier_cost,2); ?></td>
                            <td align="right"><? echo number_format($tot_currier_cost_per,2); ?></td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                            <td>Total Cost</td>
                            <td align="right"><? echo number_format($total_cost,2); ?></td>
                            <td align="right"><? echo number_format($total_cost_per,2); ?></td>
                        </tr>
                        <tr bgcolor="<? echo $style1; ?>">
                            <td>Total Value</td>
                            <td align="right"><? echo number_format($order_value,2); ?></td>
                            <td align="right"><? echo number_format($total_value_per,2); ?></td>
                        </tr>
                        <tr bgcolor="<? echo $style; ?>">
                            <td>CM Value</td>
                            <td align="right"><? echo number_format($cm_cost_values_total,2); ?></td>
                            <td align="right"><? echo number_format($total_cm_per,2); ?></td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <?
            //-------------------------------------------END Show Date Location Floor & Line Wise------------------------

            //---------end------------//
        }
        else
        {

            ob_start();
            ?>
            <div>
            <table width="1320" cellspacing="0">
                <tr class="form_caption" style="border:none;">
                        <td colspan="17" align="center" style="border:none;font-size:14px; font-weight:bold" >Date Wise Production Report</td>
                 </tr>
                <tr style="border:none;">
                        <td colspan="17" align="center" style="border:none; font-size:16px; font-weight:bold">
                            Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
                        </td>
                  </tr>
                  <tr style="border:none;">
                        <td colspan="17" align="center" style="border:none;font-size:12px; font-weight:bold">
                            <? echo "From $fromDate To $toDate" ;?>
                        </td>
                  </tr>
            </table>
            <div style="max-height:200px; overflow-y:scroll; width:1380px" id="scroll_body2" >
            <table width="1360" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                <thead>
                    <tr>
                        <th style="word-break: break-all;" width="30">Sl.</th>
                        <th width="120">Buyer Name</th>
                        <th width="80">Cut Qty</th>
                        <th width="80">Sent to Print</th>
                        <th width="80">Rev Print</th>
                        <th width="80">Sent to Emb</th>
                        <th width="80">Rev Emb</th>

                        <th width="80">Sent to Wash</th>
                        <th width="80">Rev Wash</th>
                        <th width="80">Sent to Sp. Works</th>
                        <th width="80">Rev Sp. Works</th>

                        <th width="80">Sew Input(Inhouse)</th>
                        <th width="80">Sew Input (Outbound)</th>
                        <th width="80">Sew Output(Inhouse)</th>
                        <th width="80">Sew Output (Outbound)</th>

                        <th width="80">Total CM</th>
                        <th>Prod. Value (FOB)</th>


                     </tr>
                </thead>
                <?
                $job_arr=array();
                if($db_type==0) $year_cond="YEAR(a.insert_date)";
                else if($db_type==2) $year_cond="to_char(a.insert_date,'YYYY')";
                //$job_sql="SELECT a.job_no_prefix_num, a.job_no, a.set_smv,a.buyer_name, $year_cond as year, a.style_ref_no, b.id, b.po_number, b.grouping, b.file_no, (b.unit_price/a.total_set_qnty) as unit_price, b.po_quantity, a.total_set_qnty as ratio, a.order_uom from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_name=$cbo_company_name $buyer_name $file_no_cond $internal_ref_cond $all_po_ids_cond";

    			$job_sql="SELECT a.job_no_prefix_num, a.job_no, a.set_smv,a.buyer_name, $year_cond as year, a.style_ref_no, b.id, b.po_number, b.grouping, b.file_no, (b.unit_price/a.total_set_qnty) as unit_price, b.po_quantity, a.total_set_qnty as ratio, a.order_uom,c.SMV_SET,c.GMTS_ITEM_ID from wo_po_details_master a, wo_po_break_down b,WO_PO_DETAILS_MAS_SET_DETAILS c where a.job_no=b.job_no_mst AND c.job_no = b.job_no_mst AND c.job_no = a.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_name=$cbo_company_name $buyer_name $file_no_cond $internal_ref_cond $all_po_ids_cond";


    			//echo $job_sql;
                $job_sql_res=sql_select($job_sql); $tot_rows=0; $poIds='';
                //die;
                foreach($job_sql_res as $row)
                {
                    $tot_rows++;
                    $poIds.=$row[csf("id")].",";
                    $job_arr[$row[csf("id")]]=$row[csf("job_no_prefix_num")].'!!'.$row[csf("job_no")].'!!'.$row[csf("buyer_name")].'!!'.$row[csf("style_ref_no")].'!!'.$row[csf("po_number")].'!!'.$row[csf("grouping")].'!!'.$row[csf("file_no")].'!!'.$row[csf("unit_price")].'!!'.$row[csf("po_quantity")].'!!'.$row[csf("ratio")].'!!'.$row[csf("order_uom")].'!!'.$row[csf("year")].'!!'.$row[csf("set_smv")];
                $item_sm_arr[$row[csf("id")]][$row[GMTS_ITEM_ID]]=$row[SMV_SET];
    			}
                unset($job_sql_res);
                $poIds_cond="";
                //if ($file_no!="" || $internal_ref!="")
                //{
                    $poIds=chop($poIds,',');
                    if($db_type==2 && $tot_rows>1000)
                    {
                        $poIds_cond=" and (";
                        $poIdsArr=array_chunk(array_unique(explode(",",$poIds)),999);
                        foreach($poIdsArr as $ids)
                        {
                            $ids=implode(",",$ids);
                            $poIds_cond.=" po_break_down_id in ($ids) or ";
                        }
                        $poIds_cond=chop($poIds_cond,'or ');
                        $poIds_cond.=")";
                    }
                    else
                    {
                        $poIds_cond=" and po_break_down_id in ($poIds)";
                    }
                //}

                $buyer_fullQty_arr=array();
                $prod_date_qty_arr=array();
                $prod_dlfl_qty_arr=array();
                $all_data_arr=array();
                $sql_dtls="SELECT location, company_id, floor_id, sewing_line, po_break_down_id, production_date, item_number_id, production_type, production_source, embel_name, max(prod_reso_allo) as prod_reso_allo, sum(production_quantity) as production_quantity, sum(re_production_qty) as re_production_qty, sum(reject_qnty) as reject_qnty, sum(carton_qty) as carton_qty
                from pro_garments_production_mst
                where company_id=$cbo_company_name and is_deleted=0 and status_active=1 and production_type in(1,2,3,4,5) $txt_date $floor_name $location_cond $garmentsNature $poIds_cond
                group by location, company_id, floor_id, sewing_line, po_break_down_id, production_date, item_number_id, production_type, production_source, embel_name
                order by production_date ASC";
                $sql_dtls_res=sql_select($sql_dtls);
                foreach($sql_dtls_res as $row)
                {
                    $buyer_name_dat="";
                    $all_job_data=$job_arr[$row[csf("po_break_down_id")]];
                    $ex_job_data=explode('!!',$all_job_data);

                    $buyer_name_dat=$ex_job_data[2];
    				$job_smv=$ex_job_data[12];
    				$job_no=$ex_job_data[1];
    				$ratio=$ex_job_data[9];
    				 //echo $job_no.',';

                    //Buyer Wise Summary array start
                    $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                    $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                    $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                    $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                    $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];
                    //Details array start








    				if($row[csf("sewing_line")]=="") $row[csf("sewing_line")]=0;
                    if($row[csf("floor_id")]=="") $row[csf("floor_id")]=0;
                    if($row[csf("location")]=="") $row[csf("location")]=0;

                    if($row[csf("production_type")]==5)
                    {

                        $sewing_line='';
                        $resource_allo=$row[csf("prod_reso_allo")];
                        if($resource_allo==1)
                        {
                        //  $line_number=explode(",",$prod_reso_arr[$row[csf("sewing_line")]]);
                            $sewing_line=$prod_reso_arr[$row[csf("sewing_line")]];
                            //foreach($line_number as $val)
                            //{
                                //$prod_reso_arr
                                //if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
                                //echo $val.'-';
                            //}
                        }
                        else $sewing_line=$row[csf("sewing_line")];

                        $prod_key=$row[csf("location")]."**".$row[csf("floor_id")];// echo $row[csf("item_number_id")];die;

                        //$prod_dlfl_qty_arr[$prod_key][$row[csf("production_date")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$sewing_line]["sew_out_qnty"]+=$row[csf("production_quantity")];
                        //$prod_dlfl_qty_arr[$prod_key][$row[csf("production_date")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$sewing_line]["prod_reso_allo"]=$row[csf("prod_reso_allo")];


                        $prod_dlfl_qty_arr[$row[csf("location")]][$row[csf("floor_id")]][$sewing_line][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("production_date")]]["sew_out_qnty"]+=$row[csf("production_quantity")];

                        $prod_dlfl_qty_arr[$row[csf("location")]][$row[csf("floor_id")]][$sewing_line][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("production_date")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];

                   //------------------------cm and fov value
    				$dzn_qnty=0;
    				if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
    				else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
    				else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
    				else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
    				else $dzn_qnty=1;
    				 $dzn_qnty=$dzn_qnty*$ratio;

    			   $cm_pcs_value=($cm_value_contribution_margine[$job_no]/$dzn_qnty);
    			   $buyer_fullQty_arr[$buyer_name_dat][110]['0']['totalCM']+=$row[csf("production_quantity")]*$cm_pcs_value;
    			   $unit_price=number_format($ex_job_data[7],2);
    			   $buyer_fullQty_arr[$buyer_name_dat][110]['0']['FOVvalue']+=$row[csf("production_quantity")]*$unit_price;
    			   //----------------

    				}

                }

               // var_dump( $buyer_fullQty_arr);
                unset($sql_dtls_res);
                //die;
                //$cm_per_dzn=return_library_array( "select job_no, cm_for_sipment_sche from wo_pre_cost_dtls where is_deleted=0 and status_active=1",'job_no','cm_for_sipment_sche');
                $b=1;
                foreach($buyer_fullQty_arr as $buyer_id=>$buyer_data)
                {
                    if ($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $cutting_qty=$printing_qty=$printreceived_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewOut_inQty=$sewOut_outQty=0;
                    $cutting_qty=$buyer_data['1']['0']['pQty'];
                    $printing_qty=$buyer_data['2']['1']['embQty'];
                    $printreceived_qty=$buyer_data['3']['1']['embQty'];
                    $emb_qty=$buyer_data['2']['2']['embQty'];
                    $embRec_qty=$buyer_data['3']['2']['embQty'];

                    $wash_qty=$buyer_data['2']['3']['embQty'];
                    $washRec_qty=$buyer_data['3']['3']['embQty'];
                    $special_qty=$buyer_data['2']['4']['embQty'];
                    $specialRec_qty=$buyer_data['3']['4']['embQty'];
                    $sewIn_inQty=$buyer_data['4']['0']['pQty'];
                    $sewIn_outQty=$buyer_data['4']['3']['sQty'];
                    $sewOut_inQty=$buyer_data['5']['0']['pQty'];
                    $sewOut_outQty=$buyer_data['5']['3']['sQty'];

    				 $total_cm=$buyer_data[110]['0']['totalCM'];
    				 $total_fov_value=$buyer_data[110]['0']['FOVvalue'];

                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $b; ?>">
                        <td align="center"><? echo $b;?></td>
                        <td><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($cutting_qty); ?></td>
                        <td align="right"><? echo number_format($printing_qty); ?></td>
                        <td align="right"><? echo number_format($printreceived_qty); ?></td>
                        <td align="right"><? echo number_format($emb_qty); ?></td>
                        <td align="right"><? echo number_format($embRec_qty); ?></td>
                        <td align="right"><? echo number_format($wash_qty); ?></td>
                        <td align="right"><? echo number_format($washRec_qty);  ?></td>
                        <td align="right"><? echo number_format($special_qty); ?></td>
                        <td align="right"><? echo number_format($specialRec_qty);  ?></td>
                        <td align="right"><? echo number_format($sewIn_inQty); ?></td>
                        <td align="right"><? echo number_format($sewIn_outQty); ?></td>
                        <td align="right"><? echo number_format($sewOut_inQty); ?></td>
                        <td align="right"><? echo number_format($sewOut_outQty); ?></td>

                        <td align="right"><?=number_format($total_cm);?></td>
                        <td align="right"><?=number_format($total_fov_value);?></td>

                    </tr>
                    <?
                    $sumCutting_qty+=$cutting_qty;
                    $sumPrinting_qty+=$printing_qty;
                    $sumPrintreceived_qty+=$printreceived_qty;
                    $sumEmb_qty+=$emb_qty;
                    $sumEmbRec_qty+=$embRec_qty;
                    $sumWash_qty+=$wash_qty;
                    $sumWashRec_qty+=$washRec_qty;
                    $sumSpecial_qty+=$special_qty;
                    $sumSpecialRec_qty+=$specialRec_qty;
                    $sumSewIn_inQty+=$sewIn_inQty;
                    $sumSewIn_outQty+=$sewIn_outQty;
                    $sumSewOut_inQty+=$sewOut_inQty;
                    $sumSewOut_outQty+=$sewOut_outQty;
                    $grand_total_cm+=$total_cm;
    				$grand_total_fov_value+=$total_fov_value;
                    $b++;
                }
                ?>
                <tfoot>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Total</th>
                        <th><? echo number_format($sumCutting_qty); ?></th>
                        <th><? echo number_format($sumPrinting_qty); ?></th>
                        <th><? echo number_format($sumPrintreceived_qty); ?></th>
                        <th><? echo number_format($sumEmb_qty); ?></th>
                        <th><? echo number_format($sumEmbRec_qty); ?></th>
                        <th><? echo number_format($sumWash_qty); ?></th>
                        <th><? echo number_format($sumWashRec_qty); ?></th>
                        <th><? echo number_format($sumSpecial_qty); ?></th>
                        <th><? echo number_format($sumSpecialRec_qty); ?></th>
                        <th><? echo number_format($sumSewIn_inQty); ?></th>
                        <th><? echo number_format($sumSewIn_outQty); ?></th>
                        <th><? echo number_format($sumSewOut_inQty); ?></th>
                        <th><? echo number_format($sumSewOut_outQty); ?></th>

                        <th align="right"><? echo number_format($grand_total_cm); ?></th>
                        <th align="right"><? echo number_format($grand_total_fov_value); ?></th>

                     </tr>
                </tfoot>
                </table>
            </div>
            <br />
            <!--<div style="clear:both"></div>-->
            <div style="width:1370px" align="left">
                <table width="1350" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" align="left">
                    <thead>
                        <th width="30">SL</th>
                        <th width="100">Line No</th>
                        <th width="100">Buyer Name</th>
                        <th width="110">Style Ref</th>
                        <th width="130">Order Number</th>
                        <th width="80">PO Quantity</th>
                        <th width="130">Gmts Item</th>
                        <th width="80">Production Date</th>
                        <th width="80">Sewing Output</th>
                        <th width="80">CM/Dzn</th>
                        <th width="100">Total CM</th>
                        <th width="80">FOB/Pcs</th>
                        <th width="100">Prod. Value (FOB) DD</th>
                        <th width="50">SMV</th>
                        <th>Remarks</th>
                    </thead>
                </table>
                <div style="max-height:325px; overflow-y:scroll; width:1370px" id="scroll_body" align="left">
                <table cellspacing="0" border="1" class="rpt_table"  width="1350" rules="all" id="table_body" align="left">
                <?
            //$prod_dlfl_qty_arr[$prod_key][$row[csf("production_date")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$sewing_line]["prod_reso_allo"]
                //$prod_dlfl_qty_arr[$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("production_date")]]["prod_reso_allo"]

                $tot_embgmtIssue_qty=$tot_embgmtRec_qty=$tot_job_smv=0;
                //$prod_dlfl_qty_arr=krsort($prod_dlfl_qty_arr);
                //foreach($prod_dlfl_qty_arr as $flore_location_key=>$flore_location_data)
                foreach($prod_dlfl_qty_arr as $flore_location_key=>$flore_location_data)
                {


                foreach($flore_location_data as $floor_id=>$floor_data)//new
                {
                    //ksort($flore_location_data);
                    //$flore_ref=explode("**",$flore_location_key);
                    ?>
                    <tr><td colspan="15" style="font-size:14px; font-weight:bold">Floor : <? echo $floor_library[$floor_id]; ?>, Location : <? echo $location_library[$flore_location_key]; ?></td></tr>
                    <?
                    $j=1;


                    //foreach($flore_location_data as $po_id=>$po_data)
                    //foreach($flore_location_data as $prod_date=>$prod_date_data)
                    foreach($floor_data as $sewing_line_id=>$sewing_line_data)
                    {
                        //foreach($po_data as $prod_date=>$prod_date_data)
                        //foreach($prod_date_data as $po_id=>$po_data)
                        foreach($sewing_line_data as $po_id=>$po_data)
                        {
                            //foreach($prod_date_data as $item_id=>$item_data)
                            //foreach($po_data as $item_id=>$item_data)
                            foreach($po_data as $item_id=>$item_data)
                            {
                                ksort($item_data);
                                foreach($item_data as $prod_date=>$data_all)
                                {

                                    //foreach($item_data as $sewing_line_id=>$data_all)
                                    //foreach($prod_date_data as $sewing_line_id=>$data_all)
                                    //{
                                        $buyer_name_dat=""; $job_no_pre=''; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no=''; $unit_price=''; $po_qty=''; $ratio=''; $order_uom=''; $job_year=''; $all_job_data=''; $all_job_data='';$floor_job_smv=0;
                                        $all_job_data=$job_arr[$po_id];

                                        $ex_job_data=explode('!!',$all_job_data);

                                        $job_no_pre=$ex_job_data[0];
                                        $job_no=$ex_job_data[1];
                                        $buyer_name_dat=$ex_job_data[2];
                                        $style_ref=$ex_job_data[3];
                                        $po_no=$ex_job_data[4];
                                        $ref_no=$ex_job_data[5];
                                        $file_no=$ex_job_data[6];
                                        $unit_price=number_format($ex_job_data[7],2);
                                        $po_qty=$ex_job_data[8];
                                        $ratio=$ex_job_data[9];
                                        $order_uom=$ex_job_data[10];
                                        $job_year=$ex_job_data[11];
    									$job_smv=$ex_job_data[12];
    									$item_smv=$item_sm_arr[$po_id][$item_id];

                                        if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                        $sewing_line='';
                                        /*$resource_allo=$data_all["prod_reso_allo"];
                                        if($resource_allo==1)
                                        {
                                            $line_number=explode(",",$prod_reso_arr[$sewing_line_id]);
                                            //$line_numbers=sort($line_number);
                                            foreach($line_number as $val)
                                            {
                                                if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
                                            }
                                        }
                                        else $sewing_line=$line_library[$sewing_line_id];*/
                                        $line_number=explode(",",$sewing_line_id);
                                            //$line_numbers=sort($line_number);
                                            //echo $sewing_line_id."=";
                                        foreach($line_number as $val)
                                        {
                                            if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
                                        }
                                        //$item_smv=$smv_qty_arr[$pro_date_sql_row[csf("job_no_mst")]][$pro_date_sql_row[csf("item_number_id")]];
                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                            <td width="30"><? echo $j;?></td>
                                            <td width="100"><p><? echo $sewing_line; ?></p></td>
                                            <td width="100"><p><? echo $buyer_short_library[$buyer_name_dat]; ?></p></td>
                                            <td width="110"><p><? echo $style_ref;?></p></td>
                                            <td width="130"><p><a href="##" onClick="openmypage_order(<? echo $po_id;?>,<? echo $item_id; ?>,'orderQnty_popup');" ><? echo $po_no; ?></a></p></td>
                                            <td width="80" align="right"><p><? echo number_format($po_qty,0); ?></p></td>
                                            <td width="130"><p><? echo $garments_item[$item_id]; ?></p></td>
                                            <td width="80" align="center"><p><? echo '&nbsp;'.change_date_format($prod_date); ?></p></td>
                                            <td width="80" align="right"><p><? echo number_format($data_all["sew_out_qnty"],0); ?></p></td>
                                            <?
                                            $dzn_qnty=0; $cm_value=0; $cm_cost=0;
                                            if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
                                            else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
                                            else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
                                            else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
                                            else $dzn_qnty=1;

                                            $dzn_qnty=$dzn_qnty*$ratio;
                                            $cm_pcs_value=($cm_value_contribution_margine[$job_no]/$dzn_qnty);
                                            $cm_dzn_value=($cm_value_contribution_margine[$job_no]/$dzn_qnty)*12;
                                            $sew_out_cm_value=$data_all["sew_out_qnty"]*$cm_pcs_value;
                                            $fob_value=$data_all["sew_out_qnty"]*$unit_price;

                                            ?>

                                            <td width="80" align="right" title="<? echo $dzn_qnty; ?>"><? if($data_all["sew_out_qnty"]!=0) echo number_format($cm_dzn_value,2); else echo "0"; ?></td>
                                            <td width="100" align="right"><? if($data_all["sew_out_qnty"]!=0) echo number_format($sew_out_cm_value,2); else echo "0"; ?></td>
                                            <td width="80" align="right"><? if($data_all["sew_out_qnty"]!=0) echo number_format($unit_price,2); else echo "0"; ?></td>
                                            <td width="100" align="right"><? if($data_all["sew_out_qnty"]!=0) echo number_format($fob_value,2); else echo "0";  ?></td>
                                            <td width="50" align="right"><?= number_format($item_smv,2); ?></td>
                                            <td align="center"><a href="##" onclick="openmypage_remark(<? echo $po_id;?>,'date_wise_production_report');" > View</a></td>
                                        </tr>
                                        <?
                                        $flore_sew_out_qnty +=$data_all["sew_out_qnty"];
                                        $flore_sew_out_cm_value +=$sew_out_cm_value;
                                        $flore_fob_value +=$fob_value;
    									$floor_job_smv +=$job_smv;

                                        $tot_sew_out_qnty+=$data_all["sew_out_qnty"];
                                        $tot_sew_out_cm_value +=$sew_out_cm_value;
                                        $tot_fob_value +=$fob_value;
    									$tot_job_smv +=$job_smv;
                                        $j++;
                                        $i++;

                                    //}
                                }
                            }
                        }
                    }

                    ?>
                    <tr bgcolor="#FFFFCC">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2" align="right">Floor Total :</td>
                        <td align="right"><? echo number_format($flore_sew_out_qnty,2); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($flore_sew_out_cm_value,2); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($flore_fob_value,2); ?></td>
                        <td align="right"><? //echo number_format($floor_job_smv,2); ?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?
                    $flore_sew_out_qnty=$flore_sew_out_cm_value=$flore_fob_value=0;
                }
                }
                ?>
                </table>
                </div>
                <table border="1" class="tbl_bottom"  width="1350" rules="all" id="" align="left">
                    <tr>
                        <td width="30">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="110">&nbsp;</td>
                        <td width="130">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="130">&nbsp;</td>
                        <td width="80">Total:</td>
                        <td width="80"><? echo number_format($tot_sew_out_qnty,2); ?></td>
                        <td width="80">&nbsp;</td>
                        <td width="100"><? echo number_format($tot_sew_out_cm_value,2); ?></td>
                        <td width="80">&nbsp;</td>
                        <td width="100"><? echo number_format($tot_fob_value,2); ?></td>
                        <td width="50"><? //echo number_format($tot_job_smv,2); ?></td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </div>
            </div>
            <?
        }



    if($excel_type==0) //Show Button
    {
        $html = ob_get_contents();
        ob_clean();
        $new_link=create_delete_report_file( $html, 1, 1, "../../../" );

        echo "$html####$excel_type####$new_link";
        exit();
    }
    else //Convert to Excel Button
    {
        $html = ob_get_contents();
        ob_clean();
        //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
        foreach (glob("$user_name*.xls") as $filename) {
            //if( @filemtime($filename) < (time()-$seconds_old) )
            @unlink($filename);
        }
        //---------end------------//
        $name=time();
        $filename="".$user_name."_".$name.".xls";
        $create_new_doc = fopen($filename, 'w');
        $is_created = fwrite($create_new_doc, $html);
        echo "$html####$excel_type####$filename";
    }
    exit();
}



if($action=="report_generate_cm_value")// cm value 2 button
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
    $company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name");
    $buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
    $location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name");
    $floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
    $line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
    $prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');




    $garments_nature=str_replace("'","",$cbo_garments_nature);
    if($garments_nature==1)$garments_nature="";
    $type = str_replace("'","",$cbo_type);
    $location = str_replace("'","",$cbo_location);
    $cbo_floor = str_replace("'","",$cbo_floor);
    $file_no = str_replace("'","",$txt_file_no);
    $internal_ref = str_replace("'","",$txt_internal_ref);
    $excel_type = str_replace("'","",$excel_type);
    if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and b.company_name=$cbo_company_name";
    if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
    if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
    else $txt_date=" and production_date between $txt_date_from and $txt_date_to";
    $fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
    $toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
    if ($location==0) $location_cond=""; else $location_cond=" and location=".$location." ";
    if($cbo_floor=="") $floor_name="";else $floor_name=" and floor_id in($cbo_floor)";
    if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
    if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

    $all_order_sewQty_array=array();
    if($type==1)
    {
        $colspn='50';
    }
    else if($type==2)
    {
        $colspn='50';
    }

    $all_po_ids_array=return_library_array( "SELECT po_break_down_id  from pro_garments_production_mst
            where  status_active=1 and is_deleted=0  $txt_date group by po_break_down_id ", "po_break_down_id", "po_break_down_id" );

	$sql_std_para=sql_select("select interest_expense, income_tax, cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and is_deleted=0 order by id");

    foreach($sql_std_para as $row )
    {
        $applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
        $applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
        $diff=datediff('d',$applying_period_date,$applying_period_to_date);
        for($j=0;$j<$diff;$j++)
        {
            $date_all=add_date(str_replace("'","",$applying_period_date),$j);
            $newdate =change_date_format($date_all,'','',1);
            $financial_para[$newdate][interest_expense]=$row[csf('interest_expense')];
            $financial_para[$newdate][income_tax]=$row[csf('income_tax')];
            $financial_para[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
        }
    }


	//,min(b.PUB_SHIPMENT_DATE) as MIN_PUB_SHIPMENT_DATE,MAX(b.PUB_SHIPMENT_DATE) as MAX_PUB_SHIPMENT_DATE ,min(b.SHIPMENT_DATE) as MIN_SHIPMENT_DATE,MAX(b.SHIPMENT_DATE) as MAX_SHIPMENT_DATE
	ob_start();

    if($excel_type==2)
    {

        ?>
        <div>
        <table width="1320" cellspacing="0">
            <tr class="form_caption" style="border:none;">
                    <td colspan="17" align="center" style="border:none;font-size:14px; font-weight:bold" >Date Wise Production Report</td>
             </tr>
            <tr style="border:none;">
                    <td colspan="17" align="center" style="border:none; font-size:16px; font-weight:bold">
                        Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
                    </td>
              </tr>
              <tr style="border:none;">
                    <td colspan="17" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? echo "From $fromDate To $toDate" ;?>
                    </td>
              </tr>
        </table>
        <div style="max-height:200px; overflow-y:scroll; width:1580px" id="scroll_body2" >
         <table width="1520" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
            <thead>
                <tr>
                    <th style="word-break: break-all;" width="30">Sl.</th>
                    <th width="120">Buyer Name</th>
                    <th width="80">Cut Qty</th>
                    <th width="80">Sent to Print</th>
                    <th width="80">Rev Print</th>
                    <th width="80">Sent to Emb</th>
                    <th width="80">Rev Emb</th>
                    <th width="80">Sent to Wash</th>
                    <th width="80">Rev Wash</th>
                    <th width="80">Sent to Sp. Works</th>
                    <th width="80">Rev Sp. Works</th>
                    <th width="80">Sew Input(Inhouse)</th>
                    <th width="80">Sew Input (Outbound)</th>
                    <th width="80">Sew Output(Inhouse)</th>
                    <th width="80">Sew Output (Outbound)</th>
                    <th width="80">Total CM</th>
                    <th width="80">Produce Minute</th>
                    <th width="80">EPM</th>
                    <th>Prod. Value (FOB)</th>


                 </tr>
            </thead>
            <?
            $job_arr=array();
            if($db_type==0) $year_cond="YEAR(a.insert_date)";
            else if($db_type==2) $year_cond="to_char(a.insert_date,'YYYY')";

	$all_po_ids_cond=where_con_using_array($all_po_ids_array,0,'b.id');
	$job_sql="SELECT a.job_no_prefix_num, a.job_no, a.set_smv,a.buyer_name, $year_cond as year, a.style_ref_no, b.id as ID, b.po_number, b.grouping, b.file_no, (b.unit_price/a.total_set_qnty) as unit_price, b.po_quantity, a.total_set_qnty as ratio, a.order_uom,c.SMV_SET,c.GMTS_ITEM_ID,c.SMV_PCS_PRECOST from wo_po_details_master a, wo_po_break_down b,WO_PO_DETAILS_MAS_SET_DETAILS c where a.id=b.job_id AND c.job_id = b.job_id AND c.job_id = a.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_name=$cbo_company_name $buyer_name $file_no_cond $internal_ref_cond $all_po_ids_cond";

	// echo $job_sql;die;

	$poIdArr=array();
	$job_arr=array();
	$item_sm_arr=array();
    $buyer_po_item_smv_array=array();
	$job_sql_res=sql_select($job_sql);
	foreach($job_sql_res as $row)
	{
		$allJobArr[$row[csf("job_no")]]=$row[csf("job_no")];
		$poIdArr[$row[csf("id")]]=$row[csf("id")];
		$job_arr[$row[csf("id")]]=$row[csf("job_no_prefix_num")].'!!'.$row[csf("job_no")].'!!'.$row[csf("buyer_name")].'!!'.$row[csf("style_ref_no")].'!!'.$row[csf("po_number")].'!!'.$row[csf("grouping")].'!!'.$row[csf("file_no")].'!!'.$row[csf("unit_price")].'!!'.$row[csf("po_quantity")].'!!'.$row[csf("ratio")].'!!'.$row[csf("order_uom")].'!!'.$row[csf("year")].'!!'.$row[csf("set_smv")];

		$item_sm_arr[$row[csf("id")]][$row[GMTS_ITEM_ID]]=$row[SMV_SET];
		$item_precost_smv_arr[$row[ID]][$row[GMTS_ITEM_ID]]=$row[SMV_PCS_PRECOST];
		$buyer_po_item_smv_array[$row[BUYER_NAME]][$row[ID]][$row[GMTS_ITEM_ID]]=$row[SMV_PCS_PRECOST];

	}
    //echo "<pre>"; print_r($buyer_po_item_smv_array);
	unset($job_sql_res);


    $job_no_cond=where_con_using_array($allJobArr,1,'JOB_NO');
	$preCostSql="select JOB_NO, COSTING_PER,SEW_EFFI_PERCENT,EFFICIENCY_WASTAGE_PERCENT,CUT_SMV,CUT_EFFI_PERCENT,COSTING_DATE,EXCHANGE_RATE from wo_pre_cost_mst where STATUS_ACTIVE=1 and IS_DELETED=0 $job_no_cond ";
	$preCostSqlRes=sql_select($preCostSql);
	foreach($preCostSqlRes as $row)
	{
		$precostDataArr[$row[JOB_NO]][COSTING_PER]=$row[COSTING_PER];
		$precostDataArr[$row[JOB_NO]][EFFICIENCY_WASTAGE_PERCENT]=$row[EFFICIENCY_WASTAGE_PERCENT]/100;
		$precostDataArr[$row[JOB_NO]][CUT_SMV]=$row[CUT_SMV];
		$precostDataArr[$row[JOB_NO]][CUT_EFFI_WASTAGE_PERCENT]=((100-$row[CUT_EFFI_PERCENT]))/100;
		$precostDataArr[$row[JOB_NO]][COSTING_DATE]=change_date_format($row[COSTING_DATE],'','',1);
		$precostDataArr[$row[JOB_NO]][EXCHANGE_RATE]=$row[EXCHANGE_RATE];
	}
			$poIds_cond=where_con_using_array($poIdArr,0,'po_break_down_id');
			$buyer_fullQty_arr=array();
            $prod_date_qty_arr=array();
            $prod_dlfl_qty_arr=array();
            $all_data_arr=array();
            $sql_dtls="SELECT location, company_id, floor_id, sewing_line, po_break_down_id, production_date, item_number_id, production_type, production_source, embel_name, max(prod_reso_allo) as prod_reso_allo, sum(production_quantity) as production_quantity, sum(re_production_qty) as re_production_qty, sum(reject_qnty) as reject_qnty, sum(carton_qty) as carton_qty
            from pro_garments_production_mst
            where company_id=$cbo_company_name and is_deleted=0 and status_active=1 and production_type in(1,2,3,4,5) $txt_date $floor_name $location_cond $garmentsNature $poIds_cond
            group by location, company_id, floor_id, sewing_line, po_break_down_id, production_date, item_number_id, production_type, production_source, embel_name
            order by production_date ASC";
			//echo $sql_dtls;
            $sql_dtls_res=sql_select($sql_dtls);

            foreach($sql_dtls_res as $row)
            {
                $buyer_name_dat="";
                $all_job_data=$job_arr[$row[csf("po_break_down_id")]];
                $ex_job_data=explode('!!',$all_job_data);

                $buyer_name_dat=$ex_job_data[2];
				$job_smv=$ex_job_data[12];
				$job_no=$ex_job_data[1];
				$ratio=$ex_job_data[9];

                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];

                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];

				if($row[csf("sewing_line")]=="") $row[csf("sewing_line")]=0;
                if($row[csf("floor_id")]=="") $row[csf("floor_id")]=0;
                if($row[csf("location")]=="") $row[csf("location")]=0;

                if($row[csf("production_type")]==5)
                {

                    $sewing_line='';
                    $resource_allo=$row[csf("prod_reso_allo")];
                    if($resource_allo==1)
                    {
                        $sewing_line=$prod_reso_arr[$row[csf("sewing_line")]];

                    }
                    else $sewing_line=$row[csf("sewing_line")];

                    $prod_key=$row[csf("location")]."**".$row[csf("floor_id")];
                    $prod_dlfl_qty_arr[$row[csf("location")]][$row[csf("floor_id")]][$sewing_line][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("production_date")]]["sew_out_qnty"]+=$row[csf("production_quantity")];

                    $prod_dlfl_qty_arr[$row[csf("location")]][$row[csf("floor_id")]][$sewing_line][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("production_date")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];

                    //------------------------cm and fov value

				    $dzn_qnty=0;
				    if($precostDataArr[$job_no][COSTING_PER]==1) $dzn_qnty=12;
				    else if($precostDataArr[$job_no][COSTING_PER]==3) $dzn_qnty=12*2;
				    else if($precostDataArr[$job_no][COSTING_PER]==4) $dzn_qnty=12*3;
				    else if($precostDataArr[$job_no][COSTING_PER]==5) $dzn_qnty=12*4;
				    else $dzn_qnty=1;
				    //$dzn_qnty=$dzn_qnty*$ratio;


				    $COSTING_DATE=$precostDataArr[$job_no][COSTING_DATE];
				    $cpm=$financial_para[$COSTING_DATE][cost_per_minute];
				    $CUT_SMV=$precostDataArr[$job_no][CUT_SMV];
				    $EFFI_WAST_PER=$precostDataArr[$job_no][EFFICIENCY_WASTAGE_PERCENT];
				    $EXCHANGE_RATE=$precostDataArr[$job_no][EXCHANGE_RATE];
			  	    $item_smv=$item_precost_smv_arr[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]];


				    $CMPcs=(($item_smv*$cpm*$dzn_qnty)+($item_smv*$cpm*$dzn_qnty*$EFFI_WAST_PER))/$EXCHANGE_RATE;
			  	    $cm_pcs_value=$CMPcs;

			        //$cm_pcs_value=($cm_value_contribution_margine[$job_no]/$dzn_qnty);

			        $buyer_fullQty_arr[$buyer_name_dat][110]['0']['totalCM']+=($row[csf("production_quantity")]*($cm_pcs_value/12));

                    $buyer_fullQty_arr[$buyer_name_dat]['totalProduceMinit']+=($row[csf("production_quantity")]*$item_smv);

			        $unit_price=number_format($ex_job_data[7],2);
			        $buyer_fullQty_arr[$buyer_name_dat][110]['0']['FOVvalue']+=$row[csf("production_quantity")]*$unit_price;
			        //----------------

				}

            }

            unset($sql_dtls_res);
            $b=1;
            foreach($buyer_fullQty_arr as $buyer_id=>$buyer_data)
            {
                if ($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $cutting_qty=$printing_qty=$printreceived_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewOut_inQty=$sewOut_outQty=0;
                $cutting_qty=$buyer_data['1']['0']['pQty'];
                $printing_qty=$buyer_data['2']['1']['embQty'];
                $printreceived_qty=$buyer_data['3']['1']['embQty'];
                $emb_qty=$buyer_data['2']['2']['embQty'];
                $embRec_qty=$buyer_data['3']['2']['embQty'];

                $wash_qty=$buyer_data['2']['3']['embQty'];
                $washRec_qty=$buyer_data['3']['3']['embQty'];
                $special_qty=$buyer_data['2']['4']['embQty'];
                $specialRec_qty=$buyer_data['3']['4']['embQty'];
                $sewIn_inQty=$buyer_data['4']['0']['pQty'];
                $sewIn_outQty=$buyer_data['4']['3']['sQty'];
                $sewOut_inQty=$buyer_data['5']['0']['pQty'];
                $sewOut_outQty=$buyer_data['5']['3']['sQty'];
                $produce_mint=$buyer_data["totalProduceMinit"];




				 $total_cm=$buyer_data[110]['0']['totalCM'];
                 //echo  "total".$total_cm."<br> minit".$produce_mint;
				 $total_fov_value=$buyer_data[110]['0']['FOVvalue'];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $b; ?>">
                    <td align="center"><? echo $b;?></td>
                    <td><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($cutting_qty); ?></td>
                    <td align="right"><? echo number_format($printing_qty); ?></td>
                    <td align="right"><? echo number_format($printreceived_qty); ?></td>
                    <td align="right"><? echo number_format($emb_qty); ?></td>
                    <td align="right"><? echo number_format($embRec_qty); ?></td>
                    <td align="right"><? echo number_format($wash_qty); ?></td>
                    <td align="right"><? echo number_format($washRec_qty);  ?></td>
                    <td align="right"><? echo number_format($special_qty); ?></td>
                    <td align="right"><? echo number_format($specialRec_qty);  ?></td>
                    <td align="right"><? echo number_format($sewIn_inQty); ?></td>
                    <td align="right"><? echo number_format($sewIn_outQty); ?></td>
                    <td align="right"><? echo number_format($sewOut_inQty); ?></td>

                    <td align="right"><? echo number_format($sewOut_outQty); ?></td>
                    <td align="right"><?=fn_number_format($total_cm);?></td>

                    <!-- =================== -->
                    <td align="right"><? echo $produce_mint;?></td>
                    <td align="right" title="Total CM / Produce Minute"><?

                    $emp=$total_cm/$produce_mint; echo fn_number_format($emp,4);?></td>

                    <td align="right"><?=number_format($total_fov_value);?></td>

                </tr>
                <?
                //echo $emp;
                $sumCutting_qty+=$cutting_qty;
                $sumPrinting_qty+=$printing_qty;
                $sumPrintreceived_qty+=$printreceived_qty;
                $sumEmb_qty+=$emb_qty;
                $sumEmbRec_qty+=$embRec_qty;
                $sumWash_qty+=$wash_qty;
                $sumWashRec_qty+=$washRec_qty;
                $sumSpecial_qty+=$special_qty;
                $sumSpecialRec_qty+=$specialRec_qty;
                $sumSewIn_inQty+=$sewIn_inQty;
                $sumSewIn_outQty+=$sewIn_outQty;
                $sumSewOut_inQty+=$sewOut_inQty;
                $sumSewOut_outQty+=$sewOut_outQty;
                $sumEmp+=fn_number_format($emp,4);
                $sumproduce_mint+=$produce_mint;
                $grand_total_cm+=fn_number_format($total_cm,0);
				$grand_total_fov_value+=$total_fov_value;
                $b++;
            }
            ?>
            <tfoot>
                <tr>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th><? echo number_format($sumCutting_qty); ?></th>
                    <th><? echo number_format($sumPrinting_qty); ?></th>
                    <th><? echo number_format($sumPrintreceived_qty); ?></th>
                    <th><? echo number_format($sumEmb_qty); ?></th>
                    <th><? echo number_format($sumEmbRec_qty); ?></th>
                    <th><? echo number_format($sumWash_qty); ?></th>
                    <th><? echo number_format($sumWashRec_qty); ?></th>
                    <th><? echo number_format($sumSpecial_qty); ?></th>
                    <th><? echo number_format($sumSpecialRec_qty); ?></th>
                    <th><? echo number_format($sumSewIn_inQty); ?></th>
                    <th><? echo number_format($sumSewIn_outQty); ?></th>
                    <th><? echo number_format($sumSewOut_inQty); ?></th>
                    <th><? echo number_format($sumSewOut_outQty); ?></th>

                    <th align="right"><? echo number_format($grand_total_cm,0); ?></th>
                    <th align="right"><? echo number_format( $sumproduce_mint,0)?></th>

                    <th align="right"><? echo number_format($sumEmp,4) ?></th>

                    <th align="right"><? echo number_format($grand_total_fov_value,0); ?></th>

                 </tr>
            </tfoot>
            </table>
        </div>
        <br />
        <div style="width:1530px" align="left">
            <table width="1510" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" align="left">
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Line No</th>
                    <th width="100">Buyer Name</th>
                    <th width="110">Style Ref</th>
                    <th width="130">Order Number</th>
                    <th width="80">PO Quantity</th>
                    <th width="130">Gmts Item</th>
                    <th width="80">Production Date</th>
                    <th width="80">Sewing Output</th>
                    <th width="80" title="((Item smv*cpm*Costing Per)+(CUT SMV*cpm*Costing Per))*EFFI WAST PER)/EXCHANGE RATE)/12">CM/Dzn</th>
                    <th width="100">Total CM</th>
                    <th width="80">FOB/Pcs</th>
                    <th width="100">Prod. Value (FOB) DD</th>
                    <th width="50">SMV</th>
                    <th width="80">Remarks</th>
                    <!-- ======================= -->
                    <th>Produce Minute</th>
                    <th width="80">EPM</th>
                </thead>
            </table>
            <div style="max-height:325px; overflow-y:scroll; width:1530px" id="scroll_body" align="left">
            <table cellspacing="0" border="1" class="rpt_table"  width="1510" rules="all" id="table_body" align="left">
            <?
            $tot_embgmtIssue_qty=$tot_embgmtRec_qty=$tot_job_smv=0;
            foreach($prod_dlfl_qty_arr as $flore_location_key=>$flore_location_data)
            {
            foreach($flore_location_data as $floor_id=>$floor_data)//new
            {
                ?>
                <tr><td colspan="15" style="font-size:14px; font-weight:bold">Floor : <? echo $floor_library[$floor_id]; ?>, Location : <? echo $location_library[$flore_location_key]; ?></td></tr>
                <?
                $j=1;

                foreach($floor_data as $sewing_line_id=>$sewing_line_data)
                {
                    foreach($sewing_line_data as $po_id=>$po_data)
                    {
                        foreach($po_data as $item_id=>$item_data)
                        {
                            ksort($item_data);
                            foreach($item_data as $prod_date=>$data_all)
                            {

                                    $buyer_name_dat=""; $job_no_pre=''; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no=''; $unit_price=''; $po_qty=''; $ratio=''; $order_uom=''; $job_year=''; $all_job_data=''; $all_job_data='';$floor_job_smv=0;
                                    $all_job_data=$job_arr[$po_id];

                                    $ex_job_data=explode('!!',$all_job_data);

                                    $job_no_pre=$ex_job_data[0];
                                    $job_no=$ex_job_data[1];
                                    $buyer_name_dat=$ex_job_data[2];
                                    $style_ref=$ex_job_data[3];
                                    $po_no=$ex_job_data[4];
                                    $ref_no=$ex_job_data[5];
                                    $file_no=$ex_job_data[6];
                                    $unit_price=number_format($ex_job_data[7],2);
                                    $po_qty=$ex_job_data[8];
                                    $ratio=$ex_job_data[9];
                                    $order_uom=$ex_job_data[10];
                                    $job_year=$ex_job_data[11];
									$job_smv=$ex_job_data[12];
									$item_smv=$item_sm_arr[$po_id][$item_id];


									//cmdzn start-----------------

									$dzn_qnty=0;
									if($precostDataArr[$job_no][COSTING_PER]==1) $dzn_qnty=12;
									else if($precostDataArr[$job_no][COSTING_PER]==3) $dzn_qnty=12*2;
									else if($precostDataArr[$job_no][COSTING_PER]==4) $dzn_qnty=12*3;
									else if($precostDataArr[$job_no][COSTING_PER]==5) $dzn_qnty=12*4;
									else $dzn_qnty=1;


									$item_precost_smv=$item_precost_smv_arr[$po_id][$item_id];
									$COSTING_DATE=$precostDataArr[$job_no][COSTING_DATE];
									$cpm=$financial_para[$COSTING_DATE][cost_per_minute];
									$CUT_SMV=$precostDataArr[$job_no][CUT_SMV];
									$EFFI_WAST_PER=$precostDataArr[$job_no][EFFICIENCY_WASTAGE_PERCENT];
									$EXCHANGE_RATE=$precostDataArr[$job_no][EXCHANGE_RATE];

									$CMPcs=(($item_precost_smv*$cpm*$dzn_qnty)+($item_precost_smv*$cpm*$dzn_qnty*$EFFI_WAST_PER))/$EXCHANGE_RATE;

									 //echo $item_precost_smv.'*'.$cpm.'*'.$dzn_qnty.'*'.$EFFI_WAST_PER.'----';

									//----------------------end

                                    if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                    $sewing_line='';

                                    $line_number=explode(",",$sewing_line_id);
                                    foreach($line_number as $val)
                                    {
                                        if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
                                    }
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                        <td width="30"><? echo $j;?></td>
                                        <td width="100"><p><? echo $sewing_line; ?></p></td>
                                        <td width="100"><p><? echo $buyer_short_library[$buyer_name_dat]; ?></p></td>
                                        <td width="110"><p><? echo $style_ref;?></p></td>
                                        <td width="130"><p><a href="##" onClick="openmypage_order(<? echo $po_id;?>,<? echo $item_id; ?>,'orderQnty_popup');" ><? echo $po_no; ?></a></p></td>
                                        <td width="80" align="right"><p><? echo number_format($po_qty,0); ?></p></td>
                                        <td width="130"><p><? echo $garments_item[$item_id]; ?></p></td>
                                        <td width="80" align="center"><p><? echo '&nbsp;'.change_date_format($prod_date); ?></p></td>

                                        <td width="80" align="right"><p><? echo number_format($data_all["sew_out_qnty"],0); ?></p></td>
                                        <?
                                        $dzn_qnty=0; $cm_value=0; $cm_cost=0;
                                        if($precostDataArr[$job_no][COSTING_PER]==1) $dzn_qnty=12;
                                        else if($precostDataArr[$job_no][COSTING_PER]==3) $dzn_qnty=12*2;
                                        else if($precostDataArr[$job_no][COSTING_PER]==4) $dzn_qnty=12*3;
                                        else if($precostDataArr[$job_no][COSTING_PER]==5) $dzn_qnty=12*4;
                                        else $dzn_qnty=1;

                                        $dzn_qnty=$dzn_qnty*$ratio;
                                        //$cm_pcs_value=($cm_value_contribution_margine[$job_no]/$dzn_qnty);
                                        //$cm_dzn_value=($cm_value_contribution_margine[$job_no]/$dzn_qnty)*12;
                                        $cm_pcs_value=$CMPcs;
										//$cm_dzn_value=$CMPcs/12;
										$cm_dzn_value=$CMPcs;
										$sew_out_cm_value=$data_all["sew_out_qnty"]*($cm_pcs_value/12);
                                        $fob_value=$data_all["sew_out_qnty"]*$unit_price;

                                        $produce_min=$data_all["sew_out_qnty"] * $item_smv;


                                        ?>

                                        <td width="80" align="right" title="<? echo $dzn_qnty; ?>"><? if($data_all["sew_out_qnty"]!=0) echo number_format($cm_dzn_value,2); else echo "0"; ?></td>
                                        <td width="100" align="right"><? if($data_all["sew_out_qnty"]!=0) echo number_format($sew_out_cm_value,2); else echo "0"; ?></td>
                                        <td width="80" align="right"><? if($data_all["sew_out_qnty"]!=0) echo number_format($unit_price,2); else echo "0"; ?></td>
                                        <td width="100" align="right"><? if($data_all["sew_out_qnty"]!=0) echo number_format($fob_value,2); else echo "0";  ?></td>

                                        <td width="50" align="right"><?= number_format($item_smv,2); ?></td>

                                        <td width="80" align="right"><a href="##" onclick="openmypage_remark(<? echo $po_id;?>,'date_wise_production_report');" > View</a></td>
                                        <td align="right"><? echo number_format($produce_min,0)?></td>
                                        <td width="80" align="right" title="Total CM / Produce Minute"><? echo number_format(($sew_out_cm_value/$produce_min),4)?></td>
                                    </tr>
                                    <?
                                    $flore_sew_out_qnty +=$data_all["sew_out_qnty"];
                                    $flore_sew_out_cm_value +=$sew_out_cm_value;
                                    $flore_fob_value +=$fob_value;
                                    $floor_job_smv +=$job_smv;
                                    $floor_produce_min+=$produce_min;
									//$floor_emp +=$sew_out_cm_value/$produce_min;

                                    $tot_sew_out_qnty+=$data_all["sew_out_qnty"];
                                    $tot_sew_out_cm_value +=$sew_out_cm_value;
                                    $tot_fob_value +=$fob_value;
									$tot_job_smv +=$job_smv;
                                    //$tot_emp +=$sew_out_cm_value/$produce_min;
                                    $j++;
                                    $i++;

                            }
                        }
                    }
                }

                ?>
                <tr bgcolor="#FFFFCC">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td colspan="2" align="right">Floor Total :</td>
                    <td align="right"><? echo number_format($flore_sew_out_qnty,2); ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($flore_sew_out_cm_value,2); ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($flore_fob_value,2); ?></td>
                    <td align="right"><? echo number_format($floor_job_smv,2); ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($floor_produce_min,2); ?></td>
                    <td align="right">&nbsp;<? //echo number_format($floor_emp,4); ?></td>
                </tr>
                <?
                $flore_sew_out_qnty=$flore_sew_out_cm_value=$flore_fob_value=0;
            }
            }
            ?>
            </table>
            </div>
            <!-- <table border="1" class="tbl_bottom"  width="1510" rules="all" id="" align="left">
                <tr>
                    <td width="30">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="80">Total:</td>
                    <td width="80"><? //echo number_format($tot_sew_out_qnty,2); ?></td>
                    <td width="80">&nbsp;</td>
                    <td width="100"><? //echo number_format($tot_sew_out_cm_value,2); ?></td>
                    <td width="80">&nbsp;</td>
                    <td width="100"><?// echo number_format($tot_fob_value,2); ?></td>
                    <td width="50"><? //echo number_format($tot_job_smv,2); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td width="80"><? //echo number_format($tot_emp,4); ?></td>
                </tr>
            </table> -->
        </div>
        </div>
        <?
    }



        $html = ob_get_contents();
        ob_clean();
        foreach (glob("$user_name*.xls") as $filename) {
            @unlink($filename);
        }
        //---------end------------//
        $name=time();
        $filename="".$user_name."_".$name.".xls";
        $create_new_doc = fopen($filename, 'w');
        $is_created = fwrite($create_new_doc, $html);
        echo "$html####$excel_type####$filename";
    exit();
}



if($action=="report_generate_cm_value3")// cm value 3 button
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
    $company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name");
    $buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
    $location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name");
    $floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
    $line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
    $prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

    $garments_nature=str_replace("'","",$cbo_garments_nature);
    if($garments_nature==1)$garments_nature="";
    $type = str_replace("'","",$cbo_type);
    $location = str_replace("'","",$cbo_location);
    $cbo_floor = str_replace("'","",$cbo_floor);
    $file_no = str_replace("'","",$txt_file_no);
    $internal_ref = str_replace("'","",$txt_internal_ref);
    $excel_type = str_replace("'","",$excel_type);
    if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and b.company_name=$cbo_company_name";
    if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
    if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
    else $txt_date=" and production_date between $txt_date_from and $txt_date_to";
    $fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
    $toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
    if ($location==0) $location_cond=""; else $location_cond=" and location=".$location." ";
    if($cbo_floor=="") $floor_name="";else $floor_name=" and floor_id in($cbo_floor)";
    if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
    if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

    $all_order_sewQty_array=array();
    if($type==1)
    {
        $colspn='50';
    }
    else if($type==2)
    {
        $colspn='50';
    }

    $all_po_ids_array=return_library_array( "SELECT po_break_down_id  from pro_garments_production_mst
            where  status_active=1 and is_deleted=0  $txt_date group by po_break_down_id ", "po_break_down_id", "po_break_down_id" );

    $sql_std_para=sql_select("SELECT interest_expense, income_tax, cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and is_deleted=0 order by id");

    foreach($sql_std_para as $row )
    {
        $applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
        $applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
        $diff=datediff('d',$applying_period_date,$applying_period_to_date);
        for($j=0;$j<$diff;$j++)
        {
            $date_all=add_date(str_replace("'","",$applying_period_date),$j);
            $newdate =change_date_format($date_all,'','',1);
            $financial_para[$newdate][interest_expense]=$row[csf('interest_expense')];
            $financial_para[$newdate][income_tax]=$row[csf('income_tax')];
            $financial_para[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
        }
    }


    //,min(b.PUB_SHIPMENT_DATE) as MIN_PUB_SHIPMENT_DATE,MAX(b.PUB_SHIPMENT_DATE) as MAX_PUB_SHIPMENT_DATE ,min(b.SHIPMENT_DATE) as MIN_SHIPMENT_DATE,MAX(b.SHIPMENT_DATE) as MAX_SHIPMENT_DATE
    ob_start();

    if($excel_type==3)
    {
        ?>
        <div>
        <table width="1320" cellspacing="0">
            <tr class="form_caption" style="border:none;">
                    <td colspan="17" align="center" style="border:none;font-size:14px; font-weight:bold" >Date Wise Production Report</td>
             </tr>
            <tr style="border:none;">
                    <td colspan="17" align="center" style="border:none; font-size:16px; font-weight:bold">
                        Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
                    </td>
              </tr>
              <tr style="border:none;">
                    <td colspan="17" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? echo "From $fromDate To $toDate" ;?>
                    </td>
              </tr>
        </table>
        <div style="max-height:200px; overflow-y:scroll; width:1380px" id="scroll_body2" >
        <table width="1360" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
            <thead>
                <tr>
                    <th style="word-break: break-all;" width="30">Sl.</th>
                    <th width="120">Buyer Name</th>
                    <th width="80">Cut Qty</th>
                    <th width="80">Sent to Print</th>
                    <th width="80">Rev Print</th>
                    <th width="80">Sent to Emb</th>
                    <th width="80">Rev Emb</th>
                    <th width="80">Sent to Wash</th>
                    <th width="80">Rev Wash</th>
                    <th width="80">Sent to Sp. Works</th>
                    <th width="80">Rev Sp. Works</th>
                    <th width="80">Sew Input(Inhouse)</th>
                    <th width="80">Sew Input (Outbound)</th>
                    <th width="80">Sew Output(Inhouse)</th>
                    <th width="80">Sew Output (Outbound)</th>
                    <th width="80">Total CM</th>
                    <th>Prod. Value (FOB)</th>


                 </tr>
            </thead>
            <?
            $job_arr=array();
            if($db_type==0) $year_cond="YEAR(a.insert_date)";
            else if($db_type==2) $year_cond="to_char(a.insert_date,'YYYY')";

        $all_po_ids_cond=where_con_using_array($all_po_ids_array,0,'id');
        $all_job_ids_array=return_library_array( "SELECT job_id  from wo_po_break_down where  status_active=1 and is_deleted=0  $all_po_ids_cond", "job_id", "job_id" );
        $all_job_ids_cond=where_con_using_array($all_job_ids_array,0,'a.id');

        $job_id_array=array();

        $job_sql="SELECT a.id, a.job_no_prefix_num, a.job_no, a.set_smv,a.buyer_name, $year_cond as year, a.style_ref_no, b.id as po_id, b.po_number, b.grouping, b.file_no, (b.unit_price/a.total_set_qnty) as unit_price, b.po_quantity, a.total_set_qnty as ratio, a.order_uom,c.SMV_SET,c.GMTS_ITEM_ID,c.SMV_PCS_PRECOST from wo_po_details_master a, wo_po_break_down b,WO_PO_DETAILS_MAS_SET_DETAILS c where a.id=b.job_id AND b.job_id = c.job_id AND a.id = c.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_name=$cbo_company_name $buyer_name $file_no_cond $internal_ref_cond $all_job_ids_cond";

         //echo $job_sql;die;


        $poIdArr=array();
        $job_arr=array();
        $job_smv_arr=array();
        $item_sm_arr=array();
        $job_sql_res=sql_select($job_sql);
        foreach($job_sql_res as $row)
        {
            $allJobArr[$row[csf("job_no")]]=$row[csf("job_no")];
            $job_smv_arr[$row[csf("job_no")]]+=$row["SMV_SET"];
            $poIdArr[$row[csf("po_id")]]=$row[csf("po_id")];
            $job_arr[$row[csf("po_id")]]=$row[csf("job_no_prefix_num")].'!!'.$row[csf("job_no")].'!!'.$row[csf("buyer_name")].'!!'.$row[csf("style_ref_no")].'!!'.$row[csf("po_number")].'!!'.$row[csf("grouping")].'!!'.$row[csf("file_no")].'!!'.$row[csf("unit_price")].'!!'.$row[csf("po_quantity")].'!!'.$row[csf("ratio")].'!!'.$row[csf("order_uom")].'!!'.$row[csf("year")].'!!'.$row[csf("set_smv")];

            $item_sm_arr[$row[csf("po_id")]][$row['GMTS_ITEM_ID']]=$row['SMV_SET'];
            $item_precost_smv_arr[$row['PO_ID']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS_PRECOST'];
            $job_id_array[$row['ID']] = $row['ID'];
        }
        unset($job_sql_res);
       // echo "<pre>";print_r($job_id_array);die();
       $job_ids = implode(",", $job_id_array);
        $costing_per_sql=("SELECT JOB_NO,COSTING_PER from wo_pre_cost_mst where status_active=1 and job_id in($job_ids)");
       // echo $costing_per_sql;die;
         $costing_per_arr=array();
         foreach(sql_select($costing_per_sql) as $cost_val)
         {
             $costing_per_arr[$cost_val['JOB_NO']] = $cost_val['COSTING_PER'];
         }
         unset($costing_per_sql);
         // echo "<pre>";print_r($costing_per_arr);die();



        $job_no_cond=where_con_using_array($allJobArr,1,'a.JOB_NO');
        $preCostSql="SELECT a.JOB_NO,b.TOTAL_SET_QNTY,a.CM_COST,a.costing_per_id from wo_pre_cost_dtls a,wo_po_details_master b where a.job_id=b.id and  a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and  b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.CM_COST is not null $job_no_cond ";
        // echo $preCostSql;die;

        $preCostSqlRes=sql_select($preCostSql);
        foreach($preCostSqlRes as $row)
        {
            $precostDataArr[$row['JOB_NO']]['cm_cost']=$row['CM_COST'];
            $precostDataArr[$row['JOB_NO']]['costing_per_id']=$row['COSTING_PER_ID'];
            $precostDataArr[$row['JOB_NO']]['total_set_qnty']=$row['TOTAL_SET_QNTY'];
        }
        // echo "<pre>";print_r($precostDataArr);die;


        $poIds_cond=where_con_using_array($poIdArr,0,'po_break_down_id');


        $buyer_fullQty_arr=array();
        $prod_date_qty_arr=array();
        $prod_dlfl_qty_arr=array();
        $all_data_arr=array();
        $sql_dtls="SELECT location, company_id, floor_id, sewing_line, po_break_down_id, production_date, item_number_id, production_type, production_source, embel_name, max(prod_reso_allo) as prod_reso_allo, sum(production_quantity) as production_quantity, sum(re_production_qty) as re_production_qty, sum(reject_qnty) as reject_qnty, sum(carton_qty) as carton_qty
        from pro_garments_production_mst
        where company_id=$cbo_company_name and is_deleted=0 and status_active=1 and production_type in(1,2,3,4,5) $txt_date $floor_name $location_cond $garmentsNature $poIds_cond
        group by location, company_id, floor_id, sewing_line, po_break_down_id, production_date, item_number_id, production_type, production_source, embel_name
        order by production_date ASC";
        //echo $sql_dtls;
        $sql_dtls_res=sql_select($sql_dtls);
        $item_wise_cm_arr = array();
        foreach($sql_dtls_res as $row)
        {
            $buyer_name_dat="";
            $all_job_data=$job_arr[$row[csf("po_break_down_id")]];
            $ex_job_data=explode('!!',$all_job_data);

            $buyer_name_dat=$ex_job_data[2];
            $job_smv=$ex_job_data[12];
            $job_no=$ex_job_data[1];
            $ratio=$ex_job_data[9];

            $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
            $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
            $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
            $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
            $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];


            if($row[csf("sewing_line")]=="") $row[csf("sewing_line")]=0;
            if($row[csf("floor_id")]=="") $row[csf("floor_id")]=0;
            if($row[csf("location")]=="") $row[csf("location")]=0;

            if($row[csf("production_type")]==5)
            {

                $sewing_line='';
                $resource_allo=$row[csf("prod_reso_allo")];
                if($resource_allo==1)
                {
                    $sewing_line=$prod_reso_arr[$row[csf("sewing_line")]];

                }
                else $sewing_line=$row[csf("sewing_line")];

                $prod_key=$row[csf("location")]."**".$row[csf("floor_id")];
                $prod_dlfl_qty_arr[$row[csf("location")]][$row[csf("floor_id")]][$sewing_line][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("production_date")]]["sew_out_qnty"]+=$row[csf("production_quantity")];

                $prod_dlfl_qty_arr[$row[csf("location")]][$row[csf("floor_id")]][$sewing_line][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("production_date")]]["prod_reso_allo"]=$row[csf("prod_reso_allo")];

                   $costingPer = $costing_per_arr[$job_no];
                    if($costingPer==1) $pcs_value=1*12;
                    else if($costingPer==2) $pcs_value=1*1;
                    else if($costingPer==3) $pcs_value=2*12;
                    else if($costingPer==4) $pcs_value=3*12;
                    else if($costingPer==5) $pcs_value=4*12;

               //------------------------cm and fov value



                /*$dzn_qnty=0;
                if($precostDataArr[$job_no][COSTING_PER]==1) $dzn_qnty=12;
                else if($precostDataArr[$job_no][COSTING_PER]==3) $dzn_qnty=12*2;
                else if($precostDataArr[$job_no][COSTING_PER]==4) $dzn_qnty=12*3;
                else if($precostDataArr[$job_no][COSTING_PER]==5) $dzn_qnty=12*4;
                else $dzn_qnty=1;
                //$dzn_qnty=$dzn_qnty*$ratio;


                $COSTING_DATE=$precostDataArr[$job_no][COSTING_DATE];
                $cpm=$financial_para[$COSTING_DATE][cost_per_minute];
                $CUT_SMV=$precostDataArr[$job_no][CUT_SMV];
                $EFFI_WAST_PER=$precostDataArr[$job_no][EFFICIENCY_WASTAGE_PERCENT];
                $EXCHANGE_RATE=$precostDataArr[$job_no][EXCHANGE_RATE];
                $item_smv=$item_precost_smv_arr[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]];


                $CMPcs=(($item_smv*$cpm*$dzn_qnty)+($item_smv*$cpm*$dzn_qnty*$EFFI_WAST_PER))/$EXCHANGE_RATE;
                $cm_pcs_value=$CMPcs;*/

                // $cm_pcs_value = $precostDataArr[$job_no][cm_cost];
                // $cm_pcs_value = $precostDataArr[$job_no][cm_cost] * (($item_sm_arr[$row[csf("po_break_down_id")]][$row[csf('item_number_id')]]/$job_smv_arr[$job_no])*100)/100;
                $cm_pcs_value = ($precostDataArr[$job_no]['total_set_qnty']>0) ? (($precostDataArr[$job_no]['cm_cost'] / $pcs_value) /($precostDataArr[$job_no]['total_set_qnty']) ) : 0;
                $item_wise_cm_arr[$row[csf("po_break_down_id")]][$row[csf('item_number_id')]] = $cm_pcs_value;
                // echo $precostDataArr[$job_no][cm_cost]."*((".$item_sm_arr[$row[csf("po_break_down_id")]][$row[csf('item_number_id')]]."/".$job_smv_arr[$job_no].")*100)/100<br>";

                // echo $garments_item[$row[csf('item_number_id')]]."==".$cm_pcs_value."==".$precostDataArr[$job_no][cm_cost]."*((".$item_sm_arr[$row[csf("po_break_down_id")]][$row[csf('item_number_id')]]."/".$job_smv_arr[$job_no].")*100)/100<br>";

                //$cm_pcs_value=($cm_value_contribution_margine[$job_no]/$dzn_qnty);

                // $buyer_fullQty_arr[$buyer_name_dat][110]['0']['totalCM']+=($row[csf("production_quantity")]*$cm_pcs_value);
                // $buyer_fullQty_arr[$buyer_name_dat][110]['0']['totalCM']+=($row[csf("production_quantity")]*($precostDataArr[$job_no]['cm_cost']) / $pcs_value );
                $buyer_fullQty_arr[$buyer_name_dat][110]['0']['totalCM']+=($row[csf("production_quantity")]*$cm_pcs_value);




                $unit_price=number_format($ex_job_data[7],2);
                $buyer_fullQty_arr[$buyer_name_dat][110]['0']['FOVvalue']+=$row[csf("production_quantity")]*$unit_price;
                //----------------

            }

        }

            unset($sql_dtls_res);
            $b=1;
            foreach($buyer_fullQty_arr as $buyer_id=>$buyer_data)
            {
                if ($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $cutting_qty=$printing_qty=$printreceived_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewOut_inQty=$sewOut_outQty=0;
                $cutting_qty=$buyer_data['1']['0']['pQty'];
                $printing_qty=$buyer_data['2']['1']['embQty'];
                $printreceived_qty=$buyer_data['3']['1']['embQty'];
                $emb_qty=$buyer_data['2']['2']['embQty'];
                $embRec_qty=$buyer_data['3']['2']['embQty'];

                $wash_qty=$buyer_data['2']['3']['embQty'];
                $washRec_qty=$buyer_data['3']['3']['embQty'];
                $special_qty=$buyer_data['2']['4']['embQty'];
                $specialRec_qty=$buyer_data['3']['4']['embQty'];
                $sewIn_inQty=$buyer_data['4']['0']['pQty'];
                $sewIn_outQty=$buyer_data['4']['3']['sQty'];
                $sewOut_inQty=$buyer_data['5']['0']['pQty'];
                $sewOut_outQty=$buyer_data['5']['3']['sQty'];

                 $total_cm=$buyer_data[110]['0']['totalCM'];
                 $total_fov_value=$buyer_data[110]['0']['FOVvalue'];

                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $b; ?>">
                    <td align="center"><? echo $b;?></td>
                    <td><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($cutting_qty); ?></td>
                    <td align="right"><? echo number_format($printing_qty); ?></td>
                    <td align="right"><? echo number_format($printreceived_qty); ?></td>
                    <td align="right"><? echo number_format($emb_qty); ?></td>
                    <td align="right"><? echo number_format($embRec_qty); ?></td>
                    <td align="right"><? echo number_format($wash_qty); ?></td>
                    <td align="right"><? echo number_format($washRec_qty);  ?></td>
                    <td align="right"><? echo number_format($special_qty); ?></td>
                    <td align="right"><? echo number_format($specialRec_qty);  ?></td>
                    <td align="right"><? echo number_format($sewIn_inQty); ?></td>
                    <td align="right"><? echo number_format($sewIn_outQty); ?></td>
                    <td align="right"><? echo number_format($sewOut_inQty); ?></td>
                    <td align="right"><? echo number_format($sewOut_outQty); ?></td>

                    <td align="right"><?=number_format($total_cm,2);?></td>
                    <td align="right"><?=number_format($total_fov_value);?></td>

                </tr>
                <?
                $sumCutting_qty+=$cutting_qty;
                $sumPrinting_qty+=$printing_qty;
                $sumPrintreceived_qty+=$printreceived_qty;
                $sumEmb_qty+=$emb_qty;
                $sumEmbRec_qty+=$embRec_qty;
                $sumWash_qty+=$wash_qty;
                $sumWashRec_qty+=$washRec_qty;
                $sumSpecial_qty+=$special_qty;
                $sumSpecialRec_qty+=$specialRec_qty;
                $sumSewIn_inQty+=$sewIn_inQty;
                $sumSewIn_outQty+=$sewIn_outQty;
                $sumSewOut_inQty+=$sewOut_inQty;
                $sumSewOut_outQty+=$sewOut_outQty;
                $grand_total_cm+=$total_cm;
                $grand_total_fov_value+=$total_fov_value;
                $b++;
            }
            ?>
            <tfoot>
                <tr>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th><? echo number_format($sumCutting_qty); ?></th>
                    <th><? echo number_format($sumPrinting_qty); ?></th>
                    <th><? echo number_format($sumPrintreceived_qty); ?></th>
                    <th><? echo number_format($sumEmb_qty); ?></th>
                    <th><? echo number_format($sumEmbRec_qty); ?></th>
                    <th><? echo number_format($sumWash_qty); ?></th>
                    <th><? echo number_format($sumWashRec_qty); ?></th>
                    <th><? echo number_format($sumSpecial_qty); ?></th>
                    <th><? echo number_format($sumSpecialRec_qty); ?></th>
                    <th><? echo number_format($sumSewIn_inQty); ?></th>
                    <th><? echo number_format($sumSewIn_outQty); ?></th>
                    <th><? echo number_format($sumSewOut_inQty); ?></th>
                    <th><? echo number_format($sumSewOut_outQty); ?></th>

                    <th align="right"><? echo number_format($grand_total_cm,2); ?></th>
                    <th align="right"><? echo number_format($grand_total_fov_value); ?></th>

                 </tr>
            </tfoot>
            </table>
        </div>
        <br />
        <!-- ============================================================================================== /
        /                                             Details Part                                          /
        ================================================================================================= -->
        <div style="width:1430px" align="left">
            <table width="1410" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" align="left">
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Line No</th>
                    <th width="100">Buyer Name</th>
                    <th width="110">Style Ref</th>
                    <th width="130">Order Number</th>
                    <th width="80">PO Quantity</th>
                    <th width="130">Gmts Item</th>
                    <th width="80">Production Date</th>
                    <th width="80">Sewing Output</th>
                    <th width="80" title="((Item smv*cpm*Costing Per)+(CUT SMV*cpm*Costing Per))*EFFI WAST PER)/EXCHANGE RATE)/12">CM</th>
                    <th width="60">Costing Per</th>
                    <th width="100">Total CM</th>
                    <th width="80">FOB/Pcs</th>
                    <th width="100">Prod. Value (FOB) DD</th>
                    <th width="50">SMV</th>
                    <th>Remarks</th>
                </thead>
            </table>
            <div style="max-height:325px; overflow-y:scroll; width:1430px" id="scroll_body" align="left">
            <table cellspacing="0" border="1" class="rpt_table"  width="1410" rules="all" id="table_body" align="left">
            <?
            $tot_embgmtIssue_qty=$tot_embgmtRec_qty=$tot_job_smv=0;
            foreach($prod_dlfl_qty_arr as $flore_location_key=>$flore_location_data)
            {
                foreach($flore_location_data as $floor_id=>$floor_data)//new
                {
                    ?>
                    <tr><td colspan="15" style="font-size:14px; font-weight:bold">Floor : <? echo $floor_library[$floor_id]; ?>, Location : <? echo $location_library[$flore_location_key]; ?></td></tr>
                    <?
                    $j=1;

                    foreach($floor_data as $sewing_line_id=>$sewing_line_data)
                    {
                        foreach($sewing_line_data as $po_id=>$po_data)
                        {
                            foreach($po_data as $item_id=>$item_data)
                            {
                                ksort($item_data);
                                foreach($item_data as $prod_date=>$data_all)
                                {

                                        $buyer_name_dat=""; $job_no_pre=''; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no=''; $unit_price=''; $po_qty=''; $ratio=''; $order_uom=''; $job_year=''; $all_job_data=''; $all_job_data='';$floor_job_smv=0;
                                        $all_job_data=$job_arr[$po_id];

                                        $ex_job_data=explode('!!',$all_job_data);

                                        $job_no_pre=$ex_job_data[0];
                                        $job_no=$ex_job_data[1];
                                        $buyer_name_dat=$ex_job_data[2];
                                        $style_ref=$ex_job_data[3];
                                        $po_no=$ex_job_data[4];
                                        $ref_no=$ex_job_data[5];
                                        $file_no=$ex_job_data[6];
                                        $unit_price=number_format($ex_job_data[7],2);
                                        $po_qty=$ex_job_data[8];
                                        $ratio=$ex_job_data[9];
                                        $order_uom=$ex_job_data[10];
                                        $job_year=$ex_job_data[11];
                                        $job_smv=$ex_job_data[12];
                                        $item_smv=$item_sm_arr[$po_id][$item_id];

                                        $costingPer = $costing_per_arr[$job_no];
                                        if($costingPer==1) $pcs_value=1*12;
                                        else if($costingPer==2) $pcs_value=1*1;
                                        else if($costingPer==3) $pcs_value=2*12;
                                        else if($costingPer==4) $pcs_value=3*12;
                                        else if($costingPer==5) $pcs_value=4*12;
                                        // echo $pcs_value;die;


                                        //cmdzn start-----------------

                                        /*$dzn_qnty=0;
                                        if($precostDataArr[$job_no][COSTING_PER]==1) $dzn_qnty=12;
                                        else if($precostDataArr[$job_no][COSTING_PER]==3) $dzn_qnty=12*2;
                                        else if($precostDataArr[$job_no][COSTING_PER]==4) $dzn_qnty=12*3;
                                        else if($precostDataArr[$job_no][COSTING_PER]==5) $dzn_qnty=12*4;
                                        else $dzn_qnty=1;


                                        $item_precost_smv=$item_precost_smv_arr[$po_id][$item_id];
                                        $COSTING_DATE=$precostDataArr[$job_no][COSTING_DATE];
                                        $cpm=$financial_para[$COSTING_DATE][cost_per_minute];
                                        $CUT_SMV=$precostDataArr[$job_no][CUT_SMV];
                                        $EFFI_WAST_PER=$precostDataArr[$job_no][EFFICIENCY_WASTAGE_PERCENT];
                                        $EXCHANGE_RATE=$precostDataArr[$job_no][EXCHANGE_RATE];

                                        $CMPcs=(($item_precost_smv*$cpm*$dzn_qnty)+($item_precost_smv*$cpm*$dzn_qnty*$EFFI_WAST_PER))/$EXCHANGE_RATE;*/

                                         //echo $item_precost_smv.'*'.$cpm.'*'.$dzn_qnty.'*'.$EFFI_WAST_PER.'----';

                                        //----------------------end

                                        if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                        $sewing_line='';

                                        $line_number=explode(",",$sewing_line_id);
                                        foreach($line_number as $val)
                                        {
                                            if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
                                        }
                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                            <td width="30"><? echo $j;?></td>
                                            <td width="100"><p><? echo $sewing_line; ?></p></td>
                                            <td width="100"><p><? echo $buyer_short_library[$buyer_name_dat]; ?></p></td>
                                            <td width="110"><p><? echo $style_ref;?></p></td>
                                            <td width="130"><p><a href="##" onClick="openmypage_order(<? echo $po_id;?>,<? echo $item_id; ?>,'orderQnty_popup');" ><? echo $po_no; ?></a></p></td>
                                            <td width="80" align="right"><p><? echo number_format($po_qty,0); ?></p></td>
                                            <td width="130"><p><? echo $garments_item[$item_id]; ?></p></td>
                                            <td width="80" align="center"><p><? echo '&nbsp;'.change_date_format($prod_date); ?></p></td>
                                            <td width="80" align="right"><p><? echo number_format($data_all["sew_out_qnty"],0); ?></p></td>
                                            <?
                                            /*$dzn_qnty=0; $cm_value=0; $cm_cost=0;
                                            if($precostDataArr[$job_no][COSTING_PER]==1) $dzn_qnty=12;
                                            else if($precostDataArr[$job_no][COSTING_PER]==3) $dzn_qnty=12*2;
                                            else if($precostDataArr[$job_no][COSTING_PER]==4) $dzn_qnty=12*3;
                                            else if($precostDataArr[$job_no][COSTING_PER]==5) $dzn_qnty=12*4;
                                            else $dzn_qnty=1;

                                            $dzn_qnty=$dzn_qnty*$ratio;
                                            //$cm_pcs_value=($cm_value_contribution_margine[$job_no]/$dzn_qnty);
                                            //$cm_dzn_value=($cm_value_contribution_margine[$job_no]/$dzn_qnty)*12;
                                            $cm_pcs_value=$CMPcs;
                                            //$cm_dzn_value=$CMPcs/12;
                                            $cm_dzn_value=$CMPcs;*/
                                            // $cm_dzn_value=$precostDataArr[$job_no][cm_cost];

                                            // $cm_dzn_value=$item_wise_cm_arr[$po_id][$item_id];
                                            $cm_pcs_value = (($precostDataArr[$job_no]['cm_cost'] / $pcs_value) /($precostDataArr[$job_no]['total_set_qnty']) );
                                            $costing_per_id = $precostDataArr[$job_no]['costing_per_id'];

                                            //   echo $pcs_value."<br>";

                                            $sew_out_cm_value=($data_all["sew_out_qnty"]*$cm_pcs_value);
                                            // echo $data_all["sew_out_qnty"]."**".$cm_pcs_value."***".$pcs_value."<br>";
                                            $fob_value=$data_all["sew_out_qnty"]*$unit_price;

                                            ?>

                                            <td width="80" align="right" title=""><? if($data_all["sew_out_qnty"]!=0) echo number_format($cm_pcs_value,2); else echo "0"; ?></td>
                                            <td width="60" align="center" title=""><?=$costing_per[$costing_per_id];?></td>
                                            <td width="100" align="right"><? if($data_all["sew_out_qnty"]!=0) echo number_format($sew_out_cm_value,2); else echo "0"; ?></td>
                                            <td width="80" align="right"><? if($data_all["sew_out_qnty"]!=0) echo number_format($unit_price,2); else echo "0"; ?></td>
                                            <td width="100" align="right"><? if($data_all["sew_out_qnty"]!=0) echo number_format($fob_value,2); else echo "0";  ?></td>
                                            <td width="50" align="right"><?= number_format($item_smv,2); ?></td>
                                            <td align="center"><a href="##" onclick="openmypage_remark(<? echo $po_id;?>,'date_wise_production_report');" > View</a></td>
                                        </tr>
                                        <?
                                        $flore_sew_out_qnty +=$data_all["sew_out_qnty"];
                                        $flore_sew_out_cm_value +=$sew_out_cm_value;
                                        $flore_fob_value +=$fob_value;
                                        $floor_job_smv +=$job_smv;

                                        $tot_sew_out_qnty+=$data_all["sew_out_qnty"];
                                        $tot_sew_out_cm_value +=$sew_out_cm_value;
                                        $tot_fob_value +=$fob_value;
                                        $tot_job_smv +=$job_smv;
                                        $j++;
                                        $i++;

                                }
                            }
                        }
                    }

                    ?>
                    <tr bgcolor="#FFFFCC">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2" align="right">Floor Total :</td>
                        <td align="right"><? echo number_format($flore_sew_out_qnty,2); ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($flore_sew_out_cm_value,2); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($flore_fob_value,2); ?></td>
                        <td align="right"><? //echo number_format($floor_job_smv,2); ?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?
                    $flore_sew_out_qnty=$flore_sew_out_cm_value=$flore_fob_value=0;
                }
            }
            ?>
            </table>
            </div>
            <table border="1" class="tbl_bottom"  width="1410" rules="all" id="" align="left">
                <tr>
                    <td width="30">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="80">Total:</td>
                    <td width="80"><? echo number_format($tot_sew_out_qnty,2); ?></td>
                    <td width="80">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="100"><? echo number_format($tot_sew_out_cm_value,2); ?></td>
                    <td width="80">&nbsp;</td>
                    <td width="100"><? echo number_format($tot_fob_value,2); ?></td>
                    <td width="50"><? //echo number_format($tot_job_smv,2); ?></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
        </div>
        <?
    }


        $html = ob_get_contents();
        ob_clean();
        foreach (glob("$user_name*.xls") as $filename) {
            @unlink($filename);
        }
        //---------end------------//
        $name=time();
        $filename="".$user_name."_".$name.".xls";
        $create_new_doc = fopen($filename, 'w');
        $is_created = fwrite($create_new_doc, $html);
        echo "$html####$excel_type####$filename";
    exit();
}

if($action=="report_generate_cm_value4_bk")// cm value 4 button 21-11-2023
{ 
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
    $company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name");
    $buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
    $location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"); 
    $floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"); 
    $line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"); 
    $prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');      
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1 order by sewing_line_serial");
    $lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");

    $garments_nature=str_replace("'","",$cbo_garments_nature);
    $company_id = str_replace("'","",$cbo_company_name);
    $type = str_replace("'","",$cbo_type);
    $location = str_replace("'","",$cbo_location);
    $cbo_floor = str_replace("'","",$cbo_floor);
    $cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
    $file_no = str_replace("'","",$txt_file_no);
    $internal_ref = str_replace("'","",$txt_internal_ref);
    $excel_type = str_replace("'","",$excel_type);
    $fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
    $to_date = change_date_format( str_replace("'","",trim($txt_date_to)) );
    $sql_cond="";
	$sql_cond .= ($company_id!=0) ? " and a.company_id=$company_id" : "";
	$sql_cond .= ($cbo_location!=0) ? " and a.location_id=$cbo_location" : "";
	$sql_cond .= ($cbo_floor!=0) ? " and a.floor_id in($cbo_floor)" : "";
	$sql_cond .= ($cbo_line!=0) ? " and a.id  in ($cbo_line)" : "";
	$sql_cond .=  ($fromDate && $to_date) ?" and c.pr_date between '".change_date_format($fromDate,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : ""; 
    $dateCond = ($fromDate && $to_date) ?" b.production_date between '".change_date_format($fromDate,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : ""; 
    $sql_cond2="";
	$sql_cond2 .= ($company_id!=0) ? " and b.company_id=$company_id" : "";
	$sql_cond2 .= ($cbo_location!=0) ? " and b.location=$cbo_location" : "";
	$sql_cond2 .= ($cbo_floor!=0) ? " and b.floor_id=$cbo_floor" : "";
	$sql_cond2 .= ($cbo_line!=0) ? " and b.sewing_line in ($cbo_line)" : "";
    $sql_cond2 .= ($cbo_buyer_name !=0) ? "and d.buyer_name = $cbo_buyer_name" : "";
    $date = DateTime::createFromFormat('d-m-Y', $fromDate);
    $formattedDate = $date->format('M-Y');
    // echo $formattedDate ;
    foreach($lineDataArr as $lRow)
    {
        $lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
        $lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
        $lastSlNo=$lRow[csf('sewing_line_serial')];
    }
    // echo "<pre>";
    // print_r($lineSerialArr);
   
   $SqlData = "SELECT a.id as line_id,a.company_id,a.location_id,a.floor_id, a.line_number,b.id, b.mst_id,c.pr_date,b.working_hour,b.active_machine
    FROM
      prod_resource_mst a ,
      prod_resource_dtls_mast b,
      prod_resource_dtls c ,
      LIB_PROD_FLOOR d
    WHERE 
    a.id=b.mst_id 
    AND c.mst_id = a.id
    and b.ID=c.MAST_DTL_ID
    and a.floor_id=d.id
    $sql_cond order by FLOOR_SERIAL_NO asc";
    //echo $SqlData ;die;
   $line_cond = array();

    $prod_wise_arr = array();
    $per_day = array();
    foreach(sql_select($SqlData) as $row)
    {
      $prod_wise_arr[$row['FLOOR_ID']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['WORKING_HOUR'] =$row['WORKING_HOUR']; 
      $prod_wise_arr[$row['FLOOR_ID']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['ACTIVE_MACHINE']=$row['ACTIVE_MACHINE']; 
      $prod_wise_arr[$row['FLOOR_ID']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['LINE_NUMBER']=$row['LINE_NUMBER']; 
    }
    // echo '<pre>';
    // print_r($prod_wise_arr);
    $SqlInfo = "SELECT a.production_qnty,
	b.company_id,
	b.item_number_id,
	b.production_date,
	b.production_type,
	b.floor_id,
	b.po_break_down_id,
	b.sewing_line,
	a.color_size_break_down_id,
	d.style_ref_no,
    d.job_no,
    d.buyer_name,
    d.gmts_item_id,
    b.prod_reso_allo
     FROM pro_garments_production_dtls a,
     pro_garments_production_mst b , 
     wo_po_color_size_breakdown c,
     wo_po_details_master d
    WHERE a.mst_id = b.id
	and a.color_size_break_down_id = c.id
    and c.job_id = d.id
    AND b.production_type in(5) 
	AND a.status_active=1
	AND a.is_deleted=0
	AND b.status_active=1 
	AND b.is_deleted=0
	AND c.status_active=1
	AND c.is_deleted=0
	AND d.status_active=1
	AND d.is_deleted=0
	AND $dateCond $sql_cond2 
     ";
	//echo $SqlInfo;die;
	
      $sql_ex_info = sql_select($SqlInfo);
      $prod_data_arr = array();  
     
	  foreach($sql_ex_info as $rowData)
	  {
        if($rowData['PROD_RESO_ALLO']==1)
        {
            $sewing_line_id=$prod_reso_arr[$rowData['SEWING_LINE']];
            $reso_line_ids.=$rowData['SEWING_LINE'].',';
        }
        else
        {
            $sewing_line_id=$rowData['SEWING_LINE'];
        }
         
        
        if($lineSerialArr[$sewing_line_id]=="")
        {
            $lastSlNo++;
            $slNo=$lastSlNo;
            $lineSerialArr[$sewing_line_id]=$slNo;
        }
        else $slNo=$lineSerialArr[$sewing_line_id];
         
		$prod_data_arr[$rowData['FLOOR_ID']][$slNo][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['PrQty'] += $rowData['PRODUCTION_QNTY']; 
		$prod_data_arr[$rowData['FLOOR_ID']][$slNo][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['STYLE_REF_NO'] = $rowData['STYLE_REF_NO'] ; 
        $prod_data_arr[$rowData['FLOOR_ID']][$slNo][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['BUYER_NAME'] = $rowData['BUYER_NAME'] ; 
        $prod_data_arr[$rowData['FLOOR_ID']][$slNo][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['JOB_NO'] = $rowData['JOB_NO'] ; 
        $prod_data_arr[$rowData['FLOOR_ID']][$slNo][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['ITEM_NUMBER_ID'] = $rowData['ITEM_NUMBER_ID'] ;
  

	 }
    // echo "<pre>";
    // print_r($prod_data_arr);die;
    $cm_query = "SELECT cm_cost,job_no,order_uom_id FROM WO_PRE_COST_DTLS ";
    $ex_query = sql_select($cm_query);
    foreach($ex_query as $response)
    {
        $tot_cm_cost_arr[$response['JOB_NO']]['CM_VAL'] = $response['CM_COST']; 
        $tot_cm_cost_arr[$response['JOB_NO']]['ORDER_UOM_ID'] = $response['ORDER_UOM_ID']; 
    }
    //    echo "<pre>";
    // print_r($tot_cm_cost_arr);die;
    $qu = "SELECT cost_per_minute, APPLYING_PERIOD_DATE
    FROM lib_standard_cm_entry
    WHERE TO_CHAR(APPLYING_PERIOD_DATE, 'Mon-YYYY') = '$formattedDate' AND company_id =$cbo_company_name";
    //echo $qu ;
    $ex = sql_select($qu);
    $last_production_date_cpm_data = 0;
    $last_production_date_currency_conv_data=0;
    foreach($ex as $key=>$val)
    {
        $last_production_date_cpm_data  = $val['COST_PER_MINUTE'];
    }
  //  echo $last_production_date_cpm_data  ;
    $qu_data = "SELECT CON_DATE, CONVERSION_RATE
    FROM CURRENCY_CONVERSION_RATE
   WHERE     TO_CHAR (CON_DATE, 'DDD-Mon-YYYY') <= '$fromDate'
         AND company_id = '1'
         AND status_active = 1
         AND is_deleted = 0
         AND CURRENCY=2
    ORDER BY CON_DATE DESC";
  //  echo $qu_data ;
    $ex_data = sql_select($qu_data);
    $last_production_date_currency_conv_data = $ex_data[0]['CONVERSION_RATE'];
    //echo  $last_production_date_currency_conv_data ;
   // echo $con_rate ;
    // foreach($ex_data as $key_data=>$val_data)
    // { 
    //     $last_production_date_currency_conv_data  = $val_data['CONVERSION_RATE'];
    // }
    $machin_cost_hour_result = ($last_production_date_cpm_data * 60) / $last_production_date_currency_conv_data ;
   
    $new_data_arr= array();
    foreach($prod_wise_arr as $floor_id => $floor_val)
    {
        foreach($floor_val as $sewing_line=>$sewing_val)
        {
            foreach($sewing_val as $production_date=>$v)
            {
                foreach($prod_data_arr[$floor_id][$slNo][$sewing_line][$production_date] as $po_id=>$po_val)
                {
                    foreach($po_val as $item_id=>$row)
                    {
                           
                            $new_data_arr[$floor_id][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['LINE_NUMBER'] = $v['LINE_NUMBER'] ;
                            $new_data_arr[$floor_id][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['WORKING_HOUR'] = $v['WORKING_HOUR'] ;
                            $new_data_arr[$floor_id][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ACTIVE_MACHINE'] = $v['ACTIVE_MACHINE'] ;
                            $new_data_arr[$floor_id][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PrQty']  += $row['PrQty'] ;
                            $new_data_arr[$floor_id][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['STYLE_REF_NO']  = $row['STYLE_REF_NO'] ;  
                            $new_data_arr[$floor_id][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['BUYER_NAME']  = $row['BUYER_NAME'] ; 
                            $new_data_arr[$floor_id][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['JOB_NO']  = $row['JOB_NO'] ; 
                            $new_data_arr[$floor_id][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['CM_VAL']  = $tot_cm_cost_arr[$row['JOB_NO']]['CM_VAL'] ; 
                            $new_data_arr[$floor_id][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ORDER_UOM_ID']  = $tot_cm_cost_arr[$row['JOB_NO']]['ORDER_UOM_ID'] ; 
                            $new_data_arr[$floor_id][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ITEM_NUMBER_ID']  = $row['ITEM_NUMBER_ID'] ;
                            $item_count[$floor_id][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ITEM_NUMBER_ID']++;
                           $ratio_total_item  = count($item_count);
                    }
                }
            }
        }
    }
   // echo $item_count_no;
    // print_r($ratio_total_item);die;
    $total_cm_val = array() ;
    $total_cm_val_sum = array();
    $rowspan_arr = array();
    $rowspan_arr2 = array();
    $rowspan_job_count = array();
    foreach($new_data_arr as $floor_id=>$floor_val)
    {
      foreach($floor_val as $sewing_line=>$line_val)
      {  
        foreach($line_val as $item_id=>$item_val)
         {
            foreach($item_val as $job=>$res)
            {

                $total_cm_val[$sewing_line] =  ($res['CM_VAL']/12) * $res['PrQty'];
                $total_cm_val_sum[$sewing_line][$res['JOB_NO']]  += $total_cm_val[$sewing_line] ;
                $line_cost_days_arr[$sewing_line][$res['JOB_NO']] +=  $res['ACTIVE_MACHINE'] * $res['WORKING_HOUR'] * $machin_cost_hour_result ;
                $rowspan_arr[$floor_id]++ ;
                $rowspan_arr2[$floor_id][$sewing_line]++ ;
                $rowspan_job_count[$sewing_line][$res['JOB_NO']]++;
           } 
         }
       
      }
    }
    // echo "<pre>";
    // print_r($total_cm_val_sum);die;
    ?>
     
     <div class="text">
        <span style="font-size:1.2rem;font-weight:bold"><? echo $company_library[$cbo_company_name] ?></span> <br>
        <span style="font-size:1rem;font-weight:bold">Daily Production & CM Status</span> <br>
        <span style="font-size:1rem;font-weight:bold">Production Date: <? echo $fromDate ?></span>
     </div>
    <div style="width:1320px;height:400px; overflow-y:scroll">
		<table width="1300" style="padding:10px;" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
        <thead>
            <tr>
                <th width="120">Floor</th>
                <th width="120">Line No</th>
                <th width="120">Buyer</th>
                <th width="120">Job No</th>
                <th width="120">Style No</th>
                <th width="120">Gmts Item</th>
                <th width="80">Total Sew. Pr.</th>
                <th width="80">Order CM/Pcs</th>
                <th width="80">Total CM</th>
                <th width="80">Line Wise Total CM</th>
                <th width="80">Machine Use</th>
                <th width="80">Working Hour </th>
                <th width="80">Machine Cost/Hr</th>
                <th width="80">Machine Cost/Day</th>
                <th width="80">Line Cost/Day</th>
                <th width="80">Day Status</th>
               
            </tr>
        </thead>
        <tbody>
           <?php
                  $b=0;
                
                  foreach($new_data_arr as $floor_id=>$floor_val)
                  { 
                    $f =0;
                    $j=0;
                        $total_sew_pr =$total_cm_cost=$line_wise_total_cm =$total_machine_use =$total_line_cost_day=$total_day_status=$total_working_hour=$x= 0;
                      
                    foreach($floor_val as $sewing_line=>$line_val)
                    {
                        $l=0;
                        foreach($line_val as $item_id=>$item_val)
                        { $j=0;$k=0;$d=0;
                           foreach($item_val as $job=>$data)
                           {
 
                            $total_cm_val  =  ($data['CM_VAL']/12) * $data['PrQty'];
                            
                               if($data['ORDER_UOM_ID']==1)
                               {
                                $order_cm_pcs = $data['CM_VAL']/12 ;
                               }else if($data['ORDER_UOM_ID']==58){
                                $order_cm_pcs = ($data['CM_VAL'])/($ratio_total_item * 12 );
                               }
                            
                           
                            $machin_cost_day = $data['ACTIVE_MACHINE'] * $data['WORKING_HOUR'] * $machin_cost_hour_result ;
                            $b++;
                            if ($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_3nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $b; ?>" >
                            <? if($f==0) { ?>
                            <td align="center" rowspan="<? echo $rowspan_arr[$floor_id] ?>" valign="middle"  style="color:blue;"><? echo $floor_library[$floor_id]  ?></td>
                            <? $f++ ; } ?>
                            <? if($l==0) { ?>
                            <td align="center"rowspan="<? echo $rowspan_arr2[$floor_id][$sewing_line] ?>" valign="middle"><? echo $line_library[$data['LINE_NUMBER']] ;  ?></td>
                            <? $l++ ; } ?>
                            <td align="left"><?  echo $buyer_arr[$data['BUYER_NAME']] ?></td>
                            <td align="left"><? echo $data['JOB_NO'] ?></td>
                            <td align="left"><? echo $data['STYLE_REF_NO'] ?></td>
                            <td align="left"><? echo $garments_item[$data['ITEM_NUMBER_ID']] ?></td>
                            <td align="right"><? echo $data['PrQty'] ?></td>
                            <td align="right" ><? echo number_format( $order_cm_pcs,2) ?></td>
                            <td align="right"valign="middle" align="center"><? echo number_format($total_cm_val,2); ?></td>
                            <? if($j==0) { ?>
                            <td align="right"rowspan="<? echo $rowspan_job_count[$sewing_line][$data['JOB_NO']] ?>" ><? echo number_format($total_cm_val_sum[$sewing_line][$data['JOB_NO']],2); ?></td>
                            <? $j++ ; } ?>
                            <td align="right" ><? echo $data['ACTIVE_MACHINE'] ?></td>
                            <td align="right"><? echo $data['WORKING_HOUR'] ?></td>
                            <td align="right"><? echo number_format($machin_cost_hour_result,2); ?></td>
                            <td align="right"><? echo number_format($machin_cost_day,2); ?></td>
                            <? if($k==0) { ?>
                            <td align="right"rowspan="<? echo $rowspan_job_count[$sewing_line][$data['JOB_NO']] ?>"><? echo number_format($line_cost_days_arr[$sewing_line][$data['JOB_NO']],2); ; ?></td>
                            <? $k++ ; } ?>
                            <? if($d==0) { ?>
                            <td align="right"rowspan="<? echo $rowspan_job_count[$sewing_line][$data['JOB_NO']] ?>"><? echo number_format($total_cm_val_sum[$sewing_line][$data['JOB_NO']] -  $line_cost_days_arr[$sewing_line][$data['JOB_NO']],2)?></td>
                            <? $d++ ; } ?>  
                        </tr>
                        
                      

                    <?
                        $total_sew_pr += $data['PrQty'] ;
                        $total_cm_cost += $total_cm_val ;
                        
                        $total_machine_use += $data['ACTIVE_MACHINE'] ;
                        $total_machine_cost_day += $machin_cost_day;
                       
                        $total_day_status  += $total_cm_val_sum[$sewing_line][$data['JOB_NO']] -  $line_cost_days_arr[$sewing_line][$data['JOB_NO']] ;
                        $total_working_hour += $data['WORKING_HOUR'] ;
                    }
                    $line_wise_total_cm += $total_cm_val_sum[$sewing_line][$data['JOB_NO']] ;
                    $total_line_cost_day += $line_cost_days_arr[$sewing_line][$data['JOB_NO']] ;
                    //echo "***".$line_cost_days_arr[$sewing_line][$data['JOB_NO']] ;
                  }
                  
                }
                ?>
                        <tr style="font-size:1.1rem !important;font-weight:bold;bold; background: #dddddd;" align="right">
                            
                            <td colspan="6" align="center"style="font-size:1rem">Floor Total</td>
                            <td><?  echo number_format($total_sew_pr,2) ?></td>
                            <td></td>
                            
                            <td><? echo number_format($total_cm_cost,2)  ?></td>

                            <td ><? echo number_format( $line_wise_total_cm,2); ?></td>
                            <td><? echo $total_machine_use ?></td>
                            <td><? echo number_format( $total_working_hour,2) ?></td>
                            <td></td>
                            <td><? echo number_format($total_machine_cost_day,2) ; ?></td>
                            <td><? echo number_format($total_line_cost_day,2) ?></td>
                            <td><?echo number_format($total_day_status,2) ?></td>
                        
                        </tr>
                      
                    <?
                        $day_sew_pr_total += $total_sew_pr ;
                        $day_cm_total += $total_cm_cost ;
                        $day_line_wise_total_cm += $line_wise_total_cm ;
                        $day_total_machine_use += $total_machine_use ;
                        $day_total_machin_cost_day += $total_machine_cost_day ;
                        $day_total_cost_day += $total_line_cost_day ;
                        $day_total_status += $total_day_status ;
                        $day_total_wr_hour +=  $total_working_hour ;
            }
                  ?>
                         <tr style="font-size:1.1rem !important;font-weight:bold;bold; background: #dddddd;" align="right">
                            
                            <td colspan="6" align="center" style="font-size:1rem">Day Total</td>
                            <td ><? echo number_format($day_sew_pr_total,2) ?></td>
                            <td></td>
                            <td><? echo number_format($day_cm_total,2)  ?></td>
                            <td ><? echo number_format($day_line_wise_total_cm,2) ?></td>
                            <td><? echo number_format($day_total_machine_use,2) ?></td>
                            <td><? echo number_format($day_total_wr_hour,2) ?></td>
                            <td></td>
                            <td><? echo number_format($day_total_machin_cost_day,2) ?></td>
                            <td><? echo number_format($day_total_cost_day,2)?></td>
                            <td><? echo number_format($day_total_status,2)?></td>
                        
                        </tr>
                  <?


           ?>
           
        </tbody>
   </table>
    </div>

        
    <?    
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_name*.xls") as $filename) {
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename="".$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');    
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$excel_type####$filename";   
    exit();
}

if ($action == "report_generate_cm_value4") // cm value 4 button
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    $company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $company_short_library = return_library_array("select id,company_short_name from lib_company", "id", "company_short_name");
    $buyer_short_library = return_library_array("select id,short_name from lib_buyer", "id", "short_name");
    $location_library = return_library_array("select id,location_name from lib_location", "id", "location_name");
    $floor_library = return_library_array("select id,floor_name from lib_prod_floor", "id", "floor_name");
    $line_library = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");
    $prod_reso_arr = return_library_array("select id, line_number from prod_resource_mst", 'id', 'line_number');
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1 order by sewing_line_serial");
    $lineArr = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");

    $garments_nature = str_replace("'", "", $cbo_garments_nature);
    $company_id = str_replace("'", "", $cbo_company_name);
    $type = str_replace("'", "", $cbo_type);
    $location = str_replace("'", "", $cbo_location);
    $cbo_floor = str_replace("'", "", $cbo_floor);
    $cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
    $file_no = str_replace("'", "", $txt_file_no);
    $internal_ref = str_replace("'", "", $txt_internal_ref);
    $excel_type = str_replace("'", "", $excel_type);
    $fromDate = change_date_format(str_replace("'", "", trim($txt_date_from)));
    $to_date = change_date_format(str_replace("'", "", trim($txt_date_to)));
    $sql_cond = "";
    $sql_cond .= ($company_id != 0) ? " and a.company_id=$company_id" : "";
    $sql_cond .= ($cbo_location != 0) ? " and a.location_id=$cbo_location" : "";
    $sql_cond .= ($cbo_floor != 0) ? " and a.floor_id in($cbo_floor)" : "";
    $sql_cond .= ($cbo_line != 0) ? " and a.id  in ($cbo_line)" : "";
    $sql_cond .= ($fromDate && $to_date) ? " and c.pr_date between '" . change_date_format($fromDate, 'dd-mm-yyyy', '-', 1) . "' and '" . change_date_format($to_date, 'dd-mm-yyyy', '-', 1) . "'" : "";
    $dateCond = ($fromDate && $to_date) ? " b.production_date between '" . change_date_format($fromDate, 'dd-mm-yyyy', '-', 1) . "' and '" . change_date_format($to_date, 'dd-mm-yyyy', '-', 1) . "'" : "";
    $sql_cond2 = "";
    $sql_cond2 .= ($company_id != 0) ? " and b.company_id=$company_id" : "";
    $sql_cond2 .= ($cbo_location != 0) ? " and b.location=$cbo_location" : "";
    $sql_cond2 .= ($cbo_floor != 0) ? " and b.floor_id=$cbo_floor" : "";
    $sql_cond2 .= ($cbo_line != 0) ? " and b.sewing_line in ($cbo_line)" : "";
    $sql_cond2 .= ($cbo_buyer_name != 0) ? "and d.buyer_name = $cbo_buyer_name" : "";
    $date = DateTime::createFromFormat('d-m-Y', $fromDate);
    $formattedDate = $date->format('M-Y');
    // echo $formattedDate ;
    foreach ($lineDataArr as $lRow) {
        $lineArr[$lRow[csf('id')]] = $lRow[csf('line_name')];
        $lineSerialArr[$lRow[csf('id')]] = $lRow[csf('sewing_line_serial')];
        $lastSlNo = $lRow[csf('sewing_line_serial')];
    }
    // echo "<pre>";
    // print_r($lineSerialArr);

    $SqlData = "SELECT a.id as line_id,e.SEWING_LINE_SERIAL,a.company_id,a.location_id,a.floor_id, a.line_number,b.id, b.mst_id,c.pr_date,b.working_hour,b.active_machine
    FROM
      prod_resource_mst a ,
      prod_resource_dtls_mast b,
      prod_resource_dtls c ,
      LIB_PROD_FLOOR d,
      lib_sewing_line e
    WHERE 
    a.id=b.mst_id 
    AND c.mst_id = a.id
    and b.ID=c.MAST_DTL_ID
    and a.floor_id=d.id
    and REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=e.id
    $sql_cond order by FLOOR_SERIAL_NO asc, e.SEWING_LINE_SERIAL asc";
    //echo $SqlData ;die;
    $line_cond = array();

    $prod_wise_arr = array();
    $per_day = array();
    foreach (sql_select($SqlData) as $row) {
        $prod_wise_arr[$row['FLOOR_ID']][$row['SEWING_LINE_SERIAL']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['WORKING_HOUR'] = $row['WORKING_HOUR'];
        $prod_wise_arr[$row['FLOOR_ID']][$row['SEWING_LINE_SERIAL']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['ACTIVE_MACHINE'] = $row['ACTIVE_MACHINE'];
        $prod_wise_arr[$row['FLOOR_ID']][$row['SEWING_LINE_SERIAL']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['LINE_NUMBER'] = $row['LINE_NUMBER'];
    }
    // echo '<pre>';
    // print_r($prod_wise_arr);
    $SqlInfo = "SELECT a.production_qnty,
	b.company_id,
	b.item_number_id,
	b.production_date,
	b.production_type,
	b.floor_id,
	b.po_break_down_id,
	b.sewing_line,
	a.color_size_break_down_id,
	d.style_ref_no,
    d.job_no,
    d.buyer_name,
    d.gmts_item_id,
    b.prod_reso_allo
     FROM pro_garments_production_dtls a,
     pro_garments_production_mst b , 
     wo_po_color_size_breakdown c,
     wo_po_details_master d
    WHERE a.mst_id = b.id
	and a.color_size_break_down_id = c.id
    and c.job_id = d.id
    AND b.production_type in(5) 
	AND a.status_active=1
	AND a.is_deleted=0
	AND b.status_active=1 
	AND b.is_deleted=0
	AND c.status_active=1
	AND c.is_deleted=0
	AND d.status_active=1
	AND d.is_deleted=0
	AND $dateCond $sql_cond2 
     ";
    //echo $SqlInfo;die;

    $sql_ex_info = sql_select($SqlInfo);
    $prod_data_arr = array();

    foreach ($sql_ex_info as $rowData) {
        if ($rowData['PROD_RESO_ALLO'] == 1) {
            $sewing_line_id = $prod_reso_arr[$rowData['SEWING_LINE']];
            $reso_line_ids .= $rowData['SEWING_LINE'] . ',';
        } else {
            $sewing_line_id = $rowData['SEWING_LINE'];
        }


        if ($lineSerialArr[$sewing_line_id] == "") {
            $lastSlNo++;
            $slNo = $lastSlNo;
            $lineSerialArr[$sewing_line_id] = $slNo;
        } else
            $slNo = $lineSerialArr[$sewing_line_id];

        $prod_data_arr[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['PrQty'] += $rowData['PRODUCTION_QNTY'];
        $prod_data_arr[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['STYLE_REF_NO'] = $rowData['STYLE_REF_NO'];
        $prod_data_arr[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['BUYER_NAME'] = $rowData['BUYER_NAME'];
        $prod_data_arr[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['JOB_NO'] = $rowData['JOB_NO'];
        $prod_data_arr[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['ITEM_NUMBER_ID'] = $rowData['ITEM_NUMBER_ID'];


    }
    // echo "<pre>";
    // print_r($prod_data_arr);die;
    $cm_query = "SELECT cm_cost,job_no,order_uom_id FROM WO_PRE_COST_DTLS ";
    $ex_query = sql_select($cm_query);
    foreach ($ex_query as $response) {
        $tot_cm_cost_arr[$response['JOB_NO']]['CM_VAL'] = $response['CM_COST'];
        $tot_cm_cost_arr[$response['JOB_NO']]['ORDER_UOM_ID'] = $response['ORDER_UOM_ID'];
    }
    //    echo "<pre>";
    // print_r($tot_cm_cost_arr);die;
    $qu = "SELECT cost_per_minute, APPLYING_PERIOD_DATE
    FROM lib_standard_cm_entry
    WHERE TO_CHAR(APPLYING_PERIOD_DATE, 'Mon-YYYY') = '$formattedDate' AND company_id =$cbo_company_name";
    //echo $qu ;
    $ex = sql_select($qu);
    $last_production_date_cpm_data = 0;
    $last_production_date_currency_conv_data = 0;
    foreach ($ex as $key => $val) {
        $last_production_date_cpm_data = $val['COST_PER_MINUTE'];
    }
    //  echo $last_production_date_cpm_data  ;
    $dateString = $fromDate;

    // Create a DateTime object from the original date string
    $originalDate = DateTime::createFromFormat('d -m -Y', $dateString);

    // Format the date in the desired format
    $formattedDate = $originalDate->format('d-M-Y');

    //echo $formattedDate;

    //echo 'last'. $last_production_date_cpm_data  ;die;
    $qu_data = "SELECT CON_DATE, CONVERSION_RATE
    FROM CURRENCY_CONVERSION_RATE
   WHERE     CON_DATE <= TO_DATE('$formattedDate', 'DD/MM/YY') 
         AND company_id = '1'
         AND status_active = 1
         AND is_deleted = 0
         AND CURRENCY=2
    ORDER BY CON_DATE DESC";
    // echo $qu_data ;
    $ex_data = sql_select($qu_data);
    $last_production_date_currency_conv_data = $ex_data[0]['CONVERSION_RATE'];
    //echo  $last_production_date_currency_conv_data ;
    // echo $con_rate ;
    // foreach($ex_data as $key_data=>$val_data)
    // { 
    //     $last_production_date_currency_conv_data  = $val_data['CONVERSION_RATE'];
    // }
    $machin_cost_hour_result = ($last_production_date_cpm_data * 60) / $last_production_date_currency_conv_data;

    $new_data_arr = array();
    foreach ($prod_wise_arr as $floor_id => $floor_val) {
        foreach($floor_val as $line_serial => $line_val){
            foreach ($line_val as $sewing_line => $sewing_val) {
                foreach ($sewing_val as $production_date => $v) {
                    foreach ($prod_data_arr[$floor_id][$sewing_line][$production_date] as $po_id => $po_val) {
                        foreach ($po_val as $item_id => $row) {
    
                            $new_data_arr[$floor_id][$line_serial][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['LINE_NUMBER'] = $v['LINE_NUMBER'];
                            $new_data_arr[$floor_id][$line_serial][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['WORKING_HOUR'] = $v['WORKING_HOUR'];
                            $new_data_arr[$floor_id][$line_serial][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ACTIVE_MACHINE'] = $v['ACTIVE_MACHINE'];
                            $new_data_arr[$floor_id][$line_serial][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['PrQty'] += $row['PrQty'];
                            $new_data_arr[$floor_id][$line_serial][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
                            $new_data_arr[$floor_id][$line_serial][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['BUYER_NAME'] = $row['BUYER_NAME'];
                            $new_data_arr[$floor_id][$line_serial][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['JOB_NO'] = $row['JOB_NO'];
                            $new_data_arr[$floor_id][$line_serial][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['CM_VAL'] = $tot_cm_cost_arr[$row['JOB_NO']]['CM_VAL'];
                            $new_data_arr[$floor_id][$line_serial][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ORDER_UOM_ID'] = $tot_cm_cost_arr[$row['JOB_NO']]['ORDER_UOM_ID'];
                            $new_data_arr[$floor_id][$line_serial][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ITEM_NUMBER_ID'] = $row['ITEM_NUMBER_ID'];
                            $item_count[$floor_id][$line_serial][$sewing_line][$row['JOB_NO']][$row['ITEM_NUMBER_ID']]['ITEM_NUMBER_ID']++;
                            $ratio_total_item = count($item_count);
                        }
                    }
                }
            }
            
        }
    }
    // echo $item_count_no;
    // echo "<pre>";print_r($new_data_arr);die;
    $total_cm_val = array();
    $total_cm_val_sum = array();
    $rowspan_arr = array();
    $rowspan_arr2 = array();
    $rowspan_job_count = array();
    $line_chk_arr = array();
    foreach ($new_data_arr as $floor_id => $floor_val) 
    {
        foreach($floor_val as $line_serial => $serial_val)
        {
            foreach ($serial_val as $sewing_line => $line_val) 
            {
                foreach ($line_val as $item_id => $item_val) 
                {
                    foreach ($item_val as $job => $res) 
                    {

                        $total_cm_val[$sewing_line] = ($res['CM_VAL'] / 12) * $res['PrQty'];
                        $total_cm_val_sum[$sewing_line] += $total_cm_val[$sewing_line];
                        if($line_chk_arr[$sewing_line]=="")
                        {
                        
                            $line_cost_days_arr[$sewing_line] += $res['ACTIVE_MACHINE'] * $res['WORKING_HOUR'] * $machin_cost_hour_result;
                            $line_chk_arr[$sewing_line] = $sewing_line;
                        }
                        $rowspan_arr[$floor_id]++;
                        $rowspan_arr2[$floor_id][$sewing_line]++;
                        $rowspan_job_count[$sewing_line]++;
                    }
                }

            }
        }
        
    }
    // echo "<pre>";
    // print_r($total_cm_val_sum);die;
    ?>

    <div class="text">
        <span style="font-size:1.2rem;font-weight:bold">
            <? echo $company_library[$cbo_company_name] ?>
        </span> <br>
        <span style="font-size:1rem;font-weight:bold">Daily Production & CM Status</span> <br>
        <span style="font-size:1rem;font-weight:bold">Production Date:
            <? echo $fromDate ?>
        </span>
    </div>
    <div style="width:1320px;height:400px; overflow-y:scroll">
        <table width="1300" style="padding:10px;" cellspacing="0" border="1" class="rpt_table" rules="all"
            id="table_header_1">
            <thead>
                <tr>
                    <th width="120">Floor</th>
                    <th width="120">Line No</th>
                    <th width="120">Buyer</th>
                    <th width="120">Job No</th>
                    <th width="120">Style No</th>
                    <th width="120">Gmts Item</th>
                    <th width="80">Total Sew. Pr.</th>
                    <th width="80">Order CM/Pcs</th>
                    <th width="80">Total CM</th>
                    <th width="80">Line Wise Total CM</th>
                    <th width="80">Machine Use</th>
                    <th width="80">Working Hour </th>
                    <th width="80">Machine Cost/Hr</th>
                    <th width="80">Machine Cost/Day</th>
                    <th width="80">Line Cost/Day</th>
                    <th width="80" title="Line Wise Total CM - Line Cost/Day">Day Status</th>

                </tr>
            </thead>
            <tbody>
                <?php
                    $b = 0;

                    foreach ($new_data_arr as $floor_id => $floor_val) {
                        
                        $f = 0;
                        $j = 0;
                        $total_sew_pr = $total_cm_cost = $line_wise_total_cm = $total_machine_use = $total_line_cost_day = $total_day_status = $total_working_hour = $x = $total_machine_cost_day = 0;
                        foreach($floor_val as $line_serial => $serial_val)
                        {
                            foreach ($serial_val as $sewing_line => $line_val) {
                                $l = 0;
                                $j = 0;
                                $k = 0;
                                $d = 0;
                                foreach ($line_val as $item_id => $item_val) {
                                
                                    foreach ($item_val as $job => $data) {

                                        $total_cm_val = ($data['CM_VAL'] / 12) * $data['PrQty'];

                                        if ($data['ORDER_UOM_ID'] == 1) {
                                            $order_cm_pcs = $data['CM_VAL'] / 12;
                                        } else if ($data['ORDER_UOM_ID'] == 58) {
                                            $order_cm_pcs = ($data['CM_VAL']) / ($ratio_total_item * 12);
                                        }


                                        // $machin_cost_day = $data['ACTIVE_MACHINE'] * $data['WORKING_HOUR'] * $machin_cost_hour_result;
                                        $b++;
                                        if ($b % 2 == 0)
                                            $bgcolor = "#E9F3FF";
                                        else
                                            $bgcolor = "#FFFFFF";
                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_3nd<? echo $b; ?>','<? echo $bgcolor; ?>')"
                                            id="tr_3nd<? echo $b; ?>">
                                            <? if ($f == 0) { ?>
                                            <td align="center" rowspan="<? echo $rowspan_arr[$floor_id] ?>" valign="middle" style="color:blue;">
                                                <? echo $floor_library[$floor_id] ?>
                                            </td>
                                            <? $f++;
                                                                } ?>
                                            <? if ($l == 0) { ?>
                                            <td align="center" rowspan="<? echo $rowspan_arr2[$floor_id][$sewing_line] ?>" valign="middle">
                                                <? echo $line_library[$data['LINE_NUMBER']]; ?>
                                            </td>
                                            <? $l++;
                                                                } ?>
                                            <td align="left">
                                                <? echo $buyer_arr[$data['BUYER_NAME']] ?>
                                            </td>
                                            <td align="left">
                                                <? echo $data['JOB_NO'] ?>
                                            </td>
                                            <td align="left">
                                                <? echo $data['STYLE_REF_NO'] ?>
                                            </td>
                                            <td align="left">
                                                <? echo $garments_item[$data['ITEM_NUMBER_ID']] ?>
                                            </td>
                                            <td align="right">
                                                <? echo $data['PrQty'] ?>
                                            </td>
                                            <td align="right">
                                                <? echo number_format($order_cm_pcs, 2) ?>
                                            </td>
                                            <td align="right" valign="middle" align="center">
                                                <? echo number_format($total_cm_val, 2); ?>
                                            </td>
                                            <? if ($j == 0) { ?>
                                            <td align="right" rowspan="<? echo $rowspan_job_count[$sewing_line]; ?>">
                                                <? echo number_format($total_cm_val_sum[$sewing_line], 2); ?>
                                            </td>
                                        
                                            <td align="right" rowspan="<? echo $rowspan_job_count[$sewing_line]; ?>">
                                                <? echo $data['ACTIVE_MACHINE'];$total_machine_use += $data['ACTIVE_MACHINE']; ?>
                                            </td>
                                            <td align="right" rowspan="<? echo $rowspan_job_count[$sewing_line]; ?>">
                                                <? echo $data['WORKING_HOUR'];$total_working_hour += $data['WORKING_HOUR']; ?>
                                            </td>
                                            <td align="right" rowspan="<? echo $rowspan_job_count[$sewing_line]; ?>">
                                                <? echo number_format($machin_cost_hour_result, 2); ?>
                                            </td>
                                            <td align="right" rowspan="<? echo $rowspan_job_count[$sewing_line]; ?>">
                                                <? $machin_cost_day = $line_cost_days_arr[$sewing_line];echo number_format($machin_cost_day, 2);  $total_machine_cost_day += $machin_cost_day;?>
                                            </td>
                                            <? $j++; } ?>
                                            <? if ($k == 0) { ?>
                                            <td align="right" rowspan="<? echo $rowspan_job_count[$sewing_line]; ?>">
                                                <? echo number_format($line_cost_days_arr[$sewing_line], 2); $total_line_cost_day += $line_cost_days_arr[$sewing_line];
                                                                        ; ?>
                                            </td>
                                            <? $k++;
                                                                } ?>
                                            <? if ($d == 0) { ?>
                                            <td align="right" rowspan="<? echo $rowspan_job_count[$sewing_line]; ?>">
                                                <? echo number_format($total_cm_val_sum[$sewing_line] - $line_cost_days_arr[$sewing_line], 2) ?>
                                            </td>
                                            <? $d++; 
                                            $total_day_status += $total_cm_val_sum[$sewing_line] - $line_cost_days_arr[$sewing_line];
                                            $line_wise_total_cm += $total_cm_val_sum[$sewing_line];
                                                                } ?>
                                        </tr>



                                <?
                                        $total_sew_pr += $data['PrQty'];
                                        $total_cm_cost += $total_cm_val;

                                        
                                    

                                    
                                        
                                    }
                                    
                                    }
                                    //echo "***".$line_cost_days_arr[$sewing_line][$data['JOB_NO']] ;
                                }

                        }
                        ?>
                <tr style="font-size:1.1rem !important;font-weight:bold;bold; background: #dddddd;" align="right">

                    <td colspan="6" align="center" style="font-size:1rem">Floor Total</td>
                    <td>
                        <? echo number_format($total_sew_pr, 2) ?>
                    </td>
                    <td></td>

                    <td>
                        <? echo number_format($total_cm_cost, 2) ?>
                    </td>

                    <td>
                        <? echo number_format($line_wise_total_cm, 2); ?>
                    </td>
                    <td>
                        <? echo $total_machine_use ?>
                    </td>
                    <td>
                        <? echo number_format($total_working_hour, 2) ?>
                    </td>
                    <td></td>
                    <td>
                        <? echo number_format($total_machine_cost_day, 2); ?>
                    </td>
                    <td>
                        <? echo number_format($total_line_cost_day, 2) ?>
                    </td>
                    <td>
                        <? echo number_format($total_day_status, 2) ?>
                    </td>

                </tr>

                <?
                        $day_sew_pr_total += $total_sew_pr;
                        $day_cm_total += $total_cm_cost;
                        $day_line_wise_total_cm += $line_wise_total_cm;
                        $day_total_machine_use += $total_machine_use;
                        $day_total_machin_cost_day += $total_machine_cost_day;
                        $day_total_cost_day += $total_line_cost_day;
                        $day_total_status += $total_day_status;
                        $day_total_wr_hour += $total_working_hour;
                    }
                    ?>
                <tr style="font-size:1.1rem !important;font-weight:bold;bold; background: #dddddd;" align="right">

                    <td colspan="6" align="center" style="font-size:1rem">Day Total</td>
                    <td>
                        <? echo number_format($day_sew_pr_total, 2) ?>
                    </td>
                    <td></td>
                    <td>
                        <? echo number_format($day_cm_total, 2) ?>
                    </td>
                    <td>
                        <? echo number_format($day_line_wise_total_cm, 2) ?>
                    </td>
                    <td>
                        <? echo number_format($day_total_machine_use, 2) ?>
                    </td>
                    <td>
                        <? echo number_format($day_total_wr_hour, 2) ?>
                    </td>
                    <td></td>
                    <td>
                        <? echo number_format($day_total_machin_cost_day, 2) ?>
                    </td>
                    <td>
                        <? echo number_format($day_total_cost_day, 2) ?>
                    </td>
                    <td>
                        <? echo number_format($day_total_status, 2) ?>
                    </td>

                </tr>
                <?


                    ?>

            </tbody>
        </table>
    </div>


    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_name*.xls") as $filename) {
        @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = "" . $user_name . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$excel_type####$filename";
    exit();
}


if ($action == "report_generate_cm_value5") // cm value 5 button
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    $company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $company_short_library = return_library_array("select id,company_short_name from lib_company", "id", "company_short_name");
    $buyer_short_library = return_library_array("select id,short_name from lib_buyer", "id", "short_name");
    $location_library = return_library_array("select id,location_name from lib_location", "id", "location_name");
    $floor_library = return_library_array("select id,floor_name from lib_prod_floor", "id", "floor_name");
    $line_library = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");
    $prod_reso_arr = return_library_array("select id, line_number from prod_resource_mst", 'id', 'line_number');
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1 order by sewing_line_serial");
    $lineArr = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");

    $garments_nature = str_replace("'", "", $cbo_garments_nature);
    $company_id = str_replace("'", "", $cbo_company_name);
    $type = str_replace("'", "", $cbo_type);
    $location = str_replace("'", "", $cbo_location);
    $cbo_floor = str_replace("'", "", $cbo_floor);
    $cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
    $file_no = str_replace("'", "", $txt_file_no);
    $internal_ref = str_replace("'", "", $txt_internal_ref);
    $excel_type = str_replace("'", "", $excel_type);
    $fromDate = change_date_format(str_replace("'", "", trim($txt_date_from)));
    $to_date = change_date_format(str_replace("'", "", trim($txt_date_to)));
    $sql_cond = "";
    $sql_cond .= ($company_id != 0) ? " and a.company_id=$company_id" : "";
    $sql_cond .= ($cbo_location != 0) ? " and a.location_id=$cbo_location" : "";
    $sql_cond .= ($cbo_floor != 0) ? " and a.floor_id in($cbo_floor)" : "";
    $sql_cond .= ($cbo_line != 0) ? " and a.id  in ($cbo_line)" : "";
    $sql_cond .= ($fromDate && $to_date) ? " and c.pr_date between '" . change_date_format($fromDate, 'dd-mm-yyyy', '-', 1) . "' and '" . change_date_format($to_date, 'dd-mm-yyyy', '-', 1) . "'" : "";
    $dateCond = ($fromDate && $to_date) ? " b.production_date between '" . change_date_format($fromDate, 'dd-mm-yyyy', '-', 1) . "' and '" . change_date_format($to_date, 'dd-mm-yyyy', '-', 1) . "'" : "";
    $sql_cond2 = "";
    $sql_cond2 .= ($company_id != 0) ? " and b.company_id=$company_id" : "";
    $sql_cond2 .= ($cbo_location != 0) ? " and b.location=$cbo_location" : "";
    $sql_cond2 .= ($cbo_floor != 0) ? " and b.floor_id=$cbo_floor" : "";
    $sql_cond2 .= ($cbo_line != 0) ? " and b.sewing_line in ($cbo_line)" : "";
    $sql_cond2 .= ($cbo_buyer_name != 0) ? "and d.buyer_name = $cbo_buyer_name" : "";
    $date = DateTime::createFromFormat('d-m-Y', $fromDate);
    $formattedDate = $date->format('M-Y');
    // echo $formattedDate ;
    foreach ($lineDataArr as $lRow) {
        $lineArr[$lRow[csf('id')]] = $lRow[csf('line_name')];
        $lineSerialArr[$lRow[csf('id')]] = $lRow[csf('sewing_line_serial')];
        $lastSlNo = $lRow[csf('sewing_line_serial')];
    }
    // echo "<pre>";
    // print_r($lineSerialArr);
    $dates = array();
    $from = strtotime($fromDate);
    $to = strtotime($to_date);
    $stepVal = '+1 day';

    $format = "j-M-Y";
    while ($from <= $to) {
        $dates[] = date($format, $from);
        $width = count($dates);
        $from = strtotime($stepVal, $from);
    }
    $SqlData = "SELECT a.id as line_id,e.SEWING_LINE_SERIAL,a.company_id,a.location_id,a.floor_id, a.line_number,b.id, b.mst_id,c.pr_date,b.working_hour,b.active_machine
    FROM
      prod_resource_mst a ,
      prod_resource_dtls_mast b,
      prod_resource_dtls c ,
      LIB_PROD_FLOOR d,
      lib_sewing_line e
    WHERE 
    a.id=b.mst_id 
    AND c.mst_id = a.id
    and b.ID=c.MAST_DTL_ID
    and a.floor_id=d.id
    and REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=e.id
    $sql_cond order by FLOOR_SERIAL_NO asc, e.SEWING_LINE_SERIAL asc";
    //echo $SqlData ;die;
    $line_cond = array();

    $prod_wise_arr = array();
    $per_day = array();
    foreach (sql_select($SqlData) as $row) {
        $prod_wise_arr[$row['FLOOR_ID']][$row['SEWING_LINE_SERIAL']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['WORKING_HOUR'] = $row['WORKING_HOUR'];
        $prod_wise_arr[$row['FLOOR_ID']][$row['SEWING_LINE_SERIAL']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['ACTIVE_MACHINE'] = $row['ACTIVE_MACHINE'];
        $prod_wise_arr[$row['FLOOR_ID']][$row['SEWING_LINE_SERIAL']][$row['LINE_ID']][strtotime($row['PR_DATE'])]['LINE_NUMBER'] = $row['LINE_NUMBER'];
    }
    // echo '<pre>'; print_r($prod_wise_arr);die;
    $SqlInfo = "SELECT a.production_qnty,
	b.company_id,
	b.item_number_id,
	b.production_date,
	b.production_type,
	b.floor_id,
	b.po_break_down_id,
	b.sewing_line,
	a.color_size_break_down_id,
	d.style_ref_no,
    d.job_no,
    d.buyer_name,
    d.gmts_item_id,
    b.prod_reso_allo
     FROM pro_garments_production_dtls a,
     pro_garments_production_mst b , 
     wo_po_color_size_breakdown c,
     wo_po_details_master d
    WHERE a.mst_id = b.id
	and a.color_size_break_down_id = c.id
    and c.job_id = d.id
    AND b.production_type in(5) 
	AND a.status_active=1
	AND a.is_deleted=0
	AND b.status_active=1 
	AND b.is_deleted=0
	AND c.status_active=1
	AND c.is_deleted=0
	AND d.status_active=1
	AND d.is_deleted=0
	AND $dateCond $sql_cond2 
     ";
    //echo $SqlInfo;die;

    $sql_ex_info = sql_select($SqlInfo);
    $prod_data_arr = array();

    foreach ($sql_ex_info as $rowData) 
    {
        if ($rowData['PROD_RESO_ALLO'] == 1) {
            $sewing_line_id = $prod_reso_arr[$rowData['SEWING_LINE']];
            $reso_line_ids .= $rowData['SEWING_LINE'] . ',';
        } else {
            $sewing_line_id = $rowData['SEWING_LINE'];
        }


        if ($lineSerialArr[$sewing_line_id] == "") {
            $lastSlNo++;
            $slNo = $lastSlNo;
            $lineSerialArr[$sewing_line_id] = $slNo;
        } else
            $slNo = $lineSerialArr[$sewing_line_id];

        $prod_data_arr[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['PrQty'] += $rowData['PRODUCTION_QNTY'];
        $prod_data_arr[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['STYLE_REF_NO'] = $rowData['STYLE_REF_NO'];
        $prod_data_arr[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['BUYER_NAME'] = $rowData['BUYER_NAME'];
        $prod_data_arr[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['JOB_NO'] = $rowData['JOB_NO'];
        $prod_data_arr[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['ITEM_NUMBER_ID'] = $rowData['ITEM_NUMBER_ID'];
        $prod_data_arr[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])][$rowData['PO_BREAK_DOWN_ID']][$rowData['ITEM_NUMBER_ID']]['PRODUCTION_DATE'] = $rowData['PRODUCTION_DATE'];

        // $prod_wise_arr[$rowData['FLOOR_ID']][$rowData['SEWING_LINE']][strtotime($rowData['PRODUCTION_DATE'])]['JOB_NO'] = $rowData['JOB_NO'];



    }
    // echo "<pre>"; print_r($prod_data_arr);die;
    $cm_query = "SELECT cm_cost,job_no FROM WO_PRE_COST_DTLS ";
    $ex_query = sql_select($cm_query);
    foreach ($ex_query as $response) {
        $tot_cm_cost_arr[$response['JOB_NO']]['CM_VAL'] = $response['CM_COST'];
    }
    $qu = "SELECT cost_per_minute, APPLYING_PERIOD_DATE
    FROM lib_standard_cm_entry
    WHERE TO_CHAR(APPLYING_PERIOD_DATE, 'Mon-YYYY') = '$formattedDate' AND company_id =$cbo_company_name";
    //echo $qu ;
    $ex = sql_select($qu);
    // $last_production_date_cpm_data = 0;
    $last_production_date_currency_conv_data = 0;
    foreach ($ex as $key => $val) {
        $last_production_date_cpm_data = $val['COST_PER_MINUTE'];
    }
    // $search_date = $fromDate;
    $dateString = $fromDate;

    // Create a DateTime object from the original date string
    $originalDate = DateTime::createFromFormat('d -m -Y', $dateString);

    // Format the date in the desired format
    $formattedDate = $originalDate->format('d-M-Y');

    //echo $formattedDate;

    //echo 'last'. $last_production_date_cpm_data  ;die;
    $qu_data = "SELECT CON_DATE, CONVERSION_RATE
    FROM CURRENCY_CONVERSION_RATE
   WHERE     CON_DATE <= TO_DATE('$formattedDate', 'DD/MM/YY') 
         AND company_id = '1'
         AND status_active = 1
         AND is_deleted = 0
         AND CURRENCY=2
    ORDER BY CON_DATE DESC";
    // echo $qu_data ;
    $ex_data = sql_select($qu_data);
    $last_production_date_currency_conv_data = $ex_data[0]['CONVERSION_RATE'];
    // echo  '&&'.  $last_production_date_currency_conv_data ;die;
    // echo $con_rate ;
    // foreach($ex_data as $key_data=>$val_data)
    // { 
    //     $last_production_date_currency_conv_data  = $val_data['CONVERSION_RATE'];
    // }
    $machin_cost_hour_result = ($last_production_date_cpm_data * 60) / $last_production_date_currency_conv_data;
    // echo 'machie' . $machin_cost_hour_result;
    //die;
    $new_data_arr = array();
    foreach ($prod_wise_arr as $floor_id => $floor_val) 
    {
        foreach($floor_val as $line_serial => $line_val)
        {
            foreach ($line_val as $sewing_line => $sewing_val) 
            {
                foreach ($sewing_val as $production_date => $v) 
                {
                    // $new = $prod_data_arr[$floor_id][$sewing_line][$production_date];

                    foreach ($prod_data_arr[$floor_id][$sewing_line][$production_date] as $po_id => $po_val) 
                    {
                        foreach ($po_val as $item_id => $row) 
                        {

                            // echo $sewing_line."*".$production_date."*".$row['JOB_NO']."*".$row['PrQty']."<br>";
                            $new_data_arr[$production_date][$floor_id][$line_serial][$sewing_line][$row['JOB_NO']]['LINE_NUMBER'] = $v['LINE_NUMBER'];
                            $new_data_arr[$production_date][$floor_id][$line_serial][$sewing_line][$row['JOB_NO']]['WORKING_HOUR'] = $v['WORKING_HOUR'];
                            $new_data_arr[$production_date][$floor_id][$line_serial][$sewing_line][$row['JOB_NO']]['ACTIVE_MACHINE'] = $v['ACTIVE_MACHINE'];
                            $new_data_arr[$production_date][$floor_id][$line_serial][$sewing_line][$row['JOB_NO']]['PrQty'] += $row['PrQty'];
                            $new_data_arr[$production_date][$floor_id][$line_serial][$sewing_line][$row['JOB_NO']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
                            $new_data_arr[$production_date][$floor_id][$line_serial][$sewing_line][$row['JOB_NO']]['BUYER_NAME'] = $row['BUYER_NAME'];
                            $new_data_arr[$production_date][$floor_id][$line_serial][$sewing_line][$row['JOB_NO']]['JOB_NO'] = $row['JOB_NO'];
                            $new_data_arr[$production_date][$floor_id][$line_serial][$sewing_line][$row['JOB_NO']]['CM_VAL'] = $tot_cm_cost_arr[$row['JOB_NO']]['CM_VAL'];
                            $new_data_arr[$production_date][$floor_id][$line_serial][$sewing_line][$row['JOB_NO']]['ITEM_NUMBER_ID'] = $row['ITEM_NUMBER_ID'];
                            $new_data_arr[$production_date][$floor_id][$line_serial][$sewing_line][$row['JOB_NO']]['PRODUCTION_DATE'] = $row['PRODUCTION_DATE'];
                            $test[$production_date]['LF: '.$floor_id]['SL: '.$line_serial][$sewing_line][$row['JOB_NO']]['PRODUCTION_DATE'] = $row['PRODUCTION_DATE'];

                        }
                    }
                }
            }
        }
    }

    // echo "<pre>"; print_r($test);die;

    $month_data_arr = array();
    foreach ($new_data_arr as $prdate => $date_val) 
    {
        foreach ($date_val as $floor_id => $floor_val) 
        {
            foreach($floor_val as $line_serial => $serial_val)
            {
                foreach ($serial_val as $line_id => $line_val) 
                {
                    foreach ($line_val as $job_key => $response) 
                    {
                        $month_data_arr[$floor_id][$line_serial][$line_id][strtotime($response['PRODUCTION_DATE'])]['PR_QTY'] += $response['PrQty'];
                        $month_data_arr[$floor_id][$line_serial][$line_id]['LINE_NUMBER'] = $response['LINE_NUMBER'];
                        $month_data_arr[$floor_id][$line_serial][$line_id][strtotime($response['PRODUCTION_DATE'])]['CM_VAL'] += $tot_cm_cost_arr[$response['JOB_NO']]['CM_VAL'];
                        $month_data_arr[$floor_id][$line_serial][$line_id][strtotime($response['PRODUCTION_DATE'])]['PRODUCTION_DATE'] = $response['PRODUCTION_DATE'];
                        $month_data_arr[$floor_id][$line_serial][$line_id][strtotime($response['PRODUCTION_DATE'])]['WORKING_HOUR'] += $response['WORKING_HOUR'];
                        $month_data_arr[$floor_id][$line_serial][$line_id][strtotime($response['PRODUCTION_DATE'])]['ACTIVE_MACHINE'] += $response['ACTIVE_MACHINE'];
                    }
                }
            }
        }
    }
    //    echo "<pre>"; print_r($month_data_arr);die;
    $total_cm_val_sum_day = array();
    $line_chk_arr = array();
    foreach ($new_data_arr as $prdate => $date_val) 
    {
        foreach ($date_val as $floor_id => $floor_val) 
        {
            foreach($floor_val as $line_serial => $serial_val)
            {
                foreach ($serial_val as $line_id => $line_val) 
                {
                    foreach ($line_val as $job_key => $res) 
                    {
                        $total_cm_val[$line_id] = ($res['CM_VAL'] / 12) * $res['PrQty'];
                        $total_cm_val_sum[$line_id][$prdate] += ($res['CM_VAL'] / 12) * $res['PrQty'];
                        if($line_chk_arr[$prdate][$line_id]=="")
                        {
                            $line_cost_days_arr[$line_id][$prdate] += ($res['ACTIVE_MACHINE'] * $res['WORKING_HOUR'] * $machin_cost_hour_result);
                            $line_chk_arr[$prdate][$line_id] = $line_id;
                        }
                        $total_cm_val_sum_day[$line_id][$prdate][$total_cm_val[$line_id]]['PrQty'] += $total_cm_val[$line_id];

                    }
                }
            }
        }
    }

    // echo "<pre>"; print_r($line_cost_days_arr);die;
    // die;
    $tbl_width = 200 + (count($dates) * 40);
    // echo $tbl_width ;
    ?>
    <div style="height:400px; overflow-y:scroll" width="<? $tbl_width + 30 ?>">
        <table width="<? echo $tbl_width ?>" style="height:400px; overflow-y:scroll" style="padding:10px;" cellspacing="0"
            border="1" class="rpt_table" rules="all" id="table_header_1">
            <thead>
                <th width="80">Floor</th>
                <th width="80">Line no</th>
                <th width="120">Monthly Status</th>
                <?
                    foreach ($dates as $key => $val) { ?>
                <th width="60">
                    <? echo date("d-M", strtotime($val)); ?>
                </th>
                <? }
                    ?>

                <th width="80">Line Wise Total</th>
            </thead>
            <tbody>
                <?
                    $final_floor_totalData = array();
                    foreach ($month_data_arr as $floor_id => $floor_val) 
                    {
                        $floor_total = array();
                        $floor_line_total = array();
                        $floor_line_monthly_Total = array();
                        $final_floor_total_qty = 0;
                        $final_cm_earn = 0;
                        $final_total_cm_status = 0;
                        ksort($floor_val);
                        foreach($floor_val as $line_serial => $serial_val)
                        {
                            foreach ($serial_val as $sewing_line => $data) 
                            {


                                ?>
                                <tr>
                                    <td rowspan="3" align="center" valign="middle">
                                        <? echo $floor_library[$floor_id] ?>
                                    </td>
                                    <td rowspan="3" align="center" valign="middle" title="<? echo $sewing_line; ?>">
                                        <? echo $line_library[$data['LINE_NUMBER']]; ?>
                                    </td>
                                    <td>Line Wise Monthly Production Qty</td>
                                    <?
                                                foreach ($dates as $key => $val) {
                                                    $str = strtotime($val);
                                                    $floor_total[$str] += $data[$str]['PR_QTY'];
                                                    //$floor_line_total[$str] += $data[$str]['PR_QTY'];
                                                    $final_floor_totalData[$str] += $data[$str]['PR_QTY'];
                                                    ?>
                                    <td width="60" align="right">
                                        <? echo number_format($data[$str]['PR_QTY'], 2) ?>
                                    </td>
                                    <?
                                                    $total_pr_qty += $data[$str]['PR_QTY'];
                                                }
                                                ?>

                                    <td align="right">
                                        <? echo $total_pr_qty ?>
                                    </td>
                                </tr>
                                <tr>


                                    <td>Line Wise Monthly CM Earn</td>
                                    <?
                                                foreach ($dates as $key => $val) {
                                                    $str = strtotime($val);
                                                    $floor_line_total[$str] += $total_cm_val_sum[$sewing_line][$str];
                                                    $floor_total_cm_earn[$str] += $total_cm_val_sum[$sewing_line][$str];

                                                    ?>
                                    <td width="60" align="right">
                                        <? echo number_format($total_cm_val_sum[$sewing_line][$str], 2) ?>
                                    </td>
                                    <? $line_wise_monthly_cm += $total_cm_val_sum[$sewing_line][$str];
                                                }
                                                ?>
                                    <td align="right">
                                        <? echo number_format($line_wise_monthly_cm, 2) ?>
                                    </td>
                                </tr>
                                <tr>


                                    <td>Line Wise Monthly CM Status</td>
                                    <?
                                                $total_day_status = 0;
                                                foreach ($dates as $key => $val) {
                                                    $str = strtotime($val);
                                                    $line_cm_Tot = $total_cm_val_sum[$sewing_line][$str] - $line_cost_days_arr[$sewing_line][$str];
                                                    $floor_line_monthly_Total[$str] += $line_cm_Tot;
                                                    $floor_total_cm_status_val[$str] += $line_cm_Tot;
                                                    ?>
                                                    <td align="right" width="60">
                                                        <? echo number_format($line_cm_Tot, 2) ?>
                                                    </td>
                                                    <?
                                                    $total_day_status += $line_cm_Tot;
                                                }
                                                ?>
                                    <td align="right">
                                        <? echo number_format($total_day_status, 2) ?>
                                    </td>
                                </tr>


                                <?
                            }
                        }   
                                ?>
                                <tr style="font-size:1.1rem !important;font-weight:bold; background: #dddddd;">
                                    <td rowspan="3" align="center" valign="middle" colspan="2">
                                        <? echo $floor_library[$floor_id] ?> Floor total
                                    </td>
                                    <td>Line Wise Monthly Production Qty</td>
                                    <?
                                            foreach ($dates as $key => $val) {
                                                $str = strtotime($val);
                                                ?>
                                    <td align="right" width="60">
                                        <? echo  number_format($floor_total[$str], 2); ?>
                                    </td>
                                    <?
                                                $final_floor_total_qty += $floor_total[$str];
                                            }
                                            ?>
                                    <td align="right">
                                        <? echo number_format($final_floor_total_qty, 2) ?>
                                    </td>
                                </tr>
                                <tr style="font-size:1.1rem !important;font-weight:bold; background: #dddddd;">
                                    <td>Line Wise Monthly CM Earn</td>
                                    <?
                                            foreach ($dates as $key => $val) {
                                                $str = strtotime($val);
                                                ?>
                                    <td width="60" align="right">
                                        <? echo number_format($floor_line_total[$str], 2); ?>
                                    </td>
                                    <?
                                                $final_cm_earn += $floor_line_total[$str];
                                            }
                                            ?>
                                    <td align="right">
                                        <? echo number_format($final_cm_earn, 2); ?>
                                    </td>
                                </tr>
                                <tr style="font-size:1.1rem !important;font-weight:bold; background: #dddddd;">


                                    <td>Line Wise Monthly CM Status</td>
                                    <?
                                            foreach ($dates as $key => $val) {
                                                $str = strtotime($val);
                                                ?>
                                    <td width="60" align="right">
                                        <? echo number_format($floor_line_monthly_Total[$str], 2); ?>
                                    </td>
                                    <?
                                                $final_total_cm_status += $floor_line_monthly_Total[$str];
                                            }
                                            ?>
                                    <td align="right">
                                        <? echo number_format($final_total_cm_status, 2); ?>
                                    </td>
                                </tr>

                                <? 
                           
                    }
                                ?>
                    <!--all floore total-->
                    <tr style="font-size:1.1rem !important;font-weight:bold; background: #98E4FF;">
                        <td rowspan="3" colspan="2" align="center" valign="middle">All Floor Total</td>
                        <td>Line Wise Monthly Production Qty</td>
                        <?
                            foreach ($dates as $key => $val) {
                                $str = strtotime($val);
                                ?>
                        <td width="60" align="right">
                            <? echo number_format($final_floor_totalData[$str], 2); ?>
                        </td>
                        <?
                                $total_floor_Wise += $final_floor_totalData[$str];
                            }
                            ?>
                        <td align="right">
                            <? echo number_format($total_floor_Wise, 2) ?>
                        </td>
                    </tr>


                    <tr style="font-size:1.1rem !important;font-weight:bold; background: #98E4FF;">


                        <td>Line Wise Monthly CM Earn</td>
                        <?
                            foreach ($dates as $key => $val) {
                                $str = strtotime($val);
                                ?>
                        <td width="60" align="right">
                            <? echo number_format($floor_total_cm_earn[$str], 2) ?>
                        </td>
                        <?
                                $total_cm += $floor_total_cm_earn[$str];
                            }
                            ?>
                        <td align="right">
                            <? echo number_format($total_cm, 2) ?>
                        </td>
                    </tr>
                    <tr style="font-size:1.1rem !important;font-weight:bold;bold; background: #98E4FF;">


                        <td>Line Wise Monthly CM Status</td>
                        <?
                            foreach ($dates as $key => $val) 
                            {
                                $str = strtotime($val);
                                ?>
                                <td width="60" align="right">
                                    <? echo number_format($floor_total_cm_status_val[$str], 2) ?>
                                </td>
                                <?
                                $totalCm_status += $floor_total_cm_status_val[$str];
                            }
                            ?>
                        <td align="right">
                            <? echo number_format($totalCm_status, 2); ?>
                        </td>
                    </tr>
                    <?php
                        ?>

            </tbody>
        </table>
    </div>

    <?

    $html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_name*.xls") as $filename) {
        @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = "" . $user_name . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$excel_type####$filename";
    exit();
}


if($action=="orderQnty_popup")
{

	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);

	$sql= "SELECT b.id,b.po_number,b.pub_shipment_date,b.po_quantity*(select from wo_po_details_mas_set_details set where set.job_no=a.job_no and set.gmts_item_id=$gmts_item_id) as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$po_break_down_id' and a.garments_nature='$garments_nature' and a.is_deleted=0 and a.status_active=1";
	//echo $sql;
	echo "<br />". create_list_view ( "list_view", "Order No,Order Qnty,Pub Shipment Date", "200,120,220","540","220",1, "SELECT b.id,b.po_number,b.pub_shipment_date,b.po_quantity*a.total_set_qnty as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$po_break_down_id' and a.is_deleted=0 and a.status_active=1", "", "","", 1, '0,0,0', $arr, "po_number,po_quantity,pub_shipment_date","../requires/date_wise_production_report_controller", '','0,1,3');

	exit();
}

if($action=='date_wise_production_report')
{
	extract($_REQUEST);
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');
    ?>
	<fieldset>
    <legend>Cutting</legend>
    	<?
			 $i=1;
			 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='1' and is_deleted=0 and status_active=1";
			 $result=sql_select($sql);
			 $avg_prod_qty="";

 		?>

	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
			 {
			 	?>
		<tr>
			<td width="50"><? echo $i; ?></td>
			<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
			<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
			<td>
			<? echo $row[csf('remarks')];
			$avg_prod_qty+=$row[csf('production_quantity')];
			?>

			</td>
		</tr>
		 <?
		 $i++;
		}
		 ?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>

    <fieldset>
    <legend>Print/Embr Issue</legend>
    	   	<?
			 $i=1;
			 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='2' and is_deleted=0 and status_active=1";
			 $result=sql_select($sql);
			 $avg_prod_qty="";

 		?>

	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
			 {
			 	?>
		<tr>
			<td width="50"><? echo $i; ?></td>
			<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
			<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
			<td>
			<? echo $row[csf('remarks')];
			$avg_prod_qty+=$row[csf('production_quantity')];
			?>

			</td>
		</tr>
		 <?
		 $i++;
		}
		 ?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>


        <fieldset>
    <legend>Print/Embr Receive</legend>
    	   	<?
			 $i=1;
			 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='3' and is_deleted=0 and status_active=1";
			 $result=sql_select($sql);
			 $avg_prod_qty="";

 		?>

	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
			 {
			 	?>
		<tr>
			<td width="50"><? echo $i; ?></td>
			<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
			<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
			<td>
			<? echo $row[csf('remarks')];
			$avg_prod_qty+=$row[csf('production_quantity')];
			?>

			</td>
		</tr>
		 <?
		 $i++;
		}
		 ?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>


    <fieldset>
    <legend>Sewing Input</legend>
    	   	<?
			 $i=1;
			 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='4' and is_deleted=0 and status_active=1";
			 $result=sql_select($sql);
			 $avg_prod_qty="";

 		?>

	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
			 {
			 	?>
		<tr>
			<td width="50"><? echo $i; ?></td>
			<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
			<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
			<td>
			<? echo $row[csf('remarks')];
			$avg_prod_qty+=$row[csf('production_quantity')];
			?>

			</td>
		</tr>
		 <?
		 $i++;
		}
		 ?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>


        <fieldset>
    <legend>Sewing Output</legend>
    	   	<?
			 $i=1;
			 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='5' and is_deleted=0 and status_active=1";
			 $result=sql_select($sql);
			 $avg_prod_qty="";

 		?>

	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
			 {
			 	?>
		<tr>
			<td width="50"><? echo $i; ?></td>
			<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
			<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
			<td>
			<? echo $row[csf('remarks')];
			$avg_prod_qty+=$row[csf('production_quantity')];
			?>

			</td>
		</tr>
		 <?
		 $i++;
		}
		 ?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>


    <fieldset>
    <legend>Finish Input</legend>
    	   	<?
			 $i=1;
			 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='6' and is_deleted=0 and status_active=1";
			 $result=sql_select($sql);
			 $avg_prod_qty="";

 		?>

	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
			 {
			 	?>
		<tr>
			<td width="50"><? echo $i; ?></td>
			<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
			<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
			<td>
			<? echo $row[csf('remarks')];
			$avg_prod_qty+=$row[csf('production_quantity')];
			?>

			</td>
		</tr>
		 <?
		 $i++;
		}
		 ?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>


	    <fieldset>
    <legend>Finish Output</legend>
    	   	<?
			 $i=1;
			 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id' and production_type='8' and is_deleted=0 and status_active=1";
			 $result=sql_select($sql);
			 $avg_prod_qty="";

 		?>

	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
			 {
			 	?>
		<tr>
			<td width="50"><? echo $i; ?></td>
			<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
			<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
			<td>
			<? echo $row[csf('remarks')];
			$avg_prod_qty+=$row[csf('production_quantity')];
			?>

			</td>
		</tr>
		 <?
		 $i++;
		}
		 ?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>


    <?
}//end if

//cutting_popup
if($action=='cutting_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=1 and a.item_number_id=$gmts_item_id and a.production_source=$production_source and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");

	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


    ?>
    <script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

    </script>
    <fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise cutting production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in;
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}

			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
    <?


}
//cutting_popup_location
if($action=='cutting_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );
	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=1 and a.item_number_id=$gmts_item_id and a.production_source=$production_source and a.production_date='$production_date'  $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


    ?>
    <script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

    </script>
    <fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise cutting production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in;
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}

			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?

}

if($action=='printing_issue_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond  and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


    ?>
    <script>

    	function window_close()
    	{
    		parent.emailwindow.hide();
    	}

    </script>
    <fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
        <div style="100%" id="report_container">

            <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
            	<tr>
                	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise printing production</td>
                </tr>
            	<tr>
                	<td style="font-size:16px; font-weight:bold;">
                    Date : <? echo change_date_format($production_date); ?>
                    <br />
                    Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    	<th width="40" rowspan="2">SI</th>
                        <th width="100" rowspan="2">Color</th>
                        <th width="100" rowspan="2">Country Name</th>
                    	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                        <th width="80" rowspan="2" >Total</th>
                    </tr>
                    <tr>
                    <?
    				$grand_tot_in=0;
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
    				}
    				?>
                    </tr>

                </thead>
                <tbody>
                <?
    			$i=1;
    			$k=1;
    			//var_dump($result);die;
    			foreach($details_data as $color_id=>$value)
    			{
    				foreach($value as $country_id=>$row)
    				{
    					if(!in_array($color_id,$temp_arr))
    					{
    						$temp_arr[]=$color_id;
    						if($k!=1)
    						{
    							?>
    							<tr bgcolor="#CCCCCC">
    								<td >&nbsp;</td>
    								<td>&nbsp;</td>
    								<td align="right" style="font-weight:bold">Color Total:</td>
    								<?
    								foreach($sizearr_order as $size_id)
    								{
    									?>
    									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
    									<?
    								}
    								?>
    								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
    							</tr>
    							<?
    							$line_color_size_in = $line_color_total_in ="";
    						}
    						$k++;
    					}

    					?>
    					<tr>
    						<td align="center"><? echo $i;  ?></td>
                            <?
    						if(!in_array($color_id,$temp_arr_color))
    						{
    							$temp_arr_color[]=$color_id;
    							?>
    							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
    							<?
    						}
    						?>
    						<td ><p><? echo $country_library[$country_id];  ?></p></td>
    						<?
    						$color_total_in=0;
    						foreach($sizearr_order as $size_id)
    						{
    							?>
    							<td align="right"><p>
    							<?
    								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
    								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
    								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    							 ?>
    							</p></td>
    							<?
    						}
    						$line_color_total_in+=$color_total_in;
    						?>
    						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
    					</tr>
    					<?
    					$i++;
    				}

    			}
    			?>
                <tr bgcolor="#CCCCCC">
                    <td >&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right" style="font-weight:bold">Color Total:</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
                </tr>
                </tbody>
                <tfoot>
                    <th >&nbsp;</th>
                    <th>&nbsp;</th>
                    <th >Day Total:</th>
                    <?
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th ><? echo $color_size_in[$size_id]; ?></th>
                        <?
    				}
    				?>
                    <th ><? echo $grand_tot_in; ?></th>

                </tfoot>
            </table>
            </div>
        </fieldset>
    <?


}
if($action=='printing_issue_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


    ?>
    <script>

    	function window_close()
    	{
    		parent.emailwindow.hide();
    	}

    </script>
    <fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
        <div style="100%" id="report_container">

            <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
            	<tr>
                	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise printing production</td>
                </tr>
            	<tr>
                	<td style="font-size:16px; font-weight:bold;">
                    Date : <? echo change_date_format($production_date); ?>
                    <br />
                    Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    	<th width="40" rowspan="2">SI</th>
                        <th width="100" rowspan="2">Color</th>
                        <th width="100" rowspan="2">Country Name</th>
                    	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                        <th width="80" rowspan="2" >Total</th>
                    </tr>
                    <tr>
                    <?
    				$grand_tot_in=0;
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
    				}
    				?>
                    </tr>

                </thead>
                <tbody>
                <?
    			$i=1;
    			$k=1;
    			//var_dump($result);die;
    			foreach($details_data as $color_id=>$value)
    			{
    				foreach($value as $country_id=>$row)
    				{
    					if(!in_array($color_id,$temp_arr))
    					{
    						$temp_arr[]=$color_id;
    						if($k!=1)
    						{
    							?>
    							<tr bgcolor="#CCCCCC">
    								<td >&nbsp;</td>
    								<td>&nbsp;</td>
    								<td align="right" style="font-weight:bold">Color Total:</td>
    								<?
    								foreach($sizearr_order as $size_id)
    								{
    									?>
    									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
    									<?
    								}
    								?>
    								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
    							</tr>
    							<?
    							$line_color_size_in = $line_color_total_in ="";
    						}
    						$k++;
    					}

    					?>
    					<tr>
    						<td align="center"><? echo $i;  ?></td>
                            <?
    						if(!in_array($color_id,$temp_arr_color))
    						{
    							$temp_arr_color[]=$color_id;
    							?>
    							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
    							<?
    						}
    						?>
    						<td ><p><? echo $country_library[$country_id];  ?></p></td>
    						<?
    						$color_total_in=0;
    						foreach($sizearr_order as $size_id)
    						{
    							?>
    							<td align="right"><p>
    							<?
    								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
    								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
    								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    							 ?>

    							</p></td>
    							<?
    						}
    						$line_color_total_in+=$color_total_in;
    						?>
    						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
    					</tr>
    					<?
    					$i++;
    				}

    			}
    			?>
                <tr bgcolor="#CCCCCC">
                    <td >&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right" style="font-weight:bold">Color Total:</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
                </tr>
                </tbody>
                <tfoot>
                    <th >&nbsp;</th>
                    <th>&nbsp;</th>
                    <th >Day Total:</th>
                    <?
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th ><? echo $color_size_in[$size_id]; ?></th>
                        <?
    				}
    				?>
                    <th ><? echo $grand_tot_in; ?></th>

                </tfoot>
            </table>
            </div>
        </fieldset>
    <?
}
if($action=='printing_receive_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


    ?>
    <script>

    	function window_close()
    	{
    		parent.emailwindow.hide();
    	}

    </script>
    <fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
        <div style="100%" id="report_container">

            <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
            	<tr>
                	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise printing production</td>
                </tr>
            	<tr>
                	<td style="font-size:16px; font-weight:bold;">
                    Date : <? echo change_date_format($production_date); ?>
                    <br />
                    Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    	<th width="40" rowspan="2">SI</th>
                        <th width="100" rowspan="2">Color</th>
                        <th width="100" rowspan="2">Country Name</th>
                    	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                        <th width="80" rowspan="2" >Total</th>
                    </tr>
                    <tr>
                    <?
    				$grand_tot_in=0;
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
    				}
    				?>
                    </tr>

                </thead>
                <tbody>
                <?
    			$i=1;
    			$k=1;
    			//var_dump($result);die;
    			foreach($details_data as $color_id=>$value)
    			{
    				foreach($value as $country_id=>$row)
    				{
    					if(!in_array($color_id,$temp_arr))
    					{
    						$temp_arr[]=$color_id;
    						if($k!=1)
    						{
    							?>
    							<tr bgcolor="#CCCCCC">
    								<td >&nbsp;</td>
    								<td>&nbsp;</td>
    								<td align="right" style="font-weight:bold">Color Total:</td>
    								<?
    								foreach($sizearr_order as $size_id)
    								{
    									?>
    									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
    									<?
    								}
    								?>
    								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
    							</tr>
    							<?
    							$line_color_size_in = $line_color_total_in ="";
    						}
    						$k++;
    					}

    					?>
    					<tr>
    						<td align="center"><? echo $i;  ?></td>
                            <?
    						if(!in_array($color_id,$temp_arr_color))
    						{
    							$temp_arr_color[]=$color_id;
    							?>
    							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
    							<?
    						}
    						?>
    						<td ><p><? echo $country_library[$country_id];  ?></p></td>
    						<?
    						$color_total_in=0;
    						foreach($sizearr_order as $size_id)
    						{
    							?>
    							<td align="right"><p>
    							<?
    								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
    								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
    								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    							 ?>
    							</p></td>
    							<?
    						}
    						$line_color_total_in+=$color_total_in;
    						?>
    						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
    					</tr>
    					<?
    					$i++;
    				}

    			}
    			?>
                <tr bgcolor="#CCCCCC">
                    <td >&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right" style="font-weight:bold">Color Total:</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
                </tr>
                </tbody>
                <tfoot>
                    <th >&nbsp;</th>
                    <th>&nbsp;</th>
                    <th >Day Total:</th>
                    <?
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th ><? echo $color_size_in[$size_id]; ?></th>
                        <?
    				}
    				?>
                    <th ><? echo $grand_tot_in; ?></th>

                </tfoot>
            </table>
            </div>
        </fieldset>
    <?
}

if($action=='printing_receive_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


    ?>
    <script>

    	function window_close()
    	{
    		parent.emailwindow.hide();
    	}

    </script>
    <fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
        <div style="100%" id="report_container">

            <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
            	<tr>
                	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise printing production</td>
                </tr>
            	<tr>
                	<td style="font-size:16px; font-weight:bold;">
                    Date : <? echo change_date_format($production_date); ?>
                    <br />
                    Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    	<th width="40" rowspan="2">SI</th>
                        <th width="100" rowspan="2">Color</th>
                        <th width="100" rowspan="2">Country Name</th>
                    	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                        <th width="80" rowspan="2" >Total</th>
                    </tr>
                    <tr>
                    <?
    				$grand_tot_in=0;
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
    				}
    				?>
                    </tr>

                </thead>
                <tbody>
                <?
    			$i=1;
    			$k=1;
    			//var_dump($result);die;
    			foreach($details_data as $color_id=>$value)
    			{
    				foreach($value as $country_id=>$row)
    				{
    					if(!in_array($color_id,$temp_arr))
    					{
    						$temp_arr[]=$color_id;
    						if($k!=1)
    						{
    							?>
    							<tr bgcolor="#CCCCCC">
    								<td >&nbsp;</td>
    								<td>&nbsp;</td>
    								<td align="right" style="font-weight:bold">Color Total:</td>
    								<?
    								foreach($sizearr_order as $size_id)
    								{
    									?>
    									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
    									<?
    								}
    								?>
    								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
    							</tr>
    							<?
    							$line_color_size_in = $line_color_total_in ="";
    						}
    						$k++;
    					}

    					?>
    					<tr>
    						<td align="center"><? echo $i;  ?></td>
                            <?
    						if(!in_array($color_id,$temp_arr_color))
    						{
    							$temp_arr_color[]=$color_id;
    							?>
    							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
    							<?
    						}
    						?>
    						<td ><p><? echo $country_library[$country_id];  ?></p></td>
    						<?
    						$color_total_in=0;
    						foreach($sizearr_order as $size_id)
    						{
    							?>
    							<td align="right"><p>
    							<?
    								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
    								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
    								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    							 ?>
    							</p></td>
    							<?
    						}
    						$line_color_total_in+=$color_total_in;
    						?>
    						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
    					</tr>
    					<?
    					$i++;
    				}

    			}
    			?>
                <tr bgcolor="#CCCCCC">
                    <td >&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right" style="font-weight:bold">Color Total:</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
                </tr>
                </tbody>
                <tfoot>
                    <th >&nbsp;</th>
                    <th>&nbsp;</th>
                    <th >Day Total:</th>
                    <?
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th ><? echo $color_size_in[$size_id]; ?></th>
                        <?
    				}
    				?>
                    <th ><? echo $grand_tot_in; ?></th>

                </tfoot>
            </table>
            </div>
        </fieldset>
    <?
}

if($action=='embroi_issue_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=2 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


    ?>
    <script>

    	function window_close()
    	{
    		parent.emailwindow.hide();
    	}

    </script>
    <fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
        <div style="100%" id="report_container">

            <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
            	<tr>
                	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
                </tr>
            	<tr>
                	<td style="font-size:16px; font-weight:bold;">
                    Date : <? echo change_date_format($production_date); ?>
                    <br />
                    Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    	<th width="40" rowspan="2">SI</th>
                        <th width="100" rowspan="2">Color</th>
                        <th width="100" rowspan="2">Country Name</th>
                    	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                        <th width="80" rowspan="2" >Total</th>
                    </tr>
                    <tr>
                    <?
    				$grand_tot_in=0;
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
    				}
    				?>
                    </tr>

                </thead>
                <tbody>
                <?
    			$i=1;
    			$k=1;
    			//var_dump($result);die;
    			foreach($details_data as $color_id=>$value)
    			{
    				foreach($value as $country_id=>$row)
    				{
    					if(!in_array($color_id,$temp_arr))
    					{
    						$temp_arr[]=$color_id;
    						if($k!=1)
    						{
    							?>
    							<tr bgcolor="#CCCCCC">
    								<td >&nbsp;</td>
    								<td>&nbsp;</td>
    								<td align="right" style="font-weight:bold">Color Total:</td>
    								<?
    								foreach($sizearr_order as $size_id)
    								{
    									?>
    									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
    									<?
    								}
    								?>
    								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
    							</tr>
    							<?
    							$line_color_size_in = $line_color_total_in ="";
    						}
    						$k++;
    					}

    					?>
    					<tr>
    						<td align="center"><? echo $i;  ?></td>
                            <?
    						if(!in_array($color_id,$temp_arr_color))
    						{
    							$temp_arr_color[]=$color_id;
    							?>
    							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
    							<?
    						}
    						?>
    						<td ><p><? echo $country_library[$country_id];  ?></p></td>
    						<?
    						$color_total_in=0;
    						foreach($sizearr_order as $size_id)
    						{
    							?>
    							<td align="right"><p>
    							<?
    								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
    								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
    								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    							 ?>
    							</p></td>
    							<?
    						}
    						$line_color_total_in+=$color_total_in;
    						?>
    						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
    					</tr>
    					<?
    					$i++;
    				}

    			}
    			?>
                <tr bgcolor="#CCCCCC">
                    <td >&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right" style="font-weight:bold">Color Total:</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
                </tr>
                </tbody>
                <tfoot>
                    <th >&nbsp;</th>
                    <th>&nbsp;</th>
                    <th >Day Total:</th>
                    <?
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th ><? echo $color_size_in[$size_id]; ?></th>
                        <?
    				}
    				?>
                    <th ><? echo $grand_tot_in; ?></th>

                </tfoot>
            </table>
            </div>
        </fieldset>
    <?
}

if($action=='embroi_issue_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=2 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


    ?>
    <script>

    	function window_close()
    	{
    		parent.emailwindow.hide();
    	}

    </script>
    <fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
        <div style="100%" id="report_container">

            <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
            	<tr>
                	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
                </tr>
            	<tr>
                	<td style="font-size:16px; font-weight:bold;">
                    Date : <? echo change_date_format($production_date); ?>
                    <br />
                    Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    	<th width="40" rowspan="2">SI</th>
                        <th width="100" rowspan="2">Color</th>
                        <th width="100" rowspan="2">Country Name</th>
                    	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                        <th width="80" rowspan="2" >Total</th>
                    </tr>
                    <tr>
                    <?
    				$grand_tot_in=0;
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
    				}
    				?>
                    </tr>

                </thead>
                <tbody>
                <?
    			$i=1;
    			$k=1;
    			//var_dump($result);die;
    			foreach($details_data as $color_id=>$value)
    			{
    				foreach($value as $country_id=>$row)
    				{
    					if(!in_array($color_id,$temp_arr))
    					{
    						$temp_arr[]=$color_id;
    						if($k!=1)
    						{
    							?>
    							<tr bgcolor="#CCCCCC">
    								<td >&nbsp;</td>
    								<td>&nbsp;</td>
    								<td align="right" style="font-weight:bold">Color Total:</td>
    								<?
    								foreach($sizearr_order as $size_id)
    								{
    									?>
    									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
    									<?
    								}
    								?>
    								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
    							</tr>
    							<?
    							$line_color_size_in = $line_color_total_in ="";
    						}
    						$k++;
    					}

    					?>
    					<tr>
    						<td align="center"><? echo $i;  ?></td>
                            <?
    						if(!in_array($color_id,$temp_arr_color))
    						{
    							$temp_arr_color[]=$color_id;
    							?>
    							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
    							<?
    						}
    						?>
    						<td ><p><? echo $country_library[$country_id];  ?></p></td>
    						<?
    						$color_total_in=0;
    						foreach($sizearr_order as $size_id)
    						{
    							?>
    							<td align="right"><p>
    							<?
    								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
    								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
    								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    							 ?>
    							</p></td>
    							<?
    						}
    						$line_color_total_in+=$color_total_in;
    						?>
    						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
    					</tr>
    					<?
    					$i++;
    				}

    			}
    			?>
                <tr bgcolor="#CCCCCC">
                    <td >&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right" style="font-weight:bold">Color Total:</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
                </tr>
                </tbody>
                <tfoot>
                    <th >&nbsp;</th>
                    <th>&nbsp;</th>
                    <th >Day Total:</th>
                    <?
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th ><? echo $color_size_in[$size_id]; ?></th>
                        <?
    				}
    				?>
                    <th ><? echo $grand_tot_in; ?></th>

                </tfoot>
            </table>
            </div>
        </fieldset>
    <?
}

if($action=='embroi_gmt_dyeing_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond="";
	$sewing_cond="";
	if($location!=0) $location_cond="and a.location=$location";
	if($floor_id!=0) $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!=0) $sewing_cond=" and a.sewing_line=$sewing_line";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=$production_source and a.embel_name=5 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


    ?>
    <script>

    	function window_close()
    	{
    		parent.emailwindow.hide();
    	}

    </script>
    <fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
        <div style="100%" id="report_container">

            <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
            	<tr>
                	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
                </tr>
            	<tr>
                	<td style="font-size:16px; font-weight:bold;">
                    Date : <? echo change_date_format($production_date); ?>
                    <br />
                    Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    	<th width="40" rowspan="2">SI</th>
                        <th width="100" rowspan="2">Color</th>
                        <th width="100" rowspan="2">Country Name</th>
                    	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                        <th width="80" rowspan="2" >Total</th>
                    </tr>
                    <tr>
                    <?
    				$grand_tot_in=0;
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
    				}
    				?>
                    </tr>

                </thead>
                <tbody>
                <?
    			$i=1;
    			$k=1;
    			//var_dump($result);die;
    			foreach($details_data as $color_id=>$value)
    			{
    				foreach($value as $country_id=>$row)
    				{
    					if(!in_array($color_id,$temp_arr))
    					{
    						$temp_arr[]=$color_id;
    						if($k!=1)
    						{
    							?>
    							<tr bgcolor="#CCCCCC">
    								<td >&nbsp;</td>
    								<td>&nbsp;</td>
    								<td align="right" style="font-weight:bold">Color Total:</td>
    								<?
    								foreach($sizearr_order as $size_id)
    								{
    									?>
    									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
    									<?
    								}
    								?>
    								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
    							</tr>
    							<?
    							$line_color_size_in = $line_color_total_in ="";
    						}
    						$k++;
    					}

    					?>
    					<tr>
    						<td align="center"><? echo $i;  ?></td>
                            <?
    						if(!in_array($color_id,$temp_arr_color))
    						{
    							$temp_arr_color[]=$color_id;
    							?>
    							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
    							<?
    						}
    						?>
    						<td ><p><? echo $country_library[$country_id];  ?></p></td>
    						<?
    						$color_total_in=0;
    						foreach($sizearr_order as $size_id)
    						{
    							?>
    							<td align="right"><p>
    							<?
    								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
    								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
    								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    							 ?>
    							</p></td>
    							<?
    						}
    						$line_color_total_in+=$color_total_in;
    						?>
    						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
    					</tr>
    					<?
    					$i++;
    				}

    			}
    			?>
                <tr bgcolor="#CCCCCC">
                    <td >&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right" style="font-weight:bold">Color Total:</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
                </tr>
                </tbody>
                <tfoot>
                    <th >&nbsp;</th>
                    <th>&nbsp;</th>
                    <th >Day Total:</th>
                    <?
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th ><? echo $color_size_in[$size_id]; ?></th>
                        <?
    				}
    				?>
                    <th ><? echo $grand_tot_in; ?></th>

                </tfoot>
            </table>
            </div>
        </fieldset>
    <?
}

if($action=='embroi_receive_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=2 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


    ?>
    <script>

    	function window_close()
    	{
    		parent.emailwindow.hide();
    	}

    </script>
    <fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
        <div style="100%" id="report_container">

            <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
            	<tr>
                	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
                </tr>
            	<tr>
                	<td style="font-size:16px; font-weight:bold;">
                    Date : <? echo change_date_format($production_date); ?>
                    <br />
                    Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    	<th width="40" rowspan="2">SI</th>
                        <th width="100" rowspan="2">Color</th>
                        <th width="100" rowspan="2">Country Name</th>
                    	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                        <th width="80" rowspan="2" >Total</th>
                    </tr>
                    <tr>
                    <?
    				$grand_tot_in=0;
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
    				}
    				?>
                    </tr>

                </thead>
                <tbody>
                <?
    			$i=1;
    			$k=1;
    			//var_dump($result);die;
    			foreach($details_data as $color_id=>$value)
    			{
    				foreach($value as $country_id=>$row)
    				{
    					if(!in_array($color_id,$temp_arr))
    					{
    						$temp_arr[]=$color_id;
    						if($k!=1)
    						{
    							?>
    							<tr bgcolor="#CCCCCC">
    								<td >&nbsp;</td>
    								<td>&nbsp;</td>
    								<td align="right" style="font-weight:bold">Color Total:</td>
    								<?
    								foreach($sizearr_order as $size_id)
    								{
    									?>
    									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
    									<?
    								}
    								?>
    								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
    							</tr>
    							<?
    							$line_color_size_in = $line_color_total_in ="";
    						}
    						$k++;
    					}

    					?>
    					<tr>
    						<td align="center"><? echo $i;  ?></td>
                            <?
    						if(!in_array($color_id,$temp_arr_color))
    						{
    							$temp_arr_color[]=$color_id;
    							?>
    							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
    							<?
    						}
    						?>
    						<td ><p><? echo $country_library[$country_id];  ?></p></td>
    						<?
    						$color_total_in=0;
    						foreach($sizearr_order as $size_id)
    						{
    							?>
    							<td align="right"><p>
    							<?
    								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
    								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
    								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    							 ?>
    							</p></td>
    							<?
    						}
    						$line_color_total_in+=$color_total_in;
    						?>
    						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
    					</tr>
    					<?
    					$i++;
    				}

    			}
    			?>
                <tr bgcolor="#CCCCCC">
                    <td >&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right" style="font-weight:bold">Color Total:</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
                </tr>
                </tbody>
                <tfoot>
                    <th >&nbsp;</th>
                    <th>&nbsp;</th>
                    <th >Day Total:</th>
                    <?
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th ><? echo $color_size_in[$size_id]; ?></th>
                        <?
    				}
    				?>
                    <th ><? echo $grand_tot_in; ?></th>

                </tfoot>
            </table>
            </div>
        </fieldset>
    <?
}

if($action=='embroi_gmt_dyeing_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=$production_source and a.embel_name=5 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


    ?>
    <script>

    	function window_close()
    	{
    		parent.emailwindow.hide();
    	}

    </script>
    <fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
        <div style="100%" id="report_container">

            <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
            	<tr>
                	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
                </tr>
            	<tr>
                	<td style="font-size:16px; font-weight:bold;">
                    Date : <? echo change_date_format($production_date); ?>
                    <br />
                    Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    	<th width="40" rowspan="2">SI</th>
                        <th width="100" rowspan="2">Color</th>
                        <th width="100" rowspan="2">Country Name</th>
                    	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                        <th width="80" rowspan="2" >Total</th>
                    </tr>
                    <tr>
                    <?
    				$grand_tot_in=0;
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
    				}
    				?>
                    </tr>

                </thead>
                <tbody>
                <?
    			$i=1;
    			$k=1;
    			//var_dump($result);die;
    			foreach($details_data as $color_id=>$value)
    			{
    				foreach($value as $country_id=>$row)
    				{
    					if(!in_array($color_id,$temp_arr))
    					{
    						$temp_arr[]=$color_id;
    						if($k!=1)
    						{
    							?>
    							<tr bgcolor="#CCCCCC">
    								<td >&nbsp;</td>
    								<td>&nbsp;</td>
    								<td align="right" style="font-weight:bold">Color Total:</td>
    								<?
    								foreach($sizearr_order as $size_id)
    								{
    									?>
    									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
    									<?
    								}
    								?>
    								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
    							</tr>
    							<?
    							$line_color_size_in = $line_color_total_in ="";
    						}
    						$k++;
    					}

    					?>
    					<tr>
    						<td align="center"><? echo $i;  ?></td>
                            <?
    						if(!in_array($color_id,$temp_arr_color))
    						{
    							$temp_arr_color[]=$color_id;
    							?>
    							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
    							<?
    						}
    						?>
    						<td ><p><? echo $country_library[$country_id];  ?></p></td>
    						<?
    						$color_total_in=0;
    						foreach($sizearr_order as $size_id)
    						{
    							?>
    							<td align="right"><p>
    							<?
    								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
    								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
    								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    							 ?>
    							</p></td>
    							<?
    						}
    						$line_color_total_in+=$color_total_in;
    						?>
    						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
    					</tr>
    					<?
    					$i++;
    				}

    			}
    			?>
                <tr bgcolor="#CCCCCC">
                    <td >&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right" style="font-weight:bold">Color Total:</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
                </tr>
                </tbody>
                <tfoot>
                    <th >&nbsp;</th>
                    <th>&nbsp;</th>
                    <th >Day Total:</th>
                    <?
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th ><? echo $color_size_in[$size_id]; ?></th>
                        <?
    				}
    				?>
                    <th ><? echo $grand_tot_in; ?></th>

                </tfoot>
            </table>
            </div>
        </fieldset>
    <?
}

if($action=='embroi_receive_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=2 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


    ?>
    <script>

    	function window_close()
    	{
    		parent.emailwindow.hide();
    	}

    </script>
    <fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
        <div style="100%" id="report_container">

            <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
            	<tr>
                	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
                </tr>
            	<tr>
                	<td style="font-size:16px; font-weight:bold;">
                    Date : <? echo change_date_format($production_date); ?>
                    <br />
                    Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    	<th width="40" rowspan="2">SI</th>
                        <th width="100" rowspan="2">Color</th>
                        <th width="100" rowspan="2">Country Name</th>
                    	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                        <th width="80" rowspan="2" >Total</th>
                    </tr>
                    <tr>
                    <?
    				$grand_tot_in=0;
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
    				}
    				?>
                    </tr>

                </thead>
                <tbody>
                <?
    			$i=1;
    			$k=1;
    			//var_dump($result);die;
    			foreach($details_data as $color_id=>$value)
    			{
    				foreach($value as $country_id=>$row)
    				{
    					if(!in_array($color_id,$temp_arr))
    					{
    						$temp_arr[]=$color_id;
    						if($k!=1)
    						{
    							?>
    							<tr bgcolor="#CCCCCC">
    								<td >&nbsp;</td>
    								<td>&nbsp;</td>
    								<td align="right" style="font-weight:bold">Color Total:</td>
    								<?
    								foreach($sizearr_order as $size_id)
    								{
    									?>
    									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
    									<?
    								}
    								?>
    								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
    							</tr>
    							<?
    							$line_color_size_in = $line_color_total_in ="";
    						}
    						$k++;
    					}

    					?>
    					<tr>
    						<td align="center"><? echo $i;  ?></td>
                            <?
    						if(!in_array($color_id,$temp_arr_color))
    						{
    							$temp_arr_color[]=$color_id;
    							?>
    							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
    							<?
    						}
    						?>
    						<td ><p><? echo $country_library[$country_id];  ?></p></td>
    						<?
    						$color_total_in=0;
    						foreach($sizearr_order as $size_id)
    						{
    							?>
    							<td align="right"><p>
    							<?
    								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
    								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
    								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
    							 ?>
    							</p></td>
    							<?
    						}
    						$line_color_total_in+=$color_total_in;
    						?>
    						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
    					</tr>
    					<?
    					$i++;
    				}

    			}
    			?>
                <tr bgcolor="#CCCCCC">
                    <td >&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right" style="font-weight:bold">Color Total:</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
                </tr>
                </tbody>
                <tfoot>
                    <th >&nbsp;</th>
                    <th>&nbsp;</th>
                    <th >Day Total:</th>
                    <?
    				foreach($sizearr_order as $size_id)
    				{
    					?>
                    	<th ><? echo $color_size_in[$size_id]; ?></th>
                        <?
    				}
    				?>
                    <th ><? echo $grand_tot_in; ?></th>

                </tfoot>
            </table>
            </div>
        </fieldset>
    <?
}

if($action=='wash_issue_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=3 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise wash production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in;
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}

			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?
}

if($action=='wash_issue_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=3 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise wash production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in;
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}

			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?
}

if($action=='wash_receive_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=3 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise wash production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in;
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}

			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?
}

if($action=='wash_receive_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=3 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise wash production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in;
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}

			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?
}

if($action=='sp_issue_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise special works production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in;
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}

			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>

                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?
}

if($action=='sp_issue_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise special works production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in;
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}

			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?
}

if($action=='sp_receive_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";


	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise special works production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in;
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}

			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?
}

if($action=='sp_receive_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise special works production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in;
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}

			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?
}
if($action=="sewingQnty_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$sewing_line_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	//var_dump();die;

	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	//echo $po_details_sql;die;

	//For Show Date Location and Floor
	if($location_id!=0) $location_cond=" and a.location in($location_id)"; else  $location_cond="";
	if($floor_id!=0) $floor_cond=" and a.floor_id in($floor_id)"; else  $floor_cond="";
	if($sewing_line!=0) $sewing_line_cond=" and a.sewing_line in($sewing_line)"; else  $sewing_line_cond="";

	if($db_type==2)
	{
		 $sql= "SELECT  a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, a.serving_company, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id , a.prod_reso_allo
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=$page and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.production_source='$prod_source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $location_cond $floor_cond $sewing_line_cond
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.serving_company, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	else if($db_type==0)
	{
		 $sql= "SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, a.serving_company, c.color_number_id, group_concat(c.size_number_id) as size_number_id , a.prod_reso_allo
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=$page and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.production_source='$prod_source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $location_cond $floor_cond $sewing_line_cond
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.serving_company, a.prod_reso_allo, c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;


	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);

	$sql_color_size= sql_select("SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=$page and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.production_source='$prod_source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $location_cond $floor_cond $sewing_line_cond
	group by   a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id
	order by a.country_id");
	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	if($prod_source==3) $table_width=750+$col_width; else $table_width=630+$col_width;
	$summer_table_width=230+$col_width;


?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
		<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">
                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item :
				<?
                    $item_data="";
                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")]));
                    foreach($garments_item_arr as $item_id)
                    {
                        if($item_data!="") $item_data .=", ";
                        $item_data .=$garments_item[$item_id];
                    }
                    echo $item_data;
                ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?>
                <br />
                Summary
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			//var_dump($result);die;
			foreach($summery_data as $color_id=>$row)
			{
				//print_r($row);die;

				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><? echo $colorarr[$color_id];  ?></td>
                    <?
					$summry_color_total_in =0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $summry_color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">Details</td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Country Name</th>
                    <th width="80" rowspan="2">Source</th>
                    <?
					if($prod_source==3)
					{
						?>
                    	<th width="120" rowspan="2">Serving Company</th>
                        <?
					}
					?>
                    <th width="70" rowspan="2">Challan</th>
                    <th width="90" rowspan="2">Sewing Unit</th>
                    <th width="70" rowspan="2" >Sewing Line</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($result as $row)
			{
				if(!in_array($row[csf("sewing_line")],$temp_arr[$row[csf("country_id")]]))
				{
					$temp_arr[$row[csf("country_id")]][]=$row[csf("sewing_line")];
					if($k!=1)
					{
						?>
						<tr bgcolor="#CCCCCC">
							<td >&nbsp;</td>
							<td>&nbsp;</td>
							<td >&nbsp;</td>
                            <?
							if($prod_source==3)
							{
								?>
                            	<td >&nbsp;</td>
                                <?
							}
							?>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<?
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
								<?
							}
							?>
							<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
						</tr>
						<?
						$line_color_size_in = $line_color_total_in ="";
					}
					$k++;
				}

				$sewing_line='';
				if($row[csf('prod_reso_allo')]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$sewing_line_library[$val]; else $sewing_line.=",".$sewing_line_library[$val];
					}
				}
				else $sewing_line=$sewing_line_library[$row[csf('sewing_line')]];

				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
                    <td ><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
                    <?
					if($prod_source==3)
					{
						?>
                    	<td ><p><? echo $supplier_arr[$row[csf("serving_company")]];  ?></p></td>
                        <?
					}
					?>
                    <td ><p><? echo $row[csf("challan_no")];  ?></p></td>
                    <td ><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
                    <td align="center"><p><? echo $sewing_line;;//$sewing_line_library[$row[csf("sewing_line")]];  ?></p></td>
                    <td ><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
                    <?
					$color_total_in=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
						 	echo number_format($production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id],0) ;
							 $color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $line_color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $line_color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
						 ?>
                        </p></td>
                        <?
                    }
                    ?>
                    <td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td >&nbsp;</td>
                <?
				if($prod_source==3)
				{
					?>
					<td >&nbsp;</td>
					<?
				}
				?>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >&nbsp;</th>
                <?
				if($prod_source==3)
				{
					?>
					<th >&nbsp;</th>
					<?
				}
				?>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?
}

if($action=="sewingQnty_location_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$sewing_line_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	//var_dump();die;

	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	if($db_type==2)
	{
		$sql= "SELECT  a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id , a.prod_reso_allo
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=5 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.location=$location and a.floor_id=$floor_id and a.sewing_line=$sewing_line and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, c.color_number_id, group_concat(c.size_number_id) as size_number_id , a.prod_reso_allo
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=5 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.location=$location and a.floor_id=$floor_id and a.sewing_line=$sewing_line and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.prod_reso_allo, c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;


	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);
	$sql_color_size= sql_select("SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=5 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.location=$location and a.floor_id=$floor_id and a.sewing_line=$sewing_line and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id
	order by a.country_id");
	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=630+$col_width;
	$summer_table_width=230+$col_width;


?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
		<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">
                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item :
				<?
                    $item_data="";
                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")]));
                    foreach($garments_item_arr as $item_id)
                    {
                        if($item_data!="") $item_data .=", ";
                        $item_data .=$garments_item[$item_id];
                    }
                    echo $item_data;
                ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?>
                <br />
                Summary
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			//var_dump($result);die;
			foreach($summery_data as $color_id=>$row)
			{
				//print_r($row);die;

				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><? echo $colorarr[$color_id];  ?></td>
                    <?
					$summry_color_total_in =0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $summry_color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">Details</td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Country Name</th>
                    <th width="80" rowspan="2">Source</th>
                    <th width="70" rowspan="2">Challan</th>
                    <th width="90" rowspan="2">Sewing Unit</th>
                    <th width="70" rowspan="2" >Sewing Line</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($result as $row)
			{
				if(!in_array($row[csf("sewing_line")],$temp_arr[$row[csf("country_id")]]))
				{
					$temp_arr[$row[csf("country_id")]][]=$row[csf("sewing_line")];
					if($k!=1)
					{
						?>
						<tr bgcolor="#CCCCCC">
							<td >&nbsp;</td>
							<td>&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<?
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
								<?
							}
							?>
							<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
						</tr>
						<?
						$line_color_size_in = $line_color_total_in ="";
					}
					$k++;
				}

				$sewing_line='';
				if($row[csf('prod_reso_allo')]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$sewing_line_library[$val]; else $sewing_line.=",".$sewing_line_library[$val];
					}
				}
				else $sewing_line=$sewing_line_library[$row[csf('sewing_line')]];

				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
                    <td ><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
                    <td ><p><? echo $row[csf("challan_no")];  ?></p></td>
                    <td ><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
                    <td align="center"><p><? echo $sewing_line;;//$sewing_line_library[$row[csf("sewing_line")]];  ?></p></td>
                    <td ><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
                    <?
					$color_total_in=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
						 	echo number_format($production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id],0) ;
							 $color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $line_color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $line_color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
						 ?>
                        </p></td>
                        <?
                    }
                    ?>
                    <td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?
}

if($action=="sewingQnty_input_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$sewing_line_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	//var_dump();die;

	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	//echo $po_details_sql;die;

	if($db_type==2)
	{
		$sql= "SELECT  a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id , a.prod_reso_allo
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, c.color_number_id, group_concat(c.size_number_id) as size_number_id , a.prod_reso_allo
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;


	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);

	$sql_color_size= sql_select("SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id
	order by a.country_id");
	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=630+$col_width;
	$summer_table_width=230+$col_width;


?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
		<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;  margin-top:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">
                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item :
				<?
                    $item_data="";
                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")]));
                    foreach($garments_item_arr as $item_id)
                    {
                        if($item_data!="") $item_data .=", ";
                        $item_data .=$garments_item[$item_id];
                    }
                    echo $item_data;
                ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?>
                <br />
                Summary
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			//var_dump($result);die;
			foreach($summery_data as $color_id=>$row)
			{
				//print_r($row);die;

				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><? echo $colorarr[$color_id];  ?></td>
                    <?
					$summry_color_total_in =0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $summry_color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">Details</td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Country Name</th>
                    <th width="80" rowspan="2">Source</th>
                    <th width="70" rowspan="2">Challan</th>
                    <th width="90" rowspan="2">Sewing Unit</th>
                    <th width="70" rowspan="2">Sewing Line</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($result as $row)
			{
				if(!in_array($row[csf("sewing_line")],$temp_arr[$row[csf("country_id")]]))
				{
					$temp_arr[$row[csf("country_id")]][]=$row[csf("sewing_line")];
					if($k!=1)
					{
						?>
						<tr bgcolor="#CCCCCC">
							<td >&nbsp;</td>
							<td>&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<?
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
								<?
							}
							?>
							<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
						</tr>
						<?
						$line_color_size_in = $line_color_total_in ="";
					}
					$k++;
				}

				$sewing_line='';
				if($row[csf('prod_reso_allo')]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$sewing_line_library[$val]; else $sewing_line.=",".$sewing_line_library[$val];
					}
				}
				else $sewing_line=$sewing_line_library[$row[csf('sewing_line')]];

				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
                    <td ><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
                    <td ><p><? echo $row[csf("challan_no")];  ?></p></td>
                    <td ><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>

                    <td align="center"><p><? echo $sewing_line;  ?></p></td>
                    <td ><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
                    <?
					$color_total_in=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
						 	echo number_format($production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id],0) ;
							 $color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $line_color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $line_color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
						 ?>
                        </p></td>
                        <?
                    }
                    ?>
                    <td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?
}

if($action=="sewingQnty_input_location_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$sewing_line_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	//var_dump();die;

	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	//echo $po_details_sql;die;

	if($db_type==2)
	{
		$sql= "SELECT  a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id , a.prod_reso_allo
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.location=$location and a.floor_id=$floor_id and a.sewing_line=$sewing_line and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, c.color_number_id, group_concat(c.size_number_id) as size_number_id , a.prod_reso_allo
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.location=$location and a.floor_id=$floor_id and a.sewing_line=$sewing_line and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;


	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);

	$sql_color_size= sql_select("SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.location=$location and a.floor_id=$floor_id and a.sewing_line=$sewing_line and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id
	order by a.country_id");
	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=630+$col_width;
	$summer_table_width=230+$col_width;


?>

<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
		<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;  margin-top:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">
                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item :
				<?
                    $item_data="";
                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")]));
                    foreach($garments_item_arr as $item_id)
                    {
                        if($item_data!="") $item_data .=", ";
                        $item_data .=$garments_item[$item_id];
                    }
                    echo $item_data;
                ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?>
                <br />
                Summary
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			//var_dump($result);die;
			foreach($summery_data as $color_id=>$row)
			{
				//print_r($row);die;

				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><? echo $colorarr[$color_id];  ?></td>
                    <?
					$summry_color_total_in =0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $summry_color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">Details</td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Country Name</th>
                    <th width="80" rowspan="2">Source</th>
                    <th width="70" rowspan="2">Challan</th>
                    <th width="90" rowspan="2">Sewing Unit</th>
                    <th width="70" rowspan="2">Sewing Line</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($result as $row)
			{
				if(!in_array($row[csf("sewing_line")],$temp_arr[$row[csf("country_id")]]))
				{
					$temp_arr[$row[csf("country_id")]][]=$row[csf("sewing_line")];
					if($k!=1)
					{
						?>
						<tr bgcolor="#CCCCCC">
							<td >&nbsp;</td>
							<td>&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<td >&nbsp;</td>
							<?
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
								<?
							}
							?>
							<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
						</tr>
						<?
						$line_color_size_in = $line_color_total_in ="";
					}
					$k++;
				}

				$sewing_line='';
				if($row[csf('prod_reso_allo')]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$sewing_line_library[$val]; else $sewing_line.=",".$sewing_line_library[$val];
					}
				}
				else $sewing_line=$sewing_line_library[$row[csf('sewing_line')]];

				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td ><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
                    <td ><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
                    <td ><p><? echo $row[csf("challan_no")];  ?></p></td>
                    <td ><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
                    <td align="center"><p><? echo $sewing_line;  ?></p></td>
                    <td ><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
                    <?
					$color_total_in=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
						 	echo number_format($production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id],0) ;
							 $color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $line_color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 $line_color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
						 ?>
                        </p></td>
                        <?
                    }
                    ?>
                    <td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?
}

if($action=="ironQnty_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$sewing_line_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	//var_dump();die;

	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	//echo $po_details_sql;die;

	if($db_type==2)
	{
		$sql= "SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id
		from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
		where a.production_type=7 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by  a.challan_no, a.floor_id, a.country_id, c.color_number_id
		order by a.country_id, a.challan_no, a.floor_id";
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, group_concat(c.size_number_id) as size_number_id
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=7 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by a.country_id,a.challan_no, a.floor_id, c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;
	if($prod_source!=0 || $prod_source!='' ) $prod_source_cond=" and production_source='$prod_source'";

	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);

	$sql_color_size= sql_select("SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, c.size_number_id, b.production_qnty as production_qnty
	from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
	where a.production_type=7 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $prod_source_cond ");

	foreach($sql_color_size as $row)
	{
		//$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['in'] +=$row[csf('in_quantity')];
		//$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['out'] +=$row[csf('out_quantity')];
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=630+$col_width;
	$summer_table_width=230+$col_width;


?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
    <div style="100%" id="report_container">
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">
                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item :
				<?
                    $item_data="";
                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")]));
                    foreach($garments_item_arr as $item_id)
                    {
                        if($item_data!="") $item_data .=", ";
                        $item_data .=$garments_item[$item_id];
                    }
                    echo $item_data;
                ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?>
                <br />
                Summary
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
					<?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			//var_dump($result);die;
			foreach($summery_data as $color_id=>$row)
			{
				//print_r($row);die;
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td><? echo $colorarr[$color_id];  ?></td>
                    <?
					$summry_color_total_in =0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th><? echo $summry_color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th><? echo $grand_tot_in; ?></th>
            </tfoot>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">Details</td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="3">SI</th>
                    <th width="100" rowspan="3">Country Name</th>
                    <th width="80" rowspan="3">Source</th>
                    <th width="70" rowspan="3">Challan</th>
                    <th width="70" rowspan="3">Floor</th>
                    <th width="100" rowspan="3">Color</th>
                    <? if ($prod_source==1) { $prod_source="In-House"; } else if ($prod_source==3) { $prod_source="Out-Bound"; }?>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>"><? echo $prod_source; ?></th>
                    <th width="80" rowspan="3" >Total</th>
                </tr>
                <tr>
                    <th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($result as $row)
			{
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
                    <td><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
                    <td><p><? echo $row[csf("challan_no")];  ?></p></td>
                    <td><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
                    <td><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
                    <?
					$color_total=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
							$production_break_qty_in=0;
							$production_break_qty_in=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$size_id];
						 	echo number_format($production_break_qty_in,0) ;

							 $color_total+= $production_break_qty_in;
							 $color_size_in [$size_id]+=$production_break_qty_in;
						 ?>
                        </p></td>
                        <?
                    }

                    ?>
                    <td align="right"><p><? echo  number_format( $color_total,0); $grand_tot_in+=$color_total; ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
                {
                    ?>
                    <th align="right"><? echo number_format($color_size_in[$size_id],0); ?></th>
                    <?
                }

				?>
                <th><? echo $grand_tot_in; ?></th>
            </tfoot>
        </table>
        </div>
    </fieldset>
<?
exit();
}


?>