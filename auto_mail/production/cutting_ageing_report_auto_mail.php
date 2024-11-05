<?php

	date_default_timezone_set("Asia/Dhaka");
	include('../../includes/common.php');
	require_once('../../mailer/class.phpmailer.php');
	require_once('../../includes/common.php');
	require_once('../setting/mail_setting.php');

	if($db_type==0)
	{ 
		$current_date = date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0)));
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date= date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
	}

	$company_arr 	= return_library_array("select id, company_name from lib_company where status_active=1","id","company_name");
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

	$color_arr=return_library_array("select id,color_name from lib_color","id","color_name");

	$ageRangeTex=array(1=> "1-30", 2=> "31-60", 3=> "61-90", 4=> "91-120", 5=> "121-150", 6=> "150-180", 7=> "Above 180");

	$sql_result = "Select a.job_no, b.company_name, b.buyer_name , b.style_ref_no, c.color_id, c.batch_no, d.pay_mode, d.booking_no from ppl_cut_lay_mst a, wo_po_details_master b, pro_batch_create_mst c , wo_booking_mst d where a.job_no=b.job_no and d.job_no=a.job_no and a.entry_date='11-Sep-2022' and d.booking_no=c.booking_no group by a.job_no, b.company_name, b.buyer_name , b.style_ref_no, c.color_id, c.batch_no , d.pay_mode, d.booking_no order by b.company_name DESC";

  	$cutting_ageing_data = sql_select($sql_result);

  	$batch_qty_arr =array();
  	$total_batch_arr = array();
  	$total_batch_qty_arr = 0;
  	$total = 0;
  	$con = connect();

  	$s_batch = '';


  	foreach($cutting_ageing_data as $row){
 
  		$s_book = "'".$row[csf('booking_no')]."'";
  		$s_batch = "'".$row[csf('batch_no')]."'";

		$rID2=execute_query("insert into tmp_booking_no (userid, booking_no) values (999999999999,".$s_book.")");

		if($rID2)
		{
			oci_commit($con);
		}

		$rID3=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_no) values (999999999999,".$s_batch.")");

		if($rID3)
		{
			oci_commit($con);
		}
  	}

  	$sql_subprocess = "select a.company_id, a.batch_no, a.production_date, c.color_id, c.booking_no, e.job_no, e.company_name, e.buyer_name , e.style_ref_no from pro_fab_subprocess a, tmp_batch_or_iss b, pro_batch_create_mst c, wo_booking_mst d, wo_po_details_master e where a.batch_no=b.batch_issue_no and b.userid=999999999999 and a.status_active=1 and a.is_deleted=0 and a.load_unload_id=2 and a.entry_form=35 and a.batch_no=c.batch_no and d.booking_no=c.booking_no and d.job_no=e.job_no group by a.company_id, a.batch_no, a.production_date, c.color_id, c.booking_no, e.job_no, e.company_name, e.buyer_name , e.style_ref_no";

  	$booking_sql = "select a.company_id, a.receive_date, b.booking_no, b.job_no, e.color_id, e.batch_no, f.style_ref_no, f.buyer_name from inv_receive_master a, pro_finish_fabric_rcv_dtls b, tmp_booking_no c, wo_booking_mst d , pro_batch_create_mst e, wo_po_details_master f
where a.id=b.mst_id and c.booking_no=b.booking_no and c.userid=999999999999 and a.receive_basis=1 and d.booking_no=c.booking_no and d.booking_no=e.booking_no and d.job_no=f.job_no group by a.company_id, a.receive_date, b.booking_no, b.job_no, e.color_id, e.batch_no, f.style_ref_no, f.buyer_name";

  	$booking_data = sql_select($booking_sql);

  	$subprocess_data = sql_select($sql_subprocess);

  	$import_date_arr = array();

  	$production_date_arr = array();

  	foreach($booking_data as $row){

  		//$import_date_arr[$row[csf('booking_no')]] = $row[csf('receive_date')];

  		$import_date_arr[$row[csf('company_id')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('color_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]] = $row[csf('receive_date')];
  	}

  	foreach($subprocess_data as $row){

  		$production_date_arr[$row[csf('company_id')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('color_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]] = $row[csf('production_date')];
  	}

  	$r_id1=execute_query("delete from tmp_booking_no where userid=999999999999");
	if($r_id1){

		oci_commit($con);
	}

	$r_id2=execute_query("delete from tmp_batch_or_iss where userid=999999999999");
	if($r_id2){

		oci_commit($con);
	}
  	foreach($cutting_ageing_data as $row){

  		if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5){

  			$production_date = $production_date_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('color_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]];

			$ageOfDays = datediff("d",$production_date,$previous_date);
		}
		else{
			
			$production_date = $import_date_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]][$row[csf('color_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]];
			//$production_date = $import_date_arr[$row[csf('booking_no')]];

			$ageOfDays = datediff("d",$production_date,$previous_date);
		}

  		$total_batch_qty_arr += 1;

  		if($ageOfDays>=1 && $ageOfDays<=30){

  			$batch_qty_arr[1] += $ageOfDays;

  			$total_batch_arr[1] += 1;
  		}
  		else if($ageOfDays>=31 && $ageOfDays<=60){

  			$batch_qty_arr[2] += $ageOfDays;
  			$total_batch_arr[2] += 1;
  		}
  		else if($ageOfDays>=61 && $ageOfDays<=90){

  			$batch_qty_arr[3] += $ageOfDays;
  			$total_batch_arr[3] += 1;
  		}
  		else if($ageOfDays>=91 && $ageOfDays<=120){

  			$batch_qty_arr[4] += $ageOfDays;
  			$total_batch_arr[4] += 1;
  		}
  		else if($ageOfDays>=121 && $ageOfDays<=150){

  			$batch_qty_arr[5] += $ageOfDays;
  			$total_batch_arr[5] += 1;
  		}
  		else if($ageOfDays>=151 && $ageOfDays<=180){

  			$batch_qty_arr[6] += $ageOfDays;
  			$total_batch_arr[6] += 1;
  		}
  		else if($ageOfDays>=181){

  			$batch_qty_arr[7] += $ageOfDays;
  			$total_batch_arr[7] += 1;
  		}
  	}
  	ob_start();	
?>

<div style="width:900px;" align="left">
	
	<table width="900" border="0" align="center">
		<tr>
			<td align="center" colspan="9">Cutting Ageing Report</td>
		</tr>
		<tr>
			<td align="center" colspan="9"> Date:<?= $previous_date;?></td>
		</tr>
	</table><br />
	<table width="300" border="1" rules="all" align="left">
		<thead>
			<th align="center" width="50">Sl</th>
			<th align="center" width="100">Age Days</th>
			<th align="center" width="100">%</th>
		</thead>
		<tbody>
			<?php
				ksort($batch_qty_arr);
				$j=1;
				foreach ($batch_qty_arr as $ageKeyRangeTex => $ageDataTex) 
					{ 
			?>
			<tr>
				<td align="center"><?php echo $j;?></td>
				<td align="center"><?php echo $ageRangeTex[$ageKeyRangeTex]; ?></td>
				<td align="right"><?php echo number_format(($total_batch_arr[$ageKeyRangeTex]/$total_batch_qty_arr)*100,2);?>%</td>
			</tr>
			<?php 
						$j++;
						$total += ($total_batch_arr[$ageKeyRangeTex]/$total_batch_qty_arr)*100;
					}
			?>
			<tr>
				<td align="right" colspan="2">Total:</td>
				<td align="right"><?php echo number_format($total);?>%</td>
			</tr>
		</tbody>
	</table>
	<table width="900" style="margin-top: 10px;" border="1" rules="all" align="left">
		<thead>
			<tr>
				<th colspan="9">Detail Report</th>
			</tr>
			<tr>
				<th width="50">Sl</th>
				<th width="250">Working Company</th>
				<th width="200">Buyer</th>
				<th width="150">Style</th>
				<th width="150">Paymode</th>
				<th width="150">Color</th>
				<th width="150">Batch Number</th>
				<th width="300">Dyeing Unload Or Import Date</th>
				<th width="100">Age Day</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				$i=1;
				foreach($cutting_ageing_data as $data){

					if($data[csf('pay_mode')]==3 || $data[csf('pay_mode')]==5){

						$production_date = $production_date_arr[$data[csf('company_name')]][$data[csf('buyer_name')]][$data[csf('style_ref_no')]][$data[csf('color_id')]][$data[csf('booking_no')]][$data[csf('batch_no')]];

						$ageOfDays = datediff("d",$production_date,$previous_date);
					}
					else{

						$production_date = $import_date_arr[$data[csf('company_name')]][$data[csf('buyer_name')]][$data[csf('style_ref_no')]][$data[csf('color_id')]][$data[csf('booking_no')]][$data[csf('batch_no')]];
						//$production_date = $import_date_arr[$data[csf('booking_no')]];
						$ageOfDays = datediff("d",$production_date,$previous_date);
					}

			?>
			<tr>
				<td align="center"><?php echo $i; ?></td>
				<td align="center"><?php echo $company_arr[$data[csf('company_name')]]; ?></td>
				<td align="center"><?php echo $buyer_arr[$data[csf('buyer_name')]]; ?></td>
				<td align="center"><?php echo $data[csf('style_ref_no')]; ?></td>
				<td align="center"><?php echo $pay_mode[$data[csf('pay_mode')]]; ?></ttdh>
				<td align="center"><?php echo $color_arr[$data[csf('color_id')]]; ?></td>
				<td align="center"><?php echo $data[csf('batch_no')]; ?></td>
				<td align="center"><?php echo $production_date; ?></td>
				<td align="center">
					<?php
						echo $ageOfDays;
					?>
				</td>
			</tr>
			<?php
					$i++;
				} 
			?>
		</tbody>
	</table>
</div>

<?
	$message=ob_get_contents();
	ob_clean();

	$to='';
	$mail_item =106;
	$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=$mail_item and b.mail_user_setup_id=c.id and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	// echo $sql;die;
	
	
	$mail_sql=sql_select($sql);
	$receverMailArr=array();
	foreach($mail_sql as $row)
	{
		//$mailAdd="ma.kaiyum1992@gmail.com";
		$receverMailArr[$row[csf('email_address')]]=$row[csf('email_address')];		
	}

	$to=implode(',',$receverMailArr);
	
	
	$subject="Cutting Ageing Report";
	$header=mailHeader();
	if($_REQUEST['isview']==1){
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		//echo $to."<br/>".$subject."<br/>". $message."<br/>". $from_mail."<br/>".$att_file_arr;
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );}
	}
	
?>
