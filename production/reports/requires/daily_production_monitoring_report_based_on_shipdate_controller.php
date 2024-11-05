<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=="load_drop_down_buyer")
{
	$party="1,3,21,90";
	echo create_drop_down("cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	exit();
}



if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$rpt_type=str_replace("'","",$rpt_type);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_workingcompany_id=str_replace("'","",$cbo_workingcompany_id);
	
	$ship_date_from=str_replace("'","",$txt_ship_date_from);
	$ship_date_to=str_replace("'","",$txt_ship_date_to);
	$po_date_from=str_replace("'","",$txt_po_date_from);
	$po_date_to=str_replace("'","",$txt_po_date_to);

	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$cbo_shipment_status=str_replace("'","",$cbo_shipment_status);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	
	//echo $cbo_company_id."*".$cbo_location_id."*".$cbo_buyer_id."*".$cbo_store_id."*".$txt_job_no."*".$txt_job_id."*".$txt_date_from."*".$txt_date_to ."*".$rpt_type;die;

	if($rpt_type==1)
	{
		if($ship_date_from!="" && $ship_date_to!="")
		{
			if($db_type==0)
			{
				$ship_date_cond="and f.receive_date between '".change_date_format(trim($ship_date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($ship_date_to), "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$ship_date_cond="and f.receive_date between '".change_date_format(trim($ship_date_from),'','',1)."' and '".change_date_format(trim($ship_date_to),'','',1)."'";	
			}
		}
		else
		{
			$ship_date_cond="";
		}

		if($po_date_from!="" && $po_date_to!="")
		{
			if($db_type==0)
			{
				$po_date_cond="and f.receive_date between '".change_date_format(trim($po_date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($po_date_to), "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$po_date_cond="and f.receive_date between '".change_date_format(trim($po_date_from),'','',1)."' and '".change_date_format(trim($po_date_to),'','',1)."'";	
			}
		}
		else
		{
			$po_date_cond="";
		}
		
		$company_arr 	= return_library_array("select id, company_name from lib_company where status_active=1","id","company_name");
		$supplier_arr 	= return_library_array("select id,short_name from lib_supplier where status_active=1","id","short_name");
		$buyer_arr 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$season_arr 	= return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
		$color_arr 		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
		$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");









		$po_sql="select a.id,a.po_number,a.job_no_mst,a.po_received_date,a.shipment_date,a.t_month,a.plan_cut,b.buyer_name,b.style_ref_no,b.gmts_item_id,b.season_buyer_wise,b.set_smv ,sum(a.po_quantity ) as po_qnty,a.unit_price     
from wo_po_break_down a, wo_po_details_master b 
where a.job_no_mst=b.job_no  and b.company_name=3 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0   and a.job_no_mst='OG-20-00665' 


group  by a.id,a.po_number,a.job_no_mst,a.po_received_date,a.shipment_date,a.t_month,a.plan_cut,b.buyer_name,b.style_ref_no,b.gmts_item_id,b.season_buyer_wise,b.set_smv,a.unit_price    

order by a.job_no_mst desc";

		$po_main_sql=sql_select($po_sql);
		foreach ($po_main_sql as $rows)
		{
			$all_po_id_arr[$rows[csf("id")]] = $rows[csf("id")];
			$all_job_arr[$rows[csf("job_no_mst")]] = "'".$rows[csf("job_no_mst")]."'";
		}

		//job chunk

		$all_job_arr = array_filter($all_job_arr);
		$all_job_arr = array_unique(explode(",",implode(",", $all_job_arr)));

		$all_job_nos=implode(",",$all_job_arr);
		$all_job_cond=""; $jobCond="";
		$all_job_cond_2=""; $jobCond_2="";
		if($db_type==2 && count($all_job_arr)>999)
		{
			$all_job_arr_chunk=array_chunk($all_job_arr,999) ;
			foreach($all_job_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				//$jobCond.="  e.id in($chunk_arr_value) or ";
				$jobCond_2.="  b.job_no_mst in($chunk_arr_value) or ";
			}

			//$all_job_cond.=" and (".chop($jobCond,'or ').")";
			$all_job_cond_2.=" and (".chop($jobCond_2,'or ').")";
		}
		else
		{
			$all_job_cond=" and e.id in($all_job_nos)";
			$all_job_cond_2=" and b.job_no_mst in($all_job_nos)";
		}


		//po_chunk
		$all_po_id_arr = array_filter($all_po_id_arr);
		$all_po_id_arr = array_unique(explode(",",implode(",", $all_po_id_arr)));

		if(!empty($all_po_id_arr))
		{
			$all_po_ids=implode(",",$all_po_id_arr);
			$all_po_id_cond=""; $poCond="";
			$all_po_id_cond_2="";$all_po_id_cond_3=""; $poCond_2="";$poCond_3="";
			if($db_type==2 && count($all_po_id_arr)>999)
			{
				$all_po_id_arr_chunk=array_chunk($all_po_id_arr,999) ;
				foreach($all_po_id_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$poCond.="  e.id in($chunk_arr_value) or ";
					$poCond_2.="  b.po_break_down_id in($chunk_arr_value) or ";
					$poCond_3.="  c.po_break_down_id in($chunk_arr_value) or ";
				}

				$all_po_id_cond.=" and (".chop($poCond,'or ').")";
				$all_po_id_cond_2.=" and (".chop($poCond_2,'or ').")";
				$all_po_id_cond_3.=" and (".chop($poCond_3,'or ').")";
			}
			else
			{
				$all_po_id_cond=" and e.id in($all_po_ids)";
				$all_po_id_cond_2=" and b.po_break_down_id in($all_po_ids)";
				$all_po_id_cond_3=" and c.po_break_down_id in($all_po_ids)";
			}

			$booking_sql = sql_select("SELECT a.body_part_id,c.booking_no,a.lib_yarn_count_deter_id,a.color_size_sensitive,c.fabric_color_id,  c.gmts_color_id, c.color_type, d.booking_date, d.pay_mode, d.booking_type, d.entry_form, d.is_short,f.company_name, f.job_no, f.style_ref_no, f.buyer_name, f.client_id, f.season_buyer_wise, f.total_set_qnty, f.job_quantity,c.po_break_down_id, c.fin_fab_qnty, a.uom, c.rate, d.supplier_id
			from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f
			where a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and  f.job_no = e.job_no_mst and c.booking_type =1 and c.booking_no = d.booking_no and c.po_break_down_id = e.id $all_po_id_cond 
			union all
			select a.body_part_id,c.booking_no,a.lib_yarn_count_deter_id,a.color_size_sensitive,c.fabric_color_id,c.gmts_color_id,c.color_type, d.booking_date, d.pay_mode, d.booking_type, d.entry_form, d.is_short,f.company_name,
			f.job_no, f.style_ref_no, f.buyer_name, f.client_id, f.season_buyer_wise,f.total_set_qnty, f.job_quantity,c.po_break_down_id, c.fin_fab_qnty, a.uom, c.rate, d.supplier_id 
			from wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls c , wo_booking_mst d , wo_po_break_down e, wo_po_details_master f
			where a.job_no=c.job_no and a.status_active=1 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and f.job_no = e.job_no_mst  and c.booking_type in(3,4) and c.booking_no = d.booking_no  and c.po_break_down_id = e.id $all_po_id_cond");

			foreach ($booking_sql as  $val)
			{
				$book_info_arr[$val[csf("po_break_down_id")]]["booking_no"] 	= $val[csf("booking_no")];
				$book_info_arr[$val[csf("po_break_down_id")]]["booking_date"] 	= $val[csf("booking_date")];


				/*$book_po_ref[$val[csf("booking_no")]]["company_name"] 	= $val[csf("company_name")];
				$book_po_ref[$val[csf("booking_no")]]["buyer_name"] 	= $val[csf("buyer_name")];
				$book_po_ref[$val[csf("booking_no")]]["job_no"] 		.= $val[csf("job_no")].",";
				$book_po_ref[$val[csf("booking_no")]]["client_id"] 		= $val[csf("client_id")];
				$book_po_ref[$val[csf("booking_no")]]["season"] 		.= $val[csf("season_buyer_wise")].",";
				$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] 	.= $val[csf("style_ref_no")].",";
				$book_po_ref[$val[csf("booking_no")]]["booking_no"] 	= $val[csf("booking_no")];
				$book_po_ref[$val[csf("booking_no")]]["booking_date"] 	= $val[csf("booking_date")];
				$book_po_ref[$val[csf("booking_no")]]["pay_mode"] 		= $pay_mode[$val[csf("pay_mode")]];
				if($val[csf("pay_mode")] == 3 || $val[csf("pay_mode")] == 5)
				{
					$book_po_ref[$val[csf("booking_no")]]["supplier"] = $company_arr[$val[csf("supplier_id")]];
				}else{
					$book_po_ref[$val[csf("booking_no")]]["supplier"] = $supplier_arr[$val[csf("supplier_id")]];
				}

				$job_qnty_arr[$val[csf("job_no")]]["qnty"] = $val[csf("job_quantity")]*$val[csf("total_set_qnty")];
				$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$bookingColor]["qnty"] += $val[csf("fin_fab_qnty")];
				$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$bookingColor]["color_type"] .= $color_type[$val[csf("color_type")]].",";

				$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$bookingColor]["amount"] += $val[csf("fin_fab_qnty")]*$val[csf("rate")];

				$bookingType="";
				if($val[csf('booking_type')] == 4)
				{
					$bookingType = "Sample With Order";
				}
				else
				{
					$bookingType = $booking_type_arr[$val[csf('entry_form')]];
				}
				$book_po_ref[$val[csf("booking_no")]]["booking_type"] = $bookingType;*/
			}
		}
		$color_size_sql = sql_select("select b.job_no_mst,b.po_break_down_id,b.country_ship_date,b.color_number_id from wo_po_color_size_breakdown b where b.status_active=1 and b.is_deleted=0 $all_job_cond_2 $all_po_id_cond_2");
		$ii=1;
		foreach ($color_size_sql as  $val)
		{
			$color_size_arrx[$val[csf("job_no_mst")]][$val[csf("po_break_down_id")]][$val[csf("country_ship_date")]]["color_number_id"]= $ii++;

			//$ii=0;
		}

		/*echo "<pre>";
		print_r($color_size_arrx);
		echo "</pre>";*/


		$embl_array=sql_select("select b.job_no_mst,b.id ,LISTAGG( d.emb_type, ',' ) WITHIN GROUP(  ORDER BY  d.emb_type) AS emb_type 
		from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_embe_cost_dtls d
		where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and c.job_no_mst=d.job_no $all_po_id_cond_3  $all_job_cond_2 
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.emb_name!=3
		group by b.job_no_mst,b.id");

		//$data_array=sql_select("select id, job_no, emb_type,status_active from  wo_pre_cost_embe_cost_dtls where  emb_name!=3 $all_job_cond_2 order by id");
		
		$emb_type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,99=>$blank_array);


		foreach( $embl_array as $row )
		{
			$embType= implode(",", array_unique(explode(",", $row[csf("emb_type")]))) ;
			$embType_arr=explode("'", $embType);
			foreach ($embType_arr as $val) {
				//$emb_type_arr[$row[csf("job_no_mst")]][$row[csf("id")]]["emb_type"]= $val[csf("id")];
			}
			$emb_type_arr[$row[csf("job_no_mst")]][$row[csf("id")]]["emb_type"]= $embType;
			
		}
		/*echo "<pre>";
		print_r($emb_type_arr);
		echo "</pre>";*/
		$sewing_serv_company_array=sql_select("select c.po_break_down_id,c.item_number_id,c.serving_company,
			sum(case when c.production_type=5 then d.production_qnty end) as sewing_out_qnty ,
			sum(case when c.production_type=4 then d.production_qnty end) as sewing_in_qnty, 
			sum(case when c.production_type=3 and c.embel_name=3 then d.production_qnty end) as wash_qnty, 


			from pro_garments_production_mst c,pro_garments_production_dtls d where c.production_type in(4,5) and c.status_active=1 and c.is_deleted=0 $all_po_id_cond_3 group by c.po_break_down_id,c.item_number_id,c.serving_company");
		foreach( $sewing_serv_company_array as $row )
		{
			$sewing_data_arr[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["serving_company"]= $row[csf("serving_company")];
			$sewing_data_arr[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["sewing_out_qnty"]= $row[csf("sewing_out_qnty")];
			$sewing_data_arr[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["sewing_in_qnty"]= $row[csf("sewing_in_qnty")];
			$sewing_data_arr[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["wash_qnty"]= $row[csf("wash_qnty")];
		}

		


		ob_start();
		?>

		<fieldset style="width:6790px;">
			<table cellpadding="0" cellspacing="0" width="6770">
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? //if($date_from!="") echo "From : ".change_date_format(str_replace("'","",$txt_date_from)) ;?> <? if($date_to!="") echo "To : ".change_date_format(str_replace("'","",$txt_date_to)) ;?></strong></td>
				</tr>
			</table>

			<table width="6770" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
				<thead>
					<tr>
						<th colspan="21"style="background: yellow;"></th>
						<th colspan="5" style="background: grey;">Grey Fabric and Dyeing</th>
						<th colspan="3" style="background: #7b9abe;">Finish Fabric</th>
						<th colspan="3" style="background: #f0b87a;">AOP Fabric</th>
						<th colspan="2"style="background: yellow;">Cutting</th>
						<th colspan="5"style="background: #d0d3cf;">Printing</th>
						<th colspan="5"style="background: #8bb9dd;">Embroidery</th>
						<th colspan="5"style="background: #82c59c;">Wash</th>
						<th colspan="6"style="background: #f0b87a;">Seweing</th>
						<th colspan="5"style="background: #ffc65a;">Finishing</th>
						<th colspan="4"style="background: yellow;">Shipment</th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="150">Buyer</th>
						<th width="200">Style</th>
						<th width="100">Job No</th>
						<th width="100">Season</th>
						<th width="100">Main Inst No</th>
						<th width="100">KD Received Date</th>
						<th width="120">PO Receive Date</th>
						<th width="100">Ship Month</th>
						<th width="60">TOD</th>
						<th width="100">Production Time</th>
						<th width="150">PO No</th>
						<th width="100">Gmt Item</th>
						<th width="100">Embellish. Type</th>
						<th width="100">Avg. SMV</th>
						<th width="100">Price</th>
						<th width="100">No of Color</th>
						<th width="100">Total Order Qty (Pcs)</th>
						<th width="100">GMT Production Unit</th>
						<th width="100">Order Qty (Pcs)</th>
						<th width="100">Plun cut Qty (Pcs )</th>
						
						<th width="100">Grey Fabric Req. Qty</th>
						<th width="100">Knitting Qty.</th>
						<th width="100">Knitting Balance</th>	
						<th width="100">Dyeing Qty.</th>
						<th width="100">Dyeing Balance</th> 


						<th width="100">Fin Fab Req Qty</th>
						<th width="100">Finishing Delivery</th>
						<th width="100">Finish Delivery Balance</th>


						<th width="100">AOP Req. Qty.</th>
						<th width="100">AOP Delivery Qty.</th>
						<th width="100">AOP Delivery Balance</th>

						<th width="100">Cutting Qty Pcs</th>
						<th width="100">Cutting Bal</th>

						<th width="100">Print Req. Qty</th>
						<th width="100">Print Sent Qty</th>
						<th width="100">Print Sent Bal</th>
						<th width="100">Print Receive Qty</th>	
						<th width="100">Print Rcv Bal</th>	

						<th width="100">EMB Req. Qty</th>	
						<th width="100">EMB Sent Qty</th>	
						<th width="100">EMB Sent Bal</th>	
						<th width="100">EMB Receive Qty</th>
						<th width="100">EMB Rcv Bal</th>


						<th width="100">Wash Req. Qty</th>	
						<th width="100">Wash Sent Qty</th>	
						<th width="100">Wash Sent Bal</th>	
						<th width="100">Wash Receive Qty</th>	
						<th width="100">Wash Rcv Bal</th>

						<th width="100">Sewing Input Qty</th>	
						<th width="100">Input Bal</th>
						<th width="100">Input WIP</th>
						<th width="100">Sewing Output Qty</th>	
						<th width="100">Sewing Balance</th>	
						<th width="100">Sewing WIP</th>


						<th width="100">Iron Qty</th>
						<th width="100">Iron Bal</th>	
						<th width="100">Carton Qty</th>	
						<th width="100">Carton Bal</th>
						<th width="100">Carton WIP</th>

						<th width="100">Ship Qty</th>
						<th width="100">Ship Bal</th>	
						<th width="100">Ship Mode</th>
						<th>Ship Status</th>
					</tr>
					
					
				</thead>
			</table>
			<div style="width: 6790px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="6770" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 

					<?
					$i=1;

					foreach ($po_main_sql as $row)
					{

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						
							<td width="30"><? echo $i; ?></td>
							<td width="150"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
							<td width="200"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
							<td width="100"><? echo $row[csf('job_no_mst')]; ?></td>
							<td width="100"><? echo $season_arr[$row[csf('season_buyer_wise')]]; ?></td>
							<td width="100"><? echo $book_info_arr[$row[csf("id")]]["booking_no"]; ?></td>
							<td width="100"><? echo $book_info_arr[$row[csf("id")]]["booking_date"]; ?></td>
							<td width="120"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
							<td width="100"><? echo $months_short[$row[csf('t_month')]]; ?></td>
							<td width="60"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
							<td width="100">
								<?  
									//$diff=date_diff($row[csf('shipment_date')],$row[csf('shipment_date')]); 
									//echo $diff->format("%R%a days");


								//echo	$daysOnHand = datediff("d",change_date_format($row[csf('shipment_date')],'','',1),cdate("Y-m-d"));
									//$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$booking_no][$prodStrArr[0]]['min_date'],'','',1),date("Y-m-d"));



								?>


							</td>
							<td width="150"><p><? echo $row[csf('po_number')]; ?></p></td>
							<td width="100"><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
							<td width="100"><? echo$emb_type_arr[$row[csf("job_no_mst")]][$row[csf("id")]]["emb_type"]; ?></td>
							<td width="100"><? echo $row[csf('set_smv')]; ?></td>
							<td width="100"align="right"><? echo $row[csf('unit_price')]; ?></td>
							<td width="100"align="center"><? echo 'color no'; ?></td>
							<td width="100"align="right"><? echo $row[csf('po_qnty')]; ?></td>
							<td width="100"align="center"><? echo $sewing_data_arr[$row[csf("id")]][$row[csf("gmts_item_id")]]["serving_company"]; ?></td>
							<td width="100" align="right"><? echo $row[csf('po_qnty')]; ?></td>
							<td width="100" align="right"><? echo $row[csf('plan_cut')]; ?></td>
							<td width="100">Grey Fabric Req. Qty</td>
							<td width="100">Knitting Qty.</td>
							<td width="100">Knitting Balance</td>	
							<td width="100">Dyeing Qty.</td>
							<td width="100">Dyeing Balance</td> 
							<td width="100">Fin Fab Req Qty</td>
							<td width="100">Finishing Delivery</td>
							<td width="100">Finish Delivery Balance</td>
							<td width="100">AOP Req. Qty.</td>
							<td width="100">AOP Delivery Qty.</td>
							<td width="100">AOP Delivery Balance</td>	
							<td width="100">Cutting Qty Pcs</td>
							<td width="100">Cutting Bal</td>
							<td width="100">Print Req. Qty</td>
							<td width="100">Print Sent Qty</td>
							<td width="100">Print Sent Bal</td>
							<td width="100">Print Receive Qty</td>	
							<td width="100">Print Rcv Bal</td>	
							<td width="100">EMB Req. Qty</td>	
							<td width="100">EMB Sent Qty</td>	
							<td width="100">EMB Sent Bal</td>	
							<td width="100">EMB Receive Qty</td>
							<td width="100">EMB Rcv Bal</td>
							<td width="100">Wash Req. Qty</td>	
							<td width="100">Wash Sent Qty</td>	
							<td width="100">Wash Sent Bal</td>	
							<td width="100">Wash Receive Qty</td>	
							<td width="100">Wash Rcv Bal</td>
							<td width="100"align="right"><? echo $sewing_data_arr[$row[csf("id")]][$row[csf("gmts_item_id")]]["sewing_in_qnty"]; ?></td>	
							<td width="100">Input Bal</td>
							<td width="100">Input WIP</td>
							<td width="100" align="right"><? echo $sewing_data_arr[$row[csf("id")]][$row[csf("gmts_item_id")]]["sewing_out_qnty"]; ?></td>	
							<td width="100">Sewing Balance</td>	
							<td width="100">Sewing WIP</td>
							<td width="100">Iron Qty</td>
							<td width="100">Iron Bal</td>	
							<td width="100">Carton Qty</td>	
							<td width="100">Carton Bal</td>
							<td width="100">Carton WIP</td>
							<td width="100">Ship Qty</td>
							<td width="100">Ship Bal</td>	
							<td width="100">Ship Mode</td>
							<td >Ship Status</td>
						</tr>
						<?	
						$i++;
					}

						

					?>	
								
					<tr>
						<td colspan="16" align="right" style="font-size: 20px;"><strong>Grand Total : </strong></td>
						
						<td></td>
					</tr>
				</table>
			</div>
			
		</fieldset>
		<?
	}
		

	
    $html = ob_get_contents();
    ob_clean();
    foreach (glob($user_id."*.xls") as $filename) {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$rpt_type";
    exit();
}



?>