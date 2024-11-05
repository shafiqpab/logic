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
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' 
	order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );     	 	
	exit();    	 
}

if($action=="report_generate") 
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$company_id=str_replace("'","",$cbo_company_id);
    $buyer_id=str_replace("'","",$cbo_buyer_id);
    $today_date=date("Y-m-d");
	$date_from=str_replace("'","",$txt_date_from);
    $date_to=str_replace("'","",$txt_date_to);
    $type=str_replace("'","",$type);
	$cbo_year_selection=str_replace("'","",$cbo_year_selection);
	if(str_replace("'","",$cbo_company_id)==0) 
        {
            $company_cond="";
            $ex_company = "";
        } 
        else {
            $company_cond=" and a.serving_company= $company_id";
            $ex_company = " and dm.delivery_company_id = $company_id";
        }
    if(str_replace("'","",$buyer_id)==0) $buyer_cond=""; else $buyer_cond=" and b.buyer_name= $buyer_id";
	if(str_replace("'","",$buyer_id)==0) $buyer_cond2=""; else $buyer_cond2=" and a.buyer_name= $buyer_id";
	if(str_replace("'","",$cbo_location_id)==0) 
	{
		$location_cond="";
	}
	else 
	{
		$location_cond=" and a.location=".str_replace("'","",$cbo_location_id)."";
	}
        
    if (str_replace("'", "", $txt_date_from) != "" || str_replace("'", "", $txt_date_to) != "") 
    {
		if ($db_type == 0) {
			$start_date = change_date_format(str_replace("'", "", $txt_date_from), "yyyy-mm-dd", "");
			$end_date = change_date_format(str_replace("'", "", $txt_date_to), "yyyy-mm-dd", "");
		} else if ($db_type == 2) {
			$start_date = change_date_format(str_replace("'", "", $txt_date_from), "", "", 1);
			$end_date = change_date_format(str_replace("'", "", $txt_date_to), "", "", 1);
		}
		$date_cond = " and a.production_date between '$start_date' and '$end_date'";
		$date_cond_ex = " and m.ex_factory_date between '$start_date' and '$end_date'";
	}
    else
    {
        if($db_type == 0)
        {
            $year_cond = " and year(m.ex_factory_date) = '$cbo_year_selection'";
            $production_year_cond = " and year(a.production_date) = '$cbo_year_selection'";
        }else{
            $year_cond = " and to_char(m.ex_factory_date,'YYYY') = '$cbo_year_selection'";
            $production_year_cond = " and to_char(a.production_date,'YYYY') = '$cbo_year_selection'";
        }
        
        
    }
        
    if($type == 1)
    {
        $sql_ex = "SELECT  m.id,dm.delivery_company_id,dm.delivery_location_id, a.buyer_name,  m.ex_factory_qnty as ex_qnty,d.production_qnty,
        m.po_break_down_id, c.order_rate*d.production_qnty as ex_value
        from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_color_size_breakdown c,pro_ex_factory_dtls d
        where dm.id = m.delivery_mst_id 
        and a.job_no = c.job_no_mst
        and m.id = d.mst_id 
        and d.color_size_break_down_id = c.id     
        and m.entry_form <> 85 

        and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 
        and m.status_active = 1 and m.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0
        $date_cond_ex
        $ex_company
        order by m.id";
        $ex_result = sql_select($sql_ex); $data_ex_arr = array();
        foreach($ex_result as $row)
        {
            $data_ex_arr[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_qnty'] += $row[csf("production_qnty")];
            $data_ex_arr[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_value'] += $row[csf("ex_value")];
            $po_id .= $row[csf("po_break_down_id")].",";
            
        }
        unset($ex_result);
        
        //$po_id = implode(",",array_filter(array_unique(explode(",",chop($po_id,",")))));
        
        $po_id_cond = "";
        if ($po_id != "") {
            $po_id = substr($po_id, 0, -1);
            if ($db_type == 0)
                $po_id_cond = "and m.po_break_down_id in(" . $po_id . ")";
            else {
                $po_ids = explode(",", $po_id);
                if (count($po_ids) > 1000) {
                    $po_id_cond = "and (";
                    $po_ids = array_chunk($po_ids, 1000);
                    $z = 0;
                    foreach ($po_ids as $id) {
                        $id = implode(",", $id);
                        if ($z == 0)
                            $po_id_cond .= " m.po_break_down_id in(" . $id . ")";
                        else
                            $po_id_cond .= " or m.po_break_down_id in(" . $id . ")";
                        $z++;
                    }
                    $po_id_cond .= ")";
                } else
                    $po_id_cond = " and m.po_break_down_id in(" . $po_id . ")";
            }
        }
        
        
        $sql_ex_return = "SELECT  m.id,dm.company_id,dm.location_id, a.buyer_name,  m.ex_factory_qnty as ex_qnty,d.production_qnty,
        m.po_break_down_id, c.order_rate*d.production_qnty as ex_value, 2 as type
        from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_color_size_breakdown c,pro_ex_factory_dtls d
        where dm.id = m.delivery_mst_id 
        and a.job_no = c.job_no_mst
        and m.id = d.mst_id 
        and d.color_size_break_down_id = c.id   
        and m.entry_form = 85 

        and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 
        and m.status_active = 1 and m.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0
        $po_id_cond
        order by m.id";
        $ex_return_result = sql_select($sql_ex_return); $data_ex_return_arr = array();
        foreach($ex_return_result as $row)
        {
            $data_ex_return_arr[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("buyer_name")]]['ex_return_qnty'] += $row[csf("production_qnty")];
            $data_ex_return_arr[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("buyer_name")]]['ex_return_value'] += $row[csf("ex_value")];
           
        }
        unset($ex_return_result);

        $sqlManHour = "SELECT a.id as line_ref,a.company_id, a.location_id,a.floor_id,a.line_number, b.man_power, b.working_hour,b.from_date,b.to_date
        from prod_resource_mst a, prod_resource_dtls_mast b
        where a.id = b.mst_id
        and b.is_deleted = 0
        and a.is_deleted = 0
        and b.from_date >= '$start_date'
        and b.to_date <= '$end_date'";
        $result = sql_select($sqlManHour); $ManHourArr = array();
        foreach($result as $row)
        {
            $ManHourArr[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_id")]][$row[csf("line_ref")]]['worker_no'] +=$row[csf("man_power")];
            $ManHourArr[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_id")]][$row[csf("line_ref")]]['work_hour'] +=$row[csf("working_hour")];
        }
        unset($result);
        //        echo "<pre>";
        //        print_r($ManHourArr);
        //        echo "</pre>";
        //        die;
        
    
     $sql = "SELECT a.serving_company, a.location,b.buyer_name,a.po_break_down_id,c.po_number,
        d.production_qnty, a.id,a.floor_id,a.sewing_line,e.order_rate*d.production_qnty as production_value, a.production_source 
        from wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a, pro_garments_production_dtls d, wo_po_color_size_breakdown e
        where b.job_no = c.job_no_mst 
        and c.id = a.po_break_down_id 
        and a.id = d.mst_id and a.production_type=5 and d.production_type=5 
        and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id 
        and a.status_active = 1 and a.is_deleted = 0
        and b.status_active = 1 and b.is_deleted = 0
        and c.status_active = 1 and c.is_deleted = 0
        and d.status_active = 1 and d.is_deleted = 0
        and d.production_qnty is not null and d.production_qnty <> 0 
        
        and a.production_source = 1
        $company_cond
        $location_cond
        $buyer_cond
        $date_cond 
        union all 
        select a.serving_company,a.location,b.buyer_name,a.po_break_down_id,c.po_number, 
        d.production_qnty, a.id,a.floor_id,a.sewing_line, e.order_rate*d.production_qnty as production_value, a.production_source 
        from wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a, pro_garments_production_dtls d, wo_po_color_size_breakdown e 
        where b.job_no = c.job_no_mst and c.id = a.po_break_down_id and a.id = d.mst_id and a.production_type=5 and d.production_type=5 and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id
        and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0
        and d.production_qnty is not null and d.production_qnty <> 0 and a.production_source = 3
        $date_cond 
        ";
        $nameArray = sql_select($sql);
        $dataArr = array();$OutBoundArr=array();
        foreach($nameArray as $row)
        {
            if($row[csf("production_source")] == 1)
            {
                $dataArr[$row[csf("serving_company")]][$row[csf("location")]][$row[csf("buyer_name")]]["qnty"] += $row[csf("production_quantity")];           
                $sourceArr[$row[csf("serving_company")]][$row[csf("location")]][$row[csf("buyer_name")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]["qnty"] += $row[csf("production_qnty")];
                $sourceArr[$row[csf("serving_company")]][$row[csf("location")]][$row[csf("buyer_name")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]["production_value"] += $row[csf("production_value")];
            }
            else if($row[csf("production_source")] == 3)
            {
                $OutBoundArr[$row[csf("serving_company")]]["production_qnty"] +=  $row[csf("production_qnty")];
                $OutBoundArr[$row[csf("serving_company")]]["production_value"] +=  $row[csf("production_value")];
            }
        }
        unset($nameArray);
        $DataArray = array();
        foreach($sourceArr as $companyId => $companyData)
        {
            foreach($companyData as $locationId=>$locationData)
            {
                foreach($locationData as $buyerId =>$buyerData)
                {
					$ex_qnty = $data_ex_arr[$companyId][$locationId][$buyerId]['ex_qnty'];
					$ex_value = $data_ex_arr[$companyId][$locationId][$buyerId]['ex_value'];
					$ex_return_qnty = $data_ex_return_arr[$companyId][$locationId][$buyerId]['ex_return_qnty'];
					$ex_return_value = $data_ex_return_arr[$companyId][$locationId][$buyerId]['ex_return_value'];
					$qnty = $ex_qnty- $ex_return_qnty;
					$value = $ex_value - $ex_return_value;
                    foreach($buyerData as $floorId=>$floorData)
                    {
                        foreach($floorData as $lineId=>$row)
                        {
                            //$rate = $value/$qnty;
                            $worker_no = $ManHourArr[$companyId][$locationId][$floorId][$lineId]['worker_no'];
                            $work_hour = $ManHourArr[$companyId][$locationId][$floorId][$lineId]['work_hour'];
                            $DataArray[$companyId][$locationId][$buyerId]["qnty"] += $row["qnty"];
                            $DataArray[$companyId][$locationId][$buyerId]["production_value"] += $row["production_value"];
                            $DataArray[$companyId][$locationId][$buyerId]["worker_no"] += $worker_no;
                            $DataArray[$companyId][$locationId][$buyerId]["work_hour"] += $work_hour;
                            $DataArray[$companyId][$locationId][$buyerId]["ex_qnty"] += $qnty;
                            $DataArray[$companyId][$locationId][$buyerId]["ex_value"] += $value;
							$qnty=$value=$rate=0;
                        }
                    }
                }
            }
        }
        
	?>
    
    <? ob_start(); ?>
    <fieldset style="width: 1320px;font-family: 'Arial Narrow', Arial, sans-serif;" align="center">
        <table width="1300" cellpadding="0" cellspacing="0"> 
            <tr class="form_caption">
                <td colspan="<?php echo $col_span; ?>" align="center"><strong><? echo $companyArr[$company_id]; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="<?php echo $col_span; ?>" align="center"><strong><? echo "Export & Production Summary".":".$txt_date_from." to ".$txt_date_to; ?></strong></td> 
            </tr>
            
        </table>
    	<table class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="40">SL</th>
                    <th width="100">COMPANY</th>
                    <th width="130">LOCATION</th>
                    <th width="120">Buyer</th>
                    <th width="100">Export(Pcs)</th>
                    <th width="100">Average Price($)</th>
                    <th width="100">Export Value($)</th>
                    <th width="100">Worker</th>
                    <th width="100">Sewing Prod. Hour</th>
                    <th width="100">Sewing Production</th>
                    <th width="100">Excluding 5% Sewing Production</th>
                    <th width="100">Average Price($)</th>
                    <th width="">Excluding 5% Sewing Value($)</th>
                </tr>
            </thead>
        </table>
    	<div style="width:1300px; max-height:350px;" id="scroll_body" align="center"> 
            <table class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tbody>
                    <? 
                    $buyer_row_span_arr = array();
                    foreach($DataArray as $company_id => $company_data)
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
                    $i =1;
                    foreach($DataArray as $companyId => $companyData)
                    {
                        foreach($companyData as $locationId => $locationData)
                        {
                            $y=1;
                            foreach($locationData as $buyerId => $row)
                            {
                                if ($m % 2 == 0)
                                $bgcolor = "#E9F3FF";
                                else
                                $bgcolor = "#FFFFFF";
                                $buyer_td_span = $buyer_row_span_arr[$companyId."*".$locationId];
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
                                <?
                                if($y == 1)
                                {
                                    ?>
                                    <td width="40" align="center" rowspan="<? echo $buyer_td_span;?>"><? echo $i;?></td>
                                    <td width="100" rowspan="<? echo $buyer_td_span;?>"><p><? echo $companyArr[$companyId];?></p></td>
                                    <td width="130" rowspan="<? echo $buyer_td_span;?>"><p><? echo $locationArr[$locationId];?></p></td>
                                    <?
                                }
                                ?>
                                    <td width="120"><? echo $buyerArr[$buyerId];?></td>
                                    <td width="100" align="right"><? echo number_format($row["ex_qnty"],2,".","");?></td>
                                    <td width="100" align="right"><? if($row["ex_qnty"]){ echo number_format($row["ex_value"]/$row["ex_qnty"],2);} else {echo "0";}?></td>
                                    <td width="100" align="right"><? echo number_format($row["ex_value"],2,".","");?></td>
                                    <td width="100" align="right"><? echo $row["worker_no"];?></td>
                                    <td width="100" align="right"><? echo $row["work_hour"];?></td>
                                    <td width="100" align="right"><? echo $row["qnty"];?></td>
                                    <td width="100" align="right"><? echo number_format(($row["qnty"]*95)/100,2,".","");?></td>
                                    <td width="100" align="right"><? echo number_format(($row["production_value"]*95)/($row["qnty"]*95),2,".","");?></td>
                                    <td align="right"><? echo number_format(($row["production_value"]*95)/100,2,".","");?></td>
                                </tr>
                                <?
                                $y++;$m++;
                                $sub_ex_qnty += $row["ex_qnty"];
                                $sub_ex_value += $row["ex_value"];
                                $sub_worker_no += $row["worker_no"];
                                $sub_work_hour += $row["work_hour"];
                                $sub_prod_qnty += $row["qnty"];
                                $exclu_five_persant +=($row["qnty"]*95)/100;
                                $exclu_five_persant_value += ($row["production_value"]*95)/100;
                                
                                $grand_ex_qnty += $row["ex_qnty"];
                                $grand_ex_value += $row["ex_value"];
                                $grand_worker_no += $row["worker_no"];
                                $grand_work_hour += $row["work_hour"];
                                $grand_prod_qnty += $row["qnty"];
                                $grand_exclu_five_persant +=($row["qnty"]*95)/100;
                                $grand_exclu_five_persant_value += ($row["production_value"]*95)/100;
                            }
                            ?>
                            <tr style="background-color:#e0e0e0; font-weight: bold">
                            <td colspan="4" align="right" ><b>Sub Total</b></td>
                            <td align="right">&nbsp;<? echo number_format($sub_ex_qnty,2,".","");?></td>
                            <td align="right">&nbsp;<? if($sub_ex_qnty){ echo number_format($sub_ex_value/$sub_ex_qnty,2,".","");} else {echo "0";}?></td>
                            <td align="right">&nbsp;<? echo number_format($sub_ex_value,2,".","");?></td>
                            <td align="right">&nbsp;<? echo number_format($sub_worker_no,2,".","");?></td>
                            <td align="right">&nbsp;<? echo number_format($sub_work_hour,2,".","");?></td>
                            <td align="right">&nbsp;<? echo number_format($sub_prod_qnty,2,".","");?></td>
                            <td align="right">&nbsp;<? echo number_format($exclu_five_persant,2,".","");?></td>
                            <td>&nbsp;</td>
                            <td align="right">&nbsp;<? echo number_format($exclu_five_persant_value,2,".","");?></td>
                            </tr>
                             <?  
                             $i++;
                             $sub_ex_qnty=$sub_ex_value=$sub_worker_no=$sub_work_hour=$sub_prod_qnty=$exclu_five_persant=$exclu_five_persant_value=0;
                        }
                    }
                    if(count($OutBoundArr) > 0)
                    {
                        $supplierArr = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name")
                        ?>
                        <tr >
                            <td colspan="3" align="right"><p><b>Subcontract(Out-bound)</b></p></td>
                            <td></td>
                            <td align="right"></td>
                            <td align="right"></td>
                            <td align="right"></td>
                            <td align="right"></td>
                            <td align="right"></td>
                            <td align="right"></td>
                            <td align="right"></td>
                            <td align="right"></td>
                            <td align="right"></td>
                        </tr>
                        <?
                        foreach($OutBoundArr as $supplierId=> $row)
                        {
                            if ($m % 2 == 0)
                                $bgcolor = "#E9F3FF";
                                else
                                $bgcolor = "#FFFFFF";
                            ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
                                    <td align="center"><? echo $i;?></td>
                                    <td colspan="2"></td>
                                    <td title="<? echo $supplierId;?>"><? echo $supplierArr[$supplierId];?></td>
                                    <td colspan="5" align="right"></td>

                                    <td align="right"><? echo $row["production_qnty"];?></td>
                                    <td align="right"><? echo number_format(($row["production_qnty"]*95)/100,2,".","");?></td>
                                    <td align="right"><? echo number_format(($row["production_value"]*95)/($row["production_qnty"]*95),2,".","");?></td>
                                    <td align="right"><? echo number_format(($row["production_value"]*95)/100,2,".","");?></td>
                                </tr>

                            <?
                            $i++;$m++;
                            
                            $sub_out_prod_qnty += $row["production_qnty"];
                            $sub_out_exclu_five_persant +=($row["production_qnty"]*95)/100;
                            $sub_out_exclu_five_persant_value += ($row["production_value"]*95)/100;
                            
                            $grand_prod_qnty += $row["production_qnty"];
                            $grand_exclu_five_persant +=($row["production_qnty"]*95)/100;
                            $grand_exclu_five_persant_value += ($row["production_value"]*95)/100;
                        }
                    }
                                        ?>
                        <tr style="background-color:#e0e0e0; font-weight: bold">
                            <td colspan="4" align="right"><p><b>Sub Total</b></p></td>
                            <td align="right"></td>
                            <td align="right"></td>
                            <td align="right"></td>
                            <td align="right"></td>
                            <td align="right"></td>
                            <td align="right"><? echo number_format($sub_out_prod_qnty,2,".","");?></td>
                            <td align="right"><? echo number_format($sub_out_exclu_five_persant,2,".","");?></td>
                            <td align="right"><? //echo number_format($sub_out_exclu_five_persant_value/$sub_out_exclu_five_persant,2,".","");?></td>
                            <td align="right"><? echo number_format($sub_out_exclu_five_persant_value,2,".","");?></td>
                        </tr>
                                            
                        <tr style="background-color:#e0e0e0; font-weight: bold">
                            <td colspan="4" align="right"><p><b>Grand Total</b></p></td>
                            <td align="right"><? echo number_format($grand_ex_qnty,2,".","");?></td>
                            <td align="right"><? if($grand_ex_qnty){ echo number_format($grand_ex_value/$grand_ex_qnty,2,".","");} else {echo "0";}?></td>
                            <td align="right"><? echo number_format($grand_ex_value,2,".","");?></td>
                            <td align="right"><? echo number_format($grand_worker_no,2,".","");?></td>
                            <td align="right"><? echo number_format($grand_work_hour,2,".","");?></td>
                            <td align="right"><? echo number_format($grand_prod_qnty,2,".","");?></td>
                            <td align="right"><? echo number_format($grand_exclu_five_persant,2,".","");?></td>
                            <td align="right"><? //echo number_format($grand_exclu_five_persant_value/$grand_exclu_five_persant,2,".","");?></td>
                            <td align="right"><? echo number_format($grand_exclu_five_persant_value,2,".","");?></td>
                        </tr>
               </tbody> 
            </table>
        </div>
    </fieldset>
    <?    
    }
    else if($type == 2)
    {
        if(str_replace("'","",$cbo_location_id)==0) 
        {
            $location_cond="";
        }
        else 
        {
            $location_cond=" and dm.delivery_location_id=".str_replace("'","",$cbo_location_id)."";
        }

        $sql_ex_return = "SELECT  m.id,dm.sys_number,dm.company_id,dm.location_id, a.buyer_name,m.ex_factory_date,  m.ex_factory_qnty as ex_qnty,d.production_qnty,
        m.po_break_down_id, c.order_rate*d.production_qnty as ex_value , dm.challan_no
        from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_color_size_breakdown c,pro_ex_factory_dtls d
        where dm.id = m.delivery_mst_id 
        and a.job_no = c.job_no_mst
        and m.id = d.mst_id 
        and d.color_size_break_down_id = c.id   
        and m.entry_form = 85 
        and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 
        and m.status_active = 1 and m.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0
        $buyer_cond2
        order by m.id";
        $ex_return_result = sql_select($sql_ex_return); $data_ex_return_arr = array();
        foreach($ex_return_result as $row)
        {
            $return_qnty_challan_arr[$row[csf("challan_no")]]["return_qnty"] += $row[csf("production_qnty")];
            $return_qnty_challan_arr[$row[csf("challan_no")]]["return_value"] += $row[csf("ex_value")];
           
        }
        unset($ex_return_result);

        $sql_country_w="select dm.sys_number,dm.delivery_company_id,dm.delivery_location_id,m.ex_factory_date, a.buyer_name,sum(d.production_qnty) as ex_qnty,sum(c.order_rate*d.production_qnty) as ex_value
        from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_color_size_breakdown c, pro_ex_factory_dtls d
        where dm.id = m.delivery_mst_id 
        and a.job_no = c.job_no_mst and m.id = d.mst_id 
        and d.color_size_break_down_id = c.id   and m.entry_form <> 85 
        and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 
        and m.status_active = 1 and m.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0
        $date_cond_ex $ex_company $location_cond $buyer_cond2 $year_cond
        group by dm.sys_number,dm.delivery_company_id,dm.delivery_location_id,m.ex_factory_date, a.buyer_name
        order by m.ex_factory_date, dm.sys_number desc";

        $country_w_res = sql_select($sql_country_w);
        foreach($country_w_res as $row)
        {

            $header_arr[date("M",strtotime($row[csf("ex_factory_date")]))."-".date("y",strtotime($row[csf("ex_factory_date")]))] = date("M",strtotime($row[csf("ex_factory_date")]))."-".date("y",strtotime($row[csf("ex_factory_date")]));

            $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]][date("M",strtotime($row[csf("ex_factory_date")]))."-".date("y",strtotime($row[csf("ex_factory_date")]))]['ex_qnty'] += ($row[csf("ex_qnty")] - $return_qnty_challan_arr[$row[csf("sys_number")]]["return_qnty"]);

            $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]][date("M",strtotime($row[csf("ex_factory_date")]))."-".date("y",strtotime($row[csf("ex_factory_date")]))]['ex_value'] += ($row[csf("ex_value")] - $return_qnty_challan_arr[$row[csf("sys_number")]]["return_value"]);

            $buyer_wise_arr[$row[csf("buyer_name")]][date("M",strtotime($row[csf("ex_factory_date")]))."-".date("y",strtotime($row[csf("ex_factory_date")]))]['ex_qnty'] += ($row[csf("ex_qnty")] - $return_qnty_challan_arr[$row[csf("sys_number")]]["return_qnty"]);

            $buyer_wise_arr[$row[csf("buyer_name")]][date("M",strtotime($row[csf("ex_factory_date")]))."-".date("y",strtotime($row[csf("ex_factory_date")]))]['ex_value'] += ($row[csf("ex_value")] - $return_qnty_challan_arr[$row[csf("sys_number")]]["return_value"]);

            $buyer_wise_qnty_tot += ($row[csf("ex_qnty")] - $return_qnty_challan_arr[$row[csf("sys_number")]]["return_qnty"]);
            $buyer_wise_value_tot += ($row[csf("ex_value")] - $return_qnty_challan_arr[$row[csf("sys_number")]]["return_value"]);
        }
    

        //print_r($return_qnty_challan_arr);die;

        ob_start(); ?>

    <fieldset style="width: <? echo 380+ count($header_arr)*120?>px; font-family: 'Arial Narrow', Arial, sans-serif;" align="left">
        <table style="width: <? echo 350+ count($header_arr)*120?>px;text-align: center;" cellpadding="0" cellspacing="0" align="center"> 
            <tr class="form_caption">
                <td colspan="<? echo 3 + count($header_arr)?>" align="center"><strong><? echo $companyArr[$company_id]; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="<? echo 3 + count($header_arr); ?>" align="center">
                    <strong>
                        <? 
                            if (str_replace("'", "", $txt_date_from) != "" && str_replace("'", "", $txt_date_to) != "") 
                            {
                                echo "Yearly Export Summary From : $txt_date_from to $txt_date_to"; 
                            }
                            else{
                                echo "Yearly Export Summary With Value : $cbo_year_selection"; 
                            }
                            
                        ?>
                    </strong>
                </td> 
            </tr>
            
        </table>
        <table class="rpt_table" style="width: <? echo 350+ count($header_arr)*140?>px;" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <thead>
                <tr>
                    <th width="120" rowspan="2">COMPANY</th>
                    <th width="130" rowspan="2">LOCATION</th>
                    <th width="100" rowspan="2">Buyer</th>
                    <? 
                        foreach ($header_arr as $key => $value) 
                        {
                            ?>
                            <th width="120" colspan="2"><? echo $value;?></th>
                            <?
                        }
                    ?>
                </tr>
                <tr>
                    <? 
                        foreach ($header_arr as $key => $value) 
                        {
                            ?>
                            <th width="70">Qnty</th>
                            <th width="70">Value</th>
                            <?
                        }
                    ?>
                </tr>
            </thead>
        </table>
        <div style="width:<? echo 368+ count($header_arr)*140?>px; max-height:350px; overflow-y: scroll;" id="scroll_body" align="left"> 
            <table class="rpt_table" style="width: <? echo 350+ count($header_arr)*140?>px;" cellpadding="0" cellspacing="0" border="1" rules="all" align="left";>
                <tbody>
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
                    $i =1;  $sub_total_arr=array(); $grand_total_arr = array();
                    foreach($result_array as $companyId => $companyData)
                    {
                        foreach($companyData as $locationId => $locationData)
                        {
                            $y=1;
                            foreach($locationData as $buyerId => $row)
                            {
                                if ($m % 2 == 0)
                                $bgcolor = "#E9F3FF";
                                else
                                $bgcolor = "#FFFFFF";
                                $buyer_td_span = $buyer_row_span_arr[$companyId."*".$locationId];
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
                                <?
                                if($y == 1)
                                {
                                    ?>
                                    <td width="120" rowspan="<? echo $buyer_td_span;?>"><p><? echo $companyArr[$companyId];?></p></td>
                                    <td width="130" rowspan="<? echo $buyer_td_span;?>"><p><? echo $locationArr[$locationId];?></p></td>
                                    <?
                                }
                                ?>
                                    <td width="100"><p><? echo $buyerArr[$buyerId];?></p></td>

                                    <? 
                                        foreach($header_arr as $m_y)
                                        {
                                            
                                            ?>
                                            <td width="70" align="right"><p><? echo number_format($row[$m_y]["ex_qnty"]); ?></p></td>
                                            <td width="70" align="right"><p><? echo number_format($row[$m_y]["ex_value"],2);?></p></td>
                                            <?
                                            $sub_total_arr[$companyId."*".$locationId."*".$m_y]["qnty"] += $row[$m_y]["ex_qnty"]; 
                                            $sub_total_arr[$companyId."*".$locationId."*".$m_y]["value"] += $row[$m_y]["ex_value"]; 
                                            $grand_total_arr[$m_y]["qnty"] += $row[$m_y]["ex_qnty"]; 
                                            $grand_total_arr[$m_y]["value"] += $row[$m_y]["ex_value"]; 
                                        }
                                    ?>
                                   
                                </tr>
                                <?
                                $y++;$m++;
                               
                            }
                            ?>
                            <tr style="background-color:#e0e0e0; font-weight: bold">
                            <td colspan="3" align="right" width="350"><b>Total</b></td>
                            <? 
                                foreach($header_arr as $m_y_f){
                                    ?>
                                    <td align="right" width="70"><p>&nbsp;<? echo number_format($sub_total_arr[$companyId."*".$locationId."*".$m_y_f]['qnty'],2,".","");?></p></td>
                                    <td align="right" width="70"><p>&nbsp;<? echo number_format($sub_total_arr[$companyId."*".$locationId."*".$m_y_f]['value'],2,".","");?></p></td>
                                    <?
                                }
                            ?>
                            
                            </tr>
                             <?  
                             $i++;
                             
                             $sub_total_arr=array();
                        }
                    }
                    ?>
               </tbody> 
            </table>
        </div>
        <table class="rpt_table" style="width: <? echo 350+ count($header_arr)*140?>px;" cellpadding="0" cellspacing="0" border="1" rules="all" align="left";>
            <tfoot>
               <tr>
                   <th align="right" width="120"><b></b></th>
                   <th align="right" width="130"><b></b></th>
                   <th align="right" width="100"><b>Grand Total </b></th>
                    <? 
                            foreach($header_arr as $m_y_f){
                                ?>
                                <th align="right" width="70">&nbsp;<p><? echo number_format($grand_total_arr[$m_y_f]['qnty'],2,".","");?></p></th>
                                <th align="right" width="70">&nbsp;<p><? echo number_format($grand_total_arr[$m_y_f]['value'],2,".","");?></p></th>
                                <?
                            }
                        ?>
               </tr>
            </tfoot>
        </table>
    </fieldset>
    <br>
    <fieldset style="width: <? echo 420+ count($header_arr)*120?>px; font-family: 'Arial Narrow', Arial, sans-serif;" align="left">
        <h3>Buyer Wise Summary</h3>
        <table class="rpt_table" style="width: <? echo 400+ count($header_arr)*140?>px;" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <thead>
                <tr>
                    <th width="100" rowspan="2">Buyer</th>
                    <? 
                        foreach ($header_arr as $key => $value) 
                        {
                            ?>
                            <th width="120" colspan="2"><? echo $value;?></th>
                            <?
                        }
                    ?>
                    <th width="180" colspan="3">Total</th>
                    <th width="120" colspan="2">Average</th>
                </tr>
                <tr>
                    <? 
                        foreach ($header_arr as $key => $value) 
                        {
                            ?>
                            <th width="70">Qnty</th>
                            <th width="70">Value</th>
                            <?
                        }
                    ?>
                    <th width="60">Export Qty</th>
                    <th width="60">Avg</th>
                    <th width="60">Export Value</th>
                    <th width="60">Qty %</th>
                    <th width="60">Value %</th>
                </tr>
            </thead>
        </table>
        <div style="width:<? echo 418+ count($header_arr)*140?>px; max-height:350px; overflow-y: scroll;" id="scroll_body2" align="left"> 
            <table class="rpt_table" style="width: <? echo 400+ count($header_arr)*140?>px;" cellpadding="0" cellspacing="0" border="1" rules="all" align="left";>
                <tbody>
                    <?
                    $j=1;$grand_total = array();
                    foreach ($buyer_wise_arr as $buyer_id => $row) 
                    {
                        if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('trb_<? echo $j; ?>', '<? echo $bgcolor; ?>')" id="trb_<? echo $j; ?>">
                        
                        <td width="100"><? echo $buyerArr[$buyer_id];?></td>
                        <?
                        $row_total_qnty=$row_total_value=$avg=0;
                        foreach ($header_arr as $m_y_val) 
                        {
                            ?>
                            <td width="70" align="right"><p><? echo number_format($row[$m_y_val]["ex_qnty"]);?></p></td>
                            <td width="70" align="right"><p><? echo number_format($row[$m_y_val]["ex_value"],2);?></p></td>
                            <?
                            $row_total_qnty += $row[$m_y_val]["ex_qnty"];
                            $row_total_value += $row[$m_y_val]["ex_value"];
                            $grand_total[$m_y_val]["qty"] += $row[$m_y_val]["ex_qnty"];
                            $grand_total[$m_y_val]["val"] += $row[$m_y_val]["ex_value"];
                        }
                        $avg = $row_total_value/$row_total_qnty;
                        $total_percen_qnty += ($row_total_qnty/$buyer_wise_qnty_tot)*100;
                        $total_percen_value += ($row_total_value/$buyer_wise_value_tot)*100;

                        $total_ex_qnty +=$row_total_qnty;
                        $total_ex_value +=$row_total_value;

                        ?>
                        <td width="60" align="right"><p><? echo number_format($row_total_qnty,2,".","");?></p></td>
                        <td width="60" align="right"><p><? echo number_format($avg,2,".","");?></p></td>
                        <td width="60" align="right"><p><? echo number_format($row_total_value,2,".","");?></p></td>
                        <td width="60" align="right"><p><? echo number_format(($row_total_qnty/$buyer_wise_qnty_tot)*100,2)?></p></td>
                        <td width="60" align="right"><p><? echo number_format(($row_total_value/$buyer_wise_value_tot)*100,2)?></p></td>                                
                        </tr>
                        <?
                        $j++;
                    }
                        ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th width="100">Total</th>
                        <? 
                        foreach ($header_arr as $myv) {
                            ?>
                            <th width="70" align="right"><p><? echo number_format($grand_total[$myv]["qty"]);?></p></th>
                            <th width="70" align="right"><p><? echo number_format($grand_total[$myv]["val"],2);?></p></th>
                            <?
                        }
                        $total_avg = $total_ex_value/$total_ex_qnty;
                        ?>
                        <th width="60" align="right"><p><? echo number_format($total_ex_qnty,2)?></p></th>
                        <th width="60" align="right"><p><? echo number_format($total_avg,2)?></p></th>
                        <th width="60" align="right"><p><? echo number_format($total_ex_value,2)?></p></th>
                        <th width="60" align="right"><p><? echo number_format($total_percen_qnty,2)?></p></th>
                        <th width="60" align="right"><p><? echo number_format($total_percen_value,2)?></p></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <br>


        <?

    }
    else if ($type == 3) 
    {

        $sql_ex_return = "SELECT  m.id,dm.company_id,dm.location_id, a.buyer_name, d.production_qnty,
        m.po_break_down_id, c.order_rate*d.production_qnty as ex_value,dm.challan_no
        from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_color_size_breakdown c,pro_ex_factory_dtls d
        where dm.id = m.delivery_mst_id 
        and a.job_no = c.job_no_mst
        and m.id = d.mst_id 
        and d.color_size_break_down_id = c.id   
        and m.entry_form = 85 

        and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 
        and m.status_active = 1 and m.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0

        order by m.id";
        $ex_return_result = sql_select($sql_ex_return); $data_ex_return_arr = array();
        foreach($ex_return_result as $row)
        {
            $return_qnty_challan_arr[$row[csf("challan_no")]]["return_qnty"] += $row[csf("production_qnty")];
            $return_qnty_challan_arr[$row[csf("challan_no")]]["return_value"] += $row[csf("ex_value")];
           
        }
        unset($ex_return_result);


        $sql_ex="select dm.sys_number,dm.delivery_company_id,dm.delivery_location_id,m.ex_factory_date, a.buyer_name,sum(d.production_qnty) as ex_qnty,sum(c.order_rate*d.production_qnty) as ex_value
        from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_color_size_breakdown c, pro_ex_factory_dtls d
        where dm.id = m.delivery_mst_id 
        and a.job_no = c.job_no_mst and m.id = d.mst_id 
        and d.color_size_break_down_id = c.id   and m.entry_form <> 85 
        and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 
        and m.status_active = 1 and m.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0
        $date_cond_ex $ex_company $location_cond $buyer_cond2 $year_cond
        group by dm.sys_number,dm.delivery_company_id,dm.delivery_location_id,m.ex_factory_date, a.buyer_name
        order by m.ex_factory_date, dm.sys_number ";


        $ex_result = sql_select($sql_ex); $data_ex_arr = array();
        foreach($ex_result as $row)
        {

            $all_date_arr[] = $row[csf("ex_factory_date")];
            $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]][date("M",strtotime($row[csf("ex_factory_date")]))."-".date("y",strtotime($row[csf("ex_factory_date")]))]['ex_qnty'] += ($row[csf("ex_qnty")] - $return_qnty_challan_arr[$row[csf("sys_number")]]["return_qnty"]);

            $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]][date("M",strtotime($row[csf("ex_factory_date")]))."-".date("y",strtotime($row[csf("ex_factory_date")]))]['ex_value'] += ($row[csf("ex_value")] - $return_qnty_challan_arr[$row[csf("sys_number")]]["return_value"]);
            $grand_total_ex_qnty += ($row[csf("ex_qnty")] - $return_qnty_challan_arr[$row[csf("sys_number")]]["return_qnty"]);
        }
        unset($ex_result);
    
     /*$sql = "SELECT a.serving_company, a.location,b.buyer_name,a.po_break_down_id,c.po_number,a.production_date,d.production_qnty, a.id,a.floor_id,a.sewing_line,e.order_rate*d.production_qnty as production_value, a.production_source 
        from wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a, pro_garments_production_dtls d, wo_po_color_size_breakdown e
        where b.job_no = c.job_no_mst 
        and c.id = a.po_break_down_id 
        and a.id = d.mst_id 
        and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id 
        and e.job_no_mst = c.job_no_mst
        and a.production_type=5 and d.production_type=5 
        and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
        and c.status_active = 1 and c.is_deleted = 0
        and d.status_active = 1 and d.is_deleted = 0
        and d.production_qnty is not null and d.production_qnty <> 0 
        $company_cond
        $location_cond
        $buyer_cond
        $date_cond 
        $production_year_cond
        order by a.production_date 
        ";*/

        $sql = "SELECT a.serving_company, a.location,
        b.buyer_name, a.po_break_down_id,a.production_date,d.production_qnty, a.id,a.floor_id,a.sewing_line,e.order_rate*d.production_qnty as production_value, 
        a.production_source
        from pro_garments_production_mst a, pro_garments_production_dtls d, wo_po_color_size_breakdown e,wo_po_details_master b
        where a.id = d.mst_id and d.color_size_break_down_id = e.id and e.job_no_mst=b.job_no and a.po_break_down_id=e.po_break_down_id 
        and a.production_type=5 and d.production_type=5 and a.status_active = 1 and a.is_deleted = 0 
        and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 and d.production_qnty is not null and d.production_qnty <> 0 
        $company_cond $location_cond $buyer_cond $date_cond $production_year_cond
        order by a.production_date";

        $nameArray = sql_select($sql);
        $dataArr = array();$OutBoundArr=array();

        foreach($nameArray as $row)
        {
            $all_date_arr[] = $row[csf("production_date")];
            $result_array[$row[csf("serving_company")]][$row[csf("location")]][$row[csf("buyer_name")]][date("M",strtotime($row[csf("production_date")]))."-".date("y",strtotime($row[csf("production_date")]))]["sewing_qnty"] += $row[csf("production_qnty")]; 
            //$result_array[$row[csf("serving_company")]][$row[csf("location")]][$row[csf("buyer_name")]][date("M",strtotime($row[csf("production_date")]))."-".date("y",strtotime($row[csf("production_date")]))]["sewing_value"] += $row[csf("production_value")];   
        }
        unset($nameArray);

        $DataArray = array();
        foreach($result_array as $companyId => $companyData)
        {
            foreach($companyData as $locationId=>$locationData)
            {

                $buyer_row_span= 0;
                foreach($locationData as $buyer_id => $row)
                {
                    $buyer_row_span++;
                }
                $buyer_row_span_arr[$companyId."*".$locationId] =$buyer_row_span;
            }
        }

        // For ascending month and year 
            function date_sort($a, $b) {
                return strtotime($a) - strtotime($b);
            }
            usort($all_date_arr, "date_sort");

            foreach ($all_date_arr as $key => $val) 
            {
               $header_arr[date("M",strtotime($val))."-".date("y",strtotime($val))] = date("M",strtotime($val))."-".date("y",strtotime($val));
            }


        ob_start(); ?>

    <fieldset style="width: <? echo 620+ count($header_arr)*180?>px; font-family: 'Arial Narrow', Arial, sans-serif;" align="left">
        <table style="width: <? echo 590+ count($header_arr)*180?>px;text-align: center;" cellpadding="0" cellspacing="0" align="center"> 
            <tr class="form_caption">
                <td colspan="<? echo 5 + count($header_arr)?>" align="center"><strong><? echo $companyArr[$company_id]; ?></strong></td> 
            </tr>
            <tr class="form_caption">
                <td colspan="<? echo 5 + count($header_arr); ?>" align="center">
                    <strong>
                        <? 
                            if (str_replace("'", "", $txt_date_from) != "" && str_replace("'", "", $txt_date_to) != "") 
                            {
                                echo "Sewing vs Export From : $txt_date_from to $txt_date_to"; 
                            }
                            else{
                                echo "Yearly Sewing vs Export With Value : $cbo_year_selection"; 
                            }
                            
                        ?>
                    </strong>
                </td> 
            </tr>
            
        </table>
        <table class="rpt_table" style="width: <? echo 590+ count($header_arr)*180?>px;" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <thead>
                <tr>
                    <th width="120" rowspan="2">COMPANY</th>
                    <th width="130" rowspan="2">LOCATION</th>
                    <th width="100" rowspan="2">Buyer</th>
                    <? 
                        foreach ($header_arr as $key => $value) 
                        {
                            ?>
                            <th width="180" colspan="3"><? echo $value;?></th>
                            <?
                        }
                    ?>
                    <th width="180" colspan="3">Total</th>
                    <th width="60" rowspan="2">Qty Avg.%</th>
                </tr>
                <tr>
                    <? 
                        foreach ($header_arr as $key => $value) 
                        {
                            ?>
                            <th width="60">Sewing Qty</th>
                            <th width="60">Shipment Qty</th>
                            <th width="60">Shipment Value</th>
                            <?
                        }
                    ?>
                        <th width="60">Sewing Qty</th>
                        <th width="60">Export Qty</th>
                        <th width="60">Export Value</th>
                </tr>
            </thead>
        </table>
        <div style="width:<? echo 608+ count($header_arr)*180?>px; max-height:350px; overflow-y: scroll;" id="scroll_body" align="left"> 
            <table class="rpt_table" style="width: <? echo 590+ count($header_arr)*180?>px;" cellpadding="0" cellspacing="0" border="1" rules="all" align="left";>
                <tbody>
                    <?
                        foreach($result_array as $companyId => $companyData)
                        {
                            foreach($companyData as $locationId=>$locationData)
                            {
                                $buyer_row_span_arr[$companyId."*".$locationId];
                                $y=1;$sub_total_arr=array();$sub_total_qnty_avg_perc=$sub_total_sew_qnty=$sub_total_ex_qnty=$sub_total_ex_value=0;
                                foreach($locationData as $buyer_id => $row)
                                {
                            
                                    if ($m % 2 == 0)
                                    $bgcolor = "#E9F3FF";
                                    else
                                    $bgcolor = "#FFFFFF";
                                    $buyer_td_span = $buyer_row_span_arr[$companyId."*".$locationId];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
                                        <?
                                        if($y == 1)
                                        {
                                            ?>
                                            <td width="120" rowspan="<? echo $buyer_td_span;?>"><p><? echo $companyArr[$companyId];?></p></td>
                                            <td width="130" rowspan="<? echo $buyer_td_span;?>"><p><? echo $locationArr[$locationId];?></p></td>
                                            <?
                                        }
                                        ?>
                                        <td width="100"><p><? echo $buyerArr[$buyer_id];?></p></td>

                                        <? 
                                        $row_sew_qnty=$row_ex_qnty=$row_ex_value=0;
                                            foreach($header_arr as $m_y)
                                            {
                                                
                                                ?>
                                                <td width="60" align="right"><p><? echo $row[$m_y]["sewing_qnty"]; ?></p></td>
                                                <td width="60" align="right"><p><? echo $row[$m_y]["ex_qnty"]; ?></p></td>
                                                <td width="60" align="right"><p><? echo number_format($row[$m_y]["ex_value"],2);?></p></td>
                                                <?
                                                $sub_total_arr[$companyId."*".$locationId."*".$m_y]["sewing_qnty"] += $row[$m_y]["sewing_qnty"];
                                                $sub_total_arr[$companyId."*".$locationId."*".$m_y]["ex_qnty"] += $row[$m_y]["ex_qnty"]; 
                                                $sub_total_arr[$companyId."*".$locationId."*".$m_y]["ex_value"] += $row[$m_y]["ex_value"]; 
                                                $row_sew_qnty += $row[$m_y]["sewing_qnty"];
                                                $row_ex_qnty += $row[$m_y]["ex_qnty"];
                                                $row_ex_value += $row[$m_y]["ex_value"];
                                                $grand_total_arr[$m_y]["ex_qnty"] += $row[$m_y]["ex_qnty"]; 
                                                $grand_total_arr[$m_y]["ex_value"] += $row[$m_y]["ex_value"]; 
                                                $grand_total_arr[$m_y]["sewing_qnty"] += $row[$m_y]["sewing_qnty"]; 
                                            }

                                            $sub_total_qnty_avg_perc += ($row_ex_qnty/$grand_total_ex_qnty)*100;
                                            $sub_total_sew_qnty += $row_sew_qnty;
                                            $sub_total_ex_qnty += $row_ex_qnty;
                                            $sub_total_ex_value += $row_ex_value;

                                            $grand_total_qnty_avg_perc += ($row_ex_qnty/$grand_total_ex_qnty)*100;
                                            $grand_total_sew_qnty += $row_sew_qnty;
                                            //$grand_total_ex_qnty += $row_ex_qnty;
                                            $grand_total_ex_value += $row_ex_value;
                                        ?>
                                        <td width="60" align="right"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row_sew_qnty;?></p></td>
                                        <td width="60" align="right"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row_ex_qnty;?></p></td>
                                        <td width="60" align="right"><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($row_ex_value,2);?></p></td>
                                        <td width="60" align="right"><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format(($row_ex_qnty/$grand_total_ex_qnty)*100,2);?></p></td>
                                       
                                    </tr>
                                    <?
                                    $y++;$m++;

                                }
                                
                                ?>

                                <tr style="background-color:#e0e0e0; font-weight: bold">
                                    <td colspan="3" align="right" width="350"><b>Total</b></td>
                                    <? 
                                    foreach($header_arr as $m_y_f)
                                    {
                                        ?>
                                        <td align="right" width="60"><p style="word-break: break-all;word-wrap: break-word;">&nbsp;<? echo number_format($sub_total_arr[$companyId."*".$locationId."*".$m_y_f]['sewing_qnty'],2,".","");?></p></td>
                                        <td align="right" width="60"><p style="word-break: break-all;word-wrap: break-word;">&nbsp;<? echo number_format($sub_total_arr[$companyId."*".$locationId."*".$m_y_f]['ex_qnty'],2,".","");?></p></td>
                                        <td align="right" width="60"><p style="word-break: break-all;word-wrap: break-word;">&nbsp;<? echo number_format($sub_total_arr[$companyId."*".$locationId."*".$m_y_f]['ex_value'],2,".","");?></p></td>
                                        <?
                                    }

                                    ?>
                                    <td align="right" width="60"><b><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($sub_total_sew_qnty,2);?></p></b></td>
                                    <td align="right" width="60"><b><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($sub_total_ex_qnty,2);?></p></b></td>
                                    <td align="right" width="60"><b><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($sub_total_ex_value,2);?></p></b></td>
                                    <td align="right" width="60"><b><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($sub_total_qnty_avg_perc,2);?></p></b></td>
                                </tr>
                                <?
                                
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <table class="rpt_table" style="width: <? echo 590+ count($header_arr)*180?>px;" cellpadding="0" cellspacing="0" border="1" rules="all" align="left";>
            <tfoot>
                <tr style="background-color:#e0e0e0; font-weight: bold">
                    <td  align="right" width="120"></td>
                    <td  align="right" width="130"></td>
                    <td  align="right" width="100"><b>Grand Total</b></td>
                    <? 
                    foreach($header_arr as $m_y_f)
                    {
                        ?>
                        <td align="right" width="60"><p style="word-break: break-all;word-wrap: break-word;">&nbsp;<? echo number_format($grand_total_arr[$m_y_f]["sewing_qnty"],2,".","");?></p></td>
                        <td align="right" width="60"><p style="word-break: break-all;word-wrap: break-word;">&nbsp;<? echo number_format($grand_total_arr[$m_y_f]['ex_qnty'],2,".","");?></p></td>
                        <td align="right" width="60"><p style="word-break: break-all;word-wrap: break-word;">&nbsp;<? echo number_format($grand_total_arr[$m_y_f]['ex_value'],2,".","");?></p></td>
                        <?
                    }

                    ?>
                    <td align="right" width="60"><b><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($grand_total_sew_qnty,2);?></p></b></td>
                    <td align="right" width="60"><b><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($grand_total_ex_qnty,2);?></p></b></td>
                    <td align="right" width="60"><b><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($grand_total_ex_value,2);?></p></b></td>
                    <td align="right" width="60"><b><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($grand_total_qnty_avg_perc,2);?></p></b></td>
                </tr>
            </tfoot>
        </table>
    </fieldset>

    <br><br>
                    
    <?

    }

	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename####$type";
	exit();      
    
} 

?>