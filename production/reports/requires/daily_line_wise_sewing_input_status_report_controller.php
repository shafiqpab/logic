<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location_id", 140, "SELECT id,location_name FROM lib_location WHERE status_active=1 AND is_deleted=0 AND company_id='$data'
    ORDER BY location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/daily_line_wise_sewing_input_status_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/daily_line_wise_sewing_input_status_report_controller',document.getElementById('cbo_floor_id').value+'_'+this.value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_to').value, 'load_drop_down_line', 'line_td' );",0 );
    exit();
}

if ($action=="load_drop_down_floor")
{
    echo create_drop_down( "cbo_floor_id", 150, "SELECT id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/daily_line_wise_sewing_input_status_report_controller',this.value+'_'+document.getElementById('cbo_location_id').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_to').value, 'load_drop_down_line', 'line_td' );",0 );
    exit();


}


if ($action=="load_drop_down_line")
{
    $explode_data = explode("_",$data);
    $prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
    $txt_date = $explode_data[3];

    $cond="";
    if($prod_reso_allo==1)
    {
        $line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
        $line_array=array();

        if($txt_date=="")
        {
            if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
            if( $explode_data[0]!=0 ) $cond = " and floor_id= $explode_data[0]";
            $line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
        }
        else
        {
            if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
            if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";
         if($db_type==0)    $data_format="and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."'";
         if($db_type==2)    $data_format="and b.pr_date='".change_date_format($txt_date,'','',1)."'";

             $line_data=sql_select( "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach($line_data as $row)
        {
            $line='';
            $line_number=explode(",",$row[csf('line_number')]);
            foreach($line_number as $val)
            {
                if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
            }
            $line_array[$row[csf('id')]]=$line;
        }

        echo create_drop_down( "cbo_line_id", 100,$line_array,"", 1, "-- Select --", $selected, "",0,0 );
    }
    else
    {
        if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
        if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

        echo create_drop_down( "cbo_line_id", 100, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select --", $selected, "",0,0 );
    }
    exit();
}



if($action=="report_generate")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0 and status_active=1");

    $companyArr = return_library_array("SELECT id,company_name FROM lib_company WHERE status_active=1 and is_deleted=0 and id=$cbo_company_id ","id","company_name");


    $buyer_lib = return_library_array("SELECT id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name");
    $floor_lib = return_library_array("SELECT id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id=$cbo_company_id","id","floor_name");

    $line_lib = return_library_array("SELECT id,line_name from lib_sewing_line","id","line_name");
    if($prod_reso_allo==1)
    {

        $line_libr ="SELECT id,line_number from prod_resource_mst where company_id=$cbo_company_id and is_deleted=0 ";
        foreach(sql_select($line_libr) as $row)
        {
            $line='';
            $line_number=explode(",",$row[csf('line_number')]);
            foreach($line_number as $val)
            {
                if($line=='') $line=$line_lib[$val]; else $line.=",".$line_lib[$val];
            }
            $line_lib_resource[$row[csf('id')]]=$line;
        }

    }


    $locationArr = return_library_array("SELECT id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id=$cbo_company_id","id","location_name");
    $color_lib = return_library_array("SELECT id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
    $size_lib = return_library_array("SELECT id,size_name FROM lib_size WHERE status_active=1 AND is_deleted=0","id","size_name");
    $prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');

    $today_date=date("Y-m-d");
    $date_from=str_replace("'","",$txt_date_from);
    $date_to=str_replace("'","",$txt_date_to);
    $company_cond=(str_replace("'","",$cbo_company_id)==0)?"":" and a.serving_company= $cbo_company_id";
    $location_cond=(str_replace("'","",$cbo_location_id)==0)?"":" and a.location= $cbo_location_id";
    $floor_cond=(str_replace("'","",$cbo_floor_id)==0)?"":" and a.floor_id= $cbo_floor_id";
    $line_cond=(str_replace("'","",$cbo_line_id)==0)?"":" and a.sewing_line= $cbo_line_id";
    $shift_cond = (str_replace("'","",$cbo_shift_name)==0)? "" :" and a.shift_name= $cbo_shift_name";

    $lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
        order by sewing_line_serial");
    foreach($lineDataArr as $lRow)
    {
        $lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
        $lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
        $lastSlNo=$lRow[csf('sewing_line_serial')];
    }


    if (str_replace("'", "", $txt_date_from) != "" || str_replace("'", "", $txt_date_to) != "")
    {
        if ($db_type == 0)
        {
            $start_date = change_date_format(str_replace("'", "", $txt_date_from), "yyyy-mm-dd", "");
            $end_date = change_date_format(str_replace("'", "", $txt_date_to), "yyyy-mm-dd", "");
        }
        else if ($db_type == 2)
        {
            $start_date = change_date_format(str_replace("'", "", $txt_date_from), "", "", 1);
            $end_date = change_date_format(str_replace("'", "", $txt_date_to), "", "", 1);
        }
        $date_cond = " and a.production_date between '$start_date' and '$end_date'";
    }




        $sql = "SELECT b.buyer_name,b.style_ref_no,b.job_no_prefix_num ,a.po_break_down_id,c.po_number, c.grouping, a.item_number_id,e.color_number_id,   d.cut_no,a.floor_id,a.sewing_line ,sum(d.production_qnty) as prod_qnty ,a.prod_reso_allo,a.shift_name,a.production_date
        FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a, pro_garments_production_dtls d, wo_po_color_size_breakdown e  WHERE b.job_no = c.job_no_mst  and c.id = a.po_break_down_id and a.id = d.mst_id and a.production_type=4 and d.production_type=4  and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  and c.status_active in(1,2,3) and c.is_deleted = 0 and e.status_active in(1,2,3) and e.is_deleted = 0
        and a.location is not null and a.location <> 0 and d.color_size_break_down_id is not null and d.color_size_break_down_id <> 0 $company_cond $location_cond $floor_cond $shift_cond
        $line_cond  $date_cond  group by b.buyer_name,b.style_ref_no,b.job_no_prefix_num ,a.po_break_down_id,c.po_number,c.grouping,a.item_number_id,e.color_number_id, d.cut_no,a.floor_id,a.sewing_line,a.prod_reso_allo,a.shift_name,a.production_date order by b.buyer_name,b.style_ref_no,b.job_no_prefix_num ,a.po_break_down_id,c.po_number,c.grouping,a.item_number_id,e.color_number_id, d.cut_no";
        //  echo $sql; die;
    $production_data = sql_select($sql);
    $cut_nos="";
    $po_ids="";
    $color_ids="";
    $item_ids="";
    $data_array = array();
    $po_id_array = array();
    foreach($production_data as $vals)
    {
        $cut_no=$vals[csf("cut_no")];
        $po_id=$vals[csf("po_break_down_id")];
        $color_id=$vals[csf("color_number_id")];
        $item_id=$vals[csf("item_number_id")];
        $cut_nos.=($cut_nos=="")? "'".$cut_no."'" : ","."'".$cut_no."'";
        $po_ids.=($po_ids=="")? $po_id: ",".$po_id;
        $color_ids.=($color_ids=="")? $color_id: ",".$color_id;
        $item_ids.=($item_ids=="")? $item_id: ",".$item_id;
        $po_id_array[$po_id]= $po_id;
        if($vals[csf('prod_reso_allo')]==1)
        {
            $sewing_line_ids=$prod_reso_arr[$vals[csf('sewing_line')]];
            $sl_ids_arr = explode(",", $sewing_line_ids);
            $sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take
        }
        else
        {
            $sewing_line_id=$vals[csf('sewing_line')];
        }

        if($lineSerialArr[$sewing_line_id]=="")
        {
            $lastSlNo++;
            $slNo=$lastSlNo;
            $lineSerialArr[$sewing_line_id]=$slNo;
        }
        else $slNo=$lineSerialArr[$sewing_line_id];

        $data_array[$vals[csf("production_date")]][$floor_lib[$vals[csf("floor_id")]]][$slNo][$vals[csf("sewing_line")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]][$vals[csf("shift_name")]]['style_ref_no'] = $vals[csf("style_ref_no")];
       
        $data_array[$vals[csf("production_date")]][$floor_lib[$vals[csf("floor_id")]]][$slNo][$vals[csf("sewing_line")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]][$vals[csf("shift_name")]]['po_number'] = $vals[csf("po_number")];

        $data_array[$vals[csf("production_date")]][$floor_lib[$vals[csf("floor_id")]]][$slNo][$vals[csf("sewing_line")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]][$vals[csf("shift_name")]]['grouping'] = $vals[csf("grouping")];

        $data_array[$vals[csf("production_date")]][$vals[csf("production_date")]][$floor_lib[$vals[csf("floor_id")]]][$slNo][$vals[csf("sewing_line")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]][$vals[csf("shift_name")]]['floor_id'] = $vals[csf("floor_id")];
        $data_array[$vals[csf("production_date")]][$floor_lib[$vals[csf("floor_id")]]][$slNo][$vals[csf("sewing_line")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]][$vals[csf("shift_name")]]['prod_qnty'] += $vals[csf("prod_qnty")];

        $data_array[$vals[csf("production_date")]][$floor_lib[$vals[csf("floor_id")]]][$slNo][$vals[csf("sewing_line")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]][$vals[csf("cut_no")]][$vals[csf("shift_name")]]['shift_name'] = $vals[csf("shift_name")];
    }

    //=================================== CLEAR TEMP ENGINE ====================================
    $con = connect();
    execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 168 and ref_from in(1)");
    oci_commit($con);
    //=================================== INSERT LINE ID INTO TEMP ENGINE ====================================
    fnc_tempengine("gbl_temp_engine", $user_id, 168, 1,$po_id_array, $empty_arr);

    $all_prod_sql=" SELECT o.color_number_id,m.item_number_id,m.po_break_down_id,m.sewing_line,n.production_qnty as pro_qnty,m.shift_name FROM pro_garments_production_dtls n ,pro_garments_production_mst  m,wo_po_color_size_breakdown o,gbl_temp_engine tmp WHERE m.id=n.mst_id  and o.id= n.color_size_break_down_id and m.po_break_down_id=tmp.ref_val and  n.status_active=1 and n.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and o.status_active=1 and o.is_deleted=0  and n.production_type=4  and m.production_type=4 and tmp.user_id=$user_id and tmp.entry_form=168 and tmp.ref_from=1";
    // echo  $all_prod_sql; die;
    foreach(sql_select($all_prod_sql) as $v)
    {
            $all_prod_arr[$v["PO_BREAK_DOWN_ID"]][$v["ITEM_NUMBER_ID"]][$v["COLOR_NUMBER_ID"]][$v["SEWING_LINE"]] +=$v["PRO_QNTY"];
    }
    //    echo $all_prod_sql; die;
    //    print_r($all_prod_arr); die;

    $order_cut_sql="SELECT a.cutting_no,b.order_cut_no,b.order_id,b.gmt_item_id,b.color_id  FROM ppl_cut_lay_mst a,ppl_cut_lay_dtls b WHERE a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0  and a.cutting_no in($cut_nos) ";
    $order_cut_result=sql_select($order_cut_sql);
    foreach($order_cut_result as $cut_vals)
    {
        $order_cut_arr[$cut_vals[csf("cutting_no")]] [$cut_vals[csf("gmt_item_id")]] [$cut_vals[csf("color_id")]] .=$cut_vals[csf("order_cut_no")].' ,';
    }
      $plan_cut_sql="SELECT po_break_down_id,item_number_id,color_number_id, SUM(plan_cut_qnty) as qtys FROM wo_po_color_size_breakdown   WHERE status_active in(1,2,3)  AND is_deleted=0  AND po_break_down_id in($po_ids) AND color_number_id in($color_ids) AND item_number_id in($item_ids) GROUP BY po_break_down_id,item_number_id,color_number_id ";
    $plan_cut_result=sql_select($plan_cut_sql);
    foreach($plan_cut_result as $val_plan)
    {
        $plan_cut_arr[$val_plan[csf("po_break_down_id")]][$val_plan[csf("item_number_id")]][$val_plan[csf("color_number_id")]]+=$val_plan[csf("qtys")];
    }

    // echo "<pre>";print_r($data_array);die();
    // echo "<div style='color:red;text-align:center;font-size:20px'>This  report is under QC. Please be patient.</div>";
    //=================================== CLEAR TEMP ENGINE ====================================
    execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 168 and ref_from in(1)");
    oci_commit($con);
    disconnect($con);
       ob_start();
    ?>

         <script type="text/javascript">
             setFilterGrid('table_body',-1);
         </script>

    <div style="width:1700px;max-height:400px;" id="scroll_body" align="center">
    <table class="rpt_table" width="1700" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header" >
        <thead>
            <tr>
                <th width="40">SL</th>
                <th width="100">Date</th>
                <th width="120">Buyer Name</th>
                <th width="130">Style Reff</th>
                <th width="60">Job No</th>
                <th width="100">PO No</th>
                <th width="100">Internal Ref.</th>
                <th width="130">Garment Item</th>
                <th width="80">Color Name</th>
                <th width="100">PO Qty (Pcs)</th>
                <th width="120">System Cut No</th>
                <th width="120">Order Cut No</th>
                <th width="100">Shift</th>
                <th width="100">Floor Name</th>
                <th width="90">Line Name</th>
                <th width="80">Input Qty</th>
                <th >Total Input Qty</th>
            </tr>
        </thead>
    </table>
 </div>
    <div style="width:1700px;max-height:400px;" id="scroll_body" align="center">
        <table class="rpt_table" width="1700" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <tbody>
                <?
                $m=1;
                $order_qnty_total=0;
                $total_input_qnty=0;
                $total_input_qntyV2=0;
                $plancut=0;
                $all_prod_tot=0;

                ksort($data_array);
                foreach ($data_array as $prod_date => $prod_data)
                {
                    foreach($prod_data as $floor_name => $floor_data )
                    {
                        ksort($floor_data);
                        foreach ($floor_data as $slNo => $slValue)
                        {                                                       ;
                            foreach ($slValue as $line => $line_data)
                            {
                                foreach($line_data as $buyer => $buyer_data)
                                {
                                    foreach ($buyer_data as $job_no => $job_data)
                                    {
                                        foreach ($job_data as $po_id => $po_data)
                                        {
                                            foreach ($po_data as $item_id => $item_data)
                                            {
                                                foreach ($item_data as $color_id => $color_data)
                                                {
                                                    foreach ($color_data as $cut_no => $shift_data)
                                                    {
                                                        foreach ($shift_data as $shift => $row)
                                                        {
                                                            if ($m % 2 == 0)
                                                            $bgcolor = "#E9F3FF";
                                                            else
                                                            $bgcolor = "#FFFFFF";
                                                            ?>
                                                            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
                                                                <td width="40" style="word-wrap: break-word;word-break: break-all;" align="center"><?  echo $m;?></td>
                                                                <td width="100" style="word-wrap: break-word;word-break: break-all;" align="center"><p><?  echo $prod_date;?></p></td>
                                                                <td width="120" style="word-wrap: break-word;word-break: break-all;" align="center"><p><?  echo $buyer_lib[$buyer];?></p></td>
                                                                <td width="130" style="word-wrap: break-word;word-break: break-all;" align="center"><p><?  echo $row["style_ref_no"];?></p></td>
                                                                <td width="60" style="word-wrap: break-word;word-break: break-all;" align="center"><?  echo $job_no;?></td>
                                                                <td width="100" style="word-wrap: break-word;word-break: break-all;" align="center"><p><?  echo $row["po_number"];?></p></td>
                                                                <td width="100" style="word-wrap: break-word;word-break: break-all;" align="center"><p><?  echo $row["grouping"];?></p></td>
                                                                <td width="130" style="word-wrap: break-word;word-break: break-all;"  align="center"><p><?  echo $garments_item[$item_id];?></p></td>
                                                                <td width="80"  style="word-wrap: break-word;word-break: break-all;" align="center"><p><?  echo $color_lib[$color_id];?></p></td>
                                                                <td width="100" style="word-wrap: break-word;word-break: break-all;" align="right"><?  echo $plancut=$plan_cut_arr[$po_id][$item_id][$color_id];?></td>
                                                                <td width="120" style="word-wrap: break-word;word-break: break-all;" align="center"><a href="##"  onclick="openmypage_cut_no_qty('<? echo $po_id.'**'.$color_id.'**'.$item_id.'**'.$cut_no.'**'.$row["po_number"];?>','system_cut_no_popup');" ><p><?  echo  $cut_no;?></p></a></td>
                                                                <td width="120"  style="word-wrap: break-word;word-break: break-all;" align="center"> <p><?  echo rtrim($order_cut_arr[$cut_no][$item_id][$color_id],",");?></p></td>
                                                                <td width="100"  style="word-wrap: break-word;word-break: break-all;" align="center"><p><?= $shift_name[$row["shift_name"]];?></p></td>
                                                                <td width="100"  style="word-wrap: break-word;word-break: break-all;" align="center"><p><?  echo  $floor_name;?></p></td>
                                                                <td width="90" title="<?=$slNo;?>" align="center">
                                                                    <p>
                                                                        <? if($prod_reso_allo==1)
                                                                        {
                                                                            echo $line_lib_resource[$line];
                                                                        }
                                                                        else
                                                                        {
                                                                            echo  $line_lib[$line];
                                                                        }
                                                                        ?>

                                                                    </p>
                                                                </td>
                                                                <td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" ><?  echo  $row["prod_qnty"];?></td>
                                                                <td style="word-wrap: break-word;word-break: break-all;"  align="right" >
                                                                    <a href="##"  onclick="openmypage_all_prod_qty('<? echo $po_id.'**'.$color_id.'**'.$item_id.'**'.$line.'**'.$row['po_number'];?>','all_prod_qty_popup');" >
                                                            <?  echo $all_prod_tot = $all_prod_arr[$po_id][$item_id][$color_id][$line];?> </a></td>
                                                            </tr>
                                                            <?
                                                            $order_qnty_total +=$plancut;
                                                            $total_input_qnty +=$row["prod_qnty"];
                                                            $m++;
                                                        }
                                                    }

                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }    
                }
                ?>
           </tbody>
           <tfoot>
                <tr>
                    <th style="word-wrap: break-word;word-break: break-all;"  colspan="9" align="right">Grand Total:</th>
                    <th style="word-wrap: break-word;word-break: break-all;" align="right"><strong><? echo number_format( $order_qnty_total,2);?> </strong></th>
                    <th style="word-wrap: break-word;word-break: break-all;" colspan="5"> </th>
                    <th style="word-wrap: break-word;word-break: break-all;" align="right"><strong><? echo number_format( $total_input_qnty,2);?> </strong> </th>
                    <th></th>
                </tr>
           </tfoot>
        </table>
    </div>




    <?
    foreach (glob("$user_id*.xls") as $filename)
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename,'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    echo "$total_data####$filename";
    exit();

}

if($action=='all_prod_qty_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Production Info", "../../../", 1, 1,$unicode,'','');
    list($po_id,$color,$item,$line,$po_number)=explode("**",$datas);
    $color_lib = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
    $size_lib = return_library_array("SELECT id,size_name FROM lib_size WHERE status_active=1 AND is_deleted=0","id","size_name");

 $productin_sql="SELECT m.production_date,m.challan_no,SUM(n.production_qnty) AS qnty FROM pro_garments_production_dtls n ,pro_garments_production_mst  m,wo_po_color_size_breakdown o WHERE n.status_active=1 AND n.is_deleted=0 AND m.status_active=1 AND m.is_deleted=0 AND o.status_active in(1,2,3) AND o.is_deleted=0  AND n.production_type=4 AND m.id=n.mst_id  AND o.id= n.color_size_break_down_id AND o.color_number_id='$color'
 AND m.production_type=4 AND m.po_break_down_id='$po_id' AND m.item_number_id='$item' AND m.sewing_line='$line' GROUP BY m.production_date,m.challan_no order by m.production_date";


    $productin_sql_size="SELECT m.production_date,m.challan_no,SUM(n.production_qnty) AS qnty,o.size_number_id FROM pro_garments_production_dtls n ,pro_garments_production_mst  m,wo_po_color_size_breakdown o WHERE n.status_active=1 AND n.is_deleted=0 AND m.status_active=1 AND m.is_deleted=0 AND o.status_active in(1,2,3) AND o.is_deleted=0  AND n.production_type=4 AND m.id=n.mst_id  AND o.id= n.color_size_break_down_id AND o.color_number_id='$color'
 AND m.production_type=4 AND m.po_break_down_id='$po_id' AND m.item_number_id='$item' AND m.sewing_line='$line' GROUP BY m.production_date,m.challan_no,o.size_number_id";

    $size_sql="SELECT id,po_break_down_id,item_number_id,size_number_id,color_number_id FROM wo_po_color_size_breakdown WHERE status_active in(1,2,3) AND is_deleted=0 AND po_break_down_id='$po_id' AND item_number_id='$item' AND color_number_id='$color'";
    $size_result=sql_select($size_sql);
    foreach($size_result as $size_val)
    {
        $size_arr[$size_val[csf("size_number_id")]]=$size_val[csf("size_number_id")];
    }
    foreach(sql_select( $productin_sql_size) as $key=>$rows)
    {
        $prod_arr_size[$rows[csf("production_date")]][$rows[csf("challan_no")]][$rows[csf("size_number_id")]] +=$rows[csf("qnty")];
         $size_wise_total_qnty_array[$rows[csf("size_number_id")]]+=$rows[csf("qnty")];
    }




 ?>
    <fieldset>
    <legend><b> Production Info</b></legend>
     <div style="width:470px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
             <tr>
                 <th colspan="<? echo 3+count($size_arr);  ?>">Total Input Qty POP-UP:</th>
             </tr>
              <tr>
                 <th >PO: <? echo $po_number;?></th>
                 <th colspan="<? echo 2+count($size_arr);  ?>">Color: <? echo $color_lib[$color]; ?></th>
              </tr>

             <tr>
                 <th>Input Date</th>
                 <th>Challan</th>
                 <th>Challan Qty</th>
                 <?
                 foreach($size_arr as $key=>$vals_size)
                 {
                    ?>
                     <th><? echo $size_lib[$vals_size];?></th>

                    <?

                 }
                 ?>
             </tr>
             <?
             $prod_qty_challan=0;
             foreach(sql_select($productin_sql) as $prod_val)
             {
                ?>
              <tr>
                <td align="center"><? echo change_date_format($prod_val[csf("production_date")],"d/m/Y");?></td>
                <td align="center"><? echo $prod_val[csf("challan_no")];?></td>
                <td align="center"><? echo $prod_qtys=$prod_val[csf("qnty")];?></td>
                <?
                $prod_qty_challan+=$prod_qtys;
                foreach($size_arr as $key=>$vals_size)
                 {
                    ?>
                     <td align="center"><? echo  $prod_arr_size[$prod_val[csf("production_date")]][$prod_val[csf("challan_no")]][$vals_size];?></td>

                    <?

                 }
                 ?>
              </tr>
              <?
             }

             ?>
             <tr>
                 <th colspan="2" align="right">Total</th>
                 <th><? echo $prod_qty_challan;?></th>
                 <?
                 foreach($size_arr as $key=>$vals_size)
                 {
                    ?>
                     <th align="center"><? echo  $size_wise_total_qnty_array[$vals_size];?></th>

                    <?

                 }
                 ?>
             </tr>
        </table>
     </div>
    </fieldset>



 <?
 exit();
}
if($action=='system_cut_no_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Cut No Info", "../../../", 1, 1,$unicode,'','');
    list($po_id,$color,$item,$cut_no,$po_number)=explode("**",$datas);
    $color_lib = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
    $size_lib = return_library_array("SELECT id,size_name FROM lib_size WHERE status_active=1 AND is_deleted=0","id","size_name");

    $po_lib_arr = return_library_array("SELECT id,po_number FROM wo_po_break_down WHERE status_active in(1,2,3)  AND is_deleted=0 and id in(select b.order_id from ppl_cut_lay_mst a, ppl_cut_lay_size b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and a.cutting_no='$cut_no' ) ","id","po_number");


   $productin_sql="SELECT  c.order_id,c.size_id,sum(c.marker_qty) as marker_qty FROM ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_size c WHERE a.id=b.mst_id AND b.id=c.dtls_id AND a.id=c.mst_id AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND
 c.status_active=1 AND c.is_deleted=0 AND  a.cutting_no='$cut_no'  AND b.color_id='$color' GROUP BY c.order_id,c.size_id";
 foreach(sql_select($productin_sql) as $vals)
 {
    $po_wise_size_arr[$vals[csf("order_id")]][$vals[csf("size_id")]]+=$vals[csf("marker_qty")];
    $po_wise_qnty[$vals[csf("order_id")]]+=$vals[csf("marker_qty")];
    $po_wise_arr[$vals[csf("order_id")]] =$vals[csf("order_id")];
    $size_wise_total_qnty[$vals[csf("size_id")]] +=$vals[csf("marker_qty")];
 }

    $size_sql="SELECT id,po_break_down_id,item_number_id,size_number_id,color_number_id FROM wo_po_color_size_breakdown WHERE status_active in(1,2,3)  AND is_deleted=0 AND po_break_down_id='$po_id' AND item_number_id='$item' AND color_number_id='$color'";
    $size_result=sql_select($size_sql);
    foreach($size_result as $size_val)
    {
        $size_arr[$size_val[csf("size_number_id")]]=$size_val[csf("size_number_id")];
    }
    foreach(sql_select( $productin_sql_size) as $key=>$rows)
    {
        $prod_arr_size[$rows[csf("production_date")]][$rows[csf("challan_no")]][$rows[csf("size_number_id")]] +=$rows[csf("qnty")];
    }
 ?>
    <fieldset>
    <legend><b> System Cut No Info</b></legend>
     <div style="width:470px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
             <tr>
                 <th colspan="<? echo count($size_arr)+1 ;?>">System Cut No Pop-up:</th>
             </tr>
              <tr>
                 <th colspan="<?echo count($size_arr)+1 ;?>" >Cut No: <? echo $cut_no;?> &nbsp;&nbsp;&nbsp;Color: <? echo $color_lib[$color]; ?></th>


             </tr>

             <tr>
             <th>PO</th>

                 <?
                 foreach($size_arr as $key=>$vals_size)
                 {
                    ?>
                     <th><? echo $size_lib[$vals_size];?></th>

                    <?

                 }
                 ?>
             </tr>
             <?
             foreach($po_wise_arr as $po_val)
             {
                ?>
              <tr>
                <td align="center"><?  echo $po_lib_arr[$po_val];?></td>

                <?
                foreach($size_arr as $key=>$vals_size)
                 {
                    ?>
                     <td align="center"><? echo  $po_wise_size_arr[$po_val][$vals_size];?></td>

                    <?

                 }
                 ?>
              </tr>
              <?
             }

             ?>
             <tr>
                 <th align="right">Total</th>
                 <?
                foreach($size_arr as $key=>$vals_size)
                 {
                    ?>
                     <th align="center"><? echo  $size_wise_total_qnty[$vals_size];?></th>

                    <?

                 }
                 ?>
             </tr>
        </table>
     </div>
    </fieldset>



 <?
 exit();
}
?>