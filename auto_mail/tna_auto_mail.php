<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

 


$sql="select id,team_member_name,member_contact_no from lib_mkt_team_member_info where  status_active =1 and is_deleted=0";
$data_array=sql_select($sql);
foreach( $data_array as $row )
{ 
	$dealing_merchant_arr[$row[csf("id")]] = $row[csf("team_member_name")].'<br>'.$row[csf("member_contact_no")];
}


$team_leader_name_arr = return_library_array( "select id,team_leader_name from lib_marketing_team where  status_active =1 and is_deleted=0", "id", "team_leader_name");
$company_library 	= return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$buyer_library 		= return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
$party_library 		= return_library_array( "select id, other_party_name from lib_other_party where  status_active=1 and is_deleted=0", "id", "other_party_name");
$supplier_library 	= return_library_array( "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
$user_arr 			= return_library_array( "select id,user_full_name from user_passwd where valid=1","id","user_full_name");
$country_arr 		= return_library_array( "select id, country_name from lib_country", "id", "country_name");

 

	if($db_type==0)
	{
		$previous_date= date('Y-m-d', strtotime("-1 day"));
		$current_date = date('Y-m-d', strtotime("-1 day"));
		$previous_3month_date = date('Y-m-d H:i:s', strtotime('-92 day', strtotime($current_date))); 
	}
	else
	{
		$previous_date= date('d-M-Y', strtotime("-1 day"));
		$current_date = date('d-M-Y', strtotime("-1 day"));
		$previous_3month_date = change_date_format(date('d-M-Y H:i:s', strtotime('-180 day', strtotime($current_date))),'','',1); 
	}





$a=mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));




if($db_type==0){
	$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date."'";
	$str_cond_a	=" and a.insert_date between '".$previous_date."' and '".$current_date."'";
	$str_cond_b	=" and b.insert_date between '".$previous_date."' and '".$current_date."'";
	$str_cond_c	=" and c.insert_date between '".$previous_date."' and '".$current_date."'";
	$str_cond_d	=" and a.insert_date between '".$previous_date."' and '".$current_date."'";
	$str_cond_e	=" and approved_date between '".$previous_date."' and '".$current_date."'";
}
else
{
	$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	$str_cond_a	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		//$str_cond_a=" and a.production_date between '".$previous_date."' and '".$current_date."'";
	$str_cond_b	=" and b.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	$str_cond_c	=" and c.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	$str_cond_d	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	$str_cond_e	=" and approved_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
}




	
	





//$str_cond_b = "and b.insert_date between '01-Apr-2017' and '10-Apr-2017 11:59:59 PM' ";
 //$company_library=array(3=>3);
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
		$to="";$mail_item=13;
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=13 and b.mail_user_setup_id=c.id and a.company_id=$compid  and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
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
		//if($to!="" && $flag==1)echo sendMailMailer( $to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port );
		if($_REQUEST['isview']==1){
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo $message;
		}
		else{
			if($to!="" && $flag==1)echo sendMailMailer( $to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port );
		}

		
		
	// echo $message;	

	}


foreach($company_library as $compid=>$compname) 
{

	$party_lib_array = return_library_array("select a.id,a.buyer_name from lib_buyer a where a.is_deleted=0 and a.status_active=1","id","buyer_name");

	ob_start();

	?>

	<table width="1050"  cellspacing="0" border="0">
		<tr>
			<td colspan="" align="center">
				<strong>Yesterday Total Activities For Subcontract (Dyeing)</strong>
			</td>
		</tr>
		<tr>
			<td colspan="" align="center">
				<strong><? echo $prevDay; ?></strong>
			</td>
		</tr>

		<tr>
			<td colspan="" align="center">
				<strong>Company Name: <?php  echo $company_library[$compid]; ?></strong>
			</td>
		</tr>

		<tr>
			<td colspan="" align="center">
				<? 
				$sql_address=sql_select("select id,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where status_active=1 and is_deleted=0 and id=$compid"); 
				foreach($sql_address as $result)
				{
					echo $result[csf('plot_no')];  
					echo $result[csf('level_no')];
					echo $result[csf('road_no')]; 
					echo $result[csf('block_no')]; 
					echo $result[csf('city')];
					echo $result[csf('zip_code')];  
					echo $result[csf('province')];
					echo $country_arr[$result[csf('country_id')]];
					echo $result[csf('email')];
					echo $result[csf('website')];
				}
				?>              
			</td>
		</tr>
		<tr>
			<td colspan="" align="center">
				<strong>Date: <?php $prv_date=explode(" ",$previous_date); echo date("d-m-Y", strtotime($prv_date[0])); ?></strong>
			</td>
		</tr>
	</table>
	<!-- Gray Receive Status Creation Status -->
	<table width="600"  cellspacing="0" border="1" rules="all" class="rpt_table">
		<caption><strong>Grey Receive Status</strong></caption>
		<thead style="background-color:#CCC">
			<tr align="center">
				<th width="35">Sl</th>
				<th width="150">Party</th>
				<th width="110">Receive Qty</th>
		   <!-- <th width="110">Avg Rate</th>
			<th width="110">Amount</th>-->
			<th>Remarks</th>
		   </tr>
	   </thead>
	   <tbody>

		<?
		//and a.subcon_date='$pre_orcl_format_date'
		//and a.subcon_date='$pre_orcl_format_date' condition last date
		//$sql_material_receive=sql_select("select a.party_id,sum(b.quantity) as quantity,b.rate from sub_material_mst a,sub_material_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$compid  and a.id=b.mst_id and a.subcon_date='$previous_date' group by a.party_id,b.rate");
		$sql_material_receive=sql_select("select a.party_id,sum(b.quantity) as quantity,b.rate from sub_material_mst a,sub_material_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2) and b.is_deleted=0 and a.company_id=$compid  and a.id=b.mst_id  AND a.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE() group by a.party_id,b.rate");
		$i=1;
		$total_rec_qty='';
		$total_amount='';
		foreach($sql_material_receive as $result)
		{
			//$amount 		=$result[csf('quantity')]*$result[csf('rate')];
			//$avg_rate 		=($amount/$result[csf('quantity')]);
			$total_rec_qty 	+=$result[csf('quantity')];
			$total_amount 	+=$amount;
			?>
			<tr>
				<td> <? echo $i++; ?></td>
				<td> <? echo $party_lib_array[$result[csf('party_id')]]; ?></td>
				<td align="right"> <? echo number_format($result[csf('quantity')],2); ?></td>
			   <!-- <td align="right"> <? //echo  number_format($avg_rate,1); ?></td>
				<td align="right"> <? //echo number_format($amount,2); ?></td>-->
				<td> <?  ?></td>
			   </tr>
			   <?
		   }
		   ?>
	   </tbody>
	   <tfoot style="background-color:#CCC">
		<th align="right" colspan="2"><b>Total :</b></th>
		<th align="right"><?  echo number_format($total_rec_qty,2); ?></th>
		<th  colspan="2">&nbsp; </th>
   <!-- <th align="right"><? //echo number_format($total_amount,2); ?></th>
	<th>&nbsp; </th>-->

   </tfoot>
</table>
<!-- Batch Creation Status -->
<table width="600"  cellspacing="0" border="1" rules="all" class="rpt_table">
<caption><strong>Batch Creation Status</strong></caption>
<thead style="background-color:#CCC">
	<tr align="center">
		<th width="35">Sl</th>
		<th width="150">Party</th>
		<th width="110">Batch Qty</th>
		<th>Remarks</th>
	</tr>
</thead>
<tbody>

	<?
		//and a.batch_date='$previous_date'
		//print_r($get_party_by_po_arr);
		//and a.batch_date='$pre_orcl_format_date' condition last date
	$sql_batch_creation=sql_select("SELECT sum( b.batch_qnty ) AS batch_qnty, c.party_id FROM pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_mst c,subcon_ord_dtls d WHERE a.status_active =1 AND a.is_deleted =0 AND b.status_active =1 AND b.is_deleted =0 AND a.company_id =$compid AND a.entry_form =36 AND a.id = b.mst_id  AND b.po_id = d.id and c.subcon_job=d.job_no_mst  AND a.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE()  GROUP BY c.party_id");
		//$sql_batch_creation=sql_select("select sum(b.batch_qnty)as batch_qnty from pro_batch_create_mst a,pro_batch_create_dtls b,subcon_ord_mst c where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$compid and a.entry_form=36 and a.id=b.mst_id  $str_cond_a");
		//$sql_batch_creation=sql_select("select sum(b.batch_qnty)as batch_qnty,c.party_id from pro_batch_create_mst a,pro_batch_create_dtls b,subcon_ord_mst c where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$compid and a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id  $str_cond_a group by c.party_id");
	   //echo "SELECT sum( b.batch_qnty ) AS batch_qnty, c.party_id FROM pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_mst c,subcon_ord_dtls d WHERE a.status_active =1 AND a.is_deleted =0 AND b.status_active =1 AND b.is_deleted =0 AND a.company_id =2 AND a.entry_form =36 AND a.id = b.mst_id  AND b.po_id = d.id and c.subcon_job=d.job_no_mst  $str_cond_a   GROUP BY c.party_id";
	$i=1;
	$total_batch_qty='';
	foreach($sql_batch_creation as $result)
	{
		$total_batch_qty +=$result[csf('batch_qnty')];;
		?>
		<tr>
			<td> <? echo $i++; ?></td>
			<td> <? echo $party_lib_array[$result[csf('party_id')]]; ?></td>
			<td align="right"> <? echo number_format($result[csf('batch_qnty')],2); ?></td>
			<td> <?  ?></td>
		</tr>
		<?
	}
	?>

</tbody>
<tfoot style="background-color:#CCC">
	<th align="right" colspan="2"><b>Total :</b></th>
	<th align="right"><?  echo  number_format($total_batch_qty,2); ?></th>
	<th  colspan="2">&nbsp; </th>
</tfoot>
</table>
<!-- Dyeing Production status -->
<table width="600"  cellspacing="0" border="1" rules="all" class="rpt_table">
<caption><strong>Dyeing Production status</strong></caption>
<thead style="background-color:#CCC">
	<tr align="center">
		<th width="35">Sl</th>
		<th width="150">Party</th>
		<th width="110">Prod. Qty</th>
		<th>Remarks</th>
	</tr>
</thead>
<tbody>

	<?

	  //and a.process_end_date='$previous_date'
	  //echo "select a.load_unload_id,sum(b.batch_weight) as batch_weight from pro_fab_subprocess a, pro_batch_create_mst b where b.id=a.batch_id and a.company_id like '$compid' and a.load_unload_id=2 and a.entry_form=38 and a.result=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.entry_form=38  group by a.load_unload_id";	

		//$sql_dyeing_prod=sql_select("select sum(b.batch_weight) as batch_weight,d.party_id from pro_fab_subprocess a, pro_batch_create_mst b,pro_batch_create_dtls c,subcon_ord_mst d where b.id=a.batch_id and a.company_id = '$compid' and a.load_unload_id=2 and a.entry_form=38 and b.entry_form=36 and a.result=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.id=c.mst_id and c.po_id=d.id  AND a.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE() group by d.party_id");   

	$sql_dyeing_prod=sql_select("select d.party_id, SUM(b.batch_qnty) AS sub_batch_qnty from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f, lib_color g where f.company_id='$compid' and f.batch_id=a.id and a.entry_form=36 and g.id=a.color_id and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2 and f.result=1 and a.batch_against in(1,2) and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 AND f.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE() and f.result=1 GROUP BY d.party_id");

	$i=1;
	$total_dyeing_batch_qty='';
	foreach($sql_dyeing_prod as $result)
	{
		$total_dyeing_batch_qty += $result[csf('sub_batch_qnty')];
		?>
		<tr>
			<td> <? echo $i++; ?></td>
			<td> <? echo $party_lib_array[$result[csf('party_id')]]; ?></td>
			<td align="right"> <? echo number_format($result[csf('sub_batch_qnty')],2); ?></td>
			<td> <?  ?></td>
		</tr>
		<?
	}
	?>

</tbody>
<tfoot style="background-color:#CCC">
	<th align="right" colspan="2"><b>Total :</b></th>
	<th align="right"><?  echo  number_format($total_dyeing_batch_qty,2); ?></th>
	<th  colspan="2">&nbsp; </th>
</tfoot>
</table>
<!-- Finishing Production status -->
<table width="600"  cellspacing="0" border="1" rules="all" class="rpt_table">
<caption><strong>Finishing Production status</strong></caption>
<thead style="background-color:#CCC">
	<tr align="center">
		<th width="35">Sl</th>
		<th width="150">Party</th>
		<th width="110">Prod. Qty</th>
		<th>Remarks</th>
	</tr>
</thead>
<tbody>

	<?

		//and a.product_date='$pre_orcl_format_date' condition last date
	$sql_fnsh_prod=sql_select("select a.party_id,sum(b.product_qnty) as product_qnty from subcon_production_mst a,subcon_production_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$compid and a.id=b.mst_id AND a.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE() group by a.party_id");

	$i=1;
	$total_fnsh_prod_qty='';
	foreach($sql_fnsh_prod as $result)
	{
		$total_fnsh_prod_qty+= $result[csf('product_qnty')];
		?>
		<tr>
			<td> <? echo $i++; ?></td>
			<td> <? echo $party_lib_array[$result[csf('party_id')]]; ?></td>
			<td align="right"> <? echo number_format($result[csf('product_qnty')],2); ?></td>
			<td> <?  ?></td>
		</tr>
		<?
	}
	?>

</tbody>
<tfoot style="background-color:#CCC">
	<th align="right" colspan="2"><b>Total :</b></th>
	<th align="right"><?  echo  number_format($total_fnsh_prod_qty,2); ?></th>
	<th  colspan="2">&nbsp; </th>
</tfoot>
</table>
<!-- Finish Fabric Delevery status -->
<table width="600"  cellspacing="0" border="1" rules="all" class="rpt_table">
<caption><strong>Finish Fabric Delivery status</strong></caption>
<thead style="background-color:#CCC">
	<tr align="center">
		<th width="35">Sl</th>
		<th width="150">Party</th>
		<th width="110">Del. Qty</th>
	   <!-- <th width="110">Avg Rate</th>
		<th width="110">Amount</th>-->
		<th>Remarks</th>
	   </tr>
   </thead>
   <tbody>

	<?
	//and a.delivery_date='$previous_date'
	//and a.delivery_date='$pre_orcl_format_date' condition last date
	$sql_fnsh_feb_del=sql_select("select a.party_id,sum(b.delivery_qty) as delivery_qty from subcon_delivery_mst a,subcon_delivery_dtls b,subcon_ord_dtls c, subcon_ord_mst d where a.status_active=1 and a.is_deleted=0 and a.company_id=$compid and a.id=b.mst_id and b.order_id=d.id and d.subcon_job=c.job_no_mst AND a.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE() group by a.party_id");
	 //select a.party_id,b.remarks,sum(b.delivery_qty)as qty from subcon_delivery_msta,subcon_delivery_dtls b where a.status_active=1 and a.is_deleted=0 and a.company_id=3 and a.id=b.mst_id group by a.party_id,b.remarks; 

	$i=1;
	$total_fnsh_feb_del_qty='';
	//$total_fnsh_feb_del_amount='';
	foreach($sql_fnsh_feb_del as $result)
	{
		//$amount=$result[csf('delivery_qty')]*$result[csf('rate')];
		//$avg_rate=($result[csf('delivery_qty')]/$amount);
		$total_fnsh_feb_del_qty +=$result[csf('delivery_qty')];
		//$total_fnsh_feb_del_amount 	+=$amount;
		?>
		<tr>
			<td> <? echo $i++; ?></td>
			<td> <? echo $party_lib_array[$result[csf('party_id')]]; ?></td>
			<td align="right"> <? echo number_format($result[csf('delivery_qty')],2); ?></td>
			<!--<td align="right"> <? //echo number_format($avg_rate,1); ?></td>
				<td align="right"> <? //echo number_format($amount,2); ?></td>-->
				<td> <?  ?></td>
			</tr>
			<?
		}
		?>
	</tbody>
	<tfoot style="background-color:#CCC">
		<th align="right" colspan="2"><b>Total :</b></th>
		<th align="right"><?  echo number_format($total_fnsh_feb_del_qty,2); ?></th>
<!--  <th>&nbsp; </th>
	<th align="right"><? //echo number_format($total_fnsh_feb_del_amount,2); ?></th> -->
	<th  colspan="2">&nbsp; </th>

</tfoot>
</table>
<!-- Dyeing And Finishing Bill Issue -->
<table width="800"  cellspacing="0" border="1" rules="all" class="rpt_table">
<caption><strong>Dyeing And Finishing Bill Issue</strong></caption>
<thead style="background-color:#CCC">
	<tr align="center">
		<th width="35">Sl</th>
		<th width="150">Party</th>
		<th width="110">Del. Qty</th>
		<th width="110">Avg Rate</th>
		<th width="110">Amount</th>
		<th>Remarks</th>
	</tr>
</thead>
<tbody>

	<?
	//and a.bill_date='$previous_date'
	//and a.bill_date='$pre_orcl_format_date' condition last date
	$sql_dyeing_bill_issue=sql_select("select a.party_id,sum(b.delivery_qty) as delivery_qty,sum(b.amount) as amount,b.rate from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$compid and a.id=b.mst_id AND a.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE() group by a.party_id");
	//echo "select a.party_id,sum(b.delivery_qty) as delivery_qty,sum(b.amount) as amount,b.rate from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$compid and a.id=b.mst_id AND a.insert_date BETWEEN DATE_ADD( CURDATE( ) , INTERVAL -1 DAY )  AND CURDATE() group by a.party_id";

	$i=1;
	$total_bill_issue_qty='';
	$total_bill_issue_amount='';
	foreach($sql_dyeing_bill_issue as $result)
	{
		//$amount=$result[csf('delivery_qty')]*$result[csf('rate')];
		$amount=$result[csf('delivery_qty')]*$result[csf('rate')];
		$avg_rate=$amount/($result[csf('delivery_qty')]);
		//$avg_rate=($result[csf('delivery_qty')]/$result[csf('amount')]);
		$total_bill_issue_qty +=$result[csf('delivery_qty')];
		$total_bill_issue_amount +=$result[csf('amount')];
		?>
		<tr>
			<td> <? echo $i++; ?></td>
			<td> <? echo $party_lib_array[$result[csf('party_id')]]; ?></td>
			<td align="right"> <? echo number_format($result[csf('delivery_qty')],2); ?></td>
			<td align="right"> <? echo number_format($avg_rate,2); ?></td>
			<td align="right"> <? echo number_format($result[csf('amount')],2) ?></td>
			<td> <?  ?></td>
		</tr>
		<?
	}
	?>
</tbody>
<tfoot style="background-color:#CCC">
	<th align="right" colspan="2"><b>Total :</b></th>
	<th align="right"><?  echo number_format($total_bill_issue_qty,2); ?></th>
	<th>&nbsp; </th>
	<th align="right"><? echo number_format($total_bill_issue_amount,2); ?></th>
	<th>&nbsp; </th>

</tfoot>
</table>
<?
$to="";	$mail_item=9;
$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=9 and b.mail_user_setup_id=c.id and a.company_id=$compid  and a.IS_DELETED=0 and a.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";

$mail_sql=sql_select($sql);
foreach($mail_sql as $row)
{

if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
}

$subject="Subcontract Dyeing";
$message="";
$message=ob_get_contents();
ob_clean();
$header=mailHeader();

// $to="sumonrahman@logicsoftbd.com,smbsintl@gmail.com";
//if($to!="") echo sendMailMailer( $to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port );
if($_REQUEST['isview']==1){
	if($to){
		echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
	}else{
		echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
	}
	echo $message;
}
else{
	if($to!="") echo sendMailMailer( $to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port );
}

//echo $to;

 //echo $message;	
}


foreach($company_library as $compid=>$compname) /// Bellow 5% Profitability Orders
{
	$flag=0;	
	ob_start();
	
	
	$sql = "select a.job_no,a.costing_date,b.total_cost,b.cm_cost from wo_pre_cost_mst a,wo_pre_cost_dtls b
	where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$data_array=sql_select($sql);
	foreach( $data_array as $row )
	{ 
		$marginDataArr[$row[csf("job_no")]]['total_material_service_cost']=$row[csf("total_cost")]-$row[csf("cm_cost")];
		$marginDataArr[$row[csf("job_no")]]['cm_cost']=$row[csf("cm_cost")];
		$marginDataArr[$row[csf("job_no")]]['costing_date']=$row[csf("costing_date")];
	}
	
	
	
	?>

	<table width="2150"  cellspacing="0" border="0">
		<tr>
			<td colspan="24" align="center">
				<strong>Company Name:<?php  echo $company_library[$compid]; ?></strong>
			</td>
		</tr>
		<tr>
			<td colspan="24" align="center">
				<b style="font-size:14px;">Bellow 5% Profitability Orders ( Approved Date : <?  echo date("d-m-Y", $a);  ?> )</b>
			</td>
		</tr>
	</table>

	<table width="2150" border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
		<thead style="background-color:#CCC">
			<tr align="center">
				<th rowspan="2" width="35">Sl</th>
				<th rowspan="2" width="80">Job No</th>
				<th rowspan="2" width="100">Buyer</th>
				<th rowspan="2" width="100">Style</th>
				<th rowspan="2" width="80">Min Ship Date</th>
				<th rowspan="2" width="30">SMV</th>
				<th rowspan="2" width="100">Job Qty (Pcs)</th>
				<th rowspan="2" width="100">Total SMV</th>
				<th rowspan="2" width="70">Avg Unit Price</th>
				<th colspan="8">Margin Summary</th>
				<th rowspan="2" width="80">Costing Date</th>
				<th rowspan="2">Dealing Merchant</th>
			</tr>
			<tr>
				<td width="80">Total Order Value</td>
				<td width="80">Total Material & Service Cost</td>
				<td width="80">CM Value</td>
				<td width="80">CM Cost</td>
				<td width="80">Margin</td>
				<td width="80">Margin %</td>
				<td width="80">CPM</td>
				<td width="80">EPM</td>
			</tr>
		</thead>
		<tbody>
			<?php
			$i=0;
			$total_po_qty=0;
			$total_value=0;
			if($db_type==0){$date_diff="DATEDIFF(b.shipment_date,b.po_received_date) as  date_diff,";}
			else{$date_diff="(b.shipment_date - b.po_received_date) as  date_diff,";}

			$sql_mst="select $date_diff a.job_no,a.set_smv,a.set_break_down,a.order_uom,a.dealing_marchant,b.po_number,a.buyer_name,a.style_ref_no,b.po_quantity,b.unit_price,b.shipment_date,b.po_received_date,b.is_confirmed,b.inserted_by from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b";				
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
					if($smv_sting=='')$smv_sting.=$smv;else $smv_sting.='+'.$smv;
					if($set_sting=='')$set_sting.=$set;else $set_sting.=':'.$set;
						$smv_sum+=$smv;
						$set_sum+=$set;

					}
			//............................................
					$tot_pic_qty=$set_sum*$row[csf('po_quantity')];
					$value=$row[csf('po_quantity')]*$row[csf('unit_price')]; 
					$tmsc=($marginDataArr[$row[csf('job_no')]]['total_material_service_cost']/12)*$tot_pic_qty;
					$cmValue=($value-$tmsc);
					$margin_parcent=number_format(($cmValue/$tot_pic_qty)/$row[csf('set_smv')],4);
					if($margin_parcent<5){
						?>	
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td align="center"><? echo $i;?></td>
							<td><? echo $row[csf('job_no')]; ?></td>
							<td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
							<td><? echo $row[csf('style_ref_no')]; ?></td>
							<td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
							<td align="right">
								<? echo $smv_sting;
								if($row[csf('order_uom')]!=1)echo '='.$smv_sum;
								?>
							</td>
							<td align="right"><? echo number_format($row[csf('po_quantity')],2);?></td>
							<td align="center">
								<? 
								echo $unit_of_measurement[$row[csf('order_uom')]]; 
								if($row[csf('order_uom')]!=1)echo '<br>('.$set_sting.')';
								?>
							</td>
							<td align="right"><? echo $tot_pic_qty; $total_po_qty+=$tot_pic_qty ; ?></td>
							<td align="right">
								<? 
								$tot_smv=($row[csf('set_smv')]*$row[csf('po_quantity')]); 
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
							<td align="right"><? echo number_format($tmsc,4);?></td>
							<td align="right"><? $cmValue=($value-$tmsc);echo number_format($cmValue,4);?></td>
							<td align="right"><? $cm_cost=($marginDataArr[$row[csf('job_no')]]['cm_cost']/12)*$tot_pic_qty; 
							echo number_format($cm_cost,4);?></td>
							<td align="right"><?  $margin=$cmValue-$cm_cost;echo number_format($margin,4);?></td>
							<td align="right"><? echo number_format($margin/$value,4);?></td>
							<td align="right"><? echo number_format($margin_parcent,4); ?></td>
							<td align="center"><? echo change_date_format($marginDataArr[$row[csf('job_no')]]['costing_date']);?></td>
							<td><? echo $dealing_merchant_arr[$row[csf('dealing_marchant')]]; ?></td>
						</tr>
						<?
						$flag=1;
					}
				}
				?> 
			</tbody>         
			<tfoot style="background-color:#CCC">
				<th align="right" colspan="10"><b>Total :</b></th>
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
			</tfoot>
		</table>
		<?

		$to="";
		$mail_item=12;
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=12 and b.mail_user_setup_id=c.id and a.company_id=$compid  and a.IS_DELETED=0 and a.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		
		$subject="Bellow 5% Profitability Order List";
		$message="";
		$message=ob_get_contents();
		ob_clean();
		$header=mailHeader();
		
		//$to="saidul@logicsoftbd.com,muktobani@gmail.com,beeresh@logicsoftbd.com";
		// if($to!="" && $flag==1)echo sendMailMailer( $to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port );
	//echo $message;
	if($_REQUEST['isview']==1){
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		if($to!="" && $flag==1)echo sendMailMailer( $to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port );
	}


	}
//exit();


foreach($company_library as $compid=>$compname) /// Daily Order Entry
{
	$flag=0;	
	
	//echo $str_cond_a;die;
	ob_start();
	?>

	<table width="1300"  cellspacing="0" border="0">
		<tr>
			<td colspan="18" align="center">
				<strong>Company Name:<?php  echo $company_library[$compid]; ?></strong>
			</td>
		</tr>

		<tr>
			<td colspan="18" align="center">
				<b style="font-size:14px;">Daily Order Entry ( Date :<?  echo date("d-m-Y", $a);  ?>)</b>
			</td>
		</tr>


	</table>
	<table width="1300" border="1" rules="all" class="rpt_table" id="table_body3">
		<thead>
			<tr align="center">
				<th width="35">Sl</th>
				<th width="80">Job No</th>
				<th width="100">Order No</th>
				<th width="100">Buyer</th>
				<th width="100">Style</th>
				<th width="100">Item</th>
				<th width="100">P.O Rcv Date</th>
				<th width="80">Ship Date</th>
				<th width="50">Lead Time</th>
				<th width="30">SMV</th>
				<th width="100">Order Qty.</th>
				<th width="50">UOM</th>
				<th width="100">Order Qty (Pcs)</th>
				<th width="100">Total SMV</th>
				<th width="70">Unit Price</th>
				<th width="120">Value</th>
				<th width="80">Order Status</th>
				<th>Insert By</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i=0;
			$total_po_qty=0;
			$total_value=0;
			if($db_type==0){$date_diff="DATEDIFF(b.shipment_date,b.po_received_date) as  date_diff,";}
			else{$date_diff="(b.shipment_date - b.po_received_date) as  date_diff,";}

			$sql_mst="select $date_diff a.job_no,a.set_smv,a.set_break_down,a.order_uom,b.po_number,a.buyer_name,a.style_ref_no,b.po_quantity,b.unit_price,b.shipment_date,b.po_received_date,b.is_confirmed,b.inserted_by from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b";				
			$nameArray_mst=sql_select($sql_mst);
			$tot_rows=count($nameArray_mst);
			foreach($nameArray_mst as $row)
			{
				$i++;
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				$set_arr=explode('__',$row[csf('set_break_down')]);
				$item_sting='';
				$smv_sting='';
				$set_sting='';
				$smv_sum=0;
				$set_sum=0;
				foreach($set_arr as $set_data){
					list($item,$set,$smv)=explode('_',$set_data);
					if($item_sting=='')$item_sting.=$garments_item[$item];else $item_sting.=','.$garments_item[$item];
					if($smv_sting=='')$smv_sting.=$smv;else $smv_sting.='+'.$smv;
					if($set_sting=='')$set_sting.=$set;else $set_sting.=':'.$set;
						$smv_sum+=$smv;
						$set_sum+=$set;

					}


					?>	
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center"><? echo $i;?></td>
						<td><? echo $row[csf('job_no')]; ?></td>
						<td><? echo $row[csf('po_number')]; ?></td>
						<td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
						<td><? echo $row[csf('style_ref_no')]; ?></td>
						<td><? echo $item_sting; ?></td>
						<td align="center"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
						<td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
						<td align="center"><? echo $row[csf('date_diff')]; ?></td>
						<td align="right">
							<? echo $smv_sting;
							if($row[csf('order_uom')]!=1)echo '='.$smv_sum;
							?>
						</td>
						<td align="right">
							<? 
							echo number_format($row[csf('po_quantity')],2); 

							?>
						</td>
						<td align="center">
							<? 
							echo $unit_of_measurement[$row[csf('order_uom')]]; 
							if($row[csf('order_uom')]!=1)echo '<br>('.$set_sting.')';
							?>
						</td>
						<td align="right"><? echo $tot_pic_qty=$set_sum*$row[csf('po_quantity')]; $total_po_qty+=$tot_pic_qty ; ?></td>
						<td align="right">
							<? 
							$tot_smv=($row[csf('set_smv')]*$row[csf('po_quantity')]); 
							echo number_format($tot_smv,2); 
							$grund_tot_smv+=$tot_smv; 
							?>
						</td>
						<td align="right"><?php echo number_format($row[csf('unit_price')],2); ?></td>
						<td align="right">
							<?php 
							$value=$row[csf('po_quantity')]*$row[csf('unit_price')]; 
							echo number_format($value,2);
							$total_value+= $value;
							?>
						</td>
						<td><? echo $order_status[$row[csf('is_confirmed')]]; ?></td>
						<td><? echo $user_arr[$row[csf('inserted_by')]]; ?></td>
					</tr>
					<?
					$flag=1;
				}
				if($tot_rows==0)
				{
					?>
					<tr><td colspan="13" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

					<?	
				}
				?> 
			</tbody>         
			<tfoot>
				<th align="right" colspan="10"><b>Total :</b></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right"><?  echo number_format($total_po_qty,2);$total_po_qty=0; ?></th>
				<th align="right"><? echo number_format($grund_tot_smv,2); $grund_tot_smv=0;?></th>
				<th>&nbsp;</th>
				<th align="right"><?  echo number_format($total_value,2); $total_value=0;?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
		<?

		$to="";
		$mail_item=1;
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=1 and b.mail_user_setup_id=c.id and a.company_id=$compid  and a.IS_DELETED=0 and a.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		
		$subject="Daily Order Entry";
		$message="";
		$message=ob_get_contents();
		ob_clean();
		$header=mailHeader();
		//if($to!="" && $flag==1)echo sendMailMailer( $to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port );
		if($_REQUEST['isview']==1){
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo $message;
		}
		else{
			if($to!="" && $flag==1)echo sendMailMailer( $to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port );		
		}

		
	

	}


foreach($company_library as $compid=>$compname)/// Total Activities
{
	
	ob_start();
	?>

	<table width="920">
		<tr>
			<td valign="top" align="center">
				<strong><font size="+2">Total Activities of ( Date :<?  echo date("d-m-Y", $a);  ?>)</font></strong>
			</td>
		</tr>
		<tr>
			<td valign="top" align="center">
				<strong> Company Name: <? echo $company_library[$compid];  ?></strong>
			</td>
		</tr>

		<tr>
			<td valign="top" align="left">
				<table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
					<tr>
						<td colspan="10" height="40" align="center"><strong>Order Received</strong></td>
					</tr>
					<tr>
						<td rowspan="2" width="120" align="center"><strong>Buyer</strong></td>
						<td colspan="3" align="center"><strong>Confirm Order</strong></td>
						<td colspan="3" align="center"><strong>Projected Order</strong></td>
						<td colspan="3" align="center"><strong>Total</strong></td>
					</tr>
					<tr>
						<td width="85" align="center"><strong>Qty.(Pcs)</strong></td>
						<td width="85" align="center"><strong>Value</strong></td>
						<td width="80" align="center"><strong>Avg. Rate</strong></td>
						<td width="85" align="center"><strong>Qty.(Pcs)</strong></td>
						<td width="85" align="center"><strong>Value</strong></td>
						<td width="80" align="center"><strong>Avg. Rate</strong></td>
						<td width="85" align="center"><strong>Qty.(Pcs)</strong></td>
						<td width="85" align="center"><strong>Value</strong></td>
						<td width="85" align="center"><strong>Avg. Rate</strong></td>
					</tr>
					<?
					$total_qnty=array(); $total_value=array(); 

					$sql_mst="select a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=1 $str_cond_b group by a.buyer_name";				
					$nameArray_mst=sql_select($sql_mst);
					$tot_rows2=count($nameArray_mst);
					$flag=0;
					foreach($nameArray_mst as $row)
					{
						$conf_proj_qty=0;$conf_proj_value=0;
						$i++;
						?>
						<tr>
							<td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
							<?php
							for($m=1; $m<=2; $m++)
							{

								$sql_mst2="select sum(b.po_quantity) as po_quantity, sum(b.po_total_price) as po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name like '$compid' and a.buyer_name='".$row[csf('buyer_name')]."' and b.is_confirmed='$m' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=1 $str_cond_b";
								$nameArray_mst2=sql_select($sql_mst2);
								foreach($nameArray_mst2 as $row2)
								{

									?>
									<td align="right">
										<?
										$conf_proj_qty += $row2[csf('po_quantity')];
										echo number_format($row2[csf('po_quantity')],2);

										?>
									</td>
									<td align="right">
										<?
										$conf_proj_value += $row2[csf('po_total_price')]; 
										echo number_format($row2[csf('po_total_price')],2); 
										?>
									</td>                        
									<td align="right">
										<?
										$avg_rate= $row2[csf('po_total_price')]/$row2[csf('po_quantity')];
										echo number_format($avg_rate,2);
										?>
									</td>
									<? 
								}

								if(array_key_exists($m,$total_qnty))
								{
									$total_qnty[$m]+=$row2[csf('po_quantity')];	
								}
								else
								{
									$total_qnty[$m]=$row2[csf('po_quantity')];
								}

								if(array_key_exists($m,$total_value))
								{
									$total_value[$m]+=$row2[csf('po_total_price')];
								}
								else
								{
									$total_value[$m]=$row2[csf('po_total_price')];
								} 
							}
							?>

							<td align="right"><? echo number_format($conf_proj_qty,2);  ?></td>
							<td align="right"><? echo number_format($conf_proj_value,2);  ?></td>
							<td align="right">
								<?
								$avg_rate_tot= $conf_proj_value/$conf_proj_qty; 
								echo number_format($avg_rate_tot,2);  
								?>
							</td>
						</tr>
						<?	
						$flag=1;
					}
					if($tot_rows2==0)
					{
						?>
						<tr><td colspan="10" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

						<?	
					}
					?> 
					<tr>
						<tfoot>
							<th>Total</th>
							<?php
							for($i=1; $i<=2; $i++)
							{
								?>
								<th align="right">
									<?
									$grand_qty+=$total_qnty[$i];   
									echo number_format($total_qnty[$i] ,2)   
									?>
								</th>
								<th align="right">
									<?
									$grand_value+=$total_value[$i];    
									echo number_format($total_value[$i] ,2)   
									?>
								</th>
								<th align="right">
									<?
									$tot_rate=$total_value[$i]/$total_qnty[$i];
									echo number_format($tot_rate,2);
									?>
								</th>
								<?	} ?>
								<th align="right"><?  echo  number_format($grand_qty,2);  ?></th>
								<th align="right"><?  echo  number_format($grand_value,2);  ?></th>
								<th align="right">
									<?
									$grand_rate=$grand_value/$grand_qty;
									echo number_format($grand_rate,2);
									$grand_qty=0;
									$grand_value=0;
									?>
								</th>
							</tfoot>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">
					<table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
						<tr>
							<td colspan="5" height="40" align="center"><strong>Export LC/Sales Contract Receive</strong></td>
						</tr>
						<tr>
							<td width="50" align="center"><strong>SL</strong></td>
							<td width="250" align="center"><strong>Buyer</strong></td>
							<td width="100" align="center"><strong>LC/SC</strong></td>
							<td width="250" align="center"><strong>LC/SC No</strong></td>
							<td width="200" align="center"><strong>Value</strong></td>
						</tr>
						<?

						$i=0; $tot_lc_value=0;
						
						$sql_lc_sc="SELECT sum(lc_value) as lc_sc_value, buyer_name, 1 as type, export_lc_no as no from com_export_lc where beneficiary_name like '$compid' and status_active=1 and is_deleted=0 $str_cond group by buyer_name,export_lc_no
						union all
						SELECT sum(contract_value) as lc_sc_value, buyer_name, 2 as type, contract_no as no from com_sales_contract where beneficiary_name like '$compid' and status_active=1 and is_deleted=0 $str_cond group by buyer_name,contract_no order by buyer_name";

						$nameArray_lc_sc=sql_select($sql_lc_sc);
						
						$tot_rows13=count($nameArray_lc_sc);
						
						
						foreach($nameArray_lc_sc as $row)
						{
							
							$i++;
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
								<td align="center"><? if($row[csf('type')] == 1) echo "LC"; else echo "SC"; ?></td>
								<td><?   echo $row[csf('no')]; ?></td>
								<td align="right">
									<?
									$value= $row[csf('lc_sc_value')];
									echo number_format($value,2); 
									$tot_lc_value += $value;  
									?>
								</td>
							</tr>
							<?	
							$flag=1;
						}
						if($tot_rows13==0)
						{
							?>
							<tr><td colspan="5" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

							<?	
						}
						?> 
						<tr>
							<tfoot>
								<th>&nbsp;</th>
								<th>Total</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th align="right"><?  echo  number_format($tot_lc_value,2);  ?></th>
							</tfoot>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">
					<table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
						<tr>
							<td colspan="5" height="40" align="center"><strong>Back to Back Open</strong></td>
						</tr>
						<tr>
							<td width="50" align="center"><strong>SL</strong></td>
							<td width="250" align="center"><strong>Item Category</strong></td>
							<td width="200" align="center"><strong>Supplier</strong></td>
							<td width="150" align="center"><strong>Value</strong></td>
							<td width="200" align="center"><strong>LC Type</strong></td>
						</tr>
						<?
						$i=0;$tot_bb_value=0;
						
						$sql_back_back="Select supplier_id, sum(lc_value) as lc_value, item_category_id, lc_type_id from com_btb_lc_master_details where importer_id like '$compid' and status_active=1 and is_deleted=0 $str_cond group by supplier_id,item_category_id,lc_type_id";
						
						$nameArray_back_back=sql_select($sql_back_back);
						$tot_rows14=count($nameArray_back_back);
						foreach($nameArray_back_back as $row)
						{
							
							$i++;
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
								<td><? echo $supplier_library[$row[csf('supplier_id')]]; ?></td>
								<td align="right">
									<?
									$value= $row[csf('lc_value')];
									echo number_format($value,2); 
									$tot_bb_value += $value;  
									?>
								</td>
								<td><? echo $lc_type[$row[csf('lc_type_id')]]; ?></td>

							</tr>
							<?	
							$flag=1;
						}
						if($tot_rows14==0)
						{
							?>
							<tr><td colspan="5" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

							<?	
						}
						?> 
						<tr>
							<tfoot>
								<th>&nbsp;</th>
								<th>Total</th>
								<th>&nbsp;</th>
								<th align="right"><?  echo  number_format($tot_bb_value,2);  ?></th>
								<th>&nbsp;</th>
							</tfoot>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">
					<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tr>
							<td colspan="6" height="40" align="center"><strong>Yarn Received</strong></td>
						</tr>

						<tr>
							<td width="50"><strong>SL</strong></td>
							<td width="200" align="center"><strong>Supplier Name</strong></td>
							<td width="275" align="center"><strong>Yarn Description</strong></td>
							<td width="125" align="center"><strong>Qty. (Kg)</strong></td>
							<td width="125" align="center"><strong>Value</strong></td>
							<td width="125" align="center"><strong>Avg. Rate(Tk.)</strong></td>
						</tr>
						<?
						$i=0; $tot_quantity=0; $tot_value=0;
						
						//echo "select a.supplier_id,b.product_name_details,sum(a.cons_quantity) as cons_quantity,b.avg_rate_per_unit, sum(a.cons_amount) as cons_amount from inv_transaction a, product_details_master b where b.id=a.prod_id and a.company_id like '$compid' and a.item_category=1 and a.transaction_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_a group by a.supplier_id,a.prod_id";
						
						$sql_rec="select a.supplier_id,b.product_name_details,sum(a.cons_quantity) as cons_quantity,sum(b.avg_rate_per_unit) as avg_rate_per_unit, sum(a.cons_amount) as cons_amount from inv_transaction a, product_details_master b where b.id=a.prod_id and a.company_id like '$compid' and a.item_category=1 and a.transaction_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_a group by a.supplier_id,a.prod_id,b.product_name_details";				
						$nameArray_rec=sql_select($sql_rec);
						$tot_rows3=count($nameArray_rec);
						foreach($nameArray_rec as $row)
						{
							
							$i++;
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><? echo $supplier_library[$row[csf('supplier_id')]]; ?></td>
								<td><? echo $row[csf('product_name_details')]; ?></td>

								<td align="right">
									<?
									$tot_quantity += $row[csf('cons_quantity')]; 
									echo number_format($row[csf('cons_quantity')],2); 
									?>
								</td>                        
								<td align="right">
									<? 
									$value= $row[csf('cons_amount')];
									echo number_format($value,2); 
									$tot_value += $value;  
									?>
								</td>
								<td align="right">
									<?
									$rate= $value/$row[csf('cons_quantity')];
									echo number_format($rate,2);
									?>
								</td>
							</tr>
							<?	
							$flag=1;
						}
						if($tot_rows3==0)
						{
							?>
							<tr><td colspan="6" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

							<?	
						}
						?> 
						<tr>
							<tfoot>
								<th>&nbsp;</th>
								<th>Total</th>
								<th>&nbsp;</th>
								<th align="right"><? echo number_format($tot_quantity ,2)  ?></th>
								<th align="right"><?  echo  number_format($tot_value,2);  ?></th>
								<th align="right">&nbsp;</th>
							</tfoot>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">
					<table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
						<tr>
							<td colspan="6" height="40" align="center"><strong>Yarn Issued</strong></td>
						</tr>

						<tr>
							<td width="50"><strong>SL</strong></td>
							<td width="275" align="center"><strong>Yarn Description</strong></td>
							<td width="200" align="center"><strong>Purpose</strong></td>
							<td width="125" align="center"><strong>Qty. (Kg)</strong></td>
							<td width="125" align="center"><strong>Value</strong></td>
							<td width="125" align="center"><strong>Avg. Rate(Tk.)</strong></td>
						</tr>
						<?
						$i=0; $tot_quantity=0; $tot_value=0;
						
						$sql_issue="select c.issue_purpose, b.product_name_details,sum(a.cons_quantity) as cons_quantity,sum(b.avg_rate_per_unit) as avg_rate_per_unit,sum(a.cons_amount) as cons_amount from inv_transaction a, product_details_master b, inv_issue_master c where b.id=a.prod_id and c.id=a.mst_id and a.company_id like '$compid' and a.item_category=1 and a.transaction_type=2 and c.entry_form=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $str_cond_a group by c.issue_purpose,a.prod_id,b.product_name_details";				
						$nameArray_issue=sql_select($sql_issue);
						
						$tot_rows4=count($nameArray_issue);
						
						foreach($nameArray_issue as $row)
						{
							
							$i++;
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><? echo $row[csf('product_name_details')]; ?></td>
								<td><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>

								<td align="right">
									<?
									$tot_quantity += $row[csf('cons_quantity')]; 
									echo number_format($row[csf('cons_quantity')],2); 
									?>
								</td>                        
								<td align="right">
									<? 
									$value= $row[csf('cons_amount')];
									echo number_format($value,2); 
									$tot_value += $value;  
									?>
								</td>
								<td align="right">
									<?
									$rate= $value/$row[csf('cons_quantity')];
									echo number_format($rate,2);
									?>
								</td>
							</tr>
							<?	
							$flag=1;
						}
						if($tot_rows4==0)
						{
							?>
							<tr><td colspan="6" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

							<?	
						}
						?> 
						<tr>
							<tfoot>
								<th>&nbsp;</th>
								<th>Total</th>
								<th>&nbsp;</th>
								<th align="right"><? echo number_format($tot_quantity ,2)  ?></th>
								<th align="right"><?  echo  number_format($tot_value,2);  ?></th>
								<th align="right">&nbsp;</th>
							</tfoot>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">
					<table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
						<tr>
							<td colspan="6" height="40" align="center"><strong>Knitting Production</strong></td>
						</tr>

						<tr>
							<td width="50" align="center"><strong>SL</strong></td>
							<td width="275" align="center"><strong>Source</strong></td>
							<td width="200" align="center"><strong>Total Prod.</strong></td>
							<td width="125" align="center"><strong>QC Pass Qty.</strong></td>
							<td width="125" align="center"><strong>Reject Qty.</strong></td>
							<td width="125" align="center"><strong>Reject %</strong></td>
						</tr>
						<?
						$i=0; $sub_tot_production=0; $tot_grey_receive_qnty=0; $tot_reject_fabric_receive=0;
						
						$sql_knit="select a.knitting_source,sum(b.grey_receive_qnty) as grey_receive_qnty,sum(b.reject_fabric_receive) as reject_fabric_receive from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.company_id like '$compid' and a.entry_form=2 and a.knitting_source in(1,3) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b group by a.knitting_source";				
						$nameArray_knit=sql_select($sql_knit);
						
						$tot_rows5=count($nameArray_knit);
						
						foreach($nameArray_knit as $row)
						{
							
							$i++;
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
								<td align="right">
									<?
									$tot_production = $row[csf('grey_receive_qnty')]+$row[csf('reject_fabric_receive')]; 
									echo number_format($tot_production,2); 
									$sub_tot_production += $tot_production; 
									?>
								</td>
								<td align="right">
									<?
									echo number_format($row[csf('grey_receive_qnty')],2); 
									$tot_grey_receive_qnty += $row[csf('grey_receive_qnty')]; 
									?>
								</td>                 
								<td align="right">
									<? 
									echo number_format($row[csf('reject_fabric_receive')],2);
									$tot_reject_fabric_receive += $row[csf('reject_fabric_receive')]; 
									?>
								</td>
								<td align="right">
									<?
									$reject_percent= $row[csf('reject_fabric_receive')]/$tot_production;
									echo number_format($reject_percent,4);
									?>
								</td>
							</tr>
							<?	
							$flag=1;
						}
						if($tot_rows5==0)
						{
							?>
							<tr><td colspan="6" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

							<?	
						}
						?> 
						<tr>
							<tfoot>
								<th>&nbsp;</th>
								<th>Total</th>
								<th align="right"><? echo number_format($sub_tot_production ,2)  ?></th>
								<th align="right"><? echo number_format($tot_grey_receive_qnty ,2)  ?></th>
								<th align="right"><? echo number_format($tot_reject_fabric_receive,2);  ?></th>
								<th align="right">
									<? 
									$tot_reject_percent= $tot_reject_fabric_receive/$sub_tot_production; 
									echo  number_format($tot_reject_percent,4);  
									?>
								</th>
							</tfoot>
						</tr>
					</table>
				</td>
			</tr>


			<tr>
				<td valign="top" align="left">
					<table width="60%" cellpadding="0" cellspacing="0" rules="all" border="1">
						<tr>
							<td colspan="3" height="40" align="center"><strong>Dyeing Completed</strong></td>
						</tr>

						<tr>
							<td width="50" align="center"><strong>SL</strong></td>
							<td width="325" align="center"><strong>Source</strong></td>
							<td width="250" align="center"><strong>Qty. (Kg)</strong></td>
						</tr>
						<?
						$i=0;
						
						$sql_dyeing="select a.load_unload_id,sum(b.batch_weight) as batch_weight from pro_fab_subprocess a, pro_batch_create_mst b where b.id=a.batch_id and a.company_id like '$compid' and a.load_unload_id=2 and a.entry_form=35 and a.result=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_a group by a.load_unload_id";					
						$nameArray_dyeing=sql_select($sql_dyeing);
						
						$tot_rows6=count($nameArray_dyeing);
						$tot_receive_qnty=0;
						foreach($nameArray_dyeing as $row)
						{
							
							$i++;
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><? echo $loading_unloading[$row[csf('load_unload_id')]]; ?></td>
								<td align="right">
									<?
									echo number_format($row[csf('batch_weight')],2); 
									$tot_receive_qnty += $row[csf('batch_weight')]; 
									?>
								</td>
							</tr>
							<?	
							$flag=1;
						}
						if($tot_rows6==0)
						{
							?>
							<tr><td colspan="3" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

							<?	
						}
						?> 
						<tr>
							<tfoot>
								<th>&nbsp;</th>
								<th>Total</th>
								<th align="right"><? echo number_format($tot_receive_qnty ,2)  ?></th>
							</tfoot>
						</tr>
					</table>
				</td>
			</tr>


			<tr>
				<td valign="top" align="left">
					<table width="60%" cellpadding="0" cellspacing="0" rules="all" border="1">
						<tr>
							<td colspan="3" height="40" align="center"><strong>Finish Fabric Production</strong></td>
						</tr>

						<tr>
							<td width="50" align="center"><strong>SL</strong></td>
							<td width="325" align="center"><strong>Source</strong></td>
							<td width="250" align="center"><strong>Total Prod.</strong></td>
						</tr>
						<?
						$i=0;
						
						$sql_finish="select a.knitting_source,sum(b.receive_qnty) as receive_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.company_id like '$compid' and a.entry_form=7 and a.knitting_source in(1,3) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b group by a.knitting_source";				
						$nameArray_finish=sql_select($sql_finish);
						
						$tot_rows7=count($nameArray_finish);
						$sub_receive_qnty=0;
						foreach($nameArray_finish as $row)
						{
							
							$i++;
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
								<td align="right">
									<?
									echo number_format($row[csf('receive_qnty')],2); 
									$sub_receive_qnty += $row[csf('receive_qnty')]; 
									?>
								</td>
							</tr>
							<?	
							$flag=1;
						}
						if($tot_rows7==0)
						{
							?>
							<tr><td colspan="3" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

							<?	
						}
						?> 
						<tr>
							<tfoot>
								<th>&nbsp;</th>
								<th>Total</th>
								<th align="right"><? echo number_format($sub_receive_qnty ,2)  ?></th>
							</tfoot>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">
					<table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
						<tr>
							<td colspan="6" height="40" align="center"><strong>Fabric Issued to Cutting and Cutting Production</strong></td>
						</tr>

						<tr>
							<td width="50" align="center"><strong>SL</strong></td>
							<td width="275" align="center"><strong>Source</strong></td>
							<td width="200" align="center"><strong>Fab. Issued (Kg)</strong></td>
							<td width="125" align="center"><strong>Qty. (Pcs)</strong></td>
							<td width="125" align="center"><strong>Reject Qty.</strong></td>
							<td width="125" align="center"><strong>Reject %</strong></td>
						</tr>
						<?
						$i=0;
						
						$sql_fab_issue="select a.knit_dye_source,sum(b.issue_qnty) as issue_qnty,a.company_id,a.knit_dye_source from inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.company_id like '$compid' and a.entry_form=18 and a.knit_dye_source in(1,3) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b  group by a.knit_dye_source,a.company_id";		
						$nameArray_fab_issue=sql_select($sql_fab_issue);
						
						$tot_rows8=count($nameArray_fab_issue);
						$tot_issue_qnty=0;$tot_cutting=0;$tot_reject=0;
						foreach($nameArray_fab_issue as $row)
						{
							
							$i++;
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><? echo $knitting_source[$row[csf('knit_dye_source')]]; ?></td>
								<td align="right">
									<?
									echo number_format($row[csf('issue_qnty')],2); 
									$tot_issue_qnty += $row[csf('issue_qnty')]; 
									?>
								</td>
								<td align="right">
									<?
									$cutting=return_field_value("sum(a.production_quantity)", "pro_garments_production_mst a", "a.company_id='".$row[csf('company_id')]."' and a.production_source='".$row[csf('knit_dye_source')]."' and a.production_type=1  and a.status_active=1 and a.is_deleted=0 $str_cond_d");

									echo number_format($cutting,2); 
									$tot_cutting += $cutting; 
									?>
								</td>                 
								<td align="right">
									<?
									$reject=return_field_value("sum(a.reject_qnty)", "pro_garments_production_mst a", "a.company_id='$row[company_id]'  and a.production_source='$row[knit_dye_source]' and a.production_type=1  and a.status_active=1 and a.is_deleted=0 $str_cond_d"); 
									echo number_format($reject,2);
									$tot_reject += $reject; 

									?>
								</td>
								<td align="right">
									<?
									$reject_per= $reject/$cutting;
									echo number_format($reject_per,4);
									?>
								</td>
							</tr>
							<?	
							$flag=1;
						}
						if($tot_rows8==0)
						{
							?>
							<tr><td colspan="6" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

							<?	
						}
						?> 
						<tr>
							<tfoot>
								<th>&nbsp;</th>
								<th>Total</th>
								<th align="right"><? echo number_format($tot_issue_qnty ,2)  ?></th>
								<th align="right"><? echo number_format($tot_cutting,2);  ?></th>
								<th align="right"><? echo number_format($tot_reject,2);?>
								</th>
								<th>&nbsp;</th>
							</tfoot>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">
					<table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
						<tr>
							<td colspan="8" height="40" align="center"><strong>Sewing Completed</strong></td>
						</tr>

						<tr>
							<td width="50" align="center"><strong>SL</strong></td>
							<td width="200" align="center"><strong>Buyer</strong></td>
							<td width="150" align="center"><strong>Good Qty. (Pcs)</strong></td>
							<td width="125" align="center"><strong>Reject Qty.</strong></td>
							<td width="125" align="center"><strong>Alter Qty.</strong></td>
							<td width="125" align="center"><strong>Spot Qty.</strong></td>
							<td width="125" align="center"><strong>Total</strong></td>
							<td width="125" align="center"><strong>FOB Value</strong></td>
						</tr>
						<?
						$pro_qnty=array();
						$rej_qnty=array();
						$alter_qnty=array();
						$spot_qnty=array();
						$total_qnty=array();
						$fob_val=array();
						
						
						$tot_production_quantity=0;
						$tot_reject_qnty=0;
						$tot_alter_qnty=0;
						$tot_spot_qnty=0;
						$tot_all=0;
						$tot_fob_val=0;
						
						$sql = "select c.buyer_name, a.production_quantity, a.reject_qnty, a.alter_qnty, a.spot_qnty, b.unit_price from pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.company_id like '$compid' and a.production_type=5 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.shiping_status=1 $str_cond_a";

						$sew_sql = sql_select($sql);
						foreach($sew_sql as $sew_array)
						{
							$pro_qnty[$sew_array[csf("buyer_name")]]+=$sew_array[csf("production_quantity")];
							$rej_qnty[$sew_array[csf("buyer_name")]]+=$sew_array[csf("reject_qnty")];
							$alter_qnty[$sew_array[csf("buyer_name")]]+=$sew_array[csf("alter_qnty")];
							$spot_qnty[$sew_array[csf("buyer_name")]]+=$sew_array[csf("spot_qnty")];
							$total_qnty[$sew_array[csf("buyer_name")]]+=$sew_array[csf("production_quantity")]+$sew_array[csf("reject_qnty")]+$sew_array[csf("alter_qnty")]+$sew_array[csf("spot_qnty")];
							$fob_val[$sew_array[csf("buyer_name")]]+=($sew_array[csf("production_quantity")]+$sew_array[csf("reject_qnty")]+$sew_array[csf("alter_qnty")]+$sew_array[csf("spot_qnty")])*$sew_array[csf("unit_price")];
						}


						$i=0;
						foreach($pro_qnty as $buyer_id=>$row)
						{
							
							$i++;
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><? echo $buyer_library[$buyer_id]; ?></td>
								<td align="right">
									<?
									echo number_format($pro_qnty[$buyer_id],2);
									$tot_production_quantity += $pro_qnty[$buyer_id]; 
									?>
								</td>
								<td align="right">
									<?
									echo number_format($rej_qnty[$buyer_id],2);
									$tot_reject_qnty += $rej_qnty[$buyer_id]; 
									?>
								</td>                 
								<td align="right">
									<?
									echo number_format($alter_qnty[$buyer_id],2);
									$tot_alter_qnty += $alter_qnty[$buyer_id]; 
									?>
								</td>
								<td align="right">
									<?
									echo number_format($spot_qnty[$buyer_id],2);
									$tot_spot_qnty += $spot_qnty[$buyer_id]; 
									?>
								</td>
								<td align="right">
									<?
									$total= $total_qnty[$buyer_id];
									echo number_format($total,2);
									$tot_all += $total; 
									?>
								</td>
								<td align="right">
									<?
									$fob= $fob_val[$buyer_id];
									echo number_format($fob,2);
									$tot_fob_val += $fob; 
									?>
								</td>
							</tr>
							<?	
							$flag=1;
						}
						?> 
						<tr>
							<td>&nbsp;</td>
							<td align="center"><b>Total</b></td>
							<td align="right"><b><? echo number_format($tot_production_quantity,2)  ?></b></td>
							<td align="right"><b><? echo number_format($tot_reject_qnty,2);  ?></b></td>
							<td align="right"><b><? echo number_format($tot_alter_qnty,2); ?></b></td>
							<td align="right"><b><? echo number_format($tot_spot_qnty,2); ?></b></td>
							<td align="right"><b><? echo number_format($tot_all,2); ?></b></td>
							<td align="right"><b><? echo number_format($tot_fob_val,2); ?></b></td>
						</tr>
						<tr>
							<tfoot>
								<th>&nbsp;</th>
								<th>In %</th>
								<th align="right">
									<?
									$production_quantity_per= $tot_production_quantity/$tot_all;
									echo number_format($production_quantity_per,2)  
									?>
								</th>
								<th align="right">
									<?
									$reject_qnty_per= $tot_reject_qnty/$tot_all; 
									echo number_format($reject_qnty_per,2)  
									?>
								</th>
								<th align="right">
									<?
									$alter_qnty_per= $tot_alter_qnty/$tot_all;  
									echo number_format($alter_qnty_per,2)  
									?>
								</th>
								<th align="right">
									<?
									$spot_qnty_per= $tot_spot_qnty/$tot_all;   
									echo number_format($spot_qnty_per,2)  
									?>
								</th>
								<th align="right">
									<? 
									//echo number_format($tot_all,2)  
									?>
								</th>
								<th align="right">
									<? 
									//echo number_format($tot_all,2)  
									?>
								</th>
							</tfoot>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">
					<table width="70%" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tr>
							<td colspan="5" height="40" align="center"><strong>Garments Finishing</strong></td>
						</tr>

						<tr>
							<td width="50" align="center"><strong>SL</strong></td>
							<td width="300" align="center"><strong>Buyer</strong></td>
							<td width="170" align="center"><strong>Qty. (Pcs)</strong></td>
							<td width="180" align="center"><strong>Number of Carton</strong></td>
							<td width="200" align="center"><strong>%</strong></td>                    
						</tr>
						<?
						$i=0;
						
						
						$tot_prod_qty=0;
						$tot_carton_qty=0;
						$tot_percent_qty=0;

						$sql_fab_issue="select c.buyer_name, sum(a.production_quantity) as production_quantity, sum(a.carton_qty) as carton_qty from pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.company_id like '$compid' and a.production_type=8 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.shiping_status=1 $str_cond_a group by c.buyer_name";	

						$nameArray_fab_issue=sql_select($sql_fab_issue);
						
						$tot_rows10=count($nameArray_fab_issue);
						
						foreach($nameArray_fab_issue as $row)
						{
							
							$i++;
							$final_qty=return_field_value("sum(a.production_quantity)", "pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c", "a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.company_id like '$compid' and a.production_type=8 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.shiping_status=1 $str_cond_a");
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
								<td align="right">
									<?
									echo number_format($row[csf('production_quantity')],2); 
									$tot_prod_qty += $row[csf('production_quantity')]; 
									?>
								</td>
								<td align="right">
									<?
									echo number_format($row[csf('carton_qty')],2); 
									$tot_carton_qty += $row[csf('carton_qty')]; 
									?>
								</td>
								<td align="right">
									<?
									$tot_percent=($row[csf('production_quantity')]*100)/$final_qty;
									echo number_format($tot_percent,4); 
									$tot_percent_qty += $tot_percent; 
									?>
								</td>
							</tr>
							<?	
							$flag=1;
						}
						if($tot_rows10==0)
						{
							?>
							<tr><td colspan="5" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

							<?	
						}
						?> 
						<tr>
							<tfoot>
								<th>&nbsp;</th>
								<th>Total</th>
								<th align="right"><? echo number_format($tot_prod_qty,2);  ?></th>
								<th align="right"><? echo number_format($tot_carton_qty,2);  ?></th>
								<th align="right"><? echo number_format($tot_percent_qty,2);?></th>
							</tfoot>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">
					<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tr>
							<td colspan="7" height="40" align="center"><strong>Final Inspection</strong></td>
						</tr>
						<tr>
							<td width="50" align="center"><strong>SL</strong></td>
							<td width="150" align="center"><strong>Job No</strong></td>
							<td width="250" align="center"><strong>Buyer</strong></td>
							<td width="100" align="center"><strong>Order No</strong></td> 
							<td width="100" align="center"><strong>Inspection Qty</strong></td> 
							<td width="125" align="center"><strong>Shipment Date</strong></td>     
							<td width="150" align="center"><strong>Inspection Status</strong></td>                        
						</tr>
						<?
						$i=0;
						
						
						$sql_fab_issue="select a.inspection_status,c.job_no,c.buyer_name,b.po_number,a.inspection_qnty,b.shipment_date from pro_buyer_inspection a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.id in(SELECT MAX(id) FROM pro_buyer_inspection where inspection_status in(1,2,3) $str_cond GROUP BY po_break_down_id) and c.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";	
						
						
						$nameArray_fab_issue=sql_select($sql_fab_issue);
						
						$tot_rows11=count($nameArray_fab_issue);
						
						foreach($nameArray_fab_issue as $row)
						{
							
							$i++;
							
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><? echo $row[csf('job_no')]; ?></td>
								<td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
								<td><? echo $row[csf('po_number')]; ?></td>
								<td align="right"><? echo $row[csf('inspection_qnty')]; ?></td>
								<td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
								<td align="center"><? echo $inspection_status[$row[csf('inspection_status')]]; ?></td>
							</tr>
							<?	
							$flag=1;
						}
						if($tot_rows11==0)
						{
							?>
							<tr><td colspan="6" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

							<?	
						}
						?> 
					</table>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">
					<table width="70%" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tr>
							<td colspan="4" height="40" align="center"><strong>Ex-factory Done</strong></td>
						</tr>

						<tr>
							<td width="50" align="center"><strong>SL</strong></td>
							<td width="400" align="center"><strong>Buyer</strong></td>
							<td width="200" align="center"><strong>Delv. Qty. (Pcs)</strong></td>
							<td width="200" align="center"><strong>FOB Value</strong></td>
						</tr>
						<?
						$ex_fac_qty=array();
						$ex_fac_val=array();
						
						$tot_ex_factory_qnty=0;
						$tot_ex_factory_val=0;
						
						$ex_sql = sql_select("select c.buyer_name,a.ex_factory_qnty,b.unit_price from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status in(1,2,3) and c.is_deleted=0 and c.status_active=1 $str_cond_a");
						foreach($ex_sql as $ex_array)
						{
							$ex_fac_qty[$ex_array[csf("buyer_name")]]+=$ex_array[csf("ex_factory_qnty")];
							$ex_fac_val[$ex_array[csf("buyer_name")]]+=$ex_array[csf("ex_factory_qnty")]*$ex_array[csf("unit_price")];
						}
						
						
						$i=0;
						$sql_fab_issue="select c.buyer_name from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status in(1,2,3) and c.is_deleted=0 and c.status_active=1 $str_cond_a group by c.buyer_name";				
						$nameArray_fab_issue=sql_select($sql_fab_issue);
						
						$tot_rows12=count($nameArray_fab_issue);
						
						foreach($nameArray_fab_issue as $row)
						{
							
							$i++;
							
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
								<td align="right">
									<?
									echo number_format($ex_fac_qty[$row[csf("buyer_name")]],2);
									$tot_ex_factory_qnty += $ex_fac_qty[$row[csf("buyer_name")]]; 
									?>
								</td>
								<td align="right">
									<?
									echo number_format($ex_fac_val[$row[csf("buyer_name")]],2);
									$tot_ex_factory_val += $ex_fac_val[$row[csf("buyer_name")]]; 
									?>
								</td>
							</tr>
							<?	
							$flag=1;
						}
						if($tot_rows12==0)
						{
							?>
							<tr><td colspan="4" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

							<?	
						}
						?> 
						<tr>
							<tfoot>
								<th>&nbsp;</th>
								<th>Total</th>
								<th align="right"><? echo number_format($tot_ex_factory_qnty,2);  ?></th>
								<th align="right"><? echo number_format($tot_ex_factory_val,2);  ?></th>
							</tfoot>
						</tr>
					</table>
				</td>
			</tr>
			<!-- ADD HERE EX-Factory Completed Yesterday (Buyer Name,Job No, PO No) -->
			<!-- This part add by Reza start -->
			<tr>
				<td>
					<table width="70%" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tr>
							<td colspan="4" height="40" align="center"><strong>Full Shipment</strong></td>
						</tr>

						<tr>
							<td width="50" align="center"><strong>SL</strong></td>
							<td width="400" align="center"><strong>Buyer Name</strong></td>
							<td width="200" align="center"><strong>Job No</strong></td>
							<td width="200" align="center"><strong>PO No</strong></td>
						</tr>
						<?
                    	//$sql_full_ship="select b.job_no_mst,b.po_number,c.buyer_name from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.shiping_status = 3 and b.shiping_status=3 and c.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=1 and c.is_deleted=0 and c.status_active=1 $str_cond_d group by b.job_no_mst,b.po_number,c.buyer_name";

						$sql_full_ship="select b.job_no_mst,b.po_number,c.buyer_name from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.shiping_status = 3 and b.shiping_status=3 and c.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $str_cond_d group by b.job_no_mst,b.po_number,c.buyer_name";


						$fullShipArray=sql_select($sql_full_ship);
						$tot_full_ship=count($fullShipArray);
						foreach($fullShipArray as $row){
							$i++;
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
								<td><? echo $row[csf('job_no_mst')]; ?></td>
								<td><? echo $row[csf('po_number')]; ?></td>
							</tr>
							<?
							$flag=1;
						}
						if($tot_full_ship==0)
						{
							?>
							<tr><td colspan="4" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

							<?	
						}
						?>

					</table>
				</td>
			</tr>
			<!-- full shipment end -->
			<tr>
				<td>
					<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tr>
							<td colspan="8" height="40" align="center"><strong>Leftover After Shipment</strong></td>
						</tr>

						<tr>
							<td width="50" align="center"><strong>SL</strong></td>
							<td align="center" width="150"><strong>Buyer Name</strong></td>
							<td align="center" width="130"><strong>Job No</strong></td>
							<td align="center" width="150"><strong>Style</strong></td>
							<td align="center" width="120"><strong>PO No</strong></td>
							<td align="center" width="100"><strong>Fin Qty</strong></td>
							<td align="center" width="100"><strong>Ex-Fac Qty</strong></td>
							<td align="center"><strong>Leftover Qty</strong></td>
						</tr>
						<?
					//$str_cond_d=" and a.insert_date between '01-Feb-2015' and '01-Mar-2015 11:59:59 PM'";
						$sql_leftover="select sum(a.ex_factory_qnty) as ex_factory_qnty,b.job_no_mst,b.po_number,c.buyer_name,c.style_ref_no,sum(d.production_quantity) as finish_quantity from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c,pro_garments_production_mst d where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.po_break_down_id=d.po_break_down_id and a.shiping_status = 3 and d.production_type=8 and b.shiping_status=3 and c.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=3 and c.is_deleted=0 and c.status_active=1 $str_cond_d group by b.job_no_mst,b.po_number,c.buyer_name,style_ref_no";
						
						 //echo $sql_leftover;
						$leftoverArray=sql_select($sql_leftover);
						$tot_leftover=count($leftoverArray);
						$i=1;
						foreach($leftoverArray as $row){

							$leftover_qty=($row[csf('finish_quantity')]-$row[csf('ex_factory_qnty')]);
							if($leftover_qty){
								?>
								<tr>
									<td align="center"><? echo $i; ?></td>
									<td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
									<td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
									<td><? echo $row[csf('style_ref_no')]; ?></td>
									<td><? echo $row[csf('po_number')]; ?></td>
									<td align="right"><? echo $row[csf('finish_quantity')]; ?></td>
									<td align="right"><? echo $row[csf('ex_factory_qnty')]; ?></td>
									<td align="right"><? echo $leftover_qty; ?></td>
								</tr>
								<?
								$i++;
								$flag=1;
							}
						}
						if($tot_leftover==0)
						{
							?>
							<tr><td colspan="8" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

							<?	
						}
						?>

					</table>
				</td>
			</tr>
			<!- This part add by Reza end -->
			<tr>
				<td valign="top" align="left">
					<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tr>
							<td colspan="5" height="40" align="center"><strong>PC Received</strong></td>
						</tr>
						<tr>
							<td width="50" align="center"><strong>SL</strong></td>
							<td width="100" align="center"><strong>LC/SC</strong></td>
							<td width="250" align="center"><strong>LC/SC No</strong></td>
							<td width="250" align="center"><strong>Loan No</strong></td>
							<td width="200" align="center"><strong>Amount</strong></td>
						</tr>
						<?
						$i=0; $tot_loan_amount=0;

						$sql_pre_export="select c.export_type,c.lc_sc_id,max(b.loan_number) as loan_number,sum(c.amount) loan_amount from com_pre_export_finance_mst a, com_pre_export_finance_dtls b, com_pre_export_lc_wise_dtls c where a.id=b.mst_id and b.id=c.pre_export_dtls_id and a.beneficiary_id like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b group by c.export_type, c.lc_sc_id";

						$nameArray_pre_export=sql_select($sql_pre_export);
						$tot_rows15=count($nameArray_pre_export);
						foreach($nameArray_pre_export as $row)
						{

							$i++;
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td align="center"><? if($row[csf('export_type')] == 1) echo "LC"; else echo "SC"; ?></td>
								<td align="center">
									<? 
									if($row[csf('export_type')] == 1) 
									{
										$lc_no=return_field_value("export_lc_no", "com_export_lc", "id='$row[lc_sc_id]' and status_active=1 and is_deleted=0");
										echo $lc_no;
									}
									else
									{
										$sales_cont_no=return_field_value("contract_no", "com_sales_contract", "id='$row[lc_sc_id]' and status_active=1 and is_deleted=0"); 
										echo $sales_cont_no;
									}
									?>
								</td>
								<td><?   echo $row[csf('loan_number')]; ?></td>

								<td align="right">
									<?
									$value= $row[csf('loan_amount')];
									echo number_format($value,2); 
									$tot_loan_amount += $value;  
									?>
								</td>
							</tr>
							<?	
							$flag=1;
						}
						if($tot_rows15==0)
						{
							?>
							<tr><td colspan="5" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

							<?	
						}
						?> 
						<tr>
							<tfoot>
								<th>&nbsp;</th>
								<th>Total</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th align="right"><?  echo  number_format($tot_loan_amount,2);  ?></th>
							</tfoot>
						</tr>
					</table>
				</td>
			</tr>


			<tr>
				<td valign="top" align="left">
					<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tr>
							<td colspan="6" height="40" align="center"><strong>Export Proceed Realized</strong></td>
						</tr>
						<tr>
							<td width="50" align="center"><strong>SL</strong></td>
							<td width="200" align="center"><strong>Buyer</strong></td>
							<td width="100" align="center"><strong>LC/SC</strong></td>
							<td width="150" align="center"><strong>LC/SC No</strong></td>
							<td width="150" align="center"><strong>Realized</strong></td>
							<td width="200" align="center"><strong>Short Realized</strong></td>
						</tr>
						<?
						$i=0; $tot_realized=0; $tot_short_realized=0;

						$sql_realization_invoice="select a.buyer_id,c.is_lc,c.lc_sc_id,b.type,sum(b.document_currency) as tot_document_currency from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_invoice_ship_mst c where a.id=b.mst_id and a.invoice_bill_id=c.id and a.benificiary_id like '$compid' and a.is_invoice_bill=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b group by a.buyer_id,c.is_lc,c.lc_sc_id,b.type";



						$nameArray_realization_invoice=sql_select($sql_realization_invoice);
						$tot_rows16=count($nameArray_realization_invoice);
						foreach($nameArray_realization_invoice as $row_invoice)
						{
							$i++;
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><?   echo $buyer_library[$row_invoice[csf('buyer_id')]]; ?></td>
								<td align="center"><? if($row_invoice[csf('is_lc')] == 1) echo "LC"; else echo "SC"; ?></td>
								<td>
									<? 
									if($row_invoice[csf('is_lc')] == 1) 
									{
										$lc_no=return_field_value("export_lc_no", "com_export_lc", "id='$row_invoice[lc_sc_id]' and status_active=1 and is_deleted=0");
										echo $lc_no;
									}
									else
									{
										$sales_cont_no=return_field_value("contract_no", "com_sales_contract", "id='$row_invoice[lc_sc_id]' and status_active=1 and is_deleted=0"); 
										echo $sales_cont_no;
									}
									?>
								</td>
								<td align="right">
									<? 
									if($row_invoice[csf('type')] == 1) 
									{
										echo number_format($row_invoice[csf('tot_document_currency')],2);
										$tot_realized+= $row_invoice[csf('tot_document_currency')];
									}
									?>
								</td>
								<td align="right">
									<? 
									if($row_invoice[csf('type')] == 0) 
									{
										echo number_format($row_invoice[csf('tot_document_currency')],2);
										$tot_short_realized+= $row_invoice[csf('tot_document_currency')];
									}
									?>
								</td>
							</tr>
							<?	
							$flag=1;
						}
						$sql_realization_bill="select a.buyer_id,c.is_lc,c.lc_sc_id,b.type,sum(b.document_currency) as tot_document_currency from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c where a.id=b.mst_id and a.invoice_bill_id=c.doc_submission_mst_id and a.benificiary_id like '$compid' and a.is_invoice_bill=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b group by a.buyer_id,c.is_lc,c.lc_sc_id,b.type";




						$nameArray_realization_bill=sql_select($sql_realization_bill);
						$tot_rows17=count($nameArray_realization_bill);
						foreach($nameArray_realization_bill as $row_bill)
						{
							$i++;
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><?   echo $buyer_library[$row_bill[csf('buyer_id')]]; ?></td>
								<td align="center"><? if($row_bill[csf('is_lc')] == 1) echo "LC"; else echo "SC"; ?></td>
								<td>
									<? 
									if($row_bill[csf('is_lc')] == 1) 
									{
										$lc_no=return_field_value("export_lc_no", "com_export_lc", "id='$row_bill[lc_sc_id]' and status_active=1 and is_deleted=0");
										echo $lc_no;
									}
									else
									{
										$sales_cont_no=return_field_value("contract_no", "com_sales_contract", "id='$row_bill[lc_sc_id]' and status_active=1 and is_deleted=0"); 
										echo $sales_cont_no;
									}
									?>
								</td>
								<td align="right">
									<? 
									if($row_bill[csf('type')] == 1) 
									{
										echo number_format($row_bill[csf('tot_document_currency')],2);
										$tot_realized+= $row_bill[csf('tot_document_currency')];
									}
									?>
								</td>
								<td align="right">
									<? 
									if($row_bill[csf('type')] == 0) 
									{
										echo number_format($row_bill[csf('tot_document_currency')],2); 
										$tot_short_realized+= $row_bill[csf('tot_document_currency')];
									}
									?>
								</td>
							</tr>
							<?	
							$flag=1;
						}
						$tot_count=$tot_rows16+$tot_rows17;
						if($tot_count==0)
						{
							?>
							<tr><td colspan="6" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>

							<?	
						}
						?> 
						<tr>
							<tfoot>
								<th colspan="4" align="center">Total :</th>
								<th align="right"><?  echo number_format($tot_realized,2);   ?></th>
								<th align="right"><?  echo number_format($tot_short_realized,2);   ?></th>
							</tfoot>
						</tr>
					</table>
				</td>
			</tr>

		</table>
		<?
		$mail_item=2;
		$to="";
		$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=2 and b.mail_user_setup_id=c.id and a.company_id=$compid  and a.IS_DELETED=0 and a.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		$mail_sql2=sql_select($sql2);
		foreach($mail_sql2 as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}

		$subject="Total Activities of ( Date :".date("d-m-Y", $a).")";
 		//$subject="Yesterday  total activities";
		$message="";
		$message=ob_get_contents();
		ob_clean();
		$header=mailHeader();
		// if($to!="" && $flag==1){echo sendMailMailer( $to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port );}
		
	    if($_REQUEST['isview']==1){
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo $message;
		}
		else{
			if($to!="" && $flag==1){echo sendMailMailer( $to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port );}		
		}

		
		
		
	}



	?> 