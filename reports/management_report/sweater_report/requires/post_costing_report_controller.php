<?
session_start();
//ini_set('memory_limit','3072M');
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/class.fabrics.php');


/*require_once('../../../../includes/class.reports.php');
require_once('../../../../includes/class.yarns.php');*/

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------

$user_name = $_SESSION['logic_erp']["user_id"];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

	$company_name=str_replace("'","",$cbo_company_name);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	
	if($txt_ref_no!='') $ref_cond="and a.style_ref_no='$txt_ref_no'";else $ref_cond="";
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if(trim($txt_job_no)!="")  $job_no_cond="and a.job_no_prefix_num like ".trim($txt_job_no).""; else $job_no_cond="";

	
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}
	else $year_cond="";
	
	$exchange_rate=76; $po_ids_array=array();

	if($db_type==0) $year_field="YEAR(a.insert_date) as YEAR"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as YEAR";
	else $year_field="";


	 $main_sql=sql_select("SELECT a.id as JOB_ID, a.job_no_prefix_num, a.job_no as JOB_NO, $year_field, a.company_name, a.buyer_name as BUYER_NAME, a.style_ref_no as STYLE_REF_NO, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv, a.avg_unit_price as AVG_UNIT_PRICE,  b.id as PO_ID, b.po_number as PO_NUMBER,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity as PO_QUANTITY, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status as SHIPING_STATUS, c.exchange_rate from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=b.job_no_mst and c.entry_from=158 and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.shiping_status in (2,3) $buyer_id_cond $ref_cond $year_cond $job_no_cond order by b.pub_shipment_date, a.job_no_prefix_num, b.id");
	 //echo "SELECT a.id as JOB_ID, a.job_no_prefix_num, a.job_no as JOB_NO, $year_field, a.company_name, a.buyer_name as BUYER_NAME, a.style_ref_no as STYLE_REF_NO, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv, a.avg_unit_price as AVG_UNIT_PRICE,  b.id as PO_ID, b.po_number as PO_NUMBER,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity as PO_QUANTITY, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status, c.exchange_rate from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=b.job_no_mst and c.entry_from=158 and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.shiping_status in (2,3) $buyer_id_cond $ref_cond $year_cond $job_no_cond order by b.pub_shipment_date, a.job_no_prefix_num, b.id"; die;
	$main_data_arr=array();
	//$attributes = array('BUYER_NAME','STYLE_REF_NO','JOB_NO','');
	foreach ($main_sql as $row) {
		$main_data_arr[$row['JOB_ID']]['job_id'] = $row['JOB_ID'];
		$main_data_arr[$row['JOB_ID']]['buyer_name'] = $row['BUYER_NAME'];
		$main_data_arr[$row['JOB_ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
		$main_data_arr[$row['JOB_ID']]['job_no'] = $row['JOB_NO'];
		$main_data_arr[$row['JOB_ID']]['po_number'][$row['PO_ID']] = $row['PO_NUMBER'];
		$main_data_arr[$row['JOB_ID']]['po_quantity'] += $row['PO_QUANTITY'];
		$main_data_arr[$row['JOB_ID']]['avg_unit_price'] = $row['AVG_UNIT_PRICE'];
		$main_data_arr[$row['JOB_ID']]['shiping_status'][$row['SHIPING_STATUS']] = $shipment_status[$row['SHIPING_STATUS']];
	}
	


	$all_po_id="";  $all_jobs=''; $exchangepo_rateArr=array(); $exchangejob_rateArr=array();
	foreach($main_sql as $row)
	{
		if($all_po_id=="") $all_po_id=$row['PO_ID']; else $all_po_id.=",".$row['PO_ID'];
		if($all_jobs=="") $all_jobs="'".$row[csf("job_no")]."'"; else $all_jobs.=",'".$row[csf("job_no")]."'";
		$exchangepo_rateArr[$row['PO_ID']]=$row[csf("exchange_rate")];
		$exchangejob_rateArr[$row[csf("job_no")]]=$row[csf("exchange_rate")];
		$job_id_arr[$row['JOB_ID']] =$row['JOB_ID'];
	}
	$all_jobs=implode(",",array_unique(explode(",",$all_jobs)));
	
	$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2="";$po_cond_for_in3=""; $po_cond_for_in4=""; $po_cond_for_in5=""; 
	$po_ids=count(array_unique(explode(",",$all_po_id)));
	if($db_type==2 && $po_ids>1000)
	{
		$po_cond_for_in=" and (";
		$po_cond_for_in2=" and (";
		$po_cond_for_in3=" and (";
		$po_cond_for_in4=" and (";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
			$po_cond_for_in2.=" id in($ids) or"; 
			$po_cond_for_in3.=" b.order_id in($ids) or";
			$po_cond_for_in4.=" b.po_breakdown_id in($ids) or"; 
			$po_cond_for_in5.=" b.id in($ids) or"; 
		}
		$po_cond_for_in=chop($po_cond_for_in,'or ');
		$po_cond_for_in.=")";
		
		$po_cond_for_in2=chop($po_cond_for_in2,'or ');
		$po_cond_for_in2.=")";
		$po_cond_for_in3=chop($po_cond_for_in3,'or ');
		$po_cond_for_in3.=")";
		$po_cond_for_in4=chop($po_cond_for_in4,'or ');
		$po_cond_for_in4.=")";
		$po_cond_for_in5=chop($po_cond_for_in5,'or ');
		$po_cond_for_in5.=")";
	}
	else
	{
		$poIds=implode(",",array_unique(explode(",",$all_po_id)));
		
		if($poIds==""){$po_cond_for_in="";}else{$po_cond_for_in=" and b.po_break_down_id in($poIds)";}
		if($poIds==""){$po_cond_for_in2="";}else{$po_cond_for_in2=" and id in($poIds)";}
		if($poIds==""){$po_cond_for_in3="";}else{$po_cond_for_in3=" and b.order_id in($poIds)";}
		if($poIds==""){$po_cond_for_in4="";}else{$po_cond_for_in4=" and b.po_breakdown_id in($poIds)";}
		if($poIds==""){$po_cond_for_in5="";}else{$po_cond_for_in5=" and b.id in($poIds)";}
	}
	
	$jobNos=implode(",",array_filter(array_unique(explode(",",$all_jobs))));
	$job_nos=count(explode(",",$jobNos)); $jobCond="";
	if($db_type==2 && $job_nos>500)
	{
		$jobCond=" and (";
		$jobNosArr=array_chunk(explode(",",$jobNos),499);
		foreach($jobNosArr as $ids)
		{
			$ids=implode(",",$ids);
			$jobCond.=" b.job_no in($ids) or"; 
		}
		$jobCond=chop($jobCond,'or ');

		$jobCond.=")";
	}
	else $jobCond=" and b.job_no in($jobNos)";
	if($jobNos=='')  $jobCond="and b.job_no in('0')";
	else $jobCond=$jobCond;

	if($db_type==2 && count ($job_id_arr) >500)
	{
		$jobIdCond=" and (";
		$jobIdsArr=array_chunk($job_id_arr,499);
		foreach($jobIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$jobIdCond.=" job_id in($ids) or"; 
		}
		$jobIdCond=chop($jobIdCond,'or ');

		$jobIdCond.=")";
	}
	else{
		$ids=implode(",",$job_id_arr);
		$jobIdCond=" and job_id in($ids)";
	}

	
	
	if($cbo_date_type==1){
		$ex_factory_arr=return_library_array( "select b.po_break_down_id, sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty from pro_ex_factory_mst b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id", "po_break_down_id", "qnty");
	}

	$ex_factory_date=sql_select("SELECT max(shipment_date) as shipment_date, job_id from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond_for_in2 group by job_id");
	foreach ($ex_factory_date as $row) {
		$job_wise_exfactory_date[$row[csf('job_id')]] = $row[csf('shipment_date')];
	}

	$shipped_qty_sql = sql_select("SELECT b.ex_factory_qnty as EX_FACTORY_QNTY, b.shiping_mode as SHIPING_MODE, b.po_break_down_id, a.job_id as JOB_ID from wo_po_break_down a join  pro_ex_factory_mst b on a.id=b.po_break_down_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and garments_nature=100 $po_cond_for_in");
	foreach ($shipped_qty_sql as $row) {
		$main_data_arr[$row['JOB_ID']]['shipped_qty'] += $row['EX_FACTORY_QNTY'];
		$main_data_arr[$row['JOB_ID']]['shiping_mode'][$row['SHIPING_MODE']] = $shipment_mode[$row['SHIPING_MODE']];
	}

	$invoice_unit_price_sql=sql_select("SELECT a.job_id as JOB_ID, avg(b.current_invoice_rate) as UNIT_PRICE, avg(c.discount_in_percent) as DISCOUNT_IN_PERCENT, avg(c.commission_percent) as COMMISSION_PERCENT  from wo_po_break_down a join com_export_invoice_ship_dtls b on a.id=b.po_breakdown_id join com_export_invoice_ship_mst c on  c.id=b.mst_id where b.current_invoice_qnty >0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in4 group by a.job_id"); 
	foreach ($invoice_unit_price_sql as $row) {
		$main_data_arr[$row['JOB_ID']]['unit_price'] = $row['UNIT_PRICE'];
		$main_data_arr[$row['JOB_ID']]['discount_in_percent'] = $row['DISCOUNT_IN_PERCENT'];
		$main_data_arr[$row['JOB_ID']]['commission_percent'] = $row['COMMISSION_PERCENT'];
	}
	$pre_cost_dtls=sql_select("SELECT job_id as JOB_ID, cm_cost as CM_COST, cm_cost_percent as CM_COST_PERCENT, embel_cost as EMBEL_COST , common_oh as COMMON_OH, price_dzn as PRICE_DZN, margin_dzn as MARGIN_DZN, margin_dzn_percent as MARGIN_DZN_PERCENT from wo_pre_cost_dtls where status_active=1 and is_deleted=0 $jobIdCond");
	foreach ($pre_cost_dtls as $row) {
		$main_data_arr[$row['JOB_ID']]['embel_cost'] = $row['EMBEL_COST'];
		$main_data_arr[$row['JOB_ID']]['common_oh'] = $row['COMMON_OH'];
		$main_data_arr[$row['JOB_ID']]['cm_cost'] = $row['CM_COST'];
		$main_data_arr[$row['JOB_ID']]['price_dzn'] = $row['PRICE_DZN'];
		$main_data_arr[$row['JOB_ID']]['cm_cost_percent'] = $row['CM_COST_PERCENT'];
		$main_data_arr[$row['JOB_ID']]['margin_dzn'] = $row['MARGIN_DZN'];
		$main_data_arr[$row['JOB_ID']]['margin_dzn_percent'] = $row['MARGIN_DZN_PERCENT'];
	}
	
	$yarn_rec_sql=sql_select("SELECT a.id as JOB_ID, sum(b.order_amount) as ORDER_AMOUNT from wo_po_details_master a join inv_transaction b on a.job_no = b.job_no where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form=248 $jobCond group by a.id"); 

	foreach ($yarn_rec_sql as $row) {
		$main_data_arr[$row['JOB_ID']]['yarn_rec_amount'] = $row['ORDER_AMOUNT'];
	}

	$trim_rec_sql = sql_select("SELECT d.job_id as JOB_ID, sum(b.order_amount) as ORDER_AMOUNT from inv_receive_master a join inv_transaction c on a.id=c.MST_ID join order_wise_pro_details b on c.id=b.trans_id join wo_po_break_down d on d.id=b.po_breakdown_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  b.trans_type =1 and a.entry_form=24 and b.ENTRY_FORM=24  and a.receive_basis=1 $po_cond_for_in4 group by d.job_id");

	foreach ($trim_rec_sql as $row) {
		$main_data_arr[$row['JOB_ID']]['trims_rec_amount'] = $row['ORDER_AMOUNT'];
	}

	$actual_cost_sql= sql_select("SELECT a.id as JOB_ID, sum(c.amount_usd) as COST_AMOUNT from wo_po_details_master a, wo_po_break_down b, wo_actual_cost_entry c where a.id=b.job_id and b.id=c.po_id and c.status_active=1 and c.is_deleted=0 and  c.based_on=1 and c.cost_head in (1,4,6) $po_cond_for_in5 group by a.id");

	foreach ($actual_cost_sql as $row) {
		$main_data_arr[$row['JOB_ID']]['actual_cost'] = $row['COST_AMOUNT'];
	}


	/*echo '<pre>';
	print_r($main_data_arr); die;*/

	ob_start();
	?>
    <fieldset>
    	<table width="1500">
            <tr class="form_caption">
                <td colspan="20" align="center"><strong>Post Costing Report</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="20" align="center"><strong><? echo $company_arr[$company_name]; ?></strong>
                </td>
            </tr>
        </table>
        <table style="margin-top:10px" id="table_header_1" class="rpt_table" width="1500" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead id="scroll_body">
                <th width="40">SL</th>
                <th width="70">Buyer Name</th>
                <th width="110">Style NO.</th>
                <th width="70">Job NO.</th>
                <th width="100">Purchase Order</th>
                <th width="70">Order Qty</th>
                <th width="70">Cost Sheet PC</th>
                <th width="70">Total Value</th>
                <th width="70">Ex Factory Date</th>
                <th width="70">Shipment Status</th>
                <th width="70">Shipped Qty</th>
                <th width="70">Actual Shipping Mode</th>
                <th width="70">Invoice Unit Price</th>
                <th width="70">Total Sales</th>
                <th width="70">Discount Amount</th>
                <th width="70">Pre Cost CM</th>
                <th width="70">Pre Cost CM (%)</th>
                <th width="70">Post Cost CM</th>
                <th width="70">Post Cost CM (%)</th>
                <th width="70">Act CM Earned</th>
            </thead>
        <!-- </table>
    	<div style="width:4102px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="4080" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body"> -->
            <? $i=1; ?>
            	<? foreach ($main_data_arr as $job_id=>$value) { 
            		$total_order += $value['po_quantity'];
            		$total_value = $value['po_quantity']*$value['avg_unit_price'];
            		$sum_total_value += $total_value;
            		$total_salse_qty=$value['unit_price']*$value['shipped_qty'];
            		$commission_amount = ($value['commission_percent']*$total_salse_qty)/100;
            		$commission = ($commission_amount/$value['shipped_qty'])*12;
            		$yarn_cost = ($value['yarn_rec_amount']/$value['shipped_qty'])*12;
            		$trims_cost = ($value['trims_rec_amount']/$value['shipped_qty'])*12;
            		$actual_cost = ($value['actual_cost']/$value['shipped_qty'])*12;

            		$per_dzn_cost = $commission+$yarn_cost+$trims_cost+$value['embel_cost']+$value['common_oh']+$actual_cost;

            		$pre_cost_cm= $value['price_dzn']-$per_dzn_cost;
            		$act_cm_earned=$pre_cost_cm*$value['shipped_qty'];
            		$discount_amount =($value['discount_in_percent']*$total_salse_qty)/100;
            	?>
                	<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $buyer_arr[$value['buyer_name']]; ?></td>
                        <td><? echo $value['style_ref_no']; ?></td>
		                <td><? echo $value['job_no']; ?></td>
		                <td><? echo implode(", ", $value['po_number']); ?></td>
		                <td><? echo $value['po_quantity']; ?></td>
		                <td><? echo $value['avg_unit_price']; ?></td>
		                <td><? echo number_format($total_value,2); ?></td>
		                <td><? echo change_date_format($job_wise_exfactory_date[$job_id],"yyyy-mm-dd",""); ?></td>
		                <td><? echo implode(",", $value['shiping_status']) ?></td>
		                <td><? echo number_format($value['shipped_qty']) ?></td>
		                <td><? echo implode(",", $value['shiping_mode']) ?></td>
		                <td><? echo number_format($value['unit_price'],2); ?></td>
		                <td><? echo number_format($total_salse_qty,2); ?></td>
		                <td><? echo number_format($discount_amount,2); ?></td>
		                <td><? echo $value['margin_dzn'] ?></td>
		                <td><? echo $value['margin_dzn_percent'] ?></td>
		                <td><? echo number_format($pre_cost_cm,2) ?></td>
		                <td><? echo ''; ?></td>
		                <td><? echo number_format($act_cm_earned,2) ?></td>
                    </tr>
				<?
					$i++;
					$total_order_qty += $value['po_quantity'];
					$total_order_value += $total_value;
					$total_shipped_qty += $value['shipped_qty'];
					$grand_total_salse_qty += $total_salse_qty;
					$total_discount_amount += $discount_amount;
					$total_act_cm_earned += $act_cm_earned;
				}
				?>
				<tr>
					<td colspan="5" align="right"><b>Total &nbsp;&nbsp;&nbsp;</b></td>
					<td><b><? echo $total_order_qty ?></b></td>
					<td></td>
					<td><b><? echo number_format($total_order_value,2) ?></b></td>
					<td></td>
					<td></td>
					<td><b><? echo $total_shipped_qty ?></b></td>
					<td></td>
					<td></td>
					<td><b><? echo number_format($grand_total_salse_qty,2) ?></b></td>
					<td><b><? echo number_format($total_discount_amount,2) ?></b></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td><b><? echo number_format($total_act_cm_earned,2) ?></b></td>
				</tr>
            </table>
		</div>
        <!-- <table class="rpt_table" width="4080" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tr bgcolor="#CCDDEE" onClick="change_color('tr_pt','#CCDDEE')" id="tr_pt" style="font-weight:bold;">
            </tr>
                
        </table> -->

	</fieldset>
<?
	foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	echo "$total_data****$filename";
	exit();
}

if($action=="load_drop_down_season_buyer")
{
	echo create_drop_down( "cbo_season_name", 140, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}


if($action=="report_generate_actual_cost")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $cbo_order_status;die;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name"  );
	
	
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season where  status_active =1 and is_deleted=0 order by season_name ASC", "id", "season_name"  );
	
	
	
	
	$company_name=str_replace("'","",$cbo_company_name);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	if($txt_ref_no!='') $ref_cond="and b.grouping='$txt_ref_no'";else $ref_cond="";
	if($txt_file_no!='') $file_cond="and b.file_no=$txt_file_no";else $file_cond="";
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	
	
	if(str_replace("'","",$cbo_season_name)!=0)
	{
		$season_cond=" and a.SEASON_BUYER_WISE=$cbo_season_name";

	}
	
	
	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if(trim($txt_job_no)!="") $job_no=trim($txt_job_no); else $job_no="%%";
	
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}
	else $year_cond="";
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	if($cbo_date_type==3) $date_cond="";else $date_cond=$date_cond;
	
	$po_id_cond="";
	if(trim(str_replace("'","",$txt_order_no))!="")
	{
		if(str_replace("'","",$hide_order_id)!="") $po_id_cond=" and b.id in(".str_replace("'","",$hide_order_id).")";
		else $po_id_cond=" and LOWER(b.po_number) like LOWER('%".trim(str_replace("'","",$txt_order_no))."%')";
	}
	
	$shipping_status_cond='';
	if(str_replace("'","",$shipping_status)!=0) $shipping_status_cond=" and b.shiping_status=$shipping_status";
	
	$exchange_rate=76; $po_ids_array=array();
		
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
		 if($cbo_date_type==3){
		$ref_closing_po_arr=return_library_array( "select b.inv_pur_req_mst_id as po_id,max(b.closing_date) as closing_date
		from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0 and b.closing_date  between $txt_date_from and $txt_date_to group by b.inv_pur_req_mst_id", "po_id", "closing_date");
		//echo "select b.inv_pur_req_mst_id as po_id,max(b.closing_date) as closing_date
		//from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0 and b.closing_date  between $txt_date_from and $txt_date_to group by b.inv_pur_req_mst_id";	
		foreach($ref_closing_po_arr as $po_id=>$ids){
			$poArr[$po_id]=$po_id;
		}
		$poIdsArr=array_chunk($poArr,999);
		$ref_po_cond_for_in=" and (";
		//$po_cond_for_in2=" and (";
		foreach($poIdsArr as $ids){
			$ref_po_cond_for_in.=" b.id in(".implode(",",$ids).") or"; 
			//$po_cond_for_in2.=" b.po_break_down_id in(".implode(",",$ids).") or"; 
		}
		$ref_po_cond_for_in=chop($ref_po_cond_for_in,'or ');
		$ref_po_cond_for_in.=")";
	}
	else
	{
		$ref_po_cond_for_in="";
	}
	
	$sql="select a.SEASON_BUYER_WISE,a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name,a.team_leader, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv, b.id, b.po_number,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status, c.exchange_rate from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=b.job_no_mst and c.entry_from=158 and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=$cbo_order_status $date_cond $buyer_id_cond $po_id_cond $ref_cond $file_cond $season_cond $shipping_status_cond $year_cond $ref_po_cond_for_in order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
	  //echo $sql; die;
	$result=sql_select($sql);
	$all_po_id="";  $all_jobs=''; $exchangepo_rateArr=array(); $exchangejob_rateArr=array();
	foreach($result as $row)
	{
		//if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		//if($all_jobs=="") $all_jobs="'".$row[csf("job_no")]."'"; else $all_jobs.=",'".$row[csf("job_no")]."'";
		$all_jobs[$row[csf("job_no")]]=$row[csf("job_no")];
		$all_po_id[$row[csf("id")]]=$row[csf("id")];
		$exchangepo_rateArr[$row[csf("id")]]=$row[csf("exchange_rate")];
		$exchangejob_rateArr[$row[csf("job_no")]]=$row[csf("exchange_rate")];
	}
	//$all_jobs=implode(",",array_unique(explode(",",$all_jobs)));
	//$all_jobs=implode("','",$all_jobs);
	
	$po_cond_for_in=""; $po_cond_for_in2="";$po_cond_for_in3=""; $po_cond_for_in4=""; $po_cond_for_in5=""; 
	$po_ids=count($all_po_id);
	if($db_type==2 && $po_ids>1000)
	{
		$po_cond_for_in=" and (";
		$po_cond_for_in2=" and (";
		$po_cond_for_in3=" and (";
		$po_cond_for_in4=" and (";
		$poIdsArr=array_chunk($all_po_id,999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
			$po_cond_for_in2.=" po_id in($ids) or"; 
			$po_cond_for_in3.=" b.order_id in($ids) or";
			$po_cond_for_in4.=" b.po_breakdown_id in($ids) or"; 
			$po_cond_for_in5.=" c.po_break_down_id in($ids) or"; 
		}
		$po_cond_for_in=chop($po_cond_for_in,'or ');
		$po_cond_for_in.=")";
		
		$po_cond_for_in2=chop($po_cond_for_in2,'or ');
		$po_cond_for_in2.=")";
		$po_cond_for_in3=chop($po_cond_for_in3,'or ');
		$po_cond_for_in3.=")";
		$po_cond_for_in4=chop($po_cond_for_in4,'or ');
		$po_cond_for_in4.=")";
		$po_cond_for_in5=chop($po_cond_for_in5,'or ');
		$po_cond_for_in5.=")";
	}
	else
	{
		$poIds=implode(",",$all_po_id);
		
		if($poIds==""){$po_cond_for_in="";}else{$po_cond_for_in=" and b.po_break_down_id in($poIds)";}
		if($poIds==""){$po_cond_for_in2="";}else{$po_cond_for_in2=" and po_id in($poIds)";}
		if($poIds==""){$po_cond_for_in3="";}else{$po_cond_for_in3=" and b.order_id in($poIds)";}
		if($poIds==""){$po_cond_for_in4="";}else{$po_cond_for_in4=" and b.po_breakdown_id in($poIds)";}
		if($poIds==""){$po_cond_for_in5="";}else{$po_cond_for_in5=" and c.po_break_down_id in($poIds)";}
	}
		
		
		
		$ex_factory_arr=return_library_array( "select b.po_break_down_id, 
	sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty 
	from pro_ex_factory_mst b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id", "po_break_down_id", "qnty");
	
	ob_start();
	?>
    <fieldset>
    	<table width="3660">
            <tr class="form_caption">
                <td colspan="27" align="center"><strong>Post Costing Report</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="27" align="center"><strong><? echo $company_arr[$company_name];?></strong>
                    <br>
                    <strong><? echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?></strong>
                </td>
            </tr>
        </table>
        <table style="margin-top:10px" id="table_header_1" class="rpt_table" width="3760" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="70">Buyer</th>
				<th width="100">Team Leader</th>
                <th width="70">Job No</th>
                <th width="70">Season</th>
                <th width="60">Job Year</th>
                <th width="110">Style Name</th>
                <th width="120">Garments Item</th>
                <th width="100">PO No</th>
                <th width="90">PO Quantity(Pcs)</th>
                <th width="70">Unit Price</th>
                <th width="110">PO Value</th>
                <th width="100">SMV</th>
                <th width="80">Shipment Date</th>
                <th width="100">Shipping Status</th>
                <th width="100">Cost Source</th>
                <th width="100">PO/Ex-Factory Qnty</th>
                <th width="110">PO/Ex-Factory Value</th>
                <th width="110">Yarn Cost</th>
                <th width="110">Knitting Cost</th>
                <th width="110">Dye & Fin Cost</th>
                <th width="110">Fabric Purchase Cost</th>
                <th width="110">AOP Cost</th>
                <th width="110">Trims Cost</th>
                <th width="100">Embellishment Cost</th>
                <th width="100">Wash Cost</th>
                <th width="100">Commercial Cost</th>
                <th width="100">Freight Cost</th>
                <th width="100">Testing Cost</th>
                <th width="100">Inspection Cost</th>
                <th width="100">Courier Cost</th>
				<th width="100">Total Mat & Serv.Cost</th>
                <th width="100">Commission Cost</th>
				<th width="100">Net P.O/Ex-Fact. Value</th>                
				<th width="100">CM Value (Contribution)</th> 
                <th width="100">CM Cost</th>
                <th width="100">Margin</th>
                <th  width="100">% to Ex-Factory Value</th>
				<th width="">Company</th>
            </thead>
        </table>
    	<div style="width:3782px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="3760" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?
				$fabriccostArray=array(); $yarncostArray=array(); $trimsCostArray=array(); $prodcostArray=array(); $actualCostArray=array(); $actualTrimsCostArray=array(); 
				$subconCostArray=array(); $embellCostArray=array(); $washCostArray=array(); $aopCostArray=array(); $yarnTrimsCostArray=array(); 
				
				
				$fabriccostDataArray=sql_select("select job_no, costing_per_id, embel_cost, wash_cost, cm_cost, commission, currier_pre_cost, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0 ".where_con_using_array($all_jobs,1,'job_no')."");
				
				foreach($fabriccostDataArray as $fabRow)
				{
					 $fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['wash_cost']=$fabRow[csf('wash_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				}
				unset($fabriccostDataArray);
				
				$trimscostDataArray=sql_select("select b.po_break_down_id, (b.cons*a.rate) as total from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($all_po_id,0,'b.po_break_down_id')."");
				foreach($trimscostDataArray as $trimsRow)
				{
					 $trimsCostArray[$trimsRow[csf('po_break_down_id')]]+=$trimsRow[csf('total')];
				}
				unset($trimscostDataArray);
				 
					
				
				$actualCostDataArray=sql_select("select cost_head,po_id,(amount_usd) as amount_usd from wo_actual_cost_entry where company_id=$company_name and status_active=1 and is_deleted=0 $po_cond_for_in2 ");
				
				foreach($actualCostDataArray as $actualRow)
				{
				   $actualCostArray[$actualRow[csf('cost_head')]][$actualRow[csf('po_id')]]+=$actualRow[csf('amount_usd')];
				}
				unset($actualCostDataArray);
		
				
				$subconInBillDataArray=sql_select("select b.order_id, 
									  sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
									  sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
									  from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id in(2,4) $po_cond_for_in3  group by b.order_id");
				foreach($subconInBillDataArray as $subRow)
				{
					$subconCostArray[$subRow[csf('order_id')]]['knit_bill']=$subRow[csf('knit_bill')];
					$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']=$subRow[csf('dye_finish_bill')];
				}
				unset($subconInBillDataArray);	
				
				$subconOutBillDataArray=sql_select("select b.order_id, 
									  sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
									  sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
									  from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,4) $po_cond_for_in3 group by b.order_id");
				foreach($subconOutBillDataArray as $subRow)
				{
					$subconCostArray[$subRow[csf('order_id')]]['knit_bill']+=$subRow[csf('knit_bill')];
					$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']+=$subRow[csf('dye_finish_bill')];
				}
				unset($subconOutBillDataArray);
				
				$embell_type_arr=return_library_array( "select id, emb_name from wo_pre_cost_embe_cost_dtls where 1=1  ".where_con_using_array($all_jobs,1,'job_no')."", "id", "emb_name");	
				
				$bookingDataArray=sql_select("select a.booking_type, a.item_category, a.currency_id, b.exchange_rate, b.po_break_down_id, b.process, b.amount, b.pre_cost_fabric_cost_dtls_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4,12,25) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in");
				foreach($bookingDataArray as $woRow)
				{
					$amount=0; $trimsAmnt=0;
					$exchange_rate=0;
					$exchange_rate=$exchangepo_rateArr[$woRow[csf("po_break_down_id")]];
					if($woRow[csf('currency_id')]==1) { $amount=$woRow[csf('amount')]/$exchange_rate; } else { $amount=$woRow[csf('amount')]; }
					
					if($woRow[csf('item_category')]==25 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						if($embell_type_arr[$woRow[csf('pre_cost_fabric_cost_dtls_id')]]==3)
						{
							$washCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
						else
						{
							$embellCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
					}
					else if($woRow[csf('item_category')]==12 && $woRow[csf('process')]==35 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						$aopCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
					}
					else if($woRow[csf('item_category')]==4)
					{
						if($woRow[csf('currency_id')]==1) { $trimsAmnt=$woRow[csf('amount')]/$woRow[csf('exchange_rate')]; } else { $trimsAmnt=$woRow[csf('amount')]; }
						$actualTrimsCostArray[$woRow[csf('po_break_down_id')]]+=$trimsAmnt; 
					}
				}
				unset($bookingDataArray);
				
				//$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
				
				$yarnTrimsDataArray=sql_select("select b.po_breakdown_id, b.prod_id, a.item_category, 
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose!=2 THEN a.cons_rate*b.quantity ELSE 0 END) AS yarn_iss_qty,
					sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN a.cons_rate*b.quantity ELSE 0 END) AS yarn_iss_return_qty,
					sum(CASE WHEN a.transaction_type=5 and b.entry_form ='11' and b.trans_type=5 THEN a.cons_rate*b.quantity ELSE 0 END) AS trans_in_qty_yarn,
					sum(CASE WHEN a.transaction_type=6 and b.entry_form ='11' and b.trans_type=6 THEN a.cons_rate*b.quantity ELSE 0 END) AS trans_out_qty_yarn,
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='25' and b.trans_type=2 THEN a.cons_rate*b.quantity ELSE 0 END) AS trims_issue_amnt
					from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in4  group by b.po_breakdown_id, b.prod_id, a.item_category");
					
				foreach($yarnTrimsDataArray as $invRow)
				{
					$exchange_rate=0;
					$exchange_rate=$exchangepo_rateArr[$invRow[csf("po_breakdown_id")]];
					if($invRow[csf('item_category')]==1)
					{
						$issAmt=$invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')]-$invRow[csf('yarn_iss_return_qty')]-$invRow[csf('trans_out_qty_yarn')];
						$rate='';
						
						
						$iss_amnt=$issAmt/$exchange_rate;
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][1]+=$iss_amnt;
					}
					else
					{
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][4]+=$invRow[csf('trims_issue_amnt')];
					}
				}
				unset($yarnTrimsDataArray);
				
				//$ex_rate=76;
				$sql_fin_purchase="select b.po_breakdown_id, (a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis<>9 and a.item_category=2 and a.transaction_type=1 and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in4 ";
				
				$dataArrayFinPurchase=sql_select($sql_fin_purchase);
				foreach($dataArrayFinPurchase as $finRow)
				{
					$ex_rate=0;
					$ex_rate=$exchangepo_rateArr[$finRow[csf("po_breakdown_id")]];
					$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]+=$finRow[csf('finish_purchase_amnt')]/$ex_rate;
				}
				unset($dataArrayFinPurchase);
				
				$sql_fin_purchase_wv="select b.po_breakdown_id, (a.cons_rate*b.quantity) as woven_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=3 and a.transaction_type=1 and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_cond_for_in4";
				
				$dataArrayFinPurchaseW=sql_select($sql_fin_purchase_wv);
				foreach($dataArrayFinPurchaseW as $finRow)
				{
					$ex_rate=0;
					$ex_rate=$exchangepo_rateArr[$finRow[csf("po_breakdown_id")]];
					$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]+=$finRow[csf('woven_purchase_amnt')]/$ex_rate;
				}
				unset($dataArrayFinPurchaseW);
				
				$i=1; $tot_po_qnty=0; $tot_po_value=0; $tot_ex_factory_qnty=0; $tot_ex_factory_val=0; $tot_yarn_cost_mkt=0; $tot_knit_cost_mkt=0; $tot_dye_finish_cost_mkt=0; $tot_yarn_dye_cost_mkt=0; $tot_aop_cost_mkt=0; $tot_trims_cost_mkt=0; $tot_embell_cost_mkt=0; $tot_wash_cost_mkt=0; $tot_commission_cost_mkt=0; $tot_comm_cost_mkt=0; $tot_freight_cost_mkt=0; $tot_test_cost_mkt=0; $tot_inspection_cost_mkt=0; $tot_currier_cost_mkt=0; $tot_cm_cost_mkt=0; $tot_mkt_all_cost=0; $tot_mkt_margin=0; $tot_yarn_cost_actual=0; $tot_knit_cost_actual=0; $tot_dye_finish_cost_actual=0; $tot_yarn_dye_cost_actual=0; $tot_aop_cost_actual=0; $tot_trims_cost_actual=0; $tot_embell_cost_actual=0; $tot_wash_cost_actual=0; $tot_commission_cost_actual=0; $tot_comm_cost_actual=0; $tot_freight_cost_actual=0; $tot_test_cost_actual=0; $tot_inspection_cost_actual=0; $tot_currier_cost_actual=0; $tot_cm_cost_actual=0; $tot_actual_all_cost=0; $tot_actual_margin=0; $tot_fabric_purchase_cost_mkt=0; $tot_fabric_purchase_cost_actual=0;
				
				$buyer_name_array=array();

				$mkt_po_val_array = array(); $mkt_yarn_array = array(); $mkt_knit_array = array(); $mkt_dy_fin_array = array(); $mkt_yarn_dy_array = array();
				$mkt_aop_array = array(); $mkt_trims_array = array(); $mkt_emb_array = array(); $mkt_wash_array=array(); $mkt_commn_array=array(); $mkt_commercial_array=array();
				$mkt_freight_array = array(); $mkt_test_array = array(); $mkt_ins_array = array(); $mkt_courier_array = array(); $mkt_cm_array = array();
				$mkt_total_array = array(); $mkt_margin_array = array(); $mkt_fabric_purchase_array = array();
				
				$ex_factory_val_array= array(); $yarn_cost_array= array(); $knit_cost_array= array(); $dye_cost_array= array(); $yarn_dyeing_cost_array= array();
				$aop_n_others_cost_array= array(); $trims_cost_array= array(); $enbellishment_cost_array= array(); $wash_cost_array= array(); $commission_cost_array= array(); 
				$commercial_cost_array= array(); $freight_cost_array= array(); $testing_cost_array= array(); $inspection_cost_array= array(); $courier_cost_array= array();
				$cm_cost_array= array(); $total_cost_array= array(); $margin_array= array(); $ex_factory_array=array(); $actual_cm_amnt=array(); $actual_fabric_purchase_array = array();
				if($db_type==0) $jobYearCond="and YEAR(a.insert_date)=$cbo_year";
				else if($db_type==2) $jobYearCond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
				 $condition= new condition();
				 $condition->company_name("=$company_name");
				 if(str_replace("'","",$cbo_buyer_name)>0){
					  $condition->buyer_name("=$cbo_buyer_name");
				 }
				 if(str_replace("'","",$cbo_year)!=0){
					$condition->job_year("$jobYearCond"); 
				}
				 if(str_replace("'","",$txt_job_no) !=''){
					  $condition->job_no_prefix_num("=$txt_job_no");
				 }
				if(trim(str_replace("'","",$txt_order_no))!='')
				 {
					$condition->po_number(" like '%".trim(str_replace("'","",$txt_order_no))."%'"); 
				 }
				 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
				 {
					 $start_date=str_replace("'","",$txt_date_from);
					 $end_date=str_replace("'","",$txt_date_to);
					 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
				 }
				 if(str_replace("'","",$cbo_order_status) >0){
					  $condition->is_confirmed("=$cbo_order_status");
				 }
				 if(str_replace("'","",$cbo_order_status)==0){
					  $condition->is_confirmed("in(1,2,3)");
				 }
				 if(count($all_po_id))
				 {
					$all_po_id=implode(',',$all_po_id);
					$condition->po_id_in("$all_po_id");
				 }
				
				/* if(str_replace("'","",$txt_job_no)=='')
				 {
					$condition->job_no("in($all_jobs)");
				 }*/
				 
				 $condition->init();
				
				 $yarn= new yarn($condition);
				 $yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				 $conversion= new conversion($condition);
				 $conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
				 $trims= new trims($condition);
				 $trims_costing_arr=$trims->getAmountArray_by_order();
				$emblishment= new emblishment($condition);
				$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
				$commission= new commision($condition);
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$commercial= new commercial($condition);
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				$other= new other($condition);
				$other_costing_arr=$other->getAmountArray_by_order();
				$wash= new wash($condition);
				$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
				$fabric= new fabric($condition);
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				
				//echo 1;die;
				
				//print_r($fabric_costing_arr);die;
				$not_yarn_dyed_cost_arr=array(1,2,30,35);
				foreach($result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$exchange_rate=$row[csf('exchange_rate')];
					$po_ids_array[]=$row[csf('id')];
					$gmts_item='';
					$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
					foreach($gmts_item_id as $item_id)
					{
						if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
					}
					
					$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
					$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
					$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
					$po_value=$order_qnty_in_pcs*$unit_price;
					
					$tot_po_qnty+=$order_qnty_in_pcs; 
					$tot_po_value+=$po_value;
					
					$ex_factory_qty=$ex_factory_arr[$row[csf('id')]];
					$ex_factory_value=$ex_factory_qty*$unit_price;
					
					$tot_ex_factory_qnty+=$ex_factory_qty; 
					$tot_ex_factory_val+=$ex_factory_value; 
					
					$dzn_qnty=0;
					$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
					if($costing_per_id==1) $dzn_qnty=12;
					else if($costing_per_id==3) $dzn_qnty=12*2;
					else if($costing_per_id==4) $dzn_qnty=12*3;
					else if($costing_per_id==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;

					$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
					$yarn_cost_mkt=$yarn_costing_arr[$row[csf('id')]];
					$knit_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][1]);
					$dye_finish_cost_mkt=0;
					foreach($conversion_cost_head_array as $process_id=>$val)
					{
						if(!in_array($process_id,$not_yarn_dyed_cost_arr))
						{
							$dye_finish_cost_mkt+=array_sum($conversion_costing_arr_process[$row[csf('id')]][$process_id]);
						}
					}					

					$yarn_dye_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][30]);
					$aop_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][35]);
					$trims_cost_mkt=fn_number_format($trims_costing_arr[$row[csf('id')]],2,'.','');
					
					$print_amount=$emblishment_costing_arr_name[$row[csf('id')]][1];
					$embroidery_amount=$emblishment_costing_arr_name[$row[csf('id')]][2];
					$special_amount=$emblishment_costing_arr_name[$row[csf('id')]][4];
					$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('id')]][3];
					$other_amount=$emblishment_costing_arr_name[$row[csf('id')]][5];
					$foreign_cost=$commission_costing_arr[$row[csf('id')]][1];
					$local_cost=$commission_costing_arr[$row[csf('id')]][2];
					
					$test_cost=$other_costing_arr[$row[csf('id')]]['lab_test'];
					$freight_cost=$other_costing_arr[$row[csf('id')]]['freight'];
					$inspection_cost=$other_costing_arr[$row[csf('id')]]['inspection'];
					$certificate_cost=$other_costing_arr[$row[csf('id')]]['certificate_pre_cost'];
					$currier_cost=$other_costing_arr[$row[csf('id')]]['currier_pre_cost'];
					$cm_cost=$other_costing_arr[$row[csf('id')]]['cm_cost'];
					
					$embell_cost_mkt=$print_amount+$embroidery_amount+$special_amount+$other_amount;
					
					$wash_cost_mkt=$wash_cost;
					$commission_cost_mkt=$foreign_cost+$local_cost;
					$comm_cost_mkt=$commercial_costing_arr[$row[csf('id')]];
					$freight_cost_mkt=$freight_cost;
					$test_cost_mkt=$test_cost;
					$inspection_cost_mkt=$inspection_cost;
					$currier_cost_mkt=$currier_cost;
					$cm_cost_mkt=$cm_cost;
					
					$fabric_purchase_cost_mkt=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('id')]])+array_sum($fabric_costing_arr['woven']['grey'][$row[csf('id')]]);
					//$mkt_all_cost=$yarn_cost_mkt+$knit_cost_mkt+$dye_finish_cost_mkt+$yarn_dye_cost_mkt+$aop_cost_mkt+$trims_cost_mkt+$embell_cost_mkt+$wash_cost_mkt+$commission_cost_mkt+$comm_cost_mkt+$freight_cost_mkt+$test_cost_mkt+$inspection_cost_mkt+$currier_cost_mkt+$cm_cost_mkt+$fabric_purchase_cost_mkt;
					
					$mkt_all_cost=$yarn_cost_mkt+$yarn_dye_cost_mkt+$knit_cost_mkt+$dye_finish_cost_mkt+$fabric_purchase_cost_mkt+$aop_cost_mkt+$trims_cost_mkt+$embell_cost_mkt+$wash_cost_mkt+$comm_cost_mkt+$freight_cost_mkt+$test_cost_mkt+$inspection_cost_mkt+$currier_cost_mkt;
					
					//$mkt_margin=$po_value-$mkt_all_cost;
					$mkt_margin=$po_value-($mkt_all_cost+$commission_cost_mkt);
					$mkt_margin_perc=($mkt_margin/$po_value)*100;
					
					$yarn_cost_actual=$yarnTrimsCostArray[$row[csf('id')]][1];
					$trims_cost_actual=fn_number_format($actualTrimsCostArray[$row[csf('id')]],2,'.','');
					$yarn_dye_cost_actual=0;
					$aop_cost_actual=$aopCostArray[$row[csf('id')]];
					$embell_cost_actual=$embellCostArray[$row[csf('id')]];
					$wash_cost_actual=$washCostArray[$row[csf('id')]];
					$knit_cost_actual=$subconCostArray[$row[csf('id')]]['knit_bill']/$exchange_rate;
					$dye_finish_cost_actual=$subconCostArray[$row[csf('id')]]['dye_finish_bill']/$exchange_rate;
					$commission_cost_actual=($ex_factory_qty/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['commission'];
					$comm_cost_actual=fn_number_format($comm_cost_mkt,2,'.','');
					//$comm_cost_actual=fn_number_format($actualCostArray[6][$row[csf('id')]],2,'.','');
					$freight_cost_actual=fn_number_format($actualCostArray[2][$row[csf('id')]],2,'.','');
					$test_cost_actual=fn_number_format($test_cost,2,'.','');
					//$test_cost_actual=fn_number_format($actualCostArray[1][$row[csf('id')]],2,'.','');
					$inspection_cost_actual=fn_number_format($actualCostArray[3][$row[csf('id')]],2,'.','');
					$currier_cost_actual=fn_number_format($actualCostArray[4][$row[csf('id')]],2,'.','');
					$cm_cost_actual=fn_number_format($actualCostArray[5][$row[csf('id')]],2,'.','');
					
					$fabric_purchase_cost_actual=$finish_purchase_amnt_arr[$row[csf('id')]];
					
					/*$actual_all_cost=$yarn_cost_actual+$knit_cost_actual+$dye_finish_cost_actual+$yarn_dye_cost_actual+$aop_cost_actual+$trims_cost_actual+$embell_cost_actual+$wash_cost_actual+$commission_cost_actual+$comm_cost_actual+$freight_cost_actual+$test_cost_actual+$inspection_cost_actual+$currier_cost_actual+$cm_cost_actual+$fabric_purchase_cost_actual;*/
					
					
					$actual_all_cost=$yarn_cost_actual+$knit_cost_actual+$dye_finish_cost_actual+$yarn_dye_cost_actual+$aop_cost_actual+$trims_cost_actual+$embell_cost_actual+$wash_cost_actual+$comm_cost_actual+$freight_cost_actual+$test_cost_actual+$inspection_cost_actual+$currier_cost_actual+$fabric_purchase_cost_actual;
					$actual_margin_perc=0;
					$actual_margin=$ex_factory_value-$actual_all_cost;
					$actual_margin_perc=($actual_margin/$ex_factory_value)*100;
					
					$tot_yarn_cost_mkt+=$yarn_cost_mkt; 
					$tot_knit_cost_mkt+=$knit_cost_mkt; 
					$tot_dye_finish_cost_mkt+=$dye_finish_cost_mkt; 
					$tot_yarn_dye_cost_mkt+=$yarn_dye_cost_mkt; 
					$tot_aop_cost_mkt+=$aop_cost_mkt; 
					$tot_trims_cost_mkt+=$trims_cost_mkt; 
					$tot_embell_cost_mkt+=$embell_cost_mkt; 
					$tot_wash_cost_mkt+=$wash_cost_mkt; 
					$tot_commission_cost_mkt+=$commission_cost_mkt; 
					$tot_comm_cost_mkt+=$comm_cost_mkt; 
					$tot_freight_cost_mkt+=$freight_cost_mkt; 
					$tot_test_cost_mkt+=$test_cost_mkt; 
					$tot_inspection_cost_mkt+=$inspection_cost_mkt; 
					$tot_currier_cost_mkt+=$currier_cost_mkt; 
					$tot_cm_cost_mkt+=$cm_cost_mkt; 
					$tot_mkt_all_cost+=$mkt_all_cost; 
					$tot_mkt_margin+=$mkt_margin; 
					$tot_yarn_cost_actual+=$yarn_cost_actual; 
					$tot_knit_cost_actual+=$knit_cost_actual; 
					$tot_dye_finish_cost_actual+=$dye_finish_cost_actual;
					$tot_yarn_dye_cost_actual+=$yarn_dye_cost_actual; 
					$tot_aop_cost_actual+=$aop_cost_actual; 
					$tot_trims_cost_actual+=$trims_cost_actual; 
					$tot_embell_cost_actual+=$embell_cost_actual;
					$tot_wash_cost_actual+=$wash_cost_actual; 
					$tot_commission_cost_actual+=$commission_cost_actual; 
					$tot_comm_cost_actual+=$comm_cost_actual; 
					$tot_freight_cost_actual+=$freight_cost_actual; 
					$tot_test_cost_actual+=$test_cost_actual; 
					$tot_inspection_cost_actual+=$inspection_cost_actual; 
					$tot_currier_cost_actual+=$currier_cost_actual; 
					$tot_cm_cost_actual+=$cm_cost_actual; 
					$tot_actual_all_cost+=fn_number_format($actual_all_cost,2,'.',''); 
					$tot_actual_margin+=$actual_margin;
					
					$tot_fabric_purchase_cost_mkt+=$fabric_purchase_cost_mkt;
					$tot_fabric_purchase_cost_actual+=$fabric_purchase_cost_actual;
					
					$buyer_name_array[$row[csf('buyer_name')]]=$buyer_arr[$row[csf('buyer_name')]];
					$mkt_po_val_array[$row[csf('buyer_name')]]+=$po_value;
					$mkt_yarn_array[$row[csf('buyer_name')]]+=$yarn_cost_mkt+$yarn_dye_cost_mkt;
					$mkt_knit_array[$row[csf('buyer_name')]]+=$knit_cost_mkt;
					$mkt_dy_fin_array[$row[csf('buyer_name')]]+=$dye_finish_cost_mkt;
					//$mkt_yarn_dy_array[$row[csf('buyer_name')]]+=$yarn_dye_cost_mkt;
					$mkt_aop_array[$row[csf('buyer_name')]]+=$aop_cost_mkt;
					$mkt_trims_array[$row[csf('buyer_name')]]+=$trims_cost_mkt;
					$mkt_emb_array[$row[csf('buyer_name')]]+=$embell_cost_mkt;
					$mkt_wash_array[$row[csf('buyer_name')]]+=$wash_cost_mkt;
					$mkt_commn_array[$row[csf('buyer_name')]]+=$commission_cost_mkt;
					$mkt_commercial_array[$row[csf('buyer_name')]]+=$comm_cost_mkt;
					$mkt_freight_array[$row[csf('buyer_name')]]+=$freight_cost_mkt;
					$mkt_test_array[$row[csf('buyer_name')]]+=$test_cost_mkt;
					$mkt_ins_array[$row[csf('buyer_name')]]+=$inspection_cost_mkt;
					$mkt_courier_array[$row[csf('buyer_name')]]+=$currier_cost_mkt;
					$mkt_cm_array[$row[csf('buyer_name')]]+=$cm_cost_mkt;
					$mkt_total_array[$row[csf('buyer_name')]]+=$mkt_all_cost;
					$mkt_margin_array[$row[csf('buyer_name')]]+=$mkt_margin;
					$mkt_fabric_purchase_array[$row[csf('buyer_name')]]+=$fabric_purchase_cost_mkt;
					
					$ex_factory_val_array[$row[csf('buyer_name')]]+=$ex_factory_value;
					$yarn_cost_array[$row[csf('buyer_name')]]+=$yarn_cost_actual;
					$knit_cost_array[$row[csf('buyer_name')]]+=$knit_cost_actual;
					$dye_cost_array[$row[csf('buyer_name')]]+=$dye_finish_cost_actual;
					
					$aop_n_others_cost_array[$row[csf('buyer_name')]]+=$aop_cost_actual;
					$trims_cost_array[$row[csf('buyer_name')]]+=$trims_cost_actual;
					$enbellishment_cost_array[$row[csf('buyer_name')]]+=$embell_cost_actual;
					$wash_cost_array[$row[csf('buyer_name')]]+=$wash_cost_actual;
					$commission_cost_array[$row[csf('buyer_name')]]+=$commission_cost_actual;
					$commercial_cost_array[$row[csf('buyer_name')]]+=$comm_cost_actual;
					$freight_cost_array[$row[csf('buyer_name')]]+=$freight_cost_actual;
					$testing_cost_array[$row[csf('buyer_name')]]+=$test_cost_actual;
					$inspection_cost_array[$row[csf('buyer_name')]]+=$inspection_cost_actual;
					$courier_cost_array[$row[csf('buyer_name')]]+=$currier_cost_actual;
					$cm_cost_array[$row[csf('buyer_name')]]+=$cm_cost_actual;
					$total_cost_array[$row[csf('buyer_name')]]+=$actual_all_cost;
					$margin_array[$row[csf('buyer_name')]]+=$actual_margin;
					$actual_fabric_purchase_array[$row[csf('buyer_name')]]+=$fabric_purchase_cost_actual;
					
					
				 $button_po="<a href='#' onClick=\"generate_po_report('".$company_name."','".$row[csf('id')]."','".$row[csf('job_no')]."','show_po_detail_report','1')\" '> ".$row[csf('po_number')]."<a/>";
					
				?>
                	<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="70"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
						<td width="100"><? echo $team_leader_arr[$row[csf('team_leader')]]; ?></td>
                        <td width="70" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                        <td width="70" align="center"><? echo $season_arr[$row[csf('SEASON_BUYER_WISE')]]; ?></td>
                        <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                        <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="120"><p><? echo $gmts_item; ?></p></td>
                        <td width="100"><p><? echo  $button_po; ?></p></td>
                        <td width="88" align="right" style="padding-right:2px"><? echo $order_qnty_in_pcs; ?></td>
                        <td width="68" align="right" style="padding-right:2px"><? echo fn_number_format($unit_price,2); ?></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo fn_number_format($po_value,2); ?></td>
                        <td width="98" align="right" style="padding-right:2px"><? echo fn_number_format($row[csf('set_smv')],2); ?></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        <td width="100"><p><? echo $shipment_status[$row[csf('shiping_status')]]; ?></p></td>
                        
                        <td width="100" align="left"><b>Pre Costing</b></td>
                        <td width="98" align="right" style="padding-right:2px"><? echo fn_number_format($order_qnty_in_pcs,0); ?></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo fn_number_format($po_value,2); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo  fn_number_format($yarn_cost_mkt+$yarn_dye_cost_mkt,2); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($knit_cost_mkt,2); ?></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo fn_number_format($dye_finish_cost_mkt,2); ?></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo fn_number_format($fabric_purchase_cost_mkt,2); ?></td>
                        
                        <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($aop_cost_mkt,2); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($trims_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($embell_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($wash_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($comm_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($freight_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($test_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($inspection_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($currier_cost_mkt,2); ?></td>
                        
                        <td width="100" align="right"><? echo fn_number_format($mkt_all_cost,2);?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($commission_cost_mkt,2); ?></td>

                        <td width="100" align="right">
						<? 	
							$Net_PO_ex_Fact_Value_mkt=$po_value-$commission_cost_mkt;
							echo fn_number_format($Net_PO_ex_Fact_Value_mkt,2);
							$tot_Net_PO_ex_Fact_Value_mkt+= $Net_PO_ex_Fact_Value_mkt;
						?>
                        </td>
                        <td width="100" align="right">
						<? 
							$CM_Value_Contribution_value_mkt=$Net_PO_ex_Fact_Value_mkt-$mkt_all_cost;
							echo fn_number_format($CM_Value_Contribution_value_mkt,2); 
							$tot_CM_Value_Contribution_value_mkt+= $CM_Value_Contribution_value_mkt;
						?>
                        </td>
                        
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($cm_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right">
						<? 
							$mkt_margin=$CM_Value_Contribution_value_mkt-$cm_cost_mkt; 
							echo fn_number_format($mkt_margin,2); 
							$total_mkt_margin+=$mkt_margin;
						?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($mkt_margin_perc,2); ?></td>
						 <td><? echo $company_short_arr[$row[csf('company_name')]]; ?></td>
                    </tr>
                    
                    <tr bgcolor="#F5F5F5" onClick="change_color('tr_a<? echo $i; ?>','#F5F5F5')" id="tr_a<? echo $i; ?>">
                    	
                        <td width="40"><? echo $i; ?></td>
                        <td width="70"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
						<td width="100"><? echo $team_leader_arr[$row[csf('team_leader')]]; ?></td>
                        <td width="70" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                        <td width="70" align="center"><? echo $season_arr[$row[csf('SEASON_BUYER_WISE')]]; ?></td>
                        <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                        <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="120"><p><? echo $gmts_item; ?></p></td>
                        <td width="100"><p><? echo  $button_po; ?></p></td>
                        <td width="88" align="right" style="padding-right:2px"><? echo $order_qnty_in_pcs; ?></td>
                        <td width="68" align="right" style="padding-right:2px"><? echo fn_number_format($unit_price,2); ?></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo fn_number_format($po_value,2); ?></td>
                        <td width="98" align="right" style="padding-right:2px"><? echo fn_number_format($row[csf('set_smv')],2); ?></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        <td width="100"><p><? echo $shipment_status[$row[csf('shiping_status')]]; ?></p></td>
                        <td width="100" align="left"><b>Actual</b></td>
                        <td width="98" align="right" style="padding-right:2px">
						<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no_prefix_num')];?>','<? echo $row[csf('id')]; ?>','650px')"><? echo  fn_number_format($ex_factory_qty,0); ?></a>
						</td>
                        <td width="108" align="right" style="padding-right:2px"><? echo fn_number_format($ex_factory_value,2); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','yarn_cost_actual','Actual Yarn Cost Details','900px')"><? echo fn_number_format($yarn_cost_actual,2); ?></a></td>
                        <td width="108" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','knit_cost_actual','Actual Knitting Cost Details','900px')"><? echo fn_number_format($knit_cost_actual,2); ?></a></td>
                        <td width="108" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','dye_finish_cost_actual','Actual Dyeing & Finish Cost Details','1100px')"><? echo fn_number_format($dye_finish_cost_actual,2); ?></a></td>
                        <td width="108" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','fabric_purchase_cost_actual','Fabric Purchase Cost Details','900px')"><? echo fn_number_format($fabric_purchase_cost_actual,2); ?></a></td><!--vvvvv-->
                        
                        <td width="108" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','aop_cost_actual','AOP Cost Details','800px')"><? echo fn_number_format($aop_cost_actual,2); ?></a></td>
                        <td width="108" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','trims_cost_actual','Trims Cost Details','800px')"><? echo fn_number_format($trims_cost_actual,2); ?></a></td>
                        <td width="98" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','embell_cost_actual','Embellishment Cost Details','800px')"><? echo fn_number_format($embell_cost_actual,2); ?></a></td>
                        <td width="98" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','wash_cost_actual','Wash Cost Details','800px')"><? echo fn_number_format($wash_cost_actual,2); ?></a></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($comm_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($freight_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($test_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($inspection_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($currier_cost_actual,2); ?></td>
                        <td width="100" align="right"><? echo fn_number_format($actual_all_cost,2);?></td>
                        <td width="98" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]."_".$row[csf('job_no')]."_".$ex_factory_qty."_".$dzn_qnty; ?>','commission_cost_actual','Commission Cost Details','600px')"><? echo fn_number_format($commission_cost_actual,2); ?></a></td>
                        <td width="100" align="right">
						<? 
							
							
							//$Net_PO_ex_Fact_Value_actual=$po_value-$commission_cost_actual;
							$Net_PO_ex_Fact_Value_actual=$ex_factory_value-$commission_cost_actual;
							echo fn_number_format($Net_PO_ex_Fact_Value_actual,2);
							$tot_Net_PO_ex_Fact_Value_actual+=$Net_PO_ex_Fact_Value_actual;
						?>
                        </td>
                        <td width="100" align="right">
						<? 
							$CM_Value_Contribution_actual=$Net_PO_ex_Fact_Value_actual-$actual_all_cost;
							echo fn_number_format($CM_Value_Contribution_actual,2);
							$tot_CM_Value_Contribution_actual+=$CM_Value_Contribution_actual;
						?>
                        </td>
                        
                        
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($cm_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right">
						<? 
							$actual_margin=$CM_Value_Contribution_actual-$cm_cost_actual;
							echo fn_number_format($actual_margin,2);
							$total_actual_margin+=$actual_margin;
						?>
                        </td>
                        <td width="100" style="padding-right:2px" align="right">
						<? 
						echo  fn_number_format(($actual_margin/$ex_factory_value)*100,2);
						
						 //echo fn_number_format($actual_margin_perc,2); ?>
                        
                        </td>
						 <td><? echo $company_short_arr[$row[csf('company_name')]]; ?></td>
                    </tr>
                    
                    <tr bgcolor="#F5F5F5" onClick="change_color('tr_v<? echo $i; ?>','#F5F5F5')" id="tr_v<? echo $i; ?>">
                    	
                        <td width="40"><? echo $i; ?></td>
                        <td width="70"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
						<td width="100"><? echo $team_leader_arr[$row[csf('team_leader')]]; ?></td>
                        <td width="70" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                        <td width="70" align="center"><? echo $season_arr[$row[csf('SEASON_BUYER_WISE')]]; ?></td>
                        <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                        <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="120"><p><? echo $gmts_item; ?></p></td>
                        <td width="100"><p><? echo  $button_po; ?></p></td>
                        <td width="88" align="right" style="padding-right:2px"><? echo $order_qnty_in_pcs; ?></td>
                        <td width="68" align="right" style="padding-right:2px"><? echo fn_number_format($unit_price,2); ?></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo fn_number_format($po_value,2); ?></td>
                        <td width="98" align="right" style="padding-right:2px"><? echo fn_number_format($row[csf('set_smv')],2); ?></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        <td width="100"><p><? echo $shipment_status[$row[csf('shiping_status')]]; ?></p></td>
                        <td width="100" align="left"><b>Variance</b></td>
                        <td width="98" align="right" style="padding-right:2px"><? echo  fn_number_format($order_qnty_in_pcs-$ex_factory_qty,0); ?></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo fn_number_format($po_value-$ex_factory_value,2); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format(($yarn_cost_mkt+$yarn_dye_cost_mkt)-$yarn_cost_actual,2); ?></td>
                        
                        <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($knit_cost_mkt-$knit_cost_actual,2); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($dye_finish_cost_mkt-$dye_finish_cost_actual,2); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($fabric_purchase_cost_mkt-$fabric_purchase_cost_actual,2); ?></td>
                        
                        <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($aop_cost_mkt-$aop_cost_actual,2); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($trims_cost_mkt-$trims_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($embell_cost_mkt-$embell_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($wash_cost_mkt-$wash_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($comm_cost_mkt-$comm_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($freight_cost_mkt-$freight_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($test_cost_mkt-$test_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($inspection_cost_mkt-$inspection_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($currier_cost_mkt-$currier_cost_actual,2); ?></td>
                        <td width="100" align="right"><? echo fn_number_format($mkt_all_cost-$actual_all_cost,2);?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($commission_cost_mkt-$commission_cost_actual,2); ?></td>
                        <td width="100" align="right">
						<? 
							$Net_PO_ex_Fact_Value_actual=$ex_factory_value-$commission_cost_actual;
							echo fn_number_format($Net_PO_ex_Fact_Value_mkt-$Net_PO_ex_Fact_Value_actual,2);
							//$tot_Net_PO_ex_Fact_Value_actual+=$Net_PO_ex_Fact_Value_actual;
						?>
                        </td>
                        <td width="100" align="right">
						<? 
							$CM_Value_Contribution_actual=$Net_PO_ex_Fact_Value_actual-$actual_all_cost;
							if($CM_Value_Contribution_value_mkt<0 && $CM_Value_Contribution_actual<0)
							{
								$cm_variance_contribution_value=($CM_Value_Contribution_value_mkt)+($CM_Value_Contribution_actual);
								echo fn_number_format($cm_variance_contribution_value,2);
							}
							else if($CM_Value_Contribution_value_mkt>0)//Is greater than 0
							{
								$cm_variance_contribution_value=($CM_Value_Contribution_actual)-($CM_Value_Contribution_value_mkt);
								echo fn_number_format($cm_variance_contribution_value,2);
							}
							else if($CM_Value_Contribution_value_mkt<0)//Is Less than 0
							{
								$cm_variance_contribution_value=($CM_Value_Contribution_actual)+($CM_Value_Contribution_value_mkt);
								echo fn_number_format($cm_variance_contribution_value,2);
							}
							else
							{
								echo fn_number_format($CM_Value_Contribution_value_mkt-$CM_Value_Contribution_actual,2);
							}
							//$tot_CM_Value_Contribution_actual+=$CM_Value_Contribution_actual;
						?>
                        </td>
                        
                        
                        <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($cm_cost_mkt-$cm_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right">
						<? 
							$actual_margin=$CM_Value_Contribution_actual-$cm_cost_actual;
							if($mkt_margin<0 && $actual_margin<0)
							{
								$margin_variance_value=($mkt_margin)+($actual_margin);
								echo fn_number_format($margin_variance_value,2);
							}
							else if($mkt_margin>0) //Is greater than 0
							{
								$margin_variance_value=($actual_margin)-($mkt_margin);
								echo fn_number_format($margin_variance_value,2);
							}
							else if($mkt_margin<0) //Is Less than 0
							{
								$margin_variance_value=($actual_margin)+($mkt_margin);
								echo fn_number_format($margin_variance_value,2);
							}
							else
							{
								echo fn_number_format($mkt_margin-$actual_margin,2);
							}
							//$total_actual_margin+=$Net_PO_ex_Fact_Value_mkt-$actual_margin;
						?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
						<? 
						echo  fn_number_format($mkt_margin_perc-(($actual_margin/$ex_factory_value)*100),2);
						
						 //echo fn_number_format($actual_margin_perc,2); ?>
                        
                        </td>
						 <td><? echo $company_short_arr[$row[csf('company_name')]]; ?></td>
                    </tr>
                    
				<?
					$i++;
				}
				
				$tot_mkt_margin_perc=($tot_mkt_margin/$tot_po_value)*100;
				$tot_actual_margin_perc=($tot_actual_margin/$tot_ex_factory_val)*100;
				
				?>
            </table>
		</div>
        <table class="rpt_table" width="3760" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tr bgcolor="#CCDDEE" onClick="change_color('tr_pt','#CCDDEE')" id="tr_pt" style="font-weight:bold;">
                <td colspan="9" style="padding-right:4px" align="right">Total</td>
                <td width="90"><? echo fn_number_format($tot_po_qnty,0); ?></td>
                <td width="70">&nbsp;</td>
                <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($tot_po_value,2); ?></td>
                <td width="98" style="padding-right:2px" align="right">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">Pre Costing Total</td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_po_qnty,0); ?></td>
                <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($tot_po_value,2); ?></td>
                <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt,2); ?></td>
                <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($tot_knit_cost_mkt,2); ?></td>
                <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($tot_dye_finish_cost_mkt,2); ?></td>
                <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($tot_fabric_purchase_cost_mkt,2); ?></td>
                
                <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($tot_aop_cost_mkt,2); ?></td>
                <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($tot_trims_cost_mkt,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_embell_cost_mkt,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_wash_cost_mkt,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_comm_cost_mkt,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_freight_cost_mkt,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_test_cost_mkt,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_inspection_cost_mkt,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_currier_cost_mkt,2); ?></td>
                <td width="100" align="right"><? echo fn_number_format($tot_mkt_all_cost,2);?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_commission_cost_mkt,2); ?></td>
                <td width="100" align="right"><? echo fn_number_format($tot_Net_PO_ex_Fact_Value_mkt,2);?></td>
                <td width="100" align="right"><? echo fn_number_format($tot_CM_Value_Contribution_value_mkt,2);?></td>

                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_cm_cost_mkt,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($total_mkt_margin,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_mkt_margin_perc,2); ?></td>
				<td width="58" style="padding-right:2px" align="right"><? //echo fn_number_format($tot_mkt_margin_perc,2); ?></td>
            </tr>
            <tr bgcolor="#CCCCFF" onClick="change_color('tr_at','#CCCCFF')" id="tr_at" style="font-weight:bold;">
                <td colspan="9" style="padding-right:4px" align="right"></td>
                <td width="90">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="108" style="padding-right:2px" align="right">&nbsp;</td>
                <td width="98" style="padding-right:2px" align="right">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">Actual Total</td>
                <td width="98" align="right" style="padding-right:2px"><? echo fn_number_format($tot_ex_factory_qnty,0); ?></td>
                <td width="108" align="right" style="padding-right:2px"><? echo fn_number_format($tot_ex_factory_val,2); ?></td>
                <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($tot_yarn_cost_actual,2); ?></td>
                <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($tot_knit_cost_actual,2); ?></td>
                <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($tot_dye_finish_cost_actual,2); ?></td>
                <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($tot_fabric_purchase_cost_actual,2); ?></td>
               
                <td width="108" style="padding-right:2px" align="right"><? echo fn_number_format($tot_aop_cost_actual,2); ?></td>
                <td width="108" align="right" style="padding-right:2px"><? echo fn_number_format($tot_trims_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_embell_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_wash_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_comm_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_freight_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_test_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_inspection_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_currier_cost_actual,2); ?></td>
                
                <td width="100" align="right"><? echo fn_number_format($tot_actual_all_cost,2);?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_commission_cost_actual,2); ?></td>
                <td width="100" align="right"><? echo fn_number_format($tot_Net_PO_ex_Fact_Value_actual,2);?></td>
                <td width="100" align="right"><? echo fn_number_format($tot_CM_Value_Contribution_actual,2);?></td>
                
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_cm_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($total_actual_margin,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo fn_number_format($tot_actual_margin_perc,2); ?></td>
				<td width="58" style="padding-right:2px" align="right"><? //echo fn_number_format($tot_actual_margin_perc,2); ?></td>
            </tr>
        </table>
	</fieldset>
<?
	foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	echo "$total_data****$filename";
	exit();
}

if($action=="yarn_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	//$exchange_rate=76;
	$exchange_rate_arr=return_library_array( "select job_no, exchange_rate from wo_pre_cost_mst where status_active=1 and is_deleted=0", "job_no", "exchange_rate");
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

	$dataArrayYarn=array();
	$yarn_sql="select job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, sum(cons_qnty) as qnty, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id";
	$resultYarn=sql_select($yarn_sql);
	foreach($resultYarn as $yarnRow)
	{
		$dataArrayYarn[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('copm_one_id')]."**".$yarnRow[csf('percent_one')]."**".$yarnRow[csf('copm_two_id')]."**".$yarnRow[csf('percent_two')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('qnty')]."**".$yarnRow[csf('amount')].",";
	}
	
	$receive_array=array();
	$sql_receive="select prod_id, sum(order_qnty) as qty, sum(order_amount) as amnt from inv_transaction where transaction_type=1 and item_category=1 and status_active=1 and is_deleted=0 group by prod_id";
	$resultReceive = sql_select($sql_receive);
	foreach($resultReceive as $invRow)
	{
		$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
		$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
	}
	
	$yarnIssDataArray=sql_select("select po_breakdown_id, prod_id,
					sum(CASE WHEN entry_form ='3' and trans_type=2 and issue_purpose!=2 THEN quantity ELSE 0 END) AS yarn_iss_qty,
					sum(CASE WHEN entry_form ='9' and trans_type=4 THEN quantity ELSE 0 END) AS yarn_iss_return_qty,
					sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS trans_in_qty_yarn,
					sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS trans_out_qty_yarn
					from order_wise_pro_details where trans_type in(2,4,5,6) and status_active=1 and is_deleted=0 and po_breakdown_id in($po_id) group by po_breakdown_id,prod_id");
	foreach($yarnIssDataArray as $invRow)
	{
		$iss_qty=$invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')]-$invRow[csf('yarn_iss_return_qty')]-$invRow[csf('trans_out_qty_yarn')];
		$dataArrayYarnIssue[$invRow[csf('po_breakdown_id')]][1]+=$iss_qty;
		$rate='';
		if($receive_array[$invRow[csf('prod_id')]]>0)
		{
			$rate=$receive_array[$invRow[csf('prod_id')]]/$exchange_rate;
		}
		else
		{
			$rate=$avg_rate_array[$invRow[csf('prod_id')]]/$exchange_rate;
		}
		$dataArrayYarnIssue[$invRow[csf('po_breakdown_id')]][2]+=$iss_qty*$rate;
	}

?>
	<style>
		hr
		{
			color: #676767;
			background-color: #676767;
			height: 1px;
		}
	</style> 
    <div>
        <fieldset style="width:1033px;">
        	<table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="80">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="90">Order Qty</th>
                    <th width="70">Count</th>
                    <th width="100">Composition</th>
                    <th width="70">Type</th>
                    <th width="90">Required<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                    <th width="100">Cost ($)</th>
                    <th width="90">Issued</th>
                    <th>Cost ($)</th>
                </thead>
            </table>	
            <div style="width:1030px; max-height:310px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1010" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						
					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_mkt_required=0; $tot_required_cost=0; $tot_yarn_iss_qty=0; $tot_yarn_iss_cost=0;
				
					 $condition= new condition();
						if($po_id!='')
						{
							$condition->po_id("in($po_id)"); 
						}
					
					 $condition->init();
				
					 $yarn= new yarn($condition);
					 	// echo $yarn->getQuery(); die;
					$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$dzn_qnty=0; $job_mkt_required=0; $yarn_issued=0; $yarn_data_array=array(); //$job_mkt_required_cost=0;
						$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per_id==1) $dzn_qnty=12;
						else if($costing_per_id==3) $dzn_qnty=12*2;
						else if($costing_per_id==4) $dzn_qnty=12*3;
						else if($costing_per_id==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						$job_mkt_required_cost=$yarn_costing_arr[$row[csf('id')]];
						$exchange_rate=$exchange_rate_arr[$row[csf('job_no')]];
						
						$dataYarn=explode(",",substr($dataArrayYarn[$row[csf('job_no')]],0,-1));
						foreach($dataYarn as $yarnRow)
						{
							$yarnRow=explode("**",$yarnRow);
							$count_id=$yarnRow[0];
							$copm_one_id=$yarnRow[1];
							$percent_one=$yarnRow[2];
							$copm_two_id=$yarnRow[3];
							$percent_two=$yarnRow[4];
							$type_id=$yarnRow[5];
							$qnty=$yarnRow[6];
							$amnt=$yarnRow[7];
							
							$mkt_required=$plan_cut_qnty*($qnty/$dzn_qnty);
							//$mkt_required_cost=$plan_cut_qnty*($amnt/$dzn_qnty);
							$job_mkt_required+=$mkt_required;
							//$job_mkt_required_cost+=$mkt_required_cost;
							
							$yarn_data_array['count'][]=$yarn_count_details[$count_id];
							$yarn_data_array['type'][]=$yarn_type[$type_id];
							
							if($percent_two!=0)
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]." ".$percent_two." %";
							}
							else
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
							}

							$yarn_data_array['comp'][]=$compos;
						}
						
						$yarn_iss_qty=$dataArrayYarnIssue[$row[csf('id')]][1];
						$yarn_iss_cost=$dataArrayYarnIssue[$row[csf('id')]][2];
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="90" align="right"><? echo $order_qnty_in_pcs; ?></td>
                            <td width="70">
								<? 
                                    $d=1;
                                    foreach($yarn_data_array['count'] as $yarn_count_value)
                                    {
                                        if($d!=1)
                                        {
                                            echo "<hr/>";
                                        }
                                        echo $yarn_count_value;
                                        $d++;
                                    }
                                ?>
                            </td>
                            <td width="100">
                                <div style="word-wrap:break-word; width:100px">
                                    <? 
                                         $d=1;
                                         foreach($yarn_data_array['comp'] as $yarn_composition_value)
                                         {
                                            if($d!=1)
                                            {
                                                echo "<hr/>";
                                            }
                                            echo $yarn_composition_value;
                                            $d++;
                                         }
                                    ?>
                                </div>
                            </td>
                            <td width="70">
                                <p>
                                    <? 
                                         $d=1;
                                         foreach($yarn_data_array['type'] as $yarn_type_value)
                                         {
                                            if($d!=1)
                                            {
                                               echo "<hr/>";
                                            }
                                            
                                            echo $yarn_type_value; 
                                            $d++;
                                         }
                                    ?>
                                </p>
                            </td>
							<td width="90" align="right"><? echo fn_number_format($job_mkt_required,2,'.',''); ?></td>
                            <td width="100" align="right"><? echo fn_number_format($job_mkt_required_cost,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo fn_number_format($yarn_iss_qty,2,'.',''); ?></td>
                            <td align="right" style="padding-right:2px"><? echo fn_number_format($yarn_iss_cost,2,'.',''); ?></td>
						</tr>
					<?
						$i++;
						$tot_mkt_required+=$job_mkt_required; 
						$tot_required_cost+=$job_mkt_required_cost;
						$tot_yarn_iss_qty+=$yarn_iss_qty; 
						$tot_yarn_iss_cost+=$yarn_iss_cost; 
					}
					?>
                	<tfoot>
                        <th colspan="5">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th colspan="3">&nbsp;</th>
                        <th><? echo fn_number_format($tot_mkt_required,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_required_cost,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_yarn_iss_qty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_yarn_iss_cost,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="mkt_yarn_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$data=explode("**",$mkt_data);
	$grey_yarn_cost=$data[0];
	$dyed_yarn_cost=$data[1];
	$total=$grey_yarn_cost+$dyed_yarn_cost;
?>

    <div align="center">
        <fieldset style="width:620px;">
        	<table class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                    <th width="200">Grey Yarn Cost</th>
                    <th width="200">Dyed Yarn Cost</th>
                    <th width="">Total</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                	<td align="right" style="padding-right:3px"><? echo fn_number_format($grey_yarn_cost,2); ?></td>
                    <td align="right" style="padding-right:3px"><? echo fn_number_format($dyed_yarn_cost,2); ?></td>
                    <td align="right" style="padding-right:3px"><? echo fn_number_format($total,2); ?></td>
                </tr>
            </table>	
        </fieldset>
    </div>
<?
	exit();
}

if($action=="knitting_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$exchange_rate_arr=return_library_array( "select job_no, exchange_rate from wo_pre_cost_mst where status_active=1 and is_deleted=0", "job_no", "exchange_rate");
	
	$subconInBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.delivery_qty) as qty from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=2 group by b.order_id");
	foreach($subconInBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']=$subRow[csf('qty')];
	}	
	
	$subconOutBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.receive_qty) as qty from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=2 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id");
	foreach($subconOutBillDataArray as $subRow)
	{
		//$subconCostArray[$subRow[csf('order_id')]]['amnt']+=$subRow[csf('knit_bill')];
		//$subconCostArray[$subRow[csf('order_id')]]['qty']+=$subRow[csf('qty')];
	}
	
	$bookingArray=return_library_array( "select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id", "po_break_down_id", "qnty");
	
	$greyProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=2 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$knitCostArray=return_library_array( "select job_no, sum(amount) AS knit_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no", "job_no", "knit_charge");
?>
	<script>
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
	}
	function openmypage_bill(po_id,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'post_costing_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width=560px, height=350px, center=1, resize=0, scrolling=0', '../../../');
	}
    </script>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:1050px;">
        <fieldset style="width:1050px;">
        	<table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="80">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="80">Style Name</th>
                    <th width="110">Gmts Item</th>
                    <th width="90">Order Qty</th>
                    <th width="80">Booking Qty</th>
                    <th width="80">Grey Prod.</th>
                    <th width="90">Knitting Cost</th>
                    <th width="80">Fabric Bill Qty</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:1050px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						
					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_booking_qnty=0; $tot_greyProd_qnty=0; $tot_knitCost=0; $tot_knitbill=0; $tot_knitQty=0;
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}
						
						$dzn_qnty=0;
						$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
						$exchange_rate=$exchange_rate_arr[$row[csf('job_no')]];
						if($costing_per_id==1) $dzn_qnty=12;
						else if($costing_per_id==3) $dzn_qnty=12*2;
						else if($costing_per_id==4) $dzn_qnty=12*3;
						else if($costing_per_id==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						$bookingQty=$bookingArray[$row[csf('id')]];
						$tot_booking_qnty+=$bookingQty;
						$greyProdQty=$greyProdArray[$row[csf('id')]];
						$tot_greyProd_qnty+=$greyProdQty;
						$knitCost=($order_qnty_in_pcs/$dzn_qnty)*$knitCostArray[$row[csf('job_no')]];
						$tot_knitCost+=$knitCost;
						$knitQty=$subconCostArray[$row[csf('id')]]['qty'];
						$knitbill=$subconCostArray[$row[csf('id')]]['amnt']/$exchange_rate;
						$tot_knitQty+=$knitQty;
						$tot_knitbill+=$knitbill;
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
                            <td width="110" style="word-break:break-all;"><? echo $gmts_item; ?></td>
                            <td width="90" align="right"><? echo $order_qnty_in_pcs; ?></td>
							<td width="80" align="right"><? echo fn_number_format($bookingQty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo fn_number_format($greyProdQty,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo fn_number_format($knitCost,2,'.',''); ?></td>
							<td width="80" align="right"><? echo fn_number_format($knitQty,2,'.',''); ?></td>
							<td align="right"><a href="##" onClick="openmypage_bill('<? echo $row[csf('id')]; ?>','knitting_bill','Knitting bill Details')"><? echo fn_number_format($knitbill,2,'.',''); ?></a></td>
						</tr>
					<?
					$i++;
					}
					?>
                	<tfoot>
                        <th colspan="7">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th><? echo fn_number_format($tot_booking_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_greyProd_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_knitCost,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_knitQty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_knitbill,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="knitting_bill")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	//$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	$exchange_rate=return_field_value("a.exchange_rate", "wo_pre_cost_mst a, wo_po_break_down b", "a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.id in ($po_id)","exchange_rate");
	
	$subconInBillDataArray=sql_select("select a.id as mst_id, a.bill_no, a.company_id, a.party_source, sum(b.delivery_qty) as qty, sum(b.amount) AS knit_bill from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=2 group by a.id, a.bill_no, a.company_id, a.party_source");// b.order_id, b.currency_id
	/*foreach($subconInBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']=$subRow[csf('qty')];
	}*/	
	
	$subconOutBillDataArray=sql_select("select a.id as mst_id, a.bill_no, sum(b.receive_qty) as qty, sum(b.amount) AS knit_bill from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=2 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.bill_no");// b.order_id, b.currency_id
	/*foreach($subconOutBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']+=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']+=$subRow[csf('qty')];
	}*/
	
	
	$bookingArray=return_library_array( "select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id", "po_break_down_id", "qnty");
	
	$greyProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=2 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$knitCostArray=return_library_array( "select job_no, sum(amount) AS knit_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no", "job_no", "knit_charge");
?>
	<script>
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="290px";
	}
	
	function print_report(data,party_source)
	{
		//alert("su..re");
		var report_title="Knitting Bill";
		var show_val_column='';
		if(party_source==1)
		{
			var r=confirm("Press \"OK\" to open with Order Comments\nPress \"Cancel\" to open without Order Comments");
			if (r==true)
			{
				show_val_column="1";
			}
			else
			{
				show_val_column="0";
			}
		}
		else show_val_column="0";
		var data=data+"*"+report_title+"*"+show_val_column;
		window.open("../../../../subcontract_bill/requires/knitting_bill_issue_controller.php?data="+data+'&action=knitting_bill_print', true );
	}
    </script>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:470px;">
    
        <fieldset style="width:470px;">
        <legend>In Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconInBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><a href="##" onClick="print_report('<?php echo $row[csf('company_id')]."*".$row[csf('mst_id')]."*".$row[csf('bill_no')]; ?>','<?php echo $row[csf('party_source')]; ?>')"><? echo $row[csf('bill_no')]; ?></a></td>
                            <td width="100" align="right"><? echo fn_number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo fn_number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo fn_number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
            <br>
        <legend>Out Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body2">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconOutBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><? echo $row[csf('bill_no')]; ?></td>
                            <td width="100" align="right"><? echo fn_number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo fn_number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo fn_number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="dye_fin_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	//$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$exchange_rate=return_field_value("a.exchange_rate", "wo_pre_cost_mst a, wo_po_break_down b", "a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.id in ($po_id)","exchange_rate");
	
	$subconInBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.delivery_qty) as qty from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=4 group by b.order_id");
	foreach($subconInBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']=$subRow[csf('qty')];
	}	
	
	$subconOutBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.receive_qty) as qty from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=4 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id");
	foreach($subconOutBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']+=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']+=$subRow[csf('qty')];
	}
	
	$bookingArray=array();
	$bookingDataArray=sql_select( "select b.po_break_down_id, sum(b.grey_fab_qnty) as grey_qnty, sum(b.fin_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id");
	foreach($bookingDataArray as $bokRow)
	{
		$bookingArray[$bokRow[csf('po_break_down_id')]]['grey']=$bokRow[csf('grey_qnty')];
		$bookingArray[$bokRow[csf('po_break_down_id')]]['fin']=$bokRow[csf('qnty')];
	}
	
	$finProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=7 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$finCostArray=return_library_array( "select job_no, sum(amount) AS dye_fin_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process not in(1,2,30,35) and status_active=1 and is_deleted=0 group by job_no", "job_no", "dye_fin_charge");

?>
<script>
	function openmypage_bill(po_id,type,tittle)
	{
		//alert("su..re"); return;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'post_costing_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width=560px, height=350px, center=1, resize=0, scrolling=0', '../../../');
	}
</script>
    <div>
        <fieldset style="width:1113px;">
        	<table class="rpt_table" width="1110" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="80">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="80">Style Name</th>
                    <th width="110">Gmts Item</th>
                    <th width="90">Order Qty</th>
                    <th width="80">Booking Qty(Grey)</th>
                    <th width="80">Booking Qty(Fin)</th>
                    <th width="80">Finish Prod.</th>
                    <th width="90">Dye & Fin Cost</th>
                    <th width="80">Fabric Bill Qty</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:1110px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1090" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					/*$po_ids=count(array_unique(explode(",",$po_id)));
					//echo $all_po_id;
					$all_po_ids=chop($all_po_id,','); $poIds_cond="";
					//print_r($all_po_ids);
					if($db_type==2 && $po_ids>990)
					{
						$poIds_cond=" and (";
						$poIdsArr=array_chunk(explode(",",$all_po_ids),990);
						//print_r($gate_outIds);
						foreach($poIdsArr as $ids)
						{
							$ids=implode(",",$ids);
							$poIds_cond.=" po_id  in($ids) or ";
						}
						$poIds_cond=chop($poIds_cond,'or ');
						$poIds_cond.=")";
					}
					else
					{
						$poIds_cond=" and  po_id  in($all_po_id)";
					}*/
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						
					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.plan_cut , b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_booking_qnty=0; $tot_finProd_qnty=0; $tot_finCost=0; $tot_finbill=0; $tot_knitQty=0;
					$condition= new condition();
					
					/* $poids=explode(",",$po_id);
					 foreach( $poids as $po)
					 {
						$condition->po_id(" in($po)");  
					 }*/
					if(trim(str_replace("'","",$po_id))!='')
					 {
						$condition->po_id(" in($po_id)"); 
					 }
					  $condition->init();
					  $conversion= new conversion($condition);
					//  echo $conversion->getQuery();
				 	$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
					
					  
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}
						
						$dzn_qnty=0;
						$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per_id==1) $dzn_qnty=12;
						else if($costing_per_id==3) $dzn_qnty=12*2;
						else if($costing_per_id==4) $dzn_qnty=12*3;
						else if($costing_per_id==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						
						$plun_qnty_in_pcs=$row[csf('plan_cut')]*$row[csf('ratio')];
						
						$bookingQty=$bookingArray[$row[csf('id')]]['fin'];
						$tot_booking_qnty+=$bookingQty;
						
						$bookingQtyGrey=$bookingArray[$row[csf('id')]]['grey'];
						$tot_booking_grey_qnty+=$bookingQtyGrey;
						
						$finProdQty=$finProdArray[$row[csf('id')]];
						$tot_finProd_qnty+=$finProdQty;
						
						$finCost=0;$not_yarn_dyed_cost_arr=array(1,2,30,35);
						foreach($conversion_cost_head_array as $process_id=>$val)
						{
							if(!in_array($process_id,$not_yarn_dyed_cost_arr))
							{
								$finCost+=$conversion_costing_arr_process[$row[csf('id')]][$process_id];
							}
						}	
						//$finCost=($order_qnty_in_pcs/$dzn_qnty)*$finCostArray[$row[csf('job_no')]];
						//$finCost=($plun_qnty_in_pcs/$dzn_qnty)*$finCostArray[$row[csf('job_no')]];
						$tot_finCost+=$finCost;
						
						$finQty=$subconCostArray[$row[csf('id')]]['qty'];
						$finbill=$subconCostArray[$row[csf('id')]]['amnt']/$exchange_rate;
						
						$tot_finQty+=$finQty;
						$tot_finbill+=$finbill;
						
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
                            <td width="110" style="word-break:break-all;"><? echo $gmts_item; ?></td>
                            <td width="90" align="right"><? echo $order_qnty_in_pcs; ?></td>
							<td width="80" align="right"><? echo fn_number_format($bookingQtyGrey,2,'.',''); ?></td>
                            <td width="80" align="right"><? echo fn_number_format($bookingQty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo fn_number_format($finProdQty,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo fn_number_format($finCost,2,'.',''); ?></td>
							<td width="80" align="right"><? echo fn_number_format($finQty,2,'.',''); ?></td>
							<td align="right"><a href="##" onClick="openmypage_bill('<? echo $row[csf('id')]; ?>','dyeing_bill','Dyeing bill Details')"><? echo fn_number_format($finbill,2,'.',''); ?></a></td>
						</tr>
					<?
					$i++;
					}
					?>
                	<tfoot>
                        <th colspan="7">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th><? echo fn_number_format($tot_booking_grey_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_booking_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_finProd_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_finCost,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_finQty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_finbill,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="dyeing_bill")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	//$exchange_rate=76;
	$exchange_rate=return_field_value("a.exchange_rate", "wo_pre_cost_mst a, wo_po_break_down b", "a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.id in ($po_id)","exchange_rate");
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	
	$subconInBillDataArray=sql_select("select a.id as mst_id, a.bill_no, a.company_id, a.party_source, sum(b.delivery_qty) as qty, sum(b.amount) AS knit_bill from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=4 group by a.id, a.bill_no, a.company_id, a.party_source");//, b.order_id, b.currency_id
	
	$subconOutBillDataArray=sql_select("select a.id as mst_id, a.bill_no, sum(b.receive_qty) as qty, sum(b.amount) AS knit_bill from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=4 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.bill_no");//, b.currency_id, b.order_id
	
	$bookingArray=return_library_array( "select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id", "po_break_down_id", "qnty");
	
	$greyProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=2 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$knitCostArray=return_library_array( "select job_no, sum(amount) AS knit_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no", "job_no", "knit_charge");
?>
	<script>
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="290px";
	}
	
	function print_report(data)
	{
		//alert("su..re");
		var report_title="Dyeing And Finishing Bill";
		var show_val_column=1;
		var data=data+"*"+report_title+"*"+show_val_column;
		window.open("../../../../subcontract_bill/requires/sub_fabric_finishing_bill_issue_controller.php?data="+data+'&action=fabric_finishing_print', true );
	}
	
    </script>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:470px;">
        <fieldset style="width:470px;">
        <legend>In Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconInBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><a href="##" onClick="print_report('<?php echo $row[csf('company_id')]."*".$row[csf('mst_id')]."*".$row[csf('bill_no')]; ?>')"><? echo $row[csf('bill_no')]; ?></a></td>
                            <td width="100" align="right"><? echo fn_number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo fn_number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo fn_number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
            <br>
        <legend>Out Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body2">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconOutBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><? echo $row[csf('bill_no')]; ?></td>
                            <td width="100" align="right"><? echo fn_number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo fn_number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo fn_number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
	<?
	exit();
}

if($action=="yarn_cost_actual")
{
	echo load_html_head_contents("Yarn Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	
	$supplier_array=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$receive_array=array();
	//$exchange_rate=76;
	$exchange_rate=return_field_value("a.exchange_rate", "wo_pre_cost_mst a, wo_po_break_down b", "a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.id in ($po_id)","exchange_rate");
	$receive_array=array();
	$sql_receive="select a.currency_id,a.receive_purpose,b.prod_id, (b.order_qnty) as qty, (b.order_amount) as amnt,b.cons_quantity,b.cons_amount from inv_receive_master a, inv_transaction b where  a.id=b.mst_id and b.transaction_type=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0";
	$resultReceive = sql_select($sql_receive);
	foreach($resultReceive as $invRow)
	{
		
		if($invRow[csf('currency_id')]==1)//Taka
		{
			$avg_rate=$invRow[csf('cons_amount')]/$invRow[csf('cons_quantity')];
			$receive_array[$invRow[csf('prod_id')]]=$avg_rate/$exchange_rate;
		}
		else if($invRow[csf('currency_id')]==2)//USD
		{
			$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
			$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
		}
		else
		{
			
			$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
		}
	}
	
	
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:860px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:860px; margin-left:7px">
		<div id="report_container">
            <table class="rpt_table" width="855" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
					<th colspan="8"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Issue Id</th>
                    <th width="80">Issue Date</th>
                    <th width="70">Supplier</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Issue Qty.</th>
                    <th width="80">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
				</thead>
                <?
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_cost=0;
				$sql="select a.issue_number, a.issue_date, c.supplier_id, sum(b.quantity) as issue_qnty, c.id as prod_id, c.product_name_details, c.avg_rate_per_unit from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in($po_id) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, c.supplier_id, c.product_name_details, c.avg_rate_per_unit";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
                    $yarn_issued=$row[csf('issue_qnty')];
					$total_yarn_issue_qnty+=$yarn_issued;
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="70"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90">
							<? 
								echo fn_number_format($yarn_issued,2); 
                            ?>
                        </td>
                        <td align="right" width="80">
							<? 
								if($receive_array[$row[csf('prod_id')]]>0)
								{
									$avg_rate=$receive_array[$row[csf('prod_id')]];
								}
								else
								{
									$avg_rate=$row[csf('avg_rate_per_unit')]/$exchange_rate;
								}
								echo fn_number_format($avg_rate,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
							<?
								$yarn_cost=$yarn_issued*$avg_rate;
								echo fn_number_format($yarn_cost,2); 
								$total_yarn_cost+=$yarn_cost;
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo fn_number_format($total_yarn_issue_qnty,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo fn_number_format($total_yarn_cost,2);?></td>
                </tr>
                <thead>
                    <th colspan="8"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th>SL</th>
                	<th width="110">Return Id</th>
                    <th width="80">Return Date</th>
                    <th width="70">Supplier</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Return Qty.</th>
                    <th width="80">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
               </thead>
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_cost=0;
				$sql="select a.recv_number, a.receive_date, sum(b.quantity) as returned_qnty, c.id as prod_id, c.supplier_id, c.product_name_details, c.avg_rate_per_unit from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in($po_id) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, c.supplier_id, c.product_name_details, c.avg_rate_per_unit";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
                    $yarn_returned=$row[csf('returned_qnty')];
					$total_yarn_return_qnty+=$yarn_returned;
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
						<td width="70"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90">
							<? 
								echo fn_number_format($yarn_returned,2); 
                            ?>
                        </td>
                        <td align="right" width="80">
							<? 
								if($receive_array[$row[csf('prod_id')]]>0)
								{
									$avg_rate=$receive_array[$row[csf('prod_id')]];
								}
								else
								{
									$avg_rate=$row[csf('avg_rate_per_unit')]/$exchange_rate;
								}
								echo fn_number_format($avg_rate,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right" width="80">
							<?
								$yarn_return_cost=$yarn_returned*$avg_rate;
								echo fn_number_format($yarn_return_cost,2); 
								$total_yarn_return_cost+=$yarn_return_cost;
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo fn_number_format($total_yarn_return_qnty,2);?></td>
                     <td>&nbsp;</td>
                    <td align="right"><? echo fn_number_format($total_yarn_return_cost,2);?></td>
                </tr>
                <thead>
                    <th colspan="8"><b>Transfer In</b></th>
                </thead>
                <thead>
                	<th>SL</th>
                	<th width="110">Transfer Id</th>
                    <th width="80">Transfer Date</th>
                    <th width="70">Supplier</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Transfer Qty.</th>
                    <th width="80">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
               	</thead>
                <?
                $i=1; $total_trans_in_qnty=0; $total_trans_in_cost=0;
				$sql="select a.transfer_system_id, a.transfer_date, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.supplier_id, d.product_name_details, d.avg_rate_per_unit from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=11 and c.po_breakdown_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.transfer_system_id, a.transfer_date, b.from_prod_id, d.supplier_id, d.product_name_details, d.avg_rate_per_unit";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="180"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="70"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90"><? echo fn_number_format($row[csf('transfer_qnty')],2); ?> </td>
                        <td align="right" width="80">
							<? 
								if($receive_array[$row[csf('from_prod_id')]]>0)
								{
									$avg_rate=$receive_array[$row[csf('from_prod_id')]];
								}
								else
								{
									$avg_rate=$row[csf('avg_rate_per_unit')]/$exchange_rate;
								}
								echo fn_number_format($avg_rate,2); 
                            ?>&nbsp;
                        </td>
                        <td>
							<?
								$yarn_trans_cost=$row[csf('transfer_qnty')]*$avg_rate;
								echo fn_number_format($yarn_trans_cost,2); 
                            ?>
                        </td>
                    </tr>
                <?
					$total_trans_in_qnty+=$row[csf('transfer_qnty')];
					$total_trans_in_cost+=$yarn_trans_cost;
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo fn_number_format($total_trans_in_qnty,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo fn_number_format($total_trans_in_cost,2);?></td>
                </tr>
                <thead>
                    <th colspan="8"><b>Transfer Out</b></th>
                </thead>
                <thead>
                	<th>SL</th>
                	<th width="110">Transfer Id</th>
                    <th width="80">Transfer Date</th>
                    <th width="70">Supplier</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Transfer Qty.</th>
                    <th width="80">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
               	</thead>
                <?
                $i=1; $total_trans_out_qnty=0; $total_trans_out_cost=0;
				$sql="select a.transfer_system_id, a.transfer_date, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details, d.supplier_id, d.avg_rate_per_unit from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=11 and c.po_breakdown_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, b.from_prod_id, d.product_name_details, d.supplier_id, d.avg_rate_per_unit";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="180"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="70"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90"><? echo fn_number_format($row[csf('transfer_qnty')],2); ?> </td>
                        <td align="right" width="80">
							<? 
								if($receive_array[$row[csf('from_prod_id')]]>0)
								{
									$avg_rate=$receive_array[$row[csf('from_prod_id')]];
								}
								else
								{
									$avg_rate=$row[csf('avg_rate_per_unit')]/$exchange_rate;
								}
								echo fn_number_format($avg_rate,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
							<?
								$yarn_trans_cost_out=$row[csf('transfer_qnty')]*$avg_rate;
								echo fn_number_format($yarn_trans_cost_out,2); 
                            ?>
                        </td>
                    </tr>
                <?
					$total_trans_out_qnty+=$row[csf('transfer_qnty')];
					$total_trans_out_cost+=$yarn_trans_cost_out;
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td >&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo fn_number_format($total_trans_out_qnty,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo fn_number_format($total_trans_out_cost,2);?></td>
                </tr>
               	<tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right">Balance</th>
                    <th align="right"><? echo fn_number_format(($total_yarn_issue_qnty+$total_trans_in_qnty)-($total_yarn_return_qnty+$total_trans_out_qnty),2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo fn_number_format(($total_yarn_cost+$total_trans_in_cost)-($total_yarn_return_cost+$total_trans_out_cost),2); ?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if($action=="knit_cost_actual")
{
	echo load_html_head_contents("Knitting Bill Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	$product_array=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13", "id", "product_name_details");
?>
	<fieldset style="width:870px; margin-left:7px">
        <table class="rpt_table" width="865" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">Bill No.</th>
                <th width="80">Bill Date</th>
                <th width="180">Fabric Description</th>
                <th width="110">Fabric Qty (Kg)</th>
                <th width="80">Rate/Kg</th>
                <th width="120">Amount (Taka)</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:865px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="845" cellpadding="0" cellspacing="0">  
            	<tr bgcolor="#CCDDEE">
					<td colspan="8"><b>Inside Knitting Bill</b></td>
				</tr>  
				<?
                $i=1; //$avg_rate=76;
				$avg_rate=return_field_value("a.exchange_rate", "wo_pre_cost_mst a, wo_po_break_down b", "a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.id in ($po_id)","exchange_rate");
				$total_in_knit_qnty=0; $total_in_knit_cost_tk=0; $total_in_knit_cost_usd=0;
				$total_out_knit_qnty=0; $total_out_knit_cost_tk=0; $total_out_knit_cost_usd=0;
				$total_knit_qnty=0; $total_knit_cost_tk=0; $total_knit_cost_usd=0;
				
                $sql="select a.id, a.bill_no, a.bill_date, b.item_id, b.rate, b.delivery_qty, b.amount as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id=$po_id and a.process_id=2 and a.party_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('bill_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('bill_date')]); ?></td>
                        <td width="180"><p><? echo $product_array[$row[csf('item_id')]]; ?></p></td>
                        <td align="right" width="110"><? echo fn_number_format($row[csf('delivery_qty')],2); ?></td>
                        <td align="right" width="80"><? echo fn_number_format($row[csf('rate')],2); ?>&nbsp;</td>
                        <td align="right" width="120"><? echo fn_number_format($row[csf('amount')],2); ?></td>
                        <td align="right">
                            <?
								$amount_usd=$row[csf('amount')]/$avg_rate;
                                echo fn_number_format($amount_usd,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_in_knit_qnty+=$row[csf('delivery_qty')];
					$total_in_knit_cost_tk+=$row[csf('amount')];
					$total_in_knit_cost_usd+=$amount_usd;
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo fn_number_format($total_in_knit_qnty,2); ?></td>
                    <td align="right"><? echo fn_number_format($total_in_knit_cost_tk/$total_in_knit_qnty,2); ?>&nbsp;</td>
                    <td align="right"><? echo fn_number_format($total_in_knit_cost_tk,2); ?></td>
                    <td align="right"><? echo fn_number_format($total_in_knit_cost_usd,2); ?></td>
                </tr>
                <tr bgcolor="#CCDDEE">
					<td colspan="8"><b>Outside Knitting Bill</b></td>
				</tr>
                <?
                $sql="select a.id, a.bill_no, a.bill_date, b.item_id, b.rate, b.receive_qty, b.amount as amount from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and b.order_id=$po_id and a.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('bill_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('bill_date')]); ?></td>
                        <td width="180"><p><? echo $product_array[$row[csf('item_id')]]; ?></p></td>
                        <td align="right" width="110"><? echo fn_number_format($row[csf('receive_qty')],2); ?></td>
                        <td align="right" width="80"><? echo fn_number_format($row[csf('rate')],2); ?>&nbsp;</td>
                        <td align="right" width="120"><? echo fn_number_format($row[csf('amount')],2); ?></td>
                        <td align="right">
                            <?
								$amount_usd=$row[csf('amount')]/$avg_rate;
                                echo fn_number_format($amount_usd,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_out_knit_qnty+=$row[csf('receive_qty')];
					$total_out_knit_cost_tk+=$row[csf('amount')];
					$total_out_knit_cost_usd+=$amount_usd;
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo fn_number_format($total_out_knit_qnty,2); ?></td>
                    <td align="right"><? echo fn_number_format($total_out_knit_cost_tk/$total_out_knit_qnty,2); ?>&nbsp;</td>
                    <td align="right"><? echo fn_number_format($total_out_knit_cost_tk,2); ?></td>
                    <td align="right"><? echo fn_number_format($total_out_knit_cost_usd,2); ?></td>
                </tr>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Grand Total</th>
                    <th align="right"><? echo fn_number_format($total_out_knit_qnty+$total_in_knit_qnty,2); ?></th>
                    <th align="right"><? echo fn_number_format(($total_out_knit_cost_tk+$total_in_knit_cost_tk)/($total_out_knit_qnty+$total_in_knit_qnty),2); ?>&nbsp;</th>
                    <th align="right"><? echo fn_number_format($total_out_knit_cost_tk+$total_in_knit_cost_tk,2); ?></th>
                    <th align="right"><? echo fn_number_format($total_out_knit_cost_usd+$total_in_knit_cost_usd,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="dye_finish_cost_actual")
{
	echo load_html_head_contents("Dyeing Bill Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	$product_array=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2", "id", "product_name_details");
?>
	<fieldset style="width:1070px; margin-left:7px">
        <table class="rpt_table" width="1065" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">Bill No.</th>
                <th width="80">Bill Date</th>
                <th width="180">Fabric Description</th>
                <th width="110">Fabric Qty (Kg)</th>
                <th width="80">Main Rate/Kg</th>
                
                <th width="80">Process name</th>
                <th width="80">Additonal Rate</th>
                <th width="80" title="Main Rate/Kg+Additonal Rate">Total Rate</th>
                
                <th width="120" title="Fabric Qty (Kg)*Total Rate">Amount (Taka)</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:1065px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0">  
            	<tr bgcolor="#CCDDEE">
					<td colspan="11"><b>Inside Dyeing Bill</b></td>
				</tr>  
				<?
                $i=1; //$avg_rate=76;
				$avg_rate=return_field_value("a.exchange_rate", "wo_pre_cost_mst a, wo_po_break_down b", "a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.id in ($po_id)","exchange_rate");
				$total_in_knit_qnty=0; $total_in_knit_cost_tk=0; $total_in_knit_cost_usd=0;
				$total_out_knit_qnty=0; $total_out_knit_cost_tk=0; $total_out_knit_cost_usd=0;
				$total_knit_qnty=0; $total_knit_cost_tk=0; $total_knit_cost_usd=0;
				
                $sql="select a.id, a.bill_no, a.bill_date, b.item_id, b.rate, b.delivery_qty,b.add_process_name,b.add_rate, b.amount as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id=$po_id and a.process_id=4 and a.party_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('bill_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('bill_date')]); ?></td>
                        <td width="180"><p><? echo $product_array[$row[csf('item_id')]]; ?></p></td>
                        <td align="right" width="110"><? echo fn_number_format($row[csf('delivery_qty')],2); ?></td>
                        <td align="right" width="80"><? echo fn_number_format($row[csf('rate')],2); ?>&nbsp;</td>
                        
                        <td align="right" width="80"><? echo  $row[csf('add_process_name')]; ?>&nbsp;</td>
                        <td align="right" width="80"><? echo fn_number_format($row[csf('add_rate')],2); ?>&nbsp;</td>
                        <td align="right" width="80">
						<? 
							$totalRate=$row[csf('rate')]+$row[csf('add_rate')]; 
							echo fn_number_format($totalRate,2); 
						?>
                        &nbsp;</td>
                        
                        <td align="right" width="120">
						<? 
							$amount=($totalRate*$row[csf('delivery_qty')]);
							$row[csf('amount')]=$amount;
							echo fn_number_format($row[csf('amount')],2); 
						?>
                        </td>
                        <td align="right">
                            <?
								$amount_usd=$row[csf('amount')]/$avg_rate;
                                echo fn_number_format($amount_usd,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_in_knit_qnty+=$row[csf('delivery_qty')];
					$total_in_knit_cost_tk+=$row[csf('amount')];
					$total_in_knit_cost_usd+=$amount_usd;
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo fn_number_format($total_in_knit_qnty,2); ?></td>
                    <td align="right"><? echo fn_number_format($total_in_knit_cost_tk/$total_in_knit_qnty,2); ?>&nbsp;</td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td align="right"><? echo fn_number_format($total_in_knit_cost_tk,2); ?></td>
                    <td align="right"><? echo fn_number_format($total_in_knit_cost_usd,2); ?></td>
                </tr>
                <tr bgcolor="#CCDDEE">
					<td colspan="11"><b>Outside Dyeing Bill</b></td>
				</tr>
                <?
                $sql="select a.id, a.bill_no, a.bill_date, b.item_id, b.rate, b.receive_qty, b.amount as amount from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and b.order_id=$po_id and a.process_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('bill_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('bill_date')]); ?></td>
                        <td width="180"><p><? echo $product_array[$row[csf('item_id')]]; ?></p></td>
                        <td align="right" width="110"><? echo fn_number_format($row[csf('receive_qty')],2); ?></td>
                        <td align="right" width="80"><? echo fn_number_format($row[csf('rate')],2); ?>&nbsp;</td>
                        
                        <td align="right" width="80"><? //echo fn_number_format($row[csf('rate')],2); ?>&nbsp;</td>
                        <td align="right" width="80"><? //echo fn_number_format($row[csf('rate')],2); ?>&nbsp;</td>
                        <td align="right" width="80"><? //echo fn_number_format($row[csf('rate')],2); ?>&nbsp;</td>
                        
                        <td align="right" width="120"><? echo fn_number_format($row[csf('amount')],2); ?></td>
                        <td align="right">
                            <?
								$amount_usd=$row[csf('amount')]/$avg_rate;
                                echo fn_number_format($amount_usd,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_out_knit_qnty+=$row[csf('receive_qty')];
					$total_out_knit_cost_tk+=$row[csf('amount')];
					$total_out_knit_cost_usd+=$amount_usd;
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo fn_number_format($total_out_knit_qnty,2); ?></td>
                    <td align="right"><? echo fn_number_format($total_out_knit_cost_tk/$total_out_knit_qnty,2); ?>&nbsp;</td>
                    <td align="right"><? echo fn_number_format($total_out_knit_cost_tk,2); ?></td>
                    
                    <td align="right"><? //echo fn_number_format($total_out_knit_cost_tk,2); ?></td>
                    <td align="right"><? //echo fn_number_format($total_out_knit_cost_tk,2); ?></td>
                    <td align="right"><? //echo fn_number_format($total_out_knit_cost_tk,2); ?></td>
                    
                    
                    <td align="right"><? echo fn_number_format($total_out_knit_cost_usd,2); ?></td>
                </tr>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Grand Total</th>
                    <th align="right"><? echo fn_number_format($total_out_knit_qnty+$total_in_knit_qnty,2); ?></th>
                    <th align="right"><? echo fn_number_format(($total_out_knit_cost_tk+$total_in_knit_cost_tk)/($total_out_knit_qnty+$total_in_knit_qnty),2); ?>&nbsp;</th>
                    <th align="right"><? echo fn_number_format($total_out_knit_cost_tk+$total_in_knit_cost_tk,2); ?></th>
                    <th> </th>
                    <th> </th>
                    <th> </th>
                    
                    <th align="right"><? echo fn_number_format($total_out_knit_cost_usd+$total_in_knit_cost_usd,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="fabric_purchase_cost_actual")
{
	echo load_html_head_contents("Fabric Purchase Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	//$ex_rate=76;
	$ex_rate=return_field_value("a.exchange_rate", "wo_pre_cost_mst a, wo_po_break_down b", "a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.id in ($po_id)","exchange_rate");
	//echo $ex_rate.'kk';
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:860px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:860px; margin-left:7px">
		<div id="report_container">
        	<u><b>Knit Fabric Purchase</b></u>
            <table class="rpt_table" width="855" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Receive Id</th>
                    <th width="80">Receive Date</th>
                    <th width="280">Fabric Description</th>
                    <th width="110">Receive Qty.</th>
                    <th width="110">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
				</thead>
                <?
                $i=1; $total_recv_qnty=0; $total_recv_cost=0;
				$sql="select a.id, a.recv_number, a.receive_date, sum(b.quantity) as recv_qnty, sum(d.cons_rate*b.quantity) as amnt, c.id, c.product_name_details from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=1 and d.item_category=2 and c.item_category_id=2 and d.id=b.trans_id and b.trans_type=1 and b.entry_form=37 and a.entry_form=37 and b.po_breakdown_id=$po_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_basis!=9 group by a.id, c.id, a.recv_number, a.receive_date, c.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
                    $total_recv_qnty+=$row[csf('recv_qnty')];
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="280"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="110">
							<? 
								echo fn_number_format($row[csf('recv_qnty')],2); 
                            ?>
                        </td>
                        <td align="right" title="Recv Qty/Recv Amount(<? echo $row[csf('amnt')];?>)/Exchange Rate(76)" width="110">
							<? 
								$avg_rate=($row[csf('amnt')]/$row[csf('recv_qnty')])/$ex_rate;
								echo fn_number_format($avg_rate,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
							<?
								$cost=$row[csf('recv_qnty')]*$avg_rate;
								//$cost=$row[csf('amnt')]/$ex_rate;
								$total_recv_cost+=$cost;
								echo fn_number_format($cost,2); 
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
               	<tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo fn_number_format($total_recv_qnty,2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo fn_number_format($total_recv_cost,2); ?></th>
                </tfoot>
            </table>
            <br>
            <u><b>Woven Fabric Purchase</b></u>
            <table class="rpt_table" width="855" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Receive Id</th>
                    <th width="80">Receive Date</th>
                    <th width="280">Fabric Description</th>
                    <th width="110">Receive Qty.</th>
                    <th width="110">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
				</thead>
                <?
                $i=1; $total_recv_qnty_w=0; $total_recv_cost_w=0;
				$sql="select a.id, a.recv_number, a.currency_id, a.receive_date, sum(b.quantity) as recv_qnty, sum(d.cons_rate*b.quantity) as amnt, c.id, c.product_name_details from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=1 and d.item_category=3 and c.item_category_id=3 and d.id=b.trans_id and b.trans_type=1 and b.entry_form=17 and a.entry_form=17 and b.po_breakdown_id=$po_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.id, a.recv_number, a.currency_id, a.receive_date, c.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
                    $total_recv_qnty_w+=$row[csf('recv_qnty')];
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="280"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="110">
							<? 
								echo fn_number_format($row[csf('recv_qnty')],2); 
                            ?>
                        </td>
                        <td align="right" width="110">
							<? 
								//if($row[csf('currency_id')]==2) $avg_rate=($row[csf('amnt')]/$row[csf('recv_qnty')]);
								//else 
								$avg_rate=($row[csf('amnt')]/$row[csf('recv_qnty')])/$ex_rate;
								echo fn_number_format($avg_rate,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
							<?
								$cost=$row[csf('recv_qnty')]*$avg_rate;
								//$cost=$row[csf('amnt')]/$ex_rate;
								$total_recv_cost_w+=$cost;
								echo fn_number_format($cost,2); 
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
               	<tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo fn_number_format($total_recv_qnty_w,2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo fn_number_format($total_recv_cost_w,2); ?></th>
                </tfoot>
            </table>
            <table class="tbl_bottom" width="855" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<tr>
                	<td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="280">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="110">Grand Total</td>
                    <td align="right"><? echo fn_number_format($total_recv_cost+$total_recv_cost_w,2); ?></td>
                </tr>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if($action=="fabric_purchase_cost")
{
	echo load_html_head_contents("Fabric Purchase Cost Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	//$exchange_rate=76;
	$exchange_rate=return_field_value("a.exchange_rate", "wo_pre_cost_mst a, wo_po_break_down b", "a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.id in ($po_id)","exchange_rate");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$sql_fin_purchase="select b.po_breakdown_id, sum(b.quantity) as qty, sum(a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis<>9 and a.item_category=2 and a.transaction_type=1 and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($po_id) group by b.po_breakdown_id";
	$dataArrayFinPurchase=sql_select($sql_fin_purchase);
	foreach($dataArrayFinPurchase as $finRow)
	{
		$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['qty']=$finRow[csf('qty')];
		$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['amnt']=$finRow[csf('finish_purchase_amnt')]/$exchange_rate;
	}
	
	$sql_fin_purchase="select b.po_breakdown_id, sum(b.quantity) as qty, sum(a.cons_rate*b.quantity) as woven_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=3 and a.transaction_type=1 and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($po_id) group by b.po_breakdown_id";
	$dataArrayFinPurchase=sql_select($sql_fin_purchase);
	foreach($dataArrayFinPurchase as $finRow)
	{
		$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['qty']+=$finRow[csf('qty')];
		$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['amnt']+=$finRow[csf('woven_purchase_amnt')]/$exchange_rate;
	}
?>
    <div>
        <fieldset style="width:710px; margin-left:8px">
        	<table class="rpt_table" width="680" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="100">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="110">Order Qty.</th>
                    <th width="110">Received</th>
                    <th>Cost ($)</th>
                </thead>
            </table>	
            <div style="width:700px; max-height:310px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="680" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						
					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_recv_qty=0; $tot_cost=0;
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						
						$recv_qty=$finish_purchase_arr[$row[csf('id')]]['qty'];
						$recv_cost=$finish_purchase_arr[$row[csf('id')]]['amnt'];
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="100" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="110" align="right"><? echo $order_qnty_in_pcs; ?></td>
							<td width="110" align="right"><? echo fn_number_format($recv_qty,2,'.',''); ?></td>
                            <td align="right" style="padding-right:2px"><? echo fn_number_format($recv_cost,2,'.',''); ?></td>
						</tr>
					<?
						$i++;
						$tot_recv_qty+=$recv_qty; 
						$tot_cost+=$recv_cost; 
					}
					?>
                	<tfoot>
                        <th colspan="5">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th><? echo fn_number_format($tot_recv_qty,2,'.',''); ?></th>
                        <th><? echo fn_number_format($tot_cost,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="aop_cost_actual")
{
	echo load_html_head_contents("AOP Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

?>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO NO.</th>
                <th width="80">WO Date</th>
                <th width="80">Currency</th>
                <th width="120">Amount (Taka)</th>
                <th width="120">Conversion rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_aop_cost=0;// $avg_rate=76;
				$avg_rate=return_field_value("a.exchange_rate", "wo_pre_cost_mst a, wo_po_break_down b", "a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.id in ($po_id)","exchange_rate");
                $sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id=$po_id and a.item_category=12 and a.booking_type in(3,6) and b.process=35 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id, a.exchange_rate";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                        <td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                        <td align="right" width="120">
                            <? 
								$amnt_tk=$row[csf('amount')]*$row[csf('exchange_rate')];
                                echo fn_number_format($amnt_tk,2); 
                            ?>
                        </td>
                        <td align="right" width="120"><? echo fn_number_format($row[csf('exchange_rate')],2); ?>&nbsp;</td>
                        <td align="right">
                            <?
								if($row[csf('currency_id')]==1)
								{
                                	$amount=$row[csf('amount')]/$row[csf('exchange_rate')];
								}
								else
								{
									$amount=$row[csf('amount')];
								}
                                echo fn_number_format($amount,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_aop_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo fn_number_format($total_aop_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="trims_cost_actual")
{
	echo load_html_head_contents("Trim Actual Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

?>
<script>
	function generate_work_order_report(booking_no, cbo_company_name,id_approved_id,cbo_isshort,budget_version,show_comment,path,action_type) {
				
				
				if (action_type == 'show_trim_booking_report3')
				{
					var action_method = "action=show_trim_booking_report3";
				}
				else if (action_type == 'show_trim_booking_report4')
				{
					var action_method = "action=show_trim_booking_report4";
				}
				//show_trim_booking_report3 =Yes, show_trim_booking_report4=No
				report_title = "&report_title=Main Trims Booking";
				if(budget_version==2) 
				{
					http.open("POST", "../../../../order/woven_order/requires/trims_booking_controller2.php", true);
				}
				else
				{
					http.open("POST", "../../../../order/woven_order/requires/trims_booking_controller.php", true);

				}
						
				var data = action_method + report_title +
				'&txt_booking_no=' + "'" + booking_no + "'" +
				'&cbo_company_name=' + "'" + cbo_company_name + "'" +
				'&id_approved_id=' + "'" + id_approved_id + "'" +
				'&cbo_isshort=' + "'" + cbo_isshort + "'" +
				'&show_comment=' + "'" + show_comment + "'" +
				'&path=' + "'" + path + "'";
				//alert(data);
				
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = generate_fabric_report_reponse;
			}

			function generate_fabric_report_reponse() {
				if (http.readyState == 4) {
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
						'<html><head><title></title></head><body>' + http.responseText + '</body</html>');
					d.close();
				}
			}
</script>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO NO.</th>
                <th width="80">WO Date</th>
                <th width="80">Currency</th>
                <th width="120">Amount (Taka)</th>
                <th width="120">Conversion rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_trims_cost=0; //$avg_rate=76;
				$avg_rate=return_field_value("a.exchange_rate", "wo_pre_cost_mst a, wo_po_break_down b", "a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.id in ($po_id)","exchange_rate");
                $sql="select a.booking_no,a.company_id,a.is_short,a.is_approved,a.item_from_precost,a.entry_form,a.budget_version,a.booking_date, a.currency_id, a.exchange_rate, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id=$po_id and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no,a.company_id,a.is_approved,a.item_from_precost,a.entry_form,a.budget_version,a.booking_date, a.currency_id,a.is_short, a.exchange_rate";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
				$show_comment=1;$path="../../../../";
				if($row[csf('item_from_precost')]==2)
				{
					$action_type="show_trim_booking_report4";
				}
				else
				{
					$action_type="show_trim_booking_report3";
				}
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                       
						 <td width="110"><p><span style="cursor: pointer; text-decoration: underline; color: blue;" title="Print Booking"
							onClick="generate_work_order_report('<?php echo $row[csf('booking_no')];?>',<?php echo $row[csf('company_id')];?>,<?php echo $row[csf('is_approved')];?>,<?php echo $row[csf('is_short')];?>,<?php echo $row[csf('budget_version')];?>,<?php echo $show_comment;?>,'<?php echo $path;?>','<?php echo $action_type;?>')"><?php echo $row[csf('booking_no')];?></span></p></td>
							
                        <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                        <td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                        <td align="right" width="120">
                            <? 
								if($row[csf('currency_id')]==2) //USD
								{
									$amnt_tk=$row[csf('amount')]*$row[csf('exchange_rate')];
								}
								else
								{
									$amnt_tk=$row[csf('amount')];	
								}
								
                                echo fn_number_format($amnt_tk,2); 
                            ?>
                        </td>
                        <td align="right" width="120"><? echo fn_number_format($row[csf('exchange_rate')],2); ?>&nbsp;</td>
                        <td align="right">
                            <?
								if($row[csf('currency_id')]==1)
								{
                                	$amount=$row[csf('amount')]/$row[csf('exchange_rate')];
								}
								else
								{
									$amount=$row[csf('amount')];
								}
                                echo fn_number_format($amount,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_trims_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo fn_number_format($total_trims_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="embell_cost_actual")
{
	echo load_html_head_contents("Embellishment Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

?>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO NO.</th>
                <th width="80">WO Date</th>
                <th width="80">Currency</th>
                <th width="120">Amount (Taka)</th>
                <th width="120">Conversion rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_aop_cost=0; //$avg_rate=76;
				$avg_rate=return_field_value("a.exchange_rate", "wo_pre_cost_mst a, wo_po_break_down b", "a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.id in ($po_id)","exchange_rate");
                $sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.emb_name!=3 and b.po_break_down_id=$po_id and a.item_category=25 and a.booking_type in(3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id, a.exchange_rate";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                        <td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                        <td align="right" width="120">
                            <? 
								$amnt_tk=$row[csf('amount')]*$row[csf('exchange_rate')];
                                echo fn_number_format($amnt_tk,2); 
                            ?>
                        </td>
                        <td align="right" width="120"><? echo fn_number_format($row[csf('exchange_rate')],2); ?>&nbsp;</td>
                        <td align="right">
                            <?
								if($row[csf('currency_id')]==1)
								{
                                	$amount=$row[csf('amount')]/$row[csf('exchange_rate')];
								}
								else
								{
									$amount=$row[csf('amount')];
								}
                                echo fn_number_format($amount,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_aop_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo fn_number_format($total_aop_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="wash_cost_actual")
{
	echo load_html_head_contents("Wash Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

?>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO NO.</th>
                <th width="80">WO Date</th>
                <th width="80">Currency</th>
                <th width="120">Amount (Taka)</th>
                <th width="120">Conversion rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_aop_cost=0; //$avg_rate=76;
				$avg_rate=return_field_value("a.exchange_rate", "wo_pre_cost_mst a, wo_po_break_down b", "a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.id in ($po_id)","exchange_rate");
                $sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.emb_name=3 and b.po_break_down_id=$po_id and a.item_category=25 and a.booking_type in(3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id, a.exchange_rate";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                        <td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                        <td align="right" width="120">
                            <? 
								$amnt_tk=$row[csf('amount')]*$row[csf('exchange_rate')];
                                echo fn_number_format($amnt_tk,2); 
                            ?>
                        </td>
                        <td align="right" width="120"><? echo fn_number_format($row[csf('exchange_rate')],2); ?>&nbsp;</td>
                        <td align="right">
                            <?
								if($row[csf('currency_id')]==1)
								{
                                	$amount=$row[csf('amount')]/$row[csf('exchange_rate')];
								}
								else
								{
									$amount=$row[csf('amount')];
								}
                                echo fn_number_format($amount,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_aop_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo fn_number_format($total_aop_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="commission_cost_actual")
{
	echo load_html_head_contents("Commission Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	$data=explode("_",$po_id);
	$poId=$data[0];
	$job_no=$data[1];
	$ex_factory_qty=$data[2];
	$dzn_qnty=$data[3];

?>
	<fieldset style="width:560px; margin-left:10px">
        <table class="rpt_table" width="555" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="170">Commission Type</th>
                <th width="170">Commission Base</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:555px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="535" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_comm_cost=0;
                $sql="select particulars_id, commission_base_id, commission_amount as amount from wo_pre_cost_commiss_cost_dtls where job_no='$job_no' and status_active=1 and is_deleted=0 and commission_amount>0";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="170"><p><? echo $commission_particulars[$row[csf('particulars_id')]]; ?></p></td>
                        <td width="170"><p><? echo $commission_base_array[$row[csf('commission_base_id')]]; ?></p></td>
                        <td align="right">
                            <?
								$amount=($ex_factory_qty/$dzn_qnty)*$row[csf('amount')];
                                echo fn_number_format($amount,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_comm_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo fn_number_format($total_comm_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}
//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	
	$unit_price_arr=return_library_array( "select id, unit_price 
	from wo_po_break_down where status_active=1 and is_deleted=0 and id=$id ", "id", "unit_price");	
	?>
    
     <script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>
	<div style="width:100%" align="center" id="report_container">
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<fieldset style="width:550px"> 
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%"> 
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="100">Ex-Fact. Return Qty.</th>
                        <th width="">Ex-Fact. Value</th>
                       
                     </tr>   
                </thead> 	 	
            </table>  
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
              
				$exfac_sql=("select b.challan_no,b.ex_factory_date, 
				CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty 
				from   pro_ex_factory_mst b  where  b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($id) ");
                $sql_dtls=sql_select($exfac_sql);
                
                foreach($sql_dtls as $row_real)
                { 
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF"; 
					$tot_exfact_qty=$row_real[csf("ex_factory_qnty")]-$row_real[csf("ex_factory_return_qnty")]; 
					$unit_price=$unit_price_arr[$id]; 
					$tot_exfact_val=$tot_exfact_qty*$unit_price;                            
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td> 
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("challan_no")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                        <td width="" align="right"><? echo fn_number_format($tot_exfact_val,2); ?></td>
                    </tr>
                    <? 
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
					  $total_exfact_val+=$tot_exfact_val;
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                     <th><? echo fn_number_format($rec_qnty,2); ?></th>
                    <th><? echo fn_number_format($rec_return_qnty,2); ?></th>
                    <th><? echo fn_number_format($total_exfact_val,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="2" align="right"><? echo fn_number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                 <th><? echo fn_number_format($total_exfact_val,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div> 
		</fieldset>
	</div>    
	<?
    exit();	
}
//Ex-Factory Delv. and Return
if($action=="show_po_detail_report")
{
	?>
    <script>
	
	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('html_print_data').innerHTML+'</body</html>');
		d.close(); 
	}
	function openmypage_mkt(mkt_data,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'post_costing_report_controller.php?mkt_data='+mkt_data+'&action='+type, tittle, 'width=660px, height=200px, center=1, resize=0, scrolling=0', '../../../');
	}
	
	function openmypage(po_id,type,tittle)
	{
		var popup_width='';
		if(type=="dye_fin_cost") 
		{
			popup_width='1140px';
		}
		else if(type=="fabric_purchase_cost") 
		{
			popup_width='740px';
		}
		else popup_width='1060px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'post_costing_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../../');
	}
	
	
</script>
<?	
		
		
		echo load_html_head_contents("Po Detail", "../../../../", 1, 1,$unicode,'','');
		extract($_REQUEST);
		$company_short_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		
		
	  	$bgcolor="#E9F3FF";  $bgcolor2="#FFFFFF";	
		
	  	 $gsm_weight_top=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no='$job_no' and body_part_id=1");
		 $gsm_weight_bottom=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no='$job_no' and body_part_id=20");
		 $costing_date=return_field_value("costing_date", "wo_pre_cost_mst", "job_no='$job_no'","costing_date");
		 $po_qty=0;
		 $po_plun_cut_qty=0;
		 $total_set_qnty=0;$tot_po_value=0;
		 $sql_po="select a.job_no,a.total_set_qnty,b.id,c.order_total,b.unit_price,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst   and b.id=c.po_break_down_id and b.id =$po_id  and a.job_no ='$job_no'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id";
		$sql_po_data=sql_select($sql_po);
		foreach($sql_po_data as $sql_po_row)
		{
		$po_qty+=$sql_po_row[csf('order_quantity')];
		$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
		$tot_po_value+=$sql_po_row[csf('order_total')];
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
		$unit_price=$sql_po_row[csf('unit_price')]/$total_set_qnty;
		$tot_po_value=$po_qty*$unit_price;
		}
		$fab_knit_req_kg_avg=0;
		$fab_woven_req_yds_avg=0;
		if($db_type==0)
		{
		$sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,a.order_uom,a.job_quantity,a.avg_unit_price,b.costing_per,b.budget_minute,b.approved,b.updated_by,b.sew_smv,b.sew_effi_percent,b.incoterm,b.exchange_rate,c.fab_knit_req_kg,c.fab_knit_fin_req_kg,c.fab_woven_req_yds,c.fab_woven_fin_req_yds,c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b left join wo_pre_cost_sum_dtls c on   b.job_no=c.job_no where a.job_no=b.job_no and a.job_no='$job_no' and a.company_name=$company_name  and a.status_active=1  order by a.job_no";  
		}
		if($db_type==2)
		{
		 $sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,a.order_uom,a.job_quantity,a.avg_unit_price,b.costing_per,b.budget_minute,b.approved,b.updated_by,b.sew_smv,b.sew_effi_percent,b.incoterm,b.exchange_rate,c.fab_knit_req_kg,c.fab_knit_fin_req_kg,c.fab_woven_req_yds,c.fab_woven_fin_req_yds,c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b left join wo_pre_cost_sum_dtls c on   b.job_no=c.job_no where a.job_no=b.job_no and a.status_active=1 and a.job_no='$job_no' and a.company_name=$company_name  order by a.job_no";  
		}
		
		$data_array=sql_select($sql);
		
		
		$condition= new condition();
		if(str_replace("'","",$job_no) !='')
		{
		  $condition->job_no("='$job_no'");
		}
		if(str_replace("'","",$po_id) !='')
		{
		  $condition->po_id("=$po_id");
		}
		 $condition->init();
		
		$fabric= new fabric($condition);
		$fabric_costing_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
		
		//echo array_sum($fabric_costing_qty_arr['knit']['grey'][$po_id]);die;
		
		$yarn= new yarn($condition);
	    $yarn_req_arr=$yarn->getOrderWiseYarnQtyArray();
		 $fab_knit_qty_gray=(array_sum($fabric_costing_qty_arr['knit']['grey'][$po_id])/$po_qty)*12;
		 $fab_woven_qty_gray=(array_sum($fabric_costing_qty_arr['woven']['grey'][$po_id])/$po_qty)*12;
		 
		 $fab_knit_qty_finish=(array_sum($fabric_costing_qty_arr['knit']['finish'][$po_id])/$po_qty)*12;
		 $fab_woven_qty_finish=(array_sum($fabric_costing_qty_arr['woven']['finish'][$po_id])/$po_qty)*12;
		 $yarn_req_qty_avg=($yarn_req_arr[$po_id]/$po_qty)*12;
			
		 
	?>
	<div style="width:100%" align="center"> <input type="button" onClick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px;"/>
		<fieldset style="width:850px" id="html_print_data">
       
         <table width="840px"   cellpadding="0" cellspacing="0" border="0">
        	<tr>
            	<td rowspan="3" width="25%">
                   <?
                $data_array2=sql_select("select image_location  from common_photo_library  where master_tble_id='$company_name' and form_name='company_details' and is_deleted=0 and file_type=1");
              	foreach($data_array2 as $img_row)
                {
					?>
                    <img src='../../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />	
                    <? 
                }
                ?>
                </td>
            	<td align="center"><b style="font-size:20px;"><? echo $company_short_arr[$company_name]; ?></b></td>
            	<td rowspan="3" width="25%" align="right">
                <?
                   $data_array_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");               
                foreach($data_array_img as $img_row)
                {
					?>
                    <img src='../../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='80' align="middle" />	
                    <? 
                }
                ?>
               
                </td>
            </tr>
        	<tr>
            	<td align="center"><b style="font-size:14px;">Pre and Post Cost Comparison</b></td>
            </tr>
           <tr>
                <td align="center">
                    
					<? if( $data_array[0][csf("approved")]==1){echo "<div style='font-size:18px; color:#F00; background:#CCC;'>THIS COST SHEET IS APPROVED </div>";
					
					} else {echo "&nbsp;";} 
					
					$prepared_app_data = $data_array[0][csf("updated_by")];		

					?> 
                     
                </td>
            </tr>
        
        </table>
       			 <? 
				$uom="";
				foreach ($data_array as $row)
				{	
					$order_price_per_dzn=0;
					$order_job_qnty=0;
					$avg_unit_price=0;
					
					$order_values = $row[csf("job_quantity")]*$row[csf("avg_unit_price")];
					$result =sql_select("select po_number,grouping,file_no,pub_shipment_date from wo_po_break_down where job_no_mst='$job_no'  and id=$po_id and status_active=1 and is_deleted=0 order by pub_shipment_date DESC");
					$job_in_orders = '';$pulich_ship_date='';$job_in_file = '';$job_in_ref = '';
					foreach ($result as $val)
					{
						$job_in_orders= $val[csf('po_number')];
						$pulich_ship_date = $val[csf('pub_shipment_date')];
						$job_in_ref.=$val[csf('grouping')].",";
						$job_in_file.=$val[csf('file_no')].",";
						
					}
					$job_in_orders = $job_in_orders;
					
					$job_ref=array_unique(explode(",",rtrim($job_in_ref,", ")));
					$job_file=array_unique(explode(",",rtrim($job_in_file,", ")));
					
					foreach ($job_ref as $ref)
					{
						$ref_cond.=", ".$ref;
					}
					$file_con='';
					foreach ($job_file as $file)
					{
						if($file_con=='') $file_cond=$file; else $file_cond.=", ".$file;
					}
					if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for=" For 1 DZN";}
					else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for=" For PCS";}
					else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for=" For 2 DZN";}
					else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for="For 3 DZN";}
					else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for=" For 4 DZN";}
						
		
		?>
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr bgcolor="<? echo $bgcolor2;?>">
                    	<td width="130">Job Number</td>
                        <td width="100"><b><? echo $row[csf("job_no")]; ?></b></td>
                        <td width="130">Buyer</td>
                        <td width="110"><b><? echo $buyer_arr[$row[csf("buyer_name")]]; $buyer_id=$row[csf("buyer_name")];?></b></td>
                        <td>Garments Item</td>
                        <?  
							$grmnt_items = "";
							if($garments_item[$row[csf("gmts_item_id")]]=="")
							{
								
								$grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$job_no");
								foreach($grmts_sql as $key=>$val){
									$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
								}
								$grmnt_items = substr_replace($grmnt_items,"",-1,1);
							}else{
								$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
							}
							
						?>
                        <td width="160"><b><? echo $grmnt_items; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<td>Style Ref. No </td>
                        <td><b><? echo $row[csf("style_ref_no")]; ?></b></td>
                         <td>Costing Date</td><td><? echo $costing_date;?></td>
                        <td>PO Qnty</td>
                        <td><b><? $uom=$row[csf("order_uom")]; echo fn_number_format($po_qty)." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2;?>">
                    	<td>Order Numbers</td>
                        <td colspan="3"><? echo $job_in_orders; ?></td>
                        <td>Plan Cut Qnty [Cut % <? echo fn_number_format((($po_plun_cut_qty/$total_set_qnty-$po_qty)/$po_qty)*100,2);?>]</td>
                        <td><b><? $uom=$row[csf("order_uom")]; echo fn_number_format($po_plun_cut_qty/$total_set_qnty)." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor;?>">
                    	<td>Knit Fabric Cons</td>
                        <td><b><? echo $fab_knit_qty_gray;$fab_knit_req_kg_avg+=$fab_knit_qty_gray; ?> (Kg)</b></td>
	
                        <td>Woven Fabric Cons</td>
                        <td><b><? echo $fab_woven_qty_gray;$fab_woven_req_yds_avg+= $fab_woven_qty_gray;?> (Yds)</b></td>
                        <td>Price Per Unit</td>
                        <td><b><? echo fn_number_format($row[csf("avg_unit_price")],2); ?> USD</b></td>
                    </tr>
                    
                    <tr bgcolor="<? echo $bgcolor2;?>">
                    	<td>Avg Yarn Req</td>
                        <td><b><? echo $yarn_req_qty_avg; ?> (Kg)</b></td>
                        <td>Woven Fin Fabric Cons</td>
                        <td><b><? echo $fab_woven_qty_finish; ?>(Yds)</b></td>
                        
                        <td>Order Value</td>
                        <td><b><? echo fn_number_format($po_qty*$row[csf("avg_unit_price")],2); ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor;?>">
                    	<td>Knit Fin Fabric Cons</td>
                        <td><b><? echo $fab_knit_qty_finish; ?> (Kg)</b></td>
                        <td>Costing Per</td>
                        <td><b><? echo $costing_for; ?></b></td>
                        <td>Inco Term</td>
                        <td><b><? echo $incoterm[$row[csf("incoterm")]]; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2;?>">
                        <td>GSM</td>
                        <td><b><? echo $gsm_weight_top.",".$gsm_weight_bottom ?></b></td>
                        <td>SMV</td>
                        <td><b><? $sew_smv=$row[csf("sew_smv")]; echo fn_number_format($sew_smv,2); ?> </b></td>
                        <td>Efficiency %</td>
                        <td colspan="1"><b><? echo $sew_effi_percent=$row[csf("sew_effi_percent")] ?> </b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor;?>">
                        <td>Exchange Rate</td>
                        <td><b><? echo $exchange_rate=$row[csf("exchange_rate")]; ?></b></td>
                        <td>Budget SAH</td>
                        <td><b><? echo fn_number_format(($row[csf("sew_smv")]*$po_qty)/60,2); ?></b></td>
                        <td>Shipment Date </td>
                        <td><b><? echo $pulich_ship_date;?></b></td>
                    </tr>
                    
                    
                    
                    
                </table>
            <?	
			
			
	}//end first foearch
	
				$condition= new condition();
				 $condition->company_name("=$company_name");
				 if(str_replace("'","",$job_no) !=''){
					  $condition->job_no("='$job_no'");
				 }
				if(str_replace("'","",$po_id)!='')
				 {
					 $condition->po_id("=$po_id");
				 }
				 $condition->init();
				$fabric= new fabric($condition);
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				 $yarn= new yarn($condition);
				 $yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				 $conversion= new conversion($condition);
				 $conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
				// print_r($conversion_costing_arr_process);
				 $trims= new trims($condition);
				 $trims_costing_arr=$trims->getAmountArray_by_order();
				$emblishment= new emblishment($condition);
				$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
				$commission= new commision($condition);
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$commercial= new commercial($condition);
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				$other= new other($condition);
				$other_costing_arr=$other->getAmountArray_by_order();
				$wash= new wash($condition);
				$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
				
				$exchange_rate=76; 
				$sql_fin_purchase="select b.po_breakdown_id, sum(a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis<>9 and a.item_category=2 and a.transaction_type=1 and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=$po_id group by b.po_breakdown_id";
				$dataArrayFinPurchase=sql_select($sql_fin_purchase);
				foreach($dataArrayFinPurchase as $finRow)
				{
					$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]=$finRow[csf('finish_purchase_amnt')]/$exchange_rate;
				}
				
				 $subconInBillDataArray=sql_select("select b.order_id, 
									  sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
									  sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
									  from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id in(2,4) and b.order_id=$po_id group by b.order_id");
				foreach($subconInBillDataArray as $subRow)
				{
					$subconCostArray[$subRow[csf('order_id')]]['knit_bill']=$subRow[csf('knit_bill')];
					$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']=$subRow[csf('dye_finish_bill')];
				}	
				$prodcostDataArray=sql_select("select job_no, 
									  sum(CASE WHEN cons_process=1 THEN amount END) AS knit_charge,
									  sum(CASE WHEN cons_process=30 THEN amount END) AS yarn_dye_charge,
									  sum(CASE WHEN cons_process=35 THEN amount END) AS aop_charge,
									  sum(CASE WHEN cons_process not in(1,2,30,35) THEN amount END) AS dye_finish_charge
									  from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
				foreach($prodcostDataArray as $prodRow)
				{
					$prodcostArray[$prodRow[csf('job_no')]]['knit_charge']=$prodRow[csf('knit_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['aop_charge']=$prodRow[csf('aop_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
				}
				$actualCostDataArray=sql_select("select cost_head,po_id,sum(amount_usd) as amount_usd from wo_actual_cost_entry where company_id=$company_name and status_active=1 and is_deleted=0 group by cost_head,po_id");
				foreach($actualCostDataArray as $actualRow)
				{
				   $actualCostArray[$actualRow[csf('cost_head')]][$actualRow[csf('po_id')]]=$actualRow[csf('amount_usd')];
				}
					
				$bookingDataArray=sql_select("select a.booking_type, a.item_category, a.currency_id, a.exchange_rate, b.po_break_down_id, b.process, b.amount, b.pre_cost_fabric_cost_dtls_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id=$po_id and  a.company_id=$company_name and a.item_category in(4,12,25) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				foreach($bookingDataArray as $woRow)
				{
					$amount=0; $trimsAmnt=0;
					if($woRow[csf('currency_id')]==1) { $amount=$woRow[csf('amount')]/$exchange_rate; } else { $amount=$woRow[csf('amount')]; }
					
					if($woRow[csf('item_category')]==25 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						if($embell_type_arr[$woRow[csf('pre_cost_fabric_cost_dtls_id')]]==3)
						{
							$washCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
						else
						{
							$embellCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
					}
					else if($woRow[csf('item_category')]==12 && $woRow[csf('process')]==35 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						$aopCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
					}
					else if($woRow[csf('item_category')]==4)
					{
						if($woRow[csf('currency_id')]==1) { $trimsAmnt=$woRow[csf('amount')]/$woRow[csf('exchange_rate')]; } else { $trimsAmnt=$woRow[csf('amount')]; }
						$actualTrimsCostArray[$woRow[csf('po_break_down_id')]]+=$trimsAmnt; 
					}
				}
				$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
				$receive_array=array();
				$sql_receive="select prod_id, sum(order_qnty) as qty, sum(order_amount) as amnt from inv_transaction where transaction_type=1 and item_category=1 and status_active=1 and is_deleted=0 group by prod_id";
				$resultReceive = sql_select($sql_receive);
				foreach($resultReceive as $invRow)
				{
					$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
					$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
				}
				$yarnTrimsDataArray=sql_select("select b.po_breakdown_id, b.prod_id, a.item_category, 
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose!=2 THEN b.quantity ELSE 0 END) AS yarn_iss_qty,
					sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
					sum(CASE WHEN a.transaction_type=5 and b.entry_form ='11' and b.trans_type=5 THEN b.quantity ELSE 0 END) AS trans_in_qty_yarn,
					sum(CASE WHEN a.transaction_type=6 and b.entry_form ='11' and b.trans_type=6 THEN b.quantity ELSE 0 END) AS trans_out_qty_yarn,
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='25' and b.trans_type=2 THEN a.cons_rate*b.quantity ELSE 0 END) AS trims_issue_amnt
					from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.po_breakdown_id=$po_id group by b.po_breakdown_id, b.prod_id, a.item_category");
				foreach($yarnTrimsDataArray as $invRow)
				{
					if($invRow[csf('item_category')]==1)
					{
						$iss_qty=$invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')]-$invRow[csf('yarn_iss_return_qty')]-$invRow[csf('trans_out_qty_yarn')];
						$rate='';
						if($receive_array[$invRow[csf('prod_id')]]>0)
						{
							$rate=$receive_array[$invRow[csf('prod_id')]];
						}
						else
						{
							$rate=$avg_rate_array[$invRow[csf('prod_id')]]/$exchange_rate;
						}
						
						$iss_amnt=$iss_qty*$rate;
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][1]+=$iss_amnt;
					}
					else
					{
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][4]+=$invRow[csf('trims_issue_amnt')];
					}
				}
				$yarncostDataArray=sql_select("select job_no, sum(amount) as amnt, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 and job_no='$job_no' group by job_no");
				foreach($yarncostDataArray as $yarnRow)
				{
				   $yarncostArray[$yarnRow[csf('job_no')]]=$yarnRow[csf('amount')];
				}
				
				$fabriccostDataArray=sql_select("select job_no, costing_per_id, embel_cost, wash_cost, cm_cost, commission, currier_pre_cost, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0 and job_no='$job_no'");
				foreach($fabriccostDataArray as $fabRow)
				{
					 $fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['wash_cost']=$fabRow[csf('wash_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				}
				
				$ex_factory_arr=return_library_array( "select po_break_down_id, 
				sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as qnty 
				from pro_ex_factory_mst where status_active=1 and is_deleted=0 and po_break_down_id =$po_id group by po_break_down_id", "po_break_down_id", "qnty");
				
					$dzn_qnty=0;
					$costing_per_id=$fabriccostArray[$job_no]['costing_per_id'];
					if($costing_per_id==1) $dzn_qnty=12;
					else if($costing_per_id==3) $dzn_qnty=12*2;
					else if($costing_per_id==4) $dzn_qnty=12*3;
					else if($costing_per_id==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;
					
				 $ex_factory_qty=$ex_factory_arr[$po_id];
				 $unit_price=$unit_price/$total_set_qnty;
				$tot_ex_factory_val=$ex_factory_qty*$unit_price;
				$tot_fabric_purchase_cost_mkt=$fabric_costing_arr['knit']['grey'][$po_id]+$fabric_costing_arr['woven']['grey'][$po_id];
				$tot_fabric_purchase_cost_actual=$finish_purchase_amnt_arr[$po_id];
				$tot_yarn_cost_mkt=$yarn_costing_arr[$po_id];
				$tot_yarn_dye_cost_mkt=$conversion_costing_arr_process[$po_id][30];
				$tot_knit_cost_mkt=$conversion_costing_arr_process[$po_id][1];
				
					$tot_dye_finish_cost_mkt=0;$not_yarn_dyed_cost_arr=array(1,2,30,35);
					foreach($conversion_cost_head_array as $process_id=>$val)
					{
						if(!in_array($process_id,$not_yarn_dyed_cost_arr))
						{
							$tot_dye_finish_cost_mkt+=$conversion_costing_arr_process[$po_id][$process_id];
						}
					}
					$tot_dye_finish_cost_actual=$subconCostArray[$po_id]['dye_finish_bill']/$exchange_rate;
					$tot_knit_cost_actual=$subconCostArray[$po_id]['knit_bill']/$exchange_rate;
					
					$tot_aop_cost_actual=$aopCostArray[$po_id];
					$tot_aop_cost_mkt=$conversion_costing_arr_process[$po_id][35];
					$tot_trims_cost_mkt=$trims_costing_arr[$po_id];
					$tot_trims_cost_actual=$actualTrimsCostArray[$po_id];
					
					$print_amount=$emblishment_costing_arr_name[$po_id][1];
					$embroidery_amount=$emblishment_costing_arr_name[$po_id][2];
					$special_amount=$emblishment_costing_arr_name[$po_id][4];
					$wash_cost=$emblishment_costing_arr_name_wash[$po_id][3];
					$other_amount=$emblishment_costing_arr_name[$po_id][5];
					
					$tot_embell_cost_mkt=$print_amount+$embroidery_amount+$special_amount+$other_amount;
					$tot_embell_cost_actual=$embellCostArray[$po_id];
					$tot_wash_cost_mkt=$wash_cost;
					$tot_wash_cost_actual=$washCostArray[$po_id];
					
					$foreign_cost=$commission_costing_arr[$po_id][1];
					$local_cost=$commission_costing_arr[$po_id][2];
					

					
					
					$tot_commission_cost_mkt=$foreign_cost+$local_cost;
					$tot_commission_cost_actual=($ex_factory_qty/$dzn_qnty)*$fabriccostArray[$job_no]['commission'];
					$tot_comm_cost_actual=$actualCostArray[6][$po_id];
					$tot_comm_cost_mkt=$commercial_costing_arr[$po_id];
					
					$tot_test_cost_mkt=$other_costing_arr[$po_id]['lab_test'];
					$tot_freight_cost_mkt=$other_costing_arr[$po_id]['freight'];
					$tot_inspection_cost_mkt=$other_costing_arr[$po_id]['inspection'];
					$tot_certificate_cos_mktt=$other_costing_arr[$po_id]['certificate_pre_cost'];
					//$common_oh_cost=$other_costing_arr[$row[csf('id')]]['common_oh'];
					$tot_currier_cost_mkt=$other_costing_arr[$po_id]['currier_pre_cost'];
					$tot_cm_cost_mkt=$other_costing_arr[$po_id]['cm_cost'];
					
					$tot_yarn_cost_actual=$yarnTrimsCostArray[$po_id][1];
					
					$tot_freight_cost_actual=$actualCostArray[2][$po_id];
					$tot_test_cost_actual=$actualCostArray[1][$po_id];
					$tot_inspection_cost_actual=$actualCostArray[3][$po_id];
					$tot_currier_cost_actual=$actualCostArray[4][$po_id];
					$tot_cm_cost_actual=$actualCostArray[5][$po_id];
					
					//$tot_mkt_all_cost=$tot_mkt_all_cost;
					$tot_mkt_all_cost=$tot_yarn_cost_mkt+$tot_knit_cost_mkt+$tot_dye_finish_cost_mkt+$tot_yarn_dye_cost_mkt+$tot_aop_cost_mkt+$tot_trims_cost_mkt+$tot_embell_cost_mkt+$tot_wash_cost_mkt+$tot_commission_cost_mkt+$tot_comm_cost_mkt+$tot_freight_cost_mkt+$tot_test_cost_mkt+$tot_inspection_cost_mkt+$tot_currier_cost_mkt+$tot_cm_cost_mkt+$tot_fabric_purchase_cost_mkt;
					
					$tot_mkt_margin=$tot_po_value-$tot_mkt_all_cost;
					$tot_mkt_margin_perc=($mkt_margin/$tot_po_value)*100;
					
					$tot_actual_all_cost=$tot_yarn_cost_actual+$tot_knit_cost_actual+$tot_dye_finish_cost_actual+$tot_yarn_dye_cost_actual+$tot_aop_cost_actual+$tot_trims_cost_actual+$tot_embell_cost_actual+$tot_wash_cost_actual+$tot_commission_cost_actual+$tot_comm_cost_actual+$tot_freight_cost_actual+$tot_test_cost_actual+$tot_inspection_cost_actual+$tot_currier_cost_actual+$tot_cm_cost_actual+$tot_fabric_purchase_cost_actual;
					
					$tot_actual_margin=$tot_ex_factory_val-$tot_actual_all_cost;
					$tot_actual_margin_perc=($tot_actual_margin/$tot_ex_factory_val)*100;
					
	?>
         <br/> <br/>
        <table width="760" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
      
                            <thead>
                                <th width="30">SL</th>
                                <th width="180">Particulars</th>
                                <th width="130">Pre Costing</th>
                                <th width="80">%</th>
                                <th width="130">Post-Costing</th>
                                <th width="80">%</th>
                                <th>Variance</th>
                            </thead> 
							<?
								$bgcolor1='#E9F3FF';
								$bgcolor2='#FFFFFF';
							?>
                            </thead>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_1','<? echo $bgcolor1; ?>')" id="trtd_1">
                                <td align="center">1</td>
                                <td>PO/Shipment Value</td>
                                <td align="right"><? echo fn_number_format($tot_po_value,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo fn_number_format($tot_ex_factory_val,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo fn_number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_2','<? echo $bgcolor2; ?>')" id="trtd_2">
                                <td colspan="7"><b>Cost</b></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_3','<? echo $bgcolor1; ?>')" id="trtd_3">
                                <td align="center">2</td>
                                <td>Fabric Purchase Cost</td>
                                <td align="right"><? echo fn_number_format($tot_fabric_purchase_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_fabric_purchase_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_fabric_purchase_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_fabric_purchase_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_4','<? echo $bgcolor2; ?>')" id="trtd_4">
                                <td align="center">3</td>
                                <td>Yarn Cost+Yarn Dyeing Cost</td>
                                <td align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $tot_yarn_cost_mkt."**".$tot_yarn_dye_cost_mkt; ?>','mkt_yarn_cost','Grey And Dyed Yarn Mkt. Cost Details')"></a><? echo fn_number_format($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format((($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt)/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_yarn_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_yarn_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt)-$tot_yarn_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_5','<? echo $bgcolor2; ?>')" id="trtd_5">
                                <td align="center">4</td>
                                <td>Knitting Cost</td>
                                <td align="right"><? echo fn_number_format($tot_knit_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_knit_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_knit_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_knit_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_knit_cost_mkt-$tot_knit_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_6','<? echo $bgcolor2; ?>')" id="trtd_6">
                                <td align="center">5</td>
                                <td>Dye & Fin Cost</td>
                                <td align="right"><? echo fn_number_format($tot_dye_finish_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_dye_finish_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','dye_fin_cost','Dye & Fin Cost Details')"></a><? echo fn_number_format($tot_dye_finish_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_dye_finish_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_dye_finish_cost_mkt-$tot_dye_finish_cost_actual,2); ?></td>
                            </tr>
                            
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_7','<? echo $bgcolor1; ?>')" id="trtd_7">
                                <td align="center">6</td>
                                <td>AOP & Others Cost</td>
                                <td align="right"><? echo fn_number_format($tot_aop_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_aop_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_aop_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_aop_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_aop_cost_mkt-$tot_aop_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_8','<? echo $bgcolor2; ?>')" id="trtd_8">
                                <td align="center">7</td>
                                <td>Trims Cost</td>
                                <td align="right"><? echo fn_number_format($tot_trims_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_trims_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_trims_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_trims_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_trims_cost_mkt-$tot_trims_cost_actual,2); ?></td>
                            </tr> 
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_9','<? echo $bgcolor1; ?>')" id="trtd_9">
                                <td align="center">8</td>
                                <td>Embellishment Cost</td>
                                <td align="right"><? echo fn_number_format($tot_embell_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_embell_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_embell_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_embell_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_embell_cost_mkt-$tot_embell_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_10','<? echo $bgcolor2; ?>')" id="trtd_10">
                                <td align="center">9</td>
                                <td>Wash Cost</td>
                                <td align="right"><? echo fn_number_format($tot_wash_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_wash_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_wash_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_wash_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_wash_cost_mkt-$tot_wash_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_11','<? echo $bgcolor1; ?>')" id="trtd_11">
                                <td align="center">10</td>
                                <td>Commission Cost</td>
                                <td align="right"><? echo fn_number_format($tot_commission_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_commission_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_commission_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_commission_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_commission_cost_mkt-$tot_commission_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_12','<? echo $bgcolor2; ?>')" id="trtd_12">
                                <td align="center">11</td>
                                <td>Commercial Cost</td>
                                <td align="right"><? echo fn_number_format($tot_comm_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_comm_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_comm_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_comm_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_comm_cost_mkt-$tot_comm_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_13','<? echo $bgcolor1; ?>')" id="trtd_13">
                                <td align="center">12</td>
                                <td>Freight Cost</td>
                                <td align="right"><? echo fn_number_format($tot_freight_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_freight_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_freight_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_freight_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_freight_cost_mkt-$tot_freight_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_14','<? echo $bgcolor2; ?>')" id="trtd_14">
                                <td align="center">13</td>
                                <td>Testing Cost</td>
                                <td align="right"><? echo fn_number_format($tot_test_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_test_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_test_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_test_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_test_cost_mkt-$tot_test_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_15','<? echo $bgcolor1; ?>')" id="trtd_15">
                                <td align="center">14</td>
                                <td>Inspection Cost</td>
                                <td align="right"><? echo fn_number_format($tot_inspection_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_inspection_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_inspection_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_inspection_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_inspection_cost_mkt-$tot_inspection_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_16','<? echo $bgcolor2; ?>')" id="trtd_16">
                                <td align="center">15</td>
                                <td>Courier Cost</td>
                                <td align="right"><? echo fn_number_format($tot_currier_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_currier_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_currier_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_currier_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_currier_cost_mkt-$tot_currier_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_17','<? echo $bgcolor1; ?>')" id="trtd_17">
                                <td align="center">16</td>
                                <td>CM</td>
                                <td align="right"><? echo fn_number_format($tot_cm_cost_mkt,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_cm_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_cm_cost_actual,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_cm_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_cm_cost_mkt-$tot_cm_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_18','<? echo $bgcolor2; ?>')" id="trtd_18">
                                <td align="center">17</td>
                                <td>Total Cost</td>
                                <td align="right"><? echo fn_number_format($tot_mkt_all_cost,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_mkt_all_cost/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_actual_all_cost,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_actual_all_cost/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_mkt_all_cost-$tot_actual_all_cost,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_19','<? echo $bgcolor1; ?>')" id="trtd_19">
                                <td align="center">18</td>
                                <td>Margin/Loss</td>
                                <td align="right"><? echo fn_number_format($tot_mkt_margin,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_mkt_margin/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_actual_margin,2); ?></td>
                                <td align="right"><? echo fn_number_format(($tot_actual_margin/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo fn_number_format($tot_mkt_margin-$tot_actual_margin,2); ?></td>
                            </tr>
                        </table>
		</fieldset>
	</div>    
	<?
    exit();	
}
//disconnect($con);
?>
<script>
function openmypage(po_id,type,tittle)
	{
		//alert(po_id);
		var popup_width='';
		if(type=="dye_fin_cost") 
		{
			popup_width='1140px';
		}
		else if(type=="fabric_purchase_cost") 
		{
			popup_width='740px';
		}
		else popup_width='1060px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'post_costing_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '');
	}
</script>