<?
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.others.php');

session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$company_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');

if($action=="report_generate")
{
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$from_year 		= str_replace("'","",$cbo_from_year);
	$to_year 		= str_replace("'","",$cbo_to_year);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$type 			= str_replace("'","",$type);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$from_year);
	$exlastYear 	= explode('-',$to_year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exlastYear[1];
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$fiscal_year_arr = array();
	$j=12;
	$i=1;
	$startDate =''; 
	$endDate ="";
	/*for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($firstYear<$lastYear)
			{			
				$fiscal_year = $firstYear.'-'.($firstYear+1);
				$fiscal_year_arr[$fiscal_year] = $fiscal_year;
			}

			if($i==1) $startDate=$firstYear.'-7-1';

			if($k==7)
			{
				$endDate=($firstYear+1).'-'.($k-1).'-30';
			}
			$i++;
		}
	}*/
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			//$fiscal_year='';
			if($firstYear<$lastYear)
			{
				$fiscal_year=$firstYear.'-'.($firstYear+1);
				$monthYr=''; $fstYr=$lstYr="";
				$fstYr=date("d-M-Y",strtotime(($firstYear.'-7-1')));
				$lstYr=date("d-M-Y",strtotime((($firstYear+1).'-6-30')));
				
				$monthYr=$fstYr.'_'.$lstYr;
				
				$yearMonth_arr[$fiscal_year]=$monthYr;
				$i++;
			}
		}
	}
	// print_r($yearMonth_arr);die();
	// die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'=='.$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	// ============================ for gmts finishing =============================	
	$sql_fin_prod = "SELECT a.location,a.po_break_down_id,to_char(a.production_date,'YYYY') as year,sum(b.production_qnty) as qty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company=$cbo_company_id and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type=8 and a.location is not null and a.location <> 0 group by a.location,a.po_break_down_id,a.production_date order by a.location";
	// echo $sql_fin_prod;die();
	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
	}

	// ========================= for kniting ======================
	$sql_kniting_dyeing = "SELECT b.po_breakdown_id,b.entry_form,to_char(c.receive_date,'YYYY') as year,sum(a.grey_receive_qnty) as qty from pro_grey_prod_entry_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company=$cbo_company_id and c.receive_date between '$startDate' and '$endDate' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) group by b.po_breakdown_id,b.entry_form,c.receive_date order by b.entry_form";
	// echo $sql_kniting_dyeing;die();
	$sql_kniting_dyeing_res = sql_select($sql_kniting_dyeing);
	foreach ($sql_kniting_dyeing_res as $val) 
	{
		$po_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
	}
	// ========================= for dyeing ======================
	$sql_dyeing.="SELECT b.po_breakdown_id";
	foreach($yearMonth_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_dyeing.=", SUM(CASE WHEN c.receive_date between '$exydata[0]' and '$exydata[1]' THEN a.receive_qnty END) AS m$fyear ";
	}
	$sql_dyeing.=" from pro_finish_fabric_rcv_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company=$cbo_company_id and c.receive_date between '$startDate' and '$endDate' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(7) group by b.po_breakdown_id,b.entry_form,c.receive_date order by b.entry_form";

	/*$sql_dyeing = "SELECT b.po_breakdown_id,b.entry_form,to_char(c.receive_date,'YYYY') as year,sum(a.receive_qnty) as qty from pro_finish_fabric_rcv_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company=$cbo_company_id and c.receive_date between '$startDate' and '$endDate' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(7) group by b.po_breakdown_id,b.entry_form,c.receive_date order by b.entry_form";*/
	echo $sql_dyeing;die();
	$sql_dyeing_res = sql_select($sql_dyeing);
	foreach ($sql_dyeing_res as $val) 
	{
		$po_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
	}

	
	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	$cm_sql = "SELECT a.cm_cost,b.id from wo_pre_cost_dtls a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
	}
	$condition= new condition();
	// $condition->company_name("=$cbo_company_id");

	if(isset($poIds))
	{
		$condition->po_id_in($poIds);
	}
	$condition->init();
	// var_dump($condition);

	$conversion= new conversion($condition);
	//echo $conversion->getQuery(); die;
	$conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
	//echo "<pre>";print_r($conversion_costing_arr);die();
	$conversion_qty_arr=$conversion->getQtyArray_by_orderAndProcess();

	// =========================== getting subcon order qty ====================================
	/*$sql_sub = "SELECT to_char(b.order_rcv_date,'YYYY') as year,a.currency_id, b.main_process_id,b.process_id,b.order_quantity from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=238 and b.order_rcv_date between '$startDate' and '$endDate' and b.main_process_id in(2,4)";
	// echo $sql_sub;die();
	$sql_sub_res = sql_select($sql_sub);*/

	// ======================================= getting subcontact data =================================
	$sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2 order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];

	//==================================== subcon order data =============================
	$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	// print_r($order_wise_rate);

	// =================================== subcon kniting =============================
	$sql_sub_knit = "SELECT b.order_id, to_char(a.product_date,'YYYY') as year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=159 and a.knitting_company=$cbo_company_id and a.product_date between '$startDate' and '$endDate' and a.product_type=2 and b.process='1'";
	$sql_sub_knit_res = sql_select($sql_sub_knit);
	// echo $sql_sub_knit;die();
	// =================================== subcon dyeing =============================
	$sql_sub_dye = "SELECT b.order_id,to_char(a.product_date,'YYYY') as year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id=$cbo_company_id and a.product_date between '$startDate' and '$endDate' and a.product_type=4";
	$sql_sub_dye_res = sql_select($sql_sub_dye);
	// echo $sql_sub_dye;die();

	$main_array = array();
	$locationArray = array();
	$po_id_array = array();
	$year_location_qty_array = array();
	$process_array = array(1,30);
	$kniting_process_array = array(1);
	foreach ($sql_fin_prod_res as $val) 
	{	
		$fiscalYear=$val[csf("year")].'-'.($val[csf("year")]+1);

		$main_array[$fiscalYear]['location'] = $val[csf('location')];
		$main_array[$fiscalYear]['qty'] += $val[csf('qty')];
		$locationArray[$val[csf('location')]] = $val[csf('location')];
		// ======================== calcutate finishing amount ====================
		if($cm_cost_array[$val[csf('po_break_down_id')]]>0)
		{
			$cm_cost=$cm_cost_array[$val[csf('po_break_down_id')]];
			// echo $cm_cost."<br>";
			// $po_qty=$po_qty_array[$val[csf('po_break_down_id')]];
			$cm_avg_cost=$cm_cost/12;
			$finish_cost=$cm_avg_cost*$val[csf('qty')];
		}
		$year_location_qty_array[$fiscalYear][$val[csf('location')]]['finishing'] += $finish_cost;			
	}
	// print_r($year_location_qty_array);
	// ======================== calcutate kniting amount ====================
	$dyeing_kniting_qty_array = array();
	foreach ($sql_kniting_dyeing_res as $val) 
	{
		$dyeing_cost=0;
		$kniting_cost=0;					
		if($val[csf('po_breakdown_id')]>0)
		{
			$kniting_cost = array_sum($conversion_costing_arr[$val[csf('po_breakdown_id')]][1]);
			$kniting_qty = array_sum($conversion_qty_arr[$val[csf('po_breakdown_id')]][1]);	
			$avg_kniting_rate = $kniting_cost/$kniting_qty;
		}	
		$knitingCost =$avg_kniting_rate*$val[csf('qty')];		
		$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);
		$dyeing_kniting_qty_array[$fiscalDyeingYear][2] += $knitingCost;
		$main_array[$fiscalDyeingYear]['kniting'] += $knitingCost;
		
	}
	// print_r($main_array);die();
	// ======================== calcutate dyeing amount ====================
	
	foreach ($sql_dyeing_res as $val) 
	{
		

		$dyeing_cost=0;
		$kniting_cost=0;

		foreach ($conversion_cost_head_array as $key => $value) 
		{
			if(!in_array($key, $process_array ))
			{
				$dyeing_cost += $conversion_costing_arr[$val[csf('po_breakdown_id')]][$key][12];
				$dyeing_qty += $conversion_qty_arr[$val[csf('po_breakdown_id')]][$key][12];				
			}
		}
		$avg_dyeing_rate = $dyeing_cost/$dyeing_qty;					
		// $dyeing_cost =$avg_dyeing_rate*$val[csf('qty')];		
		// $fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);
		// $dyeing_kniting_qty_array[$fiscalDyeingYear][7] += $dyeing_cost;
		// $main_array[$fiscalDyeingYear]['dyeing'] += $dyeing_cost;

		foreach($yearMonth_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$myear='m'.$nyear;
			$dyeing_cost =$avg_dyeing_rate*$row[csf($myear)];		
			$main_array[$fyear]['dyeing']=$dyeing_cost;
		}
		
	}	

	// ========================== subcontact ===============================
	$subcon_ord_qty = array();
	foreach ($sql_sub_res as $val) 
	{	
		$subFiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);
		$subcon_ord_qty[$subFiscalDyeingYear][$val[csf('main_process_id')]] += $val[csf('order_quantity')];
		
	}
	foreach ($sql_sub_knit_res as $val) 
	{								
		$subKnit_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf('qty')];	
		// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
		$subKnit_costUSD = $subKnit_cost/$rate;
		$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);
		$main_array[$fiscalDyeingYear]['subKnit'] += $subKnit_costUSD;
		
	}
	foreach ($sql_sub_dye_res as $val) 
	{							
		$subDye_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf('qty')];	
		// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
		$subDye_costUSD = $subDye_cost/$rate;	
		$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);
		$main_array[$fiscalDyeingYear]['subDye'] += $subDye_costUSD;		
	}

	unset($sql_fin_prod_res);
	unset($sql_kniting_dyeing_res);
	unset($sql_dyeing_res);
	unset($sql_sub_dye_res);
	unset($sql_sub_knit_res);
	$tbl_width = 760+(count($locationArray)*120);
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->

	    <table width="<? echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="9" align="center" ><strong style="font-size:19px"><?php echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="9" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'yearly_revenue_report', '')"> -<b>Yearly Revenue Report <? echo $from_year; ?> To <? echo $to_year; ?></b></h3>
	    <div id="yearly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
		<!-- <caption></caption> -->
	        <thead>
	            <th width="60">Date</th><!-- 
	            <th width="120">Ratanpur gmt</th>
	            <th width="120">Ashulia gmt</th> -->
	            <?
	            foreach ($locationArray as $loc_id => $val) 
	            {
	            	?>
	            	<th width="120" title="<? echo $loc_id;?>"><?  echo ucfirst($location_library[$loc_id]);?> gmt</th>
	            	<?
	            }
	            ?>
	            <th width="100">Total gmt</th>
	            <th width="100">Dyeing</th>
	            <th width="100">Knitting </th>
	            <th width="100">Printing</th>
	            <th width="100">Embroidery</th>
	            <th width="100">Washing</th>
	            <th width="100">Total</th>
	        </thead>
		        <tbody>   
		        <?
		        	$total_gmts_array 	= array();
		        	$gr_fiscal_total 	= 0;
		        	$gr_dyeing_total 	= 0;
		        	$gr_kniting_total 	= 0;
		        	$gr_year_total 		= 0;
		        	foreach ($fiscal_year_arr as $year => $val) 
		        	{
		        		$fiscal_total = 0;		        		
			        	?>     
				        <tr bgcolor="<? echo $bgcolor; ?>">
				            <td><a href="javascript:void()" onclick="report_generate_by_year('<? echo $year?>')"><? echo $year;?></a></td>
				            <?
				            $total_gmts = 0;
				            foreach ($locationArray as $loc_id => $location_name) 
				            {
				            	?>
				            	<td align="right" title="(Total CM/PO Qty)*Prod Qty"><?  echo number_format($year_location_qty_array[$year][$loc_id]['finishing'],2);?></td>
				            	<?
				            	$total_gmts += $year_location_qty_array[$year][$loc_id]['finishing'];
				            	$gr_fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing'];
				            	$total_gmts_array[$loc_id] += $year_location_qty_array[$year][$loc_id]['finishing'];
				            	$fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing'];
				            }
				            $year_total = $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting'];
				            ?>
				            <td align="right"><? echo number_format($total_gmts);?></td>
				            <td align="right"><? echo number_format($main_array[$year]['dyeing']+$main_array[$year]['subDye'],2); ?></td>
				            <td align="right"><? echo number_format($main_array[$year]['kniting']+$main_array[$year]['subKnit'],2); ?></td>
				            <td align="right"><? //echo number_format($a,2); ?></td>
				            <td align="right"><? //echo number_format($a,2); ?></td>
				            <td align="right"><? //echo number_format($a,2); ?></td>
				            <td align="right"><? echo number_format($year_total,2); ?></td>
				        </tr>
				        <?
				        $gr_dyeing_total += $main_array[$year]['dyeing']+$main_array[$year]['subDye'];
				        $gr_kniting_total += $main_array[$year]['kniting']+$main_array[$year]['subKnit'];
				        $gr_year_total += $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting']+$main_array[$year]['subDye']+$main_array[$year]['subKnit'];
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	            <?
	            foreach ($locationArray as $loc_id => $val) 
	            {
	            	?>
	            	<th><?  echo number_format($total_gmts_array[$loc_id],2);?></th>
	            	<?
	            }
	            ?>
	            <th><? echo number_format($gr_fiscal_total,2); ?></th>
	            <th><? echo number_format($gr_dyeing_total,2); ?></th>
	            <th><? echo number_format($gr_kniting_total,2); ?></th>
	            <th><? //echo number_format($a,2); ?></th>
	            <th><? //echo number_format($a,2); ?></th>
	            <th><? //echo number_format($a,2); ?></th>
	            <th><? echo number_format($gr_year_total,2); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
			
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
}  

if($action=="report_generate_by_year")
{
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	// $from_year 		= str_replace("'","",$cbo_from_year);
	// $to_year 		= str_replace("'","",$cbo_to_year);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$year 	= str_replace("'","",$year);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	// echo $firstYear."==".$lastYear;die();
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$j=12;
	$i=1;
	$startDate =''; 
	$endDate ="";
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($i==1 && $k>6)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
			else if ($i!=1 && $k<7)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
		}
		$i++;
	}
	// echo "<pre>";print_r($fiscalMonth_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate."==".$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	// ============================ for gmts finishing =============================	
	$sql_fin_prod = "SELECT a.location,a.po_break_down_id,to_char(a.production_date,'MON-YYYY') as month_year,sum(b.production_qnty) as qty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company=$cbo_company_id and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type=8 and a.location is not null and a.location <> 0 group by a.location,a.po_break_down_id,a.production_date order by a.location";
	// echo $sql_fin_prod;die();
	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
	}
	// ========================= for kniting ======================
	$sql_kniting_dyeing = "SELECT b.po_breakdown_id,b.entry_form,to_char(c.receive_date,'MON-YYYY') as month_year,sum(a.grey_receive_qnty) as qty from pro_grey_prod_entry_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company=$cbo_company_id and c.receive_date between '$startDate' and '$endDate' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) group by b.po_breakdown_id,b.entry_form,c.receive_date order by b.entry_form";
	// echo $sql_kniting_dyeing;die();
	$sql_kniting_dyeing_res = sql_select($sql_kniting_dyeing);
	foreach ($sql_kniting_dyeing_res as $val) 
	{
		$po_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
	}
	
	// ========================= for dyeing ======================
	$sql_dyeing = "SELECT b.po_breakdown_id,b.entry_form,to_char(c.receive_date,'MON-YYYY') as month_year,sum(a.receive_qnty) as qty from pro_finish_fabric_rcv_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company=$cbo_company_id and c.receive_date between '$startDate' and '$endDate' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(7) group by b.po_breakdown_id,b.entry_form,c.receive_date order by b.entry_form";
	// echo $sql_dyeing;die();
	$sql_dyeing_res = sql_select($sql_dyeing);
	foreach ($sql_dyeing_res as $val) 
	{
		$po_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
	}

	$poIds = implode(",", $po_id_array);
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	$cm_sql = "SELECT a.cm_cost,b.id from wo_pre_cost_dtls a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
	}
	$condition= new condition();
	// $condition->company_name("=$cbo_company_id");

	if(isset($poIds))
	{
	  	$condition->po_id_in($poIds);
	}
	$condition->init();
	// var_dump($condition);

	$conversion= new conversion($condition);
	//echo $conversion->getQuery(); die;
	$conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
	//echo "<pre>";print_r($conversion_costing_arr);die();
	$conversion_qty_arr=$conversion->getQtyArray_by_orderAndProcess();

	// ======================================= getting subcontact data =================================
	$sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2 order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];


	$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	// =================================== subcon kniting =============================
	$sql_sub_knit = "SELECT b.order_id, to_char(a.product_date,'MON-YYYY') as month_year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=159 and a.knitting_company=$cbo_company_id and a.product_date between '$startDate' and '$endDate' and a.product_type=2 and b.process='1'";
	$sql_sub_knit_res = sql_select($sql_sub_knit);
	// echo $sql_sub_knit;die();
	// =================================== subcon dyeing =============================
	$sql_sub_dye = "SELECT b.order_id, to_char(a.product_date,'MON-YYYY') as month_year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id=$cbo_company_id and a.product_date between '$startDate' and '$endDate' and a.product_type=4";
	$sql_sub_dye_res = sql_select($sql_sub_dye);
	// echo $sql_sub_dye;die();
	$main_array = array();
	$locationArray = array();
	$po_id_array = array();
	$year_location_qty_array = array();
	$process_array = array(1,30);
	$kniting_process_array = array(1);
	foreach ($sql_fin_prod_res as $val) 
	{	
		$fiscalYear=$val[csf("month_year")];

		$main_array[$fiscalYear]['location'] = $val[csf('location')];
		$main_array[$fiscalYear]['qty'] += $val[csf('qty')];
		$locationArray[$val[csf('location')]] = $val[csf('location')];
		// ======================== calcutate finishing amount ====================
		if($cm_cost_array[$val[csf('po_break_down_id')]]>0)
		{
			$cm_cost=$cm_cost_array[$val[csf('po_break_down_id')]];
			// $po_qty=$po_qty_array[$val[csf('po_break_down_id')]];
			$cm_avg_cost=$cm_cost/12;
			$finish_cost=$cm_avg_cost*$val[csf('qty')];
		}
		$year_location_qty_array[$fiscalYear][$val[csf('location')]]['finishing'] += $finish_cost;			
	}
	// print_r($year_location_qty_array);
	// ======================== calcutate kniting amount ====================
	$dyeing_kniting_qty_array = array();
	foreach ($sql_kniting_dyeing_res as $val) 
	{
		$dyeing_cost=0;
		$kniting_cost=0;		
		if($val[csf('po_breakdown_id')]>0)
		{
			$kniting_cost = array_sum($conversion_costing_arr[$val[csf('po_breakdown_id')]][1]);
			$kniting_qty = array_sum($conversion_qty_arr[$val[csf('po_breakdown_id')]][1]);	
			$avg_kniting_rate = $kniting_cost/$kniting_qty;
		}	
		$knitingCost =$avg_kniting_rate*$val[csf('qty')];		
		$fiscalDyeingYear=$val[csf("month_year")];
		$dyeing_kniting_qty_array[$fiscalDyeingYear][2] += $knitingCost;
		$main_array[$fiscalDyeingYear]['kniting'] += $knitingCost;
	}
	// print_r($main_array);die();
	// ======================== calcutate dyeing amount ====================
	foreach ($sql_dyeing_res as $val) 
	{
		$dyeing_cost=0;
		
		foreach ($conversion_cost_head_array as $key => $value) 
		{
			if(!in_array($key, $process_array))
			{
				$dyeing_cost += $conversion_costing_arr[$val[csf('po_breakdown_id')]][$key][12];
				$dyeing_qty += $conversion_qty_arr[$val[csf('po_breakdown_id')]][$key][12];				
			}
		}
		$avg_dyeing_rate = $dyeing_cost/$dyeing_qty;					
		$dyeing_cost =$avg_dyeing_rate*$val[csf('qty')];		
		$fiscalDyeingYear=$val[csf("month_year")];
		$dyeing_kniting_qty_array[$fiscalDyeingYear][7] += $dyeing_cost;
		$main_array[$fiscalDyeingYear]['dyeing'] += $dyeing_cost;
		
	}


	// ========================== subcontact ===============================

	foreach ($sql_sub_knit_res as $val) 
	{								
		// $subKnit_cost =$rate*$val[csf('qty')];	
		$subKnit_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf('qty')];	
		// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
		$subKnit_costUSD = $subKnit_cost/$rate;	
		$fiscalDyeingYear=$val[csf("month_year")];
		$main_array[$fiscalDyeingYear]['subKnit'] += $subKnit_costUSD;
		
	}
	foreach ($sql_sub_dye_res as $val) 
	{							
		$subDye_cost =$rate*$val[csf('qty')];			
		$subDye_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf('qty')];	
		// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
		$subDye_costUSD = $subDye_cost/$rate;			
		$fiscalDyeingYear=$val[csf("month_year")];
		$main_array[$fiscalDyeingYear]['subDye'] += $subDye_costUSD;		
	}
	unset($sql_fin_prod_res);
	unset($sql_kniting_dyeing_res);
	unset($sql_dyeing_res);
	unset($sql_sub_dye_res);
	unset($sql_sub_knit_res);
	// echo "<pre>" ;print_r($main_array);die();
	$tbl_width = 760+(count($locationArray)*120);
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->	   
	    
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'monthly_revenue_report', '')"> -<b>Monthly Revenue Report <? echo $year; ?></b></h3>
	    <div id="monthly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
		<!-- <caption style="padding-top: 15px;"><b>Monthly Revenue Report <? echo $year; ?></b></caption> -->
	        <thead>
	            <th width="60">Date</th><!-- 
	            <th width="120">Ratanpur gmt</th>
	            <th width="120">Ashulia gmt</th> -->
	            <?
	            foreach ($locationArray as $loc_id => $val) 
	            {
	            	?>
	            	<th width="120" title="<? echo $loc_id;?>"><?  echo ucfirst($location_library[$loc_id]);?> gmt</th>
	            	<?
	            }
	            ?>
	            <th width="100">Total gmt</th>
	            <th width="100">Dyeing</th>
	            <th width="100">Knitting </th>
	            <th width="100">Printing</th>
	            <th width="100">Embroidery</th>
	            <th width="100">Washing</th>
	            <th width="100">Total</th>
	        </thead>
		        <tbody>   
		        <?
		        	$total_gmts_array 	= array();
		        	$gr_fiscal_total 	= 0;
		        	$gr_dyeing_total 	= 0;
		        	$gr_kniting_total 	= 0;
		        	$gr_year_total 		= 0;
		        	foreach ($fiscalMonth_arr as $year => $val) 
		        	{
		        		$year_ex = explode("-", $year);
		        		$fiscal_total = 0;		        		
			        	?>     
				        <tr bgcolor="<? echo $bgcolor; ?>">
				            <td><a href="javascript:void()" onclick="report_generate_by_month('<? echo $year?>')"><? echo date('F-y',strtotime($year));?></a></td>
				            <?
				            $total_gmts = 0;
				            foreach ($locationArray as $loc_id => $location_name) 
				            {
				            	?>
				            	<td align="right" title="(Total CM/PO Qty)*Prod Qty"><?  echo number_format($year_location_qty_array[$year][$loc_id]['finishing'],2);?></td>
				            	<?
				            	$total_gmts += $year_location_qty_array[$year][$loc_id]['finishing'];
				            	$gr_fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing'];
				            	$total_gmts_array[$loc_id] += $year_location_qty_array[$year][$loc_id]['finishing'];
				            	$fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing'];
				            }
				            $year_total = $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting']+$main_array[$year]['subDye']+$main_array[$year]['subKnit'];
				            // echo "string==".$main_array[$year]['subDye'];
				            ?>
				            <td align="right"><? echo number_format($total_gmts);?></td>
				            <td align="right"><? echo number_format($main_array[$year]['dyeing']+$main_array[$year]['subDye'],2); ?></td>
				            <td align="right"><? echo number_format($main_array[$year]['kniting']+$main_array[$year]['subKnit'],2); ?></td>
				            <td align="right"><? //echo number_format($a,2); ?></td>
				            <td align="right"><? //echo number_format($a,2); ?></td>
				            <td align="right"><? //echo number_format($a,2); ?></td>
				            <td align="right"><? echo number_format($year_total,2); ?></td>
				        </tr>
				        <?
				        $gr_dyeing_total += $main_array[$year]['dyeing']+$main_array[$year]['subDye'];
				        $gr_kniting_total += $main_array[$year]['kniting']+$main_array[$year]['subKnit'];
				        $gr_year_total += $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting']+$main_array[$year]['subDye']+$main_array[$year]['subKnit'];
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	            <?
	            foreach ($locationArray as $loc_id => $val) 
	            {
	            	?>
	            	<th><?  echo number_format($total_gmts_array[$loc_id],2);?></th>
	            	<?
	            }
	            ?>
	            <th><? echo number_format($gr_fiscal_total,2); ?></th>
	            <th><? echo number_format($gr_dyeing_total,2); ?></th>
	            <th><? echo number_format($gr_kniting_total,2); ?></th>
	            <th><? //echo number_format($a,2); ?></th>
	            <th><? //echo number_format($a,2); ?></th>
	            <th><? //echo number_format($a,2); ?></th>
	            <th><? echo number_format($gr_year_total,2); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
			
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
}

if($action=="report_generate_by_month")
{
	// print_r($_REQUEST);die();
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	// $from_year 		= str_replace("'","",$cbo_from_year);
	// $to_year 		= str_replace("'","",$cbo_to_year);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$year 	= str_replace("'","",$month_year);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	$time = date('m,Y',strtotime($year));
	$time = explode(',', $time);
	$numberOfDays = cal_days_in_month(CAL_GREGORIAN, $time[0],$time[1]);
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$j=12;
	$i=1;
	$days_arr = array();
	for ($i=1; $numberOfDays >= $i; $i++) 
	{ 
		$day = date('M',strtotime($year));
		$dayMonth = $i.'-'.$day;
		$dayMonth = date('d-M',strtotime($dayMonth));
		$days_arr[strtoupper($dayMonth)] = strtoupper($dayMonth);
	}
	// print_r($days_arr);die();
	$startDate =''; 
	$endDate ="";
	$startDate=date("d-M-Y",strtotime(($firstYear.'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));

	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	// ============================ for gmts finishing =============================
	// echo date('d-M');
	$sql_fin_prod = "SELECT a.location,a.po_break_down_id,to_char(a.production_date,'DD-MON') as month_year,sum(b.production_qnty) as qty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company=$cbo_company_id and to_char(a.production_date,'MON-YYYY')='$year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type=8 and a.location is not null and a.location <> 0 group by a.location,a.po_break_down_id,a.production_date order by a.location";
	// echo $sql_fin_prod;die();
	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
	}

	// ========================= for kniting ======================
	$sql_kniting_dyeing = "SELECT b.po_breakdown_id,b.entry_form,to_char(c.receive_date,'DD-MON') as month_year,sum(a.grey_receive_qnty) as qty from pro_grey_prod_entry_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company=$cbo_company_id and to_char(c.receive_date,'MON-YYYY')='$year' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) group by b.po_breakdown_id,b.entry_form,c.receive_date order by b.entry_form";
	// echo $sql_kniting_dyeing;die();
	$sql_kniting_dyeing_res = sql_select($sql_kniting_dyeing);
	foreach ($sql_kniting_dyeing_res as $val) 
	{
		$po_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
	}

	// ========================= for dyeing ======================
	$sql_dyeing = "SELECT b.po_breakdown_id,b.entry_form,to_char(c.receive_date,'DD-MON') as month_year,sum(a.receive_qnty) as qty from pro_finish_fabric_rcv_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company=$cbo_company_id and to_char(c.receive_date,'MON-YYYY')='$year' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(7) group by b.po_breakdown_id,b.entry_form,c.receive_date order by b.entry_form";
	// echo $sql_dyeing;die();
	$sql_dyeing_res = sql_select($sql_dyeing);
	foreach ($sql_dyeing_res as $val) 
	{
		$po_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
	}

	$poIds = implode(",", $po_id_array);
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	$cm_sql = "SELECT a.cm_cost,b.id from wo_pre_cost_dtls a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
	}
	$condition= new condition();
	// $condition->company_name("=$cbo_company_id");

	if(isset($poIds))
	{
	  	$condition->po_id_in($poIds);
	}
	$condition->init();
	// var_dump($condition);

	$conversion= new conversion($condition);
	//echo $conversion->getQuery(); die;
	$conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
	//echo "<pre>";print_r($conversion_costing_arr);die();
	$conversion_qty_arr=$conversion->getQtyArray_by_orderAndProcess();
	// ======================================= getting subcontact data =================================
	$sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2 order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];

	$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	// =================================== subcon kniting =============================
	$sql_sub_knit = "SELECT b.order_id, to_char(a.product_date,'DD-MON') as month_year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=159 and a.knitting_company=$cbo_company_id and to_char(a.product_date,'MON-YYYY')='$year' and a.product_type=2 and b.process='1'";
	$sql_sub_knit_res = sql_select($sql_sub_knit);

	// =================================== subcon dyeing =============================
	$sql_sub_dye = "SELECT b.order_id, to_char(a.product_date,'DD-MON') as month_year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id=$cbo_company_id and to_char(a.product_date,'MON-YYYY')='$year' and a.product_type=4";
	$sql_sub_dye_res = sql_select($sql_sub_dye);

	$main_array = array();
	$locationArray = array();
	$po_id_array = array();
	$year_location_qty_array = array();
	$process_array = array(1,30);
	$kniting_process_array = array(1);
	foreach ($sql_fin_prod_res as $val) 
	{	
		$fiscalYear=$val[csf("month_year")];

		$main_array[$fiscalYear]['location'] = $val[csf('location')];
		$main_array[$fiscalYear]['qty'] += $val[csf('qty')];
		$locationArray[$val[csf('location')]] = $val[csf('location')];
		// ======================== calcutate finishing amount ====================
		if($cm_cost_array[$val[csf('po_break_down_id')]]>0)
		{
			$cm_cost=$cm_cost_array[$val[csf('po_break_down_id')]];
			// $po_qty=$po_qty_array[$val[csf('po_break_down_id')]];
			$cm_avg_cost=$cm_cost/12;
			$finish_cost=$cm_avg_cost*$val[csf('qty')];
		}
		$year_location_qty_array[$fiscalYear][$val[csf('location')]]['finishing'] += $finish_cost;			
	}
	// print_r($year_location_qty_array);
	// ======================== calcutate kniting amount ====================
	$dyeing_kniting_qty_array = array();
	foreach ($sql_kniting_dyeing_res as $val) 
	{
		$kniting_qty=0;
		$kniting_cost=0;
					
		if($val[csf('po_breakdown_id')]>0)
		{
			$kniting_cost += array_sum($conversion_costing_arr[$val[csf('po_breakdown_id')]][1]);
			$kniting_qty += array_sum($conversion_qty_arr[$val[csf('po_breakdown_id')]][1]);
		}	
		$avg_kniting_rate = $kniting_cost/$kniting_qty;	
		// echo $kniting_cost;echo "=";echo $kniting_qty."=$avg_kniting_rate"."<br>";
		$knitingCost =$avg_kniting_rate*$val[csf('qty')];	
		// echo $knitingCost."<br>";	
		$fiscalDyeingYear=$val[csf("month_year")];
		$dyeing_kniting_qty_array[$fiscalDyeingYear][2] += $knitingCost;
		$main_array[$fiscalDyeingYear]['kniting'] += $knitingCost;
		
	}
	// die();
	// print_r($main_array);die();
	// ======================== calcutate dyeing amount ====================
	$dyeing_kniting_qty_array = array();
	foreach ($sql_dyeing_res as $val) 
	{
		$dyeing_cost=0;
		$dyeing_qty=0;
		
		foreach ($conversion_cost_head_array as $key => $value) 
		{
			if(!in_array($key, $process_array))
			{
				$dyeing_cost += array_sum($conversion_costing_arr[$val[csf('po_breakdown_id')]][$key]);
				$dyeing_qty += array_sum($conversion_qty_arr[$val[csf('po_breakdown_id')]][$key]);	
				// echo $dyeing_cost."==".$dyeing_qty."<br>";			
			}
		}
		$avg_dyeing_rate = $dyeing_cost/$dyeing_qty;	
		// echo $dyeing_cost."=".$dyeing_qty."=$avg_dyeing_rate"."<br>";				
		$dyeing_cost =$avg_dyeing_rate*$val[csf('qty')];
		// echo $val[csf('qty')];die();		
		$fiscalDyeingYear=$val[csf("month_year")];
		$dyeing_kniting_qty_array[$fiscalDyeingYear][7] += $dyeing_cost;
		$main_array[$fiscalDyeingYear]['dyeing'] += $dyeing_cost;
		
	}
	// ========================== subcontact ===============================
	foreach ($sql_sub_knit_res as $val) 
	{								
		// $subKnit_cost =$rate*$val[csf('qty')];	
		$subKnit_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf('qty')];	
		// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
		$subKnit_costUSD = $subKnit_cost/$rate;		
		$fiscalDyeingYear=$val[csf("month_year")];
		$main_array[$fiscalDyeingYear]['subKnit'] += $subKnit_costUSD;
		
	}
	foreach ($sql_sub_dye_res as $val) 
	{							
		$subDye_cost =$rate*$val[csf('qty')];		
		$subDye_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf('qty')];	
		// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
		$subDye_costUSD = $subDye_cost/$rate;			
		$fiscalDyeingYear=$val[csf("month_year")];
		$main_array[$fiscalDyeingYear]['subDye'] += $subDye_costUSD;		
	}
	// print_r($main_array);
	unset($sql_fin_prod_res);
	unset($sql_kniting_dyeing_res);
	unset($sql_dyeing_res);
	unset($sql_sub_dye_res);
	unset($sql_sub_knit_res);
	
	$tbl_width = 760+(count($locationArray)*120);
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->
	   
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'daily_revenue_report', '')"> -<b>Daily Revenue Report <? echo date('F-Y',strtotime($year)); ?></b></h3>
	    <div id="daily_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
		<!-- <caption style="padding-top: 15px;"><b>Daily Revenue Report <? echo date('F-Y',strtotime($year)); ?></b></caption> -->
	        <thead>
	            <th width="60">Date</th><!-- 
	            <th width="120">Ratanpur gmt</th>
	            <th width="120">Ashulia gmt</th> -->
	            <?
	            foreach ($locationArray as $loc_id => $val) 
	            {
	            	?>
	            	<th width="120" title="<? echo $loc_id;?>"><?  echo ucfirst($location_library[$loc_id]);?> gmt</th>
	            	<?
	            }
	            ?>
	            <th width="100">Total gmt</th>
	            <th width="100">Dyeing</th>
	            <th width="100">Knitting </th>
	            <th width="100">Printing</th>
	            <th width="100">Embroidery</th>
	            <th width="100">Washing</th>
	            <th width="100">Total</th>
	        </thead>
		        <tbody>   
		        <?
		        	$total_gmts_array 	= array();
		        	$gr_fiscal_total 	= 0;
		        	$gr_dyeing_total 	= 0;
		        	$gr_kniting_total 	= 0;
		        	$gr_year_total 		= 0;
		        	// ksort($main_array);
		        	foreach ($days_arr as $year => $val) 
		        	{
		        		$fiscal_total = 0;		        		
			        	?>     
				        <tr bgcolor="<? echo $bgcolor; ?>">
				            <td><? echo date('d-F',strtotime($year));?></td>
				            <?
				            $total_gmts = 0;
				            foreach ($locationArray as $loc_id => $location_name) 
				            {
				            	?>
				            	<td align="right" title="(Total CM/PO Qty)*Prod Qty"><?  echo number_format($year_location_qty_array[$year][$loc_id]['finishing'],2);?></td>
				            	<?
				            	$total_gmts += $year_location_qty_array[$year][$loc_id]['finishing'];
				            	$gr_fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing'];
				            	$total_gmts_array[$loc_id] += $year_location_qty_array[$year][$loc_id]['finishing'];
				            	$fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing'];
				            }
				            $year_total = $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting']+$main_array[$year]['subDye']+$main_array[$year]['subKnit'];
				            ?>
				            <td align="right"><? echo number_format($total_gmts);?></td>
				            <td align="right"><? echo number_format($main_array[$year]['dyeing']+$main_array[$year]['subDye'],2); ?></td>
				            <td align="right"><? echo number_format($main_array[$year]['kniting']+$main_array[$year]['subKnit'],2); ?></td>
				            <td align="right"><? //echo number_format($a,2); ?></td>
				            <td align="right"><? //echo number_format($a,2); ?></td>
				            <td align="right"><? //echo number_format($a,2); ?></td>
				            <td align="right"><? echo number_format($year_total,2); ?></td>
				        </tr>
				        <?
				        $gr_dyeing_total += $main_array[$year]['dyeing']+$main_array[$year]['subDye'];
				        $gr_kniting_total += $main_array[$year]['kniting']+$main_array[$year]['subKnit'];
				        $gr_year_total += $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting']+$main_array[$year]['subDye']+$main_array[$year]['subKnit'];
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	            <?
	            foreach ($locationArray as $loc_id => $val) 
	            {
	            	?>
	            	<th><?  echo number_format($total_gmts_array[$loc_id],2);?></th>
	            	<?
	            }
	            ?>
	            <th><? echo number_format($gr_fiscal_total,2); ?></th>
	            <th><? echo number_format($gr_dyeing_total,2); ?></th>
	            <th><? echo number_format($gr_kniting_total,2); ?></th>
	            <th><? //echo number_format($a,2); ?></th>
	            <th><? //echo number_format($a,2); ?></th>
	            <th><? //echo number_format($a,2); ?></th>
	            <th><? echo number_format($gr_year_total,2); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
			
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
}
?>