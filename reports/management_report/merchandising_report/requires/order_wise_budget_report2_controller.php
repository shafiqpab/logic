<?
session_start();
//ini_set('memory_limit','3072M');

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class3/class.conditions.php');
require_once('../../../../includes/class3/class.reports.php');
require_once('../../../../includes/class3/class.yarns.php');
require_once('../../../../includes/class3/class.conversions.php');
require_once('../../../../includes/class3/class.emblishments.php');
require_once('../../../../includes/class3/class.commisions.php');
require_once('../../../../includes/class3/class.commercials.php');
require_once('../../../../includes/class3/class.others.php');
require_once('../../../../includes/class3/class.trims.php');
require_once('../../../../includes/class3/class.washes.php');
require_once('../../../../includes/class3/class.fabrics.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
//$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
//$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
//$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
//$approve_arr=return_library_array( "select job_no, approved from wo_pre_cost_mst", "job_no", "approved");

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst",'job_no','costing_per');
	$report_type=str_replace("'","",$reporttype);
	$company_name=str_replace("'","",$cbo_company_name);
	$season=str_replace("'","",$txt_season);
	$internal_ref=trim(str_replace("'","",$txt_internal_ref));
	if($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='$internal_ref' ";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if(trim($cbo_year)!=0) $year_cond=" $year_field_con=$cbo_year"; else $year_cond="";
	}

	$order_status_id=str_replace("'","",$cbo_order_status);
	$order_status_cond='';
	if($order_status_id==0)
	{
		$order_status_cond=" and b.is_confirmed in(1,2)";
	}
	else if($order_status_id!=0)
	{
		$order_status_cond=" and b.is_confirmed=$order_status_id";
	}

	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

	$date_cond='';
	if($report_type==5)
	{
		if(str_replace("'","",$cbo_search_date)==1)
		{
			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				if($db_type==0)
				{
					$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
					$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
				}
				else if($db_type==2)
				{
					$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
					$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
				}
				$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
				$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
			}
		}
		else if(str_replace("'","",$cbo_search_date)==2)
		{
			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				if($db_type==0)
				{
					$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
					$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
				}
				else if($db_type==2)
				{
					$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
					$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
				}
				$date_cond=" and b.po_received_date between '$start_date' and '$end_date'";
				$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
			}//applying_period_date,applying_period_to_date
		}
		else if(str_replace("'","",$cbo_search_date)==3)// PO Insert Date
		{
			if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			{
				if($db_type==0)
				{
					$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
					$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
					$date_cond=" and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
				}
				else if($db_type==2)
				{
					$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
					$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
					$date_cond=" and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
				}
				$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
			}//applying_period_date,applying_period_to_date
		}
		$hader_caption="Order Wise Budget On Shipout Report";
		$header_ord_qty="Order Qty(Pcs)";
		$header_act_ship_qty="Actual Shipment Qty(Pcs)";
	}
	else if($report_type==6)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";
			$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}
		$hader_caption="Country Ship Date Wise Budget On Shipout Report";
		$header_ord_qty="Country Order Qty(Pcs)";
		$header_act_ship_qty="Country Actual Ship Qty(Pcs)";
	}
	$job_no=str_replace("'","",$txt_job_no);
	//echo $job_no;die;
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
//	if($season=="") $season_cond=""; else $season_cond=" and a.season in('".implode("','",explode(",",$season))."')";
	$txt_season_id=str_replace("'","",$txt_season_id);
	if($txt_season_id=="") $season_cond=""; else $season_cond=" and a.season_buyer_wise in(".$txt_season_id.")";

	$order_no=str_replace("'","",$txt_order_id);
	$order_num=str_replace("'","",$txt_order_no);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and b.id in ($order_no)";
	else if ($order_num=="") $order_no_cond=""; else $order_no_cond=" and  b.po_number in ('$order_num') ";

	$style1="#E9F3FF";
	$style="#FFFFFF";

	$asking_profit_arr=array();  $actual_shipout_arr=array();

	$asking_profit=sql_select("select id,company_id,asking_profit,max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 $date_max_profit");
	foreach($asking_profit as $ask_row )
	{
		$asking_profit_arr[$ask_row[csf('company_id')]]['asking_profit']=$ask_row[csf('asking_profit')];
		$asking_profit_arr[$ask_row[csf('company_id')]]['max_profit']=$ask_row[csf('max_profit')];
	} //var_dump($asking_profit_arr);

	if($report_type==5)
	{
		$act_ahip_arr=sql_select("select po_break_down_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as shipout_qty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty
		from  pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id");// quotation_id='$data'
		foreach($act_ahip_arr as $row)
		{
			$actual_shipout_arr[$row[csf('po_break_down_id')]]['shipout_qty']=$row[csf('shipout_qty')]-$row[csf('return_qnty')];
		}
	}
	else if($report_type==6)
	{
		$act_ahip_arr=sql_select("select po_break_down_id, country_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as shipout_qty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty
		from  pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id, country_id");// quotation_id='$data'
		foreach($act_ahip_arr as $row)
		{
			$actual_shipout_arr[$row[csf('po_break_down_id')]][$row[csf('country_id')]]['shipout_qty']=$row[csf('shipout_qty')]-$row[csf('return_qnty')];
		}
	}

	$financial_para=array();
	$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute from lib_standard_cm_entry where company_id=$company_name and status_active=1 and	is_deleted=0 and interest_expense>0 and income_tax>0 order by id");
	foreach($sql_std_para as $sql_std_row)
	{
		$financial_para[csf('interest_expense')]=$sql_std_row[csf('interest_expense')];
		$financial_para[csf('income_tax')]=$sql_std_row[csf('income_tax')];
		$financial_para[csf('cost_per_minute')]=$sql_std_row[csf('cost_per_minute')];
	}
	ob_start();
	?>
	<br/>
	<div>
        <table width="3980">
            <tr class="form_caption">
                <td colspan="41" align="center"><strong><? echo $hader_caption; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="41" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" width="3985" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="35" rowspan="2">SL</th>
                    <th width="120" rowspan="2">Buyer</th>
                    <th width="100" rowspan="2">Job</th>
                    <th width="110" rowspan="2">Style Ref.</th>
                    <th width="100" rowspan="2">Order No</th>
                    <th width="100" rowspan="2"><? echo $header_ord_qty; ?></th>
                    <th width="80" rowspan="2">File No</th>
                    <th width="80" rowspan="2">Internal Ref: No</th>
                    <th width="100" rowspan="2"><? echo $header_act_ship_qty; ?></th>
                    <th width="110" rowspan="2">Particulars</th>
                    <th width="100" rowspan="2">Gross FOB Value</th>
                    <th width="100" rowspan="2">Less Commission</th>
                    <th width="100" rowspan="2">Net FOB Value</th>
                    <th width="100" rowspan="2">Less Cost Of Material & Service</th>
                    <th width="100" rowspan="2">Yarn Cost</th>
                    <th width="80" rowspan="2">Fab Purchase</th>
                    <th width="100" rowspan="2">Conversion Cost</th>
                    <th colspan="8">Conversion Cost(Dyeing & Finishing)</th>
                    <th width="100" rowspan="2">Trims Cost</th>
                    <th width="100" rowspan="2">Embellishment Cost</th>
                    <th colspan="3">Embell. Cost</th>
                    <th width="100" rowspan="2">Other Direct Exp.</th>
                    <th width="100" rowspan="2">Contribution /Value Additions</th>
                    <th width="100" rowspan="2">Less CM Cost</th>
                    <th width="100" rowspan="2">Gross Profit/Loss Cost</th>
                    <th width="100" rowspan="2">Less Commercial Cost</th>
                    <th width="100" rowspan="2">Less Operating Exp.</th>
                    <th width="100" rowspan="2">Operation Profit/Loss</th>
                    <th width="100" rowspan="2">Less Deprecation & Amortization</th>
                    <th width="100" rowspan="2">Less Interest</th>
                    <th width="100" rowspan="2">Less Income Tex</th>
                    <th rowspan="2">Net Profit</th>
                </tr>
                <tr>
                	 <th width="100">Knitting Cost</th>
                      <th width="100">Dyeing Cost</th>
                      <th width="100">Yarn Dyed Cost</th>
					  <th width="100">AOP Cost</th>
                      <th width="100">Heat Setting</th>
                      <th width="100">Finishing Cost</th>
                      <th width="100">Washing Cost</th>
                      <th width="100">Other Cost</th>

                      <th width="80">Printing</th>
                      <th width="85">Embroidery</th>
                      <th width="80">Special Works</th>
                </tr>
            </thead>
        </table>
        <div style="width:4005px; max-height:250px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="3985" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <?
		$total_order_value=0; $total_commission_cost=0; $total_net_fob_value=0; $total_yarn_costing=0; $total_tot_conversion_cost=0; $total_tot_trim_cost=0; $total_tot_embell_cost=0; $total_cost_of_material_service=0; $total_other_direct_expenses=0; $total_contribution_value=0; $total_cm_cost=0; $total_gross_profit=0; $total_tot_commercial_cost=0;$total_operating_expense=0; $total_operating_profit=0; $total_depreciation_amortization=0; $total_interest_expense=0; $total_income_tax=0; $total_net_profit=0;

		$total_pro_order_value=0; $total_pro_commission_cost=0; $total_pro_net_fob_value=0; $total_pro_cost_of_material_service=0; $total_pro_yarn_costing=0;$total_pro_conversion_cost=0; $total_pro_knit_cost=0; $total_pro_fabric_dyeing_cost=0; $total_pro_yarn_dyed_cost=0; $total_pro_heat_setting_cost=0; $total_pro_fabric_finish=0;$total_pro_washing_cost=0; $total_pro_all_over_cost=0; $total_pro_other_cost_conv=0; $total_pro_trim_cost=0; $total_pro_embell_cost=0; $total_pro_print_cost=0;$total_pro_embroidery_cost=0; $total_pro_special_cost=0; $total_pro_other_direct_expenses=0; $total_pro_contribution_value=0; $total_pro_cm_cost=0; $total_pro_gross_profit=0;$total_pro_commercial_cost=0; $total_pro_operating_expense=0; $total_pro_operating_profit=0; $total_pro_depreciation_amortization=0; $total_pro_interest_expense=0;$total_pro_income_tax=0; $total_pro_net_profit=0;

		$total_per_order_value=0; $total_pro_commission_cost=0; $total_per_net_fob_value=0; $total_per_cost_of_material_service=0; $total_per_yarn_costing=0;$total_per_conversion_cost=0; $total_per_trim_cost=0; $total_per_embell_cost=0; $total_per_other_direct_expenses=0; $total_per_contribution_value=0; $total_per_cm_cost=0;$total_per_gross_profit=0; $total_per_tot_commercial_cost=0; $total_per_operating_expense=0; $total_per_operating_profit=0; $total_per_depreciation_amortization=0;$total_per_interest_expense=0; $total_per_income_tax=0; $total_per_net_profit=0;

		$total_actual_shipout_val=0; $total_actual_commission_cost=0; $total_actual_net_fob_value=0; $total_actual_cost_of_material_service=0; $total_actual_yarn_costing=0; $total_actual_conversion_cost=0; $total_actual_tot_knit_cost=0; $total_actual_fabric_dyeing_cost=0; $total_actual_yarn_dyed_cost=0; $total_actual_all_over_cost=0;	 $total_actual_heat_setting_cost=0; $total_actual_fabric_finish=0; $total_actual_washing_cost=0; $total_actual_other_cost_conv=0; $total_actual_trim_cost=0; $total_actual_embell_cost=0; $total_actual_print_cost=0; $total_actual_embroidery_cost=0; $total_actual_special_cost=0; $total_actual_other_direct_expenses=0; $total_actual_contribution_value=0; $total_actual_cm_cost=0; $total_actual_gross_profit=0; $total_actual_tot_commercial_cost=0; $total_actual_operating_expense=0;$total_actual_operating_profit=0; $total_actual_depreciation_amortization=0; $total_actual_interest_expense=0; $total_actual_income_tax=0; $total_actual_net_profit=0;

		$total_purchase_costing=0;$total_actual_fab_purchase=0;$total_pro_fab_purchase_cost=0;$total_per_fab_purchase_costing=0;

		if($report_type==5)
		{
			 $sql="select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.avg_unit_price, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity, b.plan_cut, b.unit_price, b.shiping_status, b.grouping, b.file_no, b.po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond  $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $internal_ref_cond order by b.id ";// and b.shiping_status=3
		}
		else if($report_type==6)
		{
			if($db_type==0) $all_country_id="group_concat(c.country_id)";
			else if($db_type==2) $all_country_id="LISTAGG(c.country_id, ',') WITHIN GROUP (ORDER BY c.country_id)";

			 $sql="select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.avg_unit_price, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.unit_price, b.shiping_status, b.grouping, b.file_no, sum(c.order_quantity) as po_quantity, $all_country_id as country_id, sum(c.plan_cut_qnty) as plan_cut, sum(c.order_total) as po_total_price from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $order_status_cond $season_cond $internal_ref_cond group by a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.avg_unit_price, a.total_set_qnty, b.id, b.po_number, b.unit_price, b.shiping_status, b.grouping, b.file_no order by b.id ";
		}
		//echo $sql;//die;
		$result=sql_select($sql); $i=1;
		$tot_rows=count($result);

		$condition= new condition();
		$condition->company_name("=$cbo_company_name");
		if(str_replace("'","",$cbo_buyer_name)>0){
			$condition->buyer_name("=$cbo_buyer_name");
		}
		if(str_replace("'","",$txt_job_no) !=''){
			$condition->job_no_prefix_num("=$txt_job_no");
		}
		if(str_replace("'","",$cbo_order_status) >0){
			$condition->is_confirmed("=$cbo_order_status");
		}
		if(str_replace("'","",$cbo_order_status)==0){
			$condition->is_confirmed("in(1,2)");
		}
		if($report_type==5)
		{
			if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			}
		}
		if ($report_type==6)
		{
			if(str_replace("'","",$cbo_search_date) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
				$condition->country_ship_date(" between '$start_date' and '$end_date'");
			}
		}
		if(str_replace("'","",$cbo_search_date) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
		{
			$condition->po_received_date(" between '$start_date' and '$end_date'");

		}

		if(str_replace("'","",$cbo_search_date)==3 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
		{
		}
		if(str_replace("'","",$txt_file_no)!='')
		{
			$condition->file_no("=$txt_file_no");
		}
		if(str_replace("'","",$txt_internal_ref)!='')
		{
			$condition->grouping("=$txt_internal_ref");
		}
		if(str_replace("'","",$txt_order_no)!='')
		{
			$condition->po_number("=$txt_order_no");
		}
		if(str_replace("'","",$txt_season)!='')
		{
			$condition->season("=$txt_season");
		}
		$condition->init();
		//$yarn= new yarn($condition);



		//echo $yarn->getQuery(); die;







		if($report_type==5)
		{
			$yarn= new yarn($condition);
			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
			$yarn= new yarn($condition);
			$yarn_des_data=$yarn->getCountCompositionAndTypeWiseYarnQtyAndAmountArray();
			$fabric= new fabric($condition);
			$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			$conversion= new conversion($condition);
			$conversion_costing_arr=$conversion->getAmountArray_by_order();
			$conversion= new conversion($condition);
			$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
			$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_order();
			$emblishment= new emblishment($condition);
			$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
			$emblishment= new emblishment($condition);
			$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
			$other= new other($condition);
			$other_costing_arr=$other->getAmountArray_by_order();
			$commercial= new commercial($condition);
			$commercial_costing_arr=$commercial->getAmountArray_by_order();
			$commission= new commision($condition);

			$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
			$wash= new wash($condition);
			$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
		}
		else if($report_type==6)
		{
			$yarn= new yarn($condition);
			$yarn_costing_arr=$yarn->getOrderAndCountryWiseYarnAmountArray();
			$yarn= new yarn($condition);
			$yarn_des_data=$yarn->getCountCompositionAndTypeWiseYarnQtyAndAmountArray();
			$fabric= new fabric($condition);
			$fabric_costing_arr=$fabric->getAmountArray_by_OrderAndCountry_knitAndwoven_greyAndfinish();
			$conversion= new conversion($condition);
			$conversion_costing_arr=$conversion->getAmountArray_by_orderAndCountry();
			$conversion= new conversion($condition);
			$conversion_costing_arr_process=$conversion->getAmountArray_by_orderCountryAndProcess();
			$trims= new trims($condition);
			$trims_costing_arr=$trims->getAmountArray_by_orderAndCountry();
			$emblishment= new emblishment($condition);
			$emblishment_costing_arr=$emblishment->getAmountArray_by_orderAndCountry();
			$emblishment= new emblishment($condition);
			$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderCountryAndEmbname();
			$other= new other($condition);
			$other_costing_arr=$other->getAmountArray_by_orderAndCountry();
			$commercial= new commercial($condition);
			$commercial_costing_arr=$commercial->getAmountArray_by_orderAndCountry();
			$commission= new commision($condition);
			$commission_costing_arr=$commission->getAmountArray_by_orderCountryAndItemid();
			$wash= new wash($condition);
			$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderCountryAndEmbname();
		}

		/*$fabric_dyeingCost_arr=array(25,31,32,38,60,61,61,63,72,74,78,79,80,81,84,85,86,87);
		$aop_cost_arr=array(35,36,37);
		$fab_finish_cost_arr=array(34,65,66,67,68,69,70,71,73,75,77,88,90,91,92,93,100,125,127,128,129);
		$washing_cost_arr=array(64,82,89);
		$other_cost_arr=array(4,26,39,40,76,83,94,120,124,130,131,132,133,134,135,136,137,138);*/
		$knit_cost_arr=array(1,2,3,4);
		$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149);
		$aop_cost_arr=array(35,36,37,40);
		$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
		$washing_cost_arr=array(140,142,148,64);

		foreach($result as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			$dzn_qnty=0;
			$costing_per_id=$costing_per_arr[$row[csf('job_no')]];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
			if($costing_per_id==1) $dzn_qnty=12;
			else if($costing_per_id==3) $dzn_qnty=12*2;
			else if($costing_per_id==4) $dzn_qnty=12*3;
			else if($costing_per_id==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;

			$dzn_qnty_yarn=$dzn_qnty*$row[csf('ratio')];
			$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
			$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
			$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];

			$order_value=$row[csf('po_total_price')];
			$gross_fob_value=$row[csf('po_total_price')];

			if($report_type==5)
			{
				$actual_shipout_qty=$actual_shipout_arr[$row[csf('po_id')]]['shipout_qty'];
				$actual_shipout_val=$actual_shipout_qty*$row[csf('unit_price')];

				$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
				$local=$commission_costing_arr[$row[csf('po_id')]][2];
				$commission_cost=$foreign+$local;
				$net_fob_value=$gross_fob_value-$commission_cost;

				$yarn_costing=$yarn_costing_arr[$row[csf('po_id')]];
				if(is_infinite($yarn_costing) || is_nan($yarn_costing)){$yarn_costing=0;}
				$fab_purchase_knit=($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]]);
				if(is_infinite($fab_purchase_knit) || is_nan($fab_purchase_knit)){$fab_purchase_knit=0;}
				$fab_purchase_woven=($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
				if(is_infinite($fab_purchase_woven) || is_nan($fab_purchase_woven)){$fab_purchase_woven=0;}
				$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;

				$tot_conversion_cost=($conversion_costing_arr[$row[csf('po_id')]]);
				if(is_infinite($tot_conversion_cost) || is_nan($tot_conversion_cost)){$tot_conversion_cost=0;}
				$tot_trim_cost= $trims_costing_arr[$row[csf('po_id')]];
				if(is_infinite($tot_trim_cost) || is_nan($tot_trim_cost)){$tot_trim_cost=0;}
				$tot_embell_cost=$emblishment_costing_arr[$row[csf('po_id')]];
				if(is_infinite($tot_embell_cost) || is_nan($tot_embell_cost)){$tot_embell_cost=0;}
				$freight_cost=$other_costing_arr[$row[csf('po_id')]]['freight'];
				if(is_infinite($freight_cost) || is_nan($freight_cost)){$freight_cost=0;}
				$inspection=$other_costing_arr[$row[csf('po_id')]]['inspection'];
				if(is_infinite($inspection) || is_nan($inspection)){$inspection=0;}
				$certificate_cost=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
				if(is_infinite($certificate_cost) || is_nan($certificate_cost)){$certificate_cost=0;}
				$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];
				if(is_infinite($currier_cost) || is_nan($currier_cost)){$currier_cost=0;}
				$lab_test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
				if(is_infinite($lab_test_cost) || is_nan($lab_test_cost)){$lab_test_cost=0;}
				$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('po_id')]][3];
				if(is_infinite($wash_cost) || is_nan($wash_cost)){$wash_cost=0;}

				$other_direct_expenses=$freight_cost+$inspection+$certificate_cost+$currier_cost+$lab_test_cost+$wash_cost;
				$cost_of_material_service=$yarn_costing+$tot_conversion_cost+$tot_trim_cost+$tot_embell_cost+$other_direct_expenses+$fab_purchase;
				if(is_infinite($cost_of_material_service) || is_nan($cost_of_material_service)){$cost_of_material_service=0;}


				$tot_knit_cost=($conversion_costing_arr_process[$row[csf('po_id')]][1])+($conversion_costing_arr_process[$row[csf('po_id')]][2])+($conversion_costing_arr_process[$row[csf('po_id')]][3]);

				$fabric_dyeing_cost=0;
				foreach($fabric_dyeingCost_arr as $dye_id)
				{
					$fabric_dyeing_cost+=($conversion_costing_arr_process[$row[csf('po_id')]][$dye_id]);
				}

				$yarn_dyed_cost=($conversion_costing_arr_process[$row[csf('po_id')]][30]);

				$all_over_cost=0;
				foreach ($aop_cost_arr as $aop_id)
				{
					$all_over_cost+=($conversion_costing_arr_process[$row[csf('po_id')]][$aop_id]);
				}

				$heat_setting_cost=($conversion_costing_arr_process[$row[csf('po_id')]][33]);

				$fabric_finish=0;
				foreach($fab_finish_cost_arr as $fab_fin_id)
				{
					$fabric_finish+=($conversion_costing_arr_process[$row[csf('po_id')]][$fab_fin_id]);
				}

				$washing_cost=0;
				foreach($washing_cost_arr as $washing_id)
				{
					$washing_cost+=($conversion_costing_arr_process[$row[csf('po_id')]][$washing_id]);
				}

				$other_cost_conv=0;
				foreach($other_cost_arr as $other_id)
				{
					$other_cost_conv+=($conversion_costing_arr_process[$row[csf('po_id')]][$other_id]);
				}
				$print_cost=$emblishment_costing_arr_name[$row[csf('po_id')]][1];
				if(is_infinite($print_cost) || is_nan($print_cost)){$print_cost=0;}
				$embroidery_cost=$emblishment_costing_arr_name[$row[csf('po_id')]][2];
				if(is_infinite($embroidery_cost) || is_nan($embroidery_cost)){$embroidery_cost=0;}
				$special_cost=$emblishment_costing_arr_name[$row[csf('po_id')]][4];
				if(is_infinite($special_cost) || is_nan($special_cost)){$special_cost=0;}
				$contribution_value=$net_fob_value-$cost_of_material_service;
				$cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
				if(is_infinite($cm_cost) || is_nan($cm_cost)){$cm_cost=0;}
				$gross_profit=$contribution_value-$cm_cost;
				$commercial_cost=$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
				if(is_infinite($commercial_cost) || is_nan($commercial_cost)){$commercial_cost=0;}
				$operating_expense=$other_costing_arr[$row[csf('po_id')]]['common_oh'];
				if(is_infinite($operating_expense) || is_nan($operating_expense)){$operating_expense=0;}
				$tot_commercial_cost=$commercial_costing_arr[$row[csf('po_id')]];
				if(is_infinite($tot_commercial_cost) || is_nan($tot_commercial_cost)){$tot_commercial_cost=0;}

				$operating_profit=$gross_profit-($tot_commercial_cost+$operating_expense);
				$depreciation_amortization=$other_costing_arr[$row[csf('po_id')]]['depr_amor_pre_cost'];
				$interest_expense=$net_fob_value*$financial_para[csf('interest_expense')]/100;
				if(is_infinite($interest_expense) || is_nan($interest_expense)){$interest_expense=0;}
				$income_tax=$net_fob_value*$financial_para[csf('income_tax')]/100;
				if(is_infinite($income_tax) || is_nan($income_tax)){$income_tax=0;}
				$net_profit=$operating_profit-($depreciation_amortization+$interest_expense+$income_tax);
			}
			else if($report_type==6)
			{
				$actual_shipout_qty=0; $actual_shipout_val=0; $foreign=0; $local=0; $commission_cost=0; $yarn_costing=0; $tot_conversion_cost=0; $tot_trim_cost=0; $tot_embell_cost=0; $freight_cost=0; $inspection=0; $certificate_cost=0; $currier_cost=0; $lab_test_cost=0; $wash_cost=0; $tot_knit_cost=0; $fabric_dyeing_cost=0; $yarn_dyed_cost=0; $all_over_cost=0; $heat_setting_cost=0; $fabric_finish=0; $washing_cost=0; $other_cost_conv=0; $print_cost=0; $embroidery_cost=0; $special_cost=0; $cm_cost=0; $commercial_cost=0; $operating_expense=0; $tot_commercial_cost=0; $depreciation_amortization=0; $interest_expense=0; $income_tax=0;

				$ex_all_country_id=array_unique(explode(',',$row[csf('country_id')]));

				foreach($ex_all_country_id as $country_id)
				{
					$actual_shipout_qty+=$actual_shipout_arr[$row[csf('po_id')]][$country_id]['shipout_qty'];

					$foreign+=$commission_costing_arr[$row[csf('po_id')]][$country_id][1];
					$local+=$commission_costing_arr[$row[csf('po_id')]][$country_id][2];
					$yarn_costing+=$yarn_costing_arr[$row[csf('po_id')]][$country_id];
					$fab_purchase_knit=($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]][$country_id]);
					$fab_purchase_woven=($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]][$country_id]);
					$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;

					$tot_conversion_cost+=($conversion_costing_arr[$row[csf('po_id')]][$country_id]);
					$tot_trim_cost+=$trims_costing_arr[$row[csf('po_id')]][$country_id];
					$tot_embell_cost+=$emblishment_costing_arr[$row[csf('po_id')]][$country_id];
					$freight_cost+=$other_costing_arr[$row[csf('po_id')]][$country_id]['freight'];
					$inspection=+$other_costing_arr[$row[csf('po_id')]][$country_id]['inspection'];
					$certificate_cost+=$other_costing_arr[$row[csf('po_id')]][$country_id]['certificate_pre_cost'];
					$currier_cost+=$other_costing_arr[$row[csf('po_id')]][$country_id]['currier_pre_cost'];
					$lab_test_cost+=$other_costing_arr[$row[csf('po_id')]][$country_id]['lab_test'];
					$wash_cost+=$emblishment_costing_arr_name_wash[$row[csf('po_id')]][$country_id][3];
					if(is_infinite($wash_cost) || is_nan($wash_cost)){$wash_cost=0;}

					$tot_knit_cost+=($conversion_costing_arr_process[$row[csf('po_id')]][$country_id][1])+array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$country_id][2])+array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$country_id][3]);

					foreach($fabric_dyeingCost_arr as $dye_id)
					{
						$fabric_dyeing_cost+=($conversion_costing_arr_process[$row[csf('po_id')]][$country_id][$dye_id]);
					}
					$yarn_dyed_cost+=($conversion_costing_arr_process[$row[csf('po_id')]][$country_id][30]);

					foreach ($aop_cost_arr as $aop_id)
					{
						$all_over_cost+=($conversion_costing_arr_process[$row[csf('po_id')]][$country_id][$aop_id]);
					}
					$heat_setting_cost+=($conversion_costing_arr_process[$row[csf('po_id')]][$country_id][33]);

					foreach($fab_finish_cost_arr as $fab_fin_id)
					{
						$fabric_finish+=($conversion_costing_arr_process[$row[csf('po_id')]][$country_id][$fab_fin_id]);
					}

					foreach($washing_cost_arr as $washing_id)
					{
						$washing_cost+=($conversion_costing_arr_process[$row[csf('po_id')]][$country_id][$washing_id]);
					}

					foreach($other_cost_arr as $other_id)
					{
						$other_cost_conv+=($conversion_costing_arr_process[$row[csf('po_id')]][$country_id][$other_id]);
					}
					$print_cost+=$emblishment_costing_arr_name[$row[csf('po_id')]][$country_id][1];
					$embroidery_cost+=$emblishment_costing_arr_name[$row[csf('po_id')]][$country_id][2];
					$special_cost+=$emblishment_costing_arr_name[$row[csf('po_id')]][$country_id][4];

					$cm_cost+=$other_costing_arr[$row[csf('po_id')]][$country_id]['cm_cost'];

					$commercial_cost+=$fabriccostArray[$row[csf('job_no')]][$country_id]['comm_cost'];
					$operating_expense+=$other_costing_arr[$row[csf('po_id')]][$country_id]['common_oh'];
					$tot_commercial_cost+=$commercial_costing_arr[$row[csf('po_id')]][$country_id];

					$depreciation_amortization+=$other_costing_arr[$row[csf('po_id')]][$country_id]['depr_amor_pre_cost'];
				}
				$actual_shipout_val=$actual_shipout_qty*$row[csf('unit_price')];
				$commission_cost=$foreign+$local;
				$net_fob_value=$gross_fob_value-$commission_cost;
				$interest_expense=$net_fob_value*$financial_para[csf('interest_expense')]/100;
				$income_tax=$net_fob_value*$financial_para[csf('income_tax')]/100;

				$other_direct_expenses=$freight_cost+$inspection+$certificate_cost+$currier_cost+$lab_test_cost+$wash_cost;
				if(is_infinite($other_direct_expenses) || is_nan($other_direct_expenses)){$other_direct_expenses=0;}

				$cost_of_material_service=$yarn_costing+$tot_conversion_cost+$tot_trim_cost+$tot_embell_cost+$other_direct_expenses+$fab_purchase;
				if(is_infinite($cost_of_material_service) || is_nan($cost_of_material_service)){$cost_of_material_service=0;}


				$contribution_value=$net_fob_value-$cost_of_material_service;
				$gross_profit=$contribution_value-$cm_cost;
				$operating_profit=$gross_profit-($tot_commercial_cost+$operating_expense);
				$net_profit=$operating_profit-($depreciation_amortization+$interest_expense+$income_tax);
			}
			//==================Total Budget================
			$total_order_value+=$order_value;
			$total_commission_cost+=$commission_cost;
			$total_net_fob_value+=$net_fob_value;
			$total_yarn_costing+=$yarn_costing;
			$total_purchase_costing+=$fab_purchase;

			$total_cost_of_material_service+=$cost_of_material_service;
			$total_tot_conversion_cost+=$tot_conversion_cost;

			$total_tot_knit_cost+=$tot_knit_cost;
			$total_fabric_dyeing_cost+=$fabric_dyeing_cost;
			$total_yarn_dyed_cost+=$yarn_dyed_cost;
			$total_all_over_cost+=$all_over_cost;
			$total_heat_setting_cost+=$heat_setting_cost;
			$total_fabric_finish+=$fabric_finish;
			$total_washing_cost+=$washing_cost;
			$total_other_cost_conv+=$other_cost_conv;

			$total_tot_trim_cost+=$tot_trim_cost;
			$total_tot_embell_cost+=$tot_embell_cost;

			$total_print_cost+=$print_cost;
			$total_embroidery_cost+=$embroidery_cost;
			$total_special_cost+=$special_cost;

			$total_other_direct_expenses+=$other_direct_expenses;
			$total_contribution_value+=$contribution_value;
			$total_cm_cost+=$cm_cost;
			$total_gross_profit+=$gross_profit;
			$total_tot_commercial_cost+=$tot_commercial_cost;
			$total_operating_expense+=$operating_expense;
			$total_operating_profit+=$operating_profit;
			$total_depreciation_amortization+=$depreciation_amortization;
			$total_interest_expense+=$interest_expense;
			$total_income_tax+=$income_tax;
			$total_net_profit+=$net_profit;
			//=========================Actual_shipout========
			$actual_foreign=($foreign/$order_qty_pcs)*$actual_shipout_qty;
			$actual_local=($local/$order_qty_pcs)*$actual_shipout_qty;
			$actual_commission_cost=$actual_foreign+$actual_local;
			$actual_net_fob_value=$actual_shipout_val-$actual_commission_cost;

			$actual_yarn_costing=($yarn_costing/$order_qty_pcs)*$actual_shipout_qty;
			$actual_fab_purchase=($fab_purchase*$actual_shipout_qty)/$row[csf('po_quantity')];
			$actual_conversion_cost=($tot_conversion_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_trim_cost=($tot_trim_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_embell_cost=($tot_embell_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_freight_cost=($freight_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_inspection=($inspection/$order_qty_pcs)*$actual_shipout_qty;
			$actual_certificate_cost=($certificate_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_currier_cost=($currier_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_lab_test_cost=($lab_test_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_wash_cost=($wash_cost/$order_qty_pcs)*$actual_shipout_qty;

			$actual_other_direct_expenses=$actual_freight_cost+$actual_inspection+$actual_certificate_cost+$actual_currier_cost+$actual_lab_test_cost+$actual_wash_cost;
		    if(is_infinite($actual_other_direct_expenses) || is_nan($actual_other_direct_expenses)){$actual_other_direct_expenses=0;}

 			$actual_cost_of_material_service=$actual_yarn_costing+$actual_conversion_cost+$actual_trim_cost+$actual_embell_cost+$actual_other_direct_expenses;
			if(is_infinite($actual_cost_of_material_service) || is_nan($actual_cost_of_material_service)){$actual_cost_of_material_service=0;}

			$actual_knit_cost=($tot_knit_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_fabric_dyeing_cost=($fabric_dyeing_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_yarn_dyed_cost=($yarn_dyed_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_all_over_cost=($all_over_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_heat_setting_cost=($heat_setting_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_fabric_finish=($fabric_finish/$order_qty_pcs)*$actual_shipout_qty;
			$actual_washing_cost=($washing_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_other_cost_conv=($other_cost_conv/$order_qty_pcs)*$actual_shipout_qty;

			$actual_print_cost=($print_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_embroidery_cost=($embroidery_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_special_cost=($special_cost/$order_qty_pcs)*$actual_shipout_qty;

			$actual_contribution_value=$actual_net_fob_value-$actual_cost_of_material_service;
			$actual_cm_cost=($cm_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_gross_profit=$actual_contribution_value-$actual_cm_cost;
			$actual_tot_commercial_cost=($tot_commercial_cost/$order_qty_pcs)*$actual_shipout_qty;
			$actual_operating_expense=($operating_expense/$order_qty_pcs)*$actual_shipout_qty;
			$actual_operating_profit=$actual_gross_profit-($actual_tot_commercial_cost+$actual_operating_expense);
			$actual_depreciation_amortization=($depreciation_amortization/$order_qty_pcs)*$actual_shipout_qty;
			$actual_interest_expense=$actual_net_fob_value*$financial_para[csf('interest_expense')]/100;
			$actual_income_tax=$actual_net_fob_value*$financial_para[csf('income_tax')]/100;
			$actual_net_profit=$actual_operating_profit-($actual_depreciation_amortization+$actual_interest_expense+$actual_income_tax);
				//==================Total Actual Achievment================
			$total_actual_shipout_val+=$actual_shipout_val;
			$total_actual_commission_cost+=$actual_commission_cost;
			$total_actual_net_fob_value+=$actual_net_fob_value;
			$total_actual_cost_of_material_service+=$actual_cost_of_material_service;
			$total_actual_yarn_costing+=$actual_yarn_costing;
			$total_actual_fab_purchase+=$actual_fab_purchase;
			$total_actual_conversion_cost+=$actual_conversion_cost;

			$total_actual_tot_knit_cost+=$actual_knit_cost;
			$total_actual_fabric_dyeing_cost+=$actual_fabric_dyeing_cost;
			$total_actual_yarn_dyed_cost+=$actual_yarn_dyed_cost;
			$total_actual_all_over_cost+=$actual_all_over_cost;
			$total_actual_heat_setting_cost+=$actual_heat_setting_cost;
			$total_actual_fabric_finish+=$actual_fabric_finish;
			$total_actual_washing_cost+=$actual_washing_cost;
			$total_actual_other_cost_conv+=$actual_other_cost_conv;

			$total_actual_trim_cost+=$actual_trim_cost;
			$total_actual_embell_cost+=$actual_embell_cost;

			$total_actual_print_cost+=$actual_print_cost;
			$total_actual_embroidery_cost+=$actual_embroidery_cost;
			$total_actual_special_cost+=$actual_special_cost;

			$total_actual_other_direct_expenses+=$actual_other_direct_expenses;
			$total_actual_contribution_value+=$actual_contribution_value;
			$total_actual_cm_cost+=$actual_cm_cost;
			$total_actual_gross_profit+=$actual_gross_profit;
			$total_actual_tot_commercial_cost+=$actual_tot_commercial_cost;
			$total_actual_operating_expense+=$actual_operating_expense;
			$total_actual_operating_profit+=$actual_operating_profit;
			$total_actual_depreciation_amortization+=$actual_depreciation_amortization;
			$total_actual_interest_expense+=$actual_interest_expense;
			$total_actual_income_tax+=$actual_income_tax;
			$total_actual_net_profit+=$actual_net_profit;
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
				<td width="35" rowspan="4"><? echo $i; ?></td>
				<td width="120" rowspan="4"><p><? echo $buyer_library[$row[csf('buyer_name')]] ?></p></td>
				<td width="100" rowspan="4"><p><? echo $row[csf('job_no')]; ?></p></td>
				<td width="110" rowspan="4"><p><? echo  $row[csf('style_ref_no')]; ?></p></td>
				<td width="100" rowspan="4"><p><? echo $row[csf('po_number')]; ?></p></td>
				<td width="100" rowspan="4" align="right"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
				<td width="80" rowspan="4"><p><? echo $row[csf('file_no')]; ?></p></td>
				<td width="80" rowspan="4"><p><? echo $row[csf('grouping')]; ?></p></td>
				<td width="100" rowspan="4" align="right"><p>
				<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no')];?>',
 '<? echo $row[csf('po_id')]; ?>','750px')"><? echo  number_format($actual_shipout_qty,2); ?></a>
				<? //echo number_format($actual_shipout_qty,2); ?></p></td>
				<td width="110"><p><strong>Budget Value</strong></p></td>
				<td width="100" align="right"><p><? echo number_format($order_value,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($commission_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($net_fob_value,2); ?></p></td>
				<td width="100" align="right" title="Yarn Costing+Conversion Cost+Trim Cost+Embell Cost+Other Direct Expenses"><p><? echo number_format($cost_of_material_service,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($yarn_costing,2); ?></p></td>
                <td width="80" align="right"><p><a href="##" onClick="generate_precost_fab_purchase_detail('<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('job_no')];?>','<? echo $company_name; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $fab_source_id; ?>','fab_purchase_detail')"><? echo number_format($fab_purchase,2); ?></a></p></td>
				<td width="100" align="right"><p><? echo number_format($tot_conversion_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($tot_knit_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($fabric_dyeing_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($yarn_dyed_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($all_over_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($heat_setting_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($fabric_finish,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($washing_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($other_cost_conv,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($tot_trim_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($tot_embell_cost,2); ?></p></td>

				<td width="80" align="right"><p><? echo number_format($print_cost,2); ?></p></td>
				<td width="85" align="right"><p><? echo number_format($embroidery_cost,2); ?></p></td>
				<td width="80" align="right"><p><? echo number_format($special_cost,2); ?></p></td>

				<td width="100" align="right" title="Freight Cost + Inspection + Certificate Cost+Currier Cost+Lab Test Cost+Wash Cost"><p><? echo number_format($other_direct_expenses,2); ?></p></td>
				<td width="100" align="right" title=" Net Fob Value-Actual Cost Of Material Service"><p><? echo number_format($contribution_value,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($cm_cost,2); ?></p></td>
				<td width="100" align="right" title=" Contribution Value-Actual Cm Cost"><p><? echo number_format($gross_profit,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($tot_commercial_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($operating_expense,2); ?></p></td>
				<td width="100" align="right" title="Gross Profit-(Actual Tot Commercial Cost+Actual Operating Expense)"><p><? echo number_format($operating_profit,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($depreciation_amortization,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($interest_expense,2); ?></p></td>
				<td width="100" align="right" title="Net_fob_value*Financial_para/100"><p><? echo number_format($income_tax,2); ?></p></td>
				<td align="right" title="operating_profit-(Actual_depreciation_amortization+Actual_interest_expense+Actual_income_tax)"><p><? echo number_format($net_profit,2); ?></p></td>
			</tr>
			<tr>
				<td width="90"><p><strong>Budget Achivement</strong></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_shipout_val,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_commission_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_net_fob_value,2); ?></p></td>
				<td width="100" align="right" title="Actual Yarn Costing+Conversion Cost + Trim Cost + Embell Cost + Other Direct Expenses"><p><? echo number_format($actual_cost_of_material_service,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_yarn_costing,2); ?></p></td>
                <td width="80" align="right"><p><? echo number_format($actual_fab_purchase,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_conversion_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_knit_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_fabric_dyeing_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_yarn_dyed_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_all_over_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_heat_setting_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_fabric_finish,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_washing_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_other_cost_conv,2); ?></p></td>

				<td width="100" align="right"><p><? echo number_format($actual_trim_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_embell_cost,2); ?></p></td>

				<td width="80" align="right"><p><? echo number_format($actual_print_cost,2); ?></p></td>
				<td width="85" align="right"><p><? echo number_format($actual_embroidery_cost,2); ?></p></td>
				<td width="80" align="right"><p><? echo number_format($actual_special_cost,2); ?></p></td>

				<td width="100" align="right" title="Freight Cost + Inspection + Certificate Cost+Currier Cost+Lab Test Cost+Wash Cost"><p><? echo number_format($actual_other_direct_expenses,2); ?></p></td>
				<td width="100" align="right" title="Actual Net Fob Value-Actual Cost Of Material Service"><p><? echo number_format($actual_contribution_value,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_cm_cost,2); ?></p></td>
				<td width="100" align="right" title="Actual Contribution Value-Actual Cm Cost"><p><? echo number_format($actual_gross_profit,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_tot_commercial_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_operating_expense,2); ?></p></td>
				<td width="100" align="right" title="Actual Gross Profit-(Actual Tot Commercial Cost+Actual Operating Expense)"><p><? echo number_format($actual_operating_profit,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_depreciation_amortization,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($actual_interest_expense,2); ?></p></td>
				<td width="100" align="right" title="Actual Net_fob_value*Financial_para/100"><p><? echo number_format($actual_income_tax,2); ?></p></td>
				<td align="right" title=" Actual_operating_profit-(Actual_depreciation_amortization+Actual_interest_expense+Actual_income_tax)"><p><? echo number_format($actual_net_profit,2); ?></p></td>
			</tr>
			<?
				$pro_order_value=$actual_shipout_val-$order_value;
				$pro_commission_cost=$commission_cost-$actual_commission_cost;
				$pro_net_fob_value=$actual_net_fob_value-$net_fob_value;
				$pro_cost_of_material_service=$cost_of_material_service-$actual_cost_of_material_service;
				$pro_yarn_costing=$yarn_costing-$actual_yarn_costing;
				$pro_conversion_cost=$tot_conversion_cost-$actual_conversion_cost;

				$pro_trim_cost=$tot_trim_cost-$actual_trim_cost;
				$pro_embell_cost=$tot_embell_cost-$actual_embell_cost;

				$pro_print_cost=$print_cost-$actual_print_cost;
				$pro_embroidery_cost=$embroidery_cost-$actual_embroidery_cost;
				$pro_special_cost=$special_cost-$actual_special_cost;

				$pro_other_direct_expenses=$other_direct_expenses-$actual_other_direct_expenses;
				$pro_contribution_value=$contribution_value-$actual_contribution_value;
				$pro_cm_cost=$cm_cost-$actual_cm_cost;
				$pro_gross_profit=$gross_profit-$actual_gross_profit;
				$pro_commercial_cost=$tot_commercial_cost-$actual_tot_commercial_cost;
				$pro_operating_expense=$operating_expense-$actual_operating_expense;
				$pro_operating_profit=$operating_profit-$actual_operating_profit;
				$pro_depreciation_amortization=$depreciation_amortization-$actual_depreciation_amortization;
				$pro_interest_expense=$interest_expense-$actual_interest_expense;
				$pro_income_tax=$income_tax-$actual_income_tax;
				$pro_net_profit=$net_profit-$actual_net_profit;

				$pro_knit_cost=$actual_knit_cost-$tot_knit_cost;
				$pro_fabric_dyeing_cost=$actual_fabric_dyeing_cost-$fabric_dyeing_cost;
				$pro_yarn_dyed_cost=$actual_yarn_dyed_cost-$yarn_dyed_cost;
				$pro_heat_setting_cost=$actual_heat_setting_cost-$heat_setting_cost;
				$pro_fabric_finish=$actual_fabric_finish-$fabric_finish;
				$pro_washing_cost=$actual_washing_cost-$washing_cost;
				$pro_all_over_cost=$actual_all_over_cost-$all_over_cost;
				$pro_other_cost_conv=$actual_other_cost_conv-$other_cost_conv;

				$pro_fab_purchase_cost=$actual_fab_purchase-$fab_purchase;

			?>
			<tr>
				<td width="90"><p><strong>Variance</strong></p></td>
				<td width="100" align="right" title="Net Profit-Actual_Net_profit"><p><? echo number_format($pro_order_value,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_commission_cost,2); ?></p></td>
				<td width="100" align="right" title="Actual_net_fob_value-Net_fob_value"><p><? echo number_format($pro_net_fob_value,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_cost_of_material_service,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_yarn_costing,2); ?></p></td>
                <td width="80" align="right"><p><? echo number_format($pro_fab_purchase_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_conversion_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_knit_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_fabric_dyeing_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_yarn_dyed_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_all_over_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_heat_setting_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_fabric_finish,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_washing_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_other_cost_conv,2); ?></p></td>

				<td width="100" align="right"><p><? echo number_format($pro_trim_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_embell_cost,2); ?></p></td>

				<td width="80" align="right"><p><? echo number_format($pro_print_cost,2); ?></p></td>
				<td width="85" align="right"><p><? echo number_format($pro_embroidery_cost,2); ?></p></td>
				<td width="80" align="right"><p><? echo number_format($pro_special_cost,2); ?></p></td>

				<td width="100" align="right"><p><? echo number_format($pro_other_direct_expenses,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_contribution_value,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_cm_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_gross_profit,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_commercial_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_operating_expense,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_operating_profit,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_depreciation_amortization,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_interest_expense,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($pro_income_tax,2); ?></p></td>
				<td align="right"><p><? echo number_format($pro_net_profit,2); ?></p></td>
			</tr>
			<?
				$per_order_value=($pro_order_value/$order_value)*100;
				if(is_infinite($per_order_value) || is_nan($per_order_value)){$per_order_value=0;}
				$per_commission_cost=($pro_commission_cost/$commission_cost)*100;
				if(is_infinite($per_commission_cost) || is_nan($per_commission_cost)){$per_commission_cost=0;}
				$per_net_fob_value=($pro_net_fob_value/$net_fob_value)*100;
				if(is_infinite($per_net_fob_value) || is_nan($per_net_fob_value)){$per_net_fob_value=0;}
				$per_cost_of_material_service=($pro_cost_of_material_service/$cost_of_material_service)*100;
				if(is_infinite($per_cost_of_material_service) || is_nan($per_cost_of_material_service)){$per_cost_of_material_service=0;}
				$per_yarn_costing=($pro_yarn_costing/$yarn_costing)*100;
				if(is_infinite($per_yarn_costing) || is_nan($per_yarn_costing)){$per_yarn_costing=0;}
				$per_conversion_cost=($pro_conversion_cost/$tot_conversion_cost)*100;
				if(is_infinite($per_conversion_cost) || is_nan($per_conversion_cost)){$per_conversion_cost=0;}
				$per_trim_cost=($pro_trim_cost/$tot_trim_cost)*100;
				if(is_infinite($per_trim_cost) || is_nan($per_trim_cost)){$per_trim_cost=0;}
				$per_embell_cost=($pro_embell_cost/$tot_embell_cost)*100;
				if(is_infinite($per_embell_cost) || is_nan($per_embell_cost)){$per_embell_cost=0;}
				$per_other_direct_expenses=($pro_other_direct_expenses/$other_direct_expenses)*100;
				if(is_infinite($per_other_direct_expenses) || is_nan($per_other_direct_expenses)){$per_other_direct_expenses=0;}
				$per_contribution_value=($pro_contribution_value/$contribution_value)*100;
				if(is_infinite($per_contribution_value) || is_nan($per_contribution_value)){$per_contribution_value=0;}
				$per_cm_cost=($pro_cm_cost/$cm_cost)*100;
				if(is_infinite($per_cm_cost) || is_nan($per_cm_cost)){$per_cm_cost=0;}
				$per_gross_profit=($pro_gross_profit/$gross_profit)*100;
				if(is_infinite($per_gross_profit) || is_nan($per_gross_profit)){$per_gross_profit=0;}
				$per_commercial_cost=($pro_commercial_cost/$tot_commercial_cost)*100;
				if(is_infinite($per_commercial_cost) || is_nan($per_commercial_cost)){$per_commercial_cost=0;}
				$per_operating_expense=($pro_operating_expense/$operating_expense)*100;
				if(is_infinite($per_operating_expense) || is_nan($per_operating_expense)){$per_operating_expense=0;}
				$per_operating_profit=($pro_operating_profit/$operating_profit)*100;
				if(is_infinite($per_operating_profit) || is_nan($per_operating_profit)){$per_operating_profit=0;}
				$per_depreciation_amortization=($pro_depreciation_amortization/$depreciation_amortization)*100;
				if(is_infinite($per_depreciation_amortization) || is_nan($per_depreciation_amortization)){$per_depreciation_amortization=0;}
				$per_interest_expense=($pro_interest_expense/$interest_expense)*100;
				if(is_infinite($per_interest_expense) || is_nan($per_interest_expense)){$per_interest_expense=0;}
				$per_income_tax=($pro_income_tax/$income_tax)*100;
				if(is_infinite($per_income_tax) || is_nan($per_income_tax)){$per_income_tax=0;}
				$per_net_profit=($pro_net_profit/$net_profit)*100;
				if(is_infinite($per_net_profit) || is_nan($per_net_profit)){$per_net_profit=0;}
				$per_knit_cost=($pro_knit_cost/$tot_knit_cost)*100;
				if(is_infinite($per_knit_cost) || is_nan($per_knit_cost)){$per_knit_cost=0;}
				$per_fabric_dyeing_cost=($pro_fabric_dyeing_cost/$fabric_dyeing_cost)*100;
				if(is_infinite($per_fabric_dyeing_cost) || is_nan($per_fabric_dyeing_cost)){$per_fabric_dyeing_cost=0;}
				$per_yarn_dyed_cost=($pro_yarn_dyed_cost/$yarn_dyed_cost)*100;
				if(is_infinite($per_yarn_dyed_cost) || is_nan($per_yarn_dyed_cost)){$per_yarn_dyed_cost=0;}
				$per_all_over_cost=($pro_all_over_cost/$all_over_cost)*100;
				if(is_infinite($per_all_over_cost) || is_nan($per_all_over_cost)){$per_all_over_cost=0;}
				$per_heat_setting_cost=($pro_heat_setting_cost/$heat_setting_cost)*100;
				if(is_infinite($per_heat_setting_cost) || is_nan($per_heat_setting_cost)){$per_heat_setting_cost=0;}
				$per_fabric_finish=($pro_fabric_finish/$fabric_finish)*100;
				if(is_infinite($per_fabric_finish) || is_nan($per_fabric_finish)){$per_fabric_finish=0;}
				$per_washing_cost=($pro_washing_cost/$washing_cost)*100;
				if(is_infinite($per_washing_cost) || is_nan($per_washing_cost)){$per_washing_cost=0;}
				$per_other_cost_conv=($pro_other_cost_conv/$other_cost_conv)*100;
				if(is_infinite($per_other_cost_conv) || is_nan($per_other_cost_conv)){$per_other_cost_conv=0;}

				$per_fab_purchase_cost=($pro_fab_purchase_cost/$fab_purchase)*100;
				if(is_infinite($per_fab_purchase_cost) || is_nan($per_fab_purchase_cost)){$per_fab_purchase_cost=0;}
			?>
			 <tr>
				<td width="90"><p><strong>Variance %</strong></p></td>
				<td width="100" align="right"><p><? echo number_format($per_order_value,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_commission_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_net_fob_value,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_cost_of_material_service,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_yarn_costing,2); ?></p></td>
                <td width="80" align="right"><p><?  echo number_format($per_fab_purchase_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_conversion_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_knit_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_fabric_dyeing_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_yarn_dyed_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_all_over_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_heat_setting_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_fabric_finish,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_washing_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_other_cost_conv,2); ?></p></td>

				<td width="100" align="right"><p><? echo number_format($per_trim_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_embell_cost,2); ?></p></td>
				<td width="80" align="right"><p><? echo number_format($per_embell_cost,2); ?></p></td>
				<td width="85" align="right"><p><? echo number_format($per_embell_cost,2); ?></p></td>
				<td width="80" align="right"><p><? echo number_format($per_embell_cost,2); ?></p></td>

				<td width="100" align="right"><p><? echo number_format($per_other_direct_expenses,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_contribution_value,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_cm_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_gross_profit,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_commercial_cost,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_operating_expense,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_operating_profit,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_depreciation_amortization,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_interest_expense,2); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($per_income_tax,2); ?></p></td>
				<td align="right"><p><? echo number_format($per_net_profit,2); ?></p></td>
			</tr>
			<?
			//Total Variance
			$total_pro_order_value+=$pro_order_value; $total_pro_commission_cost+=$pro_commission_cost;
			$total_pro_net_fob_value+=$pro_net_fob_value;
			$total_pro_cost_of_material_service+=$pro_cost_of_material_service;
			$total_pro_yarn_costing+=$pro_yarn_costing;
			$total_pro_conversion_cost+=$pro_conversion_cost;
			$total_pro_fab_purchase_cost+=$pro_fab_purchase_cost;

			$total_pro_knit_cost+=$pro_knit_cost;
			$total_pro_fabric_dyeing_cost+=$pro_fabric_dyeing_cost;
			$total_pro_yarn_dyed_cost+=$pro_yarn_dyed_cost;
			$total_pro_heat_setting_cost+=$pro_heat_setting_cost;
			$total_pro_fabric_finish+=$pro_fabric_finish;
			$total_pro_washing_cost+=$pro_washing_cost;
			$total_pro_all_over_cost+=$pro_all_over_cost;
			$total_pro_other_cost_conv+=$pro_other_cost_conv;

			$total_pro_trim_cost+=$pro_trim_cost;
			$total_pro_embell_cost+=$pro_embell_cost;

			$total_pro_print_cost+=$pro_print_cost;
			$total_pro_embroidery_cost+=$pro_embroidery_cost;
			$total_pro_special_cost+=$pro_special_cost;


			$total_pro_other_direct_expenses+=$pro_other_direct_expenses;
			$total_pro_contribution_value+=$pro_contribution_value;
			$total_pro_cm_cost+=$pro_cm_cost;
			$total_pro_gross_profit+=$pro_gross_profit;
			$total_pro_commercial_cost+=$pro_commercial_cost;
			$total_pro_operating_expense+=$pro_operating_expense;
			$total_pro_operating_profit+=$pro_operating_profit;
			$total_pro_depreciation_amortization+=$pro_depreciation_amortization;
			$total_pro_interest_expense+=$pro_interest_expense;
			$total_pro_income_tax+=$pro_income_tax;
			$total_pro_net_profit+=$pro_net_profit;
			//Total Variance %
			$total_per_order_value+=$per_order_value; $total_pro_commission_cost+=$per_commission_cost;
			$total_per_net_fob_value+=$per_net_fob_value;
			$total_per_cost_of_material_service+=$per_cost_of_material_service;
			$total_per_yarn_costing+=$per_yarn_costing;
			$total_per_fab_purchase_costing+=$per_yarn_costing;
			$total_per_conversion_cost+=$per_conversion_cost;
			$total_per_trim_cost+=$per_trim_cost;
			$total_per_embell_cost+=$per_embell_cost;
			$total_per_other_direct_expenses+=$per_other_direct_expenses;
			$total_per_contribution_value+=$per_contribution_value;
			$total_per_cm_cost+=$per_cm_cost;
			$total_per_gross_profit+=$per_gross_profit;
			$total_per_tot_commercial_cost+=$per_tot_commercial_cost;
			$total_per_operating_expense+=$per_operating_expense;
			$total_per_operating_profit+=$per_operating_profit;
			$total_per_depreciation_amortization+=$per_depreciation_amortization;
			$total_per_interest_expense+=$per_interest_expense;
			$total_per_income_tax+=$per_income_tax;
			$total_per_net_profit+=$per_net_profit;

			$i++;
		}
		//echo $tot_order_value;
		?>
        </table>
        </div>
        <table class="tbl_bottom" width="3985" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
        	<tr>
                <td width="35"></td>
                <td width="120"></td>
                <td width="100"></td>
                <td width="110"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="80"></td>
                <td width="80"></td>
                <td width="100"></td>
                <td width="110">Total</td>
                <td width="100"><? echo number_format($total_order_value,2);?></td>
                <td width="100"><? echo number_format($total_commission_cost,2);?></td>
                <td width="100"><? echo number_format($total_net_fob_value,2);?></td>
                <td width="100"><? echo number_format($total_cost_of_material_service,2);?></td>
                <td width="100"><? echo number_format($total_yarn_costing,2);?></td>
                <td width="80"><? echo number_format($total_purchase_costing,2);?></td>
                <td width="100"><? echo number_format($total_tot_conversion_cost,2);?></td>
                <td width="100"><? echo number_format($total_tot_knit_cost,2);?></td>
                <td width="100"><? echo number_format($total_fabric_dyeing_cost,2);?></td>
                <td width="100"><? echo number_format($total_yarn_dyed_cost,2);?></td>
                <td width="100"><? echo number_format($total_all_over_cost,2);?></td>
                <td width="100"><? echo number_format($total_heat_setting_cost,2);?></td>
                <td width="100"><? echo number_format($total_fabric_finish,2);?></td>
                <td width="100"><? echo number_format($total_washing_cost,2);?></td>
                <td width="100"><? echo number_format($total_other_cost_conv,2);?></td>

                <td width="100"><? echo number_format($total_tot_trim_cost,2);?></td>
                <td width="100"><? echo number_format($total_tot_embell_cost,2);?></td>

                <td width="80"><? echo number_format($total_print_cost,2);?></td>
                <td width="85"><? echo number_format($total_embroidery_cost,2);?></td>
                <td width="80"><? echo number_format($total_special_cost,2);?></td>

                <td width="100"><? echo number_format($total_other_direct_expenses,2);?></td>
                <td width="100"><? echo number_format($total_contribution_value,2);?></td>
                <td width="100"><? echo number_format($total_cm_cost,2);?></td>
                <td width="100"><? echo number_format($total_gross_profit,2);?></td>
                <td width="100"><? echo number_format($total_tot_commercial_cost,2);?></td>
                <td width="100"><? echo number_format($total_operating_expense,2);?></td>
                <td width="100"><? echo number_format($total_operating_profit,2);?></td>
                <td width="100"><? echo number_format($total_depreciation_amortization,2);?></td>
                <td width="100"><? echo number_format($total_interest_expense,2);?></td>
                <td width="100"><? echo number_format($total_income_tax,2);?></td>
                <td><? echo number_format($total_net_profit,2);?></td>
            </tr>
            <tr>
                <td width="35"></td>
                <td width="120"></td>
                <td width="100"></td>
                <td width="110"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="80"></td>
                <td width="80"></td>
                <td width="100"></td>
                <td width="110">Actual Total</td>
                <td width="100"><? echo number_format($total_actual_shipout_val,2);?></td>
                <td width="100"><? echo number_format($total_actual_commission_cost,2);?></td>
                <td width="100"><? echo number_format($total_actual_net_fob_value,2);?></td>
                <td width="100"><? echo number_format($total_actual_cost_of_material_service,2);?></td>
                <td width="100"><? echo number_format($total_actual_yarn_costing,2);?></td>
                <td width="80"><? echo number_format($total_actual_fab_purchase,2);?></td>
                <td width="100"><? echo number_format($total_actual_conversion_cost,2);?></td>

                <td width="100"><? echo number_format($total_actual_tot_knit_cost,2);?></td>
                <td width="100"><? echo number_format($total_actual_fabric_dyeing_cost,2);?></td>
                <td width="100"><? echo number_format($total_actual_yarn_dyed_cost,2);?></td>
                <td width="100"><? echo number_format($total_actual_all_over_cost,2);?></td>
                <td width="100"><? echo number_format($total_actual_heat_setting_cost,2);?></td>
                <td width="100"><? echo number_format($total_actual_fabric_finish,2);?></td>
                <td width="100"><? echo number_format($total_actual_washing_cost,2);?></td>
                <td width="100"><? echo number_format($total_actual_other_cost_conv,2);?></td>

                <td width="100"><? echo number_format($total_actual_trim_cost,2);?></td>
                <td width="100"><? echo number_format($total_actual_embell_cost,2);?></td>

                <td width="80"><? echo number_format($total_actual_print_cost,2);?></td>
                <td width="85"><? echo number_format($total_actual_embroidery_cost,2);?></td>
                <td width="80"><? echo number_format($total_actual_special_cost,2);?></td>

                <td width="100"><? echo number_format($total_actual_other_direct_expenses,2);?></td>
                <td width="100"><? echo number_format($total_actual_contribution_value,2);?></td>
                <td width="100"><? echo number_format($total_actual_cm_cost,2);?></td>
                <td width="100"><? echo number_format($total_actual_gross_profit,2);?></td>
                <td width="100"><? echo number_format($total_actual_tot_commercial_cost,2);?></td>
                <td width="100"><? echo number_format($total_actual_operating_expense,2);?></td>
                <td width="100"><? echo number_format($total_actual_operating_profit,2);?></td>
                <td width="100"><? echo number_format($total_actual_depreciation_amortization,2);?></td>
                <td width="100"><? echo number_format($total_actual_interest_expense,2);?></td>
                <td width="100"><? echo number_format($total_actual_income_tax,2);?></td>
                <td><? echo number_format($total_actual_net_profit,2);?></td>
            </tr>
            <tr>
                <td width="35"></td>
                <td width="120"></td>
                <td width="100"></td>
                <td width="110"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="80"></td>
                <td width="80"></td>
                <td width="100"></td>
                <td width="110">Variance Total</td>
                <td width="100"><? echo number_format($total_pro_order_value,2);?></td>
                <td width="100"><? echo number_format($total_pro_commission_cost,2);?></td>
                <td width="100"><? echo number_format($total_pro_net_fob_value,2);?></td>
                <td width="100"><? echo number_format($total_pro_cost_of_material_service,2);?></td>
                <td width="100"><? echo number_format($total_pro_yarn_costing,2);?></td>
                <td width="80"><? echo number_format($total_pro_fab_purchase_cost,2);?></td>
                <td width="100"><? echo number_format($total_pro_conversion_cost,2);?></td>
                <td width="100"><? echo number_format($total_pro_knit_cost,2);?></td>
                <td width="100"><? echo number_format($total_pro_fabric_dyeing_cost,2);?></td>
                <td width="100"><? echo number_format($total_pro_yarn_dyed_cost,2);?></td>
                <td width="100"><? echo number_format($total_pro_all_over_cost,2);?></td>
                <td width="100"><? echo number_format($total_pro_heat_setting_cost,2);?></td>
                <td width="100"><? echo number_format($total_pro_fabric_finish,2);?></td>
                <td width="100"><? echo number_format($total_pro_washing_cost,2);?></td>
                <td width="100"><? echo number_format($total_pro_other_cost_conv,2);?></td>

                <td width="100"><? echo number_format($total_pro_trim_cost,2);?></td>
                <td width="100"><? echo number_format($total_pro_embell_cost,2);?></td>

                <td width="80"><? echo number_format($total_pro_print_cost,2);?></td>
                <td width="85"><? echo number_format($total_pro_embroidery_cost,2);?></td>
                <td width="80"><? echo number_format($total_pro_special_cost,2);?></td>

                <td width="100"><? echo number_format($total_pro_other_direct_expenses,2);?></td>
                <td width="100"><? echo number_format($total_pro_contribution_value,2);?></td>
                <td width="100"><? echo number_format($total_pro_cm_cost,2);?></td>
                <td width="100"><? echo number_format($total_pro_gross_profit,2);?></td>
                <td width="100"><? echo number_format($total_pro_commercial_cost,2);?></td>
                <td width="100"><? echo number_format($total_pro_operating_expense,2);?></td>
                <td width="100"><? echo number_format($total_pro_operating_profit,2);?></td>
                <td width="100"><? echo number_format($total_pro_depreciation_amortization,2);?></td>
                <td width="100"><? echo number_format($total_pro_interest_expense,2);?></td>
                <td width="100"><? echo number_format($total_pro_income_tax,2);?></td>
                <td><? echo number_format($total_pro_net_profit,2);?></td>
            </tr>
            <tr>
                <td width="35"></td>
                <td width="120"></td>
                <td width="100"></td>
                <td width="110"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="80"></td>
                <td width="80"></td>
                <td width="100"></td>
                <td width="110">Variance %</td>
                <td width="100"><?
					$cv=($total_pro_order_value/$total_order_value)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_order_value/$total_order_value)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_commission_cost/$total_commission_cost)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_commission_cost/$total_commission_cost)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_net_fob_value/$total_net_fob_value)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_net_fob_value/$total_net_fob_value)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_cost_of_material_service/$total_cost_of_material_service)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_cost_of_material_service/$total_cost_of_material_service)*100,2); ?></td>
                <td width="100"><?
					$cv=($total_pro_yarn_costing/$total_yarn_costing)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_yarn_costing/$total_yarn_costing)*100,2); ?></td>
                 <td width="80"><?
					$cv=($total_pro_fab_purchase_cost/$total_purchase_costing)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_fab_purchase_cost/$total_purchase_costing)*100,2); ?></td>
                <td width="100"><?
					$cv=($total_pro_tot_conversion_cost/$total_tot_conversion_cost)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_tot_conversion_cost/$total_tot_conversion_cost)*100,2);?></td>

                <td width="100"><?
					$cv=($total_pro_knit_cost/$total_tot_knit_cost)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_knit_cost/$total_tot_knit_cost)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_fabric_dyeing_cost/$total_fabric_dyeing_cost)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_fabric_dyeing_cost/$total_fabric_dyeing_cost)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_yarn_dyed_cost/$total_yarn_dyed_cost)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_yarn_dyed_cost/$total_yarn_dyed_cost)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_all_over_cost/$total_all_over_cost)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_all_over_cost/$total_all_over_cost)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_heat_setting_cost/$total_heat_setting_cost)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_heat_setting_cost/$total_heat_setting_cost)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_fabric_finish/$total_fabric_finish)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_fabric_finish/$total_fabric_finish)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_washing_cost/$total_washing_cost)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_washing_cost/$total_washing_cost)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_other_cost_conv/$total_other_cost_conv)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_other_cost_conv/$total_other_cost_conv)*100,2);?></td>

                <td width="100"><?
					$cv=($total_pro_tot_trim_cost/$total_tot_trim_cost)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_tot_trim_cost/$total_tot_trim_cost)*100,2); ?></td>
                <td width="100"><?
					$cv=($total_pro_tot_embell_cost/$total_tot_embell_cost)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_tot_embell_cost/$total_tot_embell_cost)*100,2); ?></td>

                <td width="80"><?
					$cv=($total_pro_print_cost/$total_print_cost)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_print_cost/$total_print_cost)*100,2); ?></td>
                <td width="85"><?
					$cv=($total_pro_embroidery_cost/$total_embroidery_cost)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_embroidery_cost/$total_embroidery_cost)*100,2); ?></td>
                <td width="80"><?
					$cv=($total_pro_special_cost/$total_special_cost)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_special_cost/$total_special_cost)*100,2); ?></td>

                <td width="100"><?
					$cv=($total_pro_other_direct_expenses/$total_other_direct_expenses)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_other_direct_expenses/$total_other_direct_expenses)*100,2); ?></td>
                <td width="100"><?
					$cv=($total_pro_contribution_value/$total_contribution_value)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_contribution_value/$total_contribution_value)*100,2); ?></td>
                <td width="100"><?
					$cv=($total_pro_cm_cost/$total_cm_cost)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_cm_cost/$total_cm_cost)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_gross_profit/$total_gross_profit)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_gross_profit/$total_gross_profit)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_commercial_cost/$total_tot_commercial_cost)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_commercial_cost/$total_tot_commercial_cost)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_operating_expense/$total_operating_expense)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_operating_expense/$total_operating_expense)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_operating_profit/$total_operating_profit)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_operating_profit/$total_operating_profit)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_depreciation_amortization/$total_depreciation_amortization)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_depreciation_amortization/$total_depreciation_amortization)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_interest_expense/$total_interest_expense)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_interest_expense/$total_interest_expense)*100,2);?></td>
                <td width="100"><?
					$cv=($total_pro_income_tax/$total_income_tax)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_income_tax/$total_income_tax)*100,2);?></td>
                <td><?
					$cv=($total_pro_net_profit/$total_net_profit)*100;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($vc,2);
				//echo number_format(($total_pro_net_profit/$total_net_profit)*100,2);?></td>
            </tr>
        </table>
    </div>
    <?
	$html = ob_get_contents();
    ob_clean();
	 foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
	$name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$report_type";

	//echo "$total_data****$filename****$tot_rows";
	exit();
}

//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
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

		$exfac_sql=("select b.challan_no,a.sys_number,b.ex_factory_date,
		CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
		CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($id) ");
                $sql_dtls=sql_select($exfac_sql);

                foreach($sql_dtls as $row_real)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td>
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("sys_number")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                         <td width="" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                    </tr>
                    <?
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
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
?>