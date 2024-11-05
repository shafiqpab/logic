<?php
date_default_timezone_set("Asia/Dhaka");
include('../../includes/common.php');
//include('../../mailer/class.phpmailer.php');
include('../setting/mail_setting.php');
session_start();
extract($_REQUEST);
$user_id = $_SESSION['logic_erp']['user_id'];

//$user_id=26;
 

$user_maill_arr=return_library_array("select id,USER_EMAIL from USER_PASSWD","id","USER_EMAIL");
$lib_com_arr=return_library_array("select id,COMPANY_NAME from LIB_COMPANY","id","COMPANY_NAME");
$supplier_arr = return_library_array("select id,SUPPLIER_NAME from LIB_SUPPLIER where status_active=1 and is_deleted=0","id","SUPPLIER_NAME");

list($sysId,$mailId,$type,$alter_user_id)=explode('__',$data);
$sysId=str_replace('*',',',$sysId);
if($mailId){$mailToArr[]=str_replace('*',',',$mailId);}

$user_id = ($alter_user_id != "")?$alter_user_id:$user_id;
	 

if($action=='pi_approval_mail'){

		$sql="  SELECT a.NET_TOTAL_AMOUNT,a.IMPORTER_ID,A.ID,a.PI_DATE,a.ITEM_CATEGORY_ID,a.PI_NUMBER,a.SUPPLIER_ID,a.INSERTED_BY  FROM com_pi_master_details a,  com_pi_item_details b  WHERE a.id=b.pi_id  AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0  AND a.id IN ($sysId) GROUP BY a.NET_TOTAL_AMOUNT,A.ID,a.IMPORTER_ID,a.PI_DATE,a.ITEM_CATEGORY_ID,a.PI_NUMBER,a.SUPPLIER_ID,a.INSERTED_BY ";
		$sql_dtls=sql_select($sql);
		
	 // echo $sql;die;
		
		$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","entry_form=27 and user_id=$user_id and company_id ={$sql_dtls[0]['IMPORTER_ID']} and is_deleted = 0");

 
		$elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1  AND a.entry_form=27 and a.SEQUENCE_NO>$user_sequence_no and a.company_id={$sql_dtls[0][IMPORTER_ID]} order by a.SEQUENCE_NO"; //and a.page_id=2150 and a.entry_form=46
		// echo $elcetronicSql;die;
		
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

	
		ob_start();	
		?>
		<b>Dear Concerned,</b>	<br />			
		PI has been <?= ($type ==1)?'Unapproved':'Approved';?> - Please Proceed.	<br /><br />						
		
		<table rules="all" border="1">
			<tr bgcolor="#CCCCCC">
				<td>SL</td>
				<td>Company</td>
				<td>System ID</td>
				<td>Item Category</td>
				<td>PI Receive Date</td>
				<td>PI No</td>
				<td>PI Value</td>
				<td>Supplier</td>
			</tr>
			
			<?php 
			$i=1;
			foreach($sql_dtls as $row){ 
				if($user_maill_arr[$row['INSERTED_BY']]){$mailArr[$row['INSERTED_BY']]=$user_maill_arr[$row['INSERTED_BY']];}
				if($user_maill_arr[$user_id]){$mailArr[$user_id]=$user_maill_arr[$user_id];}
			?>
			<tr>
				<td><?=$i;?></td>
				<td><?= $lib_com_arr[$row['IMPORTER_ID']];?></td>
				<td><?=$row['ID'];?></td>
				<td><?=$item_category[$row['ITEM_CATEGORY_ID']];?></td>
				<td><?= change_date_format($row['PI_DATE']);?></td>
				<td><?=$row['PI_NUMBER']?></td>
				<td><?=number_format($row['NET_TOTAL_AMOUNT'],4);?></td>
				<td><?=$supplier_arr[$row['SUPPLIER_ID']];?></td>
			</tr>
			<?php } ?>
		</table>
		<?	
			
			$message=ob_get_contents();
			ob_clean();
			$sysId = str_replace(",","','",$sysId);
			$image_arr = return_library_array("select ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='proforma_invoice' and MASTER_TBLE_ID in('$sysId')",'ID','IMAGE_LOCATION');
			$att_file_arr=array();
			foreach($image_arr as $file){
				$att_file_arr[] = '../../'.$file.'**'.$file;
			}

			 //print_r($att_file_arr);
		

			$header=mailHeader();
			$to=implode(',',$mailToArr);
		
			 //echo $to;die;
			$subject = "Pi Approval";
			if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);
		
		

			exit();	
}



if($action=='deny_pi_approval_mail'){

		$sql="  SELECT a.IMPORTER_ID,A.ID,a.PI_DATE,a.ITEM_CATEGORY_ID,a.PI_NUMBER,a.SUPPLIER_ID,a.INSERTED_BY, c.NOT_APPROVAL_CAUSE FROM com_pi_master_details a,  com_pi_item_details b,  FABRIC_BOOKING_APPROVAL_CAUSE c WHERE a.id=b.pi_id  AND a.id = c.booking_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0  AND c.entry_form = 27  AND a.id IN ($sysId) GROUP BY A.ID,a.IMPORTER_ID,a.PI_DATE,a.ITEM_CATEGORY_ID,a.PI_NUMBER,a.SUPPLIER_ID,a.INSERTED_BY, c.NOT_APPROVAL_CAUSE";
		$sql_dtls=sql_select($sql);
		
			$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=77 and a.MAIL_TYPE in(2,0) and b.mail_user_setup_id=c.id and a.company_id={$sql_dtls[0][IMPORTER_ID]} AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
			$mail_sql=sql_select($sql);
			foreach($mail_sql as $row)
			{
				$mailArr[]=$rows[EMAIL_ADDRESS];
			}
		
		
		
		
		
		ob_start();	
		?>
		<b>Dear Concerned,</b>	<br />			
		Your approval request against the following reference is denied.							
		
		<table rules="all" border="1">
			<tr bgcolor="#CCCCCC">
				<td>SL</td>
				<td>Company</td>
				<td>System ID</td>
				<td>Item Category</td>
				<td>PI No</td>
				<td>PI Receive Date</td>
				<td>Supplier</td>
				<td>Deny cause</td>
			</tr>
			
			<?php 
			$i=1;
			foreach($sql_dtls as $row){ 
				if($user_maill_arr[$row[INSERTED_BY]]){$mailArr[$row[INSERTED_BY]]=$user_maill_arr[$row[INSERTED_BY]];}
				if($user_maill_arr[$user_id]){$mailArr[$user_id]=$user_maill_arr[$user_id];}
			?>
			<tr>
				<td><?=$i;?></td>
				<td><?= $lib_com_arr[$row['IMPORTER_ID']];?></td>
				<td><?=$row[ID];?></td>
				<td><?=$item_category[$row[ITEM_CATEGORY_ID]];?></td>
				<td><?=$row[PI_NUMBER]?></td>
				<td><?= change_date_format($row[PI_DATE]);?></td>
				<td><?=$supplier_arr[$row[SUPPLIER_ID]];?></td>
				<td><?=$row[NOT_APPROVAL_CAUSE];?></td>
			</tr>
			<?php } ?>
		</table>
		<?	
			
			$message=ob_get_contents();
			ob_clean();
			$sysId = str_replace(",","','",$sysId);
			$image_arr = return_library_array("select ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='proforma_invoice' and MASTER_TBLE_ID in('$sysId')",'ID','IMAGE_LOCATION');
			$att_file_arr=array();
			foreach($image_arr as $file){
				$att_file_arr[] = '../../'.$file.'**'.$file;
			}


			$to=implode(',',$mailArr);

	
			if($_REQUEST['isview']==1){
				$mail_item=77;
				if($to){
					echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
				}else{
					echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
				}
				echo $message;
			}
			else{
				//include('../../mailer/class.phpmailer.php');
				//include('../setting/mail_setting.php');
				$header=mailHeader();
				$subject = "Pi Approval Deny";
				if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);
			}


	
}



?> 