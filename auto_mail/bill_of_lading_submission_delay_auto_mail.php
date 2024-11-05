<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

	$comp_lib = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id","company_name");
	$buyer_lib = return_library_array("select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0","id", "buyer_name");

    $strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	$date_diff="(to_date('".date('d-M-y', $strtotime)."', 'dd-MM-yy')- to_date(a.bl_date, 'dd-MM-yy'))";
	$where_con=" and a.bl_date is not NULL";
 
	$sql="select a.id,a.is_lc,a.benificiary_id,a.buyer_id,a.lc_sc_id,a.invoice_no,a.invoice_date,a.INVOICE_VALUE, a.bl_date, a.total_carton_qnty,a.ex_factory_date,$date_diff as pending_days  from com_export_invoice_ship_mst a where a.status_active=1 and a.is_deleted=0 $where_con and  ($date_diff > 7 or a.ex_factory_date is NULL) and a.invoice_date > '1-Jul-2019' and a.PAYMENT_METHOD=1 and a.buyer_id<>12 order by a.bl_date";//   and a.invoice_no='123123'
	$result=sql_select($sql);
	$dataArr=array();$above14dataArr=array();
	foreach($result as $row)
	{
		$dataInvoiceIdArr[$row[csf('id')]]=$row[csf('id')];
		//$dataArr[$row[csf('benificiary_id')]][]=$row;
		//$above14dataArr[$row[csf('benificiary_id')]][]=$row;
		
		if($row[csf('pending_days')] >7 and $row[csf('pending_days')] <15){
			$dataArr[$row[csf('benificiary_id')]][]=$row;
			$lc_sc_id_arr[$row[csf('is_lc')]][$row[csf('lc_sc_id')]]=$row[csf('lc_sc_id')];
		}
		else{
			$above14dataArr[$row[csf('benificiary_id')]][]=$row;
			$lc_sc_id_arr[$row[csf('is_lc')]][$row[csf('lc_sc_id')]]=$row[csf('lc_sc_id')];
		}
	
	}
	
	
		$sql_lc = "select id,export_lc_no as lc_sc from com_export_lc where status_active=1 and is_deleted=0 and id in(".implode(',',$lc_sc_id_arr[1]).")";

		$sql_sc = "select id,contract_no as lc_sc from com_sales_contract where status_active=1 and is_deleted=0 and id in(".implode(',',$lc_sc_id_arr[2]).")";
	
		$lc_lib = return_library_array($sql_lc, "id","lc_sc");
		$sc_lib = return_library_array($sql_sc, "id","lc_sc");
	
		$sql_submit_date_sql="select b.invoice_id,a.submit_date from com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
		$invoice_list_arr=array_chunk($dataInvoiceIdArr,999);
		$p=1;
		foreach($invoice_list_arr as $invoice_process)
		{
			if($p==1){$sql_submit_date_sql .=" and (b.invoice_id in(".implode(",",$invoice_process).")";} 
			else{$sql_submit_date_sql .=" or b.invoice_id in(".implode(",",$invoice_process).")";}
			$p++;
		}
		$sql_submit_date_sql .=")";	
		$submit_date_lib = return_library_array($sql_submit_date_sql, "invoice_id","submit_date");
		 //print_r($dataArr);
		
	
	ob_start();
	echo "Dear Sir,<br> 
Bank Submission Pending for Following Commercial Invoices From 8 to 14 Days Delay Mail Based on ".date("d-m-Y", $strtotime);
	$flag=0;
	foreach($dataArr as $company_id=>$company_data_arr){?>
        <table border="1" rules="all">
        <tr>
            <td colspan="11"><strong><? echo $comp_lib[$company_id];?></strong></td>
        </tr>
        <tr bgcolor="#CCCCCC">
            <th width="35">SL</th>
            <th>Buyer</th>
            <th>LC No</th>
            <th>Invoice No</th>
            <th>Invoice Date</th>
            <th>Invoice Val</th>
            <th>CTN Qty</th>
            <th>Ship Date</th>
            <th bgcolor="#FEE9FE">BL Date</th>
            <th bgcolor="#FEE9DD">Bank Sub Date</th>
            <th>Pending Days</th>
        </tr>
       <? 
       $i=1;
        foreach($company_data_arr as $rows){
            if($submit_date_lib[$rows[csf('id')]]==''){
                $flag=1;	
                $lc_sc_id=($rows[csf('is_lc')]==1)?$lc_lib[$rows[csf('lc_sc_id')]]:$sc_lib[$rows[csf('lc_sc_id')]];
                ?>
                <tr>
                    <td align="center"><? echo $i;?></td>
                    <td><? echo $buyer_lib[$rows[csf('buyer_id')]];?></td>
                    <td><? echo $lc_sc_id;?></td>
                    <td><? echo $rows[csf('invoice_no')];?></td>
                    <td align="center"><? echo change_date_format($rows[csf('invoice_date')]);?></td>
                    <td align="right"><? echo $rows['INVOICE_VALUE'];?></td>
                    <td align="right"><? echo $rows[csf('total_carton_qnty')];?></td>
                    <td align="center"><? echo change_date_format($rows[csf('ex_factory_date')]);?></td>
                    <td align="center" bgcolor="#FEE9FE"><? echo $rows[csf('bl_date')];?></td>
                    <td align="center" bgcolor="#FEE9DD"><? echo $submit_date_lib[$rows[csf('id')]];?></td>
                    <td align="center"><? echo $rows[csf('pending_days')];?></td>
                </tr>
                <? 
                 $i++;
            }
        }
            
		echo "</table><br>";
	}
	$message=ob_get_contents();
	ob_clean();
	

	ob_start();
	echo "Dear Sir,<br> Bank Submission Pending for Following Commercial Invoices . Please Check Submission Pending Above 14 Days Over Delay Mail Based on ".date("d-m-Y", $strtotime);
	$flag14=0;
	foreach($above14dataArr as $company_id=>$company_data_arr){?>
    <table border="1" rules="all">
    <tr>
    	<td colspan="11"><strong><? echo $comp_lib[$company_id];?></strong></td>
    </tr>
    <tr bgcolor="#CCCCCC">
        <th width="35">SL</th>
        <th>Buyer</th>
        <th>LC No</th>
        <th>Invoice No</th>
        <th>Invoice Date</th>
		<th>Invoice Val</th>
        <th>CTN Qty</th>
        <th>Ship Date</th>
        <th bgcolor="#FEE9FE">BL Date</th>
        <th bgcolor="#FEE9DD">Bank Sub Date</th>
        <th>Pending Days</th>
    </tr>
   <? 
   $i=1;
    foreach($company_data_arr as $rows){
		if($submit_date_lib[$rows[csf('id')]]==''){
			$flag14=1;
			$lc_sc_id=($rows[csf('is_lc')]==1)?$lc_lib[$rows[csf('lc_sc_id')]]:$sc_lib[$rows[csf('lc_sc_id')]];
			?>
			<tr>
				<td align="center"><? echo $i;?></td>
				<td><? echo $buyer_lib[$rows[csf('buyer_id')]];?></td>
				<td><? echo $lc_sc_id;?></td>
				<td><? echo $rows[csf('invoice_no')];?></td>
				<td align="center"><? echo change_date_format($rows[csf('invoice_date')]);?></td>
				<td align="right"><? echo $rows['INVOICE_VALUE'];?></td>
				<td align="right"><? echo $rows[csf('total_carton_qnty')];?></td>
				<td align="center"><? echo change_date_format($rows[csf('ex_factory_date')]);?></td>
				<td align="center" bgcolor="#FEE9FE"><? echo $rows[csf('bl_date')];?></td>
				<td align="center" bgcolor="#FEE9DD"></td>
				<td align="center"><? echo $rows[csf('pending_days')];?></td>
			</tr>
			<? 
			 $i++;
			}
		}
		echo "</table><br>";
	}
	$message_14=ob_get_contents();
	ob_clean();

	
/*		$to="";
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=21 and b.mail_user_setup_id=c.id and a.company_id=$compid";
		$sql_result_arr=sql_select($sql);
		foreach($sql_result_arr as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
*/
		$subject="Bank Submission pending  From 8 to 14 days Delay Based On :".date("d-m-Y", $strtotime);
		$subject_14="Bank Submission pending Above 14 days Over Delay Based On  :".date("d-m-Y", $strtotime);

		$to="sarker@logicsoftbd.com,tahir@urmigroup.net,mainul@urmigroup.net,mohiuddin_sumon@urmigroup.net,erpsupport@urmigroup.net";
		$to14="sarker@logicsoftbd.com,asif@urmigroup.net,mirashraful@urmigroup.net,shamarukh.fakhruddin@urmigroup.net,tahir@urmigroup.net,mainul@urmigroup.net,mohiuddin_sumon@urmigroup.net,erpsupport@urmigroup.net,emdad@urmigroup.net,mamunur@urmigroup.net,export.com@urmigroup.net";
		
		
		
		$header=mailHeader();
		//if($flag==1){echo sendMailMailer( $to, $subject, $message );}
		//if($flag14==1){echo sendMailMailer( $to14, $subject_14, $message_14 );}
		if($_REQUEST['isview']==1){
			echo $message;
			echo $message_14;
		}
		else
		{
			if($flag==1){echo sendMailMailer( $to, $subject, $message );}
			if($flag14==1){echo sendMailMailer( $to14, $subject_14, $message_14 );}

		 };

	echo $message;
	echo $message_14;

?>