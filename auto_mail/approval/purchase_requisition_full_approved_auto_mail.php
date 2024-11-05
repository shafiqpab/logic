<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../../includes/common.php');


$company_library = return_library_array("select id, company_short_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_short_name");
$store_arr = return_library_array("select id, STORE_NAME from LIB_STORE_LOCATION ", 'id', 'STORE_NAME');
$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');


extract($_REQUEST);

//$current = change_date_format(date('Y-m-d',time()),'','',1); 	

if($view_date){$currentTime = strtotime($view_date);}
else{$currentTime = time();}

$previous_date = change_date_format(date('Y-m-d', strtotime('-1 day', $currentTime)), '', '', 1);


$date_cond	= " and d.APPROVED_DATE between '" . $previous_date . "' and '" . $previous_date . " 11:59:59 PM'";

$sql = "SELECT a.ID,to_char(a.insert_date,'YYYY') as YEAR,a.COMPANY_ID,a.REQU_NO,a.REQU_PREFIX_NUM,a.REQUISITION_DATE,a.STORE_NAME ,b.ITEM_CATEGORY,b.RATE,b.REQUIRED_FOR,b.QUANTITY,b.STOCK,b.AMOUNT,c.ITEM_GROUP_ID,c.ITEM_DESCRIPTION, c.ITEM_SIZE,c.ORDER_UOM,b.PRODUCT_ID from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b,product_details_master c, approval_history d where a.id=b.mst_id and a.id=d.mst_id and d.entry_form=1 and d.current_approval_status=1  and b.product_id=c.id  and a.id = b.mst_id and b.item_category not in(1,2,3,12,13,14) and a.status_active=1 and a.is_deleted=0 and a.is_approved=1 $date_cond order by a.COMPANY_ID";

//echo $sql;die;

$sql_result = sql_select($sql);
$pro_id_arr = array();
$dataArr = array();
foreach ($sql_result as $row) {
	$pro_id_arr[$row[PRODUCT_ID]] = $row[PRODUCT_ID];
	$dataArr[$row[REQU_NO]][] = $row;
}

$rec_arr = array();
$rec_sql = "select b.PROD_ID, b.TRANSACTION_DATE,b.cons_quantity as REC_QTY, b.CONS_RATE from inv_receive_master a, inv_transaction b where a.id=b.mst_id " . where_con_using_array($pro_id_arr, 0, 'b.prod_id') . " and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.prod_id, b.id";
$rec_sql_result = sql_select($rec_sql);
foreach ($rec_sql_result as $row) {
	$rec_arr[$row[PROD_ID]]['transaction_date'] = $row[TRANSACTION_DATE];
	$rec_arr[$row[PROD_ID]]['rec_qty'] = $row[REC_QTY];
	$rec_arr[$row[PROD_ID]]['rate'] = $row[CONS_RATE];
}



ob_start();
?>
<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
	<caption><b style="font-size:24px;">Purchase Requisition Approved</b><br /> Date: <?= date('d-m-Y', strtotime($previous_date)); ?></caption>

	<thead bgcolor="#999999">
		<th width="35">SL</th>
		<th>Company</th>
		<th>Year</th>
		<th>Req. No</th>
		<th>Store Name</th>
		<th>Item Category</th>
		<th>Item Group</th>
		<th>Item Des & Item Size</th>
		<th>Req. For</th>
		<th>UOM</th>
		<th>Req. Qty.</th>
		<th>Rate</th>
		<th>Amount</th>
		<th>Stock</th>
		<th>Last Rec. Date</th>
		<th>Last Rec.Qty</th>
		<th>Last Rec. Rate</th>
	</thead>


	<tbody>
		<?
		$i = 1;
		$sumDataArr = array();
		foreach ($dataArr as $sql_result) {
			$colspan = count($sql_result);
			$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
		?>
			<tr bgcolor="<?= $bgcolor; ?>">
				<td rowspan="<?= $colspan; ?>" align="center"><?= $i; ?></td>
				<td rowspan="<?= $colspan; ?>" align="center"><?= $company_library[$sql_result[0][COMPANY_ID]]; ?></td>
				<td rowspan="<?= $colspan; ?>" align="center"><?= $sql_result[0][YEAR]; ?></td>
				<td rowspan="<?= $colspan; ?>" align="center"><?= $sql_result[0][REQU_PREFIX_NUM]; ?></td>
				<td rowspan="<?= $colspan; ?>"><?= $store_arr[$sql_result[0][STORE_NAME]]; ?></td>

				<?
				$flag = 0;
				foreach ($sql_result as $row) {
					$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
				?>
					<? if ($flag <> 0) { ?>
			<tr bgcolor="<?= $bgcolor; ?>">
			<? } ?>
			<td><?= $item_category[$row[ITEM_CATEGORY]]; ?></td>
			<td><?= $item_group_arr[$row[ITEM_GROUP_ID]]; ?></td>
			<td><?= $row[ITEM_DESCRIPTION] . ', ' . $row[ITEM_SIZE]; ?></td>
			<td><?= $use_for[$row[REQUIRED_FOR]]; ?></td>
			<td align="center"><?= $unit_of_measurement[$row[ORDER_UOM]]; ?></td>
			<td align="right"><?= $row[QUANTITY]; ?></td>
			<td align="right"><?= number_format($row[RATE], 2); ?></td>
			<td align="right"><?= $row[AMOUNT]; ?></td>
			<td align="right"><?= $row[STOCK]; ?></td>
			<td align="center"><?= change_date_format($rec_arr[$row[PRODUCT_ID]]['transaction_date']); ?></td>
			<td align="right"><?= $rec_arr[$row[PRODUCT_ID]]['rec_qty']; ?></td>
			<td align="right"><?= number_format($rec_arr[$row[PRODUCT_ID]]['rate'], 2); ?></td>
			</tr>
		<?
					$sumDataArr['total_amount'] += $row[AMOUNT];
					$sumDataArr['total_stock'] += $row[STOCK];
					$sumDataArr['grand_amount'] += $row[AMOUNT];
					$sumDataArr['grand_stock'] += $row[STOCK];

					$flag++;
				}
				$i++;
		?>
		<tr bgcolor="#EEE">
			<th colspan="12" align="right">Total:</th>
			<th align="right"><?= $sumDataArr['total_amount']; ?></th>
			<th align="right"><?= $sumDataArr['total_stock']; ?></th>
			<th colspan="3"></th>
		</tr>
	<?
			$sumDataArr['total_amount'] = 0;
			$sumDataArr['total_stock'] = 0;
		}

	?>
	</tbody>
	<tbody bgcolor="#FFFFCC">
		<th colspan="12" align="right">Grand Total:</th>
		<th align="right"><?= $sumDataArr['grand_amount']; ?></th>
		<th align="right"><?= $sumDataArr['grand_stock']; ?></th>
		<th colspan="3"></th>
	</tbody>
</table>

<?
$message = ob_get_contents();
ob_clean();




$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where a.id=b.mail_group_mst_id and b.mail_user_setup_id=c.id  and a.mail_item=81 AND a.MAIL_TYPE=1";
$mail_sql = sql_select($sql);
foreach ($mail_sql as $row) {
	if ($row['EMAIL_ADDRESS']) {
		$toArr[$row['EMAIL_ADDRESS']] = $row['EMAIL_ADDRESS'];
	}
}
$to = implode(',', $toArr);



if ($_REQUEST['isview'] == 1) {
	$mail_item = 81;
	if ($to) {
		echo 'Mail Item:' . $form_list_for_mail[$mail_item] . '=>' . $to;
	} else {
		echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>" . $form_list_for_mail[$mail_item] . "</b>]<br>";
	}
	echo $message;
} else {
	require_once('../../mailer/class.phpmailer.php');
	require_once('../setting/mail_setting.php');
	$header = mailHeader();
	$subject = "Purchase Requisition Approved";
	if ($to != "") {
		if($flag!=0)echo sendMailMailer($to, $subject, $message, $from_mail);
	}
}




?>






</body>

</html>