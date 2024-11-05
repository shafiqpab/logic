<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_name", 100, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b  where a.id=b.supplier_id and b.tag_company='$data' and a.status_active=1 and a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", 0, "" );
    exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_id 	= str_replace("'", "", $cbo_company_name);
	$buyer_id 		= str_replace("'", "", $cbo_buyer_name);
	$supplier_id 	= str_replace("'", "", $cbo_supplier_name);
	$txt_style_no 	= str_replace("'", "", $txt_style_no);
	$txt_job_no 	= str_replace("'", "", $txt_job_no);
	$txt_order_no 	= str_replace("'", "", $txt_order_no);
	$txt_int_ref 	= str_replace("'", "", $txt_int_ref);
	$embel_type 	= str_replace("'", "", $cbo_embel_type);
	$cbo_year 	= str_replace("'", "", $cbo_year);
	 
	$lib_buyer=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$lib_supplier=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$lib_company=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$lib_location=return_library_array( "select id, location_name from  lib_location",'id','location_name');
	$lib_floor=return_library_array( "select id, floor_name from  lib_prod_floor",'id','floor_name');
	$lib_color=return_library_array( "select id, color_name from  lib_color",'id','color_name');
	$lib_size=return_library_array( "select id, size_name from  lib_size",'id','size_name');
	
	/* =================================================================================/
    / 										SQL Condition								/
    /================================================================================= */

	$sql_cond = "";
	$sql_cond .= ($company_id==0) ? "": " and d.company_name=$company_id";
	$sql_cond .= ($buyer_id==0) ? "": " and d.buyer_name=$buyer_id";
	$sql_cond .= ($supplier_id==0) ? "": " and a.serving_company=$supplier_id";
	$sql_cond .= ($txt_style_no=="") ? "": " and d.style_ref_no like '%$txt_style_no%'";
	$sql_cond .= ($txt_job_no=="") ? "": " and d.job_no_prefix_num=$txt_job_no";
	$sql_cond .= ($txt_order_no=="") ? "": " and e.po_number like '%$txt_order_no%'";
	$sql_cond .= ($txt_int_ref=="") ? "": " and e.grouping like '%$txt_int_ref%'";
	$sql_cond .= ($embel_type==0) ? "": " and a.embel_name=$embel_type";	

	$year_field_con=" and to_char(d.insert_date,'YYYY')";
    if($cbo_year!=0) $year_cond="$year_field_con=$cbo_year"; ($cbo_year="");  
	/* =================================================================================/
    / 										Main Query									/
    /================================================================================= */

	$sql="SELECT f.id as sys_id,f.sys_number,a.po_break_down_id as PO_ID,a.production_date,a.embel_name,b.production_qnty  as QTY,b.reject_qty, d.JOB_NO, d.style_ref_no as STYLE, e.PO_NUMBER,c.color_number_id as COLOR_ID,c.size_number_id as size_id,c.order_quantity,c.plan_cut_qnty,c.id as color_size_id,f.issue_challan_id,a.production_type,a.serving_company,f.MANUAL_CHALLAN_NO from pro_garments_production_mst a, pro_garments_production_dtls b,pro_gmts_delivery_mst f, wo_po_color_size_breakdown c, wo_po_details_master d, wo_po_break_down e where a.id=b.mst_id and a.production_type in(2,3) and b.production_type in(2,3) and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and d.id=e.job_id and d.id=c.job_id and e.id=a.po_break_down_id and e.id=c.po_break_down_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.id=a.delivery_mst_id and f.status_active=1 and f.is_deleted=0 $sql_cond $year_cond order by c.size_order";
	 //echo $sql;die();
	$sql_res = sql_select($sql);
	if(count($sql_res)==0)
	{
		echo '<div style="text-align:center;color:red;font-weight:bold;font-size:18px;">Data not found.</div>';die();
	}
	$issueArray = array();
	$receiveArray = array();
	$orderQtyArray = array();
	$color_size_id_chk_array = array();
	$issue_challan_array = array();
	$size_array = array();
	foreach ($sql_res as $val) 
	{
		if($val['PRODUCTION_TYPE']==2)
		{
			$issueArray[$val['COLOR_ID']][$val['PRODUCTION_DATE']][$val['SYS_ID']][$val['SIZE_ID']]['size_qty'] += $val['QTY'];
		}
		else
		{
			$receiveArray[$val['COLOR_ID']][$val['ISSUE_CHALLAN_ID']][$val['PRODUCTION_DATE']][$val['SYS_ID']][$val['SIZE_ID']]['size_qty'] += $val['QTY'];
			$receiveArray[$val['COLOR_ID']][$val['ISSUE_CHALLAN_ID']][$val['PRODUCTION_DATE']][$val['SYS_ID']]['reject_qty'] += $val['REJECT_QTY'];
			//working here 
			$receiveArray[$val['COLOR_ID']][$val['ISSUE_CHALLAN_ID']]['receive_total'] += $val['QTY'];
			$receiveArray[$val['COLOR_ID']][$val['ISSUE_CHALLAN_ID']]['reject_total'] += $val['REJECT_QTY'];
			
		}

		
		if($color_size_id_chk_array[$val['COLOR_SIZE_ID']]=="")
		{
			$orderQtyArray[$val['COLOR_ID']][$val['SIZE_ID']]['order_qty'] += $val['ORDER_QUANTITY'];
			$orderQtyArray[$val['COLOR_ID']][$val['SIZE_ID']]['plancut_qty'] += $val['PLAN_CUT_QNTY'];
			$color_size_id_chk_array[$val['COLOR_SIZE_ID']] = $val['COLOR_SIZE_ID'];
		}
		$size_array[$val['SIZE_ID']] = $val['SIZE_ID'];
		$challan_array[$val['SYS_ID']] = $val['SYS_NUMBER'];
		$supplier_array[$val['SYS_ID']] = $val['SERVING_COMPANY'];
		$manual_challan_array[$val['SYS_ID']] = $val['MANUAL_CHALLAN_NO'];
		$job_no = $val['JOB_NO'];
		$style = $val['STYLE'];
		$po_number .= $val['PO_NUMBER']."**";
	}
	
	foreach($receiveArray as $color => $colorWise)
	{
		foreach($colorWise as $challan => $challanWise)
		{	
			foreach($challanWise as $productionDate)
			{
				foreach($productionDate as $systemId)
				{
					$receiveArray[$color][$challan]['row_span'] += 1;
				}
			}
			
		}
		
	}
	unset($sql_res);
	//echo "<pre>"; print_r($receiveArray);die();
	
	$tbl_width = 160+850+240+(count($size_array)*2*50);

	ob_start();
	?>	
 	<fieldset style="width:<?=$tbl_width+20;?>px;"> 	
 		<style type="text/css">
 			h2{font-size: 20px;font-weight: bold;}

 			#resp-table {
			width: 100%;
			display: table;
			}
 			#resp-table-body{
			display: table-row-group;
			}
 			.resp-table-row{
				display: table-row;
				}
 			.table-body-cell{
				display: table-cell;
				border: 1px solid #8DAFDA;
				text-align: right;
				}
 		</style>
 		<div>
 			<center>
	 			<h2><? echo ucfirst($lib_company[$company_id]);?></h2>
	 			<h2>Embellishment Report</h2>
	 		</center>

 			<table>
 				<tr>
 					<td width="10%"><b>Job No:</b></td>
 					<td width="20%"><?=$job_no;?></td>
 					<td width="10%"><b>Style No:</b></td>
 					<td width="20%"><?=$style;?></td>
 					<td width="10%"><b>PO Number:</b></td>
 					<td width="20%"><?=implode(", ", array_unique(array_filter(explode("**", $po_number))));?></td>
 				</tr>
 			</table>
 		</div>	
 		<!-- ========================== table heading ========================== -->
        <table width="<?=$tbl_width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left"> 
            <thead>
            	<tr>
            		<th colspan="<?=count($size_array)+6;?>">Sent Part</th>
            		<th colspan="<?=count($size_array)+8;?>">Receive Part</th>
            	</tr>
               <tr>
	                <th rowspan="2" width="30">SL</th>
	                <th rowspan="2" width="80">Issue Date</th>
	                <th rowspan="2" width="120">Supplier</th>
	                <th rowspan="2" width="120">Challan No</th>
	                <th rowspan="2" width="100">Color</th>
	                <th colspan="<?=count($size_array);?>" width="<?=count($size_array)*50;?>">Size</th>
	                <th rowspan="2" width="80">Total Sent</th>

	                
	                <th rowspan="2" width="80">Receive Date</th>
	                <th rowspan="2" width="120">Challan No</th>
	                <th rowspan="2" width="120">Manual Challan</th>
	                <th colspan="<?=count($size_array);?>" width="<?=count($size_array)*50;?>">Size</th>
	                <th rowspan="2" width="80">Total Rcv</th>
	                <th rowspan="2" width="80">Receive Total</th>
	                <th rowspan="2" width="80">Reject Qty</th>
	                <th rowspan="2" width="80">Reject Total</th>
	                <th rowspan="2" width="80">Total Short</th>


				</tr>
				<tr>
					<?
					foreach ($size_array as $skey => $sval) 
					{
						?>
						<th width="50"><p><?=$lib_size[$skey];?></p></th>
						<?
					}
					// ===========================
					foreach ($size_array as $skey => $sval) 
					{
						?>
						<th width="50"><p><?=$lib_size[$skey];?></p></th>
						<?
					}
					?>
				</tr>
            </thead>  
        </table> 
        
        <!-- ========================== table body ========================== -->         
        <div id="scroll_body" style="width:<?=$tbl_width+20;?>px; max-height:300px;overfllow-y:auto;">
        	<table width="<?=$tbl_width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left"> 
	    		<tbody id="tbl_list_search" align="center">
	        		<?
	        		$i=1;
	        		$tot_no_of_bundle = 0;
	        		$tot_qty = 0;
	        		$color_wise_issue_arr = array();
	        		$color_wise_rcv_arr = array();
	        		$grnd_tot_arr = array();
					$GrandTotal = 0 ;
					$Rcv_total = 0 ;
					$Rcv_grand_total=0;
	        		foreach ($issueArray as $color_id => $color_data) 
	        		{
        				?>
        				<!-- ============= order qty ========== -->
							<tr>
								<td width="330" colspan="5"><b>Order Qty</b></td>
								<?
								$tot = 0;
								foreach ($size_array as $skey => $sval) 
								{
									?>
									<td align="right" width="50"><b><?=number_format($orderQtyArray[$color_id][$skey]['order_qty'],0);?></b></td>
									<?
									$tot += $orderQtyArray[$color_id][$skey]['order_qty'];
								}
								?>
								<td align="right"><b><?=number_format($tot,0);?></b></td>

								<td width="80"></td>
								<td width="120"></td>
								<td width="120"></td>
								<?
								$tot = 0;
								foreach ($size_array as $skey => $sval) 
								{
									?>
									<td align="right" width="50"><b><?=number_format($orderQtyArray[$color_id][$skey]['order_qty'],0);?></b></td>
									<?
									$tot += $orderQtyArray[$color_id][$skey]['order_qty'];
								}
								?>
								<td align="right"><b><?=number_format($tot,0);?></b></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
							</tr>
							<!-- ============= plancut qty ========== -->
							<tr>
								<td width="330" colspan="5"><b>Extra Cut</b></td>
								<?
								$tot = 0;
								foreach ($size_array as $skey => $sval) 
								{
									?>
									<td align="right" width="50"><b><?=number_format(($orderQtyArray[$color_id][$skey]['plancut_qty']-$orderQtyArray[$color_id][$skey]['order_qty']),0);?></b></td>
									<?
									$tot += $orderQtyArray[$color_id][$skey]['plancut_qty']-$orderQtyArray[$color_id][$skey]['order_qty'];
								}
								?>
								<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>

								<td width="80"></td>
								<td width="120"></td>
								<td width="120"></td>
								<?
								$tot = 0;
								foreach ($size_array as $skey => $sval) 
								{
									?>
									<td align="right" width="50"><b><?=number_format(($orderQtyArray[$color_id][$skey]['plancut_qty']-$orderQtyArray[$color_id][$skey]['order_qty']),0);?></b></td>
									<?
									$tot += $orderQtyArray[$color_id][$skey]['plancut_qty']-$orderQtyArray[$color_id][$skey]['order_qty'];
								}
								?>
								<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
							</tr>
							<!-- ============= total qty ========== -->
							<tr style="background: #B9F8D3;">
								<td width="330" colspan="5"><b>Total Order Qty</b></td>
								<?
								$tot = 0;
								foreach ($size_array as $skey => $sval) 
								{
									?>
									<td align="right" width="50"><b><?=number_format($orderQtyArray[$color_id][$skey]['plancut_qty'],0);?></b></td>
									<?
									$tot += $orderQtyArray[$color_id][$skey]['plancut_qty'];
								}
								?>
								<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>

								<td width="80"></td>
								<td width="120"></td>
								<td width="120"></td>
								<?
								$tot = 0;
								foreach ($size_array as $skey => $sval) 
								{
									?>
									<td align="right" width="50"><b><?=number_format($orderQtyArray[$color_id][$skey]['plancut_qty'],0);?></b></td>
									<?
									$tot += $orderQtyArray[$color_id][$skey]['plancut_qty'];
								}
								?>
								<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
							</tr>
        				<?
						$newTot=0;
						$rej_new_total=0;
						$total_sent=0;
						$rej_qty = 0;
	        			foreach ($color_data as $date => $date_data) 
	        			{
	        				foreach ($date_data as $sys_id => $sys_data) 
	        				{	        									
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
								?>
								
								<!-- ============== details part ============= -->
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
									<td width="30" align="left"><? echo $i;?></td>
									<td width="80" align="center"><?=change_date_format($date);?></td>
									<td width="120" align="center"><?=$lib_supplier[$supplier_array[$sys_id]];?></td>
									<td width="120"><?=$challan_array[$sys_id];?></td>
									<td width="100"><?=$lib_color[$color_id];?></td>
									<?
									$tot_issue = 0;
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td width="50" align="right"><?=$sys_data[$skey]['size_qty'];?></td>
										<?
										$tot_issue += $sys_data[$skey]['size_qty'];
										$color_wise_issue_arr[$color_id][$skey] += $sys_data[$skey]['size_qty'];
										$grnd_tot_arr[$skey]['issue'] += $sys_data[$skey]['size_qty'];
									}
									?>
									<td width="80" align="right"><?=number_format($tot_issue,0);?></td>


									<td width="<?=560+count($size_array)*50;?>" colspan="<?=8+count($size_array);?>" style="border: 0px;">
										<table border="1" cellspacing="0" cellpadding="0" rules="all">
											
											<?
											// echo "<pre>"; print_r($receiveArray);
											$newRcv = 0 ;
											
											$first_rowspan = true;
											$k=1;
											$RcvDataArray = $receiveArray[$color_id][$sys_id];
											foreach ($RcvDataArray as $date_key => $date_val) 
											{ 
												
												foreach ($date_val as $sys_key => $sys_val) 
												{

													?>
													<tr >
														<td style="width: 80px;" align="center"><?=change_date_format($date_key);?></td>
														<td align="center" width="120"><?=$challan_array[$sys_key];?></td>
														<td align="center" width="120"><?=$manual_challan_array[$sys_key];?></td>
														<?
														$tot_rcv=0;
														
														foreach ($size_array as $skey => $sval) 
														{
															?>
															<td align="right" style="width: 50px;"><?=$sys_val[$skey]['size_qty'];?></td>
															<? $tot_rcv += $sys_val[$skey]['size_qty'];
															
															$color_wise_rcv_arr[$color_id][$skey] += $sys_val[$skey]['size_qty'];
															$grnd_tot_arr[$skey]['rcv'] += $sys_val[$skey]['size_qty'];
														}
														?>
														<td style="width: 80px;" align="right"><?=number_format($tot_rcv,0);?></td>
														<?php if($first_rowspan){ ?>
															<td align="right" style="width: 80px;" valign="middle" rowspan="<?=$receiveArray[$color_id][$sys_id]['row_span'];?>"><?=$receiveArray[$color_id][$sys_id]['receive_total'];
															$newTot += $receiveArray[$color_id][$sys_id]['receive_total'];
															?></td>
														<?php } ?>
														<td align="right" style="width: 80px;"><?=number_format($sys_val['reject_qty'],0);
														$rej_qty += $sys_val['reject_qty'];
														?></td>
														<?php if($first_rowspan){ ?>
															<td style="width: 80px;" align="right" valign="middle"  rowspan="<?=$receiveArray[$color_id][$sys_id]['row_span'];?>"><?=$receiveArray[$color_id][$sys_id]['reject_total'];
															$rej_new_total += $receiveArray[$color_id][$sys_id]['reject_total'];?></td>
														<?php } ?>
														<?php if($first_rowspan){ ?>
															<td  style="width: 80px;" align="right" valign="middle"  rowspan="<?=$receiveArray[$color_id][$sys_id]['row_span'];?>"><?=  $tot_issue -    $receiveArray[$color_id][$sys_id]['receive_total'];
															$total_sent += $tot_issue -    $receiveArray[$color_id][$sys_id]['receive_total'];?></td>
														<?php } ?>
													</tr>
													<?
													$first_rowspan = false;
												}
												
												
											}
											
											?>
										
										</table>
									</td>
								</tr>
								
								<?
								$i++;
	        					
	        				}
	        			}
	        			?>
	        			<!-- ============= Total sent ========== -->
								<tr style="background: #cddcdc;">
									<td width="330" colspan="5"><b>Total Sent</b></td>
									<?
									$tot = 0;
									
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td align="right" width="50"><b><?=number_format($color_wise_issue_arr[$color_id][$skey],0);?></b></td>
										<?
										$tot += $color_wise_issue_arr[$color_id][$skey];

									}
									?>
									<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>

									<td width="80"></td>
									<td width="120"></td>
									<td width="120"></td>
									<?
									$tot = 0;
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td align="right" width="50"><b></b></td>
										<?
									}
									?>
									<td width="80" align="right"><b></b></td>
									<td width="80"></td>
									<td width="80"></td>
									<td width="80"></td>
									<td width="80"></td>
								</tr>
								<!-- ============= Due sent ========== -->
								<tr  style="background: #dccdcd;">
									<td width="330" colspan="5"><b>Due Sent</b></td>
									<?
									$tot = 0;
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td align="right" width="50"><b><?=number_format(($orderQtyArray[$color_id][$skey]['plancut_qty']-$color_wise_issue_arr[$color_id][$skey]),0);?></b></td>
										<?
										$tot += $orderQtyArray[$color_id][$skey]['plancut_qty']-$color_wise_issue_arr[$color_id][$skey];
									}
									?>
									<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>

									<td width="80"></td>
									<td width="120"></td>
									<td width="120" align="right"><b>Total Receive</b></td>
									<?
									$tot = 0;
									
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td align="right" width="50"><b><?=number_format($color_wise_rcv_arr[$color_id][$skey],0);?></b></td>
										<?
										$tot += $color_wise_rcv_arr[$color_id][$skey];
										//$newTot +=$color_wise_rcv_arr[$color_id][$skey];
										
									}
									
									?>
									<td align="right" width="80" align="right"><b><?=number_format($tot,0);?></b></td>
									<td align="right" width="80" align="right"><b><?echo $newTot;?></b></td>
									<td align="right" width="80" align="right"><b><?=number_format($rej_qty,0);?></b></td>
									<td align="right" width="80" align="right"><b><?=$rej_new_total ?></b></td>
									<td align="right" width="80" align="right"><b><? echo $total_sent; ?></b></td>
								</tr>
	        			<?
						$GrandTotal += $tot ;
						$Rcv_total += $tot ;
						$Rcv_grand_total += $newTot ;
						$newRej += $rej_new_total;
						$new_rej_qty += $rej_qty ;
						$total_sent_new += $total_sent;
	        		}
					
	        		?>
	        	</tbody>
				<tfoot>
               <tr>
	                <th width="330" style="text-align:center;" colspan="5"> Grand Total Sent</th>
					<?
					foreach ($size_array as $skey => $sval) 
					{
						?>
						<th width="50"><?=$grnd_tot_arr[$skey]['issue'];?></th>
						<?
					}
					?>
					<th align="right" width="80"><? echo $GrandTotal ?></th>
					<th align="right" width="80" align="center"></th>
					<th align="right" width="120"></th>
					<th align="right" width="120">Grand Total Rcv</th>
					<?
					foreach ($size_array as $skey => $sval) 
					{
						?>
						<th width="50"><?=$grnd_tot_arr[$skey]['rcv'];?></th>
						<?
					}
					?>
					<th align="right" width="80"><?=$Rcv_total?></th>
					<th  align="right" width="80"><?=$Rcv_grand_total?></th>
					<th align="right" width="80"><?=number_format($new_rej_qty,0);?></th>
					<th align="right"  width="80"><?=number_format($newRej,0);?></th>
					<th align="right" width="80"><?=number_format($total_sent_new,0);?></th>
				</tr>
            </tfoot> 
	        </table>
	    </div>
	    <!-- ========================== table footer ========================== -->
        <!-- <table width="<? //$tbl_width+5;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left"> 
             
        </table>  -->
    </fieldset>
	<?
	unset($dataArray);
	unset($qtyArray);
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

if($action=="report_generate_bk")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_id 	= str_replace("'", "", $cbo_company_name);
	$buyer_id 		= str_replace("'", "", $cbo_buyer_name);
	$txt_style_no 	= str_replace("'", "", $txt_style_no);
	$txt_job_no 	= str_replace("'", "", $txt_job_no);
	$txt_order_no 	= str_replace("'", "", $txt_order_no);
	$txt_int_ref 	= str_replace("'", "", $txt_int_ref);
	$embel_type 	= str_replace("'", "", $cbo_embel_type);
	 
	$lib_buyer=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$lib_supplier=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$lib_company=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$lib_location=return_library_array( "select id, location_name from  lib_location",'id','location_name');
	$lib_floor=return_library_array( "select id, floor_name from  lib_prod_floor",'id','floor_name');
	$lib_color=return_library_array( "select id, color_name from  lib_color",'id','color_name');
	$lib_size=return_library_array( "select id, size_name from  lib_size",'id','size_name');
	
	/* =================================================================================/
    / 										SQL Condition								/
    /================================================================================= */

	$sql_cond = "";
	$sql_cond .= ($company_id==0) ? "": " and d.company_name=$company_id";
	$sql_cond .= ($buyer_id==0) ? "": " and d.buyer_name=$buyer_id";
	$sql_cond .= ($txt_style_no=="") ? "": " and d.style_ref_no like '%$txt_style_no%'";
	$sql_cond .= ($txt_job_no=="") ? "": " and d.job_no_prefix_num=$txt_job_no";
	$sql_cond .= ($txt_order_no=="") ? "": " and e.po_number like '%$txt_order_no%'";
	$sql_cond .= ($txt_int_ref=="") ? "": " and e.grouping like '%$txt_int_ref%'";
	$sql_cond .= ($embel_type==0) ? "": " and a.embel_name=$embel_type";	

	
	/* =================================================================================/
    / 										Main Query									/
    /================================================================================= */

	$sql="SELECT f.id as sys_id,f.sys_number,a.po_break_down_id as PO_ID,a.production_date,a.embel_name,b.production_qnty  as QTY,b.reject_qty, d.JOB_NO, d.style_ref_no as STYLE, e.PO_NUMBER,c.color_number_id as COLOR_ID,c.size_number_id as size_id,c.order_quantity,c.plan_cut_qnty,c.id as color_size_id,f.issue_challan_id,a.production_type from pro_garments_production_mst a, pro_garments_production_dtls b,pro_gmts_delivery_mst f, wo_po_color_size_breakdown c, wo_po_details_master d, wo_po_break_down e where a.id=b.mst_id and a.production_type in(2,3) and b.production_type in(2,3) and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and d.id=e.job_id and d.id=c.job_id and e.id=a.po_break_down_id and e.id=c.po_break_down_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.id=a.delivery_mst_id and f.status_active=1 and f.is_deleted=0 $sql_cond order by c.size_order";
	// echo $sql;die();
	$sql_res = sql_select($sql);
	if(count($sql_res)==0)
	{
		echo '<div style="text-align:center;color:red;font-weight:bold;font-size:18px;">Data not found.</div>';die();
	}
	$issueArray = array();
	$receiveArray = array();
	$orderQtyArray = array();
	$color_size_id_chk_array = array();
	$issue_challan_array = array();
	$size_array = array();
	foreach ($sql_res as $val) 
	{
		if($val['PRODUCTION_TYPE']==2)
		{
			$issueArray[$val['COLOR_ID']][$val['PRODUCTION_DATE']][$val['SYS_ID']][$val['SIZE_ID']]['size_qty'] += $val['QTY'];
		}
		else
		{
			$receiveArray[$val['COLOR_ID']][$val['ISSUE_CHALLAN_ID']][$val['PRODUCTION_DATE']][$val['SYS_ID']][$val['SIZE_ID']]['size_qty'] += $val['QTY'];
			$receiveArray[$val['COLOR_ID']][$val['ISSUE_CHALLAN_ID']][$val['PRODUCTION_DATE']][$val['SYS_ID']]['reject_qty'] += $val['REJECT_QTY'];
		}

		
		if($color_size_id_chk_array[$val['COLOR_SIZE_ID']]=="")
		{
			$orderQtyArray[$val['COLOR_ID']][$val['SIZE_ID']]['order_qty'] += $val['ORDER_QUANTITY'];
			$orderQtyArray[$val['COLOR_ID']][$val['SIZE_ID']]['plancut_qty'] += $val['PLAN_CUT_QNTY'];
			$color_size_id_chk_array[$val['COLOR_SIZE_ID']] = $val['COLOR_SIZE_ID'];
		}
		$size_array[$val['SIZE_ID']] = $val['SIZE_ID'];
		$challan_array[$val['SYS_ID']] = $val['SYS_NUMBER'];
		$job_no = $val['JOB_NO'];
		$style = $val['STYLE'];
		$po_number .= $val['PO_NUMBER']."**";
	}
	// echo "<pre>"; print_r($receiveArray[8502][8810]);die();
	unset($sql_res);

	$tbl_width = 850+(count($size_array)*2*50);

	ob_start();
	?>	
 	<fieldset style="width:<?=$tbl_width+20;?>px;"> 	
 		<style type="text/css">
 			h2{font-size: 20px;font-weight: bold;}

 			#resp-table {
			width: 100%;
			display: table;
			}
 			#resp-table-body{
			display: table-row-group;
			}
 			.resp-table-row{
				display: table-row;
				}
 			.table-body-cell{
				display: table-cell;
				border: 1px solid #8DAFDA;
				text-align: right;
				}
 		</style>
 		<div>
 			<center>
	 			<h2><? echo ucfirst($lib_company[$company_id]);?></h2>
	 			<h2>Embellishment Report</h2>
	 		</center>

 			<table>
 				<tr>
 					<td width="10%"><b>Job No:</b></td>
 					<td width="20%"><?=$job_no;?></td>
 					<td width="10%"><b>Style No:</b></td>
 					<td width="20%"><?=$style;?></td>
 					<td width="10%"><b>PO Number:</b></td>
 					<td width="20%"><?=implode(", ", array_unique(array_filter(explode("**", $po_number))));?></td>
 				</tr>
 			</table>
 		</div>	
 		<!-- ========================== table heading ========================== -->
        <table width="<?=$tbl_width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left"> 
            <thead>
            	<tr>
            		<th colspan="<?=count($size_array)+5;?>">Sent Part</th>
            		<th colspan="<?=count($size_array)+5;?>">Receive Part</th>
            	</tr>
               <tr>
	                <th rowspan="2" width="30">SL</th>
	                <th rowspan="2" width="80">Issue Date</th>
	                <th rowspan="2" width="120">Challan No</th>
	                <th rowspan="2" width="100">Color</th>
	                <th colspan="<?=count($size_array);?>" width="<?=count($size_array)*50;?>">Size</th>
	                <th rowspan="2" width="80">Total Sent</th>

	                
	                <th rowspan="2" width="80">Receive Date</th>
	                <th rowspan="2" width="120">Challan No</th>
	                <th colspan="<?=count($size_array);?>" width="<?=count($size_array)*50;?>">Size</th>
	                <th rowspan="2" width="80">Total Rcv</th>
	                <th rowspan="2" width="80">Reject Qty</th>
	                <th rowspan="2" width="80">Total Short</th>


				</tr>
				<tr>
					<?
					foreach ($size_array as $skey => $sval) 
					{
						?>
						<th width="50"><p><?=$lib_size[$skey];?></p></th>
						<?
					}
					// ===========================
					foreach ($size_array as $skey => $sval) 
					{
						?>
						<th width="50"><p><?=$lib_size[$skey];?></p></th>
						<?
					}
					?>
				</tr>
            </thead>  
        </table> 
        
        <!-- ========================== table body ========================== -->         
        <div id="scroll_body" style="width:<?=$tbl_width+20;?>px; max-height:300px;overfllow-y:auto;">
        	<table width="<?=$tbl_width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left"> 
	    		<tbody id="tbl_list_search" align="center">
	        		<?
	        		$i=1;
	        		$tot_no_of_bundle = 0;
	        		$tot_qty = 0;
	        		$color_wise_issue_arr = array();
	        		$color_wise_rcv_arr = array();
	        		$grnd_tot_arr = array();
	        		foreach ($issueArray as $color_id => $color_data) 
	        		{
	        			foreach ($color_data as $date => $date_data) 
	        			{
	        				?>
	        				<!-- ============= order qty ========== -->
								<tr>
									<td width="330" colspan="4"><b>Order Qty</b></td>
									<?
									$tot = 0;
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td align="right" width="50"><b><?=number_format($orderQtyArray[$color_id][$skey]['order_qty'],0);?></b></td>
										<?
										$tot += $orderQtyArray[$color_id][$skey]['order_qty'];
									}
									?>
									<td align="right"><b><?=number_format($tot,0);?></b></td>

									<td width="80"></td>
									<td width="120"></td>
									<?
									$tot = 0;
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td align="right" width="50"><b><?=number_format($orderQtyArray[$color_id][$skey]['order_qty'],0);?></b></td>
										<?
										$tot += $orderQtyArray[$color_id][$skey]['order_qty'];
									}
									?>
									<td align="right"><b><?=number_format($tot,0);?></b></td>
									<td width="80"></td>
									<td width="80"></td>
								</tr>
								<!-- ============= plancut qty ========== -->
								<tr>
									<td width="330" colspan="4"><b>Extra Cut</b></td>
									<?
									$tot = 0;
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td align="right" width="50"><b><?=number_format(($orderQtyArray[$color_id][$skey]['plancut_qty']-$orderQtyArray[$color_id][$skey]['order_qty']),0);?></b></td>
										<?
										$tot += $orderQtyArray[$color_id][$skey]['plancut_qty']-$orderQtyArray[$color_id][$skey]['order_qty'];
									}
									?>
									<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>

									<td width="80"></td>
									<td width="120"></td>
									<?
									$tot = 0;
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td align="right" width="50"><b><?=number_format(($orderQtyArray[$color_id][$skey]['plancut_qty']-$orderQtyArray[$color_id][$skey]['order_qty']),0);?></b></td>
										<?
										$tot += $orderQtyArray[$color_id][$skey]['plancut_qty']-$orderQtyArray[$color_id][$skey]['order_qty'];
									}
									?>
									<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
									<td width="80"></td>
									<td width="80"></td>
								</tr>
								<!-- ============= total qty ========== -->
								<tr style="background: #B9F8D3;">
									<td width="330" colspan="4"><b>Total Order Qty</b></td>
									<?
									$tot = 0;
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td align="right" width="50"><b><?=number_format($orderQtyArray[$color_id][$skey]['plancut_qty'],0);?></b></td>
										<?
										$tot += $orderQtyArray[$color_id][$skey]['plancut_qty'];
									}
									?>
									<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>

									<td width="80"></td>
									<td width="120"></td>
									<?
									$tot = 0;
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td align="right" width="50"><b><?=number_format($orderQtyArray[$color_id][$skey]['plancut_qty'],0);?></b></td>
										<?
										$tot += $orderQtyArray[$color_id][$skey]['plancut_qty'];
									}
									?>
									<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
									<td width="80"></td>
									<td width="80"></td>
								</tr>
	        				<?
	        				foreach ($date_data as $sys_id => $sys_data) 
	        				{	        									
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
								?>
								
								<!-- ============== details part ============= -->
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
									<td width="30" align="left"><? echo $i;?></td>
									<td width="80" align="center"><?=change_date_format($date);?></td>
									<td width="120"><?=$challan_array[$sys_id];?></td>
									<td width="100"><?=$lib_color[$color_id];?></td>
									<?
									$tot_issue = 0;
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td width="50" align="right"><?=$sys_data[$skey]['size_qty'];?></td>
										<?
										$tot_issue += $sys_data[$skey]['size_qty'];
										$color_wise_issue_arr[$color_id][$skey] += $sys_data[$skey]['size_qty'];
										$grnd_tot_arr[$skey]['issue'] += $sys_data[$skey]['size_qty'];
									}
									?>
									<td width="80" align="right"><?=number_format($tot_issue,0);?></td>


									<td width="<?=440+count($size_array)*50;?>" colspan="<?=5+count($size_array);?>" style="border: 0px;">
										<div id="resp-table">
											<div id="resp-table-body">
											<?
											// echo "<pre>"; print_r($receiveArray);
											$k=1;
											foreach ($receiveArray[$color_id][$sys_id] as $date_key => $date_val) 
											{ 
												foreach ($date_val as $sys_key => $sys_val) 
												{

													?>
													<div class="resp-table-row">
														<div class="table-body-cell" style="width: 80px;" align="center"><?=change_date_format($date_key);?></div>
														<div class="table-body-cell" style="width: 120px;"><?=$challan_array[$sys_key];?></div>
														<?
														$tot_rcv=0;
														foreach ($size_array as $skey => $sval) 
														{
															?>
															<div class="table-body-cell" style="width: 50px;"><?=$sys_val[$skey]['size_qty'];?></div>
															<? $tot_rcv += $sys_val[$skey]['size_qty'];

															$color_wise_rcv_arr[$color_id][$skey] += $sys_val[$skey]['size_qty'];
															$grnd_tot_arr[$skey]['rcv'] += $sys_val[$skey]['size_qty'];
														}
														
														?>
														<div class="table-body-cell" style="width: 80px;"><?=number_format($tot_rcv,0);?></div>
														<div class="table-body-cell" style="width: 80px;"><?=number_format($sys_val['reject_qty'],0);?></div>
														<div class="table-body-cell" style="width: 80px;"><?=number_format(($tot_issue-$tot_rcv),0);?></div>
														
													</div>
													<?
												}
											}
											?>
										</div>
										</div>
									</td>
								</tr>
								
								<?
								$i++;
	        					
	        				}
	        			}
	        			?>
	        			<!-- ============= Total sent ========== -->
								<tr style="background: #cddcdc;">
									<td width="330" colspan="5"><b>Total Sent</b></td>
									<?
									$tot = 0;
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td align="right" width="50"><b><?=number_format($color_wise_issue_arr[$color_id][$skey],0);?></b></td>
										<?
										$tot += $color_wise_issue_arr[$color_id][$skey];
									}
									?>
									<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>

									<td width="80"></td>
									<td width="120"></td>
									<td width="120"></td>
									<?
									$tot = 0;
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td align="right" width="50"><b></b></td>
										<?
									}
									?>
									<td width="80" align="right"><b></b></td>
									<td width="80"></td>
									<td width="80"></td>
								</tr>
								<!-- ============= Due sent ========== -->
								<tr  style="background: #dccdcd;">
									<td width="330" colspan="5"><b>Due Sent</b></td>
									<?
									$tot = 0;
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td align="right" width="50"><b><?=number_format(($orderQtyArray[$color_id][$skey]['plancut_qty']-$color_wise_issue_arr[$color_id][$skey]),0);?></b></td>
										<?
										$tot += $orderQtyArray[$color_id][$skey]['plancut_qty']-$color_wise_issue_arr[$color_id][$skey];
									}
									?>
									<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>

									<td width="80"></td>
									<td width="120"></td>
									<td width="120" align="right"><b>Total Receive</b></td>
									<?
									$tot = 0;
									foreach ($size_array as $skey => $sval) 
									{
										?>
										<td align="right" width="50"><b><?=number_format($color_wise_rcv_arr[$color_id][$skey],0);?></b></td>
										<?
										$tot += $color_wise_rcv_arr[$color_id][$skey];
									}
									?>
									<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
									<td width="80" align="right"><?=number_format($a,0);?></td>
									<td width="80" align="right"><?=number_format($a,0);?></td>
								</tr>
	        			<?
	        		}
	        		?>
	        	</tbody>
	        </table>
	    </div>
	    <!-- ========================== table footer ========================== -->
        <table width="<?=$tbl_width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left"> 
            <tfoot>
               <tr>
	                <th width="30" align="left"></th>
					<th width="80" align="center"></th>
					<th width="120"></th>
					<th width="100"></th>
					<?
					foreach ($size_array as $skey => $sval) 
					{
						?>
						<th width="50"><?=$grnd_tot_arr[$skey]['issue'];?></th>
						<?
					}
					?>
					<th width="80"></th>


					<th width="80" align="center"></th>
					<th width="120"></th>
					<?
					foreach ($size_array as $skey => $sval) 
					{
						?>
						<th width="50"><?=$grnd_tot_arr[$skey]['rcv'];?></th>
						<?
					}
					?>
					<th width="80"><?=number_format($a,0);?></th>
					<th width="80"><?=number_format($a,0);?></th>
					<th width="80"><?=number_format($a,0);?></th>
				</tr>
            </tfoot>  
        </table> 
    </fieldset>
	<?
	unset($dataArray);
	unset($qtyArray);
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