<?php

date_default_timezone_set("Asia/Dhaka");
require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require_once('setting/mail_setting.php');

$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),0))),'','',1);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-15 day', strtotime($current_date))),'','',1); 
$prev_date2 = change_date_format(date('Y-m-d H:i:s', strtotime('-45 day', strtotime($current_date))),'','',1); 

    

$color_array = return_library_array("select id, color_name from lib_color", "id", "color_name");
$company_library=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name",$con);
$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
$buyer_short_name_library = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");



$report_format_arr = array(0=>"",1 => "show_fabric_booking_report_gr", 2 => "show_fabric_booking_report", 3 => "show_fabric_booking_report3", 4 => "show_fabric_booking_report1", 5 => "show_fabric_booking_report2", 6 => "show_fabric_booking_report4", 7 => "show_fabric_booking_report5", 8 => "show_fabric_booking_report", 9 => "show_fabric_booking_report3", 10 => "show_fabric_booking_report4", 28 => "show_fabric_booking_report_akh",46=>"show_fabric_booking_report_urmi",136=>"print_booking_3",244=>"show_fabric_booking_report_ntg",38=>"show_fabric_booking",39=>"show_fabric_booking_report2",64=>"show_fabric_booking_report3",84=>"show_fabric_booking_report_islam"); //8,9 for short
//--------------------------------------------------------------------------------------------------------------------



//$company_lib = array(17=>$company_library[17]);

foreach($company_library as $compid=>$compname)
{
    $cbo_type ='1';
    $txt_date_from = $prev_date2;
    $txt_date_to = $prev_date;
    $chk_no_boking = '1';
    $type = str_replace("'", "", $cbo_type);
    $company_name = $compid;
            
    if (str_replace("'", "", trim($txt_file_no)) != "")
        $file_no = " and LOWER(b.file_no) like LOWER('%" . str_replace("'", "", trim($txt_file_no)) . "%')";
    else
        $file_no = "";
    if (str_replace("'", "", trim($cbo_order_status)) != 0)
        $is_confirmed_cond = " and b.is_confirmed = '" . str_replace("'", "", trim($cbo_order_status)) . "'";
    else
        $is_confirmed_cond = "";
    $order_status = array(0 => "ALL", 1 => "Confirmed", 2 => "Projected");


    $txt_job_no = str_replace("'", "", $txt_job_no);
    $job_no_cond = "";
    if (trim($txt_job_no) != "") {
        $job_no = trim($txt_job_no);
        $job_no_cond = " and a.job_no_prefix_num=$job_no";
    }

    
    $txt_fab_color = str_replace("'", "", $txt_fab_color);
    if (trim($txt_fab_color) != "")
        $fab_color = "%" . trim($txt_fab_color) . "%";
    else
        $fab_color = "%%";


    if ($end_date_po == "")
        $end_date_po = $start_date_po;
    else
        $end_date_po = $end_date_po;

    
    if ($db_type == 0) {
        $str_cond_insert = " and b.insert_date between '" . $txt_date_from . "' and '" . $txt_date_to . " 23:59:59'";
    } else {
        $str_cond_insert = " and b.insert_date between '" . $txt_date_from . "' and '" . $txt_date_to . " 11:59:59 PM'";
    }


    if ($txt_fab_color == "") {
        $color_cond = "";
        $color_cond_prop = "";
    } 
    else 
    {
        if ($db_type == 0) {
            $color_id = return_field_value("group_concat(id) as color_id", "lib_color", "color_name like '$fab_color'", "color_id");
        } else {
            $color_id = return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as color_id", "lib_color", "color_name like '$fab_color'", "color_id");
        }
        if ($color_id == "") {
            $color_cond_search = "";
            $color_cond_prop = "";
        } else {
            $color_cond_search = " and b.fabric_color_id in ($color_id)";
            $color_cond_prop = " and color_id in ($color_id)";
        }
    }
    if($type==1) 
    {
        $table_width = "1550";
    } 
    else 
    {
            $table_width = "1450";
    }
    
    
    
    $sql="SELECT a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut, b.file_no, b.grouping, b.is_confirmed  
    from wo_po_details_master a, wo_po_break_down b 
    where a.id=b.job_id and a.company_name=$compid  $is_confirmed_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_no_cond $str_cond $str_cond_insert $file_no order by b.pub_shipment_date, a.job_no_prefix_num, b.id";


    // echo $sql;die();
    $nameArray=sql_select($sql); 
    $po_data_arr=array(); 
    $job_data_arr=array(); 
    $job_allData_arr=array(); 
    $job_arr=array(); 
    $tot_rows=0; 
    $poIds='';
    if(count($nameArray)>0)
    {
        foreach($nameArray as $row)
        {
            $tot_rows++;
            $poIds.=$row[csf("po_id")].",";
            $job_arr[$row[csf("job_no")]]=$row[csf("job_no")];
            if($type==1  || $type==3)
            {
                $po_data_arr[$row[csf("po_id")]]=$row[csf("company_name")]."##".$row[csf("buyer_name")]."##".$row[csf("job_no_prefix_num")]."##".$row[csf("job_no")]."##".$row[csf("style_ref_no")]."##".$row[csf("gmts_item_id")]."##".$row[csf("order_uom")]."##".$row[csf("ratio")]."##".$row[csf("po_number")]."##".$row[csf("po_qnty")]."##".$row[csf("pub_shipment_date")]."##".$row[csf("shiping_status")]."##".$row[csf("insert_date")]."##".$row[csf("po_received_date")]."##".$row[csf("plan_cut")]."##".$row[csf("file_no")]."##".$row[csf("grouping")]."##".$row[csf("is_confirmed")];
            }
            if($type==2)
            {
                $job_data_arr[$row[csf("job_no")]]=$row[csf("company_name")]."##".$row[csf("buyer_name")]."##".$row[csf("job_no_prefix_num")]."##".$row[csf("style_ref_no")]."##".$row[csf("gmts_item_id")]."##".$row[csf("order_uom")]."##".$row[csf("ratio")];
                
                $job_allData_arr[$row[csf("job_no")]].=$row[csf("grouping")]."**".$row[csf("file_no")]."**".$row[csf("po_number")]."**".$row[csf("po_qnty")]."**".$row[csf("pub_shipment_date")]."**".$row[csf("shiping_status")]."**".$row[csf("insert_date")]."**".$row[csf("po_received_date")]."**".$row[csf("plan_cut")]."**".$row[csf("is_confirmed")]."**".$row[csf("po_id")]."___";
            }
            //Array ( [15834] => 3##26##799##OG-16-00799##836447##1##1##1
        }
    }
    // else
    // {
    //     echo "3**".'Data Not Found'; die;
    // }
    unset($nameArray);
    
    /*$auto_store_arr=array();
    $auto_store_sql=sql_select("select item_category_id, auto_update from variable_settings_production where company_name ='$company_name' and variable_list = 15");
    
    foreach($auto_store_sql as $row)
    {
        $auto_store_arr[$row[csf("item_category_id")]]=$row[csf("auto_update")];
    }
    unset($auto_store_sql);*/
    
        
    
    //print_r($job_allData_arr);
    //die;
    
    $poIds=chop($poIds,','); 
    $yarn_iss_po_cond=""; $purchase_po_cond=""; $trans_po_cond=""; $batch_po_cond=""; $dye_po_cond=""; $wo_po_cond="";$yarn_po_cond="";
    
    //$yarn_allo_po_cond="";  $grey_delivery_po_cond=""; $fin_delivery_po_cond="";  $fin_purchase_po_cond=""; $po_color_po_cond=""; $cons_po_cond=""; $tna_po_cond="";
    if($db_type==2 && $tot_rows>1000)
    {
        $yarn_iss_po_cond=" and (";
        $purchase_po_cond=" and (";
        $trans_po_cond=" and (";
        $batch_po_cond=" and (";
        $dye_po_cond=" and (";
        $wo_po_cond=" and (";
        $yarn_po_cond=" and (";
        

        $poIdsArr=array_chunk(explode(",",$poIds),999);
        foreach($poIdsArr as $ids)
        {
            $ids=implode(",",$ids);
            $yarn_iss_po_cond.=" a.po_breakdown_id in($ids) or ";
            $purchase_po_cond.=" c.po_breakdown_id in($ids) or ";
            $trans_po_cond.=" po_breakdown_id in($ids) or ";
            $batch_po_cond.=" b.po_id in($ids) or ";
            $dye_po_cond.=" b.po_id in($ids) or ";
            $wo_po_cond.=" b.po_break_down_id in($ids) or ";
            $yarn_po_cond.=" b.id in($ids) or ";
        }
        
        $yarn_iss_po_cond=chop($yarn_iss_po_cond,'or ');
        $yarn_iss_po_cond.=")";
        
        $purchase_po_cond=chop($purchase_po_cond,'or ');
        $purchase_po_cond.=")";
        
        $trans_po_cond=chop($trans_po_cond,'or ');
        $trans_po_cond.=")";
        
        $batch_po_cond=chop($batch_po_cond,'or ');
        $batch_po_cond.=")";
        
        $dye_po_cond=chop($dye_po_cond,'or ');
        $dye_po_cond.=")";
        
        $wo_po_cond=chop($wo_po_cond,'or ');
        $wo_po_cond.=")";
        
        $yarn_po_cond=chop($yarn_po_cond,'or ');
        $yarn_po_cond.=")";
    }
    else
    {
        $yarn_iss_po_cond=" and a.po_breakdown_id in ($poIds)";
        $purchase_po_cond=" and c.po_breakdown_id in ($poIds)";
        $trans_po_cond=" and po_breakdown_id in ($poIds)";
        $batch_po_cond=" and b.po_id in ($poIds)";
        $dye_po_cond=" and b.po_id in ($poIds)";
        $wo_po_cond=" and b.po_break_down_id in ($poIds)";
        $yarn_po_cond=" and b.id in ($poIds)";
    }

    //============================ load library data =================================
    // print_r($job_arr);
    $jobNo = "'".implode("','", $job_arr)."'";
    if ($db_type == 0) 
    {
    $fabric_desc_details = return_library_array("select job_no, group_concat(distinct(fabric_description)) as fabric_description from wo_pre_cost_fabric_cost_dtls where job_no in($jobNo) group by job_no", "job_no", "fabric_description");
    } 
    else 
    {
        $fabric_desc_details = return_library_array("select job_no, LISTAGG(cast(fabric_description as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as fabric_description from wo_pre_cost_fabric_cost_dtls where job_no in($jobNo) group by job_no", "job_no", "fabric_description");
    }


    $batch_details = return_library_array("SELECT a.id, a.batch_no from pro_batch_create_mst a ,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $batch_po_cond", "id", "batch_no");

    $costing_per_id_library = array();
    $costing_date_library = array();
    $costing_sql = sql_select("select job_no, costing_per, costing_date from wo_pre_cost_mst where job_no in($jobNo)");
    foreach ($costing_sql as $row) {
        $costing_per_id_library[$row[csf('job_no')]] = $row[csf('costing_per')];
        $costing_date_library[$row[csf('job_no')]] = $row[csf('costing_date')];
    }    
        
    //----------------------------------------------------------------------    
    $poIdsArr=array_chunk(explode(",",$poIds),999);
    $p=1;
    foreach($poIdsArr as $ids)
    {

        if($p==1) $po_con .=" and (po_break_down_id in(".implode(',',$ids).")"; else  $po_con .=" OR po_break_down_id in(".implode(',',$ids).")";
        $p++;
    }
    $po_con .=")";
    $lapdipDataEc=sql_select("select job_no_mst, po_break_down_id, color_name_id, lapdip_no from wo_po_lapdip_approval_info where is_deleted=0 and status_active=1 $po_con");
    // echo "select job_no_mst, po_break_down_id, color_name_id, lapdip_no from wo_po_lapdip_approval_info where is_deleted=0 and status_active=1 $po_con";die();
    foreach($lapdipDataEc as $row)
    {
        $key=$row[csf('job_no_mst')].$row[csf('po_break_down_id')].$row[csf('color_name_id')];
        $lapdip_arr[$key]= $row[csf('lapdip_no')];
    }     
    unset($lapdipDataEc) ;
    //------------------------------------------------------------------------- 
    
    
    
    $booking_print_arr = array();
    $booking_print_sql = sql_select("select report_id, format_id from lib_report_template where template_name='$company_name' and module_id=2 and report_id in (1,2,3) and is_deleted=0 and status_active=1");
    foreach ($booking_print_sql as $print_id) 
    {
        $arr=explode(",",$print_id[csf('format_id')]);
        $cnt=count($arr);
        if($cnt>0){
            $cnt--;
            $booking_print_arr[$print_id[csf('report_id')]] = $arr[$cnt];
        }else{
            $booking_print_arr[$print_id[csf('report_id')]] =0;
        }
        //$booking_print_arr[$print_id[csf('report_id')]] = (int) $print_id[csf('format_id')];
    }
    // echo "<pre>";
    // print_r($booking_print_arr);
    // echo "</pre>";die;
    unset($booking_print_sql);

    $dataArrayYarn = array();
    $dataArrayYarnIssue = array();
    $yarn_sql = "SELECT a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id, sum(a.avg_cons_qnty) as qnty from wo_pre_cost_fab_yarn_cost_dtls a,wo_po_break_down b where a.job_id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $yarn_po_cond group by a.job_no, a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.type_id"; //, sum(cons_qnty) as qnty
    // echo $yarn_sql;die();
    $resultYarn = sql_select($yarn_sql);
    foreach ($resultYarn as $yarnRow) 
    {
        $dataArrayYarn[$yarnRow[csf('job_no')]] .= $yarnRow[csf('count_id')] . "**" . $yarnRow[csf('copm_one_id')] . "**" . $yarnRow[csf('percent_one')] . "**" . $yarnRow[csf('copm_two_id')] . "**" . $yarnRow[csf('percent_two')] . "**" . $yarnRow[csf('type_id')] . "**" . $yarnRow[csf('qnty')] . ",";
    }
    unset($resultYarn);

    $sql_yarn_iss = "select a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, 
    sum(CASE WHEN a.entry_form ='3' THEN a.quantity ELSE 0 END) AS issue_qnty,
    sum(CASE WHEN a.entry_form ='9' THEN a.quantity ELSE 0 END) AS return_qnty
    from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 $yarn_iss_po_cond group by a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
            // echo $sql_yarn_iss;die();
    $dataArrayIssue = sql_select($sql_yarn_iss);
    foreach ($dataArrayIssue as $row_yarn_iss) 
    {
        $dataArrayYarnIssue[$row_yarn_iss[csf('po_breakdown_id')]] .= $row_yarn_iss[csf('yarn_count_id')] . "**" . $row_yarn_iss[csf('yarn_comp_type1st')] . "**" . $row_yarn_iss[csf('yarn_comp_percent1st')] . "**" . $row_yarn_iss[csf('yarn_comp_type2nd')] . "**" . $row_yarn_iss[csf('yarn_comp_percent2nd')] . "**" . $row_yarn_iss[csf('yarn_type')] . "**" . $row_yarn_iss[csf('issue_qnty')] . "**" . $row_yarn_iss[csf('return_qnty')] . ",";
    }
    unset($dataArrayIssue); 
    
    $greyPurchaseQntyArray = array(); $finish_purchase_qnty_arr=array();
    $sql_grey_purchase="SELECT c.po_breakdown_id, c.quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (22,2,58) and c.entry_form in (22,2,58) and c.trans_id !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $purchase_po_cond"; // and entry_form 58 for roll recv
    // echo $sql_grey_purchase;die();
    
    $dataGreyArrayPurchase = sql_select($sql_grey_purchase);
    foreach ($dataGreyArrayPurchase as $purGreyRow)
    {
        $greyPurchaseQntyArray[$purGreyRow[csf('po_breakdown_id')]] +=$purGreyRow[csf('quantity')];
    }
    unset($dataGreyArrayPurchase);
    
    $sql_fin_purchase="select c.po_breakdown_id, c.color_id, sum(c.quantity) as finish_purchase from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=37 and c.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $purchase_po_cond group by c.po_breakdown_id, c.color_id";
            //and a.receive_basis<>9
    // echo $sql_fin_purchase;die();
    $dataArrayFinPurchase=sql_select($sql_fin_purchase);
    foreach($dataArrayFinPurchase as $finRow)
    {
        $finish_purchase_qnty_arr[$finRow[csf('po_breakdown_id')]][$finRow[csf('color_id')]]=$finRow[csf('finish_purchase')];
    }
    unset($dataArrayFinPurchase);
    
    $grey_receive_qnty_arr=array(); $grey_receive_return_qnty_arr=array(); $grey_issue_return_qnty_arr=array(); $grey_issue_qnty_arr=array(); $trans_qnty_arr=array();
    $trans_qnty_fin_arr=array(); $finish_receive_qnty_arr=array(); $finish_issue_qnty_arr=array(); $finish_recv_rtn_qnty_arr=array(); $finish_issue_rtn_qnty_arr=array();
    $grey_available_arr=array(); $finish_available_arr=array();
    $dataArrayTrans = sql_select("select trans_id, po_breakdown_id, color_id, entry_form, trans_type, quantity from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in (2,7,11,13,14,15,16,18,37,45,46,51,52,61,66,71,82,83,84,134) $trans_po_cond");
    foreach ($dataArrayTrans as $row) 
    {
        //knit
        if($row[csf('entry_form')]==2 || $row[csf('entry_form')]==45 && $row[csf('trans_type')]==3 || $row[csf('entry_form')]==51 && $row[csf('trans_type')]==4 || $row[csf('entry_form')]==16 || $row[csf('entry_form')]==61 || $row[csf('entry_form')]==11 || $row[csf('entry_form')]==13 || $row[csf('entry_form')]==82 || $row[csf('entry_form')]==83)
        {
            if($row[csf('entry_form')]==2)  $grey_receive_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
            if($row[csf('entry_form')]==45 && $row[csf('trans_type')]==3) $grey_receive_return_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
            if(($row[csf('entry_form')]==51 || $row[csf('entry_form')]==84) && $row[csf('trans_type')]==4) $grey_issue_return_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
            if($row[csf('entry_form')]==16 || $row[csf('entry_form')]==61) $grey_issue_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('quantity')];
            
            if($row[csf('entry_form')]==11) 
            {
                if($row[csf('trans_type')]==5 || $row[csf('trans_type')]==6)
                {
                    $trans_qnty_arr[$row[csf('po_breakdown_id')]]['yarn_trans'] += $row[csf('quantity')];
                }
            }
            $grey_trns_out=0; $grey_trns_in=0;
            if($row[csf('entry_form')]==13 || $row[csf('entry_form')]==82 || $row[csf('entry_form')]==83) 
            {
                /*if($row[csf('trans_type')]==5 || $row[csf('trans_type')]==6)
                {
                    $trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans'] += $row[csf('quantity')];
                }*/
                
                if($row[csf('trans_type')]==5)
                {
                    $trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans'] += $row[csf('quantity')];
                    if($row[csf('trans_id')]!=0) $grey_trns_in=$row[csf('quantity')];
                }
                if($row[csf('trans_type')]==6)
                {
                    $trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans'] -= $row[csf('quantity')];
                    if($row[csf('trans_id')]!=0) $grey_trns_out=$row[csf('quantity')];
                }              
            }
            
            $knit_avail=0; $grey_rec=0; $grey_purchase=0; $grey_rec_return=0; $net_grey_trns=0;
            if($row[csf('trans_id')]!=0) 
            {
                if($row[csf('entry_form')]==2) $grey_rec= $row[csf('quantity')];
                if($row[csf('entry_form')]==45 && $row[csf('trans_type')]==3) $grey_rec_return= $row[csf('quantity')];
                $grey_purchase=$greyPurchaseQntyArray[$row[csf('po_breakdown_id')]]-$grey_rec_return;
            }
            $net_grey_trns=$grey_trns_in-$grey_trns_out;
            $knit_avail=$grey_rec+$grey_purchase+$net_grey_trns;
            $grey_available_arr[$row[csf('po_breakdown_id')]]+=$knit_avail;
        }
        //finish
        if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==14 || $row[csf('entry_form')]==15 || $row[csf('entry_form')]==134 || $row[csf('entry_form')]==66 || $row[csf('entry_form')]==18 || $row[csf('entry_form')]==71 || ($row[csf('entry_form')]==46 && $row[csf('trans_type')]==3) || ($row[csf('entry_form')]==52 && $row[csf('trans_type')]==4) || $row[csf('entry_form')]==37)
        {
            $finish_trns_out=0; $finish_trns_in=0;
            if($row[csf('entry_form')]==14 || $row[csf('entry_form')]==15 || $row[csf('entry_form')]==134) 
            {
                
                if($row[csf('trans_type')]==5)
                {
                    $trans_qnty_fin_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['trans']+= $row[csf('quantity')];
                    if($row[csf('trans_id')]!=0) $finish_trns_in=$row[csf('quantity')];
                }
                if($row[csf('trans_type')]==6)
                {
                    $trans_qnty_fin_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['trans']-= $row[csf('quantity')];
                    if($row[csf('trans_id')]!=0) $finish_trns_out=$row[csf('quantity')];
                }
            }
            
            if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==66) $finish_receive_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] +=$row[csf('quantity')];
            if($row[csf('entry_form')]==18 || $row[csf('entry_form')]==71) $finish_issue_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] +=$row[csf('quantity')];
            if($row[csf('entry_form')]==46 && $row[csf('trans_type')]==3) $finish_recv_rtn_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] +=$row[csf('quantity')];
            if($row[csf('entry_form')]==52 && $row[csf('trans_type')]==4) $finish_issue_rtn_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] +=$row[csf('quantity')];
            $finish_avail=0; $finish_rec=0; $finish_purchase=0; $finish_rec_return=0; $net_finish_trns=0;
            if($row[csf('trans_id')]!=0) 
            {
                if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==37) 
                {
                    $finish_rec= $row[csf('quantity')];
                    if($row[csf('entry_form')]==7)
                    {
                        $finish_purchase_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] +=$row[csf('quantity')];
                    }
                }
                if($row[csf('entry_form')]==46 && $row[csf('trans_type')]==3) $finish_rec_return= $row[csf('quantity')];
            }

            $net_finish_trns=$finish_trns_in-$finish_trns_out;

            $finish_avail=$finish_rec+$net_finish_trns - $finish_rec_return;
            $finish_available_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]+=$finish_avail;
            //finish color arr

            if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==18 || $row[csf('entry_form')]==37)
            {
                $po_color_arr[$row[csf('po_breakdown_id')]].=$row[csf('color_id')].',';
            }
        }
    }
    unset($dataArrayTrans);
    // echo "**<pre>";print_r($trans_qnty_arr);die;



    $batch_qnty_arr = return_library_array("select b.po_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.batch_against<>2 and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_po_cond group by b.po_id", "po_id", "batch_qnty");

    $dye_qnty_arr = array();
    $sql_dye = "select b.po_id, a.color_id, sum(b.batch_qnty) as dye_qnty from pro_batch_create_mst a, pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $dye_po_cond group by b.po_id, a.color_id";
    $resultDye = sql_select($sql_dye);
    foreach ($resultDye as $dyeRow) {
        $dye_qnty_arr[$dyeRow[csf('po_id')]][$dyeRow[csf('color_id')]] = $dyeRow[csf('dye_qnty')];
    }
    unset($resultDye);

    $dataArrayWo = array();
    
    $sql_wo = "SELECT b.po_break_down_id, a.id, a.booking_no, a.booking_no_prefix_num, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $color_cond_search $wo_po_cond group by b.po_break_down_id, a.id, a.booking_no, a.booking_no_prefix_num, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id";
    $resultWo = sql_select($sql_wo);
    foreach ($resultWo as $woRow) 
    {
        $dataArrayWo[$woRow[csf('po_break_down_id')]] .= $woRow[csf('id')] . "**" . $woRow[csf('booking_no')] . "**" . $woRow[csf('insert_date')] . "**" . $woRow[csf('item_category')] . "**" . $woRow[csf('fabric_source')] . "**" . $woRow[csf('company_id')] . "**" . $woRow[csf('booking_type')] . "**" . $woRow[csf('booking_no_prefix_num')] . "**" . $woRow[csf('job_no')] . "**" . $woRow[csf('is_short')] . "**" . $woRow[csf('is_approved')] . "**" . $woRow[csf('fabric_color_id')] . "**" . $woRow[csf('req_qnty')] . "**" . $woRow[csf('grey_req_qnty')] . "**" . $woRow[csf('booking_no_prefix_num')] . ",";
        //$colorWiseReqQty_arr[$woRow[csf('po_break_down_id')]][$woRow[csf('fabric_color_id')]] += $woRow[csf('grey_req_qnty')];
        //$colorWiseReqQty_arr[$woRow[csf('po_break_down_id')]][$woRow[csf('fabric_color_id')]] += $woRow[csf('req_qnty')];
    }

    unset($resultWo);
    $tot_order_qnty = 0;
    $tot_mkt_required = 0;
    $tot_yarn_issue_qnty = 0;
    $tot_balance = 0;
    $tot_fabric_req = 0;
    $tot_grey_recv_qnty = 0;
    $tot_grey_balance = 0;
    $tot_grey_available = 0;
    $tot_grey_issue = 0;
    $tot_batch_qnty = 0;
    $tot_color_wise_req = 0;
    $tot_dye_qnty = 0;
    $tot_fabric_recv = 0;
    $tot_fabric_purchase = 0;
    $tot_fabric_balance = 0;
    $tot_issue_to_cut_qnty = 0;
    $tot_fabric_available = 0;
    $tot_fabric_left_over = 0;
    $tot_knit_balance_qnty = 0;
    $tot_grey_inhand = 0;
    $tot_grey_issue_bl = 0;

    $buyer_name_array = array();
    $order_qty_array = array();
    $grey_required_array = array();
    $yarn_issue_array = array();
    $grey_issue_array = array();
    $fin_fab_Requi_array = array();
    $fin_fab_recei_array = array();
    $issue_to_cut_array = array();
    $yarn_balance_array = array();
    $grey_balance_array = array();
    $fin_balance_array = array();
    $knitted_array = array();
    $dye_qnty_array = array();
    $batch_qnty_array = array();

    //echo $sql;die;
    $template_id_arr = return_library_array("select po_number_id, template_id from tna_process_mst group by po_number_id, template_id", "po_number_id", "template_id");

    ob_start();
    ?>
    <fieldset style="width:<? echo $table_width + 20; ?>px;">   
        <table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?>">
            <tr>
                <td align="center" width="100%" colspan="<? //echo $colspan + 29; ?>" style="font-size:16px"><strong><?php echo $company_library[$company_name]; ?></strong></td>
            </tr>
            <!-- <tr>
                <td align="center" width="100%" colspan="<? //echo $colspan + 29; ?>" style="font-size:16px"><strong><? if ($start_date != "" && $end_date != "") echo "From " . change_date_format($start_date) . " To " . change_date_format($end_date); ?></strong></td>
            </tr> -->
        </table>
        <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header_1">
            <thead>
                <tr>
                    <th colspan="11">Order Details</th>
                    <th colspan="4">Yarn Status</th>
                </tr>
                <tr>
                    <th width="40">SL</th>
                    <th width="100">Job Number</th>
                    <th width="120">Order Number</th>
                    <?
                    if ($type == 1) 
                    {
                        echo '<th width="100">Order Status</th>';
                    }
                    ?>
                    <th width="80">Buyer Name</th>
                    <th width="130">Style Ref.</th>
                    
                    
                    <th width="140">Item Name</th>
                    <th width="100">Order Qnty</th>
                    <th width="80">Shipment Date</th>
                    <?
                    if ($type == 1) 
                    {
                        ?>
                        <th width="80">PO Received Date</th>
                        <th width="80">PO Entry Date</th>
                        <?
                    }
                    ?>
                    <th width="70">Count</th>
                    <th width="110">Composition</th>
                    <th width="80">Type</th>
                    <th width="100">Required<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                </tr>
            </thead>
        </table>
        
        <!-- <div style="width:<? echo $table_width + 20; ?>px; overflow-y:scroll; max-height:500px" id="scroll_body"> -->
        <div style="width:<? echo $table_width + 20; ?>px; " >
            <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                <?
                //echo 3333333;die;
                
                $nameArray = sql_select($sql);
                

                $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
                $gmtsitemRatioArray = array();
                $gmtsitemRatioSql=sql_select('SELECT a.job_no AS JOB_NO,b.gmts_item_id AS GMTS_ITEM_ID ,b.set_item_ratio AS SET_ITEM_RATIO from wo_po_details_master a, wo_po_details_mas_set_details b where 1=1 and a.id=b.job_id');
                foreach($gmtsitemRatioSql as $gmtsitemRatioSqlRow)
                {
                    $gmtsitemRatioArray[$gmtsitemRatioSqlRow['JOB_NO']][$gmtsitemRatioSqlRow['GMTS_ITEM_ID']]=$gmtsitemRatioSqlRow['SET_ITEM_RATIO'];    
                }
                unset($gmtsitemRatioSql);

                $budget_cond = " and a.company_name=$compid";
                $budget_cond .= (str_replace("'", "", $txt_file_no) != '') ? " and b.file_no=$txt_file_no" : "";
                $budget_cond .= (str_replace("'", "", $txt_ref_no) != '') ? " and b.grouping=$txt_ref_no" : "";
                $budget_cond .= (str_replace("'", "", $txt_job_no) != '') ? " and a.job_no_prefix_num=$txt_job_no" : "";
                if ($start_date != "" && $end_date != "") {
                    $budget_cond .= " and b.pub_shipment_date between '$start_date' and '$end_date'";
                }

                $sql_budget_data = "SELECT a.job_no AS JOB_NO ,b.id AS ID,c.item_number_id AS ITEM_NUMBER_ID,c.country_id AS COUNTRY_ID,c.color_number_id AS COLOR_NUMBER_ID,c.size_number_id AS SIZE_NUMBER_ID,c.order_quantity AS ORDER_QUANTITY,c.plan_cut_qnty AS PLAN_CUT_QNTY,c.country_ship_date AS COUNTRY_SHIP_DATE,d.id AS PRE_COST_DTLS_ID,d.fab_nature_id AS FAB_NATURE_ID,d.construction AS CONSTRUCTION, d.gsm_weight AS GSM_WEIGHT,e.cons AS CONS,e.requirment AS REQUIRMENT,f.id AS YARN_ID,f.count_id AS COUNT_ID,f.copm_one_id AS COPM_ONE_ID,f.percent_one AS PERCENT_ONE,f.type_id AS TYPE_ID,f.color AS COLOR,f.cons_ratio AS CONS_RATIO,f.cons_qnty AS CONS_QNTY,f.avg_cons_qnty AS AVG_CONS_QNTY,f.rate AS RATE,f.amount AS AMOUNT from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where 1=1 and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 $budget_cond";
                $budget_res = sql_select($sql_budget_data);
                $yarn_des_data = array();
                foreach ($budget_res as $val) 
                {
                    $costingPer = $costing_per_arr[$val['JOB_NO']];
                    if($costingPer==1) $pcs_value=1*12;
                    else if($costingPer==2) $pcs_value=1*1;
                    else if($costingPer==3) $pcs_value=2*12;
                    else if($costingPer==4) $pcs_value=3*12;
                    else if($costingPer==5) $pcs_value=4*12;

                    $gmtsitemRatio  = $gmtsitemRatioArray[$val['JOB_NO']][$val['ITEM_NUMBER_ID']];
                    $consRatio      = $val['CONS_RATIO'];
                    $requirment     = $val['REQUIRMENT'];
                    $consQnty       = $requirment*$consRatio/100;
                    $reqQty         = ($val['PLAN_CUT_QNTY']/$gmtsitemRatio)*($consQnty/$pcs_value); 
                    $yarn_des_data[$val['ID']][$val['COUNT_ID']][$val['COPM_ONE_ID']][$val['PERCENT_ONE']][$val['TYPE_ID']] += $reqQty;
                }

                $k = 1;
                $i = 1;
                
                
                
                if ($type == 1) 
                {
                   

                    if ($chk_no_boking == 1) // Not Found
                    {   // check no booking 
                        foreach ($po_data_arr as $po_id=>$po_data) 
                        {
                            $nobooking_check = array_filter(explode(",", substr($dataArrayWo[$po_id], 0, -1)));
                            if (count($nobooking_check) < 1) {
                                $template_id = $template_id_arr[$po_id];
                                $ex_data=explode("##",$po_data);
                                $company_id=$ex_data[0];
                                $buyer_name=$ex_data[1];
                                $job_no_prefix_num=$ex_data[2];
                                $job_no=$ex_data[3];
                                $style_ref_no=$ex_data[4];
                                $gmts_item_id=$ex_data[5];
                                $order_uom=$ex_data[6];
                                $ratio=$ex_data[7];
                                $po_number=$ex_data[8];
                                $po_qnty=$ex_data[9];
                                $pub_shipment_date=$ex_data[10];
                                $shiping_status=$ex_data[11];
                                $insert_date=$ex_data[12];
                                $po_received_date=$ex_data[13];
                                $plan_cut=$ex_data[14];
                                $grouping=$ex_data[16];
                                $file_no=$ex_data[15];
                                $is_confirmed=$ex_data[17];
                                
                                $template_id=$template_id_arr[$po_id];
                                
                                $order_qnty_in_pcs=$po_qnty*$ratio;
                                $plan_cut_qnty=$plan_cut*$ratio;
                                $order_qty_array[$buyer_name]+=$order_qnty_in_pcs;
                                
                                $gmts_item='';
                                $gmts_item_id=explode(",",$gmts_item_id);
                                foreach($gmts_item_id as $item_id)
                                {
                                    if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
                                }
                                

                                $dzn_qnty = 0;
                                $balance = 0;
                                $job_mkt_required = 0;
                                $yarn_issued = 0;
                                if ($costing_per_id_library[$job_no] == 1)
                                    $dzn_qnty = 12;
                                else if ($costing_per_id_library[$job_no] == 3)
                                    $dzn_qnty = 12 * 2;
                                else if ($costing_per_id_library[$job_no] == 4)
                                    $dzn_qnty = 12 * 3;
                                else if ($costing_per_id_library[$job_no] == 5)
                                    $dzn_qnty = 12 * 4;
                                else
                                    $dzn_qnty = 1;

                                $dzn_qnty = $dzn_qnty * $ratio;

                                $yarn_data_array = array();
                                $mkt_required_array = array();
                                $yarn_desc_array_for_popup = array();
                                $yarn_desc_array = array();
                                $yarn_iss_qnty_array = array();
                                $s = 1;
                                
                                $yarn_descrip_data = $yarn_des_data[$po_id];

                                $qnty = 0;
                                foreach ($yarn_descrip_data as $count => $count_value) {
                                    foreach ($count_value as $Composition => $composition_value) {
                                        foreach ($composition_value as $percent => $percent_value) {
                                            foreach ($percent_value as $typee => $type_value) {
                                                //$yarnRow=explode("**",$yarnRow);
                                                $count_id = $count; //$yarnRow[0];
                                                $copm_one_id = $Composition; //$yarnRow[1];
                                                $percent_one = $percent; //$yarnRow[2];
                                                $copm_two_id = 0;
                                                $percent_two = 0;
                                                $type_id = $typee; //$yarnRow[5];
                                                $qnty = $type_value; //$yarnRow[6];

                                                $mkt_required = $qnty; //$plan_cut_qnty*($qnty/$dzn_qnty);
                                                $mkt_required_array[$s] = $mkt_required;
                                                $job_mkt_required += $mkt_required;

                                                $yarn_data_array['count'][$s] = $yarn_count_details[$count_id];
                                                $yarn_data_array['type'][$s] = $yarn_type[$type_id];

                                                /* if($percent_two!=0)
                                                    {
                                                    $compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]." ".$percent_two." %";
                                                    }
                                                    else
                                                    {
                                                    $compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
                                                    } */
                                                $compos = $composition[$copm_one_id] . " " . $percent_one . " %" . " " . $composition[$copm_two_id];

                                                $yarn_data_array['comp'][] = $compos;

                                                $yarn_desc_array[$s] = $yarn_count_details[$count_id] . " " . $compos . " " . $yarn_type[$type_id];

                                                $yarn_desc_for_popup = $count_id . "__" . $copm_one_id . "__" . $percent_one . "__" . $copm_two_id . "__" . $percent_two . "__" . $type_id;
                                                $yarn_desc_array_for_popup[$s] = $yarn_desc_for_popup;

                                                $s++;
                                            }
                                        }
                                    }
                                }

                                $dataYarnIssue = explode(",", substr($dataArrayYarnIssue[$po_id], 0, -1));
                                foreach ($dataYarnIssue as $yarnIssueRow) {
                                    $yarnIssueRow = explode("**", $yarnIssueRow);
                                    $yarn_count_id = $yarnIssueRow[0];
                                    $yarn_comp_type1st = $yarnIssueRow[1];
                                    $yarn_comp_percent1st = $yarnIssueRow[2];
                                    $yarn_comp_type2nd = $yarnIssueRow[3];
                                    $yarn_comp_percent2nd = $yarnIssueRow[4];
                                    $yarn_type_id = $yarnIssueRow[5];
                                    $issue_qnty = $yarnIssueRow[6];
                                    $return_qnty = $yarnIssueRow[7];

                                    if ($yarn_comp_percent2nd != 0) {
                                        $compostion_not_req = $composition[$yarn_comp_type1st] . " " . $yarn_comp_percent1st . " % " . $composition[$yarn_comp_type2nd] . " " . $yarn_comp_percent2nd . " %";
                                    } else {
                                        $compostion_not_req = $composition[$yarn_comp_type1st] . " " . $yarn_comp_percent1st . " % " . $composition[$yarn_comp_type2nd];
                                    }

                                    $desc = $yarn_count_details[$yarn_count_id] . " " . $compostion_not_req . " " . $yarn_type[$yarn_type_id];

                                    $net_issue_qnty = $issue_qnty - $return_qnty;
                                    $yarn_issued += $net_issue_qnty;
                                    if (!in_array($desc, $yarn_desc_array)) {
                                        $yarn_iss_qnty_array['not_req'] += $net_issue_qnty;
                                    } else {
                                        $yarn_iss_qnty_array[$desc] += $net_issue_qnty;
                                    }
                                }

                                $grey_purchase_qnty = $greyPurchaseQntyArray[$po_id] - $grey_receive_return_qnty_arr[$po_id];
                                $grey_recv_qnty = $grey_receive_qnty_arr[$po_id];
                                $grey_fabric_issue = $grey_issue_qnty_arr[$po_id] - $grey_issue_return_qnty_arr[$po_id];

                                if (($cbo_discrepancy == 1 && $grey_recv_qnty > $yarn_issued) || ($cbo_discrepancy == 0)) {
                                    if ($i % 2 == 0)
                                        $bgcolor = "#E9F3FF";
                                    else
                                        $bgcolor = "#FFFFFF";
                                    $buyer_name_array[$buyer_name] = $buyer_short_name_library[$buyer_name];

                                    $booking_array = array();
                                    $color_data_array = array();  //$colorGreyReqQty_arr = array();
                                    $required_qnty = 0;
                                    $req_purc_qnty = 0;
                                    $main_booking = '';
                                    $sample_booking = '';
                                    $main_booking_excel = '';
                                    $sample_booking_excel = '';
                                    $all_book_prefix_no = '';
                                    $dataArray = array_filter(explode(",", substr($dataArrayWo[$po_id], 0, -1)));
                                    if (count($dataArray) > 0) {
                                        foreach ($dataArray as $woRow) {
                                            $woRow = explode("**", $woRow);
                                            $id = $woRow[0];
                                            $booking_no = $woRow[1];
                                            $insert_date = $woRow[2];
                                            $item_category = $woRow[3];
                                            $fabric_source = $woRow[4];
                                            $company_id = $woRow[5];
                                            $booking_type = $woRow[6];
                                            $booking_no_prefix_num = $woRow[7];
                                            $job_no = $woRow[8];
                                            $is_short = $woRow[9];
                                            $is_approved = $woRow[10];
                                            $fabric_color_id = $woRow[11];
                                            $req_qnty = $woRow[12];
                                            $grey_req_qnty = $woRow[13];
                                            $book_prefix_no = $woRow[14];

                                            $required_qnty += $grey_req_qnty;
                                            if ($fabric_source == 2) {
                                                $req_purc_qnty += $grey_req_qnty;
                                            }

                                            if (!in_array($id, $booking_array)) {
                                                $system_date = date('d-M-Y', strtotime($insert_date));

                                                if ($fabric_source == 2)
                                                    $wo_color = "color='color:#000'";
                                                else
                                                    $wo_color = "";

                                                $action_name = '';
                                                if ($booking_type == 4) {
                                                    $action_name = 'show_fabric_booking_report';
                                                    $sample_booking .= "<a href='##' style='color:#000' onclick=\"generate_worder_report('3','" . $booking_no . "','" . $company_id . "','" . $po_id . "','" . $item_category . "','" . $fabric_source . "','" . $job_no . "','" . $is_approved . "','" . $action_name . "')\"><font style='font-weight:bold' $wo_color>" . $booking_no . "(" . $system_date . ")" . "</font></a><br>";
                                                    $sample_booking_excel .= "<font style='font-weight:bold' $wo_color>" . $booking_no . "(" . $system_date . ")" . "</font><br>";
                                                } else {
                                                    $all_book_prefix_no .= $book_prefix_no . ",";
                                                    if ($is_short == 1) {
                                                        $pre = "S";
                                                        $action_name = $report_format_arr[$booking_print_arr[2]];
                                                    } else {
                                                        $pre = "M";
                                                        $action_name = $report_format_arr[$booking_print_arr[1]];
                                                    }

                                                    if ($action_name == '')
                                                        $action_name = 'show_fabric_booking_report';

                                                    $main_booking .= "<a href='##' style='color:#000' onclick=\"generate_worder_report('" . $is_short . "','" . $booking_no . "','" . $company_id . "','" . $po_id . "','" . $item_category . "','" . $fabric_source . "','" . $job_no . "','" . $is_approved . "','" . $action_name . "')\"><font style='font-weight:bold' $wo_color>" . $booking_no . "(" . $system_date . ")" . $pre . "</font></a><br>";
                                                    $main_booking_excel .= "<font style='font-weight:bold' $wo_color>" . $booking_no . "(" . $system_date . ")" . $pre . "</font><br>";
                                                }

                                                $booking_array[] = $id;
                                            }
                                            $color_data_array[$fabric_color_id] += $req_qnty;
                                            //$colorGreyReqQty_arr[$fabric_color_id] += $grey_req_qnty;
                                        }
                                    }
                                    else {
                                        $main_booking .= "No Booking";
                                        $main_booking_excel .= "No Booking";
                                        $sample_booking .= "No Booking";
                                        $sample_booking_excel .= "No Booking";
                                        $all_book_prefix_no = "&nbsp;";
                                    }
                                    

                                    if ($main_booking == "") {
                                        $main_booking .= "No Booking";
                                        $main_booking_excel .= "No Booking";
                                    }

                                    if ($sample_booking == "") {
                                        $sample_booking .= "No Booking";
                                        $sample_booking_excel .= "No Booking";
                                    }
                                    $all_book_prefix_no = implode(",", array_unique(explode(",", chop($all_book_prefix_no, ","))));
                                    $finish_color = array_unique(explode(",", $po_color_arr[$po_id]));
                                    foreach ($finish_color as $color_id) {
                                        if ($color_id > 0) {
                                            $color_data_array[$color_id] += 0;
                                        }
                                    }

                                    $yarn_issue_array[$buyer_name] += $yarn_issued;

                                    $grey_required_array[$buyer_name] += $required_qnty;

                                    $net_trans_yarn = $trans_qnty_arr[$po_id]['yarn_trans'];
                                    $yarn_issue_array[$buyer_name] += $net_trans_yarn;

                                    $balance = $required_qnty - ($yarn_issued + $net_trans_yarn + $req_purc_qnty);

                                    $yarn_balance_array[$buyer_name] += $balance;

                                    $knitted_array[$buyer_name] += $grey_recv_qnty + $grey_purchase_qnty;
                                    $net_trans_knit = $trans_qnty_arr[$po_id]['knit_trans'];
                                    $knitted_array[$buyer_name] += $net_trans_knit;

                                    $grey_balance = $required_qnty - ($grey_recv_qnty + $net_trans_knit + $grey_purchase_qnty);

                                    $grey_balance_array[$buyer_name] += $grey_balance;
                                    $grey_issue_array[$buyer_name] += $grey_fabric_issue;

                                    $batch_qnty = $batch_qnty_arr[$po_id];
                                    $batch_qnty_array[$buyer_name] += $batch_qnty;

                                    $grey_available = $grey_recv_qnty + $grey_purchase_qnty + $net_trans_knit;

                                    $knit_balance_qnty = $required_qnty - ($grey_recv_qnty + $req_purc_qnty);
                                    $grey_inhand_qnty = $grey_available - $grey_fabric_issue;
                                    $grey_iss_balance_qnty = $required_qnty - $grey_fabric_issue;

                                    $tot_order_qnty += $order_qnty_in_pcs;
                                    $tot_mkt_required += $job_mkt_required;
                                    $tot_yarn_issue_qnty += $yarn_issued;
                                    $tot_fabric_req += $required_qnty;
                                    $tot_balance += $balance;
                                    $tot_grey_recv_qnty += $grey_recv_qnty;
                                    $tot_knit_balance_qnty += $knit_balance_qnty;
                                    $tot_grey_purchase_qnty += $grey_purchase_qnty;
                                    $tot_grey_available += $grey_available;
                                    $tot_grey_balance += $grey_balance;
                                    $tot_grey_issue += $grey_fabric_issue;
                                    $tot_grey_inhand += $grey_inhand_qnty;
                                    $tot_grey_issue_bl += $grey_iss_balance_qnty;
                                    $tot_batch_qnty += $batch_qnty;

                                    if ($required_qnty > $job_mkt_required)
                                        $bgcolor_grey_td = '#FF0000';
                                    $bgcolor_grey_td = '';

                                    // $po_entry_date = date('d-m-Y', strtotime($row[csf('insert_date')]));
                                    $po_entry_date = date('d-m-Y', strtotime($insert_date));
                                    $costing_date = $costing_date_library[$job_no];
                                    $tot_color = count($color_data_array);
                                    
                                    //15.03.2020 added by zaman
                                    $bgColorArr['color'] = $discrepancy_td_color;
                                    $bgColorArr['req_qty'] = $required_qnty;
                                    $bgColorArr['rcv_qty'] = $grey_recv_qnty;
                                    //$discrepancy_td_color_zs = getBgColorZs($bgColorArr);
                                    //end

                                    if ($tot_color > 0) 
                                    {
                                        $z = 1;
                                        foreach ($color_data_array as $key => $value) {
                                            if ($z == 1) {
                                                $display_font_color = "";
                                                $font_end = "";
                                            } else {
                                                $display_font_color = "<font style='display:none' color='$bgcolor'>";
                                                $font_end = "</font>";
                                            }

                                            if ($z == 1) {
                                                $html .= "<tr bgcolor='" . $bgcolor . "'>
                                                    <td align='left'>" . $i . "</td>
                                                    <td align='left'>" . $main_booking_excel . "</td>
                                                    <td align='left'>" . $sample_booking_excel . "</td>
                                                    <td align='center'>" . $job_no . "</td>
                                                    <td align='left'>" .$po_number . "</td>
                                                    <td>" . $buyer_short_name_library[$buyer_name] . "</td>
                                                    <td align='left'>" . $style_ref_no . "</td>
                                                    
                                                    <td align='left'>" . $gmts_item . "</td>
                                                    <td align='right'>" . $order_qnty_in_pcs . "</td>
                                                    <td align='left'>" . change_date_format($pub_shipment_date) . "</td>
                                                    <td align='center'>" . change_date_format($po_received_date) . "</td>
                                                    <td align='center'>" . $po_entry_date . "</td>";

                                                $html_short .= "<tr bgcolor='" . $bgcolor . "'>
                                                        <td align='left'>" . $i . "</td>
                                                        <td align='left'>" . $main_booking_excel . "</td>
                                                        <td align='left'>" . $sample_booking_excel . "</td>
                                                        <td align='left'>" .$po_number. "</td>
                                                        <td>" . $buyer_short_name_library[$buyer_name] . "</td>
                                                        <td align='right'>" . $order_qnty_in_pcs . "</td>
                                                        <td align='left'>" . change_date_format($pub_shipment_date) . "</td>";
                                            } else {
                                                $html .= "<tr bgcolor='" . $bgcolor . "'>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>";

                                                $html_short .= "<tr bgcolor='" . $bgcolor . "'>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>";
                                            }
                                            ?>
                                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $k; ?>">
                                                <td width="40"><? echo $display_font_color . $i . $font_end; ?></td>
                                                
                                                <td width="100" align="center"><p><? echo $display_font_color . $job_no . $font_end; ?></p></td>
                                                <td width="120">
                                                    <p>
                                                        <!-- <a href='#report_details' onClick="progress_comment_popup('<? echo $job_no; ?>', '<? echo $po_id; ?>', '<? echo $template_id; ?>', '<? echo $tna_process_type; ?>');"><? echo $display_font_color .$po_number. $font_end; ?></a> -->
                                                        <? echo $po_number; ?>
                                                    </p>
                                                </td>
                                                <td width="100"><p><? echo $display_font_color . $order_status[$is_confirmed] . $font_end ?></p></td>
                                                <td width="80"><p><? echo $display_font_color . $buyer_short_name_library[$buyer_name] . $font_end; ?></p></td>
                                                <td width="130"><p><? echo $display_font_color . $style_ref_no . $font_end; ?></p></td>
                                                
                                                <!-- <td width="40" onClick="openmypage_image('requires/fabric_receive_status_report_controller.php?action=show_image&job_no=<? //echo $job_no ?>', 'Image View')" style="cursor:pointer;"><p><img  src='../../<? //echo $imge_arr[$job_no]; ?>' height='25' width='40' /></p></td> -->
                                                <td width="140"><p><? echo $display_font_color . $gmts_item . $font_end; ?></p></td>
                                                <td width="100" align="right"><? if ($z == 1) echo number_format($order_qnty_in_pcs, 0, '.', ''); ?></td>
                                                <td width="80" align="center"><? echo $display_font_color . change_date_format($pub_shipment_date) . $font_end; ?></td>
                                                <td width="80" align="center"><? echo $display_font_color . change_date_format($po_received_date) . $font_end; ?></td>
                                                <td width="80" align="center"><? echo $display_font_color . $po_entry_date . $font_end; ?></td>
                                                
                                                <td width="70">
                                                    <p>
                                                        <?
                                                        $html .= "<td>";
                                                        $d = 1;
                                                        foreach ($yarn_data_array['count'] as $yarn_count_value) {
                                                            if ($d != 1) {
                                                                echo $display_font_color . "<hr/>" . $font_end;
                                                                if ($z == 1)
                                                                    $html .= "<hr/>";
                                                            }

                                                            echo $display_font_color . $yarn_count_value . $font_end;
                                                            if ($z == 1)
                                                                $html .= $yarn_count_value;
                                                            $d++;
                                                        }

                                                        $html .= "</td><td>";
                                                        ?>
                                                    </p>
                                                </td>
                                                <td width="110" style="word-break:break-all;">
                                                    <p>
                                                        <?
                                                        $d = 1;
                                                        foreach ($yarn_data_array['comp'] as $yarn_composition_value) {
                                                            if ($d != 1) {
                                                                echo $display_font_color . "<hr/>" . $font_end;
                                                                if ($z == 1)
                                                                    $html .= "<hr/>";
                                                            }
                                                            echo $display_font_color . $yarn_composition_value . $font_end;
                                                            if ($z == 1)
                                                                $html .= $yarn_composition_value;
                                                            $d++;
                                                        }

                                                        $html .= "</td><td>";
                                                        ?>
                                                    </p>
                                                </td>
                                                <td width="80">
                                                    <p>
                                                        <?
                                                        $d = 1;
                                                        foreach ($yarn_data_array['type'] as $yarn_type_value) {
                                                            if ($d != 1) {
                                                                echo $display_font_color . "<hr/>" . $font_end;
                                                                if ($z == 1)
                                                                    $html .= "<hr/>";
                                                            }

                                                            echo $display_font_color . $yarn_type_value . $font_end;
                                                            if ($z == 1)
                                                                $html .= $yarn_type_value;
                                                            $d++;
                                                        }

                                                        $html .= "</td><td>";
                                                        ?>
                                                    </p>
                                                </td>
                                                <td width="100" align="right">
                                                    <?
                                                    if ($z == 1) {
                                                        echo "<font color='$bgcolor' style='display:none'>" . number_format(array_sum($mkt_required_array), 2, '.', '') . "</font>\n";
                                                        $d = 1;
                                                        foreach ($mkt_required_array as $mkt_required_value) {
                                                            if ($d != 1) {
                                                                echo "<hr/>";
                                                                $html .= "<hr/>";
                                                            }

                                                            $yarn_desc_for_popup_req = explode("__", $yarn_desc_array_for_popup[$d]);
                                                            ?>
                                                            <!-- <a href="##" onClick="openmypage('<? echo $po_id; ?>', 'yarn_req', '<? echo $yarn_desc_for_popup_req[0]; ?>', '<? echo $yarn_desc_for_popup_req[1]; ?>', '<? echo $yarn_desc_for_popup_req[2]; ?>', '<? echo $yarn_desc_for_popup_req[3]; ?>', '<? echo $yarn_desc_for_popup_req[4]; ?>', '<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value, 2, '.', ''); ?></a> -->

                                                            <? echo number_format($mkt_required_value, 2, '.', ''); ?>

                                                            <?
                                                            $html .= number_format($mkt_required_value, 2);
                                                            $d++;
                                                        }
                                                    }

                                                    $html .= "</td><td bgcolor='$discrepancy_td_color'>";
                                                    $html_short .= "<td>";
                                                    ?>
                                                </td>
                                                <? $html .= "</td>"; ?>
                                                
                                                
                                                <?
                                                $html .= "<td bgcolor='$bgcolor_grey_td'>";
                                                $html_short .= "</td><td bgcolor='$bgcolor_grey_td'>";
                                                ?>
                                                
                                                <?
                                                $html .= "</td><td bgcolor='$discrepancy_td_color_zs'>";
                                                $html_short .= "</td><td>";
                                                ?>
                                                
                                                <? $html .= "</td>"; ?>
                                                
                                                <?
                                                $html .= "<td>";
                                                $html_short .= "</td>";
                                                ?>
                                                <? $html .= "</td>"; ?>
                                                
                                                
                                                <?
                                                $html .= "<td bgcolor='#FF9BFF'>";
                                                $html_short .= "<td bgcolor='#FF9BFF'>";
                                                ?>
                                                <td width="100" align="center" bgcolor="#FF9BFF">
                                                    <p>
                                                        <?
                                                        if ($key == 0) {
                                                            echo "-";
                                                            $html .= "-";
                                                            $html_short .= "-";
                                                        } else {
                                                            echo $color_array[$key];
                                                            $html .= $color_array[$key];
                                                            $html_short .= $color_array[$key];
                                                        }
                                                        ?>
                                                    </p>
                                                </td>
                                                <?
                                                $html .= "</td><td>";
                                                $html_short .= "</td>"; 
                                                ?>
                                                <td width="100" align="right">
                                                    <?
                                                    /*$grey_req=0;
                                                    $grey_req=$color_data_array[$key];
                                                        echo number_format($grey_req, 2, '.', '');
                                                    $html .= number_format($grey_req, 2);

                                                    $fin_fab_Requi_array[$buyer_name] += $grey_req;
                                                    $tot_color_wise_req += $grey_req;*/
                                                    
                                                    echo number_format($value, 2, '.', '');
                                                    $html .= number_format($value, 2);

                                                    $fin_fab_Requi_array[$buyer_name] += $value;
                                                    $tot_color_wise_req += $value;
                                                    ?>
                                                </td>
                                                <?
                                                $html .= "</td><td>";
                                                $html_short .= "<td>";

                                                $fab_recv_qnty = $finish_receive_qnty_arr[$po_id][$key];
                                                $fab_purchase_qnty = $finish_purchase_qnty_arr[$po_id][$key] - $finish_recv_rtn_qnty_arr[$po_id][$key];
                                                $issue_to_cut_qnty = $finish_issue_qnty_arr[$po_id][$key] - $finish_issue_rtn_qnty_arr[$po_id][$key];
                                                $dye_qnty = $dye_qnty_arr[$po_id][$key];
                                                ?>
                                                
                                                <?
                                                $html .= "</td>";
                                                $html_short .= "</td>";
                                                ?>
                                                
                                                <?
                                                
                                                $html_short .= "<td>";
                                                ?>
                                            
                                            </tr>
                                            <?
                                            if ($z == 1)
                                                $html .= "</tr>";
                                            else
                                                $html .= "</td></tr>";
                                            $html_short .= "</td></tr>";
                                            $z++;
                                            $k++;
                                        }
                                    }
                                    else 
                                    {
                                        $html .= "<tr bgcolor='" . $bgcolor . "'>
                                                    <td align='left'>" . $i . "</td>
                                                    <td align='left'>" . $main_booking_excel . "</td>
                                                    <td align='left'>" . $sample_booking_excel . "</td>
                                                    <td align='center'>" . $job_no . "</td>
                                                    <td align='left'>" .$po_number. "</td>
                                                    <td>" . $buyer_short_name_library[$buyer_name] . "</td>
                                                    <td align='left'>" . $style_ref_no . "</td>
                                                    
                                                    <td align='left'>" . $gmts_item . "</td>
                                                    <td align='right'>" . $order_qnty_in_pcs . "</td>
                                                    <td align='left'>" . change_date_format($pub_shipment_date) . "</td>
                                                    <td align='center'>" . change_date_format($po_received_date) . "</td>
                                                    <td align='center'>" . $po_entry_date . "</td>";

                                        $html_short .= "<tr bgcolor='" . $bgcolor . "'>
                                                <td align='left'>" . $i . "</td>
                                                <td align='left'>" . $main_booking_excel . "</td>
                                                <td align='left'>" . $sample_booking_excel . "</td>
                                                <td align='left'>" .$po_number. "</td>
                                                <td>" . $buyer_short_name_library[$buyer_name] . "</td>
                                                <td align='right'>" . $order_qnty_in_pcs . "</td>
                                                <td align='left'>" . change_date_format($pub_shipment_date) . "</td>";
                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $k; ?>">
                                            <td width="40"><? echo $i; ?></td>
                                            <td width="100" align="center"><? echo $job_no; ?></td>
                                            <td width="120">
                                                <p>
                                                    <!-- <a href='#report_details' onClick="progress_comment_popup('<? echo $job_no; ?>', '<? echo $po_id; ?>', '<? echo $template_id; ?>', '<? echo $tna_process_type; ?>');"><? echo $po_number; //$display_font_color.$row[csf('po_number')].$font_end;         ?></a> -->

                                                    <? echo $po_number; ?>
                                                </p>
                                            </td>
                                            <td width="100"><p><? echo $order_status[$is_confirmed] ?></p></>
                                            <td width="80"><p><? echo $buyer_short_name_library[$buyer_name]; ?></p></td>
                                            <td width="130"><p><? echo $style_ref_no; ?></p></td>
                                            
                                            <!-- <td width="40" onClick="openmypage_image('requires/fabric_receive_status_report_controller.php?action=show_image&job_no=<? //echo $row[csf("job_no")] ?>', 'Image View')" style="cursor:pointer;"><p><img  src='../../<? //echo $imge_arr[$job_no]; ?>' height='25' width='40' /></p></td> -->
                                            <td width="140"><p><? echo $gmts_item; ?></p></td>
                                            <td width="100" align="right"><? echo number_format($order_qnty_in_pcs, 0, '.', ''); ?></td>
                                            <td width="80" align="center"><? echo change_date_format($pub_shipment_date); ?></td>
                                            <td width="80" align="center"><? echo change_date_format($po_received_date); ?></td>
                                            <td width="80" align="center"><? echo $po_entry_date; ?></td>
                                            <td width="70">
                                                <?
                                                $html .= "<td>";
                                                $d = 1;
                                                foreach ($yarn_data_array['count'] as $yarn_count_value) {
                                                    if ($d != 1) {
                                                        echo "<hr/>";
                                                        $html .= "<hr/>";
                                                    }

                                                    echo $yarn_count_value;
                                                    $html .= $yarn_count_value;

                                                    $d++;
                                                }

                                                $html .= "</td><td>";
                                                ?>
                                            </td>
                                            <td width="110" style="word-break:break-all;">
                                                <p>
                                                    <?
                                                    $d = 1;
                                                    foreach ($yarn_data_array['comp'] as $yarn_composition_value) {
                                                        if ($d != 1) {
                                                            echo "<hr/>";
                                                            $html .= "<hr/>";
                                                        }

                                                        echo $yarn_composition_value;
                                                        $html .= $yarn_composition_value;

                                                        $d++;
                                                    }

                                                    $html .= "</td><td>";
                                                    ?>
                                                </p>
                                            </td>
                                            <td width="80">
                                                <p>
                                                    <?
                                                    $d = 1;
                                                    foreach ($yarn_data_array['type'] as $yarn_type_value) {
                                                        if ($d != 1) {
                                                            echo "<hr/>";
                                                            $html .= "<hr/>";
                                                        }

                                                        echo $yarn_type_value;
                                                        $html .= $yarn_type_value;

                                                        $d++;
                                                    }

                                                    $html .= "</td><td>";
                                                    ?>
                                                </p>
                                            </td>
                                            <td width="100" align="right">
                                                <?
                                                echo "<font color='$bgcolor' style='display:none'>" . number_format(array_sum($mkt_required_array), 2, '.', '') . "</font>\n";
                                                $d = 1;
                                                foreach ($mkt_required_array as $mkt_required_value) {
                                                    if ($d != 1) {
                                                        echo "<hr/>";
                                                        $html .= "<hr/>";
                                                    }

                                                    $yarn_desc_for_popup_req = explode("__", $yarn_desc_array_for_popup[$d]);
                                                    ?>
                                                    <!-- <a href="##" onClick="openmypage('<? echo $po_id; ?>', 'yarn_req', '<? echo $yarn_desc_for_popup_req[0]; ?>', '<? echo $yarn_desc_for_popup_req[1]; ?>', '<? echo $yarn_desc_for_popup_req[2]; ?>', '<? echo $yarn_desc_for_popup_req[3]; ?>', '<? echo $yarn_desc_for_popup_req[4]; ?>', '<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value, 2, '.', ''); ?></a> -->

                                                    <? echo number_format($mkt_required_value, 2, '.', ''); ?>

                                                    <?
                                                    $html .= number_format($mkt_required_value, 2);
                                                    $d++;
                                                }

                                                $html .= "</td><td bgcolor='$discrepancy_td_color'>";
                                                $html_short .= "<td>";
                                                ?>
                                            </td>
                                            <? $html .= "</td><td>"; ?>
                                            
                                            
                                            
                                        </tr>
                                        <?
                                        $html .= "</td>
                                        <td>" . number_format($grey_fabric_issue, 2) . "</td>
                                        <td>" . number_format($batch_qnty, 2) . "</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td></tr>";

                                        $html_short .= "</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        </tr>
                                        ";
                                        $k++;
                                    }
                                    $i++;
                                }
                            }
                        }
                    } 
                   

                    // end main query
                }
                else 
                {
                    /* ###########
                        all code repeated both if and else
                        if($chk_no_boking==1) display only  no booking Yes
                        else display booking and  no booking Yes
                        ############## */

                    if ($chk_no_boking == 1) 
                    {// check no booking 
                        foreach ($job_data_arr as $job_no=>$other_data) 
                        {
                            
                            $ex_data_job=explode('##',$other_data);
                            $company_id=''; $buyer_name='';  $job_no_prefix_num=''; $style_ref_no=''; $gmts_item_id=''; $order_uom=''; $ratio=''; $poId_id=''; $grouping=''; $file_no=''; $po_number=''; $po_qnty=''; $pub_shipment_date=''; $shiping_status=''; $insert_date=''; $po_received_date=''; $plan_cut=''; $is_confirmed='';// $po_id
                            
                            $company_id=$ex_data_job[0];
                            $buyer_name=$ex_data_job[1];
                            $job_no_prefix_num=$ex_data_job[2];
                            $style_ref_no=$ex_data_job[3];
                            $gmts_item_id=$ex_data_job[4];
                            $order_uom=$ex_data_job[5];
                            $ratio=$ex_data_job[6];
                            
                            $job_all_data=array_filter(explode('___',$job_allData_arr[$job_no]));
                            //echo $job_all_data;
                            $grouping_all=''; $file_no_all=''; $po_number_all=''; $pub_shipment_date_all=''; $insert_date_all=''; $po_received_date_all=''; $po_id_all='';
                            $bk=0;
                            
                            foreach($job_all_data as $data_po)
                            {
                                $ex_data=explode('**',$data_po);
                                
                                if($grouping_all=="") $grouping_all=$ex_data[0]; else $grouping_all.=','.$ex_data[0];
                                if($file_no_all=="") $file_no_all=$ex_data[1]; else $file_no_all.=','.$ex_data[1];
                                if($po_number_all=="") $po_number_all=$ex_data[2]; else $po_number_all.=','.$ex_data[2];
                                $po_qnty+=$ex_data[3];
                                if($pub_shipment_date_all=="") $pub_shipment_date_all=$ex_data[4]; else $pub_shipment_date_all.=','.$ex_data[4];
                                //$shiping_status=$ex_data[5];
                                if($insert_date_all=="") $insert_date_all=$ex_data[6]; else $insert_date_all.=','.$ex_data[6];
                                if($po_received_date_all=="") $po_received_date_all=$ex_data[7]; else $po_received_date_all.=','.$ex_data[7];
                                $plan_cut+=$ex_data[8];
                                //$is_confirmed=$ex_data[9];
                                if($po_id_all=="") $po_id_all=$ex_data[10]; else $po_id_all.=','.$ex_data[10];
                            }
                            //echo $po_number_all;
                            $poId_id=implode(',',array_filter(array_unique(explode(',',$po_id_all)))); 
                            $grouping=implode(',',array_filter(array_unique(explode(',',$grouping_all))));  
                            $file_no=implode(',',array_filter(array_unique(explode(',',$file_no_all))));  
                            $po_number=implode(', ',array_filter(array_unique(explode(',',$po_number_all))));  
                            $po_qnty=$po_qnty; 
                            $pub_shipment_date=implode(',',array_filter(array_unique(explode(',',$pub_shipment_date_all))));  
                            //$shiping_status=''; 
                            $insert_date=implode(',',array_filter(array_unique(explode(',',$insert_date_all)))); 
                            $po_received_date=implode(',',array_filter(array_unique(explode(',',$po_received_date_all))));
                            $plan_cut=$plan_cut; 
                            
                            
                            
                            
                            
                            
                            $check_job_po_id = explode(",", $poId_id);
                            foreach ($check_job_po_id as $po_id) {
                                $no_book_check .= implode(",", array_filter(explode(",", substr($dataArrayWo[$po_id], 0, -1)))) . ",";
                            }

                            $no_book_check_arr = explode(",", substr($no_book_check, 0, -1));
                            if (count($no_book_check_arr) < 1) {
                                $order_qnty_in_pcs = $po_qnty * $ratio;
                                $plan_cut_qnty = $plan_cut * $ratio;
                                $order_qty_array[$buyer_name] += $order_qnty_in_pcs;
                                $gmts_item = '';
                                $gmts_item_id = explode(",", $gmts_item_id);
                                foreach ($gmts_item_id as $item_id) {
                                    if ($gmts_item == "")
                                        $gmts_item = $garments_item[$item_id];
                                    else
                                        $gmts_item .= "," . $garments_item[$item_id];
                                }

                                $dzn_qnty = 0;
                                $balance = 0;
                                $job_mkt_required = 0;
                                $yarn_issued = 0;
                                if ($costing_per_id_library[$job_no] == 1)
                                    $dzn_qnty = 12;
                                else if ($costing_per_id_library[$job_no] == 3)
                                    $dzn_qnty = 12 * 2;
                                else if ($costing_per_id_library[$job_no] == 4)
                                    $dzn_qnty = 12 * 3;
                                else if ($costing_per_id_library[$job_no] == 5)
                                    $dzn_qnty = 12 * 4;
                                else
                                    $dzn_qnty = 1;

                                $dzn_qnty = $dzn_qnty * $ratio;

                                $yarn_data_array = array();
                                $mkt_required_array = array();
                                $yarn_desc_array_for_popup = array();
                                $yarn_desc_array = array();
                                $yarn_iss_qnty_array = array();
                                $s = 1;
                                

                                $grey_purchase_qnty = 0;
                                $grey_recv_qnty = 0;
                                $grey_fabric_issue = 0;
                                $booking_data = '';
                                $job_po_id = explode(",", $poId_id);
                                foreach ($job_po_id as $po_id) {
                                    $yarn_descrip_data = $yarn_des_data[$po_id];

                                    foreach ($yarn_descrip_data as $count => $count_value) {
                                        foreach ($count_value as $Composition => $composition_value) {
                                            foreach ($composition_value as $percent => $percent_value) {
                                                foreach ($percent_value as $typee => $type_value) {
                                                    //$yarnRow=explode("**",$yarnRow);
                                                    $count_id = $count; //$yarnRow[0];
                                                    $copm_one_id = $Composition; //$yarnRow[1];
                                                    $percent_one = $percent; //$yarnRow[2];
                                                    //$copm_two_id=$yarnRow[3];
                                                    //$percent_two=$yarnRow[4];
                                                    $type_id = $typee; //$yarnRow[5];
                                                    $qnty = $type_value; //$yarnRow[6];

                                                    $mkt_required = $qnty; //$plan_cut_qnty*($qnty/$dzn_qnty);
                                                    $mkt_required_array[$s] = $mkt_required;
                                                    $job_mkt_required += $mkt_required;

                                                    $yarn_data_array['count'][$s] = $yarn_count_details[$count_id];
                                                    $yarn_data_array['type'][$s] = $yarn_type[$type_id];

                                                    /* if($percent_two!=0)
                                                        {
                                                        $compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]." ".$percent_two." %";
                                                        }
                                                        else
                                                        {
                                                        $compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
                                                        } */
                                                    $compos = $composition[$copm_one_id] . " " . $percent_one . " %" . " " . $composition[$copm_two_id];

                                                    $yarn_data_array['comp'][] = $compos;

                                                    $yarn_desc_array[$s] = $yarn_count_details[$count_id] . " " . $compos . " " . $yarn_type[$type_id];

                                                    $yarn_desc_for_popup = $count_id . "__" . $copm_one_id . "__" . $percent_one . "__" . $copm_two_id . "__" . $percent_two . "__" . $type_id;
                                                    $yarn_desc_array_for_popup[$s] = $yarn_desc_for_popup;

                                                    $s++;
                                                }
                                            }
                                        }
                                    }
                                    $dataYarnIssue = explode(",", substr($dataArrayYarnIssue[$po_id], 0, -1));
                                    foreach ($dataYarnIssue as $yarnIssueRow) {
                                        $yarnIssueRow = explode("**", $yarnIssueRow);
                                        $yarn_count_id = $yarnIssueRow[0];
                                        $yarn_comp_type1st = $yarnIssueRow[1];
                                        $yarn_comp_percent1st = $yarnIssueRow[2];
                                        $yarn_comp_type2nd = $yarnIssueRow[3];
                                        $yarn_comp_percent2nd = $yarnIssueRow[4];
                                        $yarn_type_id = $yarnIssueRow[5];
                                        $issue_qnty = $yarnIssueRow[6];
                                        $return_qnty = $yarnIssueRow[7];

                                        if ($yarn_comp_percent2nd != 0) {
                                            $compostion_not_req = $composition[$yarn_comp_type1st] . " " . $yarn_comp_percent1st . " %" . " " . $composition[$yarn_comp_type2nd] . " " . $yarn_comp_percent2nd . " %";
                                        } else {
                                            $compostion_not_req = $composition[$yarn_comp_type1st] . " " . $yarn_comp_percent1st . " %" . " " . $composition[$yarn_comp_type2nd];
                                        }

                                        $desc = $yarn_count_details[$yarn_count_id] . " " . $compostion_not_req . " " . $yarn_type[$yarn_type_id];

                                        $net_issue_qnty = $issue_qnty - $return_qnty;
                                        $yarn_issued += $net_issue_qnty;
                                        if (!in_array($desc, $yarn_desc_array)) {
                                            $yarn_iss_qnty_array['not_req'] += $net_issue_qnty;
                                        } else {
                                            $yarn_iss_qnty_array[$desc] += $net_issue_qnty;
                                        }
                                    }

                                    $grey_purchase_qnty += $greyPurchaseQntyArray[$po_id] - $grey_receive_return_qnty_arr[$po_id];
                                    $grey_recv_qnty += $grey_receive_qnty_arr[$po_id];
                                    $grey_fabric_issue += $grey_issue_qnty_arr[$po_id] - $grey_issue_return_qnty_arr[$po_id];

                                    $booking_data .= implode(",", array_filter(explode(",", substr($dataArrayWo[$po_id], 0, -1)))) . ",";
                                }

                                if (($cbo_discrepancy == 1 && $grey_recv_qnty > $yarn_issued) || ($cbo_discrepancy == 0)) {
                                    if ($i % 2 == 0)
                                        $bgcolor = "#E9F3FF";
                                    else
                                        $bgcolor = "#FFFFFF";
                                    $buyer_name_array[$buyer_name] = $buyer_short_name_library[$buyer_name];

                                    $booking_array = array();
                                    $color_data_array = array();
                                    $required_qnty = 0;
                                    $main_booking = '';
                                    $sample_booking = '';
                                    $main_booking_excel = '';
                                    $sample_booking_excel = '';
                                    $dataArray = explode(",", substr($booking_data, 0, -1));
                                    if (count($dataArray) > 0) {
                                        foreach ($dataArray as $woRow) {
                                            $woRow = explode("**", $woRow);
                                            $id = $woRow[0];
                                            $booking_no = $woRow[1];
                                            $insert_date = $woRow[2];
                                            $item_category = $woRow[3];
                                            $fabric_source = $woRow[4];
                                            $company_id = $woRow[5];
                                            $booking_type = $woRow[6];
                                            $booking_no_prefix_num = $woRow[7];
                                            $job_no = $woRow[8];
                                            $is_short = $woRow[9];
                                            $is_approved = $woRow[10];
                                            $fabric_color_id = $woRow[11];
                                            $req_qnty = $woRow[12];
                                            $grey_req_qnty = $woRow[13];
                                            $book_prefix_no = $woRow[14];

                                            $required_qnty += $grey_req_qnty;
                                            if ($fabric_source == 2) {
                                                $req_purc_qnty += $grey_req_qnty;
                                            }

                                            if (!in_array($id, $booking_array)) {

                                                $system_date = date('d-M-Y', strtotime($insert_date));

                                                if ($fabric_source == 2)
                                                    $wo_color = "color='color:#000'";
                                                else
                                                    $wo_color = "";

                                                $action_name = '';
                                                if ($booking_type == 4) {
                                                    $action_name = 'show_fabric_booking_report';
                                                    $sample_booking .= "<a href='##' style='color:#000' onclick=\"generate_worder_report('3','" . $booking_no . "','" . $company_id . "','" . $poId_id . "','" . $item_category . "','" . $fabric_source . "','" . $job_no . "','" . $is_approved . "','" . $action_name . "')\"><font style='font-weight:bold' $wo_color>" . $booking_no . "(" . $system_date . ")" . "</font></a><br>";
                                                    $sample_booking_excel .= "<font style='font-weight:bold' $wo_color>" . $booking_no . "(" . $system_date . ")" . "</font><br>";
                                                } else {
                                                    $all_book_prefix_no .= $book_prefix_no . ",";
                                                    if ($is_short == 1) {
                                                        $pre = "S";
                                                        $action_name = $report_format_arr[$booking_print_arr[2]];
                                                    } else {
                                                        $pre = "M";
                                                        $action_name = $report_format_arr[$booking_print_arr[1]];
                                                    }

                                                    if ($action_name == '')
                                                        $action_name = 'show_fabric_booking_report';

                                                    $main_booking .= "<a href='##' style='color:#000' onclick=\"generate_worder_report('" . $is_short . "','" . $booking_no . "','" . $company_id . "','" . $poId_id . "','" . $item_category . "','" . $fabric_source . "','" . $job_no . "','" . $is_approved . "','" . $action_name . "')\"><font style='font-weight:bold' $wo_color>" . $booking_no . "(" . $system_date . ")" . $pre . "</font></a><br>";
                                                    $main_booking_excel .= "<font style='font-weight:bold' $wo_color>" . $booking_no . "(" . $system_date . ")" . $pre . "</font><br>";
                                                }

                                                $booking_array[] = $id;
                                            }
                                            $color_data_array[$fabric_color_id] += $req_qnty;
                                        }
                                    }
                                    else {
                                        $main_booking .= "No Booking";
                                        $main_booking_excel .= "No Booking";
                                        $sample_booking .= "No Booking";
                                        $sample_booking_excel .= "No Booking";
                                        $all_book_prefix_no = "&nbsp;";
                                    }

                                    

                                    if ($sample_booking == "") {
                                        $sample_booking .= "No Booking";
                                        $sample_booking_excel .= "No Booking";
                                    }
                                    $all_book_prefix_no = chop($all_book_prefix_no, ",");
                                    $yarn_issue_array[$buyer_name] += $yarn_issued;
                                    $grey_required_array[$buyer_name] += $required_qnty;

                                    $net_trans_yarn = 0;
                                    $net_trans_knit = 0;
                                    $batch_qnty = 0;
                                    foreach ($job_po_id as $val) {
                                        $finish_color = array_unique(explode(",", $po_color_arr[$val]));
                                        foreach ($finish_color as $color_id) {
                                            if ($color_id > 0) {
                                                $color_data_array[$color_id] += 0;
                                            }
                                        }

                                        $net_trans_yarn += $trans_qnty_arr[$val]['yarn_trans'];
                                        $net_trans_knit += $trans_qnty_arr[$val]['knit_trans'];

                                        $batch_qnty += $batch_qnty_arr[$val];
                                    }

                                    $yarn_issue_array[$buyer_name] += $net_trans_yarn;
                                    $balance = $required_qnty - ($yarn_issued + $net_trans_yarn + $req_purc_qnty);

                                    $yarn_balance_array[$buyer_name] += $balance;

                                    $knitted_array[$buyer_name] += $grey_recv_qnty + $grey_purchase_qnty;

                                    $knitted_array[$buyer_name] += $net_trans_knit;
                                    $grey_balance = $required_qnty - ($grey_recv_qnty + $net_trans_knit + $grey_purchase_qnty);

                                    $grey_balance_array[$buyer_name] += $grey_balance;

                                    $grey_issue_array[$buyer_name] += $grey_fabric_issue;

                                    $batch_qnty_array[$buyer_name] += $batch_qnty;

                                    $grey_available = $grey_recv_qnty + $grey_purchase_qnty + $net_trans_knit;

                                    $knit_balance_qnty = $required_qnty - ($grey_recv_qnty + $req_purc_qnty);
                                    $grey_inhand_qnty = $grey_available - $grey_fabric_issue;
                                    $grey_iss_balance_qnty = $required_qnty - $grey_fabric_issue;

                                    $tot_order_qnty += $order_qnty_in_pcs;
                                    $tot_mkt_required += $job_mkt_required;
                                    $tot_yarn_issue_qnty += $yarn_issued;
                                    $tot_fabric_req += $required_qnty;
                                    $tot_balance += $balance;
                                    $tot_grey_recv_qnty += $grey_recv_qnty;
                                    $tot_knit_balance_qnty += $knit_balance_qnty;
                                    $tot_grey_purchase_qnty += $grey_purchase_qnty;
                                    $tot_grey_available += $grey_available;
                                    $tot_grey_balance += $grey_balance;
                                    $tot_grey_issue += $grey_fabric_issue;
                                    $tot_grey_inhand += $grey_inhand_qnty;
                                    $tot_grey_issue_bl += $grey_iss_balance_qnty;
                                    $tot_batch_qnty += $batch_qnty;

                                    if ($required_qnty > $job_mkt_required)
                                        $bgcolor_grey_td = '#FF0000';
                                    $bgcolor_grey_td = '';

                                    $po_entry_date = date('d-m-Y', strtotime($insert_date));
                                    $costing_date = $costing_date_library[$job_no];
                                    $tot_color = count($color_data_array);
                                    
                                    //15.03.2020 added by zaman
                                    $bgColorArr['color'] = $discrepancy_td_color;
                                    $bgColorArr['req_qty'] = $required_qnty;
                                    $bgColorArr['rcv_qty'] = $grey_recv_qnty;
                                    //$discrepancy_td_color_zs = getBgColorZs($bgColorArr);
                                    //end
                                    
                                    if ($tot_color > 0) {
                                        $z = 1;
                                        foreach ($color_data_array as $key => $value) {
                                            if ($z == 1) {
                                                $display_font_color = "";
                                                $font_end = "";
                                            } else {
                                                $display_font_color = "<font style='display:none' color='$bgcolor'>";
                                                $font_end = "</font>";
                                            }

                                            if ($z == 1) 
                                            {
                                                $html .= "<tr bgcolor='" . $bgcolor . "'>
                                                    <td align='left'>" . $i . "</td>
                                                    <td align='left'>" . $main_booking_excel . "</td>
                                                    <td align='left'>" . $sample_booking_excel . "</td>
                                                    <td align='center'>" . $job_no . "</td>
                                                    <td align='left'>" . $po_number . "</td>
                                                    <td>" . $buyer_short_name_library[$buyer_name] . "</td>
                                                    <td align='left'>" . $$style_ref_no . "</td>
                                                    <td align='left'>" . $gmts_item . "</td>
                                                    <td align='right'>" . $order_qnty_in_pcs . "</td>
                                                    <td align='left'>View</td>";

                                                $html_short .= "<tr bgcolor='" . $bgcolor . "'>
                                                        <td align='left'>" . $i . "</td>
                                                        <td align='left'>" . $main_booking_excel . "</td>
                                                        <td align='left'>" . $sample_booking_excel . "</td>
                                                        <td align='left'>" . $po_number . "</td>
                                                        <td>" . $buyer_short_name_library[$buyer_name] . "</td>
                                                        <td align='right'>" . $order_qnty_in_pcs . "</td>
                                                        <td align='left'>View</td>";
                                            } 
                                            else 
                                            {
                                                $html .= "<tr bgcolor='" . $bgcolor . "'>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>";

                                                $html_short .= "<tr bgcolor='" . $bgcolor . "'>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>";
                                            }
                                            ?>
                                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $k; ?>">
                                                <td width="40"><? echo $display_font_color . $i . $font_end; ?></td>
                                                
                                                <td width="100" align="center"><? echo $display_font_color . $job_no . $font_end; ?></td>
                                                <td width="120"><p><? echo $display_font_color . $po_number . $font_end; ?></p></td>
                                                <td width="80"><p><? echo $display_font_color . $buyer_short_name_library[$buyer_name] . $font_end; ?></p></td>
                                                <td width="130"><p><? echo $display_font_color . $$style_ref_no . $font_end; ?></p></td>
                                                <!-- <td width="40" onClick="openmypage_image('requires/fabric_receive_status_report_controller.php?action=show_image&job_no=<? //echo $row[csf("job_no")] ?>', 'Image View')" style="cursor:pointer;color: blue;">View</td> -->
                                                <td width="140"><p><? echo $display_font_color . $gmts_item; ?></p></td>
                                                <td width="100" align="right"><? if ($z == 1) echo number_format($order_qnty_in_pcs, 0, '.', ''); ?></td>
                                                <td width="80" align="center"><? echo $display_font_color; ?><a href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>', 'Shipment_date', '')"><? echo "View"; ?></a><? echo $font_end; ?></td>
                                                <td width="70">
                                                    <?
                                                    $html .= "<td>";
                                                    $d = 1;
                                                    foreach ($yarn_data_array['count'] as $yarn_count_value) 
                                                    {
                                                        if ($d != 1) 
                                                        {
                                                            echo $display_font_color . "<hr/>" . $font_end;
                                                            if ($z == 1)
                                                                $html .= "<hr/>";
                                                        }

                                                        echo $display_font_color . $yarn_count_value . $font_end;
                                                        if ($z == 1)
                                                            $html .= $yarn_count_value;
                                                        $d++;
                                                    }

                                                    $html .= "</td><td>";
                                                    ?>
                                                </td>
                                                <td width="110" style="word-break:break-all;">
                                                    <p>
                                                        <?
                                                        $d = 1;
                                                        foreach ($yarn_data_array['comp'] as $yarn_composition_value) 
                                                        {
                                                            if ($d != 1) {
                                                                echo $display_font_color . "<hr/>" . $font_end;
                                                                if ($z == 1)
                                                                    $html .= "<hr/>";
                                                            }
                                                            echo $display_font_color . $yarn_composition_value . $font_end;
                                                            if ($z == 1)
                                                                $html .= $yarn_composition_value;
                                                            $d++;
                                                        }

                                                        $html .= "</td><td>";
                                                        ?>
                                                    </p>
                                                </td>
                                                <td width="80">
                                                    <p>
                                                        <?
                                                        $d = 1;
                                                        foreach ($yarn_data_array['type'] as $yarn_type_value) {
                                                            if ($d != 1) {
                                                                echo $display_font_color . "<hr/>" . $font_end;
                                                                if ($z == 1)
                                                                    $html .= "<hr/>";
                                                            }

                                                            echo $display_font_color . $yarn_type_value . $font_end;
                                                            if ($z == 1)
                                                                $html .= $yarn_type_value;
                                                            $d++;
                                                        }

                                                        $html .= "</td><td>";
                                                        ?>
                                                    </p>
                                                </td>
                                                <td width="100" align="right">
                                                    <?
                                                    if ($z == 1) {
                                                        echo "<font color='$bgcolor' style='display:none'>" . number_format(array_sum($mkt_required_array), 2, '.', '') . "</font>\n";
                                                        $d = 1;
                                                        foreach ($mkt_required_array as $mkt_required_value) {
                                                            if ($d != 1) {
                                                                echo "<hr/>";
                                                                $html .= "<hr/>";
                                                            }

                                                            $yarn_desc_for_popup_req = explode("__", $yarn_desc_array_for_popup[$d]);
                                                            ?>
                                                            <!-- <a href="##" onClick="openmypage('<? echo $poId_id; ?>', 'yarn_req', '<? echo $yarn_desc_for_popup_req[0]; ?>', '<? echo $yarn_desc_for_popup_req[1]; ?>', '<? echo $yarn_desc_for_popup_req[2]; ?>', '<? echo $yarn_desc_for_popup_req[3]; ?>', '<? echo $yarn_desc_for_popup_req[4]; ?>', '<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value, 2, '.', ''); ?></a> -->

                                                            <? echo number_format($mkt_required_value, 2, '.', ''); ?>
                                                            <?
                                                            $html .= number_format($mkt_required_value, 2);
                                                            $d++;
                                                        }
                                                    }

                                                    $html .= "</td><td bgcolor='$discrepancy_td_color'>";
                                                    $html_short .= "<td>";
                                                    ?>
                                                </td>
                                                <? $html .= "</td>"; ?>
                                                
                                                
                                                <?
                                                $html .= "<td bgcolor='$bgcolor_grey_td'>";
                                                $html_short .= "</td><td bgcolor='$bgcolor_grey_td'>";
                                                ?>
                                                
                                                <?
                                                $html .= "</td><td bgcolor='$discrepancy_td_color_zs'>";
                                                $html_short .= "</td><td>";
                                                ?>
                                                
                                                <? $html .= "</td>"; ?>
                                                
                                                
                                                <? $html .= "<td>"; ?>
                                                
                                                <?
                                                $html .= "<td>";
                                                $html_short .= "</td>";
                                                ?>
                                                <? $html .= "</td>"; ?>
                                                
                                                
                                                <?
                                                $html .= "<td bgcolor='#FF9BFF'>";
                                                $html_short .= "<td bgcolor='#FF9BFF'>";
                                                ?>
                                                <td width="100" align="center" bgcolor="#FF9BFF">
                                                    <p>
                                                        <?
                                                        if ($key == 0) {
                                                            echo "-";
                                                            $html .= "-";
                                                            $html_short .= "-";
                                                        } else {
                                                            echo $color_array[$key];
                                                            $html .= $color_array[$key];
                                                            $html_short .= $color_array[$key];
                                                        }
                                                        ?>
                                                    </p>
                                                </td>
                                                <?
                                                $html .= "</td><td>";
                                                $html_short .= "</td>";
                                                ?>
                                                <td width="100" align="right">
                                                    <?
                                                    echo number_format($value, 2, '.', '');
                                                    $html .= number_format($value, 2);

                                                    $fin_fab_Requi_array[$buyer_name] += $value;
                                                    $tot_color_wise_req += $value;
                                                    ?>
                                                </td>
                                                <?
                                                $html .= "</td><td>";
                                                $html_short .= "<td>";
                                                $fab_recv_qnty = 0;
                                                $fab_purchase_qnty = 0;
                                                $issue_to_cut_qnty = 0;
                                                $dye_qnty = 0;
                                                foreach ($job_po_id as $val) {
                                                    $fab_recv_qnty += $finish_receive_qnty_arr[$val][$key];
                                                    $fab_purchase_qnty += $finish_purchase_qnty_arr[$val][$key] - $finish_recv_rtn_qnty_arr[$val][$key];
                                                    $issue_to_cut_qnty += $finish_issue_qnty_arr[$val][$key] - $finish_issue_rtn_qnty_arr[$val][$key];
                                                    $dye_qnty += $dye_qnty_arr[$val][$key];
                                                }
                                                ?>
                                                
                                                <?
                                                $html .= "</td><td>";
                                                $html_short .= "</td>";
                                                ?>
                                                
                                                <?
                                                
                                                $html_short .= "<td>";
                                                ?>
                                                
                                                                                                    
                                            </tr>
                                            <?
                                            if ($z == 1)
                                                $html .= "</tr>";
                                            else
                                                $html .= "</td></tr>";
                                            $html_short .= "</td></tr>";
                                            $z++;
                                            $k++;
                                        }
                                    }
                                    else 
                                    {
                                        $html .= "<tr bgcolor='" . $bgcolor . "'>
                                                    <td align='left'>" . $i . "</td>
                                                    <td align='left'>" . $main_booking_excel . "</td>
                                                    <td align='left'>" . $sample_booking_excel . "</td>
                                                    <td align='center'>" . $job_no . "</td>
                                                    <td align='left'>" . $po_number . "</td>
                                                    <td>" . $buyer_short_name_library[$buyer_name] . "</td>
                                                    <td align='left'>" . $$style_ref_no . "</td>
                                                    <td align='left'>" . $gmts_item . "</td>
                                                    <td align='right'>" . $order_qnty_in_pcs . "</td>
                                                    <td align='left'>View</td>";

                                        $html_short .= "<tr bgcolor='" . $bgcolor . "'>
                                                <td align='left'>" . $i . "</td>
                                                <td align='left'>" . $main_booking_excel . "</td>
                                                <td align='left'>" . $sample_booking_excel . "</td>
                                                <td align='left'>" . $po_number . "</td>
                                                <td>" . $buyer_short_name_library[$buyer_name] . "</td>
                                                <td align='right'>" . $order_qnty_in_pcs . "</td>
                                                <td align='left'>View</td>";
                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $k; ?>">
                                            <td width="40"><? echo $i; ?></td>
                                            <td width="100" align="center"><? echo $job_no; ?></td>
                                            <td width="120"><p><? echo $po_number; ?></p></td>
                                            <td width="80"><p><? echo $buyer_short_name_library[$buyer_name]; ?></p></td>
                                            <td width="130"><p><? echo $$style_ref_no; ?></p></td>
                                            <!-- <td width="40" onClick="openmypage_image('requires/fabric_receive_status_report_controller.php?action=show_image&job_no=<? //echo $job_no ?>', 'Image View')" style="cursor:pointer;color: blue;">View</td> -->
                                            <td width="140"><p><? echo $gmts_item; ?></p></td>
                                            <td width="100" align="right"><? echo number_format($order_qnty_in_pcs, 0, '.', ''); ?></td>
                                            <td width="80" align="center"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $poId_id; ?>', 'Shipment_date', '')"><? echo "View"; ?></a></td>
                                            <td width="70">
                                                <?
                                                $html .= "<td>";
                                                $d = 1;
                                                foreach ($yarn_data_array['count'] as $yarn_count_value) 
                                                {
                                                    if ($d != 1) {
                                                        echo "<hr/>";
                                                        $html .= "<hr/>";
                                                    }

                                                    echo $yarn_count_value;
                                                    $html .= $yarn_count_value;

                                                    $d++;
                                                }

                                                $html .= "</td><td>";
                                                ?>
                                            </td>
                                            <td width="110" style="word-break:break-all;">
                                                <p>
                                                    <?
                                                    $d = 1;
                                                    foreach ($yarn_data_array['comp'] as $yarn_composition_value) {
                                                        if ($d != 1) {
                                                            echo "<hr/>";
                                                            $html .= "<hr/>";
                                                        }

                                                        echo $yarn_composition_value;
                                                        $html .= $yarn_composition_value;

                                                        $d++;
                                                    }

                                                    $html .= "</td><td>";
                                                    ?>
                                                </p>
                                            </td>
                                            <td width="80">
                                                <p>
                                                    <?
                                                    $d = 1;
                                                    foreach ($yarn_data_array['type'] as $yarn_type_value) {
                                                        if ($d != 1) {
                                                            echo "<hr/>";
                                                            $html .= "<hr/>";
                                                        }

                                                        echo $yarn_type_value;
                                                        $html .= $yarn_type_value;

                                                        $d++;
                                                    }

                                                    $html .= "</td><td>";
                                                    ?>
                                                </p>
                                            </td>
                                            <td width="100" align="right">
                                                <?
                                                echo "<font color='$bgcolor' style='display:none'>" . number_format(array_sum($mkt_required_array), 2, '.', '') . "</font>\n";
                                                $d = 1;
                                                foreach ($mkt_required_array as $mkt_required_value) {
                                                    if ($d != 1) {
                                                        echo "<hr/>";
                                                        $html .= "<hr/>";
                                                    }

                                                    $yarn_desc_for_popup_req = explode("__", $yarn_desc_array_for_popup[$d]);
                                                    ?>
                                                    <!-- <a href="##" onClick="openmypage('<? echo $poId_id; ?>', 'yarn_req', '<? echo $yarn_desc_for_popup_req[0]; ?>', '<? echo $yarn_desc_for_popup_req[1]; ?>', '<? echo $yarn_desc_for_popup_req[2]; ?>', '<? echo $yarn_desc_for_popup_req[3]; ?>', '<? echo $yarn_desc_for_popup_req[4]; ?>', '<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value, 2, '.', ''); ?></a> -->

                                                    <? echo number_format($mkt_required_value, 2, '.', ''); ?>
                                                    
                                                    <?
                                                    $html .= number_format($mkt_required_value, 2);
                                                    $d++;
                                                }

                                                $html .= "</td><td bgcolor='$discrepancy_td_color'>";
                                                $html_short .= "<td>";
                                                ?>
                                            </td>
                                            <? $html .= "</td><td>"; ?>
                                            
                                            
                                        </tr>
                                        <?
                                        $html .= "</td>
                                        <td>" . number_format($grey_fabric_issue, 2) . "</td>
                                        <td>" . number_format($batch_qnty, 2) . "</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td></tr>
                                        ";

                                        $html_short .= "</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        </tr>
                                        ";
                                        $k++;
                                    }
                                    $i++;
                                }
                            }
                        }// end main query
                    } 
                   
                }
                ?>
            </table>
        </div>
        
        <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <tfoot>
                <tr>
                    <th width="40"></th>
                    
                    <th width="100"></th>
                    <th width="120"></th>
                    <? if ($type == 1) 
                    { ?>
                        <th width='100'></th>
                    <? } ?>
                    <th width="80"></th>
                    <th width="130" title='here iam'></th>
                    
                    
                    <th width="140">Total</th>
                    <th width="100" id="value_tot_order_qnty"><? echo number_format($tot_order_qnty, 0); ?></th>
                    <th width="80"></th>
                    <?
                    if ($type == 1) 
                    {
                        ?>
                        <th width="80"></th>
                        <th width="80"></th>
                        <?
                    }
                    ?>
                    <th width="70"></th>
                    <th width="110"></th>
                    <th width="80"></th>
                    <th width="100" id="value_tot_yarn_rec"><? echo number_format($tot_mkt_required, 2); ?></th>
                    
                    
                </tr>
            </tfoot>
        </table>
        
        
    </fieldset>

    <?

    $emailBody=ob_get_contents();
    ob_clean();
    $to='';
    $sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=62 and b.mail_user_setup_id=c.id and a.company_id=$compid AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
    $mail_sql=sql_select($sql);
    $mailArr=array();
    foreach($mail_sql as $row)
    {
        $mailArr[$row[csf('email_address')]]=$row[csf('email_address')]; 
    }
    $to=implode(',',$mailArr);
    $subject="Order List Without Fabric Booking";

    if($_REQUEST['isview']==1){
        echo $emailBody;
    }
    else{
        if($to!="")echo sendMailMailer( $to, $subject, $emailBody );

    }



}
  

?>