<?php
date_default_timezone_set("Asia/Dhaka");
extract($_REQUEST);

$req_url_arr=explode('/',$_SERVER['REQUEST_URI']);
$base_path = $_SERVER['SERVER_NAME'].'/'.$req_url_arr[1];


require_once('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
//require '../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
require_once('setting/mail_setting.php');

if($action=='lc'){
	//http://59.152.60.250:14080/teamerp/auto_mail/lcsc_notification_auto_mail.php?action=lc&sys_id=GKD-LC-20-00001

	$comp_lib = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id","company_name");
	$bank_lib = return_library_array("select id, BANK_NAME from LIB_BANK where  status_active=1 and is_deleted=0","id", "BANK_NAME");

	$sql="select A.ID,a.EXPORT_LC_NO,A.EXPORT_LC_SYSTEM_ID,A.BENEFICIARY_NAME,A.LIEN_BANK,A.LC_VALUE,A.INTERNAL_FILE_NO,A.BANK_FILE_NO,a.SHIPPING_MODE  from COM_EXPORT_LC a where a.status_active=1 and a.is_deleted=0 and a.EXPORT_LC_SYSTEM_ID='$sys_id'";// and a.invoice_no='123123'
	$result=sql_select($sql);
	$row=$result[0];
	
	if($sys_id==''){echo "SYSTEM ID WRONG";exit();}
	
	$is_sales=return_field_value("is_sales","com_export_lc_order_info","com_export_lc_id=".$row[ID]." and status_active=1","is_sales");
	if($is_sales==0)
	{
		if($data_ref[1]==23 || $data_ref[1]==35 || $data_ref[1]==36 || $data_ref[1]==37 || $data_ref[1]==45)
		{
			
			if($data_ref[1]==35 || $data_ref[1]==36)
			{
				$sql = "select wm.id, ci.id as idd, 0 as GMTS_ITEM_ID, wm.embellishment_job as PO_NUMBER, sum(wb.amount) as PO_TOTAL_PRICE, sum(wb.order_quantity) as PO_QUANTITY, wm.delivery_date as shipment_date, wb.job_no_mst, wb.cust_style_ref as STYLE_REF_NO, wb.ORDER_UOM, 1 as ratio, ci.ATTACHED_QNTY, ci.ATTACHED_RATE, ci.ATTACHED_VALUE, ci.status_active 
				from subcon_ord_dtls wb, subcon_ord_mst wm, com_export_lc_order_info ci 
				where wb.job_no_mst = wm.embellishment_job and wm.id=ci.wo_po_break_down_id and ci.com_export_lc_id=".$row[ID]." and ci.status_active = 1 and ci.is_deleted = 0
				group by  wm.id, ci.id, wm.embellishment_job, wm.delivery_date, wb.job_no_mst, wb.cust_style_ref, wb.ORDER_UOM, ci.ATTACHED_QNTY, ci.ATTACHED_RATE, ci.ATTACHED_VALUE, ci.status_active 
				order by ci.id";
			}
			else
			{
				$sql = "select wm.id, ci.id as idd, 0 as GMTS_ITEM_ID, wm.subcon_job as PO_NUMBER, sum(wb.amount) as PO_TOTAL_PRICE, sum(wb.order_quantity) as PO_QUANTITY, wm.delivery_date as shipment_date, wb.job_no_mst, wb.cust_style_ref as STYLE_REF_NO, wb.ORDER_UOM, 1 as ratio, ci.ATTACHED_QNTY, ci.ATTACHED_RATE, ci.ATTACHED_VALUE, ci.status_active 
				from subcon_ord_dtls wb, subcon_ord_mst wm, com_export_lc_order_info ci 
				where wb.job_no_mst = wm.subcon_job and wm.id=ci.wo_po_break_down_id and ci.com_export_lc_id=".$row[ID]." and ci.status_active = 1 and ci.is_deleted = 0
				group by  wm.id, ci.id, wm.subcon_job, wm.delivery_date, wb.job_no_mst, wb.cust_style_ref, wb.ORDER_UOM, ci.ATTACHED_QNTY, ci.ATTACHED_RATE, ci.ATTACHED_VALUE, ci.status_active 
				order by ci.id";
			}
			
		}
		else
		{
			if ($db_type == 0) {
			$actual_po_arr = return_library_array("select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "acc_po_no");
			} else {
				$actual_po_arr = return_library_array("select po_break_down_id, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "acc_po_no");
			}
		
			$sql = "select wb.id, ci.id as idd, wm.GMTS_ITEM_ID, wb.PO_NUMBER, wb.PO_TOTAL_PRICE, wb.PO_QUANTITY, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.STYLE_REF_NO, wm.ORDER_UOM, wm.total_set_qnty as ratio, ci.ATTACHED_QNTY, ci.ATTACHED_RATE, ci.ATTACHED_VALUE, ci.status_active from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_export_lc_id=".$row[ID]." and ci.status_active = 1 and ci.is_deleted = 0 order by ci.id";
		}
		
	}
	else
	{
		$sql = "select wm.id, ci.id as idd, 0 as GMTS_ITEM_ID, wm.job_no as PO_NUMBER, sum(wb.amount) as PO_TOTAL_PRICE, sum(wb.finish_qty) as PO_QUANTITY, wm.delivery_date as shipment_date, wb.job_no_mst, wm.STYLE_REF_NO, wb.ORDER_UOM, 1 as ratio, ci.ATTACHED_QNTY, ci.ATTACHED_RATE, ci.ATTACHED_VALUE, ci.status_active 
		from fabric_sales_order_dtls wb, fabric_sales_order_mst wm, com_export_lc_order_info ci 
		where wm.id = wb.mst_id and wb.mst_id=ci.wo_po_break_down_id and ci.com_export_lc_id=".$row[ID]." and ci.status_active = 1 and ci.is_deleted = 0
		group by  wm.id, ci.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.STYLE_REF_NO, wb.ORDER_UOM, ci.ATTACHED_QNTY, ci.ATTACHED_RATE, ci.ATTACHED_VALUE, ci.status_active 
		order by ci.id";
	}
     // echo $sql;
	  $result_dtls=sql_select($sql);
	 
		$imgSql="select FILE_TYPE,IMAGE_LOCATION,REAL_FILE_NAME, MASTER_TBLE_ID, FORM_NAME from common_photo_library where form_name in('proforma_invoice') and is_deleted=0  ".where_con_using_array(array($row[ID]),1,'MASTER_TBLE_ID')."";//'quotation_entry',
		$imgSqlResult=sql_select($imgSql);
		foreach($imgSqlResult as $rows){
			$att_file_arr[]='../'.$rows[IMAGE_LOCATION].'**'.$rows[REAL_FILE_NAME];
		}

	//print_r($att_file_arr);die;
	ob_start();
		?>
        <table border="0">
        	<tr><td><b>Beneficiary</b></td><td>:</td><td><?= $comp_lib[$row['BENEFICIARY_NAME']]; ?></td></tr>
        	<tr><td><b>LC Number</b></td><td>:</td><td><?= $row['EXPORT_LC_NO']; ?></td></tr>
        	<tr><td><b>Lien Bank Name</b></td><td>:</td><td><?= $bank_lib[$row['LIEN_BANK']]; ?></td></tr>
        	<tr><td><b>LC Value</b></td><td>:</td><td><?= number_format($row['LC_VALUE'],2); ?></td></tr>
        	<tr><td><b>Internal File No</b></td><td>:</td><td><?= $row['INTERNAL_FILE_NO']; ?></td></tr>
        	<tr><td><b>Bank File No</b></td><td>:</td><td><?= $row['BANK_FILE_NO']; ?></td></tr>
        	<tr><td><b>Shipping Mode</b></td><td>:</td><td><?= $shipment_mode[$row['SHIPPING_MODE']]; ?></td>
        </table>
        <table border="1" rules="all">
        	<tr bgcolor="#999999">
            	<th>Order Number</th>
            	<th>Style Ref</th>
            	<th>Gmts. Item</th>
            	<th>UOM</th>
            	<th>Rate</th>
            	<th>Order QTY</th>
            	<th>Value</th>
            	<th>Attach Qty</th>
            	<th>Attached Value</th>
              </tr>
              <? 
			  $total_po_qty=0;$total_po_val=0;$total_att_qty=0;$total_att_val=0;
			  foreach($result_dtls as $rows){ 
				  $total_po_qty+=$rows[PO_QUANTITY];
				  $total_po_val+=$rows[PO_TOTAL_PRICE];
				  $total_att_qty+=$rows[ATTACHED_QNTY];
				  $total_att_val+=$rows[ATTACHED_VALUE];
			  
			  ?>
              <tr>
               	<td><?= $rows[PO_NUMBER];?></td>
               	<td><?= $rows[STYLE_REF_NO];?></td>
               	<td><?= $garments_item[$rows['GMTS_ITEM_ID']];?></td>
               	<td align="center"><?= $unit_of_measurement[$rows['ORDER_UOM']];?></td>
               	<td align="right"><?= number_format($rows['ATTACHED_RATE'],2);?></td>
               	<td align="right"><?= number_format($rows['PO_QUANTITY']);?></td>
               	<td align="right"><?= number_format($rows['PO_TOTAL_PRICE'],2);?></td>
               	<td align="right"><?= number_format($rows['ATTACHED_QNTY']);?></td>
               	<td align="right"><?= number_format($rows['ATTACHED_VALUE'],2);?></td>
              </tr>
              <? } ?>
              <tfoot>
               	<td align="right" colspan="5">Total:</td>
               	<td align="right"><?= $total_po_qty;?></td>
               	<td align="right"><?= number_format($total_po_val,2);?></td>
               	<td align="right"><?= $total_att_qty;?></td>
               	<td align="right"><?= number_format($total_att_val,2);?></td>
              </tfoot>
         </table>
         
         <?
	
	$message=ob_get_contents();
	ob_clean();
	
	$to="";	
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=36 and b.mail_user_setup_id=c.id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and a.company_id=".$row['BENEFICIARY_NAME']." ";//and a.company_id=$compid 
	
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
	
 	$subject = "LC Notification";
	
	$header=mailHeader();
 

	if($_REQUEST['isview']==1){
		$mail_item=36;
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);
	}

	//echo $message;
	exit();
	
}
 
if($action=='sc'){
	// echo "SC";die;
	//http://59.152.60.250:14080/teamerp/auto_mail/lcsc_notification_auto_mail.php?action=sc&sys_id=GKD-SC-20-00034

	$comp_lib = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id","company_name");
	$bank_lib = return_library_array("select id, BANK_NAME from LIB_BANK where  status_active=1 and is_deleted=0","id", "BANK_NAME");

	$sql="select A.ID,a.CONTRACT_NO,A.CONTACT_SYSTEM_ID,A.BENEFICIARY_NAME,A.LIEN_BANK,A.CONTRACT_VALUE,A.INTERNAL_FILE_NO,A.BANK_FILE_NO,a.SHIPPING_MODE from com_sales_contract a where a.status_active=1 and a.is_deleted=0 and a.CONTACT_SYSTEM_ID='$sys_id'";// and a.invoice_no='123123'
	// echo $sql;die;
	$result=sql_select($sql);
	$row=$result[0];
	
	if($sys_id==''){echo "SYSTEM ID WRONG";exit();}
	
	$is_sales=return_field_value("is_sales","com_export_lc_order_info","com_export_lc_id=".$row[ID]." and status_active=1","is_sales");
	if($is_sales==0)
	{
		if ($db_type == 0) {
			$actual_po_arr = return_library_array("select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "acc_po_no");
		} else {
			$actual_po_arr = return_library_array("select po_break_down_id, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "acc_po_no");
		}
	
		$sql = "select WB.ID, CI.ID AS IDD, WM.GMTS_ITEM_ID, WB.PO_NUMBER, WB.PO_TOTAL_PRICE, WB.PO_QUANTITY, WB.PUB_SHIPMENT_DATE AS SHIPMENT_DATE, WB.JOB_NO_MST, WM.STYLE_REF_NO, WM.ORDER_UOM, WM.TOTAL_SET_QNTY AS RATIO, CI.ATTACHED_QNTY, CI.ATTACHED_RATE, CI.ATTACHED_VALUE, CI.STATUS_ACTIVE from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='".$row['ID']."' and ci.status_active = 1 and ci.is_deleted = 0 order by ci.id";
	}
	else
	{
		$sql = "select WM.ID, CI.ID AS IDD, 0 AS GMTS_ITEM_ID, WM.JOB_NO AS PO_NUMBER, SUM(WB.AMOUNT) AS PO_TOTAL_PRICE, SUM(WB.FINISH_QTY) AS PO_QUANTITY, WM.DELIVERY_DATE AS SHIPMENT_DATE, WB.JOB_NO_MST, WM.STYLE_REF_NO, WB.ORDER_UOM, 1 AS RATIO, CI.ATTACHED_QNTY, CI.ATTACHED_RATE, CI.ATTACHED_VALUE, CI.STATUS_ACTIVE 
		from fabric_sales_order_dtls wb, fabric_sales_order_mst wm, com_sales_contract_order_info ci 
		where wm.id = wb.mst_id and wb.mst_id=ci.wo_po_break_down_id and ci.com_sales_contract_id='".$row['ID']."' and ci.status_active = 1 and ci.is_deleted = 0
		group by  wm.id, ci.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		order by ci.id";
	}
    $result_dtls=sql_select($sql);
	//echo $sql;
	 
 		
	
		$imgSql="select FILE_TYPE,IMAGE_LOCATION,REAL_FILE_NAME, MASTER_TBLE_ID, FORM_NAME from common_photo_library where form_name in('sales_contract_entry') and is_deleted=0  ".where_con_using_array(array($row['ID']),1,'MASTER_TBLE_ID')."";//'quotation_entry',
		$imgSqlResult=sql_select($imgSql);
		foreach($imgSqlResult as $rows){
			$att_file_arr[]='../'.$rows['IMAGE_LOCATION'].'**'.$rows['REAL_FILE_NAME'];
		}
		
		 //echo $imgSql;die;
		
	  //print_r($att_file_arr);;die;
	//$att_file_arr=array('../file_upload/sales_contract_entry_82_3296.pdf**14001-0532285SAD.pdf');
	
	ob_start();
	?>
        <table border="0">
        	<tr><td><b>Beneficiary</b></td><td>:</td><td><?= $comp_lib[$row['BENEFICIARY_NAME']]; ?></td></tr>
        	<tr><td><b>SC Number</b></td><td>:</td><td><?= $row['CONTRACT_NO']; ?></td></tr>
        	<tr><td><b>Lien Bank Name</b></td><td>:</td><td><?= $bank_lib[$row['LIEN_BANK']]; ?></td></tr>
        	<tr><td><b>SC Value</b></td><td>:</td><td><?= number_format($row['CONTRACT_VALUE'],2); ?></td></tr>
        	<tr><td><b>Internal File No</b></td><td>:</td><td><?= $row['INTERNAL_FILE_NO']; ?></td></tr>
        	<tr><td><b>Bank File No</b></td><td>:</td><td><?= $row['BANK_FILE_NO']; ?></td></tr>
        	<tr><td><b>Shipping Mode</b></td><td>:</td><td><?= $shipment_mode[$row['SHIPPING_MODE']]; ?></td>
        </table>
        <table border="1" rules="all">
        	<tr bgcolor="#999999">
            	<th>Order Number</th>
            	<th>Style Ref</th>
            	<th>Gmts. Item</th>
            	<th>UOM</th>
            	<th>Rate</th>
            	<th>Order QTY</th>
            	<th>Value</th>
            	<th>Attach Qty</th>
            	<th>Attached Value</th>
              </tr>
              <? 
			  $total_po_qty=0;$total_po_val=0;$total_att_qty=0;$total_att_val=0;
			  foreach($result_dtls as $rows){ 
				  $total_po_qty+=$rows['PO_QUANTITY'];
				  $total_po_val+=$rows['PO_TOTAL_PRICE'];
				  $total_att_qty+=$rows['ATTACHED_QNTY'];
				  $total_att_val+=$rows['ATTACHED_VALUE'];
			  
			  ?>
              <tr>
               	<td><?= $rows['PO_NUMBER'];?></td>
               	<td><?= $rows['STYLE_REF_NO'];?></td>
               	<td><?= $garments_item[$rows['GMTS_ITEM_ID']];?></td>
               	<td align="center"><?= $unit_of_measurement[$rows['ORDER_UOM']];?></td>
               	<td align="right"><?= number_format($rows['ATTACHED_RATE'],2);?></td>
               	<td align="right"><?= $rows['PO_QUANTITY'];?></td>
               	<td align="right"><?= number_format($rows['PO_TOTAL_PRICE'],2);?></td>
               	<td align="right"><?= $rows['ATTACHED_QNTY'];?></td>
               	<td align="right"><?= number_format($rows['ATTACHED_VALUE'],2);?></td>
              </tr>
              <? } ?>
              <tfoot>
               	<td align="right" colspan="5">Total:</td>
               	<td align="right"><?= $total_po_qty;?></td>
               	<td align="right"><?= number_format($total_po_val,2);?></td>
               	<td align="right"><?= $total_att_qty;?></td>
               	<td align="right"><?= number_format($total_att_val,2);?></td>
              </tfoot>
        </table>
		<br>
		<?
		if($mail_body){
			?>
			    <p><?= $mail_body;?></p>
			<?php
	    } 
    
	//echo "SC";die;
	$message = ob_get_contents();
	if($manual ==1){
		ob_clean();
	}
	
	
	$to="";	
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=36 and b.mail_user_setup_id=c.id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and a.IS_DELETED=0 and a.STATUS_ACTIVE=1  and a.company_id=".$row['BENEFICIARY_NAME']." ";

	//echo $sql;die;
	
	$mail_sql = sql_select($sql);
	foreach($mail_sql as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
	
	if($manual ==1){
		$to = $mail;
	}

 	$subject = "SC Notification";
	
	$header = mailHeader();

	//print_r($to);die;

	if($_REQUEST['isview'] == 1){
		// echo "isview";die;
		$mail_item=36;
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		//echo $message;
	}
	else{
		// $to = 'alhassan.cse@gmail.com';
		if($to!="") {
			echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);
			// echo "Mail send";
		}
	}
	exit();
}

?>