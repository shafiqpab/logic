<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class3/class.conditions.php');
require_once('../../../../includes/class3/class.reports.php');
require_once('../../../../includes/class3/class.yarns.php');
require_once('../../../../includes/class3/class.fabrics.php');


$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_name=$_SESSION['logic_erp']['user_id'];


if($action=="print_button_variable_setting")
{
	
	//echo " select format_id from  lib_report_template where template_name ='".$data."' and module_id=11 and report_id=19 and is_deleted=0 and status_active=1";
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=11 and report_id=19 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	//echo "fn_report_generated1();\n";
	
	exit();	
}
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
$team_member_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
$yarn_color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name");




if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//echo 'aziz';
	$company_id=str_replace("'","",$cbo_company_id);
	$report_type=str_replace("'","",$type);
	$report_title=str_replace("'","",$report_title);
	$report_button=30;
	
	$date_cond='';$country_date_cond='';$ex_factory_date_cond='';$not_ex_factory_date_cond='';
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
			$country_date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";
			$ex_factory_date_cond=" and d.ex_factory_date between '$start_date' and '$end_date'";
			$not_ex_factory_date_cond=" and d.ex_factory_date not between '$start_date' and '$end_date'";
			$not_country_date_cond=" and c.country_ship_date not between '$start_date' and '$end_date'";
			
		}
		//echo $date_cond;
	if($report_type==1)
	{	
	ob_start();

	//display:none
	?>
    <div style="width:2685px; " align="left">
   <!-- <h3 align="left" id="accordion_h2" style="width:2340px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>-->
    <fieldset style="width:100%;" id="content_search_panel2">	
        <table width="2680">
            <tr class="form_caption">
            <td colspan="32" align="center"><strong><? echo $report_title;?></strong></td>
            </tr>
            <tr class="form_caption">
            <td colspan="32" align="center"><strong><? echo $company_library[$company_id]; ?></strong></td>
            </tr>
             <tr class="form_caption">
            <td colspan="32" align="center"><strong><? echo change_date_format($start_date).' To '.change_date_format($end_date); ?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" width="2680" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <tr>
            <th width="30">SL</th>
            <th width="80">Job No</th>
            <th width="80">Po No</th>
            <th width="80">Projected by</th>
            <th width="80">Projection date</th>
            <th width="120">Customer</th>
            <th width="120">SKU/ Item name</th> 
            <th width="100">Program/Dept.</th>
            <th width="120">Style Name/Style No</th>
            <th width="60">SMV</th>
            <th width="80">Season</th>
            <th width="80">OPD (DD/MM/YY)</th>
            <th width="80">TOD (DD/MM/YY)</th>
            <th width="100">Body Part</th>
            <th width="100">Fabrication</th>
            <th width="80">Color</th>
            <th width="100">Order Status/ Order No</th>
            <th width="60">Fabric Weight (GSM)</th>
            <th width="150">Yarn Composition</th>
            <th width="70">Yarn Count</th>
            <th width="80">Yarn Color</th>
            <th width="70">Yarn Type</th>
            <th width="80">Garments Qty (Pcs)</th>
            <th width="80">Plan Cut Qty (Pcs)</th>
            <th width="70">Unit Price</th>
            <th width="80">Eqv.Basic qty. (pcs)</th>
            <th width="70">Consumption Per Pcs</th>
            
            <th width="80">Required Yarn(kg)</th>
            <th width="80">Budgeted  Yarn Price App.($)</th>
            <th width="80">Value</th>
            <th width="70">FC-Month</th>
            <th width="">Target Month</th>
            
            </tr>
        </thead>
        </table>
        <div style="width:2700; max-height:400px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="2680" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
       <?
	    $year_date=date('Y');
	    $basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst where year='".$year_date."' ",'comapny_id','basic_smv');
	   $price_quote_arr=array();$color_po_data_arr=array();
	   $sql_quot=sql_select("select  id,op_date,color_range  from wo_price_quotation where status_active=1 and is_deleted=0  ");		foreach($sql_quot as $row)
		{
			$price_quote_arr[$row[csf('id')]]['op_date']=$row[csf('op_date')];
			$price_quote_arr[$row[csf('id')]]['color_range']=$row[csf('color_range')];
		}
		$sql_color=sql_select("select  po_break_down_id,item_number_id,order_quantity,plan_cut_qnty  from wo_po_color_size_breakdown where status_active=1 and is_deleted=0  ");
		foreach($sql_color as $row)
		{
			$color_po_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['order_quantity']+=$row[csf('order_quantity')];
			$color_po_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];
			
		}

$sql_export="select c.id as fabric_dtls_id,a.job_no,a.company_name,a.product_dept,a.set_smv,  a.buyer_name, a.style_ref_no,a.season, a.dealing_marchant, a.total_set_qnty as ratio,a.quotation_id,  b.is_confirmed, b.plan_cut, b.id as po_id, b.po_number, b.shipment_date, sum(b.po_quantity) as po_quantity,sum(e.plan_cut_qnty) as plan_cut_qnty,sum(e.order_quantity) as order_quantity, b.unit_price,c.body_part_id,c.fabric_description,c.gsm_weight,d.type_id,d.count_id,d.copm_one_id,d.percent_one,d.color as yarn_color,d.rate as yarn_rate,e.item_number_id as gmts_item_id
			from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cost_fab_yarn_cost_dtls d,wo_po_color_size_breakdown e where a.job_no=b.job_no_mst  and a.job_no=c.job_no and b.job_no_mst=c.job_no and b.job_no_mst=d.job_no  and c.job_no=d.job_no and a.job_no=d.job_no and c.id=d.fabric_cost_dtls_id  and b.id=e.po_break_down_id and a.job_no=e.job_no_mst and e.item_number_id=c.item_number_id and a.company_name='$company_id'  $date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by c.id, a.job_no,b.id ,b.po_number,a.company_name,a.product_dept,a.set_smv, a.buyer_name, a.style_ref_no,a.season,a.set_smv, a.dealing_marchant, e.item_number_id, 
a.total_set_qnty,a.quotation_id, b.is_confirmed, b.plan_cut,b.shipment_date, b.unit_price,c.body_part_id,
c.fabric_description,c.gsm_weight,d.type_id,d.count_id,d.copm_one_id,d.percent_one,d.color,d.rate order by b.id,a.job_no,a.dealing_marchant,c.body_part_id,d.copm_one_id,d.count_id";
			
		$result=sql_select($sql_export);
		$i=1;$total_yarn_req_qty=0;
		
		$condition= new condition();
		$condition->company_name("=$company_id");
		if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
		 {
			 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			
		 }
		$condition->init();
		$yarn= new yarn($condition);
		//echo $yarn->getQuery(); die;
		$yarn_req_qty_arr=$yarn->get_order_and_gmtsItem_fabricId_Count_Composition_AndType_wise_QtyArray();
		//$yarn_req_qty_arr=$yarn->getOrderCountCompositionColorAndTypeWiseYarnQtyAndAmountArray();
		//$po_yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
		//print_r($yarn_req_qty_arr);die;
		foreach($result as $row )
		{
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
			
		//$order_qty_pcs=$row[csf('order_quantity')]*$row[csf('ratio')];
		$order_qty_pcs=$color_po_data_arr[$row[csf('po_id')]][$row[csf('gmts_item_id')]]['order_quantity'];
		$plan_cut_qty=$color_po_data_arr[$row[csf('po_id')]][$row[csf('gmts_item_id')]]['plan_cut_qnty'];
		//$plan_cut_qty=$color_po_data_arr[$row[csf('po_id')]][$row[csf('gmts_item_id')]]['plan_cut_qnty'];
		
		$price_quot_color_range=$price_quote_arr[$row[csf('quotation_id')]]['color_range'];
		$price_quot_op_date=$price_quote_arr[$row[csf('quotation_id')]]['op_date'];
		$basic_qnty_pcs= (($row[csf('set_smv')])*$order_qty_pcs)/$basic_smv_arr[$row[csf("company_name")]];
		$yarn_composition=$composition[$row[csf('copm_one_id')]].','.$row[csf('percent_one')].'%';
		$yarn_count=$yarn_count_library[$row[csf('count_id')]];
		$yarn_count_type=$yarn_type[$row[csf('type_id')]];
		$body_part_id=$body_part[$row[csf('body_part_id')]];
		$yarn_req=$yarn_req_qty_arr[$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('fabric_dtls_id')]][$row[csf('count_id')]][$row[csf('copm_one_id')]][$row[csf('type_id')]];
		//$po_yarn_req_qty=$po_yarn_req_qty_arr[$row[csf('po_id')]];
	   ?>
        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
        <td width="30"><? echo $i; ?></td>
        <td width="80"><p><? echo $row[csf('job_no')]; ?></p></td>
        <td width="80"><p>&nbsp;<? echo trim($row[csf('po_number')]); ?></p></td>
        <td width="80" title="<? echo $row[csf('job_no')];?>"><p><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></p></td>
        <td width="80"><p><? echo date('d-m-Y'); ?></p></td>
        <td width="120"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
        <td width="120"><p><? 
		
		echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
        <td width="100"><p><? echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
        <td width="120"><p>&nbsp;<? echo $row[csf('style_ref_no')]; ?></p></td>
        
        <td width="60" align="right"><p><? echo $row[csf('set_smv')]; ?></p></td>
        <td width="80"><p><? echo $row[csf('season')]; ?></p></td>
        <td width="80"><p><? echo change_date_format($price_quot_op_date); ?></p></td>
        <td width="80"><p><? echo change_date_format($row[csf('shipment_date')]); ?></p></td>
        <td width="100"><p><? echo $body_part_id; ?></p></td>
        
        <td width="100"><p><? echo $row[csf('fabric_description')]; ?></p></td>
        <td width="80"><p><? echo $color_range[$price_quot_color_range]; ?></p></td>
        <td width="100"><p><? echo $order_status[$row[csf('is_confirmed')]]; ?></p></td>
        <td width="60"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
        <td width="150"><p><? echo $yarn_composition; ?></p></td>
        <td width="70"><p><? echo $yarn_count; ?></p></td>
        <td width="80"><p><? echo $yarn_color_library[$row[csf('yarn_color')]]; ?></p></td>
        <td width="70"><p><? echo $yarn_count_type; ?></p></td>
        
        <td width="80" align="right"><p><? echo number_format($order_qty_pcs); ?></p></td>
         <td width="80" align="right"><p><? echo number_format($plan_cut_qty); ?></p></td>
        <td width="70" align="right"><p><? echo $row[csf('unit_price')]; ?></p></td>
        <td width="80" align="right" title="<? echo 'Set Smv'.'*'.'PO Qty'.'/'.'Capacity Calcution Basic smv';?>"><p><? echo number_format($basic_qnty_pcs); ?></p></td>
        <td width="70" align="right" title="<? echo 'PO Yarn Req Qty/Plan Cut Qty Pcs';?>"><p><? echo number_format($yarn_req/$plan_cut_qty,4); ?></p></td>
        
      	<td width="80" align="right"><p><? echo number_format($yarn_req,2); ?></p></td>
      	<td width="80" align="right"><p><? echo $row[csf('yarn_rate')]; ?></p></td>
      	<td width="80" align="right"><p><? echo number_format($yarn_req*$row[csf('yarn_rate')],2); ?></p></td>
        
        <td width="70"><p><? echo date('Y-m'); ?></p></td>
      	<td width=""><p><? echo date("Y-m",strtotime($row[csf('shipment_date')])); ?></p></td>
        </tr>
        <?
		$i++;
		$total_yarn_req_qty+=$yarn_req;
		}
		?>
        <tr>
        <td colspan="27" align="right">Total </td>
        <td align="right"><? echo number_format($total_yarn_req_qty,2);?> </td>
        </tr>
        </table>
        </div>

    </fieldset>
    </div>

    <?
	}
	else if($report_type==2) //Plan Vs Ex-F Button
	{	
	ob_start();
	 
//print_r($weekarr);
	?>
    <div style="width:1820px; " align="left">
   <!-- <h3 align="left" id="accordion_h2" style="width:2340px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>-->
    <fieldset style="width:100%;" id="content_search_panel2">	
        <table width="1820">
            <tr class="form_caption">
            <td colspan="20" align="center"><strong><? echo $report_title;?></strong></td>
            </tr>
            <tr class="form_caption">
            <td colspan="20" align="center"><strong><? echo $company_library[$company_id]; ?></strong></td>
            </tr>
             <tr class="form_caption">
            <td colspan="20" align="center"><strong><? echo change_date_format($start_date).' To '.change_date_format($end_date); ?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" width="1820" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <tr>
            <th width="30">SL</th>
            <th width="150">Unique ID</th>
            <th width="80">Buyer</th>
            <th width="80">PO Number</th>
            <th width="80">Job No</th>
            <th width="120">Shipping Status</th>
            <th width="120">Style Ref.</th> 
            <th width="100">Prod Dept.</th>
            <th width="120">Job Qty.</th>
            <th width="100">Item</th>
            <th width="80">Country</th>
            <th width="80">Plan Date(Ship Date)</th>
            <th width="80">Country Total</th>
            <th width="100">Ex-Factory Date</th>
            <th width="100">Ex-Factory Qty</th>
            <th width="80">Total Ex-Factory</th>
            <th width="100">OPD</th>
            <th width="60">Shipment Year</th>
            <th width="100">Shipment Month</th>
            <th width="">Shipment Week</th>
            </tr>
        </thead>
        </table>
        <div style="width:1840px; max-height:400px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="1820" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
       <?
	   	 $week_data_arr=array();
		  $sql_week=sql_select("select  c.week as weeks,c.week_date  from week_of_year c order by c.week_date");
		  foreach( $sql_week as $row)
		  {
			 $week_data_arr[$row[csf('week_date')]]['week']=$row[csf('weeks')]; 
		  }
	    $sql_color=sql_select("select  c.po_break_down_id as po_id,c.item_number_id,c.country_id,c.order_quantity,c.plan_cut_qnty  from wo_po_color_size_breakdown c where c.status_active=1 and c.is_deleted=0 $country_date_cond ");
		$color_po_data_arr=array();
		foreach($sql_color as $row)
		{
			$color_po_data_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['order_quantity']+=$row[csf('order_quantity')];
		}
		unset($sql_color);
		$sql_exf="select  b.po_break_down_id as po_id,b.item_number_id,b.country_id,b.ex_factory_date, 
		sum(CASE WHEN b.entry_form!=85  THEN b.ex_factory_qnty ELSE 0 END) as ex_qnty,
		sum(CASE WHEN b.entry_form=85  THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
		
		 from  pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.company_id=$company_id and b.status_active=1 and b.is_deleted=0 group by  b.po_break_down_id,b.item_number_id,b.country_id,b.ex_factory_date ";
		 $result_ex=sql_select($sql_exf);
		 $ex_factory_po_data_arr=array(); $ex_factory_po_date_arr=array();
		foreach($result_ex as $row)
		{
			
			$ex_factory_po_date_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('ex_factory_date')]]['qty']+=$row[csf('ex_qnty')]-$row[csf('return_qnty')];
			$ex_factory_po_data_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['total_exf_qnty']+=$row[csf('ex_qnty')]-$row[csf('return_qnty')];
			
		}
		if($db_type==0) $item_group="group_concat(distinct c.item_number_id) as item_number_id";
		else if($db_type==2) $item_group="LISTAGG(c.item_number_id, ',') WITHIN GROUP (ORDER BY c.item_number_id) as item_number_id";
		if($db_type==0) $efact_item="and d.ex_factory_qnty!=''";
		else if($db_type==2) $efact_item="and nvl(d.ex_factory_qnty,0)!=0";
		 $sql_plan="select b.id as po_id,a.job_no,a.company_name,$item_group,a.buyer_name,a.product_dept, a.style_ref_no,a.job_quantity,b.po_number,b.po_received_date,c.shiping_status, c.country_ship_date,c.country_id,sum(c.order_quantity) as order_quantity,d.ex_factory_date as ex_factory_date
					from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown  c 
					LEFT JOIN pro_ex_factory_mst d on  c.po_break_down_id=d.po_break_down_id and d.entry_form=0  and d.delivery_mst_id>0 and d.country_id=c.country_id  and  d.status_active=1 
					and d.is_deleted=0 $efact_item  
					where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=c.job_no_mst  and a.company_name='$company_id'  $country_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0   group by b.id, b.po_number,c.country_id,a.job_no,a.company_name,a.buyer_name,a.product_dept,d.ex_factory_date, a.style_ref_no,a.job_quantity,b.po_received_date,c.shiping_status, c.country_ship_date  order by b.id,c.country_id,a.job_no";
		/*echo "select a.job_no,a.company_name,group_concat(distinct c.item_number_id) as item_number_id,a.buyer_name,a.product_dept, a.style_ref_no,a.job_quantity,b.id as po_id, b.po_number,b.po_received_date,c.shiping_status, c.country_ship_date,c.country_id,sum(c.order_quantity) as order_quantity
					from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown  c	where a.job_no=b.job_no_mst    and b.id=c.po_break_down_id and a.job_no=c.job_no_mst  and a.company_name='$company_id'  $country_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0   group by b.id, b.po_number,a.job_no,a.company_name,a.buyer_name,a.product_dept,a.style_ref_no,a.job_quantity,b.po_received_date,c.shiping_status, c.country_ship_date,c.country_id  order by b.id,a.job_no";*/
		$result_data=sql_select($sql_plan);
		$i=1;$total_country_qty=0;$total_cumu_ex_factory_qty=0;$total_ex_factory_qty_down=0;
		foreach($result_data as $row )
		{
			
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
		
		$week_key=$week_data_arr[$row[csf('country_ship_date')]]['week'];
		//$order_qty_pcs=$color_po_data_arr[$row[csf('po_id')]][$row[csf('gmts_item_id')]]['order_quantity'];
		$unique_id=$row[csf('buyer_name')].'-'.$row[csf('po_id')].'-'.$row[csf('country_id')];
		$ex_factory_date=$row[csf('ex_factory_date')];	
		
		$gmts_item=''; $gmts_item_id=array_unique(explode(",",$row[csf('item_number_id')]));
		$ex_factory_qty=0;$order_qty_pcs=0;$total_ex_factory_qty=0;
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
				$ex_factory_qty+=$ex_factory_po_date_arr[$row[csf('po_id')]][$item_id][$row[csf('country_id')]][$ex_factory_date]['qty'];
				$total_ex_factory_qty+=$ex_factory_po_data_arr[$row[csf('po_id')]][$item_id][$row[csf('country_id')]]['total_exf_qnty'];
				$order_qty_pcs+=$color_po_data_arr[$row[csf('po_id')]][$item_id][$row[csf('country_id')]]['order_quantity'];
	
		}
	
		/*if($ex_factory_qty==0)
		{
			$total_ex_factory_qty=0;
		}
		else
		{
			$total_ex_factory_qty=$ex_factory_po_data_arr[$row[csf('po_id')]][$row[csf('gmts_item_id')]][$row[csf('country_id')]]['total_exf_qnty'];
		}*/
		//$order_qty_pcs=$color_po_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['order_quantity']+=$row[csf('order_quantity')];
		
		
	   ?>
        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
        <td width="30"><? echo $i; ?></td>
        <td width="150"><p><? echo $unique_id; ?></p></td>
        <td width="80" align="left"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
        <td width="80" align="left"><p><? echo trim($row[csf('po_number')]); ?></p></td>
        <td width="80"><p><? echo $row[csf('job_no')] ?></p></td>
        <td width="120" align="left"><p><? echo $shipment_status[$row[csf('shiping_status')]]; ?></p></td>
        <td width="120" align="left"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
        <td width="100"><p><? echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
        <td width="120" align="right"><p><? echo $row[csf('job_quantity')]; ?></p></td>
        <td width="100" align="left"><p><? 
			echo $gmts_item; ?></p></td>
        <td width="80"><p><? echo $country_name_library[$row[csf('country_id')]]; ?></p></td>
        <td width="80" align="left"> <p> <? echo change_date_format($row[csf('country_ship_date')]); ?></p></td>
        <td width="80"  align="right"><p><? echo number_format($order_qty_pcs);//$row[csf('order_quantity')]; ?></p></td>
        <td width="100" align="left"><p><? echo change_date_format($ex_factory_date); ?></p></td>
       
        <td width="100"  align="right"><p><? echo number_format($ex_factory_qty,2); ?></p></td>
        <td width="80"  align="right"><p><? echo number_format($total_ex_factory_qty,2); ?></p></td>
        <td width="100"  align="left"><p><? echo change_date_format($row[csf('po_received_date')]); ?></p></td>
        <td width="60"><p><? echo date("Y",strtotime($row[csf('country_ship_date')])); ?></p></td>
        <td width="100"><p><? echo date("Y-m",strtotime($row[csf('country_ship_date')])); ?></p></td>
        <td width=""><p><? echo date("Y",strtotime($row[csf('country_ship_date')])).'-'.$week_key; ?></p></td>
       
        </tr>
        <?
		$i++;
		$total_country_qty+=$order_qty_pcs;
		$total_ex_factory_qty_down+=$ex_factory_qty;
		$total_cumu_ex_factory_qty+=$total_ex_factory_qty;
		}
		?>
        <tr>
        <td colspan="12" align="right">Total </td>
        <td align="right"><? echo number_format($total_country_qty,2);?> </td>
         <td align="right"></td> 
         <td align="right"><? echo number_format($total_ex_factory_qty_down,2);?> </td> 
         <td align="right"><? echo number_format($total_cumu_ex_factory_qty,2);?> </td>
         <td colspan="4"></td> 
        </tr>
        </table>
        </div>
    </fieldset>
    </div>
    <?
	}
	else if($report_type==3) // Ex-F Vs Plan  Button
	{	
	ob_start();
	
//print_r($weekarr);
	?>
    <div style="width:1640px; " align="left">
   <!-- <h3 align="left" id="accordion_h2" style="width:2340px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>-->
    <fieldset style="width:100%;" id="content_search_panel2">	
        <table width="1640">
            <tr class="form_caption">
            <td colspan="19" align="center"><strong><? echo $report_title;?></strong></td>
            </tr>
            <tr class="form_caption">
            <td colspan="19" align="center"><strong><? echo $company_library[$company_id]; ?></strong></td>
            </tr>
             <tr class="form_caption">
            <td colspan="19" align="center"><strong><? echo change_date_format($start_date).' To '.change_date_format($end_date); ?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" width="1640" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <tr>
            <th width="30">SL</th>
            <th width="150">Unique ID</th>
            <th width="80">Buyer Name</th>
            <th width="80">Order NO</th>
           
            <th width="120">Ex-F Date</th>
            <th width="120">Chllan</th> 
            <th width="120">Country</th>
            <th width="100">Qnty.</th>
            <th width="100">Total Ex-Factory</th>
            <th width="80">Year</th>
            <th width="70">Month</th>
            <th width="70">Week</th>
            <th width="100">Plan Date</th>
            <th width="100">Plan Qty</th>
            <th width="80">OPD</th>
            <th width="60">Plan Cycle Time</th>
            <th width="60">Actual Cycle Time</th>
            <th width="60">Cycle Time Delay</th>
            <th width="">Excess Shipment</th>
            </tr>
        </thead>
        </table>
        <div style="width:1660px; max-height:400px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="1640" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
       <?
	      $week_data_arr=array();
		  $sql_week=sql_select("select  c.week as weeks,c.week_date  from week_of_year c order by c.week_date");
		  foreach( $sql_week as $row)
		  {
			 $week_data_arr[$row[csf('week_date')]]['week']=$row[csf('weeks')]; 
		  }
		 $sql_color=sql_select("select  c.po_break_down_id as po_id,c.item_number_id,c.country_id,c.order_quantity,c.plan_cut_qnty  from wo_po_color_size_breakdown c where c.status_active=1 and c.is_deleted=0  ");
		$color_po_data_arr=array();
		foreach($sql_color as $row)
		{
			$color_po_data_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['order_quantity']+=$row[csf('order_quantity')];
		}
		unset($sql_color);
		
		$sql_exf="select  b.po_break_down_id as po_id,b.item_number_id,b.country_id,b.challan_no,b.ex_factory_date, 
		sum(CASE WHEN b.entry_form!=85  THEN b.ex_factory_qnty ELSE 0 END) as ex_qnty,
		sum(CASE WHEN b.entry_form=85  THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
		
		 from  pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.company_id=$company_id and b.status_active=1 and b.is_deleted=0 group by  b.po_break_down_id,b.challan_no,b.item_number_id,b.country_id,b.ex_factory_date ";
		 $result_ex=sql_select($sql_exf);
		 $ex_factory_po_data_arr=array(); $ex_factory_po_date_arr=array();
		foreach($result_ex as $row)
		{
			
			$ex_factory_po_date_arr[$row[csf('challan_no')]][$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('ex_factory_date')]]['qty']+=$row[csf('ex_qnty')]-$row[csf('return_qnty')];
			$ex_factory_po_data_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['total_exf_qnty']+=$row[csf('ex_qnty')]-$row[csf('return_qnty')];
			
		}
		if($db_type==0) $item_group="group_concat(distinct c.item_number_id) as item_number_id";
		else if($db_type==2) $item_group="LISTAGG(c.item_number_id, ',') WITHIN GROUP (ORDER BY c.item_number_id) as item_number_id";
		 $sql_result="select b.id as po_id,a.job_no,$item_group,a.company_name,a.buyer_name, b.po_number,b.po_received_date,c.country_ship_date,c.country_id,sum(c.order_quantity) as order_quantity,d.ex_factory_date,d.challan_no
					from wo_po_details_master a, wo_po_break_down b,
					wo_po_color_size_breakdown  c LEFT JOIN pro_ex_factory_mst d on  c.po_break_down_id=d.po_break_down_id   and d.delivery_mst_id>0 and d.country_id=c.country_id  and  d.status_active=1 and d.is_deleted=0 and d.ex_factory_qnty!=0 where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=c.job_no_mst and d.entry_form=0 and a.company_name='$company_id'  $ex_factory_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  group by b.id, b.po_number,d.ex_factory_date,d.challan_no,a.job_no,a.company_name,a.buyer_name,b.po_received_date, c.country_ship_date,c.country_id  order by b.id,a.job_no";
			
		$result_arr=sql_select($sql_result);
		$i=1;$total_country_qty=0;$total_cumu_ex_factory_qty=0;$total_ex_factory_qty_down=0;
		foreach($result_arr as $row )
		{
			
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
		//echo $week_key=$weekarr[$row[csf("country_ship_date")]];
		
		
		//$order_qty_pcs=$color_po_data_arr[$row[csf('po_id')]][$row[csf('gmts_item_id')]]['order_quantity'];
		$unique_id=$row[csf('buyer_name')].'-'.$row[csf('po_id')].'-'.$row[csf('country_id')];
		$ex_factory_date=$row[csf('ex_factory_date')];	
		 $week_key=$week_data_arr[$ex_factory_date]['week'];
		
		$gmts_item=''; $gmts_item_id=array_unique(explode(",",$row[csf('item_number_id')]));
		$ex_factory_qty=0;$order_qty_pcs=0;$total_ex_factory_qty=0;
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
				$ex_factory_qty+=$ex_factory_po_date_arr[$row[csf('challan_no')]][$row[csf('po_id')]][$item_id][$row[csf('country_id')]][$ex_factory_date]['qty'];
				$total_ex_factory_qty+=$ex_factory_po_data_arr[$row[csf('po_id')]][$item_id][$row[csf('country_id')]]['total_exf_qnty'];
				$order_qty_pcs+=$color_po_data_arr[$row[csf('po_id')]][$item_id][$row[csf('country_id')]]['order_quantity'];
		}
		
		 $plan_cycle_time=datediff('d',$row[csf('po_received_date')],$row[csf('country_ship_date')])-1;
		 $actual_cycle_time=datediff('d',$row[csf('po_received_date')],$ex_factory_date)-1;
		 $cycle_time_dely=$actual_cycle_time-$plan_cycle_time;
		 $excess_shipment_qty=$total_ex_factory_qty-$order_qty_pcs;
		 if($ex_factory_date!='0000-00-00')
		 {
			  $ex_factory_date=$ex_factory_date;
			  $year=date("Y",strtotime($ex_factory_date));
		 }
		 else 
		 {
			 $ex_factory_date='';
			  $year=date("Y",strtotime($ex_factory_date));
		 }
		$exf_day_mon=date("Y-m",strtotime($ex_factory_date));
	   ?>
        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
        <td width="30"><? echo $i; ?></td>
        <td width="150"><p><? echo $unique_id; ?></p></td>
        <td width="80" align="left"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
        <td width="80" align="left"><p><? echo trim($row[csf('po_number')]); ?></p></td>
       
        <td width="120" align="left"><p><? echo change_date_format($ex_factory_date); ?></p></td>
        <td width="120" align="left"><p><? echo $row[csf('challan_no')]; ?></p></td>
        <td width="120" align="center"><p><? echo $country_name_library[$row[csf('country_id')]]; ?></p></td>
        <td width="100" align="right"><p><? echo number_format($ex_factory_qty,2); ?></p></td>
        <td width="100" align="right"><p><? 
			echo number_format($total_ex_factory_qty,2); ?></p></td>
        <td width="80" align="center"><p><? if($year=='1970' || $year=='') echo ' '; else echo $year; ?></p></td>
        <td width="80" align="center"><p><? if($exf_day_mon=='1970-01' || $exf_day_mon=='') echo ' '; else echo $exf_day_mon;// echo date("Y-m",strtotime($ex_factory_date)); ?></p></td>
        <td width="80" align="center"><p><? echo $week_key; ?></p></td>
        <td width="100" align="left"><p><? echo change_date_format($row[csf('country_ship_date')]); ?></p></td>
       
        <td width="100" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
        <td width="80" align="left"><p><? echo change_date_format($row[csf('po_received_date')]); ?></p></td>
        <td width="60" align="center"><p><? echo $plan_cycle_time; ?></p></td>
        <td width="60" align="center"><p><? echo $actual_cycle_time; ?></p></td>
        <td width="60" align="center"><p><? echo $cycle_time_dely; ?></p></td>
        <td width=""  align="right"><p><? echo $excess_shipment_qty; ?></p></td>
       
        </tr>
        <?
		$i++;
		$total_country_qty+=$row[csf('order_quantity')];
		$total_ex_factory_qty_down+=$ex_factory_qty;
		$total_cumu_ex_factory_qty+=$total_ex_factory_qty;
		}
		?>
        <tr>
        <td colspan="7" align="right">Total </td>
        <td align="right"><? echo number_format($total_ex_factory_qty_down,2);?> </td>
         <td align="right"><? echo number_format($total_cumu_ex_factory_qty,2);?> </td>
         <td colspan="4"></td> 
         <td align="right"><? echo number_format($total_country_qty,2);?> </td>
         <td colspan="5"></td> 
        </tr>
        </table>
        </div>
    </fieldset>
    </div>
    <?
	}
	else if($report_type==4) //Both Plan Vs Ex-F Button
	{	
	ob_start();
	
	 $week_data_arr=array();
	  $sql_week=sql_select("select  c.week as weeks,c.week_date  from week_of_year c order by c.week_date");
	  foreach( $sql_week as $row)
	  {
		 $week_data_arr[$row[csf('week_date')]]['week']=$row[csf('weeks')]; 
	  }
	  
	?>
    <div style="width:2440px; " align="left">
   <!-- <h3 align="left" id="accordion_h2" style="width:2340px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>-->
    <fieldset style="width:100%;" id="content_search_panel2">	
        <table width="2440">
            <tr class="form_caption">
            <td colspan="28" align="center"><strong><? echo $report_title;?></strong></td>
            </tr>
            <tr class="form_caption">
            <td colspan="28" align="center"><strong><? echo $company_library[$company_id]; ?></strong></td>
            </tr>
             <tr class="form_caption">
            <td colspan="28" align="center"><strong><? echo change_date_format($start_date).' To '.change_date_format($end_date); ?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" width="2440" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <tr>
            <th width="30">SL</th>
            <th width="150">Unique ID</th>
            <th width="80">Buyer</th>
            <th width="80">PO Number</th>
            <th width="80">Job No</th>
            <th width="120">Shipping Status</th>
            <th width="120">Style Ref.</th> 
            <th width="100">Prod Dept.</th>
            <th width="120">Job Qty.</th>
            <th width="100">Item</th>
            <th width="80">Country</th>
            <th width="80">Plan Date(Ship Date)</th>
            <th width="80">Country Total</th>
            <th width="80">Challan</th>
            <th width="100">Ex-Factory Date</th>
            <th width="100">Ex-Factory Qty</th>
            <th width="80">Total Ex-Factory</th>
            <th width="100">OPD</th>
            <th width="70">Shipment Year</th>
            <th width="90">Shipment Month</th>
            <th width="70">Shipment Week</th>
            <th width="60">Ex-Factory Year</th>
            <th width="100">Ex-Factory Month</th>
            <th width="70">Ex-Factory Week</th>
            <th width="70">Plan Cycle Time</th>
            <th width="60">Actual Cycle Time</th>
            <th width="100">Cycle Time Delay</th>
            <th width="">Excess Shipment</th>
            
            </tr>
        </thead>
        </table>
        <div style="width:2460px; max-height:400px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="2440" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <tr>
       		<td colspan="28" align="left"><b>Within Ship Date</b> </td>
       </tr>
       <?
	    $sql_color=sql_select("select  c.po_break_down_id as po_id,c.item_number_id,c.country_id,c.order_quantity,c.plan_cut_qnty  from wo_po_color_size_breakdown c where c.status_active=1 and c.is_deleted=0 $country_date_cond ");
		$color_po_data_arr=array();
		foreach($sql_color as $row)
		{
			$color_po_data_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['order_quantity']+=$row[csf('order_quantity')];
		}
		unset($sql_color);
		$sql_exf="select  b.po_break_down_id as po_id,b.item_number_id,b.country_id,b.ex_factory_date, 
		sum(CASE WHEN b.entry_form!=85  THEN b.ex_factory_qnty ELSE 0 END) as ex_qnty,
		sum(CASE WHEN b.entry_form=85  THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
		
		 from  pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.company_id=$company_id and b.status_active=1 and b.is_deleted=0 group by  b.po_break_down_id,b.item_number_id,b.country_id,b.ex_factory_date ";
		 $result_ex=sql_select($sql_exf);
		 $ex_factory_po_data_arr=array(); $ex_factory_po_date_arr=array();
		foreach($result_ex as $row)
		{
			
			$ex_factory_po_date_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('ex_factory_date')]]['qty']+=$row[csf('ex_qnty')]-$row[csf('return_qnty')];
			$ex_factory_po_data_arr[$row[csf('po_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['total_exf_qnty']+=$row[csf('ex_qnty')]-$row[csf('return_qnty')];
			
		}
		if($db_type==0) $item_group="group_concat(distinct c.item_number_id) as item_number_id";
		else if($db_type==2) $item_group="LISTAGG(c.item_number_id, ',') WITHIN GROUP (ORDER BY c.item_number_id) as item_number_id";
		if($db_type==0) $efact_item="and d.ex_factory_qnty!=''";
		else if($db_type==2) $efact_item="and nvl(d.ex_factory_qnty,0)!=0";
		 $sql_plan="select b.id as po_id,a.job_no,a.company_name,$item_group,a.buyer_name,a.product_dept, a.style_ref_no,a.job_quantity,b.po_number,b.po_received_date,c.shiping_status, c.country_ship_date,c.country_id,sum(c.order_quantity) as order_quantity,d.ex_factory_date as ex_factory_date,d.challan_no
					from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown  c 
					LEFT JOIN pro_ex_factory_mst d on  c.po_break_down_id=d.po_break_down_id and d.entry_form=0  and d.delivery_mst_id>0 and d.country_id=c.country_id  and  d.status_active=1 
					and d.is_deleted=0 $efact_item  
					where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=c.job_no_mst  and a.company_name='$company_id'  $country_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0   group by b.id, b.po_number,c.country_id,a.job_no,a.company_name,a.buyer_name,a.product_dept,d.ex_factory_date,d.challan_no, a.style_ref_no,a.job_quantity,b.po_received_date,c.shiping_status, c.country_ship_date  order by b.id,c.country_id,a.job_no";

		$result_data=sql_select($sql_plan); $poids="";
		$i=1;$total_country_qty=0;$total_cumu_ex_factory_qty=0;$total_ex_factory_qty_down=0;
		foreach($result_data as $row )
		{
			
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
		$week_key=$week_data_arr[$row[csf('country_ship_date')]]['week'];
		$exf_week_key=$week_data_arr[$row[csf('ex_factory_date')]]['week'];		
		$unique_id=$row[csf('buyer_name')].'-'.$row[csf('po_id')].'-'.$row[csf('country_id')];
		$ex_factory_date=$row[csf('ex_factory_date')];	
		
		$gmts_item=''; $gmts_item_id=array_unique(explode(",",$row[csf('item_number_id')]));
		$ex_factory_qty=0;$order_qty_pcs=0;$total_ex_factory_qty=0;
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
				$ex_factory_qty+=$ex_factory_po_date_arr[$row[csf('po_id')]][$item_id][$row[csf('country_id')]][$ex_factory_date]['qty'];
				$total_ex_factory_qty+=$ex_factory_po_data_arr[$row[csf('po_id')]][$item_id][$row[csf('country_id')]]['total_exf_qnty'];
				$order_qty_pcs+=$color_po_data_arr[$row[csf('po_id')]][$item_id][$row[csf('country_id')]]['order_quantity'];
	
		}
		 $plan_cycle_time=datediff('d',$row[csf('po_received_date')],$row[csf('country_ship_date')])-1;
		 $actual_cycle_time=datediff('d',$row[csf('po_received_date')],$ex_factory_date)-1;
		 $cycle_time_dely=$actual_cycle_time-$plan_cycle_time;
		 $excess_shipment_qty=$total_ex_factory_qty-$order_qty_pcs;
		 if($ex_factory_date!='0000-00-00')
		 {
			  $ex_factory_date=$ex_factory_date;
			  $year=date("Y",strtotime($ex_factory_date));
		 }
		 else 
		 {
			 $ex_factory_date='';
			  $year=date("Y",strtotime($ex_factory_date));
		 }
		 $exf_mon_year=date("Y-m",strtotime($ex_factory_date));
		 $exf_week=date("Y",strtotime($ex_factory_date));
		
	   ?>
       
        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
        <td width="30"><? echo $i; ?></td>
        <td width="150"><p><? echo $unique_id; ?></p></td>
        <td width="80" align="left"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
        <td width="80" align="left"><p><? echo trim($row[csf('po_number')]); ?></p></td>
        <td width="80"><p><? echo $row[csf('job_no')] ?></p></td>
        <td width="120" align="left"><p><? echo $shipment_status[$row[csf('shiping_status')]]; ?></p></td>
        <td width="120" align="left"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
        <td width="100"><p><? echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
        <td width="120" align="right"><p><? echo $row[csf('job_quantity')]; ?></p></td>
        <td width="100" align="left"><p><? echo $gmts_item; ?></p></td>
        <td width="80"><p><? echo $country_name_library[$row[csf('country_id')]]; ?></p></td>
        <td width="80" align="left"> <p> <? echo change_date_format($row[csf('country_ship_date')]); ?></p></td>
        <td width="80"  align="right"><p><? echo number_format($order_qty_pcs);//$row[csf('order_quantity')]; ?></p></td>
        <td width="80"  align="right"><p><? echo $row[csf('challan_no')]; ?></p></td>
        <td width="100" align="left"><p><? echo change_date_format($ex_factory_date); ?></p></td>
       
        <td width="100"  align="right"><p><? echo number_format($ex_factory_qty,2); ?></p></td>
        <td width="80"  align="right"><p><? echo number_format($total_ex_factory_qty,2); ?></p></td>
        <td width="100"  align="left"><p><? echo change_date_format($row[csf('po_received_date')]); ?></p></td>
        <td width="70" align="left"><p><? echo date("Y",strtotime($row[csf('country_ship_date')])); ?></p></td>
        <td width="90" align="left"><p><? echo date("Y-m",strtotime($row[csf('country_ship_date')])); ?></p></td>
        <td width="70" align="left"><p><? echo date("Y",strtotime($row[csf('country_ship_date')])).'-'.$week_key; ?></p></td>
        
        <td width="60"><p><? if($year=='1970')echo '';else echo $year; ?></p></td>
        <td width="100"><p><? if($exf_mon_year=='1970-01')echo '';else echo $exf_mon_year; ?></p></td>
        <td width="70"><p><? if($exf_week=='1970') echo '';else echo $exf_week.'-'.$exf_week_key; ?></p></td>
        
        <td width="70"><p><? echo $plan_cycle_time; ?></p></td>
        <td width="60"><p><? echo $actual_cycle_time; ?></p></td>
        <td width="100"><p><? echo  $cycle_time_dely; ?></p></td>
        <td width="" align="right"><p><? echo $excess_shipment_qty; ?></p></td>
       
        </tr>
        <?
		$i++;
		$total_country_qty+=$order_qty_pcs;
		$total_ex_factory_qty_down+=$ex_factory_qty;
		$total_cumu_ex_factory_qty+=$total_ex_factory_qty;
		if($poids=="") $poids=$row[csf('po_id')];else $poids.=",".$row[csf('po_id')];
		}
		?>
        <tr>
        <td colspan="12" align="right">Total <? //echo  $poids;?> </td>
        <td align="right"><? echo number_format($total_country_qty,2);?> </td>
         <td align="right"></td> 
         <td align="right"></td> 
         <td align="right"> <? echo number_format($total_ex_factory_qty_down,2);?> </td>
         <td align="right"><? echo number_format($total_cumu_ex_factory_qty,2);?></td> 
         <td colspan="11"></td> 
        </tr>
        
        <tr>
       		<td colspan="28" align="left"><b>Out Of Ship Date(Ex-Factory)</b> </td>
       </tr>
       <?
	   //$poids=$poids;
	  // echo $poids;
		if($db_type==0) $item_group="group_concat(distinct c.item_number_id) as item_number_id";
		else if($db_type==2) $item_group="LISTAGG(c.item_number_id, ',') WITHIN GROUP (ORDER BY c.item_number_id) as item_number_id";
		if($db_type==0) $efact_item="and d.ex_factory_qnty!=''";
		else if($db_type==2) $efact_item="and nvl(d.ex_factory_qnty,0)!=0";
		 $sql_exf_plan="select b.id as po_id,a.job_no,a.company_name,$item_group,a.buyer_name,a.product_dept, a.style_ref_no,a.job_quantity,b.po_number,b.po_received_date,c.shiping_status, c.country_ship_date,c.country_id,sum(c.order_quantity) as order_quantity,d.ex_factory_date as ex_factory_date,d.challan_no
					from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown  c 
					LEFT JOIN pro_ex_factory_mst d on  c.po_break_down_id=d.po_break_down_id and d.entry_form=0  and d.delivery_mst_id>0 and d.country_id=c.country_id  and  d.status_active=1 
					and d.is_deleted=0 $efact_item  
					where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=c.job_no_mst  and a.company_name='$company_id' $not_country_date_cond $ex_factory_date_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0   group by b.id, b.po_number,c.country_id,a.job_no,a.company_name,a.buyer_name,a.product_dept,d.ex_factory_date,d.challan_no, a.style_ref_no,a.job_quantity,b.po_received_date,c.shiping_status, c.country_ship_date  order by b.id,c.country_id,a.job_no";

		$result_arr=sql_select($sql_exf_plan);
		$i=1;$total_country_qty=0;$total_cumu_ex_factory_qty=0;$total_ex_factory_qty_down=0;
		foreach($result_arr as $row )
		{
			
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
		//echo $week_key=$weekarr[$row[csf("country_ship_date")]];
		$week_key=$week_data_arr[$row[csf('country_ship_date')]]['week'];
		$exf_week_key=$week_data_arr[$row[csf('ex_factory_date')]]['week'];
	
		$unique_id=$row[csf('buyer_name')].'-'.$row[csf('po_id')].'-'.$row[csf('country_id')];
		$ex_factory_date=$row[csf('ex_factory_date')];	
		
		$gmts_item=''; $gmts_item_id=array_unique(explode(",",$row[csf('item_number_id')]));
		$ex_factory_qty=0;$order_qty_pcs=0;$total_ex_factory_qty=0;
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
				$ex_factory_qty+=$ex_factory_po_date_arr[$row[csf('po_id')]][$item_id][$row[csf('country_id')]][$ex_factory_date]['qty'];
				$total_ex_factory_qty+=$ex_factory_po_data_arr[$row[csf('po_id')]][$item_id][$row[csf('country_id')]]['total_exf_qnty'];
				$order_qty_pcs+=$color_po_data_arr[$row[csf('po_id')]][$item_id][$row[csf('country_id')]]['order_quantity'];
	
		}
		 $plan_cycle_time=datediff('d',$row[csf('po_received_date')],$row[csf('country_ship_date')])-1;
		 $actual_cycle_time=datediff('d',$row[csf('po_received_date')],$ex_factory_date)-1;
		 $cycle_time_dely=$actual_cycle_time-$plan_cycle_time;
		 $excess_shipment_qty=$total_ex_factory_qty-$order_qty_pcs;
		 if($ex_factory_date!='0000-00-00')
		 {
			  $ex_factory_date=$ex_factory_date;
			  $year=date("Y",strtotime($ex_factory_date));
		 }
		 else 
		 {
			 $ex_factory_date='';
			  $year=date("Y",strtotime($ex_factory_date));
		 }
		 $exf_mon_year=date("Y-m",strtotime($ex_factory_date));
		 $exf_week=date("Y",strtotime($ex_factory_date));
		
	   ?>
       
        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
        <td width="30"><? echo $i; ?></td>
        <td width="150"><p><? echo $unique_id; ?></p></td>
        <td width="80" align="left"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
        <td width="80" align="left"><p><? echo trim($row[csf('po_number')]); ?></p></td>
        <td width="80"><p><? echo $row[csf('job_no')] ?></p></td>
        <td width="120" align="left"><p><? echo $shipment_status[$row[csf('shiping_status')]]; ?></p></td>
        <td width="120" align="left"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
        <td width="100"><p><? echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
        <td width="120" align="right"><p><? echo $row[csf('job_quantity')]; ?></p></td>
        <td width="100" align="left"><p><? echo $gmts_item; ?></p></td>
        <td width="80"><p><? echo $country_name_library[$row[csf('country_id')]]; ?></p></td>
        <td width="80" align="left"> <p> <? echo change_date_format($row[csf('country_ship_date')]); ?></p></td>
        <td width="80"  align="right"><p><? echo number_format($order_qty_pcs);//$row[csf('order_quantity')]; ?></p></td>
        <td width="80"  align="right"><p><? echo $row[csf('challan_no')]; ?></p></td>
        <td width="100" align="left"><p><? echo change_date_format($ex_factory_date); ?></p></td>
       
        <td width="100"  align="right"><p><? echo number_format($ex_factory_qty,2); ?></p></td>
        <td width="80"  align="right"><p><? echo number_format($total_ex_factory_qty,2); ?></p></td>
        <td width="100"  align="left"><p><? echo change_date_format($row[csf('po_received_date')]); ?></p></td>
        <td width="70" align="left"><p><? echo date("Y",strtotime($row[csf('country_ship_date')])); ?></p></td>
        <td width="90" align="left"><p><? echo date("Y-m",strtotime($row[csf('country_ship_date')])); ?></p></td>
        <td width="70" align="left"><p><? echo date("Y",strtotime($row[csf('country_ship_date')])).'-'.$week_key; ?></p></td>
        
        <td width="60"><p><? if($year=='1970')echo '';else echo $year; ?></p></td>
        <td width="100"><p><? if($exf_mon_year=='1970-01')echo '';else echo $exf_mon_year; ?></p></td>
        <td width="70"><p><? if($exf_week=='1970') echo '';else echo $exf_week.'-'.$exf_week_key; ?></p></td>
        
        <td width="70"><p><? echo $plan_cycle_time; ?></p></td>
        <td width="60"><p><? echo $actual_cycle_time; ?></p></td>
        <td width="100"><p><? echo  $cycle_time_dely; ?></p></td>
        <td width="" align="right"><p><? echo $excess_shipment_qty; ?></p></td>
       
        </tr>
        <?
		$i++;
		$total_country_qty+=$order_qty_pcs;
		$total_ex_factory_qty_down+=$ex_factory_qty;
		$total_cumu_ex_factory_qty+=$total_ex_factory_qty;
		}
		?>
        <tr>
        <td colspan="12" align="right">Total </td>
        <td align="right"><? echo number_format($total_country_qty,2);?> </td>
         <td align="right"></td> 
         <td align="right"></td> 
         <td align="right"> <? echo number_format($total_ex_factory_qty_down,2);?> </td>
         <td align="right"><? echo number_format($total_cumu_ex_factory_qty,2);?></td> 
         <td colspan="11"></td> 
        </tr>
        </tr>
        </table>
        </div>
    </fieldset>
    </div>
    <?
	}
	
else if($report_type==5) 
{	
//$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
//$yarn_color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
$start_date ="'".$start_date."'";
$end_date ="'".$end_date."'";

$color_qty_array=array();
$sql_color_qty='select a.job_no AS "job_no",a.buyer_name AS "buyer_name",a.style_ref_no "style_ref_no",b.id AS "id",b.po_number as "po_number",b.po_received_date AS "po_received_date",c.color_number_id AS "color_number_id", c.plan_cut_qnty AS "plan_cut_qnty" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c   where 1=1  and b.pub_shipment_date between '.$start_date.' and '.$end_date.' and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1';

$data_color_qty=sql_select($sql_color_qty);
foreach($data_color_qty as $row_color_qty){
	$color_qty_array[$row_color_qty['id']][$row_color_qty['color_number_id']]+=$row_color_qty['plan_cut_qnty'];
}

$contrast_color=array();
$rowspan=array();
$sql_contrast=sql_select('select a.job_no AS "job_no",a.buyer_name AS "buyer_name",a.style_ref_no "style_ref_no",b.id AS "id",b.po_number as "po_number",b.po_received_date AS "po_received_date",b.shipment_date AS "shipment_date",c.color_number_id AS "color_number_id",f.gmts_color_id AS "gmts_color_id",f.contrast_color_id AS "contrast_color_id",d.fabric_description AS "fabric_description",min(d.gsm_weight) AS "gsm_weight",d.lib_yarn_count_deter_id AS "lib_yarn_count_deter_id",AVG(d.rate) AS "rate",AVG(e.cons) AS "cons",AVG(e.requirment) AS "requirment" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e left join wo_pre_cos_fab_co_color_dtls f on e.pre_cost_fabric_cost_dtls_id=f.pre_cost_fabric_cost_dtls_id and e.color_number_id=f.gmts_color_id  where 1=1 and b.pub_shipment_date between '.$start_date.' and '.$end_date.' and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id   and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.po_received_date, b.shipment_date, d.fabric_description,d.lib_yarn_count_deter_id, c.color_number_id,f.gmts_color_id,f.contrast_color_id order by c.color_number_id');
foreach( $sql_contrast as  $row_contrast){
	if($row_contrast['contrast_color_id']>0){
	$contrast_color[$row_contrast['id']][$row_contrast['lib_yarn_count_deter_id']][$row_contrast['color_number_id']][$row_contrast['contrast_color_id']]=$row_contrast['contrast_color_id'];
	}
	else{
	 $contrast_color[$row_contrast['id']][$row_contrast['lib_yarn_count_deter_id']][$row_contrast['color_number_id']][$row_contrast['color_number_id']]=$row_contrast['color_number_id'];
	}
	$rowspan[$row_contrast['color_number_id']][$row_contrast['contrast_color_id']]=$row_contrast['contrast_color_id'];
}
$tnaarray=array();
	$sql_tna='select a.po_number_id AS "po_number_id",a.task_number AS "task_number",a.task_start_date AS "task_start_date", a.task_finish_date AS "task_finish_date", a.actual_start_date AS "actual_start_date", a.actual_finish_date AS "actual_finish_date"  from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.task_number in(60,73,84,12) and b.pub_shipment_date between '.$start_date.' and '.$end_date.'  and b.status_active=1 and b.po_quantity>0 and b.is_confirmed=1';
	$data_tna=sql_select($sql_tna);
	foreach($data_tna as $row_tna){
		$tnaarray['task_start_date'][$row_tna['task_number']][$row_tna['po_number_id']]=$row_tna['task_start_date'];
		$tnaarray['task_finish_date'][$row_tna['task_number']][$row_tna['po_number_id']]=$row_tna['task_finish_date'];
		$tnaarray['actual_start_date'][$row_tna['task_number']][$row_tna['po_number_id']]=$row_tna['actual_start_date'];
		$tnaarray['actual_finish_date'][$row_tna['task_number']][$row_tna['po_number_id']]=$row_tna['actual_finish_date'];
	}
	
	 $kint_ac=array();
	 $sql_kint_ac='select a.id AS "po_number_id" ,c.febric_description_id AS "febric_description_id",c.color_id AS "color_id",max(b.receive_date) AS "max_receive_date",min(b.receive_date) AS "min_receive_date", sum(d.quantity) AS "quantity"
from  wo_po_break_down a,inv_receive_master b, pro_grey_prod_entry_dtls c,order_wise_pro_details d where b.id=c.mst_id and c.id=d.dtls_id and d.po_breakdown_id=a.id and d.entry_form=2 and  b.entry_form=2 and  a.pub_shipment_date between '.$start_date.' and '.$end_date.' group by a.id,c.febric_description_id,c.color_id';
	$data_kint_ac=sql_select($sql_kint_ac);
	foreach($data_kint_ac as $row_kint_ac){
		$kint_ac['actual_start_date'][$row_kint_ac['febric_description_id']][$row_kint_ac['po_number_id']]=$row_kint_ac['max_receive_date'];
		$kint_ac['actual_finish_date'][$row_kint_ac['febric_description_id']][$row_kint_ac['po_number_id']]=$row_kint_ac['min_receive_date'];
		$kint_ac['quantity'][$row_kint_ac['febric_description_id']][$row_kint_ac['po_number_id']][$row_kint_ac['color_id']]=$row_kint_ac['quantity'];
		
	}
	
	$fin_ac=array();
	  $sql_fin_ac='select a.id AS "po_number_id" ,c.fabric_description_id AS "febric_description_id",c.color_id AS "color_id", max(b.receive_date) AS "max_receive_date",min(b.receive_date) AS "min_receive_date", sum(d.quantity) AS "quantity" from wo_po_break_down a,inv_receive_master b, pro_finish_fabric_rcv_dtls c,order_wise_pro_details d where b.id=c.mst_id and c.id=d.dtls_id and d.po_breakdown_id=a.id and d.entry_form=37 and b.entry_form=37 and a.pub_shipment_date between '.$start_date.' and '.$end_date.' group by a.id,c.fabric_description_id,c.color_id';
	$data_fin_ac=sql_select($sql_fin_ac);
	foreach($data_fin_ac as $row_fin_ac){
		$fin_ac['actual_start_date'][$row_fin_ac['febric_description_id']][$row_fin_ac['po_number_id']]=$row_fin_ac['max_receive_date'];
		$fin_ac['actual_finish_date'][$row_fin_ac['febric_description_id']][$row_fin_ac['po_number_id']]=$row_fin_ac['min_receive_date'];
		$fin_ac['quantity'][$row_fin_ac['febric_description_id']][$row_fin_ac['po_number_id']][$row_fin_ac['color_id']]=$row_fin_ac['quantity'];
	}
	
	    /*$pp=array();
		$sql_pp=sql_select( 'select a.id AS "id", c.color_number_id AS "color_number_id", max(c.approval_status_date) AS "approval_status_date" from wo_po_break_down a , wo_po_color_size_breakdown b, wo_po_sample_approval_info c where a.id=b.po_break_down_id and a.id=c.po_break_down_id and b.color_number_id=c.color_number_id  and a.pub_shipment_date between '.$start_date.' and '.$end_date.' and c.sample_type_id=7 and approval_status=3 and  b.status_active=1 and b.is_deleted=0 group by a.id,c.color_number_id');
		foreach($sql_pp as $row_pp){
			$pp[$row_pp['id']][$row_pp['color_number_id']]=$row_pp['approval_status_date'];
		}*/
		
	
		$cutting=array();
		$sql_cutting=sql_select( 'select a.id AS "id", c.color_number_id AS "color_number_id", max(production_date) as "max_production_date",min(production_date) as "min_production_date" from wo_po_break_down a,  pro_garments_production_mst b, wo_po_color_size_breakdown c, pro_garments_production_dtls d where a.id=b.po_break_down_id and a.id=c.po_break_down_id and b.id=d.mst_id and c.id=d.color_size_break_down_id and a.pub_shipment_date between '.$start_date.' and '.$end_date.' and b.production_type=1 and  b.status_active=1 and b.is_deleted=0 group by a.id,c.color_number_id');
		foreach($sql_cutting as $row_cutting){
			$cutting['max_production_date'][$row_cutting['id']][$row_cutting['color_number_id']]=$row_cutting['max_production_date'];
			$cutting['min_production_date'][$row_cutting['id']][$row_cutting['color_number_id']]=$row_cutting['min_production_date'];
		}
	/*	$batcharr=array();
		$sql_batch=sql_select('select a.color_id AS "color_id",b.po_id AS "po_id", c.detarmination_id AS "detarmination_id", sum(b.batch_qnty) AS "batch_qnty" from pro_batch_create_mst a,pro_batch_create_dtls b,product_details_master c, wo_po_break_down e where a.id=b.mst_id and b.prod_id=c.id and b.po_id=e.id and e.pub_shipment_date between '.$start_date.' and '.$end_date.' group by a.color_id,b.po_id,c.detarmination_id');
		foreach($sql_batch as $row_batch){
			$batcharr[$row_batch['po_id']][$row_batch['detarmination_id']][$row_batch['color_id']]=$row_batch['batch_qnty'];
		}*/
		$batcharr=array();
		$sql_batch=sql_select('select a.color_id AS "color_id",b.po_id AS "po_id", c.detarmination_id AS "detarmination_id", sum(b.batch_qnty) AS "batch_qnty" from pro_batch_create_mst a,pro_batch_create_dtls b,product_details_master c, wo_po_break_down e where a.id=b.mst_id and b.prod_id=c.id and b.po_id=e.id and e.pub_shipment_date between '.$start_date.' and '.$end_date.' and a.batch_against<>2 and a.entry_form=0 group by a.color_id,b.po_id,c.detarmination_id');
		foreach($sql_batch as $row_batch){
			$batcharr[$row_batch['po_id']][$row_batch['detarmination_id']][$row_batch['color_id']]=$row_batch['batch_qnty'];
		}
		
		$dye_qnty_arr=array();
		$sql_dye='select b.po_id, a.color_id,e.detarmination_id AS "detarmination_id", sum(b.batch_qnty) as dye_qnty from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c,product_details_master e,wo_po_break_down f where a.id=b.mst_id and a.id=c.batch_id and b.prod_id=e.id and b.po_id=f.id and f.pub_shipment_date between '.$start_date.' and '.$end_date.' and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.po_id, a.color_id,e.detarmination_id';
		$resultDye=sql_select($sql_dye);
		foreach($resultDye as $dyeRow)
		{
			$dye_qnty_arr[$dyeRow[csf('po_id')]][$dyeRow[csf('detarmination_id')]][$dyeRow[csf('color_id')]]=$dyeRow[csf('dye_qnty')];
		}
		//echo 'select a.po_breakdown_id AS "po_breakdown_id", a.color_id AS "color_id",b.detarmination_id AS "detarmination_id",  sum(quantity) AS "finish_receive" from order_wise_pro_details a, product_details_master b,wo_po_break_down c where a.prod_id=b.id and a.po_breakdown_id=c.id and  a.status_active=1 and a.is_deleted=0 and a.entry_form =7 and c.pub_shipment_date between '.$start_date.' and '.$end_date.' group by a.po_breakdown_id, a.color_id,b.detarmination_id'; 
		//die;
		$finProdArr=array();
		$finProdSql=sql_select('select a.po_breakdown_id AS "po_breakdown_id", a.color_id AS "color_id",b.detarmination_id AS "detarmination_id",  sum(quantity) AS "finish_receive" from order_wise_pro_details a, product_details_master b,wo_po_break_down c where a.prod_id=b.id and a.po_breakdown_id=c.id and  a.status_active=1 and a.is_deleted=0 and a.entry_form =7 and c.pub_shipment_date between '.$start_date.' and '.$end_date.' group by a.po_breakdown_id, a.color_id,b.detarmination_id');
		foreach($finProdSql as $finProdRow){
			$finProdArr[$finProdRow['po_breakdown_id']][$finProdRow['detarmination_id']][$finProdRow['color_id']]=$finProdRow['finish_receive'];
		}
		$book_req_arr=array();
		$book_req_sql=sql_select('select a.lib_yarn_count_deter_id,b.fabric_color_id,c.id,sum(b.fin_fab_qnty) AS "fin_fab_qnty", sum(grey_fab_qnty) AS "grey_fab_qnty" FROM wo_pre_cost_fabric_cost_dtls a,  wo_booking_dtls b ,wo_po_break_down c WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=c.id and c.pub_shipment_date between '.$start_date.' and '.$end_date.' group by a.lib_yarn_count_deter_id,b.fabric_color_id,c.id');
		foreach($book_req_sql as $book_req_row){
			$book_req_arr['fin_fab_qnty'][$book_req_row['id']][$book_req_row['lib_yarn_count_deter_id']][$book_req_row['fabric_color_id']]=$book_req_row['fin_fab_qnty'];
			$book_req_arr['grey_fab_qnty'][$book_req_row['id']][$book_req_row['lib_yarn_count_deter_id']][$book_req_row['fabric_color_id']]=$book_req_row['grey_fab_qnty'];
		}
	
		  $sql='select a.job_no AS "job_no",a.buyer_name AS "buyer_name",a.style_ref_no "style_ref_no",b.id AS "id",b.po_number as "po_number",b.po_received_date AS "po_received_date",b.pub_shipment_date AS "shipment_date",b.pp_meeting_date AS "pp_meeting_date",c.color_number_id AS "color_number_id",d.fabric_description AS "fabric_description",min(d.gsm_weight) AS "gsm_weight",d.lib_yarn_count_deter_id AS "lib_yarn_count_deter_id",AVG(d.rate) AS "rate",AVG(e.cons) AS "cons",AVG(e.requirment) AS "requirment" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e where 1=1  and b.pub_shipment_date between '.$start_date.' and '.$end_date.' and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.po_received_date, b.pub_shipment_date,b.pp_meeting_date , d.fabric_description,d.lib_yarn_count_deter_id, c.color_number_id order by b.id';
		
		
		$data=sql_select($sql);

//print_r($data);
$condition= new condition();
	if(str_replace("'","",$start_date) !='' && str_replace("'","",$end_date)){
		$condition->pub_shipment_date('between '.$start_date.' and '.$end_date.'');
	}
	$condition->init();
	$fabric= new fabric($condition);
	$fabric_costing_arr=$fabric->getQtyArray_by_OrderLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
	ob_start();
	?>
    <div style="width:2440px; " align="left">
   <!-- <h3 align="left" id="accordion_h2" style="width:2340px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel</h3>-->
    <fieldset style="width:100%;" id="content_search_panel2">	
        <table width="2440">
            <tr class="form_caption">
            <td colspan="28" align="center"><strong><? echo $report_title;?></strong></td>
            </tr>
            <tr class="form_caption">
            <td colspan="28" align="center"><strong><? echo $company_library[$company_id]; ?></strong></td>
            </tr>
             <tr class="form_caption">
            <td colspan="28" align="center"><strong><? echo change_date_format(str_replace("'","",$start_date)).' To '.change_date_format(str_replace("'","",$end_date)); ?></strong></td>
            </tr>
        </table>
        <table width="4450" id="table_header_1" border="1" class="rpt_table" rules="all">
    <thead>
        <tr>
            <th width="30">SL</th>
            <th width="50">Booking rcvd date</th>
            <th width="50" >OPD Date</th>
            <th width="50">Buyer</th>
            <th width="50">Order No</th>
            <th width="50">Job No</th>
            <th width="60">style ref</th>                   
            <th width="100">Color Name (Unique to Booking col)</th>
            
            <th width="100">Required Ship Qty (Col wise Pcs)</th>
            <th width="130">Fabrication</th>
            <th width="30">GSM</th>
            <th width="100">Fabric Color</th>
            <th width="80">Shipment date</th>
            <th width="90">Avg Fab Consumption per dozen</th>
            <th width="100">Knit TOD Start</th>
            <th width="100">Knit TOD End</th>
            <th width="100">Fabric Del Start</th> 
            <th width="100">Fabric Del End</th>
            <th width="80">Actual Knit Start date</th>
            <th width="80">Actual Knit End date</th>
            <th width="100">Actual Fabric Del Start Date</th>
            <th width="100">Actual Fabric Del End Date</th>
            <th width="90">Cutting Start Date</th>
            <th width="90">Cutting End Date</th>
            <th width="100">Actual Cutting Start Date</th>
            <th width="80">Actual Cutting End Date</th>                                                         
            <th width="80">PP Meeting Date</th>
            <th width="100">Reqd Grey fab QTY( Booking)</th>
            <th width="100">Req Finish fab QTY (Booking)</th>
            <th width="80">Knitted qty</th>
            <th width="80">Knit Bal</th>
            <th width="80">Batch Qty</th>
            <th width="80">Dyed Qty</th>
            <th width="80">Dyed Bal</th>
            <th width="80">Dyeing-Finshing qty</th>
            <th width="80">Dyeing-Finshing Bal</th>
            <th width="60">Finish Fab Del Qty</th>
            <th width="60">Finish Fab Del Bal</th>
            <th width="60">Ship Month</th>
        </tr>
    </thead>
    
    <tbody> 
    <?
	$check_color=array();
	$i=1;
	foreach($data as $row){
		$color=1;
		foreach($contrast_color[$row['id']][$row['lib_yarn_count_deter_id']][$row['color_number_id']] as $contrast_color_id){
			$contrast_color_id=$contrast_color_id;
			if($contrast_color_id=='' || $contrast_color_id==0){
				$contrast_color_id=$row['color_number_id'];
			}
	?>
        <tr>
            <td width="30"><? echo $i; ?></td>
           	<td width="50"><? echo date("d-m-Y",strtotime($row['po_received_date'])); ?></td>
            <td width="50"></td>
            <td width="50"><? echo $buyer_library[$row['buyer_name']]; ?></td>
            <td width="50"><? echo $row['po_number']; ?></td>
            <td width="50"><? echo $row['job_no']; ?></td>
            <td width="60"><? echo $row['style_ref_no']; ?></td> 
            <?
			//if($color==1){
			?>
            <td width="100"  rowspan="<? //echo count($contrast_color[$row['lib_yarn_count_deter_id']][$row['color_number_id']]); ?>">
			<? 
			echo $yarn_color_library[$row['color_number_id']]; 
			?>
            </td>
            <td width="100" rowspan="<? //echo count($contrast_color[$row['lib_yarn_count_deter_id']][$row['color_number_id']]); ?>">
			<? 
			if($check_color[$row['id']][$row['color_number_id']]==''){
			echo $color_qty_array[$row['id']][$row['color_number_id']];
			$check_color[$row['id']][$row['color_number_id']]=$row['color_number_id'];
			}
			//$check_color[$row['id']][$row['color_number_id']]=$row['color_number_id'];
			?>
            </td>
            <?
			//}
			?>
            
            
            <td width="130" title="<? echo $row['pre_cost_dtls_id']; ?>"><? echo $row['fabric_description']; ?> </td> 
            <td width="30"><? echo $row['gsm_weight']; ?> </td>
            <td width="100"><? echo $yarn_color_library[$contrast_color_id]; ?></td>
            <td width="80"><? echo date("d-m-Y",strtotime($row['shipment_date'])); ?></td>
            <td width="90" align="right">
            <?
			echo $fabric_costing_arr['knit']['finish'][$row['id']][$row['lib_yarn_count_deter_id']][$row['color_number_id']]/$color_qty_array[$row['id']][$row['color_number_id']]*12;
			?>
            </td>
            <td width="100">
			<? 
			$KnitTODStart=date("d-m-Y",strtotime($tnaarray['task_start_date'][60][$row['id']]));
			if($KnitTODStart !='01-01-1970'){
			echo $KnitTODStart;
			}
			?>
            </td>
            <td width="100">
			<? 
			$KnitTODEnd=date("d-m-Y",strtotime($tnaarray['task_finish_date'][60][$row['id']])); 
			if($KnitTODEnd !='01-01-1970'){
			echo $KnitTODEnd; 
			}
			?>
            </td>
            <td width="100">
			<? 
			$FabricDelStart=date("d-m-Y",strtotime($tnaarray['task_start_date'][73][$row['id']])); 
			if($FabricDelStart !='01-01-1970'){
			echo $FabricDelStart; 
			} 
			?>
            </td> 
            <td width="100">
			<? 
			$FabricDelEnd = date("d-m-Y",strtotime($tnaarray['task_finish_date'][73][$row['id']])); 
			if($FabricDelEnd !='01-01-1970'){
			echo $FabricDelEnd; 
			} 
			?>
            </td>
            <td width="80">
			<? 
			$ActualKnitStart=date("d-m-Y",strtotime($kint_ac['actual_start_date'][$row['lib_yarn_count_deter_id']][$row['id']]));
			if($ActualKnitStart !='01-01-1970'){
			echo $ActualKnitStart; 
			} 
			?>
            </td>
            <td width="80">
			<?
			$ActualKnitEnddate = date("d-m-Y",strtotime($kint_ac['actual_finish_date'][$row['lib_yarn_count_deter_id']][$row['id']])); 
			if($ActualKnitEnddate !='01-01-1970')
			{
				echo $ActualKnitEnddate;
			}			
			?>
            </td>
            <td width="100">
			<?
			$ActualFabricDelStartDate = date("d-m-Y",strtotime($fin_ac['actual_start_date'][$row['lib_yarn_count_deter_id']][$row['id']])); 
			if($ActualFabricDelStartDate !='01-01-1970')
			{
				echo $ActualFabricDelStartDate;
			}	 
			?>
            </td>
            <td width="100">
			<? 
			$ActualFabricDelEndDate =  date("d-m-Y",strtotime($fin_ac['actual_finish_date'][$row['lib_yarn_count_deter_id']][$row['id']])); 
			if($ActualFabricDelEndDate !='01-01-1970')
			{
				echo $ActualFabricDelEndDate;
			}	
			?>
            </td>
            <td width="90">
			<? 
			$CuttingStartDate =  date("d-m-Y",strtotime($tnaarray['task_start_date'][84][$row['id']])); 
			if($CuttingStartDate !='01-01-1970')
			{
				echo $CuttingStartDate;
			}	 
			?>
            </td>
            <td width="90">
			<?
			$CuttingEndDate =  date("d-m-Y",strtotime($tnaarray['task_finish_date'][84][$row['id']])); 
			if($CuttingEndDate !='01-01-1970')
			{
				echo $CuttingEndDate;
			}	
			?>
            </td>
            <td width="100">
			<? 
			$ActualCuttingStartDate = date("d-m-Y",strtotime($cutting['max_production_date'][$row['id']][$row['color_number_id']]));
			if($ActualCuttingStartDate !='01-01-1970')
			{
				echo $ActualCuttingStartDate;
			}	
			?>
            </td>
            <td width="80">
			<? 
			$ActualCuttingEndDate = date("d-m-Y",strtotime($cutting['min_production_date'][$row['id']][$row['color_number_id']])) ;
			if($ActualCuttingEndDate !='01-01-1970')
			{
				echo $ActualCuttingEndDate;
			}
			?>
            </td>     
                                                                
            <td width="80">
			<? 
			 $pp_meeting_date=date("d-m-Y",strtotime($row['pp_meeting_date'])); 
			 if($pp_meeting_date !='01-01-1970' && $pp_meeting_date != '30-11--0001')
				{
					echo $pp_meeting_date;
				}
			 ?>
			
            </td>
            <td width="100">
			<? 
			echo $book_req_arr['grey_fab_qnty'][$row['id']][$row['lib_yarn_count_deter_id']][$contrast_color_id];

			
			?>
            </td>
            <td width="100">
			<?
			echo $book_req_arr['fin_fab_qnty'][$row['id']][$row['lib_yarn_count_deter_id']][$contrast_color_id];

			?>
            </td>
            <td width="80"><?   echo $kint_ac['quantity'][$row['lib_yarn_count_deter_id']][$row['id']][$contrast_color_id]; ?></td>
            <td width="80">
            <?
			echo $book_req_arr['grey_fab_qnty'][$row['id']][$row['lib_yarn_count_deter_id']][$contrast_color_id]-$kint_ac['quantity'][$row['lib_yarn_count_deter_id']][$row['id']][$contrast_color_id];
			
			?>
            </td>
            <td width="80">
			<? 
			//if($contrast_color_id>0){
			echo $batcharr[$row['id']][$row['lib_yarn_count_deter_id']][$contrast_color_id];
			//}else{
			//echo $batcharr[$row['id']][$row['lib_yarn_count_deter_id']][$row['color_number_id']];
			//}
			?>
            </td>
            <td width="80">
			<? 
			//if($contrast_color_id>0){
			echo $dye_qnty_arr[$row[csf('id')]][$row[csf('lib_yarn_count_deter_id')]][$contrast_color_id]; 
			//}else{
			//echo $dye_qnty_arr[$row['id']][$row['lib_yarn_count_deter_id']][$row['color_number_id']];
			//}
			?>
            </td>
            <td width="80"><? echo $batcharr[$row['id']][$row['lib_yarn_count_deter_id']][$contrast_color_id]-$dye_qnty_arr[$row[csf('id')]][$row[csf('lib_yarn_count_deter_id')]][$contrast_color_id]?></td>
            <td width="80"><? echo $finProdArr[$row['id']][$row['lib_yarn_count_deter_id']][$contrast_color_id]; ?></td>
            <td width="80">
			<? 
			echo $book_req_arr['fin_fab_qnty'][$row['id']][$row['lib_yarn_count_deter_id']][$contrast_color_id]-$finProdArr[$row['id']][$row['lib_yarn_count_deter_id']][$contrast_color_id];
			?>
            </td>
            <td width="60"><? echo  $fin_ac['quantity'][$row['lib_yarn_count_deter_id']][$row['id']][$contrast_color_id]; ?></td>
            <td width="60">
           <? 
		   	echo $book_req_arr['fin_fab_qnty'][$row['id']][$row['lib_yarn_count_deter_id']][$contrast_color_id]-$fin_ac['quantity'][$row['lib_yarn_count_deter_id']][$row['id']][$contrast_color_id]; 
			?>
            </td>
            <td width="60"><? echo date("M",strtotime($row['shipment_date'])); ?></td>
        </tr>
        
        <?
		$color++;
        $i++;
		}
	}
?>
    </tbody>
</table>
    </fieldset>
    </div>
    <?
	}
	$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("ptm*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="ptm".$user_name."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');	
		$is_created = fwrite($create_new_doc, $html);
		echo "$filename####$report_button"; 
	//exit();
	
}


if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	
	$company_id=str_replace("'","",$cbo_company_id);
	$report_type=str_replace("'","",$type);
	$report_title=str_replace("'","",$report_title);
	$report_button_id=31;
	ob_start();

	
	?>
    <div style="width:2260px; display:none" align="left">
    <h3 align="left" id="accordion_h2" style="width:2260px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')"> -Search Panel 2</h3>
    <fieldset style="width:100%;" id="content_search_panel2">	
        <table width="2260">
            <tr class="form_caption">
            <td colspan="27" align="center"><strong><? echo $report_title;?></strong></td>
            </tr>
            <tr class="form_caption">
            <td colspan="27" align="center"><strong><? echo $company_library[$company_id]; ?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" width="2260" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <tr>
            <th width="30">SL</th>
            <th width="80">Projected by</th>
            <th width="80">Projection date</th>
            <th width="120">Customer</th>
            <th width="120">SKU/ Item name</th> 
            <th width="100">Program/Dept.</th>
            <th width="120">Style Name/Style No</th>
            <th width="60">SMV</th>
            <th width="80">Season</th>
            <th width="80">OPD (DD/MM/YY)</th>
            <th width="80">TOD (DD/MM/YY)</th>
            <th width="100">Fabrication</th>
            <th width="80">Color</th>
            <th width="100">Order Status/ Order No</th>
            <th width="60">Fabric Weight (GSM)</th>
            <th width="150">Yarn Composition</th>
            <th width="70">Yarn Count</th>
            <th width="70">Yarn Type</th>
            <th width="80">Garments Qty (Pcs)</th>
            <th width="70">Unit Price</th>
            <th width="80">Eqv.Basic qty. (pcs)</th>
            <th width="70">Consumption Per Pcs</th>
            
            <th width="80">Required Yarn(kg)</th>
            <th width="80">Budgeted Yarn Price App.($)</th>
            <th width="80">Value</th>
            <th width="70">FC-Month</th>
            <th width="">Target Month</th>
            
            
            </tr>
        </thead>
        </table>
        <div style="width:2280px; max-height:400px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="2260" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
       <?
	   $sql_export2="select a.job_no,a.product_dept,  a.buyer_name, a.style_ref_no,a.season,a.set_smv, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio,  b.is_confirmed, b.plan_cut, b.id as po_id, b.po_number, b.shipment_date, b.po_received_date,b.po_quantity, b.unit_price
			from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
			
		$result_data=sql_select($sql_export2);
		 $i=1; 
      		
		foreach($result_data as $row )
		{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
			
	  	$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
	   ?>
        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
        <td width="30"><? echo $i; ?></td>
        <td width="80"><p><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></p></td>
        <td width="80"><p><? echo date('d-m-Y'); ?></p></td>
        <td width="120"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
        <td width="120"><p><? 
		$gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
		}
		echo $gmts_item; ?></p></td>
        <td width="100"><p><? echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
        <td width="120"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
        
        <td width="60"><p><? echo $row[csf('set_smv')]; ?></p></td>
        <td width="80"><p><? echo $row[csf('season')]; ?></p></td>
        <td width="80"><p><? //echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
        <td width="80"><p><? echo change_date_format($row[csf('shipment_date')]); ?></p></td>
        
        <td width="100"><p><? //echo $row[csf('set_smv')]; ?></p></td>
        <td width="80"><p><? //echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
        <td width="100"><p><? //echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
        <td width="60"><p><? //echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
        <td width="150"><p><? //echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
        <td width="70"><p><? //echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
        <td width="70"><p><? //echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
        
        <td width="80"><p><? echo $order_qty_pcs; ?></p></td>
        <td width="70"><p><? echo $row[csf('unit_price')]; ?></p></td>
        <td width="80"><p><? //echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
        <td width="70"><p><? //echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
        
      	<td width="80"><p><? //echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
      	<td width="80"><p><? //echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
      	<td width="80"><p><? //echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
        
        <td width="70"><p><? //echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
      	<td width=""><p><? //echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
      	
              
          
           
        </tr>
        
        <?
		$i++;
		}
		?>
        </table>
        </div>
            </fieldset>
    </div>
    <?
		$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("ptm2*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="ptm2".$user_name."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');	
		$is_created = fwrite($create_new_doc, $html);
		echo "$html####$filename####$report_button_id"; 
	
	//exit();
}
?>
