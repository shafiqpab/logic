<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

$company_library = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$buyer_library = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
$user_arr = return_library_array( "select id,user_full_name from user_passwd where valid=1","id","user_full_name");
$lib_buyer_brand = return_library_array( "select id,BRAND_NAME from LIB_BUYER_BRAND","id","BRAND_NAME");
$dealing_marchant_sql = sql_select("select id,team_member_name,team_member_email,member_contact_no from lib_mkt_team_member_info where status_active=1 and is_deleted=0");
foreach($dealing_marchant_sql as $row)
{
	$dealing_marchant[$row[csf('id')]]=$row[csf('team_member_name')];
}

$team_leader_sql=sql_select("select ID,TEAM_LEADER_NAME from lib_marketing_team where team_type in (0,1,2) and status_active =1 and is_deleted=0 order by team_leader_name"); //project_type=1 and 
foreach($team_leader_sql as $row)
{
	$team_leader[$row['ID']]=$row['TEAM_LEADER_NAME'];
}
 
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
$current_date = $_REQUEST['view_date']? date("Y-m-d H:i:s", strtotime($_REQUEST['view_date'])):$current_date;

$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
$previous_3month_date = change_date_format(date('Y-m-d H:i:s', strtotime('-180 day', strtotime($current_date))),'','',1); 

$current_date=$previous_date;
 
$a=mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));

if($db_type==0){
	$str_cond_b	=" and b.insert_date between '".$previous_date."' and '".$previous_date."'";
}
else
{
	$str_cond_b	=" and b.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
}
//echo $str_cond_b;die;
 

foreach($company_library as $compid => $compname)
{ 
	$htmlHeaderArr = array(); $htmlBodyArr = array(); $htmlFooterArr = array();
	ob_start();
	?>
		<table width="1500" cellspacing="0" border="0">
			<tr>
				<td colspan="20" align="center">
					<strong><?= $company_library[$compid]; ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="20" align="center">
					<b style="font-size:14px;">Order Entry Date :<?= $previous_date;  ?></b>
				</td>
			</tr>
		</table>
		<table width="1500" border="1" rules="all" class="rpt_table" id="table_body3">
			<thead>
				<tr align="center" bgcolor="#CCC">
					<th width="80">Job No</th>
					<th width="200">Internal Ref. Number</th>
					<th width="100">Order No</th>
					<th width="100">Buyer</th>
					<th width="100">Brand</th>
					<th width="100">Style</th>
					<th width="100">Item</th>
					<th width="100">P.O Rcv Date</th>
					<th width="80">Ship Date</th>
					<th width="50">Lead Time</th>
					<th width="50">PHD/PCD</th>
					<th width="30">SMV</th>
					<th width="30">Print</th>
					<th width="30">Embroidery</th>
					<th width="30">Wash</th>
					<th width="100">Order Qty.</th>
					<th width="50">UOM</th>
					<th width="100">Order Qty (Pcs)</th>
					<th width="100">Total SMV</th>
					<th width="70">Unit Price</th>
					<th width="120">Value</th>
					<th width="80">Order Status</th>
					<th width="80">Team Leader</th>
					<th width="80">Dealing Merchant</th>
					<th>Insert By</th>
				</tr>
			</thead>
			<tbody>
	<?
	$htmlHeaderArr[$compid] = ob_get_contents();
	ob_clean();

		
	if($db_type == 0){$date_diff = "DATEDIFF(b.shipment_date,b.po_received_date) as  date_diff,";}
	else{$date_diff="(b.shipment_date - b.po_received_date) as  date_diff,";}
		
	$sql_mst = "select $date_diff a.TEAM_LEADER,a.DEALING_MARCHANT,a.job_no,a.set_smv,a.set_break_down,a.order_uom,b.po_number,a.buyer_name,a.brand_id,a.style_ref_no,b.po_quantity,b.unit_price,b.shipment_date,b.pack_handover_date,b.po_received_date,b.is_confirmed,b.inserted_by,b.grouping,c.embelishment,c.embro,c.wash,sum(c.PRINTSEQ) as PRINTSEQ,sum(c.WASHSEQ) as WASHSEQ,sum(c.EMBROSEQ) as EMBROSEQ from wo_po_details_master a, wo_po_break_down b,WO_PO_DETAILS_MAS_SET_DETAILS c where a.job_no=b.job_no_mst and b.job_id=c.job_id and a.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b group by a.TEAM_LEADER,a.DEALING_MARCHANT,a.job_no,a.set_smv,a.set_break_down,a.order_uom,b.po_number,a.buyer_name,a.brand_id,a.style_ref_no,b.po_quantity,b.unit_price,b.shipment_date,b.pack_handover_date,b.po_received_date,b.is_confirmed,b.inserted_by,b.grouping,c.embelishment,c.embro,c.wash";
	// echo $sql_mst;die;
	$nameArray_mst = sql_select($sql_mst);
	$order_entry_arr = array();
	foreach($nameArray_mst as $row)
	{
		$key = $row['BUYER_NAME'].'**'.$row['BRAND_ID'];
		$order_entry_arr[$key][] = $row;
	}
	unset($nameArray_mst);

		// echo "<pre>";
        // print_r($order_entry_arr);die;
        // die;

	foreach($order_entry_arr as $buyer_brand => $nameArray_mst){
		$i=0;$flag=0;
		$total_po_qty = 0; $total_value = 0;
		ob_start();
	
			foreach($nameArray_mst as $row)
			{
				$i++;
				$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
				$set_arr = explode('__',$row[csf('set_break_down')]);
				$item_sting = '';
				$smv_sting = '';
				$set_sting = '';
				$smv_sum = 0;
				$set_sum = 0;
					foreach($set_arr as $set_data){
						list($item,$set,$smv) = explode('_',$set_data);
						if($item_sting == '')$item_sting.=$garments_item[$item];else $item_sting.=','.$garments_item[$item];
						if($smv_sting == '')$smv_sting.=$smv;else $smv_sting.='+'.$smv;
						if($set_sting == '')$set_sting.=$set;else $set_sting.=':'.$set;
							$smv_sum += $smv;
							$set_sum += $set;
					}
					?>	
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td><? echo $row[csf('job_no')]; ?></td>
						<td><? echo $row[csf('grouping')]; ?></td>
						<td><? echo $row[csf('po_number')]; ?></td>
						<td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
						<td><? echo $lib_buyer_brand[$row[csf('brand_id')]]; ?></td>
						<td><? echo $row[csf('style_ref_no')]; ?></td>
						<td><? echo $item_sting; ?></td>
						<td align="center"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
						<td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
						<td align="center"><? echo $row[csf('date_diff')]; ?></td>
						<td align="center"><? echo change_date_format($row[csf('pack_handover_date')]); ?></td>
						<td align="right">
							<?= $smv_sting;
							if($row[csf('order_uom')]!=1)echo '='.number_format($smv_sum,2);
							?>
						</td>
						<td align="right"><? if($row['EMBELISHMENT'] == 1) echo "Yes"; elseif($row['EMBELISHMENT'] == 2) echo "No";?></td>
						<td align="right"><? if($row['EMBRO'] == 1) echo "Yes"; elseif($row['EMBRO'] == 2) echo "No";?></td>
						<td align="right"><? if($row['WASH'] == 1) echo "Yes"; elseif($row['WASH'] == 2) echo "No";?> </td>
						<td align="right">
							<?= number_format($row[csf('po_quantity')],2);?>
						</td>
						<td align="center">
							<?=$unit_of_measurement[$row[csf('order_uom')]]; 
							if($row[csf('order_uom')]!=1)echo '<br>('.$set_sting.')';
							?>
						</td>
						<td align="right"><? echo $tot_pic_qty=$set_sum*$row[csf('po_quantity')]; ?></td>
						<td align="right">
							<? 
							$tot_smv=($row[csf('set_smv')]*$row[csf('po_quantity')]); 
							echo number_format($tot_smv,2); 
							?>
						</td>
						<td align="right"><?php echo number_format($row[csf('unit_price')],2); ?></td>
						<td align="right">
							<?php 
							$value=$row[csf('po_quantity')]*$row[csf('unit_price')]; 
							echo number_format($value,2);
							?>
						</td>
						<td><?= $order_status[$row[csf('is_confirmed')]]; ?></td>
						<td><?= $team_leader[$row[csf('TEAM_LEADER')]]; ?></td>
						<td><?= $dealing_marchant[$row[csf('DEALING_MARCHANT')]]; ?></td>
						<td><?= $user_arr[$row[csf('inserted_by')]]; ?></td>
					</tr>
					<?
					$flag=1;
					$total_po_qty_arr[$buyer_brand] += $tot_pic_qty;
					$grund_tot_smv_arr[$buyer_brand] += $tot_smv;
					$total_value_arr[$buyer_brand] += $value;
			}
			
		$htmlBodyArr[$buyer_brand] = ob_get_contents();
		ob_clean();
	}//buyer brand




	//print_r($htmlBodyArr);die;
		
		
	
		
	$mail_item = 1;
	$sql = "SELECT A.BUYER_IDS, A.BRAND_IDS, C.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item = $mail_item and b.mail_user_setup_id=c.id and a.company_id = $compid AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql = sql_select($sql);
	$user_buyer_brand_arr = array();
	foreach($mail_sql as $row)
	{
		$BUYER_ID_ARR = explode(',',$row['BUYER_IDS']);
		$BRAND_ID_ARR = explode(',',$row['BRAND_IDS']);

		foreach($BUYER_ID_ARR as $buyreId){
			foreach($BRAND_ID_ARR as $brndId){
				if($buyreId && $brndId){
					$key = $buyreId.'**'.$brndId;
					$user_buyer_brand_arr[$row['EMAIL_ADDRESS']][$key] = $key;
				}
			}	
		}
	}
	
	//print_r($user_buyer_brand_arr);die;
	
	
	foreach($user_buyer_brand_arr as $to => $buyer_brand_arr){
		
		$customHtmlBody = ''; $custom_total_po_qty = 0; $custom_grund_tot_smv = 0; $custom_total_value = 0;
		foreach($buyer_brand_arr as $buyer_brand){
			$customHtmlBody .= $htmlBodyArr[$buyer_brand];

			$custom_total_po_qty += $total_po_qty_arr[$buyer_brand];
			$custom_grund_tot_smv += $grund_tot_smv_arr[$buyer_brand];
			$custom_total_value += $total_value_arr[$buyer_brand];
		}
		ob_start();
		echo $htmlHeaderArr[$compid] . $customHtmlBody;
		?>
		</tbody>         
			<tfoot>
				<th align="right" colspan="9"><b>Total :</b></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th><?= number_format($custom_total_po_qty,2);?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th><?= number_format($custom_grund_tot_smv,2);?></th>
				<th>&nbsp;</th>
				<th><?= number_format($custom_total_value,2);?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
		<small style="font-size:7px;"><?= "Generate Date & Time:".date('d-m-Y h:i:s a');?></small>
		<?
		$message = ob_get_contents();
		ob_clean();

		$subject="Daily Order Entry";
		$header = mailHeader();
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


}//company
?> 