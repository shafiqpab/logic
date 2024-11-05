<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Mail</title>
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


$action='shipment_pending_mail_notification';	
	
if($action=='shipment_pending_mail_notification'){
	
	//$data=2120;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	
	//$team_member_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0","id","team_member_name");
	$team_member_arr=return_library_array("select id,team_leader_name from lib_marketing_team where project_type=6 and status_active =1 and is_deleted=0 order by team_leader_name","id","team_leader_name");
	
	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	$current_date = change_date_format(date("Y-m-d", $strtotime),'','',1);
	$previous_date = change_date_format(date('Y-m-d', strtotime('-29 day', strtotime($current_date))),'','',1);
 
 
	if($db_type==0)
	{	
		$date_diff="(DATEDIFF($current_date, b.PUB_SHIPMENT_DATE))";
		//$where_con=" and a.bl_date =''";
	}
	else
	{
		$date_diff="(to_date('$current_date')-to_date(b.PUB_SHIPMENT_DATE, 'dd-MM-yy'))";
		
		//$date_diff_sc="(to_date(contract_date, 'dd-MM-yy')- to_date(TO_CHAR(insert_date,'dd-MM-yy HH24:MI:SS')))";
		//$where_con=" and a.bl_date is NULL";
		
		//$date_diff_lc="(TRUNC(insert_date )-TRUNC(lc_date ))";
		//$date_diff_sc="(TRUNC(insert_date )-TRUNC(contract_date ))";
		
	}
	

foreach($company_arr as $company_id=>$company_name){	
	
	
	
	//order ..................................................
	$sql = "select a.TEAM_LEADER,a.JOB_NO,a.BUYER_NAME,a.STYLE_REF_NO,b.PUB_SHIPMENT_DATE,b.PO_NUMBER,(a.TOTAL_SET_QNTY*b.PO_QUANTITY) as PO_QTY_PCS,b.SHIPING_STATUS, $date_diff as SHIP_DATE_OVER,b.id as PO_ID FROM wo_po_details_master a,wo_po_break_down b WHERE b.job_no_mst = a.job_no AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 and b.SHIPING_STATUS !=3 and $date_diff>14 and $date_diff < 20 and a.COMPANY_NAME=$company_id  order by b.PUB_SHIPMENT_DATE";
	$sql_result = sql_select($sql);
	foreach ($sql_result as $rows)
	{	
		$po_id_arr[$rows[PO_ID]] = $rows[PO_ID];
	}

	//Exfactory ..................................................
	$sqlExf="select b.po_break_down_id,sum(a.production_qnty) as production_qnty
	from pro_garments_production_dtls a,pro_garments_production_mst b
	where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id in(".implode(',',$po_id_arr).") and a.production_type=8 group by b.po_break_down_id
	union all 
	select b.po_break_down_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
	from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id in(".implode(',',$po_id_arr).") and a.production_type=10 group by b.po_break_down_id";
	
	$sqlExfResult = sql_select($sqlExf);	
	foreach ($sqlExfResult as $rows)
	{	
		$po_exf_qty_arr[$rows[csf('po_break_down_id')]] += $rows[csf('production_qnty')];
	}
	


	$sqlCut="SELECT a.PO_BREAK_DOWN_ID,
	sum(CASE WHEN a.production_type = 52 and b.production_type = 52 THEN b.PRODUCTION_QNTY ELSE 0 END) AS PRODUCTION_QNTY_CUTTING,
	sum(CASE WHEN a.production_type = 56 and b.production_type = 56 THEN b.PRODUCTION_QNTY ELSE 0 END) AS PRODUCTION_QNTY_LINKING,
	sum(CASE WHEN a.production_type = 3 and b.production_type = 3 THEN b.PRODUCTION_QNTY ELSE 0 END) AS PRODUCTION_QNTY_WASH,
	sum(CASE WHEN a.production_type = 8 and b.production_type = 8 THEN b.PRODUCTION_QNTY ELSE 0 END) AS PRODUCTION_QNTY_FINISH
	FROM pro_garments_production_mst a, pro_garments_production_dtls b
 WHERE a.id = b.mst_id  and a.PO_BREAK_DOWN_ID in(".implode(',',$po_id_arr).") AND a.production_type in( 52,56,3,8) AND b.production_type in( 52,56,3,8) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 group by a.PO_BREAK_DOWN_ID";
	$sqlCutResult = sql_select($sqlCut);	
	foreach ($sqlCutResult as $rows)
	{	
		$po_cut_qty_arr[$rows['PO_BREAK_DOWN_ID']] += $rows[PRODUCTION_QNTY_CUTTING];
		$po_linking_qty_arr[$rows['PO_BREAK_DOWN_ID']] += $rows[PRODUCTION_QNTY_LINKING];
		$po_wash_qty_arr[$rows['PO_BREAK_DOWN_ID']] += $rows[PRODUCTION_QNTY_WASH];
		$po_finish_qty_arr[$rows['PO_BREAK_DOWN_ID']] += $rows[PRODUCTION_QNTY_FINISH];
	}



	$width=1000;
	$width2=1100;
	ob_start();	
	?>
	<div style="width:<? echo $width;?>px; margin-bottom:5px;" align="left">
		<fieldset>
            <table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="12" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[$company_id]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="12">Shipment Pending Report</td>
                </tr>  
            </table>
            <table width="<? echo $width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr>
                        <th width="35">Sl</th>
                        <th width="100">Buyer Name</th>
                        <th width="100">Team Leader</th>
                        <th width="80">Job No</th>
                        <th width="150">Style No</th>
                        <th width="100">PO No</th>
                        <th width="70">Pub. Ship Date</th>
                        <th width="60">Shipdate Over</th>
                        <th width="60">PO Qty (Pcs)</th>
                        <th width="60">Ship Qty</th>
                        <th width="60">In Hand Qty</th>
                        <th>Ship Status</th>
                    </tr>
                </thead>
	            <tbody>
	                <?
					$i= 1;$flag=0;
					foreach($sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td width="35" align="center"><? echo $i;?></td>
	                        <td width="100"><p><? echo $buyer_arr[$row[BUYER_NAME]];?></p></td>
	                        <td width="100"><p><? echo $team_member_arr[$row[TEAM_LEADER]];?></p></td>
	                        <td width="80" align="center"><? echo $row[JOB_NO];?></td>
	                        <td width="150"><p><? echo $row[STYLE_REF_NO];?></p></td>
	                        <td width="100"><p><? echo $row[PO_NUMBER];?></p></td>
	                        <td width="70" align="center"><? echo $row[PUB_SHIPMENT_DATE];?></td>
	                        <td width="60" align="center"><? echo $row[SHIP_DATE_OVER];?></td>
	                        <td width="60" align="right"><? echo $row[PO_QTY_PCS];?></td>
	                        <td width="60" align="right"><? echo $po_exf_qty_arr[$row[PO_ID]];?></td>
	                        <td width="60" align="right"><? echo ($row[PO_QTY_PCS]-$po_exf_qty_arr[$row[PO_ID]]);?></td>
	                        <td align="center"><? echo $shipment_status[$row[SHIPING_STATUS]];?></td>
	                    </tr>
						<?
						
	                    $i++;
						$flag=1;
	                }
	                ?>
	                
	                </tbody>
	            </table>
             
        </fieldset>
        <br />
        <fieldset>
            <table width="<? echo $width2;?>" cellpadding="0" cellspacing="0" id="caption">
                <tr>  
                   <td align="center" width="100%" colspan="12">Pending PO Production Status Report</td>
                </tr>  
            </table>
            <table width="<? echo $width2;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr>
                        <th width="35">Sl</th>
                        <th width="100">Buyer Name</th>
                        <th width="100">Team Leader</th>
                        <th width="80">Job No</th>
                        <th width="150">Style No</th>
                        <th width="100">PO No</th>
                        <th width="70">Pub. Ship Date</th>
                        <th width="60">Shipdate Over</th>
                        <th width="60">PO Qty (Pcs)</th>
                        <th width="60">Knitting Qty</th>
                        <th width="60">Make Up</th>
                        <th width="60">Wash</th>
                        <th width="60">Finishng</th>
                        <th>Ship Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i= 1;
                    foreach($sql_result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="35" align="center"><? echo $i;?></td>
                            <td width="100"><p><? echo $buyer_arr[$row[BUYER_NAME]];?></p></td>
                            <td width="100"><p><? echo $team_member_arr[$row[TEAM_LEADER]];?></p></td>
                            <td width="80" align="center"><? echo $row[JOB_NO];?></td>
                            <td width="150"><p><? echo $row[STYLE_REF_NO];?></p></td>
                            <td width="100"><p><? echo $row[PO_NUMBER];?></p></td>
                            <td width="70" align="center"><? echo $row[PUB_SHIPMENT_DATE];?></td>
                            <td width="60" align="center"><? echo $row[SHIP_DATE_OVER];?></td>
                            <td width="60" align="right"><? echo $row[PO_QTY_PCS];?></td>
                            <td width="60" align="right"><? echo $po_cut_qty_arr[$row[PO_ID]];?></td>
                            <td width="60" align="right"><? echo $po_linking_qty_arr[$row[PO_ID]];?></td>
                            <td width="60" align="right"><? echo $po_wash_qty_arr[$row[PO_ID]];?></td>
                            <td width="60" align="right"><? echo $po_finish_qty_arr[$row[PO_ID]];?></td>
                            <td align="center"><? echo $shipment_status[$row[SHIPING_STATUS]];?></td>
                        </tr>
                        <?
                        $i++;
                        $flag=1;
                    }
                    ?>
                    </tbody>
              </table>                
                
        </fieldset>
    </div>
	<?
	$emailBody=ob_get_contents();
	ob_clean();

	
	
	
	
	
	$mail_item = 27;
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item = $mail_item and b.mail_user_setup_id=c.id AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and a.company_id =".$company_id."";	
	$mail_sql2=sql_select($sql2);
	$mailToArr=array();
	foreach($mail_sql2 as $row)
	{
		if ($row[csf('email_address')]!="") {
			$mailToArr[$row[csf('email_address')]]=$row[csf('email_address')];
		}

	}
	
	$to = implode(',',$mailToArr);

	$subject="Shipment Pending Report";


    if($_REQUEST['isview']==1){
        if($to){
            echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
        }else{
            echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
        }
        echo $emailBody;
    }
    else{
        if($to!=""){echo sendMailMailer( $to, $subject, $emailBody, $from_mail );}
    }



 

}
	
exit();	

}

?>




</body>
</html>
