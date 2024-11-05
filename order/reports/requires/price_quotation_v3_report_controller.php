<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
	exit();
}

if ($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	
	
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	
	$report_button_id = str_replace("'","",$operation);
	if($db_type==0)
	{
		if($report_button_id == 2){
			$report_button_cond = " and UNIX_TIMESTAMP(`order_conf_date`) != 0 ";
		}else if($report_button_id == 3){
			$report_button_cond = " and UNIX_TIMESTAMP(`order_conf_date`) = 0  ";
		}else{
			$report_button_cond = "";
		}
	}
	
	if($db_type==2)
	{
		if($report_button_id == 2){
			$report_button_cond = " and a.order_conf_date  is not null ";
		}else if($report_button_id == 3){
			$report_button_cond = " and a.order_conf_date  is null ";
		}else{
			$report_button_cond = "";
		}
		
	}
	
	if ($cbo_company==0) $company_cond=""; else $company_cond=" and a.company_id=$cbo_company";
	if ($cbo_buyer==0) $buyer_cond=""; else $buyer_cond=" and a.buyer_id=$cbo_buyer"; 
	
	

	if($db_type==0)
	{
		if( $date_from=="" && $date_to=="" ) $date_cond=""; else $date_cond=" and a.quot_date between '".$date_from."' and '".$date_to."'";
	}
	if($db_type==2)
	{
		if( $date_from=="" && $date_to=="" ) $date_cond=""; else $date_cond=" and a.quot_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
	}

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$AgentArr=return_library_array( "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name", "id", "buyer_name");

	
	$sql="select a.id, a.company_id, a.system_id, a.quot_date, a.team_member, a.buyer_id, a.agent, a.style_ref, a.gmts_item, a.fabrication, a.order_qty, a.cons_size, a.yarn_total, a.knit_fab_purc_total, a.woven_fab_purc_total, a.yarn_dye_crg_total, a.knit_crg_total, a.dye_crg_total, a.spandex_total, a.aop_total, a.print_total, a.embro_total, a.gmts_wash_dye_total, a.access_price_total, a.cm_total, a.others_cost_total, a.comm_cost_total, a.fact_u_price, a.agnt_comm_tot, a.local_comm_tot, a.final_offer_price, a.order_conf_date, a.order_conf_price, sum(b.ttl_top_bottom_cons) as fabric_cons 
	FROM wo_price_quotation_v3_mst a, wo_price_quotation_v3_dtls b 
	WHERE a.status_active=1 AND a.is_deleted=0  AND b.status_active=1 $report_button_cond  $date_cond   $company_cond  $buyer_cond  AND b.is_deleted=0 AND a.id=b.mst_id 
	group by a.id, a.company_id, a.system_id, a.quot_date, a.team_member, a.buyer_id, a.agent, a.style_ref, a.gmts_item, a.fabrication, a.order_qty, a.cons_size, a.yarn_total, a.knit_fab_purc_total, a.woven_fab_purc_total, a.yarn_dye_crg_total, a.knit_crg_total, a.dye_crg_total, a.spandex_total, a.aop_total, a.print_total, a.embro_total, a.gmts_wash_dye_total, a.access_price_total, a.cm_total, a.others_cost_total, a.comm_cost_total, a.fact_u_price, a.agnt_comm_tot, a.local_comm_tot, a.final_offer_price, a.order_conf_date, a.order_conf_price
	ORDER BY a.id, a.quot_date";
	
	
	$sql_result=sql_select($sql);
	
	//echo "<pre>";
	//print_r($sql_result);die; 
	
	
	ob_start();
	?>
    <style>
    	.wordBreakWrap{
			word-break: break-all;
			word-wrap: break-word;
		}
    </style>
		<fieldset>
			<table width="2450"  cellspacing="0" >
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none; font-size:18px;" colspan="25"><? echo $company_library[str_replace("'","",$cbo_company_id)]; ?></td>
				</tr>
				<tr class="" style="border:none;">
					<td align="center" style="border:none;font-size:16px; font-weight:bold"  colspan="25"> <? echo $report_title; ?></td>
				</tr>
				<tr class="" style="border:none;">
					<td align="center" style="border:none;font-size:12px; font-weight:bold"  colspan="25"> <? if( $date_from!="" && $date_to!="" ) echo "From  ".change_date_format($date_from)."  To  ".change_date_format($date_to);?></td>
				</tr>
			</table>
			<table width="2450" cellspacing="0" border="1" class="rpt_table" rules="all" align="left">
				<thead>
                    <tr>
                        <th width="30"><p class="wordBreakWrap">SL</p></th>   
                        <th width="120"><p class="wordBreakWrap">PQ ID</p></th>
                        <th width="80"><p class="wordBreakWrap">PQ Date</p></th>
                        <th width="120"><p class="wordBreakWrap">Team Member</p></th>
                        <th width="120"><p class="wordBreakWrap">Buyer</p></th>
                        <th width="120"><p class="wordBreakWrap">Agent</p></th>
                        <th width="100"><p class="wordBreakWrap">Style Ref.</p></th>
                        <th width="120"><p class="wordBreakWrap">Garment Item</p></th>
                        <th width="120"><p class="wordBreakWrap">Fabrication</p></th>
                        <th width="80"><p class="wordBreakWrap">Offered Qty</p></th>
                        <th width="120"><p class="wordBreakWrap">Fabric Cons</p></th>
                        <th width="100"><p class="wordBreakWrap">Total Fabric Cost /Dzn</p></th>
                        <th width="100"><p class="wordBreakWrap">Trims Cost /Dzn</p></th>
                        <th width="100"><p class="wordBreakWrap">Print Cost /Dzn</p></th>
                        <th width="100"><p class="wordBreakWrap">Emb. Cost /Dzn</p></th>
                        <th width="100"><p class="wordBreakWrap">Gmts Wash. Cost /Dzn</p></th>
                        <th width="100"><p class="wordBreakWrap">CM Cost /Dzn</p></th>
                        <th width="100"><p class="wordBreakWrap">Other Cost /Dzn</p></th>
                        <th width="100"><p class="wordBreakWrap">Commercia Cost /Dzn</p></th>
                        <th width="100"><p class="wordBreakWrap">Factory Unit Price</p></th>
                        <th width="100"><p class="wordBreakWrap">Commission Cost Agent /Dzn</p></th>
                        <th width="100"><p class="wordBreakWrap">Commission Cost Local /Dzn</p></th>
                        <th width="100"><p class="wordBreakWrap">Final Offer Price</p></th>
                        <th width="80"><p class="wordBreakWrap">Conf. Date</p></th>
                        <th width="100"><p class="wordBreakWrap">Conf. Price /Pcs</p></th>
                    </tr>
				</thead>
			</table>
			<div style="max-height:350px; overflow-y:scroll; width:2470px" id="scroll_body" >
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2450" rules="all" id="table_body" align="left" >
				<?
				$total_wo_qnty = $total_wo_amt =  0;
				$i=1;
				foreach($sql_result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$total_fabric_cost = $row[csf("yarn_total")] + $row[csf("knit_fab_purc_total")] + $row[csf("woven_fab_purc_total")] + $row[csf("yarn_dye_crg_total")] + $row[csf("knit_crg_total")] + $row[csf("dye_crg_total")] + $row[csf("spandex_total")] + $row[csf("aop_total")];
					$queryString = $row[csf("company_id")]."_".$row[csf("id")]."_".$row[csf("gmts_item")];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30"><p class="wordBreakWrap"><? echo $i;?></p></td>
                        <td width="120"><p class="wordBreakWrap"><a href="#" onclick="price_quotation_print('<? echo $queryString; ?>')"><? echo $row[csf("system_id")]; ?></a></p></td>	
						<td width="80" align="center"><p class="wordBreakWrap"><? echo change_date_format($row[csf("quot_date")]); ?></p></p></td>
                        <td width="120"><p class="wordBreakWrap"><? echo $row[csf("team_member")]; ?></p></td>
                        <td width="120"><p class="wordBreakWrap"><? echo $buyerArr[$row[csf("buyer_id")]]; ?></p></td>
                        <td width="120"><p class="wordBreakWrap"><? echo $AgentArr[$row[csf("agent")]]; ?></p></td>
                        <td width="100"><p class="wordBreakWrap"><? echo $row[csf("style_ref")]; ?></p></td>
                        <td width="120"><p class="wordBreakWrap"><? echo $row[csf("gmts_item")]; ?></p></td>
                        <td width="120"><p class="wordBreakWrap"><? echo $row[csf("fabrication")]; ?></p></td>
                        <td width="80" align="right"><p class="wordBreakWrap"><? echo number_format($row[csf("order_qty")]); ?></p></td>
                        <td width="120" align="right"><p class="wordBreakWrap"><? echo number_format($row[csf("fabric_cons")],4,".",""); ?></p></td>
                        <td width="100" align="right"><p class="wordBreakWrap"><? echo number_format($total_fabric_cost,4,".",""); ?></p></td>
                        <td width="100" align="right"><p class="wordBreakWrap"><? echo number_format($row[csf("access_price_total")],4,".",""); ?></p></td>
                        <td width="100" align="right"><p class="wordBreakWrap"><? echo number_format($row[csf("print_total")],4,".",""); ?></p></td>
                        <td width="100" align="right"><p class="wordBreakWrap"><? echo number_format($row[csf("embro_total")],4,".",""); ?></p></td>
                        <td width="100" align="right"><p class="wordBreakWrap"><? echo number_format($row[csf("gmts_wash_dye_total")],4,".",""); ?></p></td>
                        <td width="100" align="right"><p class="wordBreakWrap"><? echo number_format($row[csf("cm_total")],4,".",""); ?></p></td>
                        <td width="100" align="right"><p class="wordBreakWrap"><? echo number_format($row[csf("others_cost_total")],4,".",""); ?></p></td>
                        <td width="100" align="right"><p class="wordBreakWrap"><? echo number_format($row[csf("comm_cost_total")],4,".",""); ?></p></td>
                        <td width="100" align="right"><p class="wordBreakWrap"><? echo number_format($row[csf("fact_u_price")],4,".",""); ?></p></td>
                        <td width="100" align="right"><p class="wordBreakWrap"><? echo number_format($row[csf("agnt_comm_tot")],4,".",""); ?></p></td>
                        <td width="100" align="right"><p class="wordBreakWrap"><? echo number_format($row[csf("local_comm_tot")],4,".",""); ?></p></td>
                        <td width="100" align="right"><p class="wordBreakWrap"><? echo number_format($row[csf("final_offer_price")],4,".",""); ?></p></td>
                        <td width="80" align="center"><p class="wordBreakWrap"><? echo change_date_format($row[csf("order_conf_date")]); ?></p></td>
                        <td width="100" align="right"><p class="wordBreakWrap"><? echo number_format($row[csf("order_conf_price")],4,".",""); ?></p></td>
					</tr>
					<?
					$i++;
					$total_wo_qnty +=  $row[csf("order_qty")];
					$total_wo_amt +=  $row[csf("fabric_cons")];
				}
				?>
				</table>
                <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2450" rules="all" align="left" >
					<tfoot>
						<tr>
                            <th width="30">&nbsp;</th>   
                            <th width="120">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="80" id="value_total_wo_qnty"><p class="wordBreakWrap"><? echo number_format($total_wo_qnty,2,".",""); ?></p></th>
                            <th width="120" id="value_total_wo_amnt______"><p class="wordBreakWrap"><? //echo number_format($total_wo_amt,2,".",""); ?></p></th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="100">&nbsp;</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) 
    {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
	exit();
}


if($action == "top_botton_report")
{
	extract($_REQUEST);
	echo load_html_head_contents("Price Quotation", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);
    $buyer_name_arr = return_library_array("select a.id, a.buyer_name from lib_buyer a, lib_buyer_party_type b, lib_buyer_tag_company c  where a.id=b.buyer_id and a.id=c.buyer_id and b.party_type=1 and c.tag_company='$data[0]' and a.status_active=1 and a.is_deleted =0 order by a.buyer_name", "id", "buyer_name");
	
    $agent_name_arr = return_library_array("select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name order by buyer_name", "id", "buyer_name");
	
	$company_library = return_library_array("select id,company_name  from lib_company where status_active =1 and is_deleted=0", "id", "company_name");
	
	
	$sql = "SELECT a.id, a.sys_no_prefix, a.sys_prefix_num, a.system_id, a.company_id, a.team_member, a.buyer_id, a.quot_date, a.agent, a.style_ref, a.gmts_item, a.fabrication, a.color, a.yarn_count, a.cons_size, a.order_qty, a.measurment_basis, a.yarn_cons, a.yarn_unit_price, a.yarn_total, a.knit_fab_purc_cons, a.knit_fab_purc_price, a.knit_fab_purc_total, a.woven_fab_purc_cons, a.woven_fab_purc_price, a.woven_fab_purc_total, a.yarn_dye_crg_cons, a.yarn_dye_crg_price, a.yarn_dye_crg_total, a.knit_crg_cons, a.knit_crg_unit_price, a.knit_crg_total, a.dye_crg_cons, a.dye_crg_unit_price, a.dye_crg_total, a.spandex_amt, a.spandex_cons, a.spandex_unit_price, a.spandex_total, a.aop_cons, a.aop_price, a.aop_total, a.collar_cuff_cons, a.collar_cuff_unit_price, a.collar_cuff_total, a.print_cons, a.print_unit_price, a.print_total, a.gmts_wash_dye_cons, a.gmts_wash_dye_price, a.gmts_wash_dye_total, a.access_price_cons, a.access_price_unit_price, a.access_price_total, a.zipper_cons, a.zipper_unit_price, a.zipper_total, a.button_cons, a.button_unit_price, a.button_total, a.test_cons, a.test_unit_price, a.test_total, a.cm_cons, a.cm_unit_price, a.cm_total, a.inspec_cost_cons, a.inspec_cost_unit_price, a.inspec_cost_total, a.freight_cons, a.freight_unit_price, a.freight_total, a.carrier_cost_cons, a.carrier_cost_unit_price, a.carrier_cost_total, a.others_column_caption, a.others_cost_cons, a.others_cost_unit_price, a.others_cost_total, a.comm_cost_cons, a.comm_cost_price, a.comm_cost_total, a.remarks, a.fact_u_price, a.agnt_comm, a.agnt_comm_tot, a.local_comm, a.local_comm_tot, a.final_offer_price, a.order_conf_price, a.order_conf_date, a.embro_cons, a.embro_unit_price, a.embro_total, a.uom_yarn, a.uom_knit_fab_purc, a.uom_woven_fab_purc, a.uom_yarn_dye_crg, a.uom_knit_crg, a.uom_dye_crg, a.uom_spandex, a.uom_aop, a.uom_collar_cuff, a.uom_print, a.uom_embro, a.uom_wash_gmts_dye, a.uom_access_price, a.uom_zipper, a.uom_button, a.uom_test, a.uom_cm, a.uom_inspec_cost, a.uom_freight, a.uom_carrier_cost, a.uom_others, a.uom_others2, a.uom_others3, a.size_range, a.others_column_caption2, a.others_cost_cons2, a.others_cost_unit_price2, a.others_cost_total2, a.others_column_caption3, a.others_cost_cons3, a.others_cost_unit_price3, a.others_cost_total3, 
	b.id as dtls_id,a.is_approved,  b.garments_type, b.fabric_source, b.fabric_natu, b.body_length, b.sleeve_length, b.inseam_length, b.front_back_rise, b.sleev_rise_allow, b.chest, b.thigh, b.chest_thigh_allow, b.gsm, b.body_fabric, b.wastage, b.net_body_fabric, b.rib, b.ttl_top_bottom_cons
	FROM wo_price_quotation_v3_mst a, wo_price_quotation_v3_dtls b where a.id=b.mst_id and a.id='$data[1]' and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
	
	 
	
	
	//echo $sql;die;
	$result = sql_select($sql);
	$dtlsDataArray =array();
	foreach($result as $row)
	{
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['fabric_source']=$row[csf('fabric_source')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['fabric_natu']=$row[csf('fabric_natu')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['body_length']=$row[csf('body_length')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['sleeve_length']=$row[csf('sleeve_length')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['front_back_rise']=$row[csf('front_back_rise')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['sleev_rise_allow']=$row[csf('sleev_rise_allow')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['chest']=$row[csf('chest')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['thigh']=$row[csf('thigh')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['chest_thigh_allow']=$row[csf('chest_thigh_allow')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['gsm']=$row[csf('gsm')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['body_fabric']=$row[csf('body_fabric')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['wastage']=$row[csf('wastage')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['net_body_fabric']=$row[csf('net_body_fabric')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['rib']=$row[csf('rib')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['ttl_top_bottom_cons']=$row[csf('ttl_top_bottom_cons')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['inseam_length']=$row[csf('inseam_length')];
	}
	
	
	
	
	

	$sub_total = $result[0][csf('yarn_total')]+$result[0][csf('knit_fab_purc_total')]+$result[0][csf('woven_fab_purc_total')]+$result[0][csf('yarn_dye_crg_total')]+$result[0][csf('knit_crg_total')]+$result[0][csf('dye_crg_total')]+$result[0][csf('spandex_total')]+$result[0][csf('aop_total')]+$result[0][csf('collar_cuff_total')]+$result[0][csf('print_total')]+$result[0][csf('embro_total')]+$result[0][csf('gmts_wash_dye_total')]+$result[0][csf('access_price_total')]+$result[0][csf('zipper_total')]+$result[0][csf('button_total')]+$result[0][csf('test_total')]+$result[0][csf('cm_total')]+$result[0][csf('inspec_cost_total')]+$result[0][csf('freight_total')]+$result[0][csf('carrier_cost_total')]+$result[0][csf('others_cost_total')]+$result[0][csf('others_cost_total2')]+$result[0][csf('others_cost_total3')];
	
	$tot_factory_cost =$sub_total+$result[0][csf('comm_cost_total')];
		
	$measurement_basis_arr = array(1=>"Cad Bassis", 2=>"Measurement Basis");	
		
        ?> 
	<div align="left">
        <div style="width:210mm;">
            <table cellspacing="0" border="0" style="width:210mm; margin-right:-10px;">
                <tr class="form_caption">
                    <?
                    $data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                    ?>
                    <td rowspan="2" align="left" width="50">
					<?
                    foreach ($data_array as $img_row) 
                    {
                        ?>
                        <img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle"/>
                        <?
                    }
					
                    ?>
                    </td>
                    <td colspan="8" align="center" style="font-size:25px;">
                        <strong> <? echo $company_library[$data[0]]; ?></strong>
                        
                        
                    </td>
                    <td rowspan="2" align="left" width="50">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="8" align="center" style="font-size:18px">
                   		 <? echo show_company($data[0], '', array('city')); ?>
                         <br>
                    	<strong><u> Price Quotation</u></strong>
                    </td>
                </tr>
                <?
                $msg = "";
                if($result[0][csf('is_approved')] != 0)
                {
                	$msg = ($result[0][csf('is_approved')] == 1) ? "This Quotation is approved!" : "This Quotation is partial approved!";
	                ?>
	                <tr>
	                	<td colspan="10" align="center" style="font-size:16px;color: red;"> <? echo $msg;?> </td>
	                </tr>
	                <?
            	}
                ?>
            </table>
        </div>
        <br>
		<div style="width:210mm;">
            <table style="width:210mm; text-align:center; font-size:13px;" cellspacing="0" border="1" rules="all" class="rpt_table">
            	<tr>
                	<td colspan="14" bgcolor="#dddddd" align="left"><b>System No: <? echo $result[0][csf('system_id')]; ?></b></td>
                </tr>
                <tr>
                    <td colspan="2" align="left">Buyer</td>
                    <td align="left"><? echo  $buyer_name_arr[$result[0][csf('buyer_id')]]; ?></td>
                    <td colspan="2" align="left">Consumption Basis</td>
                    <td colspan="4" align="left"><? echo $measurement_basis_arr[$result[0][csf('measurment_basis')]]; ?></td>
                    <td colspan="2" align="left">Date</td>
                    <td colspan="3" align="left"><? echo change_date_format($result[0][csf('quot_date')],'dd-mm-yyyy'); ?></td>
                </tr>
                <tr>
                    <td colspan="2" align="left">Agent</td>
                    <td align="left"><? echo $agent_name_arr[$result[0][csf('agent')]]; ?></td>
                    <td colspan="2" align="left">Size Range</td>
                    <td colspan="4" align="left"><? echo $result[0][csf('size_range')]; ?></td>
                    <td colspan="2" align="left">Style Ref</td>
                    <td colspan="3" align="left"><? echo $result[0][csf('style_ref')]; ?></td>
                </tr>
                <tr>
                    <td colspan="2" align="left">Description</td>
                    <td align="left"><? echo $result[0][csf('gmts_item')]; ?></td>
                    <td colspan="2" align="left">Team Member</td>
                    <td colspan="4" align="left"><? echo $result[0][csf('team_member')]; ?></td>
                    <td colspan="2" align="left">Fabrication</td>
                    <td colspan="3" align="left"><? echo $result[0][csf('fabrication')]; ?></td>
                </tr>
                <tr>
                    <td colspan="2" align="left">GSM</td>
                    <td align="left"><? echo $result[0][csf('gsm')]; ?></td>
                    <td colspan="2" align="left">Consumption Size</td>
                    <td colspan="4" align="left"><? echo $result[0][csf('cons_size')]; ?></td>
                    <td colspan="2" align="left">Color</td>
                    <td colspan="3" align="left"><? echo $result[0][csf('color')]; ?></td>
                </tr>
                <tr>
                    <td colspan="2" align="left">Require Yarn Count</td>
                    <td colspan="7" align="left"><? echo $result[0][csf('yarn_count')]; ?></td>
                    
                    <td colspan="2" align="left">Order Qty</td>
                    <td colspan="3" align="left"><? echo $result[0][csf('order_qty')]; ?></td>
                </tr>
                <tr>
                    <td colspan="14" bgcolor="#dddddd" align="left"><b>Fabric Consumption / Dz</b></td>
                </tr>
                
                <?
				$subTotalCons = 0;
				$subTotalBottom = 0;
				$grandTota =0;
				
				foreach($dtlsDataArray as $gmtsType => $gmtsData)
				{
					// Top == 1
					// Bottom = 20
					if($gmtsType==1)
					{
						?>
						<tr style="font-weight:bold;">
                        	<td>Garment<br>Type</td>
							<td>Source</td>
							<td>Fabric Nature</td>
							<td>Body Length</td>
							<td>Sleeve Length</td>
							<td>Allow</td>
							<td>1/2 Chest</td>
							<td>Allow</td>
							<td>GSM</td>
							<td>Body Fabric</td>
							<td>Wastage<br>%</td>
							<td>Net Body<br>Fabric</td>
							<td>Rib<br>%</td>
                            <td>TTL Top<br>Cons</td>
							
						</tr>
                        <? foreach($gmtsData as $row)
						{ 
							$subTotalCons += $row['ttl_top_bottom_cons'];
							$grandTota += $row['ttl_top_bottom_cons'];
				
						?>
						<tr  valign="middle">
                        	<td><? echo $body_part_type[$gmtsType]; ?></td>
							<td><? echo $fabric_source[$row['fabric_source']]; ?></td>
							<td><? echo $item_category[$row['fabric_natu']]; ?></td>
							<td><? echo $row['body_length']; ?></td>
							<td><? echo $row['sleeve_length']; ?></td>
							<td><? echo $row['sleev_rise_allow']; ?></td>
							<td><? echo $row['chest']; ?></td>
							<td><? echo $row['chest_thigh_allow']; ?></td>
							<td><? echo $row['gsm']; ?></td>
							<td><? echo $row['body_fabric']; ?></td>
							<td><? echo $row['wastage']; ?></td>
							<td><? echo number_format($row['net_body_fabric'],4);  ?></td>
							<td><? echo $row['rib']; ?></td>
							<td align="right"><? echo $row['ttl_top_bottom_cons']; ?></td>
						</tr>
                         <? 
						 }
						 ?>
                         <tr valign="middle">
                        	<td colspan="13" align="right"><strong>Subtotal Top Consumption </strong></td>
							<td align="right"><strong><?  echo number_format($subTotalCons,4);  ?></strong></td>
						</tr>
                         <?
						 
					}
					else
					{
						
				
						?>
						<tr style="font-weight:bold;">
                        	<td>Garment<br>Type</td>
							<td>Source</td>
							<td>Fabric Nature</td>
							<td>TTL Side/<br>Inseam Length</td>
							<td>Front/ <br>Back Rise</td>
							<td>Allow</td>
							<td>1/2 Thigh</td>
							<td>Allow</td>
							<td>GSM</td>
							<td>Body Fabric<br>Cons</td>
							<td>Wastage<br>%</td>
							<td>Net Body<br>Fabric</td>
							<td>Rib<br>%</td>
                            <td>TTL Top<br>Cons</td>
							
						</tr>
                        <? foreach($gmtsData as $row)
						{ 
						$subTotalBottom += $row['ttl_top_bottom_cons'];
						$grandTota += $row['ttl_top_bottom_cons'];
						?>
						<tr valign="middle">
                        	<td><? echo $body_part_type[$gmtsType]; ?></td>
							<td><? echo $fabric_source[$row['fabric_source']]; ?></td>
							<td><? echo $item_category[$row['fabric_natu']]; ?></td>
							
							<td><? echo $row['inseam_length']; ?></td>
							<td><? echo $row['front_back_rise']; ?></td>
							<td><? echo $row['sleev_rise_allow']; ?></td>
							<td><? echo $row['thigh']; ?></td>
							<td><? echo $row['chest_thigh_allow']; ?></td>
							<td><? echo $row['gsm']; ?></td>
							<td><? echo $row['body_fabric']; ?></td>
							<td><? echo $row['wastage']; ?></td>
							<td><?  echo number_format($row['net_body_fabric'],4);  ?></td>
							<td><? echo $row['rib']; ?></td>
							<td align="right"><? echo $row['ttl_top_bottom_cons']; ?></td>
						</tr>
						<?
						}
						?>
                         <tr valign="middle">
                        	<td colspan="13" align="right"><strong>Subtotal Bottom Consumption </strong></td>
							<td align="right"><strong><? echo number_format($subTotalBottom,4); ?></strong></td>
						</tr>
                        <tr valign="middle">
                        	<td colspan="13" align="right"><strong>Grand Total Consumption </strong></td>
							<td align="right"><strong><? echo number_format($grandTota,4); ?></strong></td>
						</tr>
                         <?
					}
				}
				
				?>
            </table>
		</div>
         <br/>
         <div style="width:210mm;">
			<div style='width:65%;float:left;padding-right:10px;'>
			<table cellspacing='0' border='1' class='rpt_table' rules='all' align='left' style=' text-align:center; font-size:11px; font-family: Arial Narrow,Arial,sans-serif;' >
                <thead>
                    <th width="">Costing Head</th>
                    <th width="">UOM</th>
                    <th width="100">Consumption</th>
                    <th width="100">Unit Price</th>
                    <th width="100">Total Price</th>
                </thead>
                <tbody class="" id="costing_dtls">
                	<? 
					 if($result[0][csf('yarn_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Yarn Price</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_yarn')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('yarn_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('yarn_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('yarn_total')],4); 
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('knit_fab_purc_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Knit Fabric Purchase</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_knit_fab_purc')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('knit_fab_purc_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('knit_fab_purc_price')],2); ?></td>
                        <td align="right"><? 
						 echo number_format($result[0][csf('knit_fab_purc_total')],4); 
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('woven_fab_purc_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Woven Fabric Purchase</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_woven_fab_purc')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('woven_fab_purc_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('woven_fab_purc_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('woven_fab_purc_total')],4); 
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('yarn_dye_crg_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Yarn Dyeing Charge</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_yarn_dye_crg')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('yarn_dye_crg_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('yarn_dye_crg_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('yarn_dye_crg_total')],4);  
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('knit_crg_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Knitting Charge</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_knit_crg')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('knit_crg_cons')],4);  ?></td>
                        <td align="right"><? echo number_format($result[0][csf('knit_crg_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('knit_crg_total')],4);
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('dye_crg_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Dyeing Charge</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_dye_crg')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('dye_crg_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('dye_crg_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('dye_crg_total')],4);
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('spandex_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Spandex</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_spandex')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('spandex_amt')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('spandex_unit_price')],2); ?></td>
                        <td align="right"><? 
						 echo number_format($result[0][csf('spandex_total')],4);
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('aop_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">AOP</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_aop')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('aop_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('aop_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('aop_total')],4);
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('collar_cuff_unit_price')]*1 > 0){ 
					 ?> 
                    <tr>
                        <td align="left">Flat Knit Collar & Cuff</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_collar_cuff')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('collar_cuff_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('collar_cuff_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('collar_cuff_total')],4);
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('print_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Print</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_print')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('print_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('print_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('print_total')],4);  
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('embro_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Embroidery</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_embro')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('embro_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('embro_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('embro_total')],4);  
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('gmts_wash_dye_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Wash/Gmts Dyeing</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_wash_gmts_dye')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('gmts_wash_dye_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('gmts_wash_dye_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('gmts_wash_dye_total')],4);  
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('access_price_unit_price')]*1 > 0){ 
					 ?> 
                    <tr>
                        <td align="left">Accessories Price</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_access_price')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('access_price_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('access_price_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('access_price_total')],4);  
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('zipper_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Zipper</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_zipper')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('zipper_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('zipper_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('zipper_total')],4);  
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('button_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Button</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_button')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('button_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('button_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('button_total')],4);  
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('test_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Test</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_test')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('test_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('test_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('test_total')],4);
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('cm_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">CM</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_cm')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('cm_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('cm_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('cm_total')],4);  
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('inspec_cost_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Inspection Cost</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_inspec_cost')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('inspec_cost_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('inspec_cost_unit_price')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('inspec_cost_total')],4); ?></td>
                    </tr>
                     <? 
					 }
					 if($result[0][csf('freight_unit_price')]*1 > 0){ 
					 ?>
                   
                    <tr>
                        <td align="left">Freight</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_freight')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('freight_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('freight_unit_price')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('freight_total')],4); ?></td>
                    </tr>
                     <? 
					 }
					 if($result[0][csf('carrier_cost_unit_price')]*1 > 0){ 
					 ?>
                    
                    <tr>
                        <td align="left">Currier Cost</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_carrier_cost')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('carrier_cost_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('carrier_cost_unit_price')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('carrier_cost_total')],4); ?></td>
                    </tr> 
					<? 
					 }
					if($result[0][csf('others_cost_unit_price')]*1 > 0){ 
					?>
                    <tr>
                        <td align="left"><? echo $result[0][csf('others_column_caption')]; ?></td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_others')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_unit_price')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_total')],4);  ?></td>
                    </tr>
                     <? }if($result[0][csf('others_cost_unit_price2')]*1 > 0){ 
					?>
                    <tr>
                        <td align="left"><? echo $result[0][csf('others_column_caption2')]; ?></td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_others2')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_cons2')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_unit_price2')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_total2')],4);  ?></td>
                    </tr>
                     <? }if($result[0][csf('others_cost_unit_price3')]*1 > 0){ 
					?>
                    <tr>
                        <td align="left"><? echo $result[0][csf('others_column_caption3')]; ?></td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_others3')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_cons3')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_unit_price3')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_total3')],4);  ?></td>
                    </tr>
                     <? }?>
                    
                    <tr>
                        <td align="left" colspan="4"> <strong>Sub Total</strong> </td>
                        <td align="right"><strong><? echo number_format($sub_total,4); ?></strong></td>
                    </tr> 
                    <tr>
                        <td align="left">Commercial Cost</td>
                        <td colspan="3"><? echo number_format($result[0][csf('comm_cost_cons')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('comm_cost_total')],4);  
						?></td>
                    </tr> 
                    <tr>
                        <td align="left" colspan="4"><strong>Total Factory Cost/ Dz </strong></td>
                        <td align="right"><strong><? echo number_format($tot_factory_cost,4); ?></strong></td>
                    </tr> 
                </tbody>
            </table>
            </div>
            <div style="width:30%;float:right;">
			<table cellspacing='0' border='1' class='rpt_table' id='' rules='all' align='left' style='width:64mm; text-align:center;font-size:11px; font-family: Arial Narrow,Arial,sans-serif;' >
                <thead>
                    <th colspan="2">Offer Price/ Unit (FOB)</th>
                </thead>
                <tbody class="" id="costing_dtls">
                    <tr>
                        <td align="left"width="150">Factory Unit Price</td>
                        <td align="right"><? 
						//$final_offer_price = $tot_factory_cost/12;
						echo number_format($result[0][csf('fact_u_price')],4); 
						?></td>
                    </tr> 
                    <tr>
                        <td align="left"width="150">Agent Commission</td>
                        <td align="right"><? 
						//$final_offer_price = $tot_factory_cost/12;
						echo number_format($result[0][csf('agnt_comm_tot')],4); 
						?></td>
                    </tr> 
                    <tr>
                        <td align="left"width="150">Local Commission</td>
                        <td align="right"><? 
						//$final_offer_price = $tot_factory_cost/12;
						echo number_format($result[0][csf('local_comm_tot')],4); 
						?></td>
                    </tr> 
                    
                    <tr>
                        <td align="left" >Final Offer Price</td>
                        <td align="right"><strong><? echo number_format($result[0][csf('final_offer_price')],4); ?></strong></td>
                    </tr> 
                    <tr>
                        <td colspan="2"><strong>Order Confirmed Price</strong></td>
                    </tr> 
                    <tr>
                        <td colspan="2" align="right"><strong><? echo number_format($result[0][csf('order_conf_price')],4); ?></strong></td>
                    </tr> 
                    <tr>
                        <td colspan="2"><strong>Order Confirmed Date</strong></td>
                    </tr> 
                    <tr>
                        <td colspan="2"><strong><? echo change_date_format($result[0][csf('order_conf_date')],'dd-mm-yyyy'); ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:left; height:60px;  text-align: justify; text-justify: inter-word; " valign="top"><strong>Remarks : </strong><? echo $result[0][csf('remarks')]; ?></td>
                    </tr>
                </tbody>
            </table>
            </div>
		</div>
        <br>
        <div style="padding-top:500px; width:210mm;">
            <table cellspacing="0" style="width:210mm;" border="0">
                <tr align="center">
                    <td colspan="4" align="left" style="padding-left:40px;">Prepared By</td>
                    <td colspan="2" align="right" style="padding-right:40px;">Approved By</td>
                </tr>
            </table>
		</div>
     </div>   
	<?
}
