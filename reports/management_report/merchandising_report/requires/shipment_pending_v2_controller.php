<?
include('../../../../includes/common.php');

session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$company_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 0, "-- All Buyer --", $selected, "" );     	 
	exit();
}




if($action=="report_generate")
{
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	$type=str_replace("'","",$type);
	
	
	if($cbo_date_category==1){
		$select_fill="b.shipment_date as shipment_date";
		$date_con=" and b.shipment_date < '".date("d-M-Y",time())."'";
		$dateField="Original Ship Date";
		if($db_type==2 && $cbo_year>0){
			$year_con=" and to_char(b.shipment_date,'YYYY')=$cbo_year";
		}
		elseif($cbo_year>0){
			$year_con=" and date(b.shipment_date,'Y')=$cbo_year";
		}
	}
	else if($cbo_date_category==2){
		$select_fill="b.pub_shipment_date as shipment_date";
		$date_con=" and b.pub_shipment_date < '".date("d-M-Y",time())."'";
		$dateField="Publish Ship Date";
	
		if($db_type==2 && $cbo_year>0){
			$year_con=" and to_char(b.pub_shipment_date,'YYYY')=$cbo_year";
		}
		elseif($cbo_year>0){
			$year_con=" and date(b.pub_shipment_date,'Y')=$cbo_year";
		}
	
	}
	else if($cbo_date_category==3){
		$select_fill="c.country_ship_date as shipment_date";
		$date_con=" and c.country_ship_date < '".date("d-M-Y",time())."'";
		$dateField="Country Ship Date";
	
		if($db_type==2 && $cbo_year>0){
			$year_con=" and to_char(c.country_ship_date,'YYYY')=$cbo_year";
		}
		elseif($cbo_year>0){
			$year_con=" and date(c.country_ship_date,'Y')=$cbo_year";
		}
	}
		
	
	if($cbo_buyer_name){$buyer_com=" and a.buyer_name in($cbo_buyer_name)";}
	
	
	$order_sql="SELECT a.job_no, a.style_ref_no, a.company_name, a.buyer_name, a.ship_mode,a.total_set_qnty,a.set_smv,a.insert_date,
	a.update_date, a.order_uom,b.po_number,b.po_quantity,b.po_total_price,b.details_remarks,b.SHIPING_STATUS, c.po_break_down_id,c.order_quantity as po_quantity_pcs,c.plan_cut_qnty, $select_fill , c.order_total,c.item_number_id from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id and a.company_name=$cbo_company_id $buyer_cond and b.is_confirmed=1  AND b.shiping_status != 3 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0  AND c.status_active = 1 AND c.is_deleted = 0 $date_con $year_con $buyer_com  order by b.pub_shipment_date DESC";

// echo $order_sql;die;

	$order_sql_relsult=sql_select($order_sql);
	foreach( $order_sql_relsult as $row)
	{
		$key=date("M, Y",strtotime($row[csf('shipment_date')]));
		$po_avg_rate=($row[csf('po_total_price')]/$row[csf('po_quantity')])/$row[csf('total_set_qnty')];
		
		$dataArr['month_buyer_wise_po_id'][$key][$row[csf('buyer_name')]][$row[csf('po_break_down_id')]]=$row[csf('po_number')];
		$dataArr['po_id'][$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
		$dataArr['job_no'][$row[csf('po_break_down_id')]]=$row[csf('job_no')];
		$dataArr['remarks'][$row[csf('po_break_down_id')]]=$row[csf('details_remarks')];
		
		$dataArr['order_wise_po_qty_pcs'][$row[csf('po_break_down_id')]]+=$row[csf('po_quantity_pcs')];
		$dataArr['order_wise_po_val'][$row[csf('po_break_down_id')]]+=($row[csf('po_quantity_pcs')]*$po_avg_rate);
		$dataArr['po_avg_rate'][$row[csf('po_break_down_id')]]=$po_avg_rate;
		$dataArr['SHIPING_STATUS'][$row[csf('po_break_down_id')]]=$row[csf('SHIPING_STATUS')];
		
		
		
		
		$poDataArr[$row[csf('po_break_down_id')]]=array(
			shipment_date=>$row[csf('shipment_date')],
			po_total_price=>$row[csf('po_total_price')],
			po_total_price=>$row[csf('po_total_price')],
			po_avg_rate=>$po_avg_rate
		);
		
	}
	unset($order_sql_relsult);
	
	
	//image......................................................................	
	$job_arr=array_unique(explode(',',implode(',',$dataArr['job_no'])));
	$sql_con='';
	if($db_type==2 &&  count($job_arr)>999)
	{
		$job_chunk=array_chunk($job_arr, 999);
		foreach($job_chunk as $row)
		{
			$job_ids=implode("','", $row);
			if($sql_con=="")
			{
				$sql_con=" and (master_tble_id in ('".$job_ids."')";
			}
			else
			{
				$sql_con.=" or master_tble_id in ('".$job_ids."')";
			}
		}
		$sql_con.=")";
	}
	else
	{
		$sql_con=" and master_tble_id in ('".implode("','",$job_arr)."')";
	}
		
	$imge_arr=return_library_array("select master_tble_id,image_location from common_photo_library where 1=1 $sql_con ",'master_tble_id','image_location');
	
//Prec cost....................................
	if($db_type==2 && count($job_arr)>1000)
	{
		$sql_con=" and (";
		$chunk_arr=array_chunk($job_no_arr,999);
		foreach($chunk_arr as $ids)
		{
			$sql_con.=" b.job_no in('".implode("','",$ids)."') or"; 
		}
		$sql_con=chop($sql_con,'or');
		$sql_con.=")";
	}
	else
	{
		$sql_con=" and b.job_no in('".implode("','",$job_no_arr)."')";
	}
	
	$sql="select a.costing_per,b.id,b.job_no,b.total_cost,b.cm_cost from wo_pre_cost_mst a,wo_pre_cost_dtls b where status_active=1 and is_deleted=0 $sql_con";
	foreach(sql_select($sql) as $rows)
	{
		$cm_cost_arr[$rows[csf("job_no")]]=$rows[csf("cm_cost")];
		$total_cost_arr[$rows[csf("job_no")]]=$rows[csf("total_cost")];
		$costing_per_arr[$rows[csf("job_no")]]=$rows[csf("costing_per")];
	}
	
	

	
//production........................................................		
	$sql_con='';	
	if($db_type==2 &&  count($dataArr['po_id'])>999)
	{
		$po_chunk=array_chunk($dataArr['po_id'], 999);
		foreach($po_chunk as $row)
		{
			$po_ids=implode(",", $row);
			if($sql_con=="")
			{
				$sql_con=" and (po_break_down_id in ($po_ids)";
			}
			else
			{
				$sql_con.=" or po_break_down_id in ($po_ids)";
			}
		}
		$sql_con.=")";
	}
	else
	{
		$sql_con=" and po_break_down_id in (".implode(',',$dataArr['po_id']).")";
	}
		
		
	$cutting_qnty=return_library_array("SELECT po_break_down_id,sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='1' and is_deleted=0 and status_active=1 $sql_con group by po_break_down_id",'po_break_down_id','production_quantity');

	
	$sql_summary_ex_factory=return_library_array("SELECT po_break_down_id,sum(ex_factory_qnty) AS ex_factory_qnty from pro_ex_factory_mst  where status_active=1 and is_deleted=0 $sql_con group by po_break_down_id",'po_break_down_id','ex_factory_qnty');
	
	
	$sewingin_qnty=return_library_array("SELECT po_break_down_id,sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='5' and is_deleted=0 and status_active=1 $sql_con group by po_break_down_id",'po_break_down_id','production_quantity');
	
	$finish_qnty=return_library_array("SELECT po_break_down_id,sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='8' and is_deleted=0 and status_active=1 $sql_con group by po_break_down_id",'po_break_down_id','production_quantity');

	
	foreach($dataArr['month_buyer_wise_po_id'] as $key=>$buyer_arr){
		foreach($buyer_arr as $buyer_id=>$po_id_arr){
		  foreach($po_id_arr as $po_id=>$po_no){
			
			//monthly_qty...........................
			$dataArr['po_qty_pcs'][$key]+=$dataArr['order_wise_po_qty_pcs'][$po_id];
			$dataArr['cutting_qty_pcs'][$key]+=$cutting_qnty[$po_id];
			$dataArr['exfact_qty_pcs'][$key]+=$sql_summary_ex_factory[$po_id];
			$dataArr['sewing_qty_pcs'][$key]+=$sewingin_qnty[$po_id];
			$dataArr['finish_qty_pcs'][$key]+=$finish_qnty[$po_id];
			
			if(($sql_summary_ex_factory[$po_id]>$dataArr['order_wise_po_qty_pcs'][$po_id])){
				$dataArr['excess_qty'][$key]+=($sql_summary_ex_factory[$po_id]-$dataArr['order_wise_po_qty_pcs'][$po_id]);
			}
			
			//monthly_val...........................
			$dataArr['po_val'][$key]+=$dataArr['order_wise_po_val'][$po_id];
			$dataArr['cutting_val'][$key]+=$cutting_qnty[$po_id]*$dataArr['po_avg_rate'][$po_id];
			$dataArr['exfact_val'][$key]+=$sql_summary_ex_factory[$po_id]*$dataArr['po_avg_rate'][$po_id];
			$dataArr['sewing_val'][$key]+=$sewingin_qnty[$po_id]*$dataArr['po_avg_rate'][$po_id];
			//$poDataArr[$po_id]['sewing_qty']-$poDataArr[$po_id]['finish_qty'];
			$dataArr['finish_val'][$key]+=$sewingin_qnty[$po_id]-$finish_qnty[$po_id];//$finish_qnty[$po_id]*$dataArr['po_avg_rate'][$po_id];
			$dataArr['ship_to_po_bal_fob_val'][$key]+=($dataArr['order_wise_po_qty_pcs'][$po_id]-$sql_summary_ex_factory[$po_id])*$dataArr['po_avg_rate'][$po_id];
			$dataArr['sewing_to_ship_bal_fob_val'][$key]+=($sewingin_qnty[$po_id]-$sql_summary_ex_factory[$po_id])*$dataArr['po_avg_rate'][$po_id];
		  
			if((($sql_summary_ex_factory[$po_id]*$dataArr['po_avg_rate'][$po_id])>$dataArr['order_wise_po_val'][$po_id])){
				$dataArr['excess_value'][$key]+=(($sql_summary_ex_factory[$po_id]*$dataArr['po_avg_rate'][$po_id])-$dataArr['order_wise_po_val'][$po_id]);
			}
		  
			//monthly_buyer...........................
		  	$dataArr['month_buyer_po_qty_pcs'][$key][$buyer_id]+=$dataArr['order_wise_po_qty_pcs'][$po_id];
			$dataArr['month_buyer_exfact_qty_pcs'][$key][$buyer_id]+=$sql_summary_ex_factory[$po_id];
			
			$dataArr['month_buyer_po_val'][$key][$buyer_id]+=$dataArr['order_wise_po_val'][$po_id];
			$dataArr['month_buyer_exfact_val'][$key][$buyer_id]+=$sql_summary_ex_factory[$po_id]*$dataArr['po_avg_rate'][$po_id];
			
			if($sql_summary_ex_factory[$po_id]>$dataArr['order_wise_po_qty_pcs'][$po_id]){
				$dataArr['month_buyer_excess_qty'][$key][$buyer_id]+=$sql_summary_ex_factory[$po_id]-$dataArr['order_wise_po_qty_pcs'][$po_id];
				$dataArr['month_buyer_excess_val'][$key][$buyer_id]+=($sql_summary_ex_factory[$po_id]*$dataArr['po_avg_rate'][$po_id])-$dataArr['order_wise_po_val'][$po_id];
			}
			
			
			
			
			
		    //po wise data....................
		  	$poDataArr[$po_id]['cutting_qty']=$cutting_qnty[$po_id];
		  	$poDataArr[$po_id]['sewing_qty']=$sewingin_qnty[$po_id];
		  	$poDataArr[$po_id]['finish_qty']=$finish_qnty[$po_id];
			$poDataArr[$po_id]['exfact_qty']=$sql_summary_ex_factory[$po_id];
		  	$poDataArr[$po_id]['po_quantity']=$dataArr['order_wise_po_qty_pcs'][$po_id];
		  
			if($sql_summary_ex_factory[$po_id]>$dataArr['order_wise_po_qty_pcs'][$po_id]){
				$poDataArr[$po_id]['excess_qty']=$sql_summary_ex_factory[$po_id]-$dataArr['order_wise_po_qty_pcs'][$po_id];
				$poDataArr[$po_id]['excess_val']=($sql_summary_ex_factory[$po_id]-$dataArr['order_wise_po_qty_pcs'][$po_id])*$dataArr['po_avg_rate'][$po_id];
			}
		  
		  
		  //CM calculation..............................................
			if($costing_per_arr[$dataArr['job_no'][$po_id]]==1) $dzn_qnty=12;
			else if($costing_per_arr[$dataArr['job_no'][$po_id]]==3) $dzn_qnty=12*2;
			else if($costing_per_arr[$dataArr['job_no'][$po_id]]==4) $dzn_qnty=12*3;
			else if($costing_per_arr[$dataArr['job_no'][$po_id]]==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$cm_per_pcs=(($dataArr['po_avg_rate'][$po_id]*$dzn_qnty)-$total_cost_arr[$dataArr['job_no'][$po_id]])+$cm_cost_arr[$dataArr['job_no'][$po_id]];
		
		   $dataArr['sewing_cm_val'][$key]+=$cm_per_pcs*$sewingin_qnty[$po_id];
		   $dataArr['po_cm_val'][$key]+=$cm_per_pcs*$dataArr['order_wise_po_qty_pcs'][$po_id];
		   
		   $poDataArr[$po_id]['sewing_cm_val']=$cm_per_pcs*$sewingin_qnty[$po_id];
		  
		  }
		}
	}
	
	unset($cutting_qnty);
	unset($sql_summary_ex_factory);
	unset($sewingin_qnty);
	unset($finish_qnty);
	$cmy=date("M, Y",time());//current_month_year
	
	
	ob_start();
	

	
?>
<style>
.rpt_table{font-size:13px!important;}
</style>
<!--=============Total Summary Start==================================================================-->
    <table width="1600"  cellspacing="0">
        <tr class="form_caption">
            <td colspan="18" align="center" ><strong style="font-size:19px"><?php echo $company_arr[$cbo_company_id]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="18" align="center"><strong style="font-size:14px">Total Pending Order Summary </strong></td>
        </tr>
    </table>
	<table border="1" rules="all" class="rpt_table" width="1600">
        <thead>
            <th width="30">SL</th>
            <th width="80">Month</th>
            <th width="80">PO Qty.</th>
            <th width="100">PO Value</th>
            <th width="80">Cut Qty.</th>
            <th width="80">Cut. Bal. /Access </th>
            <th width="80">Sewing Qty.</th>
            <th width="80">Sewing Balance</th>
            <th width="80">Finis. Qty.</th>
            <th width="80">Finishing  Balance </th>
            <th width="80">Ship Out </th>
            <th width="100">Export <br /> FOB Value</th>
            <th width="100">Ship Bal to PO Qty.</th>
            <th width="100">Ship. Bal. to PO FOB Value.</th>
            <th width="80">Sew. to Ship Bal.Qty.</th>
            <th width="100">Sew. to Ship Bal. FOB Value</th>
            <th width="100">Excess Ship Qty</th>
            <th>Excess Ship Value</th>
            
        </thead>
        
        <tr bgcolor="<? echo $bgcolor1; ?>" onclick="change_color('tr1st_1','<? echo $bgcolor1; ?>')" id="tr1st_1">
            <td>1</td>
            <td>Previous Month</td>
            <td align="right"><? $po_qty=array_sum($dataArr['po_qty_pcs'])-$dataArr['po_qty_pcs'][$cmy];echo number_format(array_sum($dataArr['po_qty_pcs'])-$dataArr['po_qty_pcs'][$cmy],0);?></td>
            <td align="right"><? $po_val=array_sum($dataArr['po_val'])-$dataArr['po_val'][$cmy];echo number_format(array_sum($dataArr['po_val'])-$dataArr['po_val'][$cmy],2);?></td>
            <td align="right"><? echo number_format(array_sum($dataArr['cutting_qty_pcs'])-$dataArr['cutting_qty_pcs'][$cmy],0);?></td>
            <td align="right"><? echo number_format((array_sum($dataArr['po_qty_pcs'])-$dataArr['po_qty_pcs'][$cmy])-(array_sum($dataArr['cutting_qty_pcs'])-$dataArr['cutting_qty_pcs'][$cmy]),0);?></td>
            <td align="right"><? echo number_format(array_sum($dataArr['sewing_qty_pcs'])-$dataArr['sewing_qty_pcs'][$cmy],0);?></td>
            <td align="right"><? echo number_format((array_sum($dataArr['cutting_qty_pcs'])-$dataArr['cutting_qty_pcs'][$cmy])-(array_sum($dataArr['sewing_qty_pcs'])-$dataArr['sewing_qty_pcs'][$cmy]),0);?></td>
            <td align="right"><? echo number_format(array_sum($dataArr['finish_qty_pcs'])-$dataArr['finish_qty_pcs'][$cmy],0);?></td>
            <td align="right"><? echo number_format((array_sum($dataArr['sewing_qty_pcs'])-$dataArr['sewing_qty_pcs'][$cmy])-(array_sum($dataArr['finish_qty_pcs'])-$dataArr['finish_qty_pcs'][$cmy]),0);?></td>
            <td align="right"><? $shipoutQty=array_sum($dataArr['exfact_qty_pcs'])-$dataArr['exfact_qty_pcs'][$cmy];
			echo number_format(array_sum($dataArr['exfact_qty_pcs'])-$dataArr['exfact_qty_pcs'][$cmy],0);?></td>
            <td align="right"><? $export_ship_val=array_sum($dataArr['exfact_val'])-$dataArr['exfact_val'][$cmy];echo number_format(array_sum($dataArr['exfact_val'])-$dataArr['exfact_val'][$cmy],2);?></td>
            <td align="right">
			<? 
				$shipBaltoPoQty=$po_qty-$shipoutQty;
				echo number_format($shipBaltoPoQty,0);
			?>
            </td>
            <td align="right"><? echo number_format($po_val-$export_ship_val,2);?></td>
            <td align="right"><? echo round((array_sum($dataArr['sewing_qty_pcs'])-$dataArr['sewing_qty_pcs'][$cmy])-(array_sum($dataArr['exfact_qty_pcs'])-$dataArr['exfact_qty_pcs'][$cmy]));?></td>
            <td align="right"><? echo number_format(array_sum($dataArr['sewing_to_ship_bal_fob_val'])-$dataArr['sewing_to_ship_bal_fob_val'][$cmy],2);?></td>
            <td align="right"><?=array_sum($dataArr['excess_qty'])-$dataArr['excess_qty'][$cmy];?></td>
            <td align="right"><?=number_format(array_sum($dataArr['excess_value'])-$dataArr['excess_value'][$cmy],2);?></td>
        </tr>
        <tr bgcolor="<? echo $bgcolor2; ?>" onclick="change_color('tr1st_2','<? echo $bgcolor2; ?>')" id="tr1st_2">
            <td>2</td>
            <td> <? echo $cmy; ?> </td>
            <td align="right"><? echo number_format($dataArr['po_qty_pcs'][$cmy],0);?></td>
            <td align="right"><? echo number_format($dataArr['po_val'][$cmy],2);?></td>
            <td align="right"><? echo number_format($dataArr['cutting_qty_pcs'][$cmy],0);?></td>
            <td align="right"><? echo number_format($dataArr['po_qty_pcs'][$cmy]-$dataArr['cutting_qty_pcs'][$cmy],0);?></td>
            <td align="right"><? echo number_format($dataArr['sewing_qty_pcs'][$cmy],0);?></td>
            <td align="right"><? echo number_format($dataArr['cutting_qty_pcs'][$cmy]-$dataArr['sewing_qty_pcs'][$cmy],0);?></td>
            <td align="right"><? echo number_format($dataArr['finish_qty_pcs'][$cmy],0);?></td>
            <td align="right"><? echo number_format($dataArr['sewing_qty_pcs'][$cmy]-$dataArr['finish_qty_pcs'][$cmy],0);?></td>
            <td align="right"><? echo number_format($dataArr['exfact_qty_pcs'][$cmy],0);?></td>
            <td align="right"><? echo number_format($dataArr['exfact_val'][$cmy],2);?></td>
            <td align="right"><? $tot_diff_shiptoPOQty=$dataArr['po_qty_pcs'][$cmy]-$dataArr['exfact_qty_pcs'][$cmy]; echo number_format($dataArr['po_qty_pcs'][$cmy]-$dataArr['exfact_qty_pcs'][$cmy],0);?></td>
            <td align="right"><? echo number_format($dataArr['ship_to_po_bal_fob_val'][$cmy],2);?></td>
            <td align="right"><? echo round($dataArr['sewing_qty_pcs'][$cmy]-$dataArr['exfact_qty_pcs'][$cmy]);?></td>
            <td align="right"><? echo number_format($dataArr['sewing_to_ship_bal_fob_val'][$cmy],2);?></td>
            <td align="right"><?=$dataArr['excess_qty'][$cmy];?></td>
            <td align="right"><?= number_format($dataArr['excess_value'][$cmy],2);?></td>
        </tr>
        <tfoot>
            <th colspan="2" align="right">Total</th>
            <th align="right"><? echo number_format(array_sum($dataArr['po_qty_pcs']),0);?></th>
            <th align="right"><? echo number_format(array_sum($dataArr['po_val']),2);?></th>
            <th align="right"><? echo number_format(array_sum($dataArr['cutting_qty_pcs']),0);?></th>
            <th align="right"><? echo number_format(array_sum($dataArr['po_qty_pcs'])-array_sum($dataArr['cutting_qty_pcs']),0);?></th>
            <th align="right"><? echo number_format(array_sum($dataArr['sewing_qty_pcs']),0);?></th>
            <th align="right"><? echo number_format(array_sum($dataArr['cutting_qty_pcs'])-array_sum($dataArr['sewing_qty_pcs']),2);?></th>
            <th align="right"><? echo number_format(array_sum($dataArr['finish_qty_pcs']),0);?></th>
            <th align="right"><? echo number_format(array_sum($dataArr['sewing_qty_pcs'])-array_sum($dataArr['finish_qty_pcs']),0);?></th>
            <th align="right"><? echo number_format(array_sum($dataArr['exfact_qty_pcs']),0);?></th>
            <th align="right"><? echo number_format(array_sum($dataArr['exfact_val']),2);?></th>
            <th align="right"><? echo number_format($shipBaltoPoQty+$tot_diff_shiptoPOQty,0); ?></th>
            <th><? echo number_format((($po_val-$export_ship_val)+$dataArr['ship_to_po_bal_fob_val'][$cmy]),2);?></th>
            <th><? echo round(array_sum($dataArr['sewing_qty_pcs'])-array_sum($dataArr['exfact_qty_pcs']))?></th>
            <th><? echo number_format(array_sum($dataArr['sewing_to_ship_bal_fob_val']),2);?></th>
            
            <th align="right"><?=array_sum($dataArr['excess_qty']);?></th>
            <th align="right"><?=number_format(array_sum($dataArr['excess_value']),2);?></th>
            
        </tfoot>
    </table> 
     
        
        
    <table>
        <tr>
       <? $flag=0;
	   	foreach($dataArr['month_buyer_po_qty_pcs'] as $month_year=>$buyer_arr){
			$flag++;
		?> 
        <td valign="top">
           <table border="1" rules="all" class="rpt_table" width="800">
                <thead>
                    <tr>
                        <th colspan="10">Total Summary <? echo $month_year;?></th>
                     </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="70">Buyer</th>
                        <th width="70">Po Qty</th>
                        <th width="70">PO Value</th>
                        <th width="70">Ship Out</th>
                        <th width="100">Export<br /> FOB Value</th>
                        <th width="70">Ship Bal PO Qty.</th>
                        <th width="100">Ship. Bal. PO Value.</th>
                        <th width="100">Excess Ship Qty</th>
                        <th>Excess Ship Value</th>
                     </tr>
                 </thead>
                <? $i=1;
                foreach($buyer_arr as $buyer_id=>$po_qty){
                ?> 
                <tr>
                    <td align="center"><? echo $i;?></td>
                    <td><? echo $buyer_short_name_arr[$buyer_id];?></td>
                    <td align="right"><? echo round($po_qty);?></td>
                    <td align="right"><? echo number_format($dataArr['month_buyer_po_val'][$month_year][$buyer_id],2);?></td>
                    <td align="right"><? echo round($dataArr['month_buyer_exfact_qty_pcs'][$month_year][$buyer_id]); ?></td>
                    
                    <td align="right"><? echo number_format($dataArr['month_buyer_exfact_val'][$month_year][$buyer_id],2)?></td>
                    
                    <td align="right"><? echo round($po_qty-$dataArr['month_buyer_exfact_qty_pcs'][$month_year][$buyer_id]); ?></td>
                    <td align="right"><? echo number_format($dataArr['month_buyer_po_val'][$month_year][$buyer_id]-$dataArr['month_buyer_exfact_val'][$month_year][$buyer_id],2); ?></td>
                    <td align="right"><? echo number_format($dataArr['month_buyer_excess_qty'][$month_year][$buyer_id],2); ?></td>
                    <td align="right"><? echo number_format($dataArr['month_buyer_excess_val'][$month_year][$buyer_id],2); ?></td>
                 </tr>
                 <? $i++;} ?>
                <tfoot>
                    <th></th>
                    <th></th>
                    <th align="right"><? echo round(array_sum($dataArr['month_buyer_po_qty_pcs'][$month_year]));?></th>
                    <th align="right"><? echo number_format(array_sum($dataArr['month_buyer_po_val'][$month_year]),2);?></th>
                    <th align="right"><? echo round(array_sum($dataArr['month_buyer_exfact_qty_pcs'][$month_year]));?></th>
                    
                    <th align="right"><? echo number_format(array_sum($dataArr['month_buyer_exfact_val'][$month_year]),2); ?></th>
                    <th align="right"><? echo round((array_sum($dataArr['month_buyer_po_qty_pcs'][$month_year])-array_sum($dataArr['month_buyer_exfact_qty_pcs'][$month_year])));?></th>
                    <th align="right"><? echo number_format((array_sum($dataArr['month_buyer_po_val'][$month_year])-array_sum($dataArr['month_buyer_exfact_val'][$month_year])),2); ?></th>
                    <th align="right"><? echo number_format(array_sum($dataArr['month_buyer_excess_qty'][$month_year]),2); ?></th>
                    <th align="right"><? echo number_format(array_sum($dataArr['month_buyer_excess_val'][$month_year]),2); ?></th>
                 
                 </tfoot>
             </table>
             </td>
             <? 
			 if($flag==2){echo "</tr><tr>";$flag=0;}
			 
			 } ?>
        </tr>
    </table>
        
        <style type="text/css">
        	table tr td{word-break: break-all;word-wrap: break-word;}
        </style>
		
        <table border="1" rules="all" class="rpt_table" width="2300" align="left" cellpadding="0" cellspacing="0">
         <thead>
            <tr>
                <th colspan="25">Details Report</th>
             </tr>
            <tr>
                <th width="35">SL</th>
                <th width="80">Buyer</th>
                <th width="80">Job No</th>
                <th width="120">Po Number</th>
                <th width="35">Img</th>
                <th width="80"><? echo $dateField;?></th>
                <th width="50">Delay</th>
                <th width="100">Po Qty. (Pcs)</th>
                <th width="50">Unit Price</th>
                <th width="100">PO Value</th>
                <th width="100">Cutting Qty</th>
                <th width="100">Cut.<br /> Bal./Access</th>
                <th width="100">Sewing Qty</th>
                <th width="100">Sewing <br />Balance</th>
                <th width="100">Finish Qty</th>
                <th width="100">Finish Bal.</th>
                <th width="100">Ship Qty</th>
                <th width="100">Export <br />FOB Value</th>
                <th width="100">Ship Bal PO Qty.</th>
                <th width="100">Ship. Bal. FOB Value.</th>
                
                <th width="100">Excess Ship Qty</th>
                <th width="100">Excess Ship Value</th>
                
                <th width="100">Sew. to Ship Bal.Qty.</th>
                <th width="100">Sew. to Ship Bal. FOB Value</th>
                <th width="">Remarks</th>
             </tr>
         </thead>
        </table>
		  
          <div style="width:2320px; max-height:350px; overflow-y:scroll" id="scroll_body">
           <table align="left" class="rpt_table" width="2300" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
           <? 
            $i=1;
			foreach($dataArr['month_buyer_wise_po_id'] as $month_year=>$buyer_arr)
			{
            ?> 
                <tr>
                    <td bgcolor="#CCCCCC" colspan="25"><? echo $month_year;?></td>
                </tr>
                
				<?
                foreach($buyer_arr as $buyer_id=>$po_id_arr)
                {
					foreach($po_id_arr as $po_id=>$po_no)
					{
		                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						?> 
		                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		                    <td width="35"><? echo $i;?></td>
		                    <td width="80"><p><? echo $buyer_short_name_arr[$buyer_id];?></p></td>
		                    <td width="80" align="center"><? echo $dataArr['job_no'][$po_id];?></td>
		                    <td width="120"><p style="width:110px; word-wrap:break-word;"><? echo $po_no;?></p></td>
		                    <td width="35" align="center">
		                    	<!-- <a target="_blank" href="../../../<?// echo $imge_arr[$dataArr['job_no'][$po_id]];?>">
		                    		<img src="../../../<? //echo $imge_arr[$dataArr['job_no'][$po_id]];?>" width="25" />
		                    	</a> -->
		                    	<a href="##" onClick="openmypage_image('requires/shipment_pending_v2_controller.php?action=show_image&job_no=<? echo $dataArr['job_no'][$po_id];?>','Image View')">
										<? if(isset($imge_arr[$dataArr['job_no'][$po_id]])){?>
											<img src="../../../<? echo $imge_arr[$dataArr['job_no'][$po_id]];?>" height="28" width="28" />
										<? }else{ ?>
											<img src="../../../img/noimage.png" height="28" width="28"/>
										<? } ?>							
								</a>
		                    </td>
		                    <td width="80" align="center"><? echo change_date_format($poDataArr[$po_id]['shipment_date']);?></td>
		                    <td width="50" align="center"><? echo datediff('d',date('d-M-Y',time()+86400),$poDataArr[$po_id]['shipment_date'] );?></td>
		                    <td width="100" align="right"><? echo number_format($poDataArr[$po_id]['po_quantity']);?></td>
		                    <td width="50" align="right"><? echo number_format($poDataArr[$po_id]['po_avg_rate'],2);?></td>
		                    
		                    <td width="100" align="right"><? echo number_format($poDataArr[$po_id]['po_total_price'],2);?></td>
		                    <td width="100" align="right"><? echo number_format($poDataArr[$po_id]['cutting_qty']);?></td>
		                    <td width="100" align="right"><? echo number_format($poDataArr[$po_id]['po_quantity']-$poDataArr[$po_id]['cutting_qty']);?></td>
		                    <td width="100" align="right"><? echo $poDataArr[$po_id]['sewing_qty'];?></td>
		                    <td width="100" align="right"><? echo $poDataArr[$po_id]['cutting_qty']-$poDataArr[$po_id]['sewing_qty'];?></td>
		                    <td width="100" align="right"><? echo $poDataArr[$po_id]['finish_qty'];?></td>
		                    
		                    <td width="100" align="right"><? $finish_bal_qty=$poDataArr[$po_id]['sewing_qty']-$poDataArr[$po_id]['finish_qty']; echo round($finish_bal_qty);?></td>
		                    <td width="100" align="right"><? echo $poDataArr[$po_id]['exfact_qty'];?></td>
		                    <td width="100" align="right" title="PO Avg. Rate:<? echo $poDataArr[$po_id]['po_avg_rate'];?>"><? echo number_format($poDataArr[$po_id]['exfact_qty']*$poDataArr[$po_id]['po_avg_rate'],2);?></td>
		                    <td width="100" align="right"><? echo round($poDataArr[$po_id]['po_quantity']-$poDataArr[$po_id]['exfact_qty']);?></td>
		                    <td width="100" align="right"><? echo number_format($poDataArr[$po_id]['po_avg_rate']*($poDataArr[$po_id]['po_quantity']-$poDataArr[$po_id]['exfact_qty']),2);?></td>
		                    
                            <td width="100" align="right"><?=$poDataArr[$po_id]['excess_qty'];?></td>
                            <td width="100" align="right"><?=number_format($poDataArr[$po_id]['excess_val'],2);?></td>
                            
                            <td width="100" align="right"><? echo round($poDataArr[$po_id]['sewing_qty']-$poDataArr[$po_id]['exfact_qty']);?></td>
		                    <td width="100" align="right"><? echo number_format($poDataArr[$po_id]['po_avg_rate']*($poDataArr[$po_id]['sewing_qty']-$poDataArr[$po_id]['exfact_qty']),2);?></td>
		                    <td align="right"><? echo $dataArr['remarks'][$po_id];?></td>
		                 </tr>
				
		             	<?
						$month_finish_bal_qty_arr[$month_year]+=$poDataArr[$po_id]['sewing_qty']-$poDataArr[$po_id]['finish_qty'];
					 	$month_ship_bal_FOB_value_arr[$month_year]+=$poDataArr[$po_id]['po_avg_rate']*($poDataArr[$po_id]['po_quantity']-$poDataArr[$po_id]['exfact_qty']);
					 	$month_sewing_bal_FOB_value_arr[$month_year]+=$poDataArr[$po_id]['po_avg_rate']*($poDataArr[$po_id]['sewing_qty']-$poDataArr[$po_id]['exfact_qty']);
						
						$month_excess_qty_arr[$month_year]+=$poDataArr[$po_id]['excess_qty'];
						$month_excess_val_arr[$month_year]+=$poDataArr[$po_id]['excess_val'];
						
						
				 
				 
				 		$i++;
			 		}
			 	} 
			 	?>
                <tr bgcolor="#CCC">
                    <td colspan="7" align="right">Month Total:</td>
                    <td align="right"><? echo $dataArr['po_qty_pcs'][$month_year];?></td>
                    <td></td>
                    <td align="right"><? echo number_format($dataArr['po_val'][$month_year],2);?></td>
                    <td align="right"><? echo $dataArr['cutting_qty_pcs'][$month_year];?></td>
                    <td align="right"><? echo $dataArr['po_qty_pcs'][$month_year]-$dataArr['cutting_qty_pcs'][$month_year];?></td>
                    <td align="right"><? echo $dataArr['sewing_qty_pcs'][$month_year];?></td>
                    <td align="right"><? echo $dataArr['cutting_qty_pcs'][$month_year]-$dataArr['sewing_qty_pcs'][$month_year];?></td>
                    <td align="right"><? echo $dataArr['finish_qty_pcs'][$month_year];?></td>
                    <td align="right"><? echo number_format($month_finish_bal_qty_arr[$month_year],0);//number_format($dataArr['finish_val'][$month_year],2);?></td>
                    <td align="right"><? echo $dataArr['exfact_qty_pcs'][$month_year];?></td>
                    <td align="right"><? echo $dataArr['exfact_val'][$month_year];?></td>
                    <td align="right"><? echo round($dataArr['po_qty_pcs'][$month_year]-$dataArr['exfact_qty_pcs'][$month_year]);?></td>
                    <td align="right"><? echo $month_ship_bal_FOB_value_arr[$month_year];?></td>
                    
                    <td width="100" align="right"><?=$month_excess_qty_arr[$month_year];?></td>
                    <td width="100" align="right"><?=number_format($month_excess_val_arr[$month_year],2);?></td>
                    
                    <td align="right"><? echo round($dataArr['sewing_qty_pcs'][$month_year]-$dataArr['exfact_qty_pcs'][$month_year]);?></td>
                    <td align="right"><? echo $month_sewing_bal_FOB_value_arr[$month_year];?></td>
                    <td align="right"></td>
                 </tr>
			 
				<? 
			} 
			?>
             </table>
			</div>

			<table border="1" rules="all" class="rpt_table" width="2300" cellpadding="0" cellspacing="0">
                <tfoot>
                    <th width="35"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="120"></th>
                    <th width="35"></th>
                    <th width="80">Total:</th>
                    <th width="50"></th>
                    <th width="100" align="right"><? echo round(array_sum($dataArr['po_qty_pcs']));?></td>
                    <th width="50"></td>
                    <th width="100" align="right"><? echo number_format(array_sum($dataArr['po_val']),2);?></td>
                    <th width="100" align="right"><? echo round(array_sum($dataArr['cutting_qty_pcs']));?></td>
                    <th width="100" align="right"><? echo round(array_sum($dataArr['po_qty_pcs'])-array_sum($dataArr['cutting_qty_pcs']));?></td>
                    <th width="100" align="right"><? echo round(array_sum($dataArr['sewing_qty_pcs']));?></td>
                    <th width="100" align="right"><? echo round(array_sum($dataArr['cutting_qty_pcs'])-array_sum($dataArr['sewing_qty_pcs']));?></td>
                    <th width="100" align="right"><? echo round(array_sum($dataArr['finish_qty_pcs']));?></td>
                     <th width="100" align="right"><? echo number_format(array_sum($month_finish_bal_qty_arr),0);//number_format(array_sum($dataArr['finish_val']),2);?></td>
                    <th width="100" align="right"><? echo array_sum($dataArr['exfact_qty_pcs']);?></td>
                    <th width="100" align="right"><? echo number_format(array_sum($dataArr['exfact_val']),2);?></th>
                    <th width="100" align="right"><? echo round(array_sum($dataArr['po_qty_pcs'])-array_sum($dataArr['exfact_qty_pcs']));?></td>
                    <th width="100" align="right"><? echo number_format(array_sum($month_ship_bal_FOB_value_arr),2);?></td>
                    
                    <td width="100" align="right"><?=array_sum($month_excess_qty_arr);?></td>
                    <td width="100" align="right"><?=number_format(array_sum($month_excess_val_arr),2);?></td>
                    
                    
                    <th width="100" align="right"><? echo round(array_sum($dataArr['sewing_qty_pcs'])-array_sum($dataArr['exfact_qty_pcs']));?></td>
                    <th width="100" align="right"><? echo number_format(array_sum($month_sewing_bal_FOB_value_arr),2);?></td>
                    <th align="right"></td>
                 </tfoot>
		</table>
		
<?
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

if($action=="show_image")
{
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	
	?>
    <table>
	    <tr>
	    <?
	    foreach ($data_array as $row)
		{ 
			?>
		    <td style="text-align: center;" valign="middle">
		    	<div style="margin: 0 auto; width: 400px">
		    		<img src='../../../../<? echo $row[csf('image_location')]; ?>' width='400' />
		    	</div>
		    </td>
		    <?
		}
		?>
	    </tr>
    </table>
    
    <?
	exit();
}
?>