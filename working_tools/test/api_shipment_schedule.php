<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
//$con=connect();



	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$buyer_buffer_arr=return_library_array("select id,delivery_buffer_days from  lib_buyer","id","delivery_buffer_days");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supp_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
	$lib_country=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	
	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );


 

	$start_date='01-Jul-2019';
	$end_date='31-Jul-2019';
	$cbo_category_by=1;
	$company_id=3;


	$company_id=str_replace("'","",$company_id);
	$cbo_category_by=str_replace("'","",$cbo_category_by);
	
	if($db_type==0 && $start_date!="" && $end_date!="")
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-');
		$end_date=change_date_format($end_date,'yyyy-mm-dd','-');
	}
	if($db_type==2 && $start_date!="" && $end_date!="")
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-',1);
		$end_date=change_date_format($end_date,'yyyy-mm-dd','-',1);
	}

	
	
	if($cbo_category_by==1 && $start_date!="" && $end_date!="")
	{
		$date_cond="and b.pub_shipment_date between '$start_date' and  '$end_date'";
	}
	else if($cbo_category_by==2 && $start_date!="" && $end_date!="")
	{
		$date_cond=" and b.po_received_date between '$start_date' and  '$end_date'";
	}
	elseif($cbo_category_by==3  && $start_date!="" && $end_date!="") 
	{
		$date_cond=" and b.shipment_date between '$start_date' and  '$end_date'";
	}
	else
	{
		$date_cond="";
	}
	
	if($company_id>0){
		$company_con=" and a.company_name=$company_id";
	}
	
	

$sql="select A.COMPANY_NAME,A.BUYER_NAME,b.id as PO_ID,B.SHIPING_STATUS,SUM(B.PO_QUANTITY*A.TOTAL_SET_QNTY) AS PO_QUANTITY, SUM(B.PO_TOTAL_PRICE) AS PO_TOTAL_PRICE   from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $date_cond $company_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.company_name,a.buyer_name,b.id,b.shiping_status order by a.company_name";
$data_array=sql_select($sql);//b.unit_price

foreach ($data_array as $row)
{ 
	$status_wise_po_id_arr[$row[SHIPING_STATUS]][$row[PO_ID]]=$row[PO_ID];
	$po_id_arr[$row[PO_ID]]=$row[PO_ID];
}

$ex_factory_qty_arr=return_library_array( "select po_break_down_id,sum(ex_factory_qnty) as ex_factory_qnty from  pro_ex_factory_mst where  status_active=1 and is_deleted=0 and po_break_down_id in(".implode(',',$po_id_arr).") group by po_break_down_id",'po_break_down_id','ex_factory_qnty');

foreach($status_wise_po_id_arr[3] as $po_id){
	$full_shiped_qty_arr[$po_id]=$ex_factory_qty_arr[$po_id];	
}

foreach($status_wise_po_id_arr[2] as $po_id){
	$partial_shiped_qty_arr[$po_id]=$ex_factory_qty_arr[$po_id];	
}



foreach ($data_array as $row){
	$key=$row[COMPANY_NAME].'*'.$row[BUYER_NAME];
	$dataArr[$key]['COMPANY_NAME']=$company_library[$row[COMPANY_NAME]];
	$dataArr[$key]['BUYER_NAME']=$buyer_arr[$row[BUYER_NAME]];
	$dataArr[$key]['QUANTITY']+=$row[PO_QUANTITY];
	$dataArr[$key]['QUANTITY_VALUE']+=$row[PO_TOTAL_PRICE];
	$totalBuyerValue[$key]+=$row[PO_TOTAL_PRICE];
	$dataArr[$key]['FULL_SHIPPED']+=$full_shiped_qty_arr[$row[PO_ID]];
	$dataArr[$key]['PARTIAL_SHIPPED']+=$partial_shiped_qty_arr[$row[PO_ID]];
	$dataArr[$key]['RUNNING']+=$row[PO_QUANTITY]-($full_shiped_qty_arr[$row[PO_ID]]+$partial_shiped_qty_arr[$row[PO_ID]]);

}


foreach ($dataArr as $key=>$row){
	$returnDataArr[]=array(
		'COMPANY_NAME'=>$dataArr[$key]['COMPANY_NAME'],
		'BUYER_NAME'=>$dataArr[$key]['BUYER_NAME'],
		'QUANTITY'=>$dataArr[$key]['QUANTITY'],
		'QUANTITY_VALUE'=>number_format($dataArr[$key]['QUANTITY_VALUE'],2),
		'QUANTITY_VALUE_PERCENTAGE'=>number_format(($dataArr[$key]['QUANTITY_VALUE']/array_sum($totalBuyerValue))*100,2),
		'FULL_SHIPPED'=>$dataArr[$key]['FULL_SHIPPED'],
		'PARTIAL_SHIPPED'=>$dataArr[$key]['PARTIAL_SHIPPED'],
		'RUNNING'=>$dataArr[$key]['RUNNING'],
		'EX_FACTORY_PERCENTAGE'=>number_format((($dataArr[$key]['FULL_SHIPPED']+$dataArr[$key]['PARTIAL_SHIPPED'])/$dataArr[$key]['QUANTITY'])*100,4),
	);
}






var_dump($returnDataArr);die;


									
									
                                    //$data_array_po=sql_select("select a.company_name, b.id, sum(b.po_quantity*a.total_set_qnty) as po_quantity , b.shiping_status  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.company_name =$row[company_name] and a.buyer_name =$row[buyer_name] and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond and a.status_active=1 and b.status_active=1 ");
									$data_array_po=sql_select("select a.company_name, b.id, (b.po_quantity*a.total_set_qnty) as po_quantity , b.shiping_status  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.company_name =".$row[csf('company_name')]." and a.buyer_name =".$row[csf('buyer_name')]." and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $date_cond $order_status_con $pocond  $job_con $po_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
                                    $full_shiped=0;$partial_shiped=0;
                                    foreach ($data_array_po as $row_po)
                                    {
                                   // $ex_factory_qnty=return_field_value( 'sum(ex_factory_qnty)','pro_ex_factory_mst', 'po_break_down_id="'.$row_po[id].'" and status_active=1 and is_deleted=0' );
								  $ex_factory_qnty=$ex_factory_qty_arr[$row_po[csf("id")]];
                                    if($row_po[csf('shiping_status')]==3)
                                    {
                                    $full_shiped+=$ex_factory_qnty;
                                    }
                                    if($row_po[csf('shiping_status')]==2)
                                    {
                                    $partial_shiped+=$ex_factory_qnty;
                                    }
                                    }


die;












	$company_name=3;
	
	$delivery_company_name=str_replace("'","",$cbo_delivery_company_name);
	$location_name=str_replace("'","",$cbo_location_name);
	$shipping_status=str_replace("'","",$cbo_shipping_status);
	$date_from=str_replace("'","",$prev_date);
	$date_to=str_replace("'","",$current_date);
	$reportType=str_replace("'","",$reportType);

	// =========================== MAKING QUERY  COND ============================
		
	$shiping_status_cond = ($shipping_status != "")? " and b.shiping_status in($shipping_status)" : "";

	if($date_from!="" && $date_to!="")
	{
		$date_cond="and c.ex_factory_date between '$date_from' and  '$date_to' ";
	}
	else
	{
		$date_cond="";
	}

	if($delivery_company_name)
	{
		 $del_comp_cond="and e.delivery_company_id in($delivery_company_name) ";
	}
	else
	{
		 $del_comp_cond="";
	}
	if($location_name)
	{
		 $del_location_cond.="and e.location_id in($cbo_location_name) ";
	}
	else
	{
		 $del_location_cond="";
	} 
	if($company_name)
	{
		 $company_cond=" and a.company_name in($company_name)";
	}
	else
	{
		 $company_cond="";
	} 
	if(str_replace("'","", $cbo_buyer_name)) 
	{		
		$buyer_cond =" and a.buyer_name = ".str_replace("'", "",  $cbo_buyer_name) ;
	}	
	

$sql = "SELECT a.company_name,a.job_no_prefix_num, a.buyer_name, a.style_ref_no,a.ship_mode as po_ship_mode,b.id as po_id, b.po_number,b.shiping_status,b.unit_price, c.shiping_mode, e.delivery_company_id as del_com,e.delivery_location_id as  del_loc,f.cutup_date,f.country_ship_date, max(c.ex_factory_date) as ex_factory_date, sum(d.production_qnty) as ex_fact_qty, sum(c.total_carton_qnty) as carton_qnty,e.attention, max(b.po_quantity*a.total_set_qnty) as po_qty
	from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c, pro_ex_factory_dtls d, pro_ex_factory_delivery_mst e, wo_po_color_size_breakdown f
	where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=f.job_no_mst and b.id=f.po_break_down_id and c.id=d.mst_id and e.id=c.delivery_mst_id and f.id=d.color_size_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and f.status_active=1 $company_cond $buyer_cond $del_comp_cond $del_location_cond $shiping_status_cond $date_cond 
	group by a.company_name,a.job_no_prefix_num,a.buyer_name,a.style_ref_no, a.ship_mode,b.id ,b.po_number,b.shiping_status,c.shiping_mode, e.delivery_company_id,e.delivery_location_id,f.cutup_date,f.country_ship_date,b.unit_price,e.attention";
	$sql_res = sql_select($sql);


	$buyer_summary_array = array();
	$po_id_arr = array();$buyer_po_qty_arr = array();
	foreach ($sql_res as $row) 
	{
		$po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		$buyer_po_qty_arr[$row[csf('buyer_name')]][$row[csf('po_id')]]=$row[csf('po_qty')];
		// ================== for buyer summary =====================
		$buyer_summary_array[$row[csf('buyer_name')]]['cur_ex_fact_qty'] 	+= $row[csf('ex_fact_qty')];
		$buyer_summary_array[$row[csf('buyer_name')]]['unit_price'] 		+= $row[csf('unit_price')];
		$buyer_bufer_days 	= $buyer_buffer_arr[$row[csf('buyer_name')]];
		$cutup_date 		= $row[csf('cutup_date')];
		$ex_factory_date 	= $row[csf('ex_factory_date')];
		$country_ship_date 	= $row[csf('country_ship_date')];
		// ========== add buyer_bufer_days ================
		if($buyer_bufer_days)
		{
			$cutup_date = strtotime($cutup_date);
			$exten_date = date('d-M-y',strtotime("+ $buyer_bufer_days",$cutup_date));
		}
		else
		{
			$exten_date = $cutup_date;
		}
		// ================ for shipment status wise qnty ==========================
		if(strtotime($country_ship_date) > strtotime($ex_factory_date))
		{
			$buyer_summary_array[$row[csf('buyer_name')]]['early_qty'] 		+= $row[csf('ex_fact_qty')];
		}
		else if(strtotime($exten_date) > strtotime($ex_factory_date))
		{
			$buyer_summary_array[$row[csf('buyer_name')]]['ontime_qty'] 	+= $row[csf('ex_fact_qty')];
		}
		else if(strtotime($exten_date) < strtotime($ex_factory_date))
		{
			$buyer_summary_array[$row[csf('buyer_name')]]['late_qty'] 		+= $row[csf('ex_fact_qty')];
		}
		
	}




?>


            <table width="930" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Buyer Name</th>
                    <th width="80">Curr. Ex-Fact. Qty.</th>
                    <th width="80">Avg. Price</th>
                    <th width="80">Curr. Ex-Fact. Val</th>
                    <th width="80">Early Qnty</th>
                    <th width="80">On Time Qty</th>
                    <th width="80">Late Qty</th>
                    <th width="80">Extra Qty.</th>
                    <th width="80">Extra Value </th>
                    <th width="80">Short Qty</th>
                    <th width="80">Short Value</th>
                </thead>
            	<?
            	$sl=1;
            	foreach ($buyer_summary_array as $buyer_key => $row) 
            	{
            		if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            		$cur_ex_fact_qty 	= $row['cur_ex_fact_qty'];
            		$unit_price 		= $row['unit_price'];
            		$early_qty 			= $row['early_qty']/$cur_ex_fact_qty*100;
            		$late_qty  			= $row['late_qty']/$cur_ex_fact_qty*100;
            		$ontime_qty  		= $row['ontime_qty']/$cur_ex_fact_qty*100;


            		$cur_ex_fact_val	= $cur_ex_fact_qty*$unit_price;
            		$avg_price 			= $cur_ex_fact_val/$cur_ex_fact_qty;
            		$order_quantity		= array_sum($buyer_po_qty_arr[$buyer_key]);
					//$buyer_po_qnty_array[$buyer_key];
            		$extra_quantity		= $order_quantity - $cur_ex_fact_qty;
            		$extra_value		= $extra_quantity * $unit_price;            		
            		$short_quantity		= $order_quantity - $cur_ex_fact_qty;
            		$short_value		= $extra_quantity * $unit_price;
            		
					if($extra_quantity<0){$short_quantity=$extra_quantity;}else{$short_quantity='';}
					if($extra_value<0){$short_value=$extra_value;}else{$short_value='';}
					
					
					?>            		
	            	<tr bgcolor="<? echo $bgcolor; ?>">
	            		<td width="30"><? echo $sl;?></td>
	            		<td width="100" align="left"><? echo $buyer_arr[$buyer_key]; ?></td>
	            		<td width="80" align="right"><? echo number_format($cur_ex_fact_qty,0); ?></td>
	            		<td width="80" align="right"><? echo number_format($avg_price,2); ?></td>
	            		<td width="80" align="right"><? echo number_format($cur_ex_fact_val,2); ?></td>
	            		<td width="80" align="right"><? echo number_format($early_qty,2); ?>%</td>
	            		<td width="80" align="right"><? echo number_format($ontime_qty,2); ?>%</td>
	            		<td width="80" align="right"><? echo number_format($late_qty,2); ?>%</td>
	            		<td width="80" align="right"><? echo number_format($extra_quantity,0); ?></td>
	            		<td width="80" align="right"><? echo number_format($extra_value,2); ?></td>
	            		<td width="80" align="right"><? echo number_format($short_quantity,2); ?></td>
	            		<td width="80" align="right"><? echo number_format($short_value,2); ?></td>
	            	</tr>
            		<?
            	}
            	?>
            </table>

			
			<br/><br/>




			<?


			 $com_cond = "";
			 $start_date='01-Jul-2019';
			 $txt_demand_date='18-Sep-2019';
			 $company_id='1';
			 if ($company_id > 0)
				 $com_cond = " and a.company_name=$company_id";
			$sql = "select a.company_name,b.id, b.shiping_status, b.po_quantity, a.total_set_qnty, b.plan_cut, (b.unit_price/a.total_set_qnty) as order_rate,b.pub_shipment_date 
			from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and b.pub_shipment_date between '" . $start_date . "' and '" . $txt_demand_date . "' and b.is_confirmed=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.shiping_status !=3 $com_cond"; //and a.job_no_prefix_num like '$txt_job_number' 
		 	echo $sql;
			 $result = sql_select($sql);
			 foreach ($result as $row) {
				if ($row[csf('shiping_status')] == 2) {
					$buyer_ex_quantity = 0;
					$partial_ex_factory[$row[csf('id')]] = $row[csf('id')];
				}
				$po_quantity = $row[csf('po_quantity')] * $row[csf('total_set_qnty')];
				if (date("Y-m-d", strtotime($row[csf('pub_shipment_date')])) == date("Y-m-d", strtotime($txt_demand_date))) {
		
					$company_order_qnty_day[$row[csf('id')]]['order_qnty'] = $po_quantity;
					$company_order_qnty_day[$row[csf('id')]]['order_rate'] = $row[csf('order_rate')];
					$company_order_qnty_day[$row[csf('id')]]['company_name'] = $row[csf('company_name')];
				}

				$company_order_qnty[$row[csf('id')]]['order_qnty'] = $po_quantity;
				$company_order_qnty[$row[csf('id')]]['order_rate'] = $row[csf('order_rate')];
				$company_order_qnty[$row[csf('id')]]['company_name'] = $row[csf('company_name')];
			 }
			 //print_r($company_order_qnty_day); //die;

			 if (count($partial_ex_factory) > 0)
			 {
				 $sql_summary_ex_factory = return_library_array("SELECT po_break_down_id,sum(ex_factory_qnty) AS ex_factory_qnty from pro_ex_factory_mst  where po_break_down_id in (" . implode(",", $partial_ex_factory) . ") and status_active=1 and is_deleted=0 group by po_break_down_id", 'po_break_down_id', 'ex_factory_qnty');
			}

			 foreach ($company_order_qnty as $poid => $podtls) {
				$podtls['order_qnty'] = $podtls['order_qnty'] - $sql_summary_ex_factory[$poid];


				//$company_order_summ[$podtls['company_name']]['order_qnty'] += $podtls['order_qnty'];
        		//$company_order_summ[$podtls['company_name']]['order_val'] += $podtls['order_qnty'] * $podtls['order_rate'];
				if ($company_order_qnty_day[$poid]['order_qnty'] != '') 
				{
					$company_order_day_summ[$podtls['company_name']]['order_qnty'] += $podtls['order_qnty'];
					$company_order_day_summ[$podtls['company_name']]['order_val'] += $podtls['order_qnty'] * $podtls['order_rate'];
				}
			 }

			?>
			<table border="1" rules="all" class="rpt_table" width="500"style="align:right" >
				<thead>
					<tr>
						<th colspan="2"><? echo date("M-d", strtotime($txt_demand_date)); ?></th>
						<th colspan="3">Daily Pending</th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="150"> Company Name </th>
						<th width="130">Pending PO Value </th>
						<th width="140">Pending PO Qnty.</th>
						<th>FOB</th>
					</tr>
				</thead>
				<tbody>
			<?
			$d = 1;
			$i = 0;
			$tot_po_val = 0;
			$tot_po_qnty = 0;
			foreach ($company_order_day_summ as $company => $cdata) {
				$i++;
				?> 
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr1st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="tr1st_<? echo $i; ?><? echo $d; ?>">
							<td><? echo $d++; ?></td>
							<td><p><? echo $company_details[$company]; ?></p></td>
							<td align="right"><? echo number_format($cdata['order_val'], 2);
				$tot_po_val += $cdata['order_val']; ?></td>
							<td align="right"><? echo number_format($cdata['order_qnty'], 0);
				$tot_po_qnty += $cdata['order_qnty']; ?></td>
							<td align="right"><? echo number_format(($cdata['order_val'] / $cdata['order_qnty']), 2); ?></td>
						</tr>
					<? } ?>
				</tbody>
				<tfoot>
				<th colspan="2" align="right">Total</th><th><? echo number_format($tot_po_val, 2); ?></th><th><? echo number_format($tot_po_qnty, 2); ?></th><th><? echo number_format(($tot_po_val / $tot_po_qnty), 2); ?></th>
			</tfoot>
    	</table>
















