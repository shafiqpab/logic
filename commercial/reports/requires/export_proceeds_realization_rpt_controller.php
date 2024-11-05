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
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
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

	//echo $hide_year;die;
	$company_arr=return_library_array( "select company_name,id from  lib_company",'id','company_name');
	$bank_arr=return_library_array( "select bank_name,id from lib_bank",'id','bank_name');
	$buyer_arr=return_library_array( "select id,buyer_name from  lib_buyer",'id','buyer_name');
	$sql_cond=""; $sql_cond2="";
	if($cbo_lein_bank>0) $sql_cond=" and a.lien_bank=$cbo_lein_bank" ;
	if($cbo_lein_bank>0) $sql_cond2=" and d.lien_bank=$cbo_lein_bank";

	if($cbo_buyer_name>0) $sql_cond.=" and a.buyer_id=$cbo_buyer_name";
	if($cbo_buyer_name>0) $sql_cond2.=" and c.buyer_id=$cbo_buyer_name";
	if(trim($txt_bill_no)!="") $sql_cond.=" and a.bank_ref_no='$txt_bill_no'";

	if($txt_date_from!="" && $txt_date_to!="") $sql_cond.=" and b.received_date between '$txt_date_from' and '$txt_date_to'";
	if($txt_date_from!="" && $txt_date_to!="") $sql_cond2.=" and e.received_date between '$txt_date_from' and '$txt_date_to'";

		if($txt_bill_no!='')
		{
			$bil_caption='Bill No';
			$sql="select a.id as bill_inv_id, a.company_id, a.lien_bank, a.buyer_id, a.bank_ref_no, b.id as rlz_id, b.received_date as rlz_date,b.is_invoice_bill, c.type, c.account_head, c.document_currency, c.domestic_currency from com_export_doc_submission_mst a, com_export_proceed_realization b, com_export_proceed_rlzn_dtls c where a.id=b.invoice_bill_id and b.id=c.mst_id and b.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_name $sql_cond order by rlz_date";
		}
		else
		{
			$bil_caption='Bill No / Invoice No';
			$sql="select a.id as bill_inv_id, a.company_id, a.lien_bank, a.buyer_id, a.bank_ref_no, b.id as rlz_id, b.received_date as rlz_date,b.is_invoice_bill, c.type, c.account_head, c.document_currency, c.domestic_currency
		from com_export_doc_submission_mst a, com_export_proceed_realization b, com_export_proceed_rlzn_dtls c
		where a.id=b.invoice_bill_id and b.id=c.mst_id and b.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_name $sql_cond
		union all
		select c.id as bill_inv_id, c.benificiary_id as company_id, d.lien_bank, c.buyer_id, c.invoice_no as bank_ref_no,e.id as rlz_id, e.received_date as rlz_date,e.is_invoice_bill, f.type, f.account_head, f.document_currency, f.domestic_currency
		from com_export_invoice_ship_mst c, com_sales_contract d , com_export_proceed_realization e, com_export_proceed_rlzn_dtls f
		where c.lc_sc_id = d.id  and e.invoice_bill_id=c.id and e.id=f.mst_id and d.pay_term = 3 and e.is_invoice_bill=2 and c.status_active=1 and c.is_deleted=0  and c.benificiary_id=$cbo_company_name $sql_cond2 order by rlz_date";
		}



	//echo $sql;//die;
	$result=sql_select($sql);
	$result_data=array();$count_collspan_deduction=array();$count_collspan_distribution=array();$rlz_data=array();
	foreach($result as $row)
	{
		$result_data[$row[csf("is_invoice_bill")]][$row[csf("bill_inv_id")]]["company_id"]		=$row[csf("company_id")];
		$result_data[$row[csf("is_invoice_bill")]][$row[csf("bill_inv_id")]]["lien_bank"]		=$row[csf("lien_bank")];
		$result_data[$row[csf("is_invoice_bill")]][$row[csf("bill_inv_id")]]["buyer_id"]		=$row[csf("buyer_id")];
		$result_data[$row[csf("is_invoice_bill")]][$row[csf("bill_inv_id")]]["bank_ref_no"]		=$row[csf("bank_ref_no")];
		$result_data[$row[csf("is_invoice_bill")]][$row[csf("bill_inv_id")]]["rlz_id"]			=$row[csf("rlz_id")];
		$result_data[$row[csf("is_invoice_bill")]][$row[csf("bill_inv_id")]]["rlz_date"]		=$row[csf("rlz_date")];
		$result_data[$row[csf("is_invoice_bill")]][$row[csf("bill_inv_id")]]["type"]			=$row[csf("type")];
		$result_data[$row[csf("is_invoice_bill")]][$row[csf("bill_inv_id")]]["document_currency"]+=$row[csf("document_currency")];
		$result_data[$row[csf("is_invoice_bill")]][$row[csf("bill_inv_id")]]["domestic_currency"]+=$row[csf("domestic_currency")];
		$result_data[$row[csf("is_invoice_bill")]][$row[csf("bill_inv_id")]]["is_invoice_bill"]	=$row[csf("is_invoice_bill")];
		$result_data[$row[csf("is_invoice_bill")]][$row[csf("bill_inv_id")]]["bill_inv_id"]		=$row[csf("bill_inv_id")];

		if($row[csf("type")]==0){
			$result_data[$row[csf("is_invoice_bill")]][$row[csf("bill_inv_id")]]["deduction_tot_value"]+=$row[csf("document_currency")];
		}else{
			$result_data[$row[csf("is_invoice_bill")]][$row[csf("bill_inv_id")]]["distribution_tot_value"]+=$row[csf("document_currency")];
		}

		if($row[csf("type")]==0)
		{
			$count_collspan_deduction[$row[csf("account_head")]]=$row[csf("account_head")];
		}
		else
		{
			$count_collspan_distribution[$row[csf("account_head")]]=$row[csf("account_head")];
		}

		$rlz_data[$row[csf("is_invoice_bill")]][$row[csf("bill_inv_id")]][$row[csf("type")]][$row[csf("account_head")]]["document_currency"]+=$row[csf("document_currency")];
		$rlz_data[$row[csf("is_invoice_bill")]][$row[csf("bill_inv_id")]][$row[csf("type")]][$row[csf("account_head")]]["domestic_currency"]+=$row[csf("domestic_currency")];
	}

	//echo "<pre>"; print_r($result_data);
	//echo " deduct <pre>"; print_r($count_collspan_deduction); echo " distri <pre>"; print_r($count_collspan_distribution);echo " rlz <pre>"; print_r($rlz_data);die;
	$table_width=1150+(240*count($count_collspan_deduction))+(240*count($count_collspan_distribution));
	$dib_width=$table_width+20;
	ob_start();
?>
<div style="width:<? echo $dib_width?>px;" id="">
	<p style="font-size:16px; font-weight:bold"><? echo $report_title; ?></p>
    <table width="<? echo $table_width?>" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
        <thead>
            <tr>
                <th width="30" rowspan="3">SL</th>
                <th width="140" rowspan="3">Beneficiary</th>
                <th width="130" rowspan="3">Lien Bank</th>
                <th width="130" rowspan="3">Buyer</th>
                <th width="100" rowspan="3"><? echo $bil_caption ?></th>
                <th width="80" rowspan="3">Realization Date</th>
                <th width="100" rowspan="3">RealizationValue</th>
                <th width="100" rowspan="3">Deduction Value</th>
                <th width="100" rowspan="3">Distribution Value</th>
                <th width="100" rowspan="3">Convertion Rate</th>
                <th colspan="<? echo count($count_collspan_deduction)*3;?>">Deduction</th>
                <th colspan="<? echo count($count_collspan_distribution)*3;?>">Distribution</th>
                <th width="100" rowspan="3">Domestic currency</th>
            </tr>
            <tr>
            	<?
					foreach($count_collspan_deduction as $head_id)
					{
						?>
                        <th width="240" colspan="3"><? echo $commercial_head[$head_id]; ?></th>
                        <?
					}
					foreach($count_collspan_distribution as $head_id)
					{
						?>
                        <th width="240" colspan="3"><? echo $commercial_head[$head_id]; ?></th>
                        <?
					}
				?>
            </tr>
            <tr>
            	<?
					foreach($count_collspan_deduction as $head_id)
					{
						?>
                        <th width="80">Document</th>
                        <th width="80">Conversion Rate</th>
                        <th width="80">Domestic</th>
                        <?
					}
					foreach($count_collspan_distribution as $head_id)
					{
						?>
                        <th width="80">Document</th>
                        <th width="80">Conversion Rate</th>
                        <th width="80">Domestic</th>
                        <?
					}
				?>
            </tr>
        </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:<? echo $dib_width;?>px;" id="scroll_body">
         <table cellspacing="0" border="1" class="rpt_table"  width="<? echo $table_width;?>" rules="all" id="table_body" >
        <tbody>
        <?
		$i=1;
		$tot_deduction=array();$tot_distribution=array();
		foreach($result_data as $is_invoice_bill=>$result_datas)
		{
			foreach($result_datas as $bill_id=>$b_row)
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				$conver_rate=$b_row["domestic_currency"]/$b_row["document_currency"];
				?>
	            <tr bgcolor="<? echo $bgcolor?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	            	<td width="30" align="center"><? echo $i; ?></td>
	                <td width="140"><p><? echo $company_arr[$b_row["company_id"]]; ?></p></td>
	                <td width="130"><p><? echo $bank_arr[$b_row["lien_bank"]]; ?>&nbsp;</p></td>
	                <td width="130"><p><? echo $buyer_arr[$b_row["buyer_id"]]; ?>&nbsp;</p></td>
	                <td width="100" align="center"><p><a href="##" onclick="openmypage_inv('<? echo $bill_id; ?>','<? echo $is_invoice_bill; ?>','inv_details')"><? echo $b_row["bank_ref_no"]; ?></a>&nbsp;</p></td>
	                <td width="80" align="center"><p><? if($b_row["rlz_date"]!="" && $b_row["rlz_date"]!="0000-00-00") echo change_date_format($b_row["rlz_date"]); ?>&nbsp;</p></td>
	                <td width="100" align="right"><? echo number_format($b_row["document_currency"],4); $tot_document_currency+=$b_row["document_currency"]; ?></td>
	                <td width="100" align="right"><? echo number_format($b_row["deduction_tot_value"],4); $tot_deduction_value+=$b_row["deduction_tot_value"]; ?></td>
	                <td width="100" align="right"><? echo number_format($b_row["distribution_tot_value"],4); $tot_distribution_value+=$b_row["distribution_tot_value"]; ?></td>
	                <td width="100" align="right"><? echo number_format($conver_rate,4); ?></td>
	                <?
					foreach($count_collspan_deduction as $head_id)
					{
						?>
						<td width="80" align="right"><? echo number_format($rlz_data[$is_invoice_bill][$bill_id][0][$head_id]["document_currency"],4); ?></td>
	                    <td width="80" align="right"><? if($rlz_data[$is_invoice_bill][$bill_id][0][$head_id]["domestic_currency"]>0 && $rlz_data[$is_invoice_bill][$bill_id][0][$head_id]["document_currency"]>0) echo number_format($rlz_data[$is_invoice_bill][$bill_id][0][$head_id]["domestic_currency"]/$rlz_data[$is_invoice_bill][$bill_id][0][$head_id]["document_currency"],4); ?></td>
	                    <td width="80" align="right"><? echo number_format($rlz_data[$is_invoice_bill][$bill_id][0][$head_id]["domestic_currency"],4); ?></td>
						<?
						$tot_deduction[$head_id]["document_currency"]+=$rlz_data[$is_invoice_bill][$bill_id][0][$head_id]["document_currency"];
						$tot_deduction[$head_id]["domestic_currency"]+=$rlz_data[$is_invoice_bill][$bill_id][0][$head_id]["domestic_currency"];
					}
					foreach($count_collspan_distribution as $head_id)
					{
						?>
						<td width="80" align="right"><? echo number_format($rlz_data[$is_invoice_bill][$bill_id][1][$head_id]["document_currency"],4); ?></td>
	                    <td width="80" align="right"><? if($rlz_data[$is_invoice_bill][$bill_id][1][$head_id]["domestic_currency"]>0 && $rlz_data[$is_invoice_bill][$bill_id][1][$head_id]["document_currency"]>0) echo number_format($rlz_data[$is_invoice_bill][$bill_id][1][$head_id]["domestic_currency"]/$rlz_data[$is_invoice_bill][$bill_id][1][$head_id]["document_currency"],4); ?></td>
	                    <td  width="80" align="right"><? echo number_format($rlz_data[$is_invoice_bill][$bill_id][1][$head_id]["domestic_currency"],4); ?></td>
						<?
						$tot_distribution[$head_id]["document_currency"]+=$rlz_data[$is_invoice_bill][$bill_id][1][$head_id]["document_currency"];
						$tot_distribution[$head_id]["domestic_currency"]+=$rlz_data[$is_invoice_bill][$bill_id][1][$head_id]["domestic_currency"];
					}
					?>
	                <td width="100" align="right"><? echo number_format($b_row["domestic_currency"],4); $tot_domestric_currency+=$b_row["domestic_currency"]; ?></td>
	            </tr>
	            <?
				$i++;
			}
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
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($tot_document_currency,4); ?></th>
                <th><? echo number_format($tot_deduction_value,4); ?></th>
                <th><? echo number_format($tot_distribution_value,4); ?></th>
                <th>&nbsp;</th>
                <?
					foreach($count_collspan_deduction as $head_id)
					{
						?>
                        <th align="right"><? echo number_format($tot_deduction[$head_id]["document_currency"],4); ?></th>
                        <th align="right">&nbsp;</th>
                        <th align="right"><? echo number_format($tot_deduction[$head_id]["domestic_currency"],4); ?></th>
                        <?
					}
					foreach($count_collspan_distribution as $head_id)
					{
						?>
                        <th align="right"><? echo number_format($tot_distribution[$head_id]["document_currency"],4); ?></th>
                        <th align="right"><? echo number_format($tot_distribution[$head_id],2); ?></th>
                        <th align="right"><? echo number_format($tot_distribution[$head_id]["domestic_currency"],4); ?></th>
                        <?
					}
				?>
                <th align="right"><? echo number_format($tot_domestric_currency,4); ?></th>
            </tr>
        </tfoot>
    </table>
    </div>
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

if($action=="inv_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($bil_id=="") die;
	//echo $bil_id;die;
?>

<script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}
</script>
<!--<div style="width:660px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
<div style="width:510px" id="report_container">
    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500">
        <thead>
        	<tr>
                <th width="50">SL NO</th>
                <th width="130">Invoice No</th>
                <th width="80">Invoie Date</th>
                <th width="110">Invoice Qty</th>
                <th>Invoice Value</th>
            </tr>
        </thead>
        <tbody>
		<?
		if($is_invoice_bill==1)
		{
			$inv_sql="select d.id as invoice_id, d.invoice_no, d.invoice_date, d.invoice_quantity, d.net_invo_value
		from
			com_export_doc_submission_invo c, com_export_invoice_ship_mst d
		where
			c.invoice_id=d.id and c.doc_submission_mst_id=$bil_id and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1
		group by d.id, d.invoice_no, d.invoice_date, d.invoice_quantity, d.net_invo_value";
		}
		else
		{
			$inv_sql="select d.id as invoice_id, d.invoice_no, d.invoice_date, d.invoice_quantity, d.net_invo_value
		from
			com_export_invoice_ship_mst d
		where
			d.id=$bil_id and d.is_deleted=0 and d.status_active=1
		group by d.id, d.invoice_no, d.invoice_date, d.invoice_quantity, d.net_invo_value";
		}


		//echo $inv_sql;die;
		$i=1;
        $sql_re=sql_select($inv_sql);
        foreach($sql_re as $row)
        {
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><p><?  echo $row[csf('invoice_no')]; ?>&nbsp;</p></td>
				<td align="center"><? if($row[csf('invoice_date')]!="" && $row[csf('invoice_date')]!="0000-00-00") echo change_date_format($row[csf('invoice_date')]); ?></td>
				<td align="right"><?  echo number_format($row[csf('invoice_quantity')],2); $total_invoice_quantity+=$row[csf('invoice_quantity')]; ?></td>
				<td align="right"><?  echo number_format($row[csf('net_invo_value')],2); $total_net_invo_value+=$row[csf('net_invo_value')]; ?></td>
			</tr>
			<?
			$i++;

        }
        ?>
        </tbody>
        <tfoot>
            <tr >
                <th align="right">&nbsp;</th>
                <th align="right" >&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_invoice_quantity,2); ?></th>
                <th align="right"><? echo number_format($total_net_invo_value,2); ?></th>
            </tr>
        </tfoot>
    </table>
</div>
<?
exit();
}
?>
