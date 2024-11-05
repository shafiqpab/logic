<?php
date_default_timezone_set("Asia/Dhaka");

include('../../includes/common.php');
//require_once('../../mailer/class.phpmailer.php');
include('../setting/mail_setting.php');
 
extract($_REQUEST);

	//localhost/platform-v3.5/auto_mail/woven/sample_requisition_acknowledge_auto_mail.php?req_id=1678&approval_type=1
	

 	
 	$sql_req2="SELECT distinct(a.id) as MST_ID, a.entry_form_id, a.COMPANY_ID, a.requisition_date, a.requisition_number_prefix_num, a.style_ref_no, a.BUYER_NAME, a.season, a.product_dept, a.DEALING_MARCHANT, a.agent_name, a.buyer_ref, a.bh_merchant, a.estimated_shipdate, a.remarks, a.status_active, a.is_deleted, a.refusing_cause, b.confirm_del_end_date from sample_development_mst a,sample_requisition_acknowledge b where a.entry_form_id in (117,203,449)  AND a.ID in($req_id) and a.is_approved in (0,1) and a.status_active=1 and a.is_deleted=0 and a.id=b.sample_mst_id order by   a.id desc"; //and a.COMPANY_ID=$cbo_company_name and a.is_acknowledge=1  and a.req_ready_to_approved=1
	//echo $sql_req2;
	
	$nameArray=sql_select($sql_req2);
	$dataArr=array();
	foreach($nameArray as $row){
		$dataArr[MST_ID][$row[MST_ID]]=$row[MST_ID];
		$dataArr[BUYER_ID][$row[BUYER_NAME]]=$row[BUYER_NAME];
		$dataArr[SEASON_ID][$row[SEASON]]=$row[SEASON];
		$dataArr[DEALING_MARCHANT_ID][$row[DEALING_MARCHANT]]=$row[DEALING_MARCHANT];
		$dataArr[COMPANY_ID][$row[COMPANY_ID]]=$row[COMPANY_ID];
	}
	
	
	
	$company_arr=return_library_array( "select id, company_short_name from lib_company WHERE id in(".implode(',',$dataArr[COMPANY_ID]).")",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer WHERE id in(".implode(',',$dataArr[BUYER_ID]).")", "id", "short_name");
	$dealing_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info  WHERE id in(".implode(',',$dataArr[DEALING_MARCHANT_ID]).") and status_active =1 and is_deleted=0",'id','team_member_name');
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season   WHERE id in(".implode(',',$dataArr[SEASON_ID]).") and  status_active =1 and is_deleted=0 ", "id", "season_name"  );
	
	
	
	 $sampQty=sql_select("select sample_mst_id,sum(sample_prod_qty) as sd from sample_development_dtls where  status_active=1 and is_deleted=0 and entry_form_id in (117,203,449) and SAMPLE_MST_ID in(".implode(',',$dataArr[MST_ID]).") group by sample_mst_id");
	foreach ($sampQty as $val)
	{
		$samplQtyArr[$val[csf('sample_mst_id')]]=$val[csf('sd')];
	}
	
	
	$reqSql="select sample_mst_id,sum(required_qty) as rq from sample_development_fabric_acc where status_active=1 and is_deleted=0 and form_type=1 and SAMPLE_MST_ID in(".implode(',',$dataArr[MST_ID]).") group by sample_mst_id";
	//echo $reqSql;die;
	$reqSqlResult=sql_select($reqSql);
	foreach ($reqSqlResult as $Reqval)
	{
		$reqQtyArr[$Reqval[csf('sample_mst_id')]]=$Reqval[csf('rq')];
	}

	$emb_sel=sql_select("select sample_mst_id,count(id) as ide from sample_development_fabric_acc where form_type=3 and status_active=1 and is_deleted=0 and SAMPLE_MST_ID in(".implode(',',$dataArr[MST_ID]).")  group by sample_mst_id");
	foreach($emb_sel as $embVal)
	{
		$embArr[$embVal[csf('sample_mst_id')]]=$embVal[csf('ide')];
	}	
	
	
	if($approval_type==1){
		$mail_item=42;
		$subject="Sample Requisition Acknowledge";
	}
	else
	{
		$mail_item=43;
		$subject="Sample Requisition Unacknowledge";
	}
	
	ob_start();
	?>
        <fieldset style="width:1050px; margin-top:10px">
        <legend><?= $subject;?></legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1140">
                <thead bgcolor="#CCCCCC">
                    <th width="40">SL</th>
                    <th width="80">Requisition No</th>
                    <th width="60">Booking No</th>
                    <th width="70">Date</th>
                    <th width="120">Company</th>
                    <th width="60">Dealing <br>Merchant</th>
                    <th width="90">Buyer</th>
                    <th width="60">Season</th>
                    <th width="70">Style Ref</th>
                    <th width="70">Sample Qty</th>
                    <th width="50">Fabric Qty</th>
                    <th width="80">Embellishment</th>
                    <th width="90">Confirm Del. End Date</th>
                    <th>Refusing Cause</th>
                </thead>
        
                    <tbody>
                        <? 
                            $i=1;
                            foreach ($nameArray as $row)
                            { 
                            	
								 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>"> 
									<td align="center"><?= $i; ?></td>
									<td align="center"><?= $row[csf('requisition_number_prefix_num')]; ?></td>
									<td align="center"><?= $booking_no; ?></td>
                                    <td align="center"><?= ($row[csf('requisition_date')]!="0000-00-00")?change_date_format($row[csf('requisition_date')]):''; ?></td>
									<td> <?= $company_arr[$row[csf('company_id')]]; ?></td>
									<td> <?= $dealing_arr[$row[csf('dealing_marchant')]]; ?></td>
									<td> <?= $buyer_arr[$row[csf('buyer_name')]]; ?></td>
									<td><p><?= $season_arr[$row[csf('season')]]; ?></p></td>
									<td><p><?= $row[csf('style_ref_no')]; ?></p></td>
									<td align="right"><p><?= $samplQtyArr[$row[MST_ID]]; ?></p></td>
									<td align="right"><p><?=  $reqQtyArr[$row[MST_ID]];?></p></td>
									<td align="center"><?= ($embArr[$row[MST_ID]]>0)?"YES":"NO"; ?></td>
									<td align="center"><?= change_date_format($row[CONFIRM_DEL_END_DATE]); ?></td>
									<td><? echo $row[csf('refusing_cause')];?></td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>

				
			</table>
        </fieldset>
	<?
	$message=ob_get_contents();
	ob_clean();
	$to='';
	$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=$mail_item and b.mail_user_setup_id=c.id and a.company_id in(".implode(',',$dataArr[COMPANY_ID]).") and   A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	 //echo $sql;die;
	
	$mail_sql=sql_select($sql);
	$receverMailArr=array();
	foreach($mail_sql as $row)
	{
		$buyerArr=explode(',',$row[BUYER_IDS]);
		$brandArr=explode(',',$row[BRAND_IDS]);

		if(count($buyerArr)){
			foreach($buyerArr as $buyerid){
				foreach($brandArr as $brandid){
					$receverMailArr[$buyerid][$brandid][$row[csf('email_address')]]=$row[csf('email_address')];
				}
			}
		}
		else{
			$receverMailArr[end($dataArr['BUYER_ID'])][$brandid][$row[csf('email_address')]]=$row[csf('email_address')];
		}
		
	}

	$to=implode(',',$receverMailArr[end($dataArr['BUYER_ID'])][$brand_id]);

	$header=mailHeader();
	
	if($_REQUEST['isview']==1){
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{

		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );}		
	}



	exit();	



?>