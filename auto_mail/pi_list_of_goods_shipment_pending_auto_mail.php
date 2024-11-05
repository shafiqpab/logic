<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>


<?php
date_default_timezone_set("Asia/Dhaka");
// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
extract($_REQUEST);
	
// var returnValue=return_global_ajax_value(reponse[2], 'price_quotation_mail_notification', '', '../../../auto_mail/mail_notification');

echo load_html_head_contents("Mail Notification", "../", 1, 1,'','','');


$action='pi_list_of_goods_shipment_pending_auto_mail';	
	
if($action=='pi_list_of_goods_shipment_pending_auto_mail'){
	
	//$data=2120;
	$company_arr=return_library_array( "select id, company_name from lib_company ",'id','company_name');
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	
	$team_member_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0","id","team_member_name");
	
	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	$current_date = change_date_format(date("Y-m-d", $strtotime),'','',1);
	$previous_date = change_date_format(date('Y-m-d', strtotime('-180 day', strtotime($current_date))),'','',1);
	 
	  
 
	if($db_type==0)
	{	
		$date_diff="(DATEDIFF($current_date, a.LAST_SHIPMENT_DATE))";
	}
	else
	{
		$date_diff="(to_date('$current_date')-to_date(a.LAST_SHIPMENT_DATE, 'dd-MM-yy'))";
	}
	
	$item_group_arr=return_library_array( "SELECT id,item_name FROM lib_item_group",'id','item_name');
	$supplier_name_arr=return_library_array( "SELECT id,supplier_name FROM lib_supplier",'id','supplier_name');
	
	

foreach($company_arr as $company_id=>$company_name){	
	
	
	
	//PI ..................................................
	$sql = "select a.IMPORTER_ID,a.LC_NUMBER,a.SUPPLIER_ID,a.LAST_SHIPMENT_DATE,a.LC_DATE,a.LC_EXPIRY_DATE,a.PI_ID FROM COM_BTB_LC_MASTER_DETAILS a WHERE a.status_active = 1 AND a.is_deleted = 0 and a.LAST_SHIPMENT_DATE between '".$previous_date."' and '".$current_date."' and a.IMPORTER_ID=$company_id and $date_diff>5" ;
	$sql_result = sql_select($sql);
	$po_id_arr=array();$piDataArr=array();
	foreach ($sql_result as $rows)
	{	
		foreach(explode(',',$rows[PI_ID]) as $pi_id){
			if($pi_id){
			$po_id_arr[$pi_id] = $pi_id;
			}
			$piDataArr[$pi_id]=$rows;	
		}
		
	}

	$sql = "SELECT b.PI_ID FROM COM_IMPORT_INVOICE_MST a,COM_IMPORT_INVOICE_DTLS b where a.id=b.IMPORT_INVOICE_ID and b.PI_ID in(".implode(',',$po_id_arr).") and a.ETA_ACTUAL is null";
	$sql_result = sql_select($sql);
	$newPiDataArr=array();
	foreach ($sql_result as $rows)
	{	
		$newPiDataArr[$rows[PI_ID]]=$piDataArr[$rows[PI_ID]];
	}
	

	$sql = "select a.PI_NUMBER,a.ITEM_CATEGORY_ID,b.PI_ID,b.ITEM_GROUP,b.ITEM_DESCRIPTION,b.UOM,AVG(b.RATE) as RATE,SUM(b.QUANTITY) as QUANTITY,SUM(b.AMOUNT) as AMOUNT FROM com_pi_master_details a,com_pi_item_details b WHERE a.id=b.pi_id and a.status_active = 1 AND a.is_deleted = 0 and a.id in(".implode(',',$po_id_arr).") and a.IMPORTER_ID=$company_id
group by a.PI_NUMBER,a.ITEM_CATEGORY_ID,b.PI_ID,b.ITEM_GROUP,b.ITEM_DESCRIPTION,b.UOM";
	
	$sql_result = sql_select($sql);
	$invoiceQtyData=array();$invoiceValData=array();$invoiceData=array();
	foreach ($sql_result as $row)
	{	
		$invoiceQtyData[$row[PI_ID]]+=$row[QUANTITY];
		$invoiceValData[$row[PI_ID]]+=$row[AMOUNT];
		
		$invoiceData[ITEM_GROUP][$row[PI_ID]][$row[ITEM_GROUP]]=$item_group_arr[$row[ITEM_GROUP]];
		$invoiceData[ITEM_DESCRIPTION][$row[PI_ID]][$row[ITEM_DESCRIPTION]]=$row[ITEM_DESCRIPTION];
		$invoiceData[PI_NUMBER][$row[PI_ID]][$row[PI_NUMBER]]=$row[PI_NUMBER];
		$invoiceData[ITEM_CATEGORY_ID][$row[PI_ID]][$row[ITEM_CATEGORY_ID]]=$item_category[$row[ITEM_CATEGORY_ID]];
		$invoiceData[UOM][$row[PI_ID]][$row[UOM]]=$unit_of_measurement[$row[UOM]];
	}



	$width=1300;
	ob_start();	
	?>
	<div style="width:<? echo $width;?>px; margin-bottom:5px;" align="left">
		<fieldset>
            <table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[$company_id]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="16">PI List of Goods Shipment Pending</td>
                </tr>  
            </table>
            <table width="<? echo $width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr>
                        <th width="35">Sl</th>
                        <th width="100">Importer</th>
                        <th width="100">PI Numner</th>
                        <th width="100">Supplier Name</th>
                        <th width="100">Item Category</th>
                        <th width="100">Item Group</th>
                        <th width="100">Item Description</th>
                        <th width="50">UOM</th>
                        <th width="80">Qty</th>
                        <th width="60">Rate</th>
                        <th width="80">Amount ($)</th>
                        <th width="100">LC No</th>
                        <th width="80">LC Date</th>
                        <th width="80">Last Ship Date</th>
                        <th width="80">LC Expiry Date</th>
                        <th>Early/Late (Days)</th>
                    </tr>
                </thead>
	            <tbody>
	                <?
					$i= 1;
					foreach($newPiDataArr as $pi_id=>$row)
					{
						$datediff=datediff('d',date("d-m-Y"),$row[LAST_SHIPMENT_DATE])-1;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td width="35" align="center"><? echo $i;?></td>
	                        <td width="100"><? echo $company_arr[$row[IMPORTER_ID]];?></td>
	                        <td width="100" align="right"><? echo implode(',',$invoiceData[PI_NUMBER][$row[PI_ID]]);?></td>
	                        <td width="100"><? echo $supplier_name_arr[$row[SUPPLIER_ID]];?></td>
	                        <td width="100"><? echo implode(',',$invoiceData[ITEM_CATEGORY_ID][$row[PI_ID]]);?></td>
	                        <td width="100"><? echo implode(',',$invoiceData[ITEM_GROUP][$row[PI_ID]]);?></td>
	                        <td width="100"><? echo implode(',',$invoiceData[ITEM_DESCRIPTION][$row[PI_ID]]);?></td>
	                        <td width="50" align="center"><? echo implode(',',$invoiceData[UOM][$row[PI_ID]]);?></td>
	                        <td width="80" align="right"><? echo $invoiceQtyData[$row[PI_ID]];?></td>
	                        <td width="60" align="center"><? echo number_format($invoiceValData[$row[PI_ID]]/$invoiceQtyData[$row[PI_ID]],2);?></td>
	                        <td width="80" align="right"><? echo $invoiceValData[$row[PI_ID]];?></td>
	                        <td width="100" align="right"><? echo $row[LC_NUMBER];?></td>
	                        <td width="80" align="right"><? echo $row[LC_DATE];?></td>
	                        <td width="80" align="right"><? echo $row[LAST_SHIPMENT_DATE];?></td>
	                        <td width="80" align="right"><? echo $row[LC_EXPIRY_DATE];?></td>
	                        <td align="center"><? echo $datediff;?></td>
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
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=27 and b.mail_user_setup_id=c.id AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and a.company_id =".$company_id."";	
	$mail_sql2=sql_select($sql2);
	foreach($mail_sql2 as $row)
	{
		if ($to=="") {
			$to=$row[csf('email_address')];
		}  else{
			 $to=$to.",".$row[csf('email_address')];
		}

	}

	$subject="PI List of Goods Shipment Pending";
	//if($to!=""){echo send_mail_mailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Not Send';}
	if($_REQUEST['isview']==1){
		echo $emailBody;
	}
	else{
		if($to!=""){echo send_mail_mailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Not Send';}
	}

}
	
exit();	

}

?>
<!-- if comment -->




</body>
</html>
