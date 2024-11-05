<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Auto Mail</title>
</head>
<body>
<?php
date_default_timezone_set("Asia/Dhaka");
include('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
include('setting/mail_setting.php');
extract($_REQUEST);

echo load_html_head_contents("Daily Order Update Report", "../", 1, 1,'','','');


$action='pending_po_production_status_auto_mail';	
	
if($action=='pending_po_production_status_auto_mail'){
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");

	
	
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name", "id", "team_leader_name"  );

	

	if($db_type==0)
	{
		$current_date = date("Y-m-d",time());
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date = date('Y-m-d', strtotime('-1 day', strtotime($current_date))); 
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d",time()),'','',1);
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date = change_date_format(date('Y-m-d', strtotime('-1 day', strtotime($current_date))),'','',1);
	}

 
 
	if($db_type==0){
		$date_cond =" and b.UPDATE_DATE between '".$previous_date."' and '".$previous_date."'";
	}
	else
	{
		$date_cond =" and b.UPDATE_DATE between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}

 
 


	if($db_type==0)
	{	
		$date_diff="(DATEDIFF(b.PUB_SHIPMENT_DATE), '".date('Y-m-d',strtotime($current_date))."')";
	}
	else
	{
		$date_diff="(to_date(B.PUB_SHIPMENT_DATE, 'dd-MM-yy') - to_date('".date('d-M-y',strtotime($current_date))."', 'dd-MM-yy'))";
	}

//$company_arr=array(1=>$company_arr[1]);
foreach($company_arr as $company_id=>$company_name){	
	
	
	 


$sql = "select a.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.JOB_NO,a.TEAM_LEADER,b.id as PO_ID,b.PO_NUMBER,b.PUB_SHIPMENT_DATE,$date_diff as SHIP_OVER ,(b.PO_QUANTITY*a.TOTAL_SET_QNTY) as PO_QUANTITY_PCS,b.SHIPING_STATUS,b.UPDATE_DATE,b.IS_CONFIRMED from WO_PO_DETAILS_MASTER a ,WO_PO_BREAK_DOWN b where a.id=b.job_id and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.IS_DELETED=0 and b.SHIPING_STATUS <>3  and a.COMPANY_NAME=$company_id $date_cond ";
   //echo $sql;
 	$dataArr=sql_select($sql);
	$po_id_arr=array();
	foreach ($dataArr as $rows)
	{	
		$po_id_arr[$rows[PO_ID]] = $rows[PO_ID];
	}
 
//Exfactory ..................................................
	/*$sqlExf="select b.po_break_down_id,sum(a.production_qnty) as production_qnty
	from pro_garments_production_dtls a,pro_garments_production_mst b
	where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id in(".implode(',',$po_id_arr).") and a.production_type=8 group by b.po_break_down_id
	union all 
	select b.po_break_down_id,(sum( CASE WHEN b.trans_type=5 THEN a.production_qnty ELSE 0 END)-sum( CASE WHEN b.trans_type=6 THEN a.production_qnty ELSE 0 END)) as production_qnty
	from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id in(".implode(',',$po_id_arr).") and a.production_type=10 group by b.po_break_down_id";
	
	$sqlExfResult = sql_select($sqlExf);
	$po_exf_qty_arr=array();	
	foreach ($sqlExfResult as $rows)
	{	
		$po_exf_qty_arr[$rows[csf('po_break_down_id')]] += $rows[csf('production_qnty')];
	}*/
	

	$sql = "select a.PO_BREAK_DOWN_ID,a.INSPECTION_STATUS,a.INSPECTION_LEVEL,a.INSPECTION_CAUSE,a.INSPECTION_QNTY FROM PRO_BUYER_INSPECTION a WHERE a.PO_BREAK_DOWN_ID  in(".implode(',',$po_id_arr).") and a.status_active = 1 AND a.is_deleted = 0 and a.INSPECTION_LEVEL=1";
	//echo $sql;die;
		
	$sql_result = sql_select($sql);
	$ins_complate_pcs=array();
	foreach ($sql_result as $row)
	{	
		$ins_complate_pcs[$row[PO_BREAK_DOWN_ID]]+=$row[INSPECTION_QNTY];
	}
	
	
	

	$sqlCut="SELECT a.PO_BREAK_DOWN_ID,
	sum(CASE WHEN a.production_type = 52 and b.production_type = 52 THEN b.PRODUCTION_QNTY ELSE 0 END) AS PRODUCTION_QNTY_CUTTING,
	sum(CASE WHEN a.production_type = 56 and b.production_type = 56 THEN b.PRODUCTION_QNTY ELSE 0 END) AS PRODUCTION_QNTY_LINKING,
	sum(CASE WHEN a.production_type = 3 and b.production_type = 3 THEN b.PRODUCTION_QNTY ELSE 0 END) AS PRODUCTION_QNTY_WASH,
	sum(CASE WHEN a.production_type = 8 and b.production_type = 8 THEN b.PRODUCTION_QNTY ELSE 0 END) AS PRODUCTION_QNTY_FINISH,
	sum(CASE WHEN a.production_type = 7 and b.production_type = 7 THEN b.PRODUCTION_QNTY ELSE 0 END) AS PRODUCTION_QNTY_IRON,
	sum(CASE WHEN a.production_type = 5 and b.production_type = 5 THEN b.PRODUCTION_QNTY ELSE 0 END) AS PRODUCTION_QNTY_SEWING
	FROM pro_garments_production_mst a, pro_garments_production_dtls b
 WHERE a.id = b.mst_id  and a.PO_BREAK_DOWN_ID in(".implode(',',$po_id_arr).") AND a.production_type in( 52,56,3,8,7,5) AND b.production_type in( 52,56,3,8,7,5) AND b.status_active = 1 AND b.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 group by a.PO_BREAK_DOWN_ID";
	
	//echo $sqlCut;die;
	$sqlCutResult = sql_select($sqlCut);	
	
	$po_cut_qty_arr=array();	
	$po_linking_qty_arr=array();	
	$po_wash_qty_arr=array();	
	$po_finish_qty_arr=array();	
	$po_sewing_qty_arr=array();	
	$po_iron_qty_arr=array();	
	
	foreach ($sqlCutResult as $rows)
	{	
		$po_cut_qty_arr[$rows['PO_BREAK_DOWN_ID']] += $rows[PRODUCTION_QNTY_CUTTING];
		$po_linking_qty_arr[$rows['PO_BREAK_DOWN_ID']] += $rows[PRODUCTION_QNTY_LINKING];
		$po_wash_qty_arr[$rows['PO_BREAK_DOWN_ID']] += $rows[PRODUCTION_QNTY_WASH];
		$po_finish_qty_arr[$rows['PO_BREAK_DOWN_ID']] += $rows[PRODUCTION_QNTY_FINISH];
		$po_sewing_qty_arr[$rows['PO_BREAK_DOWN_ID']] += $rows[PRODUCTION_QNTY_SEWING];
		$po_iron_qty_arr[$rows['PO_BREAK_DOWN_ID']] += $rows[PRODUCTION_QNTY_IRON];
		
	} 
 
 
	$width2=650;
	$width=1750;
	ob_start();	
	?>
	<div style="width:<? echo $width;?>px; margin-bottom:5px;" align="left">
		<fieldset>
            <table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="18" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[$company_id]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="18">Daily Order Update Report</td>
                </tr>  
                
                <tr>  
                   <td align="center" width="100%" colspan="18">Date:<?= change_date_format($previous_date);?></td>
                </tr>  
                
                
            </table>
            
   			<table width="<? echo $width2;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr>
                        <th width="35">Sl</th>
                        <th width="100">Buyer Name</th>
                        <th width="80">Job No</th>
                        <th width="100">Style No</th>
                        <th width="50">Status</th>
                        <th width="100">PO No</th>
                        <th width="60">Qty (Pcs)</th>
                        <th>Update Date</th>
                    </tr>
                </thead>
	            <tbody>
	                <?
					$i= 1;
					foreach($dataArr as $pi_id=>$row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                        <td align="center"><? echo $i;?></td>
	                        <td><? echo $buyer_arr[$row[BUYER_NAME]];?></td>
	                        <td align="center"><? echo $row[JOB_NO];?></td>
	                        <td><? echo $row[STYLE_REF_NO];?></td>
	                        <td><? echo $order_status[$row[IS_CONFIRMED]];?></td>
	                        <td><? echo $row[PO_NUMBER];?></td>
	                        <td align="center"><? echo $row[PO_QUANTITY_PCS];?></td>
	                        <td align="center"><? echo $row[UPDATE_DATE];?></td>
	                    </tr>
						<?
						
	                    $i++;
	                }
	                ?>
	                
	                </tbody>
	            </table>          
            
            
            
            <br />
            
            
            
            
            
            
            <table width="<? echo $width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr>
                        <th width="35">Sl</th>
                        <th width="100">Buyer Name</th>
                        <th width="100">Team Leader</th>
                        <th width="100">Job No</th>
                        <th width="100">Style No</th>
                        <th width="100">PO No</th>
                        <th width="80">Pub. Ship Date</th>
                        <th width="60">Shipdate Over</th>
                        <th width="100">Qty (Pcs)</th>
                        <th width="100">Knitting Qty (Pcs)</th>
                        <th width="100">1st Inspection Complete (Pcs)</th>
                        <th width="100">Make Up Complete (Pcs)</th>
                        <th width="100">Wash Complete (Pcs)</th>
                        <th width="100">Attachment Complete (Pcs)</th>
                        <th width="100">Sewing Complete (Pcs)</th>
                        <th width="100">Iron Complete (Pcs)</th>
                        <th width="100">Packing Complete (Pcs)</th>
                        <th>Ship Status</th>
                    </tr>
                </thead>
	            <tbody>
	                <?
					$i= 1;
					foreach($dataArr as $pi_id=>$row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                        <td align="center"><? echo $i;?></td>
	                        <td><? echo $buyer_arr[$row[BUYER_NAME]];?></td>
	                        <td align="center"><? echo $team_leader_arr[$row[TEAM_LEADER]];?></td>
	                        <td align="center"><? echo $row[JOB_NO];?></td>
	                        <td><? echo $row[STYLE_REF_NO];?></td>
	                        <td><? echo $row[PO_NUMBER];?></td>
	                        <td align="center"><? echo change_date_format($row[PUB_SHIPMENT_DATE]);?></td>
	                        <td align="center"><? echo $row[SHIP_OVER];?></td>
	                        <td align="center"><? echo $row[PO_QUANTITY_PCS];?></td>
	                        <td align="center"><? echo $po_cut_qty_arr[$row[PO_ID]];?></td>
	                        <td align="center"><? echo $ins_complate_pcs[$row[PO_ID]]?></td>
	                        <td align="center"><? echo $po_linking_qty_arr[$row[PO_ID]];?></td>
	                        <td align="center"><? echo $po_wash_qty_arr[$row[PO_ID]];?></td>
	                        <td align="center"><? echo $row[xxxxxxx];?></td>
	                        <td align="center"><? echo $po_sewing_qty_arr[$row[PO_ID]];?></td>
	                        <td align="center"><? echo $po_iron_qty_arr[$row[PO_ID]];?></td>
	                        <td align="center"><? echo $po_finish_qty_arr[$row[PO_ID]];?></td>
	                        <td align="center"><? echo $shipment_status[$row[SHIPING_STATUS]];?></td>
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
	$mail_item=34;
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=34 and b.mail_user_setup_id=c.id   and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and a.company_id =".$company_id."";	
	$mail_sql2=sql_select($sql2);
	foreach($mail_sql2 as $row)
	{
		if ($to=="") {
			$to=$row[csf('email_address')];
		}  else{
			 $to=$to.",".$row[csf('email_address')];
		}

	}



	$subject="Daily Order Update Report";
	$header=mailHeader();
	if($_REQUEST['isview']==1){
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $emailBody;
	}
	else{
		if($to!="")echo sendMailMailer( $to, $subject, $emailBody, $from_mail);
	}

	//echo $emailBody;
}
	
exit();	

}

?>




</body>
</html>
