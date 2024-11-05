<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.others.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");  
	exit();
}


if($action=="report_generate")
{
	?>
	<style type="text/css">
		/*.block_div { 
				width:auto;
				height:auto;
				text-wrap:normal;
				vertical-align:bottom;
				display: block;
				position: !important; 
				-webkit-transform: rotate(-90deg);
				-moz-transform: rotate(-90deg);
		}*/
		table tr th,table tr td
		{
			word-wrap: break-word;
			word-break: break-all;
		}
	  
   </style> 
	<?
	extract($_REQUEST);
	$process = array( &$_POST );
	
	//change_date_format($txt_date);die;
	
	
	
	extract(check_magic_quote_gpc( $process ));
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer","id","buyer_name"); 
	$color_name_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name'); 
	$company_id=str_replace("'","",$cbo_company_id);
	$hidd_job_id=str_replace("'","",$hidd_job_id);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_wo_no=str_replace("'","",$txt_wo_no);
	$hidd_wo_id=str_replace("'","",$hidd_wo_id);
	$txt_fso_no=str_replace("'","",$txt_fso_no);
	$hide_fso_id=str_replace("'","",$hide_fso_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	if($db_type==2)
	{
		$txt_date_from=change_date_format($txt_date_from,'','',1);
		$txt_date_to=change_date_format($txt_date_to,'','',1);
	}	

	$sql_cond ="";
	$sql_cond2 ="";
	if(!empty($company_id))
	{
		$sql_cond .=" and  a.company_name = $company_id ";
		$sql_cond2 .=" and  a.company_id = $company_id ";
	}
	$sys_no = '';
	if(!empty($txt_style_ref))
	{
		$sql_cond .=" and a.style_ref_no like '%".$txt_style_ref."%'";
		$sql_cond2 .=" and a.style_ref_no like '%".$txt_style_ref."%'";
		$sys_no = 1;
	}
	if(!empty($hidd_job_id))
	{
		$sql_cond .=" and a.id in('".$hidd_job_id."')";
		$sql_cond2 .=" and c.job_id in('".$hidd_job_id."')";
		$sys_no = 1;
	}
	else if(!empty($txt_job_no))
	{
		$sql_cond .=" and a.job_no like '%".$txt_job_no."%'";
		$sql_cond2 .=" and c.job_no like '%".$txt_job_no."%'";
		$sys_no = 1;
	}

	if(!empty($hidd_wo_id))
	{
		$sql_cond .=" and b.booking_mst_id in('".$hidd_wo_id."')";
		$sql_cond2 .=" and b.booking_mst_id in('".$hidd_wo_id."')";
		$sys_no = 1;
	}
	else if(!empty($txt_wo_no))
	{
		$sql_cond .=" and b.booking_no like '%".$txt_wo_no."%'";
		$sql_cond2 .=" and b.booking_no like '%".$txt_wo_no."%'";
		$sys_no = 1;
	}

	if(!empty($hide_fso_id))
	{
		$sql_cond2 .=" and a.id in('".$hide_fso_id."')";
		$sys_no = 1;
	}
	else if(!empty($txt_fso_no))
	{
		$sql_cond2 .=" and a.job_no like '%".$txt_fso_no."%'";
		$sys_no = 1;
	}

	if(!empty($txt_date_from) && !empty($txt_date_to) && empty($sys_no))
	{
		$sql_cond .=" and a.insert_date between '".$txt_date_from."' and '".$txt_date_to."' ";
		$sql_cond2 .=" and a.insert_date between '".$txt_date_from."' and '".$txt_date_to."' ";
	}
	
	
	
	$sql_stripe="SELECT c.id,c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,sum(b.grey_fab_qnty) as grey_qty,sum(b.fin_fab_qnty) as fab_qty,b.dia_width,d.color_number_id as color_number_id,d.id as did,d.uom,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed,b.po_break_down_id,d.item_number_id,b.job_no,a.style_ref_no  from wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d,wo_po_details_master a where c.id=b.pre_cost_fabric_cost_dtls_id and c.job_no=b.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no=d.job_no and a.job_no=b.job_no and a.job_no=d.job_no  and d.color_number_id=b.gmts_color_id  and c.color_type_id in (2,6) and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1  and 	b.is_deleted=0 $sql_cond  group by c.id,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,d.color_number_id,d.uom,d.id,d.stripe_color,d.yarn_dyed,d.fabreqtotkg ,d.measurement,c.composition,c.construction,b.dia_width,b.po_break_down_id,d.item_number_id,b.job_no,a.style_ref_no order by c.id,d.id ";
        //echo $sql_stripe;
       $result_data=sql_select($sql_stripe);
       $poIdArr = array();
		foreach($result_data as $row)
		{
			$stripe_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['stripe_color'][$row[csf('did')]]=$row[csf('stripe_color')];
			$stripe_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['measurement'][$row[csf('did')]]=$row[csf('measurement')];
			$stripe_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg'][$row[csf('did')]]+=$row[csf('grey_qty')];
			$stripe_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['subtotal_measurement'][$row[csf('did')]]=$row[csf('measurement')];;

			$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['yarn_dyed'][$row[csf('did')]]=$row[csf('yarn_dyed')];

			$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['composition']=$row[csf('composition')];
			$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['construction']=$row[csf('construction')];
			$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
			$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom']=$row[csf('uom')];
			$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['color_type_id']=$row[csf('color_type_id')];
			$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
			$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg']=$row[csf('grey_qty')];
			$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabric_description']=$row[csf('fabric_description')];
			$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['item_number_id']=$row[csf('item_number_id')];
			$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no')];
			$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$poIdArr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
		}
		
		
		$pospanArr = array();
		$bodyspanArr = array();
		$colorspanArr = array();
		foreach($stripe_arr as $po_id=>$po_data)
		{
			$p_sp = 0;
			foreach($po_data as $body_id=>$body_data)
			{
				$b_sp = 0;
				foreach($body_data as $color_id=>$color_val)
				{
					$c_sp = 0;
					foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
					{
						$color_strip_color_qty_arr[$po_id][$body_id][$color_id]=$color_val['fabreqtotkg'][$strip_color_id];
						$p_sp++;
						$b_sp++;
						$c_sp++;
					}
					$colorspanArr[$po_id][$body_id][$color_id] = $c_sp;
				}
				$bodyspanArr[$po_id][$body_id] = $b_sp;
			}
			$pospanArr[$po_id] = $p_sp;
		}

		$sql_stripe2="SELECT c.id,c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,sum(b.grey_fab_qnty) as grey_qty,sum(b.fin_fab_qnty) as fab_qty,b.dia_width,d.color_number_id as color_number_id,d.id as did,d.uom,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed,b.po_break_down_id,d.item_number_id,b.job_no,a.style_ref_no,b.booking_no,a.job_no as fso_no  from wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d,fabric_sales_order_mst a where  c.id = b.pre_cost_fabric_cost_dtls_id   AND c.job_no = b.job_no   AND d.pre_cost_fabric_cost_dtls_id = c.id   AND d.pre_cost_fabric_cost_dtls_id = b.pre_cost_fabric_cost_dtls_id   AND a.po_job_no = b.job_no   AND a.job_no = d.job_no   AND d.color_number_id = b.gmts_color_id   AND c.color_type_id IN (2, 6)   AND b.status_active = 1   AND c.is_deleted = 0   AND c.status_active = 1   AND d.is_deleted = 0   AND d.status_active = 1   AND b.is_deleted = 0   AND a.is_deleted = 0 $sql_cond2  group by c.id,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,d.color_number_id,d.uom,d.id,d.stripe_color,d.yarn_dyed,d.fabreqtotkg ,d.measurement,c.composition,c.construction,b.dia_width,b.po_break_down_id,d.item_number_id,b.job_no,a.style_ref_no,b.booking_no,a.job_no order by c.id,d.id ";
			//echo $sql_stripe;
		$result_data2=sql_select($sql_stripe2);
		foreach($result_data2 as $row)
		{
			$poIdArr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
		}
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (80) and ENTRY_FORM=880");
		oci_commit($con);
		disconnect($con);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 8880, 80, $poIdArr, $empty_arr);//PO ID
		$po_name_arr=return_library_array( "select a.id,a.po_number from wo_po_break_down a,gbl_temp_engine b where a.id=b.ref_val and b.entry_form=8880 and b.ref_from=80 and b.user_id = ".$user_id." and a.status_active=1 and a.is_deleted=0",'id','po_number');
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (80) and ENTRY_FORM=880");
		oci_commit($con);
		disconnect($con);
		
		$width = 35 + 120*8 + 80*4 + 170;
		ob_start();
	?>
	<fieldset style="width:<?=$width+40+240;?>px;">
		<div style="width:<?=$width+30;?>px; padding: 5px 10px">
			
			<table class="rpt_table" width="<?=$width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" style="padding-top: 20px">
				<caption>Stripe Details from Budget</caption>
				<thead>

					<tr>
						<th width="35">SL</th>
						<th width="120">Job Number</th>
						<th width="120">Style Number</th>
						<th width="120">PO Number</th>
						<th width="120">Garments Item</th>
						<th width="120">Body Part</th>
						<th width="220">Fab. Desc.</th>
						<th width="120">Gmts Color</th>
						<th width="80">Fabric Qty</th>
						<th width="70">Color Sequence</th>
						<th width="120">Stripe Color</th>
						<th width="80">Stripe Measurement</th>
						<th width="80">Uom</th>
		                <th width="80">Qty.(KG)</th>
					</tr>
				</thead>
            </table>
            <div style="width:<?=$width+20;?>px; overflow-y: scroll; max-height:380px;" id="scroll_body1">
				<table width="<?=$width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body1">
					<tbody>
						<?
		                $i = 1;
		                $sl = 1;
		                $tot_tot_plan_cut_qnty=0;
						
						foreach($stripe_arr as $po_id=>$po_data)
						{
							$po_span = $pospanArr[$po_id];
							$po_inc = 0;
							foreach($po_data as $body_id=>$body_data)
							{
								$body_span = $bodyspanArr[$po_id][$body_id];
								$body_inc = 0;
								foreach($body_data as $color_id=>$color_val)
								{
									$gmt_color_span = $colorspanArr[$po_id][$body_id][$color_id];
									$gmt_color_inc = 0;
									$job_no=$stripe_arr2[$po_id][$body_id][$color_id]['job_no'];
									$style_ref_no=$stripe_arr2[$po_id][$body_id][$color_id]['style_ref_no'];
									$composition=$stripe_arr2[$po_id][$body_id][$color_id]['composition'];
									$construction=$stripe_arr2[$po_id][$body_id][$color_id]['construction'];
									$gsm_weight=$stripe_arr2[$po_id][$body_id][$color_id]['gsm_weight'];
									$color_type_id=$stripe_arr2[$po_id][$body_id][$color_id]['color_type_id'];
									$uom_id=$stripe_arr2[$po_id][$body_id][$color_id]['uom'];
									$item_number_id=$stripe_arr2[$po_id][$body_id][$color_id]['item_number_id'];
									$dia_width=$stripe_arr2[$po_id][$body_id][$color_id]['dia_width'];
									$subtotal_measurement=array_sum($color_val['subtotal_measurement']);					
									$color_qty=$color_strip_color_qty_arr[$po_id][$body_id][$color_id];
									$fabric_des=$stripe_arr2[$po_id][$body_id][$color_id]['fabric_description'];
									$qnty=$color_qty/$subtotal_measurement;
									$total_color_qty+=$color_qty;
									$subtotal_measurement=array_sum($color_val['subtotal_measurement']);
									$color_seq = 1 ;
									foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
									{
										$measurement=$color_val['measurement'][$strip_color_id];
										$fabreqtotkg=$color_val['fabreqtotkg'][$strip_color_id];
										$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
										$qnty_kg=$qnty*$measurement;

										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
										<tr bgcolor="<?=$bgcolor?>">
											<td width="35" ><?=$i;?></td>
											
											<?php if ($po_inc == 0): ?>
												<td width="120" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$po_span;?>"><?=$job_no;?></td>
												<td width="120" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$po_span;?>"><?=$style_ref_no;?></td>
												<td width="120" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$po_span;?>"><?=$po_name_arr[$po_id];?></td>
											<?php endif ?>
											<?php if ($body_inc == 0): ?>
												<td width="120" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$body_span;?>"><?=$garments_item[$item_number_id];?></td>
												<td width="120" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$body_span;?>"><?=$body_part[$body_id];?></td>
												<td width="220" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$body_span;?>"><?=$fabric_des;?></td>
											<?php endif ?>
											<?php if ($gmt_color_inc == 0): ?>
												
												<td width="120" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$gmt_color_span;?>"><?=$color_name_arr[$color_id];?></td>
												<td width="80" style="justify-content: center;text-align: right;vertical-align: middle;" rowspan="<?=$gmt_color_span;?>"><?=fn_number_format($color_qty,2);?></td>
											<?php endif ?>
											
											<td align="right" width="70">
												<?=$color_seq++;?>
											</td>
											<td style="justify-content: center;text-align: center;vertical-align: middle;" width="120">
												<?=$color_name_arr[$s_color_val];?>
											</td>
											<td align="right" width="80">
												<?=fn_number_format($measurement,2);?>
											</td>
											<td style="justify-content: center;text-align: center;vertical-align: middle;" align="right" width="80">
												<?=$unit_of_measurement[$uom_id];?>
											</td>
											<td align="right" width="80">
												<?=fn_number_format($qnty_kg,2);?>
											</td>
										</tr>
										<?
										$i++;
										$po_inc++;
										$gmt_color_inc++;
										$body_inc++;
										$total_color_qty_kg+=$qnty_kg;
									}
								}
							}
						}
						
		                ?>
					</tbody>
					<tfoot>
						<td align="right" colspan="8">Total</td>
						<td align="right"><?=fn_number_format($total_color_qty,2);?></td>
						<td colspan="4"></td>
						<td align="right"><?=fn_number_format($total_color_qty_kg,2);?></td>
					</tfoot>
				</table>
			</div>
			<? 
			
			$stripe_arr = array();
			$stripe_arr2 = array();
			$strip_id_arr = array();
			foreach($result_data2 as $row)
			{
				$stripe_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['stripe_color'][$row[csf('did')]]=$row[csf('stripe_color')];
				$stripe_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['measurement'][$row[csf('did')]]=$row[csf('measurement')];
				$stripe_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg'][$row[csf('did')]]+=$row[csf('grey_qty')];
				$stripe_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['subtotal_measurement'][$row[csf('did')]]=$row[csf('measurement')];;

				$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['yarn_dyed'][$row[csf('did')]]=$row[csf('yarn_dyed')];

				$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['composition']=$row[csf('composition')];
				$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['construction']=$row[csf('construction')];
				$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
				$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom']=$row[csf('uom')];
				$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['color_type_id']=$row[csf('color_type_id')];
				$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
				$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg']=$row[csf('grey_qty')];
				$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabric_description']=$row[csf('fabric_description')];
				$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['item_number_id']=$row[csf('item_number_id')];
				$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no')];
				$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['booking_no']=$row[csf('booking_no')];
				$stripe_arr2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fso_no']=$row[csf('fso_no')];
				$strip_id_arr[$row[csf('did')]]=$row[csf('did')];
			}
			
			$pospanArr = array();
			$bodyspanArr = array();
			$colorspanArr = array();
			$color_strip_color_qty_arr = array();
			foreach($stripe_arr as $po_id=>$po_data)
			{
				$p_sp = 0;
				foreach($po_data as $body_id=>$body_data)
				{
					$b_sp = 0;
					foreach($body_data as $color_id=>$color_val)
					{
						$c_sp = 0;
						foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
						{
							$color_strip_color_qty_arr[$po_id][$body_id][$color_id]=$color_val['fabreqtotkg'][$strip_color_id];
							$p_sp++;
							$b_sp++;
							$c_sp++;
						}
						$colorspanArr[$po_id][$body_id][$color_id] = $c_sp;
					}
					$bodyspanArr[$po_id][$body_id] = $b_sp;
				}
				$pospanArr[$po_id] = $p_sp;
			}
			$width = 35 + 120*8 + 80*4 + 170 + 240;
			 ?>
		    <table class="rpt_table" width="<?=$width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" style="padding-top: 20px">
				<caption>Stripe Details from FSO</caption>
				<thead>

					<tr>
						<th width="35">SL</th>
						<th width="120">Fabric Booking No.</th>
						<th width="120">FSO No.</th>
						<th width="120">Job Number</th>
						<th width="120">Style Number</th>
						<th width="120">PO Number</th>
						<th width="120">Garments Item</th>
						<th width="120">Body Part</th>
						<th width="220">Fab. Desc.</th>
						<th width="120">Gmts Color</th>
						<th width="80">Fabric Qty</th>
						<th width="70">Color Sequence</th>
						<th width="120">Stripe Color</th>
						<th width="80">Stripe Measurement</th>
						<th width="80">Uom</th>
		                <th width="80">Qty.(KG)</th>
					</tr>
				</thead>
            </table>
            <div style="width:<?=$width+20;?>px; overflow-y: scroll; max-height:380px;" id="scroll_body2">
				<table width="<?=$width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body2">
					<tbody>
						<?
		                $i = 1;
		                $sl = 1;
		                $total_color_qty=0;
		                $total_color_qty_kg=0;
						
						foreach($stripe_arr as $po_id=>$po_data)
						{
							$po_span = $pospanArr[$po_id];
							$po_inc = 0;
							foreach($po_data as $body_id=>$body_data)
							{
								$body_span = $bodyspanArr[$po_id][$body_id];
								$body_inc = 0;
								foreach($body_data as $color_id=>$color_val)
								{
									$gmt_color_span = $colorspanArr[$po_id][$body_id][$color_id];
									$gmt_color_inc = 0;
									$job_no=$stripe_arr2[$po_id][$body_id][$color_id]['job_no'];
									$style_ref_no=$stripe_arr2[$po_id][$body_id][$color_id]['style_ref_no'];
									$fso_no=$stripe_arr2[$po_id][$body_id][$color_id]['fso_no'];
									$booking_no=$stripe_arr2[$po_id][$body_id][$color_id]['booking_no'];
									$composition=$stripe_arr2[$po_id][$body_id][$color_id]['composition'];
									$construction=$stripe_arr2[$po_id][$body_id][$color_id]['construction'];
									$gsm_weight=$stripe_arr2[$po_id][$body_id][$color_id]['gsm_weight'];
									$color_type_id=$stripe_arr2[$po_id][$body_id][$color_id]['color_type_id'];
									$uom_id=$stripe_arr2[$po_id][$body_id][$color_id]['uom'];
									$item_number_id=$stripe_arr2[$po_id][$body_id][$color_id]['item_number_id'];
									$dia_width=$stripe_arr2[$po_id][$body_id][$color_id]['dia_width'];
									$subtotal_measurement=array_sum($color_val['subtotal_measurement']);					
									$color_qty=$color_strip_color_qty_arr[$po_id][$body_id][$color_id];
									$fabric_des=$stripe_arr2[$po_id][$body_id][$color_id]['fabric_description'];
									$qnty=$color_qty/$subtotal_measurement;
									$total_color_qty+=$color_qty;
									$subtotal_measurement=array_sum($color_val['subtotal_measurement']);
									$color_seq = 1 ;
									foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
									{
										$measurement=$color_val['measurement'][$strip_color_id];
										$fabreqtotkg=$color_val['fabreqtotkg'][$strip_color_id];
										$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
										$qnty_kg=$qnty*$measurement;

										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
										<tr bgcolor="<?=$bgcolor?>">
											<td width="35" ><?=$i;?></td>
											
											<?php if ($po_inc == 0): ?>
												<td width="120" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$po_span;?>"><?=$booking_no;?></td>
												<td width="120" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$po_span;?>"><?=$fso_no;?></td>
												<td width="120" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$po_span;?>"><?=$job_no;?></td>
												<td width="120" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$po_span;?>"><?=$style_ref_no;?></td>
												<td width="120" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$po_span;?>"><?=$po_name_arr[$po_id];?></td>
											<?php endif ?>
											<?php if ($body_inc == 0): ?>
												<td width="120" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$body_span;?>"><?=$garments_item[$item_number_id];?></td>
												<td width="120" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$body_span;?>"><?=$body_part[$body_id];?></td>
												<td width="220" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$body_span;?>"><?=$fabric_des;?></td>
											<?php endif ?>
											<?php if ($gmt_color_inc == 0): ?>
												
												<td width="120" style="justify-content: center;text-align: center;vertical-align: middle;" rowspan="<?=$gmt_color_span;?>"><?=$color_name_arr[$color_id];?></td>
												<td width="80" style="justify-content: center;text-align: right;vertical-align: middle;" rowspan="<?=$gmt_color_span;?>"><?=fn_number_format($color_qty,2);?></td>
											<?php endif ?>
											
											<td align="right" width="70">
												<?=$color_seq++;?>
											</td>
											<td style="justify-content: center;text-align: center;vertical-align: middle;" width="120">
												<?=$color_name_arr[$s_color_val];?>
											</td>
											<td align="right" width="80">
												<?=fn_number_format($measurement,2);?>
											</td>
											<td style="justify-content: center;text-align: center;vertical-align: middle;" align="right" width="80">
												<?=$unit_of_measurement[$uom_id];?>
											</td>
											<td align="right" width="80">
												<?=fn_number_format($qnty_kg,2);?>
											</td>
										</tr>
										<?
										$i++;
										$po_inc++;
										$gmt_color_inc++;
										$body_inc++;
										$total_color_qty_kg+=$qnty_kg;
									}
								}
							}
						}
						
		                ?>
					</tbody>
					<tfoot>
						<td align="right" colspan="10">Total</td>
						<td align="right"><?=fn_number_format($total_color_qty,2);?></td>
						<td colspan="4"></td>
						<td align="right"><?=fn_number_format($total_color_qty_kg,2);?></td>
					</tfoot>
				</table>
			</div>        
		</div>
	</fieldset>
	<script type="text/javascript">
		setFilterGrid("table_body1",-1);
		setFilterGrid("table_body2",-1);
	</script>
	<?    
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	disconnect($con);
	exit();      
}

if ($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
	?>
    <script>
	function js_set_value( job_id )
	{
		//alert(po_id)
		document.getElementById('txt_job_id').value=job_id;
		parent.emailwindow.hide();
	}

	</script>
     <input type="hidden" id="txt_job_id" />
 	<?
	if ($data[0]==0) $company_id=""; else $company_id=" and a.company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$data[1]";
	//if ($data[2]==0) $year_id=""; else $year_id=" and buyer_name=$data[2]";
	if($db_type==0)
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$data[2]).""; else $year_cond="";
	}
	else
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$data[2]).""; else $year_cond="";
	}

	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");
	 $order_type=str_replace("'","",$data[3]);
	if($order_type==1)
	{
		 $sql= "select a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 $company_id $buyer_id $year_cond group by a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader order by a.id DESC ";
	}
	else
	{
		 $sql= "select a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader from wo_po_details_master a, wo_po_break_down  b where  b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 $company_id $buyer_id $year_cond group by a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader order by a.id DESC";
	}
	//echo $sql;die;

	$arr=array(2=>$product_dept,3=>$marchentrArr,4=>$teamMemberArr);
	echo  create_list_view("list_view", "Job No,Style Ref.,Prod. Dept.,Marchant,Team Name", "100,110,110,150,150","680","360",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0,0,product_dept,dealing_marchant,team_leader", $arr , "job_no_prefix_num,style_ref_no,product_dept,dealing_marchant,team_leader", "",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	exit();
}

if($action=="wo_no_popup")
{
  	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	$ex_data=explode('_',$data);
	$company_id=$ex_data[0];
 	?>
	<script>
		function js_set_value(wo_id,wo_no)
		{
			document.getElementById('txt_wo_no').value=wo_no;
			document.getElementById('txt_wo_id').value=wo_id;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<fieldset style="width:620px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="620" class="rpt_table">
	                <thead>
	                    <th>Buyer</th>
	                    <th>Search</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                        <input type="hidden" name="txt_wo_no" id="txt_wo_no" value="" style="width:50px">
	                        <input type="hidden" name="txt_wo_id" id="txt_wo_id" value="" style="width:50px">
	                    </th> 
	                </thead>
	                <tr class="general">
	                    <td align="center">
	                        <?
								echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--","", "" );   	 
	                        ?>       
	                    </td>
	                    <td align="center">				
	                        <input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
	                    </td> 						
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_id').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_search_common').value, 'create_wo_search_list_view', 'search_div', 'stripe_measurement_report_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	            </table>
	            <div id="search_div" style="margin-top:10px"></div>   
	        </form>
	    </fieldset>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action=="create_wo_search_list_view")
{
	$data=explode('_',$data);//var_dump($data);
	
	if ($data[0]==0) $buyer_id=""; else $buyer_id=" and buyer_id=$data[0]";
	if ($data[1]==0) $company_id=""; else $company_id=" and company_id=$data[1]";
	if ($data[2]==0) $search_wo=""; else $search_wo=" and booking_no_prefix_num=$data[2]";
	
	
	
	
	if($db_type==0)
	{
		$year=" YEAR(insert_date) as year";
	}
	elseif($db_type==2)
	{
		$year=" TO_CHAR(insert_date,'YYYY') as year";
	}
	
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	
	$sql= "SELECT id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 0 as type from wo_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $search_wo 
	union all
	SELECT id, booking_no, $year, booking_no_prefix_num, booking_date, buyer_id, booking_type, is_short, 1 as type from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $company_id $buyer_id $search_wo 

	order by id desc
	";
	
	//echo $sql;
 	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
			<thead>
				<th width="30">SL</th>
				<th width="80">WO No </th>
				<th width="80">Year</th>
				<th width="130">WO Type</th>
				<th width="150">Buyer</th>
				<th width="100">WO Date</th>
			</thead>
		</table>
		<div style="width:618px; overflow-y:scroll; max-height:300px;" id="" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view" >
			<?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					if ($selectResult[csf("type")]==0)
					{	
						if ($selectResult[csf("booking_type")]==1 || $selectResult[csf("booking_type")]==2)
						{
							if ($selectResult[csf("is_short")]==1)
							{
								$wo_type="Short";
							}
							else
							{
								$wo_type="Main";
							}
						}
						elseif($selectResult[csf("booking_type")]==4)
						{
							$wo_type="Sample With Order";
						}
					}
					else
					{
						$wo_type="Sample Non Order";
					}					
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('booking_no')]; ?>')"> 
						<td width="30" align="center"><? echo $i; ?></td>	
						<td width="80" align="center"><p><? echo $selectResult[csf('booking_no_prefix_num')]; ?></p></td>
						<td width="80" align="center"><? echo $selectResult[csf('year')]; ?></td>
						<td width="130"><p><? echo $wo_type; ?></p></td>
						<td width="150"><p><? echo $buyerArr[$selectResult[csf('buyer_id')]]; ?></p></td>
						<td  width="100" align="center"><? echo change_date_format($selectResult[csf('booking_date')]); ?></td>	
					</tr>
				<?
					$i++;
				}
			?>
			</table>
		</div>
	</div>           
	<?
	
	
	exit();
}

if ($action == "actn_job_popup")
{
	echo load_html_head_contents("Sales Order No", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		function toggle(x, origColor)
		{
			var newColor = 'yellow';
			if (x.style)
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str)
		{
			toggle(document.getElementById('search' + str), '#FFFFCC');
			if (jQuery.inArray($('#txt_job_id' + str).val(), selected_id) == -1)
			{
				selected_id.push($('#txt_job_id' + str).val());
				selected_name.push($('#txt_job_no' + str).val());
			}
			else
			{
				for (var i = 0; i < selected_id.length; i++)
				{
					if (selected_id[i] == $('#txt_job_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++)
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:440px;">
					<table width="420" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>PO Buyer</th>
							<th>Search By</th>
							<th id="search_by_td_up">Please Enter Sales Order No</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
							<input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
							<input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
						</thead>
						<tbody>
							<tr>
								<td id="buyer_td">
									<?
									echo create_drop_down("cbo_po_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
									?>
								</td>
								<td align="center">
									<?
									$search_by_arr = array(1 => "Sales Order No", 2 => "Style Ref");
									$dd = "change_search_event(this.value, '0*0', '0*0', '../../../') ";
									echo create_drop_down("cbo_search_by", 120, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
									?>
								</td>
								<td align="center" id="search_by_td">
									<input type="text" style="width:110px" class="text_boxes" name="txt_search_common" id="txt_search_common"/>
								</td>
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>****'+document.getElementById('cbo_po_buyer_name').value + '****'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'actn_job_popup_listview', 'search_div', 'stripe_measurement_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:90px;"/>
								</td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:10px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "actn_job_popup_listview")
{
	$data = explode('**', $data);
	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	$company_id = $data[0];
	$buyer_id = $data[1];
	$po_buyer_id = $data[2];
	$within_group = $data[3];
	$search_by = $data[4];
	$search_string = trim($data[5]);

	$search_field_cond = '';
	if ($search_string != "")
	{
		if ($search_by == 1)
		{
			$search_field_cond = " AND C.JOB_NO LIKE '%".$search_string."'";
		}
		else
		{
			$search_field_cond = " AND LOWER(C.STYLE_REF_NO) LIKE LOWER('".$search_string."%')";
		}
	}

	//for po company
	$po_company_cond = '';
	if($within_group == 1 && $buyer_id != 0)
	{
		$po_company_cond = " AND C.PO_COMPANY_ID = ".$buyer_id;
	}
	
	//for po buyer
	$po_buyer_cond = '';
	if ($po_buyer_id == 0)
	{
		if ($_SESSION['logic_erp']["buyer_id"] != "")
		{
			$po_buyer_cond = " AND C.BUYER_ID IN(".$_SESSION['logic_erp']["buyer_id"].")";
		}
	}
	else
	{
		$po_buyer_cond = " AND C.PO_BUYER = ".$po_buyer_id;
	}

	$sql = "SELECT  C.SALES_BOOKING_NO AS BOOKING_NO, C.WITHIN_GROUP, TO_CHAR(C.INSERT_DATE, 'YYYY') AS YEAR, C.ID, C.JOB_NO, C.STYLE_REF_NO, C.BUYER_ID, C.PO_BUYER, C.PO_COMPANY_ID FROM FABRIC_SALES_ORDER_MST C WHERE C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND C.COMPANY_ID = ".$company_id.$search_field_cond.$po_company_cond.$po_buyer_cond ." order by C.ID DESC";
	
	
	//echo $sql; //die;
	$sql_rslt = sql_select($sql);
	$data_arr = array();
	foreach($sql_rslt as $row)
	{
		$data_arr[$row['JOB_NO']]['ID'] = $row['ID'];
		$data_arr[$row['JOB_NO']]['YEAR'] = $row['YEAR'];
		$data_arr[$row['JOB_NO']]['WITHIN_GROUP'] = $yes_no[$row['WITHIN_GROUP']];
		$data_arr[$row['JOB_NO']]['BOOKING_NO'] = $row['BOOKING_NO'];
		$data_arr[$row['JOB_NO']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
		
		//for buyer
		if ($row['WITHIN_GROUP'] == 1)
		{
			$po_buyer = $buyer_arr[$row['PO_BUYER']];
			$po_company = $company_arr[$row['PO_COMPANY_ID']];
		}
		else
		{
			$po_buyer = $buyer_arr[$row['BUYER_ID']];
			$po_company = $buyer_arr[$row['BUYER_ID']];
		}
		$data_arr[$row['JOB_NO']]['PO_BUYER'] = $po_buyer;
		$data_arr[$row['JOB_NO']]['PO_COMPANY'] = $po_company;
		
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="120">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">PO Buyer</th>
			<th width="70">PO Company</th>
			<th width="120">Booking No</th>
			<th>Style Ref.</th>
		</thead>
	</table>
	<div style="width:700px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="680" class="rpt_table" id="tbl_list_search">
			<?
			$i = 0;
			foreach ($data_arr as $job_no=>$row)
			{
				$i++;
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
					<td width="40"><? echo $i; ?>
	                    <input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>" value="<? echo $row['ID']; ?>"/>
	                    <input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $job_no; ?>"/>
					</td>
					<td width="120" align="center"><p><? echo $job_no; ?></p></td>
					<td width="60" align="center"><p><? echo $row['YEAR']; ?></p></td>
					<td width="80" align="center"><p><? echo $row['WITHIN_GROUP']; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $row['PO_BUYER']; ?>&nbsp;</p></td>
					<td width="70" align="center"><p><? echo $row['PO_COMPANY']; ?>&nbsp;</p></td>
					<td width="120" align="center"><p><? echo $row['BOOKING_NO']; ?></p></td>
					<td><p><? echo $row['STYLE_REF_NO']; ?></p></td>
				</tr>
				<?
	        }
	        ?>
	    </table>
	</div>

	<?
	exit();
}
?>