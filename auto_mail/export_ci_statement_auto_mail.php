<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');
extract($_REQUEST);
	

	echo load_html_head_contents("Mail Notification", "../", 1, 1,'','','');


	if($db_type==0)
	{
		$previous_date= date('Y-m-d', strtotime("first day of -1 month"));
		$current_date = date('Y-m-d', strtotime("last day of -1 month"));
	}
	else
	{
		$previous_date= date('d-M-Y', strtotime("first day of -1 month"));
		$current_date = date('d-M-Y', strtotime("last day of -1 month"));
	}
	

	$company_arr=return_library_array( "select id, company_name from lib_company where is_deleted=0 ",'id','company_name');
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");




foreach($company_arr as $company_id=>$company_name)
{
		//echo $previous_date.'==='.$current_date;
	
		$txt_date_from=str_replace("'","",$previous_date);
		$txt_date_to=str_replace("'","",$current_date);

		$date_con=" and c.RECEIVED_DATE between '$txt_date_from' and  '$txt_date_to'";
		
		$sub_sql="SELECT b.INVOICE_ID, a.SUBMIT_DATE, a.BANK_REF_NO, c.RECEIVED_DATE 
		from com_export_doc_submission_mst a, com_export_doc_submission_invo b
		left join com_export_proceed_realization c on b.doc_submission_mst_id=c.invoice_bill_id 
		where a.id=b.doc_submission_mst_id and a.company_id=$company_id and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_con";
		//echo $sub_sql;die;
		$sub_sql_result=sql_select($sub_sql);
		$bank_sub_data=array();$invDataARr=array();
		foreach($sub_sql_result as $row)
		{
			$bank_sub_data[$row["INVOICE_ID"]]["SUBMIT_DATE"]=$row["SUBMIT_DATE"];
			$bank_sub_data[$row["INVOICE_ID"]]["BANK_REF_NO"]=$row["BANK_REF_NO"];
			$bank_sub_data[$row["INVOICE_ID"]]["RECEIVED_DATE"]=$row["RECEIVED_DATE"];
			$invDataARr[$row["INVOICE_ID"]]=$row["INVOICE_ID"];
		}
		//echo "<pre>"; print_r($bank_sub_data);die;
		unset($sub_sql_result);
		$order_sql="SELECT b.ID as PO_ID, b.PO_NUMBER, c.GMTS_ITEM_ID 
		from wo_po_break_down b, wo_po_details_master c 
		where b.job_no_mst=c.job_no and c.company_name=$company_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		$order_sql_result=sql_select($order_sql);
		$order_data_arr=array();
		foreach($order_sql_result as $row)
		{
			$order_data_arr[$row["PO_ID"]]["PO_NUMBER"]=$row["PO_NUMBER"];
			$gmt_item_arr=explode(",",$row["GMTS_ITEM_ID"]);
			foreach($gmt_item_arr as $gmt_id)
			{
				$order_data_arr[$row["PO_ID"]]["GMTS_ITEM"].=$garments_item[$gmt_id].",";
			}
		}
		unset($order_sql_result);
		
		
		if($company_id!=0) $company_id = $company_id; else $company_id="%%";
		if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;
		if($cbo_lien_bank == 0) $cbo_lien_bank="%%"; else $cbo_lien_bank = $cbo_lien_bank;
		if($cbo_location == 0) $cbo_location="%%"; else $cbo_location = $cbo_location;
		if($cbo_based_on == 0) $cbo_based_on="%%"; else $cbo_based_on = $cbo_based_on;
		if(trim($txt_date_from)!= "") $txt_date_from  =$txt_date_from;
		if(trim($txt_date_to)!= "") $txt_date_to = $txt_date_to;
		if($forwarder_name!=0) $forwarder_cond=" and a.forwarder_name='$forwarder_name'"; else $forwarder_cond="";
		if($txt_invoice_no!="") $invoice_cond=" and a.invoice_no like '%$txt_invoice_no'"; else $invoice_cond="";

		if($cbo_ascending_by!="" && $cbo_ascending_by==1 ) 
		{
			$ascending_by = "invoice_no";
			$ascendig_cond=" order by $ascending_by asc ";
		} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==2) {
			$ascending_by = "invoice_date";
			$ascendig_cond=" order by $ascending_by asc ";
		} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==3) {
			$ascending_by = "exp_form_no";
			$ascendig_cond=" order by $ascending_by asc ";
		}elseif ($cbo_ascending_by!="" && $cbo_ascending_by==4) {
			$ascending_by = "exp_form_date";
			$ascendig_cond=" order by $ascending_by asc ";
		}else{
			$ascendig_cond="";
		}
		if ($txt_date_from!="" && $txt_date_to!="")
		{
			//$str_cond=" and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to'";
		}
		else
		{
			$str_cond="";
		}
		
		
		$shipping_mode=str_replace("'","",$shipping_mode);
		$ship_cond="";
		if($shipping_mode!=0) $ship_cond=" and a.shipping_mode=$shipping_mode";

		$txt_lc_sc_no=str_replace("'","",$txt_lc_sc_no);
		//echo  $str_cond;die;
		$sql="SELECT c.po_breakdown_id, a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.remarks, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, a.freight_amnt_by_supllier, a.insentive_applicable, b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.tenor, 1 as type
		FROM com_export_invoice_ship_dtls c, com_export_invoice_ship_mst a, com_export_lc b
		WHERE a.lc_sc_id=b.id and c.mst_id=a.id and a.is_lc=1 and a.benificiary_id LIKE '$company_id' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%'  $str_cond $forwarder_cond $ship_cond $invoice_cond $all_submitted_invoice_cond ".where_con_using_array($invDataARr,0,'a.id')."
		UNION ALL
		SELECT c.po_breakdown_id, a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.remarks, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, a.freight_amnt_by_supllier, a.insentive_applicable, b.pay_term as pay_term, b.lien_bank as lien_bank, b.contract_no as lc_sc_no, b.tenor, 2 as type
		FROM com_export_invoice_ship_dtls c, com_export_invoice_ship_mst a, com_sales_contract b
		WHERE a.lc_sc_id=b.id and c.mst_id=a.id and a.is_lc=2 and a.benificiary_id LIKE '$company_id' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND b.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ascendig_cond $all_submitted_invoice_cond  ".where_con_using_array($invDataARr,0,'a.id')."
		order by buyer_id, invoice_no";
		//echo $sql;die;
		$sql_re=sql_select($sql);
		$inv_data=array();$buyer_wise_data=array();
		foreach($sql_re as $row)
		{
			if($orde_inv_check[$row[csf('id')]][$row[csf('po_breakdown_id')]]=="")
			{
				$orde_inv_check[$row[csf('id')]][$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
				$inv_data[$row[csf('id')]]['po_breakdown_id'] .= $row[csf('po_breakdown_id')].",";
				$inv_data[$row[csf('id')]]['po_number'] .= $order_data_arr[$row[csf('po_breakdown_id')]]["PO_NUMBER"].",";
				$inv_data[$row[csf('id')]]['gmts_item'] .= chop($order_data_arr[$row[csf('po_breakdown_id')]]["GMTS_ITEM"],",").",";
			}
			$inv_data[$row[csf('id')]]['id'] = $row[csf('id')];
			$inv_data[$row[csf('id')]]['benificiary_id'] = $row[csf('benificiary_id')];
			$inv_data[$row[csf('id')]]['location_id'] = $row[csf('location_id')];
			$inv_data[$row[csf('id')]]['invoice_no'] = $row[csf('invoice_no')];
			$inv_data[$row[csf('id')]]['invoice_date'] = $row[csf('invoice_date')];
			$inv_data[$row[csf('id')]]['ex_factory_date'] = $row[csf('ex_factory_date')];
			$inv_data[$row[csf('id')]]['is_lc'] = $row[csf('is_lc')];
			$inv_data[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
			$inv_data[$row[csf('id')]]['exp_form_no'] = $row[csf('exp_form_no')];
			$inv_data[$row[csf('id')]]['exp_form_date'] = $row[csf('exp_form_date')];
			$inv_data[$row[csf('id')]]['invoice_value'] = $row[csf('invoice_value')];
			$inv_data[$row[csf('id')]]['net_invo_value'] = $row[csf('net_invo_value')];
			$inv_data[$row[csf('id')]]['invoice_quantity'] = $row[csf('invoice_quantity')];
			$inv_data[$row[csf('id')]]['total_carton_qnty'] = $row[csf('total_carton_qnty')];
			$inv_data[$row[csf('id')]]['actual_shipment_date'] = $row[csf('actual_shipment_date')];
			$inv_data[$row[csf('id')]]['shipping_bill_n'] = $row[csf('shipping_bill_n')];
			$inv_data[$row[csf('id')]]['ship_bl_date'] = $row[csf('ship_bl_date')];
			$inv_data[$row[csf('id')]]['bl_no'] = $row[csf('bl_no')];
			$inv_data[$row[csf('id')]]['bl_date'] = $row[csf('bl_date')];
			$inv_data[$row[csf('id')]]['remarks'] = $row[csf('remarks')];
			$inv_data[$row[csf('id')]]['shipping_mode'] = $row[csf('shipping_mode')];
			$inv_data[$row[csf('id')]]['carton_net_weight'] = $row[csf('carton_net_weight')];
			$inv_data[$row[csf('id')]]['carton_gross_weight'] = $row[csf('carton_gross_weight')];
			$inv_data[$row[csf('id')]]['freight_amnt_by_supllier'] = $row[csf('freight_amnt_by_supllier')];
			$inv_data[$row[csf('id')]]['insentive_applicable'] = $row[csf('insentive_applicable')];			
			$inv_data[$row[csf('id')]]['pay_term'] = $row[csf('pay_term')];
			$inv_data[$row[csf('id')]]['lien_bank'] = $row[csf('lien_bank')];
			$inv_data[$row[csf('id')]]['lc_sc_no'] = $row[csf('lc_sc_no')];
			$inv_data[$row[csf('id')]]['tenor'] = $row[csf('tenor')];
			$inv_data[$row[csf('id')]]['type'] = $row[csf('type')];
			
			$inv_data[$row[csf('id')]]['submit_date'] = $bank_sub_data[$row[csf('id')]]["SUBMIT_DATE"];
			$inv_data[$row[csf('id')]]['bank_ref_no'] = $bank_sub_data[$row[csf('id')]]["BANK_REF_NO"];
			$inv_data[$row[csf('id')]]['received_date'] = $bank_sub_data[$row[csf('id')]]["RECEIVED_DATE"];
			if($inv_check[$row[csf('id')]]=="")
			{
				$inv_check[$row[csf('id')]]=$row[csf('id')];
				$buyer_wise_data[$row[csf('buyer_id')]]['invoice_quantity']+=$row[csf('invoice_quantity')];
				$buyer_wise_data[$row[csf('buyer_id')]]['invoice_value']+=$row[csf('invoice_value')];
				$buyer_wise_data[$row[csf('buyer_id')]]['net_invo_value']+=$row[csf('net_invo_value')];
				$buyer_key_data[$row[csf('buyer_id')]]+=$row[csf('net_invo_value')];
			}
		}
		unset($sql_re);
		sort($buyer_key_data);
		ob_start();
		?>
		<div style="width:2000px"> 
			<table width="2000" cellpadding="0" cellspacing="0" id="caption">
				<tr>
					<td align="center" width="100%" colspan="20">
						<strong style="font-size:18px"><? echo " ". $company_arr[str_replace("'","",$company_id)]; ?></strong><br />
                        <strong style="font-size:13px"> Export CI Statement </strong><br />
                        <strong style="font-size:13px">Date: <? echo $previous_date.' To '.$current_date; ?></strong>
					</td>
				</tr>
				
                
			</table>
            <br />                       
            <table width="650" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_0" align="left">
                <thead>
                    <tr>
                        <th width="50">Sl</th>
                        <th width="150">Buyer</th>
                        <th width="150">Invoice Qty.</th>
                        <th width="150">Invoice Value (Gross)</th>
                        <th>Invoice Value (Net)</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				$buy_tot_invoice_quantity =0;
				$buy_tot_invoice_value =0;
				$buy_tot_net_invo_value =0;

				foreach($buyer_wise_data as $buy_id=>$buy_val)
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<td align="center" title="<?= $buy_id;?>"><? echo $i;?></td>
                        <td title="<?=$buy_id;?>"><? echo $buyer_arr[$buy_id];?></td>
                        <td align="right"><? echo number_format($buy_val["invoice_quantity"],2);?></td>
                        <td align="right"><? echo number_format($buy_val["invoice_value"],2);?></td>
                        <td align="right"><? echo number_format($buy_val["net_invo_value"],2);?></td>
                    </tr>
                    <?
					$i++;
					$buy_tot_invoice_quantity +=$buy_val["invoice_quantity"];
					$buy_tot_invoice_value +=$buy_val["invoice_value"];
					$buy_tot_net_invo_value +=$buy_val["net_invo_value"];
				}
				?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="2" align="right">Total:</th>
                        <th align="right"><? echo number_format($buy_tot_invoice_quantity,2);?></th>
                        <th align="right"><? echo number_format($buy_tot_invoice_value,2);?></th>
                        <th align="right"><? echo number_format($buy_tot_net_invo_value,2);?></th>
                    </tr>
                </tfoot>
            </table>
			<br />                       
            <table width="2000" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
                <thead>
                    <tr>
                        <th width="40">Sl</th>
                        <th width="100">Invoice No.</th>
                        <th width="70">Invoice Date</th>
                        <th width="150">Order No</th>
                        <th width="100">LC/CON. NO.</th>
                        <th width="100">Buyer Name</th>
                        <th width="100">CTN</th>
                        <th width="100">Invoice Qnty.</th>
                        <th width="100">Invoice value</th>
                        <th width="100">Realization Amount</th>
                        <th width="70">Realized Date</th>
                        <th width="80">Ship Mode</th>
                        <th width="100">FDBC</th>
                        <th width="100">EXP. NO.</th>
                        <th width="120">Item</th>
                        <th width="100">B/L No</th>
                        <th width="70">B/L Date</th>
                        <th width="80">Discount</th>
                        <th width="80">Air Freight</th>
                        <th width="80">LOC/IMP</th>
                        <th>Rmarks</th>
                    </tr>
                </thead>
    
	                <tbody>
	                <?
					$k=1;
					$total_total_carton_qnty=0;
					$total_invoice_quantity=0;
					$total_invoice_value=0;
					$total_rlz_value=0;
					$total_inv_discount=0;
					$total_freight_amnt_by_supllier=0;
					
					$insentive_applicable_arr=array(1=>"Local",2=>"Foreign");
	                foreach($inv_data as $inv_id=>$inv_data)
	                {
						if ($k%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						$inv_discount=$inv_data["invoice_value"]-$inv_data["net_invo_value"];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                    	<td><? echo $k;?></td>
                            <td style="word-break:break-all;" title="<? echo $inv_id; ?>"><p><? echo $inv_data["invoice_no"]; ?>&nbsp;</p></td>
                            <td style="word-break:break-all;"><p><? echo change_date_format($inv_data["invoice_date"]); ?>&nbsp;</p></td>
                            <td style="word-break:break-all;"><p><? echo chop($inv_data["po_number"],","); ?>&nbsp;</p></td>
                            <td style="word-break:break-all;"><p><? echo $inv_data["lc_sc_no"]; ?>&nbsp;</p></td>
                            <td style="word-break:break-all;"><p><? echo $buyer_arr[$inv_data["buyer_id"]]; ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($inv_data["total_carton_qnty"],2);?></td>
                            <td align="right"><? echo number_format($inv_data["invoice_quantity"],2);?></td>
                            <td align="right" title="<?= $inv_data["invoice_value"]; ?>"><? echo number_format($inv_data["net_invo_value"],2);?></td>
                            <td align="right"><? if($inv_data["received_date"]!="" && $inv_data["received_date"]!="0000-00-00") echo number_format($inv_data["net_invo_value"],2); else echo "0.00";?></td>
                            <td align="center"><? if($inv_data["received_date"]!="" && $inv_data["received_date"]!="0000-00-00") echo change_date_format($inv_data["received_date"]); ?></td>
                            <td style="word-break:break-all;"><p><? echo $shipment_mode[$inv_data["shipping_mode"]]; ?>&nbsp;</p></td>
                            <td style="word-break:break-all;"><p><? echo $inv_data["bank_ref_no"]; ?>&nbsp;</p></td>
                            <td style="word-break:break-all;"><p><? echo $inv_data["exp_form_no"]; ?>&nbsp;</p></td>
                            <td style="word-break:break-all;"><p><? echo implode(",",array_unique(explode(",",chop($inv_data["gmts_item"],",")))); ?>&nbsp;</p></td>
                            <td style="word-break:break-all;"><p><? echo $inv_data["bl_no"]; ?>&nbsp;</p></td>
                            <td align="center"><? if($inv_data["bl_date"]!="" && $inv_data["bl_date"]!="0000-00-00") echo change_date_format($inv_data["bl_date"]); ?></td>
                            <td align="right"><? echo number_format($inv_discount,2);?></td>
                            <td align="right"><? echo number_format($inv_data["freight_amnt_by_supllier"],2);?></td>
                            <td style="word-break:break-all;"><p><? echo $insentive_applicable_arr[$inv_data["insentive_applicable"]]; ?>&nbsp;</p></td>
                            <td style="word-break:break-all;"><p><? echo $inv_data["remarks"]; ?>&nbsp;</p></td>
						</tr>
						<?
						$k++;
						$total_total_carton_qnty+=$inv_data["total_carton_qnty"];
						$total_invoice_quantity+=$inv_data["invoice_quantity"];
						$total_invoice_value+=$inv_data["net_invo_value"];
						if($inv_data["received_date"]!="" && $inv_data["received_date"]!="0000-00-00")
						{
							$total_rlz_value+=$inv_data["net_invo_value"];
						}
						$total_inv_discount+=$inv_discount;
						$total_freight_amnt_by_supllier+=$inv_data["freight_amnt_by_supllier"];
	                }
	                ?>
	                </tbody>

	            	<tfoot>
	                    <tr>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th lign="right">Total</th>
                            <th align="right"><? echo number_format($total_total_carton_qnty,2);?></th>
                            <th align="right"><? echo number_format($total_invoice_quantity,2);?></th>
                            <th align="right"><? echo number_format($total_invoice_value,2);?></th>
                            <th align="right"><? echo number_format($total_rlz_value,2);?></th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th><? echo number_format($total_inv_discount,2);?></th>
                            <th><? echo number_format($total_freight_amnt_by_supllier,2);?></th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
	                    </tr>
	                </tfoot>
	            </table>
    	</div>
        
        <?
		$message=ob_get_contents();
		ob_clean();
 		//echo $message;


	$mail_item=45;
	$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=45 and b.mail_user_setup_id=c.id and a.company_id =".$company_id." and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	  //echo $sql;die;
	
	
	$mail_sql=sql_select($sql);
	$receverMailArr=array();
	foreach($mail_sql as $row)
	{
		$receverMailArr[$row[csf('email_address')]]=$row[csf('email_address')];
	}

	$to=implode(',',$receverMailArr);
	
	
	$subject="Export CI Statement";
	$header=mailHeader();
	//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail);}
	if($_REQUEST['isview']==1){
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail);}
	}

		

	

}

	exit();
?>
