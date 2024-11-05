<?
include('../../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');


if ($action=="load_drop_down_location")
{
	 echo create_drop_down( "cbo_location_id", 160, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );	
	 	 
}


if($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year_name=str_replace("'","",$cbo_year_name);
	$cbo_month_end=str_replace("'","",$cbo_month_end);
	$cbo_end_year_name=str_replace("'","",$cbo_end_year_name);
	
	$buyerArr=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
	//$buyerArr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	
	$company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id='$cbo_company_name'");
	$year_library=return_library_array( "select id, year from  lib_capacity_calc_mst", "id", "year"  );
	
	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_year_name);
	//$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;
	//$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;
	
	$s_date=$cbo_end_year_name."-".$cbo_month_end."-"."01";
	$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;
	
	if($db_type==2)
	{
		$s_date=change_date_format($s_date,'yyyy-mm-dd','-',1);
		$e_date=change_date_format($e_date,'yyyy-mm-dd','-',1);
	}
	else if($db_type==1)
	{
		$s_date=change_date_format($s_date,'yyyy-mm-dd','-',0);
		$e_date=change_date_format($e_date,'yyyy-mm-dd','-',0);
	}
	
	$qry_working_hour=return_field_value("working_hour","lib_standard_cm_entry", "company_id='".str_replace("'","",$cbo_company_name)."' and applying_period_date='$s_date' and is_deleted=0 and status_active=1","working_hour");
	?>
    <div style=" max-height:400px;" align="left">
        <div style="width:100%;">
            <table style="text-align:center;" cellspacing="0" width="100%" border="0" rules="" class="">
                <tr class="form_caption">
                    <?
                    $data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$cbo_company_name' and form_name='company_details' and is_deleted=0 and file_type=1");
                    ?>
                    <td rowspan="2" align="left" width="50">
                    <?
                    foreach ($data_array as $img_row) 
                    {
                        ?>
                        <img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle"/>
                        <?
                    }
                    ?>
                    </td>
                    <td colspan="10" align="center" style="font-size:18px;">
                        <strong style="font-size:18px"> <? echo $company_library[0]['COMPANY_NAME']; ?></strong>
                        <? echo show_company($cbo_company_name,'', array('city')); ?>
                    </td>
                    <td rowspan="2" align="left" width="50">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="10" align="center"><strong style="font-size:18px"> Product Wise Monthly Capacity and Order Summary</strong></td>
                </tr>
                <tr>
                    <td colspan="12" align="center">&nbsp;</td>
                </tr>
            </table>
        </div>
        
        <div style=" max-height:400px;"  align="left" id="scroll_body">
            <table style="text-align:center; font-size:13px;" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <td colspan="12" align="left" style="font-size:16px;">Capacity Availability for the Month of <? echo $months[$cbo_month_end]." ".$cbo_end_year_name;?></td>
                    </tr>
                    <tr>
                        <th width="70">Section</th>
                        <th width="70">Item</th>
                        <th width="70">Working Days</th>
                        <th width="70">Working Hour</th>
                        <th width="70">Sewing Line</th>
                        <th width="70">Machine Operator</th>
                        <th width="70">Available (SAH)</th>
                        <th width="70">Plan Efficiency</th>
                        <th width="70">Total Capacity (SAH)</th>
                        <th width="70">SMV (Forecast)</th>
                        <th width="70">Total Capacity (Pcs)</th>
                        <th width="">Remarks </th>
                    </tr>			
                </thead>
                <tbody>
                <? 
                $yearCond = "";
                $monthCond = "";
                $prodCatCond = "";
                if(str_replace("'","",$cbo_end_year_name)!=0){
                    $yearCond = " and a.year = $cbo_end_year_name";
                }
                if(str_replace("'","",$cbo_product_category)!=0){
                    $prodCatCond = " and a.prod_category_id = $cbo_product_category";
                }else{
					$prodCatCond = " and a.prod_category_id in(1,2,5)";
				}
                if(str_replace("'","",$cbo_month_end)!=0){
                    $monthCond = " and c.month_id = $cbo_month_end and b.month_id=$cbo_month_end";
                }
                
                
                $sql = "select a.id, a.comapny_id, a.location_id, a.capacity_source, a.year, a.avg_machine_line, a.basic_smv, a.effi_percent, a.prod_category_id, b.date_calc, b.day_status, b.no_of_line, b.capacity_min, b.capacity_pcs, c.month_id, c.working_day, c.capacity_month_min, c.capacity_month_pcs from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c where a.id=b.mst_id and a.id=c.mst_id and a.capacity_source=1 and a.prod_category_id != 0 and a.comapny_id=$cbo_company_name and a.location_id=$cbo_location_id $yearCond $monthCond $prodCatCond and a.is_deleted=0 and a.status_active=1";
				
                $sql_data=sql_select($sql);
                $month_data_arr=array();
                $numberOfLine = "";
				$prodIdCompare = "";
				
                foreach($sql_data as $val)
                {
                    $month_data_arr[$val[csf('prod_category_id')]]['id']=$val[csf('id')];
                    $month_data_arr[$val[csf('prod_category_id')]]['prod_category_id']=$val[csf('prod_category_id')];
                    $month_data_arr[$val[csf('prod_category_id')]]['working_day']=$val[csf('working_day')];
                    
                    $month_data_arr[$val[csf('prod_category_id')]]['effi_percent']=$val[csf('effi_percent')];
                    $month_data_arr[$val[csf('prod_category_id')]]['avg_machine_line']=$val[csf('avg_machine_line')];
                    $month_data_arr[$val[csf('prod_category_id')]]['basic_smv']=$val[csf('basic_smv')];
                    $month_data_arr[$val[csf('prod_category_id')]]['effi_percent']=$val[csf('effi_percent')];
                    if( $numberOfLine !="" && $numberOfLine != $val[csf('no_of_line')] && $prodIdCompare == $val[csf('prod_category_id')] )
                    {
                        $month_data_arr[$val[csf('prod_category_id')]]['no_of_line'] = "Show";
                    }else{
                        $numberOfLine = $val[csf('no_of_line')];
						$prodIdCompare = $val[csf('prod_category_id')];
						
                        $month_data_arr[$val[csf('prod_category_id')]]['no_of_line'] = $val[csf('no_of_line')];
                    }
                    
                    
                    $month_data_arr[$val[csf('prod_category_id')]]['capacity_min']=$val[csf('capacity_min')];
                    $month_data_arr[$val[csf('prod_category_id')]]['capacity_pcs']=$val[csf('capacity_pcs')];
                    
                    $month_data_arr[$val[csf('prod_category_id')]]['month_id']=$val[csf('month_id')];
                    $month_data_arr[$val[csf('prod_category_id')]]['working_day']=$val[csf('working_day')];
                    $month_data_arr[$val[csf('prod_category_id')]]['capacity_month_min']=$val[csf('capacity_month_min')];
                    $month_data_arr[$val[csf('prod_category_id')]]['capacity_month_pcs']=$val[csf('capacity_month_pcs')];
                    
                }
                
               // echo "<pre>";
                //print_r($month_data_arr);die;
                $i=1;
                $gTotWorkintDayes = "";
                $gTotWorkintHours = "";
                $gTotSwingLine = "";
                $gTotMatchinOperator = "";
                $gTotAvailableSAH = "";
                $gTotEfficiency = "";
                $gTotCapacitySAH = "";
                $gTotCapacityPcs = "";
                
                foreach($month_data_arr as $prodId => $value)
                {
                ?>
                    <tr>
                        <?
                        /*$capacity_month = ($value['capacity_month_min']/60);
                        $totalCapacitySMV = ($capacity_month*$value['effi_percent'])/100;
                        $totalCapacityPcs = ($totalCapacitySMV*60)/$value['basic_smv'];*/
						
						$capacity_month = (($value['capacity_month_min']*100)/$value['effi_percent'])/60;
                        $totalCapacitySMV = ($capacity_month*$value['effi_percent'])/100;
                        $totalCapacityPcs = ($totalCapacitySMV*60)/$value['basic_smv'];
                        
                        $gTotWorkintDayes += $value['working_day'];
                        $gTotWorkintHours += $qry_working_hour;
                        $gTotSwingLine += $value['no_of_line'];
                        $gTotMatchinOperator += $value['avg_machine_line'];
                        $gTotAvailableSAH += $capacity_month;
                        $gTotEfficiency += $value['effi_percent'];
                        $gTotCapacitySAH += $totalCapacitySMV;
                        $gTotCapacityPcs += $totalCapacityPcs;
                        
                        if($i==1)
                        {
                        ?>
                          <td rowspan="<? echo count($month_data_arr);?>" style="vertical-align:middle;">Sewing</td>
                        <?php
                        }
                        ?>
                        <td width="70"><? echo $product_types[$prodId]; ?></td>
                        <td width="70"><? echo $value['working_day']; ?></td>
                        <td width="70"><? echo $qry_working_hour; ?></td>
                        <? if($value['no_of_line']!="Show"){?>
                        <td width="70"> <? echo $value['no_of_line'];  ?></td>
                        <? }else{?>
                        <td width="70">
                            <a href="#" onclick="show_month_dtls_data('<? echo $value['id']."__".$cbo_month_end ?>')"> 
                                <? echo $value['no_of_line'];  ?>
                            </a>
                        </td>
                        <? } ?>
                        <td width="70"><? echo $value['avg_machine_line']; ?></td>
                        <td width="70" align="right"><? echo number_format($capacity_month,2); ?></td>
                        <td width="70"><? echo $value['effi_percent']; ?></td>
                        <td width="70" align="right"><? echo number_format($totalCapacitySMV,2); ?></td>
                        <td width="70"><? echo $value['basic_smv']; ?></td>
                        <td width="70" align="right"><? echo number_format($totalCapacityPcs,2); ?></td>
                        <td><? echo "????"; ?></td>
                     </tr>
                <? 
                $i++;
                }
                ?>
                    <tr class="tbl_bottom">
                        <td colspan="2"  align="right">Total/ Avg.</td>
                        <td width="70"><? echo number_format($gTotWorkintDayes/count($month_data_arr),2); ?></td>
                        <td width="70"><? echo number_format($gTotWorkintHours/count($month_data_arr),2); ?></td>
                        <td width="70"><? echo number_format($gTotSwingLine,2); ?></td>
                        <td width="70"><? echo number_format($gTotMatchinOperator,2); ?></td>
                        <td width="70" align="right"><? echo number_format($gTotAvailableSAH,2); ?></td>
                        <td width="70"><? echo number_format($gTotEfficiency/count($month_data_arr),2); ?></td>
                        <td width="70" align="right"><? echo number_format($gTotCapacitySAH,2); ?></td>
                        <td width="70"><? echo " "; ?></td>
                        <td width="70" align="right"><? echo number_format($gTotCapacityPcs,2); ?></td>
                        <td ><? echo " "; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php 
         
			if(str_replace("'","",$cbo_product_category)!=0){
				//$prodCatCond = " and c.item_number_id = $cbo_product_category";
				$prodCatCond = " and g.product_type_id = $cbo_product_category";
			}else{
				$prodCatCond = " and g.product_type_id in(1,2,5)";
			}

			
			$ord_sql = "SELECT a.buyer_name, b.po_received_date, b.po_quantity , b.id, c.gmts_item_id, c.smv_pcs, g.product_type_id, sum(d.production_quantity) as production_quantity FROM wo_po_details_master a, wo_po_details_mas_set_details c, lib_garment_item g, wo_po_break_down b left join pro_garments_production_mst d on (b.id=d.po_break_down_id and d.production_type=5 and d.company_id=$cbo_company_name and d.location=$cbo_location_id and d.production_date<'$s_date' and d.is_deleted=0 and d.status_active=1) WHERE b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and c.gmts_item_id=g.id and b.po_received_date<='$e_date' and a.company_name=$cbo_company_name and a.location_name=$cbo_location_id $prodCatCond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and g.is_deleted=0 and g.status_active=1 GROUP BY a.buyer_name, b.po_received_date, b.po_quantity, b.id, c.gmts_item_id, c.smv_pcs, g.product_type_id";
						
			$order_sql = sql_select($ord_sql);
			
			$self_order_buyer_arr=array();
			$buyer_ord_tot_arr = array();
			$prev_ord_tot_arr = array();
			//$prev_production_tot_arr = array();
			foreach( $order_sql as $order_val )
			{
				if(strtotime($order_val[csf('po_received_date')])>=strtotime($s_date))
				{
					$self_order_buyer_arr[$order_val[csf('buyer_name')]] = $order_val[csf('buyer_name')];
					//$buyer_ord_tot_arr[$order_val[csf('buyer_name')]]['order_quantity'] += $order_val[csf('po_quantity')];
					$product_ord_arr[$order_val[csf('product_type_id')]][$order_val[csf('buyer_name')]]['order_quantity'] += $order_val[csf('po_quantity')]*1;
					$product_total_arr[$order_val[csf('product_type_id')]]['smv'] += $order_val[csf('po_quantity')]*$order_val[csf('smv_pcs')];
				}else{
					$prev_ord_tot_arr[$order_val[csf('product_type_id')]]['balance_quantity_self'] += ($order_val[csf('po_quantity')]-$order_val[csf('production_quantity')]);
					$prev_ord_tot_arr[$order_val[csf('product_type_id')]]['balance_min_self'] += ($order_val[csf('po_quantity')]-$order_val[csf('production_quantity')])*$order_val[csf('smv_pcs')];
				}
			}
			
			
			//echo "<pre>";
			//print_r($product_ord_arr);die;
			//echo $product_ord_arr[5][187]['order_quantity'];//die;
			
			if(str_replace("'","",$cbo_product_category)!=0){
				$prodCatCond = " and d.product_type_id = $cbo_product_category";
			}else{
				$prodCatCond = " and d.product_type_id in(1,2,5)";
			}
            
             $subcon_sql = "select a.party_id, b.order_no, b.order_quantity, b.order_rcv_date, b.delivery_date, b.smv, c.item_id, d.product_type_id, sum(e.production_qnty) as production_qnty from subcon_ord_mst a, subcon_ord_dtls b, lib_garment_item d, subcon_ord_breakdown c left join subcon_gmts_prod_dtls e on (c.order_id=e.order_id and e.company_id=$cbo_company_name and e.location_id=$cbo_location_id and e.production_date < '$s_date' and e.production_type=2 and e.is_deleted=0 and e.status_active=1) where a.subcon_job =b.job_no_mst and b.id=c.order_id and c.item_id=d.id and a.company_id=$cbo_company_name and a.location_id=$cbo_location_id and b.order_rcv_date<='$s_date'  $prodCatCond  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.is_deleted=0 and d.status_active=1 group by a.party_id, b.order_no, b.order_quantity, b.order_rcv_date, b.delivery_date, b.smv, c.item_id, d.product_type_id";
			
            $subcon_order_sql = sql_select($subcon_sql);
            
                 
            $subcon_ord_tot_arr = array();
            $self_order_subcon_arr=array();
            $prev_subcon_ord_tot_arr=array();
            //$prev_subcon_prod_tot_arr=array();
            foreach( $subcon_order_sql as $subcon_val )
			{
                if(strtotime($subcon_val[csf('order_rcv_date')])>=strtotime($s_date))
                {
                    $self_order_subcon_arr[$subcon_val[csf('party_id')]] = $subcon_val[csf('party_id')];
                    //$subcon_ord_tot_arr[$subcon_val[csf('party_id')]]['order_quantity'] += $subcon_val[csf('order_quantity')];
                    $subcon_ord_arr[$subcon_val[csf('product_type_id')]][$subcon_val[csf('party_id')]]['order_quantity'] += $subcon_val[csf('order_quantity')];
                    $product_total_arr[$subcon_val[csf('product_type_id')]]['smv'] += $subcon_val[csf('order_quantity')]*$subcon_val[csf('smv')];
                }else{
                    $prev_subcon_ord_tot_arr[$subcon_val[csf('product_type_id')]]['balance_quantity_self'] += ($subcon_val[csf('order_quantity')] - $subcon_val[csf('production_qnty')]);
                    $prev_subcon_ord_tot_arr[$subcon_val[csf('product_type_id')]]['balance_min_self'] += ($subcon_val[csf('order_quantity')] - $subcon_val[csf('production_qnty')])*$subcon_val[csf('smv')];
                    
                }
            }
            
            if(count($self_order_buyer_arr)==0){
                $hideBuyerTd = "style='display:none;'";
            }
            if(count($self_order_subcon_arr)==0){
                $hideSubconTd = "style='display:none;'";
            }
        ?>
        <div style=" max-height:400px;" align="left" id="scroll_body">
            <table style="text-align:center; font-size:13px;" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <td colspan="<?php echo (count($self_order_buyer_arr)+count($self_order_subcon_arr))+9;?>" align="left" style="font-size:16px;">Order Allocation for Month of  <? echo $months[$cbo_month_end]." ".$cbo_end_year_name;?></td>
                    </tr>
                    <tr>
                        <th width="70" rowspan="3">Item</th>
                        <th width="70" rowspan="3">Capacity (Pcs)</th>
                        <th width="70" rowspan="3">Capacity (SAH)</th>
                        <th colspan="<?php echo (count($self_order_buyer_arr)+count($self_order_subcon_arr))+4;?>">Order Allocation</th>
                        <th width="70" rowspan="3">Pcs Deviation</th>
                        <th width="70" rowspan="3">SAH Deviation</th>
                    </tr>
                    <tr>
                        <th width="" <? echo $hideBuyerTd;?> colspan="<?php echo count($self_order_buyer_arr);?>">Direct Orders</th>
                        <th width="" <? echo $hideSubconTd;?> colspan="<?php echo count($self_order_subcon_arr);?>">Subcontract Orders</th>
                        <th width="70" rowspan="2">Pcs Carry Forward From <? echo $months[$cbo_month_end];?></th>
                        <th width="70" rowspan="2">Total Allocation (Pcs)</th>
                        
                        <th width="70" rowspan="2">SAH Carry Forward From <? echo $months[$cbo_month_end];?></th>
                        <th width="70" rowspan="2">Total Allocation (SAH)</th>
                    </tr>
                    <tr>
                    <?php 
                        foreach($self_order_buyer_arr as $s_buyer)
                        {
                        ?>
                            <th width="70"><?php echo $buyerArr[$s_buyer]; ?></th>
                        <?php	
                        }
                        foreach($self_order_subcon_arr as $s_subcon)
                        {
                        ?>
                            <th width="70"><?php echo $buyerArr[$s_subcon]; ?></th>
                        <?php	
                        }
                    ?>
                    </tr>			
                </thead>
                <tbody>
               <?php
                $gTotalCarryForward=0;
                $gTotalAllocation=0;
                $gTotalSAHcarryForward=0;
                $gTotalAllocationSAH=0;
                $gTotalPcsDeviation=0;
                $gTotalSAHDeviation=0;
				
                foreach($month_data_arr as $prodId => $value)
                {
                    $TotalAllocation=0;
                    
                    /*$capacity_month = ($value['capacity_month_min']/60);
                    $totalCapacitySMV = ($capacity_month*$value['effi_percent'])/100;
                    $totalCapacityPcs = ($totalCapacitySMV*60)/$value['basic_smv'];*/
					
					$capacity_month = (($value['capacity_month_min']*100)/$value['effi_percent'])/60;
					$totalCapacitySMV = ($capacity_month*$value['effi_percent'])/100;
					$totalCapacityPcs = ($totalCapacitySMV*60)/$value['basic_smv'];
                ?>
                    <tr>
                        <td width="70"><? echo $product_types[$prodId]; ?></td>
                        
                        <td width="70" align="right"><? echo number_format($totalCapacityPcs,2); ?></td>
                        <td width="70" align="right"><? echo number_format($totalCapacitySMV,2); ?></td>
                        <?php
                        
                        foreach($self_order_buyer_arr as $s_buyer)
                        {
                            $TotalAllocation += $product_ord_arr[$prodId][$s_buyer]['order_quantity'];
							$buyer_ord_tot_arr[$s_buyer]['order_quantity'] += $product_ord_arr[$prodId][$s_buyer]['order_quantity'];
                        ?>
                            <td width="70" align="right"><? echo number_format($product_ord_arr[$prodId][$s_buyer]['order_quantity'],2); ?> </td>
                        <?php	
                        }
                        
                        
                        foreach($self_order_subcon_arr as $s_subcon)
                        {
                            $TotalAllocation += $subcon_ord_arr[$prodId][$s_subcon]['order_quantity'];
							$subcon_ord_tot_arr[$prodId]['order_quantity'] += $subcon_ord_arr[$prodId][$s_subcon]['order_quantity'];
                        ?>
                            <td width="70" align="right"><? echo number_format($subcon_ord_arr[$prodId][$s_subcon]['order_quantity'],2); ?></td>
                        <?php	
                        }
                        ?>
                        
                        <td width="70" align="right"><? 
                        $carryForwardTot = ($prev_ord_tot_arr[$prodId]['balance_quantity_self']+$prev_subcon_ord_tot_arr[$prodId]['balance_quantity_self']);
                        echo number_format($carryForwardTot,2); 
                        
                        $SAHcarryForward = ($prev_ord_tot_arr[$prodId]['balance_min_self']+$prev_subcon_ord_tot_arr[$prodId]['balance_min_self'])/60;
                        $gTotalSAHcarryForward += ($prev_ord_tot_arr[$prodId]['balance_min_self']+$prev_subcon_ord_tot_arr[$prodId]['balance_min_self'])/60;
                        
                        $TotalAllocation += $carryForwardTot;
                        $gTotalCarryForward += $carryForwardTot;
                        $gTotalAllocation +=$TotalAllocation;
                        $gTotalAllocationSAH +=$SAHcarryForward+$product_total_arr[$prodId]['smv']/60;
                        
                        ?></td>
                        <td width="70" align="right"><? echo number_format($TotalAllocation,2); ?></td>
                        <td width="70" align="right"><? echo number_format($SAHcarryForward,2); ?></td>
                        <td width="70" align="right"><? echo number_format($SAHcarryForward+$product_total_arr[$prodId]['smv']/60,2); ?></td>
                        <td width="70" align="right"><?
                        
                        $gTotalPcsDeviation +=($TotalAllocation-$totalCapacityPcs);
                        echo number_format(($TotalAllocation-$totalCapacityPcs),2); 
                        ?></td>
                        <td width="70" align="right"><? 
                        $gTotalSAHDeviation += ( ($SAHcarryForward+$product_total_arr[$prodId]['smv']/60) - $totalCapacitySMV );
                        echo number_format(( ($SAHcarryForward+$product_total_arr[$prodId]['smv']/60) - $totalCapacitySMV ),2); ?></td>
                     </tr>
                <? 
                $i++;
                }
                ?>
                    <tr class="tbl_bottom">
                        <td>Total</td>
                        <td width="70" align="right"><? echo number_format($gTotCapacityPcs,2); ?></td>
                        <td width="70" align="right"><? echo number_format($gTotCapacitySAH,2); ?></td>
                        <?php
						if( count($month_data_arr)>0){
							foreach( $self_order_buyer_arr as $s_buyer )
							{
							?>
								<td width="70" align="right"><? echo number_format($buyer_ord_tot_arr[$s_buyer]['order_quantity'],2); ?></td>
							<?php	
							}
							
							foreach( $self_order_subcon_arr as $s_subcon )
							{
							?>
								<td width="70" align="right"><? echo number_format($subcon_ord_tot_arr[$s_subcon]['order_quantity'],2); ?></td>
							<?php	
							}
						}
                        ?>
                        <td width="70" align="right"><? echo number_format($gTotalCarryForward,2); ?></td>
                        <td width="70" align="right"><? echo number_format($gTotalAllocation,2); ?></td>
                        <td width="70" align="right"><? echo number_format($gTotalSAHcarryForward,2); ?></td>
                        <td width="70" align="right"><? echo number_format($gTotalAllocationSAH,2); ?></td>
                        <td align="right"><? echo number_format($gTotalPcsDeviation,2); ?></td>
                        <td align="right"><? echo number_format($gTotalSAHDeviation,2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
	<?
	exit();
}


if ($action=="multi_sewing_line")
{
	echo load_html_head_contents("Popup Info", "../../../../", 1, 1, $unicode, 1);
    extract($_REQUEST);
	$data = explode("__",$data);
	$sql="select id, month_id, date_calc, day_status, no_of_line, capacity_min, capacity_pcs from lib_capacity_calc_dtls where mst_id=$data[0] and month_id=$data[1] order by date_calc";
	
	$result = sql_select($sql);
	
	?>
	<table align="" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table">
        <thead>
        	<th>Date</th><th>Day Status</th><th>No. of Line</th><th>Capacity (Mnt.)</th><th>Capacity (Pcs)</th>
        </thead>
        <tbody>
        <?
        $i=1;
        foreach($result as $row )
        {  
        	if($row[csf('capacity_min')] == "") $disable_status = "disabled='disabled'";
			?>
			<tr align="center">
				<td>
					<input type="text" name="txt_date_<? echo $i; ?>" id="txt_date_<? echo $i; ?>" class="datepicker" style="width:67px" value="<? echo  change_date_format($row[csf('date_calc')]);  ?>" readonly/>
				</td>
				<td>
					<?
						$day_status=array(1=>"Open",2=>"Closed");
						echo create_drop_down( "cbo_day_status_$i", 72,$day_status,"", 0, "-- Select --",$row[csf('day_status')],"" );
					?>
				</td>
				<td>
					<input type="text" name="txt_no_of_line_<? echo $i; ?>" id="txt_no_of_line_<? echo $i; ?>" value="<? echo $row[csf('no_of_line')]; ?>" class="text_boxes_numeric" style="width:60px" />
				</td>
				<td>
					<input type="text" name="txt_capacity_min_<? echo $i; ?>" id="txt_capacity_min_<? echo $i; ?>" value="<? echo $row[csf('capacity_min')]; ?>" class="text_boxes_numeric" style="width:100px" readonly="readonly" <? echo $disable_status; ?> />
				</td>
				<td>
					<input type="text" name="txt_capacity_pcs_<? echo $i; ?>" id="txt_capacity_pcs_<? echo $i; ?>" value="<? echo $row[csf('capacity_pcs')]; ?>" class="text_boxes_numeric" style="width:100px" readonly="readonly" <? echo $disable_status; ?> />
				</td>
			</tr>
			<? 
			$disable_status="";
			$i++;
			$kk++;
		} 
		?>
		</tbody>
	</table>
	<?
	exit();
} 
