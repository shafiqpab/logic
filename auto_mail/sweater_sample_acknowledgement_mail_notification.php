<?php
date_default_timezone_set("Asia/Dhaka");
// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require_once('setting/mail_setting.php');
extract($_REQUEST);
// var returnValue=return_global_ajax_value(reponse[2], 'sweater_sample_acknowledgement_mail_notification', '', '../../../auto_mail/sweater_sample_acknowledgement_mail_notification');
//echo load_html_head_contents("Mail Notification", "../", 1, 1,'','','');

//$action='sweater_sample_acknowledgement_mail_notification';	
	//$data=2120;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$season_arr=return_library_array("select id,season_name from  lib_buyer_season","id","season_name");
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$team_name_arr=return_library_array("select id,team_name from  lib_sample_production_team where product_category=6 and is_deleted=0","id","team_name");
	$team_email_arr=return_library_array("select id,email from  lib_sample_production_team where product_category=6 and is_deleted=0","id","email");
	
	$sample_name_arr=return_library_array( "select id,sample_name from  lib_sample where  status_active=1 and is_deleted=0",'id','sample_name');
    $dealing_merchant_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
    $dealing_merchant_mail_arr = return_library_array("select id, TEAM_MEMBER_EMAIL from lib_mkt_team_member_info", 'id', 'TEAM_MEMBER_EMAIL');

	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	$current_date = change_date_format(date("Y-m-d", $strtotime),'','',1);
	$previous_date = change_date_format(date('Y-m-d', strtotime('-1 day', strtotime($current_date))),'','',1);

 
	if($db_type==0){
		$date_cond	=" and c.insert_date between '".$previous_date."' and '".$current_date."'";
	}
	else
	{
		$date_cond	=" and c.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	}




//foreach($company_arr as $company_id=>$company_name){	
	
	
$sql = "select a.COMPANY_ID,a.REQUISITION_NUMBER,a.REQUISITION_DATE,a.STYLE_REF_NO,a.SEASON,a.DEALING_MARCHANT,a.TEAM_LEADER,a.BUYER_NAME ,
	LISTAGG(CAST(b.SAMPLE_NAME AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as SAMPLE_NAME,
	LISTAGG(CAST(b.DELV_END_DATE AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as DELV_END_DATE,
	sum(b.SAMPLE_PROD_QTY) as SAMPLE_PROD_QTY,
	max(c.CONFIRM_DEL_END_DATE) as CONFIRM_DEL_END_DATE, c.IS_DELETED
	 FROM sample_development_mst a,sample_development_dtls b,sample_requisition_acknowledge c WHERE a.id=b.sample_mst_id and a.id=c.SAMPLE_MST_ID and a.status_active = 1 AND a.is_deleted = 0 and b.entry_form_id=341  and c.id=$data
	group by a.COMPANY_ID,a.REQUISITION_NUMBER,a.REQUISITION_DATE,a.STYLE_REF_NO,a.SEASON,a.DEALING_MARCHANT,a.TEAM_LEADER,a.BUYER_NAME,c.IS_DELETED 
 ";  //$date_cond and a.IS_ACKNOWLEDGE=1
	$sql_result = sql_select($sql);
 	$company_id=$sql_result[0][COMPANY_ID];
	
	$width=1080;
	ob_start();	
	?>
	<div style="width:<? echo $width;?>px; margin-bottom:5px;" align="left">
		<fieldset>
            <table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="13" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[$company_id]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="13">Sample Acknowledgement</td>
                </tr>  
            </table>
            <table width="<? echo $width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr>
                        <th width="35">Sl</th>
                        <th width="100">Req. No</th>
                        <th width="60">Req. Date</th>
                        <th width="100">Style Ref</th>
                        <th width="50">Season</th>
                        <th width="100">Sample Name</th>
                        <th width="100">Dealing Merchandiser</th>
                        <th width="100">Buyer Name</th>
                        <th width="60">Delivery Date</th>
                        <th width="100">Team Name</th>
                        <th width="60">Req. Qty. (Pcs)</th>
                        <th width="60">Acknowledge Type</th>
                        <th>Confirmed Del. Date</th>
                    </tr>
                </thead>
	            <tbody>
	                <?
					$i= 1;
					foreach($sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$sample_name_temp_arr=array();
						foreach(explode(',',$row[SAMPLE_NAME]) as $sn){
							$sample_name_temp_arr[$sn]=$sample_name_arr[$sn];
						}
						
						$delivery_date_temp_arr=array();
						foreach(explode(',',$row[DELV_END_DATE]) as $deliveryDate){
							$delivery_date_temp_arr[$deliveryDate]=change_date_format($deliveryDate);
						}
						$sample_team_id=$row[TEAM_LEADER];
						
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td align="center"><? echo $i;?></td>
	                        <td align="center"><? echo $row[REQUISITION_NUMBER];?></td>
	                        <td><? echo change_date_format($row[REQUISITION_DATE]);?></td>
	                        <td><? echo $row[STYLE_REF_NO];?></td>
	                        <td><? echo $season_arr[$row[SEASON]];?></td>
	                        <td><?  echo implode(', ',$sample_name_temp_arr);?></td>
	                        <td><? echo $dealing_merchant_arr[$row[DEALING_MARCHANT]]; ?></td>
	                        <td><?  echo $buyer_arr[$row[BUYER_NAME]];?></td>
	                        <td><? echo implode(", ",$delivery_date_temp_arr);?></td>
	                        <td align="center"><? echo $team_name_arr[$row[TEAM_LEADER]];?></td>
	                        <td align="right"><? echo $row[SAMPLE_PROD_QTY];?></td>
	                        <td align="right"><? echo ($row[IS_DELETED]==1)?"Un-Acknowledged":"Acknowledged";?></td>
	                        <td align="center"><? echo change_date_format($row[CONFIRM_DEL_END_DATE]);?></td>
	                    </tr>
						<?
						
	                    $i++;
						if($dealing_merchant_mail_arr[$row[DEALING_MARCHANT]]){
							$to=$dealing_merchant_mail_arr[$row[DEALING_MARCHANT]];
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
	
	/*$to="";
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=2 and b.mail_user_setup_id=c.id and a.company_id=$company_id";
	$mail_sql2=sql_select($sql2);
	foreach($mail_sql2 as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}*/

	$subject="Sample Acknowledgement";
	//if($to!=""){echo sendMailMailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Email not found';}
	//echo $emailBody;
	if($_REQUEST['isview']==1){
		echo $emailBody;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Email not found';}
	}


	
exit();	



?>
