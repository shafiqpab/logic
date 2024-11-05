<?php

date_default_timezone_set("Asia/Dhaka");

// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');



$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name",$con);


$qury_for_merchandiser = "select id as merchandiser_id,team_member_name as merchandiser from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name";

$merchandisers = sql_select($qury_for_merchandiser);


// $queryForPriceQuotation = "SELECT pq.id price_quotation_id, pq.approved, pq.ready_to_approved, pq.remarks, pqc.confirm_price fob, company.company_name, buyer.id buyer_id, buyer.buyer_name, merchandiser.id merchandiser_id, merchandiser.team_member_name merchandiser, pq.insert_date, pq.style_ref, pq.style_ref, pq.style_desc, pq.pord_dept, pq.offer_qnty, pq.est_ship_date
// FROM lib_buyer buyer join wo_price_quotation  pq on buyer.id = pq.buyer_id    join lib_company company on  company.ID = pq.company_id join  lib_mkt_team_member_info merchandiser
// on merchandiser.id = pq.dealing_merchant left join wo_price_quotation_costing_mst pqc on pq.id = pqc.quotation_id WHERE  (trunc(SYSDATE) - trunc(pq.insert_date)) <=10  and pq.status_active = 1 and pq.is_deleted = 0";
// 	$price_quotations = sql_select($queryForPriceQuotation);
	$queryForPriceQuotation = "SELECT distinct pq.id price_quotation_id, pq.approved, pq.ready_to_approved, pq.remarks, pqc.confirm_price fob,
	 company.company_name, buyer.id buyer_id, buyer.buyer_name, merchandiser.id merchandiser_id, merchandiser.team_member_name merchandiser, pq.insert_date,
	  pq.style_ref, pq.style_ref, pq.style_desc, pq.pord_dept, pq.offer_qnty, pq.est_ship_date FROM lib_buyer buyer join wo_price_quotation  pq
		on buyer.id = pq.buyer_id    join lib_company company on  company.ID = pq.company_id join  lib_mkt_team_member_info merchandiser
		on merchandiser.id = pq.dealing_merchant join approval_history ah on ah.mst_id = pq.id left join wo_price_quotation_costing_mst pqc on pq.id = pqc.quotation_id
		WHERE  (trunc(SYSDATE) - trunc(ah.approved_date)) <=10 and ah.entry_form = 10 and pq.status_active = 1 and pq.is_deleted = 0";
		$price_quotations = sql_select($queryForPriceQuotation);

$queryForApprovalInfo = "SELECT pq.id price_quotation_id, authority.user_full_name approved_by, ah.approved_date from approval_history ah join wo_price_quotation pq on pq.id = ah.mst_id join user_passwd authority on
authority.id = ah.approved_by where ah.entry_form = 10 order by ah.approved_date desc";

$queryForReadyToApprove = "SELECT pq.id price_quotation_id, pq.approved, pq.ready_to_approved, pq.remarks, pqc.confirm_price fob, company.company_name, buyer.id buyer_id, buyer.buyer_name, merchandiser.id merchandiser_id, merchandiser.team_member_name merchandiser, pq.insert_date, pq.style_ref, pq.style_ref, pq.style_desc, pq.pord_dept, pq.offer_qnty, pq.est_ship_date, pq.update_date
FROM lib_buyer buyer join wo_price_quotation  pq on buyer.id = pq.buyer_id    join lib_company company on  company.ID = pq.company_id join  lib_mkt_team_member_info merchandiser
on merchandiser.id = pq.dealing_merchant left join wo_price_quotation_costing_mst pqc on pq.id = pqc.quotation_id WHERE   pq.status_active = 1 and pq.is_deleted = 0 and pq.ready_to_approved = 1 and pq.approved = 0 and (trunc(SYSDATE) - trunc(pq.update_date)) <=10";

$pqsForReadyToApprove =  sql_select($queryForReadyToApprove);

$approvalInfos = sql_select($queryForApprovalInfo);
// var_dump($approvalInfos);
// die();




$emailBody = "";
$merchandisersArrApproved = array();
$merchandisersArrReadyToApprove = array();
foreach($price_quotations as $pq_key => $pq){
	// if($pq['APPROVED'] == 1 || $pq['APPROVED'] == 2 || $pq['APPROVED'] == 3){
		$merchandisersArrApproved[] = $pq['MERCHANDISER_ID'];
	// }
}
foreach($pqsForReadyToApprove as $pq_key => $pq){
		$merchandisersArrReadyToApprove[] = $pq['MERCHANDISER_ID'];
}

if(count($merchandisersArrApproved)){
	$emailBody .= "<h4 style='text-align:center;'>Approved Price Quotations for Last 10 Days.</h4>";
}else{
	$emailBody .= "<h4 style='text-align:center;'>There is no approved price quotations.</h4>";
}
// var_dump($merchandisersArr);

foreach ($merchandisers as $merchant_key => $merchandiser) {
	// var_dump($merchandiser);die();
$sl = 1;
if(in_array($merchandiser['MERCHANDISER_ID'], $merchandisersArrApproved)){

$emailBody .= '<table rules="all" border="1" width=""><tr><td colspan="13">';
$emailBody .= "<strong>" ."Dealing Merchant: ".$merchandiser['MERCHANDISER'].'</strong><br> </td></tr>';


					$emailBody .= "<tr>
									<th>SL</th>
									<th>QID</th>
									<th>Company</th>
									<th>Buyer Name</th>
									<th>Style Ref</th>
									<th>Style Desc</th>
									<th>Prod. Dept.</th>
									<th>Offer Qnty</th>
									<th>Est Ship Date</th>
									<th>FOB</th>
									<th>
									<table  Width='100%' border='1' >
	 									<tr>
		 									<th width='50%'>Approved By</th>
	 										<th width='50%'>Date of Approval</th>
		 								</tr>
 									</table>
									</th>

									<th>Remarks</th>

									<tr/>";

		foreach($price_quotations as $pq_key => $pq){
			// var_dump($pq);die();
			// if(($pq['APPROVED'] == 1 || $pq['APPROVED'] == 2 || $pq['APPROVED'] == 3) && $merchandiser['MERCHANDISER_ID'] == $pq['MERCHANDISER_ID']){
				if( $merchandiser['MERCHANDISER_ID'] == $pq['MERCHANDISER_ID']){
				// var_dump($pq['PRICE_QUOTATION_ID']);
				$emailBody .= '<tr>';
				$emailBody .= '<td width="30">'. $sl++.'</td>';
				$emailBody .= '<td width="50">'. $pq['PRICE_QUOTATION_ID'].'</td>';
				$emailBody .= '<td width="100">'. wordwrap($pq['COMPANY_NAME'],10,"<br>\n",TRUE).'</td>';
				$emailBody .= '<td width="100">'. wordwrap($pq['BUYER_NAME'],10,"<br>\n",TRUE).'</td>';
				$emailBody .=  '<td width="100">'. wordwrap($pq['STYLE_REF'],10,"<br>\n",TRUE).'</td>';
				$emailBody .=  '<td width="100">'. wordwrap($pq['STYLE_DESC'],10,"<br>\n",TRUE).'</td>';
				$emailBody .=  '<td width="80">'. $product_dept[$pq['PORD_DEPT']].'</td>';
				$emailBody .=  '<td width="90">'. $pq['OFFER_QNTY'].'</td>';
				$emailBody .= '<td width="100">'. $pq['EST_SHIP_DATE'].'</td>';
				$emailBody .=  '<td width="30">'.$pq['FOB'].'</td>';
				$emailBody .=  '<td>';

$emailBody .= '<table  border="1" width="100%" >';
				foreach ($approvalInfos as $key => $approvalInfo) {

							if($pq['PRICE_QUOTATION_ID'] == $approvalInfo['PRICE_QUOTATION_ID']){
									$emailBody .= '<tr>';
									$emailBody .= '<td width="120">';
										$emailBody .= $approvalInfo['APPROVED_BY'];
									$emailBody .= '</td>';
									$emailBody .= '<td width="120">';
										$emailBody .= $approvalInfo['APPROVED_DATE'];
									$emailBody .= '</td>';
									$emailBody .= '</tr>';
								// echo $approvalInfo['APPROVED_BY'].'<hr>';
							}

				}
	$emailBody .= '</table>';
				$emailBody .=  '</td>';
					$emailBody .=  '<td width="170">'.wordwrap($pq['REMARKS'],15,"<br>\n",TRUE).'</td>';

$emailBody .=  '</tr>';
			}
		}
		$emailBody .=  '</table><br>';
}
}

// echo $emailBody; die();
$emailBody2 = '';
if(count($merchandisersArrReadyToApprove)){
	$emailBody2 .=  '<h4 style="text-align:center;">Pending Price Quotations for Approval.</h4>';
}else{
	$emailBody2 .=  '<h4 style="text-align:center;">There is no pending price quotations for approval.</h4>';
}

	// echo $emailBody;


foreach ($merchandisers as $merchant_key => $merchandiser) {
	// var_dump($merchandiser);die();
$sl = 1;
if(in_array($merchandiser['MERCHANDISER_ID'], $merchandisersArrReadyToApprove)){

$emailBody2 .=  '<table rules="all" border="1" width="80%"><tr><td colspan="13">';

		 $emailBody2 .=  "<strong>" ."Dealing Merchant: ".$merchandiser['MERCHANDISER']."</strong><br></td></tr><tr>
	 					<th>SL</th>
	 					<th >QID</th>
	 					<th>Company</th>
	 					<th>Buyer Name</th>
	 					<th>Style Ref</th>
	 					<th>Style Desc</th>
	 					<th>Prod. Dept.</th>
	 					<th>Offer Qnty</th>
	 					<th>Est Ship Date</th>
	 					<th>FOB</th>
	 					<th>Ready to Approve Date</th>
	 					<th>Remarks</th>


	 					<tr/>";


		foreach($pqsForReadyToApprove as $pq_key => $pq){
			// var_dump($pq);die();
			// if($pq['READY_TO_APPROVED'] == 1 && $pq['APPROVED'] == 0 && $merchandiser['MERCHANDISER_ID'] == $pq['MERCHANDISER_ID']){
				if($merchandiser['MERCHANDISER_ID'] == $pq['MERCHANDISER_ID']){
				// var_dump($pq['PRICE_QUOTATION_ID']);
				$emailBody2 .=  '<tr>';
				$emailBody2 .=  '<td width="30">'. $sl++.'</td>';
				$emailBody2 .=  '<td width="50">'. $pq['PRICE_QUOTATION_ID'].'</td>';
				$emailBody2 .= '<td width="100">'. wordwrap($pq['COMPANY_NAME'],10,"<br>\n",TRUE).'</td>';
				$emailBody2 .= '<td width="100">'. wordwrap($pq['BUYER_NAME'],10,"<br>\n",TRUE).'</td>';
				$emailBody2 .=  '<td width="100">'. wordwrap($pq['STYLE_REF'],10,"<br>\n",TRUE).'</td>';
				$emailBody2 .=  '<td width="100">'. wordwrap($pq['STYLE_DESC'],10,"<br>\n",TRUE).'</td>';
				$emailBody2 .= '<td width="80">'. $product_dept[$pq['PORD_DEPT']].'</td>';
				$emailBody2 .= '<td width="90">'. $pq['OFFER_QNTY'].'</td>';
				$emailBody2 .= '<td width="100">'. $pq['EST_SHIP_DATE'].'</td>';
				$emailBody2 .= '<td width="30">'.$pq['FOB'].'</td>';
				$quotationPrepareDate = $pq['UPDATE_DATE']?$pq['UPDATE_DATE']:$pq['INSERT_DATE'];
				$emailBody2 .= '<td width="120">'.$quotationPrepareDate .'</td>';
				$emailBody2 .= '<td width="170">'.wordwrap($pq['REMARKS'],15,"<br>\n",TRUE).'</td>';
				$emailBody2 .= '</tr>';;



			}
		}

		$emailBody2 .= "</table><br>";

}
}
echo $emailBody.'<br>';
echo $emailBody2;

$to="";
// var_dump($company_library);
foreach($company_library as $compid=>$compname)/// Total Activities
{
	$mail_item=15;
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=15 and b.mail_user_setup_id=c.id and a.company_id=$compid AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql2=sql_select($sql2);
// var_dump($mail_sql2);
	foreach($mail_sql2 as $row)
	{
// var_dump($row);
		if ($to=="") {

			$to=$row[csf('email_address')];
		}  else{
			 $to=$to.",".$row[csf('email_address')];
		}

	}
}

$subject="Approved Price Quotations for Last 10 Days";
$subject2="Pending Price Quotaions for Approval";
	$message1 = $emailBody;
	$message2 = $emailBody2;

$header=mail_header();
// echo $to;

//if($to!=""){echo send_mail_mailer( $to, $subject, $message1, $from_mail );}
//if($to!=""){echo send_mail_mailer( $to, $subject2, $message2, $from_mail );}
if($_REQUEST['isview']==1){
	if($to){
		echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
	}else{
		echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
	}
	echo $message1;
	echo $message2;
}
else{
	if($to!=""){echo send_mail_mailer( $to, $subject, $message1, $from_mail );}
	if($to!=""){echo send_mail_mailer( $to, $subject2, $message2, $from_mail );}
}



?>
