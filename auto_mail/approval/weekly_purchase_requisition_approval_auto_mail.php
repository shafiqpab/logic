<?php
	date_default_timezone_set("Asia/Dhaka");
	
	if(date("D",time())!='Sat'){echo "This mail send only Saturday.";exit();}
	
	
	require_once('../../includes/common.php');


	
	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d",time()),0)));
		$prev_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d",time()),0))),'','',1);
		$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-7 day', strtotime($current_date))),'','',1); 
	}
	
	
	
	
	
	
	$company_library=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	$store_arr=return_library_array( "select id, STORE_NAME from LIB_STORE_LOCATION", 'id', 'STORE_NAME' );	

	$dateCon=" and b.APPROVED_DATE between '$prev_date' and '$current_date 11:59:59 pm'";

	$sql="SELECT a.COMPANY_ID,a.REQU_PREFIX_NUM,a.REQUISITION_DATE, a.REQU_NO,a.STORE_NAME,b.APPROVED_DATE,
listagg(c.item_category, ',') within group (order by c.item_category) as ITEM_CATEGORY_ID, 
listagg(d.item_description, ',') within group (order by d.item_description) as ITEM_DESCRIPTION, 
listagg(d.ITEM_SIZE, ',') within group (order by d.ITEM_SIZE) as ITEM_SIZE,
TO_CHAR(a.insert_date,'YYYY') as YEAR,
sum(c.amount) as REQ_VALUE from inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c ,product_details_master d where a.id=b.mst_id and a.id = c.mst_id and b.entry_form=1  and c.product_id=d.id and c.item_category not in(1,2,3,12,13,14) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.is_approved in (1,3) $dateCon group by a.COMPANY_ID,a.REQU_PREFIX_NUM,a.REQUISITION_DATE, a.REQU_NO,a.STORE_NAME,b.APPROVED_DATE,TO_CHAR(a.insert_date,'YYYY')";
	$sql_result=sql_select($sql);
	
		   //echo $sql; 
	


ob_start();
?>
<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
    <thead>
    	<tr>
        	<th colspan="10">Weekly Purchase Requisition Approved On [<?=change_date_format($prev_date);?> To <?=change_date_format($current_date);?>]</th>
        </tr>
        <tr bgcolor="#999999">
            <th>SL</th>
            <th>PR Approval Date</th>
            <th>Company</th>
            <th>Year</th>
            <th>PR No</th>
            <th>PR Date</th>
            <th>Store Name</th>
            <th>Item Category</th>
            <th>Item Des</th>
            <th>Item Size</th>
            <th>Amount (TK.)</th>
        </tr>
    </thead>
    
    <tbody>
       <?
	   	$i=1;
	   	foreach($sql_result as $row)
		{
			$bgcolor=($i%2==0)?"#ffffff":"#D7E8FF";
			$itemNameArr=array();
			foreach(explode(',',$row[ITEM_CATEGORY_ID]) as $item_id){
				$itemNameArr[$item_id]=$item_category[$item_id];
			}
			$totalAmount+=$row[REQ_VALUE];
		?> 
        <tr bgcolor="<? echo $bgcolor; ?>">
            <td align="center"><?= $i;?></td>
            <td align="center"><?= change_date_format($row[APPROVED_DATE]);?></td>
            <td><?= $company_library[$row[COMPANY_ID]];?></td>
            <td align="center"><?= $row[YEAR];?></td>
            <td align="center"><?= $row[REQU_PREFIX_NUM];?></td>
            <td align="center"><?= change_date_format($row[REQUISITION_DATE]);?></td>
            <td><?= $store_arr[$row[STORE_NAME]];?></td>
            <td><?= implode(',',$itemNameArr);?></td>
            <td><?= $row[ITEM_DESCRIPTION];?></td>
            <td><?= $row[ITEM_SIZE];?></td>
            <td align="right"><?= number_format($row[REQ_VALUE],2);?></td>
        </tr>
        <?
		$i++;
		}
		
		?>
    </tbody>
    <tfoot bgcolor="#999999">
        <th colspan="10" align="right">Total Amount:</th>
        <th align="right"><?=number_format($totalAmount,2);?></th>
    </tfoot>
</table>

<?
	$message=ob_get_contents();
	ob_clean();
	
	
	
	$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address as MAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=85 and b.mail_user_setup_id=c.id and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	 //echo $sql;die;
	
	
	$mail_sql=sql_select($sql);
	$receverMailArr=array();
	foreach($mail_sql as $row)
	{
		$receverMailArr[$row[MAIL]]=$row[MAIL];
	}

	$to=implode(',',$receverMailArr);
	


	//echo $message;
		
	if($_REQUEST['isview']==1){
		$mail_item=85;
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		require_once('../../mailer/class.phpmailer.php');
		require_once('../setting/mail_setting.php');
		$header=mailHeader();
		$subject="Weekly Purchase Requisition Approved";
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	}



?>






</body>
</html>