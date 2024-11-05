<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

$company_library=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$buyer_library=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
// $supplier_library = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");

$team_leader=return_library_array("select id,team_leader_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 order by team_leader_name","id","team_leader_name");
$dealing_merchand=return_library_array("select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name","id","team_member_name");

$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
		
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s", $strtotime),0))),'','',1);	
$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-2 day', strtotime($current_date))),'','',1); 	
$date_cond	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";

	

foreach($company_library as $compid=>$compname)
{
	ob_start();	

			// and a.job_no in ('FAL-21-00902','FAL-21-00912','FAL-21-00889','FAL-21-00890','FAL-21-00891')

			  $sqlOrder="select a.job_no,a.dealing_marchant,a.team_leader,a.insert_date,a.buyer_name,a.style_ref_no,a.season,b.po_number,a.gmts_item_id from wo_po_details_master a ,wo_po_break_down b where a.company_name=$compid and a.job_no=b.job_no_mst	  and a.status_active =1 and a.is_deleted=0  and b.status_active =1 and b.is_deleted=0  $date_cond ";
			//   echo  $sqlOrder;
			$order_data_arr=sql_select($sqlOrder);
			foreach($order_data_arr as $val){
				$job_arr[$val[csf('job_no')]]=$val[csf('job_no')];
				$dataArr[$val[csf('job_no')]]=$val;
			}
			
			
			

			 $bookingSql="select b.job_no from wo_booking_mst a,wo_booking_dtls b where a.company_id=$compid and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 ".where_con_using_array($job_arr,1,'b.job_no')." group by b.job_no";
			 //echo $bookingSql;die;
			 $bookingSqlResult=sql_select($bookingSql);
			  foreach($bookingSqlResult as $row){
			 	unset($dataArr[$row[csf('job_no')]]);
		      }


			


?>

				<b align="center"><? echo $company_library[$compid];?></b>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="100">Job No</th>
						<th width="100">Style</th>
						<th width="100">PO</th>
						<th width="100">Buyer</th>
						<th width="100">Season</th>
						<th width="100">Job Entry Date</th>
						<th width="100">Garments Item</th>
						<th width="100">Team Leader</th>
						<th width="100">Dealing Merchant</th>
					</thead>

					<?php

							$i = 1;
					
							foreach ($dataArr as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')"  id="tr_<? echo $i; ?>">
							<td width="40" align="center"><? echo $i; ?></td>
							<td width="100" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
							<td width="100" align="center"><? echo $row[csf('style_ref_no')]; ?>		</td>
							<td width="100" align="center"><? echo $row[csf('po_number')]; ?></td>
							<td width="100" align="center"><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
							<td width="100" align="center"><p><? echo $row[csf('season')]; ?></p></td>
							<td width="100" align="center"><?  echo change_date_format($row[csf('insert_date')]); ?></td>				
							<td width="100" align="center"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>							
							<td width="100" align="center"><p><? echo $team_leader[$row[csf('team_leader')]]; ?></p></td>
							<td width="100" align="center"><p><? echo $dealing_merchand[$row[csf('dealing_marchant')]]; ?></p></td>
							</tr>

<?
						$i++;	}

		
			


	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=64 and b.mail_user_setup_id=c.id and a.company_id=$compid AND a.MAIL_TYPE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		$toArr[$row[csf('email_address')]]=$row[csf('email_address')]; 
	}
	
 	$to=implode(',',$toArr);
	
	$subject = "NO Fabric Booking";
	
	$message=ob_get_contents();
	ob_clean();
	$header=mailHeader();
	//if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);
	if($_REQUEST['isview']==1){
		echo $message;
	}
	else{
		if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);
	}


	//echo $message;

}
	
	





?> 