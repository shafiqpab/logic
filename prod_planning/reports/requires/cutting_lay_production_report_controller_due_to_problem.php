<?php 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class.reports.php');
require_once('../../../includes/class.fabrics.php');

$user_name	= $_SESSION['logic_erp']['user_id'];
$data		= $_REQUEST['data'];
$action		= $_REQUEST['action'];

$company_arr		= return_library_array( "select id, company_name from lib_company",'id','company_name');
$color_library		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$order_no_library	= return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$buyer_arr			= return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$table_arr			= return_library_array( "select id, table_no from lib_cutting_table",'id','table_no');

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "onchange_buyer()" );     	 
	exit();
}

if($action=="report_generate")
{
	//echo "su..re";
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//cbo_company_name*cbo_buyer_name*txt_job_no*txt_file_no*txt_order_no*txt_cutting_no*txt_table_no*txt_date_from*txt_date_to
	$company_name	= str_replace( "'", "", $cbo_company_name );
	$buyer_name		= str_replace( "'", "", $cbo_buyer_name );
	$job_no			= str_replace( "'", "", $txt_job_no );
	$file_no		= str_replace( "'", "", $txt_file_no );
	$order_no		= str_replace( "'", "", $txt_order_no );
	$cutting_no		= str_replace( "'", "", $txt_cutting_no );
	$table_no		= str_replace( "'", "", $txt_table_no );
	$from_date		= str_replace( "'", "", $txt_date_from );
	$to_date		= str_replace( "'", "", $txt_date_to );
	
	//id cutting_no table_no job_no entry_date
	$company_name	= "AND a.company_id	= '".$company_name."'";
	$buyer_name		== 0  ? $buyer_name		= "" : $buyer_name		= "AND c.buyer_name='".$buyer_name."'";
	$job_no			== "" ? $job_no			= "" : $job_no			= "AND c.job_no_prefix_num='".$job_no."'";
	$file_no		== "" ? $file_no		= "" : $file_no			= "AND d.file_no='".$file_no."'";
	$order_no		== "" ? $order_no		= "" : $order_no		= "AND d.po_number='".$order_no."'";
	$cutting_no		== "" ? $cutting_no		= "" : $cutting_no		= "AND a.cut_num_prefix_no='".$cutting_no."'";
	$table_no		== "" ? $table_no		= "" : $table_no		= "AND a.table_no='".$table_no."'";
	$from_date		!= "" && $to_date	   != "" ? $cutting_date	= "AND a.entry_date between $txt_date_from AND $txt_date_to" : $cutting_date="";

	//wo_po_color_size_breakdown
	$sql_order_dtls=sql_select("SELECT a.po_break_down_id, a.color_number_id, SUM(a.plan_cut_qnty) AS plan_cut_qnty, SUM(a.order_quantity) AS rmg_color_qty FROM wo_po_color_size_breakdown a WHERE status_active=1 AND is_deleted=0 GROUP BY a.po_break_down_id, a.color_number_id");
	$order_dtls_arr=array();
	foreach($sql_order_dtls as $row)
	{
		$order_dtls_arr[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['plan_cut_qnty'] = $row[csf('plan_cut_qnty')];
		$order_dtls_arr[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['rmg_color_qty'] = $row[csf('rmg_color_qty')];
	}
	//print_r($order_dtls_arr);die;

	$sql2=sql_select("SELECT po_breakdown_id, color_id, SUM(quantity) as quantity FROM order_wise_pro_details WHERE entry_form in(7,37) AND trans_id>0 GROUP BY po_breakdown_id, color_id");
	$fin_rcv_qty_arr=array();
	foreach($sql2 as $row)
	{
		$fin_rcv_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('quantity')];
	}

	$sql5=sql_select("SELECT order_id, color_id, SUM(reject_qty) AS reject_qty, SUM(qc_pass_qty) AS qc_pass_qty, SUM(replace_qty) AS replace_qty FROM pro_gmts_cutting_qc_dtls GROUP BY order_id, color_id");
	$qc_qty_arr=array();
	foreach($sql5 as $row)
	{
		$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]]['reject_qty']	= $row[csf('reject_qty')];
		$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]]['qc_pass_qty']= $row[csf('qc_pass_qty')];
		$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]]['replace_qty']= $row[csf('replace_qty')];
	}

	//main query============
	//$sql=sql_select("SELECT a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, b.order_id, b.color_id, SUM(b.marker_qty) AS marker_qty, c.buyer_name, c.style_ref_no, d.po_number, d.file_no FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b, wo_po_details_master c, wo_po_break_down d WHERE a.id=b.mst_id AND a.entry_form=76 AND a.job_no=c.job_no AND c.job_no=d.job_no_mst AND b.order_id=d.id $company_name $buyer_name $job_no $file_no $order_no $cutting_no $table_no $cutting_date GROUP BY a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, b.order_id, b.color_id, c.buyer_name, c.style_ref_no, d.po_number, d.file_no");
	
	$sql=sql_select("SELECT a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, a.entry_date, b.order_id, b.color_id, SUM(b.marker_qty) AS marker_qty, c.buyer_name, c.style_ref_no, d.po_number, d.file_no 
	FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b, wo_po_details_master c, wo_po_break_down d 
	WHERE a.id=b.mst_id AND a.entry_form=76 AND a.job_no=c.job_no AND c.job_no=d.job_no_mst AND b.order_id=d.id $company_name $buyer_name $job_no $file_no $order_no $cutting_no $table_no $cutting_date 
	GROUP BY a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, a.entry_date, b.order_id, b.color_id, c.buyer_name, c.style_ref_no, d.po_number, d.file_no");

	//===================
	$job_arr=array();
	foreach($sql as $job_for_class){
		$job_arr[]=$job_for_class[csf('job_no')];
	}
	$fabric=new fabric($job_arr,'job');
	$fab_data=$fabric->getQtyArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish();
	//==============================
	
	$job_order_color_arr=array();
	$subtotal_marker_qty=array();
	foreach($sql as $row)
	{
		$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cuting_date'] 	= $row[csf('entry_date')];
		$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cutting_no']		= $row[csf('cutting_no')];
		
		$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['table_no']		= $row[csf('table_no')];
		$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['order_id']		= $row[csf('order_id')];
		$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['job_no']			= $row[csf('job_no')];
		$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['batch_id']		= $row[csf('batch_id')];
		$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['buyer_name']		= $row[csf('buyer_name')];
		$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['file_no']			= $row[csf('file_no')];
		$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['po_number']		= $row[csf('po_number')];
		$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['style_ref_no']	= $row[csf('style_ref_no')];
		$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['lay_fabric_wght']	= $row[csf('lay_fabric_wght')];
		$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cad_marker_cons']	= $row[csf('cad_marker_cons')];
		$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['marker_qty']	 	= $row[csf('marker_qty')];
		$subtotal_marker_qty[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]]['marker_qty'] += $row[csf('marker_qty')];
	}

	//print_r($job_order_color_arr);
	//echo $sql;
	ob_start();
	?>
    <table class="rpt_table" width="1900" cellpadding="0" cellspacing="0" border="1" rules="all">
        <caption style="font-size:20px; font-weight:bold;">
			<?php 
				$com_name = str_replace( "'", "", $cbo_company_name );
                echo $company_arr[$com_name]."<br/>"."Cutting Lay Production Report";
            ?>
            <div style="color:red; text-align:left; font-size:16px;">Group By Job, PO and Color</div>
        </caption>
        <thead>
            <tr>
                <th width="50">Sl</th>
                <th width="70">Cutting Date</th>
                <th width="100">Cutting No.</th>
                <th width="100">Color Name</th>
                <th width="60">Table No</th>
                <th width="100">Buyer Name</th>
                <th width="100">Job No</th>
                <th width="60">Style Reff</th>
                <th width="60">File No</th>
                <th width="60">Batch No</th>
                <th width="60">Order No</th>
                <th width="60">Fini. Req. Qty.</th>
                <th width="60">Fini. Rcvd. Qty.</th>
                <th width="60">RMG Color Qty</th>
                <th width="60">Plan Cut Qty (Color)</th>
                <th width="60">Yet To Cut</th>
                <th width="60">Lay Fabric Weight (Kg)</th>
                <th width="60">CAD Marker Cons/Pcs</th>
                <th width="60">Marker Qty.</th>
                <th width="60">QC Pass Qty.</th>
                <th width="60">Replace Qty.</th>
                <th width="60">Reject Qty.</th>
                <th width="60">Net Cons/Pcs</th>
                <th width="60">QC Pass Cons. Qty.</th>
                <th width="60">Cons. Variation Qty.</th>
                <th width="60">Cons. Variation (%)</th>
                <th width="60">Total Rej. Fab. Qty. (Kg)</th>
                <th>Total Rej. Fab. Qty. (%)</th>
            </tr>
        </thead>
    </table>
    <div style=" max-height:350px; width:1900px; overflow-y:scroll;" id="scroll_body">
        <table class="rpt_table" id="table_body" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <tbody>
            	<?php 
				$sl=0;
				$color_subtot_arr=array();
				$grand_total_fini_req_qty			= 0;
				$grand_total_fini_rcv_qty			= 0;
				$grand_total_rmg_color_qty			= 0;
				$grand_total_plan_cut_qty			= 0;
				$grand_total_yet_to_cut				= 0;
				$grand_total_lay_fabric_weight		= 0;
				$grand_total_cad_marker_cons		= 0;
				$grand_total_marker_qty				= 0;
				$grand_total_qc_pass_qty			= 0;
				$grand_total_replace_qty			= 0;
				$grand_total_reject_qty				= 0;
				$grand_total_cut_cons_qty			= 0;
				$grand_total_qc_pass_cons_qty		= 0;
				$grand_total_cons_variation_qty		= 0;
				$grand_total_cons_variation_percn	= 0;
				$grand_total_reject_kg				= 0;
				$grand_total_reject_percn			= 0;

				foreach($job_order_color_arr as $job_ids=>$job_vals)
				{
					foreach($job_vals as $order_ids=>$order_vals)
					{
						//$color_subtot_arr['job_ids']=$job_ids;
						foreach($order_vals as $color_ids=>$color_vals)
						{							
							$total_fini_req_qty			= 0;
							$total_fini_rcv_qty			= 0;
							$total_rmg_color_qty		= 0;
							$total_plan_cut_qty			= 0;
							$total_yet_to_cut			= 0;
							$total_lay_fabric_weight	= 0;
							$total_cad_marker_cons		= 0;
							$total_marker_qty			= 0;
							$total_qc_pass_qty			= 0;
							$total_replace_qty			= 0;
							$total_reject_qty			= 0;
							$total_cut_cons_qty			= 0;
							$total_qc_pass_cons_qty		= 0;
							$total_cons_variation_qty	= 0;
							$total_cons_variation_percn	= 0;
							$total_reject_kg			= 0;
							$total_reject_percn			= 0;

							foreach($color_vals as $cutting_ids=>$cutting_vals)
							{
								$sl++;
								$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
								$fin_qty=$fab_data['knit']['finish'][$order_ids][$color_ids][1]+$fab_data['knit']['finish'][$order_ids][$color_ids][20]+$fab_data['knit']['finish'][$order_ids][$color_ids][125];
								//Plan Cut Qty - sum of  Marker Qty.
								$yet_to_cut=$order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty'] - $color_vals['marker_qty'];
								// Lay Fabric Weight / Marker Qty.
								$net_cons_per_pcs=$cutting_vals['lay_fabric_wght']/$cutting_vals['marker_qty'];
								//qc_pass_cons_qty = ((Replace Qty * Marker Cons. Per pcs) + Lay Fabric Weight)/QC pass qty.
								$qc_pass_cons_qty=(($qc_qty_arr[$color_ids][$order_ids]['replace_qty']*$net_cons_per_pcs)+$cutting_vals['lay_fabric_wght'])/$qc_qty_arr[$color_ids][$order_ids]['qc_pass_qty'];
								//cons_variation_qty=QC pass Consum - Net Cons per Pcs
								$cons_variation_qty=$qc_pass_cons_qty-$net_cons_per_pcs;
								//Cons. Variation / QC pass cons. * 100
								$cons_variation_percn=$cons_variation_qty/$qc_pass_cons_qty*100;
								//Reject Qty. * Net Cons Per Pcs
								$reject_kg=$qc_qty_arr[$color_ids][$order_ids]['reject_qty']*$net_cons_per_pcs;
								//Total Reject Fab. Qty. / Lay Fabric weight *100
								$reject_percn=$reject_kg/$cutting_vals['lay_fabric_wght']*100;

								?>
								<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
									<td width="50"><?php echo $sl; ?></td>
									<td width="70"><p><?php echo change_date_format($cutting_vals['cuting_date']); ?></p></td>
									<td width="100"><p><a href="#" onClick="generate_report_lay_chart('<?php echo $cutting_vals['cutting_no']."*".$job_ids; ?>')"><?php echo $cutting_vals['cutting_no']; ?></a></p></td>
									<td width="100"><p><?php echo $color_library[$color_ids]; ?></p></td>
									<td width="60"><p><?php echo $cutting_vals['table_no']; ?></p></td>
									<td width="100"><p><?php echo $buyer_arr[$cutting_vals['buyer_name']]; ?></p></td><!--Buyer Name-->
									<td width="100"><p><?php echo $cutting_vals['job_no']; ?></p></td><!--Job No-->
									<td width="60"><p><?php echo $cutting_vals['style_ref_no']; ?></p></td><!--Style Reff-->
									<td width="60"><p><?php echo $cutting_vals['file_no']; ?></p></td><!--File No-->
									<td width="60"><p><?php echo $cutting_vals['batch_id']; ?></p></td><!--Batch No-->
									<td width="60"><p><?php echo $cutting_vals['po_number']; ?></p></td><!--Order No-->
                                   
                                    <?
									//print_r($color_check_arr);
									if(in_array($color_ids,$color_check_arr))
									{
									?>	
									<td width="60" align="right"><p><?php //echo number_format($fin_qty, 2); ?></p></td><!--Fini. Req. Qty.-->
									<td width="60" align="right"><p><?php //echo number_format($fin_rcv_qty_arr[$order_ids][$color_ids], 2); ?></p></td><!--Fini. Rcvd. Qty.-->
									<td width="60" align="right"><p><?php //echo $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty']; ?></p></td><!--RMG Color Qty-->
									<td width="60" align="right"><p><?php //echo $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty']; ?></p></td><!--Plan Cut Qty (Color)-->
                                    <td width="60" align="right"><p><?php //echo $yet_to_cut; ?></p></td><!--Yet To Cut-->	
									<?	
									}
									else
									{
									$total_fini_req_qty			+= $fin_qty;
									$total_fini_rcv_qty			+= $fin_rcv_qty_arr[$order_ids][$color_ids];
									$total_rmg_color_qty		+= $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty'];
									$total_plan_cut_qty			+= $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty'];
									?>
                                    
									<td width="60" align="right"><p><?php echo number_format($fin_qty, 2); ?></p></td><!--Fini. Req. Qty.-->
									<td width="60" align="right"><p><?php echo number_format($fin_rcv_qty_arr[$order_ids][$color_ids], 2); ?></p></td><!--Fini. Rcvd. Qty.-->
									<td width="60" align="right"><p><?php echo $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty']; ?></p></td><!--RMG Color Qty-->
									<td width="60" align="right"><p><?php echo $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty']; ?></p></td><!--Plan Cut Qty (Color)-->
                                    <td width="60" align="right" title="(Plan Cut Qty (Color)) - (Marker qty)"><p><?php echo $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty']-$subtotal_marker_qty[$job_ids][$order_ids][$color_ids]['marker_qty']; ?></p></td><!--Yet To Cut-->
                                    <?
									}
									?>
									<td width="60" align="right"><p><?php echo number_format($cutting_vals['lay_fabric_wght'], 2); ?></p></td><!--Lay Fabric Weight (Kg)-->
									<td width="60" align="right"><p><?php echo number_format($cutting_vals['cad_marker_cons']/12, 4); ?></p></td><!--CAD Marker Cons/Pcs-->
									<td width="60" align="right"><p><?php echo $cutting_vals['marker_qty']; ?></p></td><!--Marker Qty.-->
									<td width="60" align="right"><p><?php echo $qc_qty_arr[$color_ids][$order_ids]['qc_pass_qty']; ?></p></td><!--QC Pass Qty.-->
									<td width="60" align="right"><p><?php echo $qc_qty_arr[$color_ids][$order_ids]['replace_qty']; ?></p></td><!--Replace Qty.-->
									<td width="60" align="right"><p><?php echo $qc_qty_arr[$color_ids][$order_ids]['reject_qty']; ?></p></td><!--Reject Qty.-->
									<td width="60" align="right" title="(Lay Fabric Weight (Kg)) / (Marker Qty)"><p><?php echo number_format($net_cons_per_pcs, 4); ?></p></td><!--Net Cons. Per Pcs-->
									<td width="60" align="right" title="((Replace Qty * Marker Cons Per pcs) + Lay Fabric Weight (Kg)) / QC pass qty"><p><?php echo number_format($qc_pass_cons_qty, 4); ?></p></td><!--QC Pass Cons. Qty.-->
									<td width="60" align="right" title="QC Pass Cons Qty - Net Cons per Pcs"><p><?php echo number_format($cons_variation_qty, 4); ?></p></td><!--Cons. Variation Qty.-->
									<td width="60" align="right" title="Cons Variation Qty /  QC Pass Cons Qty * 100"><p><?php echo number_format($cons_variation_percn, 2); ?></p></td><!--Cons. Variation %-->
									<td width="60" align="right" title="Reject Qty. * Net Cons Per Pcs"><p><?php echo number_format($reject_kg, 2); ?></p></td><!--Reject(Kg)-->
									<td align="right" title="Total Reject Fab Qty / Lay Fabric weight (kg)*100"><p><?php echo number_format($reject_percn, 2); ?></p></td><!--Reject(%)-->
								</tr>
								<?php 
								$total_yet_to_cut			+= $yet_to_cut;	
								$total_lay_fabric_weight	+= $cutting_vals['lay_fabric_wght'];
								$total_cad_marker_cons		+= $cutting_vals['cad_marker_cons']/12;
								$total_marker_qty			+= $cutting_vals['marker_qty'];
								$total_qc_pass_qty			+= $qc_qty_arr[$color_ids][$order_ids]['qc_pass_qty'];
								$total_replace_qty			+= $qc_qty_arr[$color_ids][$order_ids]['replace_qty'];
								$total_reject_qty			+= $qc_qty_arr[$color_ids][$order_ids]['reject_qty'];
								$total_cut_cons_qty			+= $net_cons_per_pcs;
								$total_qc_pass_cons_qty		+= $qc_pass_cons_qty;
								$total_cons_variation_qty	+= $cons_variation_qty;
								$total_cons_variation_percn	+= $cons_variation_percn;
								$total_reject_kg			+= $reject_kg;
								$total_reject_percn			+= $reject_percn;

								$color_check_arr[]=$color_ids;

							}
							if(!in_array($color_ids,$color_subtot_arr))
							{
								?>
								<tr bgcolor="yellow"> 
									<td colspan="11" align="right"><strong>Color Qty Total=</strong></td>
									<td align="right"><p><strong><?php echo number_format($total_fini_req_qty, 2); ?></strong></p></td><!--Fini. Req. Qty.-->
									<td align="right"><p><strong><?php echo number_format($total_fini_rcv_qty, 2); ?></strong></p></td><!--Fini. Rcvd. Qty.-->
									<td align="right"><p><strong><?php echo $total_rmg_color_qty; ?></strong></p></td><!--RMG Color Qty-->
									<td align="right"><p><strong><?php echo $total_plan_cut_qty; ?></strong></p></td><!--Plan Cut Qty (Color)-->
									<td align="right"><p><strong><?php echo $total_yet_to_cut; ?></strong></p></td><!--Yet To Cut-->
									<td align="right"><p><strong><?php echo number_format($total_lay_fabric_weight, 2); ?></strong></p></td><!--Lay Fabric Weight (Kg)-->
									<td align="right"><p><strong><?php echo number_format($total_cad_marker_cons, 4); ?></strong></p></td><!--CAD Marker Cons/Pcs-->
									<td align="right"><p><strong><?php echo $total_marker_qty; ?></strong></p></td><!--Marker Qty.-->
									<td align="right"><p><strong><?php echo $total_qc_pass_qty; ?></strong></p></td><!--QC Pass Qty.-->
									<td align="right"><p><strong><?php echo $total_replace_qty; ?></strong></p></td><!--Replace Qty.-->
									<td align="right"><p><strong><?php echo $total_reject_qty; ?></strong></p></td><!--Reject Qty.-->
									<td align="right"><p><strong><?php echo number_format($total_cut_cons_qty, 4); ?></strong></p></td><!--Net Cons. Per Pcs-->
									<td align="right"><p><strong><?php echo number_format($total_qc_pass_cons_qty, 4); ?></strong></p></td><!--QC Pass Cons. Qty.-->
									<td align="right"><p><strong><?php echo number_format($total_cons_variation_qty, 4); ?></strong></p></td><!--Cons. Variation Qty.-->
									<td align="right"><p><strong><?php echo number_format($total_cons_variation_percn, 2); ?></strong></p></td><!--Cons. Variation %-->
									<td align="right"><p><strong><?php echo number_format($total_reject_kg, 2); ?></strong></p></td><!--Reject(Kg)-->
									<td align="right"><p><strong><?php echo number_format($total_reject_percn, 2); ?></strong></p></td><!--Reject(%)-->
								</tr>
								<?php
								$grand_total_fini_req_qty		+= $total_fini_req_qty;
								$grand_total_fini_rcv_qty		+= $total_fini_rcv_qty;
								$grand_total_rmg_color_qty		+= $total_rmg_color_qty;
								$grand_total_plan_cut_qty		+= $total_plan_cut_qty;
								$grand_total_yet_to_cut			+= $total_yet_to_cut;
								$grand_total_lay_fabric_weight	+= $total_lay_fabric_weight;
								$grand_total_cad_marker_cons	+= $total_cad_marker_cons;
								$grand_total_marker_qty			+= $total_marker_qty;
								$grand_total_qc_pass_qty		+= $total_qc_pass_qty;
								$grand_total_replace_qty		+= $total_replace_qty;
								$grand_total_reject_qty			+= $total_reject_qty;
								$grand_total_cut_cons_qty		+= $total_cut_cons_qty;
								$grand_total_qc_pass_cons_qty	+= $total_qc_pass_cons_qty;
								$grand_total_cons_variation_qty	+= $total_cons_variation_qty;
								$grand_total_cons_variation_percn+= $total_cons_variation_percn;
								$grand_total_reject_kg			+= $total_reject_kg;
								$grand_total_reject_percn		+= $total_reject_percn;
							}
							$color_subtot_arr[]=$color_ids;
							unset($color_check_arr);
						}
						
					}
				}
				?>
                <tr bgcolor="yellow"> 
                    <td colspan="11" align="right"><strong>Job Total=</strong></td>
                    <td align="right"><p><strong><?php echo number_format($grand_total_fini_req_qty, 2); ?></strong></p></td><!--Fini. Req. Qty.-->
                    <td align="right"><p><strong><?php echo number_format($grand_total_fini_rcv_qty, 2); ?></strong></p></td><!--Fini. Rcvd. Qty.-->
                    <td align="right"><p><strong><?php echo $grand_total_rmg_color_qty; ?></strong></p></td><!--RMG Color Qty-->
                    <td align="right"><p><strong><?php echo $grand_total_plan_cut_qty; ?></strong></p></td><!--Plan Cut Qty (Color)-->
                    <td align="right"><p><strong><?php echo $grand_total_yet_to_cut; ?></strong></p></td><!--Yet To Cut-->
                    <td align="right"><p><strong><?php echo number_format($grand_total_lay_fabric_weight, 2); ?></strong></p></td><!--Lay Fabric Weight (Kg)-->
                    <td align="right"><p><strong><?php echo number_format($grand_total_cad_marker_cons, 4); ?></strong></p></td><!--CAD Marker Cons/Pcs-->
                    <td align="right"><p><strong><?php echo $grand_total_marker_qty; ?></strong></p></td><!--Marker Qty.-->
                    <td align="right"><p><strong><?php echo $grand_total_qc_pass_qty; ?></strong></p></td><!--QC Pass Qty.-->
                    <td align="right"><p><strong><?php echo $grand_total_replace_qty; ?></strong></p></td><!--Replace Qty.-->
                    <td align="right"><p><strong><?php echo $grand_total_reject_qty; ?></strong></p></td><!--Reject Qty.-->
                    <td align="right"><p><strong><?php echo number_format($grand_total_cut_cons_qty, 4); ?></strong></p></td><!--Net Cons. Per Pcs-->
                    <td align="right"><p><strong><?php echo number_format($grand_total_qc_pass_cons_qty, 4); ?></strong></p></td><!--QC Pass Cons. Qty.-->
                    <td align="right"><p><strong><?php echo number_format($grand_total_cons_variation_qty, 4); ?></strong></p></td><!--Cons. Variation Qty.-->
                    <td align="right"><p><strong><?php echo number_format($grand_total_cons_variation_percn, 2); ?></strong></p></td><!--Cons. Variation %-->
                    <td align="right"><p><strong><?php echo number_format($grand_total_reject_kg, 2); ?></strong></p></td><!--Reject(Kg)-->
                    <td align="right"><p><strong><?php echo number_format($grand_total_reject_percn, 2); ?></strong></p></td><!--Reject(%)-->
                </tr>
            </tbody>
        </table>
    </div>
	<?php
	foreach (glob("*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$report_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_excel,$report_data);
	echo $report_data."####".$name;
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');

	extract($_REQUEST);
	?>
	<script>
	
		function js_set_value(str)
		{
			$("#hide_job_no").val(str); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th>
                        <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"> 					
                        <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    </th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_job_no_search_list_view', 'search_div', 'cutting_lay_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	//$year_id=$data[4];
	//$month_id=$data[5];
	//echo $month_id;
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond order by job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','','') ;
	exit(); 
} // Job Search end

if($action=="cutting_number_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_cutting_value(strCon ) 
		{
			document.getElementById('hdn_cut_no').value=strCon;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%; overflow-y:hidden;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="950" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="140">Company name</th>
                    <th width="130">Cutting No</th>
                    <th width="130">Job No</th>
                    <th width="130">Order No</th>
                    <th width="250">Date Range</th>
                    <th width="120"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                  <tr>                    
                        <td>
                              <? 
                              echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 0, "-- Select Company --",$company_id, "",1);
                             ?>
                        </td>
                      
                        <td align="center" >
                                <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:120px"  class="text_boxes"/>
                                <input type="hidden" id="hdn_cut_no" name="hdn_cut_no" />
                        </td>
                        <td align="center">
                               <input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:120px"  />
                        </td>
                        <td align="center">
                               <input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"  />
                        </td>
                        <td align="center" width="250">
                               <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
                               <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
                        </td>
                        <td align="center">
                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value, 'create_cutting_search_list_view', 'search_div', 'cutting_lay_production_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                        </td>
                 </tr>
        		 <tr>                  
                        <td align="center" height="40" valign="middle" colspan="6">
                            <? echo load_month_buttons(1);  ?>
                        </td>
                </tr>   
            </tbody>
         </tr>         
      </table> 
     <div align="center" valign="top" id="search_div"> </div>  
  </form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_cutting_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];
    if($db_type==2) { $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year ";}
    if($db_type==0) {$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year ";}
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and a.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  $year_cond";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond="and c.po_number like '%".trim($order_no)."%' ";
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
	       {
			      $sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		   }
	  if($db_type==2)
	       {
			      $sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		   }
	}
	
	$sql_order="select a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,d.order_id,d.color_id,$year FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d where  a.id=d.mst_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and a.entry_form=76 and c.id=d.order_id $conpany_cond  $cut_cond $job_cond $sql_cond $order_cond order by id";
	$arr=array(2=>$table_arr,4=>$order_no_library,5=>$color_library);
	echo create_list_view("list_view", "Cut No,Year,Table No,Job No,Order NO,Color,Marker Length,Markar Width,Fabric Width,Entry Date","90,50,60,120,120,100,80,80,80,120","950","270",0, $sql_order , "js_set_cutting_value", "cut_num_prefix_no", "", 1, "0,0,table_no,0,order_id,color_id,0,0,0,0,0", $arr, "cut_num_prefix_no,year,table_no,job_no,order_id,color_id,marker_length,marker_width,fabric_width,entry_date", "","setFilterGrid('list_view',-1)") ;
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		function js_set_value( str ) 
		{
			$('#hide_order_no').val(str);
			parent.emailwindow.hide();
		}
    </script>
</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th>
                        <input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;">
                        <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    </th> 
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
					>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'cutting_lay_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit(); 
}

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	
	if($data[1]==0)
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
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==1) 
		$search_field="b.po_number"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no";
		
	$start_date =$data[4];
	$end_date =$data[5];	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3') ;
   exit(); 
}
?>