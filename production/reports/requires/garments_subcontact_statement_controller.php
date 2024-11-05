<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	// echo "<pre>";
	// print_r($process);
	extract(check_magic_quote_gpc( $process ));
	$production_process[1001]="Embellishment";
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name"); 
	$supplier_library = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");

	$order_num =str_ireplace("'","",$txt_order_no);
	$rpt_type =str_ireplace("'","",$rpt_type);
	$style_no=str_replace("'","",$txt_style);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$cbo_buyer_name =str_replace("'","",$cbo_buyer_name) ;
	$hidden_order_id =str_replace("'","",$hidden_order_id) ;
	$hidden_factory_id =str_replace("'","",$hidden_factory_id) ;
	$prod_type =str_replace("'","",$cbo_prod_process) ;
	if($rpt_type==1)
	{
		if($cbo_buyer_name==0) $buyer_name_cond=""; else $buyer_name_cond=" and a.buyer_name=$cbo_buyer_name";
		if($hidden_order_id="") $order_id_cond=""; else $order_id_cond=" and b.id in($hidden_order_id)";
		if($style_no=="") $style_cond=""; else $style_cond=" and a.style_ref_no like '%$style_no%'";
		if($hidden_order_id="")
		{
			if($order_num=="") $order_cond=""; else $order_cond=" and b.po_number ='".$order_num."'";
		}
		// echo $order_cond;
		if($internal_ref=="") $internal_cond=""; else $internal_cond=" and b.grouping like '%$internal_ref%'";
		if($hidden_factory_id==0) $factory_id_cond=""; else $factory_id_cond=" and c.serving_company in($hidden_factory_id)";
	
		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")
		{ 	
			$txt_date_cond="";
		}
		else
		{
			$txt_date_cond=" and c.production_date between $txt_date_from and $txt_date_to";
			if(str_replace("'","",trim($cbo_info_type))==3)
			{
				//echo $txt_date_cond=" and c.insert_date between $txt_date_from and $txt_date_to"; 
				if($db_type==0)
				{
					$txt_date_cond=" and DATE(c.insert_date) between DATE($txt_date_from) and DATE($txt_date_to)";
				}
				else
				{
					$txt_date_cond=" and TRUNC(c.insert_date) between TO_DATE($txt_date_from,'dd/mon/yyyy') and TO_DATE($txt_date_to,'dd/mon/yyyy')";
				}
			} 
		}
	
		if($prod_type==5){
			$prod_con_in=" and c.production_type=4";
			$prod_con_out=" and c.production_type=5";
			$selectField=",c.production_type";
			$cPTypeArr=$production_process;
		}
		else if($prod_type==1001){
			$prod_con_in=" and c.production_type=2";
			$prod_con_out=" and c.production_type=3";
			$selectField=",c.embel_name as production_type";
			$cPTypeArr=$emblishment_name_array;
		}
		
		if($db_type==0) $year_cond="YEAR(a.insert_date)";
		else if($db_type==2) $year_cond="to_char(a.insert_date,'YYYY')";
		
		$sql = "SELECT a.buyer_name, a.job_no, a.style_ref_no,  a.job_no_prefix_num,b.id, $year_cond as year $selectField ,b.id, b.po_number, b.grouping, b.po_quantity,c.production_date, c.serving_company,d.production_qnty as sew_out_qty,d.reject_qty from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d where b.job_id=a.id and c.id=d.mst_id and a.company_name=$cbo_company_name and b.id=c.po_break_down_id and c.production_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $prod_con_out $buyer_name_cond $style_cond $internal_cond $factory_id_cond $txt_date_cond $order_cond"; 
		//   echo $sql;
		$data_arr=sql_select($sql);
		foreach($data_arr as $row)
		{
			$key=$row[csf('serving_company')].$row[csf('buyer_name')].$row[csf('job_no_prefix_num')].$row[csf('style_ref_no')].$row[csf('production_type')];
			$dataArr[$key]=array(
				serving_company=>$row[csf('serving_company')],
				buyer_name=>$row[csf('buyer_name')],
				job_no_prefix_num=>$row[csf('job_no_prefix_num')],
				style_ref_no=>$row[csf('style_ref_no')],
				production_type=>$row[csf('production_type')],
			);
			//$sewing_in_qty[$key]+=$row[csf('sew_in_qty')];
			$sewing_out_qty[$key]+=$row[csf('sew_out_qty')];
			$reject_qty[$key]+=$row[csf('reject_qty')];
			$po_no[$key][$row[csf('po_number')]]=$row[csf('po_number')];
			$grouping[$key][$row[csf('grouping')]]=$row[csf('grouping')];
			$po_qty[$key][$row[csf('id')]]=$row[csf('po_quantity')];
			$po_id[$key][$row[csf('id')]]=$row[csf('id')];
			$po_serving_com_id[$key][$row[csf('id')]]=$row[csf('id')].$row[csf('serving_company')];
			$po_id_arr[$row[csf('id')]]=$row[csf('id')];
		}
	
		$po_id_string=implode(",",$po_id_arr);
		// echo $po_id_string."__".$cbo_info_type;
	
		if($po_id_string=="" && str_replace("'","",trim($cbo_info_type))!=3 && str_replace("'","",trim($cbo_info_type))!=2)
		{
			echo "<h2>"."Production Not Found"."</h2>";die;
		}
		if($po_id_string==""){$po_breakdonw_in_po_id_cond="";} else{ $po_breakdonw_in_po_id_cond="c.po_break_down_id in($po_id_string) and";}				
		$sql = "SELECT c.po_break_down_id,d.production_qnty as sew_in_qty, c.serving_company from pro_garments_production_mst c, pro_garments_production_dtls d where c.id=d.mst_id and $po_breakdonw_in_po_id_cond c.company_id=$cbo_company_name and c.production_source=3 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $prod_con_in ";
		  //echo $sql;
		$data_arr=sql_select($sql);
		foreach($data_arr as $row)
		{
			$key_id=$row[csf('po_break_down_id')].$row[csf('serving_company')];
			$sewing_in_qty[$key_id]+=$row[csf('sew_in_qty')];
		}	
		ob_start();
		if(str_replace("'","",trim($cbo_info_type))==1)
		{
			?>
			<div>
			<table width="1160" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="12" align="center" style="border:none;font-size:14px; font-weight:bold"><? echo $report_title; ?> (<? echo $production_process[$prod_type]; ?>)</td>
				</tr>
				<tr style="border:none;">
					<td colspan="12" align="center" style="border:none; font-size:16px; font-weight:bold">
					Company Name: <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                </td>
				</tr>
				<tr style="border:none;">
					<td colspan="12" align="center" style="border:none;font-size:12px; font-weight:bold">
					<? echo "From $fromDate To $toDate" ;?>
					</td>
				</tr>
			</table>
			<div style="width:1248px;">
			<table width="1230" border="1" class="rpt_table" rules="all" align="left">
				<thead>
					<tr>
						<th width="35" >SL</th>
						<th width="120">Working Factory</th>
						<th width="110">Buyer Name</th>
						<th width="60">Job No</th>
						<th width="120">Style Name</th>
						<th width="100">Order Number</th>
						<th width="100">Internal Ref.</th>
						<th width="100">Type</th>
						<th width="100">Order Qty.</th>
						<th width="70">Total Sent</th>
						<th width="70">Total Receive</th>
						<th width="70">Reject</th>
						<th>Blance</th>
					</tr>
				</thead>
			</table> 
			</div>   
			<div style="width:1248px; max-height:280px; overflow-y:auto;" id="scroll_body">
				<table width="1230" border="1" class="rpt_table" rules="all" id="table_body" align="left">
					<tbody>
						<?
						$i=1;
						foreach($dataArr as $key=>$row)
						{
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							$sew_in_qty=0;
							foreach($po_serving_com_id[$key] as $po){
							$sew_in_qty+=$sewing_in_qty[$po];
							}
							$tot_po_qty+=array_sum($po_qty[$key]);
							$tot_sew_in_qty+=$sew_in_qty;
							$tot_sew_out_qty+=$sewing_out_qty[$key];
							$tot_reject_qty+=$reject_qty[$key];
							$tot_sew_bal_qty+=($sew_in_qty-$sewing_out_qty[$key]-$reject_qty[$key]);
							$yet_to_rec=0;
							?>
							<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
								<td width="35" align="center"><? echo $i;?></td>
								<td width="120"><p><? echo $supplier_library[$row['serving_company']]; ?></p></td>
								<td width="110"><? echo $buyerArr[$row['buyer_name']];?></td>
								<td width="60"><? echo $row['job_no_prefix_num'];?></td>
								<td width="120"><p><? echo $row['style_ref_no']; ?></p></td>
								<td width="100"><p><? echo implode(',',$po_no[$key]); ?></p></td>
								<td width="100"><p><? echo implode(',',$grouping[$key]); ?></p></td>
								<td width="100"><p><? echo $cPTypeArr[$row['production_type']]; ?></p></td>
								<td width="100" align="right"><? echo array_sum($po_qty[$key]); ?></td>
								<td width="70" align="right">
									<a href="javascript:fn_pro_dtls('<? echo implode(',',$po_id[$key]);?>','sew_input','<? echo $production_process[$prod_type];?> Issue details',950,<? echo $prod_type;?>,<? echo $txt_date_from;?>,<? echo $txt_date_to;?>,'<? echo $row['serving_company'];?>')">
										<? echo number_format($sew_in_qty); ?>                        			
									</a>
								</td>
								<td width="70" align="right">
									<a href="javascript:fn_pro_dtls('<? echo implode(',',$po_id[$key]);?>','sew_output','<? echo $production_process[$prod_type];?> Receive details',950,<? echo $prod_type;?>,<? echo $txt_date_from;?>,<? echo $txt_date_to;?>,'<? echo $row['serving_company'];?>')">
										<? echo number_format($sewing_out_qty[$key]); ?>                        			
									</a>
								</td>
								<td width="70" align="right">
										<?php echo number_format($reject_qty[$key]);?>
								</td>
								<td align="right">
									<a href="javascript:fn_pro_dtls('<? echo implode(',',$po_id[$key]);?>','sew_blance','<? echo $production_process[$prod_type];?> Blance details',950,<? echo $prod_type;?>,<? echo $txt_date_from;?>,<? echo $txt_date_to;?>,'<? echo $row['serving_company'];?>')">
										<?  echo number_format($sew_in_qty-$sewing_out_qty[$key]-$reject_qty[$key]); ?>                        		
									</a>
								</td>
							</tr>
						<?
						$i++; 
						} 
						?> 
					</tbody>
				</table>
			</div>
			<div style="width:1248px;">
			<table width="1230" border="1" class="rpt_table" rules="all" id="table_body" align="left">
				<tfoot>
					<th colspan="8" align="right"><strong>Total :</strong></th>
					<th width="100" align="right"><?php echo $tot_po_qty; ?></th>
					<th width="70" align="right"><?php echo $tot_sew_in_qty; ?></th>
					<th width="70" align="right"><?php echo $tot_sew_out_qty; ?></th>
					<th width="70" align="right"><?php echo $tot_reject_qty;?></th>
					<th width="163" align="right"><?php echo $tot_sew_bal_qty; ?></th>
				</tfoot>
			</table>
			</div>
			
			</div>
			   <?php 
		}
	
		if(str_replace("'","",trim($cbo_info_type))==2)
		{
			$prod_type_cond="";
			if($prod_type==5)
			{ 
				$prod_type_cond ="and c.production_type in (4,5)";
				$having_cond= "having (sum(case when c.production_type=4 then c.production_quantity else 0 end)) > 0 OR (sum(case when c.production_type=5 then c.production_quantity else 0 end)) > 0";
			}
			else if($prod_type==1001)
			{ 
				$prod_type_cond ="and c.production_type in (2,3)"; 
				$having_cond=" having (sum(case when c.production_type=2 then c.production_quantity else 0 end)) > 0 OR (sum(case when c.production_type=3 then c.production_quantity else 0 end)) > 0";
			}
			?>
			<div style="width:1240px;">
			<table width="1240px" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="13" align="center" style="border:none;font-size:14px; font-weight:bold"><? echo $report_title; ?> (<? echo $production_process[$prod_type]; ?>)</td>
				</tr>
				<tr style="border:none;">
					<td colspan="13" align="center" style="border:none; font-size:16px; font-weight:bold">
					Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="13" align="center" style="border:none;font-size:12px; font-weight:bold">
					<? echo "From $fromDate To $toDate" ;?>
					</td>
				</tr>
			</table>
			<table width="1220" border="1" class="rpt_table" rules="all" align="left">
				<thead>
					<tr>
						<th rowspan="2" width="40" >SL</th>
						<th colspan="7">Order Details</th>
						<th rowspan="2" width="120">Working Factory</th>
						<? if($prod_type==1001){?><th colspan="3">Embellishment Status</th><? } ?>
						<? if($prod_type==5){?><th colspan="4">Sewing Status</th><? } ?>
					</tr>
					<tr>
						<th width="110">Buyer Name</th>
						<th width="60">Job No</th>
						<th width="120">Style</th>
						<th width="100">Order Number</th>
						<th width="100">Gmts Item Name</th>
						<th width="70">Ship Date</th>
						<th width="100">Order Qty.</th>
						<? if($prod_type==1001){?>
						<th width="100">Total Sent</th>
						<th width="100">Total Receive</th>
						<th width="100">Yet To Receive</th>
						<? } ?>
						<? if($prod_type==5){?>
						<th width="100">Total Sew. In. Qty.</th>
						<th width="100">Total Sew Prod. Qty.</th>
						<th width="100">Total Gmts. Rcv. Qty.</th>
						<th>Yet To Sew Output</th>
						<? } ?>
					</tr>
				</thead>
			</table>    
			<div style="width:1240px; max-height:300px; overflow-y:auto;" id="scroll_body">
				<table width="1220" border="1" class="rpt_table" rules="all" id="table_body" align="left">
					<tbody>
						<?
						if($db_type==0) $year_cond="YEAR(a.insert_date)";
						else if($db_type==2) $year_cond="to_char(a.insert_date,'YYYY')";
				
						$sql = "select a.buyer_name, a.job_no, a.style_ref_no, a.gmts_item_id, a.job_no_prefix_num, $year_cond as year,
						b.id, b.po_number, b.shipment_date, b.po_quantity, 
						c.production_date, c.serving_company,
						sum(case when c.production_type=2 then c.production_quantity else 0 end) as embl_sent_qty,
						sum(case when c.production_type=3 then c.production_quantity else 0 end) as embl_rec_qty,
						sum(case when c.production_type=4 then c.production_quantity else 0 end) as sew_in_qty,
						sum(case when c.production_type=5 then c.production_quantity else 0 end) as sew_out_qty
						
						from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c
						
						where b.job_no_mst=a.job_no and a.company_name=$cbo_company_name and b.id=c.po_break_down_id and c.production_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
						$buyer_name_cond $style_cond $factory_id_cond $txt_date_cond $prod_type_cond $order_cond
						group by a.buyer_name, a.job_no, a.style_ref_no, a.gmts_item_id, a.job_no_prefix_num, b.id, b.po_number, b.shipment_date, b.po_quantity, c.production_date, c.serving_company, a.insert_date $having_cond order by c.production_date ";
						// echo $sql;
	
						$tot_sent_in_qty=0;
						$tot_rec_out_qty=0;
						$tot_sent_out_qty=0;
						$data_arr=sql_select($sql);
	
						if(empty($data_arr))
							{
								echo "<h2>"."Production Not Found"."</h2>";die;
							}
						$i=1;
						foreach($data_arr as $row)
						{
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							$tot_po_qty+=$row[csf('po_quantity')];
							$yet_to_rec=0;
							$gmts_name="";
							$item_id=array_unique(explode(",",$row[csf("gmts_item_id")]));
							foreach($item_id as $val)
							{
								if($gmts_name=="") $gmts_name=$garments_item[$val]; else $gmts_name.=', '.$garments_item[$val];
							}
							?>
							<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
								<td width="40" align="center"><? echo $i;?></td>
								<td width="110"><? echo $buyerArr[$row[csf('buyer_name')]];?></td>
								<td width="60"><? echo $row[csf('job_no_prefix_num')];?></td>
								<td width="120"><div style="word-wrap:break-word; width:120px"><? echo $row[csf('style_ref_no')]; ?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('po_number')]; ?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $gmts_name; ?></div></td>
								<td width="70"><? echo change_date_format($row[csf('shipment_date')]);?></td>
								<td width="100" align="right"><? echo number_format($row[csf('po_quantity')]); ?></td>
								<td width="120"><div style="word-wrap:break-word; width:120px"><? echo $supplier_library[$row[csf('serving_company')]]; ?></div></td>
								<? if($prod_type==1001){?>
								<td width="100" align="right">
									<? 
										echo number_format($row[csf('embl_sent_qty')]); 
										$tot_sent_in_qty+=$row[csf('embl_sent_qty')];
									?>
								</td>
								<td width="100" align="right">
									<? 
										echo number_format($row[csf('embl_rec_qty')]); 
										$tot_rec_out_qty+=$row[csf('embl_rec_qty')];
									?>
								</td>
								<td width="100" align="right">
									<? 
										$yet_to_rec=$row[csf('embl_sent_qty')]-$row[csf('embl_rec_qty')]; 
										echo number_format($yet_to_rec); 
										$tot_sent_out_qty+=$yet_to_rec;
									?>
								</td>
								<? } ?>
								<? if($prod_type==5){?>
								<td width="100" align="right">
									<? 
										echo number_format($row[csf('sew_in_qty')]); 
										$tot_sent_in_qty+=$row[csf('sew_in_qty')];
									?>
								</td>
								<td width="100" align="right">
									<? 
										echo number_format($row[csf('sew_out_qty')]); 
										$tot_rec_out_qty+=$row[csf('sew_out_qty')];
									?>
								</td>
								<td width="100" align="right">
									<? 
										echo number_format($row[csf('sew_out_qty')]); 
										$tot_sent_out_qty+=$row[csf('sew_out_qty')];
									?>
								</td>
								<td align="right"><? //echo round($rows['amount']);?></td>
								<? } ?>
							</tr>
							<?
							$i++; 
						} 
						?> 
					</tbody>
					<tfoot>
						<? if($prod_type==1001){?>
						<th colspan="9" align="right"><strong>Total :</strong></th>
						<th width="100" align="right"><?php echo number_format($tot_sent_in_qty); ?></th>
						<th width="100" align="right"><?php echo number_format($tot_rec_out_qty); ?></th>
						<th width="100" align="right"><?php echo number_format($tot_sent_out_qty); ?></th>
						<? }
						   if($prod_type==5){?>
						<th colspan="9" align="right"><strong>Total :</strong></th>
						<th width="100" align="right"><?php echo number_format($tot_sent_in_qty); ?></th>
						<th width="100" align="right"><?php echo number_format($tot_rec_out_qty); ?></th>
						<th width="100" align="right"><?php echo number_format($tot_sent_out_qty); ?></th>
						<th width="" align="right"><?php //echo $tot_sew_bal_qty; ?></th>
						<? } ?>
					</tfoot>
				</table>
				</div>
			</div>
			
			<?php 
		}
	
		if(str_replace("'","",trim($cbo_info_type))==3)
		{
			if($prod_type==5)
			{
				$production_type_cond=" and c.production_type in (4,5)";
				$production_data_cond="sum(case when c.production_type=4 then c.production_quantity else 0 end) as production_in_qty, sum(case when c.production_type=5 then c.production_quantity else 0 end) as production_out_qty";
				$selectField=", 5 as production_type";
				$group_by_selectField="";
			}
			else if($prod_type==1001)
			{
				$production_type_cond=" and c.production_type in (2,3)";
				$production_data_cond="sum(case when c.production_type=2 then c.production_quantity else 0 end) as production_in_qty, sum(case when c.production_type=3 then c.production_quantity else 0 end) as production_out_qty";
				$selectField=",c.embel_name as production_type";
				$group_by_selectField=", c.embel_name";
			}
			
			?>
	
			<div>
			<table width="1060" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="12" align="center" style="border:none;font-size:14px; font-weight:bold"><? echo $report_title; ?> (<? echo $production_process[$prod_type]; ?>)</td>
				</tr>
				<tr style="border:none;">
					<td colspan="12" align="center" style="border:none; font-size:16px; font-weight:bold">
					Company Name: <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                </td>
				</tr>
				<tr style="border:none;">
					<td colspan="12" align="center" style="border:none;font-size:12px; font-weight:bold">
					<? echo "From $fromDate To $toDate" ;?>
					</td>
				</tr>
			</table>
			<div style="width:1078px;">
			<table width="1060" border="1" class="rpt_table" rules="all" align="left">
				<thead>
					<tr>
						<th width="35" >SL</th>
						<th width="120">Working Factory</th>
						<th width="110">Buyer Name</th>
						<th width="60">Job No</th>
						<th width="120">Style Name</th>
						<th width="100">Order Number</th>
						<th width="100">Type</th>
						<th width="100">Order Qty.</th>
						<th width="70">Total Sent</th>
						<th width="70">Total Receive</th>
						<th>Blance</th>
					</tr>
				</thead>
			</table> 
			</div>   
			<div style="width:1078px; max-height:280px; overflow-y:auto;" id="scroll_body">
				<table width="1060" border="1" class="rpt_table" rules="all" id="table_body" align="left">
					<tbody>
					<?
					$i=1;
					$production_in_qty_arr=array(); 
					$production_out_qty_arr=array(); 
					$order_qty_arr=array();
					$sql = "SELECT a.buyer_name, a.job_no, a.style_ref_no, a.job_no_prefix_num,b.id , b.po_number, b.po_quantity $selectField, c.serving_company, c.po_break_down_id, $production_data_cond from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where b.job_no_mst=a.job_no and a.company_name=$cbo_company_name and b.id=c.po_break_down_id and c.production_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_name_cond $style_cond $factory_id_cond $production_type_cond $txt_date_cond $order_cond group by a.buyer_name, a.job_no, a.style_ref_no, a.job_no_prefix_num,b.id , b.po_number, b.po_quantity, c.serving_company, c.po_break_down_id $group_by_selectField" ;
					// echo $sql; //die;
					$dataArr=sql_select($sql);
					if(empty($dataArr))
						{
							echo "<h2>"."Production Not Found"."</h2>";die;
						}
					foreach($dataArr as $row)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						$production_in_qty_arr[]=$row[csf('production_in_qty')];
						$production_out_qty_arr[]=$row[csf('production_out_qty')];
						$order_qty_arr[]=$row[csf('po_quantity')];
						?>
						<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
							<td width="35" align="center"><? echo $i;?></td>
							<td width="120"><p><? echo $supplier_library[$row[csf('serving_company')]]; ?></p></td>
							<td width="110"><? echo $buyerArr[$row[csf('buyer_name')]];?></td>
							<td width="60"><? echo $row[csf('job_no_prefix_num')];?></td>
							<td width="120"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
							<td width="100"><p><? echo $row[csf('po_number')]; ?></p></td>
							<td width="100"><p><? echo $cPTypeArr[$row[csf('production_type')]]; ?></p></td>
							<td width="100" align="right"><? echo $row[csf('po_quantity')]; ?></td>
	
							<td width="70" align="right"><? echo number_format($row[csf('production_in_qty')]); ?></td>
							
	
							<td width="70" align="right"><? echo number_format($row[csf('production_out_qty')]); ?></td>
							
	
							<td align="right"><?  //echo number_format($row[csf('production_in_qty')]-$row[csf('production_out_qty')]); ?></td>
						</tr>
					   <?
					   $i++; 
				    } 
				   ?> 
				</tbody>
			</table>
			</div>
			<div style="width:1078px;">
			<table width="1060" border="1" class="rpt_table" rules="all" id="table_body" align="left">
				<tfoot>
					<th colspan="7" align="right"><strong>Total :</strong></th>
					<th width="100" align="right"><?php echo array_sum($order_qty_arr); ?></th>
					<th width="70" align="right"><?php echo array_sum($production_in_qty_arr); ?></th>
					<th width="70" align="right"><?php echo array_sum($production_out_qty_arr); ?></th>
					<th width="163" align="right"><?php //echo $tot_sew_bal_qty; ?></th>
				</tfoot>
			</table>
			</div>
			
			</div>
		   <?php 
		}
	}

	if($rpt_type==2)
	{
		$search_cond="";
		if($cbo_buyer_name){ $search_cond.=" and a.buyer_name=$cbo_buyer_name"; }
		if($hidden_order_id!=''){ $search_cond.=" and b.id in($hidden_order_id)"; }
		if($style_no!=""){ $search_cond.=" and a.style_ref_no like '%$style_no%'"; }
		if($order_num!=""){ $search_cond.=" and b.po_number ='".$order_num."'"; }
		if($internal_ref!=""){$search_cond.=" and b.grouping like '%$internal_ref%'"; }
		if($hidden_factory_id!=""){ $search_cond.=" and c.serving_company in($hidden_factory_id)";}
	
		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
		{ 	
			$search_cond.=" and c.production_date between $txt_date_from and $txt_date_to";
		}
			
		ob_start();	
		if(str_replace("'","",trim($cbo_info_type))==2)
		{
			$prod_type_cond="";
			if($prod_type==5)
			{ 
				$search_cond.=" and c.production_type in (4,5)";
				$having_cond= "having (sum(case when c.production_type=4 then c.production_quantity else 0 end)) > 0 OR (sum(case when c.production_type=5 then c.production_quantity else 0 end)) > 0";
			}
			else if($prod_type==1001)
			{ 
				$search_cond.=" and c.production_type in (2,3)"; 
				$having_cond=" having (sum(case when c.production_type=2 then c.production_quantity else 0 end)) > 0 OR (sum(case when c.production_type=3 then c.production_quantity else 0 end)) > 0";
			}
			?>
			<div style="width:1240px;">
			<table width="1240px" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="13" align="center" style="border:none;font-size:14px; font-weight:bold"><? echo $report_title; ?> (<? echo $production_process[$prod_type]; ?>)</td>
				</tr>
				<tr style="border:none;">
					<td colspan="13" align="center" style="border:none; font-size:16px; font-weight:bold">
					Company Name: <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="13" align="center" style="border:none;font-size:12px; font-weight:bold">
					<? echo "From $fromDate To $toDate" ;?>
					</td>
				</tr>
			</table>
			<table width="1220" border="1" class="rpt_table" rules="all" align="left">
				<thead>
					<tr>
						<th rowspan="2" width="40" >SL</th>
						<th colspan="7">Order Details</th>
						<? if($prod_type==1001){?><th colspan="3">Embellishment Status</th><? } ?>
						<? if($prod_type==5){?><th colspan="3">Sewing Status</th><? } ?>
					</tr>
					<tr>
						<th width="110">Buyer Name</th>
						<th width="60">Job No</th>
						<th width="120">Style</th>
						<th width="100">Order Number</th>
						<th width="100">Gmts Item Name</th>
						<th width="70">Ship Date</th>
						<th width="100">Order Qty.</th>
						<? if($prod_type==1001){?>
						<th width="100">Total Sent</th>
						<th width="100">Total Receive</th>
						<th width="100">Yet To Receive</th>
						<? } ?>
						<? if($prod_type==5){?>
						<th width="100">Total Sew. In. Qty.</th>
						<th width="100">Total Sew Prod. Qty.</th>
						<th width="100">Yet To Sew Output</th>
						<? } ?>
					</tr>
				</thead>
			</table>    
			<div style="width:1240px; max-height:300px; overflow-y:auto;" id="scroll_body">
				<table width="1220" border="1" class="rpt_table" rules="all" id="table_body" align="left">
					<tbody>
						<?
				
						$sql = "SELECT a.buyer_name as BUYER_NAME, a.job_no as JOB_NO, a.style_ref_no as STYLE_REF_NO, a.gmts_item_id as GMTS_ITEM_ID, a.job_no_prefix_num as JOB_NO_PREFIX_NUM, b.id as PO_ID, b.po_number as PO_NUMBER, b.shipment_date as SHIPMENT_DATE, b.po_quantity as PO_QUANTITY, 
						sum(case when c.production_type=2 then c.production_quantity else 0 end) as EMBL_SENT_QTY,
						sum(case when c.production_type=3 then c.production_quantity else 0 end) as EMBL_REC_QTY,
						sum(case when c.production_type=4 then c.production_quantity else 0 end) as SEW_IN_QTY,
						sum(case when c.production_type=5 then c.production_quantity else 0 end) as SEW_OUT_QTY
						from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c
						where b.job_no_mst=a.job_no and a.company_name=$cbo_company_name and b.id=c.po_break_down_id and c.production_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $search_cond 
						group by a.buyer_name, a.job_no, a.style_ref_no, a.gmts_item_id, a.job_no_prefix_num, b.id, b.po_number, b.shipment_date, b.po_quantity $having_cond ";
						// echo $sql;
						$data_arr=sql_select($sql);
						if(empty($data_arr))
						{
							echo "<h2>"."Production Not Found"."</h2>";die;
						}
						$i=1;
						foreach($data_arr as $row)
						{
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							$tot_po_qty+=$row[csf('po_quantity')];
							$yet_to_rec=0;
							$gmts_name="";
							$item_id=array_unique(explode(",",$row["GMTS_ITEM_ID"]));
							foreach($item_id as $val)
							{
								if($gmts_name==""){ $gmts_name=$garments_item[$val]; } else { $gmts_name.=', '.$garments_item[$val]; }
							}
							?>
							<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
								<td width="40" align="center"><? echo $i;?></td>
								<td width="110"><? echo $buyerArr[$row['BUYER_NAME']];?></td>
								<td width="60"><? echo $row['JOB_NO_PREFIX_NUM'];?></td>
								<td width="120"><div style="word-wrap:break-word; width:120px"><? echo $row['STYLE_REF_NO']; ?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row['PO_NUMBER']; ?></div></td>
								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $gmts_name; ?></div></td>
								<td width="70"><? echo change_date_format($row['SHIPMENT_DATE']);?></td>
								<td width="100" align="right"><? echo number_format($row['PO_QUANTITY']); ?></td>
								<? ?>
								<td width="100" align="right">
									<? 
										if($prod_type==1001){
											$sent_in_qty=$row['EMBL_SENT_QTY'];
											$title="Embellishment Issue Details";
											$width_popup="850";
											$production_type="2";
										}
										elseif($prod_type==5)
										{
											$sent_in_qty=$row['SEW_IN_QTY'];
											$title="Sewing Issue Details";
											$width_popup="500";
											$production_type="4";
										}
										$tot_sent_in_qty+=$sent_in_qty;
									?>
									<a href='##' onclick="fnc_po_details('<?=$row['PO_ID'];?>','<?=$prod_type; ?>','<?=$production_type;?>',<?=$width_popup;?>,<? echo $txt_date_from;?>,<? echo $txt_date_to;?>,'<?=$title;?>','po_popup_details')"><? echo number_format($sent_in_qty,2); ?></a>
								</td>
								<td width="100" align="right">
									<? 
										if($prod_type==1001){
											$rcv_out_qty=$row['EMBL_REC_QTY'];
											$title="Embellishment Receive Details";
											$width_popup="850";
											$production_type="3";
										}
										elseif($prod_type==5)
										{
											$rcv_out_qty=$row['SEW_OUT_QTY'];
											$title="Sewing Receive Details";
											$width_popup="500";
											$production_type="5";
										}
										$tot_rcv_out_qty+=$rcv_out_qty;
									?>
									<a href='##' onclick="fnc_po_details('<?=$row['PO_ID'];?>','<?=$prod_type; ?>','<?=$production_type;?>',<?=$width_popup;?>,<? echo $txt_date_from;?>,<? echo $txt_date_to;?>,'<?=$title;?>','po_popup_details')"><? echo number_format($rcv_out_qty,2); ?></a>
								</td>
								<td width="100" align="right">
									<? 
										$yet_to_rec=$sent_in_qty-$rcv_out_qty; 
										echo number_format($yet_to_rec); 
										$tot_sent_out_qty+=$yet_to_rec;
									?>
								</td>
							</tr>
							<?
							$i++; 
						} 
						?> 
					</tbody>
					<tfoot>
						<th colspan="8" align="right"><strong>Total :</strong></th>
						<th width="100" align="right"><?php echo number_format($tot_sent_in_qty); ?></th>
						<th width="100" align="right"><?php echo number_format($tot_rcv_out_qty); ?></th>
						<th width="100" align="right"><?php echo number_format($tot_sent_out_qty); ?></th>
					</tfoot>
				</table>
				</div>
			</div>
			
			<?php 
		}
	}
	if($rpt_type==3)
	{
		if($cbo_buyer_name==0) $buyer_name_cond=""; else $buyer_name_cond=" and a.buyer_name=$cbo_buyer_name";
		if($hidden_order_id=0) $order_id_cond=""; else $order_id_cond=" and b.id in($hidden_order_id)";
		if($style_no=="") $style_cond=""; else $style_cond=" and a.style_ref_no like '%$style_no%'";
		if($order_num=="") $order_cond=""; else $order_cond=" and b.po_number ='".$order_num."'";
		// echo $order_cond;
		if($internal_ref=="") $internal_cond=""; else $internal_cond=" and b.grouping like '%$internal_ref%'";
		if($hidden_factory_id==0) $factory_id_cond=""; else $factory_id_cond=" and c.serving_company in($hidden_factory_id)";
	
		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")
		{ 	
			$txt_date_cond="";
		}
		else
		{
			$txt_date_cond=" and c.production_date between $txt_date_from and $txt_date_to";
			if(str_replace("'","",trim($cbo_info_type))==3)
			{
				if($db_type==0)
				{
					$txt_date_cond=" and DATE(c.production_date) between DATE($txt_date_from) and DATE($txt_date_to)";
				}
				else
				{
					$txt_date_cond=" and TRUNC(c.production_date) between TO_DATE($txt_date_from,'dd/mon/yyyy') and TO_DATE($txt_date_to,'dd/mon/yyyy')";
				}
			} 
		}
	
		if($prod_type==5){
			$prod_con_in=" and c.production_type=4";
			$prod_con_out=" and c.production_type=5";
			$selectField=",c.production_type";
			$cPTypeArr=$production_process;
		}
		
		if($db_type==0) $year_cond="YEAR(a.insert_date)";
		else if($db_type==2) $year_cond="to_char(a.insert_date,'YYYY')";
		
		$sql = "SELECT a.buyer_name, a.job_no, a.style_ref_no,  a.job_no_prefix_num,b.id, $year_cond as year $selectField ,b.id, b.po_number, b.grouping, b.po_quantity,c.production_date, c.serving_company,d.production_qnty as sew_out_qty,d.reject_qty from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d where b.job_id=a.id and c.id=d.mst_id and a.company_name=$cbo_company_name and b.id=c.po_break_down_id and c.production_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $prod_con_out $buyer_name_cond $style_cond $internal_cond $factory_id_cond $txt_date_cond $order_cond"; 
		// echo $sql; die;
		$data_arr=sql_select($sql);
		foreach($data_arr as $row)
		{
			$key=$row[csf('serving_company')].$row[csf('buyer_name')].$row[csf('job_no_prefix_num')].$row[csf('style_ref_no')].$row[csf('production_type')];
			$dataArr[$key]=array(
				serving_company=>$row[csf('serving_company')],
				buyer_name=>$row[csf('buyer_name')],
				job_no_prefix_num=>$row[csf('job_no_prefix_num')],
				style_ref_no=>$row[csf('style_ref_no')],
				production_type=>$row[csf('production_type')],
			);
			//$sewing_in_qty[$key]+=$row[csf('sew_in_qty')];
			$sewing_out_qty[$key]+=$row[csf('sew_out_qty')];
			$reject_qty[$key]+=$row[csf('reject_qty')];
			$po_no[$key][$row[csf('po_number')]]=$row[csf('po_number')];
			$grouping[$key][$row[csf('grouping')]]=$row[csf('grouping')];
			$po_qty[$key][$row[csf('id')]]=$row[csf('po_quantity')];
			$po_id[$key][$row[csf('id')]]=$row[csf('id')];
			$po_serving_com_id[$key][$row[csf('id')]]=$row[csf('id')].$row[csf('serving_company')];
			$po_id_arr[$row[csf('id')]]=$row[csf('id')];
		}
	
		$po_id_string=implode(",",$po_id_arr);
		// echo $po_id_string."__".$cbo_info_type;
	
		if($po_id_string=="" && str_replace("'","",trim($cbo_info_type))!=3 && str_replace("'","",trim($cbo_info_type))!=2)
		{
			echo "<h2>"."Production Not Found"."</h2>";die;
		}
		if($po_id_string==""){$po_breakdonw_in_po_id_cond="";} else{ $po_breakdonw_in_po_id_cond="c.po_break_down_id in($po_id_string) and";}				
		$sql = "SELECT c.po_break_down_id,d.production_qnty as sew_in_qty, c.serving_company from pro_garments_production_mst c, pro_garments_production_dtls d where c.id=d.mst_id and $po_breakdonw_in_po_id_cond c.company_id=$cbo_company_name and c.production_source=3 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $prod_con_in ";
		  //echo $sql;
		$data_arr=sql_select($sql);
		foreach($data_arr as $row)
		{
			$key_id=$row[csf('po_break_down_id')].$row[csf('serving_company')];
			$sewing_in_qty[$key_id]+=$row[csf('sew_in_qty')];
		}	
		ob_start();
		if(str_replace("'","",trim($cbo_info_type))==3)
		{
			if($prod_type==5)
			{
				$production_type_cond=" and c.production_type in (4,5)";
				$production_data_cond="sum(case when c.production_type=4 then d.production_qnty else 0 end) as production_in_qty, sum(case when c.production_type=5 then d.production_qnty else 0 end) as production_out_qty";
			}
			?>
	
			<div>
			<table width="1060" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="12" align="center" style="border:none;font-size:14px; font-weight:bold"><? echo $report_title; ?> (<? echo $production_process[$prod_type]; ?>)</td>
				</tr>
				<tr style="border:none;">
					<td colspan="12" align="center" style="border:none; font-size:16px; font-weight:bold">
					Company Name: <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="12" align="center" style="border:none;font-size:12px; font-weight:bold">
					<? echo "From $fromDate To $toDate" ;?>
					</td>
				</tr>
			</table>
			<div style="width:1518px;">
			<table width="1500" border="1" class="rpt_table" rules="all" align="left">
				<thead>
					<tr>
						<th width="35" >SL</th>
						<th width="70">Transaction Date</th>
						<th width="60">Name of Month</th>
						<th width="100">Delivery/Receive Challan No</th>
						<th width="100">Delivery/Receive Gate Pass No</th>
						<th width="110">Name of Sub Con. Factory</th>
						<th width="100">Buyer Name</th>
						<th width="90">Job No</th>
						<th width="80">Style Name</th>
						<th width="70">Name of Item</th>
						<th width="70">Color</th>
						<th width="70">Location</th>
						<th width="70">Order Number</th>
						<th width="70">Name of Country</th>
						<th width="70">Order Qty.</th>
						<th width="70">Delivery Qty.</th>
						<th width="70">Receive Qty.</th>
						<th width="70">Sys. Chln.</th>
						<th>Remarks</th>
					</tr>
				</thead>
			</table> 
			</div>   
			<div style="width:1518px; max-height:280px; overflow-y:auto;" id="scroll_body">
				<table width="1500" border="1" class="rpt_table" rules="all" id="table_body" align="left">
					<tbody>
					<?
					$i=1;
					$production_in_qty_arr=array(); 
					$production_out_qty_arr=array(); 
					$order_qty_arr=array();
					$monthArr = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
					$gate_pass_arr=return_library_array( "select challan_no, sys_number from inv_gate_pass_mst where basis=65 and company_id=$cbo_company_name",'challan_no','sys_number');
					$buyer_name_arr = return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
					$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
					$location_arr = return_library_array("select id, location_name from lib_location",'id','location_name');
					$gmts_item_arr = return_library_array("select id, item_name from lib_garment_item",'id','item_name');
					$company_arr = return_library_array("select id, company_name from lib_company",'id','company_name');
					$supplier_arr = return_library_array("select id, supplier_name from lib_supplier",'id','supplier_name');
					$color_arr = return_library_array("select id, color_name from lib_color",'id','color_name');
					
					$sql = "SELECT a.buyer_name, a.job_no, a.style_ref_no, a.job_no_prefix_num, a.gmts_item_id, b.id , b.po_number, b.po_quantity, c.id as sys_challan_no, c.production_type, c.serving_company, c.po_break_down_id, c.production_date, c.challan_no, c.location, c.country_id, c.remarks, c.production_source, e.color_number_id, $production_data_cond
					from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d, wo_po_color_size_breakdown e
					where a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and d.color_size_break_down_id=e.id and a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.production_source=3 and c.is_deleted=0 $buyer_name_cond $style_cond $factory_id_cond $production_type_cond $txt_date_cond $order_cond 
					group by a.buyer_name, a.job_no, a.style_ref_no, a.job_no_prefix_num, a.gmts_item_id, b.id , b.po_number, b.po_quantity, c.id, c.serving_company, c.po_break_down_id, c.production_type, c.production_date, c.challan_no, c.location, c.country_id, c.remarks, c.production_source, e.color_number_id" ;
					// echo $sql; die;
					$dataArr=sql_select($sql);
					if(empty($dataArr))
						{
							echo "<h2>"."Production Not Found"."</h2>";die;
						}
					foreach($dataArr as $row)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						$production_in_qty_arr[]=$row[csf('production_in_qty')];
						$production_out_qty_arr[]=$row[csf('production_out_qty')];
						$order_qty_arr[]=$row[csf('po_quantity')];
						if($row[csf('production_source')] == 1){
							$factory_name = $company_arr[$row[csf('serving_company')]];
						}else if($row[csf('production_source')] == 3){
							$factory_name = $supplier_arr[$row[csf('serving_company')]];
						}
						?>
						<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
							<td width="35" align="center"><? echo $i;?></td>
							<td width="70"><p><? echo change_date_format($row[csf('production_date')]); ?></p></td>
							<td width="60"><? echo $monthArr[date('n',strtotime($row[csf('production_date')]))];?></td>
							<td width="100"> <? echo $row[csf('challan_no')]; ?></td>
							<td width="100"><p><? echo $gate_pass_arr[$row[csf('sys_challan_no')]]; ?></p></td>
							<td width="110"><p><? echo $factory_name; ?></p></td>
							<td width="100"><p><? echo $buyer_name_arr[$row[csf('buyer_name')]]; ?></p></td>
							<td width="90"><p><? echo $row[csf('job_no')]; ?></p></td>
							<td width="80"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
							<td width="70"><p><? echo $gmts_item_arr[$row[csf('gmts_item_id')]]; ?></p></td>
							<td width="70"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
							<td width="70"><p><? echo $location_arr[$row[csf('location')]]; ?></p></td>
							<td width="70"><p><? echo $row[csf('po_number')]; ?></p></td>
							<td width="70"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
							<td width="70" align="right"><? echo $row[csf('po_quantity')]; ?></td>
							<td width="70" align="right"><? echo number_format($row[csf('production_in_qty')]); ?></td>
							<td width="70" align="right"><? echo number_format($row[csf('production_out_qty')]); ?></td>
							<td width="70"><p><? echo $row[csf('sys_challan_no')]; ?></p></td>
							<td ><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
					   <?
					   $i++; 
				    } 
				   ?> 
				</tbody>
			</table>
			</div>
			<div style="width:1518px;">
			<table width="1500" border="1" class="rpt_table" rules="all" id="table_body" align="left">
				<tfoot>
					<th colspan="14" align="right"><strong>Total :</strong></th>
					<th width="70" align="right"><?php echo array_sum($order_qty_arr); ?></th>
					<th width="70" align="right"><?php echo array_sum($production_in_qty_arr); ?></th>
					<th width="70" align="right"><?php echo array_sum($production_out_qty_arr); ?></th>
					<th width="175" align="right"><?php //echo $tot_sew_bal_qty; ?></th>
				</tfoot>
			</table>
			</div>
			
			</div>
		   <?php 
		}
	}
	
	
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) { @unlink($filename); }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename"; 
    exit();
}

if($action=="po_popup_details")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");

	if($txt_date_from!='' && $txt_date_to!=''){$date_cond=" and mst.production_date between $txt_date_from and $txt_date_to";}
	if($prod_type==5)
	{
		$sql="SELECT a.production_date as PRODUCTION_DATE, a.production_source as PRODUCTION_SOURCE, a.serving_company as SERVING_COMPANY, sum(b.production_qnty) as PRODUCTION_QNTY, c.color_number_id as COLOR_NUMBER_ID 
		from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
		where a.id=b.mst_id and c.po_break_down_id=$order_id and b.production_type=$production_type and b.color_size_break_down_id=c.id and a.production_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond
		group by a.production_date,a.production_source,a.serving_company,c.color_number_id
		order by a.production_date ";
		// echo $sql;
		$result_data=sql_select($sql);		
		?>
		
		<table cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table" >
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="80">Sewing Date</th>
				<th width="80">Color</th>
				<th width="80">Sewing Qty</th>
				<th >Sewing Company</th>
			</tr>
		</thead>
		<tbody>
			<?
				$i=1;
				$tot_qnty=array();
				foreach($result_data as $val)
				{
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
						<td align="center"><? echo $i;  ?></td>
						<td align="center"><? echo change_date_format($val["PRODUCTION_DATE"]); ?></td>
						<td><? echo $colorarr[$val["COLOR_NUMBER_ID"]]; ?></td>
						<td align="right"><? echo $val["PRODUCTION_QNTY"]; ?></td>
						<td>
							<?php
								$source= $val['PRODUCTION_SOURCE'];
								if($source==3){ $serving_company= $supplier_arr[$val['SERVING_COMPANY']]; }
								else{ $serving_company= $company_arr[$val['SERVING_COMPANY']]; }
								echo $serving_company;
							?>
						</td>
					</tr>
					<?
					$production_quantity+=$val["PRODUCTION_QNTY"];
					$i++;
				}
			?>
		</tbody>
		<tfoot>
			<th colspan="3" align="right"><strong>Total :</strong></th>
			<th align="right"><?php echo $production_quantity; ?></th>
			<th></th>
		</tfoot>
		</table>
		<?	
	}

	if($prod_type==1001)
	{
		$sql="SELECT a.production_date as PRODUCTION_DATE, a.embel_name as EMBEL_NAME,a.production_source as PRODUCTION_SOURCE, a.serving_company as SERVING_COMPANY, sum(a.production_quantity) as PRODUCTION_QNTY
		from pro_garments_production_mst a
		where a.po_break_down_id=$order_id and a.production_type=$production_type and a.production_source=3 and a.status_active=1 and a.is_deleted=0 $date_cond
		group by a.production_date, a.embel_name ,a.production_source,a.serving_company
		order by a.production_date ";
		// echo $sql;
		$result_data=sql_select($sql);
		$data_arr=array();
		$embel_data_arr=array();
		foreach ( $result_data as $val )
		{
			$data_arr[$val["PRODUCTION_DATE"]]["production_date"]=$val["PRODUCTION_DATE"];
			$embel_data_arr[$val["PRODUCTION_DATE"]][$val["EMBEL_NAME"]]["production_qnty"]+=$val["PRODUCTION_QNTY"];
			$embel_data_arr[$val["PRODUCTION_DATE"]][$val["EMBEL_NAME"]]["serving_company"].=$supplier_arr[$val["SERVING_COMPANY"]].', ';
		}
		
		?>
		
		<table cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table" >
		<thead>
			<tr>
				<th width="30" rowspan="2">SL</th>
				<th width="60" rowspan="2">Date</th>
				<th colspan="2">Printing Issue</th>
				<th colspan="2">Embroidery Issue</th>
				<th colspan="2">Wash Issue</th>
				<th colspan="2">Special Work Issue</th>
			</tr>
			<tr>
				<th width="60">Outside</th>
				<th width="120">Print Com.</th>
				<th width="60">Outside</th>
				<th width="120">Embl. Com.</th>
				<th width="60">Outside</th>
				<th width="120">Wash Com.</th>
				<th width="60">Outside</th>
				<th >Spec Com.</th>
			</tr>
		</thead>
		<tbody>
			<?
				$i=1;
				$tot_qnty=array();
				foreach($data_arr as $key=>$val)
				{
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
						<td align="center"><? echo $i;  ?></td>
						<td align="center"><? echo change_date_format($val["production_date"]); ?></td>
						<td align="right"><? echo $embel_data_arr[$key][1]["production_qnty"]; ?></td>
						<td><? echo rtrim($embel_data_arr[$key][1]["serving_company"],', '); ?></td>
						<td align="right"><? echo $embel_data_arr[$key][2]["production_qnty"]; ?></td>
						<td><? echo rtrim($embel_data_arr[$key][2]["serving_company"],', ');  ?></td>
						<td align="right"><? echo $embel_data_arr[$key][4]["production_qnty"]; ?></td>
						<td><? echo rtrim($embel_data_arr[$key][4]["serving_company"],', ');  ?></td>
						<td align="right"><? echo $embel_data_arr[$key][3]["production_qnty"]; ?></td>
						<td><? echo rtrim($embel_data_arr[$key][3]["serving_company"],', ');  ?></td>

					</tr>
					<?
					$print_quantity+=$embel_data_arr[$key][1]["production_qnty"];
					$embel_quantity+=$embel_data_arr[$key][2]["production_qnty"];
					$wash_quantity+=$embel_data_arr[$key][4]["production_qnty"];
					$spec_quantity+=$embel_data_arr[$key][3]["production_qnty"];
					$i++;
				}
			?>
		</tbody>
		<tfoot>
			<th colspan="2"><strong>Total :</strong></th>
			<th><?=$print_quantity;?></th>
			<th></th>
			<th><?=$embel_quantity;?></th>
			<th></th>
			<th><?=$wash_quantity;?></th>
			<th></th>
			<th><?=$spec_quantity;?></th>
			<th></th>
		</tfoot>
		</table>
		<?	
	}
		
	exit();	
}

if($action=="sew_input")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");

	if($prod_type==5){
		$prod_con_in=" and a.production_type=4";
		$prod_con_out=" and a.production_type=5";
	}
	else if($prod_type==1001){
		$prod_con_in=" and a.production_type=2";
		$prod_con_out=" and a.production_type=3";
		//$embel_type=" and c.embel_name in(1,2)";
	}
		
		//serving_company
		
		$sql="SELECT 
			mst.production_date,
			mst.challan_no,
			mst.serving_company,
			mst.remarks,
			a.production_qnty, 
			b.color_number_id, 
			b.size_number_id,
			b.size_order 
		from 
			pro_garments_production_mst mst,
			pro_garments_production_dtls a, 
			wo_po_color_size_breakdown b 
		where 
			mst.id=a.mst_id and 
			b.po_break_down_id in($order_id) and 
			a.color_size_break_down_id=b.id and 
			a.status_active=1 and 
			mst.serving_company=$serving_company and 
			a.is_deleted=0 and 
			b.status_active=1 
			$prod_con_in and
			b.is_deleted=0 
		order by b.size_order asc";
		   //echo $sql;
		$result=sql_select($sql);
		foreach ( $result as $row )
		{
			$key=$row[csf('production_date')].$row[csf('challan_no')].$row[csf('color_number_id')];
			$remarks_arr[$key]=$row[csf('remarks')];
			$party_arr[$key]=$row[csf('serving_company')];
			$issue_date_arr[$key]=$row[csf('production_date')];
			$challan_arr[$key]=$row[csf('challan_no')];
			$color_array[$key]=$row[csf('color_number_id')];
			$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$qun_array[$key][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
		}
		
	?>
    
	<table cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table" >
	<thead>
		<tr>
            <th width="30" rowspan="2">SL</th>
            <th width="60" rowspan="2">Issue Date</th>
            <th width="110" rowspan="2">Party Neme</th>
            <th width="60" rowspan="2">Challan NO</th>
            <th width="60" rowspan="2" align="center">Color</th>
            <th colspan="<? echo count($size_array);?>">Size</th>
            <th width="60" rowspan="2" align="right">Isuse Qty</th>
            <th rowspan="2">Remarks</th>
        </tr>
		<tr>
            <?
            foreach ($size_array as $sizid)
            {
                ?>
                    <th width="60"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                <?
            }
            ?>
        </tr>
	</thead>
	<tbody>
		<?
			$i=1;
			$tot_qnty=array();
			foreach($color_array as $cid=>$val)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
                 <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
					<td align="center"><? echo $i;  ?></td>
					<td><? echo change_date_format($issue_date_arr[$cid]); ?></td>
					<td><? echo $supplier_library[$party_arr[$cid]]; ?></td>
					<td><? echo $challan_arr[$cid]; ?></td>
					
                    <td><? echo $colorarr[$val]; ?></td>
					<?
					foreach ($size_array as $sizval)
					{
						$size_count=count($sizval);
						?>
						<td align="right"><? echo $qun_array[$cid][$sizval]; ?></td>
						<?
						$tot_qnty[$cid]+=$qun_array[$cid][$sizval];
						$tot_qnty_size[$sizval]+=$qun_array[$cid][$sizval];
					}
					?>
					<td align="right"><? echo $tot_qnty[$cid]; ?></td>
					<td><? echo $remarks_arr[$cid]; ?></td>
				</tr>
				<?
				$production_quantity+=$tot_qnty[$cid];
				$i++;
			}
		?>
	</tbody>
	<tfoot>
		<th colspan="5" align="right"><strong>Total :</strong></th>
		<?
			foreach ($size_array as $sizval)
			{
			?>
				<th align="right"><?php echo $tot_qnty_size[$sizval]; ?></th>
			<?
			}
		?>
		<th align="right"><?php echo $production_quantity; ?></th>
		<th></th>
	</tfoot>
</table>
<?	
	
exit();	
}

if($action=="sew_output")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$supplier_library = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");

	
	$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
	$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date_cond="";
	else $txt_date_cond=" and mst.production_date between '$txt_date_from' and '$txt_date_to'"; 
	
	if($prod_type==5){
		$prod_con_in=" and a.production_type=4";
		$prod_con_out=" and a.production_type=5";
	}
	else if($prod_type==1001){
		$prod_con_in=" and a.production_type=2";
		$prod_con_out=" and a.production_type=3";
		//$embel_type=" and c.embel_name in(1,2)";
	}
		
		
		$sql="SELECT 
			mst.production_date,
			mst.challan_no,
			mst.serving_company,
			mst.remarks,
			a.production_qnty, 
			b.color_number_id, 
			b.size_number_id,
			b.size_order
		from 
			pro_garments_production_mst mst,
			pro_garments_production_dtls a, 
			wo_po_color_size_breakdown b 
		where 
			mst.id=a.mst_id and 
			b.po_break_down_id in($order_id) and 
			a.color_size_break_down_id=b.id 
			$txt_date_cond and 
			mst.serving_company=$serving_company and 
			a.status_active=1 and 
			a.is_deleted=0 and 
			b.status_active=1 
			$prod_con_out and
			b.is_deleted=0 
		order by b.size_order asc";
		 // echo $sql;
		$result=sql_select($sql);
		foreach ( $result as $row )
		{	$key=$row[csf('production_date')].$row[csf('challan_no')].$row[csf('color_number_id')];
			$remarks_arr[$key]=$row[csf('remarks')];
			$party_arr[$key]=$row[csf('serving_company')];
			$issue_date_arr[$key]=$row[csf('production_date')];
			$challan_arr[$key]=$row[csf('challan_no')];
			$color_array[$key]=$row[csf('color_number_id')];
			$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$qun_array[$key][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
		}
		
	?>
    
	<table cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table" >
	<thead>
		<tr>
            <th width="30" rowspan="2">SL</th>
            <th width="60" rowspan="2">Receive Date</th>
            <th width="110" rowspan="2">Party Neme</th>
            <th width="60" rowspan="2">Challan NO</th>
            <th width="60" rowspan="2" align="center">Color</th>
            <th colspan="<? echo count($size_array);?>">Size</th>
            <th width="60" rowspan="2" align="right">Receive Qty</th>
            <th rowspan="2">Remarks</th>
        </tr>
		<tr>
            <?
            foreach ($size_array as $sizid)
            {
                ?>
                    <th width="60"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                <?
            }
            ?>
        </tr>
	</thead>
	<tbody>
		<?
			$i=1;
			$tot_qnty=array();
			foreach($color_array as $cid=>$val)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
                 <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
					<td align="center"><? echo $i;  ?></td>
					<td><? echo change_date_format($issue_date_arr[$cid]); ?></td>
					<td><? echo $supplier_library[$party_arr[$cid]]; ?></td>
					<td><? echo $challan_arr[$cid]; ?></td>
					
                    <td><? echo $colorarr[$val]; ?></td>
					<?
					foreach ($size_array as $sizval)
					{
						$size_count=count($sizval);
						?>
						<td align="right"><? echo $qun_array[$cid][$sizval]; ?></td>
						<?
						$tot_qnty[$cid]+=$qun_array[$cid][$sizval];
						$tot_qnty_size[$sizval]+=$qun_array[$cid][$sizval];
					}
					?>
					<td align="right"><? echo $tot_qnty[$cid]; ?></td>
					<td><? echo $remarks_arr[$cid]; ?></td>
				</tr>
				<?
				$production_quantity+=$tot_qnty[$cid];
				$i++;
			}
		?>
	</tbody>
	<tfoot>
		<th colspan="5" align="right"><strong>Total :</strong></th>
		<?
			foreach ($size_array as $sizval)
			{
			?>
				<th align="right"><?php echo $tot_qnty_size[$sizval]; ?></th>
			<?
			}
		?>
		<th align="right"><?php echo $production_quantity; ?></th>
		<th></th>
	</tfoot>
</table>
<?	
	
exit();	
}


if($action=="sew_blance")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$supplier_library = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");

	
	$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
	$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date_cond="";
	else $txt_date_cond=" and mst.production_date between '$txt_date_from' and '$txt_date_to'"; 
	
	if($prod_type==5){
		$prod_con_in=" a.production_type=4";
		$prod_con_out=" a.production_type=5";
		$whereIn = "4,5";
	}
	else if($prod_type==1001){
		$prod_con_in=" a.production_type=2";
		$prod_con_out=" a.production_type=3";
		$whereIn = "2,3";
	}
		
		$sql="SELECT 
			mst.production_date,
			mst.challan_no,
			mst.serving_company,
			mst.remarks,
			case when $prod_con_in $txt_date_cond then a.production_qnty else 0 end as sew_in_qty,
			case when $prod_con_out then a.production_qnty else 0 end as sew_out_qty,
			b.color_number_id, 
			b.size_number_id ,
			b.size_order,
			a.reject_qty
		from 
			pro_garments_production_mst mst,
			pro_garments_production_dtls a, 
			wo_po_color_size_breakdown b 
		where 
			mst.id=a.mst_id and 
			b.po_break_down_id in($order_id) and 
			a.color_size_break_down_id=b.id and
			mst.serving_company=$serving_company and 
			a.status_active=1 and 
			a.is_deleted=0 and 
			b.status_active=1 and 
			a.production_type in($whereIn) and
			b.is_deleted=0 
		order by b.size_order asc";
		//   echo $sql;
		$result=sql_select($sql);
		foreach ( $result as $row )
		{
			$remarks_arr[$row[csf('color_number_id')]]=$row[csf('remarks')];
			$party_arr[$row[csf('color_number_id')]]=$row[csf('serving_company')];
			$issue_date_arr[$row[csf('color_number_id')]]=$row[csf('production_date')];
			$challan_arr[$row[csf('color_number_id')]]=$row[csf('challan_no')];
			$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$in_qty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('sew_in_qty')];
			$out_qty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('sew_out_qty')];
			$reject_qty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('reject_qty')];
			$bal_qty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=($row[csf('sew_in_qty')]-$row[csf('sew_out_qty')]-$row[csf('reject_qty')]);
		}
		
	?>
    
	<table cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table" >
	<thead>
		<tr>
            <th width="30" rowspan="2">SL</th>
            <th width="110" rowspan="2">Party Neme</th>
            <th width="60" rowspan="2" align="center">Color</th>
            <th colspan="<? echo count($size_array);?>">Size</th>
            <th width="60" rowspan="2" align="right">Blance Qty</th>
            <th rowspan="2">Remarks</th>
        </tr>
		<tr>
            <?
            foreach ($size_array as $sizid)
            {
                ?>
                    <th width="60"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                <?
            }
            ?>
        </tr>
	</thead>
	<tbody>
		<?
			$i=1;
			$tot_qnty=array();
			foreach($color_array as $cid)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
                 <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
					<td align="center"><? echo $i;  ?></td>
					<td><? echo $supplier_library[$party_arr[$cid]]; ?></td>
					
                    <td><? echo $colorarr[$cid]; ?></td>
					<?
					foreach ($size_array as $sizval)
					{
						$size_count=count($sizval);
						?>
						<td align="right"><? echo $bal_qty[$cid][$sizval]; ?></td>
						<?
						$tot_qnty[$cid]+=$bal_qty[$cid][$sizval];
						$tot_qnty_size[$sizval]+=$bal_qty[$cid][$sizval];
					}
					?>
					<td align="right"><? echo $tot_qnty[$cid]; ?></td>
					<td><? echo $remarks_arr[$cid]; ?></td>
				</tr>
				<?
				$production_quantity+=$tot_qnty[$cid];
				$i++;
			}
		?>
	</tbody>
	<tfoot>
		<th colspan="3" align="right"><strong>Total :</strong></th>
		<?
			foreach ($size_array as $sizval)
			{
			?>
				<th align="right"><?php echo $tot_qnty_size[$sizval]; ?></th>
			<?
			}
		?>
		<th align="right"><?php echo $production_quantity; ?></th>
		<th></th>
	</tfoot>
</table>
<?	
	
exit();	
}




//order search------------------------------//
if($action=="order_search")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			//alert(strCon);
			var splitSTR = strCon.split("_");
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			//alert(str_or);
			
			toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str_or );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			num 	= num.substr( 0, num.length - 1 );
			//alert(num);
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
			$('#txt_selected_no').val( num );
		}
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	//$job_year=str_replace("'","",$job_year);
	//$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	
	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==0)
	{
		if($job_year!=0) $job_year_cond=" and year(b.insert_date)=$job_year"; else $job_year_cond="";
		$select_date=" year(b.insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(b.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(b.insert_date,'YYYY')";
	}
	//if($txt_style_ref_id!="") $style_cond=" and b.id in($txt_style_ref_id)"; else $style_cond="";
	$sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $buyer_cond and a.status_active=1 and b.status_active=1"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Order NO,Job No,Year,Style Ref No","150,80,70,150","500","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	//echo "<input type='hidden' id='txt_selected_no' />";
	
	?>
    <!--<script language="javascript" type="text/javascript">
	var order_no='<? //echo $order_no;?>';
	var order_id='<? //echo $order_id;?>';
	//var style_des='<? //echo $txt_order;?>';
	//alert(order_no);
	if(order_no!="")
	{
		order_no_arr=order_no.split(",");
		order_id_arr=order_id.split(",");
		//style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<order_no_arr.length; k++)
		{
			//str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			str_ref=order_no_arr[k]+'_'+order_id_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>-->
    
    <?
	exit();
}

if($action="factory_search")
{
	//echo "su..re";
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			//alert(strCon);
			var splitSTR = strCon.split("_");
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str_or );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			num 	= num.substr( 0, num.length - 1 );
			//alert(num);
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
			$('#txt_selected_no').val( num );
		}
    </script>
    <?
	
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	//$job_year=str_replace("'","",$job_year);
	//$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	
	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==0)
	{
		if($job_year!=0) $job_year_cond=" and year(b.insert_date)=$job_year"; else $job_year_cond="";
		$select_date=" year(b.insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(b.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(b.insert_date,'YYYY')";
	}
	
	$sql = "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(22,23) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name";
	
	//$sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $buyer_cond and a.status_active=1 and b.status_active=1"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Working Factory Name","300","350","310",0, $sql , "js_set_value", "id,supplier_name", "", 1, "0", $arr, "supplier_name", "","setFilterGrid('list_view',-1)","0","",1);	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	//echo "<input type='hidden' id='txt_selected_no' />";
	
	?>
    <!--<script language="javascript" type="text/javascript">
	var style_no='<? //echo $txt_order_id_no;?>';
	var style_id='<? //echo $txt_order_id;?>';
	var style_des='<? //echo $txt_order;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>-->
    
    <?
	exit();
}
