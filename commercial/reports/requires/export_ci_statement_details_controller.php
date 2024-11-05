<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

/*if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in('$data') and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}*/

if ($action=="lc_sc_no_auto_com")
{
	$sql = "SELECT b.export_lc_no as lc_sc_no FROM com_export_lc b WHERE b.status_active=1 AND b.is_deleted=0 AND b.beneficiary_name in($data)
			UNION ALL
			SELECT c.contract_no as lc_sc_no FROM com_sales_contract c WHERE c.status_active=1 AND c.is_deleted=0 AND c.beneficiary_name in ($data)";
    echo "[" . substr(return_library_autocomplete($sql, "lc_sc_no"), 0, -1) . "]";
    exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	//$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_ascending_by=str_replace("'","",$cbo_ascending_by);
	$RptType=str_replace("'","",$RptType);
	// echo $RptType;die;
	//echo $cbo_ascending_by;die;
	if($RptType==1) //DETAILS
	{
		$user_arr = return_library_array("select id,user_name from user_passwd ","id","user_name");
		$bank_arr=return_library_array("select id,bank_name from   lib_bank","id","bank_name");
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$location_arr=return_library_array("select id,location_name from  lib_location","id","location_name");
		$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
		$supplier_arr=return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
		$country_arr=return_library_array("select id,country_name from  lib_country","id","country_name");

		$sub_sql=sql_select("SELECT b.invoice_id, a.courier_date, a.submit_date, a.bnk_to_bnk_cour_no, a.bank_ref_no, a.possible_reali_date 
			from com_export_doc_submission_mst a,com_export_doc_submission_invo b 
			where a.id=b.doc_submission_mst_id and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			
		$bank_sub_data=array();
		foreach($sub_sql as $row)
		{
			$bank_sub_data[$row[csf("invoice_id")]]["courier_date"]=$row[csf("courier_date")];
			$bank_sub_data[$row[csf("invoice_id")]]["submit_date"]=$row[csf("submit_date")];
			$bank_sub_data[$row[csf("invoice_id")]]["bnk_to_bnk_cour_no"]=$row[csf("bnk_to_bnk_cour_no")];
			$bank_sub_data[$row[csf("invoice_id")]]["bank_ref_no"]=$row[csf("bank_ref_no")];
			$bank_sub_data[$row[csf("invoice_id")]]["possible_reali_date"]=$row[csf("possible_reali_date")];
		}

		$buyer_submit_date_arr=return_library_array(" SELECT b.invoice_id,a.submit_date from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=39","invoice_id","submit_date");

		$rlz_date_arr=return_library_array(" SELECT a.invoice_id,b.received_date,b.is_invoice_bill
		from com_export_doc_submission_invo a, com_export_proceed_realization b
		where a.doc_submission_mst_id=b.invoice_bill_id and b.is_invoice_bill  = 1
		union all
		select b.invoice_bill_id as invoice_id, b.received_date , b.is_invoice_bill
		from  com_export_proceed_realization b
		where  b.is_invoice_bill  = 2
		order by invoice_id","invoice_id","received_date");

		$exfact_qnty_arr=return_library_array(" SELECT invoice_no,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty
		from pro_ex_factory_mst where status_active=1 and is_deleted=0 and invoice_no>0 group by invoice_no","invoice_no","ex_factory_qnty");
		$variable_standard_arr=return_library_array(" SELECT monitor_head_id,monitoring_standard_day from variable_settings_commercial where status_active=1 and is_deleted=0 and company_name in($cbo_company_name) and variable_list=19","monitor_head_id","monitoring_standard_day");

		$sql_order_set=sql_select("SELECT a.mst_id, a.po_breakdown_id, a.current_invoice_qnty, (a.current_invoice_qnty*c.total_set_qnty) as invoice_qnty_pcs, c.total_set_qnty from com_export_invoice_ship_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and c.company_name in ($cbo_company_name) and a.status_active=1 and a.is_deleted=0");
		$inv_qnty_pcs_arr=array();
		foreach($sql_order_set as $row)
		{
			$inv_qnty_pcs_arr[$row[csf("mst_id")]]+=$row[csf("invoice_qnty_pcs")];
		}
		ob_start();

		?>
		<style>
			.wrd_brk{word-break: break-all;}
		</style>
		
		<div style="width:5600px"> 
			<table width="5580" cellpadding="0" cellspacing="0" id="caption">
				<tr>
					<td align="center" width="100%" colspan="20" class="form_caption" >
						<strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong>
					</td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="20" class="form_caption" >
						<strong style="font-size:18px"><? echo $report_title; ?>
						<?
						if($cbo_based_on!=0)
						{
						?>
							(<strong style="font-size:18px">Based On:<? $based_on_arr=array(1=>"Invoice Date",2=>"Exfactory Date",3=>"Actual Date"); echo " ". $based_on_arr[$cbo_based_on]; ?></strong>)

						<?
						}
						?></strong>
					</td>
				</tr>
			</table>
			<br />  
			<!-- Enter after every 10 rows -->         
            <table width="5580" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
                <thead>
                    <tr>
                        <th width="30">Sl</th>
                        <th width="100">Company Name</th>
                        <th width="100">Country Name</th>
                        <th width="100">Invoice No.</th>
                        <th width="50">File</th>
                        <th width="70">Invoice Date</th>
                        <th width="70">Insert Date</th>
                        <th width="80">Ship Mode</th>
                        <th width="70">SC/LC</th>
                        <th width="100">SC/LC No.</th>

                        <th width="70">Buyer Name</th>
                        <th width="100">Forwarder Name</th>
                        <th width="150">Lien Bank</th>
                        <th width="90">EXP Form No</th>
                        <th width="70">EXP Form Date</th>
                        <th width="100">Ex-factory Qnty</th>
                        <th width="100">Invoice Qnty.</th>
                        <th width="100">Invoice Qnty. Pcs</th>
                        <th width="80">Num. Of Ctn Qnty.</th>
                        <th width="80">Avg Price</th>

                        <th width="100">Invoice value</th>
                        <th width="80">Discount</th>
                        <th width="70">Bonous</th>
                        <th width="70">Claim</th>
                        <th width="80">Commission</th>
                        <th width="80">Other Deduction</th>
                        <th width="80">Upcharge</th>
                        <th width="100">Net Invoice Amount</th>
                        <th width="80">Currency</th>
                        <th width="70">ETD Date</th>

                        <th width="70">Ex-Factory Date</th>
                        <th width="70">Actual Ship Date</th>
                        <th width="70">Possible BL Date</th>
                        <th width="100">Copy B/L No</th>
                        <th width="70">Copy B/L Date</th>
                        <th width="70">B/L Days</th>
                        <th width="70">Org B/L Rcv Date</th>
                        <th width="70">Shipping Bill No</th>
                        <th width="70">Shipping Bill Date</th>
                        <th width="70">Possible GSP Date</th>

                        <th width="70">Actual GSP Date</th>
                        <th width="100">GSP No.</th>
                        <th width="70">Possible CO Date</th>
                        <th width="70">Actual CO Date</th>
                        <th width="70">GSP Cour. Date</th>
                        <th width="70">I/C Rcv Date</th>
                        <th width="70">Document In Hand</th>
                        <th width="70">Possible Buyer Sub date</th>
                        <th width="70">Actual Buyer Sub Date</th>
                        <th width="70">Buyer Sub Days</th>

                        <th width="70">Possible Bank Sub date</th>
                        <th width="70">Actual Bank Sub Date</th>
                        <th width="70">Bank Sub Days</th>
                        <th width="100">Bank Bill No.</th>
                        <th width="70">Bank Bill Date</th>
                        <th width="70">Shipping Bill No</th>
                        <th width="70">Shipping Bill Date</th>
                        <th width="70">Gross Weight</th>
                        <th width="70">Net weight</th>
                        <th width="80">Pay Term</th>

                        <th width="80">LC Tenor</th>
                        <th width="70">Possible Rlz. Date</th>
                        <th width="70">Actual Realized Date</th>
                        <th width="70">Realization Days</th>
                        <th width="70">Realization Amount</th>
                        <th width="100">Remarks</th>
                        <th width="100">Feeder Vessle</th>
                        <th width="100">Mother Vessle</th>
                        <th width="70">ETA Dest.</th>
                        <th >B TO B Courier No</th>
                    </tr>
                </thead>
            </table>
            <div style="width:5600px; overflow-y:scroll; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
	            <table width="5580" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
	                <?	                
	                $cbo_company_name=str_replace("'","",$cbo_company_name);
	                $cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	                $cbo_lien_bank=str_replace("'","",$cbo_lien_bank);
	                $cbo_location=str_replace("'","",$cbo_location);
	                $cbo_based_on=str_replace("'","",$cbo_based_on);
	                $txt_date_from=str_replace("'","",$txt_date_from);
	                $txt_date_to=str_replace("'","",$txt_date_to);
	                $forwarder_name=str_replace("'","",$forwarder_name);
	                $txt_invoice_no=str_replace("'","",$txt_invoice_no);
	                $shipping_mode=str_replace("'","",$shipping_mode);
	                $txt_lc_sc_no=str_replace("'","",$txt_lc_sc_no);

	                $company_cond=$buyer_cond=$lien_bank_cond=$location_cond=$lien_bank_cond_sales='';
	                $forwarder_cond=$invoice_cond=$ship_cond='';

	                if($cbo_company_name != 0) $company_cond = " and a.benificiary_id in($cbo_company_name)";
	                if($cbo_buyer_name != 0) $buyer_cond=" and a.buyer_id in($cbo_buyer_name)";
	                if($cbo_lien_bank != 0) $lien_bank_cond=" and b.lien_bank in($cbo_lien_bank)";
	                if($cbo_lien_bank != 0) $lien_bank_cond_sales=" and c.lien_bank in($cbo_lien_bank)";
	                if($cbo_location != 0) $location_cond=" and a.location_id in($cbo_location)";
					if($shipping_mode !=0) $ship_cond=" and a.shipping_mode=$shipping_mode";
					if($txt_lc_sc_no !='') $lc_no_cond=" and b.export_lc_no LIKE '%$txt_lc_sc_no%'";
					if($txt_lc_sc_no !='') $sc_no_cond=" and c.contract_no LIKE '%$txt_lc_sc_no%'";			

					
	                //if($cbo_based_on == 0) $cbo_based_on="%%"; else $cbo_based_on = $cbo_based_on;          
	                if(trim($txt_date_from) != '') $txt_date_from = $txt_date_from;	                
	                if(trim($txt_date_to) != '') $txt_date_to = $txt_date_to;
					
					if($forwarder_name != 0) $forwarder_cond=" and a.forwarder_name='$forwarder_name'";		
					if($txt_invoice_no != '') $invoice_cond=" and a.invoice_no like '%$txt_invoice_no'";

					if($cbo_ascending_by != '' && $cbo_ascending_by == 1) 
					{
						$ascending_by = 'invoice_no';
						$ascendig_cond=" order by benificiary_id, $ascending_by ";
					} elseif ($cbo_ascending_by != '' && $cbo_ascending_by == 2) {
						$ascending_by = 'invoice_date';
						$ascendig_cond=" order by benificiary_id, $ascending_by ";
					} elseif ($cbo_ascending_by!='' && $cbo_ascending_by==3) {
						$ascending_by = 'exp_form_no';
						$ascendig_cond=" order by benificiary_id, $ascending_by ";
					}elseif ($cbo_ascending_by!='' && $cbo_ascending_by==4) {
						$ascending_by = 'exp_form_date';
						$ascendig_cond=" order by benificiary_id, $ascending_by ";
					}else{
						$ascendig_cond='order by benificiary_id';
					}

	                if ($txt_date_from != '' && $txt_date_to != '')
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
							$str_cond=" and a.ex_factory_date between '$txt_date_from' and '$txt_date_to'";
						}
						else if($cbo_based_on ==3)
						{
							$str_cond=" and a.actual_shipment_date between '$txt_date_from' and '$txt_date_to'";
						}
						else if($cbo_based_on ==4)
						{
							$str_cond=" and a.bl_date between '$txt_date_from' and '$txt_date_to'";
						}
						else if($cbo_based_on ==5)
						{
							$str_cond=" and a.ship_bl_date between '$txt_date_from' and '$txt_date_to'";
						}
						else if($cbo_based_on ==6)
						{
							$str_cond=" and e.received_date between '$txt_date_from' and '$txt_date_to'";
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
	                	$str_cond='';
	                }
					
					//echo  $str_cond;die;
					if($cbo_based_on==6)
					{
						if($db_type==0)
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_doc_submission_invo d, com_export_proceed_realization e
							WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $company_cond $buyer_cond $lien_bank_cond $location_cond $str_cond $forwarder_cond $ship_cond $invoice_cond $lc_no_cond

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_doc_submission_invo d, com_export_proceed_realization e
							WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id and a.is_lc=2 and  a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $buyer_cond $lien_bank_cond_sales $location_cond $str_cond $forwarder_cond $ship_cond $invoice_cond $sc_no_cond

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a. 	invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e
							WHERE a.is_lc=2 and a.lc_sc_id=c.id and e.invoice_bill_id = a.id and e.is_invoice_bill = 2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $buyer_cond $lien_bank_cond_sales $location_cond $str_cond $forwarder_cond $ship_cond $invoice_cond $sc_no_cond $ascendig_cond ";

						}
						else
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b , com_export_doc_submission_invo d, com_export_proceed_realization e
							WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $company_cond $buyer_cond $lien_bank_cond $location_cond $str_cond $forwarder_cond $ship_cond $invoice_cond $lc_no_cond

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_doc_submission_invo d, com_export_proceed_realization e
							WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id and a.is_lc=2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $buyer_cond $lien_bank_cond_sales $location_cond $str_cond $forwarder_cond $ship_cond $invoice_cond $sc_no_cond
							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e
							WHERE a.is_lc=2 and a.lc_sc_id=c.id and e.invoice_bill_id = a.id and e.is_invoice_bill = 2  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $buyer_cond $lien_bank_cond_sales $location_cond $str_cond $forwarder_cond $ship_cond $invoice_cond $sc_no_cond $ascendig_cond ";
						}
					}
					else if($cbo_based_on==8)
					{
						if($db_type==0)
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $company_cond $buyer_cond $lien_bank_cond $location_cond $str_cond $forwarder_cond $ship_cond $invoice_cond $lc_no_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1)

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $buyer_cond $lien_bank_cond_sales $location_cond $str_cond $forwarder_cond $ship_cond $invoice_cond $sc_no_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2)  $ascendig_cond";

						}
						else
						{							
							$realized_invoice = sql_select("SELECT a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b
							where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2 and b.status_active = 1 and b.is_deleted = 0
							union all
							select b.invoice_bill_id as invoice_id from  com_export_proceed_realization b where b.is_invoice_bill=2 and b.benificiary_id in($cbo_company_name) and b.status_active = 1 and b.is_deleted = 0");
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


							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $company_cond $buyer_cond $lien_bank_cond $location_cond $str_cond $forwarder_cond $ship_cond $invoice_cond $lc_no_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1)

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $buyer_cond $lien_bank_cond_sales $location_cond $str_cond $forwarder_cond $ship_cond $invoice_cond $all_realized_invoice_cond $all_submitted_invoice_cond $sc_no_cond $ascendig_cond";
						}
					}
					else
					{
						if($cbo_based_on ==9)
						{
							$submit_cond=" and b.submit_date between '$txt_date_from' and  '$txt_date_to'";

							$submitted_invoices = sql_select("SELECT a.invoice_id from com_export_doc_submission_invo a, com_export_doc_submission_mst b
							where a.doc_submission_mst_id=b.id and b.status_active = 1 and b.is_deleted = 0 and b.company_id in($cbo_company_name) and b.entry_form=40 $submit_cond ");
							foreach ($submitted_invoices as $val)
							{
								$all_submitted_invoice_arr[$val[csf("invoice_id")]] = $val[csf("invoice_id")];
							}

							if(count($all_submitted_invoice_arr)>999)
							{
								$all_submitted_invoice_chunk_arr=array_chunk($all_submitted_invoice_arr, 999);
								$all_submitted_invoice_cond=" and (";
								foreach ($all_submitted_invoice_chunk_arr as $value)
								{
									$all_submitted_invoice_cond .="and a.id in (".implode(",", $value).") or ";
								}
								$all_submitted_invoice_cond=chop($all_submitted_invoice_cond,"or ");
								$all_submitted_invoice_cond.=")";
							}
							else
							{
								$all_submitted_invoice_cond=" and a.id in (".implode(",", $all_submitted_invoice_arr).")";
							}
						}
						if($db_type==0)
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $company_cond $buyer_cond $lien_bank_cond $location_cond $str_cond $forwarder_cond $ship_cond $invoice_cond $lc_no_cond $all_submitted_invoice_cond

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $buyer_cond $lien_bank_cond_sales $location_cond $str_cond $forwarder_cond $ship_cond $invoice_cond $sc_no_cond $all_submitted_invoice_cond $ascendig_cond";//id

						}
						else
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $company_cond $buyer_cond $lien_bank_cond $location_cond $str_cond $forwarder_cond $ship_cond $invoice_cond $lc_no_cond $all_submitted_invoice_cond

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $buyer_cond $lien_bank_cond_sales $location_cond $str_cond $forwarder_cond $ship_cond $invoice_cond $sc_no_cond $all_submitted_invoice_cond $ascendig_cond";
						}
					}
					// echo $sql;die;
					$sql_re=sql_select($sql); $k=1;
					foreach($sql_re as $row)
	                {
	                	$system_id_val="'".$row[csf('id')]."'";
						$system_file_img_arr[$row[csf('id')]] = $system_id_val;
	                }
	                $system_file_img_cond = implode(',',$system_file_img_arr);

					$data_file=sql_select("select image_location, master_tble_id from common_photo_library where master_tble_id in($system_file_img_cond) and form_name='export_invoice' and is_deleted=0 and file_type=2");
					$system_file_arr=array();
					foreach($data_file as $row)
					{
						$system_file_arr[$row[csf('master_tble_id')]]['file']=$row[csf('image_location')];
					}
					unset($data_file);

					$m=1;
					$company_check_val_arr=array();
	                foreach($sql_re as $row_result)
	                {
						if ($k%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
						$id=$row_result[csf('id')];
						$exfact_date_calculate=$row_result[csf('ex_factory_date')];//$variable_standard_arr
						$bl_date_calculate=$row_result[csf('bl_date')];
						$variable_standard_bl_day=$possiable_bl_date=$realization_sub_day=$doc_presentation_days=$possiable_bank_sub_date=$variable_standard_gsp_day=$possiable_gsp_date=$variable_standard_co_day=$possiable_co_date="";
						if($exfact_date_calculate !='' && $exfact_date_calculate !='0000-00-00')
						{
							$variable_standard_bl_day=$variable_standard_arr[1]*60*60*24;
							$possiable_bl_date=date('d-m-Y',strtotime($exfact_date_calculate)+$variable_standard_bl_day);

						}
						if($bl_date_calculate !='' && $bl_date_calculate !='0000-00-00')
						{
							if($row_result[csf('type')]==1)
							{
								$realization_sub_day=$bank_sub_data[$row_result[csf('id')]]["submit_date"];
								$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
								$possiable_bank_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
							}
							if($row_result[csf('type')]==2)
							{
								$realization_sub_day=$buyer_submit_date_arr[$row_result[csf('id')]];
								$doc_presentation_days=$row_result[csf('doc_presentation_days')]*60*60*24;
								$possiable_buyer_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
							}
							$variable_standard_gsp_day=$variable_standard_arr[2]*60*60*24;
							$possiable_gsp_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_gsp_day);
							$variable_standard_co_day=$variable_standard_arr[3]*60*60*24;
							$possiable_co_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_co_day);

						}
						$company_check_val=$row_result[csf('benificiary_id')];
						if (!in_array($company_check_val,$company_check_val_arr) )
						{
							if($m != 1)
							{
								?>
								<!-- Enter after every 10 rows -->
								<!-- <tr bgcolor="yellow">
			                        <td width="30">&nbsp;</td>
			                        <td width="100">&nbsp;</td>
			                        <td width="100">&nbsp;</td>
			                        <td width="100">&nbsp;</td>
			                        <td width="50">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="80">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="100">&nbsp;</td>

			                        <td width="70">&nbsp;</td>
			                        <td width="100">&nbsp;</td>
			                        <td width="150">&nbsp;</td>
			                        <td width="90">&nbsp;</td>
			                        <td width="70" align="right"><strong>Sub Total:</strong></td>
			                        <td width="100" align="right"><? echo number_format($sub_total_ex_fact_qnty,2); ?></td>
			                        <td width="100" align="right"><? echo number_format($sub_total_invoice_qty,2); ?></td>
			                        <td width="100" align="right"><? echo number_format($sub_total_invoice_qty_pcs,2); ?></td>
			                        <td width="80" align="right"><? echo number_format($sub_total_carton_qty,2); ?></td>
			                        <td width="80" align="right"><? echo number_format($sub_total_avg_price,2);  ?></td>


			                        <td width="100" align="right"><? echo number_format($sub_total_grs_value,2);  ?></td>
			                        <td width="80" align="right"><? echo number_format($sub_total_discount_value,2);  ?></td>
			                        <td width="70" align="right"><? echo number_format($sub_total_bonous_value,2);  ?></td>
			                        <td width="70" align="right"><? echo number_format($sub_total_claim_value,2);  ?></td>
			                        <td width="80" align="right"><? echo number_format($sub_total_commission_value,2);  ?></td>
			                        <td width="80" align="right"><? echo number_format($sub_total_other_discount_value,2); ?></td>
			                        <td width="80"  align="right"><? echo number_format($sub_total_upcharge_value,2);?></td>
			                        <td width="100" align="right"><? echo number_format($sub_total_order_qnty,2);  ?></td>
			                        <td width="80">&nbsp;</td>
			                        <td width="70">&nbsp;</td>

			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>			                        
			                        <td width="100">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>

			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>

			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="100">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70"><? echo number_format($sub_total_carton_gross_weight,2);  ?></td>
			                        <td width="70"><? echo number_format($sub_total_carton_net_weight,2);  ?></td>
			                        <td width="80">&nbsp;</td>

			                        <td width="80">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td width="70" align="right"><? echo number_format($sub_total_rlz_amt,2); ?></td>
			                        <td width="100">&nbsp;</td>
			                        <td width="100">&nbsp;</td>
			                        <td width="100">&nbsp;</td>
			                        <td width="70">&nbsp;</td>
			                        <td >&nbsp;</td>
		                    	</tr> -->
							<?
							}
							$company_check_val_arr[]=$company_check_val;
							$m++;
							unset($sub_total_ex_fact_qnty);
							unset($sub_total_invoice_qty);
							unset($sub_total_invoice_qty_pcs);
							unset($sub_total_carton_qty);
							unset($sub_total_avg_price);
							unset($sub_total_grs_value);
							unset($sub_total_discount_value);
							unset($sub_total_bonous_value);
							unset($sub_total_claim_value);
							unset($sub_total_commission_value);
							unset($sub_total_other_discount_value);
							unset($sub_total_upcharge_value);
							unset($sub_total_order_qnty);
							unset($sub_total_carton_gross_weight);
							unset($sub_total_carton_net_weight);
							unset($sub_total_rlz_amt);						

						}
						?>
						<!-- double enter after every 10 rows -->
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                    	<td width="30"><? echo $k; ?></td>
	                        <td width="100" class="break-all"><p><? echo $company_arr[$row_result[csf('benificiary_id')]]; ?></p></td>
	                        <td width="100" class="break-all"><p><? echo $country_arr[$row_result[csf('country_id')]]; ?></p></td>
	                        <td width="100" class="break-all"><p>
	                        	<a href='##' style='color:#000' onClick="print_report('<? echo $row_result[csf('id')]; ?>','invoice_report_print','../export_details/requires/export_information_entry_controller')"><font color="blue"><b><? echo $row_result[csf('invoice_no')]; ?></b></font></a></p>
							</td>
							<td width="50" class="break-all"><p>
								<? 
								$file_name=$system_file_arr[$row_result[csf('id')]]['file'];
								if( $file_name !='')
								{
									?>
									<input type="button" class="image_uploader" id="fileno_<? echo $i; ?>" style="width:50px" value="File" onClick="openmypage_file(<? echo $k; ?>,'1')"/>
									<input type="hidden" class="text_boxes" id="sysid_<? echo $k; ?>" name="sysid_<? echo $k;?>" value="<? echo $row_result[csf('id')]; ?>" style="width:45px" />
									<?	  
								}
								?></p>
			                </td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;
	                        	<? if($row_result[csf('invoice_date')] != '0000-00-00' && $row_result[csf('invoice_date')] != '') { echo change_date_format($row_result[csf('invoice_date')]); } else { echo '&nbsp;'; } ?></p>
	                        </td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;
	                        	<? if($row_result[csf('insert_date')] != '0000-00-00' && $row_result[csf('insert_date')] != '') { echo change_date_format($row_result[csf('insert_date')]); } else { echo '&nbsp;'; } ?></p>
	                        </td>
	                        <td width="80" class="break-all"><p><? echo $shipment_mode[$row_result[csf('shipping_mode')]]; ?></p></td>
	                        <td width="70" align="center" class="break-all"><p>
	                        	<? if($row_result[csf('type')] == 1) echo 'LC'; else echo 'SC'; ?></p>
							</td>
	                        <td width="100" class="break-all"><p><? echo $row_result[csf('lc_sc_no')]; ?></p></td>



	                        <td width="70" class="break-all"><p><? echo  $buyer_arr[$row_result[csf('buyer_id')]]; ?></p></td>
	                        <td width="100" class="break-all"><p><? echo $supplier_arr[$row_result[csf('forwarder_name')]]; ?></p></td>
	                        <td width="150" class="break-all"><p><? echo $bank_arr[$row_result[csf('lien_bank')]]; ?></p></td>
	                        <td width="90" class="break-all"><p><? echo $row_result[csf('exp_form_no')].'&nbsp;'; ?></p></td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;<? if($row_result[csf('exp_form_date')] != '0000-00-00' && $row_result[csf('exp_form_date')] != '') { echo change_date_format($row_result[csf('exp_form_date')]); } else { echo '&nbsp;'; } ?></p></td>
	                        <td width="100" align="right" class="break-all"><p>
	                        	<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row_result[csf('country_id')]; ?>','<? echo $row_result[csf('id')]; ?>','550px')"><? echo number_format($exfact_qnty_arr[$row_result[csf('id')]],2); ?></a></p>
	                        </td>
	                        <td width="100" align="right" class="break-all"><p><a href='#report_detals' onclick= "openmypage('<? echo $id; ?>','<? echo $k; ?>')"><? echo number_format($row_result[csf('invoice_quantity')],2); ?></a></p></td>
	                        <td width="100" align="right" class="break-all"><p><? echo number_format($inv_qnty_pcs_arr[$row_result[csf('id')]],2); ?></p></td>
	                        <td width="80" align="right" class="break-all"><p><? echo number_format($row_result[csf('total_carton_qnty')],2); ?></p></td>
	                        <td width="80" align="right" class="break-all"><p>
	                        	<? $avg_price=$row_result[csf('invoice_value')]/$row_result[csf('invoice_quantity')];  
	                        	echo number_format($avg_price,2); ?></p></td>


	                        <td width="100" align="right" class="break-all"><p><? echo number_format($row_result[csf('invoice_value')],2,'.',''); ?></p></td>
	                        <td width="80" align="right" class="break-all"><p><? echo number_format($row_result[csf('discount_ammount')],2,'.',''); ?></p></td>
	                        <td width="70" align="right" class="break-all"><p><? echo number_format($row_result[csf('bonus_ammount')],2,'.',''); ?></p></td>
	                        <td width="70" align="right" class="break-all"><p><? echo number_format($row_result[csf('claim_ammount')],2,'.',''); ?></p></td>
	                        <td width="80" align="right" class="break-all"><p><? echo number_format($row_result[csf('commission')],2,'.',''); ?></p></td>
	                        <td width="80" align="right" class="break-all"><p><? echo number_format($row_result[csf('other_discount_amt')],2,'.',''); ?></p></td>
	                        <td width="80" align="right" class="break-all"><p><? echo number_format($row_result[csf('upcharge')],2,'.',''); ?></p></td>
	                        <td width="100" align="right" class="break-all"><p><? echo number_format($row_result[csf('net_invo_value')],2,'.',''); ?></p></td>
	                        <td width="80" align="center" class="break-all"><p><? echo $currency[$row_result[csf('currency_name')]]; ?></p></td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;<? if($row_result[csf('etd')] != '0000-00-00' && $row_result[csf('etd')] != '') echo change_date_format( $row_result[csf('etd')]); else echo ''; ?></p></td>


	                        <td width="70" align="center" class="break-all"><p>&nbsp;<? if($row_result[csf('ex_factory_date')] != '0000-00-00' && $row_result[csf('ex_factory_date')] != '') { echo change_date_format($row_result[csf('ex_factory_date')]); } else { echo '&nbsp;';} ?></p></td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;<? if($row_result[csf('actual_shipment_date')] !='0000-00-00' && $row_result[csf('actual_shipment_date')] != '') { echo change_date_format($row_result[csf('actual_shipment_date')]); } else { echo '&nbsp;'; } ?></p></td>	                        
	                        <td width="70" align="center" class="break-all" title="Ex-factory Date+Variable Standard Date"><p>&nbsp;<? if($possiable_bl_date != '0000-00-00' && $possiable_bl_date != '') { echo change_date_format($possiable_bl_date); } else { echo '&nbsp;'; } ?></p></td>	
	                        <td width="100" class="break-all"><p><? echo $row_result[csf('bl_no')]; ?></p></td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;<? if($row_result[csf('bl_date')] != '0000-00-00' && $row_result[csf('bl_date')] != '') { echo change_date_format($row_result[csf('bl_date')]); } else { echo '&nbsp;'; } ?></p></td>
	                        <td width="70" title="exfactory date-bl date" align="center" class="break-all"><p><? $diff_bl=datediff("d",$exfact_date_calculate, $row_result[csf('bl_date')]); if($diff_bl>0) echo $diff_bl.'days'; ?></p></td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;<? if($row_result[csf('bl_rev_date')] != '0000-00-00' && $row_result[csf('bl_rev_date')] != '') { echo change_date_format($row_result[csf('bl_rev_date')]); } else { echo '&nbsp;'; } ?></p></td>
							<td width="70" align="center" class="break-all"><p><? echo $row_result[csf('shipping_bill_n')]; ?></p></td>
							<td width="70" align="center" class="break-all"><p>&nbsp;<?  echo change_date_format($row_result[csf('ship_bl_date')]); ?></p></td>
	                        <td width="70" align="center" class="break-all" title="BL Date+Variable Standard Date"><p>&nbsp;<? if($possiable_gsp_date != '0000-00-00' && $possiable_gsp_date != '') { echo change_date_format($possiable_gsp_date); } else { echo '&nbsp;'; } ?></p></td>


	                        <td width="70" align="center" class="break-all"><p>&nbsp;
	                        	<? if(trim($row_result[csf('gsp_co_no_date')]) != '0000-00-00' && trim($row_result[csf('gsp_co_no_date')]) != '') { echo change_date_format($row_result[csf('gsp_co_no_date')]); } else { echo '&nbsp;'; } ?></p>
	                        </td>
	                        <td width="100" class="break-all"><p><? echo $row_result[csf('gsp_co_no')]; ?></p></td>
	                        <td width="70" align="center" class="break-all" title="BL Date+Variable Standard Date">&nbsp;<? if($possiable_co_date != '0000-00-00' && $possiable_co_date != '') { echo change_date_format($possiable_co_date); } else { echo '&nbsp;'; } ?></p></td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;<? if($row_result[csf('co_date')] != '0000-00-00' && $row_result[csf('co_date')] != '') { echo change_date_format($row_result[csf('co_date')]); } else { echo '&nbsp;'; } ?></p></td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;
	                        	<?
								$curier_receipt_date=$bank_sub_data[$row_result[csf('id')]]['courier_date'];
		                        if(!(trim($curier_receipt_date) == '0000-00-00' || trim($curier_receipt_date) ==''))
		                        {
		                            echo change_date_format($curier_receipt_date);
		                        }
		                        else
		                        {
		                            echo '&nbsp;';
		                        } ?></p>
		                    </td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;<? if($row_result[csf('ic_recieved_date')] != '0000-00-00' && $row_result[csf('ic_recieved_date')] != '') echo change_date_format( $row_result[csf('ic_recieved_date')]); else echo ''; ?></p></td>
	                        <td width="70" title="exfactory date-bank submission date/current date" align="center" class="break-all"><p>&nbsp;
		                        <?
		                        if(($exfact_date_calculate != '0000-00-00') )
		                        {
		                            $current_date=date("Y-m-d");
		                            if($bank_sub_data[$row_result[csf('id')]]["submit_date"] == '0000-00-00' || $bank_sub_data[$row_result[csf('id')]]["submit_date"] == '')
		                            {
		                            	$diff=datediff("d",$exfact_date_calculate, $current_date);
		                            }
		                            else
		                            {
		                            	$diff=datediff("d",$exfact_date_calculate, $bank_sub_data[$row_result[csf('id')]]['submit_date']);
		                            }
		                        }
		                        else
		                        {
		                            $diff='';
		                        }
		                        if($diff>0) echo  $diff.' days';
		                        ?></p>
	                        </td>
	                        <td width="70" align="center" class="break-all" title="BL Date+Document Presentation Days"><p>&nbsp;<? if($possiable_buyer_sub_date != '0000-00-00' && $possiable_buyer_sub_date != '') {echo change_date_format($possiable_buyer_sub_date); } else { echo '&nbsp;';} ?></p></td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;
	                        	<?
	                            if(trim($buyer_submit_date_arr[$row_result[csf('id')]]) != '0000-00-00' && trim($buyer_submit_date_arr[$row_result[csf('id')]]) != '') echo change_date_format(trim($buyer_submit_date_arr[$row_result[csf('id')]])); else echo '&nbsp;';
	                        	?></p>
	                    	</td>
	                        <td width="70" title="From BL Date To Buyer Submission Date" align="center" class="break-all"><p>
								<?
								$diff_buyer_sub=0;
								if($row_result[csf('bl_date')] != '0000-00-00' && $row_result[csf('bl_date')] != '')
								{
									$diff_buyer_sub=datediff("d",$row_result[csf('bl_date')], $buyer_submit_date_arr[$row_result[csf('id')]]);
								}
								else
								{
									$diff_buyer_sub=0;
								}
								 if($diff_buyer_sub>0) echo $diff_buyer_sub.'days';
								?></p>
							</td>


	                        <td width="70" align="center" class="break-all" title="BL Date+Document Presentation Days"><p>&nbsp;<? if($possiable_bank_sub_date != '0000-00-00' && $possiable_bank_sub_date != '') { echo change_date_format($possiable_bank_sub_date); } else { echo '&nbsp;'; } ?></p></td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;
							<?
	                            if(trim($bank_sub_data[$row_result[csf('id')]]['submit_date']) != '0000-00-00' && trim($bank_sub_data[$row_result[csf('id')]]['submit_date']) != '') echo change_date_format(trim($bank_sub_data[$row_result[csf('id')]]['submit_date'])); else echo '&nbsp;'; ?></p>
	                    	</td>
	                        <td width="70" title="From BL Date To Bank Submission Date" align="center" class="break-all"><p>
								<?
								$diff_sub=0;
								if($row_result[csf('bl_date')] != '0000-00-00' && $row_result[csf('bl_date')] != '')
								{
									$diff_sub=datediff("d",$row_result[csf('bl_date')], $bank_sub_data[$row_result[csf('id')]]['submit_date']);
								}
								else
								{
									$diff_sub=0;
								}
								if($diff_sub>0) echo $diff_sub.'days';
								?></p>
							</td>
	                        <td width="100" class="break-all"><p><? echo $bank_sub_data[$row_result[csf('id')]]['bank_ref_no']; ?></p></td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;
	                        	<?
		                        if(!(trim($bank_sub_data[$row_result[csf('id')]]['submit_date']) == '0000-00-00' || trim($bank_sub_data[$row_result[csf('id')]]['submit_date']) == ''))
		                        {
		                           echo change_date_format($bank_sub_data[$row_result[csf('id')]]['submit_date']);
		                        }
		                        else
		                        {
		                            echo '&nbsp;';
		                        }
		                        ?></p>
		                    </td>
	                        <td width="70" class="break-all"><p><? echo $row_result[csf('shipping_bill_n')]; ?></p></td>
							<td width="70" class="break-all"><p>&nbsp;<? if($row_result[csf('ship_bl_date')] != '0000-00-00' && $row_result[csf('ship_bl_date')] != '') { echo change_date_format($row_result[csf('ship_bl_date')]); } else { echo '&nbsp;'; } ?></p></td>
							<td width="70" class="break-all"><p><? echo $row_result[csf('carton_gross_weight')]; ?></p>
							</td>
							<td width="70" class="break-all"><p><? echo $row_result[csf('carton_net_weight')]; ?></p></td>
	                        <td width="80" class="break-all"><p><? echo $pay_term[$row_result[csf('pay_term')]]; ?></p></td>


	                        <td width="80" class="break-all"><p><? echo $row_result[csf('tenor')]; ?></p></td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;
		                        <?
								if(!(trim($bank_sub_data[$row_result[csf('id')]]['possible_reali_date']) == '0000-00-00' || trim($bank_sub_data[$row_result[csf('id')]]['possible_reali_date']) == ''))
								{
									echo change_date_format($bank_sub_data[$row_result[csf('id')]]['possible_reali_date']);
								}
								else
								{
									echo '&nbsp;';
								}
		                        ?></p>
	                        </td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;
		                        <?
								if(!(trim($rlz_date_arr[$row_result[csf('id')]]) == '0000-00-00' || trim($rlz_date_arr[$row_result[csf('id')]]) == ''))
								{
									echo change_date_format($rlz_date_arr[$row_result[csf('id')]]);
								}
								else
								{
									echo '&nbsp;';
								}
		                        ?></p>
	                        </td>
	                        <td width="70" align="center" class="break-all" title="From Bank or Buyer Submit Date To Actual Realization Date"><p>
								<?
								if( $realization_sub_day!="" && $realization_sub_day!='0000-00-00' && $rlz_date_arr[$row_result[csf('id')]] != '' && $rlz_date_arr[$row_result[csf('id')]] != '0000-00-00')
								{
									$diff_rlz=datediff("d",$realization_sub_day,$rlz_date_arr[$row_result[csf('id')]]);
								}
								if($diff_rlz>0) echo $diff_rlz.' days';
								?></p>
							</td>
	                        <td width="70" align="right" class="break-all"><p>
	                        	<? if(!(trim($rlz_date_arr[$row_result[csf('id')]]) == '0000-00-00' || trim($rlz_date_arr[$row_result[csf('id')]]) == ''))
								{
									echo number_format($row_result[csf('net_invo_value')],2,'.',''); $total_rlz_amt+=$row_result[csf('net_invo_value')]; $sub_total_rlz_amt+=$row_result[csf('net_invo_value')];
								}
								else
								{
									echo '';
								}
								?></p>
							</td>
	                        <td width="100" class="break-all"><p><? echo $row_result[csf('remarks')]; ?></p></td>
	                        <td width="100" class="break-all"><p><? echo $row_result[csf('feeder_vessel')]; ?></p></td>
	                        <td width="100" class="break-all"><p><? echo $row_result[csf('mother_vessel')]; ?></p></td>
	                        <td width="70" align="center" class="break-all"><p>&nbsp;<? if($row_result[csf('etd_destination')] != '0000-00-00' && $row_result[csf('etd_destination')] != '') { echo change_date_format($row_result[csf('etd_destination')]); } else { echo '&nbsp;';} ?></p></td>
	                        <td><p><? echo $bank_sub_data[$row_result[csf('id')]]['bnk_to_bnk_cour_no']; ?></p></td>
						</tr>
						<?
						$k++;
						$sub_total_ex_fact_qnty+=$exfact_qnty_arr[$row_result[csf('id')]];
						$sub_total_invoice_qty +=$row_result[csf('invoice_quantity')];
						$sub_total_invoice_qty_pcs +=$inv_qnty_pcs_arr[$row_result[csf('id')]];
						$sub_total_carton_qty +=$row_result[csf('total_carton_qnty')];
						$sub_total_avg_price +=$avg_price; 
						$sub_total_grs_value +=$row_result[csf('invoice_value')];
						$sub_total_discount_value +=$row_result[csf('discount_ammount')];
						$sub_total_bonous_value +=$row_result[csf('bonus_ammount')];
						$sub_total_claim_value +=$row_result[csf('claim_ammount')];
						$sub_total_commission_value +=$row_result[csf('commission')];
						$sub_total_other_discount_value +=$row_result[csf('other_discount_amt')];
						$sub_total_upcharge_value +=$row_result[csf('upcharge')];
						$sub_total_order_qnty +=$row_result[csf('net_invo_value')];
						$sub_total_carton_gross_weight += $row_result[csf('carton_gross_weight')];
						$sub_total_carton_net_weight += $row_result[csf('carton_net_weight')];

						$total_ex_fact_qnty+=$exfact_qnty_arr[$row_result[csf('id')]];
						$total_invoice_qty +=$row_result[csf('invoice_quantity')];
						$total_invoice_qty_pcs +=$inv_qnty_pcs_arr[$row_result[csf('id')]];	
						$total_carton_qty +=$row_result[csf('total_carton_qnty')];
						$total_avg_price +=$avg_price;
						$total_grs_value +=$row_result[csf('invoice_value')];
						$total_discount_value +=$row_result[csf('discount_ammount')];	  
						$total_bonous_value +=$row_result[csf('bonus_ammount')];
						$total_claim_value +=$row_result[csf('claim_ammount')];
						$total_commission_value +=$row_result[csf('commission')];
						$total_other_discount_value +=$row_result[csf('other_discount_amt')];	 
						$total_upcharge_value +=$row_result[csf('upcharge')];
						$total_order_qnty +=$row_result[csf('net_invo_value')];
						$total_carton_gross_weight += $row_result[csf('carton_gross_weight')];			
						$total_carton_net_weight += $row_result[csf('carton_net_weight')];
						
	                }
	                ?>
	                <!-- Enter after every 10 rows -->
	                <!-- <tr bgcolor="yellow">
                        <td width="30">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="50">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="100">&nbsp;</td>

                        <td width="70">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="150">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="70" align="right"><strong>Sub Total:</strong></td>
                        <td width="100" align="right"><? echo number_format($sub_total_ex_fact_qnty,2); ?></td>
                        <td width="100" align="right"><? echo number_format($sub_total_invoice_qty,2); ?></td>
                        <td width="100" align="right"><? echo number_format($sub_total_invoice_qty_pcs,2); ?></td>
                        <td width="80" align="right"><? echo number_format($sub_total_carton_qty,2); ?></td>
                        <td width="80" align="right"><? echo number_format($sub_total_avg_price,2);  ?></td>


                        <td width="100" align="right"><? echo number_format($sub_total_grs_value,2);  ?></td>
                        <td width="80" align="right"><? echo number_format($sub_total_discount_value,2);  ?></td>
                        <td width="70" align="right"><? echo number_format($sub_total_bonous_value,2);  ?></td>
                        <td width="70" align="right"><? echo number_format($sub_total_claim_value,2);  ?></td>
                        <td width="80" align="right"><? echo number_format($sub_total_commission_value,2);  ?></td>
                        <td width="80"  align="right"><? echo number_format($sub_total_other_discount_value,2); ?></td>
                        <td width="80"  align="right"><? echo number_format($sub_total_upcharge_value,2);?></td>
                        <td width="100" align="right"><? echo number_format($sub_total_order_qnty,2);  ?></td>
                        <td width="80">&nbsp;</td>
                        <td width="70">&nbsp;</td>

                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>	                        
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>

                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>

                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70"><? echo number_format($sub_total_carton_gross_weight,2);  ?></td>
                        <td width="70"><? echo number_format($sub_total_carton_net_weight,2);  ?></td>
                        <td width="80">&nbsp;</td>

                        <td width="80">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70" align="right"><? echo number_format($sub_total_rlz_amt,2);  ?></td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td >&nbsp;</td>
                    </tr> -->
					</tbody>
	            </table>
	     		<table width="5580" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
	            	<tfoot>
	                    <tr>
	                        <th width="30">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>

	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="150">&nbsp;</th>
	                        <th width="90">&nbsp;</th>
	                        <th width="70" align="right"><strong>Total:</strong></th>
	                        <th width="100" id="total_ex_fact_qnty" align="right"><? echo number_format($total_ex_fact_qnty,2); ?></th>
	                        <th width="100" id="total_invoice_qty" align="right"><? echo number_format($total_invoice_qty,2); ?></th>
	                        <th width="100" id="total_invoice_qty_pcs" align="right"><? echo number_format($total_invoice_qty_pcs,2); ?></th>
	                        <th width="80" id="total_carton_qty" align="right"><? echo number_format($total_carton_qty,2); ?></th>
	                        <th width="80" id="value_total_avg_price"  align="right"><? echo number_format($total_avg_price,2);  ?></th>

	                        <th width="100" id="value_total_grs_value"  align="right"><? echo number_format($total_grs_value,2);  ?></th>
	                        <th width="80" id="value_total_discount_value"  align="right"><? echo number_format($total_discount_value,2);  ?></th>
	                        <th width="70" id="value_total_bonous_value"  align="right"><? echo number_format($total_bonous_value,2);  ?></th>
	                        <th width="70" id="value_total_claim_value"  align="right"><? echo number_format($total_claim_value,2);  ?></th>
	                        <th width="80" id="value_total_commission_value"  align="right"><? echo number_format($total_commission_value,2);  ?></th>
	                        <th width="80"  align="right"><? echo number_format($total_other_discount_value,2); ?></th>
	                        <th width="80"  align="right"><? echo number_format($total_upcharge_value,2);?></th>
	                        <th width="100"  id="value_total_net_invo_value"  align="right"><? echo number_format($total_order_qnty,2);  ?></th>
	                        <th width="80">&nbsp;</th>
	                        <th width="70">&nbsp;</th>

	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>	                        
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>

	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>

	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70"><? echo number_format($total_carton_gross_weight,2);  ?></th>
	                        <th width="70"><? echo number_format($total_carton_net_weight,2);  ?></th>
	                        <th width="80">&nbsp;</th>

	                        <th width="80">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70"  id="value_total_rlz_amt"  align="right"><? echo number_format($total_rlz_amt,2);  ?></th>
	                        <th width="100">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th >&nbsp;</th>
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
		echo "$total_data****".$RptType."****".$filename;
	}	
	exit();
}

if($action==='show_file')
{
	echo load_html_head_contents("File","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$data_array=sql_select("select image_location ,real_file_name from common_photo_library  where master_tble_id='$invoice_id' and form_name='export_invoice' and is_deleted=0 and file_type=2");
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        { 
        	$image_location=explode(".",$row[csf('image_location')]);
        	$file_name=$row[csf('real_file_name')];
	        ?>
	        <td><a style="display: block; overflow: hidden; width: 80px; float: left;text-align: center;" href="../../../<? echo $row[csf('image_location')]; ?>" target="_new"> 
	        	<?

	        		if($image_location[1]=='xls')
	        		{
		        		?>
		        		<img src="../../../file_upload/Excel-icon.png" width="80" height="60"><br> <? echo $file_name; ?> </a>
		        		<?
	        		}
	        		else if($image_location[1]=='doc' || $image_location[1]=='docx')
	        		{
		        		?>
		        		<img src="../../../file_upload/docx-icon.png" width="80" height="60"><br> <? echo $file_name; ?> </a>
		        		<?
	        		}
	        		else
	        		{
		        		?>
		        		<img src="../../../file_upload/blank_file.png" width="80" height="60"><br> <? echo $file_name; ?>  </a>
		        		<?
	        		}
	        	?>
	        </td>
	        <?
        }
        ?>
        </tr>
    </table>
    <?
}

if($action==='po_id_details')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);
	$invoice_id=str_replace("'","",$invoice_id);
	//print_r($po_id);die;
	?>

	<div style="width:970px">
	<fieldset style="width:100%"  >
	    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="970">
	        <thead>
	            <th width="120">Order NO</th>
	            <th width="120">Style No</th>
	            <th width="80">Ship Date</th>
	            <th width="80">Order Qty (Pcs)</th>
	            <th width="70">Unit Price (P.O)</th>
	            <th width="80">Order Value ($)</th>
	            <th width="80">Attach Qnty (Pcs)</th>
	            <th width="70">Unit Price (LC/SC)</th>
	            <th width="80">Attach Value ($)</th>
	            <th width="80">Invoice Qnty (Pcs)</th>
	            <th width="70">Unit Price (Invoice)</th>
	            <th width="80">Invoice Value ($)</th>
	            <th width="80">Unit Price Diff. (LC-Inv)</th>
	        </thead>
	        <tbody>
			<?
			$lc_attach_sql=sql_select("select b.com_export_lc_id as lc_sc_id, b.wo_po_break_down_id, b.attached_qnty, b.attached_rate, b.attached_value, 1 as type from com_export_lc_order_info b where b.status_active=1 and b.is_deleted=0
			union all
			select b.com_sales_contract_id as lc_sc_id, b.wo_po_break_down_id, b.attached_qnty, b.attached_rate, b.attached_value, 2 as type from  com_sales_contract_order_info b where b.status_active=1 and b.is_deleted=0");
			$lc_sc_qnty_arr=array();
			foreach($lc_attach_sql as $row)
			{
				$lc_sc_qnty_arr[$row[csf('lc_sc_id')]][$row[csf('type')]][$row[csf('wo_po_break_down_id')]]["attached_qnty"]=$row[csf('attached_qnty')];
				$lc_sc_qnty_arr[$row[csf('lc_sc_id')]][$row[csf('type')]][$row[csf('wo_po_break_down_id')]]["attached_rate"]=$row[csf('attached_rate')];
				$lc_sc_qnty_arr[$row[csf('lc_sc_id')]][$row[csf('type')]][$row[csf('wo_po_break_down_id')]]['attached_value']=$row[csf('attached_value')];
			}
			$sql="select a.lc_sc_id, a.is_lc, b.current_invoice_qnty, b.current_invoice_rate, b.current_invoice_value, c.id as po_id, c.po_number, c.pub_shipment_date, c.po_quantity, c.unit_price, c.po_total_price,d.style_ref_no from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b, wo_po_break_down c ,wo_po_details_master d where a.id=b.mst_id and b.po_breakdown_id=c.id and  c.job_no_mst = d.job_no  and a.id='$invoice_id' and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			//echo $sql;
			$i=$k+1;
	        $sql_re=sql_select($sql);
	        $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;
	        $result=0;
	        foreach($sql_re as $row)
	        {
	        ?>

	            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                <td><? echo $row[csf('po_number')]; ?>&nbsp;</td>
	                <td><? echo $row[csf('style_ref_no')]; ?>&nbsp;</td>
	                <td align="center"><? if($row[csf('pub_shipment_date')]!='' && $row[csf('pub_shipment_date')]!="0000-00-00") echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
	                <td align="right"><?  echo number_format($row[csf('po_quantity')],0);  $total_order_qty +=$row[csf('po_quantity')]; ?> </td>
	                <td align="right"><?  echo number_format($row[csf('unit_price')],4);  ?> </td>
	                <td align="right"><?  echo number_format($row[csf('po_total_price')],2); $total_order_value+=$row[csf('po_total_price')]; ?></td>

	                <td align="right"><?  echo number_format($lc_sc_qnty_arr[$row[csf('lc_sc_id')]][$row[csf('is_lc')]][$row[csf('po_id')]]['attached_qnty'],0);  $total_lc_qty +=$lc_sc_qnty_arr[$row[csf('lc_sc_id')]][$row[csf('is_lc')]][$row[csf('po_id')]]['attached_qnty']; ?> </td>
	                <td align="right"><?  echo number_format($lc_sc_qnty_arr[$row[csf('lc_sc_id')]][$row[csf('is_lc')]][$row[csf('po_id')]]['attached_rate'],4);  ?> </td>
	                <td align="right"><?  echo number_format($lc_sc_qnty_arr[$row[csf('lc_sc_id')]][$row[csf('is_lc')]][$row[csf('po_id')]]['attached_value'],2); $total_lc_value+=$lc_sc_qnty_arr[$row[csf('lc_sc_id')]][$row[csf('is_lc')]][$row[csf('po_id')]]['attached_value']; ?></td>
	                <td align="right"><?  echo number_format($row[csf('current_invoice_qnty')],0);  $total_invoice_qty +=$row[csf('current_invoice_qnty')]; ?> </td>
	                <td align="right"><?  echo number_format($row[csf('current_invoice_rate')],4);  ?> </td>
	                <td align="right"><?  echo number_format($row[csf('current_invoice_value')],2); $total_invoice_value+=$row[csf('current_invoice_value')]; ?></td>
	            	<td align="right" title="LC Rate-Invoice Rate"><? $rate_dev=$lc_sc_qnty_arr[$row[csf('lc_sc_id')]][$row[csf('is_lc')]][$row[csf('po_id')]]['attached_rate']-$row[csf('current_invoice_rate')];  echo number_format($rate_dev,4);  ?> </td>
	            </tr>
			<?
			$i++;
	        }
	        ?>
	        </tbody>
	        <tfoot>
	            <tr >
	                <th align="right">&nbsp;</th>
	                <th align="right">&nbsp;</th>
	                <th align="right" >Total</th>
	                <th align="right"><? echo number_format($total_order_qty,0); ?></th>
	                <th align="right">&nbsp;</th>
	                <th align="right"><? echo number_format($total_order_value,2); ?></th>
	                <th align="right"><? echo number_format($total_lc_qty,0); ?></th>
	                <th align="right">&nbsp;</th>
	                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
	                <th align="right"><? echo number_format($total_invoice_qty,0); ?></th>
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
//Ex-Factory Delv. and Return
if($action==='ex_factory_popup')
{
 	echo load_html_head_contents('Ex-Factory Details', '../../../', 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px">
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="">Ex-Fact.Return Qty.</th>

                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;

				$exfac_sql=("SELECT b.challan_no,a.sys_number,b.ex_factory_date,
				CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
				from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.invoice_no in($id) ");
                $sql_dtls=sql_select($exfac_sql);

                foreach($sql_dtls as $row_real)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td>
                        <td width="90"><? echo change_date_format($row_real[csf('ex_factory_date')]); ?></td>
                        <td width="120"><? echo $row_real[csf('sys_number')]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf('ex_factory_qnty')]; ?></td>
                         <td width="" align="right"><? echo $row_real[csf('ex_factory_return_qnty')]; ?></td>
                    </tr>
                    <?
                    $rec_qnty+=$row_real[csf('ex_factory_qnty')];
					 $rec_return_qnty+=$row_real[csf('ex_factory_return_qnty')];
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><? echo number_format($rec_qnty,2); ?></th>
                    <th><? echo number_format($rec_return_qnty,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div>
		</fieldset>
	</div>
	<?
    exit();
}
disconnect($con);
?>
