<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');


$company_library 	= return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$buyer_library 		= return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
$user_arr 			= return_library_array( "select id,user_full_name from user_passwd where valid=1","id","user_full_name");

 

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
	
	
	//$previous_date=$current_date='4-Dec-2021';
	
	
	
	if($db_type==0){
		$str_cond_b	=" and b.insert_date between '".$previous_date."' and '".$current_date."'";
	}
	else
	{
		$str_cond_b	=" and b.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	}
	
	
	

foreach($company_library as $compid=>$compname) /// Daily Order Entry
{
	$flag=0;	
	ob_start();
	?>
	<table width="1300"  cellspacing="0" border="0">
		<tr>
			<td colspan="18" align="center">
				<strong>Working Company: <?php  echo $company_library[$compid]; ?></strong>
			</td>
		</tr>
		<tr>
			<td colspan="18" align="center">
				<b style="font-size:14px;">Order Entry Date :<?= date("d-m-Y",strtotime($previous_date));  ?></b>
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

			$sql_mst="
			select $date_diff a.job_no,a.set_smv,a.set_break_down,a.order_uom,b.po_number,a.buyer_name,a.style_ref_no,b.po_quantity,b.unit_price,b.shipment_date,b.po_received_date,b.is_confirmed,b.inserted_by from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.WORKING_COMPANY_ID=$compid and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b
			union all
			select $date_diff a.job_no,a.set_smv,a.set_break_down,a.order_uom,b.po_number,a.buyer_name,a.style_ref_no,b.po_quantity,b.unit_price,b.shipment_date,b.po_received_date,b.is_confirmed,b.inserted_by from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.STYLE_OWNER=$compid and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b
			
			";	
			
			//echo $sql_mst;
						
			$nameArray_mst=sql_select($sql_mst);
			$tot_rows=count($nameArray_mst);
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
							<? echo $smv_sting; if($row[csf('order_uom')]!=1){echo '='.$smv_sum;} ?>
						</td>
						<td align="right"><? echo number_format($row[csf('po_quantity')],2);?></td>
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
		$mail_item=82;
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=82 and b.mail_user_setup_id=c.id and a.company_id=$compid and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		
		$subject="Daily Order Entry by Working Company";
		$message="";
		$message=ob_get_contents();
		ob_clean();
		$header=mailHeader();
		if($to!="" && $flag==1)echo sendMailMailer( $to, $subject, $message, $from_mail );

		if($_REQUEST['isview']==1){
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo $message;
		}
		else{
			if($to!="" && $flag==1)echo sendMailMailer( $to, $subject, $message, $from_mail );
		}


	}



	?> 