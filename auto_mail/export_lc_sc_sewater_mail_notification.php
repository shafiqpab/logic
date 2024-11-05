<?php
date_default_timezone_set("Asia/Dhaka");
// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require_once('setting/mail_setting.php');

extract($_REQUEST);
	
// var returnValue=return_global_ajax_value(reponse[2], 'price_quotation_mail_notification', '', '../../../auto_mail/mail_notification');

echo load_html_head_contents("Mail Notification", "../", 1, 1,'','','');


$action='price_quotation_mail_notification';	
	
if($action=='price_quotation_mail_notification'){
	
	//$data=2120;
$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$bank_arr=return_library_array("select id,bank_name from   lib_bank","id","bank_name");

 
$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = change_date_format(date("Y-m-d", $strtotime),'','',1);
$previous_date = change_date_format(date('Y-m-d', strtotime('-29 day', strtotime($current_date))),'','',1);
 

 
	if($db_type==0)
	{	
		$date_diff_lc="(DATEDIFF(insert_date, lc_date))";
		$date_diff_sc="(DATEDIFF(insert_date, contract_date))";
		//$where_con=" and a.bl_date =''";
	}
	else
	{
		//$date_diff_lc="(to_date(lc_date, 'dd-MM-yy')- to_date(TO_CHAR(insert_date,'dd-MM-yy HH24:MI:SS')))";
		//$date_diff_sc="(to_date(contract_date, 'dd-MM-yy')- to_date(TO_CHAR(insert_date,'dd-MM-yy HH24:MI:SS')))";
		//$where_con=" and a.bl_date is NULL";
		$date_diff_lc="(TRUNC(insert_date )-TRUNC(lc_date ))";
		$date_diff_sc="(TRUNC(insert_date )-TRUNC(contract_date ))";
		
	}
	

foreach($company_arr as $company_id=>$company_name){	
	
	$cbo_company_name=$company_id;
	

	if ($previous_date!="" && $current_date!="")
	{
		$str_cond=" and lc_date between '$previous_date' and  '$current_date'";
		
		$str_cond_con=" and contract_date between '$previous_date' and  '$current_date'";
	}

	$sql="select id, internal_file_no,bank_file_no,lc_year as lc_sc_year,beneficiary_name,buyer_name,applicant_name,export_lc_no as lc_sc ,lc_date as lc_sc_date,insert_date,last_shipment_date,lc_value as lc_sc_value, currency_name,expiry_date,lien_bank,lien_date,pay_term,tenor,inco_term,nominated_shipp_line as ship_line, null as convertible_to_lc,issuing_bank_name,transfer_bank,negotiating_bank,re_imbursing_bank,replacement_lc, null as converted_from,1 as type ,$date_diff_lc as date_diff
	from  com_export_lc
	where  beneficiary_name=$cbo_company_name  and status_active=1 and is_deleted=0 $str_cond and $date_diff_lc>3  
	
	UNION ALL
	
	select id,internal_file_no,bank_file_no,sc_year as lc_sc_year,beneficiary_name,buyer_name,applicant_name,contract_no as lc_sc,contract_date as lc_sc_date,insert_date,last_shipment_date,contract_value as lc_sc_value,currency_name,expiry_date,lien_bank,lien_date,pay_term,tenor,inco_term,shipping_line as ship_line,convertible_to_lc, null as issuing_bank_name, null as transfer_bank , null as negotiating_bank, null as re_imbursing_bank, null as replacement_lc, converted_from,2 as type ,$date_diff_sc as date_diff 
	from com_sales_contract
	where  beneficiary_name =$cbo_company_name and status_active=1 and is_deleted=0 $str_cond_con and $date_diff_sc>3  order by internal_file_no";
	
	   //echo $sql;
	$sql_re=sql_select($sql);	
	$com_ex_lc_id_arr=array();
	foreach ($sql_re as $value) 
	{
		$com_ex_lc_id_arr[$value[csf('id')]]=$value[csf('id')];
	}
	$com_ex_lc_ids_con = implode(",",$com_ex_lc_id_arr);
	
	
	
	
	
	
	
	
	$width=1160;
	ob_start();	
	?>
	<div style="width:<? echo $width;?>px;">
		<fieldset>
            <table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="20">Export LC/Sales Contract Report</td>
                </tr>  
            </table>
            <table width="<? echo $width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr>
                        <th width="35">Sl</th>
                        <th width="100">Internal File No.</th>
                        <th width="60">Year</th>
                        <th width="100">Buyer</th>
                        <th width="60">SC/LC</th>
                        <th width="110">SC/LC No.</th>
                        <th width="75">Convertible Type</th>
                        <th width="80">SC/LC Date</th>
                        <th width="80">Insert Date</th>
                        <th width="100">Delayed Days</th>
                        <th width="110">SC Value(Direct)</th>
                        <th width="110">LC Value(Direct)</th>
                        <th>Lien Bank</th>
                    </tr>
                </thead>
	                <tbody>
	                <?

					$sql="select b.lc_sc_id, sum(c.current_invoice_value) as current_invoice_value , 1 as lc_sc_type
					from com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c 
					where b.is_lc=1 and b.id=c.mst_id 
					and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.benificiary_id = $cbo_company_name
					group by b.lc_sc_id
					union all
					select b.lc_sc_id, sum(c.current_invoice_value) as current_invoice_value , 2 as lc_sc_type
					from com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c 
					where b.is_lc=2 and b.id=c.mst_id  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.benificiary_id = $cbo_company_name
					group by b.lc_sc_id";
					//echo $sql;
					$doc_inv_sc_res = sql_select($sql);
					foreach ($doc_inv_sc_res as $val) 
					{
						$doc_inv_sc_arr[$val[csf("lc_sc_id")]][$val[csf("lc_sc_type")]] += $val[csf("current_invoice_value")];
					}
					unset($doc_inv_sc_res);
					
					
					
					if($db_type == 0)
					{
						$upcharge_select = "IFNULL(upcharge, 0)";
					}else{
						$upcharge_select = "nvl(upcharge,0)";
					}
					
					$gross_sql =  sql_select("select id, is_lc, lc_sc_id, net_invo_value, (discount_ammount+bonus_ammount+claim_ammount+commission ) - $upcharge_select as deduct_amnt 
					from com_export_invoice_ship_mst where status_active=1 and is_deleted=0");

					foreach ($gross_sql as $val2) 
					{
						$net_amount_arr[$val2[csf("lc_sc_id")]][$val2[csf("is_lc")]]["net_invo_value"] += $val2[csf("net_invo_value")];
						$net_amount_arr[$val2[csf("lc_sc_id")]][$val2[csf("is_lc")]]["deduct_amnt"] += $val2[csf("deduct_amnt")];
					}
					unset($gross_sql);

					
					
					
					$i= 1; $k=1; $item_group_array=array();$year=array();
										
					$sql_attach = "select ci.com_export_lc_id, sum( wm.total_set_qnty*ci.attached_qnty) as attached_qnty FROM wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci WHERE wb.job_no_mst = wm.job_no AND wb.id = ci.wo_po_break_down_id AND ci.com_export_lc_id in($com_ex_lc_ids_con) AND ci.status_active = 1 AND ci.is_deleted = 0 group by ci.com_export_lc_id";
					$sql_attach_res = sql_select($sql_attach);
					$attach_qty_arr=array();
					foreach ($sql_attach_res as $value)
					{	
						$attach_qty_arr[$value[csf("com_export_lc_id")]] = $value[csf("attached_qnty")];
					}

					$grand_total_file_value=0;
					$grand_total_balance=0;
					$grand_sc_value_1_3=0;
					$grand_lc_value_1=0;
					$grand_sc_value_2=0;
					$grand_lc_value_0_1=0;
					$grand_gross_value=0;
					$grand_net_value=0;
					$tot_attach_qty=0;
					$grand_total_attach_qty=0;

					foreach($sql_re as $row_result)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$attach_qty=$attach_qty_arr[$row_result[csf("id")]];

						 
						$delayed = datediff('d',$row_result[csf('lc_sc_date')],$row_result[csf('insert_date')])-1;
						 
						 $delayed =$row_result[csf('date_diff')];
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td align="center"><? echo $i;?></td>
	                        <td><p><? echo  $row_result[csf('internal_file_no')];?></p></td>
                            <td><p><? echo $row_result[csf('lc_sc_year')];?></p></td>
	                        <td><p><? echo $buyer_arr[$row_result[csf('buyer_name')]]; ?></p></td>
	                        
	                        <td align="center"><p><? if($row_result[csf('type')] == 1) echo "LC"; else echo "SC"; ?></p></td>
	                        <td><p><? echo $row_result[csf('lc_sc')];?></p></td>
	                        <td align="center"><p><? echo $convertible_to_lc[$row_result[csf('convertible_to_lc')]];?></p></td>
	                        <td align="center"><p><?  if($row_result[csf('lc_sc_date')]!="0000-00-00") echo change_date_format( $row_result[csf('lc_sc_date')]);?></p></td>
	                        <td align="center"><p><? echo change_date_format( $row_result[csf('insert_date')]);?></p></td>
	                        <td align="center"><p><? echo ($delayed>1) ? $delayed." days" : $delayed." day";?></p></td>
	                        <td align="right"><p><? if($row_result[csf('type')] == 2 && $row_result[csf('convertible_to_lc')] ==2 && $row_result[csf('converted_from')]<1){$sc_2=$row_result[csf('lc_sc_value')]; echo number_format($sc_2 ,2); }?></p></td>
	                        <td align="right"><p><? if($row_result[csf('replacement_lc')] == 2 && $row_result[csf('type')] == 1 ){ $lc_0_1=$row_result[csf('lc_sc_value')] ; echo number_format($lc_0_1,2);} ?></p></td>
	                        
                            
                            <td><p><? echo $bank_arr[$row_result[csf('lien_bank')]]; ?></p>
                            
                            
	                    </tr>
						<?
						
	                    $i++;
;
						if($row_result[csf('type')] == 2 && $row_result[csf('convertible_to_lc')] !=2) $sc_value_1_3 += $row_result[csf('lc_sc_value')];
	                    if(($row_result[csf('replacement_lc')] == 1 && $row_result[csf('type')] == 1 ) || ($row_result[csf('type')] == 2 && $row_result[csf('convertible_to_lc')] ==2 && $row_result[csf('converted_from')]>0) )$lc_value_1 += $row_result[csf('lc_sc_value')]; 
	                     
	                    $balance_1_3_1=$sc_value_1_3-$lc_value_1;
	                    //$grand_total_balance +=$balance_1_3_1;
	                    if($row_result[csf('type')] == 2 && $row_result[csf('convertible_to_lc')] ==2 && $row_result[csf('converted_from')]<1) $sc_value_2 += $row_result[csf('lc_sc_value')];
	                    if($row_result[csf('replacement_lc')] == 2 && $row_result[csf('type')] == 1 )$lc_value_0_1 += $row_result[csf('lc_sc_value')];
	                    $file_value=($lc_value_1+ $balance_1_3_1+$sc_value_2+$lc_value_0_1);

	                    $total_gross_value += $gross_value;
	                    $total_net_value += $net_value;
	                    $grand_gross_value += $gross_value;
	                    $grand_net_value += $net_value;
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
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=26 and b.mail_user_setup_id=c.id and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and a.company_id =".$company_id."";	
	$mail_sql2=sql_select($sql2);
	foreach($mail_sql2 as $row)
	{
		if ($to=="") {
			$to=$row[csf('email_address')];
		}  else{
			 $to=$to.",".$row[csf('email_address')];
		}

	}

	$subject="Export LC/Sales Contract Report";
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
