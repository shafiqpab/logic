<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
require '../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
//require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

	$user_maill_arr=return_library_array("select id,USER_EMAIL from USER_PASSWD","id","USER_EMAIL");

	function fridaydayCount($from, $to, $day = 5) 
	{
		$from = new DateTime($from);
		$to   = new DateTime($to);

		$wF = $from->format('w');
		$wT = $to->format('w');
		if ($wF < $wT)       $isExtraDay = $day >= $wF && $day <= $wT;
		else if ($wF == $wT) $isExtraDay = $wF == $day;
		else                 $isExtraDay = $day >= $wF || $day <= $wT;

		return floor($from->diff($to)->days / 7) + $isExtraDay;
	}
	function saturdaydayCount($from, $to, $day = 6) 
	{
		$from = new DateTime($from);
		$to   = new DateTime($to);

		$wF = $from->format('w');
		$wT = $to->format('w');
		if ($wF < $wT)       $isExtraDay = $day >= $wF && $day <= $wT;
		else if ($wF == $wT) $isExtraDay = $wF == $day;
		else                 $isExtraDay = $day >= $wF || $day <= $wT;

		return floor($from->diff($to)->days / 7) + $isExtraDay;
	}

	list($sysId,$mailId)=explode('__',$data);
	$sysId=str_replace('*',',',$sysId);
	$mailArr[]=str_replace('*',',',$mailId);
	 

	$company_library =return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0 AND id not in(2,3,5,4)", "id", "company_name");
	
	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	$tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),1))),'','',1);
	$day_after_tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),2))),'','',1);
	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),0))),'','',1);
	$prev_thirty_date = change_date_format(date('Y-m-d H:i:s', strtotime('-30 day', strtotime($current_date))),'','',1); 
	$prev_fifteen_date = change_date_format(date('Y-m-d H:i:s', strtotime('-15 day', strtotime($current_date))),'','',1); 
	$actual_date="is null";
	$date_cond=" and a.pi_date between '$prev_fifteen_date' and '$current_date'";		
	$po_rcv_date_cond=" and f.po_received_date between '$prev_thirty_date' and '$current_date'";

	 
	//echo $date_cond;
	$sql_lib_company = "select id, company_name from lib_company where status_active=1 and is_deleted=0";
	$sql_lib_company_res=sql_select($sql_lib_company);
	//foreach($sql_lib_company_res as $com)
	foreach($company_library as $compid=>$compname)
	{
		//$compid = 3;
		$company_arr=return_library_array("select id, company_name from lib_company",'id','company_name');
		$supplier_arr=return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyer_arr=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		$designation_array=return_library_array( "select id, custom_designation from lib_designation", "id","custom_designation");
		$dept_array=return_library_array( "select id, department_name from lib_department", "id","department_name");
		$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id","user_full_name");
		$yarnCount_arr=return_library_array( "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1",'id','yarn_count');
		
		$sql_req_wo="SELECT
					a.company_id, b.buyer_id,b.style_ref_no, a.id, a.requ_no, a.basis, a.requisition_date, a.delivery_date,  b.id  AS req_dtls_id,
					b.booking_no, b.count_id, b.yarn_type_id, b.cons_uom AS req_uom, b.quantity AS req_qnty,    b.rate  AS req_rate, b.amount AS req_amt,
					b.job_no, d.wo_date, d.wo_number, d.is_approved AS wo_approved,   e.supplier_order_quantity as wo_qty,  e.rate  AS wo_rate,  e.amount AS wo_amount, MIN(f.po_received_date) AS po_received_date
				FROM
					inv_purchase_requisition_mst   a, inv_purchase_requisition_dtls  b
					LEFT JOIN wo_non_order_info_dtls         e ON b.id = e.requisition_dtls_id
					LEFT JOIN wo_non_order_info_mst          d ON d.id = e.mst_id
					INNER JOIN wo_po_break_down       f ON f.job_no_mst = b.job_no
				WHERE
					a.id = b.mst_id
					AND a.company_id = $compid $po_rcv_date_cond
					AND d.wo_date is null
					AND b.item_category = 1
					AND a.status_active = 1
					AND a.is_deleted = 0
					AND b.status_active = 1
					AND b.is_deleted = 0
					AND a.entry_form = 70
				GROUP BY     a.company_id,    b.buyer_id,    b.style_ref_no,    a.id,    a.requ_no,    a.basis,    a.requisition_date,    a.delivery_date,
					b.id,    b.booking_no,    b.count_id,    b.yarn_type_id,    b.cons_uom,    b.quantity,    b.rate,    b.amount,    b.job_no,    d.wo_date, d.wo_number,
					d.is_approved,  e.supplier_order_quantity,  e.rate,    e.amount";
					
			//echo $sql_req_wo; echo '<br><br>';//die;
			
			$sql_req_wo_res=sql_select($sql_req_wo);
			$req_wo_arr=array();
			foreach ($sql_req_wo_res as $row)
			{
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['requisition_date']=$row[csf('requisition_date')];
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['requ_no']=$row[csf('requ_no')];
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['wo_date']=$row[csf('wo_date')];
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['wo_number']=$row[csf('wo_number')];
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['count_id']=$row[csf('count_id')];
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['yarn_type_id']=$row[csf('yarn_type_id')];
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['buyer_id']=$row[csf('buyer_id')];
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['booking_no']=$row[csf('booking_no')];
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['req_uom']=$row[csf('req_uom')];
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['req_qnty']+=$row[csf('req_qnty')];
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['wo_qty']+=$row[csf('wo_qty')];
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['wo_rate']=$row[csf('wo_rate')];			
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['wo_amount']+=$row[csf('wo_amount')];
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['wo_approved']=$row[csf('wo_approved')];
				$req_wo_arr[$row[csf('job_no')]][$row[csf('requ_no')]][$row[csf('wo_number')]][$row[csf('count_id')]][$row[csf('yarn_type_id')]]['po_received_date']=$row[csf('po_received_date')];
			}
			
			$table_width='1500'."px";
			ob_start();
			//echo '<pre>'; print_r($req_wo_arr); die;
		?>
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
			   <td align="center" width="100%" colspan="16" style="font-size:16px"><strong><? echo "Yarn procurement progress and delay reports [ From Last 30 Days ]"; ?></strong></td>
			</tr>
			<tr>
			   <td align="center" width="100%" colspan="16" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$compid)]; ?></strong></td>
			</tr>
        </table>	
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $table_width; ?>" class="rpt_table" >
			<thead>
				<tr>
					<th width="30" >SL</th>
					<th width="100" >Job No</th>
					<th width="100" >Buyer</th>
					<th width="100" >Style</th>
					<th width="80" >Order Rcv Date</th>
					<th width="80" >Req Date</th>
					<th width="100" >Req No</th>	
					<th width="70" >Req Qty</th>					
					<th width="60" >Count</th>
					<th width="80" >Yarn Type</th>
					<th width="80" >WO Date</th>
					<th width="100" >WO No</th>
					<th width="80" >WO Qty</th>
					<th width="80" >Rate</th>
					<th width="80" >Amount</th>
					<th width="60" >Approved</th>
					<th>Delay</th>
				</tr>
			</thead>
			<tbody>	<?	
				$i=1;
				$tot_wo_qty=0;
				$tot_wo_amount=0;
				foreach ($req_wo_arr as $j_no=> $job_data)
				{
					foreach ($job_data as $req_id=> $req_data)
					{
						foreach ($req_data as $wo_id=> $wo_data)
						{
							foreach ($wo_data as $coun_id=> $count_data)
							{
								foreach ($count_data as $y_type_id=> $row)
								{
											
											if($row['wo_date']!="")
											{
												$dt_start = new DateTime($row['po_received_date']);
												$date_start = $dt_start->format('m/d/Y');
												$date1=date_create($date_start);	
												
												$dt_end = new DateTime($row['wo_date']);
												$date_end = $dt_end->format('m/d/Y');
												$date2=date_create($date_end);
												//$btb_allow_days = $allow_day+$friday;
												
												if($date1>=$date2)
												{
													$diff=date_diff($date2,$date1);
													$delay= $diff->format("%R%a");									
												}
												else
												{
													$diff=date_diff($date1,$date2);
													$delay= $diff->format("%R%a");
												}
												
												if($delay>4)
												{
													$col_color="red";
												}
												else
												{
													$col_color="white";
												}
											}
											else
											{
												$dt_start = new DateTime($pc_date_time);
												$date_start = $dt_start->format('m/d/Y');
												$date1=date_create($date_start);	
												
												$dt_end = new DateTime($row['po_received_date']);
												$date_end = $dt_end->format('m/d/Y');
												$date2=date_create($date_end);
												//$btb_allow_days = $allow_day+$friday;
												
												if($date1>=$date2)
												{
													$diff=date_diff($date2,$date1);
													$delay= $diff->format("%R%a");									
												}
												else
												{
													$diff=date_diff($date1,$date2);
													$delay= $diff->format("%R%a");
												}
												
												if($delay>4)
												{
													$col_color="red";
												}
												else
												{
													$col_color="white";
												}
											}
									if($delay>4)
									{
										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
										?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="30" align="center"><?= $i; ?></td>
												<td width="100"> <?=$j_no;?></td>			          
												<td width="100" align="center"><?= $buyer_arr[$row['buyer_id']]; ?></p></td>
												<td width="100"><p><?= $row['style_ref_no']; ?></p></td>
												<td width="80"  align="center"><p><?= $row['po_received_date']; ?></p></td>
												<td width="80"  align="center"><p><?= $row['requisition_date']; ?></p></td>
												<td width="100" align="center"> <?=   $req_id;?></td>
												<td width="70" align="right"> <?= number_format($row['req_qnty'],2);?></td>
												<td width="60" align="center"><?= $yarnCount_arr[$coun_id];  ?></td>
												<td width="80" align="center"><?= $yarn_type[$y_type_id];  ?></td>												
												<td width="80" align="center"><?= $row['wo_date']; ?></td>
												<td width="100" align="center"><?= $row['wo_number']; ?></td>
												<td width="80" align="center"><?= number_format($row['wo_qty'],2);   ?></td>
												<td width="80" align="center"><?= $row['wo_rate'];  ?></td>
												<td width="80" align="center"><?= number_format($row['wo_amount'],2);  ?></td>
												<td width="60" align="center"><?= $row['wo_approved'];  ?></td>
												<td align="center" bgcolor="<? echo $col_color;?>">
													<?	echo  $delay." Days";  ?>
												</td>
											</tr>
										<?
									
										$i++;
										$tot_req_qnty+=$row['req_qnty'];
										$tot_wo_qty+=$row['wo_qty'];
										$tot_wo_amount+=$row['wo_amount'];
										
										$gtot_req_qnty+=$row['req_qnty'];
										$gtotal_wo_qty += $row['wo_qty'] ;
										$gtotal_wo_amount += $row['wo_amount'];
									}
								}
							}
						}
					}							
				}
	?>
	
		</tbody> 
		<tfoot>					
			<tr bgcolor="<? echo "#FFCCCC"; ?>">
				<td colspan="7" align="right"><strong> Grand Total :</strong></td>
				<td align="right"><strong><?= number_format($gtot_req_qnty,2);?></strong></td>
				<td colspan="4" align="right"><strong></strong></td>
				<td width="80" align="center"><strong><?= number_format($gtotal_wo_qty,2);?></strong></td>
				<td width="80" align="center"><? ?></td>
				<td width="80" align="center"><strong><?= number_format($gtotal_wo_amount,2);?></strong></td>
				<td width="60" align="center"><?  ?></td>
				<td align="center">			</td>
			</tr>
		</tfoot>
	</table>
	
		
		
		
		<?
		
		$to='al-amin@team.com.bd';$message="";
		
		/*
		
		$sql_mail="SELECT distinct a.company_id, c.email_address,    a.mail_item,    c.user_id,    c.user_type
				FROM   mail_group_mst a, mail_group_child b, user_mail_address  c
				WHERE  b.mail_group_mst_id = a.id   AND b.mail_user_setup_id = c.id and a.company_id=$compid";
		//echo $sql_mail;
		$i=0;
		$mail_sql_res=sql_select($sql_mail);
		foreach($mail_sql_res as $row)
		{
			if($row[csf('email_address')] != 'mizan@team.com.bd')
			{
				if ($to=="")  
					$to=$row[csf('email_address')]; 
				else $to=$to.", ".$row[csf('email_address')]; 
			}
		}
		*/
		
		$header=mailHeader();
		
		$subject="Yarn Procurement Progress and Delays  Report based on Order Received date from last 30 days.";
		$message=ob_get_contents();
		ob_clean();
			
			$att_file_arr=array();			
			//$filename="PI_Tracking_and_delays_report_".$company_library[$compid].".xls";
			$filename="Yarn_procurement_progress_and_delays_report_".$compid.".xls";
			$create_new_doc = fopen($filename, 'w');
			$is_created = fwrite($create_new_doc,$message);
			$att_file_arr[]=$filename.'**'.$filename;
			
			$mail_body = "Please see the attached file for Yarn Procurement Progress and Delays Report [ From Last 30 Days ] of ".$company_library[$compid];
			
			//echo $mail_body;echo '<pre>';echo '<pre>';
			//echo $to;echo '<pre>';echo '<pre>';
			//echo $to;echo '<pre>';echo '<pre>';
			//echo $company_library[$compid]."_id: ".$compid;
			
			$to='al-amin@team.com.bd';
			/*
			
			if($compid==1)
			{
				$to=$to.", ".'merchandiser@gramtechknit.com, mir.forhad@team.com.bd';
			}
			elseif($compid==2){
				$to=$to.", ".'allmerchandiser@marsstitchltd.com, mir.forhad@team.com.bd';
			}
			elseif($compid==3){
				$to=$to.", ".'bfl_merchandisers@brothersfashion-bd.com, mir.forhad@team.com.bd';
			}
			elseif($compid==4){
				$to=$to.", ".'allmerchant@4ajacket.com, mir.forhad@team.com.bd';
			}
			elseif($compid==5){
				$to=$to.", ".'cbm_merchandisers@cbm-international.com, mir.forhad@team.com.bd';
			}
			else{
				$to=$to.", ".'al-amin@team.com.bd';
			}
			
			*/
			
			$to=$to.", ".'nakib@team.com.bd, shofiq@team.com.bd';
			//$to=$to.", ".'al-amin@team.com.bd, allmerchandiser@marsstitchltd.com, cbm_merchandisers@cbm-international.com, bfl_merchandisers@brothersfashion-bd.com, allmerchant@4ajacket.com, merchandiser@gramtechknit.com';
			//echo $to;echo '<pre>'; //die;
			//
			
			if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
			
			//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );}
			//echo $message;
 
	} // End Company
	
	//  allmerchandiser@marsstitchltd.com; cbm_merchandisers@cbm-international.com; bfl_merchandisers@brothersfashion-bd.com; allmerchant@4ajacket.com ; merchandiser@gramtechknit.com
	
?>

</body>
</html>