<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
//$con=connect();


	if($db_type==0)
	{
		$current_date = date("Y-m-d",strtotime(add_time(date("Y-m-d",time()),0)));
		$prev_date = date('Y-m-d', strtotime('-14 day', strtotime($current_date)));
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d",strtotime(add_time(date("Y-m-d",time()),0))),'','',1);
		$prev_date = change_date_format(date('Y-m-d', strtotime('-14 day', strtotime($current_date))),'','',1); 
	}

	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$buyer_buffer_arr=return_library_array("select id,delivery_buffer_days from  lib_buyer","id","delivery_buffer_days");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supp_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
	$lib_country=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	
	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );


 

$prev_date='01-Aug-2019';
$current_date='31-Aug-2019';






	$cbo_company_id=3;
	$cbo_year=2019;
	$cbo_date_category=2;
	
	
	if($cbo_date_category==1){
		$select_fill="b.shipment_date as shipment_date";
		$date_con=" and b.shipment_date < '".date("d-M-Y",time())."'";
		$dateField="Ship Date";
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
		
	
	
	
	$order_sql="SELECT a.job_no, a.style_ref_no, a.company_name, a.buyer_name, a.ship_mode,a.total_set_qnty,a.set_smv,a.insert_date,
	a.update_date, a.order_uom,b.po_number,b.po_quantity,b.po_total_price,b.details_remarks, c.po_break_down_id,c.order_quantity as po_quantity_pcs,c.plan_cut_qnty, $select_fill , c.order_total,c.item_number_id from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id and a.company_name=$cbo_company_id $buyer_cond and b.is_confirmed=1  AND b.shiping_status != 3 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0  AND c.status_active = 1 AND c.is_deleted = 0 $date_con $year_con $buyer_com  order by b.pub_shipment_date DESC";

 //echo $order_sql;die;

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
		
		$poDataArr[$row[csf('po_break_down_id')]]=array(
			shipment_date=>$row[csf('shipment_date')],
			po_total_price=>$row[csf('po_total_price')],
			po_total_price=>$row[csf('po_total_price')],
			po_avg_rate=>$po_avg_rate
		);
		
	}
	unset($order_sql_relsult);
	
	
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
			//monthly_val...........................
			$dataArr['po_val'][$key]+=$dataArr['order_wise_po_val'][$po_id];
			$dataArr['cutting_val'][$key]+=$cutting_qnty[$po_id]*$dataArr['po_avg_rate'][$po_id];
			$dataArr['exfact_val'][$key]+=$sql_summary_ex_factory[$po_id]*$dataArr['po_avg_rate'][$po_id];
			$dataArr['sewing_val'][$key]+=$sewingin_qnty[$po_id]*$dataArr['po_avg_rate'][$po_id];
			$dataArr['finish_val'][$key]+=$finish_qnty[$po_id]*$dataArr['po_avg_rate'][$po_id];
		  
		  
		  
			$dataArr['ship_to_po_bal_fob_val'][$key]+=($dataArr['order_wise_po_qty_pcs'][$po_id]-$sql_summary_ex_factory[$po_id])*$dataArr['po_avg_rate'][$po_id];
			$dataArr['sewing_to_ship_bal_fob_val'][$key]+=($sewingin_qnty[$po_id]-$sql_summary_ex_factory[$po_id])*$dataArr['po_avg_rate'][$po_id];
		  
		  
		  
			//monthly_buyer...........................
		  	$dataArr['month_buyer_po_qty_pcs'][$key][$buyer_id]+=$dataArr['order_wise_po_qty_pcs'][$po_id];
			$dataArr['month_buyer_exfact_qty_pcs'][$key][$buyer_id]+=$sql_summary_ex_factory[$po_id];
			
			$dataArr['month_buyer_po_val'][$key][$buyer_id]+=$dataArr['order_wise_po_val'][$po_id];
			$dataArr['month_buyer_exfact_val'][$key][$buyer_id]+=$sql_summary_ex_factory[$po_id]*$dataArr['po_avg_rate'][$po_id];
		    //po wise data....................
		  	$poDataArr[$po_id]['cutting_qty']=$cutting_qnty[$po_id];
		  	$poDataArr[$po_id]['sewing_qty']=$sewingin_qnty[$po_id];
		  	$poDataArr[$po_id]['finish_qty']=$finish_qnty[$po_id];
			$poDataArr[$po_id]['exfact_qty']=$sql_summary_ex_factory[$po_id];
		  	$poDataArr[$po_id]['po_quantity']=$dataArr['order_wise_po_qty_pcs'][$po_id];
		  
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
	
	





$shipPenDataArr['PRE_MONTH']=array(
	MONTH=>'Previous Month',
	PO_QTY=>array_sum($dataArr['po_qty_pcs'])-$dataArr['po_qty_pcs'][$cmy],
	PO_VALUE=>array_sum($dataArr['po_val'])-$dataArr['po_val'][$cmy],
	CUT_QTY=> array_sum($dataArr['cutting_qty_pcs'])-$dataArr['cutting_qty_pcs'][$cmy],
	CUT_BAL_ACCESS=>(array_sum($dataArr['po_qty_pcs'])-$dataArr['po_qty_pcs'][$cmy])-(array_sum($dataArr['cutting_qty_pcs'])-$dataArr['cutting_qty_pcs'][$cmy]),
	SEWING_QTY=> array_sum($dataArr['sewing_qty_pcs'])-$dataArr['sewing_qty_pcs'][$cmy],
	SEWING_BALANCE=>(array_sum($dataArr['cutting_qty_pcs'])-$dataArr['cutting_qty_pcs'][$cmy])-(array_sum($dataArr['sewing_qty_pcs'])-$dataArr['sewing_qty_pcs'][$cmy]),
	
	FINIS_QTY=> (array_sum($dataArr['finish_qty_pcs'])-$dataArr['finish_qty_pcs'][$cmy]),
	
	FINISHING_BALANCE=>(array_sum($dataArr['sewing_qty_pcs'])-$dataArr['sewing_qty_pcs'][$cmy])-(array_sum($dataArr['finish_qty_pcs'])-$dataArr['finish_qty_pcs'][$cmy]),
	
	
	SHIP_OUT=>array_sum($dataArr['exfact_qty_pcs'])-$dataArr['exfact_qty_pcs'][$cmy],
	EXPORT_FOB_VALUE=>array_sum($dataArr['exfact_val'])-$dataArr['exfact_val'][$cmy],
	
	SHIP_BAL_TO_PO_QTY=>(array_sum($dataArr['po_qty_pcs'])-$dataArr['po_qty_pcs'][$cmy])-array_sum($dataArr['exfact_qty_pcs'])-$dataArr['exfact_qty_pcs'][$cmy],
	
	SHIP_BAL_TO_PO_FOB_VALUE=>(array_sum($dataArr['sewing_qty_pcs'])-$dataArr['sewing_qty_pcs'][$cmy])-(array_sum($dataArr['finish_qty_pcs'])-(array_sum($dataArr['exfact_qty_pcs'])-$dataArr['exfact_qty_pcs'][$cmy]))
			,
	SEW_TO_SHIP_BALQTY=>(array_sum($dataArr['sewing_qty_pcs'])-$dataArr['sewing_qty_pcs'][$cmy])-(array_sum($dataArr['exfact_qty_pcs'])-$dataArr['exfact_qty_pcs'][$cmy]),
	SEW_TO_SHIP_BAL_FOB_VALUE=>array_sum($dataArr['sewing_to_ship_bal_fob_val'])-$dataArr['sewing_to_ship_bal_fob_val'][$cmy],
	);
	
$shipPenDataArr['CRR_MONTH']=array(
	MONTH=>$cmy,
	PO_QTY=>$dataArr['po_qty_pcs'][$cmy],
	PO_VALUE=>$dataArr['po_val'][$cmy],
	CUT_QTY=>$dataArr['cutting_qty_pcs'][$cmy],
	CUT_BAL_ACCESS=>$dataArr['po_qty_pcs'][$cmy]-$dataArr['cutting_qty_pcs'][$cmy],
	SEWING_QTY=>$dataArr['sewing_qty_pcs'][$cmy],
	SEWING_BALANCE=>$dataArr['cutting_qty_pcs'][$cmy]-$dataArr['sewing_qty_pcs'][$cmy],
	FINIS_QTY=> $dataArr['finish_qty_pcs'][$cmy],
	FINISHING_BALANCE=>$dataArr['sewing_qty_pcs'][$cmy]-$dataArr['finish_qty_pcs'][$cmy],
	SHIP_OUT=>$dataArr['exfact_qty_pcs'][$cmy],
	EXPORT_FOB_VALUE=>$dataArr['exfact_val'][$cmy],
	SHIP_BAL_TO_PO_QTY=>$dataArr['po_qty_pcs'][$cmy]-$dataArr['exfact_qty_pcs'][$cmy],
	SHIP_BAL_TO_PO_FOB_VALUE=>$dataArr['ship_to_po_bal_fob_val'][$cmy],
	SEW_TO_SHIP_BALQTY=>round($dataArr['sewing_qty_pcs'][$cmy]-$dataArr['exfact_qty_pcs'][$cmy]),
	SEW_TO_SHIP_BAL_FOB_VALUE=>$dataArr['sewing_to_ship_bal_fob_val'][$cmy],
	);

 			
  
  var_dump($shipPenDataArr);          
            
            
            
            
           
            
            
            
            
            
            
            

