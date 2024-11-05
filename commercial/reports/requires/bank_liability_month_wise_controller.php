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
	$cbo_from_month=str_replace("'","",$cbo_from_month);
	$cbo_from_year=str_replace("'","",$cbo_from_year); 
	$cbo_to_month=str_replace("'","",$cbo_to_month);
	$cbo_to_year=str_replace("'","",$cbo_to_year);
	
	$from_date="01-".str_pad($cbo_from_month, 2, "0", STR_PAD_LEFT)."-" .$cbo_from_year;
	$to_date=cal_days_in_month(CAL_GREGORIAN,$cbo_to_month,$cbo_to_year)."-".str_pad($cbo_to_month, 2, "0", STR_PAD_LEFT)."-" .$cbo_to_year;
	if($db_type==0)
	{
		$from_date=change_date_format($from_date,"yyyy-mm-dd");
		$to_date=change_date_format($to_date,"yyyy-mm-dd");
	}
	else
	{
		$from_date=change_date_format($from_date,"","",1);
		$to_date=change_date_format($to_date,"","",1);
	}
	
	$from_time=strtotime($from_date);
	$to_time=strtotime($to_date);
	
	//$time_dev=strtotime($to_date)-strtotime($from_date);
	$p=1;
	for($i=$from_time;$i<=$to_time;$i=$i+86400)
	{
		$month_arr[date("m-Y",$i)]=date("M-y",$i);
		$p++;
	}
	//echo $p."<pre>";print_r($month_arr);die;
	
	//echo $hide_year;die;
	if($cbo_company_name>0) $com_conds=" and id=$cbo_company_name";
	$company_arr=return_library_array( "select company_short_name,id,company_name from lib_company where status_active=1 and is_deleted=0 $com_conds ",'id','company_short_name');
	//$bank_arr=return_library_array( "select bank_name,id from lib_bank",'id','bank_name');
	//echo "<pre>";print_r($company_arr);die;
	if($cbo_company_name>0) $com_cond_rlz=" and a.benificiary_id=$cbo_company_name";
	$sql_rlz=return_library_array("select a.invoice_bill_id, sum(b.document_currency) as rlz_value  from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b where a.id=b.mst_id and a.is_invoice_bill=1 and a.status_active=1 and b.status_active=1 $com_cond_rlz group by a.invoice_bill_id",'invoice_bill_id','rlz_value');
	if($cbo_company_name>0) $bill_com_cond=" and b.company_id=$cbo_company_name";
	if($cbo_lein_bank>0) $bill_bank_cond=" and b.lien_bank=$cbo_lein_bank";
	
	$bill_coll_sql="select a.invoice_id, b.entry_form, a.submission_dtls_id, a.net_invo_value as bill_value 
	from com_export_doc_submission_invo a, com_export_doc_submission_mst b 
	where a.doc_submission_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond";
	$bill_coll_sql_result=sql_select($bill_coll_sql);
	$bill_receive=0;
	foreach($bill_coll_sql_result as $row)
	{
		if($row[csf("entry_form")]==39)
		{
			$inv_sub_arr[$row[csf("invoice_id")]]+=$row[csf("bill_value")];
		}
		else
		{
			if($row[csf("submission_dtls_id")]==0) $inv_sub_arr[$row[csf("invoice_id")]]+=$row[csf("bill_value")];
		}
	}
	//echo "<pre>";print_r($inv_sub_arr);die;
	unset($bill_coll_sql_result);
	
	if($db_type==0)
	{
		$bill_data_cond=" and b.possible_reali_date!='0000-00-00'";
	}
	else
	{
		$bill_data_cond=" and b.possible_reali_date is not null";
	}
	if($from_date!="" && $to_date!="") $bill_data_cond.=" and b.possible_reali_date between '$from_date' and '$to_date'";
	/*$bill_coll_sql="select a.invoice_id, b.id as bill_id, a.submission_dtls_id, b.company_id, b.entry_form, b.possible_reali_date, sum(a.net_invo_value) as bill_value 
	from com_export_doc_submission_invo a, com_export_doc_submission_mst b 
	where a.doc_submission_mst_id=b.id and a.status_active=1 and b.entry_form=40 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond $bill_data_cond
	group by a.invoice_id, b.id, a.submission_dtls_id, b.company_id, b.entry_form, b.possible_reali_date
	order by b.possible_reali_date";*/
	
	$bill_coll_sql="select b.id as bill_id, b.bank_ref_no, b.company_id, b.entry_form, b.possible_reali_date, a.invoice_id, sum(a.net_invo_value) as bill_value 
	from com_export_doc_submission_invo a, com_export_doc_submission_mst b 
	where a.doc_submission_mst_id=b.id and a.status_active=1 and b.entry_form=40 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond $bill_data_cond
	group by b.id, b.bank_ref_no, b.company_id, b.entry_form, b.possible_reali_date, a.invoice_id
	order by b.possible_reali_date";
	//echo $bill_coll_sql;//die;//
	$bill_coll_sql_result=sql_select($bill_coll_sql);
	$bill_data=array();$summery_data=array();$bank_submit_data=array();
	foreach($bill_coll_sql_result as $row)
	{
		$bank_submit[$row[csf("invoice_id")]]+=$row[csf("bill_value")];
		$bank_submit_data[$row[csf("bill_id")]]["bill_id"]=$row[csf("bill_id")];
		$bank_submit_data[$row[csf("bill_id")]]["bank_ref_no"]=$row[csf("bank_ref_no")];
		$bank_submit_data[$row[csf("bill_id")]]["company_id"]=$row[csf("company_id")];
		$bank_submit_data[$row[csf("bill_id")]]["entry_form"]=$row[csf("entry_form")];
		$bank_submit_data[$row[csf("bill_id")]]["possible_reali_date"]=$row[csf("possible_reali_date")];
		$bank_submit_data[$row[csf("bill_id")]]["bill_value"]+=$row[csf("bill_value")];
	}
	//echo "<pre>";print_r($bank_submit_data);die;
	foreach($bank_submit_data as $val)
	{
		$bill_data[$val["company_id"]][date("M-y",strtotime($val["possible_reali_date"]))]["bank_bill"]+=$val["bill_value"]-$sql_rlz[$val["bill_id"]];
		$bill_data[$val["company_id"]][date("M-y",strtotime($val["possible_reali_date"]))]["bill_id"].=$val["bill_id"].",";
		$summery_receable[date("M-y",strtotime($val["possible_reali_date"]))]+=$val["bill_value"]-$sql_rlz[$val["bill_id"]];
	}
	
	//$bill_data[$row[csf("company_id")]][date("M-y",strtotime($row[csf("possible_reali_date")]))]["bank_bill"]+=$row[csf("bill_value")]-$sql_rlz[$row[csf("bill_id")]];
	
	//echo "<pre>";print_r($bill_data);die;
	unset($bill_coll_sql_result);
	
	$bill_buyer_sql="select a.invoice_id, b.id as bill_id, a.submission_dtls_id, b.company_id, b.entry_form, b.possible_reali_date, sum(a.net_invo_value) as bill_value 
	from com_export_doc_submission_invo a, com_export_doc_submission_mst b 
	where a.doc_submission_mst_id=b.id and a.status_active=1 and b.entry_form=39 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond $bill_data_cond
	group by a.invoice_id, b.id, a.submission_dtls_id, b.company_id, b.entry_form, b.possible_reali_date
	order by b.possible_reali_date";
	//echo $bill_buyer_sql;
	$bill_buyer_sql_result=sql_select($bill_buyer_sql);
	foreach($bill_buyer_sql_result as $row)
	{
		$pending_buyer_sub_value=$row[csf("bill_value")]-$bank_submit[$row[csf("invoice_id")]];
		if($pending_buyer_sub_value>0)
		{
			$bill_data[$row[csf("company_id")]][date("M-y",strtotime($row[csf("possible_reali_date")]))]["buyer_bill"]+=$row[csf("bill_value")]-$bank_submit[$row[csf("invoice_id")]];
			if($bill_check[$row[csf("bill_id")]]=="")
			{
				$bill_check[$row[csf("bill_id")]]=$row[csf("bill_id")];
				$bill_data[$row[csf("company_id")]][date("M-y",strtotime($row[csf("possible_reali_date")]))]["bill_id"].=$row[csf("bill_id")].",";
			}
			$summery_receable[date("M-y",strtotime($row[csf("possible_reali_date")]))]+=$row[csf("bill_value")]-$bank_submit[$row[csf("bill_id")]];
		}
	}
	unset($bill_buyer_sql_result);
	
	//echo "<pre>";print_r($bill_data);die;
	if($cbo_company_name>0) $com_ship_cond=" and b.benificiary_id=$cbo_company_name";
	if($cbo_lein_bank>0) $bank_ship_cond=" and a.lien_bank=$cbo_lein_bank";
	if($db_type==0)
	{
		$ex_data_cond=" and b.ex_factory_date!='0000-00-00'";
	}
	else
	{
		$ex_data_cond=" and b.ex_factory_date is not null";
	}
	
	$inv_sql=" select b.benificiary_id, a.lien_bank, b.id as inv_id, b.net_invo_value, b.ex_factory_date 
	from com_export_lc a, com_export_invoice_ship_mst b 
	where a.id=b.lc_sc_id and b.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.lien_bank>0 and b.location_id>0 $com_ship_cond $bank_ship_cond $ex_data_cond
	union all
	select b.benificiary_id, a.lien_bank, b.id as inv_id, b.net_invo_value, b.ex_factory_date 
	from com_sales_contract a, com_export_invoice_ship_mst b 
	where a.id=b.lc_sc_id and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.lien_bank>0 and b.location_id>0 $com_ship_cond $bank_ship_cond $ex_data_cond";
	//echo $inv_sql;die;
	$inv_sql_result=sql_select($inv_sql);
	foreach($inv_sql_result as $row)
	{
		$ex_fact_month=date("M-y",strtotime($row[csf("ex_factory_date")])+(86400*45));
		$pending_value=$row[csf("net_invo_value")]-$inv_sub_arr[$row[csf("inv_id")]];
		if($pending_value>0)
		{
			$doc_ind_hand_arr[$row[csf("benificiary_id")]][$ex_fact_month]["inv_value"]+=$pending_value;
			$doc_ind_hand_arr[$row[csf("benificiary_id")]][$ex_fact_month]["inv_id"].=$row[csf("inv_id")].",";
			
			$summery_receable[$ex_fact_month]+=$pending_value;
		}
	}
	
	//echo "<pre>";print_r($bill_data);
	//echo "<pre>";print_r($doc_ind_hand_arr);
	//echo "<pre>";print_r($summery_data);
	//die;
	
	$exfact_com_cond="";
	if($cbo_company_name>0) $exfact_com_cond=" and a.company_id=$cbo_company_name";
	$ex_fact_data=return_library_array("select b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
	//echo "<pre>";print_r($ex_fact_data[33372]);echo "<br>";//die;
	$com_cond="";
	if($cbo_company_name>0) $com_ord_cond=" and b.company_name=$cbo_company_name";
	if($cbo_lein_bank>0) $bank_ord_cond=" and a.TAG_BANK=$cbo_lein_bank";
	//echo $from_date."=".$to_date;die;
	if($from_date!="" && $to_date!="") $ord_date_cond=" and c.pub_shipment_date between '$from_date' and '$to_date'";
	$order_sql="select c.id as po_id, a.bank_id, b.company_name, c.pub_shipment_date, (c.po_quantity*b.total_set_qnty) as order_quantity, c.po_total_price as order_total 
	from lib_buyer_tag_bank a, wo_po_details_master b, wo_po_break_down c 
	where a.buyer_id=b.buyer_name and b.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 $com_ord_cond $bank_ord_cond $ord_date_cond
	order by c.pub_shipment_date";
	//echo $order_sql;die;
	$order_sql_result=sql_select($order_sql);
	$pending_ord_qnty=0;$tot_pending_ord_qnty=0;$tot_pending_ord_value=0;
	
	foreach($order_sql_result as $row)
	{
		$ord_rate=0;
		if($row[csf("order_total")]>0 && $row[csf("order_quantity")] >0)
		{
			$ord_rate=($row[csf("order_total")]/$row[csf("order_quantity")])*1;
		}
		$pendin_qnty=($row[csf("order_quantity")]-$ex_fact_data[$row[csf("po_id")]])*1;
		$pending_ord_value=($pendin_qnty*$ord_rate);
		
		$pending_ord_data_arr[$row[csf("company_name")]][date("M-y",strtotime($row[csf("pub_shipment_date")]))]["pendin_qnty"]+=$pendin_qnty;
		$pending_ord_data_arr[$row[csf("company_name")]][date("M-y",strtotime($row[csf("pub_shipment_date")]))]["pending_ord_value"]+=$pending_ord_value;
		
		$summery_tobe_export[date("M-y",strtotime($row[csf("pub_shipment_date")]))]+=$pending_ord_value;
	}
	//echo "<pre>";print_r($pending_ord_data_arr);die;
	unset($order_sql_result);
	
	
	$sql_cond_payment="";
	if($cbo_company_name>0) $sql_cond_payment=" and a.company_id=$cbo_company_name";
	$sql_payment="select a.lc_id, b.invoice_id, b.accepted_ammount 
	from com_import_payment b left join com_import_payment_mst a on b.mst_id=a.id and a.status_active=1 $sql_cond_payment  
	where b.status_active=1 and b.is_deleted=0";
	//echo $sql_payment;
	$sql_payment_result=sql_select($sql_payment);
	$lc_wise_payment=array();$invoice_wise_payment=array();
	foreach($sql_payment_result as $row)
	{
		//$lc_wise_payment[$row[csf("lc_id")]]+=$row[csf("accepted_ammount")];
		$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")];
	}
	//echo $invoice_wise_payment[984];die;
	unset($sql_payment_result);
	
	$sql_cond="";
	if($cbo_company_name>0) $sql_cond=" and a.importer_id=$cbo_company_name";
	if($cbo_lein_bank>0) $sql_cond.=" and a.issuing_bank_id=$cbo_lein_bank";
	if($db_type==0)
	{
		$sql_cond.=" and b.maturity_date!='0000-00-00'";
	}
	else
	{
		$sql_cond.=" and b.maturity_date is not null";
	}
	if($from_date!="" && $to_date!="") $sql_cond.=" and b.maturity_date between '$from_date' and '$to_date'";
	$import_sql="select a.id as btb_lc_id, a.importer_id, a.issuing_bank_id, a.lc_value, a.payterm_id, b.id as import_inv_id, b.maturity_date, b.retire_source, b.edf_paid_date, sum(c.current_acceptance_value) as edf_loan_value
	from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
	where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 $sql_cond 
	group by a.id, a.importer_id, a.issuing_bank_id, a.lc_value, a.payterm_id, b.id, b.maturity_date, b.retire_source, b.edf_paid_date
	order by b.maturity_date";
	//echo $import_sql;die;
	$import_sql_result=sql_select($import_sql);
	$import_data=array();
	foreach($import_sql_result as $row)
	{
		if($row[csf("retire_source")]==30 || $row[csf("retire_source")]==31)
		{
			if($row[csf("edf_paid_date")]=="" || $row[csf("edf_paid_date")]=="0000-00-00")
			{
				$import_data[$row[csf("importer_id")]][date("M-y",strtotime($row[csf("maturity_date")]))]["edf_value"]+=$row[csf("edf_loan_value")];
				$import_data[$row[csf("importer_id")]][date("M-y",strtotime($row[csf("maturity_date")]))]["edf_inv_id"].=$row[csf("import_inv_id")].",";
				$summery_data[date("M-y",strtotime($row[csf("maturity_date")]))][$row[csf("importer_id")]]["edf"]+=$row[csf("edf_loan_value")];
				$sum_edf_arr[date("M-y",strtotime($row[csf("maturity_date")]))]+=$row[csf("edf_loan_value")];
			}
		}
		else if($row[csf("retire_source")]==142)
		{
			if($row[csf("edf_paid_date")]=="" || $row[csf("edf_paid_date")]=="0000-00-00")
			{
				$import_data[$row[csf("importer_id")]][date("M-y",strtotime($row[csf("maturity_date")]))]["upass_value"]+=$row[csf("edf_loan_value")];
				$import_data[$row[csf("importer_id")]][date("M-y",strtotime($row[csf("maturity_date")]))]["upass_inv_id"].=$row[csf("import_inv_id")].",";
				$upass_summery[date("M-y",strtotime($row[csf("maturity_date")]))]+=$row[csf("edf_loan_value")];
			}
		}
		else
		{
			if($row[csf("payterm_id")]==2)
			{
				$import_data[$row[csf("importer_id")]][date("M-y",strtotime($row[csf("maturity_date")]))]["usence_value"]+=$row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]];
				$import_data[$row[csf("importer_id")]][date("M-y",strtotime($row[csf("maturity_date")]))]["usence_inv_id"].=$row[csf("import_inv_id")].",";
				$sum_usence_arr[date("M-y",strtotime($row[csf("maturity_date")]))]+=$row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]];
			}
		}
	}
	unset($import_sql_result);
	
	$table_width_summery=(490+(80*count($company_arr)));
	$table_width_dtls=(350+(80*count($month_arr)));
	//echo $table_width_summery."=".$table_width_dtls;die;
	ob_start();
	?>
    <style>
	.td
	{ 
	  font-size:14px !important;
	}
	</style>
    <div>
    <div style="width:<?=$table_width_summery;?>px;">
    <table width="<? echo $table_width_summery; ?>" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
        <thead>
        	<tr>
            	<th colspan="<? echo count($company_arr)+3; ?>" style="color:red; font-weight:bold; font-size:16px;" align="center">GROUP SUMMARY- FIGURE IN MILLION US DOLLARS</th>
                <th colspan="3" rowspan="3" style="font-size:16px;">Export Import Forecast</th>
            </tr>
            <tr>
            	<th colspan="<? echo count($company_arr)+3; ?>" style="font-weight:bold; font-size:14px;" align="center">Total Import Payment With UPASS</th>
            </tr>
            <tr>
                <th width="80" rowspan="2">MONTH</th>
                <th colspan="<? echo count($company_arr); ?>">Company</th>
                <th width="80" rowspan="2">UPASS</th>
                <th width="80" rowspan="2">G. Total</th>
            </tr>
            <tr>
                <?
                $count_col=0;
                foreach($company_arr as $com_id=>$com_data)
                {
                    ?>
                    <th width="80" title="<? echo $com_id; ?>"><? echo $com_data;?></th>
                    <?
                }
                ?>
                <th width="80">Import</th>
                <th width="80">To Be Export </th>
                <th>Receivable</th>
            </tr>
        </thead>
        <tbody>
                <?
                $i=1;
                $milion_fig=1000000;
                foreach($month_arr as $month_id=>$month_data)
                {
                    if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer;">
                        <td align="center" style=""><? echo $month_data; ?></td>
                        <?
                        $com_tot=0;
                        foreach($company_arr as $com_id=>$com_data)
                        {
                            ?>
                            <td align="right" style="font-size:14px;" title="<? echo number_format($summery_data[$month_data][$com_id]["edf"],2);?>"><? echo number_format(($summery_data[$month_data][$com_id]["edf"]/$milion_fig),2); ?></td>
                            <?
                            $com_tot+=$summery_data[$month_data][$com_id]["edf"];
                            $g_tot_com_summery[$com_id]+=$summery_data[$month_data][$com_id]["edf"];
                        }
                        ?>
                        <td align="right" title="<?= number_format($upass_summery[$month_data],2);?>"><? echo number_format(($upass_summery[$month_data]/$milion_fig),2); $g_tot_upass_summery+=$upass_summery[$month_data];  $com_tot +=($upass_summery[$month_data]/$milion_fig); ?></td>
                        <td align="right" title="<?= number_format($com_tot,2);?>"><? echo number_format(($com_tot/$milion_fig),2); ?></td>
                        <td align="right" title="<?= number_format($com_tot,2);?>"><? echo number_format(($com_tot/$milion_fig),2); ?></td>
                        <td align="right" title="<?= number_format($summery_tobe_export[$month_data],2);?>"><? echo number_format(($summery_tobe_export[$month_data]/$milion_fig),2); $g_tot_tobe_summery+=$summery_tobe_export[$month_data]; ?></td>
                        <td align="right" title="<?= number_format($summery_receable[$month_data],2);?>"><? echo number_format(($summery_receable[$month_data]/$milion_fig),2); $g_tot_receable_summery+=$summery_receable[$month_data]; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td align="right">Total:</td>
                    <?
                    $g_com_tot=0;
                    foreach($company_arr as $com_id=>$com_data)
                    {
                        ?>
                        <td align="right" title="<?= number_format($g_tot_com_summery[$com_id],2);?>"><? echo number_format(($g_tot_com_summery[$com_id]/$milion_fig),2); ?></td>
                        <?
                        $g_com_tot+=$g_tot_com_summery[$com_id];
                    }
                    ?>
                    <td align="right" title="<?= number_format($g_tot_upass_summery,2);?>"><? echo number_format(($g_tot_upass_summery/$milion_fig),2); $g_com_tot+=$g_tot_upass_summery; ?></td>
                    <td align="right" title="<?= number_format($g_com_tot,2);?>"><? echo number_format(($g_com_tot/$milion_fig),2); ?></td>
                    <td align="right" title="<?= number_format($g_com_tot,2);?>"><? echo number_format(($g_com_tot/$milion_fig),2); ?></td>
                    <td align="right" title="<?= number_format($g_tot_tobe_summery,2);?>"><? echo number_format(($g_tot_tobe_summery/$milion_fig),2); ?></td>
                    <td align="right" title="<?= number_format($g_tot_receable_summery,2);?>"><? echo number_format(($g_tot_receable_summery/$milion_fig),2); ?></td>
                </tr>
        </tbody>
    </table>
    <table width="<? echo $table_width_dtls; ?>" cellpadding="0" cellspacing="0" border="0" align="left">
        <tr><td>&nbsp;</td></tr>
    </table>
    </div>
    <div style="width:<?=$table_width_dtls;?>px;">
    <table width="<? echo $table_width_dtls; ?>" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
        <thead>
        	<tr>
            	<th  colspan="<? echo count($month_arr)+3; ?>" style="color:red; font-weight:bold; font-size:16px;" align="center">COMPANY WISE SUMMARY- FIGURE IN MILLION US DOLLARS</th>
            </tr>
            <tr>
                <th width="80">Company</th>
                <th width="150">Export Import Status</th>
                <?
                foreach($month_arr as $month_id=>$month_data)
                {
                    ?>
                    <th width="80"><? echo $month_data;?></th>
                    <?
                }
                ?>
                <th>Month Total</th>
            </tr>
        </thead>
        <tbody>
                <?
                $milion_fig=1000000;
                foreach($company_arr as $com_id=>$com_data)
                {
                    if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
                        <td align="center" rowspan="7" valign="middle" style="font-weight:bold;"><? echo $com_data; ?></td>
                        <td align="center">Receivable</td>
                        <?
                        $month_tot_recv=0;
                        foreach($month_arr as $month_id=>$month_data)
                        {
                            $receivable=$doc_ind_hand_arr[$com_id][$month_data]["inv_value"]+$bill_data[$com_id][$month_data]["buyer_bill"]+$bill_data[$com_id][$month_data]["bank_bill"];
                            ?>
                            <td align="right" title="<? echo $receivable."=".$doc_ind_hand_arr[$com_id][$month_data]["inv_value"]."=".$bill_data[$com_id][$month_data]["buyer_bill"]."=".number_format($bill_data[$com_id][$month_data]["bank_bill"],6,".","");?>"><a href="##" onClick="openmypage_popup('<? echo $com_id."__".$month_id; ?>','Order In Hand Info','docs_in_hand_popup');" ><? echo number_format(($receivable/$milion_fig),2); ?></a></td>
                            <?
                            $month_tot_recv+=$receivable;
                        }
                        ?>
                        <td align="right"><? echo number_format(($month_tot_recv/$milion_fig),2);?></td>
                    </tr>
                   
                    <?
                    $i++;
                    if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
                        <td align="center">To Be Export</td>
                        <?
                        $month_tobe_export=0;
                        foreach($month_arr as $month_id=>$month_data)
                        {
                            $tobe_export=$pending_ord_data_arr[$com_id][$month_data]["pending_ord_value"];
                            ?>

                            <td align="right" title="<? echo number_format($tobe_export,2);?>"><a href="##" onClick="openmypage_popup('<? echo $com_id."__".$month_id; ?>','Order In Hand Info','bank_order_in_hand_popup');" ><? echo number_format(($tobe_export/$milion_fig),2); ?></a></td>
                            <?
                            $month_tobe_export+=$tobe_export;
                        }
                        ?>
                        <td align="right"><? echo number_format(($month_tobe_export/$milion_fig),2);?></td>
                    </tr>
                     <?
                    $i++;
                    if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
                        <td align="center" style="font-size:15px; font-weight:bold;">Total Export :</td>
                        <?
                        $month_tot_export=0;
                        foreach($month_arr as $month_id=>$month_data)
                        {
                            $tot_export=($doc_ind_hand_arr[$com_id][$month_data]["inv_value"]+$bill_data[$com_id][$month_data]["buyer_bill"]+$bill_data[$com_id][$month_data]["bank_bill"])+$pending_ord_data_arr[$com_id][$month_data]["pending_ord_value"];
                            ?>

                            <td align="right" style="font-size:15px; font-weight:bold;" title="<? echo number_format($tot_export,2);?>"><? echo number_format(($tot_export/$milion_fig),2); ?></td>
                            <?
                            $month_tot_export+=$tot_export;
                        }
                        ?>
                        <td align="right" style="font-size:15px; font-weight:bold;"><? echo number_format(($month_tot_export/$milion_fig),2);?></td>
                    </tr>
                    <?
                    $i++;
                    if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
                        <td align="center">Usance (DEF)</td>
                        <?
                        $month_usence=0;
                        foreach($month_arr as $month_id=>$month_data)
                        {
                            $usence=$import_data[$com_id][$month_data]["usence_value"];
                            ?>

                            <td align="right" title="<? echo number_format($usence,2);?>"><a href='#report_detals'  onclick= "openmypage('<? echo $com_id; ?>','<? echo $month_id; ?>','ifdbc_popup','Usance (DEF)','2')"><? echo number_format(($usence/$milion_fig),2); ?></a></td>
                            <?
                            $month_usence+=$usence;
                        }
                        ?>
                        <td align="right"><? echo number_format(($month_usence/$milion_fig),2);?></td>
                    </tr>
                    <?
                    $i++;
                    if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
                        <td align="center">PAD / EDF</td>
                        <?
                        $month_edf_pad=0;
                        foreach($month_arr as $month_id=>$month_data)
                        {
                            $edf_pad=$import_data[$com_id][$month_data]["edf_value"];
                            ?>

                            <td align="right" title="<? echo number_format($edf_pad,2);?>"><a href='#report_detals'  onclick= "openmypage('<? echo $com_id; ?>','<? echo $month_id; ?>','ifdbc_popup','PAD/EDF Info','3')"><? echo number_format(($edf_pad/$milion_fig),2); ?></a></td>
                            <?
                            $month_edf_pad+=$edf_pad;
                        }
                        ?>
                        <td align="right"><? echo number_format(($month_edf_pad/$milion_fig),2);?></td>
                    </tr>
                    <?
                    $i++;
                    if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
                        <td align="center">UPASS / MIX UPASS</td>
                        <?
                        $month_upass=0;
                        foreach($month_arr as $month_id=>$month_data)
                        {
                            $upass=$import_data[$com_id][$month_data]["upass_value"];
                            ?>

                            <td align="right" title="<? echo number_format($upass,2);?>"><a href='#report_detals'  onclick= "openmypage('<? echo $com_id; ?>','<? echo $month_id; ?>','ifdbc_popup','UPASS / MIX UPASS Info','4')"><? echo number_format(($upass/$milion_fig),2); ?></a></td>
                            <?
                            $month_upass+=$upass;
                        }
                        ?>
                        <td align="right" title="<?= $month_upass;?>"><? echo number_format(($month_upass/$milion_fig),2);?></td>
                    </tr>
                    <?
                    $i++;
                    if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
                        <td align="center" style="font-size:15px; font-weight:bold;">Import Liability</td>
                        <?
                        $month_import_liability=0;
                        foreach($month_arr as $month_id=>$month_data)
                        {
                            $import_liability=$import_data[$com_id][$month_data]["usence_value"]+$import_data[$com_id][$month_data]["edf_value"]+$import_data[$com_id][$month_data]["upass_value"];
                            ?>

                            <td align="right" style="font-size:15px; font-weight:bold;" title="<? echo number_format($import_liability,2);?>"><? echo number_format(($import_liability/$milion_fig),2); ?></td>
                            <?
                            $month_import_liability+=$import_liability;
                        }
                        ?>
                        <td align="right" style="font-size:15px; font-weight:bold;"><? echo number_format(($month_import_liability/$milion_fig),2);?></td>
                    </tr>
                    <tr bgcolor="#CCCCCC" style="cursor:pointer">
                        <td align="right" colspan="2" style="font-size:15px; font-weight:bold;">Different Export With Liability : &nbsp;</td>
                        <?
                        $month_export_import_diff=0;
                        foreach($month_arr as $month_id=>$month_data)
                        {
							$tot_export=($doc_ind_hand_arr[$com_id][$month_data]["inv_value"]+$bill_data[$com_id][$month_data]["buyer_bill"]+$bill_data[$com_id][$month_data]["bank_bill"])+$pending_ord_data_arr[$com_id][$month_data]["pending_ord_value"];
                            $import_liability=$import_data[$com_id][$month_data]["usence_value"]+$import_data[$com_id][$month_data]["edf_value"]+$import_data[$com_id][$month_data]["upass_value"];
							$export_import_diff=$tot_export-$import_liability;
							$month_export_import_diff+=$export_import_diff;
                            ?>
                            <td align="right" style="font-size:15px; font-weight:bold;" title="<? echo number_format($export_import_diff,2);?>">
							<? 
							if($export_import_diff<0)
							{
								$export_import_diff=abs($export_import_diff);
								echo "(".number_format(($export_import_diff/$milion_fig),2).")";
							}
							else
							{
								echo number_format(($export_import_diff/$milion_fig),2);
							} 
							?>
                            </td>
                            <?
                            $month_import_liability+=$import_liability;
                        }
                        ?>
                        <td align="right" style="font-size:15px; font-weight:bold;">
						<?
						if($month_export_import_diff<0)
						{
							$month_export_import_diff=abs($month_export_import_diff);
							echo "(".number_format(($month_export_import_diff/$milion_fig),2).")";
						}
						else
						{
							echo number_format(($month_export_import_diff/$milion_fig),2);
						}  
						?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
                    <td align="center" rowspan="7"  valign="middle" style="font-weight:bold;">Group Total</td>
                    <td align="center">Receivable</td>
                    <?
                    $month_tot_recv=0;
                    foreach($month_arr as $month_id=>$month_data)
                    {
                        $receivable=$summery_receable[$month_data];
                        ?>
                        <td align="right" title="<? echo number_format($receivable,2);?>"><? echo number_format(($receivable/$milion_fig),2); ?></td>
                        <?
                        $month_tot_recv+=$receivable;
                    }
                    ?>
                    <td align="right"><? echo number_format(($month_tot_recv/$milion_fig),2);?></td>
                </tr>
                <?
				$i++;
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
					<td align="center">To Be Export</td>
					<?
					$month_tobe_export=0;
					foreach($month_arr as $month_id=>$month_data)
					{
						$tobe_export=$summery_tobe_export[$month_data];
						?>

						<td align="right" title="<? echo number_format($tobe_export,2);?>"><? echo number_format(($tobe_export/$milion_fig),2); ?></td>
						<?
						$month_tobe_export+=$tobe_export;
					}
					?>
					<td align="right"><? echo number_format(($month_tobe_export/$milion_fig),2);?></td>
				</tr>
				 <?
				$i++;
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
					<td align="center" style="font-size:15px; font-weight:bold;">Total Export :</td>
					<?
					$month_tot_export=0;
					foreach($month_arr as $month_id=>$month_data)
					{
						$tot_export=$summery_receable[$month_data]+$summery_tobe_export[$month_data];
						?>

						<td align="right" style="font-size:15px; font-weight:bold;" title="<? echo number_format($tot_export,2);?>"><? echo number_format(($tot_export/$milion_fig),2); ?></td>
						<?
						$month_tot_export+=$tot_export;
					}
					?>
					<td align="right" style="font-size:15px; font-weight:bold;"><? echo number_format(($month_tot_export/$milion_fig),2);?></td>
				</tr>
				<?
				$i++;
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
					<td align="center">Usance (DEF)</td>
					<?
					$month_usence=0;
					foreach($month_arr as $month_id=>$month_data)
					{
						$usence=$sum_usence_arr[$month_data];
						?>
						<td align="right" title="<? echo number_format($month_usence,2);?>"><? echo number_format(($usence/$milion_fig),2); ?></td>
						<?
						$month_usence+=$usence;
					}
					?>
					<td align="right"><? echo number_format(($month_usence/$milion_fig),2);?></td>
				</tr>
				<?
				$i++;
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
					<td align="center">PAD / EDF</td>
					<?
					$month_edf_pad=0;
					foreach($month_arr as $month_id=>$month_data)
					{
						$edf_pad=$sum_edf_arr[$month_data];
						?>

						<td align="right" title="<? echo number_format($edf_pad,2);?>"><? echo number_format(($edf_pad/$milion_fig),2); ?></td>
						<?
						$month_edf_pad+=$edf_pad;
					}
					?>
					<td align="right"><? echo number_format(($month_edf_pad/$milion_fig),2);?></td>
				</tr>
				<?
				$i++;
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
					<td align="center">UPASS / MIX UPASS</td>
					<?
					$month_upass=0;
					foreach($month_arr as $month_id=>$month_data)
					{
						$upass=$upass_summery[$month_data];
						?>

						<td align="right" title="<? echo number_format($upass,2);?>"><? echo number_format(($upass/$milion_fig),2); ?></td>
						<?
						$month_upass+=$upass;
					}
					?>
					<td align="right" title="<? echo $month_upass; ?>"><? echo number_format(($month_upass/$milion_fig),2);?></td>
				</tr>
				<?
				$i++;
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
					<td align="center" style="font-size:15px; font-weight:bold;">Import Liability</td>
					<?
					$month_import_liability=0;
					foreach($month_arr as $month_id=>$month_data)
					{
						$import_liability=$sum_usence_arr[$month_data]+$sum_edf_arr[$month_data]+$upass_summery[$month_data];
						?>

						<td align="right" style="font-size:15px; font-weight:bold;" title="<? echo number_format($import_liability,2);?>"><? echo number_format(($import_liability/$milion_fig),2); ?></td>
						<?
						$month_import_liability+=$import_liability;
					}
					?>
					<td align="right" style="font-size:15px; font-weight:bold;"><? echo number_format(($month_import_liability/$milion_fig),2);?></td>
				</tr>
				<tr bgcolor="#CCCCCC" style="cursor:pointer">
					<td align="right" colspan="2" style="font-size:15px; font-weight:bold;">Different Export With Liability : &nbsp;</td>
					<?
					$month_export_import_diff=0;
					foreach($month_arr as $month_id=>$month_data)
					{
						$tot_export=$summery_receable[$month_data]+$summery_tobe_export[$month_data];
						$import_liability=$sum_usence_arr[$month_data]+$sum_edf_arr[$month_data]+$upass_summery[$month_data];
						$export_import_diff=$tot_export-$import_liability;
						$month_export_import_diff+=$export_import_diff;
						?>
						<td align="right" style="font-size:15px; font-weight:bold;" title="<? echo number_format($export_import_diff,2);?>">
						<? 
						if($export_import_diff<0)
						{
							$export_import_diff=abs($export_import_diff);
							echo "(".number_format(($export_import_diff/$milion_fig),2).")";
						}
						else
						{
							echo number_format(($export_import_diff/$milion_fig),2);
						} 
						?>
						</td>
						<?
						$month_import_liability+=$import_liability;
					}
					?>
					<td align="right" style="font-size:15px; font-weight:bold;">
					<?
					if($month_export_import_diff<0)
					{
						$month_export_import_diff=abs($month_export_import_diff);
						echo "(".number_format(($month_export_import_diff/$milion_fig),2).")";
					}
					else
					{
						echo number_format(($month_export_import_diff/$milion_fig),2);
					}  
					?></td>
				</tr>
        </tbody>
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


if($action=="btb_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	
	if($db_type==0)
	{
		$inv_sql="select a.id as btb_lc_id, c.current_acceptance_value as accep_value
		from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and CONVERT(a.lc_category, SIGNED)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id<>2";
	}
	else
	{
		$inv_sql="select a.id as btb_lc_id, c.current_acceptance_value as accep_value
		from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id<>2";
	}
	
	$inv_result=sql_select($inv_sql);
	$accp_data=array();
	foreach($inv_result as $row)
	{
		$accp_data[$row[csf("btb_lc_id")]]+=$row[csf("accep_value")];
	}
	
	if($db_type==0)
	{
		$btb_sql="select a.id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.ref_closing_status<>1 and CONVERT(a.lc_category, SIGNED)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id<>2 order by CONVERT(a.lc_category, SIGNED) ";
	}
	else
	{
		$btb_sql="select a.id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id<>2 order by to_number(a.lc_category) ";
	}
	
	//echo $btb_sql;//die;
	$btb_sql_result=sql_select($btb_sql);
	/*$btb_data=array();$all_btb_company=array();$all_btb_bank=array();
	foreach($btb_sql_result as $row)
	{
		$all_btb_company[$row[csf("importer_id")]]=$row[csf("importer_id")];
		$all_btb_bank[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]=$row[csf("issuing_bank_id")];
		$btb_data[$row[csf("importer_id")]][$row[csf("issuing_bank_id")]]+=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
		$btb_bank_total[$row[csf("issuing_bank_id")]]+=$row[csf("lc_value")]-$lc_wise_payment[$row[csf("id")]];
	}*/
	?>
    
    <script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
    
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container" align="center" style="width:820px">
	<fieldset style="width:820px;">
        <table class="rpt_table" border="1" rules="all" width="800" cellpadding="0" cellspacing="0">
            <thead>
                <th width="50">SL</th>
                <th width="120">BTB LC No</th>
                <th width="130">Applicant</th>
                <th width="130">Bank</th>
                <th width="130"> Benficiary</th>
                <th width="70">LC Date</th>
                <th width="70">LC Expiry Date</th>
                <th>LC Amount (USD)</th>
            </thead>
            <tbody>
            <? 
			$i=1; $r=1;
			foreach($btb_sql_result as $row)  
			{
				$pendin_value =$row[csf("lc_value")]-$accp_data[$row[csf("id")]];
				if($cat_check[$row[csf("lc_category")]*1]=="" && $pendin_value>0)
				{
					$cat_check[$row[csf("lc_category")]*1]=$row[csf("lc_category")]*1;
					if($r==1)
					{
						?>
                        <tr bgcolor="#FFFFCC"><td colspan="8" title="<? echo $row[csf("lc_category")]; ?>"><? echo $supply_source[$row[csf("lc_category")]*1]; ?></td></tr>
                        <?
					}
					else
					{
						?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="7" align="right" title="<? echo $row[csf("lc_category")]; ?>">Currency Wise Product Total:</td>
                            <td align="right"><? echo number_format($cat_pendin_value,2);  ?></td>
                        </tr>
                        <tr bgcolor="#FFFFCC"><td colspan="8" title="<? echo $row[csf("lc_category")]; ?>"><? echo $supply_source[$row[csf("lc_category")]*1]; ?></td></tr>
                        <?
					}
					$cat_pendin_value=0;$r++;
				}
				
				if(number_format($pendin_value,2)>0)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center" title="<? echo $r; ?>"><? echo $i; ?></td>
                        <td><p><? echo $row[csf('lc_number')]; ?>&nbsp;</p></td>
                        <td><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                        <td><p><? echo $bank_arr[$row[csf('issuing_bank_id')]]; ?>&nbsp;</p></td>
                        <td><p><? echo $company_arr[$row[csf('importer_id')]]; ?>&nbsp;</p></td>
                        <td align="center"><p><? if($row[csf('lc_date')] !="" && $row[csf('lc_date')] !="0000-00-00") echo change_date_format($row[csf('lc_date')]);  ?>&nbsp;</p></td>
                        <td align="center"><p><? if($row[csf('lc_expiry_date')] !="" && $row[csf('lc_expiry_date')] !="0000-00-00") echo change_date_format($row[csf('lc_expiry_date')]);  ?>&nbsp;</p></td>
                        <td align="right" title="<?= $pendin_value;?>"><? echo number_format($pendin_value,2);  ?></td>
                    </tr>
                    <?
                    $tot_pendin_value+=$pendin_value;
					$cat_pendin_value+=$pendin_value;
					$i++;
				}
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td colspan="7" align="right">Currency Wise Product Total:</td>
                <td align="right"><? echo number_format($cat_pendin_value,2);  ?></td>
            </tr>
            </tbody>
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="7" align="right">Total:</td>
                    <td align="right" id="value_tot_pendin_value"><? echo number_format($tot_pendin_value,2); ?></td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
}

if($action=="margine_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	/*$inv_sql="select a.id as btb_lc_id, b.invoice_no, b.maturity_date, c.current_acceptance_value as accep_value
	from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
	where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id=2";
	$inv_result=sql_select($inv_sql);
	$accp_data=array();

	foreach($inv_result as $row)
	{
		$accp_data[$row[csf("btb_lc_id")]]+=$row[csf("accep_value")];
	}*/
	
	
	$sql_cond_payment="";
	if($company_id>0) $sql_cond_payment=" and a.company_id=$company_id";
	$sql_payment="select a.lc_id, b.invoice_id, b.accepted_ammount 
	from com_import_payment b left join com_import_payment_mst a on b.mst_id=a.id and a.status_active=1 and a.is_deleted=0 $sql_cond_payment
	 where b.status_active=1 and b.is_deleted=0";
	$sql_payment_result=sql_select($sql_payment);
	$lc_wise_payment=array();$invoice_wise_payment=array();
	foreach($sql_payment_result as $row)
	{
		//$lc_wise_payment[$row[csf("lc_id")]]+=$row[csf("accepted_ammount")];
		$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")];
	}
	
	
	if($db_type==0)
	{
		$btb_sql="select a.id as btb_lc_id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.payterm_id, a.lc_value, b.id as inv_id, b.invoice_no, b.maturity_date, b.edf_paid_date, sum(c.current_acceptance_value) as accep_value
		from com_btb_lc_master_details a, com_import_invoice_dtls c, com_import_invoice_mst b 
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.is_deleted=0 and a.status_active=1  and a.ref_closing_status<>1 and CONVERT(a.lc_category, SIGNED)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id=2
		group by a.id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.payterm_id, a.lc_value, b.id, b.invoice_no, b.maturity_date, b.edf_paid_date
		order by a.lc_category, a.id";
	}
	else
	{
		$btb_sql="select a.id as btb_lc_id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.payterm_id, a.lc_value, b.id as inv_id, b.invoice_no, b.maturity_date, b.edf_paid_date, sum(c.current_acceptance_value) as accep_value
		from com_btb_lc_master_details a, com_import_invoice_dtls c, com_import_invoice_mst b 
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.is_deleted=0 and a.status_active=1  and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id=2
		group by a.id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.payterm_id, a.lc_value, b.id, b.invoice_no, b.maturity_date, b.edf_paid_date
		order by a.lc_category, a.id";
	}
	
	
	//echo $btb_sql;	
	/*
	$btb_sql="select a.id as btb_lc_id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.lc_value, b.invoice_no, b.maturity_date, sum(c.current_acceptance_value) as accep_value
	from com_btb_lc_master_details a 
	left join com_import_invoice_dtls c on a.id=c.btb_lc_id and c.is_deleted=0 and c.status_active=1
	left join com_import_invoice_mst b on c.import_invoice_id=b.id and b.is_deleted=0 and b.status_active=1
	where a.is_deleted=0 and a.status_active=1  and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id=2
	group by a.id, a.lc_number, a.lc_category, a.currency_id, a.margin, a.supplier_id, a.lc_value, b.invoice_no, b.maturity_date
	order by a.lc_category, a.id";
	$btb_sql="select a.id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value from com_btb_lc_master_details a where a.status_active=1 and a.is_deleted=0 and a.ref_closing_status<>1 and to_number(a.lc_category)>0 and a.importer_id=$company_id and a.issuing_bank_id=$bank_id and a.lc_type_id=2 order by to_number(a.lc_category) ";*/
	//echo $btb_sql;//die;
	$btb_sql_result=sql_select($btb_sql);
	?>
    
    <script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
    
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container" align="center" style="width:1040px">
	<fieldset style="width:1040px;">
        <table class="rpt_table" border="1" rules="all" width="1040" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="120">Margin LC No</th>
                <th width="120">Invoice No</th>
                <th width="70">Currency</th>
                <th width="90">LC Amount</th>
                <th width="90">Accpt. Amount</th>
                <th width="90">Margin Amount</th>
                <th width="90">Net Due</th>
                <th width="90">Net Due (USD)</th>
                <th width="70">Maturity Date</th>
                <th>Supplier</th>
            </thead>
            <tbody>
            <? 
			$i=1; $r=1;
			foreach($btb_sql_result as $row)  
			{
				$margine_amt=0;
				//$pendin_value =$row[csf("lc_value")]-$accp_data[$row[csf("id")]];
				$payment_value=0;
				if($row[csf("payterm_id")]==1)
				{
					if($row[csf("edf_paid_date")]!="" && $row[csf("edf_paid_date")]!="0000-00-00")
					{
						$payment_value=$invoice_wise_payment[$row[csf("inv_id")]];
					}
				}
				else
				{
					$payment_value=$invoice_wise_payment[$row[csf("inv_id")]];
				}
				$pendin_value =$row[csf("accep_value")]-$payment_value;
				if($row[csf("margin")]) $margine_amt=($row[csf("lc_value")]/100)*$row[csf("margin")];
				$net_due=$row[csf('accep_value')]-$margine_amt;
				if($row[csf("currency_id")]==7) $net_due_usd=$net_due * 0.0093;
				else if($row[csf("currency_id")]==3) $net_due_usd=$net_due * 1.10;
				else $net_due_usd=$net_due * 1;
				if($cat_check[$row[csf("lc_category")]*1]=="" && $pendin_value>0)
				{
					$cat_check[$row[csf("lc_category")]*1]=$row[csf("lc_category")]*1;
					
					if($r==1)
					{
						?>
                        <tr bgcolor="#FFFFCC"><td colspan="11" title="<? echo $row[csf("lc_category")]; ?>"><? echo $supply_source[$row[csf("lc_category")]*1]; ?></td></tr>
                        <?
					}
					else
					{
						?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="8" align="right" title="<? echo $row[csf("lc_category")]; ?>">Currency Wise Product Total:</td>
                            <td align="right"><? echo number_format($cat_net_due_usd,2);  ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr bgcolor="#FFFFCC"><td colspan="11" title="<? echo $row[csf("lc_category")]; ?>"><? echo $supply_source[$row[csf("lc_category")]*1]; ?></td></tr>
                        <?
						$cat_net_due_usd=0;
					}
					$r++;
				}
				
				if($pendin_value>0)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center" title="<? echo $r; ?>"><? echo $i; ?></td>
                        <td title="<? echo $row[csf("payterm_id")]."=".$payment_value."=".$row[csf("inv_id")];?>"><p><? echo $row[csf('lc_number')]; ?>&nbsp;</p></td>
                        <td><p><? echo $row[csf('invoice_no')]; ?>&nbsp;</p></td>
                        <td><p><? echo $currency[$row[csf('currency_id')]]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('lc_value')],2); ?></td>
                        <td align="right"><? echo number_format($row[csf('accep_value')],2); ?></td>
                        <td align="right"><? echo number_format($margine_amt,2);  ?></td>
                        <td align="right" title="<? echo $row[csf("margin")]; ?>"><? echo number_format($net_due,2);  ?></td>
                        <td align="right"><? echo number_format($net_due_usd,2);  ?></td>
                        <td align="center"><p><? if($row[csf('maturity_date')] !="" && $row[csf('maturity_date')] !="0000-00-00") echo change_date_format($row[csf('maturity_date')]);  ?>&nbsp;</p></td>
                        <td><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                    </tr>
                    <?
                    $tot_net_due_usd+=$net_due_usd;
					$cat_net_due_usd+=$net_due_usd;
				}
				$i++;
			}
			?>
            <tr bgcolor="#CCCCCC">
                <td colspan="8" align="right" title="<? echo $row[csf("lc_category")]; ?>">Currency Wise Product Total:</td>
                <td align="right"><? echo number_format($cat_net_due_usd,2);  ?></td>
                <td></td>
                <td></td>
            </tr>
            </tbody>
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total:</td>
                    <td align="right" id="value_tot_pendin_value"><? echo number_format($tot_net_due_usd,2); ?></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
}

if($action=="ifdbc_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	//echo $upass_flag.test;die;
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	//$sql_payment="select a.lc_id, a.invoice_id, b.accepted_ammount from com_import_payment_mst a, com_import_payment b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $sql_cond_payment";
	/*$sql_payment="select a.lc_id, b.invoice_id, b.accepted_ammount from com_import_payment_mst a, com_import_payment b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $sql_cond_payment";
	//echo $sql_payment;die;
	$sql_payment_result=sql_select($sql_payment);
	$lc_wise_payment=array();$invoice_wise_payment=array();
	foreach($sql_payment_result as $row)
	{
		$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")];
	}*/
	$sql_payment="select a.lc_id, b.invoice_id, b.accepted_ammount 
	from com_import_payment b left join com_import_payment_mst a on b.mst_id=a.id and a.status_active=1  $sql_cond_payment  
	where b.status_active=1 and b.is_deleted=0";
	//echo $sql_payment;
	$sql_payment_result=sql_select($sql_payment);
	$lc_wise_payment=array();$invoice_wise_payment=array();
	foreach($sql_payment_result as $row)
	{
		//$lc_wise_payment[$row[csf("lc_id")]]+=$row[csf("accepted_ammount")];
		$invoice_wise_payment[$row[csf("invoice_id")]]+=$row[csf("accepted_ammount")];
	}
	//echo $invoice_wise_payment[322];die;
	if($type==2) $retire_source_cond=" and b.retire_source not in(30,31,142) and a.payterm_id=2"; 
	else if($type==4) $retire_source_cond=" and b.retire_source in(142)";
	else $retire_source_cond=" and b.retire_source in(30,31)";
	//$retire_source_cond
	/*$import_sql="select a.id as btb_lc_id, a.importer_id, a.issuing_bank_id, a.lc_value, a.payterm_id, b.id as import_inv_id, b.maturity_date, b.retire_source, c.current_acceptance_value as edf_loan_value
	from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
	where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and a.payterm_id=2 $sql_cond 
	order by b.maturity_date";*/
	if($db_type==0)
	{
		$ifdbc_edf_sql="select a.id as btb_lc_id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value, a.maturity_from_id, a.payterm_id, b.id as import_inv_id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, b.loan_ref, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
		from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id  and a.lc_category>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and a.importer_id=$company_id and to_char(b.maturity_date,'mm-yyyy')='$month_id' $retire_source_cond
		group by a.id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value, a.maturity_from_id, a.payterm_id, b.id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, b.loan_ref
		order by CONVERT(a.lc_category, SIGNED),btb_lc_id";
	}
	else
	{
		$ifdbc_edf_sql="select a.id as btb_lc_id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value, a.maturity_from_id, a.payterm_id, b.id as import_inv_id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, b.loan_ref, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
		from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
		where a.id=c.btb_lc_id and c.import_invoice_id=b.id  and a.lc_category>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and a.importer_id=$company_id and to_char(b.maturity_date,'mm-yyyy')='$month_id'  $retire_source_cond
		group by a.id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value, a.maturity_from_id, a.payterm_id, b.id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, b.retire_source, b.loan_ref
		order by to_number(a.lc_category),btb_lc_id";
	}
	
	//echo $ifdbc_edf_sql;//die;
	$ifdbc_edf_sql_result=sql_select($ifdbc_edf_sql);
	?>
    
    <script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
    
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container" align="center" style="width:920px">
	<fieldset style="width:920px;">
        <table class="rpt_table" border="1" rules="all" width="900" cellpadding="0" cellspacing="0">
            <thead>
                <th width="50">SL</th>
                <th width="120">BTB LC No</th>
                <?
				if($type==3)
				{
					if($upass_flag) $edf_caption="UPASS No."; else $edf_caption="EDF No.";
					?>
                    <th width="100"><?=$edf_caption;?></th>
                    <?
				}
				?>
                <th width="100">LC Amount (USD)</th>
                <th width="100">Acc. Amount (USD)</th>
                <?
				if($type==2)
				{
					?>
					<th width="100">Acc. Date</th>
					<th width="100">Maturity Date</th>
					<th> Suppliers</th>
                    <?
				}
				else
				{
					?>
					<th width="100">Disbursement Date</th>
					<th width="100">Maturity Date</th>
					<th> Suppliers</th>
                    <?
				}
				?>
            </thead>
            <tbody>
            <? 
			$i=1; $r=1;
			//echo "<pre>";print_r($ifdbc_edf_sql_result);die;
			foreach($ifdbc_edf_sql_result as $row)  
			{
				//echo $type."=".$row[csf("retire_source")]."<br>";
				$paid_value = 0;
				if($type==2)
				{
					$pending_value=$row[csf("edf_loan_value")]-$invoice_wise_payment[$row[csf("import_inv_id")]];
				}
				else
				{
					$paid_value=0;
					if($row[csf("payterm_id")]==3) 
					{
						$paid_value=$row[csf("edf_loan_value")];
					}
					else
					{
						if($row[csf("edf_paid_date")] != "" && $row[csf("edf_paid_date")] != "0000-00-00")
						{
							$paid_value=$row[csf("edf_loan_value")];
						}
					}
					$pending_value=$row[csf("edf_loan_value")]-$paid_value;
				}
				//if($row[csf('lc_number')]=="802180514423") echo $paid_value.test;
				$maturity_date="";
				//if($row[csf("maturity_from_id")]==1) $maturity_date=$row[csf("bank_acc_date")];
				//else if($row[csf("maturity_from_id")]==2 || $row[csf("maturity_from_id")]==5) $maturity_date=$row[csf("shipment_date")];
				//else if($row[csf("maturity_from_id")]==3) $maturity_date=$row[csf("nagotiate_date")];
				//else if($row[csf("maturity_from_id")]==4) $maturity_date=$row[csf("bill_date")];
				//else $maturity_date="";
				 
				
				if($cat_check[$row[csf("lc_category")]*1]=="" && $pending_value>0)
				{
					$cat_check[$row[csf("lc_category")]*1]=$row[csf("lc_category")]*1;
					if($type==3){ $tot_col_span=8;  $col_span=3;} else { $tot_col_span=7; $col_span=2;}
					if($r==1)
					{
						?>
						<tr bgcolor="#FFFFCC"><td colspan="<? echo $tot_col_span;?>" title="<? echo $row[csf("lc_category")]; ?>"><? echo $supply_source[$row[csf("lc_category")]*1]; ?></td></tr>
						<?
					}
					else
					{
						?>
						<tr bgcolor="#CCCCCC">
							<td colspan="<? echo $col_span;?>" align="right" title="<? echo $row[csf("lc_category")]; ?>">Currency Wise Product Total:</td>
							<td align="right"><? echo number_format($cat_lc_value,2);  ?></td>
							<td align="right"><? echo number_format($cat_pendin_value,2);  ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
						</tr>
						<tr bgcolor="#FFFFCC"><td colspan="<? echo $tot_col_span;?>" title="<? echo $row[csf("lc_category")]; ?>"><? echo $supply_source[$row[csf("lc_category")]*1]; ?></td></tr>
						<?
					}
					$cat_lc_value=$cat_pendin_value=0;$r++;
				}
				
				//echo $cat_check[$row[csf("lc_category")]*1]."=".$pending_value."=".$maturity_date."<br>";
				if($pending_value>0)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center" title="<? echo $r; ?>"><? echo $i; ?></td>
						<td><p><? echo $row[csf('lc_number')]; ?>&nbsp;</p></td>
                        <?
						if($type==3)
						{
							?>
                            <td><p><? echo $row[csf('loan_ref')]; ?>&nbsp;</p></td>
                            <?
						}
						?>
						<td align="right"><? echo number_format($row[csf('lc_value')],2);  ?></td>
						<td align="right" title="<? echo $row[csf("edf_loan_value")]."=".$invoice_wise_payment[$row[csf("import_inv_id")]]; ?>"><? echo number_format($pending_value,2);  ?></td>
                        <td align="center"><p><? echo change_date_format($maturity_date);  ?>&nbsp;</p></td>
                        <td align="center"><p><? if($row[csf('maturity_date')] !="" && $row[csf('maturity_date')] !="0000-00-00") echo change_date_format($row[csf('maturity_date')]);  ?>&nbsp;</p></td>
                        <td align="center"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
					</tr>
					<?
					$tot_lc_value+=$row[csf('lc_value')];
					$cat_lc_value+=$row[csf('lc_value')];
					$tot_pendin_value+=$pending_value;
					$cat_pendin_value+=$pending_value;
					$i++;
				}
			}
			
			?>
            <tr bgcolor="#CCCCCC">
                <td colspan="<? echo $col_span;?>" align="right" title="<? echo $row[csf("lc_category")]; ?>">Currency Wise Product Total:</td>
                <td align="right"><? echo number_format($cat_lc_value,2);  ?></td>
                <td align="right"><? echo number_format($cat_pendin_value,2);  ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?
			if($type==3) $col_span=3; else $col_span=2;
			
			?>
            </tbody>
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="<? echo $col_span;?>" align="right">Total:</td>
                    <td align="right" id="value_tot_lc_value"><? echo number_format($tot_lc_value,2); ?></td>
                    <td align="right" id="value_tot_pendin_value"><? echo number_format($tot_pendin_value,2); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
}

if($action=="fdbp_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	//echo $type.test;die;
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	//$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	//$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	$lc_sc_sql=sql_select("select id, export_lc_no, 1 as type from com_export_lc where beneficiary_name=$company_id
	union all select id, contract_no as export_lc_no, 2 as type from com_sales_contract where beneficiary_name=$company_id");
	$lc_sc_num=array();
	foreach($lc_sc_sql as $row)
	{
		$lc_sc_num[$row[csf("id")]][$row[csf("type")]]=$row[csf("export_lc_no")];
	}
	unset($lc_sc_sql);
	$proceed_rlz_sql="select b.invoice_bill_id, c.document_currency as document_currency 
	from com_export_proceed_realization b, com_export_proceed_rlzn_dtls c 
	where b.id=c.mst_id and b.is_invoice_bill=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.benificiary_id=$company_id";
	//echo $proceed_rlz_sql;die;
	$realize_data_arr=array();
	$proceed_rlz_sql_result=sql_select($proceed_rlz_sql);
	foreach($proceed_rlz_sql_result as $row)
	{
		$realize_data_arr[$row[csf("invoice_bill_id")]]+=$row[csf("document_currency")];
	}
	unset($proceed_rlz_sql_result);
	if($db_type==0)
	{
		$invoice_sql="select b.doc_submission_mst_id, group_concat(a.invoice_no) as invoice_no, sum(a.invoice_quantity) as invoice_quantity 
		from com_export_invoice_ship_mst a, com_export_doc_submission_invo b, com_export_doc_submission_mst c 
		where a.id=b.invoice_id and b.doc_submission_mst_id=c.id and c.entry_form = 40 and c.lien_bank > 0 and c.submit_type=2 and c.company_id=$company_id and c.lien_bank=$bank_id and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.doc_submission_mst_id";
	}
	else
	{
		$invoice_sql="select b.doc_submission_mst_id, listagg(cast(a.invoice_no as varchar(4000)),',') within group (order by a.invoice_no) as invoice_no, sum(a.invoice_quantity) as invoice_quantity 
		from com_export_invoice_ship_mst a, com_export_doc_submission_invo b, com_export_doc_submission_mst c 
		where a.id=b.invoice_id and b.doc_submission_mst_id=c.id and c.entry_form = 40 and c.lien_bank > 0 and c.submit_type=2 and c.company_id=$company_id and c.lien_bank=$bank_id and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.doc_submission_mst_id";
	}
	//echo $invoice_sql;die;
	$invoice_result=sql_select($invoice_sql);
	$invoice_data=array();
	foreach($invoice_result as $row)
	{
		$invoice_data[$row[csf("doc_submission_mst_id")]]["invoice_no"]=implode(",",array_unique(explode(",",$row[csf("invoice_no")])));
		$invoice_data[$row[csf("doc_submission_mst_id")]]["invoice_quantity"]=$row[csf("invoice_quantity")];
	}
	unset($invoice_result);
	
	$bill_sql="select b.id as bill_id, b.buyer_id, b.bank_ref_no as bill_no, b.submit_date as bill_date, b.company_id, b.possible_reali_date, b.lien_bank, a.is_lc, a.lc_sc_id, a.id as bill_dtls_id, a.net_invo_value as bill_value, c.id as bill_pur_id, c.lc_sc_curr as bill_purchase_value 
	from com_export_doc_submission_invo a, com_export_doc_submission_mst b, com_export_doc_sub_trans c
	where a.doc_submission_mst_id=b.id and b.id=c.doc_submission_mst_id  and b.entry_form = 40 and b.lien_bank > 0 and b.submit_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id and b.lien_bank=$bank_id";
	//echo $bill_sql;
	$bill_sql_result=sql_select($bill_sql);
	$fdbp_data=array();
	foreach($bill_sql_result as $row)
	{
		$fdbp_data[$row[csf("bill_id")]]["bill_id"]=$row[csf("bill_id")];
		$fdbp_data[$row[csf("bill_id")]]["buyer_id"]=$row[csf("buyer_id")];
		$fdbp_data[$row[csf("bill_id")]]["bill_no"]=$row[csf("bill_no")];
		$fdbp_data[$row[csf("bill_id")]]["bill_date"]=$row[csf("bill_date")];
		$fdbp_data[$row[csf("bill_id")]]["company_id"]=$row[csf("company_id")];
		$fdbp_data[$row[csf("bill_id")]]["possible_reali_date"]=$row[csf("possible_reali_date")];
		$fdbp_data[$row[csf("bill_id")]]["lien_bank"]=$row[csf("lien_bank")];
		$fdbp_data[$row[csf("bill_id")]]["is_lc"]=$row[csf("is_lc")];
		$fdbp_data[$row[csf("bill_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
		if($bill_dtls_check[$row[csf("bill_dtls_id")]]=="")
		{
			$bill_dtls_check[$row[csf("bill_dtls_id")]]=$row[csf("bill_dtls_id")];
			$fdbp_data[$row[csf("bill_id")]]["bill_value"]+=$row[csf("bill_value")];
			$tot_bill_valu+=$row[csf("bill_value")];
		}
		if($bill_purchase_check[$row[csf("bill_pur_id")]]=="")
		{
			$bill_purchase_check[$row[csf("bill_pur_id")]]=$row[csf("bill_pur_id")];
			$fdbp_data[$row[csf("bill_id")]]["bill_purchase_value"]+=$row[csf("bill_purchase_value")];
		}
		
	}
	//echo "<br>".$tot_bill_valu.jahid;
	?>
    <script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");

		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
    
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container" align="center" style="width:1030px">
	<fieldset style="width:1030px;">
        <table class="rpt_table" border="1" rules="all" width="1030" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="130">Invoice No.</th>
                <th width="100">Export Bill No.</th>
                <th width="70">Bill Date</th>
                <th width="90">Inv/Bill Qty/Pcs</th>
                <th width="100">Bill Value</th>
                <th width="100">Purchase Amount</th>
                <th width="60">(%)</th>
                <th width="70">Purchase Date</th>
                <th width="100">LC/SC No</th>
                <th width="60">LC / SC</th>
                <th>Buyer Name</th>
            </thead>
            <tbody>
            <? 
			$i=1; $r=1;
			//echo "<pre>";print_r($ifdbc_edf_sql_result);die;
			$tot_pendin_value=0;
			foreach($fdbp_data as $bill_id=>$row)  
			{
				//echo $type."=".$row[csf("retire_source")]."<br>";
				$pendin_value = 0;
				$pendin_value=$row[("bill_value")]-$realize_data_arr[$row[("bill_id")]];
				$bill_purchase_percent=(($row[("bill_purchase_value")]/$row[("bill_value")])*100);
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($pendin_value>0)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center" title="<? echo $r; ?>"><? echo $i; ?></td>
						<td><p><? echo $invoice_data[$row[("bill_id")]]["invoice_no"]; ?>&nbsp;</p></td>
						<td><p><? echo $row[("bill_no")]; ?>&nbsp;</p></td>
                        <td align="center"><p><? if($row[('bill_date')] !="" && $row[('bill_date')] !="0000-00-00") echo change_date_format($row[('bill_date')]);?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($invoice_data[$row[("bill_id")]]["invoice_quantity"],2);  ?></td>
                        <td align="right"><? echo number_format($row[("bill_value")],2);  ?></td>
                        <td align="right"><? echo number_format($row[("bill_purchase_value")],2);  ?></td>
						<td align="right"><p><? echo number_format($bill_purchase_percent,2);?>&nbsp;</p></td>
                        <td align="center"><p><? if($row[('bill_date')] !="" && $row[('bill_date')] !="0000-00-00") echo change_date_format($row[('bill_date')]);?>&nbsp;</p></td>
						<td><p><? echo $lc_sc_num[$row[('lc_sc_id')]][$row[("is_lc")]]; ?>&nbsp;</p></td>
						<td align="center"><p><? if($row[("is_lc")]==1) echo "LC"; else echo "SC"; ?>&nbsp;</p></td>
						<td><p><? echo $buyer_arr[$row[("buyer_id")]]; ?>&nbsp;</p></td>
					</tr>
					<?
					$tot_bill_qnty+=$invoice_data[$row[("bill_id")]]["invoice_quantity"];
					$tot_bill_value+=$row[('bill_value')];
					$tot_bill_purchase_value+=$row[('bill_purchase_value')];
					$tot_pendin_value+=$pendin_value;
					$i++;
				}
				
			}
			?>
            </tbody>
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="4" align="right" title="<? echo $tot_pendin_value; ?>">Total:</td>
                    <td align="right" id="tot_bill_qnty"><? echo number_format($tot_bill_qnty,2); ?></td>
                    <td align="right" id="value_tot_bill_value"><? echo number_format($tot_bill_value,2); ?></td>
                    <td align="right" id="value_tot_bill_purchase_value"><? echo number_format($tot_bill_purchase_value,2); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
}

if($action=="docs_in_hand_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$month_id=$data_ref[1];
	$bill_com_cond="";
	if($com_id>0) $bill_com_cond=" and b.company_id=$com_id";
	if($bank_id>0) $bill_bank_cond=" and b.lien_bank=$bank_id";
	if($month_id) $bank_ship_cond=" and to_char(b.possible_reali_date,'mm-yyyy')='$month_id'";
	$bill_coll_sql="select a.invoice_id, sum(a.net_invo_value) as bill_value 
	from com_export_doc_submission_invo a, com_export_doc_submission_mst b 
	where a.doc_submission_mst_id=b.id and b.entry_form = 40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.possible_reali_date is not null $bill_com_cond $bill_bank_cond $bank_ship_cond 
	group by a.invoice_id";
	//echo $bill_coll_sql;
	$bill_coll_sql_result=sql_select($bill_coll_sql);
	$bill_receive=0;
	foreach($bill_coll_sql_result as $row)
	{
		$inv_sub_arr[$row[csf("invoice_id")]]=$row[csf("bill_value")];
	}
	
	/*$bill_coll_sql="select a.invoice_id, sum(a.net_invo_value) as bill_value 
	from com_export_doc_submission_invo a, com_export_doc_submission_mst b 
	where a.doc_submission_mst_id=b.id and b.entry_form = 40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
	group by a.invoice_id";
	$bill_coll_sql_result=sql_select($bill_coll_sql);
	$bill_receive=0;
	foreach($bill_coll_sql_result as $row)
	{
		$inv_sub_arr[$row[csf("invoice_id")]]=$row[csf("bill_value")];
	}*/
	
	unset($bill_coll_sql_result);
	if($com_id>0) $com_ship_cond=" and b.benificiary_id=$com_id";
	if($month_id) $bank_ship_cond=" and to_char(d.possible_reali_date,'mm-yyyy')='$month_id'";
	
	$inv_buyer_sql="select a.buyer_name, a.lien_bank, a.contract_no as lc_sc_no, a.pay_term, a.contract_value as lc_sc_value, b.benificiary_id, b.id as inv_id, b.net_invo_value, b.invoice_no, b.invoice_date, b.invoice_quantity, b.invoice_value, b.ex_factory_date, d.submit_date, d.possible_reali_date 
	from com_sales_contract a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_doc_submission_mst d 
	where a.id=b.lc_sc_id and b.id=c.invoice_id and c.doc_submission_mst_id=d.id and d.entry_form = 39 and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $com_ship_cond $bank_ship_cond $ex_data_cond";
	//echo $inv_buyer_sql;//die;
	$inv_buyer_result=sql_select($inv_buyer_sql);
	$inv_buyer_data=array();
	foreach($inv_buyer_result as $row)
	{
		$inv_rate=$row[csf("net_invo_value")]/$row[csf("invoice_quantity")];
		$pending_value=$row[csf("net_invo_value")]-$inv_sub_arr[$row[csf("inv_id")]];
		$buyer_submit_inv[$row[csf("inv_id")]]=$row[csf("inv_id")];
		if($pending_value>0)
		{
			if($inv_sub_arr[$row[csf("inv_id")]]=="") $inv_sub_arr[$row[csf("inv_id")]]+=$row[csf("net_invo_value")];
			
			$pending_qnty=$pending_value/$inv_rate;
			$inv_buyer_data[$row[csf("inv_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$inv_buyer_data[$row[csf("inv_id")]]["lien_bank"]=$row[csf("lien_bank")];
			$inv_buyer_data[$row[csf("inv_id")]]["lc_sc_no"]=$row[csf("lc_sc_no")];
			$inv_buyer_data[$row[csf("inv_id")]]["pay_term"]=$row[csf("pay_term")];
			$inv_buyer_data[$row[csf("inv_id")]]["lc_sc_value"]=$row[csf("lc_sc_value")];
			$inv_buyer_data[$row[csf("inv_id")]]["benificiary_id"]=$row[csf("benificiary_id")];
			$inv_buyer_data[$row[csf("inv_id")]]["invoice_no"]=$row[csf("invoice_no")];
			$inv_buyer_data[$row[csf("inv_id")]]["invoice_date"]=$row[csf("invoice_date")];
			$inv_buyer_data[$row[csf("inv_id")]]["invoice_no"]=$row[csf("invoice_no")];
			$inv_buyer_data[$row[csf("inv_id")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
			if($row[csf("ex_factory_date")]!="" && $row[csf("ex_factory_date")]!="0000-00-00")
			{
				$inv_buyer_data[$row[csf("inv_id")]]["maturity_date"]=date('d-m-Y',(strtotime($row[csf("ex_factory_date")])+(86400*45)));
			}
			else
			{
				$inv_buyer_data[$row[csf("inv_id")]]["maturity_date"]="";
			}
			
			$inv_buyer_data[$row[csf("inv_id")]]["aging_days"]=((strtotime(date('d-m-Y'))-strtotime($row[csf("submit_date")]))/86400);
			$inv_buyer_data[$row[csf("inv_id")]]["pending_qnty"]=$pending_qnty;
			$inv_buyer_data[$row[csf("inv_id")]]["pending_value"]=$pending_value;
		}
	}
	unset($inv_buyer_result);
	//echo "<pre>";print_r($buyer_submit_inv);die;
	if($db_type==0)
	{
		$ex_data_cond=" and b.ex_factory_date!='0000-00-00'";
	}
	else
	{
		$ex_data_cond=" and b.ex_factory_date is not null";
	}
	$inv_sql=" select a.buyer_name, a.lien_bank, a.export_lc_no as lc_sc_no, a.pay_term, a.lc_value as lc_sc_value, a.tenor, b.benificiary_id, b.id as inv_id, b.net_invo_value, b.invoice_no, b.invoice_date, b.invoice_quantity, b.invoice_value, b.ex_factory_date, b.is_lc
	from com_export_lc a, com_export_invoice_ship_mst b 
	where a.id=b.lc_sc_id and b.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id not in(select INVOICE_ID from com_export_doc_submission_invo where status_active=1) $com_ship_cond $ex_data_cond
	union all
	select a.buyer_name, a.lien_bank, a.contract_no as lc_sc_no, a.pay_term, a.contract_value as lc_sc_value, a.tenor, b.benificiary_id, b.id as inv_id, b.net_invo_value, b.invoice_no, b.invoice_date, b.invoice_quantity, b.invoice_value, b.ex_factory_date, b.is_lc 
	from com_sales_contract a, com_export_invoice_ship_mst b 
	where a.id=b.lc_sc_id and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id not in(select INVOICE_ID from com_export_doc_submission_invo where status_active=1) $com_ship_cond $ex_data_cond";
	//echo $inv_sql;die;
	$inv_sql_result=sql_select($inv_sql);
	$inv_data=array();
	foreach($inv_sql_result as $row)
	{
		$inv_rate=$row[csf("net_invo_value")]/$row[csf("invoice_quantity")];
		$pending_value=$row[csf("net_invo_value")]-$inv_sub_arr[$row[csf("inv_id")]];
		$ex_date=date('m-Y',(strtotime($row[csf("ex_factory_date")])+(86400*45)));
		//$pending_value>0 && $buyer_submit_inv[$row[csf("inv_id")]] =="" &&
		if($month_id==$ex_date && $pending_value>0)
		{
			$pending_qnty=$pending_value/$inv_rate;
			$inv_data[$row[csf("inv_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$inv_data[$row[csf("inv_id")]]["lien_bank"]=$row[csf("lien_bank")];
			$inv_data[$row[csf("inv_id")]]["lc_sc_no"]=$row[csf("lc_sc_no")];
			$inv_data[$row[csf("inv_id")]]["pay_term"]=$row[csf("pay_term")];
			$inv_data[$row[csf("inv_id")]]["lc_sc_value"]=$row[csf("lc_sc_value")];
			$inv_data[$row[csf("inv_id")]]["benificiary_id"]=$row[csf("benificiary_id")];
			$inv_data[$row[csf("inv_id")]]["invoice_no"]=$row[csf("invoice_no")];
			$inv_data[$row[csf("inv_id")]]["invoice_date"]=$row[csf("invoice_date")];
			$inv_data[$row[csf("inv_id")]]["invoice_no"]=$row[csf("invoice_no")];
			$inv_data[$row[csf("inv_id")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
			$inv_data[$row[csf("inv_id")]]["is_lc"]=$row[csf("is_lc")];
			$inv_data[$row[csf("inv_id")]]["maturity_date_cal"]=$row[csf("ex_factory_date")]."__".$row[csf("tenor")]."__".$row[csf("is_lc")];
			if($row[csf("ex_factory_date")]!="" && $row[csf("ex_factory_date")]!="0000-00-00")
			{
				if($row[csf("is_lc")]==1)
				{
					$tenor_priod=0;
					if($row[csf("tenor")]!="" && $row[csf("tenor")]>0) $tenor_priod=86400*$row[csf("tenor")];
					$maturity_date=strtotime($row[csf("ex_factory_date")])+$tenor_priod+(86400*10);
					$inv_data[$row[csf("inv_id")]]["maturity_date"]=date('d-m-Y',$maturity_date);
				}
				else
				{
					$inv_data[$row[csf("inv_id")]]["maturity_date"]=date('d-m-Y',(strtotime($row[csf("ex_factory_date")])+(86400*45)));
				}
			}
			else
			{
				$inv_data[$row[csf("inv_id")]]["maturity_date"]="";
			}
			
			$inv_data[$row[csf("inv_id")]]["aging_days"]=((strtotime(date('d-m-Y'))-strtotime($row[csf("ex_factory_date")]))/86400);
			$inv_data[$row[csf("inv_id")]]["pending_qnty"]=$pending_qnty;
			$inv_data[$row[csf("inv_id")]]["pending_value"]=$pending_value;
		}
	}
	unset($inv_buyer_result);
	//echo "<pre>";print_r($inv_data);die;
	
	//$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	//$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	?>
    
    <script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
    
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container" align="center" style="width:1180px">
	<fieldset style="width:1180px;">
        <table class="rpt_table" border="1" rules="all" width="1180" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="120">Buyer</th>
                <th width="120">Invoice No</th>
                <th width="70">Invoice Date</th>
                <th width="90">Invoice Qty.</th>
                <th width="90">Invoice Value</th>
                <th width="50">LC/SC</th>
                <th width="80">Pay Terms</th>
                <th width="120">Lc/SC No</th>
                <th width="90">Lc/SC Value</th>
                <th width="70">Ex-factory Date</th>
                <th width="70">Maturity Date</th>
                <th width="70">Ageing Days</th>
                <th>Remarks</th>
            </thead>
            <tbody>
            	<tr bgcolor="#66FFCC"><td colspan="14" style="font-weight:bold; font-size:14px;">Un Submitted Invoice :</td></tr>
                <? 
				$tot_pending_qnty=$tot_pending_value=0;
                $i=1; $r=1;
                foreach($inv_data as $inv_id=>$val)  
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center"><? echo $r; ?></td>
                        <td><p><? echo $buyer_arr[$val['buyer_name']]; ?>&nbsp;</p></td>
                        <td><p><? echo $val['invoice_no']; ?>&nbsp;</p></td>
                        <td align="center"><p><? echo change_date_format($val['invoice_date']); ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($val['pending_qnty'],2); ?></td>
                        <td align="right"><? echo number_format($val['pending_value'],2); ?></td>
                        <td align="center"><p><? if($val['is_lc']==1) echo "LC"; else echo "SC";?></p></td>
                        <td align="center"><p><? echo $pay_term[$val['pay_term']]; ?>&nbsp;</p></td>
                        <td><p><? echo $val['lc_sc_no']; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($val['lc_sc_value'],2); ?></td>
                        <td align="center"><p><? if($val['ex_factory_date'] !="" && $val['ex_factory_date'] !="0000-00-00") echo change_date_format($val['ex_factory_date']); ?>&nbsp;</p></td>
                        <td align="center" title="<? echo $val['maturity_date_cal'];?>" ><p><? if($val['maturity_date']!="" && $val['maturity_date']!="0000-00-00") echo change_date_format($val['maturity_date']); ?>&nbsp;</p></td>
                        <td align="center"><p><? echo $val['aging_days']; ?>&nbsp;</p></td>
                        <td><p><? echo $val['remarks']; ?>&nbsp;</p></td>
                    </tr>
                    <?
                    $tot_pending_qnty+=$val['pending_qnty'];
                    $tot_pending_value+=$val['pending_value'];
					$gt_pending_qnty+=$val['pending_qnty'];
                    $gt_pending_value+=$val['pending_value'];
                    $i++;$r++;
                }
                ?>
                <tr bgcolor="#FFFFCC">
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td align="right"><? echo number_format($tot_pending_qnty,2);?></td>
                    <td align="right"><? echo number_format($tot_pending_value,2);?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#66FFCC"><td colspan="14" style="font-weight:bold; font-size:14px;">Submission To Buyer / Shipping (Copy Docs) :</td></tr>
                <? 
               	$r=1;
				//echo "<pre>";print_r($inv_buyer_data);
                foreach($inv_buyer_data as $inv_id=>$val)  
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td><p><? echo $buyer_arr[$val['buyer_name']]; ?>&nbsp;</p></td>
                        <td><p><? echo $val['invoice_no']; ?>&nbsp;</p></td>
                        <td align="center"><p><? echo change_date_format($val['invoice_date']); ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($val['pending_qnty'],2); ?></td>

                        <td align="right"><? echo number_format($val['pending_value'],2); ?></td>
                        <td align="center"><p>SC</p></td>
                        <td align="center"><p><? echo $pay_term[$val['pay_term']]; ?>&nbsp;</p></td>
                        <td><p><? echo $val['lc_sc_no']; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($val['lc_sc_value'],2); ?></td>
                        <td align="center"><p><? if($val['ex_factory_date'] !="" && $val['ex_factory_date'] !="0000-00-00") echo change_date_format($val['ex_factory_date']); ?>&nbsp;</p></td>
                        <td align="center" ><p><? if($val['maturity_date']!="" && $val['maturity_date']!="0000-00-00") echo change_date_format($val['maturity_date']); ?>&nbsp;</p></td>
                        <td align="center"><p><? echo $val['aging_days']; ?>&nbsp;</p></td>
                        <td><p><? echo $val['remarks']; ?>&nbsp;</p></td>
                    </tr>
                    <?
                    $tot_pending_qnty+=$val['pending_qnty'];
                    $tot_pending_value+=$val['pending_value'];
					$gt_pending_qnty+=$val['pending_qnty'];
                    $gt_pending_value+=$val['pending_value'];
                    $i++;$r++;
                }
                ?>
                <tr bgcolor="#FFFFCC">
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td align="right"><? echo number_format($tot_pending_qnty,2);?></td>
                    <td align="right"><? echo number_format($tot_pending_value,2);?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td align="right"><? echo number_format($gt_pending_qnty,2);?></td>
                    <td align="right"><? echo number_format($gt_pending_value,2);?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        	<!--<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total:</td>
                    <td align="right" id="value_tot_pendin_value"><?// echo number_format($tot_net_due_usd,2); ?></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>-->
        </table>
    </fieldset>
    </div>
    <?
	
	if($com_id>0) $lc_sc_cond=" and a.beneficiary_name=$com_id";
	if($bank_id>0) $lc_sc_cond.=" and a.lien_bank=$bank_id";

	$sql_lc_sc="select a.id as lc_sc_id, a.tenor, 1 as type from com_export_lc a where a.status_active=1 and a.is_deleted=0 $lc_sc_cond
	union all
	select a.id as lc_sc_id, a.tenor, 2 as type from com_sales_contract a where a.status_active=1 and a.is_deleted=0 $lc_sc_cond";
	//echo $sql_lc_sc;die;
	$sql_lc_sc_result=sql_select($sql_lc_sc);
	$lc_sc_data=array();
	foreach($sql_lc_sc_result as $row)
	{
		$lc_sc_ids=$row[csf("lc_sc_id")]."__".$row[csf("type")];
		$lc_sc_data[$lc_sc_ids]=$row[csf("tenor")];
	}
	unset($sql_lc_sc_result);
	
	if($com_id>0) $beneficiary_cond=" and b.benificiary_id=$com_id";
	$proceed_rlz_sql="select a.is_lc, a.lc_sc_id, b.invoice_bill_id, c.id as dtls_id, c.account_head, c.document_currency as document_currency 
	from com_export_doc_submission_invo a, com_export_proceed_realization b, com_export_proceed_rlzn_dtls c 
	where a.doc_submission_mst_id=b.invoice_bill_id and b.id=c.mst_id and b.is_invoice_bill=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $beneficiary_cond";
	//echo $proceed_rlz_sql;die;
	$realize_data_arr=array();$lc_wise_rlz=array();
	$proceed_rlz_sql_result=sql_select($proceed_rlz_sql);
	foreach($proceed_rlz_sql_result as $row)
	{
		if($proceed_dtls_check[$row[csf("dtls_id")]]=="")
		{
			$proceed_dtls_check[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
			$realize_data_arr[$row[csf("invoice_bill_id")]]+=$row[csf("document_currency")];
		}
	}
	unset($proceed_rlz_sql_result);
	//echo "<pre>";print_r($realize_data_arr);

	$bill_com_cond="";
	if($com_id>0) $bill_com_cond=" and b.company_id=$com_id";
	//if($bank_id>0) $bill_bank_cond=" and b.lien_bank=$bank_id";
	if($month_id) $bill_bank_cond=" and to_char(b.possible_reali_date,'mm-yyyy')='$month_id'";
	$bill_trans="select b.id as bill_id, sum(a.lc_sc_curr) as bill_value 
	from com_export_doc_sub_trans a, com_export_doc_submission_mst b 
	where a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.lien_bank > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
	group by b.id";
	$bill_trans_result=sql_select($bill_trans);
	$bill_trans_data=array();
	foreach($bill_trans_result as $row)
	{
		$bill_trans_data[$row[csf("bill_id")]]+=$row[csf("bill_value")];
	}
	unset($bill_trans_result);
	
	if($db_type==0)
	{
		$bill_sql="select b.id as bill_id, b.buyer_id, b.bank_ref_no, b.submit_date, b.bank_ref_date, b.submit_type, a.is_lc, a.lc_sc_id, group_concat(c.id) as inv_ids, group_concat(c.invoice_no) as invoice_no, sum(c.net_invo_value) as inv_value, sum(c.invoice_quantity) as bill_qnty, sum(a.net_invo_value) as bill_value
		from com_export_invoice_ship_mst c, com_export_doc_submission_invo a, com_export_doc_submission_mst b 
		where c.id=a.invoice_id and a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.lien_bank > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
		group by b.id, b.buyer_id, b.bank_ref_no, b.submit_date, b.bank_ref_date, b.submit_type, a.is_lc, a.lc_sc_id";
	}
	else
	{
		$bill_sql="select b.id as bill_id, b.buyer_id, b.bank_ref_no, b.submit_date, b.bank_ref_date, b.submit_type, a.is_lc, a.lc_sc_id, listagg(cast(c.id as varchar(4000)),',') within group(order by c.id) as inv_ids, listagg(cast(c.invoice_no as varchar(4000)),',') within group(order by c.id) as invoice_no, sum(c.net_invo_value) as inv_value, sum(c.invoice_quantity) as bill_qnty, sum(a.net_invo_value) as bill_value
		from com_export_invoice_ship_mst c, com_export_doc_submission_invo a, com_export_doc_submission_mst b
		where c.id=a.invoice_id and a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.lien_bank > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
		group by b.id, b.buyer_id, b.bank_ref_no, b.submit_date, b.bank_ref_date, b.submit_type, a.is_lc, a.lc_sc_id";
	}
	//echo $bill_sql;
	$bill_sql_result=sql_select($bill_sql);
	//echo "<pre>";print_r($inv_data);die;
	//$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	//$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	?>
	<fieldset style="width:1180px;">
    <div style="font-size:14px; font-weight:bold;">Bills Receivable :</div>
        <table class="rpt_table" border="1" rules="all" width="1180" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="120">Buyer</th>
                <th width="120">Bank Ref. No</th>
                <th width="70">Submission Date</th>
                <th width="200">Invoice No</th>
                <th width="70">Bank Ref. Date</th>
                <th width="90">Bill Qty.</th>
                <th width="100">Bill Value</th>
                <th width="60">Tenor Days</th>
                <th width="70">Maturity Date</th>
                <th width="90">Bill Purchase Amount</th>
                <th width="70">Bill Purchase %</th>
                <th>Purchase Date</th>
            </thead>
            <tbody>
                <? 
                $i=1;
                foreach($bill_sql_result as $row)  
                {
					
					$bill_rate=$row[csf('inv_value')]/$row[csf('bill_qnty')];
					$pending_bill_value=$row[csf('bill_value')];
					$pending_bill_qnty=$pending_bill_value/$bill_rate;
					$ls_sc_ids=$row[csf('lc_sc_id')]."__".$row[csf('is_lc')];
					$maturity_date="";
					if($row[csf('bank_ref_date')] !="" && $row[csf('bank_ref_date')] !="0000-00-00")
					{
						$maturity_date=date('d-m-Y',(strtotime($row[csf("bank_ref_date")])+strtotime($lc_sc_data[$ls_sc_ids])));
					}
					$bill_value_trans=$bill_trans_data[$row[csf("bill_id")]];
					$purchase_percent=0;
					if($bill_value_trans!="")
					{
						$purchase_percent=(($bill_value_trans/$pending_bill_value)*100);
					}
					if($realize_data_arr[$row[csf("bill_id")]]=="")
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td align="center"><? echo $i; ?></td>
                            <td><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                            <td><p><? echo $row[csf('bank_ref_no')]; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo change_date_format($row[csf('submit_date')]); ?>&nbsp;</p></td>
                            <td><p><? echo implode(",",array_unique(explode(",",$row[csf('invoice_no')]))); ?>&nbsp;</p></td>
                            <td align="center"><p><? if($row[csf('bank_ref_date')] !="" && $row[csf('bank_ref_date')] !="0000-00-00") echo change_date_format($row[csf('bank_ref_date')]); ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($pending_bill_qnty,2); ?></td>
                            <td align="right"><? echo number_format($pending_bill_value,2); ?></td>
                            <td align="center"><p><? echo $lc_sc_data[$ls_sc_ids];?></p></td>
                            <td align="center"><p><? if($maturity_date !="") echo change_date_format($maturity_date); ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($bill_value_trans,2); ?></td>
                            <td align="right"><? echo number_format($purchase_percent,2); ?></td>
                            <td align="center"><p><? echo change_date_format($row[csf('submit_date')]); ?>&nbsp;</p></td>
                        </tr>
                        <?
                        $tot_pending_bill_qnty+=$pending_bill_qnty;
                        $tot_pending_bill_value+=$pending_bill_value;
                        $tot_bill_value_trans+=$bill_value_trans;
                        $i++;
					}
                }
                ?>
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td align="right"><? echo number_format($tot_pending_bill_qnty,2);?></td>
                    <td align="right"><? echo number_format($tot_pending_bill_value,2);?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_bill_value_trans,2);?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        	<!--<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total:</td>
                    <td align="right" id="value_tot_pendin_value"><?// echo number_format($tot_net_due_usd,2); ?></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>-->
        </table>
    </fieldset>
    </div>
    <?
}

if($action=="docs_forcast_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];

	$bank_id=$data_ref[1];
	$bill_com_cond="";
	if($com_id>0) $bill_com_cond=" and b.company_id=$com_id";
	if($bank_id>0) $bill_bank_cond=" and b.lien_bank=$bank_id";
	$bill_coll_sql="select a.invoice_id, sum(a.net_invo_value) as bill_value 
	from com_export_doc_submission_invo a, com_export_doc_submission_mst b 
	where a.doc_submission_mst_id=b.id and b.entry_form = 40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
	group by a.invoice_id";
	$bill_coll_sql_result=sql_select($bill_coll_sql);
	foreach($bill_coll_sql_result as $row)
	{
		$inv_sub_arr[$row[csf("invoice_id")]]=$row[csf("bill_value")];
	}
	unset($bill_coll_sql_result);
	if($com_id>0) $com_ship_cond=" and b.benificiary_id=$com_id";
	//if($bank_id>0) $bank_ship_cond=" and a.lien_bank=$bank_id";
	
	//echo "<pre>";print_r($inv_buyer_data);die;
	$inv_sql=" select a.lien_bank, a.tenor, b.id as inv_id, b.benificiary_id, b.location_id, b.net_invo_value, b.invoice_quantity, b.invoice_value, b.ex_factory_date, b.is_lc
	from com_export_lc a, com_export_invoice_ship_mst b 
	where a.id=b.lc_sc_id and b.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.lien_bank>0 and b.location_id>0 $com_ship_cond $bank_ship_cond
	union all
	select a.lien_bank, a.tenor, b.id as inv_id, b.benificiary_id, b.location_id, b.net_invo_value, b.invoice_quantity, b.invoice_value, b.ex_factory_date, b.is_lc 
	from com_sales_contract a, com_export_invoice_ship_mst b 
	where a.id=b.lc_sc_id and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.lien_bank>0 and b.location_id>0 $com_ship_cond $bank_ship_cond";
	//echo $inv_sql;die;
	$inv_sql_result=sql_select($inv_sql);
	$forcast_data=$bank_com_location_data=$maturity_fortnightly_arr=array();
	foreach($inv_sql_result as $row)
	{
		$inv_rate=$row[csf("net_invo_value")]/$row[csf("invoice_quantity")];
		$pending_value=$row[csf("net_invo_value")]-$inv_sub_arr[$row[csf("inv_id")]];
		if($row[csf("ex_factory_date")]!="" && $row[csf("ex_factory_date")]!="0000-00-00")
		{
			if($pending_value>0)
			{
				$pending_qnty=$pending_value/$inv_rate;
				$bank_com_location_data[$row[csf("lien_bank")]][$row[csf("benificiary_id")]][$row[csf("location_id")]]=$row[csf("lien_bank")]."*".$row[csf("benificiary_id")]."*".$row[csf("location_id")];
				if($row[csf("is_lc")]==1)
				{
					$tenor_priod=0;
					if($row[csf("tenor")]!="" && $row[csf("tenor")]>0) $tenor_priod=86400*$row[csf("tenor")];
					$maturity_date=date('d-m-Y',strtotime($row[csf("ex_factory_date")])+$tenor_priod+(86400*10));
				}
				else
				{
					$maturity_date=date('d-m-Y',(strtotime($row[csf("ex_factory_date")])+(86400*45)));
				}
				$maturi_fortnight=get_day_forthnightly($maturity_date);
				$maturity_fortnightly_arr[$maturi_fortnight]=$maturi_fortnight;
				$forcast_data[$row[csf("lien_bank")]][$row[csf("benificiary_id")]][$row[csf("location_id")]][$maturi_fortnight]+=$pending_qnty;
			}
		}
		
		/*if($pending_value>0 && $buyer_submit_inv[$row[csf("inv_id")]] =="")
		{
			
			$inv_data[$row[csf("inv_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$inv_data[$row[csf("inv_id")]]["lien_bank"]=$row[csf("lien_bank")];
			$inv_data[$row[csf("inv_id")]]["lc_sc_no"]=$row[csf("lc_sc_no")];
			$inv_data[$row[csf("inv_id")]]["pay_term"]=$row[csf("pay_term")];
			$inv_data[$row[csf("inv_id")]]["lc_sc_value"]=$row[csf("lc_sc_value")];
			$inv_data[$row[csf("inv_id")]]["benificiary_id"]=$row[csf("benificiary_id")];
			$inv_data[$row[csf("inv_id")]]["invoice_no"]=$row[csf("invoice_no")];
			$inv_data[$row[csf("inv_id")]]["invoice_date"]=$row[csf("invoice_date")];
			$inv_data[$row[csf("inv_id")]]["invoice_no"]=$row[csf("invoice_no")];
			$inv_data[$row[csf("inv_id")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
			$inv_data[$row[csf("inv_id")]]["is_lc"]=$row[csf("is_lc")];
			$inv_data[$row[csf("inv_id")]]["maturity_date_cal"]=$row[csf("ex_factory_date")]."__".$row[csf("tenor")]."__".$row[csf("is_lc")];
			if($row[csf("ex_factory_date")]!="" && $row[csf("ex_factory_date")]!="0000-00-00")
			{
				if($row[csf("is_lc")]==1)
				{
					$tenor_priod=0;
					if($row[csf("tenor")]!="" && $row[csf("tenor")]>0) $tenor_priod=86400*$row[csf("tenor")];
					$maturity_date=strtotime($row[csf("ex_factory_date")])+$tenor_priod+(86400*10);
					$inv_data[$row[csf("inv_id")]]["maturity_date"]=date('d-m-Y',$maturity_date);
				}
				else
				{
					$inv_data[$row[csf("inv_id")]]["maturity_date"]=date('d-m-Y',(strtotime($row[csf("ex_factory_date")])+(86400*45)));
				}
			}
			else
			{
				$inv_data[$row[csf("inv_id")]]["maturity_date"]="";
			}
			
			$inv_data[$row[csf("inv_id")]]["aging_days"]=((strtotime(date('d-m-Y'))-strtotime($row[csf("ex_factory_date")]))/86400);
			$inv_data[$row[csf("inv_id")]]["pending_qnty"]=$pending_qnty;
			$inv_data[$row[csf("inv_id")]]["pending_value"]=$pending_value;
		}*/
	}
	unset($inv_buyer_result);
	$bank_colspan=$com_colspan=array();
	$tot_col=0;
	foreach($bank_com_location_data as $bank_id=>$bank_val)
	{
		foreach($bank_val as $com_id=>$com_data)
		{
			foreach($com_data as $location_id=>$location_data)
			{
				$bank_colspan[$bank_id]++;
				$com_colspan[$bank_id][$com_id]++;
				$tot_col++;
			}
		}
	}
	//ksort($maturity_fortnightly_arr);
	//echo "<pre>";print_r($maturity_fortnightly_arr);die;
	//echo "<pre>";print_r($forcast_data);die;
	//echo "<pre>";print_r($bank_colspan);
	//echo "<pre>";print_r($com_colspan);
	//echo $tot_col;
	//die;
	
	//$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	$location_arr = return_library_array("select id, location_name from lib_location","id","location_name");
	$tbl_width=250+(100*$tot_col);
	$div_width=$tbl_width+20;
	?>
    
    <script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
    
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container" align="center" style="width:<? echo $div_width;?>px">
	<fieldset style="width:<? echo $div_width;?>px;">
        <table class="rpt_table" border="1" rules="all" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
            <thead>
            	<tr>
                    <th width="150" rowspan="2">Bill To Be Receive</th>
                    <?
                    foreach($bank_com_location_data as $bank_id=>$bank_val)
                    {
                        ?>
                        <th colspan="<? echo $bank_colspan[$bank_id];?>"><? echo $bank_arr[$bank_id];?></th>
                        <?
                    }
                    ?>
                    <th width="100" rowspan="3">Grand Total</th>
                </tr>
                <tr>
                    <?
                    foreach($bank_com_location_data as $bank_id=>$bank_val)
                    {
						foreach($bank_val as $com_id=>$com_data)
						{
							?>
							<th colspan="<? echo $com_colspan[$bank_id][$com_id];?>"><? echo $company_arr[$com_id];?></th>
							<?
						}
                    }
                    ?>
                </tr>
                <tr>
                	<th>Name Of Month</th>
                    <?
                    foreach($bank_com_location_data as $bank_id=>$bank_val)
                    {
						foreach($bank_val as $com_id=>$com_data)
						{
							foreach($com_data as $location_id=>$location_data)
							{
								?>
                                <th width="100" title="<? echo $location_id;?>"><? echo $location_arr[$location_id];?></th>
                                <?
							}
							
						}
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <? 
                $i=1; $r=1;
                foreach($maturity_fortnightly_arr as $fort_id=>$fort_val)  
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center"><? echo $fort_val; ?></td>
                        <?
						foreach($bank_com_location_data as $bank_id=>$bank_val)
						{
							foreach($bank_val as $com_id=>$com_data)
							{
								foreach($com_data as $location_id=>$location_data)
								{
									?>
									<td align="right"><? echo number_format($forcast_data[$bank_id][$com_id][$location_id][$fort_val],2);?></td>
									<?
									$tot_val+=$forcast_data[$bank_id][$com_id][$location_id][$fort_val];
									$gt_val[$bank_id][$com_id][$location_id]+=$forcast_data[$bank_id][$com_id][$location_id][$fort_val];
								}
							}
						}
						?>
                        <td align="right"><p><? echo number_format($tot_val,2); $gt_tot_val+=$tot_val;  $tot_val=0; ?>&nbsp;</p></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
            </tbody>
        	<tfoot>
                <tr class="tbl_bottom">
                	<th>Total:</th>
                    <?
                    foreach($bank_com_location_data as $bank_id=>$bank_val)
                    {
						foreach($bank_val as $com_id=>$com_data)
						{
							foreach($com_data as $location_id=>$location_data)
							{
								?>
                                <th align="right"><? echo number_format($gt_val[$bank_id][$com_id][$location_id],2);?></th>
                                <?
							}
							
						}
                    }
                    ?>
                    <th align="right"><? echo number_format($gt_tot_val,2);?></th>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
}



if($action=="bill_receiveable_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	if($com_id>0) $lc_sc_cond=" and a.beneficiary_name=$com_id";
	if($bank_id>0) $lc_sc_cond.=" and a.lien_bank=$bank_id";
	$sql_lc_sc="select a.id as lc_sc_id, a.tenor, 1 as type from com_export_lc a where a.status_active=1 and a.is_deleted=0 $lc_sc_cond
	union all
	select a.id as lc_sc_id, a.tenor, 2 as type from com_sales_contract a where a.status_active=1 and a.is_deleted=0 $lc_sc_cond";
	//echo $sql_lc_sc;die;
	$sql_lc_sc_result=sql_select($sql_lc_sc);
	$lc_sc_data=array();
	foreach($sql_lc_sc_result as $row)
	{
		$lc_sc_ids=$row[csf("lc_sc_id")]."__".$row[csf("type")];
		$lc_sc_data[$lc_sc_ids]=$row[csf("tenor")];
	}
	unset($sql_lc_sc_result);
	
	if($com_id>0) $beneficiary_cond=" and b.benificiary_id=$com_id";
	$proceed_rlz_sql="select a.is_lc, a.lc_sc_id, b.invoice_bill_id, c.id as dtls_id, c.account_head, c.document_currency as document_currency 
	from com_export_doc_submission_invo a, com_export_proceed_realization b, com_export_proceed_rlzn_dtls c 
	where a.doc_submission_mst_id=b.invoice_bill_id and b.id=c.mst_id and b.is_invoice_bill=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $beneficiary_cond";
	//echo $proceed_rlz_sql;die;
	$realize_data_arr=array();$lc_wise_rlz=array();
	$proceed_rlz_sql_result=sql_select($proceed_rlz_sql);
	foreach($proceed_rlz_sql_result as $row)
	{
		if($proceed_dtls_check[$row[csf("dtls_id")]]=="")
		{
			$proceed_dtls_check[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
			$realize_data_arr[$row[csf("invoice_bill_id")]]+=$row[csf("document_currency")];
			/*if($row[csf("account_head")]==20)
			{
				$lc_sc_id=$row[csf("lc_sc_id")]."__".$row[csf("is_lc")];
				$lc_wise_rlz[$lc_sc_id]+=$row[csf("document_currency")];
			}*/
		}
	}
	unset($proceed_rlz_sql_result);
	$bill_com_cond="";
	if($com_id>0) $bill_com_cond=" and b.company_id=$com_id";
	if($bank_id>0) $bill_bank_cond=" and b.lien_bank=$bank_id";
	$bill_trans="select b.id as bill_id, sum(a.lc_sc_curr) as bill_value 
	from com_export_doc_sub_trans a, com_export_doc_submission_mst b 
	where a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.lien_bank > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
	group by b.id";
	$bill_trans_result=sql_select($bill_trans);
	$bill_trans_data=array();
	foreach($bill_trans_result as $row)
	{
		$bill_trans_data[$row[csf("bill_id")]]+=$row[csf("bill_value")];
	}
	unset($bill_trans_result);
	
	if($db_type==0)
	{
		$bill_sql="select b.id as bill_id, b.buyer_id, b.bank_ref_no, b.submit_date, b.bank_ref_date, b.submit_type, a.is_lc, a.lc_sc_id, group_concat(c.id) as inv_ids, group_concat(c.invoice_no) as invoice_no, sum(c.net_invo_value) as inv_value, sum(c.invoice_quantity) as bill_qnty, sum(a.net_invo_value) as bill_value
		from com_export_invoice_ship_mst c, com_export_doc_submission_invo a, com_export_doc_submission_mst b 
		where c.id=a.invoice_id and a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.lien_bank > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
		group by b.id, b.buyer_id, b.bank_ref_no, b.submit_date, b.bank_ref_date, b.submit_type, a.is_lc, a.lc_sc_id";
	}
	else
	{
		$bill_sql="select b.id as bill_id, b.buyer_id, b.bank_ref_no, b.submit_date, b.bank_ref_date, b.submit_type, a.is_lc, a.lc_sc_id, listagg(cast(c.id as varchar(4000)),',') within group(order by c.id) as inv_ids, listagg(cast(c.invoice_no as varchar(4000)),',') within group(order by c.id) as invoice_no, sum(c.net_invo_value) as inv_value, sum(c.invoice_quantity) as bill_qnty, sum(a.net_invo_value) as bill_value
		from com_export_invoice_ship_mst c, com_export_doc_submission_invo a, com_export_doc_submission_mst b
		where c.id=a.invoice_id and a.doc_submission_mst_id=b.id and b.entry_form = 40 and b.lien_bank > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bill_com_cond $bill_bank_cond 
		group by b.id, b.buyer_id, b.bank_ref_no, b.submit_date, b.bank_ref_date, b.submit_type, a.is_lc, a.lc_sc_id";
	}
	//echo $bill_sql;
	$bill_sql_result=sql_select($bill_sql);
	//echo "<pre>";print_r($inv_data);die;
	//$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	//$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	?>
    
    <script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
    
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container" align="center" style="width:1180px">
	<fieldset style="width:1180px;">
        <table class="rpt_table" border="1" rules="all" width="1180" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="120">Buyer</th>
                <th width="120">Bank Ref. No</th>
                <th width="70">Submission Date</th>
                <th width="200">Invoice No</th>
                <th width="70">Bank Ref. Date</th>
                <th width="90">Bill Qty.</th>
                <th width="100">Bill Value</th>
                <th width="60">Tenor Days</th>
                <th width="70">Maturity Date</th>
                <th width="90">Bill Purchase Amount</th>
                <th width="70">Bill Purchase %</th>
                <th>Purchase Date</th>
            </thead>
            <tbody>
                <? 
                $i=1;
                foreach($bill_sql_result as $row)  
                {
					
					$bill_rate=$row[csf('inv_value')]/$row[csf('bill_qnty')];
					$pending_bill_value=$row[csf('bill_value')];
					$pending_bill_qnty=$pending_bill_value/$bill_rate;
					$ls_sc_ids=$row[csf('lc_sc_id')]."__".$row[csf('is_lc')];
					$maturity_date="";
					if($row[csf('bank_ref_date')] !="" && $row[csf('bank_ref_date')] !="0000-00-00")
					{
						$maturity_date=date('d-m-Y',(strtotime($row[csf("bank_ref_date")])+strtotime($lc_sc_data[$ls_sc_ids])));
					}
					$bill_value_trans=$bill_trans_data[$row[csf("bill_id")]];
					$purchase_percent=0;
					if($bill_value_trans!="")
					{
						$purchase_percent=(($bill_value_trans/$pending_bill_value)*100);
					}
					if($realize_data_arr[$row[csf("bill_id")]]=="" && $row[csf('is_lc')]==1)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td align="center"><? echo $i; ?></td>
                            <td><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                            <td><p><? echo $row[csf('bank_ref_no')]; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo change_date_format($row[csf('submit_date')]); ?>&nbsp;</p></td>
                            <td><p><? echo implode(",",array_unique(explode(",",$row[csf('invoice_no')]))); ?>&nbsp;</p></td>
                            <td align="center"><p><? if($row[csf('bank_ref_date')] !="" && $row[csf('bank_ref_date')] !="0000-00-00") echo change_date_format($row[csf('bank_ref_date')]); ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($pending_bill_qnty,2); ?></td>
                            <td align="right"><? echo number_format($pending_bill_value,2); ?></td>
                            <td align="center"><p><? echo $lc_sc_data[$ls_sc_ids];?></p></td>
                            <td align="center"><p><? if($maturity_date !="") echo change_date_format($maturity_date); ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($bill_value_trans,2); ?></td>
                            <td align="right"><? echo number_format($purchase_percent,2); ?></td>
                            <td align="center"><p><? echo change_date_format($row[csf('submit_date')]); ?>&nbsp;</p></td>
                        </tr>
                        <?
                        $tot_pending_bill_qnty+=$pending_bill_qnty;
                        $tot_pending_bill_value+=$pending_bill_value;
                        $tot_bill_value_trans+=$bill_value_trans;
                        $i++;
					}
                }
                ?>
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td align="right"><? echo number_format($tot_pending_bill_qnty,2);?></td>
                    <td align="right"><? echo number_format($tot_pending_bill_value,2);?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_bill_value_trans,2);?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        	<!--<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total:</td>
                    <td align="right" id="value_tot_pendin_value"><?// echo number_format($tot_net_due_usd,2); ?></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>-->
        </table>
    </fieldset>
    </div>
    <?
}

if($action=="bank_order_in_hand_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$month_id=$data_ref[1];
	//echo $com_id."=".$month_id;die;
	//echo $type.test;die;
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	//$team_lead_arr = return_library_array("select id, team_leader_name from lib_marketing_team where status_active=1 and is_deleted=0","id","team_leader_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
	//$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	//$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
	$ex_fact_data=return_library_array("select b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
                
	$str_cond="";
	if($com_id>0) $str_cond.=" and a.company_name=$com_id";
	if($bank_id>0) $str_cond.=" and b.TAG_BANK=$bank_id";
	$order_sql="SELECT a.buyer_name, a.dealing_marchant, c.id as po_id, (c.po_quantity*a.total_set_qnty) as order_quantity, (c.po_total_price) as order_total, c.unit_price
	from wo_po_details_master a, wo_po_break_down c, lib_buyer_tag_bank b
	where a.job_no=c.job_no_mst and b.buyer_id=a.buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and to_char(c.pub_shipment_date,'mm-yyyy')='$month_id' $str_cond";
	//echo $order_sql;die;
	$order_result=sql_select($order_sql);
	$dtls_data=array();
	foreach($order_result as $row)
	{
		$pending_qnty=$row[csf("order_quantity")]-$ex_fact_data[$row[csf("po_id")]];
		$rate=$row[csf("order_total")]/$row[csf("order_quantity")];
		$pending_value=$pending_qnty*$rate;
		$dtls_data[$row[csf("buyer_name")]][$row[csf("dealing_marchant")]]["order_quantity"]+=$pending_qnty;
		$dtls_data[$row[csf("buyer_name")]][$row[csf("dealing_marchant")]]["order_total"]+=$pending_value;
	}
	//echo "<pre>";print_r($dtls_data);die;
	
	?>
    <script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
    
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="report_container" align="center" style="width:720px">
	<fieldset style="width:720px;">
        <table class="rpt_table" border="1" rules="all" width="720" cellpadding="0" cellspacing="0">
            <thead>
            	<tr>
                	<th width="40" rowspan="2">SL</th>
                    <th width="150" rowspan="2">Buyer Name</th>
                    <th width="150" rowspan="2">Dealing Marchent</th>
                    <th colspan="3">Order In Hand</th>
                </tr>
                <tr>
                	<th width="120">Quantity</th>
                    <th width="120">Value</th>
                    <th >Avg.  FOB</th>
                </tr>
            </thead>
            <tbody>
            <? 
			$i=1; $r=1;
			//echo "<pre>";print_r($dtls_data);die;
			$tot_pendin_value=0;
			foreach($dtls_data as $buyer_id=>$buyer_data)  
			{
				foreach($buyer_data as $marchand_id=>$row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$avg_fob = $row[("order_total")]/$row[("order_quantity")];
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td title="<? echo $buyer_id; ?>"><p><? echo $buyer_arr[$buyer_id]; ?>&nbsp;</p></td>
                        <td title="<? echo $marchand_id; ?>"><p><? echo $dealing_mer_arr[$marchand_id]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[("order_quantity")],2);  ?></td>
                        <td align="right"><? echo number_format($row[("order_total")],2);  ?></td>
                        <td align="right"><? echo number_format($avg_fob,2);?></td>
                    </tr>
                    <?
                    $tot_order_quantity+=$row[("order_quantity")];
                    $tot_order_total+=$row[('order_total')];
                    $i++;
				}
			}
			?>
            </tbody>
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="3" align="right">Total:</td>
                    <td align="right"><? echo number_format($tot_order_quantity,2); ?></td>
                    <td align="right"><? echo number_format($tot_order_total,2); ?></td>
                    <td><? $tot_avg_fob=$tot_order_total/$tot_order_quantity; echo number_format($tot_avg_fob,2); ?></td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
}


if($action=="order_in_hand_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	//$company_id=str_replace("'","",$company_id);
	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$team_lead_arr = return_library_array("select id, team_leader_name from lib_marketing_team where status_active=1 and is_deleted=0","id","team_leader_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
	?>
	<script>
	function print_window()
	{
		$('#table_body tbody tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		$('#table_body tbody tr:first').show();
		d.close();
	}

	</script>	
    <? 
	ob_start(); 
	$html='<div id="report_container" align="center" style="width:2130px">
	<fieldset style="width:2130px;">
        <table class="rpt_table" border="1" rules="all" width="2130" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="110">Company Name</th>
                <th width="110">Working Company</th>
                <th width="100">Buyer Name</th>
                <th width="100">Team Leader</th>
                <th width="100"> Dealing Merchant</th>
                <th width="70">Job No</th>
                <th width="110">Style No</th>
                <th width="110">PO No</th>
                <th width="100">SC/LC</th>
                <th width="100">Bank Name</th>
                <th width="70">Pub. Ship Date</th>
				<th width="60">UOM</th>
                <th width="130">PO Qty.</th>
                <th width="70">Unit Price</th>
                <th width="140">PO Value ($)</th>
                <th width="130">Ship Qty.</th>
                <th width="140">Ship Value ($)</th>
                <th width="130">In Hand Qty.</th>
                <th width="140">In Hand Value ($)</th>
                <th>Ship Status</th>
            </thead>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2130" cellpadding="0" cellspacing="0" id="table_body">
            <tbody>';
		?>
	<div id="report_container" align="center" style="width:2130px">
	<fieldset style="width:2130px;">
        <table class="rpt_table" border="1" rules="all" width="2130" cellpadding="0" cellspacing="0">
            <thead>
                <th width="30">SL</th>
                <th width="110">Company Name</th>
                <th width="110">Working Company</th>
                <th width="100">Buyer Name</th>
                <th width="100">Team Leader</th>
                <th width="100"> Dealing Merchant</th>
                <th width="70">Job No</th>
                <th width="110">Style No</th>
                <th width="110">PO No</th>
                <th width="100">SC/LC</th>
                <th width="100">Bank Name</th>
                <th width="70">Pub. Ship Date</th>
				<th width="60">UOM</th>
                <th width="130">PO Qty.</th>
                <th width="70">Unit Price</th>
                <th width="140">PO Value ($)</th>
                <th width="130">Ship Qty.</th>
                <th width="140">Ship Value ($)</th>
                <th width="130">In Hand Qty.</th>
                <th width="140">In Hand Value ($)</th>
                <th>Ship Status</th>
            </thead>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2130" cellpadding="0" cellspacing="0" id="table_body">
            <tbody>
            <? 
                $i=1; 
                $InHandValue=0;
                $shipValue=0;
                $povalue=0;
                $exfact_com_cond="";
                if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
				if($com_id>0) $lc_sc_com_cond=" and a.beneficiary_name=$com_id";
				if($bank_id>0) $lc_sc_com_cond.=" and a.lien_bank=$bank_id";
				
				
				$lc_sc_sql="select b.wo_po_break_down_id, a.contract_no, 1 as type, a.lien_bank from com_sales_contract a, com_sales_contract_order_info b where a.id=b.com_sales_contract_id and a.status_active=1 and b.status_active=1 $lc_sc_com_cond
				union all
				select b.wo_po_break_down_id, a.export_lc_no as contract_no, 2 as type, a.lien_bank from com_export_lc a, com_export_lc_order_info b where a.id=b.com_export_lc_id and a.status_active=1 and b.status_active=1 $lc_sc_com_cond";
				//echo $lc_sc_sql;die;
				$lc_sc_result=sql_select($lc_sc_sql);
				$po_lc_sc_no=array();
				$lien_bank_arr=array();
				foreach($lc_sc_result as $row)  
                {
					if($duplicate_check[$row[csf("wo_po_break_down_id")]][$row[csf("contract_no")]]=="")
					{
						$duplicate_check[$row[csf("wo_po_break_down_id")]][$row[csf("contract_no")]]=$row[csf("contract_no")];
						if($po_lc_sc_no[$row[csf("wo_po_break_down_id")]]=="") $po_lc_sc_no[$row[csf("wo_po_break_down_id")]]=$row[csf("contract_no")].","; else $po_lc_sc_no[$row[csf("wo_po_break_down_id")]].=$row[csf("contract_no")];
					}

					if ($row[csf("type")]==2) 
					{
						$lien_bank_arr[$row[csf("wo_po_break_down_id")]]=$row[csf("lien_bank")];
					}
					else
					{
						$lien_bank_arr[$row[csf("wo_po_break_down_id")]]=$row[csf("lien_bank")];
					}

				}
				/*echo "<pre>";
				print_r($lien_bank_arr);
				echo "</pre>";*/
               /* $exfact_com_cond="";
				if($cbo_company_name>0) $exfact_com_cond=" and a.company_id=$cbo_company_name";
				$ex_fact_data=return_library_array("select b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");*/
                $ex_fact_data=return_library_array("select b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
                //echo "<pre>";print_r($ex_fact_data);die;
                
				/*$order_sql="select a.bank_id, b.company_name, (c.po_quantity*b.total_set_qnty) as order_quantity, c.po_total_price as order_total 
				from lib_buyer a, wo_po_details_master b, wo_po_break_down c 
				where a.id=b.buyer_name and b.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and a.bank_id>0 $com_ord_cond $bank_ord_cond";
				foreach($order_sql_result as $row)
				{
					$ord_rate=0;
					if($row[csf("order_total")]>0 && $row[csf("order_quantity")] >0)
					{
						$ord_rate=($row[csf("order_total")]/$row[csf("order_quantity")])*1;
					}
					$pendin_qnty=($row[csf("order_quantity")]-$ex_fact_data[$row[csf("po_break_down_id")]])*1;
					$pending_ord_value=($pendin_qnty*$ord_rate);
					$pending_ord_data_arr[$row[csf("company_name")]][$row[csf("bank_id")]]["pendin_qnty"]+=$pendin_qnty;
					$pending_ord_data_arr[$row[csf("company_name")]][$row[csf("bank_id")]]["pending_ord_value"]+=$pending_ord_value;
					
					$pending_ord_data_bank_arr[$row[csf("bank_id")]]["pendin_qnty"]+=$pendin_qnty;
					$pending_ord_data_bank_arr[$row[csf("bank_id")]]["pending_ord_value"]+=$pending_ord_value;
					$tot_pending_ord_qnty+=$pendin_qnty;
					$tot_pending_ord_value+=$pending_ord_value;
					//$tot_pending_ord_qnty+=$pendin_qnty;
				}
				*/
				$com_cond="";
                if($com_id>0) $com_cond=" and a.company_name=$com_id";
				if($bank_id>0) $com_cond.=" and b.TAG_BANK=$bank_id";
                $order_sql="SELECT a.company_name, a.buyer_name, a.team_leader, a.dealing_marchant, a.style_ref_no, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, (c.po_quantity*a.total_set_qnty) as order_quantity, (c.po_total_price) as order_total, c.pub_shipment_date, c.unit_price, a.style_owner as working_company
                from wo_po_details_master a, lib_buyer_tag_bank b, wo_po_break_down c 
                where a.job_no=c.job_no_mst and b.buyer_id=a.buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and b.TAG_BANK>0 $com_cond order by po_break_down_id";
				//echo $order_sql;die;
				
                $order_sql_result=sql_select($order_sql);
                $bank_ids=array();
                foreach($order_sql_result as $row)  
                {
                    $pending_ord_qnty=0;
                    $Ship_value=0;
                    $po_quantity_value=0;
                    $pending_ord_value=0;
                    $ord_rate = 0;
                    if($row[csf("order_total")] > 0 && $row[csf("order_quantity")] > 0)
                    {
                        $ord_rate =$row[csf("order_total")]/$row[csf("order_quantity")];
                    }
                    $po_quantity_value =$row[csf("order_total")];
                    $po_quantity =$row[csf("order_quantity")];
                    $Ship_qnty =$ex_fact_data[$row[csf("po_break_down_id")]];
                    $Ship_value =$Ship_qnty*$ord_rate;
                    $pendin_qnty =$row[csf("order_quantity")]-$ex_fact_data[$row[csf("po_break_down_id")]];
                    $pending_ord_value =$pendin_qnty*$ord_rate;
					$test_pending_data[$row[csf("po_break_down_id")]]=$pending_ord_value;
					$in_hand_qnty=$row[csf("order_quantity")]-$Ship_qnty;
					if($lien_bank_arr[$row[csf('po_break_down_id')]] !=""){
						$bank_ids[$lien_bank_arr[$row[csf('po_break_down_id')]]] = $lien_bank_arr[$row[csf('po_break_down_id')]];
					}
					
					if($pendin_qnty>0)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($row[csf('shiping_status')]>0) $shiping_status=$row[csf('shiping_status')]; else $shiping_status=1;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td align="center" width="30"><? echo $i; ?></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="110"><p><? echo $company_arr[$row[csf('company_name')]]; ?>&nbsp;</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="110"><p><? echo $company_arr[$row[csf('working_company')]]; ?>&nbsp;</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="100"><p><? echo $team_lead_arr[$row[csf('team_leader')]]; ?>&nbsp;</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="100"><p><? echo $dealing_mer_arr[$row[csf('dealing_marchant')]]; ?>&nbsp;</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="70"><p><? echo $row[csf('job_no_mst')]; ?>&nbsp;</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="110"><p><? echo $row[csf('style_ref_no')];  ?>&nbsp;</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="110"><p><? echo $row[csf('po_number')];  ?>&nbsp;</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="100"><? echo chop($po_lc_sc_no[$row[csf('po_break_down_id')]],","); ?></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="100"><? echo $bank_arr[$lien_bank_arr[$row[csf('po_break_down_id')]]]; ?></td>
							<td width="70" align="center"><p><? if($row[csf('pub_shipment_date')] !="" && $row[csf('pub_shipment_date')] !="0000-00-00") echo date("d-M-Y", strtotime($row[csf('pub_shipment_date')])); ?></p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="60" align="center"><? echo "Pcs"; ?></td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130"><? echo number_format($po_quantity,0); ?></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right"><? echo number_format($row[csf('unit_price')],2);  ?></td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140"><? echo number_format($po_quantity_value,2); ?></td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130"><? echo number_format($Ship_qnty,0);  ?></td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140"><? echo number_format($Ship_value,2); ?></td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130"><? echo number_format($in_hand_qnty,0);  ?></td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140"><? echo number_format($pending_ord_value,2);  ?></td>
							<td style="word-wrap:break-word; word-break: break-all;" align="center" title="<? echo $shiping_status; ?>"><? echo  $shipment_status[$shiping_status]; ?></td>
						</tr>
						<?
						$html.='<tr bgcolor="'. $bgcolor.'" onClick="change_color(\'tr_'. $i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'">
							<td align="center" width="30" style="word-break:break-all">'.$i.'</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="110"><p>'.$company_arr[$row[csf('company_name')]].'&nbsp;</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="110"><p>'.$company_arr[$row[csf('working_company')]].'&nbsp;</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="100"><p>'.$buyer_arr[$row[csf('buyer_name')]].'&nbsp;</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="100"><p>'.$team_lead_arr[$row[csf('team_leader')]].'&nbsp;</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="100"><p>'.$dealing_mer_arr[$row[csf('dealing_marchant')]].'&nbsp;</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="70"><p>'.$row[csf('job_no_mst')].'&nbsp;</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="110"><p>'.$row[csf('style_ref_no')].'&nbsp;</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="110"><p>'.$row[csf('po_number')].'&nbsp;</p></td>
							<td width="100" style="word-wrap:break-word; word-break: break-all;">'.chop($po_lc_sc_no[$row[csf('po_break_down_id')]],",").'</td>
							<td width="100" style="word-wrap:break-word; word-break: break-all;">'.$bank_arr[$lien_bank_arr[$row[csf('po_break_down_id')]]].'</td>
							<td width="70" align="center"><p>'.date("d-M-Y", strtotime($row[csf('pub_shipment_date')])).'</p></td>
							<td style="word-wrap:break-word; word-break: break-all;" width="60" align="center">Pcs</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130">'.number_format($po_quantity,0).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" width="70" align="right">'.number_format($row[csf('unit_price')],2).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140">'.number_format($po_quantity_value,2).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130">'.number_format($Ship_qnty,0).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140">'.number_format($Ship_value,2).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="130">'.number_format($in_hand_qnty,0).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="right" width="140">'.number_format($pending_ord_value,2).'</td>
							<td style="word-wrap:break-word; word-break: break-all;" align="center" title="'.$shiping_status.'">'.$shipment_status[$shiping_status].'</td>
						</tr>';
						$i++;
						$tot_po_quantity+=$po_quantity;
						$povalue+=$po_quantity_value; 
						$tot_Ship_qnty+=$Ship_qnty;
						$shipValue+=$Ship_value;
						$total_in_hand_qnty+=$in_hand_qnty; 
						$InHandValue+=$pending_ord_value;
					}
                    
                }
				//echo "<pre>";print_r($test_data);die;
                ?>
            </tbody>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2130" cellpadding="0" cellspacing="0" id="report_table_footer">
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="13" align="right">Total : </td>
                    <td width="130" align="right" id="tot_po_quantity"><? echo number_format($tot_po_quantity,0); ?></td>
                    <td width="70">&nbsp;</td>
                    <td width="140" align="right" id="value_povalue"><? echo number_format($povalue,2); ?></td>
                    <td width="130" align="right" id="tot_Ship_qnty"><? echo number_format($tot_Ship_qnty,0); ?></td>
                    <td align="right" id="value_shipvalue" width="140"><? echo number_format($shipValue,2); ?></td>
                    <td width="130" align="right" id="total_in_hand_qnty"><? echo number_format($total_in_hand_qnty,0); ?></td>
                    <td align="right" id="value_inhandvalue" width="140"><? echo number_format($InHandValue,2); ?></td>
                    <td width="58"></td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
	$html.='</tbody>
        </table>
        <table class="rpt_table" border="1" rules="all" width="2130" cellpadding="0" cellspacing="0" id="report_table_footer">
        	<tfoot>
                <tr class="tbl_bottom">
                    <td colspan="13" align="right">&nbsp;&nbsp;Total : </td>
                    <td width="130" align="right" id="tot_po_quantity">'.number_format($tot_po_quantity,0).'</td>
					<td width="70">&nbsp;</td>
                    <td width="140" align="right" id="value_povalue" width="140">'.number_format($povalue,2).'</td>
                    <td width="130" align="right" id="tot_Ship_qnty">'.number_format($tot_Ship_qnty,0).'</td>
                    <td align="right" id="value_shipvalue" width="140">'.number_format($shipValue,2).'</td>
                    <td width="130" align="right" id="total_in_hand_qnty">'.number_format($total_in_hand_qnty,0).'</td>
                    <td align="right" id="value_inhandvalue" width="140">'.number_format($InHandValue,2).'</td>
                    <td width="58"></td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>';
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
		//if( @filemtime($filename) < (time()-$seconds_old) )
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";

	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	ob_end_clean();
	//echo "$total_data****$filename";
	$bank_ids = implode(",",$bank_ids);
	
	
	?>
    <p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	&nbsp; <a href="<? echo $filename; ?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp; 
	<a href="bank_liability_month_wise_controller.php?action=print_preview_2&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Merchant Wise" class="formbutton" style="width:110px"/></a>&nbsp;
	&nbsp; 
	<a href="bank_liability_month_wise_controller.php?action=print_preview_3&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Buyer Wise" class="formbutton" style="width:110px"/></a>&nbsp;
	&nbsp; 
	<a href="bank_liability_month_wise_controller.php?action=print_preview_4&company_id='<? echo $company_id; ?>'" style="text-decoration:none"><input type="button" value="Month Wise" class="formbutton" style="width:110px"/></a>
	&nbsp; 
	<a href="bank_liability_month_wise_controller.php?action=print_preview_5&company_id='<? echo $company_id; ?>'&bank_ids='<? echo $bank_ids;?>'" style="text-decoration:none"><input type="button" value="Monthly Bank Wise" class="formbutton" style="width:130px"/></a>&nbsp;
	</p>
    <?

	echo $html; 

	?>
    <script>
	var tableFilters = 
	{
		col_60: "none",
		col_operation: {
			id: ["tot_po_quantity","value_povalue","tot_Ship_qnty","value_shipvalue","total_in_hand_qnty","value_inhandvalue"],
			col: [13,15,16,17,18,19],
			operation: ["sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	setFilterGrid("table_body",-1,tableFilters);
    </script>
    <?
	exit();
}

if ($action=="print_preview_2") // Created by Tipu
{
	//echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
	ob_start();
    ?>
    <div id="report_container" align="center" style="width:1140px">
		<fieldset style="width:1140px;">
		    <table class="rpt_table" border="1" rules="all" width="1140" cellpadding="0" cellspacing="0">
		        <thead>
		            <th width="40">SL</th>
		            <th width="110">Company Name</th> 
		            <th width="100">Working Company</th> 
		            <th width="100">Buyer Name</th> 
		            <th width="100"> Dealing Merchant</th>
		            <th width="90">UOM</th>
		            <th width="130">PO Qty.</th>
		            <th width="130">In Hand Qty.</th>
		            <th width="140">In Hand Value ($)</th>
		            <th width="100">Shipment Date</th>
		            <th>Ship Status</th>
		        </thead>
		    </table>
		    <table class="rpt_table" border="1" rules="all" width="1140" cellpadding="0" cellspacing="0" id="table_body">
	            <tbody>
	            	<?
					$exfact_com_cond="";
    				if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
	            	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
                	//echo "<pre>";print_r($ex_fact_data);die;

					/*$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, sum(b.order_quantity) as po_quantity, sum(b.order_total) as order_total, c.pub_shipment_date
					FROM wo_po_details_master a, lib_buyer b, wo_po_break_down c 
					WHERE a.job_no=c.job_no_mst and b.id=a.buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.shiping_status<>3 and c.is_deleted=0 $com_cond 
					GROUP by a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, c.pub_shipment_date 
					ORDER by a.dealing_marchant asc";*/
					$com_cond="";
					if($com_id>0) $com_cond=" and a.company_name=$com_id";
					if($bank_id>0) $com_cond.=" and b.TAG_BANK=$bank_id";
					$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, sum(c.po_quantity*a.total_set_qnty) as po_quantity, sum(c.po_total_price) as order_total, c.pub_shipment_date
					FROM wo_po_details_master a, lib_buyer_tag_bank b, wo_po_break_down c 
					WHERE a.job_no=c.job_no_mst and b.buyer_id=a.buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and b.TAG_BANK>0 $com_cond 
					GROUP by a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id, c.job_no_mst, c.po_number, c.pub_shipment_date 
					ORDER by a.buyer_name asc";
					//echo $main_sql;die;
					
	                $main_sql_result=sql_select($main_sql);	 
	               
	                $i=1;
	                $merchant_wise_total_po_quantity=$merchant_wise_InHandValue=0;
                	foreach ($main_sql_result as $dealing_marchant_key => $row) 
                	{
                		$in_hand_value=0;
						$ord_rate = 0;

						if($row[csf("order_total")] > 0 && $row[csf("po_quantity")] > 0)
						{
							$ord_rate =$row[csf("order_total")]/$row[csf("po_quantity")];
						}
						$po_quantity =$row[csf("po_quantity")];
						$pendin_qnty =$row[csf("po_quantity")]-$ex_fact_data[$row[csf("po_break_down_id")]];
						$in_hand_value =$pendin_qnty*$ord_rate;
		                $Ship_qnty =$ex_fact_data[$row[csf("po_break_down_id")]];
						$in_hand_qnty=$row[csf("po_quantity")]-$Ship_qnty;
						if($row[csf('shiping_status')]>0) $shiping_status=$row[csf('shiping_status')]; else $shiping_status=1;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						if(!in_array($row[csf('dealing_marchant')], $chk))
	                   	{	                   		
	                   		if ($i!=1) 
	                   		{
			                    ?>
			                   	<tr>
			                        <td colspan="6" align="right"><strong>Merchant Total :</strong></td>
			                        <td width="130"><strong><? echo $merchant_wise_total_po_quantity; ?></strong></td>
			                        <td width="130"><strong><? echo $merchant_wise_total_in_hand_qnty; ?></strong></td>
			                        <td width="140"><strong><? echo $merchant_wise_InHandValue; ?></strong></td>
			                        <td width="100"></td>
			                        <td></td>
			                    </tr>
			                   <?			                   	                   
		               		}
		               		unset($merchant_wise_total_po_quantity);
		               		unset($merchant_wise_total_in_hand_qnty);
		               		unset($merchant_wise_InHandValue);
                   		}
			            $chk[]=$row[csf('dealing_marchant')];

						?>

	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td width="40"><? echo $i; ?></td>
	                        <td width="110"><? echo $company_arr[$row[csf('company_name')]]; ?></td>
	                        <td width="100"><? echo $company_arr[$row[csf('style_owner')]]; ?></td>
	                        <td width="100"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
	                        <td width="100"><? echo $dealing_mer_arr[$row[csf('dealing_marchant')]]; ?></td>
	                        <td width="90"><? echo "Pcs"; ?></td>
	                        <td width="130"><? echo $po_quantity; ?></td>
	                        <td width="130"><? echo $in_hand_qnty; ?></td>
	                        <td width="140"><? echo $in_hand_value; ?></td>
	                        <td width="100" align="center"><p><? if($row[csf('pub_shipment_date')] !="" && $row[csf('pub_shipment_date')] !="0000-00-00") echo date("d-M-Y", strtotime($row[csf('pub_shipment_date')])); ?></p></td>
	                        <td><? echo $shipment_status[$shiping_status]; ?></td>
	                    </tr>
	                    <?
	                    $i++;
	                    $merchant_wise_total_po_quantity+=$po_quantity;
	                    $merchant_wise_total_in_hand_qnty+=$in_hand_qnty;
	                    $merchant_wise_InHandValue+=$in_hand_value;
	                    $grand_total_po_quantity+=$po_quantity;
	                    $grand_total_in_hand_qnty+=$in_hand_qnty;
						$grand_total_InHandValue+=$in_hand_value;
                	}

	                    ?>
	                   	<tr>
	                        <td colspan="6" align="right"><strong>Merchant Total :</strong></td>
	                        <td width="130"><strong><? echo $merchant_wise_total_po_quantity;?></strong></td>
	                        <td width="130"><strong><? echo $merchant_wise_total_in_hand_qnty;?></strong></td>
	                        <td width="140"><strong><? echo $merchant_wise_InHandValue; ?></strong></td>
	                        <td width="100"></td>
	                        <td></td>
	                    </tr>
	            </tbody>
        	</table>

        	<table class="rpt_table" border="1" rules="all" width="1140" cellpadding="0" cellspacing="0" id="report_table_footer">
	        	<tfoot>
	                <tr>
	                    <td width="40"></td>
	                    <td width="110"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="90" align="right"><strong>Grand Total:</strong></td>
	                    <td width="130"><strong><? echo $grand_total_po_quantity;?></strong></td>
	                    <td width="130"><strong><? echo $grand_total_in_hand_qnty;?></strong></td>
	                    <td width="140"><strong><? echo $grand_total_InHandValue; ?></strong></td>
	                    <td width="100"></td>
	                    <td></td>
	                </tr>
	            </tfoot>
        	</table>
		</fieldset>
	</div>
    <?
    foreach (glob("$user_id*.xls") as $filename2) 
	{
		@unlink($filename2);
		//if( @filemtime($filename2) < (time()-$seconds_old) )
	}
	//---------end------------//
	$name=time();
	$filename2=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename2, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename2=$user_id."_".$name.".xls";
	//ob_end_clean();
	//echo "$total_data****$filename2";

	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($filename2).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename2));
    flush(); // Flush system output buffer
    readfile($filename2);
    exit;
}

if ($action=="print_preview_3") // Created by Tipu
{
	//echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");

	ob_start();
	
    ?>
    <div id="report_container" align="center" style="width:1240px">
		<fieldset style="width:1240px;">
		    <table class="rpt_table" border="1" rules="all" width="1240" cellpadding="0" cellspacing="0">
		        <thead>
		            <th width="40">SL</th>
		            <th width="100">Company Name</th> 
		            <th width="100">Working Company</th> 
		            <th width="100">Buyer Name</th> 
		            <th width="100"> Dealing Merchant</th>
		            <th width="130">PO Qty.</th>
		            <th width="130">In Hand Qty.</th>
		            <th width="140">In Hand Value ($)</th>
		            <th width="130">Total</th>
		            <th>Total Amount</th>
		        </thead>
		    </table>
		    <table class="rpt_table" border="1" rules="all" width="1240" cellpadding="0" cellspacing="0" id="table_body">
	            <tbody>
	            	<?
					/*$exfact_com_cond="";
    				if($com_id>0) $exfact_com_cond=" and a.company_name=$com_id";
	            	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
						
					$com_cond="";
					if($com_id>0) $com_cond=" and a.company_name=$com_id";
					if($bank_id>0) $com_cond.=" and b.bank_id=$bank_id";
					$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, sum(c.po_quantity*a.total_set_qnty) as po_quantity, sum(c.po_total_price) as order_total, c.pub_shipment_date
					FROM wo_po_details_master a, lib_buyer b, wo_po_break_down c 
					WHERE a.job_no=c.job_no_mst and b.id=a.buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and b.bank_id>0 $com_cond 
					GROUP by a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id, c.job_no_mst, c.po_number, c.pub_shipment_date 
					ORDER by a.buyer_name asc";*/
					$exfact_com_cond="";
    				if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
	            	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");                	
					$com_cond="";
					if($com_id>0) $com_cond=" and a.company_name=$com_id";
					if($bank_id>0) $com_cond.=" and b.TAG_BANK=$bank_id";
					$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, sum(c.po_quantity*a.total_set_qnty) as po_quantity, sum(c.po_total_price) as order_total, c.pub_shipment_date
					FROM wo_po_details_master a, lib_buyer_tag_bank b, wo_po_break_down c 
					WHERE a.job_no=c.job_no_mst and b.buyer_id=a.buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and b.TAG_BANK>0 $com_cond 
					GROUP by a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id, c.job_no_mst, c.po_number, c.pub_shipment_date 
					ORDER by a.buyer_name asc";
					 //echo $main_sql;die;
					
	                $main_sql_result=sql_select($main_sql);	 
	               	foreach ($main_sql_result as $key => $row) 
	               	{
	               		$in_hand_value=0;
						$ord_rate = 0;

						if($row[csf("order_total")] > 0 && $row[csf("po_quantity")] > 0)
						{
							$ord_rate =$row[csf("order_total")]/$row[csf("po_quantity")];
						}
						$po_quantity =$row[csf("po_quantity")];
						$pendin_qnty =$row[csf("po_quantity")]-$ex_fact_data[$row[csf("po_break_down_id")]];
						$in_hand_value =$pendin_qnty*$ord_rate;
		                $Ship_qnty =$ex_fact_data[$row[csf("po_break_down_id")]];
						$in_hand_qnty=$row[csf("po_quantity")]-$Ship_qnty;
						if($in_hand_value>0)
						{
							$buyer_rowspan[$row[csf('buyer_name')]]++;
							$in_hand_qnty_arr[$row[csf('buyer_name')]]+=$in_hand_qnty;
							$in_hand_value_arr[$row[csf('buyer_name')]]+=$in_hand_value;
						}
	               	}
	               	/*echo "<pre>";
	               	print_r($buyer_data_arr);die;*/
	                $i=1;

                	foreach ($main_sql_result as $dealing_marchant_key => $row) 
                	{
                		$in_hand_value=0;
						$ord_rate = 0;

						if($row[csf("order_total")] > 0 && $row[csf("po_quantity")] > 0)
						{
							$ord_rate =$row[csf("order_total")]/$row[csf("po_quantity")];
						}
						$po_quantity =$row[csf("po_quantity")];
						$pendin_qnty =$row[csf("po_quantity")]-$ex_fact_data[$row[csf("po_break_down_id")]];
						$in_hand_value =$pendin_qnty*$ord_rate;
		                $Ship_qnty =$ex_fact_data[$row[csf("po_break_down_id")]];
						$in_hand_qnty=$row[csf("po_quantity")]-$Ship_qnty;
						if($row[csf('shiping_status')]>0) $shiping_status=$row[csf('shiping_status')]; else $shiping_status=1;
						if($in_hand_value>0)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="100"><? echo $company_arr[$row[csf('company_name')]]; ?></td>
								<td width="100"><? echo $company_arr[$row[csf('style_owner')]]; ?></td>
								<td width="100"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
								<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $dealing_mer_arr[$row[csf('dealing_marchant')]]; ?></td>
								<td width="130"><? echo $po_quantity; ?></td>
								<td width="130"><? echo $in_hand_qnty; ?></td>
								<td width="140"><? echo $in_hand_value; ?></td>
	
								<? 
								if(!in_array($row[csf('buyer_name')], $chk))
								{
									?>
										<td width="130" rowspan="<? echo $buyer_rowspan[$row[csf('buyer_name')]]; ?>"><? echo $in_hand_qnty_arr[$row[csf('buyer_name')]]; $total_in_hand_qnty+=$in_hand_qnty_arr[$row[csf('buyer_name')]]; ?></td>
										<td rowspan="<? echo $buyer_rowspan[$row[csf('buyer_name')]]; ?>"><? echo $in_hand_value_arr[$row[csf('buyer_name')]]; $total_in_hand_value+=$in_hand_value_arr[$row[csf('buyer_name')]]; ?></td>
									<?
								}
								$chk[]=$row[csf('buyer_name')]; 
								?>
	
							</tr>
							<?
							$i++;
							$grand_total_po_quantity+=$po_quantity;
							$grand_total_in_hand_qnty+=$in_hand_qnty;
							$grand_total_InHandValue+=$in_hand_value;
						}
                	}

	                    ?>
	            </tbody>
        	</table>

        	<table class="rpt_table" border="1" rules="all" width="1240" cellpadding="0" cellspacing="0" id="report_table_footer">

	        	<tfoot>
	                <tr>
	                    <td width="40"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="100" align="right"><strong>Grand Total:</strong></td>
	                    <td width="130"><strong><? echo $grand_total_po_quantity;?></strong></td>
	                    <td width="130"><strong><? echo $grand_total_in_hand_qnty;?></strong></td>
	                    <td width="140"><strong><? echo $grand_total_InHandValue; ?></strong></td>
	                    <td width="130"><strong><? echo $total_in_hand_qnty; ?></strong></td>
	                    <td><strong><? echo $total_in_hand_value; ?></strong></td>
	                </tr>
	            </tfoot>
        	</table>
		</fieldset>
	</div>
    <?
    foreach (glob("$user_id*.xls") as $filename2) 
	{
		@unlink($filename2);
		//if( @filemtime($filename2) < (time()-$seconds_old) )
	}
	//---------end------------//
	$name=time();
	$filename2=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename2, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename2=$user_id."_".$name.".xls";
	//ob_end_clean();
	//echo "$total_data****$filename2";

	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($filename2).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename2));
    flush(); // Flush system output buffer
    readfile($filename2);
    exit;
}

if ($action=="print_preview_4") // Created by Tipu
{
	//echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");

	ob_start();

	/*$com_cond="";
    if($company_id>0) $com_cond=" and a.company_name=$company_id";
	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
	//echo "<pre>";print_r($ex_fact_data);die;
	
	

	$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, sum(b.order_quantity) as po_quantity, sum(b.order_total) as order_total, $month_year_cond
	FROM wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_break_down c 
	WHERE a.job_no=c.job_no_mst and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.shiping_status<>3 and c.is_deleted=0 $com_cond 
	GROUP by a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, c.pub_shipment_date 
	ORDER by c.pub_shipment_date asc";*/
	if ($db_type==0) $month_year_select="date_format(c.pub_shipment_date, '%M-%y') as month_year";
	else $month_year_select="to_char(c.pub_shipment_date, 'Month-YY') as month_year";
	$exfact_com_cond="";
	if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
	//echo "<pre>";print_r($ex_fact_data);die;
	$com_cond="";
	if($com_id>0) $com_cond=" and a.company_name=$com_id";
	if($bank_id>0) $com_cond.=" and b.TAG_BANK=$bank_id";
	$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, sum(c.po_quantity*a.total_set_qnty) as po_quantity, sum(c.po_total_price) as order_total, $month_year_select
	FROM wo_po_details_master a, lib_buyer_tag_bank b, wo_po_break_down c 
	WHERE a.job_no=c.job_no_mst and b.buyer_id=a.buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and b.TAG_BANK>0 $com_cond 
	GROUP by a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id, c.job_no_mst, c.po_number, c.pub_shipment_date 
	ORDER by a.buyer_name asc";
	
	//echo $main_sql;die;

	$main_sql_result=sql_select($main_sql);
	$main_arr=array();
	$month_year_qty_arr=array();
	foreach ($main_sql_result as $key => $row) 
   	{
   		$in_hand_value=0;
		$ord_rate = 0;

		if($row[csf("order_total")] > 0 && $row[csf("po_quantity")] > 0)
		{
			$ord_rate =$row[csf("order_total")]/$row[csf("po_quantity")];
		}
		$po_quantity =$row[csf("po_quantity")];
		$pendin_qnty =$row[csf("po_quantity")]-$ex_fact_data[$row[csf("po_break_down_id")]];
		$pendin_value =$pendin_qnty*$ord_rate;
        if($pendin_qnty>0)

		{
			$buyer_rowspan[$row[csf('buyer_name')]]++;
			$in_hand_qnty_arr[$row[csf('buyer_name')]]+=$pendin_qnty;
			$in_hand_value_arr[$row[csf('buyer_name')]]+=$pendin_value;
	
			$main_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]]=$row[csf('month_year')];
			$lc_com_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]]['company_name']=$row[csf('company_name')];
			$w_com_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]]['style_owner']=$row[csf('style_owner')];
	
			$month_year_qty_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]][$row[csf('month_year')]]['po_quantity']+=$row[csf("po_quantity")];
			$month_year_qty_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]][$row[csf('month_year')]]['inhand_qty']+=$pendin_qnty;
			$month_year_qty_arr[$row[csf('buyer_name')]][$row[csf('dealing_marchant')]][$row[csf('month_year')]]['in_hand_value']+=$pendin_value;
		}
   		
   		/*$month_year_qty_arr[$row[csf('month_year')]]['po_quantity']+=$row[csf("po_quantity")];
   		$month_year_qty_arr[$row[csf('month_year')]]['inhand_qty']+=$in_hand_qnty;
   		$month_year_qty_arr[$row[csf('month_year')]]['in_hand_value']+=$in_hand_value;*/



   		$month_year_arr[$row[csf('month_year')]]=$row[csf('month_year')];
   	}   	
	 	$divWith=count($month_year_arr);
	 	$table_width=1000+($divWith*300);
       	/*echo "<pre>";
       	print_r($month_year_arr);die;*/
       //echo '<pre>';print_r($main_arr);die;
    ?>
    <div id="report_container" align="center" style="width:1010px">
		<fieldset style="width:<? echo $table_width; ?>px;">
		    <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0">
		        <thead>
		        	<tr>
			            <th width="40" rowspan="2">SL</th>
			            <th width="100" rowspan="2">Company Name</th> 
			            <th width="100" rowspan="2">Working Company</th> 
			            <th width="100" rowspan="2">Buyer Name</th> 
			            <th width="100" rowspan="2">Merchandiser</th>
			            <?
	                    foreach($month_year_arr as $month_year => $row)
	                    {	                    	
                    		?>
	                    	<th width="300" colspan="3"><? echo $month_year; ?></th>
	                    	<?                    	
	                    }
	                    ?>
			            <th width="130" rowspan="2">Total Inhand Qty</th>
			            <th width="130" rowspan="2">Total Inhand Value</th>
		            </tr>
		            <tr> 
		            	<?
	                    foreach($month_year_arr as $month_year => $row)
	                    {	                    	
                    	?>          	
			            <th width="100">PO Qty</th>
			            <th width="100">In hand Qty</th>
			            <th width="100">In hand value</th>
			            <?                    	
	                    }
	                    ?>
		            </tr>
		        </thead>
		    </table>
		    <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	            <tbody>
	            	<?
	                $i=1;
	                $grandTotal_in_hand_qnty=$grandTotal_in_hand_value=0;
                	foreach ($main_arr as $buyer => $buyer_data)
                	{  // $com_id = [$buyer][$buyer_data]['company_name'];
                        //echo $com_id.'system';
                		foreach ($buyer_data as $marcent => $row) 
                		{                			
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$lc_company_name=$lc_com_arr[$buyer][$marcent]['company_name'];
							$w_company_name=$w_com_arr[$buyer][$marcent]['style_owner'];
							?>
		                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		                        <td width="40"><? echo $i; ?></td>
		                        <td width="100"><? echo $company_arr[$lc_company_name]; ?></td>
		                        <td width="100"><? echo $company_arr[$w_company_name]; ?></td>
		                        <td width="100"><? echo $buyer_arr[$buyer]; ?></td>
		                        <td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $dealing_mer_arr[$marcent]; ?></td>
		                        <?
		                        $total_po_quantity=$total_inhand_qty=$total_in_hand_value=0;
			                    foreach($month_year_arr as $month_year => $row)
			                    {	 
			                    	$po_quantity=$month_year_qty_arr[$buyer][$marcent][$month_year]['po_quantity'];
			                    	$inhand_qty=$month_year_qty_arr[$buyer][$marcent][$month_year]['inhand_qty'];
			                    	$in_hand_value=$month_year_qty_arr[$buyer][$marcent][$month_year]['in_hand_value'];

			                    	$grand_Total_po_quantity[$month_year]+=$po_quantity;                   	
			                    	$grand_Total_inhand_qty[$month_year]+=$inhand_qty;                   	
			                    	$grand_Total_in_hand_value[$month_year]+=$in_hand_value;                   	
			                    	?> 
			                        <td width="100"><? echo number_format($po_quantity,2); $total_po_quantity+=$po_quantity; ?></td>
			                        <td width="100"><? echo number_format($inhand_qty,2); $total_inhand_qty+=$inhand_qty; ?></td>
			                        <td width="100"><? echo number_format($in_hand_value,2); $total_in_hand_value+=$in_hand_value; ?></td>
				                    <?                    	
			                    }
			                    ?>
								<td width="130"><? echo $total_inhand_qty;  ?></td>
		                        <td width="130"><? echo $total_in_hand_value; ?></td>

		                    </tr>
		                    <?
		                    $i++;
		                    $grandTotal_in_hand_qnty+=$total_inhand_qty;
		                    $grandTotal_in_hand_value+=$total_in_hand_value;
                		}
                	}
	                ?>
	            </tbody>
        	</table>

        	<table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
	        	<tfoot>
	                <tr>
	                    <td width="40"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="100" align="right"><strong>Grand Total:</strong></td>
	                    <?
	                    foreach($month_year_arr as $month_year => $row)
	                    {	                    	
                    		?>
	                    <td width="100"><strong><? echo number_format($grand_Total_po_quantity[$month_year],2); ?></strong></td>
	                    <td width="100"><strong><? echo number_format($grand_Total_inhand_qty[$month_year],2); ?></strong></td>
	                    <td width="100"><strong><? echo number_format($grand_Total_in_hand_value[$month_year],2); ?></strong></td>
	                    <?
		                }
		                ?>
	                    <td width="130"><strong><? echo $grandTotal_in_hand_qnty; ?></strong></td>
	                    <td width="130"><strong><? echo $grandTotal_in_hand_value; ?></strong></td>
	                </tr>
	            </tfoot>
        	</table>
		</fieldset>
	</div>
    <?
    foreach (glob("$user_id*.xls") as $filename2) 
	{
		@unlink($filename2);
		//if( @filemtime($filename2) < (time()-$seconds_old) )
	}
	//---------end------------//
	$name=time();
	$filename2=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename2, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename2=$user_id."_".$name.".xls";
	//ob_end_clean();
	//echo "$total_data****$filename2";

	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($filename2).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename2));
    flush(); // Flush system output buffer
    readfile($filename2);
    exit;
}
if ($action=="print_preview_5") // Created by Shafiq-Sumon
{
	//echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$data_ref=explode("__",str_replace("'","",$company_id));
	$com_id=$data_ref[0];
	$bank_id=$data_ref[1];
	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$buyer_bank_arr = return_library_array("select id,bank_id from lib_buyer where status_active=1 and is_deleted=0","id","bank_id");
	$bank_arr = return_library_array("select id, bank_name from lib_bank","id","bank_name");
	$location_array = return_library_array("select id, location_name from lib_location where status_active=1 ","id","location_name");
	$dealing_mer_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
	//print_r($location_array);die;

	/*$com_cond="";
    if($company_id>0) $com_cond=" and a.company_name=$company_id";
	$ex_fact_data=return_library_array("select b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
	//echo "<pre>";print_r($ex_fact_data);die;
	
	if ($db_type==0) $month_year_cond="date_format(c.pub_shipment_date, '%M-%y') as month_year";
	else $month_year_cond="to_char(c.pub_shipment_date, 'Month-YY') as month_year";

	$main_sql="select a.company_name, a.style_owner, a.buyer_name, a.location_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, sum(b.order_quantity) as po_quantity, sum(b.order_total) as order_total, $month_year_cond
	from wo_po_details_master a, wo_po_color_size_breakdown b, wo_po_break_down c
	where a.job_no=c.job_no_mst and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and c.is_deleted=0 $com_cond 
	group by a.company_name, a.location_name, a.style_owner, a.buyer_name, a.dealing_marchant, b.shiping_status, b.po_break_down_id, c.job_no_mst, c.po_number, c.pub_shipment_date 
	order by c.pub_shipment_date asc";*/
	
	if ($db_type==0) $month_year_select="date_format(c.pub_shipment_date, '%M-%y') as month_year";
	else $month_year_select="to_char(c.pub_shipment_date, 'Month-YY') as month_year";
	$exfact_com_cond="";
	if($com_id>0) $exfact_com_cond=" and a.company_id=$com_id";
	$ex_fact_data=return_library_array("SELECT b.po_break_down_id, sum(b.ex_factory_qnty) as ex_qnty from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form<>85 and b.shiping_status <> 3 $exfact_com_cond group by b.po_break_down_id","po_break_down_id","ex_qnty");
	//echo "<pre>";print_r($ex_fact_data);die;
	$com_cond="";
	if($com_id>0) $com_cond=" and a.company_name=$com_id";
	if($bank_id>0) $com_cond.=" and b.TAG_BANK=$bank_id";
	//, a.location_name
	$main_sql="SELECT a.company_name, a.style_owner, a.buyer_name, a.dealing_marchant, c.shiping_status, c.id as po_break_down_id, c.job_no_mst, c.po_number, b.bank_id, c.pub_shipment_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.po_total_price as order_total, $month_year_select
	FROM wo_po_details_master a, lib_buyer_tag_bank b, wo_po_break_down c 
	WHERE a.job_no=c.job_no_mst and b.buyer_id=a.buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.shiping_status<>3 and b.TAG_BANK>0 $com_cond
	order by c.pub_shipment_date";
	
	//echo $main_sql;//die;

	$main_sql_result=sql_select($main_sql);

	$i=0;$test_count=0;$test_value=0;
	foreach ($main_sql_result as $value) 
	{
		/*if($buyer_bank_arr[$value[csf('buyer_name')]])
		{
			$inhand_data_array[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["po_quantity"]+=$value[csf('po_quantity')];
			$inhand_data_array[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["order_total"]+=$value[csf('order_total')];

			//$dtls_data[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('company_name')]][$value[csf('location_name')]]=$value[csf('location_name')];
			$dtls_data[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('company_name')]][$value[csf('style_owner')]]=$value[csf('style_owner')];
			//$summery_data[$value[csf('company_name')]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_qnty"]+=$value[csf('po_quantity')];
			$summery_data[$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_qnty"]+=$value[csf('po_quantity')];
			//$summery_data[$value[csf('company_name')]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_amt"]+=$value[csf('order_total')];
			$summery_data[$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_amt"]+=$value[csf('order_total')];

			//$bank_wise_summery_data[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_qnty"]+=$value[csf('po_quantity')];
			$bank_wise_summery_data[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_qnty"]+=$value[csf('po_quantity')];
			//$bank_wise_summery_data[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_amt"]+=$value[csf('order_total')];
			$bank_wise_summery_data[$buyer_bank_arr[$value[csf('buyer_name')]]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_amt"]+=$value[csf('order_total')];
			$all_month[$value[csf('month_year')]]=$value[csf('month_year')];
		}*/
		//$value[csf('bank_id')]
		$order_rate=$value[csf('order_total')]/$value[csf('po_quantity')];
		$pending_qnty=$value[csf('po_quantity')]-$ex_fact_data[$value[csf('po_break_down_id')]];
		$pending_value=$pending_qnty*$order_rate;
		if($pending_qnty>0)
		{
			$test_value+=$pending_value;
			$inhand_data_array[$value[csf('bank_id')]][$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["po_quantity"]+=$pending_qnty;
			$inhand_data_array[$value[csf('bank_id')]][$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["order_total"]+=$pending_value;
	
			//$dtls_data[$value[csf('bank_id')]][$value[csf('company_name')]][$value[csf('location_name')]]=$value[csf('location_name')];
			$dtls_data[$value[csf('bank_id')]][$value[csf('company_name')]][$value[csf('style_owner')]]=$value[csf('style_owner')];
			//$summery_data[$value[csf('company_name')]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_qnty"]+=$value[csf('po_quantity')];
			$summery_data[$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_qnty"]+=$pending_qnty;
			//$summery_data[$value[csf('company_name')]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_amt"]+=$value[csf('order_total')];
			$summery_data[$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_amt"]+=$pending_value;
	
			//$bank_wise_summery_data[$value[csf('bank_id')]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_qnty"]+=$value[csf('po_quantity')];
			$bank_wise_summery_data[$value[csf('bank_id')]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_qnty"]+=$pending_qnty;
			//$bank_wise_summery_data[$value[csf('bank_id')]][$value[csf('location_name')]][$value[csf('month_year')]]["tot_amt"]+=$value[csf('order_total')];
			$bank_wise_summery_data[$value[csf('bank_id')]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_amt"]+=$pending_value;
			$all_month[$value[csf('month_year')]]=$value[csf('month_year')];
			$i++;
			$test_count++;
			$test_data[$value[csf('po_break_down_id')]]=$pending_value;
		}
	}
	//echo $test_value;die;
	//echo "<pre>";print_r($test_data);die;
	//echo "<pre>";print_r($inhand_data_array);die;
	foreach($dtls_data as $bank_id=>$bank_val)
	{
		foreach($bank_val as $com_id=>$com_val)
		{
			foreach($com_val as $location_id=>$loc_val)
			{
				$bank_colspan[$bank_id]++;
				$com_colspan[$bank_id][$com_id]++;
				$dtls_colspan++;
			}
		}
	}
	$tot_colspan=0;
	foreach($summery_data as $com_id=>$com_val)
	{
		foreach($com_val as $location_id=>$loc_val)
		{
			$tot_colspan++;
			$tot_com_colspan[$com_id]++;
			
		}
	}
	$dtls_tot_colspan=$dtls_colspan+$tot_colspan;

	//print_r ( $grand_total_qnty);die;
	//echo "<pre>";print_r($grand_total_qnty);//die;
	//echo $count_col."=".$com_col."=".$bank_col;die;
	$table_width=(150+(100*($dtls_tot_colspan*2)));
	$div_width=$table_width+20;

	ob_start();
    ?>
    <div id="report_container" align="center" style="width:<? echo $div_width; ?>px">
		<fieldset style="width:<? echo $table_width; ?>px;">
		    <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0">
		        <thead>
	   				<tr>
	   					<th bgcolor="#d6fff1" colspan="<? echo ($dtls_tot_colspan*2)+1;?>">Shipment Schedule Of Order In Hand</th>
					</tr>
					<tr>	   					
	   					<th bgcolor="#b4f3bf">Bank</th>
						   <? 
							 foreach ($dtls_data as $bank_id => $bank_name) 
							 {
								?>
								<th bgcolor="#eaffc9" colspan="<? echo $bank_colspan[$bank_id]*2;?>" title="<? echo $bank_id ?>"><? echo $bank_arr[$bank_id];?></th>
								<?
								$bank_tot_colspan+=$bank_colspan[$bank_id]*2;
						 	  }  
						   ?>
					   	<!-- <th colspan="<? //echo $month_colspan;?>">Brack Bank</th> -->
					   	<th bgcolor="#ffe2c9" colspan="<? echo $tot_colspan*2;?>">Total</th>
					</tr>
					<tr>
						<th bgcolor="#b4f3bf" colspan="">Factories</th>
						<? 
						foreach($dtls_data as $bank_id=>$bank_val)
						{
							foreach($bank_val as $com_id=>$com_val)
							{									
								?>
								<th bgcolor="#dfefff" colspan="<? echo $com_colspan[$bank_id][$com_id]*2;?>"><? echo $company_arr[$com_id];?></th>
								<?
								$fac_tot_colspan+=$com_colspan[$bank_id][$com_id]*2;
							}
						}  
						
						foreach($summery_data as $com_id=>$com_val)
						{
							?>
							<th bgcolor="#b4f3bf" colspan="<? echo $tot_com_colspan[$com_id]*2;?>"><? echo $company_arr[$com_id];?></th>
							<?
						}
						?>						

					</tr>
					<tr>
						<th bgcolor="#bffffb">Location</th>
						<? 
						foreach($dtls_data as $bank_id=>$bank_val)
						{
							foreach($bank_val as $com_id=>$com_val)
							{
								foreach($com_val as $location_id=>$location_val)
								{
									?>
									<!-- <th bgcolor="#e8bfff" colspan="2"><? //echo $location_array[$location_id];?></th> -->
									<th bgcolor="#b4f3bf" colspan="2"><? echo $location_array[$location_id];?></th>
									<?
								}
								
							}
						}
						foreach($summery_data as $com_id=>$com_val)
						{
							foreach($com_val as $location_id=>$location_val)
							{
								?>
								<th bgcolor="#bffffb" colspan="2"><? echo $location_array[$location_id];?></th>
								<?
							}
						} 
						?>
					</tr>
					<tr>
						<th bgcolor="#b4f3bf">Month</th>
						<? 
						foreach ($dtls_data as $bank_name => $company_data) 
						{
							foreach ($company_data as $company => $location_data) 
							{ # code...
								foreach ($location_data as $location => $month_data) 
								{
									?>

									<th width="100">Inhand Qnty</th>
									<th width="100">Inhand Value</th>
									<?
								}
							}
						} 
						foreach($summery_data as $com_id=>$com_val)
						{
							foreach($com_val as $location_id=>$location_val)
							{
								?>
								<th>Inhand Qnty</th>
								<th>Inhand Value</th>
								<?
							}
						}
						?>
					</tr>
		        </thead>				
		    
	            <tbody>
					
	            	<?
	                $i=1;
	                //$grandTotal_in_hand_qnty=$grandTotal_in_hand_value=0;
                	foreach ($all_month as $month_id => $month_name) 
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        	<td bgcolor="#c9ffef"><p>&nbsp;<? echo $month_name;?></p></td>
                            <?
							foreach ($dtls_data as $bank_name => $company_data) 
							{
								foreach ($company_data as $company => $location_data) 
								{ # code...
									foreach ($location_data as $location => $month_data) 
									{
										?>
										<td align="right"><? echo $inhand_data_array[$bank_name][$company][$location][$month_id]["po_quantity"];?></td>
										<td align="right"><? echo number_format($inhand_data_array[$bank_name][$company][$location][$month_id]["order_total"],2);?></td>
										<?
										$grand_total_qnty[$bank_name][$company][$location]['tot_qnty'] += $inhand_data_array[$bank_name][$company][$location][$month_id]["po_quantity"];
										$grand_total_qnty[$bank_name][$company][$location]['tot_amt'] += $inhand_data_array[$bank_name][$company][$location][$month_id]["order_total"];
									}
								}
							} 
							foreach($summery_data as $com_id=>$com_val)
							{
								foreach($com_val as $location_id=>$location_val)
								{
									//$summery_data[$value[csf('company_name')]][$value[csf('style_owner')]][$value[csf('month_year')]]["tot_qnty"]+=$value[csf('po_quantity')];
									?>
									<td align="right"><? echo $location_val[$month_id]["tot_qnty"]; 
									$grand_tot_summery_qnty_total +=$location_val[$month_id]["tot_qnty"];?></td>
									<td align="right"><? echo number_format($location_val[$month_id]["tot_amt"],2);
									$grand_tot_summery_value_total  += $location_val[$month_id]["tot_amt"] ;
									?></td>
									<?
									$toatl_qnty[$com_id][$location_id]["total_po_quantity"] += $location_val[$month_id]["tot_qnty"];
									$toatl_qnty[$com_id][$location_id]["total_po_value"] += $location_val[$month_id]["tot_amt"] ;
								}
							}
							?>
                        </tr>
                        <?
						$i++;
						//$tot_summery_qnty=0;$tot_summery_value=0;
						//$toatl_qnty=0;$toatl_value=0;
					}
	                ?>
					
	            </tbody>
				<tfoot>
					<tr bgcolor="#ffc1bf">
						<td align="right"><strong>Total:</strong></td>
					<?
						foreach ($dtls_data as $bank_name => $company_data) 
						{
							foreach ($company_data as $company => $location_data) 
							{ # code...
								foreach ($location_data as $location => $month_data) 
								{
									?>
									<td align="right"><strong><? echo $grand_total_qnty[$bank_name][$company][$location]['tot_qnty'];?></strong></td>
									<td align="right"><strong><? echo number_format($grand_total_qnty[$bank_name][$company][$location]['tot_amt'],2);?></strong></td>
									<?
									//$grand_total_qnty[$bank_name][$company][$location]['tot_qnty'] += $inhand_data_array[$bank_name][$company][$location][$month_id]["po_quantity"];
									//$grand_total_qnty[$bank_name][$company][$location]['tot_amt'] += $inhand_data_array[$bank_name][$company][$location][$month_id]["order_total"];
								}
							}
						}
						
						
						foreach($summery_data as $com_id=>$com_val)
						{
							foreach($com_val as $location_id=>$location_val)
							{
								?>

								<td align="right"><strong><? echo $toatl_qnty[$com_id][$location_id]["total_po_quantity"];?></strong></td>
								<td align="right"><strong><? echo number_format($toatl_qnty[$com_id][$location_id]["total_po_value"] ,2);?></strong></td>
								<?
							}
						}
						?>
					</tr>
				</tfoot>
        	</table>
			<? 
				//echo "<pre>";print_r($grand_total_qnty);
				//echo "<pre>";print_r($dtls_data);
				//die;
			?>
		</fieldset>
	</div>
    <?
    foreach (glob("$user_id*.xls") as $filename2) 
	{
		@unlink($filename2);
		//if( @filemtime($filename2) < (time()-$seconds_old) )
	}
	//---------end------------//
	$name=time();
	$filename2=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename2, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename2=$user_id."_".$name.".xls";
	//ob_end_clean();
	//echo "$total_data****$filename2";

	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($filename2).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename2));
    flush(); // Flush system output buffer
    readfile($filename2);
    exit;
}

function get_day_forthnightly($input_day)
{
    $input_month_year=date("m-Y",strtotime($input_day));
    $first_day="01-".$input_month_year;
    $fortnight_day=date('d-m-Y',strtotime($first_day."+2 week"));
    $last_day=date("t-m-Y", strtotime($input_day));
    $fortnight_first_month='01-15 '.date("M-Y",strtotime($input_day));
    $fortnight_last_month='16-'.date("t", strtotime($input_day)).' '.date("M-Y",strtotime($input_day));
    $difference = strtotime($last_day)-strtotime($first_day);
    $difference_day=(floor($difference / 86400) + 1);
    $forthnight_day_arr=array();
    for($i=1; $i<=$difference_day; $i++)
    {
        if($i==1)
        {
            $forthnight_day_arr[$first_day]=$fortnight_first_month;
            $next_day=date('d-m-Y',strtotime($first_day."+1 day"));
        }
        else
        {
            if(strtotime($next_day)>strtotime($fortnight_day))$fortnight_month=$fortnight_last_month; else $fortnight_month=$fortnight_first_month;
            $forthnight_day_arr[$next_day]=$fortnight_month;
            $next_day=date('d-m-Y',strtotime($next_day."+1 day"));
        }        
    }
    //return $forthnight_day_arr; // return whole month arr
	return $forthnight_day_arr[date('d-m-Y',strtotime($input_day))];// return specific fortnight
}

disconnect($con);
?>
