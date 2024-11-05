<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../../includes/common.php');

extract($_REQUEST);


	$sql="SELECT 
 	TO_CHAR((SYSDATE), 'DD-MM-YYYY') AS CURRENT_DATE , TO_CHAR (UN_APPROVED_DATE, 'DD-MM-YYYY') as UN_APPROVED_DATE,
	 FULL_APPROVED,MST_ID from APPROVAL_HISTORY where MST_ID IN(select MST_ID from APPROVAL_HISTORY where ENTRY_form=15 and CURRENT_APPROVAL_STATUS=0 and UN_APPROVED_DATE is not null AND (TO_CHAR (UN_APPROVED_DATE, 'DD-MON-YYYY') > TO_CHAR ((SYSDATE-3), 'DD-MON-YYYY'))  GROUP BY MST_ID) and ENTRY_form=15  order by id asc";
	  //echo $sql;  
	
	$sql_res=sql_select($sql);
	$historyDataArr=array();
	foreach($sql_res as $row)
	{
		$historyDataArr[ID][$row[MST_ID]]=$row[MST_ID];
		$historyDataArr[UN_APPROVED_DATE][$row[MST_ID]]=$row[UN_APPROVED_DATE];
		$historyDataArr[DAYS_LEFT][$row[MST_ID]]=datediff('d',$row[CURRENT_DATE],$row[UN_APPROVED_DATE]);
		if($row[FULL_APPROVED]==1){unset($historyDataArr[ID][$row[MST_ID]]);}
	}



	$orderSql="select A.ID AS PRE_COST_MST_ID,a.JOB_NO,b.COMPANY_NAME,b.BUYER_NAME,B.GMTS_ITEM_ID,B.DEALING_MARCHANT,B.ORDER_UOM,B.JOB_QUANTITY,B.TOTAL_PRICE,b.TOTAL_SET_QNTY,b.STYLE_REF_NO,b.SET_SMV,b.TEAM_LEADER,c.MARGIN_PCS_SET from WO_PRE_COST_MST a,WO_PO_DETAILS_MASTER b,WO_PRE_COST_DTLS c where b.id=a.job_id and b.id=c.JOB_ID and a.job_id=c.job_id  ".where_con_using_array($historyDataArr[ID],0,'a.id')." and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0";
	 // echo $orderSql;die;
	$orderSqlRes=sql_select($orderSql);
	$orderDataArr=array();
	foreach($orderSqlRes as $row)
	{
		$orderDataArr[$row[JOB_NO]]=$row;
	}


	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$company_lib = return_library_array("select id,company_name from lib_company","id","company_name");
	$del_mar_lib = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
    $team_leader_arr = return_library_array("select id,team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name", 'id', 'team_leader_name');


ob_start();	
?>
<table rules="all" border="1">
    <thead bgcolor="#CCCCCC">
        <th>SL</th>
        <th>Company</th>
        <th>Buyer</th>
        <th>Job No</th>
        <th>Style</th>
        <th>Gmts Item Name</th>
        <th>SMV</th>
        <th>Order Qty</th>
        <th>UOM</th>
        <th>Order Qty (Pcs)</th>
        <th>Order Value</th>
        <th>Total Margin</th>
        <th>Margin %</th>
        <th>Last Un-approve Date</th>
        <th>Team Leader</th>
        <th>Dealing Merchat</th>
        <th>Days Left</th>
    </thead>
    <tbody>
    
	<?php 
	$i=1;
	foreach($orderDataArr as $job_no=>$row){ 
		//$mailArr[$row[INSERTED_BY]]=$user_maill_arr[$row[INSERTED_BY]];
		$garmentsItemArr=array();
		foreach(explode(',',$row[GMTS_ITEM_ID]) as $gitem){
			$garmentsItemArr[$gitem]=$garments_item[$gitem];
		}
	
	?>
    <tr>
        <td><?=$i;?></td>
        <td><?=$company_lib[$row[COMPANY_NAME]];?></td>
        <td><?=$buyer_arr[$row[BUYER_NAME]];?></td>
        <td><?=$job_no;?></td>
        <td><?=$row[STYLE_REF_NO];?></td>
        <td><?= implode(',',$garmentsItemArr);?></td>
        <td align="center"><?=$row[SET_SMV];?></td>
        <td align="right"><?=$row[JOB_QUANTITY];?></td>
        <td><?=$unit_of_measurement[$row[ORDER_UOM]];?></td>
        <td align="right"><?=number_format($job_qty_pcs = $row[JOB_QUANTITY]*$row[TOTAL_SET_QNTY]);?></td>
        <td align="right"><?=number_format($row[TOTAL_PRICE],2);?></td>
        <td align="right"><?=number_format($job_qty_pcs*$row[MARGIN_PCS_SET],2);?></td>
        <td align="right"><?=number_format(($job_qty_pcs*$row[MARGIN_PCS_SET]/$row[TOTAL_PRICE])*100,2);?></td>
        <td><?=$historyDataArr[UN_APPROVED_DATE][$row[PRE_COST_MST_ID]];?></td>
        <td><?=$team_leader_arr[$row[TEAM_LEADER]];?></td>
        <td><?=$del_mar_lib[$row[DEALING_MARCHANT]];?></td>
        <td><?=$historyDataArr[DAYS_LEFT][$row[PRE_COST_MST_ID]];?></td>
    </tr>
    <?php $i++;} ?>
    </tbody>
</table>
<?	
	

	
	$message=ob_get_contents();
	ob_clean();

	$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=93 and a.MAIL_TYPE in(2,0) and b.mail_user_setup_id=c.id AND a.MAIL_TYPE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		$mailArr[]=$row['EMAIL_ADDRESS'];
	}
	$to=implode(',',$mailArr);
	
 

	  if($_REQUEST['isview']==1){
		$mail_item=93;
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
		$subject="Re Approval Pending List of Precost";
		$header=mailHeader();
		if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);
	}





?> 