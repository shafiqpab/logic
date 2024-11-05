<?php
	date_default_timezone_set("Asia/Dhaka");
	// require_once('../mailer/class.phpmailer.php');
	require_once('../includes/common.php');
	require_once('setting/mail_setting.php');

	$company_library=return_library_array( "select id, company_short_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_short_name"  );
	extract($_REQUEST);

	//http://localhost/platform-v3.5/auto_mail/purchase_requisition_approval_auto_mail.php?company_id=3&req_id=257,4065


	$sql="SELECT a.id,a.REQU_PREFIX_NUM,a.REQUISITION_DATE, a.REQU_NO,a.STORE_NAME,a.INSERTED_BY, b.APPROVED_BY ,b.APPROVED_NO,b.APPROVED_DATE,c.ITEM_CATEGORY, sum(c.amount) as REQ_VALUE
	from inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c 
	where a.id=b.mst_id and a.id = c.mst_id and b.entry_form=1 and a.company_id=$company_id and c.item_category not in(1,2,3,12,13,14) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.is_approved in (1,3) and a.id in($req_id) 
	group by a.id,a.REQU_PREFIX_NUM,a.REQUISITION_DATE, a.REQU_NO,a.STORE_NAME,a.INSERTED_BY, b.APPROVED_BY ,b.APPROVED_NO,b.APPROVED_DATE,c.ITEM_CATEGORY
	order by a.id
	";
	$sql_result=sql_select($sql);

	if(count($sql_result) == 0){
		$sql="SELECT a.id,a.REQU_PREFIX_NUM,a.REQUISITION_DATE, a.REQU_NO,a.STORE_NAME,a.INSERTED_BY, 0 as APPROVED_BY ,0 as APPROVED_NO,'00-00-0000' as APPROVED_DATE,c.ITEM_CATEGORY, sum(c.amount) as REQ_VALUE
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls c 
	where  a.id = c.mst_id and a.company_id=$company_id and c.item_category not in(1,2,3,12,13,14) and a.status_active=1 and a.is_deleted=0  and a.is_approved in (1,3,2) and a.id in($req_id) 
	group by a.id,a.REQU_PREFIX_NUM,a.REQUISITION_DATE, a.REQU_NO,a.STORE_NAME,a.INSERTED_BY, c.ITEM_CATEGORY
	order by a.id";
		$sql_result=sql_select($sql);
	}
	
		 //echo $sql;die;
	
	$user_arr=return_library_array( "select id, user_name from user_passwd", 'id', 'user_name' );	
	$store_arr=return_library_array( "select id, STORE_NAME from LIB_STORE_LOCATION where COMPANY_ID=$company_id", 'id', 'STORE_NAME' );	
	$desiganation_arr=return_library_array( "select id, DESIGNATION from user_passwd", 'id', 'DESIGNATION' );	


ob_start();
?>
<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1002" class="rpt_table" id="tbl_list_search">
    <thead bgcolor="#999999">
    	<th width="35">SL</th>
        <th>Requisition No</th>
        <th>Requisition Value</th>
        <th>Requisition Date</th>
        <th>Item Category</th>
        <th>Req. By:</th>
        <th>Store Name</th>
        <th>Signatory</th>
        <th>Designation</th>
        <th>Approval Date & Time</th>
        <th>Approve No</th>
    </thead>
    
    <tbody>
       <?
	   	$i=1;
	   	foreach($sql_result as $row)
		{
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
		?> 
        <tr bgcolor="<? echo $bgcolor; ?>">
            <td align="center"><?= $i;?></td>
            <td align="center"><?= $row[REQU_PREFIX_NUM];?></td>
            <td align="right"><?= $row[REQ_VALUE];?></td>
            <td align="center"><?= change_date_format($row[REQUISITION_DATE]);?></td>
            <td><?= $item_category[$row[ITEM_CATEGORY]];?></td>
            <td><?= $user_arr[$row[INSERTED_BY]];?></td>
            <td><?= $store_arr[$row[STORE_NAME]];?></td>
            <td><?= $user_arr[$row[APPROVED_BY]];?></td>
            <td><?= $desiganation_arr[$row[APPROVED_BY]];?></td>
            <td align="center"><?= $row[APPROVED_DATE];?></td>
            <td align="center"><?= $row[APPROVED_NO];?></td>
        </tr>
        <?
		$i++;
		}
		
		?>
    </tbody>
</table>

<?
	$message=ob_get_contents();
	ob_clean();
	
	
	
	$to="";
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=39 AND a.MAIL_TYPE=1 and b.mail_user_setup_id=c.id and a.company_id=$company_id";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
	$header=mailHeader();
	
	$subject="Purchase Requisition Approval Notification";
	//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	if($_REQUEST['isview']==1){
		echo $message;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	}

		
	
?>






</body>
</html>