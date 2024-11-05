<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$seource_des_array=array(3=>"Non EPZ",4=>"Non EPZ",5=>"Abroad",6=>"Abroad",11=>"EPZ",12=>"EPZ");

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_lein_bank=str_replace("'","",$cbo_lein_bank); 
	$txt_exchange_rate=str_replace("'","",$txt_exchange_rate);
	//echo $hide_year;die;
	$company_arr=return_library_array( "select company_name,id from  lib_company",'id','company_name');
	$bank_arr=return_library_array( "select bank_name,id from lib_bank",'id','bank_name');
	
	/*$lc_sc_sql="select id as lc_sc_id, lien_bank, convertible_to_lc, contract_value as lc_sc_value, converted_from, 1 as type from com_sales_contract where beneficiary_name=$cbo_company_name and lien_bank=$cbo_lein_bank and sc_year<='$hide_year' and sc_year>'2015'
	union all
	select id as lc_sc_id, lien_bank, replacement_lc as convertible_to_lc, lc_value as lc_sc_value, null as converted_from, 2 as type from com_export_lc where beneficiary_name=$cbo_company_name and lien_bank=$cbo_lein_bank and lc_year<='$hide_year' and lc_year>'2015'";
	
	//echo $lc_sc_sql;
	//where beneficiary_name=$cbo_company_name and lien_bank=$cbo_lein_bank and lc_year='$hide_year'
	//echo $lc_sc_sql;//die;
	
	$lc_sc_sql_result=sql_select($lc_sc_sql);
	$lc_id=$sc_id="";
	$file_value=$sc_value_1_3=$lc_value_1=$sc_value_2=$lc_value_2=0;
	foreach($lc_sc_sql_result as $row)
	{
		if($row[csf("type")]==1)
		{
			$sc_id.=$row[csf("lc_sc_id")].",";
			if($row[csf("convertible_to_lc")]!=2)
			{
				$sc_value_1_3+=$row[csf("lc_sc_value")];
			}
			else
			{
				if($row[csf("converted_from")]>0)
				{
					$lc_value_1+=$row[csf("lc_sc_value")];
				}
				else
				{
					$sc_value_2+=$row[csf("lc_sc_value")];
				}
			}
		}
		else
		{
			$lc_id.=$row[csf("lc_sc_id")].",";
			if($row[csf("convertible_to_lc")]==2)
			{
				$lc_value_2+=$row[csf("lc_sc_value")];
			}
			else
			{
				$lc_value_1+=$row[csf("lc_sc_value")];
			}
		}
	}
	//$file_value=(($sc_value_1_3-$lc_value_1)+$sc_value_2+$lc_value_2+$lc_value_1);
	$file_value=($sc_value_1_3+$sc_value_2+$lc_value_2);
	$sc_id=chop($sc_id,",");
	$lc_id=chop($lc_id,",");
	if($sc_id!="")
	{
		$sc_id_arr=array_unique(explode(",",$sc_id));
		if($db_type==0)
		{
			$rlz_sc_cond=" and a.lc_sc_id in(".implode(",",$sc_id_arr).")";
		}
		else
		{
			$sc_id_arr=array_chunk($sc_id_arr,999);
			$rlz_sc_cond=" and (";
			foreach($sc_id_arr as $id_arr)
			{
				$rlz_sc_cond.="a.lc_sc_id in(".implode(",",$id_arr).") or";
			}
			$rlz_sc_cond=chop($rlz_sc_cond,"or");
			$rlz_sc_cond.=")";
			//echo $rlz_sc_cond;die;
		}
	}
	
	if($lc_id!="")
	{
		$lc_id_arr=array_unique(explode(",",$lc_id));
		if($db_type==0)
		{
			$rlz_lc_cond=" and a.lc_sc_id in(".implode(",",$lc_id_arr).")";
		}
		else
		{
			$lc_id_arr=array_chunk($lc_id_arr,999);
			$rlz_lc_cond=" and (";
			foreach($lc_id_arr as $id_arr)
			{
				$rlz_lc_cond.="a.lc_sc_id in(".implode(",",$id_arr).") or";
			}
			$rlz_lc_cond=chop($rlz_lc_cond,"or");
			$rlz_lc_cond.=")";
		}
	}
	if($lc_id=="" && $sc_id=="") die;*/
	
	$sql_cond_payment="";
	if($cbo_company_name>0) $sql_cond_payment=" and a.company_id=$cbo_company_name";
	$sql_payment="select a.lc_id, a.invoice_id, b.accepted_ammount from com_import_payment_mst a, com_import_payment b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $sql_cond_payment";
	$sql_payment_result=sql_select($sql_payment);
	$lc_wise_payment=array();$invoice_wise_payment=array();
	foreach($sql_payment_result as $row)
	{
		$lc_wise_payment[$row[csf("lc_id")]]+=$row[csf("accepted_ammount")];
		$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")];
	}
	unset($sql_payment_result);
	$sql_cond="";
	if($cbo_company_name>0) $sql_cond=" and a.importer_id=$cbo_company_name";
	if($cbo_lein_bank>0) $sql_cond.=" and a.issuing_bank_id=$cbo_lein_bank";
	
	$btb_sql="select a.id, a.importer_id, a.issuing_bank_id, a.lc_value from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.ref_closing_status<>1 $sql_cond order by a.importer_id, a.issuing_bank_id";
	$btb_sql_result=sql_select($btb_sql);
	$btb_data=array();$all_btb_company=array();$all_btb_bank=array();
	foreach($btb_sql_result as $row)
	{
		$all_btb_company[$row[csf("importer_id")]]=$row[csf("importer_id")];
		$all_btb_bank[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]=$row[csf("issuing_bank_id")];
		$btb_data[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]+=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
		$btb_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
	}
	unset($btb_sql_result);
	$ifdbc_edf_sql="select a.id as btb_lc_id, a.importer_id, a.issuing_bank_id, a.lc_category, a.lc_value, a.maturity_from_id, b.id as import_inv_id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
	from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
	where a.id=c.btb_lc_id and c.import_invoice_id=b.id  and a.lc_category>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 $sql_cond 
	group by a.id, a.importer_id, a.issuing_bank_id, a.lc_category, a.lc_value, a.maturity_from_id, b.id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date";
	//echo $ifdbc_edf_sql;die;
	$ifdbc_edf_sql_result=sql_select($ifdbc_edf_sql);
	$ifdbc_edf_data=array();
	foreach($ifdbc_edf_sql_result as $row)
	{
		if(abs($row[csf("lc_category")]) != 1 && abs($row[csf("lc_category")]) != 3 && abs($row[csf("lc_category")]) != 5 && abs($row[csf("lc_category")])!= 11)
		{
			if($row[csf("bank_acc_date")] != "" && $row[csf("bank_acc_date")] != "0000-00-00")
			{
				$ifdbc_edf_data[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]["ifdbc"]+=$row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]];
				$ifdbc_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]];
			}
		}
		else
		{
			$condition_date="";$edf_paid_value=0;
			if($row[csf("maturity_from_id")]==1)
			{
				$condition_date=$row[csf("bank_acc_date")];
			}
			else if($row[csf("maturity_from_id")]==2 || $row[csf("maturity_from_id")]==5)
			{
				$condition_date=$row[csf("shipment_date")];
			}
			else if($row[csf("maturity_from_id")]==3)
			{
				$condition_date=$row[csf("nagotiate_date")];
			}
			else if($row[csf("maturity_from_id")]==4)
			{
				$condition_date=$row[csf("bill_date")];
			}
			
			if($row[csf("edf_paid_date")] != "" && $row[csf("edf_paid_date")] != "0000-00-00")
			{
				$edf_paid_value=$row[csf("edf_loan_value")];
			}
			
			if($condition_date != "" && $condition_date != "0000-00-00")
			{
				$ifdbc_edf_data[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]["edf"]+=$row[csf("edf_loan_value")]-$edf_paid_value;
				$edf_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("edf_loan_value")]-$edf_paid_value;
			}
		}
	}	
	unset($ifdbc_edf_sql_result);
	//company_id
	$exfact_com_cond="";
	if($cbo_company_name>0) $exfact_com_cond=" and a.company_id=$cbo_company_name";
	$ex_fact_data=return_library_array("select b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
	//echo "<pre>";print_r($ex_fact_data);die;
	$com_cond="";
	if($cbo_company_name>0) $com_cond=" and b.company_name=$cbo_company_name";
	$order_sql="select a.po_break_down_id, sum(a.order_quantity) as order_quantity from wo_po_color_size_breakdown a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.shiping_status<>3 $com_cond group by a.po_break_down_id";
	$order_sql_result=sql_select($order_sql);
	$pending_ord_qnty=0;
	foreach($order_sql_result as $row)
	{
		$pending_ord_qnty+=$row[csf("order_quantity")]-$ex_fact_data[$row[csf("po_break_down_id")]];
	}
	
	$beneficiary_cond="";
	if($cbo_company_name>0) $beneficiary_cond=" and b.benificiary_id=$cbo_company_name";
	$proceed_rlz_sql="select b.invoice_bill_id, c.document_currency as document_currency from com_export_proceed_realization b, com_export_proceed_rlzn_dtls c where b.id=c.mst_id and b.is_invoice_bill=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $beneficiary_cond";
	//echo $proceed_rlz_sql;die;
	$realize_data_arr=array();
	$proceed_rlz_sql_result=sql_select($proceed_rlz_sql);
	$rlz_value=0;
	foreach($proceed_rlz_sql_result as $row)
	{
		$realize_data_arr[$row[csf("invoice_bill_id")]]+=$row[csf("document_currency")];
		$rlz_value+=$row[csf("document_currency")];
	}
	unset($proceed_rlz_sql_result);
	
	$bill_com_cond="";
	if($cbo_company_name>0) $bill_com_cond=" and b.company_id=$cbo_company_name";
	$bill_sql="select b.id as bill_id, b.bank_ref_no, b.company_id, b.possible_reali_date, b.lien_bank, sum(a.net_invo_value) as bill_value from com_export_doc_submission_invo a, com_export_doc_submission_mst b where a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.lien_bank > 0 and b.submit_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond group by b.id, b.bank_ref_no, b.company_id, b.possible_reali_date, b.lien_bank";
	$bill_sql_result=sql_select($bill_sql);
	$fdbp_data=array();
	foreach($bill_sql_result as $row)
	{
		$fdbp_data[$row[csf("company_id")]][$row[csf("lien_bank")]]+=$row[csf("bill_value")]-$realize_data_arr[$row[csf("bill_id")]];
		$fdbp_bank_data[$row[csf("lien_bank")]]+=$row[csf("bill_value")]-$realize_data_arr[$row[csf("bill_id")]];
	}
	unset($bill_sql_result);
	
	$bill_coll_sql="select b.id as bill_id, sum(a.net_invo_value) as bill_value from com_export_doc_submission_invo a, com_export_doc_submission_mst b where a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.submit_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond group by b.id";
	$bill_coll_sql_result=sql_select($bill_coll_sql);
	$bill_receive=0;
	foreach($bill_coll_sql_result as $row)
	{
		$bill_receive+=$row[csf("bill_value")]-$realize_data_arr[$row[csf("bill_id")]];
	}
	unset($bill_coll_sql_result);
	
	$packing_sql=sql_select("select sum(a.loan_amount) as loan_amount  from com_pre_export_finance_dtls a, com_pre_export_finance_mst b where a.mst_id=b.id and a.loan_type=20 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $beneficiary_cond");
	$odg_amt=0;
	foreach($packing_sql as $row)
	{
		$odg_amt+=$row[csf("loan_amount")];
	}
	unset($packing_sql);
	$bank_id_cond="";
	if($cbo_lein_bank>0) $bank_id_cond=" and a.id=$cbo_lein_bank";
	$bank_cd_sql=sql_select("select sum(b.loan_limit) as loan_amount from lib_bank a, lib_bank_account b where a.id=b.account_id and b.account_type=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bank_id_cond");
	$sod_cd_amt=0;
	foreach($bank_cd_sql as $row)
	{
		$sod_cd_amt+=$row[csf("loan_amount")];
	}
	unset($bank_cd_sql);
	
	//echo $pending_ord_qnty."==".$bill_receive."==". $odg_amt."==".$sod_cd_amt;die;
	//echo "<pre>";print_r($all_btb_bank);die;
	$count_col=0;$bank_col=0;
	foreach($all_btb_bank as $com_id=>$com_data)
	{
		foreach($com_data as $bank_val)
		{
			$count_col++;
			if($bank_check[$bank_val]=="")
			{
				$bank_check[$bank_val]=$bank_val;
				$bank_col++;
			}
		}
	}
	//echo "<pre>";print_r($bank_check);die;
	//echo $count_col."=".$com_col."=".$bank_col;die;
	$tot_col=$count_col+$bank_col;
	$table_width=(200+(100*$tot_col));
	$div_width=20+$table_width;
	ob_start();
?>
<div style="width:<? echo $div_width; ?>px;" id="scroll_body">
<fieldset style="width:100%">
    <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
    	<thead>
        	<tr>
            	<th width="50" rowspan="3">SL</th>
                <th width="150" rowspan="3">Facility</th>
                <th colspan="<? echo $count_col; ?>">Amount</th>
                <th colspan="<? echo $bank_col; ?>">Bank Total</th>
            </tr>
            <tr>
            	<?
				foreach($all_btb_bank as $com_id=>$com_data)
				{
					?>
                    <th colspan="<? echo count($com_data); ?>" title="<? echo $com_id; ?>"><? echo $company_arr[$com_id];?></th>
                    <?
				}
				foreach($bank_check as $bank_id)
				{
					?>
                    <th rowspan="2" width="100"><? echo $bank_arr[$bank_id];?></th>
                    <?
				}
				?>
            </tr>
            <tr>
            	<?
				foreach($all_btb_bank as $com_id=>$com_data)
				{
					foreach($com_data as $bank_id)
					{
						?>
                        <th width="100"><? echo $bank_arr[$bank_id];?></th>
                        <?	
					}
				}
				?>
            </tr>
        </thead>
        <tbody>
        	<tr bgcolor="#FFFFFF">
            	<td align="center">1</td>
                <td>BTB LC</td>
                <?
				$total_libiality=array();$total_bank_libiality=array();
				foreach($all_btb_bank as $com_id=>$com_data)
				{
					foreach($com_data as $bank_id)
					{
						?>
                        <td align="right"><? echo number_format($btb_data[$com_id][$bank_id],2);?></td>
                        <?
						$total_libiality[$com_id][$bank_id]+=$btb_data[$com_id][$bank_id];
					}
				}
				foreach($bank_check as $bank_id)
				{
					?>
                    <td align="right"><? echo number_format($btb_bank_total[$bank_id],2);?></td>
                    <?
					$total_bank_libiality[$bank_id]+=$btb_bank_total[$bank_id];
				}
				?>
            </tr>
            <tr bgcolor="#E9F3FF">
            	<td align="center">2</td>
                <td>IFDBC</td>
                <?
				foreach($all_btb_bank as $com_id=>$com_data)
				{
					foreach($com_data as $bank_id)
					{
						?>
                        <td align="right"><? echo number_format($ifdbc_edf_data[$com_id][$bank_id]["ifdbc"],2);?></td>
                        <?	
						$total_libiality[$com_id][$bank_id]+=$ifdbc_edf_data[$com_id][$bank_id]["ifdbc"];
					}
				}
				foreach($bank_check as $bank_id)
				{
					?>
                    <td align="right"><? echo number_format($ifdbc_bank_total[$bank_id],2);?></td>
                    <?
					$total_bank_libiality[$bank_id]+=$ifdbc_bank_total[$bank_id];
				}
				?>
            </tr>
            <tr bgcolor="#FFFFFF">
            	<td align="center">3</td>
                <td>PAD (EDF)</td>
                <?
				foreach($all_btb_bank as $com_id=>$com_data)
				{
					foreach($com_data as $bank_id)
					{
						?>
                        <td align="right"><? echo number_format($ifdbc_edf_data[$com_id][$bank_id]["edf"],2);?></td>
                        <?
						$total_libiality[$com_id][$bank_id]+=$ifdbc_edf_data[$com_id][$bank_id]["edf"];	
					}
				}
				foreach($bank_check as $bank_id)
				{
					?>
                    <td align="right"><? echo number_format($edf_bank_total[$bank_id],2);?></td>
                    <?
					$total_bank_libiality[$bank_id]+=$edf_bank_total[$bank_id];
				}
				?>
            </tr>
            <tr bgcolor="#E9F3FF">
            	<td align="center">4</td>
                <td>FDBP</td>
                <?
				foreach($all_btb_bank as $com_id=>$com_data)
				{
					foreach($com_data as $bank_id)
					{
						?>
                        <td align="right"><? echo number_format($fdbp_data[$com_id][$bank_id],2);?></td>
                        <?	
						$total_libiality[$com_id][$bank_id]+=$fdbp_data[$com_id][$bank_id];	
					}
				}
				foreach($bank_check as $bank_id)
				{
					?>
                    <td align="right"><? echo number_format($fdbp_bank_data[$bank_id],2);?></td>
                    <?
					$total_bank_libiality[$bank_id]+=$fdbp_bank_data[$bank_id];
				}
				?>
            </tr>
            <tr bgcolor="#CCCCCC">
            	<td align="center">5</td>
                <td>Total Liability (USD)</td>
                <?
				foreach($all_btb_bank as $com_id=>$com_data)
				{
					foreach($com_data as $bank_id)
					{
						?>
                        <td align="right"><? echo number_format($total_libiality[$com_id][$bank_id],2);?></td>
                        <?	
					}
				}
				foreach($bank_check as $bank_id)
				{
					?>
                    <td align="right"><? echo number_format($total_bank_libiality[$bank_id],2);?></td>
                    <?
				}
				?>
            </tr>
            <tr bgcolor="#FFFFCC">
            	<td align="center">6</td>
                <td>Total Liability (BDT)</td>
                <?
				foreach($all_btb_bank as $com_id=>$com_data)
				{
					foreach($com_data as $bank_id)
					{
						?>
                        <td align="right"><? echo number_format(($total_libiality[$com_id][$bank_id]*$txt_exchange_rate),2);?></td>
                        <?	
					}
				}
				foreach($bank_check as $bank_id)
				{
					?>
                    <td align="right"><? echo number_format(($total_bank_libiality[$bank_id]*$txt_exchange_rate),2);?></td>
                    <?
				}
				?>
            </tr>
        </tbody>
    </table>
    <table width="400" cellpadding="0" cellspacing="0" align="left" style="margin-top:30px;">
    	<tr><td colspan="2" style="font-size:16px; font-weight:bold;">Others :</td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><td width="250">Export Under Execution (USD)</td><td><? echo number_format($pending_ord_qnty,2) ?></td></tr>
        <tr><td>Bills Receiveable (USD)</td><td><? echo number_format($bill_receive,2) ?></td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><td colspan="2" style="font-size:16px; font-weight:bold;">Accounts Balance :</td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><td>ODG (PC) (BDT)</td><td><? echo number_format($odg_amt,2) ?></td></tr>
        <tr><td>SOD (CD Limit) (BDT)</td><td><? echo number_format($sod_cd_amt,2) ?></td></tr>
    </table>
</fieldset>
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


if($action=="btb_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($btb_id=="") die;
	$day_of_month=cal_days_in_month(CAL_GREGORIAN, $month_val, $year_val);
	$maturity_start_date=$year_val."-".$month_val."-01";
	$maturity_end_date=$year_val."-".$month_val."-".$day_of_month;
	if($db_type==2)
	{
		$maturity_start_date=change_date_format($maturity_start_date,"","",1);
		$maturity_end_date=change_date_format($maturity_end_date,"","",1);
	}
	//echo $maturity_start_date."**".$maturity_end_date;die;
	//print_r($po_id);die;
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
<div style="width:660px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
<div style="width:660px" id="report_container">
<fieldset style="width:660px">
    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="660">
        <thead>
        	<tr>
                <th width="40">SL NO</th>
                <th width="120">BTB LC NO</th>
                <th width="70">BTB LC Date</th>
                <th width="100">BTB LC Value</th>
                <th width="120">Invoice No</th>
                <th width="100">Invoice Value</th>
                <th>Maturity Date</th>
            </tr>
        </thead>
        <tbody>
		<?
		//for show file year
		/*
		$lc_year_sql=sql_select("select id as lc_sc_id, lc_year as lc_sc_year, 0 as type from com_export_lc union all select id as lc_sc_id, sc_year as lc_sc_year, 1 as type from com_sales_contract");
		$lc_sc_year=array();
		foreach($lc_year_sql as $row)
		{
			$lc_sc_year[$row[csf("lc_sc_id")]][$row[csf("type")]]=$row[csf("lc_sc_year")];
		}*/
		
		if($btb_id!="")
		{
			//previous query with file year and export lc/sc
			/*$btb_sql="select b.lc_sc_id, b.is_lc_sc, c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.maturity_date, sum(e.current_acceptance_value) as inv_value
			from 
					com_btb_export_lc_attachment b,  com_btb_lc_master_details c, com_import_invoice_mst d, com_import_invoice_dtls e
			where 
					b.import_mst_id=c.id and c.id=e.btb_lc_id and e.import_invoice_id=d.id and c.id in($btb_id) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and d.maturity_date between '$maturity_start_date' and '$maturity_end_date' 
			group by b.lc_sc_id, b.is_lc_sc, c.id, c.lc_number, c.lc_date, c.lc_value, d.id, d.invoice_no, d.invoice_date, d.maturity_date";*/
			
			if($type==3)
			{
				$btb_sql="select c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.maturity_date, sum(e.current_acceptance_value) as inv_value
				from 
						com_btb_lc_master_details c, com_import_invoice_mst d, com_import_invoice_dtls e
				where 
						c.id=e.btb_lc_id and e.import_invoice_id=d.id and c.id in($btb_id) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and c.lc_date between '$maturity_start_date' and '$maturity_end_date' 
				group by c.id, c.lc_number, c.lc_date, c.lc_value, d.id, d.invoice_no, d.invoice_date, d.maturity_date";
				
				
			}
			else
			{
				$btb_sql="select c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.maturity_date, sum(e.current_acceptance_value) as inv_value
				from 
						com_btb_lc_master_details c, com_import_invoice_mst d, com_import_invoice_dtls e
				where 
						c.id=e.btb_lc_id and e.import_invoice_id=d.id and c.id in($btb_id) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and d.maturity_date between '$maturity_start_date' and '$maturity_end_date' 
				group by c.id, c.lc_number, c.lc_date, c.lc_value, d.id, d.invoice_no, d.invoice_date, d.maturity_date";
			}
			
		}
		
		//echo $btb_sql;
		
		
		$payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amt");
		
		
		$i=1;
        $sql_re=sql_select($btb_sql);
        $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;$result=0;
        
        foreach($sql_re as $row)
        {
			if($type==2)
			{
				$invoice_value=$row[csf("inv_value")]-$payment_data_array[$row[csf("invoice_id")]];
				if($invoice_value>0)
				{
					$lc_value=$payment_data_array[$row[csf('btb_lc_date')]];
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center"><? echo $i; ?></td>
						<td><p><?  echo $row[csf('btb_lc_number')]; ?>&nbsp;</p></td>
						<td align="center"><? if($row[csf('btb_lc_date')]!="" && $row[csf('btb_lc_date')]!="0000-00-00") echo change_date_format($row[csf('btb_lc_date')]); ?></td>
						<!--<td align="center"><?//  echo $lc_sc_year[$row[csf("lc_sc_id")]][$row[csf("is_lc_sc")]];?></td>-->
						<td align="right"><?  echo number_format($row[csf('lc_value')],2); $total_lc_value+=$row[csf('lc_value')]; ?></td>
						<td><p><?  echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
						<td align="right"><?  echo number_format($invoice_value,2); $total_invoice_value+=$invoice_value;  ?></td>
						<td align="center"><? if($row[csf('maturity_date')]!="" && $row[csf('maturity_date')]!="0000-00-00") echo change_date_format($row[csf('maturity_date')]); ?></td>
					</tr>
					<?
					$i++;
				}
			}
			else
			{
				$lc_value=$invoice_value=0;
				$invoice_value=$row[csf("inv_value")];
				$lc_value=$row[csf('lc_value')]-$payment_data_array[$row[csf('invoice_id')]];
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td align="center"><? echo $i; ?></td>
					<td><p><?  echo $row[csf('btb_lc_number')]; ?>&nbsp;</p></td>
					<td align="center"><? if($row[csf('btb_lc_date')]!="" && $row[csf('btb_lc_date')]!="0000-00-00") echo change_date_format($row[csf('btb_lc_date')]); ?></td>
					<!--<td align="center"><?//  echo $lc_sc_year[$row[csf("lc_sc_id")]][$row[csf("is_lc_sc")]];?></td>-->
					<td align="right"><?  echo number_format($lc_value,2); $total_lc_value+=$lc_value; ?></td>
					<td><p><?  echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
					<td align="right"><?  echo number_format($invoice_value,2); $total_invoice_value+=$invoice_value;  ?></td>
					<td align="center"><? if($row[csf('maturity_date')]!="" && $row[csf('maturity_date')]!="0000-00-00") echo change_date_format($row[csf('maturity_date')]); ?></td>
				</tr>
				<?
				$i++;
			}
			
        }
        ?>
        </tbody>
        <tfoot>
            <tr >
                <th align="right">&nbsp;</th>
                <th align="right" >&nbsp;</th>
                <!--<th align="right">&nbsp;</th>-->
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
                <th align="right">&nbsp;</th>
                <th align="right"><? echo number_format($total_invoice_value,2); ?></th>
                <th align="right">&nbsp;</th>
            </tr>
        </tfoot>
    </table>
</fieldset>
</div>
<?
exit();
}


if($action=="btb_open_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $type;die;
	if($btb_id=="") die;
	$day_of_month=cal_days_in_month(CAL_GREGORIAN, $month_val, $year_val);
	$maturity_start_date=$year_val."-".$month_val."-01";
	$maturity_end_date=$year_val."-".$month_val."-".$day_of_month;
	if($db_type==2)
	{
		$maturity_start_date=change_date_format($maturity_start_date,"","",1);
		$maturity_end_date=change_date_format($maturity_end_date,"","",1);
	}
	//echo $maturity_start_date."**".$maturity_end_date;die;
	//print_r($po_id);die;
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
<div style="width:450px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
<div style="width:450px" id="report_container">
<fieldset style="width:450px">
    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="450">
        <thead>
        	<tr>
                <th width="50">SL NO</th>
                <th width="150">BTB LC NO</th>
                <th width="100">BTB LC Date</th>
                <th>BTB LC Value</th>
            </tr>
        </thead>
        <tbody>
		<?
		
		if($btb_id!="")
		{
			$btb_sql="select c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value
			from 
					com_btb_lc_master_details c 
			where 
					 c.id in($btb_id) and c.is_deleted=0 and c.status_active=1";
			
			
			if($type==5)
			{
				$payment_data_array=return_library_array("select lc_id, sum(accepted_ammount) as paid_amt from  com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","lc_id","paid_amt");
			}
			else
			{
				if($db_type==0)
				{
					$payment_data_array=return_library_array("select c.btb_lc_id, sum(c.current_acceptance_value) as edf_loan_value 
				from com_import_invoice_mst b, com_import_invoice_dtls c 
				where c.btb_lc_id in($btb_id) and c.import_invoice_id=b.id and b.edf_paid_date!='0000-00-00' and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by c.btb_lc_id","btb_lc_id","edf_loan_value");
				}
				else
				{
					$payment_data_array=return_library_array("select c.btb_lc_id, sum(c.current_acceptance_value) as edf_loan_value 
				from com_import_invoice_mst b, com_import_invoice_dtls c 
				where c.btb_lc_id in($btb_id) and c.import_invoice_id=b.id and b.edf_paid_date is not null and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by c.btb_lc_id","btb_lc_id","edf_loan_value");
				}
			}
			
			
			
			
			
		}
		
		//echo $btb_sql;
		
		
		//$payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amt");
		
		
		
		
		$i=1;
        $sql_re=sql_select($btb_sql);
        $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;$result=0;
        
        foreach($sql_re as $row)
        {
			$lc_value=$row[csf('lc_value')]-$payment_data_array[$row[csf("btb_lc_sc_id")]];
			if(number_format($lc_value,2)>0)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td align="center"><? echo $i; ?></td>
					<td><p><?  echo $row[csf('btb_lc_number')]; ?>&nbsp;</p></td>
					<td align="center"><? if($row[csf('btb_lc_date')]!="" && $row[csf('btb_lc_date')]!="0000-00-00") echo change_date_format($row[csf('btb_lc_date')]); ?></td>
					<td align="right"><?  echo number_format($lc_value,2); $total_lc_value+=$lc_value; ?></td>
				</tr>
				<?
				$lc_value=0;
				$i++;
			}
			
        }
        ?>
        </tbody>
        <tfoot>
            <tr >
                <th align="right">&nbsp;</th>
                <th align="right" >&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
            </tr>
        </tfoot>
    </table>
</fieldset>
</div>
<?
exit();
}


if($action=="btb_paid_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($inv_id=="") die;
	$day_of_month=cal_days_in_month(CAL_GREGORIAN, $month_val, $year_val);
	$btb_start_date=$year_val."-".$month_val."-01";
	$btb_end_date=$year_val."-".$month_val."-".$day_of_month;
	if($db_type==2)
	{
		$btb_start_date=change_date_format($btb_start_date,"","",1);
		$btb_end_date=change_date_format($btb_end_date,"","",1);
	}
	//echo $maturity_start_date."**".$maturity_end_date;die;
	//print_r($po_id);die;
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
<div style="width:760px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
<div style="width:760px" id="report_container">
<fieldset style="width:760px">
    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="760">
        <thead>
        	<tr>
                <th width="40">SL NO</th>
                <th width="120">BTB LC NO</th>
                <th width="70">BTB LC Date</th>
                <th width="100">BTB LC Value</th>
                <th width="120">Invoice No</th>
                <th width="100">Invoice Value</th>
                <th width="100">Paid Value</th>
                <th>Paid Date</th>
            </tr>
        </thead>
        <tbody>
		<?
		
		if($inv_id!="")
		{
			
			
			$btb_sql="select c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.maturity_date, sum(e.current_acceptance_value) as inv_value, f.accepted_ammount, f.payment_date
			from 
					com_btb_lc_master_details c, com_import_invoice_mst d, com_import_invoice_dtls e,  f
			where 
					c.id=e.btb_lc_id and e.import_invoice_id=d.id and d.id=f.invoice_id  and d.id in($inv_id) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and c.lc_date between '$btb_start_date' and '$btb_end_date' 
			group by c.id, c.lc_number, c.lc_date, c.lc_value, d.id, d.invoice_no, d.invoice_date, d.maturity_date, f.accepted_ammount, f.payment_date";
		}
		
		//echo $btb_sql;
		
		$i=1;
        $sql_re=sql_select($btb_sql);
        $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;$result=0;
        
        foreach($sql_re as $row)
        {
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><p><?  echo $row[csf('btb_lc_number')]; ?>&nbsp;</p></td>
                <td align="center"><? if($row[csf('btb_lc_date')]!="" && $row[csf('btb_lc_date')]!="0000-00-00") echo change_date_format($row[csf('btb_lc_date')]); ?></td>
				<td align="right"><?  echo number_format($row[csf('lc_value')],2); $total_lc_value+=$row[csf('lc_value')]; ?></td>
				<td><p><?  echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
				<td align="right"><?  echo number_format($row[csf("inv_value")],2); $total_invoice_value+=$row[csf("inv_value")];  ?></td>
				<td align="right"><?  echo number_format($row[csf("accepted_ammount")],2); $total_paid_value+=$row[csf("accepted_ammount")];  ?></td>
				<td align="center"><? if($row[csf('payment_date')]!="" && $row[csf('payment_date')]!="0000-00-00") echo change_date_format($row[csf('payment_date')]); ?></td>
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
                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
                <th align="right">&nbsp;</th>
                <th align="right"><? echo number_format($total_invoice_value,2); ?></th>
                <th align="right"><? echo number_format($total_paid_value,2); ?></th>
                <th align="right">&nbsp;</th>
            </tr>
        </tfoot>
    </table>
</fieldset>
</div>
<?
exit();
}

if($action=="btb_open_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($btb_id=="") die;
	$day_of_month=cal_days_in_month(CAL_GREGORIAN, $month_val, $year_val);
	$btb_start_date=$year_val."-".$month_val."-01";
	$btb_end_date=$year_val."-".$month_val."-".$day_of_month;
	if($db_type==2)
	{
		$btb_start_date=change_date_format($btb_start_date,"","",1);
		$btb_end_date=change_date_format($btb_end_date,"","",1);
	}
	//echo $maturity_start_date."**".$maturity_end_date;die;
	//print_r($po_id);die;
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
<div style="width:500px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
<div style="width:500px" id="report_container">
<fieldset style="width:500px">
    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="480">
        <thead>
        	<tr>
                <th width="50">SL NO</th>
                <th width="150">BTB LC NO</th>
                <th width="100">BTB LC Date</th>
                <th>BTB LC Value</th>
            </tr>
        </thead>
        <tbody>
		<?
		
		if($btb_id!="")
		{
			
			$btb_sql="select c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value
			from 
					com_btb_lc_master_details c
			where 
					c.id in($btb_id) and c.is_deleted=0 and c.status_active=1  and c.lc_date between '$btb_start_date' and '$btb_end_date'";
		}
		
		//echo $btb_sql;
		
		$i=1;
        $sql_re=sql_select($btb_sql);
        $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;$result=0;
        
        foreach($sql_re as $row)
        {
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><p><?  echo $row[csf('btb_lc_number')]; ?>&nbsp;</p></td>
				<td align="center"><? if($row[csf('btb_lc_date')]!="" && $row[csf('btb_lc_date')]!="0000-00-00") echo change_date_format($row[csf('btb_lc_date')]); ?></td>
				<td align="right"><?  echo number_format($row[csf('lc_value')],2); $total_lc_value+=$row[csf('lc_value')]; ?></td>
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
                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
            </tr>
        </tfoot>
    </table>
</fieldset>
</div>
<?
exit();
}

disconnect($con);
?>
