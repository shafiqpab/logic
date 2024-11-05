<?php

date_default_timezone_set("Asia/Dhaka");

// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require_once('setting/mail_setting.php');




$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0  ", "id", "company_name");
$qury_for_merchandiser = "select id as merchandiser_id,team_member_name as merchandiser from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name";
$merchandisers = sql_select($qury_for_merchandiser);

 
	if($db_type==0)
	{	
		$approved_date_diff="(DATEDIFF('".date('Y-m-d',time())."', ah.approved_date))";
		$update_date_diff="(DATEDIFF('".date('Y-m-d',time())."', precost.update_date))";
	}
	else
	{
		//$date_diff="(to_date('".date('d-M-y',time())."', 'dd-MM-yy')- to_date(a.allocation_date, 'dd-MM-yy'))";
		$approved_date_diff="(trunc(SYSDATE) - trunc(ah.approved_date))";
		$update_date_diff="(trunc(SYSDATE) - trunc(precost.update_date))";
	}

//".where_con_using_array($precostIdArr,0,'precosting_id')."

	
	
	foreach($company_library as $company_id=>$company_name){
	
	$queryForPrecosting = "SELECT distinct precost.id precosting_id, precost.job_no, precost.approved, precost.ready_to_approved, precost.remarks,company.company_name, buyer.id buyer_id, buyer.buyer_name, merchandiser.id merchandiser_id, merchandiser.team_member_name merchandiser, precost.insert_date, precost.update_date,orderentry.style_ref_no, orderentry.job_quantity, orderentry.style_description, orderentry.product_dept, orderentry.avg_unit_price, precost.costing_date from wo_pre_cost_mst precost    join wo_po_details_master  orderentry on precost.job_no = orderentry.job_no join  lib_buyer buyer  on buyer.id = orderentry.buyer_name  join  lib_company company on  company.id = orderentry.company_name join  lib_mkt_team_member_info merchandiser on merchandiser.id = orderentry.dealing_marchant join approval_history ah on ah.mst_id = precost.id WHERE  $approved_date_diff <=10 and ah.entry_form = 15 and precost.status_active = 1 and precost.is_deleted = 0 and orderentry.company_name=$company_id";
   $precostings = sql_select($queryForPrecosting);





$queryForReadyToApprove = "SELECT distinct precost.id precosting_id, precost.job_no, precost.approved, precost.ready_to_approved, precost.remarks, company.company_name, buyer.id buyer_id, buyer.buyer_name, merchandiser.id merchandiser_id, merchandiser.team_member_name merchandiser, precost.insert_date, precost.update_date, orderentry.style_ref_no, orderentry.job_quantity, orderentry.style_description, orderentry.product_dept, orderentry.style_ref_no, orderentry.job_quantity, orderentry.style_description, orderentry.product_dept, orderentry.avg_unit_price, precost.costing_date from wo_pre_cost_mst precost join wo_po_details_master  orderentry on precost.job_no = orderentry.job_no join  lib_buyer buyer  on buyer.id = orderentry.buyer_name  join  lib_company company on  company.id = orderentry.company_name join  lib_mkt_team_member_info merchandiser on merchandiser.id = orderentry.dealing_marchant WHERE   precost.status_active = 1 and precost.is_deleted = 0 and precost.ready_to_approved = 1 and precost.approved = 2 and $update_date_diff <=10 and company.id=$company_id";

$pqsForReadyToApprove =  sql_select($queryForReadyToApprove);




$emailBody = "";
$merchandisersArrApproved = array();
$merchandisersArrReadyToApprove = array();$precostIdArr= array();
foreach($precostings as $pc_key => $pc){
	// if($pq[csf('APPROVED')] == 1 || $pq[csf('APPROVED')] == 2 || $pq[csf('APPROVED')] == 3){
		$merchandisersArrApproved[] = $pc[csf('MERCHANDISER_ID')];
		$precostIdArr[$pc[csf('precosting_id')]] = $pc[csf('precosting_id')];
	// }
}
foreach($pqsForReadyToApprove as $pc_key => $pc){
		$merchandisersArrReadyToApprove[] = $pc[csf('MERCHANDISER_ID')];
		$precostIdArr[$pc[csf('precosting_id')]] = $pc[csf('precosting_id')];
}

//$queryForApprovalInfo = "SELECT precost.id precosting_id, precost.job_no, authority.user_full_name approved_by, ah.approved_date from approval_history ah join wo_pre_cost_mst precost on precost.id = ah.mst_id join user_passwd authority on authority.id = ah.approved_by where ah.entry_form = 15  order by ah.approved_date desc";

$queryForApprovalInfo = "SELECT ah.mst_id precosting_id, authority.user_full_name approved_by, ah.approved_date from approval_history ah join user_passwd authority on authority.id = ah.approved_by where ah.entry_form = 15 ".where_con_using_array($precostIdArr,0,'ah.mst_id')."  order by ah.approved_date desc";
$approvalInfos = sql_select($queryForApprovalInfo);

//print_r($precostIdArr);






if(count($merchandisersArrApproved)){
	$emailBody .= "<h4 style='text-align:center;'>Approved Precostings for Last 10 Days.</h4>";
	$emailBody .= "<p style='text-align:center;'>$company_name</p>";

// var_dump($merchandisersArr);

foreach ($merchandisers as $merchant_key => $merchandiser) {
	// var_dump($merchandiser);die();
$sl = 1;
if(in_array($merchandiser[csf('MERCHANDISER_ID')], $merchandisersArrApproved)){

$emailBody .= '<table rules="all" border="1" width=""><tr><td colspan="13">';
$emailBody .= "<strong>" ."Dealing Merchant: ".$merchandiser[csf('MERCHANDISER')].'</strong><br> </td></tr>';


					$emailBody .= "<tr>
									<th>SL</th>
									<th>Job No</th>
									<th>Company</th>
									<th>Buyer Name</th>
									<th>Style Ref</th>
									<th>Style Desc</th>
									<th>Prod. Dept.</th>
									<th>Job Qnty</th>
									<th>Costing Date</th>
									<th>FOB</th>
									<th>
									<table  Width='100%' border='1' rules='all' >
	 									<tr>
		 									<th width='50%'>Approved By</th>
	 										<th width='50%'>Date of Approval</th>
		 								</tr>
 									</table>
									</th>

									<th>Remarks</th>

									<tr/>";

		foreach($precostings as $pc_key => $pc){
			// var_dump($pq);die();
			// if(($pq[csf('APPROVED')] == 1 || $pq[csf('APPROVED')] == 2 || $pq[csf('APPROVED')] == 3) && $merchandiser[csf('MERCHANDISER_ID')] == $pq[csf('MERCHANDISER_ID')]){
				if( $merchandiser[csf('MERCHANDISER_ID')] == $pc[csf('MERCHANDISER_ID')]){
				// var_dump($pq[csf('PRICE_QUOTATION_ID')]);
				$emailBody .= '<tr>';
				$emailBody .= '<td width="30">'. $sl++.'</td>';
				$emailBody .= '<td width="50">'. $pc[csf('JOB_NO')].'</td>';
				$emailBody .= '<td width="100">'. wordwrap($pc[csf('COMPANY_NAME')],10,"<br>\n",TRUE).'</td>';
				$emailBody .= '<td width="100">'. wordwrap($pc[csf('BUYER_NAME')],10,"<br>\n",TRUE).'</td>';
				$emailBody .=  '<td width="100">'. wordwrap($pc[csf('STYLE_REF_NO')],10,"<br>\n",TRUE).'</td>';
				$emailBody .=  '<td width="100">'. wordwrap($pc[csf('STYLE_DESCRIPTION')],10,"<br>\n",TRUE).'</td>';
				$emailBody .=  '<td width="80">'. $product_dept[$pc[csf('PRODUCT_DEPT')]].'</td>';
				$emailBody .=  '<td width="90">'. $pc[csf('JOB_QUANTITY')].'</td>';
				$emailBody .= '<td width="100">'. $pc[csf('COSTING_DATE')].'</td>';
				$emailBody .=  '<td width="30">'.$pc[csf('AVG_UNIT_PRICE')].'</td>';
				$emailBody .=  '<td>';

$emailBody .= '<table  border="1" width="100%" role="alert" rules="all"  >';
				foreach ($approvalInfos as $key => $approvalInfo) {

							if($pc[csf('PRECOSTING_ID')] == $approvalInfo[csf('PRECOSTING_ID')]){
									$emailBody .= '<tr>';
									$emailBody .= '<td width="120">';
										$emailBody .= $approvalInfo[csf('APPROVED_BY')];
									$emailBody .= '</td>';
									$emailBody .= '<td width="120">';
										$emailBody .= $approvalInfo[csf('APPROVED_DATE')];
									$emailBody .= '</td>';
									$emailBody .= '</tr>';
								// echo $approvalInfo[csf('APPROVED_BY')].'<hr>';
							}

				}
	$emailBody .= '</table>';
				$emailBody .=  '</td>';
					$emailBody .=  '<td width="170">'.wordwrap($pc[csf('REMARKS')],15,"<br>\n",TRUE).'</td>';

$emailBody .=  '</tr>';
			}
		}
		$emailBody .=  '</table><br>';
}
}
}

// echo $emailBody; die();
$emailBody2 = '';
if(count($merchandisersArrReadyToApprove)){
	$emailBody2 .=  '<h4 style="text-align:center;">Pending Precostings for Approval.</h4>';
	$emailBody2 .= "<h5 style='text-align:center;'>$company_name</h5>";
	// echo $emailBody;


foreach ($merchandisers as $merchant_key => $merchandiser) {
	// var_dump($merchandiser);die();
$sl = 1;
if(in_array($merchandiser[csf('MERCHANDISER_ID')], $merchandisersArrReadyToApprove)){

$emailBody2 .=  '<table rules="all" border="1" width="80%"><tr><td colspan="13">';

		 $emailBody2 .=  "<strong>" ."Dealing Merchant: ".$merchandiser[csf('MERCHANDISER')]."</strong><br></td></tr><tr>
	 					<th>SL</th>
	 					<th >Job No</th>
	 					<th>Company</th>
	 					<th>Buyer Name</th>
	 					<th>Style Ref</th>
	 					<th>Style Desc</th>
	 					<th>Prod. Dept.</th>
	 					<th>Job Qantity</th>
	 					<th>Costing Date</th>
	 					<th>FOB</th>
	 					<th>Ready to Approve Date</th>
	 					<th>Remarks</th>


	 					<tr/>";


		foreach($pqsForReadyToApprove as $pq_key => $pq){
				if($merchandiser[csf('MERCHANDISER_ID')] == $pq[csf('MERCHANDISER_ID')]){
				$emailBody2 .=  '<tr>';
				$emailBody2 .=  '<td width="30">'. $sl++.'</td>';
				$emailBody2 .=  '<td width="50">'. $pq[csf('JOB_NO')].'</td>';
				$emailBody2 .= '<td width="100">'. wordwrap($pq[csf('COMPANY_NAME')],10,"<br>\n",TRUE).'</td>';
				$emailBody2 .= '<td width="100">'. wordwrap($pq[csf('BUYER_NAME')],10,"<br>\n",TRUE).'</td>';
				$emailBody2 .=  '<td width="100">'. wordwrap($pq[csf('STYLE_REF_NO')],10,"<br>\n",TRUE).'</td>';
				$emailBody2 .=  '<td width="100">'. wordwrap($pq[csf('STYLE_DESCRIPTION')],10,"<br>\n",TRUE).'</td>';
				$emailBody2 .= '<td width="80">'. $product_dept[$pq[csf('PRODUCT_DEPT')]].'</td>';
				$emailBody2 .= '<td width="90">'. $pq[csf('JOB_QUANTITY')].'</td>';
				$emailBody2 .= '<td width="100">'. $pq[csf('COSTING_DATE')].'</td>';
				$emailBody2 .= '<td width="30">'.$pq[csf('AVG_UNIT_PRICE')].'</td>';
				$quotationPrepareDate = $pq[csf('UPDATE_DATE')]?$pq[csf('UPDATE_DATE')]:$pq[csf('INSERT_DATE')];
				$emailBody2 .= '<td width="120">'.$quotationPrepareDate .'</td>';
				$emailBody2 .= '<td width="170">'.wordwrap($pq[csf('REMARKS')],15,"<br>\n",TRUE).'</td>';
				$emailBody2 .= '</tr>';;

			}
		}

		$emailBody2 .= "</table><br>";

}
}
}

$to="";
	$mail_item = 11;
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and b.mail_user_setup_id=c.id and a.company_id=$company_id AND a.MAIL_TYPE=1";
	$mail_sql2=sql_select($sql2);
	foreach($mail_sql2 as $row)
	{
		if ($to=="") {
			$to=$row[csf('email_address')];
		}  else{
			 $to=$to.",".$row[csf('email_address')];
		}

	}




//$subject="Approved Precostings for Last 10 Days";
$subject2="Pending Precostings for Approval";
	$message1 = $emailBody;
	$message2 = $emailBody2;

$header=mailHeader();
//$to.=',muktobani@gmail.com,reza@logicsoftbd.com';
//if($to!=""){echo sendMailMailer( $to, $subject, $message1, $from_mail );}
//if($to!=""){echo sendMailMailer( $to, $subject2, $message2, $from_mail );}
//echo $message1.$message2;

if($_REQUEST['isview']==1){
	$mail_item=2;
	if($to){
		echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
	}else{
		echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
	}
	echo $message1;
	echo $message2;
}
else{
	if($to!=""){echo sendMailMailer( $to, $subject, $message1, $from_mail );}
	if($to!=""){echo sendMailMailer( $to, $subject2, $message2, $from_mail );}
}

 


}

?>

