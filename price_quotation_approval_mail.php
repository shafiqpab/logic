<?php

	date_default_timezone_set("Asia/Dhaka");

	require_once('../mailer/class.phpmailer.php');
	require_once('../includes/common.php');
	$from_mail="PLATFORM-ERP@asrotex.com";

$company_library 	=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$qury_for_merchandiser = "select id as merchandiser_id,team_member_name as merchandiser from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name";

$merchandisers = sql_select($qury_for_merchandiser);


$queryForPriceQuotation = "SELECT pq.id price_quotation_id, pq.approved, pq.ready_to_approved, pq.remarks, pqc.confirm_price fob, company.company_name, buyer.id buyer_id, buyer.buyer_name, merchandiser.id merchandiser_id, merchandiser.team_member_name merchandiser, pq.insert_date, pq.style_ref, pq.style_ref, pq.style_desc, pq.pord_dept, pq.offer_qnty, pq.est_ship_date
FROM lib_buyer buyer join wo_price_quotation  pq on buyer.id = pq.buyer_id    join lib_company company on  company.ID = pq.company_id join  lib_mkt_team_member_info merchandiser
on merchandiser.id = pq.dealing_merchant left join wo_price_quotation_costing_mst pqc on pq.id = pqc.quotation_id WHERE  (trunc(SYSDATE) - trunc(pq.insert_date)) <=15  and pq.status_active = 1 and pq.is_deleted = 0";
	$price_quotations = sql_select($queryForPriceQuotation);

$queryForApprovalInfo = "select pq.id price_quotation_id, authority.user_full_name approved_by, ah.approved_date from approval_history ah join wo_price_quotation pq on pq.id = ah.mst_id join user_passwd authority on
authority.id = ah.approved_by where ah.entry_form = 10 and pq.approved in (1,2,3)";

$approvalInfos = sql_select($queryForApprovalInfo);
// var_dump($approvalInfos);
// die();




$emailBody = "";
$merchandisersArrApproved = array();
$merchandisersArrNotApproved = array();
foreach($price_quotations as $pq_key => $pq){
	if($pq['APPROVED'] == 1 || $pq['APPROVED'] == 2 || $pq['APPROVED'] == 3){
		$merchandisersArrApproved[] = $pq['MERCHANDISER_ID'];
	}
	if($pq['READY_TO_APPROVED'] == 1 && $pq['APPROVED'] == 0){
		$merchandisersArrReadyToApprove[] = $pq['MERCHANDISER_ID'];
	}

}
if(count($merchandisersArrApproved)){
	$emailBody .= "<h4 style='text-align:center;'>Approved Price Quotations – Last 15 Days.</h4>";
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
			if(($pq['APPROVED'] == 1 || $pq['APPROVED'] == 2 || $pq['APPROVED'] == 3) && $merchandiser['MERCHANDISER_ID'] == $pq['MERCHANDISER_ID']){
				// var_dump($pq['PRICE_QUOTATION_ID']);
				$emailBody .= '<tr>';
				$emailBody .= '<td width="30">'. $sl++.'</td>';
				$emailBody .= '<td width="50">'. $pq['PRICE_QUOTATION_ID'].'</td>';
				$emailBody .= '<td width="100">'. $pq['COMPANY_NAME'].'</td>';
				$emailBody .= '<td width="100">'. $pq['BUYER_NAME'].'</td>';
				$emailBody .=  '<td width="70">'. $pq['STYLE_REF'].'</td>';
				$emailBody .=  '<td width="80">'. $pq['STYLE_DESC'].'</td>';
				$emailBody .=  '<td width="80">'. $product_dept[$pq['PORD_DEPT']].'</td>';
				$emailBody .=  '<td width="90">'. $pq['OFFER_QNTY'].'</td>';
				$emailBody .= '<td width="100">'. $pq['EST_SHIP_DATE'].'</td>';
				$emailBody .=  '<td width="30">'.$pq['FOB'].'</td>';
				$emailBody .=  '<td>';

$emailBody .= '<table rules="all" border="1" width="100%" >';
				foreach ($approvalInfos as $key => $approvalInfo) {

							if($pq['PRICE_QUOTATION_ID'] == $approvalInfo['PRICE_QUOTATION_ID']){
									$emailBody .= '<tr>';
									$emailBody .= '<td width="120">';
										$emailBody .= $approvalInfo['APPROVED_BY'];
									$emailBody .= '</td>';
									$emailBody .= '<td width="120">';
										$emailBody .= $approvalInfo['APPROVED_DATE'];
									$emailBody .= '</td';
									$emailBody .= '</tr>';
								// echo $approvalInfo['APPROVED_BY'].'<hr>';
							}

				}
	$emailBody .= '</table>';
				$emailBody .=  '</td>';

				// echo '</td>';
				// echo '<td>';
				// foreach ($approvalInfos as $key => $approvalInfo) {
				//
				// 			if($pq['PRICE_QUOTATION_ID'] == $approvalInfo['PRICE_QUOTATION_ID']){
				// 				$emailBody .= $approvalInfo['APPROVED_DATE'].'<hr>';
				// 				// echo $approvalInfo['APPROVED_DATE'].'<hr>';
				// 			}
				// }
					$emailBody .=  '<td width="170">'.$pq['REMARKS'].'</td>';
					// echo '<td>'.$pq['REMARKS'].'</td>';

$emailBody .=  '</tr>';
				// echo '</tr>';
				// var_dump($pq['MERCHANDISER']);
			}
		}
		$emailBody .=  '</table><br>';
	// echo '</table><br>';
			// var_dump($merchandiser);
}
}

// echo $emailBody; die();
if(count($merchandisersArrReadyToApprove)){
	$emailBody .=  '<h4 style="text-align:center;">Price Quotation Approval Pending: Last 15 Days.</h4>';
}

	// echo $emailBody;


foreach ($merchandisers as $merchant_key => $merchandiser) {
	// var_dump($merchandiser);die();
$sl = 1;
if(in_array($merchandiser['MERCHANDISER_ID'], $merchandisersArrReadyToApprove)){

$emailBody .=  '<table rules="all" border="1" width="80%"><tr><td colspan="13">';
	// echo '<table border="1">';
	// echo '<tr>';
		 // echo '<td colspan="13">';
		 $emailBody .=  "<strong>" ."Dealing Merchant: ".$merchandiser['MERCHANDISER']."</strong><br></td></tr><tr>
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
	 					<th>Quotation Prepare Date</th>
	 					<th>Remarks</th>


	 					<tr/>";


		foreach($price_quotations as $pq_key => $pq){
			// var_dump($pq);die();
			if($pq['READY_TO_APPROVED'] == 1 && $pq['APPROVED'] == 0 && $merchandiser['MERCHANDISER_ID'] == $pq['MERCHANDISER_ID']){
				// var_dump($pq['PRICE_QUOTATION_ID']);
				$emailBody .=  '<tr>';
				$emailBody .=  '<td width="30">'. $sl++.'</td>';
				$emailBody .=  '<td width="50">'. $pq['PRICE_QUOTATION_ID'].'</td>';
				$emailBody .=  '<td width="100">'. $pq['COMPANY_NAME'].'</td>';
				$emailBody .= '<td width="100">'. $pq['BUYER_NAME'].'</td>';
				$emailBody .= '<td width="70">'. $pq['STYLE_REF'].'</td>';
				$emailBody .= '<td width="80">'. $pq['STYLE_DESC'].'</td>';
				$emailBody .= '<td width="80">'. $product_dept[$pq['PORD_DEPT']].'</td>';
				$emailBody .= '<td width="90">'. $pq['OFFER_QNTY'].'</td>';
				$emailBody .= '<td width="100">'. $pq['EST_SHIP_DATE'].'</td>';
				$emailBody .= '<td width="30">'.$pq['FOB'].'</td>';
				$emailBody .= '<td width="120">'. '25/05/2018'.'</td>';
				$emailBody .= '<td width="170">'.$pq['REMARKS'].'</td>';
				$emailBody .= '</tr></table><br>';;



			}
		}

}
}
echo $emailBody;

$to="";
foreach($company_library as $compid=>$compname)/// Total Activities
{

	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=15 and b.mail_user_setup_id=c.id and a.company_id=$compid";
	$mail_sql2=sql_select($sql2);
	foreach($mail_sql2 as $row)
	{

		if ($to=="") {
			$to=$row[csf('email_address')];
		}  else{
			 $to=$to.",".$row[csf('email_address')];
		}

	}

// echo $message;
}

$subject="Approved Price Quotations – Last 15 Days";
//$subject="Yesterday  total activities";
	$message="";
	$message=$emailBody;

$header=mail_header();
// echo $to;
if($to!=""){echo send_mail_mailer( $to, $subject, $message, $from_mail );}
// echo $to;

?>
