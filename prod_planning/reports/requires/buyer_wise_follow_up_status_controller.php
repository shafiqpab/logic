<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$floor_library=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

// if ($action=="load_drop_down_location")
// {
// 	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active=1 and company_id in($data)","id,location_name", 1, "-- Select Location --", $selected, "" );     	 
// 	exit();
// }

// if ($action=="load_drop_down_floor")
// {
// 	echo create_drop_down( "cbo_floor_id", 150, "select id,floor_name from lib_prod_floor  where status_active=1  and company_id in($data)","id,floor_name", 1, "-- Select Floor --", $selected, "" );   	 
// 	exit();
// }

if($action=="report_generate")
{ 
	// var_dump($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id =str_replace("'","",$cbo_company_id);
	$buyer_id 	=str_replace("'","",$cbo_buyer_id); 
	$location_id=str_replace("'","",$cbo_location_id);  
	$floor_id 	=str_replace("'","",$cbo_floor_id); 
	$year 		=str_replace("'","",$cbo_year); 	 
	$date_from 	=str_replace("'","",$txt_date_from);
	$date_to	=str_replace("'","",$txt_date_to);

	$loc_cond="";	
	if($location_id){$loc_cond.=" and c.location_id in($location_id)";}
	$floor_cond="";	
	if($floor_id){$floor_cond.="  floor_name in($floor_id)";}
	$year_cond="";	
	if($year != 0){$year_cond=" and to_char(a.insert_date,'YYYY')=$year";}else{$year_cond="";}
	$date_cond="";	
	if($date_from !="" && $date_to !=""){$date_cond.="  and b.pub_shipment_date between '$date_from' and '$date_to'"; }
	
	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	}	 
		
	// ============================================= MAIN QUERY ==========================================
	$order_data="SELECT a.buyer_name,to_char(to_date(b.pub_shipment_date, 'DD-MM-YYYY'), 'Month') as month,b.id,
  	sum(case when a.order_uom=58 then b.po_quantity*a.total_set_qnty else b.po_quantity end) as order_qnty  	
	from wo_po_details_master a, wo_po_break_down b  
	where  a.job_no=b.job_no_mst and a.company_name in($company_id) and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond  $date_cond $year_cond 
	GROUP BY a.buyer_name,b.pub_shipment_date,b.id
	order by b.pub_shipment_date,a.buyer_name asc";
	$order_data_result=sql_select($order_data);
	$main_array = array();
	$po_array = array();
	foreach ($order_data_result as $row) 
	{
		$po_array[$row[csf('id')]] = $row[csf('id')];
		$main_array[$row[csf('month')]][$row[csf('buyer_name')]] += $row[csf('order_qnty')];
	}	
	
	$rowSpan = array();
	foreach ($main_array as $month => $month_data) 
	{
		foreach ($month_data as $buyer => $row) 
		{
			$rowSpan[$month]++;
		}
	} 
	$poIds = implode(",", $po_array);
	// ============================================= FOR ORDER QNTY ==========================================
	$sql_data="SELECT a.buyer_name,to_char(to_date(b.pub_shipment_date, 'DD-MM-YYYY'), 'Month') as month,b.id,
  	sum(b.po_quantity) as order_qnty,
  	NVL(sum(CASE WHEN c.production_type ='1'  and d.production_type ='1' THEN d.production_qnty ELSE 0 END),0) AS cutting_qnty,
  	NVL(sum(CASE WHEN c.production_type ='4'  and d.production_type ='4' THEN d.production_qnty ELSE 0 END),0) AS sewing_in,
  	NVL(sum(CASE WHEN c.production_type ='5'  and d.production_type ='5' THEN d.production_qnty ELSE 0 END),0) AS sewing_out,
  	NVL(sum(CASE WHEN c.production_type ='8'  and d.production_type ='8' THEN d.production_qnty ELSE 0 END),0) AS finish_qty
	from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c,pro_garments_production_dtls d  
	where  a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.id=d.mst_id and a.company_name in($company_id) and b.id in($poIds) and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.production_type in(1,4,5,8) $loc_cond $floor_cond  $buyer_id_cond  $date_cond $year_cond 
	GROUP BY a.buyer_name,b.pub_shipment_date,b.id
	order by b.pub_shipment_date asc";//sum(b.po_quantity) as order_qnty,
	
	$data_result=sql_select($sql_data);
	$prod_array = array();
	foreach ($data_result as $row) 
	{
		$prod_array[$row[csf('month')]][$row[csf('buyer_name')]]['cutting_qnty'] 	+= $row[csf('cutting_qnty')];
		$prod_array[$row[csf('month')]][$row[csf('buyer_name')]]['sewing_in'] 		+= $row[csf('sewing_in')];
		$prod_array[$row[csf('month')]][$row[csf('buyer_name')]]['sewing_out'] 		+= $row[csf('sewing_out')];
		$prod_array[$row[csf('month')]][$row[csf('buyer_name')]]['finish_qty'] 		+= $row[csf('finish_qty')];
	}
	// echo "<pre>";	 
	// print_r($main_array);
	// echo "</pre>";	
	// ============================================= FOR ORDER QNTY ==========================================
	$ex_data="SELECT a.buyer_name,to_char(to_date(b.pub_shipment_date, 'DD-MM-YYYY'), 'Month') as month,
  	sum(c.ex_factory_qnty) as ex_factory_qnty  	
	from wo_po_details_master a, wo_po_break_down b , pro_ex_factory_mst c
	where  a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name in($company_id) and b.id in($poIds) and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond  $date_cond $year_cond 
	GROUP BY a.buyer_name,b.pub_shipment_date
	order by b.pub_shipment_date asc";
	$ex_data_result=sql_select($ex_data);
	$ex_qty_array = array();
	foreach ($ex_data_result as $row) 
	{
		$ex_qty_array[$row[csf('month')]][$row[csf('buyer_name')]] += $row[csf('ex_factory_qnty')];
	}
	ob_start();	
	if($type==1)
	{
		?>
		<style type="text/css">	            
            .gd-color
            {
				background: #f0f9ff; /* Old browsers */
				background: -moz-linear-gradient(top, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%); /* FF3.6-15 */
				background: -webkit-linear-gradient(top, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* Chrome10-25,Safari5.1-6 */
				background: linear-gradient(to bottom, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f9ff', endColorstr='#a1dbff',GradientType=0 ); /* IE6-9 */
				font-weight: bold;
			}
			.gd-color2
			{
				background: rgb(247,251,252); /* Old browsers */
				background: -moz-linear-gradient(top, rgba(247,251,252,1) 0%, rgba(217,237,242,1) 40%, rgba(173,217,228,1) 100%); /* FF3.6-15 */
				background: -webkit-linear-gradient(top, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* Chrome10-25,Safari5.1-6 */
				background: linear-gradient(to bottom, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7fbfc', endColorstr='#add9e4',GradientType=0 ); /* IE6-9 */
				font-weight: bold;
			}
			.gd-color3
			{
				background: rgb(254,255,255); /* Old browsers */
				background: -moz-linear-gradient(top, rgba(254,255,255,1) 0%, rgba(221,241,249,1) 35%, rgba(160,216,239,1) 100%); /* FF3.6-15 */
				background: -webkit-linear-gradient(top, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* Chrome10-25,Safari5.1-6 */
				background: linear-gradient(to bottom, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#feffff', endColorstr='#a0d8ef',GradientType=0 ); /* IE6-9 */
				border: 1px solid #dccdcd;
				font-weight: bold;
			}
		</style>
		<div><br>
			<fieldset style="width: 1210px; margin: 10px auto">				
				<table width="1190" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table"  >
					<thead>
						<th  style="word-wrap:break-word;word-break: break-all;" width="100">Month</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="130">Buyer</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Order Qty</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Cutting</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Cutting Bal.</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Input</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Input Bal.</th> 
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Output</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Output Bal.</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Finishing</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Finishing Bal.</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Yet To Fin.</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Shipment</th>
						<th  style="word-wrap:break-word;word-break: break-all;" width="80">Shipment Bal.</th>
					</thead>
				</table>
				<div style="width:1210px; overflow-y:scroll; max-height:330px;" id="scroll_body">
					<table width="1190" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="html_search">
						<?
						$sl=1;
						$gr_order_qty = 0;
						$gr_input_qty = 0;
						$gr_input_bal_qty = 0;
						$gr_output_qty = 0;
						$gr_output_bal_qty = 0;
						$gr_cutting_qty = 0;
						$gr_cutting_bal_qty = 0;
						$gr_finish_qty = 0;
						$gr_finish_bal_qty = 0;
						$gr_yet_to_fin_qty = 0;
						$gr_shipment_qty = 0;
						$gr_shipment_bal_qty = 0;
						foreach ($main_array as $month => $month_data) 
						{
							$r=0;
							$sub_order_qty = 0;
							$sub_input_qty = 0;
							$sub_input_bal_qty = 0;
							$sub_output_qty = 0;
							$sub_output_bal_qty = 0;
							$sub_cutting_qty = 0;
							$sub_cutting_bal_qty = 0;
							$sub_finish_qty = 0;
							$sub_finish_bal_qty = 0;
							$sub_yet_to_fin_qty = 0;
							$sub_shipment_qty = 0;
							$sub_shipment_bal_qty = 0;
							foreach ($month_data as $buyer => $row) 
							{
								$bgcolor = ($sl%2) ? "#d5e6ff": "#ffffff";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $sl;?>','<? echo $bgcolor?>')" id="tr_<? echo $sl;?>">
									<? if($r==0){?>
									<td valign="middle" rowspan="<? echo $rowSpan[$month];?>"  style="word-wrap:break-word;word-break: break-all;" width="100" align="left"><? echo $month;?></td>
									<?}$r++;?>
									<td  style="word-wrap:break-word;word-break: break-all;" width="130" align="left"><? echo $buyer_arr[$buyer];?></td>
									<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $orderQty = $main_array[$month][$buyer];?></td>
									<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $prod_array[$month][$buyer]['cutting_qnty'];?></td>
									<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $cutting_bal = $orderQty - $prod_array[$month][$buyer]['cutting_qnty'];?></td>
									<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $prod_array[$month][$buyer]['sewing_in'];?></td>
									<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $input_bal = $prod_array[$month][$buyer]['cutting_qnty']-$prod_array[$month][$buyer]['sewing_in'];?></td> 
									<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $prod_array[$month][$buyer]['sewing_out'];?></td>
									<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $output_bal = $prod_array[$month][$buyer]['sewing_in'] - $prod_array[$month][$buyer]['sewing_out'];?></td>
									<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $prod_array[$month][$buyer]['finish_qty'];?></td>
									<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $finish_bal = $prod_array[$month][$buyer]['sewing_out'] - $prod_array[$month][$buyer]['finish_qty'];?></td>
									<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $yet_toFin = $orderQty - $prod_array[$month][$buyer]['finish_qty'];?></td>
									<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $ex_qnty = $ex_qty_array[$month][$buyer];?></td>
									<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $ex_fact_bal = $orderQty - $ex_qnty;?></td>
								</tr>
								<?
								$sub_order_qty 		+= $orderQty;
								$sub_cutting_qty 	+= $prod_array[$month][$buyer]['cutting_qnty'];
								$sub_cutting_bal_qty+= $cutting_bal;
								$sub_input_qty 		+= $prod_array[$month][$buyer]['sewing_in'];
								$sub_input_bal_qty 	+= $input_bal;
								$sub_output_qty 	+= $prod_array[$month][$buyer]['sewing_out'];
								$sub_output_bal_qty += $output_bal;
								$sub_finish_qty 	+= $prod_array[$month][$buyer]['finish_qty'];
								$sub_finish_bal_qty += $finish_bal;
								$sub_yet_to_fin_qty += $yet_toFin;
								$sub_shipment_qty 	+= $ex_qnty;
								$sub_shipment_bal_qty += $ex_fact_bal;

								$gr_order_qty += $orderQty;
								$gr_cutting_qty += $prod_array[$month][$buyer]['cutting_qnty'];
								$gr_cutting_bal_qty += $cutting_bal;
								$gr_input_qty += $prod_array[$month][$buyer]['sewing_in'];
								$gr_input_bal_qty += $input_bal;
								$gr_output_qty += $prod_array[$month][$buyer]['sewing_out'];
								$gr_output_bal_qty += $output_bal;
								$gr_finish_qty += $prod_array[$month][$buyer]['finish_qty'];
								$gr_finish_bal_qty += $finish_bal;
								$gr_yet_to_fin_qty += $yet_toFin;
								$gr_shipment_qty 	+= $ex_qnty;
								$gr_shipment_bal_qty += $ex_fact_bal;
								$sl++;
							}
							?>
							<tr class="gd-color">
								<td  style="word-wrap:break-word;word-break: break-all;" width="100"></td>
								<td  style="word-wrap:break-word;word-break: break-all;" width="130" align="right"><? echo ucfirst($month);?> Sub Total :</td>
								<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $sub_order_qty; ?></td>
								<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $sub_cutting_qty; ?></td>
								<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $sub_cutting_bal_qty; ?></td>
								<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $sub_input_qty; ?></td>
								<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $sub_input_bal_qty; ?></td> 
								<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $sub_output_qty; ?></td>
								<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $sub_output_bal_qty; ?></td>
								<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $sub_finish_qty; ?></td>
								<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $sub_finish_bal_qty; ?></td>
								<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $sub_yet_to_fin_qty; ?></td>
								<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $sub_shipment_qty; ?></td>
								<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $sub_shipment_bal_qty; ?></td>
							</tr>
							<?
						}
						?>
						
					</table>
				</div>
				<div>
					<table width="1190" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table"  >
						<tr class="gd-color2">
							<td  style="word-wrap:break-word;word-break: break-all;" width="100"></td>
							<td  style="word-wrap:break-word;word-break: break-all;" width="130" align="right">Grand Total</td>
							<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $gr_order_qty; ?></td>
							<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $gr_cutting_qty; ?></td>
							<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $gr_cutting_bal_qty; ?></td>
							<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $gr_input_qty; ?></td>
							<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $gr_input_bal_qty; ?></td> 
							<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $gr_output_qty; ?></td>
							<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $gr_output_bal_qty; ?></td>
							<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $gr_finish_qty; ?></td>
							<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $gr_finish_bal_qty; ?></td>
							<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $gr_yet_to_fin_qty; ?></td>
							<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $gr_shipment_qty; ?></td>
							<td  style="word-wrap:break-word;word-break: break-all;" width="80" align="right"><? echo $gr_shipment_bal_qty; ?></td>
						</tr>
					</table>
				</div>
			</fieldset>					
		</div>
		<?

	}
	
	foreach (glob("$user_name*.xls") as $filename) 
	{
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}
?>
      
 