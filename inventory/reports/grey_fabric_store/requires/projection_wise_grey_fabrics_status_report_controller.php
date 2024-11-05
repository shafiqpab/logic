<? 
	header('Content-type:text/html; charset=utf-8');
	session_start();
	include('../../../../includes/common.php');
	if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];
	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];
	$user_name=$_SESSION['logic_erp']['user_id'];
// finish fab order to order transfer problem

//--------------------------------------------------------------------------------------------------------------------
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$buyer_array=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_id=str_replace("'", "", $cbo_company_name);
	$cbo_buyer_name=str_replace("'", "", $cbo_buyer_name);
	$job_no=str_replace("'","",$txt_job_no);
	$sample_year=str_replace("'", "", $cbo_year);
	$year_cond="";
	if($db_type==2)
	{
		$year_cond=($sample_year)? " and  to_char(a.insert_date,'YYYY')=$sample_year" : " ";
	}
	else
	{
		$year_cond=($sample_year)? " and year(a.insert_date)=$sample_year" : " ";
	}
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond="and a.job_no_prefix_num in ($job_no) ";
	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and a.company_id=$cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")
		$txt_date="";
	else
		$txt_date=" and b.po_received_date between $txt_date_from and $txt_date_to";
	$style=str_replace("'", "", $txt_style_no);
	if($style=='') $style_ref="";else $style_ref=" and a.style_ref_no like '%$style%'";
	ob_start();
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$order_library=return_library_array( "select id,is_confirmed from wo_po_break_down", "id", "is_confirmed"  );
	$fabric_description_library=return_library_array( "select id,fabric_description from wo_pre_cost_fabric_cost_dtls", "id", "fabric_description"  );
	?>
	<script type="text/javascript">setFilterGrid('table_body',-1);</script>
	<div>
        <table cellpadding="0" cellspacing="0" width="1550">
            <tr  class="form_caption" style="border:none;">
           		 <td align="center" width="100%" colspan="18" style="font-size:20px"><strong><? echo 'Projection Wise Grey Fabrics Status Report '; ?></strong></td>
            </tr>
            <tr  class="form_caption" style="border:none;">
                <td colspan="18" align="center" style="border:none; font-size:14px;">
                <b><? echo $company_library[$cbo_company_id]; ?></b>
                </td>
            </tr>
            <tr  class="form_caption" style="border:none;">
                <td align="center" width="100%" colspan="18" style="font-size:12px">
                <? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
                </td>
            </tr>
        </table>
		<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1550" rules="all" id="table_header" >
			<thead>
				<tr>
					<th width="30">Sl No</th>
					<th width="110">PO Received Date</th>
					<th width="100">Buyer</th>
					<th width="90">Job No</th>
					<th width="95">Order No</th>
					<th width="110">Style</th>
					<th width="260">Fab. Description</th>
					<th width="100">Fab.Booking No</th>
					<th width="105">Book Qty/KG</th>
					<th width="80">Grey Fabric Rcv Qty(kg)</th>
					<th width="110">Grey Transfer to Order</th>
					<th width="80">Order wise Qty</th>
					<th width="80">Order Status</th>
					<th width="80">Total Transfer Out Qty</th>
					<th>Stock Qty(kg)</th>
				</tr>
			</thead>
		</table>
		<div style="max-height:320px; overflow-y:scroll; width:1568px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1550" rules="all" id="table_body">
				<tbody>
					<?
					
					
					$po_arr=return_library_array( "SELECT id, po_number from wo_po_break_down", "id", "po_number"  );
					$projection_transfer_mst = "SELECT a.job_no, a.style_ref_no, a.buyer_name,to_char(a.insert_date,'YYYY') as year,b.id as po_id,b.po_number,b.po_received_date, c.booking_no, c.grey_fab_qnty, c.pre_cost_fabric_cost_dtls_id, d.quantity as rcv_qty, f.to_order_id,d.trans_type,d.entry_form,e.id as trnsfer_dtls_id,f.id as trnsfer_mst_id,d.id as pro_details_id,d.prod_id from wo_po_details_master a join wo_po_break_down b on a.job_no=b.job_no_mst join wo_booking_dtls c on c.job_no = a.job_no join order_wise_pro_details d on b.id = d.po_breakdown_id and d.po_breakdown_id=c.po_break_down_id left join inv_item_transfer_dtls e on e.id = d.dtls_id  left join inv_item_transfer_mst f on e.mst_id =f.id where d.trans_type in (1,6) and c.booking_type in(1,4) and a.company_name=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and b.is_confirmed=2 and d.trans_id>0 and d.entry_form in (22,58,13,80)  $job_no_cond $year_cond $buyer_name $txt_date $style_ref group by a.job_no, a.style_ref_no, a.buyer_name ,a.insert_date, b.id,b.po_number,b.po_received_date, c.booking_no, c.grey_fab_qnty, c.pre_cost_fabric_cost_dtls_id, d.quantity, f. to_order_id,d.trans_type,d.entry_form,e.id,f.id,d.id,d.prod_id order by a.job_no";

					//echo $projection_transfer_mst;die;
					 $projection_transfer_arr=sql_select($projection_transfer_mst);
					 
					 $projection_transfer_data = array();
					 $attributes = array('job_no', 'style_ref_no', 'buyer_name' , 'po_id', 'po_number', 'po_received_date', 'booking_no', 'grey_fab_qnty', 'pre_cost_fabric_cost_dtls_id','entry_form','trans_type','trnsfer_dtls_id','prod_id');
					 

					 foreach ($projection_transfer_arr as $row) 
					 {
					 	$key = $row[csf('job_no')].'*'.$row[csf('buyer_name')].'*'.$row[csf('po_id')].'*'.$row[csf('style_ref_no')].'*'.$row[csf('booking_no')];
						
					 	foreach ($attributes as $attr)
						{
					 		$projection_transfer_data[$key][$attr] = $row[csf($attr)];
					 	}
						
					 	if($row[csf('to_order_id')] != '')
						{
					 		$projection_transfer_data[$key]['to_order_id'][$row[csf('trans_type')]][$row[csf('trnsfer_mst_id')]] = $row[csf('to_order_id')];
					 	}
						
					 	$projection_transfer_data[$key]['rcv_qty'][$row[csf('entry_form')]][$row[csf('trans_type')]] = $row[csf('rcv_qty')];
						$projection_transfer_data[$key]['trns_in_qty'][$row[csf('entry_form')]][$row[csf('trans_type')]][$row[csf('trnsfer_mst_id')]]= $row[csf('rcv_qty')];
						$booking_no[$row[csf('booking_no')]] = "'".$row[csf('booking_no')]."'";
						$po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
					 }

					// echo '<pre>';
				  //  print_r($po_id_arr); die;
					//echo "Select GREY_FAB_QNTY,JOB_NO,BOOKING_NO from wo_booking_dtls where BOOKING_NO in (".implode(',', $booking_no).")"; die;
					$gray_fab_qty = sql_select("select grey_fab_qnty,job_no,booking_no from wo_booking_dtls where booking_no in (".implode(',', $booking_no).")");
					foreach($gray_fab_qty as $row)
					{
						$gray_fab_qty_arr[$row[csf('booking_no')]][$row[csf('job_no')]] += $row[csf('grey_fab_qnty')];
					}
					
				//	echo "select c.po_breakdown_id, sum(c.quantity) as receive_quantity from order_wise_pro_details c where c.entry_form in (22) and c.po_breakdown_id in(".implode(',', $po_id_arr).")  c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id order by c.po_breakdown_id"; die;
					$receive_sql=sql_select("select c.po_breakdown_id, sum(c.quantity) as receive_quantity from order_wise_pro_details c where c.entry_form in (22) and c.po_breakdown_id in(".implode(',', $po_id_arr).") and  c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id order by c.po_breakdown_id"); //and d.po_breakdown_id=c.po_break_down_id
					
					foreach($receive_sql as $row)
					{
						$receive_qty_arr[$row[csf('po_breakdown_id')]]+= $row[csf('receive_quantity')];
					}
					
					
					
					$sql="SELECT a.id as transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id,b.to_prod_id, sum(c.quantity) as transfer_qnty,a.to_samp_dtls_id,c.prod_id 
				from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c
				where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=13 and c.trans_type=6 and c.entry_form in (13,80,82,83,110) and c.po_breakdown_id in(".implode(',', $po_id_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
				group by a.id, c.prod_id, a.id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, b.to_prod_id,a.to_samp_dtls_id,c.prod_id";
				
					$result=sql_select($sql);
                // ================= getting non order booking id ===================
					$trans_out_qty_arr = array();
					
					foreach($result as $row)
					{
						$trans_out_qty_arr[$row[csf('transfer_system_id')]] = $row[csf('transfer_qnty')];
					}
					//echo '<pre>';
					//print_r($trans_out_qty_arr); die;
					 $i =1; $firstKey =''; $row_span ='';
					 $total_transfer_value=0;$totalgrey_fab_qnty=0;$totalrcv_qty=0;$totaltrns_in_qty=0;$totalStockQty=0;$transfer_value=0;
					 
					$grand_total=array();
					$zs=0;
					foreach ($projection_transfer_data as $value)
					{ 
						$zs++;
						$match_key = $value['job_no'].'*'.$value['buyer_name'].'*'.$value['po_id'].'*'.$value['style_ref_no'].'*'.$value['booking_no'];
						$get_key = array_keys($projection_transfer_data[$match_key]['to_order_id'][6]);
						$firstKey = $get_key[0];
						//echo 'joy'.$firstKey; die;
						$row_span = count($projection_transfer_data[$match_key]['to_order_id'][6]);
						foreach ($projection_transfer_data[$match_key]['to_order_id'][6] as $key => $data)
						{ 
							if($key == $firstKey)
							{
								$grand_total[$zs]+=$trans_out_qty_arr[$key];
							}
							else
							{
								$grand_total[$zs]+=$trans_out_qty_arr[$key];
							}
						}
					}

					 $zs=0;
					 foreach ($projection_transfer_data as $value)
					 { 
					 	$zs++;
						$match_key = $value['job_no'].'*'.$value['buyer_name'].'*'.$value['po_id'].'*'.$value['style_ref_no'].'*'.$value['booking_no'];
					 	$get_key = array_keys($projection_transfer_data[$match_key]['to_order_id'][6]);
						$firstKey = $get_key[0];
					 	//echo 'joy'.$firstKey; die;
					 	$row_span = count($projection_transfer_data[$match_key]['to_order_id'][6]);
					 	foreach ($projection_transfer_data[$match_key]['to_order_id'][6] as $key => $data) { 
					 		if($key == $firstKey){?>
					 		<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                            <td width="30"  align="center" rowspan="<? echo $row_span ?>"><? echo $i; ?></td>
                            <td width="110" style="word-break:break-all" rowspan="<? echo $row_span ?>"><? echo change_date_format($value['po_received_date']); ?></td>                    <td width="100" style="word-break:break-all" rowspan="<? echo $row_span ?>"><? echo  $buyer_array[$value['buyer_name']]; ?></td>
                            <td width="90" style="word-break:break-all" rowspan="<? echo $row_span ?>"><? echo $value['job_no']; ?></td>
                            <td width="95" align="center" rowspan="<? echo $row_span ?>"><? echo $value['po_number']; ?></td>
                            <td width="110" style="word-break:break-all" rowspan="<? echo $row_span ?>" ><? echo $value['style_ref_no']; ?></td>
                            <td width="260" style="word-break:break-all" rowspan="<? echo $row_span ?>"><? echo $fabric_description_library[$value['pre_cost_fabric_cost_dtls_id']]; ?></td>
                            <td width="100" style="word-break:break-all" rowspan="<? echo $row_span ?>"><? echo $value['booking_no']; ?></td>
                            <td width="105" style="word-break:break-all" rowspan="<? echo $row_span ?>"  align="right"><? echo number_format($gray_fab_qty_arr[$value['booking_no']][$value['job_no']],2,'.',''); $totalgrey_fab_qnty+=$gray_fab_qty_arr[$value['booking_no']][$value['job_no']] ?></td>
                            <td width="80" style="word-break:break-all" rowspan="<? echo $row_span ?>" align="right"><? 
							echo number_format($receive_qty_arr[$value['po_id']],2,'.','');
							$totalrcv_qty+=$receive_qty_arr[$value['po_id']];?></td>
                            <td width="110" style="word-break:break-all" align="center"><? echo $po_arr[$projection_transfer_data[$match_key]['to_order_id'][6][$key]];?></td>
							<td width="80" style="word-break:break-all" align="right"><? echo number_format($trans_out_qty_arr[$key],2,'.','');$totaltrns_in_qty+=$trans_out_qty_arr[$key];  
							 ?></td>
                            <td width="80" style="word-break:break-all" align="center"><? echo $order_status[$order_library[$projection_transfer_data[$match_key]['to_order_id'][6][$key]]]; ?></td>
                            <td  width="80" style="word-break:break-all" align="right" rowspan="<? echo $row_span ?>" align="right"><?
                            	$rcv_value = $receive_qty_arr[$value['po_id']];
                            	//$transfer_value = array_sum($trans_out_qty_arr);
								$transfer_value=$grand_total[$zs];
                            	echo number_format($transfer_value,2,'.','');
								$total_transfer_value+=$grand_total[$zs];
                            ?></td>
                            <td  style="word-break:break-all" align="right" rowspan="<? echo $row_span ?>">
                            	<?
                            		echo number_format(($rcv_value-$transfer_value),2,'.',''); $totalStockQty+=$rcv_value-$transfer_value;
									
                            	?>
                            </td>
                        	</tr>
                            <?
							}
							else
							{
								?>
								<tr>
                                    <td width="110" style="word-break:break-all" align="center"><? echo $po_arr[$projection_transfer_data[$match_key]['to_order_id'][6][$key]];?></td>
                                    <td width="80" style="word-break:break-all" align="right"><? echo number_format($trans_out_qty_arr[$key],2,'.','');$totaltrns_in_qty+=$trans_out_qty_arr[$key];?></td>
                                    <td width="80" style="word-break:break-all" align="center"><? echo $order_status[$order_library[$projection_transfer_data[$match_key]['to_order_id'][6][$key]]]; ?></td>
                            	</tr>
                                <?
							}
						}
						$i++;
					  }
					?>
							
					</tbody>
				</table>
                 <table width="1550" border="1" cellpadding="0" cellspacing="0" rules="all"> 
                 <tr class="tbl_bottom">
					<td width="30">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="90">&nbsp;</td>
					<td width="95">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="260">&nbsp;</td>
					<td width="100">Total</td>
					<td width="105" align="right"><? echo number_format($totalgrey_fab_qnty,2,'.','') ; ?></td>
					<td width="80" align="right"><? echo number_format($totalrcv_qty,2,'.','') ; ?></td>
					<td width="110">&nbsp;</td>
					<td width="80"><? echo number_format($totaltrns_in_qty,2,'.',''); ?></td>
					<td width="80" align="right"><? //echo number_format($receive_qty,2,'.','') ; ?></td> 
					<td width="80" align="right"><? echo number_format($total_transfer_value,2,'.','') ; ?></td>
					<td align="right"><? echo number_format($totalStockQty,2,'.','')  ; ?></td>
                    </tr>
				</table>
			</div>
		</div>
		<?
	foreach (glob("$user_name*.xls") as $filename) {
	if (@filemtime($filename) < (time() - $seconds_old))
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_name . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	}
?>