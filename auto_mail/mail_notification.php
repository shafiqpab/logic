<?php
date_default_timezone_set("Asia/Dhaka");
// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require_once('setting/mail_setting.php');

 extract($_REQUEST);
	
// var returnValue=return_global_ajax_value(reponse[2], 'price_quotation_mail_notification', '', '../../../auto_mail/mail_notification');
	
	
if($action=='price_quotation_mail_notification'){
	
	 //$data=670;
	$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name",$con);

	
	$queryForReadyToApprove = "select pq.company_id,pqc.cm_cost,pqc.fabric_cost,pqc.trims_cost,pqc.embel_cost,pqc.wash_cost,PQC.total_cost, pqc.margin_dzn_percent,pqc.lab_test,pqc.inspection, pqc.margin_dzn,pqc.freight,pqc.currier_pre_cost,pqc.certificate_pre_cost,pqc.design_pre_cost,pqc.studio_pre_cost,pqc.comm_cost,pqc.common_oh,pqc.depr_amor_pre_cost,pqc.interest_pre_cost,pqc.income_tax_pre_cost, pqc.price_with_commn_dzn fob,pq.id price_quotation_id, company.company_name, buyer.buyer_name ,pq.style_ref,pq.style_desc, pq.pord_dept,pq.offer_qnty, pq.est_ship_date 	from lib_buyer buyer join wo_price_quotation pq on buyer.id = pq.buyer_id 
	join lib_company company on company.id = pq.company_id 	left join wo_price_quotation_costing_mst pqc on pq.id = pqc.quotation_id 
	where pq.status_active = 1 and pq.is_deleted = 0 and pq.ready_to_approved = 1   and pq.id=$data
	";
	
	$pqsForReadyToApprove =  sql_select($queryForReadyToApprove);
	ob_start();	
	echo 'Dear Sir,<br>';
    echo  'Attached please find the cost break ups:<br>';
	
?>
	<table cellpadding="1">
<?
	foreach($pqsForReadyToApprove as $pq_key => $pq){
		$material_cost=$pq['FABRIC_COST']+$pq['TRIMS_COST'];		
		$other_cost=$pq['LAB_TEST']+$pq['INSPECTION']+$pq['FREIGHT']+$pq['CURRIER_PRE_COST']+$pq['CERTIFICATE_PRE_COST']+$pq['DESIGN_PRE_COST']+$pq['STUDIO_PRE_COST']+$pq['DEPR_AMOR_PRE_COST']+$pq['INTEREST_PRE_COST']+$pq['INCOME_TAX_PRE_COST']+$pq['COMMON_OH'];
		echo   '<tr><td><b>Quotation No</b></td><th>:</th><td>'. $pq['PRICE_QUOTATION_ID'].'</td></tr>';
		echo '<tr><td><b>Buyer Name</b></td><th>:</th><td>'. $pq['BUYER_NAME'].'</td></tr>';
		echo  '<tr><td><b>Style Ref</b></td><th>:</th><td>'. $pq['STYLE_REF'].'</td></tr>';
		echo '<tr><td><b>Style Qty.(Pcs)</b></td><th>:</th><td>'. $pq['OFFER_QNTY'].'</td></tr>';
		echo '<tr><td><b>Delivery</b></td><th>:</th><td>'. $pq['EST_SHIP_DATE'].'</td></tr>';
		echo '<tr><td><b>Material Cost</b></td><th>:</th><td>'.number_format($material_cost, 4,'.','').'</td></tr>';
		echo '<tr><td><b>Embellishment Cost</b></td><th>:</th><td>'.number_format($pq['EMBEL_COST'], 4,'.','').'</td></tr>';
		echo '<tr><td><b>Wash Cost</b></td><th>:</th><td>'.number_format($pq['WASH_COST'], 4,'.','').'</td></tr>';
		echo '<tr><td><b>Commercial Cost</b></td><th>:</th><td>'.number_format($pq['COMM_COST'], 4,'.','').'</td></tr>';
		echo '<tr><td><b>Other Cost</b></td><th>:</th><td>'.number_format($other_cost, 4,'.','').'</td></tr>';			
		echo '<tr><td><b>CM Cost</b></td><th>:</th><td>'.number_format($pq['CM_COST'], 4,'.','').'</td></tr>';
		echo '<tr><td><b>Total Cost</b></td><th>:</th><td>'.number_format($pq['TOTAL_COST'], 4,'.','').'</td></tr>';
		echo '<tr><td><b>FOB Price/Pcs</b></td><th>:</th><td>'.number_format($pq['FOB'], 4,'.','').'</td></tr>';
		echo '<tr><td><b>Margin value</b></td><th>:</th><td>'.number_format($pq['MARGIN_DZN'], 4,'.','').'</td></tr>';
		echo '<tr><td><b>Margin (%)</b></td><th>:</th><td>'.number_format($pq['MARGIN_DZN_PERCENT'], 4,'.','').'</td></tr>';
		echo "</table>";
	}
	
     echo "<br>Kindly review and advise approval please.<br><br><br>";
     echo "Thanks.<br>";
     echo "Kind regards<br>";
	
	//$company_id=$pqsForReadyToApprove[0]['COMPANY_ID'];
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=24 AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and b.mail_user_setup_id=c.id and a.company_id in(".implode(',',array_flip($company_library)).")";	
	$mail_sql2=sql_select($sql2);
	foreach($mail_sql2 as $row)
	{
		if ($to=="") {
			$to=$row[csf('email_address')];
		}  else{
			 $to=$to.",".$row[csf('email_address')];
		}

	}

	$subject="Price Quotation Mail Notification";
	$message=ob_get_contents();
	ob_clean();
	$header=mailHeader();
	//if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);
	  //if($to!=""){echo send_mail_mailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Not Send';}
	if($_REQUEST['isview']==1){
		echo $message;
		echo $emailBody;
	}
	else{
		if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);
		if($to!=""){echo send_mail_mailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Not Send';}
	}

	//  echo $to."<br>";
 	// echo $message;die;

exit();	
}



?>
