<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Dash Board.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam 
Creation date 	: 	
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('../../includes/common.php');
echo load_html_head_contents("Graph", "../../", "", $popup, $unicode, $multi_select, 1);
 
//--------------------------------------------------------------------------------------------------------------------

?>	

<script src="../../Chart.js-master/Chart.min.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.js"></script>-->





	<div style="margin-left:10px; margin-top:10px; width:98%">
		<div style="width:32%; height:300px; float:left; position:relative; border:solid 1px">
            <canvas id="canvas" height="300" width="500"></canvas>
		</div>
        <div style="width:32%; height:300px; float:left; position:relative; margin-left:10px; border:solid 1px">
            <canvas id="canvas2" height="300" width="500"></canvas>
		</div>
        <div style="width:32%; height:300px; float:left; position:relative; margin-left:10px; border:solid 1px">
            <canvas id="canvas3" height="300" width="500"></canvas>
		</div>
        <div style="width:32%; height:300px; float:left; position:relative; margin-top:5px; border:solid 1px">
            <canvas id="canvas4" height="300" width="500"></canvas>
		</div>
        <div style="width:32%; height:300px; float:left; position:relative; margin-left:10px; margin-top:5px; border:solid 1px">
            <canvas id="canvas5" height="300" width="500"></canvas>
		</div>
        <div style="width:32%; height:300px; float:left; position:relative; margin-left:10px; margin-top:5px; border:solid 1px">
            <canvas id="canvas6" height="300" width="500"></canvas>
		</div>
        <div style="width:32%; height:280px; float:left; position:relative; margin-top:5px; border:solid 1px">
            <canvas id="canvas7" height="280" width="500"></canvas>
		</div>
        
	</div>
	<?
        list($lcCompany,$location,$floor,$workingCompany)=explode('__',$_REQUEST['cp']);
		if($workingCompany){$company_cond=" and comp.id=$workingCompany";}
		else if($lcCompany){$company_cond=" and comp.id=$lcCompany";}
		
		
		$month_array=array();	
        $month_prev=add_month(date("Y-m-d",time()),-3);
        $month_next=add_month(date("Y-m-d",time()),8);
        
        $start_yr=date("Y",strtotime($month_prev));
        $end_yr=date("Y",strtotime($month_next));
        for($e=0;$e<=11;$e++)
        {
            $tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
            $yr_mon_part[$e]=date("Y-m",strtotime($tmp));
            $month_array[$e]=date("M",strtotime($tmp))." '".date("y",strtotime($tmp));
        }
        $month_array= json_encode($month_array); 
		
        if($db_type==0)
        {
            $country_ship_date_fld="a.country_ship_date";
            $manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
        }
        else
        {
            $country_ship_date_fld="to_char(a.country_ship_date,'YYYY-MM-DD')";
            $manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
        }
		
		
		
		
		
		
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and status_active=1 and is_deleted=0");
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		
        $machine_arr=array(); $machine_id_arr=array(); $machine_dyeing_id_arr=array();
        $machine_sql_arr = sql_select("select id, prod_capacity, category_id from lib_machine_name where category_id in(1,2) and is_deleted=0 and status_active=1");
        foreach($machine_sql_arr as $machineRow)
        {
			$machine_arr[$machineRow[csf('id')]]=$machineRow[csf('prod_capacity')];
			
			if($machineRow[csf('category_id')]==1)
			{
				$machine_id_arr[]=$machineRow[csf('id')];
			}
			else
			{
				$machine_dyeing_id_arr[]=$machineRow[csf('id')];
			}
        }



        $idle_machine_array=array();
        $sql_machine_idle=sql_select("select machine_entry_tbl_id, from_date, to_date from pro_cause_of_machine_idle where machine_idle_cause in(1,2,3,6,7,8) and is_deleted=0 and status_active=1");
        foreach($sql_machine_idle as $idleRow)
        {
            $from_date=date("Y-m-d", strtotime($idleRow[csf('from_date')]));
            $to_date=date("Y-m-d", strtotime($idleRow[csf('to_date')]));
            $datediff_n = datediff( 'd', $from_date, $to_date);
            
            for($k=0; $k<$datediff_n; $k++)
            {
                $newdate_n=add_date(str_replace("'","",$from_date),$k);
                $idle_machine_array[$newdate_n].=$idleRow[csf('machine_entry_tbl_id')].",";
            }
        }
        
		
		
		$item_smv_array=array();
		if($smv_source==3)
		{
			$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}
		}
		else
		{
			$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs']=$itemData[csf('smv_pcs')];
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs_precost']=$itemData[csf('smv_pcs_precost')];
			}
		}

        $datediff=8; $today=date('Y-m-d'); //$today='2014-06-04';
        if($db_type==0)
        {
            $firstDate = date("Y-m-d", strtotime("-7 day", strtotime($today)));
            $lastDate = date("Y-m-d", strtotime($today));
			//$lastDate = date("Y-m-d", strtotime("-1 day", strtotime($today)));
        }
        else
        {
            $firstDate = date("d-M-Y", strtotime("-7 day", strtotime($today)));
            $lastDate = date("d-M-Y", strtotime($today));	
			//$lastDate = date("d-M-Y", strtotime("-1 day", strtotime($today)));	
        }
		
		$selectd_date=$_GET["date_data"];
		
		for($q=0;$q<8;$q++)
        {
			$last_date='';
			$newdate=add_date($selectd_date,$q);
            $last_date=$newdate;
		}
		
		if($db_type==0)
		{
			$str_date=date("Y-m-d", strtotime($selectd_date));
			$lst_date=date("Y-m-d", strtotime($last_date));
		}
		else 
		{
			$str_date=date("d-M-Y", strtotime($selectd_date));
			$lst_date=date("d-M-Y", strtotime($last_date));
		}
		
		//echo $str_date.'='.$lst_date;
        
        $yarn_stock_array=array(); $knit_array=array(); $dye_array=array(); 
        $sql_yarn="select "; $sql_knit="select "; $sql_subcon="select "; $sql_dye="select ";
        for($j=0;$j<$datediff;$j++)
        {
            $newdate =add_date($str_date,$j);
            $date_array[$j]=date("d-M", strtotime($newdate));
            
            if($db_type==0) $trans_date=date("Y-m-d", strtotime($newdate)); else $trans_date=date("d-M-Y", strtotime($newdate));
            
            if($j!=0) $add_comma=',';
         
        } 
        
		
		
        $tpdArr=array(); $tsmvArr=array();
        
		 $tpd_data_arr=sql_select( "select b.id, a.pr_date, a.smv_adjust_type, a.smv_adjust, (a.target_per_hour*a.working_hour) tpd, (a.man_power*a.working_hour) tsmv from prod_resource_mst b, prod_resource_dtls a where b.id=a.mst_id and b.company_id in($manufacturing_company) and a.pr_date between '$str_date' and '$lst_date' and a.is_deleted=0 and b.is_deleted=0");
		 
		
		 
		 
        foreach($tpd_data_arr as $row)
        {
			$production_date=date("Y-m-d", strtotime($row[csf('pr_date')])); 
            $tpdArr[$production_date]+=$row[csf('tpd')];
			$tsmvArr[$production_date]+=$row[csf('tsmv')]*60;
			
			if($row[csf('smv_adjust_type')]==1) 
			{
				$tsmvArr[$production_date]+=$row[csf('smv_adjust')];
			}
			else if($row[csf('smv_adjust_type')]==2) 
			{
				$tsmvArr[$production_date]-=$row[csf('smv_adjust')];
			}
        }
		//var_dump($tpdArr);
		
		
		
		
		
		
      
	  
        $sewProdArr=array(); $lineArr=array(); $effi_data_arr=array(); $effi_perc_arr=array(); $sew_target_achv_trend_arr=array(); 
        $sew_data_arr=sql_select( "select production_date, sewing_line, prod_reso_allo, po_break_down_id, item_number_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=5 and serving_company in($manufacturing_company) and production_date between '$str_date' and '$lst_date' group by production_date, sewing_line, prod_reso_allo, po_break_down_id, item_number_id");//used for daily prduction
        
		
		
		foreach($sew_data_arr as $row)
        {
            $production_date=date("Y-m-d", strtotime($row[csf('production_date')])); 
            
            $sewProdArr[$production_date]+=$row[csf('production_quantity')];
            $effi_data_arr[$production_date].=$row[csf('sewing_line')]."**".$row[csf('production_quantity')]."**".$row[csf('po_break_down_id')]."**".$row[csf('item_number_id')]."**".$row[csf('prod_reso_allo')].",";
        }
		 //var_dump($sewProdArr);
		
		$subConProd_arr=array(); $subAchvSmv_arr=array();
		$sql_subconProd="select a.order_id, a.production_date, a.production_qnty, b.smv FROM subcon_gmts_prod_dtls a, subcon_ord_dtls b WHERE a.order_id=b.id and a.company_id in($manufacturing_company) and a.production_date between '$str_date' and '$lst_date' and a.production_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";// and b.main_process_id=5
        $subconProdData=sql_select($sql_subconProd);
		foreach($subconProdData as $row)
		{
			 $production_date=date("Y-m-d", strtotime($row[csf('production_date')]));
			 $sewProdArr[$production_date]+=$row[csf('production_qnty')];
			 $subAchvSmv_arr[$production_date]+=$row[csf('production_qnty')]*$row[csf('smv')];
		}
		
		$prod_capacity_arr=array(); $dyeing_capacity_arr=array();	
        for($j=0;$j<$datediff;$j++)
        {
            $newdate=add_date($str_date,$j);
            $prod_date=date("Y-m-d", strtotime($newdate)); 
            
			if($tpdArr[$prod_date]<=0) $tpd=0; else $tpd=$tpdArr[$prod_date];
		    $tpd_arr[]=$tpd;
            
            if($sewProdArr[$prod_date]<=0) $production_quantity=0; else $production_quantity=$sewProdArr[$prod_date];
			$production_quantity+=$subconProdData[0][csf('subconProd_'.$j)];
            $sew_prod_arr[]=$production_quantity;
            
           //$sew_target_achv_trend=($production_quantity*100)/$tpd;
           $sew_target_achv_trend=($production_quantity/$tpd)*100;
		   
		   
		   if($sew_target_achv_trend=="") $sew_target_achv_trend=0;
            $sew_target_achv_trend_arr[]=number_format($sew_target_achv_trend,2,'.','');
            
			$today_smv=0;
            $today_smv=$tsmvArr[$prod_date];

            $achv_smv=0;
			$effi_data=explode(",",substr($effi_data_arr[$prod_date],0,-1));
			foreach($effi_data as $data)
			{
				$data=explode("**",$data);
				$sewing_line=$data[0];
				$production_quantity=$data[1];
				$po_break_down_id=$data[2];
				$item_number_id=$data[3];
				$prod_reso_allo=$data[4];
			   
				$item_smv=0;
				if($smv_source==2)
				{
					$item_smv=$item_smv_array[$po_break_down_id][$item_number_id]['smv_pcs_precost'];
				}
				else if($smv_source==3)
				{
					$item_smv=$item_smv_array[$po_break_down_id][$item_number_id];	
				}
				else
				{
					$item_smv=$item_smv_array[$po_break_down_id][$item_number_id]['smv_pcs'];	
				}
				
				$achv_smv+=$production_quantity*$item_smv;
			}
			
			$achv_smv+=$subAchvSmv_arr[$prod_date];
            
            $today_aff_perc=$achv_smv/$today_smv*100;
            $effi_perc_arr[]=number_format($today_aff_perc,2,'.','');
			//echo $prod_date."==".$achv_smv."**".$today_smv."**".$today_aff_perc."<br>";

        }
         //var_dump($tpd_arr);
        $date_array= json_encode($date_array);
        $tpd_arr= json_encode($tpd_arr); 
        $sew_prod_arr= json_encode($sew_prod_arr);
        $sew_target_achv_trend_arr= json_encode($sew_target_achv_trend_arr);
        $knit_array= json_encode($knit_array);
        $dye_array= json_encode($dye_array);
        $effi_perc_arr= json_encode($effi_perc_arr);
        $prod_capacity_arr=json_encode($prod_capacity_arr);
		$dyeing_capacity_arr=json_encode($dyeing_capacity_arr);
        
		
		
        $exFactory_arr=array(); $exFactory_qnty_arr=array();
        $data_arr=sql_select( "select po_break_down_id, country_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, country_id");
        foreach($data_arr as $row)
        {
            $exFactory_arr[$row[csf('po_break_down_id')]][$row[csf('country_id')]]=$row[csf('ex_factory_qnty')];
            $exFactory_qnty_arr[$row[csf('po_break_down_id')]]+=$row[csf('ex_factory_qnty')];
        }
        
        $sewOutQnty_arr=array();
        $dataSew_arr=sql_select( "select po_break_down_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=5 group by po_break_down_id, country_id");//used for mothly prduction
        foreach($dataSew_arr as $row)
        {
            $sewOutQnty_arr[$row[csf('po_break_down_id')]][$row[csf('country_id')]]=$row[csf('production_quantity')];
        }
        
        $allocation_lib_arr=array();
        $allocationData=sql_select("select a.year, b.month_id, sum(b.capacity_month_min) as capa_min from lib_capacity_calc_mst a, lib_capacity_year_dtls b where a.id=b.mst_id and a.comapny_id in($manufacturing_company) and a.status_active=1 and a.is_deleted=0 group by a.year, b.month_id");
        foreach($allocationData as $row)
        {
            $allocation_lib_arr[$row[csf('year')]][$row[csf('month_id')]]=$row[csf('capa_min')];
        }
        
      
//Capacity, Booked, Produced and Delivery In SMV..................................................start;
		$i=1; 
		$capacity_array=array(); $booked_array=array(); $order_val_array=array(); $exFactory_val_array=array(); $pending_qty_array=array(); $produced_array=array(); $delivery_array=array(); 
        foreach($yr_mon_part as $key=>$val)
        {
           
           $sql="select b.id as po_id, b.unit_price, a.country_id, c.set_smv, c.total_set_qnty, sum(a.order_quantity) AS qnty, sum(a.order_total) AS amnt from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $country_ship_date_fld like '".$val."-%"."' group by b.id, b.unit_price, c.set_smv, c.total_set_qnty, a.country_id";
            //echo $sql ;die;
            $result=sql_select($sql);
            $poQty=0; $poVal=0; $sewOutQnty=0; $exFactoryQty=0; $exFactoryVal=0; $bookedQty=0; $producedQnty=0; $deliveryQty=0;
            foreach($result as $row)
            { 
                $poQty+=$row[csf('qnty')];
                $poVal+=$row[csf('amnt')]; 
                $sewOutQnty+=$sewOutQnty_arr[$row[csf('po_id')]][$row[csf('country_id')]];
                $exFactoryQty+=$exFactory_arr[$row[csf('po_id')]][$row[csf('country_id')]];
                $exFactoryVal+=$exFactory_arr[$row[csf('po_id')]][$row[csf('country_id')]]*($row[csf('unit_price')]/$row[csf('total_set_qnty')]);
                
                $bookedQty+=$row[csf('qnty')]*$row[csf('set_smv')];
                $producedQnty+=$sewOutQnty_arr[$row[csf('po_id')]][$row[csf('country_id')]]*$row[csf('set_smv')];
                $deliveryQty+=$exFactory_arr[$row[csf('po_id')]][$row[csf('country_id')]]*$row[csf('set_smv')];
            }
            
            $booked_array[]=$bookedQty;
            $order_val_array[]=number_format($poVal,2,'.','');
            $produced_array[]=$producedQnty;
            $delivery_array[]=$deliveryQty;
            $exFactory_val_array[]=number_format($exFactoryVal,2,'.','');
            
            $year=date("Y",strtotime($val));
            $month=date("m",strtotime($val));
            
            if($allocation_lib_arr[$year][(int) $month]=="") $capacity=0; else $capacity=$allocation_lib_arr[$year][(int) $month];
            $capacity_array[]=$capacity;
			//$month_caption_arr[]=$month;
            $i++;
        }
       // print_r($capacity_array);
//Capacity, Booked, Produced and Delivery In SMV..................................................end;		
		
		
		
        $pending_month_array=array(); $pending_month_data_array=array();
        if($db_type==0) $today=date('Y-m-d'); else $today=date('d-M-Y'); $po='';
        

		$pendingData="select c.po_break_down_id, c.country_id, c.country_ship_date, c.shiping_status, sum(c.order_quantity) as po_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name in($manufacturing_company) and c.shiping_status!=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.country_ship_date<'$today' group by c.po_break_down_id, c.country_id, c.country_ship_date, c.shiping_status order by c.country_ship_date";
        $resultPend=sql_select($pendingData);
        foreach($resultPend as $pendRow)
        {
            $pend_month=date("Y-m",strtotime($pendRow[csf('country_ship_date')]));
            if($pendRow[csf('shiping_status')]==2) $exFactory_qnty=$exFactory_arr[$pendRow[csf('po_break_down_id')]][$pendRow[csf('country_id')]]; else $exFactory_qnty=0;
            $pending_qty=$pendRow[csf('po_qnty')]-$exFactory_qnty;
            $pending_month_data_array[$pend_month]+=$pending_qty;
			
			$f+=$pendRow[csf('po_qnty')];
			$g+=$exFactory_qnty;
        }
		
        foreach($pending_month_data_array as $key=>$value)
        {
            $pend_month=date("M",strtotime($key))." '".date("y",strtotime($key));
            $pending_month_array[]=$pend_month;
            $pending_qty_array[]=$value;
        }
		 //var_dump($exFactory_val_array);die;
        
        $booked_array= json_encode($booked_array);
        $order_val_array= json_encode($order_val_array); 
        $produced_array= json_encode($produced_array); 
        $delivery_array= json_encode($delivery_array); 
        $exFactory_val_array= json_encode($exFactory_val_array); 
        $capacity_array= json_encode($capacity_array);
        $pending_month_array= json_encode($pending_month_array);
        $pending_qty_array= json_encode($pending_qty_array);
		
		//$month_caption_arr= json_encode($month_caption_arr);
	//----------------------------------------	
       
	 	$sql ="select b.date_name,b.qty from ppl_order_allocation_mst a,ppl_order_allocation_dtls b where a.id=b.mst_id and a.company_id  in($manufacturing_company) and b.date_format_name between '$str_date' and '$lst_date'"; 
	   $planDataArr=sql_select($sql);
        foreach($planDataArr as $rows)
        {
			$date_name=date("d-M", strtotime($rows[csf('date_name')]));
			$orderAllocationArr[$date_name]+=$rows[csf('qty')];
		}
		//var_dump($orderAllocationArr);
		
	   for($j=0;$j<$datediff;$j++)
        {
			$newdate =add_date($str_date,$j);
			$newdate=date("d-M", strtotime($newdate));
			
			$TotalPlannedOutput[]=$orderAllocationArr[$newdate];
			$cummilitivePlannedOutput[]=array_sum($TotalPlannedOutput);
			
			$newdate=date("Y-m-d", strtotime($newdate));
			$actualPlannedOutput[]= $sewProdArr[$newdate];
			$cummilitiveActualOutput[]=array_sum($actualPlannedOutput);
        } 
		
        $totalPlannedOutput= json_encode($TotalPlannedOutput);
        $actualPlannedOutput= json_encode($actualPlannedOutput);
        $cummilitivePlannedOutput= json_encode($cummilitivePlannedOutput);
        $cummilitiveActualOutput= json_encode($cummilitiveActualOutput);
		
	
		
    ?>
    <script>
       

		var line_bar_data1= {
			type: 'bar',
			data: {
			  labels: <? echo $month_array; ?>,
			  datasets: [{
				  label: ["Capacity"],
				  type: "bar",
				  backgroundColor: "#FFA500",
				  data:<? echo $capacity_array; ?>,
				  fill: false
				},
				{
				  label: ["Booked"],
				  type: "bar",
				  backgroundColor: "#FF0000",
				  data:<? echo $booked_array; ?>,
				  fill: false
				},
				{
				  label: ["Produced"],
				  type: "bar",
				  backgroundColor: "#0000FF",
				  data:<? echo $produced_array; ?>,
				  fill: false
				},
				{
				  label: ["Delivery"],
				  type: "bar",
				  backgroundColor: "#00AA00",
				  data:<? echo $delivery_array; ?>,
				  fill: false
				}
			  ]
			},
			options: {
			  title: {
				display: true,
				text: 'Capacity, Booked, Produced and Delivery In SMV'
			  },
			  legend: { display: true }
			}
		}		
		
		
		var line_bar_data2= {
			type: 'bar',
			data: {
			  labels: <? echo $month_array; ?>,
			  datasets: [{
				  label: "Order In Hand",
				  type: "bar",
				  backgroundColor: "#FF0000",
				  data:<? echo $order_val_array; ?>,
				  fill: false
				},
				{
				  label: "Delivery",
				  type: "bar",
				  backgroundColor: "#0000FF",
				  data:<? echo $exFactory_val_array; ?>,
				  fill: false
				}
			  ]
			},
			options: {
			  title: {
				display: true,
				text: 'Order In Hand & Delivery'
			  },
			  legend: { display: true }
			}
		}  
  
  
  
		var line_bar_data3= {
			type: 'bar',
			data: {
			  labels: <? echo $month_array; ?>,
			  datasets: [{
				  label: "Delivery Pending",
				  type: "bar",
				  backgroundColor: "#8e5ea2",
				  data:<? echo $pending_qty_array; ?>,
				  fill: false
				}
			  ]
			},
			options: {
			  title: {
				display: true,
				text: 'Delivery Pending'
			  },
			  legend: { display: true }
			}
		}
  
  
		var line_bar_data4= {
			type: 'bar',
			data: {
			  labels: <? echo $date_array; ?>,
			  datasets: [{
				  label: "Trend",
				  type: "line",
				  borderColor: "#FF3300",
				  data:<? echo $tpd_arr; ?>,
				  fill: false
				},
				{
				  label: "Garments Production",
				  type: "line",
				  borderColor: "#0000FF",
				  data:<? echo $sew_prod_arr; ?>,
				  fill: false
				}
			  ]
			},
			options: {
			  title: {
				display: true,
				text: 'Garments Production Trend'
			  },
			  legend: { display: true }
			}
		}
  
  
		var line_bar_data5= {
			type: 'bar',
			data: {
			  labels: <? echo $date_array; ?>,
			  datasets: [{
				  label: "Sewing Target Achieve Trend",
				  type: "line",
				  borderColor: "#00ff00",
				  data:<? echo $sew_target_achv_trend_arr; ?>,
				  fill: false
				}
			  ]
			},
			options: {
			  title: {
				display: true,
				text: 'Sewing Target Achieve Trend In %'
			  },
			  legend: { display: true }
			}
		}
  
		
		var line_bar_data6= {
			type: 'bar',
			data: {
			  labels: <? echo $date_array; ?>,
			  datasets: [{
				  label: "Sewing Efficiency Trend In",
				  type: "line",
				  borderColor: "#FF0000",
				  data:<? echo $effi_perc_arr; ?>,
				  fill: false
				}
			  ]
			},
			options: {
			  title: {
				display: true,
				text: 'Sewing Efficiency Trend In %'
			  },
			  legend: { display: true }
			}
		}
        
		
		var line_bar_data7= {
			type: 'bar',
			data: {
			  labels: <? echo $date_array; ?>,
			  datasets: [{
				  label: "Cummilitive Planned Output",
				  type: "line",
				  borderColor: "#8e5ea2",
				  data: <? echo $cummilitivePlannedOutput;?>,
				  fill: false
				}, {
				  label: "Cummilitive Actula Output",
				  type: "line",
				  borderColor: "#3e95cd",
				  data: <? echo $cummilitiveActualOutput;?>,
				  fill: false,
				  borderDash: [3,3]
				}, {
				  label: "Total Planned Output",
				  type: "bar",
				  backgroundColor: "#8e5ea2",
				  data: <? echo $totalPlannedOutput;?>
				}, {
				  label: "Acatual Output (Daily)",
				  type: "bar",
				  backgroundColor: "#3e95cd",
				  backgroundColorHover: "#3e95cd",
				  data:<? echo $actualPlannedOutput;?>
				}
			  ]
			},
			options: {
			  /*legend: { display: false },*/
			  title: {
				display: true,
				text: 'Plan vs Production'
			  },
			  legend: { display: true }
			}
		}
		

       
		
		new Chart(document.getElementById("canvas"),line_bar_data1);
		new Chart(document.getElementById("canvas2"),line_bar_data2);
		new Chart(document.getElementById("canvas3"),line_bar_data3);
		new Chart(document.getElementById("canvas4"),line_bar_data4);
		new Chart(document.getElementById("canvas5"),line_bar_data5);
		new Chart(document.getElementById("canvas6"),line_bar_data6);
		new Chart(document.getElementById("canvas7"),line_bar_data7);
    </script>
 
 
 
<?
function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}
?>
