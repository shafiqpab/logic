<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');
extract($_REQUEST);

if($action=='pending_purchase_requisition_for_approval'){
list($sys_id,$mail,$body)=explode('__',$data);
$item_wise_mail_address_arr=array(
	4=>"abc@gmail.com,xyz@gmail.com",
	2=>"sss@gmail.com,sss@gmail.com",
);


	
	$sql="select a.COMPANY_ID,a.REQU_PREFIX_NUM,a.REQU_NO,a.REQUISITION_DATE,a.REQ_BY,b.ITEM_CATEGORY,b.AMOUNT from inv_purchase_requisition_mst a,INV_PURCHASE_REQUISITION_DTLS b where a.id=b.mst_id and  a.id=$sys_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.entry_form=69 and a.READY_TO_APPROVE=1 order by a.id desc";
	 //echo $sql;
	$sql_result=sql_select($sql);
	ob_start();
	?>
            
           <?
           $i=1;$total_amount=0;
           foreach($sql_result as $row){
           	$total_amount+=$row[AMOUNT];
			$itemArr[$row[ITEM_CATEGORY]]=$item_category[$row[ITEM_CATEGORY]];
           }
           ?>
        <table border="1" rules="all">
            <tr bgcolor="#999999">
                <th colspan="3">Pending Purchase Requisition for Approval</th>
            </tr>
            <tr>
                <th align="left">Sys. Id</th><th>:</th><td><? echo $row[REQU_PREFIX_NUM];?></td>
            </tr>
            <tr>
               <th align="left">Requisition No.</th><th>:</th><td><? echo $row[REQU_NO];?></td>
            </tr>
            <tr>
                <th align="left">Req. Date</th><th>:</th><td><? echo change_date_format($row[REQUISITION_DATE]);?></td>
            </tr>
            <tr>
                <th align="left">Item Category</th><th>:</th><td><? echo implode(', ',$itemArr);?></td>
            </tr>
            <tr>
                <th align="left">Amount</th><th>:</th><td><? echo number_format($total_amount,2); ?></td>
            </tr>
            <tr>
                <th align="left">Required by</th><th>:</th><td><? echo $row[REQ_BY];?></td>
            </tr>
        </table>
        <?
	$emailBody=ob_get_contents();
	ob_clean();

	
 
		$elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1  AND a.ENTRY_FORM=1 and a.company_id={$sql_result[0][COMPANY_ID]} order by a.SEQUENCE_NO"; //and a.page_id=2150 and a.entry_form=46
		 //echo $elcetronicSql;die;
		
		$elcetronicSqlRes=sql_select($elcetronicSql);
		foreach($elcetronicSqlRes as $rows){
			
			if($rows[BUYER_ID]!=''){
				foreach(explode(',',$rows[BUYER_ID]) as $bi){
					if($rows[USER_EMAIL]!='' && $bi==$buyer_name_id){$mailToArr[]=$rows[USER_EMAIL];}
					if($rows[BYPASS]==2){break;}
				}
			}
			else{
				if($rows[USER_EMAIL]){$mailToArr[]=$rows[USER_EMAIL];}
				if($rows[BYPASS]==2){break;}
			}

		}
 
 	if($mail!=''){$mailToArr[]=$mail;}
 	$to=implode(',',$mailToArr);
	

	$subject="Purchase Requisition Ready for Approval";
	$header=mailHeader();
	//if($to!=""){echo sendMailMailer( $to, $subject, $emailBody, $from_mail );}
	//echo $emailBody;
	if($_REQUEST['isview']==1){
		echo $emailBody;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $emailBody, $from_mail );}
	}
	
	

exit();	
}







?>
