<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
require '../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
//require_once('../mailer/class.phpmailer.php');;
require_once('setting/mail_setting.php');

$user_library=return_library_array("select id,user_full_name from user_passwd where valid=1","id","user_full_name");
$company_library=return_library_array("select id,company_name from lib_company where  status_active=1 and is_deleted=0","id","company_name");
$buyer_library=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
$po_status_library=return_library_array("select id,status_active from wo_po_break_down","id","status_active");
$supplier_library = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if($db_type==0)
{
	$tomorrow = date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d",time()),1)));
	$day_after_tomorrow = date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d",time()),2)));
	$current_date = date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d",time()),0)));
	$prev_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date)));
	$select_fill="DATE_FORMAT(b.update_date, '%d-%m-%Y %H:%i:%s')";
}
else
{
	$tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d",time()),1))),'','',1);
	$day_after_tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d",time()),2))),'','',1);
	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d",time()),0))),'','',1);
	$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
	$prev_fifteen_date = change_date_format(date('Y-m-d H:i:s', strtotime('-15 day', strtotime($current_date))),'','',1); 		
	$select_fill="to_char(b.update_date,'DD-MM-YYYY HH12:MI:SS')";
}
	
foreach($company_library as $compid=>$compname)
{
	//echo $compid; die;
	$sql_count = "select count(*) as rows_num FROM
				    prod_resource_color_size  a
					LEFT JOIN wo_po_break_down          b ON a.po_id = b.id
					LEFT JOIN wo_pre_cost_mst           c ON b.job_no_mst = c.job_no
					INNER JOIN wo_po_details_master      d ON b.job_no_mst = d.job_no
					INNER JOIN prod_resource_dtls        e ON a.mst_id = e.mst_id AND a.dtls_id = e.mast_dtl_id
				WHERE  
				d.company_name = '$compid' and e.pr_date between '$prev_date' and '$current_date'  
				AND a.status_active = 1";
	$result_count = sql_select( $sql_count );
	//echo $sql_count;
		
	//echo $result_count['rows_num'];
	foreach( $result_count as $row) 
	{
		$num = $row[csf('rows_num')];
	}
	//echo $num; 
	if($num>0)
	{

		ob_start();	
		?>
    
		<table>
			<tr>
				<td align="center">
					<strong style="font-size:24px;"> <? echo $compname; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center"><strong>Yesterday Production SMV Vs Order SMV Analysis of ( Date : <? echo date('d-m-Y');?> )</strong></td>
			</tr>
			<tr>
				<td>
				
					 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
					   <thead>
						<tr bgcolor="#999999">
							<th width="50"><strong>SL</strong></th>
							<th width="130"><strong>Buyer</strong></th>
							<th width="150">Style REf</th>
							<th width="90"><strong>PO Status</strong></th>
							<th width="110"><strong>Job No</strong></th>
							<th width="150"><strong>Order No</strong></th>  
							<th width="80"><strong>Order SMV</strong></th>
							<th width="80"><strong>Pre Cost SMV</strong></th>
							<th width="80"><strong>Production SMV</strong></th>                        
							<th><strong>Remarks</strong></th>
							</tr>
					   </thead>
						<?		
						$act_smv = array();
						$actual_smv_sql_bk = "SELECT  a.id, a.mst_id, d.style_ref_no, a.po_id, b.po_number, b.is_confirmed, a.gmts_item_id, a.color_id, a.size_id, a.target_per_line, a.operator, a.helper,
							a.order_type, a.working_hour, NVL(c.sew_smv,0) as sew_smv, NVL(a.actual_smv,0) as actual_smv, d.company_name, d.buyer_name, b.job_no_mst
							FROM  prod_resource_color_size  a
							LEFT JOIN wo_po_break_down b ON a.po_id = b.id
							LEFT JOIN wo_pre_cost_mst c ON b.job_no_mst = c.job_no
							INNER JOIN wo_po_details_master d ON b.job_no_mst = d.job_no
							INNER JOIN prod_resource_dtls e ON a.id = e.mst_id
							WHERE  d.company_name = '$compid' and e.pr_date between '$prev_date' and '$current_date'  AND a.status_active = 1";
							
						$actual_smv_sql = "SELECT p.*, M.sew_smv, M.buyer_name, M.style_ref_no, M.po_number, M.is_confirmed from (
											SELECT
												a.id,
												e.pr_date,
												h.job_no,
												a.po_id,
												a.gmts_item_id,
												a.color_id,
												a.size_id,
												(  SELECT line_name  FROM lib_sewing_line WHERE lib_sewing_line.id = d.LINE_NUMBER)   AS line_name,
												d.LINE_NUMBER,
												a.target_per_line,
												a.operator,
												a.helper,
												a.order_type,
												a.working_hour,
												nvl(a.actual_smv, 0)        AS actual_smv,
												h.company_name,
												e.mst_id,
												d.resource_num,
												a.status_active,
												d.line_marge
											FROM
												prod_resource_color_size     a,
												prod_resource_dtls           e,
												wo_po_break_down             f,
												wo_po_details_master         h,
												prod_resource_mst    d
											WHERE
													a.po_id = f.id
												AND f.job_no_mst = h.job_no
												AND a.mst_id = e.mst_id
												AND e.mst_id = d.id
												AND a.dtls_id = e.mast_dtl_id												
												AND e.pr_date BETWEEN '$prev_date' AND '$current_date'
												AND h.company_name = '$compid' 
												AND a.status_active = 1
												) p
												inner join 
												(
														SELECT
														c.job_no,
														d.style_ref_no,
														b.id,
														b.po_number,
														b.is_confirmed,
														nvl(c.sew_smv, 0) AS sew_smv,
														d.company_name,
														d.buyer_name,
														b.job_no_mst
													FROM
														wo_po_break_down      b, wo_pre_cost_mst       c , wo_po_details_master  d 
													WHERE b.job_no_mst = c.job_no and b.job_no_mst = d.job_no AND     d.company_name = '$compid' 
												) M ON M.id = p.po_id AND M.company_name=p.company_name";
						$actual_smv_result = sql_select( $actual_smv_sql );
						//echo $actual_smv_sql; echo '<br>';
						foreach( $actual_smv_result as $row) 
						{
							$act_smv[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('po_id')]]['actual_smv']=$row[csf('actual_smv')];
							$act_smv[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('po_id')]]['sew_smv']=$row[csf('sew_smv')];
							$act_smv[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('po_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
							$act_smv[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('po_id')]]['color_id']=$row[csf('color_id')];
							$act_smv[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
							$act_smv[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('po_id')]]['line_name']=$row[csf('line_name')];
							$act_smv[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
							$act_smv[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
							$act_smv[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('po_id')]]['is_confirmed']=$row[csf('is_confirmed')];

						}
						//echo '<pre>'; print_r($act_smv); die;
						
						$i=1; $job_no="";
						foreach($act_smv as $com_id=>$com_data) 
						{
							foreach( $com_data as $buy_id=>$buy_data) 
							{
								foreach( $buy_data as $po_id=>$row) 
								{
									
									$job_no=$row["job_no_mst"];
									$sql_ord_smv = "select  set_item_ratio,job_no,smv_pcs,embelishment,embro,wash,gmtsdying
										from  wo_po_details_mas_set_details where  job_no = '$job_no'";
									//echo $sql_ord_smv;//die;	
									$sql_ord_smv_count = sql_select( $sql_ord_smv );						
									foreach( $sql_ord_smv_count as $rows) 
									{
										$ord_smv = $rows[csf('smv_pcs')]/$rows[csf('set_item_ratio')];
										$ord_embelishment = $rows[csf('embelishment')];
										$ord_embro = $rows[csf('embro')];
										$ord_wash = $rows[csf('wash')];
										$ord_gmtsdying = $rows[csf('gmtsdying')];							
									}	
									//echo $ord_smv; die;									
									$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
									
									//if(($row["actual_smv"]>$row["sew_smv"]) || ($row["actual_smv"]==0))
									if($row['actual_smv']>$row['sew_smv'])
									{
										if($row['actual_smv']>$row['sew_smv'])
										{ $tdcolor="#ff5733";}	
										else{$tdcolor="#FFFFFF";}
										?>
												
										<tr bgcolor="<? echo $bgcolor ; ?>">
											<td align="center"><? echo $i;?></td>
											<td><? echo $buyer_library[$buy_id];?></td> 
											<td><? echo $row["style_ref_no"]; ?></td>
											<td align="center"><? if($row["style_ref_no"]==1) echo "Confirm"; else echo "Projection";?></td>
											<td align="center"><? echo $row["job_no_mst"];?></td>
											<td><? echo $row["po_number"];?></td>
											
										   
											<td align="center"><? echo $ord_smv;?></td>
											<td align="center"><? echo $row['sew_smv'];?></td>                       
											<td align="right" bgcolor="<? echo $tdcolor;?>">
											<? echo $row['actual_smv'];
											 ?>
											</td>
											<td><? ?></td>
									   </tr>
										<?
									
										$i++;
										$grand_ord_smv+=$ord_smv;
										$grand_pre_smv+=$row['sew_smv'];
										$grand_prod_smv+=$row['actual_smv'];
									}
								}
							}
						}
						?>
						<tfoot bgcolor="#CCCCCC">
							<td colspan="6"><strong>Total</strong></td>
							<td align="right"><strong><? echo $grand_ord_smv;?></strong></td>
							<td align="right"><strong><? echo $grand_pre_smv;?></strong></td>
							<td align="right"><strong><? echo $grand_prod_smv;?></strong></td>
							<td></td>
						</tfoot>
					 </table>
				</td>
			</tr>
		</table>

		<?		
		$to="";	
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=8 and b.mail_user_setup_id=c.id and a.company_id=$compid  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1";
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			//if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		
		$subject = "Yesterday Production SMV Vs Order SMV Analysis of ".$company_arr[$compid];
		
		$mail_body = "Yesterday Production SMV Vs Order SMV Analysis of ".$company_arr[$compid];
		
		$message="";	
		$header=mailHeader();
		$message=ob_get_contents();
		ob_clean();			
		$att_file_arr=array();
		$filename="Production_SMV_Vs_Order_SMV_".$company_arr[$compid].".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,$message);
		$att_file_arr[]=$filename.'**'.$filename;
		
		$to='al-amin@team.com.bd,sajib@team.com.bd,joy@team.com.bd';
		
	
		if($compid==1)
		{
			$to=$to.", ".'minhajul.arefin@gramtechknit.com, ie.shahadat@gramtechknit.com';
			if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		}
		elseif($compid==2){
			$to=$to.", ".'mahbub@marsstitchltd.com, azmal.huda@team.com.bd, mainul.islam@team.com.bd, tuhin.Rasul@team.com.bd';
			if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		}
		elseif($compid==3){
			$to=$to.", ".'pavel@brothersfashion-bd.com, emdad@brothersfashion-bd.com, tuhin.Rasul@team.com.bd';
			if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		}
		elseif($compid==4){
			$to=$to.", ".'sohel@4ajacket.com, allmarchant@4ajacket.com,zahedul@4ajacket.com,enamul@4ajacket.com,abdur.rahim@4ajacket.com,store3@4ajacket.com,zillur.frp@4ajacket.com,ashraful@4ajacket.com,shandhi.rozario@4ajacket.com,anwar.hossain@4ajacket.com';
			if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		}
		elseif($compid==5){
			$to=$to.", ".'anwar@cbm-international.com, amir@cbm-international.com, nazmul@cbm-international.com, tuhin.Rasul@team.com.bd';
			if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		}
		else{
			$to=$to.", ".'al-amin@team.com.bd';
			if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		}
	
		
		//$to=$to.", ".'al-amin@team.com.bd, raihan.uddin@team.com.bd, tuhin.Rasul@team.com.bd, azmal.huda@team.com.bd, emdadul.huque@team.com.bd, iliasur.rahman@team.com.bd, shah.alam@marsstitchltd.com, anwar@cbm-international.com, kutub@brothersfashion-bd.com, mainul.islam@team.com.bd, productionpfl@brothersfashion-bd.com, nazmul@cbm-international.com';
		//if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		//echo $message;
		//unset($act_smv);
		//die;
	}
 
}


?> 