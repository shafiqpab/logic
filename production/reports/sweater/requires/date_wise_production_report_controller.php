<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

if ($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
    exit();
}

if ($action=="report_button_setting")
{
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=11 and report_id=62 and is_deleted=0 and status_active=1");
    echo "print_report_button_setting('$print_report_format');\n"; 
}

if($action=="job_no_popup")
{
    echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
    extract($_REQUEST);
    //echo $txt_job_no;
    ?>
    <script>
    
        function js_set_value(str)
        {
            var splitData = str.split("_");
            //alert (splitData[1]);
            $("#hide_job_id").val(splitData[0]); 
            $("#hide_job_no").val(splitData[1]); 
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
        <fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>                   <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                    <tr>
                        <td align="center">
                             <? 
                                echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
                            ?>
                        </td>                 
                        <td align="center"> 
                        <?
                            $search_by_arr=array(1=>"Job No",2=>"Style Ref");
                            $dd="change_search_event(this.value, '0*0', '0*0', '../../') ";                         
                            echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                        </td>     
                        <td align="center" id="search_by_td">               
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? echo $txt_job_no?>" />  
                        </td>   
                        <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $type; ?>', 'create_job_no_search_list_view', 'search_div', 'date_wise_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
                </tbody>
            </table>
            <div id="search_div"></div>
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
    $month_id=$data[5];
    $type_id=$data[6];
    //echo $type_id;
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    if($data[1]==0)
    {
        if ($_SESSION['logic_erp']["data_level_secured"]==1)
        {
            if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
        }
        else $buyer_id_cond="";
    }
    else $buyer_id_cond=" and a.buyer_name=$data[1]";
    
    $search_by=$data[2];
    $search_string=trim($data[3]);
    $search_cond="";
    if($search_string!="")
    {
        if($search_by==2) $search_cond=" and a.style_ref_no='$data[3]'";
        elseif($search_by==1) $search_cond="and a.job_no_prefix_num=$data[3]";
    }
    //$year="year(insert_date)";
    if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
    else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
    else $year_field="";
    
    if($db_type==0)
    {
        if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond=""; 
    }
    else if($db_type==2)
    {
        $year_field_con=" and to_char(a.insert_date,'YYYY')";
        if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";  
    }
    //if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
    if($type_id==1) $job_cond="id,job_no_prefix_num";
    else if($type_id==2) $job_cond="id,style_ref_no";
    $arr=array (0=>$company_arr,1=>$buyer_arr);
    if($type_id==1 || $type_id==2 )
    {
        $sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field from wo_po_details_master a where a.garments_nature=100 and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $year_cond  order by a.id DESC";
    
        echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "$job_cond", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
    }
    else
    {
          $sql= "select a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name,a.style_ref_no, $year_field,b.id,b.po_number from wo_po_details_master a,wo_po_break_down b where  a.job_no=b.job_no_mst and a.garments_nature=100 and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $year_cond  order by a.id DESC";
        echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,PO No", "120,130,80,60,100","700","240",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0,0','') ;
    }
    exit(); 
} // Job Search end

if($action=="report_generate")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
            
    $reporttype         = str_replace("'","",$reporttype);
    $cbo_company_name   = str_replace("'","",$cbo_company_name);
    $cbo_wo_company_name= str_replace("'","",$cbo_wo_company_name);
    $txt_style_ref      = str_replace("'","",$txt_style_ref);
    $cbo_buyer_name     = str_replace("'","",$cbo_buyer_name);
    $job_no             = str_replace("'","",$txt_job_no);
    $job_no_id          = str_replace("'","",$txt_job_no_id);
    $year_id            = str_replace("'","",$cbo_year);
    $ship_status        = str_replace("'","",$cbo_ship_status);
    $txt_date           = str_replace("'","",$txt_date);
            
    if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";
    
    if($cbo_buyer_name==0)
    {
        if ($_SESSION['logic_erp']["data_level_secured"]==1)
        {
            if($_SESSION['logic_erp']["buyer_id"]!="")
            {
                $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
            }
            else  $buyer_id_cond="";
        }
        else $buyer_id_cond="";
    }
    else
    {
        $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
    }
            
    if($db_type==0)
    {
        if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond=""; 
    }
    else if($db_type==2)
    {
        $year_field_con=" and to_char(a.insert_date,'YYYY')";
        if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";  
    }
    $ship_status_color="";
    if($ship_status==2)
    {
        $ship_status_color="#FFC0CB";
    }
        
    $job_style_cond="";
    if(trim(str_replace("'","",$txt_style_ref))!="") $job_style_cond=" and a.style_ref_no = '".trim(str_replace("'","",$txt_style_ref))."'";
            
    $order_cond="";
    if(trim(str_replace("'","",$txt_order))!="")
    {
        if(str_replace("'","",$txt_order_id)!="")
        {
            $order_cond=" and b.id in(".str_replace("'","",$txt_order_id).")";
        }
        else
        {
            $order_cond=" and b.po_number = '".trim(str_replace("'","",$txt_order))."'";
        }
    }
    $job_cond="";
    if(trim(str_replace("'","",$job_no))!="")
    {
        if(str_replace("'","",$job_no_id)!="")
        {
            $job_cond=" and a.id in(".str_replace("'","",$job_no_id).")";
        }
        else
        {
            $job_cond=" and a.job_no_prefix_num = '".trim(str_replace("'","",$job_no))."'";
        }
    }
    
    $ship_status_cond="";
    if($ship_status==1) $ship_status_cond="and b.shiping_status in (1,2)"; else if($ship_status==2) $ship_status_cond="and b.shiping_status in (3)";
    $date_cond="";
    if($txt_date!="")
    {
		
        $date_cond.=" and c.PRODUCTION_DATE ='$txt_date'";
    }
    
    ob_start();
    
    $buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
    $supplier_arr=return_library_array("select id,supplier_name from  lib_supplier","id","supplier_name");
    $season_arr=return_library_array("select id,season_name from  lib_buyer_season","id","season_name");
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
    $cm_cost_library=return_library_array( "select JOB_NO,CM_COST from wo_pre_cost_dtls", "JOB_NO", "CM_COST");
    $imge_arr=return_library_array( "SELECT master_tble_id,image_location from common_photo_library where file_type=1 and form_name='knit_order_entry'",'master_tble_id','image_location');

   
    
   
    $today_color="#FFFF00";

    if($reporttype==1)
    {

                /* =============================================================================================/
            /                                        Main Query                                             /
            / ============================================================================================ */
            $sql="SELECT a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, a.GAUGE, a.job_no_prefix_num as JOB_PREFIX, a.JOB_NO, a.ORDER_UOM, a.total_set_qnty as RATIO,a.season_buyer_wise as SEASON,a.AVG_UNIT_PRICE, b.id as PO_ID, b.PO_NUMBER, b.PO_QUANTITY, b.PLAN_CUT, b.EXCESS_CUT, b.SHIPING_STATUS, b.PUB_SHIPMENT_DATE,c.PRODUCTION_SOURCE,c.PRODUCTION_DATE,c.SERVING_COMPANY, c.PRODUCTION_TYPE, c.production_quantity as PRODUCTION_QUANTITY, c.RE_PRODUCTION_QTY
            from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id
            and b.id=c.po_break_down_id and a.garments_nature=100 
            and c.production_type in (1,3,4,5,7,8,11,67,111,112,114) 
            and a.status_active=1 and a.is_deleted=0 
            and b.status_active=1 and b.is_deleted=0 
            and c.status_active=1 and c.is_deleted=0 
            $company_name_cond $buyer_id_cond $date_cond $job_style_cond $order_cond $job_cond $year_cond $ship_status_cond order by a.id ASC";
        //echo $sql;die;
        $sql_result=sql_select($sql);     
        if(count($sql_result)==0)
        {
            ?>
            <div style="text-align: center;color:red;font-weight:bold">Data not found. Please check budget and production.</div>
            <?
            die;
        } 
        $po_arr=array(); 
        $po_arr_sub=array(); 
        $production_arr=array(); 
        $production_arr_sub=array(); 
        $production_summary_arr=array(); 
        $production_summary_arr_sub=array(); 
        $production_summary_tot_arr=array(); 
        $order_qty_arr=array(); 
        $tot_rows=0; 
        $poId_Arr=array();
        $job_no_arr=array();
        foreach($sql_result as $row)
        {
            $tot_rows++;
            $poId_Arr[$row["PO_ID"]] = $row["PO_ID"];
            if($row["PRODUCTION_SOURCE"]==1) // inhouse
            {
                $po_arr[$row["JOB_NO"]]=$row["BUYER_NAME"].'___'.$row["STYLE_REF_NO"].'___'.$row["GAUGE"].'___'.$row["JOB_NO_PREFIX_NUM"].'___'.$row["JOB_NO"].'___'.$row["PO_NUMBER"].'___'.$row["PO_QUANTITY"].'___'.$row["PLAN_CUT"].'___'.$row["EXCESS_CUT"].'___'.$row["SHIPING_STATUS"].'___'.$row["PUB_SHIPMENT_DATE"].'___'.$row["PRODUCTION_DATE"].'___'.$row["COMPANY_NAME"].'___'.$row["SERVING_COMPANY"].'___'.$row["SEASON"];
                
                $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['g'] += $row["PRODUCTION_QUANTITY"];
                $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['r'] += $row["RE_PRODUCTION_QTY"];

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
                {
                    $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['today'] += $row["PRODUCTION_QUANTITY"];
                    $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['today_re'] += $row["RE_PRODUCTION_QTY"];
                }
                $prev_day = "";
                $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($prev_day))
                {
                    $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['yesterday'] += $row["PRODUCTION_QUANTITY"];
                    $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['yesterday_re'] += $row["RE_PRODUCTION_QTY"];
                }

                $production_summary_arr[$row["PRODUCTION_TYPE"]]['g'] += $row["PRODUCTION_QUANTITY"];
                $production_summary_arr[$row["PRODUCTION_TYPE"]]['r'] += $row["RE_PRODUCTION_QTY"];
                $order_qty_arr[$row["JOB_NO"]]['in_order_qty'] += $row["PO_QUANTITY"];
                $order_qty_arr[$row["JOB_NO"]]['in_excess_cut'] += $row["EXCESS_CUT"];
                $order_qty_arr[$row["JOB_NO"]]['in_plan_cut'] += $row["PLAN_CUT"];
            }
            else
            {
                $po_arr_sub[$row["SERVING_COMPANY"]][$row["JOB_NO"]]=$row["BUYER_NAME"].'___'.$row["STYLE_REF_NO"].'___'.$row["GAUGE"].'___'.$row["JOB_NO_PREFIX_NUM"].'___'.$row["JOB_NO"].'___'.$row["PO_NUMBER"].'___'.$row["PO_QUANTITY"].'___'.$row["PLAN_CUT"].'___'.$row["EXCESS_CUT"].'___'.$row["SHIPING_STATUS"].'___'.$row["PUB_SHIPMENT_DATE"].'___'.$row["PRODUCTION_DATE"].'___'.$row["COMPANY_NAME"].'___'.$row["SERVING_COMPANY"].'___'.$row["SEASON"];
                
                $production_arr_sub[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['g'] += $row["PRODUCTION_QUANTITY"];
                $production_arr_sub[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['r'] += $row["RE_PRODUCTION_QTY"];

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
                {
                    $production_arr[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['today'] += $row["PRODUCTION_QUANTITY"];
                    $production_arr[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['today_re'] += $row["RE_PRODUCTION_QTY"];
                }
                $prev_day = "";
                $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($prev_day))
                {
                    $production_arr[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['yesterday'] += $row["PRODUCTION_QUANTITY"];
                    $production_arr[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['yesterday_re'] += $row["RE_PRODUCTION_QTY"];
                }


                $production_summary_arr_sub[$row["PRODUCTION_TYPE"]]['g'] += $row["PRODUCTION_QUANTITY"];
                $production_summary_arr_sub[$row["PRODUCTION_TYPE"]]['r'] += $row["PRODUCTION_QUANTITY"];
                $order_qty_arr[$row["JOB_NO"]]['out_order_qty'] += $row["PO_QUANTITY"];
                $order_qty_arr[$row["JOB_NO"]]['out_excess_cut'] += $row["EXCESS_CUT"];
                $order_qty_arr[$row["JOB_NO"]]['out_plan_cut'] += $row["PLAN_CUT"];
            }
            $production_summary_tot_arr[$row["PRODUCTION_TYPE"]]['g'] += $row["PRODUCTION_QUANTITY"];
            $production_summary_tot_arr[$row["PRODUCTION_TYPE"]]['r'] += $row["RE_PRODUCTION_QTY"];

            array_push($job_no_arr, $row["JOB_NO"]);
        }
        unset($sql_result);

        $po_id_list_arr=array_chunk($poId_Arr,999);
        $poIds_cond = " and ";
        $p=1;
        foreach($po_id_list_arr as $poids)
        {
            if($p==1) 
            {
                $poIds_cond .="  ( po_break_down_id in(".implode(',',$poids).")"; 
            }
            else
            {
              $poIds_cond .=" or po_break_down_id in(".implode(',',$poids).")";
            }
            $p++;
        }
        $poIds_cond .=")";
        unset($poId_Arr);

        /* =============================================================================================/
        /                                        Order Quantity                                        /
        / ============================================================================================ */
        $po_id = str_replace("po_break_down_id", "id", $poIds_cond);
        $sql_order = "SELECT JOB_NO_MST, PO_QUANTITY,PLAN_CUT,EXCESS_CUT from wo_po_break_down where status_active=1";// $po_id
        $order_res = sql_select($sql_order);
        $job_data_array = array();
        foreach ($order_res as $val) 
        {
            $job_data_array[$val['JOB_NO_MST']]['po_quantity'] += $val['PO_QUANTITY'];
            $job_data_array[$val['JOB_NO_MST']]['plan_cut'] += $val['PLAN_CUT'];
            $job_data_array[$val['JOB_NO_MST']]['excess_cut'] += $val['EXCESS_CUT'];
        }

        /* =============================================================================================/
        /                                        Inspection Data                                        /
        / ============================================================================================ */
        $inspection_arr=array();
        $inspection_arr_sub=array();
        $inspection_summary=array();
        $inspection_total = 0;
        $sql_ins="SELECT JOB_NO,PO_BREAK_DOWN_ID,SOURCE,INSPECTION_DATE, INSPECTION_QNTY from pro_buyer_inspection where status_active=1 and is_deleted=0 $poIds_cond";
        $sql_ins_result=sql_select($sql_ins);
        foreach($sql_ins_result as $row)
        {
            if($row["SOURCE"]==1)
            {
                $inspection_arr[$row["JOB_NO"]]['ins'] += $row["INSPECTION_QNTY"];
                $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));

                if(strtotime($row["INSPECTION_DATE"])==strtotime($txt_date))
                {
                    $inspection_arr[$row["JOB_NO"]]['today'] += $row["INSPECTION_QNTY"];
                }
                if(strtotime($row["INSPECTION_DATE"])==strtotime($prev_day))
                {
                    $inspection_arr[$row["JOB_NO"]]['yesterday'] += $row["INSPECTION_QNTY"];
                }
            }
            else
            {
                $inspection_arr_sub[$row["JOB_NO"]]['ins'] += $row["INSPECTION_QNTY"];
                if(strtotime($row["INSPECTION_DATE"])==strtotime($txt_date))
                {
                    $inspection_arr[$row["JOB_NO"]]['today'] += $row["INSPECTION_QNTY"];
                }
                if(strtotime($row["INSPECTION_DATE"])==strtotime($prev_day))
                {
                    $inspection_arr[$row["JOB_NO"]]['yesterday'] += $row["INSPECTION_QNTY"];
                }
            }
            $inspection_summary[$row["SOURCE"]] += $row["INSPECTION_QNTY"];
            $inspection_total += $row["INSPECTION_QNTY"];
        }
        unset($sql_ins_result);

        ?>
        <fieldset>
            <style type="text/css">
                table tr th{word-wrap: break-word;word-break:break-all;}
            </style>
            <!-- ============================================ heading part =========================== -->
            <div>
                <table width="4750" cellspacing="0" >
                    <tr class="form_caption" style="border:none;">
                        <td colspan="67" align="center" style="border:none;font-size:16px; font-weight:bold" ><?  echo $report_title; ?></td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="67" align="center" style="border:none; font-size:14px;">
                            Company Name : <? echo $company_library[$cbo_company_name]; ?>                                
                        </td>
                    </tr>
                    <tr style="border:none;">
                        <td colspan="67" align="center" style="border:none;font-size:12px; font-weight:bold">
                            <?
                                if(str_replace("'","",trim($txt_date))!="")
                                {
                                    echo "Date : $txt_date";
                                }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- ============================================ summary part ============================== -->
            <div style="width:1300px;padding:10px 0">
                <table width="1300" cellspacing="0" border="1" class="rpt_table" rules="all">
                    <caption style="font-size:18px;font-weight:bold;justify-content: left;text-align: left;">Summary</caption>
                    <thead>
                        <th width="100">Summery</th>
                        <th width="100">Knitting</th>
                        <th width="100">In Line Inspection</th>
                        <th width="100">Linking</th>
                        <th width="100">Trimming Complete</th>
                        <th width="100">Mending Complete</th>
                        <th width="100">Wash</th>
                        <th width="100">Attachment</th>
                        <th width="100">Sewing</th>
                        <th width="100">PQC</th>
                        <th width="100">Iron</th>
                        <th width="100">Re-Iron</th>
                        <th width="100">Packing</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Inhouse Production</td>
                            <td align="right"><? echo number_format($production_summary_arr[1]['g'],0);?></td>
                            <td align="right"><? echo number_format($inspection_summary[1],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[4]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[111]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[112]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[3]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[11]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[5]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[114]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[67]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[67]['r'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[8]['g'],0);?></td>
                        </tr>
                        <tr>
                            <td>Subcon Production</td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[1]['g'],0);?></td>
                            <td align="right"><? echo number_format($inspection_summary[3],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[4]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[111]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[112]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[3]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[11]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[5]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[114]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[67]['g'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[67]['r'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[8]['g'],0);?></td>
                        </tr>
                    </tbody>
                    <tfoot>                    
                        <tr>
                            <th>Total Production</td>
                            <th align="right"><? echo number_format($production_summary_tot_arr[1]['g'],0);?></th>
                            <th align="right"><? echo number_format($inspection_total,0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[4]['g'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[111]['g'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[112]['g'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[3]['g'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[11]['g'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[5]['g'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[114]['g'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[67]['g'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[67]['r'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[8]['g'],0);?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- =========================================== inhouse data ====================================== -->
            <div>
                <table width="4750" cellspacing="0" border="1" class="rpt_table" rules="all" id="">
                    <caption style="font-size:18px;font-weight:bold;justify-content: left;text-align: left;">In-House</caption>
                    <thead>
                        <tr>
                            <th rowspan="2" width="30">Sl.</th> 
                            <th width="800" colspan="9">Order Details</th>
                            <th width="280" colspan="4">Knitting Production</th>
                            <th width="350" colspan="5">In Line Inspection</th>
                            <th width="350" colspan="5">Linking Production</th>
                            <th width="350" colspan="5">Trimming Complete</th>
                            <th width="350" colspan="5">Mending Complete</th>
                            <th width="350" colspan="5">Wash Production</th>
                            <th width="350" colspan="5">Attachment Production</th>
                            <th width="350" colspan="5">Sewing Prodution</th>
                            <th width="350" colspan="5">PQC</th>
                            <th width="420" colspan="6">Iron Production</th>
                            <th width="420" colspan="6">Packing and Finishing</th>
                        </tr>
                        <tr>
                            <th width="100">Company</th>
                            <th width="100">Working Company</th>
                            <th width="100">Buyer</th>
                            <th width="100">Style</th>
                            <th width="100">Season</th>
                            <th width="80">GG</th>
                            <th width="80">Order Qty</th>
                            <th width="80">Plan Cut Qty</th>
                            <th width="60">Ex. Cut %</th>
                            
                            <th width="70">Yesterday Knit. Complete</th>
                            <th width="70">Today Knit. Complete</th>
                            <th width="70">Total Knitting</th>
                            <th width="70">Knitting Balance</th>
                            
                            <th width="70">Inspection WIP</th>
                            <th width="70">Yesterday In Line Inspection Complete</th>
                            <th width="70">Today In Line Inspection</th>
                            <th width="70">Total  In Line Inspection</th>
                            <th width="70">In Line Inspection Balance</th>
                            
                            <th width="70">Linking WIP </th>
                            <th width="70">Yesterday Linking Complete</th>
                            <th width="70">Today Linking Complete</th>
                            <th width="70">Total Linking Complete</th>
                            <th width="70">Linking Balance</th>
                            
                            <th width="70">Trimming WIP </th>
                            <th width="70">Yesterday Trimming Complete</th>
                            <th width="70">Today Trimming Complete</th>
                            <th width="70">Total Trimming Complete</th>
                            <th width="70">Trimming Balance</th>
                            
                            <th width="70">Mending WIP </th>
                            <th width="70">Yesterday Mending Complete</th>
                            <th width="70">Today Mending Complete</th>
                            <th width="70">Total Mending Complete</th>
                            <th width="70">Mending Balance</th>
                            
                            <th width="70">Wash WIP</th>
                            <th width="70">Yesterday Wash Complete</th>
                            <th width="70">Today Wash Complete</th>
                            <th width="70">Total Wash Complete</th>
                            <th width="70">Wash Balance</th>
                            
                            <th width="70">Attachment WIP</th>
                            <th width="70">Yesterday Attachment Complete</th>
                            <th width="70">Today Attachment Complete</th>
                            <th width="70">Total Attach. Complete</th>
                            <th width="70">Attcah. Balance</th>
                            
                            <th width="70">Sewing WIP</th>
                            <th width="70">Yesterday Sewing Complete</th>
                            <th width="70">Today Sewing Complete</th>
                            <th width="70">Sewing Complete</th>
                            <th width="70">Sewing Balance</th>
                            
                            <th width="70">PQC WIP</th>
                            <th width="70">Yesterday PQC Complete</th>
                            <th width="70">Today PQC Complete</th>
                            <th width="70">PQC Complete</th>
                            <th width="70">PQC Balance</th>
                            
                            <th width="70">Iron WIP</th>
                            <th width="70">Yesterday Iron Complete</th>
                            <th width="70">Today Iron Complete</th>
                            <th width="70">Total Iron Complete</th>
                            <th width="70">Iron Balance</th>
                            <th width="70">Re-Iron</th>
                            
                            <th width="70">Yesterday Packing Complete</th>
                            <th width="70">Today Packing Complete</th>
                            <th width="70">Total Packing Complete</th>
                            <th width="70">Packing Balance</th>
                            <th width="70">Packing Complete %</th>
                            <th width="70">Status</th>
                        </tr>
                    </thead>
                </table>
                <div style="max-height:380px; overflow-y:scroll; width:4770px" id="scroll_body">
                    <table cellspacing="0" border="1" class="rpt_table" width="4750" id="table_body" rules="all">
                    <? 
                        $i=1;
                        $tot_knitting_yesterday = 0;
                        $tot_knitting_today = 0;
                        $tot_knitting_com = 0;
                        $tot_kintting_bal = 0;
                        $tot_knitting_wip = 0;

                        $tot_inspection_yesterday = 0;
                        $tot_inspection_today = 0;
                        $tot_inspection_qnty = 0;
                        $tot_inspe_bal = 0;
                        $tot_ins_wip = 0;

                        $tot_linking_yesterday = 0;
                        $tot_linking_today = 0;
                        $tot_linking_qnty = 0;
                        $tot_linking_bal = 0;
                        $tot_linking_wip = 0;

                        $tot_triming_yesterday = 0;
                        $tot_triming_today = 0;
                        $tot_triming_qnty = 0;
                        $tot_triming_bal = 0;
                        $tot_triming_wip = 0;

                        $tot_mending_yesterday = 0;
                        $tot_mending_today = 0;
                        $tot_mending_qnty = 0;
                        $tot_mending_bal = 0;
                        $tot_mending_wip = 0;
                        
                        $tot_wash_yesterday = 0;
                        $tot_wash_today = 0;
                        $tot_wash_comp = 0;
                        $tot_wash_bal = 0;
                        $tot_wash_wip = 0;
                        
                        $tot_attach_yesterday = 0;
                        $tot_attach_today = 0;
                        $tot_attach_comm = 0;
                        $tot_attach_bal = 0;
                        $tot_attach_wip = 0;
                        
                        $tot_sewing_yesterday = 0;
                        $tot_sewing_today = 0;
                        $tot_sewing_comm = 0;
                        $tot_sewing_bal = 0;
                        $tot_sewing_wip = 0;
                        
                        $tot_pqc_yesterday = 0;
                        $tot_pqc_today = 0;
                        $tot_pqc_comp = 0;
                        $tot_pqc_bal = 0;
                        $tot_pqc_wip = 0;
                        
                        $tot_iron_wip = 0;
                        $tot_iron_yesterday = 0;
                        $tot_iron_today = 0;
                        $tot_iron_com = 0;
                        $tot_iron_bal = 0;
                        $tot_re_iron = 0;
                        
                        $tot_packing_comm = 0;
                        $tot_packing_bal = 0;
                        $tot_shipment_com = 0;
                        $tot_shipment_acc_bal = 0;
                        foreach($po_arr as $job_no=>$value)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $buyer_name=$style_ref_no=$gauge=$job_no_prefix_num=$job_no=$po_number=$shiping_status=""; $pubshipment_date="";
                            $po_qty=$plan_cut=$excess_cut=0;
                            $exdata=explode("___",$value);
                            $buyer_name=$exdata[0];
                            $buyer_name=$exdata[0];
                            $style_ref_no=$exdata[1];
                            $gauge=$exdata[2];
                            $job_no_prefix_num=$exdata[3];
                            $job_no=$exdata[4];
                            $po_number=$exdata[5];
                            
                            $po_qty=$job_data_array[$job_no]['po_quantity'];
                            $plan_cut=$job_data_array[$job_no]['plan_cut'];
                            $excess_cut=$job_data_array[$job_no]['excess_cut'];
                            
                            $shiping_status=$exdata[9];
                            $pubshipment_date=$exdata[10];
                            $production_date=$exdata[11];
                            $company_name=$exdata[12];
                            $wo_company_name=$exdata[13];
                            $season=$exdata[14];

                            $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));
                            
                            $knitting_com=$inspection_qnty=$wash_comp=$attach_comm=$sewing_comm=$iron_com=$re_iron=$packing_comm=$shipment_com=$linking_comp=$mending_comm=$triming_comm=$pqc_com=0;
                            $knitting_com=$production_arr[$job_no][1]['g'];
                            $inspection_qnty=$inspection_arr[$job_no]['ins'];
                            $linking_comp=$production_arr[$job_no][4]['g'];
                            $wash_comp=$production_arr[$job_no][3]['g'];
                            $attach_comm=$production_arr[$job_no][11]['g'];
                            $sewing_comm=$production_arr[$job_no][5]['g'];
                            $iron_com=$production_arr[$job_no][7]['g']+$production_arr[$job_no][67]['g'];
                            $re_iron=$production_arr[$job_no][7]['r']+$production_arr[$job_no][67]['r'];
                            $packing_comm=$production_arr[$job_no][8]['g'];
                            $iron_n_comm=$production_arr[$job_no][67]['g'];
                            $re_iron_n_comm=$production_arr[$job_no][67]['r'];
                            $triming_comm=$production_arr[$job_no][111]['g'];
                            $mending_comm=$production_arr[$job_no][112]['g'];
                            $pqc_com=$production_arr[$job_no][114]['g'];
                            // ================================ balance ============================
                            $kintting_bal=$inspe_bal=$makeup_bal=$wash_bal=$attach_bal=$sewing_bal=$iron_bal=$packing_bal=$shipment_acc_bal=$linking_bal=$mending_bal=$triming_bal=$pqc_bal=0;
                            $kintting_bal=$plan_cut-$knitting_com;
                            $inspe_bal=$plan_cut-$inspection_qnty;
                            $linking_bal=$plan_cut-$linking_comp;
                            $triming_bal=$plan_cut-$triming_comm;
                            $mending_bal=$plan_cut-$mending_comm;
                            $pqc_bal=$plan_cut-$pqc_com;
                            $wash_bal=$plan_cut-$wash_comp;
                            $attach_bal=$plan_cut-$attach_comm;
                            $sewing_bal=$plan_cut-$sewing_comm;
                            $iron_bal=$plan_cut-$iron_com;
                            $packing_bal=$plan_cut-$packing_comm;
                            $shipment_acc_bal=$po_qty-$shipment_com;
                            // ================================== wip ===============================
                            $knitting_wip=$ins_wip=$makeup_wip=$wash_wip=$attach_wip=$sewing_wip=$packing_percent=$linking_wip=$mending_wip=$triming_wip=$pqc_wip=0;
                            
                            $ins_wip=$knitting_com-$inspection_qnty;
                            $linking_wip=$inspection_qnty-$linking_comp;
                            $triming_wip=$linking_comp-$triming_comm;
                            $mending_wip=$triming_comm-$mending_comm;
                            $mending_wip=$triming_comm-$mending_comm;
                            $wash_wip=$mending_comm-$wash_comp;
                            $attach_wip=$wash_comp-$attach_comm;
                            $sewing_wip=$attach_comm-$sewing_comm;
                            $pqc_wip=$sewing_comm-$pqc_com;
                            $iron_wip=$pqc_com-$iron_com;
                            $packing_percent=($packing_comm/$po_qty)*100;
                            // ================================== today prod =======================================
                            $knitting_com_today=$inspection_qnty_today=$linking_comp_today=$wash_comp_today=$attach_comm_today=$sewing_comm_today=$iron_com_today=$re_iron_today=$packing_comm_today=$shipment_com_today=$pqc_com_today=0;
                            // if(strtotime($production_date) == strtotime($txt_date))
                            // {
                                $knitting_com_today     = $production_arr[$job_no][1]['today'];
                                $inspection_qnty_today  = $inspection_arr[$job_no]['today'];
                                $linking_comp_today      = $production_arr[$job_no][4]['today'];
                                $wash_comp_today        = $production_arr[$job_no][3]['today'];
                                $attach_comm_today      = $production_arr[$job_no][11]['today'];
                                $sewing_comm_today      = $production_arr[$job_no][5]['today'];
                                $iron_com_today         = $production_arr[$job_no][7]['today']+$production_arr[$job_no][67]['today'];
                                $re_iron_today          = $production_arr[$job_no][7]['today_re']+$production_arr[$job_no][67]['today_re'];
                                $packing_comm_today     = $production_arr[$job_no][8]['today'];
                                $iron_n_comm_today      = $production_arr[$job_no][67]['today'];
                                $re_iron_n_comm_today   = $production_arr[$job_no][67]['today_re'];
                                $triming_comm_today     = $production_arr[$job_no][111]['today'];
                                $mending_comm_today     = $production_arr[$job_no][112]['today'];
                                $pqc_com_today         = $production_arr[$job_no][114]['today'];
                            // }
                            // ===================================== prev day prod ======================================
                            $knitting_com_yestarday=$inspection_qnty_yestarday=$linking_comp_yestarday=$wash_comp_yestarday=$attach_comm_yestarday=$sewing_comm_yestarday=$iron_com_yestarday=$re_iron_yestarday=$packing_comm_yestarday=$shipment_com_yestarday=$pqc_com_yestarday=0;
                            // if(strtotime($production_date) == strtotime($prev_day))
                            // {
                                $knitting_com_yestarday     = $production_arr[$job_no][1]['yesterday'];
                                $inspection_qnty_yestarday  = $inspection_arr[$job_no]['yesterday'];
                                $linking_comp_yestarday      = $production_arr[$job_no][4]['yesterday'];
                                $wash_comp_yestarday        = $production_arr[$job_no][3]['yesterday'];
                                $attach_comm_yestarday      = $production_arr[$job_no][11]['yesterday'];
                                $sewing_comm_yestarday      = $production_arr[$job_no][5]['yesterday'];
                                $iron_com_yestarday         = $production_arr[$job_no][7]['yesterday']+$production_arr[$job_no][67]['yesterday'];
                                $re_iron_yestarday          = $production_arr[$job_no][7]['yesterday_re']+$production_arr[$job_no][67]['yesterday_re'];
                                $packing_comm_yestarday     = $production_arr[$job_no][8]['yesterday'];
                                $iron_n_comm_yestarday      = $production_arr[$job_no][67]['yesterday'];
                                $re_iron_n_comm_yestarday   = $production_arr[$job_no][67]['yesterday_re'];
                                $triming_comm_yestarday     = $production_arr[$job_no][111]['yesterday'];
                                $mending_comm_yestarday     = $production_arr[$job_no][112]['yesterday'];
                                $pqc_com_yestarday          = $production_arr[$job_no][114]['yesterday'];
                            // }
                            
                            $packing_status="Running"; $shpment_date='';
                            $ship_status_color="";
                            
                            if($packing_comm>=$plan_cut)
                            {
                                $packing_status="Complete";

                                 $ship_status_color="#FFA500";
                            }  
                            else if($packing_comm<$plan_cut){
                                $packing_status="Running";
                                // $ship_status_color="#FFC0CB";
                            }
                            $bgcl="#FFFF00"; 
                                                
                            ?>
                             <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>    
                                <td width="100" style="word-break:break-all"><? echo $company_library[$company_name]; ?>&nbsp;</td>
                                <td width="100" style="word-break:break-all"><? echo $company_library[$wo_company_name]; ?>&nbsp;</td>
                                <td width="100" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?>&nbsp;</td>
                                <td width="100" style="word-break:break-all"><? echo $style_ref_no; ?>&nbsp;</td>
                                <td width="100" style="word-break:break-all"><? echo $season_arr[$season]; ?>&nbsp;</td>
                                <td width="80" style="word-break:break-all"><? echo $gauge_arr[$gauge]; ?>&nbsp;</td>
                                <td width="80" align="right"><? echo $po_qty; ?></td>
                                <td width="80" align="right"><? echo $plan_cut; ?></td>
                                <td width="60" align="right"><? echo number_format($excess_cut,0); ?></td>                        
                                
                                <td width="70" align="right"><? echo $knitting_com_yestarday; ?></td>

                                <?php if(!empty($knitting_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>"><? echo $knitting_com_today; ?></td>
                                <td width="70" align="right"><? echo $knitting_com; ?></td>
                                <td width="70" align="right"><? echo $kintting_bal; ?></td>
                                
                                <td width="70" align="right"><? echo $ins_wip; ?></td>
                                <td width="70" align="right"><? echo $inspection_qnty_yestarday; ?></td>

                                <?php if(!empty($inspection_qnty_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>"><? echo $inspection_qnty_today; ?></td>
                                <td width="70" align="right"><? echo $inspection_qnty; ?></td>
                                <td width="70" align="right"><? echo $inspe_bal; ?></td>
                                
                                <td width="70" align="right"><? echo $linking_wip; ?></td>
                                <td width="70" align="right"><? echo $linking_comp_yestarday; ?></td>
                                <?php if(!empty($linking_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $linking_comp_today; ?></td>
                                <td width="70" align="right"><? echo $linking_comp; ?></td>
                                <td width="70" align="right"><? echo $linking_bal; ?></td>
                                
                                <td width="70" align="right"><? echo $triming_wip; ?></td>
                                <td width="70" align="right"><? echo $triming_comm_yestarday; ?></td>

                                 <?php if(!empty($triming_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $triming_comm_today; ?></td>
                                <td width="70" align="right"><? echo $triming_comm; ?></td>
                                <td width="70" align="right"><? echo $triming_bal; ?></td>
                                
                                <td width="70" align="right"><? echo $mending_wip; ?></td>
                                <td width="70" align="right"><? echo $mending_comm_yestarday; ?></td>

                                <?php if(!empty($mending_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $mending_comm_today; ?></td>
                                <td width="70" align="right"><? echo $mending_comm; ?></td>
                                <td width="70" align="right"><? echo $mending_bal; ?></td>
                                
                                <td width="70" align="right"><? echo $wash_wip; ?></td>
                                <td width="70" align="right"><? echo $wash_comp_yestarday; ?></td>

                                <?php if(!empty($wash_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $wash_comp_today; ?></td>
                                <td width="70" align="right"><? echo $wash_comp; ?></td>
                                <td width="70" align="right"><? echo $wash_bal; ?></td>
                                
                                <td width="70" align="right"><? echo $attach_wip; ?></td>
                                <td width="70" align="right"><? echo $attach_comm_yestarday; ?></td>

                                 <?php if(!empty($attach_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $attach_comm_today; ?></td>
                                <td width="70" align="right"><? echo $attach_comm; ?></td>
                                <td width="70" align="right"><? echo $attach_bal; ?></td>
                                
                                <td width="70" align="right"><? echo $sewing_wip; ?></td>
                                <td width="70" align="right"><? echo $sewing_comm_yestarday; ?></td>

                                 <?php if(!empty($sewing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $sewing_comm_today; ?></td>
                                <td width="70" align="right"><? echo $sewing_comm; ?></td>
                                <td width="70" align="right"><? echo $sewing_bal; ?></td>
                                
                                <td width="70" align="right"><? echo $pqc_wip; ?></td>
                                <td width="70" align="right"><? echo $pqc_com_yestarday; ?></td>

                                 <?php if(!empty($pqc_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $pqc_com_today; ?></td>
                                <td width="70" align="right"><? echo $pqc_com; ?></td>
                                <td width="70" align="right"><? echo $pqc_bal; ?></td>
                                
                                <td width="70" align="right"><? echo $iron_wip; ?></td>
                                <td width="70" align="right"><? echo $iron_com_yestarday; ?></td>

                                 <?php if(!empty($iron_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $iron_com_today; ?></td>
                                <td width="70" align="right"><? echo $iron_com; ?></td>
                                <td width="70" align="right"><? echo $iron_bal; ?></td>
                                <td width="70" align="right"><? echo $re_iron; ?></td>
                                
                                <td width="70" align="right"><? echo $packing_comm_yestarday; ?></td>

                                 <?php if(!empty($packing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $packing_comm_today; ?></td>
                                <td width="70" align="right"><? echo $packing_comm; ?></td>
                                <td width="70" align="right"><? echo $packing_bal; ?></td>
                                <td width="70" align="right"><? echo number_format($packing_percent,2); ?></td>
                                <td width="70" align="left" bgcolor="<?php echo $ship_status_color ?>"><? echo $packing_status; ?></td>
                            </tr>
                            <?
                            $i++;
                            $tot_knitting_yesterday += $knitting_com_yestarday;
                            $tot_knitting_today += $knitting_com_today;
                            $tot_knitting_com+=$knitting_com;
                            $tot_kintting_bal+=$kintting_bal;
                            $tot_knitting_wip+=$knitting_wip;
                            
                            $tot_inspection_yesterday+=$inspection_qnty_yestarday;
                            $tot_inspection_today+=$inspection_qnty_today;
                            $tot_inspection_qnty+=$inspection_qnty;
                            $tot_inspe_bal+=$inspe_bal;
                            $tot_ins_wip+=$ins_wip;

                            $tot_linking_yesterday+=$linking_comp_yestarday;
                            $tot_linking_today+=$linking_comp_today;
                            $tot_linking_comp+=$linking_comp;
                            $tot_linking_bal+=$linking_bal;
                            $tot_linking_wip+=$linking_wip;

                            $tot_trimingyesterday+=$trimingcomp_yestarday;
                            $tot_trimingtoday+=$trimingcomp_today;
                            $tot_trimingcomp+=$trimingcomp;
                            $tot_trimingbal+=$trimingbal;
                            $tot_trimingwip+=$trimingwip;

                            $tot_mending_yesterday+=$mending_comp_yestarday;
                            $tot_mending_today+=$mending_comp_today;
                            $tot_mending_comp+=$mending_comp;
                            $tot_mending_bal+=$mending_bal;
                            $tot_mending_wip+=$mending_wip;
                            
                            $tot_wash_yesterday+=$wash_comp_yestarday;
                            $tot_wash_today+=$wash_comp_today;
                            $tot_wash_comp+=$wash_comp;
                            $tot_wash_bal+=$wash_bal;
                            $tot_wash_wip+=$wash_wip;
                            
                            $tot_attach_yesterday+=$attach_comm_yestarday;
                            $tot_attach_today+=$attach_comm_today;
                            $tot_attach_comm+=$attach_comm;
                            $tot_attach_bal+=$attach_bal;
                            $tot_attach_wip+=$attach_wip;
                            
                            $tot_sewing_yesterday+=$sewing_comm_yestarday;
                            $tot_sewing_today+=$sewing_comm_today;
                            $tot_sewing_comm+=$sewing_comm;
                            $tot_sewing_bal+=$sewing_bal;
                            $tot_sewing_wip+=$sewing_wip;
                            
                            $tot_pqc_yesterday+=$pqc_com_yestarday;
                            $tot_pqc_today+=$pqc_com_today;
                            $tot_pqc_comp+=$pqc_com;
                            $tot_pqc_bal+=$pqc_bal;
                            $tot_pqc_wip+=$pqc_wip;
                            
                            $tot_iron_wip+=$iron_wip;
                            $tot_iron_yesterday+=$iron_com_yestarday;
                            $tot_iron_today+=$iron_com_today;
                            $tot_iron_com+=$iron_com;
                            $tot_iron_bal+=$iron_bal;
                            $tot_re_iron+=$re_iron;
                            
                            $tot_packing_comm+=$packing_comm;
                            $tot_packing_bal+=$packing_bal;
                            $tot_shipment_com+=$shipment_com;
                            $tot_shipment_acc_bal+=$shipment_acc_bal;
                        }
                        ?>
                    </table>
                </div>
                <table width="4750" cellspacing="0" border="1" class="tbl_bottom" rules="all">
                    <tr>
                        <td width="30">&nbsp;</td> 
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="60">Total:</td>                    
                        
                        <td width="70" id="td_knitting_com"><? echo $tot_knitting_yesterday; ?></td>

                         <?php if(!empty($tot_knitting_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="td_knitting_com" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_knitting_today; ?></td>
                        <td width="70" id="td_knitting_com"><? echo $tot_knitting_com; ?></td>
                        <td width="70" id="td_knitting_bal"><? echo $tot_kintting_bal; ?></td>
                        
                        <td width="70" id="td_ins_wip"><? echo $tot_ins_wip; ?></td>
                        <td width="70" id="td_ins_wip"><? echo $tot_inspection_yesterday; ?></td>

                         <?php if(!empty($tot_inspection_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="td_ins_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_inspection_today; ?></td>
                        <td width="70" id="td_inspection_qnty"><? echo $tot_inspection_qnty; ?></td>
                        <td width="70" id="td_inspe_bal"><? echo $tot_inspe_bal; ?></td>
                        
                        <td width="70" id="td_makeup_wip"><? echo $tot_linking_wip; ?></td>
                        <td width="70" id="td_makeup_wip"><? echo $tot_linking_yesterday; ?></td>

                         <?php if(!empty($tot_linking_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="td_makeup_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_linking_today; ?></td>
                        <td width="70" id="td_makeup_comp"><? echo $tot_linking_comp; ?></td>
                        <td width="70" id="td_makeup_bal"><? echo $tot_linking_bal; ?></td>
                        
                        <td width="70" id="td_makeup_wip"><? echo $tot_triming_wip; ?></td>
                        <td width="70" id="td_makeup_wip"><? echo $tot_triming_yesterday; ?></td>

                         <?php if(!empty($tot_triming_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="td_makeup_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_triming_today; ?></td>
                        <td width="70" id="td_makeup_comp"><? echo $tot_triming_comp; ?></td>
                        <td width="70" id="td_makeup_bal"><? echo $tot_triming_bal; ?></td>
                        
                        <td width="70" id="td_makeup_wip"><? echo $tot_mending_wip; ?></td>
                        <td width="70" id="td_makeup_wip"><? echo $tot_mending_yesterday; ?></td>

                         <?php if(!empty($tot_mending_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="td_makeup_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_mending_today; ?></td>
                        <td width="70" id="td_makeup_comp"><? echo $tot_makeup_comp; ?></td>
                        <td width="70" id="td_makeup_bal"><? echo $tot_mending_bal; ?></td>
                        
                        <td width="70" id="td_wash_wip"><? echo $tot_wash_wip; ?></td>
                        <td width="70" id="td_wash_wip"><? echo $tot_wash_yesterday; ?></td>

                         <?php if(!empty($tot_wash_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="td_wash_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_wash_today; ?></td>
                        <td width="70" id="td_wash_comp"><? echo $tot_wash_comp; ?></td>
                        <td width="70" id="td_wash_bal"><? echo $tot_wash_bal; ?></td>
                        
                        <td width="70" id="td_attach_wip"><? echo $tot_attach_wip; ?></td>
                        <td width="70" id="td_attach_wip"><? echo $tot_attach_yesterday; ?></td>

                         <?php if(!empty($tot_attach_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="td_attach_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_attach_today; ?></td>
                        <td width="70" id="td_attach_comm"><? echo $tot_attach_comm; ?></td>
                        <td width="70" id="td_attach_bal"><? echo $tot_attach_bal; ?></td>
                        
                        <td width="70" id="td_sewing_wip"><? echo $tot_sewing_wip; ?></td>
                        <td width="70" id="td_sewing_wip"><? echo $tot_sewing_yesterday; ?></td>

                          <?php if(!empty($tot_sewing_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="td_sewing_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_sewing_today; ?></td>
                        <td width="70" id="td_sewing_comm"><? echo $tot_sewing_comm; ?></td>
                        <td width="70" id="td_sewing_bal"><? echo $tot_sewing_bal; ?></td>
                        
                        <td width="70" id="td_makeup_wip"><? echo $tot_pqc_wip; ?></td>
                        <td width="70" id="td_makeup_wip"><? echo $tot_pqc_yesterday; ?></td>

                         <?php if(!empty($tot_pqc_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="td_makeup_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_pqc_today; ?></td>
                        <td width="70" id="td_makeup_comp"><? echo $tot_pqc_comp; ?></td>
                        <td width="70" id="td_makeup_bal"><? echo $tot_pqc_bal; ?></td>
                        
                        <td width="70" id="td_iron_com"><? echo $tot_ir; ?></td>
                        <td width="70" id="td_iron_com"><? echo $tot_iron_com; ?></td>
                        <td width="70" id="td_iron_com"><? echo $tot_iron_com; ?></td>
                        <td width="70" id="td_iron_com"><? echo $tot_iron_com; ?></td>
                        <td width="70" id="td_iron_bal"><? echo $tot_iron_bal; ?></td>
                        <td width="70" id="td_re_iron"><? echo $tot_re_iron; ?></td>
                        
                        <td width="70" id="td_packing_comm"><? echo $tot_packing_comm; ?></td>
                        <td width="70" id="td_packing_comm"><? echo $tot_packing_comm; ?></td>
                        <td width="70" id="td_packing_comm"><? echo $tot_packing_comm; ?></td>
                        <td width="70" id="td_packing_bal"><? echo $tot_packing_bal; ?></td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                    </tr>
                </table>
            </div>
            <!-- =========================================== subcon data ========================================= -->
            <div>
                <table width="4750" cellspacing="0" border="1" class="rpt_table" rules="all" id="">
                    <caption style="font-size:18px;font-weight:bold;justify-content: left;text-align: left;">Sub-Contract</caption>
                    <thead>
                        <tr>
                            <th rowspan="2" width="30">Sl.</th> 
                            <th width="800" colspan="9">Order Details</th>
                            <th width="280" colspan="4">Knitting Production</th>
                            <th width="350" colspan="5">In Line Inspection</th>
                            <th width="350" colspan="5">Linking Production</th>
                            <th width="350" colspan="5">Trimming Complete</th>
                            <th width="350" colspan="5">Mending Complete</th>
                            <th width="350" colspan="5">Wash Production</th>
                            <th width="350" colspan="5">Attachment Production</th>
                            <th width="350" colspan="5">Sewing Prodution</th>
                            <th width="350" colspan="5">PQC</th>
                            <th width="420" colspan="6">Iron Production</th>
                            <th width="420" colspan="6">Packing and Finishing</th>
                        </tr>
                        <tr>
                            <th width="100">Company</th>
                            <th width="100">Working Company</th>
                            <th width="100">Buyer</th>
                            <th width="100">Style</th>
                            <th width="100">Season</th>
                            <th width="80">GG</th>
                            <th width="80">Order Qty</th>
                            <th width="80">Plan Cut Qty</th>
                            <th width="60">Ex. Cut %</th>
                            
                            <th width="70">Yesterday Knit. Complete</th>
                            <th width="70">Today Knit. Complete</th>
                            <th width="70">Total Knitting</th>
                            <th width="70">Knitting Balance</th>
                            
                            <th width="70">Inspection WIP</th>
                            <th width="70">Yesterday In Line Inspection Complete</th>
                            <th width="70">Today In Line Inspection</th>
                            <th width="70">Total  In Line Inspection</th>
                            <th width="70">In Line Inspection Balance</th>
                            
                            <th width="70">Linking WIP </th>
                            <th width="70">Yesterday Linking Complete</th>
                            <th width="70">Today Linking Complete</th>
                            <th width="70">Total Linking Complete</th>
                            <th width="70">Linking Balance</th>
                            
                            <th width="70">Trimming WIP </th>
                            <th width="70">Yesterday Trimming Complete</th>
                            <th width="70">Today Trimming Complete</th>
                            <th width="70">Total Trimming Complete</th>
                            <th width="70">Trimming Balance</th>
                            
                            <th width="70">Mending WIP </th>
                            <th width="70">Yesterday Mending Complete</th>
                            <th width="70">Today Mending Complete</th>
                            <th width="70">Total Mending Complete</th>
                            <th width="70">Mending Balance</th>
                            
                            <th width="70">Wash WIP</th>
                            <th width="70">Yesterday Wash Complete</th>
                            <th width="70">Today Wash Complete</th>
                            <th width="70">Total Wash Complete</th>
                            <th width="70">Wash Balance</th>
                            
                            <th width="70">Attachment WIP</th>
                            <th width="70">Yesterday Attachment Complete</th>
                            <th width="70">Today Attachment Complete</th>
                            <th width="70">Total Attach. Complete</th>
                            <th width="70">Attcah. Balance</th>
                            
                            <th width="70">Sewing WIP</th>
                            <th width="70">Yesterday Sewing Complete</th>
                            <th width="70">Today Sewing Complete</th>
                            <th width="70">Sewing Complete</th>
                            <th width="70">Sewing Balance</th>
                            
                            <th width="70">PQC WIP</th>
                            <th width="70">Yesterday PQC Complete</th>
                            <th width="70">Today PQC Complete</th>
                            <th width="70">PQC Complete</th>
                            <th width="70">PQC Balance</th>
                            
                            <th width="70">Iron WIP</th>
                            <th width="70">Yesterday Iron Complete</th>
                            <th width="70">Today Iron Complete</th>
                            <th width="70">Total Iron Complete</th>
                            <th width="70">Iron Balance</th>
                            <th width="70">Re-Iron</th>
                            
                            <th width="70">Yesterday Packing Complete</th>
                            <th width="70">Today Packing Complete</th>
                            <th width="70">Total Packing Complete</th>
                            <th width="70">Packing Balance</th>
                            <th width="70">Packing Complete %</th>
                            <th width="70">Status</th>
                        </tr>
                    </thead>
                </table>
                <div style="max-height:380px; overflow-y:scroll; width:4770px" id="scroll_body">
                    <table cellspacing="0" border="1" class="rpt_table" width="4750" id="table_body" rules="all">
                    <? 
                        $sl = 1;
                        $tot_knitting_yesterday = 0;
                        $tot_knitting_today = 0;
                        $tot_knitting_com = 0;
                        $tot_kintting_bal = 0;
                        $tot_knitting_wip = 0;

                        $tot_inspection_yesterday = 0;
                        $tot_inspection_today = 0;
                        $tot_inspection_qnty = 0;
                        $tot_inspe_bal = 0;
                        $tot_ins_wip = 0;

                        $tot_linking_yesterday = 0;
                        $tot_linking_today = 0;
                        $tot_linking_qnty = 0;
                        $tot_linking_bal = 0;
                        $tot_linking_wip = 0;

                        $tot_triming_yesterday = 0;
                        $tot_triming_today = 0;
                        $tot_triming_qnty = 0;
                        $tot_triming_bal = 0;
                        $tot_triming_wip = 0;

                        $tot_mending_yesterday = 0;
                        $tot_mending_today = 0;
                        $tot_mending_qnty = 0;
                        $tot_mending_bal = 0;
                        $tot_mending_wip = 0;
                        
                        $tot_wash_yesterday = 0;
                        $tot_wash_today = 0;
                        $tot_wash_comp = 0;
                        $tot_wash_bal = 0;
                        $tot_wash_wip = 0;
                        
                        $tot_attach_yesterday = 0;
                        $tot_attach_today = 0;
                        $tot_attach_comm = 0;
                        $tot_attach_bal = 0;
                        $tot_attach_wip = 0;
                        
                        $tot_sewing_yesterday = 0;
                        $tot_sewing_today = 0;
                        $tot_sewing_comm = 0;
                        $tot_sewing_bal = 0;
                        $tot_sewing_wip = 0;
                        
                        $tot_pqc_yesterday = 0;
                        $tot_pqc_today = 0;
                        $tot_pqc_comp = 0;
                        $tot_pqc_bal = 0;
                        $tot_pqc_wip = 0;
                        
                        $tot_iron_wip = 0;
                        $tot_iron_yesterday = 0;
                        $tot_iron_today = 0;
                        $tot_iron_com = 0;
                        $tot_iron_bal = 0;
                        $tot_re_iron = 0;
                        
                        $tot_packing_comm = 0;
                        $tot_packing_bal = 0;
                        $tot_shipment_com = 0;
                        $tot_shipment_acc_bal = 0;
                        foreach($po_arr_sub as $working_com=>$working_com_data)
                        {
                            foreach($working_com_data as $job_no=>$value)
                            {
                                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                $buyer_name=$style_ref_no=$gauge=$job_no_prefix_num=$job_no=$po_number=$shiping_status=""; $pubshipment_date="";
                                $po_qty=$plan_cut=$excess_cut=0;
                                $exdata=explode("___",$value);
                                $buyer_name=$exdata[0];
                                $buyer_name=$exdata[0];
                                $style_ref_no=$exdata[1];
                                $gauge=$exdata[2];
                                $job_no_prefix_num=$exdata[3];
                                $job_no=$exdata[4];
                                $po_number=$exdata[5];
                            
                                $po_qty=$job_data_array[$job_no]['po_quantity'];
                                $plan_cut=$job_data_array[$job_no]['plan_cut'];
                                $excess_cut=$job_data_array[$job_no]['excess_cut'];
                                
                                $shiping_status=$exdata[9];
                                $pubshipment_date=$exdata[10];
                                $production_date=$exdata[11];
                                $company_name=$exdata[12];
                                $wo_company_name=$exdata[13];
                                $season=$exdata[14];

                                $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));
                                
                                $knitting_com=$inspection_qnty=$wash_comp=$attach_comm=$sewing_comm=$iron_com=$re_iron=$packing_comm=$shipment_com=$linking_comp=$mending_comm=$triming_comm=$pqc_com=$iron_n_comm=0;
                                $knitting_com=$production_arr_sub[$working_com][$job_no][1]['g'];
                                $inspection_qnty=$inspection_arr_sub[$job_no]['ins'];
                                $linking_comp=$production_arr_sub[$working_com][$job_no][4]['g'];
                                $wash_comp=$production_arr_sub[$working_com][$job_no][3]['g'];
                                $attach_comm=$production_arr_sub[$working_com][$job_no][11]['g'];
                                $sewing_comm=$production_arr_sub[$working_com][$job_no][5]['g'];
                                $iron_com=$production_arr_sub[$working_com][$job_no][7]['g']+$production_arr_sub[$working_com][$job_no][67]['g'];
                                $re_iron=$production_arr_sub[$working_com][$job_no][7]['r']+$production_arr_sub[$working_com][$job_no][67]['r'];
                                $packing_comm=$production_arr_sub[$working_com][$job_no][8]['g'];
                                $iron_n_comm=$production_arr_sub[$working_com][$job_no][67]['g'];
                                $re_iron_n_comm=$production_arr_sub[$working_com][$job_no][67]['r'];
                                $triming_comm=$production_arr_sub[$working_com][$job_no][111]['g'];
                                $mending_comm=$production_arr_sub[$working_com][$job_no][112]['g'];
                                $pqc_com=$production_arr_sub[$working_com][$job_no][114]['g'];
                                // ================================ balance ============================
                                $kintting_bal=$inspe_bal=$makeup_bal=$wash_bal=$attach_bal=$sewing_bal=$iron_bal=$packing_bal=$shipment_acc_bal=$linking_bal=$mending_bal=$triming_bal=$pqc_bal=0;
                                $kintting_bal=$plan_cut-$knitting_com;
                                $inspe_bal=$plan_cut-$inspection_qnty;
                                $linking_bal=$plan_cut-$linking_comp;
                                $triming_bal=$plan_cut-$triming_comm;
                                $mending_bal=$plan_cut-$mending_comm;
                                $pqc_bal=$plan_cut-$pqc_com;
                                $wash_bal=$plan_cut-$wash_comp;
                                $attach_bal=$plan_cut-$attach_comm;
                                $sewing_bal=$plan_cut-$sewing_comm;
                                $iron_bal=$plan_cut-$iron_n_comm;
                                $packing_bal=$plan_cut-$packing_comm;
                                $shipment_acc_bal=$po_qty-$shipment_com;
                                // ================================== wip ===============================
                                $knitting_wip=$ins_wip=$makeup_wip=$wash_wip=$attach_wip=$sewing_wip=$packing_percent=$linking_wip=$mending_wip=$triming_wip=$pqc_wip=0;
                                
                                $ins_wip=$knitting_com-$inspection_qnty;
                                $linking_wip=$inspection_qnty-$linking_comp;
                                $triming_wip=$linking_comp-$triming_comm;
                                $mending_wip=$triming_comm-$mending_comm;
                                $mending_wip=$triming_comm-$mending_comm;
                                $wash_wip=$mending_comm-$wash_comp;
                                $attach_wip=$wash_comp-$attach_comm;
                                $sewing_wip=$attach_comm-$sewing_comm;
                                $pqc_wip=$sewing_comm-$pqc_com;
                                $iron_wip=$pqc_com-$iron_n_comm;
                                $packing_percent=($packing_comm/$po_qty)*100;
                                // ================================== today prod =======================================
                                $knitting_com_today=$inspection_qnty_today=$linking_comp_today=$wash_comp_today=$attach_comm_today=$sewing_comm_today=$iron_com_today=$re_iron_today=$packing_comm_today=$shipment_com_today=$pqc_com_today=0;
                                // if(strtotime($production_date) == strtotime($txt_date))
                                // {
                                    $knitting_com_today     = $production_arr[$working_com][$job_no][1]['today'];
                                    $inspection_qnty_today  = $inspection_arr[$job_no]['today'];
                                    $linking_comp_today      = $production_arr[$working_com][$job_no][4]['today'];
                                    $wash_comp_today        = $production_arr[$working_com][$job_no][3]['today'];
                                    $attach_comm_today      = $production_arr[$working_com][$job_no][11]['today'];
                                    $sewing_comm_today      = $production_arr[$working_com][$job_no][5]['today'];
                                    $iron_com_today         = $production_arr[$working_com][$job_no][7]['today']+$production_arr[$working_com][$job_no][67]['today'];
                                    $re_iron_today          = $production_arr[$working_com][$job_no][7]['today_re']+$production_arr[$working_com][$job_no][67]['today_re'];
                                    $packing_comm_today     = $production_arr[$working_com][$job_no][8]['today'];
                                    $iron_n_comm_today      = $production_arr[$working_com][$job_no][67]['today'];
                                    $re_iron_n_comm_today   = $production_arr[$working_com][$job_no][67]['today_re'];
                                    $triming_comm_today     = $production_arr[$working_com][$job_no][111]['today'];
                                    $mending_comm_today     = $production_arr[$working_com][$job_no][112]['today'];
                                    $pqc_com_today          = $production_arr[$working_com][$job_no][114]['today'];
                                // }
                                // ===================================== prev day prod ======================================
                                $knitting_com_yestarday=$inspection_qnty_yestarday=$linking_comp_yestarday=$wash_comp_yestarday=$attach_comm_yestarday=$sewing_comm_yestarday=$iron_com_yestarday=$re_iron_yestarday=$packing_comm_yestarday=$shipment_com_yestarday=$pqc_com_yestarday=0;
                                // if(strtotime($production_date) == strtotime($prev_day))
                                // {
                                    $knitting_com_yestarday     = $production_arr[$working_com][$job_no][1]['yesterday'];
                                    $inspection_qnty_yestarday  = $inspection_arr[$job_no]['yesterday'];
                                    $linking_comp_yestarday      = $production_arr[$working_com][$job_no][4]['yesterday'];
                                    $wash_comp_yestarday        = $production_arr[$working_com][$job_no][3]['yesterday'];
                                    $attach_comm_yestarday      = $production_arr[$working_com][$job_no][11]['yesterday'];
                                    $sewing_comm_yestarday      = $production_arr[$working_com][$job_no][5]['yesterday'];
                                    $iron_com_yestarday         = $production_arr[$working_com][$job_no][7]['yesterday']+$production_arr[$working_com][$job_no][67]['yesterday'];
                                    $re_iron_yestarday          = $production_arr[$working_com][$job_no][7]['yesterday_re']+$production_arr[$working_com][$job_no][67]['yesterday_re'];
                                    $packing_comm_yestarday     = $production_arr[$working_com][$job_no][8]['yesterday'];
                                    $iron_n_comm_yestarday      = $production_arr[$working_com][$job_no][67]['yesterday'];
                                    $re_iron_n_comm_yestarday   = $production_arr[$working_com][$job_no][67]['yesterday_re'];
                                    $triming_comm_yestarday     = $production_arr[$working_com][$job_no][111]['yesterday'];
                                    $mending_comm_yestarday     = $production_arr[$working_com][$job_no][112]['yesterday'];
                                    $pqc_com_yestarday          = $production_arr[$working_com][$job_no][114]['yesterday'];
                                // }
                                
                                $packing_status="Running"; $shpment_date='';
                                
                                if($packing_comm>=$plan_cut){
                                    $ship_status_color="#FFA500";
                                    $packing_status="Complete";
                                } 
                                 else if($packing_comm<$plan_cut){
                                    $packing_status="Running";
                                    $ship_status_color="";
                                 } 
                                                    
                                ?>
                                 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td width="30"><? echo $sl; ?></td>    
                                    <td width="100" style="word-break:break-all"><? echo $company_library[$company_name]; ?>&nbsp;</td>
                                    <td width="100" style="word-break:break-all"><? echo $supplier_arr[$working_com]; ?>&nbsp;</td>
                                    <td width="100" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?>&nbsp;</td>
                                    <td width="100" style="word-break:break-all"><? echo $style_ref_no; ?>&nbsp;</td>
                                    <td width="100" style="word-break:break-all"><? echo $season_arr[$season]; ?>&nbsp;</td>
                                    <td width="80" style="word-break:break-all"><? echo $gauge_arr[$gauge]; ?>&nbsp;</td>
                                    <td width="80" align="right"><? echo $po_qty; ?></td>
                                    <td width="80" align="right"><? echo $plan_cut; ?></td>
                                    <td width="60" align="right"><? echo number_format($excess_cut,0); ?></td>                        
                                    
                                    <td width="70" align="right"><? echo $knitting_com_yestarday; ?></td>

                                      <?php if(!empty($knitting_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $knitting_com_today; ?></td>
                                    <td width="70" align="right"><? echo $knitting_com; ?></td>
                                    <td width="70" align="right"><? echo $kintting_bal; ?></td>
                                    
                                    <td width="70" align="right"><? echo $ins_wip; ?></td>
                                    <td width="70" align="right"><? echo $inspection_qnty_yestarday; ?></td>

                                     <?php if(!empty($inspection_qnty_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>"><? echo $inspection_qnty_today; ?></td>
                                    <td width="70" align="right"><? echo $inspection_qnty; ?></td>
                                    <td width="70" align="right"><? echo $inspe_bal; ?></td>
                                    
                                    <td width="70" align="right"><? echo $linking_wip; ?></td>
                                    <td width="70" align="right"><? echo $linking_comp_yestarday; ?></td>

                                      <?php if(!empty($linking_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>"><? echo $linking_comp_today; ?></td>
                                    <td width="70" align="right"><? echo $linking_comp; ?></td>
                                    <td width="70" align="right"><? echo $linking_bal; ?></td>
                                    
                                    <td width="70" align="right"><? echo $triming_wip; ?></td>
                                    <td width="70" align="right"><? echo $triming_comm_yestarday; ?></td>

                                     <?php if(!empty($triming_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $triming_comm_today; ?></td>
                                    <td width="70" align="right"><? echo $triming_comm; ?></td>
                                    <td width="70" align="right"><? echo $triming_bal; ?></td>
                                    
                                    <td width="70" align="right"><? echo $mending_wip; ?></td>
                                    <td width="70" align="right"><? echo $mending_comm_yestarday; ?></td>

                                     <?php if(!empty($mending_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $mending_comm_today; ?></td>
                                    <td width="70" align="right"><? echo $mending_comm; ?></td>
                                    <td width="70" align="right"><? echo $mending_bal; ?></td>
                                    
                                    <td width="70" align="right"><? echo $wash_wip; ?></td>
                                    <td width="70" align="right"><? echo $wash_comp_yestarday; ?></td>

                                    <?php if(!empty($wash_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $wash_comp_today; ?></td>
                                    <td width="70" align="right"><? echo $wash_comp; ?></td>
                                    <td width="70" align="right"><? echo $wash_bal; ?></td>
                                    
                                    <td width="70" align="right"><? echo $attach_wip; ?></td>
                                    <td width="70" align="right"><? echo $attach_comm_yestarday; ?></td>

                                     <?php if(!empty($attach_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $attach_comm_today; ?></td>
                                    <td width="70" align="right"><? echo $attach_comm; ?></td>
                                    <td width="70" align="right"><? echo $attach_bal; ?></td>
                                    
                                    <td width="70" align="right"><? echo $sewing_wip; ?></td>
                                    <td width="70" align="right"><? echo $sewing_comm_yestarday; ?></td>

                                     <?php if(!empty($sewing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $sewing_comm_today; ?></td>
                                    <td width="70" align="right"><? echo $sewing_comm; ?></td>
                                    <td width="70" align="right"><? echo $sewing_bal; ?></td>
                                    
                                    <td width="70" align="right"><? echo $pqc_wip; ?></td>
                                    <td width="70" align="right"><? echo $pqc_com_yestarday; ?></td>

                                    <?php if(!empty($pqc_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $pqc_com_today; ?></td>
                                    <td width="70" align="right"><? echo $pqc_com; ?></td>
                                    <td width="70" align="right"><? echo $pqc_bal; ?></td>
                                    
                                    <td width="70" align="right"><? echo $iron_wip; ?></td>
                                    <td width="70" align="right"><? echo $iron_com_yestarday; ?></td>

                                     <?php if(!empty($iron_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $iron_com_today; ?></td>
                                    <td width="70" align="right"><? echo $iron_comm; ?></td>
                                    <td width="70" align="right"><? echo $iron_bal; ?></td>
                                    <td width="70" align="right"><? echo $re_iron; ?></td>
                                    
                                    <td width="70" align="right"><? echo $packing_comm_yestarday; ?></td>

                                     <?php if(!empty($packing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $packing_comm_today; ?></td>
                                    <td width="70" align="right"><? echo $packing_comm; ?></td>
                                    <td width="70" align="right"><? echo $packing_bal; ?></td>
                                    <td width="70" align="right"><? echo number_format($packing_percent,2); ?></td>
                                    <td width="70" align="left" bgcolor="<?php echo $ship_status_color;?>"><? echo $packing_status; ?></td>
                                </tr>
                                <?
                                $i++;
                                $sl++;
                                $tot_knitting_yesterday += $knitting_com_yestarday;
                                $tot_knitting_today += $knitting_com_today;
                                $tot_knitting_com+=$knitting_com;
                                $tot_kintting_bal+=$kintting_bal;
                                $tot_knitting_wip+=$knitting_wip;
                                
                                $tot_inspection_yesterday+=$inspection_qnty_yestarday;
                                $tot_inspection_today+=$inspection_qnty_today;
                                $tot_inspection_qnty+=$inspection_qnty;
                                $tot_inspe_bal+=$inspe_bal;
                                $tot_ins_wip+=$ins_wip;

                                $tot_linking_yesterday+=$linking_comp_yestarday;
                                $tot_linking_today+=$linking_comp_today;
                                $tot_linking_comp+=$linking_comp;
                                $tot_linking_bal+=$linking_bal;
                                $tot_linking_wip+=$linking_wip;

                                $tot_trimingyesterday+=$trimingcomp_yestarday;
                                $tot_trimingtoday+=$trimingcomp_today;
                                $tot_trimingcomp+=$trimingcomp;
                                $tot_trimingbal+=$trimingbal;
                                $tot_trimingwip+=$trimingwip;

                                $tot_mending_yesterday+=$mending_comp_yestarday;
                                $tot_mending_today+=$mending_comp_today;
                                $tot_mending_comp+=$mending_comp;
                                $tot_mending_bal+=$mending_bal;
                                $tot_mending_wip+=$mending_wip;
                                
                                $tot_wash_yesterday+=$wash_comp_yestarday;
                                $tot_wash_today+=$wash_comp_today;
                                $tot_wash_comp+=$wash_comp;
                                $tot_wash_bal+=$wash_bal;
                                $tot_wash_wip+=$wash_wip;
                                
                                $tot_attach_yesterday+=$attach_comm_yestarday;
                                $tot_attach_today+=$attach_comm_today;
                                $tot_attach_comm+=$attach_comm;
                                $tot_attach_bal+=$attach_bal;
                                $tot_attach_wip+=$attach_wip;
                                
                                $tot_sewing_yesterday+=$sewing_comm_yestarday;
                                $tot_sewing_today+=$sewing_comm_today;
                                $tot_sewing_comm+=$sewing_comm;
                                $tot_sewing_bal+=$sewing_bal;
                                $tot_sewing_wip+=$sewing_wip;
                                
                                $tot_pqc_yesterday+=$pqc_com_yestarday;
                                $tot_pqc_today+=$pqc_com_today;
                                $tot_pqc_comp+=$pqc_com;
                                $tot_pqc_bal+=$pqc_bal;
                                $tot_pqc_wip+=$pqc_wip;
                                
                                $tot_iron_wip+=$iron_wip;
                                $tot_iron_yesterday+=$iron_com_yestarday;
                                $tot_iron_today+=$iron_com_today;
                                $tot_iron_com+=$iron_com;
                                $tot_iron_bal+=$iron_bal;
                                $tot_re_iron+=$re_iron;
                                
                                $tot_packing_comm+=$packing_comm;
                                $tot_packing_bal+=$packing_bal;
                                $tot_shipment_com+=$shipment_com;
                                $tot_shipment_acc_bal+=$shipment_acc_bal;
                            }
                        }
                        ?>
                    </table>
                </div>
                <table width="4750" cellspacing="0" border="1" class="tbl_bottom" rules="all">
                    <tr>
                        <td width="30">&nbsp;</td> 
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="60">Total:</td>                    
                        
                        <td width="70" id="td_knitting_com"><? echo $tot_knitting_yesterday; ?></td>
                        <td width="70" id="td_knitting_com"><? echo $tot_knitting_today; ?></td>
                        <td width="70" id="td_knitting_com"><? echo $tot_knitting_com; ?></td>
                        <td width="70" id="td_knitting_bal"><? echo $tot_kintting_bal; ?></td>
                        
                        <td width="70" id="td_ins_wip"><? echo $tot_ins_wip; ?></td>
                        <td width="70" id="td_ins_wip"><? echo $tot_inspection_yesterday; ?></td>

                         <?php if(!empty($tot_inspection_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                        <td width="70" id="td_ins_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_inspection_today; ?></td>
                        <td width="70" id="td_inspection_qnty"><? echo $tot_inspection_qnty; ?></td>
                        <td width="70" id="td_inspe_bal"><? echo $tot_inspe_bal; ?></td>
                        
                        <td width="70" id="td_makeup_wip"><? echo $tot_linking_wip; ?></td>
                        <td width="70" id="td_makeup_wip"><? echo $tot_linking_yesterday; ?></td>

                         <?php if(!empty($tot_linking_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="td_makeup_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_linking_today; ?></td>
                        <td width="70" id="td_makeup_comp"><? echo $tot_linking_comp; ?></td>
                        <td width="70" id="td_makeup_bal"><? echo $tot_linking_bal; ?></td>
                        
                        <td width="70" id="td_makeup_wip"><? echo $tot_triming_wip; ?></td>
                        <td width="70" id="td_makeup_wip"><? echo $tot_triming_yesterday; ?></td>

                         <?php if(!empty($tot_triming_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                        <td width="70" id="td_makeup_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_triming_today; ?></td>
                        <td width="70" id="td_makeup_comp"><? echo $tot_triming_comp; ?></td>
                        <td width="70" id="td_makeup_bal"><? echo $tot_triming_bal; ?></td>
                        
                        <td width="70" id="td_makeup_wip"><? echo $tot_mending_wip; ?></td>
                        <td width="70" id="td_makeup_wip"><? echo $tot_mending_yesterday; ?></td>

                        <?php if(!empty($tot_mending_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="td_makeup_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_mending_today; ?></td>
                        <td width="70" id="td_makeup_comp"><? echo $tot_makeup_comp; ?></td>
                        <td width="70" id="td_makeup_bal"><? echo $tot_mending_bal; ?></td>
                        
                        <td width="70" id="td_wash_wip"><? echo $tot_wash_wip; ?></td>
                        <td width="70" id="td_wash_wip"><? echo $tot_wash_yesterday; ?></td>

                         <?php if(!empty($tot_wash_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="td_wash_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_wash_today; ?></td>
                        <td width="70" id="td_wash_comp"><? echo $tot_wash_comp; ?></td>
                        <td width="70" id="td_wash_bal"><? echo $tot_wash_bal; ?></td>
                        
                        <td width="70" id="td_attach_wip"><? echo $tot_attach_wip; ?></td>
                        <td width="70" id="td_attach_wip"><? echo $tot_attach_yesterday; ?></td>

                         <?php if(!empty($tot_attach_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="td_attach_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_attach_today; ?></td>
                        <td width="70" id="td_attach_comm"><? echo $tot_attach_comm; ?></td>
                        <td width="70" id="td_attach_bal"><? echo $tot_attach_bal; ?></td>
                        
                        <td width="70" id="td_sewing_wip"><? echo $tot_sewing_wip; ?></td>
                        <td width="70" id="td_sewing_wip"><? echo $tot_sewing_yesterday; ?></td>

                          <?php if(!empty($tot_sewing_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="td_sewing_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_sewing_today; ?></td>
                        <td width="70" id="td_sewing_comm"><? echo $tot_sewing_comm; ?></td>
                        <td width="70" id="td_sewing_bal"><? echo $tot_sewing_bal; ?></td>
                        
                        <td width="70" id="td_makeup_wip"><? echo $tot_pqc_wip; ?></td>
                        <td width="70" id="td_makeup_wip"><? echo $tot_pqc_yesterday; ?></td>

                         <?php if(!empty($tot_pqc_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                         
                        <td width="70" id="td_makeup_wip" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_pqc_today; ?></td>
                        <td width="70" id="td_makeup_comp"><? echo $tot_pqc_comp; ?></td>
                        <td width="70" id="td_makeup_bal"><? echo $tot_pqc_bal; ?></td>
                        
                        <td width="70" id="td_iron_com"><? echo $tot_ir; ?></td>
                        <td width="70" id="td_iron_com"><? echo $tot_iron_com; ?></td>
                        <td width="70" id="td_iron_com"><? echo $tot_iron_com; ?></td>
                        <td width="70" id="td_iron_com"><? echo $tot_iron_com; ?></td>
                        <td width="70" id="td_iron_bal"><? echo $tot_iron_bal; ?></td>
                        <td width="70" id="td_re_iron"><? echo $tot_re_iron; ?></td>
                        
                        <td width="70" id="td_packing_comm"><? echo $tot_packing_comm; ?></td>
                        <td width="70" id="td_packing_comm"><? echo $tot_packing_comm; ?></td>
                        <td width="70" id="td_packing_comm"><? echo $tot_packing_comm; ?></td>
                        <td width="70" id="td_packing_bal"><? echo $tot_packing_bal; ?></td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <?
    }
    else  if($reporttype==2)
    {
         /* =============================================================================================/
            /                                        Main Query                                             /
            / ============================================================================================ */
            $sql="SELECT a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, a.GAUGE, a.job_no_prefix_num as JOB_PREFIX, a.JOB_NO, a.ORDER_UOM, a.total_set_qnty as RATIO,a.season_buyer_wise as SEASON,a.AVG_UNIT_PRICE, b.id as PO_ID, b.PO_NUMBER, b.PO_QUANTITY, b.PLAN_CUT, b.EXCESS_CUT, b.SHIPING_STATUS, b.PUB_SHIPMENT_DATE,c.PRODUCTION_SOURCE,c.PRODUCTION_DATE,c.SERVING_COMPANY, c.PRODUCTION_TYPE, c.production_quantity as PRODUCTION_QUANTITY, c.RE_PRODUCTION_QTY
            from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id
            and b.id=c.po_break_down_id and a.garments_nature=100 
            and c.production_type in (1,3,4,5,7,8,11,67,111,112,114) 
            and a.status_active=1 and a.is_deleted=0 
            and b.status_active=1 and b.is_deleted=0 
            and c.status_active=1 and c.is_deleted=0 
            $company_name_cond $buyer_id_cond $date_cond $job_style_cond $order_cond $job_cond $year_cond $ship_status_cond order by a.id ASC";
        // echo $sql;die;
        $sql_result=sql_select($sql);     
        if(count($sql_result)==0)
        {
            ?>
            <div style="text-align: center;color:red;font-weight:bold">Data not found. Please check budget and production.</div>
            <?
            die;
        } 
        $po_arr=array(); 
        $po_arr_sub=array(); 
        $production_arr=array(); 
        $production_arr_sub=array(); 
        $production_summary_arr=array(); 
        $production_summary_arr_sub=array(); 
        $production_summary_tot_arr=array(); 
        $order_qty_arr=array(); 
        $tot_rows=0; 
        $poId_Arr=array();
        $job_no_arr=array();
        foreach($sql_result as $row)
        {
            $tot_rows++;
            $poId_Arr[$row["PO_ID"]] = $row["PO_ID"];
            if($row["PRODUCTION_SOURCE"]==1) // inhouse
            {
                $po_arr[$row["JOB_NO"]]=$row["BUYER_NAME"].'___'.$row["STYLE_REF_NO"].'___'.$row["GAUGE"].'___'.$row["JOB_NO_PREFIX_NUM"].'___'.$row["JOB_NO"].'___'.$row["PO_NUMBER"].'___'.$row["PO_QUANTITY"].'___'.$row["PLAN_CUT"].'___'.$row["EXCESS_CUT"].'___'.$row["SHIPING_STATUS"].'___'.$row["PUB_SHIPMENT_DATE"].'___'.$row["PRODUCTION_DATE"].'___'.$row["COMPANY_NAME"].'___'.$row["SERVING_COMPANY"].'___'.$row["SEASON"];
                
                $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['g'] += $row["PRODUCTION_QUANTITY"];
               

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
                {
                     $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['r'] += $row["RE_PRODUCTION_QTY"];
                }

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
                {
                    $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['today'] += $row["PRODUCTION_QUANTITY"];
                    $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['today_re'] += $row["RE_PRODUCTION_QTY"];
                }
                $prev_day = "";
                $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($prev_day))
                {
                    $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['yesterday'] += $row["PRODUCTION_QUANTITY"];
                    $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['yesterday_re'] += $row["RE_PRODUCTION_QTY"];
                }

                $production_summary_arr[$row["PRODUCTION_TYPE"]]['g'] += $row["PRODUCTION_QUANTITY"];
                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
                {
                    $production_summary_arr[$row["PRODUCTION_TYPE"]]['r'] += $row["RE_PRODUCTION_QTY"];
                    $production_summary_arr[$row["PRODUCTION_TYPE"]]['today'] += $row["PRODUCTION_QUANTITY"];
                }
               
                $order_qty_arr[$row["JOB_NO"]]['in_order_qty'] += $row["PO_QUANTITY"];
                $order_qty_arr[$row["JOB_NO"]]['in_excess_cut'] += $row["EXCESS_CUT"];
                $order_qty_arr[$row["JOB_NO"]]['in_plan_cut'] += $row["PLAN_CUT"];
            }
            else
            {
                $po_arr_sub[$row["SERVING_COMPANY"]][$row["JOB_NO"]]=$row["BUYER_NAME"].'___'.$row["STYLE_REF_NO"].'___'.$row["GAUGE"].'___'.$row["JOB_NO_PREFIX_NUM"].'___'.$row["JOB_NO"].'___'.$row["PO_NUMBER"].'___'.$row["PO_QUANTITY"].'___'.$row["PLAN_CUT"].'___'.$row["EXCESS_CUT"].'___'.$row["SHIPING_STATUS"].'___'.$row["PUB_SHIPMENT_DATE"].'___'.$row["PRODUCTION_DATE"].'___'.$row["COMPANY_NAME"].'___'.$row["SERVING_COMPANY"].'___'.$row["SEASON"];
                
                $production_arr_sub[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['g'] += $row["PRODUCTION_QUANTITY"];

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
                {
                      $production_arr_sub[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['r'] += $row["RE_PRODUCTION_QTY"];
                }

               

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
                {
                    $production_arr[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['today'] += $row["PRODUCTION_QUANTITY"];
                    $production_arr[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['today_re'] += $row["RE_PRODUCTION_QTY"];
                }
                $prev_day = "";
                $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($prev_day))
                {
                    $production_arr[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['yesterday'] += $row["PRODUCTION_QUANTITY"];
                    $production_arr[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['yesterday_re'] += $row["RE_PRODUCTION_QTY"];
                }


                $production_summary_arr_sub[$row["PRODUCTION_TYPE"]]['g'] += $row["PRODUCTION_QUANTITY"];

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
                {
                    $production_summary_arr_sub[$row["PRODUCTION_TYPE"]]['r'] += $row["RE_PRODUCTION_QTY"];
                    $production_summary_arr_sub[$row["PRODUCTION_TYPE"]]['today'] += $row["PRODUCTION_QUANTITY"];
                }

               
                $order_qty_arr[$row["JOB_NO"]]['out_order_qty'] += $row["PO_QUANTITY"];
                $order_qty_arr[$row["JOB_NO"]]['out_excess_cut'] += $row["EXCESS_CUT"];
                $order_qty_arr[$row["JOB_NO"]]['out_plan_cut'] += $row["PLAN_CUT"];
            }
            $production_summary_tot_arr[$row["PRODUCTION_TYPE"]]['g'] += $row["PRODUCTION_QUANTITY"];
            
             if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
            {
                $production_summary_tot_arr[$row["PRODUCTION_TYPE"]]['r'] += $row["RE_PRODUCTION_QTY"];
                $production_summary_tot_arr[$row["PRODUCTION_TYPE"]]['today'] += $row["PRODUCTION_QUANTITY"];
            }

            array_push($job_no_arr, $row["JOB_NO"]);
        }
        unset($sql_result);

        $po_id_list_arr=array_chunk($poId_Arr,999);
        $poIds_cond = " and ";
        $p=1;
        foreach($po_id_list_arr as $poids)
        {
            if($p==1) 
            {
                $poIds_cond .="  ( po_break_down_id in(".implode(',',$poids).")"; 
            }
            else
            {
              $poIds_cond .=" or po_break_down_id in(".implode(',',$poids).")";
            }
            $p++;
        }
        $poIds_cond .=")";
        unset($poId_Arr);

        /* =============================================================================================/
        /                                        Order Quantity                                        /
        / ============================================================================================ */
        $po_id = str_replace("po_break_down_id", "id", $poIds_cond);
        $sql_order = "SELECT JOB_NO_MST, PO_QUANTITY,PLAN_CUT,EXCESS_CUT from wo_po_break_down where status_active=1";// $po_id
        $order_res = sql_select($sql_order);
        $job_data_array = array();
        foreach ($order_res as $val) 
        {
            $job_data_array[$val['JOB_NO_MST']]['po_quantity'] += $val['PO_QUANTITY'];
            $job_data_array[$val['JOB_NO_MST']]['plan_cut'] += $val['PLAN_CUT'];
            $job_data_array[$val['JOB_NO_MST']]['excess_cut'] += $val['EXCESS_CUT'];
        }

        /* =============================================================================================/
        /                                        Inspection Data                                        /
        / ============================================================================================ */
        $inspection_arr=array();
        $inspection_arr_sub=array();
        $inspection_summary=array();
        $inspection_total = 0;
        $sql_ins="SELECT JOB_NO,PO_BREAK_DOWN_ID,SOURCE,INSPECTION_DATE, INSPECTION_QNTY from pro_buyer_inspection where status_active=1 and is_deleted=0 $poIds_cond";
        $sql_ins_result=sql_select($sql_ins);
        foreach($sql_ins_result as $row)
        {
            if($row["SOURCE"]==1)
            {
                $inspection_arr[$row["JOB_NO"]]['ins'] += $row["INSPECTION_QNTY"];
                $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));

                if(strtotime($row["INSPECTION_DATE"])==strtotime($txt_date))
                {
                    $inspection_arr[$row["JOB_NO"]]['today'] += $row["INSPECTION_QNTY"];
                }
                if(strtotime($row["INSPECTION_DATE"])==strtotime($prev_day))
                {
                    $inspection_arr[$row["JOB_NO"]]['yesterday'] += $row["INSPECTION_QNTY"];
                }
            }
            else
            {
                $inspection_arr_sub[$row["JOB_NO"]]['ins'] += $row["INSPECTION_QNTY"];
                if(strtotime($row["INSPECTION_DATE"])==strtotime($txt_date))
                {
                    $inspection_arr[$row["JOB_NO"]]['today'] += $row["INSPECTION_QNTY"];
                }
                if(strtotime($row["INSPECTION_DATE"])==strtotime($prev_day))
                {
                    $inspection_arr[$row["JOB_NO"]]['yesterday'] += $row["INSPECTION_QNTY"];
                }
            }
            $inspection_summary[$row["SOURCE"]] += $row["INSPECTION_QNTY"];
            $inspection_total += $row["INSPECTION_QNTY"];
        }
        unset($sql_ins_result);


        $job_no_arr=array_unique($job_no_arr);
        if(count($job_no_arr))
        {
            $job_no_cond=where_con_using_array($job_no_arr,1,"job_no_mst");
        }
        $shp_sql="SELECT job_no_mst,min(shipment_date) shipment_date  from wo_po_break_down where status_active=1 $job_no_cond group by job_no_mst ";
        //echo $shp_sql;
        $shp_res=sql_select($shp_sql);

        $shipment_date_arr=array();

        foreach ($shp_res as $row) 
        {
            $shipment_date_arr[$row[csf('job_no_mst')]]=$row[csf('shipment_date')];
        }
        ?>
        <fieldset>
            <style type="text/css">
                table tr th{word-wrap: break-word;word-break:break-all;}
            </style>
            <!-- ============================================ heading part =========================== -->
            <div>
                <center>
                    <table width="2610" cellspacing="0" >
                        <tr class="form_caption" style="border:none;">
                            <td colspan="25"  align="center" style="border:none;font-size:16px; font-weight:bold" ><?  echo $report_title; ?></td>
                        </tr>
                        <tr style="border:none;">
                            <td colspan="25" align="center" style="border:none; font-size:14px;">
                                Company Name : <? echo $company_library[$cbo_company_name]; ?>                                
                            </td>
                        </tr>
                        <tr style="border:none;">
                            <td colspan="25" align="center" style="border:none;font-size:12px; font-weight:bold">
                                <?
                                    if(str_replace("'","",trim($txt_date))!="")
                                    {
                                        echo "Date : $txt_date";
                                    }
                                ?>
                            </td>
                        </tr>
                    </table>
                </center>
            </div>
            <!-- ============================================ summary part ============================== -->
            <div style="width:1300px;padding:10px 0">
                <table width="1300" cellspacing="0" border="1" class="rpt_table" rules="all">
                    <caption style="font-size:18px;font-weight:bold;justify-content: left;text-align: left;">Summary</caption>
                    <thead>
                        <th width="100">Summery</th>
                        <th width="100">Knitting</th>
                        <th width="100">Linking</th>
                        <th width="100">Trimming</th>
                        <th width="100">Mending</th>
                        <th width="100">Wash</th>
                        <th width="100">Sewing</th>
                        <th width="100">PQC</th>
                        <th width="100">Iron</th>
                        <th width="100">Re-Iron</th>
                        <th width="100">Packing</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Inhouse Production</td>
                            <td align="right"><? echo number_format($production_summary_arr[1]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[4]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[111]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[112]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[3]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[5]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[114]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[67]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[67]['r'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr[8]['today'],0);?></td>
                        </tr>
                        <tr>
                            <td>Subcon Production</td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[1]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[4]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[111]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[112]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[3]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[5]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[114]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[67]['today'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[67]['r'],0);?></td>
                            <td align="right"><? echo number_format($production_summary_arr_sub[8]['today'],0);?></td>
                        </tr>
                    </tbody>
                    <tfoot>                    
                        <tr>
                            <th>Total Production</td>
                            <th align="right"><? echo number_format($production_summary_tot_arr[1]['today'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[4]['today'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[111]['today'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[112]['today'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[3]['today'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[5]['today'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[114]['today'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[67]['today'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[67]['r'],0);?></th>
                            <th align="right"><? echo number_format($production_summary_tot_arr[8]['today'],0);?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- =========================================== inhouse data ====================================== -->
            <div>
                <table width="2610" cellspacing="0" border="1" class="rpt_table" rules="all" id="">
                    <caption style="font-size:18px;font-weight:bold;justify-content: left;text-align: left;">In-House(Today)</caption>
                    <thead>
                        <tr>
                            <th rowspan="2" width="30">Sl.</th> 
                            <th width="620" colspan="7">Order Details</th>

                            <th width="210" colspan="3">Knitting Production</th>
                          
                            <th width="210" colspan="3">Linking Production</th>

                            <th width="210" colspan="3">Trimming Complete</th>

                            <th width="210" colspan="3">Mending Complete</th>

                            <th width="210" colspan="3">Wash Production</th>
                       
                            <th width="210" colspan="3">Sewing Prodution</th>

                            <th width="210" colspan="3">PQC</th>

                            <th width="280" colspan="4">Iron Production</th>

                            <th width="210" colspan="3">Packing and Finishing</th>
                        </tr>
                        <tr>
                           
                               <!--  Order Details -->
                            <th width="100">Working Company</th>
                            <th width="100">Buyer</th>
                            <th width="100">Style</th>

                            <th width="80">GG</th>
                             <th width="80">Delivery date</th>
                            <th width="80">Order Qty</th>
                            <th width="80">Plan Cut Qty</th>
                          
                            
                           <!--  Knitting -->
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            
                        
                            <!--  Linking Production -->
                            <th width="70">Today </th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                            
                                <!-- Trimming --> 
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70">Balance</th>
                            
                            <!-- Mending -->
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            
                            <!-- Wash -->
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            
                         
                           
                            <!-- Sewing -->
                            <th width="70">Today </th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                            
                                <!-- PQC -->
                            <th width="70">Today </th>
                            <th width="70"> Total</th>
                            <th width="70"> Balance</th>
                            
                           <!-- Iron  -->
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            <th width="70">Re-Iron</th>
                            
                           <!-- Packing -->
                            <th width="70">Today </th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                           
                            
                        </tr>
                    </thead>
                </table>
                <div style="max-height:380px; overflow-y:scroll; width:2640px" id="scroll_body">
                    <table cellspacing="0" border="1" class="rpt_table" width="2610" id="table_body" rules="all">
                    <? 
                        

                        $i=1;
                        $tot_packing_bal=$tot_packing_comm=$tot_packing_comm=$tot_re_iron=$tot_iron_com=$tot_iron_com=$tot_iron_bal=$tot_knitting_today=$tot_knitting_com=$tot_kintting_bal=0;
                        $tot_linking_today=$tot_linking_comp=$tot_linking_bal=$tot_triming_today=$tot_triming_comp=$tot_triming_bal=$tot_wash_bal=$tot_sewing_today=$tot_sewing_comm=0;
                        $tot_mending_today=$tot_mending_comp=$tot_mending_bal=$tot_wash_today=$tot_wash_comp=$tot_sewing_bal=$tot_pqc_today=$tot_pqc_comp=$tot_pqc_bal=$tot_iron_today=$tot_packing_today=0;
                        foreach($po_arr as $job_no=>$value)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $buyer_name=$style_ref_no=$gauge=$job_no_prefix_num=$job_no=$po_number=$shiping_status=""; $pubshipment_date="";
                            $po_qty=$plan_cut=$excess_cut=0;
                            $exdata=explode("___",$value);
                            $buyer_name=$exdata[0];
                            $buyer_name=$exdata[0];
                            $style_ref_no=$exdata[1];
                            $gauge=$exdata[2];
                            $job_no_prefix_num=$exdata[3];
                            $job_no=$exdata[4];
                            $po_number=$exdata[5];
                            
                            $po_qty=$job_data_array[$job_no]['po_quantity'];
                            $plan_cut=$job_data_array[$job_no]['plan_cut'];
                            $excess_cut=$job_data_array[$job_no]['excess_cut'];
                            
                            $shiping_status=$exdata[9];
                            $pubshipment_date=$exdata[10];
                            $production_date=$exdata[11];
                            $company_name=$exdata[12];
                            $wo_company_name=$exdata[13];
                            $season=$exdata[14];

                            $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));
                            
                            $knitting_com=$inspection_qnty=$wash_comp=$attach_comm=$sewing_comm=$iron_com=$re_iron=$packing_comm=$shipment_com=$linking_comp=$mending_comm=$triming_comm=$pqc_com=0;
                            $knitting_com=$production_arr[$job_no][1]['g'];
                            $inspection_qnty=$inspection_arr[$job_no]['ins'];
                            $linking_comp=$production_arr[$job_no][4]['g'];
                            $wash_comp=$production_arr[$job_no][3]['g'];
                            $attach_comm=$production_arr[$job_no][11]['g'];
                            $sewing_comm=$production_arr[$job_no][5]['g'];
                            $iron_com=$production_arr[$job_no][7]['g']+$production_arr[$job_no][67]['g'];
                            $re_iron=$production_arr[$job_no][7]['r']+$production_arr[$job_no][67]['r'];
                            $packing_comm=$production_arr[$job_no][8]['g'];
                            $iron_n_comm=$production_arr[$job_no][67]['g'];
                            $re_iron_n_comm=$production_arr[$job_no][67]['r'];
                            $triming_comm=$production_arr[$job_no][111]['g'];
                            $mending_comm=$production_arr[$job_no][112]['g'];
                            $pqc_com=$production_arr[$job_no][114]['g'];
                            // ================================ balance ============================
                            $kintting_bal=$inspe_bal=$makeup_bal=$wash_bal=$attach_bal=$sewing_bal=$iron_bal=$packing_bal=$shipment_acc_bal=$linking_bal=$mending_bal=$triming_bal=$pqc_bal=0;
                            $kintting_bal=$plan_cut-$knitting_com;
                            $inspe_bal=$plan_cut-$inspection_qnty;
                            $linking_bal=$plan_cut-$linking_comp;
                            $triming_bal=$plan_cut-$triming_comm;
                            $mending_bal=$plan_cut-$mending_comm;
                            $pqc_bal=$plan_cut-$pqc_com;
                            $wash_bal=$plan_cut-$wash_comp;
                            $attach_bal=$plan_cut-$attach_comm;
                            $sewing_bal=$plan_cut-$sewing_comm;
                            $iron_bal=$plan_cut-$iron_com;
                            $packing_bal=$plan_cut-$packing_comm;
                            $shipment_acc_bal=$po_qty-$shipment_com;
                            // ================================== wip ===============================
                            $knitting_wip=$ins_wip=$makeup_wip=$wash_wip=$attach_wip=$sewing_wip=$packing_percent=$linking_wip=$mending_wip=$triming_wip=$pqc_wip=0;
                            
                            $ins_wip=$knitting_com-$inspection_qnty;
                            $linking_wip=$inspection_qnty-$linking_comp;
                            $triming_wip=$linking_comp-$triming_comm;
                            $mending_wip=$triming_comm-$mending_comm;
                            $mending_wip=$triming_comm-$mending_comm;
                            $wash_wip=$mending_comm-$wash_comp;
                            $attach_wip=$wash_comp-$attach_comm;
                            $sewing_wip=$attach_comm-$sewing_comm;
                            $pqc_wip=$sewing_comm-$pqc_com;
                            $iron_wip=$pqc_com-$iron_com;
                            $packing_percent=($packing_comm/$po_qty)*100;
                            // ================================== today prod =======================================
                            $knitting_com_today=$inspection_qnty_today=$linking_comp_today=$wash_comp_today=$attach_comm_today=$sewing_comm_today=$iron_com_today=$re_iron_today=$packing_comm_today=$shipment_com_today=$pqc_com_today=0;
                            // if(strtotime($production_date) == strtotime($txt_date))
                            // {
                                $knitting_com_today     = $production_arr[$job_no][1]['today'];
                                $inspection_qnty_today  = $inspection_arr[$job_no]['today'];
                                $linking_comp_today      = $production_arr[$job_no][4]['today'];
                                $wash_comp_today        = $production_arr[$job_no][3]['today'];
                                $attach_comm_today      = $production_arr[$job_no][11]['today'];
                                $sewing_comm_today      = $production_arr[$job_no][5]['today'];
                                $iron_com_today         = $production_arr[$job_no][7]['today']+$production_arr[$job_no][67]['today'];
                                $re_iron_today          = $production_arr[$job_no][7]['today_re']+$production_arr[$job_no][67]['today_re'];
                                $packing_comm_today     = $production_arr[$job_no][8]['today'];
                                $iron_n_comm_today      = $production_arr[$job_no][67]['today'];
                                $re_iron_n_comm_today   = $production_arr[$job_no][67]['today_re'];
                                $triming_comm_today     = $production_arr[$job_no][111]['today'];
                                $mending_comm_today     = $production_arr[$job_no][112]['today'];
                                $pqc_com_today         = $production_arr[$job_no][114]['today'];
                            // }
                            // ===================================== prev day prod ======================================
                            $knitting_com_yestarday=$inspection_qnty_yestarday=$linking_comp_yestarday=$wash_comp_yestarday=$attach_comm_yestarday=$sewing_comm_yestarday=$iron_com_yestarday=$re_iron_yestarday=$packing_comm_yestarday=$shipment_com_yestarday=$pqc_com_yestarday=0;
                            // if(strtotime($production_date) == strtotime($prev_day))
                            // {
                                $knitting_com_yestarday     = $production_arr[$job_no][1]['yesterday'];
                                $inspection_qnty_yestarday  = $inspection_arr[$job_no]['yesterday'];
                                $linking_comp_yestarday      = $production_arr[$job_no][4]['yesterday'];
                                $wash_comp_yestarday        = $production_arr[$job_no][3]['yesterday'];
                                $attach_comm_yestarday      = $production_arr[$job_no][11]['yesterday'];
                                $sewing_comm_yestarday      = $production_arr[$job_no][5]['yesterday'];
                                $iron_com_yestarday         = $production_arr[$job_no][7]['yesterday']+$production_arr[$job_no][67]['yesterday'];
                                $re_iron_yestarday          = $production_arr[$job_no][7]['yesterday_re']+$production_arr[$job_no][67]['yesterday_re'];
                                $packing_comm_yestarday     = $production_arr[$job_no][8]['yesterday'];
                                $iron_n_comm_yestarday      = $production_arr[$job_no][67]['yesterday'];
                                $re_iron_n_comm_yestarday   = $production_arr[$job_no][67]['yesterday_re'];
                                $triming_comm_yestarday     = $production_arr[$job_no][111]['yesterday'];
                                $mending_comm_yestarday     = $production_arr[$job_no][112]['yesterday'];
                                $pqc_com_yestarday          = $production_arr[$job_no][114]['yesterday'];
                            // }
                            
                            $packing_status="Running"; $shpment_date='';
                            $ship_status_color="";
                            
                            if($packing_comm>=$plan_cut)
                            {
                                $packing_status="Complete";

                                 $ship_status_color="#FFA500";
                            }  
                            else if($packing_comm<$plan_cut){
                                $packing_status="Running";
                                // $ship_status_color="#FFC0CB";
                            }
                            $bgcl="#FFFF00"; 
                            if($knitting_com_today >0 || $inspection_qnty_today>0 || $linking_comp_today >0 || $triming_comm_today>0 || $mending_comm_today>0 || $wash_comp_today>0 || $attach_comm_today>0 || $sewing_comm_today>0 ||           $pqc_com_today>0 || $iron_com_today>0 || $packing_comm_today>0){           
                            ?>
                             <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>   

                                <td width="100" style="word-break:break-all"><? echo $company_library[$wo_company_name]; ?></td>
                                <td width="100" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?></td>
                                <td width="100" style="word-break:break-all"><? echo $style_ref_no; ?></td>
                                <td width="80" style="word-break:break-all"><? echo $gauge_arr[$gauge]; ?></td>
                                <td width="80" style="word-break:break-all"><? echo '&nbsp;'.change_date_format($shipment_date_arr[$job_no]); ?></td>
                                <td width="80" align="right"><? echo $po_qty; ?></td>
                                <td width="80" align="right"><? echo $plan_cut; ?></td>
                                 
                                
                              

                                <?php if(!empty($knitting_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                <!--  knitting -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>"><? echo $knitting_com_today; ?></td>
                                <td width="70" align="right"><? echo $knitting_com; ?></td>
                                <td width="70" align="right"><? echo $kintting_bal; ?></td>
                             
                                
                              

                                <?php if(!empty($linking_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                    <!-- linking -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $linking_comp_today; ?></td>
                                <td width="70" align="right"><? echo $linking_comp; ?></td>
                                <td width="70" align="right"><? echo $linking_bal; ?></td>
                                
                               

                                 <?php if(!empty($triming_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                 <!-- triming -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $triming_comm_today; ?></td>
                                <td width="70" align="right"><? echo $triming_comm; ?></td>
                                <td width="70" align="right"><? echo $triming_bal; ?></td>
                                
                                

                                <?php if(!empty($mending_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                     <!-- mending -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $mending_comm_today; ?></td>
                                <td width="70" align="right"><? echo $mending_comm; ?></td>
                                <td width="70" align="right"><? echo $mending_bal; ?></td>
                                
                        
                                <?php if(!empty($wash_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                    <!-- wash -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $wash_comp_today; ?></td>
                                <td width="70" align="right"><? echo $wash_comp; ?></td>
                                <td width="70" align="right"><? echo $wash_bal; ?></td>
                                
                      
                                 <?php if(!empty($sewing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                 <!-- sewing -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $sewing_comm_today; ?></td>
                                <td width="70" align="right"><? echo $sewing_comm; ?></td>
                                <td width="70" align="right"><? echo $sewing_bal; ?></td>
                                
                              
                                 <?php if(!empty($pqc_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                <!--  pqc -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $pqc_com_today; ?></td>
                                <td width="70" align="right"><? echo $pqc_com; ?></td>
                                <td width="70" align="right"><? echo $pqc_bal; ?></td>
                                
                                

                                 <?php if(!empty($iron_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                 <!-- iron -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $iron_com_today; ?></td>
                                <td width="70" align="right"><? echo $iron_com; ?></td>
                                <td width="70" align="right"><? echo $iron_bal; ?></td>
                                <td width="70" align="right"><? echo $re_iron; ?></td>
                                
                               
                                 <?php if(!empty($packing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                 <!-- packing -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $packing_comm_today; ?></td>
                                <td width="70" align="right"><? echo $packing_comm; ?></td>
                                <td width="70" align="right"><? echo $packing_bal; ?></td>
                               
                            </tr>
                            <?

                            $i++;
                            $tot_knitting_yesterday += $knitting_com_yestarday;
                            $tot_knitting_today += $knitting_com_today;
                            $tot_knitting_com+=$knitting_com;
                            $tot_kintting_bal+=$kintting_bal;
                            $tot_knitting_wip+=$knitting_wip;
                            
                            $tot_inspection_yesterday+=$inspection_qnty_yestarday;
                            $tot_inspection_today+=$inspection_qnty_today;
                            $tot_inspection_qnty+=$inspection_qnty;
                            $tot_inspe_bal+=$inspe_bal;
                            $tot_ins_wip+=$ins_wip;

                            $tot_linking_yesterday+=$linking_comp_yestarday;
                            $tot_linking_today+=$linking_comp_today;
                            $tot_linking_comp+=$linking_comp;
                            $tot_linking_bal+=$linking_bal;
                            $tot_linking_wip+=$linking_wip;

                            $tot_trimingyesterday+=$trimingcomp_yestarday;
                            $tot_triming_today+=$triming_comm_today;
                            $tot_triming_comp+=$triming_comm;
                            $tot_triming_bal+=$triming_bal;
                            $tot_trimingwip+=$trimingwip;

                            $tot_mending_yesterday+=$mending_comp_yestarday;
                            $tot_mending_today+=$mending_comm_today;
                            $tot_mending_comp+=$mending_comm;
                            $tot_mending_bal+=$mending_bal;
                            $tot_mending_wip+=$mending_wip;
                            
                            $tot_wash_yesterday+=$wash_comp_yestarday;
                            $tot_wash_today+=$wash_comp_today;
                            $tot_wash_comp+=$wash_comp;
                            $tot_wash_bal+=$wash_bal;
                            $tot_wash_wip+=$wash_wip;
                            
                            $tot_attach_yesterday+=$attach_comm_yestarday;
                            $tot_attach_today+=$attach_comm_today;
                            $tot_attach_comm+=$attach_comm;
                            $tot_attach_bal+=$attach_bal;
                            $tot_attach_wip+=$attach_wip;
                            
                            $tot_sewing_yesterday+=$sewing_comm_yestarday;
                            $tot_sewing_today+=$sewing_comm_today;
                            $tot_sewing_comm+=$sewing_comm;
                            $tot_sewing_bal+=$sewing_bal;
                            $tot_sewing_wip+=$sewing_wip;
                            
                            $tot_pqc_yesterday+=$pqc_com_yestarday;
                            $tot_pqc_today+=$pqc_com_today;
                            $tot_pqc_comp+=$pqc_com;
                            $tot_pqc_bal+=$pqc_bal;
                            $tot_pqc_wip+=$pqc_wip;
                            
                            $tot_iron_wip+=$iron_wip;
                            $tot_iron_yesterday+=$iron_com_yestarday;
                            $tot_iron_today+=$iron_com_today;
                            $tot_iron_com+=$iron_com;
                            $tot_iron_bal+=$iron_bal;
                            $tot_re_iron+=$re_iron;
                            
                            $tot_packing_comm+=$packing_comm;
                            $tot_packing_bal+=$packing_bal;
                            $tot_shipment_com+=$shipment_com;
                            $tot_shipment_acc_bal+=$shipment_acc_bal;
                            $tot_packing_today+=$packing_comm_today;
                            }
                        }
                        ?>
                    </table>
                </div>
                <table width="2610" cellspacing="0" border="1" class="tbl_bottom" rules="all">
                    <tr>
                        <td width="30">&nbsp;</td> 
                     
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                      
                        <td width="80">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">Total:</td>                    
                        
                     
                         <?php if(!empty($tot_knitting_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_knitting_today; ?></td>
                        <td width="70" id=""><? echo $tot_knitting_com; ?></td>
                        <td width="70" id=""><? echo $tot_kintting_bal; ?></td>
                        
                    
                     

                         <?php if(!empty($tot_linking_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_linking_today; ?></td>
                        <td width="70" id=""><? echo $tot_linking_comp; ?></td>
                        <td width="70" id=""><? echo $tot_linking_bal; ?></td>
                        
                     

                         <?php if(!empty($tot_triming_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_triming_today; ?></td>
                        <td width="70" id=""><? echo $tot_triming_comp; ?></td>
                        <td width="70" id=""><? echo $tot_triming_bal; ?></td>
                        
                    

                         <?php if(!empty($tot_mending_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_mending_today; ?></td>
                        <td width="70" id=""><? echo $tot_mending_comp; ?></td>
                        <td width="70" id=""><? echo $tot_mending_bal; ?></td>
                        
                     

                         <?php if(!empty($tot_wash_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_wash_today; ?></td>
                        <td width="70" id=""><? echo $tot_wash_comp; ?></td>
                        <td width="70" id=""><? echo $tot_wash_bal; ?></td>
                        
                    
                      
                          <?php if(!empty($tot_sewing_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_sewing_today; ?></td>
                        <td width="70" id=""><? echo $tot_sewing_comm; ?></td>
                        <td width="70" id=""><? echo $tot_sewing_bal; ?></td>
                        
                     

                         <?php if(!empty($tot_pqc_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_pqc_today; ?></td>
                        <td width="70" id=""><? echo $tot_pqc_comp; ?></td>
                        <td width="70" id=""><? echo $tot_pqc_bal; ?></td>
                        
                      
                        <td width="70" id=""><? echo $tot_iron_today; ?></td>
                        <td width="70" id=""><? echo $tot_iron_com; ?></td>
                        <td width="70" id=""><? echo $tot_iron_bal; ?></td>
                        <td width="70" id=""><? echo $tot_re_iron; ?></td>
                        
                        
                        <td width="70" id=""><? echo $tot_packing_today; ?></td>
                        <td width="70" id=""><? echo $tot_packing_comm; ?></td>
                        <td width="70" id=""><? echo $tot_packing_bal; ?></td>
                       
                    </tr>
                </table>
            </div>
            <!-- =========================================== Pending data ====================================== -->
            <div>
                <table width="2610" cellspacing="0" border="1" class="rpt_table" rules="all" id="">
                    <caption style="font-size:18px;font-weight:bold;justify-content: left;text-align: left;">Pending(Today)</caption>
                    <thead>
                        <tr>
                            <th rowspan="2" width="30">Sl.</th> 
                            <th width="620" colspan="7">Order Details</th>

                            <th width="210" colspan="3">Knitting Production</th>
                          
                            <th width="210" colspan="3">Linking Production</th>

                            <th width="210" colspan="3">Trimming Complete</th>

                            <th width="210" colspan="3">Mending Complete</th>

                            <th width="210" colspan="3">Wash Production</th>
                       
                            <th width="210" colspan="3">Sewing Prodution</th>

                            <th width="210" colspan="3">PQC</th>

                            <th width="280" colspan="4">Iron Production</th>

                            <th width="210" colspan="3">Packing and Finishing</th>
                        </tr>
                        <tr>
                           
                               <!--  Order Details -->
                            <th width="100">Working Company</th>
                            <th width="100">Buyer</th>
                            <th width="100">Style</th>

                            <th width="80">GG</th>
                             <th width="80">Delivery date</th>
                            <th width="80">Order Qty</th>
                            <th width="80">Plan Cut Qty</th>
                          
                            
                           <!--  Knitting -->
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            
                        
                            <!--  Linking Production -->
                            <th width="70">Today </th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                            
                                <!-- Trimming --> 
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70">Balance</th>
                            
                            <!-- Mending -->
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            
                            <!-- Wash -->
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            
                         
                           
                            <!-- Sewing -->
                            <th width="70">Today </th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                            
                                <!-- PQC -->
                            <th width="70">Today </th>
                            <th width="70"> Total</th>
                            <th width="70"> Balance</th>
                            
                           <!-- Iron  -->
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            <th width="70">Re-Iron</th>
                            
                           <!-- Packing -->
                            <th width="70">Today </th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                           
                            
                        </tr>
                    </thead>
                </table>
                <div style="max-height:380px; overflow-y:scroll; width:2640px" id="scroll_body">
                    <table cellspacing="0" border="1" class="rpt_table" width="2610" id="table_body" rules="all">
                    <? 
                        

                        $i2=1;
                        $tot_packing_bal=$tot_packing_comm=$tot_packing_comm=$tot_re_iron=$tot_iron_com=$tot_iron_com=$tot_iron_bal=$tot_knitting_today=$tot_knitting_com=$tot_kintting_bal=0;
                        $tot_linking_today=$tot_linking_comp=$tot_linking_bal=$tot_triming_today=$tot_triming_comp=$tot_triming_bal=$tot_wash_bal=$tot_sewing_today=$tot_sewing_comm=0;
                        $tot_mending_today=$tot_mending_comp=$tot_mending_bal=$tot_wash_today=$tot_wash_comp=$tot_sewing_bal=$tot_pqc_today=$tot_pqc_comp=$tot_pqc_bal=$tot_iron_today=$tot_packing_today=0;
                        foreach($po_arr as $job_no=>$value)
                        {
                            if ($i2%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $buyer_name=$style_ref_no=$gauge=$job_no_prefix_num=$job_no=$po_number=$shiping_status=""; $pubshipment_date="";
                            $po_qty=$plan_cut=$excess_cut=0;
                            $exdata=explode("___",$value);
                            $buyer_name=$exdata[0];
                            $buyer_name=$exdata[0];
                            $style_ref_no=$exdata[1];
                            $gauge=$exdata[2];
                            $job_no_prefix_num=$exdata[3];
                            $job_no=$exdata[4];
                            $po_number=$exdata[5];
                            
                            $po_qty=$job_data_array[$job_no]['po_quantity'];
                            $plan_cut=$job_data_array[$job_no]['plan_cut'];
                            $excess_cut=$job_data_array[$job_no]['excess_cut'];
                            
                            $shiping_status=$exdata[9];
                            $pubshipment_date=$exdata[10];
                            $production_date=$exdata[11];
                            $company_name=$exdata[12];
                            $wo_company_name=$exdata[13];
                            $season=$exdata[14];

                            $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));
                            
                            $knitting_com=$inspection_qnty=$wash_comp=$attach_comm=$sewing_comm=$iron_com=$re_iron=$packing_comm=$shipment_com=$linking_comp=$mending_comm=$triming_comm=$pqc_com=0;
                            $knitting_com=$production_arr[$job_no][1]['g'];
                            $inspection_qnty=$inspection_arr[$job_no]['ins'];
                            $linking_comp=$production_arr[$job_no][4]['g'];
                            $wash_comp=$production_arr[$job_no][3]['g'];
                            $attach_comm=$production_arr[$job_no][11]['g'];
                            $sewing_comm=$production_arr[$job_no][5]['g'];
                            $iron_com=$production_arr[$job_no][7]['g']+$production_arr[$job_no][67]['g'];
                            $re_iron=$production_arr[$job_no][7]['r']+$production_arr[$job_no][67]['r'];
                            $packing_comm=$production_arr[$job_no][8]['g'];
                            $iron_n_comm=$production_arr[$job_no][67]['g'];
                            $re_iron_n_comm=$production_arr[$job_no][67]['r'];
                            $triming_comm=$production_arr[$job_no][111]['g'];
                            $mending_comm=$production_arr[$job_no][112]['g'];
                            $pqc_com=$production_arr[$job_no][114]['g'];
                            // ================================ balance ============================
                            $kintting_bal=$inspe_bal=$makeup_bal=$wash_bal=$attach_bal=$sewing_bal=$iron_bal=$packing_bal=$shipment_acc_bal=$linking_bal=$mending_bal=$triming_bal=$pqc_bal=0;
                            $kintting_bal=$plan_cut-$knitting_com;
                            $inspe_bal=$plan_cut-$inspection_qnty;
                            $linking_bal=$plan_cut-$linking_comp;
                            $triming_bal=$plan_cut-$triming_comm;
                            $mending_bal=$plan_cut-$mending_comm;
                            $pqc_bal=$plan_cut-$pqc_com;
                            $wash_bal=$plan_cut-$wash_comp;
                            $attach_bal=$plan_cut-$attach_comm;
                            $sewing_bal=$plan_cut-$sewing_comm;
                            $iron_bal=$plan_cut-$iron_com;
                            $packing_bal=$plan_cut-$packing_comm;
                            $shipment_acc_bal=$po_qty-$shipment_com;
                            // ================================== wip ===============================
                            $knitting_wip=$ins_wip=$makeup_wip=$wash_wip=$attach_wip=$sewing_wip=$packing_percent=$linking_wip=$mending_wip=$triming_wip=$pqc_wip=0;
                            
                            $ins_wip=$knitting_com-$inspection_qnty;
                            $linking_wip=$inspection_qnty-$linking_comp;
                            $triming_wip=$linking_comp-$triming_comm;
                            $mending_wip=$triming_comm-$mending_comm;
                            $mending_wip=$triming_comm-$mending_comm;
                            $wash_wip=$mending_comm-$wash_comp;
                            $attach_wip=$wash_comp-$attach_comm;
                            $sewing_wip=$attach_comm-$sewing_comm;
                            $pqc_wip=$sewing_comm-$pqc_com;
                            $iron_wip=$pqc_com-$iron_com;
                            $packing_percent=($packing_comm/$po_qty)*100;
                            // ================================== today prod =======================================
                            $knitting_com_today=$inspection_qnty_today=$linking_comp_today=$wash_comp_today=$attach_comm_today=$sewing_comm_today=$iron_com_today=$re_iron_today=$packing_comm_today=$shipment_com_today=$pqc_com_today=0;
                            // if(strtotime($production_date) == strtotime($txt_date))
                            // {
                                $knitting_com_today     = $production_arr[$job_no][1]['today'];
                                $inspection_qnty_today  = $inspection_arr[$job_no]['today'];
                                $linking_comp_today      = $production_arr[$job_no][4]['today'];
                                $wash_comp_today        = $production_arr[$job_no][3]['today'];
                                $attach_comm_today      = $production_arr[$job_no][11]['today'];
                                $sewing_comm_today      = $production_arr[$job_no][5]['today'];
                                $iron_com_today         = $production_arr[$job_no][7]['today']+$production_arr[$job_no][67]['today'];
                                $re_iron_today          = $production_arr[$job_no][7]['today_re']+$production_arr[$job_no][67]['today_re'];
                                $packing_comm_today     = $production_arr[$job_no][8]['today'];
                                $iron_n_comm_today      = $production_arr[$job_no][67]['today'];
                                $re_iron_n_comm_today   = $production_arr[$job_no][67]['today_re'];
                                $triming_comm_today     = $production_arr[$job_no][111]['today'];
                                $mending_comm_today     = $production_arr[$job_no][112]['today'];
                                $pqc_com_today         = $production_arr[$job_no][114]['today'];
                            // }
                            // ===================================== prev day prod ======================================
                            $knitting_com_yestarday=$inspection_qnty_yestarday=$linking_comp_yestarday=$wash_comp_yestarday=$attach_comm_yestarday=$sewing_comm_yestarday=$iron_com_yestarday=$re_iron_yestarday=$packing_comm_yestarday=$shipment_com_yestarday=$pqc_com_yestarday=0;
                            // if(strtotime($production_date) == strtotime($prev_day))
                            // {
                                $knitting_com_yestarday     = $production_arr[$job_no][1]['yesterday'];
                                $inspection_qnty_yestarday  = $inspection_arr[$job_no]['yesterday'];
                                $linking_comp_yestarday      = $production_arr[$job_no][4]['yesterday'];
                                $wash_comp_yestarday        = $production_arr[$job_no][3]['yesterday'];
                                $attach_comm_yestarday      = $production_arr[$job_no][11]['yesterday'];
                                $sewing_comm_yestarday      = $production_arr[$job_no][5]['yesterday'];
                                $iron_com_yestarday         = $production_arr[$job_no][7]['yesterday']+$production_arr[$job_no][67]['yesterday'];
                                $re_iron_yestarday          = $production_arr[$job_no][7]['yesterday_re']+$production_arr[$job_no][67]['yesterday_re'];
                                $packing_comm_yestarday     = $production_arr[$job_no][8]['yesterday'];
                                $iron_n_comm_yestarday      = $production_arr[$job_no][67]['yesterday'];
                                $re_iron_n_comm_yestarday   = $production_arr[$job_no][67]['yesterday_re'];
                                $triming_comm_yestarday     = $production_arr[$job_no][111]['yesterday'];
                                $mending_comm_yestarday     = $production_arr[$job_no][112]['yesterday'];
                                $pqc_com_yestarday          = $production_arr[$job_no][114]['yesterday'];
                            // }
                            
                            $packing_status="Running"; $shpment_date='';
                            $ship_status_color="";
                            
                            if($packing_comm>=$plan_cut)
                            {
                                $packing_status="Complete";

                                 $ship_status_color="#FFA500";
                            }  
                            else if($packing_comm<$plan_cut){
                                $packing_status="Running";
                                // $ship_status_color="#FFC0CB";
                            }
                            $bgcl="#FFFF00"; 
                                    
                            
                            if($knitting_com_today >0 || $inspection_qnty_today>0 || $linking_comp_today >0 || $triming_comm_today>0 || $mending_comm_today>0 || $wash_comp_today>0 || $attach_comm_today>0 || $sewing_comm_today>0 ||           $pqc_com_today>0 || $iron_com_today>0 || $packing_comm_today>0){  
                            }else{


                            ?>
                             <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i2; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i2; ?>">
                                <td width="30"><? echo $i2; ?></td>   

                                <td width="100" style="word-break:break-all"><? echo $company_library[$wo_company_name]; ?></td>
                                <td width="100" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?></td>
                                <td width="100" style="word-break:break-all"><? echo $style_ref_no; ?></td>
                                <td width="80" style="word-break:break-all"><? echo $gauge_arr[$gauge]; ?></td>
                                <td width="80" style="word-break:break-all"><? echo '&nbsp;'.change_date_format($shipment_date_arr[$job_no]); ?></td>
                                <td width="80" align="right"><? echo $po_qty; ?></td>
                                <td width="80" align="right"><? echo $plan_cut; ?></td>
                                 
                                
                              

                                <?php if(!empty($knitting_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                <!--  knitting -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>"><? echo $knitting_com_today; ?></td>
                                <td width="70" align="right"><? echo $knitting_com; ?></td>
                                <td width="70" align="right"><? echo $kintting_bal; ?></td>
                             
                                
                              

                                <?php if(!empty($linking_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                    <!-- linking -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $linking_comp_today; ?></td>
                                <td width="70" align="right"><? echo $linking_comp; ?></td>
                                <td width="70" align="right"><? echo $linking_bal; ?></td>
                                
                               

                                 <?php if(!empty($triming_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                 <!-- triming -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $triming_comm_today; ?></td>
                                <td width="70" align="right"><? echo $triming_comm; ?></td>
                                <td width="70" align="right"><? echo $triming_bal; ?></td>
                                
                                

                                <?php if(!empty($mending_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                     <!-- mending -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $mending_comm_today; ?></td>
                                <td width="70" align="right"><? echo $mending_comm; ?></td>
                                <td width="70" align="right"><? echo $mending_bal; ?></td>
                                
                        
                                <?php if(!empty($wash_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                    <!-- wash -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $wash_comp_today; ?></td>
                                <td width="70" align="right"><? echo $wash_comp; ?></td>
                                <td width="70" align="right"><? echo $wash_bal; ?></td>
                                
                      
                                 <?php if(!empty($sewing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                 <!-- sewing -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $sewing_comm_today; ?></td>
                                <td width="70" align="right"><? echo $sewing_comm; ?></td>
                                <td width="70" align="right"><? echo $sewing_bal; ?></td>
                                
                              
                                 <?php if(!empty($pqc_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                <!--  pqc -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $pqc_com_today; ?></td>
                                <td width="70" align="right"><? echo $pqc_com; ?></td>
                                <td width="70" align="right"><? echo $pqc_bal; ?></td>
                                
                                

                                 <?php if(!empty($iron_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                 <!-- iron -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $iron_com_today; ?></td>
                                <td width="70" align="right"><? echo $iron_com; ?></td>
                                <td width="70" align="right"><? echo $iron_bal; ?></td>
                                <td width="70" align="right"><? echo $re_iron; ?></td>
                                
                               
                                 <?php if(!empty($packing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                 <!-- packing -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $packing_comm_today; ?></td>
                                <td width="70" align="right"><? echo $packing_comm; ?></td>
                                <td width="70" align="right"><? echo $packing_bal; ?></td>
                               
                            </tr>
                            <?
                            $i2++;
                            $tot_knitting_yesterday += $knitting_com_yestarday;
                            $tot_knitting_today += $knitting_com_today;
                            $tot_knitting_com+=$knitting_com;
                            $tot_kintting_bal+=$kintting_bal;
                            $tot_knitting_wip+=$knitting_wip;
                            
                            $tot_inspection_yesterday+=$inspection_qnty_yestarday;
                            $tot_inspection_today+=$inspection_qnty_today;
                            $tot_inspection_qnty+=$inspection_qnty;
                            $tot_inspe_bal+=$inspe_bal;
                            $tot_ins_wip+=$ins_wip;

                            $tot_linking_yesterday+=$linking_comp_yestarday;
                            $tot_linking_today+=$linking_comp_today;
                            $tot_linking_comp+=$linking_comp;
                            $tot_linking_bal+=$linking_bal;
                            $tot_linking_wip+=$linking_wip;

                            $tot_trimingyesterday+=$trimingcomp_yestarday;
                            $tot_triming_today+=$triming_comm_today;
                            $tot_triming_comp+=$triming_comm;
                            $tot_triming_bal+=$triming_bal;
                            $tot_trimingwip+=$trimingwip;

                            $tot_mending_yesterday+=$mending_comp_yestarday;
                            $tot_mending_today+=$mending_comm_today;
                            $tot_mending_comp+=$mending_comm;
                            $tot_mending_bal+=$mending_bal;
                            $tot_mending_wip+=$mending_wip;
                            
                            $tot_wash_yesterday+=$wash_comp_yestarday;
                            $tot_wash_today+=$wash_comp_today;
                            $tot_wash_comp+=$wash_comp;
                            $tot_wash_bal+=$wash_bal;
                            $tot_wash_wip+=$wash_wip;
                            
                            $tot_attach_yesterday+=$attach_comm_yestarday;
                            $tot_attach_today+=$attach_comm_today;
                            $tot_attach_comm+=$attach_comm;
                            $tot_attach_bal+=$attach_bal;
                            $tot_attach_wip+=$attach_wip;
                            
                            $tot_sewing_yesterday+=$sewing_comm_yestarday;
                            $tot_sewing_today+=$sewing_comm_today;
                            $tot_sewing_comm+=$sewing_comm;
                            $tot_sewing_bal+=$sewing_bal;
                            $tot_sewing_wip+=$sewing_wip;
                            
                            $tot_pqc_yesterday+=$pqc_com_yestarday;
                            $tot_pqc_today+=$pqc_com_today;
                            $tot_pqc_comp+=$pqc_com;
                            $tot_pqc_bal+=$pqc_bal;
                            $tot_pqc_wip+=$pqc_wip;
                            
                            $tot_iron_wip+=$iron_wip;
                            $tot_iron_yesterday+=$iron_com_yestarday;
                            $tot_iron_today+=$iron_com_today;
                            $tot_iron_com+=$iron_com;
                            $tot_iron_bal+=$iron_bal;
                            $tot_re_iron+=$re_iron;
                            
                            $tot_packing_comm+=$packing_comm;
                            $tot_packing_bal+=$packing_bal;
                            $tot_shipment_com+=$shipment_com;
                            $tot_shipment_acc_bal+=$shipment_acc_bal;
                            $tot_packing_today+=$packing_comm_today;
                            }
                        }
                        ?>
                    </table>
                </div>
                <table width="2610" cellspacing="0" border="1" class="tbl_bottom" rules="all">
                    <tr>
                        <td width="30">&nbsp;</td> 
                     
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                      
                        <td width="80">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">Total:</td>                    
                        
                     
                         <?php if(!empty($tot_knitting_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_knitting_today; ?></td>
                        <td width="70" id=""><? echo $tot_knitting_com; ?></td>
                        <td width="70" id=""><? echo $tot_kintting_bal; ?></td>
                        
                    
                     

                         <?php if(!empty($tot_linking_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_linking_today; ?></td>
                        <td width="70" id=""><? echo $tot_linking_comp; ?></td>
                        <td width="70" id=""><? echo $tot_linking_bal; ?></td>
                        
                     

                         <?php if(!empty($tot_triming_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_triming_today; ?></td>
                        <td width="70" id=""><? echo $tot_triming_comp; ?></td>
                        <td width="70" id=""><? echo $tot_triming_bal; ?></td>
                        
                    

                         <?php if(!empty($tot_mending_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_mending_today; ?></td>
                        <td width="70" id=""><? echo $tot_mending_comp; ?></td>
                        <td width="70" id=""><? echo $tot_mending_bal; ?></td>
                        
                     

                         <?php if(!empty($tot_wash_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_wash_today; ?></td>
                        <td width="70" id=""><? echo $tot_wash_comp; ?></td>
                        <td width="70" id=""><? echo $tot_wash_bal; ?></td>
                        
                    
                      
                          <?php if(!empty($tot_sewing_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_sewing_today; ?></td>
                        <td width="70" id=""><? echo $tot_sewing_comm; ?></td>
                        <td width="70" id=""><? echo $tot_sewing_bal; ?></td>
                        
                     

                         <?php if(!empty($tot_pqc_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_pqc_today; ?></td>
                        <td width="70" id=""><? echo $tot_pqc_comp; ?></td>
                        <td width="70" id=""><? echo $tot_pqc_bal; ?></td>
                        
                      
                        <td width="70" id=""><? echo $tot_iron_today; ?></td>
                        <td width="70" id=""><? echo $tot_iron_com; ?></td>
                        <td width="70" id=""><? echo $tot_iron_bal; ?></td>
                        <td width="70" id=""><? echo $tot_re_iron; ?></td>
                        
                        
                        <td width="70" id=""><? echo $tot_packing_today; ?></td>
                        <td width="70" id=""><? echo $tot_packing_comm; ?></td>
                        <td width="70" id=""><? echo $tot_packing_bal; ?></td>
                       
                    </tr>
                </table>
            </div>
            <!-- =========================================== subcon data ========================================= -->
            <div>
                <table width="2610" cellspacing="0" border="1" class="rpt_table" rules="all" id="">
                    <caption style="font-size:18px;font-weight:bold;justify-content: left;text-align: left;">Sub-Contract</caption>
                    <thead>
                        <tr>
                            <th rowspan="2" width="30">Sl.</th> 
                            <th width="620" colspan="7">Order Details</th>
                            <th width="210" colspan="3">Knitting Production</th>
                            
                            <th width="210" colspan="3">Linking Production</th>

                            <th width="210" colspan="3">Trimming Complete</th>

                            <th width="210" colspan="3">Mending Complete</th>

                            <th width="210" colspan="3">Wash Production</th>
                           
                            <th width="210" colspan="3">Sewing Prodution</th>

                            <th width="210" colspan="3">PQC</th>

                            <th width="280" colspan="4">Iron Production</th>

                            <th width="210" colspan="3">Packing and Finishing</th>
                        </tr>
                        <tr>
                            
                            <th width="100">Working Company</th>
                            <th width="100">Buyer</th>
                            <th width="100">Style</th>
                           
                            <th width="80">GG</th>
                            <th width="80">Delivery date</th>
                            <th width="80">Order Qty</th>
                            <th width="80">Plan Cut Qty</th>
                            
                           <!-- Knitting -->
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            
                           
                           <!--  Linking -->
                            <th width="70">Today</th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            
                            <!-- Trimming -->
                            <th width="70">Today  </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            
                            <!-- Mending -->
                            <th width="70">Today  </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            
                            <!-- Wash -->
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            
                           
                            
                           <!-- Sewing -->
                            <th width="70">Today </th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                            
                            <!-- PQC -->
                            <th width="70">Today</th>
                            <th width="70">Total</th>
                            <th width="70">Balance</th>
                            
                           <!-- Iron -->
                            <th width="70">Today  </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            <th width="70">Re-Iron</th>
                            
                            <!-- Packing -->
                            <th width="70">Today  </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                           
                        </tr>
                    </thead>
                </table>
                <div style="max-height:380px; overflow-y:scroll; width:2640px" id="scroll_body">
                    <table cellspacing="0" border="1" class="rpt_table" width="2610" id="table_body" rules="all">
                    <? 
                        $sl = 1;
                        $tot_packing_bal=$tot_packing_comm=$tot_packing_comm=$tot_re_iron=$tot_iron_com=$tot_iron_com=$tot_iron_bal=$tot_knitting_today=$tot_knitting_com=$tot_kintting_bal=0;
                        $tot_linking_today=$tot_linking_comp=$tot_linking_bal=$tot_triming_today=$tot_triming_comp=$tot_triming_bal=$tot_wash_bal=$tot_sewing_today=$tot_sewing_comm=0;
                        $tot_mending_today=$tot_mending_comp=$tot_mending_bal=$tot_wash_today=$tot_wash_comp=$tot_sewing_bal=$tot_pqc_today=$tot_pqc_comp=$tot_pqc_bal=$tot_packing_today= $tot_iron_today=0;
                        foreach($po_arr_sub as $working_com=>$working_com_data)
                        {
                            foreach($working_com_data as $job_no=>$value)
                            {
                                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                $buyer_name=$style_ref_no=$gauge=$job_no_prefix_num=$job_no=$po_number=$shiping_status=""; $pubshipment_date="";
                                $po_qty=$plan_cut=$excess_cut=0;
                                $exdata=explode("___",$value);
                                $buyer_name=$exdata[0];
                                $buyer_name=$exdata[0];
                                $style_ref_no=$exdata[1];
                                $gauge=$exdata[2];
                                $job_no_prefix_num=$exdata[3];
                                $job_no=$exdata[4];
                                $po_number=$exdata[5];
                            
                                $po_qty=$job_data_array[$job_no]['po_quantity'];
                                $plan_cut=$job_data_array[$job_no]['plan_cut'];
                                $excess_cut=$job_data_array[$job_no]['excess_cut'];
                                
                                $shiping_status=$exdata[9];
                                $pubshipment_date=$exdata[10];
                                $production_date=$exdata[11];
                                $company_name=$exdata[12];
                                $wo_company_name=$exdata[13];
                                $season=$exdata[14];

                                $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));
                                
                                $knitting_com=$inspection_qnty=$wash_comp=$attach_comm=$sewing_comm=$iron_com=$re_iron=$packing_comm=$shipment_com=$linking_comp=$mending_comm=$triming_comm=$pqc_com=$iron_n_comm=0;
                                $knitting_com=$production_arr_sub[$working_com][$job_no][1]['g'];
                                $inspection_qnty=$inspection_arr_sub[$job_no]['ins'];
                                $linking_comp=$production_arr_sub[$working_com][$job_no][4]['g'];
                                $wash_comp=$production_arr_sub[$working_com][$job_no][3]['g'];
                                $attach_comm=$production_arr_sub[$working_com][$job_no][11]['g'];
                                $sewing_comm=$production_arr_sub[$working_com][$job_no][5]['g'];
                                $iron_com=$production_arr_sub[$working_com][$job_no][7]['g']+$production_arr_sub[$working_com][$job_no][67]['g'];
                                $re_iron=$production_arr_sub[$working_com][$job_no][7]['r']+$production_arr_sub[$working_com][$job_no][67]['r'];
                                $packing_comm=$production_arr_sub[$working_com][$job_no][8]['g'];
                                $iron_n_comm=$production_arr_sub[$working_com][$job_no][67]['g'];
                                $re_iron_n_comm=$production_arr_sub[$working_com][$job_no][67]['r'];
                                $triming_comm=$production_arr_sub[$working_com][$job_no][111]['g'];
                                $mending_comm=$production_arr_sub[$working_com][$job_no][112]['g'];
                                $pqc_com=$production_arr_sub[$working_com][$job_no][114]['g'];
                                // ================================ balance ============================
                                $kintting_bal=$inspe_bal=$makeup_bal=$wash_bal=$attach_bal=$sewing_bal=$iron_bal=$packing_bal=$shipment_acc_bal=$linking_bal=$mending_bal=$triming_bal=$pqc_bal=0;
                                $kintting_bal=$plan_cut-$knitting_com;
                                $inspe_bal=$plan_cut-$inspection_qnty;
                                $linking_bal=$plan_cut-$linking_comp;
                                $triming_bal=$plan_cut-$triming_comm;
                                $mending_bal=$plan_cut-$mending_comm;
                                $pqc_bal=$plan_cut-$pqc_com;
                                $wash_bal=$plan_cut-$wash_comp;
                                $attach_bal=$plan_cut-$attach_comm;
                                $sewing_bal=$plan_cut-$sewing_comm;
                                $iron_bal=$plan_cut-$iron_n_comm;
                                $packing_bal=$plan_cut-$packing_comm;
                                $shipment_acc_bal=$po_qty-$shipment_com;
                                // ================================== wip ===============================
                                $knitting_wip=$ins_wip=$makeup_wip=$wash_wip=$attach_wip=$sewing_wip=$packing_percent=$linking_wip=$mending_wip=$triming_wip=$pqc_wip=0;
                                
                                $ins_wip=$knitting_com-$inspection_qnty;
                                $linking_wip=$inspection_qnty-$linking_comp;
                                $triming_wip=$linking_comp-$triming_comm;
                                $mending_wip=$triming_comm-$mending_comm;
                                $mending_wip=$triming_comm-$mending_comm;
                                $wash_wip=$mending_comm-$wash_comp;
                                $attach_wip=$wash_comp-$attach_comm;
                                $sewing_wip=$attach_comm-$sewing_comm;
                                $pqc_wip=$sewing_comm-$pqc_com;
                                $iron_wip=$pqc_com-$iron_n_comm;
                                $packing_percent=($packing_comm/$po_qty)*100;
                                // ================================== today prod =======================================
                                $knitting_com_today=$inspection_qnty_today=$linking_comp_today=$wash_comp_today=$attach_comm_today=$sewing_comm_today=$iron_com_today=$re_iron_today=$packing_comm_today=$shipment_com_today=$pqc_com_today=0;
                                // if(strtotime($production_date) == strtotime($txt_date))
                                // {
                                    $knitting_com_today     = $production_arr[$working_com][$job_no][1]['today'];
                                    $inspection_qnty_today  = $inspection_arr[$job_no]['today'];
                                    $linking_comp_today      = $production_arr[$working_com][$job_no][4]['today'];
                                    $wash_comp_today        = $production_arr[$working_com][$job_no][3]['today'];
                                    $attach_comm_today      = $production_arr[$working_com][$job_no][11]['today'];
                                    $sewing_comm_today      = $production_arr[$working_com][$job_no][5]['today'];
                                    $iron_com_today         = $production_arr[$working_com][$job_no][7]['today']+$production_arr[$working_com][$job_no][67]['today'];
                                    $re_iron_today          = $production_arr[$working_com][$job_no][7]['today_re']+$production_arr[$working_com][$job_no][67]['today_re'];
                                    $packing_comm_today     = $production_arr[$working_com][$job_no][8]['today'];
                                    $iron_n_comm_today      = $production_arr[$working_com][$job_no][67]['today'];
                                    $re_iron_n_comm_today   = $production_arr[$working_com][$job_no][67]['today_re'];
                                    $triming_comm_today     = $production_arr[$working_com][$job_no][111]['today'];
                                    $mending_comm_today     = $production_arr[$working_com][$job_no][112]['today'];
                                    $pqc_com_today          = $production_arr[$working_com][$job_no][114]['today'];
                                // }
                                // ===================================== prev day prod ======================================
                                $knitting_com_yestarday=$inspection_qnty_yestarday=$linking_comp_yestarday=$wash_comp_yestarday=$attach_comm_yestarday=$sewing_comm_yestarday=$iron_com_yestarday=$re_iron_yestarday=$packing_comm_yestarday=$shipment_com_yestarday=$pqc_com_yestarday=0;
                                // if(strtotime($production_date) == strtotime($prev_day))
                                // {
                                    $knitting_com_yestarday     = $production_arr[$working_com][$job_no][1]['yesterday'];
                                    $inspection_qnty_yestarday  = $inspection_arr[$job_no]['yesterday'];
                                    $linking_comp_yestarday      = $production_arr[$working_com][$job_no][4]['yesterday'];
                                    $wash_comp_yestarday        = $production_arr[$working_com][$job_no][3]['yesterday'];
                                    $attach_comm_yestarday      = $production_arr[$working_com][$job_no][11]['yesterday'];
                                    $sewing_comm_yestarday      = $production_arr[$working_com][$job_no][5]['yesterday'];
                                    $iron_com_yestarday         = $production_arr[$working_com][$job_no][7]['yesterday']+$production_arr[$working_com][$job_no][67]['yesterday'];
                                    $re_iron_yestarday          = $production_arr[$working_com][$job_no][7]['yesterday_re']+$production_arr[$working_com][$job_no][67]['yesterday_re'];
                                    $packing_comm_yestarday     = $production_arr[$working_com][$job_no][8]['yesterday'];
                                    $iron_n_comm_yestarday      = $production_arr[$working_com][$job_no][67]['yesterday'];
                                    $re_iron_n_comm_yestarday   = $production_arr[$working_com][$job_no][67]['yesterday_re'];
                                    $triming_comm_yestarday     = $production_arr[$working_com][$job_no][111]['yesterday'];
                                    $mending_comm_yestarday     = $production_arr[$working_com][$job_no][112]['yesterday'];
                                    $pqc_com_yestarday          = $production_arr[$working_com][$job_no][114]['yesterday'];
                                // }
                                
                                $packing_status="Running"; $shpment_date='';
                                
                                if($packing_comm>=$plan_cut){
                                    $ship_status_color="#FFA500";
                                    $packing_status="Complete";
                                } 
                                 else if($packing_comm<$plan_cut){
                                    $packing_status="Running";
                                    $ship_status_color="";
                                 } 
                                                    
                                ?>
                                 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td width="30"><? echo $sl; ?></td>    
                                   
                                    <td width="100" style="word-break:break-all"><? echo $supplier_arr[$working_com]; ?>&nbsp;</td>
                                    <td width="100" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?>&nbsp;</td>
                                    <td width="100" style="word-break:break-all"><? echo $style_ref_no; ?>&nbsp;</td>
                                    <td width="80" style="word-break:break-all"><? echo $gauge_arr[$gauge]; ?>&nbsp;</td>
                                    <td width="80" style="word-break:break-all"><? echo '&nbsp;'.change_date_format($shipment_date_arr[$job_no]); ?></td>                   
                                    <td width="80" align="right"><? echo $po_qty; ?></td>
                                    <td width="80" align="right"><? echo $plan_cut; ?></td>
                                   

                                      <?php if(!empty($knitting_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $knitting_com_today; ?></td>
                                    <td width="70" align="right"><? echo $knitting_com; ?></td>
                                    <td width="70" align="right"><? echo $kintting_bal; ?></td>
                                    
                                 
                                    
                                  

                                      <?php if(!empty($linking_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>"><? echo $linking_comp_today; ?></td>
                                    <td width="70" align="right"><? echo $linking_comp; ?></td>
                                    <td width="70" align="right"><? echo $linking_bal; ?></td>
                                    
                                   

                                     <?php if(!empty($triming_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $triming_comm_today; ?></td>
                                    <td width="70" align="right"><? echo $triming_comm; ?></td>
                                    <td width="70" align="right"><? echo $triming_bal; ?></td>
                                    
                                  

                                     <?php if(!empty($mending_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $mending_comm_today; ?></td>
                                    <td width="70" align="right"><? echo $mending_comm; ?></td>
                                    <td width="70" align="right"><? echo $mending_bal; ?></td>
                                    
                                   

                                    <?php if(!empty($wash_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $wash_comp_today; ?></td>
                                    <td width="70" align="right"><? echo $wash_comp; ?></td>
                                    <td width="70" align="right"><? echo $wash_bal; ?></td>
                                
                                  
                                     <?php if(!empty($sewing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $sewing_comm_today; ?></td>
                                    <td width="70" align="right"><? echo $sewing_comm; ?></td>
                                    <td width="70" align="right"><? echo $sewing_bal; ?></td>
                                    
                                   

                                    <?php if(!empty($pqc_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $pqc_com_today; ?></td>
                                    <td width="70" align="right"><? echo $pqc_com; ?></td>
                                    <td width="70" align="right"><? echo $pqc_bal; ?></td>
                                    
                                 

                                     <?php if(!empty($iron_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $iron_com_today; ?></td>
                                    <td width="70" align="right"><? echo $iron_comm; ?></td>
                                    <td width="70" align="right"><? echo $iron_bal; ?></td>
                                    <td width="70" align="right"><? echo $re_iron; ?></td>
                                    
                                   

                                     <?php if(!empty($packing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $packing_comm_today; ?></td>
                                    <td width="70" align="right"><? echo $packing_comm; ?></td>
                                    <td width="70" align="right"><? echo $packing_bal; ?></td>
                                    
                                </tr>
                                <?
                                $i++;
                                $sl++;
                                $tot_knitting_yesterday += $knitting_com_yestarday;
                                $tot_knitting_today += $knitting_com_today;
                                $tot_knitting_com+=$knitting_com;
                                $tot_kintting_bal+=$kintting_bal;
                                $tot_knitting_wip+=$knitting_wip;
                                
                                $tot_inspection_yesterday+=$inspection_qnty_yestarday;
                                $tot_inspection_today+=$inspection_qnty_today;
                                $tot_inspection_qnty+=$inspection_qnty;
                                $tot_inspe_bal+=$inspe_bal;
                                $tot_ins_wip+=$ins_wip;

                                $tot_linking_yesterday+=$linking_comp_yestarday;
                                $tot_linking_today+=$linking_comp_today;
                                $tot_linking_comp+=$linking_comp;
                                $tot_linking_bal+=$linking_bal;
                                $tot_linking_wip+=$linking_wip;

                                $tot_trimingyesterday+=$trimingcomp_yestarday;
                                $tot_triming_today+=$triming_comm_today;
                                $tot_triming_comp+=$triming_comm;
                                $tot_triming_bal+=$triming_bal;
                                $tot_trimingwip+=$trimingwip;

                                $tot_mending_yesterday+=$mending_comp_yestarday;
                                $tot_mending_today+=$mending_comm_today;
                                $tot_mending_comp+=$mending_comm;
                                $tot_mending_bal+=$mending_bal;
                                $tot_mending_wip+=$mending_wip;
                                
                                $tot_wash_yesterday+=$wash_comp_yestarday;
                                $tot_wash_today+=$wash_comp_today;
                                $tot_wash_comp+=$wash_comp;
                                $tot_wash_bal+=$wash_bal;
                                $tot_wash_wip+=$wash_wip;
                                
                                $tot_attach_yesterday+=$attach_comm_yestarday;
                                $tot_attach_today+=$attach_comm_today;
                                $tot_attach_comm+=$attach_comm;
                                $tot_attach_bal+=$attach_bal;
                                $tot_attach_wip+=$attach_wip;
                                
                                $tot_sewing_yesterday+=$sewing_comm_yestarday;
                                $tot_sewing_today+=$sewing_comm_today;
                                $tot_sewing_comm+=$sewing_comm;
                                $tot_sewing_bal+=$sewing_bal;
                                $tot_sewing_wip+=$sewing_wip;
                                
                                $tot_pqc_yesterday+=$pqc_com_yestarday;
                                $tot_pqc_today+=$pqc_com_today;
                                $tot_pqc_comp+=$pqc_com;
                                $tot_pqc_bal+=$pqc_bal;
                                $tot_pqc_wip+=$pqc_wip;
                                
                                $tot_iron_wip+=$iron_wip;
                                $tot_iron_yesterday+=$iron_com_yestarday;
                                $tot_iron_today+=$iron_com_today;
                                $tot_iron_com+=$iron_com;
                                $tot_iron_bal+=$iron_bal;
                                $tot_re_iron+=$re_iron;
                                
                                $tot_packing_comm+=$packing_comm;
                                $tot_packing_bal+=$packing_bal;
                                $tot_shipment_com+=$shipment_com;
                                $tot_shipment_acc_bal+=$shipment_acc_bal;

                                 $tot_packing_today+=$packing_comm_today;
                               
                            }
                        }
                        ?>
                    </table>
                </div>
                <table width="2610" cellspacing="0" border="1" class="tbl_bottom" rules="all">
                    <tr>
                        <td width="30">&nbsp;</td> 
                        
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        
                        <td width="100">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">Total:</td>                    
                        
                       
                        <td width="70" id=""><? echo $tot_knitting_today; ?></td>
                        <td width="70" id=""><? echo $tot_knitting_com; ?></td>
                        <td width="70" id=""><? echo $tot_kintting_bal; ?></td>
                        
                      
                        
                      

                         <?php if(!empty($tot_linking_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_linking_today; ?></td>
                        <td width="70" id=""><? echo $tot_linking_comp; ?></td>
                        <td width="70" id=""><? echo $tot_linking_bal; ?></td>
                      

                         <?php if(!empty($tot_triming_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_triming_today; ?></td>
                        <td width="70" id=""><? echo $tot_triming_comp; ?></td>
                        <td width="70" id=""><? echo $tot_triming_bal; ?></td>
                      

                        <?php if(!empty($tot_mending_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_mending_today; ?></td>
                        <td width="70" id=""><? echo $tot_mending_comp; ?></td>
                        <td width="70" id=""><? echo $tot_mending_bal; ?></td>
                        
                     

                         <?php if(!empty($tot_wash_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_wash_today; ?></td>
                        <td width="70" id=""><? echo $tot_wash_comp; ?></td>
                        <td width="70" id=""><? echo $tot_wash_bal; ?></td>
                        
                     
                       

                          <?php if(!empty($tot_sewing_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_sewing_today; ?></td>
                        <td width="70" id=""><? echo $tot_sewing_comm; ?></td>
                        <td width="70" id=""><? echo $tot_sewing_bal; ?></td>
                        
                      

                         <?php if(!empty($tot_pqc_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                         
                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_pqc_today; ?></td>
                        <td width="70" id=""><? echo $tot_pqc_comp; ?></td>
                        <td width="70" id=""><? echo $tot_pqc_bal; ?></td>
                        
                       
                        
                        <td width="70" id=""><? echo $tot_iron_today; ?></td>
                        <td width="70" id=""><? echo $tot_iron_com; ?></td>
                        <td width="70" id=""><? echo $tot_iron_bal; ?></td>
                        <td width="70" id=""><? echo $tot_re_iron; ?></td>
                        
                       
                        <td width="70" id=""><? echo $tot_packing_today; ?></td>
                        <td width="70" id=""><? echo $tot_packing_comm; ?></td>
                        <td width="70" id=""><? echo $tot_packing_bal; ?></td>
                        
                    </tr>
                </table>
            </div>
            <!-- inhouse and subcon-->
            <div> 
                <table width="2610" cellspacing="0" border="1" class="rpt_table" rules="all" id="">
                    <caption style="font-size:18px;font-weight:bold;justify-content: left;text-align: left;">In-House + Sub-Contract(Today)</caption>
                    <thead>
                        <tr>
                            <th rowspan="2" width="30">Sl.</th> 
                            <th width="620" colspan="7">Order Details</th>

                            <th width="210" colspan="3">Knitting Production</th>
                          
                            <th width="210" colspan="3">Linking Production</th>

                            <th width="210" colspan="3">Trimming Complete</th>

                            <th width="210" colspan="3">Mending Complete</th>

                            <th width="210" colspan="3">Wash Production</th>
                       
                            <th width="210" colspan="3">Sewing Prodution</th>

                            <th width="210" colspan="3">PQC</th>

                            <th width="280" colspan="4">Iron Production</th>

                            <th width="210" colspan="3">Packing and Finishing</th>
                        </tr>
                        <tr>
                           
                               <!--  Order Details -->
                            <th width="100">Working Company</th>
                            <th width="100">Buyer</th>
                            <th width="100">Style</th>

                            <th width="80">GG</th>
                             <th width="80">Delivery date</th>
                            <th width="80">Order Qty</th>
                            <th width="80">Plan Cut Qty</th>
                          
                            
                           <!--  Knitting -->
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            
                        
                            <!--  Linking Production -->
                            <th width="70">Today </th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                            
                                <!-- Trimming --> 
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70">Balance</th>
                            
                            <!-- Mending -->
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            
                            <!-- Wash -->
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            
                         
                           
                            <!-- Sewing -->
                            <th width="70">Today </th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                            
                                <!-- PQC -->
                            <th width="70">Today </th>
                            <th width="70"> Total</th>
                            <th width="70"> Balance</th>
                            
                           <!-- Iron  -->
                            <th width="70">Today </th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            <th width="70">Re-Iron</th>
                            
                           <!-- Packing -->
                            <th width="70">Today </th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                           
                            
                        </tr>
                    </thead>
                </table>
                <div style="max-height:380px; overflow-y:scroll; width:2610px" id="scroll_body">
                <table cellspacing="0" border="1" class="rpt_table" width="2610" id="table_body" rules="all">
                    <? 
                        $sl = 1;
                        $tot_packing_bal=$tot_packing_comm=$tot_packing_comm=$tot_re_iron=$tot_iron_com=$tot_iron_com=$tot_iron_bal=$tot_knitting_today=$tot_knitting_com=$tot_kintting_bal=0;
                        $tot_linking_today=$tot_linking_comp=$tot_linking_bal=$tot_triming_today=$tot_triming_comp=$tot_triming_bal=$tot_wash_bal=$tot_sewing_today=$tot_sewing_comm=0;
                        $tot_mending_today=$tot_mending_comp=$tot_mending_bal=$tot_wash_today=$tot_wash_comp=$tot_sewing_bal=$tot_pqc_today=$tot_pqc_comp=$tot_pqc_bal=$tot_packing_today= $tot_iron_today=0;
                        foreach($po_arr_sub as $working_com=>$working_com_data)
                        {
                            foreach($working_com_data as $job_no=>$value)
                            {
                                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                $buyer_name=$style_ref_no=$gauge=$job_no_prefix_num=$job_no=$po_number=$shiping_status=""; $pubshipment_date="";
                                $po_qty=$plan_cut=$excess_cut=0;
                                $exdata=explode("___",$value);
                                $buyer_name=$exdata[0];
                                $buyer_name=$exdata[0];
                                $style_ref_no=$exdata[1];
                                $gauge=$exdata[2];
                                $job_no_prefix_num=$exdata[3];
                                $job_no=$exdata[4];
                                $po_number=$exdata[5];
                            
                                $po_qty=$job_data_array[$job_no]['po_quantity'];
                                $plan_cut=$job_data_array[$job_no]['plan_cut'];
                                $excess_cut=$job_data_array[$job_no]['excess_cut'];
                                
                                $shiping_status=$exdata[9];
                                $pubshipment_date=$exdata[10];
                                $production_date=$exdata[11];
                                $company_name=$exdata[12];
                                $wo_company_name=$exdata[13];
                                $season=$exdata[14];

                                $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));
                                
                                $knitting_com=$inspection_qnty=$wash_comp=$attach_comm=$sewing_comm=$iron_com=$re_iron=$packing_comm=$shipment_com=$linking_comp=$mending_comm=$triming_comm=$pqc_com=$iron_n_comm=0;
                                $knitting_com=$production_arr_sub[$working_com][$job_no][1]['g'];
                                $inspection_qnty=$inspection_arr_sub[$job_no]['ins'];
                                $linking_comp=$production_arr_sub[$working_com][$job_no][4]['g'];
                                $wash_comp=$production_arr_sub[$working_com][$job_no][3]['g'];
                                $attach_comm=$production_arr_sub[$working_com][$job_no][11]['g'];
                                $sewing_comm=$production_arr_sub[$working_com][$job_no][5]['g'];
                                $iron_com=$production_arr_sub[$working_com][$job_no][7]['g']+$production_arr_sub[$working_com][$job_no][67]['g'];
                                $re_iron=$production_arr_sub[$working_com][$job_no][7]['r']+$production_arr_sub[$working_com][$job_no][67]['r'];
                                $packing_comm=$production_arr_sub[$working_com][$job_no][8]['g'];
                                $iron_n_comm=$production_arr_sub[$working_com][$job_no][67]['g'];
                                $re_iron_n_comm=$production_arr_sub[$working_com][$job_no][67]['r'];
                                $triming_comm=$production_arr_sub[$working_com][$job_no][111]['g'];
                                $mending_comm=$production_arr_sub[$working_com][$job_no][112]['g'];
                                $pqc_com=$production_arr_sub[$working_com][$job_no][114]['g'];
                                // ================================ balance ============================
                                $kintting_bal=$inspe_bal=$makeup_bal=$wash_bal=$attach_bal=$sewing_bal=$iron_bal=$packing_bal=$shipment_acc_bal=$linking_bal=$mending_bal=$triming_bal=$pqc_bal=0;
                                $kintting_bal=$plan_cut-$knitting_com;
                                $inspe_bal=$plan_cut-$inspection_qnty;
                                $linking_bal=$plan_cut-$linking_comp;
                                $triming_bal=$plan_cut-$triming_comm;
                                $mending_bal=$plan_cut-$mending_comm;
                                $pqc_bal=$plan_cut-$pqc_com;
                                $wash_bal=$plan_cut-$wash_comp;
                                $attach_bal=$plan_cut-$attach_comm;
                                $sewing_bal=$plan_cut-$sewing_comm;
                                $iron_bal=$plan_cut-$iron_n_comm;
                                $packing_bal=$plan_cut-$packing_comm;
                                $shipment_acc_bal=$po_qty-$shipment_com;
                                // ================================== wip ===============================
                                $knitting_wip=$ins_wip=$makeup_wip=$wash_wip=$attach_wip=$sewing_wip=$packing_percent=$linking_wip=$mending_wip=$triming_wip=$pqc_wip=0;
                                
                                $ins_wip=$knitting_com-$inspection_qnty;
                                $linking_wip=$inspection_qnty-$linking_comp;
                                $triming_wip=$linking_comp-$triming_comm;
                                $mending_wip=$triming_comm-$mending_comm;
                                $mending_wip=$triming_comm-$mending_comm;
                                $wash_wip=$mending_comm-$wash_comp;
                                $attach_wip=$wash_comp-$attach_comm;
                                $sewing_wip=$attach_comm-$sewing_comm;
                                $pqc_wip=$sewing_comm-$pqc_com;
                                $iron_wip=$pqc_com-$iron_n_comm;
                                $packing_percent=($packing_comm/$po_qty)*100;
                                // ================================== today prod =======================================
                                $knitting_com_today=$inspection_qnty_today=$linking_comp_today=$wash_comp_today=$attach_comm_today=$sewing_comm_today=$iron_com_today=$re_iron_today=$packing_comm_today=$shipment_com_today=$pqc_com_today=0;
                                // if(strtotime($production_date) == strtotime($txt_date))
                                // {
                                    $knitting_com_today     = $production_arr[$working_com][$job_no][1]['today'];
                                    $inspection_qnty_today  = $inspection_arr[$job_no]['today'];
                                    $linking_comp_today      = $production_arr[$working_com][$job_no][4]['today'];
                                    $wash_comp_today        = $production_arr[$working_com][$job_no][3]['today'];
                                    $attach_comm_today      = $production_arr[$working_com][$job_no][11]['today'];
                                    $sewing_comm_today      = $production_arr[$working_com][$job_no][5]['today'];
                                    $iron_com_today         = $production_arr[$working_com][$job_no][7]['today']+$production_arr[$working_com][$job_no][67]['today'];
                                    $re_iron_today          = $production_arr[$working_com][$job_no][7]['today_re']+$production_arr[$working_com][$job_no][67]['today_re'];
                                    $packing_comm_today     = $production_arr[$working_com][$job_no][8]['today'];
                                    $iron_n_comm_today      = $production_arr[$working_com][$job_no][67]['today'];
                                    $re_iron_n_comm_today   = $production_arr[$working_com][$job_no][67]['today_re'];
                                    $triming_comm_today     = $production_arr[$working_com][$job_no][111]['today'];
                                    $mending_comm_today     = $production_arr[$working_com][$job_no][112]['today'];
                                    $pqc_com_today          = $production_arr[$working_com][$job_no][114]['today'];
                                // }
                                // ===================================== prev day prod ======================================
                                $knitting_com_yestarday=$inspection_qnty_yestarday=$linking_comp_yestarday=$wash_comp_yestarday=$attach_comm_yestarday=$sewing_comm_yestarday=$iron_com_yestarday=$re_iron_yestarday=$packing_comm_yestarday=$shipment_com_yestarday=$pqc_com_yestarday=0;
                                // if(strtotime($production_date) == strtotime($prev_day))
                                // {
                                    $knitting_com_yestarday     = $production_arr[$working_com][$job_no][1]['yesterday'];
                                    $inspection_qnty_yestarday  = $inspection_arr[$job_no]['yesterday'];
                                    $linking_comp_yestarday      = $production_arr[$working_com][$job_no][4]['yesterday'];
                                    $wash_comp_yestarday        = $production_arr[$working_com][$job_no][3]['yesterday'];
                                    $attach_comm_yestarday      = $production_arr[$working_com][$job_no][11]['yesterday'];
                                    $sewing_comm_yestarday      = $production_arr[$working_com][$job_no][5]['yesterday'];
                                    $iron_com_yestarday         = $production_arr[$working_com][$job_no][7]['yesterday']+$production_arr[$working_com][$job_no][67]['yesterday'];
                                    $re_iron_yestarday          = $production_arr[$working_com][$job_no][7]['yesterday_re']+$production_arr[$working_com][$job_no][67]['yesterday_re'];
                                    $packing_comm_yestarday     = $production_arr[$working_com][$job_no][8]['yesterday'];
                                    $iron_n_comm_yestarday      = $production_arr[$working_com][$job_no][67]['yesterday'];
                                    $re_iron_n_comm_yestarday   = $production_arr[$working_com][$job_no][67]['yesterday_re'];
                                    $triming_comm_yestarday     = $production_arr[$working_com][$job_no][111]['yesterday'];
                                    $mending_comm_yestarday     = $production_arr[$working_com][$job_no][112]['yesterday'];
                                    $pqc_com_yestarday          = $production_arr[$working_com][$job_no][114]['yesterday'];
                                // }
                                
                                $packing_status="Running"; $shpment_date='';
                                
                                if($packing_comm>=$plan_cut){
                                    $ship_status_color="#FFA500";
                                    $packing_status="Complete";
                                } 
                                 else if($packing_comm<$plan_cut){
                                    $packing_status="Running";
                                    $ship_status_color="";
                                 } 
                                                    
                                ?>
                                 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td width="30"><? echo $sl; ?></td>    
                                   
                                    <td width="100" style="word-break:break-all"><? echo $supplier_arr[$working_com]; ?>&nbsp;</td>
                                    <td width="100" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?>&nbsp;</td>
                                    <td width="100" style="word-break:break-all"><? echo $style_ref_no; ?>&nbsp;</td>
                                    <td width="80" style="word-break:break-all"><? echo $gauge_arr[$gauge]; ?>&nbsp;</td>
                                    <td width="80" style="word-break:break-all"><? echo '&nbsp;'.change_date_format($shipment_date_arr[$job_no]); ?></td>                   
                                    <td width="80" align="right"><? echo $po_qty; ?></td>
                                    <td width="80" align="right"><? echo $plan_cut; ?></td>
                                   

                                      <?php if(!empty($knitting_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $knitting_com_today; ?></td>
                                    <td width="70" align="right"><? echo $knitting_com; ?></td>
                                    <td width="70" align="right"><? echo $kintting_bal; ?></td>
                                    
                                 
                                    
                                  

                                      <?php if(!empty($linking_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>"><? echo $linking_comp_today; ?></td>
                                    <td width="70" align="right"><? echo $linking_comp; ?></td>
                                    <td width="70" align="right"><? echo $linking_bal; ?></td>
                                    
                                   

                                     <?php if(!empty($triming_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $triming_comm_today; ?></td>
                                    <td width="70" align="right"><? echo $triming_comm; ?></td>
                                    <td width="70" align="right"><? echo $triming_bal; ?></td>
                                    
                                  

                                     <?php if(!empty($mending_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $mending_comm_today; ?></td>
                                    <td width="70" align="right"><? echo $mending_comm; ?></td>
                                    <td width="70" align="right"><? echo $mending_bal; ?></td>
                                    
                                   

                                    <?php if(!empty($wash_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $wash_comp_today; ?></td>
                                    <td width="70" align="right"><? echo $wash_comp; ?></td>
                                    <td width="70" align="right"><? echo $wash_bal; ?></td>
                                
                                  
                                     <?php if(!empty($sewing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $sewing_comm_today; ?></td>
                                    <td width="70" align="right"><? echo $sewing_comm; ?></td>
                                    <td width="70" align="right"><? echo $sewing_bal; ?></td>
                                    
                                   

                                    <?php if(!empty($pqc_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $pqc_com_today; ?></td>
                                    <td width="70" align="right"><? echo $pqc_com; ?></td>
                                    <td width="70" align="right"><? echo $pqc_bal; ?></td>
                                    
                                 

                                     <?php if(!empty($iron_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $iron_com_today; ?></td>
                                    <td width="70" align="right"><? echo $iron_comm; ?></td>
                                    <td width="70" align="right"><? echo $iron_bal; ?></td>
                                    <td width="70" align="right"><? echo $re_iron; ?></td>
                                    
                                   

                                     <?php if(!empty($packing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $packing_comm_today; ?></td>
                                    <td width="70" align="right"><? echo $packing_comm; ?></td>
                                    <td width="70" align="right"><? echo $packing_bal; ?></td>
                                    
                                </tr>
                                <?
                                $i++;
                                $sl++;
                                $tot_knitting_yesterday += $knitting_com_yestarday;
                                $tot_knitting_today += $knitting_com_today;
                                $tot_knitting_com+=$knitting_com;
                                $tot_kintting_bal+=$kintting_bal;
                                $tot_knitting_wip+=$knitting_wip;
                                
                                $tot_inspection_yesterday+=$inspection_qnty_yestarday;
                                $tot_inspection_today+=$inspection_qnty_today;
                                $tot_inspection_qnty+=$inspection_qnty;
                                $tot_inspe_bal+=$inspe_bal;
                                $tot_ins_wip+=$ins_wip;

                                $tot_linking_yesterday+=$linking_comp_yestarday;
                                $tot_linking_today+=$linking_comp_today;
                                $tot_linking_comp+=$linking_comp;
                                $tot_linking_bal+=$linking_bal;
                                $tot_linking_wip+=$linking_wip;

                                $tot_trimingyesterday+=$trimingcomp_yestarday;
                                $tot_triming_today+=$triming_comm_today;
                                $tot_triming_comp+=$triming_comm;
                                $tot_triming_bal+=$triming_bal;
                                $tot_trimingwip+=$trimingwip;

                                $tot_mending_yesterday+=$mending_comp_yestarday;
                                $tot_mending_today+=$mending_comm_today;
                                $tot_mending_comp+=$mending_comm;
                                $tot_mending_bal+=$mending_bal;
                                $tot_mending_wip+=$mending_wip;
                                
                                $tot_wash_yesterday+=$wash_comp_yestarday;
                                $tot_wash_today+=$wash_comp_today;
                                $tot_wash_comp+=$wash_comp;
                                $tot_wash_bal+=$wash_bal;
                                $tot_wash_wip+=$wash_wip;
                                
                                $tot_attach_yesterday+=$attach_comm_yestarday;
                                $tot_attach_today+=$attach_comm_today;
                                $tot_attach_comm+=$attach_comm;
                                $tot_attach_bal+=$attach_bal;
                                $tot_attach_wip+=$attach_wip;
                                
                                $tot_sewing_yesterday+=$sewing_comm_yestarday;
                                $tot_sewing_today+=$sewing_comm_today;
                                $tot_sewing_comm+=$sewing_comm;
                                $tot_sewing_bal+=$sewing_bal;
                                $tot_sewing_wip+=$sewing_wip;
                                
                                $tot_pqc_yesterday+=$pqc_com_yestarday;
                                $tot_pqc_today+=$pqc_com_today;
                                $tot_pqc_comp+=$pqc_com;
                                $tot_pqc_bal+=$pqc_bal;
                                $tot_pqc_wip+=$pqc_wip;
                                
                                $tot_iron_wip+=$iron_wip;
                                $tot_iron_yesterday+=$iron_com_yestarday;
                                $tot_iron_today+=$iron_com_today;
                                $tot_iron_com+=$iron_com;
                                $tot_iron_bal+=$iron_bal;
                                $tot_re_iron+=$re_iron;
                                
                                $tot_packing_comm+=$packing_comm;
                                $tot_packing_bal+=$packing_bal;
                                $tot_shipment_com+=$shipment_com;
                                $tot_shipment_acc_bal+=$shipment_acc_bal;

                                 $tot_packing_today+=$packing_comm_today;
                               
                            }
                        }

                        foreach($po_arr as $job_no=>$value)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $buyer_name=$style_ref_no=$gauge=$job_no_prefix_num=$job_no=$po_number=$shiping_status=""; $pubshipment_date="";
                            $po_qty=$plan_cut=$excess_cut=0;
                            $exdata=explode("___",$value);
                            $buyer_name=$exdata[0];
                            $buyer_name=$exdata[0];
                            $style_ref_no=$exdata[1];
                            $gauge=$exdata[2];
                            $job_no_prefix_num=$exdata[3];
                            $job_no=$exdata[4];
                            $po_number=$exdata[5];
                            
                            $po_qty=$job_data_array[$job_no]['po_quantity'];
                            $plan_cut=$job_data_array[$job_no]['plan_cut'];
                            $excess_cut=$job_data_array[$job_no]['excess_cut'];
                            
                            $shiping_status=$exdata[9];
                            $pubshipment_date=$exdata[10];
                            $production_date=$exdata[11];
                            $company_name=$exdata[12];
                            $wo_company_name=$exdata[13];
                            $season=$exdata[14];

                            $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));
                            
                            $knitting_com=$inspection_qnty=$wash_comp=$attach_comm=$sewing_comm=$iron_com=$re_iron=$packing_comm=$shipment_com=$linking_comp=$mending_comm=$triming_comm=$pqc_com=0;
                            $knitting_com=$production_arr[$job_no][1]['g'];
                            $inspection_qnty=$inspection_arr[$job_no]['ins'];
                            $linking_comp=$production_arr[$job_no][4]['g'];
                            $wash_comp=$production_arr[$job_no][3]['g'];
                            $attach_comm=$production_arr[$job_no][11]['g'];
                            $sewing_comm=$production_arr[$job_no][5]['g'];
                            $iron_com=$production_arr[$job_no][7]['g']+$production_arr[$job_no][67]['g'];
                            $re_iron=$production_arr[$job_no][7]['r']+$production_arr[$job_no][67]['r'];
                            $packing_comm=$production_arr[$job_no][8]['g'];
                            $iron_n_comm=$production_arr[$job_no][67]['g'];
                            $re_iron_n_comm=$production_arr[$job_no][67]['r'];
                            $triming_comm=$production_arr[$job_no][111]['g'];
                            $mending_comm=$production_arr[$job_no][112]['g'];
                            $pqc_com=$production_arr[$job_no][114]['g'];
                            // ================================ balance ============================
                            $kintting_bal=$inspe_bal=$makeup_bal=$wash_bal=$attach_bal=$sewing_bal=$iron_bal=$packing_bal=$shipment_acc_bal=$linking_bal=$mending_bal=$triming_bal=$pqc_bal=0;
                            $kintting_bal=$plan_cut-$knitting_com;
                            $inspe_bal=$plan_cut-$inspection_qnty;
                            $linking_bal=$plan_cut-$linking_comp;
                            $triming_bal=$plan_cut-$triming_comm;
                            $mending_bal=$plan_cut-$mending_comm;
                            $pqc_bal=$plan_cut-$pqc_com;
                            $wash_bal=$plan_cut-$wash_comp;
                            $attach_bal=$plan_cut-$attach_comm;
                            $sewing_bal=$plan_cut-$sewing_comm;
                            $iron_bal=$plan_cut-$iron_com;
                            $packing_bal=$plan_cut-$packing_comm;
                            $shipment_acc_bal=$po_qty-$shipment_com;
                            // ================================== wip ===============================
                            $knitting_wip=$ins_wip=$makeup_wip=$wash_wip=$attach_wip=$sewing_wip=$packing_percent=$linking_wip=$mending_wip=$triming_wip=$pqc_wip=0;
                            
                            $ins_wip=$knitting_com-$inspection_qnty;
                            $linking_wip=$inspection_qnty-$linking_comp;
                            $triming_wip=$linking_comp-$triming_comm;
                            $mending_wip=$triming_comm-$mending_comm;
                            $mending_wip=$triming_comm-$mending_comm;
                            $wash_wip=$mending_comm-$wash_comp;
                            $attach_wip=$wash_comp-$attach_comm;
                            $sewing_wip=$attach_comm-$sewing_comm;
                            $pqc_wip=$sewing_comm-$pqc_com;
                            $iron_wip=$pqc_com-$iron_com;
                            $packing_percent=($packing_comm/$po_qty)*100;
                            // ================================== today prod =======================================
                            $knitting_com_today=$inspection_qnty_today=$linking_comp_today=$wash_comp_today=$attach_comm_today=$sewing_comm_today=$iron_com_today=$re_iron_today=$packing_comm_today=$shipment_com_today=$pqc_com_today=0;
                            // if(strtotime($production_date) == strtotime($txt_date))
                            // {
                                $knitting_com_today     = $production_arr[$job_no][1]['today'];
                                $inspection_qnty_today  = $inspection_arr[$job_no]['today'];
                                $linking_comp_today      = $production_arr[$job_no][4]['today'];
                                $wash_comp_today        = $production_arr[$job_no][3]['today'];
                                $attach_comm_today      = $production_arr[$job_no][11]['today'];
                                $sewing_comm_today      = $production_arr[$job_no][5]['today'];
                                $iron_com_today         = $production_arr[$job_no][7]['today']+$production_arr[$job_no][67]['today'];
                                $re_iron_today          = $production_arr[$job_no][7]['today_re']+$production_arr[$job_no][67]['today_re'];
                                $packing_comm_today     = $production_arr[$job_no][8]['today'];
                                $iron_n_comm_today      = $production_arr[$job_no][67]['today'];
                                $re_iron_n_comm_today   = $production_arr[$job_no][67]['today_re'];
                                $triming_comm_today     = $production_arr[$job_no][111]['today'];
                                $mending_comm_today     = $production_arr[$job_no][112]['today'];
                                $pqc_com_today         = $production_arr[$job_no][114]['today'];
                            // }
                            // ===================================== prev day prod ======================================
                            $knitting_com_yestarday=$inspection_qnty_yestarday=$linking_comp_yestarday=$wash_comp_yestarday=$attach_comm_yestarday=$sewing_comm_yestarday=$iron_com_yestarday=$re_iron_yestarday=$packing_comm_yestarday=$shipment_com_yestarday=$pqc_com_yestarday=0;
                            // if(strtotime($production_date) == strtotime($prev_day))
                            // {
                                $knitting_com_yestarday     = $production_arr[$job_no][1]['yesterday'];
                                $inspection_qnty_yestarday  = $inspection_arr[$job_no]['yesterday'];
                                $linking_comp_yestarday      = $production_arr[$job_no][4]['yesterday'];
                                $wash_comp_yestarday        = $production_arr[$job_no][3]['yesterday'];
                                $attach_comm_yestarday      = $production_arr[$job_no][11]['yesterday'];
                                $sewing_comm_yestarday      = $production_arr[$job_no][5]['yesterday'];
                                $iron_com_yestarday         = $production_arr[$job_no][7]['yesterday']+$production_arr[$job_no][67]['yesterday'];
                                $re_iron_yestarday          = $production_arr[$job_no][7]['yesterday_re']+$production_arr[$job_no][67]['yesterday_re'];
                                $packing_comm_yestarday     = $production_arr[$job_no][8]['yesterday'];
                                $iron_n_comm_yestarday      = $production_arr[$job_no][67]['yesterday'];
                                $re_iron_n_comm_yestarday   = $production_arr[$job_no][67]['yesterday_re'];
                                $triming_comm_yestarday     = $production_arr[$job_no][111]['yesterday'];
                                $mending_comm_yestarday     = $production_arr[$job_no][112]['yesterday'];
                                $pqc_com_yestarday          = $production_arr[$job_no][114]['yesterday'];
                            // }
                            
                            $packing_status="Running"; $shpment_date='';
                            $ship_status_color="";
                            
                            if($packing_comm>=$plan_cut)
                            {
                                $packing_status="Complete";

                                 $ship_status_color="#FFA500";
                            }  
                            else if($packing_comm<$plan_cut){
                                $packing_status="Running";
                                // $ship_status_color="#FFC0CB";
                            }
                            $bgcl="#FFFF00"; 
                            if($knitting_com_today >0 || $inspection_qnty_today>0 || $linking_comp_today >0 || $triming_comm_today>0 || $mending_comm_today>0 || $wash_comp_today>0 || $attach_comm_today>0 || $sewing_comm_today>0 ||           $pqc_com_today>0 || $iron_com_today>0 || $packing_comm_today>0){           
                            ?>
                             <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>   

                                <td width="100" style="word-break:break-all"><? echo $company_library[$wo_company_name]; ?></td>
                                <td width="100" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?></td>
                                <td width="100" style="word-break:break-all"><? echo $style_ref_no; ?></td>
                                <td width="80" style="word-break:break-all"><? echo $gauge_arr[$gauge]; ?></td>
                                <td width="80" style="word-break:break-all"><? echo '&nbsp;'.change_date_format($shipment_date_arr[$job_no]); ?></td>
                                <td width="80" align="right"><? echo $po_qty; ?></td>
                                <td width="80" align="right"><? echo $plan_cut; ?></td>
                                 
                                
                              

                                <?php if(!empty($knitting_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                <!--  knitting -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>"><? echo $knitting_com_today; ?></td>
                                <td width="70" align="right"><? echo $knitting_com; ?></td>
                                <td width="70" align="right"><? echo $kintting_bal; ?></td>
                             
                                
                              

                                <?php if(!empty($linking_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                    <!-- linking -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $linking_comp_today; ?></td>
                                <td width="70" align="right"><? echo $linking_comp; ?></td>
                                <td width="70" align="right"><? echo $linking_bal; ?></td>
                                
                               

                                 <?php if(!empty($triming_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                 <!-- triming -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $triming_comm_today; ?></td>
                                <td width="70" align="right"><? echo $triming_comm; ?></td>
                                <td width="70" align="right"><? echo $triming_bal; ?></td>
                                
                                

                                <?php if(!empty($mending_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                     <!-- mending -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $mending_comm_today; ?></td>
                                <td width="70" align="right"><? echo $mending_comm; ?></td>
                                <td width="70" align="right"><? echo $mending_bal; ?></td>
                                
                        
                                <?php if(!empty($wash_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                    <!-- wash -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $wash_comp_today; ?></td>
                                <td width="70" align="right"><? echo $wash_comp; ?></td>
                                <td width="70" align="right"><? echo $wash_bal; ?></td>
                                
                      
                                 <?php if(!empty($sewing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                 <!-- sewing -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $sewing_comm_today; ?></td>
                                <td width="70" align="right"><? echo $sewing_comm; ?></td>
                                <td width="70" align="right"><? echo $sewing_bal; ?></td>
                                
                              
                                 <?php if(!empty($pqc_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                <!--  pqc -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $pqc_com_today; ?></td>
                                <td width="70" align="right"><? echo $pqc_com; ?></td>
                                <td width="70" align="right"><? echo $pqc_bal; ?></td>
                                
                                

                                 <?php if(!empty($iron_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                 <!-- iron -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $iron_com_today; ?></td>
                                <td width="70" align="right"><? echo $iron_com; ?></td>
                                <td width="70" align="right"><? echo $iron_bal; ?></td>
                                <td width="70" align="right"><? echo $re_iron; ?></td>
                                
                               
                                 <?php if(!empty($packing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                 <!-- packing -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $packing_comm_today; ?></td>
                                <td width="70" align="right"><? echo $packing_comm; ?></td>
                                <td width="70" align="right"><? echo $packing_bal; ?></td>
                               
                            </tr>
                            <?

                            $i++;
                            $tot_knitting_yesterday += $knitting_com_yestarday;
                            $tot_knitting_today += $knitting_com_today;
                            $tot_knitting_com+=$knitting_com;
                            $tot_kintting_bal+=$kintting_bal;
                            $tot_knitting_wip+=$knitting_wip;
                            
                            $tot_inspection_yesterday+=$inspection_qnty_yestarday;
                            $tot_inspection_today+=$inspection_qnty_today;
                            $tot_inspection_qnty+=$inspection_qnty;
                            $tot_inspe_bal+=$inspe_bal;
                            $tot_ins_wip+=$ins_wip;

                            $tot_linking_yesterday+=$linking_comp_yestarday;
                            $tot_linking_today+=$linking_comp_today;
                            $tot_linking_comp+=$linking_comp;
                            $tot_linking_bal+=$linking_bal;
                            $tot_linking_wip+=$linking_wip;

                            $tot_trimingyesterday+=$trimingcomp_yestarday;
                            $tot_triming_today+=$triming_comm_today;
                            $tot_triming_comp+=$triming_comm;
                            $tot_triming_bal+=$triming_bal;
                            $tot_trimingwip+=$trimingwip;

                            $tot_mending_yesterday+=$mending_comp_yestarday;
                            $tot_mending_today+=$mending_comm_today;
                            $tot_mending_comp+=$mending_comm;
                            $tot_mending_bal+=$mending_bal;
                            $tot_mending_wip+=$mending_wip;
                            
                            $tot_wash_yesterday+=$wash_comp_yestarday;
                            $tot_wash_today+=$wash_comp_today;
                            $tot_wash_comp+=$wash_comp;
                            $tot_wash_bal+=$wash_bal;
                            $tot_wash_wip+=$wash_wip;
                            
                            $tot_attach_yesterday+=$attach_comm_yestarday;
                            $tot_attach_today+=$attach_comm_today;
                            $tot_attach_comm+=$attach_comm;
                            $tot_attach_bal+=$attach_bal;
                            $tot_attach_wip+=$attach_wip;
                            
                            $tot_sewing_yesterday+=$sewing_comm_yestarday;
                            $tot_sewing_today+=$sewing_comm_today;
                            $tot_sewing_comm+=$sewing_comm;
                            $tot_sewing_bal+=$sewing_bal;
                            $tot_sewing_wip+=$sewing_wip;
                            
                            $tot_pqc_yesterday+=$pqc_com_yestarday;
                            $tot_pqc_today+=$pqc_com_today;
                            $tot_pqc_comp+=$pqc_com;
                            $tot_pqc_bal+=$pqc_bal;
                            $tot_pqc_wip+=$pqc_wip;
                            
                            $tot_iron_wip+=$iron_wip;
                            $tot_iron_yesterday+=$iron_com_yestarday;
                            $tot_iron_today+=$iron_com_today;
                            $tot_iron_com+=$iron_com;
                            $tot_iron_bal+=$iron_bal;
                            $tot_re_iron+=$re_iron;
                            
                            $tot_packing_comm+=$packing_comm;
                            $tot_packing_bal+=$packing_bal;
                            $tot_shipment_com+=$shipment_com;
                            $tot_shipment_acc_bal+=$shipment_acc_bal;
                            $tot_packing_today+=$packing_comm_today;
                            }
                        }
                        ?>
                    </table>
                </div></div>
                <table width="2610" cellspacing="0" border="1" class="tbl_bottom" rules="all">
                    <tr>
                        <td width="30">&nbsp;</td> 
                     
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                      
                        <td width="80">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">Total:</td>                    
                        
                     
                         <?php if(!empty($tot_knitting_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_knitting_today; ?></td>
                        <td width="70" id=""><? echo $tot_knitting_com; ?></td>
                        <td width="70" id=""><? echo $tot_kintting_bal; ?></td>
                        
                    
                     

                         <?php if(!empty($tot_linking_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_linking_today; ?></td>
                        <td width="70" id=""><? echo $tot_linking_comp; ?></td>
                        <td width="70" id=""><? echo $tot_linking_bal; ?></td>
                        
                     

                         <?php if(!empty($tot_triming_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_triming_today; ?></td>
                        <td width="70" id=""><? echo $tot_triming_comp; ?></td>
                        <td width="70" id=""><? echo $tot_triming_bal; ?></td>
                        
                    

                         <?php if(!empty($tot_mending_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_mending_today; ?></td>
                        <td width="70" id=""><? echo $tot_mending_comp; ?></td>
                        <td width="70" id=""><? echo $tot_mending_bal; ?></td>
                        
                     

                         <?php if(!empty($tot_wash_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_wash_today; ?></td>
                        <td width="70" id=""><? echo $tot_wash_comp; ?></td>
                        <td width="70" id=""><? echo $tot_wash_bal; ?></td>
                        
                    
                      
                          <?php if(!empty($tot_sewing_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_sewing_today; ?></td>
                        <td width="70" id=""><? echo $tot_sewing_comm; ?></td>
                        <td width="70" id=""><? echo $tot_sewing_bal; ?></td>
                        
                     

                         <?php if(!empty($tot_pqc_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_pqc_today; ?></td>
                        <td width="70" id=""><? echo $tot_pqc_comp; ?></td>
                        <td width="70" id=""><? echo $tot_pqc_bal; ?></td>
                        
                      
                        <td width="70" id=""><? echo $tot_iron_today; ?></td>
                        <td width="70" id=""><? echo $tot_iron_com; ?></td>
                        <td width="70" id=""><? echo $tot_iron_bal; ?></td>
                        <td width="70" id=""><? echo $tot_re_iron; ?></td>
                        
                        
                        <td width="70" id=""><? echo $tot_packing_today; ?></td>
                        <td width="70" id=""><? echo $tot_packing_comm; ?></td>
                        <td width="70" id=""><? echo $tot_packing_bal; ?></td>
                       
                    </tr>
                </table>
            </div>
        </fieldset>
        <?
    }else  if($reporttype==3)
            {
                    /* =============================================================================================/
            /                                        Main Query                                             /
            / ============================================================================================ */
            $sql="SELECT a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, a.GAUGE, a.job_no_prefix_num as JOB_PREFIX, a.JOB_NO, a.ORDER_UOM, a.total_set_qnty as RATIO,a.season_buyer_wise as SEASON,a.AVG_UNIT_PRICE, b.id as PO_ID, b.PO_NUMBER, b.PO_QUANTITY, b.PLAN_CUT, b.EXCESS_CUT, b.SHIPING_STATUS, b.PUB_SHIPMENT_DATE,c.PRODUCTION_SOURCE,c.PRODUCTION_DATE,c.SERVING_COMPANY, c.PRODUCTION_TYPE, c.production_quantity as PRODUCTION_QUANTITY, c.RE_PRODUCTION_QTY
            from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id
            and b.id=c.po_break_down_id and a.garments_nature=100 
            and c.production_type in (1,3,4,5,7,8,11,67,111,112,114) 
            and a.status_active=1 and a.is_deleted=0 
            and b.status_active=1 and b.is_deleted=0 
            and c.status_active=1 and c.is_deleted=0 
           
            $company_name_cond $buyer_id_cond $date_cond $job_style_cond $order_cond $job_cond $year_cond $ship_status_cond order by a.id ASC";
        // echo $sql;die;
        $sql_result=sql_select($sql);     
        if(count($sql_result)==0)
        {
            ?>
            <div style="text-align: center;color:red;font-weight:bold">Data not found. Please check budget and production.</div>
            <?
            die;
        } 
        $po_arr=array(); 
        $po_arr_sub=array(); 
        $production_arr=array(); 
        $production_arr_sub=array(); 
        $production_summary_arr=array(); 
        $production_summary_arr_sub=array(); 
        $production_summary_tot_arr=array(); 
        $order_qty_arr=array(); 
        $tot_rows=0; 
        $poId_Arr=array();
        $job_no_arr=array();
        foreach($sql_result as $row)
        {
            $tot_rows++;
            $poId_Arr[$row["PO_ID"]] = $row["PO_ID"];
            if($row["PRODUCTION_SOURCE"]==1) // inhouse
            {
                $po_arr[$row["JOB_NO"]]=$row["BUYER_NAME"].'___'.$row["STYLE_REF_NO"].'___'.$row["GAUGE"].'___'.$row["JOB_NO_PREFIX_NUM"].'___'.$row["JOB_NO"].'___'.$row["PO_NUMBER"].'___'.$row["PO_QUANTITY"].'___'.$row["PLAN_CUT"].'___'.$row["EXCESS_CUT"].'___'.$row["SHIPING_STATUS"].'___'.$row["PUB_SHIPMENT_DATE"].'___'.$row["PRODUCTION_DATE"].'___'.$row["COMPANY_NAME"].'___'.$row["SERVING_COMPANY"].'___'.$row["SEASON"].'___'.$row['AVG_UNIT_PRICE'];
                
                $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['g'] += $row["PRODUCTION_QUANTITY"];
               

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
                {
                     $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['r'] += $row["RE_PRODUCTION_QTY"];
                }

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
                {
                    $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['today'] += $row["PRODUCTION_QUANTITY"];
                    $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['today_re'] += $row["RE_PRODUCTION_QTY"];
                }
                $prev_day = "";
                $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($prev_day))
                {
                    $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['yesterday'] += $row["PRODUCTION_QUANTITY"];
                    $production_arr[$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['yesterday_re'] += $row["RE_PRODUCTION_QTY"];
                }

                $production_summary_arr[$row["PRODUCTION_TYPE"]]['g'] += $row["PRODUCTION_QUANTITY"];
                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
                {
                     $production_summary_arr[$row["PRODUCTION_TYPE"]]['r'] += $row["RE_PRODUCTION_QTY"];
                }
               
                $order_qty_arr[$row["JOB_NO"]]['in_order_qty'] += $row["PO_QUANTITY"];
                $order_qty_arr[$row["JOB_NO"]]['in_excess_cut'] += $row["EXCESS_CUT"];
                $order_qty_arr[$row["JOB_NO"]]['in_plan_cut'] += $row["PLAN_CUT"];
            }
            else
            {
                $po_arr_sub[$row["SERVING_COMPANY"]][$row["JOB_NO"]]=$row["BUYER_NAME"].'___'.$row["STYLE_REF_NO"].'___'.$row["GAUGE"].'___'.$row["JOB_NO_PREFIX_NUM"].'___'.$row["JOB_NO"].'___'.$row["PO_NUMBER"].'___'.$row["PO_QUANTITY"].'___'.$row["PLAN_CUT"].'___'.$row["EXCESS_CUT"].'___'.$row["SHIPING_STATUS"].'___'.$row["PUB_SHIPMENT_DATE"].'___'.$row["PRODUCTION_DATE"].'___'.$row["COMPANY_NAME"].'___'.$row["SERVING_COMPANY"].'___'.$row["SEASON"].'___'.$row['AVG_UNIT_PRICE'];
                
                $production_arr_sub[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['g'] += $row["PRODUCTION_QUANTITY"];

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
                {
                      $production_arr_sub[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['r'] += $row["RE_PRODUCTION_QTY"];
                }

               

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
                {
                    $production_arr[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['today'] += $row["PRODUCTION_QUANTITY"];
                    $production_arr[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['today_re'] += $row["RE_PRODUCTION_QTY"];
                }
                $prev_day = "";
                $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($prev_day))
                {
                    $production_arr[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['yesterday'] += $row["PRODUCTION_QUANTITY"];
                    $production_arr[$row["SERVING_COMPANY"]][$row["JOB_NO"]][$row["PRODUCTION_TYPE"]]['yesterday_re'] += $row["RE_PRODUCTION_QTY"];
                }


                $production_summary_arr_sub[$row["PRODUCTION_TYPE"]]['g'] += $row["PRODUCTION_QUANTITY"];

                if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
                {
                    $production_summary_arr_sub[$row["PRODUCTION_TYPE"]]['r'] += $row["PRODUCTION_QUANTITY"];
                }

               
                $order_qty_arr[$row["JOB_NO"]]['out_order_qty'] += $row["PO_QUANTITY"];
                $order_qty_arr[$row["JOB_NO"]]['out_excess_cut'] += $row["EXCESS_CUT"];
                $order_qty_arr[$row["JOB_NO"]]['out_plan_cut'] += $row["PLAN_CUT"];
            }
            $production_summary_tot_arr[$row["PRODUCTION_TYPE"]]['g'] += $row["PRODUCTION_QUANTITY"];
            
             if(strtotime($row["PRODUCTION_DATE"]) == strtotime($txt_date))
            {
                $production_summary_tot_arr[$row["PRODUCTION_TYPE"]]['r'] += $row["RE_PRODUCTION_QTY"];
            }

            array_push($job_no_arr, $row["JOB_NO"]);
        }
        unset($sql_result);

        $po_id_list_arr=array_chunk($poId_Arr,999);
        $poIds_cond = " and ";
        $p=1;
        foreach($po_id_list_arr as $poids)
        {
            if($p==1) 
            {
                $poIds_cond .="  ( po_break_down_id in(".implode(',',$poids).")"; 
            }
            else
            {
              $poIds_cond .=" or po_break_down_id in(".implode(',',$poids).")";
            }
            $p++;
        }
        $poIds_cond .=")";
        unset($poId_Arr);

        /* =============================================================================================/
        /                                        Order Quantity                                        /
        / ============================================================================================ */
        $po_id = str_replace("po_break_down_id", "id", $poIds_cond);
        $sql_order = "SELECT JOB_NO_MST, PO_QUANTITY,PLAN_CUT,EXCESS_CUT from wo_po_break_down where status_active=1";// $po_id
        $order_res = sql_select($sql_order);
        $job_data_array = array();
        foreach ($order_res as $val) 
        {
            $job_data_array[$val['JOB_NO_MST']]['po_quantity'] += $val['PO_QUANTITY'];
            $job_data_array[$val['JOB_NO_MST']]['plan_cut'] += $val['PLAN_CUT'];
            $job_data_array[$val['JOB_NO_MST']]['excess_cut'] += $val['EXCESS_CUT'];
        }

        /* =============================================================================================/
        /                                        Inspection Data                                        /
        / ============================================================================================ */
        $inspection_arr=array();
        $inspection_arr_sub=array();
        $inspection_summary=array();
        $inspection_total = 0;
        $sql_ins="SELECT JOB_NO,PO_BREAK_DOWN_ID,SOURCE,INSPECTION_DATE, INSPECTION_QNTY from pro_buyer_inspection where status_active=1 and is_deleted=0 $poIds_cond";
        $sql_ins_result=sql_select($sql_ins);
        foreach($sql_ins_result as $row)
        {
            if($row["SOURCE"]==1)
            {
                $inspection_arr[$row["JOB_NO"]]['ins'] += $row["INSPECTION_QNTY"];
                $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));

                if(strtotime($row["INSPECTION_DATE"])==strtotime($txt_date))
                {
                    $inspection_arr[$row["JOB_NO"]]['today'] += $row["INSPECTION_QNTY"];
                }
                if(strtotime($row["INSPECTION_DATE"])==strtotime($prev_day))
                {
                    $inspection_arr[$row["JOB_NO"]]['yesterday'] += $row["INSPECTION_QNTY"];
                }
            }
            else
            {
                $inspection_arr_sub[$row["JOB_NO"]]['ins'] += $row["INSPECTION_QNTY"];
                if(strtotime($row["INSPECTION_DATE"])==strtotime($txt_date))
                {
                    $inspection_arr[$row["JOB_NO"]]['today'] += $row["INSPECTION_QNTY"];
                }
                if(strtotime($row["INSPECTION_DATE"])==strtotime($prev_day))
                {
                    $inspection_arr[$row["JOB_NO"]]['yesterday'] += $row["INSPECTION_QNTY"];
                }
            }
            $inspection_summary[$row["SOURCE"]] += $row["INSPECTION_QNTY"];
            $inspection_total += $row["INSPECTION_QNTY"];
        }
        unset($sql_ins_result);


        $job_no_arr=array_unique($job_no_arr);
        if(count($job_no_arr))
        {
            $job_no_cond=where_con_using_array($job_no_arr,1,"job_no_mst");
        }
        $shp_sql="SELECT job_no_mst,min(shipment_date) shipment_date  from wo_po_break_down where status_active=1 $job_no_cond group by job_no_mst ";
        //echo $shp_sql;
        $shp_res=sql_select($shp_sql);

        $shipment_date_arr=array();

        foreach ($shp_res as $row) 
        {
            $shipment_date_arr[$row[csf('job_no_mst')]]=$row[csf('shipment_date')];
        }
        ?>
        <fieldset>
            <style type="text/css">
                table tr th{word-wrap: break-word;word-break:break-all;}
            </style>
            <!-- ============================================ heading part =========================== -->
            <div>
                <center>
                    <table width="1400" cellspacing="0" >
                        <tr class="form_caption" style="border:none;">
                            <td colspan="14"  align="center" style="border:none;font-size:16px; font-weight:bold" ><?  echo $report_title; ?></td>
                        </tr>
                        <tr style="border:none;">
                            <td colspan="14" align="center" style="border:none; font-size:14px;">
                                Company Name : <? echo $company_library[$cbo_company_name]; ?>                                
                            </td>
                        </tr>
                        <tr style="border:none;">
                            <td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
                                <?
                                    if(str_replace("'","",trim($txt_date))!="")
                                    {
                                        echo "Date : $txt_date";
                                    }
                                ?>
                            </td>
                        </tr>
                    </table>
                </center>
            </div>
          
            <!-- =========================================== inhouse data ====================================== -->
            <div>
                <table width="1910" cellspacing="0" border="1" class="rpt_table" rules="all" id="">
                    <caption style="font-size:18px;font-weight:bold;justify-content: left;text-align: left;">In-House(Today)</caption>
                    <thead>
                        <tr>
                            <th rowspan="2" width="30">Sl.</th> 
                            <th width="680" colspan="9">Order Details</th>

                            <th width="280" colspan="4">Knitting Production</th>
                            <th width="280" colspan="4">Linking Production</th>                    
                            <th width="210" colspan="4">Sewing Prodution</th>
                            <th width="210" colspan="4">Packing and Finishing</th>
                        </tr>
                        <tr>
                               <!--  Order Details -->
                            <th width="100">Buyer</th>
                            <th width="100">Job No</th>
                            <th width="100">Style</th>
                            <th width="80">GG</th>
                             <th width="80">Delivery date</th>
                            <th width="80">Order Qty</th>
                            <th width="80">Avg. Rate</th>
                            <th width="80">Plan Cut Qty</th>
                            <th width="80">Pre Cost CM</th>
                            
                           <!--  Knitting -->
                            <th width="70">Today</th>
                            <th width="70">Prod. Fob</th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>

                            <!--  Linking Production -->
                            <th width="70">Today</th>
                            <th width="70">Prod. Fob</th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>

                            <!-- Sewing -->
                            <th width="70">Today</th>
                            <th width="70">Prod. Fob</th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                            
                           <!-- Packing -->
                            <th width="70">Today</th>
                            <th width="70">Prod. Fob</th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                        </tr>
                    </thead>
                </table>
                <div style="max-height:380px; overflow-y:scroll; width:1930px" id="scroll_body">
                    <table cellspacing="0" border="1" class="rpt_table" width="1910" id="table_body3" rules="all">
                    <? 
                        $i=1;
                        $tot_packing_bal=$tot_packing_comm=$tot_packing_comm=$tot_re_iron=$tot_iron_com=$tot_iron_com=$tot_iron_bal=$tot_knitting_today=$tot_knitting_com=$tot_kintting_bal=$tot_plan_cut=$tot_po_qty=$tot_knitting_prod_fob=$tot_linking_prod_fob=$tot_sewing_prod_fob=$tot_packing__prod_fob=0;

                        $tot_linking_today=$tot_linking_comp=$tot_linking_bal=$tot_triming_today=$tot_triming_comp=$tot_triming_bal=$tot_wash_bal=$tot_sewing_today=$tot_sewing_comm=0;
                        $tot_mending_today=$tot_mending_comp=$tot_mending_bal=$tot_wash_today=$tot_wash_comp=$tot_sewing_bal=$tot_pqc_today=$tot_pqc_comp=$tot_pqc_bal=$tot_iron_today=$tot_packing_today=0;

                        foreach($po_arr as $job_no=>$value)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $buyer_name=$style_ref_no=$gauge=$job_no_prefix_num=$job_no=$po_number=$shiping_status=""; $pubshipment_date="";
                            $po_qty=$plan_cut=$excess_cut=0;
                            $exdata=explode("___",$value);
                            $buyer_name=$exdata[0];
                            // $buyer_name=$exdata[0];
                            $style_ref_no=$exdata[1];
                            $gauge=$exdata[2];
                            $job_no_prefix_num=$exdata[3];
                            $job_no=$exdata[4];
                            $po_number=$exdata[5];

                            $po_qty=$job_data_array[$job_no]['po_quantity'];
                            $plan_cut=$job_data_array[$job_no]['plan_cut'];
                            $excess_cut=$job_data_array[$job_no]['excess_cut'];
                            
                            $shiping_status=$exdata[9];
                            $pubshipment_date=$exdata[10];
                            $production_date=$exdata[11];
                            $company_name=$exdata[12];
                            $wo_company_name=$exdata[13];
                            $season=$exdata[14];
                            $avg_unit_price=$exdata[15];
                            // $cm_cost=$exdata[16];

                            $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));
                            
                            $knitting_com=$inspection_qnty=$wash_comp=$attach_comm=$sewing_comm=$iron_com=$re_iron=$packing_comm=$shipment_com=$linking_comp=$mending_comm=$triming_comm=$pqc_com=0;
                            $knitting_com=$production_arr[$job_no][1]['g'];
                            $inspection_qnty=$inspection_arr[$job_no]['ins'];
                            $linking_comp=$production_arr[$job_no][4]['g'];
                            $wash_comp=$production_arr[$job_no][3]['g'];
                            $attach_comm=$production_arr[$job_no][11]['g'];
                            $sewing_comm=$production_arr[$job_no][5]['g'];
                            $iron_com=$production_arr[$job_no][7]['g']+$production_arr[$job_no][67]['g'];
                            $re_iron=$production_arr[$job_no][7]['r']+$production_arr[$job_no][67]['r'];
                            $packing_comm=$production_arr[$job_no][8]['g'];
                            $iron_n_comm=$production_arr[$job_no][67]['g'];
                            $re_iron_n_comm=$production_arr[$job_no][67]['r'];
                            $triming_comm=$production_arr[$job_no][111]['g'];
                            $mending_comm=$production_arr[$job_no][112]['g'];
                            $pqc_com=$production_arr[$job_no][114]['g'];
                            // ================================ balance ============================
                            $kintting_bal=$inspe_bal=$makeup_bal=$wash_bal=$attach_bal=$sewing_bal=$iron_bal=$packing_bal=$shipment_acc_bal=$linking_bal=$mending_bal=$triming_bal=$pqc_bal=0;
                            $kintting_bal=$plan_cut-$knitting_com;
                            $inspe_bal=$plan_cut-$inspection_qnty;
                            $linking_bal=$plan_cut-$linking_comp;
                            $triming_bal=$plan_cut-$triming_comm;
                            $mending_bal=$plan_cut-$mending_comm;
                            $pqc_bal=$plan_cut-$pqc_com;
                            $wash_bal=$plan_cut-$wash_comp;
                            $attach_bal=$plan_cut-$attach_comm;
                            $sewing_bal=$plan_cut-$sewing_comm;
                            $iron_bal=$plan_cut-$iron_com;
                            $packing_bal=$plan_cut-$packing_comm;
                            $shipment_acc_bal=$po_qty-$shipment_com;
                            // ================================== wip ===============================
                            $knitting_wip=$ins_wip=$makeup_wip=$wash_wip=$attach_wip=$sewing_wip=$packing_percent=$linking_wip=$mending_wip=$triming_wip=$pqc_wip=0;
                            
                            $ins_wip=$knitting_com-$inspection_qnty;
                            $linking_wip=$inspection_qnty-$linking_comp;
                            $triming_wip=$linking_comp-$triming_comm;
                            $mending_wip=$triming_comm-$mending_comm;
                            $mending_wip=$triming_comm-$mending_comm;
                            $wash_wip=$mending_comm-$wash_comp;
                            $attach_wip=$wash_comp-$attach_comm;
                            $sewing_wip=$attach_comm-$sewing_comm;
                            $pqc_wip=$sewing_comm-$pqc_com;
                            $iron_wip=$pqc_com-$iron_com;
                            $packing_percent=($packing_comm/$po_qty)*100;
                            // ================================== today prod =======================================
                            $knitting_com_today=$inspection_qnty_today=$linking_comp_today=$wash_comp_today=$attach_comm_today=$sewing_comm_today=$iron_com_today=$re_iron_today=$packing_comm_today=$shipment_com_today=$pqc_com_today=0;
                            // if(strtotime($production_date) == strtotime($txt_date))
                            // {
                                $knitting_com_today     = $production_arr[$job_no][1]['today'];
                                $inspection_qnty_today  = $inspection_arr[$job_no]['today'];
                                $linking_comp_today      = $production_arr[$job_no][4]['today'];
                                $wash_comp_today        = $production_arr[$job_no][3]['today'];
                                $attach_comm_today      = $production_arr[$job_no][11]['today'];
                                $sewing_comm_today      = $production_arr[$job_no][5]['today'];
                                $iron_com_today         = $production_arr[$job_no][7]['today']+$production_arr[$job_no][67]['today'];
                                $re_iron_today          = $production_arr[$job_no][7]['today_re']+$production_arr[$job_no][67]['today_re'];
                                $packing_comm_today     = $production_arr[$job_no][8]['today'];
                                $iron_n_comm_today      = $production_arr[$job_no][67]['today'];
                                $re_iron_n_comm_today   = $production_arr[$job_no][67]['today_re'];
                                $triming_comm_today     = $production_arr[$job_no][111]['today'];
                                $mending_comm_today     = $production_arr[$job_no][112]['today'];
                                $pqc_com_today         = $production_arr[$job_no][114]['today'];
                            // }
                            // ===================================== prev day prod ======================================
                            $knitting_com_yestarday=$inspection_qnty_yestarday=$linking_comp_yestarday=$wash_comp_yestarday=$attach_comm_yestarday=$sewing_comm_yestarday=$iron_com_yestarday=$re_iron_yestarday=$packing_comm_yestarday=$shipment_com_yestarday=$pqc_com_yestarday=0;
                            // if(strtotime($production_date) == strtotime($prev_day))
                            // {
                                $knitting_com_yestarday     = $production_arr[$job_no][1]['yesterday'];
                                $inspection_qnty_yestarday  = $inspection_arr[$job_no]['yesterday'];
                                $linking_comp_yestarday      = $production_arr[$job_no][4]['yesterday'];
                                $wash_comp_yestarday        = $production_arr[$job_no][3]['yesterday'];
                                $attach_comm_yestarday      = $production_arr[$job_no][11]['yesterday'];
                                $sewing_comm_yestarday      = $production_arr[$job_no][5]['yesterday'];
                                $iron_com_yestarday         = $production_arr[$job_no][7]['yesterday']+$production_arr[$job_no][67]['yesterday'];
                                $re_iron_yestarday          = $production_arr[$job_no][7]['yesterday_re']+$production_arr[$job_no][67]['yesterday_re'];
                                $packing_comm_yestarday     = $production_arr[$job_no][8]['yesterday'];
                                $iron_n_comm_yestarday      = $production_arr[$job_no][67]['yesterday'];
                                $re_iron_n_comm_yestarday   = $production_arr[$job_no][67]['yesterday_re'];
                                $triming_comm_yestarday     = $production_arr[$job_no][111]['yesterday'];
                                $mending_comm_yestarday     = $production_arr[$job_no][112]['yesterday'];
                                $pqc_com_yestarday          = $production_arr[$job_no][114]['yesterday'];
                            // }
                            
                            $packing_status="Running"; $shpment_date='';
                            $ship_status_color="";
                            
                            if($packing_comm>=$plan_cut)
                            {
                                $packing_status="Complete";

                                 $ship_status_color="#FFA500";
                            }  
                            else if($packing_comm<$plan_cut){
                                $packing_status="Running";
                                // $ship_status_color="#FFC0CB";
                            }
                            $bgcl="#FFFF00"; 
                            if($knitting_com_today >0 || $inspection_qnty_today>0 || $linking_comp_today >0 || $triming_comm_today>0 || $mending_comm_today>0 || $wash_comp_today>0 || $attach_comm_today>0 || $sewing_comm_today>0 ||           $pqc_com_today>0 || $iron_com_today>0 || $packing_comm_today>0){           
                            ?>
                             <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>  
                                <td width="100" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?></td>
                                <td width="100" style="word-break:break-all"><? echo $job_no; ?></td>
                                <td width="100" style="word-break:break-all"><? echo $style_ref_no; ?></td>
                                <td width="80" style="word-break:break-all"><? echo $gauge_arr[$gauge]; ?></td>
                                <td width="80" style="word-break:break-all"><? echo '&nbsp;'.change_date_format($shipment_date_arr[$job_no]); ?></td>
                                <td width="80" align="right"><? echo $po_qty; ?></td>
                                <td width="80" align="right"><? echo $avg_unit_price; ?></td>
                                <td width="80" align="right"><? echo $plan_cut; ?></td>
                                <td width="80" align="right"><? echo $cm_cost_library[$job_no]; ?></td>
                                <?php if(!empty($knitting_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                <!--  knitting -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>"><? echo $knitting_com_today; ?></td>
                                <td width="70" align="right"><? 
                                $prod_fob=$knitting_com_today*$avg_unit_price;
                                echo $prod_fob;?></td>
                                <td width="70" align="right"><? echo $knitting_com; ?></td>
                                <td width="70" align="right"><? echo $kintting_bal; ?></td>

                                <?php if(!empty($linking_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                    <!-- linking -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $linking_comp_today; ?></td>
                                <td width="70" align="right"><? 
                                $linking_prod_fob=$linking_comp_today*$avg_unit_price;
                                echo $linking_prod_fob; ?></td>
                                <td width="70" align="right"><? echo $linking_comp; ?></td>
                                <td width="70" align="right"><? echo $linking_bal; ?></td>
                                
                      
                                 <?php if(!empty($sewing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                 <!-- sewing -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $sewing_comm_today; ?></td>

                                <td width="70" align="right" >
                                    <? 
                                    $sewing_prod_fob=$sewing_comm_today*$avg_unit_price;
                                    echo $sewing_prod_fob; ?>
                                </td>

                                <td width="70" align="right"><? echo $sewing_comm; ?></td>
                                <td width="70" align="right"><? echo $sewing_bal; ?></td>
                                
                              
                                 <?php if(!empty($packing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                 <!-- packing -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $packing_comm_today; ?></td>

                                <td width="70" align="right">
                                    <? 
                                    $packing_prod_fob=$packing_comm_today*$avg_unit_price;
                                    echo $packing_prod_fob; ?>
                                </td>

                                <td width="70" align="right"><? echo $packing_comm; ?></td>
                                <td width="70" align="right"><? echo $packing_bal; ?></td>
                               
                            </tr>
                            <?

                            $i++;
                            
                            $tot_plan_cut += $plan_cut;
                            $tot_po_qty += $po_qty;
                            $tot_knitting_prod_fob += $prod_fob;
                            $tot_linking_prod_fob += $linking_prod_fob;
                            $tot_sewing_prod_fob += $sewing_prod_fob;
                            $tot_packing__prod_fob += $packing_prod_fob;

                            $tot_knitting_yesterday += $knitting_com_yestarday;
                            $tot_knitting_today += $knitting_com_today;
                            $tot_knitting_com+=$knitting_com;
                            $tot_kintting_bal+=$kintting_bal;
                            $tot_knitting_wip+=$knitting_wip;
                            
                            $tot_inspection_yesterday+=$inspection_qnty_yestarday;
                            $tot_inspection_today+=$inspection_qnty_today;
                            $tot_inspection_qnty+=$inspection_qnty;
                            $tot_inspe_bal+=$inspe_bal;
                            $tot_ins_wip+=$ins_wip;

                            $tot_linking_yesterday+=$linking_comp_yestarday;
                            $tot_linking_today+=$linking_comp_today;
                            $tot_linking_comp+=$linking_comp;
                            $tot_linking_bal+=$linking_bal;
                            $tot_linking_wip+=$linking_wip;

                            $tot_trimingyesterday+=$trimingcomp_yestarday;
                            $tot_triming_today+=$triming_comm_today;
                            $tot_triming_comp+=$triming_comm;
                            $tot_triming_bal+=$triming_bal;
                            $tot_trimingwip+=$trimingwip;

                            $tot_mending_yesterday+=$mending_comp_yestarday;
                            $tot_mending_today+=$mending_comm_today;
                            $tot_mending_comp+=$mending_comm;
                            $tot_mending_bal+=$mending_bal;
                            $tot_mending_wip+=$mending_wip;
                            
                            $tot_wash_yesterday+=$wash_comp_yestarday;
                            $tot_wash_today+=$wash_comp_today;
                            $tot_wash_comp+=$wash_comp;
                            $tot_wash_bal+=$wash_bal;
                            $tot_wash_wip+=$wash_wip;
                            
                            $tot_attach_yesterday+=$attach_comm_yestarday;
                            $tot_attach_today+=$attach_comm_today;
                            $tot_attach_comm+=$attach_comm;
                            $tot_attach_bal+=$attach_bal;
                            $tot_attach_wip+=$attach_wip;
                            
                            $tot_sewing_yesterday+=$sewing_comm_yestarday;
                            $tot_sewing_today+=$sewing_comm_today;
                            $tot_sewing_comm+=$sewing_comm;
                            $tot_sewing_bal+=$sewing_bal;
                            $tot_sewing_wip+=$sewing_wip;
                            
                            $tot_pqc_yesterday+=$pqc_com_yestarday;
                            $tot_pqc_today+=$pqc_com_today;
                            $tot_pqc_comp+=$pqc_com;
                            $tot_pqc_bal+=$pqc_bal;
                            $tot_pqc_wip+=$pqc_wip;
                            
                            $tot_iron_wip+=$iron_wip;
                            $tot_iron_yesterday+=$iron_com_yestarday;
                            $tot_iron_today+=$iron_com_today;
                            $tot_iron_com+=$iron_com;
                            $tot_iron_bal+=$iron_bal;
                            $tot_re_iron+=$re_iron;
                            
                            $tot_packing_comm+=$packing_comm;
                            $tot_packing_bal+=$packing_bal;
                            $tot_shipment_com+=$shipment_com;
                            $tot_shipment_acc_bal+=$shipment_acc_bal;
                            $tot_packing_today+=$packing_comm_today;
                            }
                        }
                        ?>
                    </table>
                </div>
                <table width="1910" cellspacing="0" border="1" class="tbl_bottom" rules="all">
                    <tr>
                        <td width="30">&nbsp;</td> 
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                      
                        <td width="80">&nbsp;</td>
                        <td width="80">Total:</td>
                        <td width="80"><?=$tot_po_qty?></td>
                        <td width="80">&nbsp;</td>
                        <td width="80"><?=$tot_plan_cut?></td> 
                        <td width="80">&nbsp;</td>  
                         <?php if(!empty($tot_knitting_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_knitting_today; ?></td>

                        <td width="70" id=""><? echo $tot_knitting_prod_fob; ?></td>
                        <td width="70" id=""><? echo $tot_knitting_com; ?></td>
                        <td width="70" id=""><? echo $tot_kintting_bal; ?></td>

                         <?php if(!empty($tot_linking_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_linking_today; ?></td>
                        <td width="70" id="" ><? echo $$tot_linking_prod_fob; ?></td>
                        <td width="70" id=""><? echo $tot_linking_comp; ?></td>
                        <td width="70" id=""><? echo $tot_linking_bal; ?></td>

                          <?php if(!empty($tot_sewing_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_sewing_today; ?></td>
                        <td width="70" id="" ><? echo $tot_sewing_prod_fob; ?></td>
                        <td width="70" id=""><? echo $tot_sewing_comm; ?></td>
                        <td width="70" id=""><? echo $tot_sewing_bal; ?></td>

                        <td width="70" id=""><? echo $tot_packing_today; ?></td>
                        <td width="70" id=""><? echo $tot_packing__prod_fob; ?></td>
                        <td width="70" id=""><? echo $tot_packing_comm; ?></td>
                        <td width="70" id=""><? echo $tot_packing_bal; ?></td>
                       
                    </tr>
                </table>
            </div>
            <!-- =========================================== Pending data ====================================== -->
            <div>
                <table width="1910" cellspacing="0" border="1" class="rpt_table" rules="all" id="">
                    <caption style="font-size:18px;font-weight:bold;justify-content: left;text-align: left;">Pending(Today)</caption>
                    <thead>
                        <tr>
                            <th rowspan="2" width="30">Sl.</th> 
                            <th width="680" colspan="9">Order Details</th>
                            <th width="280" colspan="4">Knitting Production</th>
                            <th width="280" colspan="4">Linking Production</th>                    
                            <th width="210" colspan="4">Sewing Prodution</th>
                            <th width="210" colspan="4">Packing and Finishing</th>
                        </tr>
                        <tr>
                            <!--  Order Details -->
                            <th width="100">Buyer</th>
                            <th width="100">Job No</th>
                            <th width="100">Style</th>
                            <th width="80">GG</th>
                             <th width="80">Delivery date</th>
                            <th width="80">Order Qty</th>
                            <th width="80">Avg. Rate</th>
                            <th width="80">Plan Cut Qty</th>
                            <th width="80">Pre Cost CM</th>
                            <!--  Knitting -->
                            <th width="70">Today</th>
                            <th width="70">Prod. Fob</th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            <!--  Linking Production -->
                            <th width="70">Today</th>
                            <th width="70">Prod. Fob</th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                            <!-- Sewing -->
                            <th width="70">Today</th>
                            <th width="70">Prod. Fob</th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                           <!-- Packing -->
                            <th width="70">Today</th>
                            <th width="70">Prod. Fob</th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                        </tr>
                    </thead>
                </table>
                <div style="max-height:380px; overflow-y:scroll; width:1930px" id="scroll_body">
                    <table cellspacing="0" border="1" class="rpt_table" width="1910" id="table_body3" rules="all">
                    <? 
                        $i=1;
                        $tot_packing_bal=$tot_packing_comm=$tot_packing_comm=$tot_re_iron=$tot_iron_com=$tot_iron_com=$tot_iron_bal=$tot_knitting_today=$tot_knitting_com=$tot_kintting_bal= $tot_po_qty=$tot_plan_cut=$tot_knitting_prod_fob=$tot_linking_prod_fob=$tot_sewing_prod_fob=$tot_packing__prod_fob=0;

                        $tot_linking_today=$tot_linking_comp=$tot_linking_bal=$tot_triming_today=$tot_triming_comp=$tot_triming_bal=$tot_wash_bal=$tot_sewing_today=$tot_sewing_comm=0;

                        $tot_mending_today=$tot_mending_comp=$tot_mending_bal=$tot_wash_today=$tot_wash_comp=$tot_sewing_bal=$tot_pqc_today=$tot_pqc_comp=$tot_pqc_bal=$tot_iron_today=$tot_packing_today=0;

                        foreach($po_arr as $job_no=>$value)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $buyer_name=$style_ref_no=$gauge=$job_no_prefix_num=$job_no=$po_number=$shiping_status=""; $pubshipment_date="";
                            $po_qty=$plan_cut=$excess_cut=0;
                            $exdata=explode("___",$value);
                            $buyer_name=$exdata[0];
                            $buyer_name=$exdata[0];
                            $style_ref_no=$exdata[1];
                            $gauge=$exdata[2];
                            $job_no_prefix_num=$exdata[3];
                            $job_no=$exdata[4];
                            $po_number=$exdata[5];
                            
                            $po_qty=$job_data_array[$job_no]['po_quantity'];
                            $plan_cut=$job_data_array[$job_no]['plan_cut'];
                            $excess_cut=$job_data_array[$job_no]['excess_cut'];
                            
                            $shiping_status=$exdata[9];
                            $pubshipment_date=$exdata[10];
                            $production_date=$exdata[11];
                            $company_name=$exdata[12];
                            $wo_company_name=$exdata[13];
                            $season=$exdata[14];
                            $avg_unit_price=$exdata[15];
                            // $cm_cost=$exdata[16];

                            $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));
                            
                            $knitting_com=$inspection_qnty=$wash_comp=$attach_comm=$sewing_comm=$iron_com=$re_iron=$packing_comm=$shipment_com=$linking_comp=$mending_comm=$triming_comm=$pqc_com=0;
                            $knitting_com=$production_arr[$job_no][1]['g'];
                            $inspection_qnty=$inspection_arr[$job_no]['ins'];
                            $linking_comp=$production_arr[$job_no][4]['g'];
                            $wash_comp=$production_arr[$job_no][3]['g'];
                            $attach_comm=$production_arr[$job_no][11]['g'];
                            $sewing_comm=$production_arr[$job_no][5]['g'];
                            $iron_com=$production_arr[$job_no][7]['g']+$production_arr[$job_no][67]['g'];
                            $re_iron=$production_arr[$job_no][7]['r']+$production_arr[$job_no][67]['r'];
                            $packing_comm=$production_arr[$job_no][8]['g'];
                            $iron_n_comm=$production_arr[$job_no][67]['g'];
                            $re_iron_n_comm=$production_arr[$job_no][67]['r'];
                            $triming_comm=$production_arr[$job_no][111]['g'];
                            $mending_comm=$production_arr[$job_no][112]['g'];
                            $pqc_com=$production_arr[$job_no][114]['g'];
                            // ================================ balance ============================
                            $kintting_bal=$inspe_bal=$makeup_bal=$wash_bal=$attach_bal=$sewing_bal=$iron_bal=$packing_bal=$shipment_acc_bal=$linking_bal=$mending_bal=$triming_bal=$pqc_bal=0;
                            $kintting_bal=$plan_cut-$knitting_com;
                            $inspe_bal=$plan_cut-$inspection_qnty;
                            $linking_bal=$plan_cut-$linking_comp;
                            $triming_bal=$plan_cut-$triming_comm;
                            $mending_bal=$plan_cut-$mending_comm;
                            $pqc_bal=$plan_cut-$pqc_com;
                            $wash_bal=$plan_cut-$wash_comp;
                            $attach_bal=$plan_cut-$attach_comm;
                            $sewing_bal=$plan_cut-$sewing_comm;
                            $iron_bal=$plan_cut-$iron_com;
                            $packing_bal=$plan_cut-$packing_comm;
                            $shipment_acc_bal=$po_qty-$shipment_com;
                            // ================================== wip ===============================
                            $knitting_wip=$ins_wip=$makeup_wip=$wash_wip=$attach_wip=$sewing_wip=$packing_percent=$linking_wip=$mending_wip=$triming_wip=$pqc_wip=0;
                            
                            $ins_wip=$knitting_com-$inspection_qnty;
                            $linking_wip=$inspection_qnty-$linking_comp;
                            $triming_wip=$linking_comp-$triming_comm;
                            $mending_wip=$triming_comm-$mending_comm;
                            $mending_wip=$triming_comm-$mending_comm;
                            $wash_wip=$mending_comm-$wash_comp;
                            $attach_wip=$wash_comp-$attach_comm;
                            $sewing_wip=$attach_comm-$sewing_comm;
                            $pqc_wip=$sewing_comm-$pqc_com;
                            $iron_wip=$pqc_com-$iron_com;
                            $packing_percent=($packing_comm/$po_qty)*100;
                            // ================================== today prod =======================================
                            $knitting_com_today=$inspection_qnty_today=$linking_comp_today=$wash_comp_today=$attach_comm_today=$sewing_comm_today=$iron_com_today=$re_iron_today=$packing_comm_today=$shipment_com_today=$pqc_com_today=0;
                            // if(strtotime($production_date) == strtotime($txt_date))
                            // {
                                $knitting_com_today     = $production_arr[$job_no][1]['today'];
                                $inspection_qnty_today  = $inspection_arr[$job_no]['today'];
                                $linking_comp_today      = $production_arr[$job_no][4]['today'];
                                $wash_comp_today        = $production_arr[$job_no][3]['today'];
                                $attach_comm_today      = $production_arr[$job_no][11]['today'];
                                $sewing_comm_today      = $production_arr[$job_no][5]['today'];
                                $iron_com_today         = $production_arr[$job_no][7]['today']+$production_arr[$job_no][67]['today'];
                                $re_iron_today          = $production_arr[$job_no][7]['today_re']+$production_arr[$job_no][67]['today_re'];
                                $packing_comm_today     = $production_arr[$job_no][8]['today'];
                                $iron_n_comm_today      = $production_arr[$job_no][67]['today'];
                                $re_iron_n_comm_today   = $production_arr[$job_no][67]['today_re'];
                                $triming_comm_today     = $production_arr[$job_no][111]['today'];
                                $mending_comm_today     = $production_arr[$job_no][112]['today'];
                                $pqc_com_today         = $production_arr[$job_no][114]['today'];
                            // }
                            // ===================================== prev day prod ======================================
                            $knitting_com_yestarday=$inspection_qnty_yestarday=$linking_comp_yestarday=$wash_comp_yestarday=$attach_comm_yestarday=$sewing_comm_yestarday=$iron_com_yestarday=$re_iron_yestarday=$packing_comm_yestarday=$shipment_com_yestarday=$pqc_com_yestarday=0;
                            // if(strtotime($production_date) == strtotime($prev_day))
                            // {
                                $knitting_com_yestarday     = $production_arr[$job_no][1]['yesterday'];
                                $inspection_qnty_yestarday  = $inspection_arr[$job_no]['yesterday'];
                                $linking_comp_yestarday      = $production_arr[$job_no][4]['yesterday'];
                                $wash_comp_yestarday        = $production_arr[$job_no][3]['yesterday'];
                                $attach_comm_yestarday      = $production_arr[$job_no][11]['yesterday'];
                                $sewing_comm_yestarday      = $production_arr[$job_no][5]['yesterday'];
                                $iron_com_yestarday         = $production_arr[$job_no][7]['yesterday']+$production_arr[$job_no][67]['yesterday'];
                                $re_iron_yestarday          = $production_arr[$job_no][7]['yesterday_re']+$production_arr[$job_no][67]['yesterday_re'];
                                $packing_comm_yestarday     = $production_arr[$job_no][8]['yesterday'];
                                $iron_n_comm_yestarday      = $production_arr[$job_no][67]['yesterday'];
                                $re_iron_n_comm_yestarday   = $production_arr[$job_no][67]['yesterday_re'];
                                $triming_comm_yestarday     = $production_arr[$job_no][111]['yesterday'];
                                $mending_comm_yestarday     = $production_arr[$job_no][112]['yesterday'];
                                $pqc_com_yestarday          = $production_arr[$job_no][114]['yesterday'];
                            // }
                            
                            $packing_status="Running"; $shpment_date='';
                            $ship_status_color="";
                            
                            if($packing_comm>=$plan_cut)
                            {
                                $packing_status="Complete";

                                 $ship_status_color="#FFA500";
                            }  
                            else if($packing_comm<$plan_cut){
                                $packing_status="Running";
                                // $ship_status_color="#FFC0CB";
                            }
                            $bgcl="#FFFF00"; 
                            if($knitting_com_today >0 || $inspection_qnty_today>0 || $linking_comp_today >0 || $triming_comm_today>0 || $mending_comm_today>0 || $wash_comp_today>0 || $attach_comm_today>0 || $sewing_comm_today>0 ||           $pqc_com_today>0 || $iron_com_today>0 || $packing_comm_today>0){  
                                
                            }else{
                            ?>
                             <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>   
                               
                                <td width="100" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?></td>
                                <td width="100" style="word-break:break-all"><? echo $job_no; ?></td>
                                <td width="100" style="word-break:break-all"><? echo $style_ref_no; ?></td>
                                <td width="80" style="word-break:break-all"><? echo $gauge_arr[$gauge]; ?></td>
                                <td width="80" style="word-break:break-all"><? echo '&nbsp;'.change_date_format($shipment_date_arr[$job_no]); ?></td>
                                <td width="80" align="right"><? echo $po_qty; ?></td>
                                <td width="80" align="right"><? echo $avg_unit_price; ?></td>
                                <td width="80" align="right"><? echo $plan_cut; ?></td>
                                <td width="80" align="right"><? echo $cm_cost_library[$job_no]; ?></td>

                                <?php if(!empty($knitting_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                <!--  knitting -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>"><? echo $knitting_com_today; ?></td>

                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>">
                                <? 
                                $prod_fob=$knitting_com_today*$avg_unit_price;
                                echo $prod_fob; ?></td>
                                <td width="70" align="right"><? echo $knitting_com; ?></td>
                                <td width="70" align="right"><? echo $kintting_bal; ?></td>

                                <?php if(!empty($linking_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                    <!-- linking -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $linking_comp_today; ?></td>
                                <td width="70" align="right" >
                                    <? 
                                    $linking_prod_fob=$linking_comp_today*$avg_unit_price;
                                    echo $linking_prod_fob; ?>
                                </td>

                                <td width="70" align="right"><? echo $linking_comp; ?></td>
                                <td width="70" align="right"><? echo $linking_bal; ?></td>

                                 <?php if(!empty($sewing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                                 <!-- sewing -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $sewing_comm_today; ?></td>
                                <td width="70" align="right" >
                                <? 
                                    $sewing_prod_fob=$sewing_comm_today*$avg_unit_price;
                                    echo $sewing_prod_fob; 
                                ?>
                                </td>
                                <td width="70" align="right"><? echo $sewing_comm; ?></td>
                                <td width="70" align="right"><? echo $sewing_bal; ?></td>

                                 <?php if(!empty($packing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                 <!-- packing -->
                                <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $packing_comm_today; ?>
                                </td>

                                <td width="70" align="right">
                                    <? 
                                    $packing_prod_fob=$packing_comm_today*$avg_unit_price;
                                    echo $packing_prod_fob; ?>
                                </td>

                                <td width="70" align="right"><? echo $packing_comm; ?></td>
                                <td width="70" align="right"><? echo $packing_bal; ?></td>
                               
                            </tr>
                            <?
                            $i++;
                            $tot_po_qty += $po_qty;
                            $tot_plan_cut += $plan_cut;
                            $tot_knitting_prod_fob += $prod_fob;
                            $tot_linking_prod_fob += $linking_prod_fob;
                            $tot_sewing_prod_fob += $sewing_prod_fob;
                            $tot_packing__prod_fob += $packing_prod_fob;

                            $tot_knitting_yesterday += $knitting_com_yestarday;
                            $tot_knitting_today += $knitting_com_today;
                            $tot_knitting_com+=$knitting_com;
                            $tot_kintting_bal+=$kintting_bal;
                            $tot_knitting_wip+=$knitting_wip;
                            
                            $tot_inspection_yesterday+=$inspection_qnty_yestarday;
                            $tot_inspection_today+=$inspection_qnty_today;
                            $tot_inspection_qnty+=$inspection_qnty;
                            $tot_inspe_bal+=$inspe_bal;
                            $tot_ins_wip+=$ins_wip;

                            $tot_linking_yesterday+=$linking_comp_yestarday;
                            $tot_linking_today+=$linking_comp_today;
                            $tot_linking_comp+=$linking_comp;
                            $tot_linking_bal+=$linking_bal;
                            $tot_linking_wip+=$linking_wip;

                            $tot_trimingyesterday+=$trimingcomp_yestarday;
                            $tot_triming_today+=$triming_comm_today;
                            $tot_triming_comp+=$triming_comm;
                            $tot_triming_bal+=$triming_bal;
                            $tot_trimingwip+=$trimingwip;

                            $tot_mending_yesterday+=$mending_comp_yestarday;
                            $tot_mending_today+=$mending_comm_today;
                            $tot_mending_comp+=$mending_comm;
                            $tot_mending_bal+=$mending_bal;
                            $tot_mending_wip+=$mending_wip;
                            
                            $tot_wash_yesterday+=$wash_comp_yestarday;
                            $tot_wash_today+=$wash_comp_today;
                            $tot_wash_comp+=$wash_comp;
                            $tot_wash_bal+=$wash_bal;
                            $tot_wash_wip+=$wash_wip;
                            
                            $tot_attach_yesterday+=$attach_comm_yestarday;
                            $tot_attach_today+=$attach_comm_today;
                            $tot_attach_comm+=$attach_comm;
                            $tot_attach_bal+=$attach_bal;
                            $tot_attach_wip+=$attach_wip;
                            
                            $tot_sewing_yesterday+=$sewing_comm_yestarday;
                            $tot_sewing_today+=$sewing_comm_today;
                            $tot_sewing_comm+=$sewing_comm;
                            $tot_sewing_bal+=$sewing_bal;
                            $tot_sewing_wip+=$sewing_wip;
                            
                            $tot_pqc_yesterday+=$pqc_com_yestarday;
                            $tot_pqc_today+=$pqc_com_today;
                            $tot_pqc_comp+=$pqc_com;
                            $tot_pqc_bal+=$pqc_bal;
                            $tot_pqc_wip+=$pqc_wip;
                            
                            $tot_iron_wip+=$iron_wip;
                            $tot_iron_yesterday+=$iron_com_yestarday;
                            $tot_iron_today+=$iron_com_today;
                            $tot_iron_com+=$iron_com;
                            $tot_iron_bal+=$iron_bal;
                            $tot_re_iron+=$re_iron;
                            
                            $tot_packing_comm+=$packing_comm;
                            $tot_packing_bal+=$packing_bal;
                            $tot_shipment_com+=$shipment_com;
                            $tot_shipment_acc_bal+=$shipment_acc_bal;
                            $tot_packing_today+=$packing_comm_today;
                            }
                        }
                        ?>
                    </table>
                </div>
                <table width="1910" cellspacing="0" border="1" class="tbl_bottom" rules="all">
                    <tr>
                        <td width="30">&nbsp;</td> 
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">Total:</td>
                        <td width="80"><?=$tot_po_qty?></td>
                        <td width="80">&nbsp;</td>
                        <td width="80"><?=$tot_plan_cut;?></td>  
                        <td width="80">&nbsp;</td>
                         <?php if(!empty($tot_knitting_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_knitting_today; ?></td>
                        <td width="70" id=""><? echo $tot_knitting_prod_fob; ?></td>
                        <td width="70" id=""><? echo $tot_knitting_com; ?></td>
                        <td width="70" id=""><? echo $tot_kintting_bal; ?></td>
                         <?php if(!empty($tot_linking_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_linking_today; ?></td>
                        <td width="70" id=""><? echo $tot_linking_prod_fob; ?></td>
                        <td width="70" id=""><? echo $tot_linking_comp; ?></td>
                        <td width="70" id=""><? echo $tot_linking_bal; ?></td>
                      
                          <?php if(!empty($tot_sewing_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>
                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_sewing_today; ?></td>

                        <td width="70" id=""><? echo $tot_sewing_prod_fob;  ?></td>
                        <td width="70" id=""><? echo $tot_sewing_comm; ?></td>
                        <td width="70" id=""><? echo $tot_sewing_bal; ?></td>
                        
                        <td width="70" id=""><? echo $tot_packing_today; ?></td>
                        <td width="70" id=""><? echo $tot_packing_today; ?></td>
                        <td width="70" id=""><? echo $tot_packing_comm; ?></td>
                        <td width="70" id=""><? echo $tot_packing_bal; ?></td>
                       
                    </tr>
                </table>
            </div>
            <!-- =========================================== subcon data ========================================= -->
            <div>
                <table width="1910" cellspacing="0" border="1" class="rpt_table" rules="all" id="">
                    <caption style="font-size:18px;font-weight:bold;justify-content: left;text-align: left;">Sub-Contract</caption>
                    <thead>
                        <tr>
                             <th rowspan="2" width="30">Sl.</th> 
                            <th width="680" colspan="9">Order Details</th>

                            <th width="280" colspan="4">Knitting Production</th>
                            <th width="280" colspan="4">Linking Production</th>                    
                            <th width="210" colspan="4">Sewing Prodution</th>
                            <th width="210" colspan="4">Packing and Finishing</th>
                        </tr>
                        <tr>
                            <th width="100">Buyer</th>
                            <th width="100">Job No</th>
                            <th width="100">Style</th>
                            <th width="80">GG</th>
                            <th width="80">Delivery date</th>
                            <th width="80">Order Qty</th>
                            <th width="80">Avg. Rate</th>
                            <th width="80">Plan Cut Qty</th>
                            <th width="80">Pre Cost CM</th>
                           <!-- Knitting -->
                            <th width="70">Today </th>
                            <th width="70">Prod. Fob</th>
                            <th width="70">Total </th>
                            <th width="70"> Balance</th>
                            <!--  Linking Production -->
                            <th width="70">Today</th>
                            <th width="70">Prod. Fob</th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                            <!-- Sewing -->
                            <th width="70">Today</th>
                            <th width="70">Prod. Fob</th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                           <!-- Packing -->
                            <th width="70">Today</th>
                            <th width="70">Prod. Fob</th>
                            <th width="70">Total</th>
                            <th width="70"> Balance</th>
                        </tr>
                    </thead>
                </table>
                <div style="max-height:380px; overflow-y:scroll; width:1930px" id="scroll_body">
                    <table cellspacing="0" border="1" class="rpt_table" width="1910" id="table_body" rules="all">
                    <? 
                        $sl = 1;
                        $tot_packing_bal=$tot_packing_comm=$tot_packing_comm=$tot_re_iron=$tot_iron_com=$tot_iron_com=$tot_iron_bal=$tot_knitting_today=$tot_knitting_com=$tot_kintting_bal=$tot_po_qty=$tot_plan_cut=$tot_knitting_prod_fob=$tot_linking_prod_fob=$tot_sewing_prod_fob=$tot_packing__prod_fob=0;

                        $tot_linking_today=$tot_linking_comp=$tot_linking_bal=$tot_triming_today=$tot_triming_comp=$tot_triming_bal=$tot_wash_bal=$tot_sewing_today=$tot_sewing_comm=0;
                        $tot_mending_today=$tot_mending_comp=$tot_mending_bal=$tot_wash_today=$tot_wash_comp=$tot_sewing_bal=$tot_pqc_today=$tot_pqc_comp=$tot_pqc_bal=$tot_packing_today= $tot_iron_today=0;
                        foreach($po_arr_sub as $working_com=>$working_com_data)
                        {
                            foreach($working_com_data as $job_no=>$value)
                            {
                                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                $buyer_name=$style_ref_no=$gauge=$job_no_prefix_num=$job_no=$po_number=$shiping_status=""; $pubshipment_date="";
                                $po_qty=$plan_cut=$excess_cut=0;
                                $exdata=explode("___",$value);
                                $buyer_name=$exdata[0];
                                $buyer_name=$exdata[0];
                                $style_ref_no=$exdata[1];
                                $gauge=$exdata[2];
                                $job_no_prefix_num=$exdata[3];
                                $job_no=$exdata[4];
                                $po_number=$exdata[5];
                            
                                $po_qty=$job_data_array[$job_no]['po_quantity'];
                                $plan_cut=$job_data_array[$job_no]['plan_cut'];
                                $excess_cut=$job_data_array[$job_no]['excess_cut'];
                                
                                $shiping_status=$exdata[9];
                                $pubshipment_date=$exdata[10];
                                $production_date=$exdata[11];
                                $company_name=$exdata[12];
                                $wo_company_name=$exdata[13];
                                $season=$exdata[14];
                                $avg_unit_price=$exdata[15];
                                // $cm_cost=$exdata[16];

                                $prev_day = date('Y-m-d', strtotime('-1 day', strtotime($txt_date)));
                                
                                $knitting_com=$inspection_qnty=$wash_comp=$attach_comm=$sewing_comm=$iron_com=$re_iron=$packing_comm=$shipment_com=$linking_comp=$mending_comm=$triming_comm=$pqc_com=$iron_n_comm=0;
                                $knitting_com=$production_arr_sub[$working_com][$job_no][1]['g'];
                                $inspection_qnty=$inspection_arr_sub[$job_no]['ins'];
                                $linking_comp=$production_arr_sub[$working_com][$job_no][4]['g'];
                                $wash_comp=$production_arr_sub[$working_com][$job_no][3]['g'];
                                $attach_comm=$production_arr_sub[$working_com][$job_no][11]['g'];
                                $sewing_comm=$production_arr_sub[$working_com][$job_no][5]['g'];
                                $iron_com=$production_arr_sub[$working_com][$job_no][7]['g']+$production_arr_sub[$working_com][$job_no][67]['g'];
                                $re_iron=$production_arr_sub[$working_com][$job_no][7]['r']+$production_arr_sub[$working_com][$job_no][67]['r'];
                                $packing_comm=$production_arr_sub[$working_com][$job_no][8]['g'];
                                $iron_n_comm=$production_arr_sub[$working_com][$job_no][67]['g'];
                                $re_iron_n_comm=$production_arr_sub[$working_com][$job_no][67]['r'];
                                $triming_comm=$production_arr_sub[$working_com][$job_no][111]['g'];
                                $mending_comm=$production_arr_sub[$working_com][$job_no][112]['g'];
                                $pqc_com=$production_arr_sub[$working_com][$job_no][114]['g'];
                                // ================================ balance ============================
                                $kintting_bal=$inspe_bal=$makeup_bal=$wash_bal=$attach_bal=$sewing_bal=$iron_bal=$packing_bal=$shipment_acc_bal=$linking_bal=$mending_bal=$triming_bal=$pqc_bal=0;
                                $kintting_bal=$plan_cut-$knitting_com;
                                $inspe_bal=$plan_cut-$inspection_qnty;
                                $linking_bal=$plan_cut-$linking_comp;
                                $triming_bal=$plan_cut-$triming_comm;
                                $mending_bal=$plan_cut-$mending_comm;
                                $pqc_bal=$plan_cut-$pqc_com;
                                $wash_bal=$plan_cut-$wash_comp;
                                $attach_bal=$plan_cut-$attach_comm;
                                $sewing_bal=$plan_cut-$sewing_comm;
                                $iron_bal=$plan_cut-$iron_n_comm;
                                $packing_bal=$plan_cut-$packing_comm;
                                $shipment_acc_bal=$po_qty-$shipment_com;
                                // ================================== wip ===============================
                                $knitting_wip=$ins_wip=$makeup_wip=$wash_wip=$attach_wip=$sewing_wip=$packing_percent=$linking_wip=$mending_wip=$triming_wip=$pqc_wip=0;
                                
                                $ins_wip=$knitting_com-$inspection_qnty;
                                $linking_wip=$inspection_qnty-$linking_comp;
                                $triming_wip=$linking_comp-$triming_comm;
                                $mending_wip=$triming_comm-$mending_comm;
                                $mending_wip=$triming_comm-$mending_comm;
                                $wash_wip=$mending_comm-$wash_comp;
                                $attach_wip=$wash_comp-$attach_comm;
                                $sewing_wip=$attach_comm-$sewing_comm;
                                $pqc_wip=$sewing_comm-$pqc_com;
                                $iron_wip=$pqc_com-$iron_n_comm;
                                $packing_percent=($packing_comm/$po_qty)*100;
                                // ================================== today prod =======================================
                                $knitting_com_today=$inspection_qnty_today=$linking_comp_today=$wash_comp_today=$attach_comm_today=$sewing_comm_today=$iron_com_today=$re_iron_today=$packing_comm_today=$shipment_com_today=$pqc_com_today=0;
                                // if(strtotime($production_date) == strtotime($txt_date))
                                // {
                                    $knitting_com_today     = $production_arr[$working_com][$job_no][1]['today'];
                                    $inspection_qnty_today  = $inspection_arr[$job_no]['today'];
                                    $linking_comp_today      = $production_arr[$working_com][$job_no][4]['today'];
                                    $wash_comp_today        = $production_arr[$working_com][$job_no][3]['today'];
                                    $attach_comm_today      = $production_arr[$working_com][$job_no][11]['today'];
                                    $sewing_comm_today      = $production_arr[$working_com][$job_no][5]['today'];
                                    $iron_com_today         = $production_arr[$working_com][$job_no][7]['today']+$production_arr[$working_com][$job_no][67]['today'];
                                    $re_iron_today          = $production_arr[$working_com][$job_no][7]['today_re']+$production_arr[$working_com][$job_no][67]['today_re'];
                                    $packing_comm_today     = $production_arr[$working_com][$job_no][8]['today'];
                                    $iron_n_comm_today      = $production_arr[$working_com][$job_no][67]['today'];
                                    $re_iron_n_comm_today   = $production_arr[$working_com][$job_no][67]['today_re'];
                                    $triming_comm_today     = $production_arr[$working_com][$job_no][111]['today'];
                                    $mending_comm_today     = $production_arr[$working_com][$job_no][112]['today'];
                                    $pqc_com_today          = $production_arr[$working_com][$job_no][114]['today'];
                                // }
                                // ===================================== prev day prod ======================================
                                $knitting_com_yestarday=$inspection_qnty_yestarday=$linking_comp_yestarday=$wash_comp_yestarday=$attach_comm_yestarday=$sewing_comm_yestarday=$iron_com_yestarday=$re_iron_yestarday=$packing_comm_yestarday=$shipment_com_yestarday=$pqc_com_yestarday=0;
                                // if(strtotime($production_date) == strtotime($prev_day))
                                // {
                                    $knitting_com_yestarday     = $production_arr[$working_com][$job_no][1]['yesterday'];
                                    $inspection_qnty_yestarday  = $inspection_arr[$job_no]['yesterday'];
                                    $linking_comp_yestarday      = $production_arr[$working_com][$job_no][4]['yesterday'];
                                    $wash_comp_yestarday        = $production_arr[$working_com][$job_no][3]['yesterday'];
                                    $attach_comm_yestarday      = $production_arr[$working_com][$job_no][11]['yesterday'];
                                    $sewing_comm_yestarday      = $production_arr[$working_com][$job_no][5]['yesterday'];
                                    $iron_com_yestarday         = $production_arr[$working_com][$job_no][7]['yesterday']+$production_arr[$working_com][$job_no][67]['yesterday'];
                                    $re_iron_yestarday          = $production_arr[$working_com][$job_no][7]['yesterday_re']+$production_arr[$working_com][$job_no][67]['yesterday_re'];
                                    $packing_comm_yestarday     = $production_arr[$working_com][$job_no][8]['yesterday'];
                                    $iron_n_comm_yestarday      = $production_arr[$working_com][$job_no][67]['yesterday'];
                                    $re_iron_n_comm_yestarday   = $production_arr[$working_com][$job_no][67]['yesterday_re'];
                                    $triming_comm_yestarday     = $production_arr[$working_com][$job_no][111]['yesterday'];
                                    $mending_comm_yestarday     = $production_arr[$working_com][$job_no][112]['yesterday'];
                                    $pqc_com_yestarday          = $production_arr[$working_com][$job_no][114]['yesterday'];
                                // }
                                
                                $packing_status="Running"; $shpment_date='';
                                
                                if($packing_comm>=$plan_cut){
                                    $ship_status_color="#FFA500";
                                    $packing_status="Complete";
                                } 
                                 else if($packing_comm<$plan_cut){
                                    $packing_status="Running";
                                    $ship_status_color="";
                                 } 
                                                    
                                ?>
                                 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td width="30"><? echo $sl; ?></td>    
                                   
                          
                                    <td width="100" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?>&nbsp;</td>
                                    <td width="100" style="word-break:break-all"><? echo $job_no; ?>&nbsp;</td>
                                    <td width="100" style="word-break:break-all"><? echo $style_ref_no; ?>&nbsp;</td>
                                    <td width="80" style="word-break:break-all"><? echo $gauge_arr[$gauge]; ?>&nbsp;</td>
                                    <td width="80" style="word-break:break-all"><? echo '&nbsp;'.change_date_format($shipment_date_arr[$job_no]); ?></td>                   
                                    <td width="80" align="right"><? echo $po_qty; ?></td>
                                    <td width="80" align="right"><? echo $avg_unit_price; ?></td>
                                    <td width="80" align="right"><? echo $plan_cut; ?></td>
                                    <td width="80" align="right"><? echo $cm_cost_library[$job_no]; ?></td>
                                   
                                      <?php if(!empty($knitting_com_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $knitting_com_today; ?></td>

                                    <td width="70" align="right">
                                        <?
                                        $prod_fob=$avg_unit_price*$knitting_com_today;
                                         echo $prod_fob; ?></td>

                                    <td width="70" align="right"><? echo $knitting_com; ?></td>
                                    <td width="70" align="right"><? echo $kintting_bal; ?></td>

                                      <?php if(!empty($linking_comp_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>"><? echo $linking_comp_today; ?></td>
                                    <td width="70" align="right"><? 
                                    $linking_prod_fob=$linking_comp_today*$avg_unit_price;
                                    echo $linking_prod_fob; ?>
                                    </td>
                                    <td width="70" align="right"><? echo $linking_comp; ?></td>
                                    <td width="70" align="right"><? echo $linking_bal; ?></td>
                                    
                                     <?php if(!empty($sewing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $sewing_comm_today; ?></td>
                                    <td width="70" align="right" >
                                    <? 
                                    $sewing_prod_fob=$sewing_comm_today*$avg_unit_price;
                                    echo $sewing_prod_fob; ?>
                                    </td>

                                    <td width="70" align="right"><? echo $sewing_comm; ?></td>
                                    <td width="70" align="right"><? echo $sewing_bal; ?></td>
                                     <?php if(!empty($packing_comm_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>

                                    <td width="70" align="right" bgcolor="<?php echo $bgcl; ?>" ><? echo $packing_comm_today; ?></td>
                                    <td width="70" align="right">
                                    <? 
                                    $packing_prod_fob=$packing_comm_today*$avg_unit_price;
                                    echo $packing_prod_fob; ?>
                                    </td>
                                    <td width="70" align="right"><? echo $packing_comm; ?></td>
                                    <td width="70" align="right"><? echo $packing_bal; ?></td>
                                    
                                </tr>
                                <?
                                $i++;
                                $sl++;
                                $tot_po_qty += $po_qty;
                                $tot_plan_cut += $plan_cut;
                                $tot_knitting_prod_fob += $prod_fob;
                                $tot_linking_prod_fob += $linking_prod_fob;
                                $tot_sewing_prod_fob += $sewing_prod_fob;
                                $tot_packing__prod_fob += $packing_prod_fob;

                                $tot_knitting_yesterday += $knitting_com_yestarday;
                                $tot_knitting_today += $knitting_com_today;
                                $tot_knitting_com+=$knitting_com;
                                $tot_kintting_bal+=$kintting_bal;
                                $tot_knitting_wip+=$knitting_wip;
                                
                                $tot_inspection_yesterday+=$inspection_qnty_yestarday;
                                $tot_inspection_today+=$inspection_qnty_today;
                                $tot_inspection_qnty+=$inspection_qnty;
                                $tot_inspe_bal+=$inspe_bal;
                                $tot_ins_wip+=$ins_wip;

                                $tot_linking_yesterday+=$linking_comp_yestarday;
                                $tot_linking_today+=$linking_comp_today;
                                $tot_linking_comp+=$linking_comp;
                                $tot_linking_bal+=$linking_bal;
                                $tot_linking_wip+=$linking_wip;

                             
                                
                                $tot_attach_yesterday+=$attach_comm_yestarday;
                                $tot_attach_today+=$attach_comm_today;
                                $tot_attach_comm+=$attach_comm;
                                $tot_attach_bal+=$attach_bal;
                                $tot_attach_wip+=$attach_wip;
                                
                                $tot_sewing_yesterday+=$sewing_comm_yestarday;
                                $tot_sewing_today+=$sewing_comm_today;
                                $tot_sewing_comm+=$sewing_comm;
                                $tot_sewing_bal+=$sewing_bal;
                                $tot_sewing_wip+=$sewing_wip;
                                
                                $tot_pqc_yesterday+=$pqc_com_yestarday;
                                $tot_pqc_today+=$pqc_com_today;
                                $tot_pqc_comp+=$pqc_com;
                                $tot_pqc_bal+=$pqc_bal;
                                $tot_pqc_wip+=$pqc_wip;
                                
                                $tot_iron_wip+=$iron_wip;
                                $tot_iron_yesterday+=$iron_com_yestarday;
                                $tot_iron_today+=$iron_com_today;
                                $tot_iron_com+=$iron_com;
                                $tot_iron_bal+=$iron_bal;
                                $tot_re_iron+=$re_iron;
                                
                                $tot_packing_comm+=$packing_comm;
                                $tot_packing_bal+=$packing_bal;
                                $tot_shipment_com+=$shipment_com;
                                $tot_shipment_acc_bal+=$shipment_acc_bal;

                                 $tot_packing_today+=$packing_comm_today;
                               
                            }
                        }
                        ?>
                    </table>
                </div>
                <table width="1910" cellspacing="0" border="1" class="tbl_bottom" rules="all">
                    <tr>
                        <td width="30">&nbsp;</td>                       
                      
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        
                        <td width="100">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">Total:</td>
                        <td width="80"><?=$tot_po_qty;?></td>
                        <td width="80">&nbsp;</td>
                        <td width="80"><?=$tot_plan_cut;?></td> 
                        <td width="80">&nbsp;</td>
                       
                        <td width="70" id=""><? echo $tot_knitting_today; ?></td>
                        <td width="70" id=""><? echo $tot_knitting_prod_fob; ?></td>

                        <td width="70" id=""><? echo $tot_knitting_com; ?></td>
                        <td width="70" id=""><? echo $tot_kintting_bal; ?></td>

                         <?php if(!empty($tot_linking_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_linking_today; ?></td>
                        <td width="70" id=""><? echo $tot_linking_prod_fob; ?></td>
                        <td width="70" id=""><? echo $tot_linking_comp; ?></td>
                        <td width="70" id=""><? echo $tot_linking_bal; ?></td>
                      

                          <?php if(!empty($tot_sewing_today)){ $bgcl= $today_color; } else{ $bgcl=""; } ?>


                        <td width="70" id="" bgcolor="<?php echo $bgcl; ?>" ><? echo $tot_sewing_today; ?></td>
                        <td width="70" id=""><? echo $tot_sewing_prod_fob; ?></td>
                        <td width="70" id=""><? echo $tot_sewing_comm; ?></td>
                        <td width="70" id=""><? echo $tot_sewing_bal; ?></td>
                        
                       
                        <td width="70" id=""><? echo $tot_packing_today; ?></td>
                        <td width="70" id=""><? echo $tot_packing__prod_fob; ?></td>
                        <td width="70" id=""><? echo $tot_packing_comm; ?></td>
                        <td width="70" id=""><? echo $tot_packing_bal; ?></td>
                        
                    </tr>
                </table>
            </div>
        </fieldset>
        <?
    }
    
    
    $html = ob_get_contents();
    ob_clean();

    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');    
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename"; 
    
    exit();
}


if($action=="show_image")
{
    echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
    ?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        {
            ?>
            <td><img src='../../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
            <?
        }
        ?>
        </tr>
    </table>
    <?
    exit();
}

?>
