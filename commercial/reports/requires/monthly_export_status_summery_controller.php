<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header('location:login.php');
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 170, "SELECT id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data'
	order by location_name","id,location_name", 1, "-- All Location --", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0 );
	exit();
}


if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=5 and report_id=298 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);

	foreach($printButton as $id){
		if($id==108)$buttonHtml.='<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />';	
		if($id==195)$buttonHtml.='<input type="button" name="search" id="search" value="Show 2" onClick="generate_report(2)" style="width:80px" class="formbutton" />';	
		if($id==242)$buttonHtml.='<input type="button" name="search" id="search" value="Show 3" onClick="generate_report(3)" style="width:80px" class="formbutton" />';	
		if($id==359)$buttonHtml.='<input type="button" name="search" id="search" value="Show 4" onClick="generate_report(4)" style="width:80px" class="formbutton" />';	
	}

    echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}


if($action=="report_generate")
{
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $company_id  = str_replace("'","",$cbo_company_name);
    $location_id = str_replace("'","",$cbo_location_id);   
    $buyer_id    = str_replace("'","",$cbo_buyer_id);   
    $cbo_year    = str_replace("'","",$cbo_year);    
    $cbo_month   = str_replace("'","",$cbo_month); 
   	$rpt_type   = str_replace("'","",$rpt_type); //die();

	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");

	if($company_id!=0) 
	{
		$company_cond=" and a.benificiary_id=$company_id";
		$company_cond2=" and a.company_id=$company_id";
	}

	if($location_id!=0)
	{
		$location_cond=" and a.location_id=$location_id";
		$location_cond2=" and e.location_id=$location_id";
		$location_cond3=" and c.location_id=$location_id";
	}

	if($buyer_id!=0)
	{
		$buyer_id_cond=" and a.buyer_id=$buyer_id";
	}

	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month, $cbo_year);
	$start_date=$cbo_year."-".$cbo_month."-"."01";
	$end_date=$cbo_year."-".$cbo_month."-".$daysinmonth;
	$cd = strtotime($end_date);
	$end_date2 = date('Y-m-t', mktime(0,0,0,date('m',$cd)+6,1,date('Y',$cd)));
	$monthArr=array();
	for($x = 0; $x <= 6; $x++){
		$month_date= date('M-Y', mktime(0,0,0,date('m',$cd)+$x,1,date('Y',$cd)));
		$monthArr[$month_date]=$month_date;
	}

	if($db_type==0)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd');
		$end_date=change_date_format($end_date,'yyyy-mm-dd');
		$end_date2=change_date_format($end_date2,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$start_date=change_date_format($start_date,'','',-1);
		$end_date=change_date_format($end_date,'','',-1);
		$end_date2=change_date_format($end_date2,'','',-1);
	}

	$date_cond=" and a.EX_FACTORY_DATE between '$start_date' and '$end_date'";
	$date_cond2=" and a.received_date between '$start_date' and '$end_date'";
	// $date_cond3=" and a.received_date between '$start_date' and '$end_date2'";
	$date_cond4=" and a.possible_reali_date between '$start_date' and '$end_date2'";
	$date_cond5=" and a.invoice_date between '$start_date' and '$end_date2'";
	$date_cond6=" and c.invoice_date between '$start_date' and '$end_date2'";

	$invoice_sql="SELECT a.buyer_id as BUYER_ID, sum(a.invoice_quantity) as INVOICE_QUANTITY, sum(a.invoice_value) as INVOICE_VALUE, sum(a.net_invo_value) as NET_INVO_VALUE from com_export_invoice_ship_mst a where a.status_active=1 and a.is_deleted=0 $company_cond $location_cond $buyer_id_cond $date_cond group by a.buyer_id order by a.buyer_id";
	// echo $invoice_sql;die;
	$invoice_result=sql_select($invoice_sql);

	$order_set_sql="SELECT a.buyer_id as BUYER_ID, sum(b.current_invoice_qnty*d.total_set_qnty) as INVOICE_QNTY_PCS from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b, wo_po_break_down c, wo_po_details_master d where a.id=b.mst_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and b.status_active=1 $company_cond $location_cond $buyer_id_cond $date_cond group by a.buyer_id";
	// echo $order_set_sql;
	$order_set_result=sql_select($order_set_sql);
	$inv_qnty_pcs_arr=array();
	foreach($order_set_result as $row)
	{
		$inv_qnty_pcs_arr[$row["BUYER_ID"]]=$row["INVOICE_QNTY_PCS"];
	}

	$realization_sql="SELECT a.id as REALIZATION_ID, a.buyer_id as BUYER_ID, a.invoice_bill_id as INVOICE_BILL_ID, b.type as TYPE,b.account_head as ACCOUNT_HEAD,sum(b.document_currency) as DOCUMENT_CURRENCY
	from com_export_proceed_realization a,com_export_proceed_rlzn_dtls b
	where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $company_cond $buyer_id_cond $date_cond2 group by a.id,a.buyer_id,a.invoice_bill_id,b.type,b.account_head";
	// echo $realization_sql;
	$realization_result=sql_select($realization_sql);
	$realization_data=array();$invoice_bill_id_arr=array();
	foreach($realization_result as $row)
	{
		$realization_data[$row['BUYER_ID']]['buyer_id']=$row['BUYER_ID'];
		$realization_data[$row['BUYER_ID']]['realization_id'].=$row['REALIZATION_ID'].',';
		$invoice_bill_id_arr[$row['INVOICE_BILL_ID']]=$row['INVOICE_BILL_ID'];

		if($row['TYPE']==0 && $row['ACCOUNT_HEAD']==194)
		{
			$realization_data[$row['BUYER_ID']]['discounted_to_buyer']+=$row['DOCUMENT_CURRENCY'];
		}
		else if($row['TYPE']==0 && $row['ACCOUNT_HEAD']!=194)
		{
			$realization_data[$row['BUYER_ID']]['short_realized_value']+=$row['DOCUMENT_CURRENCY'];
		}
		
		if($row['TYPE']==1 )
		{
			$realization_data[$row['BUYER_ID']]['realized_value']+=$row['DOCUMENT_CURRENCY'];
			$realization_account_data[$row['BUYER_ID']][$row['ACCOUNT_HEAD']]+=$row['DOCUMENT_CURRENCY'];
			$realization_account_head[$row['ACCOUNT_HEAD']]=$row['ACCOUNT_HEAD'];
		}
	}
	ksort($realization_account_head);
	$head_count=count($realization_account_head);
	
	$invoice_bill_id_in=where_con_using_array($invoice_bill_id_arr,0,'a.invoice_bill_id');
	$prev_realization_sql="SELECT a.id, a.buyer_id as BUYER_ID, b.type as TYPE,sum(b.document_currency) as DOCUMENT_CURRENCY
	from com_export_proceed_realization a,com_export_proceed_rlzn_dtls b
	where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.received_date < '$start_date' $invoice_bill_id_in group by a.id,a.buyer_id,b.type,b.account_head";
	// echo $prev_realization_sql;
	$prev_realization_result=sql_select($prev_realization_sql);
	$prev_realization_data=array();
	foreach($prev_realization_result as $row)
	{
		if($row['TYPE']==1 ||$row['TYPE']==0) 
		{
			$prev_realization_data[$row['BUYER_ID']]['pre_realization']+=$row['DOCUMENT_CURRENCY'];
		}
	}

	$realiz_inv_sql="SELECT a.buyer_id as BUYER_ID, a.received_date as RECEIVED_DATE, e.id as INVOICE_ID, e.invoice_quantity as INVOICE_QUANTITY, e.net_invo_value as NET_INVO_VALUE
	from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d, com_export_invoice_ship_mst e 
	where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and e.id=d.invoice_id and c.entry_form=40 and a.status_active=1  and c.status_active=1 and d.status_active=1 and e.status_active=1 $company_cond $location_cond2 $buyer_id_cond $date_cond2";
	// echo $realiz_inv_sql;die;
	$realiz_inv_result=sql_select($realiz_inv_sql);
	$invoice_id_ck=array();
	foreach($realiz_inv_result as $row)
	{
		if(!in_array($row['INVOICE_ID'],$invoice_id_ck))
		{
			$invoice_id_ck[$row['INVOICE_ID']]=$row['INVOICE_ID'];
			$realization_data[$row['BUYER_ID']]['invoice_qnty']+=$row['INVOICE_QUANTITY'];
			$realization_data[$row['BUYER_ID']]['net_invo_value']+=$row['NET_INVO_VALUE'];
			$realization_data[$row['BUYER_ID']]['invoice_id'].=$row['INVOICE_ID'].',';
		}
	}

	$unrealization_sql="SELECT a.buyer_id as BUYER_ID, a.possible_reali_date as POSSIBLE_REALI_DATE, c.id as INVOICE_ID, c.invoice_quantity as INVOICE_QUANTITY, c.net_invo_value as NET_INVO_VALUE
	from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
	where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=39 and b.is_converted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 $company_cond2 $location_cond3 $buyer_id_cond $date_cond4 and c.id not in (SELECT d.invoice_id from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and c.entry_form=40 and a.status_active=1  and c.status_active=1 and d.status_active=1 $company_cond $buyer_id_cond)
	union all 
	SELECT a.buyer_id as BUYER_ID, a.possible_reali_date as POSSIBLE_REALI_DATE, c.id as INVOICE_ID, c.invoice_quantity as INVOICE_QUANTITY, c.net_invo_value as NET_INVO_VALUE
	from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
	where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=40 and a.status_active=1 and b.status_active=1 and c.status_active=1 $company_cond2 $location_cond3 $buyer_id_cond $date_cond4 and c.id not in (SELECT d.invoice_id from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and c.entry_form=40 and a.status_active=1  and c.status_active=1 and d.status_active=1 $company_cond $buyer_id_cond)";
	// echo $unrealization_sql;die;
	$unrealization_result=sql_select($unrealization_sql);
	$unrealization_data=$unrealization_info=array();
	foreach($unrealization_result as $row)
	{
		$unrealization_data[$row['BUYER_ID']]['buyer_id']=$row['BUYER_ID'];
		$unrealization_data[$row['BUYER_ID']]['invoice_qnty']+=$row['INVOICE_QUANTITY'];
		$unrealization_data[$row['BUYER_ID']]['net_invo_value']+=$row['NET_INVO_VALUE'];
		$unrealization_data[$row['BUYER_ID']]['invoice_id'].=$row['INVOICE_ID'].',';
		$month=date("M-Y", strtotime($row['POSSIBLE_REALI_DATE']));
		$unrealization_info[$row['BUYER_ID']][$month]['net_invo_value']+=$row['NET_INVO_VALUE'];	
		// $unrealization_info[$row['BUYER_ID']][$month]['invoice_id'].=$row['INVOICE_ID'].',';	
	}
	if($rpt_type==1 || $rpt_type==2)
	{

     ?>
        <table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0" id="table_body_1">
			<thead>
				<tr>
					<th colspan="6" >Shipment for the month of <?=$months[$cbo_month].'-'.$year[$cbo_year];?></th>
				</tr>				
				<tr>
					<th width="30">SL</th>
					<th width="200">Buyer</th>
                    <th width="100" style="display:none;">Invoice Qty.</th>
                    <th width="100">Invoice Qty. Pcs</th>
                    <th width="120">Invoice Value (Gross)</th>
                    <th >Invoice Value (Net)</th>
				</tr>
			</thead>
			<tbody >
				<?
					$i=1;
					foreach ($invoice_result as $row)
					{
						if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
						?>
						<tr bgcolor="<?= $bgcolor; ?>" onclick="change_color('tbl1_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tbl1_<?=$i;?>">
							<td align="center"><p><?= $i; ?></p></td>
							<td ><p><?= $buyerArr[$row['BUYER_ID']]; ?></p></td>
							<td align="right" style="display:none;"><?= number_format($row['INVOICE_QUANTITY'],2,'.',''); ?></td>
							<td align="right"><?= number_format($inv_qnty_pcs_arr[$row["BUYER_ID"]],2,'.',''); ?></td>
							<td align="right"><?= number_format($row['INVOICE_VALUE'],2,'.',''); ?></td>
							<td align="right">
							<a href="##" onClick="openmypage_invoice(4,'<?= $company_id; ?>','<?= $location_id; ?>','<?= $row["BUYER_ID"]; ?>','<?= $start_date; ?>','<?= $end_date; ?>','Popup of Inv Value','<?=$months[$cbo_month].'-'.$year[$cbo_year];?>')" ><?= number_format($row['NET_INVO_VALUE'],2,'.',''); ?></a>

						</td>
						</tr>
						<?
						$i++;
						$tot_invoice_qnty+=$row['INVOICE_QUANTITY'];
						$tot_invoice_qnty_pcs+=$inv_qnty_pcs_arr[$row["BUYER_ID"]];
						$tot_invoice_value+=$row['INVOICE_VALUE'];
						$tot_net_invoice_value+=$row['NET_INVO_VALUE'];
					}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th>Total:</th>
					<th style="display:none;"><?= number_format($tot_invoice_qnty,2,'.',''); ?></th>
					<th><?= number_format($tot_invoice_qnty_pcs,2,'.',''); ?></th>
					<th><?= number_format($tot_invoice_value,2,'.',''); ?></th>
					<th><?= number_format($tot_net_invoice_value,2,'.',''); ?></th>
				</tr>
			</tfoot>
		</table>
		<br>
        <table class="rpt_table" border="1" rules="all" width="<?=700+($head_count*80);?>" cellpadding="0" cellspacing="0" id="table_body_2">
			<thead>
				<tr>
					<th colspan="<?=8+$head_count;?>" >Proceeds Realization Statement </th>
				</tr>				
				<tr>
					<th colspan="<?=8+$head_count;?>" >Realization for the Month of <?=$months[$cbo_month].'-'.$year[$cbo_year];?></th>
				</tr>				
				<tr>
					<th width="30">SL</th>
					<th width="120">Buyer</th>
                    <th width="80">Invoice Qty.</th>
                    <th width="100">Invoice Value (Net)</th>
                    <th width="100">Discount For At Sight Payment</th>
                    <th width="100">Short Realized Value</th>
                    <th width="80">Realized Value</th>
					<?
						foreach($realization_account_head as $key=>$val)
						{
							?>
                    			<th width="80"><?=$commercial_head[$val];?></th>
							<?
						}
					?>
                    <th >Balance</th>
				</tr>
			</thead>
			<tbody >
				<?
					$i=1;
					foreach ($realization_data as $buyer_id=>$row)
					{
						if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
						$realized_value=$row["realized_value"]+$row['discounted_to_buyer']+$row['short_realized_value'];
						$balance=$row['net_invo_value']-($realized_value+$prev_realization_data[$buyer_id]['pre_realization']);
						$realization_id= implode(",",array_unique(explode(",",chop($row["realization_id"],','))));
						?>
						<tr bgcolor="<?= $bgcolor; ?>" onclick="change_color('tbl2_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tbl2_<?=$i;?>">
							<td align="center" title="<?= $row['net_invo_value']."=".$realized_value."=".$prev_realization_data[$buyer_id]['pre_realization'];?>"><p><?= $i; ?></p></td>
							<td ><p><?= $buyerArr[$row['buyer_id']]; ?></p></td>
							<td align="right"><?= number_format($row['invoice_qnty'],2,'.',''); ?></td>
							<td align="right"><a href="##" onClick="openmypage_invoice(1,'<?= $company_id; ?>','<?= $location_id; ?>','<?= $row["buyer_id"]; ?>','<?= $start_date; ?>','<?= $end_date; ?>','Popup of Inv Value (Realize)','<?=$months[$cbo_month].'-'.$year[$cbo_year];?>')" ><?= number_format($row['net_invo_value'],2,'.',''); ?></a></td>
							<td align="right"><?= number_format($row['discounted_to_buyer'],2,'.',''); ?></td>
							<td align="right"><a href="##" onClick="openmypage_short('<? echo $realization_id; ?>','Popup of Short Realize details')" ><?= number_format($row['short_realized_value'],2,'.',''); ?></a></td>
							<td align="right"><a href="##" onClick="openmypage_realization('<? echo $realization_id; ?>','Popup of Realize Value formate','<?=$months[$cbo_month].'-'.$year[$cbo_year];?>')" ><?= number_format($row["realized_value"],2,'.',''); ?></a></td>
							<?
								foreach($realization_account_head as $key=>$val)
								{
									?>
										<td align="right"><?= number_format($realization_account_data[$buyer_id][$val],2,'.','');?></td>
									<?
									$tot_realization_account[$val]+=$realization_account_data[$buyer_id][$val];
								}
							?>
							<td align="right"><? if(abs(number_format($balance,2,'.',''))==0) echo abs(number_format($balance,2,'.','')); else echo number_format($balance,2,'.',''); ?></td>
						</tr>
						<?
						$i++;
						$tot_realization_invoice_qnty+=$row['invoice_qnty'];
						$tot_realization_net_invoice_value+=$row['net_invo_value'];
						$tot_discounted_to_buyer_invoice_value+=$row['discounted_to_buyer'];
						$tot_short_realized_value_invoice_value+=$row['short_realized_value'];
						$tot_realized_value_invoice_value+=$row["realized_value"];
						$tot_balance_value_invoice_value+=$balance;

					}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th>Total:</th>
					<th><?= number_format($tot_realization_invoice_qnty,2,'.',''); ?></th>
					<th><?= number_format($tot_realization_net_invoice_value,2,'.',''); ?></th>
					<th><?= number_format($tot_discounted_to_buyer_invoice_value,2,'.',''); ?></th>
					<th><?= number_format($tot_short_realized_value_invoice_value,2,'.',''); ?></th>
					<th><?= number_format($tot_realized_value_invoice_value,2,'.',''); ?></th>
					<?
						foreach($realization_account_head as $key=>$val)
						{
							?>
								<th align="right"><?= number_format($tot_realization_account[$val],2,'.','');?></th>
							<?
						}
					?>
					<th><?  if(abs(number_format($tot_balance_value_invoice_value,2,'.',''))==0) echo abs(number_format($tot_balance_value_invoice_value,2,'.','')); else echo number_format($tot_balance_value_invoice_value,2,'.',''); ?></th>
					
				</tr>
			</tfoot>
		</table>
		<br>
		<table class="rpt_table" border="1" rules="all" width="1100" cellpadding="0" cellspacing="0" id="table_body_3">
			<thead>
				<tr>
					<th colspan="11" >Unrealized Decuments Statement (Bill Receivable)</th>
				</tr>				
				<tr>
					<th width="30">SL</th>
					<th width="200">Buyer</th>
                    <th width="100">Invoice Qty.</th>
                    <th >Invoice Value (Net)</th>
					<?
						foreach($monthArr as $val)
						{
							?>
								<th width="80"><?=$val;?></th>
							<?
						}
					?>
				</tr>
			</thead>
			<tbody >
				<?
				 $date_total=array();
					$i=1;
					foreach ($unrealization_data as $row)
					{
						if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
						?>
						<tr bgcolor="<?= $bgcolor; ?>" onclick="change_color('tbl3_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tbl3_<?=$i;?>">
							<td align="center"><p><?= $i; ?></p></td>
							<td ><p><?= $buyerArr[$row['buyer_id']]; ?></p></td>
							<td align="right"><?= number_format($row['invoice_qnty'],2,'.',''); ?></td>
							<td align="right"><a href="##" onClick="openmypage_invoice(2,'<?= $company_id; ?>','<?= $location_id; ?>','<?= $row["buyer_id"]; ?>','<?= $start_date; ?>','<?= $end_date2; ?>','Popup of Inv Value (Unrealize)','<?=$months[$cbo_month].'-'.$year[$cbo_year];?>')" ><?= number_format($row['net_invo_value'],2,'.',''); ?></a></td>
							<?
								foreach($monthArr as $val)
								{
									if($db_type==0)
									{
										$startDate = date("Y-m-d",strtotime($val));
										$endDate = date("Y-m-t",strtotime($val));
									}
									else
									{
										$startDate = date("d-M-Y", strtotime($val));
										$endDate = date("t-M-Y", strtotime($val));
									}
									?>
										<td width="90" align="right"><a href="##" onClick="openmypage_invoice(2,'<?= $company_id; ?>','<?= $location_id; ?>','<?= $row["buyer_id"]; ?>','<?= $startDate; ?>','<?= $endDate; ?>','Popup of Inv Value (Unrealize)','<?=$val;?>','<?=$cbo_year;?>')" ><? echo number_format($unrealization_info[$row['buyer_id']][$val]['net_invo_value'],2,'.','');?></a></td>
									<?
									$date_total[$val]+=$unrealization_info[$row['buyer_id']][$val]['net_invo_value'];
								}
							?>
						</tr>
						<?
						$i++;
						$tot_unrealization_invoice_qnty+=$row['invoice_qnty'];
						$tot_unrealization_net_invoice_value+=$row['net_invo_value'];
					}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th>Total:</th>
					<th><?= number_format($tot_unrealization_invoice_qnty,2,'.',''); ?></th>
					<th><?= number_format($tot_unrealization_net_invoice_value,2,'.',''); ?></th>
					<?
					foreach($monthArr as $val)
					{
						?>
							<th><? echo number_format($date_total[$val],2,'.','');?></th>
						<?
					}
					?>
				</tr>
			</tfoot>
		</table>

		<?
	}
	if($rpt_type==3)  //show button 3
	{
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$company_add=return_library_array( "select id, CITY from lib_company",'id','CITY');
		$inv_date_cond=" and a.INVOICE_DATE between '$start_date' and '$end_date'";
		$ex_fact_inv_date_cond=" and b.EX_FACTORY_DATE between '$start_date' and '$end_date'";

		$inv_sql ="SELECT count(a.INVOICE_NO) as INV_COUNT ,a.buyer_id as BUYER_ID, sum(a.invoice_quantity) as INVOICE_QUANTITY, sum(a.invoice_value) as INVOICE_VALUE, sum(a.net_invo_value) as NET_INVO_VALUE from com_export_invoice_ship_mst a  where a.status_active=1 and a.is_deleted=0 $company_cond $location_cond $buyer_id_cond $inv_date_cond group by a.buyer_id order by a.buyer_id";
		//echo $inv_sql;
		$inv_result=sql_select($inv_sql);

		// $ex_fact_inv_sql ="SELECT a.buyer_id as BUYER_ID, sum(a.invoice_quantity) as INVOICE_QUANTITY, sum(a.invoice_value) as INVOICE_VALUE, sum(a.net_invo_value) as NET_INVO_VALUE 
		// from com_export_invoice_ship_mst a,PRO_EX_FACTORY_MST b 
		// where a.status_active=1 and a.is_deleted=0 and a.id=b.INVOICE_NO and b.status_active=1 and b.is_deleted=0 $company_cond $location_cond $buyer_id_cond $ex_fact_inv_date_cond  group by a.buyer_id order by a.buyer_id";

		$ex_fact_inv_sql ="SELECT a.id as ID,
		a.buyer_id as BUYER_ID, a.invoice_quantity as INVOICE_QUANTITY, a.invoice_value
		as INVOICE_VALUE, a.net_invo_value as NET_INVO_VALUE 
		from com_export_invoice_ship_mst a,PRO_EX_FACTORY_MST b 
		where a.status_active=1 and a.is_deleted=0 and a.id=b.INVOICE_NO and b.status_active=1 and b.is_deleted=0 $company_cond $location_cond $buyer_id_cond $ex_fact_inv_date_cond group by a.id,a.buyer_id, a.invoice_quantity, a.invoice_value, a.net_invo_value";

		//echo $ex_fact_inv_sql;
		$ex_fact_inv_result=sql_select($ex_fact_inv_sql);

		$ex_fac_count_arr=array();
		$ex_fac_arr=array();
		$ex_fac_cnt_arr=array();
		foreach($ex_fact_inv_result as $row)
		{
			$ex_fac_count_arr[$row['BUYER_ID']] += count($row['BUYER_ID']);
			//$ex_fac_cnt_arr[$row['ID']][[$row['BUYER_ID']]] = $row['ID'];
			$ex_fac_arr[$row['BUYER_ID']]['ID']=$row['ID'];
			$ex_fac_arr[$row['BUYER_ID']]['BUYER_ID']=$row['BUYER_ID'];
			$ex_fac_arr[$row['BUYER_ID']]['INVOICE_QUANTITY']+=$row['INVOICE_QUANTITY'];
			$ex_fac_arr[$row['BUYER_ID']]['INVOICE_VALUE']+=$row['INVOICE_VALUE'];
			$ex_fac_arr[$row['BUYER_ID']]['NET_INVO_VALUE']+=$row['NET_INVO_VALUE'];
		}


		$real_inv_sql="SELECT count(e.INVOICE_NO) as INV_COUNT ,e.buyer_id as BUYER_ID, sum(e.invoice_quantity) as INVOICE_QUANTITY, sum(e.invoice_value) as INVOICE_VALUE, sum(e.net_invo_value) as NET_INVO_VALUE
		from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d, com_export_invoice_ship_mst e 
		where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and e.id=d.invoice_id and c.entry_form=40 and a.status_active=1  and c.status_active=1 and d.status_active=1 and e.status_active=1 $company_cond $location_cond2 $buyer_id_cond $date_cond2 group by e.buyer_id order by e.buyer_id";
		// echo $real_inv_sql;die;
		$real_inv_result=sql_select($real_inv_sql);

		$unreal_sql="SELECT count(c.INVOICE_NO) as INV_COUNT ,c.BUYER_ID as BUYER_ID, sum(c.invoice_quantity) as INVOICE_QUANTITY, sum(c.invoice_value) as INVOICE_VALUE, sum(c.net_invo_value) as NET_INVO_VALUE
		from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
		where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form in (39,40) and b.is_converted=0  and a.status_active=1 and b.status_active=1 and c.status_active=1 $company_cond2 $location_cond3 $buyer_id_cond and c.id not in (SELECT d.invoice_id from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and c.entry_form in(39,40) and a.status_active=1  and c.status_active=1 and d.status_active=1 $company_cond $buyer_id_cond)  and c.invoice_date  <= to_date('$end_date 23:59:59', 'dd-MM-yyyy hh24:mi:ss') group by c.buyer_id order by c.buyer_id";	



		// echo $unreal_sql;die;
		$unreal_result=sql_select($unreal_sql);
		$sql_2="SELECT c.BUYER_ID,c.id as INVOICE_ID, c.benificiary_id as BENIFICIARY_ID, c.invoice_no as INVOICE_NO, c.invoice_date as INVOICE_DATE, c.invoice_value as GROSS_INVOICE_VALUE, c.net_invo_value as NET_INVO_VALUE
		from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
		where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form in (39,40) and b.is_converted=0  and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.company_id=$company_id $location_cond $buyer_id_cond   and c.id not in (SELECT d.invoice_id from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and c.entry_form in (39,40) and a.status_active=1  and c.status_active=1 and d.status_active=1 and a.benificiary_id=$company_id $buyer_id_cond) and c.invoice_date  <= to_date('$end_date 23:59:59', 'dd-MM-yyyy hh24:mi:ss')
		group by c.BUYER_ID,c.id, c.benificiary_id , c.invoice_no , c.invoice_date, c.invoice_value, c.net_invo_value";
		$unreal_result_2=sql_select($sql_2);
		$unreal_arr=array();
		foreach($unreal_result_2 as $row){
			$unreal_arr[$row['BUYER_ID']]['GROSS_INVOICE_VALUE'] += $row['GROSS_INVOICE_VALUE'];
		}
		?>
		<div width="800" align="center">			
			<b style="font-size: 20px;"><?echo $company_arr[$company_id];?></b><br>
			<? echo $company_add[$company_id] ."<br>". $start_date ." to ". $end_date; ?>
			<table class="rpt_table" border="1" rules="all" width="800" cellpadding="0" cellspacing="0" id="table_body_1">
				<thead>				
					<tr>
						<th width="200">Particulars</th>
						<th width="200">Buyer</th>
						<th width="100" >No of Invoice</th>
						<th width="150">Total Value ($)</th>
						<th width="150">Total Quantity (Pcs)</th>
					</tr>
				</thead>
				<tbody>
				   <?
					$i=1;
					foreach ($inv_result as $row)
					{
						if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
						?>
						<tr bgcolor="<?= $bgcolor; ?>" onclick="change_color('tbl4_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tbl4_<?=$i;?>">
						<?if($i==1){?>
							<td align="center" valign="middle" rowspan="<?=count($inv_result);?>">Invoice Summary (Issue) </td><?}?>
							<td ><p><?= $buyerArr[$row['BUYER_ID']]; ?></p></td>
							<td align="right"><p><?= $row['INV_COUNT']." PCS"; ?></p></td>
							<td align="right" > <a href="##" onClick="openmypage_invoice_v2(1,'<?= $company_id; ?>','<?= $location_id; ?>','<?= $row["BUYER_ID"]; ?>','<?= $start_date; ?>','<?= $end_date; ?>','Invoice Summary (Issue)','<?=$val;?>','<?=$cbo_year;?>')" ><p><?= "$ ".number_format($row['INVOICE_VALUE'],2); ?></p></a></td>
							<td align="right"><p><?= $row['INVOICE_QUANTITY']." PCS" ?></p></td>
						</tr>	
						<?
						$i++;
						$tot_inv_count +=$row['INV_COUNT'];
						$tot_inv_val +=$row['INVOICE_VALUE'];
						$tot_inv_qty +=$row['INVOICE_QUANTITY'];
					}	
					?>	
					<tr>
						<td align="right"><b>Sub Total:</b></td>
						<td align="right"><b></b></td>
						<td align="right"><b><?= $tot_inv_count." PCS"; ?></b></td>
						<td align="right"><b><?= "$ ". number_format($tot_inv_val,2); ?></b></td>
						<td align="right"><b><?= number_format($tot_inv_qty,2)."PCS"; ?></b></td>
					</tr>
				</tbody>
				<tbody>
				<?			 
					$j=1;
					foreach ($ex_fac_arr as $row)
					{
						//echo count($row['BUYER_ID'])."<br>";
						if ($j%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
						?>
						<tr bgcolor="<?= $bgcolor; ?>" onclick="change_color('tbl5_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tbl5_<?=$j;?>">
						<?if($j==1){?>
							<td align="center" valign="middle" rowspan="<?echo count($ex_fac_arr);?>">Invoice Summary (Ex Factory)</td><?}?>
							<td ><p><?= $buyerArr[$row['BUYER_ID']]; ?></p></td>
							<!-- <td align="right"><p><?//= $row['INV_COUNT']." PCS"; ?></p></td> -->
							<td align="right"><p><?=$ex_fac_count_arr[$row['BUYER_ID']]." PCS"; ?></p></td>
							<td align="right"> <a href="##" onClick="openmypage_invoice_v2(2,'<?= $company_id; ?>','<?= $location_id; ?>','<?= $row["BUYER_ID"]; ?>','<?= $start_date; ?>','<?= $end_date; ?>','Invoice Summary (Ex Factory)','<?=$val;?>','<?=$cbo_year;?>')" ><p><?= "$ ".number_format($row['INVOICE_VALUE'],2); ?></a></p></td>
							<td align="right"><p><?= $row['INVOICE_QUANTITY']." PCS" ?></p></td>
						</tr>	
						<?
						$j++;
						//$tot_inv_count_ex +=$row['INV_COUNT'];
						$tot_inv_count_ex +=count($ex_fac_count_arr);
						$tot_inv_val_ex +=$row['INVOICE_VALUE'];
						$tot_inv_qty_ex +=$row['INVOICE_QUANTITY'];
					}	
					?>	
					<tr>
						<td align="right"><b>Sub Total:</b></td>
						<td align="right"><b></b></td>
						<td align="right"><b><?= $tot_inv_count_ex." PCS"; ?></b></td>
						<td align="right"><b><?= "$ ". number_format($tot_inv_val_ex,2); ?></b></td>
						<td align="right"><b><?= number_format($tot_inv_qty_ex,2)."PCS"; ?></b></td>
					</tr>
				</tbody>					
				<tbody>
				    <?				 
					$j=1;
					foreach ($real_inv_result as $row)
					{
						if ($j%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
						?>
						<tr bgcolor="<?= $bgcolor; ?>" onclick="change_color('tbl6_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tbl6_<?=$j;?>">
						<?if($j==1){?>
							<td align="center" valign="middle" rowspan="<?=count($real_inv_result);?>">Invoice Summary (Realization)</td><?}?>
							<td ><p><?= $buyerArr[$row['BUYER_ID']]; ?></p></td>
							<td align="right"><p><?= $row['INV_COUNT']." PCS"; ?></p></td>
							<td align="right"> <a href="##" onClick="openmypage_invoice_v2(3,'<?= $company_id; ?>','<?= $location_id; ?>','<?= $row["BUYER_ID"]; ?>','<?= $start_date; ?>','<?= $end_date; ?>','Invoice Summary (Realization)','<?=$val;?>','<?=$cbo_year;?>')" ><p><?= "$ ".number_format($row['INVOICE_VALUE'],2); ?></p></a></td>
							<td align="right"><p><?= $row['INVOICE_QUANTITY']." PCS" ?></p></td>
						</tr>	
						<?
						$j++;
						$tot_inv_count_real +=$row['INV_COUNT'];
						$tot_inv_val_ex_real +=$row['INVOICE_VALUE'];
						$tot_inv_qty_ex_real +=$row['INVOICE_QUANTITY'];
					} ?>	
					<tr>
						<td align="right"><b>Sub Total:</b></td>
						<td align="right"><b></b></td>
						<td align="right"><b><?= $tot_inv_count_real." PCS"; ?></b></td>
						<td align="right"><b><?= "$ ". number_format($tot_inv_val_ex_real,2); ?></b></td>
						<td align="right"><b><?= number_format($tot_inv_qty_ex_real,2)."PCS"; ?></b></td>
					</tr>
				</tbody>
				<tbody>
				    <?				 
					$j=1;
					foreach ($unreal_result as $row)
					{
						if ($j%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
						?>
						<tr bgcolor="<?= $bgcolor; ?>" onclick="change_color('tbl7_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tbl7_<?=$j;?>">
						<?if($j==1){?>
							<td align="center" valign="middle" rowspan="<?=count($unreal_result);?>">Total Un-Realization amount as on <?= change_date_format($end_date)?> </td><?}?>
							<td ><p><?= $buyerArr[$row['BUYER_ID']]; ?></p></td>
							<td align="right"><p><?= $row['INV_COUNT']." PCS"; ?></p></td>
							<td align="right"><a href="##" onClick="openmypage_invoice_v2(4,'<?= $company_id; ?>','<?= $location_id; ?>','<?= $row["BUYER_ID"]; ?>','<?= $start_date; ?>','<?= $end_date; ?>','Total Un-Realization Ammount as On','<?=$val;?>','<?=$cbo_year;?>')" ><p><?= "$ ".number_format($unreal_arr[$row['BUYER_ID']]['GROSS_INVOICE_VALUE'],2); ?></p></a></td>
							<td align="right"><p><?= $row['INVOICE_QUANTITY']." PCS" ?></p></td>
						</tr>	
						<?
						$j++;
						$tot_inv_count_unreal +=$row['INV_COUNT'];
						$tot_inv_val_ex_unreal +=$row['INVOICE_VALUE'];
						$tot_inv_qty_ex_unreal +=$row['INVOICE_QUANTITY'];
				    }?>	
						<tr>
							<td align="right"><b>Sub Total:</b></td>
							<td align="right"><b></b></td>
							<td align="right"><b><?= $tot_inv_count_unreal." PCS"; ?></b></td>
							<td align="right"><b><?= "$ ". number_format($tot_inv_val_ex_unreal,2); ?></b></td>
							<td align="right"><b><?= number_format($tot_inv_qty_ex_unreal,2)."PCS"; ?></b></td>
						</tr>
				</tbody>				
			</table>
		</div>
	   <?
	}

	if($rpt_type==4) //show button 4
	{

		$unrealization_sql_v2="SELECT a.buyer_id as BUYER_ID, c.invoice_date as POSSIBLE_REALI_DATE, c.id as INVOICE_ID, c.invoice_quantity as INVOICE_QUANTITY, c.net_invo_value as NET_INVO_VALUE
		from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
		where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=39 and b.is_converted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 $company_cond2 $location_cond3 $buyer_id_cond $date_cond6 and c.id not in (SELECT d.invoice_id from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and c.entry_form=40 and a.status_active=1  and c.status_active=1 and d.status_active=1 $company_cond $buyer_id_cond)
		union all 
		SELECT a.buyer_id as BUYER_ID, c.invoice_date as POSSIBLE_REALI_DATE, c.id as INVOICE_ID, c.invoice_quantity as INVOICE_QUANTITY, c.net_invo_value as NET_INVO_VALUE
		from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
		where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=40 and a.status_active=1 and b.status_active=1 and c.status_active=1 $company_cond2 $location_cond3 $buyer_id_cond $date_cond6 and c.id not in (SELECT d.invoice_id from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and c.entry_form=40 and a.status_active=1  and c.status_active=1 and d.status_active=1 $company_cond $buyer_id_cond)
		union all  
		SELECT a.buyer_id as BUYER_ID, a.invoice_date as POSSIBLE_REALI_DATE, a.id as INVOICE_ID, a.invoice_quantity as INVOICE_QUANTITY, a.net_invo_value as NET_INVO_VALUE
		from  com_export_invoice_ship_mst a
		where  a.status_active=1 $company_cond $location_cond $buyer_id_cond $date_cond5 and a.id not in (SELECT d.invoice_id from com_export_doc_submission_mst a, com_export_doc_submission_invo d where a.id=d.doc_submission_mst_id and a.status_active=1  and a.status_active=1 and d.status_active=1 $company_cond2 $buyer_id_cond)";


		// echo $unrealization_sql_v2;die;
		$unrealization_result=sql_select($unrealization_sql_v2);
		$unrealization_data=$unrealization_info=array();
		foreach($unrealization_result as $row)
		{
			$unrealization_data[$row['BUYER_ID']]['buyer_id']=$row['BUYER_ID'];
			$unrealization_data[$row['BUYER_ID']]['invoice_qnty']+=$row['INVOICE_QUANTITY'];
			$unrealization_data[$row['BUYER_ID']]['net_invo_value']+=$row['NET_INVO_VALUE'];
			$unrealization_data[$row['BUYER_ID']]['invoice_id'].=$row['INVOICE_ID'].',';
			$month=date("M-Y", strtotime($row['POSSIBLE_REALI_DATE']));
			$unrealization_info[$row['BUYER_ID']][$month]['net_invo_value']+=$row['NET_INVO_VALUE'];	
			// $unrealization_info[$row['BUYER_ID']][$month]['invoice_id'].=$row['INVOICE_ID'].',';	
		}

     ?>
        <table class="rpt_table" border="1" rules="all" width="600" cellpadding="0" cellspacing="0" id="table_body_1">
			<thead>
				<tr>
					<th colspan="6" >Shipment for the month of <?=$months[$cbo_month].'-'.$year[$cbo_year];?></th>
				</tr>				
				<tr>
					<th width="30">SL</th>
					<th width="200">Buyer</th>
                    <th width="100" style="display:none;">Invoice Qty.</th>
                    <th width="100">Invoice Qty. Pcs</th>
                    <th width="120">Invoice Value (Gross)</th>
                    <th >Invoice Value (Net)</th>
				</tr>
			</thead>
			<tbody >
				<?
					$i=1;
					foreach ($invoice_result as $row)
					{
						if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
						?>
						<tr bgcolor="<?= $bgcolor; ?>" onclick="change_color('tbl1_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tbl1_<?=$i;?>">
							<td align="center"><p><?= $i; ?></p></td>
							<td ><p><?= $buyerArr[$row['BUYER_ID']]; ?></p></td>
							<td align="right" style="display:none;"><?= number_format($row['INVOICE_QUANTITY'],2,'.',''); ?></td>
							<td align="right"><?= number_format($inv_qnty_pcs_arr[$row["BUYER_ID"]],2,'.',''); ?></td>
							<td align="right"><?= number_format($row['INVOICE_VALUE'],2,'.',''); ?></td>
							<td align="right">
							<a href="##" onClick="openmypage_invoice(4,'<?= $company_id; ?>','<?= $location_id; ?>','<?= $row["BUYER_ID"]; ?>','<?= $start_date; ?>','<?= $end_date; ?>','Popup of Inv Value','<?=$months[$cbo_month].'-'.$year[$cbo_year];?>')" ><?= number_format($row['NET_INVO_VALUE'],2,'.',''); ?></a>

						</td>
						</tr>
						<?
						$i++;
						$tot_invoice_qnty+=$row['INVOICE_QUANTITY'];
						$tot_invoice_qnty_pcs+=$inv_qnty_pcs_arr[$row["BUYER_ID"]];
						$tot_invoice_value+=$row['INVOICE_VALUE'];
						$tot_net_invoice_value+=$row['NET_INVO_VALUE'];
					}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th>Total:</th>
					<th style="display:none;"><?= number_format($tot_invoice_qnty,2,'.',''); ?></th>
					<th><?= number_format($tot_invoice_qnty_pcs,2,'.',''); ?></th>
					<th><?= number_format($tot_invoice_value,2,'.',''); ?></th>
					<th><?= number_format($tot_net_invoice_value,2,'.',''); ?></th>
				</tr>
			</tfoot>
		</table>
		<br>
        <table class="rpt_table" border="1" rules="all" width="<?=700+($head_count*80);?>" cellpadding="0" cellspacing="0" id="table_body_2">
			<thead>
				<tr>
					<th colspan="<?=8+$head_count;?>" >Proceeds Realization Statement </th>
				</tr>				
				<tr>
					<th colspan="<?=8+$head_count;?>" >Realization for the Month of <?=$months[$cbo_month].'-'.$year[$cbo_year];?></th>
				</tr>				
				<tr>
					<th width="30">SL</th>
					<th width="120">Buyer</th>
                    <th width="80">Invoice Qty.</th>
                    <th width="100">Invoice Value (Net)</th>
                    <th width="100">Discount For At Sight Payment</th>
                    <th width="100">Short Realized Value</th>
                    <th width="80">Realized Value</th>
					<?
						foreach($realization_account_head as $key=>$val)
						{
							?>
                    			<th width="80"><?=$commercial_head[$val];?></th>
							<?
						}
					?>
                    <th >Balance</th>
				</tr>
			</thead>
			<tbody >
				<?
					$i=1;
					foreach ($realization_data as $buyer_id=>$row)
					{
						if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
						$realized_value=$row["realized_value"]+$row['discounted_to_buyer']+$row['short_realized_value'];
						$balance=$row['net_invo_value']-($realized_value+$prev_realization_data[$buyer_id]['pre_realization']);
						$realization_id= implode(",",array_unique(explode(",",chop($row["realization_id"],','))));
						?>
						<tr bgcolor="<?= $bgcolor; ?>" onclick="change_color('tbl2_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tbl2_<?=$i;?>">
							<td align="center" title="<?= $row['net_invo_value']."=".$realized_value."=".$prev_realization_data[$buyer_id]['pre_realization'];?>"><p><?= $i; ?></p></td>
							<td ><p><?= $buyerArr[$row['buyer_id']]; ?></p></td>
							<td align="right"><?= number_format($row['invoice_qnty'],2,'.',''); ?></td>
							<td align="right"><a href="##" onClick="openmypage_invoice(1,'<?= $company_id; ?>','<?= $location_id; ?>','<?= $row["buyer_id"]; ?>','<?= $start_date; ?>','<?= $end_date; ?>','Popup of Inv Value (Realize)','<?=$months[$cbo_month].'-'.$year[$cbo_year];?>')" ><?= number_format($row['net_invo_value'],2,'.',''); ?></a></td>
							<td align="right"><?= number_format($row['discounted_to_buyer'],2,'.',''); ?></td>
							<td align="right"><a href="##" onClick="openmypage_short('<? echo $realization_id; ?>','Popup of Short Realize details')" ><?= number_format($row['short_realized_value'],2,'.',''); ?></a></td>
							<td align="right"><a href="##" onClick="openmypage_realization('<? echo $realization_id; ?>','Popup of Realize Value formate','<?=$months[$cbo_month].'-'.$year[$cbo_year];?>')" ><?= number_format($row["realized_value"],2,'.',''); ?></a></td>
							<?
								foreach($realization_account_head as $key=>$val)
								{
									?>
										<td align="right"><?= number_format($realization_account_data[$buyer_id][$val],2,'.','');?></td>
									<?
									$tot_realization_account[$val]+=$realization_account_data[$buyer_id][$val];
								}
							?>
							<td align="right"><? if(abs(number_format($balance,2,'.',''))==0) echo abs(number_format($balance,2,'.','')); else echo number_format($balance,2,'.',''); ?></td>
						</tr>
						<?
						$i++;
						$tot_realization_invoice_qnty+=$row['invoice_qnty'];
						$tot_realization_net_invoice_value+=$row['net_invo_value'];
						$tot_discounted_to_buyer_invoice_value+=$row['discounted_to_buyer'];
						$tot_short_realized_value_invoice_value+=$row['short_realized_value'];
						$tot_realized_value_invoice_value+=$row["realized_value"];
						$tot_balance_value_invoice_value+=$balance;

					}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th>Total:</th>
					<th><?= number_format($tot_realization_invoice_qnty,2,'.',''); ?></th>
					<th><?= number_format($tot_realization_net_invoice_value,2,'.',''); ?></th>
					<th><?= number_format($tot_discounted_to_buyer_invoice_value,2,'.',''); ?></th>
					<th><?= number_format($tot_short_realized_value_invoice_value,2,'.',''); ?></th>
					<th><?= number_format($tot_realized_value_invoice_value,2,'.',''); ?></th>
					<?
						foreach($realization_account_head as $key=>$val)
						{
							?>
								<th align="right"><?= number_format($tot_realization_account[$val],2,'.','');?></th>
							<?
						}
					?>
					<th><?  if(abs(number_format($tot_balance_value_invoice_value,2,'.',''))==0) echo abs(number_format($tot_balance_value_invoice_value,2,'.','')); else echo number_format($tot_balance_value_invoice_value,2,'.',''); ?></th>
					
				</tr>
			</tfoot>
		</table>
		<br>
		<table class="rpt_table" border="1" rules="all" width="1100" cellpadding="0" cellspacing="0" id="table_body_3">
			<thead>
				<tr>
					<th colspan="11" >Unrealized Documents  Statement (Bill Receivable)</th>
				</tr>				
				<tr>
					<th width="30">SL</th>
					<th width="200">Buyer</th>
                    <th width="100">Invoice Qty.</th>
                    <th >Invoice Value (Net)</th>
					<?
						foreach($monthArr as $val)
						{
							?>
								<th width="80"><?=$val;?></th>
							<?
						}
					?>
				</tr>
			</thead>
			<tbody >
				<?
				 $date_total=array();
					$i=1;
					foreach ($unrealization_data as $row)
					{
						if ($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }
						?>
						<tr bgcolor="<?= $bgcolor; ?>" onclick="change_color('tbl3_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tbl3_<?=$i;?>">
							<td align="center"><p><?= $i; ?></p></td>
							<td ><p><?= $buyerArr[$row['buyer_id']]; ?></p></td>
							<td align="right"><?= number_format($row['invoice_qnty'],2,'.',''); ?></td>
							<td align="right"><a href="##" onClick="openmypage_invoice_v3(2,'<?= $company_id; ?>','<?= $location_id; ?>','<?= $row["buyer_id"]; ?>','<?= $start_date; ?>','<?= $end_date2; ?>','Popup of Inv Value (Unrealize)','<?=$months[$cbo_month].'-'.$year[$cbo_year];?>')" ><?= number_format($row['net_invo_value'],2,'.',''); ?></a></td>
							<?
								foreach($monthArr as $val)
								{
									if($db_type==0)
									{
										$startDate = date("Y-m-d",strtotime($val));
										$endDate = date("Y-m-t",strtotime($val));
									}
									else
									{
										$startDate = date("d-M-Y", strtotime($val));
										$endDate = date("t-M-Y", strtotime($val));
									}
									?>
										<td width="90" align="right"><a href="##" onClick="openmypage_invoice(7,'<?= $company_id; ?>','<?= $location_id; ?>','<?= $row["buyer_id"]; ?>','<?= $startDate; ?>','<?= $endDate; ?>','Popup of Inv Value (Unrealize)','<?=$val;?>','<?=$cbo_year;?>')" ><? echo number_format($unrealization_info[$row['buyer_id']][$val]['net_invo_value'],2,'.','');?></a></td>
									<?
									$date_total[$val]+=$unrealization_info[$row['buyer_id']][$val]['net_invo_value'];
								}
							?>
						</tr>
						<?
						$i++;
						$tot_unrealization_invoice_qnty+=$row['invoice_qnty'];
						$tot_unrealization_net_invoice_value+=$row['net_invo_value'];
					}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th>Total:</th>
					<th><?= number_format($tot_unrealization_invoice_qnty,2,'.',''); ?></th>
					<th><?= number_format($tot_unrealization_net_invoice_value,2,'.',''); ?></th>
					<?
					foreach($monthArr as $val)
					{
						?>
							<th><? echo number_format($date_total[$val],2,'.','');?></th>
						<?
					}
					?>
				</tr>
			</tfoot>
		</table>

		<?
	}

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

if($action=="invoice_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$com_name = return_library_array( "SELECT id, company_name from lib_company","id","company_name");
	
	/*$sql="SELECT id as INVOICE_ID, benificiary_id as BENIFICIARY_ID, invoice_no as INVOICE_NO, invoice_date as INVOICE_DATE, invoice_value as GROSS_INVOICE_VALUE, net_invo_value as NET_INVO_VALUE
	from com_export_invoice_ship_mst where id in($invoice_id) and status_active=1 order by invoice_date";*/
	if($realized_type==1)
	{
		if($location_id){$location_cond=" and e.location_id=$location_id";}
		$sql="SELECT e.id as INVOICE_ID, e.benificiary_id as BENIFICIARY_ID, e.invoice_no as INVOICE_NO, e.invoice_date as INVOICE_DATE, e.invoice_value as GROSS_INVOICE_VALUE, e.net_invo_value as NET_INVO_VALUE
		from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d, com_export_invoice_ship_mst e 
		where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and e.id=d.invoice_id and c.entry_form=40 and a.status_active=1  and c.status_active=1 and d.status_active=1 and e.status_active=1 and a.benificiary_id=$company_id and a.buyer_id=$buyer_id and a.received_date between '$start_date' and '$end_date' $location_cond
		group by e.id, e.benificiary_id, e.invoice_no, e.invoice_date, e.invoice_value, e.net_invo_value";
	}
	if($realized_type==2)
	{
		if($location_id){$location_cond=" and c.location_id=$location_id";}
		$sql="SELECT a.company_id as BENIFICIARY_ID, c.invoice_no as INVOICE_NO, c.invoice_date as INVOICE_DATE, c.invoice_value as GROSS_INVOICE_VALUE, c.net_invo_value as NET_INVO_VALUE
		from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
		where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=39 and b.is_converted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.company_id=$company_id $location_cond and a.buyer_id=$buyer_id and a.possible_reali_date between '$start_date' and '$end_date' and c.id not in (SELECT d.invoice_id from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and c.entry_form=40 and a.status_active=1  and c.status_active=1 and d.status_active=1 and a.benificiary_id=$company_id and a.buyer_id=$buyer_id)
		group by a.company_id, c.invoice_no, c.invoice_date, c.invoice_value, c.net_invo_value
		union all 
		SELECT a.company_id as BENIFICIARY_ID, c.invoice_no as INVOICE_NO, c.invoice_date as INVOICE_DATE, c.invoice_value as GROSS_INVOICE_VALUE, c.net_invo_value as NET_INVO_VALUE
		from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
		where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=40 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.company_id=$company_id $location_cond and a.buyer_id=$buyer_id  and a.possible_reali_date between '$start_date' and '$end_date' and c.id not in (SELECT d.invoice_id from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and c.entry_form=40 and a.status_active=1  and c.status_active=1 and d.status_active=1 and a.benificiary_id=$company_id and a.buyer_id=$buyer_id)
		group by a.company_id, c.invoice_no, c.invoice_date, c.invoice_value, c.net_invo_value";
	}
	if($realized_type==7)
	{
		if($location_id){$location_cond=" and c.location_id=$location_id";}
      $sql=" SELECT 
	  a.company_id as BENIFICIARY_ID,
	  a.buyer_id             AS BUYER_ID,
			 c.invoice_date         AS INVOICE_DATE,
			 c.id                   AS INVOICE_ID,
			 c.invoice_quantity     AS INVOICE_QUANTITY,
			 c.net_invo_value       AS NET_INVO_VALUE,
			 c.invoice_no         AS INVOICE_NO,
			 c.invoice_value      AS GROSS_INVOICE_VALUE
		FROM com_export_doc_submission_mst   a,
			 com_export_doc_submission_invo  b,
			 com_export_invoice_ship_mst     c
	   WHERE     a.id = b.doc_submission_mst_id
			 AND b.invoice_id = c.id
			 AND a.entry_form = 39
			and  a.buyer_id=$buyer_id
			 AND b.is_converted = 0
			 AND a.status_active = 1
			 AND b.status_active = 1
			 AND c.status_active = 1
			 AND a.company_id = $company_id $location_cond
			 AND c.invoice_date BETWEEN '$start_date' and '$end_date'
			 AND c.id NOT IN
					 (SELECT d.invoice_id
						FROM com_export_proceed_realization  a,
							 com_export_doc_submission_mst   c,
							 com_export_doc_submission_invo  d
					   WHERE     a.invoice_bill_id = c.id
							 AND c.id = d.doc_submission_mst_id
							 AND c.entry_form = 40
							 and  a.buyer_id=$buyer_id
							 AND a.status_active = 1
							 AND c.status_active = 1
							 AND d.status_active = 1
							 AND a.benificiary_id = $company_id)
							 
							 group by
							 a.company_id,
							 a.buyer_id   ,          
			 c.invoice_date     ,   
			 c.id                 ,
			 c.invoice_quantity   , 
			 c.net_invo_value    ,
			 
			 c.invoice_no         ,
			 c.invoice_value      
							 
	  UNION ALL
	  SELECT 
	  a.company_id as BENIFICIARY_ID,
	  a.buyer_id             AS BUYER_ID,
			 c.invoice_date         AS INVOICE_DATE,
			 c.id                   AS INVOICE_ID,
			 c.invoice_quantity     AS INVOICE_QUANTITY,
			 c.net_invo_value       AS NET_INVO_VALUE,
			  c.invoice_no         AS INVOICE_NO,
			 c.invoice_value      AS GROSS_INVOICE_VALUE
		FROM com_export_doc_submission_mst   a,
			 com_export_doc_submission_invo  b,
			 com_export_invoice_ship_mst     c
	   WHERE     a.id = b.doc_submission_mst_id
			 AND b.invoice_id = c.id
			 AND a.entry_form = 40
			 AND a.status_active = 1
			 AND b.status_active = 1
			 AND c.status_active = 1
			 AND a.company_id = $company_id
			 and  a.buyer_id=$buyer_id
			 AND c.invoice_date BETWEEN '$start_date' and '$end_date'
			 AND c.id NOT IN
					 (SELECT d.invoice_id
						FROM com_export_proceed_realization  a,
							 com_export_doc_submission_mst   c,
							 com_export_doc_submission_invo  d
					   WHERE     a.invoice_bill_id = c.id
							 AND c.id = d.doc_submission_mst_id
							 AND c.entry_form = 40
							 AND a.status_active = 1
							 AND c.status_active = 1
							 AND d.status_active = 1
							 and  a.buyer_id=$buyer_id
							 AND a.benificiary_id =$company_id)
							 
							 group by   
							 a.company_id,
							 a.buyer_id   ,          
			 c.invoice_date     ,   
			 c.id                 ,
			 c.invoice_quantity   , 
			 c.net_invo_value    ,
			   c.invoice_no         ,
			 c.invoice_value        
							 
	  UNION ALL
	  SELECT 
	  a.BENIFICIARY_ID as BENIFICIARY_ID,
	  a.buyer_id             AS BUYER_ID,
			 a.invoice_date         AS INVOICE_DATE,
			 a.id                   AS INVOICE_ID,
			 a.invoice_quantity     AS INVOICE_QUANTITY,
			 a.net_invo_value       AS NET_INVO_VALUE,
			  a.invoice_no         AS INVOICE_NO,
			 a.invoice_value      AS GROSS_INVOICE_VALUE
		FROM com_export_invoice_ship_mst a
	   WHERE     a.status_active = 1
	            and a.buyer_id = $buyer_id
			 AND a.benificiary_id = $company_id
			 AND a.invoice_date BETWEEN '$start_date' and '$end_date'
			 AND a.id NOT IN
					 (SELECT d.invoice_id
						FROM com_export_doc_submission_mst   a,
							 com_export_doc_submission_invo  d
					   WHERE     a.id = d.doc_submission_mst_id
							 AND a.status_active = 1
							 AND a.status_active = 1
							 AND d.status_active = 1
							 and  a.buyer_id=$buyer_id
							 AND a.company_id = $company_id)
							 
							 
							 group by 
							 a.BENIFICIARY_ID,
							 a.buyer_id      ,     
			 a.invoice_date        ,
			 a.id              ,   
			 a.invoice_quantity ,    
			 a.net_invo_value       ,
			  a.invoice_no         ,
			 a.invoice_value      ";
	}
	if($realized_type==4) // only invoice
	{
		if($location_id){$location_cond=" and a.location_id=$location_id";}
		$sql = "SELECT a.id as INVOICE_ID, a.benificiary_id as BENIFICIARY_ID, a.invoice_no as INVOICE_NO, a.invoice_date as INVOICE_DATE,
		a.invoice_value as GROSS_INVOICE_VALUE, a.net_invo_value as NET_INVO_VALUE 
		from com_export_invoice_ship_mst a where a.status_active=1 and a.is_deleted=0 and a.benificiary_id=$company_id and a.buyer_id=$buyer_id and a.EX_FACTORY_DATE between '$start_date' and '$end_date' $location_cond group by a.id, a.benificiary_id, a.invoice_no, a.invoice_date, a.invoice_value, a.net_invo_value";
	}
	// echo $sql;
	$result=sql_select($sql); 
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML>'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<fieldset style="width:560px; margin-left:10px">
		<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
		<br />
		<div id="report_container" align="center" style="width:560px">
            <table class="rpt_table" border="1" rules="all" width="560" cellpadding="0" cellspacing="0">
             	<thead>
					 <tr>
						 <th colspan="6"> Month OF-- <?=$month_year;?></th>
					 </tr>
					 <tr>
						<th width="50">SL No</th>
						<th width="120">Com. Name</th>
						<th width="150">Invoice No</th>
						<th width="60">Invoice Date</th>
						<th width="80">Inv. Gross Value ($)</th>
						<th >Inv. Net Value ($)</th> 
					 </tr>
                </thead>
                <tbody>
					<?
						$i=1;
						foreach($result as $row)  
						{
							if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?=$i;?>">
								<td align="center"><? echo $i; ?></td>
								<td><? echo $com_name[$row['BENIFICIARY_ID']]; ?></td>
								<td><? echo $row['INVOICE_NO']; ?></td>
								<td align="center"><? echo change_date_format($row['INVOICE_DATE']); ?></td>
								<td align="right"><? echo number_format($row['GROSS_INVOICE_VALUE'],2); ?></td>
								<td align="right"><? echo number_format($row['NET_INVO_VALUE'],2); ?></td>
							</tr>
							<?
							$i++;
							$total_gross_invoice_value+=$row['GROSS_INVOICE_VALUE'];
							$total_net_invo_value+=$row['NET_INVO_VALUE'];
						}
					?>
                </tbody>   
				<tfoot>
					<tr>
						<th colspan="4">Total :</th>
						<th><?=number_format($total_gross_invoice_value,2);?></th>
						<th><?=number_format($total_net_invo_value,2);?></th>
					</tr>
				</tfoot>
            </table>
		</div>
    </fieldset>
	<?
    exit();
}

if($action=="invoice_details_V2")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$com_name = return_library_array( "SELECT id, company_name from lib_company","id","company_name");

	if($buyer_id){$buyer_id_cond=" and a.BUYER_ID=$buyer_id";}
	if($buyer_id){$buyer_id=" and e.BUYER_ID=$buyer_id";}

	if($realized_type==1)
	{
		if($location_id){$location_cond=" and a.location_id=$location_id";}
		$sql ="SELECT a.id as INVOICE_ID, a.benificiary_id as BENIFICIARY_ID, a.invoice_no as INVOICE_NO, a.invoice_date as INVOICE_DATE, a.invoice_value as GROSS_INVOICE_VALUE, a.net_invo_value as NET_INVO_VALUE from com_export_invoice_ship_mst a  where a.status_active=1 and a.is_deleted=0 and a.benificiary_id=$company_id and a.invoice_date between '$start_date' and '$end_date' $location_cond $buyer_id_cond ";

	}
	if($realized_type==2)
	{
		if($location_id){$location_cond=" and a.location_id=$location_id";}
		$sql ="SELECT a.id as INVOICE_ID, a.benificiary_id as BENIFICIARY_ID, a.invoice_no as INVOICE_NO, a.invoice_date as INVOICE_DATE, a.invoice_value as GROSS_INVOICE_VALUE, a.net_invo_value as NET_INVO_VALUE,max(b.EX_FACTORY_DATE) as EX_FACTORY_DATE
		from com_export_invoice_ship_mst a,PRO_EX_FACTORY_MST b 
		where a.status_active=1 and a.is_deleted=0 and a.id=b.INVOICE_NO and b.status_active=1 and b.is_deleted=0 and a.benificiary_id=$company_id and b.EX_FACTORY_DATE between '$start_date' and '$end_date' $location_cond $buyer_id_cond 
		group by a.id, a.benificiary_id, a.invoice_no, a.invoice_date, a.invoice_value, a.net_invo_value";
	}
	if($realized_type==3)
	{
		if($location_id){$location_cond=" and e.location_id=$location_id";}	
		$sql="SELECT e.id as INVOICE_ID, e.benificiary_id as BENIFICIARY_ID, e.invoice_no as INVOICE_NO, e.invoice_date as INVOICE_DATE, e.invoice_value as GROSS_INVOICE_VALUE, e.net_invo_value as NET_INVO_VALUE,a.RECEIVED_DATE
		from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d, com_export_invoice_ship_mst e 
		where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and e.id=d.invoice_id and c.entry_form=40 and a.status_active=1  and c.status_active=1 and d.status_active=1 and e.status_active=1 and e.benificiary_id=$company_id and a.RECEIVED_DATE between '$start_date' and '$end_date' $location_cond $buyer_id ";
	}
	if($realized_type==4) 
	{
		if($location_id){$location_cond=" and a.location_id=$location_id";}
	    $sql="SELECT c.id as INVOICE_ID, c.benificiary_id as BENIFICIARY_ID, c.invoice_no as INVOICE_NO, c.invoice_date as INVOICE_DATE, c.invoice_value as GROSS_INVOICE_VALUE, c.net_invo_value as NET_INVO_VALUE
		from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
		where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form in (39,40) and b.is_converted=0  and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.company_id=$company_id $location_cond $buyer_id_cond   and c.id not in (SELECT d.invoice_id from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and c.entry_form in (39,40) and a.status_active=1  and c.status_active=1 and d.status_active=1 and a.benificiary_id=$company_id $buyer_id_cond) and c.invoice_date  <= to_date('$end_date 23:59:59', 'dd-MM-yyyy hh24:mi:ss')
		group by c.id, c.benificiary_id , c.invoice_no , c.invoice_date, c.invoice_value, c.net_invo_value";

		$doc_bank_sql = "SELECT a.entry_form as ENTRY_FORM, c.id as INVOICE_ID, c.invoice_no as INVOICE_NO,a.SUBMIT_DATE as SUBMIT_DATE
		from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
		where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form in (39,40) and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.company_id=$company_id $location_cond $buyer_id_cond and c.id not in (SELECT d.invoice_id from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and c.entry_form in (39,40) and a.status_active=1  and c.status_active=1 and d.status_active=1 and a.benificiary_id=$company_id $buyer_id_cond)";
		//echo $doc_bank_sql;
	}
	 //echo $sql;

	$result=sql_select($sql); 


	$result_doc_bank_sql=sql_select($doc_bank_sql); 
	foreach($result_doc_bank_sql as $row){
		$doc_sub_date[$row['INVOICE_ID']][$row['ENTRY_FORM']]['SUBMIT_DATE'] = $row['SUBMIT_DATE'];
		$inv_id .= $row['INVOICE_ID'].','; 
	}

	$all_inv_id = ltrim(implode(",", array_unique(explode(",", chop($inv_id, ",")))), ',');

	$ex_fac_sql = "SELECT INVOICE_NO,max(EX_FACTORY_DATE) as EX_FACTORY_DATE  FROM PRO_EX_FACTORY_MST WHERE INVOICE_NO in ($all_inv_id) and status_active = 1 and  is_deleted =0 group by INVOICE_NO";
	//echo $ex_fac_sql;
	$result_ex_fac_sql=sql_select($ex_fac_sql);
	$ex_fac_arr=array();
	foreach($result_ex_fac_sql as $row){
		$ex_fac_arr[$row['INVOICE_NO']]['EX_FACTORY_DATE'] = $row['EX_FACTORY_DATE']; 
	}
	$month_name = date('F',strtotime($start_date));
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML>'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}	
	</script>
	<?
	if($realized_type==2)
	{ ?>
		<fieldset style="width:610px; margin-left:10px">
			<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
			<br />
			<div id="report_container" align="center" style="width:610px">
				<table class="rpt_table" border="1" rules="all" width="610" cellpadding="0" cellspacing="0">
					<thead>
						<tr><th colspan="7"> <?=$title;?></th></tr>
						<tr>
							<th colspan="7"> Month OF-- <?=$month_name;?></th>
						</tr>
						<tr>
							<th width="50">SL No</th>
							<th width="120">Com. Name</th>
							<th width="150">Invoice No</th>
							<th width="60">Invoice Date</th>
							<th width="60">Ex-factory Date</th>
							<th width="80">Inv. Gross Value ($)</th>
							<th >Inv. Net Value ($)</th> 
						</tr>
					</thead>
					<tbody>
						<?
							$i=1;
							foreach($result as $row)  
							{
								if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?=$i;?>">
									<td align="center"><? echo $i; ?></td>
									<td><? echo $com_name[$row['BENIFICIARY_ID']]; ?></td>
									<td><? echo $row['INVOICE_NO']; ?></td>
									<td align="center"><? echo change_date_format($row['INVOICE_DATE']); ?></td>
									<td align="center"><? echo change_date_format($row['EX_FACTORY_DATE']); ?></td>
									<td align="right"><? echo number_format($row['GROSS_INVOICE_VALUE'],2); ?></td>
									<td align="right"><? echo number_format($row['NET_INVO_VALUE'],2); ?></td>
								</tr>
								<?
								$i++;
								$total_gross_invoice_value+=$row['GROSS_INVOICE_VALUE'];
								$total_net_invo_value+=$row['NET_INVO_VALUE'];
							}
						?>
					</tbody>   
					<tfoot>
						<tr>
							<th colspan="5">Total :</th>
							<th><?=number_format($total_gross_invoice_value,2);?></th>
							<th><?=number_format($total_net_invo_value,2);?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
	 <?
	}
	else if($realized_type==3)
	{
		?>
			<fieldset style="width:670px; margin-left:10px">
				<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
				<br />
				<div id="report_container" align="center" style="width:670px">
					<table class="rpt_table" border="1" rules="all" width="670" cellpadding="0" cellspacing="0">
						<thead>
							<tr><th colspan="8"> <?=$title;?></th></tr>
							<tr>
								<th colspan="8"> Month OF-- <?=$month_name;?></th>
							</tr>
							<tr>
								<th width="50">SL No</th>
								<th width="120">Com. Name</th>
								<th width="150">Invoice No</th>
								<th width="60">Invoice Date</th>
								<th width="60">Ex-factory Date</th>
								<th width="60">Realization Date</th>
								<th width="80">Inv. Gross Value ($)</th>
								<th >Inv. Net Value ($)</th> 
							</tr>
						</thead>
						<tbody>
							<?
								$i=1;
								foreach($result as $row)  
								{
									if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?=$i;?>">
										<td align="center"><? echo $i; ?></td>
										<td><? echo $com_name[$row['BENIFICIARY_ID']]; ?></td>
										<td><? echo $row['INVOICE_NO']; ?></td>
										<td align="center"><? echo change_date_format($row['INVOICE_DATE']); ?></td>
										<td align="center"><? echo change_date_format($ex_fac_arr[$row['INVOICE_ID']]['EX_FACTORY_DATE']); ?></td>
										<td align="center"><? echo change_date_format($row['RECEIVED_DATE']); ?></td>
										<td align="right"><? echo number_format($row['GROSS_INVOICE_VALUE'],2); ?></td>
										<td align="right"><? echo number_format($row['NET_INVO_VALUE'],2); ?></td>
									</tr>
									<?
									$i++;
									$total_gross_invoice_value+=$row['GROSS_INVOICE_VALUE'];
									$total_net_invo_value+=$row['NET_INVO_VALUE'];
								}
							?>
						</tbody>   
						<tfoot>
							<tr>
								<th colspan="6">Total :</th>
								<th><?=number_format($total_gross_invoice_value,2);?></th>
								<th><?=number_format($total_net_invo_value,2);?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
		 <?
		
	}
	else if($realized_type==4)
	{
		?>
			<fieldset style="width:750px; margin-left:10px">
				<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
				<br />
				<div id="report_container" align="center" style="width:750px">
					<table class="rpt_table" border="1" rules="all" width="750" cellpadding="0" cellspacing="0">
						<thead>
							<tr><th colspan="10"> <?=$title;?></th></tr>
							<tr>
								<th colspan="10"> Month OF-- <?=$month_name;?></th>
							</tr>
							<tr>
								<th width="50">SL No</th>
								<th width="120">Com. Name</th>
								<th width="150">Invoice No</th>
								<th width="60">Invoice Date</th>
								<th width="60">Ex-factory Date</th>
								<th width="60">Sub.to Buyer Date</th>
								<th width="60">Sub.to Bank Date</th>
								<th width="50">Ageing</th>
								<th width="80">Inv. Gross Value ($)</th>
								<th >Inv. Net Value ($)</th> 
							</tr>
						</thead>
						<tbody>
							<?
								$i=1;
								foreach($result as $row)  
								{
									if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?=$i;?>">
										<td align="center"><? echo $i; ?></td>
										<td><? echo $com_name[$row['BENIFICIARY_ID']]; ?></td>
										<td><? echo $row['INVOICE_NO']; ?></td>
										<td align="center"><? echo change_date_format($row['INVOICE_DATE']); ?></td>
										<td align="center"><? echo change_date_format($ex_fac_arr[$row['INVOICE_ID']]['EX_FACTORY_DATE']); ?></td>
										<td align="center"><? echo change_date_format($doc_sub_date[$row['INVOICE_ID']][39]['SUBMIT_DATE']); ?></td>
										<td align="center"><? echo change_date_format($doc_sub_date[$row['INVOICE_ID']][40]['SUBMIT_DATE']); ?></td>
										<td align="right"><?
										//if($ex_fac_arr[$row['INVOICE_ID']]['EX_FACTORY_DATE']==''){
										if($end_date==''){
											echo "0 days";
										}
										else{
											if($doc_sub_date[$row['INVOICE_ID']][40]['SUBMIT_DATE']==''){
												$diff =   round(abs(strtotime($end_date) - strtotime($doc_sub_date[$row['INVOICE_ID']][39]['SUBMIT_DATE']))/86400);
											}
											else{
												$diff =   round(abs(strtotime($end_date) - strtotime($doc_sub_date[$row['INVOICE_ID']][40]['SUBMIT_DATE']))/86400);
											}
											// $years = floor($diff / (365*60*60*24));
											// $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
											// $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
											echo $diff . " days";
										}
										
										 ?></td>
										<td align="right"><? echo number_format($row['GROSS_INVOICE_VALUE'],2); ?></td>
										<td align="right"><? echo number_format($row['NET_INVO_VALUE'],2); ?></td>
									</tr>
									<?
									$i++;
									$total_gross_invoice_value+=$row['GROSS_INVOICE_VALUE'];
									$total_net_invo_value+=$row['NET_INVO_VALUE'];
								}
							?>
						</tbody>   
						<tfoot>
							<tr>
								<th colspan="8">Total :</th>
								<th><?=number_format($total_gross_invoice_value,2);?></th>
								<th><?=number_format($total_net_invo_value,2);?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
		 <?
		
	}
	else
	{
		?>
		<fieldset style="width:560px; margin-left:10px">
			<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
			<br />
			<div id="report_container" align="center" style="width:560px">
				<table class="rpt_table" border="1" rules="all" width="560" cellpadding="0" cellspacing="0">
					<thead>
						<tr><th colspan="6"> <?=$title;?></th></tr>
						<tr>
							<th colspan="6"> Month OF-- <?=$month_name;?></th>
						</tr>
						<tr>
							<th width="50">SL No</th>
							<th width="120">Com. Name</th>
							<th width="150">Invoice No</th>
							<th width="60">Invoice Date</th>
							<th width="80">Inv. Gross Value ($)</th>
							<th >Inv. Net Value ($)</th> 
						</tr>
					</thead>
					<tbody>
						<?
							$i=1;
							foreach($result as $row)  
							{
								if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?=$i;?>">
									<td align="center"><? echo $i; ?></td>
									<td><? echo $com_name[$row['BENIFICIARY_ID']]; ?></td>
									<td><? echo $row['INVOICE_NO']; ?></td>
									<td align="center"><? echo change_date_format($row['INVOICE_DATE']); ?></td>
									<td align="right"><? echo number_format($row['GROSS_INVOICE_VALUE'],2); ?></td>
									<td align="right"><? echo number_format($row['NET_INVO_VALUE'],2); ?></td>
								</tr>
								<?
								$i++;
								$total_gross_invoice_value+=$row['GROSS_INVOICE_VALUE'];
								$total_net_invo_value+=$row['NET_INVO_VALUE'];
							}
						?>
					</tbody>   
					<tfoot>
						<tr>
							<th colspan="4">Total :</th>
							<th><?=number_format($total_gross_invoice_value,2);?></th>
							<th><?=number_format($total_net_invo_value,2);?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	?>
	<?
    exit();
}


if($action=="invoice_details_v3")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	if($location_id){$location_cond3=" and c.location_id=$location_id";}
	if($location_id){$location_cond=" and a.location_id=$location_id";}
	if($company_id){$company_cond2=" and a.company_id=$company_id";}
	if($company_id){$company_cond=" and a.benificiary_id=$company_id";}
	if($buyer_id){$buyer_id_cond=" and a.buyer_id=$buyer_id";}

    $unrealization_sql_v2="SELECT a.buyer_id as BUYER_ID, a.possible_reali_date as POSSIBLE_REALI_DATE, c.id as INVOICE_ID, c.invoice_quantity as INVOICE_QUANTITY, c.net_invo_value as NET_INVO_VALUE, c.INVOICE_VALUE, 1 as type_id, c.INVOICE_NO, c.IS_LC, c.LC_SC_ID, c.invoice_date as INVOICE_DATE, c.EX_FACTORY_DATE, c.REMARKS
	from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
	where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=39 and b.is_converted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 $company_cond2 $location_cond3 $buyer_id_cond and c.invoice_date between '$start_date' and '$end_date' and c.id not in (SELECT d.invoice_id from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and c.entry_form=40 and a.status_active=1  and c.status_active=1 and d.status_active=1 $company_cond $buyer_id_cond)
	union all 
	SELECT a.buyer_id as BUYER_ID, a.possible_reali_date as POSSIBLE_REALI_DATE, c.id as INVOICE_ID, c.invoice_quantity as INVOICE_QUANTITY, c.net_invo_value as NET_INVO_VALUE, c.INVOICE_VALUE, 2 as type_id, c.INVOICE_NO, c.IS_LC, c.LC_SC_ID, c.invoice_date as INVOICE_DATE, c.EX_FACTORY_DATE, c.REMARKS
	from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
	where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=40 and a.status_active=1 and b.status_active=1 and c.status_active=1 $company_cond2 $location_cond3 $buyer_id_cond and c.invoice_date between '$start_date' and '$end_date' and c.id not in (SELECT d.invoice_id from com_export_proceed_realization a, com_export_doc_submission_mst c, com_export_doc_submission_invo d where a.invoice_bill_id=c.id and c.id=d.doc_submission_mst_id and c.entry_form=40 and a.status_active=1  and c.status_active=1 and d.status_active=1 $company_cond $buyer_id_cond)
	union all  
	SELECT a.buyer_id as BUYER_ID, a.invoice_date as POSSIBLE_REALI_DATE, a.id as INVOICE_ID, a.invoice_quantity as INVOICE_QUANTITY, a.net_invo_value as NET_INVO_VALUE, a.INVOICE_VALUE, 3 as type_id, a.INVOICE_NO, a.IS_LC, a.LC_SC_ID, a.invoice_date as INVOICE_DATE, a.EX_FACTORY_DATE, a.REMARKS
	from  com_export_invoice_ship_mst a
	where  a.status_active=1 $company_cond $location_cond $buyer_id_cond and  a.invoice_date between '$start_date' and '$end_date' and a.id not in (SELECT d.invoice_id from com_export_doc_submission_mst a, com_export_doc_submission_invo d, com_export_doc_submission_mst e where a.id=d.doc_submission_mst_id and e.id=d.doc_submission_mst_id  and a.status_active=1  and a.status_active=1 and d.status_active=1 $company_cond2 $buyer_id_cond)";

	// echo $unrealization_sql_v2;
	$data_arr=sql_select($unrealization_sql_v2); 

	$unrealization_data_arr=$lc_sc_id_arr=array();
	foreach($data_arr as $row){
		$unrealization_data_arr[$row["TYPE_ID"]][$row["INVOICE_NO"]]["BUYER_ID"]=$row["BUYER_ID"];
		$unrealization_data_arr[$row["TYPE_ID"]][$row["INVOICE_NO"]]["POSSIBLE_REALI_DATE"]=$row["POSSIBLE_REALI_DATE"];
		$unrealization_data_arr[$row["TYPE_ID"]][$row["INVOICE_NO"]]["INVOICE_ID"]=$row["INVOICE_ID"];
		$unrealization_data_arr[$row["TYPE_ID"]][$row["INVOICE_NO"]]["INVOICE_QUANTITY"]+=$row["INVOICE_QUANTITY"];
		$unrealization_data_arr[$row["TYPE_ID"]][$row["INVOICE_NO"]]["NET_INVO_VALUE"]+=$row["NET_INVO_VALUE"];
		$unrealization_data_arr[$row["TYPE_ID"]][$row["INVOICE_NO"]]["INVOICE_NO"]=$row["INVOICE_NO"];
		$unrealization_data_arr[$row["TYPE_ID"]][$row["INVOICE_NO"]]["GROSS_INVOICE_VALUE"]+=$row["INVOICE_VALUE"];
		$unrealization_data_arr[$row["TYPE_ID"]][$row["INVOICE_NO"]]["IS_LC"]=$row["IS_LC"];
		$unrealization_data_arr[$row["TYPE_ID"]][$row["INVOICE_NO"]]["LC_SC_ID"]=$row["LC_SC_ID"];
		$unrealization_data_arr[$row["TYPE_ID"]][$row["INVOICE_NO"]]["TYPE_ID"]=$row["TYPE_ID"];
		$unrealization_data_arr[$row["TYPE_ID"]][$row["INVOICE_NO"]]["INVOICE_DATE"]=$row["INVOICE_DATE"];
		$unrealization_data_arr[$row["TYPE_ID"]][$row["INVOICE_NO"]]["EX_FACTORY_DATE"]=$row["EX_FACTORY_DATE"];
		$unrealization_data_arr[$row["TYPE_ID"]][$row["INVOICE_NO"]]["REMARKS"]=$row["REMARKS"];

		if($row["IS_LC"]==1){
			$lc_id_arr[$row["LC_SC_ID"]]=$row["LC_SC_ID"];
	    }else{
			$lc_sc_arr[$row["LC_SC_ID"]]=$row["LC_SC_ID"];
		}
	}
	// echo "<pre>";
	// print_r($unrealization_data_arr);

	$con = connect();
	$rid=execute_query("delete from GBL_TEMP_ENGINE where entry_form=133 and user_id=$user_id");
	if($rid) oci_commit($con);
	if(!empty($lc_id_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 133, 4, $lc_id_arr,$empty_arr);
		$sql_result_data =sql_select("SELECT a.ID, a.EXPORT_LC_NO, a.LC_VALUE, a.PAY_TERM  from  com_export_lc a, gbl_temp_engine d where a.id=d.ref_val and d.user_id= $user_id and d.entry_form=133 and d.REF_FROM=4");
		$lc_data_arr=array();
		foreach($sql_result_data as $row){
			$lc_data_arr[$row["ID"]]["EXPORT_LC_NO"]=$row["EXPORT_LC_NO"];	
			$lc_data_arr[$row["ID"]]["LC_VALUE"]=$row["LC_VALUE"];	
			$lc_data_arr[$row["ID"]]["PAY_TERM"]=$row["PAY_TERM"];	
		}
	 }

	 if(!empty($lc_sc_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 133, 5, $lc_sc_arr,$empty_arr);
		$sql_result_data =sql_select("SELECT a.ID, a.CONTRACT_NO, a.CONTRACT_VALUE, a.PAY_TERM  from  com_sales_contract a, gbl_temp_engine d where a.id=d.ref_val and d.user_id= $user_id and d.entry_form=133 and d.REF_FROM=5");
		$sc_data_arr=array();
		foreach($sql_result_data as $row){
			$sc_data_arr[$row["ID"]]["CONTRACT_NO"]=$row["CONTRACT_NO"];	
			$sc_data_arr[$row["ID"]]["CONTRACT_VALUE"]=$row["CONTRACT_VALUE"];	
			$sc_data_arr[$row["ID"]]["PAY_TERM"]=$row["PAY_TERM"];	
		}
	 }

	// echo "<pre>";
	// print_r($unrealization_data_arr);
	$result=sql_select($unrealization_sql_v2); 
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML>'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<fieldset style="width:1260px; margin-left:10px">
		<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
		<br />
		<div id="report_container" align="center" style="width:1260px">
            <table class="rpt_table" border="2" rules="all" width="1260" cellpadding="0" cellspacing="0">
             	<thead>
					 <tr>
						 <th colspan="14"> Month OF-- <?=$month_year;?></th>
					 </tr>
					 <tr>
						<th width="50">SL No</th>
						<th width="120">Buyer</th>
						<th width="110">Invoice No</th>
						<th width="60">Invoice Date</th>
						<th width="80">Invoice Qty.</th>
						<th width="80">Inv. Gross Value ($)</th>
						<th width="80" >Inv. Net Value ($)</th> 
						<th width="80" >LC/SC</th> 
						<th width="80" >Pay Terms</th> 
						<th width="120" >Lc/SC No</th> 
						<th width="80" >Lc/SC Value</th> 
						<th width="80" >Ex-factory Date</th> 
						<th width="80" >Possible Realize Date</th> 
						<th width="80" >Remarks</th> 
					 </tr>
                </thead>
                <tbody>
					<?
						$total_invoice_quantity=$total_net_invo_value=$total_gross_invoice_value=0;
						$i=1;
						foreach($unrealization_data_arr as $inv_key => $invoice_type_arr)  
						{
							$sub_total_gross_invoice_value=$sub_total_invoice_quantity=$sub_total_net_invo_value=0;
							if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
							$typeId = $inv_key; $text = '';						
							if ($typeId == 1) {
								$text = "Submission To Buyer / Shipping (Copy Docs) :";
							} else if ($typeId == 2) {
								$text = "Submission To Bank / Shipping (Copy Docs):";
							} else if ($typeId == 3) {
								$text = "Un Submitted Invoice (Export Invoice) :";
							}															
							if (!empty($text)) {
								echo '<tr bgcolor="#8ed7c1">';
								echo '<td colspan="14">' . $text . '</td>';
								echo '</tr>';
							}
							foreach($invoice_type_arr as $row)  
							{
						
								if($row[csf('is_lc')]==1)
								{
									$lc_sc_no=$lc_data_arr[$row["LC_SC_ID"]]["EXPORT_LC_NO"];
									$lc_sc_value=$lc_data_arr[$row["LC_SC_ID"]]["LC_VALUE"];
									$pay_tearm=$lc_data_arr[$row["LC_SC_ID"]]["PAY_TERM"];
									$is_lc_sc='LC';
								}
								else
								{
									$lc_sc_no=$sc_data_arr[$row["LC_SC_ID"]]["CONTRACT_NO"];
									$lc_sc_value=$sc_data_arr[$row["LC_SC_ID"]]["CONTRACT_VALUE"];
									$pay_tearm=$sc_data_arr[$row["LC_SC_ID"]]["PAY_TERM"];
									$is_lc_sc='SC';
								}

								if($row[csf('type_id')]==3){							
									$date_rel = date('Y-m-d', strtotime($row['POSSIBLE_REALI_DATE'] . ' +35 days'));
								}else{
									$date_rel=$row['POSSIBLE_REALI_DATE'];
								}
														
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?=$i;?>">
									<td align="center"><? echo $i; ?></td>
									<td><? echo $buyer_library[$row['BUYER_ID']]; ?></td>
									<td><? echo $row['INVOICE_NO']; ?></td>
									<td align="center"><? echo change_date_format($row['INVOICE_DATE']); ?></td>
									<td align="right"><? echo number_format($row['INVOICE_QUANTITY'],2); ?></td>
									<td align="right"><? echo number_format($row['GROSS_INVOICE_VALUE'],2); ?></td>
									<td align="right"><? echo number_format($row['NET_INVO_VALUE'],2); ?></td>
									<td align="center"><? echo $is_lc_sc; ?></td>
									<td align="center"><? echo $pay_term[$pay_tearm]; ?></td>
									<td align="center"><? echo $lc_sc_no; ?></td>
									<td align="center"><? echo $lc_sc_value; ?></td>
									<td align="right"><? echo change_date_format($row["EX_FACTORY_DATE"]); ?></td>
									<td align="right"><? echo change_date_format($date_rel); ?></td>
									<td align="right"><? echo $row["REMARKS"]; ?></td>
								</tr>
								<?
								$i++;
								$total_gross_invoice_value+=$row['GROSS_INVOICE_VALUE'];
								$total_net_invo_value+=$row['NET_INVO_VALUE'];
								$total_invoice_quantity+=$row['INVOICE_QUANTITY'];

								$sub_total_invoice_quantity+=$row['INVOICE_QUANTITY'];
								$sub_total_gross_invoice_value+=$row['GROSS_INVOICE_VALUE'];
								$sub_total_net_invo_value+=$row['NET_INVO_VALUE'];
							
							}
							?>
							 <tr>
								<td align="right" colspan="4"><b>Sub Total :</b></td>
								<td align="right"> <b><?=number_format($sub_total_invoice_quantity,2);?></b></td>
								<td align="right"> <b><?=number_format($sub_total_gross_invoice_value,2);?></b></td>
								<td align="right"><b><?=number_format($sub_total_net_invo_value,2);?></b></td>
								<td colspan="7">:</td>
							</tr>
							<?
						}
					?>
                </tbody>   
				<tfoot>
					<tr>
						<th align="right" colspan="4">Total :</th>
						<th align="right"><?=number_format($total_invoice_quantity,2);?></th>
						<th align="right"><?=number_format($total_gross_invoice_value,2);?></th>
						<th align="right"><?=number_format($total_net_invo_value,2);?></th>
						<th colspan="7"></th>
					</tr>
				</tfoot>
            </table>
		</div>
    </fieldset>
	<?
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=133");
	oci_commit($con);
	disconnect($con);
    exit();
}


if($action=="realization_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$com_name = return_library_array( "SELECT id, company_name from lib_company","id","company_name");

	$sql="SELECT c.company_id as COMPANY_ID, c.bank_ref_no as BILL_NO, c.bank_ref_date as BILL_DATE, sum(b.document_currency) as Bill_VALUE, sum(case when b.type=1 then b.document_currency else 0 end) as REALIZED_VALUE
	from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_mst c
	where a.id=b.mst_id and a.invoice_bill_id=c.id and c.entry_form=40 and a.id in($realization_id) and a.status_active=1 and b.status_active=1 and c.status_active=1 group by c.company_id,c.bank_ref_no,c.bank_ref_date order by c.BANK_REF_DATE";
	// echo $sql;
	$result=sql_select($sql); 
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML>'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<fieldset style="width:560px; margin-left:10px">
		<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
		<br />
		<div id="report_container" align="center" style="width:560px">
            <table class="rpt_table" border="1" rules="all" width="560" cellpadding="0" cellspacing="0">
             	<thead>
					 <tr>
						 <th colspan="6"> Month OF-- <?=$month_year;?></th>
					 </tr>
					 <tr>
						<th width="50">SL No</th>
						<th width="120">Com. Name</th>
						<th width="150">Bill No</th>
						<th width="60">Bill Date</th>
						<th width="80">Bill Value ($)</th>
						<th >Realized Value ($)</th> 
					 </tr>
                </thead>
                <tbody>
					<?
						$i=1;
						foreach($result as $row)  
						{
							if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?=$i;?>">
								<td align="center"><? echo $i; ?></td>
								<td><? echo $com_name[$row['COMPANY_ID']]; ?></td>
								<td><? echo $row['BILL_NO']; ?></td>
								<td align="center"><? echo change_date_format($row['BILL_DATE']); ?></td>
								<td align="right"><? echo number_format($row['BILL_VALUE'],2); ?></td>
								<td align="right"><? echo number_format($row['REALIZED_VALUE'],2); ?></td>
							</tr>
							<?
							$i++;
							$total_bill_value+=$row['BILL_VALUE'];
							$total_realized_value+=$row['REALIZED_VALUE'];
						}
					?>
                </tbody>   
				<tfoot>
					<tr>
						<th colspan="4">Total :</th>
						<th><?=number_format($total_bill_value,2);?></th>
						<th><?=number_format($total_realized_value,2);?></th>
					</tr>
				</tfoot>
            </table>
		</div>
    </fieldset>
	<?
    exit();
}

if($action=="short_realization_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$com_name = return_library_array( "SELECT id, company_name from lib_company","id","company_name");

	$sql="SELECT c.id as BILL_ID,c.company_id as COMPANY_ID, c.bank_ref_no as BILL_NO, c.BANK_REF_DATE as BILL_DATE,b.type as SOURCE_TYPE,b.account_head as ACCOUNT_HEAD, sum(b.document_currency) as DOCUMENT_CURRENCY
	from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_mst c
	where a.id=b.mst_id and a.invoice_bill_id=c.id and c.entry_form=40 and a.id in($realization_id) and a.status_active=1 and b.status_active=1 and c.status_active=1 group by c.id,c.company_id,c.bank_ref_no,c.bank_ref_date,b.type,b.account_head order by c.BANK_REF_DATE";
	// echo $sql;
	$result=sql_select($sql); 
	$all_data_arr=$short_realization_arr=$data_short_realiz_arr=$total_short_realization_arr=array();
	foreach($result as $row)
	{
		$all_data_arr[$row['BILL_ID']]['bill_id']=$row['BILL_ID'];
		$all_data_arr[$row['BILL_ID']]['company_id']=$row['COMPANY_ID'];
		$all_data_arr[$row['BILL_ID']]['bill_no']=$row['BILL_NO'];
		$all_data_arr[$row['BILL_ID']]['bill_date']=$row['BILL_DATE'];
		$all_data_arr[$row['BILL_ID']]['bill_value']+=$row['DOCUMENT_CURRENCY'];
		if($row['SOURCE_TYPE']==0 && $row['ACCOUNT_HEAD']!=194)
		{
			$all_data_arr[$row['BILL_ID']]['short_realized_value']+=$row['DOCUMENT_CURRENCY'];
			$data_short_realiz_arr[$row['BILL_ID']][$row['ACCOUNT_HEAD']]['short_value']+=$row['DOCUMENT_CURRENCY'];
			$short_realization_arr[$row['ACCOUNT_HEAD']]['account_head']=$row['ACCOUNT_HEAD'];
			$short_realization_arr[$row['ACCOUNT_HEAD']]['short_value']+=$row['DOCUMENT_CURRENCY'];
		}
		if($row['SOURCE_TYPE']==1)
		{
			$all_data_arr[$row['BILL_ID']]['realized_value']+=$row['DOCUMENT_CURRENCY'];
		}
	}
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML>'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<p align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
	<br />
	<div id="report_container" align="center" style="width:1070px">
		<table class="rpt_table" border="1" rules="all" width="280" cellpadding="0" cellspacing="0">
			<thead>
					<tr>
						<th colspan="3"> Breakdown of Short Realize</th>
					</tr>
					<tr>
					<th width="50">SL</th>
					<th width="150">Account head</th>
					<th >Value ($)</th>
					</tr>
			</thead>
			<tbody>
				<?
					$i=1;
					foreach($short_realization_arr as $row)  
					{
						if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?=$i;?>">
							<td align="center"><? echo $i; ?></td>
							<td><? echo $commercial_head[$row['account_head']]; ?></td>
							<td align="right"><? echo number_format($row['short_value'],2); ?></td>
						</tr>
						<?
						$i++;$z++;
						$total_short_realized_value+=$row['short_value'];
					}
					$tbl_width=650+($z*100);
				?>
			</tbody>   
			<tfoot>
				<tr>
					<th colspan="2">Total :</th>
					<th><?=number_format($total_short_realized_value,2);?></th>
				</tr>
			</tfoot>
		</table>
		<br>
		<table class="rpt_table" border="1" rules="all" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0">
			<thead>
					<tr>
					<th width="50">SL No</th>
					<th width="120">Com. Name</th>
					<th width="150">Bill No</th>
					<th width="60">Bill Date</th>
					<th width="80">Bill Value ($)</th>
					<th width="80">Realized Value ($)</th> 
					<th >Short Realized Value ($)</th> 
					<?
						foreach($short_realization_arr as $val)
						{
							?>
								<th width="100"><? echo $commercial_head[$val['account_head']]; ?></th> 
							<?
						}
					?>
					</tr>
			</thead>
			<tbody>
				<?
					$i=1;
					foreach($all_data_arr as $row)  
					{
						if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr2_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr2_<?=$i;?>">
							<td align="center"><? echo $i; ?></td>
							<td><? echo $com_name[$row['company_id']]; ?></td>
							<td><? echo $row['bill_no']; ?></td>
							<td align="center"><? echo change_date_format($row['bill_date']); ?></td>
							<td align="right"><? echo number_format($row['bill_value'],2); ?></td>
							<td align="right"><? echo number_format($row['realized_value'],2); ?></td>
							<td align="right"><? echo number_format($row['short_realized_value'],2); ?></td>
							<?
								foreach($short_realization_arr as $val)
								{
									?>
										<td align="right"><? echo number_format($data_short_realiz_arr[$row['bill_id']][$val['account_head']]['short_value'],2); ?></td> 
									<?
									$total_short_realization_arr[$val['account_head']]+=$data_short_realiz_arr[$row['bill_id']][$val['account_head']]['short_value'];
								}
							?>
						</tr>
						<?
						$i++;
						$total_bill_value+=$row['bill_value'];
						$total_realized_value+=$row['realized_value'];
						$total_short_realized_value_popup+=$row['short_realized_value'];
					}
				?>
			</tbody>   
			<tfoot>
				<tr>
					<th colspan="4">Total :</th>
					<th><?=number_format($total_bill_value,2);?></th>
					<th><?=number_format($total_realized_value,2);?></th>
					<th><?=number_format($total_short_realized_value_popup,2);?></th>
					<?
						foreach($short_realization_arr as $val)
						{
							?>
								<th ><?=number_format($total_short_realization_arr[$val['account_head']],2);?></th> 
							<?
						}
					?>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
    exit();
}