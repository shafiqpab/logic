<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");

require_once('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_location") {
    echo create_drop_down("cbo_location", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name", "id,location_name", 1, "-Select Location-", $selected, "", 0);
    exit();
}

if ($action == "load_drop_down_buyer") {
    echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-Select Buyer-", $selected, "");
    exit();
}

if ($action == "report_generate") 
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    //echo $datediff;
    $cbo_company = str_replace("'", "", $cbo_company_id);
    $cbo_location = str_replace("'", "", $cbo_location);
    $date_from = str_replace("'", "", $txt_date_from);
    $date_to = str_replace("'", "", $txt_date_to);
    $cbo_buyer = str_replace("'", "", $cbo_buyer_name);
    $cbo_type = str_replace("'", "", $cbo_type);
    $sql_cond = "";

    if ($date_from && $date_to) {
        if ($db_type == 0) {
            $sql_cond .= " and m.ex_factory_date between '" . change_date_format($date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($date_to, 'yyyy-mm-dd') . "'";
        } else {
            $sql_cond .= " and m.ex_factory_date between '" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
        }
    }
    if ($cbo_company) {
        $sql_cond .= " and dm.delivery_company_id = $cbo_company";
    }
    if ($cbo_location) {
        $sql_cond .= " and dm.delivery_location_id = $cbo_location";
    }
    if ($cbo_buyer) {
        $sql_cond .= " and a.buyer_name = $cbo_buyer";
    }
    if ($db_type == 0) {
        $delayShortSelect = ", DATEDIFF(b.shipment_date, m.ex_factory_date) as delay_time ";
    }else{
        $delayShortSelect = ", (b.shipment_date - m.ex_factory_date) as delay_time ";
    }
    

    $companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $locationArr = return_library_array("select id,location_name from lib_location where status_active = 1 and is_deleted = 0", "id", "location_name");
    //$buyerArr = return_library_array("select id,buyer_name from lib_buyer  where status_active = 1 and is_deleted = 0", "id", "buyer_name");
    $buyer_res = sql_select("select id,buyer_name,delivery_buffer_days from lib_buyer  where status_active = 1 and is_deleted = 0");
    foreach($buyer_res as $b_row)
    {
        $buyerArr[$b_row[csf("id")]]["name"] = $b_row[csf("buyer_name")];
        $buyerArr[$b_row[csf("id")]]["buffer_time"] = $b_row[csf("delivery_buffer_days")];
        
    }
    
    $sql_res=sql_select("select b.po_break_down_id as po_id,c.color_size_break_down_id, c.production_qnty as return_qnty, d.order_rate
                from pro_ex_factory_mst b, pro_ex_factory_dtls c,  wo_po_color_size_breakdown d
                where b.id = c.mst_id and c.color_size_break_down_id = d.id
                and b.entry_form = 85
                and b.status_active=1 and b.is_deleted=0 
                and c.status_active = 1 and c.is_deleted = 0");
		$ex_return_qty_arr=array();
		foreach($sql_res as $row)
		{
                    $ex_return_value =  $row[csf('return_qnty')]*$row[csf('order_rate')];
                    $ex_return_qty_arr[$row[csf('po_id')]][$row[csf('color_size_break_down_id')]]['return_qty']=$row[csf('return_qnty')];                    
                    $ex_return_qty_arr[$row[csf('po_id')]][$row[csf('color_size_break_down_id')]]['return_value']=$ex_return_value;
                    $ex_return_qty_arr[$row[csf('po_id')]]['color_size_list'].=$row[csf('color_size_break_down_id')].",";
		}
    
  //   if($cbo_type == 2) //for country date wise comparizon in buyer level
  //   {
        /*$sql_country_w = "select  dm.delivery_company_id,dm.delivery_location_id,m.ex_factory_date, m.ex_factory_qnty as ex_qnty, a.buyer_name,m.id,
        b.po_quantity, a.total_set_qnty,b.unit_price,b.shipment_date,c.country_ship_date,m.po_break_down_id
        from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c
        where dm.id = m.delivery_mst_id and  m.po_break_down_id = b.id 
        and a.job_no = b.job_no_mst $sql_cond
        and m.po_break_down_id = c.po_break_down_id
        and dm.delivery_company_id <> 0 and m.entry_form <> 85 and dm.delivery_company_id is not null
        and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
        and m.status_active = 1 and m.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0
        ";
         */
        $sql_country_w="select  m.id,dm.delivery_company_id,dm.delivery_location_id,m.ex_factory_date, m.ex_factory_qnty as ex_qnty, a.buyer_name,
        c.country_ship_date,m.po_break_down_id,d.production_qnty, c.order_rate*d.production_qnty as ex_value,c.id as color_size
        from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_color_size_breakdown c,
        pro_ex_factory_dtls d
        where dm.id = m.delivery_mst_id 
        and a.job_no = c.job_no_mst
        and m.id = d.mst_id $sql_cond
        and d.color_size_break_down_id = c.id     
        and dm.delivery_company_id <> 0 and m.entry_form <> 85 and dm.delivery_company_id is not null
        and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 
        and m.status_active = 1 and m.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0
        order by m.id";
        
    // }
     //echo $sql_country_w;
    $country_w_res = sql_select($sql_country_w);
    $data_buyer_wise = array();
    $qntyChkArr = array();
    foreach($country_w_res as  $row)
    {
        $buffer_time = 0;
        if($buyerArr[$row[csf("buyer_name")]]["buffer_time"])
        {
            $buffer_time = "+".$buyerArr[$row[csf('buyer_name')]]['buffer_time']." days";
        }

        $time_diff_buffer = strtotime($row[csf("country_ship_date")] . $buffer_time) - strtotime($row[csf("ex_factory_date")]);
        
        $time_diff = strtotime($row[csf("country_ship_date")]) - strtotime($row[csf("ex_factory_date")]);



        $data_buyer_wise[$row[csf("buyer_name")]]["ex_qnty"] += $row[csf("production_qnty")];
        if($time_diff > 0)
        {
            $data_buyer_wise[$row[csf("buyer_name")]]["early"] += $row[csf("production_qnty")];
        }
        else if($time_diff_buffer < 0 )
        {
            $data_buyer_wise[$row[csf("buyer_name")]]["delay"] += $row[csf("production_qnty")];
        }
        else if($time_diff <= 0 && $time_diff_buffer >= 0)
        {
            $data_buyer_wise[$row[csf("buyer_name")]]["ontime"] += $row[csf("production_qnty")];
        }

        /*
        $data_buyer_wise[$row[csf("buyer_name")]]["ex_qnty"] += $row[csf("production_qnty")];
        if($time_diff > 0)
        {
            $data_buyer_wise[$row[csf("buyer_name")]]["early"] += $row[csf("production_qnty")];
        }
        else if($time_diff < 0)
        {
            $data_buyer_wise[$row[csf("buyer_name")]]["delay"] += $row[csf("production_qnty")];
        }
        else
        {
            $data_buyer_wise[$row[csf("buyer_name")]]["ontime"] += $row[csf("production_qnty")];
        }
        */


      /*  
        if($qntyChkArr[$row[csf("id")]] == "")
        {
            $qntyChkArr[$row[csf("id")]] = $row[csf("id")];
            $time_diff = strtotime($row[csf("country_ship_date")]) - strtotime($row[csf("ex_factory_date")]);
            $data_buyer_wise[$row[csf("buyer_name")]]["ex_qnty"] += $row[csf("ex_qnty")];
            if($time_diff > 0)
            {
                $data_buyer_wise[$row[csf("buyer_name")]]["early"] += $row[csf("ex_qnty")];
            }
            else if($time_diff < 0)
            {
                $data_buyer_wise[$row[csf("buyer_name")]]["delay"] += $row[csf("ex_qnty")];
            }
            else
            {
                $data_buyer_wise[$row[csf("buyer_name")]]["ontime"] += $row[csf("ex_qnty")];
            }
        }  */    
    }
    
    
    foreach($country_w_res as $row)
    {
        //$key = $row[csf("delivery_company_id")]."*".$row[csf("delivery_location_id")]."*".$row[csf("buyer_name")];
        $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_qnty'] += $row[csf("production_qnty")];
        $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_value'] += $row[csf("ex_value")];
        $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['po_id'] .= $row[csf("po_break_down_id")].",";
        $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['color_size'] .= $row[csf("color_size")].",";
    }
    
    //    echo "<pre>";
    //    print_r($result_array[17]);
    //    echo "</pre>";
    //    die;

    $sql = "select b.id,dm.delivery_company_id,dm.delivery_location_id, m.ex_factory_qnty as ex_qnty, a.buyer_name,(b.unit_price/a.total_set_qnty) as unit_price,(b.po_quantity*a.total_set_qnty) as po_quantity, 
            ((b.unit_price/a.total_set_qnty)*(b.po_quantity*a.total_set_qnty)) as  po_value $delayShortSelect
            from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_break_down b 
            where dm.id = m.delivery_mst_id and a.job_no = b.job_no_mst 
            and b.id= m.po_break_down_id and dm.status_active = 1 
            and dm.is_deleted = 0 and m.status_active = 1 and m.is_deleted = 0 and dm.delivery_company_id <> 0 
            and m.entry_form <> 85 and dm.delivery_company_id is not null
            $sql_cond
            order by dm.delivery_company_id , dm.delivery_location_id, a.buyer_name asc ";
    /*
  //echo $sql;
    $result = sql_select($sql);
    
    foreach($result as $row){
        $key = $row[csf("delivery_company_id")]."*".$row[csf("delivery_location_id")]."*".$row[csf("buyer_name")];
        $result_array["company"][$key] = $row[csf("delivery_company_id")];
        $result_array["location"][$key] = $row[csf("delivery_location_id")];
        $result_array["buyer"][$key] = $row[csf("buyer_name")];
        $result_array["ex_qnty"][$key]  += $row[csf("ex_qnty")]- $ex_return_qty_arr[$row[csf('id')]]['return_qty'];
        $result_array["ex_value"][$key]  += ($row[csf("ex_qnty")]- $ex_return_qty_arr[$row[csf('id')]]['return_qty'])*$row[csf("unit_price")];
        $result_array["po_quantity"][$key] += $row[csf("po_quantity")];
        //$result_array["po_value"][$key] += $row[csf("po_value")];

    }*/
    ob_start();
    ?>
    <div style='font-family: "Arial Narrow", Arial, sans-serif;'> 
        <table style="width:1306px; float: left;border:none;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
            <thead>
                <tr style="border:none;">
                    <td colspan="9" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $companyArr[$cbo_company];?> </td> 
                </tr>
                <tr style="border:none;">
                    <td colspan="9" align="center" style="font-weight: bold; border: none;">
                        <? echo strtoupper($report_title); ?> 
                        <?
                        if ($date_from != "" && $date_to != "") {
                            echo "<br/>From: ".change_date_format($date_from)."  To: ".change_date_format($date_to);
                        }
                        ?> 
                    </td> 
                </tr>
            </thead>
        </table>
        <fieldset style="width:550px;float:left;margin-bottom: 5px; margin-right: 5px;">
            <table style="width:550px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
                <thead>
                    <tr>
                        <td colspan="5" style="font-size:15px; font-weight:bold">Shipment Evaluation</td>
                    </tr>
                    <tr>
                        <th width="150" rowspan="2">Buyer</th>
                        <th width="100" rowspan="2">Qty(pcs)</th>
                        <th width="" colspan="3">Status</th>
                    </tr>
                    <tr>
                        <th width="100">Early</th>
                        <th width="100">On Time</th>
                        <th width="100">Delay</th>
                    </tr>
                </thead>
            </table>
            <div style="width:570px; max-height:350px;overflow-y:scroll" id="scroll_body" > 
             <table width="550" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                
                 <? 
                    if($cbo_type == 1)
                    {
                        $data_array=  sql_select($sql); 
                        foreach($data_array as $row){
                         $data_res["buy"][$row[csf("buyer_name")]] =  $row[csf("buyer_name")];
                         if($data_res["buy"][$row[csf("buyer_name")]]){
                            $data_res["exQnty"][$row[csf("buyer_name")]] += $row[csf("ex_qnty")];

                         }
                        }

                        foreach($data_array as $row){
                            if($row[csf("delay_time")] >0){
                                $data_res["early"][$row[csf("buyer_name")]]  += ($row[csf("ex_qnty")]/$data_res["exQnty"][$row[csf("buyer_name")]])*100;
                            }
                            else if($row[csf("delay_time")] <0){
                                $data_res["delay"][$row[csf("buyer_name")]]  += ($row[csf("ex_qnty")]/$data_res["exQnty"][$row[csf("buyer_name")]])*100;
                            }else {
                                $data_res["on_time"][$row[csf("buyer_name")]]  += ($row[csf("ex_qnty")]/$data_res["exQnty"][$row[csf("buyer_name")]])*100;
                            }
                        }
                        $grand_qnty = 0; $i = 1;
                        foreach($data_res["buy"] as $key =>$value){

                            if(number_format($data_res["early"][$key],2) != 0.00){
                                $early_time = number_format($data_res["early"][$key],2)."%";
                            }else{
                                $early_time= "";
                            }
                            if(number_format($data_res["on_time"][$key],2) != 0.00){
                                $On_time = number_format($data_res["on_time"][$key],2)."%";
                            }else{
                                $On_time= "";
                            }
                            if(number_format($data_res["delay"][$key],2) != 0.00){
                               $delay_time= number_format($data_res["delay"][$key],2)."%";
                            }else{
                                $delay_time= "";
                            }
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";
                            $grand_qnty += $data_res["exQnty"][$key];
                        ?>

                        <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('se_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="se_<? echo $i; ?>">
                            <td width="150"><? echo $buyerArr[$value]["name"];?></td>
                            <td width="100" align="right"><? echo $data_res["exQnty"][$key];?></td>
                            <td width="100" align="right"><? echo $early_time;?></td>
                            <td width="100" align="right"><? echo $On_time;?></td>
                            <td width="" align="right"><? echo $delay_time;?></td>
                        </tr>
                        <? 
                        $i++;
                        }
                    }
                    else
                    {
                        $i = 1;
                        foreach($data_buyer_wise as $buyerId => $row)
                        {
                            $onTime = ($data_buyer_wise[$buyerId]["ontime"]/$data_buyer_wise[$buyerId]["ex_qnty"])*100;
                            $early = ($data_buyer_wise[$buyerId]["early"]/$data_buyer_wise[$buyerId]["ex_qnty"])*100;
                            $delay = ($data_buyer_wise[$buyerId]["delay"]/$data_buyer_wise[$buyerId]["ex_qnty"])*100;
                            if(number_format($onTime,2) != 0.00){
                                $onTime = number_format($onTime,2)."%";
                            }else{
                                $onTime= "";
                            }
                            if(number_format($early,2) != 0.00){
                                $early = number_format($early,2)."%";
                            }else{
                                $early= "";
                            }
                            if(number_format($delay,2) != 0.00){
                                $delay = number_format($delay,2)."%";
                            }else{
                                $delay= "";
                            }
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('se_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="se_<? echo $i; ?>">
                                <td width="150"><? echo $buyerArr[$buyerId]["name"];?></td>
                                <td width="100" align="right"><? echo $data_buyer_wise[$buyerId]["ex_qnty"];?></td>
                                <td width="100" align="right"><? echo $early;?></td>
                                <td width="100" align="right"><? echo $onTime;?></td>
                                <td width="" align="right"><? echo $delay;?></td>
                            </tr>
                            <?
                            $grand_qnty += $data_buyer_wise[$buyerId]["ex_qnty"];
                            $i++;
                        }
                    }
  
                    ?>
                    
                    </table>
                </div>
                    <table style="width:550px;" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
                        <tr style="background-color:#e0e0e0; font-weight: bold">
                            <td width="150">Total</td>
                            <td width="100" align="right"><?echo $grand_qnty;?></td>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                    </table>
        </fieldset>
        <fieldset  style="width:720px; float:left;">
            <table style="width:700px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
                <thead>
                    <tr>
                        <td colspan="6" style="font-size:15px; font-weight:bold">Export Summary By Production Factory</td>
                    </tr>  
                    <tr>
                        <th width="150">Production Factory</th>
                        <th width="100">Location</th>
                        <th width="130">Buyer</th>
                        <th width="100"><p>Export (Qty)<br> <small style="font-size: 10px;">Excluding Return</small></p></th>
                        <th width="100">FOB</th>
                        <th width="">Value in USD</th>
                    </tr>
                </thead>
            </table>
            <div style=" max-height:350px; width:720px; overflow-y:scroll;" id="scroll_body2">
            <table style="width:700px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
                <?
                $buyer_row_span_arr = array();
                foreach($result_array as $company_id => $company_data)
                {
                    
                    foreach($company_data as $location_id => $location_data)
                    {
                        $buyer_row_span= 0;
                        foreach($location_data as $buyer_id => $row)
                        {
                            $buyer_row_span++;
                        }
                        $buyer_row_span_arr[$company_id."*".$location_id] =$buyer_row_span;
                    }
                }
               // echo  $buyer_row_span_arr[];
    //                echo "<pre>";
    //                print_r($buyer_row_span_arr);
    //                echo "</pre>";
                
                $i=$m=1;
                
                foreach($result_array as $company_id => $company_data)
                {
                    foreach($company_data as $location_id => $location_data)
                    {
                        $y = 1;
                        foreach($location_data as $buyer_id => $row)
                        {
                            if ($m % 2 == 0)
                            $bgcolor = "#E9F3FF";
                            else
                            $bgcolor = "#FFFFFF";
                            $buyer_td_span = $buyer_row_span_arr[$company_id."*".$location_id];
                            //$row["ex_qnty"];
                            //echo $buyer_td_span."*";
                            $po_arr =  array_filter(array_unique(explode(",",chop($row["po_id"],","))));
                            $ex_return_qnty = 0;$ex_return_value=0;
                            foreach($po_arr as $po_id)
                            {
                                $color_size_arr = array_filter(array_unique(explode(",",chop($ex_return_qty_arr[$po_id]["color_size_list"],","))));
                                
                                foreach($color_size_arr as $color_size_id)
                                {
                                    $ex_return_qnty +=  $ex_return_qty_arr[$po_id][$color_size_id]['return_qty'];
                                    $ex_return_value +=  $ex_return_qty_arr[$po_id][$color_size_id]['return_value'];
                                }
                            }
                            $ex_qnty_after_return=  $row["ex_qnty"] - $ex_return_qnty;
                            $ex_value_after_return=  $row["ex_value"] - $ex_return_value;
                            $FOB = $ex_value_after_return/$ex_qnty_after_return;
                            
                            //$ex_return_qty_arr[$row[csf('po_id')]]["color_size_list"];
                            //$ex_return_qty_arr[$row[csf('po_id')]][$row[csf('color_size_break_down_id')]]['return_qty']
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
                            <?
                            if($y == 1)
                            {
                                ?>
                                <td width="150" rowspan="<? echo $buyer_td_span;?>"><? echo $companyArr[$company_id];?></td>
                                <td width="100" rowspan="<? echo $buyer_td_span;?>"><? echo $locationArr[$location_id];?></td>
                                <?
                            }
                            $row["po_id"];$row["color_size"];
                            ?>
                                <td width="130"><? echo $buyerArr[$buyer_id]["name"];?></td>
                                <td width="100" align="right"><a href="##" onclick="openmypage_ex_popup('<? echo $company_id;?>','<? echo $location_id;?>','<? echo $buyer_id;?>','<? //echo $row["color_size"];?>','<? echo $date_from;?>','<? echo $date_to;?>','<? echo  $ex_return_qnty;?>');"><? echo $ex_qnty_after_return;?></a></td>
                                <td width="100" align="right"><? echo number_format($FOB,2);?></td>
                                <td width="" align="right"><? echo number_format($ex_value_after_return,2);?></td>
                            </tr>    
                            <?
                            $sub_ex_factory_qnty += $ex_qnty_after_return;
                            $sub_ex_value += $ex_value_after_return;
                            $grand_ex_factory_qnty += $ex_qnty_after_return;
                            $grand_ex_value += $ex_value_after_return;
                            $y++;$m++;
                        }
                        
                        
                       // $sub_ex_factory_qnty=$sub_ex_value=0;
                        ?>
                        <tr style="background-color:#e0e0e0; font-weight: bold">
                        <td colspan="3" align="right" ><b>Sub Total</b></td>
                        <td align="right"><? echo number_format($sub_ex_factory_qnty,2); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($sub_ex_value,2);?></td>
                        </tr>
                        <?
                      $i++;
                       $sub_ex_factory_qnty=$sub_ex_value=0;
                    }
                     
                }
                ?>
                <tr style="background-color:#e0e0e0; font-weight: bold">
                    <td colspan="3" align="right" ><b>Grand Total</b></td>
                    <td align="right"><? echo number_format($grand_ex_factory_qnty,2); ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_ex_value,2);?></td>
                </tr>
                <?
                
                
                
                
                
                
            /*    $i = 1;
                foreach ($result_array["ex_value"] as $key=>$value) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";
                        $ex_value=  $value;
                        $FOB=  $ex_value/$result_array["ex_qnty"][$key];
 
                        if (!in_array($result_array["company"][$key]."*".$result_array["location"][$key], $checkCompany)) 
                        {
                            $checkCompany[$i] = $result_array["company"][$key]."*".$result_array["location"][$key];
                            if ($i > 1) 
                            {
                                ?>
                                <tr style="background-color:#e0e0e0; font-weight: bold">
                                    <td colspan="3" align="right"><b>Sub Total</b></td>
                                    <td align="right"><? echo number_format($sub_ex_qnty, 2); ?></td>
                                    <td>&nbsp;</td>
                                    <td align="right"><? echo number_format($sub_ex_value,2); ?></td>
                                </tr>
                                <?
                                $sub_ex_qnty = 0;
                                $sub_ex_value = 0;
                            }

                        }
                        ?>
                    
                        <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr1_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr1_<? echo $i; ?>">
                            <td width="150"><? echo $companyArr[$result_array["company"][$key]]; ?></td>
                            <td width="100"><? echo $locationArr[$result_array["location"][$key]] ?></td>
                            <td width="130"><? echo $buyerArr[$result_array["buyer"][$key]]["name"] ?></td>
                            <td width="100" align="right"><? echo $result_array["ex_qnty"][$key]; ?></td>
                            <td width="100" align="right" ><? echo number_format($FOB,2)?></td>
                            <td align="right"><? echo number_format($ex_value,2); ?></td>
                        </tr>
                        <?
                        $i++;
                        $sub_ex_qnty += $result_array["ex_qnty"][$key];
                        $sub_ex_value +=$ex_value;
                        $grand_ex_qnty += $result_array["ex_qnty"][$key];
                        $grand_ex_value +=$ex_value;

                }
               */ 
                
                ?>
    <!--            <tr style="background-color:#e0e0e0; font-weight: bold">
                    <td colspan="3" align="right" ><b>Sub Total</b></td>
                    <td align="right"><? echo number_format($sub_ex_qnty,2); ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($sub_ex_value,2);?></td>
                </tr>
                <tr style="background-color:#e0e0e0; font-weight: bold">
                    <td colspan="3" align="right" ><b>Grand Total</b></td>
                    <td align="right"><? echo number_format($grand_ex_qnty,2); ?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($grand_ex_value,2);?></td>
                </tr>-->
            </table>
            </div>
        </fieldset>
    
  <!--
        <fieldset style="width:900px;float: left; margin-top: 5px;">
            <table style="width:900px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
                <thead>
                    <tr>
                        <td colspan="8" style="font-size:15px; font-weight:bold">Short Excess Summary</td>
                    </tr>
                    <tr>
                        <th width="150">Production Factory</th>
                        <th width="100">Location</th>
                        <th width="130">Buyer</th>
                        <th width="100">Total Order Qty</th>
                        <th width="100">Ship Qty</th>
                        <th width="100">Short/Excess</th>
                        <th width="100">FOB</th>
                        <th width="">Value in USD</th>
                    </tr>
                </thead>
            </table>
            <table style="width:900px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
                <? 
                $i = 1;
                foreach ($result_array["ex_value"] as $key=>$value) {
                    if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";
                        $ex_value = $value;
                        $exQnty= $result_array["ex_qnty"][$key];
                        $FOB = $ex_value/$exQnty;
                        //$FOB =  $result_array["po_value"][$key] / $result_array["po_quantity"][$key];
                        $orderQnty =  $result_array["po_quantity"][$key];
                        $shortQnty =$exQnty - $orderQnty;
                        $short_value=  $FOB* $shortQnty;
                        $grandShortValue += $short_value;
                    ?>
                
                <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="150"><? echo $companyArr[$result_array["company"][$key]]; ?></td>
                    <td width="100"><? echo $locationArr[$result_array["location"][$key]] ?></td>
                    <td width="130"><? echo $buyerArr[$result_array["buyer"][$key]]["name"] ?></td>
                    <td width="100" align="right"><? echo $orderQnty;?></td>
                    <td width="100" align="right"><? echo $exQnty; ?></td>
                    <td width="100" align="right"><? echo number_format($shortQnty,2);?></td>
                    <td width="100" align="right"><? echo number_format($FOB,2)?></td>
                    <td align="right"><? echo number_format($short_value,2);?></td>
                </tr>
                
                    <?
                
                    $i++;
                }?>
                <tr style="font-weight: bold">
                    <td colspan="6" align="right">Total</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($grandShortValue,2)?></td>
                </tr>
            </table>
        </fieldset>
    </div>
  -->
    <? 
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename";
    exit();
}

if($action == "ex_qnty_popup")
{
    echo load_html_head_contents("Country Order Dtls Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    //$color_size = chop($color_size,",");
    $order_arr=return_library_array( "select b.po_number, b.id from wo_po_color_size_breakdown a, wo_po_break_down b where a.po_break_down_id = b.id  group by b.po_number, b.id", "id", "po_number"); //and a.id in ($color_size)
        
    $country_arr=return_library_array( "select id, country_name from lib_country where status_active = 1 and is_deleted = 0",'id','country_name');
    $color_library=return_library_array( "select id, color_name from lib_color where status_active = 1 and is_deleted = 0", "id", "color_name");
    $size_library=return_library_array( "select id, size_name from lib_size where status_active = 1 and is_deleted = 0", "id", "size_name");
    ?>  
    <fieldset style="width:570px; margin-left:3px">
        <div id="report_div" align="center">
            <table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Order No.</th>
                    <th width="100">Country</th>
                    <th width="100">Color</th>
                    <th width="100">Size</th>
                    <th>Export Qnty</th>
                </thead>
                <tbody id="table_body">
                        <?
                        $sql_cond = "";
                        $sql_cond2 = "";
                        if ($db_type == 0) {
                            $sql_cond .= " and m.ex_factory_date between '" . change_date_format($date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($date_to, 'yyyy-mm-dd') . "'";
                            $sql_cond2 .= " and b.ex_factory_date between '" . change_date_format($date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($date_to, 'yyyy-mm-dd') . "'";
                        } else {
                            $sql_cond .= " and m.ex_factory_date between '" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
                            $sql_cond2 .= " and b.ex_factory_date between '" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
                        }
                        
                        if ($company) {
                            $sql_cond .= " and dm.delivery_company_id = $company";
                            $sql_cond2 .= " and a.delivery_company_id = $company";
                        }
                        //if ($location) {
                            $sql_cond .= " and dm.delivery_location_id = $location";
                            $sql_cond2 .= " and a.delivery_location_id = $location";
                        //}
                        if ($buyer) {
                            $sql_cond .= " and a.buyer_name = $buyer";
                        }
                        /*if($color_size){
                            $sql_cond .= " and c.id in ($color_size)";
                        }*/
                        $sql = "SELECT c.country_id,c.po_break_down_id,d.production_qnty as production_qnty,c.size_number_id,c.color_number_id
                        from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_color_size_breakdown c, pro_ex_factory_dtls d 
                        where dm.id = m.delivery_mst_id and a.job_no = c.job_no_mst and m.id = d.mst_id
                        and d.color_size_break_down_id = c.id and dm.delivery_company_id <> 0 and m.entry_form <> 85 and dm.delivery_company_id is not null 
                        and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and m.status_active = 1 and m.is_deleted = 0 
                        and c.status_active in(1,2,3) and c.is_deleted = 0 $sql_cond
                        order by c.po_break_down_id,c.country_id";

                       $sql_dtls_miss="SELECT b.id,sum(c.production_qnty) as qnty  from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,pro_ex_factory_dtls c where a.id=b.delivery_mst_id and b.id=c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 $sql_cond2 group by b.id having sum(c.production_qnty) is null";
                       $dtl_miss_array=array();
                       foreach(sql_select($sql_dtls_miss) as $vals)
                       {
                            $dtl_miss_array[$vals[csf("id")]]=$vals[csf("id")];
                       }
                        $dtl_miss_id=implode(",", $dtl_miss_array);

                       $sql2 = "SELECT  c.id as po_break_down_id,m.country_id,m.ex_factory_qnty as production_qnty
                        from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_break_down c  
                        where dm.id = m.delivery_mst_id and a.job_no = c.job_no_mst 
                        and m.po_break_down_id = c.id and dm.delivery_company_id <> 0 and m.entry_form <> 85 and dm.delivery_company_id is not null 
                        and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and m.status_active = 1 and m.is_deleted = 0 
                        and c.status_active in(1,2,3) and c.is_deleted = 0 $sql_cond and m.id in( $dtl_miss_id)";
                        //group by c.country_id,c.po_break_down_id
                        //echo $sql;
                        $result = sql_select($sql2);
                        $i = 1;
                        foreach ($result as $row) {
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30"><p><? echo $i; ?></p></td>
                                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $order_arr[$row[csf('po_break_down_id')]]; ?></div></td>
                                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $country_arr[$row[csf('country_id')]]; ?></div></td>
                                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $color_library[$row[csf('color_number_id')]]; ?></div></td>
                                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $size_library[$row[csf('size_number_id')]]; ?></div></td>
                                <td align="right">
                                    <p>
                                        <?  
                                            $production_without_return = $row[csf('production_qnty')] - $return_qnty;
                                            echo number_format($production_without_return,2); 
                                        ?>
                                    </p>
                                </td>
                            </tr>
                        <?
                        $total += number_format($production_without_return,2,".",""); 
                        $i++;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr class="tbl_bottom">
                            <td colspan="5" align="right">Total</td>
                            <td align="right"><? echo $total;?></td>
                        </tr>
                    </tfoot>
            </table>
        </div>
    </fieldset>
    <script type="text/javascript">
        setFilterGrid('table_body',-1);
    </script>
    <?
    exit();
}
?>
