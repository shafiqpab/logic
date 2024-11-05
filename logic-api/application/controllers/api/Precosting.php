<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//session_start();
$db_type=2;
function csf($data)								// checked 3
{
	global $db_type;
	if ($db_type==0 || $db_type==1 )  return strtolower($data); else return strtoupper($data);
}
/**
 * @package    CodeIgniter - Android Apps
 * @category   AndroidApps
 * @author     Jahid Hasan <jahid@logicsoftbd.com>
 * @copyright  2017 Logic Software Ltd Development Group
 */
require APPPATH . '/libraries/REST_Controller.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/platform-version-3.1/includes/db_functions_oracle.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/platform-version-3.1/includes/common_functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/platform-version-3.1/includes/class3/class.conditions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/platform-version-3.1/includes/class3/class.reports.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/platform-version-3.1/includes/class3/class.yarns.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/platform-version-3.1/includes/class3/class.conversions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/platform-version-3.1/includes/class3/class.trims.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/platform-version-3.1/includes/class3/class.emblishments.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/platform-version-3.1/includes/class3/class.commisions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/platform-version-3.1/includes/class3/class.commercials.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/platform-version-3.1/includes/class3/class.others.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/platform-version-3.1/includes/class3/class.washes.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/platform-version-3.1/includes/class3/class.fabrics.php');

class Precosting extends REST_Controller {
	public $pc_date_time;
	function __construct() {
		parent::__construct();
		//$this->load->model('salesorder_model');
		$this->pc_date_time = date("d-M-Y h:i:s A");
	}
	
	function procost_get()
    {
			
		$fromDate="";
		$toDate="";
		if($this->get('from_date') !="" && $this->validateDate($this->get('from_date')) && $this->validateDate($this->get('to_date')) && $this->get('to_date') !=""){
			$fromDate=$this->get('from_date');
		    $toDate=$this->get('to_date');
		}
		else if($this->validateDate($this->get('from_date')) && !$this->validateDate($this->get('to_date'))){
			$fromDate=$this->get('from_date');
		    $toDate=$this->get('from_date');
		}
		else if(!$this->validateDate($this->get('from_date')) && $this->validateDate($this->get('to_date'))){
			$fromDate=$this->get('to_date');
		    $toDate=$this->get('to_date');
		}
		
		if ($fromDate=="" && $toDate == "" && $this->get('program_no')== "") 
		{
			$this->response('Date Range or Program No Is Required', 400);
		}
		if($this->compareDate($fromDate,$toDate)===0)
		{
			$this->response('To date is greater than From date', 400);
		}
		
		
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
		$cbo_company_name=1;
		$txt_job_no='FAL-18-00072';
		$cbo_year='2018';
		$g_exchange_rate='80';
		$db_type=2;
		$company_name=str_replace("'","",$cbo_company_name);
		$job_no=str_replace("'","",$txt_job_no);
		$cbo_year=str_replace("'","",$cbo_year);
		$g_exchange_rate=str_replace("'","",$g_exchange_rate);
		if($db_type==0){ 
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		if($db_type==2) {
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
		$check_data=sql_select("select a.job_no from wo_po_details_master a,production_logicsoft d,wo_pre_cost_mst f where  a.job_no=d.jobNo and  a.job_no=f.job_no  and  f.job_no=d.jobNo  and a.company_name=$company_name and f.entry_from=158 and a.job_no like '$job_no' $year_cond group by a.job_no","","","","");
		$chk_job_nos='';
		foreach($check_data as $row)
		{
			$chk_job_nos=$row[csf('job_no')];
		}
		if($chk_job_nos!='')
		{
		$sql="select a.job_no_prefix_num, a.job_no,  a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv,a.currency_id, a.quotation_id,b.id, b.po_number,b.grouping,b.file_no, b.shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status,c.item_number_id,c.order_quantity , c.order_rate,c.order_total , c.plan_cut_qnty,c.shiping_status  as shiping_status_c from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and  a.job_no=c.job_no_mst and b.id=c.po_break_down_id  and a.company_name='$company_name' and a.job_no like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $year_cond order by  b.id";
		$result=sql_select($sql,"","","","");
		}
	/*	 $sql="select a.job_no_prefix_num, a.job_no,  a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv,a.currency_id, a.quotation_id,b.id, b.po_number,b.grouping,b.file_no, b.shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status,c.item_number_id,c.order_quantity , c.order_rate,c.order_total , c.plan_cut_qnty,c.shiping_status  as shiping_status_c from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,production_logicsoft d where a.job_no=b.job_no_mst and  a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  a.job_no=d.jobNo  and c.job_no_mst=d.jobNo and b.job_no_mst=d.jobNo  and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $year_cond order by  b.id";
		 */
		$jobNumber="";$po_ids="";
		$buyerName="";
		$styleRefno="";
		$uom="";
		$totalSetQnty="";
		$currencyId="";
		$quotationId="";
		$poNumberArr=array();
		$poIdArr=array();
		$poQtyArr=array();
		$poPcutQtyArr=array();
		$poValueArr=array();
		$ShipDateArr=array();
		$gmtsItemArr=array();
		$shipingStatus="";
		
		foreach($result as $row){
			$jobNumber=$row[csf('job_no')];
			$buyerName=$row[csf('buyer_name')];
			$styleRefno=$row[csf('style_ref_no')];
			$uom=$row[csf('order_uom')];
			$totalSetQnty=$row[csf('ratio')];
			$currencyId=$row[csf('currency_id')];
			$quotationId=$row[csf('quotation_id')];
			$poNumberArr[$row[csf('id')]]=$row[csf('po_number')];
			$poIdArr[$row[csf('id')]]=$row[csf('id')];
			if(!empty($poQtyArr[$row[csf('id')]]))
			{
				$poQtyArr[$row[csf('id')]]+=$row[csf('order_quantity')];
			}
			else
			{
				$poQtyArr[$row[csf('id')]]=$row[csf('order_quantity')];
			}
			
			if(!empty($poPcutQtyArr[$row[csf('id')]]))
			{
				$poPcutQtyArr[$row[csf('id')]]+=$row[csf('plan_cut_qnty')];
			}
			else
			{
				$poPcutQtyArr[$row[csf('id')]]=$row[csf('plan_cut_qnty')];
			}
			
			if(!empty($poValueArr[$row[csf('id')]]))
			{
				$poValueArr[$row[csf('id')]]+=$row[csf('order_total')];
			}
			else
			{
				$poValueArr[$row[csf('id')]]=$row[csf('order_total')];
			}
			
			
			$ShipDateArr[$row[csf('id')]]=date("d-m-Y",strtotime($row[csf('shipment_date')]));
			$gmtsItemArr[$row[csf('item_number_id')]]=$row[csf('item_number_id')];
			$shipingStatus=$row[csf('shiping_status')];
			$po_ids.=$row[csf('id')].',';
		}
		if($jobNumber=="") 
		{ 
			echo "<div style='width:1000px;font-size:larger'; align='center'><font color='#FF0000'>No Data Found</font></div>"; die;
		}
		$jobPcutQty=array_sum($poPcutQtyArr);
		$jobQty=array_sum($poQtyArr);
		$jobValue=array_sum($poValueArr);
		$unitPrice=$jobValue/$jobQty;
		$po_cond_for_in="";
		if(count($poIdArr)>0){
			$po_cond_for_in=" and  po_break_down_id  in(".implode(",",$poIdArr).")"; 
		}else{
			$po_cond_for_in="";
		}
		$exfactoryQtyArr=array();
		$exfactory_data=sql_select("select po_break_down_id,
			sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty 
			from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id","","","","");
		foreach($exfactory_data as $exfatory_row){
			$exfactoryQtyArr[$exfatory_row[csf('po_break_down_id')]]=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('ex_factory_return_qnty')];
		}
		$exfactoryQty=array_sum($exfactoryQtyArr);
		$exfactoryValue=array_sum($exfactoryQtyArr)*($unitPrice);
		$shortExcessQty=array_sum($poQtyArr)-$exfactoryQty;
		$shortExcessValue=$shortExcessQty*($unitPrice);
	//$quotationId=1;
		if($quotationId){
			$quaOfferQnty=0;
			$quaConfirmPrice=0;
			$quaConfirmPriceDzn=0;
			$quaPriceWithCommnPcs=0;
			$quaCostingPer=0;
			$quaCostingPerQty=0;
			$sqlQua="select a.offer_qnty,a.costing_per,b.confirm_price,b.confirm_price_dzn,b.price_with_commn_pcs from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.id =".$quotationId." and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$dataQua=sql_select($sqlQua,"","","","");
			foreach($dataQua as $rowQua){
				$quaOfferQnty=$rowQua[csf('offer_qnty')];
				$quaConfirmPrice=$rowQua[csf('confirm_price')];
				$quaConfirmPriceDzn=$rowQua[csf('confirm_price_dzn')];
				$quaPriceWithCommnPcs=$rowQua[csf('price_with_commn_pcs')];
				$quaCostingPer=$rowQua[csf('costing_per')];
				$quaCostingPerQty=0;
				if($quaCostingPer==1){
					$quaCostingPerQty=12;
				}
				if($quaCostingPer==2){
					$quaCostingPerQty=1;
				}
				if($quaCostingPer==3){
					$quaCostingPerQty=24;
				}
				if($quaCostingPer==4){
					$quaCostingPerQty=36;
				}
				if($quaCostingPer==5){
					$quaCostingPerQty=48;
				}
			}
		}

		$job="'".$jobNumber."'";
		$condition= new condition();
		if(str_replace("'","",$job) !=''){
			$condition->job_no("=$job");
		}

		$condition->init();
		$costPerArr=$condition->getCostingPerArr();
		$costPerQty=$costPerArr[$jobNumber];
		if($costPerQty>1){
			$costPerUom=($costPerQty/12)." Dzn";
		}else{
			$costPerUom=($costPerQty/1)." Pcs";
		}
		$fabric= new fabric($condition);
		$yarn= new yarn($condition);

		$conversion= new conversion($condition);
		$trim= new trims($condition);
		$emblishment= new emblishment($condition);
		$wash= new wash($condition);
		$commercial= new commercial($condition);
		$commision= new commision($condition);
		$other= new other($condition);
	// Yarn ============================
		$totYarn=0;
		$YarnData=array();
		$yarn_data_array=$yarn->getJobCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray();
	//print_r($yarn_data_array);
		$sql_yarn="select count_id,copm_one_id,percent_one,type_id   from wo_pre_cost_fab_yarn_cost_dtls f where     f.job_no ='".$jobNumber."' and f.is_deleted=0 and f.status_active=1  group by count_id,copm_one_id,percent_one,type_id";
		$data_arr_yarn=sql_select($sql_yarn);
		foreach($data_arr_yarn as $yarn_row){
		//$yarnrate=$yarn_row[csf("rate")];
			$index="'".$yarn_row[csf("count_id")]."_".$yarn_row[csf("copm_one_id")]."_".$yarn_row[csf("percent_one")]."_".$yarn_row[csf("type_id")]."'";
			$YarnData[$index]['preCost']['qty']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['qty'];
			$YarnData[$index]['preCost']['amount']+=$yarn_data_array[$jobNumber][$yarn_row[csf("count_id")]][$yarn_row[csf("copm_one_id")]][$yarn_row[csf("percent_one")]][$yarn_row[csf("type_id")]]['amount'];
		}
		if($quotationId){
			$sql_yarn_pri="select f.id as yarn_id,f.cons_ratio,f.cons_qnty,f.rate,f.amount,count_id,copm_one_id,percent_one,type_id   from wo_pri_quo_fab_yarn_cost_dtls f where     f.quotation_id =".$quotationId." and f.is_deleted=0 and f.status_active=1  order by f.id";
			$data_arr_yarn_pri=sql_select($sql_yarn_pri);
			foreach($data_arr_yarn_pri as $yarn_row_pri){
				$yarnrate=$yarn_row_pri[csf("rate")];
		//$consQnty=($sql_yarn_pri[csf("cons_qnty")]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$consQnty=($yarn_row_pri[csf("cons_qnty")]/$quaCostingPerQty)*($quaOfferQnty);
		//echo "(".$sql_yarn_pri[csf("cons_qnty")]."/".$quaCostingPerQty.")*(".$quaOfferQnty.")";
				$amount=$consQnty*$yarnrate;
				$index="'".$yarn_row_pri[csf("count_id")]."_".$yarn_row_pri[csf("copm_one_id")]."_".$yarn_row_pri[csf("percent_one")]."_".$yarn_row_pri[csf("type_id")]."'";

				$YarnData[$index]['mkt']['qty']+=$consQnty;
				$YarnData[$index]['mkt']['amount']+=$amount;
			}
		}
	 //print_r($YarnData);
		$YarnIssue=array();
//	 $sql="select a.id,a.trans_id,a.trans_type,a.entry_form,a.po_breakdown_id,a.prod_id,a.quantity,a.issue_purpose,a.returnable_qnty,a.is_sales,b.yarn_count_id,b.yarn_comp_type1st,  b.yarn_comp_percent1st,b.yarn_type,c.cons_rate,c.cons_amount from order_wise_pro_details a, product_details_master b,inv_transaction c where a.prod_id=b.id and a.trans_id=c.id and  a.trans_type=2 and a.entry_form=3 and a.po_breakdown_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and  a.status_active=1 and b.is_deleted=0 and  b.status_active=1";

		/*$sql="select x.*,(x.quantity*x.cons_rate) cons_amount from (select  a.id,a.trans_id,a.trans_type,a.entry_form,a.po_breakdown_id,a.prod_id,a.quantity,a.issue_purpose,a.returnable_qnty,a.is_sales,b.yarn_count_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type,b.lot,(select d.cons_rate from inv_transaction d where a.trans_id=d.id)cons_rate from order_wise_pro_details a, product_details_master b,inv_transaction c where a.prod_id=b.id and a.trans_id=c.id and  a.trans_type=2 and a.entry_form=3 and a.po_breakdown_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and  b.status_active=1)x";*/
		 $sql="select x.*,(x.quantity*x.cons_rate) cons_amount from ( select a.id,a.trans_id,a.trans_type,a.entry_form,a.po_breakdown_id,a.prod_id, a.quantity,a.issue_purpose,a.returnable_qnty,a.is_sales,b.yarn_count_id,b.yarn_comp_type1st, b.yarn_comp_percent1st,b.yarn_type, c.cons_rate,d.issue_basis,d.booking_no,b.lot from order_wise_pro_details a, product_details_master b,inv_transaction c,inv_issue_master d,wo_booking_mst e where a.prod_id=b.id and a.trans_id=c.id and c.mst_id=d.id and d.booking_no=e.booking_no and a.trans_type in(2) and a.entry_form in(3) and a.po_breakdown_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and e.booking_type in(1,4) )x";
		$data_Yac=sql_select($sql);
		foreach($data_Yac as $row){
			$index="'".$row[csf("yarn_count_id")]."_".$row[csf("yarn_comp_type1st")]."_".$row[csf("yarn_comp_percent1st")]."_".$row[csf("yarn_type")]."'";
			$YarnIssue[$index]['qty']+=$row[csf("quantity")];
			$YarnIssue[$index]['amount']+=$row[csf("cons_amount")];
			$YarnIssue_avg_rate[$row[csf("lot")]]['avg_rate']+=$row[csf("cons_amount")]/$row[csf("quantity")];
			$yarn_lots.=$row[csf("lot")].',';
		}
		$yarn_lots=rtrim($yarn_lots,',');
		$yarn_lots=array_unique(explode(",",$yarn_lots));
		$lot_tmp='';
		foreach($yarn_lots as $lot)
		{
			if($lot_tmp=='') $lot_tmp="'".$lot."'";else $lot_tmp.=","."'".$lot."'";
		}
		
		//echo $lot_tmp;
		$grey_fab_array=array();  //Knitting Cost actual 
		$sql_knit_prod="select c.yarn_lot,
		 sum(CASE WHEN b.entry_form =2 and b.trans_type in(1) and a.item_category=13  THEN  b.quantity ELSE 0 END) AS grey_qnty
		  from inv_receive_master a, order_wise_pro_details b,pro_grey_prod_entry_dtls c where a.id=c.mst_id and c.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) and a.item_category in(13)  and b.trans_type in(1) and c.yarn_lot in(".$lot_tmp.") and b.po_breakdown_id in(".implode(",",$poIdArr).") group by c.yarn_lot";
		$result_knit=sql_select( $sql_knit_prod );
		$knit_grey_qty=$knit_grey_amt=0;
		foreach($result_knit as $row){
			$avg_rate=$YarnIssue_avg_rate[$row[csf("yarn_lot")]]['avg_rate']/$g_exchange_rate;
			//$grey_fab_array[$row[csf("yarn_lot")]]['knit_grey_qty']+=$row[csf("grey_qnty")];
			//$grey_fab_array[$row[csf("yarn_lot")]]['knit_grey_amt']+=$row[csf("grey_qnty")]*$avg_rate;
			//echo $row[csf("grey_qnty")].'='.$avg_rate;
			$knit_grey_qty+=$row[csf("grey_qnty")];
			$knit_grey_amt+=$row[csf("grey_qnty")]*$avg_rate;
		}
			$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master", "id", "avg_rate_per_unit"  );
			
		//echo $lot_tmp;
		//echo $knit_grey_amt; // and a.yarn_lot in(".$lot_tmp.") 
		$grey_fab_trans_array=array(); 
	$sql_grey_trans="select c.from_order_id,a.from_prod_id,
		 sum(CASE WHEN b.entry_form in(83,13) and b.trans_type in(5) THEN  b.quantity ELSE 0 END) AS grey_in,
		 sum(CASE WHEN b.entry_form in(83,13) and b.trans_type in(6) THEN  b.quantity ELSE 0 END) AS grey_out
		  from  inv_item_transfer_mst c, inv_item_transfer_dtls a, order_wise_pro_details b where c.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(83,13) and b.trans_type in(5,6) and b.po_breakdown_id in(".implode(",",$poIdArr).") group by  a.from_prod_id,c.from_order_id";//
		$result_grey_trans=sql_select( $sql_grey_trans );
		$grey_fab_trans_qty_acl=$grey_fab_trans_amt_acl=0;$from_order_id='';
		foreach ($result_grey_trans as $row)
		{
			//$grey_fab_trans_array['gtt'][$row[csf('yarn_lot')]]+=$row[csf('grey_in')]-$row[csf('grey_out')];
			$avg_rate=$avg_rate_array[$row[csf('from_prod_id')]];
			$from_order_id.=$row[csf('from_order_id')].',';
			$grey_fab_trans_qty_acl+=$row[csf('grey_in')]-$row[csf('grey_out')];
			$grey_fab_trans_amt_acl+=($row[csf('grey_in')]-$row[csf('grey_out')])*$avg_rate;
		}
		//echo $grey_fab_trans_qty_acl.'='.$grey_fab_trans_amt_acl;
		$from_order_id=rtrim($from_order_id,',');
		$from_order_ids=array_unique(explode(",",$from_order_id));
		
		$subconOutBillData="select b.order_id, 
		sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
		sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
		from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,4) and b.order_id in(".implode(",",$poIdArr).") group by b.order_id";
		$subconOutBillDataArray=sql_select($subconOutBillData);
		$tot_knit_charge=0;
		foreach($subconOutBillDataArray as $subRow)
		{
			$tot_knit_charge+=$subRow[csf('knit_bill')]/$g_exchange_rate;
			//$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']+=$subRow[csf('dye_finish_bill')]/$g_exchange_rate;
		}
		//echo $knit_grey_amt.'='.$tot_knit_charge.'='.$knit_grey_qty;
		//$grey_fab_cost=$knit_grey_amt+$tot_knit_charge;
		//$tot_grey_fab_cost_acl=($grey_fab_cost/$knit_grey_qty)*$grey_fab_trans_qty_acl;
		
		
		// Transfer Fin Actual
				$fin_fab_trans_array=array(); 
				 $sql_fin_trans="select b.trans_type, b.po_breakdown_id,b.prod_id, sum(b.quantity) as qnty,
				 	sum(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty,
					sum(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty,
				 c.from_order_id
				  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(15) and a.item_category=2 and a.transaction_type in(5,6) and b.trans_type in(5,6) and b.po_breakdown_id in(".implode(",",$poIdArr).") group by b.trans_type, b.po_breakdown_id,c.from_order_id,b.prod_id";
				$result_fin_trans=sql_select( $sql_fin_trans );
				$fin_from_order_id='';$fin_fab_trans_qty=$fin_fab_trans_amt=$fin_in_qnty=$fin_out_qnty=$fin_in_amt=$fin_out_amt=0;
				foreach ($result_fin_trans as $row)
				{
					$fin_from_order_id.=$row[csf('from_order_id')].',';
					$avg_rate=$avg_rate_array[$row[csf('prod_id')]];
					
						$fin_in_qnty+=$row[csf('in_qty')];
						$fin_out_qnty+=$row[csf('out_qty')];
						
						$fin_in_amt+=$row[csf('in_qty')]*$avg_rate;
						$fin_out_amt+=$row[csf('out_qty')]*$avg_rate;
						
					//echo $fin_in_qnty.'='.$fin_out_qnty.',';
					//$fin_fab_trans_qty+=$in_qnty-$out_qnty;
					//$fin_fab_trans_amt+=($in_qnty-$out_qnty)*$avg_rate;
					//$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
				}
				
				
				unset($result_fin_trans);
				$fin_from_order_id=rtrim($fin_from_order_id,',');
				$fin_from_order_ids=array_unique(explode(",",$fin_from_order_id));
				
				$fin_trans_in_qty=$fin_in_qnty;
				$fin_trans_out_qty=$fin_out_qnty;
				
				$fin_trans_in_amt=$fin_in_amt;
				$fin_trans_out_amt=$fin_out_amt;
				
			//	echo $fin_trans_in_amt.'AA'.$fin_trans_out_amt;
				
				$tot_fin_fab_transfer_qty=$fin_trans_in_qty-$fin_trans_out_qty;
				$tot_fin_fab_transfer_amt=$fin_trans_in_amt-$fin_trans_out_amt;
				
				$tot_fin_fab_transfer_cost=$tot_fin_fab_transfer_amt/$g_exchange_rate;
				$tot_grey_fab_cost_acl=$grey_fab_trans_amt_acl/$g_exchange_rate;
				
				if($from_order_id!="") //Grey Transfer
				{
					$condition1= new condition();
					$condition1->po_id("in($from_order_id)");
					$condition1->init();
					$conversion1= new conversion($condition1);
					//echo $conversion->getQuery(); die;
					$conversion_costing_arr_process=$conversion1->getAmountArray_by_orderAndProcess();
					$conversion1= new conversion($condition1);
				 	$conversion_costing_arr_process_qty=$conversion1->getQtyArray_by_orderAndProcess();
					
					 $knit_cost=$knit_qty=0;
					 foreach($from_order_ids as $po_id)
					 {
						$knit_cost+=array_sum($conversion_costing_arr_process[$po_id][1]);
						 $knit_qty+=array_sum($conversion_costing_arr_process_qty[$po_id][1]);
					 }
					 
				}
				if($fin_from_order_id!="") //Finish Transfer
				{
					$condition2= new condition();
					$condition2->po_id("in($fin_from_order_id)");
					$condition2->init();
					$conversion2= new conversion($condition2);
					//echo $conversion2->getQuery(); die;
					$fin_conversion_costing_arr_process=$conversion2->getAmountArray_by_orderAndProcess();
					//print_r($fin_conversion_costing_arr_process);
					$conversion2= new conversion($condition2);
				 	$fin_conversion_costing_arr_process_qty=$conversion2->getQtyArray_by_orderAndProcess();
					 foreach($fin_from_order_ids as $po_id)
					 {
						 $tot_dye_finish_cost_pre=0;$tot_dye_finish_cost_pre_qty=0;
						foreach($conversion_cost_head_array as $process_id=>$val)
						{
							if($process_id!=30 && $process_id!=1 && $process_id!=35) //Yarn Dyeing,Knitting,Aop
							{
								$tot_dye_finish_cost_pre+=array_sum($fin_conversion_costing_arr_process[$po_id][$process_id]);
								
								$tot_dye_finish_cost_pre_qty+=array_sum($fin_conversion_costing_arr_process_qty[$po_id][$process_id]);
							}
						}
						
					 }
				}
				 	$knit_charge=$knit_cost/$knit_qty;
					//echo $knit_cost.'='.$knit_qty;
					//$dye_fin_avg_rate=$tot_dye_finish_cost_pre/$tot_dye_finish_cost_pre_qty;	
					
					//echo $dye_fin_avg_rate.'f';
			
		$YarnIssueReturn=array();
		  $sql="select a.id,a.trans_id,a.trans_type,a.entry_form,a.po_breakdown_id,a.prod_id,a.quantity,a.issue_purpose,a.returnable_qnty,a.is_sales,b.yarn_count_id,b.yarn_comp_type1st,  b.yarn_comp_percent1st,b.yarn_type,c.cons_rate,c.cons_amount from order_wise_pro_details a, product_details_master b,inv_transaction c where a.prod_id=b.id and a.trans_id=c.id and  a.trans_type=4 and a.entry_form=9 and a.po_breakdown_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and  a.status_active=1 and b.is_deleted=0 and  b.status_active=1";
		$data_Yac=sql_select($sql);
		foreach($data_Yac as $row){
			$index="'".$row[csf("yarn_count_id")]."_".$row[csf("yarn_comp_type1st")]."_".$row[csf("yarn_comp_percent1st")]."_".$row[csf("yarn_type")]."'";
			$YarnIssueReturn[$index]['qty']+=$row[csf("quantity")];
			//$YarnIssueReturn[$index]['amount']+=$row[csf("cons_amount")];
			$YarnIssueReturn[$index]['amount'] += $row[csf("quantity")]*$row[csf("cons_rate")];
		}
		foreach($YarnIssue as $ind=>$value){
			$YarnData[$ind]['acl']['qty']+=$value['qty']-$YarnIssueReturn[$ind]['qty'];
			$YarnData[$ind]['acl']['amount']+=($value['amount']-$YarnIssueReturn[$ind]['amount'])/$g_exchange_rate;
			//echo $value['amount'].'-'.$YarnIssueReturn[$ind]['amount'].'/'.$g_exchange_rate.'<br>';
		}
		
		 $sql_conv_yarn="select b.id as po_id,c.job_no, d.composition  from   wo_po_break_down b,wo_pre_cost_fab_conv_cost_dtls c,wo_pre_cost_fabric_cost_dtls d  where  b.job_no_mst=c.job_no and c.job_no=d.job_no  and c.fabric_description=d.id  and b.status_active=1 and c.cons_process in(30) and  c.job_no ='".$jobNumber."' and b.is_deleted=0 and b.id in(".implode(",",$poIdArr).") and c.amount>0 ";
		$result_conv_yarn=sql_select($sql_conv_yarn);
		$conv_yarn_arr=array();
		foreach($result_conv_yarn as $row){
			$index="".$row[csf("composition")]."";
			$conv_yarn_arr[$index]['preCost']['amount']+= array_sum($conversion_costing_arr_process[$row[csf("po_id")]][30]);
			$conv_yarn_arr[$index]['preCost']['qty']+= array_sum($conversion_costing_arr_process_qty[$row[csf("po_id")]][30]);
			//$conv_yarn_arr[$index]['amount']+=$row[csf("cons_amount")];
		}
		if($quotationId){
			 $pq_conv_yarn_data="select a.composition,b.quotation_id,b.cost_head,b.charge_unit,
				(CASE WHEN b.cons_type=30 THEN b.amount END) AS yarn_dyeing_cost,
				(CASE WHEN b.cons_type=30 THEN  b.req_qnty END) AS req_qnty
				from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_fab_conv_cost_dtls b where a.id=b.cost_head and b.quotation_id =".$quotationId." and b.cons_type=30 and b.status_active=1  and b.is_deleted=0";
				$result_price_conv_yarn=sql_select($pq_conv_yarn_data);
				foreach($result_price_conv_yarn as $p_row)
				{
					$pri_yarnrate=$p_row[csf("charge_unit")];
					$index="".$p_row[csf("composition")]."";
					$mktcons=($p_row[csf('req_qnty')]/$quaCostingPerQty)*($quaOfferQnty);
					$amount=$mktcons*$pri_yarnrate;
					$conv_yarn_arr[$index]['mkt']['amount']+=$amount;	
					$conv_yarn_arr[$index]['mkt']['qty']+=$mktcons;	
				}
			}



	//print_r($YarnData);
// Yarn End============================
// Fabric Purch ============================
		$totPrecons=0;
		$totPreAmt=0;
		$fabPur=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
		$fabPurArr=array();
	//print_r($fabPur);
		 $sql = "select id, job_no,item_number_id,uom, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls where job_no='".$jobNumber."' and fabric_source=2";
		$data_fabPur=sql_select($sql);
		foreach($data_fabPur as $fabPur_row){

			if($fabPur_row[csf('fab_nature_id')]==2){
				$Precons=$fabPur['knit']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				//echo $Precons.', ';
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr['pre']['knit']['qty']+=$Precons;
				$fabPurArr['pre']['knit']['amount']+=$Preamt;
			}
			if($fabPur_row[csf('fab_nature_id')]==3){
				$Precons=$fabPur['woven']['grey'][$fabPur_row[csf('id')]][$fabPur_row[csf('uom')]];
				$Preamt=$Precons*$fabPur_row[csf('rate')];
				$fabPurArr['pre']['woven']['qty']+=$Precons;
				$fabPurArr['pre']['woven']['amount']+=$Preamt;
			}
		//$totPrecons+=$Precons;
		//$totPreAmt+=$Preamt;
		}


		if($quotationId){
			$totMktcons=0;
			$totMktAmt=0;
			$sql = "select id, item_number_id, body_part_id, fab_nature_id, color_type_id, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pri_quo_fabric_cost_dtls where quotation_id='".$quotationId."' and fabric_source=2";
			$data_fabPur=sql_select($sql);
			foreach($data_fabPur as $fabPur_row){
			//$mktcons=($fabPur_row[csf('avg_cons')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($fabPur_row[csf('avg_cons')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$fabPur_row[csf('rate')];
				if($fabPur_row[csf('fab_nature_id')]==2){
					$fabPurArr['mkt']['knit']['qty']+=$mktcons;
					$fabPurArr['mkt']['knit']['amount']+=$mktamt;
				}
				if($fabPur_row[csf('fab_nature_id')]==3){
					$fabPurArr['mkt']['woven']['qty']+=$mktcons;
					$fabPurArr['mkt']['woven']['amount']+=$mktamt;
				}
			}
		}

		$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=1 and a.fabric_source=2 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_fabPur=sql_select($sql);
		foreach($data_fabPur as $fabPur_row){
			if($fabPur_row[csf('item_category')]==2){
				$fabPurArr['acl']['knit']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$fabPurArr['acl']['knit']['amount']+=$fabPur_row[csf('amount')];
				}
			}
			if($fabPur_row[csf('item_category')]==3){
				$fabPurArr['acl']['woven']['qty']+=$fabPur_row[csf('grey_fab_qnty')];
				if($fabPur_row[csf('grey_fab_qnty')]>0){
					$fabPurArr['acl']['woven']['amount']+=$fabPur_row[csf('amount')];
				}
			}
		}

	// Fabric Purch End ============================
	// Fabric Kniting  ==============================
		//$knitData=array();
		$conversion= new conversion($condition);
		//$knitQtyArr=$conversion->getQtyArray_by_jobFabricAndProcess();
		$knitQtyArr=$conversion->getQtyArray_by_jobFabricAndProcess();
		$conversion= new conversion($condition);
		$knitAmtArr=$conversion->getAmountArray_by_jobFabricAndProcess();
		//print_r($knitQtyArr);
	//$knitDesArr=array();
		 $sql = "select a.id, a.job_no,a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, a.rate, a.amount,a.avg_finish_cons,a.status_active,b.cons_process,c.id as po_id from wo_po_break_down c, wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_fab_conv_cost_dtls b where a.job_no=b.job_no and a.id=b.fabric_description and  c.job_no_mst=a.job_no  and c.job_no_mst=b.job_no  and a.job_no='".$jobNumber."' and b.cons_process=1 and a.status_active=1";
		$data_knit=sql_select($sql);
		foreach($data_knit as $row_knit){
			$index="'".$row_knit[csf('body_part_id')]."_".$row_knit[csf('color_type_id')]."_".$row_knit[csf('fabric_description')]."_".$row_knit[csf('gsm_weight')]."'";
			if($row_knit[csf('cons_process')]==1){
				//echo $knitQtyArr[$row_knit[csf('po_id')]][$row_knit[csf('cons_process')]].'=';
				$knitData[$index]['pre']['qty']=array_sum($knitQtyArr[$jobNumber][$row_knit[csf('id')]][$row_knit[csf('cons_process')]]);
				$knitData[$index]['pre']['amount']=array_sum($knitAmtArr[$jobNumber][$row_knit[csf('id')]][$row_knit[csf('cons_process')]]);
			}

		}
		if($quotationId){
			$sql = "select a.id, a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, b.cons_type,b.req_qnty,b.charge_unit from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_fab_conv_cost_dtls b where a.quotation_id=b.quotation_id and a.id=b.cost_head and  a.quotation_id='".$quotationId."' and b.cons_type=1";
			$data_knit=sql_select($sql);
			foreach($data_knit as $row_knit){
				$index="'".$row_knit[csf('body_part_id')]."_".$row_knit[csf('color_type_id')]."_".$row_knit[csf('fabric_description')]."_".$row_knit[csf('gsm_weight')]."'";
				if($row_knit[csf('cons_type')]==1){
				//$mktcons=($row_knit[csf('req_qnty')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
					$mktcons=($row_knit[csf('req_qnty')]/$quaCostingPerQty)*($quaOfferQnty);
					$mktamt=$mktcons*$row_knit[csf('charge_unit')];
					$knitData[$index]['mkt']['qty']+=$mktcons;
					$knitData[$index]['mkt']['amount']+=$mktamt;
				}
			}
		}

		$sql = "select  b.receive_qty,b.rate,b.amount,c.product_name_details from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=2 and b.order_id in(".implode(",",$poIdArr).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_knit=sql_select($sql);
		foreach($data_knit as $row_knit){
			$index=$row_knit[csf('product_name_details')];
			$knitData[$index]['acl']['qty']+=$row_knit[csf('receive_qty')];
			$knitData[$index]['acl']['amount']+=$row_knit[csf('amount')]/$g_exchange_rate;
		}

		$sql = "select  b.delivery_qty,b.rate,b.amount,c.product_name_details from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=2 and b.order_id in(".implode(",",$poIdArr).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_knit=sql_select($sql);
		foreach($data_knit as $row_knit){
			$index=$row_knit[csf('product_name_details')];
			$knitData[$index]['acl']['qty']+=$row_knit[csf('delivery_qty')];
			$knitData[$index]['acl']['amount']+=$row_knit[csf('amount')]/$g_exchange_rate;
		}
	// Fabric Kniting  End============================
	// Fabric Dye Finish  ==============================
		$finishData=array();
		$conversion= new conversion($condition);
		$finishQtyArr=$conversion->getQtyArray_by_jobFabricAndProcess();
		$conversion= new conversion($condition);
		$finishAmtArr=$conversion->getAmountArray_by_jobFabricAndProcess();
	//$finishDesArr=array();
		$sql = "select a.id, a.job_no,a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, a.rate, a.amount,a.avg_finish_cons,a.status_active,b.cons_process from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_fab_conv_cost_dtls b where a.job_no=b.job_no and a.id=b.fabric_description and  a.job_no='".$jobNumber."' and b.cons_process not in(1,2,30,35)";
		$data_finish=sql_select($sql);
		foreach($data_finish as $row_finish){
			$index="'".$row_finish[csf('body_part_id')]."_".$row_finish[csf('color_type_id')]."_".$row_finish[csf('fabric_description')]."_".$row_finish[csf('gsm_weight')]."'";
			$finishData[$index]['pre']['qty']=array_sum($finishQtyArr[$jobNumber][$row_finish[csf('id')]][$row_finish[csf('cons_process')]]);
			$finishData[$index]['pre']['amount']=array_sum($finishAmtArr[$jobNumber][$row_finish[csf('id')]][$row_finish[csf('cons_process')]]);
		}
		if($quotationId){
			$sql = "select a.id, a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, b.cons_type,b.req_qnty,b.charge_unit from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_fab_conv_cost_dtls b where a.quotation_id=b.quotation_id and a.id=b.cost_head and  a.quotation_id='".$quotationId."' and b.cons_type not in(1,2,30,35)";
			$data_finish=sql_select($sql);
			foreach($data_finish as $row_finish){
				$index="'".$row_finish[csf('body_part_id')]."_".$row_finish[csf('color_type_id')]."_".$row_finish[csf('fabric_description')]."_".$row_finish[csf('gsm_weight')]."'";
				//$mktcons=($row_finish[csf('req_qnty')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($row_finish[csf('req_qnty')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row_finish[csf('charge_unit')];
				$finishData[$index]['mkt']['qty']+=$mktcons;
				$finishData[$index]['mkt']['amount']+=$mktamt;
			}
		}
		$sql = "select  b.receive_qty,b.rate,b.amount,c.product_name_details from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.process_id=4 and b.order_id in(".implode(",",$poIdArr).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_finish=sql_select($sql);
		foreach($data_finish as $row_finish){
			$index=$row_finish[csf('product_name_details')];
			$finishData[$index]['acl']['qty']+=$row_finish[csf('receive_qty')];
			$finishData[$index]['acl']['amount']+=$row_finish[csf('amount')]/$g_exchange_rate;
		}
	// Fabric Dye Finish  End============================
	// Fabric AOP  ==============================
		$aopData=array();
		$conversion= new conversion($condition);
		$aopQtyArr=$conversion->getQtyArray_by_jobFabricAndProcess();
		$conversion= new conversion($condition);
		$aopAmtArr=$conversion->getAmountArray_by_jobFabricAndProcess();
	//$aopDesArr=array();
	$sql = "select a.id, a.job_no,a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, a.rate, a.amount,a.avg_finish_cons,a.status_active,b.cons_process from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_fab_conv_cost_dtls b where a.job_no=b.job_no and a.id=b.fabric_description and  a.job_no='".$jobNumber."' and b.cons_process=35";
		$data_aop=sql_select($sql);
		foreach($data_aop as $row_aop){
			$index="'".$row_aop[csf('body_part_id')]."_".$row_aop[csf('color_type_id')]."_".$row_aop[csf('fabric_description')]."_".$row_aop[csf('gsm_weight')]."'";
			if($row_aop[csf('cons_process')]==35){
				$finishData[$index]['pre']['qty']+=array_sum($aopQtyArr[$jobNumber][$row_aop[csf('id')]][$row_aop[csf('cons_process')]]);
				$finishData[$index]['pre']['amount']+=array_sum($aopAmtArr[$jobNumber][$row_aop[csf('id')]][$row_aop[csf('cons_process')]]);
			}

		}

		if($quotationId){
			$sql = "select a.id, a.item_number_id, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.gsm_weight,a.avg_cons, a.fabric_source, b.cons_type,b.req_qnty,b.charge_unit from wo_pri_quo_fabric_cost_dtls a, wo_pri_quo_fab_conv_cost_dtls b where a.quotation_id=b.quotation_id and a.id=b.cost_head and  a.quotation_id='".$quotationId."' and b.cons_type=35";
			$data_aop=sql_select($sql);
			foreach($data_aop as $row_aop){
				$index="'".$row_aop[csf('body_part_id')]."_".$row_aop[csf('color_type_id')]."_".$row_aop[csf('fabric_description')]."_".$row_aop[csf('gsm_weight')]."'";
				if($row_aop[csf('cons_type')]==35){
				//$mktcons=($row_aop[csf('req_qnty')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
					$mktcons=($row_aop[csf('req_qnty')]/$quaCostingPerQty)*($quaOfferQnty);
					$mktamt=$mktcons*$row_aop[csf('charge_unit')];
					$finishData[$index]['mkt']['qty']+=$mktcons;
					$finishData[$index]['mkt']['amount']+=$mktamt;
				}
			}
		}

		/*$sql = "select  b.batch_issue_qty, b.rate, b.amount, b.currency_id, b.exchange_rate,b.process_id from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and b.process_id=35 and b.job_no ='".$jobNumber."' and  b.order_id in(".implode(",",$poIdArr).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_aop=sql_select($sql);
		foreach($data_aop as $row_aop){
			if($row_aop[csf('process_id')]==35){
				$exchange_rate=$row_aop[csf('batch_issue_qty')];
				if(!$exchange_rate){
					$exchange_rate=1;
				}
				$aopData['acl']['qty']+=$row_aop[csf('batch_issue_qty')];
				$aopData['acl']['amount']+=$row_aop[csf('amount')]/$exchange_rate;
			}
		}*/
		
		$sql = "select  b.body_part_id,b.receive_qty,b.rate,b.amount from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id  and a.process_id=4 and b.sub_process_id in(31,35) and b.order_id in(".implode(",",$poIdArr).") and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1";
		$data_aop=sql_select($sql);
		foreach($data_aop as $row_aop){
			//$index=$row_aop[csf('product_name_details')];
			//$index="'".$row_aop[csf('body_part_id')]."_".$row_aop[csf('color_type_id')]."_".$row_aop[csf('febric_description_id')]."'";
			$index="'".$row_aop[csf('body_part_id')]."'";
			$finishData[$index]['acl']['qty']+=$row_aop[csf('receive_qty')];
			$finishData[$index]['acl']['amount']+=$row_aop[csf('amount')]/$g_exchange_rate;
		}

	// Fabric AOP  End============================
	
					
				
	// Trim Cost ============================

	//$trim_group=return_library_array( "select item_name,id from  lib_item_group", "id", "item_name" ); 
		$trim_groupArr=array();
		$conversion_factor=sql_select("select id,item_name,trim_uom,conversion_factor from  lib_item_group  ");
		foreach($conversion_factor as $row_f){
			$trim_groupArr[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
			$trim_groupArr[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
			$trim_groupArr[$row_f[csf('id')]]['item_name']=$row_f[csf('item_name')];
		}
	//print_r($trim_groupArr);
		$trimData=array();
	//$trimQtyArr=$trim->getQtyArray_by_jobAndItemid();
		$trimQtyArr=$trim->getQtyArray_by_jobAndPrecostdtlsid();

		$trim= new trims($condition);
	//$trimAmtArr=$trim->getAmountArray_by_jobAndItemid();
		$trimAmtArr=$trim->getAmountArray_by_jobAndPrecostdtlsid();

		$sql = "select id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active from wo_pre_cost_trim_cost_dtls  where job_no='".$jobNumber."'";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$trimData[$row[csf('trim_group')]]['pre']['qty']+=$trimQtyArr[$jobNumber][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['pre']['amount']+=$trimAmtArr[$jobNumber][$row[csf('id')]];
			$trimData[$row[csf('trim_group')]]['cons_uom']=$row[csf('cons_uom')];
		}
		if($quotationId){
			$sql = "select id,  trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active from wo_pri_quo_trim_cost_dtls  where quotation_id='".$quotationId."'";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
		//$mktcons=($row[csf('cons_dzn_gmts')]/$costPerQty)*($jobQty/$totalSetQnty);
				$mktcons=($row[csf('cons_dzn_gmts')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
				$trimData[$row[csf('trim_group')]]['mkt']['qty']+=$mktcons;
				$trimData[$row[csf('trim_group')]]['mkt']['amount']+=$mktamt;
				$trimData[$row[csf('trim_group')]]['cons_uom']=$row[csf('cons_uom')];
			}
		}
		$trimsRecArr=array();
	//echo "select b.po_breakdown_id, a.item_group_id,b.quantity as quantity,a.rate,a.cons_rate  from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in(".implode(",",$poIdArr).") ";
		$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,b.quantity as quantity,a.rate,a.cons_rate  from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in(".implode(",",$poIdArr).") ");
		foreach($receive_qty_data as $row){
			$trimsRecArr[$row[csf('item_group_id')]]['qty']+=$row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'];
			$trimsRecArr[$row[csf('item_group_id')]]['rate']=$row[csf('rate')];
			$trimsRecArr[$row[csf('item_group_id')]]['amount']+=($row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'])*$row[csf('rate')];
		//echo $row[csf('quantity')]."*".$trim_groupArr[$row[csf('item_group_id')]]['con_factor']."*".$row[csf('rate')]."<br/>";
			$trimsRecArr[$row[csf('item_group_id')]]['cons_uom']=$trim_groupArr[$row[csf('item_group_id')]]['cons_uom'];
		}
	//print_r($trimsRecArr);
		$trimsRecReArr=array();
		$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id,d.quantity as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  d.po_breakdown_id in(".implode(",",$poIdArr).")");
		foreach($receive_rtn_qty_data as $row){
			$trimsRecReArr[$row[csf('item_group_id')]]['qty']+=$row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'];
			$trimsRecReArr[$row[csf('item_group_id')]]['amount']+=($row[csf('quantity')]*$trim_groupArr[$row[csf('item_group_id')]]['con_factor'])*$trimsRecArr[$row[csf('item_group_id')]]['rate'];
		}
		foreach($trimsRecArr as $ind=>$value){
			$trimData[$ind]['acl']['qty']+=$trimsRecArr[$ind]['qty']-$trimsRecReArr[$ind]['qty'];
			$trimData[$ind]['acl']['amount']+=$trimsRecArr[$ind]['amount']-$trimsRecReArr[$ind]['amount'];
			$trimData[$ind]['cons_uom']=$trimsRecArr[$ind]['cons_uom'];
		}

	//print_r($trimData);

	// Trim Cost End============================
	// Embl Cost ============================
		$embData=array();
		$embQtyArr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
		$emblishment= new emblishment($condition);
		$embAmtArr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
		$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no='".$jobNumber."' and emb_name !=3";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$embData['pre']['qty']+=$embQtyArr[$jobNumber][$row[csf('id')]];
			$embData['pre']['amount']+=$embAmtArr[$jobNumber][$row[csf('id')]];
		}
	//print_r($embData);
		if($quotationId){
			$sql = "select id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pri_quo_embe_cost_dtls where quotation_id='".$quotationId."' and emb_name !=3";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
		//$mktcons=($row[csf('cons_dzn_gmts')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($row[csf('cons_dzn_gmts')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
				$embData['mkt']['qty']+=$mktcons;
				$embData['mkt']['amount']+=$mktamt;
			}
		}
		$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and b.emblishment_name !=3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$embData['acl']['qty']+=$row[csf('wo_qnty')];
			$embData['acl']['amount']+=$row[csf('amount')];
			
		}
	// Embl Cost End ============================
	// Wash Cost ============================
		$washData=array();
		$washQtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
		$wash= new wash($condition);
		$washAmtArr=$wash->getAmountArray_by_jobAndEmblishmentid();
		$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no='".$jobNumber."' and emb_name =3";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$washData['pre']['qty']+=$washQtyArr[$jobNumber][$row[csf('id')]];
			$washData['pre']['amount']+=$washAmtArr[$jobNumber][$row[csf('id')]];
		}
		if($quotationId){
			$sql = "select id, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pri_quo_embe_cost_dtls where quotation_id='".$quotationId."' and emb_name =3";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
		//$mktcons=($row[csf('cons_dzn_gmts')]/$costPerQty)*($jobPcutQty/$totalSetQnty);
				$mktcons=($row[csf('cons_dzn_gmts')]/$quaCostingPerQty)*($quaOfferQnty);
				$mktamt=$mktcons*$row[csf('rate')];
				$washData['mkt']['qty']+=$mktcons;
				$washData['mkt']['amount']+=$mktamt;
			}
		}
		$sql = "select a.item_category,a.exchange_rate,b.grey_fab_qnty,b.wo_qnty,b.fin_fab_qnty,b.po_break_down_id,b.rate,b.amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and b.emblishment_name =3 and b.po_break_down_id in(".implode(",",$poIdArr).") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$washData['acl']['qty']+=$row[csf('wo_qnty')];
			$washData['acl']['amount']+=$row[csf('amount')];
			
		}
	// Wash Cost End ============================
	// Commision Cost  ============================
		$commiData=array();
		$commiAmtArr=$commision->getAmountArray_by_jobAndPrecostdtlsid();
		$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pre_cost_commiss_cost_dtls  where job_no='".$jobNumber."'";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$commiData['pre']['qty']=$jobQty;
			$commiData['pre']['amount']+=$commiAmtArr[$jobNumber][$row[csf('id')]];
			$commiData['pre']['rate']+=$commiAmtArr[$jobNumber][$row[csf('id')]]/$jobQty;
		}

		if($quotationId){
			$sql = "select id,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from  wo_pri_quo_commiss_cost_dtls  where quotation_id='".$quotationId."'";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
		//$mktamt=($row[csf('commission_amount')]/$costPerQty)*($jobQty/$totalSetQnty);
				$mktamt=($row[csf('commission_amount')]/$quaCostingPerQty)*($quaOfferQnty);
				$commiData['mkt']['qty']=$quaOfferQnty;
				$commiData['mkt']['amount']+=$mktamt;
				$commiData['mkt']['rate']+=$mktamt/$quaOfferQnty;
			}
		}
	// Commision Cost  End ============================

	// Commarcial Cost  ============================
		$commaData=array();
		$commaAmtArr=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
		//print_r($commaAmtArr);
		$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls where job_no='".$jobNumber."'";
		$data_array=sql_select($sql);
		$po_ids=rtrim($po_ids,',');
		$poIds=array_unique(explode(',',$po_ids));
		foreach($data_array as $row){
			$commaData['pre']['qty']=$jobQty;
			$commaData['pre']['amount']+=$commaAmtArr[$jobNumber][$row[csf('id')]];
			$exfactory_qty=0;
			foreach($poIds as $pid)
			{
				$exfactory_qty+=$exfactoryQtyArr[$pid];
			}
			
				$commaData['pre']['rate']+=$commaAmtArr[$jobNumber][$row[csf('id')]]/$exfactory_qty;

			//echo $commaAmtArr[$jobNumber][$row[csf('id')]].'/'.$exfactory_qty;
		}
		if($quotationId){
			$sql = "select id, item_id, rate, amount, status_active from  wo_pri_quo_comarcial_cost_dtls where quotation_id=".$quotationId."";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
		//$mktamt=($row[csf('amount')]/$costPerQty)*($jobQty/$totalSetQnty);
				$mktamt=($row[csf('amount')]/$quaCostingPerQty)*($quaOfferQnty);
				$commaData['mkt']['qty']=$quaOfferQnty;
				$commaData['mkt']['amount']+=$mktamt;
				$commaData['mkt']['rate']+=$mktamt/$quaOfferQnty;
			}
		}
	// Commarcial Cost  End ============================
	// Other Cost  ============================
		$otherData=array();
		$other_cost=$other->getAmountArray_by_job();
		$sql = "select id ,lab_test ,inspection  ,cm_cost ,freight ,currier_pre_cost ,certificate_pre_cost ,common_oh ,depr_amor_pre_cost  from  wo_pre_cost_dtls where job_no='".$jobNumber."'";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$otherData['pre']['freight']['qty']=$jobQty;
			$otherData['pre']['freight']['amount']=$other_cost[$jobNumber]['freight'];
			$otherData['pre']['freight']['rate']=$other_cost[$jobNumber]['freight']/$jobQty;

			$otherData['pre']['lab_test']['qty']=$jobQty;
			$otherData['pre']['lab_test']['amount']=$other_cost[$jobNumber]['lab_test'];
			$otherData['pre']['lab_test']['rate']=$other_cost[$jobNumber]['lab_test']/$jobQty;

			$otherData['pre']['inspection']['qty']=$jobQty;
			$otherData['pre']['inspection']['amount']=$other_cost[$jobNumber]['inspection'];
			$otherData['pre']['inspection']['rate']=$other_cost[$jobNumber]['inspection']/$jobQty;

			$otherData['pre']['currier_pre_cost']['qty']=$jobQty;
			$otherData['pre']['currier_pre_cost']['amount']=$other_cost[$jobNumber]['currier_pre_cost'];
			$otherData['pre']['currier_pre_cost']['rate']=$other_cost[$jobNumber]['currier_pre_cost']/$jobQty;

			$otherData['pre']['cm_cost']['qty']=$jobQty;
			$otherData['pre']['cm_cost']['amount']=$other_cost[$jobNumber]['cm_cost'];
			$otherData['pre']['cm_cost']['rate']=$other_cost[$jobNumber]['cm_cost']/$jobQty;
		}
		if($quotationId){
			$sql = "select id ,lab_test ,inspection  ,cm_cost ,freight ,currier_pre_cost ,certificate_pre_cost ,common_oh ,depr_amor_pre_cost  from  wo_price_quotation_costing_mst where quotation_id='".$quotationId."'";
			$data_array=sql_select($sql);
			foreach($data_array as $row){
				$freightAmt=($row[csf('freight')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['freight']['qty']=$quaOfferQnty;
				$otherData['mkt']['freight']['amount']=$freightAmt;
				$otherData['mkt']['freight']['rate']=$freightAmt/$quaOfferQnty;

				$labTestAmt=($row[csf('lab_test')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['lab_test']['qty']=$quaOfferQnty;
				$otherData['mkt']['lab_test']['amount']=$labTestAmt;
				$otherData['mkt']['lab_test']['rate']=$labTestAmt/$quaOfferQnty;

				$inspectionAmt=($row[csf('inspection')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['inspection']['qty']=$quaOfferQnty;
				$otherData['mkt']['inspection']['amount']=$inspectionAmt;
				$otherData['mkt']['inspection']['rate']=$inspectionAmt/$quaOfferQnty;

				$currierPreCostAmt=($row[csf('currier_pre_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['currier_pre_cost']['qty']=$quaOfferQnty;

				$otherData['mkt']['currier_pre_cost']['amount']=$currierPreCostAmt;
				$otherData['mkt']['currier_pre_cost']['rate']=$currierPreCostAmt/$quaOfferQnty;

				$cmCostAmt=($row[csf('cm_cost')]/$quaCostingPerQty)*($quaOfferQnty);
				$otherData['mkt']['cm_cost']['qty']=$quaOfferQnty;
				$otherData['mkt']['cm_cost']['amount']=$cmCostAmt;
				$otherData['mkt']['cm_cost']['rate']=$cmCostAmt/$quaOfferQnty;
			}
		}
		$financial_para=array();
		$sql_std_para=sql_select("select cost_per_minute,applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$company_name and status_active=1 and is_deleted=0  order by id desc");	
			foreach($sql_std_para as $row )
			{
				$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					//$newDate=add_date(str_replace("'","",$applying_period_date),$j);
					$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
					
					$financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
					//$asking_profit_arr[$newDate]['max_profit']=$ask_row[csf('max_profit')];
				}
			}
			
	 $sql_cm_cost="select  jobNo, available_min,production_date from production_logicsoft where  jobNo='".$jobNumber."'";
	$cm_data_array=sql_select($sql_cm_cost);
	foreach($cm_data_array as $row)
	{
		//$production_date=date("d-m-Y", strtotime($row[csf('production_date')]));
		
		$production_date=change_date_format($row[csf('production_date')],'','',1);
		
		$cost_per_minute=$financial_para[$production_date]['cost_per_minute'];
		$otherData['acl']['cm_cost']['amount']+=($row[csf('available_min')]*$cost_per_minute)/$g_exchange_rate;
	}
	
		$sql="select id, company_id, cost_head, exchange_rate, incurred_date, incurred_date_to, applying_period_date, applying_period_to_date, based_on, po_id, job_no, gmts_item_id, item_smv, production_qty, smv_produced, amount, amount_usd from wo_actual_cost_entry where po_id in(".implode(",",$poIdArr).")";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			if($row[csf('cost_head')]==2){
				$otherData['acl']['freight']['amount']+=$row[csf('amount_usd')];
		//$otherData['acl']['freight']['rate']=$row[csf('freight')];
			}
			if($row[csf('cost_head')]==1){
				$otherData['acl']['lab_test']['amount']+=$row[csf('amount_usd')];
		//$otherData['acl']['lab_test']['rate']=$row[csf('lab_test')];
			}
			if($row[csf('cost_head')]==3){

				$otherData['acl']['inspection']['amount']+=$row[csf('amount_usd')];
		//$otherData['acl']['inspection']['rate']=$row[csf('inspection')];
			}
			if($row[csf('cost_head')]==4){
				$otherData['acl']['currier_pre_cost']['amount']+=$row[csf('amount_usd')];
		//$otherData['acl']['currier_pre_cost']['rate']=$row[csf('currier_pre_cost')];
			}
			if($row[csf('cost_head')]==5){
				//$otherData['acl']['cm_cost']['amount']+=$row[csf('amount_usd')];
		//$otherData['acl']['cm_cost']['rate']=$row[csf('cm_cost')];
			}
			if($row[csf('cost_head')]==6){
				$commaData['acl']['amount']+=$row[csf('amount_usd')];
		//$commaData['acl']['cm_cost']['rate']=$row[csf('cm_cost')];
			}
		}
		//echo $fromDate;die;
	
		//$data = $this->salesorder_model->get_sales_order_data_info($fromDate,$toDate,$this->get('program_no'));
       // $this->response($data);
    }
	
	
	function validateDate($date)
	{
		 return (bool)strtotime($date);
	}
	function compareDate($fromDate, $toDate){
		if(strtotime($fromDate) <= strtotime($toDate)){
			return 1;
		}else{
			return 0;
		}
		
	}
	
	

	
 }
