<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
require_once('../setting/mail_setting.php');
extract($_REQUEST);

$user_maill_arr=return_library_array("select id,USER_EMAIL from USER_PASSWD","id","USER_EMAIL");
$company_arr=return_library_array( "select id, COMPANY_NAME from lib_company",'id','COMPANY_NAME');

$buyer_arr=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
$supplier_arr = return_library_array("select id,SUPPLIER_NAME from LIB_SUPPLIER where status_active=1 and is_deleted=0","id","SUPPLIER_NAME");


list($sysId,$mailId)=explode('__',$data);
$sysId=str_replace('*',',',$sysId);
	 
if($action=='unapproved_mail'){

 
	$sql="select a.COMPANY_ID,A.BOOKING_NO,A.BUYER_ID,a.BOOKING_DATE,a.DELIVERY_DATE from wo_booking_mst a,wo_booking_dtls b where  a.booking_no=b.booking_no and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($sysId) group by a.COMPANY_ID,A.BOOKING_NO,A.BUYER_ID,a.BOOKING_DATE,a.DELIVERY_DATE";
	  //echo $sql;die;
	$sql_res=sql_select($sql);
	foreach($sql_res as $row)
	{
		$company_name=$row['COMPANY_ID'];
	}
	
 	
	
		
	$mailArr=array();
	$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=96 and a.MAIL_TYPE in(2,0) and b.mail_user_setup_id=c.id and a.company_id=$company_name AND a.MAIL_TYPE=1 and  c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		$mailArr[]=$row['EMAIL_ADDRESS'];
	}
	$mailArr[]=str_replace('*',',',$mailId);


	ob_start();	
	?>
	Dear Concerned,	<br />			
	Please check unapproved booking list.				
	
	<table rules="all" border="1">
		<tr bgcolor="#CCCCCC">
			<td>SL</td>
			<td>Booking No</td>
			<td>Buyer</td>
			<td>Booking Date</td>
			<td>Delivery Date</td>
			<td>Unapproved Date</td>
		</tr>
		<?php 
		$i=1;
		foreach($sql_res as $row){ 
			?>
			<tr>
				<td><?=$i;?></td>
				<td><?=$row['BOOKING_NO']?></td>
				<td><?=$buyer_arr[$row['BUYER_ID']]?></td>
				<td><?=change_date_format($row['BOOKING_DATE'])?></td>
				<td><?=change_date_format($row['DELIVERY_DATE'])?></td>
				<td><?=date('d-m-Y',time());?></td>
			</tr>
		<?php } ?>
	</table>
	<?	
		
		$message=ob_get_contents();
		ob_clean();
		$to=implode(',',$mailArr);


		if($_REQUEST['isview']==1){
			$mail_item=96;
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo $message;
		}
		else{
			$subject="Fabric Booking UnApproved";
			$header=mailHeader();
			if($to!="") echo sendMailMailer( $to, $subject, $message, $from_mail);
		}
	 
	exit();

}

if($action=='deny_mail'){

	$sql="select a.PAY_MODE,a.FABRIC_SOURCE,a.COMPANY_ID,a.INSERTED_BY,A.BUYER_ID,A.SUPPLIER_ID,A.ID, A.BOOKING_NO, e.REFUSING_REASON as APPROVAL_CAUSE,f.STYLE_REF_NO,f.JOB_NO ,c.PO_NUMBER  AS PO_NUMBER from wo_po_break_down c,wo_booking_dtls d, wo_po_details_master f, wo_booking_mst a left join REFUSING_CAUSE_HISTORY e on a.id = e.MST_ID where  a.booking_no=d.booking_no and d.job_no=c.job_no_mst  and f.job_no=d.job_no and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and a.id in($sysId) group by a.PAY_MODE,a.FABRIC_SOURCE,a.COMPANY_ID,c.PO_NUMBER,a.INSERTED_BY,A.BUYER_ID,A.SUPPLIER_ID,A.ID, A.BOOKING_NO, e.REFUSING_REASON,f.STYLE_REF_NO,f.JOB_NO";
	//echo $sql;
	$dataArr=array();$poArr=array();$comArr=array();
	$sql_dtls=sql_select($sql);// and e.entry_form=7
		foreach($sql_dtls as $row)
		{
			$dataArr[$row['COMPANY_ID']][$row['BOOKING_NO']]=$row;
			$poArr[$row['COMPANY_ID']][$row['BOOKING_NO']][$row['PO_NUMBER']]=$row['PO_NUMBER'];
			$comArr[$row['COMPANY_ID']]=$row['COMPANY_ID'];
		}
	
		
	foreach($comArr as $company_name){
		$mailArr=array();
		$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=65 and a.MAIL_TYPE in(2,0) and b.mail_user_setup_id=c.id and a.company_id=$company_name  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			$mailArr[]=$rows[EMAIL_ADDRESS];
		}
		$mailArr[]=str_replace('*',',',$mailId);
	
	
		ob_start();	
		?>
		Dear Concerned,	<br />			
		Your approval request against the following reference is denied.				
		
		<table rules="all" border="1">
			<tr bgcolor="#CCCCCC">
				<td>SL</td>
				<td>Purchase Order No</td>
				<td>Style Ref</td>
				<td>Job No</td>
				<td>Order No</td>
				<td>Buyer</td>
				<td>Supplier Name</td>
				<td>Deny cause</td>
			</tr>
			
			<?php 
			$i=1;
			foreach($dataArr[$company_name] as $row){ 
				$mailArr[$row['INSERTED_BY']]=$user_maill_arr[$row['INSERTED_BY']];
				$row['SUPPLIER_NAME'] = ($row['FABRIC_SOURCE']==1 && $row['PAY_MODE']==5)?$company_arr[$row['SUPPLIER_ID']]:$supplier_arr[$row['SUPPLIER_ID']];
			?>
			<tr>
				<td><?=$i;?></td>
				<td><?=$row['BOOKING_NO']?></td>
				<td><?=$row['STYLE_REF_NO']?></td>
				<td><?=$row['JOB_NO']?></td>
				<td><?=implode(', ',$poArr[$company_name][$row['BOOKING_NO']]);?></td>
				<td><?=$buyer_arr[$row['BUYER_ID']]?></td>
				<td><?=$row['SUPPLIER_NAME']?></td>
				<td><?=$row['APPROVAL_CAUSE']?></td>
			</tr>
			<?php } ?>
		</table>
		<?	
			
			$message=ob_get_contents();
			ob_clean();
			$to=implode(',',$mailArr);

			
			if($_REQUEST['isview']==1){
				$mail_item=65;
				if($to){
					echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
				}else{
					echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
				}
				echo $message;
			}
			else{
				$header=mailHeader();
				$subject="Fabric Booking Approval";
				if($to!="") echo sendMailMailer( $to, $subject, $message, $from_mail);
			}
			
			
	}
	exit();
}//end action



	


?> 