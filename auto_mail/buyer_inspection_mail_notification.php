<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Auto Mail</title>
</head>
<body>
<?php
date_default_timezone_set("Asia/Dhaka");
// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require_once('setting/mail_setting.php');
extract($_REQUEST);
	
// var returnValue=return_global_ajax_value(reponse[2], 'price_quotation_mail_notification', '', '../../../auto_mail/mail_notification');

echo load_html_head_contents("Mail Notification", "../", 1, 1,'','','');


$action='buyer_inspection_mail_notification';	
	
if($action=='buyer_inspection_mail_notification'){
	
	//$data=2120;
	$company_full_arr=return_library_array( "select id, company_name from lib_company where 6=6 AND is_deleted = 0 and status_active = 1",'id','company_name');
	$company_arr=return_library_array( "select id, COMPANY_SHORT_NAME from lib_company where 6=6 AND is_deleted = 0 and status_active = 1",'id','COMPANY_SHORT_NAME');
	
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$supplier_name_arr=return_library_array( "SELECT id,supplier_name FROM lib_supplier",'id','supplier_name');
	$country_arr=return_library_array( "SELECT ID,COUNTRY_NAME FROM LIB_COUNTRY",'id','COUNTRY_NAME');
	$floor_arr=return_library_array( "SELECT ID,FLOOR_NAME FROM LIB_PROD_FLOOR",'id','FLOOR_NAME');
	$inpLevelArray = array(1=>'In-line Inspection',2=>'Mid-line Inspection',3=>'Final Inspection');

	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	$current_date = change_date_format(date("Y-m-d", $strtotime),'','',1);
	$previous_date = change_date_format(date('Y-m-d', strtotime('-1 day', strtotime($current_date))),'','',1);

 
	
	$sql = "select a.WORKING_COMPANY,a.PO_BREAK_DOWN_ID,a.INSPECTION_COMPANY,a.WORKING_FLOOR,a.INSPECTION_DATE,a.INSPECTION_STATUS,a.INSPECTION_LEVEL,a.INSPECTION_CAUSE,a.INS_REASON,a.INSPECTED_BY,a.COUNTRY_ID,b.PO_NUMBER,c.COMPANY_NAME,c.BUYER_NAME,c.STYLE_REF_NO FROM PRO_BUYER_INSPECTION a,WO_PO_BREAK_DOWN b,WO_PO_DETAILS_MASTER c WHERE a.PO_BREAK_DOWN_ID=b.ID AND c.job_no=b.job_no_mst and a.JOB_NO=c.JOB_NO and a.status_active = 1 AND a.is_deleted = 0 and b.status_active = 1 AND b.is_deleted = 0 
	and a.INSPECTION_DATE between'$previous_date' and '$previous_date'	
	and a.INSPECTION_LEVEL=3

	order by a.WORKING_COMPANY,a.WORKING_FLOOR,a.id ASC
	";//and a.JOB_NO='UG-19-00008' and a.INSPECTION_STATUS != 1
	//echo $sql;die;  by a.id ASC
	
	$sql_result = sql_select($sql);
	$inspactionDataArr=array();
	foreach ($sql_result as $row)
	{	
		if($row[INSPECTION_STATUS]==1){$is=1;}else{$is=2;}
		$key=$row[WORKING_COMPANY].$row[WORKING_FLOOR].$row[PO_BREAK_DOWN_ID].$row[BUYER_NAME].$row[STYLE_REF_NO].$row[COUNTRY_ID];
		$inspactionDataArr[$is][$key]=$row;
		
		$groupByDataArr[INSPECTION_STATUS][$is][$key]=$inspection_status[$row[INSPECTION_STATUS]];
		if($row[INS_REASON]){$groupByDataArr[INS_REASON][$is][$key][]=$row[INS_REASON];}
		if($row[INSPECTION_CAUSE]){$groupByDataArr[INSPECTION_CAUSE][$is][$key]=$inspection_cause[$row[INSPECTION_CAUSE]];}
	}

	foreach($inspactionDataArr[1] as $key=>$dataRow){
		unset($inspactionDataArr[2][$key]);
	}
	

	$width=1200;
	
	ob_start();	
	?>
	<div style="width:<? echo $width;?>px; margin-bottom:5px;" align="left">
		<fieldset>
            <table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="13" class="form_caption" ><strong style="font-size:18px"><? echo $company_full_arr[$company_id]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="13"><h2>Buyer Inspection on <? echo change_date_format($previous_date);?></h2></td>
                </tr>  
            </table>
            <table width="<? echo $width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr>
                        <th width="35">Sl</th>
                        <th width="60">LC Company</th>
                        <th width="60">Prod Company</th>
                        <th width="100">Unit</th>
                        <th width="100">Buyer</th>
                        <th width="100">Style</th>
                        <th width="100">PO</th>
                        <th width="100">Country</th>
                        <th width="100">Insp. By</th>
                        <th width="100">Insp. Company</th>
                        <th width="70">Insp. Level</th>
                        <th width="100">Insp. Status</th>
                        <th width="60">Cause</th>
                        <th>Reason</th>
                    </tr>
                </thead>
	            <tbody>
	                <?
                    if(count($inspactionDataArr[2])){
						echo '<tr bgcolor="#FFFFCC"><th colspan="14">Re-Check & Failed </th></tr>';
					}
					
					$i= 1;
					foreach($inspactionDataArr[2] as $key=>$row)
					{
						$insCompany=array();
						if($row[INSPECTED_BY]==1){$insCompany=$buyer_arr;}
						elseif($row[INSPECTED_BY]==2){$insCompany=$supplier_name_arr;}
						else{$insCompany=$company_arr;}
						
						
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                        <td align="center"><? echo $i;?></td>
	                        <td align="center"><? echo $company_arr[$row[COMPANY_NAME]];?></td>
	                        <td><? echo $company_arr[$row[WORKING_COMPANY]];?></td>
	                        <td><? echo $floor_arr[$row[WORKING_FLOOR]];?></td>
	                        <td><? echo $buyer_arr[$row[BUYER_NAME]];?></td>
	                        <td><? echo $row[STYLE_REF_NO];?></td>
	                        <td><? echo $row[PO_NUMBER]; ?></td>
	                        <td align="center"><? echo $country_arr[$row[COUNTRY_ID]];?></td>
	                        <td align="center"><? echo $inspected_by_arr[$row[INSPECTED_BY]];?></td>
	                        <td align="center"><? echo $insCompany[$row[INSPECTION_COMPANY]];?></td>
	                        <td align="center"><? echo $inpLevelArray[$row[INSPECTION_LEVEL]];?></td>
	                        <td align="center"><? echo $groupByDataArr[INSPECTION_STATUS][2][$key];?></td>
	                        <td align="center"><? echo  $groupByDataArr[INSPECTION_CAUSE][2][$key];?></td>
	                        <td><? echo implode(',',$groupByDataArr[INS_REASON][2][$key]);?></td>
	                    </tr>
						<?
						
	                    $i++;
	                }
					
                    if(count($inspactionDataArr[1])){
						echo '<tr bgcolor="#99FF99"><th colspan="14">Passed </th></tr>';
					}
					$i= 1;
					foreach($inspactionDataArr[1] as $key=>$row)
					{
						$insCompany=array();
						if($row[INSPECTED_BY]==1){$insCompany=$buyer_arr;}
						elseif($row[INSPECTED_BY]==2){$insCompany=$supplier_name_arr;}
						else{$insCompany=$company_arr;}
						
						
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td align="center"><? echo $i;?></td>
	                        <td align="center"><? echo $company_arr[$row[COMPANY_NAME]];?></td>
	                        <td><? echo $company_arr[$row[WORKING_COMPANY]];?></td>
	                        <td><? echo $floor_arr[$row[WORKING_FLOOR]];?></td>
	                        <td><? echo $buyer_arr[$row[BUYER_NAME]];?></td>
	                        <td><? echo $row[STYLE_REF_NO];?></td>
	                        <td><? echo $row[PO_NUMBER]; ?></td>
	                        <td align="center"><? echo $country_arr[$row[COUNTRY_ID]];?></td>
	                        <td align="center"><? echo $inspected_by_arr[$row[INSPECTED_BY]];?></td>
	                        <td align="center"><? echo $insCompany[$row[INSPECTION_COMPANY]];?></td>
	                        <td align="center"><? echo $inpLevelArray[$row[INSPECTION_LEVEL]];?></td>
	                        <td align="center"><? echo $groupByDataArr[INSPECTION_STATUS][1][$key];?></td>
	                        <td align="center"><? echo  $groupByDataArr[INSPECTION_CAUSE][1][$key];?></td>
	                        <td><? echo implode(',',$groupByDataArr[INS_REASON][1][$key]);?></td>
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

	
	
	$companyStr = implode(',',array_keys($company_arr));
	
	$to='';
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=60 and b.mail_user_setup_id=c.id and a.company_id in($companyStr) AND a.MAIL_TYPE=1";
	$mail_sql2=sql_select($sql2, '', '', '', $con);
	foreach($mail_sql2 as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}


	
	
	
	$to="asif.ashraf@urmigroup.net,mirashraful@urmigroup.net,shamarukh.fakhruddin@urmigroup.net,neelaka@urmigroup.net,deepal@urmigroup.net,shamsu@urmigroup.net,erpsupport@urmigroup.net,sayeem@logicsoftbd.com";
	$subject="Buyer Inspection Status";
	$header=mailHeader();
	//if($to!="" && $falg==1){echo sendMailMailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Not Send';}
	//echo $emailBody;
	if($_REQUEST['isview']==1){
		echo $emailBody;
	}
	else{
		
		if($to!="" && $falg==1){echo sendMailMailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Not Send';}

  

	}

	
exit();	

}

?>





</body>
</html>
<!-- to is off -->
