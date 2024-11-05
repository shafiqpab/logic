<?php

date_default_timezone_set("Asia/Dhaka");

// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require_once('setting/mail_setting.php');

$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name",$con);


$qury_for_merchandiser = "select id as merchandiser_id,team_member_name as merchandiser from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name";

$merchandisers = sql_select($qury_for_merchandiser);

$queryForPriceQuotation = "SELECT distinct pq.id price_quotation_id, pq.approved, pq.ready_to_approved, pq.remarks, pqc.confirm_price fob, company.company_name, buyer.id buyer_id, buyer.buyer_name, merchandiser.id merchandiser_id, merchandiser.team_member_name merchandiser, pq.insert_date, pq.style_ref, pq.style_ref, pq.style_desc, pq.pord_dept, pq.offer_qnty, pq.est_ship_date, ah.current_approval_status FROM lib_buyer buyer join wo_price_quotation  pq on buyer.id = pq.buyer_id    join lib_company company on  company.ID = pq.company_id join  lib_mkt_team_member_info merchandiser
	on merchandiser.id = pq.dealing_merchant join approval_history ah on ah.mst_id = pq.id left join wo_price_quotation_costing_mst pqc on pq.id = pqc.quotation_id WHERE  ah.entry_form = 10 and ah.current_approval_status = 0 and  pq.status_active = 1 and pq.is_deleted = 0 and trunc(ah.un_approved_date) = trunc(SYSDATE)";

		$price_quotations = sql_select($queryForPriceQuotation);

$queryForApprovalInfo = "SELECT pq.id price_quotation_id, authority.id authId, authority.user_full_name un_approved_by, ah.un_approved_reason, ah.un_approved_date from approval_history ah join wo_price_quotation pq on pq.id = ah.mst_id join user_passwd authority on
authority.id = ah.un_approved_by where ah.entry_form = 10  and ah.current_approval_status = 0 order by ah.un_approved_date desc";


$approvalInfos = sql_select($queryForApprovalInfo);
// var_dump($approvalInfos);die();

$emailBody = "";
$merchandisersArrApproved = array();
$merchandisersArrReadyToApprove = array();
foreach($price_quotations as $pq_key => $pq){
	// if($pq['APPROVED'] == 1 || $pq['APPROVED'] == 2 || $pq['APPROVED'] == 3){
		$merchandisersArrApproved[] = $pq['MERCHANDISER_ID'];
	// }
}


if(count($merchandisersArrApproved)){
	$emailBody .= "<h4 style='text-align:center;'>Un-Approved Price Quotations.</h4>";
}else{
	$emailBody .= "<h4 style='text-align:center;'>There is no un-approved price quotations.</h4>";
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
		 									<th width='120' >Un-Approved By</th>
	 										<th width='120'>Date of Un-Approval</th>
	 										<th width='120'>Reason for Un-Approval</th>
		 								</tr>
 									</table>
									</th>
									<tr/>";

		foreach($price_quotations as $pq_key => $pq){
				if( $merchandiser['MERCHANDISER_ID'] == $pq['MERCHANDISER_ID']){
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
										$emailBody .= $approvalInfo['UN_APPROVED_BY'];
									$emailBody .= '</td>';
									$emailBody .= '<td width="120">';
										$emailBody .= $approvalInfo['UN_APPROVED_DATE'];
									$emailBody .= '</td>';
									$emailBody .= '<td width="120">';
										$emailBody .= $approvalInfo['UN_APPROVED_REASON'];
									$emailBody .= '</td>';
									$emailBody .= '</tr>';
								// echo $approvalInfo['APPROVED_BY'].'<hr>';
							}

				}
	$emailBody .= '</table>';
				$emailBody .=  '</td>';
					

$emailBody .=  '</tr>';
			}
		}
		$emailBody .=  '</table><br>';
}
}


echo $emailBody.'<br>';


$to="";
// var_dump($company_library);
foreach($company_library as $compid=>$compname)/// Total Activities
{

	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=15 and b.mail_user_setup_id=c.id and a.company_id=$compid  and c.IS_DELETED=0 AND a.MAIL_TYPE=1 and c.STATUS_ACTIVE=1";
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

$subject="Un-Approved Price Quotations";

	$message1 = $emailBody;

$header=mail_header();
// echo $to;

//if($to!=""){echo send_mail_mailer( $to, $subject, $message1, $from_mail );}
if($_REQUEST['isview']==1){
	echo $message1;
}
else{
	if($to!=""){echo send_mail_mailer( $to, $subject, $message1, $from_mail );}
}




?>
