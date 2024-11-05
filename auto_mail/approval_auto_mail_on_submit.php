<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../includes/common.php');
require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

$company_library = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"  );
$buyer_library = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
$user_arr = return_library_array("select id,user_full_name from user_passwd where valid=1","id","user_full_name");
$user_id_arr = return_library_array("select id,user_name from user_passwd where valid=1","id","user_name");
$user_mail_arr = return_library_array("select user_id,email_address from user_mail_address where is_deleted=0","user_id","email_address");

if($db_type==0)
{
    $current_date = date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0)));
	if($_REQUEST['view_date']){
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
	}
	
	$previous_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
	$app_date_cond	=" and b.approved_date between '".$previous_date."' and '".$current_date."'";
	$un_date_cond	=" and b.un_approved_date between '".$previous_date."' and '".$current_date."'";
	
	
}
else
{
    $current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
	if($_REQUEST['view_date']){
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
	}
	$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
	$app_date_cond	=" and b.approved_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	$un_date_cond	=" and b.un_approved_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	 
}


$booking_no = str_replace("'","",$_REQUEST['booking']);
$bookingConReadyToApp="and a.ready_to_approved=1 and a.booking_no='$booking_no'";
$bookingConUnAppReq=" and a.booking_no='$booking_no'";



//Fabric booking Auto mail..............................................................................

foreach($company_library as $compid=>$compname)
{
	
	$menu_id = 838;//410
	//$approval_type=0;
	
	
	$sequence_wise_user_id_arr=array();
	$sql = "select user_id,buyer_id,bypass,sequence_no FROM electronic_approval_setup where company_id = $compid and page_id=$menu_id and is_deleted=0 order by sequence_no,user_id asc";
    $elecData_array=sql_select($sql);
	$i=0;
	foreach($elecData_array as $erow){
		 $sequence_wise_user_id_arr[$erow[csf(sequence_no)]] = $erow[csf(user_id)];
		 $sequence_wise_buyer_id_arr[$erow[csf(sequence_no)]] = $erow[csf(buyer_id)];
         $use_seque[$erow[csf(user_id)]] = $erow[csf(sequence_no)];
         $startSequence = min($use_seque);
         $endSequence = max($use_seque);
		 $i++;
	}

	$min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$compid and page_id=$menu_id and is_deleted=0","seq");
	
	$buyer_ids_array=array();
	$buyerData=sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$compid and page_id=$menu_id and is_deleted=0 and bypass=2");
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
	}
	
	$sequence_no='';
	$booking_year=date("Y",time());
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')='".trim($booking_year)."' ";
	


    
	
/*	$appSql="select a.booking_type,a.booking_no,a.job_no,b.current_approval_status,b.approved_by,b.un_approved_by,b.sequence_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$compid and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 $app_date_cond";*/
	
$appSql="select a.booking_type,a.booking_no,a.job_no,b.current_approval_status,b.approved_by,b.un_approved_by,b.sequence_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$compid and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 $bookingConReadyToApp";

        $htmlHeader='
        <table cellspacing="0" border="0" width="670">
            <tr>
                <td colspan="8" align="center">
                    <strong style="font-size:23px;">'.$company_library[$compid].'</strong>
                </td>
            </tr>';
        
        
		$html=$htmlHeader;
		$html.='
            <tr>
                <td colspan="8" align="center"><b>Fabric Booking approval remainder.</b></td>
            </tr>
        </table>
		
		<b>Dear Concern,</b><br>
		Approved remainder of following booking -
		<table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Booking No</th>
                    <th width="80">Job No</th>
                    <th width="150">Booking Type</th>
                    <th width="80">App. Type</th>
                    <th width="100">App. By</th>
                </tr>
            </thead>
            <tbody>'; 


	$fab_booking_sql = sql_select($appSql);
	$sl=1;
	foreach($fab_booking_sql as $row){
     	if($endSequence!=$row[csf(sequence_no)]){$appType="Partial App.";}else{$appType="Full App.";}
		
		$html.='
                <tr align="center">
                    <th width="35">'.$sl.'</th>
                    <th width="100"><p>'.$row[csf(booking_no)].'</p></th>
                    <th width="100"><p>'.$row[csf(job_no)].'</p></th>
                    <th width="150">'.$report_name[$row[csf(booking_type)]].'</th>
                    <th width="80">'.$appType.'</th>
                    <th width="100">'.$user_id_arr[$row[csf(approved_by)]].'</th>
                </tr>
            '; 
	$sl++;
	}
     	$html.='</table>'; 


/*	$unAppSql="select a.booking_type,a.booking_no,a.job_no,b.current_approval_status,b.approved_by,b.un_approved_by,b.sequence_no,b.comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$compid and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=0 $un_date_cond group by a.booking_type,a.booking_no,a.job_no,b.current_approval_status,b.approved_by,b.un_approved_by,b.sequence_no,b.comments";
*/	
	
	$unAppSql="select a.id,a.booking_type,a.booking_no,a.job_no,b.current_approval_status,b.approved_by,b.un_approved_by,b.sequence_no,b.comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$compid and a.status_active=1 and a.is_deleted=0  $bookingConUnAppReq group by a.id,a.booking_type,a.booking_no,a.job_no,b.current_approval_status,b.approved_by,b.un_approved_by,b.sequence_no,b.comments";
	//and b.current_approval_status=0
	
	$fab_booking_sql = sql_select($unAppSql);

		$html2=$htmlHeader;
        $html2.='
            <tr>
                <td colspan="8" align="center"><b>Fabric booking Un-Approval Notification.</b></td>
            </tr>
        </table>
       
        
		<b>Dear Concern,</b><br>
		Following booking has been Un-Approved -
		<table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Booking No</th>
                    <th width="80">Job No</th>
                    <th width="150">Booking Type</th>
                    <th width="120">Un-Approve Reason</th>
                    <th width="100">Un App. By</th>
                </tr>
            </thead>
            <tbody>'; 



	$sl=1;
	foreach($fab_booking_sql as $row){
     	if($endSequence!=$row[csf(sequence_no)]){$appType="Partial App.";}else{$appType="Full App.";}
		
		
		 echo $unapproved_request=return_field_value("approval_cause","fabric_booking_approval_cause","page_id=$menu_id and entry_form=7 and   booking_id=".$row[csf(id)]." and approval_type=2 and status_active=1 and is_deleted=0");
		
		
		
		$html2.='
                <tr align="center">
                    <th width="35">'.$sl.'</th>
                    <th width="100"><p>'.$row[csf(booking_no)].'</p></th>
                    <th width="100"><p>'.$row[csf(job_no)].'</p></th>
                    <th width="150">'.$report_name[$row[csf(booking_type)]].'</th>
                    <th width="80">'.$row[csf(comments)].'</th>
                    <th width="100">'.$user_id_arr[$row[csf(un_approved_by)]].'</th>
                </tr>
            '; 
	$sl++;
	}
     	$html2.='</table>'; 




		$to="";
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=2 and b.mail_user_setup_id=c.id and a.company_id=$compid and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 bAND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		
 		$subject = "Approved & Un-approved remainder";
    	$message=$html;
		$header=mailHeader();
		//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}

		if($_REQUEST['isview']==1){
			$mail_item=2;
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo $message;
		}
		else{
			if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);
		}




    foreach($sequence_wise_user_id_arr as $userSequence=>$user_id)
    {

		echo $html2;	
		echo "<br>";	
		
	}





die;
}



die;









?> 