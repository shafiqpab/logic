<?php

require_once('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
require_once('../setting/mail_setting.php');

$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer where status_active=1",'id','short_name');
 $company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );


$team_name_library=return_library_array( "select id,team_name from lib_marketing_team", "id", "team_name"  );
$time = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();

$current_date = change_date_format(date("Y-m-d",strtotime(add_time(date("H:i:s",$time),0))),'','',1);
$previous_date = change_date_format(date('Y-m-d', strtotime('-1 day', $time)),'','',1);
		


function diff_in_weeks_and_days($from, $to,$outputType) {
	$day   = 24 * 3600;
	$from  = strtotime($from);
	$to    = strtotime($to) + $day;
	$diff  = abs($to - $from);
	$weeks = floor($diff / $day / 7);
	$days  = $diff / $day - $weeks * 7;
	if($outputType=="week"){
		$out   = array();
		if ($weeks) $out[] = "$weeks Week" . ($weeks > 1 ? 's' : '');
		if ($days)  $out[] = "$days Day" . ($days > 1 ? 's' : '');
		return implode(', ', $out);
	}else{
		$totdays=$diff / $day ;
		return $totdays;
	}
}


ob_start();
?>

<div style="width:1120px" >
<fieldset style="width:100%; margin-left: 200px;">
<table width="1100">
	<tr class="form_caption">
		<td colspan="8" align="center">Daily Order Update History Report</td>
	</tr>
	<tr class="form_caption">
		<td colspan="8" align="center">Date:<?= $previous_date;?></td>
	</tr>
</table>

<table class="rpt_table" width="1100" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
		<th width="40">SL</th>
		<th width="65">Edit No</th>
		<th width="65">Company</th>
		<th width="100">Team</th>
		<th width="50">Buyer</th>
		<th width="45">Job No</th>
		<th width="45">Job year</th>
		<th width="100">Style Ref</th>
		<th width="90">Order No</th>
		<th width="60">PO Qty.</th>
		<th width="70">PO Recv. Date</th>
		<th width="70">Pub. Ship. Date</th>
		<th width="70">Ship. Date</th>
		<th width="70">PHD. Date</th>
		<th width="90">Order Status</th>
		<th>Status</th>
	</thead>

<?

foreach( $company_library as $cbo_company_name=>$company_name){


 
	$txt_date_from	= $previous_date;
	$txt_date_to	= $previous_date;
	$template=1;
	$cbo_category_by=1;


	$company_name = str_replace("'","",$cbo_company_name);
	$cbo_category_by = str_replace("'","",$cbo_category_by);

 
		$date_cond='';
		if($cbo_category_by==1)
		{
			
			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				$start_date=(str_replace("'","",$txt_date_from));
				$end_date=(str_replace("'","",$txt_date_to));
				$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
				$date_diff = diff_in_weeks_and_days($start_date,$end_date,'d');
				if($date_diff ==1)
				{
					$date_cond="and trunc(b.pub_shipment_date) = '$start_date'";
				}
			}
		}
		if($cbo_category_by==2)
		{
					
			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				$start_date=(str_replace("'","",$txt_date_from));
				$end_date=(str_replace("'","",$txt_date_to));
				$date_cond="and b.po_received_date between '$start_date' and '$end_date'";
				$date_diff = diff_in_weeks_and_days($start_date,$end_date,'d');
				if($date_diff ==1)
				{
					$date_cond="and trunc(b.po_received_date) = '$start_date'";
				}
			}
		}
		if($cbo_category_by==3)
		{
				
			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				$start_date=(str_replace("'","",$txt_date_from));
				$end_date=(str_replace("'","",$txt_date_to));
				$date_cond="and trunc(b.update_date) between '$start_date' and '$end_date'";
				$date_diff = diff_in_weeks_and_days($start_date,$end_date,'d');
				if($date_diff ==1)
				{
					$date_cond="and trunc(b.update_date) = '$start_date'";
				}
			}


		}
	

		if($template==1)
		{
					//$job_array=array();
					$po_wise_buyer=array();
					$buyer_wise_data=array();
					$po_id_arr=array();
					$report_data_arr=array();
					$sql = "SELECT to_char(b.insert_date,'YYYY') as year,a.job_no,a.id as job_id,a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.team_leader,a.season_buyer_wise, b.grouping, a.gmts_item_id,a.job_quantity, b.id,b.is_confirmed,b.po_number,b.po_received_date,b.pub_shipment_date,b.shipment_date,b.factory_received_date,b.po_quantity,b.pack_handover_date,b.status_active,b.projected_po_id,b.packing,b.updated_by,b.update_date,b.insert_date, a.style_description, a.set_break_down,a.style_ref_no_prev from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and b.updated_by>0 and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $season_cond $internal_ref_cond $jobcond $ordercond $team_cond";
					$sql_data=sql_select($sql);

					 //echo $sql;die;
					
			
		
					foreach( $sql_data as $row_data)
					{
						$po_id_arr[$row_data[csf('id')]]=$row_data[csf('id')];
						$po_wise_buyer[$row_data[csf('id')]]=$row_data[csf('buyer_name')];
						$po_wise_buyer[$row_data[csf('id')]]=$row_data[csf('year')];
						$buyer_wise_data[$row_data[csf('buyer_name')]]['po_quantity']+=$row_data[csf('po_quantity')];
						$buyer_wise_data[$row_data[csf('buyer_name')]]['po_price']+=$row_data[csf('po_quantity')]*$row_data[csf('unit_price')];
						$po_wise_phd[$row_data[csf('id')]]=$row_data[csf('pack_handover_date')];
						$job_id_arr[$row_data[csf('job_id')]]=$row_data[csf('job_id')];
					}
					$con = connect();
					execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1) and ENTRY_FORM=6");
					//oci_commit($con);
					//disconnect($con);
					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 6, 1, $job_id_arr, $empty_arr);

					$sql_style_data=sql_select("SELECT a.id as job_id,a.style_ref_no,a.style_ref_no_prev from wo_po_details_master a, wo_po_break_down b,gbl_temp_engine c where a.id=b.job_id and  c.ref_val=b.job_id and c.entry_form=6 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
					
					//echo "SELECT a.id as job_id,a.style_ref_no,a.style_ref_no_prev from wo_po_details_master a, wo_po_break_down b,gbl_temp_engine c where a.id=b.job_id and  c.ref_val=b.job_id and entry_form=6 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1"; die;

					foreach( $sql_style_data as $row)
					{
						$style_ref_no_arr[$row[csf('job_id')]]['current']=$row[csf('style_ref_no')];
						$style_ref_no_arr[$row[csf('job_id')]]['previous']=$row[csf('style_ref_no_prev')];
					}
					if(count($po_id_arr)>0)
					{
						$po_id=array_chunk($po_id_arr,999, true);
						$po_cond_in="";
						$ji=0;
						foreach($po_id as $key=> $value)
						{
							if($ji==0)
							{
									$po_cond_in=" po_id in(".implode(",",$value).")"; 
									
							}
							else
							{
									$po_cond_in.=" or po_id in(".implode(",",$value).")";
							}
							$ji++;
						}
					}
		
					$array_for_compare=array();	
					$log_array=array();
					$original_array=array();
					$sql_log=sql_select( "select id,po_id,order_status,po_no,po_received_date,shipment_date,org_ship_date,fac_receive_date,previous_po_qty,avg_price,excess_cut_parcent,plan_cut,status,projected_po,packing,remarks,file_no,update_date,update_by,phd_date from wo_po_update_log where $po_cond_in order by id DESC");

					$data = array();
					foreach($sql_log as $item){
						if(!$data[$item[csf('po_id')]]  || $data[$item[csf('po_id')]][csf('update_date')] < $item[csf('update_date')]){
							$data[$item[csf('po_id')]] = $item;
						}
					}
					// echo "<pre>";
					// print_r($data);exit;
					$sql_log = $data;
					foreach($sql_log as $row_log)
					{
						$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['order_status']=$row_log[csf('order_status')];
						$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['po_no']=$row_log[csf('po_no')];
						$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['po_received_date']=$row_log[csf('po_received_date')];
						$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['shipment_date']=$row_log[csf('shipment_date')];
						$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['phd_date']=$row_log[csf('phd_date')];
						
						$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['org_ship_date']=$row_log[csf('org_ship_date')];
						$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['fac_receive_date']=$row_log[csf('fac_receive_date')];
						$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['previous_po_qty']=$row_log[csf('previous_po_qty')];
						
						$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['status']=$row_log[csf('status')];
						$log_array[$row_log[csf('po_id')]][$row_log[csf('id')]]['projected_po']=$row_log[csf('projected_po')];
						
						//original=======================
						$original_array[$row_log[csf('po_id')]]['previous_po_qty']=$row_log[csf('previous_po_qty')];
						$original_array[$row_log[csf('po_id')]]['avg_price']=$row_log[csf('avg_price')];
						$original_array[$row_log[csf('po_id')]]['previous_po_amount']=$row_log[csf('previous_po_qty')]*$row_log[csf('avg_price')];
						//===================================
						$array_for_compare[$row_log[csf('po_id')]]['previous_po_qty'][]=$row_log[csf('previous_po_qty')];
						$array_for_compare[$row_log[csf('po_id')]]['avg_price'][]=$row_log[csf('avg_price')];
						
					}
					$original_array_buyer_wise=array();
					foreach($original_array as $key=>$value){
						$original_array_buyer_wise[$po_wise_buyer[$key]]['previous_po_qty']+=$value['previous_po_qty'];
						$original_array_buyer_wise[$po_wise_buyer[$key]]['previous_po_amount']+=$value['previous_po_amount'];
					}

			 
		
					$previous_po_qty=0;
					$avg_price=0;
					$previous_po_qty_net=0;
					$avg_price_net=0;
					$i=1;
					foreach($sql_data as $row_data)
					{
						$rowSpan=count($log_array[$row_data[csf('id')]]);
						$previous_po_qty=$array_for_compare[$row_data[csf('id')]]['previous_po_qty'][0];
						$avg_price=$array_for_compare[$row_data[csf('id')]]['avg_price'][0];
						$previous_po_qty_net=$array_for_compare[$row_data[csf('id')]]['previous_po_qty'][$rowSpan-1];
						$avg_price_net=$array_for_compare[$row_data[csf('id')]]['avg_price'][$rowSpan-1];
						//a.style_ref_no, a.set_break_down
						$set_break_down_arr=explode("__", $row_data[csf('set_break_down')]);
						$item_arr=array();
						foreach ($set_break_down_arr as $data) {
							$single_iten_arr=explode("_", $data);
							$item_arr[$single_iten_arr[0]] =  $garments_item[$single_iten_arr[0]];
						}
						$item_str='';
						if(count($item_arr)>0)
						{
							$item_str=implode(", ", $item_arr);
						}
				
						$prive_data_array=array();
						$prive_data_array[$row_data[csf('id')]]['is_confirmed']=$row_data[csf('is_confirmed')];
						$prive_data_array[$row_data[csf('id')]]['po_number']=$row_data[csf('po_number')];
						$prive_data_array[$row_data[csf('id')]]['po_received_date']=$row_data[csf('po_received_date')];
						$prive_data_array[$row_data[csf('id')]]['pub_shipment_date']=$row_data[csf('pub_shipment_date')];
						$prive_data_array[$row_data[csf('id')]]['shipment_date']=$row_data[csf('shipment_date')];
						$prive_data_array[$row_data[csf('id')]]['pack_handover_date']=$row_data[csf('pack_handover_date')];
						$prive_data_array[$row_data[csf('id')]]['po_quantity']=$row_data[csf('po_quantity')];
						$prive_data_array[$row_data[csf('id')]]['status_active']=$row_data[csf('status_active')];
						$prive_data_array[$row_data[csf('id')]]['projected_po_id']=$row_data[csf('projected_po_id')];
						$prive_data_array[$row_data[csf('id')]]['style_ref_no']=$row_data[csf('style_ref_no')];
						$prive_data_array[$row_data[csf('id')]]['style_ref_no_prev']=$row_data[csf('style_ref_no_prev')];

				
						$ii=1;
						
						foreach($log_array[$row_data[csf('id')]] as $key=>$value)
						{
							if($ii%2==0){
								$bgcolor="#E9F3FF";
							}
							else{
								$bgcolor="#EFEF00";
								$pre_bgcolor="#EFCA00";
							}
							$order_status_color= $bgcolor;
							if($value['order_status']!=$prive_data_array[$row_data[csf('id')]]['is_confirmed']){
								$order_status_color="#FF0000";
								$order_status_colorCHangeArr[$row_data[csf('id')]]="#FF0000";
								
							}
							else{
								$order_status_colorCHangeArr[$row_data[csf('id')]]=$bgcolor;
							}
					
							$po_number_color= $bgcolor;
							if($value['po_no']!=$prive_data_array[$row_data[csf('id')]]['po_number']){
								$po_number_color="#FF0000";
								$po_number_colorCHangeArr[$row_data[csf('id')]]="#FF0000";
							}
							else{
								$po_number_colorCHangeArr[$row_data[csf('id')]]=$bgcolor;
							}
							$po_received_date_color= $bgcolor;
							if(change_date_format($value['po_received_date'],'dd-mm-yyyy','-')!=change_date_format($prive_data_array[$row_data[csf('id')]]['po_received_date'],'dd-mm-yyyy','-')){
								$po_received_date_color="#FF0000";
								$po_received_date_colorCHangeArr[$row_data[csf('id')]]="#FF0000";
							}
							else{
								$po_received_date_colorCHangeArr[$row_data[csf('id')]]=$bgcolor;
							}
							$pub_shipment_date_color=$bgcolor;
							if(change_date_format($value['shipment_date'],'dd-mm-yyyy','-')!=change_date_format($prive_data_array[$row_data[csf('id')]]['pub_shipment_date'],'dd-mm-yyyy','-'))
							{
								$pub_shipment_date_color="#FF0000";
								$pubshipdate_colorCHangeArr[$row_data[csf('id')]]="#FF0000";
								
							}
							else{
								$pubshipdate_colorCHangeArr[$row_data[csf('id')]]=$bgcolor;
							}
							$shipment_date_color= $bgcolor;
							if(change_date_format($value['org_ship_date'],'dd-mm-yyyy','-')!=change_date_format($prive_data_array[$row_data[csf('id')]]['shipment_date'],'dd-mm-yyyy','-')){
								$shipment_date_color="#FF0000";
								$shipment_date_colorCHangeArr[$row_data[csf('id')]]="#FF0000";
							}
							else{
								$shipment_date_colorCHangeArr[$row_data[csf('id')]]=$bgcolor;
							}
							
							$pack_handover_date_color= $bgcolor;
							if(change_date_format($value['phd_date'],'dd-mm-yyyy','-')!=change_date_format($prive_data_array[$row_data[csf('id')]]['pack_handover_date'],'dd-mm-yyyy','-')){
								$pack_handover_date_color="#FF0000";
								$pack_handover_date_colorCHangeArr[$row_data[csf('id')]]="#FF0000";
							}
							else{
								$pack_handover_date_colorCHangeArr[$row_data[csf('id')]]=$bgcolor;
							}
							
							$previous_po_qty_color= $bgcolor;
							if($value['previous_po_qty']!=$prive_data_array[$row_data[csf('id')]]['po_quantity']){
								$previous_po_qty_color="#FF0000";
								$previous_po_qty_colorCHangeArr[$row_data[csf('id')]]="#FF0000";
							}
							else{
								$previous_po_qty_colorCHangeArr[$row_data[csf('id')]]=$bgcolor;
							}
							$status_color= $bgcolor;
							if($value['status']!=$prive_data_array[$row_data[csf('id')]]['status_active']){
								$status_color="#FF0000";
								$status_colorCHangeArr[$row_data[csf('id')]]="#FF0000";
							}
							else{
								$status_colorCHangeArr[$row_data[csf('id')]]=$bgcolor;
							}
							
							$style_ref_color=$bgcolor;
							if($style_ref_no_arr[$row_data[csf('job_id')]]['previous']!=$style_ref_no_arr[$row_data[csf('job_id')]]['current']){
								$style_ref_color="#FF0000";
								//$style_ref_colorCHangeArr[$row_data[csf('id')]]="#FF0000";
							}
							// else{
							// 	$style_ref_colorCHangeArr[$row_data[csf('id')]]=$bgcolor;
							// }
							$previous_po_qt_h=$array_for_compare[$row_data[csf('id')]]['previous_po_qty'][$ii];
						
				?>
						
							<tr align="center" bgcolor="<? echo $pre_bgcolor;?>">
								
								<td><? echo $i; ?></td>
								
								<td style="word-wrap:break-word; word-break: break-all;">
								<?
								$update_no=$rowSpan-$ii;
								if($update_no==0){
								echo "Previous Data"; 
								}
								else{
									echo "Edit No: $update_no";
								}
								?>
								</td>
								<td style="word-wrap:break-word; word-break: break-all; color:"><? echo $company_library[$row_data[csf('company_name')]];?></td>
								
								<td style="word-wrap:break-word; word-break: break-all; color:"><? echo $team_name_library[$row_data[csf('team_leader')]]; ?></td>
								
								<td style="word-wrap:break-word; word-break: break-all; color:"><? echo $buyer_short_name_library[$row_data[csf('buyer_name')]]; ?></td>
								
								<td style="word-wrap:break-word; word-break: break-all; color:" title="<? echo $row_data[csf('job_no')];  ?>"><? echo $row_data[csf('job_no_prefix_num')]; ?></td>
								
								<td style="word-wrap:break-word; word-break: break-all;"  bgcolor="<? echo $pre_bgcolor; ?>"><? echo $row_data[csf('year')]; ?></td>
								
								<? 		
										$current=$style_ref_no_arr[$row_data[csf('job_id')]]['current'];
										$previous=$style_ref_no_arr[$row_data[csf('job_id')]]['previous'];


											if($previous!=''){
												$style_ref_color="#FF0000";
												$style_ref_no=$previous;
												
													//echo $previous;
											}else{
												$style_ref_color=$bgcolor;
												$style_ref_no=$current;

													//echo $current;
												} 
								?>
								<td style="word-wrap:break-word; word-break: break-all;">
									<?
										echo $style_ref_no;
									?>
								</td>
								
								<td style="word-wrap:break-word; word-break: break-all;" ><? echo $value['po_no']; ?></td>
								
								<td style="word-wrap:break-word; word-break: break-all; text-align:right" >
								<? echo $value['previous_po_qty']; ?>
								</td>
								<td style="word-wrap:break-word; word-break: break-all;" >
								<?
								$po_received_date= $value['po_received_date'];
								if($po_received_date !="" && $po_received_date !="0000-00-00" && $po_received_date !="0")
								{
								echo change_date_format($po_received_date,'dd-mm-yyyy','-'); 
								}
								?>
								</td>
								<td style="word-wrap:break-word; word-break: break-all;" >
								<?
								$pub_shipment_date= $value['shipment_date'];
								if($pub_shipment_date !="" && $pub_shipment_date !="0000-00-00" && $pub_shipment_date !="0")
								{
								echo change_date_format($pub_shipment_date,'dd-mm-yyyy','-'); 
								}
								?>
								</td>
								<td style="word-wrap:break-word; word-break: break-all;" >
								<?
								$shipment_date= $value['org_ship_date'];
								if($shipment_date !="" && $shipment_date !="0000-00-00" && $shipment_date !="0")
								{
								echo change_date_format($shipment_date,'dd-mm-yyyy','-'); 
								}
								?>
								</td>
								<td style="word-wrap:break-word; word-break: break-all;" >
								<?
									$pack_handover_date= $value['phd_date'];
									echo change_date_format($pack_handover_date,'dd-mm-yyyy','-'); 
								?>
								</td>
								<td width="90" style="word-wrap:break-word; word-break: break-all;" ><? echo $order_status[$value['order_status']]; ?></td>
								<td width="70" style="word-wrap:break-word; word-break: break-all; color:"><? echo $row_status[$value['status']]; ?></td>
							</tr>
				<?
							$prive_data_array[$row_data[csf('id')]]['is_confirmed']=$value['order_status'];
							$prive_data_array[$row_data[csf('id')]]['po_number']=$value['po_no'];
							$prive_data_array[$row_data[csf('id')]]['po_received_date']=$value['po_received_date'];
							$prive_data_array[$row_data[csf('id')]]['pub_shipment_date']=$value['shipment_date'];
							$prive_data_array[$row_data[csf('id')]]['shipment_date']=$value['org_ship_date'];
							$prive_data_array[$row_data[csf('id')]]['pack_handover_date']=$value[csf('phd_date')];
							$prive_data_array[$row_data[csf('id')]]['po_quantity']=$value['previous_po_qty'];
							$prive_data_array[$row_data[csf('id')]]['status_active']=$value['status'];
							$prive_data_array[$row_data[csf('id')]]['projected_po_id']=$value['projected_po'];
							$prive_data_array[$row_data[csf('id')]]['style_ref_no']=$value['style_ref_no'];
							$ii++;
						}
						//=========================================================
				?>
					<tr align="center" bgcolor="#EFEF00" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
						<td><? echo $i."."."1"; ?></td>
						<td style="word-wrap:break-word; word-break: break-all;"><? echo "Current Data" ?></td>
						<td style="word-wrap:break-word; word-break: break-all;"><? echo $company_library[$row_data[csf('company_name')]];?></td>
						<td style="word-wrap:break-word; word-break: break-all;"><? echo $team_name_library[$row_data[csf('team_leader')]]; ?></td>
						<td style="word-wrap:break-word; word-break: break-all;"><? echo $buyer_short_name_library[$row_data[csf('buyer_name')]]; ?></td>
						<td style="word-wrap:break-word; word-break: break-all;" title="<? echo $row_data[csf('job_no')]; ?>"><? echo $row_data[csf('job_no_prefix_num')]; ?></td>
						<td style="word-wrap:break-word; word-break: break-all;" title=""><? echo $row_data[csf('year')]; ?></td>
						<? 		
							$current=$style_ref_no_arr[$row_data[csf('job_id')]]['current'];
							$previous=$style_ref_no_arr[$row_data[csf('job_id')]]['previous']; 
						?>
					
						<td style="word-wrap:break-word; word-break: break-all;"  bgcolor="<? echo $style_ref_color;//$style_ref_colorCHangeArr[$row_data[csf('id')]];?>"><? echo $current; //$row_data[csf('style_ref_no')]; ?></td>
						
						<td style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $po_number_colorCHangeArr[$row_data[csf('id')]]; ?>"><? echo $row_data[csf('po_number')]; ?></td>
					
						<td style="word-wrap:break-word; word-break: break-all; text-align:right" title="<?  if($rowSpan>0){echo $row_data[csf('po_quantity')]-$previous_po_qty_net;}  ?>" bgcolor="<? echo $previous_po_qty_colorCHangeArr[$row_data[csf('id')]]; ?>">
						<? echo $row_data[csf('po_quantity')]; ?>
						</td>
					
						<td style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $po_received_date_colorCHangeArr[$row_data[csf('id')]]; ?>">
						<?
						$po_received_date= $row_data[csf('po_received_date')];
						if($po_received_date !="" && $po_received_date !="0000-00-00" && $po_received_date !="0")
						{
						echo change_date_format($po_received_date,'dd-mm-yyyy','-'); 
						}
						?>
						</td>
					
						<td style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $pubshipdate_colorCHangeArr[$row_data[csf('id')]]; ?>">
						<?
						$pub_shipment_date= $row_data[csf('pub_shipment_date')];
						if($pub_shipment_date !="" && $pub_shipment_date !="0000-00-00" && $pub_shipment_date !="0")
							{
							echo change_date_format($pub_shipment_date,'dd-mm-yyyy','-'); 
							}
						?>
						</td>
						
						<td style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $shipment_date_colorCHangeArr[$row_data[csf('id')]]; ?>">
						<?
						$shipment_date= $row_data[csf('shipment_date')];
						if($shipment_date !="" && $shipment_date !="0000-00-00" && $shipment_date !="0")
							{
							echo change_date_format($shipment_date,'dd-mm-yyyy','-'); 
							}
						?>
						</td>
					
						<td style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $pack_handover_date_colorCHangeArr[$row_data[csf('id')]]; ?>">
						<?
						$pack_handover_date= $row_data[csf('pack_handover_date')];
						if($pack_handover_date !="" && $pack_handover_date !="0000-00-00" && $pack_handover_date !="0")
							{
							echo change_date_format($pack_handover_date,'dd-mm-yyyy','-'); 
							}
						?>
						</td>
					
						<td style="word-wrap:break-word; word-break: break-all;" ><? echo $order_status[$row_data[csf('is_confirmed')]]; ?></td>
					
						<td style="word-wrap:break-word; word-break: break-all;" bgcolor="<? //echo $order_status_colorCHangeArr[$row_data[csf('id')]]; ?>">
						<? echo $row_status[$row_data[csf('status_active')]]; ?>
						</td>
					</tr>

				<?
						$ii++;
					}
						$con = connect();
						execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1) and ENTRY_FORM=6");
						oci_commit($con);
						disconnect($con);

		}
  
	}

	?>

	</table>
	</div>
</fieldset>
</div>
<?

	$message = ob_get_contents();
	ob_clean();
		//echo $message;


	$mail_item=133;	
	$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address as MAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=$mail_item and b.mail_user_setup_id=c.id and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	//echo $sql;die;
   $mail_sql=sql_select($sql);
   foreach($mail_sql as $row)
   {
	   $receverMailArr[$row['MAIL']]=$row['MAIL'];
   }
   $to=implode(',',$receverMailArr);
   
   $subject = "Daily Order Update History";
   $header=mailHeader();
   //echo $message ;

   
   if($_REQUEST['isview']==1){
	   if($to){
		   echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
	   }else{
		   echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
	   }
	   echo $message;
   }
   else{
	   echo  sendMailMailer( $to, $subject, $message, '','' );
   }



        ?>

