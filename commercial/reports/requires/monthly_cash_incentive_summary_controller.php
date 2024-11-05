<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header('location:login.php');
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0 );
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $company_id = str_replace("'","",$cbo_company_name);  
    $buyer_id   = str_replace("'","",$cbo_buyer_id);   
    $year_from  = str_replace("'","",$cbo_year_from);    
    $month_from = str_replace("'","",$cbo_month_from); 
    $year_to    = str_replace("'","",$cbo_year_to);    
    $month_to   = str_replace("'","",$cbo_month_to); 

	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$companyArr=return_library_array( "select id, company_name from lib_company", "id", "company_name");

	if($company_id!=0) 
	{
		$company_cond=" and a.benificiary_id=$company_id";
		$company_cond2=" and a.company_id=$company_id";
	}

	if($buyer_id){ $buyer_id_cond=" and a.buyer_id=$buyer_id"; }

	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $month_to, $year_to);
	$start_date=$year_from."-".$month_from."-"."01";
	$end_date=$year_to."-".$month_to."-".$daysinmonth;
	
	if($db_type==0)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd');
		$end_date=change_date_format($end_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$start_date=change_date_format($start_date,'','',-1);
		$end_date=change_date_format($end_date,'','',-1);
	}

	$date_cond=" and a.ex_factory_date between '$start_date' and '$end_date'";
	$date_cond2=" and e.submission_date between '$start_date' and '$end_date'";
	$date_cond3=" and g.received_date between '$start_date' and '$end_date'";

	$invoice_sql="SELECT a.id as INVOICE_ID,a.BUYER_ID, a.INVOICE_VALUE, a.NET_INVO_VALUE, sum(b.current_invoice_qnty*d.total_set_qnty) as INVOICE_QNTY_PCS 
	from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b, wo_po_break_down c, wo_po_details_master d 
	where a.id=b.mst_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 $company_cond $buyer_id_cond $date_cond 
	group by a.id,a.buyer_id, a.invoice_value, a.net_invo_value";
	// echo $invoice_sql;die;
	$invoice_data=sql_select($invoice_sql);
	$invoice_id_arr=array();
	foreach($invoice_data as $row)
	{
		$invoice_result[$row["BUYER_ID"]]["INVOICE_QNTY_PCS"]+=$row["INVOICE_QNTY_PCS"];
		if(!in_array($row["INVOICE_ID"],$invoice_id_arr))
		{
			$invoice_id_arr[$row["INVOICE_ID"]]=$row["INVOICE_ID"];
			$invoice_result[$row["BUYER_ID"]]["INVOICE_VALUE"]+=$row["INVOICE_VALUE"];
			$invoice_result[$row["BUYER_ID"]]["NET_INVO_VALUE"]+=$row["NET_INVO_VALUE"];
		}
	}
	unset($invoice_data);

	$invoice_in=where_con_using_array($invoice_id_arr,0,'a.invoice_no');
	if(count($invoice_id_arr)>0)
	{
		$exFactory_sql="SELECT c.buyer_name, sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as EX_FACTORY_QNTY,
		sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as EX_FACTORY_RETURN_QNTY 
		from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c 
		where a.po_break_down_id=b.id and b.job_id=c.id and a.status_active=1 and b.status_active=1 $invoice_in group by c.buyer_name";
		// echo $exFactory_sql;die;
		$exFactory_result=sql_select($exFactory_sql);
		foreach($exFactory_result as $row)
		{
			$invoice_result[$row['BUYER_NAME']]["EX_FACTORY_QNTY"]=$row['EX_FACTORY_QNTY']-$row['EX_FACTORY_RETURN_QNTY'];
		}
		unset($exFactory_result);
	}


	$realization_sql="SELECT a.id as INVOICE_ID, a.BUYER_ID, a.NET_INVO_VALUE, b.id as INVOICE_DTLS_ID, b.current_invoice_qnty*d.total_set_qnty as INVOICE_QNTY_PCS, f.id as SUB_DTLS_ID, f.RLZ_VALUE, f.SPECIAL_INCENTIVE, f.EURO_ZONE_INCENTIVE, f.GENERAL_INCENTIVE, f.MARKET_INCENTIVE, g.id as RLZ_RCV_ID  
	from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b, wo_po_break_down c, wo_po_details_master d, cash_incentive_submission e, cash_incentive_submission_dtls f,cash_incentive_received_mst g
	where a.id=b.mst_id and b.po_breakdown_id=c.id and c.job_id=d.id and a.id=f.submission_bill_id and e.id=f.mst_id and f.mst_id=g.cash_incentive_sub_id and e.entry_form=566 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 $company_cond $buyer_id_cond $date_cond3 ";
	// echo $realization_sql;
	$realization_result=sql_select($realization_sql);
	$realization_data=$rlz_invoice_id_arr=$rlz_invoice_dtls_id_arr=$rlz_sub_arr=array();
	foreach($realization_result as $row)
	{
		if($row['RLZ_RCV_ID'])
		{
			$realization_data[$row['BUYER_ID']]['RLZ_RCV_ID'].=$row['RLZ_RCV_ID'].',';
			$incentive_recv_id_arr[$row['RLZ_RCV_ID']]=$row['RLZ_RCV_ID'];
		}
		
		if(!in_array($row["INVOICE_ID"],$rlz_invoice_id_arr))
		{
			$rlz_invoice_id_arr[$row["INVOICE_ID"]]=$row["INVOICE_ID"];
			$realization_data[$row["BUYER_ID"]]["NET_INVO_VALUE"]+=$row["NET_INVO_VALUE"];
			$realization_data[$row["BUYER_ID"]]["RLZ_VALUE"]+=$row["RLZ_VALUE"];
		}

		if(!in_array($row["INVOICE_DTLS_ID"],$rlz_invoice_dtls_id_arr))
		{
			$rlz_invoice_dtls_id_arr[$row["INVOICE_DTLS_ID"]]=$row["INVOICE_DTLS_ID"];
			$realization_data[$row["BUYER_ID"]]["INVOICE_QNTY_PCS"]+=$row["INVOICE_QNTY_PCS"];
		}

		if(!in_array($row["SUB_DTLS_ID"],$rlz_sub_arr))
		{
			$rlz_sub_arr[$row["SUB_DTLS_ID"]]=$row["SUB_DTLS_ID"];
			$realization_data[$row["BUYER_ID"]]["CASH_CLAIM"]+=$row["SPECIAL_INCENTIVE"]+$row["EURO_ZONE_INCENTIVE"]+$row["GENERAL_INCENTIVE"]+$row["MARKET_INCENTIVE"];
		}		
	}
	unset($realization_result);

	if(count($incentive_recv_id_arr)>0)
	{
		$incentive_recv_id_in=where_con_using_array($incentive_recv_id_arr,0,'b.mst_id');
		$incentive_recv_sql="SELECT b.mst_id, sum(b.document_currency) as DOCUMENT_CURRENCY, sum(b.domestic_currency) as DOMESTIC_CURRENCY, b.account_head_id as ACCOUNT_HEAD_ID from cash_incentive_received_dtls b where b.status_active=1 $incentive_recv_id_in group by b.mst_id,b.account_head_id";  
		// echo $incentive_recv_sql;
		$incentive_recv_sql_result=sql_select($incentive_recv_sql);
		$incentive_recv_info=array();$incentive_recv_acnt_head=array();$account_head_arr=array();
		foreach($incentive_recv_sql_result as $row)
		{
			$incentive_recv_info[$row['MST_ID']]+=$row['DOCUMENT_CURRENCY'];
			$incentive_recv_acnt_head[$row['MST_ID']][$row['ACCOUNT_HEAD_ID']]+=$row['DOMESTIC_CURRENCY'];
			$account_head_arr[$row['ACCOUNT_HEAD_ID']]=$row['ACCOUNT_HEAD_ID'];
		}
		unset($incentive_recv_sql_result);
	}


	// and e.id not in ( select cash_incentive_sub_id from cash_incentive_received_mst where COMPANY_ID=$company_id and status_active=1 )
	$unrealization_sql="SELECT a.id as INVOICE_ID, a.BUYER_ID, a.NET_INVO_VALUE, b.id as INVOICE_DTLS_ID, b.current_invoice_qnty*d.total_set_qnty as INVOICE_QNTY_PCS, f.id as SUB_DTLS_ID, f.RLZ_VALUE as RLZ_VALUE
	from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b, wo_po_break_down c, wo_po_details_master d, cash_incentive_submission e, cash_incentive_submission_dtls f
	where a.id=b.mst_id and b.po_breakdown_id=c.id and c.job_id=d.id and a.id=f.submission_bill_id and e.id=f.mst_id  and e.entry_form=566 and e.submission_date <='$end_date' and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 $company_cond $buyer_id_cond ";
	// echo $unrealization_sql;
	$unrealization_result=sql_select($unrealization_sql);
	$unrealization_data=$rlz_invoice_id_arr2=$rlz_invoice_dtls_id_arr2=$rlz_sub_arr2=array();
	foreach($unrealization_result as $row)
	{
		if(!in_array($row["INVOICE_ID"],$rlz_invoice_id_arr2))
		{
			$rlz_invoice_id_arr2[$row["INVOICE_ID"]]=$row["INVOICE_ID"];
			$unrealization_data[$row["BUYER_ID"]]["NET_INVO_VALUE"]+=$row["NET_INVO_VALUE"];
			$unrealization_data[$row["BUYER_ID"]]["RLZ_VALUE"]+=$row["RLZ_VALUE"];
		}

		if(!in_array($row["INVOICE_DTLS_ID"],$rlz_invoice_dtls_id_arr2))
		{
			$rlz_invoice_dtls_id_arr2[$row["INVOICE_DTLS_ID"]]=$row["INVOICE_DTLS_ID"];
			$unrealization_data[$row["BUYER_ID"]]["INVOICE_QNTY_PCS"]+=$row["INVOICE_QNTY_PCS"];
		}

		/* if(!in_array($row["REALIZATION_ID"],$rlz_sub_arr2))
		{
			$rlz_sub_arr2[$row["REALIZATION_ID"]]=$row["REALIZATION_ID"];
			$unrealization_data[$row["BUYER_ID"]]["RLZ_VALUE"]+=$row["RLZ_VALUE"];
		} */
	} 
	unset($unrealization_result);

	$all_clam_sql="SELECT a.BUYER_ID, sum(f.SPECIAL_INCENTIVE) as SPECIAL_INCENTIVE, sum(f.EURO_ZONE_INCENTIVE) as EURO_ZONE_INCENTIVE, sum(f.GENERAL_INCENTIVE) as GENERAL_INCENTIVE, sum(f.MARKET_INCENTIVE) as MARKET_INCENTIVE
	from com_export_invoice_ship_mst a, cash_incentive_submission e, cash_incentive_submission_dtls f
	where a.id=f.submission_bill_id and e.id=f.mst_id and e.entry_form=566 and e.submission_date <='$end_date' and a.status_active=1 and e.status_active=1 and f.status_active=1 $company_cond $buyer_id_cond 
	group by a.buyer_id";
	// echo $unrealization_sql;die;
	$all_clam_result=sql_select($all_clam_sql);
	foreach($all_clam_result as $row)
	{
		$unrealization_data[$row["BUYER_ID"]]["CASH_CLAIM"]+=$row["SPECIAL_INCENTIVE"]+$row["EURO_ZONE_INCENTIVE"]+$row["GENERAL_INCENTIVE"]+$row["MARKET_INCENTIVE"];
	} 
	unset($all_clam_result);

	$all_rcv_sql="SELECT a.BUYER_ID, h.id, h.document_currency
	from com_export_invoice_ship_mst a, cash_incentive_submission e, cash_incentive_submission_dtls f, cash_incentive_received_mst g, cash_incentive_received_dtls h
	where a.id=f.submission_bill_id and e.id=f.mst_id and e.id=g.cash_incentive_sub_id and g.id=h.mst_id and e.entry_form=566 and e.submission_date <='$end_date' and a.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 and h.status_active=1 $company_cond $buyer_id_cond 
	group by a.buyer_id, h.id, h.document_currency";
	// echo $all_rcv_sql;die;
	$all_rcv_result=sql_select($all_rcv_sql);
	foreach($all_rcv_result as $row)
	{
		$unrealization_data[$row["BUYER_ID"]]["CASH_RCV"]+=$row["DOCUMENT_CURRENCY"];
	} 
	unset($all_rcv_result);

	if($db_type==0)
	{
		$lib_currency_data=sql_select("SELECT conversion_rate from currency_conversion_rate where currency=2 order by id desc limit 1");
	}
	else
	{
		$lib_currency_data=sql_select("SELECT conversion_rate from currency_conversion_rate where currency=2 and rownum<2 order by id desc");
	}
	$currency_conversion_rate=$lib_currency_data[0]["CONVERSION_RATE"];

	$unrealization_claim_sql="SELECT a.BUYER_ID, e.POSSIBLE_REALI_DATE, sum(f.SPECIAL_INCENTIVE) as SPECIAL_INCENTIVE, sum(f.EURO_ZONE_INCENTIVE) as EURO_ZONE_INCENTIVE, sum(f.GENERAL_INCENTIVE) as GENERAL_INCENTIVE, sum(f.MARKET_INCENTIVE) as MARKET_INCENTIVE
	from com_export_invoice_ship_mst a, cash_incentive_submission e, cash_incentive_submission_dtls f
	where a.id=f.submission_bill_id and e.id=f.mst_id  and e.entry_form=566 and e.submission_date <='$end_date' and e.id not in ( select cash_incentive_sub_id from cash_incentive_received_mst where COMPANY_ID=$company_id and status_active=1 ) and e.POSSIBLE_REALI_DATE is not null and a.status_active=1 and e.status_active=1 and f.status_active=1 $company_cond $buyer_id_cond group by a.BUYER_ID, e.POSSIBLE_REALI_DATE order by e.POSSIBLE_REALI_DATE";
	// echo $unrealization_claim_sql;die;
	$unrealization_claim_result=sql_select($unrealization_claim_sql);
	$psbl_date_arr=array();
	foreach($unrealization_claim_result as $row)
	{
		$psbl_date=date("M-Y",strtotime($row["POSSIBLE_REALI_DATE"]));
		$psbl_date_arr[$psbl_date]=$psbl_date;
		$unrealization_claim_data[$row["BUYER_ID"]][$psbl_date]["CASH_CLAIM"]+=$row["SPECIAL_INCENTIVE"]+$row["EURO_ZONE_INCENTIVE"]+$row["GENERAL_INCENTIVE"]+$row["MARKET_INCENTIVE"];

		$unrealization_data[$row["BUYER_ID"]]["UNRLZ"]+=$row["SPECIAL_INCENTIVE"]+$row["EURO_ZONE_INCENTIVE"]+$row["GENERAL_INCENTIVE"]+$row["MARKET_INCENTIVE"];
	} 
	unset($unrealization_claim_result);

	$tbl_width=900+count($psbl_date_arr)*80;
	ob_start();
    ?>

        <table border="0" width="730" cellpadding="0" cellspacing="0" >
			<tr>
				<td colspan="6" align="center" ><strong style="font-size: 20px;"><?=$companyArr[$company_id];?></strong></td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size: 20px;">Monthly Cash Incentive Summary</td>
			</tr>
		</table>
        <table class="rpt_table" border="1" rules="all" width="730" cellpadding="0" cellspacing="0" id="table_body_1">
			<thead>
				<tr>
					<th colspan="6" >Shipment for the Month of <?=$months[$month_from].'-'.$year[$year_from].' - '.$months[$month_to].'-'.$year[$year_to];?></th>
				</tr>				
				<tr>
					<th width="30">SL</th>
					<th width="200">Buyer</th>
                    <th width="100">Invoice Qty. Pcs</th>
                    <th width="100">Shipment Qty. Pcs</th>
                    <th width="150">Invoice Value (Gross) (USD)</th>
                    <th >Invoice Value (Net) (USD)</th>
				</tr>
			</thead>
			<tbody >
				<?
					$i=1;
					foreach ($invoice_result as $key=>$val)
					{
						if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
						?>
						<tr bgcolor="<?= $bgcolor; ?>" onclick="change_color('tbl1_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tbl1_<?=$i;?>">
							<td align="center"><?= $i++; ?></td>
							<td ><?= $buyerArr[$key]; ?></td>
							<td align="right"><?= number_format($val["INVOICE_QNTY_PCS"],2); ?></td>
							<td align="right"><?= number_format($val["EX_FACTORY_QNTY"],2); ?></td>
							<td align="right"><?= number_format($val['INVOICE_VALUE'],2); ?></td>
							<td align="right"><?= number_format($val['NET_INVO_VALUE'],2); ?></td>
						</tr>
						<?
						$tot_exFactory_qnty+=$val["EX_FACTORY_QNTY"];
						$tot_invoice_qnty_pcs+=$val["INVOICE_QNTY_PCS"];
						$tot_invoice_value+=$val['INVOICE_VALUE'];
						$tot_net_invoice_value+=$val['NET_INVO_VALUE'];
					}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th>Total:</th>
					<th><?=number_format($tot_invoice_qnty_pcs,2); ?></th>
					<th><?=number_format($tot_exFactory_qnty,2); ?></th>
					<th><?=number_format($tot_invoice_value,2); ?></th>
					<th><?=number_format($tot_net_invoice_value,2); ?></th>
				</tr>
			</tfoot>
		</table>
		<br>

        <table class="rpt_table" border="1" rules="all" width="<?=(count($account_head_arr)*80)+650;?>" cellpadding="0" cellspacing="0" id="table_body_2">
			<thead>
				<tr>
					<th colspan="<?=count($account_head_arr)+7;?>" >Cash Incentive Realization Statement</th>
				</tr>				
				<tr>
					<th colspan="<?=count($account_head_arr)+7;?>" >Month of <?=$months[$month_from].'-'.$year[$year_from].' - '.$months[$month_to].'-'.$year[$year_to];?></th>
				</tr>	
				<tr>
					<th width="30">SL</th>
					<th width="120">Buyer</th>
                    <th width="80">Invoice Quantity (Pcs)</th>
                    <th width="100">Total Net Invoice Value ($)</th>
                    <th width="100">Total Net Realized Value ($)</th>
                    <th width="100">Total Cash Incentive Value Claimed (USD)</th>
                    <th >Cash Incentive Value Realized (USD)</th>
					<?
					foreach($account_head_arr as $val)
					{
						?><th width="80"><?=$commercial_head[$val];?>  (BDT)</th><?
					}
					?>
				</tr>
			</thead>
			<tbody >
				<?
					$i=1;
					foreach ($realization_data as $key=>$row)
					{
						if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
						
						$rlz_rcv_id_arr=array_unique(explode(",",chop($row["RLZ_RCV_ID"],',')));
						$tot_acc_rcv_amount=0;
						foreach($rlz_rcv_id_arr as $rcv_id)
						{
							$tot_acc_rcv_amount+=$incentive_recv_info[$rcv_id];
						}
						?>
						<tr bgcolor="<?= $bgcolor; ?>" onclick="change_color('tbl2_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tbl2_<?=$i;?>">
							<td align="center"><?=$i++; ?></td>
							<td ><?= $buyerArr[$key]; ?></td>
							<td align="right"><?=number_format($row["INVOICE_QNTY_PCS"],2); ?></td>
							<td align="right"><a href="#" onclick="fn_rlz_open_details('<?=$key;?>','<?=$company_id;?>','<?=$start_date;?>','<?=$end_date;?>','rlz_unrlz_details','Cash Incentive Realization Statement','630','1');"><?=number_format($row["NET_INVO_VALUE"],2); ?></a></td>
							<td align="right"><a href="#" onclick="fn_rlz_open_details('<?=$key;?>','<?=$company_id;?>','<?=$start_date;?>','<?=$end_date;?>','rlz_unrlz_details','Cash Incentive Realization Statement','700','2');"><?=number_format($row["RLZ_VALUE"],2); ?></a></td>
							<td align="right"><a href="#" onclick="fn_rlz_open_details('<?=$key;?>','<?=$company_id;?>','<?=$start_date;?>','<?=$end_date;?>','rlz_unrlz_details','Cash Incentive Realization Statement','530','3');"><?=number_format($row["CASH_CLAIM"],2); ?></a></td>
							<td align="right"><a href="#" onclick="fn_rlz_open_details('<?=$key;?>','<?=$company_id;?>','<?=$start_date;?>','<?=$end_date;?>','rlz_unrlz_details','Cash Incentive Realization Statement','600','4');"><?=number_format($tot_acc_rcv_amount,2);?></a></td>
							<?
								foreach($account_head_arr as $val)
								{
									$acc_amount=0;
									foreach($rlz_rcv_id_arr as $rcv_id)
									{
										$acc_amount+=$incentive_recv_acnt_head[$rcv_id][$val];
									}
									?><td align="right"><?=number_format($acc_amount,2);?></td><?
									$tot_acc_head[$val]+=$acc_amount;
								}
							?>
						</tr>
						<?
						$tot_rlz_invoice_qnty+=$row['INVOICE_QNTY_PCS'];
						$tot_rlz_net_invoice_value+=$row['NET_INVO_VALUE'];
						$tot_rlz_value+=$row['RLZ_VALUE'];
						$tot_rlz_cash_claim_value+=$row['CASH_CLAIM'];
						$tot_rlz_acc_rcv_amount+=$tot_acc_rcv_amount;
					}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th>Total</th>
                    <th><?=number_format($tot_rlz_invoice_qnty,2); ?></th>
                    <th><?=number_format($tot_rlz_net_invoice_value,2); ?></th>
                    <th><?=number_format($tot_rlz_value,2); ?></th>
                    <th><?=number_format($tot_rlz_cash_claim_value,2); ?></th>
                    <th><?=number_format($tot_rlz_acc_rcv_amount,2); ?></th>
					<?
					foreach($account_head_arr as $val)
					{
						?><th ><?=number_format($tot_acc_head[$val],2); ?></th><?
					}
					?>		
				</tr>
			</tfoot>
		</table>
		<br>

		<table class="rpt_table" border="1" rules="all" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" id="table_body_3">
			<thead>
				<tr>
					<th colspan="<?=(count($psbl_date_arr)*1)+9;?>" >Cash Incentive Un-Realization Statement</th>
				</tr>				
				<tr>
					<th colspan="<?=(count($psbl_date_arr)*1)+9;?>" >As On <?=$months[$month_to].'-'.$year[$year_to];?></th>
				</tr>		
				<tr>
					<th width="30">SL</th>
					<th width="150">Buyer</th>
                    <th width="100">Invoice Quantity (Pcs)</th>
                    <th width="100">Total Net Invoice Value ($)</th>
                    <th width="100">Total Net Realized Value ($)</th>
                    <th width="100">Total Cash Incentive Value Claimed (USD)</th>
                    <th width="100">Cash Incentive Value Realized (USD)</th>
                    <th width="100">Cash Incentive Value Un-realized (USD)</th>
					<?
						foreach($psbl_date_arr as $row)
						{
							?>
								<th><?=$row;?></th>
							<?
						}
					?>
                    <th >Amount BDT</th>
				</tr>
			</thead>
			<tbody >
				<?
				 $date_total=array();
					$i=1;
					foreach ($unrealization_data as $key=>$row)
					{
						if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
						?>
						<tr bgcolor="<?= $bgcolor; ?>" onclick="change_color('tbl3_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tbl3_<?=$i;?>">
							<td align="center"><?= $i++; ?></td>
							<td ><?=$buyerArr[$key]; ?></td>
							<td align="right"><?=number_format($row['INVOICE_QNTY_PCS'],2); ?></td>
							<td align="right"><a href="#" onclick="fn_rlz_open_details('<?=$key;?>','<?=$company_id;?>','<?=$start_date;?>','<?=$end_date;?>','rlz_unrlz_details','Cash Incentive Un-Realization Statement','630','5');"><?=number_format($row['NET_INVO_VALUE'],2); ?></a></td>
							<td align="right"><a href="#" onclick="fn_rlz_open_details('<?=$key;?>','<?=$company_id;?>','<?=$start_date;?>','<?=$end_date;?>','rlz_unrlz_details','Cash Incentive Un-Realization Statement','730','6');"><?=number_format($row['RLZ_VALUE'],2); ?></a></td>
							<td align="right"><a href="#" onclick="fn_rlz_open_details('<?=$key;?>','<?=$company_id;?>','<?=$start_date;?>','<?=$end_date;?>','rlz_unrlz_details','Cash Incentive Un-Realization Statement','630','7');"><?=number_format($row['CASH_CLAIM'],2); ?></a></td>
							<td align="right"><a href="#" onclick="fn_rlz_open_details('<?=$key;?>','<?=$company_id;?>','<?=$start_date;?>','<?=$end_date;?>','rlz_unrlz_details','Cash Incentive Un-Realization Statement','630','8');"><?=number_format($row['CASH_RCV'],2); ?></a></td>
							<td align="right"><a href="#" onclick="fn_rlz_open_details('<?=$key;?>','<?=$company_id;?>','<?=$start_date;?>','<?=$end_date;?>','rlz_unrlz_details','Cash Incentive Un-Realization Statement','630','9');"><?=number_format($row['UNRLZ'],2); ?></a></td>
							<?
								foreach($psbl_date_arr as $val)
								{
									$st_date_arr=explode("-",$val);
									$month_ids=date_parse($st_date_arr[0]);
									$moont_end_date=cal_days_in_month(CAL_GREGORIAN,$month_ids['month'],2023);
									
									$possiable_start_date="01-".$val;
									$possiable_end_date=$moont_end_date."-".$val;
									?>
										<td align="right"><a href="#" onclick="fn_rlz_open_details('<?=$key;?>','<?=$company_id;?>','<?=$possiable_start_date;?>','<?=$possiable_end_date;?>','rlz_unrlz_details','Cash Incentive Un-Realization Statement','630','10');"><?=number_format($unrealization_claim_data[$key][$val]["CASH_CLAIM"],2);?></a></td>
									<?
									$tot_unrealization_claim_val[$val]+=$unrealization_claim_data[$key][$val]["CASH_CLAIM"];
								}
							?>
							<td align="right"><?=number_format(($row['UNRLZ'])*$currency_conversion_rate,2); ?></td>
						</tr>
						<?
						$tot_unrlz_invoice_qnty+=$row['INVOICE_QNTY_PCS'];
						$tot_unrlz_net_invoice_value+=$row['NET_INVO_VALUE'];
						$tot_unrlz_rlz_value+=$row['RLZ_VALUE'];
						$tot_unrlz_cash_claim_value+=$row['CASH_CLAIM'];
						$tot_cash_rcv_value+=$row['CASH_RCV'];
						$tot_unrlz_cash_claim_bal+=$row['UNRLZ'];
						$tot_unrlz_cash_claim_bal_amount+=($row['UNRLZ'])*$currency_conversion_rate;
					}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th>Total:</th>
					<th><?=number_format($tot_unrlz_invoice_qnty,2); ?></th>
					<th><?=number_format($tot_unrlz_net_invoice_value,2); ?></th>
					<th><?=number_format($tot_unrlz_rlz_value,2); ?></th>
					<th><?=number_format($tot_unrlz_cash_claim_value,2); ?></th>
					<th><?=number_format($tot_cash_rcv_value,2); ?></th>
					<th><?=number_format($tot_unrlz_cash_claim_bal,2); ?></th>
					<?
						foreach($psbl_date_arr as $val)
						{
							?>
								<th align="right"><?=number_format($tot_unrealization_claim_val[$val],2);?></th>
							<?
						}
					?>
					<th><?=number_format($tot_unrlz_cash_claim_bal_amount,2); ?></th>
				</tr>
			</tfoot>
		</table>
    <?

	foreach (glob("$user_id*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
} 

if($action=="rlz_unrlz_details")
{
	extract($_REQUEST);
	echo load_html_head_contents($title, "../../../", 1, 1,'','','');
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");

	?>
		<script>
			function new_window()
			{
				document.getElementById('scroll_body').style.overflow="auto";
				document.getElementById('scroll_body').style.maxHeight="none";
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><title>Monthly Cash Incentive Summary</title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body><div style="width:820px; margin-top:20px;"></div>'+document.getElementById('popup_body').innerHTML+'</body</html>');
				d.close(); 
				document.getElementById('scroll_body').style.overflowY="scroll";
				document.getElementById('scroll_body').style.maxHeight="320px";			
			}			
			
		</script>
		<table width="600" cellspacing="0" cellpadding="0" border="0" align="center" rules="all">
			<tr><td align="center"><input type="button" class="formbutton" onClick="new_window()" style="width:100px;" value="Print" ><input type="button" id="excel_preveiw" class="formbutton" style="width:100px;" value="Excel Preveiw" ></td></tr>
		</table><br>

	<?
	$data_arr=array();$inv_chk=array();
	if($popup_type==1)
	{
		$realization_sql="SELECT a.id as INV_ID, a.BUYER_ID, a.INVOICE_NO, a.NET_INVO_VALUE, e.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION, g.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION_RCV
		from com_export_invoice_ship_mst a, cash_incentive_submission e, cash_incentive_submission_dtls f,cash_incentive_received_mst g
		where a.id=f.submission_bill_id and e.id=f.mst_id and f.mst_id=g.cash_incentive_sub_id and e.entry_form=566 and a.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 and a.buyer_id=$buyer_id and a.benificiary_id=$company_id and g.received_date between '$start_date' and '$end_date' order by e.SYS_NUMBER_PREFIX_NUM ";
		//echo $realization_sql;
		$realization_result=sql_select($realization_sql);
		foreach($realization_result as $row)
		{
			$key=$row["CASE_SUBMISSION"]."__".$row["CASE_SUBMISSION"];
			$data_arr[$key]["CASE_SUBMISSION"]=$row["CASE_SUBMISSION"];
			$data_arr[$key]["CASE_SUBMISSION_RCV"]=$row["CASE_SUBMISSION_RCV"];
			$data_arr[$key]["BUYER_ID"]=$row["BUYER_ID"];
			$data_arr[$key]["INVOICE_NO"].=$row["INVOICE_NO"].", ";
			if(!in_array($row["INV_ID"],$inv_chk))
			{
				$inv_chk[]=$row["INV_ID"];
				$data_arr[$key]["NET_INVO_VALUE"]+=$row["NET_INVO_VALUE"];
			}
		}
		?>
			<div id="popup_body" with="100%">
				<table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th colspan="6"><?=$title;?></th>
						</tr>
						<tr>
							<th colspan="6">Total Net Invoice Value POPUP</th>
						</tr>
						<tr>
							<th width="50">SL No</th>
							<th width="80">Submission ID</th>
							<th width="80">Received ID</th>
							<th width="120">Buyer</th>
							<th width="150">Invoice No</th>
							<th>Net Invoice Value</th>
						</tr>
					</thead>
				</table>
				<div style="width:620px; max-height:250px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
					<tbody>
						<?
							$i=1;
							foreach($data_arr as $row)
							{
								if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
								?>
									<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tbl5_<?=$i; ?>','<?=$bgcolor; ?>')" id="tbl5_<?=$i;?>">
										<td width="50" align="center"><?=$i;?></td>
										<td width="80" align="center"><?=$row["CASE_SUBMISSION"];?></td>
										<td width="80" align="center"><?=$row["CASE_SUBMISSION_RCV"];?></td>
										<td width="120"><?=$buyerArr[$row["BUYER_ID"]];?></td>
										<td width="150"><?=rtrim($row["INVOICE_NO"],", ");?></td>
										<td align="right"><?=number_format($row["NET_INVO_VALUE"],2);?></td>
									</tr>
								<?
								$i++;
								$tot_net_inv_val+=$row["NET_INVO_VALUE"];
							}
						?>
					</tbody>
				</table>
				</div>
				<table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="120"></th>
							<th width="150">Total </th>
							<th align="right"><?=number_format($tot_net_inv_val,2);?></th>
						</tr>
					</tfoot>
				</table>

			</div>
		<?

	}

	if($popup_type==2)
	{
		
		$realization_sql="SELECT a.BUYER_ID, a.id as INVOICE_ID, e.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION, g.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION_RCV, f.RLZ_VALUE, f.SPECIAL_INCENTIVE, f.EURO_ZONE_INCENTIVE, f.GENERAL_INCENTIVE, f.MARKET_INCENTIVE
		from com_export_invoice_ship_mst a, cash_incentive_submission e, cash_incentive_submission_dtls f,cash_incentive_received_mst g
		where a.id=f.submission_bill_id and e.id=f.mst_id and f.mst_id=g.cash_incentive_sub_id and e.entry_form=566 and a.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 and a.buyer_id=$buyer_id and a.benificiary_id=$company_id and g.received_date between '$start_date' and '$end_date' order by e.SYS_NUMBER_PREFIX_NUM ";
		// echo $realization_sql;
		$realization_result=sql_select($realization_sql);
		foreach($realization_result as $row)
		{
			$key=$row["CASE_SUBMISSION"]."__".$row["CASE_SUBMISSION_RCV"]."__".$row["BUYER_ID"];
			$data_arr[$key]["CASE_SUBMISSION"]=$row["CASE_SUBMISSION"];
			$data_arr[$key]["CASE_SUBMISSION_RCV"]=$row["CASE_SUBMISSION_RCV"];
			$data_arr[$key]["BUYER_ID"]=$row["BUYER_ID"];
			$data_arr[$key]["CLAIM_VALUE"]+=($row["SPECIAL_INCENTIVE"]*1)
			+($row["EURO_ZONE_INCENTIVE"]*1)+($row["GENERAL_INCENTIVE"]*1)+($row["MARKET_INCENTIVE"]*1);
			if(!in_array($row["INVOICE_ID"],$inv_chk))
			{
				$inv_chk[]=$row["INVOICE_ID"];
				$data_arr[$key]["NET_RLZ_VALUE"]+=$row["RLZ_VALUE"];
			}
		}
		?>
			<div id="popup_body" with="100%">
				<table class="rpt_table" border="1" rules="all" width="680" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th colspan="7"><?=$title;?></th>
						</tr>
						<tr>
							<th colspan="7">Total Net Realized Value POPUP</th>
						</tr>
						<tr>
							<th width="50">SL No</th>
							<th width="80">Submission ID</th>
							<th width="80">Received ID</th>
							<th width="100">Buyer</th>
							<th width="100">Invoice No</th>
							<th width="100">Net Realized Value</th>
							<th >Claimed Value</th>
						</tr>
					</thead>
				</table>
				<div style="width:700px; max-height:250px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" border="1" rules="all" width="680" cellpadding="0" cellspacing="0">
						<tbody>
							<?
								$i=1;
								foreach($data_arr as $key=>$row)
								{
									if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
									?>
										<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tbl5_<?=$i; ?>','<?=$bgcolor; ?>')" id="tbl5_<?=$i;?>">
											<td width="50" align="center"><?=$i;?></td>
											<td width="80" align="center"><?=$row["CASE_SUBMISSION"];?></td>
											<td width="80" align="center"><?=$row["CASE_SUBMISSION_RCV"];?></td>
											<td width="100"><?=$buyerArr[$row["BUYER_ID"]];?></td>
											<td width="100"><?=$row["INVOICE_NO"];?></td>
											<td width="100" align="right"><?=number_format($row["NET_RLZ_VALUE"],2);?></td>
											<td align="right"><?=number_format($row["CLAIM_VALUE"],2);?></td>
										</tr>
									<?
									$i++;
									$tot_net_rlz_val+=$row["NET_RLZ_VALUE"];
									$tot_net_claim_val+=$row["CLAIM_VALUE"];
								}
							?>
						</tbody>
					</table>
				</div>
				<table class="rpt_table" border="1" rules="all" width="680" cellpadding="0" cellspacing="0">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"><?=number_format($tot_net_rlz_val,2);?></th>
							<th><?=number_format($tot_net_claim_val,2);?></th>
						</tr>
					</tfoot>
				</table>

			</div>
		<?

	}

	if($popup_type==3)
	{

		$realization_sql="SELECT a.id as INV_ID, a.BUYER_ID, a.INVOICE_NO, a.NET_INVO_VALUE, e.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION, g.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION_RCV, f.SPECIAL_INCENTIVE, f.EURO_ZONE_INCENTIVE, f.GENERAL_INCENTIVE, f.MARKET_INCENTIVE
		from com_export_invoice_ship_mst a, cash_incentive_submission e, cash_incentive_submission_dtls f,cash_incentive_received_mst g
		where a.id=f.submission_bill_id and e.id=f.mst_id and f.mst_id=g.cash_incentive_sub_id and e.entry_form=566 and a.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 and a.buyer_id=$buyer_id and a.benificiary_id=$company_id and g.received_date between '$start_date' and '$end_date' order by e.SYS_NUMBER_PREFIX_NUM ";
		// echo $realization_sql;
		$realization_result=sql_select($realization_sql);
		foreach($realization_result as $row)
		{
			$key=$row["CASE_SUBMISSION"];
			$data_arr[$key]["BUYER_ID"]=$row["BUYER_ID"];
			$data_arr[$key]["CASE_SUBMISSION"]=$row["CASE_SUBMISSION"];
			$data_arr[$key]["CLAIM_VALUE"]+=$row["SPECIAL_INCENTIVE"]+$row["EURO_ZONE_INCENTIVE"]+$row["GENERAL_INCENTIVE"]+$row["MARKET_INCENTIVE"];
			$data_arr[$key]["INVOICE_NO"].=$row["INVOICE_NO"].", ";
			if(!in_array($row["INV_ID"],$inv_chk))
			{
				$inv_chk[]=$row["INV_ID"];
				$data_arr[$key]["NET_INVO_VALUE"]+=$row["NET_INVO_VALUE"];
			}
		}
		?>
			<div id="popup_body" with="100%">
				<table class="rpt_table" border="1" rules="all" width="500" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th colspan="5"><?=$title;?></th>
						</tr>
						<tr>
							<th colspan="5">Total Cash Incentive Value Claimed  POPUP</th>
						</tr>
						<tr>
							<th width="50">SL No</th>
							<th width="80">Submission ID</th>
							<th width="100">Buyer</th>
							<th width="150">Invoice No</th>
							<th>Claimed Value</th>
						</tr>
					</thead>
				</table>
				<div style="width:520px; max-height:250px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" border="1" rules="all" width="500" cellpadding="0" cellspacing="0">
					<tbody>
						<?
							$i=1;
							foreach($data_arr as $row)
							{
								if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
								?>
									<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tbl5_<?=$i; ?>','<?=$bgcolor; ?>')" id="tbl5_<?=$i;?>">
										<td width="50" align="center"><?=$i;?></td>
										<td width="80" align="center"><?=$row["CASE_SUBMISSION"];?></td>
										<td width="100"><?=$buyerArr[$row["BUYER_ID"]];?></td>
										<td width="150"><?=$row["INVOICE_NO"];?></td>
										<td align="right"><?=number_format($row["CLAIM_VALUE"],2);?></td>
									</tr>
								<?
								$i++;
								$tot_net_claim_val+=$row["CLAIM_VALUE"];
							}
						?>
					</tbody>
				</table>
				</div>
				<table class="rpt_table" border="1" rules="all" width="500" cellpadding="0" cellspacing="0">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="150">Total</th>
							<th align="right"><?=number_format($tot_net_claim_val,2);?></th>
						</tr>
					</tfoot>
				</table>

			</div>
		<?

	}

	if($popup_type==4)
	{
		
		$realization_sql="SELECT a.BUYER_ID, a.id as INVOICE_ID, e.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION, f.id as CLAIM_DTLS_ID, f.SPECIAL_INCENTIVE, f.EURO_ZONE_INCENTIVE, f.GENERAL_INCENTIVE, f.MARKET_INCENTIVE, g.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION_RCV, h.id as RCV_DTLS_ID, h.DOCUMENT_CURRENCY
		from com_export_invoice_ship_mst a, cash_incentive_submission e, cash_incentive_submission_dtls f,cash_incentive_received_mst g, cash_incentive_received_dtls h
		where a.id=f.submission_bill_id and e.id=f.mst_id and f.mst_id=g.cash_incentive_sub_id and g.id=h.mst_id and e.entry_form=566 and a.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 and h.status_active=1 and a.buyer_id=$buyer_id and a.benificiary_id=$company_id and g.received_date between '$start_date' and '$end_date' ";
		// echo $realization_sql;
		$realization_result=sql_select($realization_sql);
		$chk_rcv_id=array();$chk_clm_id=array();
		foreach($realization_result as $row)
		{
			$key=$row["CASE_SUBMISSION"]."__".$row["CASE_SUBMISSION_RCV"]."__".$row["BUYER_ID"];
			$data_arr[$key]["CASE_SUBMISSION"]=$row["CASE_SUBMISSION"];
			$data_arr[$key]["CASE_SUBMISSION_RCV"]=$row["CASE_SUBMISSION_RCV"];
			$data_arr[$key]["BUYER_ID"]=$row["BUYER_ID"];

			if(!in_array($key."__".$row["CLAIM_DTLS_ID"],$chk_clm_id))
			{
				$chk_clm_id[]=$key."__".$row["CLAIM_DTLS_ID"];
				$data_arr[$key]["CLAIM_VALUE"]+=($row["SPECIAL_INCENTIVE"]*1)+($row["EURO_ZONE_INCENTIVE"]*1)+($row["GENERAL_INCENTIVE"]*1)+($row["MARKET_INCENTIVE"]*1);
			}

			if(!in_array($key."__".$row["RCV_DTLS_ID"],$chk_rcv_id))
			{
				$chk_rcv_id[]=$key."__".$row["RCV_DTLS_ID"];
				$data_arr[$key]["NET_RCV_VALUE"]+=$row["DOCUMENT_CURRENCY"];
			}
		}
		?>
			<div id="popup_body" with="100%">
				<table class="rpt_table" border="1" rules="all" width="580" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th colspan="6"><?=$title;?></th>
						</tr>
						<tr>
							<th colspan="6">Cash Incentive Value Realized POPUP</th>
						</tr>
						<tr>
							<th width="50">SL No</th>
							<th width="80">Submission ID</th>
							<th width="80">Received ID</th>
							<th width="100">Buyer</th>
							<th width="100">Claimed Value</th>
							<th >Received Value</th>
						</tr>
					</thead>
				</table>
				<div style="width:600px; max-height:250px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" border="1" rules="all" width="580" cellpadding="0" cellspacing="0">
					<tbody>
						<?
							$i=1;
							foreach($data_arr as $key=>$row)
							{
								if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
								?>
									<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tbl5_<?=$i; ?>','<?=$bgcolor; ?>')" id="tbl5_<?=$i;?>">
										<td width="50" align="center"><?=$i;?></td>
										<td width="80" align="center"><?=$row["CASE_SUBMISSION"];?></td>
										<td width="80" align="center"><?=$row["CASE_SUBMISSION_RCV"];?></td>
										<td width="100"><?=$buyerArr[$row["BUYER_ID"]];?></td>
										<td width="100" align="right"><?=number_format($row["CLAIM_VALUE"],2);?></td>
										<td align="right"><?=number_format($row["NET_RCV_VALUE"],2);?></td>
									</tr>
								<?
								$i++;
								$tot_net_rcv+=$row["NET_RCV_VALUE"];
								$tot_net_claim_val+=$row["CLAIM_VALUE"];
							}
						?>
					</tbody>
					</table>
				</div>
				<table class="rpt_table" border="1" rules="all" width="580" cellpadding="0" cellspacing="0">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="100">Total</th>
							<th width="100" align="right"><?=number_format($tot_net_claim_val,2);?></th>
							<th align="right"><?=number_format($tot_net_rcv,2);?></th>
						</tr>
					</tfoot>
				</table>

			</div>
		<?

	}

	if($popup_type==5)
	{
		$unrealization_sql=" SELECT a.id as INV_ID, a.BUYER_ID, a.INVOICE_NO, a.NET_INVO_VALUE , e.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION
		from com_export_invoice_ship_mst a, cash_incentive_submission e, cash_incentive_submission_dtls f 
		where a.id=f.submission_bill_id and e.id=f.mst_id and e.entry_form=566 and e.submission_date <='$end_date' and a.status_active=1 and e.status_active=1 and f.status_active=1 and a.buyer_id=$buyer_id and a.benificiary_id=$company_id order by e.SYS_NUMBER_PREFIX_NUM";
		// echo $unrealization_sql;
		$unrealization_result=sql_select($unrealization_sql);
		foreach($unrealization_result as $row)
		{
			$key=$row["CASE_SUBMISSION"];
			$data_arr[$key]["CASE_SUBMISSION"]=$row["CASE_SUBMISSION"];
			$data_arr[$key]["BUYER_ID"]=$row["BUYER_ID"];
			$data_arr[$key]["INVOICE_NO"].=$row["INVOICE_NO"].", ";
			if(!in_array($row["INV_ID"],$inv_chk))
			{
				$inv_chk[]=$row["INV_ID"];
				$data_arr[$key]["NET_INVO_VALUE"]+=$row["NET_INVO_VALUE"];
			}

		}

		?>
			<div id="popup_body" with="100%">
				<table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th colspan="6"><?=$title;?></th>
						</tr>
						<tr>
							<th colspan="6">Total Net Invoice Value POPUP</th>
						</tr>
						<tr>
							<th width="50">SL No</th>
							<th width="80">Submission ID</th>
							<th width="120">Buyer</th>
							<th width="150">Invoice No</th>
							<th>Net Invoice Value</th>
						</tr>
					</thead>
				</table>
				<div style="width:620px; max-height:250px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
						<tbody>
							<?
								$i=1;
								foreach($data_arr as $row)
								{
									if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
									?>
										<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tbl5_<?=$i; ?>','<?=$bgcolor; ?>')" id="tbl5_<?=$i;?>">
											<td width="50" align="center"><?=$i;?></td>
											<td width="80" align="center"><?=$row["CASE_SUBMISSION"];?></td>
											<td width="120"><?=$buyerArr[$row["BUYER_ID"]];?></td>
											<td width="150"><?=rtrim($row["INVOICE_NO"],", ");?></td>
											<td align="right"><?=number_format($row["NET_INVO_VALUE"],2);?></td>
										</tr>
									<?
									$i++;
									$tot_net_inv_val+=$row["NET_INVO_VALUE"];
								}
							?>
						</tbody>
					</table>
				</div>
				<table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="80"></th>
							<th width="120"></th>
							<th width="150">Total</th>
							<th align="right"><?=number_format($tot_net_inv_val,2);?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		<?
	}

	if($popup_type==6)
	{
		$unrealization_sql=" SELECT a.id as INV_ID, a.INVOICE_NO, a.BUYER_ID, a.NET_INVO_VALUE , e.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION, f.RLZ_VALUE as RLZ_VALUE, g.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION_RCV, f.SPECIAL_INCENTIVE, f.EURO_ZONE_INCENTIVE, f.GENERAL_INCENTIVE, f.MARKET_INCENTIVE
		from com_export_invoice_ship_mst a, cash_incentive_submission_dtls f, cash_incentive_submission e
		left join cash_incentive_received_mst g on e.id=g.CASH_INCENTIVE_SUB_ID and g.status_active=1
		where a.id=f.submission_bill_id and e.id=f.mst_id and e.entry_form=566 and e.submission_date <='$end_date' and a.status_active=1 and e.status_active=1 and f.status_active=1 and a.buyer_id=$buyer_id and a.benificiary_id=$company_id order by e.SYS_NUMBER_PREFIX_NUM";
		// echo $unrealization_sql;
		$unrealization_result=sql_select($unrealization_sql);
		foreach($unrealization_result as $row)
		{
			$key=$row["CASE_SUBMISSION"]."__".$row["CASE_SUBMISSION_RCV"];
			$data_arr[$key]["CASE_SUBMISSION"]=$row["CASE_SUBMISSION"];
			$data_arr[$key]["CASE_SUBMISSION_RCV"]=$row["CASE_SUBMISSION_RCV"];
			$data_arr[$key]["BUYER_ID"]=$row["BUYER_ID"];
			$data_arr[$key]["INVOICE_NO"].=$row["INVOICE_NO"].", ";
			if(!in_array($row["INV_ID"],$inv_chk))
			{
				$inv_chk[]=$row["INV_ID"];
				$data_arr[$key]["RLZ_VALUE"]+=$row["RLZ_VALUE"];
			}
			$data_arr[$key]["CLAIM_VALUE"]+=$row["SPECIAL_INCENTIVE"]+$row["EURO_ZONE_INCENTIVE"]+$row["GENERAL_INCENTIVE"]+$row["MARKET_INCENTIVE"];
		}

		?>
			<div id="popup_body" with="100%">
				<table class="rpt_table" border="1" rules="all" width="680" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th colspan="7"><?=$title;?></th>
						</tr>
						<tr>
							<th colspan="7">Total Net Realized Value POPUP</th>
						</tr>
						<tr>
							<th width="50">SL No</th>
							<th width="80">Submission ID</th>
							<th width="80">Received ID</th>
							<th width="120">Buyer</th>
							<th width="150">Invoice No</th>
							<th width="80">Net Realized Value</th>
							<th>Claimed Value</th>
						</tr>
					</thead>
				</table>
				<div style="width:700px; max-height:250px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" border="1" rules="all" width="680" cellpadding="0" cellspacing="0">
					<tbody>
						<?
							$i=1;
							foreach($data_arr as $row)
							{
								if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
								?>
									<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tbl5_<?=$i; ?>','<?=$bgcolor; ?>')" id="tbl5_<?=$i;?>">
										<td width="50" align="center"><?=$i;?></td>
										<td width="80" align="center"><?=$row["CASE_SUBMISSION"];?></td>
										<td width="80" align="center"><?=$row["CASE_SUBMISSION_RCV"];?></td>
										<td width="120"><?=$buyerArr[$row["BUYER_ID"]];?></td>
										<td width="150"><?=rtrim($row["INVOICE_NO"],", ");?></td>
										<td width="80" align="right"><?=number_format($row["RLZ_VALUE"],2);?></td>
										<td align="right"><?=number_format($row["CLAIM_VALUE"],2);?></td>
									</tr>
								<?
								$i++;
								$tot_net_rlz_val+=$row["RLZ_VALUE"];
								$tot_net_claim_val+=$row["CLAIM_VALUE"];
							}
						?>
					</tbody>
				</table>
				</div>
				<table class="rpt_table" border="1" rules="all" width="680" cellpadding="0" cellspacing="0">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="120"></th>
							<th width="150">Total</th>
							<th width="80" align="right"><?=number_format($tot_net_rlz_val,2);?></th>
							<th align="right"><?=number_format($tot_net_claim_val,2);?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		<?
	}

	if($popup_type==7)
	{
		$unrealization_sql=" SELECT a.id as INV_ID, a.INVOICE_NO, a.BUYER_ID, a.NET_INVO_VALUE , e.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION, f.RLZ_VALUE as RLZ_VALUE, f.SPECIAL_INCENTIVE, f.EURO_ZONE_INCENTIVE, f.GENERAL_INCENTIVE, f.MARKET_INCENTIVE
		from com_export_invoice_ship_mst a, cash_incentive_submission e, cash_incentive_submission_dtls f
		where a.id=f.submission_bill_id and e.id=f.mst_id and e.entry_form=566 and e.submission_date <='$end_date' and a.status_active=1 and e.status_active=1 and f.status_active=1 and a.buyer_id=$buyer_id and a.benificiary_id=$company_id order by e.SYS_NUMBER_PREFIX_NUM";
		// echo $unrealization_sql;
		$unrealization_result=sql_select($unrealization_sql);
		foreach($unrealization_result as $row)
		{
			$key=$row["CASE_SUBMISSION"];
			$data_arr[$key]["CASE_SUBMISSION"]=$row["CASE_SUBMISSION"];
			$data_arr[$key]["BUYER_ID"]=$row["BUYER_ID"];
			$data_arr[$key]["INVOICE_NO"].=$row["INVOICE_NO"].", ";
			$data_arr[$key]["CLAIM_VALUE"]+=$row["SPECIAL_INCENTIVE"]+$row["EURO_ZONE_INCENTIVE"]+$row["GENERAL_INCENTIVE"]+$row["MARKET_INCENTIVE"];
		}

		?>
			<div id="popup_body" with="100%">
				<table class="rpt_table" border="1" rules="all" width="580" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th colspan="7"><?=$title;?></th>
						</tr>
						<tr>
							<th colspan="7">Total Net Realized Value POPUP</th>
						</tr>
						<tr>
							<th width="50">SL No</th>
							<th width="80">Submission ID</th>
							<th width="120">Buyer</th>
							<th width="150">Invoice No</th>
							<th>Claimed Value</th>
						</tr>
					</thead>
				</table>
				<div style="width:600px; max-height:250px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" border="1" rules="all" width="580" cellpadding="0" cellspacing="0">
					<tbody>
						<?
							$i=1;
							foreach($data_arr as $row)
							{
								if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
								?>
									<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tbl5_<?=$i; ?>','<?=$bgcolor; ?>')" id="tbl5_<?=$i;?>">
										<td width="50" align="center"><?=$i;?></td>
										<td width="80" align="center"><?=$row["CASE_SUBMISSION"];?></td>
										<td width="120"><?=$buyerArr[$row["BUYER_ID"]];?></td>
										<td width="150"><?=rtrim($row["INVOICE_NO"],", ");?></td>
										<td align="right"><?=number_format($row["CLAIM_VALUE"],2);?></td>
									</tr>
								<?
								$i++;
								$tot_net_claim_val+=$row["CLAIM_VALUE"];
							}
						?>
					</tbody>
				</table>
				</div>
				<table class="rpt_table" border="1" rules="all" width="580" cellpadding="0" cellspacing="0">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="80"></th>
							<th width="120"></th>
							<th width="150">Total</th>
							<th align="right"><?=number_format($tot_net_claim_val,2);?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		<?
	}
	
	if($popup_type==8)
	{
		$unrealization_sql=" SELECT a.BUYER_ID, a.NET_INVO_VALUE , e.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION, f.id as SUB_DTLS_ID, f.RLZ_VALUE as RLZ_VALUE, f.SPECIAL_INCENTIVE, f.EURO_ZONE_INCENTIVE, f.GENERAL_INCENTIVE, f.MARKET_INCENTIVE, g.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION_RCV, h.id as RCV_DTLS_ID, h.DOCUMENT_CURRENCY
		from com_export_invoice_ship_mst a, cash_incentive_submission_dtls f, cash_incentive_submission e
		left join cash_incentive_received_mst g on e.id=g.CASH_INCENTIVE_SUB_ID and g.status_active=1
		left join cash_incentive_received_dtls h on g.id=h.mst_id and h.status_active=1
		where a.id=f.submission_bill_id and e.id=f.mst_id and e.entry_form=566 and e.submission_date <='$end_date' and a.status_active=1 and e.status_active=1 and f.status_active=1 and a.buyer_id=$buyer_id and a.benificiary_id=$company_id order by e.SYS_NUMBER_PREFIX_NUM";
		// echo $unrealization_sql;
		$unrealization_result=sql_select($unrealization_sql);
		$chk_arr=array();$chk_arr2=array();
		foreach($unrealization_result as $row)
		{
			$key=$row["CASE_SUBMISSION"]."__".$row["CASE_SUBMISSION_RCV"];
			$data_arr[$key]["CASE_SUBMISSION"]=$row["CASE_SUBMISSION"];
			$data_arr[$key]["CASE_SUBMISSION_RCV"]=$row["CASE_SUBMISSION_RCV"];
			$data_arr[$key]["BUYER_ID"]=$row["BUYER_ID"];
			if(!in_array($row["SUB_DTLS_ID"],$chk_arr))
			{
				$chk_arr[]=$row["SUB_DTLS_ID"];
				$data_arr[$key]["CLAIM_VALUE"]+=$row["SPECIAL_INCENTIVE"]+$row["EURO_ZONE_INCENTIVE"]+$row["GENERAL_INCENTIVE"]+$row["MARKET_INCENTIVE"];
			}
			if(!in_array($row["RCV_DTLS_ID"],$chk_arr2))
			{
				$chk_arr2[]=$row["RCV_DTLS_ID"];
				$data_arr[$key]["DOCUMENT_CURRENCY"]+=$row["DOCUMENT_CURRENCY"];
			}
			
		}

		?>
			<div id="popup_body" with="100%">
				<table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th colspan="7"><?=$title;?></th>
						</tr>
						<tr>
							<th colspan="7">Cash Incentive Value Realized POPUP</th>
						</tr>
						<tr>
							<th width="50">SL No</th>
							<th width="120">Buyer</th>
							<th width="80">Submission ID</th>
							<th width="80">Received ID</th>
							<th width="150">Claimed Value</th>
							<th >Received Value</th>
						</tr>
					</thead>
				</table>
				<div style="width:620px; max-height:250px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
					<tbody>
						<?
							$i=1;
							foreach($data_arr as $row)
							{
								if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
								?>
									<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tbl5_<?=$i; ?>','<?=$bgcolor; ?>')" id="tbl5_<?=$i;?>">
										<td width="50" align="center"><?=$i;?></td>
										<td width="120"><?=$buyerArr[$row["BUYER_ID"]];?></td>
										<td width="80" align="center"><?=$row["CASE_SUBMISSION"];?></td>
										<td width="80" align="center"><?=$row["CASE_SUBMISSION_RCV"];?></td>
										<td width="150" align="right"><?=number_format($row["CLAIM_VALUE"],2);?></td>
										<td align="right"><?=number_format($row["DOCUMENT_CURRENCY"],2);?></td>
									</tr>
								<?
								$i++;
								$tot_net_rcv_val+=$row["DOCUMENT_CURRENCY"];
								$tot_net_claim_val+=$row["CLAIM_VALUE"];
							}
						?>
					</tbody>
				</table>
				</div>
				<table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="120"></th>
							<th width="80"></th>
							<th width="80">Total</th>
							<th width="150" align="right"><?=number_format($tot_net_claim_val,2);?></th>
							<th align="right"><?=number_format($tot_net_rcv_val,2);?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		<?
	}

	if($popup_type==9)
	{
		$unrealization_sql=" SELECT a.id as INV_ID, a.INVOICE_NO, a.BUYER_ID, a.NET_INVO_VALUE , e.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION, f.RLZ_VALUE as RLZ_VALUE, f.SPECIAL_INCENTIVE, f.EURO_ZONE_INCENTIVE, f.GENERAL_INCENTIVE, f.MARKET_INCENTIVE
		from com_export_invoice_ship_mst a, cash_incentive_submission_dtls f, cash_incentive_submission e
		where a.id=f.submission_bill_id and e.id=f.mst_id and e.entry_form=566 and e.submission_date <='$end_date' and e.id not in ( select cash_incentive_sub_id from cash_incentive_received_mst where COMPANY_ID=$company_id and status_active=1 ) and a.status_active=1 and e.status_active=1 and f.status_active=1 and a.buyer_id=$buyer_id and a.benificiary_id=$company_id order by e.SYS_NUMBER_PREFIX_NUM";
		// echo $unrealization_sql;
		$unrealization_result=sql_select($unrealization_sql);
		foreach($unrealization_result as $row)
		{
			$key=$row["CASE_SUBMISSION"];
			$data_arr[$key]["CASE_SUBMISSION"]=$row["CASE_SUBMISSION"];
			$data_arr[$key]["BUYER_ID"]=$row["BUYER_ID"];
			$data_arr[$key]["INVOICE_NO"].=$row["INVOICE_NO"].", ";
			if(!in_array($row["INV_ID"],$inv_chk))
			{
				$inv_chk[]=$row["INV_ID"];
				$data_arr[$key]["RLZ_VALUE"]+=$row["RLZ_VALUE"];
			}
			$data_arr[$key]["CLAIM_VALUE"]+=$row["SPECIAL_INCENTIVE"]+$row["EURO_ZONE_INCENTIVE"]+$row["GENERAL_INCENTIVE"]+$row["MARKET_INCENTIVE"];
		}

		?>
			<div id="popup_body" with="100%">
				<table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th colspan="7"><?=$title;?></th>
						</tr>
						<tr>
							<th colspan="7">Total Net Realized Value POPUP</th>
						</tr>
						<tr>
							<th width="50">SL No</th>
							<th width="80">Submission ID</th>
							<th width="120">Buyer</th>
							<th width="150">Invoice No</th>
							<th width="80">Net Realized Value</th>
							<th>Claimed Value</th>
						</tr>
					</thead>
				</table>
				<div style="width:620px; max-height:250px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
					<tbody>
						<?
							$i=1;
							foreach($data_arr as $row)
							{
								if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
								?>
									<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tbl5_<?=$i; ?>','<?=$bgcolor; ?>')" id="tbl5_<?=$i;?>">
										<td width="50" align="center"><?=$i;?></td>
										<td width="80" align="center"><?=$row["CASE_SUBMISSION"];?></td>
										<td width="120"><?=$buyerArr[$row["BUYER_ID"]];?></td>
										<td width="150"><?=rtrim($row["INVOICE_NO"],", ");?></td>
										<td width="80" align="right"><?=number_format($row["RLZ_VALUE"],2);?></td>
										<td align="right"><?=number_format($row["CLAIM_VALUE"],2);?></td>
									</tr>
								<?
								$i++;
								$tot_net_rlz_val+=$row["RLZ_VALUE"];
								$tot_net_claim_val+=$row["CLAIM_VALUE"];
							}
						?>
					</tbody>
				</table>
				</div>
				<table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="80"></th>
							<th width="120"></th>
							<th width="150">Total</th>
							<th width="80" align="right"><?=number_format($tot_net_rlz_val,2);?></th>
							<th align="right"><?=number_format($tot_net_claim_val,2);?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		<?
	}
	
	if($popup_type==10)
	{
		$unrealization_sql=" SELECT a.id as INV_ID, a.INVOICE_NO, a.BUYER_ID, a.NET_INVO_VALUE , e.SYS_NUMBER_PREFIX_NUM as CASE_SUBMISSION, f.RLZ_VALUE as RLZ_VALUE, f.SPECIAL_INCENTIVE, f.EURO_ZONE_INCENTIVE, f.GENERAL_INCENTIVE, f.MARKET_INCENTIVE
		from com_export_invoice_ship_mst a, cash_incentive_submission_dtls f, cash_incentive_submission e
		where a.id=f.submission_bill_id and e.id=f.mst_id and e.entry_form=566 and e.POSSIBLE_REALI_DATE between '$start_date' and '$end_date' and e.id not in ( select cash_incentive_sub_id from cash_incentive_received_mst where COMPANY_ID=$company_id and status_active=1 ) and a.status_active=1 and e.status_active=1 and f.status_active=1 and a.buyer_id=$buyer_id and a.benificiary_id=$company_id order by e.SYS_NUMBER_PREFIX_NUM";
		//echo $unrealization_sql;
		
		$unrealization_result=sql_select($unrealization_sql);
		foreach($unrealization_result as $row)
		{
			$key=$row["CASE_SUBMISSION"];
			$data_arr[$key]["CASE_SUBMISSION"]=$row["CASE_SUBMISSION"];
			$data_arr[$key]["BUYER_ID"]=$row["BUYER_ID"];
			$data_arr[$key]["INVOICE_NO"].=$row["INVOICE_NO"].", ";
			if(!in_array($row["INV_ID"],$inv_chk))
			{
				$inv_chk[]=$row["INV_ID"];
				$data_arr[$key]["RLZ_VALUE"]+=$row["RLZ_VALUE"];
			}
			$data_arr[$key]["CLAIM_VALUE"]+=$row["SPECIAL_INCENTIVE"]+$row["EURO_ZONE_INCENTIVE"]+$row["GENERAL_INCENTIVE"]+$row["MARKET_INCENTIVE"];
		}

		?>
			<div id="popup_body" with="100%">
				<table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th colspan="7"><?=$title;?></th>
						</tr>
						<tr>
							<th colspan="7">Total Net Realized Value POPUP</th>
						</tr>
						<tr>
							<th width="50">SL No</th>
							<th width="80">Submission ID</th>
							<th width="120">Buyer</th>
							<th width="150">Invoice No</th>
							<th width="80">Net Realized Value</th>
							<th>Claimed Value</th>
						</tr>
					</thead>
				</table>
				<div style="width:620px; max-height:250px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
					<tbody>
						<?
							$i=1;
							foreach($data_arr as $row)
							{
								if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
								?>
									<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tbl5_<?=$i; ?>','<?=$bgcolor; ?>')" id="tbl5_<?=$i;?>">
										<td width="50" align="center"><?=$i;?></td>
										<td width="80" align="center"><?=$row["CASE_SUBMISSION"];?></td>
										<td width="120"><?=$buyerArr[$row["BUYER_ID"]];?></td>
										<td width="150"><?=rtrim($row["INVOICE_NO"],", ");?></td>
										<td width="80" align="right"><?=number_format($row["RLZ_VALUE"],2);?></td>
										<td align="right"><?=number_format($row["CLAIM_VALUE"],2);?></td>
									</tr>
								<?
								$i++;
								$tot_net_rlz_val+=$row["RLZ_VALUE"];
								$tot_net_claim_val+=$row["CLAIM_VALUE"];
							}
						?>
					</tbody>
				</table>
				</div>
				<table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0">
					<tfoot>
						<tr>
							<th width="50"></th>
							<th width="80"></th>
							<th width="120"></th>
							<th width="150">Total</th>
							<th width="80" align="right"><?=number_format($tot_net_rlz_val,2);?></th>
							<th align="right"><?=number_format($tot_net_claim_val,2);?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		<?
	}

    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    $filename= $user_id."_".$name.".xls";
    ?>
    <script type="text/javascript">
        setFilterGrid('tbody_id',-1);
        $("#excel_preveiw").click(function(e) {
			window.open("<? echo $filename ; ?>");
			e.preventDefault();
        });
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <?
    exit();
}