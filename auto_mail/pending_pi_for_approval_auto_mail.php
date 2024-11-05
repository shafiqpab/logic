<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Auto Mail</title>
</head>
<body>
<?php
date_default_timezone_set("Asia/Dhaka");
//require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require('setting/mail_setting.php');
extract($_REQUEST);
	
// var returnValue=return_global_ajax_value(reponse[2], 'price_quotation_mail_notification', '', '../../../auto_mail/mail_notification');

echo load_html_head_contents("Mail Notification", "../", 1, 1,'','','');


$action='pending_pi_for_approval_auto_mail';	
	
if($action=='pending_pi_for_approval_auto_mail'){
	
	//$data=2120;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$team_member_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0","id","team_member_name");
	$item_group_arr=return_library_array( "SELECT id,item_name FROM lib_item_group",'id','item_name');
	$supplier_name_arr=return_library_array( "SELECT id,supplier_name FROM lib_supplier",'id','supplier_name');
    $dealing_merchant_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');


	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	$current_date = change_date_format(date("Y-m-d", $strtotime),'','',1);
	$previous_date = change_date_format(date('Y-m-d', strtotime('-30 day', strtotime($current_date))),'','',1);
 
 
	if ($db_type == 0){ $year=" YEAR(a.insert_date)";}
	else if ($db_type == 2){$year=" to_char(a.insert_date,'YYYY')";}



foreach($company_arr as $company_id=>$company_name){	
	
	
	$sql = "select a.ID,a.PI_NUMBER,a.SUPPLIER_ID,a.ITEM_CATEGORY_ID,a.SOURCE,a.PI_DATE,a.INTERNAL_FILE_NO,a.READY_TO_APPROVED,$year as YEAR,sum(b.AMOUNT) as AMOUNT FROM com_pi_master_details a,com_pi_item_details b WHERE a.id=b.pi_id and a.status_active = 1 AND a.is_deleted = 0 and a.IMPORTER_ID=$company_id and a.PI_DATE between '".$previous_date."' and '".$current_date."'   and a.APPROVED=0
 group by a.ID,a.PI_NUMBER,a.SUPPLIER_ID,a.ITEM_CATEGORY_ID,a.SOURCE,a.PI_DATE,a.INTERNAL_FILE_NO,a.READY_TO_APPROVED,a.insert_date";
	
	$sql_result = sql_select($sql);
	$piDataArr=array();
	$pi_id_arr=array();
	foreach ($sql_result as $row)
	{	
		$piDataArr[]=$row;
		$pi_id_arr[$row[ID]]=$row[ID];
	}




	if($db_type==2 && count($pi_id_arr)>999)
	{
		$pi_id_arr_chunk=array_chunk($pi_id_arr,999) ;
		foreach($pi_id_arr_chunk as $chunk_arr)
		{
			$piCond.=" a.pi_id in(".implode(",",$chunk_arr).") or ";
		}

		$piCond.=" and (".chop($fsoCond,'or ').")";
	}
	else
	{
		$piCond=" and a.pi_id in(".implode(",",$pi_id_arr).")";
	}	

 //echo $piCond;die;


$sql_buyer_marchent = "select a.pi_id,c.buyer_name,c.dealing_marchant,c.job_no, 1 as type
    from com_pi_item_details a, wo_non_order_info_dtls b, wo_po_details_master c, wo_non_order_info_mst d
    where a.work_order_dtls_id = b.id and b.job_no = c.job_no and b.mst_id = d.id and a.work_order_id=d.id and  a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and c.is_deleted = 0 
    and c.status_active = 1 and a.item_category_id = 1 and b.item_category_id = 1 $piCond 
    group by a.pi_id, c.buyer_name, c.dealing_marchant, c.job_no 
    union all 
    select a.pi_id, b.buyer_id as buyer_name, d.dealing_marchant, b.job_no, 2 as type
    from com_pi_item_details a, wo_non_order_info_dtls b, wo_non_order_info_mst d
    where a.work_order_dtls_id = b.id and b.mst_id = d.id  and a.work_order_id=d.id  and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 $piCond 
    and a.item_category_id = 1 and b.item_category_id = 1 and d.wo_basis_id = 3 and d.entry_form = 284 
    group by a.pi_id, b.buyer_id, d.dealing_marchant, b.job_no 
	union all 
	SELECT  a.pi_id,c.buyer_name,c.dealing_marchant,c.job_no, 1 as type  from com_pi_item_details a, wo_booking_dtls b, wo_po_details_master c where a.work_order_no=b.booking_no and b.JOB_NO=c.JOB_NO and a.item_category_id in(2,3,13,14) and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $piCond group by a.pi_id, c.buyer_name, c.dealing_marchant, c.job_no
    union all 
    select a.pi_id, c.buyer_name, c.dealing_marchant, c.job_no, 3 as type
    from com_pi_item_details a, wo_booking_dtls b, wo_po_details_master c 
    where a.work_order_dtls_id = b.id and b.job_no = c.job_no and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and c.is_deleted = 0 and c.status_active = 1 $piCond 
    and a.item_category_id = 4 group by a.pi_id, c.buyer_name, c.dealing_marchant, c.job_no";
   // echo $sql_buyer_marchent;
   
    $sql_job=sql_select($sql_buyer_marchent);

    $buyer_marchant_arr=array();
    $marchant_data_arr=array();
    foreach($sql_job as $row)
    {
         $buyer_data_arr[$row[csf("pi_id")]][$row[csf("buyer_name")]]=$buyer_arr[$row[csf("buyer_name")]];
         $marchant_data_arr[$row[csf("pi_id")]][$row[csf("dealing_marchant")]]=$dealing_merchant_arr[$row[csf("dealing_marchant")]];
    }


	$width=1000;
	ob_start();	
	?>
	<div style="width:<? echo $width;?>px; margin-bottom:5px;" align="left">
		<fieldset>
            <table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="13" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[$company_id]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="13">Pending PI for Approval</td>
                </tr>  
            </table>
            <table width="<? echo $width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr>
                        <th width="35">Sl</th>
                        <th width="50">System Id of PI</th>
                        <th width="100">PI No</th>
                        <th width="100">Supplier</th>
                        <th width="100">Dealing Merchandiser</th>
                        <th width="100">Buyer Name</th>
                        <th width="100">File no</th>
                        <th width="70">PI Date</th>
                        <th width="100">Item Category</th>
                        <th width="100">Amount</th>
                        <th width="60">Source</th>
                        <th width="30">Year</th>
                        <th>Ready To Approve</th>
                    </tr>
                </thead>
	            <tbody>
	                <?
					$i= 1;
					foreach($piDataArr as $pi_id=>$row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td align="center"><? echo $i;?></td>
	                        <td align="center"><? echo $row[ID];?></td>
	                        <td><? echo $row[PI_NUMBER];?></td>
	                        <td><? echo $supplier_name_arr[$row[SUPPLIER_ID]];?></td>
	                        <td><? echo implode(',',$marchant_data_arr[$row[ID]]);?></td>
	                        <td><? echo implode(',',$buyer_data_arr[$row[ID]]);?></td>
	                        <td><? echo $row[INTERNAL_FILE_NO]; ?></td>
	                        <td align="center"><? echo change_date_format($row[PI_DATE]);?></td>
	                        <td><? echo $item_category[$row[ITEM_CATEGORY_ID]];?></td>
	                        <td align="right"><? echo number_format($row[AMOUNT],2);?></td>
	                        <td align="center"><? echo $source[$row[SOURCE]];?></td>
	                        <td align="center"><? echo $row[YEAR];?></td>
	                        <td align="center"><? echo $yes_no[$row[READY_TO_APPROVED]];?></td>
	                    </tr>
						<?
						
	                    $i++;
	                }
	                ?>
	                
	                </tbody>
	            </table>
             
        </fieldset>
        
    </div>
	<?
	$emailBody=ob_get_contents();
	ob_clean();

	
	
	
	
	
	//$company_id=$pqsForReadyToApprove[0]['COMPANY_ID'];
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=28 and b.mail_user_setup_id=c.id AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and a.company_id =".$company_id."";	
	$mail_sql2=sql_select($sql2);
	$to="";
	foreach($mail_sql2 as $row)
	{
		if ($to=="") {
			$to=$row[csf('email_address')];
		}  else{
			 $to=$to.",".$row[csf('email_address')];
		}

	}

	$subject="Pending PI for Approval";


	if($_REQUEST['isview']==1){
		$mail_item=28;
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $emailBody;
	}
	else{
		$header=mailHeader();
		if($to!=""){echo sendMailMailer( $to, $subject, $emailBody,'' );}
	}




	//echo $emailBody;
}
	
exit();	

}

?>




</body>
</html>
