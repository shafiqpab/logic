<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Auto Mail</title>
</head>
<body>
<?php
session_start();
$_SESSION['logic_erp']['user_auto_id']=9999999;



date_default_timezone_set("Asia/Dhaka");
// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
extract($_REQUEST);
	
// var returnValue=return_global_ajax_value(reponse[2], 'price_quotation_mail_notification', '', '../../../auto_mail/mail_notification');

echo load_html_head_contents("Mail Notification", "../", 1, 1,'','','');


$action='pending_pi_for_approval_auto_mail';	
	
if($action=='pending_pi_for_approval_auto_mail'){
	
	//$data=2120;
	$company_arr=return_library_array( "select id, company_name from lib_company where 3=3",'id','company_name');
	
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
    $dealing_merchant_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');

	
	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	$current_date = change_date_format(date("Y-m-d", $strtotime),'','',1);
	$previous_date = change_date_format(date('Y-m-d', strtotime('-12 day', strtotime($current_date))),'','',1);

	$previous_date="15-Dec-2019";

	$date_diff="(to_date('".date('d-M-y', $strtotime)."', 'dd-MM-yy')- to_date(a.BOOKING_DATE, 'dd-MM-yy'))";
	$year=" min(to_char(a.BOOKING_DATE,'YYYY'))";
 
 
foreach($company_arr as $company_id=>$company_name){	

$sql = "select min(e.RECEIVE_DATE) as RECEIVE_DATE,b.STYLE_ID, a.BOOKING_NO,a.BUYER_ID,min(a.BOOKING_DATE) as BOOKING_DATE,g.REMARKS_RA ,c.INTERNAL_REF,c.DEALING_MARCHANT,min(a.DELIVERY_DATE) as DELIVERY_DATE ,c.REQUISITION_NUMBER,c.STYLE_REF_NO,$year as YEAR
from WO_NON_ORD_SAMP_BOOKING_MST a,
wo_non_ord_samp_booking_dtls b,
sample_development_mst c,            
inv_receive_master e,
				PRO_ROLL_DETAILS f,
sample_development_fabric_acc g
where e.id=f.mst_id and f.BOOKING_NO=a.BOOKING_NO and e.ENTRY_FORM in(58,110,180) and f.ENTRY_FORM=58 and c.ID=g.SAMPLE_MST_ID and   g.form_type=1  and
a.booking_no=b.booking_no and a.BOOKING_NO=d.BOOKING_NO and c.id=b.style_id and a.entry_form_id=140 and b.entry_form_id=140 and a.is_deleted=0   and a.COMPANY_ID=$company_id  and $date_diff > 8 and  a.BOOKING_DATE > '$previous_date'

and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and  b.IS_DELETED=0 and b.STATUS_ACTIVE=1
and c.IS_DELETED=0 and c.STATUS_ACTIVE=1
and e.IS_DELETED=0 and e.STATUS_ACTIVE=1 and f.IS_DELETED=0 and f.STATUS_ACTIVE=1  
group by  b.STYLE_ID, a.BOOKING_NO,a.BUYER_ID, g.REMARKS_RA ,c.INTERNAL_REF,c.DEALING_MARCHANT  ,c.REQUISITION_NUMBER,c.STYLE_REF_NO
order by a.BOOKING_NO
";

 //echo $sql;die;

/*$sql = "select e.RECEIVE_DATE,b.STYLE_ID,$year as YEAR,a.BOOKING_NO,a.BUYER_ID,a.BOOKING_DATE,g.REMARKS_RA ,c.INTERNAL_REF,c.DEALING_MARCHANT,a.DELIVERY_DATE ,c.REQUISITION_NUMBER,c.STYLE_REF_NO,d.ID,d.BATCH_DATE
from WO_NON_ORD_SAMP_BOOKING_MST a,wo_non_ord_samp_booking_dtls b,sample_development_mst c,PRO_BATCH_CREATE_MST d, inv_receive_master e,PRO_ROLL_DETAILS f,sample_development_fabric_acc g
where e.id=f.mst_id and f.BOOKING_NO=a.BOOKING_NO and e.ENTRY_FORM in(58,110,180) and f.ENTRY_FORM=58 and c.ID=g.SAMPLE_MST_ID and   g.form_type=1 and a.COMPANY_ID=$company_id and
a.booking_no=b.booking_no and a.BOOKING_NO=d.BOOKING_NO and c.id=b.style_id and a.entry_form_id=140 and b.entry_form_id=140 and a.is_deleted=0 and a.status_active=1 and $date_diff > 8 and  a.BOOKING_DATE > '$previous_date'

and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and  b.IS_DELETED=0 and b.STATUS_ACTIVE=1
and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and d.IS_DELETED=0 and d.STATUS_ACTIVE=1
and e.IS_DELETED=0 and e.STATUS_ACTIVE=1 and f.IS_DELETED=0 and f.STATUS_ACTIVE=1  

order by a.id,d.id desc
   "; */
   
   
   
   //and a.BOOKING_NO = 'MF-SMN-19-00041'
  //echo $sql;
$con = connect();
execute_query("delete from tmp_poid where userid=".$_SESSION['logic_erp']['user_auto_id']."");
 
$sql_result=sql_select($sql);
$dataArr=array();
$tempArr=array();
$batchTempArr=array();
foreach($sql_result as $row){
	$dataArr[$row[BOOKING_NO]]=$row;
	
	if($row[REMARKS_RA]){$remarks[$row[BOOKING_NO]][$row[REMARKS_RA]]=$row[REMARKS_RA];}
	if($row[BOOKING_NO] && $tempArr[$row[BOOKING_NO]]==''){
		$r_id2=execute_query("insert into tmp_poid (userid, pono,type) values (".$_SESSION['logic_erp']['user_auto_id'].",'".$row[BOOKING_NO]."',2".")");
		$tempArr[$row[BOOKING_NO]]=1;
	}
	
}

unset($sql_result);


$sqlBatch="select d.ID as BATCH_ID,d.BOOKING_NO,min(d.BATCH_DATE) as BATCH_DATE from PRO_BATCH_CREATE_MST d,tmp_poid tmp where  tmp.pono=d.booking_no and tmp.type=2 and tmp.userid=".$_SESSION['logic_erp']['user_auto_id']."  and d.IS_DELETED=0 and d.STATUS_ACTIVE=1 group by d.ID,d.BOOKING_NO order by d.id desc";
$sqlBatchResult=sql_select($sqlBatch);
$batchDataArr=array();
foreach($sqlBatchResult as $row){
	$batchDataArr[$row[BOOKING_NO]]=$row[BATCH_DATE];
	$bookingBatchDataArr[$row[BOOKING_NO]][$row[BATCH_ID]]=$row[BATCH_ID];
	
	if($row[BOOKING_NO] && $batchTempArr[$row[BATCH_ID]]==''){
		$r_id1=execute_query("insert into tmp_poid (userid, poid,type) values (".$_SESSION['logic_erp']['user_auto_id'].",".$row[BATCH_ID].",1".")");
		$batchTempArr[$row[BATCH_ID]]=1;
	}
}
unset($sqlBatchResult);
oci_commit($con);


$sqlFBProduction="select b.BATCH_ID,c.DELEVERY_DATE from PRO_GREY_PROD_DELIVERY_DTLS b,PRO_GREY_PROD_DELIVERY_MST c,tmp_poid tmp where tmp.poid=b.BATCH_ID and tmp.type=1 and c.COMPANY_ID=$company_id and tmp.userid=".$_SESSION['logic_erp']['user_auto_id']." and   b.mst_id=c.id and c.ENTRY_FORM=54 order by c.id desc";
$sqlFBProductionResult=sql_select($sqlFBProduction);
$fbDataArr=array();
foreach($sqlFBProductionResult as $row){
	$fbDataArr[$row[BATCH_ID]]=$row[DELEVERY_DATE];
}
unset($sqlFBProductionResult);

//print_r($fbDataArr); die;





$sqlIssue="select a.ISSUE_DATE,a.BOOKING_NO from inv_issue_master a,tmp_poid tmp where tmp.pono=a.booking_no and tmp.type=2 and tmp.userid=".$_SESSION['logic_erp']['user_auto_id']."  order by a.id desc";
$sqlIssueResult=sql_select($sqlIssue);
$issueDataArr=array();
foreach($sqlIssueResult as $row){
	$issueDataArr[$row[BOOKING_NO]]=$row[ISSUE_DATE];
}
unset($sqlIssueResult);





	
	$width=1310;
	ob_start();	
	?>
	<div style="width:<? echo $width;?>px; margin-bottom:5px;" align="left">
		<fieldset>
            <table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="15" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[$company_id]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="15">Sample Finish Fabric Delivery Dead Line Pending</td>
                </tr>  
            </table>
            <table width="<? echo $width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="50">Booking Year</th>
                        <th width="100">Booking No</th>
                        <th width="80">Requisition</th>
                        <th width="100">Buyer Name</th>
                        <th width="100">Style Ref.</th>
                        <th width="100">Internal Ref.</th>
                        <th width="70">W/O Booking Date</th>
                        <th width="70">Yarn Delivey Date</th>
                        <th width="70">Grey Rcv Date</th>
                        <th width="70">Batch Date</th>
                        <th width="70">Delay as of Today</th>
                        <th width="70">Finished Fabric Delivery To Store</th>
                        <th width="100">Deling Merchant</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
	            <tbody>
	                <?
					
					
					
					$i= 1;
					foreach($dataArr as $row)
					{
						$flag=0;
						foreach($bookingBatchDataArr[$row[BOOKING_NO]] as $id){
							if ($fbDataArr[$id]!=''){$flag=1;$row[ID]=$id;}
							
						}
						
						
						
						//$targetDate=date('Y-m-d', strtotime('8 day', strtotime($row[BOOKING_DATE])));
						
						
						//$dateTimestamp1 = strtotime($fbDataArr[$row[ID]]); 
						//$dateTimestamp2 = strtotime($targetDate); 
						//if ($dateTimestamp1 > $dateTimestamp2 || $fbDataArr[$row[ID]]==''){ 
						if ($flag==0){ 
						
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td align="center"><? echo $i;?></td>
	                        <td align="center"><? echo $row[YEAR];?></td>
	                        <td align="center"><? echo $row[BOOKING_NO];?></td>
	                        <td align="center"><? echo $row[REQUISITION_NUMBER];?></td>
	                        <td><? echo $buyer_arr[$row[BUYER_ID]];?></td>
	                        <td><? echo $row[STYLE_REF_NO];?></td>
	                        <td><? echo $row[INTERNAL_REF]; ?></td>
	                        <td align="center"><? echo change_date_format($row[BOOKING_DATE]);?></td>
	                        <td align="center">
							<? 
								//echo change_date_format($row[DELIVERY_DATE]);
								//echo "<br>".datediff('d',$row[BOOKING_DATE],$row[DELIVERY_DATE])." Days";
								
								if($issueDataArr[$row[BOOKING_NO]]){
									echo change_date_format($issueDataArr[$row[BOOKING_NO]]);
									echo "<br>".datediff('d',$row[BOOKING_DATE],$issueDataArr[$row[BOOKING_NO]])." Days";
								}
							?>
                            </td>
	                        <td align="center">
							<? 
								echo change_date_format($row[RECEIVE_DATE]); 
								echo "<br>".datediff('d',$row[BOOKING_DATE],$row[RECEIVE_DATE])." Days";
							?>
                            </td>
	                        <td align="center">
							<? 
								echo change_date_format($row[BATCH_DATE]);
								echo "<br>".datediff('d',$row[BOOKING_DATE],$row[BATCH_DATE])." Days";
							?>
                            </td>
                            
                            <td align="center"><? echo datediff('d',$row[BOOKING_DATE],date('d-M-y',time()))." Days"; ?></td>
                            
                            
	                        <td align="center" title="Batch id:<? echo $row[ID];?>">
							<? 
								if($fbDataArr[$row[ID]]){
								echo change_date_format($fbDataArr[$row[ID]]);
								echo "<br>".datediff('d',$row[BOOKING_DATE],$fbDataArr[$row[ID]])." Days";
								}
							?>
                            </td>
	                        <td><? echo $dealing_merchant_arr[$row[DEALING_MARCHANT]];?></td>
	                        <td align="center"><? echo implode(', ',$remarks[$row[BOOKING_NO]]);?></td>
	                    </tr>
						<?
						
	                    $i++;
						}
	                }
	                ?>
	                
	                </tbody>
	            </table>
             
        </fieldset>
        
    </div>
	<?
	$emailBody=ob_get_contents();
	ob_clean();

	

	
	$to="";
	//$to='zakaria.joy@logicsoftbd.com';
	//$company_id=$pqsForReadyToApprove[0]['COMPANY_ID'];
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=30 AND a.MAIL_TYPE=1 and b.mail_user_setup_id=c.id and a.company_id =".$company_id."";
	
	$mail_sql2=sql_select($sql2);
	foreach($mail_sql2 as $row)
	{
		if ($to=="") {
			$to=$row[csf('email_address')];
		}  else{
			 $to=$to.",".$row[csf('email_address')];
		}

	}
	//$to= 'it@mfgbd.net';
	$subject="Sample Finish Fabric Delivery Dead Line Pending";
	//if($to!=""){echo send_mail_mailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Not Send';}
	if($_REQUEST['isview']==1){
		echo $emailBody;
	}
	else{
	    if($to!=""){echo send_mail_mailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Not Send';}
}

	
	//echo $company_arr[$company_id].'='.$to."<br>";
}


execute_query("delete from tmp_poid where userid=".$_SESSION['logic_erp']['user_auto_id']."");

exit();	

}

?>




</body>
</html>
