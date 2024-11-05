<?php
date_default_timezone_set("Asia/Dhaka");

	include('../includes/common.php');
	//include('../mailer/class.phpmailer.php');
	include('setting/mail_setting.php');

 
	$team_leader_name_arr = return_library_array( "select id,team_leader_name from lib_marketing_team where  status_active =1 and is_deleted=0", "id", "team_leader_name");
	$company_library 	= return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$buyer_library 		= return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
	$user_arr = return_library_array( "select id,user_full_name from user_passwd where valid=1","id","user_full_name");
	

	$previous_date= date('d-M-Y', strtotime("-1 day"));
	$current_date = date('d-M-Y', strtotime("-1 day"));
	$previous_3month_date = change_date_format(date('d-M-Y H:i:s', strtotime('-180 day', strtotime($current_date))),'','',1); 

	$a=mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
	$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	$str_cond_a	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";	
	$str_cond_e	=" and approved_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
 

 
 
foreach($company_library as $compid=>$compname) /// Less EPM than CPM approved pre-costing
{
	$flag=0;	
	ob_start();
	
	
	$sql="select mst_id,job_no,approved_by,approved_date from co_com_pre_costing_approval where current_approval_status=1 $str_cond_e order by id asc"; 
	$data_array=sql_select($sql);
	foreach( $data_array as $row )
	{ 
		$last_approved_by_arr[$row[csf(job_no)]]=$row[csf(approved_by)];
		$last_approved_date_arr[$row[csf(job_no)]]=$row[csf(approved_date)];
		$pre_cost_mst_id[$row[csf("job_no")]]=$row[csf("mst_id")];
		$pre_cost_job_id[$row[csf("job_no")]]=$row[csf("job_no")];
	}
	//var_dump($last_approved_arr);
	
	
	$sql = "select a.job_no,a.costing_date,a.sew_effi_percent,a.exchange_rate,b.commission,b.total_cost,b.cm_cost from wo_pre_cost_mst a,wo_pre_cost_dtls b
	where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.approved=1 and a.id in(".implode(',',$pre_cost_mst_id).")";
	$data_array=sql_select($sql);
	foreach( $data_array as $row )
	{ 
		$marginDataArr[$row[csf("job_no")]]['total_material_service_cost']=$row[csf("total_cost")]-$row[csf("cm_cost")];
		$marginDataArr[$row[csf("job_no")]]['cm_cost']=$row[csf("cm_cost")];
		$marginDataArr[$row[csf("job_no")]]['costing_date']=$row[csf("costing_date")];
		$marginDataArr[$row[csf("job_no")]]['sew_effi_percent']=$row[csf("sew_effi_percent")];
		$marginDataArr[$row[csf("job_no")]]['exchange_rate']=$row[csf("exchange_rate")];
		$marginDataArr[$row[csf("job_no")]]['commission']=$row[csf("commission")];
	}
	
	
	$sql="select cost_per_minute,applying_period_date from lib_standard_cm_entry where company_id=$compid and  is_deleted=0";
	$data_array=sql_select($sql);
	foreach( $data_array as $row )
	{ 
		$apd=date("m-Y",strtotime($row[csf(applying_period_date)]));
		$cost_per_minute_arr[$apd]=$row[csf(cost_per_minute)];

	}

	?>

	<table cellspacing="0" border="0" align="center">
		<tr>
			<td colspan="28" align="center">
				<strong><?php  echo $company_library[$compid]; ?></strong>
			</td>
		</tr>
		<tr>
			<td colspan="28" align="center">
				<b style="font-size:14px;"> 
					Less EPM than CPM approved pre-costing ( Date : <? echo date("d-m-Y", $a);  ?> )
				</b>
			</td>
		</tr>
	</table>

	<table border="1" width="100%" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
		<thead style="background-color:#ddd">
			<tr>
				<th rowspan="2" width="35">Sl</th>
				<th rowspan="2" width="80">Job No</th>
				<th rowspan="2" width="100">Order No</th>
				<th rowspan="2" width="100">Buyer</th>
				<th rowspan="2" width="100">Style</th>
				<th rowspan="2" width="100">Item</th>
				<th rowspan="2" width="100">P.O Rcv Date</th>
				<th rowspan="2" width="80">Ship Date</th>
				<th rowspan="2" width="80">Costing Date</th>
				<th rowspan="2" width="50">Lead Time</th>
				<th rowspan="2" width="30">SMV</th>
				<th rowspan="2" width="30">Eff %</th>
				<th rowspan="2" width="100">Order Qty.</th>
				<th rowspan="2" width="50">UOM</th>
				<th rowspan="2" width="100">Order Qty (Pcs)</th>
				<th rowspan="2" width="100">Total SMV</th>
				<th rowspan="2" width="70">Unit Price</th>
				<th colspan="10">Margin Summary</th>
				<th rowspan="2" width="80">Team Leader</th>
				<th rowspan="2" width="80">Approved By</th>
				<th rowspan="2" width="100">Approved Last Date & Time</th>
			</tr>
			<tr>
				<td width="80">Total Order Value</td>
				<td width="80">Total Comm- ision</td>
				<td width="80">Net Order Value</td>
				<td width="80">Total Material & Service Cost</td>
				<td width="80">CM Value</td>
				<td width="80">CM Cost</td>
				<td width="80">Margin</td>
				<td width="80">Margin %</td>
				<td width="80">EPM</td>
				<td width="80">CPM</td>
			</tr>
		</thead>
		<tbody>
			<?php


			$i=0;
			$total_po_qty=0;
			$total_value=0;
			if($db_type==0){$date_diff="DATEDIFF(b.shipment_date,b.po_received_date) as  date_diff,";}
			else{$date_diff="(b.shipment_date - b.po_received_date) as  date_diff,";}

			$sql_mst="select $date_diff a.job_no,a.set_smv,a.set_break_down,a.order_uom,a.dealing_marchant,a.team_leader,b.po_number,a.buyer_name,a.style_ref_no,b.po_quantity,b.unit_price,b.shipment_date,b.po_received_date,b.is_confirmed,b.inserted_by from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.job_no in('".implode("','",$pre_cost_job_id)."')";				
			$nameArray_mst=sql_select($sql_mst);
			foreach($nameArray_mst as $row)
			{
				$i++;
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  

				$set_arr=explode('__',$row[csf('set_break_down')]);
				$item_sting='';
				$smv_sting='';
				$set_sting='';
				$smv_sum=0;
				$set_sum=0;
				foreach($set_arr as $set_data){
					list($item,$set,$smv)=explode('_',$set_data);
					if($item_sting=='')$item_sting.=$garments_item[$item];else $item_sting.=','.$garments_item[$item];
					if($smv_sting=='')$smv_sting.=number_format($smv,2);else $smv_sting.='+'.number_format($smv,2);
					if($set_sting=='')$set_sting.=number_format($set,2);else $set_sting.=':'.number_format($set,2);
						$smv_sum+=$smv;
						$set_sum+=$set;
					}
			//............................................
					$tot_pic_qty=$set_sum*$row[csf('po_quantity')];
					$commision = ($marginDataArr[$row[csf('job_no')]]['commission']/12)*$tot_pic_qty;
					$value=$row[csf('po_quantity')]*$row[csf('unit_price')]; 
					$tmsc=(($marginDataArr[$row[csf('job_no')]]['total_material_service_cost']/12)*$tot_pic_qty)-$commision;
					$nov=$value-$commision;
					$cmValue=($nov-$tmsc);
					$tot_smv=($row[csf('set_smv')]*$row[csf('po_quantity')]); 			
			//$margin_parcent=number_format((($cmValue/$tot_pic_qty)/$row[csf('set_smv')])*100,4);

					$cm_cost=($marginDataArr[$row[csf('job_no')]]['cm_cost']/12)*$tot_pic_qty;
					$margin=$cmValue-$cm_cost;
					$margin_parcent=($margin/$value)*100;


					$cd=date("m-Y",strtotime($marginDataArr[$row[csf('job_no')]]['costing_date']));
					$cpm=(($cost_per_minute_arr[$cd]/$marginDataArr[$row[csf('job_no')]]['exchange_rate'])/$marginDataArr[$row[csf('job_no')]]['sew_effi_percent'])*100;

					$cm_cost=($marginDataArr[$row[csf('job_no')]]['cm_cost']/12)*$tot_pic_qty; 
			//$cmValue=($value-$tmsc);
					$margin=$cmValue-$cm_cost;

					$epm=$cmValue/$tot_smv; 
			//$epm=$margin/$value;


					if($epm<$cpm){
						?>	
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td align="center"><? echo $i;?></td>
							<td><? echo $row[csf('job_no')]; ?></td>
							<td><? echo $row[csf('po_number')]; ?></td>
							<td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
							<td><? echo $row[csf('style_ref_no')]; ?></td>
							<td><? echo $item_sting; ?></td>
							<td align="center"><? echo $row[csf('po_received_date')]; ?></td>
							<td align="center"><? echo $row[csf('shipment_date')]; ?></td>
							<td align="center"><? echo $marginDataArr[$row[csf('job_no')]]['costing_date'];?></td>
							<td align="center"><? echo $row[csf('date_diff')]; ?></td>
							<td align="right">
								<? 
								echo $smv_sting;
								if($row[csf('order_uom')]!=1)echo '='.number_format($smv_sum,2);
								?>
							</td>
							<td align="right"><? echo $marginDataArr[$row[csf('job_no')]]['sew_effi_percent'];?></td>
							<td align="right"><? echo number_format($row[csf('po_quantity')]);?></td>
							<td align="center">
								<? 
								echo $unit_of_measurement[$row[csf('order_uom')]]; 
								if($row[csf('order_uom')]!=1)echo '<br>('.$set_sting.')';
								?>
							</td>
							<td align="right"><? echo number_format($tot_pic_qty); $total_po_qty+=$tot_pic_qty ; ?></td>
							<td align="right">
								<? 
								echo number_format($tot_smv,2); 
								$grund_tot_smv+=$tot_smv; 
								?>
							</td>
							<td align="right"><?php echo number_format($row[csf('unit_price')],2); ?></td>
							<td align="right">
								<?php 
								echo number_format($value,2);
								$total_value+= $value;
								?>
							</td>
							<td align="right"><? echo number_format($commision,2);?></td>
							<td align="right"><? echo number_format($nov,2);?></td>
							<td align="right"><? echo number_format($tmsc,2);?></td>
							<td align="right"><? echo number_format($cmValue,2);?></td>
							<td align="right"><? echo number_format($cm_cost,2);?></td>
							<td align="right"><?  echo number_format($margin,2);?></td>
							<td align="right"><? echo number_format($margin_parcent,2); ?></td>
							<td align="right"><? echo number_format($epm,3);?></td>
							<td align="right" title="((Cost Per Minute / Exchange Rate)/Sew Efficiency %) "><? echo number_format($cpm,3); ?></td>
							<td><? echo $team_leader_name_arr[$row[csf('team_leader')]]; ?></td>
							<td><? echo $user_arr[$last_approved_by_arr[$row[csf('job_no')]]]; ?></td>
							<td><? echo $last_approved_date_arr[$row[csf('job_no')]]; ?></td>
						</tr>
						<?
						$flag=1;
					}
				}
				?> 
			</tbody>         
			<tfoot style="background-color:#DDD">
				<th align="right" colspan="10"><b>Total :</b></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right"><?  echo number_format($total_po_qty,2);$total_po_qty=0; ?></th>
				<th align="right"><? echo number_format($grund_tot_smv,2); $grund_tot_smv=0;?></th>
				<th>&nbsp;</th>
				<th align="right"><?  echo number_format($total_value,2); $total_value=0;?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
		<?
		$to="";
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=13 and b.mail_user_setup_id=c.id and a.company_id=$compid AND a.MAIL_TYPE=1";
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		
		$subject="Below 5% Profitability Order List";
		$message="";
		$message=ob_get_contents();
		ob_clean();
		$header=mailHeader();
		if($to!="" && $flag==1)echo sendMailMailer( $to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port );
		
		
	// echo $message;	

	}




	?> 