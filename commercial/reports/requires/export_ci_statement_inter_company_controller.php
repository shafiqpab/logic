<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}


if ($action=="lc_sc_no_auto_com")
{
	$sql = "SELECT b.export_lc_no as lc_sc_no FROM com_export_lc b WHERE b.status_active=1 AND b.is_deleted=0 AND b.beneficiary_name=$data
			UNION ALL
			SELECT c.contract_no as lc_sc_no FROM com_sales_contract c WHERE c.status_active=1 AND c.is_deleted=0 AND c.beneficiary_name=$data";
    echo "[" . substr(return_library_autocomplete($sql, "lc_sc_no"), 0, -1) . "]";
    exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_based_on=str_replace("'","",$cbo_based_on);

	$user_arr = return_library_array("select id,user_name from user_passwd ","id","user_name");
	$bank_arr=return_library_array("select id,bank_name from   lib_bank","id","bank_name");
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location_arr=return_library_array("select id,location_name from  lib_location","id","location_name");
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$supplier_arr=return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
	$country_arr=return_library_array("select id,country_name from  lib_country","id","country_name");

	$sub_sql=sql_select("select b.invoice_id, a.courier_date, a.submit_date, a.bnk_to_bnk_cour_no, a.bank_ref_no, a.possible_reali_date from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$bank_sub_data=array();
	foreach($sub_sql as $row)
	{
		$bank_sub_data[$row[csf("invoice_id")]]["courier_date"]=$row[csf("courier_date")];
		$bank_sub_data[$row[csf("invoice_id")]]["submit_date"]=$row[csf("submit_date")];
		$bank_sub_data[$row[csf("invoice_id")]]["bnk_to_bnk_cour_no"]=$row[csf("bnk_to_bnk_cour_no")];
		$bank_sub_data[$row[csf("invoice_id")]]["bank_ref_no"]=$row[csf("bank_ref_no")];
		$bank_sub_data[$row[csf("invoice_id")]]["possible_reali_date"]=$row[csf("possible_reali_date")];
	}

	$buyer_submit_date_arr=return_library_array(" select b.invoice_id,a.submit_date from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=39","invoice_id","submit_date");

	$rlz_date_arr=return_library_array(" select a.invoice_id,b.received_date,b.is_invoice_bill
	from com_export_doc_submission_invo a, com_export_proceed_realization b
	where a.doc_submission_mst_id=b.invoice_bill_id and b.is_invoice_bill  = 1
	union all
	select b.invoice_bill_id as invoice_id, b.received_date , b.is_invoice_bill
	from  com_export_proceed_realization b
	where  b.is_invoice_bill  = 2
	order by invoice_id","invoice_id","received_date");

	//$exfact_qnty_arr=return_library_array(" select invoice_no, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 and invoice_no>0 group by invoice_no","invoice_no","ex_factory_qnty");
	$exfact_qnty_arr=return_library_array(" select invoice_no,
	sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty
	from pro_ex_factory_mst where status_active=1 and is_deleted=0 and invoice_no>0 group by invoice_no","invoice_no","ex_factory_qnty");
	$variable_standard_arr=return_library_array(" select monitor_head_id,monitoring_standard_day from variable_settings_commercial where status_active=1 and is_deleted=0 and company_name=$cbo_company_name and variable_list=19","monitor_head_id","monitoring_standard_day");

	$sql_order_set=sql_select("select a.mst_id, a.po_breakdown_id, a.current_invoice_qnty, (a.current_invoice_qnty*c.total_set_qnty) as invoice_qnty_pcs, c.total_set_qnty from com_export_invoice_ship_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0");
	$inv_qnty_pcs_arr=array();
	foreach($sql_order_set as $row)
	{
		$inv_qnty_pcs_arr[$row[csf("mst_id")]]+=$row[csf("invoice_qnty_pcs")];
	}
	ob_start();


	?>
	<div style="width:2358px"> 
		<table width="2000" cellpadding="0" cellspacing="0" id="caption">
	        <tr>
	        <td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	        </tr>
	        <tr>
	        <td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?>
	        <?
			if($cbo_based_on!=0)
			{
			?>
	        (<strong style="font-size:18px">Based On:<? $based_on_arr=array(1=>"Invoice Date",2=>"Exfactory Date",3=>"Actual Date"); echo " ". $based_on_arr[$cbo_based_on]; ?></strong>)

	        <?
			}
			?></strong></td>
	        </tr>
	    </table>
		<br />           
        <table width="2700" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
	        <thead>
	            <tr>
	                <th width="50">Sl</th>
	                <th width="100">Company Name</th>
	                <th width="100">SC/LC No</th>
	                <th width="100">SC/LC Value</th>
	                <th width="150">Lien Bank</th>
	                <th width="100">PO Company</th>
	                <th width="70">PO Buyer</th>
	                <th width="120">Export PI & Date</th>
	                <th width="100">Invoice No.</th>
	                <th width="70">Invoice Date</th>
	                <th width="70">Insert Date</th>
	                <th width="70">SC/LC</th>
	                <th width="90">EXP Form No</th>
	                <th width="70">EXP Form Date</th>
	                <th width="100">Invoice value</th>
	                <th width="80">Discount</th>
	                <th width="70">Bonous</th>
	                <th width="70">Claim</th>
	                <th width="100">Net Invoice Amount</th>
	                <th width="80">Currency</th>
	                <th width="70">ETD Date</th>
	                <th width="70">Ex-Factory Date</th>
	                <th width="70">Actual Buyer Sub Date</th>
	                <th width="70">Actual Bank Sub Date</th>
	                <th width="70">Bank Sub Days</th>
	                <th width="100">Bank Bill No.</th>
	                <th width="70">Bank Bill Date</th>
	                <th width="80">Pay Term</th>
	                <th width="70">Possible Rlz. Date</th>
	                <th width="70">Actual Realized Date</th>
	                <th width="70">Realization Days</th>
	                <th >Realization Amount</th>
	            </tr>
	        </thead>
        </table>
        <div style="width:2710px; overflow-y:scroll; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body_short" align="left">
            <table width="2700" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
            	<tbody>
	                <?
	                $cbo_company_name=str_replace("'","",$cbo_company_name);
	                if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";

	                $cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	                if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;

	                $cbo_location=str_replace("'","",$cbo_location);
	                if($cbo_location == 0) $cbo_location="%%"; else $cbo_location = $cbo_location;

	                if($cbo_based_on == 0) $cbo_based_on="%%"; else $cbo_based_on = $cbo_based_on;

	                $txt_date_from=str_replace("'","",$txt_date_from);
	                if(trim($txt_date_from)!= "") $txt_date_from  =$txt_date_from;

	                $txt_date_to=str_replace("'","",$txt_date_to);
	                if(trim($txt_date_to)!= "") $txt_date_to = $txt_date_to;

					$txt_invoice_no=str_replace("'","",$txt_invoice_no);
					if($txt_invoice_no!="") $invoice_cond=" and a.invoice_no like '%$txt_invoice_no'"; else $invoice_cond="";

	                //if(trim($data[7])!="") $cbo_year2=$data[7];
	                if ($txt_date_from!="" && $txt_date_to!="")
	                {
						if($cbo_based_on ==0)
						{
							$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==1)
						{
							$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==2)
						{
							$str_cond=" and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==3)
						{
							$str_cond=" and a.actual_shipment_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==4)
						{
							$str_cond=" and a.bl_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==5)
						{
							$str_cond=" and a.ship_bl_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==6)
						{
							$str_cond=" and e.received_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==7)
						{

							if($db_type==0) $str_cond=" and a.insert_date between '$txt_date_from 00:00:01' and  '$txt_date_to 23:59:59'";
							else if($db_type==2) $str_cond=" and a.insert_date between '$txt_date_from 12:00:01 AM' and  '$txt_date_to 11:59:59 PM'";
						}
						else if($cbo_based_on ==8)
						{
							$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
						}
	                }
	                else
	                {
	                	$str_cond="";
	                }
					
					$txt_lc_sc_no=str_replace("'","",$txt_lc_sc_no);
					//echo  $str_cond;die;
					if($cbo_based_on==6)
					{
						if($db_type==0)
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.lc_value as lc_sc_value, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_doc_submission_invo d, com_export_proceed_realization e, com_export_lc_order_info f
							WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and b.id=f.com_export_lc_id and f.is_sales=1 and b.export_item_category=10 and f.status_active=1 and f.is_deleted=0 and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $str_cond $invoice_cond
							GROUP by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, b.currency_name, b.pay_term, b.lien_bank, b.export_lc_no, b.lc_value, a.insert_date, b.doc_presentation_days, a.co_date,b.tenor

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.contract_value as lc_sc_value, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_doc_submission_invo d, com_export_proceed_realization e, com_sales_contract_order_info g
							WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id and c.export_item_category=10 and c.id=g.com_sales_contract_id and g.is_sales=0 and g.status_active=1 and g.is_deleted=0 and a.is_lc=2 and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.contract_no LIKE '%$txt_lc_sc_no%' and  a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond $invoice_cond
							GROUP by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, c.contract_value, a.insert_date, c.doc_presentation_days, a.co_date, c.tenor

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.contract_value as lc_sc_value, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e, com_sales_contract_order_info g
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' and c.export_item_category=10 and c.id=g.com_sales_contract_id and g.is_sales=0 and g.status_active=1 and g.is_deleted=0 and a.is_lc=2 and a.lc_sc_id=c.id and e.invoice_bill_id = a.id and e.is_invoice_bill = 2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $invoice_cond
							GROUP by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, c.contract_value, a.insert_date, c.doc_presentation_days, a.co_date,c.tenor
							ORDER BY id";
						}
						else
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode,b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.lc_value as lc_sc_value, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_doc_submission_invo d, com_export_proceed_realization e, com_export_lc_order_info f
							WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and b.id=f.com_export_lc_id and f.is_sales=1 and b.export_item_category=10 and f.status_active=1 and f.is_deleted=0  and a.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond $invoice_cond
							GROUP by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode,b.currency_name, b.pay_term, b.lien_bank, b.export_lc_no, b.lc_value, a.insert_date, b.doc_presentation_days, a.co_date, b.tenor

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.contract_value as lc_sc_value, to_char(a.insert_date,'DD-MM-YYYY') as insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_doc_submission_invo d, com_export_proceed_realization e, com_sales_contract_order_info g
							WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.contract_no LIKE '%$txt_lc_sc_no%' and c.export_item_category=10 and c.id=g.com_sales_contract_id and g.is_sales=0 and g.status_active=1 and g.is_deleted=0  and a.is_lc=2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond $invoice_cond
							GROUP by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, c.contract_value, a.insert_date, c.doc_presentation_days, a.co_date,c.tenor

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.contract_value as lc_sc_value, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e, com_sales_contract_order_info g
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' and c.export_item_category=10 and c.id=g.com_sales_contract_id and g.is_sales=0 and g.status_active=1 and g.is_deleted=0 and a.is_lc=2 and a.lc_sc_id=c.id and e.invoice_bill_id = a.id and e.is_invoice_bill=2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $invoice_cond
							GROUP by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, c.contract_value, a.insert_date, c.doc_presentation_days, a.co_date, c.tenor
							ORDER BY id";
						}
					}
					else if($cbo_based_on==8)
					{
						if($db_type==0)
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.lc_value as lc_sc_value, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_lc_order_info c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' and b.id=c.com_export_lc_id and c.is_sales=1 and b.export_item_category=10 and c.status_active=1 and c.is_deleted=0 and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond   $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1)
							GROUP by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, b.currency_name, b.pay_term, b.lien_bank, b.export_lc_no, b.lc_value, a.insert_date, b.doc_presentation_days, a.co_date,b.tenor

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.contract_value as lc_sc_value, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c, com_sales_contract_order_info d
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' and c.export_item_category=10 and c.id=d.com_sales_contract_id and d.is_sales=0 and d.status_active=1 and d.is_deleted=0 and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2)  
							GROUP by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, c.contract_value, a.insert_date, c.doc_presentation_days, a.co_date, c.tenor
							ORDER BY id";
						}
						else
						{
							$realized_invoice = sql_select("SELECT a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b
							where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2 and b.status_active = 1 and b.is_deleted = 0
							union all
							select b.invoice_bill_id as invoice_id from  com_export_proceed_realization b where b.is_invoice_bill=2 and b.benificiary_id = $cbo_company_name and b.status_active = 1 and b.is_deleted = 0");
							foreach ($realized_invoice as $val)
							{
								$all_realized_invoice_arr[$val[csf("invoice_id")]] = $val[csf("invoice_id")];
							}

							if(count($all_realized_invoice_arr)>999)
							{
								$all_realized_invoice_chunk_arr=array_chunk($all_realized_invoice_arr, 999);
								$all_realized_invoice_cond=" and (";
								foreach ($all_realized_invoice_chunk_arr as $value)
								{
									$all_realized_invoice_cond .="and a.id not in (".implode(",", $value).") or ";
								}
								$all_realized_invoice_cond=chop($all_realized_invoice_cond,"or ");
								$all_realized_invoice_cond.=")";
							}
							else
							{
								$all_realized_invoice_cond=" and a.id not in (".implode(",", $all_realized_invoice_arr).")";
							}

							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode,b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.lc_value as lc_sc_value, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_lc_order_info c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and b.id=c.com_export_lc_id and c.is_sales=1 and b.export_item_category=10 and c.status_active=1 and c.is_deleted=0 and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $str_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1)
							GROUP by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode,b.currency_name , b.pay_term, b.lien_bank, b.export_lc_no, b.lc_value, a.insert_date, b.doc_presentation_days, a.co_date,b.tenor

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.contract_value as lc_sc_value, to_char(a.insert_date,'DD-MM-YYYY') as insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c, com_sales_contract_order_info d
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.contract_no LIKE '%$txt_lc_sc_no%' and c.export_item_category=10 and c.id=d.com_sales_contract_id and d.is_sales=0 and d.status_active=1 and d.is_deleted=0 and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond $invoice_cond $all_realized_invoice_cond
							GROUP by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, c.contract_value, a.insert_date, c.doc_presentation_days, a.co_date, c.tenor
							ORDER BY id";
						}
					}
					else
					{
						if($db_type==0)
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.lc_value as lc_sc_value, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_lc_order_info c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and b.id=c.com_export_lc_id and c.is_sales=1 and b.export_item_category=10 and c.status_active=1 and c.is_deleted=0 and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $invoice_cond
							GROUP by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, b.currency_name, b.pay_term, b.lien_bank, b.export_lc_no, b.lc_value, a.insert_date, b.doc_presentation_days, a.co_date

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.contract_value as lc_sc_value, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c, com_sales_contract_order_info d
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' and c.export_item_category=10 and c.id=d.com_sales_contract_id and d.is_sales=0 and d.status_active=1 and d.is_deleted=0 and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $invoice_cond 
							GROUP by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, c.contract_value, a.insert_date, c.doc_presentation_days, a.co_date, c.tenor
							ORDER BY id";
						}
						else
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode,b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.lc_value as lc_sc_value, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor,c.wo_po_break_down_id
							FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_lc_order_info c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' and b.id=c.com_export_lc_id and c.is_sales=1 and b.export_item_category=10 and c.status_active=1 and c.is_deleted=0 and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond $invoice_cond
							GROUP by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date,a.ex_factory_date,a.is_lc,a.buyer_id,a.exp_form_no,a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge,a.net_invo_value,a.invoice_quantity,a.total_carton_qnty,a.actual_shipment_date,a.shipping_bill_n,a.ship_bl_date,a.bl_no,a.bl_date,a.etd,a.feeder_vessel,a.mother_vessel,a.etd_destination,a.bl_rev_date,a.ic_recieved_date,a.remarks,a.gsp_co_no,a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date,a.forwarder_name,a.country_id,a.shipping_mode,b.currency_name,b.pay_term,b.lien_bank,b.export_lc_no, b.lc_value,a.insert_date,b.doc_presentation_days,a.co_date,b.tenor,c.wo_po_break_down_id

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.contract_value as lc_sc_value, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor,d.wo_po_break_down_id
							FROM com_export_invoice_ship_mst a, com_sales_contract c, com_sales_contract_order_info d
							WHERE a.benificiary_id LIKE '$cbo_company_name' and a.location_id LIKE '$cbo_location' and a.buyer_id LIKE '$cbo_buyer_name' and c.export_item_category=10 and c.id=d.com_sales_contract_id and d.is_sales=0 and d.status_active=1 and d.is_deleted=0 and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $invoice_cond
							GROUP by a.id,a.benificiary_id,a.location_id,a.invoice_no,a.invoice_date,a.ex_factory_date,a.is_lc,a.buyer_id,a.exp_form_no,a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge,a.net_invo_value,a.invoice_quantity,a.total_carton_qnty,a.actual_shipment_date,a.shipping_bill_n,a.ship_bl_date,a.bl_no,a.bl_date,a.etd,a.feeder_vessel,a.mother_vessel,a.etd_destination,a.bl_rev_date,a.ic_recieved_date,a.remarks,a.gsp_co_no,a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date,a.forwarder_name,a.country_id,a.shipping_mode,c.currency_name,c.pay_term,c.lien_bank,c.contract_no, c.contract_value,a.insert_date,c.doc_presentation_days,a.co_date,c.tenor,d.wo_po_break_down_id
							ORDER BY id";
						}
					}
					//echo $sql;//die;

					$poBuyerComp_sql="SELECT a.id, b.buyer_id, b.company_id  from fabric_sales_order_mst a, wo_booking_mst b where a.booking_id=b.id
					group by a.id, b.buyer_id, b.company_id";
					$poBuyerComp_data = sql_select($poBuyerComp_sql);
					$buyer_array=array();
					$company_array=array();
					foreach($poBuyerComp_data as $row)
					{
						$buyer_array[$row[csf('id')]]['buyer_id']=$buyer_arr[$row[csf('buyer_id')]];
						$company_array[$row[csf('id')]]['company_id']=$company_arr[$row[csf('company_id')]];
					}
					/*echo "<pre>";
					print_r($buyer_array);
					echo "</pre>";*/

	                $sql_re=sql_select($sql);$k=1;
	                foreach($sql_re as $row_result)
	                {
						if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$wo_po_break_down=$row_result[csf('wo_po_break_down_id')];

						$id=$row_result[csf('id')];
						$exfact_date_calculate=$row_result[csf('ex_factory_date')];//$variable_standard_arr
						$bl_date_calculate=$row_result[csf('bl_date')];
						$variable_standard_bl_day=$possiable_bl_date=$realization_sub_day=$doc_presentation_days=$possiable_bank_sub_date=$variable_standard_gsp_day=$possiable_gsp_date=$variable_standard_co_day=$possiable_co_date="";
						if($exfact_date_calculate!="" && $exfact_date_calculate!='0000-00-00')
						{
							$variable_standard_bl_day=$variable_standard_arr[1]*60*60*24;
							$possiable_bl_date=date('d-m-Y',strtotime($exfact_date_calculate)+$variable_standard_bl_day);

						}
						if($bl_date_calculate!="" && $bl_date_calculate!='0000-00-00')
						{
							if($row_result[csf("type")]==1)
							{
								$realization_sub_day=$bank_sub_data[$row_result[csf('id')]]["submit_date"];
								$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
								$possiable_bank_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
							}
							if($row_result[csf("type")]==2)
							{
								$realization_sub_day=$buyer_submit_date_arr[$row_result[csf('id')]];
								$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
								$possiable_buyer_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
							}
							$variable_standard_gsp_day=$variable_standard_arr[2]*60*60*24;
							$possiable_gsp_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_gsp_day);
							$variable_standard_co_day=$variable_standard_arr[3]*60*60*24;
							$possiable_co_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_co_day);

						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                    	<td width="50" style="word-break: break-all;"><? echo $k; ?></td>
	                        <td width="100" style="word-break: break-all;"><? echo  $company_arr[$row_result[csf('benificiary_id')]]; ?></td>
	                        <td width="100" style="word-break: break-all;"><? echo $row_result[csf('lc_sc_no')];?></td>
	                        <td width="100" style="word-break: break-all;"><? echo $row_result[csf('lc_sc_value')];?></td>
	                        <td width="150" style="word-break: break-all;"><? echo $bank_arr[$row_result[csf('lien_bank')]];?></td>
	                        <td width="100"><? echo $company_array[$row_result[csf('wo_po_break_down_id')]]['company_id']; ?></td>
	                        <td width="70" style="word-break: break-all;"><? echo $buyer_array[$row_result[csf('wo_po_break_down_id')]]['buyer_id']; ?></td>
	                        <td width="120" align="center"><p><? echo "<a href='#report_details' style='color:#000' onclick= \"openmypage_pi_date('$wo_po_break_down','pi_details','PI Details');\">"."View"."</a>"; ?></p></td>
	                        <td width="100" style="word-break: break-all;"><p>
	                        	<a href='##' style='color:#000' onClick="print_report('<? echo $row_result[csf('id')] ;?>','invoice_report_print','../export_details/requires/export_information_entry_controller')"><font color="blue"><b><? echo $row_result[csf('invoice_no')];?></b></font></a></p>
							</td>
							<td width="70" align="center" style="word-break: break-all;">
	                        <? if($row_result[csf('invoice_date')]!="0000-00-00" && $row_result[csf('invoice_date')]!="") {echo change_date_format($row_result[csf('invoice_date')]);} else {echo "&nbsp;";}?>
	                        </td>
	                        <td width="70"  align="center" style="word-break: break-all;">
	                        <? if($row_result[csf('insert_date')]!="0000-00-00" && $row_result[csf('insert_date')]!="") {echo change_date_format($row_result[csf('insert_date')]);} else {echo "&nbsp;";}?>
	                        </td>
	                        <td width="70"  align="center" style="word-break: break-all;"><? if($row_result[csf('type')] == 1) echo "LC"; else echo "SC"; ?></td>
	                        <td width="90" style="word-break: break-all;"><? echo $row_result[csf('exp_form_no')]."&nbsp;";?></td>
	                        <td width="70"  align="center" style="word-break: break-all;"><?  if($row_result[csf('exp_form_date')]!="0000-00-00" && $row_result[csf('exp_form_date')]!="") {echo change_date_format($row_result[csf('exp_form_date')]);} else {echo "&nbsp;";} ?></td>
	                        <td width="100" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('invoice_value')],2,'.',''); $total_grs_value +=$row_result[csf('invoice_value')];?></td>
	                        <td width="80" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('discount_ammount')],2,'.',''); $total_discount_value +=$row_result[csf('discount_ammount')]; ?></td>
	                        <td width="70" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('bonus_ammount')],2,'.',''); $total_bonous_value +=$row_result[csf('bonus_ammount')];  ?></td>
	                        <td width="70" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('claim_ammount')],2,'.',''); $total_claim_value +=$row_result[csf('claim_ammount')];  ?></td>
	                        <td width="100" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('net_invo_value')],2,'.',''); $total_order_qnty +=$row_result[csf('net_invo_value')];?></td>
	                        <td width="80" align="center" style="word-break: break-all;"><? echo $currency[$row_result[csf('currency_name')]];?></td>

	                         <td width="70"  align="center" style="word-break: break-all;"><? if($row_result[csf('etd')]!="0000-00-00" && $row_result[csf('etd')]!="") echo change_date_format( $row_result[csf('etd')]); else echo "";?></td>
	                        <td width="70"  align="center" style="word-break: break-all;"><? if($row_result[csf('ex_factory_date')]!="0000-00-00" && $row_result[csf('ex_factory_date')]!="") {echo change_date_format($row_result[csf('ex_factory_date')]);} else {echo "&nbsp;";} ?></td>
	                        <td width="70"   style="word-break: break-all;" align="center"><?
	                            if(trim($buyer_submit_date_arr[$row_result[csf('id')]])!='0000-00-00' && trim($buyer_submit_date_arr[$row_result[csf('id')]])!='') echo change_date_format(trim($buyer_submit_date_arr[$row_result[csf('id')]])); else echo "&nbsp;";
	                        ?></td>
	                        <td width="70"  style="word-break: break-all;"  align="center">
							<?
	                            if(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])!='0000-00-00' && trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])!='') echo change_date_format(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])); else echo "&nbsp;";
	                        ?></td>
	                        <td width="70" style="word-break: break-all;" title="From BL Date To Bank Submission Date"   align="center">
							<?
							$diff_sub=0;
							if($row_result[csf('bl_date')]!="0000-00-00" && $row_result[csf('bl_date')]!="")
							{
								$diff_sub=datediff("d",$row_result[csf('bl_date')], $bank_sub_data[$row_result[csf('id')]]["submit_date"]);
							}
							else
							{
								$diff_sub=0;
							}
							 if($diff_sub>0) echo $diff_sub."days";
							?></td>
							<td width="100" style="word-break: break-all;"><? echo $bank_sub_data[$row_result[csf('id')]]["bank_ref_no"]; ?></td>
	                        <td width="70"  style="word-break: break-all;"  align="center"><?

	                        if(!(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])=="0000-00-00" || trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])==""))
	                        {
	                           echo change_date_format($bank_sub_data[$row_result[csf('id')]]["submit_date"]);
	                        }
	                        else
	                        {
	                            echo "&nbsp;";
	                        }
	                        ?></td>

	                        <td width="80" style="word-break: break-all;"><? echo $pay_term[$row_result[csf('pay_term')]];?></td>
	                        <td width="70"  style="word-break: break-all;"  align="center">
	                        <?
							if(!(trim($bank_sub_data[$row_result[csf('id')]]["possible_reali_date"])=="0000-00-00" || trim($bank_sub_data[$row_result[csf('id')]]["possible_reali_date"])==""))
							{
								echo change_date_format($bank_sub_data[$row_result[csf('id')]]["possible_reali_date"]);
							}
							else
							{
								echo "&nbsp;";
							}
	                         ?>
	                        </td>
	                        <td width="70"  style="word-break: break-all;"  align="center">
	                        <?
							if(!(trim($rlz_date_arr[$row_result[csf('id')]])=="0000-00-00" || trim($rlz_date_arr[$row_result[csf('id')]])==""))
							{
								echo change_date_format($rlz_date_arr[$row_result[csf('id')]]);
							}
							else
							{
								echo "&nbsp;";
							}
	                         ?>
	                        </td>
	                        <td width="70" style="word-break: break-all;" align="center" title="From Bank or Buyer Submit Date To Actual Realization Date">
							<?
							if( $realization_sub_day!="" && $realization_sub_day!='0000-00-00' &&$rlz_date_arr[$row_result[csf('id')]]!="" && $rlz_date_arr[$row_result[csf('id')]]!='0000-00-00')
							{
								$diff_rlz=datediff("d",$realization_sub_day,$rlz_date_arr[$row_result[csf('id')]]);
							}
							if($diff_rlz>0) echo $diff_rlz." days";
							?></td>
	                        <td style="word-break: break-all;" align="right"><? if(!(trim($rlz_date_arr[$row_result[csf('id')]])=="0000-00-00" || trim($rlz_date_arr[$row_result[csf('id')]])==""))
							{
								echo number_format($row_result[csf('net_invo_value')],2,'.',''); $total_rlz_amt+=$row_result[csf('net_invo_value')];
							}
							else
							{
								echo "";
							}
							?></td>
						</tr>
						<?
						$k++;
	                }
	                ?>
                </tbody>
            </table>
     		<table width="2700" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
            	<tfoot>
                    <tr>
	                    <th width="50">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th width="150">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="120">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="90">&nbsp;</th>
		                <th width="70">Grand Total </th>
		                <th width="100" id="value_total_grs_value"  align="right"><? echo number_format($total_grs_value,2);  ?></th>
	                    <th width="80" id="value_total_discount_value"  align="right"><? echo number_format($total_discount_value,2);  ?></th>
		                <th width="70">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="100"  id="value_total_net_invo_value"  align="right"><? echo number_format($total_order_qnty,2);  ?></th>
		                <th width="80">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th width="70">&nbsp;</th>
		                <th id="value_total_rlz_amt"  align="right"><? echo number_format($total_rlz_amt,2);  ?></th>
                    </tr>
                </tfoot>
            </table>
 		</div>
	</div>
	<div align="left" style="font-weight:bold; margin-left:30px;"><? echo "User Id : ". $user_arr[$user_id] ." , &nbsp; THIS IS SYSTEM GENERATED STATEMENT, NO SIGNATURE REQUIRED ."; ?></div>
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
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data****$filename";
	//echo "****".$RptType;

	exit();
}

if($action=="pi_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//print_r ($wo_po_break_down); 

	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	//$composition;
	//$yarn_type;
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	?>
	<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
	}

	function window_close()
	{
		parent.emailwindow.hide();
	}

	</script>
	<div style="width:800px" align="center" id="scroll_body" >
	<fieldset style="width:100%; margin-left:10px" >
	<!--<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;&nbsp;&nbsp;
	--><input type="button" value="Close" onClick="window_close()" style="width:100px"  class="formbutton"/>
         <div id="report_container" align="center" style="width:100%" >
             <div style="width:780px">
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                	 <thead bgcolor="#dddddd">
                     	<tr>
                            <th width="30">SL</th>
                            <th width="70">PI No.</th>
                            <th width="70">PI Date</th>
                            <th width="130">Item Description</th>
                            <th width="80">Qnty</th>
                            <th width="70">Rate</th>
                            <th width="80">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                <?
		$i=1;
		$sql="SELECT a.id, a.pi_number,a.pi_date, a.item_category_id, b.color_id, b.construction, b.composition, b.quantity, b.rate, b.amount from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and b.work_order_id in ($wo_po_break_down) order by a.pi_number";
		//echo $sql;
		$result=sql_select($sql);

		$pi_arr=array();
		foreach( $result as $row)
		{
			$total_qnt+=$row[csf("quantity")];
			$total_amount+=$row[csf("amount")];

			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
            <tr bgcolor="<? echo $bgcolor ; ?>">
            	<td><? echo $i; ?></td>
                <td><? echo $row[csf("pi_number")]; ?></td>
                <td><? echo change_date_format($row[csf("pi_date")]); ?></td>
                <?
	                if($row[csf("item_category_id")]==10)
	                {
	                	$description = $row[csf("construction")]." ".$row[csf("composition")]." ".$color_name_arr[$row[csf("color_id")]];
	                }
                ?>
                <td><? echo $description; ?></td>
                <td align="right"><? echo $row[csf("quantity")]; ?></td>
                <td align="right"><? echo $row[csf("rate")]; ?></td>
                <td align="right"><? echo number_format($row[csf("amount")],2); ?></td>
            </tr>
          </tbody>
		<?
        $i++;
        }
		   ?>
             <tfoot>
                <th colspan="4" align="right">Total : </th>
                <th align="right"><? echo number_format($total_qnt,0); ?></th>
                <th>&nbsp;</th>
                <th align="right"><? echo number_format($total_amount,2); ?></th>
            </tfoot>
        </table>
		</div>
        </div>
	</fieldset>
	</div>
	<?
	exit();
}
disconnect($con);
?>
