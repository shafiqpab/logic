<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (SELECT buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_lein_bank=str_replace("'","",$cbo_lein_bank);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_bill_no=str_replace("'","",$txt_bill_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$report_title=str_replace("'","",$report_title);

	$company_arr=return_library_array( "select company_name,id from  lib_company",'id','company_name');
	$bank_arr=return_library_array( "select bank_name,id from lib_bank",'id','bank_name');
	$buyer_arr=return_library_array( "select id,buyer_name from  lib_buyer",'id','buyer_name');
	$sql_cond=""; 
	if($cbo_company_name>0) $sql_cond=" and a.company_name=$cbo_company_name" ;
	if($cbo_lein_bank>0) $sql_cond.=" and c.lien_bank=$cbo_lein_bank" ;
	if($cbo_buyer_name>0) $sql_cond.=" and a.buyer_name=$cbo_buyer_name";
	if(trim($txt_bill_no)!="") $sql_cond.=" and f.bank_ref_no='$txt_bill_no'";
	if($txt_date_from!="" && $txt_date_to!="") $sql_cond.=" and h.received_date between '$txt_date_from' and '$txt_date_to'";
	// NET_INVO_VALUE,INVOICE_QUANTITY
	if($db_type==0)
	{
		$sql_clm="group_concat(distinct(a.style_owner)) as WORKING_COMPANY_ID, group_concat(distinct(a.style_ref_no)) as STYLE_REF_NO";
	}
	else
	{
		$sql_clm="rtrim(xmlagg(xmlelement(e,a.style_owner,',').extract('//text()') order by a.id).GetClobVal(),',') as WORKING_COMPANY_ID, rtrim(xmlagg(xmlelement(e,a.style_ref_no,',').extract('//text()') order by a.id).GetClobVal(),',') as STYLE_REF_NO";
	}
	$sql="SELECT a.COMPANY_NAME, $sql_clm, a.BUYER_NAME, c.LIEN_BANK, d.INVOICE_NO, d.INVOICE_QUANTITY, d.NET_INVO_VALUE, f.id as INVOICE_BILL_ID, f.POSSIBLE_REALI_DATE, f.BANK_REF_NO as BILL_NO, h.id as RLZ_ID, h.received_date as RLZ_DATE, h.REMARKS
	from wo_po_details_master a, wo_po_break_down b, com_export_lc c, com_export_invoice_ship_mst d, com_export_invoice_ship_dtls e, com_export_doc_submission_mst f, com_export_doc_submission_invo g, com_export_proceed_realization h
	where a.id=b.job_id and c.id=d.lc_sc_id and d.is_lc=1 and d.id=e.mst_id and b.id=e.po_breakdown_id and f.id=g.doc_submission_mst_id and d.id=g.invoice_id and f.id=h.invoice_bill_id and g.is_lc=1 and c.id=g.lc_sc_id $sql_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 and h.status_active=1 and h.is_invoice_bill=1
	group by a.company_name,a.buyer_name,c.lien_bank,d.invoice_no,d.invoice_quantity,d.net_invo_value,f.id,f.possible_reali_date,f.bank_ref_no,h.id,h.received_date,h.remarks
	union all
	SELECT  a.COMPANY_NAME, $sql_clm, a.BUYER_NAME, c.LIEN_BANK, d.INVOICE_NO, d.INVOICE_QUANTITY, d.NET_INVO_VALUE, f.id as INVOICE_BILL_ID, f.POSSIBLE_REALI_DATE, f.BANK_REF_NO as BILL_NO, h.id as RLZ_ID, h.received_date as RLZ_DATE, h.REMARKS
	from wo_po_details_master a, wo_po_break_down b, com_sales_contract c, com_export_invoice_ship_mst d, com_export_invoice_ship_dtls e, com_export_doc_submission_mst f, com_export_doc_submission_invo g, com_export_proceed_realization h
	where a.id=b.job_id and c.id=d.lc_sc_id and d.is_lc=2 and d.id=e.mst_id and b.id=e.po_breakdown_id and f.id=g.doc_submission_mst_id and d.id=g.invoice_id and f.id=h.invoice_bill_id and g.is_lc=2 and c.id=g.lc_sc_id $sql_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 and h.status_active=1 and h.is_invoice_bill=1
	group by a.company_name,a.buyer_name,c.lien_bank,d.invoice_no,d.invoice_quantity,d.net_invo_value,f.id,f.possible_reali_date,f.bank_ref_no,h.id,h.received_date,h.remarks
	";
	// echo $sql;die;

	$result=sql_select($sql);
	$invoice_bill_id_arr=array();$invoice_bill_value=array();
	foreach($result as $row)
	{
		$invoice_bill_id_arr[$row["RLZ_ID"]]=$row["RLZ_ID"];
		$invoice_bill_value[$row["INVOICE_BILL_ID"]]+=$row["NET_INVO_VALUE"];
	}
	$invoice_bill_id_in=where_con_using_array($invoice_bill_id_arr,0,'a.id');

	$rez_sql="SELECT a.ID, a.is_invoice_bill, b.TYPE, b.ACCOUNT_HEAD, b.DOCUMENT_CURRENCY, b.DOMESTIC_CURRENCY
	from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b
	where a.id=b.mst_id and a.is_invoice_bill=1 and a.status_active=1 and b.status_active=1 $invoice_bill_id_in";
	// echo $rez_sql;die;

	$rez_data=sql_select($rez_sql);
	$rez_data_arr=array();
	foreach($rez_data as $row)
	{
		if($row[csf("type")]==0)
		{
			$rez_data_arr[$row["ID"]]["DEDUCTION_TOT_VALUE"]+=$row["DOCUMENT_CURRENCY"];
			$rez_data_arr[$row["ID"]]["ACCOUNT_HEAD"].=$commercial_head[$row["ACCOUNT_HEAD"]].", ";
		}
		else
		{
			$rez_data_arr[$row["ID"]]["DISTRIBUTION_TOT_VALUE"]+=$row["DOCUMENT_CURRENCY"];
		}
	}

	$tbl_width=1600;
	ob_start();
	?>
	<style>.wrd_brk{word-break: break-all;}</style>
	<div style="width:<?=$tbl_width+20;?>px;" id="">
		<p style="font-size:16px; font-weight:bold"><? echo $report_title; ?></p>
		<table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
			<thead>
				<tr>
					<th width="100">LC Company Name</th>
					<th width="100">Working Company</th>
					<th width="100">Buyers Name</th>
					<th width="100">Bank</th>
					<th width="100">Invoice Number</th>
					<th width="100">Style Ref.</th>
					<th width="100">Qty</th>
					<th width="100">Invoice Value [$]</th>
					<th width="100">Approx. Realize Date</th>
					<th width="100">Actual Realize Date</th>
					<th width="100">Realized Value</th>
					<th width="100">Short Realized</th>
					<th width="100">Short Amount [%]</th>
					<th width="100">Reason of Short Realized</th>
					<th width="100">Bill No.</th>
					<th >Remarks</th>
				</tr>
			</thead>
			<tbody>
				<?
				$i=1;
				$tot_deduction=array();$tot_distribution=array();
				foreach($result as $row)
				{
					if($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
					$percentage=$row["NET_INVO_VALUE"]/$invoice_bill_value[$row["INVOICE_BILL_ID"]];
					$working_company_arr=array_unique(explode(",",$row["WORKING_COMPANY_ID"]->load()));
					$working_company_name="";
					foreach($working_company_arr as $val)
					{
						$working_company_name.=$company_arr[$val].", ";
					}
					?>
					<tr bgcolor="<? echo $bgcolor?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="100" class="wrd_brk"><p><? echo $company_arr[$row["COMPANY_NAME"]]; ?></p></td>
						<td width="100" class="wrd_brk"><p><? echo rtrim($working_company_name,", "); ?></p></td>
						<td width="100" class="wrd_brk"><p><? echo $buyer_arr[$row["BUYER_NAME"]]; ?></p></td>
						<td width="100" class="wrd_brk"><p><? echo $bank_arr[$row["LIEN_BANK"]]; ?></p></td>
						<td width="100" class="wrd_brk"><p><? echo $row["INVOICE_NO"]; ?></p></td>
						<td width="100" class="wrd_brk"><p><? echo implode(", ",array_unique(explode(",",$row["STYLE_REF_NO"]->load()))); ?></p></td>
						<td width="100" align="right"><? echo number_format($row["INVOICE_QUANTITY"],2); ?></td>
						<td width="100" align="right"><? echo number_format($row["NET_INVO_VALUE"],2); ?></td>
						<td width="100" align="center"><p><? echo change_date_format($row["POSSIBLE_REALI_DATE"]); ?></p></td>
						<td width="100" align="center"><p><? echo change_date_format($row["RLZ_DATE"]); ?></p></td>
						<td width="100" align="right"><p><? echo number_format($rez_data_arr[$row["RLZ_ID"]]["DISTRIBUTION_TOT_VALUE"]*$percentage,2); ?></p></td>
						<td width="100" align="right"><p><? echo number_format($rez_data_arr[$row["RLZ_ID"]]["DEDUCTION_TOT_VALUE"]*$percentage,2); ?></p></td>
						<td width="100" align="right"><p><? echo number_format(($rez_data_arr[$row["RLZ_ID"]]["DEDUCTION_TOT_VALUE"]*$percentage)/$row["NET_INVO_VALUE"],2); ?></p></td>
						<td width="100" class="wrd_brk"><p><? echo rtrim($rez_data_arr[$row["RLZ_ID"]]["ACCOUNT_HEAD"],", ") ?></p></td>
						<td width="100" class="wrd_brk"><p><? echo $row["BILL_NO"]; ?></p></td>
						<td class="wrd_brk"><p><? echo $row["REMARKS"]; ?></p></td>
					</tr>
					<?
					$i++;		
					$tot_invoice_qnty+=$row["INVOICE_QUANTITY"];
					$tot_invoice_value+=$row["NET_INVO_VALUE"];
					$tot_realize_value+=$rez_data_arr[$row["RLZ_ID"]]["DISTRIBUTION_TOT_VALUE"]*$percentage;
					$tot_short_value+=$rez_data_arr[$row["RLZ_ID"]]["DEDUCTION_TOT_VALUE"]*$percentage;
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>TOTAL&nbsp;</th>
					<th><?=number_format($tot_invoice_qnty,2);?></th>
					<th><?=number_format($tot_invoice_value,2);?></th>
					<th></th>
					<th></th>
					<th><?=number_format($tot_realize_value,2);?></th>
					<th><?=number_format($tot_short_value,2);?></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

?>
